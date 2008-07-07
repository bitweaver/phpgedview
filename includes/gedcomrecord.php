<?php
/**
 * Base class for all gedcom records
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007	John Finlay and Others
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package PhpGedView
 * @subpackage DataModel
 * @version $Id: gedcomrecord.php,v 1.6 2008/07/07 17:30:13 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once(PHPGEDVIEW_PKG_PATH.'includes/functions_privacy.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/person_class.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/family_class.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/source_class.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/repository_class.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/media_class.php');
class GedcomRecord {
	var $gedrec = "";
	var $xref = "";
	var $type = "";
	var $changed = false;
	var $rfn = null;
	var $disp = null;

	/**
	 * constructor for this class
	 */
	function GedcomRecord($gedrec, $simple=false) {
		if (empty($gedrec)) return;

		//-- lookup the record from another gedcom
		$remoterfn = get_gedcom_value("RFN", 1, $gedrec);
		if (!empty($remoterfn)) {
			$parts = preg_split("/:/", $remoterfn);
			if (count($parts)==2) {
				$servid = $parts[0];
				$aliaid = $parts[1];
				if (!empty($servid)&&!empty($aliaid)) {
					require_once 'includes/serviceclient_class.php';
					$serviceClient = ServiceClient::getInstance($servid);
					if (!is_null($serviceClient)) {
						if (!$simple || $serviceClient->type=='local') {
							$gedrec = $serviceClient->mergeGedcomRecord($aliaid, $gedrec, true);
						}
					}
				}
			}
		}

		//-- set the gedcom record a privatized version
		$this->gedrec = privatize_gedcom($gedrec);
		$ct = preg_match("/0 @(.*)@ (.*)/", $this->gedrec, $match);
		if ($ct>0) {
			$this->xref = trim($match[1]);
			$this->type = trim($match[2]);
		}
	}

	/**
	 * Static function used to get an instance of an object
	 * @param string $pid	the ID of the object to retrieve
	 * @return GedcomRecord
	 */
	function &getInstance($pid, $simple=true) {
		global $gedcom_record_cache, $GEDCOM, $pgv_changes;

		$ged_id=get_id_from_gedcom($GEDCOM);
		// Check the cache first
		if (isset($gedcom_record_cache[$pid][$ged_id])) {
			return $gedcom_record_cache[$pid][$ged_id];
		}

		//-- look for the gedcom record
		$indirec = find_gedcom_record($pid);
		if (empty($indirec)) {
			$ct = preg_match("/(\w+):(.+)/", $pid, $match);
			//-- check if it is a remote object
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				require_once 'includes/serviceclient_class.php';
				$service = ServiceClient::getInstance($servid);
				//-- the INDI will be replaced with the type from the remote record
				$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$pid."@ INDI\r\n1 RFN ".$pid, false);
				$indirec = $newrec;
			}
		}
		//-- check if it is a new object not yet in the database
		if (empty($indirec)) {
			if (PGV_USER_CAN_EDIT && isset($pgv_changes[$pid."_".$GEDCOM])) {
				$indirec = find_updated_record($pid);
				$fromfile = true;
			}
		}
		if (empty($indirec)) return null;

		$ct = preg_match("/0 @.*@ (\w*)/", $indirec, $match);
		if ($ct>0) {
			$type = trim($match[1]);
			if ($type=="INDI") {
				$record = new Person($indirec, $simple);
				if (!empty($fromfile)) $record->setChanged(true);
				return $record;
			}
			else if ($type=="FAM") {
				$record = new Family($indirec, $simple);
				if (!empty($fromfile)) $record->setChanged(true);
				return $record;
			}
			else if ($type=="SOUR") {
				$record = new Source($indirec, $simple);
				if (!empty($fromfile)) $record->setChanged(true);
				return $record;
			}
			else if ($type=="REPO") {
				$record = new Repository($indirec, $simple);
				if (!empty($fromfile)) $record->setChanged(true);
				return $record;
			}
			else if ($type=="OBJE") {
				$record = new Media($indirec, $simple);
				if (!empty($fromfile)) $record->setChanged(true);
				return $record;
			}
			else {
				$record = new GedcomRecord($indirec, $simple);
				if (!empty($fromfile)) $record->setChanged(true);
				return $record;
			}
			// Store the object in the cache
			$gedcom_record_cache[$pid][$ged_id]=&$record;
		}
		return null;
	}

	/**
	 * get the xref
	 * @return string	returns the person ID
	 */
	function getXref() {
		return $this->xref;
	}
	/**
	 * get the object type
	 * @return string	returns the type of this object "INDI","FAM", etc.
	 */
	function getType() {
		return $this->type;
	}
	/**
	 * get gedcom record
	 */
	function getGedcomRecord() {
		return $this->gedrec;
	}
	/**
	 * set gedcom record
	 */
	function setGedcomRecord($gcRec) {
		$this->gedrec = $gcRec;
	}
	/**
	 * set if this is a changed record from the gedcom file
	 * @param boolean $changed
	 */
	function setChanged($changed) {
		$this->changed = $changed;
	}
	/**
	 * get if this is a changed record from the gedcom file
	 * @return boolean
	 */
	function getChanged() {
		return $this->changed;
	}

	/**
	 * is this person from another server
	 * @return boolean 	return true if this person was linked from another server
	 */
	function isRemote() {
		if (is_null($this->rfn)) $this->rfn = get_gedcom_value("RFN", 1, $this->gedrec);
		if (empty($this->rfn) || $this->xref!=$this->rfn) return false;
		
		$parts = preg_split("/:/", $this->rfn);
		if (count($parts)==2) {
			return true;
		}
		return false;
	}

	/**
	 * check if this object is equal to the given object
	 * basically just checks if the IDs are the same
	 * @param GedcomRecord $obj
	 */
	function equals(&$obj) {
		if (is_null($obj)) return false;
		if ($this->xref==$obj->getXref()) return true;
		return false;
	}

	/**
	 * get the URL to link to this record
	 * @string a url that can be used to link to this person
	 */
	function getLinkUrl($link='gedcomrecord.php?pid=') {
		global $GEDCOM;

		$url = $link.urlencode($this->getXref()).'&amp;ged='.urlencode($GEDCOM);
		if ($this->isRemote()) {
			list($servid, $aliaid)=explode(':', $this->rfn);
			if ($aliaid && $servid) {
				require_once 'includes/serviceclient_class.php';
				$serviceClient = ServiceClient::getInstance($servid);
				if ($serviceClient) {
					$surl = $serviceClient->getURL();
					$url = $link.urlencode($aliaid);
					if ($serviceClient->getType()=='remote') {
						if (!empty($surl)) {
							$url = dirname($surl).'/'.$url;
						}
					} else {
						$url = $surl.$url;
					}
					$gedcom = $serviceClient->getGedfile();
					if ($gedcom) {
						$url.='&amp;ged='.urlencode($gedcom);
					}
				}
			}
		}
		return $url;
	}
	
	/**
	 * Get the title that should be used in the link
	 * @return string
	 */
	function getLinkTitle() {
		global $GEDCOM, $gGedcom;

		$title = $gGedcom->mGedcomName;
		if ($this->isRemote()) {
			$parts = preg_split("/:/", $this->rfn);
			if (count($parts)==2) {
				$servid = $parts[0];
				$aliaid = $parts[1];
				if (!empty($servid)&&!empty($aliaid)) {
					require_once 'includes/serviceclient_class.php';
					$serviceClient = ServiceClient::getInstance($servid);
					if (!empty($serviceClient)) {
						$title = $serviceClient->getTitle();
					}
				}
			}
		}
		return $title;
	}

	// Get an HTML link to this object, for use in sortable lists.
	function getXrefLink($target="") {
		global $SEARCH_SPIDER;
		if (empty($SEARCH_SPIDER)) {
			if ($target) $target = "target=\"".$target."\"";
			return "<a href=\"".$this->getLinkUrl()."#content\" name=\"".preg_replace('/\D/','',$this->getXref())."\" $target>".$this->getXref()."</a>";
		}
		else
			return $this->getXref();
	}

	/**
	 * return an absolute url for linking to this record from another site
	 *
	 */
	function getAbsoluteLinkUrl() {
		global $SERVER_URL;
		return $SERVER_URL . $this->getLinkUrl();
	}

	/**
	 * Undo the latest change to this gedcom record
	 */
	function undoChange() {
		global $GEDCOM, $pgv_changes;
		require_once('includes/functions_edit.php');
		if (!PGV_USER_CAN_ACCEPT) return false;
		$cid = $this->xref."_".$GEDCOM;
		if (!isset($pgv_changes[$cid])) return false;
		$index = count($pgv_changes[$cid])-1;
		if (undo_change($cid, $index)) return true;
		return false;
	}

	/**
	 * check if this record has been marked for deletion
	 * @return boolean
	 */
	function isMarkedDeleted() {
		global $pgv_changes, $GEDCOM;

		if (!PGV_USER_CAN_EDIT) return false;
		if (isset($pgv_changes[$this->xref."_".$GEDCOM])) {
			$change = end($pgv_changes[$this->xref."_".$GEDCOM]);
			if ($change['type']=='delete') return true;
		}

		return false;
	}

	/**
	 * can the details of this record be shown
	 * This method should be overridden in sub classes
	 * @return boolean
	 */
	function canDisplayDetails() {
		if (is_null($this->disp)) $this->disp = displayDetailsById($this->xref, $this->type);
		return $this->disp;
	}
	
	/**
	 * get the URL to link to a place
	 * @string a url that can be used to link to placelist
	 */
	function getPlaceUrl($gedcom_place) {
		global $GEDCOM;
		$exp = explode(",", $gedcom_place);
		$level = count($exp);
		$url = "placelist.php?action=show&amp;level=".$level;
		for ($i=0; $i<$level; $i++) $url .= "&amp;parent[".$i."]=".urlencode(trim($exp[$level-$i-1]));
		$url .= "&amp;ged=".$GEDCOM;
		return $url;
	}

	/**
	 * get the first part of a place record
	 * @string a url that can be used to link to placelist
	 */
	function getPlaceShort($gedcom_place) {
		global $GEDCOM;
		$gedcom_place = trim($gedcom_place, " ,");
		$exp = explode(",", $gedcom_place);
		return trim($exp[0]);
	}

	/**
	 * get the sortable name
	 * This method should be overridden in child sub-classes
	 * (no class yet for NOTE record)
	 * @return string
	 */
	function getSortableName() {
		return $this->type." ".$this->xref;
	}
	
	/**
	 * get the name
	 * This method should overridden in child sub-classes
	 * @return string
	 */
	function getName() {
		return get_gedcom_value("NAME", 1, $this->gedrec);
	}
	
	/**
	 * get the additional name
	 * This method should overridden in child sub-classes
	 * @return string
	 */
	function getAddName() {
		return "";
	}

	// Get all attributes (e.g. DATE or PLAC) from an event (e.g. BIRT or MARR).
	// This is used to display multiple events on the individual/family lists.
	// Multiple events can exist because of uncertainty in dates, dates in different
	// calendars, place-names in both latin and hebrew character sets, etc.
	// It also allows us to combine dates/places from different events in the summaries.
	function getAllEventDates($event) {
		$dates=array();
		if (ShowFactDetails($event, $this->xref) && preg_match_all("/^1 *{$event}\b.*((?:[\r\n]+[2-9].*)+)/m", $this->gedrec, $events)) {
			foreach ($events[1] as $event_rec) {
				if (!FactViewRestricted($this->xref, $event_rec) && preg_match_all("/^2 DATE +(.+)/m", $event_rec, $ged_dates)) {
					foreach ($ged_dates[1] as $ged_date) {
						$dates[]=new GedcomDate($ged_date);
					}
				}
			}
		}
		return $dates;
	}
	function getAllEventPlaces($event) {
		$places=array();
		if (ShowFactDetails($event, $this->xref) && preg_match_all("/^1 *{$event}\b.*((?:[\r\n]+[2-9].*)+)/m", $this->gedrec, $events)) {
			foreach ($events[1] as $event_rec) {
				if (!FactViewRestricted($this->xref, $event_rec) && preg_match_all("/^(?:2 PLAC|3 (?:ROMN|FONE|_HEB)) +(.+)/m", $event_rec, $ged_places)) {
					foreach ($ged_places[1] as $ged_place) {
						$places[]=$ged_place;
					}
				}
			}
		}
		return $places;
	}

	// Get all the events of a type.
	// TODO: event handling needs to be tidied up - with the event class from PGV4.2 ??
	function getAllEvents($event) {
		$event_recs=array();
		if (ShowFactDetails($event, $this->xref) && preg_match_all("/^1 *{$event}\b.*((?:[\r\n]+[2-9].*)+)/m", $this->gedrec, $events)) {
			foreach ($events[0] as $event_rec) {
				if (!FactViewRestricted($this->xref, $event_rec)) {
					$event_recs[]=$event_rec;
				}
			}	
		}
		return $event_recs;
	}

	//////////////////////////////////////////////////////////////////////////////
	// Get the last-change timestamp for this record - optionally wrapped in a
	// link to ourself.
	//////////////////////////////////////////////////////////////////////////////
	function LastChangeTimestamp($add_url) {
		global $DATE_FORMAT, $TIME_FORMAT;
		$chan_rec=get_sub_record(1, '1 CHAN', $this->gedrec);
		if (empty($chan_rec))
			return '&nbsp;';
		$d=new GedcomDate(get_gedcom_value('DATE', 2, $chan_rec, '', false));
		if (preg_match('/^(\d\d):(\d\d):(\d\d)/', get_gedcom_value('DATE:TIME', 2, $chan_rec, '', false).':00', $match)) {
			$t=mktime($match[1], $match[2], $match[3]);
			$sort=$d->MinJD().$match[1].$match[2].$match[3];
			$text=strip_tags($d->Display(false, "{$DATE_FORMAT} -", array()).date(" {$TIME_FORMAT}", $t));
		} else {
			$sort=$d->MinJD().'000000';
			$text=strip_tags($d->Display(false, "{$DATE_FORMAT}", array()));
		}
		if ($add_url)
			$text='<a name="'.$sort.'" href="'.$this->getLinkUrl().'">'.$text.'</a>';
		return $text;
	}

	//////////////////////////////////////////////////////////////////////////////
	// Get the last-change user for this record
	//////////////////////////////////////////////////////////////////////////////
	function LastchangeUser() {
		$chan_rec=get_sub_record(1, '1 CHAN', $this->gedrec);
		if (empty($chan_rec))
			return '&nbsp;';
		$chan_user=get_gedcom_value("_PGVU", 2, $chan_rec, '', false);
		if (empty($chan_user))
			return '&nbsp;';
		return $chan_user;
	}
}
?>

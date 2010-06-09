<?php
/**
 * Class file for a Family
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_CLASS_FAMILY_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_gedcomrecord.php');

class Family extends GedcomRecord {
	private $husb = null;
	private $wife = null;
	private $children = array();
	private $childrenIds = array();
	private $marriage = null;
	private $children_loaded = false;
	private $numChildren = false;
	private $_isDivorced = null;

	// Create a Family object from either raw GEDCOM data or a database row
	function Family($data, $simple=true) {
		if (is_array($data)) {
			// Construct from a row from the database
			if ($data['f_husb']) {
				$this->husb=Person::getInstance($data['f_husb']);
			}
			if ($data['f_wife']) {
				$this->wife=Person::getInstance($data['f_wife']);
			}
			if (strpos($data['f_chil'], ';')) {
				$this->childrenIds=explode(';', trim($data['f_chil'], ';'));
			}
			$this->numChildren=$data['f_numchil'];
			// Check for divorce *before* we privatize the data so we can correctly label spouses/ex-spouses
			$this->_isDivorced=(bool)preg_match('/\n1 ('.PGV_EVENTS_DIV.')( Y|\n)/', $data['gedrec']);
		} else {
			// Construct from raw GEDCOM data
			if (preg_match('/^1 HUSB @(.+)@/m', $data, $match)) {
				$this->husb=Person::getInstance($match[1], $simple);
			}
			if (preg_match('/^1 WIFE @(.+)@/m', $data, $match)) {
				$this->wife=Person::getInstance($match[1], $simple);
			}
			if (preg_match_all('/^1 CHIL @(.+)@/m', $data, $match)) {
				$this->childrenIds=$match[1];
			}
			if (preg_match('/^1 NCHI (\d+)/m', $data, $match)) {
				$this->numChildren=$match[1];
			} else {
				$this->numChildren=count($this->childrenIds);
			}
			// Check for divorce *before* we privatize the data so we can correctly label spouses/ex-spouses
			$this->_isDivorced=(bool)preg_match('/\n1 ('.PGV_EVENTS_DIV.')( Y|\n)/', $data);
		}

		// Make sure husb/wife are the right way round.
		if ($this->husb && $this->husb->getSex()=='F' || $this->wife && $this->wife->getSex()=='M') {
			list($this->husb, $this->wife)=array($this->wife, $this->husb);
		}

		parent::GedcomRecord($data);
	}

	// Get an instance of a Family.  We either specify
	// an XREF (in the current gedcom), or we can provide a row
	// from the database (if we anticipate the record hasn't
	// been fetched previously).
	static function &getInstance($data, $simple=true) {
		global $gedcom_record_cache, $GEDCOM, $pgv_changes;

		if (is_array($data)) {
			$ged_id=$data['ged_id'];
			$pid   =$data['xref'];
		} else {
			$ged_id=get_id_from_gedcom($GEDCOM);
			$pid   =$data;
		}

		// Check the cache first
		if (isset($gedcom_record_cache[$pid][$ged_id])) {
			return $gedcom_record_cache[$pid][$ged_id];
		}

		// Look for the record in the database
		if (!is_array($data)) {
			$data=fetch_family_record($pid, $ged_id);

			// If we didn't find the record in the database, it may be remote
			if (!$data && strpos($pid, ':')) {
				list($servid, $remoteid)=explode(':', $pid);
				$service=ServiceClient::getInstance($servid);
				if ($service) {
					// TYPE will be replaced with the type from the remote record
					$data=$service->mergeGedcomRecord($remoteid, "0 @{$pid}@ TYPE\n1 RFN {$pid}", false);
				}
			}

			// If we didn't find the record in the database, it may be new/pending
			if (!$data && PGV_USER_CAN_EDIT && isset($pgv_changes[$pid.'_'.$GEDCOM])) {
				$data=find_updated_record($pid);
				$fromfile=true;
			}

			// If we still didn't find it, it doesn't exist
			if (!$data) {
				return null;
			}
		}

		// Create the object
		$object=new Family($data, $simple);
		if (!empty($fromfile)) {
			$object->setChanged(true);
		}

		// Store it in the cache
		$gedcom_record_cache[$object->xref][$object->ged_id]=&$object;
		//-- also store it using its reference id (sid:pid and local gedcom for remote links)
		$gedcom_record_cache[$pid][$ged_id]=&$object;
		return $object;
	}

	/**
	 * get the husbands ID
	 * @return string
	 */
	function getHusbId() {
		if (!is_null($this->husb)) return $this->husb->getXref();
		else return "";
	}

	/**
	 * get the wife ID
	 * @return string
	 */
	function getWifeId() {
		if (!is_null($this->wife)) return $this->wife->getXref();
		else return "";
	}

	/**
	 * get the husband's person object
	 * @return Person
	 */
	function &getHusband() {
		return $this->husb;
	}
	/**
	 * get the wife's person object
	 * @return Person
	 */
	function &getWife() {
		return $this->wife;
	}

	/**
	 * return the spouse of the given person
	 * @param Person $person
	 * @return Person
	 */
	function &getSpouse(&$person) {
		if (is_null($this->wife) or is_null($this->husb)) return null;
		if ($this->wife->equals($person)) return $this->husb;
		if ($this->husb->equals($person)) return $this->wife;
		return null;
	}

	/**
	 * return the spouse id of the given person id
	 * @param string $pid
	 * @return string
	 */
	function &getSpouseId($pid) {
		if (is_null($this->wife) or is_null($this->husb)) return null;
		if ($this->wife->getXref()==$pid) return $this->husb->getXref();
		if ($this->husb->getXref()==$pid) return $this->wife->getXref();
		return null;
	}

	/**
	 * get the children
	 * @return array 	array of children Persons
	 */
	function getChildren() {
		if (!$this->children_loaded) $this->loadChildren();
		return $this->children;
	}

	/**
	 * get the children ids
	 * @return array 	array of children ids
	 */
	function getChildrenIds() {
		if (!$this->children_loaded) $this->loadChildren();
		return $this->childrenIds;
	}

	// Static helper function to sort an array of families by marriage date
	static function CompareMarrDate($x, $y) {
		return GedcomDate::Compare($x->getMarriageDate(), $y->getMarriageDate());
	}

	/**
	 * Load the children from the database
	 * We used to load the children when the family was created, but that has performance issues
	 * because we often don't need all the children
	 * now, children are only loaded as needed
	 */
	function loadChildren() {
		if ($this->children_loaded) return;
		$this->childrenIds = array();
		$this->numChildren = preg_match_all("/1\s*CHIL\s*@(.*)@/", $this->gedrec, $smatch, PREG_SET_ORDER);
		for($i=0; $i<$this->numChildren; $i++) {
			//-- get the childs ids
			$chil = trim($smatch[$i][1]);
			$this->childrenIds[] = $chil;
		}
		foreach($this->childrenIds as $t=>$chil) {
			$child=Person::getInstance($chil);
			if (!is_null($child)) $this->children[] = $child;
		}
		$this->children_loaded = true;
	}

	/**
	 * get the number of children in this family
	 * @return int 	the number of children
	 */
	function getNumberOfChildren() {

		if ($this->numChildren!==false) return $this->numChildren;

		$this->numChildren = get_gedcom_value("NCHI", 1, $this->gedrec);
		if ($this->numChildren!="") return $this->numChildren.".";
		$this->numChildren = preg_match_all("/1\s*CHIL\s*@(.*)@/", $this->gedrec, $smatch);
		return $this->numChildren;
	}

	/**
	 * get updated Family
	 * If there is an updated family record in the gedcom file
	 * return a new family object for it
	 */
	function &getUpdatedFamily() {
		global $GEDCOM, $pgv_changes;
		if ($this->getChanged()) {
			return $this;
		}
		if (PGV_USER_CAN_EDIT && $this->canDisplayDetails()) {
			if (isset($pgv_changes[$this->xref."_".$GEDCOM])) {
				$newrec = find_updated_record($this->xref);
				if (!empty($newrec)) {
					$newfamily = new Family($newrec);
					$newfamily->setChanged(true);
					return $newfamily;
				}
			}
		}
		return null;
	}
	/**
	 * check if this family has the given person
	 * as a parent in the family
	 * @param Person $person
	 */
	function hasParent(&$person) {
		if (is_null($person)) return false;
		if ($person->equals($this->husb)) return true;
		if ($person->equals($this->wife)) return true;
		return false;
	}
	/**
	 * check if this family has the given person
	 * as a child in the family
	 * @param Person $person
	 */
	function hasChild(&$person) {
		if (is_null($person)) return false;
		$this->loadChildren();
		foreach($this->children as $key=>$child) {
			if ($person->equals($child)) return true;
		}
		return false;
	}

	/**
	 * parse marriage record
	 */
	function _parseMarriageRecord() {
		$this->marriage = new Event(trim(get_sub_record(1, "1 MARR", $this->gedrec)), -1);
		$this->marriage->setParentObject($this);
	}

	/**
	 * get the marriage event
	 *
	 * @return Event
	 */
	function getMarriage() {
		if (is_null($this->marriage)) $this->_parseMarriageRecord();
		return $this->marriage;
	}

	/**
	 * get marriage record
	 * @return string
	 */
	function getMarriageRecord() {
		if (is_null($this->marriage)) $this->_parseMarriageRecord();
		return $this->marriage->getGedcomRecord();
	}

	// Return whether or not this family ended in a divorce.
	// Note that this is calculated prior to privatizing the data, so we can
	// always distinguish spouses from ex-spouses.  This apparant leaking of
	// private data was discussed and agreed on the pgv forum.
	function isDivorced() {
		return $this->_isDivorced;
	}

	/**
	 * get marriage date
	 * @return string
	 */
	function getMarriageDate() {
		global $pgv_lang;
		if (!$this->canDisplayDetails()) {
			return new GedcomDate('');
		}
		if (is_null($this->marriage)) {
			$this->_parseMarriageRecord();
		}
		return $this->marriage->getDate();
	}

	/**
	 * get the marriage year
	 * @return string
	 */
	function getMarriageYear($est = true, $cal = ""){
		// TODO - change the design to use julian days, not gregorian years.
		$mdate = $this->getMarriageDate();
		$mdate = $mdate->MinDate();
		if ($cal) $mdate = $mdate->convert_to_cal($cal);
		return $mdate->y;
	}

	/**
	 * get the marriage month
	 * @return string
	 */
	function getMarriageMonth($est = true, $cal = ""){
		// TODO - change the design to use julian days, not gregorian years.
		$mdate = $this->getMarriageDate();
		$mdate=$mdate->MinDate();
		if ($cal) $mdate = $mdate->convert_to_cal($cal);
		return $mdate->m;
	}

	/**
	 * get the type for this marriage
	 * @return string
	 */
	function getMarriageType() {
		if (is_null($this->marriage)) $this->_parseMarriageRecord();
		return $this->marriage->getType();
	}

	/**
	 * get the marriage place
	 * @return string
	 */
	function getMarriagePlace() {
		$marriage = $this->getMarriage();
		return $marriage->getPlace();
	}

	// Get all the dates/places for marriages - for the FAM lists
	function getAllMarriageDates() {
		if ($this->canDisplayDetails()) {
			foreach (explode('|', PGV_EVENTS_MARR) as $event) {
				if ($array=$this->getAllEventDates($event)) {
					return $array;
				}
			}
		}
		return array();
	}
	function getAllMarriagePlaces() {
		if ($this->canDisplayDetails()) {
			foreach (explode('|', PGV_EVENTS_MARR) as $event) {
				if ($array=$this->getAllEventPlaces($event)) {
					return $array;
				}
			}
		}
		return array();
	}

	/**
	 * get the URL to link to this family
	 * @string a url that can be used to link to this family
	 */
	public function getLinkUrl() {
		return parent::_getLinkUrl('family.php?famid=');
	}

	// Get an array of structures containing all the names in the record
	function getAllNames() {
		if (is_null($this->_getAllNames)) {
			$husb=$this->husb ? $this->husb : new Person('1 SEX M');
			$wife=$this->wife ? $this->wife : new Person('1 SEX F');
			// Check the script used by each name, so we can match cyrillic with cyrillic, greek with greek, etc.
			$husb_names=$husb->getAllNames();
			foreach ($husb_names as $n=>$husb_name) {
				$husb_names[$n]['lang']=whatLanguage($husb_name['surn']);
			}
			$wife_names=$wife->getAllNames();
			foreach ($wife_names as $n=>$wife_name) {
				$wife_names[$n]['lang']=whatLanguage($wife_name['surn']);
			}
			// Add the matched names first
			foreach ($husb_names as $husb_name) {
				foreach ($wife_names as $wife_name) {
					if ($husb_name['type']!='_MARNM' && $wife_name['type']!='_MARNM' && $husb_name['lang']==$wife_name['lang']) {
						$this->_getAllNames[]=array(
							'type'=>$husb_name['type'],
							'full'=>$husb_name['full'].' + '.$wife_name['full'],
							'list'=>$husb_name['list'].$husb->getSexImage().'<br />'.$wife_name['list'].$wife->getSexImage(),
							'sort'=>$husb_name['sort'].' + '.$wife_name['sort'],
						);
					}
				}
			}
			// Add the unmatched names second (there may be no matched names)
			foreach ($husb_names as $husb_name) {
				foreach ($wife_names as $wife_name) {
					if ($husb_name['type']!='_MARNM' && $wife_name['type']!='_MARNM'  && $husb_name['lang']!=$wife_name['lang']) {
						$this->_getAllNames[]=array(
							'type'=>$husb_name['type'],
							'full'=>$husb_name['full'].' + '.$wife_name['full'],
							'list'=>$husb_name['list'].$husb->getSexImage().'<br />'.$wife_name['list'].$wife->getSexImage(),
							'sort'=>$husb_name['sort'].' + '.$wife_name['sort'],
						);
					}
				}
			}
		}
		return $this->_getAllNames;
	}

	// Extra info to display when displaying this record in a list of
	// selection items or favourites.
	function format_list_details() {
		return
		  $this->format_first_major_fact(PGV_EVENTS_MARR, 1).
		  $this->format_first_major_fact(PGV_EVENTS_DIV, 1);
	}

}
?>

<?php
/**
 * Class file for a Family
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2006	John Finlay and Others
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
 * @version $Id: family_class.php,v 1.5 2007/06/09 21:11:04 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once(PHPGEDVIEW_PKG_PATH.'includes/gedcomrecord.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/person_class.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/serviceclient_class.php');

class Family extends GedcomRecord {
	var $husb = null;
	var $wife = null;
	var $children = array();
	var $disp = true;
	var $marr_rec = null;
	var $marr_date = null;
	var $marr_type = null;
	var $marr_est = false; // estimate
	var $div_rec = null;
	var $marr_rec2 = null;
	var $marr_date2 = null;

	/**
	 * constructor
	 * @param string $gedrec	the gedcom record
	 */
	function Family($gedrec, $simple=true) {
		global $pgv_changes, $GEDCOM;

		parent::GedcomRecord($gedrec);
		$this->disp = displayDetailsById($this->xref, "FAM");
		$husbrec = get_sub_record(1, "1 HUSB", $gedrec);
		if (!empty($husbrec)) {
			//-- get the husbands ids
			$husb = get_gedcom_value("HUSB", 1, $husbrec);
			$this->husb = Person::getInstance($husb, $simple);
		}
		$wiferec = get_sub_record(1, "1 WIFE", $gedrec);
		if (!empty($wiferec)) {
			//-- get the wifes ids
			$wife = get_gedcom_value("WIFE", 1, $wiferec);
			$this->wife = Person::getInstance($wife, $simple);
		}
		$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $gedrec, $smatch, PREG_SET_ORDER);
		for($i=0; $i<$num; $i++) {
			//-- get the childs ids
			$chil = trim($smatch[$i][1]);
			$child = Person::getInstance($chil, $simple);
			if ( !is_null($child)) $this->children[] = $child;
		}
	}

	/**
	 * Static function used to get an instance of a family object
	 * @param string $pid	the ID of the family to retrieve
	 */
	function &getInstance($pid, $simple=true) {
		global $famlist, $GEDCOM, $pgv_changes;

		if (isset($famlist[$pid]) && $famlist[$pid]['gedfile']==$gGedcom->mGEDCOMId) {
			if (isset($famlist[$pid]['object'])) return $famlist[$pid]['object'];
		}

		$indirec = find_family_record($pid);
		if (empty($indirec)) {
			$ct = preg_match("/(\w+):(.+)/", $pid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				$service = ServiceClient::getInstance($servid);
				$newrec = $service->mergeGedcomRecord($remoteid, "0 @".$pid."@ FAM\r\n1 RFN ".$pid, false);
				$indirec = $newrec;
			}
		}
		if (empty($indirec)) {
			if ($gGedcom->isEditable() && isset($pgv_changes[$pid."_".$GEDCOM])) {
				$indirec = find_updated_record($pid);
				$fromfile = true;
			}
		}
		if (empty($indirec)) return null;
		$family = new Family($indirec, $simple);
		if (!empty($fromfile)) $family->setChanged(true);
		$famlist[$pid]['object'] = &$family;
		return $family;
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
		return $this->children;
	}

	/**
	 * get the number of children in this family
	 * @return int 	the number of children
	 */
	function getNumberOfChildren() {
		$nchi = get_gedcom_value("NCHI", 1, $this->gedrec);
		if ($nchi!="") return $nchi.".";
		return count($this->children);
	}

	/**
	 * get the family sortable name
	 * @return string
	 */
	function getSortableName($linebr=false) {
		global $pgv_lang;
		$name = "";
		if (is_null($this->husb)) $name .= $pgv_lang["unknown"];
		else {
			$name .= $this->husb->getSortableName();
			if ($linebr) $name .= $this->husb->getSexImage();
		}
		if ($linebr) $name .= "<br />"; else $name .= " + ";
		if (is_null($this->wife)) $name .= $pgv_lang["unknown"];
		else {
			$name .= $this->wife->getSortableName();
			if ($linebr) $name .= $this->wife->getSexImage();
		}
		return $name;
	}

	/**
	 * Check if privacy options allow this record to be displayed
	 * @return boolean
	 */
	function canDisplayDetails() {
		return $this->disp;
	}

	/**
	 * get updated Family
	 * If there is an updated family record in the gedcom file
	 * return a new family object for it
	 */
	function getUpdatedFamily() {
		global $gGedcom, $pgv_changes;
		if ($this->changed) return $this;
		if ( $gGedcom->isEditable() && ($this->disp) ) {
			if (isset($pgv_changes[$this->xref."_".$gGedcom->mGedcomName])) {
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
		foreach($this->children as $key=>$child) {
			if ($person->equals($child)) return true;
		}
		return false;
	}

	/**
	 * parse marriage record
	 */
	function _parseMarriageRecord() {
		$this->marr_rec = trim(get_sub_record(1, "1 MARR", $this->gedrec));
		$this->marr_date = get_gedcom_value("DATE", 2, $this->marr_rec, '', false);
		$this->marr_type = get_sub_record(2, "2 TYPE", $this->marr_rec);
		$this->div_rec = trim(get_sub_record(1, "1 DIV", $this->gedrec));
		//-- 2nd record with alternate date (hebrew...)
		$this->marr_rec2 = trim(get_sub_record(1, "1 MARR", $this->gedrec, 2));
		$this->marr_date2 = get_gedcom_value("DATE", 2, $this->marr_rec2, '', false);
		//-- if no date estimate from births
		/** FIXME
		if (!empty($this->marr_rec) and empty($this->marr_date)) {
			if (!is_null($this->husb)) $h=$this->husb->getBirthYear(); else $h=0;
			if (!is_null($this->wife)) $w=$this->wife->getBirthYear(); else $w=0;
			$myear=max($h,$w);
			if ($myear>0) {
				$this->marr_est=true;
				$this->marr_date="AFT ".($myear+16); // MARR > BIRT+16
			}
		}**/
	}

	/**
	 * get marriage record
	 * @return string
	 */
	function getMarriageRecord() {
		if (is_null($this->marr_rec)) $this->_parseMarriageRecord();
		return $this->marr_rec;
	}

	/**
	 * get divorce record
	 * @return string
	 */
	function getDivorceRecord() {
		if (is_null($this->div_rec)) $this->_parseMarriageRecord();
		return $this->div_rec;
	}

	/**
	 * Return whether or not this family ended in a divorce.
	 * Current implementation returns true if there is a non-empty divorce record.
	 * @return boolean true if there is a non-empty divorce record, false if no divorce record exists
	 */
	function isDivorced() {
		// Bypass privacy rules so we can differentiate Spouse from Ex-Spouse
		return preg_match('/[\r\n]1 DIV( Y)?[\r\n]/', find_gedcom_record($this->xref));
	}

	/**
	 * get marriage date
	 * @return string
	 */
	function getMarriageDate() {
		global $pgv_lang;
		if (!$this->disp) return $pgv_lang["private"];
		if (is_null($this->marr_date)) $this->_parseMarriageRecord();
		return $this->marr_date;
	}

	/**
	 * get sortable marriage date
	 * @return string the marriage date in sortable format YYYY-MM-DD HH:MM
	 */
	function getSortableMarriageDate() {
		if (!$this->disp) return "0000-00-01";
		if (empty($this->marr_date)) $this->_parseMarriageRecord();
		if (empty($this->marr_rec)) return "0000-00-00";
		$pdate = parse_date($this->marr_date);
		$hms = get_gedcom_value("DATE:TIME", 2, $this->marr_rec);
		return $pdate[0]["sort"]." ".$hms;
	}

	/**
	 * get sortable marriage date2
	 * @return string the marriage date2 in sortable format YYYY-MM-DD HH:MM
	 */
	function getSortableMarriageDate2() {
		if (empty($this->marr_date2)) $this->_parseMarriageRecord();
		if (empty($this->marr_rec2)) return "0000-00-00";
		$pdate = parse_date($this->marr_date2);
		$hms = get_gedcom_value("DATE:TIME", 2, $this->marr_rec2);
		return $pdate[0]["sort"]." ".$hms;
	}
	/**
	 * get the type for this marriage
	 * @return string
	 */
	function getMarriageType() {
		if (is_null($this->marr_type)) $this->_parseMarriageRecord();
		return $this->marr_type;
	}

	/**
	 * get the marriage place
	 * @return string
	 */
	function getMarriagePlace() {
		return get_gedcom_value("PLAC", 2, $this->getMarriageRecord(), '', false);
	}

	/**
	 * get the marriage year
	 * @return string
	 */
	function getMarriageYear() {
		return substr($this->getSortableMarriageDate(),0,4);
		/**$marryear = $this->getMarriageDate();
		$mdate = parse_date($marryear);
		$myear = $mdate[0]['year'];
		return $myear;**/
	}

	/**
	 * get divorce date
	 * @return string
	 */
	function getDivorceDate() {
		$drec = $this->getDivorceRecord();
		return get_gedcom_value("DATE", 2, $drec);
	}

	/**
	 * get the type for this marriage
	 * @return string
	 */
	function getDivorceType() {
		$drec = $this->getDivorceRecord();
		return get_gedcom_value("TYPE", 2, $drec);
	}

	/**
	 * get the divorce place
	 * @return string
	 */
	function getDivorcePlace() {
		return get_gedcom_value("PLAC", 2, $this->getDivorceRecord());
	}

	/**
	 * get the divorce year
	 * @return string
	 */
	function getDivorceYear(){
		$divorceyear = $this->getDivorceDate();
		$ddate = parse_date($divorceyear);
		$dyear = $ddate[0]['year'];
		return $dyear;
	}

	/**
	 * get the URL to link to this family
	 * @string a url that can be used to link to this family
	 */
	function getLinkUrl() {
		global $GEDCOM;

		$url = "family.php?famid=".$this->getXref()."&amp;ged=".$GEDCOM;
		if ($this->isRemote()) {
			$parts = preg_split("/:/", $this->rfn);
			if (count($parts)==2) {
				$servid = $parts[0];
				$aliaid = $parts[1];
				if (!empty($servid)&&!empty($aliaid)) {
					$servrec = find_gedcom_record($servid);
					if (empty($servrec)) $servrec = find_updated_record($servid);
					if (!empty($servrec)) {
						$surl = get_gedcom_value("URL", 1, $servrec);
						$url = "family.php?famid=".$aliaid;
						if (!empty($surl)) $url = dirname($surl)."/".$url;
						$gedcom = get_gedcom_value("_DBID", 1, $servrec);
						if (!empty($gedcom)) $url.="&amp;ged=".$gedcom;
					}
				}
			}
		}
		return $url."#content";
	}
}
?>

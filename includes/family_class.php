<?php
/**
 * Class file for a Family
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008	John Finlay and Others
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
 * @version $Id: family_class.php,v 1.7 2008/08/11 14:19:55 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once PHPGEDVIEW_PKG_PATH.'includes/gedcomrecord.php';
require_once PHPGEDVIEW_PKG_PATH.'includes/person_class.php';

class Family extends GedcomRecord {
	var $husb = null;
	var $wife = null;
	var $children = array();
	var $childrenIds = array();
	var $disp = true;
	var $marr_rec = null;
	var $marr_date = null;
	var $marr_type = null;
	var $marr_est = false; // estimate
	var $div_rec = null;
	var $children_loaded = false;
	var $numChildren = false;

	/**
	 * constructor
	 * @param string $gedrec	the gedcom record
	 */
	function Family($gedrec, $simple=true) {
		global $pgv_changes, $GEDCOM;

		//-- get the husbands ids
		$husb = $gedrec['f_husb'];
		if (!empty($husb)) $this->husb = Person::getInstance($husb, $simple);
		//-- get the wifes ids
		$wife = $gedrec['f_wife'];
		if (!empty($wife)) $this->wife = Person::getInstance($wife, $simple);
		//-- load the parents before privatizing the record because the parents may be remote records
		parent::GedcomRecord($gedrec['f_gedcom']);
		$this->disp = displayDetailsById($this->xref, "FAM");
	}

	/**
	 * Static function used to get an instance of a family object
	 * @param string $pid	the ID of the family to retrieve
	 */
	function &getInstance($pid, $simple=true) {
		global $gedcom_record_cache, $GEDCOM, $pgv_changes;

		$ged_id=get_id_from_gedcom($GEDCOM);
		// Check the cache first
		if (isset($gedcom_record_cache[$pid][$ged_id])) {
			return $gedcom_record_cache[$pid][$ged_id];
		}

		$gedrec = find_family_record($pid);
		if (empty($gedrec)) {
			$ct = preg_match("/(\w+):(.+)/", $pid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				require_once 'includes/serviceclient_class.php';
				$service = ServiceClient::getInstance($servid);
				if ($service) $gedrec = $service->mergeGedcomRecord($remoteid, "0 @".$pid."@ FAM\r\n1 RFN ".$pid, false);
			}
		}
		if (empty($gedrec)) {
			if (PGV_USER_CAN_EDIT && isset($pgv_changes[$pid."_".$GEDCOM])) {
				$gedrec = find_updated_record($pid);
				$fromfile = true;
			}
		}

		if (empty($gedrec)) return null;
		$object = new Family($gedrec, $simple);
		if (!empty($fromfile)) $object->setChanged(true);
		// Store the object in the cache
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
		//-- load the children with one query
		load_people($this->childrenIds);
		foreach($this->childrenIds as $t=>$chil) {
			$child = Person::getInstance($chil);
			if ( !is_null($child)) $this->children[] = $child;
		}
		$this->children_loaded = true;
	}

	/**
	 * get the number of children in this family
	 * @return int 	the number of children
	 */
	function getNumberOfChildren() {
		global $famlist;
		
		if ($this->numChildren!==false) return $this->numChildren;
		if (isset($famlist[$this->xref]['numchil'])) {
			$this->numChildren = $famlist[$this->xref]['numchil'];
			return $this->numChildren; 
		}
		
		$this->numChildren = get_gedcom_value("NCHI", 1, $this->gedrec);
		if ($this->numChildren!="") return $this->numChildren.".";
		$this->numChildren = preg_match_all("/1\s*CHIL\s*@(.*)@/", $this->gedrec, $smatch);
		return $this->numChildren;
	}

	/**
	 * get the family name
	 * @return string
	 */
	function getName() {
		global $pgv_lang;
		$name = "";
		if (is_null($this->husb)) $name .= $pgv_lang["unknown"];
		else {
			$name .= $this->husb->getName();
		}
		$name .= " + ";
		if (is_null($this->wife)) $name .= $pgv_lang["unknown"];
		else {
			$name .= $this->wife->getName();
		}
		return $name;
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
	function &getUpdatedFamily() {
		global $GEDCOM, $pgv_changes;
		if ($this->changed) return $this;
		if (PGV_USER_CAN_EDIT && $this->disp) {
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
		$this->marr_rec = trim(get_sub_record(1, "1 MARR", $this->gedrec));
		$this->marr_date = get_gedcom_value("DATE", 2, $this->marr_rec, '', false);
		$this->marr_type = get_gedcom_value("TYPE", 2, $this->marr_rec, '', false);
		$this->div_rec = trim(get_sub_record(1, "1 DIV", $this->gedrec));
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
	 * get the marriage year
	 * @return string
	 */
	function getMarriageYear($est = true, $cal = ""){
		// TODO - change the design to use julian days, not gregorian years.
		$mdate = new GedcomDate($this->getMarriageDate());
		$mdate=$mdate->MinDate();
		if ($cal) $mdate=$mdate->convert_to_cal($cal);
		return $mdate->y;
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

	// Get all the dates/places for marriages - for the FAM lists
	function getAllMarriageDates() {
		if ($this->canDisplayDetails()) {
			foreach (array('MARR') as $event) {
				if ($array=$this->getAllEventDates($event)) {
					return $array;
				}
			}
		}
		return array();
	}
	function getAllMarriagePlaces() {
		if ($this->canDisplayDetails()) {
			foreach (array('MARR') as $event) {
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
	function getLinkUrl() {
		return parent::getLinkUrl('family.php?famid=');
	}
}
?>

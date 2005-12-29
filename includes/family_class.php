<?php
/**
 * Class file for a Family
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * Page does not validate see line number 1109 -> 15 August 2005
 *
 * @package PhpGedView
 * @subpackage DataModel
 * @version $Id: family_class.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */
require_once 'includes/gedcomrecord.php';
require_once 'includes/person_class.php';
require_once 'includes/serviceclient_class.php';

class Family extends GedcomRecord {
	var $husb = null;
	var $wife = null;
	var $children = array();
	var $disp = true;
	var $marr_rec = null;
	var $marr_date = null;
	var $marr_type = null;
	var $div_rec = null;
	
	/**
	 * constructor
	 * @param string $gedrec	the gedcom record
	 */
	function Family($gedrec) {
		global $pgv_changes, $GEDCOM;
		
		parent::GedcomRecord($gedrec);
		$this->disp = displayDetailsById($this->xref, "FAM");
		$husbrec = get_sub_record(1, "1 HUSB", $gedrec);
		if (!empty($husbrec)) {
			//-- get the husbands ids
			$husb = get_gedcom_value("HUSB", 1, $husbrec);
			//-- check if husb is in another gedcom
			$ct = preg_match("/(\w+):(.+)/", $husb, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				$service = ServiceClient::getInstance($servid);
				$indirec = $service->mergeGedcomRecord($remoteid, "0 @".$husb."@ INDI\r\n1 RFN ".$husb);
			}
			else {
				$indirec = find_person_record($husb);
			}
			if (empty($indirec)) $indirec = find_record_in_file($husb);
			$this->husb = new Person($indirec);
		}
		$wiferec = get_sub_record(1, "1 WIFE", $gedrec);
		if (!empty($wiferec)) {
			//-- get the wifes ids
			$wife = get_gedcom_value("WIFE", 1, $wiferec);
			//-- check if wife is in another gedcom
			$ct = preg_match("/(\w+):(.+)/", $wife, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				$service = ServiceClient::getInstance($servid);
				$indirec = $service->mergeGedcomRecord($remoteid, "0 @".$wife."@ INDI\r\n1 RFN ".$wife);
			}
			else {
				 $indirec = find_person_record($wife);
			}
			if (empty($indirec)) $indirec = find_record_in_file($wife);
			$this->wife = new Person($indirec);
		}
		$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $gedrec, $smatch, PREG_SET_ORDER);
		for($i=0; $i<$num; $i++) {
			//-- get the childs ids
			$chil = trim($smatch[$i][1]);
			//-- check if wife is in another gedcom
			$ct = preg_match("/(\w+):(.+)/", $chil, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				$service = ServiceClient::getInstance($servid);
				$indirec = $service->mergeGedcomRecord($remoteid, "0 @".$chil."@ INDI\r\n1 RFN ".$chil);
			}
			else {
				$indirec = find_person_record($chil);
			}
			if (empty($indirec)) $indirec = find_record_in_file($chil);
			$this->children[] = new Person($indirec);
		}
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
		return count($this->children);
	}
	  
	/**
	 * get updated Family
	 * If there is an updated family record in the gedcom file
	 * return a new family object for it
	 */
	function &getUpdatedFamily() {
		global $GEDCOM, $pgv_changes;
		if ($this->changed) return null;
		if (userCanEdit(getUserName())&&($this->disp)) {
			if (isset($pgv_changes[$this->xref."_".$GEDCOM])) {
				$newrec = find_record_in_file($this->xref);
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
		$this->marr_rec = get_sub_record(1, "1 MARR", $this->gedrec);
		$this->marr_date = get_sub_record(2, "2 DATE", $this->marr_rec);
		$this->marr_type = get_sub_record(2, "2 TYPE", $this->marr_rec);
		$this->div_rec = get_sub_record(1, "1 DIV", $this->gedrec);
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
	 * get marriage date
	 * @return string
	 */
	function getMarriageDate() {
		if (is_null($this->marr_date)) $this->_parseMarriageRecord();
		return $this->marr_date;
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
	 * get the URL to link to this person
	 * @string a url that can be used to link to this person
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
					if (empty($servrec)) $servrec = find_record_in_file($servid);
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
		return $url;
	}
}
?>
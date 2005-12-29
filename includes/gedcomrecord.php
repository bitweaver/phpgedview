<?php
/**
 * Base class for all gedcom records
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
 * @version $Id: gedcomrecord.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */
class GedcomRecord {
	var $gedrec = "";
	var $xref = "";
	var $type = "";
	var $changed = false;
	var $rfn = null;
	
	/**
	 * constructor for this class
	 */
	function GedcomRecord($gedrec) {
		if (empty($gedrec)) return;
		//-- set the gedcom record a privatized version
		$this->gedrec = privatize_gedcom($gedrec);
		$ct = preg_match("/0 @(.*)@ (.*)/", $this->gedrec, $match);
		if ($ct>0) {
			$this->xref = trim($match[1]);
			$this->type = trim($match[2]);
		}
	}
	/**
	 * get the xref
	 */
	function getXref() {
		return $this->xref;
	}
	/**
	 * get the object type
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
	 * get the URL to link to this person
	 * @string a url that can be used to link to this person
	 */
	function getLinkUrl() {
		global $GEDCOM, $SERVER_URL;
		
		$url = $SERVER_URL;
		if ($this->isRemote()) {
			$parts = preg_split("/:/", $this->rfn);
			$servid = $parts[0];
			$aliaid = $parts[1];
			$servrec = find_gedcom_record($servid);
			if (empty($servrec)) $servrec = find_record_in_file($servid);
			if (!empty($servrec)) {
				$surl = get_gedcom_value("URL", 1, $servrec);
				$url = dirname($surl);
				$gedcom = get_gedcom_value("_DBID", 1, $servrec);
				if (!empty($gedcom)) $url.="?ged=".$gedcom;
			}
		}
		return $url;
	}
	
	/**
	 * Undo the latest change to this gedcom record
	 */
	function undoChange() {
		global $GEDCOM, $pgv_changes;
		require_once('includes/functions_edit.php');
		if (!userCanAccept(getUserName())) return false;
		$cid = $this->xref."_".$GEDCOM;
		if (!isset($pgv_changes[$cid])) return false;
		$index = count($pgv_changes[$cid])-1;
		if (undo_change($cid, $index)) return true;
		return false;
	}
}
?>
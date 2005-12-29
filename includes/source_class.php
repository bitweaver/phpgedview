<?php
/**
 * Class file for a Source (SOUR) object
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
 * @package PhpGedView
 * @subpackage DataModel
 * @version $Id: source_class.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */

require_once 'includes/gedcomrecord.php';

class Source extends GedcomRecord {
	var $disp = true;
	var $name = "";
	var $sourcefacts = null;
	var $indilist = null;
	var $famlist = null;
	
	/**
	 * Constructor for source object
	 * @param string $gedrec	the raw source gedcom record
	 */
	function Source($gedrec) {
		parent::GedcomRecord($gedrec);
		$this->disp = displayDetailsByID($this->xref, "SOUR");
		$this->name = get_source_descriptor($this->xref);
		$add_descriptor = get_add_source_descriptor($this->xref);
		if ($add_descriptor) $this->name .= " - ".$add_descriptor;
	}
	
	/**
	 * Check if privacy options allow this record to be displayed
	 * @return boolean
	 */
	function canDisplayDetails() {
		return $this->disp;
	}
	
	/**
	 * get the title of this source record
	 * @return string
	 */
	function getTitle() {
		global $pgv_lang;
		
		if (empty($this->name)) return $pgv_lang["unknown"];
		return $this->name;
	}
	
	/**
	 * get source facts array
	 * @return array
	 */
	function getSourceFacts() {
		$this->parseFacts();
		return $this->sourcefacts;
	}
	
	/**
	 * Parse the facts from the individual record
	 */
	function parseFacts() {
		if (!is_null($this->sourcefacts)) return;
		$this->sourcefacts = array();
		$gedlines = preg_split("/\n/", $this->gedrec);
		$lct = count($gedlines);
		$factrec = "";	// -- complete fact record
		$line = "";	// -- temporary line buffer
		$linenum = 1;
		for($i=1; $i<=$lct; $i++) {
			if ($i<$lct) $line = $gedlines[$i];
			else $line=" ";
			if (empty($line)) $line=" ";
			if (($i==$lct)||($line{0}==1)) {
				if (!empty($factrec) ) {
					$this->sourcefacts[] = array($factrec, $linenum);
				}
				$factrec = $line;
				$linenum = $i;
			}
			else $factrec .= "\n".$line;
		}
	}
	
	/**
	 * Merge the facts from another Source object into this object
	 * for generating a diff view
	 * @param Source $diff	the source to compare facts with
	 */
	function diffMerge(&$diff) {
		if (is_null($diff)) return;
		$this->parseFacts();
		$diff->parseFacts();
		
		//-- update old facts
		foreach($this->sourcefacts as $key=>$fact) {
			$found = false;
			foreach($diff->sourcefacts as $indexval => $newfact) {
				$newfact=preg_replace("/\\\/", "/", $newfact);
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$this->sourcefacts[$key][0].="\nPGV_OLD\n";
			}
		}
		//-- look for new facts
		foreach($diff->sourcefacts as $key=>$newfact) {
			$found = false;
			foreach($this->sourcefacts as $indexval => $fact) {
				$newfact=preg_replace("/\\\/", "/", $newfact);
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$newfact[0].="\nPGV_NEW\n";
				$this->sourcefacts[]=$newfact;
			}
		}
	}
	
	/**
	 * get the list of individuals connected to this source
	 * @return array
	 */
	function getSourceIndis() {
		global $REGEXP_DB;
		if (!is_null($this->indilist)) return $this->indilist;
		$query = "SOUR @".$this->xref."@";
		if (!$REGEXP_DB) $query = "%".$query."%";
		
		$this->indilist = search_indis($query);
		uasort($this->indilist, "itemsort");
		return $this->indilist;
	}
	
	/**
	 * get the list of families connected to this source
	 * @return array
	 */
	function getSourceFams() {
		global $REGEXP_DB;
		if (!is_null($this->famlist)) return $this->famlist;
		$query = "SOUR @".$this->xref."@";
		if (!$REGEXP_DB) $query = "%".$query."%";

		$this->famlist = search_fams($query);
		uasort($this->famlist, "itemsort");
		return $this->famlist;
	}
}
?>
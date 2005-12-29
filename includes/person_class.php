<?php
/**
 * Class file for a person
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
 * @version $Id: person_class.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */
require_once 'includes/gedcomrecord.php';
require_once 'includes/family_class.php';
require_once 'includes/serviceclient_class.php';

class Person extends GedcomRecord {
	var $sex = "U";
	var $disp = true;
	var $dispname = true;
	var $indifacts = array();
	var $otherfacts = array();
	var $globalfacts = array();
	var $mediafacts = array();
	var $facts_parsed = false;
	var $sosamax = 7;
	var $bdate = "";
	var $ddate = "";
	var $brec = "";
	var $drec = "";
	var $fams = null;
	var $famc = null;
	var $spouseFamilies = null;
	var $childFamilies = null;
	var $label = "";
	var $highlightedimage = null;
	var $file = "";
	/**
	 * Constructor for person object
	 * @param string $gedrec	the raw individual gedcom record
	 */
	function Person($gedrec) {
		global $MAX_ALIVE_AGE;
		
		$remoterfn = get_gedcom_value("RFN", 1, $gedrec);
		if (!empty($remoterfn)) {
			$parts = preg_split("/:/", $remoterfn);
			if (count($parts)==2) {
				$servid = $parts[0];
				$aliaid = $parts[1];
				if (!empty($servid)&&!empty($aliaid)) {
					$serviceClient = ServiceClient::getInstance($servid);
					if (!is_null($serviceClient)) {
						$gedrec = $serviceClient->mergeGedcomRecord($aliaid, $gedrec);
					}
				}
			}
		}
		
		parent::GedcomRecord($gedrec);
		$st = preg_match("/1 SEX (.*)/", $gedrec, $smatch);
		if ($st>0) $this->sex = trim($smatch[1]);
		if (empty($this->sex)) $this->sex = "U";
		$this->disp = displayDetails($gedrec);
		$this->dispname = showLivingName($gedrec);
	}
	/**
	 * get the persons name
	 * @return string
	 */
	function getName() {
		global $pgv_lang;
		if (!$this->canDisplayName()) return $pgv_lang["private"];
		$name = get_person_name($this->xref);
		if (empty($name)) return $pgv_lang["unknown"];
		return $name;
	}
	/**
	 * Check if an additional name exists for this person
	 * @return string
	 */
	function getAddName() {
		if (!$this->canDisplayName()) return "";
		return get_add_person_name($this->xref);
	}
	/**
	 * Check if privacy options allow this record to be displayed
	 * @return boolean
	 */
	function canDisplayDetails() {
		return $this->disp;
	}
	/**
	 * Check if privacy options allow the display of the persons name
	 * @return boolean
	 */
	function canDisplayName() {
		return ($this->disp || $this->dispname);
	}
	/**
	 * get highlighted media
	 * @return array
	 */
	function findHighlightedMedia() {
		if (is_null($this->highlightedimage)) {
			$this->highlightedimage = find_highlighted_object($this->xref, $this->gedrec);
		}
		return $this->highlightedimage;
	}
	/**
	 * parse birth and death records
	 */
	function _parseBirthDeath() {
		global $MAX_ALIVE_AGE;
		$this->brec = get_sub_record(1, "1 BIRT", $this->gedrec);
		$this->bdate = get_sub_record(2, "2 DATE", $this->brec);
		$this->drec = get_sub_record(1, "1 DEAT", $this->gedrec);
		$this->ddate = get_sub_record(2, "2 DATE", $this->drec);
		if (empty($this->ddate) && !empty($this->bdate)) {
			$pdate=parse_date(substr($this->bdate,6));
			if ($pdate[0]["year"]>0) $this->ddate = "2 DATE BEF ".($pdate[0]["year"]+$MAX_ALIVE_AGE);
		}
		if (empty($this->bdate) && !empty($this->ddate)) {
			$pdate=parse_date(substr($this->ddate,6));
			if ($pdate[0]["year"]>0) $this->bdate = "2 DATE AFT ".($pdate[0]["year"]-$MAX_ALIVE_AGE);
		}
	}
	/**
	 * get birth record
	 * @return string
	 */
	function getBirthRecord() {
		if (empty($this->brec)) $this->_parseBirthDeath();
		return $this->brec;
	}
	/**
	 * get death record
	 * @return string
	 */
	function getDeathRecord() {
		if (empty($this->drec)) $this->_parseBirthDeath();
		return $this->drec;
	}
	/**
	 * get birth date
	 * @return string
	 */
	function getBirthDate() {
		if (empty($this->bdate)) $this->_parseBirthDeath();
		return $this->bdate;
	}
	/**
	 * get death date
	 * @return string
	 */
	function getDeathDate() {
		if (empty($this->ddate)) $this->_parseBirthDeath();
		return $this->ddate;
	}
	/**
	 * get the person's sex
	 * @return string 	return M, F, or U
	 */
	function getSex() {
		return $this->sex;
	}

	/**
	 * set a label for this person
	 * The label can be used when building a list of people
	 * to display the relationship between this person
	 * and the person listed on the page
	 * @param string $label
	 */
	function setLabel($label) {
		$this->label = $label;
	}
	/**
	 * get the label for this person
	 * The label can be used when building a list of people
	 * to display the relationship between this person
	 * and the person listed on the page
	 * @return string
	 */
	function getLabel() {
		return $this->label;
	}
	/**
	 * get family with spouse ids
	 * @return array	array of the FAMS ids
	 */
	function getSpouseFamilyIds() {
		if (!is_null($this->fams)) return $this->fams;
		$this->fams = find_families_in_record($this->gedrec, "FAMS");
		return $this->fams;
	}
	/**
	 * get the families with spouses
	 * @return array	array of Family objects
	 */
	function getSpouseFamilies() {
		if (!is_null($this->spouseFamilies)) return $this->spouseFamilies;
		$fams = $this->getSpouseFamilyIds();
		$families = array();
		foreach($fams as $key=>$famid) {
			if (!empty($famid)) {
				$ct = preg_match("/(\w+):(.+)/", $famid, $match);
				if ($ct>0) {
					$servid = trim($match[1]);
					$remoteid = trim($match[2]);
					$service = ServiceClient::getInstance($servid);
					$famrec = $service->mergeGedcomRecord($remoteid, "0 @".$famid."@ FAM\r\n1 RFN ".$famid);
				}
				else {
					$famrec = find_family_record($famid);
					if (empty($famrec)) {
						$famrec = find_record_in_file($famid);
						$fromfile = true;
					}
				}
				$family = new Family($famrec);
				if (!empty($fromfile)) $family->setChanged(true);
				$families[$famid] = $family;
			}
		}
		$this->spouseFamilies = $families;
		return $families;
	}
	/**
	 * get family with child ids
	 * @return array	array of the FAMC ids
	 */
	function getChildFamilyIds() {
		if (!is_null($this->famc)) return $this->famc;
		$this->famc = find_families_in_record($this->gedrec, "FAMC");
		return $this->famc;
	}
	/**
	 * get the families with parents
	 * @return array	array of Family objects
	 */
	function getChildFamilies() {
		if (!is_null($this->childFamilies)) return $this->childFamilies;
		$fams = $this->getChildFamilyIds();
		$families = array();
		foreach($fams as $key=>$famid) {
			if (!empty($famid)) {
				$ct = preg_match("/(\w+):(.+)/", $famid, $match);
				if ($ct>0) {
					$servid = trim($match[1]);
					$remoteid = trim($match[2]);
					$service = ServiceClient::getInstance($servid);
					$famrec = $service->mergeGedcomRecord($remoteid, "0 @".$famid."@ FAM\r\n1 RFN ".$famid);
				}
				else {
					$famrec = find_family_record($famid);
					if (empty($famrec)) {
						$famrec = find_record_in_file($famid);
						$fromfile = true;
					}
				}
				if (empty($famrec)) $famrec = "0 @".$famid."@ FAM\r\n1 CHIL ".$this->xref;
				$family = new Family($famrec);
				if (!empty($fromfile)) $family->setChanged(true);
				$families[$famid] = $family;
			}
		}
		$this->childFamilies = $families;
		return $families;
	}
	/**
	 * get the step families from the parents
	 * @return array	array of Family objects
	 */
	function getStepFamilies() {
		$families = array();
		$fams = $this->getChildFamilies();
		foreach($fams as $key=>$family) {
			if (!is_null($family)) {
				$father = $family->getHusband();
				if (!is_null($father)) {
					$pfams = $father->getSpouseFamilies();
					foreach($pfams as $key1=>$fam) {
						if (!$family->equals($fam) && ($fam->getNumberOfChildren() > 0)) $families[$key1] = $fam;
					}
				}
				$mother = $family->getWife();
				if (!is_null($mother)) {
					$pfams = $mother->getSpouseFamilies();
					foreach($pfams as $key1=>$fam) {
						if (!$family->equals($fam) && ($fam->getNumberOfChildren() > 0)) $families[$key1] = $fam;
					}
				}
			}
		}
		return $families;
	}
	/**
	 * get global facts
	 * @return array
	 */
	function getGlobalFacts() {
		$this->parseFacts();
		return $this->globalfacts;
	}
	/**
	 * get indi facts
	 * @return array
	 */
	function getIndiFacts() {
		$this->parseFacts();
		return $this->indifacts;
	}
	/**
	 * get other facts
	 * @return array
	 */
	function getOtherFacts() {
		$this->parseFacts();
		return $this->otherfacts;
	}
	/**
	 * get the correct label for a family
	 * @param Family $family		the family to get the label for
	 * @return string
	 */
	function getChildFamilyLabel(&$family) {
		global $pgv_lang;
		$famlink = get_sub_record(1, "1 FAMC @".$family->getXref()."@", $this->gedrec);
		$ft = preg_match("/2 PEDI (.*)/", $famlink, $fmatch);
		if ($ft>0) {
			$temp = trim($fmatch[1]);
			if (isset($pgv_lang[$temp])) return $pgv_lang[$temp]." ";
		}
		return $pgv_lang["as_child"];
	}
	/**
	 * get the correct label for a step family
	 * @param Family $family		the family to get the label for
	 * @return string
	 */
	function getStepFamilyLabel(&$family) {
		global $pgv_lang;
		$label = "Unknown Family";
		if (is_null($family)) return $label;
		$childfams = $this->getChildFamilies();
		$mother = $family->getWife();
		$father = $family->getHusband();
		foreach($childfams as $key=>$fam) {
			if (!$fam->equals($family)) {
				$wife = $fam->getWife();
				$husb = $fam->getHusband();
				if ((is_null($husb) || !$husb->equals($father)) && (is_null($wife)||$wife->equals($mother))) {
					if ($mother->getSex()=="F") $label = $pgv_lang["mothers_family_with"];
					else $label = $pgv_lang["fathers_family_with"];
					if (!is_null($father)) $label .= $father->getName();
					else $label .= $pgv_lang["unknown"];
				}
				else if ((is_null($wife) || !$wife->equals($mother)) && (is_null($husb)||$husb->equals($father))) {
					if ($father->getSex()=="F") $label = $pgv_lang["mothers_family_with"];
					else $label = $pgv_lang["fathers_family_with"];
					if (!is_null($mother)) $label .= $mother->getName();
					else $label .= $pgv_lang["unknown"];
				}
				if ($label!="Unknown Family") return $label;
			}
		}
		return $label;
	}
	/**
	 * get the correct label for a family
	 * @param Family $family		the family to get the label for
	 * @return string
	 */
	function getSpouseFamilyLabel(&$family) {
		global $pgv_lang;
		$label = $pgv_lang["family_with"] . " ";
		$famlink = get_sub_record(1, "1 FAMS @".$family->getXref()."@", $this->gedrec);
		$ft = preg_match("/2 PEDI (.*)/", $famlink, $fmatch);
		if ($ft>0) {
			$temp = trim($fmatch[1]);
			if (isset($pgv_lang[$temp])) $label = $pgv_lang[$temp]." ";
		}
		$husb = $family->getHusband();
		$wife = $family->getWife();
		if ($this->equals($husb)) {
			if (!is_null($wife)) $label .= $wife->getName();
			else $label .= $pgv_lang["unknown"];
		}
		else {
			if (!is_null($husb)) $label .= $husb->getName();
			else $label .= $pgv_lang["unknown"];
		}
		return $label;
	}
	/**
	 * get updated Person
	 * If there is an updated individual record in the gedcom file
	 * return a new person object for it
	 * @return Person
	 */
	function &getUpdatedPerson() {
		global $GEDCOM, $pgv_changes;
		if ($this->changed) return null;
		if (userCanEdit(getUserName())&&($this->disp)) {
			if (isset($pgv_changes[$this->xref."_".$GEDCOM])) {
				$newrec = find_record_in_file($this->xref);
				if (!empty($newrec)) {
					$new = new Person($newrec);
					$new->setChanged(true);
					return $new;
				}
			}
		}
		return null;
	}
	/**
	 * Parse the facts from the individual record
	 */
	function parseFacts() {
		global $nonfacts;
		//-- only run this function once
		if ($this->facts_parsed) return;
		//-- don't run this function if privacy does not allow viewing of details
		if (!$this->canDisplayDetails()) return;
		$this->facts_parsed = true;
		//-- find all the fact information
		$indilines = split("\n", $this->gedrec);   // -- find the number of lines in the individuals record
		$lct = count($indilines);
		$factrec = "";	 // -- complete fact record
		$line = "";   // -- temporary line buffer
		$f=0;	   // -- counter
		$o = 0;
		$g = 0;
		$m = 0;
		$linenum=1;
		$sexfound = false;
		for($i=1; $i<=$lct; $i++) {
		   if ($i<$lct) $line = preg_replace("/\r/", "", $indilines[$i]);
		   else $line=" ";
		   if (empty($line)) $line=" ";
		   //print "line:".$line."<br />";
		   if (($i==$lct)||($line{0}==1)) {
				  $ft = preg_match("/1\s(\w+)(.*)/", $factrec, $match);
				  if ($ft>0) $fact = $match[1];
				  else $fact="";
				  $fact = trim($fact);
				  // -- handle special name fact case
				  if ($fact=="NAME") {
						 $this->globalfacts[$g] = array($linenum, $factrec);
						 $g++;
				  }
				  // -- handle special source fact case
				  else if ($fact=="SOUR") {
						 $this->otherfacts[$o] = array($linenum, $factrec);
						 $o++;
				  }
				  // -- handle special note fact case
				  else if ($fact=="NOTE") {
						 $this->otherfacts[$o] = array($linenum, $factrec);
						 $o++;
				  }
				  // -- handle special sex case
				  else if ($fact=="SEX") {
						 $this->globalfacts[$g] = array($linenum, $factrec);
						 $g++;
						 $sexfound = true;
				  }
				  else if (!in_array($fact, $nonfacts)) {
						 $this->indifacts[$f]=array($linenum, $factrec);
						 $f++;
				  }
				  $factrec = $line;
				  $linenum = $i;
		   }
		   else $factrec .= "\n".$line;
		}
		//-- add a new sex fact if one was not found
		if (!$sexfound) {
			$this->globalfacts[$g] = array('new', "1 SEX U");
			$g++;
		}
	}
	/**
	 * add facts from the family record
	 */
	function add_family_facts() {
		global $GEDCOM, $nonfacts, $nonfamfacts;

		if (!$this->canDisplayDetails()) return;
		$this->parseFacts();
		//-- Get the facts from the family with spouse (FAMS)
		$ct = preg_match_all("/1\s+FAMS\s+@(.*)@/", $this->gedrec, $fmatch, PREG_SET_ORDER);
		for($j=0; $j<$ct; $j++) {
			$famid = $fmatch[$j][1];
			$famrec = find_family_record($famid);
			$parents=find_parents_in_record($famrec);
			if ($parents['HUSB']==$this->xref) $spouse=$parents['WIFE'];
			else $spouse=$parents['HUSB'];
			$indilines = split("\n", $famrec);	 // -- find the number of lines in the individuals record
			$lct = count($indilines);
			$factrec = "";	 // -- complete fact record
			$line = "";   // -- temporary line buffer
			$linenum = 0;
			for($i=1; $i<=$lct; $i++) {
				if ($i<$lct) $line = preg_replace("/\r/", "", $indilines[$i]);
				else $line=" ";
				if (empty($line)) $line=" ";
				if (($i==$lct)||($line{0}==1)) {
					$ft = preg_match("/1\s(\w+)(.*)/", $factrec, $match);
					if ($ft>0) $fact = $match[1];
					else $fact="";
					$fact = trim($fact);
					// -- handle special source fact case
					if (($fact!="SOUR") && ($fact!="OBJE") && ($fact!="NOTE") && ($fact!="CHAN") && ($fact!="_UID") && ($fact!="RIN")) {
						if ((!in_array($fact, $nonfacts))&&(!in_array($fact, $nonfamfacts))) {
							$factrec.="\r\n1 _PGVS @$spouse@\r\n";
							$factrec.="1 _PGVFS @$famid@\r\n";
							$this->indifacts[]=array($linenum, $factrec);
						}
					}
					else if ($fact=="OBJE") {
						$factrec.="\r\n1 _PGVS @$spouse@\r\n";
						$factrec.="1 _PGVFS @$famid@\r\n";
						$this->otherfacts[]=array($linenum, $factrec);
					}
					$factrec = $line;
					$linenum = $i;
				}
				else $factrec .= "\n".$line;
			}
			$this->add_spouse_facts($spouse, $famrec);
			$this->add_children_facts($famid);
		}
		//$sosamax=7;
		$this->add_parents_facts($this->xref);
		$this->add_historical_facts();
		$this->add_asso_facts($this->xref);
	}
	/**
	 * add parents events to individual facts array
	 *
	 * sosamax = sosa max for recursive call
	 * bdate = indi birth date record
	 * ddate = indi death date record
	 *
	 * @param string $pid	Gedcom id
	 * @param int $sosa		2=father 3=mother ...
	 * @return records added to indifacts array
	 */
	function add_parents_facts($pid, $sosa=1) {
		global $SHOW_RELATIVES_EVENTS;
		if (!$SHOW_RELATIVES_EVENTS) return;
		if ($sosa>$this->sosamax) return;
		if (empty($this->brec)) $this->_parseBirthDeath();
		//-- find family as child
		$famids = find_family_ids($pid);
		foreach ($famids as $indexval=>$famid) {
			$parents = find_parents($famid);
			// add father death
			$spouse = $parents["HUSB"];
			if ($sosa==1) $fact="_DEAT_FATH"; else $fact="_DEAT_GPAR";
			if ($spouse and strstr($SHOW_RELATIVES_EVENTS, $fact)) {
				$srec = get_sub_record(1, "1 DEAT", find_person_record($spouse));
				$sdate = get_sub_record(2, "2 DATE", $srec);
				if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
					$factrec = "1 ".$fact;
					$factrec .= "\n".trim($sdate);
					if (!showFact("DEAT", $spouse)) $factrec .= "\n2 RESN privacy";
					$factrec .= "\n2 ASSO @".$spouse."@";
					$factrec .= "\n3 RELA sosa_".($sosa*2);
					$this->indifacts[]=array(0, $factrec);
				}
			}
			if ($sosa==1) $this->add_stepsiblings_facts($spouse, $famid); // stepsiblings with father
			$this->add_parents_facts($spouse, $sosa*2); // recursive call for father ancestors
			// add mother death
			$spouse = $parents["WIFE"];
			if ($sosa==1) $fact="_DEAT_MOTH"; else $fact="_DEAT_GPAR";
			if ($spouse and strstr($SHOW_RELATIVES_EVENTS, $fact)) {
				$srec = get_sub_record(1, "1 DEAT", find_person_record($spouse));
				$sdate = get_sub_record(2, "2 DATE", $srec);
				if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
					$factrec = "1 ".$fact;
					$factrec .= "\n".trim($sdate);
					if (!showFact("DEAT", $spouse)) $factrec .= "\n2 RESN privacy";
					$factrec .= "\n2 ASSO @".$spouse."@";
					$factrec .= "\n3 RELA sosa_".($sosa*2+1);
					$this->indifacts[]=array(0, $factrec);
				}
			}
			if ($sosa==1) $this->add_stepsiblings_facts($spouse, $famid); // stepsiblings with mother
			$this->add_parents_facts($spouse, $sosa*2+1); // recursive call for mother ancestors
			if ($sosa>3) return;
			// add father/mother marriages
			if (is_array($parents)) {
				foreach ($parents as $indexval=>$spid) {
					if ($spid==$parents["HUSB"]) {
						$fact="_MARR_FATH";
						$rela="father";
					}
					else {
						$fact="_MARR_MOTH";
						$rela="mother";
					}
					if (strstr($SHOW_RELATIVES_EVENTS, $fact)) {
						$sfamids = find_sfamily_ids($spid);
						foreach ($sfamids as $indexval=>$sfamid) {
							if ($sfamid==$famid and $rela=="mother") continue; // show current family marriage only for father
							$childrec = find_family_record($sfamid);
							$srec = get_sub_record(1, "1 MARR", $childrec);
							$sdate = get_sub_record(2, "2 DATE", $srec);
							if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
								$factrec = "1 ".$fact;
								$factrec .= "\n".trim($sdate);
								if (!showFact("MARR", $sfamid)) $factrec .= "\n2 RESN privacy";
								$factrec .= "\n2 ASSO @".$spid."@";
								$factrec .= "\n3 RELA ".$rela;
								$sparents = find_parents($sfamid);
								$spouse = $sparents["HUSB"];
								if ($spouse==$spid) $spouse = $sparents["WIFE"];
								if ($rela=="father") $rela2="stepmom";
								else $rela2="stepdad";
								if ($sfamid==$famid) $rela2="mother";
								$factrec .= "\n2 ASSO @".$spouse."@";
								$factrec .= "\n3 RELA ".$rela2;
								$factrec .= "\n2 ASSO @".$sfamid."@";
								$factrec .= "\n3 RELA family";
								$this->indifacts[]=array(0, $factrec);
							}
						}
					}
				}
			}
			//-- find siblings
			$this->add_children_facts($famid,$sosa, $pid);
		}
	}
	/**
	 * add children events to individual facts array
	 *
	 * bdate = indi birth date record
	 * ddate = indi death date record
	 *
	 * @param string $famid	Gedcom family id
	 * @param string $option Family level indicator
	 * @param string $except	Gedcom childid already processed
	 * @return records added to indifacts array
	 */
	function add_children_facts($famid, $option="", $except="") {
		global $SHOW_RELATIVES_EVENTS;
		if (!$SHOW_RELATIVES_EVENTS) return;
		if (empty($this->brec)) $this->_parseBirthDeath();
		$famrec = find_family_record($famid);
		$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch, PREG_SET_ORDER);
		for($i=0; $i<$num; $i++) {
			$spid = $smatch[$i][1];
			if ($spid!=$except) {
				$childrec = find_person_record($spid);
				$srec = get_sub_record(1, "1 SEX", $childrec);
				$sex = trim(substr($srec, 6));
				// children
				$rela="child";
				if ($sex=="F") $rela="daughter";
				if ($sex=="M") $rela="son";
				// grandchildren
				if ($option=="grand") {
					$rela="grandchild";
					if ($sex=="F") $rela="granddaughter";
					if ($sex=="M") $rela="grandson";
				}
				// stepsiblings
				if ($option=="step") {
					$rela="halfsibling";
					if ($sex=="F") $rela="halfsister";
					if ($sex=="M") $rela="halfbrother";
				}
				// siblings
				if ($option=="1") {
					$rela="sibling";
					if ($sex=="F") $rela="sister";
					if ($sex=="M") $rela="brother";
				}
				// uncles/aunts
				if ($option=="2" or $option=="3") {
					$rela="uncle/aunt";
					if ($sex=="F") $rela="aunt";
					if ($sex=="M") $rela="uncle";
				}
				// firstcousins
				if ($option=="first") {
					$rela="firstcousin";
					if ($sex=="F") $rela="femalecousin";
					if ($sex=="M") $rela="malecousin";
				}
				// add child birth
				$fact = "_BIRT_CHIL";
				if ($option=="grand") $fact = "_BIRT_GCHI";
				if ($option=="step") $fact = "_BIRT_HSIB";
				if ($option=="first") $fact = "_BIRT_COUS";
				if ($option=="1") $fact = "_BIRT_SIBL";
				if ($option=="2") $fact = "_BIRT_FSIB";
				if ($option=="3") $fact = "_BIRT_MSIB";
				if (strstr($SHOW_RELATIVES_EVENTS, $fact)) {
					$srec = get_sub_record(1, "1 BIRT", $childrec);
					$sdate = get_sub_record(2, "2 DATE", $srec);
					if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
						$factrec = "1 ".$fact;
						$factrec .= "\n".trim($sdate);
						if (!showFact("BIRT", $spid)) $factrec .= "\n2 RESN privacy";
						$factrec .= "\n2 ASSO @".$spid."@";
						$factrec .= "\n3 RELA ".$rela;
						$this->indifacts[]=array(0, $factrec);
					}
				}
				// add child death
				$fact = "_DEAT_CHIL";
				if ($option=="grand") $fact = "_DEAT_GCHI";
				if ($option=="step") $fact = "_DEAT_HSIB";
				if ($option=="first") $fact = "_DEAT_COUS";
				if ($option=="1") $fact = "_DEAT_SIBL";
				if ($option=="2") $fact = "_DEAT_FSIB";
				if ($option=="3") $fact = "_DEAT_MSIB";
				if (strstr($SHOW_RELATIVES_EVENTS, $fact)) {
					$srec = get_sub_record(1, "1 DEAT", $childrec);
					$sdate = get_sub_record(2, "2 DATE", $srec);
					if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
						$factrec = "1 ".$fact;
						$factrec .= "\n".trim($sdate);
						if (!showFact("DEAT", $spid)) $factrec .= "\n2 RESN privacy";
						$factrec .= "\n2 ASSO @".$spid."@";
						$factrec .= "\n3 RELA ".$rela;
						$this->indifacts[]=array(0, $factrec);
					}
				}
				// add child marriage
				$fact = "_MARR_CHIL";
				if ($option=="grand") $fact = "_MARR_GCHI";
				if ($option=="step") $fact = "_MARR_HSIB";
				if ($option=="first") $fact = "_MARR_COUS";
				if ($option=="1") $fact = "_MARR_SIBL";
				if ($option=="2") $fact = "_MARR_FSIB";
				if ($option=="3") $fact = "_MARR_MSIB";
				if (strstr($SHOW_RELATIVES_EVENTS, $fact)) {
					$sfamids = find_sfamily_ids($spid);
					foreach ($sfamids as $indexval=>$sfamid) {
						$childrec = find_family_record($sfamid);
						$srec = get_sub_record(1, "1 MARR", $childrec);
						$sdate = get_sub_record(2, "2 DATE", $srec);
						if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
							$factrec = "1 ".$fact;
							$factrec .= "\n".trim($sdate);
							if (!showFact("MARR", $sfamid)) $factrec .= "\n2 RESN privacy";
							$factrec .= "\n2 ASSO @".$spid."@";
							$factrec .= "\n3 RELA ".$rela;
							$parents = find_parents($sfamid);
							$spouse = $parents["HUSB"];
							if ($spouse==$spid) $spouse = $parents["WIFE"];
							if ($rela=="son") $rela2="daughter-in-law";
							else if ($rela=="daughter") $rela2="son-in-law";
							else if ($rela=="brother" or $rela=="halfbrother") $rela2="sister-in-law";
							else if ($rela=="sister" or $rela=="halfsister") $rela2="brother-in-law";
							else if ($rela=="uncle") $rela2="aunt";
							else if ($rela=="aunt") $rela2="uncle";
							else if (strstr($rela, "cousin")) $rela2="cousin-in-law";
							else $rela2="spouse";
							$factrec .= "\n2 ASSO @".$spouse."@";
							$factrec .= "\n3 RELA ".$rela2;
							$factrec .= "\n2 ASSO @".$sfamid."@";
							$factrec .= "\n3 RELA family";
							$arec = get_sub_record(2, "2 ASSO @".$spid."@", $srec);
							if ($arec) $factrec .= "\n".$arec;
							$this->indifacts[]=array(0, $factrec);
						}
					}
				}
				// add grand-children
				if ($option=="") {
					$famids = find_sfamily_ids($spid);
					foreach ($famids as $indexval=>$famid) {
						$this->add_children_facts($famid, "grand");
					}
				}
				// first cousins
				if ($option=="2" or $option=="3") {
					$famids = find_sfamily_ids($spid);
					foreach ($famids as $indexval=>$famid) {
						$this->add_children_facts($famid, "first");
					}
				}
			}
		}
	}
	/**
	 * add spouse events to individual facts array
	 *
	 * bdate = indi birth date record
	 * ddate = indi death date record
	 *
	 * @param string $spouse	Gedcom id
	 * @param string $famrec	family Gedcom record
	 * @return records added to indifacts array
	 */
	function add_spouse_facts($spouse, $famrec="") {
		global $SHOW_RELATIVES_EVENTS;
		// do not show if divorced
		if (strstr($famrec, "1 DIV")) return;
		if (empty($this->brec)) $this->_parseBirthDeath();
		// add spouse death
		$fact = "_DEAT_SPOU";
		if ($spouse and strstr($SHOW_RELATIVES_EVENTS, $fact)) {
			$srec=get_sub_record(1, "1 DEAT", find_person_record($spouse));
			$sdate=get_sub_record(2, "2 DATE", $srec);
			if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
				$factrec = "1 ".$fact;
				$factrec .= "\n".trim($sdate);
				if (!showFact("DEAT", $spouse)) $factrec .= "\n2 RESN privacy";
				$factrec .= "\n2 ASSO @".$spouse."@";
				$factrec .= "\n3 RELA spouse";
				$this->indifacts[]=array(0, $factrec);
			}
		}
	}
	/**
	 * add step-siblings events to individual facts array
	 *
	 * @param string $spouse	Father or mother Gedcom id
	 * @param string $except	Gedcom famid already processed
	 * @return records added to indifacts array
	 */
	function add_stepsiblings_facts($spouse, $except="") {
		$famids = find_sfamily_ids($spouse);
		foreach ($famids as $indexval=>$famid) {
			// process children from all step families
			if ($famid!=$except) $this->add_children_facts($famid, "step");
		}
	}
	/**
	 * add historical events to individual facts array
	 *
	 * @return records added to indifacts array
	 *
	 * Historical facts are imported from optional language file : histo.xx.php
	 * where xx is language code
	 * This file should contain records similar to :
	 *
	 *	$histo[]="1 EVEN\n2 TYPE History\n2 DATE 11 NOV 1918\n2 NOTE WW1 Armistice";
	 *	$histo[]="1 EVEN\n2 TYPE History\n2 DATE 8 MAY 1945\n2 NOTE WW2 Armistice";
	 * etc...
	 *
	 */
	function add_historical_facts() {
		global $PGV_BASE_DIRECTORY, $LANGUAGE, $lang_short_cut;
		global $SHOW_RELATIVES_EVENTS;
		if (!$SHOW_RELATIVES_EVENTS) return;
		if (empty($this->bdate)) return;
		if (empty($this->brec)) $this->_parseBirthDeath();
		$histo=array();
		if (file_exists($PGV_BASE_DIRECTORY."languages/histo.".$lang_short_cut[$LANGUAGE].".php")) {
			@include($PGV_BASE_DIRECTORY."languages/histo.".$lang_short_cut[$LANGUAGE].".php");
		}
		foreach ($histo as $indexval=>$hrec) {
			$sdate = get_sub_record(2, "2 DATE", $hrec);
			if (compare_facts($this->bdate, $sdate)<0 and compare_facts($sdate, $this->ddate)<0) {
				$this->indifacts[]=array(0, $hrec);
			}
		}
	}
	/**
	 * add events where pid is an ASSOciate
	 *
	 * @param string $pid	Gedcom id
	 * @return records added to indifacts array
	 *
	 */
	function add_asso_facts($pid) {
		global $factarray, $pgv_lang;
		global $assolist, $GEDCOM, $GEDCOMS;
		if (!function_exists("get_asso_list")) return;
		get_asso_list();
		$apid = $pid."[".$GEDCOMS[$GEDCOM]["id"]."]";
		// associates exist ?
		if (isset($assolist[$apid])) {
			// if so, print all indi's where the indi is associated to
			foreach($assolist[$apid] as $indexval => $asso) {
				$ct = preg_match("/0 @(.*)@ (.*)/", $asso["gedcom"], $match);
				$rid = $match[1];
				$typ = $match[2];
				// search for matching fact
				for ($i=1; ; $i++) {
					$srec = get_sub_record(1, "1 ", $asso["gedcom"], $i);
					if (empty($srec)) break;
					$arec = get_sub_record(2, "2 ASSO @".$pid."@", $srec);
					if ($arec) {
						$fact = trim(substr($srec, 2, 5));
						$label = strip_tags($factarray[$fact]);
						$sdate = get_sub_record(2, "2 DATE", $srec);
						// relationship ?
						$rrec = get_sub_record(3, "3 RELA", $arec);
						$rela = trim(substr($rrec, 7));
						if (empty($rela)) $rela = "ASSO";
						if (isset($pgv_lang[$rela])) $rela = $pgv_lang[$rela];
						else if (isset($factarray[$rela])) $rela = $factarray[$rela];
						// add an event record
						$factrec = "1 EVEN\n2 TYPE ".$label."<br/>[".$rela."]";
						$factrec .= "\n".trim($sdate);
						if (!showFact($fact, $rid)) $factrec .= "\n2 RESN privacy";
						$famrec = find_family_record($rid);
						if ($famrec) {
							$parents = find_parents_in_record($famrec);
							if ($parents["HUSB"]) $factrec .= "\n2 ASSO @".$parents["HUSB"]."@"; //\n3 RELA ".$factarray[$fact];
							if ($parents["WIFE"]) $factrec .= "\n2 ASSO @".$parents["WIFE"]."@"; //\n3 RELA ".$factarray[$fact];
						}
						else $factrec .= "\n2 ASSO @".$rid."@\n3 RELA ".$label;
						//$factrec .= "\n3 NOTE ".$rela;
						$factrec .= "\n2 ASSO @".$pid."@\n3 RELA ".$rela;

						$this->indifacts[] = array(0, $factrec);
					}
				}
			}
		}
	}
	/**
	 * Merge the facts from another Person object into this object
	 * for generating a diff view
	 * @param Person $diff	the person to compare facts with
	 */
	function diffMerge(&$diff) {
		if (is_null($diff)) return;
		$this->parseFacts();
		$diff->parseFacts();
		//-- loop through new facts and add them to the list if they are any changes
		//-- compare new and old facts of the Personal Fact and Details tab 1
		for($i=0; $i<count($this->indifacts); $i++) {
			$found=false;
			foreach($diff->indifacts as $indexval => $newfact) {
				if (trim($newfact[1])==trim($this->indifacts[$i][1])) {
					$this->indifacts[$i][0] = $newfact[0];				//-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->indifacts[$i][1].="\r\nPGV_OLD\r\n";
			}
		}
		foreach($diff->indifacts as $indexval => $newfact) {
			$found=false;
			foreach($this->indifacts as $indexval => $fact) {
				if (trim($fact[1])==trim($newfact[1])) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact[1].="\r\nPGV_NEW\r\n";
				$this->indifacts[]=$newfact;
			}
		}
		//-- compare new and old facts of the Notes Sources and Media tab 2
		for($i=0; $i<count($this->otherfacts); $i++) {
			$found=false;
			foreach($diff->otherfacts as $indexval => $newfact) {
				if (trim($newfact[1])==trim($this->otherfacts[$i][1])) {
					$this->otherfacts[$i][0] = $newfact[0];				  //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->otherfacts[$i][1].="\r\nPGV_OLD\r\n";
			}
		}
		foreach($diff->otherfacts as $indexval => $newfact) {
			$found=false;
			foreach($this->otherfacts as $indexval => $fact) {
				if (trim($fact[1])==trim($newfact[1])) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact[1].="\r\nPGV_NEW\r\n";
				$this->otherfacts[]=$newfact;
			}
		}
		//-- compare new and old media facts
		for($i=0; $i<count($this->mediafacts); $i++) {
			$found=false;
			foreach($diff->mediafacts as $indexval => $newfact) {
				if (trim($newfact[1])==trim($this->mediafacts[$i][1])) {
					$this->mediafacts[$i][0] = $newfact[0];				  //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->mediafacts[$i][1].="\r\nPGV_OLD\r\n";
			}
		}
		foreach($diff->mediafacts as $indexval => $newfact) {
			$found=false;
			foreach($this->mediafacts as $indexval => $fact) {
				if (trim($fact[1])==trim($newfact[1])) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact[1].="\r\nPGV_NEW\r\n";
				$this->mediafacts[]=$newfact;
			}
		}
		//-- compare new and old facts of the Global facts
		for($i=0; $i<count($this->globalfacts); $i++) {
			$found=false;
			foreach($diff->globalfacts as $indexval => $newfact) {
				if (trim($newfact[1])==trim($this->globalfacts[$i][1])) {
					$this->globalfacts[$i][0] = $newfact[0]; 			   //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->globalfacts[$i][1].="\r\nPGV_OLD\r\n";
			}
		}
		foreach($diff->globalfacts as $indexval => $newfact) {
			$found=false;
			foreach($this->globalfacts as $indexval => $fact) {
				if (trim($fact[1])==trim($newfact[1])) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact[1].="\r\nPGV_NEW\r\n";
				$this->globalfacts[]=$newfact;
			}
		}
		$newfamids = $diff->getChildFamilyIds();
		if (is_null($this->famc)) $this->getChildFamilyIds();
		foreach($newfamids as $key=>$id) {
			if (!in_array($id, $this->famc)) $this->famc[]=$id;
		}

		$newfamids = $diff->getSpouseFamilyIds();
		if (is_null($this->fams)) $this->getSpouseFamilyIds();
		foreach($newfamids as $key=>$id) {
			if (!in_array($id, $this->fams)) $this->fams[]=$id;
		}
	}

	/**
	 * get the URL to link to this person
	 * @string a url that can be used to link to this person
	 */
	function getLinkUrl() {
		global $GEDCOM;

		$url = "individual.php?pid=".$this->getXref()."&amp;ged=".$GEDCOM;
		if ($this->isRemote()) {
			$parts = preg_split("/:/", $this->rfn);
			if (count($parts)==2) {
				$servid = $parts[0];
				$aliaid = $parts[1];
				if (!empty($servid)&&!empty($aliaid)) {
					$serviceClient = ServiceClient::getInstance($servid);
					if (!empty($serviceClient)) {
						$surl = $serviceClient->getURL();
						$url = "individual.php?pid=".$aliaid;
						if ($serviceClient->getType()=="remote") {
							if (!empty($surl)) $url = dirname($surl)."/".$url;
						}
						else {
							$url = $surl.$url;
						}
						$gedcom = $serviceClient->getGedfile();
						if (!empty($gedcom)) $url.="&amp;ged=".$gedcom;
					}
				}
			}
		}
		return $url;
	}
}
?>
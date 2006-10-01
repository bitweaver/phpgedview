<?php

/**
 * Controller for the Search Page
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
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id$
 */

require_once ("config.php");
require_once ("includes/controllers/basecontrol.php");

/**
 * Main controller class for the search page.
 */
class SearchControllerRoot extends BaseController {
	var $isPostBack = false;
	var $topsearch;
	var $action = "general";
	var $srfams;
	var $srindi;
	var $srsour;
	var $resultsPageNum = 0;
	var $resultsPerPage = 50;
	var $totalResults = -1;
	var $totalGeneralResults = -1;
	var $indiResultsPrinted = -1;
	var $famResultsPrinted = -1;
	var $srcResultsPrinted = -1;
	var $multiResultsPerPage = -1;
	var $multiTotalResults = -1;
	var $view = "";
	var $query;
	var $myquery = "";
	var $soundex = "Russell";
	var $subaction = "";
	var $nameprt = "";
	var $tagfilter = "on";
	var $showasso = "off";
	var $multiquery="";
	var $mymultiquery;
	var $name="";
	var $myname;
	var $birthdate="";
	var $mybirthdate;
	var $birthplace="";
	var $mybirthplace;
	var $deathdate="";
	var $mydeathdate;
	var $deathplace="";
	var $mydeathplace;
	var $gender="";
	var $mygender;
	var $firstname="";
	var $myfirstname;
	var $lastname="";
	var $mylastname;
	var $place="";
	var $myplace;
	var $year="";
	var $myyear;
	var $sgeds = array ();
	var $Sites = array ();
	var $indi_total = array ();
	var $indi_hide = array ();
	var $indi_private = array ();
	var $fam_total = array ();
	var $fam_hide = array ();
	var $fam_private = array ();
	var $repo_total = array ();
	var $repo_hide = array ();
	var $source_total = array ();
	var $source_hide = array ();
	var $myindilist = array ();
	var $mysourcelist = array ();
	var $myfamlist = array ();
	var $myfamlist2 = array ();
	var $multisiteResults = array ();
	var $printname = array ();
	var $printfamname = array ();
	var $inputFieldNames = array ();

	/**
	 * constructor
	 */
	function SearchControllerRoot() {
		parent :: BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
		global $pgv_lang, $ALLOW_CHANGE_GEDCOM, $GEDCOM, $GEDCOMS;

		if (!empty ($_REQUEST["topsearch"])) {
			$this->topsearch = true;
			$this->isPostBack = true;
			$this->srfams = 'yes';
			$this->srindi = 'yes';
			$this->srsour = 'yes';
			$this->srrepo = 'yes';
		}

		// Get the query and remove slashes
		if (isset ($_REQUEST["query"])) {
			// Reset the "Search" text from the page header
			if ($_REQUEST["query"] == $pgv_lang["search"]) {
				unset ($this->query);
			} else {
				$this->query = stripslashes($_REQUEST["query"]);
				$this->myquery = $this->query;
			}
		}

		// Aquire all the variables values from the $_REQUEST
		$varNames = array ("isPostBack", "action", "topsearch", "srfams", "srindi", "srsour", "view", "soundex", "subaction", "nameprt", "tagfilter", "showasso", "resultsPageNum", "resultsPerPage", "totalResults", "totalGeneralResults", "indiResultsPrinted", "famResultsPrinted", "multiTotalResults", "srcResultsPrinted", "multiResultsPerPage", "indi_total", "indi_hide", "indi_private", "fam_total", "fam_hide", "fam_private", "repo_total", "repo_hide", "source_total", "source_hide", "mysourcelist", "myfamlist", "myfamlist2");
		$this->setRequestValues($varNames);

		if ($this->action == "reset") {
			$this->indi_total = array ();
			$this->indi_private = array ();
			$this->indi_hide = array ();
			$this->fam_total = array ();
			$this->fam_private = array ();
			$this->fam_hide = array ();
			$this->source_total = array ();
			$this->source_hide = array ();
			$this->repo_total = array ();
			$this->repo_hide = array ();
		}

		if (!$this->isPostBack) {
			// Enable the default gedcom for search
			$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $GEDCOM);
			$_REQUEST["$str"] = $str;
		}

		// Retrieve the gedcoms to search in
		if (($ALLOW_CHANGE_GEDCOM) && (count($GEDCOMS) > 1)) {
			foreach ($GEDCOMS as $key => $gedarray) {
				$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $key);
				if (isset ($_REQUEST["$str"]) || isset ($this->topsearch)) {
					$this->sgeds[] = $key;
					$_REQUEST["$str"] = 'yes';
				}
			}
		} else
			$this->sgeds[] = $GEDCOM;
			
		// Retrieve the sites that can be searched
		$this->Sites = get_server_list();

		// vars use for soundex serach
		if (!empty ($_REQUEST["firstname"])) {
			$this->firstname = $_REQUEST["firstname"];
			$this->myfirstname = $this->firstname;
		} else {
			unset ($this->firstname);
			$this->myfirstname = "";
		}
		if (!empty ($_REQUEST["lastname"])) {
			$this->lastname = $_REQUEST["lastname"];
			$this->mylastname = $this->lastname;
		} else {
			unset ($this->lastname);
			$this->mylastname = "";
		}
		if (!empty ($_REQUEST["place"])) {
			$this->place = $_REQUEST["place"];
			$this->myplace = $this->place;
		} else {
			unset ($this->place);
			$this->myplace = "";
		}
		if (!empty ($_REQUEST["year"])) {
			$this->year = $_REQUEST["year"];
			$this->myyear = $this->year;
		} else {
			unset ($this->year);
			$this->myyear = "";
		}

		// vars use for multisite serach
		if (!empty ($_REQUEST["multiquery"])) {
			$this->multiquery = $_REQUEST["multiquery"];
			$this->mymultiquery = $this->multiquery;
		} else {
			unset ($this->multiquery);
			$this->mymultiquery = "";
		}
		if (!empty ($_REQUEST["name"])) {
			$this->name = $_REQUEST["name"];
			$this->myname = $this->name;
		} else {
			unset ($this->name);
			$this->myname = "";
		}
		if (!empty ($_REQUEST["birthdate"])) {
			$this->birthdate = $_REQUEST["birthdate"];
			$this->mybirthdate = $this->birthdate;
		} else {
			unset ($this->birthdate);
			$this->mybirthdate = "";
		}
		if (!empty ($_REQUEST["birthplace"])) {
			$this->birthplace = $_REQUEST["birthplace"];
			$this->mybirthplace = $this->birthplace;
		} else {
			unset ($this->birthplace);
			$this->mybirthplace = "";
		}
		if (!empty ($_REQUEST["deathdate"])) {
			$this->deathdate = $_REQUEST["deathdate"];
			$this->mydeathdate = $this->deathdate;
		} else {
			unset ($this->deathdate);
			$this->mydeathdate = "";
		}
		if (!empty ($_REQUEST["deathplace"])) {
			$this->deathplace = $_REQUEST["deathplace"];
			$this->mydeathplace = $this->deathplace;
		} else {
			unset ($this->deathplace);
			$this->mydeathplace = "";
		}
		if (!empty ($_REQUEST["gender"])) {
			$this->gender = $_REQUEST["gender"];
			$this->mygender = $this->gender;
		} else {
			unset ($this->gender);
			$this->mygender = "";
		}

		// Print out all of $this's variables
		//		$this->printVars($varNames);
		//		print "sgeds: ";
		//		print_r($this->sgeds);
		//		print "<br/>";
		//		print "gedNames: ";
		//		print_r($this->gedNames);
		
		$this->inputFieldNames[] = "action"; 
		$this->inputFieldNames[] = "isPostBack"; 
		$this->inputFieldNames[] = "resultsPerPage";
		$this->inputFieldNames[] = "query";
		$this->inputFieldNames[] = "srindi";
		$this->inputFieldNames[] = "srfams";
		$this->inputFieldNames[] = "srsour";
		$this->inputFieldNames[] = "showasso";
		$this->inputFieldNames[] = "firstname";
		$this->inputFieldNames[] = "lastname";
		$this->inputFieldNames[] = "place";
		$this->inputFieldNames[] = "year";
		$this->inputFieldNames[] = "soundex";
		$this->inputFieldNames[] = "nameprt";
		$this->inputFieldNames[] = "subaction";
		$this->inputFieldNames[] = "multiquery";
		$this->inputFieldNames[] = "name";
		$this->inputFieldNames[] = "birthdate";
		$this->inputFieldNames[] = "birthplace";
		$this->inputFieldNames[] = "deathdate";
		$this->inputFieldNames[] = "deathplace";
		$this->inputFieldNames[] = "gender";
		$this->inputFieldNames[] = "tagfilter";
	
		// Get the search results based on the action
		if (isset ($this->topsearch)) $this->TopSearch();
		// If we want to show associated persons, build the list
		if ($this->showasso == "on") get_asso_list();
		if ($this->action == "general") $this->GeneralSearch();
		else if ($this->action == "soundex") $this->SoundexSearch();
		else if ($this->action == "multisite") $this->MultiSiteSearch();
	}

	/**
	 * setRequestValues - Checks if the variable names ($varNames) are in
	 * 					  the $_REQUEST and if so assigns their values to
	 * 					  $this based on the variable name ($this->$varName).
	 *
	 * @param array $varNames - Array of variable names(strings).
	 */
	function setRequestValues($varNames) {
		foreach ($varNames as $key => $varName) {
			if (isset ($_REQUEST[$varName]))
				$this-> $varName = $_REQUEST[$varName];
		}
	}

	/**
	 * setRequestValues - Prints out all of the variable names and their
	 * 					  values based on the variable name ($this->$varName).
	 *
	 * @param array $varNames - Array of variable names(strings).
	 */
	function printVars($varNames) {
		foreach ($varNames as $key => $varName) {
			print $varName.": ".$this-> $varName."<br/>";
		}
	}

	/**
	 * Handles searches entered in the top search box in the themes and
	 * prepares the search to do a general search on indi's, fams, and sources.
	 */
	function TopSearch() {
		global $SHOW_SOURCES, $GEDCOM;
		// first set some required variables. Search only in current gedcom, only in indi's.
		$this->srindi = "yes";

		// Enable the default gedcom for search
		$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $GEDCOM);
		$_REQUEST["$str"] = "yes";

		// Then see if an ID is typed in. If so, we might want to jump there.
		if (isset ($this->query)) {

			// see if it's an indi ID. If it's found and privacy allows it, JUMP!!!!
			if (find_person_record($this->query)) {
				if (showLivingNameByID($this->query) || displayDetailsByID($this->query)) {
					header("Location: individual.php?pid=".$this->query."&ged=".$GEDCOM);
					exit;
				}
			}
			// see if it's a family ID. If it's found and privacy allows it, JUMP!!!!
			if (find_family_record($this->query)) {
				//-- check if we can display both parents
				if (displayDetailsByID($this->query, "FAM") == true) {
					$parents = find_parents($this->query);
					if (showLivingNameByID($parents["HUSB"]) && showLivingNameByID($parents["WIFE"])) {
						header("Location: family.php?famid=".$this->query."&ged=".$GEDCOM);
						exit;
					}
				}
			}
			// see if it's an source ID. If it's found and privacy allows it, JUMP!!!!
			if ($SHOW_SOURCES >= getUserAccessLevel(getUserName())) {
				if (find_source_record($this->query)) {
					header("Location: source.php?sid=".$this->query."&ged=".$GEDCOM);
					exit;
				}
			}
			// see if it's a repository ID. If it's found and privacy allows it, JUMP!!!!
			if ($SHOW_SOURCES >= getUserAccessLevel(getUserName())) {
				if (find_repo_record($this->query)) {
					header("Location: repo.php?rid=".$this->query."&ged=".$GEDCOM);
					exit;
				}
			}
		}
	}

	/**
	 * 	Gathers results for a general search
	 */
	function GeneralSearch() {
		global $REGEXP_DB, $GEDCOM, $GEDCOMS;
		$oldged = $GEDCOM;
		//-- perform the search
		if (isset ($this->query)) {
			// -- array of names to be used for results. Must be here and empty.
			$this->myindilist = array ();
			$this->mysourcelist = array ();
			$this->myfamlist = array ();
			$this->myfamlist2 = array ();

			// Now see if there is a query left after the cleanup
			if (trim($this->query) != "") {

				// Write a log entry
				$logstring = "Type: General<br />Query: ".$this->query;
				AddToSearchlog($logstring, $this->sgeds);

				// Cleanup the querystring so it can be used in a database query
				// Note: when more than one word is entered, this will return results where one word
				// is in one subrecord, another in another subrecord. Theze results are filtered later.
				if (strlen($this->query) == 1)
					$this->query = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $this->query);
				if ($REGEXP_DB)
					$this->query = preg_replace(array ("/\(/", "/\)/", "/\//", "/\]/", "/\[/", "/\s+/"), array ('\(', '\)', '\/', '\]', '\[', '.*'), $this->query);
				else {
					$this->query = "%".preg_replace("/\s+/", "%", $this->query)."%";
				}
				// Search the indi's
				if ((isset ($this->srindi)) && (count($this->sgeds) > 0)) {
					$this->myindilist = search_indis($this->query, $this->sgeds);
				}

				// Search the fams
				if ((isset ($this->srfams)) && (count($this->sgeds) > 0)) {
					// Get the indilist, to check name hits. Store the ID's/names found in
					// the search array, so the fam records can be retrieved.
					// This way we include hits on family names.
					// If indi's are not searched yet, we have to search them first
					if (!isset ($this->srindi))
						$this->myindilist = search_indis($this->query, $this->sgeds);
					$famquery = array ();
					$cntgeds = count($this->sgeds);
					if ($cntgeds==1) $ged = $GEDCOMS[$this->sgeds[0]]["id"];
					foreach ($this->myindilist as $key1 => $myindi) {
						foreach ($myindi["names"] as $key2 => $name) {
							if ((preg_match("/".$this->query."/i", $name[0]) > 0)) {
								if ($cntgeds > 1) {
									$ged = splitkey($key1, "ged");
									$key1 = splitkey($key1, "id");
								}
								$famquery[] = array ($key1, $ged);
								break;
							}
						}
					}
					// Get the famrecs with hits on names from the family table
					if (!empty ($famquery))
						$this->myfamlist = search_fams_names($famquery, "OR", true, $cntgeds);
					// Get the famrecs with hits in the gedcom record from the family table
					if (!empty ($this->query))
						$this->myfamlist2 = search_fams($this->query, $this->sgeds, "OR", true);
					$this->myfamlist = pgv_array_merge($this->myfamlist, $this->myfamlist2);
				}

				// Search the sources
				if ((isset ($this->srsour)) && (count($this->sgeds) > 0)) {
					if (!empty ($this->query))
						$this->mysourcelist = search_sources($this->query, $this->sgeds);
				}

				//-- if only 1 item is returned, automatically forward to that item
				// Check for privacy first. If ID cannot be displayed, continue to the search page.
				if ((count($this->myindilist) == 1) && (count($this->myfamlist) == 0) && (count($this->mysourcelist) == 0)) {
					foreach ($this->myindilist as $key => $indi) {
						if (count($this->sgeds) > 1) {
							$ged = splitkey($key, "ged");
							$pid = splitkey($key, "id");
							if ($GEDCOM != $ged) {
								$oldged = $GEDCOM;
								$GEDCOM = $ged;
								include (get_privacy_file());
							}
						} else {
							$pid = $key;
							$key = $key."[".get_gedcom_from_id($indi["gedfile"])."]";
						}
						if (!isset ($assolist[$key])) {
							if (showLivingNameByID($pid) || displayDetailsByID($pid)) {
								header("Location: individual.php?pid=".$pid."&ged=".get_gedcom_from_id($indi["gedfile"]));
								exit;
							}
						}
						if ((count($this->sgeds > 1)) && (isset ($oldged))) {
							$GEDCOM = $oldged;
							include (get_privacy_file());
						}
					}
				}
				if ((count($this->myindilist) == 0) && (count($this->myfamlist) == 1) && (count($this->mysourcelist) == 0)) {
					foreach ($this->myfamlist as $famid => $fam) {
						if (count($this->sgeds) > 1) {
							$ged = splitkey($famid, "ged");
							$famid = splitkey($famid, "id");
							if ($GEDCOM != $ged) {
								$oldged = $GEDCOM;
								$GEDCOM = $ged;
								include (get_privacy_file());
							}
						}
						if (displayDetailsByID($famid, "FAM") == true) {
							$parents = find_parents($famid);
							if (showLivingNameByID($parents["HUSB"]) && showLivingNameByID($parents["WIFE"])) {
								header("Location: family.php?famid=".$famid."&ged=".$GEDCOM);
								exit;
							}
						}
						if (count($this->sgeds > 1)) {
							$GEDCOM = $oldged;
							include (get_privacy_file());
						}
					}
				}
				if ((count($this->myindilist) == 0) && (count($this->myfamlist) == 0) && (count($this->mysourcelist) == 1)) {
					foreach ($this->mysourcelist as $sid => $source) {
						if (count($this->sgeds) > 1) {
							$ged = splitkey($sid, "ged");
							$sid = splitkey($sid, "id");
							if ($GEDCOM != $ged) {
								$oldged = $GEDCOM;
								$GEDCOM = $ged;
								include (get_privacy_file());
							}
						}
						if (displayDetailsByID($sid, "SOUR")) {
							header("Location: source.php?sid=".$sid."&ged=".get_gedcom_from_id($source["gedfile"]));
							exit;
						}
						if (count($this->sgeds > 1)) {
							$GEDCOM = $oldged;
							include (get_privacy_file());
						}
					}
				}
			}
		}
		$GEDCOM = $oldged;
	}

	/**
	 * 	Gathers results for a soundex search
	 */
	function SoundexSearch() {
		global $REGEXP_DB, $GEDCOM;
		if (((!empty ($this->lastname)) || (!empty ($this->firstname)) || (!empty ($this->place))) && (count($this->sgeds) > 0)) {
			$logstring = "Type: Soundex<br />";
			if (!empty ($this->lastname))
				$logstring .= "Last name: ".$this->lastname."<br />";
			if (!empty ($this->firstname))
				$logstring .= "First name: ".$this->firstname."<br />";
			if (!empty ($this->place))
				$logstring .= "Place: ".$this->place."<br />";
			if (!empty ($this->year))
				$logstring .= "Year: ".$this->year."<br />";
			AddToSearchlog($logstring, $this->sgeds);

			// Adjust the search criteria
			if (isset ($this->firstname)) {
				if (strlen($this->firstname) == 1)
					$this->firstname = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $this->firstname);
				if ($REGEXP_DB)
					$this->firstname = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $this->firstname);
				else {
					$this->firstname = "%".preg_replace("/\s+/", "%", $this->firstname)."%";
				}
			}
			if (isset ($this->lastname)) {
				// see if there are brackets around letter(groups)
				$bcount = substr_count($this->lastname, "[");
				if (($bcount == substr_count($this->lastname, "]")) && $bcount > 0) {
					$barr = array ();
					$ln = $this->lastname;
					$pos = 0;
					$npos = 0;
					for ($i = 0; $i < $bcount; $i ++) {
						$pos1 = strpos($ln, "[") + 1;
						$pos2 = strpos($ln, "]");
						$barr[$i] = array (substr($ln, $pos1, $pos2 - $pos1), $pos1 + $npos -1, $pos2 - $pos1);
						$npos = $npos + $pos2 -1;
						$ln = substr($ln, $pos2 +1);
					}
				}
				if (strlen($this->lastname) == 1)
					$this->lastname = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $this->lastname);
				if ($REGEXP_DB)
					$this->lastname = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $this->lastname);
				else {
					$this->lastname = "%".preg_replace("/\s+/", "%", $this->lastname)."%";
				}
			}
			if (isset ($this->place)) {
				if (strlen($this->place) == 1)
					$this->place = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $this->place);
				if ($REGEXP_DB)
					$this->place = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $this->place);
				else {
					$this->place = "%".preg_replace("/\s+/", "%", $this->place)."%";
				}
			}
			if (isset ($this->year)) {
				if (strlen($this->year) == 1)
					$this->year = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $this->year);
				if ($REGEXP_DB)
					$this->year = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $this->year);
				else {
					$this->year = "%".preg_replace("/\s+/", "%", $this->year)."%";
				}
			}
			$this->myindilist = array ();
			if (count($this->sgeds) > 0) {

				if ($this->soundex == "DaitchM")
					DMsoundex("", "opencache");

				// Do some preliminary stuff: determine the soundex codes for the search criteria
				if ((!empty ($this->lastname)) && ($this->soundex == "DaitchM"))
					$arr2 = DMsoundex($this->lastname);
				if ((!empty ($this->lastname)) && ($this->soundex == "Russell"))
					$arr2 = soundex($this->lastname);
				if (!empty ($this->firstname)) {
					$firstnames = preg_split("/\s/", trim($this->firstname));
					$farr = array ();
					for ($j = 0; $j < count($firstnames); $j ++) {
						if ($this->soundex == "Russell")
							$farr[$j] = soundex($firstnames[$j]);
						if ($this->soundex == "DaitchM")
							$farr[$j] = DMsoundex($firstnames[$j]);
					}
				}
				if ((!empty ($this->place)) && ($this->soundex == "DaitchM"))
					$parr = DMsoundex($this->place);
				if ((!empty ($this->place)) && ($this->soundex == "Russell"))
					$parr = soundex(trim($this->place));

				// Start the search
				$oldged = $GEDCOM;
				$this->printname = array ();
				$this->printfamname = array ();
				foreach ($this->sgeds as $indexval => $value) {
						$GEDCOM = $value;
						$INDILIST_RETRIEVED = false;
						$indilist = get_indi_list();
						// -- only get the names who match soundex
						foreach ($indilist as $key => $value) {
							$save = false; // if all names must be printed
							foreach ($value["names"] as $indexval => $namearray) {
								$name = check_NN($namearray[0]);
								$savel = false;
								if ($this->nameprt == "hit")
									$save = false; // if only matching names must be printed
								if (!empty ($this->lastname)) {
									$surname = check_NN($namearray[2]);
									$surnames = preg_split("/\s/", trim($surname));
									foreach ($surnames as $skey => $svalue) {
										if ($this->soundex == "Russell") {
											if (soundex($svalue) == $arr2)
												$savel = true;
											if ($savel)
												$save = true;
										}
										if ($this->soundex == "DaitchM") {
											$arr1 = DMsoundex($svalue);
											$y = 0;
											while ($y < count($arr1) && $save == false) {
												$z = 0;
												while ($z < count($arr2) && $save == false) {
													if ($arr1[$y] == $arr2[$z]) {
														$savel = true;
														//													print $key."  Search ln: ".$surname." (".$arr1[$y]."), hit on ".$this->lastname." (".$arr2[$z].")<br />"; //--- debug
													}
													if ($savel)
														$save = true;
													$z ++;
												}
												$y ++;
											}
										}
										if ($save)
											break;
									}
								}
								$savef = false;
								if ((!empty ($this->firstname)) && ($savel == true || empty ($this->lastname))) {
									$lnames = preg_split("/\//", $namearray[0]);
									$fname = $lnames[0];
									$fnames = preg_split("/\s/", trim($fname));
									$i = 0;
									while ($i < count($fnames) && $savef == false) {
										$j = 0;
										while ($j < count($firstnames) && $savef == false) {
											if ($this->soundex == "Russell") {
												if (soundex($fnames[$i]) == $farr[$j]) {
													$savef = true;
												}
											}
											if ($this->soundex == "DaitchM") {
												$arr1 = DMsoundex($fnames[$i]);
												$y = 0;
												while ($y < count($arr1) && $savef == false) {
													$z = 0;
													while ($z < count($farr) && $savef == false) {
														$a = 0;
														while ($a < count($farr[$z]) && $savef == false) {
															if ($arr1[$y] == $farr[$z][$a])
																$savef = true;
															//														if ($savef == true) print $key."  Search fn: ".$fname." (".$arr1[$y]."), hit on ".$this->firstname." (".$farr[$z][$a].")<br />"; //--- debug
															$a ++;
														}
														$z ++;
													}
													$y ++;
												}
											}
											$j ++;
										}
										$i ++;
									}
									if (($savel == true || empty ($this->lastname)) && $savef == true)
										$save = true;
									else
										$save = false;
								}
								if ((!empty ($this->place)) || (!empty ($this->year))) {
									$indirec = find_person_record($key);
									if ((!empty ($this->place)) && ($savel == true || empty ($this->lastname)) && ($savef == true || empty ($this->firstname))) {
										$savep = false;
										$pt = preg_match_all("/\d PLAC (.*)/i", $indirec, $match, PREG_PATTERN_ORDER);
										if ($pt > 0) {
											$places = array ();
											for ($pp = 0; $pp < count($match[1]); $pp ++) {
												$places[$pp] = preg_split("/,\s/", trim($match[1][$pp]));
											}
											$cp = count($places);
											$p = 0;
											while ($p < $cp && $savep == false) {
												$pp = 0;
												while ($pp < count($places[$p]) && $savep == false) {
													if ($this->soundex == "Russell") {
														if (soundex(trim($places[$p][$pp])) == $parr)
															$savep = true;
													}
													if ($this->soundex == "DaitchM") {
														$arr1 = DMsoundex(trim($places[$p][$pp]));
														$y = 0;
														while ($y < count($arr1) && $savep == false) {
															$z = 0;
															while ($z < count($parr) && $savep == false) {
																if ($arr1[$y] == $parr[$z])
																	$savep = true;
																//															if ($savep == true) print $key."  Search pl: ".$places[$p][$pp]." (".$arr1[$y]."), hit on ".$this->place." (".$parr[$z].")<br />"; //--- debug
																$z ++;
															}
															$y ++;
														}
													}
													$pp ++;
												}
												$p ++;
											}
										}
										if (($savel == true || empty ($this->lastname)) && ($savef == true || empty ($this->firstname)) && $savep == true)
											$save = true;
										else
											$save = false;
									}
									if (!empty ($this->year) && $save == true) {
										$yt = preg_match("/\d DATE (.*$this->year.*)/i", $indirec, $match);
										if ($yt == 0)
											$save = false;
									}
								}
								if ($save === true) {
									//								print "Added ".sortable_name_from_name($namearray[0]);
									$this->printname[] = array (sortable_name_from_name($namearray[0]), $key, get_gedcom_from_id($value["gedfile"]), "");
									//								break; // leave out if we want all names from one indi shown
								}
							}
						}
				}
				$GEDCOM = $oldged;
			}
			// check the result on required characters
			if (isset ($barr)) {
				foreach ($this->printname as $pkey => $pname) {
					$print = true;
					foreach ($barr as $key => $checkchar) {
						if (str2upper(substr($pname[0], $checkchar[1], $checkchar[2])) != str2upper($checkchar[0])) {
							$print = false;
							break;
						}
					}
					if ($print == false) {
						unset ($this->printname[$pkey]);
					}
				}
			}
			// Now we have the final list of indi's to be printed.
			// We may add the assos at this point.

			if ($this->showasso == "on") {
				foreach ($this->printname as $key => $pname) {
					$apid = $pname[1]."[".$pname[2]."]";
					// Check if associates exist
					if (isset ($assolist[$apid])) {
						// if so, print all indi's where the indi is associated to
						foreach ($assolist[$apid] as $indexval => $asso) {
							if ($asso["type"] == "indi") {
								$indi_printed[$indexval] = "1";
								// print all names
								foreach ($asso["name"] as $nkey => $assoname) {
									$key = splitkey($indexval, "id");
									$this->printname[] = array (sortable_name_from_name($assoname[0]), $key, get_gedcom_from_id($asso["gedfile"]), $apid);
								}
							} else
								if ($asso["type"] == "fam") {
									$fam_printed[$indexval] = "1";
									// print all names
									foreach ($asso["name"] as $nkey => $assoname) {
										$assosplit = preg_split("/(\s\+\s)/", trim($assoname));
										// Both names have to have the same direction
										if (hasRTLText($assosplit[0]) == hasRTLText($assosplit[1])) {
											$apid2 = splitkey($indexval, "id");
											$this->printfamname[] = array (check_NN($assoname), $apid2, get_gedcom_from_id($asso["gedfile"]), $apid);
										}
									}
								}
						}
						unset ($assolist[$apid]);
					}
				}
			}

			//-- if only 1 item is returned, automatically forward to that item
			if (count($this->printname) == 1) {
				$oldged = $GEDCOM;
				$GEDCOM = $this->printname[0][2];
				include (get_privacy_file());
				if (showLivingNameByID($this->printname[0][1]) || displayDetailsByID($this->printname[0][1])) {
					header("Location: individual.php?pid=".$this->printname[0][1]."&ged=".$this->printname[0][2]);
					exit;
				} else {
					$GEDCOM = $oldged;
					include (get_privacy_file());
				}
			}
			uasort($this->printname, "itemsort");
			reset($this->printname);
		}
	}

	/**
	 *
	 */
	function MultiSiteSearch() {
		global $REGEXP_DB;
		require_once ('includes/serviceclient_class.php');
		//		AddToLog("is_multisite search");

		if (!empty ($this->Sites) && count($this->Sites) > 0) {
			$this->myindilist = array ();
			// This first tests to see if it just a basic site search
			if (!empty ($this->multiquery) && ($this->subaction == "basic")) {
				// Find out if the string is longer then one char if dont perform the search
				if (strlen($this->multiquery) > 1) {
					//					AddToLog('Basic query: '.$this->multiquery);
					$my_query = $this->multiquery;
					// Now see if there is a query left after the cleanup
					if (trim($my_query) != "") {
						// Cleanup the querystring so it can be used in a database query
						if (strlen($my_query) == 1)
							$my_query = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $my_query);
						if ($REGEXP_DB)
							$my_query = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\//", "/\]/", "/\[/"), array (".*", '\(', '\)', '\/', '\]', '\['), $my_query);
						else {
							$my_query = "%".preg_replace("/\s+/", "%", $my_query)."%";
						}
					}
				}

			} else
				if (($this->subaction == "advanced") && (!empty ($this->myname) || !empty ($this->mybirthdate) || !empty ($this->mybirthplace) || !empty ($this->deathdate) || !empty ($this->mydeathplace) || !empty ($this->mygender))) {
					//					AddToLog('Advanced query');
					//Building the query string up
					$my_query = '';
					if (!empty ($this->myname)) {
						$my_query .= "NAME=".$this->myname;
						//						AddToLog('NAME: '.$this->myname);
					}
					if (!empty ($this->mybirthdate)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "BIRTHDATE=".$this->mybirthdate;
						//						AddToLog('BIRTHDATE: '.$this->mybirthdate);
					}
					if (!empty ($this->mybirthplace)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "BIRTHPLACE=".$this->mybirthplace;
						//						AddToLog('BIRTHPLACE: '.$this->mybirthplace);
					}
					if (!empty ($this->deathdate)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "DEATHDATE=".$this->deathdate;
						//						AddToLog('DEATHDATE: '.$this->deathdate);
					}
					if (!empty ($this->mydeathplace)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query."DEATHPLACE=".$this->mydeathplace;
						//						AddToLog('DEATHPLACE: '.$this->mydeathplace);
					}
					if (!empty ($this->mygender)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "GENDER=".$this->mygender;
						//						AddToLog('GENDER: '.$this->mygender);
					}
				}

			if (!empty ($my_query)) {
				//				AddToLog("Query: ".$my_query);
				$this->multisiteResults = array ();
				// loop through the selected site to search
				$i = 0;
				foreach ($this->Sites as $key => $site) {
					$vartemp = "server".$i;
					if (isset ($_REQUEST["$vartemp"])) {
						//						AddToLog('Site: '.$site['name']);
						//print "<br />".$key;
						$serviceClient = ServiceClient :: getInstance($key);
						//print $serviceClient;
						$result = $serviceClient->search($my_query);
						//print_r($result);
						//print "<br/>";
						//if (!isset($result->totalResults)) print_r($result);
						$this->multisiteResults[$key] = $result;
						//					if(count($result->persons)> 0)
						//					{
						//						AddToLog('Total results: '.$result->totalResults);
						//					}
					}
					$i ++;
				}
			}
		}
	}

	function printResults() {
		include_once ("includes/functions_print_lists.php");
		global $GEDCOM, $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $pgv_lang, $global_facts;
		// ---- section to search and display results on a general keyword search
		if ($this->action == "general") {
			if ((isset ($this->query)) && ($this->query != "")) {
				//--- Results in these tags will be ignored when the tagfilter is on

				// Never show results in _UID
				$skiptags = "_UID";

				// If not admin, also hide searches in RESN tags
				if (!userIsAdmin(getUserName())) $skiptags .= ", RESN";

				// Add the optional tags
				if ($this->tagfilter == "on") $skiptags .= ", _PGVU, FILE, FORM, TYPE, CHAN, SUBM, REFN";
				
				$userlevel = GetUserAccessLevel();

				// Keep track of what indis are already printed to keep a reliable counter
				$indi_printed = array ();
				$fam_printed = array ();

				// init various counters
				//		init_list_counters();

				// printqueues for indi's and fams
				$printindiname = array ();
				$printfamname = array ();
				$actualsourcelist = array ();

				$cti = count($this->myindilist);
				if (($cti > 0) && (isset ($this->srindi))) {
					$oldged = $GEDCOM;
					$curged = $GEDCOM;

					// Add the facts in $global_facts that should not show
					$skiptagsged = $skiptags;
					foreach ($global_facts as $gfact => $gvalue) {
						if (isset ($gvalue["show"])) {
							if (($gvalue["show"] < $userlevel))
								$skiptagsged .= ", ".$gfact;
						}
					}

					foreach ($this->myindilist as $key => $value) {
						if (count($this->sgeds) > 1) {
							$GEDCOM = splitkey($key, "ged");
							$key = splitkey($key, "id");
							if ($GEDCOM != $curged) {
								include (get_privacy_file());
								$curged = $GEDCOM;
								// Recalculate the tags to skip
								$skiptagsged = $skiptags;
								foreach ($global_facts as $gfact => $gvalue) {
									if (isset ($gvalue["show"])) {
										if (($gvalue["show"] < $userlevel))
											$skiptagsged .= ", ".$gfact;
									}
								}
							}
						}
						//-- make sure that the data that was searched on is not in a private part of the record
						$hit = false;
						if (!displayDetailsById($key) && showLivingNameById($key)) {
							//-- any record that is not a FAMC, FAMS is private
							$ncnt = count($value["names"]);
							$found = false;
							for ($ci = 1; $ci <= $ncnt; $ci ++) {
								$record = get_sub_record($ci, "1 NAME", $value["gedcom"]);
								if (preg_match("/".$this->query."/i", $record) > 0) {
									$hit = true;
									$found = true;
								}
							}
						} else {
							$found = false;
							// First check if the hit is in the key!
							if ($this->tagfilter == "off") {
								if (strpos(str2upper($key), str2upper($this->query)) !== false)
									$found = true;
							}
							if ($found == false) {
								$recs = get_all_subrecords($value["gedcom"], "", false, false, false);
								// Also levels>1 must be checked for tags. This is done below.
								foreach ($recs as $keysr => $subrec) {
									$recs2 = preg_split("/\r?\n/", $subrec);
									foreach ($recs2 as $keysr2 => $subrec2) {
										// There must be a hit in a subrec. If found, check in which tag
										if (preg_match("/$this->query/i", $subrec2, $result) > 0) {
											$ct = preg_match("/\d\s(\S*)\s*.*/i", $subrec2, $result2);
											if (($ct > 0) && (!empty ($result2[1]))) {
												// if the tag can be displayed, do so
												if (strpos($skiptagsged, $result2[1]) === false) {
													$hit = true;
													$found = true;
												} else {
													if (strpos($skiptags, $result2[1]) !== false) {
														// Hit is hidden because we don't want to know about it
														$hit = false;
														$found = false;
													} else {
														// Hit is hidden because of Fact Privacy settings
														$hit = true;
														$found = false;
													}
												}
											}
										}
										if ($found == true)
											break;
									}
									if ($found == true)
										break;
								}
							}
						}
						if ($found == true) {

							// print all names from the indi found
							foreach ($value["names"] as $indexval => $namearray) {
								$printindiname[] = array (sortable_name_from_name($namearray[0]), $key, get_gedcom_from_id($value["gedfile"]), "");
							}
							$indi_printed[$key."[".$GEDCOM."]"] = "1";

							// If associates must be shown, see if we can display them and add them to the print array
							if (($this->showasso == "on") && (strpos($skiptagsged, "ASSO") === false)) {
								$apid = $key."[".$value["gedfile"]."]";
								// Check if associates exist
								if (isset ($assolist[$apid])) {
									// if so, print all indi's where the indi is associated to
									foreach ($assolist[$apid] as $indexval => $asso) {
										if ($asso["type"] == "indi") {
											$indi_printed[$indexval] = "1";
											// print all names
											foreach ($asso["name"] as $nkey => $assoname) {
												$key = splitkey($indexval, "id");
												$printindiname[] = array (sortable_name_from_name($assoname[0]), $key, get_gedcom_from_id($asso["gedfile"]), $apid);
											}
										} else
											if ($asso["type"] == "fam") {
												$fam_printed[$indexval] = "1";
												// print all names
												foreach ($asso["name"] as $nkey => $assoname) {
													$assosplit = preg_split("/(\s\+\s)/", trim($assoname));
													// Both names have to have the same direction
													if (hasRTLText($assosplit[0]) == hasRTLText($assosplit[1])) {
														$apid2 = splitkey($indexval, "id");
														$printfamname[] = array (check_NN($assoname), $apid2, get_gedcom_from_id($asso["gedfile"]), $apid);
													}
												}
											}
									}
								}
							}
						} else
							if ($hit == true)
								$this->indi_hide[$key."[".get_gedcom_from_id($value["gedfile"])."]"] = 1;
					}
					$GEDCOM = $oldged;
				}
				// Process the fams
				$ctf = count($this->myfamlist);
				if ($ctf > 0 || count($printfamname) > 0) {
					$oldged = $GEDCOM;
					$curged = $GEDCOM;

					// Add the facts in $global_facts that should not show
					$skiptagsged = $skiptags;
					foreach ($global_facts as $gfact => $gvalue) {
						if (isset ($gvalue["show"])) {
							if (($gvalue["show"] < $userlevel))
								$skiptagsged .= ", ".$gfact;
						}
					}

					foreach ($this->myfamlist as $key => $value) {
						if (count($this->sgeds) > 1) {
							$GEDCOM = splitkey($key, "ged");
							$key = splitkey($key, "id");
							if ($GEDCOM != $curged) {
								include (get_privacy_file());
								$curged = $GEDCOM;
								// Recalculate the tags to skip
								$skiptagsged = $skiptags;
								foreach ($global_facts as $gfact => $gvalue) {
									if (isset ($gvalue["show"])) {
										if (($gvalue["show"] < $userlevel))
											$skiptagsged .= ", ".$gfact;
									}
								}
							}
						}

						// lets see where the hit is
						$found = false;
						// If a name is hit, no need to check for tags
						foreach ($value["name"] as $nkey => $famname) {
							if ((preg_match("/".$this->query."/i", $famname)) > 0) {
								$found = true;
								break;
							}
						}
						$hit = false;
						// First check if the hit is in the key!
						if (($this->tagfilter == "off") && ($found == false)) {
							if (strpos(str2upper($key), str2upper($this->query)) !== false) {
								$found = true;
								$hit = true;
							}
						}
						// If no hit in a name or ID, check if there is a hit on a valid tag
						if ($found == false) {
							$recs = get_all_subrecords($value["gedcom"], $skiptagsged, false, false, false);
							// Also levels>1 must be checked for tags. This is done below.
							foreach ($recs as $keysr => $subrec) {
								$recs2 = preg_split("/\r?\n/", $subrec);
								foreach ($recs2 as $keysr2 => $subrec2) {
									// There must be a hit in a subrec. If found, check in which tag
									if (preg_match("/$this->query/i", $subrec2, $result) > 0) {
										$ct = preg_match("/\d.(\S*).*/i", $subrec2, $result2);
										if (($ct > 0) && (!empty ($result2[1]))) {
											// if the tag can be displayed, do so
											if (strpos($skiptagsged, $result2[1]) === false) {
												$hit = true;
												$found = true;
											} else {
												if (strpos($skiptags, $result2[1]) !== false) {
													// Hit is hidden because we don't want to know about it
													$hit = false;
													$found = false;
												} else {
													// Hit is hidden because of Fact Privacy settings
													$hit = true;
													$found = false;
												}
											}
										}
									}
									if ($found == true)
										break;
								}
								if ($found == true)
									break;
							}
						}
						if ($found == true) {
							foreach ($value["name"] as $namekey => $famname) {
								$famsplit = preg_split("/(\s\+\s)/", trim($famname));
								// Both names have to have the same direction
								if (hasRTLText($famsplit[0]) == hasRTLText($famsplit[1])) {
									// do not print if the hit only in the second name. We want it first.
									if (!((preg_match("/".$this->query."/i", $famsplit[0]) == 0) && (preg_match("/".$this->query."/i", $famsplit[1]) > 0))) {
										$printfamname[] = array (check_NN($famname), $key, get_gedcom_from_id($value["gedfile"]), "");
									}
								}
							}
							$fam_printed[$key."[".$GEDCOM."]"] = "1";
						} else
							if ($hit == true)
								$this->fam_hide[$key."[".get_gedcom_from_id($value["gedfile"])."]"] = 1;
					}
					uasort($printfamname, "itemsort");
					$GEDCOM = $oldged;
				}
				$cts = count($this->mysourcelist);
				if ($cts > 0) {
					uasort($this->mysourcelist, "itemsort");
					$oldged = $GEDCOM;
					$curged = $GEDCOM;

					// Add the facts in $global_facts that should not show
					$skiptagsged = $skiptags;
					foreach ($global_facts as $gfact => $gvalue) {
						if (isset ($gvalue["show"])) {
							if (($gvalue["show"] < $userlevel))
								$skiptagsged .= ", ".$gfact;
						}
					}
		
					foreach ($this->mysourcelist as $key => $value) {
						if (count($this->sgeds) > 1) {
							$GEDCOM = splitkey($key, "ged");
							$key = splitkey($key, "id");
							if ($curged != $GEDCOM) {
								include (get_privacy_file());
								$curged = $GEDCOM;
								// Recalculate the tags to skip
								$skiptagsged = $skiptags;
								foreach ($global_facts as $gfact => $gvalue) {
									if (isset ($gvalue["show"])) {
										if (($gvalue["show"] < $userlevel))
											$skiptagsged .= ", ".$gfact;
									}
								}
							}
						}
						$found = false;
						$hit = false;
						// First check if the hit is in the key!
						if ($this->tagfilter == "off") {
							if (strpos(str2upper($key), str2upper($this->query)) !== false) {
								$found = true;
								$hit = true;
							}
						}
						if ($found == false) {
							$recs = get_all_subrecords($value["gedcom"], $skiptagsged, false, false, false);
							// Also levels>1 must be checked for tags. This is done below.
							foreach ($recs as $keysr => $subrec) {
								$recs2 = preg_split("/[\r\n]+/", $subrec);
								foreach ($recs2 as $keysr2 => $subrec2) {
									// There must be a hit in a subrec. If found, check in which tag
									if (preg_match("/$this->query/i", $subrec2, $result) > 0) {
										$ct = preg_match("/\d.(\S*).*/i", $subrec2, $result2);
										if (($ct > 0) && (!empty ($result2[1]))) {
											// if the tag can be displayed, do so
											if (strpos($skiptagsged, $result2[1]) === false) {
												$hit = true;
												$found = true;
											} else {
												if (strpos($skiptags, $result2[1]) !== false) {
													// Hit is hidden because we don't want to know about it
													$hit = false;
													$found = false;
												} else {
													// Hit is hidden because of Fact Privacy settings
													$hit = true;
													$found = false;
												}
											}
										}
									}
									if ($found == true)
										break;
								}
								if ($found == true)
									break;
							}
						}
						if ($found == true)
							$actualsourcelist[$key] = $value;
						else
							if ($hit == true)
								$this->source_hide[$key."[".get_gedcom_from_id($value["gedfile"])."]"] = 1;
					}
					$GEDCOM = $oldged;
				}
				// Start output here, because from the indi's we may have printed some fams which need the column header.
				print "<br />";
				print "\n\t<div class=\"center\">\n";
				$totalIndiResults = count($printindiname);
				$this->totalGeneralResults = $totalIndiResults;
				$totalFamResults = count($printfamname);
				if ($totalFamResults > $this->totalGeneralResults)
					$this->totalGeneralResults = $totalFamResults;
				$totalSrcResults = count($actualsourcelist);
				if ($totalSrcResults > $this->totalGeneralResults)
						$this->totalGeneralResults = $totalSrcResults;
				// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
				if ($this->resultsPerPage >= 1 && $this->totalGeneralResults > $this->resultsPerPage) {
					$this->printPageResultsLinks($this->inputFieldNames, $this->totalGeneralResults, $this->resultsPerPage);
				}
				print "<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>";
				if ((count($printindiname) > 0) && (isset ($this->srindi)))
					print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
				if ((count($this->myfamlist) > 0) || (count($printfamname) > 0))
					print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["families"]."</td>";
				if (count($this->mysourcelist) > 0)
					print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["sources"]."</td>";
				print "</tr>\n\t\t<tr>";
				$oldged = $GEDCOM;
				$curged = $GEDCOM;
				// Print the indis
				if (count($printindiname) > 0) {
					uasort($printindiname, "itemsort");
					print "<td class=\"list_value_wrap\"><ul>";

					/***************************************************** PAGING HERE **********************************************************************/
					//set the total results and only get the results for this page
					$printindiname = $this->getPagedResults($printindiname, $this->resultsPerPage);
					$this->indiResultsPrinted = count($printindiname);
					foreach ($printindiname as $pkey => $pvalue) {
						$GEDCOM = $pvalue[2];
						if ($GEDCOM != $curged) {
							include (get_privacy_file());
							$curged = $GEDCOM;
						}
						print_list_person($pvalue[1], array (check_NN($pvalue[0]), $pvalue[2]), "", $pvalue[3]);
						print "\n";
					}
					print "\n\t\t</ul>&nbsp;</td>";
					$GEDCOM = $oldged;
					if ($GEDCOM != $curged) {
						include (get_privacy_file());
						$curged = $GEDCOM;
					}
				}

					/***************************************************** PAGING HERE **********************************************************************/
				if (count($printfamname)>0) {
					//set the total results and only get the results for this page
					$printfamname = $this->getPagedResults($printfamname, $this->resultsPerPage);
					$this->famResultsPrinted = count($printfamname);
					print "\n\t\t<td class=\"list_value_wrap\"><ul>";
					foreach ($printfamname as $pkey => $pvalue) {
						$GEDCOM = $pvalue[2];
						if ($GEDCOM != $curged) {
							include (get_privacy_file());
							$curged = $GEDCOM;
						}
						print_list_family($pvalue[1], array ($pvalue[0], $pvalue[2]), "", $pvalue[3]);
						print "\n";
					}
					print "\n\t\t</ul>&nbsp;</td>";
					$GEDCOM = $oldged;
					if ($GEDCOM != $curged) {
						include (get_privacy_file());
						$curged = $GEDCOM;
					}
				}

					/***************************************************** PAGING HERE **********************************************************************/
				if (count($actualsourcelist)>0) {
					print "\n\t\t<td class=\"list_value_wrap\"><ul>";
					//set the total results and only get the results for this page
					$actualsourcelist = $this->getPagedResults($actualsourcelist, $this->resultsPerPage);
					$this->srcResultsPrinted = count($actualsourcelist);
					foreach ($actualsourcelist as $key => $value) {
						print_list_source($key, $value);
					}
					print "\n\t\t</ul>&nbsp;</td>";
					$GEDCOM = $oldged;
					if ($GEDCOM != $curged) {
						include (get_privacy_file());
						$curged = $GEDCOM;
					}
				}
				$GEDCOM = $oldged;
				
				print "</tr><tr>\n\t";
				if ($this->indiResultsPrinted > 0 || $this->famResultsPrinted > 0 || $this->srcResultsPrinted > 0) {
					if (($this->indiResultsPrinted > 0) && (isset ($this->srindi))) {
						print "<td>".$pgv_lang["total_indis"]." ";
						if ($this->resultsPerPage >= $totalIndiResults)
							print $totalIndiResults;
						else
							if ($totalIndiResults > 0) {
								print (($this->resultsPerPage * $this->resultsPageNum) + 1)." to ";
								print (($this->resultsPerPage * $this->resultsPageNum) + $this->indiResultsPrinted)." of ".$totalIndiResults;
							}
						if (count($this->indi_private) > 0)
							print "  (".$pgv_lang["private"]." ".count($this->indi_private).")";
						if (count($this->indi_hide) > 0)
							print "  --  ".$pgv_lang["hidden"]." ".count($this->indi_hide);
						if (count($this->indi_private) > 0 || count($this->indi_hide) > 0)
							print_help_link("privacy_error_help", "qm");
						print "</td>";
					}

					if ($this->famResultsPrinted > 0 && isset ($this->srfams)) {
						print "<td>".$pgv_lang["total_fams"]." ";
						if ($this->resultsPerPage >= $totalFamResults)
							print $totalFamResults;
						else
							if ($totalFamResults > 0) {
								print (($this->resultsPerPage * $this->resultsPageNum) + 1)." to ";
								print (($this->resultsPerPage * $this->resultsPageNum) + $this->famResultsPrinted)." of ".$totalFamResults;
							}
						if (count($this->fam_private) > 0)
							print "  (".$pgv_lang["private"]." ".count($this->fam_private).")";
						if (count($this->fam_hide) > 0)
							print "  --  ".$pgv_lang["hidden"]." ".count($this->fam_hide);
						if (count($this->fam_private) > 0 || count($this->fam_hide) > 0)
							print_help_link("privacy_error_help", "qm");
						print "</td>";
					}

					if ($this->srcResultsPrinted > 0 && isset ($this->srsour)) {
						print "<td>".$pgv_lang["total_sources"]." ";
						if ($this->resultsPerPage >= $totalSrcResults)
							print $totalSrcResults;
						else
							if ($totalSrcResults > 0) {
								print (($this->resultsPerPage * $this->resultsPageNum) + 1)." to ";
								print (($this->resultsPerPage * $this->resultsPageNum) + $this->srcResultsPrinted)." of ".$totalSrcResults;
							}
						if (count($this->source_hide) > 0)
							print "  --  ".$pgv_lang["hidden"]." ".count($this->source_hide);
						print "</td>";
					}

					if ($this->indiResultsPrinted > 0 || $this->famResultsPrinted > 0 || $this->srcResultsPrinted > 0)
						print "</tr>\n\t";
				} else
					if (isset ($this->query)) {
						print "<td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br /></td></tr>\n\t\t";
						if (!isset ($this->srindi) && !isset ($this->srfams) && !isset ($this->srsour)) {
							print "<tr><td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_search_for"]."</i><br /></div>\n\t\t";
						}
					}
				print "</table></div>";
			} else
				if (isset ($this->query)) {
					print "<br /><div class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br />\n\t\t";
					if (!isset ($this->srindi) && !isset ($this->srfams) && !isset ($this->srsour)) {
						print "<i>".$pgv_lang["no_search_for"]."</i><br /></div>\n\t\t";
					}
				}
			// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
			if ($this->resultsPerPage >= 1 && $this->totalGeneralResults > $this->resultsPerPage) {
				$this->printPageResultsLinks($this->inputFieldNames, $this->totalGeneralResults, $this->resultsPerPage);
			}
		}

		//----- section to search and display results for a Soundex Search
		if ($this->action == "soundex") {
			if ($this->soundex == "DaitchM")
				DMsoundex("", "closecache");
			// 	$this->query = "";	// Stop function PrintReady from doing strange things to accented names
			if (((!empty ($this->lastname)) || (!empty ($this->firstname)) || (!empty ($this->place))) && (isset ($this->printname))) {
				print "<div class=\"center\"><br />";
				//set the total results and only get the results for this page
				$totalIndiResults = count($this->printname);
				$this->totalResults = $totalIndiResults;
				
				// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
				if ($this->resultsPerPage >= 1 && $this->totalResults > $this->resultsPerPage) {
					$this->printPageResultsLinks($this->inputFieldNames, $this->totalResults, $this->resultsPerPage);
				}
				print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>\n\t\t";
				$i = 0;
				$ct = count($this->printname);
				if ($ct > 0) {
					//			init_list_counters();
					$oldged = $GEDCOM;
					$curged = $GEDCOM;
					$extrafams = false;
					if (count($this->printfamname) > 0)
						$extrafams = true;
					if ($extrafams) {
						print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
						print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["families"]."</td>";
					} else
						print "<td colspan=\"2\" class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
					print "</tr><tr>\n\t\t<td class=\"list_value_wrap\"><ul>";

					/***************************************************** PAGING HERE **********************************************************************/

					$this->printname = $this->getPagedResults($this->printname, $this->resultsPerPage);
					$this->indiResultsPrinted = count($this->printname);
					foreach ($this->printname as $key => $pvalue) {
						$GEDCOM = $pvalue[2];
						if ($GEDCOM != $curged) {
							include (get_privacy_file());
							$curged = $GEDCOM;
						}
						print_list_person($pvalue[1], array (check_NN($pvalue[0]), $pvalue[2]), "", $pvalue[3]);
						$indiprinted[$pvalue[1]."[".$pvalue[2]."]"] = 1;
						print "\n";
						if (!$extrafams) {
							$i++;
							if ($i == floor($this->indiResultsPrinted / 2) && $this->indiResultsPrinted > 9)
								print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
						}
					}
					$GEDCOM = $oldged;
					if ($GEDCOM != $curged) {
						include (get_privacy_file());
						$curged = $GEDCOM;
					}
					print "\n\t\t</ul></td>";

					// Start printing the associated fams
					if ($extrafams) {
						uasort($this->printfamname, "itemsort");
						print "\n\t\t<td class=\"list_value_wrap\"><ul>";
						if (isset ($this->printfamname))
							$famCount = count($this->printfamname);
						else
							$famCount = 0;

						/***************************************************** PAGING HERE **********************************************************************/

						//set the total results and only get the results for this page
						$totalFamResults = count($this->printfamname);
						if ($totalFamResults > $this->totalResults)
							$this->totalResults = $totalFamResults;
						$this->printfamname = $this->getPagedResults($this->printfamname, $this->resultsPerPage);
						$this->famResultsPrinted = count($this->printfamname);
						foreach ($this->printfamname as $pkey => $pvalue) {
							$GEDCOM = $pvalue[2];
							if ($GEDCOM != $curged) {
								include (get_privacy_file());
								$curged = $GEDCOM;
							}
							print_list_family($pvalue[1], array ($pvalue[0], $pvalue[2]), "", $pvalue[3]);
							print "\n";
						}
						print "\n\t\t</ul>&nbsp;</td>";
						$GEDCOM = $oldged;
						if ($GEDCOM != $curged) {
							include (get_privacy_file());
							$curged = $GEDCOM;
						}
					}

					// start printing the table footer
					print "\n\t\t</tr>\n\t";
					if ($this->totalResults > 0) {
						print "<tr><td ";
						if ((!$extrafams) && ($ct > 9))
							print "colspan=\"2\">";
						else
							print ">";
						print $pgv_lang["total_names"]." ";
						if ($this->resultsPerPage >= $totalIndiResults)
							print $totalIndiResults;
						else
							if ($totalIndiResults > 0) {
								print (($this->resultsPerPage * $this->resultsPageNum) + 1)." to ";
								print (($this->resultsPerPage * $this->resultsPageNum) + $this->indiResultsPrinted)." of ".$totalIndiResults;
							}
						if (count($this->indi_private) > 0)
							print "  (".$pgv_lang["private"]." ".count($this->indi_private).")";
						if (count($this->indi_hide) > 0)
							print "  --  ".$pgv_lang["hidden"]." ".count($this->indi_hide);
						if (count($this->indi_private) > 0 || count($this->indi_hide) > 0)
							print_help_link("privacy_error_help", "qm");
						print "</td>";
						if ($extrafams) {
							print "<td>".$pgv_lang["total_fams"]." ";
							if ($this->resultsPerPage >= $totalFamResults)
								print $totalFamResults;
							else
								if ($this->resultsPerPage >= $totalFamResults) {
									print (($this->resultsPerPage * $this->resultsPageNum) + 1)." to ";
									print (($this->resultsPerPage * $this->resultsPageNum) + $this->famResultsPrinted)." of ".$totalFamResults;
								}
							if (count($this->fam_private) > 0)
								print "  (".$pgv_lang["private"]." ".count($this->fam_private).")";
							if (count($this->fam_hide) > 0)
								print "  --  ".$pgv_lang["hidden"]." ".count($this->fam_hide);
							if (count($this->fam_private) > 0 || count($this->fam_hide) > 0)
								print_help_link("privacy_error_help", "qm");
							print "</td>";
						}
						print "</tr>";
					}

				} else
					print "<td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i></td></tr>\n\t\t";
				print "</table></div>";
			}

			// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
			if ($this->resultsPerPage >= 1 && $this->totalResults > $this->resultsPerPage) {
				$this->printPageResultsLinks($this->inputFieldNames, $this->totalResults, $this->resultsPerPage);
			}
		}

		// ----- section to search and display results for multisite
		if ($this->action == "multisite") {
			// Only Display 5 results per 2 sites if the total results per page is 10
			$sitesChecked = 0;
			$i = 0;
			foreach ($this->Sites as $server) {
				$siteName = "server".$i;
				if (isset ($_REQUEST["$siteName"]))
					$sitesChecked ++;
				$i ++;
			}
			if ($sitesChecked >= 1) {
				$this->multiResultsPerPage = $this->resultsPerPage / $sitesChecked;

				if (!empty ($this->Sites) && count($this->Sites) > 0) {
					//AddToLog('About to diplay results');
					$no_results_found = false;
					// Start output here, because from the indi's we may have printed some fams which need the column header.
					print "<br />";
					print "\n\t<div class=\"center\">";

					if (isset ($this->multisiteResults) && (count($this->multisiteResults) > 0)) {
						$this->totalResults = 0;
						$this->multiTotalResults = 0;
						foreach ($this->multisiteResults as $key => $siteResults) {
							$serviceClient = ServiceClient :: getInstance($key);
							$siteName = $serviceClient->getServiceTitle();
							$siteURL = dirname($serviceClient->getURL());

							print "<table id=\"multiResultsOutTbl\" class=\"list_table, $TEXT_DIRECTION\" align=\"center\">";

							if (isset ($siteResults) && !empty ($siteResults->persons)) {
								$displayed_once = false;
								$personlist = $siteResults->persons;

								/***************************************************** PAGING HERE **********************************************************************/

								//set the total results and only get the results for this page
								$this->multiTotalResults += count($personlist);
								if ($this->totalResults < $this->multiTotalResults)
									$this->totalResults = $this->multiTotalResults;
								$personlist = $this->getPagedResults($personlist, $this->multiResultsPerPage);
								$pageResultsNum = 0;
								foreach ($personlist as $index => $person) {
									//if there is a name to display then diplay it
									if (!empty ($person->gedcomName)) {
										if (!$displayed_once) {
											if (!$no_results_found) {
												$no_results_found = true;
												print "<tr><td class=\"list_label\" colspan=\"2\" width=\"100%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".$pgv_lang["people"]."</td></tr>";
												print "<tr><td><table id=\"multiResultsInTbl\" align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" ><tr>";
											}
											$displayed_once = true;
											print "<td class=\"list_label\" colspan=\"2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >".$pgv_lang["site_list"]."<a href=\"".$siteURL."\" target=\"_blank\">".$siteName."</a>".$pgv_lang["site_had"]."</td></tr>";
										}
										print "<tr><td class=\"list_value $TEXT_DIRECTION\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" valign=\"center\" ><ul>";
										print "<li class=\"$TEXT_DIRECTION\" dir=\"$TEXT_DIRECTION\">";
										print "<a href=\"".$siteURL."/individual.php?pid=".$person->PID."&amp;ged=";
										print $serviceClient->gedfile;
										print "\" target=\"_blank\">";
										$indiName = sortable_name_from_name($person->gedcomName);
										$pageResultsNum += 1;
										print "<b>".$indiName."</b>";
										if (!empty ($person->PID)) {
											print " (".$person->PID.")";
										}
										if (!empty ($person->birthDate) || !empty ($person->birthPlace)) {
											print " -- <i>";
											if (!empty ($person->birthDate)) {
												print " ".$person->birthDate;
											}
											if (!empty ($person->birthPlace)) {
												print " ".$person->birthPlace;
											}
											print "</i>";
										}
										print "</a></li></ul></td>";

										/*******************************  Remote Links Per Result *************************************************/
										if (userCanEdit(getUserName())) {
											print "<td class=\"list_value $TEXT_DIRECTION\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >"."<ul style=\"list-style: NONE\"><li><a href=\"javascript:;\" "."onclick=\"return open_link('".$key."', '".$person->PID."', '".$indiName."');\">"."<b>".$pgv_lang["title_search_link"]."</b></a></ul></li></td></tr>\n";
										}
									}
								}

								print "</table>";

								print "\n\t\t&nbsp;</td></tr></table>";
							}
							if ($this->multiTotalResults > 0) {
								print "</tr><tr><td align=\"left\">Displaying individuals ";
								print (($this->multiResultsPerPage * $this->resultsPageNum) + 1)." to ". (($this->multiResultsPerPage * $this->resultsPageNum) + $pageResultsNum);
								print " of ". ($this->multiTotalResults)."</td></tr></table>";
								$this->multiTotalResults = 0;
							} else
								print "</tr></table>";
						}
						print "</table></div>";
					}
					if (!$no_results_found && $this->multiTotalResults == 0 && (isset ($this->multiquery) || isset ($this->name) || isset ($this->birthdate) || isset ($this->birthplace) || isset ($this->deathdate) || isset ($this->deathplace) || isset ($this->gender))) {
						print "<table align=\"center\" \><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".$pgv_lang["no_results"]."</i></b></font><br /></td></tr></table>";
						//					AddToLog('No results to display');
					}
				}
			} else
				if ($sitesChecked < 1 && $this->isPostBack) {
					print "<table align=\"center\" \><tr><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".$pgv_lang["no_search_site"]."</i></b></font><br /></td></tr></table>";
				}

			print "</table>";
			// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
			if ($this->resultsPerPage > 1 && $this->totalResults > $this->resultsPerPage) {
				$this->printPageResultsLinks($this->inputFieldNames, $this->totalResults, $this->multiResultsPerPage);
			}
		}
	}

	/************************************************   Helper Methods ****************************************************************/

	/**
	 * Function that returns only the results for the current page
	 * i.e. if $controller->resultsPageNum == 2 and $resultsPerPage == 10 this
	 * function would return results 11 - 20.
	 *
	 * @param array() $results - the original results.
	 * @param int $resultsPerPage - If $results count is less
	 * than $resultsPerPage it will simply return $results.
	 * @return array - the filtered results i.e. 11-20.
		*/
	function getPagedResults($results, $resultsPerPage) {
		$len = count($results);
		if ($len <= $resultsPerPage) {
			if ($this->resultsPageNum==0) return $results;
			else return array();
		}
		$pagedResults = array ();
		$startPosition = $this->resultsPageNum * $resultsPerPage;
		$endPosition = ($this->resultsPageNum + 1) * $resultsPerPage;
		$i = 0;
		if (isset ($results) && $len > 0) {
			foreach ($results as $key => $value) {
				if ($i >= $startPosition)
					$pagedResults[$key] = $value;
				$i ++;
				if ($i >= $endPosition)
					break;
			}
			return $pagedResults;
		}
		return array();
	}

	/**
	 * prints out the paging links for a page with many results i.e.  Result Page:   << 1 2 3 4 5 >>
	 *
	 * @param $this->inputFieldNames - an array of strings representing the names of the variables to include
	 * in the query string usually from input values in a form i.e. 'action', 'query', 'showasso' etc.
	 */
	function printPageResultsLinks($inputFieldNames, $totalResults, $resultsPerPage) {
		global $pgv_lang;
		print "<br /><table align='center'><tr><td>".$pgv_lang['result_page']." &nbsp;&nbsp;";
		// Prints the '<<' linking to the previous page if it's not on the first page
		if ($this->resultsPageNum > 0) {
			print " <a href='";
			$this->printQueryString($inputFieldNames, 0);
			print "'>&lt;&lt;</a> ";
			print " <a href='";
			$this->printQueryString($inputFieldNames, ($this->resultsPageNum - 1));
			print "'>&lt;</a>";
		}

		// Prints out each number linking to that page number.
		// If it's on that page number it is printed out bold instead of a link
		for ($i = 1; $i < (($totalResults / $resultsPerPage) + 1); $i ++) {
			if ($i != $this->resultsPageNum + 1) {
				print " <a href='";
				$this->printQueryString($inputFieldNames, ($i -1));
				print "'>".$i."</a>";
			} else
				print " <b>".$i."</b>";
		}

		// Prints the '>>' linking to the next page if it's not on the last page
		if ($this->resultsPageNum < (($totalResults / $resultsPerPage) - 1)) {
			print " <a href='";
			$this->printQueryString($inputFieldNames, ($this->resultsPageNum + 1));
			print "'>&gt;</a>";
			print " <a href='";
			$this->printQueryString($inputFieldNames, (int)($totalResults / $resultsPerPage));
			print "'>&gt;&gt;</a>";
		}

		print "</td></tr></table>";
	}

	/**
	 * Prints the query string that goes ... <a href'  HERE   '> for each paging result link
	 *
	 * @param $inputFieldNames - an array of strings representing the names of the variables to include
	 * in the query string usually from input values in a form i.e. 'action', 'query', 'showasso' etc.
	 * @param $pageNum - the page number to link to in the paged results
		*/
	function printQueryString($inputFieldNames, $pageNum) {
		global $GEDCOM;
		$first = true;
		print "search.php";
		foreach ($inputFieldNames as $key => $value) {
			$controllerVar = $this->getValue($value);
			if (!empty ($controllerVar)) {
				if ($first) {
					print "?";
					$first = false;
				} else
					print "&amp;";
				print "$value=".$controllerVar;
			}
		}
		print "&amp;resultsPageNum=".$pageNum;
		foreach($this->sgeds as $i=>$key) {
			$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $key);
			print "&amp;".$str."=yes";
		}
		print "&amp;ged=".$GEDCOM;
	}

	function getValue($varName) {
		if (isset ($this-> $varName)) {
			$value = $this-> $varName;
			return $value;
		} else
			return "";
	}
}
// -- end of class

//-- load a user extended class if one exists
if (file_exists('includes/controllers/search_ctrl_user.php')) {
	include_once 'includes/controllers/search_ctrl_user.php';
} else {
	class SearchController extends SearchControllerRoot {
	}
}
$controller = new SearchController();
$controller->init();
?>
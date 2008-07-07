<?php

/**
 * Controller for the Search Page
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
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
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
	var $replace = false;
	var $replaceNames = false;
	var $replacePlaces = false;
	var $replaceAll = false;
	var $replacePlacesWord = false;
	var $printplace = array();

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
		global $pgv_lang, $ALLOW_CHANGE_GEDCOM, $GEDCOM, $gGedcom;

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
			if ($_REQUEST["query"] == $pgv_lang["search"] || strlen($_REQUEST["query"])<2 || preg_match("/^\.+$/", $_REQUEST["query"])>0) {
				$this->query="";
			} else {
				$this->query = stripslashes($_REQUEST["query"]);
				$this->myquery = $this->query;
			}
		}
		if (isset ($_REQUEST["replace"])) {
			$this->replace = $_REQUEST["replace"];
				
			if(isset($_REQUEST["replaceNames"])) $this->replaceNames = true;
			if(isset($_REQUEST["replacePlaces"])) $this->replacePlaces = true;
			if(isset($_REQUEST["replacePlacesWord"])) $this->replacePlacesWord = true;
			if(isset($_REQUEST["replaceAll"])) $this->replaceAll = true;
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
		if (($ALLOW_CHANGE_GEDCOM) && (count($gGedcom) > 1)) {
			foreach ($gGedcom as $key => $gedarray) {
				$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $key);
				if (isset ($_REQUEST["$str"]) || isset ($this->topsearch)) {
					$this->sgeds[] = $key;
					$_REQUEST["$str"] = 'yes';
				}
			}
		}
		else
		$this->sgeds[] = $GEDCOM;
			
		// Retrieve the sites that can be searched
		$this->Sites = get_server_list();

		// vars use for soundex search
		if (!empty ($_REQUEST["firstname"])) {
			$this->firstname = $_REQUEST["firstname"];
			$this->myfirstname = $this->firstname;
		} else {
			$this->firstname="";
			$this->myfirstname = "";
		}
		if (!empty ($_REQUEST["lastname"])) {
			$this->lastname = $_REQUEST["lastname"];
			$this->mylastname = $this->lastname;
		} else {
			$this->lastname="";
			$this->mylastname = "";
		}
		if (!empty ($_REQUEST["place"])) {
			$this->place = $_REQUEST["place"];
			$this->myplace = $this->place;
		} else {
			$this->place="";
			$this->myplace = "";
		}
		if (!empty ($_REQUEST["year"])) {
			$this->year = $_REQUEST["year"];
			$this->myyear = $this->year;
		} else {
			$this->year="";
			$this->myyear = "";
		}

		// vars use for multisite search
		if (!empty ($_REQUEST["multiquery"])) {
			$this->multiquery = $_REQUEST["multiquery"];
			$this->mymultiquery = $this->multiquery;
		} else {
			$this->multiquery="";
			$this->mymultiquery = "";
		}
		if (!empty ($_REQUEST["name"])) {
			$this->name = $_REQUEST["name"];
			$this->myname = $this->name;
		} else {
			$this->name="";
			$this->myname = "";
		}
		if (!empty ($_REQUEST["birthdate"])) {
			$this->birthdate = $_REQUEST["birthdate"];
			$this->mybirthdate = $this->birthdate;
		} else {
			$this->birthdate="";
			$this->mybirthdate = "";
		}
		if (!empty ($_REQUEST["birthplace"])) {
			$this->birthplace = $_REQUEST["birthplace"];
			$this->mybirthplace = $this->birthplace;
		} else {
			$this->birthplace="";
			$this->mybirthplace = "";
		}
		if (!empty ($_REQUEST["deathdate"])) {
			$this->deathdate = $_REQUEST["deathdate"];
			$this->mydeathdate = $this->deathdate;
		} else {
			$this->deathdate="";
			$this->mydeathdate = "";
		}
		if (!empty ($_REQUEST["deathplace"])) {
			$this->deathplace = $_REQUEST["deathplace"];
			$this->mydeathplace = $this->deathplace;
		} else {
			$this->deathplace="";
			$this->mydeathplace = "";
		}
		if (!empty ($_REQUEST["gender"])) {
			$this->gender = $_REQUEST["gender"];
			$this->mygender = $this->gender;
		} else {
			$this->gender="";
			$this->mygender = "";
		}

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
		else if ($this->action == "replace") $this->SearchAndReplace();
		else if ($this->action == "multisite") $this->MultiSiteSearch();
	}

	function getPageTitle() {
		global $pgv_lang;
		if ($this->action == "general") return $pgv_lang["search_general"];
		else if ($this->action == "soundex") return $pgv_lang["search_soundex"];
		else if ($this->action == "replace") return $pgv_lang["search_replace"];
		else if ($this->action == "multisite") return $pgv_lang["multi_site_search"];
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
			{
				if($varName == "action")
				if($_REQUEST[$varName] == "replace")
				if(!PGV_USER_CAN_ACCEPT)
				{
					$this->action = "general";
					continue;
				}
				$this-> $varName = $_REQUEST[$varName];
			}
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
			if ($SHOW_SOURCES >= PGV_USER_ACCESS_LEVEL) {
				if (find_source_record($this->query)) {
					header("Location: source.php?sid=".$this->query."&ged=".$GEDCOM);
					exit;
				}
			}

			// see if it's a repository ID. If it's found and privacy allows it, JUMP!!!!
			if ($SHOW_SOURCES >= PGV_USER_ACCESS_LEVEL) {
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
		global $REGEXP_DB, $GEDCOM, $gGedcom;
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
//					uasort($this->myindilist, "itemsort");
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
					if ($cntgeds==1) $ged = $gGedcom[$this->sgeds[0]]["id"];
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
	 *  Preforms a search and replace
	 */
	function SearchAndReplace()
	{
		global $GEDCOM, $pgv_changes, $manual_save, $STANDARD_NAME_FACTS, $ADVANCED_NAME_FACTS;

		$this->sgeds = array($GEDCOM);
		$this->srindi = "yes";
		$this->srfams = "yes";
		$this->srsour = "yes";
		$oldquery = $this->query;
		$this->GeneralSearch();

		//-- don't try to make any changes if nothing was found
		if (count($this->myindilist)==0 && count($this->myfamlist)==0 && count($this->mysourcelist)==0) return;

		AddToLog("Search And Replace old:".$oldquery." new:".$this->replace);
		$manual_save = true;
		// Include edit functions.
		include_once("includes/functions_edit.php");
		// These contain the search query and the replace string
		// $this->replace;
		// $this->query;

		// These contain the search results
		// We need to iterate through them and do the replaces
		//$this->myindilist;
		$adv_name_tags = preg_split("/[\s,;: ]+/", $ADVANCED_NAME_FACTS);
		$name_tags = array_unique(array_merge($STANDARD_NAME_FACTS, $adv_name_tags));
		foreach($this->myindilist as $id => $individual) {
			if (isset($pgv_changes[$id."_".$GEDCOM])) $individual["gedcom"] = find_updated_record($id);

			$newRecord = $individual["gedcom"];
			if($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			}
			else {
				if($this->replaceNames) {
					foreach($name_tags as $f=>$tag) {
						$newRecord = preg_replace("~(\d) ".$tag." (.*)".$oldquery."(.*)~i",	"$1 ".$tag." $2".$this->replace."$3", $newRecord);
					}
				}
				if($this->replacePlaces) {
					if ($this->replacePlacesWord) $newRecord = preg_replace("~(\d) PLAC (.*)([,\W\s])".$oldquery."([,\W\s])~i",	"$1 PLAC $2$3".$this->replace."$4",$newRecord);
					else $newRecord = preg_replace("~(\d) PLAC (.*)".$oldquery."(.*)~i",	"$1 PLAC $2".$this->replace."$3",$newRecord);
				}
			}
			//-- if the record changed replace the record otherwise remove it from the search results
			if($newRecord != $individual["gedcom"]) replace_gedrec($id, $newRecord);
			else unset($this->myindilist[$id]);
		}

		foreach($this->myfamlist as $id => $family) {
			if (isset($pgv_changes[$id."_".$GEDCOM]))
			$family["gedcom"] = find_updated_record($id);

			$newRecord = $family["gedcom"];

			if($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			}
			else {
				if($this->replacePlaces) {
					if ($this->replacePlacesWord) $newRecord = preg_replace("~(\d) PLAC (.*)([,\W\s])".$oldquery."([,\W\s])~i",	"$1 PLAC $2$3".$this->replace."$4",$newRecord);
					else $newRecord = preg_replace("~(\d) PLAC (.*)".$oldquery."(.*)~i",	"$1 PLAC $2".$this->replace."$3",$newRecord);
				}
			}
			if($newRecord != $family["gedcom"]) replace_gedrec($id, $newRecord);
			else unset($this->myfamlist[$id]);
		}

		foreach($this->mysourcelist as $id => $source)
		{
			if (isset($pgv_changes[$id."_".$GEDCOM]))
			$source["gedcom"] = find_updated_record($id);

			$newRecord = $source["gedcom"];
				
			if($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			}
			else {
				if($this->replaceNames) {
					$newRecord = preg_replace("~(\d) TITL (.*)".$oldquery."(.*)~i",	"$1 TITL $2".$this->replace."$3", $newRecord);
					$newRecord = preg_replace("~(\d) ABBR (.*)".$oldquery."(.*)~i",	"$1 ABBR $2".$this->replace."$3", $newRecord);
				}
				if($this->replacePlaces) {
					if ($this->replacePlacesWord) $newRecord = preg_replace("~(\d) PLAC (.*)([,\W\s])".$oldquery."([,\W\s])~i",	"$1 PLAC $2$3".$this->replace."$4",$newRecord);
					else $newRecord = preg_replace("~(\d) PLAC (.*)".$oldquery."(.*)~i",	"$1 PLAC $2".$this->replace."$3",$newRecord);
				}
			}
			if($newRecord != $source["gedcom"]) replace_gedrec($id, $newRecord);
			else unset($this->mysourcelist[$id]);
		}

		write_changes();
	}

	/**
	 * Retrieves a list of places and performs a soundex search on them.
	 *
	 * Assigns the results to the global printname[]
	 */
	function Place_Search()
	{
		global $TBLPREFIX, $GEDCOM, $gGedcom, $DBQUERY;
		$sql = "SELECT i.i_id, i.i_file, i.i_name FROM ".$TBLPREFIX."places JOIN ".$TBLPREFIX."placelinks ON p_id = pl_p_id JOIN ".$TBLPREFIX."individuals as i ON pl_gid = i_id WHERE p_file = ".$gGedcom->mGEDCOMId." AND i_file = p_file AND (";

		$place_sdx = "";
			
		$placearr = explode(",", $this->place);
			
		//Determines type of soundex and performs it
		foreach($placearr as $place)
		{
			if($this->soundex == "DaitchM")
			{
				$place_sdx = DMSoundex($place);
					
				foreach($place_sdx as $key=>$val)
				{

					$sql .= "p_dm_soundex like '%".$place_sdx[$key]."%' OR ";
				}
			}

			if($this->soundex == "Russell")
			{
				$place_sdx = soundex($place);
					
				$sql .= "p_std_soundex = '".$place_sdx."' OR ";
			}
		}
			
		// Strip the extra 'OR' at the end of the sql query
		$sql = substr($sql, 0, strlen($sql) - 3);
		$sql .= ")";
		$res = dbquery($sql);
			
		//Stores results in printname[]
		$this->printname = array();
		while($row = $res->fetchRow())
		{
			$this->printname[] = array (sortable_name_from_name($row[2]), $row[0], get_gedcom_from_id($row[1]), "");
		}
	}

	/**
	 * 	Gathers results for a soundex search
	 *
	 *  TODO
	 *  ====
	 *  Does not search on the selected gedcoms, searches on all the gedcoms
	 *  Does not work on first names, instead of the code, value array is used in the search
	 *  Returns all the names even when Names with hit selected
	 *  Does not sort results by first name
	 *  Does not work on separate double word surnames
	 *  Does not work on duplicate code values of the searched text and does not give the correct code
	 *     Cohen should give DM codes 556000, 456000, 460000 and 560000, in 4.1 we search only on 560000??
	 *  Print the DM codes at least when the URL contains &DEBUG=1
	 *
	 *  The names' Soundex SQL table contains all the soundex values twice
	 *  The places table contains only one value
	 *
	 *  The code should be improved - see RFE
	 *
	 */
	function SoundexSearch() {
		global $REGEXP_DB, $GEDCOM, $gGedcom;
		global $TBLPREFIX;
		global $DBCONN, $indilist;

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
			
			if(!empty($this->place) && empty($this->firstname) && empty($this->lastname))
			{
				$this->Place_Search();
				return;
			}

			$this->myindilist = array ();
			if (count($this->sgeds) > 0) {
				// Start the search
				$this->printname = array ();
				$this->printfamname = array ();
				$res = search_indis_soundex($this->soundex, $this->lastname, $this->firstname, $this->place, $this->sgeds);
				if ($res!==false) {
					while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
						$indilist[$row['i_id']]["gedcom"] = $row['i_gedcom'];
						$indilist[$row['i_id']]["isdead"] = $row['i_isdead'];
						$indilist[$row['i_id']]["gedfile"] = $row['i_file'];
						$save = true;
						if ((!empty ($this->place)) || (!empty ($this->year))) {
							$indirec = $row['i_gedcom'];
							if ((!empty ($this->place)) &&  empty ($this->lastname) && empty ($this->firstname)) {
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
								if (empty ($this->lastname) && empty ($this->firstname) && $savep == true)
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
							$this->printname[] = array (sortable_name_from_name($row["i_name"]), $row["i_id"], get_gedcom_from_id($row["i_file"]), "");
							//								break; // leave out if we want all names from one indi shown
						}
					}
						
				}
			}
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
		if (count($this->printname) == 1 && $this->action!="replace") {
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

	/**
	 *
	 */
	function MultiSiteSearch() {
		global $REGEXP_DB;
		require_once ('includes/serviceclient_class.php');

		if (!empty ($this->Sites) && count($this->Sites) > 0) {
			$this->myindilist = array ();
			// This first tests to see if it just a basic site search
			if (!empty ($this->multiquery) && ($this->subaction == "basic")) {
				// Find out if the string is longer then one char if dont perform the search
				if (strlen($this->multiquery) > 1) {
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
				//Building the query string up
				$my_query = '';
				if (!empty ($this->myname)) {
					$my_query .= "NAME=".$this->myname;
				}
				if (!empty ($this->mybirthdate)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "BIRTHDATE=".$this->mybirthdate;
				}
				if (!empty ($this->mybirthplace)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "BIRTHPLACE=".$this->mybirthplace;
				}
				if (!empty ($this->deathdate)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "DEATHDATE=".$this->deathdate;
				}
				if (!empty ($this->mydeathplace)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query."DEATHPLACE=".$this->mydeathplace;
				}
				if (!empty ($this->mygender)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "GENDER=".$this->mygender;
				}
			}

			if (!empty ($my_query)) {
				$this->multisiteResults = array ();
				// loop through the selected site to search
				$i = 0;
				foreach ($this->Sites as $key => $site) {
					$vartemp = "server".$i;
					if (isset ($_REQUEST["$vartemp"])) {
						$serviceClient = ServiceClient :: getInstance($key);
						$result = $serviceClient->search($my_query);
						$this->multisiteResults[$key] = $result;
					}
					$i ++;
				}
			}
		}
	}

	function printResults() {
		include_once ("includes/functions_print_lists.php");
		global $GEDCOM, $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $pgv_lang, $global_facts, $REGEXP_DB;
		//-- all privacy settings must be global if we are going to load up privacy files
		global $SHOW_DEAD_PEOPLE,$SHOW_LIVING_NAMES,$SHOW_SOURCES,$MAX_ALIVE_AGE,$USE_RELATIONSHIP_PRIVACY,$MAX_RELATION_PATH_LENGTH;
		global $CHECK_MARRIAGE_RELATIONS,$PRIVACY_BY_YEAR,$PRIVACY_BY_RESN,$SHOW_PRIVATE_RELATIONSHIPS,$person_privacy,$user_privacy;
		global $global_facts,$person_facts;
		$somethingPrinted = false;	// whether anything printed
		// ---- section to search and display results on a general keyword search
		if ($this->action == "general" || $this->action=="replace") {
			if ((isset ($this->query)) && ($this->query != "")) {
				//--- Results in these tags will be ignored when the tagfilter is on

				// Never show results in _UID
				$skiptags = "_UID";

				// If not admin, also hide searches in RESN tags
				if (!PGV_USER_IS_ADMIN) $skiptags .= ", RESN";

				// Add the optional tags
				if ($this->tagfilter == "on") $skiptags .= ", _PGVU, FILE, FORM, TYPE, CHAN, SUBM, REFN";

				// Keep track of what indis are already printed to keep a reliable counter
				$indi_printed = array ();
				$fam_printed = array ();

				// init various counters
				//		init_list_counters();

				// printqueues for indi's and fams
				$printindiname = array ();
				$printfamname = array ();
				$actualsourcelist = array ();

				// remove the % added for the like comparision of non-regex databases
				if(!$REGEXP_DB)
				$this->query = str_replace('%', '.*', substr($this->query, 1, strlen($this->query) - 2));

				$cti = count($this->myindilist);
				if (($cti > 0) && (isset ($this->srindi))) {
					$oldged = $GEDCOM;
					$curged = $GEDCOM;

					// Add the facts in $global_facts that should not show
					$skiptagsged = $skiptags;
					foreach ($global_facts as $gfact => $gvalue) {
						if (isset ($gvalue["show"])) {
							if (($gvalue["show"] < PGV_USER_ACCESS_LEVEL))
							$skiptagsged .= ", ".$gfact;
						}
					}

					foreach ($this->myindilist as $key => $value) {
						if (count($this->sgeds) > 1) {
							$GEDCOM = get_gedcom_from_id(splitkey($key, "ged"));
							$key = splitkey($key, "id");
							if ($GEDCOM != $curged) {
								include (get_privacy_file());
								$curged = $GEDCOM;
								// Recalculate the tags to skip
								$skiptagsged = $skiptags;
								foreach ($global_facts as $gfact => $gvalue) {
									if (isset ($gvalue["show"])) {
										if (($gvalue["show"] < PGV_USER_ACCESS_LEVEL))
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
								$printindiname[] = array (check_NN(sortable_name_from_name($namearray[0])), $key, get_gedcom_from_id($value["gedfile"]), "");
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
							if (($gvalue["show"] < PGV_USER_ACCESS_LEVEL))
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
										if (($gvalue["show"] < PGV_USER_ACCESS_LEVEL))
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
							if (($gvalue["show"] < PGV_USER_ACCESS_LEVEL))
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
										if (($gvalue["show"] < PGV_USER_ACCESS_LEVEL))
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

				//-- [start] new code for sortable tables
				global $gGedcom;
				$oldged = $GEDCOM;
				foreach ($this->sgeds as $key=>$GEDCOM) {
					require(get_privacy_file());
					$datalist = array();
					foreach ($printindiname as $k=>$v) if ($v[2]==$GEDCOM) $datalist[$v[1]]=array("gid"=>$v[1]);
					// I removed the "name"=>$v[0] from the $datalist[$v[1]]=array("gid"=>$v[1], "name"=>$v[0]);
					// array because it contained an alternate name instead of the expected main name
					if ( count($datalist) > 0 ) {
						$somethingPrinted = true;
					}
					print_indi_table($datalist, $pgv_lang["individuals"]." : &laquo;".$this->myquery."&raquo; @ ".$gGedcom->mGedcomName);
				}
				foreach ($this->sgeds as $key=>$GEDCOM) {
					require(get_privacy_file());
					$datalist = array();
					foreach ($printfamname as $k=>$v) if ($v[2]==$GEDCOM) $datalist[]=$v[1];
					if ( count($datalist) > 0 ) {
						$somethingPrinted = true;
					}
					print_fam_table(array_unique($datalist), $pgv_lang["families"]." : &laquo;".$this->myquery."&raquo; @ ".$gGedcom->mGedcomName);
				}
				foreach ($this->sgeds as $key=>$GEDCOM) {
					require(get_privacy_file());
					$datalist = array();
					foreach ($actualsourcelist as $k=>$v) if ($v["gedfile"]==$gGedcom[$GEDCOM]["id"]) $datalist[]=$k;
					if ( count($datalist) > 0 ) {
						$somethingPrinted = true;
					}
					print_sour_table(array_unique($datalist), $pgv_lang["sources"]." : &laquo;".$this->myquery."&raquo; @ ".$gGedcom->mGedcomName);
				}
				$GEDCOM = $oldged;
				require(get_privacy_file());
				//-- [end] new code for sortable tables
				print "</div>";
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
			print "<br />";
			print "\n\t<div class=\"center\">\n";
			global $gGedcom;
			$oldged = $GEDCOM;
			$this->myquery = trim($this->mylastname." ".$this->myfirstname." ".$this->myplace." ".$this->myyear);
			foreach ($this->sgeds as $key=>$GEDCOM) {
				require(get_privacy_file());
				$datalist = array();
				foreach ($this->printname as $k=>$v) if ($v[2]==$GEDCOM) $datalist[]=$v[1];
				if ( count($datalist) > 0 ) {
					$somethingPrinted = true;
				}
				print_indi_table(array_unique($datalist), $pgv_lang["individuals"]." : &laquo;".$this->myquery."&raquo; @ ".$gGedcom->mGedcomName);
			}
			foreach ($this->sgeds as $key=>$GEDCOM) {
				require(get_privacy_file());
				$datalist = array();
				foreach ($this->printfamname as $k=>$v) if ($v[2]==$GEDCOM) $datalist[]=$v[1];
				if ( count($datalist) > 0 ) {
					$somethingPrinted = true;
				}
				print_fam_table(array_unique($datalist), $pgv_lang["families"]." : &laquo;".$this->myquery."&raquo; @ ".$gGedcom->mGedcomName);
			}
			$GEDCOM = $oldged;
			print "</div>";

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
					$no_results_found = false;
					// Start output here, because from the indi's we may have printed some fams which need the column header.
					print "<br />";
					print "\n\t<div class=\"center\">";

					if (isset ($this->multisiteResults) && (count($this->multisiteResults) > 0)) {
						$this->totalResults = 0;
						$this->multiTotalResults = 0;
						$somethingPrinted = true;	
						foreach ($this->multisiteResults as $key => $siteResults) {
							include_once('includes/serviceclient_class.php');
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
										if (PGV_USER_CAN_EDIT) {
											print "<td class=\"list_value $TEXT_DIRECTION\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >"."<ul style=\"list-style: NONE\"><li><a href=\"javascript:;\" "."onclick=\"return open_link('".$key."', '".$person->PID."', '".$indiName."');\">"."<b>".$pgv_lang["title_search_link"]."</b></a></ul></li></td></tr>\n";
										}
									}
								}

								print "</table>";

								print "\n\t\t&nbsp;</td></tr></table>";
							}
							if ($this->multiTotalResults > 0) {
								print "</tr><tr><td align=\"left\">Displaying individuals ";
								print (($this->multiResultsPerPage * $this->resultsPageNum) + 1)." ".$pgv_lang["search_to"]." ". (($this->multiResultsPerPage * $this->resultsPageNum) + $pageResultsNum);
								print " ".$pgv_lang["of"]." ". ($this->multiTotalResults)."</td></tr></table>";
								$this->multiTotalResults = 0;
							} else
							print "</tr></table>";
						}
						print "</table></div>";
					}
					if (!$no_results_found && $this->multiTotalResults == 0 && (isset ($this->multiquery) || isset ($this->name) || isset ($this->birthdate) || isset ($this->birthplace) || isset ($this->deathdate) || isset ($this->deathplace) || isset ($this->gender))) {
						print "<table align=\"center\" \><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".$pgv_lang["no_results"]."</i></b></font><br /></td></tr></table>";
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
		return $somethingPrinted;	// whether anything printed
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

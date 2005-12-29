<?php
/**
 * Searches based on user query.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @subpackage Display
 * @version $Id: search.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

require ("config.php");

/**
 * Variables required for paging the results.
 */
if (!isset ($isPostBack))
	$isPostBack = false;
if (isset($topsearch)) {
	$isPostBack = true;
	$srfams = 'yes';
	$srindi = 'yes';
	$srsour = 'yes';
	$srrepo = 'yes';
}
if (!isset ($resultsPageNum))
	$resultsPageNum = 0;
if (!isset ($resultsPerPage))
	$resultsPerPage = 50;
if (!isset ($totalResults))
	$totalResults = -1;
if (!isset ($action) || empty ($action))
	$action = "general";
if (!isset ($view))
	$view = "";

// Retrieve the sites that can be searched
$Sites = array ();
$Sites = get_server_list();

// Remove slashes
if (isset ($query)) {
	// Reset the "Search" text from the page header
	if ($query == $pgv_lang["search"]) {
		unset ($query);
		//unset($action);  // commented out now that general is the default action
	} else {
		$query = stripslashes($query);
		$myquery = $query;
	}
}
if (!isset ($soundex))
	$soundex = "Russell";
if (!isset ($subaction))
	$subaction = "";
if (!isset ($nameprt))
	$nameprt = "";
if (!isset ($tagfilter))
	$tagfilter = "on";
if (!isset ($showasso))
	$showasso = "off";

// vars use for multisite serach
if (!empty ($multiquery))
	$mymultiquery = $multiquery;
else {
	unset ($multiquery);
	$mymultiquery = "";
}
if (!empty ($name))
	$myname = $name;
else {
	unset ($name);
	$myname = "";
}
if (!empty ($birthdate))
	$mybirthdate = $birthdate;
else {
	unset ($birthdate);
	$mybirthdate = "";
}
if (!empty ($birthplace))
	$mybirthplace = $birthplace;
else {
	unset ($birthplace);
	$mybirthplace = "";
}
if (!empty ($deathdate))
	$mydeathdate = $deathdate;
else {
	unset ($deathdate);
	$mydeathdate = "";
}
if (!empty ($deathplace))
	$mydeathplace = $deathplace;
else {
	unset ($deathplace);
	$mydeathplace = "";
}
if (!empty ($gender))
	$mygender = $gender;
else {
	unset ($gender);
	$mygender = "";
}

// vars use for soundex serach
if (!empty ($firstname))
	$myfirstname = $firstname;
else {
	unset ($firstname);
	$myfirstname = "";
}
if (!empty ($lastname))
	$mylastname = $lastname;
else {
	unset ($lastname);
	$mylastname = "";
}
if (!empty ($place))
	$myplace = $place;
else {
	unset ($place);
	$myplace = "";
}
if (!empty ($year))
	$myyear = $year;
else {
	unset ($year);
	$myyear = "";
}

// Retrieve the gedcoms to search in
	$sgeds = array ();
	if (($ALLOW_CHANGE_GEDCOM) && (count($GEDCOMS) > 1)) {
		foreach ($GEDCOMS as $key => $ged) {
			$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $key);
			if (isset ($$str) || isset($topsearch)) {
				$sgeds[] = $key;
				$$str = 'yes';
			}
		}
	} else
		$sgeds[] = $GEDCOM;

	//This section is to handle searches entered in the top search box in the themes
		if (isset ($topsearch)) {
		// first set some required variables. Search only in current gedcom, only in indi's.
		$srindi = "yes";

		// Enable the default gedcom for search
		$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $GEDCOM);
		$$str = "yes";

		// Then see if an ID is typed in. If so, we might want to jump there.
		if (isset ($query)) {

			// see if it's an indi ID. If it's found and privacy allows it, JUMP!!!!
			if (find_person_record($query)) {
				if (showLivingNameByID($query) || displayDetailsByID($query)) {
					header("Location: individual.php?pid=".$query."&ged=".$GEDCOM);
					exit;
				}
			}
			// see if it's a family ID. If it's found and privacy allows it, JUMP!!!!
			if (find_family_record($query)) {
				//-- check if we can display both parents
				if (displayDetailsByID($query, "FAM") == true) {
					$parents = find_parents($query);
					if (showLivingNameByID($parents["HUSB"]) && showLivingNameByID($parents["WIFE"])) {
						header("Location: family.php?famid=".$query."&ged=".$GEDCOM);
						exit;
					}
				}
			}
			// see if it's an source ID. If it's found and privacy allows it, JUMP!!!!
			if ($SHOW_SOURCES >= getUserAccessLevel(getUserName())) {
				if (find_source_record($query)) {
					header("Location: source.php?sid=".$query."&ged=".$GEDCOM);
					exit;
				}
			}
			// see if it's a repository ID. If it's found and privacy allows it, JUMP!!!!
			if ($SHOW_SOURCES >= getUserAccessLevel(getUserName())) {
				if (find_repo_record($query)) {
					header("Location: repo.php?rid=".$query."&ged=".$GEDCOM);
					exit;
				}
			}
		}
	}

	// If we want to show associated persons, build the list
	if ($showasso == "on")
		get_asso_list();

	// Section to gather results for general search
	if ($action == "general") {
		//-- perform the search
		if (isset ($query)) {
			// -- array of names to be used for results. Must be here and empty.
			$myindilist = array ();
			$mysourcelist = array ();
			$myfamlist = array ();
			$myfamlist2 = array ();

			// Now see if there is a query left after the cleanup
			if (trim($query) != "") {

				// Write a log entry
				$logstring = "Type: General<br />Query: ".$query;
				AddToSearchlog($logstring, $sgeds);

				// Cleanup the querystring so it can be used in a database query
				// Note: when more than one word is entered, this will return results where one word
				// is in one subrecord, another in another subrecord. Theze results are filtered later.
				if (strlen($query) == 1)
					$query = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $query);
				if ($REGEXP_DB)
					$query = preg_replace(array ("/\(/", "/\)/", "/\//", "/\]/", "/\[/", "/\s+/"), array ('\(', '\)', '\/', '\]', '\[', '.*'), $query);
				else {
					$query = "%".preg_replace("/\s+/", "%", $query)."%";
				}
				// Search the indi's
				if ((isset ($srindi)) && (count($sgeds) > 0)) {
					$myindilist = search_indis($query, $sgeds);
				}

				// Search the fams
				if ((isset ($srfams)) && (count($sgeds) > 0)) {
					// Get the indilist, to check name hits. Store the ID's/names found in
					// the search array, so the fam records can be retrieved.
					// This way we include hits on family names.
					// If indi's are not searched yet, we have to search them first
					if (!isset ($srindi))
						$myindilist = search_indis($query, $sgeds);
					$famquery = array ();
					$cntgeds = count($sgeds);
					foreach ($myindilist as $key1 => $myindi) {
						foreach ($myindi["names"] as $key2 => $name) {
							if ((preg_match("/".$query."/i", $name[0]) > 0)) {
								if ($cntgeds > 1) {
									$ged = splitkey($key1, "ged");
									$key1 = splitkey($key1, "id");
								} else
									$ged = $sgeds[0];
								$famquery[] = array ($key1, $ged);
								break;
							}
						}
					}
					// Get the famrecs with hits on names from the family table
					if (!empty ($famquery))
						$myfamlist = search_fams_names($famquery, "OR", true, $cntgeds);
					// Get the famrecs with hits in the gedcom record from the family table
					if (!empty ($query))
						$myfamlist2 = search_fams($query, $sgeds, "OR", true);
					$myfamlist = pgv_array_merge($myfamlist, $myfamlist2);
				}

				// Search the sources
				if ((isset ($srsour)) && (count($sgeds) > 0)) {
					if (!empty ($query))
						$mysourcelist = search_sources($query, $sgeds);
				}

				//-- if only 1 item is returned, automatically forward to that item
				// Check for privacy first. If ID cannot be displayed, continue to the search page.
				if ((count($myindilist) == 1) && (count($myfamlist) == 0) && (count($mysourcelist) == 0)) {
					foreach ($myindilist as $key => $indi) {
						if (count($sgeds) > 1) {
							$ged = splitkey($key, "ged");
							$pid = splitkey($key, "id");
							if ($GEDCOM != $ged) {
								$oldged = $GEDCOM;
								$GEDCOM = $ged;
								include (get_privacy_file());
							}
						} else {
							$pid = $key;
							$key = $key."[".$indi["gedfile"]."]";
						}
						if (!isset ($assolist[$key])) {
							if (showLivingNameByID($pid) || displayDetailsByID($pid)) {
								header("Location: individual.php?pid=".$pid."&ged=".get_gedcom_from_id($indi["gedfile"]));
								exit;
							}
						}
						if ((count($sgeds > 1)) && (isset ($oldged))) {
							$GEDCOM = $oldged;
							include (get_privacy_file());
						}
					}
				}
				if ((count($myindilist) == 0) && (count($myfamlist) == 1) && (count($mysourcelist) == 0)) {
					foreach ($myfamlist as $$key => $fam) {
						if (count($sgeds) > 1) {
							$ged = splitkey($key, "ged");
							$pid = splitkey($key, "id");
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
						if (count($sgeds > 1)) {
							$GEDCOM = $oldged;
							include (get_privacy_file());
						}
					}
				}
				if ((count($myindilist) == 0) && (count($myfamlist) == 0) && (count($mysourcelist) == 1)) {
					foreach ($mysourcelist as $sid => $source) {
						if (count($sgeds) > 1) {
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
						if (count($sgeds > 1)) {
							$GEDCOM = $oldged;
							include (get_privacy_file());
						}
					}
				}
			}
		}
	}

	else if ($action == "soundex") {
		if (((!empty ($lastname)) || (!empty ($firstname)) || (!empty ($place))) && (count($sgeds) > 0)) {
			$logstring = "Type: Soundex<br />";
			if (!empty ($lastname))
				$logstring .= "Last name: ".$lastname."<br />";
			if (!empty ($firstname))
				$logstring .= "First name: ".$firstname."<br />";
			if (!empty ($place))
				$logstring .= "Place: ".$place."<br />";
			if (!empty ($year))
				$logstring .= "Year: ".$year."<br />";
			AddToSearchlog($logstring, $sgeds);

			// Adjust the search criteria
			if (isset ($firstname)) {
				if (strlen($firstname) == 1)
					$firstname = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $firstname);
				if ($REGEXP_DB)
					$firstname = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $firstname);
				else {
					$firstname = "%".preg_replace("/\s+/", "%", $firstname)."%";
				}
			}
			if (isset ($lastname)) {
				// see if there are brackets around letter(groups)
				$bcount = substr_count($lastname, "[");
				if (($bcount == substr_count($lastname, "]")) && $bcount > 0) {
					$barr = array ();
					$ln = $lastname;
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
				if (strlen($lastname) == 1)
					$lastname = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $lastname);
				if ($REGEXP_DB)
					$lastname = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $lastname);
				else {
					$lastname = "%".preg_replace("/\s+/", "%", $lastname)."%";
				}
			}
			if (isset ($place)) {
				if (strlen($place) == 1)
					$place = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $place);
				if ($REGEXP_DB)
					$place = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $place);
				else {
					$place = "%".preg_replace("/\s+/", "%", $place)."%";
				}
			}
			if (isset ($year)) {
				if (strlen($year) == 1)
					$year = preg_replace(array ("/\?/", "/\|/", "/\*/"), array ("\\\?", "\\\|", "\\\\\*"), $year);
				if ($REGEXP_DB)
					$year = preg_replace(array ("/\s+/", "/\(/", "/\)/", "/\[/", "/\]/"), array (".*", '\(', '\)', '\[', '\]'), $year);
				else {
					$year = "%".preg_replace("/\s+/", "%", $year)."%";
				}
			}
			$myindilist = array ();
			if (count($sgeds) > 0) {

				if ($soundex == "DaitchM")
					DMsoundex("", "opencache");

				// Do some preliminary stuff: determine the soundex codes for the search criteria
				if ((!empty ($lastname)) && ($soundex == "DaitchM"))
					$arr2 = DMsoundex($lastname);
				if ((!empty ($lastname)) && ($soundex == "Russell"))
					$arr2 = soundex($lastname);
				if (!empty ($firstname)) {
					$firstnames = preg_split("/\s/", trim($firstname));
					$farr = array ();
					for ($j = 0; $j < count($firstnames); $j ++) {
						if ($soundex == "Russell")
							$farr[$j] = soundex($firstnames[$j]);
						if ($soundex == "DaitchM")
							$farr[$j] = DMsoundex($firstnames[$j]);
					}
				}
				if ((!empty ($place)) && ($soundex == "DaitchM"))
					$parr = DMsoundex($place);
				if ((!empty ($place)) && ($soundex == "Russell"))
					$parr = soundex(trim($place));

				// Start the search
				$oldged = $GEDCOM;
				$printname = array ();
				$printfamname = array ();
				foreach ($sgeds as $indexval => $value) {
					//-- in index mode we can only look in 1 gedcom, in SQL mode we can do all
					if ($value == $GEDCOM) {
						$GEDCOM = $value;
						$INDILIST_RETRIEVED = false;
						$indilist = get_indi_list();
						// -- only get the names who match soundex
						foreach ($indilist as $key => $value) {
							$save = false; // if all names must be printed
							foreach ($value["names"] as $indexval => $namearray) {
								$name = check_NN($namearray[0]);
								$savel = false;
								if ($nameprt == "hit")
									$save = false; // if only matching names must be printed
								if (!empty ($lastname)) {
									$surname = check_NN($namearray[2]);
									$surnames = preg_split("/\s/", trim($surname));
									foreach ($surnames as $skey => $svalue) {
										if ($soundex == "Russell") {
											if (soundex($svalue) == $arr2)
												$savel = true;
											if ($savel)
												$save = true;
										}
										if ($soundex == "DaitchM") {
											$arr1 = DMsoundex($svalue);
											$y = 0;
											while ($y < count($arr1) && $save == false) {
												$z = 0;
												while ($z < count($arr2) && $save == false) {
													if ($arr1[$y] == $arr2[$z]) {
														$savel = true;
														//													print $key."  Search ln: ".$surname." (".$arr1[$y]."), hit on ".$lastname." (".$arr2[$z].")<br />"; //--- debug
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
								if ((!empty ($firstname)) && ($savel == true || empty ($lastname))) {
									$lnames = preg_split("/\//", $namearray[0]);
									$fname = $lnames[0];
									$fnames = preg_split("/\s/", trim($fname));
									$i = 0;
									while ($i < count($fnames) && $savef == false) {
										$j = 0;
										while ($j < count($firstnames) && $savef == false) {
											if ($soundex == "Russell") {
												if (soundex($fnames[$i]) == $farr[$j]) {
													$savef = true;
												}
											}
											if ($soundex == "DaitchM") {
												$arr1 = DMsoundex($fnames[$i]);
												$y = 0;
												while ($y < count($arr1) && $savef == false) {
													$z = 0;
													while ($z < count($farr) && $savef == false) {
														$a = 0;
														while ($a < count($farr[$z]) && $savef == false) {
															if ($arr1[$y] == $farr[$z][$a])
																$savef = true;
															//														if ($savef == true) print $key."  Search fn: ".$fname." (".$arr1[$y]."), hit on ".$firstname." (".$farr[$z][$a].")<br />"; //--- debug
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
									if (($savel == true || empty ($lastname)) && $savef == true)
										$save = true;
									else
										$save = false;
								}
								if ((!empty ($place)) || (!empty ($year))) {
									$indirec = find_person_record($key);
									if ((!empty ($place)) && ($savel == true || empty ($lastname)) && ($savef == true || empty ($firstname))) {
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
													if ($soundex == "Russell") {
														if (soundex(trim($places[$p][$pp])) == $parr)
															$savep = true;
													}
													if ($soundex == "DaitchM") {
														$arr1 = DMsoundex(trim($places[$p][$pp]));
														$y = 0;
														while ($y < count($arr1) && $savep == false) {
															$z = 0;
															while ($z < count($parr) && $savep == false) {
																if ($arr1[$y] == $parr[$z])
																	$savep = true;
																//															if ($savep == true) print $key."  Search pl: ".$places[$p][$pp]." (".$arr1[$y]."), hit on ".$place." (".$parr[$z].")<br />"; //--- debug
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
										if (($savel == true || empty ($lastname)) && ($savef == true || empty ($firstname)) && $savep == true)
											$save = true;
										else
											$save = false;
									}
									if (!empty ($year) && $save == true) {
										$yt = preg_match("/\d DATE (.*$year.*)/i", $indirec, $match);
										if ($yt == 0)
											$save = false;
									}
								}
								if ($save === true) {
									//								print "Added ".sortable_name_from_name($namearray[0]);
									$printname[] = array (sortable_name_from_name($namearray[0]), $key, get_gedcom_from_id($value["gedfile"]), "");
									//								break; // leave out if we want all names from one indi shown
								}
							}
						}
					}
				}
				$GEDCOM = $oldged;
			}
			// check the result on required characters
			if (isset ($barr)) {
				foreach ($printname as $pkey => $pname) {
					$print = true;
					foreach ($barr as $key => $checkchar) {
						if (str2upper(substr($pname[0], $checkchar[1], $checkchar[2])) != str2upper($checkchar[0])) {
							$print = false;
							break;
						}
					}
					if ($print == false) {
						unset ($printname[$pkey]);
					}
				}
			}
			// Now we have the final list of indi's to be printed.
			// We may add the assos at this point.

			if ($showasso == "on") {
				foreach ($printname as $key => $pname) {
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
									$printname[] = array (sortable_name_from_name($assoname[0]), $key, get_gedcom_from_id($asso["gedfile"]), $apid);
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
						unset ($assolist[$apid]);
					}
				}
			}

			//-- if only 1 item is returned, automatically forward to that item
			if (count($printname) == 1) {
				$oldged = $GEDCOM;
				$GEDCOM = $printname[0][2];
				include (get_privacy_file());
				if (showLivingNameByID($printname[0][1]) || displayDetailsByID($printname[0][1])) {
					header("Location: individual.php?pid=".$printname[0][1]."&ged=".$printname[0][2]);
					exit;
				} else {
					$GEDCOM = $oldged;
					include (get_privacy_file());
				}
			}
			uasort($printname, "itemsort");
			reset($printname);
		}
	}

	else if ($action == "multisite") {
		require_once ('includes/serviceclient_class.php');
		AddToLog("is_multisite search");

		if (!empty ($Sites) && count($Sites) > 0) {
			$myindilist = array ();
			// This first tests to see if it just a basic site search	
			if (!empty ($multiquery) && ($subaction == "basic")) {
				// Find out if the string is longer then one char if dont perform the search 
				if (strlen($multiquery) > 1) {
//					AddToLog('Basic query: '.$multiquery);
					$my_query = $multiquery;
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
				if (($subaction == "advanced") && (!empty ($myname) || !empty ($mybirthdate) || !empty ($mybirthplace) || !empty ($mydeathdate) || !empty ($mydeathplace) || !empty ($mygender))) {
//					AddToLog('Advanced query');
					//Building the query string up
					$my_query = '';
					if (!empty ($myname)) {
						$my_query .= "NAME=".$myname;
//						AddToLog('NAME: '.$myname);
					}
					if (!empty ($mybirthdate)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "BIRTHDATE=".$mybirthdate;
//						AddToLog('BIRTHDATE: '.$mybirthdate);
					}
					if (!empty ($mybirthplace)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "BIRTHPLACE=".$mybirthplace;
//						AddToLog('BIRTHPLACE: '.$mybirthplace);
					}
					if (!empty ($mydeathdate)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "DEATHDATE=".$mydeathdate;
//						AddToLog('DEATHDATE: '.$mydeathdate);
					}
					if (!empty ($mydeathplace)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query."DEATHPLACE=".$mydeathplace;
//						AddToLog('DEATHPLACE: '.$mydeathplace);
					}
					if (!empty ($mygender)) {
						if ($my_query != '')
							$my_query .= '&';
						$my_query .= "GENDER=".$mygender;
//						AddToLog('GENDER: '.$mygender);
					}
				}

			if (!empty ($my_query)) {
//				AddToLog("Query: ".$my_query);
				$Results = array ();
				// loop through the selected site to search
				$i = 0;
				foreach ($Sites as $key => $site) {
					$vartemp = "server".$i;
					if (isset ($$vartemp)) {
//						AddToLog('Site: '.$site['name']);
//print "<br />".$key;
						$serviceClient = ServiceClient :: getInstance($key);
//print $serviceClient;
						$result = $serviceClient->search($my_query);
if (!isset($result->totalResults)) print_r($result);
						$Results[$key] = $result;
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

print_header($pgv_lang["search"]);

?>
<script language="JavaScript" type="text/javascript">
<!--
	function checknames(frm) {
		action = "<?php print $action ?>";
		if (action == "general") 
		{
			if (frm.query.value.length<2) {
				alert("<?php print $pgv_lang["search_more_chars"]?>");
				frm.query.focus();
				return false;
			}
		}
		else if (action == "soundex") 
		{
			year = frm.year.value;
			fname = frm.firstname.value;
			lname = frm.lastname.value;
			place = frm.place.value;
			
			// display an error message if there is insufficient data to perform a search on
			if (year == "") {
				message = true;
				if (fname.length >= 2)
					message = false; 
				if (lname.length >= 2)
					message = false;
				if (place.length >= 2)
					message = false;
				if(message) {
					alert("<?php print $pgv_lang["search_more_chars"]?>");
					return false;
				}
			}
			
			// display a special error if the year is entered without a valid Given Name, Last Name, or Place 
			if (year != "") {
				message = true;
				if (fname != "")
					message = false;
				if (lname != "")
					message = false;
				if (place != "")
					message = false;
				if (message) { 
					alert("<?php print $pgv_lang["invalid_search_input"]?>");
					frm.firstname.focus();
					return false;
				}
			}
			return true;
		}
		else if (action == "multisite") 
		{
			if(frm.subaction.value=='basic')
			{
				if (frm.multiquery.value.length < 2) {
					alert("<?php print $pgv_lang["search_more_chars"]?>");
					return false;
				}
			}
			else if(frm.subaction.value == 'advanced')
			{
				message = true;
				name = frm.name.value;
				bdate = frm.birthdate.value;
				bplace = frm.birthplace.value;
				ddate = frm.deathdate.value;
				dplace = frm.deathplace.value;
				sex = frm.gender.value;
				
				if(name.length > 1)
					message = false;
				if(bdate.length > 1)
					message = false;
				if (bplace.length > 1)
					message = false;
				if (ddate.length > 1)
					message = false;
				if (dplace.length > 1)
					message = false;
				if(message) 
				{
					if(sex.length < 1)
					{
						alert("<?php print $pgv_lang["invalid_search_multisite_input"]?>");
						return false;
					}
					alert("<?php print $pgv_lang["invalid_search_multisite_input_gender"]?>");
					return false;
				}
			}
		}
		return true;
	}
	
	function open_link(server, pid, indiName){
		window.open("addsearchlink.php?server="+server+"&pid="+pid+"&indiName="+indiName, "", "top=50,left=50,width=700,height=500,scrollbars=1,scrollable=1,resizable=1");
		return false;
	}
	
//-->
</script>

<?php
	print "<center><h2>".$pgv_lang["search_gedcom"]."</h2></center>";
	print "<table class=\"list_table, $TEXT_DIRECTION\" width=\"35%\" border=\"0\" align=\"center\">";

	if ($view == "preview") {
		// to be done
		//	print "</td><tr><td align=\"center\">".$logstring."</td></tr></table>";
	}
	else {

		/*************************************************** Search Form Outer Table **************************************************/

		print "<tr>";
		print "<form method=\"post\" name=\"searchform\" onsubmit=\"return checknames(this);\" action=\"search.php?action=$action\">";
		$inputFieldNames[] = "action";
		print "<input type=\"hidden\" name=\"isPostBack\" value=\"true\" />\n";
		$inputFieldNames[] = "isPostBack";
		
		/**************************************************** General Search Form *************************************************************/
		
		if($action == "general") {
			print "<div id=\"gsearch\" style=\"display: block\">";
			print "<td colspan=\"3\" class=\"facts_label03\" style=\"text-align:center; \">";
			print $pgv_lang["search_general"];
			print_help_link("search_enter_terms_help", "qm");
			print "</td></tr>";
			// search terms
			print "<tr><td class=\"list_label\" style=\"padding: 5px;\">";
			print $pgv_lang["enter_terms"];
			print "</td><td class=\"list_value\" style=\"padding: 5px;\"><input tabindex=\"1\" type=\"text\" name=\"query\" value=\"";
			if (isset($myquery)) {
				$inputFieldNames[] = "query";
				print $myquery;
			}
			print "\" />";
			print "</td><td class=\"list_value\" style=\"vertical-align: middle; text-align: center; padding: 5px;\"  rowspan=\"4\">";
			print "<input tabindex=\"2\" type=\"submit\" value=\"".$pgv_lang["search"]."\" /></tr>";
			// Choice where to search
			print "<tr><td class=\"list_label\" style=\"padding: 5px;\">".$pgv_lang["search_inrecs"];
			print "</td><td class=\"list_value\" style=\"padding: 5px;\">";
			print "<input type=\"checkbox\"";
			if (isset($srindi) || !$isPostBack) {
				$inputFieldNames[] = "srindi";
				print " checked=\"checked\"";
			}
			print " value=\"yes\" name=\"srindi\" />".$pgv_lang["search_indis"]."<br />";
			print "<input type=\"checkbox\"";
			if (isset($srfams)) {
				$inputFieldNames[] = "srfams";
				print " checked=\"checked\"";
			}
			print " value=\"yes\" name=\"srfams\" />".$pgv_lang["search_fams"]."<br />";
			print "<input type=\"checkbox\"";
			if (isset($srsour)) {
				$inputFieldNames[] = "srsour";				
				print " checked=\"checked\"";
			}
			print " value=\"yes\" name=\"srsour\" />".$pgv_lang["search_sources"]."<br />";
			print "</td>";
			print "</tr>";
			print "<tr><td class=\"list_label\" style=\"padding: 5px;\">".$pgv_lang["search_tagfilter"]."</td>";
			print "<td class=\"list_value\" style=\"padding: 5px;\"><input type=\"radio\" name=\"tagfilter\" value=\"on\" ";
			$inputFieldNames[] = "tagfilter";
			if (($tagfilter == "on") || ($tagfilter == ""))
				print "checked=\"checked\" ";
			print ">".$pgv_lang["search_tagfon"]."<br /><input type=\"radio\" name=\"tagfilter\" value=\"off\" ";
			if ($tagfilter == "off")
				print "checked=\"checked\"";
			print " />".$pgv_lang["search_tagfoff"];
			print "</td></tr>";
			print "<tr><td class=\"list_label\" style=\"padding: 5px;\">";
			print $pgv_lang["search_asso_label"];
			print "</td><td class=\"list_value\" style=\"padding: 5px;\">";
			print "<input type=\"checkbox\" name=\"showasso\" value=\"on\" ";
			$inputFieldNames[] = "showasso";
			if ($showasso == "on" || !$isPostBack)
				print "checked=\"checked\" ";
			print "/>".$pgv_lang["search_asso_text"];
			print "</td></tr>";
		}

		/**************************************************** Soundex Search Form *************************************************************/
		
		if($action == "soundex") {
			print "<div id=\"ssearch\" style=\"display: block\">";
			print "<td colspan=\"3\" class=\"facts_label03\" style=\"text-align:center; \">";
			print $pgv_lang["soundex_search"];
 			print_help_link("soundex_search_help", "qm");
			print "</td></tr><tr><td class=\"list_label\" width=\"35%\">";
			print $pgv_lang["firstname_search"];
			print "</td><td class=\"list_value\">";
			$inputFieldNames[] = "firstname";			
			print "<input tabindex=\"3\" type=\"text\" name=\"firstname\" value=\"$myfirstname\" />";
			print "</td><td class=\"list_value\" style=\"vertical-align: middle; text-align: center; padding: 5px;\"  rowspan=\"7\">";
			print "<input tabindex=\"7\" type=\"submit\" value=\"";
			print $pgv_lang["search"];
			print "\" />";
			print "</td></tr><tr><td class=\"list_label\">";
			print $pgv_lang["lastname_search"];
			$inputFieldNames[] = "lastname";
			print "</td><td class=\"list_value\"><input tabindex=\"4\" type=\"text\" name=\"lastname\" value=\"$mylastname\" /></td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["search_place"];
			$inputFieldNames[] = "place";
			print "</td><td class=\"list_value\"><input tabindex=\"5\" type=\"text\" name=\"place\" value=\"$myplace\" /></td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["search_year"];
			$inputFieldNames[] = "year";
			print "</td><td class=\"list_value\"><input tabindex=\"6\" type=\"text\" name=\"year\" value=\"$myyear\" /></td>";
			print "</tr>";
			print "<tr><td class=\"list_label\" >";
			print $pgv_lang["search_soundextype"];
			$inputFieldNames[] = "soundex";
			print "<td class=\"list_value\" ><input type=\"radio\" name=\"soundex\" value=\"Russell\" ";
			if (($soundex == "Russell") || ($soundex == "")) print "checked=\"checked\" ";
				print " />".$pgv_lang["search_russell"]."<br /><input type=\"radio\" name=\"soundex\" value=\"DaitchM\" ";
			if ($soundex == "DaitchM") print "checked=\"checked\" ";
				print " />".$pgv_lang["search_DM"];
			print "</td>";
			print "</td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["search_prtnames"];
			$inputFieldNames[] = "nameprt";
			print "</td><td class=\"list_value\" ><input type=\"radio\" name=\"nameprt\" value=\"hit\" ";
			if (($nameprt == "hit") || ($nameprt == "")) print "checked=\"checked\" ";
				print ">".$pgv_lang["search_prthit"]."<br /><input type=\"radio\" name=\"nameprt\" value=\"all\" ";
			if ($nameprt == "all") print "checked=\"checked\" ";;
				print " />".$pgv_lang["search_prtall"];
			print "</td>";
			print "</td></tr>";
			print "<tr><td class=\"list_label\" style=\"padding: 5px;\">";
			print $pgv_lang["search_asso_label"];
			print "</td><td class=\"list_value\" style=\"padding: 5px;\">";
			print "<input type=\"checkbox\" name=\"showasso\" value=\"on\" ";
			$inputFieldNames[] = "showasso";
			if ($showasso == "on" || !$isPostBack)
				print "checked=\"checked\" ";
			print "/>".$pgv_lang["search_asso_text"];
			print "</td></tr>";
		}
		
		/**************************************************** Multi Site Search Form *************************************************************/

		if ($action == "multisite") {
			print "<div id=\"multisite_options\" style=\"display: block\">";
			$inputFieldNames[] = "subaction";
			print "<input type=\"hidden\" name=\"subaction\" value=\"basic\" />\n";
			print "<td colspan=\"3\" class=\"facts_label03\" style=\"text-align:center; \">";
			print $pgv_lang["multi_site_search"];
			print_help_link("multi_site_search_help", "qm");
			print "</td></tr>";
			print "<tr><td class=\"list_label\" >";
			print $pgv_lang["search_sites"];
			print "</td><td colspan=\"2\" class=\"list_value\" align=\"center\"><table><tr><td align=\"left\" >";
			$i=0;
			if($Sites)
			{
				foreach($Sites as $server)
				{
					print "<input tabindex=\"$i\" type=\"checkbox\" ";
					$vartemp = "server".$i;
					if(isset($$vartemp)) {
						if ($$vartemp == "on")
							print "checked=\"checked\" value=\"on\" ";
					}
					else if(!$isPostBack)
						print "checked=\"checked\" value=\"on\" ";
					$inputFieldNames[] = "server".$i;
					print "name=\"server".$i."\" />".$server['name']."<br />";
					$i++;
				}
			}
			else
			{
				print $pgv_lang["no_known_servers"];
			}
			print "</td></tr></table></td></tr>";
			// this is for the basic site search involving just a query string text
			print "<tr><td colspan=\"3\" class=\"facts_label02\" >";
			print $pgv_lang["basic_search_discription"];
			print "</td></tr>";
			print "<td class=\"list_label\" >";
			print $pgv_lang["basic_search"];
			print "</td><td class=\"list_value\">";
			$inputFieldNames[] = "multiquery";
			print "<input tabindex=\"".($i+1)."\" type=\"text\" name=\"multiquery\" value=\"$mymultiquery\" />";
			print "<td class=\"list_value\" style=\"vertical-align: middle; text-align: center; padding: 5px;\"  rowspan=\"1\">";
			print "<input tabindex=\"".($i+2)."\" type=\"submit\" value=\"";
			print $pgv_lang["search"];
			print "\" onclick=\"document.searchform.subaction.value='basic';\"/></td></tr>";
			/// this is for the advanced site search
			print "<tr><td class=\"facts_label02\" colspan=\"3\" >";
			print $pgv_lang["advanced_search_discription"];
			print "</td></tr>";
			print "<tr><td class=\"list_label\" >";
			print $pgv_lang["name_search"];
			print "</td><td class=\"list_value\">";
			$inputFieldNames[] = "name";
			print "<input tabindex=\"".($i+3)."\" type=\"text\" name=\"name\" value=\"$myname\" />";
			print "</td><td class=\"list_value\" style=\"vertical-align: middle; text-align: center; padding: 5px;\"  rowspan=\"6\">";			
			print "<input tabindex=\"".($i+9)."\" type=\"submit\" value=\"";
			print $pgv_lang["search"];
			print "\"  onclick=\"document.searchform.subaction.value='advanced';\"/>";
			print "</td></tr><tr><td class=\"list_label\">";
			print $pgv_lang["birthdate_search"];
			$inputFieldNames[] = "birthdate";
			print "</td><td class=\"list_value\"><input tabindex=\"".($i+4);
			print "\" type=\"text\" name=\"birthdate\" value=\"$mybirthdate\" /></td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["birthplace_search"];
			$inputFieldNames[] = "birthplace";
			print "</td><td class=\"list_value\"><input tabindex=\"".($i+5);
			print "\" type=\"text\" name=\"birthplace\" value=\"$mybirthplace\" /></td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["deathdate_search"];
			$inputFieldNames[] = "deathdate";
			print "</td><td class=\"list_value\"><input tabindex=\"".($i+6);
			print "\" type=\"text\" name=\"deathdate\" value=\"$mydeathdate\" /></td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["deathplace_search"];
			$inputFieldNames[] = "deathplace";
			print "</td><td class=\"list_value\"><input tabindex=\"".($i+7);
			print "\" type=\"text\" name=\"deathplace\" value=\"$mydeathplace\" /></td></tr>";
			print "<tr><td class=\"list_label\">";
			print $pgv_lang["gender_search"];
			$inputFieldNames[] = "gender";
			print "</td><td class=\"list_value\"><input tabindex=\"".($i+8);
			print "\" type=\"text\" name=\"gender\" value=\"$mygender\" /></td></tr>";
		}
		
		// If the search is a general or soundex search then possibly display checkboxes for the gedcoms
		if ($action == "general" || $action == "soundex") {
			// If more than one GEDCOM, switching is allowed AND DB mode is set, let the user select
			if ((count($GEDCOMS) > 1) && ($ALLOW_CHANGE_GEDCOM)) {

//				print "<tr><td class=\"list_label\" style=\"padding: 5px;\">".$pgv_lang["search_options"];
//				print_help_link("search_options_help", "qm");
//				print "</td><td colspan='2' class=\"list_value\" style=\"padding: 5px;\" style=\"text-align:center; \"></td></tr>";
		
				print "<tr><td class=\"list_label\" style=\"padding: 5px;\">";
				print $pgv_lang["search_geds"];
				print "</td><td class=\"list_value\" style=\"padding: 5px;\" colspan=\"2\">";
				$i = 0;
				foreach ($GEDCOMS as $key=>$ged) {
					$str = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $key);
					$inputFieldNames[] = "$str";
					print "<input type=\"checkbox\" ";
					if (isset($$str) || (!$isPostBack && $GEDCOM==$key))
						print "checked=\"checked\" ";
					print "value=\"yes\" name=\"".$str."\""." />".$GEDCOMS[$key]["title"]."<br />";
					$i++;
				}
				print "</td></tr>";
			}	
		}
	
		print "<tr><td class=\"list_label\" style=\"padding: 5px;\" >".$pgv_lang["other_searches"]."</td>";
		print "<td class=\"list_value\" style=\"padding: 5px;\" style=\"text-align:center; \" colspan=\"2\" >";
		if ($action == "general") {
			print "<a href='?action=soundex'>".$pgv_lang["search_soundex"]."</a>";
			if ($SHOW_MULTISITE_SEARCH >= getUserAccessLevel()) {
				$sitelist = get_server_list();
				if (count($sitelist)>0) {
					print " | <a href='?action=multisite'>".$pgv_lang["multi_site_search"]."</a></td></tr>";
				}
			}
		}
		else if ($action == "soundex") {
			print "<a href='?action=general'>".$pgv_lang["search_general"]."</a>";
			if ($SHOW_MULTISITE_SEARCH >= getUserAccessLevel()) {
				$sitelist = get_server_list();
				if (count($sitelist)>0) {
					print " | <a href='?action=multisite'>".$pgv_lang["multi_site_search"]."</a></td></tr>";
				}
			}
		}
		else if ($action == "multisite") {
			print "<a href='?action=general'>".$pgv_lang["search_general"]."</a> | ";
			print "<a href='?action=soundex'>".$pgv_lang["search_soundex"]."</a></td></tr>";		
		}
		
		print "</table></div>";
		print "</form></td></tr></table><br />";		
}

	// ---- section to search and display results on a general keyword search
	if ($action=="general") {
		if ((isset($query)) && ($query!="")) {
		//--- Results in these tags will be ignored when the tagfilter is on

		// Never show results in _UID
		if (userIsAdmin(getUserName())) $skiptags = "_UID";
		
		// If not admin, also hide searches in RESN tags
		else $skiptags = "RESN, _UID";
		
		// Add the optional tags
		$skiptags_option = ", _PGVU, FILE, FORM, CHAN, SUBM, REFN";
    	if ($tagfilter == "on") $skiptags .= $skiptags_option;
   		$userlevel = GetUserAccessLevel();

		// Keep track of what indi's are already printed to keep a reliable counter
		$indi_printed = array();
		$fam_printed = array();
  
		// init various counters
		init_list_counters();

		// printqueues for indi's and fams
		$printindiname = array();
		$printfamname = array();

		$cti=count($myindilist);
		if (($cti>0) && (isset($srindi))) {
			$oldged = $GEDCOM;
			$curged = $GEDCOM;

			// Add the facts in $global_facts that should not show
			$skiptagsged = $skiptags;
    		foreach ($global_facts as $gfact => $gvalue) {
	    		if (isset($gvalue["show"])) {
		    		if (($gvalue["show"] < $userlevel)) $skiptagsged .= ", ".$gfact;
	    		}
  		  	}

			foreach ($myindilist as $key => $value) {
				if (count($sgeds) > 1) {
					$GEDCOM = splitkey($key, "ged");
					$key = splitkey($key, "id");
					if ($GEDCOM != $curged) {
						include(get_privacy_file());
						$curged = $GEDCOM;
						// Recalculate the tags to skip
						$skiptagsged = $skiptags;
						foreach ($global_facts as $gfact => $gvalue) {
				    		if (isset($gvalue["show"])) {
		    					if (($gvalue["show"] < $userlevel)) $skiptagsged .= ", ".$gfact;
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
					for ($ci=1; $ci<=$ncnt; $ci++) {
						$record = get_sub_record($ci, "1 NAME", $value["gedcom"]);
						if (preg_match("/".$query."/i", $record)>0) {
							$hit = true;
							$found = true;
						}
					}
				}
		    	else {
			    	$found = false;
			    	// First check if the hit is in the key!
			    	if ($tagfilter == "off") {
		    			if (strpos(str2upper($key), str2upper($query)) !== false) $found = true;
		    		}
					if ($found == false) {
			    		$recs = get_all_subrecords($value["gedcom"], "", false, false, false);
						// Also levels>1 must be checked for tags. This is done below.
						foreach ($recs as $keysr => $subrec) {
							$recs2 = preg_split("/\r?\n/", $subrec);
							foreach ($recs2 as $keysr2 => $subrec2) {
								// There must be a hit in a subrec. If found, check in which tag
								if (preg_match("/$query/i", $subrec2, $result)>0) {
									$ct = preg_match("/\d\s(\S*)\s*.*/i", $subrec2, $result2);
									if (($ct > 0) && (!empty($result2[1]))) {
										$hit = true;
										// if the tag can be displayed, do so
										if (strpos($skiptagsged, $result2[1]) === false) $found = true;
									}
								}
								if ($found == true) break;
							}
							if ($found == true) break;
						}
					}
				}
				if ($found == true) {
					
					// print all names from the indi found
			    	foreach($value["names"] as $indexval => $namearray) {
						$printindiname[] = array(sortable_name_from_name($namearray[0]), $key, get_gedcom_from_id($value["gedfile"]), "");
					}
					$indi_printed[$key."[".$GEDCOM."]"] = "1";
					
					// If associates must be shown, see if we can display them and add them to the print array
					if (($showasso == "on") && (strpos($skiptagsged, "ASSO") === false)) {
						$apid = $key."[".$value["gedfile"]."]";
						// Check if associates exist
						if (isset($assolist[$apid])) {
							// if so, print all indi's where the indi is associated to
							foreach($assolist[$apid] as $indexval => $asso) {
								if ($asso["type"] == "indi") {
									$indi_printed[$indexval] = "1";
									// print all names
									foreach($asso["name"] as $nkey => $assoname) {
										$key = splitkey($indexval, "id");
										$printindiname[] = array(sortable_name_from_name($assoname[0]), $key, get_gedcom_from_id($asso["gedfile"]), $apid);
									}
								}
								else if ($asso["type"] == "fam") {
									$fam_printed[$indexval] = "1";
									// print all names
									foreach($asso["name"] as $nkey => $assoname) {
										$assosplit = preg_split("/(\s\+\s)/", trim($assoname));
										// Both names have to have the same direction
										if (hasRTLText($assosplit[0]) == hasRTLText($assosplit[1])) {
											$apid2 = splitkey($indexval, "id");
											$printfamname[]=array(check_NN($assoname), $apid2, get_gedcom_from_id($asso["gedfile"]), $apid);
										}
									}
								}
							}
						}
					}
				}
				else if ($hit == true) $indi_hide[$key."[".get_gedcom_from_id($value["gedfile"])."]"] = 1;
			}
		}
		// Start output here, because from the indi's we may have printed some fams which need the column header.
		print "<br />";
		print "\n\t<div class=\"center\"><table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>";
		if ((count($myindilist)>0)&& (isset($srindi))) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
		if ((count($myfamlist)>0) || (count($printfamname)>0)) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["families"]."</td>";
		if (count($mysourcelist)>0) print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["sources"]."</td>";
		print "</tr>\n\t\t<tr>";
		// Print the indis	
		if (count($printindiname)>0) {	
			uasort($printindiname, "itemsort");
			print "<td class=\"list_value_wrap\"><ul>";
			
/***************************************************** PAGING HERE **********************************************************************/			
			
			//set the total results and only get the results for this page
			$totalResults = count($printindiname);
			$printindiname = getPagedResults($printindiname);
			
			foreach($printindiname as $pkey => $pvalue) {
				$GEDCOM = $pvalue[2];
				if ($GEDCOM != $curged) {
					include(get_privacy_file());
					$curged = $GEDCOM;
				}
				print_list_person($pvalue[1], array(check_NN($pvalue[0]), $pvalue[2]),"", $pvalue[3]);
				print "\n";
			}
			print "\n\t\t</ul>&nbsp;</td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
		}
		
		// Process the fams
		$ctf=count($myfamlist);
		if ($ctf>0 || count($printfamname)>0) {
			$oldged = $GEDCOM;
			$curged = $GEDCOM;

			// Add the facts in $global_facts that should not show
			$skiptagsged = $skiptags;
    		foreach ($global_facts as $gfact => $gvalue) {
	    		if (isset($gvalue["show"])) {
		    		if (($gvalue["show"] < $userlevel)) $skiptagsged .= ", ".$gfact;
	    		}
  		  	}
	
			foreach ($myfamlist as $key => $value) {
				if (count($sgeds) > 1) {
					$GEDCOM = splitkey($key, "ged");
					$key = splitkey($key, "id");
					if ($GEDCOM != $curged) {
						include(get_privacy_file());
						$curged = $GEDCOM;
						// Recalculate the tags to skip
						$skiptagsged = $skiptags;
						foreach ($global_facts as $gfact => $gvalue) {
				    		if (isset($gvalue["show"])) {
	    						if (($gvalue["show"] < $userlevel)) $skiptagsged .= ", ".$gfact;
    						}
						}
  				  	}
				}

				// lets see where the hit is
			    $found = false;
				// If a name is hit, no need to check for tags
				foreach($value["name"] as $nkey => $famname) {
					if ((preg_match("/".$query."/i", $famname)) > 0) {
						$found = true;
						break;
					}
				}
				$hit = false;
		    	// First check if the hit is in the key!
		    	if (($tagfilter == "off") && ($found == false)) {
		    		if (strpos(str2upper($key), str2upper($query)) !== false) {
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
							if (preg_match("/$query/i",$subrec2, $result)>0) {
								$ct = preg_match("/\d.(\S*).*/i",$subrec2, $result2);
								if (($ct > 0) && (!empty($result2[1]))) {
									$hit = true;
									// if the tag can be displayed, do so
									if (strpos($skiptagsged, $result2[1]) === false) $found = true;
								}
							}
							if ($found == true) break;
						}
						if ($found == true) break;
					}
				}
				if ($found == true) {	
					foreach ($value["name"] as $namekey => $famname) {
						$famsplit = preg_split("/(\s\+\s)/", trim($famname));
						// Both names have to have the same direction
						if (hasRTLText($famsplit[0]) == hasRTLText($famsplit[1])) {
							// do not print if the hit only in the second name. We want it first.
							if (!((preg_match("/".$query."/i", $famsplit[0]) == 0) && (preg_match("/".$query."/i", $famsplit[1]) > 0))) {
								$printfamname[]=array(check_NN($famname), $key, get_gedcom_from_id($value["gedfile"]),"");
							}
						}
					}
					$fam_printed[$key."[".$GEDCOM."]"] = "1";
		    	}
				else if ($hit == true) $fam_hide[$key."[".get_gedcom_from_id($value["gedfile"])."]"] = 1;
			}
			uasort($printfamname, "itemsort");
			
/***************************************************** PAGING HERE **********************************************************************/			
			
			//set the total results and only get the results for this page
			if(count($printfamname) > $totalResults)
				$totalResults = count($printfamname);
			$printfamname = getPagedResults($printfamname);

			print "\n\t\t<td class=\"list_value_wrap\"><ul>";
			foreach($printfamname as $pkey => $pvalue) {
				$GEDCOM = $pvalue[2];
				if ($GEDCOM != $curged) {
					include(get_privacy_file());
					$curged = $GEDCOM;
				}
				print_list_family($pvalue[1], array($pvalue[0], $pvalue[2]), "", $pvalue[3]);
				print "\n";
			}
			print "\n\t\t</ul>&nbsp;</td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
		}
		$cts=count($mysourcelist);
		if ($cts>0) {
			uasort($mysourcelist, "itemsort"); 
			$oldged = $GEDCOM;
			$curged = $GEDCOM;

			// Add the facts in $global_facts that should not show
			$skiptagsged = $skiptags;
    		foreach ($global_facts as $gfact => $gvalue) {
	    		if (isset($gvalue["show"])) {
		    		if (($gvalue["show"] < $userlevel)) $skiptagsged .= ", ".$gfact;
	    		}
  		  	}			
			print "\n\t\t<td class=\"list_value_wrap\"><ul>";
			$actualsourcelist = array();
			foreach ($mysourcelist as $key => $value) {
				if (count($sgeds) > 1) {
					$GEDCOM = splitkey($key, "ged");
					$key = splitkey($key, "id");
					if ($curged != $GEDCOM) {
						include(get_privacy_file());
						$curged = $GEDCOM;
						// Recalculate the tags to skip
						$skiptagsged = $skiptags;
						foreach ($global_facts as $gfact => $gvalue) {
				    		if (isset($gvalue["show"])) {
		    					if (($gvalue["show"] < $userlevel)) $skiptagsged .= ", ".$gfact;
	    					}
  					  	}
					}
				}
		    	$found = false;
		    	$hit = false;
		    	// First check if the hit is in the key!
		    	if ($tagfilter == "off") {
		    		if (strpos(str2upper($key), str2upper($query)) !== false) {
			    		$found = true;
			    		$hit = true;
		    		}
	    		}
				if ($found == false) {
					$recs = get_all_subrecords($value["gedcom"], $skiptagsged, false, false, false);
					// Also levels>1 must be checked for tags. This is done below.
					foreach ($recs as $keysr => $subrec) {
						$recs2 = preg_split("/\r?\n/", $subrec);
						foreach ($recs2 as $keysr2 => $subrec2) {
							// There must be a hit in a subrec. If found, check in which tag
							if (preg_match("/$query/i",$subrec2, $result)>0) {
								$ct = preg_match("/\d.(\S*).*/i",$subrec2, $result2);
								if (($ct > 0) && (!empty($result2[1]))) {
									$hit = true;
									// if the tag can be displayed, do so
									if (strpos($skiptagsged, $result2[1]) === false) $found = true;
								}
							}
							if ($found == true) break;
						}
						if ($found == true) break;
					}
				}
				if ($found == true)
					$actualsourcelist[] = $value;
				else if ($hit == true) $source_hide[$key."[".get_gedcom_from_id($value["gedfile"])."]"] = 1;
			}

/***************************************************** PAGING HERE **********************************************************************/			
			
			//set the total results and only get the results for this page
			if($totalResults < count($actualsourcelist)) $totalResults = count($actualsourcelist);
			$actualsourcelist = getPagedResults($actualsourcelist);
			
			foreach($actualsourcelist as $key => $value) {
				print_list_source($key, $value);
			}
			print "\n\t\t</ul>&nbsp;</td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
		}
		print "</tr><tr>\n\t";
		$cti = count($indi_printed);
		$ctf = count($fam_printed);
		if ($cti > 0 || $cts > 0 || $ctf > 0) {
			if (($cti > 0) && (isset($srindi))) {
				print "<td>".$pgv_lang["total_indis"]." ".$cti;
				if (count($indi_private)>0) print "  (".$pgv_lang["private"]." ".count($indi_private).")";
				if (count($indi_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($indi_hide);
				if (count($indi_private)>0 || count($indi_hide)>0) print_help_link("privacy_error_help", "qm");
				print "</td>";
			}
			if ($ctf > 0) {
				print "<td>".$pgv_lang["total_fams"]." ".$ctf;
				if (count($fam_private)>0) print "  (".$pgv_lang["private"]." ".count($fam_private).")";
				if (count($fam_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($fam_hide);
				if (count($fam_private)>0 || count($fam_hide)>0) print_help_link("privacy_error_help", "qm");
				print "</td>";
			}
			if ($cts > 0) {
				print "<td>".$pgv_lang["total_sources"]." ".$cts;
				if (count($source_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($source_hide);
				print "</td>";
			}
			if ($cti > 0 || $cts > 0 || $ctf > 0) print "</tr>\n\t";
		}
		else if (isset($query)) {
			print "<td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br /></td></tr>\n\t\t";
			if (!isset($srindi) && !isset($srfams) && !isset($srsour)) {
				print "<tr><td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_search_for"]."</i><br /></div>\n\t\t";
			}
		}
		print "</table></div>";
	}
	else if (isset($query)) {
		print "<br /><div class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br />\n\t\t";
		if (!isset($srindi) && !isset($srfams) && !isset($srsour)) {
			print "<i>".$pgv_lang["no_search_for"]."</i><br /></div>\n\t\t";
		}
	}

	// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $resultsPerPage results	
	if($resultsPerPage >= 1 && $totalResults > $resultsPerPage) {
		printPageResultsLinks($inputFieldNames);
	}
}

//----- section to search and display results for a Soundex Search
if ($action=="soundex") {
	if ($soundex == "DaitchM")
		DMsoundex("", "closecache");
// 	$query = "";	// Stop function PrintReady from doing strange things to accented names
	if (((!empty($lastname))||(!empty($firstname)) ||(!empty($place))) && (isset($printname))) {
		print "<div class=\"center\"><br />";
		print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>\n\t\t";
		$i=0;
		$ct=count($printname);
		if ($ct > 0) {
			init_list_counters();
			$oldged = $GEDCOM;
			$curged = $GEDCOM;
			$extrafams = false;
			if (count($printfamname)>0) $extrafams = true;
			if ($extrafams) {
				print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
				print "<td class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["families"]."</td>";
			}
			else print "<td colspan=\"2\" class=\"list_label\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" alt=\"\" /> ".$pgv_lang["people"]."</td>";
			print "</tr><tr>\n\t\t<td class=\"list_value_wrap\"><ul>";

/***************************************************** PAGING HERE **********************************************************************/			
			
			//set the total results and only get the results for this page
			if(count($printname) > $totalResults)
					$totalResults = count($printname);
			$nameCount = count($printname);
			$printname = getPagedResults($printname);
			
			foreach ($printname as $key => $pvalue) {
				$GEDCOM = $pvalue[2];
				if ($GEDCOM != $curged) {
					include(get_privacy_file());
					$curged = $GEDCOM;
				}
				print_list_person($pvalue[1], array(check_NN($pvalue[0]), $pvalue[2]), "", $pvalue[3]);
				$indiprinted[$pvalue[1]."[".$pvalue[2]."]"] = 1;
				print "\n";
				if (!$extrafams) {
					if ($i == floor($ct / 2) && $ct>9) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
					$i++;
				}
			}
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include(get_privacy_file());
				$curged = $GEDCOM;
			}
			print "\n\t\t</ul></td>";

			// Start printing the associated fams			
			if ($extrafams) {
				uasort($printfamname, "itemsort");
				print "\n\t\t<td class=\"list_value_wrap\"><ul>";
				if(isset($printfamname))
					$famCount = count($printfamname);
				else
					$famCount = 0;
				
/***************************************************** PAGING HERE **********************************************************************/			

				//set the total results and only get the results for this page
				if(count($printfamname) > $totalResults)
					$totalResults = count($printfamname);			
				$printfamname = getPagedResults($printfamname);
				
				foreach($printfamname as $pkey => $pvalue) {
					$GEDCOM = $pvalue[2];
					if ($GEDCOM != $curged) {
						include(get_privacy_file());
						$curged = $GEDCOM;
					}
					print_list_family($pvalue[1], array($pvalue[0], $pvalue[2]), "", $pvalue[3]);
					print "\n";
				}
				print "\n\t\t</ul>&nbsp;</td>";
				$GEDCOM = $oldged;
				if ($GEDCOM != $curged) {
					include(get_privacy_file());
					$curged = $GEDCOM;
				}
			}

			// start printing the table footer
			print "\n\t\t</tr>\n\t";
			if ($totalResults > 0) {
				print "<tr><td ";
				if ((!$extrafams) && ($ct > 9)) print "colspan=\"2\">";
				else print ">";
				print $pgv_lang["total_indis"]." ".$nameCount;
				if (count($indi_private)>0) print "  (".$pgv_lang["private"]." ".count($indi_private).")";
				if (count($indi_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($indi_hide);
				if (count($indi_private)>0 || count($indi_hide)>0) print_help_link("privacy_error_help", "qm");
				print "</td>";
				if ($extrafams) {
					print "<td>".$pgv_lang["total_fams"]." ".$famCount;
					if (count($fam_private)>0) print "  (".$pgv_lang["private"]." ".count($fam_private).")";
					if (count($fam_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($fam_hide);
					if (count($fam_private)>0 || count($fam_hide)>0) print_help_link("privacy_error_help", "qm");
					print "</td>";
				}
				print "</tr>";
			}
			
		}
		else
			print "<td class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i></td></tr>\n\t\t";
		print "</table></div>";
		}
	
		// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $resultsPerPage results	
		if($resultsPerPage >= 1 && $totalResults > $resultsPerPage) {
			printPageResultsLinks($inputFieldNames);
		}
	}


	// ----- section to search and display results for multisite 
	if ($action=="multisite") 
	{
		// Only Display 5 results per 2 sites if the total results per page is 10
		$sitesChecked = 0;
		$i = 0;
		foreach($Sites as $server) {
		$siteName = "server".$i;
		if(isset($$siteName))
			$sitesChecked++;
		$i++;
		}
		if($sitesChecked >= 1) {
			$resultsPerPage = $resultsPerPage / $sitesChecked;
	
			if(!empty($Sites)&& count($Sites)>0)	
			{
				//AddToLog('About to diplay results');
				$no_results_found = false;
				// Start output here, because from the indi's we may have printed some fams which need the column header.
				print "<br />";
				print "\n\t<div class=\"center\">";
		
				if (isset($Results) && (count($Results)>0)) 
				{
					$multiTotalResults = 0;
					foreach($Results as $key=>$indilist)
					{
						$serviceClient = ServiceClient::getInstance($key);
						$siteName = $serviceClient->getServiceTitle();
						$siteURL = dirname($serviceClient->getURL());
				
						print "<table id=\"multiResultsOutTbl\" class=\"list_table, $TEXT_DIRECTION\" align=\"center\">";
				
						//$indilist = $indilist[0];
						if(isset($indilist) && !empty($indilist->persons)) 
						{
							$displayed_once = false;
							$personlist = $indilist->persons;
			
/***************************************************** PAGING HERE **********************************************************************/			

							//set the total results and only get the results for this page
							$multiTotalResults += count($personlist);
							$totalResults = count($personlist);
							$personlist = getPagedResults($personlist);
						
							foreach($personlist as $index=>$person) 
							{
								//if there is a name to display then diplay it
								if(!empty($person->gedcomName))
								{
									if(!$displayed_once)
									{
										if(!$no_results_found)
										{
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
									print "<b>".$indiName."</b>";
									if(!empty($person->PID))
									{	
										print " (".$person->PID.")";
									}
									if(!empty($person->birthDate)||!empty($person->birthPlace))
									{
										print " -- <i>";
										if(!empty($person->birthDate))
										{	
											print " ".$person->birthDate;
										}
										if(!empty($person->birthPlace))
										{	
											print " ".$person->birthPlace;
										}
										print "</i>";
									}
									print "</a></li></ul></td>";
									
/*******************************  Remote Links Per Result *************************************************/
									if (userCanEdit(getUserName())) {
										print "<td class=\"list_value $TEXT_DIRECTION\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >".
										  "<ul style=\"list-style: NONE\"><li><a href=\"javascript:;\" ".
										  "onclick=\"return open_link('".$key."', '".$person->PID."', '".$indiName."');\">".
										  "<b>".$pgv_lang["title_search_link"]."</b></a></ul></li></td></tr>\n";
									}
								}
							}
							
							print "</table>";
							
							print "\n\t\t&nbsp;</td></tr></table>";
						}
						else {
							print "<td class=\"list_label\" colspan=\"2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >".$pgv_lang["site_list"]."<a href=\"".$siteURL."\" target=\"_blank\">".$siteName."</a>".$pgv_lang["site_had"]."</td></tr>";
							print_r($indilist);
						}
						if ($multiTotalResults > 0)
							print "</tr><tr><td align=\"left\">Total individuals ".$multiTotalResults."</td></tr></table>";
						else
							print "</tr></table>";
					}						
					print "</table></div>";
				}
				if (!$no_results_found && $multiTotalResults == 0 && (isset($multiquery)
					|| isset($name) || isset($birthdate) || isset($birthplace)
					|| isset($deathdate) || isset($deathplace) || isset($gender))) {
					print "<table align=\"center\" \><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".$pgv_lang["no_results"]."</i></b></font><br /></td></tr></table>";
//					AddToLog('No results to display');
				}
			}
		}
		else if ($sitesChecked < 1 && $isPostBack) {
			print "<table align=\"center\" \><tr><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".$pgv_lang["no_search_site"]."</i></b></font><br /></td></tr></table>";
	}
	
	// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $resultsPerPage results	
	if($resultsPerPage >= 1 && $totalResults > $resultsPerPage) {
		printPageResultsLinks($inputFieldNames);
	}
	
	print "</table>";
	}

print "<br /><br /><br />";
print_footer();


/************************************************   Helper Methods ****************************************************************/

/**
 * Function that returns only the results for the current page
 * i.e. if $resultsPageNum == 2 and $resultsPerPage == 10 this
 * function would return results 11 - 20.
 * 
 * @param $results - the original results.  If $results count is less
 * than $resultsPerPage it will simply return $results.
 * @return array - the filtered results i.e. 11-20.
 */
function getPagedResults($results) {
	global $resultsPageNum, $resultsPerPage;
	$len = count($results);
	if ($len <= $resultsPerPage)
		return $results;
	$pagedResults = array();
	$startPosition = $resultsPageNum * $resultsPerPage;
	$endPosition = ($resultsPageNum+1) * $resultsPerPage;
	$i=0;
	if(isset($results) && $len > 0) {
		foreach($results as $key=>$value) {
			if($i >= $startPosition)
				$pagedResults[$key] = $value;
			$i++;
			if($i >= $endPosition)
				break;
		}
		return $pagedResults;
	}
}

/**
 * prints out the paging links for a page with many results i.e.  Result Page:   << 1 2 3 4 5 >>
 * 
 * @param $inputFieldNames - an array of strings representing the names of the variables to include
 * in the query string usually from input values in a form i.e. 'action', 'query', 'showasso' etc. 
 */
function printPageResultsLinks($inputFieldNames) {
	global $resultsPerPage, $totalResults, $resultsPageNum;
	
	print "<br /><table align='center'><tr><td><font size='4'>Result Page: &nbsp;&nbsp;";
	
	// Prints the '<<' linking to the previous page if it's not on the first page
	if($resultsPageNum > 0) {
		print " <a href='";
		printQueryString($inputFieldNames, ($resultsPageNum-1));
		print "'>&lt;&lt;</a>";
	}
	
	// Prints out each number linking to that page number.
	// If it's on that page number it is printed out bold instead of a link
	for($i=1; $i < (($totalResults / $resultsPerPage)+1); $i++) {
		if($i != $resultsPageNum + 1) {
			print " <a href='";
			printQueryString($inputFieldNames, ($i-1));
			print "'>".$i."</a>";
		}
		else
			print " <b>".$i."</b>";
	}
	
	// Prints the '>>' linking to the next page if it's not on the last page
	if($resultsPageNum < (($totalResults / $resultsPerPage)-1)) {
		print " <a href='";
		printQueryString($inputFieldNames, ($resultsPageNum+1));
		print "'>&gt;&gt;</a>";
	}
	
	print "</font></td></tr></table>";
}


/**
 * Prints the query string that goes ... <a href'  HERE   '> for each paging result link
 * 
 * @param $inputFieldNames - an array of strings representing the names of the variables to include
 * in the query string usually from input values in a form i.e. 'action', 'query', 'showasso' etc. 
 * @param $pageNum - the page number to link to in the paged results
 */
function printQueryString($inputFieldNames, $pageNum) {
	$first = true;
	foreach($inputFieldNames as $key=>$value) {
		global $$value;
		if (isset($$value)) {
			if($first) {
				print "?";
				$first = false;
			}
			else
				print "&";
			print "$value=".$$value;
		}
	}
	print "&resultsPageNum=".$pageNum;
}
?>

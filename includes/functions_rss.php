<?php
/**
 * Various functions used to generate the PhpGedView RSS feed.
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
 * @version $Id: functions_rss.php,v 1.6 2006/10/29 16:45:27 lsces Exp $
 * @package PhpGedView
 * @subpackage RSS
 */

if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	print "Now, why would you want to do that.        You're not hacking are you?";
	exit;
}

require("config.php");
require($factsfile["english"]);
if (file_exists($factsfile[$LANGUAGE])) require($factsfile[$LANGUAGE]);

if (isset($_SESSION["timediff"])) $time = time()-$_SESSION["timediff"];
else $time = time();
$day = date("j", $time);
$month = date("M", $time);
$year = date("Y", $time);

/**
 * Returns an ISO8601 formatted date used for the RSS feed
 *
 * @param $time the time in the UNIX time format (milliseconds since Jan 1, 1970)
 * @return SO8601 formatted date in the format of 2005-07-06T20:52:16+00:00
 */
function iso8601_date($time) {
	$tzd = date('O',$time);
	$tzd = $tzd[0] . str_pad((int) ($tzd / 100), 2, "0", STR_PAD_LEFT) .
				   ':' . str_pad((int) ($tzd % 100), 2, "0", STR_PAD_LEFT);
	$date = date('Y-m-d\TH:i:s', $time) . $tzd;
	return $date;
}

/**
 * Returns the upcoming events array used for the RSS feed.
 * Uses configuration set for the blocks. If not configured, it will default to events in the
 * next 30 days, all events for living & and not living people
 *
 * @return the array with upcoming events data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 */
function getUpcomingEvents() {
	global $pgv_lang, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $PGV_BLOCKS;
	global $DAYS_TO_SHOW_LIMIT, $SERVER_URL;

	$dataArray[0] = $pgv_lang["upcoming_events"];
	$dataArray[1] = time();
	$daytext = "<ul>";

	if (empty($config)) $config = $PGV_BLOCKS["print_upcoming_events"]["config"];
	if (!isset($DAYS_TO_SHOW_LIMIT)) $DAYS_TO_SHOW_LIMIT = 30;
	if (isset($config["days"])) $daysprint = $config["days"];
	else $daysprint = 30;
	if (isset($config["filter"])) $filter = $config["filter"];  // "living" or "all"
	else $filter = "all";
	if (isset($config["onlyBDM"])) $onlyBDM = $config["onlyBDM"];  // "yes" or "no"
	else $onlyBDM = "no";

	if ($daysprint < 1) $daysprint = 1;
	if ($daysprint > $DAYS_TO_SHOW_LIMIT) $daysprint = $DAYS_TO_SHOW_LIMIT;  // valid: 1 to limit

	// Look for cached Facts data
	$found_facts = get_event_list();

	// Output starts here

	$OutputDone = false;
	$PrivateFacts = false;
	$lastgid="";

	$dateRangeStart = mktime(0,0,0,$monthtonum[strtolower($month)],$day+1,$year);
	$dateRangeEnd = $dateRangeStart+(60*60*24*$daysprint)-1;

	foreach($found_facts as $key=>$factarray) {
		$anniversaryDate = $factarray[3];
			if ($anniversaryDate>=$dateRangeStart && $anniversaryDate<=$dateRangeEnd) {
				if ($factarray[2]=="INDI") {
					$gid = $factarray[0];
					$factrec = $factarray[1];
					$disp = true;
					if ($filter=="living" and is_dead_id($gid)) $disp = false;
					else if (!displayDetailsByID($gid)) {
						$disp = false;
						$PrivateFacts = true;
					}
					if ($disp) {
						$indirec = find_person_record($gid);
						if ($indirec) {
							$filterev = "all";
							if ($onlyBDM == "yes") $filterev = "bdm";
							$tempText = get_calendar_fact($factrec, "upcoming", $filter, $gid, $filterev);
                        	$text = str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
                        	
							if ($text!="filter") {
								if (FactViewRestricted($gid, $factrec) or $text=="") {
									$PrivateFacts = true;
								} else {
									if ($lastgid!=$gid) {
										if ($lastgid != "") $daytext .= "<br />";
										$name = get_person_name($gid);
										$daytext .= "<li><a href=\"" . $SERVER_URL . "individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
                  						if ($SHOW_ID_NUMBERS) {
	              						    $daytext .= "&nbsp;";
	              						    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
	              						    $daytext .= "(".$gid.")";
	              						    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
                  						}
										$daytext .= "</a> ";
										$lastgid=$gid;
									}
									$daytext .=  $text . "</li>\n";
									$OutputDone = true;
								}
							}
						}
					}
				}

				if ($factarray[2]=="FAM") {
					$gid = $factarray[0];
					$factrec = $factarray[1];

					$disp = true;
					if ($filter=="living") {
						$parents = find_parents($gid);
						if (is_dead_id($parents["HUSB"])) $disp = false;
						else if (!displayDetailsByID($parents["HUSB"])) {
							$disp = false;
							$PrivateFacts = true;
						}
						if ($disp) {
							if (is_dead_id($parents["WIFE"])) $disp = false;
							else if (!displayDetailsByID($parents["WIFE"])) {
							$disp = false;
							$PrivateFacts = true;
						}
					}
				} else if (!displayDetailsByID($gid, "FAM")) {
					$disp = false;
					$PrivateFacts = true;
				}
				if ($disp) {
					$famrec = find_family_record($gid);
					if ($famrec) {
						$name = get_family_descriptor($gid);
						$filterev = "all";
						if ($onlyBDM == "yes") $filterev = "bdm";
						$tempText = get_calendar_fact($factrec, "upcoming", $filter, $gid, $filterev);
						$text = str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
						if ($text!="filter" and strpos($famrec, "1 DIV")===false) {
							if (FactViewRestricted($gid, $factrec) or $text=="") {
								$PrivateFacts = true;
							} else {
								if ($lastgid!=$gid) {
									if ($lastgid != "") $daytext .= "<br />";
									$daytext .=  "<li><a href=\"" . $SERVER_URL . "family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
                  					if ($SHOW_ID_NUMBERS) {
	              					    $daytext .= "&nbsp;";
	              					    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
	              					    $daytext .= "(".$gid.")";
	              					    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
                  						}
									$daytext .=  "</a> ";
									$lastgid=$gid;
								}
								$daytext .=  $text . "</li>\n";
								$OutputDone = true;
							}
						}
					}
				}
			}
		}
	}
	$daytext .= "</ul>";

	if ($PrivateFacts) {    // Facts were found but not printed for some reason
		$pgv_lang["global_num1"] = $daysprint;
		$Advisory = "no_events_privacy";
		if ($OutputDone) $Advisory = "more_events_privacy";
		if ($daysprint==1) $Advisory .= "1";
		$daytext .=  "<b>";
		$daytext .=  print_text($Advisory, 0, 1);
		$daytext .=  "</b>";
	} else if (!$OutputDone) {    // No Facts were found
		$pgv_lang["global_num1"] = $daysprint;
		$Advisory = "no_events_" . $filter;
		if ($daysprint==1) $Advisory .= "1";
		$daytext .=  "<b>";
		$daytext .=  print_text($Advisory, 0, 1);
		$daytext .=  "</b>";
	}

	$daytext = str_replace(array("<br />", "<ul></ul>", " </a>"), array(" ", "", "</a>"), $daytext);
	$daytext = strip_tags($daytext, '<a><ul><li><b><span>');
	$dataArray[2]  = $daytext;
	return $dataArray;
}


/**
 * Returns the today's events array used for the RSS feed
 *
 * @return the array with todays events data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 */
function getTodaysEvents() {
	global $pgv_lang, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $PGV_BLOCKS;
	global $SERVER_URL;
	global $DAYS_TO_SHOW_LIMIT;

	$dataArray[0] = $pgv_lang["on_this_day"];
	$dataArray[1] = time();

	if (empty($config)) $config = $PGV_BLOCKS["print_todays_events"]["config"];
	if (isset($config["filter"])) $filter = $config["filter"];  // "living" or "all"
	else $filter = "all";
	if (isset($config["onlyBDM"])) $onlyBDM = $config["onlyBDM"];  // "yes" or "no"
	else $onlyBDM = "no";

	// Look for cached Facts data
	$found_facts = get_event_list();

	//-- Start output
	$OutputDone = false;
	$PrivateFacts = false;
	$daytext = "<ul>";
	$lastgid="";
	$dateRangeStart = mktime(0,0,0,$monthtonum[strtolower($month)],$day,$year);
	$dateRangeEnd = $dateRangeStart+(60*60*24)-1;

	foreach($found_facts as $key=>$factarray) {
		$anniversaryDate = $factarray[3];
		if ($anniversaryDate>=$dateRangeStart && $anniversaryDate<=$dateRangeEnd) {
			if ($factarray[2]=="INDI") {
				$gid = $factarray[0];
				$factrec = $factarray[1];
				$disp = true;
				if ($filter=="living" and is_dead_id($gid)){
					$disp = false;
				} else if (!displayDetailsByID($gid)) {
					$disp = false;
					$PrivateFacts = true;
				}
				if ($disp) {
					$indirec = find_person_record($gid);
					if ($indirec) {
						$filterev = "all";
						if ($onlyBDM == "yes") $filterev = "bdm";
						$tempText = get_calendar_fact($factrec, "today", $filter, $gid, $filterev);
						$text = str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
						if ($text!="filter") {
							if (FactViewRestricted($gid, $factrec) or $text=="") {
								$PrivateFacts = true;
							} else {
								if ($lastgid!=$gid) {
									$name = get_person_name($gid);
									$daytext .= "<li><a href=\"".$SERVER_URL ."individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
                  					if ($SHOW_ID_NUMBERS) {
	              					    $daytext .= "&nbsp;";
	              					    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
	              					    $daytext .= "(".$gid.")";
	              					    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
                  					}
									$daytext .=  "</a> ";
									$lastgid=$gid;
								}
								$daytext .=  $text. "</li>\n";
								$OutputDone = true;
							}
						}
					}
				}
			}

			if ($factarray[2]=="FAM") {
				$gid = $factarray[0];
				$factrec = $factarray[1];

				$disp = true;
				if ($filter=="living") {
					$parents = find_parents_in_record($gid["gedcom"]);
					if (is_dead_id($parents["HUSB"])){
						$disp = false;
					} else if (!displayDetailsByID($parents["HUSB"])) {
						$disp = false;
						$PrivateFacts = true;
					}
					if ($disp) {
						if (is_dead_id($parents["WIFE"])) $disp = false;
						else if (!displayDetailsByID($parents["WIFE"])) {
							$disp = false;
							$PrivateFacts = true;
						}
					}
				} else if (!displayDetailsByID($gid, "FAM")) {
					$disp = false;
					$PrivateFacts = true;
				}
				if($disp) {
					$famrec = find_family_record($gid);
					if ($famrec) {
						$name = get_family_descriptor($gid);
						$filterev = "all";
						if ($onlyBDM == "yes") $filterev = "bdm";
						$tempText = get_calendar_fact($factrec, "today", $filter, $gid, $filterev);
						$text = str_replace('href="calendar.php', 'href="'.$SERVER_URL.'calendar.php', $tempText);
						//if ($text!="filter") {
						if ($text!="filter" and strpos($famrec, "1 DIV")===false) {
							if (FactViewRestricted($gid, $factrec) or $text=="") {
								$PrivateFacts = true;
							} else {
								if ($lastgid!=$gid) {
									$daytext .=  "<li><a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
                  					if ($SHOW_ID_NUMBERS) {
	              					    $daytext .= "&nbsp;";
	              					    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
	              					    $daytext .= "(".$gid.")";
	              					    if ($TEXT_DIRECTION=="rtl") $daytext .= "&rlm;";
                  					}
									$daytext .=  "</a> ";
									$lastgid=$gid;
								}
								$daytext .=  $text . "</li>\n";
								$OutputDone = true;
							}
						}
					}
				}
			}
		}
	}
	$daytext .= "</ul>";

	if ($PrivateFacts) {    // Facts were found but not printed for some reason
		$Advisory = "none_today_privacy";
		if ($OutputDone) $Advisory = "more_today_privacy";
		$daytext .=  "<b>";
		$daytext .= print_text($Advisory, 0, 1);
		$daytext .=  "</b>";
	} else if (!$OutputDone) {    // No Facts were found
		$Advisory = "none_today_" . $filter;
		$daytext .=  "<b>";
		$daytext .= print_text($Advisory, 0, 1);
		$daytext .=  "</b>";
	}

	$daytext = str_replace(array("<br />", "<ul></ul>", " </a>"), array(" ", "", "</a>"), $daytext);
	//$daytext = str_replace(array("<ul></ul>"), array(""), $daytext);
	$daytext = strip_tags($daytext, '<a><ul><li><b><span>');
	$dataArray[2]  = $daytext;
	return $dataArray;
}

/**
 * Returns the GEDCOM stats.
 *
 * @return the array with GEDCOM stats data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO does not print the family with most children due to the embedded html in that function.
 */
function getGedcomStats() {
	global $pgv_lang, $day, $month, $year, $PGV_BLOCKS, $GEDCOM, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $command, $COMMON_NAMES_THRESHOLD, $SERVER_URL, $RTLOrd, $gBitSystem;

	if (empty($config)) $config = $PGV_BLOCKS["print_gedcom_stats"]["config"];
	if (!isset($config['stat_indi'])) $config = $PGV_BLOCKS["print_gedcom_stats"]["config"];

	$data = "";
	$dataArray[0] = $pgv_lang["gedcom_stats"] . " - " . $GEDCOMS[$GEDCOM]["title"];

	$head = find_gedcom_record("HEAD");
	$ct=preg_match("/1 SOUR (.*)/", $head, $match);
	if ($ct>0) {
		$softrec = get_sub_record(1, "1 SOUR", $head);
		$tt= preg_match("/2 NAME (.*)/", $softrec, $tmatch);
		if ($tt>0) $title = trim($tmatch[1]);
		else $title = trim($match[1]);
		if (!empty($title)) {
			$text = strip_tags(str_replace(array("#SOFTWARE#", "#CREATED_SOFTWARE#"), $title, $pgv_lang["gedcom_created_using"]));
			$tt = preg_match("/2 VERS (.*)/", $softrec, $tmatch);
			if ($tt>0) $version = trim($tmatch[1]);
			else $version="";
			$text = strip_tags(str_replace(array("#VERSION#", "#CREATED_VERSION#"), $version, $text));
			$data .= $text;
		}
	}
	$ct=preg_match("/1 DATE (.*)/", $head, $match);
	if ($ct>0) {
		$date = trim($match[1]);
		$dataArray[1] = strtotime($date);

		if (empty($title)){
			$data .= str_replace(array("#DATE#", "#CREATED_DATE#"), get_changed_date($date), $pgv_lang["gedcom_created_on"]);
		} else {
			$data .= str_replace(array("#DATE#", "#CREATED_DATE#"), get_changed_date($date), $pgv_lang["gedcom_created_on2"]);
		}
	}

	$data .= " <br />\n";
	if (!isset($config["stat_indi"]) || $config["stat_indi"]=="yes"){
		$data .= "<a href=\"" . $SERVER_URL . "indilist.php?surname_sublist=no\">" . get_list_size("indilist"). "</a> - " .$pgv_lang["stat_individuals"]."<br />";
	}
	if (!isset($config["stat_fam"]) || $config["stat_fam"]=="yes"){
		$data .= "<a href=\"" .$SERVER_URL . "famlist.php\">" . get_list_size("famlist"). "</a> - ".$pgv_lang["stat_families"]."<br />";
	}
	if (!isset($config["stat_sour"]) || $config["stat_sour"]=="yes"){
		$data .= "<a href=\"" . $SERVER_URL . "sourcelist.php\">" . get_list_size("sourcelist")."</a> - ".$pgv_lang["stat_sources"]."<br /> ";
	}
	if (!isset($config["stat_other"]) || $config["stat_other"]=="yes"){
		$data .= get_list_size("otherlist")." - ".$pgv_lang["stat_other"]."<br />";
	}

	if (!isset($config["stat_first_birth"]) || $config["stat_first_birth"]=="yes") {
		// NOTE: Get earliest birth year
		$sql = "select min(d_year) as lowyear from ".PHPGEDVIEW_DB_PREFIX."dates where d_file = '".$GEDCOMS[$GEDCOM]["id"]."' and d_fact = 'BIRT' and d_year != '0' and d_type is null";
		$tempsql = $gBitSystem->mDb->query($sql);
		$res =& $tempsql;
		$row =& $res->FetchRow();
		$data .= $pgv_lang["stat_earliest_birth"]." - ".$row["lowyear"]."<br />\n";
	}
	if (!isset($config["stat_last_birth"]) || $config["stat_last_birth"]=="yes") {
		// NOTE: Get the latest birth year
		$sql = "select max(d_year) as highyear from ".PHPGEDVIEW_DB_PREFIX."dates where d_file = '".$GEDCOMS[$GEDCOM]["id"]."' and d_fact = 'BIRT' and d_type is null";
		$tempsql = $gBitSystem->mDb->query($sql);
		$res =& $tempsql;
		$row =& $res->FetchRow();
		$data .= $pgv_lang["stat_latest_birth"] . " - " .$row["highyear"]."<br />\n";
	}

	if (!isset($config["stat_long_life"]) || $config["stat_long_life"]=="yes") {
		//-- get the person who lived the longest
		$sql = "select death.d_year-birth.d_year as age, death.d_gid from ".PHPGEDVIEW_DB_PREFIX."dates as death, ".PHPGEDVIEW_DB_PREFIX."dates as birth where birth.d_gid=death.d_gid AND death.d_file='".$GEDCOMS[$GEDCOM]["id"]."' and birth.d_file=death.d_file AND birth.d_fact='BIRT' and death.d_fact='DEAT' AND birth.d_year>0 and death.d_year>0 and birth.d_type is null and death.d_type is null ORDER BY age DESC limit 1";
		$tempsql = $gBitSystem->mDb->query($sql);
		$res =& $tempsql;
		$row =& $res->FetchRow();
		$res->free();
		$data .= $pgv_lang["stat_longest_life"]. " - " . $row['age']."<br />\n";
	}
	if (!isset($config["stat_avg_life"]) || $config["stat_avg_life"]=="yes") {
		//-- avg age at death
		$sql = "select avg(death.d_year-birth.d_year) as age from ".PHPGEDVIEW_DB_PREFIX."dates as death, ".PHPGEDVIEW_DB_PREFIX."dates as birth where birth.d_gid=death.d_gid AND death.d_file='".$GEDCOMS[$GEDCOM]["id"]."' and birth.d_file=death.d_file AND birth.d_fact='BIRT' and death.d_fact='DEAT' AND birth.d_year>0 and death.d_year>0 and birth.d_type is null and death.d_type is null";
		$tempsql = $gBitSystem->mDb->query($sql);
		if ($tempsql) {
			$res =& $tempsql;
			$row =& $res->fetchRow();
			$data .= $pgv_lang["stat_avg_age_at_death"] . " - " .sprintf("%d", $row["0"]) . "<br />\n";
		}
	}

	//TODO: print_list_family is not sutible for use here due to its output of HTML
	/*if (!isset($config["stat_most_chil"]) || $config["stat_most_chil"]=="yes") {
		//-- most children
		$sql = "SELECT f_numchil, f_id FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file='".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY f_numchil DESC LIMIT 10";
		//print $sql;
		$tempsql = $gBitSystem->mDb->query($sql);
		if ($tempsql) {
			$res =& $tempsql;
			$row =& $res->FetchRow();
			$res->free();
			$data .= $pgv_lang["stat_most_children"] . $row['f_numchil'];
			if (displayDetailsById($row['f_id'], "FAM")) {
				print_list_family($row['f_id'], array(get_family_descriptor($row['f_id']), $GEDCOM), false, "", false);
			}
		}
	}*/

	if (!isset($config["stat_avg_chil"]) || $config["stat_avg_chil"]=="yes") {
		//-- avg number of children
		$sql = "SELECT avg(f_numchil) from ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file='".$GEDCOMS[$GEDCOM]["id"]."'";
		$tempsql = $gBitSystem->mDb->query($sql);
		if ($tempsql) {
			$res =& $tempsql;
			$row =& $res->FetchRow();
			$data .= $pgv_lang["stat_average_children"]. " - " . sprintf("%.2f", $row["0"]) . "<br />\n";
		}
	}



	if (!isset($config["show_common_surnames"]) || $config["show_common_surnames"]=="yes") {
		$surnames = get_common_surnames_index($GEDCOM);
		if (count($surnames)>0) {
			$data .="<b>" . $pgv_lang["common_surnames"]."</b><br />";
			$i=0;
			foreach($surnames as $indexval => $surname) {
				if ($i>0) $data .= ", ";
				if (in_array(ord(substr($surname["name"], 0, 2)),$RTLOrd)) {
					//if (ord(substr($surname["name"], 0, 2),$RTLOrd)){}
					$data .= "<a href=\"" . $SERVER_URL ."indilist.php?surname=".rawurlencode($surname["name"])."\">".$surname["name"]."</a>";
				}
				else $data .= "<a href=\"" . $SERVER_URL ."indilist.php?surname=".$surname["name"]."\">".$surname["name"]."</a>";
				$i++;
			}
		}
	}

	$data = strip_tags($data, '<a><br><b>');
	$dataArray[2] = $data;
	return $dataArray;
}

/**
 * Returns the top 10 surnames
 *
 * @return the array with the top 10 surname data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO Possibly turn list into a <ul> list
 */
function getTop10Surnames() {
	global $pgv_lang, $GEDCOM,$SERVER_URL, $TEXT_DIRECTION;
	global $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $COMMON_NAMES_THRESHOLD, $PGV_BLOCKS, $command, $PGV_IMAGES, $PGV_IMAGE_DIR;

	$data = "";
	$dataArray = array();


	function top_surname_sort($a, $b) {
		return $b["match"] - $a["match"];
	}

	if (empty($config)) $config = $PGV_BLOCKS["print_block_name_top10"]["config"];

	if (isset($config["num"])) $numName = $config["num"];
	else $numName = 10;

	$dataArray[0] = str_replace("10", $numName, $pgv_lang["block_top10_title"]);
	$dataArray[1] = time();

	//-- cache the result in the session so that subsequent calls do not have to
	//-- perform the calculation all over again.
	if (isset($_SESSION["top10"][$GEDCOM])) {
		$surnames = $_SESSION["top10"][$GEDCOM];
	}
	else {
		$surnames = get_top_surnames($numName);

		// Insert from the "Add Names" list if not already in there
		if ($COMMON_NAMES_ADD != "") {
			$addnames = preg_split("/[,;] /", $COMMON_NAMES_ADD);
			if (count($addnames)==0) $addnames[] = $COMMON_NAMES_ADD;
			foreach($addnames as $indexval => $name) {
				//$surname = str2upper($name);
				$surname = $name;
				if (!isset($surnames[$surname])) {
					$surnames[$surname]["name"] = $name;
					$surnames[$surname]["match"] = $COMMON_NAMES_THRESHOLD;
				}
			}
		}

		// Remove names found in the "Remove Names" list
		if ($COMMON_NAMES_REMOVE != "") {
			$delnames = preg_split("/[,;] /", $COMMON_NAMES_REMOVE);
			if (count($delnames)==0) $delnames[] = $COMMON_NAMES_REMOVE;
			foreach($delnames as $indexval => $name) {
				//$surname = str2upper($name);
				$surname = $name;
				unset($surnames[$surname]);
			}
		}

		// Sort the list and save for future reference
		uasort($surnames, "top_surname_sort");
		$_SESSION["top10"][$GEDCOM] = $surnames;
	}
	if (count($surnames)>0) {
		$i=0;
		foreach($surnames as $indexval => $surname) {
			if (stristr($surname["name"], "@N.N")===false) {
				$data .= "<a href=\"" . $SERVER_URL ."indilist.php?surname=".rawurlencode($surname["name"])."\">".PrintReady($surname["name"])."</a> ";
				if ($TEXT_DIRECTION=="rtl") $data .= "&rlm;[&rlm;".$surname["match"]."&rlm;]&rlm;<br />";
				else $data .= "[".$surname["match"]."]<br />";
				$i++;
				if ($i>=$numName) break;
			}
		}
	}
	$dataArray[2] = $data;
	return $dataArray;
}

/**
 * Returns the recent changes list for the RSS feed
 *
 * @return the array with recent changes data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO merge many changes from recent changes block
 * @TODO use date of most recent change instead of curent time
 */
function getRecentChanges() {
	global $pgv_lang, $factarray, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $REGEXP_DB, $DEBUG, $ASC, $IGNORE_FACTS, $IGNORE_YEAR, $TOTAL_QUERIES, $LAST_QUERY, $PGV_BLOCKS, $SHOW_SOURCES;
    global $objectlist, $SERVER_URL;

	if ($command=="user") $filter = "living";
	else $filter = "all";

	if (empty($config)) $config = $PGV_BLOCKS["print_recent_changes"]["config"];
	$configDays = 30;
	if(isset($config["days"]) && $config["days"] > 0) $configDays = $config["days"];
	if (isset($config["hide_empty"])) $HideEmpty = $config["hide_empty"];
	else $HideEmpty = "no";

	$dataArray[0] = $pgv_lang["recent_changes"];
	$dataArray[1] = time();//FIXME - get most recent change time

	$recentText = "<ul>";

	$action = "today";
	$found_facts = array();
	$monthstart = mktime(1,0,0,$monthtonum[strtolower($month)],$day,$year);
	$mmon2 = date("m", $monthstart-(60*60*24*$configDays));
	$mday2 = date("d", $monthstart-(60*60*24*$configDays));
	$myear2 = date("Y", $monthstart-(60*60*24*$configDays));
	$changes = get_recent_changes($mday2, $mmon2, $myear2);

	if (count($changes)>0) {
		$found_facts = array();
		$last_total = $TOTAL_QUERIES;
		foreach($changes as $id=>$change) {
			$gid = $change['d_gid'];
			$gedrec = find_gedcom_record($change['d_gid']);
			if (empty($gedrec)) $gedrec = find_record_in_file($change['d_gid']);

			if (!empty($gedrec)) {
				$type = "INDI";
				$match = array();
				$ct = preg_match("/0 @.*@ (\w*)/", $gedrec, $match);
				if ($ct>0) $type = trim($match[1]);
				$disp = true;
				switch($type) {
					case 'INDI':
						if (($filter=="living")&&(is_dead_id($gid)==1)) $disp = false;
						else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid);
						break;
					case 'FAM':
						if ($filter=="living") {
							$parents = find_parents_in_record($gedrec);
							if (is_dead_id($parents["HUSB"])==1) $disp = false;
							else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["HUSB"]);
							if ($disp) {
								if (is_dead_id($parents["WIFE"])==1) $disp = false;
								else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["WIFE"]);
							}
						}
						else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid, "FAM");
						break;
					default:
						$disp = displayDetailsByID($gid, $type);
						break;
				}
				if ($disp) {
					$factrec = get_sub_record(1, "1 CHAN", $gedrec);
					$found_facts[] = array($gid, $factrec, $type);
				}
			}
		}
	}

// Start output
	if (count($found_facts)==0 and $HideEmpty=="yes") return false;
//		Print block content
	$pgv_lang["global_num1"] = $configDays;		// Make this visible
	if (count($found_facts)==0) {
		print_text("recent_changes_none", 0, 1);
	} else {
		print_text("recent_changes_some", 0, 1);
		$lastgid="";
		foreach($found_facts as $index=>$factarr) {
			if ($factarr[2]=="INDI") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid)) {
					$indirec = find_person_record($gid);
					if ($indirec) {
						if ($lastgid!=$gid) {
							$name = check_NN(get_sortable_name($gid));
							$recentText .= "<li><a href=\"" . $SERVER_URL . "individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
							if ($SHOW_ID_NUMBERS) {
								$recentText .= "&nbsp;&nbsp;";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
								$recentText .= "(".$gid.")";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
							}
							$recentText .= "</a> ";
							$lastgid=$gid;
						}
						$recentText .= $factarray["CHAN"];
						$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
						if ($ct>0) {
								$recentText .= " - " . get_changed_date($match[1]);
								$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
								if ($tt>0) {
									$recentText .= " - ".$match[1];
								}
                    	
						}
						$recentText .=  "</li>\n";
					}
				}
			}

			if ($factarr[2]=="FAM") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "FAM")) {
					$famrec = find_family_record($gid);
					if ($famrec) {
						$name = get_family_descriptor($gid);
						if ($lastgid!=$gid) {
							$recentText .= "<li><a href=\"" .$SERVER_URL . "family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
							if ($SHOW_FAM_ID_NUMBERS) {
								$recentText .= "&nbsp;&nbsp;";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
								$recentText .= "(".$gid.")";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
							}
							$recentText .= "</a> ";
							$lastgid=$gid;
						}
						$recentText .= $factarray["CHAN"];
						$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
						if ($ct>0) {
							$recentText .= " - " .get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
								$recentText .= " - ".$match[1];
							}
                	
						}
						$recentText .=  "</li>\n";
					}
				}
			}

			if ($factarr[2]=="SOUR") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "SOUR")) {
					$sourcerec = find_source_record($gid);
					if ($sourcerec) {
						$name = get_source_descriptor($gid);
						if ($lastgid!=$gid) {
							$recentText .= "<li><a href=\"" . $SERVER_URL . "source.php?sid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
							if ($SHOW_FAM_ID_NUMBERS) {
								$recentText .= "&nbsp;&nbsp;";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
								$recentText .= "(".$gid.")";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
							}
							$recentText .= "</a> ";
							$lastgid=$gid;
						}
						$recentText .= $factarray["CHAN"];
						$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
						if ($ct>0) {
							$recentText .= " - ".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
								$recentText .= " - ".$match[1];
							}
                	
						}
						$recentText .=  "</li>\n";
					}
				}
			}

			if ($factarr[2]=="REPO") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "REPO")) {
					$reporec = find_repo_record($gid);
					if ($reporec) {
						$name = get_repo_descriptor($gid);
						if ($lastgid!=$gid) {
							$recentText .= "<li><a href=\"" . $SERVER_URL . "repo.php?rid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
							if ($SHOW_FAM_ID_NUMBERS) {
								$recentText .= "&nbsp;&nbsp;";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
								$recentText .= "(".$gid.")";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
							}
							$recentText .= "</a> ";
							$lastgid=$gid;
						}
						$recentText .= $factarray["CHAN"];
						$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
						if ($ct>0) {
							$recentText .= " - ".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
								$recentText .=  " - ".$match[1];
							}
                	
						}
						$recentText .=  "</li>\n";
					}
				}
			}
			if ($factarr[2]=="OBJE") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "OBJE")) {
					$mediarec = find_media_record($gid);
					if ($mediarec) {
						if (isset($objectlist[$gid]["title"]) && $objectlist[$gid]["title"] != "") $title=$objectlist[$gid]["title"];
						else $title = $objectlist[$gid]["file"];
						$SearchTitle = preg_replace("/ /","+",$title);
						if ($lastgid!=$gid) {
 							$recentText .= "<li><a href=\"" . $SERVER_URL . "medialist.php?action=filter&amp;search=yes&amp;filter=$SearchTitle&amp;ged=".$GEDCOM."\"><b>".PrintReady($title)."</b>";
							if ($SHOW_FAM_ID_NUMBERS) {
								$recentText .= "&nbsp;&nbsp;";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
								$recentText .= "(".$gid.")";
								if ($TEXT_DIRECTION=="rtl") $recentText .= "&rlm;";
							}
							$recentText .= "</a> ";
							$lastgid=$gid;
						}
						$recentText .= $factarray["CHAN"];
						$ct = preg_match("/\d DATE (.*)/", $factrec, $match);
						if ($ct>0) {
							$recentText .= " - ".get_changed_date($match[1]);
							$tt = preg_match("/3 TIME (.*)/", $factrec, $match);
							if ($tt>0) {
								$recentText .= " - ".$match[1];
							}
                    	
						}
						$recentText .= "</li>\n";
					}
				}
			}
		}

	}
	$recentText .= "</ul>";

	$recentText = str_replace(array("<br />", "<ul></ul>", " </a>"), array(" ", "", "</a>"), $recentText);
	$recentText = strip_tags($recentText, '<a><ul><li><b><span>');

	$dataArray[2] = $recentText;
	return $dataArray;
}


?>
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
 * @version $Id: functions_rss.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 * @package PhpGedView
 * @subpackage RSS
 */

if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
        print "Now, why would you want to do that.        You're not hacking are you?";
        exit;
}

require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);

if (isset($_SESSION["timediff"])) $time = time()-$_SESSION["timediff"];
else $time = time();
$day = date("j", $time);
$month = date("M", $time);
$year = date("Y", $time);

$PGV_BLOCKS["print_recent_changes"]["config"] = array("days"=>30, "hide_empty"=>"no");
$PGV_BLOCKS["print_upcoming_events"]["config"] = array("days"=>30, "filter"=>"all", "onlyBDM"=>"no");


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
 * Returns the upcoming events array used for the RSS feed
 *
 * @return the array with upcoming events data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO does not pick up the upcoming events block config and always shows 30 days of data.
 */
function getUpcomingEvents() {
	global $pgv_lang, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $REGEXP_DB, $DEBUG, $ASC, $IGNORE_FACTS, $IGNORE_YEAR, $TOTAL_QUERIES, $LAST_QUERY, $PGV_BLOCKS;
	global $INDEX_DIRECTORY, $USE_RTL_FUNCTIONS,$SERVER_URL;
	global $DAYS_TO_SHOW_LIMIT;


	$dataArray[0] = $pgv_lang["upcoming_events"];
	$dataArray[1] = time();

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

	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL";	// These are always excluded

	$daytext = "<ul>";
	$action = "upcoming";

	$monthstart = mktime(1,0,0,$monthtonum[strtolower($month)],$day,$year);
	$mmon = strtolower(date("M", $monthstart));
	$mmon3 = strtolower(date("M", $monthstart+(60*60*24*$DAYS_TO_SHOW_LIMIT)));
	$mmon2 = $mmon3;
	if ($mmon3=="mar" && $mmon=="jan") $mmon2="feb";

	// Look for cached Facts data
	$found_facts = array();
	$cache_load = false;
	if ((file_exists($INDEX_DIRECTORY.$GEDCOM."_upcoming.php"))&&(!isset($DEBUG)||($DEBUG==false))) {
    	$modtime = filemtime($INDEX_DIRECTORY.$GEDCOM."_upcoming.php");
    	$mday = date("d", $modtime);
    	if ($mday==$day) {
			$fp = fopen($INDEX_DIRECTORY.$GEDCOM."_upcoming.php", "rb");
			$fcache = fread($fp, filesize($INDEX_DIRECTORY.$GEDCOM."_upcoming.php"));
			fclose($fp);
			$found_facts = unserialize($fcache);
			$cache_load = true;
		}
	}

	// Search database for raw Indi data if no cache was found
	if (!$cache_load) {
		$dayindilist = array();
		$dayindilist = search_indis_dates("", $mmon);
		if ($mmon!=$mmon2) {
			$dayindilist2 = search_indis_dates("", $mmon2);
			$dayindilist = pgv_array_merge($dayindilist, $dayindilist2);
		}
		if ($mmon2!=$mmon3) {
			$dayindilist2 = search_indis_dates("", $mmon3);
			$dayindilist = pgv_array_merge($dayindilist, $dayindilist2);
		}

		// Search database for raw Family data if no cache was found
		$dayfamlist = array();
		$dayfamlist = search_fams_dates("", $mmon);
		if ($mmon!=$mmon2) {
			$dayfamlist2 = search_fams_dates("", $mmon2);
			$dayfamlist = pgv_array_merge($dayfamlist, $dayfamlist2);
		}
		if ($mmon2!=$mmon3) {
			$dayfamlist2 = search_fams_dates("", $mmon3);
			$dayfamlist = pgv_array_merge($dayfamlist, $dayfamlist2);
		}

		// Apply filter criteria and perform other transformations on the raw data
		$found_facts = array();
		foreach($dayindilist as $gid=>$indi) {
			$facts = get_all_subrecords($indi["gedcom"], $skipfacts, false, false, false);
			foreach($facts as $key=>$factrec) {
				$date = 0; //--- MA @@@
				$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $factrec, $match);
				if ($hct>0) {
					if ($USE_RTL_FUNCTIONS) {
						$dct = preg_match("/2 DATE (.+)/", $factrec, $match);
						$hebrew_date = parse_date(trim($match[1]));
						$date = jewishGedcomDateToCurrentGregorian($hebrew_date);
					}
				} else {
					$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
					if ($ct>0) $date = parse_date(trim($match[1]));
				}
				if ($date != 0) {
					//if ($date[0]["day"] == "" and ($date[0]["mon"] == $mmon or $date[0]["mon"] == $mmon2 or $date[0]["mon"] == $mmon3)) {
					//$datestamp = $monthstart + 60*60*24;
					//} else {

					$datestamp = mktime(1,0,0,$date[0]["mon"],$date[0]["day"],$year);
					//}
					if (($datestamp > $monthstart) && ($datestamp<=$monthstart+(60*60*24*$DAYS_TO_SHOW_LIMIT))) {
						$found_facts[] = array($gid, $factrec, "INDI", $datestamp);
					}
				}
			}
		}
		foreach($dayfamlist as $gid=>$fam) {
			$facts = get_all_subrecords($fam["gedcom"], $skipfacts, false, false, false);
			foreach($facts as $key=>$factrec) {
				$date = 0; //--- MA @@@
				$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $factrec, $match);
				if ($hct>0) {
					if ($USE_RTL_FUNCTIONS) {
						$dct = preg_match("/2 DATE (.+)/", $factrec, $match);
						$hebrew_date = parse_date(trim($match[1]));
						$date = jewishGedcomDateToCurrentGregorian($hebrew_date);
					}
				} else {
					$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
					if ($ct>0) $date = parse_date(trim($match[1]));
				}
				if ($date != 0) {
					//if ($date[0]["day"] == "" and ($date[0]["mon"] == $mmon or $date[0]["mon"] == $mmon2 or $date[0]["mon"] == $mmon3)) {
					//$datestamp = $monthstart + 60*60*24;
					//} else {
					$datestamp = mktime(1,0,0,$date[0]["mon"],$date[0]["day"],$year);
					//}
					if (($datestamp > $monthstart) && ($datestamp<=$monthstart+(60*60*24*$DAYS_TO_SHOW_LIMIT))) {
						$found_facts[] = array($gid, $factrec, "FAM", $datestamp);
					}
				}
			}
		}
		// Cache the Facts data just found
		if (is_writable($INDEX_DIRECTORY)) {
			$fp = fopen($INDEX_DIRECTORY."/".$GEDCOM."_upcoming.php", "wb");
			fwrite($fp, serialize($found_facts));
			fclose($fp);
		}
	}

	// Output starts here
	$ASC = 0;
	$IGNORE_FACTS = 1;
	$IGNORE_YEAR = 1;
	uasort($found_facts, "compare_facts");

	$OutputDone = false;
	$PrivateFacts = false;
	$lastgid="";
	foreach($found_facts as $key=>$factarray) {
		$datestamp = $factarray[3];
		if (($datestamp>$monthstart) && ($datestamp<=$monthstart+(60*60*24*$daysprint))) {
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
					$filterev = "all";
					if ($onlyBDM == "yes") $filterev = "bdm";
					$tempText = get_calendar_fact($factrec, $action, $filter, $gid, $filterev);
					$text= preg_replace("/href=\"calendar\.php/", "href=\"".$SERVER_URL."calendar.php", $tempText);
					if ($text!="filter") {
						if (FactViewRestricted($gid, $factrec) or $text=="") {
							$PrivateFacts = true;
						} else {
							if ($lastgid!=$gid) {
								$name = check_NN(get_sortable_name($gid));
								$daytext .= "<li><a href=\"".$SERVER_URL ."individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
								if ($SHOW_ID_NUMBERS) {
									if ($TEXT_DIRECTION=="ltr"){
										$daytext .=  " &lrm;($gid)&lrm; ";
									} else {
										$daytext .=  " &rlm;($gid)&rlm; ";
									}
								}
								$daytext .=  "</a>\n";
								$lastgid=$gid;
							}
							$daytext .=  $text. "</li>";
							$OutputDone = true;
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
					$name = get_family_descriptor($gid);
					$filterev = "all";
					if ($onlyBDM == "yes") $filterev = "bdm";
					$tempText = get_calendar_fact($factrec, $action, $filter, $gid, $filterev);
					$text = preg_replace("/href=\"calendar\.php/", "href=\"".$SERVER_URL."calendar.php", $tempText);
					if ($text!="filter") {
						if (FactViewRestricted($gid, $factrec) or $text=="") {
							$PrivateFacts = true;
						} else {
							if ($lastgid!=$gid) {
								$daytext .=  "<li><a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
								if ($SHOW_FAM_ID_NUMBERS) {
									if ($TEXT_DIRECTION=="ltr")
										$daytext .=  " &lrm;($gid)&lrm; ";
									else $daytext .=  " &rlm;($gid)&rlm; ";
								}
								$daytext .=  "</a>\n";
								$lastgid=$gid;
							}
							$daytext .=  $text . "</li>";
							$OutputDone = true;
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
			$daytext .= print_text($Advisory, 0, 1);
		} else if (!$OutputDone) {    // No Facts were found
			$pgv_lang["global_num1"] = $daysprint;
			$Advisory = "no_events_" . $config["filter"];
			if ($daysprint==1) $Advisory .= "1";
			$daytext .= print_text($Advisory, 0, 1);
	}

	$daytext = preg_replace("/<br \/>/", " ", $daytext);
	$daytext = strip_tags($daytext, '<a><ul><li><b>');
	if($daytext == "<ul></ul>"){
		$daytext = "";
	}
	$dataArray[2]  = $daytext;
	return $dataArray;
}

/**
 * Returns the today's events array used for the RSS feed
 *
 * @return the array with todays events data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO does not display the privacy message displayed by the upcoming events feed.
 */
function getTodaysEvents() {
	global $pgv_lang, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $REGEXP_DB, $DEBUG, $ASC, $INDEX_DIRECTORY, $IGNORE_FACTS, $IGNORE_YEAR, $SERVER_URL;

	if ($command=="user") $filter = "living";
	else $filter = "all";

	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL";

	$daytext = "<ul>";
	$action = "today";
	$dataArray[0] = $pgv_lang["on_this_day"];
	$dataArray[1] = time();

	$dayindilist = array();
	$dayfamlist = array();
	$found_facts = array();
	$cache_load = false;

	if (($command=="user")&&isset($_SESSION["todays_events"][$command][$GEDCOM])&&(!isset($DEBUG)||($DEBUG==false))) {
		$found_facts = $_SESSION["todays_events"][$command][$GEDCOM];
		$cache_load = true;
	}
	else if (($command=="gedcom")&&(file_exists($INDEX_DIRECTORY.$GEDCOM."_todays.php"))&&(!isset($DEBUG)||($DEBUG==false))) {
		$modtime = filemtime($INDEX_DIRECTORY.$GEDCOM."_todays.php");
		$mday = date("d", $modtime);
		if ($mday==$day) {
			$fp = fopen($INDEX_DIRECTORY.$GEDCOM."_todays.php", "rb");
			$fcache = fread($fp, filesize($INDEX_DIRECTORY.$GEDCOM."_todays.php"));
			fclose($fp);
			$found_facts = unserialize($fcache);
			$cache_load = true;
		}
	}
	if (!$cache_load) {
		if ($REGEXP_DB) $query = "2 DATE[^\n]*[^1-9]$day $month";
		else $query = "%2 DATE %$day $month%";
		$dayindilist = search_indis($query);
		$dayfamlist = search_fams($query);
	}
	if ((count($dayindilist)>0)||(count($dayfamlist)>0)) {
		$query = "2 DATE[^\n]*[^1-9]$day $month";
		foreach($dayindilist as $gid=>$indi) {
			$disp = true;
			if (($filter=="living")&&(is_dead_id($gid)==1)) $disp = false;
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid);
			if ($disp) {
				$facts = get_all_subrecords($indi["gedcom"], $skipfacts, false, false);
				foreach($facts as $index=>$factrec) {
					$ct = preg_match("/$query/i", $factrec, $match);
					if ($ct>0) {
						$found_facts[] = array($gid, $factrec, "INDI");
					}
				}
			}
		}
		foreach($dayfamlist as $gid=>$fam) {
			$disp = true;
			if ($filter=="living") {
				$parents = find_parents_in_record($fam["gedcom"]);
				if (is_dead_id($parents["HUSB"])==1) $disp = false;
				else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["HUSB"]);
				if ($disp) {
					if (is_dead_id($parents["WIFE"])==1) $disp = false;
					else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["WIFE"]);
				}
			}
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid, "FAM");
			if ($disp) {
				$facts = get_all_subrecords($fam["gedcom"], $skipfacts, false, false);
				foreach($facts as $index=>$factrec) {
					$ct = preg_match("/$query/i", $factrec, $match);
					if ($ct>0) {
						$found_facts[] = array($gid, $factrec, "FAM");
					}
				}
			}
		}
	}
	if (count($found_facts)>0 && is_array($found_facts)) {
		$ASC = 1;
		$IGNORE_FACTS=1;
		$IGNORE_YEAR = 0;
		uasort($found_facts, "compare_facts");
		$lastgid="";
		foreach($found_facts as $index=>$factarray) {
			if ($factarray[2]=="INDI") {
				$gid = $factarray[0];
				$factrec = $factarray[1];
		  		if ((displayDetailsById($gid)) && (!FactViewRestricted($gid, $factrec))) {
					$indirec = find_person_record($gid);
					//$text = get_calendar_fact($factrec, $action, $filter, $gid);
					$tempText = get_calendar_fact($factrec, $action, $filter, $gid);
					$text= preg_replace("/href=\"calendar\.php/", "href=\"".$SERVER_URL."calendar.php", $tempText);
					if ($text!="filter") {
						if ($lastgid!=$gid) {
							$name = check_NN(get_sortable_name($gid));
							$daytext .= "<li><a href=\"".$SERVER_URL ."individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
							if ($SHOW_ID_NUMBERS) {
							   if ($TEXT_DIRECTION=="ltr")
									$daytext .= " &lrm;($gid)&lrm;";
							   else $daytext .= " &rlm;($gid)&rlm;";
							}
							$daytext .= "</a>\n";
							$lastgid=$gid;
						}
						$daytext .= $text . "</li>";
					}
				}
			}

			if ($factarray[2]=="FAM") {
				$gid = $factarray[0];
				$factrec = $factarray[1];
		  		if ((displayDetailsById($gid, "FAM")) && (!FactViewRestricted($gid, $factrec))) {
					$famrec = find_family_record($gid);
					$name = get_family_descriptor($gid);
					//$text = get_calendar_fact($factrec, $action, $filter, $gid);
					$tempText = get_calendar_fact($factrec, $action, $filter, $gid);
					$text= preg_replace("/href=\"calendar\.php/", "href=\"".$SERVER_URL."calendar.php", $tempText);
					if ($text!="filter") {
						if ($lastgid!=$gid) {
							$daytext .= "<li><a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
							if ($SHOW_FAM_ID_NUMBERS) {
							   if ($TEXT_DIRECTION=="ltr")
									$daytext .=  " &lrm;($gid)&lrm;";
							   else $daytext .=  " &rlm;($gid)&rlm;";
							}
							$daytext .=  "</a>\n";
							$lastgid=$gid;
						}
						$daytext .=  $text . "</li>";
					}
				}
			}
		}
	}

	//-- store the results in the session to improve speed of future page loads
	if ($command=="user") {
		$_SESSION["todays_events"][$command][$GEDCOM] = $found_facts;
	}
	else if (($command=="gedcom")&&(is_writable($INDEX_DIRECTORY))) {
		$fp = fopen($INDEX_DIRECTORY."/".$GEDCOM."_todays.php", "wb");
		fwrite($fp, serialize($found_facts));
		fclose($fp);
	}
	$daytext .= "</ul>";
	$daytext = preg_replace("/<br \/>/", " ", $daytext);
	$daytext = strip_tags($daytext, '<a><ul><li><b>');
	if($daytext == "<ul></ul>"){
		$daytext = "";
	}
	$dataArray[2]  = $daytext;
	return $dataArray;
}

/**
 * Returns the gedcom stats
 *
 * @return the array with recent changes data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO does not pick up the GEDCOM stats block config and always shows most common names.
 */
function getGedcomStats() {
	global $pgv_lang, $day, $month, $year, $GEDCOM, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $command, $COMMON_NAMES_THRESHOLD, $SERVER_URL, $RTLOrd, $TBLPREFIX;

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
			$text = strip_tags(str_replace("#SOFTWARE#", $title, $pgv_lang["gedcom_created_using"]));
			$tt = preg_match("/2 VERS (.*)/", $softrec, $tmatch);
			if ($tt>0) $version = trim($tmatch[1]);
			else $version="";
			$text = strip_tags(str_replace("#VERSION#", $version, $text));
			$data .= $text;
		}
	}
	$ct=preg_match("/1 DATE (.*)/", $head, $match);
	if ($ct>0) {
		$date = trim($match[1]);
		$dataArray[1] = strtotime($date);

		if (empty($title)){
			$data .= str_replace("#DATE#", get_changed_date($date), $pgv_lang["gedcom_created_on"]);
		} else {
			$data .= str_replace("#DATE#", get_changed_date($date), $pgv_lang["gedcom_created_on2"]);
		}
	}

	$data .= " <br />\n";
	$data .= get_list_size("indilist"). " - " .$pgv_lang["stat_individuals"]."<br />";
	$data .= get_list_size("famlist"). " - ".$pgv_lang["stat_families"]."<br />";
	$data .= get_list_size("sourcelist")." - ".$pgv_lang["stat_sources"]."<br /> ";
	$data .= get_list_size("otherlist")." - ".$pgv_lang["stat_other"]."<br />";



	// NOTE: Get earliest birth year
	$sql = "select min(d_year) as lowyear from ".$TBLPREFIX."dates where d_file = '".$GEDCOMS[$GEDCOM]["id"]."' and d_fact = 'BIRT' and d_year != '0' and d_type is null";
	$tempsql = dbquery($sql);
	$res =& $tempsql;
	$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
	$data .= $pgv_lang["stat_earliest_birth"]." - ".$row["lowyear"]."<br />\n";

	// NOTE: Get the latest birth year
	$sql = "select max(d_year) as highyear from ".$TBLPREFIX."dates where d_file = '".$GEDCOMS[$GEDCOM]["id"]."' and d_fact = 'BIRT' and d_type is null";
	$tempsql = dbquery($sql);
	$res =& $tempsql;
	$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
	$data .= $pgv_lang["stat_latest_birth"]." - " .$row["highyear"]."<br />\n";



	$surnames = get_common_surnames_index($GEDCOM);
	if (count($surnames)>0) {
		$data .="<b>" . $pgv_lang["common_surnames"]."</b><br />";
		$i=0;
		foreach($surnames as $indexval => $surname) {
			if ($i>0) $data .= ", ";
			if (in_array(ord(substr($surname["name"], 0, 2)),$RTLOrd)) {
				//if (ord(substr($surname["name"], 0, 2),$RTLOrd)){}
				$data .= "<a href=\"".$SERVER_URL ."indilist.php?surname=".urlencode($surname["name"])."\">".$surname["name"]."</a>";
			}
			else $data .= "<a href=\"".$SERVER_URL ."indilist.php?surname=".$surname["name"]."\">".$surname["name"]."</a>";
			$i++;
		}
	}

	$data = strip_tags($data, '<a><br><b>');

	$dataArray[2] = $data;
	return $dataArray;
}

/**
 * Returns the gedcom news for the RSS feed
 *
 * @return array of GEDCOM news arrays. Each GEDCOM news array contains $itemArray[0] = title, $itemArray[1] = date,
 * 				$itemArray[2] = data, $itemArray[3] = anchor (so that the link will load the proper part of the PGV page)
 * @TODO prepend relative URL's in news items with $SERVER_URL
 */
function getGedcomNews() {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION, $GEDCOM, $command, $TIME_FORMAT, $VERSION, $SERVER_URL;

	$usernews = getUserNews($GEDCOM);

	$dataArray = array();
	foreach($usernews as $key=>$news) {

		$day = date("j", $news["date"]);
		$mon = date("M", $news["date"]);
		$year = date("Y", $news["date"]);
		$data = "";
		$ct = preg_match("/#(.+)#/", $news["title"], $match);
		if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $news["title"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["title"]);
		}
		$itemArray[0] = $news["title"];

		$itemArray[1] = iso8601_date($news["date"]);
		$ct = preg_match("/#(.+)#/", $news["text"], $match);
		if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $news["text"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["text"]);
		}
		$ct = preg_match("/#(.+)#/", $news["text"], $match);
		if ($ct>0) {
			if (isset($pgv_lang[$match[1]])) $news["text"] = preg_replace("/$match[0]/", $pgv_lang[$match[1]], $news["text"]);
			$varname = $match[1];
			if (isset($$varname)) $news["text"] = preg_replace("/$match[0]/", $$varname, $news["text"]);
		}
		$trans = get_html_translation_table(HTML_SPECIALCHARS);
		$trans = array_flip($trans);
		$news["text"] = strtr($news["text"], $trans);
		$news["text"] = nl2br($news["text"]);
		$data .= $news["text"];
		$itemArray[2] = $data;
		$itemArray[3] = $news["anchor"];
		$dataArray[] = $itemArray;

	}
	return $dataArray;
}

/**
 * Returns the top 10 surnames
 *
 * @return the array with the top 10 surname data. the format is $dataArray[0] = title, $dataArray[1] = date,
 * 				$dataArray[2] = data
 * @TODO does not pick up the the top 10 surname block config and always uses 10 names.
 * @TODO Possibly turn list into a <ul> list
 */
function getTop10Surnames() {
	global $pgv_lang, $GEDCOM,$SERVER_URL;
	global $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $COMMON_NAMES_THRESHOLD, $PGV_BLOCKS, $command, $PGV_IMAGES, $PGV_IMAGE_DIR;

	$data = "";
	$dataArray = array();


	function top_surname_sort($a, $b) {
		return $b["match"] - $a["match"];
	}

	$PGV_BLOCKS["print_block_name_top10"]["config"] = array("num"=>10);

	if (empty($config)) $config = $PGV_BLOCKS["print_block_name_top10"]["config"];

	$dataArray[0] = str_replace("10", $config["num"], $pgv_lang["block_top10_title"]);
	$dataArray[1] = time();

	//-- cache the result in the session so that subsequent calls do not have to
	//-- perform the calculation all over again.
	if (isset($_SESSION["top10"][$GEDCOM])) {
		$surnames = $_SESSION["top10"][$GEDCOM];
	}
	else {
		$surnames = get_top_surnames($config["num"]);

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
				$data .= "<a href=\"".$SERVER_URL ."indilist.php?surname=".urlencode($surname["name"])."\">".PrintReady($surname["name"])."</a> [".$surname["match"]."] <br />";
				$i++;
				if ($i>=$config["num"]) break;
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
 * @TODO does not pick up the recent changes block config and always uses 30 days.
 * @TODO use date of most recent change instead of curent time
 */
function getRecentChanges() {
	global $pgv_lang, $factarray, $month, $year, $day, $monthtonum, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $command, $TEXT_DIRECTION, $SHOW_FAM_ID_NUMBERS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $REGEXP_DB, $DEBUG, $ASC, $IGNORE_FACTS, $IGNORE_YEAR, $TOTAL_QUERIES, $LAST_QUERY, $PGV_BLOCKS, $SHOW_SOURCES,$SERVER_URL;
	global $medialist;

	if ($command=="user") $filter = "living";
	else $filter = "all";

	if (empty($config)) $config = $PGV_BLOCKS["print_recent_changes"]["config"];
	if ($config["days"]<1 or $config["days"]>30) $config["days"] = 30;
	if (isset($config["hide_empty"])) $HideEmpty = $config["hide_empty"];
	else $HideEmpty = "no";

	$dataArray[0] = $pgv_lang["recent_changes"];
	$dataArray[1] = time();//FIXME - get most recent change time

	$recentText = "";
	$action = "today";
	$dayindilist = array();
	$dayfamlist = array();
	$daysourcelist = array();
	$dayrepolist = array();
	$found_facts = array();
	/* - don't cache this block
	if (isset($_SESSION["recent_changes"][$command][$GEDCOM])&&(!isset($DEBUG)||($DEBUG==false))) {
		$found_facts = $_SESSION["recent_changes"][$command][$GEDCOM];
	}
	else {
		*/
		$monthstart = mktime(1,0,0,$monthtonum[strtolower($month)],$day,$year);
		$mmon = strtolower(date("M", $monthstart));
		$mmon2 = strtolower(date("M", $monthstart-(60*60*24*$config["days"])));
		$mday2 = date("d", $monthstart-(60*60*24*$config["days"]));
		$myear2 = date("Y", $monthstart-(60*60*24*$config["days"]));

		$fromdate = $myear2.date("m", $monthstart-(60*60*24*$config["days"])).$mday2;
		if ($day < 10)
			$mday3 = "0".$day;
		else $mday3 = $day;
		$todate = $year.date("m", $monthstart).$mday3;

		$dayindilist = search_indis_dates("", $mmon, $year, "CHAN");
		$dayfamlist = search_fams_dates("", $mmon, $year, "CHAN");
		if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $dayrepolist = search_other_dates("", $mmon, $year, "CHAN");
		if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $daysourcelist = search_sources_dates("", $mmon, $year, "CHAN");
		if ($mmon!=$mmon2) {
			$dayindilist2 = search_indis_dates("", $mmon2, $myear2, "CHAN");
			$dayfamlist2 = search_fams_dates("", $mmon2, $myear2, "CHAN");
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $dayrepolist2 = search_other_dates("", $mmon2, $myear2, "CHAN");
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $daysourcelist2 = search_sources_dates("", $mmon2, $myear2, "CHAN");
			$dayindilist = pgv_array_merge($dayindilist, $dayindilist2);
			$dayfamlist = pgv_array_merge($dayfamlist, $dayfamlist2);
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $daysourcelist = pgv_array_merge($daysourcelist, $daysourcelist2);
			if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) $dayrepolist = pgv_array_merge($dayrepolist, $dayrepolist2);
		}
	/*
	}
	*/
	if ((count($dayindilist)>0)||(count($dayfamlist)>0)||(count($daysourcelist)>0)) {
		$found_facts = array();
		$last_total = $TOTAL_QUERIES;
		foreach($dayindilist as $gid=>$indi) {
			$disp = true;
			if (($filter=="living")&&(is_dead_id($gid)==1)) $disp = false;
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid);
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $indi["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));

						$datemonth=$monthtonum[str2lower($date[0]["month"])];
						if ($datemonth > 0 && $datemonth < 10) $datemonth = "0".$datemonth;
						$factdate = $date[0]["year"].$datemonth.$date[0]["day"];
						if ($factdate <= $todate && $factdate > $fromdate) {
							$found_facts[] = array($gid, $factrec, "INDI");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $indi["gedcom"], $i);
				}
			}
		}
		foreach($dayfamlist as $gid=>$fam) {
			$disp = true;
			if ($filter=="living") {
				$parents = find_parents_in_record($fam["gedcom"]);
				if (is_dead_id($parents["HUSB"])==1) $disp = false;
				else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["HUSB"]);
				if ($disp) {
					if (is_dead_id($parents["WIFE"])==1) $disp = false;
					else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($parents["WIFE"]);
				}
			}
			else if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsByID($gid, "FAM");
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $fam["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));

						$datemonth=$monthtonum[str2lower($date[0]["month"])];
						if ($datemonth > 0 && $datemonth < 10) $datemonth = "0".$datemonth;
						$factdate = $date[0]["year"].$datemonth.$date[0]["day"];

						if ($factdate <= $todate && $factdate > $fromdate) {
							$found_facts[] = array($gid, $factrec, "FAM");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $fam["gedcom"], $i);
				}
			}
		}
		foreach($daysourcelist as $gid=>$source) {
			$disp = true;
			$disp = displayDetailsByID($gid, "SOUR");
			if ($disp) {
				$i = 1;
				$factrec = get_sub_record(1, "1 CHAN", $source["gedcom"], $i);
				while(!empty($factrec)) {
					$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
					if ($ct>0) {
						$date = parse_date(trim($match[1]));

						$datemonth=$monthtonum[str2lower($date[0]["month"])];
						if ($datemonth > 0 && $datemonth < 10) $datemonth = "0".$datemonth;
						$factdate = $date[0]["year"].$datemonth.$date[0]["day"];
						if ($factdate <= $todate && $factdate > $fromdate) {
							$found_facts[] = array($gid, $factrec, "SOUR");
						}
					}
					$i++;
					$factrec = get_sub_record(1, "1 CHAN", $source["gedcom"], $i);
				}
			}
		}
		foreach($dayrepolist as $rid=>$repo) {
			$disp = false;
			if ($repo["type"] == "REPO") {
				$disp = displayDetailsByID($rid, "REPO");
				if ($disp) {
					$i = 1;
					$factrec = get_sub_record(1, "1 CHAN", $repo["gedcom"], $i);
					while(!empty($factrec)) {
						$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
						if ($ct>0) {
							$date = parse_date(trim($match[1]));
							$datemonth=$monthtonum[str2lower($date[0]["month"])];
							if ($datemonth > 0 && $datemonth < 10) $datemonth = "0".$datemonth;
							$factdate = $date[0]["year"].$datemonth.$date[0]["day"];
							if ($factdate <= $todate && $factdate > $fromdate) {
								$found_facts[] = array($rid, $factrec, "REPO");
							}
						}
						$i++;
						$factrec = get_sub_record(1, "1 CHAN", $repo["gedcom"], $i);
					}
				}
			}
			else if ($repo["type"] == "OBJE") {
				$disp = displayDetailsByID($rid, "OBJE");
				if ($disp) {
					$i = 1;
					$factrec = get_sub_record(1, "1 CHAN", $repo["gedcom"], $i);
					while(!empty($factrec)) {

						$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
						if ($ct>0) {
							$date = parse_date(trim($match[1]));

							$datemonth=$monthtonum[str2lower($date[0]["month"])];
							if ($datemonth > 0 && $datemonth < 10) $datemonth = "0".$datemonth;
							$factdate = $date[0]["year"].$datemonth.$date[0]["day"];
							if ($factdate <= $todate && $factdate > $fromdate) {
								$found_facts[] = array($rid, $factrec, "OBJE");
							}
						}
						$i++;
						$factrec = get_sub_record(1, "1 CHAN", $repo["gedcom"], $i);
					}
				}
			}
		}
	}
//-- store the results in the session to improve speed of future page loads
//	$_SESSION["recent_changes"][$command][$GEDCOM] = $found_facts;

// Start output
	if (count($found_facts)==0 and $HideEmpty=="yes") return false;


//		Print block content
	$pgv_lang["global_num1"] = $config["days"];		// Make this visible
	if (count($found_facts)==0) {
		//$recentText .= $pgv_lang["recent_changes"];
		$recentText .= print_text("recent_changes_none", 0, 1);
	} else {
		//$recentText .= $pgv_lang["recent_changes_some"];
		$recentText .= print_text("recent_changes_some", 0, 1);
		$ASC = 1;
		$IGNORE_FACTS = 1;
		$IGNORE_YEAR = 0;
		uasort($found_facts, "compare_facts");
		$lastgid="";
		foreach($found_facts as $index=>$factarr) {
			if ($factarr[2]=="INDI") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid)) {
					$indirec = find_person_record($gid);
					if ($lastgid!=$gid) {
						$name = check_NN(get_sortable_name($gid));
						$recentText .= "<a href=\"".$SERVER_URL ."individual.php?pid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_ID_NUMBERS) {
							if ($TEXT_DIRECTION=="ltr")
								$recentText .= " &lrm;($gid)&lrm; ";
							else $recentText .= " &rlm;($gid)&rlm; ";
						}
						$recentText .= "</a><br />\n";
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
					$recentText .= "<br />";
				}
			}

			if ($factarr[2]=="FAM") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "FAM")) {
					$famrec = find_family_record($gid);
					$name = get_family_descriptor($gid);
					if ($lastgid!=$gid) {
						$recentText .= "<a href=\"".$SERVER_URL ."family.php?famid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
							if ($TEXT_DIRECTION=="ltr")
								$recentText .= " &lrm;($gid)&lrm; ";
							else $recentText .= " &rlm;($gid)&rlm; ";
						}
						$recentText .= "</a><br />\n";
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
					$recentText .= "<br />";
				}
			}

			if ($factarr[2]=="SOUR") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "SOUR")) {
					$sourcerec = find_source_record($gid);
					$name = get_source_descriptor($gid);
					if ($lastgid!=$gid) {
						$recentText .= "<a href=\"".$SERVER_URL ."source.php?sid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
							if ($TEXT_DIRECTION=="ltr")
								$recentText .= " &lrm;($gid)&lrm; ";
							else $recentText .= " &rlm;($gid)&rlm; ";
						}
						$recentText .= "</a><br />\n";
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
					$recentText .= "<br />";
				}
			}

			if ($factarr[2]=="REPO") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "REPO")) {
					$reporec = find_repo_record($gid);
					$name = get_repo_descriptor($gid);
					if ($lastgid!=$gid) {
						$recentText .= "<a href=\"".$SERVER_URL ."repo.php?rid=$gid&amp;ged=".$GEDCOM."\"><b>".PrintReady($name)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
							if ($TEXT_DIRECTION=="ltr")
								$recentText .= " &lrm;($gid)&lrm; ";
							else $recentText .= " &rlm;($gid)&rlm; ";
						}
						$recentText .= "</a><br />\n";
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
					$recentText .= "<br />";
				}
			}
			if ($factarr[2]=="OBJE") {
				$gid = $factarr[0];
				$factrec = $factarr[1];
				if (displayDetailsById($gid, "OBJE")) {
					$mediarec = find_media_record($gid);
					if (isset($medialist[0]["title"]) && $medialist[0]["title"] != "") $title=$medialist[0]["title"];
					else $title = $medialist[0]["file"];
					$SearchTitle = preg_replace("/ /","+",$title);
					if ($lastgid!=$gid) {
 						$recentText .= "<a href=\"".$SERVER_URL ."medialist.php?action=filter&amp;search=yes&amp;filter=$SearchTitle&amp;ged=".$GEDCOM."\"><b>".PrintReady($title)."</b>";
						if ($SHOW_FAM_ID_NUMBERS) {
							if ($TEXT_DIRECTION=="ltr")
								$recentText .= " &lrm;($gid)&lrm; ";
							else $recentText .= " &rlm;($gid)&rlm; ";
						}
						$recentText .= "</a><br />\n";
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
					$recentText .= "<br />";
				}
			}
		}

	}

	$recentText = strip_tags($recentText, '<a><br><b>');
	$dataArray[2] = $recentText;
	return $dataArray;

}

?>
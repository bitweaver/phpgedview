<?php
/**
* Privacy Functions
*
* See http://www.phpgedview.net/privacy.php for more information on privacy in PhpGedView
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
* @version $Id: functions_privacy.php,v 1.1 2009/04/30 17:51:51 lsces Exp $
* @package PhpGedView
* @subpackage Privacy
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_PRIVACY_PHP', '');

if ($USE_RELATIONSHIP_PRIVACY) {
	/**
	* store relationship paths in a cache
	*
	* the <var>$NODE_CACHE</var> is an array of nodes that have been previously checked
	* by the relationship calculator.  This cache greatly speed up the relationship privacy
	* checking on charts as many relationships on charts are in the same relationship path.
	*
	* See the documentation for the get_relationship() function in the functions.php file.
	*/
	$NODE_CACHE = array();
}

//-- allow users to overide functions in privacy file
if (!function_exists("is_dead")) {
/**
* check if a person is dead
*
* this function will read a person's gedcom record and try to determine whether the person is
* dead or not.  It checks several parameters to determine death status in the following order:
* 1. a DEAT record returns dead
* 2. a BIRT record less than <var>$MAX_ALIVE_AGE</var> returns alive
* 3. Any date in the record that would make them older than <var>$MAX_ALIVE_AGE</var>
* 4. A date in the parents record that makes the parents older than <var>$MAX_ALIVE_AGE</var>+40
* 5. A marriage record with a date greater than <var>$MAX_ALIVE_AGE</var>-10
* 6. A date in the spouse record greater than <var>$MAX_ALIVE_AGE</var>
* 7. A date in the children's record that is greater than <var>$MAX_ALIVE_AGE</var>-10
* 8. A date in the grand children's record that is greater than <var>$MAX_ALIVE_AGE</var>-30
*
* This function should only be called once per individual.  In index mode this is called during
* the Gedcom import.  In MySQL mode this is called the first time the individual is accessed
* and then the database table is updated.
* @author John Finlay (yalnifj)
* @param string $indirec the raw gedcom record
* @return bool true if dead false if alive
*/
function is_dead($indirec, $cyear="", $import=false) {
	global $CHECK_CHILD_DATES;
	global $MAX_ALIVE_AGE;
	global $HIDE_LIVE_PEOPLE;
	global $PRIVACY_BY_YEAR;
	global $pgv_lang;
	global $GEDCOM;

	if (preg_match('/^0 @('.PGV_REGEX_XREF.')@ INDI/', $indirec, $match)) {
		$pid=$match[1];
	} else {
		return false;
	}

	if (empty($cyear)) {
		$cyear=date("Y");
	}

	// -- check for a death record
	foreach (explode('|', PGV_EVENTS_DEAT) as $tag) {
		$deathrec = get_sub_record(1, "1 ".$tag, $indirec);
		if ($deathrec) {
			if ($cyear==date("Y")) {
				$resn = get_gedcom_value("RESN", 2, $deathrec);
				if (empty($resn) || ($resn!='confidential' && $resn!='privacy')) {
					// Gedcom asserts an event if either the value is Y, or a date/place is supplied.
					if (strpos($deathrec, "1 {$tag} Y")===0 || strpos($deathrec, "\n2 DATE ") || strpos($deathrec, "\n2 PLAC ")) {
						return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
					}
				}
			} else {
				if (preg_match('/\n2 DATE (.+)/', $deathrec, $match)) {
					$date=new GedcomDate($match[1]);
					$year=$date->gregorianYear();
					return update_isdead($pid, get_id_from_gedcom($GEDCOM), $year + $cyear < date("Y"));
				}
			}
		}
	}

	//-- if birthdate less than $MAX_ALIVE_AGE return false
	foreach (explode('|', PGV_EVENTS_BIRT) as $tag) {
		$birthrec = get_sub_record(1, "1 ".$tag, $indirec);
		if ($birthrec) {
			$ct = preg_match("/\d DATE.*\s(\d{3,4})\s/", $birthrec, $match);
			if ($ct>0) {
				$byear = $match[1];
				if (($cyear-$byear) < $MAX_ALIVE_AGE) {
					//print "found birth record less that $MAX_ALIVE_AGE\n";
					return update_isdead($pid, get_id_from_gedcom($GEDCOM), false);
				}
			}
		}
	}

	// If no death record than check all dates; the oldest one is the DOB
	$ct = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $indirec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		if (strstr($match[$i][0], "@#DHEBREW@")===false) {
			$byear = $match[$i][1];
			// If any date is prior to than MAX_ALIVE_AGE years ago assume they are dead
			if (($cyear-$byear) > $MAX_ALIVE_AGE) {
				//print "older than $MAX_ALIVE_AGE (".$match[$i][0].") year is $byear\n";
				return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
			}
		}
	}

	//-- during import we can't check child dates
	if ($import) {
		return -1;
	}

	// If we found no dates then check the dates of close relatives.
	if($CHECK_CHILD_DATES ) {
		//-- check the parents for dates
		$numfams = preg_match_all("/1\s*FAMC\s*@(.*)@/", $indirec, $fmatch, PREG_SET_ORDER);
		for($j=0; $j<$numfams; $j++) {
			$parents = find_parents($fmatch[$j][1]);
			if ($parents) {
				if (!empty($parents["HUSB"])) {
					$prec = find_person_record($parents["HUSB"]);
					$ct = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $prec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						$byear = $match[$i][1];
						// If any date is prior to than MAX_ALIVE_AGE years ago assume they are dead
						if (($cyear-$byear) > $MAX_ALIVE_AGE+40) {
							//print "father older than $MAX_ALIVE_AGE+40 (".$match[$i][0].") year is $byear\n";
							return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
						}
					}
				}
				if (!empty($parents["WIFE"])) {
					$prec = find_person_record($parents["WIFE"]);
					$ct = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $prec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						$byear = $match[$i][1];
						// If any date is prior to than MAX_ALIVE_AGE years ago assume they are dead
						if (($cyear-$byear) > $MAX_ALIVE_AGE+40) {
							//print "mother older than $MAX_ALIVE_AGE+40 (".$match[$i][0].") year is $byear\n";
							return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
						}
					}
				}
			}
		}
		$children = array();
		// For each family in which this person is a spouse...
		$numfams = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $fmatch, PREG_SET_ORDER);
		for($j=0; $j<$numfams; $j++) {
			// Get the family record
			$famrec = find_family_record($fmatch[$j][1]);

			//-- check for marriage date
			$marrec = get_sub_record(1, "1 MARR", $famrec);
			if ($marrec!==false) {
				$bt = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $marrec, $bmatch, PREG_SET_ORDER);
				for($h=0; $h<$bt; $h++) {
					$byear = $bmatch[$h][1];
					// if marriage was more than MAX_ALIVE_AGE-10 years ago assume the person has died
					if (($cyear-$byear) > ($MAX_ALIVE_AGE-10)) {
						//print "marriage older than $MAX_ALIVE_AGE-10 (".$bmatch[$h][0].") year is $byear\n";
						return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
					}
				}
			}
			//-- check spouse record for dates
			$parents = find_parents_in_record($famrec);
			if ($parents) {
				if ($parents["HUSB"]!=$pid) $spid = $parents["HUSB"];
				else $spid = $parents["WIFE"];
				$spouserec = find_person_record($spid);
				// Check dates
				$bt = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $spouserec, $bmatch, PREG_SET_ORDER);
				for($h=0; $h<$bt; $h++) {
					$byear = $bmatch[$h][1];
					// if the spouse is > $MAX_ALIVE_AGE assume the individual is dead
					if (($cyear-$byear) > $MAX_ALIVE_AGE) {
						//print "spouse older than $MAX_ALIVE_AGE (".$bmatch[$h][0].") year is $byear\n";
						return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
					}
				}
			}
			// Get the set of children
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				// Get each child's record
				$childrec = find_person_record($match[$i][1]);
				$children[] = $childrec;

				// Check each child's dates
				$bt = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $childrec, $bmatch, PREG_SET_ORDER);
				for($h=0; $h<$bt; $h++) {
					$byear = $bmatch[$h][1];
					// if any child was born more than MAX_ALIVE_AGE-10 years ago assume the parent has died
					if (($cyear-$byear) > ($MAX_ALIVE_AGE-10)) {
						//print "child older than $MAX_ALIVE_AGE-10 (".$bmatch[$h][0].") year is $byear\n";
						return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
					}
				}
			}
		}
		//-- check grandchildren for dates
		foreach($children as $indexval => $child) {
			// For each family in which this person is a spouse...
			$numfams = preg_match_all("/1\s*FAMS\s*@(.*)@/", $child, $fmatch, PREG_SET_ORDER);
			for($j=0; $j<$numfams; $j++) {
				// Get the family record
				$famrec = find_family_record($fmatch[$j][1]);

				// Get the set of children
				$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
					// Get each child's record
					$childrec = find_person_record($match[$i][1]);

					// Check each grandchild's dates
					$bt = preg_match_all("/\d DATE.*\s(\d{3,4})\s/", $childrec, $bmatch, PREG_SET_ORDER);
					for($h=0; $h<$bt; $h++) {
						$byear = $bmatch[$h][1];
						// if any grandchild was born more than MAX_ALIVE_AGE-30 years ago assume the grandparent has died
						if (($cyear-$byear) > ($MAX_ALIVE_AGE-30)) {
							//print "grandchild older than $MAX_ALIVE_AGE-30 (".$bmatch[$h][0].") year is $byear\n";
							return update_isdead($pid, get_id_from_gedcom($GEDCOM), true);
						}
					}
				}
			}
		}
	}
	return update_isdead($pid, get_id_from_gedcom($GEDCOM), false);
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("displayDetailsById")) {

/**
* checks if the person has died recently before showing their data
* @param string $pid the id of the person to check
* @return boolean
*/
function checkPrivacyByYear($pid) {
	global $MAX_ALIVE_AGE;

	$cyear = date("Y");
	$indirec = find_person_record($pid);
	//-- check death record
	$deatrec = get_sub_record(1, "1 DEAT", $indirec);
	$ct = preg_match("/2 DATE .*(\d\d\d\d).*/", $deatrec, $match);
	if ($ct>0) {
		$dyear = $match[1];
		if (($cyear-$dyear) <= $MAX_ALIVE_AGE-25) {
			return false;
		}
	}

	//-- check marriage records
	$famids = find_families_in_record($indirec, "FAMS");
	foreach($famids as $indexval => $famid) {
		$famrec = find_family_record($famid);
		//-- check death record
		$marrrec = get_sub_record(1, "1 MARR", $indirec);
		$ct = preg_match("/2 DATE .*(\d\d\d\d).*/", $marrrec, $match);
		if ($ct>0) {
			$myear = $match[1];
			if (($cyear-$myear) <= $MAX_ALIVE_AGE-15) {
				return false;
			}
		}
	}

	//-- check birth record
	$birtrec = get_sub_record(1, "1 BIRT", $indirec);
	$ct = preg_match("/2 DATE .*(\d\d\d\d).*/", $birtrec, $match);
	if ($ct>0) {
		$byear = $match[1];
		if (($cyear-$byear) <= $MAX_ALIVE_AGE) {
			return false;
		}
	}

	return true;
}


/**
* check if details for a GEDCOM XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.
*
* @author yalnifj
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings for
* @param string $type the GEDCOM type represented by the $pid.  This setting is used so that
* different gedcom types can be handled slightly different. (ie. a source cannot be dead)
* The possible values of $type are:
* - "INDI" record is an individual
* - "FAM" record is a family
* - "SOUR" record is a source
*          - "REPO" record is a repository
* @return boolean return true to show the persons details, return false to keep them private
*/
function displayDetailsById($pid, $type = "INDI") {
	global $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE, $PRIV_HIDE, $USE_RELATIONSHIP_PRIVACY, $CHECK_MARRIAGE_RELATIONS, $MAX_RELATION_PATH_LENGTH;
	global $global_facts, $person_privacy, $user_privacy, $HIDE_LIVE_PEOPLE, $GEDCOM, $SHOW_DEAD_PEOPLE, $MAX_ALIVE_AGE, $PRIVACY_BY_YEAR;
	global $PRIVACY_CHECKS, $PRIVACY_BY_RESN, $SHOW_SOURCES, $SHOW_LIVING_NAMES;
	global $GEDCOMS, $INDEX_DIRECTORY;

	static $privacy_cache = array();

	if (!$HIDE_LIVE_PEOPLE) return true;
	if (empty($pid)) return true;

	$pkey = $GEDCOMS[$GEDCOM]['id'].$pid;
	//-- check if the privacy has been cached and use it
	if (isset($privacy_cache[$pkey])) {
		return $privacy_cache[$pkey];
	}

	//-- keep a count of how many times we have checked for privacy
	if (!isset($PRIVACY_CHECKS)) $PRIVACY_CHECKS = 1;
	else $PRIVACY_CHECKS++;

	if (PGV_DEBUG_PRIV) {
		$fp = fopen($INDEX_DIRECTORY."/priv_log.txt", "a");
		$backtrace = debug_backtrace();
		$temp = "";
		if (isset($backtrace[2])) $temp .= basename($backtrace[2]["file"])." (".$backtrace[2]["line"].")";
		if (isset($backtrace[1])) $temp .= basename($backtrace[1]["file"])." (".$backtrace[1]["line"].")";
		$temp .= basename($backtrace[0]["file"])." (".$backtrace[0]["line"].")";
		fwrite($fp, date("Y-m-d H:i:s")."\t".$_SERVER["SCRIPT_NAME"]."\t".$temp."\t".$PRIVACY_CHECKS."- checking privacy for ".$type." ".$pid.PGV_EOL);
		fclose($fp);
	}

	$cache_privacy = true;

	//-- start of user specific privacy checks
	$username = PGV_USER_NAME;
	if ($username) {
		if (isset($user_privacy[$username]["all"])) {
			if ($user_privacy[$username]["all"] >= PGV_USER_ACCESS_LEVEL) {
				if ($cache_privacy) $privacy_cache[$pkey] = true;
				return true;
			} else {
				if ($cache_privacy) $privacy_cache[$pkey] = false;
				return false;
			}
		}
		if (isset($user_privacy[$username][$pid])) {
			if ($user_privacy[$username][$pid] >= PGV_USER_ACCESS_LEVEL) {
				if ($cache_privacy) $privacy_cache[$pkey] = true;
				return true;
			} else {
				if ($cache_privacy) $privacy_cache[$pkey] = false;
				return false;
			}
		}

		if (isset($person_privacy[$pid])) {
			if ($person_privacy[$pid]>=PGV_USER_ACCESS_LEVEL) {
				if ($cache_privacy) $privacy_cache[$pkey] = true;
				return true;
			}
			else {
				if ($cache_privacy) $privacy_cache[$pkey] = false;
				return false;
			}
		}
		if (PGV_USER_GEDCOM_ADMIN) {
			if ($cache_privacy) $privacy_cache[$pkey] = true;
			return true;
		}

		//-- look for an Ancestral File RESN (restriction) tag
		if (isset($PRIVACY_BY_RESN) && ($PRIVACY_BY_RESN==true)) {
			$gedrec = find_gedcom_record($pid);
			$resn = get_gedcom_value("RESN", 1, $gedrec);
			if (!empty($resn)) {
				if ($resn == "confidential") $ret = false;
				else if ($resn=="privacy" && PGV_USER_GEDCOM_ID != $pid) $ret = false;
				else $ret = true;
				if (!$ret) {
					if ($cache_privacy) $privacy_cache[$pkey] = $ret;
					return $ret;
				}
			}
		}

		if (PGV_USER_CAN_ACCESS) {
			if ($type=="INDI") {
				$gedrec = find_person_record($pid);
				$isdead = is_dead($gedrec);
				if ($USE_RELATIONSHIP_PRIVACY || get_user_setting($username, 'relationship_privacy')=="Y") {
					if ($isdead) {
						if ($SHOW_DEAD_PEOPLE>=PGV_USER_ACCESS_LEVEL) {
							if ($PRIVACY_BY_YEAR && $SHOW_DEAD_PEOPLE==PGV_USER_ACCESS_LEVEL) {
								if (!checkPrivacyByYear($pid)) {
									if ($cache_privacy) $privacy_cache[$pkey] = false;
									return false;
								}
							}
							if ($cache_privacy) $privacy_cache[$pkey] = true;
							return true;
						} else {
							if ($cache_privacy) $privacy_cache[$pkey] = false;
							return false;
						}
					} else {
						$my_id=PGV_USER_GEDCOM_ID;
						if (empty($my_id)) {
							if ($cache_privacy) $privacy_cache[$pkey] = false;
							return false;
						}
						if ($my_id==$pid) {
							if ($cache_privacy) $privacy_cache[$pkey] = true;
							return true;
						}
						if (get_user_setting($username, 'max_relation_path')>0) {
							$path_length = get_user_setting($username, 'max_relation_path');
						} else {
							$path_length = $MAX_RELATION_PATH_LENGTH;
						}
						$relationship = get_relationship(PGV_USER_GEDCOM_ID, $pid, $CHECK_MARRIAGE_RELATIONS, $path_length);
						if ($relationship!==false) {
							if ($cache_privacy) $privacy_cache[$pkey] = true;
							return true;
						} else {
							if ($cache_privacy) $privacy_cache[$pkey] = false;
							return false;
						}
					}
				} else {
					if ($isdead) {
						if ($SHOW_DEAD_PEOPLE>=PGV_USER_ACCESS_LEVEL) {
							if ($cache_privacy) $privacy_cache[$pkey] = true;
							return true;
						} else {
							if ($cache_privacy) $privacy_cache[$pkey] = false;
							return false;
						}
					} else {
						if ($SHOW_LIVING_NAMES>=PGV_USER_ACCESS_LEVEL) {
							if ($cache_privacy) $privacy_cache[$pkey] = true;
							return true;
						} else {
							if ($cache_privacy) $privacy_cache[$pkey] = false;
							return false;
						}
					}
				}
			}
		}
	} //-- end the user specif privacy settings

	//-- check the person privacy array for an exception
	if (isset($person_privacy[$pid])) {
		if ($person_privacy[$pid]>=PGV_USER_ACCESS_LEVEL) {
			if ($cache_privacy) {
				$privacy_cache[$pkey] = true;
			}
			return true;
		} else {
			if ($cache_privacy) {
				$privacy_cache[$pkey] = false;
			}
			return false;
		}
	}

	//-- look for an Ancestral File RESN (restriction) tag
	if (isset($PRIVACY_BY_RESN) && ($PRIVACY_BY_RESN==true)) {
		$gedrec = find_gedcom_record($pid);
		$resn = get_gedcom_value("RESN", 1, $gedrec);
		if ($resn == "none") {
			if ($cache_privacy) $privacy_cache[$pkey] = true;
			return true;
		} else if (!empty($resn)) {
			if ($cache_privacy) $privacy_cache[$pkey] = false;
			return false;
		}
	}

	if ($type=="INDI") {
		//-- option to keep person living if they haven't been dead very long
		if ($PRIVACY_BY_YEAR) {
			if (!checkPrivacyByYear($pid)) {
				if ($cache_privacy) {
					$privacy_cache[$pkey] = false;
				}
				return false;
			}
		}

		$gedrec = find_person_record($pid);
		$disp = is_dead($gedrec);
		if ($disp) {
			if ($SHOW_DEAD_PEOPLE>=PGV_USER_ACCESS_LEVEL) {
				if ($cache_privacy) {
					$privacy_cache[$pkey] = true;
				}
				return true;
			} else {
				if ($cache_privacy) {
					$privacy_cache[$pkey] = false;
				}
				return false;
			}
		} else {
			if (empty($username)) {
				if ($cache_privacy) {
					$privacy_cache[$pkey] = false;
				}
				return false;
			}
			if ($SHOW_LIVING_NAMES>PGV_USER_ACCESS_LEVEL) {
				if ($cache_privacy) {
					$privacy_cache[$pkey] = true;
				}
				return true;
			} else {
				if ($cache_privacy) {
					$privacy_cache[$pkey] = false;
				}
				return false;
			}
		}
	}
	if ($type=="FAM") {
		//-- check if we can display at least one parent
		$parents = find_parents($pid);
		$display = displayDetailsById($parents["HUSB"]) || displayDetailsById($parents["WIFE"]);
		$privacy_cache[$pkey] = $display;
		return $display;
	}
	if ($type=="SOUR") {
		if ($SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL) {
			$disp = true;
			$sourcerec = find_source_record($pid);
			if (!empty($sourcerec)) {
				$repoid = get_gedcom_value("REPO", 1, $sourcerec);
				$disp = displayDetailsById($repoid, "REPO");
			}
			$privacy_cache[$pkey] = $disp;
			return $disp;
		} else {
			$privacy_cache[$pkey] = false;
			return false;
		}
	}
	if ($type=="REPO") {
		if ($SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL) {
			$privacy_cache[$pkey] = true;
			return true;
		} else {
			$privacy_cache[$pkey] = false;
			return false;
		}
	}
	if ($type=="OBJE") {
		//-- for media privacy check all of the links to the media
		$links = get_media_relations($pid);
		$disp = true;
		foreach($links as $gid=>$type) {
			$disp = $disp && displayDetailsById($gid, $type);
			if (!$disp) {
				$privacy_cache[$pkey] = false;
				return false;
			}
		}
		$privacy_cache[$pkey] = $disp;
		return $disp;
	}
	$privacy_cache[$pkey] = true;
	return true;
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("showLivingNameById")) {
/**
* check if the name for a GEDCOM XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.  It first checks the
* <var>$SHOW_LIVING_NAMES</var> variable to see if names are shown to the public.  If they are
* then this function will always return true.  If the name is hidden then all relationships
* connected with the individual are also hidden such that arriving at this record results in a dead
* end.
*
* @author yalnifj
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings for
* @return boolean return true to show the person's name, return false to keep it private
*/
function showLivingNameById($pid) {
	global $SHOW_LIVING_NAMES, $person_privacy, $user_privacy;

	if (displayDetailsById($pid)) return true;
	$username = PGV_USER_NAME;
	if (!empty($username)) {
		if (isset($user_privacy[$username]["all"])) {
			if ($user_privacy[$username]["all"] >= PGV_USER_ACCESS_LEVEL) return true;
			else return false;
		}
		if (isset($user_privacy[$username][$pid])) {
			if ($user_privacy[$username][$pid] >= PGV_USER_ACCESS_LEVEL) return true;
			else return false;
		}
	}

	if (isset($person_privacy[$pid])) {
		if ($person_privacy[$pid]>=PGV_USER_ACCESS_LEVEL) return true;
		else return false;
	}

	if ($SHOW_LIVING_NAMES>=PGV_USER_ACCESS_LEVEL) return true;
	return false;
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("showFact")) {
/**
* check if the given GEDCOM fact for the given individual, family, or source XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.  It first checks the $global_facts array
* for admin override settings for the fact.
*
* @author yalnifj
* @param string $fact the GEDCOM fact tag to check the privacy settings
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings
* @return boolean return true to show the fact, return false to keep it private
*/
function showFact($fact, $pid, $type='INDI') {
	global $global_facts, $person_facts, $SHOW_SOURCES;

	//-- first check the global facts array
	if (isset($global_facts[$fact]["show"])) {
		if (PGV_USER_ACCESS_LEVEL>$global_facts[$fact]["show"])
			return false;
	}
	//-- check the person facts array
	if (isset($person_facts[$pid][$fact]["show"])) {
		if (PGV_USER_ACCESS_LEVEL>$person_facts[$pid][$fact]["show"])
			return false;
	}
	if ($fact=="SOUR") {
		if ($SHOW_SOURCES<PGV_USER_ACCESS_LEVEL)
			return false;
	}
	if ($fact!="NAME") {
		return displayDetailsById($pid, $type);
	} else {
		if (!displayDetailsById($pid, $type))
			return showLivingNameById($pid);
		else
			return true;
	}
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("showFactDetails")) {
/**
* check if the details of given GEDCOM fact for the given individual, family, or source XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.  It first checks the $global_facts array
* for admin override settings for the fact.
*
* @author yalnifj
* @param string $fact the GEDCOM fact tag to check the privacy settings
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings
* @return boolean return true to show the fact details, return false to keep it private
*/
function showFactDetails($fact, $pid) {
	global $global_facts, $person_facts;

	//-- first check the global facts array
	if (isset($global_facts[$fact]["details"])) {
		if (PGV_USER_ACCESS_LEVEL>$global_facts[$fact]["details"]) return false;
	}
	//-- check the person facts array
	if (isset($person_facts[$pid][$fact]["details"])) {
		if (PGV_USER_ACCESS_LEVEL>$person_facts[$pid][$fact]["details"]) return false;
	}

	return showFact($fact, $pid);
}
}

/**
* remove all private information from a gedcom record
*
* this function will analyze and gedcom record and privatize it by removing all private
* information that should be hidden from the user trying to access it.
* @param string $gedrec the raw gedcom record to privatize
* @return string the privatized gedcom record
*/
function privatize_gedcom($gedrec) {
	global $pgv_lang, $factarray, $GEDCOM, $SHOW_PRIVATE_RELATIONSHIPS, $pgv_private_records;
	global $global_facts, $person_facts;

	$gt = preg_match("/0 @(.+)@ (.+)/", $gedrec, $gmatch);
	if ($gt > 0) {
		$gid = trim($gmatch[1]);
		$type = trim($gmatch[2]);
		$disp = displayDetailsById($gid, $type);
		$pgv_private_records[$gid] = "";
		//-- check if the whole record is private
		if (!$disp) {
			//-- check if name should be private
			if (($type=="INDI")&&(!showLivingNameById($gid))) {
				$newrec = "0 @".$gid."@ INDI\n";
				$newrec .= "1 NAME " . $pgv_lang["private"] . "\n";
				if ($SHOW_PRIVATE_RELATIONSHIPS) {
					$fams = find_families_in_record($gedrec, "FAMS");
					foreach($fams as $f=>$famid) {
						$newrec .= "1 FAMS @$famid@\n";
					}
					$fams = find_families_in_record($gedrec, "FAMC");
					foreach($fams as $f=>$famid) {
						$newrec .= "1 FAMC @$famid@\n";
					}
				}
			}
			else if ($type=="SOUR") {
				$newrec = "0 @".$gid."@ SOUR\n";
				$newrec .= "1 TITL ".$pgv_lang["private"]."\n";
			}
			else {
				$newrec = "0 @".$gid."@ $type\n";
				if ($type=="INDI") {
					// Find all Name records of all Name types for this individual
					// A person can have, for instance, more than one 1 NAME record.  None of them should be privatized.
					foreach (array('NAME', 'FONE', 'ROMN', '_HNM', '_HEB') as $nameFact) {
						$factNum = 1;
						while (true) {
							$chil = trim(get_sub_record(1, "1 {$nameFact}", $gedrec, $factNum));
							if (empty($chil)) break;
							$newrec .= $chil."\n";
							$factNum ++;
						}
					}
					$chil = get_sub_record(1, "1 FAMC", $gedrec);
					$i=1;
					while (!empty($chil)) {
						$newrec .= trim($chil)."\n";
						$i++;
						$chil = get_sub_record(1, "1 FAMC", $gedrec, $i);
					}
					$chil = get_sub_record(1, "1 FAMS", $gedrec);
					$i=1;
					while (!empty($chil)) {
						$newrec .= trim($chil)."\n";
						$i++;
						$chil = get_sub_record(1, "1 FAMS", $gedrec, $i);
					}
				}
				else if ($type=="SOUR") {
					$chil = get_sub_record(1, "1 ABBR", $gedrec);
					if (!empty($chil)) $newrec .= trim($chil)."\n";
					$chil = get_sub_record(1, "1 TITL", $gedrec);
					if (!empty($chil)) $newrec .= trim($chil)."\n";
				}
				else if ($type=="FAM") {
					$chil = get_sub_record(1, "1 HUSB", $gedrec);
					if (!empty($chil)) $newrec .= trim($chil)."\n";
					$chil = get_sub_record(1, "1 WIFE", $gedrec);
					if (!empty($chil)) $newrec .= trim($chil)."\n";
					$chil = get_sub_record(1, "1 CHIL", $gedrec);
					$i=1;
					while (!empty($chil)) {
						$newrec .= trim($chil)."\n";
						$i++;
						$chil = get_sub_record(1, "1 CHIL", $gedrec, $i);
					}
				}
			}
			if ($type=="INDI") $newrec .= trim(get_sub_record(1, "1 SEX", $gedrec))."\n"; // do not privatize gender
			$newrec .= "1 NOTE ".trim($pgv_lang["person_private"])."\n";
			//print $newrec;
			$pgv_private_records[$gid] = $gedrec;
			return $newrec;
		}
		else {
			//-- check if we need to do any fact privacy checking
			//---- check for RESN
			$resn = false;
			if (preg_match("/\d RESN/", $gedrec)) $resn = true;
			//---- check for any person facts
			$ppriv = isset($person_facts[$gid]);
			//---- check for any global facts
			$gpriv = false;
			foreach($global_facts as $key=>$gfact) {
				if (preg_match("/1 ".$key."/", $gedrec)>0) $gpriv = true;
			}
			//-- if no fact privacy then return the record
			if (!$resn && !$ppriv && !$gpriv) return $gedrec;

			$newrec = "0 @".$gid."@ $type\n";
			//-- check all of the sub facts for access
			$subs = get_all_subrecords($gedrec, "", false, false);
			foreach($subs as $indexval => $sub) {
				$ct = preg_match("/1 (\w+)/", $sub, $match);
				if ($ct > 0) $type = trim($match[1]);
				else $type="";
				if (($type=='FACT' || $type=='EVEN') && preg_match('/2 TYPE (\w+)/', $sub, $match) && array_key_exists($match[1], $factarray)) {
					$type=$match[1];
				}
				if (FactViewRestricted($gid, $sub)==false && showFact($type, $gid) && showFactDetails($type, $gid)) $newrec .= $sub;
				else {
					$pgv_private_records[$gid] .= $sub;
				}
			}
			return $newrec;
		}
	}
	else {
		//-- not a valid gedcom record
		return $gedrec;
	}
}

function get_last_private_data($gid) {
	global $pgv_private_records;

	if (!isset($pgv_private_records[$gid])) return false;
	return $pgv_private_records[$gid];
}

/**
* get current user's access level
*
* checks the current user and returns their privacy access level
* @return int their access level
*/
function getUserAccessLevel($user_id=PGV_USER_ID, $ged_id=PGV_GED_ID) {
	global $PRIV_PUBLIC, $PRIV_NONE, $PRIV_USER;

	if ($user_id) {
		if (userGedcomAdmin($user_id, $ged_id)) {
			return $PRIV_NONE;
		} else {
			if (userCanAccess($user_id, $ged_id)) {
				return $PRIV_USER;
			} else {
				return $PRIV_PUBLIC;
			}
		}
	} else {
		return $PRIV_PUBLIC;
	}
}

/**
* Check fact record for editing restrictions
*
* Checks if the user is allowed to change fact information,
* based on the existence of the RESN tag in the fact record.
*
* @return int Allowed or not allowed
*/
function FactEditRestricted($pid, $factrec) {
	if (PGV_USER_GEDCOM_ADMIN) {
		return false;
	}

	if (preg_match("/2 RESN (.*)/", $factrec, $match)) {
		$match[1] = strtolower(trim($match[1]));
		if ($match[1] == "privacy" || $match[1]=="locked") {
			$myindi=PGV_USER_GEDCOM_ID;
			if ($myindi == $pid) {
				return false;
			}
			if (gedcom_record_type($pid, PGV_GED_ID)=='FAM') {
				$famrec = find_family_record($pid);
				$parents = find_parents_in_record($famrec);
				if ($myindi == $parents["HUSB"] || $myindi == $parents["WIFE"]) {
					return false;
				}
			}
			return true;
		}
	}
	return false;
}

/**
* Check fact record for viewing restrictions
*
* Checks if the user is allowed to view fact information,
* based on the existence of the RESN tag in the fact record.
*
* @return int Allowed or not allowed
*/
function FactViewRestricted($pid, $factrec) {
	if (PGV_USER_GEDCOM_ADMIN) {
		return false;
	}

	if (preg_match("/2 RESN (.*)/", $factrec, $match)) {
		$match[1] = strtolower(trim($match[1]));
		if ($match[1] == "confidential") return true;
		if ($match[1] == "privacy") {
			$myindi=PGV_USER_GEDCOM_ID;
			if ($myindi == $pid) {
				return false;
			}
			if (gedcom_record_type($pid, PGV_GED_ID)=='FAM') {
				$famrec = find_family_record($pid);
				$parents = find_parents_in_record($famrec);
				if ($myindi == $parents["WIFE"] || $myindi == $parents["HUSB"]) {
					return false;
				}
			}
			return true;
		}
	}
	return false;
}

?>

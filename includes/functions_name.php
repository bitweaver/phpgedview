<?php
/**
 * Name Specific Functions
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008 John Finlay and Others.  All rights reserved.
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
 * @version $Id: functions_name.php,v 1.10 2008/07/07 17:30:16 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

$NPFX_accept = array("Adm", "Amb", "Brig", "Can", "Capt", "Chan", "Chapln", "Cmdr", "Col", "Cpl", "Cpt", "Dr", "Gen", "Gov", "Hon", "Lady", "Lt", "Mr", "Mrs", "Ms", "Msgr", "Pfc", "Pres", "Prof", "Pvt", "Rabbi", "Rep", "Rev", "Sen", "Sgt", "Sir", "Sr", "Sra", "Srta", "Ven");

/**
 * Get array of common surnames from index
 *
 * This function returns a simple array of the most common surnames
 * found in the individuals list.
 * @param int $min the number of times a surname must occur before it is added to the array
 */
function get_common_surnames_index($ged) {
	global $gGedcom;

	if (empty($gGedcom[$ged]["commonsurnames"])) store_gedcoms();
	$surnames = array();
	if (empty($gGedcom[$ged]["commonsurnames"]) || ($gGedcom[$ged]["commonsurnames"]==",")) return $surnames;
	$names = preg_split("/[,;]/", $gGedcom[$ged]["commonsurnames"]);
	foreach($names as $indexval => $name) {
		$name = trim($name);
		if (!empty($name)) $surnames[$name]["name"] = stripslashes($name);
	}
	return $surnames;
}

/**
 * Get array of common surnames
 *
 * This function returns a simple array of the most common surnames
 * found in the individuals list.
 * @param int $min the number of times a surname must occur before it is added to the array
 */
function get_common_surnames($min) {
	global $TBLPREFIX, $GEDCOM, $indilist, $CONFIGURED, $gGedcom, $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $pgv_lang, $HNN, $ANN;

	$surnames = array();
	if (!$CONFIGURED || !adminUserExists() || (count($gGedcom)==0) || (!check_for_import($GEDCOM))) return $surnames;
	//-- this line causes a bug where the common surnames list is not properly updated
	// if ((!isset($indilist))||(!is_array($indilist))) return $surnames;
	$surnames = get_top_surnames(100);
	arsort($surnames);
	$topsurns = array();
	$i=0;
	foreach($surnames as $indexval => $surname) {
		$surname["name"] = trim($surname["name"]);
		if (!empty($surname["name"])
				&& stristr($surname["name"], "@N.N")===false
				&& stristr($surname["name"], $HNN)===false
				&& stristr($surname["name"], $ANN.",")===false
				&& stristr($COMMON_NAMES_REMOVE, $surname["name"])===false ) {
			if ($surname["match"]>=$min) {
				$topsurns[str2upper($surname["name"])] = $surname;
			}
			$i++;
		}
	}
	$addnames = preg_split("/[,;] /", $COMMON_NAMES_ADD);
	if ((count($addnames)==0) && (!empty($COMMON_NAMES_ADD))) $addnames[] = $COMMON_NAMES_ADD;
	foreach($addnames as $indexval => $name) {
		if (!empty($name)) {
			$topsurns[$name]["name"] = $name;
			$topsurns[$name]["match"] = $min;
		}
	}
	$delnames = preg_split("/[,;] /", $COMMON_NAMES_REMOVE);
	if ((count($delnames)==0) && (!empty($COMMON_NAMES_REMOVE))) $delnames[] = $COMMON_NAMES_REMOVE;
	foreach($delnames as $indexval => $name) {
		if (!empty($name)) {
			unset($topsurns[$name]);
		}
	}

	//-- check if we found some, else recurse
	if (empty($topsurns) && $min>2) $topsurns = get_common_surnames($min/2);
	uasort($topsurns, "itemsort");
	return $topsurns;
}

/**
 * get the person's name as surname, given names
 *
 * This function will return the given person's name in a format that is good for sorting
 * Surname, given names
 * @param string $pid the gedcom xref id for the person
 * @param string $alpha	only get the name that starts with a certain letter
 * @param string $surname only get the name that has this surname
 * @param boolean $allnames true returns all names in an array
 * @return string the sortable name
 */
function get_sortable_name($pid, $alpha="", $surname="", $allnames=false) {
	global $TBLPREFIX, $SHOW_LIVING_NAMES, $PRIV_PUBLIC;
	global $GEDCOM, $gGedcom, $indilist, $pgv_lang;

	$mynames = array();

	if (empty($pid)) {
		if ($allnames == false) return "@N.N., @P.N.";
		else {
			$mynames[] = "@N.N., @P.N.";
			return $mynames;
		}
	}

	//-- first check if the person is in the cache
	if ((isset($indilist[$pid]["names"]))&&($indilist[$pid]["gedfile"]==$gGedcom->mGEDCOMId)) {
		$names = $indilist[$pid]["names"];
	}
	else {
		//-- cache missed, so load the person into the cache with the find_person_record function
		//-- and get the name from the cache again
		$gedrec = find_person_record($pid);
		if (empty($gedrec)) $gedrec = find_updated_record($pid);
		if (!empty($indilist[$pid]["names"])) {
			$names = $indilist[$pid]["names"];
		}
		else {
			if ($allnames)
				return array("@N.N., @P.N.");
			else
				return "@N.N., @P.N.";
		}
	}
	if ($allnames) {
		$mynames = array();
		foreach ($names as $key => $name) {
			$mynames[] = sortable_name_from_name($name[0]);
		}
		return $mynames;
	}
	foreach($names as $indexval => $name) {
		if ($surname!="" && $name[2]==$surname) return sortable_name_from_name($name[0]);
		else if ($alpha!="" && $name[1]==$alpha) return sortable_name_from_name($name[0]);
	}
	return sortable_name_from_name($names[0][0]);
}

/**
 * get the sortable name from the gedcom name
 * @param string $name 	the name from the 1 NAME gedcom line including the /
 * @return string 	The new name in the form Surname, Given Names
 */
function sortable_name_from_name($name) {
	global $NPFX_accept;
	//-- remove any unwanted characters from the name
	if (preg_match("/^\.(\.*)$|^\?(\?*)$|^_(_*)$|^,(,*)$/", $name)) $name = preg_replace(array("/,/","/\./","/_/","/\?/"), array("","","",""), $name);
	$ct = preg_match("~(.*)/(.*)/(.*)~", $name, $match);
	if ($ct>0) {
		$surname = trim($match[2]);
		if (empty($surname)) $surname = "@N.N.";
		$givenname = trim($match[1]);
		$othername = trim($match[3]);
		if (empty($givenname)&&!empty($othername)) {
			$givenname = $othername;
			$othername = "";
		}

		// Remove any prefixes from given name
		while (preg_match('/^(\w+)\.? +(.*)/', $givenname, $match) && in_array($match[1], $NPFX_accept))
			$givenname=$match[2];

		if (empty($givenname)) $givenname = "@P.N.";
		$name = $surname;
		if (!empty($othername)) $name .= " ".$othername;
		$name .= ", ".$givenname;
	}
	if (!empty($name)) return $name;
	else return "@N.N., @P.N.";
}

/**
 * get the name for a person
 *
 * returns the name in the form Given Name Surname
 * If the <var>$NAME_FROM_GEDCOM</var> variable is true then the name is retrieved from the
 * gedcom record not from the database index.
 * @param string $pid the xref gedcom id of the person
 * @param bool $checkUnknown whether to check for (unknown) before returning
 * @return string the person's name (Given Name Surname)
 */
function get_person_name($pid, $checkUnknown=true) {
	global $NAME_REVERSE;
	global $NAME_FROM_GEDCOM;
	global $indilist;
	global $GEDCOM, $gGedcom;

	$name = "";

	//-- get the name from the gedcom record
	if ($NAME_FROM_GEDCOM) {
		$indirec = find_person_record($pid);
		if (!$indirec) $indirec = find_updated_record($pid);
		$name = get_gedcom_value("NAME", 1, $indirec, '', false);
	} else {
		//-- first check if the person is in the cache
		if ((isset($indilist[$pid]["names"][0][0]))&&($indilist[$pid]["gedfile"]==$gGedcom[$GEDCOM]["id"])) {
			$name = $indilist[$pid]["names"][0][0];
		} else {
			//-- cache missed, so load the person into the cache with the find_person_record function
			//-- and get the name from the cache again
			$gedrec = find_person_record($pid);
			if (empty($gedrec)) {
				$gedrec = find_updated_record($pid);
			}
			if (!empty($gedrec)) {
				$names = get_indi_names($gedrec);
				$name = $names[0][0];
			}
		}
	}

	if ($NAME_REVERSE) $name = reverse_name($name);
	
//	if ($checkUnknown) $name = check_NN($name);
	return $name;
}

/**
 * reverse a name
 * this function will reverse a name for languages that
 * prefer last name first such as hungarian and chinese
 * @param string $name	the name to reverse, must be gedcom encoded as if from the 1 NAME line
 * @return string		the reversed name
 */
function reverse_name($name) {
	$ct = preg_match("~(.*)/(.*)/(.*)~", $name, $match);
	if ($ct>0) {
		$surname = trim($match[2]);
		if (empty($surname)) $surname = "@N.N.";
		$givenname = trim($match[1]);
		$othername = trim($match[3]);
		if (empty($givenname)&&!empty($othername)) {
			$givenname = $othername;
			$othername = "";
		}
		if (empty($givenname)) $givenname = "@P.N.";
		$name = $surname;
		$name .= " ".$givenname;
		if (!empty($othername)) $name .= " ".$othername;
	}

	return $name;
}

/**
 * get the descriptive title of the media object
 *
 * @param string $sid the gedcom xref id for the media to find
 * @return string the title of the source
 */
function get_media_descriptor($id) {
	global $objectlist;
	if ($id=="") return false;

	if (isset($objectlist[$id]["title"])) {
		if (!empty($objectlist[$id]["title"])) return $objectlist[$id]["title"];
		else return $objectlist[$id]["file"];
	} else {
		$gedrec = find_media_record($id);
		if (!empty($gedrec)) {
			if (!empty($objectlist[$id]["title"])) return $objectlist[$id]["title"];
			else return $objectlist[$id]["file"];
		}
	}
	return false;
}

/**
 * get the descriptive title of the source
 *
 * @param string $sid the gedcom xref id for the source to find
 * @return string the title of the source
 */
function get_source_descriptor($sid) {
	global $sourcelist;
	if ($sid=="") return false;

	if (isset($sourcelist[$sid]["name"])) {
		return $sourcelist[$sid]["name"];
	} else {
		$gedrec = find_source_record($sid);
		if (!empty($gedrec)) return $sourcelist[$sid]["name"];
	}
	return false;
}

/**
 * get the descriptive title of the repository
 *
 * @param string $rid the gedcom xref id for the repository to find
 * @return string the title of the repository
 */
function get_repo_descriptor($rid) {
	global $TBLPREFIX, $WORD_WRAPPED_NOTES;
	global $GEDCOM, $repo_id_list;

	if ($rid=="") return false;

	if (isset($repo_id_list[$rid]["name"])) {
		return $repo_id_list[$rid]["name"];
	}
	else {
		$repo_id_list = get_repo_id_list();
		if ((!empty($repo_id_list)) && (isset($repo_id_list[$rid]))) return $repo_id_list[$rid]["name"];
	}
	return false;
}

/**
 * get the additional descriptive title of the source
 *
 * @param string $sid the gedcom xref id for the source to find
 * @return string the additional title of the source
 */
function get_add_source_descriptor($sid) {
	global $TBLPREFIX, $WORD_WRAPPED_NOTES;
	global $GEDCOM, $sourcelist;
	$title = "";
	if ($sid=="") return false;

	$gedrec = find_source_record($sid);
	if (!empty($gedrec)) {
		$ct = preg_match("/\d ROMN (.*)/", $gedrec, $match);
		if ($ct>0) return($match[1]);
		$ct = preg_match("/\d _HEB (.*)/", $gedrec, $match);
		if ($ct>0) return($match[1]);
	}
	return false;
}

/**
 * get the additional descriptive title of the repository
 *
 * @param string $rid the gedcom xref id for the repository to find
 * @return string the additional title of the repository
 */
function get_add_repo_descriptor($rid) {
	global $TBLPREFIX, $WORD_WRAPPED_NOTES;
	global $GEDCOM, $repolist;
	$title = "";
	if ($rid=="") return false;

	$gedrec = find_repo_record($rid);
	if (!empty($gedrec)) {
		$ct = preg_match("/\d ROMN (.*)/", $gedrec, $match);
		if ($ct>0) return($match[1]);
		$ct = preg_match("/\d _HEB (.*)/", $gedrec, $match);
		if ($ct>0) return($match[1]);
	}
	return false;
}

function get_sortable_family_descriptor($fid) {
	global $pgv_lang;
	$parents = find_parents($fid);
	if ($parents["HUSB"]) {
		if (displayDetailsById($parents["HUSB"]) || showLivingNameById($parents["HUSB"]))
			$hname = get_sortable_name($parents["HUSB"]);
		else $hname = $pgv_lang["private"];
	}
	else $hname = "@N.N., @P.N.";
	if ($parents["WIFE"]) {
		if (displayDetailsById($parents["WIFE"]) || showLivingNameById($parents["WIFE"]))
			$wname = get_sortable_name($parents["WIFE"]);
		else $wname = $pgv_lang["private"];
	}
	else $wname = "@N.N., @P.N.";

	if (!empty($hname) && !empty($wname)) $result = check_NN($hname)." + ".check_NN($wname);
	else if (!empty($hname) && empty($wname)) $result = check_NN($hname);
	else if (empty($hname) && !empty($wname)) $result = check_NN($wname);

	return $result;
}

function get_family_descriptor($fid) {
	global $pgv_lang, $NAME_REVERSE;
	$parents = find_parents($fid);
	if ($parents["HUSB"]) {
		if (displayDetailsById($parents["HUSB"]) || showLivingNameById($parents["HUSB"]))
			$hname = get_person_name($parents["HUSB"], false);
		else $hname = $pgv_lang["private"];
	} else {
		if ($NAME_REVERSE) $hname = "@N.N. @P.N.";
		else $hname = "@P.N. @N.N.";
	}
	if ($parents["WIFE"]) {
		if (displayDetailsById($parents["WIFE"]) || showLivingNameById($parents["WIFE"]))
			$wname = get_person_name($parents["WIFE"], false);
		else $wname = $pgv_lang["private"];
	} else {
		if ($NAME_REVERSE) $wname = "@N.N. @P.N.";
		else $wname = "@P.N. @N.N.";
	}
	$result = "";
	if (!empty($hname) && !empty($wname)) $result = check_NN($hname)." + ".check_NN($wname);
	else if (!empty($hname) && empty($wname)) $result = check_NN($hname);
	else if (empty($hname) && !empty($wname)) $result = check_NN($wname);

	return $result;
}

function get_family_add_descriptor($fid) {
	global $pgv_lang;
	$parents = find_parents($fid);
	if ($parents["HUSB"]) {
		if (displayDetailsById($parents["HUSB"]) || showLivingNameById($parents["HUSB"]))
			$hname = get_add_person_name($parents["HUSB"]);
		else $hname = $pgv_lang["private"];
	}
	else $hname = "";
	// handle the additional name of a non existing spouse the same way as of
	// a spouse who does not have an additional name

	if ($parents["WIFE"]) {
		if (displayDetailsById($parents["WIFE"]) || showLivingNameById($parents["WIFE"]))
			$wname = get_add_person_name($parents["WIFE"]);
		else $wname = $pgv_lang["private"];
	}
	else $wname = "";

	if (!empty($hname) && !empty($wname)) $result = $hname . " + " . $wname;
	else if (!empty($hname) && empty($wname)) $result = $hname;
	else if (empty($hname) && !empty($wname)) $result = $wname;
	else $result = "";

	return $result;
}

// -- find and return a given individual's second name in format: firstname lastname
function get_add_person_name($pid) {
	global $NAME_FROM_GEDCOM;

	//-- get the name from the indexes
	$record = find_person_record($pid);
	$name_record = get_sub_record(1, "1 NAME", $record);
	$name = get_add_person_name_in_record($name_record);
	$name = check_NN($name); 
		
	return $name;
}

function get_add_person_name_in_record($name_record, $keep_slash=false) {
	global $NAME_REVERSE;
	global $NAME_FROM_GEDCOM;

	// Check for ROMN name
	$romn = preg_match("/(2 ROMN (.*)|2 _HEB (.*))/", $name_record, $romn_match);
	if ($romn > 0){
		if ($keep_slash) return trim($romn_match[count($romn_match)-1]);
		$names = preg_split("/\//", $romn_match[count($romn_match)-1]);
		if (count($names)>1) {
			if ($NAME_REVERSE) {
				$name = trim($names[1])." ".trim($names[0]);
			}
			else {
				$name = trim($names[0])." ".trim($names[1]);
			}
		}
		else $name = trim($names[0]);
	}
	else $name = "";

	if ($NAME_REVERSE) $name = reverse_name($name);
	return $name;
}

// -- find and return a given individual's second name in sort format: familyname, firstname
function get_sortable_add_name($pid) {
	global $NAME_REVERSE;
	global $NAME_FROM_GEDCOM;

	//-- get the name from the indexes
	$record = find_person_record($pid);
	$name_record = get_sub_record(1, "1 NAME", $record);

	// Check for ROMN name
	$romn = preg_match("/(2 ROMN (.*)|2 _HEB (.*))/", $name_record, $romn_match);
	if ($romn > 0){
		$names = preg_split("/\//", $romn_match[count($romn_match)-1]);
		if ($names[0] == "") $names[0] = "@P.N.";	//-- MA
		if (empty($names[1])) $names[1] = "@N.N.";	//-- MA
		if (count($names)>1) {
			$fullname = trim($names[1]).",";
			$fullname .= ",# ".trim($names[0]);
			if (count($names)>2) $fullname .= ",% ".trim($names[2]);
		}
		else $fullname=$romn_match[1];
		if (!$NAME_REVERSE) {
			$name = trim($names[1]).", ".trim($names[0]);
		}
		else {
			$name = trim($names[0])." ,".trim($names[1]);
		}
	}
	else $name = get_sortable_name($pid);

	return $name;
}

/**
 * strip name prefixes
 *
 * this function strips the prefixes of lastnames
 * get rid of jr. Jr. Sr. sr. II, III and van, van der, de lowercase surname prefixes
 * a . and space must be behind a-z to ensure shortened prefixes and multiple prefixes are removed
 * @param string $lastname	The name to strip
 * @return string	The updated name
 */
function strip_prefix($lastname){
	$name = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^([a-z]{1,4}[\. \_\-\(\[])+/"), array(",",",",""), $lastname);
	$name = trim($name);
	if ($name=="") return $lastname;
	return $name;
}

/**
 * Extract the surname from a name
 *
 * This function will extract the surname from an individual name in the form
 * Surname, Given Name
 * All surnames are stored in the global $surnames array
 * It will only get the surnames that start with the letter $alpha
 * For names like van den Burg, it will only return the "Burg"
 * It will work if the surname is all lowercase
 * @param string $indiname	the name to extract the surname from
 */
function extract_surname($indiname, $count=true) {
	global $surnames, $alpha, $surname, $show_all, $i, $testname;

	if (!isset($testname)) $testname="";

	$nsurname = "";
	//-- get surname from a standard name
	if (preg_match("~/([^/]*)/~", $indiname, $match)>0) {
		$nsurname = trim($match[1]);
	}
	//-- get surname from a sortable name
	else {
		$names = preg_split("/,/", $indiname);
		if (count($names)==1) $nsurname = "@N.N.";
		else $nsurname = trim($names[0]);
		$nsurname = preg_replace(array("/ [jJsS][rR]\.?/", "/ I+/"), array("",""), $nsurname);
	}
	if ($count) surname_count($nsurname);
	return $nsurname;
}

/**
 * add a surname to the surnames array for counting
 * @param string $nsurname
 * @return string
 */
function surname_count($nsurname) {
	global $surnames, $alpha, $surname, $show_all, $i, $testname;
	// Match names with chosen first letter
	$lname = strip_prefix($nsurname);
	if (empty($lname)) $lname = $nsurname;
	$sort_letter=get_first_letter($lname);
		$tsurname = preg_replace(array("/ [jJsS][rR]\.?/", "/ I+/"), array("",""), $nsurname);
		$tsurname = str2upper($tsurname);
		if (empty($surname) || (str2upper($surname)==$tsurname)) {
			if (!isset($surnames[$tsurname])) {
				$surnames[$tsurname] = array();
				$surnames[$tsurname]["name"] = $nsurname;
				$surnames[$tsurname]["match"] = 1;
				$surnames[$tsurname]["fam"] = 1;
				$surnames[$tsurname]["alpha"] = get_first_letter($tsurname);
			}
			else {
				$surnames[$tsurname]["match"]++;
				if ($i==0 || $testname != $tsurname) $surnames[$tsurname]["fam"]++;
			}
			if ($i==0) $testname = $tsurname;
		}
		return $nsurname;
}

/**
 * get first letter
 *
 * get the first letter of a UTF-8 string
 * @param string $text	the text to get the first letter from
 * @return string 	the first letter UTF-8 encoded
 */
function get_first_letter($text, $import=false) {
	global $LANGUAGE, $CHARACTER_SET;
	global $MULTI_LETTER_ALPHABET, $digraph, $trigraph, $quadgraph, $digraphAll, $trigraphAll, $quadgraphAll;

	$danishFrom = array("AA", "Aa", "AE", "Ae", "OE", "Oe", "aa", "ae", "oe");
	$danishTo   = array("Å", "Å", "Æ", "Æ", "Ø", "Ø", "å", "æ", "ø");

	$text=trim(str2upper($text));
	if (!$import) {
		if ($LANGUAGE=="danish" || $LANGUAGE=="norwegian") {
			$text = str_replace($danishFrom, $danishTo, $text);
		}
	}

	$multiByte = false;
	// Look for 4-byte combinations that should be treated as a single character
	$letter = substr($text, 0, 4);
	if ($import) {
		if (isset($quadgraphAll[$letter])) $multiByte = true;
	} else {
		if (isset($quadgraph[$letter])) $multiByte = true;
	}

	if (!$multiByte) {
		// 4-byte combination isn't listed: try 3-byte combination
		$letter = substr($text, 0, 3);
		if ($import) {
			if (isset($trigraphAll[$letter])) $multiByte = true;
		} else {
			if (isset($trigraph[$letter])) $multiByte = true;
		}
	}

	if (!$multiByte) {
		// 3-byte combination isn't listed: try 2-byte combination
		$letter = substr($text, 0, 2);
		if ($import) {
			if (isset($digraphAll[$letter])) $multiByte = true;
		} else {
			if (isset($digraph[$letter])) $multiByte = true;
		}
	}

	if (!$multiByte) {
		// All lists failed: try for a UTF8 character
		$charLen = 1;
		$letter = substr($text, 0, 1);
		if ((ord($letter) & 0xE0) == 0xC0) $charLen = 2;		// 2-byte sequence
		if ((ord($letter) & 0xF0) == 0xE0) $charLen = 3;		// 3-byte sequence
		if ((ord($letter) & 0xF8) == 0xF0) $charLen = 4;		// 4-byte sequence
		$letter = substr($text, 0, $charLen);
	}
        if ($letter=="/") $letter="@"; //where has @P.N. vanished from names with a null firstname?
	
	return $letter;
}

/**
 * This function replaces @N.N. and @P.N. with the language specific translations
 * @param mixed $names	$names could be an array of name parts or it could be a string of the name
 * @return string
 */
function check_NN($names) {
	global $pgv_lang, $UNDERLINE_NAME_QUOTES;
	global $unknownNN, $unknownPN;

	$fullname = "";
return $fullname;

	if (!is_array($names)){
		$lang = whatLanguage($names);
		$NN = $unknownNN[$lang];
		$names = stripslashes($names);
		$names = preg_replace(array("~ /~","~/,~","~/~"), array(" ", ",", " "), $names);
		$names = preg_replace(array("/@N.N.?/","/@P.N.?/"), array($unknownNN[$lang],$unknownPN[$lang]), trim($names));
		//-- underline names with a * at the end
		//-- see this forum thread http://sourceforge.net/forum/forum.php?thread_id=1223099&forum_id=185165
		if ($UNDERLINE_NAME_QUOTES) {
			$names = preg_replace("/\"(.+)\"/", "<span class=\"starredname\">$1</span>", $names);
		}
		$names = preg_replace("/([^ ]+)\*/", "<span class=\"starredname\">$1</span>", $names);
		return $names;
	}
	if (count($names) == 2 && stristr($names[0], "@N.N") && stristr($names[1], "@N.N")){
		$fullname = $pgv_lang["NN"]. " + ". $pgv_lang["NN"];
	}
	else {
		for($i=0; $i<count($names); $i++) {
			$lang = whatLanguage($names[$i]);
			$unknown = false;
			if (stristr($names[$i], "@N.N")) {
				$unknown = true;
				$names[$i] = preg_replace("/@N.N.?/", $unknownNN[$lang], trim($names[$i]));
			}
			if (stristr($names[$i], "@P.N")) $names[$i] = $unknownPN[$lang];
			if ($i==1 && $unknown && count($names)==3) $fullname .= ", ";
			else if ($i==2 && $unknown && count($names)==3) $fullname .= " + ";
				else if ($i==2 && stristr($names[2], "Individual ") && count($names) == 3) $fullname .= " + ";
			else if ($i==2 && count($names)>3) $fullname .= " + ";
				else $fullname .= ", ";
				$fullname .= trim($names[$i]);
			}
		}
	$fullname = trim($fullname);
	if (substr($fullname,-1)==",") $fullname = substr($fullname,0,strlen($fullname)-1);
	if (substr($fullname,0,2)==", ") $fullname = substr($fullname,2);
	$fullname = trim($fullname);
	if (empty($fullname)) return $pgv_lang["NN"];

	return $fullname;
}

// Returns 1 if $string is valid 7 bit ASCII and 0 otherwise.
function is_7bitascii($string) {
	return preg_match('/^(?:[\x09\x0A\x0D\x20-\x7E])*$/', $string);
}

// Returns 1 if $string is valid UTF-8 and 0 otherwise.
// See http://w3.org/International/questions/qa-forms-utf-8.html
function is_utf8($string) {
	return preg_match('/^(?:[\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$/', $string);
}

/**
 * Put all characters in a string in lowercase
 *
 * This function is a replacement for strtolower() and will put all characters in lowercase
 *
 * @author	eikland
 * @param	string $value the text to be converted to lowercase
 * @return	string $value_lower the converted text in lowercase
 * @todo look at function performance as it is much slower than strtolower
 */
function str2lower($value) {
	global $ALPHABET_upper, $ALPHABET_lower;
	static $all_ALPHABET_upper, $all_ALPHABET_lower;

	// Input may be 7 or 8 bit ASCII and we need UTF8 to work properly
	if (is_7bitascii($value))
		return strtolower($value);
	else if (!is_utf8($value))
		$value=utf8_encode($value);

	//-- get all of the upper and lower alphabets as a string
	if (!isset($all_ALPHABET_upper)) {
		$all_ALPHABET_upper = "";
		$all_ALPHABET_lower = "";
		foreach ($ALPHABET_upper as $l => $up_alphabet){
			$lo_alphabet = $ALPHABET_lower[$l];
			$ll = strlen($lo_alphabet);
			$ul = strlen($up_alphabet);
			if ($ll < $ul) $lo_alphabet .= substr($up_alphabet, $ll);
			if ($ul < $ll) $up_alphabet .= substr($lo_alphabet, $ul);
			$all_ALPHABET_lower .= $lo_alphabet;
			$all_ALPHABET_upper .= $up_alphabet;
		}
	}

	$len=strlen($value);
	for ($i=0; $i<$len;) {
		$letter=ord($value[$i]);
		if ($letter < 0x80) { // 7 bit ASCII
			$value[$i]=strtolower($value[$i]);
			$i++;
		} else if ($letter < 0xC0) { // 8 bit ASCII
			$pos=strpos($all_ALPHABET_upper, $value[$i]);
			if ($pos===false)
				$i++;
			else
				$value[$i++]=$all_ALPHABET_lower[$pos];
		} else if ($letter < 0xE0) { // 2 byte UNICODE
			$pos=strpos($all_ALPHABET_upper, $value[$i].$value[$i+1]);
			if ($pos===false)
				$i+=2;
			else {
				$value[$i++]=$all_ALPHABET_lower[$pos++];
				$value[$i++]=$all_ALPHABET_lower[$pos  ];
			}
		} else if ($letter < 0xF0) { // 3 byte UNICODE
			$pos=strpos($all_ALPHABET_upper, $value[$i].$value[$i+1].$value[$i+2]);
			if ($pos===false)
				$i+=3;
			else {
				$value[$i++]=$all_ALPHABET_lower[$pos++];
				$value[$i++]=$all_ALPHABET_lower[$pos++];
				$value[$i++]=$all_ALPHABET_lower[$pos  ];
			}
		}	else { // 4 byte UNICODE
			$pos=strpos($all_ALPHABET_upper, $value[$i].$value[$i+1].$value[$i+2].$value[$i+3]);
			if ($pos===false)
				$i+=4;
			else	{
				$value[$i++]=$all_ALPHABET_lower[$pos++];
				$value[$i++]=$all_ALPHABET_lower[$pos++];
				$value[$i++]=$all_ALPHABET_lower[$pos++];
				$value[$i++]=$all_ALPHABET_lower[$pos  ];
			}
		}
	}
	return $value;
}
// END function str2lower

/**
 * Put all characters in a string in uppercase
 *
 * This function is a replacement for strtoupper() and will put all characters in uppercase
 *
 * @author	botak
 * @param	string $value the text to be converted to uppercase
 * @return	string $value_upper the converted text in uppercase
 * @todo look at function performance as it is much slower than strtoupper
 */
function str2upper($value) {
	global $ALPHABET_upper, $ALPHABET_lower;
	static $all_ALPHABET_upper, $all_ALPHABET_lower;

	// Input may be 7 or 8 bit ASCII and we need UTF8 to work properly
	if (is_7bitascii($value))
		return strtoupper($value);
	else if (!is_utf8($value))
		$value=utf8_encode($value);

	//-- get all of the upper and lower alphabets as a string
	if (!isset($all_ALPHABET_upper)) {
		$all_ALPHABET_upper = "";
		$all_ALPHABET_lower = "";
		foreach ($ALPHABET_upper as $l => $up_alphabet){
			$lo_alphabet = $ALPHABET_lower[$l];
			$ll = strlen($lo_alphabet);
			$ul = strlen($up_alphabet);
			if ($ll < $ul) $lo_alphabet .= substr($up_alphabet, $ll);
			if ($ul < $ll) $up_alphabet .= substr($lo_alphabet, $ul);
			$all_ALPHABET_lower .= $lo_alphabet;
			$all_ALPHABET_upper .= $up_alphabet;
		}
	}

	$len=strlen($value);
	for ($i=0; $i<$len;) {
		$letter=ord($value[$i]);
		if ($letter < 0x80) { // 7 bit ASCII
			$value[$i]=strtoupper($value[$i]);
			$i++;
		} else if ($letter < 0xC0) { // 8 bit ASCII
			$pos=strpos($all_ALPHABET_lower, $value[$i]);
			if ($pos===false)
				$i++;
			else
				$value[$i++]=$all_ALPHABET_upper[$pos];
		} else if ($letter < 0xE0) { // 2 byte UNICODE
			$pos=strpos($all_ALPHABET_lower, $value[$i].$value[$i+1]);
			if ($pos===false)
		 		$i+=2;
			else {
				$value[$i++]=$all_ALPHABET_upper[$pos++];
				$value[$i++]=$all_ALPHABET_upper[$pos  ];
			}
		} else if ($letter < 0xF0) { // 3 byte UNICODE
			$pos=strpos($all_ALPHABET_lower, $value[$i].$value[$i+1].$value[$i+2]);
			if ($pos!==false) {
				$value[$i++]=$all_ALPHABET_upper[$pos++];
				$value[$i++]=$all_ALPHABET_upper[$pos++];
				$value[$i++]=$all_ALPHABET_upper[$pos  ];
			} else
				$i+=3;
		}	else { // 4 byte UNICODE
			$pos=strpos($all_ALPHABET_lower, $value[$i].$value[$i+1].$value[$i+2].$value[$i+3]);
			if ($pos!==false) {
				$value[$i++]=$all_ALPHABET_upper[$pos++];
				$value[$i++]=$all_ALPHABET_upper[$pos++];
				$value[$i++]=$all_ALPHABET_upper[$pos++];
				$value[$i++]=$all_ALPHABET_upper[$pos  ];
			} else
				$i+=4;
		}
	}
	return $value;
}
// END function str2upper


/**
 * Convert a string to UTF8
 *
 * This function is a replacement for utf8_decode()
 *
 * @author	http://www.php.net/manual/en/function.utf8-decode.php
 * @param	string $in_str the text to be converted
 * @return	string $new_str the converted text
 */
function smart_utf8_decode($in_str)
{
	$new_str = html_entity_decode(htmlentities($in_str, ENT_COMPAT, 'UTF-8'));
	$new_str = str_replace("&oelig;", "\x9c", $new_str);
	$new_str = str_replace("&OElig;", "\x8c", $new_str);
	return $new_str;
	/**
	// Replace ? with a unique string
	$new_str = str_replace("?", "q0u0e0s0t0i0o0n", $in_str);

	// Try the utf8_decode
	$new_str=utf8_decode($new_str);

	// if it contains ? marks
	if (strpos($new_str,"?") !== false)
	{
	// Something went wrong, set new_str to the original string.
	$new_str=$in_str;
	}
	else
	{
	// If not then all is well, put the ?-marks back where is belongs
	$new_str = str_replace("q0u0e0s0t0i0o0n", "?", $new_str);
	}
	return $new_str;
	**/
}

/**
 * get an array of names from an indivdual record
 * @param string $indirec	The raw individual gedcom record
 * @return array	The array of individual names
 */
function get_indi_names($indirec, $import=false) {
	global $NAME_REVERSE;
	$names = array();
	//-- get all names
	$namerec = get_sub_record(1, "1 NAME", $indirec, 1);
	if (empty($namerec)) $names[] = array("@P.N /@N.N./", "@", "@N.N.", "A");
	else {
		$j = 1;
		while(!empty($namerec)) {
			$name = get_gedcom_value("NAME", 1, $namerec, '', false);
			$name = preg_replace('/\/\//','/@N.N./', $name); // Missing SURN=>@N.N.
			if ($NAME_REVERSE) { // Missing GIVN=>@P.N.
				$name=preg_replace('/\/$/', '/ @P.N./', $name);
			} else {
				$name=preg_replace('/^\//', '@P.N. /', $name);
			}
			$surname = extract_surname($name, false);
			if (empty($surname)) $surname = "@N.N.";
			//-- all ____ names get changed to @N.N.
			if (preg_match("/^_+$/", $surname)>0) $surname="@N.N.";
			$lname = preg_replace("/^[a-z0-9 '\.\-\_\(\[]+/", "", $surname);
			if (empty($lname)) $lname = $surname;
			$letter = get_first_letter($lname, $import);
			$letter = str2upper($letter);
			if (empty($letter)) $letter = "@";
			if (preg_match("~/~", $name)==0) $name .= " /@N.N./";
			$names[] = array($name, $letter, $surname, "A");
			//-- check for _HEB or ROMN name sub tags
			$addname = get_add_person_name_in_record($namerec, true);
			if (!empty($addname)) {
				$surname = extract_surname($addname, false);
				if (empty($surname)) $surname = "@N.N.";
				//-- remove these characters so that they do not get selected as the first letter
				$lname = preg_replace("/^[a-z0-9 '\.\-\_\(\[]+/", "", $surname);
				if (empty($lname)) $lname = $surname;
				$letter = get_first_letter($lname, $import);
				$letter = str2upper($letter);
				if (empty($letter)) $letter = "@";
				if (preg_match("~/~", $addname)==0) $addname .= " /@N.N./";
				$names[] = array($addname, $letter, $surname, "A");
			}
			//-- check for _MARNM name subtags
			$ct = preg_match_all("/\d _MARNM (.*)/", $namerec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$marriedname = trim($match[$i][1]);
				$surname = extract_surname($marriedname, false);
				if (empty($surname)) $surname = "@N.N.";
				$lname = preg_replace("/^[a-z0-9 '\.\-\_\(\[]+/", "", $surname);
				if (empty($lname)) $lname = $surname;
				$letter = get_first_letter($lname, $import);
				$letter = str2upper($letter);
				if (empty($letter)) $letter = "@";
				if (preg_match("~/~", $marriedname)==0) $marriedname .= " /@N.N./";
				$names[] = array($marriedname, $letter, $surname, "C");
			}
			//-- check for _AKA name subtags
			$ct = preg_match_all("/\d _AKA (.*)/", $namerec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$marriedname = trim($match[$i][1]);
				$surname = extract_surname($marriedname, false);
				if (empty($surname)) $surname = "@N.N.";
				$lname = preg_replace("/^[a-z0-9 '\.\-\_\(\[]+/", "", $surname);
				if (empty($lname)) $lname = $surname;
				$letter = get_first_letter($lname, $import);
				$letter = str2upper($letter);
				if (empty($letter)) $letter = "@";
				if (preg_match("~/~", $marriedname)==0) $marriedname .= " /@N.N./";
				$names[] = array($marriedname, $letter, $surname, "A");
			}
			$j++;
			$namerec = get_sub_record(1, "1 NAME", $indirec, $j);
		}
	}
	return $names;
}

/**
 * determine the Daitch-Mokotoff Soundex code for a name
 * @param string $name	The name
 * @return array		The array of codes
 */
function DMSoundex($name, $option = "") {
return;
	global $PGV_BASEDIRECTORY, $dmsoundexlist, $dmcoding, $maxchar, $INDEX_DIRECTORY, $cachecount, $cachename;

	// If the code tables are not loaded, reload! Keep them global!
	if (!isset($dmcoding)) {
		$fname = $PGV_BASEDIRECTORY."includes/dmarray.full.utf-8.php";
		require($fname);
	}

	// Load the previously saved cachefile and return. Keep the cache global!

	if ($option == "opencache") {
		$cachename = $INDEX_DIRECTORY."DM".date("mdHis", filemtime($fname)).".dat";
		if (file_exists($cachename)) {
			$fp = fopen($cachename, "r");
			$fcontents = fread($fp, filesize($cachename));
			fclose($fp);
			$dmsoundexlist = unserialize($fcontents);
			unset($fcontents);
			$cachecount = count($dmsoundexlist);
			return;
		}
		else {
			$dmsoundexlist = array();
			// clean up old cache
			$handle = opendir($INDEX_DIRECTORY);
			while (($file = readdir ($handle)) != false) {
				if ((substr($file, 0, 2) == "DM") && (substr($file, -4) == ".dat")) unlink($INDEX_DIRECTORY.$file);
			}
			closedir($handle);
			return;
		}
	}

	// Write the cache to disk after use. If nothing is added, just return.
	if ($option == "closecache") {
		if (count($dmsoundexlist) == $cachecount) return;
		$fp = @fopen($cachename, "w");
		if ($fp) {
			@fwrite($fp, serialize($dmsoundexlist));
			@fclose($fp);
			return;
		}
	}

	// Check if in cache
	$name = str2upper($name);
	$name = substr(trim($name), 0, 30);
	if (isset($dmsoundexlist[$name])) return $dmsoundexlist[$name];

	// Define the result array and set the first (empty) result
	$result = array();
	$result[0][0] = "";
	$rescount = 1;
	$nlen = strlen($name);
	$npos = 0;


	// Loop here through the characters of the name
	while($npos < $nlen) {
		// Check, per length of characterstring, if it exists in the array.
		// Start from max to length of 1 character
		$code = array();
		for ($i=$maxchar; $i>=0; $i--) {
			// Only check if not read past the last character in the name
			if (($npos + $i) <= $nlen) {
				// See if the substring exists in the coding array
				$element = substr($name,$npos,$i);
				// If found, add the sets of results to the code array for the letterstring
				if (isset($dmcoding[$element])) {
					$dmcount = count($dmcoding[$element]);
					// Loop here through the codesets
					// first letter? Then store the first digit.
					if ($npos == 0) {
						// Loop through the sets of 3
						for ($k=0; $k<$dmcount/3; $k++) {
							$c = $dmcoding[$element][$k*3];
							// store all results, cleanup later
							$code[] = $c;
						}
						break;
					}
					// before a vowel? Then store the second digit
					// Check if the code for the next letter exists
					if ((isset($dmcoding[substr($name, $npos + $i + 1)]))) {
						// See if it's a vowel
						if ($dmcoding[substr($name, $npos + $i + 1)] == 0) {
							// Loop through the sets of 3
							for ($k=0; $k<$dmcount/3; $k++) {
								$c = $dmcoding[$element][$k*3+1];
								// store all results, cleanup later
								$code[] = $c;
							}
							break;
						}
					}
					// Do this in all other situations
					for ($k=0; $k<$dmcount/3; $k++) {
						$c = $dmcoding[$element][$k*3+2];
						// store all results, cleanup later
						$code[] = $c;
					}
					break;
				}
			}
		}
		// Store the results and multiply if more found
		if (isset($dmcoding[$element])) {
			// Add code to existing results

			// Extend the results array if more than one code is found
			for ($j=1; $j<count($code); $j++) {
				$rcnt = count($result);
				// Duplicate the array
				for ($k=0; $k<$rcnt; $k++) {
					$result[] = $result[$k];
				}
			}

			// Add the code to the existing strings
			// Repeat for every code...
			for ($j=0; $j<count($code); $j++) {
				// and add it to the appropriate block of array elements
				for ($k=0; $k<$rescount; $k++) {
					$result[$j * $rescount + $k][] = $code[$j];
				}
			}
			$rescount=count($result);
			$npos = $npos + strlen($element);
		}
		else {
			// The code was not found. Ignore it and continue.
			$npos = $npos + 1;
		}
	}

	// Kill the doubles and zero's in each result
	// Do this for every result
	$ctr = count($result);
	for ($i=0; $i<$ctr; $i++) {
		$j=1;
		$res = $result[$i][0];
		// and check every code in the result.
		// codes are stored separately in array elements, to keep
		// distinction between 6 and 66.

		$cti = count($result[$i]);
		while($j<$cti) {

//  Zeroes to remain in the Soundex result
			if ((($result[$i][$j-1] != $result[$i][$j]) && ($result[$i][$j] != -1)) || $result[$i][$j] == 0) {

				$res .= $result[$i][$j];
			}
			$j++;
		}
		// Fill up to 6 digits and store back in the array
		$result[$i] = substr($res."000000", 0, 6);
	}

	// Kill the double results in the array
	$result=array_flip(array_flip($result));

	// Store in cache and return
	$dmsoundexlist[$name] = $result;
	return $result;
}

?>

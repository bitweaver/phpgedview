<?php
/**
 * Name Specific Functions
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_NAME_PHP', '');

/**
 * Get array of common surnames from index
 *
 * This function returns a simple array of the most common surnames
 * found in the individuals list.
 * @param int $min the number of times a surname must occur before it is added to the array
 */
function get_common_surnames_index($ged) {
	global $GEDCOMS;

	if (empty($GEDCOMS[$ged]["commonsurnames"])) store_gedcoms();
	$surnames = array();
	if (empty($GEDCOMS[$ged]["commonsurnames"]) || ($GEDCOMS[$ged]["commonsurnames"]==",")) return $surnames;
	$names = preg_split("/[,;]/", $GEDCOMS[$ged]["commonsurnames"]);
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
	global $GEDCOM, $CONFIGURED, $GEDCOMS, $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $pgv_lang, $HNN, $ANN;

	$surnames = array();
	if (!$CONFIGURED || !adminUserExists() || (count($GEDCOMS)==0) || (!check_for_import($GEDCOM))) return $surnames;
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
				$topsurns[$surname["name"]] = $surname;
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
	uksort($topsurns, "stringsort");
	return $topsurns;
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
 * get first letter
 *
 * get the first letter of a UTF-8 string
 * @param string $text	the text to get the first letter from
 * @return string 	the first letter UTF-8 encoded
 */
function get_first_letter($text, $import=false) {
	global $LANGUAGE, $CHARACTER_SET;
	global $digraph, $trigraph, $quadgraph, $digraphAll, $trigraphAll, $quadgraphAll;

	$danishFrom = array("AA", "Aa", "AE", "Ae", "OE", "Oe", "aa", "ae", "oe");
	$danishTo   = array("Å", "Å", "Æ", "Æ", "Ø", "Ø", "å", "æ", "ø");

	$text=trim(UTF8_strtoupper($text));
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
 * determine the Daitch-Mokotoff Soundex code for a word
 * @param string $name	The name
 * @return array		The array of codes
 * @author G. Kroll (canajun2eh), after a previous implementation by Boudewijn Sjouke
 */
function DMSoundex($name) {
	global $transformNameTable, $dmsounds, $maxchar;

	// If the code tables are not loaded, reload! Keep them global!
	if (!defined('PGV_DMSOUNDS_UTF8_PHP')) {
		require ( PHPGEDVIEW_PKG_PATH.'includes/dmsounds_UTF8.php' );
	}

	// Apply special transformation rules to the input string
	$name = UTF8_strtoupper($name);
	foreach($transformNameTable as $transformRule) {
		$name = ereg_replace($transformRule[0], $transformRule[1], $name);
	}

	// Initialize
	$nameLanguage = whatLanguage($name);
	if ($nameLanguage == 'hebrew' || $nameLanguage == 'arabic') $noVowels = true;
	else $noVowels = false;
	$lastPos = strlen($name) - 1;
	$currPos = 0;
	$state = 1;						// 1: start of input string, 2: before vowel, 3: other
	$result = array();				// accumulate complete 6-digit D-M codes here
	$partialResult = array();		// accumulate incomplete D-M codes here
	$partialResult[] = array('!');	// initialize 1st partial result  ('!' stops "duplicate sound" check)

	// Loop through the input string.
	// Stop when the string is exhausted or when no more partial results remain
	while (count($partialResult) !=0  && $currPos <= $lastPos) {
		// Find the DM coding table entry for the chunk at the current position
		$thisEntry = substr($name, $currPos, $maxchar);		// Get maximum length chunk
		while ($thisEntry != '') {
			if (isset($dmsounds[$thisEntry])) break;
			$thisEntry = substr($thisEntry, 0, -1);			// Not in table: try a shorter chunk
		}
		if ($thisEntry == '') {
			$currPos ++;			// Not in table: advance pointer to next byte
			continue;				// and try again
		}

		$soundTableEntry = $dmsounds[$thisEntry];
		$workingResult = $partialResult;
		$partialResult = array();
		$currPos += strlen($thisEntry);

		if ($state != 1) {			// Not at beginning of input string
			if ($currPos <= $lastPos) {
				// Determine whether the next chunk is a vowel
				$nextEntry = substr($name, $currPos, $maxchar);		// Get maximum length chunk
				while ($nextEntry != '') {
					if (isset($dmsounds[$nextEntry])) break;
					$nextEntry = substr($nextEntry, 0, -1);			// Not in table: try a shorter chunk
				}
			} else $nextEntry = '';
			if ($nextEntry != '' && $dmsounds[$nextEntry][0] != '0') $state = 2;	// Next chunk is a vowel
			else $state = 3;
		}

		while ($state < count($soundTableEntry)) {
			if ($soundTableEntry[$state] == '') {		// empty means 'ignore this sound in this state'
				foreach($workingResult as $workingEntry) {
					$tempEntry = $workingEntry;
					$tempEntry[count($tempEntry)-1] .= '!';		// Prevent false 'doubles'
					$partialResult[] = $tempEntry;
				}
			} else {
				foreach($workingResult as $workingEntry) {
					if ($soundTableEntry[$state] !== $workingEntry[count($workingEntry)-1]) {
						// Incoming sound isn't a duplicate of the previous sound
						$workingEntry[] = $soundTableEntry[$state];
					} else {
						// Incoming sound is a duplicate of the previous sound
						// For Hebrew and Arabic, we need to create a pair of D-M sound codes,
						// one of the pair with only a single occurrence of the duplicate sound,
						// the other with both occurrences
						if ($noVowels) {
//							$partialResult[] = $workingEntry;
							$workingEntry[] = $soundTableEntry[$state];
						}
					}
					if (count($workingEntry) < 7) $partialResult[] = $workingEntry;
					else {
						// This is the 6th code in the sequence
						// We're looking for 7 entries because the first is '!' and doesn't count
						$tempResult = str_replace('!', '', implode('', $workingEntry)) . '000000';
						$result[] = substr($tempResult, 0, 6);
					}
				}
			}
			$state = $state + 3;	// Advance to next triplet while keeping the same basic state
		}
	}

	// Zero-fill and copy all remaining partial results
	foreach ($partialResult as $workingEntry) {
		$tempResult = str_replace('!', '', implode('', $workingEntry)) . '000000';
		$result[] = substr($tempResult, 0, 6);
	}

	$result = array_flip(array_flip($result));		// Kill the double results in the array

	// We're done.  All that's left is to sort the result
	sort($result);
	return $result;
}

// Wrapper function for soundex function.  Return a colon separated list of values.
function soundex_std($text) {
	Character_Substitute($text);
	$words=explode(' ', $text);
	$soundex_array=array();
	foreach ($words as $word) {
		if ($word) {
			$soundex_array[]=soundex($word);
		}
	}
	if (count($words)>1) {
		$soundex_array[]=soundex(strtr($text, ' ', ''));
	}
	return implode(':', array_unique($soundex_array));
}

// Wrapper function for soundex function.  Return a colon separated list of values.
function soundex_dm($text) {
	Character_Substitute($text);
	$words=explode(' ', $text);
	$soundex_array=array();
	$combined = "";
	foreach ($words as $word) {
		if ($word) {
			$soundex_array=array_merge($soundex_array, DMSoundex($word));
		}
	}
	if (count($words)>1) {
		$soundex_array=array_merge($soundex_array, DMSoundex(strtr($text, ' ', '')));
	}
	return implode(":", array_unique($soundex_array));
}

?>

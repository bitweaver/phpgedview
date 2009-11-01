<?php
/**
 * RTL Functions
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
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
 * @version $Id: functions_rtl.php,v 1.4 2009/11/01 12:11:27 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_RTL_PHP', '');

$SpecialChar = array(' ','.',',','"','\'','/','\\','|',':',';','+','&','#','@','-','=','*','%','!','?','$','<','>',"\n");
$SpecialPar = array('(',')','[',']','{','}');
$SpecialNum  = array('0','1','2','3','4','5','6','7','8','9');

$RTLOrd = array(215,216,217,218,219);


/**
 * $HNN and $ANN are used in
 * RTLUndefined, check_NN, get_common_surnames, print_block_name_top10
 *
 */

$HNN = "\x28\xd7\x9c\xd7\x90\x20\xd7\x99\xd7\x93\xd7\x95\xd7\xa2\x29";
$ANN = "\x28\xd8\xba\xd9\x8a\xd8\xb1\x20\xd9\x85\xd8\xb9\xd8\xb1\xd9\x88\xd9\x81\x29";


function getLRM(){
	return "&lrm;";
}

function getRLM(){
	return "&rlm;";
}

/**
 * This function strips &lrm; and &rlm; from the input string.  It should be used for all
 * text that has been passed through the PrintReady() function before that text is stored
 * in the database.  The database should NEVER contain these characters.
 *
 * @param 	string	The string from which the &lrm; and &rlm; characters should be stripped
 * @return	string	The input string, with &lrm; and &rlm; stripped
 */
function stripLRMRLM($inputText) {
	return str_replace(array(PGV_UTF8_LRM, PGV_UTF8_RLM, "&lrm;", "&rlm;", "&LRM;", "&RLM;"), "", $inputText);
}

/**
 * This function encapsulates all RTL texts in the input with <span dir='rtl'> and </span>
 *
 * @param 	string	Raw input
 * @param	string	Directionality
 * @return	string	The string with all texts encapsulated as required
 */
function spanRTL($inputText, $direction="RTL") {
	switch ($direction) {
	case 'LTR':
		break;
	case 'BOTH':
		break;
	DEFAULT:
		break;
	}
	// Specifications haven't been finalized:  do nothing for now
	return $inputText;
}


/**
 * This function:
 *		encapsulates all LTR texts in the input with <span dir='ltr'> and </span>
 *		encapsulates all RTL texts in the input with <span dir='rtl'> and </span>
 *
 * @param 	string	Raw input
 * @return	string	The string with all texts encapsulated as required
 */
function spanLTRRTL($inputText) {
	// Specifications haven't been finalized:  do nothing for now
	// Not sure if this will actually be needed
	return $inputText;
}

/**
 * Determine alphabet of input character
 *
 * This function inspects the input character to determine which alphabet it belongs to.
 * The test for Vietnamese is not 100% accurate, since Vietnamese borrows from the French
 * alphabet.  This results in some Vietnamese characters being identified as "other".
 *
 * @param	string	Input character
 * @return	string	Name of the alphabet
 */
function whatAlphabet($char) {
	global $UTF8_ranges;

	$ordinal = UTF8_ord($char);

	$language = "none";
	foreach ($UTF8_ranges as $UTF8_range) {
		if ($ordinal < $UTF8_range[1]) break;
		if (($ordinal >= $UTF8_range[1]) && ($ordinal <= $UTF8_range[2])) {
			$language = $UTF8_range[0];
			break;
		}
	}

	return $language;
}

/**
 * Determine language of input string
 *
 * This function inspects the input string to determine its language.  Except for Vietnamese,
 * when the input string contains characters from more than one alphabet, this function will
 * return "other".  For Vietnamese, if any characters of the input string are "vietnamese" and
 * the only other characters are of language "other", the result is "vietnamese".
 *
 * @param	string	Input string
 * @return	string	Name of the language
 */
function whatLanguage($string) {
	$string = preg_replace(array("/@N.N.?/","/@P.N.?/"), "", $string);
	$langsFound = array();
	$lastLang = "other";
	$skipTo = "";
	for ($index=0; $index<strlen($string);) {
		$charLen = 1;
		$letter = substr($string, $index, 1);
		if ((ord($letter) & 0xE0) == 0xC0) $charLen = 2;		// 2-byte sequence
		if ((ord($letter) & 0xF0) == 0xE0) $charLen = 3;		// 3-byte sequence
		if ((ord($letter) & 0xF8) == 0xF0) $charLen = 4;		// 4-byte sequence

		$letter = substr($string, $index, $charLen);
		$index += $charLen;

		if ($letter==$skipTo) $skipTo = "";
		else {
			if ($letter=="&") $skipTo = ";";
			else if ($letter=="<") $skipTo = ">";
			if ($skipTo=="") {
				$lang = whatAlphabet($letter);
				if ($lang!="none") {
					$langsFound[$lang] = true;
					$lastLang = $lang;
				}
			}
		}
	}
	if (isset($langsFound["vietnamese"])) {
		if (count($langsFound)==1) return "vietnamese";
		if (count($langsFound)==2 && isset($langsFound["other"])) return "vietnamese";
	}
	if (count($langsFound)!=1) return "other";
	return $lastLang;
}


/**
 * convert HTML entities to to their original characters
 *
 * original found at http://www.php.net/manual/en/function.get-html-translation-table.php
 * @see http://www.php.net/manual/en/function.get-html-translation-table.php
 * @param string $string	the string to remove the entities from
 * @return string	the string with entities converted
 */
function unhtmlentitiesrtl($string)  {
	$trans_tbl=array_flip(get_html_translation_table (HTML_ENTITIES));
	$trans_tbl['&lrm;']=PGV_UTF8_LRM;
	$trans_tbl['&rlm;']=PGV_UTF8_RLM;
	return preg_replace('/&#(\d+);/e', "chr(\\1)", strtr($string, $trans_tbl));
}

/**
 * process a string according to bidirectional rules
 *
 * this function will take a text string and reverse it for RTL languages
 * according to bidi rules.
 * @param string $text	String to change
 * @return string	the new bidi string
 * @todo add other RTL langauges
 */
function bidi_text($text) {
	global $RTLOrd;

	// דו"ח אישי
	//קראטוןםפ שדגכעיחלךף זסבה� מצתץ עברי איתה מאיה (אתקה) שם משפחה ‎
	//מספר מזהה (SSN)

	$found = false;
	foreach($RTLOrd as $indexval => $ord) {
    	if (strpos($text, chr($ord))!==false) $found=true;
	}
	if (!$found) return $text;

	$special_chars = array(' ','"','\'','(',')','[',']',':',"\n");
	$newtext = "";
	$parts = array();
	$temp = "";
	$state = 0;
	$p = 0;
	for($i=0; $i<strlen($text); $i++) {
		$letter = $text{$i};
		//print $letter.ord($letter).",";
		//-- handle Hebrew chars
		if (in_array(ord($letter),$RTLOrd)) {
			if (!empty($temp)) {
				//-- just in case the $temp is a Hebrew char push it onto the stack
				if (in_array(ord($temp{0}),$RTLOrd));
				//-- if the $temp starts with a char in the special_chars array then remove the space and push it onto the stack seperately
				else if (in_array($temp{strlen($temp)-1}, $special_chars)) {
					$char = substr($temp, strlen($temp)-1);
					$temp = substr($temp, 0, strlen($temp)-1);
					if ($char=="[") $char = "]";
					else if ($char=="(") $char = ")";
					array_push($parts, $temp);
					array_push($parts, $char);
				}
				//-- otherwise push it onto the begining of the stack
				else array_unshift($parts, $temp);
			}
			$temp = $letter . $text{$i+1};
			$i++;
			if ($i < strlen($text)-1) {
				$l = $text{$i+1};
				if (in_array($l, $special_chars)) {
					if ($l=="]") $l = "[";
					else if ($l==")") $l = "(";
					$temp = $l . $temp;
					$i++;
				}
			}
			array_push($parts, $temp);
			$temp = "";
		}
		else if (ord($letter)==226) {
			if ($i < strlen($text)-2) {
				$l = $letter.$text{$i+1}.$text{$i+2};
				$i += 2;
				if (($l==PGV_UTF8_LRM)||($l==PGV_UTF8_RLM)) {
					if (!empty($temp)) {
						$last = array_pop($parts);
						if ($temp{0}==")") $last = '(' . $last;
						else if ($temp{0}=="(") $last = ')' . $last;
						else if ($temp{0}=="]") $last = '[' . $last;
						else if ($temp{0}=="[") $last = ']' . $last;
						array_push($parts, $last);
						$temp = "";
					}
				}
			}
		}
		else $temp .= $letter;
	}
	if (!empty($temp)) {
		if (in_array(ord($temp{0}),$RTLOrd)) array_push($parts, $temp);
		else array_push($parts, $temp);
	}

	//-- loop through and check if parenthesis are correct... if parenthesis were broken by
	//-- rtl text then they need to be reversed
	for($i=0; $i<count($parts); $i++) {
		$bef = "";
		$aft = "";
		$wt = preg_match("/^(\s*).*(\s*)$/", $parts[$i], $match);
		if ($wt>0) {
			$bef = $match[1];
			$aft = $match[2];
		}
		$temp = trim($parts[$i]);
		if (!empty($temp)) {
			if ($temp{0}=="(" && $temp{strlen($temp)-1}!=")") $parts[$i] = $bef.substr($temp, 1).")".$aft;
			if ($temp{0}=="[" && $temp{strlen($temp)-1}!="]") $parts[$i] = $bef.substr($temp, 1)."]".$aft;
			if ($temp{0}!="(" && $temp{strlen($temp)-1}==")") $parts[$i] = $bef."(".substr($temp, 0, strlen($temp)-1).$aft;
			if ($temp{0}!="[" && $temp{strlen($temp)-1}=="]") $parts[$i] = $bef."[".substr($temp, 0, strlen($temp)-1).$aft;
		}
	}
	//print_r($parts);
	$parts = array_reverse($parts);
	$newtext = implode("", $parts);
	return $newtext;
}

/**
 * Verify if text is a RtL character
 *
 * This will verify if text is a RtL character
 * @param string $text to verify
 */
function oneRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI?
	return (strlen($text)==2 && in_array(ord($text),$RTLOrd));
}

/**
 * Verify if text starts by a RtL character
 *
 * This will verify if text starts by a RtL character
 * @param string $text to verify
 */
function begRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI?
	return (in_array(ord(substr(trim($text),0,2)),$RTLOrd) || in_array(ord(substr(trim($text),1,2)),$RTLOrd));
}

/**
 * Verify if text ends by a RtL character
 *
 * This will verify if text ends by a RtL character
 * @param string $text to verify
 */
function endRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI? -- I believe that not used
	return (in_array(ord(substr(trim($text),strlen(trim($text))-2,2)),$RTLOrd) || in_array(ord(substr(trim($text),strlen(trim($text))-3,2)),$RTLOrd));
}

/**
 * Verify if text is RtL
 *
 * This will verify if text has RtL characters
 * @param string $text to verify
 */
function hasRTLText($text) {
	global $RTLOrd;
	//--- What if gedcom in ANSI?
	// if (!(strpos($text, chr(215))=== false)) return true;  // OK?
	for ($i=0; $i<strlen($text); $i++) {
	  if (in_array(ord(substr(trim($text),$i,2)),$RTLOrd)) return true;
	}
	return false;

}

/**
 * Verify if text is LtR
 *
 * This will verify if text has LtR characters that are not special characters
 * @param string $text to verify
 */
function hasLTRText($text) {
	global $SpecialChar, $SpecialPar, $SpecialNum, $RTLOrd;
	//--- What if gedcom in ANSI?
	//--- Should have one fullspecial characters array in PGV -

	for ($i=0; $i<strlen($text); $i++) {
		if (in_array(ord(substr(trim($text),$i,2)),$RTLOrd) || in_array(ord(substr(trim($text),$i-1,2)),$RTLOrd)) $i++;
	  	else {
		  	if (substr($text,$i,26)=='<span class="starredname">') $i+=25;
		  	else if (substr($text,$i,7)=="</span>") $i+=6;
		  	else {
				$byte = substr(trim($text),$i,1);
		    	if (!in_array($byte,$SpecialChar) && !in_array($byte,$SpecialPar) && !in_array($byte,$SpecialNum)) return true;
	    	}
	    }
	}
	return false;
}

/*
 * Function to reverse RTL text for proper appearance on charts.
 *
 * GoogleChart and the GD library don't handle RTL text properly.  They assume that all text is LTR.
 * This function reverses the input text so that it will appear properly when rendered by GoogleChart
 * and by the GD library (the Circle Diagram).
 *
 * Note 1: Numbers must always be rendered LTR, even when the rest of the text is RTL.
 * Note 2: The visual direction of paired characters such as parentheses, brackets, directional
 *         quotation marks, etc. must be reversed so that the appearance of the RTL text is preserved.
 */
function reverseText($text) {
	global $UTF8_numbers, $UTF8_brackets;

	$text = strip_tags(html_entity_decode($text,ENT_COMPAT,'UTF-8'));
	$text = str_replace(array('&lrm;', '&rlm;', PGV_UTF8_LRM, PGV_UTF8_RLM), '', $text);
	$textLanguage = whatLanguage($text);
	if ($textLanguage!='hebrew' && $textLanguage!='arabic') return $text;

	$reversedText = '';
	$numbers = '';
	while ($text!='') {
		$charLen = 1;
		$letter = substr($text, 0, 1);
		if ((ord($letter) & 0xE0) == 0xC0) $charLen = 2;		// 2-byte sequence
		if ((ord($letter) & 0xF0) == 0xE0) $charLen = 3;		// 3-byte sequence
		if ((ord($letter) & 0xF8) == 0xF0) $charLen = 4;		// 4-byte sequence

		$letter = substr($text, 0, $charLen);
		$text = substr($text, $charLen);
		if (in_array($letter, $UTF8_numbers)) $numbers .= $letter;		// accumulate numbers in LTR mode
		else {
			$reversedText = $numbers.$reversedText;		// emit any waiting LTR numbers now
			$numbers = '';
			if (isset($UTF8_brackets[$letter])) $reversedText = $UTF8_brackets[$letter].$reversedText;
			else $reversedText = $letter.$reversedText;
		}
	}

	$reversedText = $numbers.$reversedText;		// emit any waiting LTR numbers now
	return $reversedText;
}

?>

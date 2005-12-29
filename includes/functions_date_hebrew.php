<?php
/**
 * Hebrew/Jewish Date Functions
 *
 * The functions in this file are used when converting dates to the Hebrew or Jewish Calendar
 * This file is only loaded if the year is hebrew, or if the $CALENDAR_FORMAT is hebrew or jewish
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id: functions_date_hebrew.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */

/**
 * security check to prevent hackers from directly accessing this file
 */
if (strstr($_SERVER["SCRIPT_NAME"],"functions_date_hebrew.php")) {
	print "Why do you want to do that?";
	exit;
}

//-- Hebrew date (escape value @#DHEBREW@) month (Jewish) or full date (Hebrew pages) translation  (meliza)  
function convert_hdate($dstr_beg, $dstr_end, $day, $month, $year) {
	global $LANGUAGE, $CALENDAR_FORMAT, $monthtonum; 
	
	if ($month != "") $month = $monthtonum[$month];
	
	if ($LANGUAGE != "hebrew" && ($CALENDAR_FORMAT == "gregorian" || $CALENDAR_FORMAT == "jewish" || $CALENDAR_FORMAT == "jewish_and_gregorian")) {
		if ($month != "") $hebrewMonthName = getJewishMonthName($month, $year);
		else $hebrewMonthName = "";
		$datestr = $dstr_beg . $day . " " . $hebrewMonthName . " " . $year . " " . $dstr_end;
	}
	else {																	  //-- Hebrew page
		$newdate = getHebrewJewishDates($year, $month, $day);
		$datestr = $dstr_beg . $newdate . $dstr_end;
	} 

	return $datestr;		
}

//-- functions to take a Jewish date and display it in Hebrew.
//-- provided by: KosherJava
function getFullHebrewJewishDates($year, $month="", $day="", $altYear="", $altMonth="") {
	global $DISPLAY_JEWISH_GERESHAYIM, $TEXT_DIRECTION;
	$USE_FULL_PARTIAL_JEWISH_DATES = true;
	$sb = "<span lang=\"he-IL\" dir=\"rtl\">";
	if($day != "") {
		$sb .= getHebrewJewishDay($day);
		$sb .= " ";
	}
	if($month != "") {
		if($day != "") { //jewish date is exact
			$sb .= getHebrewJewishMonth($month, $year);
			$sb .= " ";
		} else { //only month and not day
			$sb .= getHebrewJewishMonth($month, $year);
			if($USE_FULL_PARTIAL_JEWISH_DATES) {
				if($altMonth != "" && $altMonth != $month) {
					$sb .= " / ";
					$sb .= getHebrewJewishMonth($altMonth, $altYear);
				}
			}
			$sb .= " ";
		}
	}
	if($USE_FULL_PARTIAL_JEWISH_DATES) {
		if($month=="") {
			$sb .= getHebrewJewishYear($year - 1);
			$sb .= " / ";
			$sb .= getHebrewJewishYear($year);
		} else if($altMonth!=0 && $month != $altMonth && $altYear !=0 && $altYear != $year && $day == "") {
			$sb .= getHebrewJewishYear($year);
			$sb .= " / ";
			$sb .= getHebrewJewishYear($altYear);
		} else {
			$sb .= getHebrewJewishYear($year);
		}
	} else {
		$sb .= getHebrewJewishYear($year);
	}

	$sb .= "</span>";
	if($TEXT_DIRECTION == "ltr") { //only do this for ltr languages
		$sb.= "&lrm;"; //add entity to return to left to right direction
	}
	if($DISPLAY_JEWISH_GERESHAYIM == false) {
		$sb = preg_replace(array("/\"/", "/'/"), array("",""), $sb);
	}
	return $sb;
}

function getHebrewJewishDates($year, $month="", $day="", $altYear="", $altMonth="") {
	global $DISPLAY_JEWISH_GERESHAYIM, $TEXT_DIRECTION;
	$sb = "<span lang=\"he-IL\" dir=\"rtl\">";
	if($day != "") {
		$sb .= getHebrewJewishDay($day);
		$sb .= " ";
	}
	if($month != "") {
		if($day != "") { //jewish date is exact
			$sb .= getHebrewJewishMonth($month, $year);
			$sb .= " ";
		} else { //only month and not day
			$sb .= getHebrewJewishMonth($month, $year); //FIXME Since month is based on 1st of the Gregorian date,
			$sb .= " ";							//		Would be nice to return both months.
		}
	}
	if($year != "") {
		$sb .= getHebrewJewishYear($year);
	}
	$sb .= "</span>";
	if($TEXT_DIRECTION == "ltr") { //only do this for ltr languages
		$sb.= "&lrm;"; //add entity to return to left to right direction
	}

	if($DISPLAY_JEWISH_GERESHAYIM == false) {
			$sb = preg_replace(array("/\"/", "/'/"), array("",""), $sb);
	}
	return $sb;
}

function getHebrewJewishYear($year) {
	global $DISPLAY_JEWISH_THOUSANDS;

	$jAlafim = "אלפים";                       //word ALAFIM in Hebrew for display on years evenly divisable by 1000
	$jHundreds = array("", "ק", "ר", "ש", "ת", "תק", "תר","תש", "תת", "תתק");
	$jTens = array("", "י", "כ", "ל", "מ", "נ", "ס", "ע", "פ", "צ");
	$jTenEnds = array("", "י", "ך", "ל", "ם", "ן", "ס", "ע", "ף", "ץ");
	$tavTaz = array("ט\"ו", "ט\"ז");
	$jOnes = array("", "א", "ב", "ג", "ד", "ה", "ו", "ז", "ח", "ט");

	$singleDigitYear = isSingleDigitJeiwshYear($year);
	$thousands = $year / 1000;                                   //get # thousands

	$sb = "";	
	//append thousands to String
	if($year % 1000 == 0) {                                      // in year is 5000, 4000 etc
		$sb .= $jOnes[$thousands];
		$sb .= "'";
		$sb .= " ";
		$sb .= $jAlafim;                                         //add # of thousands plus word thousand (overide alafim boolean)
	} else if($DISPLAY_JEWISH_THOUSANDS) {                       // if alafim boolean display thousands
		$sb .= $jOnes[$thousands];
		$sb .= "'";                                              //append thousands quote
		$sb .= " ";
	}
	$year = $year % 1000;                                        //remove 1000s
	$hundreds = $year / 100;                                     // # of hundreds
	$sb .= $jHundreds[$hundreds];                                //add hundreds to String
	$year = $year % 100;                                         //remove 100s
	if($year == 15) {                                            //special case 15
		$sb .= $tavTaz[0];
	} else if($year == 16) {                                     //special case 16
		$sb .= $tavTaz[1];
	} else {
		$tens = $year / 10;
		if($year % 10 == 0) {                                    // if evenly divisable by 10
			if($singleDigitYear == false) {
				$sb .= $jTenEnds[$tens]; // use end letters so that for example 5750 will end with an end nun
			} else {
				$sb .= $jTens[$tens]; // use standard letters so that for example 5050 will end with a regular nun
			}
		} else {
			$sb .= $jTens[$tens];
			$year = $year % 10;
			$sb .= $jOnes[$year];
		}
	}

	if($singleDigitYear == true) {
		$sb .= "'"; //append single quote
	} else { // append double quote before last digit
        $pos1 = strlen($sb)-2;
 		$sb = substr($sb, 0, $pos1) . "\"" . substr($sb, $pos1);
		$sb = preg_replace("/\"\"/", "\"", $sb);
	}
	return $sb;
}

function getHebrewJewishMonth($month, $year) {
	$jMonths = array("תשרי",
			"חשון",
			"כסלו",
			"טבת",
			"שבט",
			"אדר א'",
			"אדר ב'",
			"ניסן",
			"אייר",
			"סיון",
			"תמוז",
			"אב",
			"אלול",
			"אדר"); //last 1 is Adar for non leap year

	if (empty($month)) return "";
	if($month == 6) { // if Adar check for leap year
		if(isJewishLeapYear($year)) {
			return $jMonths[5]; //if it is leap year return default php "Adar A"
		} else { // non leap year
			return $jMonths[13];
		}
	} else { // non Adar months
		return $jMonths[$month - 1];
	}
}

function getHebrewJewishDay($day) {
	$jTens = array("", "י", "כ", "ל", "מ", "נ", "ס", "ע", "פ", "צ");
	$jTenEnds = array("", "י", "ך", "ל", "ם", "ן", "ס", "ע", "ף", "ץ");
	$tavTaz = array("ט\"ו", "ט\"ז");
	$jOnes = array("", "א", "ב", "ג", "ד", "ה", "ו", "ז", "ח", "ט");

	if (empty($day)) return "";
	$sb = "";
	if($day < 10) { //single digit days get single quote appended
		$sb .= $jOnes[$day];
		$sb .= "'";
	} else if($day == 15) { //special case 15
		$sb .= $tavTaz[0];
	} else if($day == 16) { //special case 16
		$sb .= $tavTaz[1];
	} else {
		$tens = $day / 10;
		$sb .= $jTens[$tens];
		if($day % 10 == 0) { // 10 or 20 single digit append single quote
			$sb .= "'";
		} else if($day > 10) { // >10 display " between 10s and 1s
			$sb .= "\"";
		}
		$day = $day % 10; //discard 10s
		$sb .= $jOnes[$day];
	}
	return $sb;
}

function isJewishLeapYear($year) {
	if($year % 19 == 0 || $year % 19 == 3 || $year % 19 ==6 || $year % 19 == 8 || $year % 19 == 11
			|| $year % 19 == 14 || $year % 19 == 17) { // 3rd, 6th, 8th, 11th, 14th, 17th or 19th years of 19 year cycle
		return true;
	} else { // non leap year
		return false;
	}
}

function isSingleDigitJeiwshYear($year) {
	$shortYear = $year %1000; //discard thousands
	//next check for all possible single Hebrew digit years
	if($shortYear < 11 || ($shortYear <100 && $shortYear % 10 == 0)  || ($shortYear <= 400 && $shortYear % 100 == 0) ) {
		return true;
	} else {
		return false;
	}
}

function getJewishMonthName($month, $year) {
	global $JEWISH_ASHKENAZ_PRONUNCIATION;
	$ashkenazMonths = array("Tishrei", "Cheshvan", "Kislev", "Teves", "Shevat", "Adar I", "Adar II", "Nisan", "Iyar", "Sivan", "Tamuz", "Av", "Elul", "Adar");
	$sefardMonths = array("Tishrei", "Heshvan", "Kislev", "Tevet", "Shevat", "Adar I", "Adar II", "Nisan", "Iyar", "Sivan", "Tamuz", "Av", "Elul", "Adar");
	$monthNames = $ashkenazMonths;
	if($JEWISH_ASHKENAZ_PRONUNCIATION != true) {
		$monthNames = $sefardMonths;
	}
	if($month == 6) { // if Adar check for leap year
		if(isJewishLeapYear($year)) {
			return $monthNames[5];
		} else {
			return $monthNames[13];
		}
	} else {
		if (isset($monthNames[$month - 1])) return $monthNames[$month - 1];
		else return $monthNames[$month];
	}

}

/**
 * Convert a jewish gedcom date into a Gregorian date
 *
 * parses a gedcom date IE @#DHEBREW@  into an array of month day and year values
 * @param array $date		The date as coming from the parse_date() function
 * @return array
 * @TODO Actually implement parse method (done in other places but should be unified into one function
 */
function jewishGedcomDateToGregorian($datearray){
	global $monthtonum;
	$dates = array();
	foreach($datearray as $date) {
		if (isset($date["year"])) {
			if (empty($date["mon"])) $date["mon"] = 1;
			if (empty($date["day"])) $date["day"] = 1;
			$julianDate =  jewishtojd ( $date["mon"], $date["day"], $date["year"] );
			$gregdate = jdtogregorian ( $julianDate );
			$pieces = preg_split("~/~", $gregdate);
			$dates[] = array("mon"=>$pieces[0], "day"=>$pieces[1], "year"=>$pieces[2], "month"=>array_search($pieces[0], $monthtonum), "ext"=>"converted jewish");
		}
	}
	return $dates;
}

/**
 * Convert a jewish gedcom date into this year's Gregorian date
 *
 * parses a gedcom date IE @#DHEBREW@  into an array of month day and year values
 * @param array $date		The date as coming from the parse_date() function
 * @return array
 * @TODO Actually implement parse method (done in other places but should be unified into one function
 */
function jewishGedcomDateToCurrentGregorian($datearray){
	global $monthtonum, $month, $year, $hMonth, $hYear; 
	$dates = array();
	
     if (empty($hYear)) {
	    if (isset($_SESSION["timediff"])) $time = time()-$_SESSION["timediff"];
        else $time = time();
 	    if (empty($day)) 	$day           = date("j", $time);
	    if (empty($month)) $month          = date("M", $time);
	    if (empty($year)) 	$year          = date("Y", $time);
	    $dtarray = array();
 	 	$dtarray[0]["day"]   = $day;
 		$dtarray[0]["mon"]   = $monthtonum[str2lower(trim($month))];	
 		$dtarray[0]["year"]  = $year;
 		$dtarray[0]["month"] = $month;
    	$date   = gregorianToJewishGedcomDate($dtarray);
    	$hDay   = $date[0]["day"];
    	$hMonth = $date[0]["month"];
    	$hYear	= $date[0]["year"];
     }
     
     if (!empty($hMonth) && !empty($month) && $monthtonum[$hMonth]>$monthtonum[str2lower($month)]) $altyr = 1;
     else $altyr = -1; 
     
	foreach($datearray as $date) {
			if (empty($date["mon"])) $date["mon"] = 13;
			if (empty($date["day"])) $date["day"] = 30;

 			$julianDate1 = jewishtojd ( $date["mon"], $date["day"], $hYear );
			$gregdate1   = jdtogregorian ( $julianDate1 );
			$pieces1     = preg_split("~/~", $gregdate1);
			$julianDate2 = jewishtojd ( $date["mon"], $date["day"], $hYear+$altyr );
			$gregdate2   = jdtogregorian ( $julianDate2 );
			$pieces2     = preg_split("~/~", $gregdate2);
			if ($pieces1[2] == $year)
			     $dates[] = array("mon"=>$pieces1[0], "day"=>$pieces1[1], "year"=>$pieces1[2], "month"=>array_search($pieces1[0], $monthtonum), "ext"=>"converted jewish");
			else $dates[] = array("mon"=>$pieces2[0], "day"=>$pieces2[1], "year"=>$pieces2[2], "month"=>array_search($pieces2[0], $monthtonum), "ext"=>"converted jewish");

	}
	return $dates;
}

/**
 * Convert a Gregorian gedcom date into a Jewish GEDCOM date without the @#DHEBREW@
 *
 * @param  $datearray		The Gregorian date
 * @return array
 *
 * @TODO Improve !!
 */
function gregorianToJewishGedcomDate($datearray){
	global $monthtonum;
	
	$dates = array();
	foreach($datearray as $date) {
		if (isset($date["year"])) {
			if (empty($date["day"])) $date["day"] = 1;
			if (empty($date["mon"])) $date["mon"] = 1;
		}
	
		$jd = gregoriantojd($date["mon"], $date["day"], $date["year"]);
		$hebrewDate = jdtojewish($jd);
		list ($hMon, $hDay, $hYear) = split ('/', $hebrewDate);
	
		$i=0;
		
		foreach($monthtonum as $hMonth=>$num) {
			$i++;
   	     	if (($i>12 && $num == $hMon)) {
		        break;
       		}
    	}
        $dates[] = array("mon"=>$hMon, "day"=>$hDay, "year"=>$hYear, "month"=>$hMonth, "ext"=>"converted gregorian");
    }

	return $dates;
}
?>
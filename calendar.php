<?php
/**
 * Display Events on a Calendar
 *
 * Displays events on a daily, monthly, or yearly calendar.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * This Page Is Valid XHTML 1.0 Transitional! > 3 September 2005
 *
 * $Id: calendar.php,v 1.4 2006/10/02 23:04:16 lsces Exp $
 * @package PhpGedView
 * @subpackage Calendar
 */

/**
 * load the configuration and create the context
 */
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

if (empty($day)) $day = adodb_date("j");
if (empty($month)) $month = adodb_date("M");
if (empty($year)) $year = adodb_date("Y");

if ($USE_RTL_FUNCTIONS) {
	//-------> Today's Hebrew Day with Gedcom Month

	$datearray = array();
 	$datearray[0]["day"]   = $day;
 	$datearray[0]["mon"]   = $monthtonum[str2lower(trim($month))];
 	$datearray[0]["year"]  = $year;
 	$datearray[0]["month"] = $month;
 	$datearray[1]["day"]   = adodb_date("j");
 	$datearray[1]["mon"]   = $monthtonum[str2lower(trim(adodb_date("M")))];
 	$datearray[1]["year"]  = adodb_date("Y");
 	// should use $parse_date

    $date   	= gregorianToJewishGedcomDate($datearray);
    $hDay   	= $date[0]["day"];
    $hMonth 	= $date[0]["month"];
    $hYear		= $date[0]["year"];
    $CalYear	= $hYear;

    $currhDay   = $date[1]["day"];
    $currhMon   = trim($date[1]["month"]);
    $currhMonth = $monthtonum[str2lower($currhMon)];
    $currhYear 	= $date[1]["year"];
}

if (empty($action)) $action = "today";
if (empty($filterev)) $filterev = "bdm";
if (empty($filterof)) $filterof = "all";
if (empty($filtersx)) $filtersx = "";

$olddates = true;
if ($action=="calendar") {
	$test = @adodb_mktime(1,0,0,1,1,1960);
	if ($test==-1) $olddates = false;
}
$endyear="0";
if ($action=="year") {
	$abbr=array("$pgv_lang[abt]","$pgv_lang[aft]","$pgv_lang[bef]","$pgv_lang[bet]","$pgv_lang[cal]","$pgv_lang[est]","$pgv_lang[from]","$pgv_lang[int]","$pgv_lang[cir]","$pgv_lang[apx]","$pgv_lang[and]","$pgv_lang[to]");
	$year=trim($year);
	for ($i=0;$i<=strlen($year);$i++){
		if (substr($year,0,1)=="0" && substr($year,1,1)!="-") $year=substr($year,1);
	}
	$pos1=strpos($year," ");
	if ($pos1==0) $pos1=strlen($year);
	if (function_exists("str2lower")) $in_year=str2lower(substr($year, 0, $pos1));
	else $in_year=substr($year, 0, $pos1);
	if (in_array("$in_year", $abbr)){
		if (function_exists("str2lower"))	$year = preg_replace(array("/$abbr[0]/","/$abbr[1]/","/$abbr[2]/","/$abbr[3]/","/$abbr[4]/","/$abbr[5]/","/$abbr[6]/","/$abbr[7]/","/$abbr[8]/","/$abbr[9]/","/ $abbr[10] /","/ $abbr[11] /"), array("abt","aft","bef","bet","cal","est","from","int","cir","apx"," and "," to "), str2lower($year));
		else $year = preg_replace(array("/$abbr[0]/","/$abbr[1]/","/$abbr[2]/","/$abbr[3]/","/$abbr[4]/","/$abbr[5]/","/$abbr[6]/","/$abbr[7]/","/$abbr[8]/","/$abbr[9]/"), array("abt","aft","bef","bet","cal","est","from","int","cir","apx"), $year);
	}
	if (strlen($year)>1 && preg_match("/\?/", $year)) $year = preg_replace("/\?/", "[0-9]", $year);
	$year = preg_replace(array("/&lt;/", "/&gt;/", "/[?*+|&.,:'%_<>!#�{}=^]/", "/\\$/", "/\\\/",  "/\"/"), "", $year);
	if (preg_match("/[\D]{1,2}/", $year) && strlen($year)<=2) $year="";
	if (empty($year)) $year = adodb_date("Y");
	$year=trim($year);
	$year_text=$year;
	$year_query=$year;

	$startyear="0";
	if ((strpos($year, "-")>"0") && !preg_match("/[\[\]]/", $year)){
		if (substr($year,0,1) > "9"){
			while (substr($year,0,1) > "9") $year = trim(substr($year, 1));
		}
		$pos1 = (strpos($year, "-"));
		if (strlen($year)==$pos1+2){					// endyear n
			$year_query = substr($year, 0, ($pos1-1))."[".substr($year, ($pos1-1), 3)."]";
			$year_text  = substr($year, 0, ($pos1+1)).substr($year, 0, ($pos1-1)).substr($year, ($pos1+1), 1);
		}
		else if (strlen($year)==$pos1+3){				// endyear nn
			$year_text = substr($year, 0, ($pos1-2));
			if ((substr($year, ($pos1-1), 1)=="0")&&(substr($year, ($pos1+2), 1)=="9")){
				$year_query  = $year_text."[".substr($year, ($pos1-2), 1)."-".substr($year, ($pos1+1), 1)."][0-9]";
			}
			else {
				$startyear= substr($year, 0, $pos1);
				$endyear= substr($year, 0, ($pos1-2)).substr($year, ($pos1+1), 2);
			}
			$year_text = substr($year, 0, ($pos1))." - ".($startyear=="0"?"":$year_text).substr($year, ($pos1+1), 2);
		}
		else if ((strlen($year)==$pos1+4)&&($pos1==4)){	// endyear nnn
			$year_text = substr($year, 0, ($pos1-3));
			if ((substr($year, ($pos1-2), 2)=="00")&&(substr($year, ($pos1+2), 2)=="99")){
				$year_query  = $year_text."[".substr($year, ($pos1-3), 1)."-".substr($year, ($pos1+1), 1)."][0-9][0-9]";
			}
			else {
				$startyear= substr($year, 0, $pos1);
				$endyear= substr($year, 0, ($pos1-3)).substr($year, ($pos1+1), 3);
			}
			$year_text = substr($year, 0, ($pos1))." - ".$year_text.substr($year, ($pos1+1), 3);
		}
		else {											// endyear nnn(n)
			$startyear = substr($year, 0, $pos1);
			$endyear   = substr($year, ($pos1+1));
			$year_text = $startyear." - ".$endyear;
		}
		if ($startyear>$endyear){
			$year_text = $startyear;
			$startyear = $endyear;
			$endyear   = $year_text;
			$year = $startyear."-".$endyear;
			$year_text = $startyear." - ".$endyear;
		}
	}
	if (strpos($year, "[", 1)>"0"){
		$pos1=(strpos($year, "[", 0));
		$year_text=substr($year, 0, $pos1);
		while (($pos1 = strpos($year, "[", $pos1))!==false) {
			$year_text .= substr($year, ($pos1+1), 1);
			$pos1++;
		}
		$pos1=strpos($year, "]", $pos1);
		if (strlen($year)>$pos1 && !strpos($year, "]", $pos1+1)) $year_add=substr($year, $pos1+1, strlen($year));
		$pos1=strpos($year, "]", $pos1+1);
		if (strlen($year)>$pos1 && !strpos($year, "]", $pos1+1)) $year_add=substr($year, $pos1+1, strlen($year));
		if (isset($year_add)) $year_text .= $year_add." ~ ";
		else $year_text .= " - ";
		if (strpos($year, " ", 0)>0) $pos1=(strpos($year, " ", 0)+1);
		else $pos1=0;
		$year_text .= substr($year, $pos1, (strpos($year, "[", 0))-$pos1);
		$pos1=(strpos($year, "[", 0));
		while (($pos1 = strpos($year, "]", $pos1))!==false) {
			$year_text .= substr($year, ($pos1-1), 1);
			$pos1++;
		}
		if (isset($year_add)) $year_text .= $year_add;
		$year_query=$year;
	}
	else if (strlen($year)<4 && preg_match("/[\d]{1,3}/", $year)){
		if (substr($year, 0, 2)<=substr(adodb_date("Y"), 0, 2)){
			for ($i=strlen($year); $i<4; $i++) $year_text .="0";
			$startyear=$year_text;
			$year_text .= " - ".$year;
			for ($i=strlen($year); $i<4; $i++) $year_text .="9";
			$endyear=$year;
			for ($i=strlen($year); $i<4; $i++) $endyear .="9";
		}
		else {
			for ($i=strlen($year); $i<3; $i++) $year_text .="0";
			for ($i=strlen($year); $i<3; $i++) $year .= "0";
		}
		$year_query=$year;
	}
}
else {
	if (strlen($year)<3) $year = adodb_date("Y");
	if (strlen($year)>4){
		if (strpos($year, "[", 1)>"0"){
			$pos1 = (strpos($year, "[", 0));
			$year_text = $year;
			$year = substr($yy, 0, ($pos1));
			$year .= substr($yy, ($pos1+1), 1);
			if (strlen($year_text)==$pos1+10) $year .= substr($yy, ($pos1+6), 1);
		}
		else if (strpos($year, "-", 1)>"0") $year = substr($year, 0, (strpos($year, "-", 0)));
			else $year = adodb_date("Y");
	}
	$year=trim($year);
}

// calculate leap year
if (strlen($year)<5 && preg_match("/[\d]{2,4}/", $year)) {
	if (checkdate(2,29,$year)) $leap = TRUE;
	else $leap = FALSE;
}
else $leap = FALSE;

$pregquery = "";
// Check for invalid days
$m_days = 31;
$m_name = strtolower($month);
if ($m_name == "feb") {
	if (!$leap) {
		$m_days = 28;
		if ($day >= '28') {
			$day = "28";
			$pregquery = "2 DATE[^\n]*2[8|9] $month";
			if ($REGEXP_DB) $query = "2 DATE[^\n]*2[8|9] $month";
			else $query = "%2 DATE%2%$month%";
		}
	}
	else {
		$m_days = 29;
		if ($day >= '29') {
			$day = "29";
			$pregquery = "2 DATE[^\n]*29 $month";
			if ($REGEXP_DB) $query = "2 DATE[^\n]*29 $month";
			else $query = "%2 DATE%29 $month%";
		}
	}
}
else if ($m_name == "apr" || $m_name == "jun" || $m_name == "sep" || $m_name == "nov") {
	$m_days = 30;
	if ($day >= '30') {
		$day = "30";
		$pregquery = "2 DATE[^\n]*30 $month";
		if ($REGEXP_DB) $query = "2 DATE[^\n]*30 $month";
		else $query = "%2 DATE%30 $month%";
	}
}

if (!isset($query)) {
	if ($day<10) {
		$pregquery = "2 DATE[^\n]*[ |0]$day $month";
		if ($REGEXP_DB) $query = "2 DATE[^\n]*[ |0]$day $month";
		else $query = "%2 DATE%$day $month%";
	}
	else {
		$pregquery = "2 DATE[^\n]*$day $month";
		if ($REGEXP_DB) $query = "2 DATE[^\n]*$day $month";
		else $query = "%2 DATE%$day $month%";
	}
}

If (!isset($datearray[4]["year"]) && $USE_RTL_FUNCTIONS) {
	if ($action!="year") {
		 $year1 = $year;
		 $year2 = $year;
	}
	$datearray = array();
 	$datearray[0]["day"]   = $day;
 	$datearray[0]["mon"]   = $monthtonum[str2lower(trim($month))];
 	$datearray[0]["year"]  = $year;
 	$datearray[0]["month"] = $month;
 	// for month
 	$datearray[1]["day"]   = 01;
 	$datearray[1]["mon"]   = $monthtonum[str2lower(trim($month))];
 	$datearray[1]["year"]  = $year;
 	$datearray[2]["day"]   = $m_days;
 	$datearray[2]["mon"]   = $monthtonum[str2lower(trim($month))];
 	$datearray[2]["year"]  = $year;

 	// for year
	if ($action=="year") {
		$pattern="[ - |-|and|bet|from|to|abt|bef|aft|cal|cir|est|apx|int]";
		$a=preg_split($pattern, $year_text);
		if ($a[0]!="") $gstartyear = $a[0];
		if (isset($a[1]))
			if ($a[0]!="") $gendyear = $a[1];
			else {
				$gstartyear = $a[1];
				if (isset($a[2])) $gendyear = $a[2];
				else $gendyear = $a[1];
			}
		else $gendyear = $a[0];

 		$datearray[3]["day"]   = 01;
 		$datearray[3]["mon"]   = 01;
 		$datearray[3]["year"]  = $gstartyear;
 		$datearray[4]["day"]   = 31;
 		$datearray[4]["mon"]   = 12;
 		$datearray[4]["year"]  = $gendyear;
	}

    $date   	= gregorianToJewishGedcomDate($datearray);
    $hDay   	= $date[0]["day"];
    $hMonth 	= $date[0]["month"];
    $CalYear	= $date[0]["year"];

    if (!isset($queryhb) && $action!="year") {   //---- ?????? does not work - see I90 in 1042 @@@@@
    	if ($hDay<10) {
			$preghbquery = "2 DATE[^\n]*[ |0]$hDay $hMonth";
			if ($REGEXP_DB) $queryhb = "2 DATE[^\n]*[ |0]$hDay $hMonth";
			else $queryhb = "%2 DATE%$hDay $hMonth%";
		}
		else {
			$preghbquery = "2 DATE[^\n]*$hDay $hMonth";
			if ($REGEXP_DB) $queryhb = "2 DATE[^\n]*$hDay $hMonth";
			else $queryhb = "%2 DATE%$hDay $hMonth%";
		}
	}
}
print_header($pgv_lang["anniversary_calendar"]);
print "<div style=\" text-align: center;\" id=\"calendar_page\">\n";

	//-- moved here from session.php, should probably be moved somewhere else still
	$sql = "SELECT i_id FROM ".PHPGEDVIEW_DB_PREFIX."individuals where i_file='".$GEDCOMS[$GEDCOM]["id"]."' AND i_gedcom like '%@#DHEBREW@%'";
	$tempsql = $gGedcom->mDb->query($sql);
	$res = $tempsql;
	if ($res->numRows()>0) $HEBREWFOUND[$GEDCOM] = true;
	else $HEBREWFOUND[$GEDCOM] = false;

	// Print top text
	?>
	<table class="facts_table <?php print $TEXT_DIRECTION ?> width100">
	  <tr><td class="facts_label"><h2>
<?php
if ($action=="today") {
	print $pgv_lang["on_this_day"]."</h2></td></tr>\n";
	print "<tr><td class=\"topbottombar\">";
	//-- the year is needed for alternate calendars
 	if ($CALENDAR_FORMAT!="gregorian") print get_changed_date("$day $month $year");
	else print get_changed_date("$day $month");
	if ($CALENDAR_FORMAT=="gregorian" && $USE_RTL_FUNCTIONS && $HEBREWFOUND[$GEDCOM] == true) print " / ".get_changed_date("@#DHEBREW@ $hDay $hMonth $CalYear");
}
else if ($action=="calendar") {
	print $pgv_lang["in_this_month"]."</h2></td></tr>\n";
	print "<tr><td class=\"topbottombar\">";
	print get_changed_date(" $month $year ");
	if ($CALENDAR_FORMAT=="gregorian" && $USE_RTL_FUNCTIONS && $HEBREWFOUND[$GEDCOM] == true) {
		$hdd = $date[1]["day"];
		$hmm = $date[1]["month"];
		$hyy = $date[1]["year"];
		print " /  ".get_changed_date("@#DHEBREW@ $hdd $hmm $hyy");
        if ($hmm!=$date[2]["month"]) {
	            $hdd = $date[2]["day"];
        		$hmm = $date[2]["month"];
				$hyy = $date[2]["year"];
				print " -".get_changed_date("@#DHEBREW@ $hdd $hmm $hyy");
		}
    }
}
else if ($action=="year") {
	print $pgv_lang["in_this_year"]."</h2></td></tr>\n";
	print "<tr><td class=\"topbottombar\">";
	print get_changed_date(" $year_text ");
	if ($CALENDAR_FORMAT=="gregorian" && $USE_RTL_FUNCTIONS && $HEBREWFOUND[$GEDCOM] == true) {
		$hdd = $date[3]["day"];
		$hmm = $date[3]["month"];
		$hstartyear = $date[3]["year"];
		print " /  ".get_changed_date("@#DHEBREW@ $hdd $hmm $hstartyear");
	    $hdd = $date[4]["day"];
        $hmm = $date[4]["month"];
		$hendyear = $date[4]["year"];
		print " -".get_changed_date("@#DHEBREW@ $hdd $hmm $hendyear");
	}
}
	?>
    </td>
  </tr>
</table>
<?php
if ($view!="preview") {
// Print calender form
	print "<form name=\"dateform\" method=\"get\" action=\"calendar.php\">";
	print "<input type=\"hidden\" name=\"action\" value=\"$action\" />";
	print "\n\t\t<table class=\"facts_table $TEXT_DIRECTION width100\">\n\t\t<tr>";
	print "<td class=\"descriptionbox vmiddle\">";
	print_help_link("annivers_date_select_help", "qm", "day");
	print $pgv_lang["day"]."</td>\n";
	print "<td colspan=\"7\" class=\"optionbox\">";
	for($i=1; $i<($m_days+1); $i++) {
		if (empty($dd)) $dd = $day;
		print "<a href=\"calendar.php?day=$i&amp;month=".strtolower($month)."&amp;year=$year&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx&amp;action=today\">";
		if ($i==$dd) print "<span class=\"error\">$i</span>";
		else print $i;
		print "</a> | ";
	}
	$Dd = adodb_date("j");
	$Mm = adodb_date("M");
	$Yy = adodb_date("Y");
//	print "<a href=\"calendar.php?filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx\"><b>".get_changed_date("$Dd $Mm $Yy")."</b></a> | ";
	//-- for alternate calendars the year is needed
  	if ($CALENDAR_FORMAT!="gregorian" || ($USE_RTL_FUNCTIONS && $HEBREWFOUND[$GEDCOM] == true)) $datestr = "$Dd $Mm $Yy";
// 	if ($CALENDAR_FORMAT!="gregorian") $datestr = "$Dd $Mm $Yy"; // MA @@@
	else $datestr = "$Dd $Mm";
	print "<a href=\"calendar.php?filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx&amp;year=$year\"><b>".get_changed_date($datestr);
	if ($USE_RTL_FUNCTIONS && $HEBREWFOUND[$GEDCOM] == true) {
		$hdatestr = "@#DHEBREW@ $currhDay $currhMon $currhYear";
		print " / ".get_changed_date($hdatestr);
	}
	print "</b></a> | ";
	print "</td>\n";

	print "</tr><tr>";
	print "<td class=\"descriptionbox vmiddle\">";
	print_help_link("annivers_month_select_help", "qm", "month");
	print $pgv_lang["month"]."</td>\n";
	print "<td colspan=\"7\" class=\"optionbox\">";
	foreach($monthtonum as $mon=>$num) {
		if (isset($pgv_lang[$mon])) {
			if (empty($mm)) $mm=strtolower($month);
			print "<a href=\"calendar.php?day=$dd&amp;month=$mon&amp;year=$year&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx&amp;action=".($action=="year"?"calendar":"$action")."\">";
			$monthstr = $pgv_lang[$mon];
			if ($mon==$mm) print "<span class=\"error\">".$monthstr."</span>";
			else print $monthstr;
			print "</a> | ";
		}
	}

	print "<a href=\"calendar.php?month=".strtolower(adodb_date("M"))."&amp;action=calendar&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx\"><b>".$pgv_lang[strtolower(adodb_date("M"))]." ".strtolower(adodb_date("Y"))."</b></a> | ";
	print "</td>\n";
	print "</tr><tr>";
	print "<td class=\"descriptionbox vmiddle\">";
	print_help_link("annivers_year_select_help", "qm", "year");
	print $pgv_lang["year"]."</td>\n";
	$username = getUserName();
	print "<td class=\"optionbox vmiddle\">";
	if (strlen($year)<5){
		if ($year<"AA") print " <a href=\"calendar.php?day=$day&amp;month=$month&amp;year=".($year-1)."&amp;action=".($action=="calendar"?"calendar":"year")."&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx\" title=\"".($year-1)."\" >-1</a> ";
	}
	print "<input type=\"text\" name=\"year\" value=\"$year\" size=\"7\" />";
	if (strlen($year)<5){
		if ($year<(adodb_date("Y"))) print " <a href=\"calendar.php?day=$day&amp;month=$month&amp;year=".($year+1)."&amp;action=".($action=="calendar"?"calendar":"year")."&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx\" title=\"".($year+1)."\" >+1</a> |";
		else if ($year<"AA") print " +1 |";
	}
	print " <a href=\"calendar.php?day=$day&amp;month=$month&amp;year=".adodb_date("Y")."&amp;action=".($action=="calendar"?"calendar":"year")."&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$filtersx\"><b>".strtolower(adodb_date("Y"))."</b></a> | ";

	print "</td>\n ";
	if (!$HIDE_LIVE_PEOPLE||(!empty($username))) {
		print "<td class=\"descriptionbox vmiddle\">";
		print_help_link("annivers_show_help", "qm", "show");
		print $pgv_lang["show"].":&nbsp;</td>\n";
		print "<td class=\"optionbox vmiddle\">";

		print "<input type=\"hidden\" name=\"filterof\" value=\"$filterof\" />";
		print "<select class=\"list_value\" name=\"filterof\" onchange=\"document.dateform.submit();\">\n";
		print "<option value=\"all\"";
		if ($filterof == "all") print " selected=\"selected\"";
		print ">".$pgv_lang["all_people"]."</option>\n";
		print "<option value=\"living\"";
		if ($filterof == "living") print " selected=\"selected\"";
		print ">".$pgv_lang["living_only"]."</option>\n";
		print "<option value=\"recent\"";
		if ($filterof == "recent") print " selected=\"selected\"";
		print ">".$pgv_lang["recent_events"]."</option>\n";
		print "</select>\n";
	}
	else {
		print "<td class=\"descriptionbox vmiddle\">".$pgv_lang["showcal"]."</td>\n";
		print "<td colspan=\"5\" class=\"optionbox vmiddle\">";
		if ($filterof=="all") print "<span class=\"error\">".$pgv_lang["all_people"]. "</span> | ";
		else {
			$filt="all";
			print "<a href=\"calendar.php?day=$dd&amp;month=$month&amp;year=$year&amp;filterof=$filt&amp;filtersx=$filtersx&amp;action=$action\">".$pgv_lang["all_people"]."</a>"." | ";
		}
		if ($filterof=="recent") print "<span class=\"error\">".$pgv_lang["recent_events"]. "</span> | ";
		else {
			$filt="recent";
			print "<a href=\"calendar.php?day=$dd&amp;month=$month&amp;year=$year&amp;filterof=$filt&amp;filtersx=$filtersx&amp;action=$action\">".$pgv_lang["recent_events"]."</a>"." | ";
		}
	}


	if (!$HIDE_LIVE_PEOPLE||(!empty($username))) {
		print "</td>\n ";
		print "<td class=\"descriptionbox vmiddle\">";
		print_help_link("annivers_sex_help", "qm", "sex");
		print $pgv_lang["sex"].":&nbsp;</td>\n";
		print "<td class=\"optionbox vmiddle\">";
		if ($filtersx==""){
			print " <img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["all"]."\" alt=\"".$pgv_lang["all"]."\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\" />";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["all"]."\" alt=\"".$pgv_lang["all"]."\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\" />";
			print " | ";
		}
		else {
			$fs="";
			print " <a href=\"calendar.php?day=$dd&amp;month=$month&amp;year=$year&&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$fs&amp;action=$action\">";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["all"]."\" alt=\"".$pgv_lang["all"]."\" width=\"9\" height=\"9\" border=\"0\" align=\"middle\" />";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["all"]."\" alt=\"".$pgv_lang["all"]."\" width=\"9\" height=\"9\" border=\"0\" align=\"middle\" />";
			print "</a>"." | ";
		}
		if ($filtersx=="M"){
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"]."\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\" />";
			print " | ";
		}
		else {
			$fs="M";
			print "<a href=\"calendar.php?day=$dd&amp;month=$month&amp;year=$year&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$fs&amp;action=$action\">";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"]."\" width=\"9\" height=\"9\" border=\"0\" align=\"middle\" />";
			print "</a>"." | ";
		}
		if ($filtersx=="F"){
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"]."\" width=\"15\" height=\"15\" border=\"0\" align=\"middle\" />";
			print " | ";
		}
		else {
			$fs="F";
			print "<a href=\"calendar.php?day=$dd&amp;month=$month&amp;year=$year&amp;filterev=$filterev&amp;filterof=$filterof&amp;filtersx=$fs&amp;action=$action\">";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"]."\" width=\"9\" height=\"9\" border=\"0\" align=\"middle\" />";
			print "</a>"." | ";
		}

	}

	if (!$HIDE_LIVE_PEOPLE||(!empty($username))) {
		print "</td>\n ";
		global $factarray;
		print "<td class=\"descriptionbox vmiddle\">";
		print_help_link("annivers_event_help", "qm", "showcal");
		print $pgv_lang["showcal"]."&nbsp;</td>\n";
		print "<td class=\"optionbox\"";
		if (!$HIDE_LIVE_PEOPLE||!empty($username)) print ">";
		else print " colspan=\"3\">";
		print "<input type=\"hidden\" name=\"filterev\" value=\"$filterev\" />";
		print "<select class=\"list_value\" name=\"filterev\" onchange=\"document.dateform.submit();\">\n";

		print "<option value=\"bdm\"";
		if ($filterev == "bdm") print " selected=\"selected\"";
		print ">".$pgv_lang["bdm"]."</option>\n";

		print "<option value=\"all\"";
		if ($filterev == "all") print " selected=\"selected\"";
		print ">".$pgv_lang["all"]."</option>\n";

		print "<option value=\"BIRT\"";
		if ($filterev == "BIRT") print " selected=\"selected\"";
		print ">".$factarray["BIRT"]."</option>\n";
		print "<option value=\"CHR\"";
		if ($filterev == "CHR") print " selected=\"selected\"";
		print ">".$factarray["CHR"]."</option>\n";
		print "<option value=\"CHRA\"";
		if ($filterev == "CHRA") print " selected=\"selected\"";
		print ">".$factarray["CHRA"]."</option>\n";
		print "<option value=\"BAPM\"";
		if ($filterev == "BAPM") print " selected=\"selected\"";
		print ">".$factarray["BAPM"]."</option>\n";
		print "<option value=\"_COML\"";
		if ($filterev == "_COML") print " selected=\"selected\"";
		print ">".$factarray["_COML"]."</option>\n";
		print "<option value=\"MARR\"";
		if ($filterev == "MARR") print " selected=\"selected\"";
		print ">".$factarray["MARR"]."</option>\n";
		print "<option value=\"_SEPR\"";
		if ($filterev == "_SEPR") print " selected=\"selected\"";
		print ">".$factarray["_SEPR"]."</option>\n";
		print "<option value=\"DIV\"";
		if ($filterev == "DIV") print " selected=\"selected\"";
		print ">".$factarray["DIV"]."</option>\n";
		print "<option value=\"DEAT\"";
		if ($filterev == "DEAT") print " selected=\"selected\"";
		print ">".$factarray["DEAT"]."</option>\n";
		print "<option value=\"BURI\"";
		if ($filterev == "BURI") print " selected=\"selected\"";
		print ">".$factarray["BURI"]."</option>\n";
		print "<option value=\"IMMI\"";
		if ($filterev == "IMMI") print " selected=\"selected\"";
		print ">".$factarray["IMMI"]."</option>\n";
		print "<option value=\"EMIG\"";
		if ($filterev == "EMIG") print " selected=\"selected\"";
		print ">".$factarray["EMIG"]."</option>\n";
		print "<option value=\"EVEN\"";
		if ($filterev == "EVEN") print " selected=\"selected\"";
		print ">".$pgv_lang["custom_event"]."</option>\n";
		print "</select>\n";
	}

	print "</td>\n";
	print "</tr>";
	print "<tr><td class=\"topbottombar\" colspan=\"8\">";
	print_help_link("day_month_help", "qm");
	print "<input type=\"hidden\" name=\"day\" value=\"$dd\" />";
	print "<input type=\"hidden\" name=\"month\" value=\"$mm\" />";
	print "<input type=\"hidden\" name=\"filtersx\" value=\"$filtersx\" />";
	print "<input type=\"submit\" value=\"".$pgv_lang["viewday"]."\" onclick=\"document.dateform.elements['action'].value='today';\" />\n";
	print "<input type=\"submit\" value=\"".$pgv_lang["viewmonth"]."\" onclick=\"document.dateform.elements['action'].value='calendar';\" />\n";
	print "<input type=\"submit\" value=\"".$pgv_lang["viewyear"]."\" onclick=\"document.dateform.elements['action'].value='year';\" />\n";
	print "</td></tr></table><br />";
	print "</form>\n";


}
if (($action=="today") || ($action=="year")) {
	$myindilist = array();
	$myfamlist = array();

	if ($action=="year"){
		if (isset($year_query)) $year=$year_query;
		$pregquery = "2 DATE[^\n]*(bet|$year)";
		if ($REGEXP_DB) $query = "2 DATE[^\n]*(bet|$year)";
		else $query = "%2 DATE%bet%";
		$pregquery1 = "2 DATE[^\n]*$year";
		if ($REGEXP_DB) $query1 = "2 DATE[^\n]*$year";
		else $query1 = "%2 DATE%$year%";                          //--- should this remain $query ??? MA @@@@

		if ($endyear>0){
			$myindilist = search_indis_year_range($startyear,$endyear);
			$myfamlist = search_fams_year_range($startyear,$endyear);
		}
		if ($USE_RTL_FUNCTIONS && isset($hstartyear) && isset($hendyear)) {
			$myindilist1 = search_indis_year_range($hstartyear,$hendyear);
			$myindilist = pgv_array_merge($myindilist, $myindilist1);

			$myfamlist1 = search_fams_year_range($hstartyear,$hendyear);
			$myfamlist = pgv_array_merge($myfamlist, $myfamlist1);
		}
	}
	if ($endyear==0) {
		if ($USE_RTL_FUNCTIONS) {
			$myindilist1 = search_indis($query);
			$myindilist = pgv_array_merge($myindilist, $myindilist1);

			$myfamlist1 = search_fams($query);
			$myfamlist = pgv_array_merge($myfamlist, $myfamlist1);
		}
		else {
			if ($action=="today") {
				$myindilist = search_indis_dates($day, $month, "", "!CHAN");
				$myfamlist = search_fams_dates($day, $month, "", "!CHAN");
			}
			if ($action=="year") {
				$myindilist = search_indis_dates("", "", $year, "!CHAN");
				$myfamlist = search_fams_dates("", "", $year, "!CHAN");
			}
        }

		if ($USE_RTL_FUNCTIONS && isset($queryhb) && $action!="year") {
			$myindilist1 = search_indis($queryhb);
			$myindilist = pgv_array_merge($myindilist, $myindilist1);

			$myfamlist1 = search_fams($queryhb);
			$myfamlist = pgv_array_merge($myfamlist, $myfamlist1);
		}
	}
	if (isset($query1)) {
		$query=$query1;
		$pregquery = $pregquery1;
	}
	if (!empty($filtersx)) {
		$add2myindilist = array();
		foreach($myfamlist as $gid=>$fam) {
			$parents = find_parents($gid);
			if ($filtersx=="M") $add2myindilist[$parents["HUSB"]] = $fam["gedcom"];
			else $add2myindilist[$parents["WIFE"]] = $fam["gedcom"];
		}
		$myindilist = search_indis_fam($add2myindilist);
	}
	uasort($myindilist, "itemsort");
	if (empty($filtersx)) uasort($myfamlist, "itemsort");
	$count_private_indi=0;
	$count_indi=0;
	$count_male=0;
	$count_female=0;
	$count_unknown=0;
	$text_indi="";
	$sx=1;
	foreach($myindilist as $gid=>$indi) {
		//print $gid."<br />";
		if (!empty($filtersx)) $sx = preg_match("/1 SEX $filtersx/i", $indi["gedcom"]);
		if ((($filterof!="living")||(is_dead_id($gid)!=1)) && $sx>0) {
			$filterout=false;
			$indilines = split("\n", $indi["gedcom"]);
			$factrec = "";
			$lct = count($indilines);
			$text_fact = "";
			for($i=1; $i<=$lct; $i++) {
				$text_temp = "";
				if ($i<$lct) $line = $indilines[$i];
				if (empty($line)) $line = " ";
				if ($i==$lct||($line{0}=="1")) {
					if (!empty($factrec)) {
						$t1 = preg_match("/2 DATE.*DHEBREW.* (\d\d\d\d)/i", $factrec, $m1);
						if ($USE_RTL_FUNCTIONS && $action=="year" && isset($hendyear) && $hendyear>0 && $t1>0) {
							$j = $hstartyear;   //-- why MA @@@ ??
								if ($m1[1]==$hstartyear || $m1[1]==$hendyear) {
									// verify if the date falls within the first or the last range gregorian year @@@@ !!!!
									$cta = preg_match("/2 DATE (.*)/", $factrec, $match);
									if ($cta>0) {
										$hdate = parse_date(trim($match[1]));
										$gdate = jewishGedcomDateToGregorian($hdate);

                                	    $gyear=$gdate[0]["year"];

										if ($gyear>=$gstartyear && $gyear<=$gendyear) $hprocess=true;
										else $hprocess=false;
									}
									else $hprocess=false;
								}
								else if ($m1[1]>$hstartyear && $m1[1]<$hendyear) $hprocess = true;
								     else $hprocess=false;
								if ($hprocess) $text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
								else $text_temp .="filter";
						}
						else
						if ($endyear>0) {
							$j = $startyear;   //----- why??? MA @@@@
							$t1 = preg_match("/2 DATE.* (\d\d\d\d)/i", $factrec, $m1);
							if (($t1 > 0) && ($m1[1] >= $startyear) && ($m1[1] <= $endyear)){
								$text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
							}
							else {
								$t2 = preg_match("/2 DATE.* (\d\d\d)/i", $factrec, $m2);
								if (($t2 > 0) && ($m2[1] >= $startyear) && ($m2[1] <= $endyear)){
									$text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
								}
							}
						}
						else {
							$ct = preg_match("/$pregquery/i", $factrec, $match);
							if ($action=="year"){
								if ($ct==0){
									$cb = preg_match("/2 DATE[^\n]*(bet)/i", $factrec, $m1);
									if ($cb>0) {
										$cy = preg_match("/DATE.* [\d]{3,4}/i", $factrec, $m2);
										if ($cy>0) {
											$numbers = preg_split("/[^\d]{4,9}/i", $m2[0]);
											$years= array();
											if (count($numbers)>2){
												$y=0;
												foreach($numbers as $key => $value) {
													if (!($value>0 && $value<32) && $value!=""){
														$years[$y]=$value;
														$y++;
													}
												}
											}
											if (!isset($years[0])) $years[0]=0;
											if (!isset($years[1])) $years[1]=0;
											if ($years[0]<$year && $years[1]>$year) $ct=1;
											else $text_temp .="filter";
										}
									}
								}
							}
							if ($ct < 1 && $USE_RTL_FUNCTIONS && isset($preghbquery)) $ct = preg_match("/$preghbquery/i", $factrec, $match);
							if ($action=="year"){
								if ($ct==0){
									$cb = preg_match("/2 DATE[^\n]*(bet)/i", $factrec, $m1);
									if ($cb>0) {
										$cy = preg_match("/DATE.* [\d]{3,4}/i", $factrec, $m2);
										if ($cy>0) {
											$numbers = preg_split("/[^\d]{4,9}/i", $m2[0]);
											$years= array();
											if (count($numbers)>2){
												$y=0;
												foreach($numbers as $key => $value) {
													if (!($value>0 && $value<32) && $value!=""){
														$years[$y]=$value;
														$y++;
													}
												}
											}
											if (!isset($years[0])) $years[0]=0;
											if (!isset($years[1])) $years[1]=0;
											if ($years[0]<$year && $years[1]>$year) $ct=1;
											else $text_temp .="filter";
										}
										else $text_temp .="filter";

//										}
									}
								}
							}
						if ($ct>0) $text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
                        }
					}
					$factrec="";
				}
 				if ($text_temp=="filter") $filterout=true;
 				else if ($text_temp=="filterfilter") $filterout=true;
				// if (stristr($text_temp, "filter") !== false) $filterout=true; // removes also correct entries
				     else $text_fact .= $text_temp;
				$factrec.=$line."\n";
			}
			if (!empty($text_fact) && displayDetailsById($gid)) {

				// $text_indi .= "<tr><td>";
				$text_indi .= "<li><a href=\"individual.php?pid=$gid&amp;GEDCOM=".get_gedcom_from_id($indi["gedfile"])."\"><b>".PrintReady(check_NN(get_sortable_name($gid)))."</b>";
				$text_indi .= "<img id=\"box-$gid.$lct-sex\" src=\"$PGV_IMAGE_DIR/";
				if (preg_match("/1 SEX M/", $indi["gedcom"])>0){
					$count_male++;
					$text_indi .= $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
				}
				else if (preg_match("/1 SEX F/", $indi["gedcom"])>0){
					$count_female++;
					$text_indi .= $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
				}
				else {
					$count_unknown++;
					$text_indi .= $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"];
				}
				$text_indi .= "\" class=\"sex_image\" />";
				if ($SHOW_ID_NUMBERS) {
					if ($TEXT_DIRECTION=="ltr") $text_indi .= "&lrm;($gid)&lrm;";
					else $text_indi .= "&rlm;($gid)&rlm;";
				}
				$text_indi .= "</a><br />\n\t\t";
				$text_indi .= "<div class=\"indent";
				if($TEXT_DIRECTION == "rtl") $text_indi .= "_rtl";
				$text_indi .= "\">";
				$text_indi .= $text_fact;
				$text_indi .= "<br /></div></li>\n\t\t";
				//if ($i<=$lct) $text_indi .= "<br />";
				//$text_indi .= "</li>\n\t\t";
				$count_indi++;
			}
			// else if (!$filterout && $text_fact!="") $count__url_indi++; //?? admin sees as private in year also indis w/o a year in their fact
			else if (!$filterout) {
				$count_private_indi++;
			}
		}
	}

	$count_private_fam = 0;
	$count_fam=0;
	$text_fam="";
	if ($filtersx==""){
		foreach($myfamlist as $gid=>$fam) {
			$display=true;
			if ($filterof=="living"){
				$parents = find_parents($gid);
				if (is_dead_id($parents["HUSB"]) || is_dead_id($parents["WIFE"])) $display=false;
			}
			if ($display and strpos(find_gedcom_record($gid), "1 DIV")!==false) $display = false;
  			if ($display){
				$filterout=false;
				$name = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/","/^[a-z. ]*/"), array(",",",",""), $fam["name"]);
				$names = preg_split("/[,+]/", $name);
				$fam["name"] = check_NN($names);
				$indilines = split("\n", $fam["gedcom"]);
				$lct = count($indilines);
				$factrec = "";
				$text_fact = "";
				for($i=1; $i<=$lct; $i++) {
					$text_temp = "";
					if ($i<$lct) $line = $indilines[$i];
					if (empty($line)) $line = " ";
					if ($i==$lct||($line{0}=="1")) {
						if (!empty($factrec)) {
							$t1 = preg_match("/2 DATE.*DHEBREW.* (\d\d\d\d)/i", $factrec, $m1);
							if ($USE_RTL_FUNCTIONS && $action=="year" && isset($hendyear) && $hendyear>0 && $t1>0) {
								$j = $hstartyear;   //----- why??? MA @@@@
								if ($m1[1]==$hstartyear || $m1[1]==$hendyear) {
								// verify if the date falls within the first or the last range gregorian year @@@@ !!!!
								// find gregorian year of the fact hebrew date
									$cta = preg_match("/2 DATE (.*)/", $factrec, $match);
									if ($cta>0) {
										$hdate = parse_date(trim($match[1]));
										$gdate = jewishGedcomDateToGregorian($hdate);

                                	    $gyear=$gdate[0]["year"];

										if ($gyear>=$gstartyear && $gyear<=$gendyear) $hprocess=true;
										else $hprocess=false;
									}
									else $hprocess=false;
								}
								else if ($m1[1]>$hstartyear && $m1[1]<$hendyear) $hprocess = true;
								     else $hprocess=false;
								if ($hprocess) $text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
                                else $text_temp = "filter";
							}
						    else
							if ($endyear>0){
								$j = $startyear;
								$t1 = preg_match("/2 DATE.* (\d\d\d\d)/i", $factrec, $m1);
								if (($t1 > 0) && ($m1[1] >= $startyear) && ($m1[1] <= $endyear)){
									$text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
								}
								else {
									$t2 = preg_match("/2 DATE.* (\d\d\d)/i", $factrec, $m2);
									if (($t2 > 0) && ($m2[1] >= $startyear) && ($m2[1] <= $endyear)){
										$text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
									}
								}
							}
							else {
								$ct = preg_match("/$pregquery/i", $factrec, $match);
								if ($action=="year"){
									if ($ct==0){
										$cb = preg_match("/2 DATE[^\n]*(bet)/i", $factrec, $m1);
										if ($cb>0) {
											$cy = preg_match("/DATE.* [\d]{3,4}/i", $factrec, $m2);
											if ($cy>0) {
												$numbers = preg_split("/[^\d]{4,9}/i", $m2[0]);
												$years= array();
												if (count($numbers)>2){
													$y=0;
													foreach($numbers as $key => $value) {
														if (!($value>0 && $value<32) && $value!=""){
															$years[$y]=$value;
															$y++;
														}
													}
												}
												if (!isset($years[0])) $years[0]=0;
												if (!isset($years[1])) $years[1]=0;
												if ($years[0]<$year && $years[1]>$year) $ct=1;
												else $text_temp="filter";
											}
										}
									}
								}
    							if ($ct < 1 && $USE_RTL_FUNCTIONS && isset($preghbquery)) $ct = preg_match("/$preghbquery/i", $factrec, $match);
//								if ($ct < 1 && $USE_RTL_FUNCTIONS) $ct = preg_match("/$preghbquery/i", $factrec, $match);
								if ($action=="year"){
									if ($ct==0){
										$cb = preg_match("/2 DATE[^\n]*(bet)/i", $factrec, $m1);
										if ($cb>0) {
											$cy = preg_match("/DATE.* [\d]{3,4}/i", $factrec, $m2);
											if ($cy>0) {
												$numbers = preg_split("/[^\d]{4,9}/i", $m2[0]);
												$years= array();
												if (count($numbers)>2){
													$y=0;
													foreach($numbers as $key => $value) {
														if (!($value>0 && $value<32) && $value!=""){
															$years[$y]=$value;
															$y++;
														}
													}
												}
												if (!isset($years[0])) $years[0]=0;
												if (!isset($years[1])) $years[1]=0;
												if ($years[0]<$year && $years[1]>$year) $ct=1;
												else $text_temp="filter";
											}
										}
									}
								}
								if ($ct>0) $text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
							}
						}
						$factrec="";
					}
					if ($text_temp=="filter") $filterout=true;
					else $text_fact .= $text_temp;
					$factrec.=$line."\n";
				}
				if (!empty($text_fact) && displayDetailsById($gid, "FAM")) {
					$text_fam .= "<li><a href=\"family.php?famid=$gid&amp;GEDCOM=".get_gedcom_from_id($fam["gedfile"])."\"><b>".PrintReady(get_family_descriptor($gid))."</b>";
					$text_fam .= "</a><br />\n\t\t";
					$text_fam .= "<div class=\"indent";
					if ($TEXT_DIRECTION == "rtl") $text_fam .= "_rtl";
					$text_fam .= "\">";
					$text_fam .= $text_fact;
					$text_fam .= "<br /></div></li>\n\t\t";
					$count_fam++;
				}
				else if (!$filterout) $count_private_fam++;
			}
		}
	}

	// Print the day/year list(s)
	if (!empty($text_indi) || !empty($text_fam) || $count_private_indi>0 || $count_private_fam>0) {
		print "\n\t\t<table class=\"center $TEXT_DIRECTION\">\n\t\t<tr>";
		if (!empty($text_indi) || ($count_private_indi>0)) {
			print "<td class=\"descriptionbox center\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" title=\"".$pgv_lang["individuals"]."\" alt=\"".$pgv_lang["individuals"]."\" /> ".$pgv_lang["individuals"]."</td>";
		}
		if (!empty($text_fam) || ($count_private_fam)) {
			print "<td class=\"descriptionbox center\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" title=\"".$pgv_lang["families"]."\" alt=\"".$pgv_lang["families"]."\" /> ".$pgv_lang["families"]."</td>";
		}
		print "</tr><tr>\n\t\t";
		if (!empty($text_indi) || ($count_private_indi)) {
			print "<td class=\"optionbox\"><ul>\n\t\t";
			if (!empty($text_indi)) print $text_indi;
			if ($count_private_indi>0){
				print "<li><b>";
				print $pgv_lang["private"];
				print "</b>&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$count_private_indi.")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "&nbsp;&nbsp;";
				print_help_link("privacy_error_help", "qm", "private");
				print "</li>\n\t\t";
			}
			print "</ul></td>";
		}
		if (!empty($text_fam) || ($count_private_fam)) {
			print "<td class=\"optionbox\"><ul>\n\t\t";
			if (!empty($text_fam)) print $text_fam;
			if ($count_private_fam>0){
				print "<li><b>";
				print $pgv_lang["private"];
				print "</b>&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$count_private_fam.")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "&nbsp;&nbsp;";
				print_help_link("privacy_error_help", "qm", "private");
				print "</li>\n\t\t";
			}
			print "</ul></td>";
		}
		print "</tr><tr>";
		if ($count_indi>0 || $count_private_indi>0){
			print "<td class=\"descriptionbox\">\n";
			if (($count_male+$count_female+$count_unknown+$count_private_indi)>0)
			print $pgv_lang["total_indis"]." ".($count_male+$count_female+$count_unknown+$count_private_indi)."<br />&nbsp;&nbsp;";
			if ($count_male>0){
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sex"]["small"]."\" ";
				print "title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"]."\" class=\"sex_image\" />&nbsp;";
				print $count_male;
			}
			if (($count_male>0)&&($count_female>0)) print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if ($count_female>0) {
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sexf"]["small"]."\" ";
				print "title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"]."\" class=\"sex_image\"  />&nbsp;";
				print $count_female;
			}
			if ($count_unknown>0) {
				print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sexn"]["small"]."\" ";
				print "title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"]."\" class=\"sex_image\" />&nbsp;";
				print $count_unknown;
			}
			print "</td>";
		}
		if (($count_fam>0)||($count_private_fam>0)) {
			print "<td class=\"descriptionbox\">\n";
			if (($count_fam>0)||($count_private_fam>0)) print $pgv_lang["total_fams"];
			print "&nbsp;&nbsp;".($count_fam+$count_private_fam);
			print "</td>";
		}
		print "</tr>";
	}
	else {
		print "\n\t<table class=\"list_table center $TEXT_DIRECTION\">\n\t\t<tr>";
		print "<td class=\"optionbox center\">&nbsp;";
		print $pgv_lang["individuals"]."  /  ".$pgv_lang["families"];
		print "&nbsp;</td></tr><tr><td class=\"warning center\"><i>";
		print $pgv_lang["no_results"];
		print "</i><br />\n\t\t</td></tr>";
	}
	if ($view=="preview") print "<tr><td>";
}
else if ($action=="calendar") {
	if($CALENDAR_FORMAT=="jewish" || $CALENDAR_FORMAT=="hebrew" || $CALENDAR_FORMAT=="hijri") { //since calendar is based on gregorian it doesn't make sense to not display the gregorian caption
		print "<span class=\"subheaders\">".$pgv_lang[strtolower($month)]." $year</span> &#160; \n";
	}
	if (empty($WEEK_START)) $WEEK_START="0";                //-- if the starting day for a week was not defined in the language file, then make it Sunday
	print "<table class=\"list_table center $TEXT_DIRECTION\">\n";
	print "\t<tr>\n";
	$days = array();
	$days[0] = "sunday";
	$days[1] = "monday";
	$days[2] = "tuesday";
	$days[3] = "wednesday";
	$days[4] = "thursday";
	$days[5] = "friday";
	$days[6] = "saturday";
	$j = $WEEK_START;
	for($i=0; $i<7; $i++) {
		print "\t\t<td class=\"descriptionbox\">".$pgv_lang[$days[$j]]."</td>\n";
		$j++;
		if ($j>6) $j=0;
	}
	print "\t</tr>\n";
	$monthstart = adodb_mktime(1,0,0,$monthtonum[strtolower($month)],1,$year);
	$startday = adodb_date("w", $monthstart);
	$endday = adodb_dow($year,$monthtonum[strtolower($month)],adodb_date("t", $monthstart));
	$lastday=adodb_date("t", $monthstart);
	$mmon = strtolower(adodb_date("M", $monthstart));
	$monthstart = $monthstart-(60*60*24*$startday);
	if($WEEK_START<=$startday)
		$monthstart += $WEEK_START*(60*60*24);
	else //week start > $startday
		$monthstart -= (7-$WEEK_START)*(60*60*24);
	if (($endday==6 && $WEEK_START==0) || ($endday==0 && $WEEK_START==1)) $show_no_day=0;
	else $show_no_day=6;
	if ((($startday==0 && $WEEK_START==0) || ($startday==2 && $WEEK_START==1)) && $show_no_day==0) $show_no_day=6;
	$show_not_set=false;
	$lastday-=29;
	if ($lastday<0) $lastday=0;
	$myindilist = array();
	$myfamlist = array();
	$pregquery = "2 DATE[^\n]*$mmon";
	if ($REGEXP_DB) $query = "2 DATE[^\n]*$mmon";
	else $query = "%2 DATE%$mmon%";

	$fact = "";
	if ($filterev=="bdm") $fact = "BIRT,DEAT,MARR";
	else if ($filterev=="all") $fact = "!CHAN";
	else $fact = $filterev;

	$myindilist = search_indis_dates("", $mmon, "", $fact);
	$myfamlist = search_fams_dates("", $mmon, "", $fact);

	if ($USE_RTL_FUNCTIONS) {
		$datearray[0]["day"]   = 01;
 		$datearray[0]["mon"]   = $monthtonum[str2lower($month)];
 		$datearray[0]["year"]  = $year;
 		$datearray[0]["month"] = $month;
 		$datearray[1]["day"]   = 15;
 		$datearray[1]["mon"]   = $monthtonum[str2lower($month)];
 		$datearray[1]["year"]  = $year;
 		$datearray[1]["month"] = $month;
 		$datearray[2]["day"]   = adodb_date("t", $monthstart);
 		$datearray[2]["mon"]   = $monthtonum[str2lower($month)];
 		$datearray[2]["year"]  = $year;
 		$datearray[2]["month"] = $month;

		$date   = gregorianToJewishGedcomDate($datearray);
		$HBMonth1 = $date[0]["month"];
		$HBYear1  = $date[0]["year"];
		$HBMonth2 = $date[1]["month"];
		$HBMonth3 = $date[2]["month"];

		$preghbquery1 = "2 DATE[^\n]*$HBMonth1";
		if ($REGEXP_DB) $query1 = "2 DATE[^\n]*$HBMonth1";
		else $query1 = "%2 DATE%$HBMonth1%";

		$myindilist1 = search_indis_dates("", $HBMonth1, "", $fact);
		$myfamlist1 = search_fams_dates("", $HBMonth1, "", $fact);

		$myindilist = pgv_array_merge($myindilist, $myindilist1);
		$myfamlist  = pgv_array_merge($myfamlist, $myfamlist1);

		if ($HBMonth1 != $HBMonth2) {
			$preghbquery2 = "2 DATE[^\n]*$HBMonth2";
			if ($REGEXP_DB) $query2 = "2 DATE[^\n]*$HBMonth2";
			else $query2 = "%2 DATE%$HBMonth2%";

			$myindilist1 = search_indis_dates("", $HBMonth2, "", $fact);
			$myfamlist1 = search_fams_dates("", $HBMonth2, "", $fact);

			$myindilist = pgv_array_merge($myindilist, $myindilist1);
			$myfamlist  = pgv_array_merge($myfamlist, $myfamlist1);
		}

		if ($HBMonth2 != $HBMonth3) {
			$preghbquery3 = "2 DATE[^\n]*$HBMonth3";
			if ($REGEXP_DB) $query3 = "2 DATE[^\n]*$HBMonth3";
			else $query3 = "%2 DATE%$HBMonth3%";

			$myindilist1 = search_indis_dates("", $HBMonth3, "", $fact);
			$myfamlist1 = search_fams_dates("", $HBMonth3, "", $fact);

			$myindilist = pgv_array_merge($myindilist, $myindilist1);
			$myfamlist  = pgv_array_merge($myfamlist, $myfamlist1);
		}

		if (!isJewishLeapYear($HBYear1) && ($HBMonth1 == "adr" || $HBMonth2 == "adr" || $HBMonth3 == "adr")) {
			$HBMonth4 = "ads";
			$preghbquery4 = "2 DATE[^\n]*$HBMonth4";
			if ($REGEXP_DB) $query4 = "2 DATE[^\n]*$HBMonth4";
			else $query4 = "%2 DATE%$HBMonth4%";

			$myindilist1 = search_indis_dates("", $HBMonth4, "", $fact);
			$myfamlist1 = search_fams_dates("", $HBMonth4, "", $fact);

			$myindilist = pgv_array_merge($myindilist, $myindilist1);
			$myfamlist  = pgv_array_merge($myfamlist, $myfamlist1);
		}
	}

	if (!empty($filtersx)) {
		$add2myindilist = array();
		foreach($myfamlist as $gid=>$fam) {
			$parents = find_parents($gid);
			if ($filtersx=="M") $add2myindilist[$parents["HUSB"]] = $fam["gedcom"];
			else $add2myindilist[$parents["WIFE"]] = $fam["gedcom"];
		}
		$myindilist = search_indis_fam($add2myindilist);
	}
	uasort($myindilist, "itemsort");
	for($k=0; $k<6; $k++) {
		print "\t<tr>\n";
		for($j=0; $j<7; $j++) {
			$mday = adodb_date("j", $monthstart);
			$mmon = strtolower(adodb_date("M", $monthstart));

			print "\t\t<td class=\"optionbox wrap\">\n";
			if ($show_no_day==0 && $j==0 && $k==0) $show_not_set=true;
			else if ($show_no_day==$k && $j==6) $show_not_set=true;
			if ($mmon==strtolower($month)||($show_not_set)) {
				if ($show_not_set) {
					$pregquery = "2 DATE(|[^\n]*[^\d]+|[^\n]*([ |0]0)|[^\n]*3[$lastday-9]|[^\n]*[4-9][0-9]) $month";

					// I see April 1973 in 2004 both correctly in April and in March with another event

					// Include here Hebrew dates that do not convert to a Gregorian date (same into blocks) - like 31 NSN 5724

					if ($USE_RTL_FUNCTIONS) {
						    $preghbquery1 = "";
						    $preghbquery2 = "";
						    $preghbquery3 = "";

						 	$datearray[0]["day"]   = 01;
 							$datearray[0]["mon"]   = $monthtonum[str2lower($month)];
 							$datearray[0]["year"]  = $year;
 							$datearray[0]["month"] = $month;
 							// should use $parse_date

    						$date    = gregorianToJewishGedcomDate($datearray);
 							$HBMonth = $date[0]["month"];
 							$HBYear  = $date[0]["year"];

					        if (!isJewishLeapYear($HBYear) && ($HBMonth == "adr")) {
								$HBMonth1 = "ads";
                                $preghbquery  = "2 DATE(|[^\n]*[^\d]+|[^\n]*([ |0]0)|[^\n]*[3][1-9]|[^\n]*[4-9][0-9]) [$HBMonth|$HBMonth1]";
					        }
				        	else {
				        		$preghbquery  = "2 DATE(|[^\n]*[^\d]+|[^\n]*([ |0]0)|[^\n]*[3][1-9]|[^\n]*[4-9][0-9]) $HBMonth";
			        	    }
				    }
				}
				else {
					$day = $mday;
					$currentDay = false;
					if(($year == adodb_date("Y")) && (strtolower($month) == strtolower(adodb_date("M"))) && ($mday == adodb_date("j"))) //current day
						$currentDay = true;
					print "<span class=\"cal_day". ($currentDay?" current_day":"") ."\">".$mday."</span>";
					if ($CALENDAR_FORMAT=="hebrew_and_gregorian" || $CALENDAR_FORMAT=="hebrew" ||
						(($CALENDAR_FORMAT=="jewish_and_gregorian" || $CALENDAR_FORMAT=="jewish" || ($USE_RTL_FUNCTIONS &&  $HEBREWFOUND[$GEDCOM] == true)) && $LANGUAGE == "hebrew")) {
						$monthTemp = $monthtonum[strtolower($month)];
						$jd = gregoriantojd($monthTemp, $mday, $year);
						$hebrewDate = jdtojewish($jd);
						// if ($USE_RTL_FUNCTIONS &&  $HEBREWFOUND[$GEDCOM] == true) {
							list ($hebrewMonth, $hebrewDay, $hebrewYear) = split ('/', $hebrewDate);
							print "<span class=\"rtl_cal_day". ($currentDay?" current_day":"") ."\">";
							print getHebrewJewishDay($hebrewDay) . " " .getHebrewJewishMonth($hebrewMonth, $hebrewYear) . "</span>";
						// }
					}
					else if($CALENDAR_FORMAT=="jewish_and_gregorian" || $CALENDAR_FORMAT=="jewish" || ($USE_RTL_FUNCTIONS && $HEBREWFOUND[$GEDCOM] == true)) {
						// else if($CALENDAR_FORMAT=="jewish_and_gregorian" || $CALENDAR_FORMAT=="jewish" || $USE_RTL_FUNCTIONS) {
						$monthTemp = $monthtonum[strtolower($month)];
						$jd = gregoriantojd($monthTemp, $mday, $year);
						$hebrewDate = jdtojewish($jd);
						// if ($USE_RTL_FUNCTIONS &&  $HEBREWFOUND[$GEDCOM] == true) {
							list ($hebrewMonth, $hebrewDay, $hebrewYear) = split ('/', $hebrewDate);
							print "<span class=\"rtl_cal_day". ($currentDay?" current_day":"") ."\">";
							print $hebrewDay . " " . getJewishMonthName($hebrewMonth, $hebrewYear) . "</span>";
						// }
					}
					else if($CALENDAR_FORMAT=="hijri") {
						$monthTemp = $monthtonum[strtolower($month)];
						$hDate = getHijri($mday, $monthTemp, $year);
						list ($hMonthName, $hDay, $hYear) = split ('/', $hDate);
						print "<span class=\"rtl_cal_day". ($currentDay?" current_day":"") ."\">";
						print $hDay . " " . $hMonthName . "</span>";
					}
					print "<br style=\"clear: both\" />";
					$dayindilist = array();

					if ($mday<10) $pregquery = "2 DATE[^\n]*[ |0]$mday $mmon";
					else if (!$leap && $mmon == "feb" && $mday == '28') $pregquery = "2 DATE[^\n]*2[8|9] $mmon";
					else $pregquery = "2 DATE[^\n]*$mday $mmon";

					if ($USE_RTL_FUNCTIONS) {
						    $preghbquery1 = "";
						    $preghbquery2 = "";
						    $preghbquery3 = "";
						 	$datearray[0]["day"]   = $mday;
 							if (isset($monthTemp)) $datearray[0]["mon"]   = $monthTemp;
 							else $monthTemp = "";
 							$datearray[0]["year"]  = $year;
 							$datearray[0]["month"] = $mmon;
 							// should use $parse_date

    						$date    = gregorianToJewishGedcomDate($datearray);
 							$HBDay   = $date[0]["day"];
							$HBMonth = $date[0]["month"];
							$HBYear  = $date[0]["year"];

	// is there a better way to add 1 day to the $datearray than changing the day and using jewishGedcomDateToGregorian
	// for Yartzeit
	// KJ definitions - need parameters
	//     what to do in ADR
	//     if ADR 30 does not occur show on SHV 30 or NSN 01 (?) ...
	//     if CSH 30 does not occur show on KSL 01 or CHS 29     ...
	//     if KSL 30 does not occur show on TVT 01 or KSL 29     ...

							if ($HBDay == '29' and ($HBMonth=='adr' || $HBMonth=='csh' || $HBMonth=='ksl')) {
								// handle day 30 in day 29 for ADR, CSH and KSL ???
								//                   2003       30   30      30
								//					 2004	    29   29      29  No ADR
								//					 2005       30   29      30
								$date[0]["day"]='30';
								$datearray     = jewishGedcomDateToGregorian($date);
								$date          = gregorianToJewishGedcomDate($datearray);
					        }

					        $HBDay1   = 30;

							if (!isJewishLeapYear($HBYear) && $HBMonth == "adr") {
								$HBMonth1 = "ads";
								if ($HBDay<10) {
									$preghbquery  = "2 DATE[^\n]*[ |0]$HBDay $HBMonth";
									$preghbquery1 = "2 DATE[^\n]*[ |0]$HBDay $HBMonth1";
								}
								else {
									$preghbquery  = "2 DATE[^\n]*$HBDay $HBMonth";
									$preghbquery1 = "2 DATE[^\n]*$HBDay $HBMonth1";

									if ($HBMonth != $date[0]["month"])
									   $preghbquery2 = "2 DATE[^\n]*$HBDay1 $HBMonth";
									   $preghbquery3 = "2 DATE[^\n]*$HBDay1 $HBMonth1";
								}
							}
							else
							    if ($HBDay<10) $preghbquery = "2 DATE[^\n]*[ |0]$HBDay $HBMonth";
							    else {
								    $preghbquery = "2 DATE[^\n]*$HBDay $HBMonth";
							        if ($HBMonth != $date[0]["month"])
					                   $preghbquery2 = "2 DATE[^\n]*$HBDay1 $HBMonth";
						        }
				    }
				}
				$text_day = "";
				$count_private = 0;
				$sx=1;
				foreach($myindilist as $gid=>$indi) {
					//print $gid;
					if (!empty($filtersx)) $sx = preg_match("/1 SEX $filtersx/i", $indi["gedcom"]);
					if ((($filterof!="living")||(is_dead_id($gid)!=1))&& $sx>0) {

						if (preg_match("/$pregquery/i", $indi["gedcom"])>0 || ($USE_RTL_FUNCTIONS && (preg_match("/$preghbquery/i", $indi["gedcom"])>0 || ($preghbquery1!="" && preg_match("/$preghbquery1/i", $indi["gedcom"])>0) || ($preghbquery2!="" && preg_match("/$preghbquery2/i", $indi["gedcom"])>0) || ($preghbquery3!="" && preg_match("/$preghbquery3/i", $indi["gedcom"])>0)))) {
							$filterout=false;
							$indilines = split("\n", $indi["gedcom"]);
							$factrec = "";
							$lct = count($indilines);
							$text_fact = "";
							for($i=1; $i<=$lct; $i++) {
								$text_temp = "";
								if ($i<$lct) $line = $indilines[$i];
								if (empty($line)) $line = " ";
								if ($i==$lct||($line{0}=="1")) {
									if (!empty($factrec)) {
										$ct = preg_match("/$pregquery/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS) $ct = preg_match("/$preghbquery/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS && $preghbquery1!="") $ct = preg_match("/$preghbquery1/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS && $preghbquery2!="") $ct = preg_match("/$preghbquery2/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS && $preghbquery3!="") $ct = preg_match("/$preghbquery3/i", $factrec, $match);
										if ($ct>0) {
											$text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
										}
									}
									$factrec="";
								}
								if ($text_temp=="filter") $filterout=true;
								else $text_fact .= $text_temp;
								$factrec.=$line."\n";
							}
							if (!empty($text_fact) && displayDetailsById($gid)) {
								$text_day .= "<a href=\"individual.php?pid=$gid&amp;GEDCOM=".get_gedcom_from_id($indi["gedfile"])."\"><b>".PrintReady(check_NN(get_sortable_name($gid)))."</b>";
								if ($SHOW_ID_NUMBERS) {
						      	    if ($TEXT_DIRECTION=="ltr") $text_day .= " &lrm;($gid)&lrm;";
							        else $text_day .= " &rlm;($gid)&rlm;";
						        }
								$text_day .= "</a><br />\n";
								$text_day .= "<div class=\"indent";
								if($TEXT_DIRECTION == "rtl") $text_day .= "_rtl";
								$text_day .= " $TEXT_DIRECTION\">";
								$text_day .= $text_fact;
								$text_day .= "</div><br />\n";
							}
							else if (!$filterout) $count_private++;
						}
					}
				}
				$dayfamlist = array();
				reset($myfamlist);
				$count_private_fam = 0;
				if ($filtersx==""){
					foreach($myfamlist as $gid=>$fam) {
						$display=true;
						if ($filterof=="living"){
							$parents = find_parents($gid);
							if (is_dead_id($parents["HUSB"]) || is_dead_id($parents["WIFE"])) $display=false;
						}
						if ($display and strpos(find_gedcom_record($gid), "1 DIV")!==false) $display = false;
						if ($display) {
			    			if (preg_match("/$pregquery/i", $fam["gedcom"])>0 || ($USE_RTL_FUNCTIONS && (preg_match("/$preghbquery/i", $fam["gedcom"])>0 || ($preghbquery1!="" && preg_match("/$preghbquery1/i", $fam["gedcom"])>0) || ($preghbquery2!="" && preg_match("/$preghbquery2/i", $fam["gedcom"])>0) || ($preghbquery3!="" && preg_match("/$preghbquery3/i", $fam["gedcom"])>0)))) {

								$filterout=false;
								$name = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/","/^[a-z. ]*/"), array(",",",",""), $fam["name"]);
								$names = preg_split("/[,+]/", $name);
								$fam["name"] = check_NN($names);
								$indilines = split("\n", $fam["gedcom"]);
								$factrec = "";
								$lct = count($indilines);
								$text_fact = "";
								for($i=1; $i<=$lct; $i++) {
									$text_temp = "";
									if ($i<$lct) $line = $indilines[$i];
									if (empty($line)) $line = " ";
									if ($i==$lct||($line{0}=="1")) {

									if (!empty($factrec)) {
										$ct = preg_match("/$pregquery/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS) $ct = preg_match("/$preghbquery/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS && $preghbquery1!="") $ct = preg_match("/$preghbquery1/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS && $preghbquery2!="") $ct = preg_match("/$preghbquery2/i", $factrec, $match);
										if ($ct < 1 && $USE_RTL_FUNCTIONS && $preghbquery3!="") $ct = preg_match("/$preghbquery3/i", $factrec, $match);
										if ($ct>0) {
											$text_temp .= get_calendar_fact($factrec, $action, $filterof, $gid, $filterev);
										}
									}
									$factrec="";

									}
									if ($text_temp=="filter") $filterout=true;
									else $text_fact .= $text_temp;
									$factrec.=$line."\n";
								}
								if (!empty($text_fact) && displayDetailsById($gid, "FAM")) {
									$text_day .= "<a href=\"family.php?famid=$gid&amp;GEDCOM=".get_gedcom_from_id($fam["gedfile"])."\"><b>".PrintReady(get_family_descriptor($gid))."</b>";
									$text_day .= "</a><br />\n";
									$text_day .= "<div class=\"indent";
									if ($TEXT_DIRECTION == "rtl") $text_day .= "_rtl";
									$text_day .= "\">";
									$text_day .= $text_fact;
									$text_day .= "</div><br />\n";
								}
								else if (!$filterout) $count_private++;
							}
						}
					}
				}
				// Print the calendar day list
				if ($show_not_set){
					if ($text_day!=""){
						print "<span class=\"cal_day\"";
						if ($TEXT_DIRECTION == "rtl") print	" style=\"float: right;\"";
						print " >".$pgv_lang["day_not_set"]."</span>";
						print "<br style=\"clear: both\" />";
					}
					$show_not_set=false;
					$show_no_day=-1;
				}
				print "<div id=\"day$k-$j\" class=\"details1\" style=\"width: 120px; height: ";
				if ($view=="preview") print "100%;";
				else print "150px; overflow: auto;";
				print "\">\n";
				print $text_day;
				if ($count_private>0){
					print_help_link("privacy_error_help", "qm", "private");
					print "<a name=\"p\">".$pgv_lang["private"];
					print "</a> (".$count_private.") ";
					print "<br />\n";
				}
				if ($view=="preview"){
					print "<br />";
					if (empty($text_day)) print "<br /><br /><br />";
				}
				print "</div>\n";
			}
			else print "<br />\n";
			print "\t\t</td>\n";
			$monthstart+=(60*60*24);
			$mmon = strtolower(adodb_date("M", $monthstart));
			if (($mmon!=strtolower($month)) && ($k>2)) {
				if ($show_no_day==6){
					$show_no_day=$k;
					if ($j==6) $show_no_day++;
				}
				else if ($show_no_day<0) $k=6;
			}
		} //-- end day for loop
		print "\t</tr>\n";
	} //-- end week for loop
	if ($view=="preview") print "<tr><td colspan=\"7\">";
}
if ($view=="preview"){
	if (isset($myindilist[$gid]["gedfile"])) $showfile=get_gedcom_from_id($myindilist[$gid]["gedfile"]);
	else $showfile=get_gedcom_from_id($myfamlist[$gid]["gedfile"]);
	$showfilter="";
	if ($filterof!="all") $showfilter = ($filterof=="living"?$pgv_lang["living_only"]:$pgv_lang["recent_events"]);
	if (!empty($filtersx)){
		if (!empty($showfilter)) $showfilter .= " - ";
		$showfilter .= ($filtersx=="M"?$pgv_lang["male"]:$pgv_lang["female"]);
	}
	if ($filterev != "all"){
		if (!empty($showfilter)) $showfilter .= " - ";
		$showfilter .= $factarray[$filterev];
	}
	print "<br />".$showfile." (".$pgv_lang["filter"].": ";
	if (!empty($showfilter)) print $showfilter.")\n";
	else print $pgv_lang["all"].")\n";
	print "</td></tr>";
}
print "</table>\n";
print "</div><br />\n";
print_footer();
?>

<?php
/**
 * Individual List
 *
 * Copyright (c) 2008, PGV Development Team, all rights reserved.
 *
 * The individual list shows all individuals from a chosen gedcom file. The list is
 * setup in two sections. The alphabet bar and the details.
 *
 * The alphabet bar shows all the available letters users can click. The bar is built
 * up from the lastnames first letter. Added to this bar is the symbol @, which is
 * shown as a translated version of the variable <var>pgv_lang["NN"]</var>, and a
 * translated version of the word ALL by means of variable <var>$pgv_lang["all"]</var>.
 *
 * The details can be shown in two ways, with surnames or without surnames. By default
 * the user first sees a list of surnames of the chosen letter and by clicking on a
 * surname a list with names of people with that chosen surname is displayed.
 *
 * Beneath the details list is the option to skip the surname list or show it.
 * Depending on the current status of the list.
 *
 * @package PhpGedView
 * @subpackage Lists
 * @version $Id$
 */

/**
 * load the main configuration and context
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require_once('includes/functions_print_lists.php');

/**
 * is the person alive in the given year
 * @param string $indirec	the persons raw gedcom record
 * @param int $year			the year to check if they are alive in
 * @return int			return 0 if the person is alive, negative number if they died earlier, positive number if they will be born in the future
 */
function check_alive($indirec, $year) {
	global $MAX_ALIVE_AGE;
	if (is_dead($indirec, $year)) return -1;

	// Died before year?
	$deathrec = get_sub_record(1, "1 DEAT", $indirec);
	if (preg_match("/\d DATE (.*)/", $deathrec, $match)) {
		$ddate = new GedcomDate($match[1]);
		$dyear=$ddate->gregorianYear();
		if ($year>$dyear) {
			return -1;
		}
	}

	// Born after year?
	$birthrec = get_sub_record(1, "1 BIRT", $indirec);
	if (preg_match("/\d DATE (.*)/", $birthrec, $match)) {
		$bdate = new GedcomDate($match[1]);
		$byear=$bdate->gregorianYear();
		if ($year<$byear) {
			return 1;
		}
	}

	// Born before year and died after year
	if (isset($byear) && isset($dyear) && $year>=$byear && $year<=$dyear)
			return 0;

	// If no death record than check all dates;
	$years = array();
	$subrecs = get_all_subrecords($indirec, "CHAN", true, true, false);
	foreach($subrecs as $ind=>$subrec)
		if (preg_match("/\d DATE (.*)/", $subrec, $match)) {
			$date = new GedcomDate($match[1]);
			$datey= $date->gregorianYear();
			if ($datey) {
				$years[] = $datey;
			}
		}

	// Events both before and after year
	if (count($years)>1 && $year>=$years[0] && $year<=$years[count($years)-1]) {
		return 0;
	}

	foreach($years as $ind=>$year1) {
		if (($year1-$year) > $MAX_ALIVE_AGE) {
			return -1;
		}
	}

	return 0;
}
//-- end functions

if (isset($_REQUEST['surname_sublist'])) $surname_sublist = $_REQUEST['surname_sublist'];
if (isset($_REQUEST['show_all'])) $show_all = $_REQUEST['show_all'];
if (isset($_REQUEST['show_all_firstnames'])) $show_all_firstnames = $_REQUEST['show_all_firstnames'];
if (isset($_REQUEST['year'])) $year = $_REQUEST['year'];
if (isset($_REQUEST['alpha'])) $alpha = $_REQUEST['alpha'];
if (isset($_REQUEST['surname'])) $surname = $_REQUEST['surname'];
if (isset($_REQUEST['view'])) $view = $_REQUEST['view'];


if (empty($surname_sublist)) $surname_sublist = "yes";
if (empty($show_all)) $show_all = "no";
if (empty($show_all_firstnames)) $show_all_firstnames = "no";
if (empty($year)) $year=date("Y");

// Remove slashes
if (isset($alpha)) $alpha = stripslashes($alpha);
if (isset($surname)) $surname = stripslashes($surname);

print_header($pgv_lang["alive_in_year"]);
print "<div class =\"center\">";
print "\n\t<h2>".str_replace("#YEAR#", $year, $pgv_lang["is_alive_in"]);
print_help_link("alive_in_year_help", "qm");
print "</h2>";

if ($view!="preview") {
	print "\n\t<form name=\"newyear\" action=\"aliveinyear.php\" method=\"get\">";
	if (!empty($alpha)) print "\n\t\t<input type=\"hidden\" name=\"alpha\" value=\"$alpha\" />";
	if (!empty($surname)) print "\n\t\t<input type=\"hidden\" name=\"surname\" value=\"$surname\" />";
	print "\n\t\t<input type=\"hidden\" name=\"surname_sublist\" value=\"$surname_sublist\" />";
	print "\n\t\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t\t<tr>";
	print "\n\t\t\t<td class=\"list_label\">&nbsp;" . $pgv_lang["year"] . "&nbsp;</td>";
	print "\n\t\t\t<td class=\"list_value\">";
	print "\n\t\t\t\t<input class=\"pedigree_form\" type=\"text\" name=\"year\" size=\"3\" value=\"$year\" />";
	print "\n\t\t\t\t";
	print_help_link("year_help", "qm");
	print "\n\t\t\t</td>";
	print "\n\t\t\t<td rowspan=\"3\" class=\"list_value\">";
	print "<input type=\"submit\" value=\"".$pgv_lang["view"]."\" /></td>";
	print "\n\t\t\t</tr>\n\t\t</table>";
	print "\n\t</form>\n";
}

/**
 * Check for the @ symbol
 *
 * This variable is used for checking if the @ symbol is present in the alphabet list.
 * @global boolean $pass
 */
$pass = FALSE;

/**
 * Total indilist array
 *
 * The tindilist array will contain individuals that are extracted from the database.
 * @global array $tindilist
 */
$tindilist = array();

/**
 * Individual alpha array
 *
 * The indialpha array will contain all first letters that are extracted from an individuals
 * lastname.
 * @global array $indialpha
 */
$indialpha = get_indi_alpha();

if (isset($alpha) && !in_array($alpha, $indialpha)) unset($alpha);

if (count($indialpha) > 0) {
	foreach($indialpha as $letter) {
		if (empty($alpha)) {
			if (!empty($surname)) {
				$alpha = get_first_letter(strip_prefix($surname));
			}
		}
		if ($letter != "@") {
			if (!isset($startalpha) && !isset($alpha)) {
				$startalpha = $letter;
				$alpha = $letter;
			}
			print "<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($letter)."&amp;surname_sublist=$surname_sublist\">";
			if (($alpha==$letter)&&($show_all=="no")) print "<span class=\"warning\">".$letter."</span>";
			else print $letter;
			print "</a> | \n";
		}
		if ($letter === "@") $pass = TRUE;
	}
	if ($pass == TRUE) {
		if (isset($alpha) && $alpha == "@") print "<a href=\"aliveinyear.php?year=$year&amp;alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
		else print "<a href=\"aliveinyear.php?year=$year&amp;alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\">".PrintReady($pgv_lang["NN"])."</a>";
		print " | \n";
		$pass = FALSE;
	}
	if ($show_all=="yes") print "<a href=\"aliveinyear.php?year=$year&amp;show_all=yes&amp;surname_sublist=$surname_sublist\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
	else print "<a href=\"aliveinyear.php?year=$year&amp;show_all=yes&amp;surname_sublist=$surname_sublist\">".$pgv_lang["all"]."</a>\n";
	if (isset($startalpha)) $alpha = $startalpha;
}
print_help_link("alpha_help", "qm");

//-- escaped letter for regular expressions
$expalpha = $alpha;
if ($expalpha=="(" || $expalpha=="[" || $expalpha=="?" || $expalpha=="/" || $expalpha=="*" || $expalpha=="+") $expalpha = "\\".$expalpha;

$TableTitle = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" title=\"".$pgv_lang["individuals"]."\" alt=\"".$pgv_lang["individuals"]."\" />&nbsp;&nbsp;".$pgv_lang["surnames"];

print "<br /><br /><table class=\"list_table $TEXT_DIRECTION\"><tr>";
if (($surname_sublist=="yes")&&($show_all=="yes")) {
	get_indi_list();
	if (!isset($alpha)) $alpha="";
	$surnames = array();
	$indi_hide=array();
	$indi_dead=0;
	$indi_unborn=0;
	$indi_alive = 0;
	foreach($indilist as $gid=>$indi) {
		//-- make sure that favorites from other gedcoms are not shown
		if ($indi["gedfile"]==PGV_GED_ID) {
			if (displayDetailsById($gid)||showLivingNameById($gid)) {
				$ret = check_alive($indi["gedcom"], $year);
				if ($ret==0) {
					foreach($indi["names"] as $indexval => $name) {
						surname_count($name[2]);
						$indi_alive++;
					}
				}
				else if ($ret<0) $indi_dead++;
				else $indi_unborn++;
			}
			else $indi_hide[$gid."[".$indi["gedfile"]."]"]=1;
		}
	}
	$i = 0;
	uasort($surnames, "itemsort");
	$count = count($surnames);
	$count_indi = 0;
	$col = 1;
	if ($count>36) $col=4;
	else if ($count>18) $col=3;
	else if ($count>6) $col=2;
	$newcol=ceil($count/$col);
	print "<td class=\"list_label\" colspan=\"$col\">";
	print $TableTitle;
	print "</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";

// Surnames with starting and ending letters in 2 text orientations is shown in a wrong way on the page with different orientation from the orientation of the first name letter

	foreach($surnames as $surname=>$namecount) {
		if (begRTLText($namecount["name"])) {
			print "<div class =\"rtl\" dir=\"rtl\">&nbsp;<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($namecount["alpha"])."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">&nbsp;".PrintReady($namecount["name"]) . getRLM() . " - [".($namecount["match"])."]" . getRLM();
		}
		else if (substr($namecount["name"], 0, 5) == "@N.N.") {
			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"aliveinyear.php?year=$year&amp;alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=@N.N.\">&nbsp;".$pgv_lang["NN"] . getLRM() . " - [".($namecount["match"])."]" . getLRM() . "&nbsp;";
		}
		else {
			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($namecount["alpha"])."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".PrintReady($namecount["name"]) . getLRM() . " - [" . ($namecount["match"])."]" . getLRM();
		}

		print "</a></div>\n";
		$count_indi += $namecount["match"];
		$i++;
		if ($i==$newcol && $i<$count) {
			print "</td><td class=\"list_value\" style=\"padding: 14px;\">\n";
			$newcol=$i+ceil($count/$col);
		}
	}
	print "</td>\n";
	if ($count>1 || count($indi_hide)>0) {
		print "</tr><tr><td colspan=\"$col\" align=\"center\">&nbsp;";
		if ($SHOW_MARRIED_NAMES && $count>1) print $pgv_lang["total_names"]." ".$count_indi."<br />";
		if ($count>1) print $pgv_lang["total_indis"]." ".count($indilist)."&nbsp;";
		if ($count>1 && count($indi_hide)>0) print "--&nbsp;";
		if (count($indi_hide)>0) print $pgv_lang["hidden"]." ".count($indi_hide);
		if ($count>1) print "<br />".$pgv_lang["surnames"]." ".$count;
		print "<br />".$pgv_lang["total_living"]." $indi_alive -- ".$pgv_lang["total_dead"]." $indi_dead -- ".$pgv_lang["total_not_born"]." ".$indi_unborn;
		print "</td>\n";
	}
}
else if (($surname_sublist=="yes")&&(empty($surname))&&($show_all=="no")) {
	if (!isset($alpha)) $alpha="";
	$tindilist=array();
	// get all of the individuals whose last names start with this letter
	$tindilist = get_alpha_indis($alpha);
	$surnames = array();
	$indi_hide=array();
	$indi_dead=0;
	$indi_unborn=0;
	$indi_alive = 0;
	$temp = 0;
	$surnames = array();
	foreach($tindilist as $gid=>$indi) {
		if ((displayDetailsByID($gid))||(showLivingNameById($gid))) {
			$ret = check_alive($indi["gedcom"], $year);
			if ($ret==0) {
				$indi_alive++;
				foreach($indi["names"] as $name) {
					if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
						if ($alpha == "\xC3\x98") $text = "OE";
						else if ($alpha == "\xC3\x86") $text = "AE";
						else if ($alpha == "\xC3\x85") $text = "AA";
						if (isset($text)) {
							if ((preg_match("/^$expalpha/", $name[1])>0)||(preg_match("/^$text/", $name[1])>0)) surname_count($name[2]);
						}
						else if (preg_match("/^$expalpha/", $name[1])>0) surname_count($name[2]);
					}
					else {
						if (preg_match("/^$expalpha/", $name[1])>0) surname_count($name[2]);
					}
				}
			}
			else if ($ret<0) $indi_dead++;
			else $indi_unborn++;
		}
		else $indi_hide[$gid."[".$indi["gedfile"]."]"]=1;
	}
	$i = 0;
	uasort($surnames, "itemsort");
	$count = count($surnames);
	$count_indi = 0;
	$col = 1;
	if ($count>36) $col=4;
	else if ($count>18) $col=3;
	else if ($count>6) $col=2;
	$newcol=ceil($count/$col);
	print "<td class=\"list_label\" colspan=\"$col\">";
	print $TableTitle;
	print "</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";

	foreach($surnames as $surname=>$namecount) {
		if (begRTLText($namecount["name"])) {
			print "<div class =\"rtl\" dir=\"rtl\">&nbsp;<a href=\"aliveinyear.php?year=$year&amp;alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">&nbsp;".PrintReady($namecount["name"]) . getRLM() . " - [".($namecount["match"])."]" . getRLM();
		}
		else if (substr($namecount["name"], 0, 5) == "@N.N.") {
			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"aliveinyear.php?year=$year&amp;alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=@N.N.\">&nbsp;".$pgv_lang["NN"] . getLRM() . " - [".($namecount["match"])."]" . getLRM() . "&nbsp;";
		}
		else {
			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"aliveinyear.php?year=$year&amp;alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".PrintReady($namecount["name"]) . getLRM() . " - [".($namecount["match"])."]" . getLRM();
		}
		print "</a>&nbsp;</div>\n";
		$count_indi += $namecount["match"];
		$i++;
		if ($i==$newcol && $i<$count) {
			print "</td><td class=\"list_value\" style=\"padding: 14px;\">\n";
			$newcol=$i+ceil($count/$col);
		}
	}
	print "</td>\n";
	print "</tr><tr><td colspan=\"$col\" align=\"center\">&nbsp;";
	if ($SHOW_MARRIED_NAMES && $count>1) print $pgv_lang["total_names"]." ".$count_indi;
	if (count($indi_hide)>0) print $pgv_lang["hidden"]." ".count($indi_hide);
	if ($count>1) print "<br />".$pgv_lang["surnames"]." ".$count;
	print "<br />".$pgv_lang["total_living"]." $indi_alive -- ".$pgv_lang["total_dead"]." $indi_dead -- ".$pgv_lang["total_not_born"]." ".$indi_unborn;
	print "</td>\n";
}
else {
	$firstname_alpha = false;
	$indi_dead=0;
	$indi_unborn=0;
	//-- if the surname is set then only get the names in that surname list
	if ((!empty($surname))&&($surname_sublist=="yes")) {
		$surname = trim($surname);
		$tindilist = get_surname_indis($surname);
		//-- split up long surname lists by first letter of first name
		if (count($tindilist)>500) $firstname_alpha = true;
		print "<div class=\"center\"><b>".PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["indis_with_surname"]))."</b><br /></div>";
	}

	if (($surname_sublist=="no")&&(!empty($alpha))&&($show_all=="no")) {
		$tindilist = get_alpha_indis($alpha);
	}

	//-- simplify processing for ALL indilist
	if (($surname_sublist=="no")&&($show_all=="yes")) {
		print "<td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>\n";
		$tindilist = get_indi_list();
		$names = array();
		$total_living = 0;
		foreach($tindilist as $gid => $indi) {
			//-- make sure that favorites from other gedcoms are not shown
			if ($indi["gedfile"]==PGV_GED_ID) {
				$ret = check_alive($indi["gedcom"], $year);
				if ($ret==0) {
					foreach($indi["names"] as $indexval => $namearray) {
						$names[] = array($namearray[0], $namearray[1], $namearray[2], $namearray[3], $gid);
					}
					$total_living++;
				}
				else if ($ret<0) $indi_dead++;
				else $indi_unborn++;
			}
		}
		uasort($names, "itemsort");
		reset($names);
		$total_indis = count($indilist);
		$count = count($names);
		$i=0;
		foreach($names as $indexval => $namearray) {
			$name = check_NN(sortable_name_from_name($namearray[0]));
			echo format_list_person($namearray[4], array($name, $GEDCOM));
			$i++;
			if ($i==ceil($count/2) && $count>8) print "</ul></td><td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>\n";
		}
		print "</ul></td>\n";
		print "</tr><tr><td colspan=\"2\" align=\"center\">";
		if ($SHOW_MARRIED_NAMES) print $pgv_lang["total_names"]." ".$count."<br />\n";
		print $pgv_lang["total_indis"]." ".$total_indis;
		print "<br />".$pgv_lang["total_living"]." $total_living -- ".$pgv_lang["total_dead"]." $indi_dead -- ".$pgv_lang["total_not_born"]." ".$indi_unborn;
		print "</td>\n";
	}
	else {
		//--- the list is really long so divide it up again by the first letter of the first name
		if ($firstname_alpha) {
			if (!isset($_SESSION[$surname."_firstalpha"])) {
				$firstalpha = array();
				foreach($tindilist as $gid=>$indi) {
					foreach($indi["names"] as $indexval => $namearray) {
						$letter = str2upper(get_first_letter($namearray[0]));
						if (!isset($firstalpha[$letter])) {
							$firstalpha[$letter] = array("letter"=>$letter, "ids"=>$gid);
						}
						else $firstalpha[$letter]["ids"] .= ",".$gid;
					}
				}
				uasort($firstalpha, "lettersort");
				//-- put the list in the session so that we don't have to calculate this the next time
				$_SESSION[$surname."_firstalpha"] = $firstalpha;
			}
			else $firstalpha = $_SESSION[$surname."_firstalpha"];
			print "<div class=\"center\">".$pgv_lang["first_letter_fname"]."<br />\n";
			foreach($firstalpha as $letter=>$list) {
				$PASS = false;
				if ($letter != "@") {
					if (!isset($fstartalpha) && !isset($falpha)) {
						$fstartalpha = $letter;
						$falpha = $letter;
					}
					print "<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=".urlencode($letter)."&amp;surname_sublist=$surname_sublist\">";
					if (($falpha==$letter)&&($show_all=="no")) print "<span class=\"warning\">".$letter."</span>";
					else print $letter;
					print "</a> | \n";
				}
				if ($letter === "@") $pass = TRUE;
			}
			if ($pass == TRUE) {
				if (isset($falpha) && $falpha == "@") print "<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
				else print "<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes\">".PrintReady($pgv_lang["NN"])."</a>";
				print " | \n";
				$pass = FALSE;
			}
			if ($show_all_firstnames=="yes") print "<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=no\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
			else print "<a href=\"aliveinyear.php?year=$year&amp;alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=yes\">".$pgv_lang["all"]."</a>\n";
			print_help_link("firstname_alpha_help", "qm");
			print "</div>\n";
			if (isset($fstartalpha)) $falpha = $fstartalpha;
			if ($show_all_firstnames=="no") {
				$findilist = array();
				$ids = preg_split("/,/", $firstalpha[$falpha]["ids"]);
				foreach($ids as $indexval => $id) {
					$findilist[$id] = $indilist[$id];
				}
				$tindilist = $findilist;
			}
		}
		//uasort($tindilist, "itemsort");
		print "<td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>\n";
		$count = count($tindilist);
		$i=0;
		$names = array();
		foreach($tindilist as $gid => $indi) {
			$ret = check_alive($indi["gedcom"], $year);
			if ($ret==0) {
				foreach($indi["names"] as $indexval => $namearray) {
					$text = "";
					if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
						if ($alpha == "\xC3\x98") $text = "OE";
						else if ($alpha == "\xC3\x86") $text = "AE";
						else if ($alpha == "\xC3\x85") $text = "AA";
					}
					if ((empty($surname)&&((preg_match("/^$alpha/", $namearray[1])>0)||(!empty($text)&&preg_match("/^$text/", $namearray[1])>0))) || (stristr($namearray[2], $surname)!==false)) {
						$name = check_NN(sortable_name_from_name($namearray[0]));
						$names[] = array("gid"=>$gid, "name"=>$name);
					}
				}
			}
			else if ($ret<0) $indi_dead++;
			else $indi_unborn++;
		}
		uasort($names, "itemsort");
		$count = count($names);
		$i=0;
		foreach($names as $indexval => $namearray) {
			$gid = $namearray["gid"];
			$name = $namearray["name"];
			$indi = $tindilist[$gid];
			echo format_list_person($gid, array($name, get_gedcom_from_id($indi["gedfile"])));
			$i++;
			if ($i==ceil($count/2) && $count>8) print "</ul></td><td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>\n";
		}
		print "</ul></td>\n";
		print "</tr><tr><td colspan=\"2\" align=\"center\">";
		if ($SHOW_MARRIED_NAMES) print $pgv_lang["total_names"]." ".$i."<br />\n";
		print $pgv_lang["total_indis"]." ".$count;
		print "<br />".$pgv_lang["total_living"]." $i -- ".$pgv_lang["total_dead"]." $indi_dead -- ".$pgv_lang["total_not_born"]." ".$indi_unborn;
		print "</td>\n";
	}
}
print "</tr></table>";

print_help_link("name_list_help", "qm");
if ($alpha != "@") {
	if ($surname_sublist=="yes") print "<br /><a href=\"aliveinyear.php?year=$year&amp;alpha=$alpha&amp;surname_sublist=no&amp;show_all=$show_all\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<br /><a href=\"aliveinyear.php?year=$year&amp;alpha=$alpha&amp;surname_sublist=yes&amp;show_all=$show_all\">".$pgv_lang["show_surnames"]."</a>";
}
if ($alpha != "@") print_help_link("skip_sublist_help", "qm");
print "<br /><br />\n";
print "</div>\n";
print_footer();

?>

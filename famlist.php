<?php
/**
 * Family List
 *
 * The Family list shows all families from a chosen gedcom file. The list is
 * setup in two sections. The alphabet bar and the details.
 *
 * The alphabet bar shows all the available letters users can click. The bar is built
 * up from the lastnames' first letter. Added to this bar is the symbol @, which is
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
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  PGV Development Team
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
 * This Page Is Valid XHTML 1.0 Transitional! > 24 August 2005
 *
 * @version $Id: famlist.php,v 1.5 2007/06/02 13:11:24 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();
// TODO - Bodge to get started
if(!isset($GEDCOM)) {
	$GEDCOM = "CAINEFull.GED";
	$GEDCOMS[$GEDCOM]["id"] = 2;
}

// leave manual config until we can move it to bitweaver table 
require_once("includes/functions_print_lists.php");

$sublistTrigger = 200;		// Number of names required before list starts sub-listing by first name

// Remove slashes
$lrm = chr(0xE2).chr(0x80).chr(0x8E);
$rlm = chr(0xE2).chr(0x80).chr(0x8F);
if (isset($_REQUEST['alpha']) ) { $alpha = $_REQUEST['alpha']; }
if (isset($_REQUEST['surname']) ) { $surname = $_REQUEST['surname']; }
if (isset($_REQUEST['surname_sublist']) ) { $surname_sublist = $_REQUEST['surname_sublist']; }
else $surname_sublist = 'yes';
if (isset($_REQUEST['show_all']) ) { $show_all = $_REQUEST['show_all']; }
if (empty($show_all)) $show_all = "no";

if (isset($alpha)) {
	$alpha = stripslashes($alpha);
	$alpha = str_replace(array($lrm, $rlm), "", $alpha);
	$doctitle = "Family List : ".$alpha;
}
if (isset($surname)) {
	$surname = stripslashes($surname);
	$surname = str_replace(array($lrm, $rlm), "", $surname);
	$doctitle = "Family List : ";
	if (empty($surname) or trim("@".$surname,"_")=="@" or $surname=="@N.N.") $doctitle .= $pgv_lang["NN"];
	else $doctitle .= $surname;
}

if (empty($show_all_firstnames)) $show_all_firstnames = "no";
if (empty($DEBUG)) $DEBUG = false;

/**
 * Check for the @ symbol
 *
 * This variable is used for checking if the @ symbol is present in the alphabet list.
 * @global boolean $pass
 */
$pass = false;

/**
 * Total famlist array
 *
 * The tfamlist array will contain families that are extracted from the database.
 * @global array $tfamlist
 */
$tfamlist = array();

/**
 * Family alpha array
 *
 * The famalpha array will contain all first letters that are extracted from families last names
 * @global array $famalpha
 */

$famalpha = get_fam_alpha();

//uasort($famalpha, "stringsort");
asort($famalpha);

if (isset($alpha) && !isset($famalpha["$alpha"])) unset($alpha);

// $TableTitle = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" title=\"".$pgv_lang["families"]."\" alt=\"".$pgv_lang["families"]."\" />&nbsp;&nbsp;";

if (count($famalpha) > 0) {
//	print_help_link("alpha_help", "qm", "alpha_index");
	foreach($famalpha as $letter=>$list) {
		if (empty($alpha)) {
			if (!empty($surname)) {
				$alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,1);
			}
		}
		if ($letter != "@") {
			if (!isset($startalpha) && !isset($alpha)) {
				$startalpha = $letter;
				$alpha = $letter;
			}
		}
		if ($letter === "@") $pass = true;
	}
	if (isset($startalpha)) $alpha = $startalpha;
	$gBitSmarty->assign_by_ref( "indialpha", $famalpha );
}

//print "<br /><br />";
//print_help_link("name_list_help", "qm", "name_list");
//print "<br /><br />";

//print "<table class=\"list_table $TEXT_DIRECTION\"><tr>";
if (($surname_sublist=="yes")&&($show_all=="yes")) {
	get_fam_list();
	if (!isset($alpha)) $alpha="";
	$surnames = array();
	$fam_hide = array();
	foreach($famlist as $gid=>$fam) {
		if (displayDetailsById($gid, "FAM")||showLivingNameById($gid, "FAM")) {
			$names = preg_split("/\+/", $fam["name"]);
			$foundnames = array();
			for($i=0; $i<count($names); $i++) {
				$name = trim($names[$i]);
				$sname = extract_surname($name);
				if (isset($foundnames[$sname])) {
					if (isset($surnames[$sname]["match"])) $surnames[$sname]["match"]--;
				}
				else $foundnames[$sname]=1;
			}
		}
		else $fam_hide[$gid."[".$fam["gedfile"]."]"] = 1;
	}
//	uasort($surnames, "itemsort");
asort($surnames);
	$n = 0;
	$total = 0;
	foreach($surnames as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		$url = "famlist.php?ged=".$GEDCOM."&amp;surname=".urlencode($surn);
		if (empty($surn) or trim("@".$surn,"_")=="@" or $surn=="@N.N.") $surn = tra('(unknown)');
		$surnames["$key"]['n'] = ++$n;
		$surnames["$key"]['surn'] = $surn;
		$surnames["$key"]['url'] = $url;
		$total += $value["match"];
	}
	$gBitSmarty->assign( "surname_total", $total );
	$gBitSmarty->assign_by_ref( "surnames", $surnames );
//	print_surn_table($surnames, "FAM");
}
else if (($surname_sublist=="yes")&&(empty($surname))&&($show_all=="no")) {
	if (!isset($alpha)) $alpha="";
	$tfamlist = get_alpha_fams($alpha);
	$surnames = array();
	$fam_hide = array();
	foreach($tfamlist as $gid=>$fam) {
		if ((displayDetailsByID($gid, "FAM"))||(showLivingNameById($gid, "FAM"))) {
			foreach($fam["surnames"] as $indexval => $name) {
				$lname = strip_prefix($name);
				if (empty($lname)) $lname = $name;
				$firstLetter=get_first_letter(str2upper($lname));
				if ($alpha==$firstLetter) surname_count(trim($name));
			}
		}
		else $fam_hide[$gid."[".$fam["gedfile"]."]"] = 1;
	}
	$i = 0;
//	uasort($surnames, "itemsort");
	asort($surnames);
	$n = 0;
	$total = 0;
	foreach($surnames as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		$url = "famlist.php?ged=".$GEDCOM."&amp;surname=".urlencode($surn);
		if (empty($surn) or trim("@".$surn,"_")=="@" or $surn=="@N.N.") $surn = tra('(unknown)');
		$surnames["$key"]['n'] = ++$n;
		$surnames["$key"]['surn'] = $surn;
		$surnames["$key"]['url'] = $url;
		$total += $value["match"];
	}
	$gBitSmarty->assign( "surname_total", $total );
	$gBitSmarty->assign_by_ref( "surnames", $surnames );
//	print_surn_table($surnames, "FAM");
}
else {
	$firstname_alpha = false;
	//-- if the surname is set then only get the names in that surname list
	if ((!empty($surname))&&($surname_sublist=="yes")) {
		$surname = trim($surname);
		$tfamlist = get_surname_fams($surname);
		//-- split up long surname lists by first letter of first name
		if (count($tfamlist)>$sublistTrigger) $firstname_alpha = true;
	}

	if (($surname_sublist=="no")&&(!empty($alpha))&&($show_all=="no")) {
		$tfamlist = get_alpha_fams($alpha);
	}

	//-- simplify processing for ALL famlist
	if (($surname_sublist=="no")&&($show_all=="yes")) {
		$tfamlist = get_fam_list();
		uasort($tfamlist, "itemsort");
	}
	else {
		//--- the list is really long so divide it up again by the first letter of the first name
		if ($firstname_alpha) {
			if (!isset($_SESSION[$surname."_firstalphafams"])||$DEBUG) {
				$firstalpha = array();
				foreach($tfamlist as $gid=>$fam) {
					$names = preg_split("/[,+] ?/", $fam["name"]);
					$letter = str2upper(get_first_letter(trim($names[1])));
					if (!isset($firstalpha[$letter])) {
						$firstalpha[$letter] = array("letter"=>$letter, "ids"=>$gid);
					}
					else $firstalpha[$letter]["ids"] .= ",".$gid;
					if (isset($names[2])&&isset($names[3])) {
						$letter = str2upper(get_first_letter(trim($names[2])));
						if ($letter==$alpha) {
							$letter = str2upper(get_first_letter(trim($names[3])));
							if (!isset($firstalpha[$letter])) {
								$firstalpha[$letter] = array("letter"=>$letter, "ids"=>$gid);
							}
							else $firstalpha[$letter]["ids"] .= ",".$gid;
						}
					}
				}
				uasort($firstalpha, "lettersort");
				//-- put the list in the session so that we don't have to calculate this the next time
				$_SESSION[$surname."_firstalphafams"] = $firstalpha;
			}
			else $firstalpha = $_SESSION[$surname."_firstalphafams"];
/*			print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
			print $TableTitle;
			print PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["fams_with_surname"]));
			print "</td></tr><tr>\n";
			print "<td style=\"text-align:center;\" colspan=\"2\">";
			print $pgv_lang["first_letter_fname"]."<br />\n";
			foreach($firstalpha as $letter=>$list) {
				$pass = false;
				if ($letter != "@") {
					if (!isset($fstartalpha) && !isset($falpha)) {
						$fstartalpha = $letter;
						$falpha = $letter;
					}
					print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=".urlencode($letter)."&amp;surname_sublist=".$surname_sublist."\">";
					if (($falpha==$letter)&&($show_all_firstnames=="no")) print "<span class=\"warning\">".$letter."</span>";
					else print $letter;
					print "</a> | \n";
				}
				if ($letter === "@") $pass = true;
			}
			if ($pass == true) {
				if (isset($falpha) && $falpha == "@") print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
				else print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes\">".PrintReady($pgv_lang["NN"])."</a>";
				print " | \n";
				$pass = false;
			}
			print_help_link("firstname_alpha_help", "qm", "firstname_alpha_index");
			if ($show_all_firstnames=="yes") print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=no\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
			else print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=yes\">".$pgv_lang["all"]."</a>\n";
			if (isset($fstartalpha)) $falpha = $fstartalpha;
			if ($show_all_firstnames=="no") {
				$ffamlist = array();
				$ids = preg_split("/,/", $firstalpha[$falpha]["ids"]);
				foreach($ids as $indexval => $id) {
					$ffamlist[$id] = $famlist[$id];
				}
				$tfamlist = $ffamlist;
			}
			print "</td></tr><tr>\n";
*/
		}
		uasort($tfamlist, "itemsort");
	}
}

if ($show_all=="yes") unset($alpha);
if (!empty($surname) && $surname_sublist=="yes") $legend = str_replace("#surname#", check_NN($surname), $pgv_lang["fams_with_surname"]);
else if (isset($alpha) and $show_all=="no") $legend = str_replace("#surname#", $alpha.".", $pgv_lang["fams_with_surname"]);
else $legend = $pgv_lang["families"];
if ($show_all_firstnames=="yes") $falpha = "@";
if (isset($falpha) and $falpha!="@") $legend .= " ".$falpha.".";
//$legend = PrintReady($legend);

if (!empty($surname) or $surname_sublist=="no") {
//	print_fam_table($tfamlist, $legend);
	$gBitSmarty->assign( "names", $tfamlist );
}
if (isset($alpha)) {
	$gBitSmarty->assign( "alpha", $alpha );
    if (!isset($doctitle)) $doctitle = "Family List : ".$alpha;
}
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSystem->display( 'bitpackage:phpgedview/famlist.tpl', tra( 'Family selection list' ) );
?>
<?php
/**
 * Individual List
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
 * NOTE: indilist.php and famlist.php contain mostly identical code.
 * Updates to one file almost certainly need to be made to the other one as well.
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
 * $Id: indilist.php,v 1.14 2010/02/08 21:27:24 wjames5 Exp $
 * @package PhpGedView
 * @subpackage Lists
 */

/**
 * Initialization
 */ 
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'includes/bitsession.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM( 1 );

// We show three different lists:
$alpha   =safe_GET('alpha'); // All surnames beginning with this letter where "@"=unknown and ","=none
$surname =safe_GET('surname', '[^<>&%{};]*'); // All indis with this surname.  NB - allow ' and "
$show_all=safe_GET('show_all', array('no','yes'), 'no'); // All indis

// Don't show the list until we have some filter criteria
if (isset($alpha) || isset($surname) || $show_all=='yes') {
	$showList = true;
} else {
	$showList = false;
}

if (empty($show_all_firstnames)) $show_all_firstnames = "no";

$sublistTrigger = 50;		// Number of names required before list starts sub-listing by first name

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
	$doctitle = "Individual List : ".$alpha;
}
if (isset($surname)) {
	$surname = stripslashes($surname);
	$surname = str_replace(array($lrm, $rlm), "", $surname);
	$doctitle = "Individual List : ";
	if (empty($surname) or trim("@".$surname,"_")=="@" or $surname=="@N.N.") $doctitle .= $pgv_lang["NN"];
	else $doctitle .= $surname;
}

/**
 * Check for the @ symbol
 *
 * This variable is used for checking if the @ symbol is present in the alphabet list.
 * @global boolean $pass
 */
$pass = false;

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
 * @global array $initials
 */

$gBitSmarty->assign( "show_all", $show_all );

// Fetch a list of the initial letters of all surnames in the database
$initials = get_indilist_salpha( $SHOW_MARRIED_NAMES, false, $gGedcom->mGEDCOMId );
// If there are no individuals in the database, do something sensible
if (!$initials) {
	$initials[]='@';
}

if (isset($alpha) && !isset($initials["$alpha"])) unset($alpha);

if (count($initials) > 0) {
	foreach($initials as $letter=>$list) {
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
		}
		if ($letter === "@") $pass = true;
		$ininitials[$letter]['url'] = urlencode($letter);
	}
	if (isset($startalpha)) $alpha = $startalpha;
	$gBitSmarty->assign_by_ref( "indialpha", $initials );
}

$gBitSmarty->assign( "surname_sublist", $surname_sublist );
//-- escaped letter for regular expressions
if ( !isset($alpha) ) $alpha = '';
$expalpha = $alpha;
if ($expalpha=="(" || $expalpha=="[" || $expalpha=="?" || $expalpha=="/" || $expalpha=="*" 
	|| $expalpha=="+" || $expalpha==')') $expalpha = "\\".$expalpha;

if ( empty($SEARCH_SPIDER) && (empty($surname)) ) {
	$gBitSmarty->assign( 'url', "indilist.php?ged=".$GEDCOM."&amp;surname=");
	$listHash = $_REQUEST;
	if ( $show_all == 'no') $listHash['letter'] = $alpha;
	$surname_list = $gGedcom->listSurnames($listHash);

	$gBitSmarty->assign_by_ref( "surnames", $surname_list );
	$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );
}
else {
	$firstname_alpha = false;
	//-- if the surname is set then only get the names in that surname list
	$tindilist = get_indilist_surns( $surname, $alpha, $SHOW_MARRIED_NAMES, false, $gGedcom->mGEDCOMId );
	//-- simplify processing for ALL indilist
	if (($surname_sublist=="no")&&($show_all=="yes")) {
		$names = array();
		foreach($tindilist as $gid => $indi) {
			//-- make sure that favorites from other gedcoms are not shown
			if ($indi["gedfile"]==$gGedcom->mGEDCOMId) {
	            foreach($indi["names"] as $indexval => $namearray) {
		            if ($SHOW_MARRIED_NAMES || $namearray[3]!='C') {
			            $names[] = array($namearray[0], $namearray[1], $namearray[2], $namearray[3], $gid);
		            }
	            }
			}
		}
		uasort($names, "itemsort");
		reset($names);
	}
	else {
		//--- the list is really long so divide it up again by the first letter of the first name
		if (($firstname_alpha)&&(empty($SEARCH_SPIDER))) {
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
/*			print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
			print PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["indis_with_surname"]));
			print "</td></tr><tr>\n";
			print "<td style=\"text-align:center;\" colspan=\"2\">";
			print $pgv_lang["first_letter_fname"]."<br />\n";
			foreach($firstalpha as $letter=>$list) {
				$PASS = false;
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
				if (isset($falpha) && $falpha == "@") print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=".$surname_sublist."\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
				else print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=".$surname_sublist."\">".PrintReady($pgv_lang["NN"])."</a>";
				print " | \n";
				$pass = false;
			}
			if ($show_all_firstnames=="yes") print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=no\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
			else print "<a href=\"?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=yes\">".$pgv_lang["all"]."</a>\n";
			print_help_link("firstname_alpha_help", "qm");
			if (isset($fstartalpha)) $falpha = $fstartalpha;
			if ($show_all_firstnames=="no") {
				$findilist = array();
				$ids = preg_split("/,/", $firstalpha[$falpha]["ids"]);
				foreach($ids as $indexval => $id) {
					if (isset($indilist[$id])) $findilist[$id] = $indilist[$id];
				}
				$tindilist = $findilist;
			}
			print "</td></tr><tr>\n";
*/
		}
		$names = array();
		foreach ($tindilist as $gid => $indi) {
			foreach( $indi[$surname] as $key => $name) {
				$names[$key]["gid"] = $key;
			}
		}
//		asort($names);

		foreach($names as $key => $value) {
			$person = Person::getInstance($value["gid"]);

			$names["$key"]['name'] = $person->getFullName();
			$names["$key"]['sex'] = $person->sex;
			$names["$key"]['url'] = "individual.php?ged=".$GEDCOM."&amp;pid=".$value["gid"]."#content";
			$names["$key"]['birthdate'] = $person->getBirthDate()->Display();
			$names["$key"]['birthplace'] = $person->getBirthPlace();
			$names["$key"]['deathdate'] = $person->getDeathDate()->Display();
			$names["$key"]['dateurl'] = $person->getBirthDate()->Display();
//			$names["$key"]['placeurl'] = $person->getPlaceUrl($names["$key"]['birthplace']);
			$names["$key"]['noc'] = $person->getNumberOfChildren();
		}
		$gBitSmarty->assign_by_ref( "names", $names );
	}
}

if ($show_all=="yes") unset($alpha);
if (!empty($surname) && $surname_sublist=="yes") $legend = str_replace("#surname#", check_NN($surname), $pgv_lang["indis_with_surname"]);
else if (isset($alpha)) $legend = str_replace("#surname#", $alpha.".", $pgv_lang["indis_with_surname"]);
else $legend = $pgv_lang["individuals"];
if ($show_all_firstnames=="yes") $falpha = "@";
if (isset($falpha) and $falpha!="@") $legend .= " ".$falpha.".";
$gBitSmarty->assign( "legend", $legend );

if (isset($alpha)) {
	$gBitSmarty->assign( "alpha", $alpha );
    if (!isset($doctitle)) $doctitle = "Individual List : ".$alpha;
}
else if (!isset($doctitle)) $doctitle = "Full Individual List";
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSystem->display( 'bitpackage:phpgedview/indilist.tpl', tra( 'Individual selection list' ) );
?>

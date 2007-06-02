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
 * $Id: bit_indilist.php,v 1.1 2007/06/02 12:31:53 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
 */

// Initialization
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
global $SEARCH_SPIDER;
require_once("includes/functions_print_lists.php");
require(PHPGEDVIEW_PKG_PATH."languages/lang.en.php");

if(!(empty($SEARCH_SPIDER))) {
	$surname_sublist = "no";
	}
else {
	if (empty($surname_sublist)) $surname_sublist = "yes";
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
if (!isset($target)) $target = 'IND';

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
 * @global array $indialpha
 */

if (empty($SEARCH_SPIDER))
	$gBitSmarty->assign( "SEARCH_SPIDER", $SEARCH_SPIDER );
$gBitSmarty->assign( "show_all", $show_all );

$indialpha = get_indi_alpha();
//uasort($indialpha, "stringsort");
asort($indialpha);

if (isset($alpha) && !isset($indialpha["$alpha"])) unset($alpha);

if (count($indialpha) > 0) {
	foreach($indialpha as $letter=>$list) {
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
		$indialpha[$letter]['url'] = urlencode($letter);
	}
	if (isset($startalpha)) $alpha = $startalpha;
	$gBitSmarty->assign_by_ref( "indialpha", $indialpha );
}

$gBitSmarty->assign( "surname_sublist", $surname_sublist );
//-- escaped letter for regular expressions
$expalpha = $alpha;
if ($expalpha=="(" || $expalpha=="[" || $expalpha=="?" || $expalpha=="/" || $expalpha=="*" 
	|| $expalpha=="+" || $expalpha==')') $expalpha = "\\".$expalpha;

if ((empty($SEARCH_SPIDER))&&($surname_sublist=="yes")&&($show_all=="yes")) {
	get_indi_list();
	if (!isset($alpha)) $alpha="";
	$surnames = array();
	$indi_hide=array();
	foreach($indilist as $gid=>$indi) {
		//-- make sure that favorites from other gedcoms are not shown
		if ($indi["gedfile"]==$GEDCOMS[$GEDCOM]["id"]) {
			if (displayDetailsById($gid)||showLivingNameById($gid)) {
				foreach($indi["names"] as $indexval => $name) {
					surname_count($name[2]);
				}
			}
			else $indi_hide[$gid."[".$indi["gedfile"]."]"] = 1;
		}
	}
	$i = 0;
//	uasort($surnames, "itemsort");
asort($surnames);
	$n = 0;
	$total = 0;
	foreach($surnames as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		if ($target=="FAM") $url = "famlist.php";	else $url = "bit_indilist.php";
		$url .= "?ged=".$GEDCOM."&amp;surname=".urlencode($surn);
		if (empty($surn) or trim("@".$surn,"_")=="@" or $surn=="@N.N.") $surn = tra('(unknown)');
		$surnames["$key"]['n'] = ++$n;
		$surnames["$key"]['surn'] = $surn;
		$surnames["$key"]['url'] = $url;
		$total += $value["match"];
	}
	$gBitSmarty->assign( "surname_total", $total );
	$gBitSmarty->assign_by_ref( "surnames", $surnames );
}
else if ((empty($SEARCH_SPIDER))&&($surname_sublist=="yes")&&(empty($surname))&&($show_all=="no")) {
	if (!isset($alpha)) $alpha="";
	// get all of the individuals whose last names start with this letter
	$tindilist = get_alpha_indis($alpha);
	$surnames = array();
	$indi_show = array();
	$indi_hide = array();
	foreach($tindilist as $gid=>$indi) {
		if ((displayDetailsByID($gid))||(showLivingNameById($gid))) {
			foreach($indi["names"] as $name) {
                // Make sure we only display true "hits"
				$trueHit = false;
				$firstLetter = get_first_letter(strip_prefix($name[2]));
				if ($alpha==$firstLetter) $trueHit = true;

				if (!$trueHit && $DICTIONARY_SORT[$LANGUAGE]) {
					if (strlen($firstLetter)==2) {
						//-- strip diacritics before checking equality
						if (strlen($firstLetter)==2) {
							$aPos = strpos($UCDiacritWhole, $firstLetter);
							if ($aPos!==false) {
								$aPos = $aPos >> 1;
								$firstLetter = substr($UCDiacritStrip, $aPos, 1);
							} else {
								$aPos = strpos($LCDiacritWhole, $firstLetter);
								if ($aPos!==false) {
									$aPos = $aPos >> 1;
									$firstLetter = substr($LCDiacritStrip, $aPos, 1);
								}
							}
						}
						if ($alpha==$firstLetter) $trueHit = true;
					}
				}
				if ($trueHit) {
					surname_count($name[2]);
					$indi_show[$gid."[".$indi["gedfile"]."]"] = 1;
				}
			}
		} else {
			$indi_hide[$gid."[".$indi["gedfile"]."]"] = 1;
		}
	}

	$i = 0;
//	uasort($surnames, "itemsort");
asort($surnames);
	$n = 0;
	$total = 0;
	foreach($surnames as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		if ($target=="FAM") $url = "famlist.php";	else $url = "bit_indilist.php";
		$url .= "?ged=".$GEDCOM."&amp;surname=".urlencode($surn);
		if (empty($surn) or trim("@".$surn,"_")=="@" or $surn=="@N.N.") $surn = tra('(unknown)');
		$surnames["$key"]['n'] = ++$n;
		$surnames["$key"]['surn'] = $surn;
		$surnames["$key"]['url'] = $url;
		$total += $value["match"];
	}
	$gBitSmarty->assign( "surname_total", $total );
	$gBitSmarty->assign_by_ref( "surnames", $surnames );
}
else {
	$firstname_alpha = false;
	//-- if the surname is set then only get the names in that surname list
	if ((!empty($surname))&&($surname_sublist=="yes")&&(empty($SEARCH_SPIDER))) {
		$surname = trim($surname);
		$tindilist = get_surname_indis($surname);
		//-- split up long surname lists by first letter of first name
		if (count($tindilist)>$sublistTrigger) $firstname_alpha = true;
	}

	if (($surname_sublist=="no")&&(!empty($alpha))&&($show_all=="no")) {
		$tindilist = get_alpha_indis($alpha);
	}

	//-- simplify processing for ALL indilist
	if (($surname_sublist=="no")&&($show_all=="yes")) {
		$tindilist = get_indi_list();
		$names = array();
		foreach($tindilist as $gid => $indi) {
			//-- make sure that favorites from other gedcoms are not shown
			if ($indi["gedfile"]==$GEDCOMS[$GEDCOM]["id"]) {
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
			foreach($indi["names"] as $name) {
            	// Make sure we only display true "hits"
				$trueHit = false;
				if (!empty($surname)) {
					if (strcasecmp(strip_prefix($surname), strip_prefix($name[2]))==0) $trueHit = true;
				} else {
					$firstLetter = get_first_letter(strip_prefix($name[2]));
					if (strcasecmp($alpha, $firstLetter)==0) $trueHit = true;

					if (!$trueHit && $DICTIONARY_SORT[$LANGUAGE]) {
						if (strlen($firstLetter)==2) {
							//-- strip diacritics before checking equality
							$aPos = strpos($UCDiacritWhole, $firstLetter);
							if ($aPos!==false) {
								$aPos = $aPos >> 1;
								$firstLetter = substr($UCDiacritStrip, $aPos, 1);
							} else {
								$aPos = strpos($LCDiacritWhole, $firstLetter);
								if ($aPos!==false) {
									$aPos = $aPos >> 1;
									$firstLetter = substr($LCDiacritStrip, $aPos, 1);
								}
							}
							if (strcasecmp($alpha, $firstLetter)==0) $trueHit = true;
						}
					}
				}
				if ($trueHit && $firstname_alpha && $show_all_firstnames=="no") {
					// Make sure we only display true "hits" on the first name
					$trueHit = false;
					$firstLetter = get_first_letter($name[0]);
					if (strcasecmp($falpha, $firstLetter)==0) $trueHit = true;

					if (!$trueHit && $DICTIONARY_SORT[$LANGUAGE]) {
						if (strlen($firstLetter)==2) {
							//-- strip diacritics before checking equality
							$aPos = strpos($UCDiacritWhole, $firstLetter);
							if ($aPos!==false) {
								$aPos = $aPos >> 1;
								$firstLetter = substr($UCDiacritStrip, $aPos, 1);
							} else {
								$aPos = strpos($LCDiacritWhole, $firstLetter);
								if ($aPos!==false) {
									$aPos = $aPos >> 1;
									$firstLetter = substr($LCDiacritStrip, $aPos, 1);
								}
							}
							if (strcasecmp($falpha, $firstLetter)==0) $trueHit = true;
						}
					}
				}
				if ($trueHit) {
					$thisName = check_NN(sortable_name_from_name($name[0]));
					$names[] = array("gid"=>$gid, "name"=>$thisName);
				}
			}
		}
		// uasort($names, "itemsort");
		asort($names);
		foreach($names as $key => $value) {
			if (!isset($value["name"])) break;
			$person = Person::getInstance($value["gid"]);

			$names["$key"]['sex'] = $person->sex;
			$names["$key"]['birthdate'] = $person->getSortableBirthDate();
			$names["$key"]['birthplace'] = $person->getBirthPlace();
			$names["$key"]['deathdate'] = $person->getSortableDeathDate();
			$names["$key"]['dateurl'] = $person->getDateUrl($person->getBirthDate());
			$names["$key"]['placeurl'] = $person->getPlaceUrl($names["$key"]['birthplace']);
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
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSystem->display( 'bitpackage:phpgedview/indilist.tpl', tra( 'Individual selection list' ) );
?>

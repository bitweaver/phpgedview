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
 * @version $Id: famlist.php,v 1.10 2008/07/07 18:01:13 lsces Exp $
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


if (empty($surname_sublist)) $surname_sublist = "yes";
if (empty($show_all)) $show_all = "no";

// Remove slashes
if (isset($alpha)) {
	$alpha = stripLRMRLM(stripslashes($alpha));
	$doctitle = $pgv_lang["family_list"]." : ".$alpha;
}
if (isset($surname)) {
	$surname = stripLRMRLM(stripslashes($surname));
	$doctitle = $pgv_lang["family_list"]." : ";
	if (empty($surname) or trim("@".$surname,"_")=="@" or $surname=="@N.N.") $doctitle .= $pgv_lang["NN"];
	else $doctitle .= $surname;
}
if (isset($doctitle)) {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
		document.title = '<?php print $doctitle; ?>';
	//-->
	</script>
	<?php
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

uasort($famalpha, "stringsort");

if (empty($surname_sublist))
        $surname_sublist = "yes";

/**
 * In the first half of 2007, Google is only indexing the first 1,000 urls 
 * on a page.  We now produce 4 urls per line, instead of 12.  So, we divide 
 * 1000 by 5 for some breathing room, and adjust to do surname pages if the 
 * alphalist page would exceed that number minus the header urls and alphas.
 * 200 - letters - unknown and all - menu urls 
 * If you have over 200 families in the same surname, some will still not
 * get indexed through here, and will have to be caught by the close relatives
 * on the individual.php, family.php, or the indilist.php page.
 */
if (!(empty($SEARCH_SPIDER))) {
	$googleSplit = 200 - 26 - 2 - 4;
	if (isset($alpha))
       		$show_count = count(get_alpha_fams($alpha));
	else if (isset($surname))
        	$show_count = count(get_surname_fams($surname));
	else
        	$show_count = count(get_fam_list());

        if (($show_count > $googleSplit ) && (empty($surname)))  /* Generate extra surname pages if needed */
                $surname_sublist = "yes";
        else
                $surname_sublist = "no";
}

if (isset($alpha) && !isset($famalpha["$alpha"])) $alpha="";

if (count($famalpha) > 0) {
	if (empty($SEARCH_SPIDER))
		print_help_link("alpha_help", "qm", "alpha_index");
	foreach($famalpha as $letter=>$list) {
		if (empty($alpha)) {
			if (!empty($surname)) {
				if ($USE_RTL_FUNCTIONS && hasRTLText($surname)) $alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,2);
				else $alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,1);
			}
		}
		if ($letter != "@") {
			if (!isset($startalpha) && !isset($alpha)) {
				$startalpha = $letter;
				$alpha = $letter;
			}
			print "<a href=\"famlist.php?alpha=".urlencode($letter)."&amp;surname_sublist=".$surname_sublist."&amp;ged=".$GEDCOM."\">";
			if (($alpha==$letter)&&($show_all=="no")) print "<span class=\"warning\">".$letter."</span>";
			else print $letter;
			print "</a> | \n";
		}
		if ($letter === "@") $pass = true;
	}
	if ($pass == true) {
		if (isset($alpha) && $alpha == "@") print "<a href=\"famlist.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.&amp;ged=".$GEDCOM."\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
		else print "<a href=\"famlist.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.&amp;ged=".$GEDCOM."\">".PrintReady($pgv_lang["NN"])."</a>";
		print " | \n";
		$pass = false;
	}
	if ($show_all=="yes") print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=".$surname_sublist."&amp;ged=".$GEDCOM."\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
	else print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=".$surname_sublist."&amp;ged=".$GEDCOM."\">".$pgv_lang["all"]."</a>\n";
	if (isset($startalpha)) $alpha = $startalpha;
}

print "<br /><br />";

if(empty($SEARCH_SPIDER)) {
	if (isset($alpha) && $alpha != "@") {
		if ($surname_sublist=="yes") {
			print_help_link("skip_sublist_help", "qm", "skip_surnames");
			print "<a href=\"famlist.php?alpha=".$alpha."&amp;surname_sublist=no&amp;show_all=".$show_all."&amp;ged=".$GEDCOM."\">".$pgv_lang["skip_surnames"]."</a>";
		} else {
			print_help_link("skip_sublist_help", "qm", "show_surnames");
			print "<a href=\"famlist.php?alpha=".$alpha."&amp;surname_sublist=yes&amp;show_all=".$show_all."&amp;ged=".$GEDCOM."\">".$pgv_lang["show_surnames"]."</a>";
		}
	}
}

if (empty($SEARCH_SPIDER)) {
	print_help_link("name_list_help", "qm", "name_list");
	print "<br /><br />";
}

print "<table class=\"list_table $TEXT_DIRECTION\"><tr><td class=\"center\">";
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
	uasort($surnames, "itemsort");
	print_surn_table($surnames, "FAM");
	if (count($fam_hide)>0) print "<br /><span class=\"warning\">".$pgv_lang["hidden"].": ".count($fam_hide)."</span>";
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
	uasort($surnames, "itemsort");
	print_surn_table($surnames, "FAM");
	if (count($fam_hide)>0) print "<br /><span class=\"warning\">".$pgv_lang["hidden"].": ".count($fam_hide)."</span>";
}
else {
	$firstname_alpha = false;
	//-- if the surname is set then only get the names in that surname list
	if ((!empty($surname))&&($surname_sublist=="yes")) {
		$surname = trim($surname);
		$tfamlist = get_surname_fams($surname);
		//-- split up long surname lists by first letter of first name
		if ($SUBLIST_TRIGGER_F>0 && count($tfamlist)>$SUBLIST_TRIGGER_F) $firstname_alpha = true;
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
						if (isset($names[0])&&isset($names[1])&&$names[0]==$surname) $firstalpha[$letter] = array("letter"=>$letter, "ids"=>$gid);
					}
					else if ($names[0]==$surname) $firstalpha[$letter]["ids"] .= ",".$gid;
					if (isset($names[2])&&isset($names[3])) {
						$letter = str2upper(get_first_letter(trim($names[3])));
						if (!isset($firstalpha[$letter])) {
							if ($names[2]==$surname) $firstalpha[$letter] = array("letter"=>$letter, "ids"=>$gid);
						}
						else if ($names[2]==$surname) $firstalpha[$letter]["ids"] .= ",".$gid;
// Make sure that the same gid is not already defined for the letter
					}
				}

				uasort($firstalpha, "lettersort");
				//-- put the list in the session so that we don't have to calculate this the next time
				$_SESSION[$surname."_firstalphafams"] = $firstalpha;
			}
			else $firstalpha = $_SESSION[$surname."_firstalphafams"];
			print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
			print PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["fams_with_surname"]));
			print "</td></tr><tr>\n";
			print "<td style=\"text-align:center;\" colspan=\"2\">";
			print $pgv_lang["first_letter_fname"]."<br />\n";
			print_help_link("firstname_f_help", "qm", "firstname_alpha_index");
			foreach($firstalpha as $letter=>$list) {
				$pass = false;
				if ($letter != "@") {
					if (!isset($fstartalpha) && !isset($falpha)) {
						$fstartalpha = $letter;
						$falpha = $letter;
					}
					print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=".urlencode($letter)."&amp;surname_sublist=".$surname_sublist."&amp;ged=".$GEDCOM."\">";
					if (($falpha==$letter)&&($show_all_firstnames=="no")) print "<span class=\"warning\">".$letter."</span>";
					else print $letter;
					print "</a> | \n";
				}
				if ($letter === "@") $pass = true;
			}
			if ($pass == true) {
				if (isset($falpha) && $falpha == "@") print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes&amp;ged=".$GEDCOM."\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
				else print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes&amp;ged=".$GEDCOM."\">".PrintReady($pgv_lang["NN"])."</a>";
				print " | \n";
				$pass = false;
			}
			if ($show_all_firstnames=="yes") print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=no&amp;ged=".$GEDCOM."\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
			else print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=yes&amp;ged=".$GEDCOM."\">".$pgv_lang["all"]."</a>\n";
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
		}
		uasort($tfamlist, "itemsort");
	}
}
print "</td></tr></table>";

if ($show_all=="yes") unset($alpha);
if (!empty($surname) && $surname_sublist=="yes") $legend = str_replace("#surname#", check_NN($surname), $pgv_lang["fams_with_surname"]);
else if (isset($alpha) and $show_all=="no") $legend = str_replace("#surname#", $alpha.".", $pgv_lang["fams_with_surname"]);
else $legend = $pgv_lang["families"];
if ($show_all_firstnames=="yes") $falpha = "@";
if (isset($falpha) and $falpha!="@") $legend .= ", ".$falpha.".";
$legend = PrintReady($legend);

if (!empty($surname) or $surname_sublist=="no") {
//	print_fam_table($tfamlist, $legend);
	$gBitSmarty->assign_by_ref( "names", $tfamlist );
}
if (isset($alpha)) {
	$gBitSmarty->assign( "alpha", $alpha );
    if (!isset($doctitle)) $doctitle = "Family List : ".$alpha;
}
else if (!isset($doctitle)) $doctitle = "Full Family List";
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSystem->display( 'bitpackage:phpgedview/famlist.tpl', tra( 'Family selection list' ) );
?>

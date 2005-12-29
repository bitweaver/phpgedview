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
 * This Page Is Valid XHTML 1.0 Transitional! > 24 August 2005
 *
 * @version $Id: famlist.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
 */

require("config.php");
print_header($pgv_lang["family_list"]);
print "<div class =\"center\">";
print "\n\t<h2>".$pgv_lang["family_list"]."</h2>";
if (empty($surname_sublist)) $surname_sublist = "yes";
if (empty($show_all)) $show_all = "no";

// Remove slashes
if (isset($alpha)) $alpha = stripslashes($alpha);
if (isset($surname)) $surname = stripslashes($surname);
if (empty($show_all_firstnames)) $show_all_firstnames = "no";
if (empty($DEBUG)) $DEBUG = false;

/**
 * Check for the @ symbol
 *
 * This variable is used for checking if the @ symbol is present in the alphabet list.
 * @global boolean $pass
 */
$pass = FALSE;

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

if (isset($alpha) && !isset($famalpha["$alpha"])) unset($alpha);

$TableTitle = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["sfamily"]["small"]."\" border=\"0\" title=\"".$pgv_lang["families"]."\" alt=\"".$pgv_lang["families"]."\" />&nbsp;&nbsp;";

if (count($famalpha) > 0) {
	print_help_link("alpha_help", "qm", "alpha_index");
	foreach($famalpha as $letter=>$list) {
		if (empty($alpha)) {
			if (!empty($surname)) {
				if ($USE_RTL_FUNCTIONS && isRTLText($surname)) $alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,2);
				else $alpha = substr(preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z. ]*/"), array(",",",",""), $surname),0,1);
			}
		}
		if ($letter != "@") {
			if (!isset($startalpha) && !isset($alpha)) {
				$startalpha = $letter;
				$alpha = $letter;
			}
//			print "<a href=\"famlist.php?alpha=".urlencode($letter)."&amp;surname_sublist=$surname_sublist\">";
			print "<a href=\"famlist.php?alpha=".urlencode($letter)."&amp;surname_sublist=yes\">";
			if (($alpha==$letter)&&($show_all=="no")) print "<span class=\"warning\">".$letter."</span>";
			else print $letter;
			print "</a> | \n";
		}
		if ($letter === "@") $pass = TRUE;
	}
	if ($pass == TRUE) {
		if (isset($alpha) && $alpha == "@") print "<a href=\"famlist.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
		else print "<a href=\"famlist.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\">".PrintReady($pgv_lang["NN"])."</a>";
		print " | \n";
		$pass = FALSE;
	}
	if ($show_all=="yes") print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=$surname_sublist\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
	else print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=$surname_sublist\">".$pgv_lang["all"]."</a>\n";
	if (isset($startalpha)) $alpha = $startalpha;
}
print "<br />";
print_help_link("name_list_help", "qm", "name_list");
print "<br /><table class=\"list_table $TEXT_DIRECTION\"><tr>";
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
	$i = 0;
	uasort($surnames, "itemsort");
	$count = count($surnames);
	$col = 1;
	if ($count>36) $col=4;
	else if ($count>18) $col=3;
	else if ($count>6) $col=2;
	$newcol=ceil($count/$col);
	print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"$col\">";
	print $TableTitle;
	print $pgv_lang["surnames"]."</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";
	foreach($surnames as $surname=>$namecount) {
		if (stristr($namecount["name"], "@")) $namelist = check_NN($namecount["name"]);
		else $namelist = $namecount["name"];
		if (begRTLText($namecount["name"])) {
			print "<div class =\"rtl\" dir=\"rtl\">&nbsp;<a href=\"famlist.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">&nbsp;".$namelist . "&rlm; - [".($namecount["match"])."]&rlm;";
		}
		else {
			print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"famlist.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">&nbsp;".$namelist . "&lrm; - [".($namecount["match"])."]&lrm;";
        }
		print "</a></div>\n";
		$i++;
		if ($i==$newcol && $i<$count) {
			print "</td><td class=\"list_value\" style=\"padding: 14px;\">\n";
			$newcol=$i+ceil($count/$col);
		}
	}
	print "</td>\n";
	if ($count>1 || count($fam_hide)>0) {
		print "</tr><tr><td colspan=\"$col\" align=\"center\">&nbsp;";
		if ($count>1) print $pgv_lang["total_fams"]." ".count($famlist)."&nbsp;";
		if ($count>1 && count($fam_hide)>0) print "--&nbsp;";
		if (count($fam_hide)>0) print $pgv_lang["hidden"]." ".count($fam_hide);
		if ($count>1) print "<br />".$pgv_lang["surnames"]." ".$count;
		print "</td>\n";
	}
}
else if (($surname_sublist=="yes")&&(empty($surname))&&($show_all=="no")) {
	if (!isset($alpha)) $alpha="";
	$tfamlist = get_alpha_fams($alpha);
	$surnames = array();
	$fam_hide = array();
	foreach($tfamlist as $gid=>$fam) {
		if ((displayDetailsByID($gid, "FAM"))||(showLivingNameById($gid, "FAM"))) {
			$i=0;
			foreach($fam["surnames"] as $indexval => $name) {
				surname_count(trim($name));
				$i++;
			}
		}
		else $fam_hide[$gid."[".$fam["gedfile"]."]"] = 1;
	}
	$i = 0;
	uasort($surnames, "itemsort");
	$count = count($surnames);
	$count_indi = 0;
	$count_fam = 0;
	$col = 1;
	if ($count>36) $col=4;
	else if ($count>18) $col=3;
	else if ($count>6) $col=2;
	$newcol=ceil($count/$col);
	print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"$col\">";
	print $TableTitle;
	print $pgv_lang["surnames"]."</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";
	foreach($surnames as $surname=>$namecount) {
		if (begRTLText($namecount["name"])) print "<div class =\"rtl\" dir=\"rtl\">&nbsp;<a href=\"famlist.php?alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".$namecount["name"]."&rlm;&nbsp;-&nbsp;[".($namecount["fam"])."]&rlm;";
		else print "<div class =\"ltr\" dir=\"ltr\">&nbsp;<a href=\"famlist.php?alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".$namecount["name"]."&lrm;&nbsp;-&nbsp;[".($namecount["fam"])."]&lrm;";
		print "</a>&nbsp;</div>\n";
		$count_indi += $namecount["match"];
		$count_fam += $namecount["fam"];
		$i++;
		if ($i==$newcol && $i<$count) {
			print "</td><td class=\"list_value\" style=\"padding: 14px;\">\n";
			$newcol=$i+ceil($count/$col);
		}
	}
	print "</td>\n";
	if ($count>1 || count($fam_hide)>0) {
		print "</tr><tr><td colspan=\"$col\" align=\"center\">&nbsp;";
	    if (oneRTLText($alpha) || ($alpha == "@" && begRTLText($pgv_lang["NN"]))) print $pgv_lang["total_indis"]." &lrm;(".str2lower($pgv_lang["surname"])." ".($alpha=="@"?$pgv_lang["NN"]:$alpha).")&lrm;&nbsp;&rlm;&nbsp;".($count_indi)."&rlm;&nbsp;<br />";
		else print $pgv_lang["total_indis"]." &rlm;(".str2lower($pgv_lang["surname"])." ".($alpha=="@"?$pgv_lang["NN"]:$alpha).")&rlm;&nbsp;&lrm;".($count_indi)."&lrm;&nbsp;<br />";
		print $pgv_lang["total_fams"]." ".count($tfamlist)."&nbsp;<br />";
		print $pgv_lang["surnames"]." ".$count."&nbsp;</td>\n";
	}
}
else {
	$firstname_alpha = false;
	//-- if the surname is set then only get the names in that surname list
	if ((!empty($surname))&&($surname_sublist=="yes")) {
		$surname = trim($surname);
		$tfamlist = get_surname_fams($surname);
		//-- split up long surname lists by first letter of first name
		if (count($tfamlist)>500) $firstname_alpha = true;
	}

	if (($surname_sublist=="no")&&(!empty($alpha))&&($show_all=="no")) {
		$tfamlist = get_alpha_fams($alpha);
	}

	//-- simplify processing for ALL famlist
	if (($surname_sublist=="no")&&($show_all=="yes")) {
		$tfamlist = get_fam_list();
		uasort($tfamlist, "itemsort");
		$count = count($tfamlist);
		$i=0;
		print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
		print $TableTitle;
		print $pgv_lang["families"]."</td></tr><tr>\n";
		print "<td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>\n";
		foreach($tfamlist as $gid => $fam) {
			$fam["name"] = check_NN($fam["name"]);
			print_list_family($gid, array($fam["name"], get_gedcom_from_id($fam["gedfile"])));
			$i++;
			if ($i==ceil($count/2) && $count>8) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		}
		print "</ul></td>\n";
		if ($count>1) {
			print "</tr><tr><td colspan=\"2\" align=\"center\">";
			print $pgv_lang["total_fams"]." ".$count."</td>\n";
		}
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
			print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
			print $TableTitle;
			print PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["fams_with_surname"]));
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
					print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=".urlencode($letter)."&amp;surname_sublist=$surname_sublist\">";
					if (($falpha==$letter)&&($show_all_firstnames=="no")) print "<span class=\"warning\">".$letter."</span>";
					else print $letter;
					print "</a> | \n";
				}
				if ($letter === "@") $pass = TRUE;
			}
			if ($pass == TRUE) {
				if (isset($falpha) && $falpha == "@") print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes\"><span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span></a>";
				else print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;falpha=@&amp;surname_sublist=yes\">".PrintReady($pgv_lang["NN"])."</a>";
				print " | \n";
				$pass = FALSE;
			}
			print_help_link("firstname_alpha_help", "qm", "firstname_alpha_index");
			if ($show_all_firstnames=="yes") print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=no\"><span class=\"warning\">".$pgv_lang["all"]."</span>\n";
			else print "<a href=\"famlist.php?alpha=".urlencode($alpha)."&amp;surname=".urlencode($surname)."&amp;show_all_firstnames=yes\">".$pgv_lang["all"]."</a>\n";
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
		$count = count($tfamlist);
		$i=0;
		if ($firstname_alpha==false) {
			print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
			print $TableTitle;
			if (!empty($surname) && $surname_sublist=="yes") print PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["fams_with_surname"]));
			else print $pgv_lang["families"];
			print "</td></tr><tr>\n";
		}
		print "<td class=\"list_value_wrap\"><ul>\n";
		foreach($tfamlist as $gid => $fam) {
			$fam["name"] = check_NN($fam["name"]);
			print_list_family($gid, array($fam["name"], get_gedcom_from_id($fam["gedfile"])));
			$i++;
			if ($i==ceil($count/2) && $count>8) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		}
		print "</ul></td>\n";
		if ($count>1) {
			print "</tr><tr><td colspan=\"2\" align=\"center\">";
			print $pgv_lang["total_fams"]." ".$count;
			if (count($fam_private)>0) print "&nbsp;(".$pgv_lang["private"]." ".count($fam_private).")";
			if (count($fam_hide)>0) print "&nbsp;--&nbsp;";
			if (count($fam_hide)>0) print $pgv_lang["hidden"]." ".count($fam_hide);
			print "</td>\n";
		}
	}
}
print "</tr></table>";

print "<br />";
if ($alpha != "@") {
	if ($surname_sublist=="yes") print_help_link("skip_sublist_help", "qm", "skip_surnames");
	else print_help_link("skip_sublist_help", "qm", "show_surnames");
}
if ($show_all=="yes" && $alpha != "@"){
	if ($surname_sublist=="yes") print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
 	else print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if (empty($alpha)) {
	if ($surname_sublist=="yes") print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<a href=\"famlist.php?show_all=yes&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>\n";
}
else if ($alpha != "@" && is_array(isset($surname))) {
	print "<a href=\"famlist.php?alpha=$alpha&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if ($alpha != "@") {
	if ($surname_sublist=="yes") print "<a href=\"famlist.php?alpha=$alpha&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<a href=\"famlist.php?alpha=$alpha&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}

print "<br /><br />\n";
print "</div>\n";
print_footer();
?>
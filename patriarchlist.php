<?php
/**
 * Patriarch List
 *
 * The individual list shows all individuals from a chosen gedcom file. The list is
 * setup in two sections. The alphabet bar and the details.
 *
 * The alphabet bar shows all the available letters users can click. The bar is build
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
 * to be done;
 * - just run first part only if neccessary (file changes)
 * - put info on patriarch in SQL file
 * - add helpfile
 * - put different routines in subroutine file
 *
 * Parses gedcom file and displays a list of 'earthfathers' == patriarch.
 * This program was made in analogy of the family.php program
 * ==	You probably do not have to check the whole list but just select on -no- spouse in the list
 * ==	You still have to deal with the 'singles' and mother with children
 * ==	lOOKS LIKE: SELECT * FROM `pgv_individuals` WHERE i_file='$GEDCOM' and i_cfams IS NULL ORDER BY `i_cfams` ASC, 'i_sfams' ASC
 * ==	The IS NULL check does not work??? Set to another value?
 *
 * This Page Is Valid XHTML 1.0 Transitional! > 24 August 2005
 *
 * @version $Id: patriarchlist.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
 */

require("config.php");
require_once("includes/functions_print_lists.php");
$patrilist = array();
$patrialpha = array();


function indi2roots() {
	//--global $TBLPREFIX, $GEDCOM;
	global $ct,$patrilist,$patrialpha;

	$my2indilist= array();
	$keys= array();
	$orignames= array();
	$person = array();
	
	//--print "<br>start indi2:" . date("m/d/y G.i:s",time()) . "<br>\n";

	$my2indilist = get_indi_list();
	$ct = count($my2indilist);

	//--	print "<br>tijd na getindi:" . date("m/d/y G.i:s",time()) . "<br>\n";

	//--	first select the names then do the alphabetic sort
	$orignum=0;
	$i=0;
	$keys = array_keys($my2indilist);
	//--key is I<nr>

	while ($i < $ct) {
		$key=$keys[$i];
		$value= $my2indilist[$key]["names"][0][0];
		$value2= $my2indilist[$key]["gedfile"];
		$person= find_person_record($key);
		$famc="";
		$ctc= preg_match("/1\s*FAMC\s*@(.*)@/",$person,$match);
		if ($ctc > 0) {
			$famc= $match[1];
			$parents= find_parents($famc);
			if (($parents["WIFE"] == "") and ($parents["HUSB"] == "")) $famc= "";
		}
		
		//-- we assume that when there is a famc record, this person is not a patriarch
		//-- in special cases it is possible that a child is a member of a famc record but no parents are given
		//-- and so they are the patriarch's

		//-- first spouse record. assuming a person has just one father and one mother.
		if ($famc == "") {
			//--print "select:$orignum,$key,$value,$person<br>";
			$orignum ++;
			$orignames["$key"]["name"]=$value;
		 	$orignames["$key"]["gedfile"]=$value2;
		}
		$i++;
	}
	$ct= $orignum;
	//--print "totaal aantal=,$ct<br>";
	//--	print "<br>indi2 na select:" . date("m/d/y G.i:s",time()) . "<br>\n";
	$patrilist=$orignames;
	uasort($patrilist, "itemsort");

	//--end search
	//--print "<br>indi2 na sort:" . date("m/d/y G.i:s",time()) . "<br>\n";

	$i=0;
	$keys = array_keys($patrilist);
	//--key is I<nr>

	$oldletter= "";
	while ($i < $ct) {
		$key=$keys[$i];
//		$value= trim($patrilist[$key]["name"]);
		$value = get_sortable_name($key);
		$value2= $patrilist[$key]["gedfile"];
		$person= find_person_record($key);
//--> Changed MA @@@ as in extract_surname() etc.
		$tmpnames = preg_split("/,/", $value);
		$tmpnames[0] = preg_replace(array("/ [jJsS][rR]\.?,/", "/ I+,/", "/^[a-z\. ]*/"), array(",",",",""), $tmpnames[0]);
		$tmpnames[0] = trim($tmpnames[0]);
		//-- check for all lowercase name and start over
		if (empty($tmpnames[0])) {
			$tmpnames = preg_split("/,/", $value);
			$tmpnames[0] = trim($tmpnames[0]);
		}
		$tmpletter = get_first_letter($tmpnames[0]);
		if ($tmpletter!=$oldletter) $oldletter=$tmpletter;
		if ((!isset($alpha)) || ($alpha = $tmpletter)) {
			$orignames["$key"]["name"]=$value;
		 	$orignames["$key"]["gedfile"]=$value2;
			$letter=$tmpletter;
//<---- MA @@@
			if (!isset($patrialpha[$letter])) {
	 			$patrialpha[$letter]["letter"]= "$letter";
	 			$patrialpha[$letter]["gid"]= "$key";
			}
			else $patrialpha[$letter]["gid"].= ",$key";
			//-- print " reeks: $letter,$key,==" . $patrialpha[$letter]["gid"] . "==<br>";
			//-- print "select:$i,$letter,$key,$value,$person<br>";
		}
		$i++;
	}
	$patrilist=$orignames;
	//--print "<br>indi2 na alpha:" . date("m/d/y G.i:s",time()) . "<br>\n";
}
// end indi2roots


function put_patri_list() {
	//-- save the items in the database
	global $ct,$patrilist,$patrialpha;
	//-- print "start roots2database<br />";
	global $GEDCOM,$INDEX_DIRECTORY, $FP, $pgv_lang;

	$indexfile = $INDEX_DIRECTORY.$GEDCOM."_patriarch.php";
	$FP = fopen($indexfile, "wb");
	if (!$FP) {
		print "<font class=\"error\">".$pgv_lang["unable_to_create_index"]."</font>";
		exit;
	}

	fwrite($FP, 'a:1:{s:13:"patrilist";');
	fwrite($FP, serialize($patrilist));
	fwrite($FP, '}');
	fclose($FP);
}

//-- find all of the individuals who start with the given letter within patriarchlist
function get_alpha_patri($letter) {
	global $patrialpha, $patrilist;

	$tpatrilist = array();

	$list = $patrialpha[$letter]["gid"];
	$gids = preg_split("/[+,]/", $list);
	foreach($gids as $indexval => $gid)	{
//-- print "regel201:letter,gid,list,gids: " . $letter . ":" . $gid . ":" . $patrilist[$gid] . ":" . $gids[0] . ":<br>";
		$tpatrilist[$gid] = $patrilist[$gid];
	}
	return $tpatrilist;
}

//-- get the patriarchlist from the datastore
function get_patri_list() {
	global $patrilist;

	return $patrilist;
}


print_header($pgv_lang["dynasty_list"]);


print "<div class =\"center\">";
print "\n\t<h2>".$pgv_lang["dynasty_list"]."</h2>";
indi2roots();
put_patri_list();
if (empty($surname_sublist)) $surname_sublist = "yes";
if (empty($show_all)) $show_all = "no";

// Remove slashes
if (isset($alpha)) $alpha = stripslashes($alpha);
if (isset($surname)) $surname = stripslashes($surname);

$pass = FALSE;
$tpatrilist = array();

//-- name in $patriarchalpha for sorting??? MA @@@ =====>

uasort($patrialpha, "lettersort");

print_help_link("alpha_help", "qm", "alpha_index");
foreach($patrialpha as $letter=>$list) {
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
		print "<a href=\"patriarchlist.php?alpha=".urlencode($letter)."&amp;surname_sublist=no\">";
		if (($alpha==$letter)&&($show_all=="no")) print "<span class=\"warning\">".$letter."</span>";
		else print $letter;
		print "</a> | \n";
	}
	if ($letter === "@") $pass = TRUE;
}
if ($pass == TRUE) {
	if ($alpha == "@") print "<span class=\"warning\">".PrintReady($pgv_lang["NN"])."</span>";
	else print "<a href=\"patriarchlist.php?alpha=@&amp;surname_sublist=yes&amp;surname=@N.N.\">".PrintReady($pgv_lang["NN"])."</a>";
	print " | \n";
	$pass = FALSE;
}
print "<a href=\"patriarchlist.php?show_all=yes&amp;surname_sublist=$surname_sublist\">";
if ($show_all=="yes") print "<span class=\"warning\">";
print_text("all"); 
if ($show_all=="yes") print "</span>";
print "</a>\n";
if (isset($startalpha)) $alpha = $startalpha;

$expalpha = $alpha;
if ($expalpha=="(") $expalpha = '\(';
if ($expalpha=="[") $expalpha = '\[';
if ($expalpha=="?") $expalpha = '\?';
if ($expalpha=="/") $expalpha = '\/';
print "<br />";
print_help_link("name_list_help", "qm", "name_list");
print "<br /><br /><table class=\"list_table $TEXT_DIRECTION\"><tr>";

$TableTitle = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border=\"0\" title=\"".$pgv_lang["individuals"]."\" alt=\"".$pgv_lang["individuals"]."\" />&nbsp;&nbsp;";

if (($surname_sublist=="yes")&&($show_all=="yes")) {
	$tpatrilist = get_patri_list();
	if (!isset($alpha)) $alpha="";
	// Start printing names
	$surnames = array();
	$indi_hide=array();
	foreach($tpatrilist as $gid=>$fam) {
	//-- make sure that favorites from other gedcoms are not shown
    	if ($fam["gedfile"]==$GEDCOMS[$GEDCOM]["id"]) {
			// Added space to regexp after z to also remove prefixes

			if (displayDetailsById($gid)||showLivingNameById($gid)) {          //-- MA @@@@
				extract_surname($fam["name"]);                                 //-- MA @@@@
			}
			else $indi_hide[$gid."[".$fam["gedfile"]."]"] = 1;
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
	print $pgv_lang["surnames"]."</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";

	foreach($surnames as $surname=>$namecount) {
		if (begRTLText($namecount["name"])) {
			print "<div class =\"rtl\"><a href=\"patriarchlist.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=".$namecount["name"]."\">".$namecount["name"]." - &rlm;[".($namecount["match"])."]&rlm;";
		}
		else if (substr(trim($namecount["name"]), 0, 4) == "@N.N") {
			print "<div class =\"ltr\"><a href=\"patriarchlist.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=@N.N.\">".$pgv_lang["NN"]." - &lrm;[".($namecount["match"])."]&lrm;";
		}
		else print "<div class =\"ltr\"><a href=\"patriarchlist.php?alpha=".$namecount["alpha"]."&amp;surname_sublist=$surname_sublist&amp;surname=".$namecount["name"]."\">".$namecount["name"]." - &lrm;[".($namecount["match"])."]&lrm;";
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
		if ($count>1) print $pgv_lang["total_indis"]." ".$count_indi."&nbsp;";
		if ($count>1 && count($indi_hide)>0) print "--&nbsp;";
		if (count($indi_hide)>0) print $pgv_lang["hidden"]." ".count($indi_hide);
		if ($count>1) print "<br />".$pgv_lang["surnames"]." ".$count;
		print "</td>\n";
	}	
}
else if (($surname_sublist=="yes")&&(empty($surname))&&($show_all=="no")) {
	if (!isset($alpha)) $alpha="";
	$tpatrilist = get_alpha_patri($alpha);
	$surnames = array();
	$indi_hide=array();
	foreach($tpatrilist as $gid=>$fam) {
	//-- make sure that favorites from other gedcoms are not shown
        if ($fam["gedfile"]==$GEDCOMS[$GEDCOM]["id"]) {
			if (displayDetailsById($gid)||showLivingNameById($gid)) {          //-- MA @@@@
				extract_surname($fam["name"]);                                 //-- MA @@@@
			}
			else $indi_hide[$gid."[".$fam["gedfile"]."]"] = 1;
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
	print $pgv_lang["surnames"]."</td></tr><tr>\n";
	print "<td class=\"list_value\" style=\"padding: 14px;\">\n";

	foreach($surnames as $surname=>$namecount) {
		if (begRTLText($namecount["name"])) {
			print "<div class =\"rtl\">&nbsp;<a href=\"patriarchlist.php?alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".urlencode($namecount["name"])."\">".$namecount["name"]." - &rlm;[".($namecount["match"])."]&rlm;";
		}
		else print "<div class =\"ltr\">&nbsp;<a href=\"patriarchlist.php?alpha=".$alpha."&amp;surname_sublist=$surname_sublist&amp;surname=".$namecount["name"]."\">".$namecount["name"]." - &lrm;[".($namecount["match"])."]&lrm;";
		print "</a>&nbsp;</div>\n";
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
		if ($count>1) print $pgv_lang["total_indis"]." ".$count_indi."&nbsp;";
		if ($count>1 && count($indi_hide)>0) print "--&nbsp;";
		if (count($indi_hide)>0) print $pgv_lang["hidden"]." ".count($indi_hide);
		if ($count>1) print "<br />".$pgv_lang["surnames"]." ".$count;
		print "</td>\n";
	}
}
else {
	$firstname_alpha = false;
	//-- if the surname is set then only get the names in that surname list
	//echo "<br> 3"; //-- MA @@@
	if ((!empty($surname))&&($surname_sublist=="yes")) {
		$tpatrilist = get_patri_list();
		$npatrilist = array();
		foreach($tpatrilist as $gid=>$fam) {
			if (stristr($fam["name"],$surname)) $npatrilist[$gid] = $fam;
		}
		$tpatrilist = $npatrilist;
	}
	else if (($surname_sublist=="no")&&($show_all=="yes")) $tpatrilist = get_patri_list();
	else $tpatrilist = get_alpha_patri($alpha);

//		$ct = count($tpatrilist);
//echo "<br> 4"; //-- MA @@@

	$i=0;
	$surnames = array();
	foreach($tpatrilist as $gid=>$fam) {
		//-- make sure that favorites from other gedcoms are not shown
		if ($fam["gedfile"]==$GEDCOMS[$GEDCOM]["id"]) {
			if (!empty($names[1])) $firstname = trim($names[1]);
			else $firstname = "";
			$names = preg_split("/[,+]/", $fam["name"]);
			$surname = str2upper(trim($names[$i]));
			if (empty($surname)) $surname = $gid;
			$surnames[$surname.$firstname.$gid]["name"] = check_NN($fam["name"]);
			$surnames[$surname.$firstname.$gid]["gid"] = $gid;
//--regel 467
			$surnames[$surname.$firstname.$gid]["gedfile"] = $fam["gedfile"];
		}
	}
	if (!empty($surnames)) {
		uasort($surnames, "itemsort");
		$count = count($surnames);
		print "<td class=\"list_label\" style=\"padding: 0pt 5pt 0pt 5pt; \" colspan=\"2\">";
		print $TableTitle;
		if (!empty($surname) && $surname_sublist=="yes") print PrintReady(str_replace("#surname#", check_NN($surname), $pgv_lang["indis_with_surname"]));
		else print $pgv_lang["individuals"];
		print "</td></tr><tr>\n";
		print "<td class=\"list_value\"><ul>\n";
		foreach($surnames as $k => $surname) {
			print_list_person($surname["gid"], array($surname["name"], get_gedcom_from_id($surname["gedfile"])));
			$i++;
			if ($i==floor($count/2)) print "</ul></td><td class=\"list_value\"><ul>\n";
		}
		print "</ul></td>\n";

		if ($count>1) {
			print "</tr><tr><td colspan=\"2\" align=\"center\">";
			print $pgv_lang["total_indis"]." ".$count;
			if (count($indi_private)>0) print "&nbsp;(".$pgv_lang["private"]." ".count($indi_private).")";
			if (count($indi_hide)>0) print "&nbsp;--&nbsp;";
			if (count($indi_hide)>0) print $pgv_lang["hidden"]." ".count($indi_hide);
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
	if ($surname_sublist=="yes") print "<a href=\"patriarchlist.php?show_all=yes&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
 	else print "<a href=\"patriarchlist.php?show_all=yes&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if ((!isset($alpha)) || ($alpha=="" && $alpha != "@")) {
	if ($surname_sublist=="yes") print "<a href=\"patriarchlist.php?show_all=yes&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<a href=\"patriarchlist.php?show_all=yes&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if ($alpha != "@" && is_array(isset($surname))) {
	print "<a href=\"patriarchlist.php?alpha=$alpha&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
else if ($alpha != "@"){
	if ($surname_sublist=="yes") print "<a href=\"patriarchlist.php?alpha=$alpha&amp;surname_sublist=no\">".$pgv_lang["skip_surnames"]."</a>";
	else print "<a href=\"patriarchlist.php?alpha=$alpha&amp;surname_sublist=yes\">".$pgv_lang["show_surnames"]."</a>";
}
print "<br />\n";
print "</div>\n";
print_footer();
?>
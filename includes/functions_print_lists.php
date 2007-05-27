<?php
/**
 * Functions for printing lists
 *
 * Various printing functions for printing lists
 * used on the indilist, famlist, find, and search pages.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * @subpackage Display
 * @version $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once("includes/person_class.php");

/**
 * print a person in a list
 *
 * This function will print a
 * clickable link to the individual.php
 * page with the person's name
 * lastname, firstname and their
 * birthplace and date
 * @author John Finlay
 * @param string $key the GEDCOM xref id of the person to print
 * @param array $value is an array of the form array($name, $GEDCOM)
 */
function print_list_person($key, $value, $findid=false, $asso="", $useli=true) {
	global $pgv_lang, $SCRIPT_NAME, $pass, $indi_private, $indi_hide, $indi_total, $factarray;
	global $GEDCOM, $SHOW_ID_NUMBERS, $TEXT_DIRECTION, $SHOW_PEDIGREE_PLACES, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_DEATH_LISTS;

	if ($value[1]>=1) $value[1] = get_gedcom_from_id($value[1]);
	$GEDCOM = $value[1];
	if (!isset($indi_private)) $indi_private=array();
	if (!isset($indi_hide)) $indi_hide=array();
	if (!isset($indi_total)) $indi_total=array();
	$indi_total[$key."[".$GEDCOM."]"] = 1;

	$person = Person::getInstance($key);
	$disp = $person->canDisplayDetails();
	if ($person->canDisplayName()) {
		if (begRTLText($value[0])) $listDir = "rtl";
		else $listDir = "ltr";
		$tag = "span";
		if ($useli) $tag = "li";
		print "<".$tag." class=\"".$listDir."\" dir=\"".$listDir."\">";
		if ($findid == true) print "<a href=\"javascript:;\" onclick=\"pasteid('".$key."', '".preg_replace("/(['\"])/", "\\$1", PrintReady($value[0]." - ".$person->getBirthYear()))."'); return false;\" class=\"list_item\"><b>".$value[0]."</b>";
		else print "<a href=\"individual.php?pid=$key&amp;ged=$value[1]\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		if ($SHOW_ID_NUMBERS){
			print "&nbsp;&nbsp;";
			if ($listDir=="rtl") print "&rlm;";
			print "(".$key.")";
			if ($listDir=="rtl") print "&rlm;";
			print "&nbsp;&nbsp;";
		}

		if (!$disp) {
			print "<br /><i>".$pgv_lang["private"]."</i>";
			$indi_private[$key."[".$GEDCOM."]"] = 1;
		}
		else {
			print_first_major_fact($key, array("BIRT", "CHR", "BAPM", "BAPL", "ADOP"));
			print_first_major_fact($key, array("DEAT", "BURI"));
			/**
			$fact = print_first_major_fact($key);
			if (isset($SHOW_DEATH_LISTS) && $SHOW_DEATH_LISTS==true) {
				if ($fact!="DEAT") {
					$indirec = find_person_record($key);
					$factrec = get_sub_record(1, "1 DEAT", $indirec);
					if (strlen($factrec)>7 && showFact("DEAT", $key) && !FactViewRestricted($key, $factrec)) {
						print " -- <i>";
						print $factarray["DEAT"];
						print " ";
						print_fact_date($factrec);
						print_fact_place($factrec);
						print "</i>";
					}
				}
			}
			**/
		}
		print "</a>";
		if (($asso != "") && ($disp)) {
			$p1 = strpos($asso,"[");
			$p2 = strpos($asso,"]");
			$ged = substr($asso,$p1+1,$p2-$p1-1);
			if ($ged>=1) $ged = get_gedcom_from_id($ged);
			$key = substr($asso,0,$p1);
			$oldged = $GEDCOM;
			$GEDCOM = $ged;
			$name = get_person_name($key);
			$GEDCOM = $oldged;
			print " <a href=\"individual.php?pid=$key&amp;ged=$ged\" title=\"$name\" class=\"list_item\">";
			print "&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="ltr") print "(".$pgv_lang["associate"]."&nbsp;&nbsp;".$key.")";
  			else print "&rlm;(&rlm;".$pgv_lang["associate"]."&nbsp;&nbsp;".$key."&rlm;)&rlm;</span></a>";
		}
//		if ($useli) print "</li>";
		print "</".$tag.">";
	}
	else {
		$pass = TRUE;
		$indi_hide[$key."[".$GEDCOM."]"] = 1;
	}
}

/**
 * print a family in a list
 *
 * This function will print a
 * clickable link to the family.php
 * @param string $key the GEDCOM xref id of the person to print
 * @param array $value is an array of the form array($name, $GEDCOM)
 */
function print_list_family($key, $value, $findid=false, $asso="", $useli=true) {
	global $pgv_lang, $pass, $fam_private, $fam_hide, $fam_total, $SHOW_ID_NUMBERS;
	global $GEDCOM, $HIDE_LIVE_PEOPLE, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION;
	$GEDCOM = $value[1];
	if (!isset($fam_private)) $fam_private=array();
	if (!isset($fam_hide)) $fam_hide=array();
	if (!isset($fam_total)) $fam_total=array();
	$fam_total[$key."[".$GEDCOM."]"] = 1;
	$famrec=find_family_record($key);
	$display = displayDetailsByID($key, "FAM");
	$showLivingHusb=true;
	$showLivingWife=true;
	$parents = find_parents($key);
	//-- check if we can display both parents
	if (!$display) {
		$showLivingHusb=showLivingNameByID($parents["HUSB"]);
		$showLivingWife=showLivingNameByID($parents["WIFE"]);
	}
	if ($showLivingWife && $showLivingHusb) {
		if (begRTLText($value[0])) $listDir = "rtl";
		else $listDir = "ltr";
		if ($useli) $tag = "li";
		else $tag = "span";
		print "<".$tag." class=\"".$listDir."\" dir=\"".$listDir."\">";
		if ($findid == true) print "<a href=\"javascript:;\" onclick=\"pasteid('".$key."'); return false;\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		else print "<a href=\"family.php?famid=$key&amp;ged=$value[1]\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		if ($SHOW_ID_NUMBERS) {
			print "&nbsp;&nbsp;";
			if ($listDir=="rtl") print "&rlm;";
			print "(".$key.")";
			if ($listDir=="rtl") print "&rlm;";
			print "&nbsp;&nbsp;";
		}
		if (!$display) {
			print "<br /><i>".$pgv_lang["private"]."</i>";
			$fam_private[$key."[".$GEDCOM."]"] = 1;
		}
		else {
			print_first_major_fact($key, array("MARR"));
			print_first_major_fact($key, array("DIV"));
		}
		print "</a>";
		if ($asso != "") {
			$p1 = strpos($asso,"[");
			$p2 = strpos($asso,"]");
			$ged = substr($asso,$p1+1,$p2-$p1-1);
			$indikey = substr($asso,0,$p1);
			$oldged = $GEDCOM;
			$GEDCOM = $ged;
			$name = get_person_name($key);
			$GEDCOM = $oldged;
			print " <a href=\"individual.php?pid=$indikey&amp;ged=$ged\" title=\"$name\" class=\"list_item\">";
			print "&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="ltr") print "(".$pgv_lang["associate"]."&nbsp;&nbsp;".$indikey.")</a>";
  			else print "&rlm;(&rlm;".$pgv_lang["associate"]." &nbsp;&nbsp;".$indikey."&rlm;)&rlm;</span></a>";
		}
		print "</".$tag.">";
	}															//begin re-added by pluntke
	if (!$showLivingWife || !$showLivingHusb) {				   	//fixed THIS line (changed && to ||)
		$pass = TRUE;
		$fam_hide[$key."[".$GEDCOM."]"] = 1;
	}															//end re-added by pluntke
}

/**
 * print a source in a list
 *
 * This function will print a
 * clickable link to the source.php
 * page with the source's name
 * @param string $key the GEDCOM xref id of the person to print
 * @param array $value is an array of the form array($name, $GEDCOM)
 */
function print_list_source($key, $value, $useli=true) {
	global $source_total, $source_hide, $SHOW_SOURCES, $SHOW_ID_NUMBERS, $GEDCOM, $TEXT_DIRECTION;

	$GEDCOM = get_gedcom_from_id($value["gedfile"]);
	if (!isset($source_total)) $source_total=array();
	$source_total[$key."[".$GEDCOM."]"] = 1;
	if (displayDetailsByID($key, "SOUR")) {
		if (begRTLText($value["name"])) $listDir = "rtl";
		else $listDir = "ltr";
		if ($useli) $tag = "li";
		else $tag = "span";
		print "\n\t\t\t<".$tag." class=\"".$listDir."\" dir=\"".$listDir."\">";
		print "\n\t\t\t<a href=\"source.php?sid=$key&amp;ged=".get_gedcom_from_id($value["gedfile"])."\" class=\"list_item\"><b>".PrintReady($value["name"])."</b>";
		if ($SHOW_ID_NUMBERS) {
			print "&nbsp;&nbsp;";
			if ($listDir=="rtl") print "&rlm;(".$key.")&rlm;";
			else print "&lrm;(".$key.")&lrm;";
		}
		print "</a>\n";
		print "</".$tag.">\n";
	}
	else $source_hide[$key."[".$GEDCOM."]"] = 1;
}

/**
 * print a repository in a list
 *
 * This function will print a
 * clickable link to the repo.php
 * @param string $key the GEDCOM xref id of the person to print
 * @param array $value is an array of the form array($name, $GEDCOM)
 */
function print_list_repository($key, $value, $useli=true) {
	global $repo_total, $repo_hide, $SHOW_ID_NUMBERS, $GEDCOM, $TEXT_DIRECTION;

	$GEDCOM = get_gedcom_from_id($value["gedfile"]);
	if (!isset($repo_total)) $repo_total=array();
	$repo_total[$key."[".$GEDCOM."]"] = 1;
	if (displayDetailsByID($key, "REPO")) {
		if (begRTLText($value["name"])) $listDir = "rtl";
		else $listDir = "ltr";
		if ($useli) $tag = "li";
		else $tag = "span";
		print "\n\t\t\t<".$tag." class=\"".$listDir."\" dir=\"".$listDir."\">";
		$id = $value["id"];
		print "<a href=\"repo.php?rid=$id\" class=\"list_item\">";
		print PrintReady($value["name"]);
		if ($SHOW_ID_NUMBERS) {
			print "&nbsp;&nbsp;";
			if ($listDir=="rtl") print "&rlm;(".$id.")&rlm;";
			else print "&lrm;(".$id.")&lrm;";
		}
		print "</a></".$tag.">\n";
	}
	else $repo_hide[$key."[".$GEDCOM."]"] = 1;
}

/**
 * print a sortable table of individuals
 *
 * @param array $datalist contain individuals that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_indi_table($datalist, $legend="", $option="") {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $SHOW_MARRIED_NAMES, $TEXT_DIRECTION, $GEDCOM_ID_PREFIX, $GEDCOM;
	if (count($datalist)<1) return;
	$name_subtags = array("", "_HEB", "ROMN", "_AKA");
	if ($SHOW_MARRIED_NAMES) $name_subtags[] = "_MARNM";
	require_once("js/sorttable.js.htm");
	require_once("includes/person_class.php");
	if (empty($legend)) $legend=$pgv_lang["individuals"];
	echo "<fieldset><legend>".$legend."</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- filter buttons
	$person = new Person("");
	echo "<button type=\"button\" class=\"SEX_M\" title=\"".$pgv_lang["button_SEX_M"]."\" >";
	$person->sex = "M"; echo $person->getSexImage()."&nbsp;</button> ";
	echo "<button type=\"button\" class=\"SEX_F\" title=\"".$pgv_lang["button_SEX_F"]."\" >";
	$person->sex = "F"; echo $person->getSexImage()."&nbsp;</button> ";
	echo "<button type=\"button\" class=\"SEX_U\" title=\"".$pgv_lang["button_SEX_U"]."\" >";
	$person->sex = "U"; echo $person->getSexImage()."&nbsp;</button> ";
	echo " <input type=\"text\" size=\"4\" id=\"aliveyear\" value=\"".date('Y')."\" /> ";
	echo "<button type=\"button\" class=\"alive_in_year\" title=\"".$pgv_lang["button_alive_in_year"]."\" >";
	echo $pgv_lang["alive_in_year"]."</button> ";
	echo "<button type=\"button\" class=\"DEAT_N\" title=\"".$pgv_lang["button_DEAT_N"]."\" >";
	echo $pgv_lang["alive"]."</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y\" title=\"".$pgv_lang["button_DEAT_Y"]."\" >";
	echo $pgv_lang["dead"]."</button> ";
	echo "<button type=\"button\" class=\"TREE_R\" title=\"".$pgv_lang["button_TREE_R"]."\" >";
	echo $pgv_lang["roots"]."</button> ";
	echo "<button type=\"button\" class=\"TREE_L\" title=\"".$pgv_lang["button_TREE_L"]."\" >";
	echo $pgv_lang["leaves"]."</button> ";
	echo "<br />";
	$y100 = get_changed_date(date('Y')-100);
	echo "<button type=\"button\" class=\"BIRT_YES\" title=\"".$pgv_lang["button_BIRT_YES"]."\" >";
	echo $factarray["BIRT"]."&gt;100</button> ";
	echo "<button type=\"button\" class=\"BIRT_Y100\" title=\"".$pgv_lang["button_BIRT_Y100"]."\" >";
	echo $factarray["BIRT"]."&lt;=100</button> ";
	echo "<button type=\"button\" class=\"DEAT_YES\" title=\"".$pgv_lang["button_DEAT_YES"]."\" >";
	echo $factarray["DEAT"]."&gt;100</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y100\" title=\"".$pgv_lang["button_DEAT_Y100"]."\" >";
	echo $factarray["DEAT"]."&lt;=100</button> ";
	echo "<button type=\"button\" class=\"reset\" title=\"".$pgv_lang["button_reset"]."\" >";
	echo $pgv_lang["reset"]."</button> ";
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">INDI</th>";
	echo "<th class=\"list_label\">".$factarray["NAME"]."</th>";
	if ($option=="sosa") echo "<th class=\"list_label\">Sosa</th>";
	echo "<th class=\"list_label\">".$factarray["BIRT"]."</th>";
	echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".$pgv_lang["anniversary"]."\" title=\"".$pgv_lang["anniversary"]."\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">".$factarray["PLAC"]."</th>";
	echo "<th class=\"list_label\"><img src=\"./images/children.gif\" alt=\"".$pgv_lang["children"]."\" title=\"".$pgv_lang["children"]."\" border=\"0\" /></th>";
	echo "<th class=\"list_label\">".$factarray["DEAT"]."</th>";
	echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".$pgv_lang["anniversary"]."\" title=\"".$pgv_lang["anniversary"]."\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">".$factarray["AGE"]."</th>";
	echo "<th class=\"list_label\">".$factarray["PLAC"]."</th>";
	if ($SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">".$factarray["CHAN"]."</th>";
	echo "<th class=\"list_label\" style=\"display:none\">SEX</th>";
	echo "<th class=\"list_label\" style=\"display:none\">BIRT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">DEAT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">TREE</th>";
	echo "</tr>\n";
	//-- table body
	$hidden = 0;
	$n = 0;
	$dateY = date("Y");
	foreach($datalist as $key => $value) {
		if (!is_array($value)) {
			$person = null;
			if (strpos($key, $GEDCOM_ID_PREFIX)!==false) $person = Person::getInstance($key); // from placelist
			if (is_null($person)) $person = Person::getInstance($value); // from ancestry chart and search
			if (!is_null($person)) $name = $person->getSortableName(); //-- for search results
		}
		else {
			$gid = "";
			if (isset($value["gid"])) $gid = $value["gid"]; // from indilist
			if (isset($value[4])) $gid = $value[4]; // from indilist ALL
			if (isset($value["gedcom"])) $person = new Person($value["gedcom"]); // from source.php
			else $person = Person::getInstance($gid);
			if (isset($value["name"]) && $person->canDisplayName()) $name = $value["name"];
			else $name = $person->getSortableName();
			if (isset($value[4])) $name = $person->getSortableName($value[0]); // from indilist ALL
		}
		/* @var $person Person */
		if (is_null($person)) continue;
		if (!$person->canDisplayName()) {
			$hidden++;
			continue;
		}
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		//-- Gedcom ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$person->getLinkUrl()."\" class=\"list_item\">".$person->xref."</a></td>";
		}
		//-- Indi name(s)
		if ($person->isDead()) echo "<td class=\"list_value_wrap\"";
		else echo "<td class=\"list_value_wrap alive\"";
		echo " align=\"".get_align($name)."\">";
		echo "<a href=\"".$person->getLinkUrl()."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
		echo $person->getSexImage();
		foreach ($name_subtags as $k=>$subtag) {
			for ($num=1; $num<9; $num++) {
				$addname = $person->getSortableName($subtag, $num);
				if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$person->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
				if (empty($addname)) break;
			}
		}
		echo "</td>";
		//-- SOSA
		if ($option=="sosa") {
			echo "<td class=\"list_value_wrap\">";
			$sosa = $key;
			$rootid = $datalist[1];
			echo "<a href=\"relationship.php?pid1=".$rootid."&amp;pid2=".$person->xref."\"".
			" title=\"".$pgv_lang["relationship_chart"]."\"".
			" class=\"list_item name2\">".$sosa."</a>";
			echo "</td>";
		}
		//-- Birth date
		echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap\">";
		$sortkey = $person->getSortableBirthDate();
		$txt = get_changed_date($person->getBirthDate(), true);
		//if (empty($txt)) $txt = $pgv_lang["yes"];
		echo "&nbsp;<a href=\"".$person->getDateUrl($person->bdate)."\"".
		" title=\"".$sortkey."\"".
		" class=\"list_item\">".$txt."</a>";
		//-- Birth 2nd date ?
		$txt = get_changed_date($person->bdate2, true);
		if ($txt) echo "<br /><a href=\"".$person->getDateUrl($person->bdate2)."\"".
		" title=\"".$person->getSortableBirthDate2()."\"".
		" class=\"list_item\">".$txt."</a>";
		echo "</td>";
		//-- Birth anniversary
		echo "<td class=\"list_value_wrap rela\">";
		$age = $person->getAge("", date("d M Y"));
		if ($age) {
			$age = str_replace($pgv_lang["apx"]." ", "", $age);
			echo "<a href=\"".$person->getDateUrl($person->bdate)."\" class=\"list_item\">".$age."</a>";
		}
		else echo "&nbsp;";
		echo "</td>";
		//-- Birth place
		echo "<td class=\"list_value_wrap\" align=\"".get_align($person->getBirthPlace())."\">";
		echo "<a href=\"".$person->getPlaceUrl($person->getBirthPlace())."\" class=\"list_item\" title=\"".$person->getBirthPlace()."\">"
		.PrintReady($person->getPlaceShort($person->getBirthPlace()))."</a>";
		echo "&nbsp;</td>";
		//-- Number of children
		echo "<td class=\"list_value_wrap\">";
		if ($person->disp) {
			echo "<a href=\"".$person->getLinkUrl()."\" class=\"list_item\">".$person->getNumberOfChildren()."</a>";
		}
		echo "</td>";
		//-- Death date
		echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap\">";
		$sortkey = $person->getSortableDeathDate();
		$txt = get_changed_date($person->getDeathDate(), true);
		if ($person->dest) $txt = $pgv_lang["yes"];
		if (!$person->isDead()) $txt = "";
		//else if (get_sub_record(1, "1 DEAT", $person->gedrec)=="") $txt .= "***";
		echo "&nbsp;<a href=\"".$person->getDateUrl($person->ddate)."\"".
		" title=\"".$sortkey."\"".
		" class=\"list_item\">".$txt."</a>";
		//-- Death 2nd date ?
		$txt = get_changed_date($person->ddate2, true);
		if ($txt) echo "<br /><a href=\"".$person->getDateUrl($person->ddate2)."\"".
		" title=\"".$person->getSortableDeathDate2()."\"".
		" class=\"list_item\">".$txt."</a>";
		echo "</td>";
		//-- Death anniversary
		echo "<td class=\"list_value_wrap rela\">";
		$age = "";
		if ($person->isDead() and !$person->dest) $age = $person->getAge("\n1 BIRT\n2 DATE ".$person->ddate."\n", date("d M Y"));
		if ($age) {
			$age = str_replace($pgv_lang["apx"]." ", "", $age);
			echo "<a href=\"".$person->getDateUrl($person->ddate)."\" class=\"list_item\">".$age."</a>";
		}
		else echo "&nbsp;";
		echo "</td>";
		//-- Age at death
		echo "<td class=\"list_value_wrap\">";
		$age = "";
		$sortkey = 0;
		if ($person->isDead() and !$person->dest) {
			$age = $person->getAge();
			$age = str_replace($pgv_lang["apx"]." ", "", $age);
			// estimation in days for sorting
			if (strpos($age, $pgv_lang["months"]) or strpos($age, $pgv_lang["month1"])) $sortkey = $age*30;
			else if (strpos($age, $pgv_lang["days"]) or strpos($age, $pgv_lang["day1"])) $sortkey = $age*1;
			else $sortkey = $age*365;
		}
		echo "<a href=\"".$person->getLinkUrl()."\" title=\"".sprintf("%05d",$sortkey)."\" class=\"list_item\">&nbsp;".$age."</a>";
		echo "</td>";
		//-- Death place
		echo "<td class=\"list_value_wrap\" align=\"".get_align($person->getDeathPlace())."\">";
		echo "<a href=\"".$person->getPlaceUrl($person->getDeathPlace())."\" class=\"list_item\" title=\"".$person->getDeathPlace()."\">"
		.PrintReady($person->getPlaceShort($person->getDeathPlace()))."</a>";
		echo "&nbsp;</td>";
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap rela\">";
			$timestamp = get_changed_date($person->getLastchangeDate())." ".get_gedcom_value("DATE:TIME", 2, $person->getLastchangeRecord());
			echo "<a href=\"".$person->getLinkUrl()."\"".
			" title=\"".$person->getSortableLastchangeDate()."\"".
			" class=\"list_item\">".$timestamp."</a>";
			echo "&nbsp;</td>";
		}
		//-- Sorting by gender
		echo "<td style=\"display:none\">";
		echo $person->getSex();
		echo "</td>";
		//-- Sorting by birth date
		echo "<td style=\"display:none\">";
		if (!$person->disp || $person->getBirthYear()>=$dateY-100) echo "Y100";
		else echo "YES";
		echo "</td>";
		//-- Sorting by death date
		echo "<td style=\"display:none\">";
		if ($person->isDead()) {
			if ($person->getDeathYear()>=$dateY-100) echo "Y100";
			else echo "YES";
		}
		else echo "N";
		echo "</td>";
		//-- Roots or Leaves ?
		echo "<td style=\"display:none\">";
		if (!$person->getChildFamilyIds()) echo "R"; // roots
		else if (!$person->isDead() and $person->getNumberOfChildren()<1) echo "L"; // leaves
		echo "</td>";

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td class=\"list_label\">";
	echo $pgv_lang["total_names"]." : ".$n;
	if ($hidden) echo "<br />".$pgv_lang["hidden"]." : ".$hidden;
	echo "</td>";
	if ($option=="sosa") echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	if ($SHOW_LAST_CHANGE) echo "<td></td>";
	echo "</tr>";
	echo "</table>\n";
	echo "</fieldset>\n";
}

/**
 * print a sortable table of surnames
 *
 * @param array $datalist contain records that were extracted from the database.
 * @param string $target where to go after clicking a surname : INDI page or FAM page
 */
function print_surn_table($datalist, $target="INDI") {
	global $pgv_lang, $factarray, $GEDCOM, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	require_once("js/sorttable.js.htm");
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	echo "<th class=\"list_label\">".$factarray["SURN"]."</th>";
	echo "<th class=\"list_label\">";
	if ($target=="FAM") echo $pgv_lang["families"]; else echo $pgv_lang["individuals"];
//	if ($target=="FAM") echo $pgv_lang["spouses"]; else echo $pgv_lang["individuals"];
	echo "</th>";
	echo "</tr>\n";
	//-- table body
	$total = 0;
	$n = 0;
	foreach($datalist as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		if ($target=="FAM") $url = "famlist.php";	else $url = "indilist.php";
		$url .= "?ged=".$GEDCOM."&amp;surname=".urlencode($surn);
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		//-- Surname
		if (empty($surn) or trim("@".$surn,"_")=="@" or $surn=="@N.N.") $surn = $pgv_lang["NN"];
		echo "<td class=\"list_value_wrap\" align=\"".get_align($surn)."\">";
		echo "<a href=\"".$url."\" class=\"list_item name1\">".PrintReady($surn)."</a>";
		echo "&nbsp;</td>";
		//-- Surname count
		echo "<td class=\"list_value_wrap\">";
		echo "<a href=\"".$url."\" class=\"list_item name2\">".$value["match"]."</a>";
		echo "</td>";
		$total += $value["match"];

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	echo "<td class=\"list_item\">&nbsp;</td>";
	echo "<td class=\"list_item\">&nbsp;</td>";
	echo "<td class=\"list_label name2\">".$total."</td>";
	echo "</tr>\n";
	echo "</table>\n";
}

/**
 * print a sortable table of families
 *
 * @param array $datalist contain families that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_fam_table($datalist, $legend="") {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $SHOW_MARRIED_NAMES, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	$name_subtags = array("", "_HEB", "ROMN", "_AKA");
	//if ($SHOW_MARRIED_NAMES) $name_subtags[] = "_MARNM";
	require_once("js/sorttable.js.htm");
	require_once("includes/family_class.php");
	if (empty($legend)) $legend=$pgv_lang["families"];
	echo "<fieldset><legend>".$legend."</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- filter buttons
	echo "<button type=\"button\" class=\"DEAT_N\" title=\"".$pgv_lang["button_DEAT_N"]."\" >";
	echo $pgv_lang["both_alive"]."</button> ";
	echo "<button type=\"button\" class=\"DEAT_W\" title=\"".$pgv_lang["button_DEAT_W"]."\" >";
	echo $pgv_lang["widower"]."</button> ";
	echo "<button type=\"button\" class=\"DEAT_H\" title=\"".$pgv_lang["button_DEAT_H"]."\" >";
	echo $pgv_lang["widow"]."</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y\" title=\"".$pgv_lang["button_DEAT_Y"]."\" >";
	echo $pgv_lang["both_dead"]."</button> ";
	echo "<button type=\"button\" class=\"TREE_R\" title=\"".$pgv_lang["button_TREE_R"]."\" >";
	echo $pgv_lang["roots"]."</button> ";
	echo "<button type=\"button\" class=\"TREE_L\" title=\"".$pgv_lang["button_TREE_L"]."\" >";
	echo $pgv_lang["leaves"]."</button> ";
	echo "<br />";
	$y100 = get_changed_date(date('Y')-100);
	echo "<button type=\"button\" class=\"MARR_U\" title=\"".$pgv_lang["button_MARR_U"]."\" >";
	echo $factarray["MARR"]." ?</button> ";
	echo "<button type=\"button\" class=\"MARR_YES\" title=\"".$pgv_lang["button_MARR_YES"]."\" >";
	echo $factarray["MARR"]."&gt;100</button> ";
	echo "<button type=\"button\" class=\"MARR_Y100\" title=\"".$pgv_lang["button_MARR_Y100"]."\" >";
	echo $factarray["MARR"]."&lt;=100</button> ";
	echo "<button type=\"button\" class=\"MARR_DIV\" title=\"".$pgv_lang["button_MARR_DIV"]."\" >";
	echo $factarray["DIV"]."</button> ";
	echo "<button type=\"button\" class=\"reset\" title=\"".$pgv_lang["button_reset"]."\" >";
	echo $pgv_lang["reset"]."</button> ";
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">FAM</th>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">INDI</th>";
	echo "<th class=\"list_label\">".$factarray["NAME"]."</th>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">INDI</th>";
	echo "<th class=\"list_label\">".$pgv_lang["spouse"]."</th>";
	echo "<th class=\"list_label\">".$factarray["MARR"]."</th>";
	echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".$pgv_lang["anniversary"]."\" title=\"".$pgv_lang["anniversary"]."\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">".$factarray["AGE"]."</th>";
	echo "<th class=\"list_label\">".$factarray["AGE"]."</th>";
	echo "<th class=\"list_label\">".$factarray["PLAC"]."</th>";
	echo "<th class=\"list_label\"><img src=\"./images/children.gif\" alt=\"".$pgv_lang["children"]."\" title=\"".$pgv_lang["children"]."\" border=\"0\" /></th>";
	if ($SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">".$factarray["CHAN"]."</th>";
	echo "<th class=\"list_label\" style=\"display:none\">MARR</th>";
	echo "<th class=\"list_label\" style=\"display:none\">DEAT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">TREE</th>";
	echo "</tr>\n";
	//-- table body
	$hidden = 0;
	$n = 0;
	foreach($datalist as $key => $value) {
		if (!is_array($value)) {
			$family = Family::getInstance($key); // from placelist
			if (is_null($family)) $family = Family::getInstance($value); // from ancestry chart
			unset($value);
		}
		else {
			$gid = "";
			if (isset($value["gid"])) $gid = $value["gid"];
			if (isset($value["gedcom"])) $family = new Family($value["gedcom"]);
			else $family = Family::getInstance($gid);
		}
		if (is_null($family)) continue;
		//-- Retrieve husband and wife
		$husb = $family->getHusband();
		if (is_null($husb)) $husb = new Person('');
		$wife = $family->getWife();
		if (is_null($wife)) $wife = new Person('');
		if (!$husb->canDisplayName() or !$wife->canDisplayName()) {
			$hidden++;
			continue;
		}
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		//-- Family ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$family->getLinkUrl()."\" class=\"list_item\">".$family->xref."</a>";
			echo "</td>";
		}
		//-- Husband ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$husb->getLinkUrl()."\" class=\"list_item\">".$husb->xref."</a>";
			echo "</td>";
		}
		//-- Husband name(s)
		if (isset($value["name"])) {
			$partners = explode(" + ", $value["name"]); // "husb + wife"
			$name = check_NN($partners[0]);
		}
		else $name = $husb->getSortableName();
		if ($husb->isDead()) echo "<td class=\"list_value_wrap\"";
		else echo "<td class=\"list_value_wrap alive\"";
		echo " align=\"".get_align($name)."\">";
		echo "<a href=\"".$family->getLinkUrl()."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
		if ($husb->xref) echo $husb->getSexImage();
		foreach ($name_subtags as $k=>$subtag) {
			for ($num=1; $num<9; $num++) {
				$addname = $husb->getSortableName($subtag, $num);
				if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$family->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
				if (empty($addname)) break;
			}
		}
		echo "</td>";
		//-- Wife ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$wife->getLinkUrl()."\" class=\"list_item\">".$wife->xref."</a>";
			echo "</td>";
		}
		//-- Wife name(s)
		if (isset($value["name"])) $name = check_NN($partners[1]);
		else $name = $wife->getSortableName();
		if ($wife->isDead()) echo "<td class=\"list_value_wrap\"";
		else echo "<td class=\"list_value_wrap alive\"";
		echo " align=\"".get_align($name)."\">";
		echo "<a href=\"".$family->getLinkUrl()."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
		if ($wife->xref) echo $wife->getSexImage();
		foreach ($name_subtags as $k=>$subtag) {
			for ($num=1; $num<9; $num++) {
				$addname = $wife->getSortableName($subtag, $num);
				if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$family->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
				if (empty($addname)) break;
			}
		}
		echo "</td>";
		//-- Marriage date
		echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap\">";
		$sortkey = $family->getSortableMarriageDate();
		if (!$family->marr_est) $txt = get_changed_date($family->getMarriageDate(), true);
		if (empty($txt) and !empty($family->marr_rec)) $txt = $pgv_lang["yes"];
		echo "&nbsp;<a href=\"".$family->getDateUrl($family->marr_date)."\"".
		" title=\"".$sortkey."\"".
		" class=\"list_item\">".$txt."</a>";
		//-- Marriage 2nd date ?
		$txt = get_changed_date($family->marr_date2, true);
		if ($txt) echo "<br /><a href=\"".$family->getDateUrl($family->marr_date2)."\"".
		" title=\"".$family->getSortableMarriageDate2()."\"".
		" class=\"list_item\">".$txt."</a>";
		echo "</td>";
		//-- Marriage anniversary
		echo "<td class=\"list_value_wrap rela\">";
		$age = "";
		if (!$family->marr_est) $age = $husb->getAge("\n1 BIRT\n2 DATE ".$family->marr_date."\n", date("d M Y"));
		if ($age) {
			$age = str_replace($pgv_lang["apx"]." ", "", $age);
			echo "<a href=\"".$family->getDateUrl($family->marr_date)."\" class=\"list_item\">".$age."</a>";
		}
		else echo "&nbsp;";
		echo "</td>";
		//-- Husband age
		echo "<td class=\"list_value_wrap\">";
		$age = "";
		if ($family->getMarriageDate() and !$family->marr_est) {
			$age = $husb->getAge("", $family->getMarriageDate());
			$age = str_replace($pgv_lang["apx"]." ", "", $age);
		}
		echo "<a href=\"".$husb->getLinkUrl()."\" title=\"".sprintf("%02d",$age)."\" class=\"list_item\">&nbsp; ".$age."</a>";
		echo "</td>";
		//-- Wife age
		echo "<td class=\"list_value_wrap\">";
		$age = "";
		if ($family->getMarriageDate() and !$family->marr_est) {
			$age = $wife->getAge("", $family->getMarriageDate());
			$age = str_replace($pgv_lang["apx"]." ", "", $age);
		}
		echo "<a href=\"".$wife->getLinkUrl()."\" title=\"".sprintf("%02d",$age)."\" class=\"list_item\">&nbsp;".$age."</a>";
		echo "</td>";
		//-- Marriage place
		echo "<td class=\"list_value_wrap\" align=\"".get_align($family->getMarriagePlace())."\">";
		echo "<a href=\"".$family->getPlaceUrl($family->getMarriagePlace())."\" class=\"list_item\" title=\"".$family->getMarriagePlace()."\">"
		.PrintReady($family->getPlaceShort($family->getMarriagePlace()))."</a>";
		echo "&nbsp;</td>";
		//-- Number of children
		echo "<td class=\"list_value_wrap\">";
		echo "<a href=\"".$family->getLinkUrl()."\" class=\"list_item\">".$family->getNumberOfChildren()."</a>";
		echo "</td>";
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap rela\">";
			$timestamp = get_changed_date($family->getLastchangeDate())." ".get_gedcom_value("DATE:TIME", 2, $family->getLastchangeRecord());
			echo "<a href=\"".$family->getLinkUrl()."\"".
			" title=\"".$family->getSortableLastchangeDate()."\"".
			" class=\"list_item\">".$timestamp."</a>";
			echo "&nbsp;</td>";
		}
		//-- Sorting by marriage date
		echo "<td style=\"display:none\">";
		if (!$family->disp or $family->getMarriageRecord()=="" or $family->getMarriageYear()=="0000") echo "U";
		else if ($family->getMarriageYear()>=date('Y')-100) echo "Y100";
		else echo "YES";
		if ($family->isDivorced()) echo " DIV";
		echo "</td>";
		//-- Sorting alive/dead
		echo "<td style=\"display:none\">";
		if ($husb->isDead() and $wife->isDead()) echo "Y";
		if ($husb->isDead() and !$wife->isDead()) {
			if ($wife->getSex()=="F") echo "H";
			if ($wife->getSex()=="M") echo "W"; // male partners
		}
		if (!$husb->isDead() and $wife->isDead()) {
			if ($husb->getSex()=="M") echo "W";
			if ($husb->getSex()=="F") echo "H"; // female partners
		}
		if (!$husb->isDead() and !$wife->isDead()) echo "N";
		echo "</td>";
		//-- Roots or Leaves
		echo "<td style=\"display:none\">";
		if (!$husb->getChildFamilyIds() and !$wife->getChildFamilyIds()) echo "R"; // roots
		else if (!$husb->isDead() and !$wife->isDead() and $family->getNumberOfChildren()<1) echo "L"; // leaves
		echo "</td>";

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td class=\"list_label\">";
	echo $pgv_lang["total_fams"]." : ".$n;
	if ($hidden) echo "<br />".$pgv_lang["hidden"]." : ".$hidden;
	echo "</td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	if ($SHOW_LAST_CHANGE) echo "<td></td>";
	echo "</tr>";
	echo "</table>\n";
	echo "</fieldset>\n";
}

/**
 * print a sortable table of sources
 *
 * @param array $datalist contain sources that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_sour_table($datalist, $legend="") {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	$name_subtags = array("_HEB", "ROMN");
	require_once("js/sorttable.js.htm");
	require_once("includes/source_class.php");
	if (empty($legend)) $legend=$pgv_lang["sources"];
	echo "<fieldset><legend>".$legend."</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">SOUR</th>";
	echo "<th class=\"list_label\">".$factarray["TITL"]."</th>";
	$t2 = false; echo "<td class=\"list_label t2\">".$factarray["TITL"]."2</td>";
	echo "<th class=\"list_label\">".$factarray["AUTH"]."</th>";
	//-- only show the count of linked records if the DB is sufficiently small to handle the load
	$show_details = (get_list_size("indilist")<1000);
	if ($show_details) {
		echo "<th class=\"list_label\">".$pgv_lang["individuals"]."</th>";
		echo "<th class=\"list_label\">".$pgv_lang["families"]."</th>";
		echo "<th class=\"list_label\">".$pgv_lang["media"]."</th>";
	}
	if ($SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">".$factarray["CHAN"]."</th>";
	echo "</tr>\n";
	//-- table body
	$hidden = 0;
	$n = 0;
	foreach ($datalist as $key => $value) {
		if (!is_array($value)) {
			$source = Source::getInstance($key); // from placelist
			if (is_null($source)) $source = Source::getInstance($value);
			unset($value);
		}
		else {
			$gid = "";
			if (isset($value["gid"])) $gid = $value["gid"];
			if (isset($value["gedcom"])) $source = new Source($value["gedcom"]);
			else $source = Source::getInstance($gid);
		}
		if (is_null($source)) continue;
		if (!$source->canDisplayDetails()) {
			$hidden++;
			continue;
		}
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		//-- Source ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item\">".$source->xref."</a>";
			echo "</td>";
		}
		//-- Source name(s)
		$name = $source->getSortableName();
		echo "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
		echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item name2\">".PrintReady($name)."</a>";
		echo "</td>";
		// alternate title in a new column
		echo "<td class=\"list_value_wrap t2\">";
		foreach ($name_subtags as $k=>$subtag) {
			$addname = $source->getSortableName($subtag);
			if (!empty($addname) and $addname!=$name) {
				echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a><br />";
				$t2 = true;
			}
		}
		echo "&nbsp;</td>";
		//-- Author
		echo "<td class=\"list_value_wrap\" align=\"".get_align($source->getAuth())."\">";
		echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item\">".PrintReady($source->getAuth())."</a>";
		echo "&nbsp;</td>";

		if ($show_details) {
			//-- Linked INDIs
			echo "<td class=\"list_value_wrap\">";
			echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item\">".$source->countSourceIndis()."</a>";
			echo "</td>";
			//-- Linked FAMs
			echo "<td class=\"list_value_wrap\">";
			echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item\">".$source->countSourceFams()."</a>";
			echo "</td>";
			//-- Linked OBJEcts
			echo "<td class=\"list_value_wrap\">";
			echo "<a href=\"".$source->getLinkUrl()."\" class=\"list_item\">".$source->countSourceObjects()."</a>";
			echo "</td>";
		}
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap rela\">";
			$timestamp = get_changed_date($source->getLastchangeDate())." ".get_gedcom_value("DATE:TIME", 2, $source->getLastchangeRecord());
			echo "<a href=\"".$source->getLinkUrl()."\"".
			" title=\"".$source->getSortableLastchangeDate()."\"".
			" class=\"list_item\">".$timestamp."</a>";
			echo "&nbsp;</td>";
		}

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td class=\"list_label\">";
	echo $pgv_lang["total_sources"]." : ".$n;
	if ($hidden) echo "<br />".$pgv_lang["hidden"]." : ".$hidden;
	echo "</td>";
	echo "<td></td>";
	echo "<td class=\"t2\"></td>";
	echo "<td></td>";
	if ($show_details) {
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
	}
	if ($SHOW_LAST_CHANGE) echo "<td></td>";
	echo "</tr>";
	echo "</table>\n";
	echo "</fieldset>\n";
	//-- hide TITLE2 col if empty
	if (!$t2) {
		echo <<< T2
		<script type="text/javascript">
		// <![CDATA[
			var table = document.getElementById("$table_id");
			cells = table.getElementsByTagName('td');
			for (i=0;i<cells.length;i++) if (cells[i].className && (cells[i].className.indexOf('t2') != -1)) cells[i].style.display='none';
		// ]]>
		</script>
T2;
	}
}

/**
 * print a sortable table of repositories
 *
 * @param array $datalist contain repositories that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_repo_table($datalist, $legend="") {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	$name_subtags = array("_HEB", "ROMN");
	require_once("js/sorttable.js.htm");
	require_once("includes/repository_class.php");
	if (empty($legend)) $legend=$pgv_lang["repos_found"];
	echo "<fieldset><legend>".$legend."</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">REPO</th>";
	echo "<th class=\"list_label\">".$factarray["NAME"]."</th>";
	echo "<th class=\"list_label\">".$pgv_lang["sources"]."</th>";
	if ($SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">".$factarray["CHAN"]."</th>";
	echo "</tr>\n";
	//-- table body
	$n = 0;
	foreach ($datalist as $key => $value) {
		if (!is_array($value)) {
			$repo = Repository::getInstance($key);
			if (is_null($repo)) $repo = Repository::getInstance($value);
			unset($value);
		}
		else {
			$gid = "";
			if (isset($value["gid"])) $gid = $value["gid"];
			if (isset($value["gedcom"])) $repo = new Repository($value["gedcom"]);
			else $repo = Repository::getInstance($gid);
		}
		if (is_null($repo)) continue;
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		//-- REPO ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$repo->getLinkUrl()."\" class=\"list_item\">".$repo->xref."</a>";
			echo "</td>";
		}
		//-- Repository name(s)
		$name = $repo->getSortableName();
		echo "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
		echo "<a href=\"".$repo->getLinkUrl()."\" class=\"list_item name2\">".PrintReady($name)."</a>";
		foreach ($name_subtags as $k=>$subtag) {
			$addname = $repo->getSortableName($subtag);
			if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$repo->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
		}
		echo "</td>";
		//-- Linked SOURces
		echo "<td class=\"list_value_wrap\">";
		echo "<a href=\"".$repo->getLinkUrl()."\" class=\"list_item\">".count($repo->getRepositorySours())."</a>";
		echo "</td>";
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap rela\">";
			$timestamp = get_changed_date($repo->getLastchangeDate())." ".get_gedcom_value("DATE:TIME", 2, $repo->getLastchangeRecord());
			echo "<a href=\"".$repo->getLinkUrl()."\"".
			" title=\"".$repo->getSortableLastchangeDate()."\"".
			" class=\"list_item\">".$timestamp."</a>";
			echo "&nbsp;</td>";
		}

		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</fieldset>\n";
}

/**
 * print a sortable table of media objects
 *
 * @param array $datalist contain media objects that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_media_table($datalist, $legend="") {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	require_once("js/sorttable.js.htm");
	require_once("includes/media_class.php");
	if (empty($legend)) $legend=$pgv_lang["media"];
	echo "<fieldset><legend>".$legend."</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">OBJE</th>";
	echo "<th class=\"list_label\">".$factarray["TITL"]."</th>";
	echo "<th class=\"list_label\">".$pgv_lang["individuals"]."</th>";
	echo "<th class=\"list_label\">".$pgv_lang["families"]."</th>";
	echo "<th class=\"list_label\">".$pgv_lang["sources"]."</th>";
	if ($SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">".$factarray["CHAN"]."</th>";
	echo "</tr>\n";
	//-- table body
	$n = 0;
	foreach ($datalist as $key => $value) {
		$media = new Media($value["GEDCOM"]);
		if (is_null($media)) $media = Media::getInstance($key);
		if (is_null($media)) continue;
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		//-- Object ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$media->getLinkUrl()."\" class=\"list_item\">".$media->xref."</a>";
			echo "</td>";
		}
		//-- Object name(s)
		$name = $media->getSortableName();
		echo "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
		echo "<a href=\"".$media->getLinkUrl()."\" class=\"list_item name2\">".PrintReady($name)."</a>";
		echo "<br /><a href=\"".$media->getLinkUrl()."\">".basename($media->file)."</a>";
		echo "<br />".$media->getFiletype();
		echo "&nbsp;&nbsp;".$media->width."x".$media->height;
		echo "&nbsp;&nbsp;".$media->getFilesize()."kB";
		print_fact_notes("1 NOTE ".$media->getNote(),1);
		echo "</td>";
		//-- Linked records
		foreach (array("INDI", "FAM", "SOUR") as $rectype) {
			$resu = array();
			foreach ($value["LINKS"] as $k=>$v) {
			  if ($v!=$rectype) continue;
				$record = GedcomRecord::getInstance($k);
				$txt = $record->getSortableName();
				if ($SHOW_ID_NUMBERS) $txt .= " (".$k.")";
				$resu[] = $txt;
			}
			sort($resu);
			echo "<td class=\"list_value_wrap\" align=\"".get_align(@$resu[0])."\">";
			foreach ($resu as $txt) echo "<a href=\"".$record->getLinkUrl()."\" class=\"list_item\">".PrintReady("&bull; ".$txt)."</a><br />";
			echo "</td>";
		}
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap rela\">";
			$timestamp = get_changed_date($media->getLastchangeDate())." ".get_gedcom_value("DATE:TIME", 2, $media->getLastchangeRecord());
			echo "<a href=\"".$media->getLinkUrl()."\"".
			" title=\"".$media->getSortableLastchangeDate()."\"".
			" class=\"list_item\">".$timestamp."</a>";
			echo "&nbsp;</td>";
		}

		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</fieldset>\n";
}

/**
 * print a sortable table of recent changes
 *
 * @param array $datalist contain records that were extracted from the database.
 */
function print_changes_table($datalist) {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $SHOW_MARRIED_NAMES, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	require_once("js/sorttable.js.htm");
	require_once("includes/gedcomrecord.php");
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	//echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">".$pgv_lang["id"]."</th>";
	echo "<th class=\"list_label\">".$pgv_lang["record"]."</th>";
	echo "<th class=\"list_label\">".$factarray["CHAN"]."</th>";
	echo "<th class=\"list_label\">".$factarray["_PGVU"]."</th>";
	echo "</tr>\n";
	//-- table body
	$hidden = 0;
	$n = 0;
	foreach($datalist as $key => $value) {
		$record = GedcomRecord::getInstance($key);
		if (is_null($record) and isset($value[0])) $record = GedcomRecord::getInstance($value[0]);
		if (is_null($record)) continue;
		// Privacy
		if (!$record->canDisplayDetails()) {
			$hidden++;
			continue;
		}
		//-- Counter
		echo "<tr>";
		//echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		++$n;
		//-- Record ID
		if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			$sortkey = substr($record->xref, 0 ,1).sprintf("%05d", substr($record->xref, 1));
			echo "<a href=\"".$record->getLinkUrl()."\" title=\"".$sortkey."\" class=\"list_item\">".$record->xref."</a></td>";
		}
		//-- Record name(s)
		if ($record->type=="FAM") $name = $record->getSortableName(true);
		else $name = $record->getSortableName();
		echo "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
		echo "<a href=\"".$record->getLinkUrl()."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
		if ($record->type=="INDI") {
			echo $record->getSexImage();
			$name_subtags = array("", "_HEB", "ROMN", "_AKA");
			if ($SHOW_MARRIED_NAMES) $name_subtags[] = "_MARNM";
			foreach ($name_subtags as $k=>$subtag) {
				for ($num=1; $num<9; $num++) {
					$addname = $record->getSortableName($subtag, $num);
					if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$record->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
					if (empty($addname)) break;
				}
			}
		}
		if ($record->type=="SOUR" or $record->type=="REPO") {
			$name_subtags = array("_HEB", "ROMN");
			foreach ($name_subtags as $k=>$subtag) {
				$addname = $record->getSortableName($subtag);
				if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$record->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
			}
		}
		echo "</td>";
		//-- Last change
		echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap\">";
		$timestamp = get_changed_date($record->getLastchangeDate())." ".get_gedcom_value("DATE:TIME", 2, $record->getLastchangeRecord());
		echo "<a href=\"".$record->getLinkUrl()."\"".
		" title=\"".$record->getSortableLastchangeDate()."\"".
		" class=\"list_item\">".$timestamp."</a>";
		echo "&nbsp;</td>";
		//-- Last change user
		echo "<td class=\"list_value_wrap\">";
		echo "<a href=\"".$record->getLinkUrl()."\" class=\"list_item\">".$record->getLastchangeUser()."</a>";
		echo "&nbsp;</td>";

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	//echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td class=\"list_label\">";
	echo $pgv_lang["total_names"].": ".$n;
	if ($hidden) echo "<br />".$pgv_lang["hidden"].": ".$hidden;
	echo "</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "</tr>";
	echo "</table>\n";
}

/**
 * print a sortable table of events
 * and generates hCalendar records
 * @see http://microformats.org/
 *
 * @param array $datalist contain records that were extracted from the database.
 */
function print_events_table($datalist, $nextdays=0, $option="") {
	global $pgv_lang, $factarray, $LANGUAGE, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $SHOW_MARRIED_NAMES, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	require_once("js/sorttable.js.htm");
	require_once("includes/gedcomrecord.php");
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
	echo "<tr>";
	//echo "<td></td>";
	//if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">".$pgv_lang["id"]."</th>";
	echo "<th class=\"list_label\">".$pgv_lang["record"]."</th>";
	echo "<th class=\"list_label\">".$factarray["DATE"]."</th>";
	echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".$pgv_lang["anniversary"]."\" title=\"".$pgv_lang["anniversary"]."\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">".$factarray["EVEN"]."</th>";
	echo "</tr>\n";
	//-- table body
	$hidden = 0;
	$n = 0;
	$dateY = date("Y");
	// max anniversary date
	$datemax = mktime(0, 0, 0, date("m"), date("d")+$nextdays, $dateY);
	foreach($datalist as $key => $value) {

		//-- check if we actually need to load up the record from the DB first
		//-- Event name
		$exp = explode("\n", $value[1]);
		$exp = explode(" ", $exp[0]);
		$event = trim($exp[1]);
		if (empty($event)) continue;
		//-- only birt/marr/deat ?
		if (strpos($option, "onlyBDM") and strpos(" BIRT MARR DEAT", $event)===false) continue;
		//-- Event date
		$edate = get_gedcom_value("DATE", 2, $value[1], "", false);
		if (empty($edate)) continue;
		$timestamp = get_changed_date($edate, true);
		$pdate = parse_date($edate);
		
//		if (strpos($edate, "@#DHEBREW")!==false) $pdate = jewishGedcomDateToGregorian($pdate);
		if (strpos($edate, "@#DHEBREW")!==false) $pdate = jewishGedcomDateToCurrentGregorian($pdate);
		
		if ($pdate[0]["day"] == "") continue;
		$anniv = mktime(0, 0, 0, 0+$pdate[0]["mon"], 0+$pdate[0]["day"], $dateY);
		// add 1 year if anniversary before today
		if (date("Ymd", $anniv) < date("Ymd")) $anniv = mktime(0, 0, 0, 0+$pdate[0]["mon"], 0+$pdate[0]["day"], $dateY+1);

		if ($datemax < $anniv) continue;
		// upcoming events starting tomorrow
		if ($nextdays>0 and date("Ymd") == date("Ymd", $anniv)) continue;
		// sorting by MM-DD-YYYY
		$sortkey = sprintf("%02d-%02d-%04d", $pdate[0]["mon"], $pdate[0]["day"], $pdate[0]["year"]);

		//-- get gedcom record
		$record = GedcomRecord::getInstance($value[0]);
		if (is_null($record)) continue;
		//-- only living people ?
		if (strpos($option, "living")) {
			if ($record->type=="INDI" and $record->isDead()) continue;
			if ($record->type=="FAM") {
				$husb = $record->getHusband();
				if (is_null($husb)) continue;
				if ($husb->isDead()) continue;
				$wife = $record->getWife();
				if (is_null($wife)) continue;
				if ($wife->isDead()) continue;
			}
		}

		// Privacy
		if (!$record->canDisplayDetails()) {
			$hidden++;
			continue;
		}
		//-- Counter
		echo "<tr class=\"vevent\">"; // hCalendar:vevent
		//echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		$n++;
		//-- Record ID
		/**if ($SHOW_ID_NUMBERS) {
			echo "<td class=\"list_value_wrap rela\">";
			echo "<a href=\"".$record->getLinkUrl()."\" class=\"list_item\">".$record->xref."</a></td>";
		}**/
		//-- Record name(s)
		if ($record->type=="FAM") $name = $record->getSortableName(true);
		else $name = $record->getSortableName();
		echo "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
		echo "<a href=\"".$record->getLinkUrl()."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
		if ($record->type=="INDI") {
			echo $record->getSexImage();
			$name_subtags = array("", "_HEB", "ROMN", "_AKA");
			if ($SHOW_MARRIED_NAMES) $name_subtags[] = "_MARNM";
			foreach ($name_subtags as $k=>$subtag) {
				for ($num=1; $num<9; $num++) {
					$addname = $record->getSortableName($subtag, $num);
					if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$record->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
					if (empty($addname)) break;
				}
			}
		}
		if ($record->type=="SOUR" or $record->type=="REPO") {
			$name_subtags = array("_HEB", "ROMN");
			foreach ($name_subtags as $k=>$subtag) {
				$addname = $record->getSortableName($subtag);
				if (!empty($addname) and $addname!=$name) echo "<br /><a title=\"".$subtag."\" href=\"".$record->getLinkUrl()."\" class=\"list_item\">".PrintReady($addname)."</a>";
			}
		}
		echo "</td>";
		//-- Event date
		echo "<td class=\"".strrev($TEXT_DIRECTION)." list_value_wrap\">";
		echo "<a href=\"".$record->getDateUrl($edate)."\"".
		" title=\"".$sortkey."\"".
		" class=\"list_item\">".$timestamp."</a>";
		echo "</td>";
		//-- Anniversary
		echo "<td class=\"list_value_wrap rela\">";
		$person = new Person("");
		$age = $person->getAge("\n1 BIRT\n2 DATE ".$edate."\n", date("d M Y", $anniv));
		$age = str_replace($pgv_lang["apx"]." ", "", $age);
		echo "<a href=\"".$record->getDateUrl($edate)."\"".
		//" title=\"".strip_tags(get_changed_date(date("d M Y", $anniv)))."\"".
		" title=\"".date("m-d-Y", $anniv)."\"".
		" class=\"list_item\">".$age."</a>";
		echo "<abbr class=\"dtstart\" title=\"".date("Ymd", $anniv)."\"></abbr>"; // hCalendar:dtstart
		echo "<abbr class=\"summary\" title=\"".$pgv_lang["anniversary"]." #$age ".$factarray[$event]." : ".PrintReady(strip_tags($record->getSortableName()))."\"></abbr>"; // hCalendar:summary
		echo "</td>";
		//-- Event name
		echo "<td class=\"list_value_wrap\">";
		echo "<a href=\"".$record->getLinkUrl()."\" class=\"list_item url\">".$factarray[$event]."</a>"; // hCalendar:url
		echo "&nbsp;</td>";

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	//echo "<td></td>";
	//if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td class=\"list_label\">";
	echo $pgv_lang["total_names"].": ".$n;
	if ($hidden) echo "<br />".$pgv_lang["hidden"].": ".$hidden;
	echo "</td>";
	echo "<td>";
	if (strpos($option, "noDownload")===false) {
		$uri = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		global $whichFile;
		$whichFile = "hCal-events.ics";
		$title = print_text("download_file",0,1);
		if ($n) echo "<a href=\"http://feeds.technorati.com/events/".$uri."\"><img src=\"images/hcal.png\" border=\"0\" alt=\"".$title."\" title=\"".$title."\" /></a>";
	}
	echo "</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "</tr>";
	echo "</table>\n";
}

/**
 * check string align direction depending on language and rtl config
 *
 * @param string $txt string argument
 * @return string left|right
 */
function get_align($txt) {
		global $TEXT_DIRECTION, $USE_RTL_FUNCTIONS;

		if (!empty($txt)) {
  			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($txt) && hasLTRText($txt)) return "left";
  			if ($TEXT_DIRECTION=="ltr" && hasRTLText($txt) && !hasLTRText($txt) && $USE_RTL_FUNCTIONS) return "right";
		}
		if ($TEXT_DIRECTION=="rtl") return "right";
		return "left";
}

/**
 * load behaviour js data
 * to be called at the end just before </body> tag
 *
 * @see http://bennolan.com/behaviour/
 * @param none
 */
function load_behaviour() {
	global $pgv_lang;
	require_once("js/prototype.js.htm");
	require_once("js/behaviour.js.htm");
	require_once("js/overlib.js.htm");
?>
	<script type="text/javascript">
	// <![CDATA[
	var myrules = {
		'fieldset button' : function(element) {
			element.onmouseover = function() { // show helptext
				helptext = this.title;
				if (helptext=='') helptext = this.value;
				if (helptext=='' || helptext==undefined) helptext = 'Help text : button_'+this.className;
				this.title = helptext; if (document.all) return; // IE = title
				this.value = helptext; this.title = ''; // Firefox = value
				return overlib(helptext, BGCOLOR, "#000000", FGCOLOR, "#FFFFE0");
			}
			element.onmouseout = nd; // hide helptext
			element.onmousedown = function() { // show active button
				var buttons = this.parentNode.getElementsByTagName("button");
				for (var i=0; i<buttons.length; i++) buttons[i].style.opacity = 1;
				this.style.opacity = 0.67;
			}
			element.onclick = function() { // apply filter
				var temp = this.parentNode.getElementsByTagName("table")[0];
				if (!temp) return true;
				var table = temp.id;
				var args = this.className.split('_'); // eg: BIRT_YES
				if (args[0]=="alive") return table_filter_alive(table);
				if (args[0]=="reset") return table_filter(table, "", "");
				if (args[1].length) return table_filter(table, args[0], args[1]);
				return false;
			}
		},
		'.sortable th' : function(element) {
			element.onmouseout = nd; // hide helptext
			element.onmouseover = function() { // show helptext
				helptext = this.title;
				if (helptext=='') helptext = this.value;
				if (helptext=='' || helptext==undefined) helptext = <?php echo "'".$pgv_lang["sort_column"]."'"?>;
				this.title = helptext; if (document.all) return; // IE = title
				this.value = helptext; this.title = ''; // Firefox = value
				return overlib(helptext, BGCOLOR, "#000000", FGCOLOR, "#FFFFE0");
			}
		}
	}
	Behaviour.register(myrules);
	// ]]>
	</script>
<?php
}
?>

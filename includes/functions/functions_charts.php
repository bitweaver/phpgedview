<?php
/**
 * Functions used for charts
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage Charts
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_CHARTS_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_person.php');

/**
 * print a table cell with sosa number
 *
 * @param int $sosa
 * @param string $pid optional pid
 * @param string $arrowDirection   direction of link arrow
 */
function print_sosa_number($sosa, $pid = "", $arrowDirection = "up") {
	global $view, $pbwidth, $pbheight;
	global $PGV_IMAGE_DIR, $PGV_IMAGES;

	if (substr($sosa,-1,1)==".") $personLabel = substr($sosa,0,-1);
	else $personLabel = $sosa;
	if ($arrowDirection=="blank") $visibility = "hidden";
	else $visibility = "normal";
	print "<td class=\"subheaders center\" style=\"vertical-align: middle; text-indent: 0px; margin-top: 0px; white-space: nowrap; visibility: $visibility;\">";
	print getLRM() . $personLabel . getLRM();
	if ($sosa != "1" && $pid != "") {
		if ($arrowDirection=="left") $dir = 0;
		else if ($arrowDirection=="right") $dir = 1;
		else if ($arrowDirection== "down") $dir = 3;
		else $dir = 2;		// either "blank" or "up"
		print "<br />";
		print_url_arrow($pid, "#$pid", "$pid", $dir);
//		print "&nbsp;";
	}
	print "</td>";
}

/**
 * print family header
 *
 * @param string $famid family gedcom ID
 */
function print_family_header($famid) {
	$family=Family::getInstance($famid);
	if ($family) {
		echo '<p class="name_head">', PrintReady($family->getFullName()), '</p>';
	}
}

/**
 * print the parents table for a family
 *
 * @param string $famid family gedcom ID
 * @param int $sosa optional child sosa number
 * @param string $label optional indi label (descendancy booklet)
 * @param string $parid optional parent ID (descendancy booklet)
 * @param string $gparid optional gd-parent ID (descendancy booklet)
 */
function print_family_parents($famid, $sosa = 0, $label="", $parid="", $gparid="", $personcount="1") {
	global $pgv_lang, $view, $show_full, $show_famlink;
	global $TEXT_DIRECTION, $SHOW_EMPTY_BOXES, $SHOW_ID_NUMBERS, $LANGUAGE;
	global $pbwidth, $pbheight;
	global $PGV_IMAGE_DIR, $PGV_IMAGES;
	global $show_changes, $pgv_changes, $GEDCOM;

	$family = Family::getInstance($famid);
	if (is_null($family)) return;

	$husb = $family->getHusband();
	if (is_null($husb)) $husb = new Person('');
	$wife = $family->getWife();
	if (is_null($wife)) $wife = new Person('');

	if (!is_null($husb)) {
		$tempID = $husb->getXref();
		if (!empty($tempID)) print "<a name=\"{$tempID}\"></a>\r\n";
	}
	if (!is_null($wife)) {
		$tempID = $wife->getXref();
		if (!empty($tempID)) print "<a name=\"{$tempID}\"></a>\r\n";
	}
	print_family_header($famid);

	// -- get the new record and parents if in editing show changes mode
	if (PGV_USER_CAN_EDIT && isset($pgv_changes[$famid . "_" . $GEDCOM])) {
		$newrec = find_updated_record($famid);
		$newparents = find_parents_in_record($newrec);
	}

	/**
	 * husband side
	 */
	print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td rowspan=\"2\">";
	print "<span class=\"subheaders\">" . get_sosa_name($sosa*2) . "</span>";
	print "\n\t<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\" border=\"0\"><tr>";
	if ($parid) {
		if ($husb->getXref()==$parid) print_sosa_number($label);
		else print_sosa_number($label, "", "blank");
	}
	else if ($sosa > 0) print_sosa_number($sosa * 2);
	if (isset($newparents) && $husb->getXref() != $newparents["HUSB"]) {
		print "\n\t<td valign=\"top\" class=\"facts_valueblue\">";
		print_pedigree_person($newparents['HUSB'], 1, $show_famlink, 2, $personcount);
	} else {
		print "\n\t<td valign=\"top\">";
		print_pedigree_person($husb->getXref(), 1, $show_famlink, 2, $personcount);
	}
	print "</td></tr></table>";
	print "</td>\n";
	// husband's parents
	$hfams = $husb->getChildFamilies();
	$hparents = false;
	$upfamid = "";
	if (count($hfams) > 0 or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		print "<td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" width=\"3\" height=\"" . ($pbheight) . "\" alt=\"\" /></td>";
		print "<td><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td>";
		$hparents = false;
		foreach($hfams as $hfamid=>$hfamily) {
			if (!is_null($hfamily)) {
				$hparents = find_parents_in_record($hfamily->getGedcomRecord());
				$upfamid = $hfamid;
				break;
			}
		}
		if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
			// husband's father
			print "\n\t<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\" border=\"0\"><tr>";
			if ($sosa > 0) print_sosa_number($sosa * 4, $hparents['HUSB'], "down");
			if (!empty($gparid) and $hparents['HUSB']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
			print "\n\t<td valign=\"top\">";
			print_pedigree_person($hparents['HUSB'], 1, $show_famlink, 4, $personcount);
			print "</td></tr></table>";
		}
		print "</td>";
	}
	if (!empty($upfamid) and ($sosa!=-1) and ($view != "preview")) {
		print "<td valign=\"middle\" rowspan=\"2\">";
		print_url_arrow($upfamid, ($sosa==0 ? "?famid=$upfamid&amp;show_full=$show_full" : "#$upfamid"), "$upfamid", 1);
		print "</td>\n";
	}
	if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		// husband's mother
		print "</tr><tr><td><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td>";
		print "\n\t<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\" border=\"0\"><tr>";
		if ($sosa > 0) print_sosa_number($sosa * 4 + 1, $hparents['WIFE'], "down");
		if (!empty($gparid) and $hparents['WIFE']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
		print "\n\t<td valign=\"top\">";
		print_pedigree_person($hparents['WIFE'], 1, $show_famlink, 5, $personcount);
		print "</td></tr></table>";
		print "</td>\n";
	}
	print "</tr></table>\n\n";
	if ($sosa!=0) {
		print "<a href=\"family.php?famid=$famid\" class=\"details1\">";
		if ($SHOW_ID_NUMBERS) print getLRM() . "($famid)" . getLRM() . "&nbsp;&nbsp;";
		else print str_repeat("&nbsp;", 10);
		$marriage = $family->getMarriage();
		if ($marriage->canShow()) {
			$marriage->print_simple_fact();
		} else print $pgv_lang["private"];
		print "</a>";
	}
	else print "<br />\n";

	/**
	 * wife side
	 */
	print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td rowspan=\"2\">";
	print "<span class=\"subheaders\">" . get_sosa_name($sosa*2+1) . "</span>";
	print "\n\t<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\"><tr>";
	if ($parid) {
		if ($wife->getXref()==$parid) print_sosa_number($label);
		else print_sosa_number($label, "", "blank");
	}
	else if ($sosa > 0) print_sosa_number($sosa * 2 + 1);
	if (isset($newparents) && $wife->getXref() != $newparents["WIFE"]) {
		print "\n\t<td valign=\"top\" class=\"facts_valueblue\">";
		print_pedigree_person($newparents['WIFE'], 1, $show_famlink, 3, $personcount);
	} else {
		print "\n\t<td valign=\"top\">";
		print_pedigree_person($wife->getXref(), 1, $show_famlink, 3, $personcount);
	}
	print "</td></tr></table>";
	print "</td>\n";
	// wife's parents
	$hfams = $wife->getChildFamilies();
	$hparents = false;
	$upfamid = "";
	if (count($hfams) > 0 or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		print "<td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" width=\"3\" height=\"" . ($pbheight) . "\" alt=\"\" /></td>";
		print "<td><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td>";
		$j = 0;
		foreach($hfams as $hfamid=>$hfamily) {
			if (!is_null($hfamily)) {
				$hparents = find_parents_in_record($hfamily->getGedcomRecord());
				$upfamid = $hfamid;
				break;
			}
		}
		if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
			// wife's father
			print "\n\t<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\"><tr>";
			if ($sosa > 0) print_sosa_number($sosa * 4 + 2, $hparents['HUSB'], "down");
			if (!empty($gparid) and $hparents['HUSB']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
			print "\n\t<td valign=\"top\">";
			print_pedigree_person($hparents['HUSB'], 1, $show_famlink, 6, $personcount);
			print "</td></tr></table>";
		}
		print "</td>\n";
	}
	if (!empty($upfamid) and ($sosa!=-1) and ($view != "preview")) {
		print "<td valign=\"middle\" rowspan=\"2\">";
		print_url_arrow($upfamid, ($sosa==0 ? "?famid=$upfamid&amp;show_full=$show_full" : "#$upfamid"), "$upfamid", 1);
		print "</td>\n";
	}
	if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		// wife's mother
		print "</tr><tr><td><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td>";
		print "\n\t<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\"><tr>";
		if ($sosa > 0) print_sosa_number($sosa * 4 + 3, $hparents['WIFE'], "down");
		if (!empty($gparid) and $hparents['WIFE']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
		print "\n\t<td valign=\"top\">";
		print_pedigree_person($hparents['WIFE'], 1, $show_famlink, 7, $personcount);
		print "</td></tr></table>\n";
		print "</td>\n";
	}
	print "</tr></table>\n\n";
}

/**
 * print the children table for a family
 *
 * @param string $famid family gedcom ID
 * @param string $childid optional child ID
 * @param int $sosa optional child sosa number
 * @param string $label optional indi label (descendancy booklet)
 */
function print_family_children($famid, $childid = "", $sosa = 0, $label="", $personcount="1") {
	global $pgv_lang, $factarray, $pbwidth, $pbheight, $view, $show_famlink, $show_cousins;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $show_changes, $pgv_changes, $GEDCOM, $SHOW_ID_NUMBERS, $TEXT_DIRECTION;

	$family=Family::getInstance($famid);
	$children=$family->getChildrenIds();
	print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\"><tr>";
	if ($sosa>0) print "<td></td>";
	print "<td><span class=\"subheaders\">".$pgv_lang["children"]."</span></td>";
	if ($sosa>0) print "<td></td><td></td>";
	print "</tr>\n";

	$newchildren = array();
	$oldchildren = array();
	if (PGV_USER_CAN_EDIT) {
		if ((isset($_REQUEST['show_changes'])&&$_REQUEST['show_changes']=='yes') && (isset($pgv_changes[$famid . "_" . $GEDCOM]))) {
			$newrec = find_updated_record($famid);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $newrec, $match, PREG_SET_ORDER);
			if ($ct > 0) {
				$oldchil = array();
				for($i = 0; $i < $ct; $i++) {
					if (!in_array($match[$i][1], $children)) $newchildren[] = $match[$i][1];
					else $oldchil[] = $match[$i][1];
				}
				foreach($children as $indexval => $chil) {
					if (!in_array($chil, $oldchil)) $oldchildren[] = $chil;
				}
				//-- if there are no old or new children then the children were reordered
				if ((count($newchildren)==0)&&(count($oldchildren)==0)) {
					$children = array();
					for($i = 0; $i < $ct; $i++) {
						$children[] = $match[$i][1];
					}
				}
			}
		}
	}
	$nchi=1;
	if ((count($children) > 0) || (count($newchildren) > 0) || (count($oldchildren) > 0)) {
		foreach($children as $indexval => $chil) {
			if (!in_array($chil, $oldchildren)) {
				print "<tr>\n";
				if ($sosa != 0) {
					if ($chil == $childid) print_sosa_number($sosa, $childid);
					else if (empty($label)) print_sosa_number("");
					else print_sosa_number($label.($nchi++).".");
				}
				print "<td valign=\"middle\" >";
				print_pedigree_person($chil, 1, $show_famlink, 8, $personcount);
				$personcount++;
				print "</td>";
				if ($sosa != 0) {
					// loop for all families where current child is a spouse
					$famids = find_sfamily_ids($chil);
					$maxfam = count($famids)-1;
					for ($f=0; $f<=$maxfam; $f++) {
						$famid = $famids[$f];
						if (!$famid) continue;
						$parents = find_parents($famid);
						if (!$parents) continue;
						if ($parents["HUSB"] == $chil) $spouse = $parents["WIFE"];
						else $spouse =  $parents["HUSB"];
						// multiple marriages
						if ($f>0) {
							print "</tr>\n<tr><td>&nbsp;</td>";
							print "<td valign=\"top\"";
							if ($TEXT_DIRECTION == "rtl") print " align=\"left\">";
							else print " align=\"right\">";
							//if ($f==$maxfam) print "<img height=\"50%\"";
							//else print "<img height=\"100%\"";
							if ($f==$maxfam) print "<img height=\"".($pbheight/2-3)."px\"";
							else print "<img height=\"".$pbheight."px\"";
							print " width=\"3\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" alt=\"\" />";
							print "</td>";
						}
						print "<td class=\"details1\" valign=\"middle\" align=\"center\">";
 						$divrec = "";
						if (showFact("MARR", $famid)) {
							// marriage date
							$famrec = find_family_record($famid);
							$ct = preg_match("/2 DATE.*(\d\d\d\d)/", get_sub_record(1, "1 MARR", $famrec), $match);
							if ($ct>0) print "<span class=\"date\">".trim($match[1])."</span>";
							// divorce date
							$divrec = get_sub_record(1, "1 DIV", $famrec);
							$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $divrec, $match);
							if ($ct>0) print "-<span class=\"date\">".trim($match[1])."</span>";
						}
						print "<br /><img width=\"100%\" height=\"3\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" />";
						// family link
						if ($famid) {
							print "<br />";
							print "<a class=\"details1\" href=\"family.php?famid=$famid\">";
							if ($SHOW_ID_NUMBERS) print getLRM() . "&nbsp;($famid)&nbsp;" . getLRM();
							print "</a>";
						}
						print "</td>\n";
						// spouse information
						print "<td style=\"vertical-align: center;";
						if (!empty($divrec) and ($view != "preview")) print " filter:alpha(opacity=40);-moz-opacity:0.4\">";
						else print "\">";
						print_pedigree_person($spouse, 1, $show_famlink, 9, $personcount);
						$personcount++;
						print "</td>\n";
						// cousins
						if ($show_cousins) {
							print_cousins($famid, $personcount);
							$personcount++;
						}
					}
				}
				print "</tr>\n";
			}
		}
		foreach($newchildren as $indexval => $chil) {
			print "<tr >";
			print "<td valign=\"top\" class=\"facts_valueblue\" style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\">\n";
			print_pedigree_person($chil, 1, $show_famlink, 0, $personcount);
			$personcount++;
			print "</td></tr>\n";
		}
		foreach($oldchildren as $indexval => $chil) {
			print "<tr >";
			print "<td valign=\"top\" class=\"facts_valuered\" style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\">\n";
			print_pedigree_person($chil, 1, $show_famlink, 0, $personcount);
			$personcount++;
			print "</td></tr>\n";
		}
		// message 'no children' except for sosa
   }
   else if ($sosa<1) {
		print "<tr><td></td><td valign=\"top\" >";

		$nchi = "";
		if (isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_updated_record($famid);
		else $famrec = find_family_record($famid);
		$ct = preg_match("/1 NCHI (\w+)/", $famrec, $match);
		if ($ct>0) $nchi = $match[1];
		else {
			$famrec = find_family_record($famid);
			$ct = preg_match("/1 NCHI (\w+)/", $famrec, $match);
			if ($ct>0) $nchi = $match[1];
		}
		if ($nchi=="0") print "<img src=\"images/small/childless.gif\" alt=\"".$pgv_lang["childless_family"]."\" title=\"".$pgv_lang["childless_family"]."\" /> ".$pgv_lang["childless_family"];
		else print $pgv_lang["no_children"];
		print "</td></tr>";
   }
   else {
	   print "<tr>\n";
	   print_sosa_number($sosa, $childid);
	   print "<td valign=\"top\">";
	   print_pedigree_person($childid, 1, $show_famlink, 0, $personcount);
	   $personcount++;
	   print "</td></tr>\n";
   }
   print "</table><br />";

   if ($view!="preview" && $sosa==0 && PGV_USER_CAN_EDIT) {
	   print_help_link("add_child_help", "qm", "add_child_to_family");
		print "<a href=\"javascript:;\" onclick=\"return addnewchild('$famid','');\">" . $pgv_lang["add_child_to_family"] . "</a>";
		print "<span style='white-space:nowrap;'>";
		print " <a href=\"javascript:;\" onclick=\"return addnewchild('$famid','M');\">[".Person::sexImage('M', 'small', $pgv_lang['son'     ])."]</a>";
		print " <a href=\"javascript:;\" onclick=\"return addnewchild('$famid','F');\">[".Person::sexImage('F', 'small', $pgv_lang['daughter'])."]</a>";
		print "</span>";
   }
}
/**
 * print the facts table for a family
 *
 * @param string $famid family gedcom ID
 * @param int $sosa optional child sosa number
 */
function print_family_facts(&$family, $sosa = 0) {
	global $pgv_lang, $pbwidth, $pbheight, $view;
	global $nonfacts, $factarray;
	global $TEXT_DIRECTION, $GEDCOM, $SHOW_ID_NUMBERS;
	global $show_changes, $pgv_changes;
	global $linkToID;

	$famid=$family->getXref();
	// -- if both parents are displayable then print the marriage facts
	if ($family->canDisplayDetails()) {
		$linkToID = $famid;		// -- Tell addmedia.php what to link to
		// -- array of GEDCOM elements that will be found but should not be displayed
		$nonfacts = array("FAMS", "FAMC", "MAY", "BLOB", "HUSB", "WIFE", "CHIL", "");

		// -- find all the fact information
		$indifacts = $family->getFacts();

		if (count($indifacts) > 0) {
			sort_facts($indifacts);
			print "\n\t<span class=\"subheaders\">" . $pgv_lang["family_group_info"];
			if ($SHOW_ID_NUMBERS and $famid != "") print "&nbsp;&nbsp;&nbsp;($famid)";
			print "</span><br />\n\t<table class=\"facts_table\">";
			/* @var $value Event */
			foreach ($indifacts as $key => $value) {
				if ($value->getTag()!="SOUR" && $value->getTag()!="OBJE" && $value->getTag()!="NOTE")
					print_fact($value);
			}
			// do not print otheritems for sosa
			if ($sosa == 0) {
				foreach($indifacts as $key => $value) {
					$fact = $value->getTag();
					// -- handle special source fact case
					if ($fact == "SOUR") {
						print_main_sources($value->getGedComRecord(), 1, $famid, $value->getLineNumber());
					}
					// -- handle special note fact case
					else if ($fact == "NOTE") {
						print_main_notes($value->getGedComRecord(), 1, $famid, $value->getLineNumber());
					}
				}
				// NOTE: Print the media
				print_main_media($famid);
			}
		}
		else {
			if ($sosa==0) {
				print "\n\t<span class=\"subheaders\">" . $pgv_lang["family_group_info"];
				if ($SHOW_ID_NUMBERS and $famid != "") print "&nbsp;&nbsp;&nbsp;($famid)";
				print "</span><br />\n\t";
			}
			print "<table class=\"facts_table\">";
			if ($sosa == 0) {
				print "<tr><td class=\"messagebox\" colspan=\"2\">";
				print $pgv_lang["no_family_facts"];
				print "</td></tr>\n";
			}
		}
		// -- new fact link
		if ($view!="preview" && $sosa==0 && PGV_USER_CAN_EDIT) {
			print_add_new_fact($famid, $indifacts, "FAM");
			
			// -- new note
			print "<tr><td class=\"descriptionbox\">";
			print_help_link("add_note_help", "qm" ,"add_note_lbl");
			print $pgv_lang["add_note_lbl"] . "</td>";
			print "<td class=\"optionbox\">";
			print "<a href=\"javascript:;\" onclick=\"return add_new_record('$famid','NOTE');\">" . $pgv_lang["add_note"] . "</a>";
			print "<br />\n";
			print "</td></tr>\n";
			
			// -- new shared note
			print "<tr><td class=\"descriptionbox\">";
			print_help_link("add_shared_note_help", "qm" ,"add_shared_note_lbl");
			print $pgv_lang["add_shared_note_lbl"] . "</td>";
			print "<td class=\"optionbox\">";
			print "<a href=\"javascript:;\" onclick=\"return add_new_record('$famid','SHARED_NOTE');\">" . $pgv_lang["add_shared_note"] . "</a>";
			print "<br />\n";
			print "</td></tr>\n";
			
			// -- new media
			print "<tr><td class=\"descriptionbox\">";
			print_help_link("add_media_help", "qm", "add_media_lbl");
			print $pgv_lang["add_media_lbl"] . "</td>";
			print "<td class=\"optionbox\">";
			print "<a href=\"javascript: ".$pgv_lang["add_media_lbl"]."\" onclick=\"window.open('addmedia.php?action=showmediaform&linktoid={$famid}', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;\">".$pgv_lang["add_media"]."</a>";
			print "<br />\n";
			print "<a href=\"javascript:;\" onclick=\"window.open('inverselink.php?linktoid={$famid}&linkto=family', '_blank', 'top=50,left=50,width=400,height=300,resizable=1,scrollbars=1'); return false;\">".$pgv_lang["link_to_existing_media"]."</a>";
			print "</td></tr>\n";
			
			// -- new source citation
			print "<tr><td class=\"descriptionbox\">";
			print_help_link("add_source_help", "qm", "add_source_lbl");
			print $pgv_lang["add_source_lbl"] . "</td>";
			print "<td class=\"optionbox\">";
			print "<a href=\"javascript:;\" onclick=\"return add_new_record('$famid','SOUR');\">" . $pgv_lang["add_source"] . "</a>";
			print "<br />\n";
			print "</td></tr>\n";
			// -- end new objects
		}
		print "\n\t</table>\n";
	}
}

/**
 * print a family with Sosa-Stradonitz numbering system
 * ($rootid=1, father=2, mother=3 ...)
 *
 * @param string $famid family gedcom ID
 * @param string $childid tree root ID
 * @param string $sosa starting sosa number
 * @param string $label optional indi label (descendancy booklet)
 * @param string $parid optional parent ID (descendancy booklet)
 * @param string $gparid optional gd-parent ID (descendancy booklet)
 */
function print_sosa_family($famid, $childid, $sosa, $label="", $parid="", $gparid="", $personcount="1") {
	global $pgv_lang, $pbwidth, $pbheight, $view;

	if ($view != "preview") print "<hr />";
	print "\r\n\r\n<p style='page-break-before:always' />\r\n";
	if (!empty($famid)) print"<a name=\"{$famid}\"></a>\r\n";
	print_family_parents($famid, $sosa, $label, $parid, $gparid, $personcount);
	$personcount++;
	print "\n\t<br />\n";
	print "<table width=\"95%\"><tr><td valign=\"top\" style=\"width: " . ($pbwidth) . "px;\">\n";
	print_family_children($famid, $childid, $sosa, $label, $personcount);
	print "</td><td valign=\"top\">";
	if ($sosa == 0) print_family_facts(Family::getInstance($famid), $sosa);
	print "</td></tr></table>\n";
	print "<br />";
}
/**
 * check root id for pedigree tree
 *
 * @param string $rootid root ID
 * @return string $rootid validated root ID
 */
function check_rootid( $rootid ) {
	global $PEDIGREE_ROOT_ID, $USE_RIN, $gGedcom;
	// -- if the $rootid is not already there then find the first person in the file and make him the root
	if ( !find_person_record( $rootid ) ) {
		if ( find_person_record( $gGedcom->mRootId ) ) {
			$rootid = $gGedcom->mRootId;
		} else {
			// TODO - Add options for user gedcom id's'
			if ( find_person_record( $gGedcom->mRootId ) ) {
				$rootid = $gGedcom->mRootId;
			} else {
				if ( find_person_record( trim($PEDIGREE_ROOT_ID) ) ) {
					$rootid = trim($PEDIGREE_ROOT_ID);
				} else {
					$rootid = get_first_xref( 'INDI', $gGedcom->mGEDCOMId );
					// If there are no users in the gedcom, do something.
					if ( !$rootid ) {
						$rootid='I1';
					}
				}
			}
		}
	}

	if ($USE_RIN) {
		$indirec = find_person_record( $rootid );
		if ($indirec == false) $rootid = find_rin_id( $rootid );
	}

	return $rootid;
}

/**
 * creates an array with all of the individual ids to be displayed on an ascendancy chart
 *
 * the id in position 1 is the root person.  The other positions are filled according to the following algorithm
 * if an individual is at position $i then individual $i's father will occupy position ($i*2) and $i's mother
 * will occupy ($i*2)+1
 *
 * @param string $rootid
 * @return array $treeid
 */
function ancestry_array($rootid, $maxgen=0) {
	global $PEDIGREE_GENERATIONS, $SHOW_EMPTY_BOXES;
	// -- maximum size of the id array
	if ($maxgen==0) $maxgen = $PEDIGREE_GENERATIONS;
	$treesize = pow(2, ($maxgen));

	$treeid = array();
	$treeid[0] = "";
	$treeid[1] = $rootid;
	// -- fill in the id array
	for($i = 1; $i < ($treesize / 2); $i++) {
		$treeid[($i * 2)] = false; // -- father
		$treeid[($i * 2) + 1] = false; // -- mother
		if (!empty($treeid[$i])) {
			$person = Person::getInstance($treeid[$i]);
			$family = $person->getPrimaryChildFamily();
			// Store the prefered parents
			if (!empty($family)) {
				$treeid[($i * 2)] = $family->getHusbId();
				$treeid[($i * 2) + 1] = $family->getWifeId();
			}
		}
	}
	return $treeid;
}

/**
 * print an arrow to a new url
 *
 * @param string $id Id used for arrow img name (must be unique on the page)
 * @param string $url target url
 * @param string $label arrow label
 * @param string $dir arrow direction 0=left 1=right 2=up 3=down (default=2)
 */
function print_url_arrow($id, $url, $label, $dir=2) {
	global $pgv_lang, $view;
	global $PGV_IMAGE_DIR, $PGV_IMAGES;
	global $TEXT_DIRECTION;

	if ($id=="" or $url=="") return;
	if ($view=="preview") return;

	// arrow direction
	$adir=$dir;
	if ($TEXT_DIRECTION=="rtl" and $dir==0) $adir=1;
	if ($TEXT_DIRECTION=="rtl" and $dir==1) $adir=0;

	// arrow style		0		  1 		2		  3
	$array_style=array("larrow", "rarrow", "uarrow", "darrow");
	$astyle=$array_style[$adir];

	print "<a href=\"$url\" onmouseover=\"swap_image('".$astyle.$id."',$adir); window.status ='" . $label . "'; return true; \" onmouseout=\"swap_image('".$astyle.$id."',$adir); window.status=''; return true; \"><img id=\"".$astyle.$id."\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$astyle]["other"]."\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"$label\" title=\"$label\" /></a>";
}

/**
 * builds and returns sosa relationship name in the active language
 *
 * @param string $sosa sosa number
 */
function get_sosa_name($sosa) {
	global $LANGUAGE, $pgv_lang;

	if ($sosa<2) return "";
	$sosaname = "";
	$sosanr = floor($sosa/2);
	$gen = floor(log($sosanr) / log(2));

    // first try a generic algorithm, this is later overridden
	// by language specific algorithms.
	if (!empty($pgv_lang["sosa_$sosa"])) $sosaname = $pgv_lang["sosa_$sosa"];
	else if($gen > 2) {
		switch ($LANGUAGE) {
		case "danish":
		case "norwegian":
		case "swedish":
			$sosaname = "";
			$addname = "";
			$father = UTF8_strtolower($pgv_lang["father"]);
			$mother = UTF8_strtolower($pgv_lang["mother"]);
			$grand = "be".($LANGUAGE == "danish"?"dste":"ste");
			$great = "olde";
			$tip = "tip".($LANGUAGE == "danish"?"-":"p-");
			for($i = $gen; $i > 2; $i--) {
				$sosaname .= $tip;
			}
			if ($gen >= 2) $sosaname .= $great;
			if ($gen == 1) $sosaname .= $grand;

			for ($i=$gen; $i>0; $i--){
				if (!(floor($sosa/(pow(2,$i)))%2)) $addname .= $father;
				else $addname .= $mother;
				if (($gen%2 && !($i%2)) || (!($gen%2) && $i%2)) $addname .= "s ";
			}
			if ($LANGUAGE == "swedish") $sosaname = $addname;
			if ($sosa%2==0){
				$sosaname .= $father;
				if ($gen>0) $addname .= $father;
			} else {
				$sosaname .= $mother;
				if ($gen>0) $addname .= $mother;
			}
			$sosaname = UTF8_str_split($sosaname);
			$sosaname[0] = UTF8_strtoupper($sosaname[0]);
			$sosaname = implode('', $sosaname);
			if ($LANGUAGE != "swedish") if (!empty($addname)) $sosaname .= ($gen>5?"<br />&nbsp;&nbsp;&nbsp;&nbsp;":"")." <small>(".$addname.")</small>";
			break;

		case "dutch":
			// reference: http://nl.wikipedia.org/wiki/Voorouder
			// Our numbers are 2 less than those shown in the article.  We number parents
			// as generation zero where the article numbers them as generation 2.
		    $sosaname = "";
		    // Please leave the following strings untranslated
		    if ($gen & 512) break;					// 512 or higher
			if ($gen & 256) $sosaname .= "hoog";	// 256 to 511
			if ($gen & 128) $sosaname .= "opper";	// 128 to 511
			if ($gen & 64) $sosaname .= "aarts";	// 64 to 511
			if ($gen & 32) $sosaname .= "voor";		// 32 to 511
			if ($gen & 16) $sosaname .= "edel";		// 16 to 511
			if ($gen & 8) $sosaname .= "stam";		// 8 to 511
			if ($gen & 4) $sosaname .= "oud";		// 4 to 511
			$gen = $gen & 3;
			if ($gen == 3) $sosaname .= "betovergroot";
			if ($gen == 2) $sosaname .= "overgroot";
			if ($gen == 1) $sosaname .= "groot";
			if ($sosa%2==0) $sosaname .= $pgv_lang["father"];
			else $sosaname .= $pgv_lang["mother"];
			$sosaname = strtolower($sosaname);
			break;

		case "finnish":
		    $sosaname = "";
			$father = UTF8_strtolower($pgv_lang["father"]);
			$mother = UTF8_strtolower($pgv_lang["mother"]);
	//		$father = "isä";
	//		$mother = "äiti";
	//		$pgv_lang["sosa_2"]= "äidin";	//Grand (mother)
			for ($i=$gen; $i>0; $i--){
				if (!(floor($sosa/(pow(2,$i)))%2)) $sosaname .= $father."n";
	//			else $sosaname .= $pgv_lang["sosa_2"];
				else $sosaname .= UTF8_substr($mother, 0,2)."din";
			}
			if ($sosa%2==0) $sosaname .= $father;
			else $sosaname .= $mother;
			$sosaname = UTF8_str_split($sosaname);
			$sosaname[0] = UTF8_strtoupper($sosaname[0]);
			$sosaname = implode('',$sosaname);
			break;

		case "hebrew":
		    $sosaname = "";
			$addname = "";
			$father = $pgv_lang["father"];
			$mother = $pgv_lang["mother"];
			$greatf = $pgv_lang["sosa_22"];
			$greatm = $pgv_lang["sosa_21"];
			$of = $pgv_lang["sosa_23"];
			$grandfather = $pgv_lang["sosa_4"];
			$grandmother = $pgv_lang["sosa_5"];
	//		$father = "Aba";
	//		$mother = "Ima";
	//		$grandfather = "Saba";
	//		$grandmother = "Savta";
	//		$greatf = " raba";
	//		$greatm = " rabta";
	//		$of = " shel ";
			for ($i=$gen; $i>=0; $i--) {
				if ($i==0) {
					if (!($sosa%2)) $addname .= "f";
					else $addname .= "m";
				}
				else if (!(floor($sosa/(pow(2,$i)))%2)) $addname .= "f";
				else $addname .= "m";
				if ($i==0 || strlen($addname)==3) {
					if (strlen($addname)==3) {
						if (substr($addname, 2,1)=="f") $addname = $grandfather.$greatf;
						else $addname = $grandmother.$greatm;
					}
					else if (strlen($addname)==2) {
						if (substr($addname, 1,1)=="f") $addname = $grandfather;
						else $addname = $grandmother;
					}
					else {
						if ($addname=="f") $addname = $father;
						else $addname = $mother;
					}
					$sosaname = $addname.($i<$gen-2?$of:"").$sosaname;
					$addname="";
				}
			}
			break;

		default:
			$paternal = (floor($sosa/pow(2,$gen)) == 2) ? "paternal" : "maternal";
			$male = ($sosa%2==0) ? "male" : "female";
			if (!empty($pgv_lang["sosa_{$paternal}_{$male}_n_generations"])) {
				$sosaname = sprintf($pgv_lang["sosa_{$paternal}_{$male}_n_generations"], $gen+1, $gen, $gen-1);
			}
	    }
	}

	if (!empty($sosaname)) return "$sosaname<!-- sosa=$sosa nr=$sosanr gen=$gen -->";
	else return  "<!-- sosa=$sosa nr=$sosanr gen=$gen -->";
}


/**
 * find last family ID where this person is a spouse
 *
 * @param string $pid individual ID
 * @return string last sfam ID
 */
function find_last_sfam($pid) {
	$famids = find_sfamily_ids($pid);
	$f = count($famids);
	if ($f<1) return false;
	else return $famids[$f-1];
}

/**
 * find last spouse for this person
 *
 * @param string $pid individual ID
 * @return string last spouse ID
 */
function find_last_spouse($pid) {
	$famid = find_last_sfam($pid);
	if (!$famid) return false;
	$parents = find_parents($famid);
	if (!$parents) return false;
	if ($parents["HUSB"] == $pid) return $parents["WIFE"];
	else return $parents["HUSB"];
}

/**
 * print cousins list
 *
 * @param string $famid family ID
 */
function print_cousins($famid, $personcount="1") {
	global $show_full, $bheight, $bwidth;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $pgv_lang, $TEXT_DIRECTION;

	$family=Family::getInstance($famid);
	$fchildren=$family->getChildrenIds();

	$kids = count($fchildren);
	$save_show_full = $show_full;
	if ($save_show_full) {
		$bheight/=4;
		$bwidth-=40;
	}
	$show_full = false;
	print "<td valign=\"middle\" height=\"100%\">";
	if ($kids) {
		print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" ><tr valign=\"middle\">";
		if ($kids>1) print "<td rowspan=\"".$kids."\" valign=\"middle\" align=\"right\"><img width=\"3px\" height=\"". (($bheight+5) * ($kids-1)) ."px\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" alt=\"\" /></td>";
		$ctkids = count($fchildren);
		$i = 1;
		foreach ($fchildren as $indexval => $fchil) {
			print "<td><img width=\"10px\" height=\"3px\" style=\"padding-";
			if ($TEXT_DIRECTION=="ltr") print "right";
			else print "left";
			print ": 2px;\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" alt=\"\" /></td><td>";
			print_pedigree_person($fchil, 1 , false, 0, $personcount);
			$personcount++;
			print "</td></tr>";
			if ($i < $ctkids) {
				print "<tr>";
				$i++;
			}
		}
		print "</table>";
	}
	else {
		$famrec = find_family_record($famid);
		$ct = preg_match("/1 NCHI (\w+)/", $famrec, $match);
		if ($ct>0) $nchi = $match[1];
		else $nchi = "";
		if ($nchi=="0") print "&nbsp;<img src=\"images/small/childless.gif\" alt=\"".$pgv_lang["childless_family"]."\" title=\"".$pgv_lang["childless_family"]."\" />";
	}
	$show_full = $save_show_full;
	if ($save_show_full) {
		$bheight*=4;
		$bwidth+=40;
	}
	print "</td>\n";
}
?>

<?php
/**
 * Display an family book chart
 *
 * Set the root person using the $pid variable
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @version $Id: familybook.php,v 1.3 2006/10/02 22:47:24 lsces Exp $
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/functions_charts.php");
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

function print_descendency($pid, $count) {
	global $show_spouse, $dgenerations, $bwidth, $bheight, $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $generations, $box_width, $view, $show_full, $pgv_lang;
	if ($count>=$dgenerations) return 0;
	print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	print "<tr>";
	print "<td width=\"$bwidth\">\n";
	$numkids = 0;
	$famids = find_sfamily_ids($pid);
	if (count($famids)>0) {
		$firstkids = 0;
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			if ($ct>0) {
			print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
			for($i=0; $i<$ct; $i++) {
				$rowspan = 2;
				if (($i>0)&&($i<$ct-1)) $rowspan=1;
				$chil = trim($match[$i][1]);
				print "<tr><td rowspan=\"$rowspan\" width=\"$bwidth\" style=\"padding-top: 2px;\">\n";
				if ($count+1 < $dgenerations) {
					$kids = print_descendency($chil, $count+1);
					if ($i==0) $firstkids = $kids;
					$numkids += $kids;
				}
				else {
					print_pedigree_person($chil);
					$numkids++;
				}
				print "</td>\n";
				$twidth = 7;
				if ($ct==1) $twidth+=3;
				print "<td rowspan=\"$rowspan\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"$twidth\" height=\"3\" alt=\"\" /></td>\n";
				if ($ct>1) {
					if ($i==0) {
						print "<td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
						print "<tr><td height=\"50%\" style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
					else if ($i==$ct-1) {
						print "<td height=\"50%\" style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
						print "<tr><td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
					else {
						print "<td style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
				}
				print "</tr>\n";
			}
			print "</table>\n";
			}
		}
		print "</td>\n";
		print "<td width=\"$bwidth\">\n";
	}
	// NOTE: If statement OK
	if ($numkids==0) {
		$numkids = 1;
		$tbwidth = $bwidth+16;
		for($j=$count; $j<$dgenerations; $j++) {
			print "<div style=\"width: ".($tbwidth)."px;\"><br /></div>\n</td>\n<td width=\"$bwidth\">\n";
		}
	}
	//-- add offset divs to make things line up better
	if ($show_spouse) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			if (!empty($famrec)) {
				$marrec = get_sub_record(1, "1 MARR", $famrec);
				if (!empty($marrec)) {
					print "<br />";
				}
				print "<div style=\"height: ".$bheight."px; width: ".$bwidth."px;\"><br /></div>\n";
			}
		}
	}
	print_pedigree_person($pid);
	// NOTE: If statement OK
	if ($show_spouse) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			if (!empty($famrec)) {
				$parents = find_parents_in_record($famrec);
				$marrec = get_sub_record(1, "1 MARR", $famrec);
				if (!empty($marrec)) {
					print "<br />";
					print_simple_fact($famrec, "1 MARR", $famid);
				}
				if ($parents["HUSB"]!=$pid) print_pedigree_person($parents["HUSB"]);
				else print_pedigree_person($parents["WIFE"]);
			}
		}
	}
	// NOTE: If statement OK
	if ($count==0) {
		$indirec = find_person_record($pid);
		// NOTE: If statement OK
		if (displayDetails($indirec) || showLivingName($indirec)) {
			// -- print left arrow for decendants so that we can move down the tree
			$famids = find_sfamily_ids($pid);
			//-- make sure there is more than 1 child in the family with parents
			$cfamids = find_family_ids($pid);
			$num=0;
			// NOTE: For statement OK
			for($f=0; $f<count($cfamids); $f++) {
				$famrec = find_family_record($cfamids[$f]);
				if ($famrec) {
					$num += preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
				}
			}
			// NOTE: If statement OK
			if ($famids||($num>1)) {
				print "\n\t\t<div id=\"childarrow.$pid\" dir=\"";
				if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; ";
				else print "ltr\" style=\"position:absolute; ";
				print "width:10px; height:10px; \">";
				if ($view!="preview") {
					print "<a href=\"javascript: ".$pgv_lang["show"]."\" onclick=\"return togglechildrenbox('$pid');\" onmouseover=\"swap_image('larrow.$pid',3);\" onmouseout=\"swap_image('larrow.$pid',3);\">";
					print "<img id=\"larrow.$pid\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow"]["other"]."\" border=\"0\" alt=\"\" />";
					print "</a>";
				}
				print "\n\t\t<div id=\"childbox.$pid\" dir=\"";
				if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; right: 20px; ";
				else print "ltr\" style=\"position:absolute; left: 20px;";
				print " width:".$bwidth."px; height:".$bheight."px; visibility: hidden;\">";
				print "\n\t\t\t<table class=\"person_box\"><tr><td>";
				for($f=0; $f<count($famids); $f++) {
					$famrec = find_family_record(trim($famids[$f]));
					if ($famrec) {
						$parents = find_parents($famids[$f]);
						if($parents) {
							if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
							else $spid=$parents["WIFE"];
							if (!empty($spid)) {
								if (displayDetailsById($spid) || showLivingNameById($spid)) {
									$name = get_person_name($spid);
									$name = rtrim($name);
								} else $name = $pgv_lang["private"];
								print "\n\t\t\t\t<a href=\"familybook.php?pid=$spid&amp;show_spouse=$show_spouse&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width\"><span class=\"";
								if (hasRTLText($name)) print "name2";
					   			else print "name1";
					   			print "\">";
								print PrintReady($name);
								print "<br /></span></a>";
							}
						}
						$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
						for($i=0; $i<$num; $i++) {
							//-- add the following line to stop a bad PHP bug
							if ($i>=$num) break;
							$cid = $smatch[$i][1];
							if (displayDetailsById($cid) || showLivingNameById($cid)) {
								$name = get_person_name($cid);
								$name = rtrim($name);
							} else $name = $pgv_lang["private"];
							print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"familybook.php?pid=$cid&amp;show_spouse=$show_spouse&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width\"><span class=\"";
							if (hasRTLText($name)) print "name2";
					   		else print "name1";
					   		print "\">&lt; ";
							print PrintReady($name);
							print "<br /></span></a>";
						}
					}
				}
				//-- print the siblings
				for($f=0; $f<count($cfamids); $f++) {
					$famrec = find_family_record($cfamids[$f]);
					if ($famrec) {
						$parents = find_parents($cfamids[$f]);
						if($parents) {
							print "<span class=\"name1\"><br />".$pgv_lang["parents"]."<br /></span>";
							if (!empty($parents["HUSB"])) {
								$spid = $parents["HUSB"];
								if (displayDetailsById($spid) || showLivingNameById($spid)) {
									$name = get_person_name($spid);
									$name = rtrim($name);
								} else $name = $pgv_lang["private"];
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"familybook.php?pid=$spid&amp;show_spouse=$show_spouse&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width\"><span class=\"";
								if (hasRTLText($name)) print "name2";
					   			else print "name1";
					   			print "\">";
								print PrintReady($name);
								print "<br /></span></a>";
							}
							if (!empty($parents["WIFE"])) {
								$spid = $parents["WIFE"];
								if (displayDetailsById($spid) || showLivingNameById($spid)) {
									$name = get_person_name($spid);
									$name = rtrim($name);
								} else $name = $pgv_lang["private"];
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"familybook.php?pid=$spid&amp;show_spouse=$show_spouse&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width\"><span class=\"";
								if (hasRTLText($name)) print "name2";
					   			else print "name1";
					   			print "\">";
								print PrintReady($name);
								print "<br /></span></a>";
							}
						}
						$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
						if ($num>1) print "<span class=\"name1\"><br />".$pgv_lang["siblings"]."<br /></span>";
						for($i=0; $i<$num; $i++) {
							//-- add the following line to stop a bad PHP bug
							if ($i>=$num) break;
							$cid = $smatch[$i][1];
							if ($cid!=$pid) {
								if (displayDetailsById($cid) || showLivingNameById($cid)) {
									$name = get_person_name($cid);
									$name = rtrim($name);
								} else $name = $pgv_lang["private"];
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"familybook.php?pid=$cid&amp;show_spouse=$show_spouse&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width\"><span class=\"";
								if (hasRTLText($name)) print "name2";
					   			else print "name1";
					   			print "\"> ";
								print PrintReady($name);
								print "<br /></span></a>";
							}
						}
					}
				}
				print "\n\t\t\t</td></tr></table>";
				print "\n\t\t</div>";
				print "\n\t\t</div>";
			}
		}
	}
	print "</td></tr>\n";
	print "</table>\n";
	return $numkids;
}

function max_descendency_generations($pid, $depth) {
	global $generations;
	if ($depth >= $generations) return $depth;
	$famids = find_sfamily_ids($pid);
	$maxdc = $depth;
	foreach($famids as $indexval => $famid) {
		$famrec = find_family_record($famid);
		$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$chil = trim($match[$i][1]);
			$dc = max_descendency_generations($chil, $depth+1);
			if ($dc >= $generations) return $dc;
			if ($dc > $maxdc) $maxdc = $dc;
		}
	}
	if ($maxdc==0) $maxdc++;
	return $maxdc;
}

function print_person_pedigree($pid, $count) {
	global $generations, $SHOW_EMPTY_BOXES, $PGV_IMAGE_DIR, $PGV_IMAGES, $bheight;
	if ($count>=$generations) return;
	$famids = find_family_ids($pid);
	foreach($famids as $indexval => $famid) {
		print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">\n";
		$parents = find_parents($famid);
		$height="100%";
		print "<tr>";
		if ($count<$generations-1) print "<td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
		if ($count<$generations-1) print "<td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>\n";
		print "<td rowspan=\"2\">\n";
		print_pedigree_person($parents["HUSB"]);
		print "</td>\n";
		print "<td rowspan=\"2\">\n";
		print_person_pedigree($parents["HUSB"], $count+1);
		print "</td>\n";
		print "</tr>\n<tr>\n<td height=\"50%\"";
		if ($count<$generations-1) print " style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\" ";
		print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n<tr>\n";
		if ($count<$generations-1) print "<td height=\"50%\" style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>";
		if ($count<$generations-1) print "<td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>\n";
		print "<td rowspan=\"2\">\n";
		print_pedigree_person($parents["WIFE"]);
		print "</td>\n";
		print "<td rowspan=\"2\">\n";
		print_person_pedigree($parents["WIFE"], $count+1);
		print "</td>\n";
		print "</tr>\n";
		if ($count<$generations-1) print "<tr>\n<td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
		print "</table>\n";
	}
}
function print_family_book($pid, $descent)
{
    global $generations, $dgenerations, $pgv_lang, $firstrun;
	if ($descent==0) return;
    $famids = find_sfamily_ids($pid);
    if (count($famids)>0 || empty($firstrun)) {
    	$firstrun = true;
        $pid=check_rootid($pid);
        if ((DisplayDetailsByID($pid))||(showLivingNameByID($pid))) $name = get_person_name($pid);
        else $name = $pgv_lang["private"];
        
        print "\n\t<h2 style=\"text-align: center\">".$pgv_lang["family_of"].PrintReady($name)."</h2>";
        print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr>\n";
        
        //-- descendancy
        print "<td valign=\"middle\">\n";
        $dgenerations = $generations;
        $dgenerations = max_descendency_generations($pid, 0);
        print_descendency($pid, 0);
        print "</td>\n";
        //-- pedigree
        print "<td valign=\"middle\">\n";
        print_person_pedigree($pid, 0);
        print "</td>\n";
        print "</tr></table>\n";
        print "<br /><br />\n";
        print "<hr style=\"page-break-after:always;\"/>\n";
        print "<br /><br />\n";
        
        foreach($famids as $indexval => $famid) {
            $famrec = find_family_record($famid);
            $ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
            for($i=0; $i<$ct; $i++) {
                $chil = trim($match[$i][1]);
                if (showLivingNameById($chil) || displayDetailsById($chil)) print_family_book($chil, $descent-1);
            }
        }
    }
    
}
// -- args
if (!isset($show_full)) $show_full=$PEDIGREE_FULL_DETAILS;
if (!isset($show_spouse)) $show_spouse=0;
if (empty($generations)) $generations = 1;
if (empty($descent)) $descent = 5;
if ($generations > $MAX_DESCENDANCY_GENERATIONS) $generations = $MAX_DESCENDANCY_GENERATIONS;
if (!isset($view)) $view="";

// -- size of the boxes
if (empty($box_width)) $box_width = "100";
$box_width=max($box_width, 50);
$box_width=min($box_width, 300);
if (!$show_full) $bwidth = $bwidth / 1.5;
$bwidth*=$box_width/100;
if ($show_full==false) {
	$bheight = $bheight / 2.5;
}

// -- root id
if (!isset($pid)) $pid="";
$pid=check_rootid($pid);
if ((DisplayDetailsByID($pid))||(showLivingNameByID($pid))) $name = get_person_name($pid);
else $name = $pgv_lang["private"];

// -- print html header information
print_header(PrintReady($name)." ".$pgv_lang["familybook_chart"]);
print "\n\t<table width=\"100%\" class=\"list_table $TEXT_DIRECTION\"><tr><td valign=\"top\">\n\t\t";
if ($view!="preview") print "\n\t<h2>".$pgv_lang["familybook_chart"].":<br />".PrintReady($name)."</h2>";
else print "\n\t<h2 style=\"text-align: center\">".$pgv_lang["familybook_chart"].":<br />".PrintReady($name)."</h2>";
?>

<script language="JavaScript" type="text/javascript">
<!--
	var pasteto;
	function open_find(textbox) {
		pasteto = textbox;
		findwin = window.open('find.php?type=indi', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	function paste_id(value) {
		pasteto.value=value;
	}
//-->
</script>

<?php
$gencount=0;
if ($view!="preview") {
	print "</td><td><form method=\"get\" name=\"people\" action=\"?\">\n";
	print "\n\t\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr>";
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["root_person"] . "&nbsp;</td>";
	print "<td class=\"list_value\">";
	print "\n\t\t<input class=\"pedigree_form\" type=\"text\" name=\"pid\" size=\"3\" value=\"$pid\" />";
	print "<font size=\"1\"> <a href=\"javascript:open_find(document.people.pid);\">".$pgv_lang["find_id"]."</a></font>";
	print_help_link("desc_rootid_help", "qm");
	print "</td>";
	print "<td rowspan=\"4\" class=\"list_value\">";
	print "\n\t\t<table class=\"$TEXT_DIRECTION\" style=\"width: 100%;\">\n\t\t<tr><td class=\"sublinks_cell\" style=\" vertical-align:top;\">";
	print "<input type=\"hidden\" name=\"show_full\" value=\"$show_full\" />";
	print "<input type=\"checkbox\" value=\"";
	if ($show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';\"";
	else print "0\" onclick=\"document.people.show_full.value='1';\"";
	print " />".$pgv_lang["show_details"];
	print_help_link("show_full_help", "qm");
	print "<br />\n";
	print "<input type=\"checkbox\" value=\"1\" name=\"show_spouse\"";
	if ($show_spouse) print " checked=\"checked\"";
	print " />".$pgv_lang["show_spouses"];
	print_help_link("show_spouse_help", "qm");
	print "<br /><br /><br /></td><td class=\"subheaders\" style=\"width: 60px; vertical-align:bottom;text-align: ".($TEXT_DIRECTION=="rtl"?"left":"right").";\">";
	print "<input type=\"submit\" value=\"".$pgv_lang["view"]."\" />";
	print "</td></tr></table>\n";
	print "</td>";
	print "</tr><tr>";
	print "<td class=\"list_label\" >&nbsp;" . $pgv_lang["generations"] . "&nbsp;</td>";
	print "<td class=\"list_value\">";
//	print "<input type=\"text\" size=\"3\" name=\"generations\" value=\"$generations\" />";
	print "<select name=\"generations\">";
	for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
		print "<option value=\"".$i."\"" ;
		if ($i == $generations) print "selected=\"selected\" ";
		print ">".$i."</option>";
	}
	print "</select>";
	print_help_link("desc_generations_help", "qm");
	print "</td>";
	print "</tr><tr>";
	print "<td class=\"list_label\">&nbsp;" . $pgv_lang["box_width"] . "&nbsp;</td>";
	print "<td class=\"list_value\"><input type=\"text\" size=\"3\" name=\"box_width\" value=\"$box_width\" /> <b>%</b>";
	print_help_link("box_width_help", "qm");
	print "</td>";
	print "</tr><tr>";
	print "<td class=\"list_label\" >&nbsp;" . $pgv_lang["descent_steps"] . "&nbsp;</td>";
	print "<td class=\"list_value\"><input type=\"text\" size=\"3\" name=\"descent\" value=\"$descent\" />";
	print_help_link("fambook_descent_help", "qm");
	print "</td>";
	print "</tr></table>";
	print "</form>\n";
}
print "</td></tr></table>\n";
print_family_book($pid, $descent);
print "<br /><br />\n";
print_footer();
?>

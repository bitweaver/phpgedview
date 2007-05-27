<?php
/**
 * Compact pedigree tree
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2006	John Finlay and Others
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

/**
 * Initialization
 */ 
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/functions_charts.php");

// -- args
if (!isset($rootid)) $rootid = "";
$rootid = clean_input($rootid);
$rootid = check_rootid($rootid);
if (!isset($showids)) $showids = 0;
if (!isset($showthumbs)) $showthumbs = 0;

$person = Person::getInstance($rootid);

if ($person->canDisplayName()) {
	$name = get_person_name($rootid);
	$addname = get_add_person_name($rootid);
}
else {
	$name = $pgv_lang["private"];
	$addname = "";
}
// -- print html header information
print_header(PrintReady($name) . " " . $pgv_lang["compact_chart"]);
if (strlen($name)<30) $cellwidth="420";
else $cellwidth=(strlen($name)*14);
print "\n\t<table class=\"list_table $TEXT_DIRECTION\"><tr><td width=\"${cellwidth}px\" valign=\"top\">\n\t\t";
print "<h2>" . $pgv_lang["compact_chart"] . ":";
print "<br />".PrintReady($name);
if ($addname != "") print "<br />" . PrintReady($addname);
print "</h2>";

// -- print the form
if ($view != "preview") {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
	</script>
	<?php
	print "\n\t</td><td><form name=\"people\" id=\"people\" method=\"get\" action=\"?\">";
	print "\n\t\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t";
	print "<tr>";

	// NOTE: Root ID
	print "<td class=\"descriptionbox\">";
	print_help_link("rootid_help", "qm");
	print $pgv_lang["root_person"]."&nbsp;</td>";
	print "<td class=\"optionbox vmiddle\">";
	print "<input class=\"pedigree_form\" type=\"text\" name=\"rootid\" id=\"rootid\" size=\"3\" value=\"$rootid\" />";
	print_findindi_link("rootid","");
	print "</td>";

	// NOTE: submit
	print "<td class=\"facts_label03\" rowspan=\"3\">";
	print "<input type=\"submit\" value=\"".$pgv_lang["view"]."\" />";
	print "</td>\n</tr>\n";

	if ($SHOW_ID_NUMBERS) {
		print "<tr>\n";
		print "<td class=\"descriptionbox\">";
		print_help_link("SHOW_ID_NUMBERS_help", "qm");
		print $pgv_lang["SHOW_ID_NUMBERS"];
		print "</td>\n";
		print "<td class=\"optionbox\">\n";
		print "<input name=\"showids\" type=\"checkbox\" value='".$showids."'";
		if ($showids) print " checked=\"checked\"";
		print " onclick=\"document.people.showids.value='".(!$showids)."';\" />";
		print "</td>\n</tr>\n";
	}

	if ($SHOW_HIGHLIGHT_IMAGES) {
		print "<tr>\n";
		print "<td class=\"descriptionbox\">";
		print_help_link("SHOW_HIGHLIGHT_IMAGES_help", "qm");
		print $pgv_lang["SHOW_HIGHLIGHT_IMAGES"];
		print "</td>\n";
		print "<td class=\"optionbox\">\n";
		print "<input name=\"showthumbs\" type=\"checkbox\" value='".$showthumbs."'";
		if ($showthumbs) print " checked=\"checked\"";
		print " onclick=\"document.people.showthumbs.value='".(!$showthumbs)."';\" />";
		print "</td>\n</tr>\n";
	}

	print "</table>";
	print "</form>\n";
}
print "</td></tr></table>";

// process the tree
$treeid = ancestry_array($rootid,4);
print "<br />";
print "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";

// 1
print "<tr>";
print_td_person(16);
print "<td></td>";
print "<td></td>";
print "<td></td>";
print_td_person(18);
print "<td></td>";
print_td_person(24);
print "<td></td>";
print "<td></td>";
print "<td></td>";
print_td_person(26);
print "</tr>";

// 2
print "<tr>";
print "<td style='text-align:center;'>"; print_arrow_person(16, "up"); print "</td>";
print "<td></td>";
print "<td></td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(18, "up"); print "</td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(24, "up"); print "</td>";
print "<td></td>";
print "<td></td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(26, "up"); print "</td>";
print "</tr>";

// 3
print "<tr>";
print_td_person(8);
print "<td style='text-align:center;'>"; print_arrow_person(8, "left"); print "</td>";
print_td_person(4);
print "<td style='text-align:center;'>"; print_arrow_person(9, "right"); print "</td>";
print_td_person(9);
print "<td></td>";
print_td_person(12);
print "<td style='text-align:center;'>"; print_arrow_person(12, "left"); print "</td>";
print_td_person(6);
print "<td style='text-align:center;'>"; print_arrow_person(13, "right"); print "</td>";
print_td_person(13);
print "</tr>";

// 4
print "<tr>";
print "<td style='text-align:center;'>"; print_arrow_person(17, "down"); print "</td>";
print "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(4, "up"); print "</td>";
print "<td style='text-align:center;'>"; print_arrow_person(19, "down"); print "</td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(25, "down"); print "</td>";
print "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(6, "up"); print "</td>";
print "<td style='text-align:center;'>"; print_arrow_person(27, "down"); print "</td>";
print "</tr>";

// 5
print "<tr>";
print_td_person(17);
print_td_person(19);
print "<td></td>";
print_td_person(25);
print_td_person(27);
print "</tr>";

// 6
print "<tr>";
print "<td></td>";
print "<td></td>";
print "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
print "<td></td>";
print "<td></td>";
print "</tr>";

// 7
print "<tr>";
print "<td></td>";
print "<td></td>";
print_td_person(2);
print "<td>";
print "</td>";

print "<td colspan='3'>";
print "<table width='100%'><tr>";
print "<td style='text-align:center;' width='25%'>"; print_arrow_person(2, "left"); print "</td>";
print_td_person(1);
print "<td style='text-align:center;' width='25%'>"; print_arrow_person(3, "right"); print "</td>";
print "</tr></table>";
print "</td>";

print "<td>";
print "</td>";
print_td_person(3);
print "<td></td>";
print "<td></td>";
print "</tr>";

// 8
print "<tr>";
print "<td>&nbsp;</td>";
print "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(5, "down"); print "</td>";
print "<td></td>";
print "<td></td>";
print "<td></td>";
print "<td rowspan='3' colspan='3' style='text-align:center;'>"; print_arrow_person(7, "down"); print "</td>";
print "<td></td>";
print "</tr>";

// 9
print "<tr>";
print_td_person(20);
print_td_person(22);
print "<td></td>";
print_td_person(28);
print_td_person(30);
print "</tr>";

// 10
print "<tr>";
print "<td style='text-align:center;'>"; print_arrow_person(20, "up"); print "</td>";
print "<td style='text-align:center;'>"; print_arrow_person(22, "up"); print "</td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(28, "up"); print "</td>";
print "<td style='text-align:center;'>"; print_arrow_person(30, "up"); print "</td>";
print "</tr>";

// 11
print "<tr>";
print_td_person(10);
print "<td style='text-align:center;'>"; print_arrow_person(10, "left"); print "</td>";
print_td_person(5);
print "<td style='text-align:center;'>"; print_arrow_person(11, "right"); print "</td>";
print_td_person(11);
print "<td></td>";
print_td_person(14);
print "<td style='text-align:center;'>"; print_arrow_person(14, "left"); print "</td>";
print_td_person(7);
print "<td style='text-align:center;'>"; print_arrow_person(15, "right"); print "</td>";
print_td_person(15);
print "</tr>";

// 12
print "<tr>";
print "<td style='text-align:center;'>"; print_arrow_person(21, "down"); print "</td>";
print "<td></td>";
print "<td></td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(23, "down"); print "</td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(29, "down"); print "</td>";
print "<td></td>";
print "<td></td>";
print "<td></td>";
print "<td style='text-align:center;'>"; print_arrow_person(31, "down"); print "</td>";
print "</tr>";

// 13
print "<tr>";
print_td_person(21);
print "<td></td>";
print "<td></td>";
print "<td></td>";
print_td_person(23);
print "<td></td>";
print_td_person(29);
print "<td></td>";
print "<td></td>";
print "<td></td>";
print_td_person(31);
print "</tr>";

print "</table>";
print "<br />";

print_footer();

function print_td_person($n) {
	global $treeid, $PGV_IMAGE_DIR, $PGV_IMAGES, $pgv_lang;
	global $TEXT_DIRECTION, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES;
	global $showids, $showthumbs;

	$text = "";
	$pid = $treeid[$n];

	if ($TEXT_DIRECTION=="ltr") {
		$title = $pgv_lang["indi_info"].": ".$pid;
	} else {
		$title = $pid." :".$pgv_lang["indi_info"];
	}

	if ($pid) {
		$indirec=find_person_record($pid);
		if (!$indirec) $indirec = find_updated_record($pid);

		if ((!displayDetailsByID($pid))&&(!showLivingNameByID($pid))) {
			$name = $pgv_lang["private"];
			$addname = "";
		}
		else {
			$name = get_person_name($pid);
			$addname = get_add_person_name($pid);
		}
		if (($showthumbs) && $MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES && showFact("OBJE", $pid)) {
			$object = find_highlighted_object($pid, $indirec);
			if (!empty($object["thumb"])) {
				$size = findImageSize($object["thumb"]);
				$class = "pedigree_image_portrait";
				if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
				if($TEXT_DIRECTION == "rtl") $class .= "_rtl";
				// NOTE: IMG ID
				$imgsize = findImageSize($object["file"]);
				$imgwidth = $imgsize[0]+50;
				$imgheight = $imgsize[1]+150;
				$text .= "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($object["file"])."',$imgwidth, $imgheight);\">";
				$text .= "<img id=\"box-$pid\" src=\"".$object["thumb"]."\"vspace=\"0\" hspace=\"0\" class=\"$class\" alt =\"\" title=\"\" ";
				if ($imgsize) $text .= " /></a>\n";
				else $text .= " />\n";
			}
		}
		$text .= "<a class=\"name1\" href=\"individual.php?pid=$pid\" title=\"$title\"> ";
		$text .= PrintReady($name);
		if ($addname) $text .= "<br />" . PrintReady($addname);
		$text .= "</a>";
		if ($showids) {
			$text .= " <span class='details1' ";
		   if ($TEXT_DIRECTION=="ltr") $text .= "dir=\"ltr\">";
  		   else $text .= "dir=\"rtl\">";
  		   $text .= "(".$pid.")</span>";
		}
		$text .= "<br />";
		if (displayDetailsByID($pid)) {
			$text .= "<span class='details1'>";
			$birthrec = get_sub_record(1, "1 BIRT", $indirec);
			$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $birthrec, $bmatch);
			if ($ct>0) $text .= trim($bmatch[1]);
			$deathrec = get_sub_record(1, "1 DEAT", $indirec);
			$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $deathrec, $dmatch);
			if ($ct>0) {
				$text .= "-".trim($dmatch[1]);
				$text .= "<br />".get_age($indirec, substr(get_sub_record(2, "2 DATE", $deathrec),7));
			} else $text .= "-<br />";
			$text .= "</span>";
		}
	}
	$text = unhtmlentities($text);
	// -- empty box
	if (empty($text)) $text = "&nbsp;<br />&nbsp;<br />";
	// -- box color
	$isF="";
	if ($n==1) {
		if (strpos($indirec, "1 SEX F")!==false) $isF="F";
	} else if ($n%2) $isF="F";
	// -- box size
	if ($n==1) print "<td";
	else print "<td width='15%'";
	// -- print box content
	print " class=\"person_box".$isF."\" style=\"text-align:center; vertical-align:top;\" >";
	print $text;
	print "</td>";
}

function print_arrow_person($n, $arrow_dir) {
	global $treeid;
	global $view, $showids, $showthumbs;
	global $pgv_lang, $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$pid = $treeid[$n];

	$arrow_swap = array("l"=>"0", "r"=>"1", "u"=>"2", "d"=>"3");

	$arrow_dir = substr($arrow_dir,0,1);
	if ($TEXT_DIRECTION=="rtl") {
		if ($arrow_dir=="l") $arrow_dir="r";
		else if ($arrow_dir=="r") $arrow_dir="l";
	}
	if ($TEXT_DIRECTION=="ltr") {
		$title = $pgv_lang["compact_chart"].": ".$pid;
	} else {
		$title = $pid." :".$pgv_lang["compact_chart"];
	}
	$arrow_img = "<img id='arrow$n' src='".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$arrow_dir."arrow"]["other"]."' border='0' align='middle' alt='$title' title='$title' />";
	$hideArrow = "<img id='arrow$n' src='".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$arrow_dir."arrow"]["other"]."' border='0' align='middle' alt='$title' title='$title' style='visibility:hidden;' />";

	$text = "";
	if ($pid) {
		$text .= "<a href=\"?rootid=".$pid;
		if ($showids) $text .="&amp;showids=".$showids;
		if ($showthumbs) $text .= "&amp;showthumbs=".$showthumbs;
		if ($view) $text .="&amp;view=".$view;
		$text .= "\" onmouseover=\"swap_image('arrow$n',".$arrow_swap[$arrow_dir].");\" onmouseout=\"swap_image('arrow$n',".$arrow_swap[$arrow_dir].");\" >";
		$text .= $arrow_img."</a>";
	}
	// -- arrow to empty box does not have a url attached.
	else $text = $hideArrow;
	print $text;
}
?>

<?php
/**
 * Function for printing
 *
 * Various printing functions used by all scripts and included by the functions.php file.
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
 * @subpackage Display
 * @version $Id: functions_print.php,v 1.10 2007/06/03 20:45:14 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once 'includes/functions_charts.php';

/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 * @param string $pid	the Gedcom Xref ID of the   to print
 * @param int $style	the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 * @param boolean $show_famlink	set to true to show the icons for the popup links and the zoomboxes
 * @param int $count	on some charts it is important to keep a count of how many boxes were printed
 */
function print_pedigree_person($pid, $style=1, $show_famlink=true, $count=0, $personcount="1") {
	 global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $PRIV_PUBLIC, $factarray, $ZOOM_BOXES, $LINK_ICONS, $view, $SCRIPT_NAME, $GEDCOM;
	 global $pgv_lang, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $show_full, $PEDIGREE_FULL_DETAILS, $SHOW_ID_NUMBERS, $SHOW_PEDIGREE_PLACES;
	 global $CONTACT_EMAIL, $CONTACT_METHOD, $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	 global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	 global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE;
	 global $SEARCH_SPIDER;

	 if ($style != 2) $style=1;

	 flush();
	 if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	 if (!isset($talloffset)) $talloffset = $gBitSystem->getConfig('pgv_pedigree_layout', 1);
	 if (!isset($show_full)) $show_full = $gBitSystem->getConfig('pgv_pedigree_full_details', 1);
	 // NOTE: Start div out-rand()
	 if ($pid==false) {
		  print "\n\t\t\t<div id=\"out-".rand()."\" class=\"person_boxNN\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\">";
		  print "<br />";
		  print "\n\t\t\t</div>";
		  return false;
	 }
	 if ($count==0) $count = rand();
	 $lbwidth = $bwidth*.75;
	 if ($lbwidth < 150) $lbwidth = 150;
	 $indirec=find_person_record($pid);
	 if (!$indirec) $indirec = find_updated_record($pid);
	 $isF = "NN";
	 if (preg_match("/1 SEX F/", $indirec)>0) $isF="F";
	 else if (preg_match("/1 SEX M/", $indirec)>0) $isF="";
	 $disp = displayDetailsByID($pid, "INDI");
	 $boxID = $pid.".".$personcount.".".$count;
	 $mouseAction1 = "onmouseover=\"clear_family_box_timeout('".$boxID."');\" onmouseout=\"family_box_timeout('".$boxID."');\"";
	 $mouseAction2 = " onmouseover=\"expandbox('".$boxID."', $style); return false;\" onmouseout=\"restorebox('".$boxID."', $style); return false;\"";
	 $mouseAction3 = " onmousedown=\"expandbox('".$boxID."', $style); return false;\" onmouseup=\"restorebox('".$boxID."', $style); return false;\"";
	 $mouseAction4 = " onclick=\"expandbox('".$boxID."', $style); return false;\"";
	 if ($disp || showLivingNameByID($pid)) {
		  if ($show_famlink && (empty($SEARCH_SPIDER))) {
			   if ($LINK_ICONS!="disabled") {
					//-- draw a box for the family popup
					// NOTE: Start div I.$pid.$personcount.$count.links
					print "\n\t\t<div id=\"I".$boxID."links\" style=\"position:absolute; ";
					print "left: 0px; top:0px; width: ".($lbwidth)."px; visibility:hidden; z-index:'100';\">";
					print "\n\t\t\t<table class=\"person_box$isF\"><tr><td class=\"details1\">";
					// NOTE: Zoom
	 				if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["pedigree_chart"].": ".$pid;
	 				else $title = $pid." :".$pgv_lang["pedigree_chart"];
					print "<a href=\"pedigree.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;talloffset=$talloffset&amp;ged=$GEDCOM\" title=\"$title\" $mouseAction1><b>".$pgv_lang["index_header"]."</b></a>\n";

	 				if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["descend_chart"].": ".$pid;
	 				else $title = $pid." :".$pgv_lang["descend_chart"];
					print "<br /><a href=\"descendancy.php?pid=$pid&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM\" title=\"$title\" $mouseAction1><b>".$pgv_lang["descend_chart"]."</b></a><br />\n";

					$username = getUserName();
					if (!empty($username)) {
						 $tuser=getUser($username);
						 if (!empty($tuser["gedcomid"][$GEDCOM])) {
	 						  if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["relationship_chart"].": ".$pid;
	 						  else $title = $pid." :".$pgv_lang["relationship_chart"];
							  print "<a href=\"relationship.php?pid1=".$tuser["gedcomid"][$GEDCOM]."&amp;pid2=".$pid."&amp;ged=$GEDCOM\" title=\"$title\" ".$mouseAction1."><b>".$pgv_lang["relationship_to_me"]."</b></a><br />\n";
						 }
					}
					// NOTE: Zoom
					if (file_exists("ancestry.php")) {
	 					if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["ancestry_chart"].": ".$pid;
	 					else $title = $pid." :".$pgv_lang["ancestry_chart"];
						print "<a href=\"ancestry.php?rootid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM\" title=\"$title\" ".$mouseAction1."><b>".$pgv_lang["ancestry_chart"]."</b></a><br />\n";
					}
					if (file_exists("compact.php")) {
	 					if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["compact_chart"].": ".$pid;
	 					else $title = $pid." :".$pgv_lang["compact_chart"];
						print "<a href=\"compact.php?rootid=$pid&amp;ged=$GEDCOM\" title=\"$title\" ".$mouseAction1."><b>".$pgv_lang["compact_chart"]."</b></a><br />\n";
					}
					if (file_exists("fanchart.php") and defined("IMG_ARC_PIE") and function_exists("imagettftext")) {
	 					if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["fan_chart"].": ".$pid;
	 					else $title = $pid." :".$pgv_lang["fan_chart"];
						print "<a href=\"fanchart.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;ged=$GEDCOM\" title=\"$title\" ".$mouseAction1."><b>".$pgv_lang["fan_chart"]."</b></a><br />\n";
					}
					if (file_exists("hourglass.php")) {
	 					if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["hourglass_chart"].": ".$pid;
	 					else $title = $pid." :".$pgv_lang["hourglass_chart"];
						print "<a href=\"hourglass.php?pid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM&amp;show_spouse=$show_spouse&amp;show_full=$show_full\" title=\"$title\" ".$mouseAction1."><b>".$pgv_lang["hourglass_chart"]."</b></a><br />\n";
					}
					$ct = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $match, PREG_SET_ORDER);
					for ($i=0; $i<$ct; $i++) {
						 $famid = $match[$i][1];
						 $famrec = find_family_record($famid);
						 if ($famrec) {
							  $parents = find_parents_in_record($famrec);
							  $spouse = "";
							  if ($pid==$parents["HUSB"]) $spouse = $parents["WIFE"];
							  if ($pid==$parents["WIFE"]) $spouse=$parents["HUSB"];
							  $num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
							  if ((!empty($spouse))||($num>0)) {
	 							 if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["familybook_chart"].": ".$famid;
	 							 else $title = $famid." :".$pgv_lang["familybook_chart"];
								 print "<a href=\"family.php?famid=$famid&amp;ged=$GEDCOM\" title=\"$title\" ".$mouseAction1."><b>".$pgv_lang["fam_spouse"]."</b></a><br /> \n";
								 if (!empty($spouse)) {
	 								if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["indi_info"].": ".$spouse;
	 								else $title = $spouse." :".$pgv_lang["indi_info"];
									print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\" title=\"$title\" $mouseAction1>";
 									if (($SHOW_LIVING_NAMES>=$PRIV_PUBLIC) || (displayDetailsByID($spouse))||(showLivingNameByID($spouse))) print PrintReady(get_person_name($spouse));
									else print $pgv_lang["private"];
									print "</a><br />\n";
								 }
							  }
							  for($j=0; $j<$num; $j++) {
								   $cpid = $smatch[$j][1];
	 							   if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["indi_info"].": ".$cpid;
	 							   else $title = $cpid." :".$pgv_lang["indi_info"];
								   print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"individual.php?pid=$cpid&amp;ged=$GEDCOM\" title=\"$title\" $mouseAction1>";
 								   if (($SHOW_LIVING_NAMES>=$PRIV_PUBLIC) || (displayDetailsByID($cpid))||(showLivingNameByID($cpid))) print PrintReady(get_person_name($cpid));
								   else print $pgv_lang["private"];
								   print "<br /></a>";
							  }
						 }
					}
					print "</td></tr></table>\n\t\t</div>";
			   }
			   // NOTE: Start div out-$pid.$personcount.$count
			   print "\n\t\t\t<div id=\"out-$boxID\"";
			   if ($style==1) print " class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden; z-index:'-1';\"";
			   else print " style=\"padding: 2px;\"";
			   // NOTE: Zoom
			   if (($ZOOM_BOXES!="disabled")&&(!$show_full)) {
					if ($ZOOM_BOXES=="mouseover") print $mouseAction2;
					if ($ZOOM_BOXES=="mousedown") print $mouseAction3;
					if (($ZOOM_BOXES=="click")&&($view!="preview")) print $mouseAction4;
			   }
			   print "><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
			   //-- links and zoom icons
			   // NOTE: Start div icons-$personcount.$pid.$count
			   if ($TEXT_DIRECTION == "rtl") {
					print "<div id=\"icons-$boxID\" style=\"float:left; width: 25px; height: 50px;";
			   }
			   else {
					print "<div id=\"icons-$boxID\" style=\"float:right; width: 25px; height: 50px;";
			   }
			   if ($show_full) print " display: block;";
			   else print " display: none;";
			   print "\">";
			   // NOTE: Zoom
			   if (($ZOOM_BOXES!="disabled")&&($show_full)) {
					print "<a href=\"javascript:;\"";
					if ($ZOOM_BOXES=="mouseover") print $mouseAction2;
					if ($ZOOM_BOXES=="mousedown") print $mouseAction3;
					if ($ZOOM_BOXES=="click") print $mouseAction4;
					print "><img id=\"iconz-$boxID\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["zoomin"]["other"]."\" width=\"25\" height=\"25\" border=\"0\" alt=\"".$pgv_lang["zoom_box"]."\" title=\"".$pgv_lang["zoom_box"]."\" /></a>";
			   }
			   if ($LINK_ICONS!="disabled") {
					$click_link="javascript:;";
					$whichChart="";
					if (preg_match("/pedigree.php/", $SCRIPT_NAME)>0) {
						$click_link="pedigree.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;talloffset=$talloffset&amp;ged=$GEDCOM";
						$whichChart="pedigree_chart";
						$whichID=$pid;
					}

					if (preg_match("/hourglass.php/", $SCRIPT_NAME)>0) {
						$click_link="hourglass.php?pid=$pid&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM";
						$whichChart="hourglass_chart";
						$whichID=$pid;
					}

					if (preg_match("/ancestry.php/", $SCRIPT_NAME)>0) {
						$click_link="ancestry.php?rootid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM";
						$whichChart="ancestry_chart";
						$whichID=$pid;
					}

					if (preg_match("/descendancy.php/", $SCRIPT_NAME)>0) {
						$click_link="descendancy.php?pid=$pid&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM";
						$whichChart="descend_chart";
						$whichID=$pid;
					}

					if ((preg_match("/family.php/", $SCRIPT_NAME)>0)&&!empty($famid)) {
						$click_link="family.php?famid=$famid&amp;ged=$GEDCOM";
						$whichChart="familybook_chart";
						$whichID=$famid;
					}

					if (preg_match("/individual.php/", $SCRIPT_NAME)>0) {
						$click_link="individual.php?pid=$pid&amp;ged=$GEDCOM";
						$whichChart="indi_info";
						$whichID=$pid;
					}

					if (empty($whichChart)) $title="";
					else {
	 					if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang[$whichChart].": ".$whichID;
	 					else $title = $whichID." :".$pgv_lang[$whichChart];
 					}
					print "<a href=\"$click_link\" title=\"$title\"";
					// NOTE: Zoom
					if ($LINK_ICONS=="mouseover") print "onmouseover=\"show_family_box('".$boxID."', '";
					if ($LINK_ICONS=="click") print "onclick=\"toggle_family_box('".$boxID."', '";
					if ($style==1) print "box$pid";
					else print "relatives";
					print "');";
					print " return false;\" ";
					// NOTE: Zoom
					print "onmouseout=\"family_box_timeout('".$boxID."');";
					print " return false;\"";
					if (($click_link=="#")&&($LINK_ICONS!="click")) print "onclick=\"return false;\"";
					print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"]."\" width=\"25\" border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".$pgv_lang["person_links"]."\" title=\"".$pgv_lang["person_links"]."\" /></a>";
			   }
			   // NOTE: Close div icons-$personcount.$pid.$count
			   print "</div>\n";
		  }
		  // NOTE: Start div out-$personcount.$pid.$count
		  else {
			   if ($style==1) {
					print "\n\t\t\t<div id=\"out-$boxID\" class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"";
			   }
			   else {
					print "\n\t\t\t<div id=\"out-$boxID\" class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"";
			   }
			   // NOTE: Zoom
			   if (($ZOOM_BOXES!="disabled")&&(empty($SEARCH_SPIDER))) {
					if ($ZOOM_BOXES=="mouseover") print $mouseAction2;
					if ($ZOOM_BOXES=="mousedown") print $mouseAction3;
					if (($ZOOM_BOXES=="click")&&($view!="preview")) print $mouseAction4;
			   }
			   print "><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
		  }
	 }
	 // NOTE: Start div out-$personcount.$pid.$count
	 else {
		  if ($style==1) print "\n\t\t\t<div id=\"out-$boxID\" class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
		  else print "\n\t\t\t<div id=\"out-$boxID\" class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
	 }
	 //-- find the name
	 $name = get_person_name($pid);
	 if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES && showFact("OBJE", $pid)) {
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

				if (!empty($object['mid']) && $USE_MEDIA_VIEWER) print "<a href=\"mediaviewer.php?mid=".$object['mid']."\" >";
				else print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($object["file"])."',$imgwidth, $imgheight);\">";

			   print "<img id=\"box-$boxID-thumb\" src=\"".$object["thumb"]."\" vspace=\"0\" hspace=\"0\" class=\"$class\" alt =\"\" title=\"\" ";
			   if (!$show_full) print " style=\"display: none;\"";
			   if ($imgsize) print " /></a>\n";
			   else print " />\n";
		  }
	 }
	 //-- find additional name
	 $addname = get_add_person_name($pid);
	 //-- check if the persion is visible
	 if (!$disp) {
		  if (showLivingName($indirec)) {
			   // NOTE: Start span namedef-$personcount.$pid.$count
			   if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["indi_info"].": ".$pid;
			   else $title = $pid." :".$pgv_lang["indi_info"];
			   print "<a href=\"individual.php?pid=$pid&amp;ged=$GEDCOM\" title=\"$title\" onmouseover=\"change_class('namedef-$boxID','name".$style."Hover'); return false;\" onmouseout=\"change_class('namedef-$boxID','name$style'); return false;\"><span id=\"namedef-$boxID\" class=\"name$style\">";
 			   print PrintReady($name);
			   // NOTE: IMG ID
			   print "<img id=\"box-$boxID-sex\" src=\"$PGV_IMAGE_DIR/";
			   if ($isF=="") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
			   else  if ($isF=="F")print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
			   else  print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"];
			   print "\" class=\"sex_image\" />";
			   if ($SHOW_ID_NUMBERS) {
				   print "</span><span class=\"details$style\">";
		      	   if ($TEXT_DIRECTION=="ltr") print "&lrm;($pid)&lrm;";
			        else print "&rlm;($pid)&rlm;";
				   // NOTE: Close span namedef-$personcount.$pid.$count
				   print "</span>";
			   }
			  if (strlen($addname) > 0) {
				   print "<br />";
				   // NOTE: Start span addnamedef-$personcount.$pid.$count
				   // NOTE: Close span addnamedef-$personcount.$pid.$count
				   if (hasRTLText($addname) && $style=="1") print "<span id=\"addnamedef-$boxID\" class=\"name2\"> ";
				   else print "<span id=\"addnamedef-$boxID\" class=\"name$style\"> ";
 				   print PrintReady($addname)."</span><br />";
			 }
		     print "</a>";
		  }
		  else {
			if(empty($SEARCH_SPIDER)) {
				$user = getUser($CONTACT_EMAIL);
				print "<a href=\"javascript:;\" onclick=\"if (confirm('".preg_replace("'<br />'", " ", $pgv_lang["privacy_error"])."\\n\\n".str_replace("#user[fullname]#", $user["firstname"]." ".$user["lastname"], $pgv_lang["clicking_ok"])."')) ";
				if ($CONTACT_METHOD!="none") {
					if ($CONTACT_METHOD=="mailto") print "window.location = 'mailto:".$user["email"]."'; ";
					else print "message('$CONTACT_EMAIL', '$CONTACT_METHOD'); ";
				}
				// NOTE: Start span namedef-$pid.$personcount.$count
				// NOTE: Close span namedef-$pid.$personcount.$count
				print "return false;\">";
			}
			print "<span id=\"namedef-$boxID\" class=\"name$style\">".$pgv_lang["private"]."</span>";
			if(empty($SEARCH_SPIDER)) {
				print "</a>\n";
			}
		  }
		  if ($show_full && (empty($SEARCH_SPIDER))) {
			  // NOTE: Start span fontdef-$pid.$personcount.$count
			  // NOTE: Close span fontdef-$pid.$personcount.$count
			   print "<br /><span id=\"fontdef-$boxID\" class=\"details$style\">";
			   print $pgv_lang["private"];
			   print "</span>";
		  }
		  // NOTE: Close div out-$pid.$personcount.$count
		  print "\n\t\t\t</td></tr></table></div>";
		  return;
	 }
	 if ($TEXT_DIRECTION=="ltr") $title = $pgv_lang["indi_info"].": ".$pid;
	 else $title = $pid." :".$pgv_lang["indi_info"];
	 print "<a href=\"individual.php?pid=$pid&amp;ged=$GEDCOM\" title=\"$title\" onmouseover=\"change_class('namedef-$boxID','name".$style."Hover'); return false;\" onmouseout=\"change_class('namedef-$boxID','name$style'); return false;\"";
	 if (!$show_full) {
		  //not needed or wanted for mouseover //if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"event.cancelBubble = true;\"";
		  if ($ZOOM_BOXES=="mousedown") print "onmousedown=\"event.cancelBubble = true;\"";
		  if ($ZOOM_BOXES=="click") print "onclick=\"event.cancelBubble = true;\"";
	 }
	 // NOTE: Start span namedef-$pid.$personcount.$count
	 print "><span id=\"namedef-$boxID\" class=\"name$style";
	 // add optional CSS style for each fact
	 $cssfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","CAST","DSCR","EDUC","IDNO",
	 "NATI","NCHI","NMR","OCCU","PROP","RELI","RESI","SSN","TITL","BAPL","CONL","ENDL","SLGC","_MILI");
	 foreach($cssfacts as $indexval => $fact) {
		  $ct = preg_match_all("/1 $fact/", $indirec, $nmatch, PREG_SET_ORDER);
		  if ($ct>0) print " $fact";
	 }
	 print "\">";
	 print PrintReady($name);
	 // NOTE: Close span namedef-$pid.$personcount.$count
	 print "</span>";
	 print "<span class=\"name$style\">";
	 // NOTE: IMG ID
	 print "<img id=\"box-$boxID-sex\" src=\"$PGV_IMAGE_DIR/";
	 if ($isF=="") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
	 else  if ($isF=="F")print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
	 else  print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"];
	 print "\" class=\"sex_image\" />";
	 print "</span>\r\n";
	 if ($SHOW_ID_NUMBERS) {
			if ($TEXT_DIRECTION=="ltr") print "<span class=\"details$style\">&lrm;($pid)&lrm; </span>";
			else print "<span class=\"details$style\">&rlm;($pid)&rlm; </span>";
	 }
	 if ($SHOW_LDS_AT_GLANCE) {
		 print "<span class=\"details$style\">".get_lds_glance($indirec)."</span>";
	 }
	 if (strlen($addname) > 0) {
		   print "<br />";
		   if (hasRTLText($addname) && $style=="1")
				print "<span id=\"addnamedef-$pid.$count\" class=\"name2\"> ";
		   else print "<span id=\"addnamedef-$pid.$count\" class=\"name$style\"> ";
		   print PrintReady($addname)."</span><br />";
	 }
	 print "</a>";
    if(empty($SEARCH_SPIDER)) {
	 // NOTE: Start div inout-$pid.$personcount.$count
	 //if (!$show_full) print "\n<div id=\"inout-$boxID\" style=\"display: none;\">\n";
	 // NOTE: Start div fontdev-$pid.$personcount.$count
	 print "<div id=\"fontdef-$boxID\" class=\"details$style\">";

	 // NOTE: Start div inout2-$pid.$personcount.$count
//	 if ($show_full) print "\n<div id=\"inout2-$boxID\" style=\"display: block;\">\n";
if ($show_full)
{
	 print "\n<div id=\"inout2-$boxID\" ";
	  print " style=\"display: block;\">\n";
	 $birttag = "BIRT";
	 $bpos1 = strpos($indirec, "1 BIRT");
	 if ($bpos1) {
	 	if (showFact($birttag, $pid)) print_simple_fact($indirec, $birttag, $pid);
	 }
	 //-- no birth check for christening or baptism
	 else {
		  $bpos1 = strpos($indirec, "1 CHR");
		  if ($bpos1) {
			   $birttag = "CHR";
			   if (showFact($birttag, $pid)) print_simple_fact($indirec, $birttag, $pid);
		  }
		  else {
			   $bpos1 = strpos($indirec, "1 BAPM");
			   if ($bpos1) {
					$birttag = "BAPM";
					if (showFact($birttag, $pid)) print_simple_fact($indirec, $birttag, $pid);
			   }
		  }
	 }
	 //-- section to display optional tags in the boxes
	 if (!empty($CHART_BOX_TAGS)) {
		 $opt_tags = preg_split("/[, ]+/", $CHART_BOX_TAGS);
		 foreach($opt_tags as $indexval => $tag) {
			 if (!empty($tag)&&($tag!="BURI")&&($tag!="CREM")) {
			 	if (showFact($tag, $pid)) print_simple_fact($indirec, $tag, $pid);
		 	}
		 }
	 }

	 $bpos1 = strpos($indirec, "1 DEAT");
	 if ($bpos1) {
		  if (showFact("DEAT", $pid)) {
			  print_simple_fact($indirec, "DEAT", $pid);
		  }
	 }
	 foreach (array("BURI", "CREM") as $indexval => $tag) {
	 	if (strpos($CHART_BOX_TAGS, $tag)!==false && showFact($tag, $pid)) print_simple_fact($indirec, $tag, $pid);
	}
	print "</div>\n";
}

	 // NOTE: Close div inout2-$pid.$personcount.$count
	 //if ($show_full) print "</div>\n";
	 print "</div>\n";
    }// SEARCH_SPIDER

//	 -- find all level 1 sub records
//	  $skipfacts = array("SEX","FAMS","FAMC","NAME","TITL","NOTE","SOUR","SSN","OBJE","HUSB","WIFE","CHIL","ALIA","ADDR","PHON","SUBM","_EMAIL","CHAN","URL","EMAIL","WWW","RESI","_UID","_TODO");
//	  $subfacts = get_all_subrecords($indirec, implode(",", $skipfacts));
//	   NOTE: Open div inout-$pid.$personcount.$count

//   --All code to load information has been moved to expand_view.php
    if(empty($SEARCH_SPIDER)) {
	  print "\n<div id=\"inout-$boxID\" style=\"display: none;\">\n";
	  print "<div id=\"LOADING-inout-$boxID\">";
	  print $pgv_lang['loading'];
 	  print "</div></div>";
    }// SEARCH_SPIDER

	 // NOTE: Close div out-rand()
	 print "\n\t\t\t</td></tr></table></div>";
}
/**
 * print out standard HTML header
 *
 * This function will print out the HTML, HEAD, and BODY tags and will load in the CSS javascript and
 * other auxiliary files needed to run PGV.  It will also include the theme specific header file.
 * This function should be called by every page, except popups, before anything is output.
 *
 * Popup pages, because of their different format, should invoke function print_simple_header() instead.
 *
 * @param string $title	the title to put in the <TITLE></TITLE> header tags
 * @param string $head
 * @param boolean $use_alternate_styles
 */
function print_header($title, $head="",$use_alternate_styles=true) {
	global $pgv_lang, $bwidth;
	global $HOME_SITE_URL, $HOME_SITE_TEXT, $SERVER_URL;
	global $BROWSERTYPE, $SEARCH_SPIDER;
	global $view, $cart;
	global $CHARACTER_SET, $VERSION, $PGV_IMAGE_DIR, $GEDCOMS, $GEDCOM, $CONTACT_EMAIL, $COMMON_NAMES_THRESHOLD, $INDEX_DIRECTORY;
	global $SCRIPT_NAME, $QUERY_STRING, $action, $query, $changelanguage,$theme_name;
	global $FAVICON, $stylesheet, $print_stylesheet, $rtl_stylesheet, $headerfile, $toplinks, $THEME_DIR, $print_headerfile;
	global $PGV_IMAGES, $TEXT_DIRECTION, $ONLOADFUNCTION,$REQUIRE_AUTHENTICATION, $SHOW_SOURCES, $ENABLE_RSS, $RSS_FORMAT;
	global $META_AUTHOR, $META_PUBLISHER, $META_COPYRIGHT, $META_DESCRIPTION, $META_PAGE_TOPIC, $META_AUDIENCE, $META_PAGE_TYPE, $META_ROBOTS, $META_REVISIT, $META_KEYWORDS, $META_TITLE, $META_SURNAME_KEYWORDS;

	// If not on allowed list, dump the spider onto the redirect page.
	// This kills recognized spiders in their tracks.
	// To stop unrecognized spiders, see META_ROBOTS below.
	if(!empty($SEARCH_SPIDER)) {
		if(!((strstr($SCRIPT_NAME, "/individual.php")) ||
		     (strstr($SCRIPT_NAME, "/indilist.php")) ||
		     (strstr($SCRIPT_NAME, "/login.php")) ||
		     (strstr($SCRIPT_NAME, "/source.php")) ||
		     (strstr($SCRIPT_NAME, "/search_engine.php")) ||
		     (strstr($SCRIPT_NAME, "/index.php"))) ) {
			header("Location: search_engine.php");
			exit;
		}
	}
	header("Content-Type: text/html; charset=$CHARACTER_SET");

	// Determine browser type
	if (stristr($_SERVER["HTTP_USER_AGENT"], "Opera"))
		$BROWSERTYPE = "opera";
	else if (stristr($_SERVER["HTTP_USER_AGENT"], "Netscape"))
		$BROWSERTYPE = "netscape";
	else if (stristr($_SERVER["HTTP_USER_AGENT"], "Gecko"))
		$BROWSERTYPE = "mozilla";
	else if (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
		$BROWSERTYPE = "msie";
	else
		$BROWSERTYPE = "other";

	print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n\t<head>\n\t\t";
	print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$CHARACTER_SET\" />\n\t\t";
	if( $FAVICON ) {
	   print "<link rel=\"shortcut icon\" href=\"$FAVICON\" type=\"image/x-icon\"></link>\n\t\t";
	}

	if (!isset($META_TITLE)) $META_TITLE = "";
	print "<title>".PrintReady(strip_tags($title)." - ".$META_TITLE." - PhpGedView", TRUE)."</title>\n\t";
	if ($ENABLE_RSS){
		$applicationType = "application/rss+xml";
		if ($RSS_FORMAT == "ATOM" || $RSS_FORMAT == "ATOM0.3"){
			$applicationType = "application/atom+xml";
		}
		$gedcomTitle = "";
		if (!empty($GEDCOMS[$GEDCOM]["title"])) $gedcomTitle = $GEDCOMS[$GEDCOM]["title"];
		if(empty($gedcomTitle)){
			$gedcomTitle = "RSS";
		}
		if(! $REQUIRE_AUTHENTICATION){
			print "<link href=\"" . $SERVER_URL . "rss.php?ged=$GEDCOM\" rel=\"alternate\" type=\"$applicationType\" title=\"$gedcomTitle\" />\n\t";
		}
		//print "<link href=\"" . $SERVER_URL . "rss.php?ged=$GEDCOM&amp;auth=basic\" rel=\"alternate\" type=\"$applicationType\" title=\"$gedcomTitle - " . $pgv_lang["authenticated_feed"] . "\" />\n\t";
	}
	print "<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\" media=\"all\"></link>\n\t";
	if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) print "<link rel=\"stylesheet\" href=\"$rtl_stylesheet\" type=\"text/css\" media=\"all\"></link>\n\t";
	if ($use_alternate_styles) {
		if ($BROWSERTYPE != "other") {
			print "<link rel=\"stylesheet\" href=\"".$THEME_DIR.$BROWSERTYPE.".css\" type=\"text/css\" media=\"all\"></link>\n\t";
		}
	}
	print "<link rel=\"stylesheet\" href=\"$print_stylesheet\" type=\"text/css\" media=\"print\"></link>\n\t";
	if ($BROWSERTYPE == "msie") print "<style type=\"text/css\">\nFORM { margin-top: 0px; margin-bottom: 0px; }\n</style>\n";
	print "<!-- Bitweaver PhpGedView v$VERSION -->\n";
	if (isset($changelanguage)) {
		$terms = preg_split("/[&?]/", $QUERY_STRING);
		$vars = "";
		for ($i=0; $i<count($terms); $i++) {
			if ((!empty($terms[$i]))&&(strstr($terms[$i], "changelanguage")===false)&&(strpos($terms[$i], "NEWLANGUAGE")===false)) {
					$vars .= $terms[$i]."&";
			}
		}
		$query_string = $vars;
	}
	else $query_string = $QUERY_STRING;
	if ($view!="preview") {
		 $old_META_AUTHOR = $META_AUTHOR;
		 $old_META_PUBLISHER = $META_PUBLISHER;
		 $old_META_COPYRIGHT = $META_COPYRIGHT;
		 $old_META_DESCRIPTION = $META_DESCRIPTION;
		 $old_META_PAGE_TOPIC = $META_PAGE_TOPIC;
		  $cuser = getUser($CONTACT_EMAIL);
		  if ($cuser) {
			  if (empty($META_AUTHOR)) $META_AUTHOR = $cuser["firstname"]." ".$cuser["lastname"];
			  if (empty($META_PUBLISHER)) $META_PUBLISHER = $cuser["firstname"]." ".$cuser["lastname"];
			  if (empty($META_COPYRIGHT)) $META_COPYRIGHT = $cuser["firstname"]." ".$cuser["lastname"];
		  }
		  if (!empty($META_AUTHOR)) print "<meta name=\"author\" content=\"".$META_AUTHOR."\" />\n";
		  if (!empty($META_PUBLISHER)) print "<meta name=\"publisher\" content=\"".$META_PUBLISHER."\" />\n";
		  if (!empty($META_COPYRIGHT)) print "<meta name=\"copyright\" content=\"".$META_COPYRIGHT."\" />\n";
		  print "<meta name=\"keywords\" content=\"".$META_KEYWORDS;
		  if ($META_SURNAME_KEYWORDS) {
		  	$surnames = get_common_surnames_index($GEDCOM);
		  	foreach($surnames as $surname=>$count) if (!empty($surname)) print ", $surname";
	  	  }
		  print "\" />\n";
		  if ((empty($META_DESCRIPTION))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_DESCRIPTION = $GEDCOMS[$GEDCOM]["title"];
		  if ((empty($META_PAGE_TOPIC))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_PAGE_TOPIC = $GEDCOMS[$GEDCOM]["title"];
		  if (!empty($META_DESCRIPTION)) print "<meta name=\"description\" content=\"".preg_replace("/\"/", "", $META_DESCRIPTION)."\" />\n";
		  if (!empty($META_PAGE_TOPIC)) print "<meta name=\"page-topic\" content=\"".preg_replace("/\"/", "", $META_PAGE_TOPIC)."\" />\n";
	 	  if (!empty($META_AUDIENCE)) print "<meta name=\"audience\" content=\"$META_AUDIENCE\" />\n";
	 	  if (!empty($META_PAGE_TYPE)) print "<meta name=\"page-type\" content=\"$META_PAGE_TYPE\" />\n";

		  // Restrict good search engine spiders to the index page and the individual.php pages.
		  // Quick and dirty hack that will still leave some url only links in Google.
		  // Also ignored by crawlers like wget, so other checks have to be done too.
		  if((strstr($SCRIPT_NAME, "/individual.php")) ||
		     (strstr($SCRIPT_NAME, "/indilist.php")) ||
		     (strstr($SCRIPT_NAME, "/source.php")) ||
		     (strstr($SCRIPT_NAME, "/search_engine.php")) ||
		     (strstr($SCRIPT_NAME, "/index.php")) ) {
			// empty case is to index,follow anyways.
	 	  	if (empty($META_ROBOTS)) $META_ROBOTS = "index,follow";
			print "<meta name=\"robots\" content=\"$META_ROBOTS\" />\n";
		  }
		  else {
			print "<meta name=\"robots\" content=\"noindex,nofollow\" />\n";
		  }
	 	  if (!empty($META_REVISIT)) print "<meta name=\"revisit-after\" content=\"$META_REVISIT\" />\n";
		  print "<meta name=\"generator\" content=\"Bitweaver PhpGedView v$VERSION - http://www.bitweaver.org\" />\n";
		 $META_AUTHOR = $old_META_AUTHOR;
		 $META_PUBLISHER = $old_META_PUBLISHER;
		 $META_COPYRIGHT = $old_META_COPYRIGHT;
		 $META_DESCRIPTION = $old_META_DESCRIPTION;
		 $META_PAGE_TOPIC = $old_META_PAGE_TOPIC;
	}
	else {
?>
<script language="JavaScript" type="text/javascript">
<!--
function hidePrint() {
	 var printlink = document.getElementById('printlink');
	 var printlinktwo = document.getElementById('printlinktwo');
	 if (printlink) {
		  printlink.style.display='none';
		  printlinktwo.style.display='none';
	 }
}
function showBack() {
	 var printlink = document.getElementById('printlink');
	 var printlinktwo = document.getElementById('printlinktwo');
	 if (printlink) {
		  printlink.style.display='inline';
		  printlinktwo.style.display='inline';
	 }
}
//-->
</script>
<?php
}
?>
<script language="JavaScript" type="text/javascript">
	 <!--
	 <?php print "query = \"$query_string\";\n"; ?>
	 <?php print "textDirection = \"$TEXT_DIRECTION\";\n"; ?>
	 <?php print "browserType = \"$BROWSERTYPE\";\n"; ?>
	 <?php print "themeName = \"".strtolower($theme_name)."\";\n"; ?>
	 <?php print "SCRIPT_NAME = \"$SCRIPT_NAME\";\n"; ?>
	 /* keep the session id when opening new windows */
	 <?php print "sessionid = \"".session_id()."\";\n"; ?>
	 <?php print "sessionname = \"".session_name()."\";\n"; ?>
	 plusminus = new Array();
	 plusminus[0] = new Image();
	 plusminus[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]; ?>";
	 plusminus[1] = new Image();
	 plusminus[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]; ?>";
	 zoominout = new Array();
	 zoominout[0] = new Image();
	 zoominout[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["zoomin"]["other"]; ?>";
	 zoominout[1] = new Image();
	 zoominout[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["zoomout"]["other"]; ?>";
	 arrows = new Array();
	 arrows[0] = new Image();
	 arrows[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow2"]["other"]; ?>";
	 arrows[1] = new Image();
	 arrows[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow2"]["other"]; ?>";
	 arrows[2] = new Image();
	 arrows[2].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["uarrow2"]["other"]; ?>";
	 arrows[3] = new Image();
	 arrows[3].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow2"]["other"]; ?>";

function delete_record(pid, linenum, mediaid) {
	if (!mediaid) mediaid="";
	 if (confirm('<?php print $pgv_lang["check_delete"]; ?>')) {
		  window.open('edit_interface.php?action=delete&pid='+pid+'&linenum='+linenum+'&mediaid='+mediaid+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}
function deleteperson(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_person"]; ?>')) {
		  window.open('edit_interface.php?action=deleteperson&pid='+pid+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}

function deleterepository(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_repo"]; ?>')) {
		  window.open('edit_interface.php?action=deleterepo&pid='+pid+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}
function message(username, method, url, subject) {
	 if ((!url)||(url=="")) url='<?php print urlencode(basename($SCRIPT_NAME)."?".$QUERY_STRING); ?>';
	 if ((!subject)||(subject=="")) subject= '';
	 window.open('message.php?to='+username+'&method='+method+'&url='+url+'&subject='+subject+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}
var whichhelp = 'help_<?php print basename($SCRIPT_NAME)."&action=".$action; ?>';
//-->
</script>
<script src="./js/phpgedview.js" language="JavaScript" type="text/javascript"></script>
<?php
	 print $head;
	 print "</head>\n\t<body id=\"body\"";
	 if ($view=="preview") print " onbeforeprint=\"hidePrint();\" onafterprint=\"showBack();\"";
	 print " onload=\"";
	 if (!empty($ONLOADFUNCTION)) print $ONLOADFUNCTION;
	 if ($TEXT_DIRECTION=="rtl") {
		print " maxscroll = document.documentElement.scrollLeft;";
 	}
 	 print "\"";
	 print ">\n\t";
	 print "<!-- begin header section -->\n";
	 if ($view!="preview") {
		  include($headerfile);
		  include($toplinks);
	 }
	 else {
		  include($print_headerfile);
	 }
	 print "<!-- end header section -->\n";
	 print "<!-- begin content section -->\n";
}
/**
 * print simple HTML header
 *
 * This function will print out the HTML, HEAD, and BODY tags and will load in the CSS javascript and
 * other auxiliary files needed to run PGV.  It does not include any theme specific header files.
 * This function should be called by every page before anything is output on popup pages.
 *
 * @param string $title	the title to put in the <TITLE></TITLE> header tags
 * @param string $head
 * @param boolean $use_alternate_styles
 */
function print_simple_header($title) {
	 global $pgv_lang;
	 global $HOME_SITE_URL;
	 global $HOME_SITE_TEXT, $SEARCH_SPIDER;
	 global $view, $rtl_stylesheet;
	 global $CHARACTER_SET, $VERSION, $PGV_IMAGE_DIR;
	 global $SCRIPT_NAME, $QUERY_STRING, $action, $query, $changelanguage;
	 global $FAVICON, $stylesheet, $headerfile, $toplinks, $THEME_DIR, $print_headerfile, $SCRIPT_NAME;
	 global $TEXT_DIRECTION, $GEDCOMS, $GEDCOM, $CONTACT_EMAIL, $COMMON_NAMES_THRESHOLD,$PGV_IMAGES;
	 global $META_AUTHOR, $META_PUBLISHER, $META_COPYRIGHT, $META_DESCRIPTION, $META_PAGE_TOPIC, $META_AUDIENCE, $META_PAGE_TYPE, $META_ROBOTS, $META_REVISIT, $META_KEYWORDS, $META_TITLE, $META_SURNAME_KEYWORDS;

	// If not on allowed list, dump the spider onto the redirect page.
	// This kills recognized spiders in their tracks.
	// To stop unrecognized spiders, see META_ROBOTS below.
	if(!empty($SEARCH_SPIDER)) {
		if(!((strstr($SCRIPT_NAME, "/individual.php")) ||
		     (strstr($SCRIPT_NAME, "/indilist.php")) ||
		     (strstr($SCRIPT_NAME, "/source.php")) ||
		     (strstr($SCRIPT_NAME, "/search_engine.php")) ||
		     (strstr($SCRIPT_NAME, "/index.php"))) ) {
			header("Location: search_engine.php");
			exit;
		}
	}
	 header("Content-Type: text/html; charset=$CHARACTER_SET");
	 print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	 print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n\t<head>\n\t\t";
	 print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$CHARACTER_SET\" />\n\t\t";
	if( $FAVICON ) {
	   print "<link rel=\"shortcut icon\" href=\"$FAVICON\" type=\"image/x-icon\"></link>\n\t\t";
	}
	if (!isset($META_TITLE)) $META_TITLE = "";
	print "<title>".PrintReady(strip_tags($title))." - ".$META_TITLE." - PhpGedView</title>\n\t<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\"></link>\n\t";
	if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) print "<link rel=\"stylesheet\" href=\"$rtl_stylesheet\" type=\"text/css\" media=\"all\"></link>\n\t";
	$old_META_AUTHOR = $META_AUTHOR;
		 $old_META_PUBLISHER = $META_PUBLISHER;
		 $old_META_COPYRIGHT = $META_COPYRIGHT;
		 $old_META_DESCRIPTION = $META_DESCRIPTION;
		 $old_META_PAGE_TOPIC = $META_PAGE_TOPIC;
		  $cuser = getUser($CONTACT_EMAIL);
		  if ($cuser) {
			  if (empty($META_AUTHOR)) $META_AUTHOR = $cuser["firstname"]." ".$cuser["lastname"];
			  if (empty($META_PUBLISHER)) $META_PUBLISHER = $cuser["firstname"]." ".$cuser["lastname"];
			  if (empty($META_COPYRIGHT)) $META_COPYRIGHT = $cuser["firstname"]." ".$cuser["lastname"];
		  }
		  if (!empty($META_AUTHOR)) print "<meta name=\"author\" content=\"".$META_AUTHOR."\" />\n";
		  if (!empty($META_PUBLISHER)) print "<meta name=\"publisher\" content=\"".$META_PUBLISHER."\" />\n";
		  if (!empty($META_COPYRIGHT)) print "<meta name=\"copyright\" content=\"".$META_COPYRIGHT."\" />\n";
		  print "<meta name=\"keywords\" content=\"".$META_KEYWORDS;
		  if ($META_SURNAME_KEYWORDS) {
			  $surnames = get_common_surnames_index($GEDCOM);
			  foreach($surnames as $surname=>$count) print ", $surname";
	  	  }
		  print "\" />\n";
		  if ((empty($META_DESCRIPTION))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_DESCRIPTION = $GEDCOMS[$GEDCOM]["title"];
		  if ((empty($META_PAGE_TOPIC))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_PAGE_TOPIC = $GEDCOMS[$GEDCOM]["title"];
		  if (!empty($META_DESCRIPTION)) print "<meta name=\"description\" content=\"".preg_replace("/\"/", "", $META_DESCRIPTION)."\" />\n";
		  if (!empty($META_PAGE_TOPIC)) print "<meta name=\"page-topic\" content=\"".preg_replace("/\"/", "", $META_PAGE_TOPIC)."\" />\n";
	 	  if (!empty($META_AUDIENCE)) print "<meta name=\"audience\" content=\"$META_AUDIENCE\" />\n";
	 	  if (!empty($META_PAGE_TYPE)) print "<meta name=\"page-type\" content=\"$META_PAGE_TYPE\" />\n";

		  // Restrict good search engine spiders to the index page and the individual.php pages.
		  // Quick and dirty hack that will still leave some url only links in Google.
		  // Also ignored by crawlers like wget, so other checks have to be done too.
		  if((strstr($SCRIPT_NAME, "/individual.php")) ||
		     (strstr($SCRIPT_NAME, "/indilist.php")) ||
		     (strstr($SCRIPT_NAME, "/source.php")) ||
		     (strstr($SCRIPT_NAME, "/search_engine.php")) ||
		     (strstr($SCRIPT_NAME, "/index.php")) ) {
			// empty case is to index,follow anyways.
	 	  	if (empty($META_ROBOTS)) $META_ROBOTS = "index,follow";
			print "<meta name=\"robots\" content=\"$META_ROBOTS\" />\n";
		  }
		  else {
			print "<meta name=\"robots\" content=\"noindex,nofollow\" />\n";
		  }
	 	  if (!empty($META_REVISIT)) print "<meta name=\"revisit-after\" content=\"$META_REVISIT\" />\n";
		  print "<meta name=\"generator\" content=\"Bitweaver PhpGedView v$VERSION - http://www.bitweaver.org\" />\n";
		 $META_AUTHOR = $old_META_AUTHOR;
		 $META_PUBLISHER = $old_META_PUBLISHER;
		 $META_COPYRIGHT = $old_META_COPYRIGHT;
		 $META_DESCRIPTION = $old_META_DESCRIPTION;
		 $META_PAGE_TOPIC = $old_META_PAGE_TOPIC;
?>
	<style type="text/css">
	<!--
	.largechars {
		font-size: 18px;
	}
	-->
	</style>
	 <script language="JavaScript" type="text/javascript">
	 <!--
	 /* set these vars so that the session can be passed to new windows */
	 <?php print "sessionid = \"".session_id()."\";\n"; ?>
	 <?php print "sessionname = \"".session_name()."\";\n"; ?>
	 plusminus = new Array();
	 plusminus[0] = new Image();
	 plusminus[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]; ?>";
	 plusminus[1] = new Image();
	 plusminus[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]; ?>";
	 zoominout = new Array();
	 zoominout[0] = new Image();
	 zoominout[0].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["zoomin"]["other"]; ?>";
	 zoominout[1] = new Image();
	 zoominout[1].src = "<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["zoomout"]["other"]; ?>";

	var helpWin;
	function helpPopup(which) {

		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('help_text.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');

		else helpWin.location = 'help_text.php?help='+which;
		return false;
	}
function message(username, method, url, subject) {
	 if ((!url)||(url=="")) url='<?php print urlencode(basename($SCRIPT_NAME)."?".$QUERY_STRING); ?>';
	 if ((!subject)||(subject=="")) subject= '';
	 window.open('message.php?to='+username+'&method='+method+'&url='+url+'&subject='+subject+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}
	 //-->
	 </script>
	 <script src="./js/phpgedview.js" language="JavaScript" type="text/javascript"></script>
	 <?php
	 print "</head>\n\t<body style=\"margin: 5px;\"";
	 print " onload=\"loadHandler();\">\n\t";
}
// -- print the html to close the page
function print_footer() {
	 global $without_close, $pgv_lang, $view, $buildindex, $pgv_changes, $DBTYPE;
	 global $SHOW_STATS, $SCRIPT_NAME, $QUERY_STRING, $footerfile, $print_footerfile, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $printlink;
	 global $PGV_IMAGE_DIR, $theme_name, $PGV_IMAGES, $TEXT_DIRECTION, $footer_count, $DEBUG;

	 if (!isset($footer_count)) $footer_count = 1;
	 else $footer_count++;
	 print "<!-- begin footer -->\n";
	 if ($view!="preview") {
		  include($footerfile);
	 }
	 else {
		  include($print_footerfile);
		  print "\n\t<div id=\"backprint\" style=\"text-align: center; width: 95%\"><br />";
		  $backlink = $SCRIPT_NAME."?".get_query_string();
		  if (!$printlink) {
			   print "\n\t<br /><a id=\"printlink\" href=\"javascript:;\" onclick=\"print(); return false;\">".$pgv_lang["print"]."</a><br />";
			   print "\n\t <a id=\"printlinktwo\"	  href=\"javascript:;\" onclick=\"window.location='".$backlink."'; return false;\">".$pgv_lang["cancel_preview"]."</a><br />";
		  }
		  $printlink = true;
		  print "</div>";
	 }
	 if (function_exists("load_behaviour")) load_behaviour();  // @see function_print_lists.php
	 print "\n\t</body>\n</html>";
}
// -- print the html to close the page
function print_simple_footer() {
	 global $pgv_lang;
	 global $start_time, $buildindex;
	 global $VERSION, $SHOW_STATS;
	 global $SCRIPT_NAME, $QUERY_STRING;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;
	 if (empty($SCRIPT_NAME)) {
		  $SCRIPT_NAME = $_SERVER["SCRIPT_NAME"];
		  $QUERY_STRING = $_SERVER["QUERY_STRING"];
	 }
	 print "\n\t<br /><br /><div align=\"center\" style=\"width: 99%;\">";
	 print_contact_links();
	 print "\n\t<a href=\"http://www.phpgedview.net\" target=\"_blank\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["gedview"]["other"]."\" border=\"0\" alt=\"Bitweaver PhpGedView Version $VERSION\" title=\"Bitweaver PhpGedView Version $VERSION\" /></a><br />";
	 if ($SHOW_STATS || (isset($DEBUG) && ($DEBUG==true))) print_execution_stats();
	 print "</div>";
	 print "\n\t</body>\n</html>";
}

/**
 * Prints Exection Statistics
 *
 * prints out the execution time and the databse queries
 */
function print_execution_stats() {
	global $start_time, $pgv_lang, $TOTAL_QUERIES, $PRIVACY_CHECKS, $STARTMEM;
	$end_time = getmicrotime();
	$exectime = $end_time - $start_time;
	print "<br /><br />".$pgv_lang["exec_time"];
	printf(" %.3f ".$pgv_lang["sec"], $exectime);
	print "  ".$pgv_lang["total_queries"]." $TOTAL_QUERIES.";
	if (!$PRIVACY_CHECKS) $PRIVACY_CHECKS=0;
	print " ".$pgv_lang["total_privacy_checks"]." $PRIVACY_CHECKS.";
	if (function_exists("memory_get_usage")) {
		print " ".$pgv_lang["total_memory_usage"]." ";
		$mem = memory_get_usage()/1024;
		printf("%.2f", $mem);
		print " KB.";
	}
	print "<br />";
}

//-- print a form to change the language
function print_lang_form($option=0) {
	 global $ENABLE_MULTI_LANGUAGE, $pgv_lang, $pgv_language, $flagsfile, $LANGUAGE, $language_settings;
	 global $LANG_FORM_COUNT;
	 global $SCRIPT_NAME, $QUERY_STRING;
	 if ($ENABLE_MULTI_LANGUAGE) {
		  if (empty($LANG_FORM_COUNT)) $LANG_FORM_COUNT=1;
		  else $LANG_FORM_COUNT++;
		  print "\n\t<div class=\"lang_form\">\n";
		  switch($option) {
			   case 1:
			   //-- flags option
			   $i = 0;
			   foreach ($pgv_language as $key=>$value)
			   {
				 if (($key != $LANGUAGE) and ($language_settings[$key]["pgv_lang_use"]))
				 {
					$i ++;
					$flagid = "flag" . $i;
					print "<a href=\"$SCRIPT_NAME?$QUERY_STRING&amp;changelanguage=yes&amp;NEWLANGUAGE=$key\">";
					print "<img src=\"" . $flagsfile[$key] . "\" class=\"dimflag\" alt=\"" . $pgv_lang[$key]. "\" title=\"" . $pgv_lang[$key]. "\" onmouseover=\"change_class('".$flagid."','brightflag');\" onmouseout=\"change_class('".$flagid."','dimflag');\" id='".$flagid."' /></a>\n";
				 }
				 else
				 {
					if ($language_settings[$key]["pgv_lang_use"]) print "<img src=\"" . $flagsfile[$key] . "\" class=\"activeflag\" alt=\"" . $pgv_lang[$key]. "\" title=\"" . $pgv_lang[$key]. "\" />\n";
				 }
			   }
			   break;
			   default:
					print "<form name=\"langform$LANG_FORM_COUNT\" action=\"$SCRIPT_NAME";
					print "\" method=\"get\">";
					$vars = preg_split("/&amp;/", $QUERY_STRING);
					foreach($vars as $indexval => $var) {
						$parts = preg_split("/=/", $var);
						if (count($parts)>1) {
							if (($parts[0]!="changelanguage")&&($parts[0]!="NEWLANGUAGE"))
								print "\n\t\t<input type=\"hidden\" name=\"$parts[0]\" value=\"".urldecode($parts[1])."\" />";
						}
					}
					print "\n\t\t<input type=\"hidden\" name=\"changelanguage\" value=\"yes\" />\n\t\t<select name=\"NEWLANGUAGE\" class=\"header_select\" onchange=\"submit();\">";
					print "\n\t\t\t<option value=\"\">".$pgv_lang["change_lang"]."</option>";
					foreach ($pgv_language as $key=>$value) {
						 if ($language_settings[$key]["pgv_lang_use"]) {
							  print "\n\t\t\t<option value=\"$key\" ";
							  if ($LANGUAGE == $key) print "class=\"selected-option\"";
							  print ">".$pgv_lang[$key]."</option>";
						 }
					}
					print "</select>\n</form>\n";
			   break;
		  }
		  print "</div>";
	 }
}
/**
 * print user links
 *
 * this function will print login/logout links and other links based on user privileges
 */
function print_user_links() {
	 global $pgv_lang, $SCRIPT_NAME, $QUERY_STRING, $GEDCOM, $PRIV_USER, $PRIV_PUBLIC, $USE_REGISTRATION_MODULE, $pid;
	 global $SEARCH_SPIDER, $gBitUser;

	 if ( $gBitUser->mUserId > 0 ) {
		  print '<a href="'.$gBitUser->getDisplayUrl().'" class="link">Logged in as ('.$gBitUser->getDisplayName().')</a><br />';
		  if ( $gBitUser->isAdmin() ) print "<a href=\"admin/admin_gedcoms.php\" class=\"link\">".$pgv_lang["admin"]."</a> | ";
		print "<a href=\"../users/logout.php\" class=\"link\">".$pgv_lang["logout"]."</a>";
	 }
	 else {
		  if(empty($SEARCH_SPIDER)) {
		  	print "<a href=\"../users/login.php\" class=\"link\">".$pgv_lang["login"]."</a>";
		  }
	 }
	 print "<br />";
}
/**
 * print links for genealogy and technical contacts
 *
 * this function will print appropriate links based on the preferred contact methods for the genealogy
 * contact user and the technical support contact user
 */
function print_contact_links($style=0) {
	global $WEBMASTER_EMAIL, $SUPPORT_METHOD, $CONTACT_EMAIL, $CONTACT_METHOD, $pgv_lang;
	if ($SUPPORT_METHOD=="none" && $CONTACT_METHOD=="none") return array();
	if ($SUPPORT_METHOD=="none") $WEBMASTER_EMAIL = $CONTACT_EMAIL;
	if ($CONTACT_METHOD=="none") $CONTACT_EMAIL = $WEBMASTER_EMAIL;
	switch($style) {
		case 0:
			print "<div class=\"contact_links\">\n";
			//--only display one message if the contact users are the same
			if ($CONTACT_EMAIL==$WEBMASTER_EMAIL) {
				$user = getUser($WEBMASTER_EMAIL);
				if (($user)&&($SUPPORT_METHOD!="mailto")) {
					print $pgv_lang["for_all_contact"]." <a href=\"javascript:;\" accesskey=\"". $pgv_lang["accesskey_contact"] ."\" onclick=\"message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;\">".$user["firstname"]." ".$user["lastname"]."</a><br />\n";
				}
				else {
					print $pgv_lang["for_support"]." <a href=\"mailto:";
					if ($user) print $user["email"]."\" accesskey=\"". $pgv_lang["accesskey_contact"] ."\">".$user["firstname"]." ".$user["lastname"]."</a><br />\n";
					else print $WEBMASTER_EMAIL."\">".$WEBMASTER_EMAIL."</a><br />\n";
				}
			}
			//-- display two messages if the contact users are different
			else {
				  $user = getUser($CONTACT_EMAIL);
				  if (($user)&&($CONTACT_METHOD!="mailto")) {
					  print $pgv_lang["for_contact"]." <a href=\"javascript:;\" accesskey=\"". $pgv_lang["accesskey_contact"] ."\" onclick=\"message('$CONTACT_EMAIL', '$CONTACT_METHOD'); return false;\">".$user["firstname"]." ".$user["lastname"]."</a><br />\n";
				  }
				  else {
					   print $pgv_lang["for_contact"]." <a href=\"mailto:";
					   if ($user) print $user["email"]."\" accesskey=\"". $pgv_lang["accesskey_contact"] ."\">".$user["firstname"]." ".$user["lastname"]."</a><br />\n";
					   else print $CONTACT_EMAIL."\">".$CONTACT_EMAIL."</a><br />\n";
				  }
				  $user = getUser($WEBMASTER_EMAIL);
				  if (($user)&&($SUPPORT_METHOD!="mailto")) {
					  print $pgv_lang["for_support"]." <a href=\"javascript:;\" onclick=\"message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;\">".$user["firstname"]." ".$user["lastname"]."</a><br />\n";
				  }
				  else {
					   print $pgv_lang["for_support"]." <a href=\"mailto:";
					   if ($user) print $user["email"]."\">".$user["firstname"]." ".$user["lastname"]."</a><br />\n";
					   else print $WEBMASTER_EMAIL."\">".$WEBMASTER_EMAIL."</a><br />\n";
				  }
			}
			print "</div>\n";
			break;
		case 1:
			$menuitems = array();
			if ($CONTACT_EMAIL==$WEBMASTER_EMAIL) {
				$submenu = array();
				$user = getUser($WEBMASTER_EMAIL);
				if (($user)&&($SUPPORT_METHOD!="mailto")) {
					$submenu["label"] = $pgv_lang["support_contact"]." ".$user["firstname"]." ".$user["lastname"];
					$submenu["onclick"] = "message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;";
					$submenu["link"] = "#";
				}
				else {
					$submenu["label"] = $pgv_lang["support_contact"]." ";
					$submenu["link"] = "mailto:";
					if ($user) {
						$submenu["link"] .= $user["email"];
						$submenu["label"] .= $user["firstname"]." ".$user["lastname"];
					}
					else {
						$submenu["link"] .= $WEBMASTER_EMAIL;
						$submenu["label"] .= $WEBMASTER_EMAIL;
					}
				}
	            $submenu["label"] = $pgv_lang["support_contact"];
	            $submenu["labelpos"] = "right";
	            $submenu["class"] = "submenuitem";
	            $submenu["hoverclass"] = "submenuitem_hover";
	            $menuitems[] = $submenu;
			}
			else {
				$submenu = array();
				$user = getUser($CONTACT_EMAIL);
				if (($user)&&($CONTACT_METHOD!="mailto")) {
					$submenu["label"] = $pgv_lang["genealogy_contact"]." ".$user["firstname"]." ".$user["lastname"];
					$submenu["onclick"] = "message('$CONTACT_EMAIL', '$CONTACT_METHOD'); return false;";
					$submenu["link"] = "#";
				}
				else {
					$submenu["label"] = $pgv_lang["genealogy_contact"]." ";
					$submenu["link"] = "mailto:";
					if ($user) {
						$submenu["link"] .= $user["email"];
						$submenu["label"] .= $user["firstname"]." ".$user["lastname"];
					}
					else {
						$submenu["link"] .= $CONTACT_EMAIL;
						$submenu["label"] .= $CONTACT_EMAIL;
					}
				}
	            $submenu["labelpos"] = "right";
	            $submenu["class"] = "submenuitem";
	            $submenu["hoverclass"] = "submenuitem_hover";
	            $menuitems[] = $submenu;
	            $submenu = array();
				$user = getUser($WEBMASTER_EMAIL);
				if (($user)&&($SUPPORT_METHOD!="mailto")) {
					$submenu["label"] = $pgv_lang["support_contact"]." ".$user["firstname"]." ".$user["lastname"];
					$submenu["onclick"] = "message('$WEBMASTER_EMAIL', '$SUPPORT_METHOD'); return false;";
					$submenu["link"] = "#";
				}
				else {
					$submenu["label"] = $pgv_lang["support_contact"]." ";
					$submenu["link"] = "mailto:";
					if ($user) {
						$submenu["link"] .= $user["email"];
						$submenu["label"] .= $user["firstname"]." ".$user["lastname"];
					}
					else {
						$submenu["link"] .= $WEBMASTER_EMAIL;
						$submenu["label"] .= $WEBMASTER_EMAIL;
					}
				}
	            $submenu["labelpos"] = "right";
	            $submenu["class"] = "submenuitem";
	            $submenu["hoverclass"] = "submenuitem_hover";
	            $menuitems[] = $submenu;
	        }
            return $menuitems;
			break;
	}
}
//-- print user favorites
function print_favorite_selector_xx($option=0) {
	global $pgv_lang, $GEDCOM, $SCRIPT_NAME, $SHOW_ID_NUMBERS, $pid, $INDEX_DIRECTORY, $indilist, $famlist, $sourcelist, $medialist, $QUERY_STRING, $famid, $sid;
	global $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION, $PGV_IMAGE_DIR, $PGV_IMAGES, $SEARCH_SPIDER;
	$username = getUserName();
	if (!empty($username)) $userfavs = getUserFavorites($username);
	else {
		if ($REQUIRE_AUTHENTICATION) return false;
		$userfavs = array();
	}
	if (empty($pid)&&(!empty($famid))) $pid = $famid;
	if (empty($pid)&&(!empty($sid))) $pid = $sid;
	$gedcomfavs = array();
	if ((empty($username))&&(count($gedcomfavs)==0)) return;

	if(!empty($SEARCH_SPIDER)) {
	    return; // show no favorites, because they taint every page that is indexed.
	}

	print "<div class=\"favorites_form\">\n";
	switch($option) {
		case 1:
			$menu = array();
			$menu["label"] = $pgv_lang["favorites"];
			$menu["labelpos"] = "right";
			$menu["link"] = "#";
			$menu["class"] = "favmenuitem";
			$menu["hoverclass"] = "favmenuitem_hover";
			$menu["flyout"] = "down";
			$menu["submenuclass"] = "favsubmenu";
			$menu["items"] = array();
			$mygedcom = $GEDCOM;
			$current_gedcom = $GEDCOM;
			$mypid = $pid;
			if (count($userfavs)>0) {
				$submenu = array();
				$submenu["label"] = "<b>".$pgv_lang["my_favorites"]."</b>";
				$submenu["labelpos"] = "right";
				$submenu["link"] = "#";
				$submenu["class"] = "favsubmenuitem";
				$submenu["hoverclass"] = "favsubmenuitem_hover";
				$menu["items"][] = $submenu;
			}
			foreach($userfavs as $key=>$favorite) {
				$pid = $favorite["gid"];
				$current_gedcom = $GEDCOM;
				$GEDCOM = $favorite["file"];
				$submenu = array();
				if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
//					$submenu["link"] = $favorite["url"]."&amp;ged=$GEDCOM";
					$submenu["link"] = $favorite["url"];
					$submenu["label"] = PrintReady($favorite["title"]);
					$submenu["labelpos"] = "right";
					$submenu["class"] = "favsubmenuitem";
					$submenu["hoverclass"] = "favsubmenuitem_hover";
					$menu["items"][] = $submenu;
				}
				else {
					if (displayDetailsById($pid, $favorite["type"])) {
						$indirec = find_gedcom_record($pid);
						if ($favorite["type"]=="INDI") {
							$submenu["link"] = "individual.php?pid=".$favorite["gid"]."&amp;ged=$GEDCOM";
							$submenu["label"] = PrintReady(get_person_name($favorite["gid"]));
							if ($SHOW_ID_NUMBERS) {
	 							if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
								else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							unset($indilist[$pid]);
						}
						if ($favorite["type"]=="FAM") {
							$submenu["link"] = "family.php?famid=".$favorite["gid"]."&amp;ged=$GEDCOM";
							$submenu["label"] = PrintReady(get_family_descriptor($favorite["gid"]));
							if ($SHOW_ID_NUMBERS) {
	 							if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
								else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							unset($famlist[$pid]);
						}
						if ($favorite["type"]=="SOUR") {
							$submenu["link"] = "source.php?sid=".$favorite["gid"]."&amp;ged=$GEDCOM";
							$submenu["label"] = PrintReady(get_source_descriptor($favorite["gid"]));
							if ($SHOW_ID_NUMBERS) {
	 							if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
								else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							unset($sourcelist[$pid]);
						}
						if ($favorite["type"]=="OBJE") {
							$media = Media::getInstance($pid);
							if (!is_null($media)) {
								$submenu["link"] = "mediaviewer.php?mid=".$favorite["gid"]."&amp;ged=$GEDCOM";
								$submenu["label"] = PrintReady($media->getTitle());
								if ($SHOW_ID_NUMBERS) {
		 							if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
								}
								if (isset($medialist[$pid])) unset($medialist[$pid]);
							}
						}
						$submenu["labelpos"] = "right";
						$submenu["class"] = "favsubmenuitem";
						$submenu["hoverclass"] = "favsubmenuitem_hover";
						$menu["items"][] = $submenu;
					}
				}
			}
			$pid = $mypid;
			$GEDCOM = $mygedcom;
			if ((!empty($username))&&(strpos($_SERVER["SCRIPT_NAME"], "individual.php")!==false)) {
				$menu["items"][]="separator";
				$submenu = array();
				$submenu["label"] = $pgv_lang["add_to_my_favorites"];
				$submenu["labelpos"] = "right";
				$submenu["link"] = "individual.php?action=addfav&amp;gid=$pid&amp;pid=$pid";
				$submenu["class"] = "favsubmenuitem";
				$submenu["hoverclass"] = "favsubmenuitem_hover";
				$menu["items"][] = $submenu;
		   }
		   if (count($gedcomfavs)>0) {
				$menu["items"][]="separator";
				$submenu = array();
				$submenu["label"] = "<b>".$pgv_lang["gedcom_favorites"]."</b>";
				$submenu["labelpos"] = "right";
				$submenu["link"] = "#";
				$submenu["class"] = "favsubmenuitem";
				$submenu["hoverclass"] = "favsubmenuitem_hover";
				$menu["items"][] = $submenu;
				$current_gedcom = $GEDCOM;
				foreach($gedcomfavs as $key=>$favorite) {
					$GEDCOM = $favorite["file"];
					$pid = $favorite["gid"];
					$submenu = array();
					if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
//						$submenu["link"] = $favorite["url"]."&amp;ged=$GEDCOM";
						$submenu["link"] = $favorite["url"];
						$submenu["label"] = PrintReady($favorite["title"]);
						$submenu["labelpos"] = "right";
						$submenu["class"] = "favsubmenuitem";
						$submenu["hoverclass"] = "favsubmenuitem_hover";
						$menu["items"][] = $submenu;
					}
					else {
						if (displayDetailsById($pid, $favorite["type"])) {
							$indirec = find_gedcom_record($pid);
							if ($favorite["type"]=="INDI") {
								$submenu["link"] = "individual.php?pid=".$favorite["gid"]."&amp;ged=$GEDCOM";
								$submenu["label"] = PrintReady(get_person_name($favorite["gid"]));
								if ($SHOW_ID_NUMBERS) {
	 								if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							}
							if ($favorite["type"]=="FAM") {
								$submenu["link"] = "family.php?famid=".$favorite["gid"]."&amp;ged=$GEDCOM";
								$submenu["label"] = PrintReady(get_family_descriptor($favorite["gid"]));
								if ($SHOW_ID_NUMBERS) {
	 								if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							}
							if ($favorite["type"]=="SOUR") {
								$submenu["link"] = "source.php?sid=".$favorite["gid"]."&amp;ged=$GEDCOM";
								$submenu["label"] = PrintReady(get_source_descriptor($favorite["gid"]));
								if ($SHOW_ID_NUMBERS) {
	 								if ($TEXT_DIRECTION=="ltr") $submenu["label"] .= " (".$favorite["gid"].")";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							}
							$submenu["labelpos"] = "right";
							$submenu["class"] = "favsubmenuitem";
							$submenu["hoverclass"] = "favsubmenuitem_hover";
							$menu["items"][] = $submenu;
						}
					}
				}
				$pid = $mypid;
				$GEDCOM = $mygedcom;
				print_menu($menu);
		  	}
			break;
		default:
			   print "<form name=\"favoriteform\" action=\"$SCRIPT_NAME";
			   print "\" method=\"post\" onsubmit=\"return false;\">";
			   print "\n\t\t<select name=\"fav_id\" class=\"header_select\" onchange=\"if (document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value!='') window.location=document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value; if (document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value=='add') window.location='$SCRIPT_NAME?$QUERY_STRING&amp;action=addfav&amp;gid=$pid&amp;pid=$pid';\">";
				print "\n\t\t\t<option value=\"\">".$pgv_lang["favorites"]."</option>\n";
			if (!empty($username)) {
				if (count($userfavs)>0 || (strpos($_SERVER["SCRIPT_NAME"], "individual.php")!==false || strpos($_SERVER["SCRIPT_NAME"], "family.php")!==false || strpos($_SERVER["SCRIPT_NAME"], "source.php")!==false)) {
					print "\n\t\t\t<optgroup label=\"".$pgv_lang["my_favorites"]."\">";
				}
					$mygedcom = $GEDCOM;
					$current_gedcom = $GEDCOM;
					$mypid = $pid;
				if (strpos($_SERVER["SCRIPT_NAME"], "individual.php")!==false || strpos($_SERVER["SCRIPT_NAME"], "family.php")!==false || strpos($_SERVER["SCRIPT_NAME"], "source.php")!==false) {
					print "<option value=\"add\">- ".$pgv_lang["add_to_my_favorites"]." -</option>\n";
				}
					foreach($userfavs as $key=>$favorite) {
						 $current_gedcom = $GEDCOM;
						$GEDCOM = $favorite["file"];
						$pid = $favorite["gid"];
						if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
//							print "\n\t\t\t\t<option value=\"".$favorite["url"]."&amp;ged=".$GEDCOM."\">".PrintReady($favorite["title"]);
							print "\n\t\t\t\t<option value=\"".$favorite["url"]."\">".PrintReady($favorite["title"]);
							print "</option>";
						}
						else {
							if (displayDetailsById($pid, $favorite["type"])) {
								$indirec = find_gedcom_record($pid);
								$name = $pgv_lang["unknown"];
								if ($favorite["type"]=="INDI") {
									$name = strip_tags(PrintReady(get_person_name($pid)));
								if ($SHOW_ID_NUMBERS) {
									if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
									else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
								}
								print "\n\t\t\t\t<option value=\"individual.php?pid=";
								   unset($indilist[$pid]);
								}
								if ($favorite["type"]=="FAM") {
									$name = strip_tags(PrintReady(get_family_descriptor($pid)));
									if (strlen($name)>50) $name = substr($name, 0, 50);
								if ($SHOW_ID_NUMBERS) {
									if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
									else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
								}
								print "\n\t\t\t\t<option value=\"family.php?famid=";
								   unset($famlist[$pid]);
								}
								if ($favorite["type"]=="SOUR") {
									$name = strip_tags(PrintReady(get_source_descriptor($pid)));
									if (strlen($name)>50) $name = substr($name, 0, 50);
								if ($SHOW_ID_NUMBERS) {
									if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
									else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
								}
								print "\n\t\t\t\t<option value=\"source.php?sid=";
								   unset($sourcelist[$pid]);
								}
								if ($favorite["type"]=="OBJE") {
									$media = Media::getInstance($pid);
									if (!is_null($media)) {
										$name = strip_tags(PrintReady($media->getTitle()));
										if (strlen($name)>50) $name = substr($name, 0, 50);
										if ($SHOW_ID_NUMBERS) {
											if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
											else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
										}
										print "\n\t\t\t\t<option value=\"mediaviewer.php?mid=";
										unset($sourcelist[$pid]);
									}
								}
							print $favorite["gid"]."&amp;ged=".$GEDCOM."\">".$name."</option>";
							}
						}
					}
					if (count($userfavs)>0 || (strpos($_SERVER["SCRIPT_NAME"], "individual.php")!==false || strpos($_SERVER["SCRIPT_NAME"], "family.php")!==false || strpos($_SERVER["SCRIPT_NAME"], "source.php")!==false)) {
						print "\n\t\t\t</optgroup>";
					}
					$GEDCOM = $mygedcom;
					$pid = $mypid;
			   }
			   if (count($gedcomfavs)>0) {
				print "\n\t\t\t<optgroup label=\"".$pgv_lang["gedcom_favorites"]."\">\n";
					$mygedcom = $GEDCOM;
					$current_gedcom = $GEDCOM;
					$mypid = $pid;
					foreach($gedcomfavs as $key=>$favorite) {
						$current_gedcom = $GEDCOM;
						$GEDCOM = $favorite["file"];
						$pid = $favorite["gid"];
						if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
//							print "\n\t\t\t\t<option value=\"".$favorite["url"]."&amp;ged=".$GEDCOM."\">".PrintReady($favorite["title"]);
							print "\n\t\t\t\t<option value=\"".$favorite["url"]."\">".PrintReady($favorite["title"]);
							print "</option>";
						}
						else {
						$indirec = find_gedcom_record($pid);
						$name = $pgv_lang["unknown"];
						if (displayDetailsById($pid, $favorite["type"])) {
							if ($favorite["type"]=="INDI") {
								$name = strip_tags(PrintReady(get_person_name($pid)));
							if ($SHOW_ID_NUMBERS) {
								if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
								else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							print "\n\t\t\t\t<option value=\"individual.php?pid=";
							}
							if ($favorite["type"]=="FAM") {
								$name = strip_tags(PrintReady(get_family_descriptor($pid)));
							if ($SHOW_ID_NUMBERS) {
								if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
								else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							print "\n\t\t\t\t<option value=\"family.php?famid=";
							}
							if ($favorite["type"]=="SOUR") {
								$name = strip_tags(PrintReady(get_source_descriptor($pid)));
							if ($SHOW_ID_NUMBERS) {
								if ($TEXT_DIRECTION=="ltr") $name .= " (".$favorite["gid"].")";
								else $name .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							print "\n\t\t\t\t<option value=\"source.php?sid=";
						}
						print $favorite["gid"]."&amp;ged=$GEDCOM\">".$name."</option>";
						}
						}
					}
				print "\n\t\t\t</optgroup>";
					$GEDCOM = $mygedcom;
					$pid = $mypid;
			   }
			   print "</select>\n\t</form>\n";
			   break;
	 }
	 print "</div>\n";
}

/**
 * print a simple form of the fact
 *
 * function to print the details of a fact in a simple format
 * @param string $indirec the gedcom record to get the fact from
 * @param string $fact the fact to print
 * @param string $pid the id of the individual to print, required to check privacy
 */
function print_simple_fact($indirec, $fact, $pid) {
	global $pgv_lang, $SHOW_PEDIGREE_PLACES, $factarray, $ABBREVIATE_CHART_LABELS;
	$emptyfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","BAPL","CONL","ENDL","SLGC","EVEN","MARR","SLGS","MARL","ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARS","OBJE","CHAN","_SEPR","RESI", "DATA", "MAP");
	$factrec = get_sub_record(1, "1 $fact", $indirec);
	if ((empty($factrec))||(FactViewRestricted($pid, $factrec))) return;
	$label = "";
	if (isset($pgv_lang[$fact])) $label = $pgv_lang[$fact];
	else if (isset($factarray[$fact])) $label = $factarray[$fact];
	if ($ABBREVIATE_CHART_LABELS) $label = get_first_letter($label);
// RFE [ 1229233 ] "DEAT" vs "DEAT Y"
// The check $factrec != "1 DEAT" will not show any records that only have 1 DEAT in them
	if (trim($factrec) != "1 DEAT"){
	   print "<span class=\"details_label\">".$label."</span> ";
	}
	if (showFactDetails($fact, $pid)) {
		if (!in_array($fact, $emptyfacts)) {
			$ct = preg_match("/1 $fact(.*)/", $factrec, $match);
			if ($ct>0) print PrintReady(trim($match[1]));
		}
		// 1 DEAT Y with no DATE => print YES
		// 1 DEAT N is not allowed
		// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
		/*-- handled by print_fact_date()
		 * if (get_sub_record(2, "2 DATE", $factrec)=="") {
			if (strtoupper(trim(substr($factrec,6,2)))=="Y") print $pgv_lang["yes"];
		}*/
		print_fact_date($factrec, false, false, $fact, $pid, $indirec);
		print_fact_place($factrec);
	}
	print "<br />\n";
}

/**
 * print a note record
 * @param string $text
 * @param int $nlevel	the level of the note record
 * @param string $nrec	the note record to print
 * @return boolean
 */
function print_note_record($text, $nlevel, $nrec) {
	global $pgv_lang;
	global $PGV_IMAGE_DIR, $PGV_IMAGES;
	$elementID = "N-".floor(microtime()*1000000);
	$text = preg_replace("/~~/", "<br />", trim($text));
	$text .= get_cont($nlevel, $nrec);
	$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">URL</a>", $text);
	$text = trim($text);
	if (!empty($text)) {
		$text = PrintReady($text);
		$brpos = strpos($text, "<br />");
		print "\n\t\t<br /><span class=\"label\">";
		if ($brpos !== false) print "<a href=\"javascript:;\" onclick=\"expand_layer('$elementID'); return false;\"><img id=\"{$elementID}_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".$pgv_lang["show_details"]."\" title=\"".$pgv_lang["show_details"]."\" /></a> ";
		print $pgv_lang["note"].": </span><span class=\"field\">";
		if ($brpos !== false) {
			print substr($text, 0, $brpos);
			print "<span id=\"$elementID\" class=\"note_details\">";
			print substr($text, $brpos + 6);
			print "</span>";
		} else {
			print $text;
		}
		return true;
	}
	return false;
}

/**
 * Print all of the notes in this fact record
 * @param string $factrec	the factrecord to print the notes from
 * @param int $level		The level of the factrecord
 */
function print_fact_notes($factrec, $level) {
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES;
	 $nlevel = $level+1;
	 $ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, $match[$j][0]);
		  $spos2 = strpos($factrec, "\n$level", $spos1+1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $nrec = substr($factrec, $spos1, $spos2-$spos1);
		  if (!isset($match[$j][1])) $match[$j][1]="";
		  $nt = preg_match("/@(.*)@/", $match[$j][1], $nmatch);
		  $closeSpan = false;
		  if ($nt==0) {
			   //-- print embedded note records
				$closeSpan = print_note_record($match[$j][1], $nlevel, $nrec);
		  }
		  else {
		  	if (displayDetailsByID($nmatch[1], "NOTE")) {
			   //-- print linked note records
			   $noterec = find_gedcom_record($nmatch[1]);
			   $nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
			   $closeSpan = print_note_record(($nt>0)?$n1match[1]:"", 1, $noterec);
		   		if (preg_match("/1 SOUR/", $noterec)>0) {
			   		print "<br />\n";
					print_fact_sources($noterec, 1);
				}
		  	}
		  }
		  if (preg_match("/$nlevel SOUR/", $factrec)>0) {
		  	print "<div class=\"indent\">";
		  	print_fact_sources($nrec, $nlevel);
		  	print "</div>";
	  	  }
	  	  if($closeSpan){
	  		print "</span>";
	  	  }
	 }
}
/**
 * print a gedcom title linked to the gedcom portal
 *
 * This function will print the HTML to link the current gedcom title back to the
 * gedcom portal welcome page
 * @author John Finlay
 */
function print_gedcom_title_link($InHeader=FALSE) {
	 global $GEDCOMS, $GEDCOM;
	 if ((count($GEDCOMS)==0)||(empty($GEDCOM))) return;
	 if (isset($GEDCOMS[$GEDCOM])) print "<a href=\"index.php?command=gedcom\" class=\"gedcomtitle\">".PrintReady($GEDCOMS[$GEDCOM]["title"], $InHeader)."</a>";
}

//-- function to print a privacy error with contact method
function print_privacy_error($username) {
	 global $pgv_lang, $CONTACT_METHOD, $SUPPORT_METHOD, $WEBMASTER_EMAIL;
	 $method = $CONTACT_METHOD;
	 if ($username==$WEBMASTER_EMAIL) $method = $SUPPORT_METHOD;
	 $user = getUser($username);
	 if (!$user) $method = "mailto";
	 print "<br /><span class=\"error\">".$pgv_lang["privacy_error"];
	 if ($method=="none") {
		  print "</span><br />\n";
		  return;
	 }
	 print $pgv_lang["more_information"];
	 if ($method=="mailto") {
		  if (!$user) {
			   $email = $username;
			   $fullname = $username;
		  }
		  else {
			   $email = $user["email"];
			   $fullname = $user["firstname"]." ".$user["lastname"];
		  }
		  print " <a href=\"mailto:$email\">".$fullname."</a></span><br />";
	 }
	 else {
		  print " <a href=\"javascript:;\" onclick=\"message('$username','$method'); return false;\">".$user["firstname"]." ".$user["lastname"]."</a></span><br />";
	 }
}

/* Function to print popup help boxes
 * @param string $help		The variable that needs to be processed.
 * @param int $helpText		The text to be printed if the theme does not use images for help links
 * @param int $show_desc		The text to be shown as JavaScript description
 * @param boolean $use_print_text	If the text needs to be printed with the print_text() function
 * @param boolean $output	return the text instead of printing it
 */
function print_help_link($help, $helpText, $show_desc="", $use_print_text=false, $return=false) {
	global $SHOW_CONTEXT_HELP, $pgv_lang,$view, $PGV_USE_HELPIMG, $PGV_IMAGES, $PGV_IMAGE_DIR, $gBitUser;

	if ($PGV_USE_HELPIMG) $sentense = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["help"]["small"]."\" class=\"icon\" width=\"15\" height=\"15\" alt=\"\" />";
	else $sentense = $pgv_lang[$helpText];
	$output = "";
	if (($view!="preview")&&($_SESSION["show_context_help"])){
		if ($helpText=="qm_ah"){
			if ($gBitUser->isAdmin()){
				 $output .= " <a class=\"error help\" tabindex=\"0\" href=\"javascript:// ";
				 if ($show_desc == "") $output .= $help;
				 else if ($use_print_text) $output .= print_text($show_desc, 0, 1);
				 else if (stristr($pgv_lang[$show_desc], "\"")) $output .= preg_replace('/\"/','\'',$pgv_lang[$show_desc]);
				 else  $output .= strip_tags($pgv_lang[$show_desc]);
				 $output .= "\" onclick=\"helpPopup('$help'); return false;\">".$sentense."</a> \n";
			}
		}
		else {
			$output .= " <a class=\"help\" tabindex=\"0\" href=\"javascript:// ";
			if ($show_desc == "") $output .= $help;
			else if ($use_print_text) $output .= print_text($show_desc, 0, 1);
			else if (stristr($pgv_lang[$show_desc], "\"")) $output .= preg_replace('/\"/','\'',$pgv_lang[$show_desc]);
			else  $output .= strip_tags($pgv_lang[$show_desc]);
			$output .= "\" onclick=\"helpPopup('$help'); return false;\">".$sentense."</a> \n";
		}
	}
	if (!$return) print $output;
	return $output;
}

/**
 * print a language variable
 *
 * It accepts any kind of language variable. This can be a single variable but also
 * a variable with included variables that needs to be converted.
 * print_text, which used to be called print_help_text, now takes 3 parameters
 *		of which only the 1st is mandatory
 * The first parameter is the variable that needs to be processed.  At nesting level zero,
 *		this is the name of a $pgv_lang array entry.  "whatever" refers to
 *		$pgv_lang["whatever"].  At nesting levels greater than zero, this is the name of
 *		any global variable, but *without* the $ in front.  For example, VERSION or
 *		pgv_lang["whatever"] or factarray["rowname"].
 * The second parameter is $level for the nested vars in a sentence.  This indicates
 *		that the function has been called recursively.
 * The third parameter $noprint is for returning the text instead of printing it
 *		This parameter, when set to 2 means, in addition to NOT printing the result,
 *		the input string $help is text that needs to be interpreted instead of being
 *		the name of a $pgv_lang array entry.  This lets you use this function to work
 *		on something other than $pgv_lang array entries, but coded according to the
 *		same rules.
 * When we want it to return text we need to code:
 * print_text($mytext, 0, 1);
 * @param string $help		The variable that needs to be processed.
 * @param int $level		The position of the embedded variable
 * @param int $noprint		The switch if the text needs to be printed or returned
 */
function print_text($help, $level=0, $noprint=0){
	 global $pgv_lang, $factarray, $VERSION, $COMMON_NAMES_THRESHOLD;
	 global $INDEX_DIRECTORY, $GEDCOMS, $GEDCOM, $GEDCOM_TITLE, $LANGUAGE;
	 global $GUESS_URL, $UpArrow, $DAYS_TO_SHOW_LIMIT, $MEDIA_DIRECTORY;
	 global $repeat, $thumbnail, $xref, $pid;
	 if (!isset($_SESSION["DEBUG_LANG"])) $DEBUG_LANG = "no";
	 else $DEBUG_LANG = $_SESSION["DEBUG_LANG"];
	 if ($DEBUG_LANG == "yes") print "[LANG_DEBUG] Variable called: ".$help."<br /><br />";
	 $sentence = "";
	 if ($level>0) {
		  $value ="";
		  eval("if (!empty(\$$help)) \$value = \$$help;");
		  if (empty($value)) return "";
		  $sentence = $value;
	 }
	 if (empty($sentence)) {
		  if ($noprint == 2) {
			  $sentence = $help;
	  	  }
	  	  else if (!empty($pgv_lang[$help])) $sentence = $pgv_lang[$help];
		  else {
			if ($DEBUG_LANG == "yes") print "[LANG_DEBUG] Variable not present: ".$help."<br /><br />";
		  	$sentence = $pgv_lang["help_not_exist"];
		  }
	 }
	 $mod_sentence = "";
	 $replace = "";
	 $replace_text = "";
	 $sub = "";
	 $pos1 = 0;
	 $pos2 = 0;
	 $ct = preg_match_all("/#([a-zA-Z0-9_.\-\[\]]+)#/", $sentence, $match, PREG_SET_ORDER);
	 for($i=0; $i<$ct; $i++) {
		  $value = "";
		  $newreplace = preg_replace(array("/\[/","/\]/"), array("['","']"), $match[$i][1]);
		  if ($DEBUG_LANG == "yes") print "[LANG_DEBUG] Embedded variable: ".$match[$i][1]."<br /><br />";
		  $value = print_text($newreplace, $level+1);
		  if (!empty($value)) $sentence = str_replace($match[$i][0], $value, $sentence);
		  else if ($noprint==0 && $level==0) $sentence = str_replace($match[$i][0], $match[$i][1].": ".$pgv_lang["var_not_exist"], $sentence);
	 }
	 // ------ Replace paired ~  by tag_start and tag_end (those vars contain CSS classes)
	 while (stristr($sentence, "~") == TRUE){
		  $pos1 = strpos($sentence, "~");
		  $mod_sentence = substr_replace($sentence, " ", $pos1, 1);
		  if (stristr($mod_sentence, "~")){		// If there's a second one:
			  $pos2 = strpos($mod_sentence, "~");
			  $replace = substr($sentence, ($pos1+1), ($pos2-$pos1-1));
			  $replace_text = "<span class=\"helpstart\">".str2upper($replace)."</span>";
			  $sentence = str_replace("~".$replace."~", $replace_text, $sentence);
		  } else break;
	 }
	 if ($noprint>0) return $sentence;
	 if ($level>0) return $sentence;
	 print $sentence;
}
function print_help_index($help){
	 global $pgv_lang;
	 $sentence = $pgv_lang[$help];
	 $mod_sentence = "";
	 $replace = "";
	 $replace_text = "";
	 $sub = "";
	 $pos1 = 0;
	 $pos2 = 0;
	 $admcol=false;
	 $ch=0;
	 $help_sorted = array();
	 $var="";
	 while (stristr($sentence, "#") == TRUE){
		$pos1 = strpos($sentence, "#");
		$mod_sentence = substr_replace($sentence, " ", $pos1, 1);
		$pos2 = strpos($mod_sentence, "#");
		$replace = substr($sentence, ($pos1+1), ($pos2-$pos1-1));
		$sub = preg_replace(array("/pgv_lang\\[/","/\]/"), array("",""), $replace);
		if (isset($pgv_lang[$sub])) {
			$items = preg_split("/,/", $pgv_lang[$sub]);
			$var = $pgv_lang[$items[1]];
		}
		$sub = preg_replace(array("/factarray\\[/","/\]/"), array("",""), $replace);
		if (isset($factarray[$sub])) {
			$items = preg_split("/,/", $factarray[$sub]);
			$var = $factarray[$items[1]];
		}
		if (substr($var,0,1)=="_") {
			$admcol=true;
			$ch++;
		}
		   $replace_text = "<a href=\"help_text.php?help=".$items[0]."\">".$var."</a><br />";
		   $help_sorted[$replace_text] = $var;
		   $sentence = str_replace("#".$replace."#", $replace_text, $sentence);
	 }
	 uasort($help_sorted, "stringsort");
	 if ($ch==0) $ch=count($help_sorted);
	 else $ch +=$ch;
	 if ($ch>0) print "<table width=\"100%\"><tr><td style=\"vertical-align: top;\"><ul>";
	 $i=0;
	 foreach ($help_sorted as $k => $help_item){
		print "<li>".$k."</li>";
		$i++;
		if ($i==ceil($ch/2)) print "</ul></td><td style=\"vertical-align: top;\"><ul>";
	 }
	 if ($ch>0) print "</ul></td></tr></table>";
}
/**
 * prints a JavaScript popup menu
 *
 * This function will print the DHTML required
 * to create a JavaScript Popup menu.  The $menu
 * parameter is an array that looks like this
 * $menu["label"] = "Charts";
 * $menu["labelpos"] = "down"; // tells where the text should be positioned relative to the picture options are up down left right
 * $menu["icon"] = "images/pedigree.gif";
 * $menu["hovericon"] = "images/pedigree2.gif";
 * $menu["link"] = "pedigree.php";
 * $menu["accesskey"] = "Z"; // optional accesskey
 * $menu["class"] = "menuitem";
 * $menu["hoverclass"] = "menuitem_hover";
 * $menu["flyout"] = "down"; // options are up down left right
 * $menu["items"] = array(); // an array of like menu items
 * $menu["onclick"] = "return javascript";  // java script to run on click
 * @author John Finlay
 * @param array $menu the menuitems array to print
 */
function print_menu($menu, $parentmenu="") {
	include_once 'includes/menu.php';
	$conv = array(
		'label'=>'label',
		'labelpos'=>'labelpos',
		'icon'=>'icon',
		'hovericon'=>'hovericon',
		'link'=>'link',
		'accesskey'=>'accesskey',
		'class'=>'class',
		'hoverclass'=>'hoverclass',
		'flyout'=>'flyout',
		'submenuclass'=>'submenuclass',
		'onclick'=>'onclick'
	);
	$obj = new Menu();
	if ($menu == 'separator') {
		$obj->isSeperator();
		$obj->printMenu();
		return;
	}
	$items = false;
	foreach ($menu as $k=>$v) {
		if ($k == 'items' && is_array($v) && count($v) > 0) $items = $v;
		else {
			if (isset($conv[$k])){
				if ($v != '') {
					$obj->$conv[$k] = $v;
				}
			}
		}
	}
	if ($items !== false) {
		foreach ($items as $sub) {
			$sobj = new Menu();
			if ($sub == 'separator') {
				$sobj->isSeperator();
				$obj->addSubmenu($sobj);
				continue;
			}
			foreach ($sub as $k2=>$v2) {
				if (isset($conv[$k2])) {
					if ($v2 != '') {
						$sobj->$conv[$k2] = $v2;
					}
				}
			}
			$obj->addSubmenu($sobj);
		}
	}
	$obj->printMenu();
}

//-------------------------------------------------------------------------------------------------------------
// switches between left and rigth align on chosen text direction
//-------------------------------------------------------------------------------------------------------------
function write_align_with_textdir_check($t_dir)
{
  global $TEXT_DIRECTION;
  if ($t_dir == "left")
  {
	 if ($TEXT_DIRECTION == "ltr")
	 {
	   print " style=\"text-align:left; \" ";
	 }
	 else
	 {
	   print " style=\"text-align:right; \" ";
	 }
  }
  else
  {
	 if ($TEXT_DIRECTION == "ltr")
	 {
	   print " style=\"text-align:right; \" ";
	 }
	 else
	 {
	   print " style=\"text-align:left; \" ";
	 }
  }
}
//-- print theme change dropdown box
function print_theme_dropdown($style=0) {
	 global $ALLOW_THEME_DROPDOWN, $ALLOW_USER_THEMES, $THEME_DIR, $pgv_lang, $themeformcount;
	 if ($ALLOW_THEME_DROPDOWN && $ALLOW_USER_THEMES) {
		  if (!isset($themeformcount)) $themeformcount = 0;
		  $themeformcount++;
		  $uname = getUserName();
		  $user = getUser($uname);
		  isset($_SERVER["QUERY_STRING"]) == true?$tqstring = "?".$_SERVER["QUERY_STRING"]:$tqstring = "";
		  $frompage = $_SERVER["SCRIPT_NAME"].$tqstring;
		  if(isset($_REQUEST['mod'])){
		  	if(!strstr("?", $frompage))
		  	{
		  		if(!strstr("%3F", $frompage)) ;
		  		else $frompage.="?";
		  	}
		  	if(!strstr("&mod",$frompage))$frompage.="&mod=".$_REQUEST['mod'];
		  }

		  $themes = get_theme_names();
		  print "<div class=\"theme_form\">\n";
		  $module = "";
		  switch ($style) {
			   case 0:
			   print "<form action=\"themechange.php\" name=\"themeform$themeformcount\" method=\"post\">";
			   print "<input type=\"hidden\" name=\"frompage\" value=\"".urlencode($frompage)."\" />";
			   print "<select name=\"mytheme\" class=\"header_select\" onchange=\"document.themeform$themeformcount.submit();\">";
			   print "<option value=\"\">".$pgv_lang["change_theme"]."</option>\n";
			   foreach($themes as $indexval => $themedir) {
					print "<option value=\"".$themedir["dir"]."\"";
					if ($uname) {
						 if ($themedir["dir"] == $user["theme"]) print " class=\"selected-option\"";
					} else {
						  if ($themedir["dir"] == $THEME_DIR) print " class=\"selected-option\"";
					}
					print ">".$themedir["name"]."</option>\n";
			   }
			   print "</select></form>";
			   break;
			   case 1:
					$menu = array();
					$menu["label"] = $pgv_lang["change_theme"];
					$menu["labelpos"] = "left";
					$menu["link"] = "#";
					$menu["class"] = "thememenuitem";
					$menu["hoverclass"] = "thememenuitem_hover";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "themesubmenu";
					$menu["items"] = array();
					foreach($themes as $indexval => $themedir) {
						$submenu = array();
						$submenu["label"] = $themedir["name"];
						$submenu["labelpos"] = "right";
						$submenu["link"] = "themechange.php?frompage=".urlencode($frompage)."&amp;mytheme=".$themedir["dir"];
						$submenu["class"] = "favsubmenuitem";
						if ($uname) {
							 if ($themedir["dir"] == $user["theme"]) $submenu["class"] = "favsubmenuitem_selected";
						} else {
							 if ($themedir["dir"] == $THEME_DIR) $submenu["class"] = "favsubmenuitem_selected";
						}
						$submenu["hoverclass"] = "favsubmenuitem_hover";
						$menu["items"][] = $submenu;
					}
					print_menu($menu);
			   break;
		  }
		  print "</div>\n";
	 }
	 else {
		  print "&nbsp;";
	 }
}

/**
 * Prepare text with parenthesis for printing
 * Convert & to &amp; for xhtml compliance
 *
 * @param string $text to be printed
 */
function PrintReady($text, $InHeaders=false) {
	global $query, $action, $firstname, $lastname, $place, $year, $DEBUG;
	global $TEXT_DIRECTION_array;
	// Check whether Search page highlighting should be done or not
	$HighlightOK = false;
	if (strstr($_SERVER["SCRIPT_NAME"], "search.php")) {	// If we're on the Search page
		if (!$InHeaders) {								//   and also in page body
			if ((isset($query) and ($query != "")) ) {		//   and the query isn't blank
				$HighlightOK = true;					// It's OK to mark search result
			}
		}
	}
	//-- convert all & to &amp;
	$text = preg_replace("/&/", "&amp;", $text);
	//-- make sure we didn't double convert &amp; to &amp;amp;
	$text = preg_replace("/&amp;(\w+);/", "&$1;", $text);
    $text = trim($text);
    //-- if we are on the search page body, then highlight any search hits
    //		In this routine, we will assume that the input string doesn't contain any
    //		\x01 or \x02 characters.  We'll represent the <span class="search_hit"> by \x01
    //		and </span> by \x02.  We will translate these \x01 and \x02 into their true
    //		meaning at the end.
    //
    //		This special handling is required in case the user has submitted a multiple
    //		argument search, in which the second or later arguments can be found in the
    //		<span> or </span> strings.
    if ($HighlightOK) {
			if (isset($query)) {
				$queries = explode(" ", $query);
				$newtext = $text;
				$hasallhits = true;
				foreach($queries as $index=>$query1) {
					$query1esc=addcslashes($query1, '/');
					if (preg_match("/(".$query1esc.")/i", $text)) {
						$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
					}
					else if (preg_match("/(".str2upper($query1esc).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
						$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
						$newtext = substr_replace($newtext, "\x01", $npos, 0);
					}
					else $hasallhits = false;
				}
				if ($hasallhits) $text = $newtext;
			}
			if (isset($action) && ($action === "soundex")) {
				if (isset($firstname)) {
					$queries = explode(" ", $firstname);
					$newtext = $text;
					$hasallhits = true;
					foreach($queries as $index=>$query1) {
						$query1esc=addcslashes($query1, '/');
						if (preg_match("/(".$query1esc.")/i", $text)) {
							$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
						}
						else if (preg_match("/(".str2upper($query1esc).")/", str2upper($text))) {
							$nlen = strlen($query1);
							$npos = strpos(str2upper($text), str2upper($query1));
							$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
							$newtext = substr_replace($newtext, "\x01", $npos, 0);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
				if (isset($lastname)) {
					$queries = explode(" ", $lastname);
					$newtext = $text;
					$hasallhits = true;
					foreach($queries as $index=>$query1) {
						$query1esc=addcslashes($query1, '/');
						if (preg_match("/(".$query1esc.")/i", $text)) {
							$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
						}
						else if (preg_match("/(".str2upper($query1esc).")/", str2upper($text))) {
							$nlen = strlen($query1);
							$npos = strpos(str2upper($text), str2upper($query1));
							$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
							$newtext = substr_replace($newtext, "\x01", $npos, 0);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
				if (isset($place)) {
					$queries = explode(" ", $place);
					$newtext = $text;
					$hasallhits = true;
					foreach($queries as $index=>$query1) {
						$query1esc=addcslashes($query1, '/');
						if (preg_match("/(".$query1esc.")/i", $text)) {
							$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
						}
						else if (preg_match("/(".str2upper($query1esc).")/", str2upper($text))) {
							$nlen = strlen($query1);
							$npos = strpos(str2upper($text), str2upper($query1));
							$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
							$newtext = substr_replace($newtext, "\x01", $npos, 0);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
				if (isset($year)) {
					$queries = explode(" ", $year);
					$newtext = $text;
					$hasallhits = true;
					foreach($queries as $index=>$query1) {
						$query1=addcslashes($query1, '/');
						if (preg_match("/(".$query1.")/i", $text)) {
							$newtext = preg_replace("/(".$query1.")/i", "\x01$1\x02", $newtext);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
			}
			// All the "Highlight start" and "Highlight end" flags are set:
			//		Delay the final clean-up and insertion of proper <span> and </span>
			//		until parentheses, braces, and brackets have been processed
    }

	// Look for strings enclosed in parentheses, braces, or brackets.
	//
	// Parentheses, braces, and brackets have weak directionality and aren't handled properly
	// when they enclose text whose directionality differs from that of the page.
	//
	// To correct the problem, we need to enclose the parentheses, braces, or brackets with
	// zero-width characters (&lrm; or &rlm;) having a directionality that matches the
	// directionality of the text that is enclosed by the parentheses, etc.

	$charPos = 0;
	$lastChar = strlen($text);
	$newText = "";
	while (true) {
		if ($charPos > $lastChar) break;
		$thisChar = substr($text, $charPos, 1);
		$charPos ++;
		if ($thisChar=="(" || $thisChar=="{" || $thisChar=="[") {
			$tempText = "";
			while (true) {
				$tempChar = "";
				if ($charPos > $lastChar) break;
				$tempChar = substr($text, $charPos, 1);
				$charPos ++;
				if ($tempChar==")" || $tempChar=="}" || $tempChar=="]") break;
				$tempText .= $tempChar;
			}
			$thisLang = whatLanguage($tempText);
			if (!isset($TEXT_DIRECTION_array[$thisLang]) || $TEXT_DIRECTION_array[$thisLang]=="ltr") {
				$newText .= "&lrm;" . $thisChar . $tempText. $tempChar . "&lrm;";
			} else {
				$newText .= "&rlm;" . $thisChar . $tempText. $tempChar . "&rlm;";
 			   			}
		} else {
			$newText .= $thisChar;
			   			}
			   			     }

    // Parentheses, braces, and brackets have been processed:
    //		Finish processing of "Highlight Start and "Highlight end"
	$newText = str_replace(array("\x02\x01", "\x02 \x01", "\x01", "\x02"), array("", " ", "<span class=\"search_hit\">", "</span>"), $newText);
    return $newText;
}
/**
 * print ASSO RELA information
 *
 * Ex1:
 * <code>1 ASSO @I1@
 * 2 RELA Twin</code>
 *
 * Ex2:
 * <code>1 CHR
 * 2 ASSO @I1@
 * 3 RELA Godfather
 * 2 ASSO @I2@
 * 3 RELA Godmother</code>
 *
 * @param string $pid		person or family ID
 * @param string $factrec	the raw gedcom record to print
 * @param string $linebr 	optional linebreak
 */
function print_asso_rela_record($pid, $factrec, $linebr=false) {
	global $GEDCOM, $SHOW_ID_NUMBERS, $TEXT_DIRECTION, $pgv_lang, $factarray, $PGV_IMAGE_DIR, $PGV_IMAGES, $view;
	// get ASSOciate(s) ID(s)
	$ct = preg_match_all("/\d ASSO @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	for ($i=0; $i<$ct; $i++) {
		$level = substr($match[$i][0],0,1);
		$pid2 = $match[$i][1];
		// get RELAtionship field
		$assorec = get_sub_record($level, "$level ASSO ", $factrec, $i+1);
//		if (substr($_SERVER["SCRIPT_NAME"],1) == "pedigree.php") {
			$rct = preg_match("/\d RELA (.*)/", $assorec, $rmatch);
			if ($rct>0) {
				// RELAtionship name in user language
				$key = strtolower(trim($rmatch[1]));
	            $cr = preg_match_all("/sosa_(.*)/", $key, $relamatch, PREG_SET_ORDER);
                if ($cr > 0) {
                    $rela = get_sosa_name($relamatch[0][1]);
                }
                else
                {
				    if (isset($pgv_lang["$key"])) $rela = $pgv_lang[$key];
				    else $rela = $rmatch[1];
                }
				$p = strpos($rela, "(=");
				if ($p>0) $rela = trim(substr($rela, 0, $p));
				if ($pid2==$pid) print "<span class=\"details_label\">";
				print $rela.": ";
				if ($pid2==$pid) print "</span>";
			}
			else $rela = $factarray["RELA"]; // default
//		}

		// ASSOciate ID link
		$gedrec = find_gedcom_record($pid2);
		if (strstr($gedrec, "@ INDI")!==false
		or  strstr($gedrec, "@ SUBM")!==false) {
			// ID name
			if ((DisplayDetailsByID($pid2))||(showLivingNameByID($pid2))) {
				$name = get_person_name($pid2);
				$addname = get_add_person_name($pid2);
			}
			else {
				$name = $pgv_lang["private"];
				$addname = "";
			}
			print "<a href=\"individual.php?pid=$pid2&amp;ged=$GEDCOM\">" . PrintReady($name);
//			if (!empty($addname)) print "<br />" . PrintReady($addname);
			if (!empty($addname)) print " - " . PrintReady($addname);
			if ($SHOW_ID_NUMBERS) {
				print "&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$pid2.")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			}
			print "</a>";
			// ID age
			if (!strstr($factrec, "_BIRT_")) {
				$dct = preg_match("/2 DATE (.*)/", $factrec, $dmatch);
				if ($dct>0) print " <span class=\"age\">".get_age($gedrec, $dmatch[1])."</span>";
			}
			// RELAtionship calculation : for a family print relationship to both spouses
			if ($view!="preview") {
				$famrec = find_family_record($pid);
				if ($famrec) {
					$parents = find_parents_in_record($famrec);
					$pid1 = $parents["HUSB"];
					if ($pid1 and $pid1!=$pid2) print " - <a href=\"relationship.php?pid1=$pid1&amp;pid2=$pid2&amp;followspouse=1&amp;ged=$GEDCOM\">[" . $pgv_lang["relationship_chart"] . "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["husband"] . "\" alt=\"" . $pgv_lang["husband"] . "\" class=\"sex_image\" />]</a>";
					$pid1 = $parents["WIFE"];
					if ($pid1 and $pid1!=$pid2) print " - <a href=\"relationship.php?pid1=$pid1&amp;pid2=$pid2&amp;followspouse=1&amp;ged=$GEDCOM\">[" . $pgv_lang["relationship_chart"] . "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sexf"]["small"] . "\" title=\"" . $pgv_lang["wife"] . "\" alt=\"" . $pgv_lang["wife"] . "\" class=\"sex_image\" />]</a>";
				}
				else if ($pid!=$pid2) print " - <a href=\"relationship.php?pid1=$pid&amp;pid2=$pid2&amp;followspouse=1&amp;ged=$GEDCOM\">[" . $pgv_lang["relationship_chart"] . "]</a>";
			}
		}
		else if (strstr($gedrec, "@ FAM")!==false) {
			print "<a href=\"family.php?famid=$pid2\">";
			if ($TEXT_DIRECTION == "ltr") print " &lrm;"; else print " &rlm;";
			print "[".$pgv_lang["view_family"];
  			if ($SHOW_ID_NUMBERS) print " &lrm;($pid2)&lrm;";
  			if ($TEXT_DIRECTION == "ltr") print "&lrm;]</a>\n"; else print "&rlm;]</a>\n";
		}
		else {
			print $pgv_lang["unknown"];
			if ($SHOW_ID_NUMBERS) {
				print "&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$pid2.")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			}
		}
		if ($linebr) print "<br />\n";
		print_fact_notes($assorec, $level+1);
		if (substr($_SERVER["SCRIPT_NAME"],1) == "pedigree.php") {
			print "<br />";
			if (function_exists('print_fact_sources')) print_fact_sources($assorec, $level+1);
		}
	}
}
/**
 * Print age of parents
 *
 * @param string $pid	child ID
 * @param string $bdate	child birthdate
 */
function print_parents_age($pid, $bdate) {
	global $pgv_lang, $SHOW_PARENTS_AGE, $PGV_IMAGE_DIR, $PGV_IMAGES;
	if ($SHOW_PARENTS_AGE) {
		$famids = find_family_ids($pid);
		// dont show age of parents if more than one family (ADOPtion)
		if (count($famids)==1) {
			print " <span class=\"age\">";
			$parents = find_parents($famids[0]);
			// father
			$spouse = $parents["HUSB"];
			if ($spouse and showFact("BIRT", $spouse)) {
				$age = get_age(find_person_record($spouse), $bdate, false);
				if (10<$age and $age<80) print "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["father"] . "\" alt=\"" . $pgv_lang["father"] . "\" class=\"sex_image\" />$age";
			}
			// mother
			$spouse = $parents["WIFE"];
			if ($spouse and showFact("BIRT", $spouse)) {
				$age = get_age(find_person_record($spouse), $bdate, false);
				if (10<$age and $age<80) print "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sexf"]["small"] . "\" title=\"" . $pgv_lang["mother"] . "\" alt=\"" . $pgv_lang["mother"] . "\" class=\"sex_image\" />$age";
			}
			print "</span>";
		}
	}
}
/**
 * print fact DATE TIME
 *
 * @param string $factrec	gedcom fact record
 * @param boolean $anchor	option to print a link to calendar
 * @param boolean $time		option to print TIME value
 * @param string $fact		optional fact name (to print age)
 * @param string $pid		optional person ID (to print age)
 * @param string $indirec	optional individual record (to print age)
 */
function print_fact_date($factrec, $anchor=false, $time=false, $fact=false, $pid=false, $indirec=false) {
	global $factarray, $pgv_lang, $SEARCH_SPIDER;

	$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
	if ($ct>0) {
		print " ";
		// link to calendar
		if ($anchor && (empty($SEARCH_SPIDER))) print get_date_url($match[1]);
		// simple date
		else print get_changed_date(trim($match[1]));
		// time
		if ($time) {
			$timerec = get_sub_record(2, "2 TIME", $factrec);
			if (empty($timerec)) $timerec = get_sub_record(2, "2 DATE", $factrec);
			$tt = preg_match("/[2-3] TIME (.*)/", $timerec, $tmatch);
			if ($tt>0) print " - <span class=\"date\">".$tmatch[1]."</span>";
		}
		if ($fact and $pid) {
			// age of parents at child birth
			if ($fact=="BIRT") print_parents_age($pid, $match[1]);
			// age at event
			else if ($fact!="CHAN") {
				if (!$indirec) $indirec=find_person_record($pid);
				// do not print age after death
				$deatrec=get_sub_record(1, "1 DEAT", $indirec);
				if (empty($deatrec)||(compare_facts($factrec, $deatrec)!=1)||(strstr($factrec, "1 DEAT"))) {
					print get_age($indirec,$match[1]);
				}
			}
		}
		print " ";
	}
	else {
		// 1 DEAT Y with no DATE => print YES
		// 1 DEAT N is not allowed
		// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
		$factrec = str_replace("\r\nPGV_OLD\r\n", "", $factrec);
		$factrec = str_replace("\r\nPGV_NEW\r\n", "", $factrec);
		$factdetail = preg_split("/ /", trim($factrec));
		if (isset($factdetail)) if (count($factdetail) == 3) if (strtoupper($factdetail[2]) == "Y") print $pgv_lang["yes"];
	}
	// gedcom indi age
	$ages=array();
	$agerec = get_gedcom_value("AGE", 2, $factrec);
	$daterec = get_sub_record(2, "2 DATE", $factrec);
	if (empty($agerec)) $agerec = get_gedcom_value("AGE", 3, $daterec);
	$ages[0] = $agerec;
	// gedcom husband age
	$husbrec = get_sub_record(2, "2 HUSB", $factrec);
	if (!empty($husbrec)) $agerec = get_gedcom_value("AGE", 3, $husbrec);
	else $agerec = "";
	$ages[1] = $agerec;
	// gedcom wife age
	$wiferec = get_sub_record(2, "2 WIFE", $factrec);
	if (!empty($wiferec)) $agerec = get_gedcom_value("AGE", 3, $wiferec);
	else $agerec = "";
	$ages[2] = $agerec;
	// print gedcom ages
	foreach ($ages as $indexval=>$agerec) {
		if (!empty($agerec)) {
			print "<span class=\"label\">";
			if ($indexval==1) print $pgv_lang["husband"];
			else if ($indexval==2) print $pgv_lang["wife"];
			else print $factarray["AGE"];
			print "</span>: ";
			$age = get_age_at_event($agerec);
			print PrintReady($age);
			print " ";
		}
	}
}
/**
 * print fact PLACe TEMPle STATus
 *
 * @param string $factrec	gedcom fact record
 * @param boolean $anchor	option to print a link to placelist
 * @param boolean $sub		option to print place subrecords
 * @param boolean $lds		option to print LDS TEMPle and STATus
 */
function print_fact_place($factrec, $anchor=false, $sub=false, $lds=false) {
	global $SHOW_PEDIGREE_PLACES, $TEMPLE_CODES, $pgv_lang, $factarray, $SEARCH_SPIDER;
	$out = false;
	$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
	if ($ct>0) {
		print " ";
		$levels = preg_split("/,/", $match[1]);
		if ($anchor && (empty($SEARCH_SPIDER))) {
			$place = trim($match[1]);
			// reverse the array so that we get the top level first
			$levels = array_reverse($levels);
			print "<a href=\"placelist.php?action=show&amp;";
			foreach($levels as $pindex=>$ppart) {
				 // routine for replacing ampersands
				 $ppart = preg_replace("/amp\%3B/", "", trim($ppart));
				 print "parent[$pindex]=".PrintReady($ppart)."&amp;";
			}
			print "level=".count($levels);
			print "\"> ".PrintReady($place)."</a>";
		}
		else {
			if (empty($SEARCH_SPIDER)) print " -- ";
			for ($level=0; $level<$SHOW_PEDIGREE_PLACES; $level++) {
				if (!empty($levels[$level])) {
					if ($level>0) print ", ";
					print PrintReady($levels[$level]);
				}
			}
		}
	}
	$ctn=0;
	if ($sub) {
		$placerec = get_sub_record(2, "2 PLAC", $factrec);
		if (!empty($placerec)) {
			$cts = preg_match("/\d ROMN (.*)/", $placerec, $match);
			if ($cts>0) {
//				if ($ct>0) print "<br />\n";
				if ($ct>0) print " - ";
				print " ".PrintReady($match[1]);
			}
			$cts = preg_match("/\d _HEB (.*)/", $placerec, $match);
			if ($cts>0) {
//				if ($ct>0) print "<br />\n";
				if ($ct>0) print " - ";
				print " ".PrintReady($match[1]);
			}
			$map_lati="";
			$cts = preg_match("/\d LATI (.*)/", $placerec, $match);
			if ($cts>0) {
				$map_lati=$match[1];
				print "<br /><span class=\"label\">".$factarray["LATI"].": </span>".$map_lati;
			}
			$map_long="";
			$cts = preg_match("/\d LONG (.*)/", $placerec, $match);
			if ($cts>0) {
				$map_long=$match[1];
				print " <span class=\"label\">".$factarray["LONG"].": </span>".$map_long;
			}
			if ($map_lati and $map_long) {
				$map_lati=trim(strtr($map_lati,"NSEW,�"," - -. ")); // S5,6789 ==> -5.6789
				$map_long=trim(strtr($map_long,"NSEW,�"," - -. ")); // E3.456� ==> 3.456
				print " <a target=\"_BLANK\" href=\"http://www.mapquest.com/maps/map.adp?searchtype=address&formtype=latlong&latlongtype=decimal&latitude=".$map_lati."&longitude=".$map_long."\"><img src=\"images/mapq.gif\" border=\"0\" alt=\"Mapquest &copy;\" title=\"Mapquest &copy;\" /></a>";
				print " <a target=\"_BLANK\" href=\"http://maps.google.com/maps?q=".$map_lati.",".$map_long."\"><img src=\"images/bubble.gif\" border=\"0\" alt=\"Google Maps &copy;\" title=\"Google Maps &copy;\" /></a>";
				print " <a target=\"_BLANK\" href=\"http://www.multimap.com/map/browse.cgi?lat=".$map_lati."&lon=".$map_long."&scale=&icon=x\"><img src=\"images/multim.gif\" border=\"0\" alt=\"Multimap &copy;\" title=\"Multimap &copy;\" /></a>";
				print " <a target=\"_BLANK\" href=\"http://www.terraserver.com/imagery/image_gx.asp?cpx=".$map_long."&cpy=".$map_lati."&res=30&provider_id=340\"><img src=\"images/terrasrv.gif\" border=\"0\" alt=\"TerraServer &copy;\" title=\"TerraServer &copy;\" /></a>";
			}
			$ctn = preg_match("/\d NOTE (.*)/", $placerec, $match);
			if ($ctn>0) {
				print_fact_notes($placerec, 3);
				$out = true;
			}
		}
	}
	if ($lds) {
		$ct = preg_match("/2 TEMP (.*)/", $factrec, $match);
		if ($ct>0) {
			$tcode = trim($match[1]);
			if (array_key_exists($tcode, $TEMPLE_CODES)) {
				print $pgv_lang["temple"].": ".$TEMPLE_CODES[$tcode];
			}
			else {
				print $pgv_lang["temple_code"].$tcode;
			}
		}
		$ct = preg_match("/2 STAT (.*)/", $factrec, $match);
		if ($ct>0) {
			print "<br />".$pgv_lang["status"].": ";
			print trim($match[1]);
		}
	}
}
/**
 * print first major fact for an Individual
 *
 * @param string $key	indi pid
 */
function print_first_major_fact($key, $majorfacts = array("BIRT", "CHR", "BAPM", "DEAT", "BURI", "BAPL", "ADOP")) {
	global $pgv_lang, $factarray, $LANGUAGE, $TEXT_DIRECTION;

	$indirec = find_person_record($key);
	if (!$indirec) $indirec = find_family_record($key);
	foreach ($majorfacts as $indexval => $fact) {
		$factrec = get_sub_record(1, "1 $fact", $indirec);
		if (strlen($factrec)>7 and showFact("$fact", $key) and !FactViewRestricted($key, $factrec)) {
			print "<span dir=\"$TEXT_DIRECTION\">";
			echo "<br /><i>";
			//print " -- <i>";
			if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
			else if (isset($factarray[$fact])) print $factarray[$fact];
			else print $fact;
			print " ";
			print_fact_date($factrec);
			print_fact_place($factrec);
			print "</i>";
			print "</span>";
			break;
		}
	}
	return $fact;
}

/**
 * Check for facts that may exist only once for a certain record type.
 * If the fact already exists in the second array, delete it from the first one.
 */
function CheckFactUnique($uniquefacts, $recfacts, $type) {

	 foreach($recfacts as $indexval => $fact) {
		if (($type == "SOUR") || ($type == "REPO")) $factrec = $fact[0];
		if (($type == "FAM") || ($type == "INDI")) $factrec = $fact[1];
		$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			$key = array_search($fact, $uniquefacts);
			if ($key !== false) unset($uniquefacts[$key]);
		}
	 }
	 return $uniquefacts;
}

/**
 * Print a new fact box on details pages
 * @param string $id	the id of the person,family,source etc the fact will be added to
 * @param array $usedfacts	an array of facts already used in this record
 * @param string $type	the type of record INDI, FAM, SOUR etc
 */
function print_add_new_fact($id, $usedfacts, $type) {
	global $factarray, $pgv_lang;
	global $INDI_FACTS_ADD,    $FAM_FACTS_ADD,    $SOUR_FACTS_ADD,    $REPO_FACTS_ADD;
	global $INDI_FACTS_UNIQUE, $FAM_FACTS_UNIQUE, $SOUR_FACTS_UNIQUE, $REPO_FACTS_UNIQUE;
	global $INDI_FACTS_QUICK,  $FAM_FACTS_QUICK,  $SOUR_FACTS_QUICK,  $REPO_FACTS_QUICK;

	switch ($type) {
	case "INDI":
		$addfacts   =preg_split("/[, ;:]+/", $INDI_FACTS_ADD,    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $INDI_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $INDI_FACTS_QUICK,  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "FAM":
		$addfacts   =preg_split("/[, ;:]+/", $FAM_FACTS_ADD,     -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $FAM_FACTS_UNIQUE,  -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $FAM_FACTS_QUICK,   -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "SOUR":
		$addfacts   =preg_split("/[, ;:]+/", $SOUR_FACTS_ADD,    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $SOUR_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $SOUR_FACTS_QUICK,  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "REPO":
		$addfacts   =preg_split("/[, ;:]+/", $REPO_FACTS_ADD,    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", $REPO_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", $REPO_FACTS_QUICK,  -1, PREG_SPLIT_NO_EMPTY);
		break;
	default:
		return;
	}
	$addfacts=array_merge(CheckFactUnique($uniquefacts, $usedfacts, $type), $addfacts);
	$quickfacts=array_intersect($quickfacts, $addfacts);

	usort($addfacts, "factsort");
	print "<tr><td class=\"descriptionbox\">";
	print_help_link("add_new_facts_help", "qm");
	print $pgv_lang["add_fact"]."</td>";
	print "<td class=\"optionbox\">";
	print "<form method=\"get\" name=\"newfactform\" action=\"\" onsubmit=\"return false;\">\n";
	print "<select id=\"newfact\" name=\"newfact\">\n";
	foreach($addfacts as $indexval => $fact) {
  		print PrintReady("<option value=\"$fact\">".$factarray[$fact]. " [".$fact."]</option>\n");
	}
	if (($type == "INDI") || ($type == "FAM")) print "<option value=\"EVEN\">".$pgv_lang["custom_event"]." [EVEN]</option>\n";
	if (!empty($_SESSION["clipboard"])) {
		foreach($_SESSION["clipboard"] as $key=>$fact) {
			if ($fact["type"]==$type || $fact["type"]=='all') {
				print "<option value=\"clipboard_$key\">".$pgv_lang["add_from_clipboard"]." ".$factarray[$fact["fact"]]."</option>\n";
			}
		}
	}
	print "</select>";
	print "<input type=\"button\" value=\"".$pgv_lang["add"]."\" onclick=\"add_record('$id', 'newfact');\" />\n";
	foreach($quickfacts as $k=>$v) echo "&nbsp;<small><a href='javascript://$v' onclick=\"add_new_record('$id', '$v');return false;\">".$factarray["$v"]."</a></small>&nbsp;";
	print "</form>\n";
	print "</td></tr>\n";
}

/**
 * javascript declaration for calendar popup
 *
 * @param none
 */
function init_calendar_popup() {
	global $monthtonum, $pgv_lang, $WEEK_START;

	print "<script language=\"JavaScript\" type='text/javascript'>\n<!--\n";
	// month names
	print "cal_setMonthNames(";
	foreach($monthtonum as $mon=>$num) {
		if (isset($pgv_lang[$mon])) {
			if ($num>1) print ",";
			print "\"".$pgv_lang[$mon]."\"";
		}
	}
	print ");\n";
	// day headers
	print "cal_setDayHeaders(";
	foreach(array('sunday_1st','monday_1st','tuesday_1st','wednesday_1st','thursday_1st','friday_1st','saturday_1st') as $indexval => $day) {
		if (isset($pgv_lang[$day])) {
			if ($day!=="sunday_1st") print ",";
			print "\"".$pgv_lang[$day]."\"";
		}
	}
	print ");\n";
	// week start day
	print "cal_setWeekStart(".$WEEK_START.");\n";
	print "//-->\n</script>\n";
}

/**
 * prints a link to open the Find Special Character window
 * @param string $element_id	the ID of the element the value will be pasted to
 * @param string $indiname		the id of the element the name should be pasted to
 * @param boolean $asString		Whether or not the HTML should be returned as a string or printed
 * @param boolean $multiple		Whether or not the user will be selecting multiple people
 * @param string $ged			The GEDCOM to search in
 */
function print_findindi_link($element_id, $indiname, $asString=false, $multiple=false, $ged='', $filter='') {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;

	$text = $pgv_lang["find_id"];
	if (empty($ged)) $ged=$GEDCOM;
	if (isset($PGV_IMAGES["indi"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indi"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findIndi(document.getElementById('".$element_id."'), document.getElementById('".$indiname."'), '".$multiple."', '".$ged."', '".$filter."'); findtype='individual'; return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	print $out;
}

function print_findplace_link($element_id, $ged='') {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = $pgv_lang["find_place"];
	if (isset($PGV_IMAGES["place"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findPlace(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
	print $Link;
	print "</a>";
}

function print_findfamily_link($element_id, $ged='') {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = $pgv_lang["find_family"];
	if (isset($PGV_IMAGES["family"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["family"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findFamily(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
	print $Link;
	print "</a>";
}

function print_specialchar_link($element_id,$vert) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_specialchar"];
	if (isset($PGV_IMAGES["keyboard"]["button"])) $Link = "<img id=\"".$element_id."_spec\" name=\"".$element_id."_spec\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["keyboard"]["button"]."\"  alt=\"".$text."\"  title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findSpecialChar(document.getElementById('".$element_id."')); updatewholename(); return false;\">";
	print $Link;
	print "</a>";
}

function print_autopaste_link($element_id, $choices, $concat=1, $name=1, $submit=0) {
	global $pgv_lang;

	print "<small>";
	foreach ($choices as $indexval => $choice) {
		print " &nbsp;<a href=\"javascript:;\" onclick=\"document.getElementById('".$element_id."').value ";
		if ($concat) print "+=' "; else print "='";
		print $choice."'; ";
		if ($name) print " updatewholename();";
		if ($submit) print " document.forms[0].submit();";
		print " return false;\">".$choice."</a>";
	}
	print "</small>";
}

function print_findsource_link($element_id, $sourcename="", $asString=false, $ged='') {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = $pgv_lang["find_sourceid"];
	if (isset($PGV_IMAGES["source"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findSource(document.getElementById('".$element_id."'), document.getElementById('".$sourcename."'), '".$ged."'); findtype='source'; return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if($asString) return $out;
	print $out;
}

function print_findrepository_link($element_id, $ged='') {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = $pgv_lang["find_repository"];
	if (isset($PGV_IMAGES["repository"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["repository"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findRepository(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
	print $Link;
	print "</a>";
}

function print_findmedia_link($element_id, $choose="", $ged='', $asString=false) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;

	$out = '';
	$text = $pgv_lang["find_media"];
	if (empty($ged)) $ged=$GEDCOM;
	if (isset($PGV_IMAGES["media"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out .= " <a href=\"javascript:;\" onclick=\"findMedia(document.getElementById('".$element_id."'), '".$choose."', '".$ged."'); return false;\">";
	$out .=  $Link;
	$out .= "</a>";
	if ($asString) return $out;
	print $out;
}

/**
 * get a quick-glance view of current LDS ordinances
 * @param string $indirec
 * @return string
 */
function get_lds_glance($indirec) {
	$text = "";

	$ord = get_sub_record(1, "1 BAPL", $indirec);
	if ($ord) $text .= "B";
	else $text .= "_";
	$ord = get_sub_record(1, "1 ENDL", $indirec);
	if ($ord) $text .= "E";
	else $text .= "_";
	$found = false;
	$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$famrec = find_family_record($match[$i][1]);
		if ($famrec) {
			$ord = get_sub_record(1, "1 SLGS", $famrec);
			if ($ord) {
				$found = true;
				break;
			}
		}
	}
	if ($found) $text .= "S";
	else $text .= "_";
	$ord = get_sub_record(1, "1 SLGC", $indirec);
	if ($ord) $text .= "P";
	else $text .= "_";
	return $text;
}

/**
 *		This function produces a hexadecimal dump of the input string
 *		for debugging purposes
 */

function DumpString($input) {
	if (empty($input)) return false;

	$UTF8 = array();
	$hex1L = "";
	$hex1R = "";
	$hex2L = "";
	$hex2R = "";
	$hex3L = "";
	$hex3R = "";
	$hex4L = "";
	$hex4R = "";
	$hexLetters = "0123456789ABCDEF";

	$pos = 0;
	while (true) {
		// Separate the input string into UTF8 characters
		$byte0 = ord(substr($input, $pos, 1));
		$charLen = 1;
		if (($byte0 & 0xE0) == 0xC0) $charLen = 2;		// 2-byte sequence
		if (($byte0 & 0xF0) == 0xE0) $charLen = 3;		// 3-byte sequence
		if (($byte0 & 0xF8) == 0xF0) $charLen = 4;		// 4-byte sequence
		$thisChar = substr($input, $pos, $charLen);
		$UTF8[] = $thisChar;

		// Separate the current UTF8 character into hexadecimal digits
		$byte = ord(substr($thisChar, 0, 1));
		$nibbleL = $byte >> 4;
		$hex1L .= substr($hexLetters, $nibbleL, 1);
		$nibbleR = $byte & 0x0F;
		$hex1R .= substr($hexLetters, $nibbleR, 1);

		if ($charLen > 1) {
			$byte = ord(substr($thisChar, 1, 1));
			$nibbleL = $byte >> 4;
			$hex2L .= substr($hexLetters, $nibbleL, 1);
			$nibbleR = $byte & 0x0F;
			$hex2R .= substr($hexLetters, $nibbleR, 1);
		} else {
			$hex2L .= " ";
			$hex2R .= " ";
		}

		if ($charLen > 2) {
			$byte = ord(substr($thisChar, 2, 1));
			$nibbleL = $byte >> 4;
			$hex3L .= substr($hexLetters, $nibbleL, 1);
			$nibbleR = $byte & 0x0F;
			$hex3R .= substr($hexLetters, $nibbleR, 1);
		} else {
			$hex3L .= " ";
			$hex3R .= " ";
		}

		if ($charLen > 3) {
			$byte = ord(substr($thisChar, 3, 1));
			$nibbleL = $byte >> 4;
			$hex4L .= substr($hexLetters, $nibbleL, 1);
			$nibbleR = $byte & 0x0F;
			$hex4R .= substr($hexLetters, $nibbleR, 1);
		} else {
			$hex4L .= " ";
			$hex4R .= " ";
		}

		$pos += $charLen;
		if ($pos>=strlen($input)) break;
	}

	$pos = 0;
	$lastPos = count($UTF8);
	$haveByte4 = (trim($hex4L)!="");
	$haveByte3 = (trim($hex3L)!="");
	$haveByte2 = (trim($hex2L)!="");

	// We're ready: now output everything
	$lrm = chr(0xE2).chr(0x80).chr(0x8E);
	$rlm = chr(0xE2).chr(0x80).chr(0x8F);
	print "<br /><code><span dir=\"ltr\">";
	while (true) {
		$lineLength = $lastPos - $pos;
		if ($lineLength>100) $lineLength = 100;

		// Line 1: ruler
		$thisLine = substr("      ".$pos, -6)." ";
		$thisLine .= substr("........10........20........30........40........50........60........70........80........90.......100", 0, $lineLength);
		print str_replace(" ", "&nbsp;", $thisLine)."<br />";

		// Line 2: UTF8 character string
		$thisLine = "  UTF8 ";
		for ($i=$pos; $i<($pos+$lineLength); $i++) {
			if (ord(substr($UTF8[$i], 0, 1)) < 0x20) $thisLine .= "&lrm;"." ";
			else $thisLine .= "&lrm;".$UTF8[$i];
		}
		print str_replace(array(" ", $lrm, $rlm), array("&nbsp;", "&nbsp;", "&nbsp;"), $thisLine)."<br />";

		// Line 3:  First hexadecimal byte
		$thisLine = "Byte 1 ";
		$thisLine .= substr($hex1L, $pos, $lineLength);
		$thisLine .= "<br />";
		$thisLine .= "       ";
		$thisLine .= substr($hex1R, $pos, $lineLength);
		$thisLine .= "<br />";
		print str_replace(array(" ", "<br&nbsp;/>"), array("&nbsp;", "<br />"), $thisLine);

		// Line 4:  Second hexadecimal byte
		if ($haveByte2) {
			$thisLine = "Byte 2 ";
			$thisLine .= substr($hex2L, $pos, $lineLength);
			$thisLine .= "<br />";
			$thisLine .= "       ";
			$thisLine .= substr($hex2R, $pos, $lineLength);
			$thisLine .= "<br />";
			print str_replace(array(" ", "<br&nbsp;/>"), array("&nbsp;", "<br />"), $thisLine);
		}

		// Line 5:  Third hexadecimal byte
		if ($haveByte3) {
			$thisLine = "Byte 3 ";
			$thisLine .= substr($hex3L, $pos, $lineLength);
			$thisLine .= "<br />";
			$thisLine .= "       ";
			$thisLine .= substr($hex3R, $pos, $lineLength);
			$thisLine .= "<br />";
			print str_replace(array(" ", "<br&nbsp;/>"), array("&nbsp;", "<br />"), $thisLine);
		}

		// Line 6:  Fourth hexadecimal byte
		if ($haveByte4) {
			$thisLine = "Byte 4 ";
			$thisLine .= substr($hex4L, $pos, $lineLength);
			$thisLine .= "<br />";
			$thisLine .= "       ";
			$thisLine .= substr($hex4R, $pos, $lineLength);
			$thisLine .= "<br />";
			print str_replace(array(" ", "<br&nbsp;/>"), array("&nbsp;", "<br />"), $thisLine);
		}
		print "<br>";
		$pos += $lineLength;
		if ($pos >= $lastPos) break;
	}

	print "</span></code>";
	return true;
}
?>

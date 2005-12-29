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
 * @version $Id: functions_print.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */
if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	 print "Now, why would you want to do that. You're not hacking are you?";
	 exit;
}
/**
 * print a submitter record
 *
 * find and print submitter information
 * @param string $sid  the Gedcom Xref ID of the submitter to print
 */
function print_submitter_info($sid) {
	 $srec = find_gedcom_record($sid);
	 preg_match("/1 NAME (.*)/", $srec, $match);
	 // PAF creates REPO record without a name
	 // Check here if REPO NAME exists or not
	 if (isset($match[1])) print "$match[1]<br />";
	 print_address_structure($srec, 1);
	 print_media_links($srec, 1);
}
/**
 * print a repository record
 *
 * find and print repository information attached to a source
 * @param string $sid  the Gedcom Xref ID of the repository to print
 */
function print_repository_record($sid) {
	 global $pgv_lang;
	 $source = find_gedcom_record($sid);
	 $ct = preg_match("/1 NAME (.*)/", $source, $match);
	 if ($ct > 0) {
		 $ct2 = preg_match("/0 @(.*)@/", $source, $rmatch);
		 if ($ct2>0) $rid = trim($rmatch[1]);
		 print "<span class=\"label\">".$pgv_lang["repo_name"]."</span> <span class=\"field\"><a href=\"repo.php?rid=$rid\">".PrintReady($match[1])."</a></span><br />";
	 }
	 print_address_structure($source, 1);
	 print_fact_notes($source, 1);
}
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
	$GEDCOM = $value[1];
	if (!isset($indi_private)) $indi_private=array();
	if (!isset($indi_hide)) $indi_hide=array();
	if (!isset($indi_total)) $indi_total=array();
	$indi_total[$key."[".$GEDCOM."]"] = 1;

	$disp = displayDetailsByID($key);
	if (showLivingNameByID($key)||$disp) {
		if ($useli) {
			if (begRTLText($value[0]))                            //-- For future use
				 print "<li class=\"rtl\" dir=\"rtl\">";
			else print "<li class=\"ltr\" dir=\"ltr\">";
		}
		if ($findid == true) print "<a href=\"javascript:;\" onclick=\"pasteid('".$key."', '".preg_replace("/'/", "\\'", PrintReady($value[0]))."'); return false;\" class=\"list_item\"><b>".$value[0]."</b>";
		else print "<a href=\"individual.php?pid=$key&amp;ged=$value[1]\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		if ($SHOW_ID_NUMBERS){
		   if ($TEXT_DIRECTION=="ltr") print " <span dir=\"ltr\">($key)</span>";
  		   else print " <span dir=\"rtl\">($key)</span>";
		}

		if (!$disp) {
			print " -- <i>".$pgv_lang["private"]."</i>";
			$indi_private[$key."[".$GEDCOM."]"] = 1;
		}
		else {
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
		}
		print "</a>";
		if (($asso != "") && ($disp)) {
			$p1 = strpos($asso,"[");
			$p2 = strpos($asso,"]");
			$ged = substr($asso,$p1+1,$p2-$p1-1);
			$key = substr($asso,0,$p1);
			$oldged = $GEDCOM;
			$GEDCOM = $ged;
			$name = get_person_name($key);
			$GEDCOM = $oldged;
			print " <a href=\"individual.php?pid=$key&amp;ged=$ged\" title=\"$name\" class=\"list_item\">";
			if ($TEXT_DIRECTION=="ltr") print " <span dir=\"ltr\">(".$pgv_lang["associate"]." ".$key.")</span>";
  			else print " <span dir=\"rtl\">(".$pgv_lang["associate"]." ".$key.")</span></a>";
		}

		if ($useli) print "</li>";
	}
	else {
		$pass = TRUE;
		$indi_hide[$key."[".$GEDCOM."]"] = 1;
	}
}
//-- print information about a family for a list view
function print_list_family($key, $value, $findid=false, $asso="", $useli=true) {
	global $pgv_lang, $pass, $fam_private, $fam_hide, $fam_total, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS;
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
		if ($useli) {
			if (begRTLText($value[0]))                            //-- For future use
				 print "<li class=\"rtl\" dir=\"rtl\">";
			else print "<li class=\"ltr\" dir=\"ltr\">";
		}
		if ($findid == true) print "<a href=\"javascript:;\" onclick=\"pasteid('".$key."'); return false;\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		else print "<a href=\"family.php?famid=$key&amp;ged=$value[1]\" class=\"list_item\"><b>".PrintReady($value[0])."</b>";
		if ($SHOW_FAM_ID_NUMBERS)
			if ($TEXT_DIRECTION=="ltr")	print " <span dir=\"ltr\">($key)</span>";
  			else print " <span dir=\"rtl\">($key)</span>";
		if (!$display) {
			print " -- <i>".$pgv_lang["private"]."</i>";
			$fam_private[$key."[".$GEDCOM."]"] = 1;
		}
		else {
			$bpos1 = strpos($famrec, "1 MARR");
			if ($bpos1) {
				$birthrec = get_sub_record(1, "1 MARR", $famrec);
				if (!FactViewRestricted($key, $birthrec)) {
					print " -- <i>".$pgv_lang["marriage"]." ";
					$bt = preg_match("/1 \w+/", $birthrec, $match);
					if ($bt>0) {
						 $bpos2 = strpos($birthrec, $match[0]);
						 if ($bpos2) $birthrec = substr($birthrec, 0, $bpos2);
					}
					print_fact_date($birthrec);
					print_fact_place($birthrec);
				}
				print "</i>";
			}
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
			if ($TEXT_DIRECTION=="ltr") print " <span dir=\"ltr\">(".$pgv_lang["associate"]." ".$indikey.")</span></a>";
  			else print " <span dir=\"rtl\">(".$pgv_lang["associate"]." ".$indikey.")</span></a>";
		}
		if ($useli) print "</li>\n";
	}															//begin re-added by pluntke
	if (!$showLivingWife || !$showLivingHusb) {				   	//fixed THIS line (changed && to ||)
		$pass = TRUE;
		$fam_hide[$key."[".$GEDCOM."]"] = 1;
	}															//end re-added by pluntke
}

// Prints the information for a source in a list view
function print_list_source($key, $value) {
	global $source_total, $source_hide, $SHOW_SOURCES, $SHOW_ID_NUMBERS, $GEDCOM, $TEXT_DIRECTION;

	$GEDCOM = get_gedcom_from_id($value["gedfile"]);
	if (!isset($source_total)) $source_total=array();
	$source_total[$key."[".$GEDCOM."]"] = 1;
	if (displayDetailsByID($key, "SOUR")) {
		if (begRTLText($value["name"]))
		     print "\n\t\t\t<li class=\"rtl\" dir=\"rtl\">";
		else print "\n\t\t\t<li class=\"ltr\" dir=\"ltr\">";
		print "\n\t\t\t<a href=\"source.php?sid=$key&amp;ged=".get_gedcom_from_id($value["gedfile"])."\"><class=\"list_item\">".PrintReady($value["name"]);
		if ($SHOW_ID_NUMBERS) {
			if ($TEXT_DIRECTION=="ltr") print " &lrm;($key)&lrm;";
			else print " &rlm;($key)&rlm;";
		}
		print "</a>\n";
		print "</li>\n";
	}
	else $source_hide[$key."[".$GEDCOM."]"] = 1;
}

// Prints the information for a repository in a list view
function print_list_repository($key, $value) {
	global $repo_total, $repo_hide, $SHOW_ID_NUMBERS, $GEDCOM, $TEXT_DIRECTION;

	$GEDCOM = get_gedcom_from_id($value["gedfile"]);
	if (!isset($repo_total)) $repo_total=array();
	$repo_total[$key."[".$GEDCOM."]"] = 1;
	if (displayDetailsByID($key, "REPO")) {
		if (begRTLText($key))
		     print "\n\t\t\t<li class=\"rtl\" dir=\"rtl\">";
		else print "\n\t\t\t<li class=\"ltr\" dir=\"ltr\">";

		$id = substr($value["id"], 1, -1);
		print "<a href=\"repo.php?rid=$id\" class=\"list_item\">";
		print PrintReady($key);
		if ($SHOW_ID_NUMBERS) {
			if ($TEXT_DIRECTION=="ltr") print " &lrm;($id)&lrm;";
			else print " &rlm;($id)&rlm;";
		}
		print "</a></li>\n";
	}
	else $repo_hide[$key."[".$GEDCOM."]"] = 1;
}

// Initializes counters for lists
function init_list_counters($action = "reset") {
	global $indi_total, $indi_hide, $indi_private;
	global $fam_total, $fam_hide, $fam_private;
	global $repo_total, $repo_hide;
	global $source_total, $source_hide;

	if ($action != "reset") {
		if (!isset($indi_total)) $indi_total = array();
		if (!isset($indi_private)) $indi_private = array();
		if (!isset($indi_hide)) $indi_hide = array();
		if (!isset($fam_total)) $fam_total = array();
		if (!isset($fam_private)) $fam_private = array();
		if (!isset($fam_hide)) $fam_hide = array();
		if (!isset($source_total)) $source_total = array();
		if (!isset($source_hide)) $source_hide = array();
		if (!isset($repo_total)) $repo_total = array();
		if (!isset($repo_hide)) $repo_hide = array();
	}
	else {
		$indi_total = array();
		$indi_private = array();
		$indi_hide = array();
		$fam_total = array();
		$fam_private = array();
		$fam_hide = array();
		$source_total = array();
		$source_hide = array();
		$repo_total = array();
		$repo_hide = array();
	}
}

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
	 global $PGV_IMAGE_DIR, $PGV_IMAGES, $ABBREVIATE_CHART_LABELS;
	 global $chart_style, $box_width, $generations;
	 global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE;
	 flush();
	 if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	 if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;
	 if (!isset($show_full)) $show_full=$PEDIGREE_FULL_DETAILS;
	 // NOTE: Start div out-rand()
	 if ($pid==false) {
		  print "\n\t\t\t<div id=\"out-".rand()."\" class=\"person_boxNN\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\">";
		  print "<br />";
		  print "\n\t\t\t</div>";
		  return false;
	 }
	 $lbwidth = $bwidth*.75;
	 if ($lbwidth < 150) $lbwidth = 150;
	 $indirec=find_person_record($pid);
	 if (!$indirec) $indirec = find_record_in_file($pid);
	 $isF = "NN";
	 if (preg_match("/1 SEX F/", $indirec)>0) $isF="F";
	 else if (preg_match("/1 SEX M/", $indirec)>0) $isF="";
	 $disp = displayDetailsByID($pid, "INDI");
	 if ($disp || showLivingNameByID($pid)) {
		  if ($show_famlink) {
			   if ($LINK_ICONS!="disabled") {
					//-- draw a box for the family popup
					// NOTE: Start div I.$pid.$personcount.$count.links
					print "\n\t\t<div id=\"I".$pid.".".$personcount.".".$count."links\" style=\"position:absolute; ";
					print "left: 0px; top:0px; width: ".($lbwidth)."px; visibility:hidden; z-index:'100';\">";
					print "\n\t\t\t<table class=\"person_box$isF\"><tr><td class=\"details1\">";
					// NOTE: Zoom
					print "<a href=\"pedigree.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;talloffset=$talloffset&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$personcount.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$personcount.".".$count."');\"><b>".$pgv_lang["index_header"]."</b></a>\n";
					print "<br /><a href=\"descendancy.php?pid=$pid&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$personcount.".".$count."');\"><b>".$pgv_lang["descend_chart"]."</b></a><br />\n";
					$username = getUserName();
					if (!empty($username)) {
						 $tuser=getUser($username);
						 if (!empty($tuser["gedcomid"][$GEDCOM])) {
							  print "<a href=\"relationship.php?pid1=".$tuser["gedcomid"][$GEDCOM]."&amp;pid2=".$pid."&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["relationship_to_me"]."</b></a><br />\n";
						 }
					}
					// NOTE: Zoom
					if (file_exists("ancestry.php")) print "<a href=\"ancestry.php?rootid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$personcount.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$personcount.".".$count."');\"><b>".$pgv_lang["ancestry_chart"]."</b></a><br />\n";
					if (file_exists("fanchart.php") and defined("IMG_ARC_PIE") and function_exists("imagettftext"))  print "<a href=\"fanchart.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$personcount.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$personcount.".".$count."');\"><b>".$pgv_lang["fan_chart"]."</b></a><br />\n";
					if (file_exists("hourglass.php")) print "<a href=\"hourglass.php?pid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$personcount.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$personcount.".".$count."');\"><b>".$pgv_lang["hourglass_chart"]."</b></a><br />\n";
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
								   print "<a href=\"family.php?famid=$famid&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\"><b>".$pgv_lang["fam_spouse"]."</b></a><br /> \n";
								if (!empty($spouse)) {
									print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid."');\">";
 									   if (($SHOW_LIVING_NAMES>=$PRIV_PUBLIC) || (displayDetailsByID($spouse))||(showLivingNameByID($spouse))) print PrintReady(get_person_name($spouse));
									   else print $pgv_lang["private"];
									   print "</a><br />\n";
								}
							  }
							  for($j=0; $j<$num; $j++) {
								   $cpid = $smatch[$j][1];
								   print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"individual.php?pid=$cpid&amp;ged=$GEDCOM\" onmouseover=\"clear_family_box_timeout('".$pid.".".$count."');\" onmouseout=\"family_box_timeout('".$pid.".".$count."');\">";
 								   if (($SHOW_LIVING_NAMES>=$PRIV_PUBLIC) || (displayDetailsByID($cpid))||(showLivingNameByID($cpid))) print PrintReady(get_person_name($cpid));
								   else print $pgv_lang["private"];
								   print "<br /></a>";
							  }
						 }
					}
					print "</td></tr></table>\n\t\t</div>";
			   }
			   // NOTE: Start div out-$pid.$personcount.$count
			   print "\n\t\t\t<div id=\"out-$pid.$personcount.$count\"";
			   if ($style==1) print " class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden; z-index:'-1';\"";
			   else print " style=\"padding: 2px;\"";
			   // NOTE: Zoom
			   if (($ZOOM_BOXES!="disabled")&&(!$show_full)) {
					if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"expandbox('".$pid.".".$personcount.".".$count."', $style); return false;\" onmouseout=\"restorebox('".$pid.".".$personcount.".".$count."', $style); return false;\"";
					if ($ZOOM_BOXES=="mousedown") print " onmousedown=\"expandbox('".$pid.".".$personcount.".".$count."', $style);\" onmouseup=\"restorebox('".$pid.".".$personcount.".".$count."', $style);\"";
					if (($ZOOM_BOXES=="click")&&($view!="preview")) print " onclick=\"expandbox('".$pid.".".$personcount.".".$count."', $style);\"";
			   }
			   print "><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
			   //-- links and zoom icons
			   // NOTE: Start div icons-$personcount.$pid.$count
			   if ($TEXT_DIRECTION == "rtl") {
					print "<div id=\"icons-$pid.$personcount.$count\" style=\"float:left; width: 25px; height: 50px;";
			   }
			   else {
					print "<div id=\"icons-$pid.$personcount.$count\" style=\"float:right; width: 25px; height: 50px;";
			   }
			   if ($show_full) print " display: block;";
			   else print " display: none;";
			   print "\">";
			   // NOTE: Zoom
			   if (($ZOOM_BOXES!="disabled")&&($show_full)) {
					print "<a href=\"javascript:;\"";
					if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"expandbox('".$pid.".".$personcount.".".$count."', $style);\" onmouseout=\"restorebox('".$pid.".".$personcount.".".$count."', $style);\" onclick=\"return false;\"";
					if ($ZOOM_BOXES=="mousedown") print " onmousedown=\"expandbox('".$pid.".".$personcount.".".$count."', $style);\" onmouseup=\"restorebox('".$pid.".".$personcount.".".$count."', $style);\" onclick=\"return false;\"";
					if ($ZOOM_BOXES=="click") print " onclick=\"expandbox('".$pid.".".$personcount.".".$count."', $style); return false;\"";
					print "><img id=\"iconz-$pid.$personcount.$count\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["zoomin"]["other"]."\" width=\"25\" height=\"25\" border=\"0\" alt=\"".$pgv_lang["zoom_box"]."\" title=\"".$pgv_lang["zoom_box"]."\" /></a>";
			   }
			   if ($LINK_ICONS!="disabled") {
					$click_link="#";
					if (preg_match("/pedigree.php/", $SCRIPT_NAME)>0) $click_link="pedigree.php?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;talloffset=$talloffset&amp;ged=$GEDCOM";
					if (preg_match("/hourglass.php/", $SCRIPT_NAME)>0) $click_link="hourglass.php?pid=$pid&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM";
					if (preg_match("/ancestry.php/", $SCRIPT_NAME)>0) $click_link="ancestry.php?rootid=$pid&amp;chart_style=$chart_style&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;box_width=$box_width&amp;ged=$GEDCOM";
					if (preg_match("/descendancy.php/", $SCRIPT_NAME)>0) $click_link="descendancy.php?pid=$pid&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width&amp;ged=$GEDCOM";
					if ((preg_match("/family.php/", $SCRIPT_NAME)>0)&&!empty($famid)) $click_link="family.php?famid=$famid&amp;ged=$GEDCOM";
					if (preg_match("/individual.php/", $SCRIPT_NAME)>0) $click_link="individual.php?pid=$pid&amp;ged=$GEDCOM";
					print "<a href=\"javascript:;\" ";
					// NOTE: Zoom
					if ($LINK_ICONS=="mouseover") print "onmouseover=\"show_family_box('".$pid.".".$personcount.".".$count."', '";
					if ($LINK_ICONS=="click") print "onclick=\"toggle_family_box('".$pid.".".$personcount.".".$count."', '";
					if ($style==1) print "box$pid";
					else print "relatives";
					print "');";
					print " return false;\" ";
					// NOTE: Zoom
					print "onmouseout=\"family_box_timeout('".$pid.".".$personcount.".".$count."');";
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
					print "\n\t\t\t<div id=\"out-$pid.$personcount.$count\" class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"";
			   }
			   else {
					print "\n\t\t\t<div id=\"out-$pid.$personcount.$count\" class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"";
			   }
			   // NOTE: Zoom
			   if ($ZOOM_BOXES!="disabled") {
					if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"expandbox('".$pid.".".$personcount.".".$count."', $style); return false;\" onmouseout=\"restorebox('".$pid.".".$personcount.".".$count."', $style); return false;\"";
					if ($ZOOM_BOXES=="mousedown") print " onmousedown=\"expandbox('".$pid.".".$personcount.".".$count."', $style);\" onmouseup=\"restorebox('".$pid.".".$personcount.".".$count."', $style);\"";
					if (($ZOOM_BOXES=="click")&&($view!="preview")) print " onclick=\"expandbox('".$pid.".".$personcount.".".$count."', $style);\"";
			   }
			   print "><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
		  }
	 }
	 // NOTE: Start div out-$personcount.$pid.$count
	 else {
		  if ($style==1) print "\n\t\t\t<div id=\"out-$pid.$personcount.$count\" class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
		  else print "\n\t\t\t<div id=\"out-$pid.$personcount.$count\" class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td valign=\"top\">";
	 }
	 //-- find the name
	 $name = get_person_name($pid);
	 if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES && showFact("OBJE", $pid)) {
		  $object = find_highlighted_object($pid, $indirec);
		  if (!empty($object["thumb"])) {
			   $size = @getimagesize($object["thumb"]);
			   $class = "pedigree_image_portrait";
			   if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
			   if($TEXT_DIRECTION == "rtl") $class .= "_rtl";
			   // NOTE: IMG ID
			   print "<img id=\"box-$pid.$personcount.$count-thumb\" src=\"".$object["thumb"]."\" vspace=\"0\" hspace=\"0\" class=\"$class\" alt =\"\" title=\"\" ";
			   if (!$show_full) print " style=\"display: none;\"";
			   print " />\n";
		  }
	 }
	 //-- find additional name
	 $addname = get_add_person_name($pid);
	 //-- check if the persion is visible
	 if (!$disp) {
		  if (showLivingName($indirec)) {
			   // NOTE: Start span namedef-$personcount.$pid.$count
			   print "<a href=\"individual.php?pid=$pid&amp;ged=$GEDCOM\"><span id=\"namedef-$pid.$personcount.$count\" ";
			   if (hasRTLText($name) && $style=="1")
					print "class=\"name2\">";
			   else print "class=\"name$style\">";
 			   print PrintReady($name);
			   // NOTE: IMG ID
			   print "<img id=\"box-$pid.$personcount.$count-sex\" src=\"$PGV_IMAGE_DIR/";
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
				   if (hasRTLText($addname) && $style=="1") print "<span id=\"addnamedef-$pid.$personcount.$count\" class=\"name2\"> ";
				   else print "<span id=\"addnamedef-$pid.$personcount.$count\" class=\"name$style\"> ";
 				   print PrintReady($addname)."</span><br />";
			 }
		     print "</a>";
		  }
		  else {
			   $user = getUser($CONTACT_EMAIL);
			   print "<a href=\"javascript:;\" onclick=\"if (confirm('".preg_replace("'<br />'", " ", $pgv_lang["privacy_error"])."\\n\\n".str_replace("#user[fullname]#", $user["firstname"]." ".$user["lastname"], $pgv_lang["clicking_ok"])."')) ";
			   if ($CONTACT_METHOD!="none") {
					if ($CONTACT_METHOD=="mailto") print "window.location = 'mailto:".$user["email"]."'; ";
					else print "message('$CONTACT_EMAIL', '$CONTACT_METHOD'); ";
			   }
			   // NOTE: Start span namedef-$pid.$personcount.$count
			   // NOTE: Close span namedef-$pid.$personcount.$count
			   print "return false;\"><span id=\"namedef-$pid.$personcount.$count\" class=\"name$style\">".$pgv_lang["private"]."</span></a>\n";
		  }
		  if ($show_full) {
			  // NOTE: Start span fontdef-$pid.$personcount.$count
			  // NOTE: Close span fontdef-$pid.$personcount.$count
			   print "<br /><span id=\"fontdef-$pid.$personcount.$count\" class=\"details$style\">";
			   print $pgv_lang["private"];
			   print "</span>";
		  }
		  // NOTE: Close div out-$pid.$personcount.$count
		  print "\n\t\t\t</td></tr></table></div>";
		  return;
	 }
	 print "<a href=\"individual.php?pid=$pid&amp;ged=$GEDCOM\"";
	 if (!$show_full) {
		  //not needed or wanted for mouseover //if ($ZOOM_BOXES=="mouseover") print " onmouseover=\"event.cancelBubble = true;\"";
		  if ($ZOOM_BOXES=="mousedown") print "onmousedown=\"event.cancelBubble = true;\"";
		  if ($ZOOM_BOXES=="click") print "onclick=\"event.cancelBubble = true;\"";
	 }
	 // NOTE: Start span namedef-$pid.$personcount.$count
	 if (hasRTLText($name) && $style=="1") print "><span id=\"namedef-$pid.$personcount.$count\" class=\"name2";
	 else print "><span id=\"namedef-$pid.$personcount.$count\" class=\"name$style";
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
	 print "<img id=\"box-$pid.$personcount.$count-sex\" src=\"$PGV_IMAGE_DIR/";
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
	 // NOTE: Start div inout-$pid.$personcount.$count
	 if (!$show_full) print "\n<div id=\"inout-$pid.$personcount.$count\" style=\"display: none;\">\n";
	 // NOTE: Start div fontdev-$pid.$personcount.$count
	 print "<div id=\"fontdef-$pid.$personcount.$count\" class=\"details$style\">";

	 // NOTE: Start div inout2-$pid.$personcount.$count
//	 if ($show_full) print "\n<div id=\"inout2-$pid.$personcount.$count\" style=\"display: block;\">\n";
	 print "\n<div id=\"inout2-$pid.$personcount.$count\" ";
	 if ($show_full) print " style=\"display: block;\">\n";
	 else print " style=\"display: none;\">\n";
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

	 // NOTE: Close div inout2-$pid.$personcount.$count
//	 if ($show_full) print "</div>\n";
	 print "</div>\n";

	 //-- find all level 1 sub records
	  $skipfacts = array("SEX","FAMS","FAMC","NAME","TITL","NOTE","SOUR","SSN","OBJE","HUSB","WIFE","CHIL","ALIA","ADDR","PHON","SUBM","_EMAIL","CHAN","URL","EMAIL","WWW","RESI");
	  $subfacts = get_all_subrecords($indirec, implode(",", $skipfacts));
	  // NOTE: Open div inout-$pid.$personcount.$count
	  if ($show_full) print "\n<div id=\"inout-$pid.$personcount.$count\" style=\"display: none;\">\n";
	  $f2 = 0;
	  foreach($subfacts as $indexval => $factrec) {
		  if (!FactViewRestricted($pid, $factrec)){
			if ($f2>0) print "<br />\n";
			$f2++;
			// handle ASSO record
			if (strstr($factrec, "1 ASSO")) {
				print_asso_rela_record($pid, $factrec, false);
				continue;
			}
			$fft = preg_match("/^1 (\w+)(.*)/m", $factrec, $ffmatch);
			if ($fft>0) {
					$fact = trim($ffmatch[1]);
					$details = trim($ffmatch[2]);
				}
			if (($fact!="EVEN")&&($fact!="FACT")) print "<span class=\"details_label\">".$factarray[$fact]."</span> ";
			else {
				$tct = preg_match("/2 TYPE (.*)/", $factrec, $match);
				if ($tct>0) {
					 $facttype = trim($match[1]);
					 print "<span class=\"details_label\">";
					 if (isset($factarray[$facttype])) print PrintReady($factarray[$facttype]);
					 else print $facttype;
					 print "</span> ";
				}
			}
			if (get_sub_record(2, "2 DATE", $factrec)=="") {
				if ($details!="Y" && $details!="N") print PrintReady($details);
			}
			else print PrintReady($details);
			print_fact_date($factrec, false, false, $fact, $pid, $indirec);
			//-- print spouse name for marriage events
			$ct = preg_match("/_PGVFS @(.*)@/", $factrec, $match);
			if ($ct>0) {
				$famid = $match[1];
			}
			$ct = preg_match("/_PGVS @(.*)@/", $factrec, $match);
			if ($ct>0) {
				$spouse=$match[1];
				if ($spouse!=="") {
					 print " <a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
					 if (displayDetailsById($spouse)||showLivingNameById($spouse)) print PrintReady(get_person_name($spouse));
					 else print $pgv_lang["private"];
					 print "</a>";
				}
				if (($view!="preview") && ($spouse!=="")) print " - ";
				if ($view!="preview") print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"]."]</a>\n";
			}
			print_fact_place($factrec, true, true);
		}
	  }
	 // NOTE: Close div inout-$pid.$personcount.$count
	 if ($show_full) print "</div>\n";
	 // NOTE: Close div fontdev-$pid.$personcount.$count
	 if (!$show_full) print "</div>\n";
	 // NOTE: Close div inout-$pid.$personcount.$count
	 print "</div>";
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
	global $BROWSERTYPE;
	global $view, $cart;
	global $CHARACTER_SET, $VERSION, $PGV_IMAGE_DIR, $GEDCOMS, $GEDCOM, $CONTACT_EMAIL, $COMMON_NAMES_THRESHOLD, $INDEX_DIRECTORY;
	global $SCRIPT_NAME, $QUERY_STRING, $action, $query, $changelanguage,$theme_name;
	global $FAVICON, $stylesheet, $print_stylesheet, $rtl_stylesheet, $headerfile, $toplinks, $THEME_DIR, $print_headerfile;
	global $PGV_IMAGES, $TEXT_DIRECTION, $ONLOADFUNCTION,$REQUIRE_AUTHENTICATION, $SHOW_SOURCES;
	global $META_AUTHOR, $META_PUBLISHER, $META_COPYRIGHT, $META_DESCRIPTION, $META_PAGE_TOPIC, $META_AUDIENCE, $META_PAGE_TYPE, $META_ROBOTS, $META_REVISIT, $META_KEYWORDS, $META_TITLE, $META_SURNAME_KEYWORDS;
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
	 if (!$REQUIRE_AUTHENTICATION){
		print "<link href=\"" . $SERVER_URL .  "rss.php\" rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" />\n\t";
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
	 print "<!-- PhpGedView v$VERSION -->\n";
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
		  $surnames = get_common_surnames_index($GEDCOM);
		  foreach($surnames as $surname=>$count) if (!empty($surname)) print ", $surname";
		  print "\" />\n";
		  if ((empty($META_DESCRIPTION))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_DESCRIPTION = $GEDCOMS[$GEDCOM]["title"];
		  if ((empty($META_PAGE_TOPIC))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_PAGE_TOPIC = $GEDCOMS[$GEDCOM]["title"];
		  if (!empty($META_DESCRIPTION)) print "<meta name=\"description\" content=\"".preg_replace("/\"/", "", $META_DESCRIPTION)."\" />\n";
		  if (!empty($META_PAGE_TOPIC)) print "<meta name=\"page-topic\" content=\"".preg_replace("/\"/", "", $META_PAGE_TOPIC)."\" />\n";
	 	  if (!empty($META_AUDIENCE)) print "<meta name=\"audience\" content=\"$META_AUDIENCE\" />\n";
	 	  if (!empty($META_PAGE_TYPE)) print "<meta name=\"page-type\" content=\"$META_PAGE_TYPE\" />\n";
	 	  if (!empty($META_ROBOTS)) print "<meta name=\"robots\" content=\"$META_ROBOTS\" />\n";
	 	  if (!empty($META_REVISIT)) print "<meta name=\"revisit-after\" content=\"$META_REVISIT\" />\n";
		  print "<meta name=\"generator\" content=\"PhpGedView v$VERSION - http://www.phpgedview.net\" />\n";
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
	 var backlink = document.getElementById('backlink');
	 if (backlink) {
		  backlink.style.display='block';
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
function delete_record(pid, linenum) {
	 if (confirm('<?php print $pgv_lang["check_delete"]; ?>')) {
		  window.open('edit_interface.php?action=delete&pid='+pid+'&linenum='+linenum+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}
function deleteperson(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_person"]; ?>')) {
		  window.open('edit_interface.php?action=deleteperson&pid='+pid+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}

function deleterepository(pid) {
	 if (confirm('<?php print $pgv_lang["confirm_delete_repo"]; ?>')) {
		  window.open('edit_interface.php?action=deleterepo&pid='+pid+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 }
	 return false;
}
function message(username, method, url, subject) {
	 if ((!url)||(url=="")) url='<?php print urlencode(basename($SCRIPT_NAME)."?".$QUERY_STRING); ?>';
	 if ((!subject)||(subject=="")) subject= '';
	 window.open('message.php?to='+username+'&method='+method+'&url='+url+'&subject='+subject+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}
var whichhelp = 'help_<?php print basename($SCRIPT_NAME)."&amp;action=".$action; ?>';
//-->
</script>
<script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>
<?php
	 print $head;
	 print "</head>\n\t<body";
	 if ($view=="preview") print " onbeforeprint=\"hidePrint();\" onafterprint=\"showBack();\"";
	 if ($TEXT_DIRECTION=="rtl" || !empty($ONLOADFUNCTION)) {
		 print " onload=\"$ONLOADFUNCTION";
	 	if ($TEXT_DIRECTION=="rtl") print " maxscroll = document.documentElement.scrollLeft;";
	 	print " loadHandler();";
	 	print "\"";
 	}
 	else print " onload=\"loadHandler();\"";
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
	 global $HOME_SITE_TEXT;
	 global $view, $rtl_stylesheet;
	 global $CHARACTER_SET, $VERSION, $PGV_IMAGE_DIR;
	 global $SCRIPT_NAME, $QUERY_STRING, $action, $query, $changelanguage;
	 global $FAVICON, $stylesheet, $headerfile, $toplinks, $THEME_DIR, $print_headerfile, $SCRIPT_NAME;
	 global $TEXT_DIRECTION, $GEDCOMS, $GEDCOM, $CONTACT_EMAIL, $COMMON_NAMES_THRESHOLD,$PGV_IMAGES;
	 global $META_AUTHOR, $META_PUBLISHER, $META_COPYRIGHT, $META_DESCRIPTION, $META_PAGE_TOPIC, $META_AUDIENCE, $META_PAGE_TYPE, $META_ROBOTS, $META_REVISIT, $META_KEYWORDS, $META_TITLE;
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
		  $surnames = get_common_surnames_index($GEDCOM);
		  foreach($surnames as $surname=>$count) print ", $surname";
		  print "\" />\n";
		  if ((empty($META_DESCRIPTION))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_DESCRIPTION = $GEDCOMS[$GEDCOM]["title"];
		  if ((empty($META_PAGE_TOPIC))&&(!empty($GEDCOMS[$GEDCOM]["title"]))) $META_PAGE_TOPIC = $GEDCOMS[$GEDCOM]["title"];
		  if (!empty($META_DESCRIPTION)) print "<meta name=\"description\" content=\"".preg_replace("/\"/", "", $META_DESCRIPTION)."\" />\n";
		  if (!empty($META_PAGE_TOPIC)) print "<meta name=\"page-topic\" content=\"".preg_replace("/\"/", "", $META_PAGE_TOPIC)."\" />\n";
	 	  if (!empty($META_AUDIENCE)) print "<meta name=\"audience\" content=\"$META_AUDIENCE\" />\n";
	 	  if (!empty($META_PAGE_TYPE)) print "<meta name=\"page-type\" content=\"$META_PAGE_TYPE\" />\n";
	 	  if (!empty($META_ROBOTS)) print "<meta name=\"robots\" content=\"$META_ROBOTS\" />\n";
	 	  if (!empty($META_REVISIT)) print "<meta name=\"revisit-after\" content=\"$META_REVISIT\" />\n";
		  print "<meta name=\"generator\" content=\"PhpGedView v$VERSION - http://www.phpgedview.net\" />\n";
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
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('help_text.php?help='+which,'','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'help_text.php?help='+which;
		return false;
	}
function message(username, method, url, subject) {
	 if ((!url)||(url=="")) url='<?php print urlencode(basename($SCRIPT_NAME)."?".$QUERY_STRING); ?>';
	 if ((!subject)||(subject=="")) subject= '';
	 window.open('message.php?to='+username+'&method='+method+'&url='+url+'&subject='+subject+"&"+sessionname+"="+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}
	 //-->
	 </script>
	 <script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>
	 <?php
	 print "</head>\n\t<body style=\"margin: 5px;\"";
	 print " onload=\"loadHandler();\">\n\t";
}
// -- print the html to close the page
function print_footer() {
	 global $without_close, $pgv_lang, $view, $buildindex, $pgv_changes, $VERSION_RELEASE, $DBTYPE;
	 global $VERSION, $SHOW_STATS, $SCRIPT_NAME, $QUERY_STRING, $footerfile, $print_footerfile, $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $printlink;
	 global $PGV_IMAGE_DIR, $theme_name, $PGV_IMAGES, $TEXT_DIRECTION, $footer_count;
	 if (!isset($footer_count)) $footer_count = 1;
	 else $footer_count++;
	 print "<!-- begin footer -->\n";
	 $QUERY_STRING = preg_replace("/&/", "&", $QUERY_STRING);
	 if ($view!="preview") {
		  include($footerfile);
	 }
	 else {
		  include($print_footerfile);
		  print "\n\t<div style=\"text-align: center; width: 95%\"><br />";
		  $backlink = $SCRIPT_NAME."?".get_query_string();
		  if (!$printlink) {
			   print "\n\t<br /><a id=\"printlink\" href=\"javascript:;\" onclick=\"print(); return false;\">".$pgv_lang["print"]."</a><br />";
			   print "\n\t <a id=\"printlinktwo\"	  href=\"javascript:;\" onclick=\"window.location='".$backlink."'; return false;\">".$pgv_lang["cancel_preview"]."</a><br />";
		  }
		  $printlink = true;
		  print "\n\t<a id=\"backlink\" style=\"display: none;\" href=\"javascript:;\" onclick=\"window.location='".$backlink."'; return false;\">".$pgv_lang["cancel_preview"]."</a><br />";
		  print "</div>";
	 }
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
	 print "\n\t<a href=\"http://www.phpgedview.net\" target=\"_blank\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["gedview"]["other"]."\" border=\"0\" alt=\"PhpGedView Version $VERSION\" title=\"PhpGedView Version $VERSION\" /></a><br />";
	 if ($SHOW_STATS) print_execution_stats();
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
	 global $LOGIN_URL;
	 $username = getUserName();
	 $user = getUser($username);
	 if ($user && !empty($username)) {
		  print '<a href="edituser.php" class="link">'.$pgv_lang["logged_in_as"].' ('.$username.')</a><br />';
		  if ($user["canadmin"] || (userGedcomAdmin($username, $GEDCOM))) print "<a href=\"admin.php\" class=\"link\">".$pgv_lang["admin"]."</a> | ";
		  print "<a href=\"index.php?logout=1\" class=\"link\">".$pgv_lang["logout"]."</a>";
	 }
	 else {
		  $QUERY_STRING = preg_replace("/logout=1/", "", $QUERY_STRING);
		  print "<a href=\"$LOGIN_URL?url=".urlencode(basename($SCRIPT_NAME)."?".$QUERY_STRING."&amp;ged=$GEDCOM")."\" class=\"link\">".$pgv_lang["login"]."</a>";
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
					  print $pgv_lang["for_contact"]." <a href=\"javascript:;\" accesskey=\"". $pgv_lang["accesskey_contact"] ."\" onclick=\"message('$CONTACT_EMAIL', '$CONTACT_METHOD'); return false;\">".$user["firstname"]." ".$user["lastname"]."</a><br /><br />\n";
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
function print_favorite_selector($option=0) {
	global $pgv_lang, $GEDCOM, $SCRIPT_NAME, $SHOW_ID_NUMBERS, $pid, $INDEX_DIRECTORY, $indilist, $QUERY_STRING, $famid, $sid, $SHOW_FAM_ID_NUMBERS;
	global $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$username = getUserName();
	if (!empty($username)) $userfavs = getUserFavorites($username);
	else {
		if ($REQUIRE_AUTHENTICATION) return false;
		$userfavs = array();
	}
	if (empty($pid)&&(!empty($famid))) $pid = $famid;
	if (empty($pid)&&(!empty($sid))) $pid = $sid;
	$gedcomfavs = getUserFavorites($GEDCOM);
	if ((empty($username))&&(count($gedcomfavs)==0)) return;
	print "<div class=\"favorites_form\">\n";
	switch($option) {
		case 1:
			$menu = array();
			$menu["label"] = $pgv_lang["my_favorites"];
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
			foreach($userfavs as $key=>$favorite) {
				$pid = $favorite["gid"];
				$current_gedcom = $GEDCOM;
				$GEDCOM = $favorite["file"];
				$submenu = array();
				if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
					$submenu["link"] = $favorite["url"]."&amp;ged=$GEDCOM";
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
							if ($SHOW_ID_NUMBERS)
	 							if ($TEXT_DIRECTION=="ltr")
									 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
								else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
						}
						if ($favorite["type"]=="FAM") {
							$submenu["link"] = "family.php?famid=".$favorite["gid"]."&amp;ged=$GEDCOM";
							$submenu["label"] = PrintReady(get_family_descriptor($favorite["gid"]));
							if ($SHOW_FAM_ID_NUMBERS)
	 							if ($TEXT_DIRECTION=="ltr")
									 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
								else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
						}
						if ($favorite["type"]=="SOUR") {
							$submenu["link"] = "source.php?sid=".$favorite["gid"]."&amp;ged=$GEDCOM";
							$submenu["label"] = PrintReady(get_source_descriptor($favorite["gid"]));
							if ($SHOW_ID_NUMBERS)
	 							if ($TEXT_DIRECTION=="ltr")
									 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
								else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
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
				$submenu["label"] = $pgv_lang["gedcom_favorites"];
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
						$submenu["link"] = $favorite["url"]."&amp;ged=$GEDCOM";
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
								if ($SHOW_ID_NUMBERS)
	 								if ($TEXT_DIRECTION=="ltr")
									 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							if ($favorite["type"]=="FAM") {
								$submenu["link"] = "family.php?famid=".$favorite["gid"]."&amp;ged=$GEDCOM";
								$submenu["label"] = PrintReady(get_family_descriptor($favorite["gid"]));
								if ($SHOW_FAM_ID_NUMBERS)
	 								if ($TEXT_DIRECTION=="ltr")
										 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
							}
							if ($favorite["type"]=="SOUR") {
								$submenu["link"] = "source.php?sid=".$favorite["gid"]."&amp;ged=$GEDCOM";
								$submenu["label"] = PrintReady(get_source_descriptor($favorite["gid"]));
								if ($SHOW_ID_NUMBERS)
	 								if ($TEXT_DIRECTION=="ltr")
										 $submenu["label"] .= " &lrm;(".$favorite["gid"].")&lrm;";
									else $submenu["label"] .= " &rlm;(".$favorite["gid"].")&rlm;";
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
			   if (!empty($username)) {
					print "\n\t\t\t<option value=\"\">- ".$pgv_lang["my_favorites"]." -</option>";
					$mygedcom = $GEDCOM;
					$current_gedcom = $GEDCOM;
					$mypid = $pid;
					foreach($userfavs as $key=>$favorite) {
						 $current_gedcom = $GEDCOM;
						$GEDCOM = $favorite["file"];
						$pid = $favorite["gid"];
						if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
							print "\n\t\t\t<option value=\"".$favorite["url"]."&amp;ged=$GEDCOM\">".PrintReady($favorite["title"]);
							print "</option>";
						}
						else {
							if (displayDetailsById($pid, $favorite["type"])) {
								$indirec = find_gedcom_record($pid);
								$name = $pgv_lang["unknown"];
								if ($favorite["type"]=="INDI") {
									$name = strip_tags(PrintReady(get_person_name($pid)));
									print "\n\t\t\t<option value=\"individual.php?pid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".$name;
									if ($SHOW_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
										print " &lrm;(".$favorite["gid"].")&lrm;";
								   else print " &rlm;(".$favorite["gid"].")&rlm;";
								}
								if ($favorite["type"]=="FAM") {
									$name = strip_tags(PrintReady(get_family_descriptor($pid)));
									if (strlen($name)>50) $name = substr($name, 0, 50);
									print "\n\t\t\t<option value=\"family.php?famid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".$name;
									if ($SHOW_FAM_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
										print " &lrm;(".$favorite["gid"].")&lrm;";
								   else print " &rlm;(".$favorite["gid"].")&rlm;";
								}
								if ($favorite["type"]=="SOUR") {
									$name = strip_tags(PrintReady(get_source_descriptor($pid)));
									if (strlen($name)>50) $name = substr($name, 0, 50);
									print "\n\t\t\t<option value=\"source.php?sid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".$name;
									if ($SHOW_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
										print " &lrm;(".$favorite["gid"].")&lrm;";
								   else print " &rlm;(".$favorite["gid"].")&rlm;";
								}
								print "</option>";
							}
						}
					}
					$GEDCOM = $mygedcom;
					$pid = $mypid;
			   }
			   if (count($gedcomfavs)>0) {
					print "<option value=\"\">- ".$pgv_lang["gedcom_favorites"]." -</option>\n";
					$mygedcom = $GEDCOM;
					$current_gedcom = $GEDCOM;
					$mypid = $pid;
					foreach($gedcomfavs as $key=>$favorite) {
						$current_gedcom = $GEDCOM;
						$GEDCOM = $favorite["file"];
						$pid = $favorite["gid"];
						if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
							print "\n\t\t\t<option value=\"".$favorite["url"]."&amp;ged=$GEDCOM\">".PrintReady($favorite["title"]);
							print "</option>";
						}
						else {
						$indirec = find_gedcom_record($pid);
						$name = $pgv_lang["unknown"];
						if (displayDetailsById($pid, $favorite["type"])) {
							if ($favorite["type"]=="INDI") {
								$name = strip_tags(PrintReady(get_person_name($pid)));
								print "\n\t\t\t<option value=\"individual.php?pid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".$name;
								if ($SHOW_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
									print " &lrm;(".$favorite["gid"].")&lrm;";
							   else print " &rlm;(".$favorite["gid"].")&rlm;";
							}
							if ($favorite["type"]=="FAM") {
								$name = strip_tags(PrintReady(get_family_descriptor($pid)));
								print "\n\t\t\t<option value=\"family.php?famid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".$name;
								if ($SHOW_FAM_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
									print " &lrm;(".$favorite["gid"].")&lrm;";
							   else print " &rlm;(".$favorite["gid"].")&rlm;";
							}
							if ($favorite["type"]=="SOUR") {
								$name = strip_tags(PrintReady(get_source_descriptor($pid)));
								print "\n\t\t\t<option value=\"source.php?sid=".$favorite["gid"]."&amp;ged=$GEDCOM\">".$name;
								if ($SHOW_ID_NUMBERS)
								   if ($TEXT_DIRECTION=="ltr")
									print " &lrm;(".$favorite["gid"].")&lrm;";
							   else print " &rlm;(".$favorite["gid"].")&rlm;";
							}
							print "</option>";
						}
						}
					}
					$GEDCOM = $mygedcom;
					$pid = $mypid;
			   }
			   if ((!empty($username))&&(strpos($_SERVER["SCRIPT_NAME"], "individual.php")!==false ||
			   		strpos($_SERVER["SCRIPT_NAME"], "family.php")!==false ||
			   		strpos($_SERVER["SCRIPT_NAME"], "source.php")!==false)) print "<option value=\"add\">- ".$pgv_lang["add_to_my_favorites"]." -</option>\n";
			   print "</select>\n\t</form>\n";
			   break;
	 }
	 print "</div>\n";
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
// John wanted to define once for the session - how?? - this works only for standard theme?? MA
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
 * print a fact record
 *
 * prints a fact record designed for the personal facts and details page
 * @param string $factrec	The gedcom subrecord
 * @param string $pid		The Gedcom Xref ID of the person the fact belongs to (required to check fact privacy)
 * @param int $linenum		The line number where this fact started in the original gedcom record (required for editing)
 * @param string $indirec	optional INDI record for age calculation at family event
 */
function print_fact($factrec, $pid, $linenum, $indirec=false) {
	 global $factarray;
	 global $nonfacts, $birthyear, $birthmonth, $birthdate;
	 global $hebrew_birthyear, $hebrew_birthmonth, $hebrew_birthdate;
	 global $BOXFILLCOLOR, $PGV_IMAGE_DIR;
	 global $pgv_lang, $GEDCOM;
	 global $WORD_WRAPPED_NOTES;
	 global $TEXT_DIRECTION;
	 global $HIDE_GEDCOM_ERRORS, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS;
	 global $CONTACT_EMAIL, $view, $FACT_COUNT, $monthtonum;
	 global $dHebrew;
	 global $n_chil, $n_gchi;
	 $FACT_COUNT++;
	 $estimates = array("abt","aft","bef","est","cir");
	 $ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
	 if ($ft>0) {
		  $fact = trim($match[1]);
		  $event = trim($match[2]);
	 }
	 else {
		  $fact="";
		  $event="";
	 }
	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="change_new";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="change_old";
	 if ($linenum<1) $styleadd="rela"; // not editable
	 // -- avoid known non facts
	 if (in_array($fact, $nonfacts)) return;
	 //-- do not print empty facts
	 $lines = preg_split("/\n/", trim($factrec));
	 if ((count($lines)<2)&&($event=="")) return;
	 // See if RESN tag prevents display or edit/delete
	 $resn_tag = preg_match("/2 RESN (.*)/", $factrec, $match);
	 if ($resn_tag == "1") $resn_value = strtolower(trim($match[1]));
	 if (array_key_exists($fact, $factarray)) {
		  // -- handle generic facts
		  if ($fact!="EVEN" && $fact!="FACT" && $fact!="OBJE") {
			   $factref = $fact;
			   if (!showFact($factref, $pid)) return false;
			   // The two lines below do not validate because when $styleadd does
			   // not have a value, we create several instances of the same ID.
			   // Furthermore, "name" is not valid for a TR tag.
			   print "\n\t\t<tr id=\"row_".$styleadd."\" name=\"row_".$styleadd."\">";
			   //print "\n\t\t\t<td class=\"facts_label facts_label$styleadd\">";
				print "\n\t\t\t<td class=\"descriptionbox $styleadd center width20\">";
			   if (file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/".$factref.".gif\"  alt=\"".$factarray[$fact]."\" title=\"".$factarray[$fact]."\" align=\"middle\" /> ";
			   print $factarray[$fact];
			   if ($fact=="_BIRT_CHIL" and isset($n_chil)) print "<br />".$pgv_lang["number_sign"].$n_chil++;
			   if ($fact=="_BIRT_GCHI" and isset($n_gchi)) print "<br />".$pgv_lang["number_sign"].$n_gchi++;
			   if ((userCanEdit(getUserName()))&&($styleadd!="red")&&($linenum>0)&&($view!="preview")&&(!FactEditRestricted($pid, $factrec))) {
					$menu = array();
					$menu["label"] = $pgv_lang["edit"];
					$menu["labelpos"] = "right";
					$menu["icon"] = "";
					$menu["link"] = "#";
					$menu["onclick"] = "return edit_record('$pid', $linenum);";
					$menu["class"] = "";
					$menu["hoverclass"] = "";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "submenu";
					$menu["items"] = array();
					$submenu = array();
					$submenu["label"] = $pgv_lang["edit"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return edit_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["copy"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return copy_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["delete"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return delete_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					print " <div style=\"width:25px;\">";
					print_menu($menu);
					print "</div>";
			   }
			   print "</td>";
		  }
		  else {
			   if ($fact == "OBJE") return false;
			   if (!showFact("EVEN", $pid)) return false;
			   // -- find generic type for each fact
			   $ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
			   if ($ct>0) $factref = trim($match[1]);
			   else $factref = $fact;
			   if (!showFact($factref, $pid)) return false;
			   // The two lines below do not validate because when $styleadd does
			   // not have a value, we create several instances of the same ID.
			   // Furthermore, "name" is not valid for a TR tag.
			   print "\n\t\t<tr id=\"row_".$styleadd."\" name=\"row_".$styleadd."\">";
			   if (isset($factarray["$factref"])) $label = $factarray[$factref];
				else $label = $factref;
			   //print "<td class=\"facts_label facts_label$styleadd\">" . $label;
				print "<td class=\"descriptionbox $styleadd center width20\">";
			   if (file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/".$factref.".gif\" alt=\"".$label."\" title=\"".$label."\" align=\"middle\" /> ";
			   print $label;
			   if ((userCanEdit(getUserName()))&&($styleadd!="red")&&($linenum>0)&&($view!="preview")&&(!FactEditRestricted($pid, $factrec))) {
				   $menu = array();
					$menu["label"] = $pgv_lang["edit"];
					$menu["labelpos"] = "right";
					$menu["icon"] = "";
					$menu["link"] = "#";
					$menu["onclick"] = "return edit_record('$pid', $linenum);";
					$menu["class"] = "";
					$menu["hoverclass"] = "";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "submenu";
					$menu["items"] = array();
					$submenu = array();
					$submenu["label"] = $pgv_lang["edit"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return edit_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["delete"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return delete_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					$submenu = array();
					$submenu["label"] = $pgv_lang["copy"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					$submenu["onclick"] = "return copy_record('$pid', $linenum);";
					$submenu["link"] = "#";
					$submenu["class"] = "submenuitem";
					$submenu["hoverclass"] = "submenuitem_hover";
					$menu["items"][] = $submenu;
					print " <div style=\"width:25px;\">";
					print_menu($menu);
					print "</div>";
				}
			   print "</td>";
		  }
		  print "<td class=\"optionbox $styleadd wrap\">";
		  //print "<td class=\"facts_value facts_value$styleadd\">";
		  $user = getUser(getUserName());
		  if ((showFactDetails($factref, $pid)) && (FactViewRestricted($pid, $factrec))) {
			   if (isset($resn_value)) {
					print "<img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					// print_help_link("RESN_help", "qm");
				}
			   /**
			   print $factarray["RESN"].": ";
			   if (isset($pgv_lang[$resn_value])) print $pgv_lang[$resn_value];
			   else if (isset($factarray[$resn_value])) print $factarray[$resn_value];
			   else print $resn_value;
			   print "<br />\n";
			   **/
		  }
		  if ((showFactDetails($factref, $pid)) && (!FactViewRestricted($pid, $factrec))) {
				// -- first print TYPE for some facts
				if ($fact!="EVEN" && $fact!="FACT") {
					$ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
					if ($ct>0) {
						$type = trim($match[1]);
						if (isset ($factarray["MARR_".str2upper($type)])) print $factarray["MARR_".str2upper($type)];
						else if (isset($factarray[$type])) print $factarray[$type];
						else if (isset($pgv_lang[$type])) print $pgv_lang[$type];
						else print $type;
						print "<br />";
					}
				}
			   // -- find date for each fact
			   print_fact_date($factrec, true, true, $fact, $pid, $indirec);
			   //-- print spouse name for marriage events
			   $ct = preg_match("/_PGVS @(.*)@/", $factrec, $match);
			   if ($ct>0) {
					$spouse=$match[1];
					if ($spouse!=="") {
						 print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
						 if (displayDetailsById($spouse)||showLivingNameById($spouse)) {
							 print PrintReady(get_person_name($spouse));
							 $addname = get_add_person_name($spouse);
							 if ($addname!="") print " - ".PrintReady($addname);
						 }
						 else print $pgv_lang["private"];
						 print "</a>";
					}
					if (($view!="preview") && ($spouse!=="")) print " - ";
					if ($view!="preview") {
	 				     print "<a href=\"family.php?famid=$pid\">";
						 if ($TEXT_DIRECTION == "ltr") print " &lrm;";
 						 else print " &rlm;";
						 print "[".$pgv_lang["view_family"];
  						 if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($pid)&lrm;";
  						 if ($TEXT_DIRECTION == "ltr") print "&lrm;]</a>\n";
 						 else print "&rlm;]</a>\n";
                    }
			   }
			   //-- print other characterizing fact information
			   if ($event!="" and $fact!="ASSO") {
					$ct = preg_match("/@(.*)@/", $event, $match);
					if ($ct>0) {
						 $gedrec = find_gedcom_record($match[1]);
						 if (strstr($gedrec, "INDI")!==false) print "<a href=\"individual.php?pid=$match[1]&amp;ged=$GEDCOM\">".get_person_name($match[1])."</a><br />";
						 else if ($fact=="REPO") print_repository_record($match[1]);
						 else print_submitter_info($match[1]);
					}
					else if ($fact=="ALIA") {
						 //-- strip // from ALIA tag for FTM generated gedcoms
						 print preg_replace("'/'", "", $event)."<br />";
					}
					/* -- see the print_fact_date function where this is handled
					else if ($event=="Y") {
						if (get_sub_record(2, "2 DATE", $factrec)=="") {
							print $pgv_lang["yes"]."<br />";
						}
					}
					else if ($event=="N") {
						if (get_sub_record(2, "2 DATE", $factrec)=="") {
							print $pgv_lang["no"]."<br />";
						}
					}*/
					else if (strstr("URL WWW ", $fact." ")) {
						 print "<a href=\"".$event."\" target=\"new\">".PrintReady($event)."</a>";
					}
					else if (strstr("_EMAIL", $fact)) {
						 print "<a href=\"mailto:".$event."\">".$event."</a>";
					}
 					else if (strstr("FAX", $fact)) print "&lrm;".$event." &lrm;";
					else if (!strstr("PHON ADDR ", $fact." ") && $event!="Y") print PrintReady($event." ");
					$temp = trim(get_cont(2, $factrec), "\r\n");
					if (strstr("PHON ADDR ", $fact." ")===false && $temp!="") {
						if ($WORD_WRAPPED_NOTES) print " ";
						print PrintReady($temp);
					}
			   }
			   //-- find description for some facts
			   $ct = preg_match("/2 DESC (.*)/", $factrec, $match);
			   if ($ct>0) print PrintReady($match[1]);
				// -- print PLACe, TEMPle and STATus
				print_fact_place($factrec, true, true, true);
 				if (preg_match("/ (PLAC)|(STAT)|(TEMP)|(SOUR) /", $factrec)>0 || (!empty($event)&&$fact!="ADDR")) print "<br />\n";
				// -- print BURIal -> CEMEtery
				$ct = preg_match("/2 CEME (.*)/", $factrec, $match);
				if ($ct>0) {
				   if (file_exists($PGV_IMAGE_DIR."/facts/CEME.gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/CEME.gif\" alt=\"".$factarray["CEME"]."\" title=\"".$factarray["CEME"]."\" align=\"middle\" /> ";
					print $factarray["CEME"].": ".$match[1]."<br />\n";
				}
			   //-- print address structure
			   if ($fact!="ADDR" && $fact!="PHON") {
				   print_address_structure($factrec, 2);
			   }
			   else {
				   print_address_structure($factrec, 1);
			   }
				// -- Enhanced ASSOciates > RELAtionship
				print_asso_rela_record($pid, $factrec,true);
			   // -- find _PGVU field
			   $ct = preg_match("/2 _PGVU (.*)/", $factrec, $match);
			   if ($ct>0) print $factarray["_PGVU"].": ".$match[1];
			   // -- Find RESN tag
			   if (isset($resn_value)) {
				   print "<img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
			   }
				if ($fact!="ADDR") {
					//-- catch all other facts that could be here
					$special_facts = array("ADDR","ALIA","ASSO","CEME","CONC","CONT","DATE","DESC","EMAIL",
					"FAMC","FAMS","FAX","NOTE","OBJE","PHON","PLAC","RESN","SOUR","STAT","TEMP",
					"TIME","TYPE","WWW","_EMAIL","_PGVU", "URL", "AGE");
					$ct = preg_match_all("/\n2 (\w+) (.*)/", $factrec, $match, PREG_SET_ORDER);
					if ($ct>0) print "<br />";
					for($i=0; $i<$ct; $i++) {
						$factref = $match[$i][1];
						if (!in_array($factref, $special_facts)) {
							if (isset($factarray[$factref])) $label = $factarray[$factref];
							else $label = $factref;
						   if (file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/".$factref.".gif\" alt=\"".$label."\" title=\"".$label."\" align=\"middle\" /> ";
						   else print "<span class=\"label\">".$label.": </span>";
							$value = trim($match[$i][2]);
							if (isset($pgv_lang[strtolower($value)])) print $pgv_lang[strtolower($value)];
							else print PrintReady($value);
							print "<br />\n";
						}
					}
				}
			   // -- find source for each fact
			   print_fact_sources($factrec, 2);
			   // -- find notes for each fact
			   print_fact_notes($factrec, 2);
			   //-- find multimedia objects
			   print_media_links($factrec, 2, $pid);
		  }
		  print "</td>";
		  print "\n\t\t</tr>";
	 }
	 else {
		  // -- catch all unknown codes here
		  $body = $pgv_lang["unrecognized_code"]." ".$fact;
		  if (!$HIDE_GEDCOM_ERRORS) print "\n\t\t<tr><td class=\"descriptionbox $styleadd\"><span class=\"error\">".$pgv_lang["unrecognized_code"].": $fact</span></td><td class=\"optionbox\">$event<br />".$pgv_lang["unrecognized_code_msg"]." <a href=\"javascript:;\" onclick=\"message('$CONTACT_EMAIL','', '', '$body'); return false;\">".$CONTACT_EMAIL."</a>.</td></tr>";
	 }
}
//------------------- end print fact function
/**
 * print a source linked to a fact (2 SOUR)
 *
 * this function is called by the print_fact function and other functions to
 * print any source information attached to the fact
 * @param string $factrec	The fact record to look for sources in
 * @param int $level		The level to look for sources at
 */
function print_fact_sources($factrec, $level) {
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES, $FACT_COUNT, $PGV_IMAGE_DIR, $FACT_COUNT, $PGV_IMAGES, $SHOW_SOURCES, $EXPAND_SOURCES;
	 $nlevel = $level+1;
	 if ($SHOW_SOURCES<getUserAccessLevel(getUserName())) return;
	 // -- Systems not using source records [ 1046971 ]
	 $ct = preg_match_all("/$level SOUR (.*)/", $factrec, $match, PREG_SET_ORDER);
	 for($j=0; $j<$ct; $j++) {
		if (strpos($match[$j][1], "@")===false) {
			$srec = get_sub_record($level, " SOUR ", $factrec, $j+1);
			$srec = substr($srec, 5); // remove SOUR
			$srec = str_replace("\n".($level+1)." CONT ", " ", $srec); // remove n+1 CONT
			$srec = str_replace("\n".($level+1)." CONC ", "", $srec); // remove n+1 CONC
			print "<span class=\"label\">".$pgv_lang["source"].":</span> <span class=\"field\">".PrintReady($srec)."</span><br />";
		}
	 }
	 // -- find source for each fact
	 $ct = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	 $spos2 = 0;
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, "$level SOUR @".$match[$j][1]."@", $spos2);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $srec = substr($factrec, $spos1, $spos2-$spos1);
		  $lt = preg_match_all("/$nlevel \w+/", $srec, $matches);
		  if ($j > 0) print "<br />";
		  print "\n\t\t<span class=\"label\">";
		  $sid = $match[$j][1];
		  if ($lt>0) print "<a href=\"javascript:;\" onclick=\"expand_layer('$sid$j-$FACT_COUNT'); return false;\"><img id=\"{$sid}{$j}-{$FACT_COUNT}_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".$pgv_lang["show_details"]."\" title=\"".$pgv_lang["show_details"]."\" /></a> ";
		  print $pgv_lang["source"].":</span> <span class=\"field\"><a href=\"source.php?sid=".$sid."\">";
		  print PrintReady(get_source_descriptor($sid));
		  //-- Print additional source title
    	  $add_descriptor = get_add_source_descriptor($sid);
    	  if ($add_descriptor) print " - ".PrintReady($add_descriptor);
		  print "</a></span>";
		  print "<div id=\"$sid$j-$FACT_COUNT\" class=\"source_citations\">";
		   $cs = preg_match("/$nlevel PAGE (.*)/", $srec, $cmatch);
		   if ($cs>0) {
				print "\n\t\t\t<span class=\"label\">".$factarray["PAGE"].": </span><span class=\"field\">".PrintReady($cmatch[1]);
				$pagerec = get_sub_record($nlevel, $cmatch[0], $srec);
				$text = get_cont($nlevel+1, $pagerec);
				$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
				print PrintReady($text);
				print "</span>";
		   }
		   $cs = preg_match("/$nlevel EVEN (.*)/", $srec, $cmatch);
		   if ($cs>0) {
				print "<br /><span class=\"label\">".$factarray["EVEN"]." </span><span class=\"field\">".$cmatch[1]."</span>";
				$cs = preg_match("/".($nlevel+1)." ROLE (.*)/", $srec, $cmatch);
				if ($cs>0) print "\n\t\t\t<br /><span class=\"label\">".$factarray["ROLE"]." </span><span class=\"field\">$cmatch[1]</span>";
		   }
		   $cs = preg_match("/$nlevel DATA/", $srec, $cmatch);
		   if ($cs>0) {
				$cs = preg_match("/".($nlevel+1)." DATE (.*)/", $srec, $cmatch);
				if ($cs>0) print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["date"].": </span><span class=\"field\">".get_changed_date($cmatch[1])."</span>";
				$tt = preg_match_all("/".($nlevel+1)." TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
				for($k=0; $k<$tt; $k++) {
					 print "<br /><span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".PrintReady($tmatch[$k][1]);
					 print PrintReady(get_cont($nlevel+2, $srec));
					 print "</span>";
				}
		   }
		   $cs = preg_match("/".$nlevel." DATE (.*)/", $srec, $cmatch);
		   if ($cs>0) print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["date"].": </span><span class=\"field\">".get_changed_date($cmatch[1])."</span>";
		   $cs = preg_match("/$nlevel QUAY (.*)/", $srec, $cmatch);
		   if ($cs>0) print "<br /><span class=\"label\">".$factarray["QUAY"]." </span><span class=\"field\">".$cmatch[1]."</span>";
		   $cs = preg_match_all("/$nlevel TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
		   for($k=0; $k<$cs; $k++) {
				print "<br /><span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".$tmatch[$k][1];
				$text = get_cont($nlevel+1, $srec);
				$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
				print PrintReady($text);
				print "</span>";
		   }
		   print "<div class=\"indent\">";
		   print_media_links($srec, $nlevel);
		   print_fact_notes($srec, $nlevel);
		   print "</div>";
		   print "</div>";
			if ($lt>0 and $EXPAND_SOURCES) {
				print "\r\n<script language='JavaScript' type='text/javascript'>\r\n";
				print "<!--\r\n";
				print "expand_layer('$sid$j-$FACT_COUNT');\r\n";
				print "//-->\r\n";
				print "</script>\r\n";
			}
	 }
}
function print_main_sources($factrec, $level, $pid, $linenum) {
	 global $pgv_lang;
	 global $factarray, $view;
	 global $WORD_WRAPPED_NOTES, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_SOURCES;
	 if ($SHOW_SOURCES<getUserAccessLevel(getUserName())) return;
	 $nlevel = $level+1;
	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="change_new";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="change_old";
	 // -- find source for each fact
	 $ct = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	 $spos2 = 0;
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, "$level SOUR @".$match[$j][1]."@", $spos2);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $srec = substr($factrec, $spos1, $spos2-$spos1);
		  if (!showFact("SOUR", $pid) || FactViewRestricted($pid, $factrec)) return false;
		  print "\n\t\t\t<tr><td class=\"descriptionbox $styleadd center\">";
		  //print "\n\t\t\t<tr><td class=\"facts_label$styleadd\">";
		  print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["large"]."\" width=\"50\" height=\"50\" alt=\"\" /><br />";
		  print $pgv_lang["source"];
		  if (userCanEdit(getUserName())&&(!FactEditRestricted($pid, $factrec))&&($styleadd!="red")&&($view!="preview")) {
			  $menu = array();
				$menu["label"] = $pgv_lang["edit"];
				$menu["labelpos"] = "right";
				$menu["icon"] = "";
				$menu["link"] = "#";
				$menu["onclick"] = "return edit_record('$pid', $linenum);";
				$menu["class"] = "";
				$menu["hoverclass"] = "";
				$menu["flyout"] = "down";
				$menu["submenuclass"] = "submenu";
				$menu["items"] = array();
				$submenu = array();
				$submenu["label"] = $pgv_lang["edit"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return edit_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["delete"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return delete_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["copy"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return copy_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				print " <div style=\"width:25px;\">";
				print_menu($menu);
				print "</div>";
			}
		  print "</td>";
		  print "\n\t\t\t<td class=\"optionbox $styleadd\">";
		  //print "\n\t\t\t<td class=\"facts_value$styleadd\">";
		  if (showFactDetails("SOUR", $pid)) {
			   print "<a href=\"source.php?sid=".$match[$j][1]."\">";
    		   print PrintReady(get_source_descriptor($match[$j][1]));
    		   //-- Print additional source title
    		   $add_descriptor = get_add_source_descriptor($match[$j][1]);
    		   if ($add_descriptor) print " - ".PrintReady($add_descriptor);
			   print "</a>";
			   // See if RESN tag prevents display or edit/delete
	 			$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $rmatch);
	 			if ($resn_tag > 0) $resn_value = strtolower(trim($rmatch[1]));
			    // -- Find RESN tag
			   if (isset($resn_value)) {
				   print "<img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
			   }
			   $source = find_source_record($match[$j][1]);
			   if ($source) {
				    $cs = preg_match("/$nlevel PAGE (.*)/", $srec, $cmatch);
					if ($cs>0) {
						 print "\n\t\t\t<br />".$factarray["PAGE"].": $cmatch[1]";
						 $text = get_cont($nlevel+1, $srec);
						 $text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
						 print PrintReady($text);
					}
					$cs = preg_match("/$nlevel EVEN (.*)/", $srec, $cmatch);
					if ($cs>0) {
						 print "<br /><span class=\"label\">".$factarray["EVEN"]." </span><span class=\"field\">".$cmatch[1]."</span>";
						 $cs = preg_match("/".($nlevel+1)." ROLE (.*)/", $srec, $cmatch);
						 if ($cs>0) print "\n\t\t\t<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".$factarray["ROLE"]." </span><span class=\"field\">$cmatch[1]</span>";
					}
					$cs = preg_match("/$nlevel DATA/", $srec, $cmatch);
					if ($cs>0) {
						 print "<br /><span class=\"label\">".$factarray["DATA"]." </span>";
						 $cs = preg_match("/".($nlevel+1)." DATE (.*)/", $srec, $cmatch);
						 if ($cs>0) print "\n\t\t\t<br />&nbsp;&nbsp;<span class=\"label\">".$pgv_lang["date"].":  </span><span class=\"field\">$cmatch[1]</span>";
						 $tt = preg_match_all("/".($nlevel+1)." TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
						 for($k=0; $k<$tt; $k++) {
							  print "<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".$tmatch[$k][1]."</span>";
							  print get_cont($nlevel+2, $srec);
						 }
					}
					$cs = preg_match("/$nlevel QUAY (.*)/", $srec, $cmatch);
					if ($cs>0) print "<br /><span class=\"label\">".$factarray["QUAY"]." </span><span class=\"field\">".$cmatch[1]."</span>";
					$cs = preg_match_all("/$nlevel TEXT (.*)/", $srec, $tmatch, PREG_SET_ORDER);
					for($k=0; $k<$cs; $k++) {
						 print "<br /><span class=\"label\">".$pgv_lang["text"]." </span><span class=\"field\">".$tmatch[$k][1];
						 $trec = get_sub_record($nlevel, $tmatch[$k][0], $srec);
						 $text = get_cont($nlevel+1, $trec);
						 $text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
						 print $text;
						 print "</span>";
					}
					print_media_links($srec, $nlevel);
					print_fact_notes($srec, $nlevel);
			   }
		  }
		  print "</td></tr>";
	 }
}
//-- Print all of the notes in this fact record
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
			   $text = preg_replace("/~~/", "<br />", trim($match[$j][1]));
			   $text .= get_cont($nlevel, $nrec);
			   $text = preg_replace("'(http://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">URL</a>", $text);
			   $text = trim($text);
			   if (!empty($text)) {
				   print "\n\t\t<br /><span class=\"label\">".$pgv_lang["note"].": </span><span class=\"field\">";
			   	   print PrintReady($text);
			   	   $closeSpan = true;
		   		}
		  }
		  else {
			   //-- print linked note records
			   $noterec = find_gedcom_record($nmatch[1]);
			   $nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
			   $text ="";
			   if ($nt>0) $text = preg_replace("/~~/", "<br />", trim($n1match[1]));
			   $text .= get_cont(1, $noterec);
			   $text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">URL</a>", $text);
			   $text = trim($text);
			   if (!empty($text)) {
				   print "\n\t\t<br /><span class=\"label\">".$pgv_lang["note"].": </span><span class=\"field\">";
			   	   print PrintReady($text);
			   	   $closeSpan = true;
		   		}
		   		if (preg_match("/1 SOUR/", $noterec)>0) {
			   		print "<br />\n";
					print_fact_sources($noterec, 1);
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
 * print main note row
 *
 * this function will print a table row for a fact table for a level 1 note in the main record
 * @param string $factrec	the raw gedcom sub record for this note
 * @param int $level		The start level for this note, usually 1
 * @param string $pid		The gedcom XREF id for the level 0 record that this note is a part of
 * @param int $linenum		The line number in the level 0 record where this record was found.  This is used for online editing.
 */
function print_main_notes($factrec, $level, $pid, $linenum) {
	 global $pgv_lang;
	 global $factarray, $view;
	 global $WORD_WRAPPED_NOTES, $PGV_IMAGE_DIR;
	 global $PGV_IMAGES;
	 $styleadd="";
	 $ct = preg_match("/PGV_NEW/", $factrec, $match);
	 if ($ct>0) $styleadd="change_new";
	 $ct = preg_match("/PGV_OLD/", $factrec, $match);
	 if ($ct>0) $styleadd="change_old";
	 $nlevel = $level+1;
	 $ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	 for($j=0; $j<$ct; $j++) {
		  $spos1 = strpos($factrec, "$level NOTE ".$match[$j][1]);
		  $spos2 = strpos($factrec, "\n$level", $spos1);
		  if (!$spos2) $spos2 = strlen($factrec);
		  $nrec = substr($factrec, $spos1, $spos2-$spos1);
		  if (!showFact("NOTE", $pid)||FactViewRestricted($pid, $factrec)) return false;
		  print "\n\t\t<tr><td valign=\"top\" class=\"descriptionbox $styleadd\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["note"]["other"]."\" width=\"50\" height=\"50\" alt=\"\" /><br />".$pgv_lang["note"].":";
		  if (userCanEdit(getUserName())&&(!FactEditRestricted($pid, $factrec))&&($styleadd!="red")&&($view!="preview")) {
			$menu = array();
			$menu["label"] = $pgv_lang["edit"];
			$menu["labelpos"] = "right";
			$menu["icon"] = "";
			$menu["link"] = "#";
			$menu["onclick"] = "return edit_record('$pid', $linenum);";
			$menu["class"] = "";
			$menu["hoverclass"] = "";
			$menu["flyout"] = "down";
			$menu["submenuclass"] = "submenu";
			$menu["items"] = array();
			$submenu = array();
			$submenu["label"] = $pgv_lang["edit"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return edit_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			$submenu = array();
			$submenu["label"] = $pgv_lang["delete"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return delete_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			$submenu = array();
			$submenu["label"] = $pgv_lang["copy"];
			$submenu["labelpos"] = "right";
			$submenu["icon"] = "";
			$submenu["onclick"] = "return copy_record('$pid', $linenum);";
			$submenu["link"] = "#";
			$submenu["class"] = "submenuitem";
			$submenu["hoverclass"] = "submenuitem_hover";
			$menu["items"][] = $submenu;
			print " <div style=\"width:25px;\">";
			print_menu($menu);
			print "</div>";
		}
		  print " </td>\n<td class=\"optionbox $styleadd wrap\">";
		  if (showFactDetails("NOTE", $pid)) {
			   $nt = preg_match("/\d NOTE @(.*)@/", $match[$j][0], $nmatch);
			   if ($nt==0) {
					//-- print embedded note records
					$text = preg_replace("/~~/", "<br />", trim($match[$j][1]));
					$text .= get_cont($nlevel, $nrec);
					$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
					print PrintReady($text);
			   }
			   else {
					//-- print linked note records
					$noterec = find_gedcom_record($nmatch[1]);
					$nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
					$text ="";
					if ($nt>0) $text = preg_replace("/~~/", "<br />", trim($n1match[1]));
					$text .= get_cont(1, $noterec);
					$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
					print PrintReady($text)."<br />\n";
					print_fact_sources($noterec, 1);
			   }
			   // See if RESN tag prevents display or edit/delete
	 			$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $match);
	 			if ($resn_tag > 0) $resn_value = strtolower(trim($match[1]));
			    // -- Find RESN tag
			   if (isset($resn_value)) {
				   print "<br /><img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
			   }
			   print "<br />\n";
			   print_fact_sources($nrec, $nlevel);
		  }
		  print "</td></tr>";
	 }
}
//-- Print the links to multi-media objects
function print_main_media($factrec, $nlevel, $pid, $linenum) {
	global $MULTI_MEDIA, $TBLPREFIX, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS, $MEDIA_EXTERNAL;
	global $pgv_lang, $pgv_changes, $factarray, $view;
	global $GEDCOMS, $GEDCOM, $MEDIATYPE;
	global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION;

	if (!showFact("OBJE", $pid)) return false;

	$media_found = false;
	// NOTE: Determine if there are any changes
	$styleadd="";
	$ct = preg_match("/PGV_NEW/", $factrec, $match);
	if ($ct>0) $styleadd="change_new";
	$ct = preg_match("/PGV_OLD/", $factrec, $match);
	if ($ct>0) $styleadd="change_old";

	$sqlmm = "SELECT mm_media FROM ".$TBLPREFIX."media_mapping where mm_gid = '".$pid."' AND mm_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY mm_id ASC";
	$resmm =@ dbquery($sqlmm);
	while($rowmm =& $resmm->fetchRow(DB_FETCHMODE_ASSOC)){
		$sqlm = "SELECT * FROM ".$TBLPREFIX."media where m_media = '".$rowmm["mm_media"]."' AND m_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
		$resm =@ dbquery($sqlm);
		while($rowm =& $resm->fetchRow(DB_FETCHMODE_ASSOC)){
			// NOTE: Determine the size of the mediafile
			$imgwidth = 300;
			$imgheight = 300;
			if (preg_match("'://'", $rowm["m_file"])) {
				if (in_array($rowm["m_ext"], $MEDIATYPE)){
					$imgwidth = 400;
					$imgheight = 500;
				}
				else {
					$imgwidth = 800;
					$imgheight = 400;
				}
			}
			else if ((preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($rowm["m_file"])))) {
				$imgsize = @getimagesize(filename_decode($rowm["m_file"]));
				if ($imgsize) {
					$imgwidth = $imgsize[0]+50;
					$imgheight = $imgsize[1]+50;
				}
			}
			// NOTE: Start printing the media details
			$thumbnail = thumbnail_file($rowm["m_file"]);
			$linenum = 0;
			print "\n\t\t<tr><td class=\"descriptionbox $styleadd\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"]."\" width=\"50\" height=\"50\" alt=\"\" /><br />".$factarray["OBJE"].":";
			if (userCanEdit(getUserName())&&(!FactEditRestricted($pid, $factrec))&&($styleadd!="red")&&($view!="preview")) {
				$menu = array();
				$menu["label"] = $pgv_lang["edit"];
				$menu["labelpos"] = "right";
				$menu["icon"] = "";
				$menu["link"] = "#";
				// $menu["onclick"] = "return edit_record('$pid', $linenum);";
				$menu["onclick"] = "return window.open('addmedia.php?action=editmedia&amp;pid=".$rowm["m_media"]."', '', 'top=50,left=50,width=900,height=650,resizable=1,scrollbars=1');";
				$menu["class"] = "";
				$menu["hoverclass"] = "";
				$menu["flyout"] = "down";
				$menu["submenuclass"] = "submenu";
				$menu["items"] = array();
				$submenu = array();
				$submenu["label"] = $pgv_lang["edit"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return window.open('addmedia.php?action=editmedia&amp;pid=".$rowm["m_media"]."', '', 'top=50,left=50,width=900,height=650,resizable=1,scrollbars=1');";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["delete"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				// $submenu["onclick"] = "return delete_record('$pid', $linenum);";
				$submenu["onclick"] = "return window.open('addmedia.php?action=delete&amp;pid=".$rowm["m_media"]."', '', 'top=50,left=50,width=900,height=650,resizable=1,scrollbars=1');";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["copy"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = "";
				$submenu["onclick"] = "return copy_record('$pid', $linenum);";
				$submenu["link"] = "#";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$menu["items"][] = $submenu;
				print " <div style=\"width:25px;\">";
				print_menu($menu);
				print "</div>";
			}
			// NOTE Print the title of the media
			print "</td><td class=\"optionbox $styleadd\"><span class=\"field\">";
			if (showFactDetails("OBJE", $pid)) {
				if (preg_match("'://'", $thumbnail)||(preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($thumbnail)))) {
					print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode(check_media_depth($rowm["m_file"]))."',$imgwidth, $imgheight);\"><img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\" alt=\"\" /></a>";
				}
				print "<a href=\"medialist.php?action=filter&amp;search=yes&amp;filter=".rawurlencode($rowm["m_titl"])."&amp;ged=".$GEDCOM."\">";
				if ($TEXT_DIRECTION=="rtl" && !hasRTLText($rowm["m_titl"])) print "<i>&lrm;".PrintReady($rowm["m_titl"])."</i></a>";
				else print "<i>".PrintReady($rowm["m_titl"])."</i></a>";

				// NOTE: Print the format of the media
				if (!empty($rowm["m_ext"])) {
					print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">".$rowm["m_ext"]."</span>";
					if(! empty($imgsize[0]) &&  ! empty($imgsize[1])){
						print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?" &rlm;x&rlm; " : " x ") . $imgsize[1] . "</span>";
					}
				}
				$ttype = preg_match("/".($nlevel+1)." TYPE (.*)/", $rowm["m_gedrec"], $match);
				if ($ttype>0){
					print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["type"].": </span> <span class=\"field\">$match[1]</span>";
				}
				print "</span>";
				print "<br />\n";
				//-- print spouse name for marriage events
				$ct = preg_match("/PGV_SPOUSE: (.*)/", $factrec, $match);
				if ($ct>0) {
					$spouse=$match[1];
					if ($spouse!=="") {
						print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
						if (displayDetailsById($spouse)||showLivingNameById($spouse)) {
							print PrintReady(get_person_name($spouse));
						}
						else print $pgv_lang["private"];
						print "</a>";
					}
					if (($view != "preview") && ($spouse!=="")) print " - ";
					if ($view != "preview") {
						$ct = preg_match("/PGV_FAMILY_ID: (.*)/", $factrec, $match);
						if ($ct>0) {
							$famid = trim($match[1]);
							print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"];
							if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;";
							print "]</a>\n";
						}
					}
				}
				print "<br />\n";
				print_fact_notes($rowm["m_gedrec"], $nlevel+1);
				print_fact_sources($rowm["m_gedrec"], $nlevel+1);
			}
			print "</td></tr>";
		}
		$media_found = true;
	}
	if ($media_found) return true;
	else return false;



	// $ct = preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	// for($i=0; $i<$ct; $i++) {
		// $filename="";
		// $title="";
		// $spos1 = strpos($factrec, "$level OBJE".$omatch[$i][1]);
		// $spos2 = strpos($factrec, "\n$level", $spos1);
		// if (!$spos2) $spos2 = strlen($factrec);
		// $orec = substr($factrec, $spos1, $spos2-$spos1);
		// $objrec = $orec;
		// $nt = preg_match("/@(.*)@/", $omatch[$i][1], $nmatch);
		// if ($nt==0) {
			// $tt = preg_match("/$nlevel TITL (.*)/", $orec, $match);
			// if ($tt>0) {
				// $title = $match[1];
				// $title = trim($match[1]);
			// }
			// $fullpath = extract_fullpath($orec);
			// $filename = "";
			// $filename = extract_filename($fullpath);
			// $filename = $MEDIA_DIRECTORY.$filename;
			// $filename = trim($filename);
			// if (empty($title)) $title = $filename;
		// }
		// //-- look for a matching record
		// else {
			// // TODO: Change code to handle more than 1 media change
			// if (isset($pgv_changes[$nmatch[1]."_".$GEDCOM])) {
				// foreach ($pgv_changes[$nmatch[1]."_".$GEDCOM] as $key => $change) {
					// if (stristr($change["undo"], "0 @".$nmatch[1]."@"))
					// $objrec = $change["undo"];
				// }
			// }
		// }

	// } //-- end for loop
}
//-- Print the links to multi-media objects
function print_media_links($factrec, $level,$pid='') {
	 global $MULTI_MEDIA, $TEXT_DIRECTION, $TBLPREFIX, $GEDCOMS, $MEDIATYPE;
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $MEDIA_EXTERNAL;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;
	 global $GEDCOM, $SHOW_FAM_ID_NUMBERS;
	 if (!$MULTI_MEDIA) return;
	 $nlevel = $level+1;
	 if ($level==1) $size=50;
	 else $size=25;
	 $ct = preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if (count($omatch) > 0) {
		$media_id = preg_replace("/@/", "", trim($omatch[0][1]));
		$sql = "SELECT * FROM ".$TBLPREFIX."media where m_media = '".$media_id."' AND m_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);

		// NOTE: Determine the size of the mediafile
		$imgwidth = 300;
		$imgheight = 300;
		if (preg_match("'://'", $row["m_file"])) {
			if (in_array($row["m_ext"], $MEDIATYPE)){
				$imgwidth = 400;
				$imgheight = 500;
			}
			else {
				$imgwidth = 800;
				$imgheight = 400;
			}
		}
		else if ((preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($row["m_file"])))) {
			$imgsize = @getimagesize(filename_decode($row["m_file"]));
			if ($imgsize) {
				$imgwidth = $imgsize[0]+50;
				$imgheight = $imgsize[1]+50;
			}
		}
		$thumbnail = thumbnail_file($row["m_file"]);
		if (showFactDetails("OBJE", $pid)) {
			print "<div>";
			if (preg_match("'://'", $thumbnail)||(preg_match("'://'", $MEDIA_DIRECTORY)>0)||(file_exists(filename_decode($thumbnail)))) {
				print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($row["m_file"])."',$imgwidth, $imgheight);\"><img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\" alt=\"\" /></a>";
			}
			print "<a href=\"medialist.php?action=filter&amp;search=yes&amp;filter=".rawurlencode($row["m_titl"])."&amp;ged=".$GEDCOM."\">";
			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($row["m_titl"])) print "<i>&lrm;".PrintReady($row["m_titl"])."</i></a>";
			else print "<i>".PrintReady($row["m_titl"])."</i></a>";

			// NOTE: Print the format of the media
			if (!empty($row["m_ext"])) {
				print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">".$row["m_ext"]."</span>";
				if(! empty($imgsize[0]) &&  ! empty($imgsize[1])){
					print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?" &rlm;x&rlm; " : " x ") . $imgsize[1] . "</span>";
				}
			}
			$ttype = preg_match("/".($nlevel+1)." TYPE (.*)/", $row["m_gedrec"], $match);
			if ($ttype>0){
				print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["type"].": </span> <span class=\"field\">$match[1]</span>";
			}
			print "</span>";
			print "<br />\n";
			//-- print spouse name for marriage events
			$ct = preg_match("/PGV_SPOUSE: (.*)/", $factrec, $match);
			if ($ct>0) {
				$spouse=$match[1];
				if ($spouse!=="") {
					print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
					if (displayDetailsById($spouse)||showLivingNameById($spouse)) {
						print PrintReady(get_person_name($spouse));
					}
					else print $pgv_lang["private"];
					print "</a>";
				}
				if (($view != "preview") && ($spouse!=="")) print " - ";
				if ($view != "preview") {
					$ct = preg_match("/PGV_FAMILY_ID: (.*)/", $factrec, $match);
					if ($ct>0) {
						$famid = trim($match[1]);
						print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"];
						if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;";
						print "]</a>\n";
					}
				}
			}
			print "<br />\n";
			print_fact_notes($row["m_gedrec"], $nlevel);
			print_fact_sources($row["m_gedrec"], $nlevel);
			print "</div>";
		}
	 }
}
/**
 * print an address structure
 *
 * takes a gedcom ADDR structure and prints out a human readable version of it.
 * @param string $factrec	The ADDR subrecord
 * @param int $level		The gedcom line level of the main ADDR record
 */
function print_address_structure($factrec, $level) {
	 global $pgv_lang;
	 global $factarray;
	 global $WORD_WRAPPED_NOTES;
	 global $POSTAL_CODE;

	 //	 $POSTAL_CODE = 'false' - before city, 'true' - after city and/or state
	 //-- define per gedcom till can do per address countries in address languages
	 //-- then this will be the default when country not recognized or does not exist
	 //-- both Finland and Suomi are valid for Finland etc.
	 //-- see http://www.bitboost.com/ref/international-address-formats.html

	 $nlevel = $level+1;
	 $ct = preg_match_all("/$level ADDR(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 for($i=0; $i<$ct; $i++) {
 		  $arec = get_sub_record($level, "$level ADDR", $factrec, $i+1);
 		  if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["ADDR"].": </span><br /><div class=\"indent\">";
		  $cn = preg_match("/$nlevel _NAME (.*)/", $arec, $cmatch);
		  if ($cn>0) print str_replace("/", "", $cmatch[1])."<br />\n";
		  if (strlen(trim($omatch[$i][1])) > 0) print "<br />";
		  print PrintReady(trim($omatch[$i][1]));
		  $cont = get_cont($nlevel, $arec);
		  if (!empty($cont)) print PrintReady($cont);
		  else {
			  if (strlen(trim($omatch[$i][1])) > 0) print "<br />";
			  $cs = preg_match("/$nlevel ADR1 (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  if ($cn==0) {
					  print "<br />";
					  $cn=0;
				  }
				  print PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel ADR2 (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  if ($cn==0) {
					  print "<br />";
					  $cn=0;
				  }
				  print PrintReady($cmatch[1]);
			  }

			  if (!$POSTAL_CODE) {
			 	  $cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
				  if ($cs>0) {
				  	  print "<br />";
				  	  print PrintReady($cmatch[1]);
				  }
				  $cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  print " ".PrintReady($cmatch[1]);
				  }
				  $cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  print ", ".PrintReady($cmatch[1]);
				  }
			  }
			  else {
				  $cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  print "<br />";
					  print PrintReady($cmatch[1]);
				  }
				  $cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  print ", ".PrintReady($cmatch[1]);
				  }
 				  $cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
 				  if ($cs>0) {
 					  print " ".PrintReady($cmatch[1]);
 				  }
             }

             $cs = preg_match("/$nlevel CTRY (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  print "<br />";
				  print PrintReady($cmatch[1]);
			  }
		  }
		  if ($level>1) print "</div>\n";
	 }
	 $ct = preg_match_all("/$level PHON (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["PHON"].": </span><span class=\"field\">";
			   print "&lrm;".$omatch[$i][1]."&lrm;";
			   if ($level>1) print "</span>\n";
		  }
	 }
	 $ct = preg_match_all("/$level EMAIL (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["EMAIL"].": </span><span class=\"field\">";
			   print "<a href=\"mailto:".$omatch[$i][1]."\">".$omatch[$i][1]."</a>\n";
			   if ($level>1) print "</span>\n";
		  }
	 }
	 $ct = preg_match_all("/$level FAX (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["FAX"].": </span><span class=\"field\">";
 			   print "&lrm;".$omatch[$i][1]."&lrm;";
			   if ($level>1) print "</span>\n";
		  }
	 }
	 $ct = preg_match_all("/$level (WWW|URL) (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   if ($level>1) print "\n\t\t<span class=\"label\">".$factarray["URL"].": </span><span class=\"field\">";
			   print "<a href=\"".$omatch[$i][2]."\" target=\"_blank\">".$omatch[$i][2]."</a>\n";
			   if ($level>1) print "</span>\n";
		  }
	 }
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
	global $SHOW_CONTEXT_HELP, $pgv_lang,$view, $PGV_USE_HELPIMG, $PGV_IMAGES, $PGV_IMAGE_DIR;

	if ($PGV_USE_HELPIMG) $sentense = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["help"]["small"]."\" class=\"icon\" width=\"15\" height=\"15\" alt=\"\" />";
	else $sentense = $pgv_lang[$helpText];
	$output = "";
	if (($view!="preview")&&($_SESSION["show_context_help"])){
		if ($helpText=="qm_ah"){
			if (userIsAdmin(getUserName())){
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
	 global $pgv_lang, $factarray, $VERSION, $VERSION_RELEASE, $COMMON_NAMES_THRESHOLD;
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
		  else if ($noprint==0) $sentence = str_replace($match[$i][0], $match[$i][1].": ".$pgv_lang["var_not_exist"], $sentence);
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
/**
 * gets a menu with links to the gedcom portals
 *
 * This function will create the menu structure and print
 * the menu that will take the visitor to the gedcom portals
 * @author John Finlay
 */
function get_gedcom_menu() {
	 global $GEDCOMS, $GEDCOM, $pgv_lang, $ALLOW_CHANGE_GEDCOM;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;
	 $menu = array();
	 $menu["label"] = $pgv_lang["welcome_page"];
	 $menu["labelpos"] = "down";
	 $menu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["large"];
	 $menu["link"] = "index.php?command=gedcom";
	 $menu["accesskey"] = $pgv_lang["accesskey_home_page"];
	 $menu["class"] = "menuitem";
	 $menu["hoverclass"] = "menuitem_hover";
	 $menu["flyout"] = "down";
	 $menu["submenuclass"] = "submenu";
	 if ($ALLOW_CHANGE_GEDCOM && count($GEDCOMS)>1) {
		  $menu["items"] = array();
		  foreach($GEDCOMS as $ged=>$gedarray) {
			   $submenu = array();
			   $submenu["label"] = PrintReady($gedarray["title"]);
			   $submenu["labelpos"] = "right";
			   $submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"];
			   $submenu["link"] = "index.php?command=gedcom&amp;ged=$ged";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $menu["items"][] = $submenu;
		  }
	 }
	 return $menu;
}
/**
 * prints out a menu with links related to the user account
 *
 * This function will create the menu structure and print
 * the menu that will take the visitor to mygedview portal and other user account options
 */
function get_mygedview_submenu() {
	 global $GEDCOMS, $GEDCOM, $pgv_lang,$PGV_IMAGES;
	 global $PGV_IMAGE_DIR, $MEDIA_DIRECTORY, $MULTI_MEDIA;
	 $items = array();
	 $username = getUserName();
	 if (!empty($username)) {
		  $user = getUser($username);
		  $submenu = array();
		  $submenu["label"] = $pgv_lang["mgv"];
		  $submenu["labelpos"] = "right";
		if (isset($PGV_IMAGES["mygedview"]["small"]))
			$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["small"];
		  $submenu["link"] = "index.php?command=user";
		  $submenu["class"] = "submenuitem";
		  $submenu["hoverclass"] = "submenuitem_hover";
		  $items[] = $submenu;
		  if ($user["editaccount"]) {
			  $submenu = array();
			  $submenu["label"] = $pgv_lang["editowndata"];
			  $submenu["labelpos"] = "right";
			if (isset($PGV_IMAGES["mygedview"]["small"]))
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["small"];
			  $submenu["link"] = "edituser.php";
			  $submenu["class"] = "submenuitem";
			  $submenu["hoverclass"] = "submenuitem_hover";
			  $items[] = $submenu;
		  }
		  if (!empty($user["gedcomid"][$GEDCOM])) {
				$submenu = array();
				$submenu["label"] = $pgv_lang["quick_update_title"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"];
				$submenu["link"] = "#";
				$submenu["onclick"] = "return quickEdit('".$user["gedcomid"][$GEDCOM]."');";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["my_pedigree"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"];
				$submenu["link"] = "pedigree.php?rootid=".$user["gedcomid"][$GEDCOM];
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
				$submenu = array();
				$submenu["label"] = $pgv_lang["my_indi"];
				$submenu["labelpos"] = "right";
				$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"];
				$submenu["link"] = "individual.php?pid=".$user["gedcomid"][$GEDCOM];
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
		  }
		  if ((userIsAdmin($username)) || (userGedcomAdmin($username, $GEDCOM))){
			   $items[]="separator";
			   $submenu = array();
			   $submenu["label"] = $pgv_lang["admin"];
			   $submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["admin"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"];
			   $submenu["link"] = "admin.php";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $items[] = $submenu;
			   $submenu = array();
			   $submenu["label"] = $pgv_lang["manage_gedcoms"];
			   $submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["admin"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"];
			   $submenu["link"] = "editgedcoms.php";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $items[] = $submenu;
			   $submenu = array();
			   $submenu["label"] = $pgv_lang["user_admin"];
			   $submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["admin"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"];
			   $submenu["link"] = "useradmin.php";
			   $submenu["class"] = "submenuitem";
			   $submenu["hoverclass"] = "submenuitem_hover";
			   $items[] = $submenu;
			   if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
				$submenu = array();
				$submenu["label"] = $pgv_lang["upload_media"];
				$submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["media"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"];
				$submenu["link"] = "uploadmedia.php";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
			  }
		  }
		  else if (userCanEdit($username)) {
			  if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
				$items[]="separator";
				$submenu = array();
				$submenu["label"] = $pgv_lang["upload_media"];
				$submenu["labelpos"] = "right";
				if (isset($PGV_IMAGES["media"]["small"]))
					$submenu["icon"] = $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"];
				$submenu["link"] = "uploadmedia.php";
				$submenu["class"] = "submenuitem";
				$submenu["hoverclass"] = "submenuitem_hover";
				$items[] = $submenu;
			  }
		  }
	 }
	 return $items;
}
/**
 * get the reports submenu
 *
 */
function get_reports_submenu($class="submenuitem", $hoverclass="submenuitem_hover") {
	global $GEDCOMS, $GEDCOM, $pgv_lang, $PGV_IMAGES;
	global $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE, $PRIV_HIDE;
	global $PGV_IMAGE_DIR, $LANGUAGE;
	$reports = get_report_list();
	$items = array();
	$username = getUserName();
	foreach($reports as $file=>$report) {
		if (!isset($report["access"])) $report["access"] = $PRIV_PUBLIC;
		if ($report["access"]>=getUserAccessLevel($username)) {
			$submenu = array();
			if (!empty($report["title"][$LANGUAGE])) $submenu["label"] = $report["title"][$LANGUAGE];
			else $submenu["label"] = implode("", $report["title"]);
			$submenu["labelpos"] = "right";
			$submenu["link"] = "reportengine.php?action=setup&amp;report=".$report["file"];
			if (isset($PGV_IMAGES["reports"]["small"]) and isset($PGV_IMAGES[$report["icon"]]["small"])) $iconfile=$PGV_IMAGE_DIR."/".$PGV_IMAGES[$report["icon"]]["small"];
			if (isset($iconfile) && file_exists($iconfile)) $submenu["icon"] = $iconfile;
			$submenu["class"] = $class;
			$submenu["hoverclass"] = $hoverclass;
			$items[] = $submenu;
		}
	}
	return $items;
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
		  $themes = get_theme_names();
		  print "<div class=\"theme_form\">\n";
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
					}
					else {
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
	global $TEXT_DIRECTION, $SpecialChar, $SpecialPar, $query, $action, $firstname, $lastname, $place, $year, $DEBUG;
	// Check whether Search page highlighting should be done or not
	$HighlightOK = false;
	if (strstr($_SERVER["SCRIPT_NAME"], "search.php")) {	// If we're on the Search page
		if (!$InHeaders) {								//   and also in page body
//			if ((isset($query) and ($query != "")) || (isset($action) && ($action === "soundex"))) {		//   and the query isn't blank
			if ((isset($query) and ($query != "")) ) {		//   and the query isn't blank				$HighlightOK = true;					// It's OK to mark search result
			}
		}
	}
	$SpecialOpen = '(';
	$SpecialClose = array('(');
	//-- convert all & to &amp;
	$text = preg_replace("/&/", "&amp;", $text);
	//-- make sure we didn't double convert &amp; to &amp;amp;
	$text = preg_replace("/&amp;(\w+);/", "&$1;", $text);
    $text=trim($text);
    //-- if we are on the search page body, then highlight any search hits
    if ($HighlightOK) {
	    if (isset($query)) {
	    	$queries = preg_split("/\.\*/", $query);
	    	$newtext = $text;
	    	$hasallhits = true;
		    foreach($queries as $index=>$query1) {
			    if (preg_match("/(".$query1.")/i", $text)) {
		    		$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    		}
			else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
				$nlen = strlen($query1);
				$npos = strpos(str2upper($text), str2upper($query1));
	    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
	    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
    		}
	    		else $hasallhits = false;
	    	}
	    	if ($hasallhits) $text = $newtext;
    	}
    	if (isset($action) && ($action === "soundex")) {
	    	if (isset($firstname)) {
	    		$queries = preg_split("/\.\*/", $firstname);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
					else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
			    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
			    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
		    		}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    		if (isset($lastname)) {
	    		$queries = preg_split("/\.\*/", $lastname);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
					else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
			    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
			    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
		    		}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    		if (isset($place)) {
	    		$queries = preg_split("/\.\*/", $place);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
					else if (preg_match("/(".str2upper($query1).")/", str2upper($text))) {
						$nlen = strlen($query1);
						$npos = strpos(str2upper($text), str2upper($query1));
			    		$newtext = substr_replace($newtext, "</span>", $npos+$nlen, 0);
			    		$newtext = substr_replace($newtext, "<span class=\"search_hit\">", $npos, 0);
		    		}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    		if (isset($year)) {
	    		$queries = preg_split("/\.\*/", $year);
	    		$newtext = $text;
	    		$hasallhits = true;
		    	foreach($queries as $index=>$query1) {
			    	if (preg_match("/(".$query1.")/i", $text)) {
		    			$newtext = preg_replace("/(".$query1.")/i", "<span class=\"search_hit\">$1</span>", $newtext);
	    			}
	    			else $hasallhits = false;
	    		}
	    		if ($hasallhits) $text = $newtext;
    		}
    	}
    }
    if ($TEXT_DIRECTION=="ltr" && hasRTLText($text)) {
   		if (hasLTRText($text)) {
	   		// Text contains both RtL and LtR characters
	   		// return the parenthesis with surrounding &rlm; and the rest as is
	   		$printvalue = "";
	   		$first = 1;
	   		$linestart = 0;
	   		for ($i=0; $i<strlen($text); $i++) {
                $byte = substr($text,$i,1);
				if (substr($text,$i,6) == "<br />") $linestart = $i+6;
				if (in_array($byte,$SpecialPar)	||
                   (($i==strlen($text)-1 || substr($text,$i+1,6)=="<br />") && in_array($byte,$SpecialChar))) {
			   		if ($first==1) {
				   		if ($byte==")" && !in_array(substr($text,$i+1),$SpecialClose)) {
 			   		    	 $printvalue .= "&lrm;".$byte."&lrm;";
 			   			$linestart = $i+1;
 			   			}
 				   		else
				   		if (in_array($byte,$SpecialChar)) {                          //-- all special chars
				   		    if (hasRTLText(substr($text,$linestart,4)))
				   		    	 $printvalue .= "&rlm;".$byte."&rlm;";
				   			else $printvalue .= "&lrm;".$byte."&lrm;";
			   			}
				   		else {
				   		$first = 0;
				   			if (hasRTLText(substr($text,$i+1,4))) {
				   		     $printvalue .= "&rlm;";
				   			    $ltrflag = 0;
			   			     }
				   			else {
					   			$printvalue .= "&lrm;";
				   			    $ltrflag = 1;
				   			}
				   		$printvalue .= substr($text,$i,1);
			   		}
			   		}
			   		else {
				   		$first = 1;
				   		$printvalue .= substr($text,$i,1);
				   		if ($ltrflag)
				   		     $printvalue .= "&lrm;";
				   		else $printvalue .= "&rlm;";
			   		}
		   		}
		   			 else if (oneRTLText(substr($text,$i,2))) {
			   		    	$printvalue .= substr($text,$i,2);
			   		    	$i++;
		   		    	}
		   		    	else $printvalue .= substr($text,$i,1);
	   		}
			if (!$first)
				if ($ltrflag)
					 $printvalue .= "&lrm;";
				else $printvalue .= "&rlm;";
 			return $printvalue;
   		}
   		else return "&rlm;".$text."&rlm;";
	}
	else if ($TEXT_DIRECTION=="rtl" && hasLTRText($text)) {
//echo "<br />RL1 "; //----- MA @@@@
   		$printvalue = "";
   		$linestart = 0;
   		$first = 1;
   		for ($i=0; $i<strlen($text); $i++) {
            $byte = substr($text,$i,1);
            if (substr($text,$i,6) == "<br />") $linestart = $i+6;
			if (in_array($byte,$SpecialPar)	|| (($i==strlen($text)-1 || substr($text,$i+1,6)=="<br />") && in_array($byte,$SpecialChar))) {
		   		if ($first==1) {
			   		if ($byte==")" && !in_array(substr($text,$i+1),$SpecialClose)) {
		   		    	$printvalue .= "&rlm;".$byte."&rlm;";
		   				$linestart = $i+1;
		   			}
			   		else
			   		if (in_array($byte,$SpecialChar) && ($i==strlen($text)-1 || substr($text,$i+1,6)=="<br />")) {
			   		    if (hasRTLText(substr($text,$linestart,4)))
			   		    	 $printvalue .= "&rlm;".$byte."&rlm;";
			   			else $printvalue .= "&lrm;".$byte."&lrm;";
//echo "<br />RL2 "; //----- MA @@@@
		   			}
		   			else {
			   			$first = 0;
				   		if (hasRTLText(substr($text,$i+1,4))) {
			   		    	$printvalue .= "&rlm;";
			   			    $ltrflag = 0;
			   		     }
				   		else {
					   		$printvalue .= "&lrm;";
			   			    $ltrflag = 1;
//echo "<br />RL3 "; //----- MA @@@@
				   		}
			   			$printvalue .= substr($text,$i,1);
		   			}
		   		}
		   		else {
//echo "<br />RL4 "; //----- MA @@@@
			   		$first = 1;
			   		$printvalue .= substr($text,$i,1);
			   		if ($ltrflag)
			   		     $printvalue .= "&lrm;";
			   		else $printvalue .= "&rlm;";
		   		}
	   		}
	   		else {
		   		if (oneRTLText(substr($text,$i,2))) {
		   		    $printvalue .= substr($text,$i,2);
		   		    $i++;
	   		    }
	   		    else $printvalue .= substr($text,$i,1);
   		    }
   		}
	 	if (!$first) if ($ltrflag) $printvalue .= "&lrm;";
		             else $printvalue .= "&rlm;";
//echo "<br />RL5 "; //----- MA @@@@
		return $printvalue;
     }
	 else return $text;
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
 * @author opus27
 * @param string $pid		person or family ID
 * @param string $factrec	the raw gedcom record to print
 * @param string $linebr 	optional linebreak
 */
function print_asso_rela_record($pid, $factrec, $linebr=false) {
	global $GEDCOM, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS, $TEXT_DIRECTION, $pgv_lang, $factarray, $PGV_IMAGE_DIR, $PGV_IMAGES, $view;
	// get ASSOciate(s) ID(s)
	$ct = preg_match_all("/\d ASSO @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	for ($i=0; $i<$ct; $i++) {
		$level = substr($match[$i][0],0,1);
		$pid2 = $match[$i][1];
		// get RELAtionship field
		$assorec = get_sub_record($level, " ASSO ", $factrec, $i+1);
//		if (substr($_SERVER["SCRIPT_NAME"],1) == "pedigree.php") {
			$rct = preg_match("/\d RELA (.*)/", $assorec, $rmatch);
			if ($rct>0) {
				// RELAtionship name in user language
				$key = strtolower(trim($rmatch[1]));
				if (isset($pgv_lang["$key"])) $rela = $pgv_lang[$key];
				else $rela = $rmatch[1];
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
			if ($SHOW_ID_NUMBERS) print " <span dir=\"$TEXT_DIRECTION\">($pid2)</span>";
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
  			if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($pid2)&lrm;";
  			if ($TEXT_DIRECTION == "ltr") print "&lrm;]</a>\n"; else print "&rlm;]</a>\n";
		}
		else {
			print $pgv_lang["unknown"];
			if ($SHOW_ID_NUMBERS) print " <span dir=\"$TEXT_DIRECTION\">($pid2)</span>";
		}
		if ($linebr) print "<br />\n";
		print_fact_notes($assorec, $level+1);
		if (substr($_SERVER["SCRIPT_NAME"],1) == "pedigree.php") {
			print "<br />";
			print_fact_sources($assorec, $level+1);
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
				$age = convert_number(get_age(find_person_record($spouse), $bdate, false));
				if (10<$age and $age<80) print "<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["father"] . "\" alt=\"" . $pgv_lang["father"] . "\" class=\"sex_image\" />$age";
			}
			// mother
			$spouse = $parents["WIFE"];
			if ($spouse and showFact("BIRT", $spouse)) {
				$age = convert_number(get_age(find_person_record($spouse), $bdate, false));
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
	global $factarray, $pgv_lang;

	$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
	if ($ct>0) {
		print " ";
		// link to calendar
		if ($anchor) print get_date_url($match[1]);
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
				if (empty($deatrec)||(compare_facts($factrec, $deatrec)!=1)||(strstr($factrec, "1 DEAT"))) print get_age($indirec,$match[1]);
			}
		}
		print " ";
	}
	else {
		// 1 DEAT Y with no DATE => print YES
		// 1 DEAT N is not allowed
		// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
		$factdetail = preg_split("/ /",$factrec);
		if (isset($factdetail)) if (count($factdetail) == 3) if (strtoupper(trim($factdetail[2])) === "Y") print $pgv_lang["yes"];
	}
	// gedcom indi age
	$ages=array();
	$agerec = get_sub_record(2, "2 AGE", $factrec);
	$daterec = get_sub_record(2, "2 DATE", $factrec);
	if (empty($agerec)) $agerec = get_sub_record(3, "3 AGE", $daterec);
	$ages[0] = $agerec;
	// gedcom husband age
	$husbrec = get_sub_record(2, "2 HUSB", $factrec);
	if (!empty($husbrec)) $agerec = get_sub_record(3, "3 AGE", $husbrec);
	else $agerec = "";
	$ages[1] = $agerec;
	// gedcom wife age
	$wiferec = get_sub_record(2, "2 WIFE", $factrec);
	if (!empty($wiferec)) $agerec = get_sub_record(3, "3 AGE", $wiferec);
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
			$age = get_age_at_event(substr($agerec,5));
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
	global $SHOW_PEDIGREE_PLACES, $TEMPLE_CODES, $pgv_lang, $factarray;
	$out = false;
	$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
	if ($ct>0) {
		print " ";
		$levels = preg_split("/,/", $match[1]);
		if ($anchor) {
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
			print " -- ";
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
				print "<br />".$factarray["LATI"].": ".$match[1];
			}
			$map_long="";
			$cts = preg_match("/\d LONG (.*)/", $placerec, $match);
			if ($cts>0) {
				$map_long=$match[1];
				print " ".$factarray["LONG"].": ".$match[1];
			}
			if (!empty($map_lati) and !empty($map_long)) {
				print " <a target=\"_BLANK\" href=\"http://www.mapquest.com/maps/map.adp?searchtype=address&formtype=latlong&latlongtype=decimal&latitude=".$map_lati."&longitude=".$map_long."\"><img src=\"images/mapq.gif\" border=\"0\" alt=\"Mapquest &copy;\" title=\"Mapquest &copy;\" /></a>";
				print " <a target=\"_BLANK\" href=\"http://maps.google.com/maps?spn=.2,.2&ll=".$map_lati.",".$map_long."\"><img src=\"images/bubble.gif\" border=\"0\" alt=\"Google Maps &copy;\" title=\"Google Maps &copy;\" /></a>";
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
function print_first_major_fact($key) {
	global $pgv_lang, $factarray, $PGV_BASE_DIRECTORY, $factsfile, $LANGUAGE;
	$majorfacts = array("BIRT", "CHR", "BAPM", "DEAT", "BURI", "BAPL", "ADOP");
	// make sure factarray is loaded
	if (!isset($factarray)) {
		require($PGV_BASE_DIRECTORY.$factsfile["english"]);
		if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);
	}
	$indirec = find_person_record($key);
	foreach ($majorfacts as $indexval => $fact) {
		$factrec = get_sub_record(1, "1 $fact", $indirec);
		if (strlen($factrec)>7 and showFact("$fact", $key) and !FactViewRestricted($key, $factrec)) {
			print " -- <i>";
			if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
			else if (isset($factarray[$fact])) print $factarray[$fact];
			else print $fact;
			print " ";
			print_fact_date($factrec);
			print_fact_place($factrec);
			print "</i>";
			break;
		}
	}
	return $fact;
}

/**
 * Print a new fact box on details pages
 * @param string $id	the id of the person,family,source etc the fact will be added to
 * @param array $usedfacts	an array of facts already used in this record
 * @param string $type	the type of record INDI, FAM, SOUR etc
 */
function print_add_new_fact($id, $usedfacts, $type) {
	global $factarray, $pgv_lang;
	global $SOUR_FACTS_ADD, $SOUR_FACTS_UNIQUE, $INDI_FACTS_ADD, $INDI_FACTS_UNIQUE;
	global $FAM_FACTS_ADD, $FAM_FACTS_UNIQUE, $REPO_FACTS_ADD, $REPO_FACTS_UNIQUE;

	if ($type == "SOUR") $addfacts = array_merge(CheckFactUnique(preg_split("/[, ;:]+/", $SOUR_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY), $usedfacts, "SOUR"), preg_split("/[, ;:]+/", $SOUR_FACTS_ADD, -1, PREG_SPLIT_NO_EMPTY));
	else if ($type == "REPO") $addfacts = array_merge(CheckFactUnique(preg_split("/[, ;:]+/", $REPO_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY), $usedfacts, "REPO"), preg_split("/[, ;:]+/", $REPO_FACTS_ADD, -1, PREG_SPLIT_NO_EMPTY));
	else if ($type == "INDI") $addfacts = array_merge(CheckFactUnique(preg_split("/[, ;:]+/", $INDI_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY), $usedfacts, "INDI"), preg_split("/[, ;:]+/", $INDI_FACTS_ADD, -1, PREG_SPLIT_NO_EMPTY));
	else if($type == "FAM") $addfacts = array_merge(CheckFactUnique(preg_split("/[, ;:]+/", $FAM_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY), $usedfacts, "FAM"), preg_split("/[, ;:]+/", $FAM_FACTS_ADD, -1, PREG_SPLIT_NO_EMPTY));
	else return;

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
			if ($fact["type"]==$type) {
				print "<option value=\"clipboard_$key\">".$pgv_lang["add_from_clipboard"]." ".$factarray[$fact["fact"]]."</option>\n";
			}
		}
	}
	print "</select>";
	print "<input type=\"button\" value=\"".$pgv_lang["add"]."\" onclick=\"add_record('$id', 'newfact');\" />\n";

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
 */
function print_findindi_link($element_id, $indiname) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_id"];
	if (isset($PGV_IMAGES["indi"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indi"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findIndi(document.getElementById('".$element_id."'), '".$indiname."'); return false;\">";
	print $Link;
	print "</a>";
}

function print_findplace_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_place"];
	if (isset($PGV_IMAGES["place"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findPlace(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

function print_findfamily_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_family"];
	if (isset($PGV_IMAGES["family"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["family"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findFamily(document.getElementById('".$element_id."')); return false;\">";
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

function print_autopaste_link($element_id, $choices, $concat=1) {
	global $pgv_lang;

	print "<small>";
	foreach ($choices as $indexval => $choice) {
		print " &nbsp;<a href=\"javascript:;\" onclick=\"document.getElementById('".$element_id."').value ";
		if ($concat) print "+=' "; else print "='";
		print $choice."'; updatewholename(); return false;\">".$choice."</a>";
	}
	print "</small>";
}

function print_findsource_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_sourceid"];
	if (isset($PGV_IMAGES["source"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findSource(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

function print_addnewsource_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["create_source"];
	if (isset($PGV_IMAGES["addsource"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addsource"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"addnewsource(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

function print_findrepository_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_repository"];
	if (isset($PGV_IMAGES["repository"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["repository"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findRepository(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

function print_addnewrepository_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["create_repository"];
	if (isset($PGV_IMAGES["addrepository"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addrepository"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"addnewrepository(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

function print_findmedia_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["find_media"];
	if (isset($PGV_IMAGES["media"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print " <a href=\"javascript:;\" onclick=\"findMedia(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}
?>

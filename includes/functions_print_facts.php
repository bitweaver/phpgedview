<?php
/**
 * Function for printing facts
 *
 * Various printing functions used to print fact records
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	 print "Now, why would you want to do that. You're not hacking are you?";
	 exit;
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
	 global $SHOW_FACT_ICONS;
	 global $dHebrew;
	 global $n_chil, $n_gchi;
	 global $SEARCH_SPIDER;
	 $FACT_COUNT++;
	 $estimates = array("abt","aft","bef","est","cir");
	 $ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
	 if ($ft>0) {
		  $fact = trim($match[1]);
		  $event = trim($match[2]);
		  if ($event=="" and $fact=="TEXT") $event="\n";
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
	 if (($linenum<1) && (!empty($SEARCH_SPIDER)))  return; // don't add relatives for spiders.
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
	 	//-- check if this is a fact created by the research assistant and modify the 
	   	//---- edit links to forward editing to the RA plugin if it was created there
   		$et = preg_match("/\d _RATID (.*)/", $factrec, $ematch);
   		if ($et>0) {
   			$taskid = trim($ematch[1]);
   		} 
		  // -- handle generic facts
		  if ($fact!="EVEN" && $fact!="FACT" && $fact!="OBJE") {
			   $factref = $fact;
			   if (!showFact($factref, $pid)) return false;
			   if ($styleadd=="") $rowID = "row_".floor(microtime()*1000000);
			   else $rowID = "row_".$styleadd;
			   print "\n\t\t<tr id=\"".$rowID."\" name=\"".$rowID."\">";
			   print "\n\t\t\t<td class=\"descriptionbox $styleadd center width20\">";
			   if ($SHOW_FACT_ICONS && file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/".$factref.".gif\"  alt=\"".$factarray[$fact]."\" title=\"".$factarray[$fact]."\" align=\"middle\" /> ";
			   print $factarray[$fact];
			   if ($fact=="_BIRT_CHIL" and isset($n_chil)) print "<br />".$pgv_lang["number_sign"].$n_chil++;
			   if ($fact=="_BIRT_GCHI" and isset($n_gchi)) print "<br />".$pgv_lang["number_sign"].$n_gchi++;
			   if ((userCanEdit(getUserName()))&&($styleadd!="change_old")&&($linenum>0)&&($view!="preview")&&(!FactEditRestricted($pid, $factrec))) {
					$menu = array();
					$menu["label"] = $pgv_lang["edit"];
					$menu["labelpos"] = "right";
					$menu["icon"] = "";
					if (empty($taskid)) {
						$menu["onclick"] = "return edit_record('$pid', $linenum);";
						$menu["link"] = "#";
					}
					else {
						$menu['onclick'] = "";
						$menu["link"] = "module.php?mod=research_assistant&amp;action=editfact&amp;taskid=".$taskid;
					}
					$menu["class"] = "";
					$menu["hoverclass"] = "";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "submenu";
					$menu["items"] = array();
					$submenu = array();
					$submenu["label"] = $pgv_lang["edit"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					if (empty($taskid)) {
						$submenu["onclick"] = "return edit_record('$pid', $linenum);";
						$submenu["link"] = "#";
					}
					else {
						$submenu['onclick'] = "";
						$submenu["link"] = "module.php?mod=research_assistant&amp;action=editfact&amp;taskid=".$taskid;
					}
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
		  } else {
			   if ($fact == "OBJE") return false;
			   if (!showFact("EVEN", $pid)) return false;
			   // -- find generic type for each fact
			   $ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
			   if ($ct>0) $factref = trim($match[1]);
			   else $factref = $fact;
			   if (!showFact($factref, $pid)) return false;
			   if ($styleadd=="") $rowID = "row_".floor(microtime()*1000000);
			   else $rowID = "row_".$styleadd;
			   print "\n\t\t<tr id=\"".$rowID."\" name=\"".$rowID."\">";
			   if (isset($factarray["$factref"])) $label = $factarray[$factref];
			   else $label = $factref;
			   print "<td class=\"descriptionbox $styleadd center width20\">";
			   if ($SHOW_FACT_ICONS && file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/".$factref.".gif\" alt=\"".$label."\" title=\"".$label."\" align=\"middle\" /> ";
			   print $label;
			   if ((userCanEdit(getUserName()))&&($styleadd!="change_old")&&($linenum>0)&&($view!="preview")&&(!FactEditRestricted($pid, $factrec))) {
				   $menu = array();
					$menu["label"] = $pgv_lang["edit"];
					$menu["labelpos"] = "right";
					$menu["icon"] = "";
					$menu["link"] = "#";
					if (empty($taskid)) {
						$menu["onclick"] = "return edit_record('$pid', $linenum);";
						$menu["link"] = "#";
					}
					else {
						$menu['onclick'] = "";
						$menu["link"] = "module.php?mod=research_assistant&amp;action=editfact&amp;taskid=".$taskid;
					}
					$menu["class"] = "";
					$menu["hoverclass"] = "";
					$menu["flyout"] = "down";
					$menu["submenuclass"] = "submenu";
					$menu["items"] = array();
					$submenu = array();
					$submenu["label"] = $pgv_lang["edit"];
					$submenu["labelpos"] = "right";
					$submenu["icon"] = "";
					if (empty($taskid)) {
						$submenu["onclick"] = "return edit_record('$pid', $linenum);";
						$submenu["link"] = "#";
					}
					else {
						$submenu['onclick'] = "";
						$submenu["link"] = "module.php?mod=research_assistant&amp;action=editfact&amp;taskid=".$taskid;
					}
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
					print "<img src=\"image/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
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
					if ($view!="preview" &&(empty($SEARCH_SPIDER))) {
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
 					else if (strstr("FAX PHON ", $fact." ")) print "&lrm;".$event." &lrm;";
					else if (!strstr("ADDR ", $fact." ") && $event!="Y") print PrintReady($event." ");
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
				   if ($SHOW_FACT_ICONS && file_exists($PGV_IMAGE_DIR."/facts/CEME.gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/CEME.gif\" alt=\"".$factarray["CEME"]."\" title=\"".$factarray["CEME"]."\" align=\"middle\" /> ";
					print $factarray["CEME"].": ".$match[1]."<br />\n";
				}
			   //-- print address structure
			   if ($fact!="ADDR") {
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
				   print "<img src=\"image/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
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
						   if ($SHOW_FACT_ICONS && file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print "<img src=\"".$PGV_IMAGE_DIR."/facts/".$factref.".gif\" alt=\"".$label."\" title=\"".$label."\" align=\"middle\" /> ";
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
	 global $TEXT_DIRECTION, $pgv_lang;
	 if (displayDetailsById($sid, "REPO")) {
		 $source = find_repo_record($sid);
		 $ct = preg_match("/1 NAME (.*)/", $source, $match);
		 if ($ct > 0) {
			 $ct2 = preg_match("/0 @(.*)@/", $source, $rmatch);
			 if ($ct2>0) $rid = trim($rmatch[1]);
			 print "<span class=\"field\"><a href=\"repo.php?rid=$rid\"><b>".PrintReady($match[1])."</b>&nbsp;&nbsp;&nbsp;";
			 if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			 print "(".$sid.")";
			 if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			 print "</a></span><br />";
		 }
		 print_address_structure($source, 1);
		 print_fact_notes($source, 1);
	 }
}

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
	 global $factarray, $SEARCH_SPIDER;
	 global $WORD_WRAPPED_NOTES, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_SOURCES, $EXPAND_SOURCES;
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
	 	$sid = $match[$j][1];
	 	if (displayDetailsById($sid, "SOUR")) {
			$spos1 = strpos($factrec, "$level SOUR @".$sid."@", $spos2);
			$spos2 = strpos($factrec, "\n$level", $spos1);
			if (!$spos2) $spos2 = strlen($factrec);
			$srec = substr($factrec, $spos1, $spos2-$spos1);
			$lt = preg_match_all("/$nlevel \w+/", $srec, $matches);
			if ($j > 0) print "<br />";
			print "\n\t\t<span class=\"label\">";
			$elementID = $sid."-".floor(microtime()*1000000);
			if ($lt>0) print "<a href=\"javascript:;\" onclick=\"expand_layer('$elementID'); return false;\"><img id=\"{$elementID}_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".$pgv_lang["show_details"]."\" title=\"".$pgv_lang["show_details"]."\" /></a> ";
			print $pgv_lang["source"].":</span> <span class=\"field\">";
			if(empty($SEARCH_SPIDER)) {
				print "<a href=\"source.php?sid=".$sid."\">";
			}
			print PrintReady(get_source_descriptor($sid));
			//-- Print additional source title
	    	$add_descriptor = get_add_source_descriptor($sid);
	    	if ($add_descriptor) print " - ".PrintReady($add_descriptor);
			if(empty($SEARCH_SPIDER)) {
				print "</a>";
			}
			print "</span>";
			print "<div id=\"$elementID\" class=\"source_citations\">";
			$cs = preg_match("/$nlevel PAGE (.*)/", $srec, $cmatch);
			if ($cs>0) {
				print "\n\t\t\t<span class=\"label\">".$factarray["PAGE"].": </span><span class=\"field\">";
				$text = $cmatch[1];
				$text = preg_replace("'(https?://[\w\./\-&=?~%#]*)'i", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
				print PrintReady($text);
				$pagerec = get_sub_record($nlevel, $cmatch[0], $srec);
				$text = get_cont($nlevel+1, $pagerec);
				$text = preg_replace("'(https?://[\w\./\-&=\?~%#_\d;]*)'", "<a href=\"$1\" target=\"blank\">$1</a>", $text);
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
			if (function_exists('print_media_links')) print_media_links($srec, $nlevel);
			print_fact_notes($srec, $nlevel);
			print "</div>";
			print "</div>";
			if ($lt>0 and $EXPAND_SOURCES) {
				print "\r\n<script language='JavaScript' type='text/javascript'>\r\n";
				print "<!--\r\n";
				print "expand_layer('$elementID');\r\n";
				print "//-->\r\n";
				print "</script>\r\n";
			}
	 	}
	}
}

//-- Print the links to multi-media objects
function print_media_links($factrec, $level,$pid='') {
	 global $MULTI_MEDIA, $TEXT_DIRECTION, $TBLPREFIX, $GEDCOMS, $MEDIATYPE;
	 global $pgv_lang, $factarray, $SEARCH_SPIDER, $view;
	 global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $MEDIA_EXTERNAL, $THUMBNAIL_WIDTH;
	 global $PGV_IMAGE_DIR, $PGV_IMAGES;
	 global $GEDCOM, $SHOW_FAM_ID_NUMBERS;
	 if (!$MULTI_MEDIA) return;
	 $nlevel = $level+1;
	 if ($level==1) $size=50;
	 else $size=25;
	 if (preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER) == 0) return;
	 $objectNum = 0;
	 while ($objectNum < count($omatch)) {
		$media_id = preg_replace("/@/", "", trim($omatch[$objectNum][1]));
		if (displayDetailsById($media_id, "OBJE")) {
			$sql = "SELECT * FROM ".$TBLPREFIX."media where m_media = '".$media_id."' AND m_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
			$tempsql = dbquery($sql);
			$res =& $tempsql;
			$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);

			$mainMedia = check_media_depth($row["m_file"], "NOTRUNC");
			$thumbnail = thumbnail_file($mainMedia);
			$isExternal = stristr($row["m_file"],"://");
			$mediaTitle = $row["m_titl"];

			// Determine the size of the mediafile
			$imgsize = findImageSize($mainMedia);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			if (showFactDetails("OBJE", $pid)) {
				if ($objectNum > 0) print "<br clear=all />";
				print "<div>";
				if ($isExternal ||file_exists(filename_decode($thumbnail))) {
					print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($mainMedia)."',$imgwidth, $imgheight);\"><img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\"";
					if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
					print " alt=\"". PrintReady($mediaTitle) . "\" title=\"" . PrintReady($mediaTitle) . "\" /></a>";
				}
				if(empty($SEARCH_SPIDER)) {
					print "<a href=\"medialist.php?action=filter&amp;search=yes&amp;filter=". rawurlencode($mediaTitle) ."&amp;ged=".$GEDCOM."\">";
				}
				if ($TEXT_DIRECTION=="rtl" && !hasRTLText($mediaTitle)) print "<i>&lrm;".PrintReady($mediaTitle)."</i>";
				else print "<i>".PrintReady($mediaTitle)."</i>";
				if(empty($SEARCH_SPIDER)) {
					print "</a>";
				}

				// NOTE: Print the format of the media
				if (!empty($row["m_ext"])) {
					print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">".$row["m_ext"]."</span>";
					if(!empty($imgsize[0]) && !empty($imgsize[1])){
						print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?" &rlm;x&rlm; " : " x ") . $imgsize[1] . "</span>";
					}
				}
				$ttype = preg_match("/".($nlevel+1)." TYPE (.*)/", $row["m_gedrec"], $match);
				if ($ttype>0){
					$mediaType = $match[1];
					$varName = "TYPE__".strtolower($mediaType);
					if (isset($pgv_lang[$varName])) $mediaType = $pgv_lang[$varName];
					print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["type"].": </span> <span class=\"field\">$mediaType</span>";
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
					if (($view != "preview") && ($spouse!=="") && (empty($SEARCH_SPIDER))) print " - ";
					if ($view != "preview") {
						$ct = preg_match("/PGV_FAMILY_ID: (.*)/", $factrec, $match);
						if ($ct>0) {
							$famid = trim($match[1]);
							if(empty($SEARCH_SPIDER)) {
								print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"];
								if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;";
								print "]</a>\n";
							}
						}
					}
				}
				print "<br />\n";
				print_fact_notes($row["m_gedrec"], $nlevel);
				print_fact_sources($row["m_gedrec"], $nlevel);
				print "</div>";
			}
		}
		$objectNum ++;
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
 		  $resultText = "";
 		  if ($level>1) $resultText .= "\n\t\t<span class=\"label\">".$factarray["ADDR"].": </span><br /><div class=\"indent\">";
		  $cn = preg_match("/$nlevel _NAME (.*)/", $arec, $cmatch);
		  if ($cn>0) $resultText .= str_replace("/", "", $cmatch[1])."<br />\n";
		  $resultText .= PrintReady(trim($omatch[$i][1]));
		  $cont = get_cont($nlevel, $arec);
		  if (!empty($cont)) $resultText .= str_replace(array(" ", "<br&nbsp;"), array("&nbsp;", "<br "), PrintReady($cont));
		  else {
			  if (strlen(trim($omatch[$i][1])) > 0) print "<br />";
			  $cs = preg_match("/$nlevel ADR1 (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  if ($cn==0) {
					  $resultText .= "<br />";
					  $cn=0;
				  }
				  $resultText .= PrintReady($cmatch[1]);
			  }
			  $cs = preg_match("/$nlevel ADR2 (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  if ($cn==0) {
					  $resultText .= "<br />";
					  $cn=0;
				  }
				  $resultText .= PrintReady($cmatch[1]);
			  }

			  if (!$POSTAL_CODE) {
			 	  $cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
				  if ($cs>0) {
				  	  $resultText .= "<br />".PrintReady($cmatch[1]);
				  }
				  $cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  $resultText .= " ".PrintReady($cmatch[1]);
				  }
				  $cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  $resultText .= ", ".PrintReady($cmatch[1]);
				  }
			  }
			  else {
				  $cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  $resultText .= "<br />".PrintReady($cmatch[1]);
				  }
				  $cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
				  if ($cs>0) {
					  $resultText .= ", ".PrintReady($cmatch[1]);
				  }
 				  $cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
 				  if ($cs>0) {
 					  $resultText .= " ".PrintReady($cmatch[1]);
 				  }
             }

             $cs = preg_match("/$nlevel CTRY (.*)/", $arec, $cmatch);
			  if ($cs>0) {
				  $resultText .= "<br />".PrintReady($cmatch[1]);
			  }
		  }
		  if ($level>1) $resultText .= "</div>\n";
		  $resultText .= "<br />";
		  // Here we can examine the resultant text and remove empty tags
		  print $resultText;
	 }
	 $resultText = "";
	 $resultText .= "<table>";
	 $ct = preg_match_all("/$level PHON (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   $resultText .= "<tr>";
			   $resultText .= "\n\t\t<td><span class=\"label\"><b>".$factarray["PHON"].": </b></span></td><td><span class=\"field\">";
			   $resultText .= "&lrm;".$omatch[$i][1]."&lrm;";
			   $resultText .= "</span></td></tr>\n";
		  }
	 }
	 $ct = preg_match_all("/$level FAX (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   $resultText .= "<tr>";
			   $resultText .= "\n\t\t<td><span class=\"label\"><b>".$factarray["FAX"].": </b></span></td><td><span class=\"field\">";
 			   $resultText .= "&lrm;".$omatch[$i][1]."&lrm;";
			   $resultText .= "</span></td></tr>\n";
		  }
	 }
	 $ct = preg_match_all("/$level EMAIL (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   $resultText .= "<tr>";
			   $resultText .= "\n\t\t<td><span class=\"label\"><b>".$factarray["EMAIL"].": </b></span></td><td><span class=\"field\">";
			   $resultText .= "<a href=\"mailto:".$omatch[$i][1]."\">".$omatch[$i][1]."</a>\n";
			   $resultText .= "</span></td></tr>\n";
		  }
	 }
	 $ct = preg_match_all("/$level (WWW|URL) (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	 if ($ct>0) {
		  for($i=0; $i<$ct; $i++) {
			   $resultText .= "<tr>";
			   $resultText .= "\n\t\t<td><span class=\"label\"><b>".$factarray["URL"].": </b></span></td><td><span class=\"field\">";
			   $resultText .= "<a href=\"".$omatch[$i][2]."\" target=\"_blank\">".$omatch[$i][2]."</a>\n";
			   $resultText .= "</span></td></tr>\n";
		  }
	 }
	 $resultText .= "</table>";
	 if ($resultText!="<table></table>") print $resultText;
}

function print_main_sources($factrec, $level, $pid, $linenum) {
	 global $pgv_lang;
	 global $factarray, $view, $SEARCH_SPIDER;
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
		  print "\n\t\t\t<tr><td class=\"descriptionbox $styleadd center width20\">";
		  //print "\n\t\t\t<tr><td class=\"facts_label$styleadd\">";
		  print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" alt=\"\" /><br />";
		  print $factarray["SOUR"];
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
		  print "\n\t\t\t<td class=\"optionbox $styleadd wrap\">";
		  //print "\n\t\t\t<td class=\"facts_value$styleadd\">";
		  if (showFactDetails("SOUR", $pid)) {
			   $source = find_source_record($match[$j][1]);
			   if(empty($SEARCH_SPIDER)) {
				   echo "<a href=\"source.php?sid=".$match[$j][1]."\">";
			   }
    		   $text = PrintReady(get_source_descriptor($match[$j][1]));
    		   //-- Print additional source title
    		   $add_descriptor = get_add_source_descriptor($match[$j][1]);
    		   if ($add_descriptor) $text .= " - ".PrintReady($add_descriptor);
				if (strpos($source, " _ITALIC")) echo "<i>".$text."</i>"; else echo $text;
			   if(empty($SEARCH_SPIDER)) {
			        echo "</a>";
			   }
			   // PUBL
				$cs = preg_match("/1 PUBL (.*)/", $source, $cmatch);
				if ($cs>0) {
					echo "<br />";
					$text = $cmatch[1];
					if (strpos($source, " _PAREN")) echo "(".$text.")"; else echo $text;
				}
			   // See if RESN tag prevents display or edit/delete
	 			$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $rmatch);
	 			if ($resn_tag > 0) $resn_value = strtolower(trim($rmatch[1]));
			    // -- Find RESN tag
			   if (isset($resn_value)) {
				   print "<img src=\"image/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
			   }
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
							  print "<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".$pgv_lang["text"]." </span>\n";
							  print "<span class=\"field\">".$tmatch[$k][1];
							  print get_cont($nlevel+2, $srec);
							  print "</span>";
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
		  print "\n\t\t<tr><td valign=\"top\" class=\"descriptionbox $styleadd center width20\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["notes"]["small"]."\" alt=\"\" /><br />".$factarray["NOTE"];
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
				   print "<br /><img src=\"image/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
			   }
			   print "<br />\n";
			   print_fact_sources($nrec, $nlevel);
		  }
		  print "</td></tr>";
	 }
}

/**
 * Print the links to multi-media objects
 * @param string $pid	The the xref id of the object to find media records related to
 * @param int $level	The level of media object to find
 * @param boolean $related	Whether or not to grab media from related records
 */
function print_main_media($pid, $level=1, $related=false) {
	global $MULTI_MEDIA, $TBLPREFIX, $SHOW_ID_NUMBERS, $SHOW_FAM_ID_NUMBERS, $MEDIA_EXTERNAL;
	global $pgv_lang, $pgv_changes, $factarray, $view;
	global $GEDCOMS, $GEDCOM, $MEDIATYPE, $pgv_changes, $DBCONN, $DBTYPE;
	global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION;

	if (!showFact("OBJE", $pid)) return false;
	if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
	else $gedrec = find_record_in_file($pid);
	$ids = array($pid);
	//-- find all of the related ids
	if ($related) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$ids[] = trim($match[$i][1]);
		}
	}

	//-- get a list of the current objects in the record
	$current_objes = array();
	if ($level>0) $regexp = "/".$level." OBJE @(.*)@/";
	else $regexp = "/OBJE @(.*)@/";
	$ct = preg_match_all($regexp, $gedrec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$current_objes[$match[$i][1]] = true;
	}

	$media_found = false;
	$sqlmm = "SELECT ";
	$sqlmm .= "m_media, m_ext, m_file, m_titl, m_gedfile, m_gedrec, mm_gid, mm_gedrec FROM ".$TBLPREFIX."media, ".$TBLPREFIX."media_mapping where ";
	$sqlmm .= "mm_gid IN (";
	$i=0;
	foreach($ids as $key=>$id) {
		if ($i>0) $sqlmm .= ",";
		$sqlmm .= "'".$DBCONN->escape($id)."'";
		$i++;
	}
	$sqlmm .= ") AND mm_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."' AND mm_media=m_media AND mm_gedfile=m_gedfile ";
	//-- for family and source page only show level 1 obje references
	if ($level>0) $sqlmm .= "AND mm_gedrec LIKE '$level OBJE%'";

	$sqlmm .= "ORDER BY mm_id ASC";
	$resmm = dbquery($sqlmm);
	while($rowm = $resmm->fetchRow(DB_FETCHMODE_ASSOC)){
		// NOTE: Determine the size of the mediafile
		$imgwidth = 300+40;
		$imgheight = 300+150;
		if (preg_match("'://'", $rowm["m_file"])) {
			if (in_array($rowm["m_ext"], $MEDIATYPE)){
				$imgwidth = 400+40;
				$imgheight = 500+150;
			}
			else {
				$imgwidth = 800+40;
				$imgheight = 400+150;
			}
		}
		else if (file_exists(filename_decode(check_media_depth($rowm["m_file"], "NOTRUNC")))) {
			$imgsize = findImageSize(check_media_depth($rowm["m_file"], "NOTRUNC"));
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
		}
		$rows=array();
		//-- if there is a change to this media item then get the
		//-- updated media item and show it
		if (isset($pgv_changes[$rowm["m_media"]."_".$GEDCOM])) {
			$newrec = find_record_in_file($rowm["m_media"]);
			$row = array();
			$row['m_media'] = $rowm["m_media"];
			$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
			$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
			if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
			$row['m_gedrec'] = $newrec;
			$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
			$ext = "";
			if ($et>0) $ext = substr(trim($ematch[1]),1);
			$row['m_ext'] = $ext;
			$row['mm_gid'] = $pid;
			$rows['new'] = $row;
			$rows['old'] = $rowm;
			unset($current_objes[$rowm['m_media']]);
		}
		else {
			if (!isset($current_objes[$rowm['m_media']]) && ($rowm['mm_gid']==$pid)) $rows['old'] = $rowm;
			else {
				$rows['normal'] = $rowm;
				if (isset($current_objes[$rowm['m_media']])) unset($current_objes[$rowm['m_media']]);
			}
		}
		foreach($rows as $rtype => $rowm) {
			$res = print_main_media_row($rtype, $rowm, $pid);
			$media_found = $media_found || $res;
		}
		//$media_found = true;
	}

	//-- objects are removed from the $current_objes list as they are printed
	//-- any objects left in the list are new objects recently added to the gedcom
	//-- but not yet accepted into the database.  We will print them too.
	foreach($current_objes as $media_id=>$value) {
		//-- check if we need to get the object from a remote location
		$ct = preg_match("/(.*):(.*)/", $media_id, $match);
		if ($ct>0) {
			$client = ServiceClient::getInstance($match[1]);
			if (!is_null($client)) {
				$newrec = $client->getRemoteRecord($match[2]);
				$row['m_media'] = $media_id;
				$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
				$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
				if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
				$row['m_gedrec'] = $newrec;
				$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
				$ext = "";
				if ($et>0) $ext = substr(trim($ematch[1]),1);
				$row['m_ext'] = $ext;
				$row['mm_gid'] = $pid;
				$res = print_main_media_row('normal', $row, $pid);
				$media_found = $media_found || $res;
			}
		}
		else {
			$row = array();
			$newrec = find_record_in_file($media_id);
			$row['m_media'] = $media_id;
			$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
			$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
			if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
			$row['m_gedrec'] = $newrec;
			$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
			$ext = "";
			if ($et>0) $ext = substr(trim($ematch[1]),1);
			$row['m_ext'] = $ext;
			$row['mm_gid'] = $pid;
			$res = print_main_media_row('new', $row, $pid);
			$media_found = $media_found || $res;
		}
	}
	if ($media_found) return true;
	else return false;
}

/**
 * print a media row in a table
 * @param string $rtype whether this is a 'new', 'old', or 'normal' media row... this is used to determine if the rows should be printed with an outline color
 * @param array $rowm	An array with the details about this media item
 * @param string $pid	The record id this media item was attached to
 */
function print_main_media_row($rtype, $rowm, $pid) {
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $view, $MEDIA_DIRECTORY, $TEXT_DIRECTION;
	global $SHOW_FAM_ID_NUMBERS, $GEDCOM, $factarray, $pgv_lang, $THUMBNAIL_WIDTH;
	global $SEARCH_SPIDER;

	//print $rtype." ".$rowm["m_media"]." ".$pid;
	if (!displayDetailsById($rowm['m_media'], 'OBJE') || FactViewRestricted($rowm['m_media'], $rowm['m_gedrec'])) {
		//print $rowm['m_media']." no privacy ";
		return false;
	}

	$styleadd="";
	if ($rtype=='new') $styleadd = "change_new";
	if ($rtype=='old') $styleadd = "change_old";
	// NOTEStart printing the media details
	$thumbnail = thumbnail_file($rowm["m_file"]);
	$isExternal = stristr($thumbnail,"://");

	$linenum = 0;
	print "\n\t\t<tr><td class=\"descriptionbox $styleadd center width20\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"]."\" alt=\"\" /><br />".$factarray["OBJE"];
	if ($rowm['mm_gid']==$pid && userCanEdit(getUserName()) && (!FactEditRestricted($rowm['m_media'], $rowm['m_gedrec'])) && ($styleadd!="change_old") && ($view!="preview")) {
		$encodedFileName = rawurlencode($rowm["m_file"]);
		$menu = array();
		$menu["label"] = $pgv_lang["edit"];
		$menu["labelpos"] = "right";
		$menu["icon"] = "";
		$menu["link"] = "#";
		// $menu["onclick"] = "return edit_record('$pid', $linenum);";
		$menu["onclick"] = "return window.open('addmedia.php?action=editmedia&amp;pid=".$rowm["m_media"]."&amp;filename=".$encodedFileName."&amp;linktoid=".$rowm["mm_gid"]."', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');";
		$menu["class"] = "";
		$menu["hoverclass"] = "";
		$menu["flyout"] = "down";
		$menu["submenuclass"] = "submenu";
		$menu["items"] = array();
		$submenu = array();
		$submenu["label"] = $pgv_lang["edit"];
		$submenu["labelpos"] = "right";
		$submenu["icon"] = "";
		$submenu["onclick"] = "return window.open('addmedia.php?action=editmedia&amp;pid=".$rowm["m_media"]."&amp;filename=".$encodedFileName."&amp;linktoid=".$rowm["mm_gid"]."', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');";
		$submenu["link"] = "#";
		$submenu["class"] = "submenuitem";
		$submenu["hoverclass"] = "submenuitem_hover";
		$menu["items"][] = $submenu;
		$submenu = array();
		$submenu["label"] = $pgv_lang["delete"];
		$submenu["labelpos"] = "right";
		$submenu["icon"] = "";
		$submenu["onclick"] = "return delete_record('$pid', 'OBJE', '".$rowm['m_media']."');";
		//$submenu["onclick"] = "return window.open('addmedia.php?action=delete&amp;pid=".$rowm["m_media"]."', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');";
		$submenu["link"] = "#";
		$submenu["class"] = "submenuitem";
		$submenu["hoverclass"] = "submenuitem_hover";
		$menu["items"][] = $submenu;
		$submenu = array();
		$submenu["label"] = $pgv_lang["copy"];
		$submenu["labelpos"] = "right";
		$submenu["icon"] = "";
		$submenu["onclick"] = "return copy_record('".$rowm['m_media']."', 'media');";
		$submenu["link"] = "#";
		$submenu["class"] = "submenuitem";
		$submenu["hoverclass"] = "submenuitem_hover";
		$menu["items"][] = $submenu;
		print " <div style=\"width:25px;\">";
		print_menu($menu);
		print "</div>";
	}
	// NOTE Print the title of the media
	print "</td><td class=\"optionbox wrap $styleadd\"><span class=\"field\">";
	if (showFactDetails("OBJE", $pid)) {
		$mediaTitle = $rowm["m_titl"];
		if ($mediaTitle=="") $mediaTitle = basename($rowm["m_file"]);
		if ($isExternal || file_exists(filename_decode($thumbnail))) {
			$mainFileExists = false;
			if ($isExternal || file_exists(check_media_depth($rowm["m_file"], "NOTRUNC"))) {
				$mainFileExists = true;
				$imgsize = findImageSize(check_media_depth($rowm["m_file"], "NOTRUNC"));
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;
				print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode(check_media_depth($rowm["m_file"], "NOTRUNC"))."',$imgwidth, $imgheight);\">";
			}
			print "<img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\"";
			if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
			print " alt=\"" . PrintReady($mediaTitle) . "\" title=\"" . PrintReady($mediaTitle) . "\" />";
			if ($mainFileExists) print "</a>";
		}
		if(empty($SEARCH_SPIDER)) {
			print "<a href=\"medialist.php?action=filter&amp;search=yes&amp;filter=".rawurlencode($mediaTitle)."&amp;ged=".$GEDCOM."\">";
		}
		if ($TEXT_DIRECTION=="rtl" && !hasRTLText($mediaTitle)) print "<i>&lrm;".PrintReady($mediaTitle)."</i>";
		else print "<i>".PrintReady($mediaTitle)."</i>";
		if(empty($SEARCH_SPIDER)) {
			print "</a>";
		}

		// NOTE: Print the format of the media
		if (!empty($rowm["m_ext"])) {
			print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">".$rowm["m_ext"]."</span>";
			if(! empty($imgsize[0]) &&  ! empty($imgsize[1])){
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?" &rlm;x&rlm; " : " x ") . $imgsize[1] . "</span>";
			}
		}
		$ttype = preg_match("/\d TYPE (.*)/", $rowm["m_gedrec"], $match);
		if ($ttype>0){
			$mediaType = $match[1];
			$varName = "TYPE__".strtolower($mediaType);
			if (isset($pgv_lang[$varName])) $mediaType = $pgv_lang[$varName];
			print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["type"].": </span> <span class=\"field\">$mediaType</span>";
		}
		print "</span>";
		print "<br />\n";
		//-- print spouse name for marriage events
		if ($rowm['mm_gid']!=$pid) {
			$parents = find_parents($rowm['mm_gid']);
			if ($parents) {
				if (!empty($parents['HUSB']) && $parents['HUSB']!=$pid) $spouse = $parents['HUSB'];
				if (!empty($parents['WIFE']) && $parents['WIFE']!=$pid) $spouse = $parents['WIFE'];
			}
			if (!empty($spouse)) {
				print "<a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
				if (displayDetailsById($spouse)||showLivingNameById($spouse)) {
					print PrintReady(get_person_name($spouse));
				}
				else print $pgv_lang["private"];
				print "</a>";
			}
			if(empty($SEARCH_SPIDER)) {
				if (($view != "preview") && (!empty($spouse))) print " - ";
				if ($view != "preview") {
						$famid = $rowm['mm_gid'];
						print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"];
						if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;";
						print "]</a>\n";
				}
			}
		}
		print "<br />\n";
		print_fact_sources($rowm["m_gedrec"], 1);
		print_fact_notes($rowm["m_gedrec"], 1);
	}
	print "</td></tr>";
	return true;
}
?>

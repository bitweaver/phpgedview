<?php
/**
 * Function for printing facts
 *
 * Various printing functions used to print fact records
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team
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

require_once 'includes/person_class.php';

/**
 * Turn URLs in text into HTML links.  Insert breaks into long URLs
 * so that the browser can word-wrap.
 *
 * @param string $text	Text that may or may not contain URLs
 * @return string	The text with URLs replaced by HTML links
 */
function expand_urls($text) {
	// Some versions of RFC3987 have an appendix B which gives the following regex
	// (([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
	// This matches far too much while a "precise" regex is several pages long.
	// This is a compromise.
	$URL_REGEX='((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#(\S*))?';

	return preg_replace_callback(
		'/'.addcslashes("(?!>)$URL_REGEX(?!</a>)", '/').'/i',
		create_function( // Insert <wbr/> codes into the replaced string
			'$m',
			'return "<a href=\"".$m[0]."\" target=\"blank\">".preg_replace("/\b/", "<wbr/>", $m[0])."</a>";'
		),
		preg_replace("/<(?!br)/i", "&lt;", $text) // no html except br
	);
}

/**
 * print a fact record
 *
 * prints a fact record designed for the personal facts and details page
 * @param string $factrec	The gedcom subrecord
 * @param string $pid		The Gedcom Xref ID of the person the fact belongs to (required to check fact privacy)
 * @param int $linenum		The line number where this fact started in the original gedcom record (required for editing)
 * @param string $indirec	optional INDI record for age calculation at family event
 * @param boolean $noedit	Hide or show edit links
 */
function print_fact($factrec, $pid, $linenum, $indirec=false, $noedit=false) {
	global $factarray;
	global $nonfacts;
	global $PGV_IMAGE_DIR;
	global $pgv_lang, $GEDCOM;
	global $WORD_WRAPPED_NOTES;
	global $TEXT_DIRECTION, $USE_RTL_FUNCTIONS;
	global $HIDE_GEDCOM_ERRORS, $SHOW_ID_NUMBERS;
	global $CONTACT_EMAIL, $view, $FACT_COUNT;
	global $SHOW_FACT_ICONS;
	global $n_chil, $n_gchi, $n_ggch;
	global $SEARCH_SPIDER;
	
	//-- keep the time of this access to help with concurrent edits
	$_SESSION['last_access_time'] = time();

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

	if ($fact=="NOTE") return print_main_notes($factrec, 1, $pid, $linenum, $noedit);
	if ($fact=="SOUR") return print_main_sources($factrec, 1, $pid, $linenum, $noedit);

	$styleadd="";
	$ct = preg_match("/PGV_NEW/", $factrec, $match);
	if ($ct>0) $styleadd="change_new";
	$ct = preg_match("/PGV_OLD/", $factrec, $match);
	if ($ct>0) $styleadd="change_old";
	if (($linenum<1) && (!empty($SEARCH_SPIDER)))  return; // don't add relatives for spiders.
	if ($linenum<1) $styleadd="rela"; // not editable
	if ($linenum==-1) $styleadd="histo"; // historical facts
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
			print "\n\t\t<tr class=\"".$rowID."\">";
			print "\n\t\t\t<td class=\"descriptionbox $styleadd center width20\">";
			$label = $factref;
			if (isset($factarray["$factref"])) $label = $factarray[$factref];
			if (isset($pgv_lang[$factref])) $label = $pgv_lang[$factref];
			print_fact_icon($fact, $factrec, $label, $pid);
			print $factarray[$fact];
			if ($fact=="_BIRT_CHIL" and isset($n_chil)) print "<br />".$pgv_lang["number_sign"].$n_chil++;
			if ($fact=="_BIRT_GCHI" and isset($n_gchi)) print "<br />".$pgv_lang["number_sign"].$n_gchi++;
			if ($fact=="_BIRT_GGCH" and isset($n_ggch)) print "<br />".$pgv_lang["number_sign"].$n_ggch++;
			if (!$noedit && PGV_USER_CAN_EDIT && $styleadd!="change_old" && $linenum>0 && $view!="preview" && !FactEditRestricted($pid, $factrec)) {
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
			print "\n\t\t<tr class=\"".$rowID."\">";
			$label = $factref;
			if (isset($factarray["$factref"])) $label = $factarray[$factref];
			if (isset($pgv_lang[$factref])) $label = $pgv_lang[$factref];
			print "<td class=\"descriptionbox $styleadd center width20\">";
			print_fact_icon($fact, $factrec, $label, $pid);
			print $label;
			if (!$noedit && PGV_USER_CAN_EDIT && $styleadd!="change_old" && $linenum>0 && $view!="preview" && !FactEditRestricted($pid, $factrec)) {
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
		$align = "";
/*	Did not look good
		$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
		if (!empty($event) && $ct==0) {
			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($event) && hasLTRText($event) && $event!="N" && $event!="Y") $align=" align=\"left\"";
			if ($TEXT_DIRECTION=="ltr" && $USE_RTL_FUNCTIONS && !hasLTRText($event) && hasRTLText($event)) $align=" align=\"right\"";
		}
*/
		print "<td class=\"optionbox $styleadd wrap\" $align>";
		//print "<td class=\"facts_value facts_value$styleadd\">";
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
			echo format_fact_date($factrec, true, true, $fact, $pid, $indirec);
			//-- print spouse name for marriage events
			$ct = preg_match("/_PGVS @(.*)@/", $factrec, $match);
			if ($ct>0) {
				$spouse=$match[1];
				if ($spouse!=="") {
 					print " <a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
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
					if ($TEXT_DIRECTION == "ltr") print " " . getLRM();
					else print " " . getRLM();
					print "[".$pgv_lang["view_family"];
					if ($SHOW_ID_NUMBERS) print " " . getLRM() . "($pid)" . getLRM();
 					if ($TEXT_DIRECTION == "ltr") print getLRM() . "]</a>\n";
 					else print getRLM() . "]</a>\n";
				}
			}
			//-- print other characterizing fact information
			if ($event!="" and $fact!="ASSO") {
				print " ";
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
				/* -- see the format_fact_date function where this is handled
				else if ($event=="Y") {
					if (get_sub_record(2, "2 DATE", $factrec)=="") {
						print $pgv_lang["yes"]."<br />";
					}
				}*/
				else if ($event=="N") {
					if (get_sub_record(2, "2 DATE", $factrec)=="") {
						print $pgv_lang["no"];
					}
				}
				else if (strstr("URL WWW ", $fact." ")) {
					print "<a href=\"".$event."\" target=\"new\">".PrintReady($event)."</a>";
				}
				else if (strstr("_EMAIL", $fact)) {
					print "<a href=\"mailto:".$event."\">".$event."</a>";
				}
				else if (strstr("FAX PHON FILE", $fact." ")) print getLRM(). $event." " . getLRM();
				else if (!strstr("ADDR _RATID ", $fact." ") && $event!="Y") print PrintReady($event." ");
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
				echo format_fact_place($factrec, true, true, true);
				if (preg_match("/ (PLAC)|(STAT)|(TEMP)|(SOUR) /", $factrec)>0 || (!empty($event)&&$fact!="ADDR")) print "<br />\n";
				// -- print BURIal -> CEMEtery
				$ct = preg_match("/2 CEME (.*)/", $factrec, $match);
				if ($ct>0) {
					print_fact_icon("CEME", $factrec, $factarray["CEME"], $pid);
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
			print_asso_rela_record($pid, $factrec, true, id_type($pid));
			// -- find _PGVU field
			$ct = preg_match("/2 _PGVU (.*)/", $factrec, $match);
//			if ($ct>0) print $factarray["_PGVU"].": ".$match[1];			
			if ($ct>0) print " - ".$factarray["_PGVU"].": ".$match[1];
			// -- Find RESN tag
			if (isset($resn_value)) {
				print "<img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
				print_help_link("RESN_help", "qm");
			}
			if (preg_match("/[\r\n]2 FAMC @(.+)@/", $factrec, $match)) {
				print "<br/><span class=\"label\">".$factarray["FAMC"].":</span> ";
				print "<a href=\"family.php?famid=".$match[1]."&amp;ged=$GEDCOM\">";
				print get_family_descriptor($match[1]);
				print "</a>";
				if (preg_match("/[\r\n]3 ADOP (HUSB|WIFE|BOTH)/", str2upper($factrec), $match)) {
					print '<br/><span class="indent"><span class="label">'.$factarray['ADOP'].':</span> ';
					print '<span class="field">';
					switch ($match[1]) {
					case 'HUSB':
					case 'WIFE':
						print $factarray[$match[1]];
						break;
					case 'BOTH':
						print $factarray['HUSB'].'+'.$factarray['WIFE'];
						break;
					}
					print '</span></span>';
				}
			}
			// 0 SOUR/1 DATA/2 EVEN/3 DATE/3 PLAC
			for ($even_num=1; $even_rec=get_sub_record(2, "2 EVEN", $factrec, $even_num); ++$even_num) {
				$tmp1=get_gedcom_value('EVEN', 2, $even_rec, $truncate='', $convert=false);
				$tmp2=new GedcomDate(get_gedcom_value('DATE', 3, $even_rec, $truncate='', $convert=false));
				$tmp3=get_gedcom_value('PLAC', 3, $even_rec, $truncate='', $convert=false);
				if ($even_num>1)
					print "<br />";
				print "<b>";
				foreach (preg_split('/\W+/', $tmp1) as $key=>$value) {
					if ($key>0)
						print ", ";
					if (empty($factarray[$value]))
						print $value;
					else
						print $factarray[$value];
				}
				print "</b> - ".$tmp2->Display(false, '', array())." - {$tmp3}";
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
					if ($SHOW_FACT_ICONS && file_exists($PGV_IMAGE_DIR."/facts/".$factref.".gif")) print_fact_icon($factref, $factrec, $label, $pid);
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
	} else {
		// -- catch all unknown codes here
		$body = $pgv_lang["unrecognized_code"]." ".$fact;
		$userName=getUserFullName($CONTACT_EMAIL);
		if (!$HIDE_GEDCOM_ERRORS) print "\n\t\t<tr><td class=\"descriptionbox $styleadd\"><span class=\"error\">".$pgv_lang["unrecognized_code"].": $fact</span></td><td class=\"optionbox\">$event<br />".$pgv_lang["unrecognized_code_msg"]." <a href=\"javascript:;\" onclick=\"message('$CONTACT_EMAIL','', '', '$body'); return false;\">".$userName."</a>.</td></tr>";
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
	global $TEXT_DIRECTION;
	if (displayDetailsById($sid, "REPO")) {
		$source = find_repo_record($sid);
		$ct = preg_match("/1 NAME (.*)/", $source, $match);
		if ($ct > 0) {
			$ct2 = preg_match("/0 @(.*)@/", $source, $rmatch);
			if ($ct2>0) $rid = trim($rmatch[1]);
			print "<span class=\"field\"><a href=\"repo.php?rid=$rid\"><b>".PrintReady($match[1])."</b>&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print getRLM();
			print "(".$sid.")";
			if ($TEXT_DIRECTION=="rtl") print getRLM();
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
 * @param boolean $return	whether to return the data or print the data
 */
function print_fact_sources($factrec, $level, $return=false) {
	global $pgv_lang;
	global $factarray;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_SOURCES, $EXPAND_SOURCES;
	$printDone = false;
	$data = "";
	$nlevel = $level+1;
	if ($SHOW_SOURCES<PGV_USER_ACCESS_LEVEL) return "";
	// -- Systems not using source records [ 1046971 ]
	$ct = preg_match_all("/$level SOUR (.*)/", $factrec, $match, PREG_SET_ORDER);
	for($j=0; $j<$ct; $j++) {
		if (strpos($match[$j][1], "@")===false) {
			$srec = get_sub_record($level, " SOUR ", $factrec, $j+1);
			$srec = substr($srec, 5); // remove SOUR
			$srec = str_replace("\n".($level+1)." CONT ", " ", $srec); // remove n+1 CONT
			$srec = str_replace("\n".($level+1)." CONC ", "", $srec); // remove n+1 CONC
			$data .= "<br /><span class=\"label\">".$pgv_lang["source"].":</span> <span class=\"field\">".PrintReady($srec)."</span><br />";
			$printDone = true;
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
			$data .= "<br />";
			$data .= "\n\t\t<span class=\"label\">";
			$elementID = $sid."-".floor(microtime()*1000000);
			if ($EXPAND_SOURCES) $plusminus="minus"; else $plusminus="plus";
			if ($lt>0) $data .= "<a href=\"javascript:;\" onclick=\"expand_layer('$elementID'); return false;\"><img id=\"{$elementID}_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$plusminus]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".$pgv_lang["show_details"]."\" title=\"".$pgv_lang["show_details"]."\" /></a> ";
			$data .= $pgv_lang["source"].":</span> <span class=\"field\">";
			$source = find_source_record($sid);
			$data .= "<a href=\"source.php?sid=".$sid."\">";
			$text = PrintReady(get_source_descriptor($sid));
			//-- Print additional source title
			$add_descriptor = get_add_source_descriptor($sid);
			if ($add_descriptor) $text .= " - ".PrintReady($add_descriptor);
			// if (strpos($source, " _ITALIC")) print "<i>".$text."</i>"; else print $text;
			$data .= $text;
			$data .= "</a>";
			$data .= "</span>";

			$data .= "<div id=\"$elementID\"";
			if ($EXPAND_SOURCES) $data .= " style=\"display:block\"";
			$data .= " class=\"source_citations\">";
			// PUBL
			$text = get_gedcom_value("PUBL", "1", $source);
			if (!empty($text)) {
				$data .= "<span class=\"label\">".$factarray["PUBL"].": </span>";
				$data .= $text;
			}
			$data .= printSourceStructure(getSourceStructure($srec));
			$data .= "<div class=\"indent\">";
			ob_start();
			print_media_links($srec, $nlevel);
			$data .= ob_get_clean();
			$data .= print_fact_notes($srec, $nlevel, false, true);
			$data .= "</div>";
			$data .= "</div>";
			
			$printDone = true;
		}
	}
	if ($printDone) $data .= "<br />";
	if (!$return) print $data;
	else return $data;
}

//-- Print the links to multi-media objects
function print_media_links($factrec, $level,$pid='') {
	global $MULTI_MEDIA, $TEXT_DIRECTION, $TBLPREFIX, $gGedcom;
	global $pgv_lang, $factarray, $SEARCH_SPIDER, $view;
	global $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
	global $GEDCOM, $SHOW_ID_NUMBERS;
	if (!$MULTI_MEDIA) return;
	$nlevel = $level+1;
	if ($level==1) $size=50;
	else $size=25;
	if (preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER) == 0) return;
	$objectNum = 0;
	while ($objectNum < count($omatch)) {
		$media_id = preg_replace("/@/", "", trim($omatch[$objectNum][1]));
		if (displayDetailsById($media_id, "OBJE")) {
			$sql = "SELECT m_titl, m_file, m_gedrec FROM {$TBLPREFIX}media where m_media='{$media_id}' AND m_gedfile={$gGedcom->mGEDCOMId}";
			$tempsql = dbquery($sql);
			$res =& $tempsql;
			if ($res->numRows()>0) {
			$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
			}
			else if (PGV_USER_CAN_EDIT) {
				$mediarec = find_updated_record($media_id);
				$row["m_file"] = get_gedcom_value("FILE", 1, $mediarec);
				$row["m_titl"] = get_gedcom_value("TITL", 1, $mediarec);
				if (empty($row["m_titl"])) $row["m_titl"] = get_gedcom_value("FILE:TITL", 1, $mediarec);
				$row["m_gedrec"] = $mediarec;
			}

			$mainMedia = check_media_depth($row["m_file"], "NOTRUNC");
			$thumbnail = thumbnail_file($mainMedia, true, false, $pid);
			$isExternal = isFileExternal($row["m_file"]);
			$mediaTitle = $row["m_titl"];
			
			// Determine the size of the mediafile
			$imgsize = findImageSize($mainMedia);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			if (showFactDetails("OBJE", $pid)) {
				if ($objectNum > 0) print "<br clear=\"all\" />";
				print "<table><tr><td>";
				if ($isExternal || media_exists($thumbnail)) {
				
//LBox --------  change for Lightbox Album --------------------------------------------
					if (file_exists("modules/lightbox/album.php")&& ( eregi("\.jpg",$mainMedia) || eregi("\.jpeg",$mainMedia) || eregi("\.gif",$mainMedia) || eregi("\.png",$mainMedia) ) ) { 
						$name = trim($row["m_titl"]);
						print "<a href=\"" . $mainMedia . "\" rel=\"clearbox[general_1]\" title=\"" . $media_id . ":" . $GEDCOM . ":" . PrintReady($name) . "\">" . "\n";
// ---------------------------------------------------------------------------------------------
					}elseif ($USE_MEDIA_VIEWER) {
						print "<a href=\"mediaviewer.php?mid=".$media_id."\">";
					}else{
						print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($mainMedia)."',$imgwidth, $imgheight);\">";
					}
					print "<img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\"";
					if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
					print " alt=\"". PrintReady($mediaTitle) . "\" title=\"" . PrintReady($mediaTitle) . "\" /></a>";
				}
				print "</td><td>";
				if(empty($SEARCH_SPIDER)) {
					print "<a href=\"mediaviewer.php?mid=".$media_id."\">";
				}
				if ($TEXT_DIRECTION=="rtl" && !hasRTLText($mediaTitle)) print "<i>" . getLRM() .  PrintReady($mediaTitle)."</i>";
				else print "<i>".PrintReady($mediaTitle)."</i>";
				if(empty($SEARCH_SPIDER)) {
					print "</a>";
				}

				// NOTE: Print the format of the media
				if (!empty($row["m_ext"])) {
					print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">".$row["m_ext"]."</span>";
					if($imgsize[2]!==false) {
						print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?(" " . getRLM() . "x" . getRLM() . " ") : " x ") . $imgsize[1] . "</span>";
					}
				}
				$ttype = preg_match("/".($nlevel+1)." TYPE (.*)/", $row["m_gedrec"], $match);
				if ($ttype>0) {
					$mediaType = $match[1];
					$varName = "TYPE__".strtolower($mediaType);
					if (isset($pgv_lang[$varName])) $mediaType = $pgv_lang[$varName];
					else $mediaType = $pgv_lang["TYPE__other"];
					print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["type"].": </span> <span class=\"field\">$mediaType</span>";
				}
				//print "</span>";
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
								if ($SHOW_ID_NUMBERS) print " " . getLRM() . "($famid)" . getLRM();
								print "]</a>\n";
							}
						}
					}
				}
				print "<br />\n";
				print_fact_notes($row["m_gedrec"], $nlevel);
				print_fact_sources($row["m_gedrec"], $nlevel);
				print "</td></tr></table>\n";
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
	global $factarray;
	global $POSTAL_CODE;

	//   $POSTAL_CODE = 'false' - before city, 'true' - after city and/or state
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
			$resultText .= getLRM() . $omatch[$i][1] . getLRM();
			$resultText .= "</span></td></tr>\n";
		}
	}
	$ct = preg_match_all("/$level FAX (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	if ($ct>0) {
		for($i=0; $i<$ct; $i++) {
			$resultText .= "<tr>";
			$resultText .= "\n\t\t<td><span class=\"label\"><b>".$factarray["FAX"].": </b></span></td><td><span class=\"field\">";
			$resultText .= getLRM() . $omatch[$i][1] . getLRM();
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

function print_main_sources($factrec, $level, $pid, $linenum, $noedit=false) {
	global $pgv_lang;
	global $factarray, $view;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_SOURCES;
	if ($SHOW_SOURCES<PGV_USER_ACCESS_LEVEL) return;
	
	//-- keep the time of this access to help with concurrent edits
	$_SESSION['last_access_time'] = time();
	
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
		$sid = $match[$j][1];
		$spos1 = strpos($factrec, "$level SOUR @".$sid."@", $spos2);
		$spos2 = strpos($factrec, "\n$level", $spos1);
		if (!$spos2) $spos2 = strlen($factrec);
		$srec = substr($factrec, $spos1, $spos2-$spos1);
		if (!showFact("SOUR", $pid) || FactViewRestricted($pid, $factrec)) return false;
		if (displayDetailsById($sid, "SOUR")) {
			if ($level==2) print "<tr class=row_sour2>";
			else print "<tr>";
			print "<td class=\"descriptionbox";
			if ($level==2) print " rela";
			print " $styleadd center width20\">";
			if ($level==1) echo "<img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" alt=\"\" /><br />";
			$temp = preg_match("/^\d (\w*)/", $factrec, $factname);
			echo $factarray[$factname[1]];
			if (!$noedit && PGV_USER_CAN_EDIT && !FactEditRestricted($pid, $factrec) && $styleadd!="red" && $view!="preview") {
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
				$source = find_source_record($sid);
				echo "<a href=\"source.php?sid=".$sid."\">";
				$text = PrintReady(get_source_descriptor($sid));
				//-- Print additional source title
				$add_descriptor = get_add_source_descriptor($sid);
				if ($add_descriptor) $text .= " - ".PrintReady($add_descriptor);
				// if (strpos($source, " _ITALIC")) echo "<i>".$text."</i>"; else echo $text;
				echo $text;
				echo "</a>";
				// PUBL
				$text = get_gedcom_value("PUBL", "1", $source);
				if (!empty($text)) {
					echo "<br /><span class=\"label\">".$factarray["PUBL"].": </span>";
					// if (strpos($source, " _PAREN")) echo "(".$text.")"; else echo $text;
					echo $text;
				}
				// See if RESN tag prevents display or edit/delete
				$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $rmatch);
				if ($resn_tag > 0) $resn_value = strtolower(trim($rmatch[1]));
				// -- Find RESN tag
				if (isset($resn_value)) {
					print "<img src=\"images/RESN_".$resn_value.".gif\" alt=\"".$pgv_lang[$resn_value]."\" title=\"".$pgv_lang[$resn_value]."\" />\n";
					print_help_link("RESN_help", "qm");
				}
				if ($source) {
					print printSourceStructure(getSourceStructure($srec));
					print "<div class=\"indent\">";
					print_media_links($srec, $nlevel);
					print_fact_notes($srec, $nlevel);
					print "</div>";
				}
			}
			print "</td></tr>";
		}
	}
}

/** 
 *	Print SOUR structure
 *
 *  This function prints the input array of SOUR sub-records built by the 
 *  getSourceStructure() function.
 *
 *  The input array is defined as follows:
 *	$textSOUR["PAGE"] = +1  Source citation	
 *	$textSOUR["EVEN"] = +1  Event type
 *	$textSOUR["ROLE"] = +2  Role in event
 *	$textSOUR["DATA"] = +1  place holder (no text in this sub-record)
 *	$textSOUR["DATE"] = +2  Entry recording date
 *	$textSOUR["TEXT"] = +2  (array) Text from source
 *	$textSOUR["QUAY"] = +1  Certainty assessment
 *	$textSOUR["TEXT2"] = +1 (array) Text from source
 */
function printSourceStructure($textSOUR) {
	global $pgv_lang, $factarray;

	$data='';
	if ($textSOUR["PAGE"]!="") {
		$data.="<br /><span class=\"label\">".$factarray["PAGE"].":&nbsp;</span><span class=\"field\">".PrintReady(expand_urls($textSOUR["PAGE"]))."</span>";
	}

	if ($textSOUR["EVEN"]!="") {
		$data.="<br /><span class=\"label\">".$factarray["EVEN"].":&nbsp;</span><span class=\"field\">".PrintReady($textSOUR["EVEN"])."</span>";
		if ($textSOUR["ROLE"]!="") {
			$data.="<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".$factarray["ROLE"].":&nbsp;</span><span class=\"field\">".PrintReady($textSOUR["ROLE"])."</span>";
		}
	}

	if ($textSOUR["DATE"]!="" || count($textSOUR["TEXT"])!=0) {
		// $data.="<br /><span class=\"label\">".$factarray["DATA"]."</span>";
		if ($textSOUR["DATE"]!="") {
			$date=new GedcomDate($textSOUR["DATE"]);
			$data.="<br />&nbsp;&nbsp;<span class=\"label\">".$pgv_lang["date_of_entry"].":&nbsp;</span><span class=\"field\">".$date->Display(false)."</span>";
		}
		foreach($textSOUR["TEXT"] as $text) {
			$data.="<br />&nbsp;&nbsp;<span class=\"label\">".$factarray["TEXT"].":&nbsp;</span><span class=\"field\">".PrintReady(expand_urls($text))."</span>";
		}
	}

	if ($textSOUR["QUAY"]!="") {
		$data.="<br /><span class=\"label\">".$factarray["QUAY"].":&nbsp;</span><span class=\"field\">".PrintReady($textSOUR["QUAY"])."</span>";
	}

	foreach($textSOUR["TEXT2"] as $text) {
		$data.="<br /><span class=\"label\">".$factarray["TEXT"].":&nbsp;</span><span class=\"field\">".PrintReady(expand_urls($text))."</span>";
	}
	return $data;
}

/**
 * Extract SOUR structure from the incoming Source sub-record
 *
 *  The output array is defined as follows:
 *	$textSOUR["PAGE"] = +1  Source citation	
 *	$textSOUR["EVEN"] = +1  Event type
 *	$textSOUR["ROLE"] = +2  Role in event
 *	$textSOUR["DATA"] = +1  place holder (no text in this sub-record)
 *	$textSOUR["DATE"] = +2  Entry recording date
 *	$textSOUR["TEXT"] = +2  (array) Text from source
 *	$textSOUR["QUAY"] = +1  Certainty assessment
 *	$textSOUR["TEXT2"] = +1 (array) Text from source
 */
function getSourceStructure($srec) {
	global $WORD_WRAPPED_NOTES;

	// Set up the output array
	$textSOUR = array();
	$textSOUR["PAGE"] = "";
	$textSOUR["EVEN"] = "";
	$textSOUR["ROLE"] = "";
	$textSOUR["DATA"] = "";
	$textSOUR["DATE"] = "";
	$textSOUR["TEXT"] = array();
	$textSOUR["QUAY"] = "";
	$textSOUR["TEXT2"] = array();

	if ($srec=="") return $textSOUR;

	$subrecords = explode("\n", $srec);
	$levelSOUR = substr($subrecords[0], 0, 1);
	for ($i=0; $i<count($subrecords); $i++) {
		$subrecords[$i] = trim($subrecords[$i]);
		$level = substr($subrecords[$i], 0, 1);
		$tag = substr($subrecords[$i], 2, 4);
		$text = substr($subrecords[$i], 7);
		$i++;
		for (; $i<count($subrecords); $i++) {
			$nextTag = substr($subrecords[$i], 2, 4);
			if ($nextTag!="CONC" && $nextTag!="CONT") {
				$i--;
				break;
			}
			if ($nextTag=="CONT") $text .= "<br />";
			if ($nextTag=="CONC" && $WORD_WRAPPED_NOTES) $text .= " ";
			$text .= rtrim(substr($subrecords[$i], 7));
		}
		if ($tag=="TEXT") {
			if ($level==($levelSOUR+1)) $textSOUR["TEXT2"][] = $text;
			else $textSOUR["TEXT"][] = $text;
		} else {
			$textSOUR[$tag] = $text;
		}
	}

	return $textSOUR;
}

/**
 * print main note row
 *
 * this function will print a table row for a fact table for a level 1 note in the main record
 * @param string $factrec	the raw gedcom sub record for this note
 * @param int $level		The start level for this note, usually 1
 * @param string $pid		The gedcom XREF id for the level 0 record that this note is a part of
 * @param int $linenum		The line number in the level 0 record where this record was found.  This is used for online editing.
 * @param boolean $noedit	Whether or not to allow this fact to be edited
 */
function print_main_notes($factrec, $level, $pid, $linenum, $noedit=false) {
	global $pgv_lang, $pgv_changes, $GEDCOM;
	global $factarray, $view;
	global $PGV_IMAGE_DIR;
	global $PGV_IMAGES;
	global $TEXT_DIRECTION, $USE_RTL_FUNCTIONS;
	
	//-- keep the time of this access to help with concurrent edits
	$_SESSION['last_access_time'] = time();
	
	$styleadd="";
	$ct = preg_match("/PGV_NEW/", $factrec, $match);
	if ($ct>0) $styleadd="change_new";
	$ct = preg_match("/PGV_OLD/", $factrec, $match);
	if ($ct>0) $styleadd="change_old";
	$nlevel = $level+1;
	$ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	for($j=0; $j<$ct; $j++) {
		$nrec = get_sub_record($level, "$level NOTE", $factrec, $j+1);
		if (!showFact("NOTE", $pid)||FactViewRestricted($pid, $factrec)) return false;
		$nt = preg_match("/\d NOTE @(.*)@/", $match[$j][0], $nmatch);
		if ($nt>0) {
			$nid = $nmatch[1];
			if (isset($pgv_changes[$nid."_".$GEDCOM]) && empty($styleadd)) {
				$styleadd = "change_old";
				$newfactrec = $factrec.="\r\nPGV_NEW";
				print_main_notes($factrec, $level, $pid, $linenum, $noedit);
			}
		}
		if ($level==2) print "<tr class=\"row_note2\">";
		else print "<tr>";
		print "<td valign=\"top\" class=\"descriptionbox";
		if ($level==2) print " rela";
		print " $styleadd center width20\">";
		if ($level<2) print "<img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["notes"]["small"]."\" alt=\"\" /><br />".$factarray["NOTE"];
		else {
			$factlines = explode("\n", $factrec); // 1 BIRT Y\n2 NOTE ...
			$factwords = explode(" ", $factlines[0]); // 1 BIRT Y
			$factname = $factwords[1]; // BIRT
			if ($factname == "EVEN") {
				$factwords = explode(" ", $factlines[1]); // 1 EVEN\n2 TYPE MDCL\n2 NOTE
				$factname = $factwords[2]; // MDCL
			}
			if (isset($factarray[$factname])) print $factarray[$factname];
			else print $factname;
		}
		if (!$noedit && PGV_USER_CAN_EDIT && !FactEditRestricted($pid, $factrec) && $styleadd!="change_old" && $view!="preview") {
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
		if ($nt==0) {
			//-- print embedded note records
			$text = preg_replace("/~~/", "<br />", trim($match[$j][1]));
			$text .= get_cont($nlevel, $nrec);
			$text = expand_urls($text);
			$text = PrintReady($text);
		}
		else {
			//-- print linked note records
			if (isset($pgv_changes[$nid."_".$GEDCOM]) && $styleadd=="change_new") $noterec = find_updated_record($nid);
			else $noterec = find_gedcom_record($nid);

			$nt = preg_match("/0 @$nid@ NOTE (.*)/", $noterec, $n1match);
			$text ="";
			if ($nt>0) $text = preg_replace("/~~/", "<br />", trim($n1match[1]));
			$text .= get_cont(1, $noterec);
			$text = expand_urls($text);
			$text = PrintReady($text)." <br />\n";
		}
		$align = "";
		if (!empty($text)) {
			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($text) && hasLTRText($text)) $align=" align=\"left\"";
			if ($TEXT_DIRECTION=="ltr" && $USE_RTL_FUNCTIONS && !hasLTRText($text) && hasRTLText($text)) $align=" align=\"right\"";
		}
		print " </td>\n<td class=\"optionbox $styleadd wrap\" $align>";
		if (showFactDetails("NOTE", $pid)) {
			print $text;
			if (!empty($noterec)) print_fact_sources($noterec, 1);
			// See if RESN tag prevents display or edit/delete
			$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $rmatch);
			if ($resn_tag > 0) $resn_value = strtolower(trim($rmatch[1]));
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

/**
 * Print the links to multi-media objects
 * @param string $pid	The the xref id of the object to find media records related to
 * @param int $level	The level of media object to find
 * @param boolean $related	Whether or not to grab media from related records
 */
function print_main_media($pid, $level=1, $related=false, $noedit=false) {
	global $TBLPREFIX;
	global $pgv_changes;
	global $gGedcom, $GEDCOM, $MEDIATYPE, $pgv_changes, $DBCONN;

	if (!showFact("OBJE", $pid)) return false;
	if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
	else $gedrec = find_updated_record($pid);
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
		if (!isset($current_objes[$match[$i][1]])) $current_objes[$match[$i][1]] = 1;
		else $current_objes[$match[$i][1]]++;
		$obje_links[$match[$i][1]][] = $match[$i][0];
	}

	$media_found = false;
	// $sqlmm = "SELECT DISTINCT ";
	// Adding DISTINCT is the fix for: [ 1488550 ] Family/Individual Media Duplications
	// but it may not work for all RDBMS.
	$sqlmm = "SELECT ";
	$sqlmm .= "m_media, m_ext, m_file, m_titl, m_gedfile, m_gedrec, mm_gid, mm_gedrec FROM ".$TBLPREFIX."media, ".$TBLPREFIX."media_mapping where ";
	$sqlmm .= "mm_gid IN (";
	$i=0;
	foreach($ids as $key=>$id) {
		if ($i>0) $sqlmm .= ",";
		$sqlmm .= "'".$DBCONN->escapeSimple($id)."'";
		$i++;
	}
	$sqlmm .= ") AND mm_gedfile = '".$gGedcom[$GEDCOM]["id"]."' AND mm_media=m_media AND mm_gedfile=m_gedfile ";
	//-- for family and source page only show level 1 obje references
	if ($level>0) $sqlmm .= "AND mm_gedrec LIKE '$level OBJE%'";

	$sqlmm .= "ORDER BY mm_gid DESC";
	$resmm = dbquery($sqlmm);
	$foundObjs = array();
	while($rowm = $resmm->fetchRow(DB_FETCHMODE_ASSOC)) {
		if (isset($foundObjs[$rowm['m_media']])) {
			if (isset($current_objes[$rowm['m_media']])) $current_objes[$rowm['m_media']]--;
			continue;
		}
		// NOTE: Determine the size of the mediafile
		$imgwidth = 300+40;
		$imgheight = 300+150;
		if (isFileExternal($rowm["m_file"])) {
			if (in_array($rowm["m_ext"], $MEDIATYPE)) {
				$imgwidth = 400+40;
				$imgheight = 500+150;
			}
			else {
				$imgwidth = 800+40;
				$imgheight = 400+150;
			}
		}
		else {
			$imgsize = @findImageSize(check_media_depth($rowm["m_file"], "NOTRUNC"));
			if ($imgsize[0]) {
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;
			}
		}
		$rows=array();
		//-- if there is a change to this media item then get the
		//-- updated media item and show it
		if (isset($pgv_changes[$rowm["m_media"]."_".$GEDCOM])) {
			$newrec = find_updated_record($rowm["m_media"]);
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
			$row['mm_gedrec'] = $rowm["mm_gedrec"];
			$rows['new'] = $row;
			$rows['old'] = $rowm;
			$current_objes[$rowm['m_media']]--;
		}
		else {
			if (!isset($current_objes[$rowm['m_media']]) && ($rowm['mm_gid']==$pid)) $rows['old'] = $rowm;
			else {
				$rows['normal'] = $rowm;
				if (isset($current_objes[$rowm['m_media']])) $current_objes[$rowm['m_media']]--;
			}
		}
		foreach($rows as $rtype => $rowm) {
			$res = print_main_media_row($rtype, $rowm, $pid);
			$media_found = $media_found || $res;
			$foundObjs[$rowm['m_media']]=true;
		}
		//$media_found = true;
	}

	//-- objects are removed from the $current_objes list as they are printed
	//-- any objects left in the list are new objects recently added to the gedcom
	//-- but not yet accepted into the database.  We will print them too.
	foreach($current_objes as $media_id=>$value) {
		while($value>0) {
			$objSubrec = array_pop($obje_links[$media_id]);
		//-- check if we need to get the object from a remote location
		$ct = preg_match("/(.*):(.*)/", $media_id, $match);
		if ($ct>0) {
			require_once 'includes/serviceclient_class.php';
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
					$row['mm_gedrec'] = get_sub_record($objSubrec{0}, $objSubrec, $gedrec);
				$res = print_main_media_row('normal', $row, $pid);
				$media_found = $media_found || $res;
			}
		}
		else {
			$row = array();
			$newrec = find_updated_record($media_id);
			if (empty($newrec)) $newrec = find_media_record($media_id);
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
				$row['mm_gedrec'] = get_sub_record($objSubrec{0}, $objSubrec, $gedrec);
			$res = print_main_media_row('new', $row, $pid);
			$media_found = $media_found || $res;
		}
			$value--;
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
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $view, $TEXT_DIRECTION;
	global $SHOW_ID_NUMBERS, $GEDCOM, $factarray, $pgv_lang, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
	global $SEARCH_SPIDER;

	if (!displayDetailsById($rowm['m_media'], 'OBJE') || FactViewRestricted($rowm['m_media'], $rowm['m_gedrec'])) {
		//print $rowm['m_media']." no privacy ";
		return false;
	}

	//-- keep the time of this access to help with concurrent edits
	$_SESSION['last_access_time'] = time();
		
	$styleadd="";
	if ($rtype=='new') $styleadd = "change_new";
	if ($rtype=='old') $styleadd = "change_old";
	// NOTEStart printing the media details
	$thumbnail = thumbnail_file($rowm["m_file"], true, false, $pid);
	$isExternal = isFileExternal($thumbnail);

	$linenum = 0;
	print "\n\t\t<tr><td class=\"descriptionbox $styleadd center width20\"><img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["small"]."\" alt=\"\" /><br />".$factarray["OBJE"];
	if ($rowm['mm_gid']==$pid && PGV_USER_CAN_EDIT && (!FactEditRestricted($rowm['m_media'], $rowm['m_gedrec'])) && ($styleadd!="change_old") && ($view!="preview")) {
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
		$subtitle = get_gedcom_value("TITL", 2, $rowm["mm_gedrec"]);
		if (!empty($subtitle)) $mediaTitle = $subtitle;
		$mainMedia = check_media_depth($rowm["m_file"], "NOTRUNC");
		if ($mediaTitle=="") $mediaTitle = basename($rowm["m_file"]);
		if ($isExternal || media_exists($thumbnail)) {
			$mainFileExists = false;
			if ($isExternal || media_exists($mainMedia)) {
				$mainFileExists = true;
				$imgsize = findImageSize($mainMedia);
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;
//LBox --------  change for Lightbox Album --------------------------------------------
					if (file_exists("modules/lightbox/album.php") && ( eregi("\.jpg",$mainMedia) || eregi("\.jpeg",$mainMedia) || eregi("\.gif",$mainMedia) || eregi("\.png",$mainMedia) ) ) { 
					$name = trim($rowm["m_titl"]);
					print "<a href=\"" . $mainMedia . "\" rel=\"clearbox[general_3]\" title=\"" . $rowm["m_media"] . ":" . $GEDCOM . ":" . PrintReady($name) . "\">" . "\n";
// ---------------------------------------------------------------------------------------------
				}elseif ($USE_MEDIA_VIEWER) {
					print "<a href=\"mediaviewer.php?mid=".$rowm["m_media"]."\">";
				}else{
					print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($mainMedia)."',$imgwidth, $imgheight);\">";
				}
			}
			print "<img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\"";
			if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
			print " alt=\"" . PrintReady(htmlspecialchars($mediaTitle)) . "\" title=\"" . PrintReady(htmlspecialchars($mediaTitle)) . "\" />";
			if ($mainFileExists) print "</a>";
		}
		if(empty($SEARCH_SPIDER)) {
			print "<a href=\"mediaviewer.php?mid=".$rowm["m_media"]."\">";
		}
		if ($TEXT_DIRECTION=="rtl" && !hasRTLText($mediaTitle)) print "<i>" . getLRM() . PrintReady(htmlspecialchars($mediaTitle)."&nbsp;&nbsp;({$rowm['m_media']})");
		else print "<i>".PrintReady(htmlspecialchars($mediaTitle)."&nbsp;&nbsp;({$rowm['m_media']})");
		$addtitle = get_gedcom_value("TITL:_HEB", 2, $rowm["mm_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:_HEB", 2, $rowm["m_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:_HEB", 1, $rowm["m_gedrec"]);
		if (!empty($addtitle)) print "<br />\n".PrintReady(htmlspecialchars($addtitle));
		$addtitle = get_gedcom_value("TITL:ROMN", 2, $rowm["mm_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:ROMN", 2, $rowm["m_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:ROMN", 1, $rowm["m_gedrec"]);
		if (!empty($addtitle)) print "<br />\n".PrintReady(htmlspecialchars($addtitle));
		print "</i>";
		if(empty($SEARCH_SPIDER)) {
			print "</a>";
		}

		// NOTE: Print the format of the media
		if (!empty($rowm["m_ext"])) {
			print "\n\t\t\t<br /><span class=\"label\">".$factarray["FORM"].": </span> <span class=\"field\">".$rowm["m_ext"]."</span>";
			if(isset($imgsize) and $imgsize[2]!==false) {
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?(" " . getRLM() . "x" . getRLM(). " ") : " x ") . $imgsize[1] . "</span>";
			}
		}
		$ttype = preg_match("/\d TYPE (.*)/", $rowm["m_gedrec"], $match);
		if ($ttype>0) {
			$mediaType = trim($match[1]);
			$varName = "TYPE__".strtolower($mediaType);
			if (isset($pgv_lang[$varName])) $mediaType = $pgv_lang[$varName];
			else $mediaType = $pgv_lang["TYPE__other"];
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
						if ($SHOW_ID_NUMBERS) print " " . getLRM() . "($famid)" . getLRM();
						print "]</a>\n";
				}
			}
		}
		//print "<br />\n";
		$prim = get_gedcom_value("_PRIM", 2, $rowm["mm_gedrec"]);
		if (empty($prim)) $prim = get_gedcom_value("_PRIM", 1, $rowm["m_gedrec"]);
		if (!empty($prim)) {
			print "<span class=\"label\">".$factarray["_PRIM"].":</span> ";
		if ($prim=="Y") print $pgv_lang["yes"]; else print $pgv_lang["no"];
		print "<br />\n";
		}
		$thum = get_gedcom_value("_THUM", 2, $rowm["mm_gedrec"]);
		if (empty($thum)) $thum = get_gedcom_value("_THUM", 1, $rowm["m_gedrec"]);
		if (!empty($thum)) {
			print "<span class=\"label\">".$factarray["_THUM"].":</span> ";
		if ($thum=="Y") print $pgv_lang["yes"]; else print $pgv_lang["no"];
		print "<br />\n";
		}
		print_fact_notes($rowm["m_gedrec"], 1);
		print_fact_notes($rowm["mm_gedrec"], 2);
		print_fact_sources($rowm["m_gedrec"], 1);
		print_fact_sources($rowm["mm_gedrec"], 2);
	}
	print "</td></tr>";
	return true;
}
/**
 * Print a fact icon that varies by the decade, century, and subtype
 *
 * Many facts change over time.  Military uniforms, marriage dress, census forms.
 * This is a cutesy way to show the changes over time.  More icons need to be added
 * to the themes/?????/images/facts/ directory with a form of nn00_TYPE.gif or nnn0_TYPE.gif.
 * A special case of nn00_OCCU_FARM.gif has been added to celebrate farmers and farm hands.
 * A special case of nn00_OCCU_HOUS.gif has been added for KEEPing HOUSe or HOUSe KEEPers.
 * Generic subtyping is done by storing the first four characters of the value of the
 * record in a filename.  "1 RELI Methodist" is RELI_METH.gif or 1900_RELI_METH.gif.
 * 1960__MILI_CONF.gif would be Confederate soldier, and 1860__MILI_UNIO.gif would be
 * the counterpart Union soldier.  The most specific match wins.
 * Examples: 1900_CENS.gif 1910_CENS.gif 1900_OCCU_FARM.gif 1800_OCCU_FARM.gif
 *
 * @param string $fact		The fact type to print
 * @param string $factrec	The gedcom subrecord
 * @param string $label		The fact type described and possibly translated
 * @param string $pid		The gedcom id record the fact orginiated from
 */
function print_fact_icon($fact, $factrec, $label, $pid) {
	global $SHOW_FACT_ICONS, $PGV_IMAGE_DIR;

	if ($SHOW_FACT_ICONS) {
		$fact_image = "";
		if (preg_match('/2 DATE (.+)/', $factrec, $match)) {
			$factdate = new GedcomDate($match[1]);
			$factyear = $factdate->gregorianYear();
		} else
			$factyear=0;
		$joe = null;
		if (id_type($pid)=='INDI') $joe = Person::getInstance($pid);
		$sexcheck = "";
		if (!is_null($joe)) {
			$sex = $joe->getSex();
			if(($sex == "F") || ($sex == "M") || ($sex == "U"))
				$sexcheck = "_".$sex;
		}
		// If the date is not on the fact, fall back to birth if available
		// Does not catch the date if it is attached to the source of the fact
		// (ratid entered OCCU) or if the fact comes from a family record. (MARR)
		if ($factyear == 0 && !is_null($joe)) {
			$fallback=$joe->getEstimatedBirthDate();
			$fallback=$fallback->gregorianYear();
			if($fallback)
				$factyear=$fallback;
		}

		// converting from scalar to array string.
		$century = $decade = sprintf("%04d", $factyear);
		$decade[3] = '0';	// Zero out the years
		$century[2] = '0';	// Zero out the decades
		$century[3] = '0';

		if(file_exists($PGV_IMAGE_DIR."/facts/".$fact.".gif"))
			$fact_image = $PGV_IMAGE_DIR."/facts/".$fact;
		if(file_exists($PGV_IMAGE_DIR."/facts/".$century."_".$fact.".gif"))
			$fact_image = $PGV_IMAGE_DIR."/facts/".$century."_".$fact;
		if(file_exists($PGV_IMAGE_DIR."/facts/".$decade."_".$fact.".gif"))
			$fact_image = $PGV_IMAGE_DIR."/facts/".$decade."_".$fact;

		// Since so much of the population before 1900 were farmers and their
		// wives keeping house, why not show a special icon.
		if(($fact == "OCCU") && ((stristr(substr($factrec, 7, 30), "house")) ||
		                         (stristr(substr($factrec, 7, 30), "keep")))) {
			if(file_exists($fact_image."_HOUS.gif"))
				$fact_image = $fact_image."_HOUS";
			}
		if(($fact == "OCCU") && (stristr(substr($factrec, 7, 30), "farm"))) {
			if(file_exists($fact_image."_FARM.gif"))
				$fact_image = $fact_image."_FARM";
			}
		$j = explode(" ", substr($factrec, 0, 20));
		if((!empty($j[2])) && (!empty($fact_image))) {
			$subtype = strtoupper(substr($j[2], 0, 4));
			if(file_exists($fact_image."_".$subtype.".gif"))
				$fact_image = $fact_image."_".$subtype;
			}
		// Append _M, _F, or _U if available
		if($sexcheck != "") {
			if(file_exists($fact_image.$sexcheck.".gif"))
				$fact_image = $fact_image.$sexcheck;
			}

		if(!empty($fact_image))
			print "<img src=\"".$fact_image.".gif\" alt=\"".$label."\" title=\"".$label."\" align=\"middle\" /> ";
	}
	return;
}

// -----------------------------------------------------------------------------
//  Extra print_facts_functions for lightbox 
// -----------------------------------------------------------------------------

function lightbox_print_media($pid, $level=1, $related=false, $kind, $noedit=false ) {
         include("modules/lightbox/functions/lightbox_print_media.php");
}

function lightbox_print_media_row($rtype, $rowm, $pid) {
         include("modules/lightbox/functions/lightbox_print_media_row.php");
}

function lightbox_print_media_row_sort($rtype, $rowm, $pid) {
         include("modules/lightbox/functions/lightbox_print_media_row_sort.php");
}

// -----------------------------------------------------------------------------
//  End extra print_facts_functions for lightbox
// -----------------------------------------------------------------------------

?>

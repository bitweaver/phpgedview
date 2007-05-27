<?php
/**
 * Used by AJAX to load the expanded view inside person boxes
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id: expand_view.php,v 1.1 2007/05/27 17:49:22 lsces Exp $
 */
require_once("config.php");

$pid = $_REQUEST['pid'];
$indirec = find_person_record($pid);

$skipfacts = array("SEX","FAMS","FAMC","NAME","TITL","NOTE","SOUR","SSN","OBJE","HUSB","WIFE","CHIL","ALIA","ADDR","PHON","SUBM","_EMAIL","CHAN","URL","EMAIL","WWW","RESI","_UID","_TODO");
$subfacts = get_all_subrecords($indirec, implode(",", $skipfacts));
	  
	  $f2 = 0;
	  foreach($subfacts as $indexval => $factrec) {
		  if (!FactViewRestricted($pid, $factrec)) {
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
			else {
				$fact="";
				$details="";
			}
			if (($fact!="EVEN")&&($fact!="FACT")) {
				print "<span class=\"details_label\">";
				if (isset($factarray[$fact])) print $factarray[$fact];
				else print $fact;
				print "</span> ";
			}
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
				if ($spouse!=="") print " - ";
				print "<a href=\"family.php?famid=$famid\">[".$pgv_lang["view_family"]."]</a>\n";
			}
			print_fact_place($factrec, true, true);
		}
	  }
?>
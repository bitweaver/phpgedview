<?php
/**
 * Displays a place hierachy
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and Others
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
 * @subpackage Lists
 * @version $Id 0.8: placelist.php 2008-04-20 18:46:09Z wooc$
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
//require("config.php");
if (file_exists('modules/googlemap/placehierarchy.php')) require("modules/googlemap/placehierarchy.php");
//require_once("includes/functions_print_lists.php");

function case_in_array($value, $array) {
	foreach($array as $key=>$val) {
		if (strcasecmp($value, $val)==0) return true;
	}
	return false;
}

if (empty($action)) $action = "find";
if (empty($display)) $display = "hierarchy";

if (!isset($GOOGLEMAP_ENABLED) || $GOOGLEMAP_ENABLED == "false" || (!isset($GOOGLEMAP_PLACE_HIERARCHY) || $GOOGLEMAP_PLACE_HIERARCHY == "false")) {
	$use_googlemap = false;
}
else $use_googlemap = true;

if ($display=="hierarchy") print_header($pgv_lang["place_list"]);
else print_header($pgv_lang["place_list2"]);

print "\n\t<div class=\"center\">";
if ($display=="hierarchy") print "<h2>".$pgv_lang["place_list"]."</h2>\n\t";
else print "<h2>".$pgv_lang["place_list2"]."</h2>\n\t";

// Make sure the "parent" array has no holes
if (isset($parent) && is_array($parent)) {
	$parentKeys = array_keys($parent);
	$highKey = max($parentKeys);
	for ($j=0; $j<=$highKey; $j++) {
		if (!isset($parent[$j])) $parent[$j] = "";
	}
	ksort($parent, SORT_NUMERIC);
}

if (!isset($parent)) $parent=array();
else {
	if (!is_array($parent)) $parent = array();
	else $parent = array_values($parent);
}
// Remove slashes
foreach ($parent as $p => $child){
	$parent[$p] = stripLRMRLM(stripslashes($child));
}

if (!isset($level)) {
	$level=0;
}

if ($level>count($parent)) $level = count($parent);
if ($level<count($parent)) $level = 0;

if ($use_googlemap) {
	$levelm = set_levelm($level, $parent);
}

//-- extract the place form encoded in the gedcom
$header = find_gedcom_record("HEAD");
$hasplaceform = strpos($header, "1 PLAC");

//-- hierarchical display
if ($display=="hierarchy") {
	// -- array of names
	$placelist = array();
	$positions = array();
	$numfound = 0;
	get_place_list();
	// -- sort the array
	$placelist = array_unique($placelist);
	uasort($placelist, "stringsort");

	//-- create a query string for passing to search page
	$tempparent = array_reverse($parent);
	if (count($tempparent)>0) $squery = "&query=".urlencode($tempparent[0]);
	else $squery="";
	for($i=1; $i<$level; $i++) {
		$squery.=", ".urlencode($tempparent[$i]);
	}

	//-- if the number of places found is 0 then automatically redirect to search page
	if ($numfound==0) {
		$action="show";
	}

	// -- print the breadcrumb hierarchy
	$numls=0;
	if ($level>0) {
		//-- link to search results
		if ((($level>1)||($parent[0]!=""))&&($numfound>0)) {
			print $numfound."  ".$pgv_lang["connections"].": ";
		}
		//-- breadcrumb
		$numls = count($parent)-1;
		$num_place="";
		//-- place and page text orientation is opposite -> top level added at the beginning of the place text
		print "<a href=\"?level=0\">";
		if ($numls>=0 && (($TEXT_DIRECTION=="ltr" && hasRtLText($parent[$numls])) || ($TEXT_DIRECTION=="rtl" && !hasRtLText($parent[$numls])))) print $pgv_lang["top_level"].", ";
		print "</a>";
	    for($i=$numls; $i>=0; $i--) {
			print "<a href=\"?level=".($i+1)."&amp;";
			for ($j=0; $j<=$i; $j++) {
				$levels = preg_split ("/,/", trim($parent[$j]));
				// Routine for replacing ampersands
				foreach($levels as $pindex=>$ppart) {
					$ppart = urlencode($ppart);
					$ppart = preg_replace("/amp\%3B/", "", trim($ppart));
					print "&amp;parent[$j]=".$ppart;
				}
			}
 			print "\">";
 			if (trim($parent[$i])=="") print $pgv_lang["unknown"];
			else print PrintReady($parent[$i]);
			print "</a>";
 			if ($i>0) print ", ";
 			else if (($TEXT_DIRECTION=="rtl" && hasRtLText($parent[$i])) || ($TEXT_DIRECTION=="ltr" &&  !hasRtLText($parent[$i])))  print ", ";
			if (empty($num_place)) $num_place=$parent[$i];
		}
		if ($use_googlemap) $levelo=check_were_am_i($numls, $levelm);
	}
	else if ($use_googlemap) $levelo[0]=0;
	print "<a href=\"?level=0\">";
	//-- place and page text orientation is the same -> top level added at the end of the place text
	if ($level==0 || ($numls>=0 && (($TEXT_DIRECTION=="rtl" && hasRtLText($parent[$numls])) || ($TEXT_DIRECTION=="ltr" && !hasRtLText($parent[$numls]))))) print $pgv_lang["top_level"];
	print "</a>";

	print_help_link("ppp_levels_help", "qm");

	if ($use_googlemap)
		create_map($numfound, $level, $levelm);
	else {
		// show clickable map if found
		print "\n\t<br /><br />\n\t<table class=\"width90\"><tr><td class=\"center\">";
		if ($level>=1 and $level<=3) {
			$country = $parent[0];
			if ($country == "\xD7\x99\xD7\xA9\xD7\xA8\xD7\x90\xD7\x9C") $country = "ISR"; // Israel hebrew name
			$country = strtoupper($country);
			if (strlen($country)!=3) {
				// search country code using current language countries table
				require("languages/countries.en.php");
				if (file_exists("languages/countries.".$lang_short_cut[$deflang].".php")) require("languages/countries.".$lang_short_cut[$deflang].".php");
				foreach ($countries as $countrycode => $countryname) {
					if (strtoupper($countryname) == $country) {
						$country = $countrycode;
						break;
					}
				}
				if (strlen($country)!=3) $country=substr($country,0,3);
			}
			$mapname = $country;
			$areaname = $parent[0];
			$imgfile = "places/".$country."/".$mapname.".gif";
			$mapfile = "places/".$country."/".$country.".".$lang_short_cut[$LANGUAGE].".htm";
			if (!file_exists($mapfile)) $mapfile = "places/".$country."/".$country.".htm";
			if ($level>1) {
				$state = smart_utf8_decode($parent[1]);
				$mapname .= "_".$state;
				if ($level>2) {
					$county = smart_utf8_decode($parent[2]);
					$mapname .= "_".$county;
					$areaname = str_replace("'","\'",$parent[2]);
				}
				else {
					$areaname = str_replace("'","\'",$parent[1]);
				}
				$mapname = str_replace("Ę","E",$mapname);
				$mapname = str_replace("Ó","O",$mapname);
				$mapname = str_replace("Ą","A",$mapname);
				$mapname = str_replace("Ś","S",$mapname);
				$mapname = str_replace("Ł","L",$mapname);
				$mapname = str_replace("Ż","Z",$mapname);
				$mapname = str_replace("Ź","Z",$mapname);
				$mapname = str_replace("Ć","C",$mapname);
				$mapname = str_replace("Ń","N",$mapname);
				$mapname = str_replace("ę","e",$mapname);
				$mapname = str_replace("ó","o",$mapname);
				$mapname = str_replace("ą","a",$mapname);
				$mapname = str_replace("ś","s",$mapname);
				$mapname = str_replace("ł","l",$mapname);
				$mapname = str_replace("ż","z",$mapname);
				$mapname = str_replace("ź","z",$mapname);
				$mapname = str_replace("ć","c",$mapname);
				$mapname = str_replace("ń","n",$mapname);
				$mapname = strtr($mapname,"���������������������������������������������������������������������' ","SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy--");
				$imgfile = "places/".$country."/".$mapname.".gif";
			}
			if (file_exists($imgfile) and file_exists($mapfile)) {
				include ($mapfile);
				print "<img src='".$imgfile."' usemap='#".$mapname."' border='0' alt='".$areaname."' title='".$areaname."' />";
				?>
				<script type="text/javascript" src="strings.js"></script>
				<script type="text/javascript">
				<!--
				//copy php array into js array
				var places_accept = new Array(<?php foreach ($placelist as $key => $value) print "'".str_replace("'", "\'", $value)."',"; print "''";?>)
				Array.prototype.in_array = function(val) {
					for (var i in this) {
						if (this[i] == val) return true;
					}
					return false;
				}
				function setPlaceState(txt) {
					if (txt=='') return;
					// search full text [California (CA)]
					var search = txt;
					if (places_accept.in_array(search)) return(location.href = '?level=2<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]="?>'+search);
					// search without optional code [California]
					txt = txt.replace(/(\/)/,' ('); // case: finnish/swedish ==> finnish (swedish)
					p=txt.indexOf(' (');
					if (p>1) search=txt.substring(0,p);
					else return;
					if (places_accept.in_array(search)) return(location.href = '?level=2<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]="?>'+search);
					// search with code only [CA]
					search=txt.substring(p+2);
					p=search.indexOf(')');
					if (p>1) search=search.substring(0,p);
					if (places_accept.in_array(search)) return(location.href = '?level=2<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]="?>'+search);
				}
				function setPlaceCounty(txt) {
					if (txt=='') return;
					var search = txt;
					if (places_accept.in_array(search)) return(location.href = '?level=3<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]="?>'+search);
					txt = txt.replace(/(\/)/,' (');
					p=txt.indexOf(' (');
					if (p>1) search=txt.substring(0,p);
					else return;
					if (places_accept.in_array(search)) return(location.href = '?level=3<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]="?>'+search);
					search=txt.substring(p+2);
					p=search.indexOf(')');
					if (p>1) search=search.substring(0,p);
					if (places_accept.in_array(search)) return(location.href = '?level=3<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]="?>'+search);
				}
				function setPlaceCity(txt) {
					if (txt=='') return;
					var search = txt;
					if (places_accept.in_array(search)) return(location.href = '?level=4<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]=".urlencode(@$parent[2])."&parent[3]="?>'+search);
					txt = txt.replace(/(\/)/,' (');
					p=txt.indexOf(' (');
					if (p>1) search=txt.substring(0,p);
					else return;
					if (places_accept.in_array(search)) return(location.href = '?level=4<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]=".urlencode(@$parent[2])."&parent[3]="?>'+search);
					search=txt.substring(p+2);
					p=search.indexOf(')');
					if (p>1) search=search.substring(0,p);
					if (places_accept.in_array(search)) return(location.href = '?level=4<?php print "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]=".urlencode(@$parent[2])."&parent[3]="?>'+search);
				}
				//-->
				</script>
				<?php
				print "</td><td style=\"margin-left:15; vertical-align: top;\">";
			}
		}
	}
	print "<td class=\"center\" valign=\"top\">";

	//-- create a string to hold the variable links
	$linklevels="";
	if ($use_googlemap) $placelevels="";
	for($j=0; $j<$level; $j++) {
		$linklevels .= "&amp;parent[$j]=".urlencode($parent[$j]);
		if ($use_googlemap)
			if (trim($parent[$j])=="")
				$placelevels = ", ".$pgv_lang["unknown"].$placelevels;
			else
				$placelevels = ", ".$parent[$j].$placelevels;
	}
	$i=0;
	$ct1=count($placelist);

	// -- print the array
	foreach ($placelist as $key => $value) {
		if ($i==0) {
			print "\n\t<br />\n\t<table class=\"list_table $TEXT_DIRECTION\"";
			if ($TEXT_DIRECTION=="rtl") print " dir=\"rtl\"";
			print ">\n\t\t<tr>\n\t\t<td class=\"list_label\" ";
			if ($ct1 > 20) print "colspan=\"3\"";
			else if ($ct1 > 4) print "colspan=\"2\"";
			print ">&nbsp;";
			print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" border=\"0\" title=\"".$pgv_lang["search_place"]."\" alt=\"".$pgv_lang["search_place"]."\" />&nbsp;&nbsp;";
			if ($level>0) {
				print " ".$pgv_lang["place_list_aft"]." ";
				print PrintReady($num_place);
			}
			else print $pgv_lang["place_list"];

			print "&nbsp;";
			print_help_link("ppp_placelist_help", "qm");
			print "</td></tr><tr><td class=\"list_value\"><ul>\n\t\t\t";
		}

		if (begRTLText($value))
			 print "<li class=\"rtl\" dir=\"rtl\"";
		else print "<li class=\"ltr\" dir=\"ltr\"";
		print " type=\"square\">\n<a href=\"?action=$action&amp;level=".($level+1).$linklevels;
		print "&amp;parent[$level]=".urlencode($value)."\" class=\"list_item\">";

		if (trim($value)=="") print $pgv_lang["unknown"];
		else print PrintReady($value);
		print "</a></li>\n";
		if ($ct1 > 20){
			if ($i == floor($ct1 / 3)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
			if ($i == floor(($ct1 / 3) * 2)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
		}
		else if ($ct1 > 4 && $i == floor($ct1 / 2)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
	    $i++;
	}
	if ($i>0){
		print "\n\t\t</ul></td></tr>";
		if (($action!="show")&&($level>0)) {
			print "<tr>\n\t\t<td class=\"list_label\" ";
			if ($ct1 > 20) print "colspan=\"3\"";
			else if ($ct1 > 4) print "colspan=\"2\"";
			print ">\n\t";
			print $pgv_lang["view_records_in_place"];
			print_help_link("ppp_view_records_help", "qm");
			print "</td></tr><tr><td class=\"list_value\" ";
			if ($ct1 > 20) print "colspan=\"3\"";
			else if ($ct1 > 4) print "colspan=\"2\"";
			print " style=\"text-align: center;\">";
			print "<a href=\"?action=show&amp;level=$level";
			foreach($parent as $key=>$value) {
				print "&amp;parent[$key]=".urlencode(trim($value));
			}
			print "\"><span class=\"formField\">";
			if (trim($value)=="") print $pgv_lang["unknown"];
			else print PrintReady($value);
			print "</span></a> ";
			print "</td></tr>";
		}
		print "</table>";
	}
	print "</td></tr></table>";

}

if ($level > 0) {
	if ($action=="show") {
		// -- array of names
		$myindilist = array();
		$mysourcelist = array();
		$myfamlist = array();

		$positions = get_place_positions($parent, $level);
		for($i=0; $i<count($positions); $i++) {
			$gid = $positions[$i];
			$indirec=find_gedcom_record($gid);
			$ct = preg_match("/0 @(.*)@ (.*)/", $indirec, $match);
			if ($ct>0) {
				$type = trim($match[2]);
				if ($type == "INDI") {
					$myindilist["$gid"] = get_sortable_name($gid);
				}
				else if ($type == "FAM") {
					$myfamlist["$gid"] = get_sortable_family_descriptor($gid);
				}
				else if ($type == "SOUR") {
					$mysourcelist["$gid"] = get_source_descriptor($gid);
				}
			}
		}

		print "<br />";

		$title = ""; foreach ($parent as $k=>$v) $title = $v.", ".$title;
		$title = PrintReady(substr($title, 0, -2))." ";
		// Sort each of the tables by Name
		if (count($myindilist) > 1) uasort($myindilist, "stringsort");
		if (count($myfamlist) > 1) uasort($myfamlist, "stringsort");
		if (count($mysourcelist) > 1) uasort($mysourcelist, "stringsort");
		// Print each of the tables
		print_indi_table(array_keys($myindilist),   $pgv_lang["individuals"]." @ ".$title);
		print_fam_table (array_keys($myfamlist),    $pgv_lang["families"   ]." @ ".$title);
		print_sour_table(array_keys($mysourcelist), $pgv_lang["sources"    ]." @ ".$title);
	}
}

//-- list type display
if ($display=="list") {
	$placelist = array();

	find_place_list("");
	$placelist = array_unique($placelist);
	uasort($placelist, "stringsort");
	if (count($placelist)==0) {
		print "<b>".$pgv_lang["no_results"]."</b><br />";
	}
	else {
		print "\n\t<table class=\"list_table $TEXT_DIRECTION\"";
		if ($TEXT_DIRECTION=="rtl") print " dir=\"rtl\"";
		print ">\n\t\t<tr>\n\t\t<td class=\"list_label\" ";
		$ct = count($placelist);
		print " colspan=\"".($ct>20?"3":"2")."\">&nbsp;";
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" border=\"0\" title=\"".$pgv_lang["search_place"]."\" alt=\"".$pgv_lang["search_place"]."\" />&nbsp;&nbsp;";
		print $pgv_lang["place_list2"];
		print "&nbsp;";
		print_help_link("ppp_placelist_help2", "qm");
		print "</td></tr><tr><td class=\"list_value_wrap\"><ul>\n\t\t\t";
		$i=0;
		foreach($placelist as $indexval => $revplace) {
			$linklevels = "";
			$levels = preg_split ("/,/", $revplace);		// -- split the place into comma seperated values
			$level=0;
			$revplace = "";
			foreach($levels as $indexval => $place) {
				$place = trim($place);
				$linklevels .= "&amp;parent[$level]=".urlencode($place);
				$level++;
				if ($level>1) $revplace .= ", ";
				if ($place=="") $revplace .= $pgv_lang["unknown"];
				else $revplace .= $place;
			}
			if (begRTLText($revplace))
			     print "<li class=\"rtl\" dir=\"rtl\"";
		    else print "<li class=\"ltr\" dir=\"ltr\"";
			print " type=\"square\"><a href=\"?action=show&amp;display=hierarchy&amp;level=$level$linklevels\">";
			print PrintReady($revplace)."</a></li>\n";
			$i++;
			if ($ct > 20){
				if ($i == floor($ct / 3)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
				if ($i == floor(($ct / 3) * 2)) print "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
			}
			else if ($i == floor($ct/2)) print "</ul></td><td class=\"list_value_wrap\"><ul>\n\t\t\t";
		}
		print "\n\t\t</ul></td></tr>\n\t\t";
		if ($i>1) {
			print "<tr><td>";
			if ($i>0) print $pgv_lang["total_unic_places"]." ".$i;
			print "</td></tr>\n";
		}
		print "\n\t\t</table>";
	}
}

print "<br /><a href=\"?display=";
if ($display=="list") print "hierarchy\">".$pgv_lang["show_place_hierarchy"];
else print "list\">".$pgv_lang["show_place_list"];
print "</a><br /><br />\n";
if ($hasplaceform) {
	$placeheader = substr($header, $hasplaceform);
	$ct = preg_match("/2 FORM (.*)/", $placeheader, $match);
	if ($ct>0) {
		print  $pgv_lang["form"].$match[1];
		print_help_link("ppp_match_one_help", "qm");
	}
}
else {
	print $pgv_lang["form"].$pgv_lang["default_form"]."  ".$pgv_lang["default_form_info"];
	print_help_link("ppp_default_form_help", "qm");
}

print "<br /><br /></div>";
print_footer();
if ($use_googlemap && $display=="hierarchy") map_scripts($numfound, $level, $levelm, $levelo, $linklevels, $placelevels);
?>

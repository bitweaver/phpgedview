<?php
/**
 * Displays a place hierachy
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team. All rights reserved.
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
 * @version $Id: placelist.php,v 1.8 2010/02/08 21:27:24 wjames5 Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../kernel/setup_inc.php' );

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
$action = safe_GET('action');
$display = safe_GET('display');
$parent = safe_GET('parent', PGV_REGEX_UNSAFE);
$level = safe_GET('level');

if (empty($action)) $action = "find";
if (empty($display)) $display = "hierarchy";

if (!isset($GOOGLEMAP_ENABLED) || $GOOGLEMAP_ENABLED == "false" || (!isset($GOOGLEMAP_PLACE_HIERARCHY) || $GOOGLEMAP_PLACE_HIERARCHY == "false")) {
	$use_googlemap = false;
}
else $use_googlemap = true;

if ($display=="hierarchy") print_header($pgv_lang["place_list"]);
else print_header($pgv_lang["place_list2"]);

echo "\n\t<div class=\"center\">";
if ($display=="hierarchy") echo "<h2>".$pgv_lang["place_list"]."</h2>\n\t";
else echo "<h2>".$pgv_lang["place_list2"]."</h2>\n\t";

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

//-- extract the place form encoded in the gedcom
$header = find_gedcom_record("HEAD");
$hasplaceform = strpos($header, "1 PLAC");

//-- hierarchical display
if ($display=="hierarchy") {
	$placelist=get_place_list($parent, $level);
	$numfound=count($placelist);
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

	// -- echo the breadcrumb hierarchy
	$numls=0;
	if ($level>0) {
		//-- link to search results
		if ((($level>1)||($parent[0]!=""))&&($numfound>0)) {
			echo $numfound." ".$pgv_lang["connections"].": ";
		}
		//-- breadcrumb
		$numls = count($parent)-1;
		$num_place="";
		//-- place and page text orientation is opposite -> top level added at the beginning of the place text
		echo "<a href=\"?level=0\">";
		if ($numls>=0 && (($TEXT_DIRECTION=="ltr" && hasRtLText($parent[$numls])) || ($TEXT_DIRECTION=="rtl" && !hasRtLText($parent[$numls])))) echo $pgv_lang["top_level"].", ";
		echo "</a>";
			for($i=$numls; $i>=0; $i--) {
			echo "<a href=\"?level=".($i+1);
			for ($j=0; $j<=$i; $j++) {
				$levels = explode(',', trim($parent[$j]));
				// Routine for replacing ampersands
				foreach($levels as $pindex=>$ppart) {
					$ppart = rawurlencode($ppart);
					$ppart = preg_replace("/amp\%3B/", "", trim($ppart));
					echo "&amp;parent[$j]=".$ppart;
				}
			}
 			echo "\">";
 			if (trim($parent[$i])=="") echo $pgv_lang["unknown"];
			else echo PrintReady($parent[$i]);
			echo "</a>";
 			if ($i>0) echo ", ";
 			else if (($TEXT_DIRECTION=="rtl" && hasRtLText($parent[$i])) || ($TEXT_DIRECTION=="ltr" &&  !hasRtLText($parent[$i])))  echo ", ";
			if (empty($num_place)) $num_place=$parent[$i];
		}
	}
	echo "<a href=\"?level=0\">";
	//-- place and page text orientation is the same -> top level added at the end of the place text
	if ($level==0 || ($numls>=0 && (($TEXT_DIRECTION=="rtl" && hasRtLText($parent[$numls])) || ($TEXT_DIRECTION=="ltr" && !hasRtLText($parent[$numls]))))) echo $pgv_lang["top_level"];
	echo "</a>";

	print_help_link("ppp_levels_help", "qm");

	if ($use_googlemap)
		create_map();
	else {
		// show clickable map if found
		echo "\n\t<br /><br />\n\t<table class=\"width90\"><tr><td class=\"center\">";
		if ($level>=1 and $level<=3) {
			$country = $parent[0];
			if ($country == "\xD7\x99\xD7\xA9\xD7\xA8\xD7\x90\xD7\x9C") $country = "ISR"; // Israel hebrew name
			$country = UTF8_strtoupper($country);
			if (strlen($country)!=3) {
				// search country code using current language countries table
				loadLangFile('pgv_country');
				foreach ($countries as $countrycode => $countryname) {
					if (UTF8_strtoupper($countryname) == $country) {
						$country = $countrycode;
						break;
					}
				}
			}
			$mapname = $country;
			$areaname = $parent[0];
			$imgfile = "places/".$country."/".$mapname.".gif";
			$mapfile = "places/".$country."/".$country.".".$lang_short_cut[$LANGUAGE].".htm";
			if (!file_exists($mapfile)) $mapfile = "places/".$country."/".$country.".htm";
			if ($level>1) {
				$state = $parent[1];
				$mapname .= "_".$state;
				if ($level>2) {
					$county = $parent[2];
					$mapname .= "_".$county;
					$areaname = str_replace("'","\'",$parent[2]);
				}
				else {
					$areaname = str_replace("'","\'",$parent[1]);
				}
				// Transform certain two-byte UTF-8 letters with diacritics
				// to their 1-byte ASCII analogues without diacritics
				$mapname = str_replace(array("Ę", "Ó", "Ą", "Ś", "Ł", "Ż", "Ź", "Ć", "Ń", "ę", "ó", "ą", "ś", "ł", "ż", "ź", "ć", "ń"), array("E", "O", "A", "S", "L", "Z", "Z", "C", "N", "e", "o", "a", "s", "l", "z", "z", "c", "n"), $mapname);
				$mapname = str_replace(array("Š", "Œ", "Ž", "š", "œ", "ž", "Ÿ", "¥", "µ", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "ß", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "ÿ"), array("S", "O", "Z", "s", "o", "z", "Y", "Y", "u", "A", "A", "A", "A", "A", "A", "A", "C", "E", "E", "E", "E", "I", "I", "I", "I", "D", "N", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "Y", "s", "a", "a", "a", "a", "a", "a", "a", "c", "e", "e", "e", "e", "i", "i", "i", "i", "o", "n", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "y", "y"), $mapname);
				// Transform apostrophes and blanks to dashes
				$mapname = str_replace(array("'", " "), "-", $mapname);
				$imgfile = "places/".$country."/".$mapname.".gif";
			}
			if (file_exists($imgfile) and file_exists($mapfile)) {
				include ($mapfile);
				echo "<img src='".$imgfile."' usemap='#".$mapname."' border='0' alt='".$areaname."' title='".$areaname."' />";
				?>
				<script type="text/javascript" src="js/strings.js"></script>
				<script type="text/javascript">
				<!--
				//copy php array into js array
				var places_accept = new Array(<?php foreach ($placelist as $key => $value) echo "'".str_replace("'", "\'", $value)."',"; echo "''";?>)
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
					if (places_accept.in_array(search)) return(location.href = '?level=2<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]="?>'+search);
					// search without optional code [California]
					txt = txt.replace(/(\/)/,' ('); // case: finnish/swedish ==> finnish (swedish)
					p=txt.indexOf(' (');
					if (p>1) search=txt.substring(0,p);
					else return;
					if (places_accept.in_array(search)) return(location.href = '?level=2<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]="?>'+search);
					// search with code only [CA]
					search=txt.substring(p+2);
					p=search.indexOf(')');
					if (p>1) search=search.substring(0,p);
					if (places_accept.in_array(search)) return(location.href = '?level=2<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]="?>'+search);
				}
				function setPlaceCounty(txt) {
					if (txt=='') return;
					var search = txt;
					if (places_accept.in_array(search)) return(location.href = '?level=3<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]="?>'+search);
					txt = txt.replace(/(\/)/,' (');
					p=txt.indexOf(' (');
					if (p>1) search=txt.substring(0,p);
					else return;
					if (places_accept.in_array(search)) return(location.href = '?level=3<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]="?>'+search);
					search=txt.substring(p+2);
					p=search.indexOf(')');
					if (p>1) search=search.substring(0,p);
					if (places_accept.in_array(search)) return(location.href = '?level=3<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]="?>'+search);
				}
				function setPlaceCity(txt) {
					if (txt=='') return;
					var search = txt;
					if (places_accept.in_array(search)) return(location.href = '?level=4<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]=".urlencode(@$parent[2])."&parent[3]="?>'+search);
					txt = txt.replace(/(\/)/,' (');
					p=txt.indexOf(' (');
					if (p>1) search=txt.substring(0,p);
					else return;
					if (places_accept.in_array(search)) return(location.href = '?level=4<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]=".urlencode(@$parent[2])."&parent[3]="?>'+search);
					search=txt.substring(p+2);
					p=search.indexOf(')');
					if (p>1) search=search.substring(0,p);
					if (places_accept.in_array(search)) return(location.href = '?level=4<?php echo "&parent[0]=".urlencode($parent[0])."&parent[1]=".urlencode(@$parent[1])."&parent[2]=".urlencode(@$parent[2])."&parent[3]="?>'+search);
				}
				//-->
				</script>
				<?php
				echo "</td><td style=\"margin-left:15; vertical-align: top;\">";
			}
		}
	}

	//-- create a string to hold the variable links and place names
	$linklevels="";
	if ($use_googlemap) {
		$placelevels="";
		$place_names=array();
	}
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

	// -- echo the array
	foreach ($placelist as $key => $value) {
		if ($i==0) {
			echo "\n\t<table class=\"list_table $TEXT_DIRECTION\"";
			if ($TEXT_DIRECTION=="rtl") echo " dir=\"rtl\"";
			echo ">\n\t\t<tr>\n\t\t<td class=\"list_label\" ";
			if ($ct1 > 20) echo "colspan=\"3\"";
			else if ($ct1 > 4) echo "colspan=\"2\"";
			echo ">&nbsp;";
			echo "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" border=\"0\" title=\"".$pgv_lang["search_place"]."\" alt=\"".$pgv_lang["search_place"]."\" />&nbsp;&nbsp;";
			if ($level>0) {
				echo " ".$pgv_lang["place_list_aft"].": ";
				echo PrintReady($num_place);
			}
			else echo $pgv_lang["place_list"];

			echo "&nbsp;";
			print_help_link("ppp_placelist_help", "qm", "place_list");
			echo "</td></tr><tr><td class=\"list_value\"><ul>\n\t\t\t";
		}

		if (begRTLText($value))
			echo "<li class=\"rtl\" dir=\"rtl\"";
		else echo "<li class=\"ltr\" dir=\"ltr\"";
		echo " type=\"square\">\n<a href=\"?action=$action&amp;level=".($level+1).$linklevels;
		echo "&amp;parent[$level]=".urlencode($value)."\" class=\"list_item\">";

		if (trim($value)=="") echo $pgv_lang["unknown"];
		else echo PrintReady($value);
		if ($use_googlemap) $place_names[$i]=trim($value);
		echo "</a></li>\n";
		if ($ct1 > 20){
			if ($i == floor($ct1 / 3)) echo "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
			if ($i == floor(($ct1 / 3) * 2)) echo "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
		}
		else if ($ct1 > 4 && $i == floor($ct1 / 2)) echo "\n\t\t</ul></td>\n\t\t<td class=\"list_value\"><ul>";
		$i++;
	}
	if ($i>0){
		echo "\n\t\t</ul></td></tr>";
		if (($action!="show")&&($level>0)) {
			echo "<tr>\n\t\t<td class=\"list_label\" ";
			if ($ct1 > 20) echo "colspan=\"3\"";
			else if ($ct1 > 4) echo "colspan=\"2\"";
			echo ">\n\t";
			echo $pgv_lang["view_records_in_place"];
			print_help_link("ppp_view_records_help", "qm", "view_records_in_place");
			echo "</td></tr><tr><td class=\"list_value\" ";
			if ($ct1 > 20) echo "colspan=\"3\"";
			else if ($ct1 > 4) echo "colspan=\"2\"";
			echo " style=\"text-align: center;\">";
			echo "<a href=\"?action=show&amp;level=$level";
			foreach($parent as $key=>$value) {
				echo "&amp;parent[$key]=".urlencode(trim($value));
			}
			echo "\"><span class=\"formField\">";
			if (trim($value)=="") echo $pgv_lang["unknown"];
			else echo PrintReady($value);
			echo "</span></a> ";
			echo "</td></tr>";
		}
		echo "</table>";
	}
	echo "</td></tr></table>";
}

$positions = get_place_positions($parent, $level);
if ($level > 0) {
	if ($action=="show") {
		// -- array of names
		$myindilist = array();
		$mysourcelist = array();
		$myfamlist = array();
		foreach ($positions as $position) {
			$record=GedcomRecord::getInstance($position);
			switch ($record->getType()) {
			case 'INDI':
				$myindilist[]=$record;
				break;
			case 'SOUR':
				$mysourcelist[]=$record;
				break;
			case 'FAM':
				$myfamlist[]=$record;
				break;
			}
		}
		echo "<br />";
		$title = ""; foreach ($parent as $k=>$v) $title = $v.", ".$title;
		$title = PrintReady(substr($title, 0, -2))." ";
		// Sort each of the tables by Name
		usort($myindilist,   array('GedcomRecord', 'Compare'));
		usort($myfamlist,    array('GedcomRecord', 'Compare'));
		usort($mysourcelist, array('GedcomRecord', 'Compare'));
		// echo each of the tables
		print_indi_table($myindilist,   $pgv_lang['individuals'].' @ '.$title);
		print_fam_table ($myfamlist,    $pgv_lang['families'   ].' @ '.$title);
		print_sour_table($mysourcelist, $pgv_lang['sources'    ].' @ '.$title);
	}
}

//-- list type display
if ($display=="list") {
	$placelist = array();

	$placelist=find_place_list("");
	$placelist = array_unique($placelist);
	uasort($placelist, "stringsort");
	if (count($placelist)==0) {
		echo "<b>".$pgv_lang["no_results"]."</b><br />";
	}
	else {
		echo "\n\t<table class=\"list_table $TEXT_DIRECTION\"";
		if ($TEXT_DIRECTION=="rtl") echo " dir=\"rtl\"";
		echo ">\n\t\t<tr>\n\t\t<td class=\"list_label\" ";
		$ct = count($placelist);
		echo " colspan=\"".($ct>20?"3":"2")."\">&nbsp;";
		echo "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]."\" border=\"0\" title=\"".$pgv_lang["search_place"]."\" alt=\"".$pgv_lang["search_place"]."\" />&nbsp;&nbsp;";
		echo $pgv_lang["place_list2"];
		echo "&nbsp;";
		print_help_link("ppp_placelist_help2", "qm");
		echo "</td></tr><tr><td class=\"list_value_wrap\"><ul>\n\t\t\t";
		$i=0;
		foreach($placelist as $indexval => $revplace) {
			$linklevels = "";
			$levels = explode(',', $revplace);	// -- split the place into comma seperated values
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
				echo "<li class=\"rtl\" dir=\"rtl\"";
			else echo "<li class=\"ltr\" dir=\"ltr\"";
			echo " type=\"square\"><a href=\"?action=show&amp;display=hierarchy&amp;level=$level$linklevels\">";
			echo PrintReady($revplace)."</a></li>\n";
			$i++;
			if ($ct > 20){
				if ($i == floor($ct / 3)) echo "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
				if ($i == floor(($ct / 3) * 2)) echo "\n\t\t</ul></td>\n\t\t<td class=\"list_value_wrap\"><ul>";
			}
			else if ($i == floor($ct/2)) echo "</ul></td><td class=\"list_value_wrap\"><ul>\n\t\t\t";
		}
		echo "\n\t\t</ul></td></tr>\n\t\t";
		if ($i>1) {
			echo "<tr><td>";
			if ($i>0) echo $pgv_lang["total_unic_places"]." ".$i;
			echo "</td></tr>\n";
		}
		echo "\n\t\t</table>";
	}
}

echo "<br /><a href=\"?display=";
if ($display=="list") echo "hierarchy\">".$pgv_lang["show_place_hierarchy"];
else echo "list\">".$pgv_lang["show_place_list"];
echo "</a><br /><br />\n";
if ($hasplaceform) {
	$placeheader = substr($header, $hasplaceform);
	$ct = preg_match("/2 FORM (.*)/", $placeheader, $match);
	if ($ct>0) {
		echo  $pgv_lang["form"].$match[1];
		print_help_link("ppp_match_one_help", "qm");
	}
}
else {
	echo $pgv_lang["form"].$pgv_lang["default_form"]."  ".$pgv_lang["default_form_info"];
	print_help_link("ppp_default_form_help", "qm");
}
echo "<br /><br /></div>";
print_footer();
if ($use_googlemap && $display=="hierarchy") map_scripts($numfound, $level, $parent, $linklevels, $placelevels, $place_names);
?>

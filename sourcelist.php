<?php
/**
 * Parses gedcom file and displays a list of the sources in the file.
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
 * @version $Id: sourcelist.php,v 1.3 2006/10/30 15:00:45 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
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
require("config.php");
require_once("includes/functions_print_lists.php");
$addsourcelist = get_source_add_title_list();  //-- array of additional source titlesadd
$sourcelist = get_source_list();               //-- array of regular source titles 

uasort($sourcelist, "itemsort"); 
uasort($addsourcelist, "itemsort"); 

$ca = count($addsourcelist);
$cs = get_list_size("sourcelist");
$ctot = $ca + $cs;
print_header($pgv_lang["source_list"]);
print "<div class=\"center\">";
print "<h2>".$pgv_lang["source_list"]."</h2>\n\t";

print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr><td class=\"list_label\"";
if($ca>0 || $cs>12)	print " colspan=\"2\"";
print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["source"]["small"]."\" border=\"0\" title=\"".$pgv_lang["sources"]."\" alt=\"".$pgv_lang["sources"]."\" />&nbsp;&nbsp;";
print $pgv_lang["titles_found"];
print_help_link("sourcelist_listbox_help", "qm");
print "</td></tr><tr><td class=\"$TEXT_DIRECTION list_value_wrap";
if($ca>0 || $cs>12)	print " width50";
print "\"><ul>";
$i=1;
if ($cs>0){
	// -- print the array
	foreach ($sourcelist as $key => $value) {
		print_list_source($key, $value);
		if ($i==ceil($ctot/2) && $ctot>12) {
			print "</ul></td><td class=\"list_value_wrap";
			if($ca>0 || $cs>12)	print " width50";
			print "\"><ul>\n";
		}
		$i++;
	}
	$tot_sources = count($source_total);
	$source_total = array();

	if ($ca>0) {
		// -- print the additional array
		foreach ($addsourcelist as $key => $value) {
		print_list_source($key, $value);
		if ($i==ceil($ctot/2) && $ctot>12) {
			print "</ul></td><td class=\"list_value_wrap";
			if($ca>0 || $cs>12)	print " width50";
			print "\"><ul>\n";
		}
		$i++;
		}
	}

	print "\n\t\t</ul></td>\n\t\t";
 
	print "</tr><tr><td class=\"center\" colspan=\"2\">".$pgv_lang["total_sources"]." ".$tot_sources."<br />";
	if (count($source_total) != 0) print $pgv_lang["titles_found"]."&nbsp;".(count($source_total)+$tot_sources);
	if (count($source_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($source_hide);
}
else print "<span class=\"warning\"><i>".$pgv_lang["no_results"]."</span>";

print "</td>\n\t\t</tr>\n\t</table>";

print "</div>";
print "<br /><br />";
print_footer();
?>
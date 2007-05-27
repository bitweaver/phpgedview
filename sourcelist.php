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
 * @version $Id: sourcelist.php,v 1.4 2007/05/27 14:45:29 lsces Exp $
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

print_header($pgv_lang["source_list"]);

$addsourcelist = get_source_add_title_list();  //-- array of additional source titlesadd
$sourcelist = get_source_list();               //-- array of regular source titles

uasort($sourcelist, "itemsort");
uasort($addsourcelist, "itemsort");

$ca = count($addsourcelist);
$cs = count($sourcelist);
$ctot = $ca + $cs;

print "<div class=\"center\">";
print "<h2>".$pgv_lang["source_list"]."</h2>\n\t";

print_sour_table(array_merge($sourcelist, $addsourcelist));

print "</div>";
print "<br /><br />";
load_behaviour();
print_footer();
?>
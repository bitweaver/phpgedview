<?php
/**
* A landing spot for pages that are restricted from search engines.
* WARNING: The functions print_header() and print_simple_header()
* cannot be called from here because they would cause an infinite
* back to here.
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
* Author: Mike Elliott (coloredpixels)
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
* This Page Is Valid XHTML 1.0 Transitional! > 21 August 2005
*
* @package PhpGedView
* @version $Id$
*/

require "config.php";

loadLangFile('pgv_help');

if (!isset($help)) $help = "";
require ("includes/help_text_vars.php");

header("Content-Type: text/html; charset=$CHARACTER_SET");

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=', $CHARACTER_SET, '" />';
echo '<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';
if ($rtl_stylesheet && $TEXT_DIRECTION=='rtl') {
	echo '<link rel="stylesheet" href="', $rtl_stylesheet, '" type="text/css" media="all" />';
}
echo '<meta name="robots" content="noindex,follow" />';
echo '<meta name="generator" content="', PGV_PHPGEDVIEW, ' - ', PGV_PHPGEDVIEW_URL, '" />';
echo '<title>'. $pgv_lang['label_search_engine_detected'], '</title>';
echo '</head><body>';
echo '<div class="helptext">', $pgv_lang['search_engine_landing_page'];
if ($SEARCH_SPIDER) {
	echo '<br /><br />', $pgv_lang['label_search_engine_detected'], ': ', $SEARCH_SPIDER, '<br />';
}
echo '</div><br />';

// List of indis from each gedcom
$all_gedcoms=get_all_gedcoms();
if ($ALLOW_CHANGE_GEDCOM && count($all_gedcoms)>1) {
	foreach ($all_gedcoms as $ged_id=>$gedcom) {
		$title=$pgv_lang['welcome_page'].' - '.PrintReady(get_gedcom_setting($ged_id, 'title'));
		echo '<a href="', encode_url("index.php?ged={$gedcom}"), '"><b>', $title, '</b></a><br />';
	}
	echo '<br />';
	foreach ($all_gedcoms as $ged_id=>$gedcom) {
		$title=$pgv_lang['individual_list'].' - '.PrintReady(get_gedcom_setting($ged_id, 'title'));
		echo '<a href="', encode_url("indilist.php?ged={$gedcom}"), '"><b>', $title, '</b></a><br />';
	}
} else {
	$title=$pgv_lang['welcome_page'];
	echo '<a href="', encode_url("index.php?ged={$GEDCOM}"), '"><b>', $title, '</b></a><br />';
	$title=$pgv_lang['individuals'];
	echo '<a href="', encode_url("indilist.php?ged={$GEDCOM}"), '"><b>', $title, '</b></a><br />';
}

echo '</body></html>';
?>

<?php
/**
 * A landing spot for pages that are restricted from search engines.
 * WARNING: The functions print_header() and print_simple_header() 
 * cannot be called from here because they would cause an infinite 
 * back to here.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
global $SEARCH_SPIDER, $CHARACTER_SET;
global $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $DEFAULT_GEDCOM;

require "config.php";
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];
require $helptextfile["english"];
if (file_exists($helptextfile[$LANGUAGE])) require $helptextfile[$LANGUAGE];

if (!isset($help)) $help = "";
require ("help_text_vars.php");

header("Content-Type: text/html; charset=$CHARACTER_SET");

print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head>\n\t";
print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$CHARACTER_SET\" />\n\t";

print "<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\" media=\"all\"></link>\n\t";
if ((!empty($rtl_stylesheet))&&($TEXT_DIRECTION=="rtl")) 
	print "<link rel=\"stylesheet\" href=\"$rtl_stylesheet\" type=\"text/css\" media=\"all\"></link>\n\t";
print "<meta name=\"robots\" content=\"noindex,follow\" />\n\t";
print "<meta name=\"generator\" content=\"PhpGedView v$VERSION - http://www.phpgedview.net\" />\n";
print "<title>".$pgv_lang['label_search_engine_detected']."</title>\n";
print "</head>\n<body>";

print "<div class=\"helptext\">\n";

print $pgv_lang['search_engine_landing_page'];

if(!empty($SEARCH_SPIDER)) {
	print "<br /><br />".$pgv_lang['label_search_engine_detected'].": ";
	print $SEARCH_SPIDER."\n<br />\n";
	}

print "\n</div>\n<br />";
print "<a href=\"index.php\"><b>".$pgv_lang["welcome_page"]."</b></a><br />";

// Doesn't act like its supposed to, but does force the default gedcom,
// instead of reading from an inderminate session file.
$link = "indilist.php?ged=$GEDCOM";
print "<a href=\"".$link."\"><b>".$pgv_lang["individuals"]."</b></a><br />";

//-- gedcom list
if ($ALLOW_CHANGE_GEDCOM && count($GEDCOMS)>1) {
	foreach($GEDCOMS as $ged=>$gedarray) {
		$name = $pgv_lang["individual_list"]." - ".PrintReady($gedarray["title"]);
		print "<a href=\"indilist.php?ged=".$ged."\"><b>".$name."</b></a><br />";
	}
}

print "<br /><a href=\"javascript:;\" onclick=\"window.close();\"><b>".$pgv_lang["close_window"]."</b></a>";
print "</body>\n</html>\n";
?>

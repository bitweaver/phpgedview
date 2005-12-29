<?php
/**
 * English Language Configure Help file for PHPGedView
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
 * @version $Id: editconfig_help.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 * @package PhpGedView
 * @subpackage Admin
 */
require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
require $PGV_BASE_DIRECTORY.$helptextfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];
require ("help_text_vars.php");
print_simple_header($pgv_lang["config_help"]);
print '<span class="helpheader">';
print_text("config_help");
print '</span><br /><br /><span class="helptext">';
if ($help == "help_contents_help") {
		if (userIsAdmin(getUserName())) {
		$help = "admin_help_contents_help";
		print_text("admin_help_contents_head_help");
	}
	else print_text("help_contents_head_help");
	print_help_index($help);
}
else {
	if ($help == "help_uploadgedcom.php") $help = "help_addgedcom.php";
print_text($help);
}
print "</span><br /><br />";
print "<a href=\"help_text.php?help=help_contents_help\"><b>";
print_text("help_contents");
print "</b></a><br />";
print "<a href=\"javascript:;\" onclick=\"window.close();\"><b>";
print_text("close_window");
print "</b></a>";
print_simple_footer();
?>

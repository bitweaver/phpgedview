<?php
/**
* Menu Extension
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2007 to 2009 PGV Development Team. All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* @package PhpGedView
* @subpackage Themes
* @version $Id$
*/
/*
* This is a sample customizable sub-menu for the Welcome menu in the top links of each page.
*
* To make this sub-menu appear within the Welcome menu, this file needs to be named
* "custom_welcome_menu.php". Furthermore, the individual sub-menu entries need to
* be valid. You can remove the extra comments but, for copyright reasons, the first comment
* block at the top of this file should be retained.
*
* Use the code in "includes/classes/class_menubar.php" as a guide to how valid menus and sub-menus
* should be constructed.
*/
/*
* Explanation of the '$submenu = new Menu("whatever 1", "whatever 2")' line:
* 'whatever 1' is the text that is to appear for this sub-menu entry. If you code this as
* shown, the text will appear exactly as entered no matter what the page language is.
*
* If you replace the '"whatever 1"' (replace the quotation marks too) with something like
* '$pgv_lang["whatever_1"]' (don't enter the apostrophes), you can then make the text vary
* according to the page language. You should put your English text into file
* "languages/extra.en.php" like this:
* $pgv_lang["whatever_1"] = "My submenu title 1";
*
* Similar entries should appear in each of the other "languages/extra.xx.php" files, where
* "xx" corresponds to the language (Dutch is "nl", French is "fr", German is "de" etc.)
* You should have a "languages/extra.xx.php" file for each of the languages your site
* supports. You don't need these files for unsupported languages.
*
* If the "languages/extra.xx.php" (including the English version) file doesn't exist, you
* can create your own by copying the "languages/lang.xx.php" file and giving it the new
* name. Delete all the existing $pgv_lang["xxxx"] entries, and add your own.
*
* When PhpGedView can't find the desired "languages/extra.xx.php" file or the desired text
* within that file, the English version will be used. If the desired text doesn't exist
* anywhere, you will see an error message instead. This is probably not desirable.
*
*
* 'whatever 2' is the URL required to launch the desired module, web site, or PhpGedView
* script. You need to provide all of the input parameters or variables that the script
* needs. For example, to get to the Yahoo web site, you'd replace '"whatever 2"' with
* '"http://www.yahoo.com"'. Note that the URL you enter here is enclosed in quotation marks.
*
* If the URL requires something enclosed in quotation marks, you should precede each of them
* with a backslash or enclose the entire URL in apostrophes instead of quotation marks.
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Menu separator line
$menu->addSeparator();

// First sub-menu (visible even when Search robots are looking at the site)
$submenu = new Menu("Custom Menu Item 1", "custom link #1");
$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
$menu->addSubmenu($submenu);

// Second sub-menu (invisible to Search robots)
if (empty($SEARCH_SPIDER)) {
	$submenu = new Menu("Custom Menu Item 2", "custom link #2");
	$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
	$menu->addSubmenu($submenu);
}

// Third sub-menu (visible only to users with site Admin rights)
if (PGV_USER_IS_ADMIN) {
	$submenu = new Menu("Custom Menu Item 3", "custom link #2");
	$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
	$menu->addSubmenu($submenu);
}

// Fourth sub-menu (visible only to users with GEDCOM Admin rights)
if (PGV_USER_GEDCOM_ADMIN) {
	$submenu = new Menu("Custom Menu Item 4", "custom link #2");
	$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
	$menu->addSubmenu($submenu);
}

// Fifth sub-menu (visible only Clippings Cart is enabled and not a Search robot)
if (empty($SEARCH_SPIDER) && $GLOBALS["ENABLE_CLIPPINGS_CART"]) {
	$submenu = new Menu("Custom Menu Item 5", "custom link #2");
	$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
	$menu->addSubmenu($submenu);
}

?>

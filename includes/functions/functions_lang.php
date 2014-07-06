<?php
/**
 *
 * Language specific functions
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 * @package PhpGedView
 * @subpackage DB
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_LANG_PHP', '');

/**
 * Takes a string and converts certain characters in the string to others for the purpose of soundex searches
 */
function Character_Substitute($input)
{
	$stringsToReplace = array("/AE/", "/ae/", "/OE/", "/oe/", "/UE/", "/ue/", "/ss/");
	$replacements =     array("Ä",   "Ä",   "Ö",   "ö",   "Ü",   "ü",   "ß");

	preg_replace($stringsToReplace, $replacements, $input);
}
?>

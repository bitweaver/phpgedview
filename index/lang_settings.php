<?php
/**
 * Standard file of language_settings.php
 *
 * -> NEVER manually delete or edit this file <-
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License;or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not;write to the Free Software
 * Foundation;Inc.;59 Temple Place;Suite 330;Boston;MA  02111-1307  USA
 *
 * $Id$
 *
 * @package PhpGedView
 * @subpackage Languages
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

//-- NEVER manually delete or edit this entry and every line below this entry! --START--//
// REPLACE BY bitweaver Language functions !!!!!!!!!!!!!!!!!!!!!!!!!

// Array definition of language_settings
$language_settings = array();

//-- settings for english
$lang = array();
$lang["pgv_langname"]    = "english";
$lang["pgv_lang_use"]    = true;
$lang["pgv_lang"]    = "English";
$lang["lang_short_cut"]    = "en";
$lang["langcode"]    = "en;en-us;en-au;en-bz;en-ca;en-ie;en-jm;en-nz;en-ph;en-za;en-tt;en-gb;en-zw;";
$lang["pgv_language"]    = "languages/lang.en.php";
$lang["confighelpfile"]    = "languages/configure_help.en.php";
$lang["helptextfile"]    = "languages/help_text.en.php";
$lang["flagsfile"]    = "../users/icons/flags/Great_Britain.gif.gif";
$lang["factsfile"]    = "languages/facts.en.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyz";
$language_settings["english"]  = $lang;


//-- NEVER manually delete or edit this entry and every line above this entry! --END--//

?>
<?php
/**
 * Standard file of language_settings.php
 *
 * -> NEVER manually delete or edit this file <-
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * $Id: lang_settings_std.php,v 1.6 2008/07/07 17:30:14 lsces Exp $
 *
 * @package PhpGedView
 * @subpackage Languages
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

//-- NEVER manually delete or edit this entry and every line below this entry! --START--//

// Array definition of language_settings
$language_settings = array();

//-- settings for english
$language_settings['english']=array(
'pgv_langname'=>'english',
'pgv_lang_use'=>true,
'pgv_lang'=>'English',
'lang_short_cut'=>'en',
'langcode'=>'en;en-us;en-au;en-bz;en-ca;en-ie;en-jm;en-nz;en-ph;en-za;en-tt;en-gb;en-zw;',
'pgv_language'=>'languages/lang.en.php',
'confighelpfile'=>'languages/configure_help.en.php',
'helptextfile'=>'languages/help_text.en.php',
'flagsfile'=>'images/flags/usa.gif',
'factsfile'=>'languages/facts.en.php',
'adminfile'=>'languages/admin.en.php',
'editorfile'=>'languages/editor.en.php',
'countryfile'=>'languages/countries.en.php',
'faqlistfile'=>'languages/faqlist.en.php',
'extrafile'=>'languages/extra.en.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'DICTIONARY_SORT'=>true
);
?>

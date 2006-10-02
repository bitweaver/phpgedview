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
 * $Id: lang_settings_std.php,v 1.3 2006/10/02 12:48:41 lsces Exp $
 *
 * @package PhpGedView
 * @subpackage Languages
 */

//-- NEVER manually delete or edit this entry and every line below this entry! --START--//

// Array definition of language_settings
$language_settings = array();

//-- settings for czech
$lang = array();
$lang["pgv_langname"]    = "czech";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Čeština";
$lang["lang_short_cut"]    = "cz";
$lang["langcode"]    = "cs;cz;";
$lang["pgv_language"]    = "languages/lang.cz.php";
$lang["confighelpfile"]    = "languages/configure_help.cz.php";
$lang["helptextfile"]    = "languages/help_text.cz.php";
$lang["flagsfile"]    = "../users/icons/flags/Czech_Republic.gif";
$lang["factsfile"]    = "languages/facts.cz.php";
$lang["DATE_FORMAT"]    = "D. M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AÁBCČDĎEĚÉFGHIÍJKLMNŇOÓPQRŘSŠTŤUÚŮVWXYÝZŽ";
$lang["ALPHABET_lower"]    = "aábcčdďeěéfghiíjklmnňoópqrřsštťuúůvwxyýzž";
$lang["MULTI_LETTER_ALPHABET"]	= "ch";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["czech"]  = $lang;

//-- settings for german
$lang = array();
$lang["pgv_langname"]    = "german";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Deutsch";
$lang["lang_short_cut"]    = "de";
$lang["langcode"]    = "de;de-de;de-at;de-li;de-lu;de-ch;";
$lang["pgv_language"]    = "languages/lang.de.php";
$lang["confighelpfile"]    = "languages/configure_help.de.php";
$lang["helptextfile"]    = "languages/help_text.de.php";
$lang["flagsfile"]    = "../users/icons/flags/Germany.gif";
$lang["factsfile"]    = "languages/facts.de.php";
$lang["DATE_FORMAT"]    = "D. M Y";
$lang["TIME_FORMAT"]    = "H:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AÄBCDEFGHIJKLMNOÖPQRSßTUÜVWXYZ";
$lang["ALPHABET_lower"]    = "aäbcdefghijklmnoöpqrsßtuüvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["german"]  = $lang;

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
$lang["flagsfile"]    = "../users/icons/flags/United_States.gif";
$lang["factsfile"]    = "languages/facts.en.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["english"]  = $lang;

//-- settings for spanish
$lang = array();
$lang["pgv_langname"]    = "spanish";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Español";
$lang["lang_short_cut"]    = "es";
$lang["langcode"]    = "es;es-bo;es-cl;es-co;es-cr;es-do;es-ec;es-sv;es-gt;es-hn;es-mx;es-ni;es-pa;es-py;es-pe;es-pr;es-us;es-uy;es-ve;";
$lang["pgv_language"]    = "languages/lang.es.php";
$lang["confighelpfile"]    = "languages/configure_help.es.php";
$lang["helptextfile"]    = "languages/help_text.es.php";
$lang["flagsfile"]    = "../users/icons/flags/Spain.gif";
$lang["factsfile"]    = "languages/facts.es.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnñopqrstuvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["spanish"]  = $lang;

//-- settings for spanish-ar
$lang = array();
$lang["pgv_langname"]    = "spanish-ar";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Español Latinoamericano";
$lang["lang_short_cut"]    = "es-ar";
$lang["langcode"]    = "es-ar;";
$lang["pgv_language"]    = "languages/lang.es-ar.php";
$lang["confighelpfile"]    = "languages/configure_help.es-ar.php";
$lang["helptextfile"]    = "languages/help_text.es-ar.php";
$lang["flagsfile"]    = "../users/icons/flags/Argentina.gif";
$lang["factsfile"]    = "languages/facts.es-ar.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnñopqrstuvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["spanish-ar"]  = $lang;

//-- settings for french
$lang = array();
$lang["pgv_langname"]    = "french";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Français";
$lang["lang_short_cut"]    = "fr";
$lang["langcode"]    = "fr;fr-be;fr-ca;fr-lu;fr-mc;fr-ch;";
$lang["pgv_language"]    = "languages/lang.fr.php";
$lang["confighelpfile"]    = "languages/configure_help.fr.php";
$lang["helptextfile"]    = "languages/help_text.fr.php";
$lang["flagsfile"]    = "../users/icons/flags/France.gif";
$lang["factsfile"]    = "languages/facts.fr.php";
$lang["DATE_FORMAT"]    = "D j F Y";
$lang["TIME_FORMAT"]    = "H:i:s";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AÀÂÆBCÇDEÉÈËÊFGHIÏÎJKLMNOÔŒPQRSTUÙÛVWXYZ";
$lang["ALPHABET_lower"]    = "aàâæbcçdeéèëêfghiïîjklmnoôœpqrstuùûvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["french"]  = $lang;

//-- settings for italian
$lang = array();
$lang["pgv_langname"]    = "italian";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Italiano";
$lang["lang_short_cut"]    = "it";
$lang["langcode"]    = "it;it-ch;";
$lang["pgv_language"]    = "languages/lang.it.php";
$lang["confighelpfile"]    = "languages/configure_help.it.php";
$lang["helptextfile"]    = "languages/help_text.it.php";
$lang["flagsfile"]    = "../users/icons/flags/Italy.gif";
$lang["factsfile"]    = "languages/facts.it.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["italian"]  = $lang;

//-- settings for hungarian
$lang = array();
$lang["pgv_langname"]    = "hungarian";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Magyar";
$lang["lang_short_cut"]    = "hu";
$lang["langcode"]    = "hu;";
$lang["pgv_language"]    = "languages/lang.hu.php";
$lang["confighelpfile"]    = "languages/configure_help.hu.php";
$lang["helptextfile"]    = "languages/help_text.hu.php";
$lang["flagsfile"]    = "../users/icons/flags/Hungary.gif";
$lang["factsfile"]    = "languages/facts.hu.php";
$lang["DATE_FORMAT"]    = "Y. M D.";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = true;
$lang["ALPHABET_upper"]    = "AÁBCDEÉFGHIÍJKLMNOÓÖŐPQRSTUÚÜŰVWXYZ";
$lang["ALPHABET_lower"]    = "aábcdeéfghiíjklmnoóöőpqrstuúüűvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "cs;ccs;dz;ddz;dzs;ddzs;gy;ggy;ly;lly;ny;nny;sz;ssz;ty;tty;zs;zzs";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["hungarian"]  = $lang;

//-- settings for dutch
$lang = array();
$lang["pgv_langname"]    = "dutch";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Nederlands";
$lang["lang_short_cut"]    = "nl";
$lang["langcode"]    = "nl;nl-be;";
$lang["pgv_language"]    = "languages/lang.nl.php";
$lang["confighelpfile"]    = "languages/configure_help.nl.php";
$lang["helptextfile"]    = "languages/help_text.nl.php";
$lang["flagsfile"]    = "../users/icons/flags/Netherlands.gif";
$lang["factsfile"]    = "languages/facts.nl.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["dutch"]  = $lang;

//-- settings for norwegian
$lang = array();
$lang["pgv_langname"]    = "norwegian";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Norsk";
$lang["lang_short_cut"]    = "no";
$lang["langcode"]    = "no;nb;nn;";
$lang["pgv_language"]    = "languages/lang.no.php";
$lang["confighelpfile"]    = "languages/configure_help.no.php";
$lang["helptextfile"]    = "languages/help_text.no.php";
$lang["flagsfile"]    = "../users/icons/flags/Norway.gif";
$lang["factsfile"]    = "languages/facts.no.php";
$lang["DATE_FORMAT"]    = "D. M Y";
$lang["TIME_FORMAT"]    = "H:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZÅØÆ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyzåøæ";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["norwegian"]  = $lang;

//-- settings for polish
$lang = array();
$lang["pgv_langname"]    = "polish";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Polski";
$lang["lang_short_cut"]    = "pl";
$lang["langcode"]    = "pl;";
$lang["pgv_language"]    = "languages/lang.pl.php";
$lang["confighelpfile"]    = "languages/configure_help.pl.php";
$lang["helptextfile"]    = "languages/help_text.pl.php";
$lang["flagsfile"]    = "../users/icons/flags/Poland.gif";
$lang["factsfile"]    = "languages/facts.pl.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AĄBCĆDEĘFGHIJKLŁMNŃOÓPQRSŚTUVWXYZŹŻ";
$lang["ALPHABET_lower"]    = "aąbcćdeęfghijklłmnńoópqrsśtuvwxyzźż";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["polish"]  = $lang;

//-- settings for portuguese-br
$lang = array();
$lang["pgv_langname"]    = "portuguese-br";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Português";
$lang["lang_short_cut"]    = "pt-br";
$lang["langcode"]    = "pt;pt-br;";
$lang["pgv_language"]    = "languages/lang.pt-br.php";
$lang["confighelpfile"]    = "languages/configure_help.pt-br.php";
$lang["helptextfile"]    = "languages/help_text.pt-br.php";
$lang["flagsfile"]    = "../users/icons/flags/Brazil.gif";
$lang["factsfile"]    = "languages/facts.pt-br.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AÁÂÃBCÇDEÉÊFGHIÍJKLMNÑOÓÔÕPQRSTUÚÜVWXYZ";
$lang["ALPHABET_lower"]    = "aáâãbcçdeéêfghiíjklmnñoóôõpqrstuúüvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["portuguese-br"]  = $lang;

//-- settings for finnish
$lang = array();
$lang["pgv_langname"]    = "finnish";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Suomi";
$lang["lang_short_cut"]    = "fi";
$lang["langcode"]    = "fi;";
$lang["pgv_language"]    = "languages/lang.fi.php";
$lang["confighelpfile"]    = "languages/configure_help.fi.php";
$lang["helptextfile"]    = "languages/help_text.fi.php";
$lang["flagsfile"]    = "../users/icons/flags/Finland.gif";
$lang["factsfile"]    = "languages/facts.fi.php";
$lang["DATE_FORMAT"]    = "D. M Y";
$lang["TIME_FORMAT"]    = "H:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÖ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyzåäö";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["finnish"]  = $lang;

//-- settings for swedish
$lang = array();
$lang["pgv_langname"]    = "swedish";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Svenska";
$lang["lang_short_cut"]    = "sv";
$lang["langcode"]    = "sv;sv-fi;";
$lang["pgv_language"]    = "languages/lang.sv.php";
$lang["confighelpfile"]    = "languages/configure_help.sv.php";
$lang["helptextfile"]    = "languages/help_text.sv.php";
$lang["flagsfile"]    = "../users/icons/flags/Sweden.gif";
$lang["factsfile"]    = "languages/facts.sv.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "H:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÖ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyzåäö";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["swedish"]  = $lang;

//-- settings for turkish
$lang = array();
$lang["pgv_langname"]    = "turkish";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Türkçe";
$lang["lang_short_cut"]    = "tr";
$lang["langcode"]    = "tr;";
$lang["pgv_language"]    = "languages/lang.tr.php";
$lang["confighelpfile"]    = "languages/configure_help.tr.php";
$lang["helptextfile"]    = "languages/help_text.tr.php";
$lang["flagsfile"]    = "../users/icons/flags/Turkey.gif";
$lang["factsfile"]    = "languages/facts.tr.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCÇDEFGĞHIİJKLMNOÖPRSŞTUÜVYZ";
$lang["ALPHABET_lower"]    = "abcçdefgğhıijklmnoöprsştuüvyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["turkish"]  = $lang;

//-- settings for chinese
$lang = array();
$lang["pgv_langname"]    = "chinese";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "繁體中文";
$lang["lang_short_cut"]    = "zh";
$lang["langcode"]    = "zh;zh-cn;zh-hk;zh-mo;zh-sg;zh-tw;";
$lang["pgv_language"]    = "languages/lang.zh.php";
$lang["confighelpfile"]    = "languages/configure_help.zh.php";
$lang["helptextfile"]    = "languages/help_text.zh.php";
$lang["flagsfile"]    = "../users/icons/flags/China.gif";
$lang["factsfile"]    = "languages/facts.zh.php";
$lang["DATE_FORMAT"]    = "Y年 M D日";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = true;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["chinese"]  = $lang;

//-- settings for hebrew
$lang = array();
$lang["pgv_langname"]    = "hebrew";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "‏עברית";
$lang["lang_short_cut"]    = "he";
$lang["langcode"]    = "he;";
$lang["pgv_language"]    = "languages/lang.he.php";
$lang["confighelpfile"]    = "languages/configure_help.he.php";
$lang["helptextfile"]    = "languages/help_text.he.php";
$lang["flagsfile"]    = "../users/icons/flags/Israel.gif";
$lang["factsfile"]    = "languages/facts.he.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "rtl";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "אבגדהוזחטיכךלמםנןסעפףצץקרשת";
$lang["ALPHABET_lower"]    = "אבגדהוזחטיכךלמםנןסעפףצץקרשת";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["hebrew"]  = $lang;

//-- settings for russian
$lang = array();
$lang["pgv_langname"]    = "russian";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "русский";
$lang["lang_short_cut"]    = "ru";
$lang["langcode"]    = "ru;ru-md;";
$lang["pgv_language"]    = "languages/lang.ru.php";
$lang["confighelpfile"]    = "languages/configure_help.ru.php";
$lang["helptextfile"]    = "languages/help_text.ru.php";
$lang["flagsfile"]    = "../users/icons/flags/Russian_Federation.gif";
$lang["factsfile"]    = "languages/facts.ru.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
$lang["ALPHABET_lower"]    = "абвгдеёжзийклмнопрстуфхцчшщъыьэюя";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["russian"]  = $lang;

//-- settings for greek
$lang = array();
$lang["pgv_langname"]    = "greek";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Ελληνικά";
$lang["lang_short_cut"]    = "el";
$lang["langcode"]    = "el;";
$lang["pgv_language"]    = "languages/lang.el.php";
$lang["confighelpfile"]    = "languages/configure_help.el.php";
$lang["helptextfile"]    = "languages/help_text.el.php";
$lang["flagsfile"]    = "../users/icons/flags/Greece.gif";
$lang["factsfile"]    = "languages/facts.el.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ΆΑΒΓΔΈΕΖΉΗΘΊΪΪΙΚΛΜΝΞΌΟΠΡΣΣΤΎΫΫΥΦΧΨΏΩ";
$lang["ALPHABET_lower"]    = "άαβγδέεζήηθίϊΐικλμνξόοπρσςτύϋΰυφχψώω";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["greek"]  = $lang;

//-- settings for arabic
$lang = array();
$lang["pgv_langname"]    = "arabic";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "عربي";
$lang["lang_short_cut"]    = "ar";
$lang["langcode"]    = "ar;ar-ae;ar-bh;ar-dz;ar-eg;ar-iq;ar-jo;ar-kw;ar-lb;ar-ly;ar-ma;ar-om;ar-qa;ar-sa;ar-sy;ar-tn;ar-ye;";
$lang["pgv_language"]    = "languages/lang.ar.php";
$lang["confighelpfile"]    = "languages/configure_help.ar.php";
$lang["helptextfile"]    = "languages/help_text.ar.php";
$lang["flagsfile"]    = "../users/icons/flags/Arab_League.gif";
$lang["factsfile"]    = "languages/facts.ar.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "h:i:sA";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "rtl";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ابتثجحخدذرزسشصضطظعغفقكلمنهويآةىی";
$lang["ALPHABET_lower"]    = "ابتثجحخدذرزسشصضطظعغفقكلمنهويآةىی";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["arabic"]  = $lang;

//-- settings for lithuanian
$lang = array();
$lang["pgv_langname"]    = "lithuanian";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Lietuvių";
$lang["lang_short_cut"]    = "lt";
$lang["langcode"]    = "lt;";
$lang["pgv_language"]    = "languages/lang.lt.php";
$lang["confighelpfile"]    = "languages/configure_help.lt.php";
$lang["helptextfile"]    = "languages/help_text.lt.php";
$lang["flagsfile"]    = "../users/icons/flags/Lithuania.gif";
$lang["factsfile"]    = "languages/facts.lt.php";
$lang["DATE_FORMAT"]    = "Y M D";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AĄBCČDEĘĖFGHIYĮJKLMNOPRSŠTUŲŪVZŽ";
$lang["ALPHABET_lower"]    = "aąbcčdeęėfghiyįjklmnoprsštuųūvzž";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["lithuanian"]  = $lang;

//-- settings for danish
$lang = array();
$lang["pgv_langname"]    = "danish";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Dansk";
$lang["lang_short_cut"]    = "da";
$lang["langcode"]    = "da;";
$lang["pgv_language"]    = "languages/lang.da.php";
$lang["confighelpfile"]    = "languages/configure_help.da.php";
$lang["helptextfile"]    = "languages/help_text.da.php";
$lang["flagsfile"]    = "../users/icons/flags/Denmark.gif";
$lang["factsfile"]    = "languages/facts.da.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ";
$lang["ALPHABET_lower"]    = "abcdefghijklmnopqrstuvwxyzæøå";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= false;
$language_settings["danish"]  = $lang;

//-- settings for vietnamese
$lang = array();
$lang["pgv_langname"]    = "vietnamese";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Tiếng Việt";
$lang["lang_short_cut"]    = "vi";
$lang["langcode"]    = "vi;";
$lang["pgv_language"]    = "languages/lang.vi.php";
$lang["confighelpfile"]    = "languages/configure_help.vi.php";
$lang["helptextfile"]    = "languages/help_text.vi.php";
$lang["flagsfile"]    = "../users/icons/flags/Vietnam.gif";
$lang["factsfile"]    = "languages/facts.vi.php";
$lang["DATE_FORMAT"]    = "D M Y";
$lang["TIME_FORMAT"]    = "g:i:sa";
$lang["WEEK_START"]    = "0";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = true;
$lang["ALPHABET_upper"]    = "AÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬBCDĐEÈẺẼÉẸÊỀỂỄẾỆFGHIÌỈĨÍỊJKLMNOÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢPQRSTUÙỦŨÚỤƯỪỬỮỨỰVWXYỲỶỸÝỴZ";
$lang["ALPHABET_lower"]    = "aàảãáạăằẳẵắặâầẩẫấậbcdđeèẻẽéẹêềểễếệfghiìỉĩíịjklmnoòỏõóọôồổỗốộơờởỡớợpqrstuùủũúụưừửữứựvwxyỳỷỹýỵz";
$lang["MULTI_LETTER_ALPHABET"]	= "";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["vietnamese"]  = $lang;

//-- settings for slovak
$lang = array();
$lang["pgv_langname"]    = "slovak";
$lang["pgv_lang_use"]    = false;
$lang["pgv_lang"]    = "Slovenčina";
$lang["lang_short_cut"]    = "sk";
$lang["langcode"]    = "sk;";
$lang["pgv_language"]    = "languages/lang.sk.php";
$lang["confighelpfile"]    = "languages/configure_help.sk.php";
$lang["helptextfile"]    = "languages/help_text.sk.php";
$lang["flagsfile"]    = "../users/icons/flags/Slovakia.gif";
$lang["factsfile"]    = "languages/facts.sk.php";
$lang["DATE_FORMAT"]    = "D. M Y";
$lang["TIME_FORMAT"]    = "G:i:s";
$lang["WEEK_START"]    = "1";
$lang["TEXT_DIRECTION"]    = "ltr";
$lang["NAME_REVERSE"]    = false;
$lang["ALPHABET_upper"]    = "AÁÄBCČDĎEÉFGHCHIÍJKLĽĹMNŇOÓÔPQRŔSŠTŤUÚVWXYÝZŽ";
$lang["ALPHABET_lower"]    = "aáäbcčdďeéfghchiíjklľĺmnňoóôpqrŕsštťuúvwxyýzž";
$lang["MULTI_LETTER_ALPHABET"]	= "dz,ch";
$lang["DICTIONARY_SORT"]	= true;
$language_settings["slovak"]  = $lang;

//-- NEVER manually delete or edit this entry and every line above this entry! --END--//

?>
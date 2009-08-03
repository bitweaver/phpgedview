<?php
/**
 * Standard file of language_settings.php
 *
 * -> NEVER manually delete or edit this file <-
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * $Id: lang_settings_std.php,v 1.8 2009/08/03 20:10:43 lsces Exp $
 *
 * @package PhpGedView
 * @subpackage Languages
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

//-- NEVER manually delete or edit this entry and every line below this entry! --START--//

// Array definition of language_settings
$language_settings = array();

//-- settings for indonesian
$language_settings['indonesian']=array(
'pgv_langname'=>'indonesian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Indonesia',
'lang_short_cut'=>'id',
'langcode'=>'id;in;',
'pgv_language'=>'languages/lang.id.php',
'confighelpfile'=>'languages/configure_help.id.php',
'helptextfile'=>'languages/help_text.id.php',
'flagsfile'=>'images/flags/indonesia.gif',
'factsfile'=>'languages/facts.id.php',
'adminfile'=>'languages/admin.id.php',
'editorfile'=>'languages/editor.id.php',
'countryfile'=>'languages/countries.id.php',
'faqlistfile'=>'languages/faqlist.id.php',
'extrafile'=>'languages/extra.id.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for catalan
$language_settings['catalan']=array(
'pgv_langname'=>'catalan',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Català',
'lang_short_cut'=>'ca',
'langcode'=>'ca;',
'pgv_language'=>'languages/lang.ca.php',
'confighelpfile'=>'languages/configure_help.ca.php',
'helptextfile'=>'languages/help_text.ca.php',
'flagsfile'=>'images/flags/catalonia.gif',
'factsfile'=>'languages/facts.ca.php',
'adminfile'=>'languages/admin.ca.php',
'editorfile'=>'languages/editor.ca.php',
'countryfile'=>'languages/countries.ca.php',
'faqlistfile'=>'languages/faqlist.ca.php',
'extrafile'=>'languages/extra.ca.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AÀBCÇDEÈÉFGHIÍÏJKLMNÑOÒÓPQRSTUÚÜVWXYZ',
'ALPHABET_lower'=>'aàbcçdeèéfghiíïjklmnñopqrstuúüvwxyz',
'MULTI_LETTER_ALPHABET'=>'l·l',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for czech
$language_settings['czech']=array(
'pgv_langname'=>'czech',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Čeština',
'lang_short_cut'=>'cz',
'langcode'=>'cs;cz;',
'pgv_language'=>'languages/lang.cz.php',
'confighelpfile'=>'languages/configure_help.cz.php',
'helptextfile'=>'languages/help_text.cz.php',
'flagsfile'=>'images/flags/czech republic.gif',
'factsfile'=>'languages/facts.cz.php',
'adminfile'=>'languages/admin.cz.php',
'editorfile'=>'languages/editor.cz.php',
'countryfile'=>'languages/countries.cz.php',
'faqlistfile'=>'languages/faqlist.cz.php',
'extrafile'=>'languages/extra.cz.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AÁBCČDĎEĚÉFGHIÍJKLMNŇOÓPQRŘSŠTŤUÚŮVWXYÝZŽ',
'ALPHABET_lower'=>'aábcčdďeěéfghiíjklmnňoópqrřsštťuúůvwxyýzž',
'MULTI_LETTER_ALPHABET'=>'ch',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_czech_ci'
);

//-- settings for danish
$language_settings['danish']=array(
'pgv_langname'=>'danish',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Dansk',
'lang_short_cut'=>'da',
'langcode'=>'da;',
'pgv_language'=>'languages/lang.da.php',
'confighelpfile'=>'languages/configure_help.da.php',
'helptextfile'=>'languages/help_text.da.php',
'flagsfile'=>'images/flags/denmark.gif',
'factsfile'=>'languages/facts.da.php',
'adminfile'=>'languages/admin.da.php',
'editorfile'=>'languages/editor.da.php',
'countryfile'=>'languages/countries.da.php',
'faqlistfile'=>'languages/faqlist.da.php',
'extrafile'=>'languages/extra.da.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyzæøå',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'Aa=Å,aa=å,AE=Æ,ae=æ,OE=Ø,oe=ø',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_danish_ci'
);

//-- settings for german
$language_settings['german']=array(
'pgv_langname'=>'german',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Deutsch',
'lang_short_cut'=>'de',
'langcode'=>'de;de-de;de-at;de-li;de-lu;de-ch;',
'pgv_language'=>'languages/lang.de.php',
'confighelpfile'=>'languages/configure_help.de.php',
'helptextfile'=>'languages/help_text.de.php',
'flagsfile'=>'images/flags/germany.gif',
'factsfile'=>'languages/facts.de.php',
'adminfile'=>'languages/admin.de.php',
'editorfile'=>'languages/editor.de.php',
'countryfile'=>'languages/countries.de.php',
'faqlistfile'=>'languages/faqlist.de.php',
'extrafile'=>'languages/extra.de.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'H:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AÄBCDEFGHIJKLMNOÖPQRSßTUÜVWXYZ',
'ALPHABET_lower'=>'aäbcdefghijklmnoöpqrsßtuüvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for estonian
$language_settings['estonian']=array(
'pgv_langname'=>'estonian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Eesti',
'lang_short_cut'=>'et',
'langcode'=>'et;',
'pgv_language'=>'languages/lang.et.php',
'confighelpfile'=>'languages/configure_help.et.php',
'helptextfile'=>'languages/help_text.et.php',
'flagsfile'=>'images/flags/estonia.gif',
'factsfile'=>'languages/facts.et.php',
'adminfile'=>'languages/admin.et.php',
'editorfile'=>'languages/editor.et.php',
'countryfile'=>'languages/countries.et.php',
'faqlistfile'=>'languages/faqlist.et.php',
'extrafile'=>'languages/extra.et.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSŠZŽTUVWÕÄÖÜXY',
'ALPHABET_lower'=>'abcdefghijklmnopqrsšzžtuvwõäöüxy',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_estonian_ci'
);

//-- settings for english
$language_settings['english']=array(
'pgv_langname'=>'english',
'pgv_lang_use'=>true,
'pgv_lang_self'=>'English',
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
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for spanish
$language_settings['spanish']=array(
'pgv_langname'=>'spanish',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Español',
'lang_short_cut'=>'es',
'langcode'=>'es;es-ar;es-bo;es-cl;es-co;es-cr;es-do;es-ec;es-sv;es-gt;es-hn;es-mx;es-ni;es-pa;es-py;es-pe;es-pr;es-us;es-uy;es-ve;',
'pgv_language'=>'languages/lang.es.php',
'confighelpfile'=>'languages/configure_help.es.php',
'helptextfile'=>'languages/help_text.es.php',
'flagsfile'=>'images/flags/spain.gif',
'factsfile'=>'languages/facts.es.php',
'adminfile'=>'languages/admin.es.php',
'editorfile'=>'languages/editor.es.php',
'countryfile'=>'languages/countries.es.php',
'faqlistfile'=>'languages/faqlist.es.php',
'extrafile'=>'languages/extra.es.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNÑOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnñopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_spanish_ci'
);

//-- settings for spanish-ar
$language_settings['spanish-ar']=array(
'pgv_langname'=>'spanish-ar',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Español Latinoamericano',
'lang_short_cut'=>'es-ar',
'langcode'=>'es;es-ar;es-bo;es-cl;es-co;es-cr;es-do;es-ec;es-sv;es-gt;es-hn;es-mx;es-ni;es-pa;es-py;es-pe;es-pr;es-us;es-uy;es-ve;',
'pgv_language'=>'languages/lang.es-ar.php',
'confighelpfile'=>'languages/configure_help.es-ar.php',
'helptextfile'=>'languages/help_text.es-ar.php',
'flagsfile'=>'images/flags/argentina.gif',
'factsfile'=>'languages/facts.es-ar.php',
'adminfile'=>'languages/admin.es-ar.php',
'editorfile'=>'languages/editor.es-ar.php',
'countryfile'=>'languages/countries.es-ar.php',
'faqlistfile'=>'languages/faqlist.es-ar.php',
'extrafile'=>'languages/extra.es-ar.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNÑOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnñopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_spanish_ci'
);

//-- settings for french
$language_settings['french']=array(
'pgv_langname'=>'french',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Français',
'lang_short_cut'=>'fr',
'langcode'=>'fr;fr-be;fr-ca;fr-lu;fr-mc;fr-ch;',
'pgv_language'=>'languages/lang.fr.php',
'confighelpfile'=>'languages/configure_help.fr.php',
'helptextfile'=>'languages/help_text.fr.php',
'flagsfile'=>'images/flags/france.gif',
'factsfile'=>'languages/facts.fr.php',
'adminfile'=>'languages/admin.fr.php',
'editorfile'=>'languages/editor.fr.php',
'countryfile'=>'languages/countries.fr.php',
'faqlistfile'=>'languages/faqlist.fr.php',
'extrafile'=>'languages/extra.fr.php',
'DATE_FORMAT'=>'D j F Y',
'TIME_FORMAT'=>'H:i:s',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AÀÂÆBCÇDEÉÈËÊFGHIÏÎJKLMNOÔŒPQRSTUÙÛVWXYZ',
'ALPHABET_lower'=>'aàâæbcçdeéèëêfghiïîjklmnoôœpqrstuùûvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for italian
$language_settings['italian']=array(
'pgv_langname'=>'italian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Italiano',
'lang_short_cut'=>'it',
'langcode'=>'it;it-ch;',
'pgv_language'=>'languages/lang.it.php',
'confighelpfile'=>'languages/configure_help.it.php',
'helptextfile'=>'languages/help_text.it.php',
'flagsfile'=>'images/flags/italy.gif',
'factsfile'=>'languages/facts.it.php',
'adminfile'=>'languages/admin.it.php',
'editorfile'=>'languages/editor.it.php',
'countryfile'=>'languages/countries.it.php',
'faqlistfile'=>'languages/faqlist.it.php',
'extrafile'=>'languages/extra.it.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for hungarian
$language_settings['hungarian']=array(
'pgv_langname'=>'hungarian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Magyar',
'lang_short_cut'=>'hu',
'langcode'=>'hu;',
'pgv_language'=>'languages/lang.hu.php',
'confighelpfile'=>'languages/configure_help.hu.php',
'helptextfile'=>'languages/help_text.hu.php',
'flagsfile'=>'images/flags/hungary.gif',
'factsfile'=>'languages/facts.hu.php',
'adminfile'=>'languages/admin.hu.php',
'editorfile'=>'languages/editor.hu.php',
'countryfile'=>'languages/countries.hu.php',
'faqlistfile'=>'languages/faqlist.hu.php',
'extrafile'=>'languages/extra.hu.php',
'DATE_FORMAT'=>'Y. M D.',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>true,
'ALPHABET_upper'=>'AÁBCDEÉFGHIÍJKLMNOÓÖŐPQRSTUÚÜŰVWXYZ',
'ALPHABET_lower'=>'aábcdeéfghiíjklmnoóöőpqrstuúüűvwxyz',
'MULTI_LETTER_ALPHABET'=>'cs;ccs;dz;ddz;dzs;ddzs;gy;ggy;ly;lly;ny;nny;sz;ssz;ty;tty;zs;zzs',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_hungarian_ci'
);

//-- settings for lithuanian
$language_settings['lithuanian']=array(
'pgv_langname'=>'lithuanian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Lietuvių',
'lang_short_cut'=>'lt',
'langcode'=>'lt;',
'pgv_language'=>'languages/lang.lt.php',
'confighelpfile'=>'languages/configure_help.lt.php',
'helptextfile'=>'languages/help_text.lt.php',
'flagsfile'=>'images/flags/lithuania.gif',
'factsfile'=>'languages/facts.lt.php',
'adminfile'=>'languages/admin.lt.php',
'editorfile'=>'languages/editor.lt.php',
'countryfile'=>'languages/countries.lt.php',
'faqlistfile'=>'languages/faqlist.lt.php',
'extrafile'=>'languages/extra.lt.php',
'DATE_FORMAT'=>'Y M D',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AĄBCČDEĘĖFGHIYĮJKLMNOPRSŠTUŲŪVZŽ',
'ALPHABET_lower'=>'aąbcčdeęėfghiyįjklmnoprsštuųūvzž',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_lithuanian_ci'
);

//-- settings for dutch
$language_settings['dutch']=array(
'pgv_langname'=>'dutch',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Nederlands',
'lang_short_cut'=>'nl',
'langcode'=>'nl;nl-be;',
'pgv_language'=>'languages/lang.nl.php',
'confighelpfile'=>'languages/configure_help.nl.php',
'helptextfile'=>'languages/help_text.nl.php',
'flagsfile'=>'images/flags/netherlands.gif',
'factsfile'=>'languages/facts.nl.php',
'adminfile'=>'languages/admin.nl.php',
'editorfile'=>'languages/editor.nl.php',
'countryfile'=>'languages/countries.nl.php',
'faqlistfile'=>'languages/faqlist.nl.php',
'extrafile'=>'languages/extra.nl.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'IJ=Ĳ,ij=ĳ',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for norwegian
$language_settings['norwegian']=array(
'pgv_langname'=>'norwegian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Norsk',
'lang_short_cut'=>'no',
'langcode'=>'no;nb;nn;',
'pgv_language'=>'languages/lang.no.php',
'confighelpfile'=>'languages/configure_help.no.php',
'helptextfile'=>'languages/help_text.no.php',
'flagsfile'=>'images/flags/norway.gif',
'factsfile'=>'languages/facts.no.php',
'adminfile'=>'languages/admin.no.php',
'editorfile'=>'languages/editor.no.php',
'countryfile'=>'languages/countries.no.php',
'faqlistfile'=>'languages/faqlist.no.php',
'extrafile'=>'languages/extra.no.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'H:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZÅØÆ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyzåøæ',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'Aa=Å,aa=å,AE=Æ,ae=æ,OE=Ø,oe=ø',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_danish_ci'
);

//-- settings for polish
$language_settings['polish']=array(
'pgv_langname'=>'polish',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Polski',
'lang_short_cut'=>'pl',
'langcode'=>'pl;',
'pgv_language'=>'languages/lang.pl.php',
'confighelpfile'=>'languages/configure_help.pl.php',
'helptextfile'=>'languages/help_text.pl.php',
'flagsfile'=>'images/flags/poland.gif',
'factsfile'=>'languages/facts.pl.php',
'adminfile'=>'languages/admin.pl.php',
'editorfile'=>'languages/editor.pl.php',
'countryfile'=>'languages/countries.pl.php',
'faqlistfile'=>'languages/faqlist.pl.php',
'extrafile'=>'languages/extra.pl.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AĄBCĆDEĘFGHIJKLŁMNŃOÓPQRSŚTUVWXYZŹŻ',
'ALPHABET_lower'=>'aąbcćdeęfghijklłmnńoópqrsśtuvwxyzźż',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_polish_ci'
);

//-- settings for portuguese
$language_settings['portuguese']=array(
'pgv_langname'=>'portuguese',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Português',
'lang_short_cut'=>'pt',
'langcode'=>'pt;pt-br;',
'pgv_language'=>'languages/lang.pt.php',
'confighelpfile'=>'languages/configure_help.pt.php',
'helptextfile'=>'languages/help_text.pt.php',
'flagsfile'=>'images/flags/portugal.gif',
'factsfile'=>'languages/facts.pt.php',
'adminfile'=>'languages/admin.pt.php',
'editorfile'=>'languages/editor.pt.php',
'countryfile'=>'languages/countries.pt.php',
'faqlistfile'=>'languages/faqlist.pt.php',
'extrafile'=>'languages/extra.pt.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AÁÂÃBCÇDEÉÊFGHIÍJKLMNÑOÓÔÕPQRSTUÚÜVWXYZ',
'ALPHABET_lower'=>'aáâãbcçdeéêfghiíjklmnñoóôõpqrstuúüvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_spanish_ci'
);

//-- settings for romanian
$language_settings['romanian']=array(
'pgv_langname'=>'romanian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Română',
'lang_short_cut'=>'ro',
'langcode'=>'ro;ro-ro;',
'pgv_language'=>'languages/lang.ro.php',
'confighelpfile'=>'languages/configure_help.ro.php',
'helptextfile'=>'languages/help_text.ro.php',
'flagsfile'=>'images/flags/romania.gif',
'factsfile'=>'languages/facts.ro.php',
'adminfile'=>'languages/admin.ro.php',
'editorfile'=>'languages/editor.ro.php',
'countryfile'=>'languages/countries.ro.php',
'faqlistfile'=>'languages/faqlist.ro.php',
'extrafile'=>'languages/extra.ro.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AĂÂBCDEFGHIÎJKLMNOPQRSŞTŢUVWXYZ',
'ALPHABET_lower'=>'aăâbcdefghiîjklmnopqrsştţuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_romanian_ci'
);

//-- settings for slovak
$language_settings['slovak']=array(
'pgv_langname'=>'slovak',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Slovenčina',
'lang_short_cut'=>'sk',
'langcode'=>'sk;',
'pgv_language'=>'languages/lang.sk.php',
'confighelpfile'=>'languages/configure_help.sk.php',
'helptextfile'=>'languages/help_text.sk.php',
'flagsfile'=>'images/flags/slovakia.gif',
'factsfile'=>'languages/facts.sk.php',
'adminfile'=>'languages/admin.sk.php',
'editorfile'=>'languages/editor.sk.php',
'countryfile'=>'languages/countries.sk.php',
'faqlistfile'=>'languages/faqlist.sk.php',
'extrafile'=>'languages/extra.sk.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'AÁÄBCČDĎEÉFGHIÍJKLĽĹMNŇOÓÔPQRŔSŠTŤUÚVWXYÝZŽ',
'ALPHABET_lower'=>'aáäbcčdďeéfghiíjklľĺmnňoóôpqrŕsštťuúvwxyýzž',
'MULTI_LETTER_ALPHABET'=>'dz,ch',
'MULTI_LETTER_EQUIV'=>'DŽ=Ǆ,Dž=ǅ,dž=ǆ,DZ=Ǳ,Dz=ǲ,dz=ǳ',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_slovak_ci'
);

//-- settings for serbian-latin
$language_settings['serbian-la']=array(
'pgv_langname'=>'serbian-la',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Srpski',
'lang_short_cut'=>'sr',
'langcode'=>'sr;',
'pgv_language'=>'languages/lang.sr.php',
'confighelpfile'=>'languages/configure_help.sr.php',
'helptextfile'=>'languages/help_text.sr.php',
'flagsfile'=>'images/flags/serbia.gif',
'factsfile'=>'languages/facts.sr.php',
'adminfile'=>'languages/admin.sr.php',
'editorfile'=>'languages/editor.sr.php',
'countryfile'=>'languages/countries.sr.php',
'faqlistfile'=>'languages/faqlist.sr.php',
'extrafile'=>'languages/extra.sr.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCČĆDĐEFGHIJKLMNOPRSŠTUVZŽ',
'ALPHABET_lower'=>'abcčćdđefghijklmnoprsštuvzž',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_slovenian_ci'
);

//-- settings for slovenian
$language_settings['slovenian']=array(
'pgv_langname'=>'slovenian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Slovenščina',
'lang_short_cut'=>'sl',
'langcode'=>'sl;',
'pgv_language'=>'languages/lang.sl.php',
'confighelpfile'=>'languages/configure_help.sl.php',
'helptextfile'=>'languages/help_text.sl.php',
'flagsfile'=>'images/flags/slovenia.gif',
'factsfile'=>'languages/facts.sl.php',
'adminfile'=>'languages/admin.sl.php',
'editorfile'=>'languages/editor.sl.php',
'countryfile'=>'languages/countries.sl.php',
'faqlistfile'=>'languages/faqlist.sl.php',
'extrafile'=>'languages/extra.sl.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCČĆDEFGHIJKLMNOPQRSŠTUVWXYZŽ',
'ALPHABET_lower'=>'abcčćdefghijklmnopqrsštuvwxyzž',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_slovenian_ci'
);

//-- settings for finnish
$language_settings['finnish']=array(
'pgv_langname'=>'finnish',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Suomi',
'lang_short_cut'=>'fi',
'langcode'=>'fi;',
'pgv_language'=>'languages/lang.fi.php',
'confighelpfile'=>'languages/configure_help.fi.php',
'helptextfile'=>'languages/help_text.fi.php',
'flagsfile'=>'images/flags/finland.gif',
'factsfile'=>'languages/facts.fi.php',
'adminfile'=>'languages/admin.fi.php',
'editorfile'=>'languages/editor.fi.php',
'countryfile'=>'languages/countries.fi.php',
'faqlistfile'=>'languages/faqlist.fi.php',
'extrafile'=>'languages/extra.fi.php',
'DATE_FORMAT'=>'D. M Y',
'TIME_FORMAT'=>'H:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÖ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyzåäö',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_swedish_ci'
);

//-- settings for swedish
$language_settings['swedish']=array(
'pgv_langname'=>'swedish',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Svenska',
'lang_short_cut'=>'sv',
'langcode'=>'sv;sv-fi;',
'pgv_language'=>'languages/lang.sv.php',
'confighelpfile'=>'languages/configure_help.sv.php',
'helptextfile'=>'languages/help_text.sv.php',
'flagsfile'=>'images/flags/sweden.gif',
'factsfile'=>'languages/facts.sv.php',
'adminfile'=>'languages/admin.sv.php',
'editorfile'=>'languages/editor.sv.php',
'countryfile'=>'languages/countries.sv.php',
'faqlistfile'=>'languages/faqlist.sv.php',
'extrafile'=>'languages/extra.sv.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'H:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÖ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyzåäö',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_swedish_ci'
);

//-- settings for turkish
$language_settings['turkish']=array(
'pgv_langname'=>'turkish',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Türkçe',
'lang_short_cut'=>'tr',
'langcode'=>'tr;',
'pgv_language'=>'languages/lang.tr.php',
'confighelpfile'=>'languages/configure_help.tr.php',
'helptextfile'=>'languages/help_text.tr.php',
'flagsfile'=>'images/flags/turkey.gif',
'factsfile'=>'languages/facts.tr.php',
'adminfile'=>'languages/admin.tr.php',
'editorfile'=>'languages/editor.tr.php',
'countryfile'=>'languages/countries.tr.php',
'faqlistfile'=>'languages/faqlist.tr.php',
'extrafile'=>'languages/extra.tr.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'1',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ABCÇDEFGĞHIİJKLMNOÖPRSŞTUÜVYZ',
'ALPHABET_lower'=>'abcçdefgğhıijklmnoöprsştuüvyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_turkish_ci'
);

//-- settings for vietnamese
$language_settings['vietnamese']=array(
'pgv_langname'=>'vietnamese',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Tiếng Việt',
'lang_short_cut'=>'vi',
'langcode'=>'vi;',
'pgv_language'=>'languages/lang.vi.php',
'confighelpfile'=>'languages/configure_help.vi.php',
'helptextfile'=>'languages/help_text.vi.php',
'flagsfile'=>'images/flags/vietnam.gif',
'factsfile'=>'languages/facts.vi.php',
'adminfile'=>'languages/admin.vi.php',
'editorfile'=>'languages/editor.vi.php',
'countryfile'=>'languages/countries.vi.php',
'faqlistfile'=>'languages/faqlist.vi.php',
'extrafile'=>'languages/extra.vi.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>true,
'ALPHABET_upper'=>'AÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬBCDĐEÈẺẼÉẸÊỀỂỄẾỆFGHIÌỈĨÍỊJKLMNOÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢPQRSTUÙỦŨÚỤƯỪỬỮỨỰVWXYỲỶỸÝỴZ',
'ALPHABET_lower'=>'aàảãáạăằẳẵắặâầẩẫấậbcdđeèẻẽéẹêềểễếệfghiìỉĩíịjklmnoòỏõóọôồổỗốộơờởỡớợpqrstuùủũúụưừửữứựvwxyỳỷỹýỵz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>true,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for chinese
$language_settings['chinese']=array(
'pgv_langname'=>'chinese',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'繁體中文',
'lang_short_cut'=>'zh',
'langcode'=>'zh;zh-cn;zh-hk;zh-mo;zh-sg;zh-tw;',
'pgv_language'=>'languages/lang.zh.php',
'confighelpfile'=>'languages/configure_help.zh.php',
'helptextfile'=>'languages/help_text.zh.php',
'flagsfile'=>'images/flags/china.gif',
'factsfile'=>'languages/facts.zh.php',
'adminfile'=>'languages/admin.zh.php',
'editorfile'=>'languages/editor.zh.php',
'countryfile'=>'languages/countries.zh.php',
'faqlistfile'=>'languages/faqlist.zh.php',
'extrafile'=>'languages/extra.zh.php',
'DATE_FORMAT'=>'Y年 m月 d日',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>true,
'ALPHABET_upper'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
'ALPHABET_lower'=>'abcdefghijklmnopqrstuvwxyz',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for hebrew
$language_settings['hebrew']=array(
'pgv_langname'=>'hebrew',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'‏עברית',
'lang_short_cut'=>'he',
'langcode'=>'he;',
'pgv_language'=>'languages/lang.he.php',
'confighelpfile'=>'languages/configure_help.he.php',
'helptextfile'=>'languages/help_text.he.php',
'flagsfile'=>'images/flags/israel.gif',
'factsfile'=>'languages/facts.he.php',
'adminfile'=>'languages/admin.he.php',
'editorfile'=>'languages/editor.he.php',
'countryfile'=>'languages/countries.he.php',
'faqlistfile'=>'languages/faqlist.he.php',
'extrafile'=>'languages/extra.he.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'G:i:s',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'rtl',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'אבגדהוזחטיכךלמםנןסעפףצץקרשת',
'ALPHABET_lower'=>'אבגדהוזחטיכךלמםנןסעפףצץקרשת',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for russian
$language_settings['russian']=array(
'pgv_langname'=>'russian',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'русский',
'lang_short_cut'=>'ru',
'langcode'=>'ru;ru-md;',
'pgv_language'=>'languages/lang.ru.php',
'confighelpfile'=>'languages/configure_help.ru.php',
'helptextfile'=>'languages/help_text.ru.php',
'flagsfile'=>'images/flags/russia.gif',
'factsfile'=>'languages/facts.ru.php',
'adminfile'=>'languages/admin.ru.php',
'editorfile'=>'languages/editor.ru.php',
'countryfile'=>'languages/countries.ru.php',
'faqlistfile'=>'languages/faqlist.ru.php',
'extrafile'=>'languages/extra.ru.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ',
'ALPHABET_lower'=>'абвгдеёжзийклмнопрстуфхцчшщъыьэюя',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for greek
$language_settings['greek']=array(
'pgv_langname'=>'greek',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'Ελληνικά',
'lang_short_cut'=>'el',
'langcode'=>'el;',
'pgv_language'=>'languages/lang.el.php',
'confighelpfile'=>'languages/configure_help.el.php',
'helptextfile'=>'languages/help_text.el.php',
'flagsfile'=>'images/flags/greece.gif',
'factsfile'=>'languages/facts.el.php',
'adminfile'=>'languages/admin.el.php',
'editorfile'=>'languages/editor.el.php',
'countryfile'=>'languages/countries.el.php',
'faqlistfile'=>'languages/faqlist.el.php',
'extrafile'=>'languages/extra.el.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'g:i:sa',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'ltr',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ΆΑΒΓΔΈΕΖΉΗΘΊΪΪΙΚΛΜΝΞΌΟΠΡΣΣΤΎΫΫΥΦΧΨΏΩ',
'ALPHABET_lower'=>'άαβγδέεζήηθίϊΐικλμνξόοπρσςτύϋΰυφχψώω',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_unicode_ci'
);

//-- settings for arabic
$language_settings['arabic']=array(
'pgv_langname'=>'arabic',
'pgv_lang_use'=>false,
'pgv_lang_self'=>'عربي',
'lang_short_cut'=>'ar',
'langcode'=>'ar;ar-ae;ar-bh;ar-dz;ar-eg;ar-iq;ar-jo;ar-kw;ar-lb;ar-ly;ar-ma;ar-om;ar-qa;ar-sa;ar-sy;ar-tn;ar-ye;',
'pgv_language'=>'languages/lang.ar.php',
'confighelpfile'=>'languages/configure_help.ar.php',
'helptextfile'=>'languages/help_text.ar.php',
'flagsfile'=>'images/flags/arab league.gif',
'factsfile'=>'languages/facts.ar.php',
'adminfile'=>'languages/admin.ar.php',
'editorfile'=>'languages/editor.ar.php',
'countryfile'=>'languages/countries.ar.php',
'faqlistfile'=>'languages/faqlist.ar.php',
'extrafile'=>'languages/extra.ar.php',
'DATE_FORMAT'=>'D M Y',
'TIME_FORMAT'=>'h:i:sA',
'WEEK_START'=>'0',
'TEXT_DIRECTION'=>'rtl',
'NAME_REVERSE'=>false,
'ALPHABET_upper'=>'ابتثجحخدذرزسشصضطظعغفقكلمنهويآةىی',
'ALPHABET_lower'=>'ابتثجحخدذرزسشصضطظعغفقكلمنهويآةىی',
'MULTI_LETTER_ALPHABET'=>'',
'MULTI_LETTER_EQUIV'=>'',
'DICTIONARY_SORT'=>false,
'COLLATION'=>'utf8_unicode_ci'
);

?>

<?php
/**
 *
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
 *
 * RFC1766 (HTML language codes) refers to ISO standard 639, and specifies that the
 * 2-character language code of the ISO standard should be used, and that an ISO
 * standard country code can be used as a suffix to the two-character major language
 * code to produce regional variants.  For example, en-us for the English (US) variant.
 *
 * When checking the ISO-639 codes, you should use the Library of Congress site as your
 * authority.  LOC is the current registrar for ISO-639.
 *
 * See Library of Congress http://www.loc.gov/standards/iso639-2/langcodes.html
 *
 */

/**
 * This table lists various languages and an appropriate flag for that language.
 * The key field is the abbreviation, internal to PhpGedView, for the language.
 *
 * The abbreviations are used when a new language is to be implemented in PhpGedView.
 * For example, the abbreviation for "Croatian" is "hr".  This means that the various
 * files within PhpGedView that are specific to Croatian would have .hr. as the last
 * part of the file name, and the Croatian flag would be croatia.gif.
 *
 * Note that PhpGedView allows the flag names to be other than as shown above.  For
 * example, the flag for English (abbreviation "en") could be "australia.gif".
 *
 * This table is used to produce the list of languages that can still be added to
 * PhpGedView.
 *
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_LANG_CODES_STD_PHP', '');

$lng_codes["aa"]    = array("Afar", "Ethiopia");
$lng_codes["ab"]    = array("Abkhazian", "Georgia");
$lng_codes["af"]    = array("Afrikaans", "South Africa");
$lng_codes["ak"]    = array("Akan", "Guinea");
$lng_codes["sq"]    = array("Albanian", "Albania");
$lng_codes["am"]    = array("Amharic", "Ethiopia");
$lng_codes["ar"]    = array("Arabic", "Arab League");	// Common alternate: plain green
$lng_codes["an"]    = array("Aragonese", "Spain");
$lng_codes["hy"]    = array("Armenian", "Armenia");
$lng_codes["av"]    = array("Avaric", "Azerbaijan");
$lng_codes["ay"]    = array("Aymara", "Peru");
$lng_codes["az"]    = array("Azerbaijani", "Azerbaijan");
$lng_codes["bm"]    = array("Bambara", "Mali");
$lng_codes["ba"]    = array("Bashkir", "Russia");
$lng_codes["eu"]    = array("Basque", "France");
$lng_codes["be"]    = array("Belarusian", "Belarus");
$lng_codes["bn"]    = array("Bengali", "Bangladesh");
$lng_codes["bh"]    = array("Bihari", "India");
$lng_codes["bi"]    = array("Bislama", "Vanuatu");
$lng_codes["bs"]    = array("Bosnian", "Bosnia");
$lng_codes["br"]    = array("Breton", "France");
$lng_codes["bg"]    = array("Bulgarian", "Bulgaria");
$lng_codes["my"]    = array("Burmese", "Myanmar");
$lng_codes["ca"]    = array("Catalan (Valencian)", "Spain");
$lng_codes["ch"]    = array("Chamorro", "Guam");
$lng_codes["ce"]    = array("Chechen", "Russia");
$lng_codes["ny"]    = array("Chichewa", "Malawi");
$lng_codes["zh"]    = array("Chinese", "China");
$lng_codes["cv"]    = array("Chuvash", "Russia");
$lng_codes["kw"]    = array("Cornish", "United Kingdom");
$lng_codes["co"]    = array("Corsican", "France");
$lng_codes["cr"]    = array("Cree", "Canada");
$lng_codes["hr"]    = array("Croatian", "Croatia");
$lng_codes["cs"]    = array("Czech", "Czech Republic");
$lng_codes["da"]    = array("Danish", "Denmark");
$lng_codes["dv"]    = array("Divehi", "Maldives");
$lng_codes["nl"]    = array("Dutch", "Netherlands");
$lng_codes["dz"]    = array("Dzongkha", "Bhutan");
$lng_codes["en"]    = array("English", "USA");
$lng_codes["eo"]    = array("Esperanto", "United Nations");
$lng_codes["et"]    = array("Estonian", "Estonia");
$lng_codes["ee"]    = array("Ewe", "Ghana");
$lng_codes["fo"]    = array("Faeroese", "Faeroe Islands");
$lng_codes["fa"]    = array("Persian (Farsi)", "Iran");
$lng_codes["fj"]    = array("Fijian", "Fiji");
$lng_codes["fi"]    = array("Finnish", "Finland");
$lng_codes["fr"]    = array("French", "France");
$lng_codes["fy"]    = array("Frisian", "Friesland");
$lng_codes["ff"]    = array("Fulah", "Sierra Leone");
$lng_codes["gd"]    = array("Gaelic", "Royal Lion Rampant");  // Alternate: Scotland
$lng_codes["gl"]    = array("Gallegan", "Portugal");
$lng_codes["lg"]    = array("Ganda", "Uganda");
$lng_codes["ka"]    = array("Georgian", "Georgia");
$lng_codes["de"]    = array("German", "Germany");
$lng_codes["el"]    = array("Greek", "Greece");
$lng_codes["gn"]    = array("Guarani", "Paraguay");
$lng_codes["gu"]    = array("Gujarati", "India");
$lng_codes["ht"]    = array("Haitian", "Haiti");
$lng_codes["ha"]    = array("Hausa", "Nigeria");
$lng_codes["he"]    = array("Hebrew", "Israel");
$lng_codes["hz"]    = array("Herero", "Botswana");
$lng_codes["hi"]    = array("Hindi", "India");
$lng_codes["ho"]    = array("Hiri Motu", "Papua New Guinea");
$lng_codes["hu"]    = array("Hungarian", "Hungary");
$lng_codes["is"]    = array("Icelandic", "Iceland");
$lng_codes["io"]    = array("Ido", "United Nations");
$lng_codes["ig"]    = array("Igbo", "Nigeria");
$lng_codes["id"]    = array("Indonesian", "Indonesia");
$lng_codes["ia"]    = array("Interlingua (IALA)", "United Nations");
$lng_codes["ie"]    = array("Interlingue", "United Nations");
$lng_codes["iu"]    = array("Inuktitut", "Canada");
$lng_codes["ik"]    = array("Inupiaq", "USA");
$lng_codes["ga"]    = array("Irish", "Eire");
$lng_codes["it"]    = array("Italian", "Italy");
$lng_codes["ja"]    = array("Japanese", "Japan");
$lng_codes["jv"]    = array("Javanese", "Indonesia");
$lng_codes["kl"]    = array("Kalaallisut", "Greenland");
$lng_codes["kn"]    = array("Kannada", "India");
$lng_codes["kr"]    = array("Kanuri", "Nigeria");
$lng_codes["ks"]    = array("Kashmiri", "India");
$lng_codes["kk"]    = array("Kazakh", "Kazakhstan");
$lng_codes["km"]    = array("Khmer", "Cambodia");
$lng_codes["ki"]    = array("Kikuyu", "Kenya");
$lng_codes["rw"]    = array("Kinyarwanda", "Rwanda");
$lng_codes["ky"]    = array("Kirghiz", "Kyrgyzstan");
$lng_codes["kv"]    = array("Komi", "Russia");
$lng_codes["kg"]    = array("Kongo", "Zaire");
$lng_codes["ko"]    = array("Korean", "Korea");
$lng_codes["kj"]    = array("Kuanyama", "Angola");
$lng_codes["ku"]    = array("Kurdish", "Iraq");
$lng_codes["lo"]    = array("Lao", "Laos");
$lng_codes["la"]    = array("Latin", "Vatican");
$lng_codes["lv"]    = array("Latvian", "Latvia");
$lng_codes["li"]    = array("Limburgish", "Netherlands");
$lng_codes["ln"]    = array("Lingala", "Zaire");
$lng_codes["lt"]    = array("Lithuanian", "Lithuania");
$lng_codes["lu"]    = array("Luba-Katanga", "Zaire");
$lng_codes["lb"]    = array("Luxembourgish", "Luxembourg");
$lng_codes["mk"]    = array("Macedonian", "Macedonia");
$lng_codes["mg"]    = array("Malagasy", "Madagascar");
$lng_codes["ml"]    = array("Malayalam", "India");
$lng_codes["ms"]    = array("Malay", "Malaysia");
$lng_codes["mt"]    = array("Maltese", "Malta");
$lng_codes["mi"]    = array("Maori", "New Zealand");
$lng_codes["mr"]    = array("Marathi", "India");
$lng_codes["mh"]    = array("Marshallese", "Marshall Islands");
$lng_codes["mo"]    = array("Moldavian", "Moldova");
$lng_codes["mn"]    = array("Mongolian", "Mongolia");
$lng_codes["na"]    = array("Nauru", "Nauru");
$lng_codes["nv"]    = array("Navajo", "USA");
$lng_codes["nr"]    = array("Ndebele (South dialect)", "South Africa");
$lng_codes["nd"]    = array("Ndebele (North dialect)", "South Africa");
$lng_codes["ng"]    = array("Ndonga", "Namibia");
$lng_codes["ne"]    = array("Nepali", "Nepal");
$lng_codes["no"]    = array("Norwegian", "Norway");
$lng_codes["oj"]    = array("Ojibwa", "Canada");
$lng_codes["or"]    = array("Oriya", "India");
$lng_codes["om"]    = array("Oromo", "Ethiopia");
$lng_codes["os"]    = array("Ossetian", "Iran");
$lng_codes["pi"]    = array("Pali", "India");
$lng_codes["pa"]    = array("Panjabi", "Pakistan");
$lng_codes["pl"]    = array("Polish", "Poland");
$lng_codes["pt"]    = array("Portuguese", "Portugal");
$lng_codes["oc"]    = array("Provençal", "France");
$lng_codes["ps"]    = array("Pushto", "Afghanistan");
$lng_codes["qu"]    = array("Quechua", "Peru");
$lng_codes["rm"]    = array("Raeto-Romance", "Switzerland");
$lng_codes["ro"]    = array("Romanian", "Romania");
$lng_codes["rn"]    = array("Rundi", "Burundi");
$lng_codes["ru"]    = array("Russian", "Russia");
$lng_codes["sz"]    = array("Sami (Lappish dialect)", "Finland");
$lng_codes["se"]    = array("Sami (Northern dialect)", "Finland");
$lng_codes["sm"]    = array("Samoan", "Samoa");
$lng_codes["sg"]    = array("Sango", "Central African Republic");
$lng_codes["sa"]    = array("Sanskrit", "India");
$lng_codes["sc"]    = array("Sardinian", "Italy");
$lng_codes["sr"]    = array("Serbian", "Serbia and Montenegro");
$lng_codes["sn"]    = array("Shona", "Zimbabwe");
$lng_codes["sd"]    = array("Sindhi", "Pakistan");
$lng_codes["si"]    = array("Sinhala", "Sri lanka");
$lng_codes["cu"]    = array("Slavic (Church Slavic)", "Bulgaria");
$lng_codes["sk"]    = array("Slovak", "Slovakia");
$lng_codes["sl"]    = array("Slovenian", "Slovenia");
$lng_codes["so"]    = array("Somali", "Somalia");
$lng_codes["sb"]    = array("Sorbian", "Czech Republic");
$lng_codes["st"]    = array("Sotho (Southern dialect)", "Lesotho");
$lng_codes["es"]    = array("Spanish", "Spain");
$lng_codes["su"]    = array("Sundanese", "Indonesia");
$lng_codes["sx"]    = array("Sutu", "Lesotho");
$lng_codes["sw"]    = array("Swahili", "Zaire");
$lng_codes["ss"]    = array("Swati", "Swaziland");
$lng_codes["sv"]    = array("Swedish", "Sweden");
$lng_codes["tl"]    = array("Tagalog", "Philippines");
$lng_codes["ty"]    = array("Tahitian", "Tahiti");
$lng_codes["tg"]    = array("Tajik", "Tajikistan");
$lng_codes["ta"]    = array("Tamil", "India");
$lng_codes["tt"]    = array("Tatar", "Ukraine");
$lng_codes["te"]    = array("Telugu", "India");
$lng_codes["th"]    = array("Thai", "Thailand");
$lng_codes["bo"]    = array("Tibetan", "Tibet");
$lng_codes["ti"]    = array("Tigrinya", "Ethiopia");
$lng_codes["to"]    = array("Tonga", "Tonga");
$lng_codes["ts"]    = array("Tsonga", "Mozambique");
$lng_codes["tn"]    = array("Tswana", "Botswana");
$lng_codes["tr"]    = array("Turkish", "Turkey");
$lng_codes["tk"]    = array("Turkmen", "Turkmenistan");
$lng_codes["tw"]    = array("Twi", "Cote d'Ivoire");
$lng_codes["ug"]    = array("Uighur", "China");
$lng_codes["uk"]    = array("Ukrainian", "Ukraine");
$lng_codes["ur"]    = array("Urdu", "Pakistan");
$lng_codes["uz"]    = array("Uzbek", "Uzbekistan");
$lng_codes["ve"]    = array("Venda", "South Africa");
$lng_codes["vi"]    = array("Vietnamese", "Vietnam");
$lng_codes["wa"]    = array("Walloon", "Wallonia");
$lng_codes["cy"]    = array("Welsh", "Wales");
$lng_codes["wo"]    = array("Wolof", "Senegal");
$lng_codes["xh"]    = array("Xhosa", "South Africa");
$lng_codes["ii"]    = array("Yi (Sichuan dialect)", "China");
$lng_codes["yi"]    = array("Yiddish", "USA");
$lng_codes["yo"]    = array("Yoruba", "Nigeria");
$lng_codes["za"]    = array("Zhuang", "China");
$lng_codes["zu"]    = array("Zulu", "South Africa");

/**
 * This table provides the list of browser languages that are handled by the same
 * basic language code.  Essentially, it's a list of code synonyms.
 *
 * For example, language code "de" (German) also applies to language code "de-at"
 * (German, Austria), "de-ch" (German, Switzerland), "de-de" (German, Germany) "de-li"
 * (German, Liechtenstein), and "de-lu" (German, Luxemburg).
 *
 * If a given language code isn't in the list, that language doesn't have any
 * synonym codes.  For example, code "cy" (Welsh) isn't in the list, so there aren't
 * any synonym codes for Welsh.
 *
 */

$lng_synonyms["ar"]	= "ar-ae;ar-bh;ar-dz;ar-eg;ar-iq;ar-jo;ar-kw;ar-lb;ar-ly;ar-ma;ar-om;ar-qa;ar-sa;ar-sy;ar-tn;ar-ye;";
$lng_synonyms["zh"]	= "zh-cn;zh-hk;zh-mo;zh-sg;zh-tw;";
$lng_synonyms["nl"]	= "nl-be;";
$lng_synonyms["cs"]	= "cz;";
$lng_synonyms["en"]	= "en-au;en-bz;en-ca;en-gb;en-ie;en-jm;en-nz;en-tt;en-us;en-za;";
$lng_synonyms["fr"]	= "fr-be;fr-ca;fr-ch;fr-lu;fr-mc;";
$lng_synonyms["de"]	= "de-at;de-ch;de-de;de-li;de-lu;";
$lng_synonyms["it"]	= "it-ch;";
$lng_synonyms["no"]	= "nb;nn;";
$lng_synonyms["pt"]	= "pt-br;";
$lng_synonyms["ro"]	= "ro-mo;";
$lng_synonyms["ru"]	= "ru-mo;";
$lng_synonyms["es"]	= "es-ar;es-bo;es-cl;es-co;es-cr;es-do;es-ec;es-gt;es-hn;es-mx;es-ni;es-pa;es-pe;es-pr;es-py;es-sv;es-uy;es-ve;";
$lng_synonyms["sv"]	= "sv-fi;";
// The following codes were changed in ISO-639.  The synonym codes should not be used.
// See Library of Congress: http://www.loc.gov/standards/iso639-2/codechanges.html
$lng_synonyms["yi"]	= "ji;";
$lng_synonyms["he"]	= "iw;";
$lng_synonyms["id"]	= "in;";
$lng_synonyms["jv"]	= "jw;";
$lng_synonyms["hr"]	= "sh;";

?>

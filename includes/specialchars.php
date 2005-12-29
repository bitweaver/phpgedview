<?PHP
/**
 * Special Character tables, for use by JavaScript to input characters 
 * that aren't on your keyboard
 * 
 * THIS FILE MUST BE SAVED IN UTF-8 ENCODING (or some special characters will be lost)
 * 
 * When updating, be sure to add the language into the array $specialchar_languages, 
 * add a case for that language into the switch, and add any new special characters 
 * into the default case of the switch near the bottom.
 * 
 * Languages alpha by name in original language.
 * Special characters by language from European Commision, Research in Official Statistics:
 * http://europa.eu.int/comm/eurostat/research/index.htm?http://europa.eu.int/en/comm/eurostat/research/isi/special/&1
 * Other sources:
 * Czech: http://webdesign.about.com/library/blhtmlcodes-cz.htm
 * Irish: offline sources
 * Hawaiian: http://www.olelo.hawaii.edu/eng/resources/unicode.html
 * Lithuanian: http://www.eki.ee/letter/chardata.cgi?lang=lt+Lithuanian&script=latin
 * 
 * Other special characters are all listed at the bottom.
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
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id: specialchars.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

$specialchar_languages = array(
	"af" => "Afrikaans",
	"cs" => $pgv_lang["lang_name_czech"], // Czech
	"da" => $pgv_lang["lang_name_danish"], // Danish
	"de" => $pgv_lang["lang_name_german"], // German
	"en" => $pgv_lang["lang_name_english"],
	"es" => $pgv_lang["lang_name_spanish"], // Spanish
	"eu" => "Euskara", // Basque
	"fr" => $pgv_lang["lang_name_french"], // French
	"gd-ie" => "Gaeilge", // Irish
	"el" => $pgv_lang["lang_name_greek"],
	"is" => "Íslenska", // Icelandic
	"it" => $pgv_lang["lang_name_italian"],
	"hu" => $pgv_lang["lang_name_hungarian"], // Hungarian
	"lt" => $pgv_lang["lang_name_lithuanian"], // Lithuanian
	"nl" => $pgv_lang["lang_name_dutch"], // Dutch
	"no" => $pgv_lang["lang_name_norwegian"], // Norwegian
	"hawaiian" => "‘Olelo Hawai‘i",
	"pl" => $pgv_lang["lang_name_polish"], // Polish
	"pt" => $pgv_lang["lang_name_portuguese"],
	"sl" => "Slovenšcina", // Slovenian
	"fi" => $pgv_lang["lang_name_finnish"], // Finnish
	"sv" => $pgv_lang["lang_name_swedish"], // Swedish
	"tr" => $pgv_lang["lang_name_turkish"], // Turkish
	"he" => $pgv_lang["lang_name_hebrew"],
	"ar" => $pgv_lang["lang_name_arabic"],
	"ru" => $pgv_lang["lang_name_russian"]
);

switch ($language_filter) {
case "af": // Afrikaans
   $ucspecialchars = array(
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#212;" => "Ô",
		"&#219;" => "Û",
	);
	$lcspecialchars = array(
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#244;" => "ô",
		"&#251;" => "û",
		"&#329;" => "ŉ", // n preceded by apostrophe
	);
	break;
case "cs": // Ceština
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#260;" => "Ą", // A cedille
		"&#196;" => "Ä",
		"&#201;" => "É",
		"&#280;" => "Ę", // E cedille
		"&#282;" => "Ě", // E hachek
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#218;" => "Ú",
		"&#366;" => "Ů", // U ring
		"&#221;" => "Ý",
		"&#268;" => "Č", // C hachek
		"&#313;" => "Ĺ", // L acute
		"&#327;" => "Ň", // N hachek
		"&#340;" => "Ŕ", // R acute
		"&#344;" => "Ř", // R hachek
		"&#352;" => "Š", // S hachek
		"&#381;" => "Ž", // Z hachek
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#261;" => "ą", // a cedille
		"&#228;" => "ä",
		"&#233;" => "é",
		"&#281;" => "ę", // e cedille
		"&#283;" => "ě", // e hachek
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#250;" => "ú",
		"&#367;" => "ů", // u ring
		"&#253;" => "ý",
		"&#269;" => "č", // c hachek
		"&#271;" => "ď", // &#271; d apostrophe - shows incorrectly as d hacheck - d
		"&#357;" => "ť", // t apostrophe
		"&#314;" => "ĺ", // l acute
		"&#328;" => "ň", // n hachek
		"&#341;" => "ŕ", // r acute
		"&#345;" => "ř", // r hachek
		"&#353;" => "š", // s hachek
		"&#382;" => "ž", // z hachek
	);
	break;
case "da": // Dansk
   $ucspecialchars = array(
		"&#197;" => "Å",
		"&#198;" => "Æ",
		"&#201;" => "É",
		"&#216;" => "Ø",
		"&#193;" => "Á",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#218;" => "Ú",
		"&#221;" => "Ý",
	);
	$lcspecialchars = array(
		"&#229;" => "å",
		"&#230;" => "æ",
		"&#233;" => "é",
		"&#248;" => "ø",
		"&#225;" => "á",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#250;" => "ú",
		"&#253;" => "ý",
	);
	break;
case "de": // Deutsch
   $ucspecialchars = array(
		"&#196;" => "Ä",
		"&#214;" => "Ö",
		"&#220;" => "Ü",
		"&#192;" => "À",
		"&#201;" => "É",
	);
	$lcspecialchars = array(
		"&#228;" => "ä",
		"&#246;" => "ö",
		"&#252;" => "ü",
		"&#224;" => "à",
		"&#233;" => "é",
		"&#223;" => "ß",
	);
	break;
case "en": // English  -- limited copy of the default case (Western European set)
   $ucspecialchars = array(
   		"&#192;" => "À",
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#195;" => "Ã",
		"&#196;" => "Ä",
		"&#197;" => "Å",
		"&#198;" => "Æ",
		"&#199;" => "Ç",
		"&#208;" => "Ð",  // capital Eth
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#204;" => "Ì",
		"&#205;" => "Í",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#306;" => "Ĳ",  // ligature IJ
		"&#209;" => "Ñ",
		"&#210;" => "Ò",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#213;" => "Õ",
		"&#214;" => "Ö",
		"&#338;" => "Œ",
		"&#216;" => "Ø",
		"&#222;" => "Þ",  // capital Thorn
		"&#217;" => "Ù",
		"&#218;" => "Ú",
		"&#219;" => "Û",
		"&#220;" => "Ü",
		"&#221;" => "Ý",
		"&#159;" => "Ÿ",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#225;" => "á",
		"&#226;" => "â",
		"&#227;" => "ã",
		"&#228;" => "ä",
		"&#229;" => "å",
		"&#230;" => "æ",
		"&#231;" => "ç",
		"&#240;" => "ð",  // lower-case Thorn
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#236;" => "ì",
		"&#237;" => "í",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#307;" => "ĳ",  // ligature ij
		"&#241;" => "ñ",
		"&#242;" => "ò",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#245;" => "õ",
		"&#246;" => "ö",
		"&#339;" => "œ",
		"&#248;" => "ø",
		"&#254;" => "þ",  // lower-case Eth
		"&#249;" => "ù",
		"&#250;" => "ú",
		"&#251;" => "û",
		"&#252;" => "ü",
		"&#253;" => "ý",
		"&#255;" => "ÿ",
		"&#223;" => "ß",
	);
	break;
case "es": // Español
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#209;" => "Ñ",
		"&#211;" => "Ó",
		"&#218;" => "Ú",
		"&#220;" => "Ü",
		"&#199;" => "Ç",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#241;" => "ñ",
		"&#243;" => "ó",
		"&#250;" => "ú",
		"&#252;" => "ü",
		"&#231;" => "ç",
	);
	break;
case "eu": // Euskara
   $ucspecialchars = array(
		"&#199;" => "Ç",
	);
	$lcspecialchars = array(
		"&#231;" => "ç",
	);
	break;
case "fr": // Français
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#194;" => "Â",
		"&#198;" => "Æ",
		"&#199;" => "Ç",
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#212;" => "Ô",
		"&#338;" => "Œ",
		"&#217;" => "Ù",
		"&#219;" => "Û",
		"&#220;" => "Ü",
		"&#159;" => "Ÿ",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#226;" => "â",
		"&#230;" => "æ",
		"&#231;" => "ç",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#244;" => "ô",
		"&#339;" => "œ",
		"&#249;" => "ù",
		"&#251;" => "û",
		"&#252;" => "ü",
		"&#255;" => "ÿ",
	);
	break;
case "gd-ie": // Gaeilge
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#218;" => "Ú",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#250;" => "ú",
	);
	break;
case "is": // Íslenska
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#198;" => "Æ",
		"&#208;" => "Ð",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#214;" => "Ö",
		"&#222;" => "Þ",
		"&#218;" => "Ú",
		"&#221;" => "Ý",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#230;" => "æ",
		"&#240;" => "ð",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#246;" => "ö",
		"&#254;" => "þ",
		"&#250;" => "ú",
		"&#253;" => "ý",
	);
	break;
case "it": // Italiano
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#200;" => "È",
		"&#201;" => "É",
		"&#204;" => "Ì",
		"&#205;" => "Í",
		"&#210;" => "Ò",
		"&#211;" => "Ó",
		"&#217;" => "Ù",
		"&#218;" => "Ú",
		"&#207;" => "Ï",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#236;" => "ì",
		"&#237;" => "í",
		"&#242;" => "ò",
		"&#243;" => "ó",
		"&#249;" => "ù",
		"&#250;" => "ú",
		"&#239;" => "ï",
	);
	break;
case "hu": // Magyar
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#201;" => "É",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#214;" => "Ö",
		"&#336;" => "Ő", // O with double acute
		"&#218;" => "Ú",
		"&#220;" => "Ü",
		"&#368;" => "Ű", // U with double acute
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#233;" => "é",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#246;" => "ö",
		"&#337;" => "ő", // o with double acute
		"&#250;" => "ú",
		"&#252;" => "ü",
		"&#369;" => "ű", // u with double acute
	);
	break;
case "lt": // Lithuanian
   $ucspecialchars = array(
		"&#260;" => "Ą", // A cedille
		"&#268;" => "Č", // C with hachek/caron
		"&#280;" => "Ę", // E cedille
		"&#278;" => "Ė", // E with dot above
		"&#302;" => "Į", // I  with ogonek
		"&#352;" => "Š", // S hachek
		"&#370;" => "Ų", // U  with ogonek
		"&#362;" => "Ū", // U with macron
		"&#381;" => "Ž", // Z hachek
	);
	$lcspecialchars = array(
		"&#261;" => "ą", // a cedille
		"&#269;" => "č", // c hachek/caron
		"&#281;" => "ę", // e cedille
		"&#279;" => "ė", // e with dot above
		"&#303;" => "į", // i with ogonek
		"&#353;" => "š", // s hachek
		"&#371;" => "ų", // u with ogonek		
		"&#363;" => "ū", // u with macron
		"&#382;" => "ž", // z hachek
         );
	break;
case "nl": // Nederlands
   $ucspecialchars = array(
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#205;" => "Í",
		"&#207;" => "Ï",
		"&#306;" => "Ĳ", // ligature IJ
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#214;" => "Ö",
		"&#218;" => "Ú",
		"&#217;" => "Ù",
		"&#196;" => "Ä",
		"&#219;" => "Û",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#225;" => "á",
		"&#226;" => "â",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#237;" => "í",
		"&#239;" => "ï",
		"&#307;" => "ĳ", // ligature ij
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#246;" => "ö",
		"&#250;" => "ú",
		"&#249;" => "ù",
		"&#228;" => "ä",
		"&#251;" => "û",
		"&#252;" => "ü",
	);
	break;
case "no": // Norsk
   $ucspecialchars = array(
		"&#198;" => "Æ",
		"&#216;" => "Ø",
		"&#197;" => "Å",
		"&#192;" => "À",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#211;" => "Ó",
		"&#210;" => "Ò",
		"&#212;" => "Ô",
	);
	$lcspecialchars = array(
		"&#230;" => "æ",
		"&#248;" => "ø",
		"&#229;" => "å",
		"&#224;" => "à",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#243;" => "ó",
		"&#242;" => "ò",
		"&#244;" => "ô",
	);
	break;
case "hawaiian": // 'Olelo Hawai'i
   $ucspecialchars = array(
		"&#256;" => "Ā", // A with macron
		"&#274;" => "Ē", // E with macron
		"&#298;" => "Ī", // I with macron
		"&#332;" => "Ō", // O with macron
		"&#362;" => "Ū", // U with macron
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
	$lcspecialchars = array(
		"&#257;" => "ā", // a with macron
		"&#275;" => "ē", // e with macron
		"&#299;" => "ī", // i with macron
		"&#333;" => "ō", // o with macron
		"&#363;" => "ū", // u with macron
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
	break;
case "pl": // Polski
   $ucspecialchars = array(
		"&#260;" => "Ą", // A with ogonek
		"&#262;" => "Ć", // C with acute
		"&#280;" => "Ę", // E with ogonek
		"&#321;" => "Ł", // L with stroke
		"&#323;" => "Ń", // N with acute
		"&#211;" => "Ó",
		"&#346;" => "Ś", // S with acute
		"&#377;" => "Ź", // Z with acute
		"&#379;" => "Ż", // Z with dot above
	);
	$lcspecialchars = array(
		"&#261;" => "ą", // a with ogonek
		"&#263;" => "ć", // c with acute
		"&#281;" => "ę", // e with ogonek
		"&#322;" => "ł", // l with stroke
		"&#324;" => "ń", // n with acute
		"&#243;" => "ó",
		"&#347;" => "ś", // s with acute
		"&#378;" => "ź", // z with acute
		"&#380;" => "ż", // z with dot above
	);
	break;
case "pt": // Portuguese
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#195;" => "Ã",
		"&#199;" => "Ç",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#205;" => "Í",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#213;" => "Õ",
		"&#218;" => "Ú",
		"&#220;" => "Ü",
		"&#200;" => "È",
		"&#210;" => "Ò",
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#225;" => "á",
		"&#226;" => "â",
		"&#227;" => "ã",
		"&#231;" => "ç",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#237;" => "í",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#245;" => "õ",
		"&#250;" => "ú",
		"&#252;" => "ü",
		"&#232;" => "è",
		"&#242;" => "ò",
	);
	break;
case "sl": // Slovenšcina
   $ucspecialchars = array(
		"&#268;" => "Č", // C with caron
		"&#352;" => "Š", // S with caron
		"&#381;" => "Ž", // Z with caron
		"&#262;" => "Ć", // C with acute
		"&#272;" => "Ð", // D with stroke
		"&#196;" => "Ä",
		"&#214;" => "Ö",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#269;" => "č", // c with caron
		"&#353;" => "š", // s with caron
		"&#382;" => "ž", // z with caron
		"&#263;" => "ć", // c with acute
		"&#273;" => "đ", // d with stroke
		"&#228;" => "ä",
		"&#246;" => "ö",
		"&#252;" => "ü",
	);
	break;
case "fi": // Suomi
   $ucspecialchars = array(
		"&#196;" => "Ä",
		"&#214;" => "Ö",
		"&#197;" => "Å",
		"&#352;" => "Š",
		"&#381;" => "Ž",
	);
	$lcspecialchars = array(
		"&#228;" => "ä",
		"&#246;" => "ö",
		"&#229;" => "å",
		"&#353;" => "š",
		"&#382;" => "ž",
	);
	break;
case "sv": // Svenska
   $ucspecialchars = array(
		"&#196;" => "Ä",
		"&#197;" => "Å",
		"&#201;" => "É",
		"&#214;" => "Ö",
		"&#193;" => "Á",
		"&#203;" => "Ë",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#228;" => "ä",
		"&#229;" => "å",
		"&#233;" => "é",
		"&#246;" => "ö",
		"&#225;" => "á",
		"&#235;" => "ë",
		"&#252;" => "ü",
	);
	break;
case "tr": // Türkçe
   $ucspecialchars = array(
		"&#194;" => "Â",
		"&#199;" => "Ç",
		"&#286;" => "Ğ", // G with breve
		"&#206;" => "Î",
		"&#304;" => "İ", // I with dot above
		"&#214;" => "Ö",
		"&#350;" => "Ş", // S with cedilla
		"&#219;" => "Û",
		"&#220;" => "Ü",
	);
	$lcspecialchars = array(
		"&#226;" => "â",
		"&#231;" => "ç",
		"&#287;" => "ğ", // g with breve
		"&#238;" => "î",
		"&#305;" => "ı", // i without dot above
		"&#246;" => "ö",
		"&#351;" => "ş", // s with cedilla
		"&#251;" => "û",
		"&#252;" => "ü",
	);
	break;
case "el": // greek
$ucspecialchars = array(
		"Ά" => "Ά",
		"Α" => "Α",
		"Β" => "Β", // G with breve
		"Γ" => "Γ",
		"Δ" => "Δ", // I with dot above
		"Έ" => "Έ",
		"Ε" => "Ε", // S with cedilla
		"Ζ" => "Ζ",
		"Η" => "Η",
		"Θ" => "Θ",
		"Ί" => "Ί",
		"Ϊ" => "Ϊ",
		"Ϊ" => "Ϊ",
		"Ι" => "Ι",
		"Κ" => "Κ",
		"Λ" => "Λ",
		"Μ" => "Μ",
		"Ν" => "Ν",
		"Ξ" => "Ξ",
		"Ό" => "Ό",
		"Ο" => "Ο",
		"Π" => "Π",
		"Ρ" => "Ρ",
		"Σ" => "Σ",
		"Σ" => "Σ",
		"Τ" => "Τ",
		"Ύ" => "Ύ",
		"Ϋ" => "Ϋ",
		"Ϋ" => "Ϋ",
		"Υ" => "Υ",
		"Φ" => "Φ",
		"Χ" => "Χ",
		"Ψ" => "Ψ",
		"Ώ" => "Ώ",
		"Ω" => "Ω"
	);
	$lcspecialchars = array(
		"ά" => "ά",
		"α" => "α",
		"β" => "β", // g with breve
		"γ" => "γ",
		"δ" => "δ", // i without dot above
		"έ" => "έ",
		"ε" => "ε", // s with cedilla
		"ζ" => "ζ",
		"η" => "η",
		"θ" => "θ",
		"ί" => "ί",
		"ϊ" => "ϊ",
		"ΐ" => "ΐ",
		"ι" => "ι",
		"κ" => "κ",
		"λ" => "λ",
		"μ" => "μ",
		"ν" => "ν",
		"ξ" => "ξ",
		"ό" => "ό",
		"ο" => "ο",
		"π" => "π",
		"ρ" => "ρ",
		"σ" => "σ",
		"ς" => "ς",
		"τ" => "τ",
		"ύ" => "ύ",
		"ϋ" => "ϋ",
		"ΰ" => "ΰ",
		"υ" => "υ",
		"φ" => "φ",
		"χ" => "χ",
		"ψ" => "ψ",
		"ώ" => "ώ",
		"ω" => "ω",
	);
	break;
	case "he": // hebrew
   $ucspecialchars = array(
		"א" => "א",
		"ב" => "ב",
		"ג" => "ג",
		"ד" => "ד",
		"ה" => "ה",
		"ו" => "ו",
		"ז" => "ז",
		"ח" => "ח",
		"ט" => "ט",
		"י" => "י",
		"כ" => "כ",
		"ך" => "ך",
		"ל" => "ל",
		"מ" => "מ",
		"ם" => "ם",
		"נ" => "נ",
		"ן" => "ן",
		"ס" => "ס",
		"ע" => "ע",
		"פ" => "פ",
		"ף" => "ף",
		"צ" => "צ",
		"ץ" => "ץ",
		"ק" => "ק",
		"ר" => "ר",
		"ש" => "ש",
		"ת" => "ת"
	);
	$lcspecialchars = array(
		"א" => "א",
		"ב" => "ב",
		"ג" => "ג",
		"ד" => "ד",
		"ה" => "ה",
		"ו" => "ו",
		"ז" => "ז",
		"ח" => "ח",
		"ט" => "ט",
		"י" => "י",
		"כ" => "כ",
		"ך" => "ך",
		"ל" => "ל",
		"מ" => "מ",
		"ם" => "ם",
		"נ" => "נ",
		"ן" => "ן",
		"ס" => "ס",
		"ע" => "ע",
		"פ" => "פ",
		"ף" => "ף",
		"צ" => "צ",
		"ץ" => "ץ",
		"ק" => "ק",
		"ר" => "ר",
		"ש" => "ש",
		"ת" => "ת"
	);
	break;
	case "ar": // arabic
   $ucspecialchars = array(
		"ا" => "ا",
		"ب" => "ب",
		"ت" => "ت",
		"ث" => "ث",
		"ج" => "ج",
		"ح" => "ح",
		"خ" => "خ",
		"د" => "د",
		"ذ" => "ذ",
		"ر" => "ر",
		"ز" => "ز",
		"س" => "س",
		"ش" => "ش",
		"ص" => "ص",
		"ض" => "ض",
		"ط" => "ط",
		"ظ" => "ظ",
		"ع" => "ع",
		"غ" => "غ",
		"ف" => "ف",
		"ق" => "ق",
		"ك" => "ك",
		"ل" => "ل",
		"م" => "م",
		"ن" => "ن",
		"ه" => "ه",
		"و" => "و",
		"ي" => "ي",
		"آ" => "آ",
		"ة" => "ة",
		"ى" => "ى",
		"ی" => "ی"
	);
	$lcspecialchars = array(
		"ا" => "ا",
		"ب" => "ب",
		"ت" => "ت",
		"ث" => "ث",
		"ج" => "ج",
		"ح" => "ح",
		"خ" => "خ",
		"د" => "د",
		"ذ" => "ذ",
		"ر" => "ر",
		"ز" => "ز",
		"س" => "س",
		"ش" => "ش",
		"ص" => "ص",
		"ض" => "ض",
		"ط" => "ط",
		"ظ" => "ظ",
		"ع" => "ع",
		"غ" => "غ",
		"ف" => "ف",
		"ق" => "ق",
		"ك" => "ك",
		"ل" => "ل",
		"م" => "م",
		"ن" => "ن",
		"ه" => "ه",
		"و" => "و",
		"ي" => "ي",
		"آ" => "آ",
		"ة" => "ة",
		"ى" => "ى",
		"ی" => "ی"
	);
	break;
	case "ru": // russian
   $ucspecialchars = array(
		"А" => "А",
		"Б" => "Б",
		"В" => "В",
		"Г" => "Г",
		"Д" => "Д",
		"Е" => "Е",
		"Ё" => "Ё",
		"Ж" => "Ж",
		"З" => "З",
		"И" => "И",
		"Й" => "Й",
		"К" => "К",
		"Л" => "Л",
		"М" => "М",
		"Н" => "Н",
		"О" => "О",
		"П" => "П",
		"Р" => "Р",
		"С" => "С",
		"Т" => "Т",
		"У" => "У",
		"Ф" => "Ф",
		"Х" => "Х",
		"Ц" => "Ц",
		"Ч" => "Ч",
		"Ш" => "Ш",
		"Щ" => "Щ",
		"Ъ" => "Ъ",
		"Ы" => "Ы",
		"Ь" => "Ь",
		"Э" => "Э",
		"Ю" => "Ю",
		"Я" => "Я"
	);
	$lcspecialchars = array(
		"а" => "а",
		"б" => "б",
		"в" => "в",
		"г" => "г",
		"д" => "д",
		"е" => "е",
		"ё" => "ё",
		"ж" => "ж",
		"з" => "з",
		"и" => "и",
		"й" => "й",
		"к" => "к",
		"л" => "л",
		"м" => "м",
		"н" => "н",
		"о" => "о",
		"п" => "п",
		"р" => "р",
		"с" => "с",
		"т" => "т",
		"у" => "у",
		"ф" => "ф",
		"х" => "х",
		"ц" => "ц",
		"ч" => "ч",
		"ш" => "ш",
		"щ" => "щ",
		"ъ" => "ъ",
		"ы" => "ы",
		"ь" => "ь",
		"э" => "э",
		"ю" => "ю",
		"я" => "я"
	);
	break;


default: // list all
   $ucspecialchars = array(
		"&#192;" => "À",
		"&#193;" => "Á",
		"&#194;" => "Â",
		"&#195;" => "Ã",
		"&#196;" => "Ä",
		"&#197;" => "Å",
		"&#260;" => "Ą", // A cedille
		"&#256;" => "Ā", // A with macron
		"&#198;" => "Æ",
		"&#199;" => "Ç",
		"&#268;" => "Č", // C with hachek/caron
		"&#262;" => "Ć", // C with acute
		"&#208;" => "Ð", // eth
		"&#272;" => "Ð", // D with stroke
		"&#200;" => "È",
		"&#201;" => "É",
		"&#202;" => "Ê",
		"&#203;" => "Ë",
		"&#280;" => "Ę", // E cedille
		"&#282;" => "Ě", // E hachek
		"&#274;" => "Ē", // E with macron
		"&#286;" => "Ğ", // G with breve
		"&#204;" => "Ì",
		"&#205;" => "Í",
		"&#206;" => "Î",
		"&#207;" => "Ï",
		"&#304;" => "İ", // I with dot above
		"&#298;" => "Ī", // I with macron
		"&#306;" => "Ĳ", // ligature IJ
		"&#313;" => "Ĺ", // L acute
		"&#321;" => "Ł", // L with stroke
		"&#209;" => "Ñ",
		"&#327;" => "Ň", // N hachek
		"&#323;" => "Ń", // N with acute
		"&#210;" => "Ò",
		"&#211;" => "Ó",
		"&#212;" => "Ô",
		"&#213;" => "Õ",
		"&#214;" => "Ö",
		"&#336;" => "Ő", // O with double acute
		"&#332;" => "Ō", // O with macron
		"&#338;" => "Œ",
		"&#216;" => "Ø",
		"&#340;" => "Ŕ", // R acute
		"&#344;" => "Ř", // R hachek
		"&#352;" => "Š", // S hachek
		"&#346;" => "Ś", // S with acute
		"&#350;" => "Ş", // S with cedilla
		"&#217;" => "Ù",
		"&#218;" => "Ú",
		"&#219;" => "Û",
		"&#220;" => "Ü",
		"&#366;" => "Ů", // U ring
		"&#368;" => "Ű", // U with double acute
		"&#362;" => "Ū", // U with macron
		"&#221;" => "Ý",
		"&#222;" => "Þ",
		"&#159;" => "Ÿ",
		"&#381;" => "Ž", // Z hachek
		"&#377;" => "Ź", // Z with acute
		"&#379;" => "Ż", // Z with dot above
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
	$lcspecialchars = array(
		"&#224;" => "à",
		"&#225;" => "á",
		"&#226;" => "â",
		"&#227;" => "ã",
		"&#228;" => "ä",
		"&#229;" => "å",
		"&#261;" => "ą", // a cedille
		"&#257;" => "ā", // a with macron
		"&#230;" => "æ",
		"&#231;" => "ç",
		"&#269;" => "č", // c hachek/caron
		"&#263;" => "ć", // c with acute
		"&#271;" => "ď", // &#271; d apostrophe - shows incorrectly as d hacheck - d
		"&#273;" => "đ", // d with stroke
		"&#240;" => "ð",
		"&#232;" => "è",
		"&#233;" => "é",
		"&#234;" => "ê",
		"&#235;" => "ë",
		"&#281;" => "ę", // e cedille
		"&#283;" => "ě", // e hachek
		"&#275;" => "ē", // e with macron
		"&#287;" => "ğ", // g with breve
		"&#236;" => "ì",
		"&#237;" => "í",
		"&#238;" => "î",
		"&#239;" => "ï",
		"&#305;" => "ı", // i without dot above
		"&#299;" => "ī", // i with macron
		"&#307;" => "ĳ", // ligature ij
		"&#314;" => "ĺ", // l acute
		"&#322;" => "ł", // l with stroke
		"&#241;" => "ñ",
		"&#329;" => "ŉ", // n preceded by apostrophe
		"&#328;" => "ň", // n hachek
		"&#324;" => "ń", // n with acute
		"&#242;" => "ò",
		"&#243;" => "ó",
		"&#244;" => "ô",
		"&#245;" => "õ",
		"&#246;" => "ö",
		"&#337;" => "ő", // o with double acute
		"&#333;" => "ō", // o with macron
		"&#339;" => "œ",
		"&#248;" => "ø",
		"&#341;" => "ŕ", // r acute
		"&#345;" => "ř", // r hachek
		"&#353;" => "š", // s hachek
		"&#347;" => "ś", // s with acute
		"&#351;" => "ş", // s with cedilla
		"&#223;" => "ß",
		"&#357;" => "ť", // t apostrophe
		"&#249;" => "ù",
		"&#250;" => "ú",
		"&#251;" => "û",
		"&#252;" => "ü",
		"&#367;" => "ů", // u ring
		"&#369;" => "ű", // u with double acute
		"&#363;" => "ū", // u with macron
		"&#253;" => "ý",
		"&#254;" => "þ",
		"&#255;" => "ÿ",
		"&#382;" => "ž", // z hachek
		"&#378;" => "ź", // z with acute
		"&#380;" => "ż", // z with dot above
		"&#x2018;" => "‘", // ‘okina ('okina) - new unicode &#699;
	);
}
$otherspecialchars = array(
	"&#161;" => "¡",
	"&#191;" => "¿",
	"&#171;" => "«",
	"&#187;" => "»",
	"&#8224;" => "†",
	"&#8225;" => "‡",
	"&#8734;" => "∞",  // infinity 
	"&#247;" => "÷",
	"&#215;" => "×",
	"&#170;" => "ª",  // feminine ordinal (nª)
	"&#186;" => "º",  // masculine ordinal (nº)
	"&#8364;" => "€",
	"&#162;" => "¢",
	"&#163;" => "£",
	"&#165;" => "¥",
	"&#167;" => "§",
	"&#169;" => "©",
	"&#176;" => "°",  // degree symbol
	"&#182;" => "¶",
);
?>
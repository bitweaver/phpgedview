<?php
/**
 *
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id: lang_codes_std.php,v 1.4 2007/05/28 08:25:52 lsces Exp $
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

$lng_codes["en"]    = array("English", "United Kingdom");

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
 
$lng_synonyms["en"]	= "en-au;en-bz;en-ca;en-gb;en-ie;en-jm;en-nz;en-tt;en-us;en-za;";

?>
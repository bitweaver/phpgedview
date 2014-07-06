<?php
/**
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2007 to 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_SORT_TABLES_UTF8_PHP', '');

/**********************************************************************************
 *                                                                                *
 *	To prevent loss of some characters, this file MUST be saved in UTF-8 mode     *
 *                                                                                *
 ********************************************************************************** */

 /**
 *		Build the tables required for the Dictionary sort
 *
 *		A Dictionary sort is one where all letters with diacritics are considered to be
 *		identical to the base letter (without the mark).  Diacritics become important
 *		only when the two strings (without marks) are identical.
 *
 *		There are two sets of tables, one for the Upper Case version of a UTF8 character
 *		and the other for the lower-case version.  The two tables are not necessarily
 *		identical.  For example, the Turkish dotless i doesn't exist in the Upper case
 *		table.
 *
 *		Within each set, there are three lists which MUST have a one-to-one relationship.
 *		The "DiacritStrip" list gives the base letter of the corresponding "DiacritWhole"
 *		character.
 *		The "DiacritOrder" list assigns a sort value to the diacritic mark of the
 *		"DiacritWhole" character.  All letters that don't appear in these lists, including
 *		the base letter from which the one bearing diacritic marks is formed, are assigned
 *		a sort value of " ".  By using a single letter from the ASCII code chart, we can
 *		have 52 different UTF8 characters all mapping to the same base character.  This will
 *		handle Vietnamese, which is by far the richest language in terms of diacritic marks.
 */

global $DICTIONARY_SORT, $LANGUAGE;
global $UCDiacritWhole, $LCDiacritWhole, $unknownNN, $unknownPN, $pgv_lang;
global $UTF8_ranges, $UTF8_numbers, $UTF8_brackets, $UTF8_LC_letters;

if (!isset($DICTIONARY_SORT[$LANGUAGE])) $DICTIONARY_SORT[$LANGUAGE] = false;
if ($DICTIONARY_SORT[$LANGUAGE]) {
	$UCDiacritWhole = "ÁÀÄÂÃÅǺĀĂĄǍÇĆĈĊČĎĐÉÈËÊĒĔĘĖĚĜĞĢĠĤĦÍÌÏÎĨĪĬİĮǏĴĶĹĻĽĿŁÑŃŅŇÓÒÖÔÕŐØǾŌŎƠǑŔŖŘŚŜŞŠŢŤŦÚÙÜÛŨŰŲŪŬŮƯǓǕǗǙǛŴÝŸŶŹŻŽ";
	$UCDiacritStrip = "AAAAAAAAAAACCCCCDDEEEEEEEEEGGGGHHIIIIIIIIIIJKLLLLLNNNNOOOOOOOOOOOORRRSSSSTTTUUUUUUUUUUUUUUUUWYYYZZZ";
	$UCDiacritOrder = "ABCDEFGHIJKABCDEABABCDEFGHIJKLMABABCDEFGHIJAAABCDEABCDABCDEFGHIJKLABCABCDABCABCDEFGHIJKLMNOPAABCABC";
	$LCDiacritWhole = "áàäâãåǻāăąǎçćĉċčďđéèëêēĕęėěƒĝğġģĥħíìïîĩīĭįǐĵķĺļľŀłñńņŉóòöôõőøǿōŏơǒŕŗřśŝşšţťŧúùüûũűūŭůųūưǔǖǘǚǜŵýÿŷźżž";
	$LCDiacritStrip = "aaaaaaaaaaacccccddeeeeeeeeefgggghhiiiiiiiiijklllllnnnnoooooooooooorrrsssstttuuuuuuuuuuuuuuuuuwyyyzzz";
	$LCDiacritOrder = "ABCDEFGHIJKABCDEABABCDEFGHIAABCDABCDEFGHIJLAAABCDEABCDABCDEFGHIJKLABCABCDABCABCDEFGHIJKLMNOPQAABCABC";
}

$unknownNN = array(
	'hebrew'    =>$pgv_lang['NNhebrew'],
	'arabic'    =>$pgv_lang['NNarabic'],
	'greek'     =>$pgv_lang['NNgreek'],
	'russian'   =>$pgv_lang['NNrussian'],
	'chinese'   =>$pgv_lang['NNchinese'],
	'vietnamese'=>$pgv_lang['NNvietnamese'],
	'thai'      =>$pgv_lang['NNthai'],
	'other'     =>$pgv_lang['NNother'],
);
$unknownPN = array(
	'hebrew'    =>$pgv_lang['PNhebrew'],
	'arabic'    =>$pgv_lang['PNarabic'],
	'greek'     =>$pgv_lang['PNgreek'],
	'russian'   =>$pgv_lang['PNrussian'],
	'chinese'   =>$pgv_lang['PNchinese'],
	'vietnamese'=>$pgv_lang['PNvietnamese'],
	'thai'      =>$pgv_lang['PNthai'],
	'other'     =>$pgv_lang['PNother'],
);

// Table of UTF8 code ranges
// Reference: Unicode Consortium  http://www.unicode.org
$UTF8_ranges = array();
$UTF8_ranges[] = array("other",		0x000041, 0x00005A);	// upper-case base letters
$UTF8_ranges[] = array("other",		0x000061, 0x00007A);	// lower-case base letters
$UTF8_ranges[] = array("other",		0x0000C0, 0x0000D6);	// Letters with diacritics
$UTF8_ranges[] = array("other",		0x0000D8, 0x0000F6);	// More letters with diacritics
$UTF8_ranges[] = array("other",		0x0000F8, 0x00024F);	// More letters with diacritics
$UTF8_ranges[] = array("greek",		0x000370, 0x0003FF);	// Greek
$UTF8_ranges[] = array("russian",	0x000400, 0x00052F);	// Cyrillic
$UTF8_ranges[] = array("hebrew",	0x000590, 0x0005FF);	// Hebrew
$UTF8_ranges[] = array("arabic",	0x000600, 0x0006FF);	// Arabic
$UTF8_ranges[] = array("arabic",	0x000750, 0x0007FF);	// Arabic
$UTF8_ranges[] = array("thai",	  0x000E00, 0x000E7F);	// Thai
$UTF8_ranges[] = array("vietnamese", 0x001E00, 0x001EFF);	// Vietnamese (assumption!!!)
$UTF8_ranges[] = array("greek",		0x001F00, 0x001FFF);	// Greek
$UTF8_ranges[] = array("chinese",	0x002E80, 0x002FDF);	// Chinese
$UTF8_ranges[] = array("chinese",	0x003190, 0x00319F);	// Chinese
$UTF8_ranges[] = array("chinese",	0x0031C0, 0x0031EF);	// Chinese
$UTF8_ranges[] = array("chinese",	0x003400, 0x004DBF);	// Chinese
$UTF8_ranges[] = array("chinese",	0x004E00, 0x009FBF);	// Chinese
$UTF8_ranges[] = array("chinese",	0x00F900, 0x00FAFF);	// Chinese
$UTF8_ranges[] = array("other",		0x00FB00, 0x00FB06);	// Latin ligatures
$UTF8_ranges[] = array("hebrew",	0x00FB1D, 0x00FB4F);	// Hebrew ligatures
$UTF8_ranges[] = array("arabic",	0x00FB50, 0x00FDFF);	// Arabic
$UTF8_ranges[] = array("arabic",	0x00FE70, 0x00FEFF);	// Arabic
$UTF8_ranges[] = array("chinese",	0x020000, 0x02A6DF);	// Chinese
$UTF8_ranges[] = array("chinese",	0x02F800, 0x02FA1F);	// Chinese

// Numbers:  These are always rendered in LTR, even when the rest of the text is RTL
$UTF8_numbers = array(
	'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
	"\xD9\xA0", "\xD9\xA1", "\xD9\xA2", "\xD9\xA3", "\xD9\xA4", "\xD9\xA5", "\xD9\xA6", "\xD9\xA7", "\xD9\xA8", "\xD9\xA9",
	"\xDB\xB0", "\xDB\xB1", "\xDB\xB2", "\xDB\xB3", "\xDB\xB4", "\xDB\xB5", "\xDB\xB6", "\xDB\xB7", "\xDB\xB8", "\xDB\xB9"
	);

// Parentheses and other paired characters that need to be reversed for proper appearance within RTL text
$UTF8_brackets = array(
	'('=>')', ')'=>'(',
	'['=>']', ']'=>'[',
	'{'=>'}', '}'=>'{',
	'<'=>'>', '>'=>'<',
	"\xC2\xAB"=>"\xC2\xBB", "\xC2\xBB"=>"\xC2\xAB",
	"\xEF\xB4\xBF"=>"\xEF\xB4\xBE", "\xEF\xB4\xBE"=>"\xEF\xB4\xBF",
	"\xE2\x80\xBA"=>"\xE2\x80\xB9", "\xE2\x80\xB9"=>"\xE2\x80\xBA",
	"\xE2\x80\x9E"=>"\xE2\x80\x9C", "\xE2\x80\x9D"=>"\xE2\x80\x9C", "\xE2\x80\x9C"=>"\xE2\x80\x9D",
	"\xE2\x80\x9A"=>"\xE2\x80\x98", "\xE2\x80\x99"=>"\xE2\x80\x98", "\xE2\x80\x98"=>"\xE2\x80\x99"
	);

/*
 * Array of lower-case UTF8 letters with their upper-case equivalents
 *		key: lower-case UTF8 letter
 *		value: equivalent upper-case UTF8 letter
 */
$UTF8_LC_letters = array (
	// basic Latin letters and Latin letters with diacritics
	'a'=>'A', 'à'=>'À', 'á'=>'Á', 'â'=>'Â', 'ã'=>'Ã', 'ä'=>'Ä', 'å'=>'Å', 'æ'=>'Æ', 'ā'=>'Ā', 'ă'=>'Ă', 'ą'=>'Ą', 'ǎ'=>'Ǎ', 'ǻ'=>'Ǻ', 'ǽ'=>'Ǽ', 'ạ'=>'Ạ', 'ả'=>'Ả', 'ấ'=>'Ấ', 'ầ'=>'Ầ', 'ẩ'=>'Ẩ', 'ẫ'=>'Ẫ', 'ậ'=>'Ậ', 'ắ'=>'Ắ', 'ằ'=>'Ằ', 'ẳ'=>'Ẳ', 'ẵ'=>'Ẵ', 'ặ'=>'Ặ',
	'b'=>'B',
	'c'=>'C', 'ç'=>'Ç', 'ć'=>'Ć', 'ĉ'=>'Ĉ', 'ċ'=>'Ċ', 'č'=>'Č',
	'd'=>'D', 'ď'=>'Ď', 'đ'=>'Đ',
	'e'=>'E', 'è'=>'È', 'é'=>'É', 'ê'=>'Ê', 'ë'=>'Ë', 'ē'=>'Ē', 'ĕ'=>'Ĕ', 'ė'=>'Ė', 'ę'=>'Ę', 'ě'=>'Ě', 'ẹ'=>'Ẹ', 'ẻ'=>'Ẻ', 'ẽ'=>'Ẽ', 'ế'=>'Ế', 'ề'=>'Ề', 'ể'=>'Ể', 'ễ'=>'Ễ', 'ệ'=>'Ệ',
	'f'=>'F',
	'g'=>'G', 'ĝ'=>'Ĝ', 'ğ'=>'Ğ', 'ġ'=>'Ġ', 'ģ'=>'Ģ',
	'h'=>'H', 'ĥ'=>'Ĥ', 'ħ'=>'Ħ',
	'i'=>'I', 'ì'=>'Ì', 'í'=>'Í', 'î'=>'Î', 'ï'=>'Ï', 'ĩ'=>'Ĩ', 'ī'=>'Ī', 'ĭ'=>'Ĭ', 'į'=>'Į', 'ı'=>'İ', 'ĳ'=>'Ĳ', 'ǐ'=>'Ǐ', 'ỉ'=>'Ỉ', 'ị'=>'Ị',
	'j'=>'J', 'ĵ'=>'Ĵ',
	'k'=>'K', 'ķ'=>'Ķ',
	'l'=>'L', 'ĺ'=>'Ĺ', 'ļ'=>'Ļ', 'ľ'=>'Ľ', 'ŀ'=>'Ŀ', 'ł'=>'Ł',
	'm'=>'M',
	'n'=>'N', 'ñ'=>'Ñ', 'ń'=>'Ń', 'ņ'=>'Ņ', 'ň'=>'Ň',
	'o'=>'O', 'ò'=>'Ò', 'ó'=>'Ó', 'ô'=>'Ô', 'õ'=>'Õ', 'ö'=>'Ö', 'ø'=>'Ø', 'ō'=>'Ō', 'ŏ'=>'Ŏ', 'ő'=>'Ő', 'œ'=>'Œ', 'ơ'=>'Ơ', 'ǒ'=>'Ǒ', 'ǿ'=>'Ǿ', 'ọ'=>'Ọ', 'ỏ'=>'Ỏ', 'ố'=>'Ố', 'ồ'=>'Ồ', 'ổ'=>'Ổ', 'ỗ'=>'Ỗ', 'ộ'=>'Ộ', 'ớ'=>'Ớ', 'ờ'=>'Ờ', 'ở'=>'Ở', 'ỡ'=>'Ỡ', 'ợ'=>'Ợ',
	'p'=>'P',
	'q'=>'Q',
	'r'=>'R', 'ŕ'=>'Ŕ', 'ŗ'=>'Ŗ', 'ř'=>'Ř',
	's'=>'S', 'ś'=>'Ś',
	't'=>'T',
	'u'=>'U', 'ù'=>'Ù', 'ú'=>'Ú', 'û'=>'Û', 'ü'=>'Ü', 'ŭ'=>'Ŭ', 'ů'=>'Ů', 'ű'=>'Ű', 'ų'=>'Ų', 'ư'=>'Ư', 'ǔ'=>'Ǔ', 'ǖ'=>'Ǖ', 'ǘ'=>'Ǘ', 'ǚ'=>'Ǚ', 'ǜ'=>'Ǜ', 'ụ'=>'Ụ', 'ủ'=>'Ủ', 'ứ'=>'Ứ', 'ừ'=>'Ừ', 'ử'=>'Ử', 'ữ'=>'Ữ', 'ự'=>'Ự',
	'v'=>'V',
	'w'=>'W', 'ŵ'=>'Ŵ', 'ẁ'=>'Ẁ', 'ẃ'=>'Ẃ', 'ẅ'=>'Ẅ',
	'x'=>'X',
	'y'=>'Y', 'ý'=>'Ý', 'ŷ'=>'Ŷ', 'ÿ'=>'Ÿ', 'ỳ'=>'Ỳ', 'ỵ'=>'Ỵ', 'ỷ'=>'Ỷ', 'ỹ'=>'Ỹ',
	'z'=>'Z', 'ź'=>'Ź', 'ż'=>'Ż', 'ž'=>'Ž',
	'ð'=>'Ð',
	'ŋ'=>'Ŋ',
	'þ'=>'Þ',
	'ə'=>'Ə',
	// Greek
	'α'=>'Α', 'ά'=>'Ά',
	'β'=>'Β',
	'γ'=>'Γ',
	'δ'=>'Δ',
	'ε'=>'Ε', 'έ'=>'Έ',
	'ζ'=>'Ζ',
	'η'=>'Η', 'ή'=>'Ή',
	'θ'=>'Θ',
	'ι'=>'Ι', 'ί'=>'Ί', 'ϊ'=>'Ϊ',
	'κ'=>'Κ',
	'λ'=>'Λ',
	'μ'=>'Μ',
	'ν'=>'Ν',
	'ξ'=>'Ξ',
	'ο'=>'Ο', 'ό'=>'Ό',
	'π'=>'Π',
	'ρ'=>'Ρ',
	'ς'=>'Σ',
	'τ'=>'Τ',
	'υ'=>'Υ', 'ϋ'=>'Ϋ',
	'φ'=>'Φ',
	'χ'=>'Χ',
	'ψ'=>'Ψ',
	'ω'=>'Ω', 'ώ'=>'Ώ',
	// Cyrillic
	'а'=>'А',
	'б'=>'Б',
	'в'=>'В',
	'г'=>'Г', 'ґ'=>'Ґ', 'ѓ'=>'Ѓ', 'ғ'=>'Ғ',
	'д'=>'Д',
	'е'=>'Е', 'ё'=>'Ё',
	'ж'=>'Ж', 'җ'=>'Җ',
	'з'=>'З',
	'и'=>'И', 'й'=>'Й', 'і'=>'І', 'ї'=>'Ї',
	'ј'=>'Ј',
	'к'=>'К', 'ќ'=>'Ќ', 'қ'=>'Қ', 'ҝ'=>'Ҝ',
	'л'=>'Л', 'љ'=>'Љ',
	'м'=>'М',
	'н'=>'Н', 'ң'=>'Ң', 'њ'=>'Њ',
	'о'=>'О',
	'п'=>'П',
	'р'=>'Р',
	'с'=>'С',
	'т'=>'Т', 'ћ'=>'Ћ',
	'у'=>'У', 'ў'=>'Ў', 'ү'=>'Ү', 'ұ'=>'Ұ',
	'ф'=>'Ф',
	'х'=>'Х', 'ҳ'=>'Ҳ',
	'ц'=>'Ц', 'џ'=>'Џ',
	'ч'=>'Ч', 'ҹ'=>'Ҹ',
	'ш'=>'Ш', 'щ'=>'Щ',
	'ъ'=>'Ъ', 'ы'=>'Ы', 'ь'=>'Ь',
	'э'=>'Э',
	'ю'=>'Ю',
	'я'=>'Я',
	'ђ'=>'Ђ',
	'є'=>'Є',
	'ѕ'=>'Ѕ',
	'һ'=>'Һ',
	'ә'=>'Ә',
	'ө'=>'Ө'
	);
?>

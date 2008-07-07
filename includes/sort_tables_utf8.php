<?php
/**
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

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

if (!isset($DICTIONARY_SORT[$LANGUAGE])) $DICTIONARY_SORT[$LANGUAGE] = false;
if ($DICTIONARY_SORT[$LANGUAGE]) {
	$UCDiacritWhole = "ÁÀÄÂÃÅǺĀĂĄǍÇĆĈĊČĎĐÉÈËÊĒĔĘĖĚĜĞĢĠĤĦÍÌÏÎĨĪĬİĮǏĴĶĹĻĽĿŁÑŃŅŇÓÒÖÔÕŐØǾŌŎƠǑŔŖŘŚŜŞŠŢŤŦÚÙÜÛŨŰŲŪŬŮƯǓǕǗǙǛŴÝŸŶŹŻŽ";
	$UCDiacritStrip = "AAAAAAAAAAACCCCCDDEEEEEEEEEGGGGHHIIIIIIIIIIJKLLLLLNNNNOOOOOOOOOOOORRRSSSSTTTUUUUUUUUUUUUUUUUWYYYZZZ";
	$UCDiacritOrder = "ABCDEFGHIJKABCDEABABCDEFGHIJKLMABABCDEFGHIJAAABCDEABCDABCDEFGHIJKLABCABCDABCABCDEFGHIJKLMNOPAABCABC";
	$LCDiacritWhole = "áàäâãåǻāăąǎçćĉċčďđéèëêēĕęėěƒĝğġģĥħíìïîĩīĭįǐĵķĺļľŀłñńņŉóòöôõőøǿōŏơǒŕŗřśŝşšţťŧúùüûũűūŭůųūưǔǖǘǚǜŵýÿŷźżž";
	$LCDiacritStrip = "aaaaaaaaaaacccccddeeeeeeeeefgggghhiiiiiiiiijklllllnnnnoooooooooooorrrsssstttuuuuuuuuuuuuuuuuuwyyyzzz";
	$LCDiacritOrder = "ABCDEFGHIJKABCDEABABCDEFGHIAABCDABCDEFGHIJLAAABCDEABCDABCDEFGHIJKLABCABCDABCABCDEFGHIJKLMNOPQAABCABC";	
}

$unknownNN = array();
$unknownNN["hebrew"]	= "(לא ידוע)";
$unknownNN["arabic"]	= "(غير معروف)";
$unknownNN["greek"]		= "(άγνωστος/η)";
$unknownNN["russian"]	= "(неопределено)";
$unknownNN["chinese"]	= "(未知)";
$unknownNN["vietnamese"] = "(vô danh)";
$unknownNN["other"]		= $pgv_lang["NN"];

$unknownPN = array();	 
$unknownPN["hebrew"]	= "(לא ידוע)";
$unknownPN["arabic"]	= "(غير معروف)";
$unknownPN["greek"]		= "(άγνωστος/η)";
$unknownPN["russian"]	= "(неопределено)";
$unknownPN["chinese"]	= "(未知)";
$unknownPN["vietnamese"] = "(không biết tuổi)";
$unknownPN["other"]		= $pgv_lang["PN"];

?>

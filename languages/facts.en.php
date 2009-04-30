<?php
/**
 * English Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009 PGV Development Team. All rights reserved.
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

// -- Define a fact array to map GEDCOM tags with their English values
$factarray["ABBR"]	= "Abbreviation";
$factarray["ADDR"]	= "Address";
$factarray["ADR1"]	= "Address 1";
$factarray["ADR2"]	= "Address 2";
$factarray["ADOP"]	= "Adoption";
$factarray["AFN"]	= "Ancestral File Number (AFN)";
$factarray["AGE"]	= "Age";
$factarray["AGNC"]	= "Agency";
$factarray["ALIA"]	= "Alias";
$factarray["ANCE"]	= "Ancestors";
$factarray["ANCI"]	= "Ancestors Interest";
$factarray["ANUL"]	= "Annulment";
$factarray["ASSO"]	= "Associate";
$factarray["AUTH"]	= "Author";
$factarray["BAPL"]	= "LDS Baptism";
$factarray["BAPM"]	= "Baptism";
$factarray["BARM"]	= "Bar Mitzvah";
$factarray["BASM"]	= "Bas Mitzvah";
$factarray["BIRT"]	= "Birth";
$factarray["BLES"]	= "Blessing";
$factarray["BLOB"]	= "Binary Data Object";
$factarray["BURI"]	= "Burial";
$factarray["CALN"]	= "Call Number";
$factarray["CAST"]	= "Caste / Social Status";
$factarray["CAUS"]	= "Cause of death";
$factarray["CEME"]  = "Cemetery";
$factarray["CENS"]	= "Census";
$factarray["CHAN"]	= "Last Change";
$factarray["CHAR"]	= "Character Set";
$factarray["CHIL"]	= "Child";
$factarray["CHR"]	= "Christening";
$factarray["CHRA"]	= "Adult Christening";
$factarray["CITY"]	= "City";
$factarray["CONF"]	= "Confirmation";
$factarray["CONL"]	= "LDS Confirmation";
$factarray["COPR"]	= "Copyright";
$factarray["CORP"]	= "Corporation / Company";
$factarray["CREM"]	= "Cremation";
$factarray["CTRY"]	= "Country";
$factarray["DATA"]	= "Data";
$factarray["DATE"]	= "Date";
$factarray["DEAT"]	= "Death";
$factarray["DESC"]	= "Descendants";
$factarray["DESI"]	= "Descendants Interest";
$factarray["DEST"]	= "Destination";
$factarray["DIV"]	= "Divorce";
$factarray["DIVF"]	= "Divorce filed";
$factarray["DSCR"]	= "Description";
$factarray["EDUC"]	= "Education";
$factarray["EMIG"]	= "Emigration";
$factarray["ENDL"]	= "LDS Endowment";
$factarray["ENGA"]	= "Engagement";
$factarray["EVEN"]	= "Event";
$factarray["FAM"]	= "Family";
$factarray["FAMC"]	= "Family as a Child";
$factarray["FAMF"]	= "Family File";
$factarray["FAMS"]	= "Family as a Spouse";
$factarray["FCOM"]	= "First Communion";
$factarray["FILE"]	= "External File";
$factarray["FORM"]	= "Format";
$factarray["GIVN"]	= "Given Names";
$factarray["GRAD"]	= "Graduation";
$factarray["HUSB"]  = "Husband";
$factarray["IDNO"]	= "Identification Number";
$factarray["IMMI"]	= "Immigration";
$factarray["LEGA"]	= "Legatee";
$factarray["MARB"]	= "Marriage Bann";
$factarray["MARC"]	= "Marriage Contract";
$factarray["MARL"]	= "Marriage Licence";
$factarray["MARR"]	= "Marriage";
$factarray["MARS"]	= "Marriage Settlement";
$factarray["MEDI"]	= "Media Type";
$factarray["NAME"]	= "Name";
$factarray["NATI"]	= "Nationality";
$factarray["NATU"]	= "Naturalization";
$factarray["NCHI"]	= "Number of Children";
$factarray["NICK"]	= "Nickname";
$factarray["NMR"]	= "Number of Marriages";
$factarray["NOTE"]	= "Note";
$factarray["NPFX"]	= "Prefix";
$factarray["NSFX"]	= "Suffix";
$factarray["OBJE"]	= "Multimedia Object";
$factarray["OCCU"]	= "Occupation";
$factarray["ORDI"]	= "Ordinance";
$factarray["ORDN"]	= "Ordination";
$factarray["PAGE"]	= "Citation Details";
$factarray["PEDI"]	= "Pedigree";
$factarray["PLAC"]	= "Place";
$factarray["PHON"]	= "Phone";
$factarray["POST"]	= "Postal Code";
$factarray["PROB"]	= "Probate";
$factarray["PROP"]	= "Property";
$factarray["PUBL"]	= "Publication";
$factarray["QUAY"]	= "Quality of Data";
$factarray["REPO"]	= "Repository";
$factarray["REFN"]	= "Reference Number";
$factarray["RELA"]	= "Relationship";
$factarray["RELI"]	= "Religion";
$factarray["RESI"]	= "Residence";
$factarray["RESN"]	= "Restriction";
$factarray["RETI"]	= "Retirement";
$factarray["RFN"]	= "Record File Number";
$factarray["RIN"]	= "Record ID Number";
$factarray["ROLE"]	= "Role";
$factarray["SEX"]	= "Gender";
$factarray["SLGC"]	= "LDS Child Sealing";
$factarray["SLGS"]	= "LDS Spouse Sealing";
$factarray["SOUR"]	= "Source";
$factarray["SPFX"]	= "Surname Prefix";
$factarray["SSN"]	= "Social Security Number";
$factarray["STAE"]	= "State";
$factarray["STAT"]	= "Status";
$factarray["SUBM"]	= "Submitter";
$factarray["SUBN"]	= "Submission";
$factarray["SURN"]	= "Surname";
$factarray["TEMP"]	= "Temple";
$factarray["TEXT"]	= "Text";
$factarray["TIME"]	= "Time";
$factarray["TITL"]	= "Title";
$factarray["TYPE"]	= "Type";
$factarray["WIFE"]  = "Wife";
$factarray["WILL"]	= "Will";
$factarray["_EMAIL"]	= "Email Address";
$factarray["EMAIL"]	= "Email Address";
$factarray["_TODO"]	= "To Do Item";
$factarray["_UID"]	= "Globally unique Identifier";
$factarray["_PRIM"]	= "Highlighted Image";
$factarray["_DBID"] = "Linked database ID";

// These facts are used in specific contexts
$factarray["STAT:DATE"] = "Status Change Date";
$factarray["DATA:DATE"] = "Date of entry in original source";

$factarray["NAME:_HEB"]	= "Name in Hebrew";
$factarray["PLAC:_HEB"]	= "Place in Hebrew";
$factarray["TITL:_HEB"]	= "Title in Hebrew";
$factarray["NAME:ROMN"]	= "Romanized Name";
$factarray["PLAC:ROMN"]	= "Romanized Place";
$factarray["TITL:ROMN"]	= "Romanized Title";
$factarray["NAME:FONE"]	= "Phonetic Name";
$factarray["PLAC:FONE"]	= "Phonetic Place";
$factarray["TITL:FONE"]	= "Phonetic Title";

$factarray["SHARED_NOTE"]	= "Shared Note";

//These facts are compounds for the view probabilities and the advanced search pages
$factarray["FAMC:HUSB:SURN"] = "Father's Surname";
$factarray["FAMC:WIFE:SURN"] = "Mother's Surname";
$factarray["FAMC:HUSB:BIRT:PLAC"] = "Father's Birthplace";
$factarray["FAMC:WIFE:BIRT:PLAC"] = "Mother's Birthplace";
$factarray["FAMC:MARR:PLAC"] = "Parents' Marriage Place";
$factarray["FAMC:HUSB:OCCU"] = "Father's Occupation";
$factarray[":BIRT:PLAC"] = "Birthplace";
$factarray["FAMS:MARR:PLAC"] = "Marriage Place";
$factarray["FAMS:MARR:DATE"] = "Marriage Date";
$factarray["FAMS:SPOUSE:DEAT:PLAC"] = "Spouse's Death Place";
$factarray["FAMC:HUSB:GIVN"] = "Father's Given Name";
$factarray["FAMS:SPOUSE:BIRT:PLAC"] = "Spouse's Birth Place";
$factarray["FAMC:WIFE:GIVN"] = "Mother's Given Name";
$factarray["FAMC:HUSB:FAMC:HUSB:GIVN"] = "Paternal Grandfather's Given Name";
$factarray["FAMC:WIFE:FAMC:WIFE:GIVN"] = "Maternal Grandmother's Given Name";
$factarray["FAMC:WIFE:FAMC:HUSB:GIVN"] = "Maternal Grandfather's Given Name"; 
$factarray["FAMC:HUSB:FAMC:WIFE:GIVN"] = "Paternal Grandmother's Given Name";
$factarray["FAMS:CHIL:BIRT:PLAC"] = "Child's Birth Place";
$factarray["FAMS:NOTE"] = "Spouse Note";
$factarray["FAMS:CENS:DATE"] = "Spouse Census Date";
$factarray["FAMS:CENS:PLAC"] = "Spouse Census Place";
$factarray["FAMS:DIV:DATE"] = "Spouse Divorce Date";
$factarray["FAMS:DIV:PLAC"] = "Spouse Divorce Place";
$factarray["FAMS:SLGS:DATE"] = "LDS Spouse Sealing Date";
$factarray["FAMS:SLGS:PLAC"] = "LDS Spouse Sealing Place";
$factarray["FAMS:SLGS:TEMP"] = "LDS Spouse Sealing Temple";

// These facts are all colon delimited
$factarray["BIRT:PLAC"] = "Birth Place";
$factarray["BIRT:DATE"] = "Birth Date";
$factarray["DEAT:PLAC"] = "Death Place";
$factarray["DEAT:DATE"] = "Death Date";
$factarray["CHR:PLAC"] = "Christening Place";
$factarray["CHR:DATE"] = "Christening Date";
$factarray["BAPM:PLAC"] = "Baptism Place";
$factarray["BAPM:DATE"] = "Baptism Date";
$factarray["BURI:PLAC"] = "Burial Place";
$factarray["BURI:DATE"] = "Burial Date";
$factarray["MARR:PLAC"] = "Marriage Place";
$factarray["MARR:DATE"] = "Marriage Date";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]	= "Medical";
$factarray["_DEG"]	= "Degree";
$factarray["_MILT"]	= "Military Service";
$factarray["_SEPR"]	= "Separated";
$factarray["_DETS"]	= "Death of One Spouse";
$factarray["CITN"]	= "Citizenship";
$factarray["_FA1"]	= "Fact 1";
$factarray["_FA2"]	= "Fact 2";
$factarray["_FA3"]	= "Fact 3";
$factarray["_FA4"]	= "Fact 4";
$factarray["_FA5"]	= "Fact 5";
$factarray["_FA6"]	= "Fact 6";
$factarray["_FA7"]	= "Fact 7";
$factarray["_FA8"]	= "Fact 8";
$factarray["_FA9"]	= "Fact 9";
$factarray["_FA10"]	= "Fact 10";
$factarray["_FA11"]	= "Fact 11";
$factarray["_FA12"]	= "Fact 12";
$factarray["_FA13"]	= "Fact 13";
$factarray["_MREL"]	= "Relationship to Mother";
$factarray["_FREL"]	= "Relationship to Father";
$factarray["_MSTAT"]	= "Marriage Beginning Status";
$factarray["_MEND"]	= "Marriage Ending Status";
$factarray["_NAMS"]	= "Namesake";

// GEDCOM 5.5.1 related facts
$factarray["FAX"] = "FAX";
$factarray["FACT"] = "Fact";
$factarray["WWW"] = "Web Home Page";
$factarray["MAP"] = "Map";
$factarray["LATI"] = "Latitude";
$factarray["LONG"] = "Longitude";
$factarray["FONE"] = "Phonetic";
$factarray["ROMN"] = "Romanized";

// PAF related facts
$factarray["_NAME"] = "Mailing Name";
$factarray["URL"] = "Web URL";
$factarray["_URL"] = "Web URL";
$factarray["_HEB"] = "Hebrew";
$factarray["_SCBK"] = "Scrapbook";
$factarray["_TYPE"] = "Media Type";
$factarray["_SSHOW"] = "Slide Show";

// Rootsmagic
$factarray["_SUBQ"]= "Short Version";
$factarray["_BIBL"] = "Bibliography";

// Reunion
$factarray["EMAL"]	= "Email Address";

// Other common customized facts
$factarray["_ADPF"]	= "Adopted by Father";
$factarray["_ADPM"]	= "Adopted by Mother";
$factarray["_AKAN"]	= "Also known as";
$factarray["_AKA"] 	= "Also known as";
$factarray["_BRTM"]	= "Brit Mila";
$factarray["_COML"]	= "Common Law Marriage";
$factarray["_EYEC"]	= "Eye Color";
$factarray["_FNRL"]	= "Funeral";
$factarray["_HAIR"]	= "Hair Color";
$factarray["_HEIG"]	= "Height";
$factarray["_HOL"]  = "Holocaust";
$factarray["_INTE"]	= "Interred";
$factarray["_MARI"]	= "Marriage Intention";
$factarray["_MBON"]	= "Marriage Bond";
$factarray["_MEDC"]	= "Medical Condition";
$factarray["_MILI"]	= "Military";
$factarray["_NMR"]	= "Not married";
$factarray["_NLIV"]	= "Not living";
$factarray["_NMAR"]	= "Never married";
$factarray["_PRMN"]	= "Permanent Number";
$factarray["_WEIG"]	= "Weight";
$factarray["_YART"]	= "Yahrzeit";
$factarray["_MARNM"] = "Married Name";
$factarray["_MARNM_SURN"] = "Married Surname";
$factarray["_STAT"]	= "Marriage Status";
$factarray["COMM"]	= "Comment";

// Aldfaer related facts
$factarray["MARR_CIVIL"] = "Civil Marriage";
$factarray["MARR_RELIGIOUS"] = "Religious Marriage";
$factarray["MARR_PARTNERS"] = "Registered Partnership";
$factarray["MARR_UNKNOWN"] = "Marriage Type unknown";

$factarray["_HNM"] = "Hebrew Name";

// Pseudo-facts for relatives
$factarray["_DEAT_SPOU"] = "Death of spouse";
$factarray["_BURI_SPOU"] = "Burial of spouse";
$factarray["_CREM_SPOU"] = "Cremation of spouse";

$factarray["_BIRT_CHIL"] = "Birth of a child";
$factarray["_CHR_CHIL"] = "Christening of a child";
$factarray["_BAPM_CHIL"] = "Baptism of a child";
$factarray["__BRTM_CHIL"] = "Brit Mila of a child";
$factarray["_ADOP_CHIL"] = "Adoption of a child";
$factarray["_MARR_CHIL"] = "Marriage of a child";
$factarray["_MARB_CHIL"] = "Marriage Bann of a child";
$factarray["_DEAT_CHIL"] = "Death of a child";
$factarray["_BURI_CHIL"] = "Burial of a child";
$factarray["_CREM_CHIL"] = "Cremation of a child";

$factarray["_BIRT_GCHI"] = "Birth of a grandchild";
$factarray["_CHR_GCHI"] = "Christening of a grandchild";
$factarray["_BAPM_GCHI"] = "Baptism of a grandchild";
$factarray["__BRTM_GCHI"] = "Brit Mila of a grandchild";
$factarray["_ADOP_GCHI"] = "Adoption of a grandchild";
$factarray["_MARR_GCHI"] = "Marriage of a grandchild";
$factarray["_MARB_GCHI"] = "Marriage Bann of a grandchild";
$factarray["_DEAT_GCHI"] = "Death of a grandchild";
$factarray["_BURI_GCHI"] = "Burial of a grandchild";
$factarray["_CREM_GCHI"] = "Cremation of a grandchild";

$factarray["_BIRT_GGCH"] = "Birth of a great-grandchild";
$factarray["_CHR_GGCH"] = "Christening of a great-grandchild";
$factarray["_BAPM_GGCH"] = "Baptism of a great-grandchild";
$factarray["__BRTM_GGCH"] = "Brit Mila of a great-grandchild";
$factarray["_ADOP_GGCH"] = "Adoption of a great-grandchild";
$factarray["_MARR_GGCH"] = "Marriage of a great-grandchild";
$factarray["_MARB_GGCH"] = "Marriage Bann of a great-grandchild";
$factarray["_DEAT_GGCH"] = "Death of a great-grandchild";
$factarray["_BURI_GGCH"] = "Burial of a great-grandchild";
$factarray["_CREM_GGCH"] = "Cremation of a great-grandchild";

$factarray["_MARR_FATH"] = "Marriage of father";
$factarray["_MARB_FATH"] = "Marriage Bann of father";
$factarray["_DEAT_FATH"] = "Death of father";
$factarray["_BURI_FATH"] = "Burial of father";
$factarray["_CREM_FATH"] = "Cremation of father";

$factarray["_MARR_FAMC"] = "Marriage of parents";
$factarray["_MARB_FAMC"] = "Marriage Bann of parents";

$factarray["_MARR_MOTH"] = "Marriage of mother";
$factarray["_MARB_MOTH"] = "Marriage Bann of mother";
$factarray["_DEAT_MOTH"] = "Death of mother";
$factarray["_BURI_MOTH"] = "Burial of mother";
$factarray["_CREM_MOTH"] = "Cremation of mother";

$factarray["_BIRT_SIBL"] = "Birth of sibling";
$factarray["_CHR_SIBL"] = "Christening of sibling";
$factarray["_BAPM_SIBL"] = "Baptism of sibling";
$factarray["__BRTM_SIBL"] = "Brit Mila of sibling";
$factarray["_ADOP_SIBL"] = "Adoption of sibling";
$factarray["_MARR_SIBL"] = "Marriage of sibling";
$factarray["_MARB_SIBL"] = "Marriage Bann of sibling";
$factarray["_DEAT_SIBL"] = "Death of sibling";
$factarray["_BURI_SIBL"] = "Burial of sibling";
$factarray["_CREM_SIBL"] = "Cremation of sibling";

$factarray["_BIRT_HSIB"] = "Birth of half-sibling";
$factarray["_CHR_HSIB"] = "Christening of half-sibling";
$factarray["_BAPM_HSIB"] = "Baptism of half-sibling";
$factarray["__BRTM_HSIB"] = "Brit Mila of half-sibling";
$factarray["_ADOP_HSIB"] = "Adoption of half-sibling";
$factarray["_MARR_HSIB"] = "Marriage of half-sibling";
$factarray["_MARB_HSIB"] = "Marriage Bann of half-sibling";
$factarray["_DEAT_HSIB"] = "Death of half-sibling";
$factarray["_BURI_HSIB"] = "Burial of half-sibling";
$factarray["_CREM_HSIB"] = "Cremation of half-sibling";

$factarray["_BIRT_NEPH"] = "Birth of a nephew or niece";
$factarray["_CHR_NEPH"] = "Christening of a nephew or niece";
$factarray["_BAPM_NEPH"] = "Baptism of a nephew or niece";
$factarray["__BRTM_NEPH"] = "Brit Mila of a nephew";
$factarray["_ADOP_NEPH"] = "Adoption of a nephew or niece";
$factarray["_MARR_NEPH"] = "Marriage of a nephew or niece";
$factarray["_MARB_NEPH"] = "Marriage Bann of a nephew or niece";
$factarray["_DEAT_NEPH"] = "Death of a nephew or niece";
$factarray["_BURI_NEPH"] = "Burial of a nephew or niece";
$factarray["_CREM_NEPH"] = "Cremation of a nephew or niece";

$factarray["_DEAT_GPAR"] = "Death of a grand-parent";
$factarray["_BURI_GPAR"] = "Burial of a grand-parent";
$factarray["_CREM_GPAR"] = "Cremation of a grand-parent";

$factarray["_DEAT_GGPA"] = "Death of a great-grand-parent";
$factarray["_BURI_GGPA"] = "Burial of a great-grand-parent";
$factarray["_CREM_GGPA"] = "Cremation of a great-grand-parent";

$factarray["_BIRT_FSIB"] = "Birth of father's sibling";
$factarray["_CHR_FSIB"] = "Christening of father's sibling";
$factarray["_BAPM_FSIB"] = "Baptism of father's sibling";
$factarray["__BRTM_FSIB"] = "Brit Mila of father's sibling";
$factarray["_ADOP_FSIB"] = "Adoption of father's sibling";
$factarray["_MARR_FSIB"] = "Marriage of father's sibling";
$factarray["_MARB_FSIB"] = "Marriage Bann of father's sibling";
$factarray["_DEAT_FSIB"] = "Death of father's sibling";
$factarray["_BURI_FSIB"] = "Burial of father's sibling";
$factarray["_CREM_FSIB"] = "Cremation of father's sibling";

$factarray["_BIRT_MSIB"] = "Birth of mother's sibling";
$factarray["_CHR_MSIB"] = "Christening of mother's sibling";
$factarray["_BAPM_MSIB"] = "Baptism of mother's sibling";
$factarray["__BRTM_MSIB"] = "Brit Mila of mother's sibling";
$factarray["_ADOP_MSIB"] = "Adoption of mother's sibling";
$factarray["_MARR_MSIB"] = "Marriage of mother's sibling";
$factarray["_MARB_MSIB"] = "Marriage Bann of mother's sibling";
$factarray["_DEAT_MSIB"] = "Death of mother's sibling";
$factarray["_BURI_MSIB"] = "Burial of mother's sibling";
$factarray["_CREM_MSIB"] = "Cremation of mother's sibling";

$factarray["_BIRT_COUS"] = "Birth of a first cousin";
$factarray["_CHR_COUS"]  = "Christening of a first cousin";
$factarray["_BAPM_COUS"] = "Baptism of a first cousin";
$factarray["__BRTM_COUS"] = "Brit Mila of a first cousin";
$factarray["_ADOP_COUS"] = "Adoption of a first cousin";
$factarray["_MARR_COUS"] = "Marriage of a first cousin";
$factarray["_MARB_COUS"] = "Marriage Bann of a first cousin";
$factarray["_DEAT_COUS"] = "Death of a first cousin";
$factarray["_BURI_COUS"] = "Burial of a first cousin";
$factarray["_CREM_COUS"] = "Cremation of a first cousin";

$factarray["_FAMC_EMIG"] = "Emigration of parents";
$factarray["_FAMC_RESI"] = "Residence of parents";

//-- PGV Only facts
$factarray["_THUM"]	= "Always use main image?";
$factarray["_PGVU"]	= "by"; // last changed by
$factarray["SERV"] = "Remote Server";
$factarray["_GEDF"] = "GEDCOM File";

/*-- Fact abbreviations for use in Chart boxes.  
 *		Use these abbreviations in cases where the standard method of using the first
 *		letter of the spelled-out name results in an undesirable abbreviation or where
 *		you want to produce a different result (eg: "x" instead of "M" for "Married").
 *
 *		You can abbreviate any Fact label this way.  The list of abbreviations is
 *		open-ended.
 *
 *		These abbreviations are user-customizable. Just put them into file "extra.xx.php".
 *		The length of these abbreviations is not restricted to 1 letter.
 */
 
/*-- The following lines have been commented out.  They should serve as examples. 
 
$factAbbrev["BIRT"]		= "B";
$factAbbrev["MARR"]		= "M";
$factAbbrev["DEAT"]		= "D";

 */

?>

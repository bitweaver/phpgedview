<?php
/**
 * English Language file for PhpGedView.
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
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
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
$factarray["SEX"]	= "Sex";
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
$factarray["_UID"]	= "Universal Identifier";
$factarray["_PRIM"]	= "Highlighted Image";

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
$factarray["_YART"]	= "Yartzeit";
$factarray["_MARNM"] = "Married Name";
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

$factarray["_BIRT_CHIL"] = "Birth of a child";
$factarray["_MARR_CHIL"] = "Marriage of a child";
$factarray["_DEAT_CHIL"] = "Death of a child";

$factarray["_BIRT_GCHI"] = "Birth of a grandchild";
$factarray["_MARR_GCHI"] = "Marriage of a grandchild";
$factarray["_DEAT_GCHI"] = "Death of a grandchild";

$factarray["_MARR_FATH"] = "Marriage of father";
$factarray["_DEAT_FATH"] = "Death of father";

$factarray["_MARR_MOTH"] = "Marriage of mother";
$factarray["_DEAT_MOTH"] = "Death of mother";

$factarray["_BIRT_SIBL"] = "Birth of a sibling";
$factarray["_MARR_SIBL"] = "Marriage of a sibling";
$factarray["_DEAT_SIBL"] = "Death of a sibling";

$factarray["_BIRT_HSIB"] = "Birth of a half-sibling";
$factarray["_MARR_HSIB"] = "Marriage of a half-sibling";
$factarray["_DEAT_HSIB"] = "Death of a half-sibling";

$factarray["_DEAT_GPAR"] = "Death of a grand-parent";

$factarray["_BIRT_FSIB"] = "Birth of a father's sibling";
$factarray["_MARR_FSIB"] = "Marriage of a father's sibling";
$factarray["_DEAT_FSIB"] = "Death of a father's sibling";

$factarray["_BIRT_MSIB"] = "Birth of a mother's sibling";
$factarray["_MARR_MSIB"] = "Marriage of a mother's sibling";
$factarray["_DEAT_MSIB"] = "Death of a mother's sibling";

$factarray["_BIRT_COUS"] = "Birth of a first cousin";
$factarray["_MARR_COUS"] = "Marriage of a first cousin";
$factarray["_DEAT_COUS"] = "Death of a first cousin";

//-- PGV Only facts
$factarray["_THUM"]	= "Use this image as the thumbnail?";
$factarray["_PGVU"]	= "Last changed by";
$factarray["SERV"] = "Remote Server";
$factarray["_GEDF"] = "GEDCOM File";

if (file_exists( "languages/facts.en.extra.php")) require  "languages/facts.en.extra.php";
?>

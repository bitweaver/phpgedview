<?php
/**
 * Defines an array of GEDCOM codes and the Hebrew name facts that they represent.
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
 *
 * @author Meliza
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
 */

if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "אין לך גישה ישירה לקובץ השפות.";
	exit;
}

// -- Define a fact array to map GEDCOM tags with their English values
$factarray["ABBR"] 	= "קיצור";
$factarray["ADDR"] 	= "כתובת";
$factarray["ADR1"] 	= "כתובת 1";
$factarray["ADR2"] 	= "כתובת 2";
$factarray["ADOP"]	= "אמוץ";
$factarray["AGE"] 	= "גיל";
$factarray["AFN"]	= "מספר קובץ אב-אבות (AFN)";
$factarray["AGNC"]	= "סוכנות";
$factarray["ALIA"]	= "שם נרדף";
$factarray["ANCE"]	= "אב-אבות";
$factarray["ANCI"]	= "עניין אב-אבות";
$factarray["ANUL"]	= "ביטול";
$factarray["ASSO"]	= "שותפים";
$factarray["AUTH"] 	= "מחבר";
$factarray["BAPL"]	= "טבילת מורמונים";
$factarray["BAPM"]	= "טבילה";
$factarray["BARM"] 	= "בר מצווה";
$factarray["BASM"] 	= "בת מצווה";
$factarray["BIRT"] 	= "לידה";
$factarray["BLES"]	= "ברכה";
$factarray["BLOB"] 	= "אובייקט נתונים בינארי";
$factarray["BURI"] 	= "קבורה";
$factarray["CALN"]	= "מספר קריאה";
$factarray["CAST"]	= "כת / מעמד חברתי";
$factarray["CAUS"]	= "גורם המות";
$factarray["CEME"]	= "בית קברות";
$factarray["CENS"]	= "מפקד אוכלוסין";
$factarray["CHAN"]	= "שנוי אחרון";
$factarray["CHAR"] 	= "ערכת תווים";
$factarray["CHIL"] 	= "ילד/ה";
$factarray["CHR"]	= "הטבלה";
$factarray["CHRA"]	= "הטבלת מבוגרים";
$factarray["CITY"] 	= "עיר";
$factarray["CONF"]	= "ברית";
$factarray["CONL"]	= "ברית המורמונים";
$factarray["COPR"] 	= "זכויות יוצרים";
$factarray["CORP"]	= "חברה";
$factarray["CREM"]	= "שריפת גופה";
$factarray["CTRY"] 	= "מדינה";
$factarray["DATA"] 	= "נתונים";
$factarray["DATE"] 	= "תאריך";
$factarray["DEAT"] 	= "פטירה";
$factarray["DESC"]	= "צאצאים";
$factarray["DESI"]	= "עניין הצאצאים";
$factarray["DEST"]	= "יעד";
$factarray["DIV"]	= "גירושין";
$factarray["DIVF"]	= "הגשת בקשה לגירושים";
$factarray["EDUC"]	= "השכלה";
$factarray["DSCR"] 	= "תיאור";
$factarray["EMIG"] 	= "הגירה";
$factarray["ENDL"]	= "סמיכה של מיקדש המורמונים";
$factarray["ENGA"] 	= "אירוסין";
$factarray["EVEN"] 	= "אירוע";
$factarray["FAM"] 	= "משפחה";
$factarray["FAMC"]	= "משפחה כילד";
$factarray["FAMF"] 	= "קובץ משפחה";
$factarray["FAMS"]	= "משפחה כבן/בת זוג";
$factarray["FCOM"]	= "הסעודה הראשונה";
$factarray["FILE"]	= "קובץ  חיצוני";
$factarray["FORM"] 	= "תבנית";
$factarray["GIVN"] 	= "שמות פרטיים";
$factarray["GRAD"] 	= "סיום לימודים";
$factarray["HUSB"]	= "בעל";
$factarray["IDNO"] 	= "קוד זיהוי";
$factarray["IMMI"] 	= "עליה";
$factarray["LEGA"]	= "יורש";
$factarray["MARB"]	= "הודעת נישואין";
$factarray["MARC"] 	= "כתובה";
$factarray["MARL"] 	= "רשיון נישואין";
$factarray["MEDI"]	= "סוג מדיה";
$factarray["NCHI"] 	= "מספר ילדים";
$factarray["NICK"] 	= "כנוי";
$factarray["NMR"] 	= "מספר נישואין";
$factarray["PAGE"]	= "פירטי ציטוט";
$factarray["PLAC"] 	= "מקום";
$factarray["PHON"] 	= "טלפון";
$factarray["POST"]	= "מיקוד";
$factarray["PUBL"]	= "הוצאה לאור";
$factarray["QUAY"]	= "איכות נתונים";
$factarray["RELI"]	= "דת";
$factarray["ROLE"]	= "תפקיד";
$factarray["REFN"]	= "מספר התייחסות";
$factarray["RELA"]	= "קשר משפחתי";
$factarray["RESN"]	= "הגבלה";
$factarray["RETI"]	= "פרישה";
$factarray["RFN"]	= "מספר קובץ רשום";
$factarray["RIN"]	= "קוד זיהוי רשום (ID)";
$factarray["RESI"]	= "מגורים";
$factarray["REPO"]	= "מאגר";
$factarray["SPFX"]	= "קידומת שם משפחה";
$factarray["SSN"]	= "מספר מזהה (SSN)";
$factarray["STAE"]	= "מדינה";
$factarray["STAT"]	= "סטטוס";
$factarray["SUBM"]	= "מגיש";
$factarray["SUBN"]	= "הגשה";
$factarray["PROP"]	= "נכס";
$factarray["PROB"]	= "אישור צוואה";
$factarray["SLGC"]	= "חותמת מורמונים - ילד";
$factarray["SLGS"]	= "חותמת מורמונים - בן זוג";
$factarray["SOUR"]  	= "מקור";
$factarray["SURN"]  	= "שם משפחה";
$factarray["TEMP"]	= "מקדש";
$factarray["TEXT"]  	= "טקסט";
$factarray["TIME"] 	= "זמן";
$factarray["TITL"] 	= "כותרת";
$factarray["TYPE"] 	= "סוג";
$factarray["WIFE"] 	= "אישה";
$factarray["WILL"]	= "צוואה";
$factarray["_EMAIL"]	= "כתובת דואר אלקטרוני";
$factarray["EMAIL"]	= "כתובת דואר אלקטרוני";
$factarray["_TODO"]	= "משימות";
$factarray["_UID"]	= "מזהה כללי";
$factarray["SEX"] 	= "מין";
$factarray["NAME"] 	= "שם";
$factarray["MARS"]	= "הסדר נישואין";
$factarray["NATI"] 	= "לאום";
$factarray["NATU"] 	= "התאזרחות";
$factarray["MARR"] 	= "נישואין";
$factarray["PAGE"]	= "פרטי ציטוט";
$factarray["OCCU"]	= "מקצוע";
$factarray["ORDI"]	= "הסמכה";
$factarray["ORDN"]	= "הסמכה לכמורה";
$factarray["NOTE"] 	= "הערה";
$factarray["NSFX"] 	= "צירוף סופי";
$factarray["NPFX"]	= "צירוף ראשי";
$factarray["OBJE"] 	= "מולטימדיה";
$factarray["PEDI"] 	= "יחוס";
$factarray["_PRIM"]	= "תמונה מודגשת";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]	= "רפואי";
$factarray["_DEG"]	= "דרגה";
$factarray["_MILT"] 	= "שרות צבא";
$factarray["_SEPR"]	= "פרוד";
$factarray["_DETS"]	= "מוות של אחד מבני הזוג";
$factarray["CITN"]	= "אזרחות";
$factarray["_FA1"] 	= "עובדה 1";
$factarray["_FA2"] 	= "עובדה 2";
$factarray["_FA3"] 	= "עובדה 3";
$factarray["_FA4"] 	= "עובדה 4";
$factarray["_FA5"] 	= "עובדה 5";
$factarray["_FA6"] 	= "עובדה 6";
$factarray["_FA7"] 	= "עובדה 7";
$factarray["_FA8"] 	= "עובדה 8";
$factarray["_FA9"] 	= "עובדה 9";
$factarray["_FA10"] 	= "עובדה 10";
$factarray["_FA11"] 	= "עובדה 11";
$factarray["_FA12"] 	= "עובדה 12";
$factarray["_FA13"] 	= "עובדה 13";
$factarray["_MREL"] 	= "קשר אל אמא";
$factarray["_FREL"] 	= "קשר אל אבא";
$factarray["_MSTAT"] 	= "מעמד תחילת נישואים";
$factarray["_MEND"]  	= "מעמד סיום נישואים";

// GEDCOM 5.5.1 related facts
$factarray["FAX"] 	= "פקס";
$factarray["FACT"] 	= "עובדה";
$factarray["WWW"] 	= "דף בית";
$factarray["MAP"] 	= "מפה";
$factarray["LATI"]	= "קו רוחב";
$factarray["LONG"]	= "קו אורך";
$factarray["FONE"] 	= "פונטי";
$factarray["ROMN"]	= "לטיני";

// PAF related facts
$factarray["_NAME"] 	= "שם למשלוח דואר";
$factarray["URL"] 	= "URL";
$factarray["_HEB"] 	= "עברי";
$factarray["_SCBK"] 	= "אלבום הדבקות";
$factarray["_TYPE"] 	= "סוג מדיה";
$factarray["_SSHOW"] 	= "מצגת שקופיות";

// Rootsmagic
$factarray["_SUBQ"]	= "גרסה קצרה";
$factarray["_BIBL"] 	= "ביבליוגרפיה";

// Reunion
$factarray["EMAL"]	= "כתובת דואר אלקטרוני";

// Other common customized facts
$factarray["_ADPF"]	= "אמוץ ע\"י אבא";
$factarray["_ADPM"]	= "אמוץ ע\"י אמא";
$factarray["_AKAN"]	= "ידוע בשם";
$factarray["_AKA"] 	= "ידוע בשם";
$factarray["_BRTM"] = "ברית מילה";
$factarray["_COML"]	= "ידוע בציבור";
$factarray["_EYEC"]	= "צבע עיניים";
$factarray["_FNRL"]	= "הלוויה";
$factarray["_HAIR"]	= "צבע שיער";
$factarray["_HEIG"]	= "גובה";
$factarray["_HOL"] 	= "שואה";
$factarray["_INTE"]	= "קבור";
$factarray["_MARI"]	= "כוונת נישואים";
$factarray["_MBON"]	= "קשר נישואים";
$factarray["_MEDC"]	= "מצב רפואי";
$factarray["_MILI"] 	= "צבא";
$factarray["_NMR"]	= "לא נשוי";
$factarray["_NLIV"] 	= "לא בחיים";
$factarray["_NMAR"]	= "רווק";
$factarray["_PRMN"]	= "מספר קבוע";
$factarray["_WEIG"] 	= "משקל";
$factarray["_YART"]	= "יום השנה";
$factarray["_MARNM"] 	= "שם נישואין";
$factarray["_STAT"] 	= "מעמד נישואין";
$factarray["COMM"]	= "הערה";

// Aldfaer related facts
$factarray["MARR_CIVIL"]     = "נישואין אזרחיים";
$factarray["MARR_RELIGIOUS"] = "נישואין דתיים";
$factarray["MARR_PARTNERS"]  = "שותפות רשמית";
$factarray["MARR_UNKNOWN"]   = "סוג הנישואין אינו ידוע";

$factarray["_HNM"] 	   = "שם עברי";

// Pseudo-facts for relatives
$factarray["_DEAT_SPOU"] = "פטירת אחד מבני הזוג";

$factarray["_BIRT_CHIL"] = "לידת בן או בת";
$factarray["_MARR_CHIL"] = "נישואי בן או בת";
$factarray["_DEAT_CHIL"] = "פטירת בן או בת";

$factarray["_BIRT_GCHI"] = "לידת נכד/ה";
$factarray["_MARR_GCHI"] = "נישואי נכד/ה";
$factarray["_DEAT_GCHI"] = "פטירת נכד/ה";

$factarray["_MARR_FATH"] = "נישואי אב";
$factarray["_DEAT_FATH"] = "פטירת אב";

$factarray["_MARR_MOTH"] = "נישואי אם";
$factarray["_DEAT_MOTH"] = "פטירת אם";

$factarray["_BIRT_SIBL"] = "לידת אח או אחות";
$factarray["_MARR_SIBL"] = "נישואי אח/ות";
$factarray["_DEAT_SIBL"] = "פטירת אח או אחות";

$factarray["_BIRT_HSIB"] = "לידת אח/ות למחצה";
$factarray["_MARR_HSIB"] = "נישואי אח/ות למחצה";
$factarray["_DEAT_HSIB"] = "פטירת אח/ות למחצה";

$factarray["_DEAT_GPAR"] = "פטירת סבא או סבתא";

$factarray["_BIRT_FSIB"] = "לידת אח או אחות האב";
$factarray["_MARR_FSIB"] = "נישואי אח/ות של האב";
$factarray["_DEAT_FSIB"] = "פטירת אח או אחות האב";

$factarray["_BIRT_MSIB"] = "לידת אח או אחות האם";
$factarray["_MARR_MSIB"] = "נישואי אח/ות של האם";
$factarray["_DEAT_MSIB"] = "פטירת אח או אחות האם";

$factarray["_BIRT_COUS"] = "לידת בן דוד מדרגה ראשונה";
$factarray["_MARR_COUS"] = "נישואי בן דוד מדרגה ראשונה";
$factarray["_DEAT_COUS"] = "פטירת בן דוד מדרגה ראשונה";

//-- PGV Only facts
$factarray["_THUM"] = "השתמש בתמונה זו כתמונה ממוזערת?";
$factarray["_PGVU"] = "האחרון ששינה";
$factarray["SERV"]  = "שרת מרוחק";
$factarray["_GEDF"] = "קובץ GEDCOM";

//if (file_exists($PGV_BASE_DIRECTORY . "languages/facts.he.extra.php")) require $PGV_BASE_DIRECTORY . "languages/facts.he.extra.php";
if (file_exists( "languages/facts.he.extra.php")) require  "languages/facts.he.extra.php";
?>
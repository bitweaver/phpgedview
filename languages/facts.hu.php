<?php
/**
 * Hungarian Language file
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  István Pető and Gábor Hrotkó
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
 * @author István Pető
 * @author Gábor Hrotkó
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their Hungarian values
$factarray["ABBR"] = "Rövidítés";
$factarray["ADDR"] = "Lakcím";
$factarray["ADR1"] = "Lakcím 1";
$factarray["ADR2"] = "Lakcím 2";
$factarray["ADOP"] = "Örökbefogadás";
$factarray["AFN"]  = "Ősi Álomány Szám (angol AFN)";
$factarray["AGE"]  = "Életkor";
$factarray["AGNC"] = "Képviselet";
$factarray["ALIA"] = "Úgyis mint";
$factarray["ANCE"] = "Ősök";
$factarray["ANCI"] = "Ancestors Interest";
$factarray["ANUL"] = "Házasság felbontása";
$factarray["ASSO"] = "Kapcsolódó személyek";
$factarray["AUTH"] = "Szerző";
$factarray["BAPL"] = "UNSZ-keresztség";
$factarray["BAPM"] = "Keresztelés";
$factarray["BARM"] = "Bar Mitzvah";
$factarray["BASM"] = "Bat Mitzvah";
$factarray["BIRT"] = "Született";
$factarray["BLES"] = "Megáldás";
$factarray["BLOB"] = "Bináris adatok";
$factarray["BURI"] = "Temetés";
$factarray["CALN"] = "Gyűjtemény azonosító";
$factarray["CAST"] = "Szociális/társadalmi státusz";
$factarray["CAUS"] = "A halál oka";
$factarray["CEME"]  = "Temetö";
$factarray["CENS"] = "Összeírás";
$factarray["CHAN"] = "Utolsó módosítás";
$factarray["CHAR"] = "Kódkészlet";
$factarray["CHIL"] = "Gyermek";
$factarray["CHR"]  = "Katolikus Keresztelés";
$factarray["CHRA"] = "Felnőttkori keresztség";
$factarray["CITY"] = "Város";
$factarray["CONF"] = "Konfirmáció";
$factarray["CONL"] = "UNSZ-konfirmáció";
$factarray["COPR"] = "Copyright";
$factarray["CORP"] = "Vállalat/Intézmény";
$factarray["CREM"] = "Hamvasztás";
$factarray["CTRY"] = "Ország";
$factarray["DATE"] = "Dátum";
$factarray["DATA"] = "Adat";
$factarray["DEAT"] = "Elhúnyt";
$factarray["DESC"] = "Leszármazottak";
$factarray["DEST"] = "Cél";
$factarray["DIV"]  = "Válás";
$factarray["DIVF"] = "Válási akta";
$factarray["DSCR"] = "Leírás";
$factarray["EDUC"] = "Végzettség";
$factarray["EMIG"] = "Kivándorlás";
$factarray["ENDL"] = "UNSZ-szertartás (Endowment)";
$factarray["ENGA"] = "Eljegyzés";
$factarray["EVEN"] = "Esemény";
$factarray["FAM"]  = "Család";
$factarray["FAMC"] = "Családtagok (gyermekként)";
$factarray["FAMF"] = "UNSZ családi akta";
$factarray["FAMS"] = "Családtagok (házastársként)";
$factarray["FCOM"] = "Elsőáldozás";
$factarray["FILE"] = "Külső adatállomány";
$factarray["FORM"] = "Formátum";
$factarray["GIVN"] = "Keresztnév";
$factarray["GRAD"] = "Felsőfokú végzettség";
$factarray["HUSB"]  = "Férj";
$factarray["IDNO"] = "Azonosítószám";
$factarray["IMMI"] = "Bevándorlás";
$factarray["LEGA"] = "Végrendeleti örökös";
$factarray["MARB"] = "Eljegyzés kihirdetése";
$factarray["MARC"] = "Házassági szerződés";
$factarray["MARL"] = "Házassági engedély";
$factarray["MARR"] = "Házasság";
$factarray["MARS"] = "Házasság előtti szerzõdés";
$factarray["MEDI"] = "Médiatípus";
$factarray["NAME"] = "Név";
$factarray["NATI"] = "Nemzetiség";
$factarray["NATU"] = "Honosítás";
$factarray["NCHI"] = "Gyermekek száma";
$factarray["NICK"] = "Becenév";
$factarray["NMR"]  = "Házasságkötések száma";
$factarray["NOTE"] = "Jegyzet";
$factarray["NPFX"] = "Előtag";
$factarray["NSFX"] = "Utótag";
$factarray["OBJE"] = "Multimédia-elem";
$factarray["OCCU"] = "Foglalkozás";
$factarray["ORDI"] = "UNSZ-szertartás";
$factarray["ORDN"] = "Pappá szentelés";
$factarray["PAGE"] = "Hivatkozás";
$factarray["PEDI"] = "Felmenő rokonság";
$factarray["PLAC"] = "Helyszín";
$factarray["PHON"] = "Telefon";
$factarray["POST"] = "Irányítószám";
$factarray["PROB"] = "Végrendelet hitelesítése";
$factarray["PROP"] = "Tulajdon";
$factarray["PUBL"] = "Publikáció";
$factarray["QUAY"] = "Adat-megbízhatóság";
$factarray["REPO"] = "Szervezet";
$factarray["REFN"] = "Hivatkozási szám";
$factarray["RELA"] = "Kapcsolat";
$factarray["RELI"] = "Vallás";
$factarray["RESI"] = "Lakhely";
$factarray["RESN"] = "Korlátozás";
$factarray["RETI"] = "Nyugdíjazás";
$factarray["RFN"]  = "Adat állomány-azonosító";
$factarray["RIN"]  = "Adat azonosítószáma";
$factarray["ROLE"] = "Szerep";
$factarray["SEX"]  = "Nem";
$factarray["SOUR"] = "Forrás";
$factarray["SPFX"] = "Vezetéknév előtagja";
$factarray["SSN"]  = "Társadalombiztosítási azonosító";
$factarray["STAE"] = "Állam";
$factarray["STAT"] = "Státusz";
$factarray["SUBM"] = "Adatszolgáltató";
$factarray["SUBN"] = "Beadvány";
$factarray["SURN"] = "Vezetéknév";
$factarray["TEMP"] = "Templom";
$factarray["TEXT"] = "Szöveg";
$factarray["TIME"] = "Idő";
$factarray["TITL"] = "Cím";
$factarray["TYPE"] = "Típus";
$factarray["WIFE"]  = "Feleség";
$factarray["WILL"] = "Végrendelet";
$factarray["_EMAIL"] = "Email-cím";
$factarray["EMAIL"] = "Email-cím";
$factarray["_TODO"] = "Tennivalók";
$factarray["_UID"]  = "Általános azonosító";
$factarray["_PGVU"] = "Utoljára módosította";
$factarray["SERV"] = "Remote Server";
$factarray["_GEDF"] = "GEDCOM Állomány";
$factarray["_PRIM"] = "Kijelőlt kép";
$factarray["_THUM"] = "Használjuk ezt a képet bélyegképként?";
	 
// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] = "Orvosi adatok";
$factarray["_DEG"]  = "Fokozat";
$factarray["_MILT"] = "Katonai szolgálat";
$factarray["_SEPR"] = "Különélés";
$factarray["_DETS"] = "Egyik házastárs halála";
$factarray["CITN"]  = "Állampolgárság";
$factarray["_FA1"]	= "1. tény";
$factarray["_FA2"]	= "2. tény";
$factarray["_FA3"]	= "3. tény";
$factarray["_FA4"]	= "4. tény";
$factarray["_FA5"]	= "5. tény";
$factarray["_FA6"]	= "6. tény";
$factarray["_FA7"]	= "7. tény";
$factarray["_FA8"]	= "8. tény";
$factarray["_FA9"]	= "9. tény";
$factarray["_FA10"]	= "10. tény";
$factarray["_FA11"]	= "11. tény";
$factarray["_FA12"]	= "12. tény";
$factarray["_FA13"]	= "13. tény";
$factarray["_MREL"]	= "Kapcsolat az Anyához";
$factarray["_FREL"]	= "Kapcsolat az Apához";
$factarray["_MSTAT"]	= "Házasság kezdési státusza";
$factarray["_MEND"]	= "Házasság végzési státusza";

// GEDCOM 5.5.1 related facts
$factarray["FAX"] 	= "Fax";
$factarray["FACT"] 	= "Tény";
$factarray["WWW"] 	= "Honlap";
$factarray["MAP"] 	= "Térkép";
$factarray["LATI"] 	= "Szélességi fok";
$factarray["LONG"] 	= "Hosszúsági fok";
$factarray["FONE"] 	= "Fonetikus";
$factarray["ROMN"] 	= "Katolizált";
$factarray["_HEB"] 	= "Héber";
$factarray["_SCBK"] = "Gyüjtö könyv";
$factarray["_TYPE"] = "Média tipus";
$factarray["_SSHOW"] = "Dia vetítés";

// Rootsmagic
$factarray["_SUBQ"]	= "Rövid változat";
$factarray["_BIBL"] 	= "Irodalomjegyzék";
$factarray["EMAL"]	= "Email cím";

// PAF related facts
$factarray["_NAME"] 	= "Levelezési név";
$factarray["URL"] 	= "Webcím";

// Other common customized facts
$factarray["_ADPF"] 	= "Az apa örökbefogadta";
$factarray["_ADPM"] 	= "Az anya örökbefogadta";
$factarray["_AKA"] 	= "Úgyis mint";
$factarray["_AKAN"]	= "Úgyis mint";
$factarray["_BRTM"]	= "Körülmetélés";
$factarray["_COML"]	= "Általános polgári házasság";
$factarray["_EYEC"] 	= "Szemszín";
$factarray["_FNRL"]	= "Temetés";
$factarray["_HAIR"]	= "Hajszín";
$factarray["_HEIG"] 	= "Magasság";
$factarray["_HOL"]  = "Tüzáldozat";
$factarray["_INTE"]	= "Interred";
$factarray["_MARI"]	= "Házassági szándék";
$factarray["_MBON"]	= "Marriage bond";
$factarray["_MEDC"] 	= "Egészségi állapot";
$factarray["_MILI"] 	= "Katonai szolgálat";
$factarray["_NMR"]	= "Nem házas";
$factarray["_NLIV"]	= "Nincs életben";
$factarray["_NMAR"] 	= "Soha nem házasodott meg";
$factarray["_PRMN"]	= "Ideiglenes szám";
$factarray["_WEIG"] 	= "Testsúly";
$factarray["_YART"]	= "Jarzeit";
$factarray["_MARNM"]	= "Házasult név";
$factarray["_STAT"]	= "Házassági státusz";
$factarray["COMM"]	= "Megjegyzés";
$factarray["MARR_CIVIL"] = "Polgári esküvő";
$factarray["MARR_RELIGIOUS"] = "Egyházi esküvő";
$factarray["MARR_PARTNERS"] = "Élettársi kapcsolat";
$factarray["MARR_UNKNOWN"] = "Házassági tipus nem ismert";
$factarray["_HNM"] = "Héber Név";
$factarray["_DEAT_SPOU"] = "Házastárs halála";
$factarray["_BIRT_CHIL"] = "Gyerkmek születése";
$factarray["_MARR_CHIL"] = "Gyermek házassága";
$factarray["_DEAT_CHIL"] = "Gyermek halála";
$factarray["_BIRT_GCHI"] = "Unoka születése";
$factarray["_MARR_GCHI"] = "Unoka házassága";
$factarray["_DEAT_GCHI"] = "Unoka halála";
$factarray["_MARR_FATH"] = "Apa házassága";
$factarray["_DEAT_FATH"] = "Apa halála";
$factarray["_MARR_MOTH"] = "Anya házassága";
$factarray["_DEAT_MOTH"] = "Anya halála";
$factarray["_BIRT_SIBL"] = "Testvér születése";
$factarray["_MARR_SIBL"] = "Testvér házassága";
$factarray["_DEAT_SIBL"] = "Testvér halála";
$factarray["_BIRT_HSIB"] = "Féltestvér születése";
$factarray["_MARR_HSIB"] = "Féltestvér házassága";
$factarray["_DEAT_HSIB"] = "Féltestvér halála";
$factarray["_DEAT_GPAR"] = "Nagyszülö halála";
$factarray["_BIRT_FSIB"] = "Apa testvére születése";
$factarray["_MARR_FSIB"] = "Apa testvére házassága";
$factarray["_DEAT_FSIB"] = "Apa testvére halála";
$factarray["_BIRT_MSIB"] = "Anya testvére születése";
$factarray["_MARR_MSIB"] = "Anya tesvére házassága";
$factarray["_DEAT_MSIB"] = "Anya testvére halála";
$factarray["_BIRT_COUS"] = "Elsö unokatestvér születése";
$factarray["_MARR_COUS"] = "Elsö unokatestvér házassága";
$factarray["_DEAT_COUS"] = "Elsö unokatestvér halála";

if (file_exists("languages/facts.hu.extra.php")) require "languages/facts.hu.extra.php";
?>
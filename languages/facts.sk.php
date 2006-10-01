<?php
/*=================================================
   charset=utf-8
   Project:	phpGedView
   File:	facts.sk.php
   Author:	John Finlay
   Comments:	Defines an array of GEDCOM codes and the czech name facts that they represent.
   Translation:	Peter Moravčík
   Change Log:	8/5/02 - File Created
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id$
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "Nemáte priamy prístup k súboru so slovenčinou.";
	exit;
}
// -- Priraďte údaju v poli (Gedcom kódu) jeho slovenský význam
$factarray["ABBR"]	= "Skratka";
$factarray["ADDR"]	= "Adresa";
$factarray["ADR1"]	= "Adresa 1";
$factarray["ADR2"]	= "Adresa 2";
$factarray["ADOP"]	= "Adopcia";
///////////////////////////////////////////////////////////////////////////////
$factarray["AFN"]	= "Ancestral File Number (AFN)";
///////////////////////////////////////////////////////////////////////////////
$factarray["AGE"]	= "Vek";
$factarray["AGNC"]	= "Inštitúcia";
$factarray["ALIA"]	= "Alias";
$factarray["ANCE"]	= "Predkovia";
$factarray["ANCI"]	= "O predkoch";
$factarray["ANUL"]	= "Anulovanie";
$factarray["ASSO"]	= "Osoba";
$factarray["AUTH"]	= "Autor";
$factarray["BAPL"]	= "LDS Krst";
$factarray["BAPM"]	= "Krst";
$factarray["BARM"]	= "Obrad dospelosti židovského chlapca";
$factarray["BASM"]	= "Obrad dospelosti židovského dievčaťa";
$factarray["BIRT"]	= "Narodenie";
$factarray["BLES"]	= "Požehnanie";
$factarray["BLOB"]	= "Binárny datový objekt";
$factarray["BURI"]	= "Pohreb";
$factarray["CALN"]	= "Signatúra";
$factarray["CAST"]	= "Kasta / Spoločenské postavenie";
$factarray["CAUS"]	= "Príčina smrti";
$factarray["CEME"]  = "Cintorín";
$factarray["CENS"]	= "Sčítanie ľudu";
$factarray["CHAN"]	= "Posledná úprava";
$factarray["CHAR"]	= "Znaková sada";
$factarray["CHIL"]	= "Dieťa";
$factarray["CHR"]	= "Krst (kresťanský)";
$factarray["CHRA"]	= "Krst v dospelosti";
$factarray["CITY"]	= "Mesto";
$factarray["CONF"]	= "Birmovanie";
$factarray["CONL"]	= "LDS Birmovanie";
$factarray["COPR"]	= "Copyright";
$factarray["CORP"]	= "Spoločnosť / firma";
$factarray["CREM"]	= "Kremácia";
$factarray["CTRY"]	= "Krajina";
$factarray["DATA"]	= "Dáta";
$factarray["DATE"]	= "Dátum";
$factarray["DEAT"]	= "Úmrtie";
$factarray["DESC"]	= "Potomkovia";
$factarray["DESI"]	= "O potomkoch";
$factarray["DEST"]	= "Cieľ";
$factarray["DIV"]	= "Rozvod";
$factarray["DIVF"]	= "Rozvodový spis";
$factarray["DSCR"]	= "Popis";
$factarray["EDUC"]	= "Vzdelanie";
$factarray["EMIG"]	= "Emigrácia";
///////////////////////////////////////////////////////////////////////////////
$factarray["ENDL"]	= "LDS Endowment";
///////////////////////////////////////////////////////////////////////////////
$factarray["ENGA"]	= "Zasnúbenie";
$factarray["EVEN"]	= "Udalosti";
$factarray["FAM"]	= "Rodina";
$factarray["FAMC"]	= "Rodina (ako dieťa)";
$factarray["FAMF"]	= "Súbory rodiny";
$factarray["FAMS"]	= "Rodina (ako partnera)";
$factarray["FCOM"]	= "Prvé príjimanie";
$factarray["FILE"]	= "Externý súbor";
$factarray["FORM"]	= "Formát";
$factarray["GIVN"]	= "Krstné meno(á)";
$factarray["GRAD"]	= "Promócia";
$factarray["IDNO"]	= "Identifikačné číslo";
$factarray["IMMI"]	= "Imigrácia";
$factarray["LEGA"]	= "Dedictvo";
$factarray["MARB"]	= "Ohláška (manželstva)";
$factarray["MARC"]	= "Manželská zmluva";
$factarray["MARL"]	= "Povolenie manželstva";
$factarray["MARR"]	= "Sňatok";
$factarray["MARS"]	= "Manželská dohoda";
$factarray["MEDI"]	= "Typ média";
$factarray["NAME"]	= "Meno";
$factarray["NATI"]	= "Národnosť";
$factarray["NATU"]	= "Udelenie občianstva";
$factarray["NCHI"]	= "Počet detí";
$factarray["NICK"]	= "Prezývka";
$factarray["NMR"]	= "Počet sňatkov";
$factarray["NOTE"]	= "Poznámka";
$factarray["NPFX"]	= "Prefix";
$factarray["NSFX"]	= "Suffix";
$factarray["OBJE"]	= "Multimediálny objekt";
$factarray["OCCU"]	= "Povolanie";
$factarray["ORDI"]	= "Ustanovenie";
$factarray["ORDN"]	= "Vysvetenie na kňaza";
$factarray["PAGE"]	= "O citácii";
$factarray["PEDI"]	= "Rodokmeň";
$factarray["PLAC"]	= "Miesto";
$factarray["PHON"]	= "Telefón";
$factarray["POST"]	= "PSČ";
$factarray["PROB"]	= "Súdne overenie poslednej vôle";
$factarray["PROP"]	= "Vlastníctvo";
$factarray["PUBL"]	= "Vydal";
$factarray["QUAY"]	= "Kvalita dát";
$factarray["REPO"]	= "Zdroj";
$factarray["REFN"]	= "Referenčné číslo";
$factarray["RELA"]	= "Príbuzenský vzťah";
$factarray["RELI"]	= "Náboženstvo";
$factarray["RESI"]	= "Sídlo";
$factarray["RESN"]	= "Zákaz";
$factarray["RETI"]	= "Odchod do dôchodku";
$factarray["RFN"]	= "Súborové číslo záznamu";
$factarray["RIN"]	= "ID číslo záznamu";
$factarray["ROLE"]	= "Postavenie";
$factarray["SEX"]	= "Pohlavie";
$factarray["SLGC"]	= "Vydanie záznamu o narodení (LDS)";
$factarray["SLGS"]	= "Vydanie záznamu o sňatku (LDS)";
$factarray["SOUR"]	= "Zdroj";
$factarray["SPFX"]	= "Prefix pred priezviskom";
///////////////////////////////////////////////////////////////////////////////
$factarray["SSN"]	= "Social Security Number";
///////////////////////////////////////////////////////////////////////////////
$factarray["STAE"]	= "Štát";
$factarray["STAT"]	= "Stav";
$factarray["SUBM"]	= "Prameň (KTO poskytol informáciu)";
$factarray["SUBN"]	= "Rezignácia";
$factarray["SURN"]	= "Priezvisko";
$factarray["TEMP"]	= "Chrám (Temple)";
$factarray["TEXT"]	= "Text";
$factarray["TIME"]	= "Čas";
$factarray["TITL"]	= "Titul";
$factarray["TYPE"]	= "Typ";
$factarray["WILL"]	= "Záveť";
$factarray["_EMAIL"]	= "E-mailová adresa";
$factarray["EMAIL"]	= "E-mailová adresa";
///////////////////////////////////////////////////////////////////////////////
$factarray["_TODO"]	= "To Do Item";
///////////////////////////////////////////////////////////////////////////////
$factarray["_UID"]	= "Univerzálny identifikátor";
$factarray["_PGVU"]	= "Naposledy zmenil(a)";
$factarray["_PRIM"]	= "Zvýraznený obrázok";
$factarray["_THUM"]	= "Použiť tento obrázok ako náhľad?";

// These facts are specific to gedcom exports from Family Tree Maker
$factarray["_MDCL"]	= "Lekársky";
$factarray["_DEG"]	= "Hodnosť";
$factarray["_MILT"]	= "Vojenská služba";
$factarray["_SEPR"]	= "Odlúčenie";
$factarray["_DETS"]	= "Úmrtie jedného z partnerov";
$factarray["CITN"]	= "Občianstvo";
$factarray["_FA1"] = "Údaj 1";
$factarray["_FA2"] = "Údaj 2";
$factarray["_FA3"] = "Údaj 3";
$factarray["_FA4"] = "Údaj 4";
$factarray["_FA5"] = "Údaj 5";
$factarray["_FA6"] = "Údaj 6";
$factarray["_FA7"] = "Údaj 7";
$factarray["_FA8"] = "Údaj 8";
$factarray["_FA9"] = "Údaj 9";
$factarray["_FA10"] = "Údaj 10";
$factarray["_FA11"] = "Údaj 11";
$factarray["_FA12"] = "Údaj 12";
$factarray["_FA13"] = "Údaj 13";
$factarray["_MREL"] = "Vzťah k matke";
$factarray["_FREL"] = "Vzťah k otcovi";
///////////////////////////////////////////////////////////////////////////////
$factarray["_MSTAT"] = "Marriage Beginning Status";
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
$factarray["_MEND"] = "Marriage Ending Status";
///////////////////////////////////////////////////////////////////////////////
$factarray["FAX"] = "FAX";
$factarray["FACT"] = "Údaj";
$factarray["WWW"] = "Domáca stránka";
$factarray["MAP"] = "Mapa";
$factarray["LATI"] = "Zemepisná šírka";
$factarray["LONG"] = "Zemepisná dĺžka";
$factarray["FONE"] = "Fonetický prepis";
$factarray["ROMN"] = "Latinkou";
$factarray["_NAME"] = "Meno na poštových zásielkach";
$factarray["URL"] = "URL stránok";
$factarray["_HEB"] = "Hebrejsky";
$factarray["_SCBK"] = "Album";
$factarray["_TYPE"] = "Typ média";
$factarray["_SSHOW"] = "Slide show";
$factarray["_SUBQ"]= "Skrátene";
$factarray["_BIBL"] = "Bibliografia";


// Other common customized facts
$factarray["_ADPF"]	= "Adoptovaný(á) otcom";
$factarray["_ADPM"]	= "Adoptovaný(á) matkou";
$factarray["_AKAN"]	= "Tiež známy(a) ako";
$factarray["_AKA"] 	= "Tiež známy(a) ako";
$factarray["_BRTM"]	= "Židovský obrad obriezky";
$factarray["_COML"]	= "Civilný sňatok";
$factarray["_EYEC"]	= "Farba očí";
$factarray["_FNRL"]	= "Pohreb";
$factarray["_HAIR"]	= "Farba vlasov";
$factarray["_HEIG"]	= "Výška";
$factarray["_HOL"]  = "Holocaust";
$factarray["_INTE"]	= "Pohreb do hrobu";
$factarray["_MARI"]	= "Oznámenie sňatku";
$factarray["_MBON"]	= "Manželský zväzok";
$factarray["_MEDC"]	= "Zdravotný stav";
$factarray["_MILI"]	= "Vojenská služba";
$factarray["_NMR"]	= "Slobodná/ý";
$factarray["_NLIV"]	= "Nežijúci";
$factarray["_NMAR"]	= "Celý život slobodná/ý";
$factarray["_PRMN"]	= "Číslo občianského preukazu";
$factarray["_WEIG"]	= "Váha";
$factarray["_YART"]	= "Židovský dátum narodenia Yartzeit";
$factarray["_MARNM"]	= "Priezvisko manželov";
$factarray["_STAT"]	= "Rodinný stav";
$factarray["COMM"]	= "Komentár";
$factarray["MARR_CIVIL"] = "Civilný sňatok";
$factarray["MARR_RELIGIOUS"] = "Cirkevný sňatok";
$factarray["MARR_PARTNERS"] = "Registrované partnerstvo";
$factarray["MARR_UNKNOWN"] = "Neznámý typ sňatku";
$factarray["_HNM"] = "Židovské meno";

if (file_exists( "languages/facts.sk.extra.php")) require  "languages/facts.sk.extra.php";

?>
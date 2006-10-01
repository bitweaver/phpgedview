<?php
/**
 * Czech Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Jan Hapala
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
 * @author Jan Hapala
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "Nemáte přímý přístup k souboru s češtinou.";
	exit;
}
// -- Přiřaďte údaji v poli (Gedcom kódu) jeho český význam
$factarray["ABBR"]	= "Zkratka";
$factarray["ADDR"]	= "Adresa";
$factarray["ADR1"]	= "Adresa 1";
$factarray["ADR2"]	= "Adresa 2";
$factarray["ADOP"]	= "Adopce";
///////////////////////////////////////////////////////////////////////////////
$factarray["AFN"]	= "Ancestral File Number (AFN)";
///////////////////////////////////////////////////////////////////////////////
$factarray["AGE"]	= "Věk";
$factarray["AGNC"]	= "Instituce";
$factarray["ALIA"]	= "Alias";
$factarray["ANCE"]	= "Předkové";
$factarray["ANCI"]	= "O předcích";
$factarray["ANUL"]	= "Anulování";
$factarray["ASSO"]	= "Sdružení";
$factarray["AUTH"]	= "Autor";
$factarray["BAPL"]	= "LDS Křest";
$factarray["BAPM"]	= "Křest";
$factarray["BARM"]	= "Obřad dospělosti židovského chlapce";
$factarray["BASM"]	= "Obřad dospělosti židovské dívky";
$factarray["BIRT"]	= "Narození";
$factarray["BLES"]	= "Požehnání";
$factarray["BLOB"]	= "Binární datový objekt";
$factarray["BURI"]	= "Pohřeb";
$factarray["CALN"]	= "Signatura";
$factarray["CAST"]	= "Kasta / Společenské postavení";
$factarray["CAUS"]	= "Příčina smrti";
$factarray["CENS"]	= "Sčítání lidu";
$factarray["CHAN"]	= "Poslední úprava";
$factarray["CHAR"]	= "Znaková sada";
$factarray["CHIL"]	= "Dítě";
$factarray["CHR"]	= "Křest (křesťanský)";
$factarray["CHRA"]	= "Křest v dospělosti";
$factarray["CITY"]	= "Město";
$factarray["CONF"]	= "Biřmování";
$factarray["CONL"]	= "LDS Biřmování";
$factarray["COPR"]	= "Copyright";
$factarray["CORP"]	= "Společnost / firma";
$factarray["CREM"]	= "Kremace";
$factarray["CTRY"]	= "Země";
$factarray["DATA"]	= "Data";
$factarray["DATE"]	= "Datum";
$factarray["DEAT"]	= "Úmrtí";
$factarray["DESC"]	= "Potomci";
$factarray["DESI"]	= "O potomcích";
$factarray["DEST"]	= "Cíl";
$factarray["DIV"]	= "Rozvod";
$factarray["DIVF"]	= "Rozvodový spis";
$factarray["DSCR"]	= "Popis";
$factarray["EDUC"]	= "Vzdělání";
$factarray["EMIG"]	= "Emigrace";
///////////////////////////////////////////////////////////////////////////////
$factarray["ENDL"]	= "LDS Endowment";
///////////////////////////////////////////////////////////////////////////////
$factarray["ENGA"]	= "Zasnoubení";
$factarray["EVEN"]	= "Události";
$factarray["FAM"]	= "Rodina";
$factarray["FAMC"]	= "Rodina (jako dítěte)";
$factarray["FAMF"]	= "Soubory rodiny";
$factarray["FAMS"]	= "Rodina (jako partnera)";
$factarray["FCOM"]	= "První příjímání";
$factarray["FILE"]	= "Externí soubor";
$factarray["FORM"]	= "Formát";
$factarray["GIVN"]	= "Křestní jméno(a)";
$factarray["GRAD"]	= "Promoce";
$factarray["IDNO"]	= "Identifikační číslo";
$factarray["IMMI"]	= "Imigrace";
$factarray["LEGA"]	= "Dědictví";
$factarray["MARB"]	= "Ohláška (manželství)";
$factarray["MARC"]	= "Manželská smlouva";
$factarray["MARL"]	= "Povolení manželství";
$factarray["MARR"]	= "Sňatek";
$factarray["MARS"]	= "Manželská dohoda";
$factarray["MEDI"]	= "Typ média";
$factarray["NAME"]	= "Jméno";
$factarray["NATI"]	= "Národnost";
$factarray["NATU"]	= "Udělení občanství";
$factarray["NCHI"]	= "Počet dětí";
$factarray["NICK"]	= "Přezdívka";
$factarray["NMR"]	= "Počet sňatků";
$factarray["NOTE"]	= "Poznámka";
$factarray["NPFX"]	= "Prefix";
$factarray["NSFX"]	= "Suffix";
$factarray["OBJE"]	= "Multimediální objekt";
$factarray["OCCU"]	= "Povolání";
$factarray["ORDI"]	= "Ustanovení";
$factarray["ORDN"]	= "Vysvěcení na kněze";
$factarray["PAGE"]	= "O citaci";
$factarray["PEDI"]	= "Rodokmen";
$factarray["PLAC"]	= "Místo";
$factarray["PHON"]	= "Telefon";
$factarray["POST"]	= "PSČ";
$factarray["PROB"]	= "Soudní ověření poslední vůle";
$factarray["PROP"]	= "Vlastnictví";
$factarray["PUBL"]	= "Vydal";
$factarray["QUAY"]	= "Kvalita dat";
$factarray["REPO"]	= "Zdroj";
$factarray["REFN"]	= "Referenční číslo";
$factarray["RELA"]	= "Příbuzenský vztah";
$factarray["RELI"]	= "Náboženství";
$factarray["RESI"]	= "Sídlo";
$factarray["RESN"]	= "Zákaz";
$factarray["RETI"]	= "Odchod do důchodu";
$factarray["RFN"]	= "Souborové číslo záznamu";
$factarray["RIN"]	= "ID číslo záznamu";
$factarray["ROLE"]	= "Postavení";
$factarray["SEX"]	= "Pohlaví";
$factarray["SLGC"]	= "Vydání záznamu o narození (LDS)";
$factarray["SLGS"]	= "Vydání záznamu o sňatku (LDS)";
$factarray["SOUR"]	= "Pramen";
$factarray["SPFX"]	= "Prefix před příjmením";
///////////////////////////////////////////////////////////////////////////////
$factarray["SSN"]	= "Social Security Number";
///////////////////////////////////////////////////////////////////////////////
$factarray["STAE"]	= "Stát";
$factarray["STAT"]	= "Stav";
$factarray["SUBM"]	= "Pramen (KDO poskytl informaci)";
$factarray["SUBN"]	= "Rezignace";
$factarray["SURN"]	= "Příjmení";
$factarray["TEMP"]	= "Chrám (Temple)";
$factarray["TEXT"]	= "Text";
$factarray["TIME"]	= "Čas";
$factarray["TITL"]	= "Titul";
$factarray["TYPE"]	= "Typ";
$factarray["WILL"]	= "Závěť";
$factarray["_EMAIL"]	= "Emailová adresa";
$factarray["EMAIL"]	= "Emailová adresa";
///////////////////////////////////////////////////////////////////////////////
$factarray["_TODO"]	= "To Do Item";
///////////////////////////////////////////////////////////////////////////////
$factarray["_UID"]	= "Univerzální identifikátor";
$factarray["_PGVU"]	= "Naposledy změnil(a)";
$factarray["_PRIM"]	= "Zvýrazněný obrázek";
$factarray["_THUM"]	= "Použít tento obrázek jako náhled?";

// These facts are specific to gedcom exports from Family Tree Maker
$factarray["_MDCL"]	= "Lékařský";
$factarray["_DEG"]	= "Hodnost";
$factarray["_MILT"]	= "Vojenská služba";
$factarray["_SEPR"]	= "Odloučení";
$factarray["_DETS"]	= "Úmrtí jednoho z partnerů";
$factarray["CITN"]	= "Občanství";
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
$factarray["_MREL"] = "Vztah k matce";
$factarray["_FREL"] = "Vztah k otci";
///////////////////////////////////////////////////////////////////////////////
$factarray["_MSTAT"] = "Marriage Beginning Status";
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
$factarray["_MEND"] = "Marriage Ending Status";
///////////////////////////////////////////////////////////////////////////////
$factarray["FAX"] = "FAX";
$factarray["FACT"] = "Údaj";
$factarray["WWW"] = "Domácí stránka";
$factarray["MAP"] = "Mapa";
$factarray["LATI"] = "Zeměpsiná šířka";
$factarray["LONG"] = "Zeměpisná délka";
$factarray["FONE"] = "Fonetický přepis";
$factarray["ROMN"] = "Latinkou";
$factarray["_NAME"] = "Jméno na poštovních zásilkách";
$factarray["URL"] = "URL stránek";
$factarray["_HEB"] = "Hebrejsky";
$factarray["_SUBQ"]= "Zkráceně";
$factarray["_BIBL"] = "Bibliografie";


// Other common customized facts
$factarray["_ADPF"]	= "Adoptován(a) otcem";
$factarray["_ADPM"]	= "Adoptován(a) matkou";
$factarray["_AKAN"]	= "Také znám(a) jako";
$factarray["_AKA"] 	= "Také znám(a) jako";
$factarray["_BRTM"]	= "Židovský obřad obřízky";
$factarray["_COML"]	= "Civilní sňatek";
$factarray["_EYEC"]	= "Barva očí";
$factarray["_FNRL"]	= "Pohřeb";
$factarray["_HAIR"]	= "Barva vlasů";
$factarray["_HEIG"]	= "Výška";
$factarray["_INTE"]	= "Pohřeb do hrobu";
$factarray["_MARI"]	= "Oznámení sňatku";
$factarray["_MBON"]	= "Manželský svazek";
$factarray["_MEDC"]	= "Zdravotní stav";
$factarray["_MILI"]	= "Vojenská služba";
$factarray["_NMR"]	= "Svobodná/ý";
$factarray["_NLIV"]	= "Nežijící";
$factarray["_NMAR"]	= "Celý život svobodná/ý";
$factarray["_PRMN"]	= "Číslo občanského průkazu";
$factarray["_WEIG"]	= "Váha";
$factarray["_YART"]	= "Židovské datum narození Yartzeit";
$factarray["_MARNM"]	= "Příjmení manželů";
$factarray["_STAT"]	= "Rodinný stav";
$factarray["COMM"]	= "Komentář";

if (file_exists("languages/facts.en.extra.php")) require "languages/facts.en.extra.php";

?>
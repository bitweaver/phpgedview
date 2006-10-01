<?php
/**
 * Norwegian Configure Text File
 *
 * This file contains the Norwegian text for the PGV Configure system.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Geir Håkon Eikland
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
 * # $Id$
 * @author Geir Håkon Eikland
 * @package PhpGedView
 * @subpackage Languages
 */

if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their norwegian values
$factarray["ABBR"] = "Forkortelse";
$factarray["ADDR"] = "Adresse";
$factarray["ADR1"] = "Adresse 1";
$factarray["ADR2"] = "Adresse 2";
$factarray["ADOP"] = "Adoptert";
$factarray["AFN"]  = "Slekts-filnr. Mormoner";
$factarray["AGE"]  = "Alder";
$factarray["AGNC"] = "Agentur";
$factarray["ALIA"] = "Alias";
$factarray["ANCE"] = "Forfedre";
$factarray["ANCI"] = "Mangler forfedre...";
$factarray["ANUL"] = "Annulert ekteskap";
$factarray["ASSO"] = "Forbindelser";
$factarray["AUTH"] = "Forfatter";
$factarray["BAPL"] = "Døpt Mormoner";
$factarray["BAPM"] = "Døpt på bekjennelse";
$factarray["BARM"] = "Bar Mitzvah Jødisk sermoni gutt";
$factarray["BASM"] = "Bat Mitzvah Jødisk sermoni jente";
$factarray["BIRT"] = "Født";
$factarray["BLES"] = "Velsignet / Navnefest";
$factarray["BLOB"] = "Binært dataobjekt";
$factarray["BURI"] = "Gravlagt";
$factarray["CALN"] = "Arkivnr./ISBN/ISSN";
$factarray["CAST"] = "Kaste / Sosial status";
$factarray["CAUS"] = "Dødsårsak";
$factarray["CEME"] = "Kirkegård";
$factarray["CENS"] = "Folketelling";
$factarray["CHAN"] = "Sist endret";
$factarray["CHAR"] = "Tegnsett";
$factarray["CHIL"] = "Barn";
$factarray["CHR"]  = "Døpt som barn";
$factarray["CHRA"] = "Døpt som voksen";
$factarray["CITY"] = "Sted/by";
$factarray["CONF"] = "Konfirmert";
$factarray["CONL"] = "Konfirmert Mormoner";
$factarray["COPR"] = "Opphavsrett / Copyright";
$factarray["CORP"] = "Bedrift-/firmanavn";
$factarray["CREM"] = "Kremert";
$factarray["CTRY"] = "Land";
$factarray["DATE"] = "Dato";
$factarray["DATA"] = "Data";
$factarray["DEAT"] = "Død";
$factarray["DESC"] = "Etterkommere";
$factarray["DESI"] = "Mangler etterkommer(e)...";
$factarray["DEST"] = "Mål";
$factarray["DIV"]  = "Skilsmisse";
$factarray["DIVF"] = "Skilsmissebegjæring";
$factarray["DSCR"] = "Beskrivelse";
$factarray["EDUC"] = "Utdannelse";
$factarray["EMIG"] = "Utvandret";
$factarray["ENDL"] = "Gave Mormorer";
$factarray["ENGA"] = "Forlovet";
$factarray["EVEN"] = "Hendelse";
$factarray["FAM"]  = "Familie";
$factarray["FAMC"] = "Familie-ID for barn";
$factarray["FAMF"] = "Familiekort til Mormonere";
$factarray["FAMS"] = "Familie-ID for ektefelle/partner";
$factarray["FCOM"] = "Første nattverd-måltid";
$factarray["FILE"] = "Ekstern fil";
$factarray["FORM"] = "Format / type";
$factarray["GIVN"] = "Fornavn";
$factarray["GRAD"] = "Uteksaminert";
$factarray["HUSB"] = "Ektemann";
$factarray["IDNO"] = "Person-ID-nr.";
$factarray["IMMI"] = "Innvandret";
$factarray["LEGA"] = "Arving";
$factarray["MARB"] = "Lysing av giftemål";
$factarray["MARC"] = "Ekteskapskontrakt";
$factarray["MARL"] = "Ekteskapsattest";
$factarray["MARR"] = "Ekteskap";
$factarray["MARS"] = "Ekteskapsavtale";
$factarray["MEDI"] = "Media-type";
$factarray["NAME"] = "Navn";
$factarray["NATI"] = "Nasjonalitet";
$factarray["NATU"] = "Statsborgerskap";
$factarray["NCHI"] = "Antall barn";
$factarray["NICK"] = "Klengenavn";
$factarray["NMR"]  = "Antall ekteskap";
$factarray["NOTE"] = "Note";
$factarray["NPFX"] = "Prefiks";
$factarray["NSFX"] = "Postfiks";
$factarray["OBJE"] = "Multimedia objekt";
$factarray["OCCU"] = "Yrke";
$factarray["ORDI"] = "Rituale rel. tjeneste";
$factarray["ORDN"] = "Ordinert rel. tjeneste";
$factarray["PAGE"] = "Dokument referanse";
$factarray["PEDI"] = "Slektsgrein";
$factarray["PLAC"] = "Stedsnavn";
$factarray["PHON"] = "Tlf.nr.";
$factarray["POST"] = "Postnummer";
$factarray["PROB"] = "Skifte";
$factarray["PROP"] = "Eiendom";
$factarray["PUBL"] = "Publikasjon";
$factarray["QUAY"] = "Datakvalitet (0-3)";
$factarray["REPO"] = "Oppbevaringssted";
$factarray["REFN"] = "Referensenummer";
$factarray["RELA"] = "Slektskap";
$factarray["RELI"] = "Religion";
$factarray["RESI"] = "Bosted";
$factarray["RESN"] = "Restriksjon";
$factarray["RETI"] = "Pensjonert";
$factarray["RFN"]  = "Ref.nr. (statisk)";
$factarray["RIN"]  = "Ref.nr. (dynamisk)";
$factarray["ROLE"] = "Rolle i hendelse";
$factarray["SEX"]  = "Kjønn";
$factarray["SLGC"] = "Barn-kobling Mormoner";
$factarray["SLGS"] = "Ekteskap-kobling Mormoner";
$factarray["SOUR"] = "Kilde";
$factarray["SPFX"] = "Etternavn prefiks";
$factarray["SSN"]  = "Personnummer";
$factarray["STAE"] = "Stat/Region";
$factarray["STAT"] = "Status";
$factarray["SUBM"] = "Bidragsgiver/Avsender";
$factarray["SUBN"] = "Del av datasamling";
$factarray["SURN"] = "Etternavn";
$factarray["TEMP"] = "Mormoner kirkekode";
$factarray["TEXT"] = "Kildetekst";
$factarray["TIME"] = "Klokkeslett";
$factarray["TITL"] = "Tittel";
$factarray["TYPE"] = "Type";
$factarray["WIFE"] = "Hustru";
$factarray["WILL"] = "Testamente";
$factarray["_EMAIL"] = "E-post-adresse";
$factarray["EMAIL"] = "E-post-adresse";
$factarray["_TODO"] = "Utestående gjøremål";
$factarray["_UID"] = "Universell ID";
$factarray["_PGVU"]	= "Sist oppdatert av";
$factarray["_PRIM"]	= "Merket som hovedbilde";
$factarray["_THUM"]	= "Bruke dette bilde som passfoto?";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] = "Helse";
$factarray["_DEG"] 	= "Akademisk grad";
$factarray["_MILT"] = "Militærtjeneste";
$factarray["_SEPR"] = "Separert";
$factarray["_DETS"] = "Ektefelles død";
$factarray["CITN"] 	= "Statsborgerskap";
$factarray["_FA1"]	= "Fakta 1";
$factarray["_FA2"]	= "Fakta 2";
$factarray["_FA3"]	= "Fakta 3";
$factarray["_FA4"]	= "Fakta 4";
$factarray["_FA5"]	= "Fakta 5";
$factarray["_FA6"]	= "Fakta 6";
$factarray["_FA7"]	= "Fakta 7";
$factarray["_FA8"]	= "Fakta 8";
$factarray["_FA9"]	= "Fakta 9";
$factarray["_FA10"]	= "Fakta 10";
$factarray["_FA11"]	= "Fakta 11";
$factarray["_FA12"]	= "Fakta 12";
$factarray["_FA13"]	= "Fakta 13";
$factarray["_MREL"]	= "Tilknytning til mor";
$factarray["_FREL"]	= "Tilknytning til far";
$factarray["_MSTAT"] = "Ekteskap start-status";
$factarray["_MEND"]	= "Ekteskap slutt-status";

// GEDCOM 5.5.1 related facts
$factarray["FAX"]	= "Faks";
$factarray["FACT"]	= "Fakta";
$factarray["WWW"]	= "Hjemmeside";
$factarray["MAP"]	= "Kart";
$factarray["LATI"]	= "Breddegrad";
$factarray["LONG"]	= "Lengdegrad";
$factarray["FONE"]	= "Fonetisk";
$factarray["ROMN"]	= "Romanisert";

// PAF related facts
$factarray["_NAME"]	= "Navn på postmottager";
$factarray["URL"]	= "URL (internett-adresse)";
$factarray["_HEB"]	= "Hebraisk";
$factarray["_SCBK"] = "Utklippsbok";
$factarray["_TYPE"] = "Media-type";
$factarray["_SSHOW"] = "Lysbildeframvining";

// Rootsmagic
$factarray["_SUBQ"]	= "Kortversjon";
$factarray["_BIBL"] = "Bibliografi";

// Other common customized facts
$factarray["_ADPF"] = "Adopteret av faren";
$factarray["_ADPM"] = "Adopteret av moren";
$factarray["_AKAN"] = "Også kjent som";
$factarray["_AKA"] 	= "Også kjent som";
$factarray["_BRTM"] = "Brit mila Jødisk omskjæring";
$factarray["_COML"]	= "Samboerskap";
$factarray["_EYEC"] = "Øyefarge";
$factarray["_FNRL"] = "Bisettelse";
$factarray["_HAIR"] = "Hårfarve";
$factarray["_HEIG"] = "Høyde";
$factarray["_HOL"]  = "Holocaust";
$factarray["_INTE"] = "Urnenedsettelse";
$factarray["_MARI"] = "Ekteskaps-intensjon";
$factarray["_MBON"] = "Ekteskapsgaranti";
$factarray["_MEDC"] = "Helsetilstand";
$factarray["_MILI"] = "Milit&oelig;rtjeneste";
$factarray["_NMR"] = "Ikke gift";
$factarray["_NLIV"] = "Lever ikke";
$factarray["_NMAR"] = "Aldri gift";
$factarray["_PRMN"] = "Permanent nummer";
$factarray["_WEIG"] = "Vekt";
$factarray["_YART"] = "Yartzeit Jødisk fødselsdag";
$factarray["_MARNM"] = "Navn som gift";
$factarray["_STAT"]	= "Sivilstatus";
$factarray["COMM"]	= "Kommentar";

// Aldfaer related facts
$factarray["MARR_CIVIL"] = "Borgelig vielse";
$factarray["MARR_RELIGIOUS"] = "Kirkelig vielse";
$factarray["MARR_PARTNERS"] = "Registert partnerskap";
$factarray["MARR_UNKNOWN"] = "Ukjent form for vielse";

$factarray["_HNM"] = "Hebraisk navn";

// Pseudo-facts for relatives
$factarray["_DEAT_SPOU"] = "Dødsfall til ektefelle";

$factarray["_BIRT_CHIL"] = "Fødsel til barn";
$factarray["_MARR_CHIL"] = "Ekteskap til barn";
$factarray["_DEAT_CHIL"] = "Dødsfall til barn";

$factarray["_BIRT_GCHI"] = "Fødsel til barnebarn";
$factarray["_MARR_GCHI"] = "Ekteskap til barnenarn";
$factarray["_DEAT_GCHI"] = "Dødsfall til barnebarn";

$factarray["_MARR_FATH"] = "Ekteskap til faren";
$factarray["_DEAT_FATH"] = "Dødsfall til faren";

$factarray["_MARR_MOTH"] = "Ekteskap til moren";
$factarray["_DEAT_MOTH"] = "Dødsfall til moren";

$factarray["_BIRT_SIBL"] = "Fødsel<br />- en av søsknene";
$factarray["_MARR_SIBL"] = "Ekteskap<br />- en av søsknene";
$factarray["_DEAT_SIBL"] = "Dødsfall<br />- en av søsknene";

$factarray["_BIRT_HSIB"] = "Fødsel<br />- en av halvsøsknene";
$factarray["_MARR_HSIB"] = "Ekteskap<br />- en av halvsøsknene";
$factarray["_DEAT_HSIB"] = "Dødsfall<br />- en av halvsøsknene";

$factarray["_DEAT_GPAR"] = "Dødsfall<br />- en av besteforeldrene";

$factarray["_BIRT_FSIB"] = "Fødsel<br />- en av søsknene til faren";
$factarray["_MARR_FSIB"] = "Ekteskap<br />- en av søknene til faren";
$factarray["_DEAT_FSIB"] = "Dødsfall<br />- en av søsknene til faren";

$factarray["_BIRT_MSIB"] = "Fødsel<br />- en av søsknene til moren";
$factarray["_MARR_MSIB"] = "Ekteskap<br />- en av søsknene til moren";
$factarray["_DEAT_MSIB"] = "Dødsfall<br />- en av søsknene til moren";

if (file_exists( "languages/facts.no.extra.php")) require  "languages/facts.no.extra.php";

?>
<?php
/**
 * Swedish Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Patrik Hansson
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
 * @author Patrik Hansson
 * @author Daniel Melander
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their swedish values
$factarray["ABBR"] = "Förkortning";
$factarray["ADDR"] = "Adress";
$factarray["ADR1"] = "Adress 1";
$factarray["ADR2"] = "Adress 2";
$factarray["ADOP"] = "Adoption";
$factarray["AFN"]  = "Ancestral File Number (AFN)";
$factarray["AGE"]  = "Ålder";
$factarray["AGNC"] = "Myndighet";
$factarray["ALIA"] = "Alias";
$factarray["ANCE"] = "Anor";
$factarray["ANCI"] = "Intressant anfader";
$factarray["ANUL"] = "Ogiltigförklaring";
$factarray["ASSO"] = "Kompanjoner";
$factarray["AUTH"] = "Författare";
$factarray["BAPL"] = "LDS dop";
$factarray["BAPM"] = "Döpt";
$factarray["BARM"] = "Bar Mitzvah";
$factarray["BASM"] = "Bas Mitzvah";
$factarray["BIRT"] = "Född";
$factarray["BLES"] = "Välsignelse";
$factarray["BLOB"] = "Binärt data objekt";
$factarray["BURI"] = "Begravd";
$factarray["CALN"] = "Arkivnummer";
$factarray["CAST"] = "Samhällsklass / Social status";
$factarray["CAUS"] = "Dödsorsak";
$factarray["CEME"]  = "Kyrkogård";
$factarray["CENS"] = "Folkräkning";
$factarray["CHAN"] = "Senast ändrad";
$factarray["CHAR"] = "Teckentabell";
$factarray["CHIL"] = "Barn";
$factarray["CHR"]  = "Döpt";
$factarray["CHRA"] = "Vuxendop";
$factarray["CITY"] = "Stad";
$factarray["CONF"] = "Konfirmation";
$factarray["CONL"] = "LDS konfirmation";
$factarray["COPR"] = "Upphovsrätt";
$factarray["CORP"] = "Företag";
$factarray["CREM"] = "Kremering";
$factarray["CTRY"] = "Land";
$factarray["DATA"] = "Data";
$factarray["DATE"] = "Datum";
$factarray["DEAT"] = "Död";
$factarray["DESC"] = "Ättlingar";
$factarray["DESI"] = "Intressant ättling";
$factarray["DEST"] = "Destination";
$factarray["DIV"]  = "Skild";
$factarray["DIVF"] = "Ansökt om skilsmässa";
$factarray["DSCR"] = "Fysisk beskrivning";
$factarray["EDUC"] = "Utbildning";
$factarray["EMIG"] = "Utvandring";
$factarray["ENDL"] = "LDS Gåva";
$factarray["ENGA"] = "Förlovning";
$factarray["EVEN"] = "Händelse";
$factarray["FAM"]  = "Familj";
$factarray["FAMC"] = "Familj som barn";
$factarray["FAMF"] = "Familjefil";
$factarray["FAMS"] = "Familj som make/a";
$factarray["FCOM"] = "Första nattvarden";
$factarray["FILE"] = "Extern fil";
$factarray["FORM"] = "Format";
$factarray["GIVN"] = "Förnamn";
$factarray["GRAD"] = "Examen";
$factarray["HUSB"]  = "Make";
$factarray["IDNO"] = "Personnummer";
$factarray["IMMI"] = "Invandring";
$factarray["LEGA"] = "Förmånstagare";
$factarray["MARB"] = "Lysning";
$factarray["MARC"] = "Äktenskapsförord";
$factarray["MARL"] = "Äktenskapsbevis";
$factarray["MARR"] = "Vigd";
$factarray["MARS"] = "Äktenskapsförord";
$factarray["MEDI"]	= "Mediatyp";
$factarray["NAME"] = "Namn";
$factarray["NATI"] = "Nationalitet";
$factarray["NATU"] = "Medborgarskap";
$factarray["NCHI"] = "Antal barn";
$factarray["NICK"] = "Smeknamn";
$factarray["NMR"]  = "Antal äktenskap";
$factarray["NOTE"] = "Anteckning";
$factarray["NPFX"] = "Prefix";
$factarray["NSFX"] = "Suffix";
$factarray["OBJE"] = "Multimediaobjekt";
$factarray["OCCU"] = "Yrke";
$factarray["ORDI"] = "Ritual";
$factarray["ORDN"] = "Ordination";
$factarray["PAGE"] = "Citatdetaljer";
$factarray["PEDI"] = "Antavla";
$factarray["PLAC"] = "Ort";
$factarray["PHON"] = "Telefon";
$factarray["POST"] = "Postnummer";
$factarray["PROB"] = "Styrka testamente";
$factarray["PROP"] = "Egendom";
$factarray["PUBL"] = "Publicering";
$factarray["QUAY"] = "Kvalitet på källa";
$factarray["REPO"] = "Lagringsplats";
$factarray["REFN"] = "Referensnummer";
$factarray["RELA"]	= "Släktskap";
$factarray["RELI"] = "Religion";
$factarray["RESI"] = "Bosatt";
$factarray["RESN"] = "Restriktion";
$factarray["RETI"] = "Pensionering";
$factarray["RFN"]  = "Postens fil-number";
$factarray["RIN"]  = "Postens ID-nummer";
$factarray["ROLE"] = "Roll";
$factarray["SEX"]  = "Kön";
$factarray["SLGC"] = "LDS Child Sealing";
$factarray["SLGS"] = "LDS Spouse Sealing";
$factarray["SOUR"] = "Källa";
$factarray["SPFX"] = "Efternamnsprefix";
$factarray["SSN"]  = "Social Security Number(US)";
$factarray["STAE"] = "Stat/Län";
$factarray["STAT"] = "Status";
$factarray["SUBM"] = "Bidragsgivare";
$factarray["SUBN"] = "Inlämna";
$factarray["SURN"] = "Efternamn";
$factarray["TEMP"] = "Tempel";
$factarray["TEXT"] = "Text";
$factarray["TIME"] = "Tid";
$factarray["TITL"] = "Titel";
$factarray["TYPE"] = "Typ";
$factarray["WIFE"]  = "Maka";
$factarray["WILL"] = "Testamente";
$factarray["_EMAIL"] = "E-postadress";
$factarray["EMAIL"] = "E-postadress";
$factarray["_TODO"]  = "Att-göra-post";
$factarray["_UID"]   = "Unik identifierare";
$factarray["_PGVU"]	= "Senast ändrad av";
$factarray["SERV"] = "Annan server";
$factarray["_GEDF"] = "GEDCOM-fil";
$factarray["_PRIM"]	= "Huvudbild";
$factarray["_THUM"]	= "Använd denna bild som miniatyr?";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] = "Medicinsk";
$factarray["_DEG"]  = "Betyg";
$factarray["_MILT"] = "Militärtjänst";
$factarray["_SEPR"] = "Separerade";
$factarray["_DETS"] = "En make(a) död";
$factarray["CITN"] = "Medborgarskap";
$factarray["_MREL"] = "Förhållande till modern";
$factarray["_FREL"] = "Förhållande till fadern";
$factarray["_FA1"] = "Faktum 1";
$factarray["_FA2"] = "Faktum 2";
$factarray["_FA3"] = "Faktum 3";
$factarray["_FA4"] = "Faktum 4";
$factarray["_FA5"] = "Faktum 5";
$factarray["_FA6"] = "Faktum 6";
$factarray["_FA7"] = "Faktum 7";
$factarray["_FA8"] = "Faktum 8";
$factarray["_FA9"] = "Faktum 9";
$factarray["_FA10"] = "Faktum 10";
$factarray["_FA11"] = "Faktum 11";
$factarray["_FA12"] = "Faktum 12";
$factarray["_FA13"] = "Faktum 13";
$factarray["_MSTAT"] = "Vigsel startstatus";
$factarray["_MEND"] = "Vigsel slutstatus";
$factarray["FAX"] = "FAX";
$factarray["FACT"] = "FAKTA";
$factarray["WWW"] = "Hemsida";
$factarray["MAP"] = "Karta";
$factarray["LATI"] = "Latitud";
$factarray["LONG"] = "Longitud";
$factarray["FONE"] = "Fonetisk";
$factarray["ROMN"] = "Romaniserad";
$factarray["_NAME"] = "Adressat";
$factarray["URL"] = "Web URL";
$factarray["_HEB"] = "Hebreiska";
$factarray["_SCBK"] = "Urklippsalbum";
$factarray["_TYPE"] = "Mediatyp";
$factarray["_SSHOW"] = "Bildspel";
$factarray["_SUBQ"]= "Kortversion";
$factarray["_BIBL"] = "Bibliografi";
$factarray["EMAL"]	= "Epostadress";

// Other common customized facts
$factarray["_ADPF"] = "Adopterad av fadern";
$factarray["_ADPM"] = "Adpoterad av modern";
$factarray["_AKAN"] = "Också känd som";
$factarray["_AKA"] 	= "Också känd som";
$factarray["_BRTM"] = "Brit mila";
$factarray["_COML"] = "Sambo";
$factarray["_EYEC"] = "Ögonfärg";
$factarray["_FNRL"] = "Begravning";
$factarray["_HAIR"] = "Hårfärg";
$factarray["_HEIG"] = "Längd";
$factarray["_HOL"]  = "Judeförintelsen";
$factarray["_WEIG"] = "Vikt";
$factarray["_YART"] = "Yartzeit";
$factarray["_MARNM"]	= "Vigselnamn";
$factarray["_STAT"]	= "Vigselstatus";
$factarray["COMM"]	= "Kommentar";
$factarray["MARR_CIVIL"] = "Borglig vigsel";
$factarray["MARR_RELIGIOUS"] = "Kyrklig vigsel";
$factarray["MARR_PARTNERS"] = "Registrerat partnerskap";
$factarray["MARR_UNKNOWN"] = "Okänd typ av vigsel";
$factarray["_HNM"] = "Hebreiskt namn";
$factarray["_DEAT_SPOU"] = "Äkta hälfts död";
$factarray["_BIRT_CHIL"] = "Barnafödelse";
$factarray["_MARR_CHIL"] = "Barnvigsel";
$factarray["_DEAT_CHIL"] = "Barnadöd";
$factarray["_BIRT_GCHI"] = "Födsel av barnbarn";
$factarray["_MARR_GCHI"] = "Vigsel av barnbarn";
$factarray["_DEAT_GCHI"] = "Barnbarns död";
$factarray["_MARR_FATH"] = "Faderns vigsel";
$factarray["_DEAT_FATH"] = "Faderns död";
$factarray["_MARR_MOTH"] = "Moderns vigsel";
$factarray["_DEAT_MOTH"] = "Moderns död";
$factarray["_BIRT_SIBL"] = "Syskons födelse";
$factarray["_MARR_SIBL"] = "Syskons vigsel";
$factarray["_DEAT_SIBL"] = "Syskons död";
$factarray["_BIRT_HSIB"] = "Halv-syskons födelse";
$factarray["_MARR_HSIB"] = "Halv-syskons vigsel";
$factarray["_DEAT_HSIB"] = "Halv-syskons död";
$factarray["_DEAT_GPAR"] = "Far- eller morföräldrars död";
$factarray["_BIRT_FSIB"] = "Faderns syskons födelse";
$factarray["_MARR_FSIB"] = "Faderns syskons vigsel";
$factarray["_DEAT_FSIB"] = "Dödsfall faderns syskon";
$factarray["_BIRT_MSIB"] = "Moderns syskons födelse";
$factarray["_MARR_MSIB"] = "Moderns syskons vigsel";
$factarray["_DEAT_MSIB"] = "Dödsfall för moderns syskon";
$factarray["_BIRT_COUS"] = "Kusins födelse";
$factarray["_MARR_COUS"] = "Kusins vigsel";
$factarray["_DEAT_COUS"] = "Dödsfall för kusin";
$factarray["_INTE"] = "Gravsatt";
$factarray["_MARI"] = "Avsikt att gifta sig";
$factarray["_MBON"] = "Hindersprövning";
$factarray["_MEDC"] = "Medicinska förhållande";
$factarray["_MILI"] = "Militär";
$factarray["_NMR"] = "Ogift";
$factarray["_NLIV"] = "Lever inte";
$factarray["_NMAR"] = "Aldrig gift";
$factarray["_PRMN"] = "Permanent nummer";

if (file_exists( "languages/facts.sv.extra.php")) require  "languages/facts.sv.extra.php";

?>
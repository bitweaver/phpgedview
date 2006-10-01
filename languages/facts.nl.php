<?php
/**
 * Dutch Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Erik Bent and Boudewijn Sjouke
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
 * @author Erik Bent
 * @author Boudewijn Sjouke
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their Dutch values
$factarray["ABBR"] 		= "Afkorting";
$factarray["ADDR"] 		= "Adres";
$factarray["ADR1"] 		= "Adres 1";
$factarray["ADR2"] 		= "Adres 2";
$factarray["ADOP"] 		= "Adoptie";
$factarray["AFN"] 		= "Bestandsnummer stamvaders (AFN)";
$factarray["AGE"] 		= "Leeftijd";
$factarray["AGNC"] 		= "Instantie";
$factarray["ALIA"] 		= "Verwijzing";
$factarray["ANCE"] 		= "Voorouders";
$factarray["ANCI"] 		= "Onderzoek naar voorouders";
$factarray["ANUL"] 		= "Vernietiging";
$factarray["ASSO"] 		= "Gerelateerd persoon";
$factarray["AUTH"] 		= "Auteur";
$factarray["BAPL"] 		= "LDS Doop";
$factarray["BAPM"] 		= "Gedoopt";
$factarray["BARM"] 		= "Bar mitswa";
$factarray["BASM"] 		= "Bas mitswa";
$factarray["BIRT"] 		= "Geboorte";
$factarray["BLES"] 		= "Zegening";
$factarray["BLOB"] 		= "Binaire data";
$factarray["BURI"] 		= "Begrafenis";
$factarray["CALN"] 		= "Inventarisnummer";
$factarray["CAST"] 		= "Kaste / sociale status";
$factarray["CAUS"] 		= "Doodsoorzaak";
$factarray["CEME"]  	= "Begraafplaats";
$factarray["CENS"] 		= "Volkstelling";
$factarray["CHAN"] 		= "Laatste wijziging";
$factarray["CHAR"] 		= "Karakterset";
$factarray["CHIL"] 		= "Kind";
$factarray["CHR"] 		= "Gedoopt";
$factarray["CHRA"] 		= "Volwassen doop";
$factarray["CITY"] 		= "Stad";
$factarray["CONF"] 		= "Bevestiging";
$factarray["CONL"] 		= "LDS bevestiging";
$factarray["COPR"] 		= "Copyright";
$factarray["CORP"] 		= "Bedrijf";
$factarray["CREM"] 		= "Crematie";
$factarray["CTRY"] 		= "Land";
$factarray["DATA"] 		= "Data";
$factarray["DATE"] 		= "Datum";
$factarray["DEAT"] 		= "Overleden";
$factarray["DESC"] 		= "Nakomelingen";
$factarray["DESI"] 		= "Onderzoek naar nakomelingen";
$factarray["DEST"] 		= "Bestemming";
$factarray["DIV"] 		= "Scheiding";
$factarray["DIVF"] 		= "Scheiding ingediend";
$factarray["DSCR"] 		= "Fysieke beschrijving";
$factarray["EDUC"] 		= "Opleiding";
$factarray["EMIG"] 		= "Emigratie";
$factarray["ENDL"] 		= "LDS gave";
$factarray["ENGA"] 		= "Verloving";
$factarray["EVEN"] 		= "Gebeurtenis";
$factarray["FAM"] 		= "Gezin";
$factarray["FAMC"] 		= "Gezinsleden van kind";
$factarray["FAMF"] 		= "Gezinsbestand";
$factarray["FAMS"] 		= "Gezinsleden van ega";
$factarray["FCOM"] 		= "Eerste communie";
$factarray["FILE"] 		= "Extern bestand";
$factarray["FORM"] 		= "Formaat";
$factarray["GIVN"] 		= "Voorna(a)m(en)";
$factarray["GRAD"] 		= "Geslaagd";
$factarray["HUSB"]  = "Partner";
$factarray["IDNO"] 		= "Identificatienummer";
$factarray["IMMI"] 		= "Immigratie";
$factarray["LEGA"] 		= "Legataris";
$factarray["MARB"] 		= "Ondertrouw";
$factarray["MARC"] 		= "Huwelijkscontract";
$factarray["MARL"]		= "Huwelijkstoestemming";
$factarray["MARR"]		= "Huwelijk";
$factarray["MARS"] 		= "Huwelijksvoorwaarden";
$factarray["MEDI"]		= "Multimediatype";
$factarray["NAME"] 		= "Naam";
$factarray["NATI"] 		= "Nationaliteit";
$factarray["NATU"] 		= "Naturalisatie";
$factarray["NCHI"] 		= "Aantal kinderen";
$factarray["NICK"] 		= "Roepnaam";
$factarray["NMR"] 		= "Aantal huwelijken";
$factarray["NOTE"] 		= "Notitie";
$factarray["NPFX"] 		= "Voorvoegsel";
$factarray["NSFX"] 		= "Achtervoegsel";
$factarray["OBJE"] 		= "Multimedia-object";
$factarray["OCCU"] 		= "Beroep";
$factarray["ORDI"] 		= "Ritueel";
$factarray["ORDN"] 		= "Wijding";
$factarray["PAGE"] 		= "Details citaat";
$factarray["PEDI"] 		= "Kwartierstaat";
$factarray["PLAC"] 		= "Plaats";
$factarray["PHON"] 		= "Telefoon";
$factarray["POST"] 		= "Postcode";
$factarray["PROB"] 		= "Wilserkenning";
$factarray["PROP"] 		= "Eigendom";
$factarray["PUBL"] 		= "Publicatie";
$factarray["QUAY"] 		= "Kwaliteit van de gegevens";
$factarray["REPO"] 		= "Bewaarplaats";
$factarray["REFN"] 		= "Referentienummer";
$factarray["RELA"]		= "Relatie";
$factarray["RELI"] 		= "Religie";
$factarray["RESI"] 		= "Woonplaats";
$factarray["RESN"] 		= "Beperking";
$factarray["RETI"] 		= "Pensioen";
$factarray["RFN"] 		= "Record bestandsnummer";
$factarray["RIN"] 		= "Record ID nummer";
$factarray["ROLE"] 		= "Rol";
$factarray["SEX"] 		= "Geslacht";
$factarray["SLGC"] 		= "LDS kind verzegeling";
$factarray["SLGS"] 		= "LDS ega verzegeling";
$factarray["SOUR"] 		= "Bron";
$factarray["SPFX"] 		= "Voorvoegsel achternaam";
$factarray["SSN"] 		= "SOFI nummer";
$factarray["STAE"] 		= "Staat";
$factarray["STAT"] 		= "Status";
$factarray["SUBM"] 		= "Aangeleverd door";
$factarray["SUBN"] 		= "Aanlevering";
$factarray["SURN"] 		= "Achternaam";
$factarray["TEMP"] 		= "Tempel";
$factarray["TEXT"] 		= "Tekst";
$factarray["TIME"] 		= "Tijd";
$factarray["TITL"] 		= "Titel";
$factarray["WIFE"]  		= "Partner";
$factarray["TYPE"] 		= "Type";
$factarray["WILL"] 		= "Testament";
$factarray["_EMAIL"]		= "E-mailadres";
$factarray["EMAIL"] 		= "E-mailadres";
$factarray["_TODO"] 		= "Te doen";
$factarray["_UID"] 		= "Universeel kenmerk (UID)";
$factarray["_PGVU"]		= "Laatst gewijzigd door";
$factarray["SERV"] 		= "Server op afstand";
$factarray["_GEDF"] 		= "GEDCOM-bestand";
$factarray["_PRIM"]		= "Geaccentueerde afbeelding";
$factarray["_THUM"]		= "Gebruik deze afbeelding als de miniweergave?";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] 		= "Medisch";
$factarray["_DEG"] 		= "Graad";
$factarray["_MILT"] 		= "Militaire dienst";
$factarray["_SEPR"] 		= "Gescheiden";
$factarray["_DETS"] 		= "Dood van een partner";
$factarray["CITN"] 		= "Staatsburgerschap";
$factarray["_MEND"] 		= "Status einde huwelijk";
$factarray["FAX"]		= "Fax";
$factarray["FACT"]		= "Feit";
$factarray["WWW"]		= "Internetpagina";
$factarray["MAP"]		= "Landkaart";
$factarray["LATI"]		= "Breedtegraad";
$factarray["LONG"]		= "Lengtegraad";
$factarray["FONE"]		= "Fonetisch";
$factarray["ROMN"]		= "Geromaniseerd";
$factarray["URL"]		= "Web URL";
$factarray["_HEB"]		= "Hebreeuwse naam";
$factarray["_SCBK"] 		= "Aantekenboek";
$factarray["_TYPE"] 		= "Multimediatype";
$factarray["_SSHOW"]		= "Diavoorstelling";
$factarray["_SUBQ"]		= "Verkorte versie";
$factarray["_BIBL"] 		= "Bibliografie";
$factarray["EMAL"]	= "E-mailadres";
$factarray["_NAME"] 		= "E-mail naam";
$factarray["_MSTAT"]		= "Status aanvang huwelijk";
$factarray["_FA1"] 		= "Huwelijksfeit";
$factarray["_FREL"] 		= "Relatie met vader";
$factarray["_MREL"]	 	= "Relatie met moeder";
$factarray["_FA13"] 		= "Feit 13";
$factarray["_FA12"] 		= "Feit 12";
$factarray["_FA11"] 		= "Feit 11";
$factarray["_FA10"]		= "Feit 10";
$factarray["_FA9"] 		= "Feit 9";
$factarray["_FA8"] 		= "Feit 8";
$factarray["_FA7"] 		= "Feit 7";
$factarray["_FA6"] 		= "Feit 6";
$factarray["_FA5"] 		= "Feit 5";
$factarray["_FA4"] 		= "Feit 4";
$factarray["_FA3"] 		= "Feit 3";
$factarray["_FA2"] 		= "Feit 2";
$factarray["_FA1"] 		= "Feit 1";

// Other common customized facts
$factarray["_ADPF"] 		= "Geadopteerd door de vader";
$factarray["_ADPM"] 		= "Geadopteerd door de moeder";
$factarray["_AKAN"] 		= "Ook bekend als";
$factarray["_AKA"]		= "Ook bekend als";
$factarray["_BRTM"] 		= "Besnijdenis";
$factarray["_COML"] 		= "Erkend partnerschap";
$factarray["_EYEC"] 		= "Kleur ogen";
$factarray["_FNRL"] 		= "Begrafenis";
$factarray["_HAIR"] 		= "Kleur haar";
$factarray["_HEIG"] 		= "Lengte";
$factarray["_HOL"]  		= "Holocaust";
$factarray["_INTE"] 		= "Bijgezet";
$factarray["_MARI"] 		= "Huwelijksvoornemen";
$factarray["_MBON"] 		= "Ondertrouw";
$factarray["_MEDC"] 		= "Medische toestand";
$factarray["_MILI"] 		= "Militaire dienst";
$factarray["_NMR"] 		= "Ongehuwd";
$factarray["_NLIV"] 		= "Overleden";
$factarray["_NMAR"] 		= "Nooit gehuwd";
$factarray["_PRMN"] 		= "Permanent nummer";
$factarray["_WEIG"] 		= "Gewicht";
$factarray["_YART"] 		= "Yartzeit";
$factarray["_MARNM"]		= "Naam in huwelijk";
$factarray["_STAT"]		= "Burgerlijke staat";
$factarray["COMM"]		= "Commentaar";

// Aldfaer related facts
$factarray["MARR_CIVIL"]	= "Burgerlijk huwelijk";
$factarray["MARR_RELIGIOUS"]	= "Kerkelijk huwelijk";
$factarray["MARR_PARTNERS"] 	= "Geregistreerd partnerschap";
$factarray["_DEAT_GCHI"]	= "Overlijden van een kleinkind";
$factarray["_DEAT_HSIB"] 	= "Overlijden van een halfbroer/-zus";
$factarray["_BIRT_FSIB"] 	= "Geboorte van een oom/tante";
$factarray["_MARR_MSIB"] 	= "Huwelijk van een oom/tante";
$factarray["_DEAT_COUS"] 	= "Overlijden van een neef/nicht";
$factarray["_DEAT_COUS"] 	= "Overlijden van een neef/nicht";
$factarray["_MARR_COUS"] 	= "Huwelijk van een neef/nicht";
$factarray["_BIRT_COUS"] 	= "Geboorte van een neef/nicht";
$factarray["_DEAT_MSIB"] 	= "Overlijden van een oom/tante";
$factarray["_BIRT_MSIB"] 	= "Geboorte van een oom/tante";
$factarray["_DEAT_FSIB"] 	= "Overlijden van een oom/tante";
$factarray["_MARR_FSIB"] 	= "Huwelijk van een oom/tante";
$factarray["_DEAT_GPAR"] 	= "Overlijden van een grootouder";
$factarray["_MARR_HSIB"] 	= "Huwelijk van een halfbroer/-zus";
$factarray["_BIRT_HSIB"] 	= "Geboorte van een halfbroer/-zus";
$factarray["_DEAT_SIBL"] 	= "Overlijden van een broer/zus";
$factarray["_MARR_SIBL"] 	= "Huwelijk van een broer/zus";
$factarray["_BIRT_SIBL"] 	= "Geboorte van een broer/zus";
$factarray["_DEAT_MOTH"] 	= "Overlijden van moeder";
$factarray["_MARR_MOTH"] 	= "Huwelijk van moeder";
$factarray["_DEAT_FATH"] 	= "Overlijden van vader";
$factarray["_MARR_FATH"] 	= "Huwelijk van vader";
$factarray["_MARR_GCHI"] 	= "Huwelijk van een kleinkind";
$factarray["_BIRT_GCHI"] 	= "Geboorte van een kleinkind";
$factarray["_DEAT_CHIL"] 	= "Overlijden van een kind";
$factarray["_MARR_CHIL"] 	= "Huwelijk van een kind";
$factarray["_BIRT_CHIL"] 	= "Geboorte van een kind";
$factarray["_DEAT_SPOU"] 	= "Overlijden van een partner";
$factarray["MARR_UNKNOWN"] 	= "Gewoon huwelijk";
$factarray["_HNM"] 		= "Hebreeuwse naam";


if (file_exists( "languages/facts.nl.extra.php")) require  "languages/facts.nl.extra.php";

?>

<?php
/**
 * Lithuanian Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Arturas Sleinius
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
 * @author Arturas Sleinius
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map Gedcom tags with their lithuanian values
$factarray["BIRT"]	= "Gimė";
$factarray["BLES"]	= "Palaiminimas";
$factarray["CAUS"]	= "Mirties priežastis";
$factarray["CHAR"]	= "Kodų lentelė";
$factarray["CHAN"]	= "Paskutinis pakeitimas";
$factarray["CHIL"]	= "Vaikas";
$factarray["CHRA"]	= "Suaugusio krikštas";
$factarray["CHR"]	= "Krikštas";
$factarray["CITY"]	= "Miestas";
$factarray["CTRY"]	= "Šalis";
$factarray["DATE"]	= "Data";
$factarray["DEAT"]	= "Mirė";
$factarray["DIV"]	= "Skirybos";
$factarray["EMIG"]	= "Emigravimas";
$factarray["EVEN"]	= "Įvykis";
$factarray["FAM"]	= "Šeima";
$factarray["FAMF"]	= "Šeimos failas";
$factarray["FCOM"]	= "Pirma komunija";
$factarray["FORM"]	= "Formatas";
$factarray["GIVN"]	= "Vardai";
$factarray["IMMI"]	= "Imigracija";
$factarray["MARR"]	= "Santuoka";
$factarray["NAME"]	= "Vardas";
$factarray["NATI"]	= "Tautybė";
$factarray["NCHI"]	= "Vaikų skaičius";
$factarray["NMR"]	= "Santuokų skaičius";
$factarray["PEDI"]	= "Kilmė";
$factarray["PLAC"]	= "Vietovė";
$factarray["PHON"]	= "Telefonas";
$factarray["PROP"]	= "Nuosavybė";
$factarray["REPO"]	= "Saugykla";
$factarray["SEX"]	= "Lytis";
$factarray["SOUR"]	= "Šaltinis";
$factarray["SSN"]	= "SoDra numeris";
$factarray["SURN"]	= "Pavardė";
$factarray["TEXT"]	= "Tekstas";
$factarray["TIME"]	= "Laikas";
$factarray["EMAIL"]	= "Elektroninio pašto adresas";
$factarray["_TODO"]	= "Dar padaryti įrašas";
$factarray["_PGVU"]	= "Paskutinis keitė";
$factarray["_PRIM"]	= "Paryškintas paveikslas";
$factarray["_THUM"]	= "Naudoti šį paveikslą kaip maža paveiksliuką?";
$factarray["_DEG"]	= "Laipsnis";
$factarray["_SEPR"]	= "Išsiskyręs";
$factarray["_FA1"]	= "Įvykis 1";
$factarray["_FA2"]	= "Įvykis 2";
$factarray["_FA3"]	= "Įvykis 3";
$factarray["_FA4"]	= "Įvykis 4";
$factarray["_FA5"]	= "Įvykis 5";
$factarray["_FA6"]	= "Įvykis 6";
$factarray["_FA7"]	= "Įvykis 7";
$factarray["ANCE"]	= "Protėviai";
$factarray["_FA8"]	= "Įvykis 8";
$factarray["_FA9"]	= "Įvykis 9";
$factarray["_FA10"]	= "Įvykis 10";
$factarray["_FA11"]	= "Įvykis 11";
$factarray["_FA12"]	= "Įvykis 12";
$factarray["_FA13"]	= "Įvykis 13";
$factarray["_MREL"]	= "Ryšys su motina";
$factarray["_FREL"]	= "Ryšys su tėvu";
$factarray["FAX"] = "Faksas";
$factarray["FACT"] = "Įvykis";
$factarray["FACT"] = "Faktas";
$factarray["WWW"] = "Interneto namų puslapis";
$factarray["MAP"] = "Žemėlapis";
$factarray["_NAME"] = "Pašto adresas";
$factarray["URL"] = "Interneto URL";
$factarray["_ADPF"]	= "Įvaikintas tėvo";
$factarray["_ADPM"]	= "Įvaikintas motinos";
$factarray["_AKA"] 	= "Dar žinomas kaip";
$factarray["_FNRL"]	= "Laiduotuvės";
$factarray["_EYEC"]	= "Akių spalva";
$factarray["_HAIR"]	= "Plaukų spalva";
$factarray["_HEIG"]	= "Aukštis";
$factarray["_NMR"]	= "Ne santuokoje";
$factarray["_NMAR"]	= "Nebuvo santuokoje";
$factarray["_WEIG"]	= "Svoris";
$factarray["_MARNM"] = "Pavardė po santuokos";
$factarray["_STAT"]	= "Vedybinis statusas";
$factarray["COMM"]	= "Komenatas";
$factarray["MARR_CIVIL"] = "Civilinė santuoka";
$factarray["MARR_RELIGIOUS"] = "Religinė santuoka";
$factarray["MARR_RELIGIOUS"] = "Religinės vestuvės";
$factarray["MARR_PARTNERS"] = "Registruota partnerystė";
$factarray["MARR_UNKNOWN"] = "Santuokos tipas nežinomas";
$factarray["_HNM"] = "Hebrajų vardas";
$factarray["ABBR"]	= "Santrumpa";
$factarray["ADDR"]	= "Adresas";
$factarray["ADR1"]	= "Adresas 1";
$factarray["ADR2"]	= "Adresas 2 ";
$factarray["ADOP"]	= "Įvaikinimas";
$factarray["AGE"]	= "Amžius";
$factarray["AUTH"]	= "Autorius";

if (file_exists("languages/facts.lt.extra.php")) require "languages/facts.lt.extra.php";

?>
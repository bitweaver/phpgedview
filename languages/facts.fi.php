<?php
/**
 * Finnish Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Jaakko Sarell and Matti
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
 * @subpackage Languages
 * @author Jaakko Sarell
 * @author Matti
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

// -- Define a fact array to map GEDCOM tags with their Finnish values
$factarray["ABBR"]	= "Lyhenne";
$factarray["ADDR"]	= "Osoite";
$factarray["ADR1"]	= "Osoite 1";
$factarray["ADR2"]	= "Osoite 2";
$factarray["ADOP"]	= "Adoptio";
$factarray["AFN"]	= "Esipolvitiedoston numero (AFN)";
$factarray["AGE"]	= "Ikä";
$factarray["AGNC"]	= "Viranomainen";
$factarray["ALIA"]	= "Alias";
$factarray["ANCE"]	= "Esivanhemmat";
$factarray["ANCI"]	= "Esivanhempien harrastus";
$factarray["ANUL"]	= "Kumoaminen";
$factarray["ASSO"]	= "Kumppanit";
$factarray["AUTH"]	= "Tekijä";
$factarray["BAPL"]	= "Mormoonikaste";
$factarray["BAPM"]	= "Kastettu";
$factarray["BARM"]	= "Bar Mitzva";
$factarray["BASM"]	= "Bat Mitzva";
$factarray["BIRT"]	= "Syntynyt";
$factarray["BLES"]	= "Siunaus";
$factarray["BLOB"]	= "Binaaridataobjekti";
$factarray["BURI"]	= "Haudattu";
$factarray["CALN"]	= "Puhelinnumero";
$factarray["CAST"]	= "Luokka / Sosiaalinen asema";
$factarray["CAUS"]	= "Kuolinsyy";
$factarray["CEME"]  = "Hautausmaa";
$factarray["CENS"]	= "Väestölaskenta";
$factarray["CHAN"]	= "Muutettu viimeksi";
$factarray["CHAR"]	= "Merkkivalikoima";
$factarray["CHIL"]	= "Lapsi";
$factarray["CHR"]	= "Kaste";
$factarray["CHRA"]	= "Aikuiskaste";
$factarray["CITY"]	= "Kaupunki";
$factarray["CONF"]	= "Rippi";
$factarray["CONL"]	= "Mormooni rippi";
$factarray["COPR"]	= "Tekijänoikeus";
$factarray["CORP"]	= "Yhtiö";
$factarray["CREM"]	= "Polttohautaus";
$factarray["CTRY"]	= "Maa";
$factarray["DATA"]	= "Data";
$factarray["DATE"]	= "Päiväys";
$factarray["DEAT"]	= "Kuollut";
$factarray["DESC"]	= "Jälkeläiset";
$factarray["DESI"]	= "Jälkeläisten harrastus";
$factarray["DEST"]	= "Kohde";
$factarray["DIV"]	= "Avioero";
$factarray["DIVF"]	= "Avioero kirjattu";
$factarray["DSCR"]	= "Kuvaus";
$factarray["EDUC"]	= "Koulutus";
$factarray["EMIG"]	= "Maastamuutto";
$factarray["ENDL"]	= "LDS Endowment";
$factarray["ENGA"]	= "Kihlaus";
$factarray["EVEN"]	= "Tapahtuma";
$factarray["FAM"]	= "Perhe";
$factarray["FAMC"]	= "Perhe lapsena";
$factarray["FAMF"]	= "Perhetiedosto";
$factarray["FAMS"]	= "Perhe puolisona";
$factarray["FCOM"]	= "Ensimmäinen rippi";
$factarray["FILE"]	= "Ulkoinen tiedosto";
$factarray["FORM"]	= "Muoto";
$factarray["GIVN"]	= "Etunimet";
$factarray["GRAD"]	= "Tutkinto";
$factarray["HUSB"]  = "Mies";
$factarray["IDNO"]	= "Henkilönumero";
$factarray["IMMI"]	= "Maahanmuutto";
$factarray["LEGA"]	= "Perinnönsaaja";
$factarray["MARB"]	= "Aviokuulutus";
$factarray["MARC"]	= "Avioliittosopimus";
$factarray["MARL"]	= "Vihkitodistus";
$factarray["MARR"]	= "Vihitty";
$factarray["MARS"]	= "Avioehto";
$factarray["MEDI"]	= "Mediatyyppi";
$factarray["NAME"]	= "Nimi";
$factarray["NATI"]	= "Kansallisuus";
$factarray["NATU"]	= "Kansalaiseksi ottaminen";
$factarray["NCHI"]	= "Lasten määrä";
$factarray["NICK"]	= "Lempinimi";
$factarray["NMR"]	= "Avioliittojen määrä";
$factarray["NOTE"]	= "Huomautus";
$factarray["NPFX"]	= "Etuliite";
$factarray["NSFX"]	= "Pääte";
$factarray["OBJE"]	= "Multimediaobjekti";
$factarray["OCCU"]	= "Ammatti";
$factarray["ORDI"]	= "Ordinance";
$factarray["ORDN"]	= "Ordination";
$factarray["PAGE"]	= "Lainaus";
$factarray["PEDI"]	= "Esipolvitaulu";
$factarray["PLAC"]	= "Paikka";
$factarray["PHON"]	= "Puhelin";
$factarray["POST"]	= "Postinumero";
$factarray["PROB"]	= "Testamentin vahvistus";
$factarray["PROP"]	= "Omaisuus";
$factarray["PUBL"]	= "Julkaisu";
$factarray["QUAY"]	= "Tiedon laatu";
$factarray["REPO"]	= "Tallennuspaikka";
$factarray["REFN"]	= "Viitenumero";
$factarray["RELA"]	= "sukulaisuussuhde";
$factarray["RELI"]	= "Uskonto";
$factarray["RESI"]	= "Asuinpaikka";
$factarray["RESN"]	= "Rajoitus";
$factarray["RETI"]	= "Eläkkeelle";
$factarray["RFN"]	= "Tietueen tiedostonumero";
$factarray["RIN"]	= "Tietueen ID numero";
$factarray["ROLE"]	= "Rooli";
$factarray["SEX"]	= "Sukupuoli";
$factarray["SLGC"]	= "LDS Child Sealing";
$factarray["SLGS"]	= "LDS Spouse Sealing";
$factarray["SOUR"]	= "Lähde";
$factarray["SPFX"]	= "Sukunimen etuliite";
$factarray["SSN"]	= "Henkilötunnus";
$factarray["STAE"]	= "Osavaltio";
$factarray["STAT"]	= "Tila";
$factarray["SUBM"]	= "Lähettäjä/toimittaja";
$factarray["SUBN"]	= "Lähetys/toimitus";
$factarray["SURN"]	= "Sukunimi";
$factarray["TEMP"]	= "Temppeli";
$factarray["TEXT"]	= "Teksti";
$factarray["TIME"]	= "Aika";
$factarray["TITL"]	= "Aihe";
$factarray["TYPE"]	= "Tyyppi";
$factarray["WIFE"]  = "Vaimo";
$factarray["WILL"]	= "Testamentti";
$factarray["_EMAIL"]	= "Sähköpostiosoite";
$factarray["EMAIL"]	= "Sähköpostiosoite";
$factarray["_TODO"]	= "Työlistalla";
$factarray["_UID"]	= "Yleistunniste";
$factarray["_PGVU"]	= "Viimeiseksi muuttanut";
$factarray["SERV"] = "Etäpalvelin";
$factarray["_GEDF"] = "GEDCOM-tiedosto";
$factarray["_PRIM"]	= "Korostettu kuva";
$factarray["_THUM"]	= "Käytä tätä kuvaa pienoiskuvana?";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]	= "Lääketieteellinen";
$factarray["_DEG"]	= "Tutkinto";
$factarray["_MILT"]	= "Sotapalvelus";
$factarray["_SEPR"]	= "Asumusero";
$factarray["_DETS"]	= "Puolison kuolema";
$factarray["CITN"]	= "Kansalaisuus";
$factarray["_FA1"] = "Fakta 1";
$factarray["_FA2"] = "Fakta 2";
$factarray["_FA3"] = "Fakta 3";
$factarray["_FA4"] = "Fakta 4";
$factarray["_FA5"] = "Fakta 5";
$factarray["_FA6"] = "Fakta 6";
$factarray["_FA7"] = "Fakta 7";
$factarray["_FA8"] = "Fakta 8";
$factarray["_FA9"] = "Fakta 9";
$factarray["_FA10"] = "Fakta 10";
$factarray["_FA11"] = "Fakta 11";
$factarray["_FA12"] = "Fakta 12";
$factarray["_FA13"] = "Fakta 13";
$factarray["_MREL"] = "Suhde äitiin";
$factarray["_FREL"] = "Suhde isään";
$factarray["_MSTAT"] = "Avioliiton alkutilanne";
$factarray["_MEND"] = "Avioliiton lopputilanne";
$factarray["FAX"] = "Faksi";
$factarray["FACT"] = "Tosiasia";
$factarray["WWW"] = "Kotisivu";
$factarray["MAP"] = "Kartta";
$factarray["LATI"] = "Leveysaste";
$factarray["LONG"] = "Pituusaste";
$factarray["FONE"] = "Foneettinen";
$factarray["ROMN"] = "Romanisoitu";
$factarray["_NAME"] = "Postinimi";
$factarray["URL"] = "Verkko-osoite URL";
$factarray["_HEB"] = "Heprealainen";
$factarray["_SCBK"] = "Leikekirja";
$factarray["_TYPE"] = "Mediatyyppi";
$factarray["_SSHOW"] = "Kuvasarjaesitys";

// Rootsmagic
$factarray["_SUBQ"]= "Lyhyt versio";
$factarray["_BIBL"] = "Bibliografia";

// Reunion
$factarray["EMAL"]	= "Sähköpostiosoite";

// Other common customized facts
$factarray["_ADPF"]	= "Isän adoptoima";
$factarray["_ADPM"]	= "Äidin adoptoima";
$factarray["_AKAN"]	= "Toiselta nimeltä";
$factarray["_AKA"] 	= "Toiselta nimeltä";
$factarray["_BRTM"]	= "Brit mila";
$factarray["_COML"]	= "Avoliitto";
$factarray["_EYEC"]	= "Silmien väri";
$factarray["_FNRL"]	= "Hautajaiset";
$factarray["_HAIR"]	= "Hiusten väri";
$factarray["_HEIG"]	= "Pituus";
$factarray["_HOL"]  = "Holokausti";
$factarray["_INTE"]	= "Hautaus";
$factarray["_MARI"]	= "Avioliittoaikomus";
$factarray["_MBON"]	= "Aviollinen side";
$factarray["_MEDC"]	= "Terveydellinen tila";
$factarray["_MILI"]	= "Sotilaallinen";
$factarray["_NMR"]	= "Naimaton";
$factarray["_NLIV"]	= "Ei elossa";
$factarray["_NMAR"]	= "Ei koskaan naimisissa";
$factarray["_PRMN"]	= "Pysyvä numero";
$factarray["_WEIG"]	= "Paino";
$factarray["_YART"]	= "Yartzeit";
$factarray["_MARNM"]	= "Avionimi";
$factarray["_STAT"]	= "Aviosääty";
$factarray["COMM"]	= "Huomautus";
$factarray["MARR_CIVIL"] = "Siviiliavioliitto";
$factarray["MARR_RELIGIOUS"] = "Kirkollinen avioliitto";
$factarray["MARR_PARTNERS"] = "Rekisteröity suhde";
$factarray["MARR_UNKNOWN"] = "Avioliiton tyyppi tuntematon";
$factarray["_HNM"] = "Heprealainen nimi";
$factarray["_DEAT_SPOU"] = "Puolison kuolema";
$factarray["_BIRT_CHIL"] = "Lapsen syntymä";
$factarray["_MARR_CHIL"] = "Lapsen avioituminen";
$factarray["_DEAT_CHIL"] = "Lapsen kuolema";
$factarray["_BIRT_GCHI"] = "Lapsenlapsen syntymä";
$factarray["_MARR_GCHI"] = "Lapsenlapsen avioituminen";
$factarray["_DEAT_GCHI"] = "Lapsenlapsen kuolema";
$factarray["_MARR_FATH"] = "Isän avioituminen";
$factarray["_DEAT_FATH"] = "Isän kuolema";
$factarray["_MARR_MOTH"] = "Äidin avioituminen";
$factarray["_DEAT_MOTH"] = "Äidin kuolema";
$factarray["_BIRT_SIBL"] = "Sisaruksen syntymä";
$factarray["_MARR_SIBL"] = "Sisaruksen avioituminen";
$factarray["_DEAT_SIBL"] = "Sisaruksen kuolema";
$factarray["_BIRT_HSIB"] = "Sisarpuolen syntymä";
$factarray["_MARR_HSIB"] = "Sisarpuolen avioituminen";
$factarray["_DEAT_HSIB"] = "Sisarpuolen kuolema";
$factarray["_DEAT_GPAR"] = "Isovanhemman kuolema";
$factarray["_BIRT_FSIB"] = "Isän sisaruksen syntymä";
$factarray["_MARR_FSIB"] = "Isän sisaruksen avioituminen";
$factarray["_DEAT_FSIB"] = "Isän sisaruksen kuolema";
$factarray["_BIRT_MSIB"] = "Äidin sisaruksen syntymä";
$factarray["_MARR_MSIB"] = "Äidin sisaruksen avioituminen";
$factarray["_DEAT_MSIB"] = "Äidin sisaruksen kuolema";
$factarray["_BIRT_COUS"] = "Serkun syntymä";
$factarray["_MARR_COUS"] = "Serkun avioituminen";
$factarray["_DEAT_COUS"] = "Serkun kuolema";

if (file_exists( "languages/facts.fi.extra.php")) require  "languages/facts.fi.extra.php";
//if (file_exists($PGV_BASE_DIRECTORY . "languages/facts.fi.extra.php")) require $PGV_BASE_DIRECTORY . "languages/facts.fi.extra.php";
?>
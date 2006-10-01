<?php
/**
 * Polish Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Michael Paluchowski, Tymoteusz Motylewski
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
 * @author Michael Paluchowski, http://genealogy.nethut.pl
 * @author Tymoteusz Motylewski www.motylewscy.com
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map Gedcom tags with their polish values
$factarray["ABBR"] = "Skrót";
$factarray["ADDR"] = "Adres";
$factarray["ADR1"] = "Adres 1";
$factarray["ADR2"] = "Adres 2";
$factarray["ADOP"] = "Adopcja";
$factarray["AFN"] = "Numer Ancestral File (AFN)";
$factarray["AGE"] = "Wiek";
$factarray["AGNC"] = "Agencja";
$factarray["ALIA"] = "Inaczej";
$factarray["ANCE"] = "Przodkowie";
$factarray["ANCI"] = "Udział przodków";
$factarray["ANUL"] = "Unieważnienie";
$factarray["ASSO"] = "Połączenia";
$factarray["AUTH"] = "Autor(ka)";
$factarray["BAPL"] = "Chrzest Mormoński";
$factarray["BAPM"] = "Chrzest";
$factarray["BARM"] = "Bar Mitzva";
$factarray["BASM"] = "Bas Mitzva";
$factarray["BIRT"] = "Narodziny";
$factarray["BLES"] = "Błogosławieństwo";
$factarray["BLOB"] = "Obiekt binarny";
$factarray["BURI"] = "Pogrzeb";
$factarray["CALN"] = "Numer źródła";
$factarray["CAST"] = "Kasta / Status społeczny";
$factarray["CAUS"] = "Przyczyna śmierci";
$factarray["CEME"]  = "Cmentarz";
$factarray["CENS"] = "Spis powszechny";
$factarray["CHAN"] = "Ostatnia zmiana";
$factarray["CHAR"] = "Zestaw znaków";
$factarray["CHIL"] = "Dziecko";
$factarray["CHR"] = "Chrzciny";
$factarray["CHRA"] = "Chrzciny dorosłego";
$factarray["CITY"] = "Miasto";
$factarray["CONF"] = "Bierzmowanie";
$factarray["CONL"] = "Bierzmowanie Mormońskie";
$factarray["COPR"] = "Prawa autorskie";
$factarray["CORP"] = "Korporacja / Firma";
$factarray["CREM"] = "Kremacja";
$factarray["CTRY"] = "Kraj";
$factarray["DATA"] = "Dane";
$factarray["DATE"] = "Data";
$factarray["DEAT"] = "Śmierć";
$factarray["DESC"] = "Potomkowie";
$factarray["DESI"] = "Udział potomków";
$factarray["DEST"] = "Cel";
$factarray["DIV"] = "Rozwód";
$factarray["DIVF"] = "Uzyskanie rozwodu";
$factarray["DSCR"] = "Opis";
$factarray["EDUC"] = "Edukacja";
$factarray["EMIG"] = "Emigracja";
$factarray["ENDL"] = "Posag Mormoński";
$factarray["ENGA"] = "Zaręczyny";
$factarray["EVEN"] = "Wydarzenie";
$factarray["FAM"] = "Rodzina";
$factarray["FAMC"] = "W rodzinie jako dziecko";
$factarray["FAMF"] = "Dane rodziny";
$factarray["FAMS"] = "W rodzinie jako małżonek";
$factarray["FCOM"] = "Pierwsza Komunia";
$factarray["FILE"] = "Zewnętrzny plik";
$factarray["FORM"] = "Format";
$factarray["GIVN"] = "Imię (Imiona)";
$factarray["GRAD"] = "Ukończenie studiów";
$factarray["IDNO"] = "Numer Identyfikacyjny";
$factarray["IMMI"] = "Imigracja";
$factarray["LEGA"] = "Legatariusz / Zapisobiorca";
$factarray["MARB"] = "Zapowiedzi";
$factarray["MARC"] = "Kontrakt małżeński";
$factarray["MARL"] = "Licencja małżeńska";
$factarray["MARR"] = "Ślub";
$factarray["MARS"] = "Układ małżeński";
$factarray["MEDI"]	= "Typ mediów";
$factarray["NAME"] = "Nazwisko";
$factarray["NATI"] = "Narodowość";
$factarray["NATU"] = "Naturalizacja";
$factarray["NCHI"] = "Ilość dzieci";
$factarray["NICK"] = "Pseudonim";
$factarray["NMR"] = "Liczba małżeństw";
$factarray["NOTE"] = "Notka";
$factarray["NPFX"] = "Prefiks";
$factarray["NSFX"] = "Sufiks";
$factarray["OBJE"] = "Obiekt multimedialny";
$factarray["OCCU"] = "Zawód";
$factarray["ORDI"] = "Obrządek";
$factarray["ORDN"] = "Wyznanie";
$factarray["PAGE"] = "Szczególy cytatu";
$factarray["PEDI"] = "Rodowód";
$factarray["PLAC"] = "Miejsce";
$factarray["PHON"] = "Telefon";
$factarray["POST"] = "Kod pocztowy";
$factarray["PROB"] = "Poświadczenie autentyczności testamentu";
$factarray["PROP"] = "Własność";
$factarray["PUBL"] = "Publikacja";
$factarray["QUAY"] = "Jakość danych";
$factarray["REPO"] = "Repository";
$factarray["REFN"] = "Numer referencyjny";
$factarray["RELA"]	= "Pokrewieństwo";
$factarray["RELA"]	= "Pokrewieństwo";
$factarray["RELI"] = "Wyznanie";
$factarray["RESI"] = "Miejsce zamieszkania";
$factarray["RESN"] = "Ograniczenie";
$factarray["RETI"] = "Przejście na emeryturę";
$factarray["RFN"] = "Numer katalogowy rekordu";
$factarray["RIN"] = "Numer ID rekordu";
$factarray["ROLE"] = "Role";
$factarray["SEX"] = "Płeć";
$factarray["SLGC"] = "Mormońskie Naznaczenie Dziecka";
$factarray["SLGS"] = "Mormońskie Naznaczenie Małżonka";
$factarray["SOUR"] = "Źródło";
$factarray["SPFX"] = "Prefiks nazwiska";
$factarray["SSN"] = "Numer ubezpieczenia";
$factarray["STAE"] = "Stan";
$factarray["STAT"] = "Status";
$factarray["SUBM"] = "Wpisał(a) dane";
$factarray["SUBN"] = "Wpis";
$factarray["SURN"] = "Nazwisko";
$factarray["TEMP"] = "Świątynia";
$factarray["TEXT"] = "Tekst";
$factarray["TIME"] = "Czas";
$factarray["TITL"] = "Tytuł";
$factarray["TYPE"] = "Typ";
$factarray["WILL"] = "Testament";
$factarray["_EMAIL"] = "Adres email";
$factarray["EMAIL"]	= "Adres email";
$factarray["_TODO"] = "Do zrobienia";
$factarray["_UID"] = "Uniwersalny Identyfikator";
$factarray["_PGVU"]	= "Ostatnie zmiany wprowadził(a)";
$factarray["_PRIM"]	= "Wyróżnione zdjęcie";
$factarray["_THUM"]	= "Użyj tego zdjęcia jako miniaturki?";

// These facts are specific to gedcom exports from Family Tree Maker
$factarray["_MDCL"] = "Medyczny";
$factarray["_DEG"] = "Stopień";
$factarray["_MILT"] = "Służba wojskowa";
$factarray["_SEPR"] = "W separacji";
$factarray["_DETS"] = "Śmierć jednego z małżonków";
$factarray["CITN"] = "Obywatelstwo";
$factarray["_FA1"]	= "Fakt 1";
$factarray["_FA2"]	= "Fakt 2";
$factarray["_FA3"]	= "Fakt 3";
$factarray["_FA4"]	= "Fakt 4";
$factarray["_FA5"]	= "Fakt 5";
$factarray["_FA6"]	= "Fakt 6";
$factarray["_FA7"]	= "Fakt 7";
$factarray["_FA8"]	= "Fakt 8";
$factarray["_FA9"]	= "Fakt 9";
$factarray["_FA10"]	= "Fakt 10";
$factarray["_FA11"]	= "Fakt 11";
$factarray["_FA12"]	= "Fakt 12";
$factarray["_FA13"]	= "Fakt 13";
$factarray["_MREL"]	= "Związek z matką";
$factarray["_FREL"]	= "Związek z ojcem";
$factarray["_MSTAT"]	= "Status na początku małżeństwa";
$factarray["_MEND"]	= "Status na końcu małżeństwa";

// GEDCOM 5.5.1 related facts
$factarray["FAX"] = "FAX";
$factarray["FACT"] = "Fakt";
$factarray["WWW"] = "Strona WWW";
$factarray["MAP"] = "Mapa";
$factarray["LATI"] = "Szerokość geograficzna";
$factarray["LONG"] = "Długość geograficzna";
$factarray["FONE"] = "Fonetycznie";
$factarray["ROMN"] = "Zromanizowany";

// PAF related facts
$factarray["_NAME"] = "Nazwisko adresowe";
$factarray["URL"] = "Adres WWW";
$factarray["_HEB"] = "Hebrajski";
$factarray["_SCBK"] = "Album";
$factarray["_TYPE"] = "Rodzaj media";
$factarray["_SSHOW"] = "Przegląd slajdów";

// Rootsmagic
$factarray["_SUBQ"]= "Wersja skrócona";
$factarray["_BIBL"] = "Bibliografia";

// Other common customized facts
$factarray["_ADPF"]	= "Adoptowany(a) przez ojca";
$factarray["_ADPM"]	= "Adoptowany(a) przez matkę";
$factarray["_AKAN"]	= "Również znany(a) jako";
$factarray["_AKA"] 	= "Również znany(a) jako";
$factarray["_BRTM"]	= "Brit mila";
$factarray["_COML"]	= "Common Law marriage";
$factarray["_EYEC"]	= "Kolor oczu";
$factarray["_FNRL"]	= "Pogrzeb";
$factarray["_HAIR"]	= "Kolor włosów";
$factarray["_HEIG"]	= "Wysokość";
$factarray["_HOL"]  = "Holokaust";
$factarray["_INTE"]	= "Pochowany(a)";
$factarray["_MARI"]	= "Zapowiedzi";
$factarray["_MBON"]	= "Intercyza";
$factarray["_MEDC"]	= "Stan zdrowia";
$factarray["_MILI"]	= "Wojskowe";
$factarray["_NMR"]	= "Nie żonaty / zamężna";
$factarray["_NMR"]	= "Nie jest w związku małżeńskim";
$factarray["_NMR "]	= "Bez ślubu";
$factarray["_NLIV"]	= "Nie żyje";
$factarray["_NMAR"]	= "Brak związków małżeńskich";
$factarray["_PRMN"]	= "Stały numer";
$factarray["_WEIG"]	= "Waga";
$factarray["_YART"]	= "Yartzeit";
$factarray["_MARNM"]	= "Nazwisko małżeńskie";
$factarray["_STAT"]	= "Status małżeński";
$factarray["MARR_RELIGIOUS"] = "Ślub kościelny";
$factarray["MARR_PARTNERS"] = "Zarejestrowane partnerstwo";
$factarray["MARR_UNKNOWN"] = "Małżeństwo nieznanego typu";
$factarray["_HNM"] = "Nazwisko hebrajskie";
$factarray["MARR_CIVIL"] = "Ślub cywilny";
$factarray["COMM"]	= "Komentarz";

if (file_exists( "languages/facts.pl.extra.php")) require  "languages/facts.pl.extra.php";

?>
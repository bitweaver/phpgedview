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
//-- GENERAL 
$pgv_lang["help_header"]			= "Informacje o:";
$pgv_lang["privacy_error_help"]		= "|<b>PRYWATNE DANE</b><br /><br />Jest kilka przyczyn pojawienia się takiego komunikatu:<br /><br /><b>1. Informacje o żyjących osobach ustawiono na \"Prywatne\"</b><br/> Niezajerestrowani użytkownicy którzy nie są zalogowani mają dostęp tylko do wszystkich danych osób zmarłych. Jeżeli pozwala administrator, możesz zarejestrować się wysyłając prośbę o nowe konto klikając na przycisk Zaloguj, a następnie na link #pgv_lang[requestpassword]#.<br /><br /><b>2. Masz już konto (nazwe użytkownika i hasło)......</b><br /> Ale nie jesteś zalogowany lub nieaktywny przez jakiś czas i twoja sesja wygasła.<br /><br /><b>3. Z powodu ochorony danych osobowych</b><br /> Każda osoba ma prawo poprosić administratora aby nie udostępniał ich danych osobowych innym (pojawia się \"Prywatne\") lub wogóle ukrył dane.<br/> Prywatność może być ustawiona dla:<br />a) #pgv_lang[PRIV_USER]#<br />b) #pgv_lang[PRIV_NONE]#<br />c) #pgv_lang[PRIV_HIDE]#<br /><br /><b> 4. Brak pokrewieństwa\"</b><br />Nawet jeżeli jesteś zarejestrowanym użytkownikiem <b>i</b> jesteś zalogowany, może pojawić się ten komunikat. Próbujesz przeglądać dane osoby z którą nie jesteś spokrewniony w określonej długości ścieżki pokrewieństwa określonej przez administratora.<br />Przykład:<br />";
$pgv_lang["more_help"]				= "<br />Jeśli chcesz mieć dostępną pomoc kontekstu na każdej stronie, upewnij sie że opcja <b>#pgv_lang[show_context_help]#</b> (w Menu Pomoc) jest włączona i kliknij na znak <b>?</b> za tematem.<br />";
$pgv_lang["more_config_help"]			= "<br /><b>Więcej pomocy</b><br />#pgv_lang[context_help]#<br /><br />";
$pgv_lang["readme_help"]			= "<center>Przeczytaj <a href=\"readme.txt\" target=\"_blank\"><b>Readme.txt</b></a> aby uzyskać więcej informacji.</center>";
$pgv_lang["RESN_help"]				= "~USTAWIENIA OGRANICZEŃ DLA WYDARZENIA~<br /><br />Niezalżnie od ogłólnych ustawień prywatności, PhpGedView ma możliwośc ustawienia dodatkowych restrykcji dla dostęu i edycji poszczególnych dancyh osób i rodzin. Restrykcje te może każdy ustawić i zmieniać, kto ma prawa dostępu do edytowania danych o ile ogólne ustawienia prywatności nie zabraniają tego.<br /><br />Następujące ustawienia są używane:<br /><ul><li><b>#pgv_lang[none]#</b><br/>Administratorzy strony i GEDCOM oraz użytkownicy, którzy mają prawa do edycji wydarzeń mogą zmieniać te dane. Dane wydarzenie jest możliwe do przeglądania zgodnie z ogólnymi ustawieniami prywatności ustawionymi przez administratora.</li><li><b>#pgv_lang[locked]#</b><br />To ustawienie nie ma wpływu na ograniczenie dostępu do danych tanego wydarzenia. Ogranicza prawa edycji pozwalając tylko na zmiany administratorom strony i GEDCOM. Jeżeli dane dotyczą określonego użytkownika, ma on także możliwość edycji tych danych.</li><li><b>#pgv_lang[privacy]#</b><br /> Tylko administratorzy strony i GEDCOM oraz użytkownik (o ile ma prawa edycji), którego dane dotyczą ma prawo wglądu i edycji tych danych. Dla pozostałych dane te będą ukryte niezależnie czy ktoś będzie zalogowany czy nie.</li><li><b>#pgv_lang[confidential]#</b><br />Wyłącznie administratorzy strony i GEDCOM mają prawo dostępu i edycji tych danych.</li></ul><br /><table><tr><th></th><th colspan=2>Administrator</th><th colspan=2>Właściciel</th><th colspan=2>Inni</th></tr><tr><th></th><th>R</th><th>W</th><th>R</th><th>W</th><th>R</th><th>W</th></tr><tr><td><img src=\"images/RESN_none.gif\" alt=\"\"/> #pgv_lang[none]#</td><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th></tr><tr><td><img src=\"images/RESN_locked.gif\" alt=\"\"/> #pgv_lang[locked]#</td><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th></tr><tr><td><img src=\"images/RESN_privacy.gif\" alt=\"\"/> #pgv_lang[privacy]#</td><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked_qm.gif\" /></th><th><img src=\"images/checked_qm.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th></tr><tr><td><img src=\"images/RESN_confidential.gif\" alt=\"\"/> #pgv_lang[confidential]#</td><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/checked.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th><th><img src=\"images/forbidden.gif\"/></th></tr></table><ul><li>R : może czytać</li><li>W : może edytować</li><li><img src=\"images/checked_qm.gif\"/> : zależy od ogólnych ustawień prywatności</li></ul>|";
$pgv_lang["index_help"]				= "~STRONA POWITALNA~<br /><br />To jest strona powitalna. Każda plik<a href=\"#def_gedcom\">Gedcom</a> ma własną stronę powitalną. Możesz zawsze wrócić do tej strony wybierając ją z głównego menu. Jeżeli istniej więcej plików Gedcom klikając w menu głównym pojawi sie lista plików, możesz wybrać który plik chcesz przeglądać..<br /><b>Ta strona pomocy zawiera informacje o:</b> (since this page is the main phpGedView site page)<ul><li><a href=\"#index_portal\">Welcome Page</a><li><a href=\"#header\">Header Area</a><li><a href=\"#menu\">Menus</a><li><a href=\"#header_general\">General Information</a><li><a href=\"#def\">Definitions</a></ul><br />Version #VERSION# brings a new look for <a href=\"#def_pgv\">phpGedView</a>. The menus, the starting page, and many other features are improved and/or revamped.<br /><br />";
$pgv_lang["index_portal_head_help"]		= "<div class=\"name_head\"><center><b>STRONA POWITALNA</b></center></div><br />|";
$pgv_lang["menu_help_help"]			= "~Help Menu ~<br />#pgv_lang[help_help_items]#|";

//-- Pages Help-messages
//-- Index-page

//-- Index-page Header  

//-- Index-page Menu 

//-- Index-page Portal
$pgv_lang["index_common_names_help"]		= "~NAJCZĘSCIEJ WYSTĘPUJĄCE NAZWISKA~<br /> List nazwisk znajdujących się w pliku GEDCOM jest wyświetlona w tym bloku. Liczbę nazwisk oskreśla administrator.<br />(Obecne ustawienia pozwalają wyświetlać nazwiska występujęce w pliku GEDCOM co najmniej #COMMON_NAMES_THRESHOLD# razy)<br /><br /> Klikając na nazwisko zostaniesz przenisiony do #pgv_lang[individual_list]#, gdzie możesz więcej dowiedzieć się o danym nazwisku.<br />";
$pgv_lang["index_loggedin_help"]		= "~LOGGED IN USERS BLOCK~<br />Blok ten wyświetla liczbę użytkowników obecnie zalogowanych.<br />|";

//-- Index-Page Help 

//-- Index-page Definitions

//-- Index-page MyGedcom 

$pgv_lang["mygedview_myjournal_help"]		= "~MÓJ DZIENNIK ~<br />Mo?esz u?ywa? tego dziennka do zapisywania notatek na w?asyn u?ytek.<br />Oczywi?cie notatki zostan? zapisane i nast?pnym razem kiedy wejdziesz na t? stron? b?d? tam nadal.<br /><br />Do dziennika nie maj? dost?pu inni u?ytkownicy.<br />";
$pgv_lang["PEDIGREE_GENERATIONS_help"]	= "~LICZBA POKOLEŃ~<br /><br />Tutaj możesz ustalić ilość wyświetlanych pokoleń.<br />Odpowiednia liczba zależy od rozmiaru twojego monitra i ustawionej rozdzielczości oraz czy chcesz mieć wyświetlone dodatkowe informacje o osobie.";


//-- Pedigree-page

//-- LOGIN-page 
$pgv_lang["login_page_help"]			= "~STRONA LOGOWANIA~<br /><br />Na tej stronie mo?esz si? zalogowac, poprosi? o nowe has?o lub o za?o?enie konta.<br />";
$pgv_lang["password_help"]				= "~HASŁO~<br /><br />W tym polu wpisz twoje hasło.<br />Wielkośc liter ma znaczenie.";
$pgv_lang["new_password_help"]			= "~POPROŚ O NEW HASŁO~<br />Jeżeli zapomniałeś swojego hasła, możesz uzyskać je klikając na odnośnik aby otworzyć je.<br />Zostaniesz przeniesiony do Strony \"Prośba o zagubione hasło\".|";
$pgv_lang["new_user_help"]			= "~POPROŚ O NOWE KONTO~<br /><br />Jeżeli jesteś gościem na tej stronie i chciałbyś mieć konto użytkownika na tej stronie, kliknij na ten link.<br />Otworzy się formularz rejestracyjny.";
$pgv_lang["relationship_id_help"]		= "~Numery identyfikacyjne OSOBY 1 i OSOBY 2~</b><br /><br />Jeżeli weszłeś na tą stronę z innej w portalu(na przykład: klikając na \"Pokrewieństwo ze mną\" ) zobaczysz pokrewieństwo pomiędzy tymi dwoma osobami.<br /> W innym razie musisz sam wpisać numery indentyfikacjyjne interesujących cię osób.<br /> Jeżeli ich nie znasz, kliknijąc na \"Znajdz ID\" możesz znaleźć je.";
$pgv_lang["name_list_help"]			= "~LISTA NAZWISK~<br /><br />W tym oknie pojawi się lista nazwisk lub lista nazwisk z imionami.<br /> W obu przypadkach nazwiska będą zaczynać sie od litery którą wybrałeś z indeksu alfabetycznego. O ile nie kliknąłęść \"Wszystkie\"<br /><br />To czy będziesz widzieć w liste nazwisk czy kompletną listę zależy od ustawień.";
$pgv_lang["add_child_help"]			= "~DODAJ DZIECKO DO TEJ RODZNIY~<br /><br />Klikając na ten odnośnik możesz dodać dziecko do tej rodziny.<br />Dodawanie nowego dziecka jest bardzo proste: Wystarczy kliknąć na ten odnośnik, uzupełnić pola w wyświetlonym formularzu i to wszystko.<br />";
$pgv_lang["show_fam_gedcom_help"]	= "~POKAŻ REKORD GEDCOM~<br /><br />Informacje o rodzinie przechowywane w bazie doanych zostaną wyświetlone po kliknięciu na ten link. Dane zostaną wyświetlone w formacie GEDCOM.<br /><br />";
$pgv_lang["username_help"]				= "|~NAZWA UŻYTKOWNIKA~<br /><br />W tym polu wpisz swóją nazwę użytkownika (login).<br />Wielkość liter jest ważna.";
//-- Descendancy-page

//-- Individuals-page
$pgv_lang["add_name_help"]				= "~DODAJ NOWE NAZWSKO~<br /><br />Ten link pozwala na dodadne innego nazwiska do tanej osoby. Czasami ludzie są znani pod innymi nazwiskami lub imionami'. Ten link pozwala na dodanie nowego nazwiska (imienia) do tanej osoby bez zmiany starego nazwiska.";
$pgv_lang["detected_ansi2utf_help"]		= "~WYKRYTO KODOWANIE W STANDRADZIE ANSI~<br />Pilk GEDCOM rozpoznany jako plik zapisany ze znakami w standardzie ANSI. Lepiej by było dokonać konwersji do standardu UTF-8 (bardziej uniwersalny).<br /><br /><br />#pgv_lang[convert_ansi2utf_help]#";
$pgv_lang["language_to_edit_help"]		= "#pgv_lang[edit_lang_utility]# >> <b>#pgv_lang[language_to_edit]#</b><br /><br />Z tej listy możesz wybrać język, którego komunikaty chcesz edytować.<br /><br />";
$pgv_lang["help_contents_head_help"]		= "<b>POMOC ZAWARTOŚCI</b><br /><br />";
$pgv_lang["help_contents_gedcom_places"]	= "Miejsca w pliku GEDCOM";
$pgv_lang["ah6_help"]				= "_GEDCOM:  Usuń";
$pgv_lang["ah7_help"]				= "_GEDCOM: Dodaj";
$pgv_lang["ah11_help"]				= "_GEDCOM: Konfiguruj";
$pgv_lang["ah16_help"]				= "_GEDCOM: Ustawienia prywatności";
$pgv_lang["ah17_help"]				= "Zarządzanie użytkownikami";
$pgv_lang["ah18_help"]				= "_Administracja";
$pgv_lang["ah21_help"]				= "_Pliki językowe";
$pgv_lang["simple_filter_help"]		= "~#pgv_lang[filter]#~<br /><br />Prosty filtr szukania oparty na wpisanych literach, znaki uniwersanle(np.*,?) nie są dopuszaczalne.";
$pgv_lang["year_help"]				= "~#pgv_lang[alive_in_year]#~<br /><br />Wpisz rok, w którym szukasz żyjących osób.<br /><br />";
$pgv_lang["show_thumb_help"]		= "~#pgv_lang[show_thumbnail]#~<br /><br />Miniaturki będą wyświetlane jeżeli zaznaczysz te pole.<br /><br />";



$pgv_lang["register_comments_help"]	= "~#pgv_lang[comments]#~<br /><br />Użyj tego pola aby przekazać administratorowi dlaczego prosisz o zalożenie konta i w jaki sposób jesteś spokrewniony/a z rodziną, której genealogia znajduje się na tej stroenie. Możesz także wisać inny kommentarz jak chcesz przesłać administratorowi.";
$pgv_lang["edit_NCHI_help"]		= "~#factarray[NCHI]#~<br /><br />Wpisz liczbę dziedzi jaką miała dana osoba. Jest to pole opcjonalne.<br /><br />";
$pgv_lang["edit_TIME_help"]		= "~#factarray[TIME]# HELP~<br /><br />Wpisz czas wydarzenia. Właściwy format HH:MM. Przykłady: 04:50 13:00 20:30.<br /><br />";
$pgv_lang["edit_NOTE_help"]		= "~#factarray[NOTE]# HELP~<br /><br />Jest to wolne miejsce na dowolny tekst, który ukaże się w częsci szczegóły zdarzenia..<br /><br />";
$pgv_lang["edit_CEME_help"]		= "~#factarray[CEME]# HELP~<br /><br />Wpisz nazwę cmentarza lub innego miejsce pochówku gdzie dana osoba została pochowana.<br /><br />";
$pgv_lang["edit__THUM_help"]		= "~#factarray[_THUM]#~<br /><br />To pole pozwala zaznaczyć wybraną miniaturkę zdjęcia, która będzie użyta na wykresach nawet jeżeli miniaturka nie istnieje dla danego zdjęcia.<br /><br />";
$pgv_lang["edit_add_parent_help"]	= "~DODAJ NOWĄ  MATKĘ LUB OJCA~<br/><br/>Na tej stronie możesz dodać do danej osoby nową matkę lub ojca. Wpisz dla tej nowej osoby informację o narodzinach i śmierci jeśli są znane. Pozostaw pola puste dla informacji, których nie znasz. <br/><br/>Aby dodać inne nowe wydarzenia(poza informacjami o narodzinach i śmierci) do dodanej osoby należy najpierw zapisać zmiany w bazie danych.Następnie kliknij na nazwisko osoby na zaktualzowanej stronie rodziny albo zakładkę #pgv_lang[relatives]#  aby prześć do strony #pgv_lang[indi_info]#. Na stronie #pgv_lang[indi_info]# bardziej szczegółowe informacje.<br/><br/>";
$pgv_lang["edit_death_help"] = "~#pgv_lang[death]# HELP~<br /><br />Te pole pozwala na wpisanie informacji o zgonie. Najpierw wpisz datę kiedy dana osoba zmarła w standardowym formacie (1 JAN 2004) lub wybierz za pomocą kalendarza. Później wpisz miejsce gdzie dana osoba zmarła. Możesz użyć \"Znajdź miejsce\" aby znaleźć miejsce, wpisane już przy innej osobie lub zdarzeniu.<br /><br />";
$pgv_lang["edit_sex_help"]	= "~#pgv_lang[sex]#~<br /><br />Wybierz właściwą płeć z listy. Opcja <b>nieznana</b> wskazuje że płeć danej osoby nie jest znana.<br /><br />";
$pgv_lang["edit_given_name_help"]	= "~#pgv_lang[given_name]#~<br /><br />W tym polu wpisz imiona danej osoby. Przykład: \"John Robert\"<br /><br />";
$pgv_lang["edituser_user_default_tab_help"]	= "~USER DEFAULT TAB SETTING~<br /><br /> To ustawienie pozwala wybrać, która zakładka powinna się automatycznie otwierać przy otwieraniu strony  #pgv_lang[indi_info]#.<br /><br />";
$pgv_lang["edit_EMAIL_help"]		= "Wpisz adres poczty elektronicznej (email).<br /><br />Przykładowy adres wygląda w następujący sposób: <b>name@hotmail.com</b> Pozostaw to pole wolne jeśli nie chcesz podawać adresu email.";
$pgv_lang["edit_FAX_help"]			= "Wpisz numer faksu włączając numer kierunkowy kraju i miasta.<br /><br /> Pozostaw to pole puste jeśli nie chcesz wpisywać numeru faksu. Przykładowy numer dla Niemiec wygląda w tak: +49 25859 56 76 89 a numer dla USA lub Kanady: +1 888 555-1212.|";
$pgv_lang["edit_PHON_help"]			= "Wpisz numer telefonu wraz z numerem kierunkowym kraju i miasta.<br/><br/>Pozostaw to pole puste jeśnie nie chcesz wpisywać numeru telefonu. Na przykład numer do Niemiec wygląda w następujący sposób: +49 2567 78 47 47.";
$pgv_lang["edit_ADDR_help"]			= "Wpisz adres pocztowy tak samo jakbyś napisał go na kopercie .<br /><br />Pozostaw te pole puste jeśli nie chcesz wpisywać adresu.";
$pgv_lang["context_help"]			= "Więcej pomocy jest dostępne przez kliknięcie <b>?</b> obok wybranej pozycji na stronie.";
$pgv_lang["edit_URL_help"]			= "Wpisz adres URL wraz z http://.<br /><br />Przykładowy adres URL wygląda tak: <b>http://www.phpgedview.net/</b> Pozostaw to pole puste jeśli nie chcesz wpisywać adresu URL.";
$pgv_lang["edit_SEX_help"]			= "~Edytuj #factarray[SEX]#~<br /><br />Użyj tego pola wskazując czy osoba jest <b>#pgv_lang[male]#</b> czy <b>#pgv_lang[female]#</b>. Wybierz <b>#pgv_lang[unknown]#</b> tylko jeśli nie jesteś pewny płci danej osoby.";
$pgv_lang["edit_NAME_help"]			= "~Edit #factarray[NAME]#~<br /><br />Nazwisko osoby powinno być zapisane pomiędzy znakami \"/\".<br /><br />Oto przykłady:<ul><li>Imiona /Nazwisko </li><li>Imiona /Nazwisko/ Jr.</li><li>Imiona /von Nazwisko/ jr.</li></ul>";

?>
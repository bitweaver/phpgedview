<?php
/**
 * Polish Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Tymoteusz Motylewski www.motylewscy.com
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
 * @author Tymoteusz Motylewski www.motylewscy.com
 * @version $Id$
 */
if (preg_match("/configure_help\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
//-- CONFIGURE FILE MESSAGES
$pgv_lang["configure"]			= "Konfiguracja PhpGedView";
$pgv_lang["gedconf_head"]		= "Konfiguracja GEDCOM";
$pgv_lang["configure_head"]		= "Konfiguracja PhpGedView";
$pgv_lang["advanced_conf"]		= "Opcje zaawansowanej konfiguracji";
$pgv_lang["standard_conf"]		= "Opcje standartowej konfiguracji";
$pgv_lang["default_user"]		= "Utwórz domyślnego użytkownika- administratora.";
$pgv_lang["about_user"]			= "Musisz najpierw utworzyć  konto głównego użytkownika - administratora.  Ten użytkownik będzie miał prawo do uaktualniania plików konfiguracyjnych, przeglądania prywatnych danych, oraz tworzenia nowych użytkowników.";
$pgv_lang["fullname"]			= "Imię i nazwisko:";
$pgv_lang["confirm"]			= "Powtórz hasło:";
$pgv_lang["add_user"]			= "Dodaj nowego użytkownika";
$pgv_lang["current_users"]		= "Lista użytkowników";
$pgv_lang["current_users"]		= "Lista użytkowników";
$pgv_lang["leave_blank"]		= "Pozostaw miejsce na hasło puste, jeśli chcesz zachować aktualne hasło.";
$pgv_lang["does_not_exist"]		= "nie istnieje";
$pgv_lang["config_write_error"]		= "Błąd podczas zapisywania pliku konfiguracyjnego.  Sprawdź atrybuty i sprbĂłj ponownie.";
$pgv_lang["db_setup_bad"]		= "Your current database configuration is bad.  Please check your database connection parameters and configure again.";
$pgv_lang["click_here_to_continue"]	= "Kliknij tutaj by kontynuować";
$pgv_lang["config_help"]		= "Configuration Help";
$pgv_lang["admin_gedcoms"]		= "Kliknij tutaj by administrować plikami GEDCOM.";
$pgv_lang["ged_download"]		= "ściągnij";
$pgv_lang["ged_gedcom"]			= "Plik GEDCOM";
$pgv_lang["confirm_gedcom_delete"]	= "Czy napewno chcesz skasować ten plik GEDCOM?";
$pgv_lang["gregorian"]			= "Gregoriański";
$pgv_lang["julian"]			= "Juliański";
$pgv_lang["config_french"]		= "Francuski";
$pgv_lang["jewish"]			= "Żydowski";
$pgv_lang["config_hebrew"]		= "Hebrajski";
$pgv_lang["jewish_and_gregorian"]	= "Żydowski i Gregoriański";
$pgv_lang["hebrew_and_gregorian"]	= "Hebrajski i Gregoriański";
$pgv_lang["disabled"]			= "Wyłączony";
$pgv_lang["click"]			= "Po kliknięciu myszą";
$pgv_lang["daily"]			= "Codziennie";

$pgv_lang["DBUSER"]			= "Login do bazy MySQL:";
$pgv_lang["DBUSER_help"]		= "The MySQL database username required to login to your database.  This sets the \$DBUSER variable in the config.php file.";
$pgv_lang["DBPASS"]			= "Haslo do bazy MySQL:";
$pgv_lang["DBNAME"]			= "Nazwa bazy danych:";
$pgv_lang["TBLPREFIX"]			= "Prefiks Tabeli bazy danych:";
$pgv_lang["last_login"]			= "Ostatnio zalogowany(a)";
$pgv_lang["DEFAULT_GEDCOM"]		= "Domyślny GEDCOM:";
$pgv_lang["ALLOW_CHANGE_GEDCOM"]	= "Zezwól gosciom zmieniać pliki GEDCOM:";
$pgv_lang["GEDCOM"]			= "Ścieżka GEDCOM:";
$pgv_lang["CHARACTER_SET"]		= "Kodowanie znaków:";
$pgv_lang["CHARACTER_SET_help"]		= "To jest kodowanie twojego pliku GEDCOM.  UTF-8 jest domyślnym kodowaniem i powinno działać dla prawie wszystkich stron.  Jeśli eksportowałeś(łaś) twój plik GEDCOM używając kodowania ibm windows, powinieneś(naś) wpisać WINDOWS tutaj.<br /><br />Ta opcja zmienia zmienną \$CHARACTER_SET w pliku config.php .<br /><br />UWAGA: PHP NIE obsługuje UNICODE (UTF-16) więc nie próbuj ustawiać, i poźniej się skarżyć na problemy z PHP :-)";
$pgv_lang["LANGUAGE"]			= "Język:";
$pgv_lang["LANGUAGE_help"]		= "Ustaw domyślny język dla strony.  Users have the ability to override this setting using their browser preferences or the form at the bottom of the page if ENABLE_MULTI_LANGUAGE = true.<br /><br />This sets the \$LANGUAGE variable in the config.php file.";
$pgv_lang["ENABLE_MULTI_LANGUAGE"]	= "Zezwalaj użytkownikom na zmianę języka:";
$pgv_lang["ENABLE_MULTI_LANGUAGE_help"]	= "Set to 'yes' to give users the option of selecting a different language from a dropdown list in the footer and default to the language they have set in their browser settings.<br /><br />This sets the \$ENABLE_MULTI_LANGUAGE variable in the config.php file.";
$pgv_lang["CALENDAR_FORMAT"]		= "Rodzaj kalendarza:";
$pgv_lang["DEFAULT_PEDIGREE_GENERATIONS"]	= "Pokoleń na drzewie:";
$pgv_lang["USE_RIN"]			= "Użyj RIN# zamiast GEDCOM ID:";
$pgv_lang["GEDCOM_ID_PREFIX"]		= "Prefiks ID GEDCOM:";
$pgv_lang["PEDIGREE_FULL_DETAILS"]	= "Pokazuj szczegóły urodzin i śmierci na wykresach i drzewie gnealogicznym:";
$pgv_lang["SHOW_EMPTY_BOXES"]		= "Pokazuj puste prostokąty na drzewie gen.:";
$pgv_lang["ZOOM_BOXES"]			= "Przyblizaj prostokaty na drzewie:";
$pgv_lang["AUTHENTICATION_MODULE"]	= "Plik modułu uwierzytelniania:";
$pgv_lang["PRIVACY_MODULE"]		= "Plik prywatnosci:";
$pgv_lang["HIDE_LIVE_PEOPLE"]		= "Ukryj żyjące osoby:";
$pgv_lang["CHECK_CHILD_DATES"]		= "Sprawdź daty dzieci:";
$pgv_lang["ALLOW_EDIT_GEDCOM"]		= "Zezwól na edycję on-line:";
$pgv_lang["INDEX_DIRECTORY"]		= "Katalog pliku Index:";
$pgv_lang["ALPHA_INDEX_LISTS"]		= "Połam długie listy według pierwszej litery:";
$pgv_lang["SHOW_ID_NUMBERS"]		= "Pokazuj numery ID obok imienia:";
$pgv_lang["MULTI_MEDIA"]		= "Włącz obsługę multimediów:";
$pgv_lang["MEDIA_DIRECTORY"]		= "Katalog multimedia:";
$pgv_lang["HIDE_GEDCOM_ERRORS"]		= "Ukrywaj błędy GEDCOM:";
$pgv_lang["CONTACT_EMAIL"]		= "Email genealoga:";
$pgv_lang["WEBMASTER_EMAIL"]		= "Email webmastera:";
$pgv_lang["FAVICON"]			= "Ikona ulubionych:";
$pgv_lang["THEME_DIR"]			= "Katalog motywów:";
$pgv_lang["TIME_LIMIT"]			= "Limit czasu PHP:";
$pgv_lang["PGV_SESSION_SAVE_PATH"]	= "Ścieżka zapisu sesji:";
$pgv_lang["PGV_SESSION_TIME"]		= "Limit czasu sesji:";
$pgv_lang["SHOW_STATS"]			= "Pokazuj statystyki wykonywania:";

$pgv_lang["save_config"] 	= "Zapisz konfigurację";
$pgv_lang["reset"] 		= "Resetuj";
$pgv_lang["download_here"]	= "Kliknij tutaj by ściągnąć plik.";
$pgv_lang["download_file"]	= "ściągnij plik";

//-- edit privacy messages
$pgv_lang["edit_privacy"]			= "Configuration of the privacy-file";
$pgv_lang["save_changed_settings"]		= "Zapisz zmiany";
$pgv_lang["SHOW_DEAD_PEOPLE"]			= "Pokaż nieżyjących";

//-- language edit utility
$pgv_lang["edit_langdiff"]			= "Edytuj zawartość plików językowych";
$pgv_lang["language_to_edit"]			= "Język do edytowania";
$pgv_lang["file_to_edit"]			= "Typ pliku";
$pgv_lang["check"]			= "Sprawdź";
$pgv_lang["lang_save"]				= "Zapisz";
$pgv_lang["contents"]				= "Zawartość";
$pgv_lang["cancel"]				= "Anuluj";
$pgv_lang["time_format"]		= "Format czasu";
$pgv_lang["rtl"]			= "Od prawej do lewej";
$pgv_lang["never"]					= "Nigdy";
$pgv_lang["hide_translated"]		= "Ukryj przetłumaczone";
$pgv_lang["add_new_lang_button"]	= "Dodaj nowy język";
$pgv_lang["system_time"]		= "Czas systemowy";
$pgv_lang["ltr"]			= "Od lewej do prawej";
$pgv_lang["name_reverse"]		= "Nazwiska pierwsze ?";
$pgv_lang["name_reverse"]		= "Imiona pierwsze ?";
$pgv_lang["week_start"]			= "Pierwszy dzień tygodnia";
$pgv_lang["help_filename"]		= "Plik pomocy";
$pgv_lang["facts_filename"]		= "Plik faktów";
$pgv_lang["lang_filename"]		= "Plik językowy";
$pgv_lang["lang_new_language"]		= "Nowy język";
$pgv_lang["lang_name_czech"]		= "Czeski";
$pgv_lang["lang_name_polish"]		= "Polski";
$pgv_lang["lang_language"]		= "Język";
$pgv_lang["lang_edit"]			= "Edytuj";
$pgv_lang["export"]			= "Eksportuj";

?>
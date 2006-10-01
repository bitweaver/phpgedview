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

if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "Du har ikke direkte tilgang til en språkfil.<br />You cannot access a language file directly.";
	exit;
}

$pgv_lang["roman_surn"]				= "Romanisert etternavn";
$pgv_lang["roman_givn"]				= "Romanisert fornavn";
$pgv_lang["hebrew_surn"]			= "Hebraisk etternavn";
$pgv_lang["hebrew_givn"]			= "Hebraisk fornavn";
$pgv_lang["inc_languages"]			= " Språk";
$pgv_lang["date_of_entry"]			= "Angitt dato i originalkilde";
$pgv_lang["main_media_ok1"]			= "Hovedmediafilen <b>#GLOBALS[oldMediaName]#</b> heter nå <b>#GLOBALS[newMediaName]#</b>.";
$pgv_lang["main_media_ok2"]			= "Hovedmediafilen <b>#GLOBALS[oldMediaName]#</b> er nå flyttet fra <b>#GLOBALS[oldMediaFolder]#</b> til <b>#GLOBALS[newMediaFolder]#</b>.";
$pgv_lang["main_media_ok3"]			= "Hovedmediafilen er flyttet eller gitt nytt navn fra <b>#GLOBALS[oldMediaFolder]##GLOBALS[oldMediaName]#</b> til <b>#GLOBALS[newMediaFolder]##GLOBALS[newMediaName]#</b>.";
$pgv_lang["main_media_fail0"]			= "Hovedmediafilen <b>#GLOBALS[oldMediaFolder]##GLOBALS[oldMediaName]#</b> finnes ikke.";
$pgv_lang["main_media_fail1"]			= "Klarte ikke å gi hovedmediafilen <b>#GLOBALS[oldMediaName]#</b> det nye navnet <b>#GLOBALS[newMediaName]#</b>.";
$pgv_lang["main_media_fail2"]			= "Klarte ikke å flytte hovedmediafilen <b>#GLOBALS[oldMediaName]#</b> fra <b>#GLOBALS[oldMediaFolder]#</b> til <b>#GLOBALS[newMediaFolder]#</b>.";
$pgv_lang["main_media_fail3"]			= "Klarte ikke å flytte eller å gi nytt navn til hovedmediafilen fra <b>#GLOBALS[oldMediaFolder]##GLOBALS[oldMediaName]#</b> til <b>#GLOBALS[newMediaFolder]##GLOBALS[newMediaName]#</b>.";
$pgv_lang["thumb_media_ok1"]			= "Miniatyrbildefilen <b>#GLOBALS[oldMediaName]#</b> heter nå <b>#GLOBALS[newMediaName]#</b>.";
$pgv_lang["thumb_media_ok2"]			= "Miniatyrbildefilen <b>#GLOBALS[oldMediaName]#</b> er nå flyttet fra <b>#GLOBALS[oldThumbFolder]#</b> til <b>#GLOBALS[newThumbFolder]#</b>.";
$pgv_lang["thumb_media_ok3"]			= "Miniatyrbildefilen er flyttet eller gitt nytt navn fra <b>#GLOBALS[oldThumbFolder]##GLOBALS[oldMediaName]#</b> til <b>#GLOBALS[newThumbFolder]##GLOBALS[newMediaName]#</b>.";
$pgv_lang["thumb_media_fail0"]			= "Miniatyrbildefilen <b>#GLOBALS[oldThumbFolder]##GLOBALS[oldMediaName]#</b> finnes ikke.";
$pgv_lang["thumb_media_fail1"]			= "Klarte ikke å gi miniatyrbildefilen <b>#GLOBALS[oldMediaName]#</b> det nye navnet <b>#GLOBALS[newMediaName]#</b>.";
$pgv_lang["thumb_media_fail2"]			= "Klarte ikke å flytte miniatyrbildefilen <b>#GLOBALS[oldMediaName]#</b> fra <b>#GLOBALS[oldThumbFolder]#</b> til <b>#GLOBALS[newThumbFolder]#</b>.";
$pgv_lang["thumb_media_fail3"]			= "Klarte ikke å flytte eller å gi nytt navn til miniatyrbildefilen fra <b>#GLOBALS[oldThumbFolder]##GLOBALS[oldMediaName]#</b> til <b>#GLOBALS[newThumbFolder]##GLOBALS[newMediaName]#</b>.";
$pgv_lang["server_file"]				= "Filnavn på server";
$pgv_lang["server_file_advice"]			= "Ikke bytt for å beholde originalt filnavn.";
$pgv_lang["server_file_advice2"]		= "Du kan angi en URL, som starter med &laquo;http://&raquo;.";
$pgv_lang["server_folder"]				= "Mappenavn på server";
$pgv_lang["server_folder_advice"]		= "Du kan angi opptil #GLOBALS[MEDIA_DIRECTORY_LEVELS]# mappenavn under standardmappen &laquo;#GLOBALS[MEDIA_DIRECTORY]#&raquo;.<br />Ikke angi &laquo;#GLOBALS[MEDIA_DIRECTORY]#&raquo; -delen av navnet til målmappen.";
$pgv_lang["server_folder_advice2"]		= "Denne oppføringen vil bli ignorert dersom du har angitt en URL i feltet for filnavn.";
$pgv_lang["add_linkid_advice"]			= "Angi eller søk etter IDen til personen, familien eller kilden som dette mediaobjektet skal kobles til.";
$pgv_lang["use_browse_advice"]			= "Bruk knappen &laquo;Bla gjennom&raquo; for å finne ønsket fil på din lokale datamaskin.";
$pgv_lang["add_media_other_folder"]		= "Andre mapper... - skriv inn";
$pgv_lang["add_media_file"]				= "Eksisterende mediafile på server";
$pgv_lang["link_to_existing_media"]		= "Kobling til et mediaobjekt som finnes fra før";
$pgv_lang["page_size"]					= "Sidestørrelse";
$pgv_lang["record_not_found"]			= "Fant ikke den ønskede slektbase-oppføringen. Det kan ha sin årsak i en kobling til en ugyldig person eller en ødelagt slektsfil (ged).";
$pgv_lang["record_marked_deleted"]		= "Denne oppføringen er merket for sletting og venter på godkjenning av admin.";
$pgv_lang["result_page"]				= "Resultatside";
$pgv_lang["edit_media"]					= "Rediger mediaobjekt";
$pgv_lang["wiki_main_page"]				= "Wiki hovedside";
$pgv_lang["wiki_users_guide"]			= "Wiki brukerveiledning";
$pgv_lang["wiki_admin_guide"]			= "Wiki administrator-veiledning";
$pgv_lang["no_search_for"]				= "Husk å velge en katagori å søke etter.";
$pgv_lang["no_search_site"]				= "Husk å velge minst et eksternt nettsted.";
$pgv_lang["other_searches"]				= "Andre søk";
$pgv_lang["multi_site_search"] 			= "Søk på flere nettsteder";
$pgv_lang["search_sites"] 				= "Søk på nettsteder";
$pgv_lang["search_sites_discription"] 	= "Søk på kjente nettsteder";
$pgv_lang["search_asso_text_unavailable"] = " Tilknytning<br /> er ikke mulig enda med <br /> søk på nettsteder";
$pgv_lang["no_known_servers"]		= "Ingen kjente tjenere<br />Ingen resultater vil vises";
$pgv_lang["basic_search_discription"] = "Enkelt søk på nettsteder";
$pgv_lang["advanced_search_discription"] = "Avansert søk på nettsteder";
$pgv_lang["basic_search"]			= "søk";
$pgv_lang["advanced_search"]		= "Avansert søk på nettsteder";
$pgv_lang["name_search"]			= "Navn: ";
$pgv_lang["birthdate_search"]		= "Fødselsdato: ";
$pgv_lang["birthplace_search"]		= "Fødselssted: ";
$pgv_lang["deathdate_search"]		= "Dødsdato: ";
$pgv_lang["deathplace_search"]		= "Dødssted: ";
$pgv_lang["gender_search"]			= "Kjønn: ";
$pgv_lang["site_list"]				= "Nettsteder: ";
$pgv_lang["site_had"]				= " inneholder følgende";
$pgv_lang["invalid_search_multisite_input"] = "Du må angi en av disse:  Navn, fødselsdato, fødselssted, dødsdato, dødssted eller kjønn ";
$pgv_lang["invalid_search_multisite_input_gender"] = "Du må søke på nytt med flere opplysninger enn bare kjønn";

//Remote Link
$pgv_lang["link_manage_servers"]    = "Oppsett for nettsteder";
$pgv_lang["indi_is_remote"]			= "Opplysninger om denne personen er hentet fra et eksternt nettsted.";
$pgv_lang["link_remote"]            = "Lag kobling til ekstern person";
$pgv_lang["current_person"]         = "Samme som nåværende";
$pgv_lang["title_remote_link"]      = "Legg til ekstern kobling";
$pgv_lang["title_search_link"]      = "Legg til intern kobling";
$pgv_lang["label_same_server"]      = "Samme nettsted";
$pgv_lang["label_diff_server"]      = "Annet nettsted";
$pgv_lang["label_accept_changes"]   = "Godkjenn alle endringer";
$pgv_lang["label_ask_first"]        = "Spør meg først";
$pgv_lang["label_rel_to_current"]   = "Tilknytning til nåværende person";
$pgv_lang["label_location"]         = "Plassering";
$pgv_lang["label_site"]             = "Nettsted";
$pgv_lang["label_site_url"]         = "URL til nettsted:";
$pgv_lang["label_site_url2"]        = "URL til nettsted";
$pgv_lang["label_remote_id"]        = "ID til ekstern person";
$pgv_lang["label_local_id"]         = "Person-ID";
$pgv_lang["label_merge_options"]    = "Valg for sammenslåing";
$pgv_lang["label_gedcom_id"]        = "GEDCOM-ID";
$pgv_lang["label_gedcom_id2"]       = "GEDCOM-ID:";
$pgv_lang["label_add_remote_link"]  = "Legg til kobling";
$pgv_lang["error_remote"]           = "Du har valgt et eksternt nettsted.";
$pgv_lang["error_same"]             = "Du har valgt det samme nettstedet.";
$pgv_lang["error_server_exists"]    = "Nettstedet som du ønsker å legge til, er registert fra før;\n Velg en fra listen under\nBruk et eksisterende nettsted.";
$pgv_lang["lbl_server_list"]        = "Bruk et eksisterende nettsted.";
$pgv_lang["lbl_type_server"]        = "Angi et nytt nettsted.";
$pgv_lang["error_url_blank"]		= "Tittel og URL til eksternt nettsted må fylles ut";
$pgv_lang["error_siteauth_failed"]	= "Fikk ikke tilgang til eksternt nettsted";
$pgv_lang["label_ban_server"]		= "Utfør";
$pgv_lang["label_add_search_server"]	= "Legg til IP";
$pgv_lang["remove_ip"] 				= "Fjern IP";
$pgv_lang["label_remove_ip"]		= "Bannlys IP-adresse (f.eks: 198.128.*.*): ";
$pgv_lang["label_remove_search"]	= "Merk IP-adresser som Søkemaskinspioner: ";
$pgv_lang["label_username_id"]		= "Brukernavn";
$pgv_lang["label_username_id2"]		= "Brukernavn: ";
$pgv_lang["label_password_id"]		= "Passord";
$pgv_lang["label_password_id2"]		= "Passord: ";
$pgv_lang["error_ban_server"]       = "Ugyldig IP-adresse.";
$pgv_lang["error_view_connections"] = "Du må velge nettstedet som skal vises.";
$pgv_lang["error_delete_server"]    = "Du må velge nettstedet som skal slettes.";
$pgv_lang["title_manage_servers"]   = "Oppsett for nettsteder";
$pgv_lang["label_banned_servers"]   = "Bannlys nettsteder ved IP";
$pgv_lang["label_manual_search_engines"]   = "Merk søkemaskiner manuelt ved IP";
$pgv_lang["label_search_engine_detected"]  = "Oppdagede søkemaskinspioner";
$pgv_lang["label_search_engine_spider"]    = "Søkemaskinspion";
$pgv_lang["label_ban_view_links"]   = "Vis kobling til dette bannlyste nettstedet";
$pgv_lang["label_delete"]           = "Slett";
$pgv_lang["link_success"]			= "Kobling er nå lagt til";
$pgv_lang["label_new_server"]       = "Legg til nytt nettsted";
$pgv_lang["label_server_url"]       = "URL/IP til nettsted";
$pgv_lang["label_add_server"]       = "Legg til";
$pgv_lang["label_added_servers"]	= "Eksterne nettsteder som er lagt til";
$pgv_lang["error_url"]              = "Et nettsted må ha en URL/IP.";
$pgv_lang["error_exists_server"]    = "Nettstedet er registrert fra før.";
$pgv_lang["error_view_info"]        = "Du må velge personen som du ønsker å se opplysninger om.";
$pgv_lang["error_delete_person"]    = "Du må velge personen som du ønsker å slette den eksterne koblingen til.";
$pgv_lang["title_view_conns"]       = "Vis tilkoblinger";
$pgv_lang["label_server_info"]      = "Alle personer som har en kobling til dette nettstedet:";
$pgv_lang["label_view_local"]       = "Vis opplysninger om personen som er lagre lokalt";
$pgv_lang["label_view_remote"]      = "Vis opplysninger om personen som er lagret eksternt";
$pgv_lang["label_individuals"]      = "Personer";
$pgv_lang["label_families"]         = "Familier";

$pgv_lang["ex-spouse"] 				= "Eks-ektefelle";
$pgv_lang["ex-wife"] 				= "Eks-hustru";
$pgv_lang["ex-husband"] 			= "Eks-ektemann";
$pgv_lang["noemail"] 				= "Adresser uten epost";
$pgv_lang["onlyemail"] 				= "Bare adresser med epost";
$pgv_lang["maxviews_exceeded"]		= "Du har ikke tilgang til å se flere sider. - Prøv igjen senere.";
$pgv_lang["broadcast_not_logged_6mo"]	= "Send beskjed til brukere som ikke har logget seg inn siste 6 måneder";
$pgv_lang["broadcast_never_logged_in"]	= "Send beskjed til brukere som aldri har logget seg inn";
$pgv_lang["stats_to_show"]			= "Velg statistikk som skal vises i denne rammen";
$pgv_lang["stat_avg_age_at_death"]	= "Gjennomsnittsalder ved død";
$pgv_lang["stat_longest_life"]		= "Person som har levd lengst";
$pgv_lang["stat_most_children"]		= "Familie med flest barn";
$pgv_lang["stat_average_children"]	= "Gjennomsnittsantall med barn pr. familie";
$pgv_lang["stat_events"]			= "Antall hendelser";
$pgv_lang["stat_surnames"]			= "Antall etternavn";
$pgv_lang["stat_users"]				= "Antall brukere";
$pgv_lang["no_family_facts"]		= "Ingen faktopplysninger for denne familien.";

$pgv_lang["sunday_1st"]				= "S";
$pgv_lang["monday_1st"]				= "M";
$pgv_lang["tuesday_1st"]			= "Ti";
$pgv_lang["wednesday_1st"]			= "O";
$pgv_lang["thursday_1st"]			= "To";
$pgv_lang["friday_1st"]				= "F";
$pgv_lang["saturday_1st"]			= "L";

$pgv_lang["january_1st"]			= "Jan";
$pgv_lang["february_1st"]			= "Feb";
$pgv_lang["march_1st"]				= "Mars";
$pgv_lang["april_1st"]				= "April";
$pgv_lang["may_1st"]				= "Mai";
$pgv_lang["june_1st"]				= "Juni";
$pgv_lang["july_1st"]				= "July";
$pgv_lang["august_1st"]				= "Aug";
$pgv_lang["september_1st"]			= "Sep";
$pgv_lang["october_1st"]			= "Okt";
$pgv_lang["november_1st"]			= "Nov";
$pgv_lang["december_1st"]			= "Des";

$pgv_lang["edit_source"]			= "Rediger kilde";
$pgv_lang["source_menu"]			= "Valg for kilde";
$pgv_lang["familybook_chart"]		= "Familiebok";
$pgv_lang["family_of"]				= "Familie av:&nbsp;";
$pgv_lang["descent_steps"]			= "Etterkommerledd";

$pgv_lang["user_auto_accept"]		= "Godkjenn automatisk endringer gjort av denne brukeren";
$pgv_lang["cancel"]					= "Avbryt";
$pgv_lang["cookie_help"]			= "Dette nettstedet bruker 'cookies' for å følge med på din innloggings-status.<br /><br />Det ser ut som om at cookies ikke er godtatt i nettleseren din. Du må aktivere cookies for dette nettstedet før du kan logge deg inn.  Du kan se i hjelpefilen til nettleseren din for informasjon om å aktivere cookies.";

$pgv_lang["change"]					= "Endre";
$pgv_lang["change_family_instr"]	= "Bruk denne siden til å endre eller fjerne familiemedlemmer.<br /><br />For hvert medlem i familien kan du bruke valget Endre for å velge en annen person som skal ta denne rollen i familien.  Du kan også bruke valget Fjern for å fjerne denne personen fra familien.<br /><br />Når du er ferdig med å gjøre endringer for familiemedlemmene, klikker du på knappen Lagre for å lagre endringene.<br />";
$pgv_lang["change_family_members"]	= "Bytt medlemmer av familien";
$pgv_lang["delete_family_confirm"]	= "Dersom du sletter denne familien vil alle koblinger mellom familiemedlemmene også bli fjernet. Personene vil selvsagt <u>ikke</u> bli slettet !<br />Er du sikker på at du vil slette denne familien ?";
$pgv_lang["delete_family"]			= "Slett familie";
$pgv_lang["add_favorite"]			= "Legg til en ny favoritt";
$pgv_lang["url"]					= "URL";
$pgv_lang["add_fav_enter_note"]		= "Beskrivelse av denne favoritten (valgfritt)";
$pgv_lang["add_fav_or_enter_url"]	= "eller<br />\nen internettadresse (URL) og en tittel";
$pgv_lang["add_fav_enter_id"]		= "Angi ID til en person, family eller kilde";
$pgv_lang["import_time_exceeded"]	= "Maks utføringstid for en handling er nådd !<br />- Klikk på knappen \"Fortsett\" for å gjenoppta importen av slektsfilen (som ny handling).";
$pgv_lang["next_email_sent"]		= "Neste påminnelse vil bli sendt med epost etter ";
$pgv_lang["last_email_sent"]		= "Siste påminnelse med epost ble sendt ";
$pgv_lang["confirm_remove"]			= "Er du sikker på at du vil fjerne koblingen til familien for denne personen?";
$pgv_lang["remove_child"]			= "Fjerne koblingen til familien for dette barnet";
$pgv_lang["link_new_husb"]			= "Legg til en ektemann/partner ved å bruke en registrert person";
$pgv_lang["link_new_wife"]			= "Legg til en hustru/partner ved å bruke en registrert person";
$pgv_lang["address_labels"]			= "Adresse-etiketter";
$pgv_lang["filter_address"]			= "Vis adresser som inneholder:";
$pgv_lang["address_list"]			= "Adresseliste";
$pgv_lang["autocomplete"]			= "Auto-fullfør";
$pgv_lang["undo_all_confirm"]		= "Er du sikker på at du ønker å angre alle endringer for denne slektsbasen?";
$pgv_lang["undo_all"]				= "Angre alle endinger";
$pgv_lang["index_edit_advice"]		= "Merk navnet til en ramme og klikk på en av pilene for å flytte rammen i ønket retning.";
$pgv_lang["importing_dates"]		= "Importerer dato-data";
$pgv_lang["changelog"]				= "Endringer i versjon #VERSION#";
$pgv_lang["view_changelog"]			= "Vis filen changelog.txt";
$pgv_lang["html_block_descr"]		= "Dette er en enkel ramme som du kan legge på siden din for å vise en beskjed (i HTML-format).";
$pgv_lang["html_block_sample_part1"]	= "<p class=\"blockhc\"><b>Sett inn tittelen din her</b></p><br /><p>Klikk på knappen for oppsett";
$pgv_lang["html_block_sample_part2"]	= "for å endre det som skal stå her.</p>";
$pgv_lang["html_block_name"]		= "Rammen Enkle beskjeder (HTML)";
$pgv_lang["htmlplus_block_name"]	= "Avansert HTML";
$pgv_lang["htmlplus_block_descr"]	= "Dette er en HTML-ramme som du kan plassere på siden din for å vise ulike beskjeder.  Du kan legge inn referanser til opplysninger i slektsbasen din inn i HTML-teksten.";
$pgv_lang["htmlplus_block_templates"] = "Maler";
$pgv_lang["htmlplus_block_content"] = "Innhold";
$pgv_lang["htmlplus_block_narrative"] = "Fortellende stil (bare engelsk)";
$pgv_lang["num_to_show"]			= "Antall deler som skal vises";
$pgv_lang["days_to_show"]			= "Antall dager som skal vises";
$pgv_lang["before_or_after"]		= "Plasser antall før eller etter navn?";
$pgv_lang["before"]					= "før";
$pgv_lang["after"]					= "etter";
$pgv_lang["config_block"]			= "Endre oppsett for rammen";
$pgv_lang["pls_note12"]				= "Bruk dette feltet for å beskrive hvorfor du ber om å få en konto på dette nettstedet og hvordan du eventuelt er knyttet til noen i slektsbasen.";
$pgv_lang["enter_comments"]			= "Vennligst angi slektskapet ditt til noen i slektsbasen i feltet Kommentarer.";
$pgv_lang["comments"]				= "Kommentarer";
$pgv_lang["none"]					= "Ingen";
$pgv_lang["child-family"]			= "Foreldre og søsken";
$pgv_lang["spouse-family"]			= "Ektefelle og barn";
$pgv_lang["direct-ancestors"]		= "Forfedre i direkte linje";
$pgv_lang["ancestors"]				= "Forfedre i direkte linje og deres familier";
$pgv_lang["descendants"]			= "Etterkommere";
$pgv_lang["choose_relatives"]		= "Velg slektninger";
$pgv_lang["relatives_report"]		= "Slektninger";
$pgv_lang["total_living"]			= "Antall levende";
$pgv_lang["total_dead"]				= "Antall døde";
$pgv_lang["total_not_born"]			= "Antall ikke født enda";
$pgv_lang["download_zipped"]		= "Laste ned (download) slektsfilen (GEDCOM-format) komprimert i en ZIP-fil?";
$pgv_lang["remove_custom_tags"]		= "Fjerne fakta-koder laget av phpGedView?";
$pgv_lang["cookie_login_help"]		= "Nettstedet ser at du har logget deg inn her tidligere.  Dette gjør at du nå har tilgang til privat informasjon og andre bruker-relaterte funksjoner. <br/>- Men for å kunne endre eller adiministere nettstedet, så må du av hensyn til sikkerhet logge deg inn på nytt.";
$pgv_lang["remember_me"]			= "Huske meg?";
$pgv_lang["add_unlinked_person"]	= "Legg til en person som ikke er knyttet til en familie";
$pgv_lang["add_unlinked_source"]	= "Legg til en kilde som ikke er knyttet til noen";
$pgv_lang["fams_with_surname"]		= "Familier med etternavnet #surname#";
$pgv_lang["support_contact"]		= "Teknisk hjelp";
$pgv_lang["genealogy_contact"]		= "Slektsspørsmål";
$pgv_lang["continue_import"]		= "Fortsett å importere steder";
$pgv_lang["importing_places"]		= "Importerer steder";
$pgv_lang["common_upload_errors"]	= "Denne feilen skyldes sannsynligvis at filen som du prøvde å hente (upload) var for stor i forhold til grenser satt av verten til serveren.  Standard grense i PHP er 2MB.  Du kan prøve å kontakte eierene av serveren for å få dem til å heve denne grensen som er er angitt i filen php.ini, eller så kan du laste opp (upload) filen ved hjelp av FTP.  Bruk siden <a href=\"uploadgedcom.php?action=add_form\"><b>Legg til slektsfil</b></a> for å legge til en slektsfil som du har lastet opp ved hjelp av FTP.";
$pgv_lang["total_memory_usage"]		= "Totalt bruk av minne:";
$pgv_lang["mothers_family_with"]	= "Familien til mor til ";
$pgv_lang["fathers_family_with"]	= "Familien til far til ";
$pgv_lang["halfsibling"]			= "Halvsøken";
$pgv_lang["halfbrother"]			= "Halvbror";
$pgv_lang["halfsister"]				= "Halvsøster";
$pgv_lang["family_timeline"]		= "Vis familie på en tidslinje";
$pgv_lang["children_timeline"]		= "Vis barn på en tidslinje";
$pgv_lang["other"]					= "Annet";
$pgv_lang["sort_by_marriage"]		= "Sortert på dato for ekteskap";
$pgv_lang["reorder_families"]		= "Vis familier i annen rekkefølge";
$pgv_lang["indis_with_surname"]		= "Personer med etternavnet #surname#";
$pgv_lang["first_letter_fname"]		= "På grunn av antall - velg i indeks for fornavn:";
$pgv_lang["import_marr_names"]		= "Importere Navn som gift";
$pgv_lang["marr_name_import_instr"]	= "Du må BARE klikk på knappen under dersom du ønsker at PhpGedView skal kopiere etternavnet til ektemannen som Navn som gift for ektefellen i denne slektsbasen.  Dette vil gi deg mulighet til å søke og vise hustruer med etternavnet som gift.";
$pgv_lang["calc_marr_names"]		= "Kopierer ektemennenes navn";
$pgv_lang["total_names"]			= "Antall navn";

$pgv_lang["top10_pageviews_nohits"]	= "Det er ingen treff for denne siden.";
$pgv_lang["top10_pageviews_msg"]	= "Telleren må aktiveres i konfigurasjonen for slektsfilen for at denne rammen skal virke.";
$pgv_lang["review_changes_descr"]	= "Rammen Endringer på vent, vil gi brukere med rett til å endre opplysninger online, en mulighet til å se en endringsliste før disse er blitt godkjent.  Disse endringen kan enten bli godkjent eller forkastet.<br /><br />Dersom denne rammen er aktiv, vil brukere med rettighet til å godkjenne motta en e-post daglig som en påminning om å sjekke endringer.";
$pgv_lang["review_changes_block"]	= "Rammen Endringer på vent";
$pgv_lang["review_changes_email"]	= "Sende påminnelse med epost?";
$pgv_lang["review_changes_email_freq"]	= "Hvor ofte skal påminnelser sendes (dager)";
$pgv_lang["review_changes_subject"]	= "PhpGedView - Vis endringer";
$pgv_lang["review_changes_body"]	= "Endringer i slektsbasen er blitt gjort online.  Disse endringene må sjekkes og eventuelt godkjennes før de kan vises for alle brukere.  Vennligst bruk adressen (URL) under for å gå til PhpGedView på nettet for å se endringene (du må oppgi brukernavn og passord).";
$pgv_lang["show_spouses"]			= "Vis ektefeller";
$pgv_lang["quick_update_title"] 	= "Hurtig-oppdatering";
$pgv_lang["quick_update_instructions"] = "Denne siden gir deg mulighet til å foreta en hurtig-oppdatering av informasjon til en person.  Du trenger bare å fylle ut informasjon som er ny eller som skal endres.  Etter at endringene har blitt sendt, må de sjekks av en bruker med rett til å godkjenne før de blir vist for andre brukere.";
$pgv_lang["update_name"] 			= "Oppdater navn";
$pgv_lang["update_fact"] 			= "Oppdater en faktaopplysning";
$pgv_lang["update_fact_restricted"] = "Oppdatering av denne er begrenset:";
$pgv_lang["update_photo"] 			= "Oppdater bilde";
$pgv_lang["photo_replace"] 			= "Ønsker du å erstatte et eldre bilde med dette?";
$pgv_lang["select_fact"] 			= "Velg en faktaopplysning...";
$pgv_lang["update_address"] 		= "Oppdater adresse";
$pgv_lang["add_new_chil"] 			= "Legg til et nytt barn";
$pgv_lang["top10_pageviews_descr"]	= "Denne rammen vil vise de 10 mest viste personen/familiene.  Denne rammen krever at telleren er aktivert i innstillingene for konfigurasjonen for slektsfilen.";
$pgv_lang["top10_pageviews"]		= "Vist flest ganger";
$pgv_lang["top10_pageviews_block"]	= "Rammen Vist flest ganger";
$pgv_lang["user_default_tab"]		= "Arkfanen som skal vises som standard på faktasiden til personer";
$pgv_lang["stepfamily"]				= "Ste-familie";
$pgv_lang["stepdad"]				= "Stefar";
$pgv_lang["stepmom"]				= "Stemor";
$pgv_lang["stepsister"]				= "Stesøster";
$pgv_lang["stepbrother"]			= "Stebror";
$pgv_lang["max_upload_size"]		= "Maks størrelse for opplasting: ";
$pgv_lang["edit_fam"]				= "Endre familie";
$pgv_lang["fams_charts"]			= "Valg for denne familien";
$pgv_lang["sort_by_birth"]			= "Sorter på fødselsdato";
$pgv_lang["reorder_children"]		= "Vis barn i annen rekkefølge";
$pgv_lang["add_from_clipboard"]		= "Legg til fra klippebordet: ";
$pgv_lang["record_copied"]			= "Data er kopiert til klippebordet";
$pgv_lang["copy"]					= "Kopier";
$pgv_lang["cut"]					= "Klipp ut";
$pgv_lang["indis_charts"]			= "Valg for denne personen";
$pgv_lang["edit_indi"] 				= "Endre person";
$pgv_lang["locked"]					= "Ikke gjør endringer";
$pgv_lang["privacy"]				= "Personvern";
$pgv_lang["number_sign"]			= "#";

//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]					= "<i>?</i>";
$pgv_lang["qm_ah"]				= "<i>?</i>";
$pgv_lang["page_help"]			= "Hjelp";
$pgv_lang["help_for_this_page"]	= "Hjelp for denne siden";
$pgv_lang["help_contents"]		= "Emner i Hjelp";
$pgv_lang["show_context_help"]	= "Vis Hjelp <b><i>?</i></b> til tekst";
$pgv_lang["hide_context_help"]	= "Skjul Hjelp <b><i>?</i></b> til tekst";
$pgv_lang["sorry"]				= "<b>Beklager, men vi er ikke ferdig med hjelpeteksten for denne siden eller delen enda...</b>";
$pgv_lang["help_not_exist"]		= "<b>Hjelpeteksten for denne siden eller delen er ikke lagt inn enda</b>";
$pgv_lang["var_not_exist"]		= "<span style=font-weight: bold>Kan ikke finne noen hjelpetekst.<br />Vennligst gi oss en melding om dette på <a href=\"http://sourceforge.net/tracker/?group_id=55456&atid=477079\" target=\"_blank\" />internettsiden</a> vår.</span>";
$pgv_lang["resolution"]			= "Skjermoppløsning";
$pgv_lang["menu"]				= "Meny";
$pgv_lang["header"]				= "Toppfelt";
$pgv_lang["imageview"]			= "Bildeframviser";

//-- CONFIG FILE MESSAGES
$pgv_lang["login_head"]			= "PhpGedView innlogging for brukere";
$pgv_lang["error_title"]		= "FEIL: Kan ikke åpne slektsfilen";
$pgv_lang["error_header"] 		= "Slektsfilen [#GEDCOM#], finnes ikke på det angitte stedet.";
$pgv_lang["error_header_write"]	= "Slektsfilen [#GEDCOM#], er ikke skrivbar. Sjekk attributter og tilgangsrettigheter.";
$pgv_lang["for_support"]		= "For teknisk hjelp og informasjon, kontakt";
$pgv_lang["for_contact"]		= "For hjelp med slektsspørsmål, kontakt";
$pgv_lang["for_all_contact"]	= "For teknisk hjelp og slektsspørsmål, kontakt";
$pgv_lang["build_title"]		= "Bygger opp indeksfiler";
$pgv_lang["build_error"]		= "Slektsfil har blitt oppdatert.";
$pgv_lang["please_wait"]		= "Vent litt - Bygger opp Indeksfilene på nytt.";
$pgv_lang["choose_gedcom"]		= "Velg en slektsfil";
$pgv_lang["username"]			= "Brukernavn";
$pgv_lang["invalid_username"]	= "Brukernavnet inneholder ugyldige tegn";
$pgv_lang["firstname"]			= "Fornavn";
$pgv_lang["lastname"]			= "Etternavn";
$pgv_lang["password"]			= "Passord";
$pgv_lang["confirm"]			= "Bekreft passord";
$pgv_lang["user_contact_method"]	= "Ønsket kontaktmetode";
$pgv_lang["login_aut"]			= "Endre bruker";
$pgv_lang["login"]				= "Logg inn";
$pgv_lang["logout"]				= "Logg ut";
$pgv_lang["admin"]				= "Admin";
$pgv_lang["logged_in_as"]		= "Logget inn som ";
$pgv_lang["my_pedigree"]		= "Mitt anetre";
$pgv_lang["my_indi"]			= "Meg selv";
$pgv_lang["yes"]				= "Ja";
$pgv_lang["no"]					= "Nei";
$pgv_lang["add_gedcom"]			= "Legg til en slektsfil";
$pgv_lang["change_theme"]		= "Bytt stil";
$pgv_lang["gedcom_downloadable"]	= "<br />Besøkende på nettstedet ditt kan laste ned (download) denne slektsfilen!<br />Les mer om dette i filen <a href=\"".(file_exists('readme-norsk.txt')?"readme-norsk.txt":"readme.txt")."\">readme".(file_exists('readme-norsk.txt')?"-norsk":"").".txt</a> i avsnittet 12. SIKKERHET / PERSONVERN<br />for å finne en løsning på dette.";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]		= "Anetre";
$pgv_lang["gen_ped_chart"]		= "Anetre - #PEDIGREE_GENERATIONS# slektsledd";
$pgv_lang["generations"]		= "Slektsledd&nbsp;";
$pgv_lang["view"]				= "Vis";
$pgv_lang["fam_spouse"]			= "Familie med partner";
$pgv_lang["root_person"]		= "ID til startperson&nbsp;";
$pgv_lang["hide_details"]		= "Skjul detaljer";
$pgv_lang["show_details"]		= "Vis detaljer";
$pgv_lang["person_links"]		= "Linker til diagram, familie(r), og nære slektninger til denne personen. - Klikk her for å vise denne personen som startperson i diagrammet.";
$pgv_lang["zoom_box"]			= "Zoom denne boksen inn/ut";
$pgv_lang["orientation"]		= "Retning";
$pgv_lang["portrait"]			= "Stående tre";
$pgv_lang["landscape"]			= "Liggende tre";
$pgv_lang["start_at_parents"]	= "Start med foreldrene";
$pgv_lang["charts"]				= "Diagram";
$pgv_lang["lists"]				= "Lister";
$pgv_lang["welcome_page"]		= "Hovedside";
$pgv_lang["max_generation"]		= "Du kan vise maks #PEDIGREE_GENERATIONS# slektsledd!";
$pgv_lang["min_generation"]		= "Du må vise minst 3 slektsledd!";
$pgv_lang["box_width"]			= "Boksbredde";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]	= "Kan ikke finne familien med id ";
$pgv_lang["unable_to_find_indi"]	= "Kan ikke finne personen med id ";
$pgv_lang["unable_to_find_record"]	= "Kan ikke finne oppføringen med id ";
$pgv_lang["unable_to_find_source"]	= "Kan ikke finne kilden med id ";
$pgv_lang["unable_to_find_repo"]	= "Kan ikke finne oppbevaringssted med id ";
$pgv_lang["repo_name"]			= "Navn på oppbevaringssted:";
$pgv_lang["address"]			= "Adresse:";
$pgv_lang["phone"]				= "Tlf:";
$pgv_lang["source_name"]		= "Kildenavn:";
$pgv_lang["title"]				= "Tittel:";
$pgv_lang["author"]				= "Forfatter:";
$pgv_lang["publication"]		= "Publikasjon:";
$pgv_lang["call_number"]		= "Arkivnr./ISBN/ISSN:";
$pgv_lang["living"]				= "Lever";
$pgv_lang["private"]			= "Privat";
$pgv_lang["birth"]				= "Født:";
$pgv_lang["death"]				= "Død:";
$pgv_lang["descend_chart"]		= "Etterkommere";
$pgv_lang["individual_list"]	= "Personer";
$pgv_lang["family_list"]		= "Familier";
$pgv_lang["source_list"]		= "Kilder";
$pgv_lang["place_list"]			= "Stedsnavn";
$pgv_lang["place_list_aft"] 	= "Stedsnavn etter";
$pgv_lang["media_list"]			= "Bilder / medier";
$pgv_lang["search"]				= "Søk";
$pgv_lang["clippings_cart"]		= "Utklippsmappe";
$pgv_lang["print_preview"]		= "Utskrifts-vennlig utgave";
$pgv_lang["cancel_preview"]		= "Tilbake til vanlig visning";
$pgv_lang["change_lang"]		= "Velg språk (Language)";
$pgv_lang["print"]				= "Skriv ut";
$pgv_lang["total_queries"]		= "Antall søk i databasen:";
$pgv_lang["total_privacy_checks"]	= " - Antall kontroller av personvern: ";
$pgv_lang["back"]				= "Tilbake";
$pgv_lang["privacy_list_indi_error"]	= "Av hensyn til personvern, er en eller flere personer skjult.";
$pgv_lang["privacy_list_fam_error"]	= "Av hensyn til personvern, er en eller flere familier skjult.";

//-- INDIVDUAL FILE MESSAGES
$pgv_lang["aka"]				= "Også kjent som";
$pgv_lang["male"]				= "Mann";
$pgv_lang["female"]				= "Kvinne";
$pgv_lang["temple"]				= "Mormoner-tempel";
$pgv_lang["temple_code"]		= "Mormoner-tempel-kode:";
$pgv_lang["status"]				= "Status";
$pgv_lang["source"]				= "Kilde";
$pgv_lang["citation"]			= "Henvisning:";
$pgv_lang["text"]				= "Kildetekst:";
$pgv_lang["note"]				= "Note";
$pgv_lang["NN"]					= "Ukjent";
$pgv_lang["PN"]					= "(<i>ukjent</i>)";
$pgv_lang["unrecognized_code"]	= "Ukjent kode i slektsfilen";
$pgv_lang["unrecognized_code_msg"]	= "Dette er en feil som vi ønsker å rette på. Vennligst rapporter denne feilen til";
$pgv_lang["indi_info"]			= "Person-opplysninger";
$pgv_lang["pedigree_chart"]		= "Anetre";
$pgv_lang["individual"]			= "Person";
$pgv_lang["family"]				= "Familie";
$pgv_lang["family_with"]		= "Familie med";
$pgv_lang["as_spouse"]			= "Familie med ektefelle/partner";
$pgv_lang["as_child"]			= "Familie med foreldre";
$pgv_lang["view_gedcom"]		= "Vis opplysningene i slektsfilen";
$pgv_lang["add_to_cart"]		= "Legg i utklippsmappen";
$pgv_lang["still_living_error"]	= "Personen lever fremdeles eller har ikke noen datoer for fødsel eller dødsfall. Alle opplysninger om levende personer er skjult for offentligheten.<br />For ytterligere informasjon kontakt";
$pgv_lang["privacy_error"]		= "Opplysninger om denne personen er privat.<br />";
$pgv_lang["more_information"]	= "For mer informasjon, kontakt";
$pgv_lang["name"]				= "Navn";
$pgv_lang["given_name"]			= "Fornavn:";
$pgv_lang["surname"]			= "Etternavn:";
$pgv_lang["suffix"]				= "Suffiks:";
$pgv_lang["object_note"]		= "Note for objekt:";
$pgv_lang["sex"]				= "Kjønn";
$pgv_lang["personal_facts"]		= "Fakta og detaljer om personen";
$pgv_lang["type"]				= "Type";
$pgv_lang["date"]				= "Dato";
$pgv_lang["place_description"]	= "Sted / Beskrivelse";
$pgv_lang["parents"] 			= "Foreldre:";
$pgv_lang["siblings"] 			= "Søsken";
$pgv_lang["father"] 			= "Far";
$pgv_lang["mother"] 			= "Mor";
$pgv_lang["parent"] 			= "En av foreldrene";
$pgv_lang["relatives"]			= "Nære slektninger";
$pgv_lang["relatives_events"]	= "Hendelser til nære slektninger";
$pgv_lang["child"]				= "Barn";
$pgv_lang["spouse"]				= "Ektefelle/partner";
$pgv_lang["surnames"]			= "Etternavn";
$pgv_lang["adopted"]			= "Adoptert";
$pgv_lang["foster"]				= "Fosterbarn";
$pgv_lang["sealing"]			= "Kobling";
$pgv_lang["challenged"]			= "Innsigelser";
$pgv_lang["disproved"]			= "Motbevist";
$pgv_lang["infant"]				= "Spedbarn";
$pgv_lang["stillborn"]			= "Ufødt";
$pgv_lang["deceased"]			= "Døde";
$pgv_lang["link_as_child"]		= "Knytt denne personen til en eksisterende familie som et barn";
$pgv_lang["link_as_wife"]		= "Knytt denne personen til en eksisterende familie som en hustru";
$pgv_lang["link_as_husband"]	= "Knytt denne personen til en eksisterende familie som en ektemann";
$pgv_lang["no_tab1"]			= "Det er ikke noen fakta / opplysninger om denne personen.";
$pgv_lang["no_tab2"]			= "Det er ikke noen noter for denne personen.";
$pgv_lang["no_tab3"]			= "Det er ikke noen kilder knyttet til denne personen.";
$pgv_lang["no_tab4"]			= "Det er ikke noen bilder eller andre medier knyttet til denne personen.";
$pgv_lang["no_tab5"]			= "Det er ikke noen nære slektninger knyttet til denne personen.";
$pgv_lang["no_tab6"]			= "Det er ikke noen forsker-logg knyttet til denne personen.";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]		= "Familie-opplysninger";
$pgv_lang["family_group_info"]	= "Familie-opplysninger";
$pgv_lang["husband"]			= "Ektemann";
$pgv_lang["wife"]				= "Hustru";
$pgv_lang["marriage"]			= "Bryllup:";
$pgv_lang["lds_sealing"]		= "Mormoner kobling:";
$pgv_lang["marriage_license"]	= "Ekteskapsattest:";
$pgv_lang["media_object"]		= "Bilder eller andre medier:";
$pgv_lang["children"]			= "Barn";
$pgv_lang["no_children"]		= "<i>Ingen registerte barn</i>";
$pgv_lang["childless_family"]	= "Denne familien fikk aldri noen barn";
$pgv_lang["number_children"]	= "Antall barn: ";
$pgv_lang["parents_timeline"]	= "Vis partnere på en tidslinje";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]			= "Utklippsmappe";
$pgv_lang["clip_explaination"]	= "Utklippsmappen gir deg muligheten til å \"klippe ut\" deler av dette slektstreet og samle utklippene i en ny slektsfil (GEDCOM-format), som kan lastes ned (download).<br /><br />";
$pgv_lang["item_with_id"]		= "Objekt med id";
$pgv_lang["error_already"]		= "finnes fra før i utklippsmappen.";
$pgv_lang["which_links"]		= "Merk av hvilke opplysninger du ønsker å kopiere fra den valgte familien.";
$pgv_lang["just_family"]		= "Bare familien alene.";
$pgv_lang["parents_and_family"]	= "Familien og foreldre.";
$pgv_lang["parents_and_child"]	= "Familien, foreldre og barn.";
$pgv_lang["parents_desc"]		= "Familien, foreldre og alle etterkommere.";
$pgv_lang["continue"]			= "Neste skritt...";
$pgv_lang["which_p_links"]		= "Merk av hvilke opplysninger du ønsker å kopiere fra den valgte personen.";
$pgv_lang["just_person"]		= "Bare personen alene.";
$pgv_lang["person_parents_sibs"]	= "Personen, foreldre og søsken.";
$pgv_lang["person_ancestors"]		= "Personen og slektninger i direkte linje.";
$pgv_lang["person_ancestor_fams"]	= "Personen og slektninger i direkte linje med deres familier.";
$pgv_lang["person_spouse"]		= "Personen, samt ektefelle/partner og barn.";
$pgv_lang["person_desc"]		= "Personen, ektefelle/partner, og alle etterkommere.";
$pgv_lang["unable_to_open"]		= "Kan ikke kopiere til utklippsmappen";
$pgv_lang["person_living"]		= "Denne personen lever fortsatt. Personlige data vises derfor ikke.";
$pgv_lang["person_private"]		= "Data for denne personen er private. Personlige data vises derfor ikke.";
$pgv_lang["family_private"]		= "Data for denne familien er private. Familiære data vises derfor ikke.";
$pgv_lang["download"]			= "Høyreklikk (control-click på Mac) på linken under og Velg \"Lagre som\" for å laste ned (download) filene.";
$pgv_lang["media_files"]		= "Bilder eller medie-filer som er knyttet til denne slektsbasen";
$pgv_lang["cart_is_empty"]		= "<b>Utklippsmappen din er tom!</b>";
$pgv_lang["id"]					= "ID";
$pgv_lang["name_description"]	= "Navn / beskrivelse";
$pgv_lang["remove"]				= "Fjern";
$pgv_lang["empty_cart"]			= "Fjern alt i mappen";
$pgv_lang["download_now"]		= "Laste ned (download) nå";
$pgv_lang["indi_downloaded_from"]	= "Denne personen er hentet fra:";
$pgv_lang["family_downloaded_from"]	= "Denne familien er hentet fra:";
$pgv_lang["source_downloaded_from"]	= "Denne kilden er hentet fra:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]		= " stedsnavn som inneholder";
$pgv_lang["top_level"]			= "Lands-/toppnivå";
$pgv_lang["form"]				= "Stedsnavn lagret som: ";
$pgv_lang["default_form"]		= "Grend/bydel, sted/by, kommune/sogn, fylke/region, land";
$pgv_lang["default_form_info"]	= "(Standard)";
$pgv_lang["gedcom_form_info"]	= "(GEDCOM-format)";
$pgv_lang["unknown"]			= "Ukjent";
$pgv_lang["individuals"]		= "Personer";
$pgv_lang["view_records_in_place"]	= " Vis alle personer / familier knyttet til stedet ";
$pgv_lang["place_list2"] 		= "Alle stedsnavn";
$pgv_lang["show_place_hierarchy"]	= "Vis stedsnavn etter nivå";
$pgv_lang["show_place_list"]	= "Vis alle stedsnavn";
$pgv_lang["total_unic_places"]	= "Antall unike steder";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["external_objects"]	= "Eksterne objekt";
$pgv_lang["multi_title"]		= "Bilder eller andre medier";
$pgv_lang["media_found"]		= " bilder / medier funnet";
$pgv_lang["view_person"]		= "Vis person";
$pgv_lang["view_family"]		= "Vis familie";
$pgv_lang["view_source"]		= "Vis kilde";
$pgv_lang["view_object"]		= "Vis objekt";
$pgv_lang["prev"]				= "< Forrige";
$pgv_lang["next"]				= "Neste >";
$pgv_lang["file_not_found"]		= "Fant ikke filen.";
$pgv_lang["medialist_show"]		= "Vis ";
$pgv_lang["per_page"]			= " bilder / medier pr. side";
$pgv_lang["delete_directory"]	= "Slett mappe";
$pgv_lang["remove_object"]			= "Fjern objekt";
$pgv_lang["confirm_remove_object"]	= "Er du sikker på at du vil fjerne dette objektet fra slektsbasen?";
$pgv_lang["remove_links"]			= "Fjern koblinger";
$pgv_lang["confirm_remove_links"]	= "Er du sikker på at du vil fjerne alle koblinger til dette objektet?";
$pgv_lang["delete_file"]		= "Slett fil";
$pgv_lang["confirm_delete_file"]	= "Er du sikker på at du vil slette denne filen?";
$pgv_lang["multiple_gedcoms"]	= "Denne filen har en kobling til en annen slektsbase på dette nettstedet.  Filen kan derfor ikke slettes, flyttes eller gis nytt navn før disse koblingene er fjernet.";
$pgv_lang["external_file"]		= "Dette mediaobjektet finnes ikke som en fil på dette nettstedet.  Den kan derfor ikke slettes, flyttes eller gis nytt navn.";
$pgv_lang["directory_not_empty"]	= "Mappen er ikke tom.";
$pgv_lang["directory_not_exist"]	= "Mappen finnes ikke.";
$pgv_lang["media_not_deleted"]	= "Media-mappen ble ikke slettet.";
$pgv_lang["media_deleted"]		= "Media-mappen er nå slettet.";
$pgv_lang["thumbs_not_deleted"]	= "Mappen for minityrbildene ble ikke slettet.";
$pgv_lang["thumbs_deleted"]		= "Mappen for minityrbildene er nå slettet.";
$pgv_lang["delete_dir_success"]	= "Mappene for media og minityrbilder er nå slettet.";
$pgv_lang["current_dir"]		= "Nåværende mappe: ";
$pgv_lang["add_directory"]		= "Opprett mappe/katalog";
$pgv_lang["show_thumbnail"]		= "Vis miniatyrbilde(r)";
$pgv_lang["image_format"]		= "Bilde-filtype";
$pgv_lang["media_format"]		= "Media-filtype";
$pgv_lang["image_size"]			= "Bilde-størrelser";
$pgv_lang["media_file_size"]	= "Media-størrelse";
$pgv_lang["no_thumb_dir"]		= " mappen/katalogen for miniatyrbilde(r) finnes ikke og den kunne heller ikke opprettes.";
$pgv_lang["manage_media"]		= "Behandling av bilder";
$pgv_lang["gen_thumb"]			= "Lag miniatyrbilde(r)";
$pgv_lang["move_to"]			= "Flytt til";
$pgv_lang["folder_created"]		= "Opprettet mappe";
$pgv_lang["folder_no_create"]	= "Kan ikke opprette mappe";
$pgv_lang["security_no_create"]	= "Sikkerhetsadvarsel: Filen <b><i>index.php</i></b> finnes ikke i ";
$pgv_lang["security_not_exist"]	= "Sikkerhetsadvarsel: Klarte ikke å lage filen <b><i>index.php</i></b> i ";
$pgv_lang["illegal_chars"]		= "Ugyldige tegn i navn";
$pgv_lang["link_media"]			= "Kobling av Media";
$pgv_lang["to_person"]			= "Til person";
$pgv_lang["to_family"]			= "Til familie";
$pgv_lang["to_source"]			= "Til kilde";
$pgv_lang["media_id"]			= "Media ID";
$pgv_lang["invalid_id"]			= "Ukjent ID i denne slektsbasen.";
$pgv_lang["media_exists"]		= "Media-fil finnes fra før.";
$pgv_lang["media_thumb_exists"]	= "Media-miniatyrbilde finnes fra før.";
$pgv_lang["move_file_success"]	= "Media- og miniatyrbilde-filer er nå flyttet.";
$pgv_lang["media_folder_corrupt"]	= "Det er en feil med mediamappen.";
$pgv_lang["max_media_depth"]	= "Du kan bare bruke #MEDIA_DIRECTORY_LEVELS# mappe-nivåer";
$pgv_lang["upload_file"]		= "Hent (upload) fil fra datamaskinen din";
$pgv_lang["thumb_genned"]		= "Miniatyrbilde(r) er lagd automatisk.";
$pgv_lang["thumbgen_error"]		= "Klarte ikke å lage miniatyrbilde(r) for ";
$pgv_lang["generate_thumbnail"]	= "Lag miniatyrbilde(r) automatisk fra ";
$pgv_lang["auto_thumbnail"]		= "Automatisk miniatyrbilde";
$pgv_lang["no_upload"]			= "Klarte ikke å laste opp (upload) mediafiler fordi multi-media-filer ikke er aktivert eller fordi mediamappen/-katalogen ikke har skriverettighet.";
$pgv_lang["upload"]				= "Hent (upload)";
$pgv_lang["upload_media"]		= "Hent (upload) bilde- / mediefiler";
$pgv_lang["folder"]		 		= "Mappe";
$pgv_lang["media_file"]			= "Medie-fil";
$pgv_lang["thumbnail"]			= "Miniatyrbilde(r)";
$pgv_lang["upload_successful"]	= "Overføring (upload) er utført";
$pgv_lang["media_file_deleted"]		= "Media-fil er nå slettet.";
$pgv_lang["media_file_not_deleted"]	= "Klarte ikke å slette mediafil.";
$pgv_lang["media_file_not_moved"]	= "Klarte ikke å flytte mediafil.";
$pgv_lang["media_file_not_renamed"]	= "Klarte ikke å flytte eller å gi mediafilen et nytt navn.";
$pgv_lang["thumbnail_deleted"]		= "Miniatyrbilde-fil er nå slettet.";
$pgv_lang["thumbnail_not_deleted"]	= "Klarte ikke å slette miniatyrbilde-fil.";
$pgv_lang["media_record_deleted"]	= "Media-oppføringen #xref# og tilhørende koblinger er nå fjernet fra slektsbasen.";
$pgv_lang["media_record_not_deleted"]	= "Klarte ikke å fjerne media-oppføringen #xref# og tilhørende koblinger fra slektsbasen.";
$pgv_lang["record_updated"]			= "Oppføringen #pid# er nå oppdatert.";
$pgv_lang["record_not_updated"]		= "Klarte ikke å oppdatere oppføringen #pid#.";
$pgv_lang["record_removed"]			= "Oppføringen #xref# er nå fjernet fra slektsfilen.";
$pgv_lang["record_not_removed"]		= "Klarte ikke å fjerne oppføringen #xref# i slektsfilen.";
$pgv_lang["record_added"]			= "Oppføringen #xref# er nå lagt til slektsfilen.";
$pgv_lang["record_not_added"]		= "Klarte ikke å legge til oppføringen #xref# til slektsfilen.";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]		= "Søke i slektsfilen";
$pgv_lang["enter_terms"]		= "Skriv inn søkeord";
$pgv_lang["soundex_search"]		= "Søk slik du <i>tror</i> navnet er skrevet";
$pgv_lang["sources"]			= "Kilder";
$pgv_lang["firstname_search"]	= "Fornavn";
$pgv_lang["lastname_search"]	= "Etternavn";
$pgv_lang["search_place"]		= "Stedsnavn";
$pgv_lang["search_year"]		= "År";
$pgv_lang["no_results"]			= "Fant ingen...";
$pgv_lang["invalid_search_input"] 	= "Vennligst angi fornavn, etternavn eller stedsnavn \\n\\t i tillegg til år";
$pgv_lang["search_options"]			= "Valg for søket";
$pgv_lang["search_geds"]			= "Slektsbase det skal søkes i";
$pgv_lang["search_type"]			= "Søkemetode";
$pgv_lang["search_general"]			= "Vanlig søk";
$pgv_lang["search_soundex"]			= "Slik du tror det er skrevet";
$pgv_lang["search_inrecs"]			= "Søk etter";
$pgv_lang["search_fams"]			= "Familier";
$pgv_lang["search_indis"]			= "Personer";
$pgv_lang["search_sources"]			= "Kilder";
$pgv_lang["search_more_chars"]      = "Du må søke etter mer enn bare en bokstav";
$pgv_lang["search_soundextype"]		= "Metode";
$pgv_lang["search_russell"]			= "Russell";
$pgv_lang["search_DM"]				= "Daitch-Mokotoff";
$pgv_lang["search_prtnames"]		= "Vise";
$pgv_lang["search_prthit"]			= "Navn med antall treff";
$pgv_lang["search_prtall"]			= "Alle navn";
$pgv_lang["search_tagfilter"]		= "Begrensning";
$pgv_lang["search_tagfon"]			= "Utelat enkelte ikke-tilknyttede slektsdata";
$pgv_lang["search_tagfoff"]			= "Av";
$pgv_lang["associate"]				= "tilknyttede";
$pgv_lang["search_asso_label"]		= "Vise i tillegg";
$pgv_lang["search_asso_text"]		= "Beslektede personer / familier";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]		= " kilder funnet";
$pgv_lang["titles_found"]		= "Titler";
$pgv_lang["find_source"]		= "Finn kilde";

//-- REPOLIST FILE MESSAGES
$pgv_lang["repo_list"]				= "Oppbevaringssteder";
$pgv_lang["repos_found"]			= " oppbevaringssteder funnet";
$pgv_lang["find_repository"]		= "Finn oppbevaringssted";
$pgv_lang["total_repositories"]		= "Antall oppbevaringssteder";
$pgv_lang["repo_info"]				= "Informasjon om oppbevaringssted";
$pgv_lang["delete_repo"]			= "Slett oppbevaringssted";
$pgv_lang["other_repo_records"]		= "Kilder som er knyttet til dette oppbevaringsstedet:";
$pgv_lang["create_repository"]		= "Opprett nytt oppbevaringssted";
$pgv_lang["new_repo_created"]		= "Nytt oppbevaringssted er opprettet";
$pgv_lang["paste_rid_into_field"]	= "Lim inn følgende ID for oppbevaringsstedet til aktuelle koblings-feltet for ulike kilder: ";
$pgv_lang["confirm_delete_repo"]	= "Er du sikker på at du vil slette dette oppbevaringsstedet fra slektsbasen?";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]		= "Informasjon om kilde";
$pgv_lang["other_records"]		= "Navn som er knyttet til denne kilden:";
$pgv_lang["people"]				= "Personer";
$pgv_lang["families"]			= "Familier";
$pgv_lang["total_sources"]		= "Antall kilder";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]		= "Lager person- og familie-indeks";
$pgv_lang["building_index"]		= "Lager indekslister";
$pgv_lang["invalid_gedformat"]	= "Ugyldig GEDCOM 5.5 format";
$pgv_lang["importing_records"]	= "Importerer slektsdatene til slektsbasen";
$pgv_lang["detected_change"]	= "PhpGedView har oppdaget en endring i slektsfilen #GEDCOM#. Indeksfiler må bygges opp igjen før du kan fortsette.";
$pgv_lang["please_be_patient"]	= "Vennligst VENT...";
$pgv_lang["reading_file"]		= "Leser slektsfilen";
$pgv_lang["flushing"]			= "Henter ut data";
$pgv_lang["found_record"]		= "dataposter funnet";
$pgv_lang["exec_time"]			= "Utføringstid:";
$pgv_lang["time_limit"]			= "Tidsgrense:";
$pgv_lang["unable_to_create_index"]	= "<b>Indeksfil kan ikke opprettes.</b><br />Sørg for nødvendige skrive-tillatelser i PhpGedView mappen (index).<br />Skrivebeskyttelsen kan evt. etableres igjen når indeksfilene er opprettet.";
$pgv_lang["indi_complete"]		= "Oppdatering av personindeksfilen er ferdig.";
$pgv_lang["family_complete"]	= "Oppdatering av familieindeksfilen er ferdig.";
$pgv_lang["source_complete"]	= "Oppdatering av kildeindeksfilen er ferdig.";
$pgv_lang["tables_exist"]		= "PhpGedView tabeller finnes allerede i databasen";
$pgv_lang["you_may"]			= "Du kan:";
$pgv_lang["drop_tables"]		= "Fjerne eksisterende tabeller";
$pgv_lang["import_multiple"]	= "Importere og arbeide med flere slektsfiler";
$pgv_lang["explain_options"]	= "Hvis du velger å fjerne tabellene, vil alle data bli hentet fra denne slektsfilen.<br />Hvis du velger å importere og arbeide med flere slektsfiler samtidig, vil PhpGedView slette alle data som ble importert fra slektsfil(er) med samme navn. Dette valget gjør det mulig å legge flere slekts-datasett i samme tabell og lett skifte mellom dem.<br /><br /><b>NB! Systemet ser forskjell på store og små bokstaver i filnavn.</b>  Det betyr at <b>Test.GED</b> <u>ikke</u> er den samme filen som <b>test.ged</b>.";
$pgv_lang["path_to_gedcom"]		= "Angi stien til slektsfilen din:";
$pgv_lang["dataset_exists"]		= "Det er allerede importert en slektsfil i databasen med navnet ";
$pgv_lang["changes_present"]	= "Denne slektsbasen har endringer på vent.  Dersom du fortsetter denne importen, vil disse endringene bli lagt til databasen uten at du at du får godkjent dem først.  Du BØR se gjennom endringene før du fortsetter importen!";
$pgv_lang["empty_dataset"]		= "Vil du tømme den nåværende slektsbasen og legge data inn på nytt?";
$pgv_lang["index_complete"]		= "Indeksering ferdig.";
$pgv_lang["click_here_to_go_to_pedigree_tree"] = "Klikk her for å gå til slektstreet.";
$pgv_lang["updating_is_dead"]	= "Oppdaterer statusen \"Er død\" for personer";
$pgv_lang["import_complete"]	= "Import ferdig";
$pgv_lang["updating_family_names"]	= "Oppdaterer familienavn for FAM";
$pgv_lang["processed_for"]		= "Behandlet fil for ";
$pgv_lang["run_tools"]			= "Vil du utføre en av disse valgene før filen blir importert:";
$pgv_lang["addmedia"]			= "Legg til bilder / medier";
$pgv_lang["dateconvert"]		= "Dato konverterings-rutine";
$pgv_lang["xreftorin"]			= "Konvertere XREF-IDer til RIN-nummer";
$pgv_lang["tools_readme"]		= "Se verktøy/rutine-delen i <a href=\"readme.txt\" target=\"_blank\">readme.txt</a>-filen for mer informasjon.";
$pgv_lang["sec"]				= "sek.";
$pgv_lang["bytes_read"]			= "Bytes lest:";
$pgv_lang["created_remotelinks"]	= "Opprettet tabellen <i>Eksterne koblinger</i>.";
$pgv_lang["created_remotelinks_fail"] 	= "Klarte ikke å opprette tabellen <i>Eksterne koblinger</i>.";
$pgv_lang["created_indis"]		= "Opprettet tabellen <i>Personer</i>.";
$pgv_lang["created_indis_fail"]	= "Klarte ikke å opprette tabellen <i>Personer</i>!";
$pgv_lang["created_fams"]		= "Opprettet tabellen <i>Familier</i>.";
$pgv_lang["created_fams_fail"]	= "Klarte ikke å opprette tabellen <i>Familier</i>!";
$pgv_lang["created_sources"]	= "Opprettet tabellen <i>Kilder</i>.";
$pgv_lang["created_sources_fail"]	= "Klarte ikke å opprette tabellen <i>Kilder</i>!";
$pgv_lang["created_other"]		= "Opprettet tabellen <i>Annet</i>.";
$pgv_lang["created_other_fail"]	= "Klarte ikke å opprette tabellen <i>Annet</i>!";
$pgv_lang["created_places"]		= "Opprettet tabellen <i>Steder</i>.";
$pgv_lang["created_places_fail"]	= "Klarte ikke å opprette tabellen <i>Steder</i>!";
$pgv_lang["created_placelinks"] 	= "Opprettet tabellen <i>Stedskoblinger</i>.";
$pgv_lang["created_placelinks_fail"]	= "Klarte ikke å opprette tabellen <i>Stedskoblinger</i>.";
$pgv_lang["created_media_fail"]	= "Klarte ikke å opprette tabellen <i>Media</i>.";
$pgv_lang["created_media_mapping_fail"]	= "Klarte ikke å opprette tabellen <i>Media-mapper</i>.";
$pgv_lang["import_progress"]	= "Import utført...";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"]			= "Antall familier";
$pgv_lang["total_indis"]		= "Antall personer";
$pgv_lang["starts_with"]		= "Starter med:";
$pgv_lang["person_list"]		= "Personliste:";
$pgv_lang["paste_person"]		= "Legg inn person";
$pgv_lang["notes_sources_media"]	= "Noter, kilder, og media";
$pgv_lang["notes"]				= "Noter";
$pgv_lang["ssourcess"]			= "Kilder";
$pgv_lang["media"]				= "Bilder / medier";
$pgv_lang["name_contains"]		= "Navn inneholder:";
$pgv_lang["filter"]				= "Søk";
$pgv_lang["find_individual"]	= "Finn person";
$pgv_lang["find_familyid"]		= "Finn familie";
$pgv_lang["find_sourceid"]		= "Finn kilde";
$pgv_lang["find_specialchar"]	= "Finn spesielle bokstaver";
$pgv_lang["magnify"]			= "Forstørr";
$pgv_lang["skip_surnames"]		= "Vis utvidet liste".(isset($alpha)?" (".$alpha.")":"");
$pgv_lang["show_surnames"]		= "Vis kun etternavn";
$pgv_lang["all"]				= "ALLE";
$pgv_lang["hidden"]				= "Skjulte";
$pgv_lang["confidential"]		= "Fortrolig";
$pgv_lang["alpha_index"]		= "Alfabetisk indeks";
$pgv_lang["name_list"] 			= "Navneliste";
$pgv_lang["firstname_alpha_index"] 	= "Alfabetisk indeks for fornavn";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]				= "Alder";
$pgv_lang["days"]				= "dager";
$pgv_lang["months"]				= "måneder";
$pgv_lang["years"]				= "år";
$pgv_lang["day1"]				= "dag";
$pgv_lang["month1"]				= "måned";
$pgv_lang["year1"]				= "år";
$pgv_lang["timeline_title"]		= "PhpGedView tidslinje";
$pgv_lang["timeline_chart"]		= "Tidslinje";
$pgv_lang["remove_person"]		= "Fjern person";
$pgv_lang["show_age"]			= "Vis markør for alder";
$pgv_lang["add_another"]		= "Legg til en person på tidslinjen:<br />Person ID:";
$pgv_lang["find_id"]			= "Finn ID";
$pgv_lang["show"]				= "Vis";
$pgv_lang["year"]				= "År";
$pgv_lang["timeline_instructions"]	= "- <i><b>PS!</b>  Du kan flytte på boksene under ved hjelp av musa!</i> (I de nyeste nettleserne)";
$pgv_lang["zoom_out"]			= "Zoom ut";
$pgv_lang["zoom_in"]			= "Zoom inn";

//-- MONTH NAMES
$pgv_lang["jan"]				= "januar";
$pgv_lang["feb"]				= "februar";
$pgv_lang["mar"]				= "mars";
$pgv_lang["apr"]				= "april";
$pgv_lang["may"]				= "mai";
$pgv_lang["jun"]				= "juni";
$pgv_lang["jul"]				= "juli";
$pgv_lang["aug"]				= "august";
$pgv_lang["sep"]				= "september";
$pgv_lang["oct"]				= "oktober";
$pgv_lang["nov"]				= "november";
$pgv_lang["dec"]				= "desember";
$pgv_lang["abt"]				= "omkring";
$pgv_lang["aft"]				= "etter";
$pgv_lang["and"]				= "og";
$pgv_lang["bef"]				= "før";
$pgv_lang["bet"]				= "mellom";
$pgv_lang["cal"]				= "beregnet";
$pgv_lang["est"]				= "anslått";
$pgv_lang["from"]				= "fra";
$pgv_lang["int"]				= "tolket";
$pgv_lang["to"]					= "til";
$pgv_lang["cir"]				= "cirka";
$pgv_lang["apx"]				= "ca";

//-- Admin File Messages
$pgv_lang["select_an_option"]		= "Alternativer:";
$pgv_lang["readme_documentation"]	= "ReadMe-dokumentasjon (Engelsk)";
$pgv_lang["view_readme"]			= "Vis filen readme.txt";
$pgv_lang["configuration"]			= "Program-innstillinger";
$pgv_lang["rebuild_indexes"]		= "Lag indeksene på nytt";
$pgv_lang["user_admin"]				= "Brukere og rettigheter";
$pgv_lang["user_created"]			= "Bruker er opprettet.";
$pgv_lang["user_create_error"]		= "Bruker kan ikke opprettes. Gå tilbake og prøv på nytt.";
$pgv_lang["password_mismatch"]		= "De to passordene er forskjellige.";
$pgv_lang["enter_username"]			= "Du må oppgi et brukernavn.";
$pgv_lang["enter_fullname"]			= "Du må oppgi et fullt navn.";
$pgv_lang["enter_password"]			= "Du må oppgi et passord.";
$pgv_lang["confirm_password"]		= "Du må bekrefte passordet.";
$pgv_lang["update_user"]			= "Oppdater brukerkonto";
$pgv_lang["update_myaccount"]		= "Oppdater Min konto";
$pgv_lang["save"]					= "Lagre";
$pgv_lang["delete"]					= "Slett";
$pgv_lang["edit"]					= "Endre";
$pgv_lang["full_name"]				= "Fullt navn";
$pgv_lang["visibleonline"]			= "Vis andre at du er pålogget";
$pgv_lang["comment"]				= "Kommentar fra admin til bruker";
$pgv_lang["comment_exp"]			= "Advarsel fra admin om dato";
$pgv_lang["editaccount"]			= "Gi denne brukeren rettighet til å endre brukerkontoen sin";
$pgv_lang["admin_gedcom"]			= "Administrere";
$pgv_lang["confirm_user_delete"]	= "Er du sikker på at du vil slette brukeren";
$pgv_lang["create_user"]			= "Opprett bruker";
$pgv_lang["no_login"]				= "Bruker kan ikke godkjennes.";
$pgv_lang["basic_realm"]			= "PhpGedView autoriseringssystem";
$pgv_lang["basic_auth_failure"]		= "Du må angi et gyldig brukernavn og passord for få tilgang til denne delen";
$pgv_lang["basic_auth"]				= "Enkel autorisasjon";
$pgv_lang["digest_auth"]			= "Oversikt autorisasjon"; //not used in code yet
$pgv_lang["no_auth_needed"]			= "Ingen autorisasjon";
$pgv_lang["import_gedcom"]			= "Importer en slektsfil";
$pgv_lang["duplicate_username"]		= "NB!! Det finnes allerede et slikt brukernavn. Gå tilbake og velg et annet brukernavn.";
$pgv_lang["gedcomid"]				= "Bruker ID<br />i slektsbasen";
$pgv_lang["enter_gedcomid"]			= "Du må oppgi en ID.";
$pgv_lang["user_info"]				= "Min brukerinformasjon";
$pgv_lang["rootid"]					= "ID til startperson<br />i slektsbasen";
$pgv_lang["download_gedcom"]		= "Laste ned (download) slektsfil (GEDCOM)";
$pgv_lang["upload_gedcom"]			= "Hente (upload) slektsfil(er) (GEDCOM)";
$pgv_lang["add_new_gedcom"]			= "Lag en ny slektsfil";
$pgv_lang["gedcom_file"]			= "Slektsfil:";
$pgv_lang["enter_filename"]			= "Du må oppgi et navn for slektsfilen.";
$pgv_lang["file_not_exists"]		= "Oppgitt filnavn finnes ikke!";
$pgv_lang["file_not_present"]		= "Fant ikke filen.";
$pgv_lang["file_exists"]			= "Det finnes allerede en slektsfil med dette navnet. Velg et annet navn, eller slett den gamle filen.";
$pgv_lang["new_gedcom_title"]		= "Slektsbase fra [#GEDCOMFILE#]";
$pgv_lang["upload_error"]			= "Det oppstod en FEIL under henting (upload) av filen din.";
$pgv_lang["upload_media_help"]		= "~#pgv_lang[upload_media]#~<br /><br />Velg en fil fra din lokale PC <b>#MEDIA_DIRECTORY#</b> eller i en av undermappene til denne.<br /><br />Navn på mapper du angir vil bli lagt til #MEDIA_DIRECTORY#. (Eksempel: #MEDIA_DIRECTORY#minfamilie)<br />Dersom mappen for miniatyrbilder ikke finnes fra før, vil denne bli lagd automatisk.";
$pgv_lang["upload_gedcom_help"]		= "Velg en slektsfil (GEDCOM) eller ZIP-fil fra din lokale PC for å hente (upload) til serveren din. Filen(e) vil bli lagret i mappen <b>#INDEX_DIRECTORY#</b>.<br /><br />Dersom du henter en ZIP-fil, bør den bare inneholde en slektsfil. Slektsfilen vil automatisk bli brukt for å importere datene til slektsbasen.<br /><br />";
$pgv_lang["add_gedcom_instructions"]	= "Tast inn et filnavn for denne nye slektsfilen (GEDCOM). Den blir opprettet i mappen index:";
$pgv_lang["file_success"]			= "Filen er lastet opp";
$pgv_lang["file_too_big"]			= "Opplastet fil overskrider tillatt størrelse";
$pgv_lang["file_partial"]			= "Filen ble bare delvis lastet opp, forsøk på nytt";
$pgv_lang["file_missing"]			= "Det ble ikke mottatt noen fil. Hent filen på nytt.";
$pgv_lang["manage_gedcoms"]			= "Slektsfil(er) og personvern";
$pgv_lang["research_assistant"]		= "Forsknings-assistent";
$pgv_lang["administration"]			= "Administrasjon";
$pgv_lang["ansi_to_utf8"]			= "Konvertere fra ANSI til UTF-8 tegnsett?";
$pgv_lang["utf8_to_ansi"]			= "Konvertere fra UTF-8 til ANSI tegnsett?";
$pgv_lang["visitor"]				= "Besøkende";
$pgv_lang["user"]					= "Godkjent bruker";
$pgv_lang["gedadmin"]				= "Administrator av slektsfilen";
$pgv_lang["siteadmin"]				= "Administrator av nettstedet";
$pgv_lang["apply_privacy"]			= "Legge til nye innstillinger for personvern?";
$pgv_lang["choose_priv"]			= "Velg nivå for personvern:";
$pgv_lang["user_manual"]			= "Brukerdokumentasjon";
$pgv_lang["upgrade"]				= "Oppgrader PhpGedView";
$pgv_lang["view_logs"]				= "Vis logg-fil ";
$pgv_lang["logfile_content"]		= "Innhold i logg-filen";
$pgv_lang["step1"]					= "Del 1 av 4:";
$pgv_lang["step2"]					= "Del 2 av 4:";
$pgv_lang["step3"]					= "Del 3 av 4:";
$pgv_lang["step4"]					= "Del 4 av 4:";
$pgv_lang["validate_gedcom"]		= "Sjekker kvaliteten til slektsfilen";
$pgv_lang["img_admin_settings"]		= "Endre innstillingene for bilde-behandling";
$pgv_lang["download_note"]			= "NB! Store slektsfiler (GEDCOM) kan ta lang tid å forberede før en nedlastning (download).  Dersom PHP melder at tiden har gått ut før nedlastningen er ferdig, så kan det være at du ikke har mottatt hele filen.  For å sjekke om den nedlastede slektsfilen er korrekt, kan se om filen inneholder linjen <b>0 TRLR</b> på slutten.  Som en tommelfinger-regel vil tiden det tar å laste ned slektsfilen, være like lang som det tok å importere den (avhengig av hastigheten på internett-tilkoblingen din).";
$pgv_lang["pgv_registry"]			= "Vis andre nettsteder som bruker PhpGedView";
$pgv_lang["verify_upload_instructions"]	= "Dersom du velger å fortsette, vil den eksisterende slektsfilen bli erstattet med den filen som du har valgt å laste opp. Den nye filen vil deretter bli importert inn i PhpGedView.<br />Velger du å avbryte, vil den eksisterende slektsfilen forbli uforandret.";
$pgv_lang["cancel_upload"]			= "Avbryt opplastingen (upload)";
$pgv_lang["add_media_records"]		= "Legg til nye Media-oppføringer";
$pgv_lang["manage_media_files"]		= "Behandle Media-filer";
$pgv_lang["link_media_records"]		= "Knytt media-objekt til personer";
$pgv_lang["add_media_button"]		= "Legg til Media";
$pgv_lang["media_linked"]			= "Dette media-objektet er knyttet til:";
$pgv_lang["media_not_linked"]		= "Dette media-objektet er ikke knyttet til noe(n) i slektsfilen.";
$pgv_lang["phpinfo"]				= "PHPInfo";
$pgv_lang["admin_info"]				= "Informasjon";
$pgv_lang["admin_geds"]				= "Data- og slektsfil-administrasjon";
$pgv_lang["admin_site"]				= "Administrasjon av nettstedet";

//-- Relationship chart messages
$pgv_lang["relationship_chart"]	= "Slektskap";
$pgv_lang["person1"]			= "Person 1 ";
$pgv_lang["person2"]			= "Person 2 ";
$pgv_lang["no_link_found"]		= "- Kan ikke finne flere slektslinjer mellom de to personene!";
$pgv_lang["sibling"]			= "Søsken";
$pgv_lang["follow_spouse"]		= "Finn slektslinje via giftemål";
$pgv_lang["timeout_error"]		= "FEIL: Fant ikke noen slektslinje innenfor fastsatt søketid.";
$pgv_lang["son"]				= "Sønn";
$pgv_lang["daughter"]			= "Datter";
$pgv_lang["son-in-law"]				= "Svigersønn";
$pgv_lang["daughter-in-law"]		= "Svigerdatter";
$pgv_lang["grandchild"]				= "Barnebarn";
$pgv_lang["grandson"]				= "Barnebarn (gutt)";
$pgv_lang["granddaughter"]			= "Barnebarn (jente)";
$pgv_lang["brother"]			= "Bror";
$pgv_lang["sister"]				= "Søster";
$pgv_lang["brother-in-law"]		= "Svoger";
$pgv_lang["sister-in-law"]			= "Svigerinne";
$pgv_lang["aunt"]				= "Tante";
$pgv_lang["uncle"]				= "Onkel";
$pgv_lang["firstcousin"]		= "Søskenbarn";
$pgv_lang["femalecousin"]		= "Kusine";
$pgv_lang["malecousin"]			= "Fetter";
$pgv_lang["cousin-in-law"]		= "Søskenbarn til ektefelle";
$pgv_lang["relationship_to_me"]	= "Slektskap til deg";
$pgv_lang["rela_husb"]			= "Slektskap til ektemann";
$pgv_lang["rela_wife"]			= "Slektskap til hustru";
$pgv_lang["next_path"]			= "Finn neste slektslinje";
$pgv_lang["show_path"]			= "Slektslinje";
$pgv_lang["line_up_generations"]	= "Vis like slektsledd på linje";
$pgv_lang["oldest_top"]			= "Vis de eldste øverst";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]		= "Er du sikker på at du vil slette disse slektsdataene?";
$pgv_lang["access_denied"]		= "<h3><b>Ingen adgang!</b></h3><br />Du har ikke tilgang til denne delen.";
$pgv_lang["gedrec_deleted"]		= "Oppføringen i slektsfilen er nå slettet.";
$pgv_lang["gedcom_deleted"]		= "Slektsfilen [#GED#] er nå slettet.";
$pgv_lang["changes_exist"]		= "Det er gjort endringer i denne slektsbasen.";
$pgv_lang["accept_changes"]		= "Godta / Avvis endring(ene)";
$pgv_lang["show_changes"]		= "Denne oppføringen er blitt oppdatert. Klikk her for å se endring(ene).";
$pgv_lang["hide_changes"]		= "Klikk her for å skjule endringer.";
$pgv_lang["review_changes"]		= "Vis endringer i slektsbasen";
$pgv_lang["undo_successful"]	= "Angring er utført";
$pgv_lang["undo"]				= "Angre";
$pgv_lang["view_change_diff"]	= "Vis endring(er), forskjell(er)";
$pgv_lang["changes_occurred"]	= "Følgende endring(er) er gjort for denne personen:";
$pgv_lang["find_place"]			= "Finn stedsnavn";
$pgv_lang["refresh"]			= "Oppdater";
$pgv_lang["close_window"]		= "Lukk vinduet";
$pgv_lang["close_window_without_refresh"]	= "Lukk vinduet uten å oppdatere skjermen";
$pgv_lang["place_contains"]		= "Stedsnavn inneholder:";
$pgv_lang["accept_gedcom"]		= "Bestem deg for om du vil godkjenne eller avvise de enkelte endringene.<dl><dt>For å godta <i>alle</i> endringene på en gang</dt><dd>Klikk på linken \"Godkjenn alle endringer\" i boksen under.</dd><dt>For å få mer informasjon om en endring kan du klikke på:</dt><dd><li>\"$pgv_lang[view_change_diff]\" for å se på forskjellen mellom gammelt og nytt innhold.<br /><li>\"$pgv_lang[view_gedcom]\" for å se på det nye innholdet i slektsfil-oppsett (GEDCOM).</dd></dl>";
$pgv_lang["ged_import"]			= "Importer";
$pgv_lang["now_import"]			= "Nå bør du importere innholdet i slektsfilen til PhpGedView ved å klikke på linken \"Importer\" nedenfor.";
$pgv_lang["add_fact"]			= "Legg til nye fakta";
$pgv_lang["add"]				= "Legg til";
$pgv_lang["custom_event"]		= "Egendef. hendelse";
$pgv_lang["update_successful"]	= "Oppdatering er utført";
$pgv_lang["add_child"]			= "Legg til barn";
$pgv_lang["add_child_to_family"]	= "Legg til et barn til denne familien";
$pgv_lang["add_sibling"]		= "Legg til en bror eller søster";
$pgv_lang["add_son_daughter"]	= "Legg til en sønn eller datter";
$pgv_lang["must_provide"]		= "Du må sørge for et ";
$pgv_lang["delete_person"]		= "Slett denne personen";
$pgv_lang["confirm_delete_person"]	= "Er du sikker på at du vil slette denne personen fra slektsbasen?";
$pgv_lang["find_media"]			= "Finn media";
$pgv_lang["set_link"]			= "Lag kobling";
$pgv_lang["add_source_lbl"]		= "Legg til kildehenvisning";
$pgv_lang["add_source"]			= "Legg til en ny kildehenvisning";
$pgv_lang["add_note_lbl"]		= "Legg til note";
$pgv_lang["add_note"]			= "Legg til en ny note";
$pgv_lang["add_media_lbl"]		= "Legg til bilder/andre medier";
$pgv_lang["add_media"]			= "Legg til en ny bilde- / mediefil";
$pgv_lang["delete_source"]		= "Slett denne kilden";
$pgv_lang["confirm_delete_source"]	= "Er du sikker på at du vil slette denne kilden fra slektsbasen?";
$pgv_lang["add_husb"]			= "Legg til ektemann";
$pgv_lang["add_husb_to_family"]	= "Legg til en ektemann/far til denne familien";
$pgv_lang["add_wife"]			= "Legg til hustru";
$pgv_lang["add_wife_to_family"]	= "Legg til en hustru/mor til denne familien";
$pgv_lang["find_family"]		= "Finn familie";
$pgv_lang["find_fam_list"]		= "Finn Familieliste";
$pgv_lang["add_new_wife"]		= "Legg til en ny hustru";
$pgv_lang["add_new_husb"]		= "Legg til en ny ektemann";
$pgv_lang["edit_name"]			= "Rediger navn";
$pgv_lang["delete_name"]		= "Slett navn";
$pgv_lang["no_temple"]			= "Ingen Tempel-Living tilordning";
$pgv_lang["replace"]			= "Erstatt oppføring";
$pgv_lang["append"]				= "Legg til en ny oppføring";
$pgv_lang["add_father"]			= "Legg til en ny far";
$pgv_lang["add_mother"]			= "Legg til en ny mor";
$pgv_lang["add_obje"]			= "Legg til et nytt bilde / media";
$pgv_lang["no_changes"]			= "Det er ikke noen endringer som det er nødvendig å vise på nytt.";
$pgv_lang["accept"]				= "Godkjenne";
$pgv_lang["accept_all"]			= "Godkjenn alle endringer";
$pgv_lang["accept_successful"]	= "Godkjente endringer er lagt til databasen";
$pgv_lang["edit_raw"]			= "Endre opplysninger direkte i slektsfilen";
$pgv_lang["select_date"]		= "Velg en dato";
$pgv_lang["create_source"]		= "Opprett en ny kilde";
$pgv_lang["new_source_created"]	= "Den nye kilden er lagret!";
$pgv_lang["paste_id_into_field"]= "Legg inn følgende kilde-ID inn i endringsfeltet som en referanse til denne kilden ";
$pgv_lang["add_name"]			= "Legg til nytt navn";
$pgv_lang["privacy_not_granted"]	= "Du har ikke tilgang til";
$pgv_lang["user_cannot_edit"]		= "Dette brukernavnet har ikke rettigheter til å endre denne slektsbase.";
$pgv_lang["gedcom_editing_disabled"]	= "Muligheten til å gjøre endringer i denne slektsbase er blitt deaktivert av system-administratoren.";
$pgv_lang["privacy_prevented_editing"]	= "På grunn av hensyn til personvern, kan du ikke endre opplysningene.";
$pgv_lang["add_asso"]			= "Legg til en ny tilknyttet person";
$pgv_lang["edit_sex"]			= "Endre kjønn";
$pgv_lang["ged_noshow"]			= "Denne siden er deaktivert av administratoren til nettstedet.";

//-- calendar.php messages
$pgv_lang["bdm"]				= "Fødselsdager, dødsfall og giftemål";
$pgv_lang["on_this_day"]		= "Årsdag for hendelser i slekten...";
$pgv_lang["in_this_month"]		= "Årsdager for hendelser i slekten...";
$pgv_lang["in_this_year"]		= "Hendelser i slekten...";
$pgv_lang["year_anniversary"]	= "#year_var#. årsdag";
$pgv_lang["today"]				= "I dag";
$pgv_lang["day"]				= "Dag";
$pgv_lang["month"]				= "Måned";
$pgv_lang["showcal"]			= "Vis hendelser for";
$pgv_lang["anniversary_calendar"] = "Historisk kalender";
$pgv_lang["monday"]				= "mandag";
$pgv_lang["tuesday"]			= "tirsdag";
$pgv_lang["wednesday"]			= "onsdag";
$pgv_lang["thursday"]			= "torsdag";
$pgv_lang["friday"]				= "fredag";
$pgv_lang["saturday"]			= "lørdag";
$pgv_lang["sunday"]				= "søndag";
$pgv_lang["viewday"]			= "Vis dag";
$pgv_lang["viewmonth"]			= "Vis måned";
$pgv_lang["viewyear"]			= "Vis år";
$pgv_lang["all_people"]			= "Alle personer";
$pgv_lang["living_only"]		= "Nålevende personer";
$pgv_lang["recent_events"]		= "De siste 100 årene";
$pgv_lang["day_not_set"]		= "Dag ikke angitt";
$pgv_lang["year_error"]			= "Beklager, datoer før 1970 støttes ikke.";

//-- user self registration module
$pgv_lang["lost_password"]		= "Glemt passordet ditt?";
$pgv_lang["requestpassword"]	= "Be om nytt passord";
$pgv_lang["no_account_yet"]		= "Har du ikke <b>egen</b> konto enda!?";
$pgv_lang["requestaccount"]		= "Søke om brukerkonto";
$pgv_lang["emailadress"]		= "Epost-adresse";
$pgv_lang["mandatory"] 			= "Felt merket med * må fylles ut.";

$pgv_lang["mail01_line01"]		= "Hei #user_fullname# ...";
$pgv_lang["mail01_line02"]		= "En anmodning er gjort på ( #SERVER_NAME# ) om å få en brukerkonto med din epost-adresse ( #user_email# ).";
$pgv_lang["mail01_line03"]		= "Opplysningene om deg ble gitt ved forespørsel om brukerkonto.";
$pgv_lang["mail01_line04"]		= "Vennligst klikk på linken under og fyll ut riktige data for å bekrefte kontoen og epost-adressen din.";
$pgv_lang["mail01_line05"]		= "Dersom du ikke har bedt om å få en konto, kan du bare slette denne e-posten.";
$pgv_lang["mail01_line06"]		= "Du vil ikke få tilsendt flere e-poster herfra, fordi kontoen vil bli slettet etter 7 dager dersom den ikke bekreftes.";

$pgv_lang["mail01_subject"]		= "Din registrering hos #SERVER_NAME#";

$pgv_lang["mail02_line01"]		= "Hei administrator ...";
$pgv_lang["mail02_line02"]		= "En ny bruker har registreret seg hos ( #SERVER_NAME# ).";
$pgv_lang["mail02_line03"]		= "Brukeren har fått tilsendt en e-post med de nødvendige data for å bekrefte ønske om konto.";
$pgv_lang["mail02_line04"]		= "Så snart brukeren har bekreftet kontoen, vil du få en epost der du blir bedt om å gi denne brukeren tillatelse til å få en konto.";
$pgv_lang["mail02_line04a"]		= "Så snart brukeren har bekreftet kontoen, vil du få en epost om dette.  Brukeren vil nå kunne logge seg inn uten at du trenger å gjøre noe.";
$pgv_lang["mail02_subject"]		= "Ny registrering på #SERVER_NAME#";

$pgv_lang["hashcode"]			= "Kontrollkode:";
$pgv_lang["thankyou"]			= "Hei #user_fullname# og takk for søknaden din om å få en brukerkonto.";
$pgv_lang["pls_note06"]			= "Du vil nå få tilsendt en bekreftelses-epost til adressen ( #user_email# ).<br />Ved hjelp av denne e-posten kan du aktivere kontoen din. Dersom du ikke aktiverer kontoen din innen 7 dager, vil den bli slettet (du kan registrere kontoen igjen etter de 7 dagene, om du ønsker det). For å logge deg inn på dette nettstedet, kreves det at du oppgir et brukernavn og passord.<br /><br /><br /><br />";
$pgv_lang["pls_note06a"] 		= "Vi vil nå sende en bekreftelse på e-post til adressen ( #user_email# ). Du må bekrefte ønske ditt om å få en konto ved å følge instruksene i i e-posten. Dersom du ikke bekrefter ønsket om å få en konto innen 7 dager, vil ønsket om en konto bli avslått automatisk.  Dersom senere ønsker å få en ny konto, må du søke på nytt.<br /><br />Etter at du har fulgt instruksjonene i bekreftelses-e-posten, kan du logge deg inn.  For å logge deg inn på denne nettsiden, kreves det at du oppgir brukernavnrt og passordet ditt.<br /><br />";

$pgv_lang["registernew"]		= "Bekreftelse av ny konto";
$pgv_lang["user_verify"]		= "Bruker-godkjenning";
$pgv_lang["send"]				= "Send";

$pgv_lang["pls_note07"]			= "Oppgi det brukernavnet, passordet og kontrollkoden du fikk tilsendt pr. epost fra dette nettstedet som en bekreftelse på søknaden din.";
$pgv_lang["pls_note08"]			= "Informasjonen om brukeren <b>#user_name#</b> er sjekket.";

$pgv_lang["mail03_line01"]		= "Hei administrator ...";
$pgv_lang["mail03_line02"]		= "#newuser[username]# ( #newuser[fullname]# ) har bekreftet ønsket om å få en brukerkonto.";
$pgv_lang["mail03_line03"]		= "Klikk på linken \"Admin\" på siden \"Logg inn\" på PhpGedView for å godkjenne den nye brukeren på nettstedet ditt.";
$pgv_lang["mail03_line03a"]		= "Du behøver ikke forta deg noe; brukeren kan nå logge seg inn.";
$pgv_lang["mail03_subject"]		= "Ny bekreftelse fra #SERVER_NAME#";

$pgv_lang["pls_note09"]			= "Du er gjenkjent som en søker på dette nettstedet.";
$pgv_lang["pls_note10"]			= "Administratoren til nettstedet har fått beskjed om søknaden.<br />Så snart vedkommende har godkjent kontoen din,<br />kan du logge deg inn med ditt brukernavn og passord.";
$pgv_lang["pls_note10a"]		= "Du kan nå logge deg inn med brukernavnet og passordet ditt.";
$pgv_lang["data_incorrect"]		= "Data var ugyldig!<br />- Prøv igjen!";
$pgv_lang["user_not_found"]		= "Kunne ikke gjenkjenne opplysningene du oppga! Gå tilbake og prøv igjen.";

$pgv_lang["lost_pw_reset"]		= "Nytt passord";

$pgv_lang["pls_note11"]			= "For å få et nytt passord, må du oppgi brukernavnet og epost-adressen til brukerkontoen din. <br /><br />Vi vil deretter sende deg en epost med en spesiell internettadresse, som inneholder en bekreftelses-kode for kontoen din.<br />På denne internettsiden vil du kunne endre passordet for å få tilgang til brukersiden din igjen.<br />Av sikkerhetsgrunner, bør du ikke vise denne bekreftelses-koden til noen, inkludert administrator(ene) til denne siden (vi vil heller ikke spørre etter den).<br /><br />Dersom du ønsker å få mer hjelp vedrørende dette, så kontakt administrator.";
$pgv_lang["enter_email"]		= "Du må oppgi en epost-adresse.";

$pgv_lang["mail04_line01"]		= "Hei #user_fullname# ...";
$pgv_lang["mail04_line02"]		= "Det ble bestilt et nytt passord til brukernavnet ditt!";
$pgv_lang["mail04_line03"]		= "Anbefaling:";
$pgv_lang["mail04_line04"]		= "Vennligst klikk på linken under. Logg deg inn med det nye passordet. Du bør så angi et nytt passord for å verne om sikkerheten til dataene dine.";
$pgv_lang["mail04_line05"]		= "Etter at du har logget deg inn, velg da '#pgv_lang[myuserdata]#' i menyen '#pgv_lang[mygedview]#' og angi et nytt passord i feltet for å endre passordet ditt.";
$pgv_lang["mail04_subject"]		= "Data anmodning fra #SERVER_NAME#";

$pgv_lang["pwreqinfo"]			= "Hei...<br /><br />En epost med det nye passordet ble sendt til epost-adressen (#user[email]#).<br /><br />Vær vennlig å sjekk epost-kontoen din om noen minutter.<br /><br />Anbefaling:<br /><br />Etter at du har mottatt eposten, bør du logge deg inn på dette nettstedet med ditt nye passord og endre det. Dette bør gjøres for å verne om sikkerheten til dataene din.";

$pgv_lang["editowndata"]		= "Min konto";
$pgv_lang["savedata"]			= "Lagre endrede data";
$pgv_lang["datachanged"]		= "Brukerdata er endret!";
$pgv_lang["datachanged_name"]	= "Du må kanskje logge deg inn på nytt med det nye brukernavnet ditt.";
$pgv_lang["myuserdata"]			= "Min konto";
$pgv_lang["verified"]			= "Bruker har<br />bekreftet søknaden";
$pgv_lang["verified_by_admin"]	= "Godkjent bruker<br />[av Admin]";
$pgv_lang["user_theme"]			= "Min stil";
$pgv_lang["mgv"]				= "Min side";
$pgv_lang["mygedview"]			= "Min GedView";
$pgv_lang["passwordlength"]		= "Passordet må inneholde minst 6 tegn.";
$pgv_lang["admin_approved"]		= "Din konto hos #SERVER_NAME# er blitt godkjent";
$pgv_lang["you_may_login"]		= " av administratoren til nettstedet.<br />Du kan nå logge deg inn på nettstedet ved å klikke på linken under:";
$pgv_lang["welcome_text_auth_mode_1"]	= "<center><h3><b>Velkommen til disse slektssidene !</b></h3>PS! - <i>Sidene er tilgjengelig for <b>alle</b> besøkende som har en brukerkonto.</i><br />Har du en brukerkonto, kan du logge deg inn under.<br /><br />Dersom du ikke har en brukerkonto enda, kan du søke om å få en<br />ved å klikke på linken \"Søke om brukerkonto\".<br />Etter å ha sjekket informasjonen i søknaden, vil administratoren til nettstedet aktivere kontoen din.<br />Du vil motta en e-post når den er godkjent.</center>";
$pgv_lang["welcome_text_auth_mode_2"]	= "<center><h3><b>Velkommen til disse slektssidene !</b></h3>PS! - <i>Sidene er <b>bare</b> tilgjengelig for <b>registrerte</b> brukere!</i><br />Har du en brukerkonto, kan du logge deg inn under.<br /><br />Dersom du ikke har en konto enda, kan søke om å få opprettet en konto<br />ved å klikke på linken \"Søke om brukerkonto\".<br />Etter å ha sjekket informasjonen i søknaden din,<br />kan administratoren til nettstedet enten godkjenne eller avslå den.<br />Du vil motta en e-post med beskjed dersom søknaden din blir akseptert.</center>";
$pgv_lang["welcome_text_auth_mode_3"]	= "<center><h3><b>Velkommen til disse slektssidene !</b></h3>PS! - <i>Sidene er <b>bare</b> tilgjengelig for <b>medlemmer av familien</b>.</i><br />Har du en brukerkonto, kan du logge deg inn under.<br /><br />Dersom du ikke har en konto enda, kan søke om å få opprettet en konto<br />ved å klikke på linken \"Søke om brukerkonto\".<br />Etter å ha sjekket informasjonen i søknaden din,<br />kan administratoren til nettstedet enten godkjenne eller avslå den.<br />Du vil motta en e-post dersom den blir godkjent.</center>";
$pgv_lang["welcome_text_cust_head"]		= "<center><h3><b>Velkommen til disse slektssidene !</b></h3>PS! - <i>Sidene er <b>bare</b> tilgjengelig for brukere som har <b>gyldig</b> brukernavn og passord.</i></center><br />";

//-- mygedview page
$pgv_lang["welcome"]			= "Velkommen";
$pgv_lang["upcoming_events"]	= "Kommende begivenheter";
$pgv_lang["living_or_all"]		= "Vise bare hendelser for levende personer?";
$pgv_lang["basic_or_all"]		= "Vise bare fødselsdager, dødsfall og giftemål?";
$pgv_lang["no_events_living"]	= "Fant ingen hendelser for levende personer for de neste #pgv_lang[global_num1]# dagene.";
$pgv_lang["no_events_living1"]	= "Fant ingen hendelser for levende personer for i morgen.";
$pgv_lang["no_events_all"]		= "Fant ingen hendelser for de neste #pgv_lang[global_num1]# dagene.";
$pgv_lang["no_events_all1"]		= "Fant ingen hendelser for i morgen.";
$pgv_lang["no_events_privacy"]	= "Det finnes hendelser for de neste #pgv_lang[global_num1]# dagene, men på grunn av hensyn til personvern vises ikke disse.";
$pgv_lang["no_events_privacy1"]	= "Det finnes hendelser for i morgen, men på grunn av hensyn til personvern vises ikke disse.";
$pgv_lang["more_events_privacy"]	= "<br />Det finnes flere hendelser for de neste #pgv_lang[global_num1]# dagene, men på grunn av hensyn til personvern vises ikke disse.";
$pgv_lang["more_events_privacy1"]	= "<br />Det finnes flere hendelser for i morgen, men på grunn av hensyn til personvern vises ikke disse.";
$pgv_lang["none_today_living"]	= "Fant ingen hendelser for levende personer for i dag.";
$pgv_lang["none_today_all"]		= "Fant ingen hendelser for i dag.";
$pgv_lang["none_today_privacy"]	= "Det finnes hendelser for i dag, men på grunn av hensyn til personvern vises ikke disse.";
$pgv_lang["more_today_privacy"]	= "<br />Det finnes flere hendelser for i dag, men på grunn av hensyn til personvern vises ikke disse.";
$pgv_lang["chat"]				= "Chat";
$pgv_lang["users_logged_in"]	= "Brukere som er pålogget";
$pgv_lang["anon_user"]				= "1 anonym bruker pålogget";
$pgv_lang["anon_users"]				= "#pgv_lang[global_num1]# anonyme brukere pålogget";
$pgv_lang["login_user"]				= "1 bruker pålogget";
$pgv_lang["login_users"]			= "#pgv_lang[global_num1]# brukere pålogget";
$pgv_lang["no_login_users"]			= "Ingen brukere er pålogget";
$pgv_lang["message"]			= "Ny beskjed til ";
$pgv_lang["my_messages"]		= "Mine beskjeder";
$pgv_lang["date_created"]		= "Dato sendt:";
$pgv_lang["message_from"]		= "Epost-adresse:";
$pgv_lang["message_from_name"]	= "Ditt navn:";
$pgv_lang["message_to"]			= "Beskjed til:";
$pgv_lang["message_subject"]	= "Emne:";
$pgv_lang["message_body"]		= "Din beskjed:";
$pgv_lang["no_to_user"]			= "Det ble ikke oppgitt en annen bruker som mottaker. - Kan ikke fortsette.";
$pgv_lang["provide_email"]		= "Vennligst oppgi epost-adressen din, så vi kan besvare denne henvendelse.<br />Dersom du ikke oppgir epost-adressen din, har vi ikke mulig til å besvare denne forespørselen.<br />PS. Epost-adressen din vil ikke bli brukt til annet enn å besvare denne forespørselen.";
$pgv_lang["reply"]				= "Svar";
$pgv_lang["message_deleted"]	= "Beskjed slettet";
$pgv_lang["message_sent"]		= "Beskjed sendt";
$pgv_lang["reset"]				= "Vanlig størrelse / oppsett";
$pgv_lang["site_default"]		= "Standard på siden";
$pgv_lang["mygedview_desc"]		= "Dette er <i>din side</i> der du kan velge egne <i>favoritter</i>, bli påminnet om <i>kommende begivenheter</i> og <i>samarbeide med andre brukere</i>.";
$pgv_lang["no_messages"]		= "Det er ingen beskjeder til deg.";
$pgv_lang["clicking_ok"]		= "Ved å klikke på OK, åpnes det et nytt vindu, der du kan kontakte #user[fullname]#";
$pgv_lang["favorites"]			= "Favoritter";
$pgv_lang["my_favorites"]		= "Mine favoritter";
$pgv_lang["no_favorites"]		= "<i>Du har ikke valgt noen favoritter enda!</i><br />For å legge til en person til dine favoritter, kan du enten finne siden med fakta om personen og så klikke på linken <br />\"Legg til i Mine favoritter\" eller bruk ID-feltet under for å legge til en person ved hjelp av personens ID-nummer.";
$pgv_lang["add_to_my_favorites"] = "Legg til i Mine favoritter";
$pgv_lang["gedcom_favorites"]	 = "Favoritter i denne slektsbasen";
$pgv_lang["no_gedcom_favorites"] = "Det er ikke valgt noen Favoritter enda.  Det er administrator som kan legge til personer her, slik at disse vises ved oppstart.";
$pgv_lang["confirm_fav_remove"]	= "Er du sikker på at du vil fjerne denne personen fra favorittene dine?";
$pgv_lang["invalid_email"]		= "Tast inn en gyldig epost-adresse.";
$pgv_lang["enter_subject"]		= "Tast inn en tekst i feltet for emne.";
$pgv_lang["enter_body"]			= "Skriv inn en beskjed / tekst før den sendes.";
$pgv_lang["confirm_message_delete"]	= "Er du sikker på du vil slette denne beskjeden? Når den er slettet, kan den ikke hentes tilbake igjen.";

$pgv_lang["message_email1"]		= "Følgende beskjed ble sendt til kontoen din i PhpGedView fra ";
$pgv_lang["message_email2"]		= "Du sendte følgende beskjed til en bruker av PhpGedView:";
$pgv_lang["message_email3"]		= "Du sendte følgende beskjed til administratoren av PhpGedView:";
$pgv_lang["viewing_url"]		= "Denne beskjed ble sendt da du var på følgende url: ";
$pgv_lang["messaging2_help"]	= "Når du sender denne beskjeden, vil du også motta en kopi til den epost-adressen din som du har oppgitt.";
$pgv_lang["random_picture"]		= "Tilfeldig utvalgt bilde / medie";
$pgv_lang["message_instructions"]	= "<b>PS:</b> Privat informasjon om levende personer vil bare bli gitt til slektninger og nære venner.  Du vil bli spurt om å bekrefte din tilknytning / slektskap før du vil få se private data.  Av og til kan informasjon om døde personer også være private.  Hvis dette er tilfelle, er det på grunn av at det ikke funnet nok informasjon om personen til å avgjøre om vedkommende lever eller ikke, eller andre opplysninger om personen.<br /><br />Før du gjør en foresprsel, vennligst sjekk opp at det er riktig person ved å se på datoer, steder og nære slektninger.  Dersom du legger inn endringer i slektsdataene, vær vennlig å oppgi kildene der du fant dataene.<br /><br />";
$pgv_lang["sending_to"]			= "- Beskjeden vil bli sendt til #TO_USER#";
$pgv_lang["preferred_lang"]	 	= "- som ønsker at du skriver beskjeden på #USERLANG#<br />";
$pgv_lang["gedcom_created_using"]	= "Denne slektsbasen ble lagd ved hjelp av <b>#SOFTWARE# #VERSION#</b>";
$pgv_lang["gedcom_created_on"]	= "SlektsGED-filen ble lagd <b>#DATE#</b>";
$pgv_lang["gedcom_created_on2"]	= " <b>#DATE#</b>";
$pgv_lang["gedcom_stats"]		= "Statistikk for slektsbasen";
$pgv_lang["stat_individuals"]	= "Personer";
$pgv_lang["stat_families"]		= "Familier";
$pgv_lang["stat_sources"]		= "Kilder";
$pgv_lang["stat_other"]			= "Andre oppføringer";
$pgv_lang["stat_earliest_birth"] 	= "Tidligste fødselsår";
$pgv_lang["stat_latest_birth"] 	= "Siste fødselsår";
$pgv_lang["stat_earliest_death"] 	= "Tidligste dødsår";
$pgv_lang["stat_latest_death"] 	= "Siste dødsår";
$pgv_lang["customize_page"]		= "Endre <i>din egen</i> side";
$pgv_lang["customize_gedcom_page"]	= "Endre hovedsiden til denne slektsbasen";
$pgv_lang["upcoming_events_block"]	= "Rammen Kommende begivenheter";
$pgv_lang["upcoming_events_descr"]	= "Rammen for kommende begivenheter viser en liste med begivenheter i den aktive slektsfilen som vil skje de neste 30 dagene.  For en bruker vil denne rammen bare vise levende personer.  På hovedsiden vil alle personer/familier bli vist (NB. Her vil personvern-valg kunne redusere listen).";
$pgv_lang["todays_events_block"]	= "Rammen Årsdag for hendelser i slekten";
$pgv_lang["todays_events_descr"]	= "Rammen for Årsdag for hendelser viser en personliste som er knyttet til hendelser i den aktive slektsfilen dagen i dag.  Dersom det ikke er noen hendelser, vises heller ikke rammen.  På \"din\" GedView side vil denne rammen bare vise levende personer.  På hovedsiden vil alle personer / familier bli vist (NB. Her vil personvern-valg kunne redusere listen).";
$pgv_lang["logged_in_users_block"]	= "Rammen Brukere som er logget inn";
$pgv_lang["logged_in_users_descr"]	= "Rammen for brukere som er logget inn, viser en liste med bruker som er logget inn på det gjeldende tidspunktet...";
$pgv_lang["user_messages_block"]	= "Rammen Bruker-beskjeder";
$pgv_lang["user_messages_descr"]	= "Rammen for bruker-beskjeder viser en liste med beskjeder som har blitt sendt til de aktive brukerne.";
$pgv_lang["user_favorites_block"]	= "Rammen Brukeres favoritter-personer";
$pgv_lang["user_favorites_descr"]	= "Rammen for brukeres Favoritter viser en liste med deres egne \"favoritt-personer\" i slekten slik at de lett kan finnes igjen.";
$pgv_lang["welcome_block"]		= "Rammen Bruker/eier-info";
$pgv_lang["welcome_descr"]		= "Rammen for brukeres egen siden viser brukeren, gjeldende dato og tid, hurtiglinker for endre egen konto eller å gå til deres eget slektstre, og en link for å tilpasse egen siden.";
$pgv_lang["random_media_block"]	= "Rammen Tilfeldig utvalgt bilde- / mediefil";
$pgv_lang["random_media_descr"]	= "Rammen for tilfeldig viste bilde / mediefil velger et tilfeldig foto eller annet medie-objekt i den aktive slektsbasen og viser det til brukeren.";
$pgv_lang["random_media_persons_or_all"]	= "Vise bare personer, hendelser eller alle?";
$pgv_lang["random_media_persons"]	= "Personer";
$pgv_lang["random_media_events"]	= "Hendelser";
$pgv_lang["gedcom_block"]		= "Rammen Slektsbase-info";
$pgv_lang["gedcom_descr"]		= "Rammen for informasjon virker på samme måte som den enkelte brukers velkomstmelding, ved at besøkende på siden ønskes velkommen, viser tittelen på standard aktive slektsfil og gjeldende dato og tid.";
$pgv_lang["gedcom_favorites_block"]	= "Rammen Favoritter i slektsbasen";
$pgv_lang["gedcom_favorites_descr"]	= "Rammen for Favoritter gir administrator på nettstedet muligheten til å velge ut noen sentrale personer i slektsfilen som vil være av interesse for de fleste besøkende. På den måten kan de lett finne disse favoritt-personene og er en måte å fremheve disse personene som sentrale i slekthistorien.";
$pgv_lang["gedcom_stats_block"]	= "Rammen Statistikk for slektsbasen";
$pgv_lang["gedcom_stats_descr"]	= "Rammen for statistikk viser besøkende en del basis-informasjon om slektsfilen, slik som når den ble lagd og hvor mange personer, familier og kilder som finnes i slektsbasen.<br /><br />Den har også en liste med de etternavnene som er registrert flest ganger i slektsbasen.  Du kan velge om du vil vise disse etternavnene i rammen eller ikke.<br />Du kan også velge at visse navn ikke skal vises i listen eller legge legge til navn som du mener bør være med (selv om de ikke fyller kravet om å være med i listen).<br />Du kan angi antall ganger et navn må være registrert for å vises i listen i konfigurasjonsfilen for slektsbasen.";
$pgv_lang["gedcom_stats_show_surnames"]	= "Vise Mest brukte etternavn?";
$pgv_lang["portal_config_intructions"]	= "Her kan du tilpasse siden ved å bestemme hvor de ulike rammene på siden skal plasseres.<br />Siden er delt opp i to seksjoner, 'Hoved'-seksjonen og 'Høyre' seksjon.<br />'Hoved'-seksjonen er tildelt en større bredde og kommer under velkomsttittel på siden.<br />Den 'høyre' seksjonen begynner til høyre for tittelen og fremstår som en mer hurtiginformasjons-blokk.<br />Hver seksjon har sin egen liste med rammer som vil bli vist på siden i den rekkefølgen som de er listet.<br />Du kan legge til, fjerne og omorganisere rammene slik du ønsker det.<br /><br />Dersom listen for en av seksjonene er tom, vil de andre rammene bruke hele bredden på siden.<br /><br />";
$pgv_lang["login_block"]		= "Rammen Logg inn";
$pgv_lang["login_descr"]		= "Rammen for 'Logg inn' skriver et brukernavn og passord for brukere slik at de kan logg seg inn.";
$pgv_lang["theme_select_block"]	= "Rammen Velg stil";
$pgv_lang["theme_select_descr"]	= "Rammen for stilvalg viser valgfeltet for ønsket stil selv om valget for å bytte stil ikke er aktivert.";
$pgv_lang["block_top10_title"]	= "Mest viste etternavn";
$pgv_lang["block_top10"]		= "Rammen Topp 10 etternavn";
$pgv_lang["block_top10_descr"]	= "Rammen viser en tabell med de 10 mest viste etternavnene i slektsbasen";

$pgv_lang["gedcom_news_block"]	= "Rammen Nyheter for slektsbasen";
$pgv_lang["gedcom_news_descr"]	= "Nyhetsrammen viser besøkende siste nytt eller artikler lagt inn av en bruker med administrator-rettigheter.<br />Rammen er et fint sted å bekjentgjøre oppdatering av slektsbasen eller et slektstevne.";
$pgv_lang["gedcom_news_limit"]		= "Nyheter utløper:";
$pgv_lang["gedcom_news_limit_nolimit"]	= "Aldri";
$pgv_lang["gedcom_news_limit_date"]		= "Alder på artikkel";
$pgv_lang["gedcom_news_limit_count"]	= "Antall artikler";
$pgv_lang["gedcom_news_flag"]		= "Grense:";
$pgv_lang["gedcom_news_archive"] 	= "Vis arkiv";

$pgv_lang["user_news_block"]	= "Rammen Notatblokk for bruker";
$pgv_lang["user_news_descr"]	= "Rammen med en notatblokk lar den enkelte bruker legge inn notater eller som en online-oppslagstavle.";
$pgv_lang["my_journal"]			= "Min notatblokk";
$pgv_lang["no_journal"]			= "Du har ikke laget noen notater enda.";
$pgv_lang["confirm_journal_delete"]	= "Er du sikker på at du vil slette dette notatet?";
$pgv_lang["add_journal"]		= "Legg inn et nytt notat";
$pgv_lang["gedcom_news"]		= "Nyheter";
$pgv_lang["confirm_news_delete"]	= "Er du sikker på at du vil slette denne nyhetsartiklen?";
$pgv_lang["add_news"]			= "Legg inn et nyhetsinnlegg";
$pgv_lang["no_news"]			= "Ingen nyhetsartikler er blitt lagt inn...!";
$pgv_lang["edit_news"]			= "Legg til / endre notat- / nyhetsinnlegg";
$pgv_lang["enter_title"]		= "Vennligst oppgi en tittel.";
$pgv_lang["enter_text"]			= "Vennligst legg inn en tekst for dette nyhets- eller notatblokk-innlegget.";
$pgv_lang["news_saved"]			= "Nyhets- / Notablokkinnlegg er lagret...!";
$pgv_lang["article_text"]		= "Sett inn tekst:";
$pgv_lang["main_section"]		= "Hoved seksjons-rammer";
$pgv_lang["right_section"]		= "Høyre seksjons-rammer";
$pgv_lang["available_blocks"]		= "Tilgjengelige rammer";
$pgv_lang["move_up"]			= "Flytt opp";
$pgv_lang["move_down"]			= "Flytt ned";
$pgv_lang["move_right"]			= "Flytt til høyre";
$pgv_lang["move_left"]			= "Flytt til venstre";
$pgv_lang["broadcast_all"]		= "Send til alle brukere";
$pgv_lang["hit_count"]			= "Antall treff:";
$pgv_lang["phpgedview_message"]	= "PhpGedView beskjed";
$pgv_lang["common_surnames"]	= "Mest brukte etternavn";
$pgv_lang["default_news_title"]		= "Velkommen til disse slektssidene";
$pgv_lang["default_news_text"]		= "Informasjon om slekten(e) på dette nettstedet blir vist ved hjelp av <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView #VERSION#</a><br />Sidene gir deg et innblikk og en oversikt over denne slekten/slektssamlingen.<br />Som en start, kan du velge personlisten på menyen, et av diagrammene eller søke etter et navn eller et sted.<br /><br />Dersom det er noe du ikke forstår på en side, sjekk Hjelp i menyen!<br />Der vil du til enhver tid få informasjon om den siden du er på.<br /><br /><b><i>Takk for at du besøker dette nettstedet.</i></b>";
$pgv_lang["reset_default_blocks"]	= "Tilbakestill til standardrammer";
$pgv_lang["recent_changes"]			= "Siste endringer";
$pgv_lang["recent_changes_block"]	= "Rammen Siste endringer";
$pgv_lang["recent_changes_descr"]	= "Rammen <i>Siste endringer</i> vil vise en liste med alle endringer som er gjort i slektsbasen/-filen den siste måneden.  Denne rammen kan hjelpe deg til å holde deg oppdatert med de endringene som er gjort.  Endringene som vises er knyttet til CHAN-merket (tag).";
$pgv_lang["recent_changes_none"]	= "<b>Det har ikke vært gjort noen endringer de siste #pgv_lang[global_num1]# dagene.</b><br />";
$pgv_lang["recent_changes_some"]	= "<b>Endringer gjort de siste #pgv_lang[global_num1]# dagene</b><br />";
$pgv_lang["show_empty_block"]		= "Ikke vise denne rammen dersom den er tom?";
$pgv_lang["hide_block_warn"]		= "Dersom du velger å ikke vise en tom ramme, vil du heller ikke kunne endre oppsettet for rammen før den vises igjen ved at den ikke lenger er tom.";
$pgv_lang["delete_selected_messages"]	= "Slett beskjeder som er merket";
$pgv_lang["use_blocks_for_default"]	= "Bruke dette ramme-oppsettet som standard for alle brukere?";
$pgv_lang["block_not_configure"]	=	"Denne rammen har ingen valgmuligheter.";

//-- upgrade.php messages
$pgv_lang["include"]			= "Inkluder:";
$pgv_lang["page_x_of_y"]		= "Side #GLOBALS[currentPage]# av #GLOBALS[lastPage]#";
$pgv_lang["options"]			= "Valg:";
$pgv_lang["config_update_ok"]	= "Oppdatering av konfigurasjonsfilen er utført.";

//-- validate GEDCOM
$pgv_lang["performing_validation"]	= "Sjekken er utført...!  Gjør de nødvendige valgene og klikk deretter på 'Rydd'";
$pgv_lang["changed_mac"]			= "Macintosh linjeslutt oppdaget. Byttet linjeslutt med bare retur - til slutt med retur (RT) og linjeskift (LF).";
$pgv_lang["changed_places"]			= "Oppdaget ugyldig oppsett for steder. Rydder opp slik at de er tilpasset GEDCOM 5.5 spesifikasjonene.  Et eksempel fra slektsfilen din er:";
$pgv_lang["invalid_dates"]			= "Oppdaget ugyldig dato-format. Ved rydding vil disse bli endret til formatet DD MMM ÅÅÅÅ (f.eks. 1 JAN 2004).";
$pgv_lang["valid_gedcom"]			= "Gyldig slektsfil funnet.  Det er ikke nødvendig å gjøre endringer.";
$pgv_lang["optional_tools"]			= "Du kan også velg å kjøre følgende tilleggsverktøy før importering.";
$pgv_lang["optional"]				= "Tilleggsverktøy";
$pgv_lang["day_before_month"]		= "Dag før måned (DD MM ÅÅÅÅ)";
$pgv_lang["month_before_day"]		= "Måned før dag (MM DD YYYY)";
$pgv_lang["do_not_change"]			= "Ikke gjør endringer";
$pgv_lang["change_id"]				= "Endre personID til:";
$pgv_lang["example_place"]			= "Eksempel på ugyldig stedsnavn fra slektsfilen din:";
$pgv_lang["example_date"]			= "Eksempel på ugyldig dato fra slektsfilen din:";
$pgv_lang["add_media_tool"]			= "Lage koblinger til bilder / andre medier i slektsfilen";
$pgv_lang["launch_media_tool"]		= "Lag koblinger til bilder / andre medier.";
$pgv_lang["add_media_descr"]		= "Denne siden vil legge til mediemerket OBJE koblet til personer, familier mm. i slektsfilen.<br />Når du er ferdig vil de registrerte bilder og andre medier bli vist sammen med de IDen du har koblet dem til i ";
$pgv_lang["highlighted"]			= "Bruke som hovedbilde";
$pgv_lang["extension"]				= "Fil-type";
$pgv_lang["order"]					= "Rekkefølge";
$pgv_lang["inject_media_tool"]		= "Legg til media i slektsfilen";
$pgv_lang["media_table_created"]	= "Oppdateringen av <i>bilde- / medie</i>-tabellen er ferdig.";
$pgv_lang["click_to_add_media"]		= "Klikk her for å legge til bilder / medier, som er vist over, i slektsfilen: #GEDCOM#";
$pgv_lang["adds_completed"]			= "Ferdig med å legge til bilder / medier i slektsfilen.";
$pgv_lang["ansi_encoding_detected"]	= "Oppdaget ANSI tekstkoding.  PhpGedView fungerer best med filer som er kodet med UTF-8.";
$pgv_lang["invalid_header"]			= "Oppdaget at det er linjer før startlinjen (0 HEAD) i slektsfilen.  Under oppryddingen vil disse linjene bli fjernet.";
$pgv_lang["macfile_detected"]		= "Oppdaget Macintosh-fil.  Under oppryddingen vil denne filen bli konvertert til en DOS-fil.";
$pgv_lang["place_cleanup_detected"]	= "Oppdaget ugyldig stedskoder.  Disse bør endres!  Følgende steder er ugyldige: ";
$pgv_lang["cleanup_places"]			= "Rydd opp i stedene";
$pgv_lang["empty_lines_detected"]	= "Oppdaget tomme linjer i slektsfilen din.  Under oppryddingen vil disse tomme linjene bli fjernet.";
$pgv_lang["import_options"]			= "Valg for import";
$pgv_lang["import_options_help"] 	= "Du kan velge alternative måter å importere slektsfilen (ged) din.";
$pgv_lang["verify_gedcom"]			= "Sjekk slektsfilen (GEDCOM)";
$pgv_lang["verify_gedcom_help"]		= "Her kan du velge om du skal hente og importere denne slektsfilen eller om du vil avbryte prosessen.";
$pgv_lang["import_statistics"]		= "Statistikk for importen";

//-- hourglass chart
$pgv_lang["hourglass_chart"]	= "Timeglass";

//-- report engine
$pgv_lang["choose_report"]		= "Velg rapport";
$pgv_lang["enter_report_values"]	= "Oppsett for rapport";
$pgv_lang["selected_report"]	= "Valgt rapport";
$pgv_lang["run_report"]			= "Vis rapport";
$pgv_lang["select_report"]		= "Neste >>";
$pgv_lang["download_report"]	= "Lagre rapport";
$pgv_lang["reports"]			= "Rapporter";
$pgv_lang["pdf_reports"]		= "PDF rapporter";
$pgv_lang["html_reports"]		= "HTML rapporter";

//-- Ahnentafel report
$pgv_lang["ahnentafel_report"]		= "Forfedre";
$pgv_lang["ahnentafel_header"]		= "Forfedre til ";
$pgv_lang["ahnentafel_generation"]	= "Generasjon ";
$pgv_lang["ahnentafel_pronoun_m"]	= "Han ";
$pgv_lang["ahnentafel_pronoun_f"]	= "Hun ";
$pgv_lang["ahnentafel_born_m"]		= "ble født";			// male
$pgv_lang["ahnentafel_born_f"]		= "ble født";			// female
$pgv_lang["ahnentafel_christened_m"] = "ble døpt";			// male
$pgv_lang["ahnentafel_christened_f"] = "ble døpt";			// female
$pgv_lang["ahnentafel_married_m"]	= "gift";				// male
$pgv_lang["ahnentafel_married_f"]	= "gift";				// female
$pgv_lang["ahnentafel_died_m"]		= "døde";				// male
$pgv_lang["ahnentafel_died_f"]		= "døde";				// female
$pgv_lang["ahnentafel_buried_m"]	= "ble gravlagt";		// male
$pgv_lang["ahnentafel_buried_f"]	= "ble gravlagt";		// female
$pgv_lang["ahnentafel_place"]		= " i/på ";				// place name follows this
$pgv_lang["ahnentafel_no_details"]	= " men detaljene er ukjent";

//-- Descendancy report
$pgv_lang["descend_report"]			= "Etterkommere";
$pgv_lang["descendancy_header"]		= "Etterkommere til ";

//-- Family report
$pgv_lang["family_group_report"]	= "Familie";
$pgv_lang["page"]				= "Side";
$pgv_lang["of"]					= "av";
$pgv_lang["enter_famid"]		= "Angi FamilieID";
$pgv_lang["show_sources"]		= "Vise kilder?";
$pgv_lang["show_notes"]			= "Vise noter?";
$pgv_lang["show_basic"]			= "Skriv ut de vanligste hendelsene<br />&nbsp;&nbsp;&nbsp;- selv om disse er tomme?";
$pgv_lang["show_photos"]		= "Vise bilder?";
$pgv_lang["relatives_report_ext"]	= "Mange slektninger";
$pgv_lang["with"]				= "med";
$pgv_lang["on"]					= "den";			// for precise dates
$pgv_lang["in"]					= "i";			// for imprecise dates
$pgv_lang["individual_report"]	= "Person";
$pgv_lang["enter_pid"]			= "Angi PersonID";
$pgv_lang["individual_list_report"]	= "Liste over personer";
$pgv_lang["generated_by"]		= "Laget av";
$pgv_lang["list_children"]		= "(Sortert etter alder)";
$pgv_lang["birth_report"]		= "Fødselsdato og -sted";
$pgv_lang["birthplace"]			= "Fødested inneholder ";
$pgv_lang["birthdate1"]			= "Vis fødte <b>fra</b> dato ";
$pgv_lang["birthdate2"]			= "Vis fødte <b>til</b> dato ";
$pgv_lang["death_report"]		= "Dødsdato og -sted";
$pgv_lang["deathplace"]			= "Dødssted inneholder ";
$pgv_lang["deathdate1"]			= "Vis døde <b>fra</b> dato";
$pgv_lang["deathdate2"]			= "Vis døde <b>til</b> dato ";
$pgv_lang["marr_report"]		= "Ekteskapdato og -sted";
$pgv_lang["marrplace"]			= "Ekteskapsted inneholder ";
$pgv_lang["marrdate1"]			= "Vis ekteskap <b>fra</b> dato ";
$pgv_lang["marrdate2"]			= "Vis ekteskap <b>til</b> dato ";
$pgv_lang["sort_by"]			= "Sortert på ";

$pgv_lang["cleanup"]			= "Rydd";
$pgv_lang["skip_cleanup"]		= "Ikke rydd...!?";

//-- CONFIGURE (extra) messages for the programs patriarch, slklist and statistics
$pgv_lang["dynasty_list"]		= "Stamfedre";
$pgv_lang["make_slklist"]		= "Lag EXCEL (*.slk) regneark";
$pgv_lang["excel_list"]			= "Lagre følgende filer i EXCEL (slk-format) (bruk først listen stamfedre):";
$pgv_lang["excel_tab"]			= "Arkfanen:";
$pgv_lang["excel_create"]		= " vil bli lagd i filen:";
$pgv_lang["patriarch_list"]		= "Stamfedre";
$pgv_lang["slk_list"]			= "EXCEL (*.slk) regneark";
$pgv_lang["statistics"]			= "Statistikk";

//-- Merge Records
$pgv_lang["merge_records"]		= "Flette data (dobbelregisterte)";
$pgv_lang["merge_same"]			= "Dataene er ikke av samme type.  Kan ikke flette data som er av forskjellig type!";
$pgv_lang["merge_step1"]		= "Flettesteg 1 av 3";
$pgv_lang["merge_step2"]		= "Flettesteg 2 av 3";
$pgv_lang["merge_step3"]		= "Flettesteg 3 av 3";
$pgv_lang["select_gedcom_records"]	= "Velg 2 oppføringer i slektsbasen som skal flettes.  Oppføringene må være av samme type.";
$pgv_lang["merge_to"]			= "Flett til ID:";
$pgv_lang["merge_from"]			= "Flett fra ID:";
$pgv_lang["merge_facts_same"]	= "Følgende fakta /opplysninger er nøyaktig like i begge oppføringer og vil bli flettet automatisk";
$pgv_lang["no_matches_found"]	= "Fant ingen like fakta-felt";
$pgv_lang["unmatching_facts"]	= "Følgende faktafelt har forskjellig innhold.  Velg de opplysningene du ønsker å beholde.";
$pgv_lang["record"]				= "Oppføring";
$pgv_lang["adding"]				= "Legger til";
$pgv_lang["updating_linked"]	= "Oppdaterer data som er knyttet til denne";
$pgv_lang["merge_more"]			= "Flette flere oppføringer.";
$pgv_lang["same_ids"]			= "Du oppgav to like IDer.  Du kan ikke flette en oppføring med seg selv.";

//-- ANCESTRY FILE MESSAGES
$pgv_lang["ancestry_chart"]		= "Forfedre";
$pgv_lang["gen_ancestry_chart"]	= "Forfedre - #PEDIGREE_GENERATIONS# slektsledd";
$pgv_lang["chart_style"]		= "Utforming";
$pgv_lang["chart_list"]			= "Forfedre";
$pgv_lang["chart_booklet"]   	= "Hefte";
$pgv_lang["show_cousins"]		= "Vis søskenbarn";

//-- FAN CHART
$pgv_lang["compact_chart"]		= "Kompakt";
$pgv_lang["fan_chart"]			= "Sirkel";
$pgv_lang["gen_fan_chart"]		= "Sirkeldiagram - #PEDIGREE_GENERATIONS# slektsledd";
$pgv_lang["fan_width"]			= "Bredde";
$pgv_lang["gd_library"]			= "Ugyldig konfigurasjon av PHP server: Biblioteket GD 2.x er nødvendig for bilde-funksjonen.";
$pgv_lang["gd_freetype"]		= "Ugyldig konfigurasjon av PHP server: Biblioteket Freetype er nødvendig for TrueType skrifttyper.";
$pgv_lang["gd_helplink"]		= "http://www.php.net/gd";
$pgv_lang["fontfile_error"]		= "Fant ikke nødvendige filer med skrifttyper på PHP serveren";
$pgv_lang["fanchart_IE"]		= "Dette slekts-hjulet kan ikke bli skrevet ut direkte fra din nettleser. Bruk høyre-klikk og velg så Lagre bilde. Så må du åpne bilde i et annet program for så å skrive det ut derfra.";

//-- RSS Feed
$pgv_lang["rss_descr"]			= "Nyheter og lenker fra nettstedet #GEDCOM_TITLE#";
$pgv_lang["rss_logo_descr"]		= "Oppføringen er laget av PhpGedView #VERSION#";
$pgv_lang["rss_feeds"]			= "RSS lenker";
$pgv_lang["no_feed_title"]		= "Ingen lenker tilgjengelig";
$pgv_lang["no_feed"]			= "Det er ingen RSS-lenker tilgjengelig på dette PhpGedView-nettstedet";
$pgv_lang["feed_login"]			= "Dersom du har en konto på dette PhpGedView-nettstedet, kan du <a href=\"#AUTH_URL#\">logge deg inn</a> på tjeneren som bruker enkel HTTP-autorisering for å se private opplysninger.";

//-- ASSOciates RELAtionship
// After any change in the following list, please check $assokeys in edit_interface.php
$pgv_lang["attendant"] 			= "Deltagere";
$pgv_lang["attending"] 			= "Observatør";
$pgv_lang["best_man"] 			= "Forlover til brudgomen";
$pgv_lang["bridesmaid"] 		= "Forlover til bruden";
$pgv_lang["buyer"] 				= "Innkjøper";
$pgv_lang["circumciser"]		= "Omskjærer";
$pgv_lang["civil_registrar"] 	= "Sorenskriver";
$pgv_lang["friend"] 			= "Venn";
$pgv_lang["godfather"] 			= "Gudfar";
$pgv_lang["godmother"] 			= "Gudmor";
$pgv_lang["godparent"] 			= "Gudforeldre";
$pgv_lang["informant"] 			= "Informant";
$pgv_lang["lodger"] 			= "Leietaker";
$pgv_lang["nurse"] 				= "Pleier";
$pgv_lang["priest"]				= "Prest";
$pgv_lang["rabbi"] 				= "Rabbi";
$pgv_lang["registry_officer"] 	= "Registerfører";
$pgv_lang["seller"] 			= "Selger";
$pgv_lang["servant"] 			= "Tjener";
$pgv_lang["twin"] 				= "Tvilling";
$pgv_lang["twin_brother"] 		= "Tvillingbror";
$pgv_lang["twin_sister"] 		= "Tvillingsøster";
$pgv_lang["witness"] 			= "Vitne";

//-- statistics utility
$pgv_lang["statutci"]			= "klarte ikke å lage indeks";
$pgv_lang["statnnames"]         = "antall navn     =";
$pgv_lang["statnfam"]           = "antall familier =";
$pgv_lang["statnmale"]          = "antall menn     =";
$pgv_lang["statnfemale"]        = "antall kvinner  =";
$pgv_lang["statvars"]			= "Fyll inn følgende variabler for diagrammet";
$pgv_lang["statlxa"]			= "langs x-aksen:";
$pgv_lang["statlya"]			= "langs y-aksen:";
$pgv_lang["statlza"]			= "langs z-aksen";
$pgv_lang["stat_10_none"]		= "ingen";
$pgv_lang["stat_11_mb"]			= "Fødselsmåned";
$pgv_lang["stat_12_md"]			= "Dødsmåned";
$pgv_lang["stat_13_mm"]			= "Ekteskapsmåned";
$pgv_lang["stat_14_mb1"]		= "Fødselsmåned for førstefødte i familie";
$pgv_lang["stat_15_mm1"]		= "Måned for første ekteskap";
$pgv_lang["stat_16_mmb"]		= "Måneder mellom ekteskap og første barn.";
$pgv_lang["stat_17_arb"]		= "alder i forhold til fødselsår.";
$pgv_lang["stat_18_ard"]		= "alder i forhold til dødsår.";
$pgv_lang["stat_19_arm"]		= "alder i forhold til vigselår.";
$pgv_lang["stat_20_arm1"]		= "alder ved første ekteskap.";
$pgv_lang["stat_21_nok"]		= "antall barn.";
$pgv_lang["stat_gmx"]			= " sjekk akse-verdier for måned";
$pgv_lang["stat_gax"]			= " sjekk akse-verdier for alder";
$pgv_lang["stat_gnx"]			= " sjekk akse-verdier for antall";
$pgv_lang["stat_200_none"]		= "alle (eller tom)";
$pgv_lang["stat_201_num"]		= "antall";
$pgv_lang["stat_202_perc"]		= "prosent";
$pgv_lang["stat_300_none"]		= "ingen";
$pgv_lang["stat_301_mf"]		= "mann/kvinne";
$pgv_lang["stat_302_cgp"]		= "perioder. Sjekk akse-verdier (z-akse)";
$pgv_lang["statmess1"]			= "<b>Bare fyll ut neste rader i forhold til tidligere verdier for x-akse eller z-akse</b>";
$pgv_lang["statar_xgp"]			= "akse-verdier for perioder (x-axis):";
$pgv_lang["statar_xgl"]			= "akse-verdier for alder    (x-axis):";
$pgv_lang["statar_xgm"]			= "akse-verdier for måned    (x-axis):";
$pgv_lang["statar_xga"]			= "akse-verdier for antall   (x-axis):";
$pgv_lang["statar_zgp"]			= "akse-verdier for perioder (z-axis):";
$pgv_lang["statreset"]			= "Nullstill";
$pgv_lang["statsubmit"]			= "Vis diagram";

//-- statisticsplot utility
$pgv_lang["statistiek_list"]	= "Statistisk graf";
$pgv_lang["stpl"]			 	= "...";
$pgv_lang["stplGDno"]			= "Grafisk visningsbibliotek er ikke tilgjengelig i PHP 4. Vennligst kontakt verten for nettstedet ditt";
$pgv_lang["stpljpgraphno"]		= "JPgraph moduler finnes ikke i katalogen <i>phpgedview/jpgraph/</i>.  Vennligst hent dem hos http://www.aditus.nu/jpgraph/jpdownload.php<br> <h3>Installer først JPgraph i katalogen <i>phpgedview/jpgraph/</i></h3><br>";
$pgv_lang["stplinfo"]			= "diagram-informasjon:";
$pgv_lang["stpltype"]			= "type:";
$pgv_lang["stplnoim"]			= " ikke tilgjengelig:";
$pgv_lang["stplmf"]			 	= " / mann-kvinne";
$pgv_lang["stplipot"]			= " / per tidsperiode";
$pgv_lang["stplgzas"]			= "rammer z-akse:";
$pgv_lang["stplmonth"]			= "måned";
$pgv_lang["stplnumbers"]		= "antall for en familie";
$pgv_lang["stplage"]			= "alder";
$pgv_lang["stplperc"]			= "prosent";
$pgv_lang["stplnumof"]			= "Antall ";
$pgv_lang["stplmarrbirth"]		= "Måneder mellom ekteskap og fødselsdato til første barn";

//-- alive in year
$pgv_lang["alive_in_year"]		= "Levde i året...";
$pgv_lang["is_alive_in"]		= "<i>Levde i året&nbsp;&nbsp;#YEAR#</i>";
$pgv_lang["alive"]				= "Levende ";
$pgv_lang["dead"]				= "Død ";
$pgv_lang["maybe"]				= "Kanskje ";

//-- Help system
$pgv_lang["definitions"]		= "Definisjoner";

//-- Index_edit
$pgv_lang["description"]		= "Beskrivelse";
$pgv_lang["block_desc"]			= "Beskrivelse av rammer";
$pgv_lang["click_here"]			= "Klikk her for å fortsette";
$pgv_lang["click_here_help"]	= "~#pgv_lang[click_here]#~<br /><br />Klikk på denne knappen for godkjenne endringer du har lagret tidligere.";
$pgv_lang["block_summaries"]	= "~#pgv_lang[block_desc]#~<br /><br />Her er en kort beskrivelse for hver av de rammene du har valgt for sidene #pgv_lang[welcome]# eller #pgv_lang[mygedview]#.<br /><table border='1' align='center'><tr><td class='list_value'><b>#pgv_lang[name]#</b></td><td class='list_value'><b>#pgv_lang[description]#</b></td></tr>#pgv_lang[block_summary_table]#</table><br /><br />";
$pgv_lang["block_summary_table"]	= "&nbsp;";		// Built in index_edit.php

//-- Find page
$pgv_lang["total_places"]		= "steder funnet";
$pgv_lang["media_contains"]		= "Media inneholder:";
$pgv_lang["repo_contains"]		= "Oppbevaringsstedet inneholder:";
$pgv_lang["source_contains"]	= "Kilde inneholder:";
$pgv_lang["display_all"]		= "Vis alle";

//-- accesskey navigation
$pgv_lang["accesskeys"]			= "Hurtigtaster";
$pgv_lang["accesskey_skip_to_content_desc"]	= "Gå til innhold";
$pgv_lang["accesskey_viewing_advice_desc"]	= "Visningsråd";

// FAQ Page
$pgv_lang["add_faq_header"] = "Tittel spørsmål & svar";
$pgv_lang["add_faq_body"] = "Tekst spørsmål & svar";
$pgv_lang["add_faq_order"] = "Plassering spørsmål & svar";
$pgv_lang["no_faq_items"] = "Listen med spørsmål & svar er tom.";
$pgv_lang["position_item"] = "Plassering";
$pgv_lang["faq_list"] = "Spørsmål & svar";
$pgv_lang["confirm_faq_delete"] = "Er du sikker på at du vil slette dette spørsmål & svar";
$pgv_lang["preview"] =  "Forhåndsvis";
$pgv_lang["no_id"] = "Ingen spørsmål & svar-ID er angitt !";

// Help search
$pgv_lang["hs_title"] 			= "Søk i hjelpetekst";
$pgv_lang["hs_search"] 			= "Søk";
$pgv_lang["hs_close"] 			= "Lukk vindu";
$pgv_lang["hs_results"] 		= "Resultat funnet:";
$pgv_lang["hs_keyword"] 		= "Søk etter";
$pgv_lang["hs_searchin"]		= "Søk i";
$pgv_lang["hs_searchuser"]		= "Brukerhjelp";
$pgv_lang["hs_searchconfig"]	= "Administrator-hjelp";
$pgv_lang["hs_searchhow"]		= "Søketype";
$pgv_lang["hs_searchall"]		= "Alle ord";
$pgv_lang["hs_searchany"]		= "Vilkårlige ord";
$pgv_lang["hs_searchsentence"]	= "Nøyaktig";
$pgv_lang["hs_intruehelp"]		= "Bare i hjelpetekst";
$pgv_lang["hs_inallhelp"]		= "All tekst";

// Media import
$pgv_lang["media_import"] = "Importer og konverter media";
$pgv_lang["confirm_folder_delete"] = "Er du sikker på at du vil slette denne mappen?";
$pgv_lang["choose"] = "Velg: ";
$pgv_lang["account_information"] = "Konto-informasjon";

//-- Media item "TYPE" sub-field
$pgv_lang["TYPE__audio"] = "Lyd";
$pgv_lang["TYPE__book"] = "Bok";
$pgv_lang["TYPE__card"] = "Kort";
$pgv_lang["TYPE__certificate"] = "Sertifikat";
$pgv_lang["TYPE__document"] = "Dokument";
$pgv_lang["TYPE__electronic"] = "Elektronik";
$pgv_lang["TYPE__fiche"] = "Mikrofil";
$pgv_lang["TYPE__film"] = "Mikrofilm";
$pgv_lang["TYPE__magazine"] = "Magasin";
$pgv_lang["TYPE__manuscript"] = "Manuskript";
$pgv_lang["TYPE__map"] = "Kart";
$pgv_lang["TYPE__newspaper"] = "Avis";
$pgv_lang["TYPE__photo"] = "Bilde";
$pgv_lang["TYPE__tombstone"] = "Gravstein";
$pgv_lang["TYPE__video"] = "Video";

if (file_exists( "languages/lang.no.extra.php")) require  "languages/lang.no.extra.php";

?>
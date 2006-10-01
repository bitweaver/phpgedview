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
 * @author Matti Valve
 * @version $Id$
 */
if (preg_match("/configure_help\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

//-- CONFIGURE FILE MESSAGES
$pgv_lang["can_admin"]			= "Käyttäjä voi ylläpitä";
$pgv_lang["can_edit"]			= "Käyttäjä voi muokata";

$pgv_lang["config_help"]		= "Configuration help";

$pgv_lang["add_user"]			= "Lisää uusi käyttäjä";
$pgv_lang["current_users"]		= "Nykyinen käyttäjälista";
$pgv_lang["leave_blank"]		= "Jätä salasana tyhjäksi jos et halua muuttaa sitä.";
$pgv_lang["messaging2"]			= "Sisäiset viestit ja sähköposti";
$pgv_lang["messaging3"]			= "PhpGedView lähettää sähköposteja ilman säilytystä";
$pgv_lang["no_messaging"]		= "Ei mitään yhteystapaa";
$pgv_lang["privileges"]			= "Etuoikeudet";
$pgv_lang["date_registered"]	= "Rekisteröintipäivä";
$pgv_lang["last_login"]			= "Viimeksi kirjautunut";
$pgv_lang["show_phpinfo"]		= "Näytä PHPInfosivu";

//-- edit privacy messages

//-- language edit utility
$pgv_lang["enable_disable_lang"]	= "Configure supported languages";
$pgv_lang["translator_tools"]	= "Translator tools";
$pgv_lang["add_new_language"]		= "Add files and settings for a new language";

$pgv_lang["lang_edit_help"]		= "~Tällä sivulla ylläpidetään kielitiedostoja~<br />Voit kääntää, verrata ja viedä kielitiedostoja.<br />Lisäksi voit tehdä asetuksia ohjelman tukemiin kieliin.<br /><br />Voit käyttää seuraavia vaihtoehtoja ja työkaluja:";
$pgv_lang["edit_langdiff"]		= "Editoi ja konfiguroi kielitiedostoja";
$pgv_lang["lang_back_admin"]	= "Palaa ylläpitovalikkoon";
$pgv_lang["language_to_edit_help"]		= "#pgv_lang[edit_lang_utility]# >> <b>#pgv_lang[language_to_edit]#</b><br /><br /> Tästä pudotusvalikosta voit valita kielen, jolla haluat <b>editoida</b> viestejä.";
$pgv_lang["file_to_edit_help"]			= "#pgv_lang[edit_lang_utility]# >> <b>#pgv_lang[file_to_edit]#</b><br /><br />Tästä pudotusvalikosta voit valita minkä tyyppistä kielitiedostoa haluat editoida.<br /><br />Vaihtoehdot ovat:<br />lang.xx.php<br />facts.xx.php<br />configure_help.xx.php<br />help_text.xx.php<br /><br />, missä xx vastaa kielikoodia ja asettuu automaattisesti.<br />";
$pgv_lang["language_to_export_help"]	= "#pgv_lang[export_lang_utility]# >> <b>#pgv_lang[language_to_export]#</b><br /><br />Tästä pudotusvalikosta voit valita sen kielen, jonka viestit haluat <b>viedä</b>.";
$pgv_lang["new_language_help"]			= "#pgv_lang[compare_lang_utility]# >> <b>#pgv_lang[new_language]#</b><br /><br />Tästä pudotusvalikosta voit valita sen kielen, jonka haluat lähteeksi verrataksesi sitä toiseen kieleen.<br /><br />Kaikki muutokset ja lisäykset tehdään ensin <b>englanninkieliseen</b> kielitiedostoon.";
$pgv_lang["old_language_help"]			= "#pgv_lang[compare_lang_utility]# >> <b>#pgv_lang[old_language]#</b><br /><br />Tästä pudotusvalikosta voit valita kielen jota haluat verrata <b>lähteen</b> pudotusvalikosta valittuun kieleen.<br /><br />Kun ole tehny valinnan, näpäytä <b>vertaa</b> painiketta ja saat luettelon kaikista lisäyksistä ja poistoista.<br /><br />Varmuudeksi:<br /><b>lisäys</b> tarkoittaa: se <b>on jo olemassa</b> lähdetiedostossa mutta <b>ei</b> vertailutiedostossa.<br /><br /><b>Poistaminen</b> tarkoittaa: se <b>ei</b> ole enää lähdetiedostossa, mutta <b>on</b> (vielä) vertailutiedostossa.";
$pgv_lang["system_time"]		= "Nykyinen järjestelmäaika:";
$pgv_lang["hide_translated_help"]		= "#pgv_lang[edit_lang_utility]# >> <b>#pgv_lang[hide_translated]#</b><br /><br /> Mikäli vastaat kyllä, näkyvät vain ne valitsemasi kielen viestit joita ei ole käännetty eli joita ei vielä ole valitsemassasi kielitiedostossa.<br />Kun viesti on käännetty tätä ei enää näytetä luettelossa.";
$pgv_lang["never"]				= "Ei koskaan";

//$pgv_lang["add_new_lang_help"]			= "<b>#pgv_lang[add_new_language]#</b><br /><br />Tällä valinnalla voit lisätä uuden kielen PhpGedView-ohjelmaan.<br />Voit tehdä uuden kielen standardiasetukset kuten: kielikoodi, kielen suunta, viikon alkamispäivä tälle kielelle, aika-asetukset, aakkoset jne.<br /><br />Lisäohjeita PhpGedView-ohjelman tukemista kielen asetuksista saat valittuasi kielen ja näpäytettyäsi \" Lisää uusi kieli\" painiketta.<br />Uuden kielen asetusikkunassa on lisää kysymysmerkkejä, joita näpäyttämällä saat lisäohjeita.";
//$pgv_lang["lang_configure_help"]	= "Tällä sivulla valitaan käyttäjien käytössä olevat kielet. Asetukset voidaan tehdä esimerkiksi niin, että vain saksa ja suomi ovat käytettävissä. Tämä voi olla hyödyllistä mikäli et pysty kommunikoimaan käyttäjien kanssa esimerkiksi unkariksi.<br /><br />Tällä sivulla voit myös muuttaa tiettyjä PhpGedView-ohjelman ominaisuuksia, jotka riipuvat valitusta kielestä. Täällä voit määrittää esimerkiksi kuinka PhpGedView muotoilee päivämäärä- ja aikakentät.";

//-- User Migration Tool messages
$pgv_lang["um_header"] = "User Information Migration tool";
$pgv_lang["um_backup"] = "Backup";
$pgv_lang["COMMIT_COMMAND_help"] 			= "~#pgv_lang[COMMIT_COMMAND]#~<br /><br />Mikäli haluat käyttää versiontarkistusjärjestelmää kuten CVS arkistoidaksesi muutokset GEDCOM-tiedostossasi, asetuksissasi tai yksityisyysaksetuksissasi, kirjoita komento tähän. Jätä lokero tyhjäksi mikäli et halua käyttää versiontarkistusjärjestelmää. Hyväksyttävät vaihtoehdot ovat <b>cvs</b> ja <b>svn</b>.<br />";

?>
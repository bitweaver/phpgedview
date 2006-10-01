<?php
/**
 * Czech Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Jan Hapala
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
 * @author Jan Hapala
 * @version $Id$
 */
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "Nemáte přímý přístup k souboru s češtinou.";
	exit;
}

//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]				= "?";
$pgv_lang["qm_ah"]			= "?";
$pgv_lang["page_help"]			= "Nápověda";
$pgv_lang["help_for_this_page"]		= "Nápověda k této stránce";
$pgv_lang["help_contents"]		= "Obsah nápovědy";
$pgv_lang["show_context_help"]		= "Zobrazit kontextovou nápovědu";
$pgv_lang["hide_context_help"]		= "Skrýt kontextovou nápovědu";
$pgv_lang["sorry"]			= "<b>Nápověda k této stránce či položce ještě bohužel nebyla dokončena</b>";
$pgv_lang["help_not_exist"]		= "<b>Nápověda k této stránce či položce zatím není k dispozici</b>";
$pgv_lang["resolution"]			= "Rozlišení obrazovky";
$pgv_lang["menu"]			= "Menu";
$pgv_lang["header"]			= "Záhlaví";
$pgv_lang["imageview"]			= "Prohlížeč obrázků";
$pgv_lang["login_head"]			= "Přihlášení uživatele PhpGedView";

//-- CONFIG FILE MESSAGES
$pgv_lang["error_title"]		= "CHYBA: Není možné otevřít soubor GEDCOM";
$pgv_lang["error_header"] 		= "Soubor GEDCOM, [#GEDCOM#], není na zadaném místě.";
$pgv_lang["error_header_write"]	= "Do souboru GEDCOM [#GEDCOM#] nelze zapisovat. Zkontrolujte vlastnosti a přístupová práva souboru.";
$pgv_lang["for_support"]		= "Pro technickou podporu a další informace kontaktujte";
$pgv_lang["for_contact"]		= "S dotazy k rodokmenu se obracejte na";
$pgv_lang["for_all_contact"]		= "Pro technickou podporu nebo s dotazy k rodokmenu prosím kontaktujte";
$pgv_lang["build_title"]		= "Vytvoření Index souborů";
$pgv_lang["build_error"]		= "Soubor GEDCOM byl aktualizován.";
$pgv_lang["please_wait"]		= "Chvilku strpení. Probíhá přepisování rejstříkových souborů.";
$pgv_lang["choose_gedcom"]		= "Vybrat GEDCOM databázi";
$pgv_lang["username"]			= "Uživatelské jméno";
$pgv_lang["invalid_username"]		= "Uživatelské jméno obsahuje neplatné znaky";
$pgv_lang["fullname"]			= "Celé jméno";
$pgv_lang["password"]			= "Heslo";
$pgv_lang["confirm"]			= "Potvrzení hesla";
$pgv_lang["user_contact_method"]	= "Upřednostňovaný způsob kontaktu";
$pgv_lang["login"]			= "Přihlásit se";
$pgv_lang["login_aut"]			= "Upravit uživatele";
$pgv_lang["logout"]			= "Odhlásit se";
$pgv_lang["admin"]			= "Admin";
$pgv_lang["logged_in_as"]		= "Přihlášen jako ";
$pgv_lang["my_pedigree"]		= "Můj vývod";
$pgv_lang["my_indi"]			= "Můj osobní záznam";
$pgv_lang["yes"]			= "Ano";
$pgv_lang["no"]				= "Ne";
$pgv_lang["add_gedcom"]			= "Přidat GEDCOM";
$pgv_lang["add_gedcom"]			= "Přidat další GEDCOM";
$pgv_lang["no_support"]			= "Zjistili jsme, že váš prohlížeč nepodporuje standardy užívané PhpGedView. Většina prohlížečů ve svých novějších verzích tyto standardy podporuje. Prosím aktualizujte svůj prohlížeč na novější verzi.";
$pgv_lang["change_theme"]		= "Změna motivu";
$pgv_lang["gedcom_downloadable"] 	= "Tento GEDCOM může být stažen po internetu!<br />Prosím přečtěte si odstavec o BEZPEČNOSTI v souboru <a href=\"readme.txt\">readme.txt</a> a zjednejte nápravu";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]	= "Vývod";
$pgv_lang["gen_ped_chart"]	= "#PEDIGREE_GENERATIONS# - generační schéma";
$pgv_lang["generations"]	= "Počet generací";
$pgv_lang["view"]		= "Zobrazit";
$pgv_lang["fam_spouse"]		= "Rodina s partnerem";
$pgv_lang["root_person"]	= "ID střena";
$pgv_lang["hide_details"]	= "Skrýt podrobnosti";
$pgv_lang["show_details"]	= "Ukázat podrobnosti";
$pgv_lang["person_links"]	= "Odkazy na schémata, rodiny a blízké příbuzné této osoby. Klikněte na tuto ikonu pro zvolené zobrazení rodokmenu s touto osobou jako střenem.";
$pgv_lang["zoom_box"]		= "Zvětšení/zmenšení tohoto rámečku.";
$pgv_lang["portrait"]		= "Na výšku";
$pgv_lang["landscape"]		= "Na šířku";
$pgv_lang["start_at_parents"]	= "Začít u rodičů";
$pgv_lang["charts"]		= "Schémata";
$pgv_lang["lists"]		= "Seznamy";
$pgv_lang["welcome_page"]	= "Úvodní stránka";
$pgv_lang["max_generation"]		= "Maximální počet generací  je #PEDIGREE_GENERATIONS#.";
$pgv_lang["min_generation"]		= "Minimální počet generací je 3.";
$pgv_lang["box_width"] 				= "Šířka rámečku";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]	= "Není možné nalézt rodinu s id";
$pgv_lang["unable_to_find_indi"]	= "Není možné nalézt osobu s id";
$pgv_lang["unable_to_find_record"]	= "Není možné nalézt záznam s id";
$pgv_lang["unable_to_find_source"]	= "Není možné nalézt zdroj s id";
$pgv_lang["unable_to_find_repo"]	= "Není možné nalézt pramen s id";
$pgv_lang["repo_name"]			= "Název pramene:";
$pgv_lang["address"]			= "Adresa:";
$pgv_lang["phone"]			= "Telefon:";
$pgv_lang["source_name"]		= "Název zdroje:";
$pgv_lang["title"]			= "Nadpis";
$pgv_lang["author"]			= "Autor:";
$pgv_lang["publication"]		= "Publikace:";
$pgv_lang["call_number"]		= "Telefonní číslo:";
$pgv_lang["living"]			= "Žijící osoba";
$pgv_lang["private"]			= "Soukromé";
$pgv_lang["birth"]			= "Narození:";
$pgv_lang["death"]			= "Úmrtí:";
$pgv_lang["descend_chart"]		= "Rozrod";
$pgv_lang["individual_list"]		= "Seznam osob";
$pgv_lang["family_list"]		= "Seznam rodin";
$pgv_lang["source_list"]		= "Seznam pramenů";
$pgv_lang["place_list"]			= "Hierarchie míst";
$pgv_lang["place_list_aft"] 		= "Hierarchie míst podle";
$pgv_lang["media_list"]			= "Seznam multimédií";
$pgv_lang["search"]			= "Hledat";
$pgv_lang["clippings_cart"]		= "Schránka výstřižků";
$pgv_lang["not_an_array"]		= "Toto není pole";
$pgv_lang["print_preview"]		= "Tiskový režim";
$pgv_lang["cancel_preview"]		= "Zpět do normálního zobrazení";
$pgv_lang["change_lang"]		= "Změnit jazyk";
$pgv_lang["print"]			= "Vytisknout";
$pgv_lang["total_queries"]		= "Celkový počet dotazů na databázi: ";
$pgv_lang["total_privacy_checks"]	= "Úplných kontrol privátnosti celkem:";
$pgv_lang["back"]			= "Zpět";
$pgv_lang["privacy_list_indi_error"]	= "Kvůli nastavení privátnosti je jedna či více osob skrytých.";
$pgv_lang["privacy_list_fam_error"]	= "Kvůli nastavení privátnosti je jedna či více osob skrytých.";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["aka"]			= "alias";
$pgv_lang["male"]			= "Muž";
$pgv_lang["female"]			= "Žena";
$pgv_lang["temple"]			= "Chrám LDS (Temple)";
$pgv_lang["temple_code"]		= "Kód LDS Chrámu (Temple):";
$pgv_lang["status"]			= "Stav";
$pgv_lang["source"]			= "Pramen";
$pgv_lang["citation"]			= "Citace:";
$pgv_lang["text"]			= "Zdrojový text:";
$pgv_lang["note"]			= "Poznámka";
$pgv_lang["NN"]			= "(neznámé)";
$pgv_lang["PN"]			= "(neznámé)";
$pgv_lang["unrecognized_code"]		= "Nebyl rozpoznán GEDCOM kód";
$pgv_lang["unrecognized_code_msg"]	= "Došlo k chybě. Rádi bychom ji opravili. Prosím informujte nás o tomto problému na ";
$pgv_lang["indi_info"]			= "Informace o osobě";
$pgv_lang["pedigree_chart"]		= "Vývod";
$pgv_lang["desc_chart2"]		= "Rozrod";
$pgv_lang["family"]			= "Rodina";
$pgv_lang["family_with"]		= "Rodina s";
$pgv_lang["as_spouse"]			= "Rodina s partnerem";
$pgv_lang["as_child"]			= "Rodina s rodiči";
$pgv_lang["view_gedcom"]		= "Zobrazit GEDCOM záznam";
$pgv_lang["add_to_cart"]		= "Přidat do schránky výstřižků";
$pgv_lang["still_living_error"]		= "tato osoba ještě žije u ní nejsou zaznamenána data narození a úmrtí. Všechny podrobnosti o žijících osobách jsou skryta.<br />Pro další informace kontaktujte";
$pgv_lang["privacy_error"]		= "Podrobnosti o této osobě jsou soukromé.<br />";
$pgv_lang["more_information"]		= "Pro další informace kontaktujte";
$pgv_lang["name"]			= "Jméno";
$pgv_lang["given_name"]			= "Křestní jméno:";
$pgv_lang["surname"]			= "Příjmení:";
$pgv_lang["suffix"]			= "Suffix:";
$pgv_lang["object_note"]		= "Poznámka k objektu:";
$pgv_lang["sex"]			= "Pohlaví";
$pgv_lang["personal_facts"]		= "Osobní údaje a podrobnosti";
$pgv_lang["type"]			= "Typ";
$pgv_lang["date"]			= "Datum";
$pgv_lang["place_description"]		= "Místo / Popis";
$pgv_lang["parents"] 			= "Rodiče:";
$pgv_lang["siblings"] 			= "Sourozenci";
$pgv_lang["father"] 			= "Otec";
$pgv_lang["mother"] 			= "Matka";
$pgv_lang["relatives"]			= "Blízcí příbuzní";
$pgv_lang["child"]			= "Dítě";
$pgv_lang["spouse"]			= "Partner";
$pgv_lang["surnames"]			= "Příjmení";
$pgv_lang["adopted"]			= "Adoptován(a)";
$pgv_lang["foster"]			= "Pěstoun";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["sealing"]			= "nepřeloženo!!!: Sealing";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["link_as"]			= "Připojit tuto osobu k existující rodině jako ";
$pgv_lang["no_tab1"]				= "K této osobě nejsou dostupné žádné údaje.";
$pgv_lang["no_tab2"]				= "K této osobě nejsou žádné poznámky.";
$pgv_lang["no_tab3"]				= "K této osobě nejsou žádné citace pramenů.";
$pgv_lang["no_tab4"]				= "K této osobě nejsou k dispozici žádné obrázky.";
$pgv_lang["no_tab5"]				= "K této osobě nejsou dostupní žádní blízcí příbuzní.";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]		= "Informace o rodině";
$pgv_lang["family_group_info"]		= "Informace o rodině (manželství)";
$pgv_lang["husband"]			= "Manžel";
$pgv_lang["wife"]			= "Manželka";
$pgv_lang["marriage"]			= "Sňatek:";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["lds_sealing"]		= "LDS Sealing:";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["marriage_license"]		= "Svatební smlouva:";
$pgv_lang["media_object"]		= "Multimediální soubor:";
$pgv_lang["children"]			= "Děti";
$pgv_lang["no_children"]		= "Žádné zaznamenané děti";
$pgv_lang["parents_timeline"]		= "Ukázat pár na<br />časové ose";

//-- CLIPPINGS FILE MESSAGES

$pgv_lang["clip_cart"]			= "Schránka výstřižků";
$pgv_lang["clip_explaination"]		= "Schránka výstřižků vám umožňuje pořídit z tohoto rodokmenu &quot;výstřižky&quot; a uložit je do samostatného GEDCOM souboru ke stáhnutí.<br /><br />";
$pgv_lang["item_with_id"]		= "Položka s id";
$pgv_lang["error_already"]		= "již je mezi výstřižky.";
$pgv_lang["which_links"]		= "Které vztahy z této rodiny byste ještě rádi přidali?";
$pgv_lang["just_family"]		= "Přidat jen tento rodinný záznam.";
$pgv_lang["parents_and_family"]		= "Přidat rodiče s tímto rodinným záznamem.";
$pgv_lang["parents_and_child"]		= "Přidat záznamy rodičů a dětí s tímto rodinným záznamem.";
$pgv_lang["parents_desc"]		= "Přidat všechny záznamy rodičů a všech potomků s tímto rodinným záznamem.";
$pgv_lang["continue"]			= "Pokračovat v přidávání";
$pgv_lang["which_p_links"]		= "Které vztahy této osoby byste chtěli také přidat?";
$pgv_lang["just_person"]		= "Přidat jen tuto osobu.";
$pgv_lang["person_parents_sibs"]	= "Přidat tuto osobu, její rodiče a sourozence.";
$pgv_lang["person_ancestors"]		= "Přidat tuto osobu a její předky v přímé linii.";
$pgv_lang["person_ancestor_fams"]	= "Přidat tuto osobu, její předky v přímé linii a jejich rodiny.";
$pgv_lang["person_spouse"]		= "Přidat tuto osobu, jejího partnera a děti.";
$pgv_lang["person_desc"]		= "Přidat záznam o této osobě, jejím partnerovi a všech potomcích.";
$pgv_lang["unable_to_open"]		= "Nebylo možné složku s výstřižky otevřít pro zápis";
$pgv_lang["person_living"]		= "Toto je žijící osoba. Osobní údaje nebudou zahrnuty.";
$pgv_lang["person_private"]		= "Podrobnosti o této osobě jsou soukromé. Osobní údaje nebudou zahrnuty.";
$pgv_lang["family_private"]		= "Podrobnosti o této rodině jsou soukromé. Rodinné údaje nebudou zahrnuty.";
$pgv_lang["download"]			= "Pro stáhnutí souborů klikněte pravým tlačítkem (na Macu control-click) na odkazy dole a označte &quot;Uložit cíl jako&quot;.";
$pgv_lang["media_files"]		= "V tomto GEDCOM souboru jsou reference na soubory médií.";
$pgv_lang["cart_is_empty"]		= "Vaše Schránka výstřižků je prázdná.";
$pgv_lang["id"]				= "ID";
$pgv_lang["name_description"]		= "Jméno / Popis";
$pgv_lang["remove"]			= "Odstranit";
$pgv_lang["empty_cart"]			= "Vyprázdnit schránku";
$pgv_lang["download_now"]		= "Stáhnout";
$pgv_lang["indi_downloaded_from"]	= "Tato osoba byla stažena z:";
$pgv_lang["family_downloaded_from"]	= "Tato rodina byla stažena z:";
$pgv_lang["source_downloaded_from"]	= "Tento zdroj byl stažen z:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]			= "nalezených souvislostí v místech";
$pgv_lang["top_level"]				= "Horní úroveň";
$pgv_lang["form"]				= "Místa jsou zapsána ve tvaru: ";
$pgv_lang["default_form"]			= "město, kraj, stát, země";
$pgv_lang["default_form_info"]			= "(Default)";
$pgv_lang["gedcom_form_info"]			= "(GEDCOM)";
$pgv_lang["unknown"]				= "Neznám(a)";
$pgv_lang["individuals"]			= "Osoby";
$pgv_lang["view_records_in_place"]	= "Zobrazit všechny záznamy spojené s tímto místem";
$pgv_lang["place_list2"] 			= "Seznam míst";
$pgv_lang["show_place_hierarchy"]	= "Zobrazit místa hierarchicky";
$pgv_lang["show_place_list"]		= "Zobrazit všechna místa ze seznamu.";
$pgv_lang["total_unic_places"]		= "Unikátních míst celkem";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["multi_title"]			= "Seznam multimediálních souborů";
$pgv_lang["media_found"]			= "nalezených mediálních souborů.";
$pgv_lang["view_person"]			= "Zobrazit osobu";
$pgv_lang["view_family"]			= "Zobrazit rodinu";
$pgv_lang["view_source"]			= "Zobrazit zdroj";
$pgv_lang["prev"]				= "&lt; Předchozí";
$pgv_lang["next"]				= "Další &gt;";
$pgv_lang["file_not_found"]			= "Soubor nebyl nalezen.";
$pgv_lang["medialist_show"] 		= "Zobrazit";
$pgv_lang["per_page"]				= "medilních objektů na stránku";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]			= "Hledání v souboru GEDCOM";
$pgv_lang["enter_terms"]			= "Vložte výraz, který se má vyhledat:";
$pgv_lang["soundex_search"]			= "- Nebo zkuste zadat jméno tak, jak si myslíte, že se píše (metoda Soundex):";
$pgv_lang["sources"]				= "ZDROJE";
$pgv_lang["firstname_search"]			= "Jméno: ";
$pgv_lang["lastname_search"]			= "Příjmení: ";
$pgv_lang["search_place"]			= "Místo: ";
$pgv_lang["search_year"]			= "Rok: ";
$pgv_lang["no_results"]				= "Nenalezen odpovídající záznam.";
$pgv_lang["invalid_search_input"] 	= "Prosím zadejte k roku ještě jméno, příjmení nebo místo.";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]			= "Zdroje nalezeny";
$pgv_lang["titles_found"]			= "Tituly";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]			= "Informace o zdroji";
$pgv_lang["other_records"]			= "Záznamy, které se odkazují na tento pramen:";
$pgv_lang["people"]				= "Lidé";
$pgv_lang["families"]				= "Rodiny";
$pgv_lang["total_sources"]			= "Pramenů celkem";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]			= "Vytváření rejstříků osob a rodin";
$pgv_lang["building_index"]			= "Vytváření seznamů rejstříků";
$pgv_lang["invalid_gedformat"]	= "Neplatný formát (neodpovídá standardu GEDCOM 5.5)";
$pgv_lang["importing_records"]			= "Importování záznamů do databáze";
$pgv_lang["detected_change"]			= "PhpGedView zaznamenal změnu v GEDCOM souboru #GEDCOM#. Soubory rejstříků se teď musí přepsat.";
$pgv_lang["please_be_patient"]			= "PROSÍM O STRPENÍ";
$pgv_lang["reading_file"]			= "Čtení souboru GEDCOM";
$pgv_lang["flushing"]				= "Obsah paměti";
$pgv_lang["found_record"]			= "Nalezen záznam";
$pgv_lang["exec_time"]				= "Celkový čas:";
$pgv_lang["unable_to_create_index"]		= "Není možné vytvořit soubor index. Ujistěte se, že máte práva zápisu do složky PhpGedView. Stávající nastavení práv můžete obnovit po zapsání souborů.";
$pgv_lang["indi_complete"]			= "Aktualizace souboru s rejstříkem osob je hotova.";
$pgv_lang["family_complete"]			= "Aktualizace souboru s rejstříkem rodin je hotova.";
$pgv_lang["source_complete"]			= "Aktualizace souboru s rejstříkem zdrojů je hotova.";
$pgv_lang["tables_exist"]			= "Tabulky PhpGedView již v databázi existují.";
$pgv_lang["you_may"]				= "Můžete:";
$pgv_lang["drop_tables"]			= "Smazat nynější tabulky";
$pgv_lang["import_multiple"]			= "Importovat a pracovat s více GEDCOM soubory";
$pgv_lang["explain_options"]			= "Jestliže zvolíte možnost \"smazat tabulky\", všechna budou nahrazena tímto GEDCOM souborem.<br />Pokud zvolíte \"importovat a pracovat s GEDCOM soubory\", PhpGedView smaže jakákoliv data, která byla importována ze stejnojmenného GEDCOM souboru. Tato volba vám umožňuje ukládat data z více GEDCOM souborů ve stejných tabulkách a jednoduše mezi nimi přepínat.";
$pgv_lang["path_to_gedcom"]			= "Zadejte cestu k vašemu GEDCOM souboru:";
$pgv_lang["gedcom_title"]			= "Zadejte nadpis popisující data v tomto GEDCOM souboru";
$pgv_lang["dataset_exists"]			= "GEDCOM soubor s tímto názvem byl již do databáze importován.";
$pgv_lang["empty_dataset"]			= "Chcete vymazat stará data a nahradit je novými?";
$pgv_lang["empty_dataset"]			= "Chcete vyprázdnit databázi?";
$pgv_lang["index_complete"]			= "Rejstřík je hotov.";
$pgv_lang["click_here_to_go_to_pedigree_tree"]	= "Klikněte sem pro vstup do rodokmenu.";
$pgv_lang["updating_is_dead"]			= "Doplnění stavu k zemřelým osobám ";
$pgv_lang["import_complete"]			= "Importování je hotovo";
$pgv_lang["updating_family_names"]		= "Aktualizace příjmení pro FAM ";
$pgv_lang["processed_for"]			= "Zpracován soubor pro ";
$pgv_lang["run_tools"]				= "Chcete spustit jeden z následujících nástrojů ještě před importováním GEDCOM souboru:";
$pgv_lang["addmedia"]				= "Nástroj přidání médií";
$pgv_lang["dateconvert"]			= "Konverze dat";
$pgv_lang["xreftorin"]				= "Zkonvertovat identifikátory XREF na čísla RIN";
$pgv_lang["tools_readme"]			= "Pro další informace se podívejte do částí \"tools\" v souboru #README.TXT#.";
$pgv_lang["sec"]				= "s";
$pgv_lang["bytes_read"]				= "Načtené bajty:";
$pgv_lang["created_indis"]			= "Tabulka <i>Osoby</i> byla úspěšně vytvořena.";
$pgv_lang["created_indis_fail"]	= "Není možné vytvořit tabulku <i>jednotlivců</i>.";
$pgv_lang["created_fams"]			= "Tabulka <i>Rodiny</i> byla úspěšně vytvořena.";
$pgv_lang["created_fams_fail"]	= "Není možné vytvořit tabulku <i>rodin</i>.";
$pgv_lang["created_sources"]		= "Tabulka <i>Prameny</i> byla úspěšně vytvořena.";
$pgv_lang["created_sources_fail"]	= "Není možné vytvořit tabulku <i>pramenů</i>.";
$pgv_lang["created_other"]			= "Tabulka <i>Ostatní</i> byla úspěšně vytvořena.";
$pgv_lang["created_other_fail"]	= "Není možné vytvořit tabulku <i>ostatních záznamů</i>.";
$pgv_lang["created_places"]			= "Tabulka <i>Místa</i> byla úspěšně vytvořena.";
$pgv_lang["created_places_fail"]	= "Není možné vytvořit tabulku <i>míst</i>.";
$pgv_lang["import_progress"]	= "Průběh nahrávání...";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"]				= "Všechny rodiny";
$pgv_lang["total_indis"]			= "Všechny osoby";
$pgv_lang["starts_with"]			= "Začít s:";
$pgv_lang["person_list"]			= "Seznam osob:";
$pgv_lang["paste_person"]			= "Vložení osoby";
$pgv_lang["notes_sources_media"]		= "Poznámky, prameny a média";
$pgv_lang["notes"]				= "Poznámky";
$pgv_lang["ssourcess"]				= "Prameny";
$pgv_lang["media"]				= "Média";
$pgv_lang["name_contains"]			= "Jméno obsahuje:";
$pgv_lang["filter"]				= "Prohledat";
$pgv_lang["find_individual"]			= "Najít ID osoby";
$pgv_lang["find_familyid"]			= "Najít ID rodiny";
$pgv_lang["find_sourceid"]			= "Najít ID zdroje";
$pgv_lang["skip_surnames"]			= "Přeskočit seznamy příjmení";
$pgv_lang["show_surnames"]			= "Ukázat seznamy příjmení";
$pgv_lang["all"]				= "VŠECHNY";
$pgv_lang["hidden"]					= "Skryto";
$pgv_lang["confidential"]			= "Důvěrné";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]				= "Věk";
$pgv_lang["timeline_title"]			= "Časová osa PhpGedView";
$pgv_lang["timeline_chart"]			= "Časová osa";
$pgv_lang["remove_person"]			= "Odstranit osobu";
$pgv_lang["show_age"]				= "Zobrazit ukazatel věku";
$pgv_lang["add_another"]			= "Přidat na osu další osobu:<br />ID osoby:";
$pgv_lang["find_id"]				= "Najít ID";
$pgv_lang["show"]				= "Ukázat";
$pgv_lang["year"]				= "Rok:";
$pgv_lang["timeline_instructions"]		= "Ve většině novějších prohlížečů můžete klepnout na jméno v rámečku a přetáhnout jej na osu.";
$pgv_lang["zoom_in"]				= "Zvětšit";
$pgv_lang["zoom_out"]				= "Zmenšit";

//-- MONTH NAMES
$pgv_lang["jan"]			= "leden";
$pgv_lang["feb"]			= "únor";
$pgv_lang["mar"]			= "březen";
$pgv_lang["apr"]			= "duben";
$pgv_lang["may"]			= "květen";
$pgv_lang["jun"]			= "červen";
$pgv_lang["jul"]			= "červenec";
$pgv_lang["aug"]			= "srpen";
$pgv_lang["sep"]			= "září";
$pgv_lang["oct"]			= "říjen";
$pgv_lang["nov"]			= "listopad";
$pgv_lang["dec"]			= "prosinec";
$pgv_lang["abt"]			= "kolem";
$pgv_lang["aft"]			= "po";
$pgv_lang["and"]			= "a";
$pgv_lang["bef"]			= "před";
$pgv_lang["bet"]			= "mezi";
$pgv_lang["cal"]			= "odhadnuto";
$pgv_lang["est"]			= "přibližně";
$pgv_lang["from"]			= "od";
$pgv_lang["int"]			= "interpretováno";
$pgv_lang["to"]				= "do";
$pgv_lang["cir"]			= "asi";
$pgv_lang["apx"]			= "přibl.";

//-- Admin File Messages
$pgv_lang["select_an_option"]		= "Vyberte jednu z možností:";
$pgv_lang["readme_documentation"]	= "README dokumentace";
$pgv_lang["configuration"]		= "Konfigurace";
$pgv_lang["rebuild_indexes"]		= "Přepsat Indexy";
$pgv_lang["user_admin"]			= "Správa uživatelů";
$pgv_lang["user_created"]		= " Uživatel byl úspěšně vytvořen.";
$pgv_lang["user_create_error"]		= "Není možné přidat uživatele. Prosím vraťte se zpět a zkuste to znovu.";
$pgv_lang["password_mismatch"]		= "Hesla se neshodují.";
$pgv_lang["enter_username"]		= "Musíte vložit uživatelské jméno.";
$pgv_lang["enter_fullname"]		= "Musíte vložit celé jméno.";
$pgv_lang["enter_password"]		= "Musíte vložit heslo.";
$pgv_lang["confirm_password"]		= "Musíte potvrdit heslo.";
$pgv_lang["update_user"]		= "Aktualizovat uživatelský účet";
$pgv_lang["update_myaccount"]		= "Aktualizovat můj účet";
$pgv_lang["save"]			= "Uložit";
$pgv_lang["delete"]			= "Smazat";
$pgv_lang["edit"]			= "Úpravy";
$pgv_lang["full_name"]			= "Celé jméno";
$pgv_lang["visibleonline"]			= "Viditelný pro jiné uživatele, když je online";
$pgv_lang["editaccount"]			= "Umožnit tomuto uživateli upravovat informace o svém účtu";
$pgv_lang["admin_gedcom"]			= "Spravovat GEDCOM";
$pgv_lang["confirm_user_delete"]	= "Jste si jistí, že chcete smazat uživatele";
$pgv_lang["create_user"]		= "Vytvořit uživatele";
$pgv_lang["no_login"]			= "Není možné ověřit uživatele.";
$pgv_lang["import_gedcom"]		= "Importovat tento GEDCOM soubor";
$pgv_lang["duplicate_username"]		= "Toto uživatelské jméno již existuje. Prosím, vraťte se zpět a vyberte jiné uživatelské jméno.";
$pgv_lang["gedcomid"]			= "ID osoby";
$pgv_lang["enter_gedcomid"]		= "Musíte vložit ID GEDCOM souboru.";
$pgv_lang["user_info"]			= "Informace o mém uživateli";
$pgv_lang["rootid"]			= "Střen (proband) vývodu";
$pgv_lang["download_gedcom"]		= "Stáhnout GEDCOM";
$pgv_lang["upload_gedcom"]		= "Nahrát GEDCOM";
$pgv_lang["add_new_gedcom"]		= "Vytvořit nový GEDCOM";
$pgv_lang["gedcom_file"]		= "Soubor GEDCOM:";
$pgv_lang["enter_filename"]		= "Musíte vložit název GEDCOM souboru.";
$pgv_lang["file_not_exists"]	= "Soubor, jehož název jste zadali, neexistuje.";
$pgv_lang["file_exists"]		= "GEDCOM soubor s tímto názvem již zde je. Prosím vyberte jiný název souboru nebo smažte starý soubor.";
$pgv_lang["new_gedcom_title"]		= "Genealogie z [#GEDCOMFILE#]";
$pgv_lang["upload_error"]		= "Během nahrávání vašeho souboru se objevila chyba.";
$pgv_lang["upload_help"]		= "Vyberte soubor ze svého počítače k nahrání na svůj server. Všechny soubory budou nahrány do složky:";
$pgv_lang["add_gedcom_instructions"]	= "Zadejte název pro tento nový GEDCOM soubor. Tento soubor bude uložen do hlavního adresáře: ";
$pgv_lang["file_success"]		= "Soubor byl úspěšně nahrán";
$pgv_lang["file_too_big"]		= "Nahraný soubor přesáhl povolenou velikost";
$pgv_lang["file_partial"]		= "Soubor byl nahrán jen částečně, prosím zkuste to znovu";
$pgv_lang["file_missing"]		= "Žádný soubor dodán. Nahrajte jej znovu";
$pgv_lang["manage_gedcoms"]		= "Správa GEDCOM souborů a úprava privátnosti";
$pgv_lang["administration"]		= "Administrace";
$pgv_lang["ansi_to_utf8"]		= "Převést kódování v tomto GEDCOM souboru z ANSI (ISO-8859-1) na UTF-8?";
$pgv_lang["utf8_to_ansi"]		= "Chcete změnit kódování v tomto GEDCOM souboru z UTF-8 na ANSI (ISO-8859-1)?";
$pgv_lang["user_manual"]		= "Uživatelský manuál aplikace PhpGedView";
$pgv_lang["upgrade"]			= "Aktualizovat PhpGedView";

///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["view_logs"]			= "Zobrazit logfiles";
$pgv_lang["logfile_content"]	= "Obsah \"log\" souboru";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["step1"]				= "Krok 1 z 4:";
$pgv_lang["step2"]				= "Krok 2 z 4:";
$pgv_lang["step3"]				= "Krok 3 z 4:";
$pgv_lang["step4"]				= "Krok 4 z 4:";
$pgv_lang["validate_gedcom"]		= "Potvrdit platnost GEDCOMu";
$pgv_lang["img_admin_settings"]		= "Upravit nastavení nakládání s obrázky";
$pgv_lang["download_note"]		= "POZNÁMKA: Velké GEDCOM soubory se mohou stahovat velmi dlouho. Jestliže PHP přeruší proces ještě před úplným stažením souboru, stažený GEDCOM nebude kompletní. Chcete-li se ujistit, že je váš soubor celý, podívejte se, jestli je na jeho konci řádek 0 TRLR. Stažení souboru by mělo trvat přibližně stejně dlouho jako jeho nahrání.";
$pgv_lang["pgv_registry"]		= "Zobrazit jiné weby používající PhpGedView";
$pgv_lang["verify_upload_instructions"]	= "Zvolíte-li pokračování, bude starý GEDCOM soubor nahrazen novým souborem, který jste nahráli, a importování začne znovu. Zvolíte-li konec, zůstane starý soubor zachován.";
$pgv_lang["cancel_upload"]			= "Ukončit nahrávání";


//-- Relationship chart messages
$pgv_lang["relationship_chart"]		= "Schéma vztahů";
$pgv_lang["person1"]			= "Osoba 1";
$pgv_lang["person2"]			= "Osoba 2";
$pgv_lang["no_link_found"]		= "Mezi těmito dvěma osobami nebyl nalezen žádný (další) vztah.";
$pgv_lang["sibling"]			= "Sourozenec";
$pgv_lang["follow_spouse"]		= "Zkontrolovat příbuzenství sňatkem.";
$pgv_lang["timeout_error"]		= "Vykonávání skriptu bylo ukončeno před dokončením vyhledávání.";
$pgv_lang["son"]			= "Syn";
$pgv_lang["daughter"]			= "Dcera";
$pgv_lang["brother"]			= "Bratr";
$pgv_lang["sister"]			= "Sestra";
$pgv_lang["relationship_to_me"]		= "Vztah ke mně";
$pgv_lang["next_path"]			= "Najít další vztah";
$pgv_lang["show_path"]			= "Ukázat vztah";
$pgv_lang["line_up_generations"]	= "Vyrovnat podle generační úrovně";
$pgv_lang["oldest_top"]             = "Zobrazit nejstarší nahoře";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]		= "Jste si jisti, že chcete smazat tento údaj?";
$pgv_lang["access_denied"]		= "<b>Přístup odepřen</b><br />Nemáte přístup k tomuto zdroji.";
$pgv_lang["gedrec_deleted"]		= "Záznam byl úspěšně smazán.";
$pgv_lang["gedcom_deleted"]		= "GEDCOM soubor [#GED#] byl úspěšně smazán.";
$pgv_lang["changes_exist"]		= "V tomto GEDCOM souboru byly provedeny změny.";
$pgv_lang["accept_changes"]		= "Přijmout / Odmítnout změny";
$pgv_lang["show_changes"]		= "Tento záznam byl aktualizován. Klikněte sem pro zobrazení změn.";
$pgv_lang["hide_changes"]		= "Chcete-li skrýt změny, klikněte sem.";
$pgv_lang["review_changes"]		= "Revize změn v GEDCOM souborech";
$pgv_lang["undo_successful"]		= "Návrat byl úspěšný";
$pgv_lang["undo"]			= "Zpět";
$pgv_lang["view_change_diff"]		= "Prohlédnout si změny";
$pgv_lang["changes_occurred"]		= "U této osoby byly provedeny následující změny:";
$pgv_lang["find_place"]			= "Najít místo";
$pgv_lang["close_window"]		= "Zavřít okno";
$pgv_lang["close_window_without_refresh"] = "Zavřít okno bez opětovného načtení";
$pgv_lang["place_contains"]		= "Místo obsahuje:";
$pgv_lang["accept_gedcom"]		= "U každé změny se rozhodněte, zda ji chcete přijmout, nebo zamítnout.<br />Chcete-li přijmout všechny změny najednou, klikněte na \"Přijmout všechny změny\" v políčku dole.<br />Jestliže chcete více informací k některé úpravě, klikněte na \"Zobrazit rozdíly\" a uvidíte rozdíly mezi starou a novou verzí, <br /> nebo klikněte na \"Zobrazit přímo záznam GEDCOM\" a uvidíte novou verzi zapsanou přímo ve fromátu GEDCOM.";
$pgv_lang["ged_import"]			= "Importovat";
$pgv_lang["now_import"]			= "Nyní byste měli importovat záznamy do PhpGedView kliknutím na odkaz \"Importovat\".";
$pgv_lang["add_fact"]			= "Přidat nový údaj";
$pgv_lang["add"]			= "Přidat";
$pgv_lang["custom_event"]		= "Vlastní událost";
$pgv_lang["update_successful"]		= "Aktualizace byla úspěšná";
$pgv_lang["add_child"]			= "Přidat dítě";
$pgv_lang["add_child_to_family"]	= "Přidat dítě k této rodině";
$pgv_lang["add_sibling"]		= "Přidat bratra nebo sestru";
$pgv_lang["add_son_daughter"]		= "Přidat syna nebo dceru";
$pgv_lang["must_provide"]		= "Musíte poskytnout ";
$pgv_lang["delete_person"]		= "Smazat tuto osobu";
$pgv_lang["confirm_delete_person"]	= "Jste si jistí, že chcete vymazat tuto osobu z GEDCOM souboru?";
$pgv_lang["find_media"]			= "Najít média";
$pgv_lang["set_link"]			= "Nastavit odkaz";
$pgv_lang["add_source_lbl"]		= "Přidat citaci k prameni";
$pgv_lang["add_source"]			= "Přidat novou citaci k prameni";
$pgv_lang["add_note_lbl"]		= "Přidat poznámku";
$pgv_lang["add_note"]			= "Přidat novou poznámku";
$pgv_lang["add_media_lbl"]		= "Přidat média";
$pgv_lang["add_media"]			= "Přidat do médií novou položku";
$pgv_lang["delete_source"]		= "Smazat tento pramen";
$pgv_lang["confirm_delete_source"]	= "Jste si jistí, že chcete vymazat tento pramen z GEDCOM souboru?";
$pgv_lang["add_husb"]			= "Přidat manžela";
$pgv_lang["add_husb_to_family"]		= "Přidat manžela k této rodině";
$pgv_lang["add_wife"]			= "Přidat manželku";
$pgv_lang["add_wife_to_family"]		= "Přidat manželku k této rodině";
$pgv_lang["find_family"]		= "Najít rodinu";
$pgv_lang["find_fam_list"]		= "Sestavit seznam rodin";
$pgv_lang["add_new_wife"]		= "Přidat novou manželku";
$pgv_lang["add_new_husb"]		= "Přidat nového manžela";
$pgv_lang["edit_name"]			= "Upravit jméno";
$pgv_lang["delete_name"]		= "Smazat jméno";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["no_temple"]			= "No Temple - Living Ordinance";
///////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["replace"]			= "Nahradit záznam";
$pgv_lang["append"]			= "Připojit záznam";
$pgv_lang["add_father"]			= "Přidat nového otce";
$pgv_lang["add_mother"]			= "Přidat novou matku";
$pgv_lang["add_obje"]			= "Přidat nový multimediální soubor";
$pgv_lang["no_changes"]			= "Zatím nebyly provedeny žádné změny, které by se měly přezkoumat.";
$pgv_lang["accept"]				= "Přijmout";
$pgv_lang["accept_all"]			= "Přijmout všechny změny";
$pgv_lang["accept_successful"]	= "Změny byly přijaty a nové údaje zapsány do databáze";
$pgv_lang["edit_raw"]			= "Upravit přímo záznam GEDCOM";
$pgv_lang["select_date"]		= "Vybrat datum";
$pgv_lang["create_source"]		= "Vytvořit nový pramen";
$pgv_lang["new_source_created"]	= "Nový pramen byl vytvořen.";
$pgv_lang["paste_id_into_field"]= "Vložte toto ID pramene do poliček, z nichž se chcete odvolávat na tento pramen.";
$pgv_lang["add_name"]				= "Přidat nové jméno";
$pgv_lang["privacy_not_granted"]	= "Nemáte přístup k";
$pgv_lang["user_cannot_edit"]		= "Tento uživatel nemůže upravovat tento GEDCOM.";
$pgv_lang["gedcom_editing_disabled"]	= "Upravování tohoto GEDCOMu bylo zakázáno administrátorem systému.";
$pgv_lang["privacy_prevented_editing"]	= "Nastavení privátnosti vám neumožňuje upravovat tento záznam.";

//-- calendar.php messages
$pgv_lang["on_this_day"]		= "Tohoto dne ve vaší historii...";
$pgv_lang["in_this_month"]		= "V tomto měsíci ve vaší historii...";
$pgv_lang["in_this_year"]		= "Tohoto roku ve vaší historii...";
$pgv_lang["year_anniversary"]		= "#year_var# výročí";
$pgv_lang["today"]			= "Dnes";
$pgv_lang["day"]			= "Den:";
$pgv_lang["month"]			= "Měsíc";
$pgv_lang["showcal"]			= "Události k zobrazení:";
$pgv_lang["anniversary_calendar"]	= "Kalendář výročí";
$pgv_lang["sunday"]			= "neděle";
$pgv_lang["monday"]			= "pondělí";
$pgv_lang["tuesday"]			= "úterý";
$pgv_lang["wednesday"]			= "středa";
$pgv_lang["thursday"]			= "čtvrtek";
$pgv_lang["friday"]			= "pátek";
$pgv_lang["saturday"]			= "sobota";
$pgv_lang["viewday"]			= "Zobrazit den";
$pgv_lang["viewmonth"]			= "Zobrazit měsíc";
$pgv_lang["viewyear"]			= "Zobrazit rok";
$pgv_lang["all_people"]			= "Všichni lidé";
$pgv_lang["living_only"]		= "Žijící lidé";
$pgv_lang["recent_events"]		= "Nedávné události (&lt; 100 let)";
$pgv_lang["day_not_set"]			= "Datum nezadáno";
$pgv_lang["year_error"]			= "Data před rokem 1970 bohužel nejsou podporována.";

//-- upload media messages
$pgv_lang["upload_media"]		= "Nahrát multimediální soubory";
$pgv_lang["media_file"]			= "Soubory médií";
$pgv_lang["thumbnail"]			= "Zmenšenina";
$pgv_lang["upload_successful"]		= "Nahrání bylo úspěšné";
$pgv_lang["lost_password"]		= "Zapomněli jste své heslo?";

//-- user self registration module
$pgv_lang["requestpassword"]		= "Zažádat o nové heslo";
$pgv_lang["no_account_yet"]		= "Ještě nemáte svůj účet?";
$pgv_lang["requestaccount"]		= "Zažádat o nový uživatelský účet";
$pgv_lang["register_info_01"]		= "Množství dat, které může být veřejně zobrazeno na těchto stránkách, může být omezeno kvůli zákonům o ochraně osobních dat. Většina lidí si nepřeje, aby jejich osobní data byla veřejně dostupná na internetu. Mohlo by jich být zneužito k rozesílání spamu nebo ke krádeži osobnosti.<br /><br />Abyste měli přístup k soukromým údajům, musíte mít na těchto stránkách účet. Pro získání účtu se můžete zaregistrovat poskytnutím požadovaných informací. Potom, co administrátor vaši registraci zkontroluje a schválí, budete se moci přihlásit a zobrazit si soukromá data.<br /><br />Pokud je aktivována vyšší úroveň utajení, budete mít po přihlášení přístup jen k vlastním soukromým datům a soukromým údajům vašich nejbližších příbuzných. Administrátor také může poskytnout přístup k úpravám databáze, takže můžete měnit nebo přidávat informace.<br /><br />POZNÁMKA: Soukromá data obdržíte jedině v případě, že můžete dokázat, že jste blízcí příbuzní osoby v databázi.<br /><br />Jestliže nejste blízcí příbuzní, pravděpodobně nedostanete účet, takže byste si měli vyhnout možným problémům.<br />Pokud potřebujete další radu nebo informaci, prosím kontaktujte webmastera (odkaz dole).<br /><br />";
$pgv_lang["register_info_02"]		= "";
$pgv_lang["pls_note01"]			= "Prosím pozor: Tato aplikace rozlišuje velká a malá písmena!";
$pgv_lang["min6chars"]			= "Heslo musí obsahovat alespoň 6 znaků";
$pgv_lang["pls_note02"]			= "Prosím pozor: Hesla mohou obsahovat písmena, čísla i další znaky.";
$pgv_lang["pls_note03"]			= "Tato e-mailová adresa bude před aktivací účtu ověřena. Na stránkách nebude zobrazena. Na tuto adresu bude odeslána zpráva s vašimi registračními údaji";
$pgv_lang["emailadress"]		= "E-mailová adresa";
$pgv_lang["pls_note04"]			= "Vyplnění polí označených * je povinné.";
$pgv_lang["pls_note05"]			= "Po vyplnění a odeslání formuláře na této stránce a ověření vašich odpovědí obdržíte na e-mail, který jste zadali, zprávu s potvrzením. Potvrzovací e-mail použijte k aktivování svého účtu; jestliže se vám nepodaří aktivovat svůj účet do sedmi dní, bude vymazán (pak se můžete znovu pokusit zaregistrovat). Pro vstup budete budete potřebovat znát své přihlašovací (uživatelské) jméno a heslo. Musíte zadat platnou existující e-mailovou adresu, abyste mohli obdržet e-mail s potvrzením registrace.<br /><br />Pokud narazíte při registraci účtu na nějaký problém, prosím obraťte se na webmastera.";

$pgv_lang["mail01_line01"]		= "Nazdar #user_fullname# ...";
$pgv_lang["mail01_line02"]		= "Z adresy ( #SERVER_NAME# ) byl vyslán požadavek na přihlášení pod vaší e-mailovou adresou ( #user_email# ).";
$pgv_lang["mail01_line03"]		= "Byla použita následující data.";
$pgv_lang["mail01_line04"]		= "Prosím klikněte na odkaz dole a vyplňte požadovaná data pro ověření vašeho účtu a vaší e-mailové adresy.";
$pgv_lang["mail01_line05"]		= "Kdybyste tato data nevyžadovali, můžete tuto zprávu klidně smazat.";
$pgv_lang["mail01_line06"]		= "Žádný další e-mail z tohoto systému již nedostanete, protože účet bude do sedmi dnů bez ověření smazán.";

$pgv_lang["mail01_subject"]		= "Vaše registrace na #SERVER_NAME#";

$pgv_lang["mail02_line01"]		= "Nazdar administrátore ...";
$pgv_lang["mail02_line02"]		= "Nový uživatel se zaregistroval na ( #SERVER_NAME# ).";
$pgv_lang["mail02_line03"]		= "Uživatel obdržel e-mail s daty nezbytnými k ověření svého účtu.";
$pgv_lang["mail02_line04"]		= "Jakmile se uživatel dokončí toto ověřování, budete e-mailem informováni, abyste uživateli dali práva k přihlášení na vaše stránky.";

$pgv_lang["mail02_subject"]		= "Nová registrace na #SERVER_NAME#";

$pgv_lang["hashcode"]			= "Ověřovací kód:";
$pgv_lang["thankyou"]			= "Nazdar #user_fullname# ...<br />Díky za registraci.";
$pgv_lang["pls_note06"]			= "Nyní na e-mail ( #user_email# ) obdržíte potvrzení. Tento e-mail použijte k aktivování svého účtu; jestliže se vám nepodaří aktivovat svůj účet do sedmi dní, bude vymazán (pak se můžete znovu pokusit zaregistrovat). Abyste se mohli přihlásit na tyto stránky, budete potřebovat znát své přihlašovací jméno a heslo.";

$pgv_lang["registernew"]		= "Potvrzení nového účtu";
$pgv_lang["user_verify"]		= "Ověření uživatele";
$pgv_lang["send"]			= "Odeslat";

$pgv_lang["pls_note07"]			= "Prosím vepište své uživatelské jméno, heslo a ověřovací kód, který jste obdrželi z tohoto systému, pro ověření vaší žádosti o účet.";
$pgv_lang["pls_note08"]			= "Data pro uživatele #user_name# byla zkontrolována.";

$pgv_lang["mail03_line01"]		= "Nazdar administrátore ...";
$pgv_lang["mail03_line02"]		= "#newuser[username]# ( #newuser[fullname]# ) zkontroloval registrační data.";
$pgv_lang["mail03_line03"]		= "Pro přihlášení a úpravu uživatele a povolení k přihlášení na vaše stránky prosím klikněte na odkaz dole.";

$pgv_lang["mail03_subject"]		= "Nové ověření na #SERVER_NAME#";

$pgv_lang["pls_note09"]			= "Byli jste identifikováni jako registrovaný uživatel.";
$pgv_lang["pls_note10"]			= "Administrátor byl upozorněn.<br />Jakmile vám dám povolení k přihlášení, budete se moci přihlásit zadáním svého uživatelského jména a hesla.";
$pgv_lang["data_incorrect"]		= "Data nebyla správná!<br />Prosím zkuste to znovu!";
$pgv_lang["user_not_found"]		= "Nebylo možné ověřit data, která jste vložili.  Prosím vraťte se zpět a zkuste to znovu.";

$pgv_lang["lost_pw_reset"]		= "Zapomenuté heslo";

$pgv_lang["pls_note11"]			= "Chcete-li, aby vaše heslo bylo smazáno, pošlete nám uživatelské jméno a e-mailovou adresu svého uživatelského účtu. <br /><br />Pošleme vám e-mailem odkaz na stránku, která obsahuje zakódované heslo pro váš účet. Navštívíte-li tuto URL, budete mít možnost změnit si heslo a přihlásit se do tohoto systému. Z bezpečnostních důvodů byste měli toto heslo uchovat v tajnosti (neměli by jej znát ani administrátoři těchto stránek; nebudeme se na něj ptát).<br /><br />Jestliže budete požadovat pomoc administrátora, prosím kontaktujte administrátora stránek.";
$pgv_lang["enter_email"]		= "Musíte zadat e-mailovou adresu.";

$pgv_lang["mail04_line01"]		= "Nazdar #user_fullname# ...";
$pgv_lang["mail04_line02"]		= "Bylo zažádáno o nové heslo pro vaše uživatelské jméno!";
$pgv_lang["mail04_line03"]		= "Doporučení:";
$pgv_lang["mail04_line04"]		= "Teď prosím klikněte na odkaz dole, přihlaste se s novým heslem a ihned si ho změňte, abyste uchovali svá data v tajnosti.";

$pgv_lang["mail04_subject"]		= "Data požadována na #SERVER_NAME#";

$pgv_lang["pwreqinfo"]			= "Nazdar...<br /><br />Na adresu (#user[email]#) byl zaslán e-mail s novým heslem.<br /><br />Prosím zkontrolujte svou e-mailovou schránku, zprávu byste měli obdržet během několika minut.<br /><br />Doporučení:<br /><br />Potom, co zažádáte o e-mail, měli byste se na tyto stránky přihlásit se svým novým heslem a změnit si jej, abyste uchovali svá data v tajnosti.";

$pgv_lang["editowndata"]		= "Můj účet";
$pgv_lang["savedata"]			= "Uložit změněná data";
$pgv_lang["datachanged"]		= "Uživatelská data byla změněna!";
$pgv_lang["datachanged_name"]		= "Možná bude potřeba, abyste se přihlásili znovu (se svým novým uživatelským jménem).";
$pgv_lang["myuserdata"]			= "Můj účet";
$pgv_lang["verified"]			= "Uživatel potvrdil registraci";
$pgv_lang["verified_by_admin"]		= "Uživatel byl adminem povolen";
$pgv_lang["user_theme"]			= "Můj motiv";
$pgv_lang["mgv"]			= "MyGedView";
$pgv_lang["mygedview"]			= "Vstupní brána MyGedView";
$pgv_lang["passwordlength"]		= "Heslo musí obsahovat alespoň 6 znaků.";
$pgv_lang["admin_approved"]		= "Váš účet na #SERVER_NAME# byl povolen";
$pgv_lang["you_may_login"]		= " administrátorem stránek. Nyní se můžete odkazem níže přihlásit do systému PhpGedView:";
$pgv_lang["welcome_text_auth_mode_1"]	=	"<b>VÍTEJTE NA TĚCHTO RODOPISNÝCH STRÁNKÁCH</b><br /><br />Přístup na tyto stránky je povolen všem návštěvníkům, kteří zde mají zřízený účet.<br />Jestliže zde již máte svůj účet, můžete se na této stránce přihlásit.<br /><br />Pokud ještě účet nemáte, můžete o něj požádat kliknutím na příslušný odkaz na této stránce.<br />Po ověření zadaných údajů vám administrátor účet zpřístupní.<br />Oznámení o zpřístupnění obdržíte e-mailem.";
$pgv_lang["welcome_text_auth_mode_2"]	=	"<b>VÍTEJTE NA TĚCHTO RODOPISNÝCH STRÁNKÁCH</b><br /><br />Přístup na tyto stránky je umožněn pouze <b>přihlášeným</b> uživatelům.<br />Jestliže zde již máte svůj účet, můžete se na této stránce přihlásit.<br /><br />Pokud ještě účet nemáte, můžete o něj požádat kliknutím na příslušný odkaz na této stránce.<br />Po ověření zadaných údajů administrátor vaši žádost buď přijme, nebo odmítne.<br />Oznámení o přijetí své žádosti obdržíte e-mailem.";
$pgv_lang["welcome_text_auth_mode_3"]	=	"<b>VÍTEJTE NA TĚCHTO RODOPISNÝCH STRÁNKÁCH</b><br /><br />Přístup na tyto stránky je povolen pouze <b>členům rodiny</b>.<br />Jestliže zde již máte svůj účet, můžete se na této stránce přihlásit.<br /><br />Pokud ještě účet nemáte, můžete o něj požádat kliknutím na příslušný odkaz na této stránce.<br />Po ověření zadaných údajů administrátor vaši žádost buď přijme, nebo odmítne.<br />Oznámení o přijetí své žádosti obdržíte e-mailem.";
$pgv_lang["welcome_text_cust_head"]		=	"<b>VÍTEJTE NA TĚCHTO RODOPISNÝCH STRÁNKÁCH</b><br /><br />Přístup na tyto stránky je umožněn pouze uživatelům, kteří zde mají zřízený svůj uživatelský účet s heslem.<br />";


//-- mygedview page
$pgv_lang["welcome"]			= "Vítejte";
$pgv_lang["upcoming_events"]		= "Nadcházející události";
$pgv_lang["chat"]			= "Chat";
$pgv_lang["users_logged_in"]		= "Přihlášení uživatelé";
$pgv_lang["message"]			= "Poslat zprávu";
$pgv_lang["my_messages"]		= "Moje zprávy";
$pgv_lang["date_created"]		= "Data byla zaslána:";
$pgv_lang["message_from"]		= "Emailová adresa";
$pgv_lang["message_from_name"]		= "Vaše jméno:";
$pgv_lang["message_to"]			= "Adresát:";
$pgv_lang["message_subject"]		= "Předmět:";
$pgv_lang["message_body"]		= "Text:";
$pgv_lang["no_to_user"]			= "Nezadali jste příjemce zprávy.  Není možné pokračovat.";
$pgv_lang["provide_email"]		= "Prosím zadejte svou e-mailovou adresu, abychom vás případně mohli kontaktovat.  Pokud nám neposkytnete svou e-mailovou adresu, nebudeme vám moci odpovědět.  Vaše e-mailová adresa nebude užita k žádným jiným účelům než k odpovědi na váš dotaz.";
$pgv_lang["reply"]			= "Odpovědět";
$pgv_lang["message_deleted"]		= "Zpráva byla smazána";
$pgv_lang["message_sent"]		= "Zpráva byla zaslána";
$pgv_lang["reset"]			= "Reset";
$pgv_lang["site_default"]		= "Domácí stránka";
$pgv_lang["mygedview_desc"]		= "Vaše stránka MyGedView vám umožňuje uchovávat záložky svých přátel, sledovat nadcházející události a spolupracovat s ostatními uživateli PhpGedView.";
$pgv_lang["no_messages"]		= "Nemáte žádné příchozí zprávy.";
$pgv_lang["clicking_ok"]		= "Kliknete-li na OK, otevře se další okno, kde budete moci kontaktovat #user[fullname]#";
$pgv_lang["my_favorites"]		= "Moje oblíbené";
$pgv_lang["no_favorites"]		= "Nevybrali jste žádné oblíbené.  Chcete-li přidat nějakou osobu ke svým oblíbeným, najděte informace o této osobě a pak klikněte na odkaz \"Přidat do mých oblíbených\" nebo vyplňte políčko ID dole pro přidání osoby pomocí ID čísla.";
$pgv_lang["add_to_my_favorites"]	= "Přidat do mých oblíbených";
$pgv_lang["gedcom_favorites"]		= "Oblíbené u tohoto GEDCOMu";
$pgv_lang["no_gedcom_favorites"]	= "V tuto chvíli nejsou označeny žádné oblíbené. Admin může nastavit zobrazení oblíbených při startu.";
$pgv_lang["confirm_fav_remove"]		= "Jste si jisti, že chcete odstranit tuto položku z vašich oblíbených?";
$pgv_lang["invalid_email"]		= "Prosím zadejte platnou e-mailovou adresu.";
$pgv_lang["enter_subject"]		= "Prosím zadejte předmět zprávy.";
$pgv_lang["enter_body"]			= "Prosím zadejte text zprávy před jejím odesláním.";
$pgv_lang["confirm_message_delete"]	= "Jste si jistí, že chcete smazat tuto zprávu?  Nebude možné ji později získat zpět.";
$pgv_lang["message_email1"]		= "Tato zpráva byla zaslána na váš PhpGedView uživatelský účet z ";
$pgv_lang["message_email2"]		= "Tuto zprávu jste poslali na PhpGedView uživatelský účet:";
$pgv_lang["message_email3"]		= "Tuto zprávu jste poslali administrátorovi PhpGedView:";
$pgv_lang["viewing_url"]		= "Tato zpráva byla poslána během zobrazování následující url: ";
$pgv_lang["messaging2_help"]		= "Jestliže jste odeslali tuto zprávu, obdržíte její kopii na svou e-mailovou adresu, kterou jste uvedli při registraci.";
$pgv_lang["random_picture"]		= "Náhodný obrázek";
$pgv_lang["message_instructions"]	= "<b>Prosím pozor:</b> Soukromé informace žijících osob budou poskytnuta pouze rodinným příbuzným a blízkým přátelům. Předtím, než obdržíte jakákoliv soukromá data, budete požádáni o ověření vašeho vztahu.  Někdy mohou být soukromé povahy i data zemřelých osob.  Jestliže je to tento případ, pak je to z toho důvodu, že není dost známých informací o této osobě, aby bylo možné rozhodnout, jestli žije nebo ne, a pak tedy ani žádné další informace o této informace nemáme.<br /><br />Před vznesením dotazu na nějakou, prosím zkontrolujte u ní data, místa a blízké příbuzné, abyste si byli jistí, že se tážete na tu správnou osobu.  Jestliže provádíte změny v genealogických datech, prosím uveďte prameny, ze kterých jste čerpali.<br /><br />";
$pgv_lang["sending_to"]			= "Tato zpráva bude odeslána na #TO_USER#";
$pgv_lang["preferred_lang"]	 	= "Tento uživatel upřednostňuje příjem zpráv přes #USERLANG#";
$pgv_lang["gedcom_created_using"]	= "Tento GEDCOM byl vytvořen v programu <b>#SOFTWARE# #VERSION#</b>.";
$pgv_lang["gedcom_created_on"]		= "Tento GEDCOM byl vytvořen <b>#DATE#</b>.";
$pgv_lang["gedcom_created_on2"]	= "<b>#DATE#</b>";
$pgv_lang["gedcom_stats"]		= "Statistika GEDCOMu";
$pgv_lang["stat_individuals"]		= "osob,";
$pgv_lang["stat_families"]		= "rodin,";
$pgv_lang["stat_sources"]		= "pramenů,";
$pgv_lang["stat_other"]			= "ostatních záznamů";
$pgv_lang["customize_page"]		= "Upravit vstupní bránu MyGedView";
$pgv_lang["customize_gedcom_page"]	= "Upravit úvodní stránku tohoto GEDCOMu";
$pgv_lang["upcoming_events_block"]	= "Blok nadcházejících událostí";
$pgv_lang["upcoming_events_descr"]	= "V bloku nadcházejících událostí je zobrazen seznam událostí z momentálně prohlíženého GEDCOMu, jejichž výročí si připomeneme během příštích 30 dní.  Přes uživatelskou stránku MyGedView se v bloku zobrazí jen živí lidé.  Přes vstupní bránu GEDCOMu se zobrazí všichni lidé.";
$pgv_lang["todays_events_block"]	= "Blok pro dnešní den";
$pgv_lang["todays_events_descr"]	= "Tohoto dne ve vaší historii... V bloku bude zobrazen seznam událostí z momentálně prohlíženého GEDCOMu, které se staly tohoto dne.  Pokud nejsou nalezeny žádné události, blok se neukáže.  Přes uživatelskou stránku MyGedView se v bloku zobrazí jen seznam žijících lidí.  Přes vstupní bránu GEDCOMu se zobrazí všichni lidé.";
$pgv_lang["logged_in_users_block"]	= "Přihlášeni uživatelé";
$pgv_lang["logged_in_users_descr"]	= "Kliknutím na tento odkaz zjistíte, kteří uživatelé jsou právě teď přihlášeni.";
$pgv_lang["user_messages_block"]	= "Blok zpráv";
$pgv_lang["user_messages_descr"]	= "V bloku zpráv je zobrazen seznam zpráv, které byly poslány aktivnímu uživateli.";
$pgv_lang["user_favorites_block"]	= "Blok uživatelských Oblíbených";
$pgv_lang["user_favorites_descr"]	= "V bloku uživatelských Oblíbených se uživateli zobrazí seznam oblíbených osob v tomto systému, takže se k nim může snadněji dostat.";
$pgv_lang["welcome_block"]		= "Uvítací blok uživatele";
$pgv_lang["welcome_descr"]		= "Uvítací blok uživatele ukazuje uživateli nynější datum a čas, odkazy pro úpravu jeho účtu nebo pro zobrazení vlastního vývodu a odkaz pro úpravu vstupní brány.";
$pgv_lang["random_media_block"]		= "Blok náhodného média";
$pgv_lang["random_media_descr"]		= "Blok náhodného média náhodně vybere obrázek nebo jinou mediální položku v právě prohlíženém GEDCOMu a zobrazí ji uživateli.";
$pgv_lang["gedcom_block"]		= "Uvítací blok GEDCOM";
$pgv_lang["gedcom_descr"]		= "Uvítací blok GEDCOM funguje stejně jako uvítací blok uživatele – vítá návštěvníka stránek a zobrazuje nadpis právě prohlíženého GEDCOMu a nynější datum a čas.";
$pgv_lang["gedcom_favorites_block"]	= "Blok Oblíbených GEDCOMu";
$pgv_lang["gedcom_favorites_descr"]	= "Blok Oblíbených GEDCOMu umožňuje administrátorovi stránek vybrat své oblíbené osoby, aby k nim měli návštěvníci snazší přístup.  Je to možnost, jak zvýraznit osoby, které jsou v historii vašeho rodu důležité.";
$pgv_lang["gedcom_stats_block"]		= "Blok statistiky GEDCOMu";
$pgv_lang["gedcom_stats_descr"]		= "Blok statistiky GEDCOMu ukazuje návštěvníkovi základní informace o GEDCOM souboru, jako například čas vytvoření nebo počet osob v souboru.";
$pgv_lang["portal_config_intructions"]	= "Na této stránce si můžete upravit stránku rozmístěním bloků na stránce dle vaší potřeby.  Stránka je rozdělena na dvě části: 'Hlavní' a 'Pravý' oddíl. Bloky 'hlavního' oddílu jsou větší a jsou umístěny pod titulkem stránky. 'Pravý' oddíl začíná napravo od titulku a pokračuje dolů po pravé straně. Každý oddíl svůj vlastní seznam bloků, které se zobrazí na stránce v pořadí, v jakém jsou v seznamu. Bloky můžete přidávat, odstraňovat a přeskupovat dle libosti.";
$pgv_lang["login_block"]		= "Přihlašovací blok";
$pgv_lang["login_descr"]		= "Přihlašovací blok zobrazuje uživatelům uživatelské jméno a heslo k přihlášení.";
$pgv_lang["theme_select_block"] 	= "Blok pro výběr motivu";
$pgv_lang["theme_select_descr"] 	= "Blok pro výběr motivu se zobrazuje, i když není změna motivu povolena.";
$pgv_lang["block_top10_title"]		= "Nejčastější příjmení";
$pgv_lang["block_top10"]			= "Blok příjmení Top-10";
$pgv_lang["block_top10_descr"]		= "V tomto bloku vidíte tabulku 10 nejužívanějších příjmení v této databázi.";
$pgv_lang["gedcom_news_block"]		= "Blok novinek GEDCOMu";
$pgv_lang["gedcom_news_descr"]		= "Blok novinek GEDCOMu návštěvníkům ukazuje zprávy a články přidané adminem.  Tady je vhodné například upozorňovat na aktualizované GEDCOM soubory nebo oznamovat rodinná setkání.";
$pgv_lang["user_news_block"]		= "Uživatelský deník";
$pgv_lang["user_news_descr"]		= "Uživatelský deník umožňuje uživatelům uchovávat poznámky nebo deník online.";
$pgv_lang["my_journal"]			= "Můj deník";
$pgv_lang["no_journal"]			= "Nevytvořili jste v deníku žádné položky.";
$pgv_lang["confirm_journal_delete"]	= "Opravdu chcete vymazat tuto položku z deníku?";
$pgv_lang["add_journal"]		= "Přidat do deníku nový záznam";
$pgv_lang["gedcom_news"]		= "Novinky";
$pgv_lang["confirm_news_delete"]	= "Opravdu chcete z Novinek vymazat tuto položku?";
$pgv_lang["add_news"]			= "Přidat nový článek";
$pgv_lang["no_news"]			= "Žádné články, novinky nebyly dodány.";
$pgv_lang["edit_news"]			= "Přidat/Upravit záznam v Deníku/Novinkách";
$pgv_lang["enter_title"]		= "Vložte prosím nadpis.";
$pgv_lang["enter_text"]			= "Vložte prosím text tohoto záznamu.";
$pgv_lang["news_saved"]			= "Příspěvek do Novinek/Deníku byl úspěšně uložen.";
$pgv_lang["article_text"]		= "Vložte text:";
$pgv_lang["main_section"]		= "Bloky hlavního oddílu";
$pgv_lang["right_section"]		= "Bloky pravého oddílu";
$pgv_lang["move_up"]			= "Posunout nahoru";
$pgv_lang["move_down"]			= "Posunout dolů";
$pgv_lang["move_right"]			= "Přesunout doprava";
$pgv_lang["move_left"]			= "Přesunout doleva";
$pgv_lang["add_main_block"]		= "Přidat blok do hlavního oddílu...";
$pgv_lang["add_right_block"]		= "Přidat blok do pravého oddílu...";
$pgv_lang["broadcast_all"]		= "Rozeslat všem uživatelům";
$pgv_lang["hit_count"]			= "Počet přístupů:";
$pgv_lang["phpgedview_message"]	= "Zpráva PhpGedView";
$pgv_lang["common_surnames"]	= "Nejčastější příjmení";
$pgv_lang["default_news_title"]		= "Vítejte ve svém rodokmenu";
$pgv_lang["default_news_text"]		= "Výstup tohoto rodokmenu je zpracován pomocí <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView 3</a>.  Tato stránka nabízí úvod a přehled k tomuto rodopisu.  Pro vstup do rodokmenu vyberte z menu jedno ze schémat, seznam osob nebo vyhledávání jména či místa.<br /><br />V případě potíží s používáním tohoto systému, klikněte na nápovědu a dozvíte se, jak pracovat se stránkou, na níž se právě nacházíte.<br /><br />Děkujeme, že jste tyto stránky navštívili.";
$pgv_lang["reset_default_blocks"]	= "Obnovit původní bloky";
$pgv_lang["recent_changes"]		= "Poslední změny";
$pgv_lang["recent_changes_block"]	= "Blok posledních změn";
$pgv_lang["recent_changes_descr"]	= "V Bloku posledních změn se zobrazí všechny změny, jež byly v GEDCOMu provedeny během posledního měsíce. Tento blok vám pomůže sledovat tyto změny. Změny se rozpoznávají podle tagu CHAN.";
$pgv_lang["delete_selected_messages"]	= "Smazat vybrané zprávy";
$pgv_lang["use_blocks_for_default"]	= "Použít tyto bloky jako implicitní nastavení bloků pro všechny uživatele?";

//-- upgrade.php messages
$pgv_lang["upgrade_util"]		= "Aktualizovat nástroj";
$pgv_lang["no_upgrade"]			= "Nejsou žádné soubory, které by bylo možné aktualizovat.";
$pgv_lang["use_version"]		= "Používáte verzi:";
$pgv_lang["current_version"]		= "Současná stabilní verze:";
$pgv_lang["upgrade_download"]		= "Stáhnout aktualizaci:";
$pgv_lang["upgrade_tar"]		= "TAR";
$pgv_lang["upgrade_zip"]		= "ZIP";
$pgv_lang["latest"]				= "Používáte aktuální verzi PhpGedView.";
$pgv_lang["location"]			= "Umístění aktualizovaných souborů: ";
$pgv_lang["include"]			= "Zahrnuto:";
$pgv_lang["options"]			= "Volby:";
$pgv_lang["inc_phpgedview"]		= " PhpGedView";
$pgv_lang["inc_languages"]		= " Jazyky";
$pgv_lang["inc_config"]			= " Konfigurační soubory";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["inc_index"]			= " Index soubory";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["inc_themes"]			= " Motivy";
$pgv_lang["inc_docs"]			= " Manuály";
$pgv_lang["inc_privacy"]		= " Soubory privátního nastavení";
$pgv_lang["inc_backup"]			= " Vytvořit zálohu";
$pgv_lang["upgrade_help"]		= " Pomoc";
$pgv_lang["cannot_read"]		= "Nelze číst soubor:";
$pgv_lang["not_configured"]		= "Ještě jste si PhpGedView nenakonfigurovali.";
$pgv_lang["location_upgrade"]	= "Prosím vyplňte umístění svých aktualizovaných souborů.";
$pgv_lang["new_variable"]		= "Nalezena nová proměnná: ";
$pgv_lang["config_open_error"] 		= "Během otevírání konfiguračního souboru se objevila chyba.";
$pgv_lang["gedcom_config_write_error"]	= "Chyba!!! Není možné zapisovat do konfiguračního souboru GEDCOMu.";
$pgv_lang["config_update_ok"]		= "Konfigurační soubor byl úspěšně aktualizován.";
$pgv_lang["config_uptodate"]		= "Váš konfigurační soubor je zastaralý.";
$pgv_lang["processing"]			= "Provádím...";
$pgv_lang["privacy_open_error"] 	= "Objevila se chyba během otevírání souboru [#PRIVACY_MODULE#].";
$pgv_lang["privacy_write_error"]	= "CHYBA!!! Není možné zapisovat do souboru [#PRIVACY_MODULE#].<br />Ujistěte se, že máte práva zápisu do souboru.<br />Současné nastavení práv může být po provedení zápisu do souboru obnoveno.";
$pgv_lang["privacy_update_ok"]		= "Soubor privátního nastavení: [#PRIVACY_MODULE#] byl úspěšně aktualizován.";
$pgv_lang["privacy_uptodate"]		= "Váš [#PRIVACY_MODULE#] soubor je zastaralý.";
$pgv_lang["heading_privacy"]		= "Soubory privátního nastavení:";
$pgv_lang["heading_phpgedview"]		= "Soubory PhpGedView:";
$pgv_lang["heading_image"]		= "Obrázkové soubory:";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["heading_index"] 		= "Index soubory:";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["heading_language"]		= "Jazykové soubory:";
$pgv_lang["heading_theme"]		= "Soubory motivů:";
$pgv_lang["heading_docs"]		= "Manuály:";

$pgv_lang["copied_success"]		= "úspěšně zkopírováno.";
$pgv_lang["backup_copied_success"]		= "záložní soubory byly úspěšně vytvořeny.";
$pgv_lang["folder_created"]		= "Vytvořena složka";
$pgv_lang["process_error"]		= "Během zpracovávání této stránky se objevila chyba. Není možné zjistit novou verzi.";
$pgv_lang["upgrade_completed"]	= "Aktualizace byla úspěšně dokončena";
$pgv_lang["start_using_upgrad"]	= "Klikněte sem pro zahájení práce s verzí";

//-- validate GEDCOM
$pgv_lang["performing_validation"]	= "Provádění validace (zkontrolování) GEDCOMu, vyberte potřebné možnosti a klikněte na 'Pokračovat'";
////////////////////////////////////////////////////////////////////////////
$pgv_lang["changed_mac"]		= "Nalezena Macintoshová zakončení řádků. Tam, kde byl jen návrat na konec (řádku?), byl vložen návrat na konec a řádek byl doplněn.";
////////////////////////////////////////////////////////////////////////////
$pgv_lang["changed_places"]		= "Rozpoznáno neplatné kódování míst. Záznamy o místech byly vyčištěny tak, aby vyhovovaly standardu GEDCOM 5.5.  Ukázka z vašeho GEDCOM:";
$pgv_lang["invalid_dates"]		= "Rozpoznány nesprávné datové formáty, vyčištěním budou tyto formáty změněny do podoby DD MMM YYYY (např. 1 JAN 2004).";
$pgv_lang["valid_gedcom"]		= "Validní GEDCOM.  Žádné opravy nebyly třeba.";
$pgv_lang["optional_tools"]		= "Před importováním můžete zvolit spuštění některého z nabízených nástrojů.";
$pgv_lang["optional"]			= "Volitelné nástroje";
$pgv_lang["date_format"]		= "Formát data:";
$pgv_lang["day_before_month"]		= "Den před měsícem (DD MM YYYY)";
$pgv_lang["month_before_day"]		= "Měsíc před dnem (MM DD YYYY)";
$pgv_lang["do_not_change"]		= "Neměnit";
$pgv_lang["change_id"]			= "Změnit ID osob na:";
$pgv_lang["example_date"]		= "Ukázka neplatného datového formátu z vašeho GEDCOMu:";
$pgv_lang["add_media_tool"]		= "Nástroj pro přidání médií";
$pgv_lang["launch_media_tool"]		= "Klikněte sem, chcete-li spustit nástroj pro přidání médií.";
$pgv_lang["add_media_descr"]		= "Tento nástroj přidá do GEDCOMu tagy OBJE.  Po ukončení přidávání médií zavřete toto okno.";
$pgv_lang["highlighted"]		= "Zvýrazněný obrázek";
$pgv_lang["extension"]			= "Přípona";
$pgv_lang["order"]			= "Pořadí";
$pgv_lang["add_media_button"]		= "Přidat média";
$pgv_lang["media_table_created"]	= "Tabulka <i>Média</i> byla úspěšně vytvořena.";
$pgv_lang["click_to_add_media"]			= "Klikněte se m pro přidání médií (ze seznamu nahoře) do tohoto GEDCOMu #GEDCOM#";
$pgv_lang["adds_completed"]		= "Média byla do GEDCOMu úspěšně přidána.";
$pgv_lang["ansi_encoding_detected"]	= "Rozpoznáno kódování ANSI.  PhpGedView pracuje nejlépe se soubory s kódováním UTF-8.";
$pgv_lang["invalid_header"]		= "V GEDCOM souboru byly nalezeny řádky před hlavičkou GEDCOM (0 HEAD). Při čištění souboru budou tyto řádky odstraněny.";
$pgv_lang["macfile_detected"]	= "Byl nalezen soubor pro Macintosh. Při čištění bude tento soubor převeden na soubor pro DOS.";
$pgv_lang["place_cleanup_detected"]	= "Bylo rozpoznáno špatné kódování míst. Tyto chyby by měly být opraveny. Následující příklad ukazuje jedno z nesprávně zapsaných míst:";
$pgv_lang["cleanup_places"]		= "Vyčištění míst";
$pgv_lang["empty_lines_detected"]	= "Ve vašem GEDCOM souboru byly nalezeny prázdné řádky. Při čištění budou tyto řádky odstraněny.";

//-- hourglass chart
$pgv_lang["hourglass_chart"]	= "Schéma přesýpacích hodin";
$pgv_lang["choose_report"]		= "Vybrat zprávu, jež se má vytvořit";
$pgv_lang["enter_report_values"]	= "Zadat kritéria pro zprávu";
$pgv_lang["selected_report"]		= "Vybraná zpráva";
$pgv_lang["run_report"]			= "Zobrazit zprávu";
$pgv_lang["select_report"]		= "Vybrat zprávu";
$pgv_lang["download_report"]			= "Stáhnout zprávu";
$pgv_lang["reports"]				= "Zprávy";
$pgv_lang["pdf_reports"]			= "Zprávy v PDF";
$pgv_lang["html_reports"]			= "Zprávy v HTML";
$pgv_lang["family_group_report"]	= "Zpráva o rodině";
$pgv_lang["page"]					= "Strana";
$pgv_lang["of"] 					= "z";
$pgv_lang["enter_famid"]			= "Zadejte ID rodiny";
$pgv_lang["show_sources"]			= "Zobrazit prameny?";
$pgv_lang["show_notes"] 			= "Zobrazit poznámky?";
$pgv_lang["show_basic"] 			= "Tisknout základní údaje, i když jsou prázdné?";
$pgv_lang["show_photos"]			= "Zobrazit fotky?";
$pgv_lang["individual_report"]		= "Zpráva o osobě";
$pgv_lang["enter_pid"]				= "Zadat ID osoby";
$pgv_lang["individual_list_report"]	= "Zpráva se seznamem osob";
$pgv_lang["generated_by"]			= "Vytvořeno v";
$pgv_lang["list_children"]			= "Seřadit všechny děti podle data narození.";
$pgv_lang["birth_report"]			= "Zpráva o datu a místě narození";
$pgv_lang["birthplace"]				= "Místem narození je";
$pgv_lang["birthdate1"]				= "Rozsah data narození začíná";
$pgv_lang["birthdate2"]				= "Rozsah data narození končí";
$pgv_lang["sort_by"]				= "Seřazeno podle";


$pgv_lang["cleanup"]			= "Opravit";
$pgv_lang["skip_cleanup"]			= "Přeskočit opravování";
$pgv_lang["dynasty_list"]		= "Přehled rodin";
$pgv_lang["make_slklist"]		= "Vytvořit EXCEL seznam (SLK)";
$pgv_lang["excel_list"]			= "Převést následující řádky do EXCEL formátu (nejdřív seznam praotců):";
$pgv_lang["excel_tab"]			= "tabulka:";
$pgv_lang["excel_create"]		= "se vytvoří ze souboru:";
$pgv_lang["patriarch_list"]		= "Seznam praotců";
$pgv_lang["slk_list"]			= "Seznam EXCEL SLK";
$pgv_lang["statistics"]			= "Statistika";
$pgv_lang["merge_records"]			= "Sloučit záznamy";
$pgv_lang["merge_same"] 			= "Záznamy nejsou stejného typu. Není možné sloučit záznamu rozdílných typů.";
$pgv_lang["merge_step1"]			= "Slučování - krok 1 ze 3";
$pgv_lang["merge_step2"]			= "Slučování - krok 2 ze 3";
$pgv_lang["merge_step3"]			= "Slučování - krok 3 ze 3";
$pgv_lang["select_gedcom_records"]	= "Označte 2 GEDCOM záznamy, které se mají sloučit. Záznamy musí být stejného typu.";
$pgv_lang["merge_to"]				= "Sloučit DO ID:";
$pgv_lang["merge_from"] 			= "Sloučit Z ID:";
$pgv_lang["merge_facts_same"]		= "Následující údaje byly v obou záznamech naprosto stejné, a budou tedy automaticky sloučeny.";
$pgv_lang["no_matches_found"]		= "Nenalezeny odpovídající údaje";
$pgv_lang["unmatching_facts"]		= "Následující údaje si neodpovídají. Označte informaci, kterou chcete zachovat.";
$pgv_lang["record"] 				= "Záznam";
$pgv_lang["adding"] 				= "Přidání";
$pgv_lang["updating_linked"]		= "Aktualizování připojené zprávy";
$pgv_lang["merge_more"] 			= "Sloučit více záznamů.";
$pgv_lang["same_ids"]				= "Zadali jste stejná ID čísla. Nemůžete sloučit údaje se sebou samými.";
$pgv_lang["ancestry_chart"] 		= "Přehled předků";
$pgv_lang["gen_ancestry_chart"]		= "Přehled předků do #PEDIGREE_GENERATIONS#. pokolení";
$pgv_lang["chart_style"]			= "Styl schématu";
$pgv_lang["ancestry_list"]			= "Seznam předků";
$pgv_lang["ancestry_booklet"]   	= "Kniha předků";
$pgv_lang["sosa_2"] 				= "Otec";
$pgv_lang["sosa_3"] 				= "Matka";
$pgv_lang["sosa_4"] 				= "Děd";
$pgv_lang["sosa_5"] 				= "Bába";
$pgv_lang["sosa_6"] 				= "Děd";
$pgv_lang["sosa_7"] 				= "Bába";
$pgv_lang["sosa_8"] 				= "Pra-děd";
$pgv_lang["sosa_9"] 				= "Pra-bába";
$pgv_lang["sosa_10"]				= "Pra-děd";
$pgv_lang["sosa_11"]				= "Pra-bába";
$pgv_lang["sosa_12"]				= "Pra-děd";
$pgv_lang["sosa_13"]				= "Pra-bába";
$pgv_lang["sosa_14"]				= "Pra-děd";
$pgv_lang["sosa_15"]				= "Pra-bába";
$pgv_lang["sosa_16"]				= "Pra-pra-děd";
$pgv_lang["sosa_17"]				= "Pra-pra-bába";
$pgv_lang["sosa_18"]				= "Pra-pra-děd";
$pgv_lang["sosa_19"]				= "Pra-pra-bába";
$pgv_lang["sosa_20"]				= "Pra-pra-děd";
$pgv_lang["sosa_21"]				= "Pra-pra-bába";
$pgv_lang["sosa_22"]				= "Pra-pra-děd";
$pgv_lang["sosa_23"]				= "Pra-pra-bába";
$pgv_lang["sosa_24"]				= "Pra-pra-děd";
$pgv_lang["sosa_25"]				= "Pra-pra-bába";
$pgv_lang["sosa_26"]				= "Pra-pra-děd";
$pgv_lang["sosa_27"]				= "Pra-pra-bába";
$pgv_lang["sosa_28"]				= "Pra-pra-děd";
$pgv_lang["sosa_29"]				= "Pra-pra-bába";
$pgv_lang["sosa_30"]				= "Pra-pra-děd";
$pgv_lang["sosa_31"]				= "Pra-pra-bába";
$pgv_lang["sosa_32"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_33"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_34"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_35"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_36"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_37"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_38"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_39"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_40"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_41"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_42"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_43"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_44"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_45"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_46"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_47"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_48"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_49"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_50"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_51"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_52"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_53"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_54"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_55"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_56"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_57"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_58"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_59"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_60"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_61"]			   = "Pra-pra-pra-bába";
$pgv_lang["sosa_62"]			   = "Pra-pra-pra-děd";
$pgv_lang["sosa_63"]			   = "Pra-pra-pra-bába";
$pgv_lang["fan_chart"]				= "Vějířové schéma";
$pgv_lang["gen_fan_chart"]  		= "#PEDIGREE_GENERATIONS# - generační vějířové schéma";
$pgv_lang["fan_width"]				= "Šířka vějíře";
$pgv_lang["gd_library"]				= "Špatná konfigurace PHP serveru: pro práci s obrázky je třeba knihovna GD 2.x.";
$pgv_lang["gd_freetype"]			= "Špatná konfigurace PHP serveru: Pro práci s TrueType fonty je potřeba knihovna Freetype.";
$pgv_lang["gd_helplink"]			= "http://www.php.net/gd";
$pgv_lang["fontfile_error"]			= "Soubor s tímto fontem nebyl na PHP serveru nalezen.";
$pgv_lang["rss_descr"]				= "Novinky a odkazy ze stránky #GEDCOM_TITLE#";
$pgv_lang["rss_logo_descr"]			= "Materiál vytvořilo PhpGedView";

if (file_exists("languages/lang.cz.extra.php")) require "languages/lang.cz.extra.php";

?>

<?php
/**
 * File contains var's to glue Help_text for PHPGedView together
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @subpackage Help
 * @author John Finlay & Jans Luder
 * @version $Id: help_text_vars.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

// The variables in this file are used to glue together other var's in the help_text.xx.php
// Do NOT put any var's, that need to be translated, in this file

require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

$pgv_lang["help_manageservers.php"]	= "#pgv_lang[help_managesites]#";
$pgv_lang["edit_RESN_help"]			= "#pgv_lang[RESN_help]#";

$pgv_lang["help_aliveinyear.php"]	= "#pgv_lang[alive_in_year_help]#";

//General
$pgv_lang["start_ahelp"]			= "<div class=\"list_value_wrap\"><center class=\"error\">#pgv_lang[start_admin_help]#</center>";
$pgv_lang["end_ahelp"]				= "<center class=\"error\">#pgv_lang[end_admin_help]#</center></div>";
$pgv_lang["redast"]				= "<span class=\"error\"<b>*</b></span>";

// Header
$pgv_lang["header_help_items"]			= "<a name=\"header\">&nbsp;</a>#pgv_lang[header_help]#<br /><a name=\"header_search\"></a><a href=\"#header\">$UpArrow </a>#pgv_lang[header_search_help]#<br /><a name=\"header_lang_select\"></a><a href=\"#header\">$UpArrow </a>#pgv_lang[header_lang_select_help]#<br /><a name=\"header_user_links\"></a><a href=\"#header\">$UpArrow </a>#pgv_lang[header_user_links_help]#<br /><a name=\"header_favorites\"></a><a href=\"#header\">$UpArrow </a>#pgv_lang[header_favorites_help]#<br /><a name=\"header_theme\"></a><a href=\"#header\">$UpArrow </a>#pgv_lang[header_theme_help]#<br />";
$pgv_lang["menu_help_items"]			= "<a name=\"menu\">&nbsp;</a>#pgv_lang[menu_help]#<a name=\"menu_fam\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_famtree_help]#<br /><a name=\"menu_myged\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_myged_help]#<a name=\"menu_charts\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_charts_help]#<a name=\"menu_lists\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_lists_help]#<a name=\"menu_annical\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_annical_help]#<a name=\"menu_clip\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_clip_help]#<a name=\"menu_search\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_search_help]#<a name=\"menu_rslog\"></a><a name=\"menu_help\"></a><a href=\"#menu\">$UpArrow </a>#pgv_lang[menu_help_help]#<br />";
$pgv_lang["index_portal_help_blocks"]		= "<a href=\"#top\">$UpArrow </a><a name=\"index_portal\">&nbsp;</a>#pgv_lang[index_portal_head_help]##pgv_lang[index_portal_help]#<br /><a name=\"index_welcome\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_welcome_help]#<br /><a name=\"index_login\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_login_help]#<br /><a name=\"index_events\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_events_help]#<br /><a name=\"index_onthisday\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_onthisday_help]#<br /><a name=\"index_favorites\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_favorites_help]#<br /><a name=\"index_stats\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_stats_help]#<br /><a name=\"index_common_surnames\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_common_names_help]#<br /><a name=\"index_media\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_media_help]#<br /><a name=\"index_loggedin\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[index_loggedin_help]#<br /><a name=\"recent_changes\"></a><a href=\"#index_portal\">$UpArrow </a>#pgv_lang[recent_changes_help]#<br />";

//Help
$pgv_lang["help_help_items"]			= "#pgv_lang[help_help]#<br />#pgv_lang[help_page_help]##pgv_lang[help_content_help]##pgv_lang[help_faq_help]##pgv_lang[help_HS_help]##pgv_lang[help_qm_help]#";
$pgv_lang["def_help_items"]			= "<a name=\"def\">&nbsp;</a>#pgv_lang[def_help]#<br /><a name=\"def_gedcom\"></a><a href=\"#def\">$UpArrow </a>#pgv_lang[def_gedcom_help]#<br /><a name=\"def_gedcom_date\"></a><a href=\"#def\">$UpArrow </a>#pgv_lang[def_gedcom_date_help]#<br /><a name=\"def_pdf_format\"></a><a href=\"#def\">$UpArrow </a>#pgv_lang[def_pdf_format_help]#<br /><a name=\"def_pgv\"></a><a href=\"#def\">$UpArrow </a>#pgv_lang[def_pgv_help]#<br /><a name=\"def_portal\"></a><a href=\"#def\">$UpArrow </a>#pgv_lang[def_portal_help]#<br /><a name=\"def_theme\"></a><a href=\"#def\">$UpArrow </a>#pgv_lang[def_theme_help]#<br />";

// edit_user.php (My account)
$pgv_lang["edituser_user_contact_help"]		= "#pgv_lang[edituser_contact_meth_help]#<br /><br /><b>#pgv_lang[messaging]#</b><br />#pgv_lang[mail_option1_help]#<br /><b>#pgv_lang[messaging2]#</b><br />#pgv_lang[mail_option2_help]#<br /><b>#pgv_lang[mailto]#</b><br />#pgv_lang[mail_option3_help]#<br /><b>#pgv_lang[no_messaging]#</b><br />#pgv_lang[mail_option4_help]#<br />";
$pgv_lang["help_edituser.php"]			= "~#pgv_lang[myuserdata]#~<br /><br />#pgv_lang[edituser_my_account_help]#<br />#pgv_lang[more_help]#";

// user_admin.php
$pgv_lang["help_useradmin.php"]			= "#pgv_lang[useradmin_help]#<br /><br />#pgv_lang[is_user_help]#<br />#pgv_lang[more_help]#";
$pgv_lang["useradmin_user_contact_help"]	= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_user_contact_help]#";
$pgv_lang["useradmin_change_lang_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_change_lang_help]#";
$pgv_lang["useradmin_email_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_email_help]#";
$pgv_lang["useradmin_user_theme_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_user_theme_help]#";
// these need to be checked and maybe moved to the help_text.en.php
$pgv_lang["useradmin_username_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_username_help]#";
$pgv_lang["useradmin_firstname_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_firstname_help]#";
$pgv_lang["useradmin_lastname_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_lastname_help]#";
$pgv_lang["useradmin_password_help"]		= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_password_help]#";
$pgv_lang["useradmin_conf_password_help"]	= "#pgv_lang[is_user_help]#<br /><br />#pgv_lang[edituser_conf_password_help]#";
$pgv_lang["edit_useradmin_help"]		= "#pgv_lang[useradmin_edit_user_help]#<br />#pgv_lang[more_help]#";

// general help items used in help welcome page
$pgv_lang["general_help"]			= "<a name=\"header_general\">&nbsp;</a>#pgv_lang[header_general_help]##pgv_lang[best_display_help]#<br />#pgv_lang[preview_help]#<br />";

// page help for the Welcome page
$pgv_lang["help_index.php"]			= "#pgv_lang[index_help]#<br />#pgv_lang[index_portal_help_blocks]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[header_help_items]#<br /><br /><a href=\"#top\">$UpArrow </a>#pgv_lang[menu_help_items]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[general_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[def_help_items]#<br />";

// page help for the MyGedView page
$pgv_lang["mygedview_portal_help_blocks"]	= "<a name=\"mygedview_portal\"></a>#pgv_lang[mygedview_portal_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_welcome\"></a>#pgv_lang[mygedview_welcome_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_customize\"></a>#pgv_lang[mygedview_customize_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_message\"></a>#pgv_lang[mygedview_message_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_events\"></a>#pgv_lang[index_events_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_onthisday\"></a>#pgv_lang[index_onthisday_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_favorites\"></a>#pgv_lang[mygedview_favorites_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_stats\"></a>#pgv_lang[index_stats_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_myjournal\"></a>#pgv_lang[mygedview_myjournal_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_media\"></a>#pgv_lang[index_media_help]#<br /><a href=\"#mygedview_portal\">$UpArrow </a><a name=\"mygedview_loggedin\"></a>#pgv_lang[index_loggedin_help]#<br /><a name=\"mygedview_recent_changes\"></a><a href=\"#mygedview_portal\">$UpArrow </a>#pgv_lang[recent_changes_help]#<br />";
$pgv_lang["index_myged_help"]			= "#pgv_lang[mygedview_portal_help_blocks]#<br />";


//Login
$pgv_lang["help_login.php"]			= "#pgv_lang[login_page_help]#<br />#pgv_lang[mygedview_login_help]#";
$pgv_lang["help_login_register.php"]		= "~#pgv_lang[requestaccount]#~<br /><br />#pgv_lang[register_info_01]#";
$pgv_lang["help_login_lost_pw.php"]		= "~#pgv_lang[lost_pw_reset]#~<br /><br />#pgv_lang[pls_note11]#";
$pgv_lang["index_login_register_help"]		= "#pgv_lang[index_login_help]#<br />#pgv_lang[new_user_help]#<br /><br />#pgv_lang[new_password_help]#<br />";

//Add Facts
$pgv_lang["add_new_facts_help"]			= "#pgv_lang[multiple_help]#<br />#pgv_lang[add_facts_help]#<br />#pgv_lang[add_custom_facts_help]#<br />#pgv_lang[add_from_clipboard_help]#<br />#pgv_lang[def_gedcom_date_help]#<br />#pgv_lang[add_facts_general_help]#";

//Admin Help News Block
$pgv_lang["index_gedcom_news_ahelp"]		= "#pgv_lang[index_gedcom_news_help]##pgv_lang[start_ahelp]##pgv_lang[index_gedcom_news_adm_help]##pgv_lang[end_ahelp]#";

//Admin Help Advanced HTML Block
$pgv_lang["index_htmlplus_ahelp"]		= "#pgv_lang[index_htmlplus_help]##pgv_lang[start_ahelp]##pgv_lang[index_htmlplus_content_help]##pgv_lang[end_ahelp]#";

/*
//Upgrade Utility
$pgv_lang["help_upgrade.php"]			="#pgv_lang[how_upgrade_help]#<br /><br />#pgv_lang[readme_help]#";
*/

//-- Admin
$pgv_lang["help_admin.php"]			="~#pgv_lang[administration]#~</b><br /><br />#pgv_lang[admin_help]#<br /><br />#pgv_lang[readme_help]#";

//-- Language editor and configuration
$pgv_lang["help_editlang.php"]			="#pgv_lang[lang_edit_help]#<br /><br />#pgv_lang[translation_forum_help]#<br /><br />#pgv_lang[bom_check_help]#<br /><br />#pgv_lang[edit_lang_utility_help]#<br /><br />#pgv_lang[export_lang_utility_help]#<br /><br />#pgv_lang[compare_lang_utility_help]#<br /><br />#pgv_lang[add_new_language_help]#<br /><br />#pgv_lang[more_help]#";
$pgv_lang["help_changelanguage.php"]			="#pgv_lang[config_lang_utility_help]##pgv_lang[more_help]#";

//-- User Migrate and Backup tool
$pgv_lang["help_usermigrate.php"]	="#pgv_lang[um_tool_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[um_sql_index_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[um_index_sql_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[um_bu_help]#";

//-- FAQ List editing tool
$pgv_lang["faq_page_help"]	=	"#pgv_lang[help_faq.php]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[preview_faq_item_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[restore_faq_edits_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[add_faq_item_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[edit_faq_item_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[delete_faq_item_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[moveup_faq_item_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[movedown_faq_item_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[add_faq_header_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[add_faq_body_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[add_faq_order_help]#";

//--				G E D C O M
//-- Gedcom Info
$pgv_lang["gedcom_info_help"]			= "<div class=\"name_head center\"><b>#pgv_lang[help_contents_gedcom_info]#</b></div><br />#pgv_lang[def_gedcom_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[def_gedcom_date_help]#<br /><a href=\"#top\">$UpArrow </a>#pgv_lang[ppp_levels_help]#";

//-- Add Gedcom
$pgv_lang["help_addgedcom.php"]			="#pgv_lang[add_gedcom_help]#<br /><br />#pgv_lang[add_upload_gedcom_help]#<br />#pgv_lang[readme_help]#";
//-- Add new Gedcom
$pgv_lang["help_addnewgedcom.php"]		="#pgv_lang[add_new_gedcom_help]#<br /><br />#pgv_lang[readme_help]#";
//-- Download Gedcom
$pgv_lang["help_downloadgedcom.php"]		="#pgv_lang[download_gedcom_help]#";
//-- Edit Gedcoms
$pgv_lang["help_editgedcoms.php"]		="#pgv_lang[edit_gedcoms_help]#";
//-- Edit Config Gedcoms
$pgv_lang["help_editconfig_gedcom.php"]		="#pgv_lang[edit_config_gedcom_help]##pgv_lang[more_config_hjaelp]#<br /><br />#pgv_lang[readme_help]#";
//-- Import Gedcom
$pgv_lang["help_importgedcom.php"]		="#pgv_lang[import_gedcom_help]#";
//-- Upload Gedcom
$pgv_lang["help_uploadgedcom.php"]		="#pgv_lang[upload_gedcom_help]#<br /><br />#pgv_lang[add_upload_gedcom_help]#<br />#pgv_lang[readme_help]#";
//-- Validate Gedcom
$pgv_lang["help_validategedcom.php"]		="#pgv_lang[validate_gedcom_help]#";
//-- Edit Privacy
$pgv_lang["help_edit_privacy.php"]		="~#pgv_lang[edit_privacy_title]#~<br /><br />#pgv_lang[edit_privacy_help]##pgv_lang[more_config_hjaelp]#<br />#pgv_lang[readme_help]#";

//Specials for contents
$vpos = strpos($pgv_lang["enter_terms"], ":", 0);
if ($vpos>0) $enter_terms = substr($pgv_lang["enter_terms"], 0, $vpos);
else $enter_terms = $pgv_lang["enter_terms"];
$vpos = strpos($pgv_lang["soundex_search"], ":", 0);
if ($vpos>0) $soundex_search = substr($pgv_lang["soundex_search"], 0, $vpos);
else $soundex_search = $pgv_lang["soundex_search"];

$pgv_lang["help_used_in_contents"]		= "<div class=\"name_head center\"><b>#pgv_lang[page_help]#</b></div><br />#pgv_lang[help_help_items]#";
$pgv_lang["search_used_in_contents"]		= "<div class=\"name_head center\"><b>#pgv_lang[search]#</b></div><ul><li><a href=\"#header_search\">#pgv_lang[header]#</a><li><a href=\"#menu_search\">#pgv_lang[menu]#</a><li><a href=\"#help_search\">#pgv_lang[search]#<li><a href=\"#search_enter_terms\">$enter_terms</a><li><a href=\"#soundex_search\">$soundex_search</ul><br /><br /><a href=\"#top\">$UpArrow </a><a name=\"header_search\"></a>#pgv_lang[header_search_help]#<br /><br /><a href=\"#top\">$UpArrow </a><a name=\"menu_search\"></a>#pgv_lang[menu_search_help]#<br /><a href=\"#top\">$UpArrow </a><a name=\"help_search\"></a>#pgv_lang[help_search.php]#<br /><a href=\"#top\">$UpArrow </a><a name=\"search_enter_terms\"></a>#pgv_lang[search_enter_terms_help]#<br /><br /><br /><a name=\"soundex_search\"></a><a href=\"#top\">$UpArrow </a>#pgv_lang[soundex_search_help]#";


/*-- Var's for Menu Item: Help contents
	The var $pgv_lang["help_contents_help"] contains all the vars below.
	example: $pgv_lang["h1"] >>> help_index.php will be the var $pgv_lang["help_index.php"],
	to be displayed if the text of $pgv_lang["welcome_page"] is clicked in the Help Contents
*/
$pgv_lang["h1"]		= "help_index.php,welcome_page";
$pgv_lang["h2"]		= "index_myged_help,mygedview";
$pgv_lang["h3"]		= "help_calendar.php,anniversary_calendar";
$pgv_lang["h4"]		= "help_clippings.php,clip_cart";
$pgv_lang["h5"]		= "help_descendancy.php,descend_chart";
$pgv_lang["h6"]		= "help_edituser.php,editowndata";
$pgv_lang["h7"]		= "gedcom_info_help,help_contents_gedcom_info";
$pgv_lang["h8"]		= "help_family.php,family_info";
$pgv_lang["h9"]		= "help_famlist.php,family_list";
$pgv_lang["h10"]	= "header_help_items,header";
$pgv_lang["h11"]	= "help_individual.php,indi_info";
$pgv_lang["h12"]	= "help_indilist.php,individual_list";
$pgv_lang["h13"]	= "help_login.php,login";
$pgv_lang["h14"]	= "menu_help_items,menu";
$pgv_lang["h15"]	= "help_medialist.php,media_list";
$pgv_lang["h16"]	= "help_relationship.php,relationship_chart";
$pgv_lang["h17"]	= "best_display_help,resolution";
$pgv_lang["h18"]	= "search_used_in_contents,search";
$pgv_lang["h19"]	= "help_source.php,source";
$pgv_lang["h20"]	= "help_sourcelist.php,source_list";
$pgv_lang["h21"]	= "help_pedigree.php,index_header";
$pgv_lang["h22"]	= "preview_help,print_preview";
$pgv_lang["h23"]	= "help_placelist.php,place_list";
$pgv_lang["h24"]	= "help_timeline.php,timeline_chart";
$pgv_lang["h25"]	= "help_used_in_contents,page_help";
$pgv_lang["h26"]	= "edituser_password_help,password";
$pgv_lang["h27"]	= "edituser_username_help,username";
$pgv_lang["h28"]	= "add_media_help,add_media_lbl";
$pgv_lang["h29"]	= "help_login_register.php,requestaccount";
$pgv_lang["h30"]	= "help_login_lost_pw.php,lost_pw_reset";
$pgv_lang["h31"]	= "help_ancestry.php,ancestry_chart";
$pgv_lang["h32"]	= "help_fanchart.php,fan_chart";
$pgv_lang["h33"]	= "help_reportengine.php,reports";
$pgv_lang["h34"]	= "def_help_items,definitions";
$pgv_lang["h35"]	= "accesskey_viewing_advice_help,accesskeys";
$pgv_lang["h36"]	= "help_faq.php,faq_list";
$pgv_lang["h37"]	= "hs_title_help,hs_title";

$pgv_lang["help_contents_help"] = "";
$i=1;
while (isset($pgv_lang["h$i"])) {
	$Which = "h".$i;
	if ($pgv_lang[$Which]!="") $pgv_lang["help_contents_help"] .= "#pgv_lang[h$i]#";
	$i++;
}




//-- Help Contents for admin

// $pgv_lang["ah1"]	= "how_upgrade_help,ah1_help";
$pgv_lang["ah1"]	= "";
$pgv_lang["ah2"]	= "help_editconfig.php,ah2_help";
$pgv_lang["ah3"]	= "add_upload_gedcom_help,ah3_help";
$pgv_lang["ah4"]	= "gedcom_configfile_help,ah4_help";
$pgv_lang["ah5"]	= "default_gedcom_help,ah5_help";
$pgv_lang["ah6"]	= "delete_gedcom_help,ah6_help";
$pgv_lang["ah7"]	= "add_gedcom_help,ah7_help";
$pgv_lang["ah8"]	= "add_new_gedcom_help,ah8_help";
$pgv_lang["ah9"]	= "download_gedcom_help,ah9_help";
$pgv_lang["ah10"]	= "edit_gedcoms_help,ah10_help";
$pgv_lang["ah11"]	= "edit_config_gedcom_help,ah11_help";
$pgv_lang["ah12"]	= "import_gedcom_help,ah12_help";
$pgv_lang["ah13"]	= "upload_gedcom_help,ah13_help";
$pgv_lang["ah14"]	= "validate_gedcom_help,ah14_help";
$pgv_lang["ah15"]	= "convert_ansi2utf_help,ah15_help";
$pgv_lang["ah16"]	= "help_edit_privacy.php,ah16_help";
$pgv_lang["ah17"]	= "help_useradmin.php,ah17_help";
$pgv_lang["ah18"]	= "help_admin.php,ah18_help";
$pgv_lang["ah19"]	= "addmedia_tool_help,ah19_help";
$pgv_lang["ah20"]	= "change_indi2id_help,ah20_help";
$pgv_lang["ah21"]	= "help_editlang.php,ah21_help";
$pgv_lang["ah22_help"]	= "_Readme.txt";
$pgv_lang["ah22"]	= "readme_help,ah22_help";
$pgv_lang["ah23"]	= "help_changelanguage.php,ah23_help";
$pgv_lang["ah24"]	= "um_tool_help,ah24_help";
$pgv_lang["ah25"]	= "um_bu_help,ah25_help";
$pgv_lang["ah26"]	= "faq_page_help,ah26_help";

$pgv_lang["a_help_contents_help"] = "";
$i=1;
while (isset($pgv_lang["ah$i"])) {
	$Which = "ah".$i;
	if ($pgv_lang[$Which]!="") $pgv_lang["a_help_contents_help"] .= "#pgv_lang[ah$i]#";
	$i++;
}

$pgv_lang["admin_help_contents_help"]		=$pgv_lang["help_contents_help"].$pgv_lang["a_help_contents_help"];

?>

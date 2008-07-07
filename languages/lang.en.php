<?php
/**
 * English texts
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team
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
 *
 * @author PGV Developers
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access a language file directly.";
	exit;
}

$pgv_lang["age_differences"]		= "Show Date Differences";
$pgv_lang["date_of_entry"]			= "Date of entry in original source";
$pgv_lang["multi_site_search"] 		= "Multi Site Search";
$pgv_lang["switch_lifespan"]		= "Show Lifespan chart";
$pgv_lang["switch_timeline"]		= "Show Timeline chart";
$pgv_lang["differences"]			= "Differences";
$pgv_lang["charts_block"]			= "Charts Block";
$pgv_lang["charts_block_descr"]		= "The Charts block allows you to place a chart on the Welcome or the MyGedView portal page.  You can configure the block to show an ancestors, descendants, or hourglass view.  You can also choose the root person for the chart.";
$pgv_lang["charts_click_box"]		= "Click on any of the boxes to get more information about that person.";
$pgv_lang["chart_type"]				= "Chart Type";
$pgv_lang["changedate1"]			= "Ending range of change dates";
$pgv_lang["changedate2"]			= "Starting range of change dates";
$pgv_lang["search_place_word"]		= "Whole words only";
$pgv_lang["invalid_search_input"] 	= "Please enter a Given name, Last name, or Place in addition to Year";
$pgv_lang["duplicate_username"] 	= "Duplicate user name.  A user with that user name already exists.  Please choose another user name.";
$pgv_lang["cache_life"]				= "Cache file life";
$pgv_lang["genealogy"]					= "genealogy";
$pgv_lang["activate"]					= "Activate";
$pgv_lang["deactivate"]					= "Deactivate";
$pgv_lang["play"]					= "Play";
$pgv_lang["stop"]					= "Stop";
$pgv_lang["random_media_start_slide"]	= "Start slideshow on page load?";
$pgv_lang["random_media_ajax_controls"]	= "Show slideshow controls?";
$pgv_lang["description"]			= "Description";
$pgv_lang["current_dir"]			= "Current directory";
$pgv_lang["SHOW_ID_NUMBERS"]		= "Show ID numbers next to names";
$pgv_lang["SHOW_HIGHLIGHT_IMAGES"]	= "Show highlight images in people boxes";
$pgv_lang["view_img_details"]		= "View image details";
$pgv_lang["server_folder"]			= "Folder name on server";
$pgv_lang["medialist_recursive"]	= "List files in subdirectories";
$pgv_lang["media_options"]			= "Media Options";
$pgv_lang["confirm_password"]					= "You must confirm the password.";
$pgv_lang["enter_email"]						= "You must enter an email address.";
$pgv_lang["enter_fullname"] 					= "You must enter a first and last name.";
$pgv_lang["name"]					= "Name";
$pgv_lang["children"]				= "Children";
$pgv_lang["child"]					= "Child";
$pgv_lang["family"] 				= "Family";
$pgv_lang["as_child"]				= "Family with Parents";
$pgv_lang["source_menu"]			= "Options for source";
$pgv_lang["other_records"]			= "Records that link to this Source:";
$pgv_lang["other_repo_records"]		= "Records that link to this Repository:";
$pgv_lang["repo_info"]				= "Repository Information";
$pgv_lang["enter_terms"]			= "Enter Search terms";
$pgv_lang["search_asso_label"]		= "Associates";
$pgv_lang["search_asso_text"]		= "Show related persons/families";
$pgv_lang["search_DM"]				= "Daitch-Mokotoff";
$pgv_lang["search_fams"]			= "Family Names";
$pgv_lang["search_gedcom"]			= "Search databases";
$pgv_lang["search_geds"]			= "Databases to search in";
$pgv_lang["search_indis"]			= "Individual Names";
$pgv_lang["search_inrecs"]			= "Search for";
$pgv_lang["search_prtall"]			= "All names";
$pgv_lang["search_prthit"]			= "Names with hit";
$pgv_lang["results_per_page"]		= "Results per page";
$pgv_lang["firstname_search"]		= "Given name";
$pgv_lang["search_prtnames"]		= "Individuals'<br />names to print:";
$pgv_lang["other_searches"]			= "Other Searches";
$pgv_lang["add_to_cart"]			= "Add to Clippings Cart";
$pgv_lang["view_gedcom"]			= "View GEDCOM Record";
$pgv_lang["welcome"]				= "Welcome";
$pgv_lang["son"]					= "Son";
$pgv_lang["daughter"]				= "Daughter";
$pgv_lang["welcome_page"]			= "Welcome Page";
$pgv_lang["editowndata"]			= "My Account";
$pgv_lang["user_admin"] 			= "User administration";
$pgv_lang["manage_media"]			= "Manage Media";
$pgv_lang["search_general"]			= "General search";
$pgv_lang["clipping_privacy"]		= "Some items could not be added due to privacy restrictions";
$pgv_lang["chart_new"]				= "Family Tree Chart";
$pgv_lang["loading"]				= "Loading...";
$pgv_lang["clear_chart"]			= "Clear Chart";
$pgv_lang["file_information"]		= "File Information";
$pgv_lang["choose_file_type"]		= "Choose File Type";
$pgv_lang["add_individual_by_id"]		= "Add Individual By ID";
$pgv_lang["advanced_options"]		= "Advanced Options";
$pgv_lang["zip_files"]				= "Zip File(s)";
$pgv_lang["include_media"]			= "Include Media (automatically zips files)";
$pgv_lang["roman_surn"]				= "Romanized Surname";
$pgv_lang["roman_givn"]				= "Romanized Given Names";
$pgv_lang["include"]				= "Include:";
$pgv_lang["page_x_of_y"]				= "Page #GLOBALS[currentPage]# of #GLOBALS[lastPage]#";
$pgv_lang["options"]				= "Options:";
$pgv_lang["config_update_ok"]			= "Configuration file updated successfully.";
$pgv_lang["page_size"]					= "Page size";
$pgv_lang["record_not_found"]			= "The requested GEDCOM record could not be found.  This could be caused by a link to an invalid person or by a corrupt GEDCOM file.";
$pgv_lang["result_page"]				= "Result Page";
$pgv_lang["edit_media"]					= "Edit Media Item";
$pgv_lang["wiki_main_page"]				= "Wiki Main Page";
$pgv_lang["wiki_users_guide"]			= "Wiki User's Guide";
$pgv_lang["wiki_admin_guide"]			= "Wiki Administrator's Guide";
$pgv_lang["no_search_for"]			= "Be sure to select an option to search for.";
$pgv_lang["no_search_site"]			= "Be sure to select at least one remote site.";
$pgv_lang["search_sites"] 			= "Sites to search";
$pgv_lang["site_list"]				= "Site: ";
$pgv_lang["site_had"]				= " contained the following";
$pgv_lang["label_search_engine_detected"]  = "Search Engine Spider Detected";

$pgv_lang["ex-spouse"] = "Ex-Spouse";
$pgv_lang["ex-wife"] = "Ex-Wife";
$pgv_lang["ex-husband"] = "Ex-Husband";
$pgv_lang["noemail"] 				= "Addresses without emails";
$pgv_lang["onlyemail"] 				= "Only addresses with emails";
$pgv_lang["maxviews_exceeded"]		= "Permitted page view rate of #GLOBALS[MAX_VIEWS]# per #GLOBALS[MAX_VIEW_TIME]# seconds exceeded.";
$pgv_lang["broadcast_not_logged_6mo"]	= "Send message to users who have not logged in for 6 months";
$pgv_lang["broadcast_never_logged_in"]	= "Send message to users who have never logged in";
$pgv_lang["stats_to_show"]			= "Select the stats to show in this block";
$pgv_lang["stat_avg_age_at_death"]	= "Average age at death";
$pgv_lang["stat_longest_life"]		= "Person who lived the longest";
$pgv_lang["stat_most_children"]		= "Family with the most children";
$pgv_lang["stat_average_children"]	= "Average number of children per family";
$pgv_lang["stat_events"]			= "Total events";
$pgv_lang["stat_media"]				= "Media objects";
$pgv_lang["stat_surnames"]			= "Total surnames";
$pgv_lang["stat_users"]				= "Total users";
$pgv_lang["no_family_facts"]		= "No facts for this family.";
$pgv_lang["stat_males"]				= "Total males";
$pgv_lang["stat_females"]			= "Total females";

$pgv_lang["sunday_1st"]				= "Su";
$pgv_lang["monday_1st"]				= "M";
$pgv_lang["tuesday_1st"]			= "Tu";
$pgv_lang["wednesday_1st"]			= "W";
$pgv_lang["thursday_1st"]			= "Th";
$pgv_lang["friday_1st"]				= "F";
$pgv_lang["saturday_1st"]			= "Sa";

$pgv_lang["jan_1st"]					= "Jan";
$pgv_lang["feb_1st"]					= "Feb";
$pgv_lang["mar_1st"]					= "March";
$pgv_lang["apr_1st"]					= "April";
$pgv_lang["may_1st"]					= "May";
$pgv_lang["jun_1st"]					= "June";
$pgv_lang["jul_1st"]					= "July";
$pgv_lang["aug_1st"]					= "Aug";
$pgv_lang["sep_1st"]					= "Sep";
$pgv_lang["oct_1st"]					= "Oct";
$pgv_lang["nov_1st"]					= "Nov";
$pgv_lang["dec_1st"]					= "Dec";

$pgv_lang["edit_source"]			= "Edit Source";
$pgv_lang["familybook_chart"]		= "Family Book Chart";
$pgv_lang["family_of"]				= "Family of:&nbsp;";
$pgv_lang["descent_steps"]			= "Descent Steps";

$pgv_lang["cancel"]					= "Cancel";
$pgv_lang["cookie_help"]			= "This site uses cookies to keep track of your login status.<br /><br />Cookies do not appear to be enabled in your browser. You must enable cookies for this site before you can login.  You can consult your browser's help documentation for information on enabling cookies.";
//new stuff
//Individual
$pgv_lang["indi_is_remote"]			= "The information for this individual was linked from a remote site.";
$pgv_lang["link_remote"]            = "Link remote person";
//Add Remote Link
$pgv_lang["title_search_link"]      = "Add Local Link";
$pgv_lang["label_site_url2"]        = "Site URL";
//new stuff

$pgv_lang["delete_family_confirm"]	= "Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?";
$pgv_lang["delete_family"]			= "Delete family";
$pgv_lang["add_favorite"]			= "Add a new favorite";
$pgv_lang["url"]					= "URL";
$pgv_lang["add_fav_enter_note"]		= "Enter an optional note about this favorite";
$pgv_lang["add_fav_or_enter_url"]	= "OR<br />\nEnter a URL and a title";
$pgv_lang["add_fav_enter_id"]		= "Enter a Person, Family, or Source ID";
$pgv_lang["next_email_sent"]		= "Next email reminder will be sent after ";
$pgv_lang["last_email_sent"]		= "Last email reminder was sent ";
$pgv_lang["remove_child"]			= "Remove this child from the family";
$pgv_lang["link_new_husb"]			= "Add a husband using an existing person";
$pgv_lang["link_new_wife"]			= "Add a wife using an existing person";
$pgv_lang["address_labels"]			= "Address Labels";
$pgv_lang["filter_address"]			= "Show addresses that contain:";
$pgv_lang["address_list"]			= "Address List";
$pgv_lang["autocomplete"]			= "Autocomplete";
$pgv_lang["index_edit_advice"]		= "Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.";
$pgv_lang["changelog"]				= "Version #VERSION# changes";
$pgv_lang["html_block_descr"]		= "This is a simple HTML block that you can place on your page to add any sort of message you may want.";
$pgv_lang["html_block_sample_part1"]	= "<p class=\"blockhc\"><b>Put your title here</b></p><br /><p>Click the configure button";
$pgv_lang["html_block_sample_part2"]	= "to change what is printed here.</p>";
$pgv_lang["html_block_name"]		= "HTML";
$pgv_lang["htmlplus_block_name"]	= "Advanced HTML";
$pgv_lang["htmlplus_block_descr"]	= "This is an HTML block that you can place on your page to add any sort of message you may want.  You can insert references to information from your GEDCOM into the HTML text.";
$pgv_lang["htmlplus_block_templates"] = "Templates";
$pgv_lang["htmlplus_block_content"] = "Content";
$pgv_lang["htmlplus_block_narrative"] = "Narrative style (English only)";
$pgv_lang["htmlplus_block_custom"]	= "Custom";
$pgv_lang["htmlplus_block_keyword"]	= "Keyword Examples (English only)";
$pgv_lang["htmlplus_block_taglist"]	= "Tag List";
$pgv_lang["htmlplus_block_compat"]	= "Compatibility Mode";
$pgv_lang["htmlplus_block_current"]	= "Current";
$pgv_lang["htmlplus_block_default"]	= "Default";
$pgv_lang["htmlplus_block_gedcom"]	= "Family Tree";
$pgv_lang["htmlplus_block_birth"]	= "birth";
$pgv_lang["htmlplus_block_death"]	= "death";
$pgv_lang["htmlplus_block_marrage"]	= "marriage";
$pgv_lang["htmlplus_block_adoption"]= "adoption";
$pgv_lang["htmlplus_block_burial"]	= "burial";
$pgv_lang["htmlplus_block_census"]	= "census added";
$pgv_lang["num_to_show"]			= "Number of items to show";
$pgv_lang["days_to_show"]			= "Number of days to show";
$pgv_lang["before_or_after"]		= "Place counts before or after name?";
$pgv_lang["before"]					= "before";
$pgv_lang["after"]					= "after";
$pgv_lang["config_block"]			= "Configure";
$pgv_lang["enter_comments"]			= "Please enter your relationship to the data in the Comments field.";
$pgv_lang["comments"]				= "Comments";
$pgv_lang["child-family"]			= "Parents and siblings";
$pgv_lang["spouse-family"]			= "Spouse and children";
$pgv_lang["direct-ancestors"]		= "Direct line ancestors";
$pgv_lang["ancestors"]				= "Direct line ancestors and their families";
$pgv_lang["descendants"]			= "Descendants";
$pgv_lang["choose_relatives"]		= "Choose relatives";
$pgv_lang["relatives_report"]		= "Relatives Report";
$pgv_lang["total_living"]			= "Total living";
$pgv_lang["total_dead"]				= "Total dead";
$pgv_lang["total_not_born"]			= "Total not yet born";
$pgv_lang["remove_custom_tags"]		= "Remove custom PGV tags? (eg. _PGVU, _THUM)";
$pgv_lang["cookie_login_help"]		= "This site remembered you from a previous login.  This allows you to access private information and other user-based features, but in order to edit or administer the site, you must login again for increased security.";
$pgv_lang["remember_me"]			= "Remember me from this computer?";
$pgv_lang["fams_with_surname"]		= "Families with surname #surname#";
$pgv_lang["support_contact"]		= "Technical help contact";
$pgv_lang["genealogy_contact"]		= "Genealogy contact";
$pgv_lang["common_upload_errors"]	= "This error probably means that the file you tried to upload exceeded the limit set by your host.  The default limit in PHP is 2MB.  You can contact your host's Support group to have them increase the limit in the php.ini file, or you can upload the file using FTP.  Use the <a href=\"uploadgedcom.php?action=add_form\"><b>Add GEDCOM</b></a> page to add a GEDCOM file you have uploaded using FTP.";
$pgv_lang["total_memory_usage"]		= "Total Memory Usage:";
$pgv_lang["mothers_family_with"]	= "Mother's Family with ";
$pgv_lang["fathers_family_with"]	= "Father's Family with ";
$pgv_lang["family_with"]			= "Family with";
$pgv_lang["halfsibling"]			= "Half-Sibling";
$pgv_lang["halfbrother"]			= "Half-Brother";
$pgv_lang["halfsister"]				= "Half-Sister";
$pgv_lang["family_timeline"]		= "Show family on timeline chart";
$pgv_lang["children_timeline"]		= "Show children on timeline chart";
$pgv_lang["other"]					= "Other";
$pgv_lang["sort_by_marriage"]		= "Sort by marriage date";
$pgv_lang["reorder_families"]		= "Reorder Families";
$pgv_lang["indis_with_surname"]		= "Individuals with surname #surname#";
$pgv_lang["first_letter_fname"]		= "Choose a letter to show individuals whose first name starts with that letter.";
$pgv_lang["total_names"]			= "Total Names";
$pgv_lang["top10_pageviews_nohits"]	= "There are currently no hits to show.";
$pgv_lang["top10_pageviews_msg"]	= "Hit counters must be enabled in the GEDCOM configuration, Display and Layout section, Hide and Show group.";
$pgv_lang["review_changes_descr"]	= "The Pending Changes block will give users with Edit rights a list of the records that have been changed online and that still need to be reviewed and accepted.  These changes are pending acceptance or rejection.<br /><br />If this block is enabled, users with Accept rights will receive an email once a day notifying them that changes need to be reviewed.";
$pgv_lang["review_changes_block"]	= "Pending Changes";
$pgv_lang["review_changes_email"]	= "Send out reminder emails?";
$pgv_lang["review_changes_email_freq"]	= "Reminder email frequency (days)";
$pgv_lang["review_changes_subject"]	= "PhpGedView - Review changes";
$pgv_lang["review_changes_body"]	= "Online changes have been made to a genealogical database.  These changes need to be reviewed and accepted before they will appear to all users.  Please use the URL below to enter that PhpGedView site and login to review the changes.";
$pgv_lang["show_pending"]		= "Show pending changes";
$pgv_lang["show_spouses"]		= "Show spouses";
$pgv_lang["quick_update_title"] = "Quick Update";
$pgv_lang["quick_update_instructions"] = "This page allows you to quickly update information for an individual.  You only need to fill out the information that is new or that has changed from what is currently in the database.  After your changes have been submitted they have to be reviewed by an administrator before they will become visible to all.";
$pgv_lang["update_name"] = "Update Name";
$pgv_lang["update_fact"] = "Update a Fact";
$pgv_lang["update_fact_restricted"] = "Update of this fact is restricted:";
$pgv_lang["update_photo"] = "Update Photo";
$pgv_lang["select_fact"] = "Select a fact...";
$pgv_lang["update_address"] = "Update Address";
$pgv_lang["top10_pageviews_descr"]	= "This block will show the 10 records that have been viewed the most.  This block requires that Hit Counters be enabled in the GEDCOM configuration settings.";
$pgv_lang["top10_pageviews"]		= "Most Viewed Items";
$pgv_lang["top10_pageviews_block"]		= "Most Viewed Items";
$pgv_lang["stepdad"]				= "Step-Father";
$pgv_lang["stepmom"]				= "Step-Mother";
$pgv_lang["stepsister"]				= "Step-Sister";
$pgv_lang["stepbrother"]			= "Step-Brother";
$pgv_lang["fams_charts"]			= "Options for family";
$pgv_lang["indis_charts"]			= "Options for individual";
$pgv_lang["none"]					= "None";
$pgv_lang["locked"]					= "Do not change";
$pgv_lang["privacy"]				= "Privacy";
$pgv_lang["number_sign"]			= "#";

//-- GENERAL HELP MESSAGES
$pgv_lang["qm"] 					= "?";
$pgv_lang["qm_ah"]					= "?";
$pgv_lang["page_help"]				= "Help";
$pgv_lang["help_for_this_page"] 	= "Help with this page";
$pgv_lang["help_contents"]			= "Help Contents";
$pgv_lang["show_context_help"]		= "Show Contextual Help";
$pgv_lang["hide_context_help"]		= "Hide Contextual Help";
$pgv_lang["sorry"]					= "<b>Sorry, Help text for this page or item is not yet available.</b>";
$pgv_lang["help_not_exist"] 		= "<b>Help text for this page or item is not yet available.</b>";
$pgv_lang["var_not_exist"]			= "<span style=\"font-weight: bold\">The language variable does not exist. Please report this as it is an error.</span>";
$pgv_lang["resolution"] 			= "Screen Resolution";
$pgv_lang["menu"]					= "Menu";
$pgv_lang["header"] 				= "Header";
$pgv_lang["imageview"]				= "Image Viewer";

//-- CONFIG FILE MESSAGES
$pgv_lang["login_head"] 			= "PhpGedView User Login";
$pgv_lang["for_support"]			= "For technical support and information contact";
$pgv_lang["for_contact"]			= "For help with genealogy questions contact";
$pgv_lang["for_all_contact"]		= "For technical support or genealogy questions, please contact";
$pgv_lang["build_error"]			= "GEDCOM file has been updated.";
$pgv_lang["choose_username"]		= "Desired user name";
$pgv_lang["username"]				= "User name";
$pgv_lang["invalid_username"]		= "User name contains invalid characters";
$pgv_lang["firstname"]				= "First Name";
$pgv_lang["lastname"]				= "Last Name";
$pgv_lang["choose_password"]		= "Desired password";
$pgv_lang["password"]				= "Password";
$pgv_lang["confirm"]				= "Confirm Password";
$pgv_lang["login"]					= "Login";
$pgv_lang["logout"] 				= "Logout";
$pgv_lang["admin"]					= "Admin";
$pgv_lang["logged_in_as"]			= "Logged in as ";
$pgv_lang["my_pedigree"]			= "My Pedigree";
$pgv_lang["my_indi"]				= "My Individual Record";
$pgv_lang["yes"]					= "Yes";
$pgv_lang["no"] 					= "No";
$pgv_lang["change_theme"]			= "Change Theme";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]			= "Pedigree Tree";
$pgv_lang["gen_ped_chart"]			= "#PEDIGREE_GENERATIONS# Generation Pedigree Chart";
$pgv_lang["generations"]			= "Generations";
$pgv_lang["view"]					= "View";
$pgv_lang["fam_spouse"] 			= "Family with spouse";
$pgv_lang["root_person"]			= "Root Person ID";
$pgv_lang["hide_details"]			= "Hide Details";
$pgv_lang["show_details"]			= "Show Details";
$pgv_lang["person_links"]			= "Links to charts, families, and close relatives of this person. Click this icon to view this page, starting at this person.";
$pgv_lang["zoom_box"]				= "Zoom in/out on this box.";
$pgv_lang["orientation"]			= "Orientation";
$pgv_lang["portrait"]				= "Portrait";
$pgv_lang["landscape"]				= "Landscape";
$pgv_lang["start_at_parents"]		= "Start at parents";
$pgv_lang["charts"] 				= "Charts";
$pgv_lang["lists"]					= "Lists";
$pgv_lang["max_generation"] 		= "The maximum number of pedigree generations is #PEDIGREE_GENERATIONS#.";
$pgv_lang["min_generation"] 		= "The minimum number of pedigree generations is 3.";
$pgv_lang["box_width"] 				= "Box width";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]	= "Unable to find family with ID";
$pgv_lang["unable_to_find_record"]	= "Unable to find record with ID";
$pgv_lang["title"]					= "Title:";
$pgv_lang["living"] 				= "Living";
$pgv_lang["private"]				= "Private";
$pgv_lang["birth"]					= "Birth:";
$pgv_lang["death"]					= "Death:";
$pgv_lang["descend_chart"]			= "Descendancy Chart";
$pgv_lang["individual_list"]		= "Individual List";
$pgv_lang["family_list"]			= "Family List";
$pgv_lang["source_list"]			= "Source List";
$pgv_lang["place_list"] 			= "Place Hierarchy";
$pgv_lang["place_list_aft"] 		= "Place Hierarchy after";
$pgv_lang["media_list"] 			= "MultiMedia List";
$pgv_lang["search"] 				= "Search";
$pgv_lang["clippings_cart"] 		= "Family Tree Clippings Cart";
$pgv_lang["print_preview"]			= "Printer-friendly Version";
$pgv_lang["cancel_preview"] 		= "Back to normal view";
$pgv_lang["change_lang"]			= "Change Language";
$pgv_lang["print"]					= "Print";
$pgv_lang["total_queries"]			= "Total Database Queries: ";
$pgv_lang["total_privacy_checks"]	= "Total privacy checks: ";
$pgv_lang["back"]					= "Back";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["aka"]					= "AKAs";
$pgv_lang["male"]					= "Male";
$pgv_lang["female"] 				= "Female";
$pgv_lang["temple"] 				= "LDS Temple";
$pgv_lang["temple_code"]			= "LDS Temple Code:";
$pgv_lang["status"] 				= "Status";
$pgv_lang["source"] 				= "Source";
$pgv_lang["text"]					= "Source Text:";
$pgv_lang["note"]					= "Note";
$pgv_lang["NN"] 					= "(unknown)";
$pgv_lang["PN"] 					= "(unknown)";
$pgv_lang["unrecognized_code"]		= "Unrecognized GEDCOM Code";
$pgv_lang["unrecognized_code_msg"]	= "This is an error, and we would like to fix it. Please report this error to";
$pgv_lang["indi_info"]				= "Individual Information";
$pgv_lang["pedigree_chart"] 		= "Pedigree Chart";
$pgv_lang["individual"]				= "Individual";
$pgv_lang["as_spouse"]				= "Family with Spouse";
$pgv_lang["privacy_error"]			= "This information is private and cannot be shown.";
$pgv_lang["more_information"]		= "For more information contact";
$pgv_lang["given_name"] 			= "Given Name:";
$pgv_lang["surname"]				= "Surname:";
$pgv_lang["suffix"] 				= "Suffix:";
$pgv_lang["sex"]					= "Gender";
$pgv_lang["personal_facts"] 		= "Personal Facts and Details";
$pgv_lang["type"]					= "Type";
$pgv_lang["parents"]				= "Parents:";
$pgv_lang["siblings"]				= "Sibling";
$pgv_lang["father"] 				= "Father";
$pgv_lang["mother"] 				= "Mother";
$pgv_lang["parent"] 				= "Parent";
$pgv_lang["self"] 					= "Self";
$pgv_lang["relatives"]				= "Close Relatives";
$pgv_lang["relatives_events"]		= "Events of close relatives";
$pgv_lang["historical_facts"]		= "Historical facts";
$pgv_lang["partner"] 				= "Partner";
$pgv_lang["spouse"] 				= "Spouse";
$pgv_lang["spouses"] 				= "Spouses";
$pgv_lang["surnames"]				= "Surnames";
$pgv_lang["adopted"]				= "Adopted";
$pgv_lang["foster"] 				= "Foster";
$pgv_lang["sealing"]				= "Sealing";
$pgv_lang["challenged"]				= "Challenged";
$pgv_lang["disproved"]				= "Disproved";
$pgv_lang["infant"]					= "Infant";
$pgv_lang["stillborn"]				= "Stillborn";
$pgv_lang["deceased"]				= "Deceased";
$pgv_lang["link_as_wife"]			= "Link this person to an existing family as a wife";
$pgv_lang["no_tab1"]				= "There are no Facts for this individual.";
$pgv_lang["no_tab2"]				= "There are no Notes for this individual.";
$pgv_lang["no_tab3"]				= "There are no Source citations for this individual.";
$pgv_lang["no_tab4"]				= "There are no media objects for this individual.";
$pgv_lang["no_tab5"]				= "There are no close relatives for this individual.";
$pgv_lang["no_tab6"]				= "There are no research logs attached to this individual.";
$pgv_lang["show_fact_sources"]		= "Show all sources";
$pgv_lang["show_fact_notes"]		= "Show all notes";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]			= "Family Information";
$pgv_lang["family_group_info"]		= "Family Group Information";
$pgv_lang["husband"]				= "Husband";
$pgv_lang["wife"]					= "Wife";
$pgv_lang["marriage"]				= "Marriage:";
$pgv_lang["lds_sealing"]			= "LDS Sealing:";
$pgv_lang["marriage_license"]		= "Marriage License:";
$pgv_lang["no_children"]			= "No recorded children";
$pgv_lang["childless_family"]		= "This family remained childless";
$pgv_lang["parents_timeline"]		= "Show couple on timeline chart";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]				= "Clippings Cart";
$pgv_lang["which_links"]			= "Which other links from this family would you like to add?";
$pgv_lang["just_family"]			= "Add just this family record.";
$pgv_lang["parents_and_family"] 	= "Add parents' records together with this family record.";
$pgv_lang["parents_and_child"]		= "Add parents' and children's records together with this family record.";
$pgv_lang["parents_desc"]			= "Add parents' and all descendants' records together with this family record.";
$pgv_lang["continue"]				= "Continue Adding";
$pgv_lang["which_p_links"]			= "Which links from this person would you also like to add?";
$pgv_lang["just_person"]			= "Add just this person.";
$pgv_lang["person_parents_sibs"]	= "Add this person, his parents, and siblings.";
$pgv_lang["person_ancestors"]		= "Add this person and his direct line ancestors.";
$pgv_lang["person_ancestor_fams"]	= "Add this person, his direct line ancestors, and their families.";
$pgv_lang["person_spouse"]			= "Add this person, his spouse, and children.";
$pgv_lang["person_desc"]			= "Add this person, his spouse, and all descendants.";
$pgv_lang["which_s_links"]			= "Which records linked to this source should be added?";
$pgv_lang["just_source"]		= "Add just this source.";
$pgv_lang["linked_source"]		= "Add this source and families/people linked to it.";
$pgv_lang["person_private"] 		= "Details about this person are private. Personal details will not be included.";
$pgv_lang["family_private"] 		= "Details about this family are private. Family details will not be included.";
$pgv_lang["download"]				= "Right click (control-click on a Macintosh) on the links below and select &quot;Save target as&quot; to download the files.";
$pgv_lang["cart_is_empty"]			= "Your Clippings Cart is empty.";
$pgv_lang["id"] 					= "ID";
$pgv_lang["name_description"]		= "Name / Description";
$pgv_lang["remove"] 				= "Remove";
$pgv_lang["empty_cart"] 			= "Empty Cart";
$pgv_lang["download_now"]			= "Download Now";
$pgv_lang["download_file"]			= "Download file #GLOBALS[whichFile]#";
$pgv_lang["indi_downloaded_from"]	= "This Individual was downloaded from:";
$pgv_lang["family_downloaded_from"] = "This Family was downloaded from:";
$pgv_lang["source_downloaded_from"] = "This Source was downloaded from:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]			= "Place connections found";
$pgv_lang["top_level"]				= "Top Level";
$pgv_lang["form"]					= "Places are encoded in the form: ";
$pgv_lang["default_form"]			= "City, County, State/Province, Country";
$pgv_lang["default_form_info"]		= "(Default)";
$pgv_lang["unknown"]				= "unknown";
$pgv_lang["individuals"]			= "Individuals";
$pgv_lang["view_records_in_place"]	= "View all records found in this place";
$pgv_lang["place_list2"] 			= "Place List";
$pgv_lang["show_place_hierarchy"]	= "Show Places in Hierarchy";
$pgv_lang["show_place_list"]		= "Show All Places in a List";
$pgv_lang["total_unic_places"]		= "Total Unique Places";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["external_objects"]		= "External objects";
$pgv_lang["multi_title"]			= "MultiMedia Object List";
$pgv_lang["media_found"]			= "Media Objects found";
$pgv_lang["view_person"]			= "View Person";
$pgv_lang["view_family"]			= "View Family";
$pgv_lang["view_source"]			= "View Source";
$pgv_lang["view_object"]			= "View Object";
$pgv_lang["prev"]					= "&lt; Previous";
$pgv_lang["next"]					= "Next &gt;";
$pgv_lang["next_image"]				= "Next image";
$pgv_lang["file_not_found"] 		= "File not found.";
$pgv_lang["medialist_show"] 		= "Show";
$pgv_lang["per_page"]				= "media objects per page";
$pgv_lang["media_format"]			= "Media Format";
$pgv_lang["image_size"]				= "Image Dimensions";
$pgv_lang["media_id"]				= "Media ID";
$pgv_lang["invalid_id"]				= "No such ID exists in this GEDCOM file.";
$pgv_lang["record_updated"]			= "Record #pid# successfully updated.";
$pgv_lang["record_not_updated"]		= "Record #pid# could not be updated.";
$pgv_lang["record_removed"]			= "Record #xref# successfully removed from GEDCOM.";
$pgv_lang["record_not_removed"]		= "Record #xref# could not be removed from GEDCOM.";
$pgv_lang["record_added"]			= "Record #xref# successfully added to GEDCOM.";
$pgv_lang["record_not_added"]		= "Record #xref# could not be added to GEDCOM.";

//-- SEARCH FILE MESSAGES
$pgv_lang["soundex_search"] 		= "Search the way you think the name is written (Soundex)";
$pgv_lang["sources"]				= "Sources";
$pgv_lang["lastname_search"]		= "Last name";
$pgv_lang["search_place"]			= "Place";
$pgv_lang["search_year"]			= "Year";
$pgv_lang["no_results"] 			= "No results found.";
$pgv_lang["search_soundex"]			= "Soundex search";
$pgv_lang["search_replace"]			= "Search and replace";
$pgv_lang["search_sources"]			= "Sources";
$pgv_lang["search_more_chars"]      = "Please enter more than one character";
$pgv_lang["search_soundextype"]		= "Soundex type:";
$pgv_lang["search_russell"]			= "Basic";
$pgv_lang["search_tagfilter"]		= "Exclude Filter";
$pgv_lang["search_tagfon"]			= "Exclude some non-genealogical data";
$pgv_lang["search_tagfoff"]			= "Off";
$pgv_lang["associate"]				= "associate";
$pgv_lang["search_record"]			= "Entire record";
$pgv_lang["search_to"]				= "to";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["titles_found"]			= "Titles";
$pgv_lang["find_source"]			= "Find Source";

//-- REPOLIST FILE MESSAGES
$pgv_lang["repo_list"]				= "Repository List";
$pgv_lang["repos_found"]			= "Repositories found";
$pgv_lang["find_repository"]		= "Find Repository";
$pgv_lang["total_repositories"]		= "Total Repositories";
$pgv_lang["confirm_delete_repo"]	= "Are you sure you want to delete this Repository from the database?";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]			= "Source Information";
$pgv_lang["people"] 				= "People";
$pgv_lang["families"]				= "Families";
$pgv_lang["total_sources"]			= "Total Sources";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["invalid_gedformat"]		= "Invalid GEDCOM format";
$pgv_lang["exec_time"]				= "Execution time:";
$pgv_lang["unable_to_create_index"] = "Unable to create Index files.  Make sure Write permissions are set on the PhpGedView directory.  Permissions may be restored once Index files are written.";
$pgv_lang["changes_present"]		= "The current GEDCOM has changes pending review.  If you continue this Import, these pending changes will be posted to the database immediately.  You should review the pending changes before continuing the Import.";
$pgv_lang["sec"]					= "sec.";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"] 			= "Total families";
$pgv_lang["total_indis"]			= "Total individuals";
$pgv_lang["notes"]					= "Notes";
$pgv_lang["ssourcess"]				= "Sources";
$pgv_lang["media"]					= "Media";
$pgv_lang["name_contains"]			= "Name contains:";
$pgv_lang["filter"] 				= "Filter";
$pgv_lang["find_individual"]		= "Find Individual ID";
$pgv_lang["find_familyid"]			= "Find Family ID";
$pgv_lang["find_sourceid"]			= "Find Source ID";
$pgv_lang["find_specialchar"]		= "Find Special Characters";
$pgv_lang["magnify"]				= "Magnify";
$pgv_lang["skip_surnames"]			= "Skip Surname lists";
$pgv_lang["show_surnames"]			= "Show Surname lists";
$pgv_lang["all"]					= "ALL";
$pgv_lang["hidden"]					= "Hidden";
$pgv_lang["confidential"]			= "Confidential";
$pgv_lang["alpha_index"]				= "Alphabetical Index";
$pgv_lang["name_list"] 				= "Name List";
$pgv_lang["firstname_alpha_index"] 	= "Firstname Alphabetical Index";
$pgv_lang["roots"]		 				= "Roots";
$pgv_lang["leaves"] 					= "Leaves";
$pgv_lang["widow"] 					= "Widow";
$pgv_lang["widower"] 				= "Widower";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]					= "Age";
$pgv_lang["days"]					= "days";
$pgv_lang["months"]					= "months";
$pgv_lang["years"]					= "years";
$pgv_lang["day1"]					= "day";
$pgv_lang["month1"]					= "month";
$pgv_lang["year1"]					= "year";
$pgv_lang["after_death"]        ="after death";
$pgv_lang["timeline_title"] 		= "PhpGedView Timeline";
$pgv_lang["timeline_chart"] 		= "Timeline Chart";
$pgv_lang["remove_person"]			= "Remove Person";
$pgv_lang["show_age"]				= "Show Age Marker";
$pgv_lang["add_another"]			= "Add another person to chart:<br />Person ID:";
$pgv_lang["find_id"]				= "Find ID";
$pgv_lang["show"]					= "Show";
$pgv_lang["year"]					= "Year:";
$pgv_lang["timeline_instructions"]	= "In most recent browsers you can click and drag the boxes around on the chart.";
$pgv_lang["zoom_in"]				= "Zoom In";
$pgv_lang["zoom_out"]				= "Zoom Out";
$pgv_lang["timeline_beginYear"] = "Begin Year";
$pgv_lang["timeline_endYear"] = "End Year";
$pgv_lang["timeline_scrollSpeed"] = "Speed";
$pgv_lang["timeline_controls"] = "Timeline Controls";
$pgv_lang["include_family"] = "Include Immediate Family";
$pgv_lang["lifespan_chart"] = "Lifespan Chart";

// calendar conversion options
$pgv_lang["cal_none"]                 = "No calendar conversion";
$pgv_lang["cal_gregorian"]            = "Gregorian";
$pgv_lang["cal_julian"]               = "Julian";
$pgv_lang["cal_french"]               = "French";
$pgv_lang["cal_jewish"]               = "Jewish";
$pgv_lang["cal_hebrew"]               = "Hebrew";
$pgv_lang["cal_jewish_and_gregorian"] = "Jewish and Gregorian";
$pgv_lang["cal_hebrew_and_gregorian"] = "Hebrew and Gregorian";
$pgv_lang["cal_hijri"]                = "Hijri";
$pgv_lang["cal_arabic"]               = "Arabic";

// some religious dates
$pgv_lang["easter"]     = "Easter";
$pgv_lang["ascension"]  = "Ascension";
$pgv_lang["pentecost"]  = "Pentecost";
$pgv_lang["assumption"] = "Assumption";
$pgv_lang["all_saints"] = "All Saints";
$pgv_lang["christmas"]  = "Christmas";

// am/pm suffixes for 12 hour clocks
$pgv_lang["a.m."]         = "am";
$pgv_lang["p.m."]         = "pm";
$pgv_lang["noon"]         = "m";
$pgv_lang["midn"]         = "mn";

//-- MONTH NAMES
$pgv_lang["jan"]					= "January";
$pgv_lang["feb"]					= "February";
$pgv_lang["mar"]					= "March";
$pgv_lang["apr"]					= "April";
$pgv_lang["may"]					= "May";
$pgv_lang["jun"]					= "June";
$pgv_lang["jul"]					= "July";
$pgv_lang["aug"]					= "August";
$pgv_lang["sep"]					= "September";
$pgv_lang["oct"]					= "October";
$pgv_lang["nov"]					= "November";
$pgv_lang["dec"]					= "December";

$pgv_lang["vend"]         = "Vendémiaire";
$pgv_lang["brum"]         = "Brumaire";
$pgv_lang["frim"]         = "Frimaire";
$pgv_lang["nivo"]         = "Nivôse";
$pgv_lang["pluv"]         = "Pluviôse";
$pgv_lang["vent"]         = "Ventôse";
$pgv_lang["germ"]         = "Germinal";
$pgv_lang["flor"]         = "Floréal";
$pgv_lang["prai"]         = "Prairial";
$pgv_lang["mess"]         = "Messidor";
$pgv_lang["ther"]         = "Thermidor";
$pgv_lang["fruc"]         = "Fructidor";
$pgv_lang["comp"]         = "jours complémentaires";

$pgv_lang["tsh"]          = "Tishrei";
$pgv_lang["csh"]          = "Heshvan";
$pgv_lang["ksl"]          = "Kislev";
$pgv_lang["tvt"]          = "Tevet";
$pgv_lang["shv"]          = "Shevat";
$pgv_lang["adr"]          = "Adar";
$pgv_lang["adr_leap_year"]= "Adar I";
$pgv_lang["ads"]          = "Adar II";
$pgv_lang["nsn"]          = "Nissan";
$pgv_lang["iyr"]          = "Iyar";
$pgv_lang["svn"]          = "Sivan";
$pgv_lang["tmz"]          = "Tamuz";
$pgv_lang["aav"]          = "Av";
$pgv_lang["ell"]          = "Elul";

$pgv_lang["muhar"]        = "Muharram";
$pgv_lang["safar"]        = "Safar";
$pgv_lang["rabia"]        = "Rabi' al-awwal";
$pgv_lang["rabit"]        = "Rabi' al-thani";
$pgv_lang["jumaa"]        = "Jumada al-awwal";
$pgv_lang["jumat"]        = "Jumada al-thani";
$pgv_lang["rajab"]        = "Rajab";
$pgv_lang["shaab"]        = "Sha'aban";
$pgv_lang["ramad"]        = "Ramadan";
$pgv_lang["shaww"]        = "Shawwal";
$pgv_lang["dhuaq"]        = "Dhu al-Qi'dah";
$pgv_lang["dhuah"]        = "Dhu al-Hijjah";

$pgv_lang["b.c."]         = "B.C.";

$pgv_lang["abt"]					= "about";
$pgv_lang["aft"]					= "after";
$pgv_lang["and"]					= "and";
$pgv_lang["bef"]					= "before";
$pgv_lang["bet"]					= "between";
$pgv_lang["cal"]					= "calculated";
$pgv_lang["est"]					= "estimated";
$pgv_lang["from"]					= "from";
$pgv_lang["int"]					= "interpreted";
$pgv_lang["to"] 					= "to";
$pgv_lang["cir"]					= "circa";
$pgv_lang["apx"]					= "approx.";

//-- Admin File Messages
$pgv_lang["rebuild_indexes"]		= "Rebuild indexes";
$pgv_lang["password_mismatch"]		= "Passwords do not match.";
$pgv_lang["enter_username"] 		= "You must enter a user name.";
$pgv_lang["enter_password"] 		= "You must enter a password.";
$pgv_lang["save"]					= "Save";
$pgv_lang["saveandgo"]		= "Save and go to new record";
$pgv_lang["delete"] 				= "Delete";
$pgv_lang["edit"]					= "Edit";
$pgv_lang["no_login"]				= "Unable to authenticate user.";
$pgv_lang["basic_realm"]			= "PhpGedView Authentication System";
$pgv_lang["basic_auth_failure"]		= "You must enter a valid login ID and password to access this resource";
$pgv_lang["basic_auth"]				= "Basic Authentication";
$pgv_lang["digest_auth"]				= "Digest Authentication"; //not used in code yet
$pgv_lang["no_auth_needed"]			= "No Authentication";
$pgv_lang["file_not_exists"]		= "The filename entered does not exist.";
$pgv_lang["research_assistant"]		= "Research Assistant";
$pgv_lang["utf8_to_ansi"]			= "Convert from UTF-8 to ANSI (ISO-8859-1)";
$pgv_lang["media_linked"]			= "This media object is linked to the following:";
$pgv_lang["media_not_linked"]		= "This media object is not linked to any GEDCOM record.";
$pgv_lang["media_dir_1"]			= "This media object is located on an external server";
$pgv_lang["media_dir_2"]			= "This media object is in the standard media directory";
$pgv_lang["media_dir_3"]			= "This media object is in the protected media directory";
$pgv_lang["thumb_dir_1"]			= "This thumbnail is located on an external server";
$pgv_lang["thumb_dir_2"]			= "This thumbnail is in the standard media directory";
$pgv_lang["thumb_dir_3"]			= "This thumbnail is in the protected media directory";
$pgv_lang["moveto_2"]				= "Move to protected directory";
$pgv_lang["moveto_3"]				= "Move to standard directory";
$pgv_lang["move_standard"]			= "Move to standard";
$pgv_lang["move_protected"]			= "Move to protected";
$pgv_lang["move_mediadirs"]			= "Move Media directories";
$pgv_lang["setperms"]				= "Set Media Permissions";
$pgv_lang["setperms_writable"]		= "Make World-Writable";
$pgv_lang["setperms_readonly"]		= "Make World-Read Only";
$pgv_lang["setperms_success"]		= "Permissions Set";
$pgv_lang["setperms_failure"]		= "Permissions Not Set";
$pgv_lang["setperms_time_exceeded"]	= "The execution time limit was reached.  Try the command again on a smaller directory.";
$pgv_lang["move_time_exceeded"]		= "The execution time limit was reached.  Try the command again to move the rest of the files.";
$pgv_lang["media_firewall_rootdir_no_exist"]			= "The Media Firewall root directory you requested does not exist.  You must create it first.";
$pgv_lang["media_firewall_protected_dir_no_exist"]		= "The protected media directory could not be created in the Media Firewall root directory.  Please create this directory and make it world-writable.";
$pgv_lang["media_firewall_protected_dir_not_writable"]	= "The protected media directory in the Media Firewall root directory is not world writable. ";
$pgv_lang["media_firewall_invalid_dir"]	= "Error: The Media Firewall was launched from a directory other than the media directory.";

//-- Relationship chart messages
$pgv_lang["relationship_great"]		= "Great";
$pgv_lang["relationship_chart"] 	= "Relationship Chart";
$pgv_lang["person1"]				= "Person 1";
$pgv_lang["person2"]				= "Person 2";
$pgv_lang["no_link_found"]			= "No (other) link between the two individuals could be found.";
$pgv_lang["sibling"]				= "Sibling";
$pgv_lang["follow_spouse"]			= "Check relationships by marriage";
$pgv_lang["timeout_error"]			= "The script timed out before a relationship could be found.";
$pgv_lang["grandchild"]				= "Grandchild";
$pgv_lang["grandson"]				= "Grandson";
$pgv_lang["granddaughter"]			= "Granddaughter";
$pgv_lang["greatgrandchild"]		= "Great grandchild";
$pgv_lang["greatgrandson"]			= "Great grandson";
$pgv_lang["greatgranddaughter"]		= "Great granddaughter";
$pgv_lang["brother"]				= "Brother";
$pgv_lang["sister"] 				= "Sister";
$pgv_lang["aunt"]					= "Aunt";
$pgv_lang["uncle"]				= "Uncle";
$pgv_lang["nephew"]				= "Nephew";
$pgv_lang["niece"]				= "Niece";
$pgv_lang["firstcousin"]			= "First cousin";
$pgv_lang["femalecousin"]			= "Female cousin";
$pgv_lang["malecousin"]				= "Male cousin";
$pgv_lang["relationship_to_me"] 	= "Relationship to me";
$pgv_lang["rela_husb"]				= "Relationship to husband";
$pgv_lang["rela_wife"]				= "Relationship to wife";
$pgv_lang["next_path"]				= "Find next path";
$pgv_lang["show_path"]				= "Show path";
$pgv_lang["line_up_generations"]	= "Line up the same generations";
$pgv_lang["oldest_top"]             = "Show oldest top";

// %1\$s replaced by first person, %2\$s by the relationship and %3\$s by the second person.
$pgv_lang["relationship_male_1_is_the_2_of_3"] = "%1\$s is the %2\$s of %3\$s.";
$pgv_lang["relationship_female_1_is_the_2_of_3"] = "%1\$s is the %2\$s of %3\$s.";

$pgv_lang["mother_in_law"]		    = "Mother-in-law";
$pgv_lang["father_in_law"]		    = "Father-in-law";
$pgv_lang["brother_in_law"]		    = "Brother-in-law";
$pgv_lang["sister_in_law"]		    = "Sister-in-law";
$pgv_lang["son_in_law"]		        = "Son-in-law";
$pgv_lang["daughter_in_law"]		= "Daughter-in-law";
$pgv_lang["cousin_in_law"]			= "Cousin-in-law";

$pgv_lang["step_son"]		        = "step son";
$pgv_lang["step_daughter"]	    	= "step daughter";

// the bosa_brothers_offspring name is used for fraternal nephews and nieces - the names below can be extended to any number
// of generations just by adding more translations.
// 1st generation
$pgv_lang["bosa_brothers_offspring_2"] 				= "nephew";             // brother's son
$pgv_lang["bosa_brothers_offspring_3"] 				= "niece";              // brother's daughter
// 2nd generation
$pgv_lang["bosa_brothers_offspring_4"] 				= "great nephew";       // brother's son's son
$pgv_lang["bosa_brothers_offspring_5"] 				= "great niece";        // brother's son's daughter
$pgv_lang["bosa_brothers_offspring_6"] 				= "great nephew";       // brother's daughter's son
$pgv_lang["bosa_brothers_offspring_7"] 				= "great niece";        // brother's daughter's daughter
// for the general case of offspring of the nth generation use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_brothers_son"]	  = "%2\$d x great nephew";
$pgv_lang["n_x_brothers_daughter"] = "%2\$d x great niece";
// the bosa_sisters_offspring name is used for sisters nephews and nieces - the names below can be extended to any number
// of generations just by adding more translations.
// 1st generation
$pgv_lang["bosa_sisters_offspring_2"] 				= "nephew";             // sister's son
$pgv_lang["bosa_sisters_offspring_3"] 				= "niece";              // sister's daughter
// 2nd generation
$pgv_lang["bosa_sisters_offspring_4"] 				= "great nephew";       // sister's son's son
$pgv_lang["bosa_sisters_offspring_5"] 				= "great niece";        // sister's son's daughter
$pgv_lang["bosa_sisters_offspring_6"] 				= "great nephew";       // sister's daughter's son
$pgv_lang["bosa_sisters_offspring_7"] 				= "great niece";        // sister's daughter's daughter
// for the general case of offspring of the nth generation use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_sisters_son"]	  = "%2\$d x great nephew";
$pgv_lang["n_x_sisters_daughter"] = "%2\$d x great niece";

// the bosa name is used for offspring - the names below can be extended to any number
// of generations just by adding more translations.
// 1st generation
$pgv_lang["bosa_2"] 				= "son";                   // son
$pgv_lang["bosa_3"] 				= "daughter";              // daughter
// 2nd generation
$pgv_lang["bosa_4"] 				= "grandson";              // son's son
$pgv_lang["bosa_5"] 				= "granddaughter";         // son's daughter
$pgv_lang["bosa_6"] 				= "grandson";              // daughter's son
$pgv_lang["bosa_7"] 				= "granddaughter";         // daughter's daughter
// 3rd generation
$pgv_lang["bosa_8"] 				= "great grandson";        // son's son's son
$pgv_lang["bosa_9"] 				= "great granddaughter";   // son's son's daughter
$pgv_lang["bosa_10"] 				= "great grandson";		   // son's daughter's son
$pgv_lang["bosa_11"] 				= "great granddaughter";   // son's daughter's daughter
$pgv_lang["bosa_12"] 				= "great grandson";        // daughter's son's son
$pgv_lang["bosa_13"] 				= "great granddaughter";   // daughter's son's daughter
$pgv_lang["bosa_14"] 				= "great grandson";		   // daughter's daughter's son
$pgv_lang["bosa_15"] 				= "great granddaughter";   // daughter's daughter's daughter
// for the general case of offspring of the nth generation use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_grandson_from_son"]	  = "%3\$d x great grandson";
$pgv_lang["n_x_granddaughter_from_son"] = "%3\$d x great granddaughter";
$pgv_lang["n_x_grandson_from_daughter"]	  = "%3\$d x great grandson";
$pgv_lang["n_x_granddaughter_from_daughter"] = "%3\$d x great granddaughter";

// the sosa_uncle name is used for uncles - the names below can be extended to any number
// of generations just by adding more translations.
// to allow fo language variations we specify different relationships for paternal and maternal
// aunts and uncles
// 1st generation
$pgv_lang["sosa_uncle_2"] 				= "uncle";            // father's brother
$pgv_lang["sosa_uncle_3"] 				= "uncle";            // mother's brother
// 2nd generation
$pgv_lang["sosa_uncle_4"] 				= "great uncle";      // fathers's father's brother
$pgv_lang["sosa_uncle_5"] 				= "great uncle";      // father's mother's brother
$pgv_lang["sosa_uncle_6"] 				= "great uncle";      // mother's father's brother
$pgv_lang["sosa_uncle_7"] 				= "great uncle";      // mother's mother's brother
// for the general case of uncles of the nth degree use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_paternal_uncle"]		= "%2\$d x great uncle";
$pgv_lang["n_x_maternal_uncle"]	    = "%2\$d x great uncle";

// the sosa_aunt name is used for aunts - the names below can be extended to any number
// of generations just by adding more translations.
// to allow fo language variations we specify different relationships for paternal and maternal
// aunts and aunts
// 1st generation
$pgv_lang["sosa_aunt_2"] 				= "aunt";            // father's sister
$pgv_lang["sosa_aunt_3"] 				= "aunt";            // mother's sister
// 2nd generation
$pgv_lang["sosa_aunt_4"] 				= "great aunt";      // fathers's father's sister
$pgv_lang["sosa_aunt_5"] 				= "great aunt";      // father's mother's sister
$pgv_lang["sosa_aunt_6"] 				= "great aunt";      // mother's father's sister
$pgv_lang["sosa_aunt_7"] 				= "great aunt";      // mother's mother's sister
// for the general case of aunts of the nth degree use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_paternal_aunt"]		= "%2\$d x great aunt";
$pgv_lang["n_x_maternal_aunt"]	    = "%2\$d x great aunt";

// the sosa_uncle name is used for uncles(by marriage) - the names below can be extended to any number
// of generations just by adding more translations.
// to allow fo language variations we specify different relationships for paternal and maternal
// aunts and uncles
// 1st generation
$pgv_lang["sosa_uncle_bm_2"] 				= "uncle";            // father's brother
$pgv_lang["sosa_uncle_bm_3"] 				= "uncle";            // mother's brother
// 2nd generation
$pgv_lang["sosa_uncle_bm_4"] 				= "great uncle";      // fathers's father's brother
$pgv_lang["sosa_uncle_bm_5"] 				= "great uncle";      // father's mother's brother
$pgv_lang["sosa_uncle_bm_6"] 				= "great uncle";      // mother's father's brother
$pgv_lang["sosa_uncle_bm_7"] 				= "great uncle";      // mother's mother's brother
// for the general case of uncles of the nth degree use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_paternal_uncle_bm"]		= "%2\$d x great uncle";
$pgv_lang["n_x_maternal_uncle_bm"]	    = "%2\$d x great uncle";

// the sosa_aunt name is used for aunts (by marriage)- the names below can be extended to any number
// of generations just by adding more translations.
// to allow fo language variations we specify different relationships for paternal and maternal
// aunts and aunts
// 1st generation
$pgv_lang["sosa_aunt_bm_2"] 				= "aunt";            // father's sister
$pgv_lang["sosa_aunt_bm_3"] 				= "aunt";            // mother's sister
// 2nd generation
$pgv_lang["sosa_aunt_bm_4"] 				= "great aunt";      // fathers's father's sister
$pgv_lang["sosa_aunt_bm_5"] 				= "great aunt";      // father's mother's sister
$pgv_lang["sosa_aunt_bm_6"] 				= "great aunt";      // mother's father's sister
$pgv_lang["sosa_aunt_bm_7"] 				= "great aunt";      // mother's mother's sister
// for the general case of aunts of the nth degree use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["n_x_paternal_aunt_bm"]		= "%2\$d x great aunt";
$pgv_lang["n_x_maternal_aunt_bm"]	    = "%2\$d x great aunt";

// if a specific cousin relationship cannot be represented in a language translate as "";
$pgv_lang["male_cousin_1"]              = "first cousin";
$pgv_lang["male_cousin_2"]              = "second cousin";
$pgv_lang["male_cousin_3"]              = "third cousin";
$pgv_lang["male_cousin_4"]              = "fourth cousin";
$pgv_lang["male_cousin_5"]              = "fifth cousin";
$pgv_lang["male_cousin_6"]              = "sixth cousin";
$pgv_lang["male_cousin_7"]              = "seventh cousin";
$pgv_lang["male_cousin_8"]              = "eighth cousin";
$pgv_lang["male_cousin_9"]              = "ninth cousin";
$pgv_lang["male_cousin_10"]             = "tenth cousin";
$pgv_lang["male_cousin_11"]             = "eleventh cousin";
$pgv_lang["male_cousin_12"]             = "twelfth cousin";
$pgv_lang["male_cousin_13"]             = "thirteenth cousin";
$pgv_lang["male_cousin_14"]             = "fourteenth cousin";
$pgv_lang["male_cousin_15"]             = "fifteenth cousin";
$pgv_lang["male_cousin_16"]             = "sixteenth cousin";
$pgv_lang["male_cousin_17"]             = "seventeenth cousin";
$pgv_lang["male_cousin_18"]             = "eighteenth cousin";
$pgv_lang["male_cousin_19"]             = "nineteenth cousin";
$pgv_lang["male_cousin_20"]             = "twentieth cousin";
$pgv_lang["male_cousin_n"]              = "%d x cousin";
$pgv_lang["female_cousin_1"]            = "first cousin";
$pgv_lang["female_cousin_2"]            = "second cousin";
$pgv_lang["female_cousin_3"]            = "third cousin";
$pgv_lang["female_cousin_4"]            = "fourth cousin";
$pgv_lang["female_cousin_5"]            = "fifth cousin";
$pgv_lang["female_cousin_6"]            = "sixth cousin";
$pgv_lang["female_cousin_7"]            = "seventh cousin";
$pgv_lang["female_cousin_8"]            = "eighth cousin";
$pgv_lang["female_cousin_9"]            = "ninth cousin";
$pgv_lang["female_cousin_10"]           = "tenth cousin";
$pgv_lang["female_cousin_11"]           = "eleventh cousin";
$pgv_lang["female_cousin_12"]           = "twelfth cousin";
$pgv_lang["female_cousin_13"]           = "thirteenth cousin";
$pgv_lang["female_cousin_14"]           = "fourteenth cousin";
$pgv_lang["female_cousin_15"]           = "fifteenth cousin";
$pgv_lang["female_cousin_16"]           = "sixteenth cousin";
$pgv_lang["female_cousin_17"]           = "seventeenth cousin";
$pgv_lang["female_cousin_18"]           = "eighteenth cousin";
$pgv_lang["female_cousin_19"]           = "nineteenth cousin";
$pgv_lang["female_cousin_20"]           = "twentieth cousin";
$pgv_lang["female_cousin_n"]            = "%d x cousin";

// Only referenced from english specific functions
$pgv_lang["removed_ascending_1"]   = " once removed ascending";
$pgv_lang["removed_ascending_2"]   = " twice removed ascending";
$pgv_lang["removed_ascending_3"]   = " three times removed ascending";
$pgv_lang["removed_ascending_4"]   = " four times removed ascending";
$pgv_lang["removed_ascending_5"]   = " five times removed ascending";
$pgv_lang["removed_ascending_6"]   = " six times removed ascending";
$pgv_lang["removed_ascending_7"]   = " seven times removed ascending";
$pgv_lang["removed_ascending_8"]   = " eight times removed ascending";
$pgv_lang["removed_ascending_9"]   = " nine times removed ascending";
$pgv_lang["removed_ascending_10"]  = " ten times removed ascending";
$pgv_lang["removed_ascending_11"]  = " eleven times removed ascending";
$pgv_lang["removed_ascending_12"]  = " twelve times removed ascending";
$pgv_lang["removed_ascending_13"]  = " thirteen times removed ascending";
$pgv_lang["removed_ascending_14"]  = " fourteen times removed ascending";
$pgv_lang["removed_ascending_15"]  = " fifteen times removed ascending";
$pgv_lang["removed_ascending_16"]  = " sixteen times removed ascending";
$pgv_lang["removed_ascending_17"]  = " seventeen times removed ascending";
$pgv_lang["removed_ascending_18"]  = " eighteen times removed ascending";
$pgv_lang["removed_ascending_19"]  = " nineteen times removed ascending";
$pgv_lang["removed_ascending_20"]  = " twenty times removed ascending";
$pgv_lang["removed_descending_1"]  = " once removed descending";
$pgv_lang["removed_descending_2"]  = " twice removed descending";
$pgv_lang["removed_descending_3"]  = " three times removed descending";
$pgv_lang["removed_descending_4"]  = " four times removed descending";
$pgv_lang["removed_descending_5"]  = " five times removed descending";
$pgv_lang["removed_descending_6"]  = " six times removed descending";
$pgv_lang["removed_descending_7"]  = " seven times removed descending";
$pgv_lang["removed_descending_8"]  = " eight times removed descending";
$pgv_lang["removed_descending_9"]  = " nine times removed descending";
$pgv_lang["removed_descending_10"] = " ten times removed descending";
$pgv_lang["removed_descending_11"] = " eleven times removed descending";
$pgv_lang["removed_descending_12"] = " twelve times removed descending";
$pgv_lang["removed_descending_13"] = " thirteen times removed descending";
$pgv_lang["removed_descending_14"] = " fourteen times removed descending";
$pgv_lang["removed_descending_15"] = " fifteen times removed descending";
$pgv_lang["removed_descending_16"] = " sixteen times removed descending";
$pgv_lang["removed_descending_17"] = " seventeen times removed descending";
$pgv_lang["removed_descending_18"] = " eighteen times removed descending";
$pgv_lang["removed_descending_19"] = " nineteen times removed descending";
$pgv_lang["removed_descending_20"] = " twenty times removed descending";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]			= "Are you sure you want to delete this GEDCOM fact?";
$pgv_lang["access_denied"]			= "<b>Access Denied</b><br />You do not have access to this resource.";
$pgv_lang["changes_exist"]			= "Changes have been made to this GEDCOM.";
$pgv_lang["find_place"] 			= "Find Place";
$pgv_lang["close_window"]			= "Close Window";
$pgv_lang["close_window_without_refresh"] = "Close Window Without Reloading";
$pgv_lang["place_contains"] 		= "Place contains:";
$pgv_lang["add"]					= "Add";
$pgv_lang["custom_event"]			= "Custom Event";
$pgv_lang["delete_person"]			= "Delete this individual";
$pgv_lang["confirm_delete_person"]	= "Are you sure you want to delete this person from the GEDCOM file?";
$pgv_lang["find_media"] 			= "Find Media";
$pgv_lang["set_link"]				= "Set Link";
$pgv_lang["delete_source"]			= "Delete this Source";
$pgv_lang["confirm_delete_source"]	= "Are you sure you want to delete this Source from the GEDCOM file?";
$pgv_lang["find_family"]			= "Find Family";
$pgv_lang["find_fam_list"]			= "Find Family List";
$pgv_lang["edit_name"]				= "Edit Name";
$pgv_lang["delete_name"]			= "Delete Name";
$pgv_lang["select_date"]			= "Select a date";
$pgv_lang["user_cannot_edit"]		= "This user name cannot edit this GEDCOM.";
$pgv_lang["ged_noshow"]				= "This page has been disabled by the site administrator.";

//-- calendar.php messages
$pgv_lang["bdm"]					= "Births, Deaths, Marriages";
$pgv_lang["on_this_day"]			= "On This Day ...";
$pgv_lang["in_this_month"]			= "In This Month ...";
$pgv_lang["in_this_year"]			= "In This Year ...";
$pgv_lang["year_anniversary"]		= "#year_var# year anniversary";
$pgv_lang["today"]					= "Today";
$pgv_lang["day"]					= "Day:";
$pgv_lang["month"]					= "Month:";
$pgv_lang["showcal"]				= "Show events of:";
$pgv_lang["anniversary"]			= "Anniversary";
$pgv_lang["anniversary_calendar"]	= "Anniversary Calendar";
$pgv_lang["sunday"] 				= "Sunday";
$pgv_lang["monday"] 				= "Monday";
$pgv_lang["tuesday"]				= "Tuesday";
$pgv_lang["wednesday"]				= "Wednesday";
$pgv_lang["thursday"]				= "Thursday";
$pgv_lang["friday"] 				= "Friday";
$pgv_lang["saturday"]				= "Saturday";
$pgv_lang["viewday"]				= "View Day";
$pgv_lang["viewmonth"]				= "View Month";
$pgv_lang["viewyear"]				= "View Year";
$pgv_lang["all_people"] 			= "All People";
$pgv_lang["living_only"]			= "Living People";
$pgv_lang["recent_events"]			= "Recent Years (&lt; 100 yrs)";
$pgv_lang["day_not_set"]			= "Day not set";

//-- user self registration module
$pgv_lang["lost_password"]			= "Lost your password?";
$pgv_lang["requestpassword"]		= "Request new password";
$pgv_lang["no_account_yet"] 		= "No account?";
$pgv_lang["requestaccount"] 		= "Request new user account";
$pgv_lang["emailadress"]			= "Email Address";
$pgv_lang["mandatory"] 			= "Fields marked with * are mandatory.";
$pgv_lang["mail01_line01"]			= "Hello #user_fullname# ...";
$pgv_lang["mail01_line02"]			= "A request was received at #SERVER_NAME# to create a PhpGedView account with your email address #user_email#.";
$pgv_lang["mail01_line03"]			= "Information about the request is shown under the link below.";
$pgv_lang["mail01_line04"]			= "Please click on the following link and fill in the requested data to confirm your request and email address.";
$pgv_lang["mail01_line05"]			= "If you didn't request an account, you can just delete this message.";
$pgv_lang["mail01_line06"]			= "You won't get any more email from this site, because the account request will be deleted automatically after seven days.";
$pgv_lang["mail01_subject"] 		= "Your registration at #SERVER_NAME#";

$pgv_lang["mail02_line01"]			= "Hello Administrator ...";
$pgv_lang["mail02_line02"]			= "A prospective user registered himself with PhpGedView at #SERVER_NAME#.";
$pgv_lang["mail02_line03"]			= "The user received an email with the information necessary to confirm his access request.";
$pgv_lang["mail02_line04"]			= "You will be informed by email when this prospective user has confirmed his request.  You can then complete the process by activating the user name.  The new user will not be able to login until you activate the account.";
$pgv_lang["mail02_line04a"]			= "You will be informed by email when this prospective user has confirmed his request.  After this, the user will be able to login without any action on your part.";
$pgv_lang["mail02_subject"] 		= "New registration at #SERVER_NAME#";

$pgv_lang["hashcode"]				= "Verification code:";
$pgv_lang["thankyou"]				= "Hello #user_fullname# ...<br />Thank you for your registration.";
$pgv_lang["pls_note06"] 			= "We will now send a confirmation email to the address <b>#user_email#</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br /><br />After you have followed the instructions in the confirmation email, the administrator still has to approve your request before your account can be used.<br /><br />To login to this site, you will need to know your user name and password.<br /><br />";
$pgv_lang["pls_note06a"] 			= "We will now send a confirmation email to the address <b>#user_email#</b>. You must verify your account request by following instructions in the confirmation email. If you do not confirm your account request within seven days, your application will be rejected automatically.  You will have to apply again.<br /><br />After you have followed the instructions in the confirmation email, you can login.  To login to this site, you will need to know your user name and password.<br /><br />";

$pgv_lang["registernew"]			= "New Account confirmation";
$pgv_lang["user_verify"]			= "User verification";
$pgv_lang["send"]					= "Send";

$pgv_lang["pls_note07"] 			= "~#pgv_lang[user_verify]#~<br /><br />To confirm your account request, please type in your user name, your password, and the verification code you received by email.";
$pgv_lang["pls_note08"] 			= "The data for the user <b>#user_name#</b> was checked.";

$pgv_lang["mail03_line01"]			= "Hello Administrator ...";
$pgv_lang["mail03_line02"]			= "User #newuser[username]# (#newuser[fullname]#) has confirmed his request for an account.";
$pgv_lang["mail03_line03"]			= "Please click on the link below to login to your site.  You must Edit the user to activate the account so that he can login to your site.";
$pgv_lang["mail03_line03a"]			= "You do not have to take any action; the user can now login.";
$pgv_lang["mail03_subject"] 		= "New user at #SERVER_NAME#";

$pgv_lang["pls_note09"] 			= "You have confirmed your request to become a registered user.";
$pgv_lang["pls_note10"] 			= "The Administrator has been informed.  As soon as he gives you permission to login, you can login with your user name and password.";
$pgv_lang["pls_note10a"]			= "You can now login with your user name and password.";
$pgv_lang["data_incorrect"] 		= "Data was not correct, please try again";
$pgv_lang["user_not_found"] 		= "Could not verify the information you entered.  Please try again or contact the site administrator for more information.";

$pgv_lang["lost_pw_reset"]			= "Lost password request";
$pgv_lang["pls_note11"] 			= "To have your password reset, enter your user name.<br /><br />We will respond by sending you an email to the address registered with your account.  The email will contain a URL and confirmation code for your account. When you visit this URL, you can change your password and login to this site. For security reasons, you should not give this confirmation code to anyone.<br /><br />If you require assistance from the site administrator, please use the contact link below.";

$pgv_lang["mail04_line01"]			= "Hello #user_fullname# ...";
$pgv_lang["mail04_line02"]			= "A new password was requested for your user name.";
$pgv_lang["mail04_line03"]			= "Recommendation:";
$pgv_lang["mail04_line04"]			= "Please click on the link below or paste it into your browser, login with the new password, and change it immediately to keep the integrity of your data secure.";
$pgv_lang["mail04_line05"]			= "After you have logged in, select the «#pgv_lang[editowndata]#» link under the «#pgv_lang[mygedview]#» menu and fill in the password fields to change your password.";
$pgv_lang["mail04_subject"] 		= "Data request at #SERVER_NAME#";

$pgv_lang["pwreqinfo"]				= "Hello...<br /><br />An email with your new password was sent to the address we have on file for <b>#user[email]#</b>.<br /><br />Please check your email account; you should receive our message soon.<br /><br />Recommendation:<br />You should login to this site with your new password as soon as possible, and you should change your password to maintain your data's security.";

$pgv_lang["myuserdata"] 			= "My Account";
$pgv_lang["user_theme"] 			= "My Theme";
$pgv_lang["mgv"]					= "MyGedView";
$pgv_lang["mygedview"]				= "MyGedView Portal";
$pgv_lang["passwordlength"] 		= "Passwords must contain at least 6 characters.";
$pgv_lang["welcome_text_auth_mode_1"]	= "<center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to every visitor who has a user account.<br /><br />If you have a user account, you can login on this page.  If you don't have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your application, the site administrator will activate your account.  You will receive an email when your application has been approved.";
$pgv_lang["welcome_text_auth_mode_2"]	= "<center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>authorized</u> users only.<br /><br />If you have a user account you can login on this page.  If you don't have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.";
$pgv_lang["welcome_text_auth_mode_3"]	= "<center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>family members only</u>.<br /><br />If you have a user account you can login on this page.  If you don't have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.";
$pgv_lang["welcome_text_cust_head"] 	= "<center><b>Welcome to this Genealogy website</b></center><br />Access is permitted to users who have an account and a password for this website.<br />";
$pgv_lang["acceptable_use"]			= "<div class=\"largeError\">Notice:</div><div class=\"error\">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living people listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div>";


//-- mygedview page
$pgv_lang["upcoming_events"]		= "Upcoming Events";
$pgv_lang["living_or_all"]			= "Show only events of living people?";
$pgv_lang["basic_or_all"]			= "Show only Births, Deaths, and Marriages?";
$pgv_lang["style"]					= "Presentation Style";
$pgv_lang["style1"]					= "List";
$pgv_lang["style2"]					= "Table";
$pgv_lang["style3"]					= "Tagcloud";
$pgv_lang["cal_download"]			= "Allow calendar events download?";
$pgv_lang["no_events_living"]		= "No events for living people exist for the next #pgv_lang[global_num1]# days.";
$pgv_lang["no_events_living1"]		= "No events for living people exist for tomorrow.";
$pgv_lang["no_events_all"]			= "No events exist for the next #pgv_lang[global_num1]# days.";
$pgv_lang["no_events_all1"]			= "No events exist for tomorrow.";
$pgv_lang["no_events_privacy"]		= "Events exist for the next #pgv_lang[global_num1]# days, but privacy restrictions prevent you from seeing them.";
$pgv_lang["no_events_privacy1"]		= "Events exist for tomorrow, but privacy restrictions prevent you from seeing them.";
$pgv_lang["more_events_privacy"]	= "<br />More events exist for the next #pgv_lang[global_num1]# days, but privacy restrictions prevent you from seeing them.";
$pgv_lang["more_events_privacy1"]	= "<br />More events exist for tomorrow, but privacy restrictions prevent you from seeing them.";
$pgv_lang["none_today_living"]		= "No events for living people exist for today.";
$pgv_lang["none_today_all"]			= "No events exist for today.";
$pgv_lang["none_today_privacy"]		= "Events exist for today, but privacy restrictions prevent you from seeing them.";
$pgv_lang["more_today_privacy"]		= "<br />More events exist for today, but privacy restrictions prevent you from seeing them.";
$pgv_lang["chat"]					= "Chat";
$pgv_lang["users_logged_in"]		= "Users Logged In";
$pgv_lang["anon_user"]				= "1 anonymous logged-in user";
$pgv_lang["anon_users"]				= "#pgv_lang[global_num1]# anonymous logged-in users";
$pgv_lang["login_user"]				= "1 logged-in user";
$pgv_lang["login_users"]			= "#pgv_lang[global_num1]# logged-in users";
$pgv_lang["no_login_users"]			= "No logged-in and no anonymous users";
$pgv_lang["message"]				= "Send Message";
$pgv_lang["my_messages"]			= "My Messages";
$pgv_lang["date_created"]			= "Date Sent:";
$pgv_lang["message_from"]			= "Email Address:";
$pgv_lang["message_from_name"]		= "Your Name:";
$pgv_lang["message_to"] 			= "Message To:";
$pgv_lang["message_subject"]		= "Subject:";
$pgv_lang["message_body"]			= "Body:";
$pgv_lang["no_to_user"] 			= "No recipient user was provided.  Cannot continue.";
$pgv_lang["provide_email"]			= "Please provide your email address so that we may contact you in response to this message.  If you do not provide your email address we will not be able to respond to your inquiry.  You email address will not be used in any other way besides responding to this inquiry.";
$pgv_lang["reply"]					= "Reply";
$pgv_lang["message_deleted"]		= "Message Deleted";
$pgv_lang["message_sent"]			= "Message successfully sent to #TO_USER#";
$pgv_lang["reset"]					= "Reset";
$pgv_lang["site_default"]			= "Site Default";
$pgv_lang["mygedview_desc"] 		= "Your MyGedView page allows you to keep bookmarks of your favorite people, track upcoming events, and collaborate with other PhpGedView users.";
$pgv_lang["no_messages"]			= "You have no pending messages.";
$pgv_lang["clicking_ok"]			= "When you click OK, another window will open and you may contact #user[fullname]#";
$pgv_lang["favorites"]				= "Favorites";
$pgv_lang["my_favorites"]			= "My Favorites";
$pgv_lang["no_favorites"]			= "You have not selected any favorites.<br /><br />To add an individual, a family, or a source to your favorites, click on the <b>#pgv_lang[add_favorite]#</b> link to reveal some fields where you can enter or search for an ID number.  Instead of an ID number, you can enter a URL and a title.";
$pgv_lang["add_to_my_favorites"]	= "Add to My Favorites";
$pgv_lang["gedcom_favorites"]		= "This GEDCOM's Favorites";
$pgv_lang["no_gedcom_favorites"]	= "At this moment there are no selected Favorites.	The admin can add Favorites to display at startup.";
$pgv_lang["confirm_fav_remove"] 	= "Are you sure you want to remove this item from your list of Favorites?";
$pgv_lang["invalid_email"]			= "Please enter a valid email address.";
$pgv_lang["enter_subject"]			= "Please enter a message subject.";
$pgv_lang["enter_body"] 			= "Please enter some message text before sending.";
$pgv_lang["confirm_message_delete"] = "Are you sure you want to delete this message?  It cannot be retrieved later.";
$pgv_lang["message_email1"] 		= "The following message has been sent to your PhpGedView user account from ";
$pgv_lang["message_email2"] 		= "You sent the following message to a PhpGedView user:";
$pgv_lang["message_email3"] 		= "You sent the following message to a PhpGedView administrator:";
$pgv_lang["viewing_url"]			= "This message was sent while viewing the following URL: ";
$pgv_lang["messaging2_help"]		= "When you send this message you will receive a copy sent via email to the address you provided.";
$pgv_lang["random_picture"] 		= "Random Picture";
$pgv_lang["message_instructions"]	= "<b>Please Note:</b> Private information of living individuals will only be given to family relatives and close friends.  You will be asked to verify your relationship before you will receive any private data.  Sometimes information of dead persons may also be private.  If this is the case, it is because there is not enough information known about the person to determine whether they are alive or not and we probaby do not have more information on this person.<br /><br />Before asking a question, please verify that you are inquiring about the correct person by checking dates, places, and close relatives.  If you are submitting changes to the genealogical data, please include the sources where you obtained the data.<br /><br />";
$pgv_lang["sending_to"] 			= "This message will be sent to #TO_USER#";
$pgv_lang["preferred_lang"] 		= "This user prefers to receive messages in #USERLANG#";
$pgv_lang["gedcom_created_using"]	= "This GEDCOM was created using <b>#CREATED_SOFTWARE# #CREATED_VERSION#</b>";
$pgv_lang["gedcom_created_on"]		= "This GEDCOM was created on <b>#CREATED_DATE#</b>";
$pgv_lang["gedcom_created_on2"] 	= " on <b>#CREATED_DATE#</b>";
$pgv_lang["gedcom_stats"]			= "GEDCOM Statistics";
$pgv_lang["stat_individuals"]		= "Individuals";
$pgv_lang["stat_families"]			= "Families";
$pgv_lang["stat_sources"]			= "Sources";
$pgv_lang["stat_other"] 			= "Other Records";
$pgv_lang["stat_earliest_birth"] 	= "Earliest Birth Year";
$pgv_lang["stat_latest_birth"] 	= "Latest Birth Year";
$pgv_lang["stat_earliest_death"] 	= "Earliest Death Year";
$pgv_lang["stat_latest_death"] 	= "Latest Death Year";
$pgv_lang["customize_page"] 		= "Customize MyGedView Portal";
$pgv_lang["customize_gedcom_page"]	= "Customize this GEDCOM Welcome page";
$pgv_lang["upcoming_events_block"]	= "Upcoming Events";
$pgv_lang["upcoming_events_descr"]	= "The Upcoming Events block shows anniversaries of events that will occur in the near future.  You can configure the amount of detail shown, and the administrator can configure how far into the future this block will look.";
$pgv_lang["todays_events_block"]	= "On This Day";
$pgv_lang["todays_events_descr"]	= "The On This Day, in Your History... block shows anniversaries of events for today.  You can configure the amount of detail shown.";
$pgv_lang["yahrzeit_block"]			= "Upcoming Yahrzeiten";
$pgv_lang["yahrzeit_descr"]			= "The Upcoming Yahrzeiten block shows anniversaries of death dates that will occur in the near future.  You can configure the period shown, and the Administrator can configure how far into the future this block will look.";
$pgv_lang["logged_in_users_block"]	= "Logged In Users";
$pgv_lang["logged_in_users_descr"]	= "The Logged In Users block shows a list of the users who are currently logged in.";
$pgv_lang["user_messages_block"]	= "User Messages";
$pgv_lang["user_messages_descr"]	= "The User Messages block shows a list of the messages that have been sent to the active user.";
$pgv_lang["user_favorites_block"]	= "User Favorites";
$pgv_lang["user_favorites_descr"]	= "The User Favorites block shows the user a list of his favorite people in the database so that he can easily link to them.";
$pgv_lang["welcome_block"]			= "User Welcome";
$pgv_lang["welcome_descr"]			= "The User Welcome block shows the user the current date and time, quick links to modify his account or go to his own Pedigree chart, and a link to customize his MyGedView Portal page.";
$pgv_lang["random_media_block"] 	= "Random Media";
$pgv_lang["random_media_descr"] 	= "The Random Media block randomly selects a photo or other media item from the currently active database and displays it to the user.<br /><br />The administrator determines whether this block can show media items associated with persons or events.";
$pgv_lang["random_media_persons_or_all"]	= "Show only persons, events, or all?";
$pgv_lang["random_media_persons"]	= "Persons";
$pgv_lang["random_media_events"]	= "Events";
$pgv_lang["gedcom_block"]			= "GEDCOM Welcome";
$pgv_lang["gedcom_descr"]			= "The GEDCOM Welcome block works the same as the User Welcome block.  It welcomes the visitor to the site and displays the title of the currently active database as well as the current date and time.";
$pgv_lang["gedcom_favorites_block"] = "GEDCOM Favorites";
$pgv_lang["gedcom_favorites_descr"] = "The GEDCOM Favorites block gives the administrator the ability to designate individuals from the database so that their information is easily accessible to all.  This is a way to highlight people who are important in your family history.";
$pgv_lang["gedcom_stats_block"] 	= "GEDCOM Statistics";
$pgv_lang["gedcom_stats_descr"] 	= "The GEDCOM Statistics block shows the visitor some basic information about the database, such as when it was created and how many people are in it.<br /><br />It also has a list of the most frequent surnames.  You can configure this block to not show the Frequent Surnames list, and you can also configure the GEDCOM to remove or add names to this list.  You can set the occurrence threshold for this list in the GEDCOM configuration.";
$pgv_lang["gedcom_stats_show_surnames"]	= "Show common surnames?";
$pgv_lang["portal_config_intructions"]	= "~#pgv_lang[customize_page]# <br /> #pgv_lang[customize_gedcom_page]#~<br /><br />You can customize the page by positioning the blocks on the page the way that you want them.<br /><br />The page is divided into <b>Main</b> and <b>Right</b> sections.	The <b>Main</b> section blocks appear larger and under the page title.  The <b>Right</b> section starts to the right of the title and goes down the right side of the page.<br /><br />Each section has its own list of blocks that will be printed on the page in the order they are listed.  You can add, remove, and reorder the blocks however you like.<br /><br />When one of the block lists is empty, the remaining blocks will take up the whole width of the page.<br /><br />";
$pgv_lang["login_block"]			= "Login";
$pgv_lang["login_descr"]			= "The Login block accepts a user name and password for users to login.";
$pgv_lang["theme_select_block"] 	= "Theme Select";
$pgv_lang["theme_select_descr"] 	= "The Theme Select block displays the Theme selector even when the Change Theme feature is disabled.";
$pgv_lang["block_top10_title"]		= "Top 10 Surnames";
$pgv_lang["block_top10"]			= "Top 10 Surnames";
$pgv_lang["block_top10_descr"]		= "This block shows a table of the 10 most frequently occurring surnames in the database.  The actual number of surnames shown in this block is configurable.  You can configure the GEDCOM to remove names from this list.";

$pgv_lang["gedcom_news_block"]		= "GEDCOM News";
$pgv_lang["gedcom_news_descr"]		= "The GEDCOM News block shows the visitor news releases or articles posted by an admin user.<br /><br />The News block is a good place to announce a significant database update, a family reunion, or the birth of a child.";
$pgv_lang["gedcom_news_limit"]		= "Limit display by:";
$pgv_lang["gedcom_news_limit_nolimit"]	= "No limit";
$pgv_lang["gedcom_news_limit_date"]		= "Age of item";
$pgv_lang["gedcom_news_limit_count"]	= "Number of items";
$pgv_lang["gedcom_news_flag"]		= "Limit:";
$pgv_lang["gedcom_news_archive"] 	= "View archive";
$pgv_lang["user_news_block"]		= "User Journal";
$pgv_lang["user_news_descr"]		= "The User Journal block lets the user keep notes or a journal online.";
$pgv_lang["my_journal"] 			= "My Journal";
$pgv_lang["no_journal"] 			= "You have not created any Journal items.";
$pgv_lang["confirm_journal_delete"] = "Are you sure you want to delete this Journal entry?";
$pgv_lang["add_journal"]			= "Add a new Journal entry";
$pgv_lang["gedcom_news"]			= "News";
$pgv_lang["confirm_news_delete"]	= "Are you sure you want to delete this News entry?";
$pgv_lang["add_news"]				= "Add a News article";
$pgv_lang["no_news"]				= "No News articles have been submitted.";
$pgv_lang["edit_news"]				= "Add/Edit Journal/News entry";
$pgv_lang["enter_title"]			= "Please enter a title.";
$pgv_lang["enter_text"] 			= "Please enter some text for this News or Journal entry.";
$pgv_lang["news_saved"] 			= "News/Journal entry successfully saved.";
$pgv_lang["article_text"]			= "Entry Text:";
$pgv_lang["main_section"]			= "Main Section Blocks";
$pgv_lang["right_section"]			= "Right Section Blocks";
$pgv_lang["available_blocks"]		= "Available Blocks";
$pgv_lang["move_up"]				= "Move Up";
$pgv_lang["move_down"]				= "Move Down";
$pgv_lang["move_right"] 			= "Move Right";
$pgv_lang["move_left"]				= "Move Left";
$pgv_lang["broadcast_all"]			= "Broadcast to all users";
$pgv_lang["hit_count"]				= "Hit Count:";
$pgv_lang["phpgedview_message"] 	= "PhpGedView Message";
$pgv_lang["common_surnames"]		= "Most Common Surnames";
$pgv_lang["default_news_title"] 	= "Welcome to Your Genealogy";
$pgv_lang["default_news_text"]		= "The genealogy information on this website is powered by <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView #VERSION#</a>.  This page provides an introduction and overview to this genealogy.<br /><br />To begin working with the data, choose one of the charts from the Charts menu, go to the Individual list, or search for a name or place.<br /><br />If you have trouble using the site, you can click on the Help icon to give you information on how to use the page that you are currently viewing.<br /><br />Thank you for visiting this site.";
$pgv_lang["reset_default_blocks"]	= "Reset to Default Blocks";
$pgv_lang["recent_changes"] 		= "Recent Changes";
$pgv_lang["recent_changes_block"]	= "Recent Changes";
$pgv_lang["recent_changes_descr"]	= "The Recent Changes block will list all of the changes that have been made to the database in the last month.  This block can help you stay current with the changes that have been made.  Changes are detected automatically, using the CHAN tag defined in the GEDCOM Standard.";
$pgv_lang["recent_changes_none"]	= "<b>There have been no changes within the last #pgv_lang[global_num1]# days.</b><br />";
$pgv_lang["recent_changes_some"]	= "<b>Changes made within the last #pgv_lang[global_num1]# days</b><br />";
$pgv_lang["show_empty_block"]		= "Should this block be hidden when it is empty?";
$pgv_lang["hide_block_warn"]		= "If you hide an empty block, you will not be able to change its configuration until it becomes visible by no longer being empty.";
$pgv_lang["delete_selected_messages"]	= "Delete Selected Messages";
$pgv_lang["use_blocks_for_default"]	= "Use these blocks as the default block configuration for all users?";
$pgv_lang["block_not_configure"]	=	"This block cannot be configured.";

//-- validate GEDCOM
$pgv_lang["add_media_tool"] 		= "Add Media Tool";

//-- hourglass chart
$pgv_lang["hourglass_chart"]		= "Hourglass Chart";

//-- report engine
$pgv_lang["choose_report"]			= "Choose a report to run";
$pgv_lang["enter_report_values"]	= "Enter report values";
$pgv_lang["selected_report"]		= "Selected Report";
$pgv_lang["select_report"]			= "Select report";
$pgv_lang["download_report"]		= "Download report";
$pgv_lang["reports"]				= "Reports";
$pgv_lang["pdf_reports"]			= "PDF reports";
$pgv_lang["html_reports"]			= "HTML reports";

//-- Ahnentafel report
$pgv_lang["ahnentafel_report"]		= "Ahnentafel Report";
$pgv_lang["ahnentafel_header"]		= "Ahnentafel Report for ";
$pgv_lang["ahnentafel_generation"]	= "Generation ";
$pgv_lang["ahnentafel_pronoun_m"]	= "He ";
$pgv_lang["ahnentafel_pronoun_f"]	= "She ";
$pgv_lang["ahnentafel_born_m"]		= "was born";			// male
$pgv_lang["ahnentafel_born_f"]		= "was born";			// female
$pgv_lang["ahnentafel_christened_m"] = "was christened";	// male
$pgv_lang["ahnentafel_christened_f"] = "was christened";	// female
$pgv_lang["ahnentafel_married_m"]	= "married";			// male
$pgv_lang["ahnentafel_married_f"]	= "married";			// female
$pgv_lang["ahnentafel_died_m"]		= "died";				// male
$pgv_lang["ahnentafel_died_f"]		= "died";				// female
$pgv_lang["ahnentafel_buried_m"]	= "was buried";			// male
$pgv_lang["ahnentafel_buried_f"]	= "was buried";			// female
$pgv_lang["ahnentafel_place"]		= " in ";				// place name follows this
$pgv_lang["ahnentafel_no_details"]	= " but the details are unknown";

//-- Changes report
$pgv_lang["changes_report"]			= "Changes Report";
$pgv_lang["changes_pending_tot"]	= "Total pending changes: ";
$pgv_lang["changes_accepted_tot"]	= "Total accepted changes: ";

//-- Descendancy report
$pgv_lang["descend_report"]		= "Descendancy Report";
$pgv_lang["descendancy_header"]		= "Descendancy Report for ";

$pgv_lang["family_group_report"]	= "Family Group Report";
$pgv_lang["page"]					= "Page";
$pgv_lang["of"] 					= "of";
$pgv_lang["enter_famid"]			= "Enter Family ID";
$pgv_lang["show_sources"]			= "Show sources?";
$pgv_lang["show_notes"] 			= "Show notes?";
$pgv_lang["show_basic"] 			= "Print basic events when blank?";
$pgv_lang["show_photos"]			= "Show photos?";
$pgv_lang["relatives_report_ext"]	= "Expanded Relatives Report";
$pgv_lang["with"]					= "with";
$pgv_lang["on"]						= "on";			// for precise dates
$pgv_lang["in"]						= "in";			// for imprecise dates
$pgv_lang["individual_report"]		= "Individual Report";
$pgv_lang["enter_pid"]				= "Enter Individual ID";
$pgv_lang["generated_by"]			= "Generated by";
$pgv_lang["list_children"]			= "List each child in order of birth.";
$pgv_lang["birth_report"]			= "Birth Date and Place Report";
$pgv_lang["birthplace"]				= "Birth Place contains";
$pgv_lang["birthdate1"]				= "Birth Date range start";
$pgv_lang["birthdate2"]				= "Birth Date range end";
$pgv_lang["death_report"]			= "Death Date and Place Report";
$pgv_lang["deathplace"]				= "Death Place contains";
$pgv_lang["deathdate1"]				= "Death Date range start";
$pgv_lang["deathdate2"]				= "Death Date range end";
$pgv_lang["marr_report"]			= "Marriage Date and Place Report";
$pgv_lang["marrplace"]				= "Marriage Place contains";
$pgv_lang["marrdate1"]				= "Marriage Date range start";
$pgv_lang["marrdate2"]				= "Marriage Date range end";
$pgv_lang["sort_by"]				= "Sort by";

$pgv_lang["cleanup"]				= "Cleanup";

//-- CONFIGURE (extra) messages for programs patriarch and statistics
$pgv_lang["dynasty_list"]			= "Overview of families";
$pgv_lang["patriarch_list"] 		= "Patriarch list";
$pgv_lang["statistics"] 			= "Statistics";

//-- Merge Records
$pgv_lang["merge_same"] 			= "Records are not the same type.  Cannot merge records that are not the same type.";
$pgv_lang["merge_step1"]			= "Merge Step 1 of 3";
$pgv_lang["merge_step2"]			= "Merge Step 2 of 3";
$pgv_lang["merge_step3"]			= "Merge Step 3 of 3";
$pgv_lang["select_gedcom_records"]	= "Select two GEDCOM records to merge.  The records must be of the same type.";
$pgv_lang["merge_to"]				= "Merge To ID:";
$pgv_lang["merge_from"] 			= "Merge From ID:";
$pgv_lang["merge_facts_same"]		= "The following facts were exactly the same in both records and will be merged automatically.";
$pgv_lang["no_matches_found"]		= "No matching facts found";
$pgv_lang["unmatching_facts"]		= "The following facts did not match.  Select the information you would like to keep.";
$pgv_lang["record"] 				= "Record";
$pgv_lang["adding"] 				= "Adding";
$pgv_lang["updating_linked"]		= "Updating linked record";
$pgv_lang["merge_more"] 			= "Merge more records.";
$pgv_lang["same_ids"]				= "You entered the same IDs.  You cannot merge the same records.";

//-- ANCESTRY FILE MESSAGES
$pgv_lang["ancestry_chart"] 		= "Ancestry Chart";
$pgv_lang["gen_ancestry_chart"]		= "#PEDIGREE_GENERATIONS# Generation Ancestry Chart";
$pgv_lang["chart_style"]			= "Chart style";
$pgv_lang["chart_list"]			= "List";
$pgv_lang["chart_booklet"]   	= "Booklet";
$pgv_lang["show_cousins"]			= "Show cousins";
// 1st generation
$pgv_lang["sosa_2"] 				= "Father";
$pgv_lang["sosa_3"] 				= "Mother";
// 2nd generation
$pgv_lang["sosa_4"] 				= "Grandfather";
$pgv_lang["sosa_5"] 				= "Grandmother";
$pgv_lang["sosa_6"] 				= "Grandfather";
$pgv_lang["sosa_7"] 				= "Grandmother";
// 3rd generation
$pgv_lang["sosa_8"] 				= "Great-grandfather";
$pgv_lang["sosa_9"] 				= "Great-grandmother";
$pgv_lang["sosa_10"]				= "Great-grandfather";
$pgv_lang["sosa_11"]				= "Great-grandmother";
$pgv_lang["sosa_12"]				= "Great-grandfather";
$pgv_lang["sosa_13"]				= "Great-grandmother";
$pgv_lang["sosa_14"]				= "Great-grandfather";
$pgv_lang["sosa_15"]				= "Great-grandmother";
// 4th generation
$pgv_lang["sosa_16"]				= "Great-great-grandfather";
$pgv_lang["sosa_17"]				= "Great-great-grandmother";
$pgv_lang["sosa_18"]				= "Great-great-grandfather";
$pgv_lang["sosa_19"]				= "Great-great-grandmother";
$pgv_lang["sosa_20"]				= "Great-great-grandfather";
$pgv_lang["sosa_21"]				= "Great-great-grandmother";
$pgv_lang["sosa_22"]				= "Great-great-grandfather";
$pgv_lang["sosa_23"]				= "Great-great-grandmother";
$pgv_lang["sosa_24"]				= "Great-great-grandfather";
$pgv_lang["sosa_25"]				= "Great-great-grandmother";
$pgv_lang["sosa_26"]				= "Great-great-grandfather";
$pgv_lang["sosa_27"]				= "Great-great-grandmother";
$pgv_lang["sosa_28"]				= "Great-great-grandfather";
$pgv_lang["sosa_29"]				= "Great-great-grandmother";
$pgv_lang["sosa_30"]				= "Great-great-grandfather";
$pgv_lang["sosa_31"]				= "Great-great-grandmother";

// for the general case of ancestors of the nth generation use the text below
// in this text %1\$d is replaced with the number of generations
//              %2\$d is replaced with the number of generations - 1
//              %3\$d is replaced with the number of generations - 2
$pgv_lang["sosa_paternal_male_n_generations"]	= "%3\$d x paternal great grandfather";
$pgv_lang["sosa_paternal_female_n_generations"]	= "%3\$d x paternal great grandmother";
$pgv_lang["sosa_maternal_male_n_generations"]	= "%3\$d x maternal great grandfather";
$pgv_lang["sosa_maternal_female_n_generations"]	= "%3\$d x maternal great grandmother";

//-- FAN CHART
$pgv_lang["compact_chart"]			= "Compact Chart";
$pgv_lang["fan_chart"]				= "Circle Diagram";
$pgv_lang["gen_fan_chart"]  		= "#PEDIGREE_GENERATIONS# Generation Circle Diagram";
$pgv_lang["fan_width"]				= "Width";
$pgv_lang["gd_library"]				= "PHP server misconfiguration: GD 2.x library required to use image functions.";
$pgv_lang["gd_freetype"]			= "PHP server misconfiguration: FreeType library required to use TrueType fonts.";
$pgv_lang["gd_helplink"]			= "http://www.php.net/gd";
$pgv_lang["fontfile_error"]			= "Font file not found on PHP server";
$pgv_lang["fanchart_IE"]			= "This Fanchart image cannot be printed directly by your browser. Use right-click then save and print.";

//-- RSS Feed
$pgv_lang["rss_descr"]				= "News and links from the #GEDCOM_TITLE# site";
$pgv_lang["rss_logo_descr"]			= "Feed created by PhpGedView";
$pgv_lang["rss_feeds"]				= "RSS Feeds";
$pgv_lang["no_feed_title"]			= "Feed not available";
$pgv_lang["no_feed"]				= "There is no RSS feed available for this PhpGedView site";
$pgv_lang["feed_login"]				= "If you have an account at this PhpGedView site, you can <a href=\"#AUTH_URL#\">log in</a> to the server using Basic HTTP Authentication to view private information.";
$pgv_lang["authenticated_feed"]		= "Authenticated Feed";

//-- ASSOciates RELAtionship
// After any change in the following list, please check $assokeys in edit_interface.php
$pgv_lang["attendant"] = "Attendant";
$pgv_lang["attending"] = "Attending";
$pgv_lang["best_man"] = "Best Man";
$pgv_lang["bridesmaid"] = "Bridesmaid";
$pgv_lang["buyer"] = "Buyer";
$pgv_lang["circumciser"] = "Circumciser";
$pgv_lang["civil_registrar"] = "Civil Registrar";
$pgv_lang["friend"] = "Friend";
$pgv_lang["godfather"] = "Godfather";
$pgv_lang["godmother"] = "Godmother";
$pgv_lang["godparent"] = "Godparent";
$pgv_lang["informant"] = "Informant";
$pgv_lang["lodger"] = "Lodger";
$pgv_lang["nurse"] = "Nurse";
$pgv_lang["priest"] = "Priest";
$pgv_lang["rabbi"] = "Rabbi";
$pgv_lang["registry_officer"] = "Registry Officer";
$pgv_lang["seller"] = "Seller";
$pgv_lang["servant"] = "Servant";
$pgv_lang["twin"] = "Twin";
$pgv_lang["twin_brother"] = "Twin brother";
$pgv_lang["twin_sister"] = "Twin sister";
$pgv_lang["witness"] = "Witness";

//-- statistics utility
$pgv_lang["statutci"]			= "unable to create index";
$pgv_lang["statnnames"]                = "number of names    =";
$pgv_lang["statnfam"]                  = "number of families =";
$pgv_lang["statnmale"]                 = "number of males    =";
$pgv_lang["statnfemale"]               = "number of females  =";
$pgv_lang["statvars"]			 = "Fill in the following parameters for the plot";
$pgv_lang["statlxa"]			 = "along X axis:";
$pgv_lang["statlya"]			 = "along Y axis:";
$pgv_lang["statlza"]			 = "along Z axis";
$pgv_lang["stat_10_none"]		 = "none";
$pgv_lang["stat_11_mb"]			 = "Month of birth";
$pgv_lang["stat_12_md"]			 = "Month of death";
$pgv_lang["stat_13_mm"]			 = "Month of marriage";
$pgv_lang["stat_14_mb1"]		= "Month of birth of first child in a relation";
$pgv_lang["stat_15_mm1"]		= "Month of first marriage";
$pgv_lang["stat_16_mmb"]		= "Months between marriage and first child";
$pgv_lang["stat_17_arb"]			 = "age related to birth year";
$pgv_lang["stat_18_ard"]			 = "age related to death year";
$pgv_lang["stat_19_arm"]			 = "age in year of marriage";
$pgv_lang["stat_20_arm1"]			 = "age in year of first marriage";
$pgv_lang["stat_21_nok"]			 = "number of children";
$pgv_lang["stat_200_none"]			 = "all (or blank)";
$pgv_lang["stat_201_num"]			 = "numbers";
$pgv_lang["stat_202_perc"]			 = "percentage";
$pgv_lang["stat_300_none"]		= "none";
$pgv_lang["stat_301_mf"]			 = "gender";
$pgv_lang["stat_302_cgp"]			 = "date periods";
$pgv_lang["statmess1"]			 = "<b>The following entries relate to the above plot parameters for the X and Z axes</b>";
$pgv_lang["statar_xgp"]			 = "X axis boundaries (periods):";
$pgv_lang["statar_xgl"]			 = "X axis boundaries (ages):";
$pgv_lang["statar_xgm"]			 = "X axis boundaries (month):";
$pgv_lang["statar_xga"]			 = "X axis boundaries (numbers):";
$pgv_lang["statar_zgp"]			 = "X axis boundaries (date periods):";
$pgv_lang["statreset"]			 = "reset";
$pgv_lang["statsubmit"]			 = "show the plot";

//-- statisticsplot utility
$pgv_lang["statistiek_list"]	= "Statistics Plot";
$pgv_lang["stpl"]			 	= "...";
$pgv_lang["stplGDno"]			 = "Graphics Display Library is not installed on the server. Please contact your system administrator.";
$pgv_lang["stpljpgraphno"]		= "JPgraph library is not installed in PhpGedView. Please download it from http://www.aditus.nu/jpgraph/jpdownload.php<br /> and then copy it to subdirectory <i>jpgraph/</i> on the server.";
$pgv_lang["stplinfo"]			 = "plotting information:";
$pgv_lang["stpltype"]			 = "type:";
$pgv_lang["stplnoim"]			 = " not implemented:";
$pgv_lang["stplmf"]			 = " / man-woman";
$pgv_lang["stplipot"]			 = " / per timeperiod";
$pgv_lang["stplgzas"]			 = "Z axis boundaries:";
$pgv_lang["stplmonth"]			 = "month";
$pgv_lang["stplnumbers"]		 = "numbers for a family";
$pgv_lang["stplage"]			 = "age";
$pgv_lang["stplperc"]			 = "percentage";
$pgv_lang["stplnumof"]			 = "Counts ";
$pgv_lang["stplmarrbirth"]		 = "Months between marriage and birth of first child";

//-- alive in year
$pgv_lang["alive_in_year"]			= "Alive in Year";
$pgv_lang["is_alive_in"]			= "Is alive in #YEAR#";
$pgv_lang["alive"]					= "Alive ";
$pgv_lang["dead"]					= "Dead ";
$pgv_lang["maybe"]					= "Maybe ";
$pgv_lang["both_alive"]					= "Both alive ";
$pgv_lang["both_dead"]					= "Both dead ";

//-- Help system
$pgv_lang["definitions"]			= "Definitions";

//-- Index_edit
$pgv_lang["block_desc"]				= "Block Descriptions";
$pgv_lang["click_here"]				= "Click here to continue";
$pgv_lang["click_here_help"]		= "~#pgv_lang[click_here]#~<br /><br />Click this button to save your changes.<br /><br />You will be returned to the #pgv_lang[welcome]# or #pgv_lang[mygedview]# page, but your changes may not be shown.  You may need to use the Page Reload function of your browser to view your changes properly.";
$pgv_lang["block_summaries"]		= "~#pgv_lang[block_desc]#~<br /><br />Here is a short description of each of the blocks you can place on the #pgv_lang[welcome]# or #pgv_lang[mygedview]# page.<br /><table border='1' align='center'><tr><td class='list_value'><b>#pgv_lang[name]#</b></td><td class='list_value'><b>#pgv_lang[description]#</b></td></tr>#pgv_lang[block_summary_table]#</table><br /><br />";
// Built in index_edit.php
$pgv_lang["block_summary_table"]	= "&nbsp;";

//-- Find page
$pgv_lang["total_places"]			= "Places found";
$pgv_lang["media_contains"]			= "Media contains:";
$pgv_lang["repo_contains"]			= "Repository contains:";
$pgv_lang["source_contains"]		= "Source contains:";
$pgv_lang["display_all"]			= "Display all";

//-- accesskey navigation
$pgv_lang["accesskeys"]				= "Keyboard Shortcuts";
$pgv_lang["accesskey_skip_to_content"]	= "C";
$pgv_lang["accesskey_search"]	= "S";
$pgv_lang["accesskey_skip_to_content_desc"]	= "Skip to Content";
$pgv_lang["accesskey_viewing_advice"]	= "0";
$pgv_lang["accesskey_viewing_advice_desc"]	= "Viewing advice";
$pgv_lang["accesskey_home_page"]	= "1";
$pgv_lang["accesskey_help_content"]	= "2";
$pgv_lang["accesskey_help_current_page"]	= "3";
$pgv_lang["accesskey_contact"]	= "4";

$pgv_lang["accesskey_individual_details"]	= "I";
$pgv_lang["accesskey_individual_relatives"]	= "R";
$pgv_lang["accesskey_individual_notes"]	= "N";
$pgv_lang["accesskey_individual_sources"]	= "O";
//clash with IE addBookmark but not a likely problem
$pgv_lang["accesskey_individual_media"]	= "A";
$pgv_lang["accesskey_individual_research_log"]	= "L";
$pgv_lang["accesskey_individual_pedigree"]	= "P";
$pgv_lang["accesskey_individual_descendancy"]	= "D";
$pgv_lang["accesskey_individual_timeline"]	= "T";
$pgv_lang["accesskey_individual_relation_to_me"]	= "M";
//clash with rarely used English Netscape/Mozilla Go menu
$pgv_lang["accesskey_individual_gedcom"]	= "G";

$pgv_lang["accesskey_family_parents_timeline"]	= "P";
$pgv_lang["accesskey_family_children_timeline"]	= "D";
$pgv_lang["accesskey_family_timeline"]	= "T";
//clash with rarely used English Netscape/Mozilla English Go menu
$pgv_lang["accesskey_family_gedcom"]	= "G";

// FAQ Page
$pgv_lang["add_faq_header"] = "FAQ Header";
$pgv_lang["add_faq_body"] = "FAQ Body";
$pgv_lang["add_faq_order"] = "FAQ Position";
$pgv_lang["add_faq_visibility"] = "FAQ Visibility";
$pgv_lang["no_faq_items"] = "The FAQ list is empty.";
$pgv_lang["position_item"] = "Position item";
$pgv_lang["faq_list"] = "FAQ List";
$pgv_lang["confirm_faq_delete"] = "Are you sure you want to delete the FAQ entry";
$pgv_lang["preview"] =  "Preview";
$pgv_lang["no_id"] = "No FAQ ID has been specified !";

// Help search
$pgv_lang["hs_title"] 			= "Search Help Text";
$pgv_lang["hs_search"] 			= "Search";
$pgv_lang["hs_close"] 			= "Close window";
$pgv_lang["hs_results"] 		= "Results found:";
$pgv_lang["hs_keyword"] 		= "Search for";
$pgv_lang["hs_searchin"]		= "Search in";
$pgv_lang["hs_searchuser"]		= "User Help";
$pgv_lang["hs_searchmodules"]	= "Modules Help";
$pgv_lang["hs_searchconfig"]	= "Administrator Help";
$pgv_lang["hs_searchhow"]		= "Search type";
$pgv_lang["hs_searchall"]		= "All words";
$pgv_lang["hs_searchany"]		= "Any word";
$pgv_lang["hs_searchsentence"]	= "Exact phrase";
$pgv_lang["hs_intruehelp"]		= "Help text only";
$pgv_lang["hs_inallhelp"]		= "All text";

// Media import
$pgv_lang["choose"] = "Choose: ";
$pgv_lang["account_information"] = "Account Information";

//-- Media item "TYPE" sub-field
$pgv_lang["TYPE__audio"] = "Audio";
$pgv_lang["TYPE__book"] = "Book";
$pgv_lang["TYPE__card"] = "Card";
$pgv_lang["TYPE__certificate"] = "Certificate";
$pgv_lang["TYPE__document"] = "Document";
$pgv_lang["TYPE__electronic"] = "Electronic";
$pgv_lang["TYPE__fiche"] = "Microfiche";
$pgv_lang["TYPE__film"] = "Microfilm";
$pgv_lang["TYPE__magazine"] = "Magazine";
$pgv_lang["TYPE__manuscript"] = "Manuscript";
$pgv_lang["TYPE__map"] = "Map";
$pgv_lang["TYPE__newspaper"] = "Newspaper";
$pgv_lang["TYPE__photo"] = "Photo";
$pgv_lang["TYPE__tombstone"] = "Tombstone";
$pgv_lang["TYPE__video"] = "Video";
$pgv_lang["TYPE__painting"] = "Painting";
$pgv_lang["TYPE__other"] = "Other";

//-- Other media suff
$pgv_lang["view_slideshow"] 		= "View as slideshow";
$pgv_lang["download_image"]			= "Download File";
$pgv_lang["no_media"]				= "No Media Found";
$pgv_lang["media_privacy"]			= "Privacy restrictions prevent you from viewing this item";
$pgv_lang["relations_heading"]		= "The image relates to:";
$pgv_lang["file_size"]				= "File Size";
$pgv_lang["img_size"]				= "Image Size";
$pgv_lang["media_broken"]			= "This media file is broken and cannot be watermarked";
$pgv_lang["unknown_mime"]			= "Media Firewall error: >Unknown Mimetype< for file";

//-- Modules
$pgv_lang["module_error_unknown_action_v2"] = "Unknown action: [action].";
$pgv_lang["module_error_unknown_type"] = "Unknown module type.";

//-- sortable tables buttons
$pgv_lang["button_alive_in_year"] = "Show persons alive in the indicated year.";
$pgv_lang["button_BIRT_Y100"] = "Show persons born within the last 100 years.";
$pgv_lang["button_BIRT_YES"] = "Show persons born more than 100 years ago.";
$pgv_lang["button_DEAT_H"] = "Show couples where only the male partner is deceased.";
$pgv_lang["button_DEAT_N"] = "Show people who are alive or couples where both partners are alive.";
$pgv_lang["button_DEAT_W"] = "Show couples where only the female partner is deceased.";
$pgv_lang["button_DEAT_Y"] = "Show people who are dead or couples where both partners are deceased.";
$pgv_lang["button_DEAT_Y100"] = "Show people who died within the last 100 years.";
$pgv_lang["button_DEAT_YES"] = "Show people who died more than 100 years ago.";
$pgv_lang["button_MARR_DIV"] = "Show divorced couples.";
$pgv_lang["button_MARR_U"] = "Show couples with an unknown marriage date.";
$pgv_lang["button_MARR_Y100"] = "Show couples who married within the last 100 years.";
$pgv_lang["button_MARR_YES"] = "Show couples who married more than 100 years ago.";
$pgv_lang["button_reset"] = "Reset to the list defaults.";
$pgv_lang["button_SEX_F"] = "Show only females.";
$pgv_lang["button_SEX_M"] = "Show only males.";
$pgv_lang["button_SEX_U"] = "Show only persons of whom the gender is not known.";
$pgv_lang["button_TREE_L"] = "Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.";
$pgv_lang["button_TREE_R"] = "Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.";
$pgv_lang["sort_column"] = "Sort by this column.";
?>

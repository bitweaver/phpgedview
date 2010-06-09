<?php
/**
 * Display a diff between two language files to help in translating.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and Others, all rights reserved.
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
 * @version $Id$
 */
 
require "config.php";
require  $confighelpfile["english"];
if (file_exists( $confighelpfile[$LANGUAGE])) require  $confighelpfile[$LANGUAGE];

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)) {
	header("Location: login.php?url=changelanguage.php");
	exit;
}

$action =safe_REQUEST($_REQUEST, 'action',  PGV_REGEX_UNSAFE);

if (!isset($action) or $action=="") $action="editold";

switch ($action) {
	case "addnew" :
		$helpindex = "add_new_language_help";
		print_header($pgv_lang["add_new_language"]);
		break;

	case "editold" :
	default :
		print_header($pgv_lang["config_lang_utility"]);
}

print PGV_JS_START;
print "var helpWin;";
print "function showchanges() {";
print "window.location = '{$_SERVER['PHP_SELF']}';";
print "}";
print "function helpPopup03(which) {";
print "location.href = 'editlang_edit_settings.php?' + which + '&new_shortcut=' + document.new_lang_form.new_shortcut.value;";
print "return false;";
print "}";
print PGV_JS_END;

// Create array with configured languages in gedcoms and users
$configuredlanguages = array();

// Read GEDCOMS configuration and collect language data
foreach (get_all_gedcoms() as $ged_id => $ged_name) {
	require get_gedcom_setting($ged_id, 'config');
	if (!isset($configuredlanguages["gedcom"][$LANGUAGE][$ged_name])) {
		$configuredlanguages["gedcom"][$LANGUAGE][$ged_name] = true;
	}
}
// Restore the current settings
require get_gedcom_setting(PGV_GED_ID, 'config');

// Read user configuration and collect language data
foreach (get_all_users() as $user_id=>$user_name) {
	if (!isset($configuredlanguages["users"][get_user_setting($user_id, 'language')][$user_id])) {
		$configuredlanguages["users"][get_user_setting($user_id, 'language')][$user_id] = true;
	}
}

// Sort the Language table into localized language name order
foreach ($pgv_language as $key => $value){
	$d_LangName = "lang_name_".$key;
	$Sorted_Langs[$key] = $pgv_lang[$d_LangName];
}
asort($Sorted_Langs);

// Split defined languages into active and inactive
$split_langs_active = array();
$split_langs_inactive = array();
foreach ($Sorted_Langs as $key => $value){
	if ($pgv_lang_use["$key"]) {
		$split_langs_active[count($split_langs_active)+1] = $key;
	}
	else {
		$split_langs_inactive[count($split_langs_inactive)+1] = $key;
	}
}

$active = count($split_langs_active);
$inactive = count($split_langs_inactive);
$maxlines = max($active, $inactive);

/* Language File Settings Mask */

print "<div class=\"center\">";
print "<a href=\"admin.php\" style=\"font-weight: bold;\">";
print $pgv_lang["lang_back_admin"];
print "</a><br />";
print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";

//-- Choose the language you want to edit the settings of

switch ($action) {
	case "addnew" :
		print "<form name=\"new_lang_form\" method=\"get\" action=\"$SCRIPT_NAME\">";
		print "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\" />";
		print "<input type=\"hidden\" name=\"action\" value=\"new_lang\" />";
		print "<input type=\"hidden\" name=\"execute\" value=\"true\" />";
		print "<table class=\"facts_table center $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"2\">";
		print $pgv_lang["add_new_language"];
		print "</td></tr>";

		require( "includes/lang_codes_std.php");
		print "<tr><td class=\"facts_value center\"><select name=\"new_shortcut\">\n";

		asort($lng_codes);		// Sort the language codes table into language name order

		foreach ($lng_codes as $key => $value) {
			$showLang = true;
			foreach ($lang_short_cut as $key02=>$value) {
				if ($value == $key) {		// This language is already in PGV
					$showLang = false;
					break;
				}
			}
			if ($showLang) {
				print "<option value=\"$key\"";
				print ">".$lng_codes[$key][0]."</option>\n";
			}
		}
		print "</select>\n\n";
		print_help_link("add_new_language_help", "qm");
		print "</td>";
		print "<td class=\"facts_value center\"><input type=\"submit\" value=\"" . $pgv_lang["add_new_lang_button"] . "\" onclick=\"return helpPopup03('" . "action=new_lang" . "&amp;" . session_name() . "=" . session_id() . "'); \" /></td></tr>";
		$USERLANG = $LANGUAGE;
		break;

	case "editold" :
	default :
		print "<form name=\"lang_config_form\" method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">";
		print "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\" />";
		print "<input type=\"hidden\" name=\"action\" value=\"config_lang\" />";
		print "<table class=\"facts_table center $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"7\">";
		print $pgv_lang["config_lang_utility"];
		print_help_link("config_lang_utility_help", "qm");
		print "</td></tr>";

		print "<tr>";


		// Column headings, left set
		print "<td class=\"facts_label03\">";
		print $pgv_lang["lang_language"];
		print "</td>";

		print "<td class=\"facts_label03\">";
		print $pgv_lang["active"];
		print "</td>";

		print "<td class=\"facts_label03\">";
		print $pgv_lang["edit_settings"];
		print "</td>";

		// Separator
		print "<td class=\"facts_label03\">" . "&nbsp;" . "</td>";

		// Column headings, right set
		print "<td class=\"facts_label03\">";
		print $pgv_lang["lang_language"];
		print "</td>";

		print "<td class=\"facts_label03\">";
		print $pgv_lang["active"];
		print "</td>";

		print "<td class=\"facts_label03\">";
		print $pgv_lang["edit_settings"];
		print "</td>";

		// End of row
		print "</tr>\n";

		// Print the Language table in sorted name order
		for ($i=1; $i<=$maxlines; $i++) {
			// print "<form name=\"activelanguage\">";
			print "<tr>";
			// Left 3 columns: Active language
			$value = "";
			if ($i <= $active) $value = $split_langs_active[$i];

			if ($value == "") {
				print "<td class=\"facts_value\">&nbsp;</td><td class=\"facts_value\">&nbsp;</td><td class=\"facts_value\">&nbsp;</td>";
			} else {
				$d_LangName = "lang_name_" . $value;
				print "<td class=\"facts_value\" style=\"text-align: center;\">";
					print $pgv_lang[$d_LangName];
				print "</td>";
				print "<td class=\"facts_value\" style=\"text-align: center;\">";
					print "<input";
					if (array_key_exists($value, $configuredlanguages["gedcom"]) or array_key_exists($value, $configuredlanguages["users"])) print " disabled=\"disabled\"";
					print " type=\"checkbox\" value=\"$value\" checked=\"checked\" onclick=\"enabledisablelanguage('$value');\" />";
				print "</td>";
				print "<td class=\"facts_value\" style=\"text-align: center;\">";
					print "<a href=\"editlang_edit_settings.php?ln=" . $value . "\">";
					print $pgv_lang["lang_edit"] . "</a>";
				print "</td>";
			}

			// Middle column: Separator
			print "<td class=\"facts_label03\">" . "&nbsp;" . "</td>";

			// Right 3 columns: Inactive language
			$value = "";
			if ($i <= $inactive) $value = $split_langs_inactive[$i];

			if ($value == "") {
				print "<td class=\"facts_value\">&nbsp;</td><td class=\"facts_value\">&nbsp;</td><td class=\"facts_value\">&nbsp;</td>";
			} else {
				$d_LangName = "lang_name_" . $value;
				print "<td class=\"facts_value\" style=\"text-align: center;\">";
					print $pgv_lang[$d_LangName];
				print "</td>";
				print "<td class=\"facts_value\" style=\"text-align: center;\">";
					print "<input type=\"checkbox\" value=\"$value\" onclick=\"enabledisablelanguage('$value');\"/>";
				print "</td>";
				print "<td class=\"facts_value\" style=\"text-align: center;\">";
					print "<a href=\"editlang_edit_settings.php?ln=" . $value . "\">";
					print $pgv_lang["lang_edit"] . "</a>";
				print "</td>";
			}
			print "</tr>";
			// print "</form>";
		}
		$USERLANG = $LANGUAGE;
		print "<tr>";
		print "<td class=\"facts_label03\" colspan=\"7\">";
			print $pgv_lang["configured_languages"];
		print "</td>";
		print "</tr>";
		print "<tr>";
		print "<td class=\"facts_value\" colspan=\"3\" rowspan=\"".count($configuredlanguages["gedcom"])."\" valign=\"top\">";
			print $pgv_lang["current_gedcoms"];
		print "</td>";
		foreach ($configuredlanguages["gedcom"] as $key => $value) {
			if (!isset($currentkey)) $currentkey = $key;
			if ($currentkey != $key) {
				print "</td></tr><tr><td class=\"facts_value\" colspan=\"2\" valign=\"top\">";
				$currentkey = $key;
			}
			else print "<td class=\"facts_value\" colspan=\"2\" valign=\"top\">";

			// Print gedcom names
			foreach($value as $gedcomname => $used) {
				echo '<a href="', encode_url("editconfig_gedcom.php?ged={$gedcomname}"), '" target="blank">', $gedcomname, '</a><br />';
			}
			print "</td><td class=\"facts_value\" colspan=\"2\" valign=\"top\">";
			// Print language name and flag
			print "<img src=\"".$language_settings[$key]["flagsfile"]."\" class=\"brightflag\" alt=\"".$pgv_lang["lang_name_".$key]."\" title=\"".$pgv_lang["lang_name_".$key]."\" />&nbsp;".$pgv_lang["lang_name_".$key]."<br />";
		}
		print "</td></tr><tr><td  class=\"facts_value\" colspan=\"5\" valign=\"top\">".$pgv_lang["users_langs"]."</td><td class=\"facts_value\" colspan=\"2\">";
		foreach ($configuredlanguages["users"] as $key => $value) {
			print "<img src=\"".$language_settings[$key]["flagsfile"]."\" class=\"brightflag\" alt=\"".$pgv_lang["lang_name_".$key]."\" title=\"".$pgv_lang["lang_name_".$key]."\" />&nbsp;<a href=\"".encode_url("useradmin.php?action=listusers&filter=language&usrlang={$key}")."\">".$pgv_lang["lang_name_".$key]."</a><br />";
		}
	print "</td></tr>";
}
print "</table>";
print "</form>";
$LANGUAGE = $USERLANG;
print "</div>";

print_footer();
?>

<?php
/**
 * Edit Privacy Settings
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @author PGV Development Team
 * @package PhpGedView
 * @subpackage Privacy
 * @version $Id: edit_privacy.php,v 1.7 2010/02/08 21:27:24 wjames5 Exp $
 */

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require 'config.php';

require 'includes/functions/functions_print_facts.php';
loadLangFile('pgv_confighelp, pgv_help');


if (empty($ged)) $ged = $GEDCOM;

if (!userGedcomAdmin(PGV_USER_ID, $ged)) {
	header('Location: editgedcoms.php');
	exit;
}

$PRIVACY_CONSTANTS=array(
	PGV_PRIV_NONE  =>'PGV_PRIV_NONE',
	PGV_PRIV_USER  =>'PGV_PRIV_USER',
	PGV_PRIV_PUBLIC=>'PGV_PRIV_PUBLIC',
	PGV_PRIV_HIDE  =>'PGV_PRIV_HIDE'
);

$action=safe_POST('action', 'update');

$v_new_person_privacy_access_ID		= safe_POST('v_new_person_privacy_access_ID',		PGV_REGEX_XREF);
$v_new_person_privacy_access_option	= safe_POST('v_new_person_privacy_access_option',	$PRIVACY_CONSTANTS);
$v_person_privacy_del				= safe_POST('v_person_privacy_del',					'1');
$v_person_privacy					= safe_POST('v_person_privacy',						$PRIVACY_CONSTANTS);

$v_new_user_privacy_username		= safe_POST('v_new_user_privacy_username',			get_all_users());
$v_new_user_privacy_access_ID		= safe_POST('v_new_user_privacy_access_ID',			PGV_REGEX_XREF);
$v_new_user_privacy_access_option	= safe_POST('v_new_user_privacy_access_option',		$PRIVACY_CONSTANTS);
$v_user_privacy_del					= safe_POST('v_user_privacy_del',					'1');
$v_user_privacy						= safe_POST('v_user_privacy');

$v_new_global_facts_abbr			= safe_POST('v_new_global_facts_abbr',				array_keys($factarray));
$v_new_global_facts_choice			= safe_POST('v_new_global_facts_choice',			array('show', 'details'));
$v_new_global_facts_access_option	= safe_POST('v_new_global_facts_access_option',		$PRIVACY_CONSTANTS);
$v_global_facts_del					= safe_POST('v_global_facts_del',					'1');
$v_global_facts						= safe_POST('v_global_facts');

$v_new_person_facts_access_ID		= safe_POST('v_new_person_facts_access_ID',			PGV_REGEX_XREF);
$v_new_person_facts_abbr			= safe_POST('v_new_person_facts_abbr',				array_keys($factarray));
$v_new_person_facts_choice			= safe_POST('v_new_person_facts_choice',			array('show', 'details'));
$v_new_person_facts_access_option	= safe_POST('v_new_person_facts_access_option',		$PRIVACY_CONSTANTS);
$v_person_facts_del					= safe_POST('v_person_facts_del',					'1');
$v_person_facts						= safe_POST('v_person_facts');

// These values may not be present in privacy files created by old versions of PGV
if (!isset($PRIVACY_BY_YEAR)) $PRIVACY_BY_YEAR = false;
if (!isset($MAX_ALIVE_AGE)) $MAX_ALIVE_AGE = 120;

/**
 * print yes/no select option
 *
 * @param string $checkVar
 */
function write_yes_no($checkVar) {
	global $pgv_lang;

	print "<option";
	if ($checkVar == false) print " selected=\"selected\"";
	print " value=\"no\">";
	print $pgv_lang["no"];
	print "</option>\n";

	print "<option";
	if ($checkVar == true) print " selected=\"selected\"";
	print " value=\"yes\">";
	print $pgv_lang["yes"];
	print "</option>";
}

/**
 * print find and print gedcom record ID
 *
 * @param string $checkVar	gedcom key
 * @param string $outputVar	error message style
 */
function search_ID_details($checkVar, $outputVar) {
	global $pgv_lang;

	$record=GedcomRecord::getInstance($checkVar);
	if ($record) {
		echo $record->format_list('span');
	} else {
		print "<span class=\"error\">";
		if ($outputVar == 1) {
			print $pgv_lang["unable_to_find_privacy_indi"];
			print "<br />[" . $checkVar . "]";
		}
		if ($outputVar == 2) {
			print $pgv_lang["unable_to_find_privacy_indi"];
		}
		print "</span><br /><br />";
	}
}


$PRIVACY_MODULE = get_privacy_file();

print_header($pgv_lang["privacy_header"]);

if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';
?>
<table class="facts_table <?php print $TEXT_DIRECTION; ?>">
	<tr>
		<td colspan="2" class="facts_label"><?php
			print "<h2>".$pgv_lang["edit_privacy_title"]." - ".PrintReady(strip_tags(get_gedcom_setting($ged, 'title'))). "</h2>";
			print "(" . getLRM() . $PRIVACY_MODULE.")";
			print "<br /><br /><a href=\"editgedcoms.php\"><b>";
			print $pgv_lang["lang_back_manage_gedcoms"];
			print "</b></a><br /><br />"; ?>
		</td>
	</tr>
</table>
<?php
if ($action=="update") {
	$boolarray = array();
	$boolarray["yes"] = "true";
	$boolarray["no"] = "false";
	$boolarray[false] = "false";
	$boolarray[true] = "true";
	print "<table class=\"facts_table $TEXT_DIRECTION\">";
	print "<tr><td class=\"descriptionbox\">";
	print $pgv_lang["performing_update"];
	print "<br />";
	$configtext = implode('', file("privacy.php"));
	print $pgv_lang["config_file_read"];
	print "</td></tr></table>\n";
	$configtext = preg_replace('/\$SHOW_DEAD_PEOPLE\s*=\s*.*;/', "\$SHOW_DEAD_PEOPLE = ".$_POST["v_SHOW_DEAD_PEOPLE"].";", $configtext);
	$configtext = preg_replace('/\$SHOW_LIVING_NAMES\s*=\s*.*;/', "\$SHOW_LIVING_NAMES = ".$_POST["v_SHOW_LIVING_NAMES"].";", $configtext);
	$configtext = preg_replace('/\$SHOW_SOURCES\s*=\s*.*;/', "\$SHOW_SOURCES = ".$_POST["v_SHOW_SOURCES"].";", $configtext);
	$configtext = preg_replace('/\$MAX_ALIVE_AGE\s*=\s*".*";/', "\$MAX_ALIVE_AGE = \"".$_POST["v_MAX_ALIVE_AGE"]."\";", $configtext);
	if ($MAX_ALIVE_AGE!=$_POST["v_MAX_ALIVE_AGE"]) reset_isdead(get_id_from_gedcom($ged));
	if (file_exists("modules/research_assistant.php")) {
		$configtext = preg_replace('/\$SHOW_RESEARCH_ASSISTANT\s*=\s*.*;/', "\$SHOW_RESEARCH_ASSISTANT = ".$_POST["v_SHOW_RESEARCH_ASSISTANT"].";", $configtext);
	}
	$configtext = preg_replace('/\$SHOW_MULTISITE_SEARCH\s*=\s*.*;/', "\$SHOW_MULTISITE_SEARCH = ".$_POST["v_SHOW_MULTISITE_SEARCH"].";", $configtext);
	$configtext = preg_replace('/\$ENABLE_CLIPPINGS_CART\s*=\s*.*;/', "\$ENABLE_CLIPPINGS_CART = ".$_POST["v_ENABLE_CLIPPINGS_CART"].";", $configtext);
	$configtext = preg_replace('/\$PRIVACY_BY_YEAR\s*=\s*.*;/', "\$PRIVACY_BY_YEAR = ".$boolarray[$_POST["v_PRIVACY_BY_YEAR"]].";", $configtext);
	$configtext = preg_replace('/\$PRIVACY_BY_RESN\s*=\s*.*;/', "\$PRIVACY_BY_RESN = ".$boolarray[$_POST["v_PRIVACY_BY_RESN"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_DEAD_PEOPLE\s*=\s*.*;/', "\$SHOW_DEAD_PEOPLE = ".$_POST["v_SHOW_DEAD_PEOPLE"].";", $configtext);
	$configtext = preg_replace('/\$USE_RELATIONSHIP_PRIVACY\s*=\s*.*;/', "\$USE_RELATIONSHIP_PRIVACY = ".$boolarray[$_POST["v_USE_RELATIONSHIP_PRIVACY"]].";", $configtext);
	$configtext = preg_replace('/\$MAX_RELATION_PATH_LENGTH\s*=\s*.*;/', "\$MAX_RELATION_PATH_LENGTH = \"".$_POST["v_MAX_RELATION_PATH_LENGTH"]."\";", $configtext);
	$configtext = preg_replace('/\$CHECK_MARRIAGE_RELATIONS\s*=\s*.*;/', "\$CHECK_MARRIAGE_RELATIONS = ".$boolarray[$_POST["v_CHECK_MARRIAGE_RELATIONS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_PRIVATE_RELATIONSHIPS\s*=\s*.*;/', "\$SHOW_PRIVATE_RELATIONSHIPS = ".$boolarray[$_POST["v_SHOW_PRIVATE_RELATIONSHIPS"]].";", $configtext);

	//-- Update the "Person Privacy" section
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start person privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end person privacy --//"));
	$person_privacy_text = "//-- start person privacy --//\n\$person_privacy = array();\n";
	if (!isset($v_person_privacy) || !is_array($v_person_privacy)) $v_person_privacy = array();
	foreach($person_privacy as $key=>$value) {
		if (isset($v_person_privacy_del[$key]) || $key==$v_new_person_privacy_access_ID) continue;
		if (isset($v_person_privacy[$key])) $person_privacy_text .= "\$person_privacy['$key'] = ".$v_person_privacy[$key].";\n";
		else $person_privacy_text .= "\$person_privacy['$key'] = ".$PRIVACY_CONSTANTS[$value].";\n";
	}
	if ($v_new_person_privacy_access_ID && $v_new_person_privacy_access_option) {
		$gedobj = new GedcomRecord(find_gedcom_record($v_new_person_privacy_access_ID));
		$v_new_person_privacy_access_ID = $gedobj->getXref();
		if ($v_new_person_privacy_access_ID) $person_privacy_text .= "\$person_privacy['$v_new_person_privacy_access_ID'] = ".$v_new_person_privacy_access_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;

	//-- Update the "User Privacy" section
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start user privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end user privacy --//"));
	$person_privacy_text = "//-- start user privacy --//\n\$user_privacy = array();\n";
	if (!isset($v_user_privacy) || !is_array($v_user_privacy)) $v_user_privacy = array();
	foreach($user_privacy as $key=>$value) {
		foreach($value as $id=>$setting) {
			if (isset($v_user_privacy_del[$key][$id]) || ($key==$v_new_user_privacy_username && $id==$v_new_user_privacy_access_ID)) continue;
			if (isset($v_user_privacy[$key][$id])) $person_privacy_text .= "\$user_privacy['$key']['$id'] = ".$v_user_privacy[$key][$id].";\n";
			else $person_privacy_text .= "\$user_privacy['$key']['$id'] = ".$PRIVACY_CONSTANTS[$setting].";\n";
		}
	}
	if ($v_new_user_privacy_username && $v_new_user_privacy_access_ID && $v_new_user_privacy_access_option) {
		$gedobj = new GedcomRecord(find_gedcom_record($v_new_user_privacy_access_ID));
		$v_new_user_privacy_access_ID = $gedobj->getXref();
		if ($v_new_user_privacy_access_ID) $person_privacy_text .= "\$user_privacy['$v_new_user_privacy_username']['$v_new_user_privacy_access_ID'] = ".$v_new_user_privacy_access_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;

	//-- Update the "Global Facts Privacy" section
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start global facts privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end global facts privacy --//"));
	$person_privacy_text = "//-- start global facts privacy --//\n\$global_facts = array();\n";
	if (!isset($v_global_facts) || !is_array($v_global_facts)) $v_global_facts = array();
	foreach($global_facts as $tag=>$value) {
		foreach($value as $key=>$setting) {
			if (isset($v_global_facts_del[$tag][$key]) || ($tag==$v_new_global_facts_abbr && $key==$v_new_global_facts_choice)) continue;
			if (isset($v_global_facts[$tag][$key])) $person_privacy_text .= "\$global_facts['$tag']['$key'] = ".$v_global_facts[$tag][$key].";\n";
			else $person_privacy_text .= "\$global_facts['$tag']['$key'] = ".$PRIVACY_CONSTANTS[$setting].";\n";
		}
	}
	if ($v_new_global_facts_abbr && $v_new_global_facts_choice && $v_new_global_facts_access_option) {
		$person_privacy_text .= "\$global_facts['$v_new_global_facts_abbr']['$v_new_global_facts_choice'] = ".$v_new_global_facts_access_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;

	//-- Update the "Person Facts Privacy" section
	$configtext_beg = substr($configtext, 0, strpos($configtext, "//-- start person facts privacy --//"));
	$configtext_end = substr($configtext, strpos($configtext, "//-- end person facts privacy --//"));
	$person_privacy_text = "//-- start person facts privacy --//\n\$person_facts = array();\n";
	if (!isset($v_person_facts) || !is_array($v_person_facts)) $v_person_facts = array();
	foreach($person_facts as $id=>$value) {
		foreach($value as $tag=>$value1) {
			foreach($value1 as $key=>$setting) {
				if (isset($v_person_facts_del[$id][$tag][$key]) || ($id==$v_new_person_facts_access_ID && $tag==$v_new_person_facts_abbr && $key==$v_new_person_facts_choice)) continue;
				if (isset($v_person_facts[$id][$tag][$key])) $person_privacy_text .= "\$person_facts['$id']['$tag']['$key'] = ".$v_person_facts[$id][$tag][$key].";\n";
				else $person_privacy_text .= "\$person_facts['$id']['$tag']['$key'] = ".$PRIVACY_CONSTANTS[$setting].";\n";
			}
		}
	}
	if ($v_new_person_facts_access_ID && $v_new_person_facts_abbr && $v_new_global_facts_choice && $v_new_global_facts_access_option) {
		$gedobj = new GedcomRecord(find_gedcom_record($v_new_person_facts_access_ID));
		$v_new_person_facts_access_ID = $gedobj->getXref();
		if ($v_new_person_facts_access_ID) $person_privacy_text .= "\$person_facts['$v_new_person_facts_access_ID']['$v_new_person_facts_abbr']['$v_new_person_facts_choice'] = ".$v_new_person_facts_access_option.";\n";
	}
	$configtext = $configtext_beg . $person_privacy_text . $configtext_end;

	$PRIVACY_MODULE = $INDEX_DIRECTORY.$GEDCOM."_priv.php";
	$fp = @fopen($PRIVACY_MODULE, "wb");
	if (!$fp) {
		global $whichFile;
		$whichFile = $PRIVACY_MODULE;
		print "<span class=\"error\">".print_text("gedcom_config_write_error",0,1)."<br /></span>\n";
	} else {
		fwrite($fp, $configtext);
		fclose($fp);
	}
	// NOTE: load the new variables
	include $INDEX_DIRECTORY.$GEDCOM."_priv.php";
	$logline = AddToLog("Privacy file $PRIVACY_MODULE updated");
 	$gedcomprivname = $GEDCOM."_priv.php";
 	check_in($logline, $gedcomprivname, $INDEX_DIRECTORY);

 	//-- delete the cache files for the welcome page blocks
	include_once 'includes/index_cache.php';
	clearCache();
}
?>
<script language="JavaScript" type="text/javascript">
<!--
		var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'editconfig_help.php?help='+which;
		return false;
	}
//-->
</script>

<form name="editprivacyform" method="post" action="edit_privacy.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="ged" value="<?php print $GEDCOM;?>" />

	<!-- NOTE: General Privacy Settings header bar -->
	<table class="facts_table">
		<tr>
			<td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
				<?php
				print "<a href=\"javascript: ".$pgv_lang["general_privacy"]."\" onclick=\"expand_layer('general-privacy-options');return false\"><img id=\"general-privacy-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a> ";
				print_help_link("general_privacy_help", "qm", "general_privacy");
				?>
				<a href="javascript: <?php print $pgv_lang["general_privacy"]; ?>" onclick="expand_layer('general-privacy-options');return false"><b><?php print $pgv_lang["general_privacy"]; ?></b></a>
			</td>
		</tr>
	</table>

	<!-- NOTE: General Privacy Settings options -->
	<div id="general-privacy-options" style="display: block">
		<table class="facts_table">
			<tr>
				<td class="descriptionbox wrap width20 <?php print $TEXT_DIRECTION; ?>">
					<?php print_help_link("SHOW_DEAD_PEOPLE_help", "qm", "SHOW_DEAD_PEOPLE"); print $pgv_lang["SHOW_DEAD_PEOPLE"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_SHOW_DEAD_PEOPLE"><?php write_access_option($SHOW_DEAD_PEOPLE); ?></select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("SHOW_LIVING_NAMES_help", "qm", "SHOW_LIVING_NAMES"); print $pgv_lang["SHOW_LIVING_NAMES"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_SHOW_LIVING_NAMES"><?php write_access_option($SHOW_LIVING_NAMES); ?></select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("SHOW_SOURCES_help", "qm", "SHOW_SOURCES"); print $pgv_lang["SHOW_SOURCES"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_SHOW_SOURCES"><?php write_access_option($SHOW_SOURCES); ?></select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("ENABLE_CLIPPINGS_CART_help", "qm", "ENABLE_CLIPPINGS_CART"); print $pgv_lang["ENABLE_CLIPPINGS_CART"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_ENABLE_CLIPPINGS_CART"><?php write_access_option($ENABLE_CLIPPINGS_CART); ?></select>
				</td>
			</tr>

			<?php if (file_exists("modules/research_assistant.php")) { ?>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("SHOW_RESEARCH_ASSISTANT_help", "qm", "SHOW_RESEARCH_ASSISTANT"); print $pgv_lang["SHOW_RESEARCH_ASSISTANT"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_SHOW_RESEARCH_ASSISTANT"><?php write_access_option($SHOW_RESEARCH_ASSISTANT); ?></select>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("SHOW_MULTISITE_SEARCH_help", "qm", "SHOW_MULTISITE_SEARCH"); print $pgv_lang["SHOW_MULTISITE_SEARCH"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_SHOW_MULTISITE_SEARCH"><?php write_access_option($SHOW_MULTISITE_SEARCH); ?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("PRIVACY_BY_YEAR_help", "qm", "PRIVACY_BY_YEAR"); print $pgv_lang["PRIVACY_BY_YEAR"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_PRIVACY_BY_YEAR"><?php write_yes_no($PRIVACY_BY_YEAR); ?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("PRIVACY_BY_RESN_help", "qm", "PRIVACY_BY_RESN"); print $pgv_lang["PRIVACY_BY_RESN"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_PRIVACY_BY_RESN"><?php write_yes_no($PRIVACY_BY_RESN); ?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("SHOW_PRIVATE_RELATIONSHIPS_help", "qm", "SHOW_PRIVATE_RELATIONSHIPS"); print $pgv_lang["SHOW_PRIVATE_RELATIONSHIPS"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_SHOW_PRIVATE_RELATIONSHIPS"><?php write_yes_no($SHOW_PRIVATE_RELATIONSHIPS); ?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("USE_RELATIONSHIP_PRIVACY_help", "qm", "USE_RELATIONSHIP_PRIVACY"); print $pgv_lang["USE_RELATIONSHIP_PRIVACY"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_USE_RELATIONSHIP_PRIVACY"><?php write_yes_no($USE_RELATIONSHIP_PRIVACY); ?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("MAX_RELATION_PATH_LENGTH_help", "qm", "MAX_RELATION_PATH_LENGTH"); print $pgv_lang["MAX_RELATION_PATH_LENGTH"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_MAX_RELATION_PATH_LENGTH"><?php
					for ($y = 1; $y <= 10; $y++) {
						print "<option";
						if ($MAX_RELATION_PATH_LENGTH == $y) print " selected=\"selected\"";
						print ">";
						print $y;
						print "</option>";
					}
					?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("CHECK_MARRIAGE_RELATIONS_help", "qm", "CHECK_MARRIAGE_RELATIONS"); print $pgv_lang["CHECK_MARRIAGE_RELATIONS"]; ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_CHECK_MARRIAGE_RELATIONS"><?php write_yes_no($CHECK_MARRIAGE_RELATIONS); ?></select>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("MAX_ALIVE_AGE_help", "qm", "MAX_ALIVE_AGE"); print $pgv_lang["MAX_ALIVE_AGE"]; ?>
				</td>
				<td class="optionbox">
					<input type="text" name="v_MAX_ALIVE_AGE" value="<?php print $MAX_ALIVE_AGE; ?>" size="5" />
				</td>
			</tr>
		</table>
	</div>

	<!-- -------------- person_privacy -----------------------------------

	NOTE: General Person Settings header bar -->
	<table class="facts_table">
		<tr>
			<td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
				<?php
				print "<a href=\"javascript: ".$pgv_lang["person_privacy"]."\" onclick=\"expand_layer('person-privacy-options');return false\"><img id=\"person-privacy-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a> "; ?><?php print_help_link("person_privacy_help", "qm", "person_privacy"); ?>
				<a href="javascript: <?php print $pgv_lang["person_privacy"]; ?>" onclick="expand_layer('person-privacy-options');return false"><b><?php print $pgv_lang["person_privacy"]; ?></b></a>
			</td>
		</tr>
	</table>

	<!-- NOTE: General Privacy Settings options -->
	<div id="person-privacy-options" style="display: none">
		<table class="facts_table">
			<tr>
				<td class="topbottombar" colspan="2">
					<b><?php print $pgv_lang["add_new_pp_setting"]; ?></b>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox">
					<?php print $pgv_lang["id"]; ?></td>
				<td class="descriptionbox"><?php print $pgv_lang["accessible_by"]; ?></td>
			</tr>

			<tr>
				<td class="optionbox width20">
					<input type="text" class="pedigree_form" name="v_new_person_privacy_access_ID" id="v_new_person_privacy_access_ID" size="4" />
					<?php
					print_findindi_link("v_new_person_privacy_access_ID","");
					print_findfamily_link("v_new_person_privacy_access_ID");
					print_findsource_link("v_new_person_privacy_access_ID");
					print_findrepository_link("v_new_person_privacy_access_ID");
					print_findmedia_link("v_new_person_privacy_access_ID", "1media");
					?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_new_person_privacy_access_option"><?php write_access_option(""); ?></select>
				</td>
			</tr>
		</table>

		<?php if (count($person_privacy) > 0) { ?>
		<table class="facts_table">
			<tr>
				<td class="topbottombar" colspan="4">
					<?php print $pgv_lang["edit_exist_person_privacy_settings"]; ?>
				</td>
			</tr>

			<tr>
				<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
				<td class="descriptionbox"><?php print $pgv_lang["id"]; ?></td>
				<td class="descriptionbox"><?php print $pgv_lang["full_name"]; ?></td>
				<td class="descriptionbox"><?php print $pgv_lang["accessible_by"]; ?></td>
			</tr>
			<?php foreach($person_privacy as $key=>$value) { ?>
			<tr>
				<td class="optionbox">
					<input type="checkbox" name="v_person_privacy_del[<?php print $key; ?>]" value="1" />
				</td>
				<td class="optionbox">
					<?php print $key; ?>
				</td>
				<td class="optionbox">
					<?php search_ID_details($key, 1); ?>
				</td>
				<td class="optionbox">
					<select size="1" name="v_person_privacy[<?php print $key; ?>]"><?php write_access_option($value); ?></select>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>
	</div>

	<!-- -------------- user_privacy -----------------------------------

	NOTE: User Privacy Settings header bar -->
	<table class="facts_table">
		<tr>
			<td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
				<?php print "<a href=\"javascript: ".$pgv_lang["user_privacy"]."\" onclick=\"expand_layer('user-privacy-options');return false\"><img id=\"user-privacy-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a> "; ?>
				<?php print_help_link("user_privacy_help", "qm", "user_privacy"); ?>
				<a href="javascript: <?php print $pgv_lang["user_privacy"]; ?>" onclick="expand_layer('user-privacy-options');return false"><b><?php print $pgv_lang["user_privacy"]; ?></b></a>
			</td>
		</tr>
	</table>

	<!-- NOTE: User Privacy Settings options -->
	<div id="user-privacy-options" style="display: none">
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="3">
				<b><?php print $pgv_lang["add_new_up_setting"]; ?></b>
			</td>
		</tr>

		<tr>
			<td class="descriptionbox"><?php print $pgv_lang["user_name"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["id"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["show_question"]; ?></td>
		</tr>

		<tr class="<?php print $TEXT_DIRECTION; ?>">
			<td class="optionbox width20">
				<select size="1" name="v_new_user_privacy_username">
				<?php
				foreach(get_all_users() as $user_id=>$user_name) {
					echo '<option value="', $user_id, '">';
					if ($TEXT_DIRECTION == 'ltr') {
						echo $user_id, ' (', getUserFullName($user_id), ')</option>';
					} else {
						echo getLRM(), '(', getUserFullName($user_id), ')', getLRM(), ' ', $user_id, '</option>';
					}

				}
				?>
				</select>
			</td>
			<td class="optionbox">
				<input type="text" class="pedigree_form" name="v_new_user_privacy_access_ID" id="v_new_user_privacy_access_ID" size="4" />
				<?php
				print_findindi_link("v_new_user_privacy_access_ID","");
				print_findfamily_link("v_new_user_privacy_access_ID");
				print_findsource_link("v_new_user_privacy_access_ID");
				print_findrepository_link("v_new_user_privacy_access_ID");
				print_findmedia_link("v_new_person_privacy_access_ID", "1media");
				?>
			</td>
			<td class="optionbox">
				<select size="1" name="v_new_user_privacy_access_option"><?php write_access_option(""); ?></select>
			</td>
		</tr>
	</table>
	<?php if (count($user_privacy) > 0) { ?>
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="5">
				<?php print $pgv_lang["edit_exist_user_privacy_settings"]; ?>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["user_name"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["id"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["full_name"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["show_question"]; ?></td>
		</tr>

		<?php
		foreach($user_privacy as $key=>$value) {
			foreach($value as $id=>$setting) {
		?>
		<tr class="<?php print $TEXT_DIRECTION; ?>">
			<td class="optionbox">
				<input type="checkbox" name="v_user_privacy_del[<?php print $key; ?>][<?php print $id; ?>]" value="1" />
			</td>
			<td class="optionbox">
				<?php echo $key, '<br />', getLRM(), '(', getUserFullName($key), ')', getLRM(); ?>
			</td>
			<td class="optionbox">
				<?php print $id; ?>
			</td>
			<td class="optionbox">
				<?php search_ID_details($id, 2); ?>
			</td>
			<td class="optionbox">
				<select size="1" name="v_user_privacy[<?php print $key; ?>][<?php print $id; ?>]"><?php write_access_option($setting); ?></select>
			</td>
		</tr>

		<?php } } ?>
	</table>
	<?php } ?>
	</div>
	<!-- -------------- global_facts -----------------------------------

	NOTE: Global Settings header bar -->
	<table class="facts_table">
		<tr>
			<td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
				<?php
				print "<a href=\"javascript: ".$pgv_lang["global_facts"]."\" onclick=\"expand_layer('global-facts-options');return false\"><img id=\"global-facts-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a> "; ?>
				<?php print_help_link("global_facts_help", "qm", "global_facts"); ?>
				<a href="javascript: <?php print $pgv_lang["global_facts"]; ?>" onclick="expand_layer('global-facts-options');return false"><b><?php print $pgv_lang["global_facts"]; ?></b></a>
			</td>
		</tr>
	</table>

	<!-- NOTE: Global Settings options -->
	<div id="global-facts-options" style="display: none">
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="3">
				<b><?php print $pgv_lang["add_new_gf_setting"]; ?></b></td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php print $pgv_lang["name_of_fact"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["choice"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["accessible_by"]; ?></td>
		</tr>
		<tr class="<?php print $TEXT_DIRECTION; ?>">
			<td class="optionbox">
				<select size="1" name="v_new_global_facts_abbr">
				<?php
				print "<option value=\"\">".$pgv_lang["choose"]."</option>";
				foreach($factarray as $tag=>$label) {
					print "<option";
					print " value=\"";
					print $tag;
					print "\">";
					print $tag . " - " . str_replace("<br />", " ", $label);
					print "</option>";
				}
				?>
				</select>
			</td>
			<td class="optionbox">
				<select size="1" name="v_new_global_facts_choice">
					<option value="details"><?php print $pgv_lang["fact_details"]; ?></option>
					<option value="show"><?php print $pgv_lang["fact_show"]; ?></option>
				</select>
			</td>
			<td class="optionbox">
				<select size="1" name="v_new_global_facts_access_option"><?php write_access_option(""); ?></select>
			</td>
		</tr>
	</table>
	<?php if (count($global_facts) > 0) { ?>
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="4">
				<b><?php print $pgv_lang["edit_exist_global_facts_settings"]; ?></b>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["name_of_fact"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["choice"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["accessible_by"]; ?></td>
		</tr>
		<?php
		foreach($global_facts as $tag=>$value) {
			foreach($value as $key=>$setting) {
		?>
		<tr class="<?php print $TEXT_DIRECTION; ?>">
			<td class="optionbox">
				<input type="checkbox" name="v_global_facts_del[<?php print $tag; ?>][<?php print $key; ?>]" value="1" />
			</td>
			<td class="optionbox">
				<?php
				if (isset($factarray[$tag])) print $factarray[$tag];
				else print $tag;
				?>
			</td>
			<td class="optionbox">
				<?php
				if ($key == "show") print $pgv_lang["fact_show"];
				if ($key == "details") print $pgv_lang["fact_details"];
				?>
			</td>
			<td class="optionbox">
				<select size="1" name="v_global_facts[<?php print $tag; ?>][<?php print $key; ?>]"><?php write_access_option($setting); ?></select>
			</td>
		</tr>
		<?php } } ?>
	</table>
	<?php } else print "&nbsp;"; ?>
	</div>
	<!-- -------------- person_facts -----------------------------------
	NOTE: Person Facts header bar -->
	<table class="facts_table">
		<tr>
			<td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
				<?php print "<a href=\"javascript: ".$pgv_lang["person_facts"]."\" onclick=\"expand_layer('person-facts-options');return false\"><img id=\"person-facts-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a> "; ?>
				<?php print_help_link("person_facts_help", "qm", "person_facts"); ?>
				<a href="javascript: <?php print $pgv_lang["person_facts"]; ?>" onclick="expand_layer('person-facts-options');return false"><b><?php print $pgv_lang["person_facts"]; ?></b></a>
			</td>
		</tr>
	</table>

	<!-- NOTE: Person Facts options -->
	<div id="person-facts-options" style="display: none">
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="4">
				<b><?php print $pgv_lang["add_new_pf_setting"]; ?></b>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php print $pgv_lang["id"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["name_of_fact"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["choice"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["accessible_by"]; ?></td>
		</tr>
		<tr class="<?php print $TEXT_DIRECTION; ?>">
			<td class="optionbox">
				<input type="text" class="pedigree_form" name="v_new_person_facts_access_ID" id="v_new_person_facts_access_ID" size="4" />
				<?php
				print_findindi_link("v_new_person_facts_access_ID","");
				print_findfamily_link("v_new_person_facts_access_ID");
				print_findsource_link("v_new_person_facts_access_ID");
				print_findrepository_link("v_new_person_facts_access_ID");
				?>
			</td>
			<td class="optionbox">
				<select size="1" name="v_new_person_facts_abbr">
				<?php
				foreach($factarray as $tag=>$label) {
					print "<option";
					print " value=\"";
					print $tag;
					print "\">";
					print $tag . " - " . str_replace("<br />", " ", $label);
					print "</option>";
				}
				?>
				</select>
			</td>
			<td class="optionbox">
				<select size="1" name="v_new_person_facts_choice">
					<option value="details"><?php print $pgv_lang["fact_details"]; ?></option>
					<option value="show"><?php print $pgv_lang["fact_show"]; ?></option>
				</select>
			</td>
			<td class="optionbox">
				<select size="1" name="v_new_person_facts_access_option"><?php write_access_option(""); ?></select>
			</td>
		</tr>
	</table>
	<?php if (count($person_facts) > 0) { ?>
	<table class="facts_table">
		<tr>
			<td class="topbottombar" colspan="6"><b><?php print $pgv_lang["edit_exist_person_facts_settings"]; ?></b></td>
		</tr>
		<tr>
			<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["id"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["full_name"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["name_of_fact"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["choice"]; ?></td>
			<td class="descriptionbox"><?php print $pgv_lang["accessible_by"]; ?></td>
		</tr>
		<?php
		foreach($person_facts as $id=>$value) {
				foreach($value as $tag=>$value1) {
					foreach($value1 as $key=>$setting) {
		?>
		<tr class="<?php print $TEXT_DIRECTION; ?>">
			<td class="optionbox">
				<input type="checkbox" name="v_person_facts_del[<?php print $id; ?>][<?php print $tag; ?>][<?php print $key; ?>]" value="1" />
			</td>
			<td class="optionbox">
				<?php print $id; ?>
			</td>
			<td class="optionbox">
				<?php search_ID_details($id, 2); ?>
			</td>
			<td class="optionbox">
				<?php print $tag. " - ".$factarray[$tag]; ?>
			</td>
			<td class="optionbox">
				<?php
				if ($key == "show") print $pgv_lang["fact_show"];
				if ($key == "details") print $pgv_lang["fact_details"];
				?>
			</td>
			<td class="optionbox">
				<select size="1" name="v_person_facts[<?php print $id; ?>][<?php print $tag; ?>][<?php print $key; ?>]"><?php write_access_option($setting); ?></select>
			</td>
		</tr>
		<?php } } } ?>
	</table>
	<?php } ?>
	</div>
	<table class="facts_table" border="0">
		<tr>
			<td class="topbottombar">
				<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
				&nbsp;&nbsp;
				<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
			</td>
		</tr>
	</table>
</form>
<?php
print_footer();

?>

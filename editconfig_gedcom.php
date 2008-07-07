<?php
/**
 * UI for online updating of the GEDCOM config file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008 PGV Development Team, all rights reserved.
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
 * @subpackage Admin
 * @version $Id: editconfig_gedcom.php,v 1.10 2008/07/07 18:01:11 lsces Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require "includes/bitsession.php";
if (empty($action)) $action = "";
if (isset($_REQUEST['source'])) $source = $_REQUEST['source'];
if (empty($source)) $source="";		// Set when loaded from uploadgedcom.php
if (!PGV_USER_GEDCOM_ADMIN) {
	header("Location: editgedcoms.php");
	exit;
}

/**
 * find the name of the first GEDCOM file in a zipfile
 * @param string $zipfile	the path and filename
 * @param boolean $extract  true = extract and return filename, false = return filename
 * @return string		the path and filename of the gedcom file
 */
function GetGEDFromZIP($zipfile, $extract=true) {
	GLOBAL $INDEX_DIRECTORY;

	require_once "includes/pclzip.lib.php";
	$zip = new PclZip($zipfile);
	// if it's not a valid zip, just return the filename
	if (($list = $zip->listContent()) == 0) {
		return $zipfile;
	}

	// Determine the extract directory
	$slpos = strrpos($zipfile, "/");
	if (!$slpos) $slpos = strrpos($zipfile,"\\");
	if ($slpos) $path = substr($zipfile, 0, $slpos+1);
	else $path = $INDEX_DIRECTORY;
	// Scan the files and return the first .ged found
	foreach ($list as $key=>$listitem) {
		if (($listitem["status"]="ok") && (strstr(strtolower($listitem["filename"]), ".")==".ged")) {
			$filename = basename($listitem["filename"]);
			if ($extract == false) return $filename;

			// if the gedcom exists, save the old one. NOT to bak as it will be overwritten on import
			if (file_exists($path.$filename)) {
				if (file_exists($path.$filename.".old")) unlink($path.$filename.".old");
				copy($path.$filename, $path.$filename.".old");
				unlink($path.$filename);
			}
			if ($zip->extract(PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_PATH, $path, PCLZIP_OPT_BY_NAME, $listitem["filename"]) == 0) {
				print "ERROR cannot extract ZIP";
			}
			return $filename;
		}
	}
	return $zipfile;
}


/**
 * print write_access option
 *
 * @param string $checkVar
 */
function write_access_option($checkVar) {
  global $PRIV_HIDE, $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE;
  global $pgv_lang;

  print "<option value=\"\$PRIV_PUBLIC\"";
  if ($checkVar==$PRIV_PUBLIC) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_PUBLIC"]."</option>\n";
  print "<option value=\"\$PRIV_USER\"";
  if ($checkVar==$PRIV_USER) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_USER"]."</option>\n";
  print "<option value=\"\$PRIV_NONE\"";
  if ($checkVar==$PRIV_NONE) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_NONE"]."</option>\n";
  print "<option value=\"\$PRIV_HIDE\"";
  if ($checkVar==$PRIV_HIDE) print " selected=\"selected\"";
  print ">".$pgv_lang["PRIV_HIDE"]."</option>\n";
}


loadLangFile("pgv_confighelp, pgv_help");

if (!isset($_POST)) $_POST = $HTTP_POST_VARS;

// Remove slashes
if (isset($_POST["NEW_COMMON_NAMES_ADD"])) $_POST["NEW_COMMON_NAMES_ADD"] = stripslashes($_POST["NEW_COMMON_NAMES_ADD"]);
if (isset($_POST["NEW_COMMON_NAMES_REMOVE"])) $_POST["NEW_COMMON_NAMES_REMOVE"] = stripslashes($_POST["NEW_COMMON_NAMES_REMOVE"]);
if (isset($_REQUEST['path'])) $path = $_REQUEST['path'];
if (isset($_REQUEST['oldged'])) $oldged = $_REQUEST['oldged'];
if (isset($_REQUEST['GEDFILENAME'])) $GEDFILENAME = $_REQUEST['GEDFILENAME'];
if (isset($_REQUEST['GEDCOMPATH'])) $GEDCOMPATH = $_REQUEST['GEDCOMPATH'];
if (isset($_REQUEST['ged'])) $ged = $_REQUEST['ged'];
if (isset($_REQUEST['gedcom_title'])) $gedcom_title = $_REQUEST['gedcom_title'];
if (isset($_REQUEST['THEME_DIR'])) $THEME_DIR = $_REQUEST['THEME_DIR'];

if (empty($oldged)) $oldged = "";
else $ged = $oldged;
if (!isset($path)) $path = "";
if (!isset($GEDFILENAME)) $GEDFILENAME = "";

if (isset($GEDCOMPATH)) {
	$ctupload = count($_FILES);
	if ($ctupload > 0) {
		// NOTE: Extract the GEDCOM filename
		if (!empty($path)) $GEDFILENAME = basename($path);
		else $GEDFILENAME = $_FILES['GEDCOMPATH']['name'];
		if ($path=="" || dirname($path) == ".") $upload_path = $INDEX_DIRECTORY;
		else $upload_path = dirname($path)."/";
		if (empty($GEDFILENAME)) $GEDFILENAME = $_FILES['GEDCOMPATH']['name'];

		//-- remove any funny characters from uploaded files
		$GEDFILENAME = preg_replace('/[\+\&\%\$@]/', "_", $GEDFILENAME);

		// NOTE: When uploading a file check if it doesn't exist yet
		if ($action=="replace" || (!isset($gGedcom[$GEDFILENAME]) && !file_exists($upload_path.$GEDFILENAME))) {
			if (move_uploaded_file($_FILES['GEDCOMPATH']['tmp_name'], $upload_path.$GEDFILENAME)) {
				AddToLog("Gedcom ".$path.$GEDFILENAME." uploaded");
				$GEDCOMPATH = $upload_path.$GEDFILENAME;
			}
			else {
				$upload_errors = array(
					UPLOAD_ERR_OK        =>print_text("file_success",0,1),
					UPLOAD_ERR_INI_SIZE  =>print_text("file_too_big",0,1),
					UPLOAD_ERR_FORM_SIZE =>print_text("file_too_big",0,1),
					UPLOAD_ERR_PARTIAL   =>print_text("file_partial",0,1),
					UPLOAD_ERR_NO_FILE   =>print_text("file_missing",0,1),
					UPLOAD_ERR_NO_TMP_DIR=>"Missing PHP temporary directory",
					UPLOAD_ERR_CANT_WRITE=>"PHP failed to write to disk",
					UPLOAD_ERR_EXTENSION =>"PHP blocked file by extension"
				);
				$error = print_text("upload_error",0,1)."<br />".$upload_errors[$_FILES['GEDCOMPATH']['error']];
				$action = "upload_form";
			}
		}
		// NOTE: If the file exists we will make a backup file
		else {
			//print "file exists, create backup";
			if (move_uploaded_file($_FILES['GEDCOMPATH']['tmp_name'], $upload_path.$GEDFILENAME.".bak")) {
				$bakfile = $upload_path.$GEDFILENAME.".bak";
				$GEDCOMPATH = $upload_path.$GEDFILENAME;
				//print $bakfile." ".$GEDCOMPATH;
			}
			else {
				$upload_errors = array(print_text("file_success",0,1), print_text("file_too_big",0,1), print_text("file_too_big",0,1),print_text("file_partial",0,1), print_text("file_missing",0,1));
				$error = print_text("upload_error",0,1)."<br />".$upload_errors[$_FILES['GEDCOMPATH']['error']];
				$action = "upload_form";
			}
		}
	}
	//-- check if there was an error during the upload
	if (empty($error)) {
		// NOTE: Extract the GEDCOM filename
		if (!empty($path)) $GEDFILENAME = basename($path);
		else $GEDFILENAME = basename($GEDCOMPATH);
		// NOTE: Check if the input contains a valid path otherwise check if there is one in the GEDCOMPATH
		if (!is_dir($path)) {
			if (!empty($path)) $parts = preg_split("/[\/\\\]/", $path);
			else $parts = preg_split("/[\/\\\]/", $GEDCOMPATH);
			$path = "";
			$ctparts = count($parts)-1;
			if (count($parts) == 1) $path = $INDEX_DIRECTORY;
			else {
				foreach ($parts as $key => $pathpart) {
					if ($key < $ctparts) $path .= $pathpart."/";
				}
			}
		}
		// NOTE: Check if it is a zipfile
		if (strstr(strtolower(trim($GEDFILENAME)), ".zip")==".zip") $GEDFILENAME = GetGEDFromZIP($path.$GEDFILENAME);
		// NOTE: Check if there is an extension
		//-- don't force the .ged extension
		//-- if (strtolower(substr(trim($GEDFILENAME), -4)) != ".ged") $GEDFILENAME .= ".ged";

		$ged = $GEDFILENAME;
	}
	else {
		$action = "";
	}
}
if (isset($ged)) {
	if (isset($gGedcom[$ged])) {
		$GEDCOMPATH = $gGedcom[$ged]["path"];
		$path = "";
		$parts = preg_split("/[\/\\\]/", $GEDCOMPATH);
		$ctparts = count($parts)-1;
		if (count($parts) == 1) $path = $INDEX_DIRECTORY;
		else {
			foreach ($parts as $key => $pathpart) {
				if ($key < $ctparts) $path .= $pathpart."/";
			}
		}
		$GEDFILENAME = $ged;
		if (!isset($gedcom_title)) $gedcom_title = $gGedcom[$ged]["title"];
		$gedcom_config = $gGedcom[$ged]["config"];
		$gedcom_privacy = $gGedcom[$ged]["privacy"];
		$gedcom_id = $gGedcom[$ged]["id"];
		$FILE = $ged;
		$oldged = $ged;
		$pgv_ver=$gGedcom[$ged]["pgv_ver"];
	}
	else {
		if (empty($_POST["GEDCOMPATH"])) {
			$GEDCOMPATH = "";
			$gedcom_title = "";
		}
		$gedcom_config = "config_gedcom.php";
		$gedcom_privacy = "privacy.php";
		$gedcom_id = "";
		$pgv_ver=$VERSION;
	}
}
else {
	$GEDCOMPATH = "";
	$gedcom_title = "";
	$gedcom_config = "config_gedcom.php";
	$gedcom_privacy = "privacy.php";
	$gedcom_id = "";
	$path = "";
	$GEDFILENAME = "";
	$pgv_ver=$VERSION;
}
$USERLANG = $LANGUAGE;
$temp = $THEME_DIR;
require($gedcom_config);
if (!isset($_POST["GEDCOMLANG"])) $GEDCOMLANG = $LANGUAGE;
$LANGUAGE = $USERLANG;
$error_msg = "";

if (!file_exists($path.$GEDFILENAME) && $source != "add_new_form") $action="add";
if ($action=="update") {
	$errors = false;
	if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
	$FILE=$GEDFILENAME;
	$newgedcom=false;
	$gedcom_config="config_gedcom.php";
	if (copy($gedcom_config, $INDEX_DIRECTORY.$FILE."_conf.php")) {
		$gedcom_config = "\${INDEX_DIRECTORY}".$FILE."_conf.php";
	}
	if (!file_exists($INDEX_DIRECTORY.$FILE."_priv.php")) {
		if (copy($gedcom_privacy, $INDEX_DIRECTORY.$FILE."_priv.php")) {
			$gedcom_privacy = "\${INDEX_DIRECTORY}".$FILE."_priv.php";
		}
	}
	else $gedcom_privacy = "\${INDEX_DIRECTORY}".$FILE."_priv.php";

	$gedarray = array();
	$gedarray["gedcom"] = $FILE;
	$gedarray["config"] = $gedcom_config;
	$gedarray["privacy"] = $gedcom_privacy;
	if (!empty($gedcom_title)) $gedarray["title"] = $gedcom_title;
	else if (!empty($_POST["gedcom_title"])) $gedarray["title"] = $_POST["gedcom_title"];
	else $gedarray["title"] = str_replace("#GEDCOMFILE#", $GEDFILENAME, $pgv_lang["new_gedcom_title"]);
	$gedarray["title"] = stripLRMRLM($gedarray["title"]);
	$gedarray["path"] = $path.$GEDFILENAME;
	$gedarray["id"] = $gedcom_id;
	$gedarray["pgv_ver"] = $pgv_ver;

	// Check that add/remove common surnames are separated by [,;] blank
	$_POST["NEW_COMMON_NAMES_REMOVE"] = preg_replace("/[,;]\b/", ", ", $_POST["NEW_COMMON_NAMES_REMOVE"]);
	$_POST["NEW_COMMON_NAMES_ADD"] = preg_replace("/[,;]\b/", ", ", $_POST["NEW_COMMON_NAMES_ADD"]);
	$COMMON_NAMES_THRESHOLD = $_POST["NEW_COMMON_NAMES_THRESHOLD"];
	$COMMON_NAMES_ADD = $_POST["NEW_COMMON_NAMES_ADD"];
	$COMMON_NAMES_REMOVE = $_POST["NEW_COMMON_NAMES_REMOVE"];
	$gedarray["commonsurnames"] = "";
	$gGedcom[$FILE] = $gedarray;
	store_gedcoms();

	require($INDEX_DIRECTORY."gedcoms.php");
	$boolarray = array();
	$boolarray["yes"]="true";
	$boolarray["no"]="false";
	$boolarray[false]="false";
	$boolarray[true]="true";
	$configtext = implode('', file("config_gedcom.php"));

	$_POST["NEW_MEDIA_DIRECTORY"] = preg_replace('/\\\/','/',$_POST["NEW_MEDIA_DIRECTORY"]);
	$ct = preg_match("'/$'", $_POST["NEW_MEDIA_DIRECTORY"]);
	if ($ct==0) $_POST["NEW_MEDIA_DIRECTORY"] .= "/";
	if (substr($_POST["NEW_MEDIA_DIRECTORY"],0,2)=="./") $_POST["NEW_MEDIA_DIRECTORY"] = substr($_POST["NEW_MEDIA_DIRECTORY"],2);
	if (preg_match("/.*[a-zA-Z]{1}:.*/",$_POST["NEW_MEDIA_DIRECTORY"])>0) $errors = true;
	if (!isFileExternal($_POST["NEW_HOME_SITE_URL"])) $_POST["NEW_HOME_SITE_URL"] = "http://".$_POST["NEW_HOME_SITE_URL"];
	$_POST["NEW_PEDIGREE_ROOT_ID"] = trim($_POST["NEW_PEDIGREE_ROOT_ID"]);
	if ($_POST["NEW_DAYS_TO_SHOW_LIMIT"] < 1) $_POST["NEW_DAYS_TO_SHOW_LIMIT"] = 1;
	if ($_POST["NEW_DAYS_TO_SHOW_LIMIT"] > 30) $_POST["NEW_DAYS_TO_SHOW_LIMIT"] = 30;

	$configtext = preg_replace('/\$ABBREVIATE_CHART_LABELS\s*=\s*.*;/', "\$ABBREVIATE_CHART_LABELS = ".$boolarray[$_POST["NEW_ABBREVIATE_CHART_LABELS"]].";", $configtext);
	$configtext = preg_replace('/\$ADVANCED_NAME_FACTS\s*=\s*.*;/', "\$ADVANCED_NAME_FACTS = \"".$_POST["NEW_ADVANCED_NAME_FACTS"]."\";", $configtext);
	$configtext = preg_replace('/\$ADVANCED_PLAC_FACTS\s*=\s*.*;/', "\$ADVANCED_PLAC_FACTS = \"".$_POST["NEW_ADVANCED_PLAC_FACTS"]."\";", $configtext);
	$configtext = preg_replace('/\$ALLOW_EDIT_GEDCOM\s*=\s*.*;/', "\$ALLOW_EDIT_GEDCOM = ".$boolarray[$_POST["NEW_ALLOW_EDIT_GEDCOM"]].";", $configtext);
	$configtext = preg_replace('/\$ALLOW_THEME_DROPDOWN\s*=\s*.*;/', "\$ALLOW_THEME_DROPDOWN = ".$boolarray[$_POST["NEW_ALLOW_THEME_DROPDOWN"]].";", $configtext);
	$configtext = preg_replace('/\$AUTO_GENERATE_THUMBS\s*=\s*.*;/', "\$AUTO_GENERATE_THUMBS = ".$boolarray[$_POST["NEW_AUTO_GENERATE_THUMBS"]].";", $configtext);
	$configtext = preg_replace('/\$CALENDAR_FORMAT\s*=\s*".*";/', "\$CALENDAR_FORMAT = \"".$_POST["NEW_CALENDAR_FORMAT"]."\";", $configtext);
	$configtext = preg_replace('/\$CHANGELOG_CREATE\s*=\s*".*";/', "\$CHANGELOG_CREATE = \"".$_POST["NEW_CHANGELOG_CREATE"]."\";", $configtext);
	$configtext = preg_replace('/\$CHARACTER_SET\s*=\s*".*";/', "\$CHARACTER_SET = \"".$_POST["NEW_CHARACTER_SET"]."\";", $configtext);
	$configtext = preg_replace('/\$CHART_BOX_TAGS\s*=\s*".*";/', "\$CHART_BOX_TAGS = \"".$_POST["NEW_CHART_BOX_TAGS"]."\";", $configtext);
	$configtext = preg_replace('/\$CHECK_CHILD_DATES\s*=\s*.*;/', "\$CHECK_CHILD_DATES = ".$boolarray[$_POST["NEW_CHECK_CHILD_DATES"]].";", $configtext);
	$configtext = preg_replace('/\$COMMON_NAMES_ADD\s*=\s*".*";/', "\$COMMON_NAMES_ADD = \"".$_POST["NEW_COMMON_NAMES_ADD"]."\";", $configtext);
	$configtext = preg_replace('/\$COMMON_NAMES_REMOVE\s*=\s*".*";/', "\$COMMON_NAMES_REMOVE = \"".$_POST["NEW_COMMON_NAMES_REMOVE"]."\";", $configtext);
	$configtext = preg_replace('/\$COMMON_NAMES_THRESHOLD\s*=\s*".*";/', "\$COMMON_NAMES_THRESHOLD = \"".$_POST["NEW_COMMON_NAMES_THRESHOLD"]."\";", $configtext);
	$configtext = preg_replace('/\$CONTACT_EMAIL\s*=\s*".*";/', "\$CONTACT_EMAIL = \"".$_POST["NEW_CONTACT_EMAIL"]."\";", $configtext);
	$configtext = preg_replace('/\$CONTACT_METHOD\s*=\s*".*";/', "\$CONTACT_METHOD = \"".$_POST["NEW_CONTACT_METHOD"]."\";", $configtext);
	$configtext = preg_replace('/\$DAYS_TO_SHOW_LIMIT\s*=\s*".*";/', "\$DAYS_TO_SHOW_LIMIT = \"".$_POST["NEW_DAYS_TO_SHOW_LIMIT"]."\";", $configtext);
	$configtext = preg_replace('/\$DEFAULT_PEDIGREE_GENERATIONS\s*=\s*".*";/', "\$DEFAULT_PEDIGREE_GENERATIONS = \"".$_POST["NEW_DEFAULT_PEDIGREE_GENERATIONS"]."\";", $configtext);
	$configtext = preg_replace('/\$DISPLAY_JEWISH_GERESHAYIM\s*=\s*.*;/', "\$DISPLAY_JEWISH_GERESHAYIM = ".$boolarray[$_POST["NEW_DISPLAY_JEWISH_GERESHAYIM"]].";", $configtext);
	$configtext = preg_replace('/\$DISPLAY_JEWISH_THOUSANDS\s*=\s*.*;/', "\$DISPLAY_JEWISH_THOUSANDS = ".$boolarray[$_POST["NEW_DISPLAY_JEWISH_THOUSANDS"]].";", $configtext);
	$configtext = preg_replace('/\$EDIT_AUTOCLOSE\s*=\s*.*;/', "\$EDIT_AUTOCLOSE = ".$boolarray[$_POST["NEW_EDIT_AUTOCLOSE"]].";", $configtext);
	$configtext = preg_replace('/\$ENABLE_MULTI_LANGUAGE\s*=\s*.*;/', "\$ENABLE_MULTI_LANGUAGE = ".$boolarray[$_POST["NEW_ENABLE_MULTI_LANGUAGE"]].";", $configtext);
	$configtext = preg_replace('/\$ENABLE_RSS\s*=\s*.*;/', "\$ENABLE_RSS = ".$boolarray[$_POST["NEW_ENABLE_RSS"]].";", $configtext);
	$configtext = preg_replace('/\$EXPAND_NOTES\s*=\s*.*;/', "\$EXPAND_NOTES = ".$boolarray[$_POST["NEW_EXPAND_NOTES"]].";", $configtext);
	$configtext = preg_replace('/\$EXPAND_RELATIVES_EVENTS\s*=\s*.*;/', "\$EXPAND_RELATIVES_EVENTS = ".$boolarray[$_POST["NEW_EXPAND_RELATIVES_EVENTS"]].";", $configtext);
	$configtext = preg_replace('/\$EXPAND_SOURCES\s*=\s*.*;/', "\$EXPAND_SOURCES = ".$boolarray[$_POST["NEW_EXPAND_SOURCES"]].";", $configtext);
	$configtext = preg_replace('/\$FAM_FACTS_ADD\s*=\s*".*";/', "\$FAM_FACTS_ADD = \"".$_POST["NEW_FAM_FACTS_ADD"]."\";", $configtext);
	$configtext = preg_replace('/\$FAM_FACTS_QUICK\s*=\s*".*";/', "\$FAM_FACTS_QUICK = \"".$_POST["NEW_FAM_FACTS_QUICK"]."\";", $configtext);
	$configtext = preg_replace('/\$FAM_FACTS_UNIQUE\s*=\s*".*";/', "\$FAM_FACTS_UNIQUE = \"".$_POST["NEW_FAM_FACTS_UNIQUE"]."\";", $configtext);
	$configtext = preg_replace('/\$FAM_ID_PREFIX\s*=\s*".*";/', "\$FAM_ID_PREFIX = \"".$_POST["NEW_FAM_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$FAVICON\s*=\s*".*";/', "\$FAVICON = \"".$_POST["NEW_FAVICON"]."\";", $configtext);
	$configtext = preg_replace('/\$GEDCOM_DEFAULT_TAB\s*=\s*".*";/', "\$GEDCOM_DEFAULT_TAB = \"".$_POST["NEW_GEDCOM_DEFAULT_TAB"]."\";", $configtext);
	$configtext = preg_replace('/\$GEDCOM_ID_PREFIX\s*=\s*".*";/', "\$GEDCOM_ID_PREFIX = \"".$_POST["NEW_GEDCOM_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$GENERATE_UIDS\s*=\s*.*;/', "\$GENERATE_UIDS = ".$boolarray[$_POST["NEW_GENERATE_UIDS"]].";", $configtext);
	$configtext = preg_replace('/\$HIDE_GEDCOM_ERRORS\s*=\s*.*;/', "\$HIDE_GEDCOM_ERRORS = ".$boolarray[$_POST["NEW_HIDE_GEDCOM_ERRORS"]].";", $configtext);
	$configtext = preg_replace('/\$HIDE_LIVE_PEOPLE\s*=\s*.*;/', "\$HIDE_LIVE_PEOPLE = ".$boolarray[$_POST["NEW_HIDE_LIVE_PEOPLE"]].";", $configtext);
	$configtext = preg_replace('/\$HOME_SITE_TEXT\s*=\s*".*";/', "\$HOME_SITE_TEXT = \"".$_POST["NEW_HOME_SITE_TEXT"]."\";", $configtext);
	$configtext = preg_replace('/\$HOME_SITE_URL\s*=\s*".*";/', "\$HOME_SITE_URL = \"".$_POST["NEW_HOME_SITE_URL"]."\";", $configtext);
	$configtext = preg_replace('/\$INDI_FACTS_ADD\s*=\s*".*";/', "\$INDI_FACTS_ADD = \"".$_POST["NEW_INDI_FACTS_ADD"]."\";", $configtext);
	$configtext = preg_replace('/\$INDI_FACTS_QUICK\s*=\s*".*";/', "\$INDI_FACTS_QUICK = \"".$_POST["NEW_INDI_FACTS_QUICK"]."\";", $configtext);
	$configtext = preg_replace('/\$INDI_FACTS_UNIQUE\s*=\s*".*";/', "\$INDI_FACTS_UNIQUE = \"".$_POST["NEW_INDI_FACTS_UNIQUE"]."\";", $configtext);
	$configtext = preg_replace('/\$JEWISH_ASHKENAZ_PRONUNCIATION\s*=\s*.*;/', "\$JEWISH_ASHKENAZ_PRONUNCIATION = ".$boolarray[$_POST["NEW_JEWISH_ASHKENAZ_PRONUNCIATION"]].";", $configtext);
	$configtext = preg_replace('/\$LANGUAGE\s*=\s*".*";/', "\$LANGUAGE = \"".$_POST["GEDCOMLANG"]."\";", $configtext);
	$configtext = preg_replace('/\$LINK_ICONS\s*=\s*\".*\";/', "\$LINK_ICONS = \"".$_POST["NEW_LINK_ICONS"]."\";", $configtext);
	$configtext = preg_replace('/\$MAX_DESCENDANCY_GENERATIONS\s*=\s*".*";/', "\$MAX_DESCENDANCY_GENERATIONS = \"".$_POST["NEW_MAX_DESCENDANCY_GENERATIONS"]."\";", $configtext);
	$configtext = preg_replace('/\$MAX_PEDIGREE_GENERATIONS\s*=\s*".*";/', "\$MAX_PEDIGREE_GENERATIONS = \"".$_POST["NEW_MAX_PEDIGREE_GENERATIONS"]."\";", $configtext);
	$configtext = preg_replace('/\$MEDIA_DIRECTORY\s*=\s*".*";/', "\$MEDIA_DIRECTORY = \"".$_POST["NEW_MEDIA_DIRECTORY"]."\";", $configtext);
	$configtext = preg_replace('/\$MEDIA_DIRECTORY_LEVELS\s*=\s*".*";/', "\$MEDIA_DIRECTORY_LEVELS = \"".$_POST["NEW_MEDIA_DIRECTORY_LEVELS"]."\";", $configtext);
	$configtext = preg_replace('/\$MEDIA_EXTERNAL\s*=\s*.*;/', "\$MEDIA_EXTERNAL = ".$boolarray[$_POST["NEW_MEDIA_EXTERNAL"]].";", $configtext);
	$configtext = preg_replace('/\$MEDIA_ID_PREFIX\s*=\s*".*";/', "\$MEDIA_ID_PREFIX = \"".$_POST["NEW_MEDIA_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$META_AUDIENCE\s*=\s*".*";/', "\$META_AUDIENCE = \"".$_POST["NEW_META_AUDIENCE"]."\";", $configtext);
	$configtext = preg_replace('/\$META_AUTHOR\s*=\s*".*";/', "\$META_AUTHOR = \"".$_POST["NEW_META_AUTHOR"]."\";", $configtext);
	$configtext = preg_replace('/\$META_COPYRIGHT\s*=\s*".*";/', "\$META_COPYRIGHT = \"".$_POST["NEW_META_COPYRIGHT"]."\";", $configtext);
	$configtext = preg_replace('/\$META_DESCRIPTION\s*=\s*".*";/', "\$META_DESCRIPTION = \"".$_POST["NEW_META_DESCRIPTION"]."\";", $configtext);
	$configtext = preg_replace('/\$META_KEYWORDS\s*=\s*".*";/', "\$META_KEYWORDS = \"".$_POST["NEW_META_KEYWORDS"]."\";", $configtext);
	$configtext = preg_replace('/\$META_PAGE_TOPIC\s*=\s*".*";/', "\$META_PAGE_TOPIC = \"".$_POST["NEW_META_PAGE_TOPIC"]."\";", $configtext);
	$configtext = preg_replace('/\$META_PAGE_TYPE\s*=\s*".*";/', "\$META_PAGE_TYPE = \"".$_POST["NEW_META_PAGE_TYPE"]."\";", $configtext);
	$configtext = preg_replace('/\$META_PUBLISHER\s*=\s*".*";/', "\$META_PUBLISHER = \"".$_POST["NEW_META_PUBLISHER"]."\";", $configtext);
	$configtext = preg_replace('/\$META_REVISIT\s*=\s*".*";/', "\$META_REVISIT = \"".$_POST["NEW_META_REVISIT"]."\";", $configtext);
	$configtext = preg_replace('/\$META_ROBOTS\s*=\s*".*";/', "\$META_ROBOTS = \"".$_POST["NEW_META_ROBOTS"]."\";", $configtext);
	$configtext = preg_replace('/\$META_SURNAME_KEYWORDS\s*=\s*.*;/', "\$META_SURNAME_KEYWORDS = ".$boolarray[$_POST["NEW_META_SURNAME_KEYWORDS"]].";", $configtext);
	$configtext = preg_replace('/\$META_TITLE\s*=\s*".*";/', "\$META_TITLE = \"".$_POST["NEW_META_TITLE"]."\";", $configtext);
	$configtext = preg_replace('/\$MULTI_MEDIA\s*=\s*.*;/', "\$MULTI_MEDIA = ".$boolarray[$_POST["NEW_MULTI_MEDIA"]].";", $configtext);
	$configtext = preg_replace('/\$NAME_FROM_GEDCOM\s*=\s*.*;/', "\$NAME_FROM_GEDCOM = ".$boolarray[$_POST["NEW_NAME_FROM_GEDCOM"]].";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_FULL_DETAILS\s*=\s*.*;/', "\$PEDIGREE_FULL_DETAILS = ".$boolarray[$_POST["NEW_PEDIGREE_FULL_DETAILS"]].";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_SHOW_GENDER\s*=\s*.*;/', "\$PEDIGREE_SHOW_GENDER = ".$boolarray[$_POST["NEW_PEDIGREE_SHOW_GENDER"]].";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_LAYOUT\s*=\s*.*;/', "\$PEDIGREE_LAYOUT = ".$boolarray[$_POST["NEW_PEDIGREE_LAYOUT"]].";", $configtext);
	$configtext = preg_replace('/\$PEDIGREE_ROOT_ID\s*=\s*".*";/', "\$PEDIGREE_ROOT_ID = \"".$_POST["NEW_PEDIGREE_ROOT_ID"]."\";", $configtext);
	$configtext = preg_replace('/\$POSTAL_CODE\s*=\s*.*;/', "\$POSTAL_CODE = ".$boolarray[$_POST["NEW_POSTAL_CODE"]].";", $configtext);
	$configtext = preg_replace('/\$QUICK_ADD_FACTS\s*=\s*".*";/', "\$QUICK_ADD_FACTS = \"".$_POST["NEW_QUICK_ADD_FACTS"]."\";", $configtext);
	$configtext = preg_replace('/\$QUICK_ADD_FAMFACTS\s*=\s*".*";/', "\$QUICK_ADD_FAMFACTS = \"".$_POST["NEW_QUICK_ADD_FAMFACTS"]."\";", $configtext);
	$configtext = preg_replace('/\$QUICK_REQUIRED_FACTS\s*=\s*".*";/', "\$QUICK_REQUIRED_FACTS = \"".$_POST["NEW_QUICK_REQUIRED_FACTS"]."\";", $configtext);
	$configtext = preg_replace('/\$QUICK_REQUIRED_FAMFACTS\s*=\s*".*";/', "\$QUICK_REQUIRED_FAMFACTS = \"".$_POST["NEW_QUICK_REQUIRED_FAMFACTS"]."\";", $configtext);
	$configtext = preg_replace('/\$REPO_FACTS_ADD\s*=\s*".*";/', "\$REPO_FACTS_ADD = \"".$_POST["NEW_REPO_FACTS_ADD"]."\";", $configtext);
	$configtext = preg_replace('/\$REPO_FACTS_QUICK\s*=\s*".*";/', "\$REPO_FACTS_QUICK = \"".$_POST["NEW_REPO_FACTS_QUICK"]."\";", $configtext);
	$configtext = preg_replace('/\$REPO_FACTS_UNIQUE\s*=\s*".*";/', "\$REPO_FACTS_UNIQUE = \"".$_POST["NEW_REPO_FACTS_UNIQUE"]."\";", $configtext);
	$configtext = preg_replace('/\$REPO_ID_PREFIX\s*=\s*".*";/', "\$REPO_ID_PREFIX = \"".$_POST["NEW_REPO_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$REQUIRE_AUTHENTICATION\s*=\s*.*;/', "\$REQUIRE_AUTHENTICATION = ".$boolarray[$_POST["NEW_REQUIRE_AUTHENTICATION"]].";", $configtext);
	$configtext = preg_replace('/\$RSS_FORMAT\s*=\s*".*";/', "\$RSS_FORMAT = \"".$_POST["NEW_RSS_FORMAT"]."\";", $configtext);
	$configtext = preg_replace('/\$SEARCHLOG_CREATE\s*=\s*".*";/', "\$SEARCHLOG_CREATE = \"".$_POST["NEW_SEARCHLOG_CREATE"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_AGE_DIFF\s*=\s*.*;/', "\$SHOW_AGE_DIFF = ".$boolarray[$_POST["NEW_SHOW_AGE_DIFF"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_CONTEXT_HELP\s*=\s*.*;/', "\$SHOW_CONTEXT_HELP = ".$boolarray[$_POST["NEW_SHOW_CONTEXT_HELP"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_COUNTER\s*=\s*.*;/', "\$SHOW_COUNTER = ".$boolarray[$_POST["NEW_SHOW_COUNTER"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_EMPTY_BOXES\s*=\s*.*;/', "\$SHOW_EMPTY_BOXES = ".$boolarray[$_POST["NEW_SHOW_EMPTY_BOXES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_FACT_ICONS\s*=\s*.*;/', "\$SHOW_FACT_ICONS = ".$boolarray[$_POST["NEW_SHOW_FACT_ICONS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_GEDCOM_RECORD\s*=\s*.*;/', "\$SHOW_GEDCOM_RECORD = ".$boolarray[$_POST["NEW_SHOW_GEDCOM_RECORD"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_HIGHLIGHT_IMAGES\s*=\s*.*;/', "\$SHOW_HIGHLIGHT_IMAGES = ".$boolarray[$_POST["NEW_SHOW_HIGHLIGHT_IMAGES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_ID_NUMBERS\s*=\s*.*;/', "\$SHOW_ID_NUMBERS = ".$boolarray[$_POST["NEW_SHOW_ID_NUMBERS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_LAST_CHANGE\s*=\s*.*;/', "\$SHOW_LAST_CHANGE = ".$boolarray[$_POST["NEW_SHOW_LAST_CHANGE"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_EST_LIST_DATES\s*=\s*.*;/', "\$SHOW_EST_LIST_DATES = ".$boolarray[$_POST["NEW_SHOW_EST_LIST_DATES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_LDS_AT_GLANCE\s*=\s*.*;/', "\$SHOW_LDS_AT_GLANCE = ".$boolarray[$_POST["NEW_SHOW_LDS_AT_GLANCE"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_LEVEL2_NOTES\s*=\s*.*;/', "\$SHOW_LEVEL2_NOTES = ".$boolarray[$_POST["NEW_SHOW_LEVEL2_NOTES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_MARRIED_NAMES\s*=\s*.*;/', "\$SHOW_MARRIED_NAMES = ".$boolarray[$_POST["NEW_SHOW_MARRIED_NAMES"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_MEDIA_DOWNLOAD\s*=\s*.*;/', "\$SHOW_MEDIA_DOWNLOAD = ".$boolarray[$_POST["NEW_SHOW_MEDIA_DOWNLOAD"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_MEDIA_FILENAME\s*=\s*.*;/', "\$SHOW_MEDIA_FILENAME = ".$boolarray[$_POST["NEW_SHOW_MEDIA_FILENAME"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_PARENTS_AGE\s*=\s*.*;/', "\$SHOW_PARENTS_AGE = ".$boolarray[$_POST["NEW_SHOW_PARENTS_AGE"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_PEDIGREE_PLACES\s*=\s*".*";/', "\$SHOW_PEDIGREE_PLACES = \"".$_POST["NEW_SHOW_PEDIGREE_PLACES"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_QUICK_RESN\s*=\s*.*;/', "\$SHOW_QUICK_RESN = ".$boolarray[$_POST["NEW_SHOW_QUICK_RESN"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_REGISTER_CAUTION\s*=\s*.*;/', "\$SHOW_REGISTER_CAUTION = ".$boolarray[$_POST["NEW_SHOW_REGISTER_CAUTION"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_RELATIVES_EVENTS\s*=\s*.*;/', "\$SHOW_RELATIVES_EVENTS = \"".$_POST["NEW_SHOW_RELATIVES_EVENTS"]."\";", $configtext);
	$configtext = preg_replace('/\$SHOW_SPIDER_TAGLINE\s*=\s*.*;/', "\$SHOW_SPIDER_TAGLINE = ".$boolarray[$_POST["NEW_SHOW_SPIDER_TAGLINE"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_STATS\s*=\s*.*;/', "\$SHOW_STATS = ".$boolarray[$_POST["NEW_SHOW_STATS"]].";", $configtext);
	$configtext = preg_replace('/\$SOUR_FACTS_ADD\s*=\s*".*";/', "\$SOUR_FACTS_ADD = \"".$_POST["NEW_SOUR_FACTS_ADD"]."\";", $configtext);
	$configtext = preg_replace('/\$SOUR_FACTS_QUICK\s*=\s*".*";/', "\$SOUR_FACTS_QUICK = \"".$_POST["NEW_SOUR_FACTS_QUICK"]."\";", $configtext);
	$configtext = preg_replace('/\$SOUR_FACTS_UNIQUE\s*=\s*".*";/', "\$SOUR_FACTS_UNIQUE = \"".$_POST["NEW_SOUR_FACTS_UNIQUE"]."\";", $configtext);
	$configtext = preg_replace('/\$SOURCE_ID_PREFIX\s*=\s*".*";/', "\$SOURCE_ID_PREFIX = \"".$_POST["NEW_SOURCE_ID_PREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$SPLIT_PLACES\s*=\s*.*;/', "\$SPLIT_PLACES = ".$boolarray[$_POST["NEW_SPLIT_PLACES"]].";", $configtext);
	$configtext = preg_replace('/\$SUBLIST_TRIGGER_I\s*=\s*.*;/', "\$SUBLIST_TRIGGER_I = \"".$_POST["NEW_SUBLIST_TRIGGER_I"]."\";", $configtext);
	$configtext = preg_replace('/\$SUBLIST_TRIGGER_F\s*=\s*.*;/', "\$SUBLIST_TRIGGER_F = \"".$_POST["NEW_SUBLIST_TRIGGER_F"]."\";", $configtext);
	$configtext = preg_replace('/\$SURNAME_LIST_STYLE\s*=\s*.*;/', "\$SURNAME_LIST_STYLE = \"".$_POST["NEW_SURNAME_LIST_STYLE"]."\";", $configtext);
	$configtext = preg_replace('/\$SUPPORT_METHOD\s*=\s*".*";/', "\$SUPPORT_METHOD = \"".$_POST["NEW_SUPPORT_METHOD"]."\";", $configtext);
	$configtext = preg_replace('/\$SURNAME_TRADITION\s*=\s*.*;/', "\$SURNAME_TRADITION = \"".$_POST["NEW_SURNAME_TRADITION"]."\";", $configtext);
	$configtext = preg_replace('/\$SYNC_GEDCOM_FILE\s*=\s*.*;/', "\$SYNC_GEDCOM_FILE = ".$boolarray[$_POST["NEW_SYNC_GEDCOM_FILE"]].";", $configtext);
	$configtext = preg_replace('/\$THUMBNAIL_WIDTH\s*=\s*".*";/', "\$THUMBNAIL_WIDTH = \"".$_POST["NEW_THUMBNAIL_WIDTH"]."\";", $configtext);
	$configtext = preg_replace('/\$UNDERLINE_NAME_QUOTES\s*=\s*.*;/', "\$UNDERLINE_NAME_QUOTES = ".$boolarray[$_POST["NEW_UNDERLINE_NAME_QUOTES"]].";", $configtext);
	$configtext = preg_replace('/\$USE_QUICK_UPDATE\s*=\s*.*;/', "\$USE_QUICK_UPDATE = ".$boolarray[$_POST["NEW_USE_QUICK_UPDATE"]].";", $configtext);
	$configtext = preg_replace('/\$USE_RIN\s*=\s*.*;/', "\$USE_RIN = ".$boolarray[$_POST["NEW_USE_RIN"]].";", $configtext);
	$configtext = preg_replace('/\$USE_RTL_FUNCTIONS\s*=\s*.*;/', "\$USE_RTL_FUNCTIONS = ".$boolarray[$_POST["NEW_USE_RTL_FUNCTIONS"]].";", $configtext);
	$configtext = preg_replace('/\$USE_THUMBS_MAIN\s*=\s*.*;/', "\$USE_THUMBS_MAIN = ".$boolarray[$_POST["NEW_USE_THUMBS_MAIN"]].";", $configtext);
	$configtext = preg_replace('/\$USE_MEDIA_VIEWER\s*=\s*.*;/', "\$USE_MEDIA_VIEWER = ".$boolarray[$_POST["NEW_USE_MEDIA_VIEWER"]].";", $configtext);
	$configtext = preg_replace('/\$USE_MEDIA_FIREWALL\s*=\s*.*;/', "\$USE_MEDIA_FIREWALL = ".$boolarray[$_POST["NEW_USE_MEDIA_FIREWALL"]].";", $configtext);
	$configtext = preg_replace('/\$MEDIA_FIREWALL_THUMBS\s*=\s*.*;/', "\$MEDIA_FIREWALL_THUMBS = ".$boolarray[$_POST["NEW_MEDIA_FIREWALL_THUMBS"]].";", $configtext);
	$configtext = preg_replace('/\$SHOW_NO_WATERMARK\s*=\s*.*;/', "\$SHOW_NO_WATERMARK = ".$_POST["NEW_SHOW_NO_WATERMARK"].";", $configtext);
	$configtext = preg_replace('/\$WATERMARK_THUMB\s*=\s*.*;/', "\$WATERMARK_THUMB = ".$boolarray[$_POST["NEW_WATERMARK_THUMB"]].";", $configtext);
	$configtext = preg_replace('/\$SAVE_WATERMARK_THUMB\s*=\s*.*;/', "\$SAVE_WATERMARK_THUMB = ".$boolarray[$_POST["NEW_SAVE_WATERMARK_THUMB"]].";", $configtext);
	$configtext = preg_replace('/\$SAVE_WATERMARK_IMAGE\s*=\s*.*;/', "\$SAVE_WATERMARK_IMAGE = ".$boolarray[$_POST["NEW_SAVE_WATERMARK_IMAGE"]].";", $configtext);
	$configtext = preg_replace('/\$PHPGEDVIEW_EMAIL\s*=\s*".*";/', "\$PHPGEDVIEW_EMAIL = \"".trim($_POST["NEW_PHPGEDVIEW_EMAIL"])."\";", $configtext);
	$configtext = preg_replace('/\$WEBMASTER_EMAIL\s*=\s*".*";/', "\$WEBMASTER_EMAIL = \"".$_POST["NEW_WEBMASTER_EMAIL"]."\";", $configtext);
	$configtext = preg_replace('/\$WELCOME_TEXT_AUTH_MODE\s*=\s*".*";/', "\$WELCOME_TEXT_AUTH_MODE = \"".$_POST["NEW_WELCOME_TEXT_AUTH_MODE"]."\";", $configtext);
	$configtext = preg_replace('/\$WELCOME_TEXT_AUTH_MODE_4\s*=\s*".*";/', "\$WELCOME_TEXT_AUTH_MODE_4 = \"".$_POST["NEW_WELCOME_TEXT_AUTH_MODE_4"]."\";", $configtext);// new
	$configtext = preg_replace('/\$WELCOME_TEXT_CUST_HEAD\s*=\s*.*;/', "\$WELCOME_TEXT_CUST_HEAD = ".$boolarray[$_POST["NEW_WELCOME_TEXT_CUST_HEAD"]].";", $configtext);
	$configtext = preg_replace('/\$WORD_WRAPPED_NOTES\s*=\s*.*;/', "\$WORD_WRAPPED_NOTES = ".$boolarray[$_POST["NEW_WORD_WRAPPED_NOTES"]].";", $configtext);
	$configtext = preg_replace('/\$ZOOM_BOXES\s*=\s*\".*\";/', "\$ZOOM_BOXES = \"".$_POST["NEW_ZOOM_BOXES"]."\";", $configtext);
	if (!$_POST["NEW_MEDIA_FIREWALL_ROOTDIR"]) {
		$NEW_MEDIA_FIREWALL_ROOTDIR = $INDEX_DIRECTORY;
	} else {
		$_POST["NEW_MEDIA_FIREWALL_ROOTDIR"] = trim($_POST["NEW_MEDIA_FIREWALL_ROOTDIR"]);
		if (substr ($_POST["NEW_MEDIA_FIREWALL_ROOTDIR"], -1) != "/") $_POST["NEW_MEDIA_FIREWALL_ROOTDIR"] = $_POST["NEW_MEDIA_FIREWALL_ROOTDIR"] . "/";
		$NEW_MEDIA_FIREWALL_ROOTDIR = $_POST["NEW_MEDIA_FIREWALL_ROOTDIR"];
	}
	if (!is_dir($NEW_MEDIA_FIREWALL_ROOTDIR)) {
		$errors = true;
		$error_msg .= "<span class=\"error\">".$pgv_lang["media_firewall_rootdir_no_exist"]."</span><br />\n";
	}
	if (!$errors) {
		// create the media directory 
		// if NEW_MEDIA_FIREWALL_ROOTDIR is the INDEX_DIRECTORY, PGV will have perms to create it
		// if PGV is unable to create the directory, tell the user to create it
		if (($NEW_USE_MEDIA_FIREWALL=='yes') || $USE_MEDIA_FIREWALL) {
			if (!is_dir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY)) {
				@mkdir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY, 0777);
				if (!is_dir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY)) {
					$errors = true;
					$error_msg .= "<span class=\"error\">".$pgv_lang["media_firewall_protected_dir_no_exist"]." ".$NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY."</span><br />\n";
				}
			}
		}
	}
	if (!$errors) {
		// create the thumbs dir to make sure we have write perms
		if (($NEW_USE_MEDIA_FIREWALL=='yes') || $USE_MEDIA_FIREWALL) {
			if (!is_dir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY."thumbs")) {
				@mkdir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY."thumbs", 0777);
				if (!is_dir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY."thumbs")) {
					$errors = true;
					$error_msg .= "<span class=\"error\">".$pgv_lang["media_firewall_protected_dir_not_writable"]." ".$NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY."</span><br />\n";
				}
			}
		}
	}
	if (!$errors) {
		// copy the .htaccess file from INDEX_DIRECTORY to NEW_MEDIA_FIREWALL_ROOTDIR in case it is still in a web-accessible area
		if (($NEW_USE_MEDIA_FIREWALL=='yes') || $USE_MEDIA_FIREWALL) {
			if ( (file_exists($INDEX_DIRECTORY.".htaccess")) && (is_dir($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY)) && (!file_exists($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY.".htaccess")) ) {
				@copy($INDEX_DIRECTORY.".htaccess", $NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY.".htaccess");
				if (!file_exists($NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY.".htaccess")) {
					$errors = true;
					$error_msg .= "<span class=\"error\">".$pgv_lang["media_firewall_protected_dir_not_writable"]." ".$NEW_MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY."</span><br />\n";
				}
			}
		}
	}
	if (!$errors) {
		$configtext = preg_replace('/\$MEDIA_FIREWALL_ROOTDIR\s*=\s*".*";/', "\$MEDIA_FIREWALL_ROOTDIR = \"".$_POST["NEW_MEDIA_FIREWALL_ROOTDIR"]."\";", $configtext);
	}	
	if (file_exists($NTHEME_DIR)) 
		$configtext = preg_replace('/\$THEME_DIR\s*=\s*".*";/', "\$THEME_DIR = \"".$_POST["NTHEME_DIR"]."\";", $configtext);
	else {
		$errors = true;
	}
	$configtext = preg_replace('/\$TIME_LIMIT\s*=\s*".*";/', "\$TIME_LIMIT = \"".$_POST["NEW_TIME_LIMIT"]."\";", $configtext);
	global $whichFile;
	$whichFile = $INDEX_DIRECTORY.$FILE."_conf.php";
	if (!is_writable($INDEX_DIRECTORY.$FILE."_conf.php")) {
		$errors = true;
		$error_msg .= "<span class=\"error\"><b>".print_text("gedcom_config_write_error",0,1)."</b></span><br />";
		$_SESSION[$gedcom_config]=$configtext;
		$error_msg .= "<br /><br /><a href=\"config_download.php?file=$gedcom_config\">".$pgv_lang["download_gedconf"]."</a> ".$pgv_lang["upload_to_index"]."$INDEX_DIRECTORY<br /><br />\n";
	}
	$fp = @fopen($INDEX_DIRECTORY.$FILE."_conf.php", "wb");
	if (!$fp) {
		$errors = true;
		$error_msg .= "<span class=\"error\">".print_text("gedcom_config_write_error",0,1)."</span><br />\n";
	}
	else {
		fwrite($fp, $configtext);
		fclose($fp);
	}


	if (($NEW_USE_MEDIA_FIREWALL=='yes') && !$USE_MEDIA_FIREWALL) {
		AddToLog("Media Firewall enabled");

		if (!$errors) {
			// create/modify an htaccess file in the main media directory
			$httext = "";
			if (file_exists($MEDIA_DIRECTORY.".htaccess")) {
				$httext = implode('', file($MEDIA_DIRECTORY.".htaccess"));
				// remove all PGV media firewall sections from the .htaccess
				$httext = preg_replace('/\n?^[#]*\s*BEGIN PGV MEDIA FIREWALL SECTION(.*\n){10}[#]*\s*END PGV MEDIA FIREWALL SECTION\s*[#]*\n?/m', "", $httext);
				// comment out any existing lines that set ErrorDocument 404
				$httext = preg_replace('/^(ErrorDocument\s*404(.*))\n?/', "#$1\n", $httext);
				$httext = preg_replace('/[^#](ErrorDocument\s*404(.*))\n?/', "\n#$1\n", $httext);
			}
			// add new PGV media firewall section to the end of the file
			$httext .= "\n######## BEGIN PGV MEDIA FIREWALL SECTION ##########";
			$httext .= "\n################## DO NOT MODIFY ###################";
			$httext .= "\n## THERE MUST BE EXACTLY 11 LINES IN THIS SECTION ##";
			$httext .= "\n<IfModule mod_rewrite.c>";
			$httext .= "\n\tRewriteEngine On";
			$httext .= "\n\tRewriteCond %{REQUEST_FILENAME} !-f";
			$httext .= "\n\tRewriteCond %{REQUEST_FILENAME} !-d";
			$httext .= "\n\tRewriteRule .* ".dirname($SCRIPT_NAME)."/mediafirewall.php [L]";
			$httext .= "\n</IfModule>";
			$httext .= "\nErrorDocument\t404\t".dirname($SCRIPT_NAME)."/mediafirewall.php";
			$httext .= "\n########## END PGV MEDIA FIREWALL SECTION ##########";

			$fp = @fopen($MEDIA_DIRECTORY.".htaccess", "wb");
			if (!$fp) {
				$errors = true;
				$error_msg .= "<span class=\"error\">".print_text("gedcom_config_write_error",0,1)."</span><br />\n";
			} else {
				fwrite($fp, $httext);
				fclose($fp);
			}
		}

	} elseif (($NEW_USE_MEDIA_FIREWALL=='no') && $USE_MEDIA_FIREWALL) {
		AddToLog("Media Firewall disabled");

		if (file_exists($MEDIA_DIRECTORY.".htaccess")) {
			$httext = implode('', file($MEDIA_DIRECTORY.".htaccess"));
			// remove all PGV media firewall sections from the .htaccess
			$httext = preg_replace('/\n?^[#]*\s*BEGIN PGV MEDIA FIREWALL SECTION(.*\n){10}[#]*\s*END PGV MEDIA FIREWALL SECTION\s*[#]*\n?/m', "", $httext);
			// comment out any lines that set ErrorDocument 404
			$httext = preg_replace('/^(ErrorDocument\s*404(.*))\n?/', "#$1\n", $httext);
			$httext = preg_replace('/[^#](ErrorDocument\s*404(.*))\n?/', "\n#$1\n", $httext);
			$fp = @fopen($MEDIA_DIRECTORY.".htaccess", "wb");
			if (!$fp) {
				$errors = true;
				$error_msg .= "<span class=\"error\">".print_text("gedcom_config_write_error",0,1)."</span><br />\n";
			} else {
				fwrite($fp, $httext);
				fclose($fp);
			}
		}

	}

	// Delete Upcoming Events cache
	if ($_POST["old_DAYS_TO_SHOW_LIMIT"] < $_POST["NEW_DAYS_TO_SHOW_LIMIT"]) {
		if (is_writable($INDEX_DIRECTORY) and file_exists($INDEX_DIRECTORY.$FILE."_upcoming.php")) {
			unlink ($INDEX_DIRECTORY.$FILE."_upcoming.php");
		}
	}
	foreach ($_POST as $key=>$value) {
		if ($key != "path") {
			$key=preg_replace("/NEW_/", "", $key);
			if ($value=='yes') $$key=true;
			else if ($value=='no') $$key=false;
			else $$key=$value;
		}
	}

	//-- delete the cache files for the welcome page blocks
	include_once("includes/index_cache.php");
	clearCache();

	$logline = AddToLog("Gedcom configuration ".$INDEX_DIRECTORY.$FILE."_conf.php"." updated");
	$gedcomconfname = $FILE."_conf.php";
	if (!empty($COMMIT_COMMAND)) check_in($logline, $gedcomconfname, $INDEX_DIRECTORY);
	if (!$errors) {
		$gednews = getUserNews($FILE);
		if (count($gednews)==0) {
			$news = array();
			$news["title"] = "#default_news_title#";
			$news["username"] = $FILE;
			$news["text"] = "#default_news_text#";
			$news["date"] = client_time();
			addNews($news);
		}
		if ($source == "upload_form") $check = "upload";
		else if ($source == "add_form") $check = "add";
		else if ($source == "add_new_form") $check = "add_new";
		if (!isset($bakfile)) $bakfile = "";
		if ($source !== "") header("Location: uploadgedcom.php?action=$source&check=$check&step=2&GEDFILENAME=$GEDFILENAME&path=$path&verify=verify_gedcom&bakfile=$bakfile");
		else {
			header("Location: editgedcoms.php");
		}
		exit;
	}
}
else if ($action=="replace") {
	header("Location: uploadgedcom.php?action=upload_form&GEDFILENAME=$GEDFILENAME&path=$path&verify=validate_form");
}

//-- output starts here
if (!isset($GENERATE_UIDS)) $GENERATE_UIDS = false;
$temp2 = $THEME_DIR;
$THEME_DIR = $temp;
print_header($pgv_lang["gedconf_head"]);
$THEME_DIR = $temp2;
// if (isset($FILE) && !check_for_import($FILE)) print "<span class=\"subheaders\">".$pgv_lang["step2"]." ".$pgv_lang["configure"]." + ".$pgv_lang["ged_gedcom"]."</span><br /><br />";
if (!isset($NTHEME_DIR)) $NTHEME_DIR=$THEME_DIR;
if (!isset($themeselect)) $themeselect="";
if (!empty($error)) print "<span class=\"error\">".$error."</span>";
?>
<script language="JavaScript" type="text/javascript">
<!--
	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'editconfig_help.php?help='+which;
		return false;
	}
	function getHelp(which) {
		if ((helpWin)&&(!helpWin.closed)) helpWin.location='editconfig_help.php?help='+which;
	}
	function closeHelp() {
		if (helpWin) helpWin.close();
	}
	function show_jewish() {
		var cal=document.getElementById('NEW_CALENDAR_FORMAT');
		var rtl=document.getElementById('NEW_USE_RTL_FUNCTIONS');
		if (rtl.options[rtl.selectedIndex].value=='yes' || cal.options[cal.selectedIndex].value.match(/jewish|hebrew/)) {
			document.getElementById('hebrew-cal' ).style.display='block';
		} else {
			document.getElementById('hebrew-cal' ).style.display='none';
		}
	}
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
//-->
</script>

<form enctype="multipart/form-data" method="post" id="configform" name="configform" action="editconfig_gedcom.php">

<table class="facts_table <?php print $TEXT_DIRECTION; ?>">
  <tr>
    <td colspan="2" class="facts_label"><?php
		print "<h2>".$pgv_lang["gedconf_head"]." - ";
		if (isset($ged)) {
//			if ($TEXT_DIRECTION=="rtl") print getRLM() . "(".$gGedcom[$ged]["id"].")&nbsp;" . getRLM();
//			else print "&nbsp;" . getLRM() . "(".$gGedcom[$ged]["id"].")" . getLRM();
			if (isset($gGedcom[$ged])) print $gGedcom[$ged]["title"];
		}
		else if ($source == "add_form") print $pgv_lang["add_gedcom"];
		else if ($source == "upload_form") print $pgv_lang["upload_gedcom"];
		else if ($source == "add_new_form") print $pgv_lang["add_new_gedcom"];
		else if ($source == "replace_form") print $pgv_lang['upload_replacement'];
		print "</h2>";
		print "<a href=\"editgedcoms.php\"><b>";
		print $pgv_lang["lang_back_manage_gedcoms"];
		print "</b></a><br /><br />";
	?>

<?php if ($source!="replace_form") { ?>
<script language="javascript" type="text/javascript">
<!--
var searchable_tds, searchable_text;

/* Function that returns all occurances of an element with the tagName "tag" that DO NOT have a parent (up to the root "node") with the same tagName 
(i.e. div A is a parent node of div B, this function would return only div A)*/
function getFirstElementsByTagName(node, tag){
	if (!node || !tag || !node.childNodes) return [];
	var rtn = [];
	var elms = node.getElementsByTagName(tag);
	for(var i=0; i<elms.length; i++){
		var parent = elms[i];
		while (parent && parent.parentNode && parent.parentNode.tagName.toUpperCase().indexOf(tag) < 0){
			if (parent.parentNode == node){
				rtn[rtn.length] = elms[i];
				break;
			}
			parent = parent.parentNode;
		}
	}
	return rtn;
}
/* Finds the name and short description of options for searching */
function capture_text(form){
	searchable_tds = [];							// Start with an empty array
	searchable_text = [];							// Start with an empty array
	var trs = form.getElementsByTagName("TR");		// Get the rows in the form
	for(var i=0; i<trs.length; i++){				// For each row in the form
		var tds = getFirstElementsByTagName(trs[i], "TD");// Get the TD's
		if (tds.length == 2 && tds[0].className.toUpperCase().indexOf("DESCRIPTIONBOX") >= 0){						// If there are exactly 2 TD's
			searchable_tds[searchable_tds.length] = tds[0];	// Save the TD's
			var text_nodes = ""; // variable for building the searchable text string
			for(var j=0; j<tds[0].childNodes.length; j++){ // for each child node in the td
				if (tds[0].childNodes[j].nodeType == 3){ // if the node is a TEXT NODE
					text_nodes = text_nodes + " " + tds[0].childNodes[j].textContent.toUpperCase(); // append a space and the capitalized text
				}
			}
			if (text_nodes && text_nodes != ""){
				searchable_text[searchable_text.length] = text_nodes; // Save the text, case insensitively
			}
		}
	}
}
/* Starting at a given node (or TD,) all parents, up to the form, are expanded & otherwise made visible. */
function expand_to_the_top(td){
	var p = td.parentNode;							// Start by expanding the row
	while (p && p.tagName.toUpperCase() != "FORM"){  // And expand until the parent form is hit
		/* Expand current node */
		if (p.style){								// If this foo has style, we can expand it
			if (p.tagName.toUpperCase() == "DIV" && p.id != ""){   // If this foo is a div, we can use the available expansion function provided in some other Javascript
				expand_layer(p.id, true);				// Expand it!
			}
			else{									// Otherwise
				p.style.display = "";				// Remove the "none" from the display
			}
		}
		p = p.parentNode;							// Climb up the tree
	}
}
/* Function that SHOWS all rows */
function show_all(root, first){
	if (root.childNodes){
    	for(var i=0; i<root.childNodes.length; i++)
    	{
    		show_all(root.childNodes[i], false);
    		if (root.style){
    			root.style.display = "";
    		}
    	}
	}
	if (first == undefined){
		collapse_divs(root);
	}
}
/* Function that HIDES all rows */
function hide_all(root){
	for(var i=0; i<searchable_tds.length; i++){
		searchable_tds[i].parentNode.style.display = "none;";
	}
	collapse_divs(root);
}
/* Function that COLLAPSES all divs (that are meant to be expanded) */
function collapse_divs(root){
	control_divs(root, false);
}

/* Function that EXPANDS all divs (that are meant to be expanded) */
function expand_divs(root){
	control_divs(root, true);
}
/* Function that HIDES all divs */
function hide_divs(root){
	var divs = getFirstElementsByTagName(root, "div");
	for(var i=0; i<divs.length; i++)
		divs[i].style.display = "none;";
}
/* Refactored function for controlling whether or not divs are expanded or collapsed */
function control_divs(root, boolexpand){
	var divs = root.getElementsByTagName("div");
	for(var i=0; i<divs.length; i++){
		if (divs[i].id != ""){   // If this foo is a div, we can use the available expansion function provided in some other Javascript
			expand_layer(divs[i].id, boolexpand);				// Collapse it!
		}
	}
}
/* Function that filters editconfig_gedcom.php options */
function hide_irrelevant(txt){
	var form = document.getElementById("configform"); 		// find the form we are sifting through
	if (txt && txt.length > 0 && txt != " "){				// If we have text to search for
		/* Save the TD's and TEXT's that will be searched if they haven't already been saved */
		if (searchable_tds == undefined){					// If we haven't already collected the searchable TD's & searchable texts
			capture_text(form);
		}
		hide_all(form);							// hide everything we dont want to see (everthing, until we find matches)
		expand_divs(form);
		hide_divs(form);
		txt = txt.toUpperCase();							// Make the search string case insensitive
		/* Search each searchable TEXT and see if we have a match */
		var matches = 0;
		for(var i=0; i<searchable_tds.length; i++){			// For each searchable TD
			if (searchable_text[i].indexOf(txt) > -1){					// If the text matches
				expand_to_the_top(searchable_tds[i]);		// Expand this node, and all parent's parent's nodes until we hit the form
				matches++;
			}
		}
		display_results(matches);
		show_hebrew();
		
	}
	else{
		display_results(-1);
		show_all(form);
		show_hebrew();
	}
}
/* Function that shows or hides Hebrew calendar options */
function show_hebrew(){
	var calendar = document.getElementById("NEW_CALENDAR_FORMAT");
	if (calendar != undefined)
		show_jewish(calendar, "hebrew-cal");
}
/* Function that resets the search field and collapses the form divs */
function clear_gedfilter(){
	display_results(-1);
	show_all(document.getElementById("configform"));
	document.getElementById("searching_for_options").value = "";
}
/* Function that writes the results to a span under the search field; -1 Means to clear it */
function display_results(amount_found){
	if (amount_found == -1)
		document.getElementById("gedfilter_results").innerHTML = "";
    else
    	document.getElementById("gedfilter_results").innerHTML = "<?php print $pgv_lang["ged_filter_results"]; ?>  " + amount_found;	
}
//-->
</script>
<table class="facts_table">
	<tr>
		<td>
			<?php print_help_link("ged_filter_description_help", "qm", "ged_filter_description"); ?>
			<b>
				<?php print $pgv_lang["ged_filter_description"]; ?>&nbsp;&nbsp;
			</b>
			<input type="text" id="searching_for_options" name="searching_for_options" onkeyup="hide_irrelevant(this.value);"/>
			&nbsp;&nbsp;
			<input type="submit" onclick="clear_gedfilter();return false;" value="<?php print $pgv_lang["ged_filter_reset"]; ?>"/>
			&nbsp;&nbsp;
			<p>
				<b>
					<font color="red">
						<span id="gedfilter_results"/>
					</font>
				</b>
			</p>
    </td>
  </tr>
</table>
<?php } // ($source!="replace_form") ?>
   </td>
  </tr>
</table>

<?php if ($source!="replace_form") { ?> <input type="hidden" name="action" value="update" />
<?php } else { ?> <input type="hidden" name="action" value="replace" /> <?php } ?>
<input type="hidden" name="source" value="<?php print $source; ?>" />
<input type="hidden" name="oldged" value="<?php print $oldged; ?>" />
<input type="hidden" name="old_DAYS_TO_SHOW_LIMIT" value="<?php print $DAYS_TO_SHOW_LIMIT; ?>" />
<?php
	if (!empty($error_msg)) print "<br /><span class=\"error\">".$error_msg."</span><br />\n";
	$i = 0;
?>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>

<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["gedcom_conf"]."\" onclick=\"expand_layer('file-options');return false\"><img id=\"file-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["gedcom_conf"]."\" onclick=\"expand_layer('file-options');return false\">".$pgv_lang["gedcom_conf"]."</a>";
?></td></tr></table>
<div id="file-options" style="display: block">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20">
		<?php
		if ($source == "upload_form" || $source=="replace_form") {
			print_help_link("upload_path_help", "qm", "upload_path"); print $pgv_lang["upload_path"];
			print "</td><td class=\"optionbox\">";
			print "<input name=\"GEDCOMPATH\" type=\"file\" size=\"60\" dir=\"ltr\" />";
			if ($source=="replace_form") print "<input type=\"hidden\" name=\"path\" value=\"".preg_replace('/\\*/', '\\', $path)."\" />";
			if (!$filesize = ini_get('upload_max_filesize')) $filesize = "2M";
			print " ( ".$pgv_lang["max_upload_size"]." $filesize )";
		}
		else {
			print_help_link("gedcom_path_help", "qm", "gedcom_path"); print $pgv_lang["gedcom_path"];
			print "</td><td class=\"optionbox\">";
			?>
		<input type="text" name="GEDCOMPATH" value="<?php print preg_replace('/\\*/', '\\', $GEDCOMPATH); ?>" size="40" dir ="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('gedcom_path_help');" />
		<?php
		}
			if ($GEDCOMPATH != "" || $GEDFILENAME != "") {
				if (!file_exists($path.$GEDFILENAME) && !empty($GEDCOMPATH)) {
					//-- gedcom not found so try looking for it with a .ged extension
					if (strtolower(substr(trim($path.$GEDFILENAME), -4)) != ".ged") $GEDFILENAME .= ".ged";
				}
				if ((!isFileExternal($GEDCOMPATH)) &&(!file_exists($path.$GEDFILENAME))) {
					print "<br /><span class=\"error\">".str_replace("#GEDCOM#", $GEDCOMPATH, $pgv_lang["error_header"])."</span>\n";
				}
			}
		?>
		</td>
	</tr>
	<?php if ($source == "upload_form") { ?>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("gedcom_path_help", "qm", "gedcom_path"); print $pgv_lang["gedcom_path"]; ?></td>
		<td class="optionbox">
		<input type="text" name="path" value="<?php print preg_replace('/\\*/', '\\', $path); ?>" size="40" dir ="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('gedcom_path_help');" />
		</td>
	</tr>
	<?php }
	if ($source != "replace_form") {
	?>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("gedcom_title_help", "qm", "gedcom_title", true); print print_text("gedcom_title"); ?></td>
		<td class="optionbox"><input type="text" name="gedcom_title" dir="ltr" value="<?php print preg_replace("/\"/", "&quot;", PrintReady($gedcom_title)); ?>" size="40" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('gedcom_title_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("LANGUAGE_help", "qm", "LANGUAGE"); print $pgv_lang["LANGUAGE"]; ?></td>
		<td class="optionbox"><input type="hidden" name="changelanguage" value="yes" />
		<select name="GEDCOMLANG" dir="ltr" onfocus="getHelp('LANGUAGE_help');" tabindex="<?php $i++; print $i; ?>">
		<?php
			foreach ($pgv_language as $key=>$value) {
			if ($language_settings[$key]["pgv_lang_use"]) {
					print "\n\t\t\t<option value=\"$key\"";
					if ($GEDCOMLANG==$key) print " selected=\"selected\"";
					print ">".$pgv_lang[$key]."</option>";
				}
			}
			print "</select>";
			if (!file_exists($INDEX_DIRECTORY . "lang_settings.php")) {
				print "<br /><span class=\"error\">";
				print $pgv_lang["LANGUAGE_DEFAULT"];
				print "</span>";
			}
		?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("CHARACTER_SET_help", "qm", "CHARACTER_SET"); print $pgv_lang["CHARACTER_SET"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_CHARACTER_SET" dir="ltr" value="<?php print $CHARACTER_SET; ?>" onfocus="getHelp('CHARACTER_SET_help');" tabindex="<?php $i++; print $i; ?>" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("PEDIGREE_ROOT_ID_help", "qm", "PEDIGREE_ROOT_ID"); print $pgv_lang["PEDIGREE_ROOT_ID"]; ?></td>

		<?php
		if ((!empty($GEDCOMPATH))&&(file_exists($path.$GEDFILENAME))&&(!empty($PEDIGREE_ROOT_ID))) {
			//-- the following section of code was modified from the find_record_in_file function of functions.php
			$fpged = fopen($path.$GEDFILENAME, "r");
			if ($fpged) {
				$gid = $PEDIGREE_ROOT_ID;
				$prefix = "";
				$suffix = $gid;
				$ct = preg_match("/^([a-zA-Z]+)/", $gid, $match);
				if ($ct>0) $prefix = $match[1];
				$ct = preg_match("/([\d\.]+)$/", $gid, $match);
				if ($ct>0) $suffix = $match[1];
				//print "prefix:$prefix suffix:$suffix";
				$BLOCK_SIZE = 1024*4;	//-- 4k bytes per read
				$fcontents = "";
				while (!feof($fpged)) {
					$fcontents = fread($fpged, $BLOCK_SIZE);
					//-- convert mac line endings
					$fcontents = preg_replace("/\r(\d)/", "\n$1", $fcontents);
					$ct = preg_match("/0 @(".$prefix."0*".$suffix.")@ INDI/", $fcontents, $match);
					if ($ct>0) {
						$gid = $match[1];
						$pos1 = strpos($fcontents, "0 @$gid@", 0);
						if ($pos1===false) $fcontents = "";
						else {
							$PEDIGREE_ROOT_ID = $gid;
							$pos2 = strpos($fcontents, "\n0", $pos1+1);
							while ((!$pos2)&&(!feof($fpged))) {
								$fcontents .= fread($fpged, $BLOCK_SIZE);
								$pos2 = strpos($fcontents, "\n0", $pos1+1);
							}
							if ($pos2) $indirec = substr($fcontents, $pos1, $pos2-$pos1);
							else $indirec = substr($fcontents, $pos1);
							break;
						}
					}
					else $fcontents = "";
				}
				fclose($fpged);
			}
		}
	?>
	<td class="optionbox"><input type="text" name="NEW_PEDIGREE_ROOT_ID" id="NEW_PEDIGREE_ROOT_ID" value="<?php print $PEDIGREE_ROOT_ID; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('PEDIGREE_ROOT_ID_help');" />
			<?php
			if ($source == "") {
				if (!empty($indirec)) {
					if ($source == "") {
						$indilist[$PEDIGREE_ROOT_ID]["gedcom"] = $indirec;
						$indilist[$PEDIGREE_ROOT_ID]["names"] = get_indi_names($indirec);
						$indilist[$PEDIGREE_ROOT_ID]["isdead"] = 1;
						$indilist[$PEDIGREE_ROOT_ID]["gedfile"] = $GEDCOM;
						echo '<span class="list_item">', get_person_name($PEDIGREE_ROOT_ID), format_first_major_fact($PEDIGREE_ROOT_ID), '</span>';
					}
				} else {
					echo '<span class="error">', $pgv_lang['unable_to_find_record'], '</span>';
				}
				print_findindi_link("NEW_PEDIGREE_ROOT_ID","");
			}
		?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("CALENDAR_FORMAT_help", "qm", "CALENDAR_FORMAT"); print $pgv_lang["CALENDAR_FORMAT"]; ?></td>
		<td class="optionbox"><select id="NEW_CALENDAR_FORMAT" name="NEW_CALENDAR_FORMAT" tabindex="<?php $i++; print $i; ?>"  onfocus="getHelp('CALENDAR_FORMAT_help');" onchange="show_jewish();">
		<?php
			foreach (array('none', 'gregorian', 'julian', 'french', 'jewish', 'jewish_and_gregorian', 'hebrew', 'hebrew_and_gregorian', 'hijri', 'arabic') as $cal) {
				print "<option value=\"{$cal}\"";
				if ($CALENDAR_FORMAT==$cal)
					print " selected=\"selected\"";
				print ">{$pgv_lang["cal_{$cal}"]}</option>";
			}
		?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("USE_RTL_FUNCTIONS_help", "qm", "USE_RTL_FUNCTIONS"); print $pgv_lang["USE_RTL_FUNCTIONS"]; ?></td>
		<td class="optionbox"><select id="NEW_USE_RTL_FUNCTIONS" name="NEW_USE_RTL_FUNCTIONS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('USE_RTL_FUNCTIONS_help');" onchange="show_jewish();">
				<option value="yes" <?php if ($USE_RTL_FUNCTIONS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$USE_RTL_FUNCTIONS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	</table>
	<div id="hebrew-cal" style="display: <?php print $USE_RTL_FUNCTIONS ? 'block' : 'none'; ?>">
	<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("DISPLAY_JEWISH_THOUSANDS_help", "qm", "DISPLAY_JEWISH_THOUSANDS"); print $pgv_lang["DISPLAY_JEWISH_THOUSANDS"]; ?></td>
		<td class="optionbox"><select name="NEW_DISPLAY_JEWISH_THOUSANDS" onfocus="getHelp('DISPLAY_JEWISH_THOUSANDS_help');">
				<option value="yes" <?php if ($DISPLAY_JEWISH_THOUSANDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$DISPLAY_JEWISH_THOUSANDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("DISPLAY_JEWISH_GERESHAYIM_help", "qm", "DISPLAY_JEWISH_GERESHAYIM"); print $pgv_lang["DISPLAY_JEWISH_GERESHAYIM"]; ?></td>
		<td class="optionbox"><select name="NEW_DISPLAY_JEWISH_GERESHAYIM" onfocus="getHelp('DISPLAY_JEWISH_GERESHAYIM_help');">
				<option value="yes" <?php if ($DISPLAY_JEWISH_GERESHAYIM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$DISPLAY_JEWISH_GERESHAYIM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("JEWISH_ASHKENAZ_PRONUNCIATION_help", "qm", "JEWISH_ASHKENAZ_PRONUNCIATION"); print $pgv_lang["JEWISH_ASHKENAZ_PRONUNCIATION"]; ?></td>
		<td class="optionbox"><select name="NEW_JEWISH_ASHKENAZ_PRONUNCIATION" onfocus="getHelp('JEWISH_ASHKENAZ_PRONUNCIATION_help');">
				<option value="yes" <?php if ($JEWISH_ASHKENAZ_PRONUNCIATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$JEWISH_ASHKENAZ_PRONUNCIATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	</table>
	</div>
	<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("USE_RIN_help", "qm", "USE_RIN"); print $pgv_lang["USE_RIN"]; ?></td>
		<td class="optionbox"><select name="NEW_USE_RIN" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('USE_RIN_help');">
				<option value="yes" <?php if ($USE_RIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$USE_RIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("GENERATE_GUID_help", "qm", "GENERATE_GUID"); print $pgv_lang["GENERATE_GUID"]; ?></td>
		<td class="optionbox"><select name="NEW_GENERATE_UIDS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('GENERATE_GUID_help');">
				<option value="yes" <?php if ($GENERATE_UIDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$GENERATE_UIDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("GEDCOM_ID_PREFIX_help", "qm", "GEDCOM_ID_PREFIX"); print $pgv_lang["GEDCOM_ID_PREFIX"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_GEDCOM_ID_PREFIX" dir="ltr" value="<?php print $GEDCOM_ID_PREFIX; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('GEDCOM_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20">
		<?php print_help_link("FAM_ID_PREFIX_help", "qm", "FAM_ID_PREFIX"); print $pgv_lang["FAM_ID_PREFIX"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_FAM_ID_PREFIX" dir="ltr" value="<?php print $FAM_ID_PREFIX; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('FAM_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SOURCE_ID_PREFIX_help", "qm", "SOURCE_ID_PREFIX"); print $pgv_lang["SOURCE_ID_PREFIX"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_SOURCE_ID_PREFIX" dir="ltr" value="<?php print $SOURCE_ID_PREFIX; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SOURCE_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("REPO_ID_PREFIX_help", "qm", "REPO_ID_PREFIX"); print $pgv_lang["REPO_ID_PREFIX"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_REPO_ID_PREFIX" dir="ltr" value="<?php print $REPO_ID_PREFIX; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('REPO_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MEDIA_ID_PREFIX_help", "qm", "MEDIA_ID_PREFIX"); print $pgv_lang["MEDIA_ID_PREFIX"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_MEDIA_ID_PREFIX" dir="ltr" value="<?php print $MEDIA_ID_PREFIX; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MEDIA_ID_PREFIX_help');" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SEARCHLOG_CREATE_help", "qm", "SEARCHLOG_CREATE"); print $pgv_lang["SEARCHLOG_CREATE"]; ?></td>
		<td class="optionbox"><select name="NEW_SEARCHLOG_CREATE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SEARCHLOG_CREATE_help');">
				<option value="none" <?php if ($SEARCHLOG_CREATE=="none") print "selected=\"selected\""; ?>><?php print $pgv_lang["no_logs"]; ?></option>
				<option value="daily" <?php if ($SEARCHLOG_CREATE=="daily") print "selected=\"selected\""; ?>><?php print $pgv_lang["daily"]; ?></option>
				<option value="weekly" <?php if ($SEARCHLOG_CREATE=="weekly") print "selected=\"selected\""; ?>><?php print $pgv_lang["weekly"]; ?></option>
				<option value="monthly" <?php if ($SEARCHLOG_CREATE=="monthly") print "selected=\"selected\""; ?>><?php print $pgv_lang["monthly"]; ?></option>
				<option value="yearly" <?php if ($SEARCHLOG_CREATE=="yearly") print "selected=\"selected\""; ?>><?php print $pgv_lang["yearly"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("CHANGELOG_CREATE_help", "qm", "CHANGELOG_CREATE"); print $pgv_lang["CHANGELOG_CREATE"]; ?></td>
		<td class="optionbox"><select name="NEW_CHANGELOG_CREATE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('CHANGELOG_CREATE_help');">
				<option value="none" <?php if ($CHANGELOG_CREATE=="none") print "selected=\"selected\""; ?>><?php print $pgv_lang["no_logs"]; ?></option>
				<option value="daily" <?php if ($CHANGELOG_CREATE=="daily") print "selected=\"selected\""; ?>><?php print $pgv_lang["daily"]; ?></option>
				<option value="weekly" <?php if ($CHANGELOG_CREATE=="weekly") print "selected=\"selected\""; ?>><?php print $pgv_lang["weekly"]; ?></option>
				<option value="monthly" <?php if ($CHANGELOG_CREATE=="monthly") print "selected=\"selected\""; ?>><?php print $pgv_lang["monthly"]; ?></option>
				<option value="yearly" <?php if ($CHANGELOG_CREATE=="yearly") print "selected=\"selected\""; ?>><?php print $pgv_lang["yearly"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("TIME_LIMIT_help", "qm", "TIME_LIMIT"); print $pgv_lang["TIME_LIMIT"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_TIME_LIMIT" value="<?php print $TIME_LIMIT; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('TIME_LIMIT_help');" /></td>
	</tr>
</table>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["media_conf"]."\" onclick=\"expand_layer('config-media');return false\"><img id=\"config-media_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["media_conf"]."\" onclick=\"expand_layer('config-media');return false\">".$pgv_lang["media_conf"]."</a>";
?></td></tr></table>
<div id="config-media" style="display: none">

<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MULTI_MEDIA_help", "qm", "MULTI_MEDIA"); print $pgv_lang["MULTI_MEDIA"]; ?></td>
		<td class="optionbox"><select name="NEW_MULTI_MEDIA" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MULTI_MEDIA_help');">
				<option value="yes" <?php if ($MULTI_MEDIA) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$MULTI_MEDIA) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
</table>

<table class="facts_table"><tr><td class="subbar">
<?php
print "<a href=\"javascript: ".$pgv_lang["media_general_conf"]."\" onclick=\"expand_layer('config-media1');return false\"><img id=\"config-media1_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["media_general_conf"]."\" onclick=\"expand_layer('config-media1');return false\">".$pgv_lang["media_general_conf"]."</a>";
?></td></tr></table>
<div id="config-media1" style="display: none">

<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MEDIA_EXTERNAL_help", "qm", "MEDIA_EXTERNAL"); print $pgv_lang["MEDIA_EXTERNAL"]; ?></td>
		<td class="optionbox"><select name="NEW_MEDIA_EXTERNAL" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MEDIA_EXTERNAL_help');">
				<option value="yes" <?php if ($MEDIA_EXTERNAL) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$MEDIA_EXTERNAL) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MEDIA_DIRECTORY_help", "qm", "MEDIA_DIRECTORY"); print $pgv_lang["MEDIA_DIRECTORY"]; ?></td>
		<td class="optionbox"><input type="text" size="50" name="NEW_MEDIA_DIRECTORY" value="<?php print $MEDIA_DIRECTORY; ?>" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MEDIA_DIRECTORY_help');" />
		<?php
		if (preg_match("/.*[a-zA-Z]{1}:.*/",$MEDIA_DIRECTORY)>0) print "<span class=\"error\">".$pgv_lang["media_drive_letter"]."</span>\n";
		?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MEDIA_DIRECTORY_LEVELS_help", "qm", "MEDIA_DIRECTORY_LEVELS"); print $pgv_lang["MEDIA_DIRECTORY_LEVELS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_MEDIA_DIRECTORY_LEVELS" value="<?php print $MEDIA_DIRECTORY_LEVELS; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MEDIA_DIRECTORY_LEVELS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("THUMBNAIL_WIDTH_help", "qm", "THUMBNAIL_WIDTH"); print $pgv_lang["THUMBNAIL_WIDTH"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_THUMBNAIL_WIDTH" value="<?php print $THUMBNAIL_WIDTH; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('THUMBNAIL_WIDTH_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("AUTO_GENERATE_THUMBS_help", "qm", "AUTO_GENERATE_THUMBS"); print $pgv_lang["AUTO_GENERATE_THUMBS"]; ?></td>
		<td class="optionbox"><select name="NEW_AUTO_GENERATE_THUMBS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('AUTO_GENERATE_THUMBS_help');">
				<option value="yes" <?php if ($AUTO_GENERATE_THUMBS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$AUTO_GENERATE_THUMBS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("USE_THUMBS_MAIN_help", "qm", "USE_THUMBS_MAIN"); print $pgv_lang["USE_THUMBS_MAIN"]; ?></td>
		<td class="optionbox"><select name="NEW_USE_THUMBS_MAIN" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('USE_THUMBS_MAIN_help');">
				<option value="yes" <?php if ($USE_THUMBS_MAIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$USE_THUMBS_MAIN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_HIGHLIGHT_IMAGES_help", "qm", "SHOW_HIGHLIGHT_IMAGES"); print $pgv_lang["SHOW_HIGHLIGHT_IMAGES"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_HIGHLIGHT_IMAGES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_HIGHLIGHT_IMAGES_help');">
				<option value="yes" <?php if ($SHOW_HIGHLIGHT_IMAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_HIGHLIGHT_IMAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("USE_MEDIA_VIEWER_help", "qm", "USE_MEDIA_VIEWER"); print $pgv_lang["USE_MEDIA_VIEWER"]; ?></td>
		<td class="optionbox"><select name="NEW_USE_MEDIA_VIEWER" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('USE_MEDIA_VIEWER_help');">
				<option value="yes" <?php if ($USE_MEDIA_VIEWER) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$USE_MEDIA_VIEWER) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_MEDIA_FILENAME_help", "qm", "SHOW_MEDIA_FILENAME"); print $pgv_lang["SHOW_MEDIA_FILENAME"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_MEDIA_FILENAME" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_MEDIA_FILENAME_help');">
				<option value="yes" <?php if ($SHOW_MEDIA_FILENAME) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_MEDIA_FILENAME) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_MEDIA_DOWNLOAD_help", "qm", "SHOW_MEDIA_DOWNLOAD"); print $pgv_lang["SHOW_MEDIA_DOWNLOAD"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_MEDIA_DOWNLOAD" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_MEDIA_DOWNLOAD_help');">
				<option value="yes" <?php if ($SHOW_MEDIA_DOWNLOAD) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_MEDIA_DOWNLOAD) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
</table>
</div>

<table class="facts_table"><tr><td class="subbar">
<?php
print "<a href=\"javascript: ".$pgv_lang["media_firewall_conf"]."\" onclick=\"expand_layer('config-media2');return false\"><img id=\"config-media2_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["media_firewall_conf"]."\" onclick=\"expand_layer('config-media2');return false\">".$pgv_lang["media_firewall_conf"]."</a>";
?></td></tr></table>
<div id="config-media2" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("USE_MEDIA_FIREWALL_help", "qm", "USE_MEDIA_FIREWALL"); print $pgv_lang["USE_MEDIA_FIREWALL"]; ?></td>
		<td class="optionbox"><select name="NEW_USE_MEDIA_FIREWALL" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('USE_MEDIA_FIREWALL_help');">
				<option value="yes" <?php if ($USE_MEDIA_FIREWALL) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$USE_MEDIA_FIREWALL) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MEDIA_FIREWALL_ROOTDIR_help", "qm", "MEDIA_FIREWALL_ROOTDIR"); print $pgv_lang["MEDIA_FIREWALL_ROOTDIR"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_MEDIA_FIREWALL_ROOTDIR" size="50" dir="ltr" value="<?php print ($MEDIA_FIREWALL_ROOTDIR == $INDEX_DIRECTORY) ? "" : $MEDIA_FIREWALL_ROOTDIR; ?>" onfocus="getHelp('MEDIA_FIREWALL_ROOTDIR_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		<?php print_text("MEDIA_FIREWALL_ROOTDIR_note"); ?></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MEDIA_FIREWALL_THUMBS_help", "qm", "MEDIA_FIREWALL_THUMBS"); print $pgv_lang["MEDIA_FIREWALL_THUMBS"]; ?></td>
		<td class="optionbox"><select name="NEW_MEDIA_FIREWALL_THUMBS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MEDIA_FIREWALL_THUMBS_help');">
				<option value="yes" <?php if ($MEDIA_FIREWALL_THUMBS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$MEDIA_FIREWALL_THUMBS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_NO_WATERMARK_help", "qm", "SHOW_NO_WATERMARK"); print $pgv_lang["SHOW_NO_WATERMARK"]; ?></td>
		<td class="optionbox">
			<select size="1" name="NEW_SHOW_NO_WATERMARK">
				<?php write_access_option($SHOW_NO_WATERMARK); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("WATERMARK_THUMB_help", "qm", "WATERMARK_THUMB"); print $pgv_lang["WATERMARK_THUMB"]; ?></td>
		<td class="optionbox">
			<select size="1" name="NEW_WATERMARK_THUMB">
				<option value="yes" <?php if ($WATERMARK_THUMB) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$WATERMARK_THUMB) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SAVE_WATERMARK_IMAGE_help", "qm", "SAVE_WATERMARK_IMAGE"); print $pgv_lang["SAVE_WATERMARK_IMAGE"]; ?></td>
		<td class="optionbox">
			<select size="1" name="NEW_SAVE_WATERMARK_IMAGE">
				<option value="yes" <?php if ($SAVE_WATERMARK_IMAGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SAVE_WATERMARK_IMAGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SAVE_WATERMARK_THUMB_help", "qm", "SAVE_WATERMARK_THUMB"); print $pgv_lang["SAVE_WATERMARK_THUMB"]; ?></td>
		<td class="optionbox">
			<select size="1" name="NEW_SAVE_WATERMARK_THUMB">
				<option value="yes" <?php if ($SAVE_WATERMARK_THUMB) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SAVE_WATERMARK_THUMB) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
</table>
</div>

<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["accpriv_conf"]."\" onclick=\"expand_layer('access-options');return false\"><img id=\"access-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["accpriv_conf"]."\" onclick=\"expand_layer('access-options');return false\">".$pgv_lang["accpriv_conf"]."</a>";
?></td></tr></table>
<div id="access-options" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("HIDE_LIVE_PEOPLE_help", "qm", "HIDE_LIVE_PEOPLE"); print $pgv_lang["HIDE_LIVE_PEOPLE"]; ?></td>
		<td class="optionbox"><select name="NEW_HIDE_LIVE_PEOPLE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('HIDE_LIVE_PEOPLE_help');">
				<option value="yes" <?php if ($HIDE_LIVE_PEOPLE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$HIDE_LIVE_PEOPLE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("CHECK_CHILD_DATES_help", "qm", "CHECK_CHILD_DATES"); print $pgv_lang["CHECK_CHILD_DATES"]; ?></td>
		<td class="optionbox"><select name="NEW_CHECK_CHILD_DATES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('CHECK_CHILD_DATES_help');">
				<option value="yes" <?php if ($CHECK_CHILD_DATES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$CHECK_CHILD_DATES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("REQUIRE_AUTHENTICATION_help", "qm", "REQUIRE_AUTHENTICATION"); print $pgv_lang["REQUIRE_AUTHENTICATION"]; ?></td>
		<td class="optionbox"><select name="NEW_REQUIRE_AUTHENTICATION" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('REQUIRE_AUTHENTICATION_help');">
				<option value="yes" <?php if ($REQUIRE_AUTHENTICATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$REQUIRE_AUTHENTICATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"> <?php print_help_link("WELCOME_TEXT_AUTH_MODE_help", "qm", "WELCOME_TEXT_AUTH_MODE"); print $pgv_lang["WELCOME_TEXT_AUTH_MODE"]; ?></td>
		<td class="optionbox"><select name="NEW_WELCOME_TEXT_AUTH_MODE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('WELCOME_TEXT_AUTH_MODE_help');">
				<option value="0" <?php if ($WELCOME_TEXT_AUTH_MODE=='0') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT0"]; ?></option>
				<option value="1" <?php if ($WELCOME_TEXT_AUTH_MODE=='1') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT1"]; ?></option>
				<option value="2" <?php if ($WELCOME_TEXT_AUTH_MODE=='2') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT2"]; ?></option>
				<option value="3" <?php if ($WELCOME_TEXT_AUTH_MODE=='3') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT3"]; ?></option>
				<option value="4" <?php if ($WELCOME_TEXT_AUTH_MODE=='4') print "selected=\"selected\""; ?>><?php print $pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT4"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("WELCOME_TEXT_AUTH_MODE_CUST_HEAD_help", "qm", "WELCOME_TEXT_AUTH_MODE_CUST_HEAD"); print $pgv_lang["WELCOME_TEXT_AUTH_MODE_CUST_HEAD"]; ?></td>
		<td class="optionbox"><select name="NEW_WELCOME_TEXT_CUST_HEAD" onfocus="getHelp('WELCOME_TEXT_AUTH_MODE_CUST_HEAD_help');" tabindex="<?php $i++; print $i; ?>" >
				<option value="yes" <?php if ($WELCOME_TEXT_CUST_HEAD) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$WELCOME_TEXT_CUST_HEAD) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("WELCOME_TEXT_AUTH_MODE_CUST_help", "qm", "WELCOME_TEXT_AUTH_MODE_CUST"); print $pgv_lang["WELCOME_TEXT_AUTH_MODE_CUST"]; ?></td>
		<td class="optionbox"><textarea name="NEW_WELCOME_TEXT_AUTH_MODE_4" rows="5" cols="60" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('WELCOME_TEXT_AUTH_MODE_CUST_help');"><?php print  $WELCOME_TEXT_AUTH_MODE_4; ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_REGISTER_CAUTION_help", "qm", "SHOW_REGISTER_CAUTION"); print $pgv_lang["SHOW_REGISTER_CAUTION"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_REGISTER_CAUTION" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_REGISTER_CAUTION_help');">
				<option value="yes" <?php if ($SHOW_REGISTER_CAUTION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_REGISTER_CAUTION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
</table>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["displ_conf"]."\" onclick=\"expand_layer('layout-options');return false\"><img id=\"layout-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["displ_conf"]."\" onclick=\"expand_layer('layout-options');return false\">".$pgv_lang["displ_conf"]."</a>";
?></td></tr></table>
<div id="layout-options" style="display: none">

<table class="facts_table"><tr><td class="subbar">
<?php
print "<a href=\"javascript: ".$pgv_lang["displ_names_conf"]."\" onclick=\"expand_layer('layout-options2');return false\"><img id=\"layout-options2_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["displ_names_conf"]."\" onclick=\"expand_layer('layout-options2');return false\">".$pgv_lang["displ_names_conf"]."</a>";
?></td></tr></table>
<div id="layout-options2" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_MARRIED_NAMES_help", "qm", "SHOW_MARRIED_NAMES"); print $pgv_lang["SHOW_MARRIED_NAMES"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_MARRIED_NAMES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_MARRIED_NAMES_help');">
				<option value="yes" <?php if ($SHOW_MARRIED_NAMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_MARRIED_NAMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("UNDERLINE_NAME_QUOTES_help", "qm", "UNDERLINE_NAME_QUOTES"); print $pgv_lang["UNDERLINE_NAME_QUOTES"]; ?></td>
		<td class="optionbox"><select name="NEW_UNDERLINE_NAME_QUOTES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('UNDERLINE_NAME_QUOTES_help');">
				<option value="yes" <?php if ($UNDERLINE_NAME_QUOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$UNDERLINE_NAME_QUOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_ID_NUMBERS_help", "qm", "SHOW_ID_NUMBERS"); print $pgv_lang["SHOW_ID_NUMBERS"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_ID_NUMBERS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_ID_NUMBERS_help');">
				<option value="yes" <?php if ($SHOW_ID_NUMBERS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_ID_NUMBERS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("NAME_FROM_GEDCOM_help", "qm", "NAME_FROM_GEDCOM"); print $pgv_lang["NAME_FROM_GEDCOM"]; ?></td>
		<td class="optionbox"><select name="NEW_NAME_FROM_GEDCOM" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('NAME_FROM_GEDCOM_help');">
				<option value="yes" <?php if ($NAME_FROM_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$NAME_FROM_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
</table>
</div>

<table class="facts_table"><tr><td class="subbar">
<?php
print "<a href=\"javascript: ".$pgv_lang["displ_comsurn_conf"]."\" onclick=\"expand_layer('layout-options3');return false\"><img id=\"layout-options3_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["displ_comsurn_conf"]."\" onclick=\"expand_layer('layout-options3');return false\">".$pgv_lang["displ_comsurn_conf"]."</a>";
?></td></tr></table>
<div id="layout-options3" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("COMMON_NAMES_THRESHOLD_help", "qm", "COMMON_NAMES_THRESHOLD"); print $pgv_lang["COMMON_NAMES_THRESHOLD"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_COMMON_NAMES_THRESHOLD" value="<?php print $COMMON_NAMES_THRESHOLD; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('COMMON_NAMES_THRESHOLD_help');" /></td>
	</tr>

	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("COMMON_NAMES_ADD_help", "qm", "COMMON_NAMES_ADD"); print $pgv_lang["COMMON_NAMES_ADD"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_COMMON_NAMES_ADD" dir="ltr" value="<?php print $COMMON_NAMES_ADD; ?>" size="50" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('COMMON_NAMES_ADD_help');" /></td>
	</tr>

	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("COMMON_NAMES_REMOVE_help", "qm", "COMMON_NAMES_REMOVE"); print $pgv_lang["COMMON_NAMES_REMOVE"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_COMMON_NAMES_REMOVE" dir="ltr" value="<?php print $COMMON_NAMES_REMOVE; ?>" size="50" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('COMMON_NAMES_REMOVE_help');" /></td>
	</tr>
</table>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<?php // Display and Layout
?>
<table class="facts_table"><tr><td class="subbar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["displ_layout_conf"]."\" onclick=\"expand_layer('layout-options4');return false\"><img id=\"layout-options4_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["displ_layout_conf"]."\" onclick=\"expand_layer('layout-options4');return false\">".$pgv_lang["displ_layout_conf"]."</a>";
?></td></tr></table>
<div id="layout-options4" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("DEFAULT_PEDIGREE_GENERATIONS_help", "qm", "DEFAULT_PEDIGREE_GENERATIONS"); print $pgv_lang["DEFAULT_PEDIGREE_GENERATIONS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_DEFAULT_PEDIGREE_GENERATIONS" value="<?php print $DEFAULT_PEDIGREE_GENERATIONS; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('DEFAULT_PEDIGREE_GENERATIONS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MAX_PEDIGREE_GENERATIONS_help", "qm", "MAX_PEDIGREE_GENERATIONS"); print $pgv_lang["MAX_PEDIGREE_GENERATIONS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_MAX_PEDIGREE_GENERATIONS" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('MAX_PEDIGREE_GENERATIONS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("MAX_DESCENDANCY_GENERATIONS_help", "qm", "MAX_DESCENDANCY_GENERATIONS"); print $pgv_lang["MAX_DESCENDANCY_GENERATIONS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_MAX_DESCENDANCY_GENERATIONS" value="<?php print $MAX_DESCENDANCY_GENERATIONS; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('DMAX_DESCENDANCY_GENERATIONS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("PEDIGREE_LAYOUT_help", "qm", "PEDIGREE_LAYOUT"); print $pgv_lang["PEDIGREE_LAYOUT"]; ?></td>
		<td class="optionbox"><select name="NEW_PEDIGREE_LAYOUT" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('PEDIGREE_LAYOUT_help');">
				<option value="yes" <?php if ($PEDIGREE_LAYOUT) print "selected=\"selected\""; ?>><?php print $pgv_lang["landscape"]; ?></option>
				<option value="no" <?php if (!$PEDIGREE_LAYOUT) print "selected=\"selected\""; ?>><?php print $pgv_lang["portrait"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_PEDIGREE_PLACES_help", "qm", "SHOW_PEDIGREE_PLACES"); print $pgv_lang["SHOW_PEDIGREE_PLACES"]; ?></td>
		<td class="optionbox"><input type="text" size="5" name="NEW_SHOW_PEDIGREE_PLACES" value="<?php print $SHOW_PEDIGREE_PLACES; ?>" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_PEDIGREE_PLACES_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ZOOM_BOXES_help", "qm", "ZOOM_BOXES"); print $pgv_lang["ZOOM_BOXES"]; ?></td>
		<td class="optionbox"><select name="NEW_ZOOM_BOXES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ZOOM_BOXES_help');">
				<option value="disabled" <?php if ($ZOOM_BOXES=='disabled') print "selected=\"selected\""; ?>><?php print $pgv_lang["disabled"]; ?></option>
				<option value="mouseover" <?php if ($ZOOM_BOXES=='mouseover') print "selected=\"selected\""; ?>><?php print $pgv_lang["mouseover"]; ?></option>
				<option value="mousedown" <?php if ($ZOOM_BOXES=='mousedown') print "selected=\"selected\""; ?>><?php print $pgv_lang["mousedown"]; ?></option>
				<option value="click" <?php if ($ZOOM_BOXES=='click') print "selected=\"selected\""; ?>><?php print $pgv_lang["click"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("LINK_ICONS_help", "qm", "LINK_ICONS"); print $pgv_lang["LINK_ICONS"]; ?></td>
		<td class="optionbox"><select name="NEW_LINK_ICONS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('LINK_ICONS_help');">
				<option value="disabled" <?php if ($LINK_ICONS=='disabled') print "selected=\"selected\""; ?>><?php print $pgv_lang["disabled"]; ?></option>
				<option value="mouseover" <?php if ($LINK_ICONS=='mouseover') print "selected=\"selected\""; ?>><?php print $pgv_lang["mouseover"]; ?></option>
				<option value="click" <?php if ($LINK_ICONS=='click') print "selected=\"selected\""; ?>><?php print $pgv_lang["click"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("GEDCOM_DEFAULT_TAB_help", "qm", "GEDCOM_DEFAULT_TAB"); print $pgv_lang["GEDCOM_DEFAULT_TAB"]; ?></td>
		<td class="optionbox"><select name="NEW_GEDCOM_DEFAULT_TAB" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('GEDCOM_DEFAULT_TAB_help');">
				<option value="0" <?php if ($GEDCOM_DEFAULT_TAB==0) print "selected=\"selected\""; ?>><?php print $pgv_lang["personal_facts"]; ?></option>
				<option value="1" <?php if ($GEDCOM_DEFAULT_TAB==1) print "selected=\"selected\""; ?>><?php print $pgv_lang["notes"]; ?></option>
				<option value="2" <?php if ($GEDCOM_DEFAULT_TAB==2) print "selected=\"selected\""; ?>><?php print $pgv_lang["ssourcess"]; ?></option>
				<option value="3" <?php if ($GEDCOM_DEFAULT_TAB==3) print "selected=\"selected\""; ?>><?php print $pgv_lang["media"]; ?></option>
				<option value="4" <?php if ($GEDCOM_DEFAULT_TAB==4) print "selected=\"selected\""; ?>><?php print $pgv_lang["relatives"]; ?></option>
				<option value="-1" <?php if ($GEDCOM_DEFAULT_TAB==-1) print "selected=\"selected\""; ?>><?php print $pgv_lang["all"]; ?></option>
				<option value="-2" <?php if ($GEDCOM_DEFAULT_TAB==-2) print "selected=\"selected\""; ?>><?php print $pgv_lang["lasttab"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_RELATIVES_EVENTS_help", "qm", "SHOW_RELATIVES_EVENTS"); print $pgv_lang["SHOW_RELATIVES_EVENTS"]; ?></td>
		<td class="optionbox">
			<input type="hidden" name="NEW_SHOW_RELATIVES_EVENTS" value="<?php echo $SHOW_RELATIVES_EVENTS; ?>" />
<?php
$previous="_DEAT_";
print "<table>";
foreach ($factarray as $factkey=>$factlabel) {
	$f6=substr($factkey,0,6);
	if ($f6=="_BIRT_" or $f6=="_MARR_" or $f6=="_DEAT_" or $f6=="_FAMC_") {
		if ($f6=="_BIRT_" or $f6=="_FAMC_") print "<tr>";
		if ($f6=="_MARR_" and $previous!="_BIRT_") print "<tr><td>&nbsp;</td>";
		if ($f6=="_DEAT_" and $previous=="_DEAT_") print "<tr><td>&nbsp;</td>";
		if ($f6=="_DEAT_" and $previous!="_MARR_") print "<td>&nbsp;</td>";
		print "\n<td><input type=\"checkbox\" name=\"SHOW_RELATIVES_EVENTS_checkbox\" value=\"".$factkey."\"";
		if (strstr($SHOW_RELATIVES_EVENTS,$factkey)) print " checked=\"checked\"";
		print " onchange=\"var old=document.configform.NEW_SHOW_RELATIVES_EVENTS.value; if (this.checked) old+=','+this.value; else old=old.replace(/".$factkey."/g,''); old=old.replace(/[,]+/gi,','); old=old.replace(/^[,]/gi,''); old=old.replace(/[,]$/gi,''); document.configform.NEW_SHOW_RELATIVES_EVENTS.value=old\" ";
		print " /> ".$factlabel."</td>";
		if ($f6=="_DEAT_") print "</tr>";
		$previous=$f6;
	}
}
print "</table>";
print "<tr>";
?>
		<td class="descriptionbox wrap width20"><?php print_help_link("EXPAND_RELATIVES_EVENTS_help", "qm", "EXPAND_RELATIVES_EVENTS"); print $pgv_lang["EXPAND_RELATIVES_EVENTS"]; ?></td>
		<td class="optionbox">
			<select name="NEW_EXPAND_RELATIVES_EVENTS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('EXPAND_RELATIVES_EVENTS_help');">
				<option value="yes" <?php if ($EXPAND_RELATIVES_EVENTS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$EXPAND_RELATIVES_EVENTS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
<?php
print "</tr>";
?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("POSTAL_CODE_help", "qm", "POSTAL_CODE"); print $pgv_lang["POSTAL_CODE"]; ?></td>
		<td class="optionbox"><select name="NEW_POSTAL_CODE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('POSTAL_CODE_help');">
				<option value="yes" <?php if ($POSTAL_CODE) print "selected=\"selected\""; ?>><?php print ucfirst($pgv_lang["after"]); ?></option>
				<option value="no" <?php if (!$POSTAL_CODE) print "selected=\"selected\""; ?>><?php print ucfirst($pgv_lang["before"]); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SUBLIST_TRIGGER_I_help", "qm", "SUBLIST_TRIGGER_I"); print $pgv_lang["SUBLIST_TRIGGER_I"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_SUBLIST_TRIGGER_I" value="<?php print $SUBLIST_TRIGGER_I; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SUBLIST_TRIGGER_I_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SUBLIST_TRIGGER_F_help", "qm", "SUBLIST_TRIGGER_F"); print $pgv_lang["SUBLIST_TRIGGER_F"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_SUBLIST_TRIGGER_F" value="<?php print $SUBLIST_TRIGGER_F; ?>" size="5" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SUBLIST_TRIGGER_F_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SURNAME_LIST_STYLE_help", "qm", "SURNAME_LIST_STYLE"); print $pgv_lang["SURNAME_LIST_STYLE"]; ?></td>
		<td class="optionbox"><select name="NEW_SURNAME_LIST_STYLE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SURNAME_LIST_STYLE_help');">
				<option value="style2" <?php if ($SURNAME_LIST_STYLE=="style2") print "selected=\"selected\""; ?>><?php print $pgv_lang["style2"]; ?></option>
				<option value="style3" <?php if ($SURNAME_LIST_STYLE=="style3") print "selected=\"selected\""; ?>><?php print $pgv_lang["style3"]; ?></option>
			</select>
		</td>
	</tr>
</table>
</div>


<table class="facts_table"><tr><td class="subbar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["displ_hide_conf"]."\" onclick=\"expand_layer('layout-options5');return false\"><img id=\"layout-options5_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["displ_hide_conf"]."\" onclick=\"expand_layer('layout-options5');return false\">".$pgv_lang["displ_hide_conf"]."</a>";
?></td></tr></table>
<div id="layout-options5" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("DAYS_TO_SHOW_LIMIT_help", "qm", "DAYS_TO_SHOW_LIMIT"); print $pgv_lang["DAYS_TO_SHOW_LIMIT"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_DAYS_TO_SHOW_LIMIT" value="<?php print $DAYS_TO_SHOW_LIMIT; ?>" size="2" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('DAYS_TO_SHOW_LIMIT_help');" /></td>
	</tr>

	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_EMPTY_BOXES_help", "qm", "SHOW_EMPTY_BOXES"); print $pgv_lang["SHOW_EMPTY_BOXES"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_EMPTY_BOXES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_EMPTY_BOXES_help');">
				<option value="yes" <?php if ($SHOW_EMPTY_BOXES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_EMPTY_BOXES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ABBREVIATE_CHART_LABELS_help", "qm", "ABBREVIATE_CHART_LABELS"); print $pgv_lang["ABBREVIATE_CHART_LABELS"]; ?></td>
		<td class="optionbox"><select name="NEW_ABBREVIATE_CHART_LABELS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ABBREVIATE_CHART_LABELS_help');">
				<option value="yes" <?php if ($ABBREVIATE_CHART_LABELS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$ABBREVIATE_CHART_LABELS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("PEDIGREE_FULL_DETAILS_help", "qm", "PEDIGREE_FULL_DETAILS"); print $pgv_lang["PEDIGREE_FULL_DETAILS"]; ?></td>
		<td class="optionbox"><select name="NEW_PEDIGREE_FULL_DETAILS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('PEDIGREE_FULL_DETAILS_help');">
				<option value="yes" <?php if ($PEDIGREE_FULL_DETAILS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$PEDIGREE_FULL_DETAILS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("PEDIGREE_SHOW_GENDER_help", "qm", "PEDIGREE_SHOW_GENDER"); print $pgv_lang["PEDIGREE_SHOW_GENDER"]; ?></td>
		<td class="optionbox"><select name="NEW_PEDIGREE_SHOW_GENDER" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('PEDIGREE_SHOW_GENDER_help');">
				<option value="yes" <?php if ($PEDIGREE_SHOW_GENDER) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$PEDIGREE_SHOW_GENDER) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_PARENTS_AGE_help", "qm", "SHOW_PARENTS_AGE"); print $pgv_lang["SHOW_PARENTS_AGE"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_PARENTS_AGE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_PARENTS_AGE_help');">
				<option value="yes" <?php if ($SHOW_PARENTS_AGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_PARENTS_AGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_LDS_AT_GLANCE_help", "qm", "SHOW_LDS_AT_GLANCE"); print $pgv_lang["SHOW_LDS_AT_GLANCE"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_LDS_AT_GLANCE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_LDS_AT_GLANCE_help');">
				<option value="yes" <?php if ($SHOW_LDS_AT_GLANCE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_LDS_AT_GLANCE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("CHART_BOX_TAGS_help", "qm", "CHART_BOX_TAGS"); print $pgv_lang["CHART_BOX_TAGS"]; ?></td>
		<td class="optionbox">
			<input type="text" size="50" name="NEW_CHART_BOX_TAGS" value="<?php print $CHART_BOX_TAGS; ?>" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('CHART_BOX_TAGS_help');" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_GEDCOM_RECORD_help", "qm", "SHOW_GEDCOM_RECORD"); print $pgv_lang["SHOW_GEDCOM_RECORD"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_GEDCOM_RECORD" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_GEDCOM_RECORD_help');">
				<option value="yes" <?php if ($SHOW_GEDCOM_RECORD) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_GEDCOM_RECORD) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("HIDE_GEDCOM_ERRORS_help", "qm", "HIDE_GEDCOM_ERRORS"); print $pgv_lang["HIDE_GEDCOM_ERRORS"]; ?></td>
		<td class="optionbox"><select name="NEW_HIDE_GEDCOM_ERRORS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('HIDE_GEDCOM_ERRORS_help');">
				<option value="yes" <?php if ($HIDE_GEDCOM_ERRORS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$HIDE_GEDCOM_ERRORS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("WORD_WRAPPED_NOTES_help", "qm", "WORD_WRAPPED_NOTES"); print $pgv_lang["WORD_WRAPPED_NOTES"]; ?></td>
		<td class="optionbox"><select name="NEW_WORD_WRAPPED_NOTES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('WORD_WRAPPED_NOTES_help');">
				<option value="yes" <?php if ($WORD_WRAPPED_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$WORD_WRAPPED_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_FACT_ICONS_help", "qm", "SHOW_FACT_ICONS"); print $pgv_lang["SHOW_FACT_ICONS"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_FACT_ICONS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_FACT_ICONS_help');">
				<option value="yes" <?php if ($SHOW_FACT_ICONS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_FACT_ICONS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("EXPAND_SOURCES_help", "qm", "EXPAND_SOURCES"); print $pgv_lang["EXPAND_SOURCES"]; ?></td>
		<td class="optionbox">
			<select name="NEW_EXPAND_SOURCES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('EXPAND_SOURCES_help');">
				<option value="yes" <?php if ($EXPAND_SOURCES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$EXPAND_SOURCES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("EXPAND_NOTES_help", "qm", "EXPAND_NOTES"); print $pgv_lang["EXPAND_NOTES"]; ?></td>
		<td class="optionbox">
			<select name="NEW_EXPAND_NOTES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('EXPAND_NOTES_help');">
				<option value="yes" <?php if ($EXPAND_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$EXPAND_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_LEVEL2_NOTES_help", "qm", "SHOW_LEVEL2_NOTES"); print $pgv_lang["SHOW_LEVEL2_NOTES"]; ?></td>
		<td class="optionbox">
			<select name="NEW_SHOW_LEVEL2_NOTES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_LEVEL2_NOTES_help');">
				<option value="yes" <?php if ($SHOW_LEVEL2_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_LEVEL2_NOTES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_AGE_DIFF_help", "qm", "SHOW_AGE_DIFF"); print $pgv_lang["SHOW_AGE_DIFF"]; ?></td>
		<td class="optionbox">
			<select name="NEW_SHOW_AGE_DIFF" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('EXPAND_NOTES_help');">
				<option value="yes" <?php if ($SHOW_AGE_DIFF) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_AGE_DIFF) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("FAVICON_help", "qm", "FAVICON"); print $pgv_lang["FAVICON"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_FAVICON" value="<?php print $FAVICON; ?>" size="40" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('FAVICON_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_COUNTER_help", "qm", "SHOW_COUNTER"); print $pgv_lang["SHOW_COUNTER"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_COUNTER" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_COUNTER_help');">
				<option value="yes" <?php if ($SHOW_COUNTER) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_COUNTER) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_SPIDER_TAGLINE_help", "qm", "SHOW_SPIDER_TAGLINE"); print $pgv_lang["SHOW_SPIDER_TAGLINE"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_SPIDER_TAGLINE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_SPIDER_TAGLINE_help');">
				<option value="yes" <?php if ($SHOW_SPIDER_TAGLINE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_SPIDER_TAGLINE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_STATS_help", "qm", "SHOW_STATS"); print $pgv_lang["SHOW_STATS"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_STATS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_STATS_help');">
				<option value="yes" <?php if ($SHOW_STATS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_STATS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_LAST_CHANGE_help", "qm", "SHOW_LAST_CHANGE"); print $pgv_lang["SHOW_LAST_CHANGE"]; ?></td>
        <td class="optionbox"><select name="NEW_SHOW_LAST_CHANGE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_LAST_CHANGE_help');">
			<option value="yes" <?php if ($SHOW_LAST_CHANGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
            <option value="no" <?php if (!$SHOW_LAST_CHANGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
            </select>
        </td>
    </tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_EST_LIST_DATES_help", "qm", "SHOW_EST_LIST_DATES"); print $pgv_lang["SHOW_EST_LIST_DATES"]; ?></td>
        <td class="optionbox"><select name="NEW_SHOW_EST_LIST_DATES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_EST_LIST_DATES_help');">
			<option value="yes" <?php if ($SHOW_EST_LIST_DATES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
            <option value="no" <?php if (!$SHOW_EST_LIST_DATES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
            </select>
        </td>
    </tr>
</table>
</div>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<?php // Edit Options
?>
<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["editopt_conf"]."\" onclick=\"expand_layer('edit-options');return false\"><img id=\"edit-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["editopt_conf"]."\" onclick=\"expand_layer('edit-options');return false\">".$pgv_lang["editopt_conf"]."</a>";
?></td></tr></table>
<div id="edit-options" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ALLOW_EDIT_GEDCOM_help", "qm", "ALLOW_EDIT_GEDCOM"); print $pgv_lang["ALLOW_EDIT_GEDCOM"]; ?></td>
		<td class="optionbox"><select name="NEW_ALLOW_EDIT_GEDCOM" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ALLOW_EDIT_GEDCOM_help');">
				<option value="yes" <?php if ($ALLOW_EDIT_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$ALLOW_EDIT_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SYNC_GEDCOM_FILE_help", "qm", "SYNC_GEDCOM_FILE"); print $pgv_lang["SYNC_GEDCOM_FILE"]; ?></td>
		<td class="optionbox"><select name="NEW_SYNC_GEDCOM_FILE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SYNC_GEDCOM_FILE_help');">
				<option value="yes" <?php if ($SYNC_GEDCOM_FILE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SYNC_GEDCOM_FILE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("INDI_FACTS_ADD_help", "qm", "INDI_FACTS_ADD"); print $pgv_lang["INDI_FACTS_ADD"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_INDI_FACTS_ADD" value="<?php print $INDI_FACTS_ADD; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('INDI_FACTS_ADD_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("INDI_FACTS_UNIQUE_help", "qm", "INDI_FACTS_UNIQUE"); print $pgv_lang["INDI_FACTS_UNIQUE"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_INDI_FACTS_UNIQUE" value="<?php print $INDI_FACTS_UNIQUE; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('INDI_FACTS_UNIQUE_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("INDI_FACTS_QUICK_help", "qm", "INDI_FACTS_QUICK"); print $pgv_lang["INDI_FACTS_QUICK"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_INDI_FACTS_QUICK" value="<?php print $INDI_FACTS_QUICK; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('INDI_FACTS_QUICK_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("FAM_FACTS_ADD_help", "qm", "FAM_FACTS_ADD"); print $pgv_lang["FAM_FACTS_ADD"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_FAM_FACTS_ADD" value="<?php print $FAM_FACTS_ADD; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('FAM_FACTS_ADD_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("FAM_FACTS_UNIQUE_help", "qm", "FAM_FACTS_UNIQUE"); print $pgv_lang["FAM_FACTS_UNIQUE"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_FAM_FACTS_UNIQUE" value="<?php print $FAM_FACTS_UNIQUE; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('FAM_FACTS_UNIQUE_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("FAM_FACTS_QUICK_help", "qm", "FAM_FACTS_QUICK"); print $pgv_lang["FAM_FACTS_QUICK"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_FAM_FACTS_QUICK" value="<?php print $FAM_FACTS_QUICK; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('FAM_FACTS_QUICK_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SOUR_FACTS_ADD_help", "qm", "SOUR_FACTS_ADD"); print $pgv_lang["SOUR_FACTS_ADD"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_SOUR_FACTS_ADD" value="<?php print $SOUR_FACTS_ADD; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SOUR_FACTS_ADD_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SOUR_FACTS_UNIQUE_help", "qm", "SOUR_FACTS_UNIQUE"); print $pgv_lang["SOUR_FACTS_UNIQUE"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_SOUR_FACTS_UNIQUE" value="<?php print $SOUR_FACTS_UNIQUE; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SOUR_FACTS_UNIQUE_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SOUR_FACTS_QUICK_help", "qm", "SOUR_FACTS_QUICK"); print $pgv_lang["SOUR_FACTS_QUICK"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_SOUR_FACTS_QUICK" value="<?php print $SOUR_FACTS_QUICK; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SOUR_FACTS_QUICK_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("REPO_FACTS_ADD_help", "qm", "REPO_FACTS_ADD"); print $pgv_lang["REPO_FACTS_ADD"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_REPO_FACTS_ADD" value="<?php print $REPO_FACTS_ADD; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('REPO_FACTS_ADD_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("REPO_FACTS_UNIQUE_help", "qm", "REPO_FACTS_UNIQUE"); print $pgv_lang["REPO_FACTS_UNIQUE"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_REPO_FACTS_UNIQUE" value="<?php print $REPO_FACTS_UNIQUE; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('REPO_FACTS_UNIQUE_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("REPO_FACTS_QUICK_help", "qm", "REPO_FACTS_QUICK"); print $pgv_lang["REPO_FACTS_QUICK"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_REPO_FACTS_QUICK" value="<?php print $REPO_FACTS_QUICK; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('REPO_FACTS_QUICK_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("EDIT_AUTOCLOSE_help", "qm", "EDIT_AUTOCLOSE"); print $pgv_lang["EDIT_AUTOCLOSE"]; ?></td>
		<td class="optionbox"><select name="NEW_EDIT_AUTOCLOSE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('EDIT_AUTOCLOSE_help');">
				<option value="yes" <?php if ($EDIT_AUTOCLOSE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$EDIT_AUTOCLOSE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SPLIT_PLACES_help", "qm", "SPLIT_PLACES"); print $pgv_lang["SPLIT_PLACES"]; ?></td>
		<td class="optionbox"><select name="NEW_SPLIT_PLACES" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SPLIT_PLACES_help');">
				<option value="yes" <?php if ($SPLIT_PLACES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SPLIT_PLACES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("USE_QUICK_UPDATE_help", "qm", "USE_QUICK_UPDATE", true); print print_text("USE_QUICK_UPDATE"); ?></td>
		<td class="optionbox"><select name="NEW_USE_QUICK_UPDATE" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('USE_QUICK_UPDATE_help');">
				<option value="yes" <?php if ($USE_QUICK_UPDATE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$USE_QUICK_UPDATE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_QUICK_RESN_help", "qm", "SHOW_QUICK_RESN", true); print print_text("SHOW_QUICK_RESN"); ?></td>
		<td class="optionbox"><select name="NEW_SHOW_QUICK_RESN" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_QUICK_RESN_help');">
				<option value="yes" <?php if ($SHOW_QUICK_RESN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_QUICK_RESN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("QUICK_ADD_FACTS_help", "qm", "QUICK_ADD_FACTS"); print $pgv_lang["QUICK_ADD_FACTS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_QUICK_ADD_FACTS" value="<?php print $QUICK_ADD_FACTS; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('QUICK_ADD_FACTS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("QUICK_REQUIRED_FACTS_help", "qm", "QUICK_REQUIRED_FACTS"); print $pgv_lang["QUICK_REQUIRED_FACTS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_QUICK_REQUIRED_FACTS" value="<?php print $QUICK_REQUIRED_FACTS; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('QUICK_REQUIRED_FACTS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("QUICK_ADD_FAMFACTS_help", "qm", "QUICK_ADD_FAMFACTS"); print $pgv_lang["QUICK_ADD_FAMFACTS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_QUICK_ADD_FAMFACTS" value="<?php print $QUICK_ADD_FAMFACTS; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('QUICK_ADD_FAMFACTS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("QUICK_REQUIRED_FAMFACTS_help", "qm", "QUICK_REQUIRED_FAMFACTS"); print $pgv_lang["QUICK_REQUIRED_FAMFACTS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_QUICK_REQUIRED_FAMFACTS" value="<?php print $QUICK_REQUIRED_FAMFACTS; ?>" size="40" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('QUICK_REQUIRED_FAMFACTS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SURNAME_TRADITION_help", "qm", "SURNAME_TRADITION"); print $pgv_lang["SURNAME_TRADITION"]; ?></td>
		<td class="optionbox"><select name="NEW_SURNAME_TRADITION" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SURNAME_TRADITION_help');">
			<?php
				foreach (array('paternal', 'spanish', 'portuguese', 'icelandic', 'none') as $value) {
					print '<option value="'.$value.'"';
					if ($SURNAME_TRADITION==$value) print ' selected="selected"';
					print '>'.$pgv_lang[$value].'</option>';
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ADVANCED_NAME_FACTS_help", "qm", "ADVANCED_NAME_FACTS"); print $pgv_lang["ADVANCED_NAME_FACTS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_ADVANCED_NAME_FACTS" value="<?php print $ADVANCED_NAME_FACTS; ?>" size="40" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ADVANCED_NAME_FACTS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ADVANCED_PLAC_FACTS_help", "qm", "ADVANCED_PLAC_FACTS"); print $pgv_lang["ADVANCED_PLAC_FACTS"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_ADVANCED_PLAC_FACTS" value="<?php print $ADVANCED_PLAC_FACTS; ?>" size="40" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ADVANCED_PLAC_FACTS_help');" /></td>
	</tr>
</table>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<?php // User Options
?>
<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["useropt_conf"]."\" onclick=\"expand_layer('user-options');return false\"><img id=\"user-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["useropt_conf"]."\" onclick=\"expand_layer('user-options');return false\">".$pgv_lang["useropt_conf"]."</a>";
?></td></tr></table>
<div id="user-options" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ENABLE_MULTI_LANGUAGE_help", "qm", "ENABLE_MULTI_LANGUAGE"); print $pgv_lang["ENABLE_MULTI_LANGUAGE"]; ?></td>
		<td class="optionbox"><select name="NEW_ENABLE_MULTI_LANGUAGE" onfocus="getHelp('ENABLE_MULTI_LANGUAGE_help');" tabindex="<?php $i++; print $i; ?>" >
				<option value="yes" <?php if ($ENABLE_MULTI_LANGUAGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$ENABLE_MULTI_LANGUAGE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
			<?php
				if (!file_exists($INDEX_DIRECTORY . "lang_settings.php")) {
					print "<br /><span class=\"error\">";
					print $pgv_lang["LANGUAGE_DEFAULT"];
					print "</span>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SHOW_CONTEXT_HELP_help", "qm", "SHOW_CONTEXT_HELP"); print $pgv_lang["SHOW_CONTEXT_HELP"]; ?></td>
		<td class="optionbox"><select name="NEW_SHOW_CONTEXT_HELP" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SHOW_CONTEXT_HELP_help');">
				<option value="yes" <?php if ($SHOW_CONTEXT_HELP) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$SHOW_CONTEXT_HELP) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("THEME_DIR_help", "qm", "THEME_DIR"); print $pgv_lang["THEME_DIR"]; ?></td>
		<td class="optionbox">
			<select name="themeselect" dir="ltr" tabindex="<?php $i++; print $i; ?>"  onchange="document.configform.NTHEME_DIR.value=document.configform.themeselect.options[document.configform.themeselect.selectedIndex].value;">
				<?php
					$themes = get_theme_names();
					foreach ($themes as $indexval => $themedir) {
						print "<option value=\"".$themedir["dir"]."\"";
						if ($themedir["dir"] == $NTHEME_DIR) print " selected=\"selected\"";
						print ">".$themedir["name"]."</option>\n";
					}
				?>
				<option value="themes/" <?php if ($themeselect=="themes//") print "selected=\"selected\""; ?>><?php print $pgv_lang["other_theme"]; ?></option>
			</select>
			<input type="text" name="NTHEME_DIR" value="<?php print $NTHEME_DIR; ?>" size="40" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('THEME_DIR_help');" />
	<?php
	if (!file_exists($NTHEME_DIR)) {
		print "<span class=\"error\">$NTHEME_DIR ";
		print $pgv_lang["does_not_exist"];
		print "</span>\n";
		$NTHEME_DIR=$THEME_DIR;
	}
	?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ALLOW_THEME_DROPDOWN_help", "qm", "ALLOW_THEME_DROPDOWN"); print $pgv_lang["ALLOW_THEME_DROPDOWN"]; ?></td>
		<td class="optionbox"><select name="NEW_ALLOW_THEME_DROPDOWN" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ALLOW_THEME_DROPDOWN_help');">
				<option value="yes" <?php if ($ALLOW_THEME_DROPDOWN) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$ALLOW_THEME_DROPDOWN) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
</table>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["contact_conf"]."\" onclick=\"expand_layer('contact-options');return false\"><img id=\"contact-options_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["contact_conf"]."\" onclick=\"expand_layer('contact-options');return false\">".$pgv_lang["contact_conf"]."</a>";
?></td></tr></table>
<div id="contact-options" style="display: none">
<table class="facts_table">
	<tr>
		<?php
		if (empty($PHPGEDVIEW_EMAIL)) {
			$PHPGEDVIEW_EMAIL = "phpgedview-noreply@".preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
		}
		?>
		<td class="descriptionbox wrap width20"><?php print_help_link("PHPGEDVIEW_EMAIL_help", "qm", "PHPGEDVIEW_EMAIL"); print $pgv_lang["PHPGEDVIEW_EMAIL"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_PHPGEDVIEW_EMAIL" value="<?php print $PHPGEDVIEW_EMAIL; ?>" size="80" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('PHPGEDVIEW_EMAIL_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("CONTACT_EMAIL_help", "qm", "CONTACT_EMAIL"); print $pgv_lang["CONTACT_EMAIL"]; ?></td>
		<td class="optionbox"><select name="NEW_CONTACT_EMAIL" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('CONTACT_EMAIL_help');">
		<?php
			if ($CONTACT_EMAIL=="you@yourdomain.com") $CONTACT_EMAIL = PGV_USER_NAME;
			foreach (get_all_users() as $user_id=>$user_name) {
				if (get_user_setting($user_id, 'verified_by_admin')=="yes") {
					print "<option value=\"".$user_id."\"";
					if ($CONTACT_EMAIL==$user_name) print " selected=\"selected\"";
					print ">".getUserFullName($user_id)." - ".$user_name."</option>\n";
				}
			}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("CONTACT_METHOD_help", "qm", "CONTACT_METHOD"); print $pgv_lang["CONTACT_METHOD"]; ?></td>
		<td class="optionbox"><select name="NEW_CONTACT_METHOD" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('CONTACT_METHOD_help');">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging" <?php if ($CONTACT_METHOD=='messaging') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging"]; ?></option>
				<option value="messaging2" <?php if ($CONTACT_METHOD=='messaging2') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging2"]; ?></option>
		<?php } else { ?>
				<option value="messaging3" <?php if ($CONTACT_METHOD=='messaging3') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging3"]; ?></option>
		<?php } ?>
				<option value="mailto" <?php if ($CONTACT_METHOD=='mailto') print "selected=\"selected\""; ?>><?php print $pgv_lang["mailto"]; ?></option>
				<option value="none" <?php if ($CONTACT_METHOD=='none') print "selected=\"selected\""; ?>><?php print $pgv_lang["no_messaging"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("WEBMASTER_EMAIL_help", "qm", "WEBMASTER_EMAIL"); print $pgv_lang["WEBMASTER_EMAIL"]; ?></td>
		<td class="optionbox"><select name="NEW_WEBMASTER_EMAIL" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('WEBMASTER_EMAIL_help');">
		<?php
			if ($WEBMASTER_EMAIL=="webmaster@yourdomain.com") $WEBMASTER_EMAIL = PGV_USER_NAME;
			foreach (get_all_users() as $user_id=>$user_name) {
				if (userIsAdmin($user_id)) {
					print "<option value=\"".$user_id."\"";
					if ($WEBMASTER_EMAIL==$user_name) print " selected=\"selected\"";
					print ">".getUserFullName($user_id)." - ".$user_name."</option>\n";
				}
			}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("SUPPORT_METHOD_help", "qm", "SUPPORT_METHOD"); print $pgv_lang["SUPPORT_METHOD"]; ?></td>
		<td class="optionbox"><select name="NEW_SUPPORT_METHOD" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('SUPPORT_METHOD_help');">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging" <?php if ($SUPPORT_METHOD=='messaging') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging"]; ?></option>
				<option value="messaging2" <?php if ($SUPPORT_METHOD=='messaging2') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging2"]; ?></option>
		<?php } else { ?>
				<option value="messaging3" <?php if ($SUPPORT_METHOD=='messaging3') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging3"]; ?></option>
		<?php } ?>
				<option value="mailto" <?php if ($SUPPORT_METHOD=='mailto') print "selected=\"selected\""; ?>><?php print $pgv_lang["mailto"]; ?></option>
				<option value="none" <?php if ($SUPPORT_METHOD=='none') print "selected=\"selected\""; ?>><?php print $pgv_lang["no_messaging"]; ?></option>
			</select>
		</td>
	</tr>
</table>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</div>

<table class="facts_table"><tr><td class="topbottombar <?php print $TEXT_DIRECTION; ?>">
<?php
print "<a href=\"javascript: ".$pgv_lang["meta_conf"]."\" onclick=\"expand_layer('config-meta');return false\"><img id=\"config-meta_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
print "&nbsp;<a href=\"javascript: ".$pgv_lang["meta_conf"]."\" onclick=\"expand_layer('config-meta');return false\">".$pgv_lang["meta_conf"]."</a>";
?></td></tr></table>
<div id="config-meta" style="display: none">
<table class="facts_table">
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("HOME_SITE_URL_help", "qm", "HOME_SITE_URL"); print $pgv_lang["HOME_SITE_URL"]; ?></td>
		<td class="optionbox"><input type="text" name="NEW_HOME_SITE_URL" value="<?php print $HOME_SITE_URL; ?>" size="50" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('HOME_SITE_URL_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("HOME_SITE_TEXT_help", "qm", "HOME_SITE_TEXT"); print $pgv_lang["HOME_SITE_TEXT"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_HOME_SITE_TEXT" value="<?php print htmlspecialchars($HOME_SITE_TEXT); ?>" size="50" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('HOME_SITE_TEXT_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_AUTHOR_help", "qm", "META_AUTHOR"); print $pgv_lang["META_AUTHOR"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_AUTHOR" value="<?php print $META_AUTHOR; ?>" onfocus="getHelp('META_AUTHOR_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		<?php print print_text("META_AUTHOR_descr"); ?></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_PUBLISHER_help", "qm", "META_PUBLISHER"); print $pgv_lang["META_PUBLISHER"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_PUBLISHER" value="<?php print $META_PUBLISHER; ?>" onfocus="getHelp('META_PUBLISHER_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		<?php print print_text("META_PUBLISHER_descr"); ?></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_COPYRIGHT_help", "qm", "META_COPYRIGHT"); print $pgv_lang["META_COPYRIGHT"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_COPYRIGHT" value="<?php print $META_COPYRIGHT; ?>" onfocus="getHelp('META_COPYRIGHT_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		<?php print print_text("META_COPYRIGHT_descr"); ?></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_DESCRIPTION_help", "qm", "META_DESCRIPTION"); print $pgv_lang["META_DESCRIPTION"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_DESCRIPTION" value="<?php print $META_DESCRIPTION; ?>" onfocus="getHelp('META_DESCRIPTION_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		<?php print $pgv_lang["META_DESCRIPTION_descr"]; ?></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_PAGE_TOPIC_help", "qm", "META_PAGE_TOPIC"); print $pgv_lang["META_PAGE_TOPIC"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_PAGE_TOPIC" value="<?php print $META_PAGE_TOPIC; ?>" onfocus="getHelp('META_PAGE_TOPIC_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		<?php print $pgv_lang["META_PAGE_TOPIC_descr"]; ?></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_AUDIENCE_help", "qm", "META_AUDIENCE"); print $pgv_lang["META_AUDIENCE"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_AUDIENCE" value="<?php print $META_AUDIENCE; ?>" onfocus="getHelp('META_AUDIENCE_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_PAGE_TYPE_help", "qm", "META_PAGE_TYPE"); print $pgv_lang["META_PAGE_TYPE"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_PAGE_TYPE" value="<?php print $META_PAGE_TYPE; ?>" onfocus="getHelp('META_PAGE_TYPE_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_ROBOTS_help", "qm", "META_ROBOTS"); print $pgv_lang["META_ROBOTS"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_ROBOTS" value="<?php print $META_ROBOTS; ?>" onfocus="getHelp('META_ROBOTS_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_REVISIT_help", "qm", "META_REVISIT"); print $pgv_lang["META_REVISIT"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_REVISIT" value="<?php print $META_REVISIT; ?>" onfocus="getHelp('META_REVISIT_help');" tabindex="<?php $i++; print $i; ?>" /><br />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_KEYWORDS_help", "qm", "META_KEYWORDS"); print $pgv_lang["META_KEYWORDS"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_KEYWORDS" value="<?php print $META_KEYWORDS; ?>" onfocus="getHelp('META_KEYWORDS_help');" tabindex="<?php $i++; print $i; ?>" size="75" /><br />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_SURNAME_KEYWORDS_help", "qm", "META_SURNAME_KEYWORDS"); print $pgv_lang["META_SURNAME_KEYWORDS"]; ?></td>
		<td class="optionbox"><select name="NEW_META_SURNAME_KEYWORDS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('META_SURNAME_KEYWORDS_help');">
				<option value="yes" <?php if ($META_SURNAME_KEYWORDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$META_SURNAME_KEYWORDS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("META_TITLE_help", "qm", "META_TITLE"); print $pgv_lang["META_TITLE"]; ?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_META_TITLE" value="<?php print $META_TITLE; ?>" onfocus="getHelp('META_TITLE_help');" tabindex="<?php $i++; print $i; ?>" size="75" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("ENABLE_RSS_help", "qm", "ENABLE_RSS"); print $pgv_lang["ENABLE_RSS"]; ?></td>
		<td class="optionbox"><select name="NEW_ENABLE_RSS" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('ENABLE_RSS_help');">
				<option value="yes" <?php if ($ENABLE_RSS) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"]; ?></option>
				<option value="no" <?php if (!$ENABLE_RSS) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"]; ?></option>
			</select>
		</td>
	</tr>


	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link("RSS_FORMAT_help", "qm", "RSS_FORMAT"); print $pgv_lang["RSS_FORMAT"]; ?></td>
		<td class="optionbox"><select name="NEW_RSS_FORMAT" dir="ltr" tabindex="<?php $i++; print $i; ?>" onfocus="getHelp('RSS_FORMAT_help');">
				<option value="ATOM" <?php if ($RSS_FORMAT=="ATOM") print "selected=\"selected\""; ?>>ATOM 1.0</option>
				<!--option value="ATOM0.3" <?php if ($RSS_FORMAT=="ATOM0.3") print "selected=\"selected\""; ?>>ATOM 0.3</option-->
				<option value="RSS2.0" <?php if ($RSS_FORMAT=="RSS2.0") print "selected=\"selected\""; ?>>RSS 2.0</option>
				<!--option value="RSS0.91" <?php if ($RSS_FORMAT=="RSS0.91") print "selected=\"selected\""; ?>>RSS 0.91</option-->
				<option value="RSS1.0" <?php if ($RSS_FORMAT=="RSS1.0") print "selected=\"selected\""; ?>>RSS 1.0</option>
			</select>
		</td>
	</tr>
<?php } ?>
</table>
</div>
<table class="facts_table" border="0">
<tr><td style="padding: 5px" class="topbottombar">
<input type="submit" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["save_config"]; ?>" onclick="closeHelp();" />
&nbsp;&nbsp;
<input type="reset" tabindex="<?php $i++; print $i; ?>" value="<?php print $pgv_lang["reset"]; ?>" /><br />
</td></tr>
</table>
</form>
<br /><!--<?php if (isset($FILE) && !check_for_import($FILE)) print_text("return_editconfig_gedcom"); ?><br />-->
<?php if (count($gGedcom)==0) { ?>
<script language="JavaScript" type="text/javascript">
	helpPopup('welcome_new_help');
</script>
<?php
}

// NOTE: Put the focus on the GEDCOM title field since the GEDCOM path actually
// NOTE: needs no changing
?>
<script language="JavaScript" type="text/javascript">
	<?php if ($source == "") print "document.configform.gedcom_title.focus();";
	else print "document.configform.GEDCOMPATH.focus();"; ?>
</script>
<?php
print_footer();
?>

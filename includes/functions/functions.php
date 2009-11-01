<?php
/**
 * Core Functions that can be used by any page in PGV
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
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
 * @package PhpGedView
 * @version $Id: functions.php,v 1.5 2009/11/01 20:58:30 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_mutex.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_media.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_UTF8.php');


////////////////////////////////////////////////////////////////////////////////
// Extract, sanitise and validate FORM (POST), URL (GET) and COOKIE variables.
//
// Request variables should ALWAYS be accessed through these functions, to
// protect against XSS (cross-site-scripting) attacks.
//
// $var     - The variable to check
// $regex   - Regular expression to validate the variable (or an array of
//            regular expressions).  A number of common regexes are defined in
//            session.php as constants PGV_REGEX_*.  If no value is specified,
//            the default blocks all characters that could introduce scripts.
// $default - A value to use if $var is undefined or invalid.
//
// You should always know whether your variables are coming from GET or POST,
// and always use the correct function.
//
// NOTE: when using checkboxes, $var is either set (checked) or unset (not
// checked).  This lets us use the syntax safe_GET('my_checkbox', 'yes', 'no')
//
// NOTE: when using listboxes, $regex can be an array of valid values.  For
// example, you can use safe_POST('lang', array_keys($pgv_language), $LANGUAGE)
// to validate against a list of valid languages and supply a sensible default.
////////////////////////////////////////////////////////////////////////////////

function safe_POST($var, $regex=PGV_REGEX_NOSCRIPT, $default=null) {
	return safe_REQUEST($_POST, $var, $regex, $default);
}
function safe_GET($var, $regex=PGV_REGEX_NOSCRIPT, $default=null) {
	return safe_REQUEST($_GET, $var, $regex, $default);
}
function safe_COOKIE($var, $regex=PGV_REGEX_NOSCRIPT, $default=null) {
	return safe_REQUEST($_COOKIE, $var, $regex, $default);
}

function safe_GET_integer($var, $min, $max, $default) {
	$num=safe_GET($var, PGV_REGEX_INTEGER, $default);
	$num=max($num, $min);
	$num=min($num, $max);
	return (int)$num;
}
function safe_POST_integer($var, $min, $max, $default) {
	$num=safe_POST($var, PGV_REGEX_INTEGER, $default);
	$num=max($num, $min);
	$num=min($num, $max);
	return (int)$num;
}

function safe_GET_bool($var, $true='(y|Y|1|yes|YES|Yes|true|TRUE|True|on)') {
	return !is_null(safe_GET($var, $true));
}
function safe_POST_bool($var, $true='(y|Y|1|yes|YES|Yes|true|TRUE|True|on)') {
	return !is_null(safe_POST($var, $true));
}

function safe_GET_xref($var, $default=null) {
	return safe_GET($var, PGV_REGEX_XREF, $default);
}
function safe_POST_xref($var, $default=null) {
	return safe_POST($var, PGV_REGEX_XREF, $default);
}

function safe_REQUEST($arr, $var, $regex=PGV_REGEX_NOSCRIPT, $default=null) {
	if (is_array($regex)) {
		$regex='(?:'.join('|', $regex).')';
	}
	if (array_key_exists($var, $arr) && preg_match_recursive('~^'.addcslashes($regex,'~').'$~', $arr[$var])) {
		return trim_recursive($arr[$var]);
	} else {
		return $default;
	}
}

function encode_url($url, $entities=true) {
	$url = decode_url($url, $entities); // Make sure we don't do any double conversions
	$url = str_replace(array(' ', '+', '@#', '"', "'"), array('%20', '%2b', '@%23', '%22', '%27'), $url);
	if ($entities) {
		$url = htmlspecialchars($url,ENT_COMPAT,'UTF-8');
	}
	return $url;
}

function decode_url($url, $entities=true) {
	if ($entities) {
		$url = html_entity_decode($url,ENT_COMPAT,'UTF-8');
	}
	$url = rawurldecode($url); // GEDCOM names can legitimately contain " " and "+"
	return $url;
}

function preg_match_recursive($regex, $var) {
	if (is_scalar($var)) {
		return preg_match($regex, $var);
	} else {
		if (is_array($var)) {
			foreach ($var as $k=>$v) {
				if (!preg_match_recursive($regex, $v)) {
					return false;
				}
			}
			return true;
		} else {
			// Neither scalar nor array.  Object?
			return false;
		}
	}
}

function trim_recursive($var) {
	if (is_scalar($var)) {
		return trim($var);
	} else {
		if (is_array($var)) {
			foreach ($var as $k=>$v) {
				$var[$k]=trim_recursive($v);
			}
			return $var;
		} else {
			// Neither scalar nor array.  Object?
			return $var;
		}
	}
}

// Update the variable definitions in a PHP config file, such as config.php
function update_config(&$text, $var, $value) {
	// NULL values probably wouldn't hurt, but empty strings are probably better
	if (is_null($value)) {
		$value='';
	}
	$regex='/^[ \t]*[$]'.$var.'[ \t]*=.*;[ \t]*/m';
	$assign='$'.$var.'='.var_export($value, true).'; ';
	if (preg_match($regex, $text)) {
		// Variable found in file - update it
		$text=preg_replace($regex, $assign, $text);
	} else {
		// Variable not found in file - insert it
		$text=preg_replace('/^(.*[\r\n]+)([ \t]*[$].*)$/s', '$1'.$assign." // new config variable\n".'$2',$text);
	}
}

// Convert a file upload PHP error code into user-friendly text
function file_upload_error_text($error_code) {
	global $pgv_lang;

	switch ($error_code) {
	case UPLOAD_ERR_OK:
		return $pgv_lang['file_success'];
	case UPLOAD_ERR_INI_SIZE:
	case UPLOAD_ERR_FORM_SIZE:
		return $pgv_lang['file_too_big'];
	case UPLOAD_ERR_PARTIAL:
		return $pgv_lang['file_partial'];
	case UPLOAD_ERR_NO_FILE:
		return $pgv_lang['file_missing'];
	case UPLOAD_ERR_NO_TMP_DIR:
		return $pgv_lang['file_no_temp_dir'];
	case UPLOAD_ERR_CANT_WRITE:
		return $pgv_lang['file_cant_write'];
	case UPLOAD_ERR_EXTENSION:
		return $pgv_lang['file_bad_extension'];
	default:
		$pgv_lang['global_num1'] = $error_code; // Make this available to print_text()
		return print_text('file_unknown_err', 0, 1);
	}
}

/**
 * get gedcom configuration file
 *
 * this function returns the path to the currently active GEDCOM configuration file
 * @return string path to gedcom.ged_conf.php configuration file
 */
function get_config_file($ged="") {
	global $GEDCOMS, $GEDCOM;

	if (empty($ged)) {
		$ged = $GEDCOM;
	}
	$config = "config_gedcom.php";
	if (count($GEDCOMS)==0) {
		return $config;
	}
	if (!empty($GEDCOM) && isset($GEDCOMS[$GEDCOM])) {
		$config = $GEDCOMS[$GEDCOM]["config"];
	} else {
		foreach ($GEDCOMS as $GEDCOM=>$gedarray) {
			$_SESSION["GEDCOM"] = $GEDCOM;
			$config = $gedarray["config"];
			break;
		}
	}
	if (!file_exists($config)) {
		$config='config_gedcom.php';
	}
	return $config;
}

/**
 * print write_access option
 *
 * @param string $checkVar
 */
function write_access_option($checkVar) {
	global $pgv_lang;

	echo "<option value=\"PGV_PRIV_PUBLIC\"";
	echo ($checkVar==PGV_PRIV_PUBLIC) ? " selected=\"selected\"" : '';
	echo ">".$pgv_lang["PRIV_PUBLIC"]."</option>\n";

	echo "<option value=\"PGV_PRIV_USER\"";
	echo ($checkVar==PGV_PRIV_USER) ? " selected=\"selected\"" : '';
	echo ">".$pgv_lang["PRIV_USER"]."</option>\n";

	echo "<option value=\"PGV_PRIV_NONE\"";
	echo ($checkVar==PGV_PRIV_NONE) ? " selected=\"selected\"" : '';
	echo ">".$pgv_lang["PRIV_NONE"]."</option>\n";

	echo "<option value=\"PGV_PRIV_HIDE\"";
	echo ($checkVar==PGV_PRIV_HIDE) ? " selected=\"selected\"" : '';
	echo ">".$pgv_lang["PRIV_HIDE"]."</option>\n";
}

/**
 * Get the version of the privacy file
 *
 * This function opens the given privacy file and returns the privacy version from the file
 * @param string $privfile the path to the privacy file
 * @return string the privacy file version number
 */
function get_privacy_file_version($privfile) {
	$privversion = "0";

	//-- check to make sure that the privacy file is the current version
	if (file_exists($privfile)) {
		$privcontents = implode("", file($privfile));
		$ct = preg_match("/PRIVACY_VERSION.*=.*\"(.+)\"/", $privcontents, $match);
		if ($ct>0) {
			$privversion = trim($match[1]);
		}
	}

	return $privversion;
}

/**
 * Get the path to the privacy file
 *
 * Get the path to the privacy file for the currently active GEDCOM
 * @return string path to the privacy file
 */
function get_privacy_file() {
	global $GEDCOMS, $GEDCOM;

	$privfile = "privacy.php";
	if ( count($GEDCOMS)==0 ) {
		$privfile = "privacy.php";
	}
	if ( !empty($GEDCOM) && isset($GEDCOMS[$GEDCOM])) {
		if (isset($GEDCOMS[$GEDCOM]["privacy"]) && file_exists($GEDCOMS[$GEDCOM]["privacy"])) {
			$privfile = $GEDCOMS[$GEDCOM]["privacy"];
		} else {
			$privfile = "privacy.php";
		}
	} else if ( !empty($GEDCOMS) ) {
		foreach ($GEDCOMS as $GEDCOM=>$gedarray) {
			$_SESSION["GEDCOM"] = $GEDCOM;
			if (isset($gedarray["privacy"]) && file_exists($gedarray["privacy"])) {
				$privfile = $gedarray["privacy"];
			} else {
				$privfile = "privacy.php";
			}
		}
	}
	if (version_compare(get_privacy_file_version($privfile), PGV_REQUIRED_PRIVACY_VERSION)<0) {
		$privfile = "privacy.php";
	}

	return $privfile;
}

function load_privacy_file($ged_id=PGV_GED_ID) {
	global $PRIV_HIDE, $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE;

	// Load the privacy settings into global scope
	global $SHOW_DEAD_PEOPLE, $SHOW_LIVING_NAMES, $SHOW_SOURCES, $MAX_ALIVE_AGE;
	global $SHOW_RESEARCH_ASSISTANT, $ENABLE_CLIPPINGS_CART, $SHOW_MULTISITE_SEARCH;
	global $USE_RELATIONSHIP_PRIVACY, $MAX_RELATION_PATH_LENGTH, $CHECK_MARRIAGE_RELATIONS;
	global $PRIVACY_BY_YEAR, $PRIVACY_BY_RESN, $SHOW_PRIVATE_RELATIONSHIPS;
	global $person_privacy, $user_privacy, $global_facts, $person_facts;

	// Load default settings
	require '../../privacy.php';

	// Load settings for the specified gedcom
	$privacy_file=get_gedcom_setting($ged_id, 'privacy');
	if (
		$privacy_file &&
		file_exists($privacy_file) &&
		version_compare(get_privacy_file_version($privacy_file), PGV_REQUIRED_PRIVACY_VERSION)>=0
	) {
		require $privacy_file;
	}
}

/**
 * Store GEDCOMS array
 *
 * this function will store the <var>$GEDCOMS</var> array in the <var>$INDEX_DIRECTORY</var>/gedcoms.php
 * file.  The gedcoms.php file is included in session.php to create the <var>$GEDCOMS</var>
 * array with every page request.
 * @see session.php
 */
function store_gedcoms() {
	global $GEDCOMS, $pgv_lang, $INDEX_DIRECTORY, $COMMON_NAMES_THRESHOLD, $GEDCOM, $CONFIGURED;
	global $IN_STORE_GEDCOMS;

	if (!$CONFIGURED) {
		return false;
	}
	//-- do not allow recursion into this function
	if (isset($IN_STORE_GEDCOMS) && $IN_STORE_GEDCOMS==true) {
		return false;
	}
	$IN_STORE_GEDCOMS = true;
//	$mutex = new Mutex("gedcoms.php");
//	$mutex->Wait();
	uasort($GEDCOMS, "gedcomsort");
	$gedcomtext = "<?php\n//--START GEDCOM CONFIGURATIONS\n";
	$gedcomtext .= "\$GEDCOMS = array();\n";
	$maxid = 0;
	foreach ($GEDCOMS as $name => $details) {
		if (isset($details["id"]) && $details["id"] > $maxid) {
			$maxid = $details["id"];
		}
	}
	if ($maxid !=0) {
		$maxid++;
	}
	reset($GEDCOMS);
	//-- keep a local copy in case another function tries to change $GEDCOMS
	$geds = $GEDCOMS;
	foreach ($geds as $indexval => $GED) {
		$GED["config"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["config"]);
		if (isset($GED["privacy"])) {
			$GED["privacy"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["privacy"]);
		} else {
			$GED["privacy"] = "privacy.php";
		}
		$GED["path"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["path"]);
		$GED["title"] = stripslashes($GED["title"]);
		$GED["title"] = preg_replace("/\"/", "\\\"", $GED["title"]);
		$gedcomtext .= "\$gedarray = array();\n";
		$gedcomtext .= "\$gedarray[\"gedcom\"] = \"".$GED["gedcom"]."\";\n";
		$gedcomtext .= "\$gedarray[\"config\"] = \"".$GED["config"]."\";\n";
		$gedcomtext .= "\$gedarray[\"privacy\"] = \"".$GED["privacy"]."\";\n";
		$gedcomtext .= "\$gedarray[\"title\"] = \"".$GED["title"]."\";\n";
		$gedcomtext .= "\$gedarray[\"path\"] = \"".$GED["path"]."\";\n";
		$gedcomtext .= "\$gedarray[\"pgv_ver\"] = \"".$GED["pgv_ver"]."\";\n";
		if (isset($GED["imported"])) {
			$gedcomtext .= "\$gedarray[\"imported\"] = ".($GED["imported"]==false?'false':'true').";\n";
		}
		// TODO: Commonsurnames from an old gedcom are used
		// TODO: Default GEDCOM is changed to last uploaded GEDCOM

		// NOTE: Set the GEDCOM ID
		if (!isset($GED["id"]) && $maxid == 0) {
			$GED["id"] = 1;
		} elseif (!isset($GED["id"]) && $maxid > 0) {
			$GED["id"] = $maxid;
		} elseif (empty($GED["id"])) {
			$GED["id"] = $maxid;
		}

		$gedcomtext .= "\$gedarray[\"id\"] = \"".$GED["id"]."\";\n";
		if (empty($GED["commonsurnames"])) {
			if ($GED["gedcom"]==$GEDCOM) {
				$GED["commonsurnames"] = "";
				$surnames = get_common_surnames($COMMON_NAMES_THRESHOLD);
				foreach ($surnames as $indexval => $surname) {
					$GED["commonsurnames"] .= $surname["name"].", ";
				}
			} else
				$GED["commonsurnames"]="";
		}
		$geds[$GED["gedcom"]]["commonsurnames"] = $GED["commonsurnames"];
		$gedcomtext .= "\$gedarray[\"commonsurnames\"] = \"".addslashes($GED["commonsurnames"])."\";\n";
		$gedcomtext .= "\$GEDCOMS[\"".$GED["gedcom"]."\"] = \$gedarray;\n";
	}
	$GEDCOMS = $geds;
	$gedcomtext .= "\n?".">";
	$fp = @fopen($INDEX_DIRECTORY."gedcoms.php", "wb");
	if (!$fp) {
		global $whichFile;
		$whichFile = $INDEX_DIRECTORY."gedcoms.php";
		print "<span class=\"error\">".print_text("gedcom_config_write_error",0,1)."<br /></span>\n";
	} else {
		fwrite($fp, $gedcomtext);
		fclose($fp);
		check_in("store_gedcoms() ->" . getUserName() ."<-", "gedcoms.php", $INDEX_DIRECTORY, true);
	}
//	$mutex->Release();
	$IN_STORE_GEDCOMS = false;
	return true;
}

/**
 * Update the site configuration settings
 * New settings are passed in as an array of key value pairs
 * The key in the array should be the name of the setting to change
 * the value should be the new value
 * $newconfig['CONFIGURED'] = true;
 *
 * @param array	$newconfig
 * @param boolean $return	return the text or try to write the file
 * @return mixed	returns true on success, or returns an array of error messages on failure
 */
function update_site_config($newconfig, $return = false) {
	global $pgv_lang, $COMMIT_COMMAND;

	$errors = array();

	//-- load the configuration file text
	if (file_exists("config.php")) {
		$configtext = file_get_contents("config.php");
	} else {
		$configtext = file_get_contents("config.dist");
	}

	foreach($newconfig as $setting=>$value) {
		update_config($configtext, $setting, $value);
	}

	//-- check if the configtext is valid PHP
	$res = @eval($configtext);
	if ($res===false) {
		if ($return) {
			return $configtext;
		}

		$fp = @fopen("config.php", "wb");
		if (!$fp) {
			$error['msg'] = $pgv_lang["pgv_config_write_error"];
			$errors[] = $error;
		} else {
			fwrite($fp, $configtext);
			fclose($fp);
			$logline = AddToLog("config.php updated by >".getUserName()."<");
			if (!empty($COMMIT_COMMAND)) {
				check_in($logline, "config.php", "");
			}
		}
	} else {
		$error['msg'] = "There was an error in the generated config.php. ".htmlentities($configtext);
		$errors[] = $error;
	}

	if (count($errors)>0) {
		return $errors;
	}
	return true;
}

// Save the languages the user has chosen to have active on the website
function update_lang_settings() {
	global $INDEX_DIRECTORY, $language_settings, $languages, $pgv_language, $lang_short_cut, $pgv_lang_self, $pgv_lang_use, $confighelpfile, $helptextfile, $factsfile;
	global $flagsfile, $adminfile, $countryfile, $faqlistfile, $extrafile, $ALPHABET_lower, $ALPHABET_upper, $DATE_FORMAT_array, $editorfile, $lang_langcode;
	global $DICTIONARY_SORT, $MULTI_LETTER_ALPHABET, $MULTI_LETTER_EQUIV, $NAME_REVERSE_array, $TEXT_DIRECTION_array, $TIME_FORMAT_array, $WEEK_START_array, $COLLATION;

	$Filename = $INDEX_DIRECTORY . "lang_settings.php";
	if (!file_exists($Filename)) {
		copy("includes/lang_settings_std.php", $Filename);
	}

	$error = "";
	if ($file_array = file($Filename)) {
		@copy($Filename, $Filename . ".old");
		if ($fp = @fopen($Filename, "w")) {
			for ($x = 0; $x < count($file_array); $x++) {
				fwrite($fp, $file_array[$x]);
				$dDummy00 = trim($file_array[$x]);
				if ($dDummy00 == "//-- NEVER manually delete or edit this entry and every line below this entry! --START--//") {
					break;
				}
			}
			fwrite($fp, PGV_EOL);
			fwrite($fp, "// Array definition of language_settings".PGV_EOL);
			fwrite($fp, "\$language_settings = array();".PGV_EOL);
			foreach ($language_settings as $key => $value) {
//				if (!isset($languages[$key]) || (isset($pgv_language[$key]) && !file_exists($pgv_language[$key]))) continue;
				if (!isset($languages[$key])) continue;
				fwrite($fp, PGV_EOL);
				fwrite($fp, "//-- settings for {$languages[$key]}".PGV_EOL);
				fwrite($fp, "\$language_settings['{$languages[$key]}']=array(".PGV_EOL);
				fwrite($fp, "'pgv_langname'=>'{$languages[$key]}',".PGV_EOL);
				fwrite($fp, "'pgv_lang_use'=>".($pgv_lang_use[$key]?'true':'false').",".PGV_EOL);
				fwrite($fp, "'pgv_lang_self'=>'{$pgv_lang_self[$key]}',".PGV_EOL);
				fwrite($fp, "'lang_short_cut'=>'{$lang_short_cut[$key]}',".PGV_EOL);
				fwrite($fp, "'langcode'=>'{$lang_langcode[$key]}',".PGV_EOL);
				fwrite($fp, "'pgv_language'=>'{$pgv_language[$key]}',".PGV_EOL);
				fwrite($fp, "'confighelpfile'=>'{$confighelpfile[$key]}',".PGV_EOL);
				fwrite($fp, "'helptextfile'=>'{$helptextfile[$key]}',".PGV_EOL);
				fwrite($fp, "'flagsfile'=>'{$flagsfile[$key]}',".PGV_EOL);
				fwrite($fp, "'factsfile'=>'{$factsfile[$key]}',".PGV_EOL);
				fwrite($fp, "'adminfile'=>'{$adminfile[$key]}',".PGV_EOL);
				fwrite($fp, "'editorfile'=>'{$editorfile[$key]}',".PGV_EOL);
				fwrite($fp, "'countryfile'=>'{$countryfile[$key]}',".PGV_EOL);
				fwrite($fp, "'faqlistfile'=>'{$faqlistfile[$key]}',".PGV_EOL);
				fwrite($fp, "'extrafile'=>'{$extrafile[$key]}',".PGV_EOL);
				fwrite($fp, "'DATE_FORMAT'=>'{$DATE_FORMAT_array[$key]}',".PGV_EOL);
				fwrite($fp, "'TIME_FORMAT'=>'{$TIME_FORMAT_array[$key]}',".PGV_EOL);
				fwrite($fp, "'WEEK_START'=>'{$WEEK_START_array[$key]}',".PGV_EOL);
				fwrite($fp, "'TEXT_DIRECTION'=>'{$TEXT_DIRECTION_array[$key]}',".PGV_EOL);
				fwrite($fp, "'NAME_REVERSE'=>".($NAME_REVERSE_array[$key]?'true':'false').",".PGV_EOL);
				fwrite($fp, "'ALPHABET_upper'=>'{$ALPHABET_upper[$key]}',".PGV_EOL);
				fwrite($fp, "'ALPHABET_lower'=>'{$ALPHABET_lower[$key]}',".PGV_EOL);
				fwrite($fp, "'MULTI_LETTER_ALPHABET'=>'{$MULTI_LETTER_ALPHABET[$key]}',".PGV_EOL);
				fwrite($fp, "'MULTI_LETTER_EQUIV'=>'{$MULTI_LETTER_EQUIV[$key]}',".PGV_EOL);
				fwrite($fp, "'DICTIONARY_SORT'=>".($DICTIONARY_SORT[$key]?'true':'false').",".PGV_EOL);
				fwrite($fp, "'COLLATION'=>'{$COLLATION[$key]}'".PGV_EOL);
				fwrite($fp, ");".PGV_EOL);
			}
			fwrite($fp, PGV_EOL);
			fwrite($fp, "?>".PGV_EOL);
			fclose($fp);
		$logline = AddToLog("lang_settings.php updated");
 		check_in($logline, $Filename, $INDEX_DIRECTORY);
		} else $error = "lang_config_write_error";
	} else $error = "lang_set_file_read_error";
	return $error;
}

// This functions checks if an existing file is physically writeable
// The standard PHP function only checks for the R/O attribute and doesn't
// detect authorisation by ACL.
function file_is_writeable($file) {
	$err_write = false;
	$handle = @fopen($file,"r+");
	if ($handle) {
		$i = fclose($handle);
		$err_write = true;
	}
	return($err_write);
}

/**
 * PGV Error Handling function
 *
 * This function will be called by PHP whenever an error occurs.  The error handling
 * is set in the session.php
 * @see http://us2.php.net/manual/en/function.set-error-handler.php
 */
function pgv_error_handler($errno, $errstr, $errfile, $errline) {
	if ((error_reporting() > 0)&&($errno<2048)) {
		if (PGV_ERROR_LEVEL==0) {
			return;
		}
		if (stristr($errstr,"by reference")==true) {
			return;
		}
		$fmt_msg="\n<br />ERROR {$errno}: {$errstr}<br />\n";
		$log_msg="ERROR {$errno}: {$errstr}; ";
		// Although debug_backtrace should always exist in PHP5, without this check, PHP sometimes crashes.
		// Possibly calling it generates an error, which causes infinite recursion??
		if ($errno<16 && function_exists("debug_backtrace") && strstr($errstr, "headers already sent by")===false) {
			$backtrace=debug_backtrace();
			$num=count($backtrace);
			if (PGV_ERROR_LEVEL==1) {
				$num=1;
			}
			for ($i=0; $i<$num; $i++) {
				if ($i==0) {
					$fmt_msg.="0 Error occurred on ";
					$log_msg.="0 Error occurred on ";
				} else {
					$fmt_msg.="{$i} called from ";
					$log_msg.="{$i} called from ";
				}
				if (isset($backtrace[$i]["line"]) && isset($backtrace[$i]["file"])) {
					$fmt_msg.="line <b>{$backtrace[$i]['line']}</b> of file <b>".basename($backtrace[$i]['file'])."</b>";
					$log_msg.="line {$backtrace[$i]['line']} of file ".basename($backtrace[$i]['file']);
				}
				if ($i<$num-1) {
					$fmt_msg.=" in function <b>".$backtrace[$i+1]['function']."</b>";
					$log_msg.=" in function ".$backtrace[$i+1]['function'];
				}
				$fmt_msg.="<br />\n";
			}
		}
		echo $fmt_msg;
		if (function_exists('AddToLog')) {
			AddToLog($log_msg);
		}
		if ($errno==1) {
			die();
		}
	}
	return false;
}

// ************************************************* START OF GEDCOM FUNCTIONS ********************************* //

/**
 * Get first tag in GEDCOM sub-record
 *
 * This routine uses function get_sub_record to retrieve the specified sub-record
 * and then returns the first tag.
 *
 */
function get_first_tag($level, $tag, $gedrec, $num=1) {
	$temp = get_sub_record($level, $level." ".$tag, $gedrec, $num)."\n";
	$temp = str_replace("\r\n", "\n", $temp);
	$length = strpos($temp, "\n");
	if ($length===false) {
		$length = strlen($temp);
	}
	return substr($temp, 2, $length-2);
}

/**
 * get a gedcom subrecord
 *
 * searches a gedcom record and returns a subrecord of it.  A subrecord is defined starting at a
 * line with level N and all subsequent lines greater than N until the next N level is reached.
 * For example, the following is a BIRT subrecord:
 * <code>1 BIRT
 * 2 DATE 1 JAN 1900
 * 2 PLAC Phoenix, Maricopa, Arizona</code>
 * The following example is the DATE subrecord of the above BIRT subrecord:
 * <code>2 DATE 1 JAN 1900</code>
 * @author John Finlay (yalnifj)
 * @author Roland Dalmulder (roland-d)
 * @param int $level the N level of the subrecord to get
 * @param string $tag a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
 * @param string $gedrec the parent gedcom record to search in
 * @param int $num this allows you to specify which matching <var>$tag</var> to get.  Oftentimes a
 * gedcom record will have more that 1 of the same type of subrecord.  An individual may have
 * multiple events for example.  Passing $num=1 would get the first 1.  Passing $num=2 would get the
 * second one, etc.
 * @return string the subrecord that was found or an empty string "" if not found.
 */
function get_sub_record($level, $tag, $gedrec, $num=1) {
	if (empty($gedrec)) {
		return "";
	}
	// -- adding \n before and after gedrec
	$gedrec = "\n".$gedrec."\n";
	$pos1=0;
	$subrec = "";
	$tag = trim($tag);
	$searchTarget = "~[\r\n]".$tag."[\s]~";
	$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
	if ($ct==0) {
		$tag = preg_replace("/(\w+)/", "_$1", $tag);
		$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		if ($ct==0) {
			return "";
		}
	}
	if ($ct<$num) {
		return "";
	}
	$pos1 = $match[$num-1][0][1];
	$pos2 = strpos($gedrec, "\n$level", $pos1+1);
	if (!$pos2) {
		$pos2 = strpos($gedrec, "\n1", $pos1+1);
	}
	if (!$pos2) {
		$pos2 = strpos($gedrec, "\nPGV_", $pos1+1); // PGV_SPOUSE, PGV_FAMILY_ID ...
	}
	if (!$pos2) {
		return ltrim(substr($gedrec, $pos1));
	}
	$subrec = substr($gedrec, $pos1, $pos2-$pos1);
	return ltrim($subrec);
}

/**
 * find all of the level 1 subrecords of the given record
 * @param string $gedrec the gedcom record to get the subrecords from
 * @param string $ignore a list of tags to ignore
 * @param boolean $families whether to include any records from the family
 * @param boolean $sort whether or not to sort the record by date
 * @param boolean $ApplyPriv whether to apply privacy right now or later
 * @return array an array of the raw subrecords to return
 */
function get_all_subrecords($gedrec, $ignore="", $families=true, $ApplyPriv=true) {
	$repeats = array();

	$id = "";
	$gt = preg_match('/0 @('.PGV_REGEX_XREF.')@/', $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$hasResn = strstr($gedrec, " RESN ");
	$prev_tags = array();
	$ct = preg_match_all('/\n1 ('.PGV_REGEX_TAG.')(.*)/', $gedrec, $match, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
	for ($i=0; $i<$ct; $i++) {
		$fact = trim($match[$i][1][0]);
		$pos1 = $match[$i][0][1];
		if ($i<$ct-1) {
			$pos2 = $match[$i+1][0][1];
		} else {
			$pos2 = strlen($gedrec);
		}
		if (empty($ignore) || strpos($ignore, $fact)===false) {
			if (!$ApplyPriv || (showFact($fact, $id)&& showFactDetails($fact,$id))) {
				if (isset($prev_tags[$fact])) {
					$prev_tags[$fact]++;
				} else {
					$prev_tags[$fact] = 1;
				}
				$subrec = substr($gedrec, $pos1, $pos2-$pos1);
				if (!$ApplyPriv || !$hasResn || !FactViewRestricted($id, $subrec)) {
					if ($fact=="EVEN") {
						$tt = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
						if ($tt>0) {
							$type = trim($tmatch[1]);
							if (!$ApplyPriv || (showFact($type, $id)&&showFactDetails($type,$id))) {
								$repeats[] = trim($subrec)."\n";
							}
						} else
							$repeats[] = trim($subrec)."\n";
					} else {
						$repeats[] = trim($subrec)."\n";
					}
				}
			}
		}
	}

	//-- look for any records in FAMS records
	if ($families) {
		$ft = preg_match_all('/\n1 FAMS @('.PGV_REGEX_XREF.')@/', $gedrec, $fmatch, PREG_SET_ORDER);
		for ($f=0; $f<$ft; $f++) {
			$famid = $fmatch[$f][1];
			$famrec = find_family_record($fmatch[$f][1]);
			$parents = find_parents_in_record($famrec);
			if ($id==$parents["HUSB"]) {
				$spid = $parents["WIFE"];
			} else {
				$spid = $parents["HUSB"];
			}
			$prev_tags = array();
			$ct = preg_match_all('/\n1 ('.PGV_REGEX_TAG.')(.*)/', $famrec, $match, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				$fact = trim($match[$i][1]);
				if (empty($ignore) || strpos($ignore, $fact)===false) {
					if (!$ApplyPriv || (showFact($fact, $id)&&showFactDetails($fact,$id))) {
						if (isset($prev_tags[$fact])) {
							$prev_tags[$fact]++;
						} else {
							$prev_tags[$fact] = 1;
						}
						$subrec = get_sub_record(1, "1 $fact", $famrec, $prev_tags[$fact]);
						$subrec .= "\n2 _PGVS @$spid@\n2 _PGVFS @$famid@\n";
						if ($fact=="EVEN") {
							$ct = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
							if ($ct>0) {
								$type = trim($tmatch[1]);
								if (!$ApplyPriv or (showFact($type, $id)&&showFactDetails($type,$id))) {
									$repeats[] = trim($subrec)."\n";
								}
							} else {
								$repeats[] = trim($subrec)."\n";
							}
						} else {
							$repeats[] = trim($subrec)."\n";
						}
					}
				}
			}
		}
	}

	return $repeats;
}

/**
 * get gedcom tag value
 *
 * returns the value of a gedcom tag from the given gedcom record
 * @param string $tag	The tag to find, use : to delineate subtags
 * @param int $level	The gedcom line level of the first tag to find, setting level to 0 will cause it to use 1+ the level of the incoming record
 * @param string $gedrec	The gedcom record to get the value from
 * @param int $truncate	Should the value be truncated to a certain number of characters
 * @param boolean $convert	Should data like dates be converted using the configuration settings
 * @return string
 */
function get_gedcom_value($tag, $level, $gedrec, $truncate='', $convert=true) {
	global $SHOW_PEDIGREE_PLACES, $pgv_lang;

	if (empty($gedrec)) {
		return "";
	}
	$tags = explode(':', $tag);
	$origlevel = $level;
	if ($level==0) {
		$level = $gedrec{0} + 1;
	}

	$subrec = $gedrec;
	foreach ($tags as $indexval => $t) {
		$lastsubrec = $subrec;
		$subrec = get_sub_record($level, "$level $t", $subrec);
		if (empty($subrec) && $origlevel==0) {
			$level--;
			$subrec = get_sub_record($level, "$level $t", $lastsubrec);
		}
		if (empty($subrec)) {
			if ($t=="TITL") {
				$subrec = get_sub_record($level, "$level ABBR", $lastsubrec);
				if (!empty($subrec)) {
					$t = "ABBR";
				}
			}
			if (empty($subrec)) {
				if ($level>0) {
					$level--;
				}
				$subrec = get_sub_record($level, "@ $t", $gedrec);
				if (empty($subrec)) {
					return;
				}
			}
		}
		$level++;
	}
	$level--;
	$ct = preg_match("/$level $t(.*)/", $subrec, $match);
	if ($ct==0) {
		$ct = preg_match("/$level @.+@ (.+)/", $subrec, $match);
	}
	if ($ct==0) {
		$ct = preg_match("/@ $t (.+)/", $subrec, $match);
	}
	if ($ct > 0) {
		$value = trim($match[1]);
		$ct = preg_match("/@(.*)@/", $value, $match);
		if (($ct > 0 ) && ($t!="DATE")) {
			$oldsub = $subrec;
			switch ($t) {
			case 'HUSB':
			case 'WIFE':
			case 'CHIL':
				$subrec = find_person_record($match[1]);
				break;
			case 'FAMC':
			case 'FAMS':
				$subrec = find_family_record($match[1]);
				break;
			case 'SOUR':
				$subrec = find_source_record($match[1]);
				break;
			case 'REPO':
				$subrec = find_other_record($match[1]);
				break;
			default:
				$subrec = find_gedcom_record($match[1]);
				break;
			}
			if ($subrec) {
				$value=$match[1];
				$ct = preg_match("/0 @$match[1]@ $t (.+)/", $subrec, $match);
				if ($ct>0) {
					$value = $match[1];
					$level = 0;
				} else
					$subrec = $oldsub;
			} else
				//-- set the value to the id without the @
				$value = $match[1];
		}
		if ($level!=0 || $t!="NOTE") {
			$value .= get_cont($level+1, $subrec);
		}
		$value = preg_replace("'\n'", "", $value);
		$value = preg_replace("'<br />'", "\n", $value);
		$value = trim($value);
		//-- if it is a date value then convert the date
		if ($convert && $t=="DATE") {
			$g = new GedcomDate($value);
			$value = $g->Display();
			if (!empty($truncate)) {
				if (UTF8_strlen($value)>$truncate) {
					$value = preg_replace("/\(.+\)/", "", $value);
					//if (UTF8_strlen($value)>$truncate) {
					//	$value = preg_replace_callback("/([a-zśź]+)/ui", create_function('$matches', 'return UTF8_substr($matches[1], 0, 3);'), $value);
					//}
				}
			}
		} else
			//-- if it is a place value then apply the pedigree place limit
			if ($convert && $t=="PLAC") {
				if ($SHOW_PEDIGREE_PLACES>0) {
					$plevels = explode(',', $value);
					$value = "";
					for ($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
						if (!empty($plevels[$plevel])) {
							if ($plevel>0) {
								$value .= ", ";
							}
							$value .= trim($plevels[$plevel]);
						}
					}
				}
				if (!empty($truncate)) {
					if (strlen($value)>$truncate) {
						$plevels = explode(',', $value);
						$value = "";
						for ($plevel=0; $plevel<count($plevels); $plevel++) {
							if (!empty($plevels[$plevel])) {
								if (strlen($plevels[$plevel])+strlen($value)+3 < $truncate) {
									if ($plevel>0) {
										$value .= ", ";
									}
									$value .= trim($plevels[$plevel]);
								} else
									break;
							}
						}
					}
				}
			} else
				if ($convert && $t=="SEX") {
					if ($value=="M") {
						$value = get_first_letter($pgv_lang["male"]);
					} elseif ($value=="F") {
						$value = get_first_letter($pgv_lang["female"]);
					} else {
						$value = get_first_letter($pgv_lang["unknown"]);
					}
				} else {
					if (!empty($truncate)) {
						if (strlen($value)>$truncate) {
							$plevels = explode(' ', $value);
							$value = "";
							for ($plevel=0; $plevel<count($plevels); $plevel++) {
								if (!empty($plevels[$plevel])) {
									if (strlen($plevels[$plevel])+strlen($value)+3 < $truncate) {
										if ($plevel>0) {
											$value .= " ";
										}
										$value .= trim($plevels[$plevel]);
									} else
										break;
								}
							}
						}
					}
				}
		return $value;
	}
	return "";
}

/**
 * create CONT lines
 *
 * Break input GEDCOM subrecord into pieces not more than 255 chars long,
 * with CONC and CONT lines as needed.  Routine also pays attention to the
 * word wrapped Notes option.  Routine also avoids splitting UTF-8 encoded
 * characters between lines.
 *
 * @param	string	$newline	Input GEDCOM subrecord to be worked on
 * @return	string	$newged		Output string with all necessary CONC and CONT lines
 */
function breakConts($newline) {
	global $WORD_WRAPPED_NOTES;

	// Determine level number of CONC and CONT lines
	$level = substr($newline, 0, 1);
	$tag = substr($newline, 1, 6);
	if ($tag!=" CONC " && $tag!=" CONT ") {
		$level ++;
	}

	$newged = "";
	$newlines = preg_split("/\r?\n/", rtrim(stripLRMRLM($newline)));
	for ($k=0; $k<count($newlines); $k++) {
		if ($k>0) {
			$newlines[$k] = "{$level} CONT ".$newlines[$k];
		}
		if (strlen($newlines[$k])>255) {
			if ($WORD_WRAPPED_NOTES) {
				while (strlen($newlines[$k])>255) {
					// Make sure this piece ends on a blank, because one blank will be
					// added automatically when everything is put back together
					$lastBlank = strrpos(substr($newlines[$k], 0, 255), " ");
					$thisPiece = rtrim(substr($newlines[$k], 0, $lastBlank+1));
					$newged .= $thisPiece."\n";
					$newlines[$k] = substr($newlines[$k], (strlen($thisPiece)+1));
					$newlines[$k] = "{$level} CONC ".$newlines[$k];
				}
			} else {
				while (strlen($newlines[$k])>255) {
					// Make sure this piece doesn't end on a blank
					// (Blanks belong at the start of the next piece)
					$thisPiece = rtrim(substr($newlines[$k], 0, 255));
					// Make sure this piece doesn't end in the middle of a UTF-8 character
					$nextPieceFirstChar = substr($newlines[$k], strlen($thisPiece), 1);
					if (($nextPieceFirstChar&"\xC0") == "\x80") {
						// Include all of the UTF-8 character in next piece
						while (true) {
							// Find the start of the UTF-8 encoded character
							$nextPieceFirstChar = substr($thisPiece, -1);
							$thisPiece = substr($thisPiece, 0, -1);
							if (($nextPieceFirstChar&"\xC0") != "\x80") {
								break;
							}
						}
						// Make sure we didn't back up to a blank
						$thisPiece = rtrim($thisPiece);
					}
					$newged .= $thisPiece."\n";
					$newlines[$k] = substr($newlines[$k], strlen($thisPiece));
					$newlines[$k] = "{$level} CONC ".$newlines[$k];
				}
			}
			$newged .= trim($newlines[$k])."\n";
		} else {
			$newged .= trim($newlines[$k])."\n";
		}
	}
	return $newged;
}

/**
 * get CONT lines
 *
 * get the N+1 CONT or CONC lines of a gedcom subrecord
 * @param int $nlevel the level of the CONT lines to get
 * @param string $nrec the gedcom subrecord to search in
 * @return string a string with all CONT or CONC lines merged
 */
function get_cont($nlevel, $nrec, $tobr=true) {
	global $WORD_WRAPPED_NOTES;
	$text = "";
	if ($tobr) {
		$newline = "<br />";
	} else {
		$newline = "\n";
	}

	$subrecords = explode("\n", $nrec);
	foreach ($subrecords as $thisSubrecord) {
		if (substr($thisSubrecord, 0, 2)!=$nlevel." ") {
			continue;
		}
		$subrecordType = substr($thisSubrecord, 2, 4);
		if ($subrecordType=="CONT") {
			$text .= $newline;
		}
		if ($subrecordType=="CONC" && $WORD_WRAPPED_NOTES) {
			$text .= " ";
		}
		if ($subrecordType=="CONT" || $subrecordType=="CONC") {
			$text .= rtrim(substr($thisSubrecord, 7));
		}
	}

	return rtrim($text, " ");
}

/**
 * find the parents in a family
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents($famid) {
	global $pgv_lang;

	$famrec = find_family_record($famid);
	if (empty($famrec)) {
		if (PGV_USER_CAN_EDIT) {
			$famrec = find_updated_record($famid);
			if (empty($famrec)) {
				return false;
			}
		} else {
			return false;
		}
	}
	return find_parents_in_record($famrec);
}

/**
 * find the parents in a family record
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents_in_record($famrec) {
	global $pgv_lang;

	if (empty($famrec)) {
		return false;
	}
	$parents = array();
	$ct = preg_match('/1 HUSB @('.PGV_REGEX_XREF.')@/', $famrec, $match);
	if ($ct>0) {
		$parents["HUSB"]=$match[1];
	} else {
		$parents["HUSB"]="";
	}
	$ct = preg_match('/1 WIFE @('.PGV_REGEX_XREF.')@/', $famrec, $match);
	if ($ct>0) {
		$parents["WIFE"]=$match[1];
	} else {
		$parents["WIFE"]="";
	}
	return $parents;
}

/**
 * find the children in a family
 *
 * find and return an array containing the children of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @param string $me	an xref id of a child to ignore, useful when you want to get a person's
 * siblings but do want to include them as well
 * @return array
 */
function find_children($famid, $me='') {
	global $pgv_lang;

	$famrec = find_family_record($famid);
	if (empty($famrec)) {
		if (PGV_USER_CAN_EDIT) {
			$famrec = find_updated_record($famid);
			if (empty($famrec)) {
				return false;
			}
		} else {
			return false;
		}
	}
	return find_children_in_record($famrec);
}

/**
 * find the children in a family record
 *
 * find and return an array containing the children of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @param string $me	an xref id of a child to ignore, useful when you want to get a person's
 * siblings but do want to include them as well
 * @return array
 */
function find_children_in_record($famrec, $me='') {
	global $pgv_lang;

	$children = array();
	if (empty($famrec)) {
		return $children;
	}

	$num = preg_match_all('/\n1 CHIL @('.PGV_REGEX_XREF.')@/', $famrec, $match,PREG_SET_ORDER);
	for ($i=0; $i<$num; $i++) {
		$child = trim($match[$i][1]);
		if ($child!=$me) {
			$children[] = $child;
		}
	}
	return $children;
}

/**
 * find all child family ids
 *
 * searches an individual gedcom record and returns an array of the FAMC ids where this person is a
 * child in the family, but only those families that are allowed to be seen by current user
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_family_ids($pid) {
	$indirec=find_person_record($pid);
	return find_visible_families_in_record($indirec, "FAMC");
}

/**
 * find all spouse family ids
 *
 * searches an individual gedcom record and returns an array of the FAMS ids where this person is a
 * spouse in the family, but only those families that are allowed to be seen by current user
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_sfamily_ids($pid) {
	$indirec=find_person_record($pid);
	return find_visible_families_in_record($indirec, "FAMS");
}

/**
 * find all family ids in the given record
 *
 * searches an individual gedcom record and returns an array of the FAMS|C ids
 * @param string $indirec the gedcom record for the person to look in
 * @param string $tag 	The family tag to look for
 * @return array array of family ids
 */
function find_families_in_record($indirec, $tag) {
	preg_match_all("/\n1 {$tag} @(".PGV_REGEX_XREF.')@/', $indirec, $match);
	return $match[1];
}

/**
 * find all family ids in the given record that should be visible to the current user
 *
 * searches an individual gedcom record and returns an array of the FAMS|C ids that are visible
 * @param string $indirec the gedcom record for the person to look in
 * @param string $tag 	The family tag to look for, FAMS or FAMC
 * @return array array of family ids
 */
function find_visible_families_in_record($indirec, $tag) {
	$allfams = find_families_in_record($indirec, $tag);
	$visiblefams = array();
	// select only those that are visible to current user
	foreach ($allfams as $key=>$famid) {
		if (displayDetailsById($famid,"FAM")) {
			$visiblefams[] = $famid;
		}
	}
	return $visiblefams;
}

/**
 * find and return an updated gedcom record
 * @param string $gid	the id of the record to find
 * @param string $gedfile	the gedcom file to get the record from.. defaults to currently active gedcom
 */
function find_updated_record($gid, $gedfile="") {
	global $GEDCOM, $pgv_changes;

	if (empty($gedfile)) {
		$gedfile = $GEDCOM;
	}

	if (isset($pgv_changes[$gid."_".$gedfile])) {
		$change = end($pgv_changes[$gid."_".$gedfile]);
		return $change['undo'];
	}
	return "";
}

// Find out if there are any pending changes that a given user may accept
function exists_pending_change($user_id=PGV_USER_ID, $ged_id=PGV_GED_ID) {
	global $pgv_changes;

	if (!isset($pgv_changes) || !userCanAccept($user_id, $ged_id)) {
		return false;
	}

	$gedcom=get_gedcom_from_id($ged_id);
	foreach ($pgv_changes as $pgv_change) {
		if ($pgv_change[0]['gedcom']==$gedcom) {
			return true;
		}
	}
	return false;
}

// ************************************************* START OF MULTIMEDIA FUNCTIONS ********************************* //
/**
 * find the highlighted media object for a gedcom entity
 *
 * Rules for finding the highlighted media object:
 * 1. The first _PRIM Y object will be used regardless of level in gedcom record
 * 2. The first level 1 object will be used if there if it doesn't have _PRIM N (level 1 objects appear on the media tab on the individual page)
 *
 * @param string $pid the individual, source, or family id
 * @param string $indirec the gedcom record to look in
 * @return array an object array with indexes "thumb" and "file" for thumbnail and filename
 */
function find_highlighted_object($pid, $indirec) {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_EXTERNAL;
	global $GEDCOMS, $GEDCOM, $TBLPREFIX, $gBitDb;

	if (!showFactDetails("OBJE", $pid)) {
		return false;
	}
	$object = array();
	$media = array();

	//-- handle finding the media of remote objects
	$ct = preg_match("/(.*):(.*)/", $pid, $match);
	if ($ct>0) {
		require_once '../classes/class_serviceclient.php';
		$client = ServiceClient::getInstance($match[1]);
		if (!is_null($client)) {
			$mt = preg_match_all('/\n\d OBJE @('.PGV_REGEX_XREF.')@/', $indirec, $matches, PREG_SET_ORDER);
			for ($i=0; $i<$mt; $i++) {
				$mediaObj = Media::getInstance($matches[$i][1]);
				$mrec = $mediaObj->getGedcomRecord();
				if (!empty($mrec)) {
					$file = get_gedcom_value("FILE", 1, $mrec);
					$row = array($matches[$i][1], $file, $mrec, $matches[$i][0]);
					$media[] = $row;
				}
			}
		}
	}

	//-- find all of the media items for a person
	$media = $gBitDb->query(
		"SELECT m_media, m_file, m_gedrec, mm_gedrec FROM {$TBLPREFIX}media, {$TBLPREFIX}media_mapping WHERE m_media=mm_media AND m_gedfile=mm_gedfile AND m_gedfile=? AND mm_gid=? ORDER BY mm_order"
		, array($GEDCOMS[$GEDCOM]["id"], $pid));

	while ( $row = $media->fetchRow() ) {
		if (displayDetailsById($row['m_media'], 'OBJE') && !FactViewRestricted($row['m_media'], $row['m_gedrec'])) {
			$level=0;
			$ct = preg_match("/(\d+) OBJE/", $row['mm_gedrec'], $match);
			if ($ct>0) {
				$level = $match[1];
			}
			if (strstr($row['mm_gedrec'], "_PRIM ")) {
				$thum = get_gedcom_value('_THUM', $level+1, $row['mm_gedrec']);
				$prim = get_gedcom_value('_PRIM', $level+1, $row['mm_gedrec']);
			} else {
				$thum = get_gedcom_value('_THUM', 1, $row['m_gedrec']);
				$prim = get_gedcom_value('_PRIM', 1, $row['m_gedrec']);
			}
			if ($prim=='N') continue;		// Skip _PRIM N objects
			if ($prim=='Y') {
				// Take the first _PRIM Y object
				$object["file"] = check_media_depth($row['m_file']);
				$object["thumb"] = thumbnail_file($row['m_file'], true, false, $pid);
//				$object["_PRIM"] = $prim;	// Not sure whether this is needed.
				$object["_THUM"] = $thum;	// This overrides GEDCOM's "Use main image as thumbnail" option
				$object["level"] = $level;
				$object["mid"] = $row['m_media'];
				break;		// Stop looking: we found a suitable image
			}
			if ($level==1 && empty($object)) {
				// Take the first level 1 object, but keep looking for an overriding _PRIM Y
				$object["file"] = check_media_depth($row['m_file']);
				$object["thumb"] = thumbnail_file($row['m_file'], true, false, $pid);
//				$object["_PRIM"] = $prim;	// Not sure whether this is needed.
				$object["_THUM"] = $thum;	// This overrides GEDCOM's "Use main image as thumbnail" option
				$object["level"] = $level;
				$object["mid"] = $row['m_media'];
			}
		}
	}
	return $object;
}

/**
 * Determine whether the main image or a thumbnail should be sent to the browser
 */
function thumb_or_main($object) {
	global $USE_THUMBS_MAIN;

	if ($object['_THUM']=='Y' || !$USE_THUMBS_MAIN) $file = 'file';
	else $file = 'thumb';

	// Here we should check whether the selected file actually exists
	return($object[$file]);
}

/**
 * get the full file path
 *
 * get the file path from a multimedia gedcom record
 * @param string $mediarec a OBJE subrecord
 * @return the fullpath from the FILE record
 */
function extract_fullpath($mediarec) {
	preg_match("/(\d) _*FILE (.*)/", $mediarec, $amatch);
	if (empty($amatch[2])) {
		return "";
	}
	$level = trim($amatch[1]);
	$fullpath = trim($amatch[2]);
	$filerec = get_sub_record($level, $amatch[0], $mediarec);
	$fullpath .= get_cont($level+1, $filerec);
	return $fullpath;
}

/**
 * get the relative filename for a media item
 *
 * gets the relative file path from the full media path for a media item.  checks the
 * <var>$MEDIA_DIRECTORY_LEVELS</var> to make sure the directory structure is maintained.
 * @param string $fullpath the full path from the media record
 * @return string a relative path that can be appended to the <var>$MEDIA_DIRECTORY</var> to reference the item
 */
function extract_filename($fullpath) {
	global $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;

	$filename="";
	$regexp = "'[/\\\]'";
	$srch = "/".addcslashes($MEDIA_DIRECTORY,'/.')."/";
	$repl = "";
	if (!isFileExternal($fullpath)) {
		$nomedia = stripcslashes(preg_replace($srch, $repl, $fullpath));
	} else {
		$nomedia = $fullpath;
	}
	$ct = preg_match($regexp, $nomedia, $match);
	if ($ct>0) {
		$subelements = preg_split($regexp, $nomedia);
		$subelements = array_reverse($subelements);
		$max = $MEDIA_DIRECTORY_LEVELS;
		if ($max>=count($subelements)) {
			$max=count($subelements)-1;
		}
		for ($s=$max; $s>=0; $s--) {
			if ($s!=$max) {
				$filename = $filename."/".$subelements[$s];
			} else {
				$filename = $subelements[$s];
			}
		}
	} else {
		$filename = $nomedia;
	}
	return $filename;
}


// ************************************************* START OF SORTING FUNCTIONS ********************************* //
/**
 * Function to sort GEDCOM fact tags based on their tanslations
 */
function factsort($a, $b) {
	global $factarray;

	if (array_key_exists($a, $factarray)) {
		$a=$factarray[$a];
	}
	if (array_key_exists($b, $factarray)) {
		$b=$factarray[$b];
	}
	return stringsort($a, $b);
}
/**
 * Function to sort place names array
 */
function placesort($a, $b) {
	$a = $a['place'];
	$b = $b['place'];
	return stringsort($a, $b);
}
/**
 * String sorting function
 * @param string $a
 * @param string $b
 * @return int negative numbers sort $a first, positive sort $b first
 */
function stringsort($aName, $bName) {
	return compareStrings($aName, $bName, true);		// Case-insensitive sort
}
function stringsort2($aName, $bName) {
	return compareStrings($aName, $bName, false);		// Case-sensitive sort
}
function compareStrings($aName, $bName, $ignoreCase=true) {
	global $LANGUAGE, $CHARACTER_SET;
	global $alphabet, $alphabet_lower, $alphabet_upper;
	global $digraph, $trigraph, $quadgraph;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;

	if (is_array($aName)) {
		debug_print_backtrace();
	}
	getAlphabet();

	if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		$danishFrom = array("AA", "Aa", "AE", "Ae", "OE", "Oe", "aa", "ae", "oe");
		$danishTo 	= array("Å", "Å", "Æ", "Æ", "Ø", "Ø", "å", "æ", "ø");
	}

	if ($LANGUAGE == "german") {
		$germanFrom = array("AA", "Aa", "Æ", "AE", "Ae", "Ø", "OE", "Oe", "SS", "Ss", "UE", "Ue", "aa", "æ", "ae", "ø", "oe", "ss", "ue");
		$germanTo 	= array("Å", "Å", "Ä", "Ä", "Ä", "Ö", "Ö", "Ö", "ß", "ß", "Ü", "Ü", "å", "ä", "ä", "ö", "ö", "ß", "ü");
	}

	//-- split strings into strings and numbers
	$aParts = preg_split("/(\d+)/", $aName, -1, PREG_SPLIT_DELIM_CAPTURE);
	$bParts = preg_split("/(\d+)/", $bName, -1, PREG_SPLIT_DELIM_CAPTURE);

	//-- loop through the arrays of strings and numbers
	$ac = count($aParts);
	$bc = count($bParts);
	for ($j=0; ($j<$ac && $j<$bc); $j++) {
		$aName = $aParts[$j];
		$bName = $bParts[$j];

		//-- sort numbers differently
		if (is_numeric($aName) && is_numeric($bName)) {
			if ($aName!=$bName) {
				return $aName-$bName;
			}
		} else {
			//-- Take care of Danish and Norwegian character transformations
			if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
				$aName = str_replace($danishFrom, $danishTo, $aName);
				$bName = str_replace($danishFrom, $danishTo, $bName);
			}
			// -- Take care of German character transformations
			if ($LANGUAGE == "german") {
				$aName = str_replace($germanFrom, $germanTo, $aName);
				$bName = str_replace($germanFrom, $germanTo, $bName);
			}

			//-- get the name lengths
			$alen = strlen($aName);
			$blen = strlen($bName);

			//-- loop through the characters in the string and if we find one that is different between the strings
			//-- return the difference
			$aIndex = 0;
			$bIndex = 0;
			$aDiacriticValue = "";
			$bDiacriticValue = "";
			while (true) {
				$aMultiLetter = false;
				$bMultiLetter = false;
				// Look for quadgraphs (4 letters that should be treated as 1)
				if (isset($quadgraph[$LANGUAGE])) {
					$aLetter = strtoupper(substr($aName, $aIndex, 4));
					if (isset($quadgraph[$LANGUAGE][$aLetter])) {
						$aMultiLetter = $quadgraph[$LANGUAGE][$aLetter];
						$aCharLen = 4;
					}
					$bLetter = strtoupper(substr($bName, $bIndex, 4));
					if (isset($quadgraph[$LANGUAGE][$bLetter])) {
						$bMultiLetter = $quadgraph[$LANGUAGE][$bLetter];
						$bCharLen = 4;
					}
				}
				// Look for trigraphs (3 letters that should be treated as 1)
				if (isset($trigraph[$LANGUAGE])) {
					if (!$aMultiLetter) {
						$aLetter = strtoupper(substr($aName, $aIndex, 3));
						if (isset($trigraph[$LANGUAGE][$aLetter])) {
							$aMultiLetter = $trigraph[$LANGUAGE][$aLetter];
							$aCharLen = 3;
						}
					}
					if (!$bMultiLetter) {
						$bLetter = strtoupper(substr($bName, $bIndex, 3));
						if (isset($trigraph[$LANGUAGE][$bLetter])) {
							$bMultiLetter = $trigraph[$LANGUAGE][$bLetter];
							$bCharLen = 3;
						}
					}
				}
				// Look for digraphs (2 letters that should be treated as 1)
				if (isset($digraphs[$LANGUAGE])) {
					if (!$aMultiLetter) {
					$aLetter = strtoupper(substr($aName, $aIndex, 2));
						if (isset($digraph[$LANGUAGE][$aLetter])) {
							$aMultiLetter = $digraph[$LANGUAGE][$aLetter];
							$aCharLen = 2;
						}
					}
					if (!$bMultiLetter) {
					$bLetter = strtoupper(substr($bName, $bIndex, 2));
						if (isset($digraph[$LANGUAGE][$bLetter])) {
							$bMultiLetter = $digraph[$LANGUAGE][$bLetter];
							$bCharLen = 2;
						}
					}
				}

				// Look for UTF-8 encoded characters
				if (!$aMultiLetter) {
					$aCharLen = 1;
					$aLetter = substr($aName, $aIndex, 1);
					$aOrd = ord($aLetter);
					if (($aOrd & 0xE0) == 0xC0) $aCharLen = 2;		// 2-byte sequence
					if (($aOrd & 0xF0) == 0xE0) $aCharLen = 3;		// 3-byte sequence
					if (($aOrd & 0xF8) == 0xF0) $aCharLen = 4;		// 4-byte sequence
				}

				if (!$bMultiLetter) {
					$bCharLen = 1;
					$bLetter = substr($bName, $bIndex, 1);
					$bOrd = ord($bLetter);
					if (($bOrd & 0xE0) == 0xC0) $bCharLen = 2;		// 2-byte sequence
					if (($bOrd & 0xF0) == 0xE0) $bCharLen = 3;		// 3-byte sequence
					if (($bOrd & 0xF8) == 0xF0) $bCharLen = 4;		// 4-byte sequence
				}

				$aLetter = substr($aName, $aIndex, $aCharLen);
				$bLetter = substr($bName, $bIndex, $bCharLen);

				if ($DICTIONARY_SORT[$LANGUAGE]) {
					//-- strip diacritics before checking equality
					if ($aCharLen==2) {
						$aPos = strpos($UCDiacritWhole, $aLetter);
						if ($aPos!==false) {
							$aPos = $aPos >> 1;
							$aLetter = substr($UCDiacritStrip, $aPos, 1);
							$aDiacriticValue .= substr($UCDiacritOrder, $aPos, 1);
						} else {
							$aPos = strpos($LCDiacritWhole, $aLetter);
							if ($aPos!==false) {
								$aPos = $aPos >> 1;
								$aLetter = substr($LCDiacritStrip, $aPos, 1);
								$aDiacriticValue .= substr($LCDiacritOrder, $aPos, 1);
							} else
								$aDiacriticValue .= " ";
						}
					} else
						$aDiacriticValue .= " ";

					if ($bCharLen==2) {
						$bPos = strpos($UCDiacritWhole, $bLetter);
						if ($bPos!==false) {
							$bPos = $bPos >> 1;
							$bLetter = substr($UCDiacritStrip, $bPos, 1);
							$bDiacriticValue .= substr($UCDiacritOrder, $bPos, 1);
						} else {
							$bPos = strpos($LCDiacritWhole, $bLetter);
							if ($bPos!==false) {
								$bPos = $bPos >> 1;
								$bLetter = substr($LCDiacritStrip, $bPos, 1);
								$bDiacriticValue .= substr($LCDiacritOrder, $bPos, 1);
							} else
								$bDiacriticValue .= " ";
						}
					} else
						$bDiacriticValue .= " ";
				}

				if ($ignoreCase) {
					$aLetter = UTF8_strtoupper($aLetter);
					$bLetter = UTF8_strtoupper($bLetter);
				}

				if ($aLetter!=$bLetter && $bLetter!="" && $aLetter!="") {
					//-- get the position of the letter in the alphabet string
					if ($aMultiLetter) {
						$sortAfter = substr($aLetter,0,1);
						if ($aLetter=="CH") $sortAfter = "H";		// This one doesn't follow the rule
						if ($aLetter=="Ch") $sortAfter = "H";
						if ($aLetter=="ch") $sortAfter = "h";
						$aPos = strpos($alphabet_upper, $sortAfter);
						if ($aPos===false) $aPos = strpos($alphabet_lower, $sortAfter);
					} else {
						$aPos = @strpos($alphabet_upper, $aLetter);
						if ($aPos===false) $aPos = @strpos($alphabet_lower, $aLetter);
					}
					if ($bMultiLetter) {
						$sortAfter = substr($bLetter,0,1);
						if ($bLetter=="CH") $sortAfter = "H";		// This one doesn't follow the rule
						if ($bLetter=="Ch") $sortAfter = "H";
						if ($bLetter=="ch") $sortAfter = "h";
						$bPos = strpos($alphabet_upper, $sortAfter);
						if ($bPos===false) $bPos = strpos($alphabet_lower, $sortAfter);
					} else {
						$bPos = @strpos($alphabet_upper, $bLetter);
						if ($bPos===false) $bPos = @strpos($alphabet_lower, $bLetter);
					}

					// Insert digraphs and trigraphs into main sequence
					if ($aMultiLetter || $bMultiLetter) {
						$aPos = ((int) $aPos << 3) + (int) $aMultiLetter;
						$bPos = ((int) $bPos << 3) + (int) $bMultiLetter;
					}

					if ($aPos!=$bPos) {
						if ($aLetter=="@") return 1;		// Force "@" to the end
						if ($bLetter=="@") return -1;		// Force "@" to the end
						if (($bPos!==false)&&($aPos===false)) return -1;
						if (($bPos===false)&&($aPos!==false)) return 1;
						if (($bPos===false)&&($aPos===false)) {
							// Determine the binary value of both letters
							$aValue = UTF8_ord($aLetter);
							$bValue = UTF8_ord($bLetter);
							return $aValue - $bValue;
						}
						return ($aPos-$bPos);
					}
				}
				$aIndex += $aCharLen;			// advance to the 1st byte of the next sequence
				$bIndex += $bCharLen;			// advance to the 1st byte of the next sequence
				if ($aIndex >= $alen) break;
				if ($bIndex >= $blen) break;
			}
		}

		//-- if we made it through the loop then check if one name is longer than the
		//-- other, the shorter one should be first
		if ($alen!=$blen) return ($alen-$blen);

		//-- They're identical: let diacritics (if any) decide
		if ($aDiacriticValue < $bDiacriticValue) return -1;
		if ($aDiacriticValue > $bDiacriticValue) return 1;
	}
	if (count($aParts)!=count($bParts)) return (count($aParts)-count($bParts));

	//-- the strings are exactly the same so return 0
	return 0;
}

////////////////////////////////////////////////////////////////////////////////
// Sort a list events for the today/upcoming blocks
////////////////////////////////////////////////////////////////////////////////
function event_sort($a, $b) {
	if ($a['jd']==$b['jd']) {
		if ($a['anniv']==$b['anniv']) {
			return compareStrings($a['fact'], $b['fact']);
		}
		else {
			return compareStrings($a['anniv'], $b['anniv']);
		}
	} else {
		return $a['jd']-$b['jd'];
	}
}

function event_sort_name($a, $b) {
	if ($a['jd']==$b['jd']) {
		return compareStrings($a['name'], $b['name']);
	} else {
		return $a['jd']-$b['jd'];
	}
}

/**
 * sort an array of media items
 *
 */

function mediasort($a, $b) {
	$aKey = "";
	if (!empty($a["TITL"]))
		$aKey = $a["TITL"];
	else
		if (!empty($a["titl"]))
			$aKey = $a["titl"];
		else
			if (!empty($a["NAME"]))
				$aKey = $a["NAME"];
			else
				if (!empty($a["name"]))
					$aKey = $a["name"];
				else
					if (!empty($a["FILE"]))
						$aKey = basename($a["FILE"]);
					else
						if (!empty($a["file"]))
							$aKey = basename($a["file"]);

	$bKey = "";
	if (!empty($b["TITL"]))
		$bKey = $b["TITL"];
	else
		if (!empty($b["titl"]))
			$bKey = $b["titl"];
		else
			if (!empty($b["NAME"]))
				$bKey = $b["NAME"];
			else
				if (!empty($b["name"]))
					$bKey = $b["name"];
				else
					if (!empty($b["FILE"]))
						$bKey = basename($b["FILE"]);
					else
						if (!empty($b["file"]))
							$bKey = basename($b["file"]);
	return compareStrings($aKey, $bKey, true);		// Case-insensitive compare
}
/**
 * sort an array according to the file name
 *
 */

function filesort($a, $b) {
	$aKey = "";
	if (!empty($a["FILE"])) {
		$aKey = basename($a["FILE"]);
	} else if (!empty($a["file"])) {
		$aKey = basename($a["file"]);
	}

	$bKey = "";
	if (!empty($b["FILE"])) {
		$bKey = basename($b["FILE"]);
	} else if (!empty($b["file"])) {
		$bKey = basename($b["file"]);
	}
	return compareStrings($aKey, $bKey, true);		// Case-insensitive compare
}

// Helper function to sort facts.
function compare_facts_date($arec, $brec) {
	if (is_array($arec))
		$arec = $arec[1];
	if (is_array($brec))
		$brec = $brec[1];

	// If either fact is undated, the facts sort equally.
	if (!preg_match("/2 _?DATE (.*)/", $arec, $amatch) || !preg_match("/2 _?DATE (.*)/", $brec, $bmatch)) {
		if (preg_match('/2 _SORT (\d+)/', $arec, $match1) && preg_match('/2 _SORT (\d+)/', $brec, $match2)) {
			return $match1[1]-$match2[1];
		}
		return 0;
	}

	$adate = new GedcomDate($amatch[1]);
	$bdate = new GedcomDate($bmatch[1]);
	// If either date can't be parsed, don't sort.
	if (!$adate->isOK() || !$bdate->isOK()) {
		if (preg_match('/2 _SORT (\d+)/', $arec, $match1) && preg_match('/2 _SORT (\d+)/', $brec, $match2)) {
			return $match1[1]-$match2[1];
		}
		return 0;
	}

	// Remember that dates can be ranges and overlapping ranges sort equally.
	$amin=$adate->MinJD();
	$bmin=$bdate->MinJD();
	$amax=$adate->MaxJD();
	$bmax=$bdate->MaxJD();

	// BEF/AFT XXX sort as the day before/after XXX
	if ($adate->qual1=='BEF') {
		$amin=$amin-1;
		$amax=$amin;
	} else
		if ($adate->qual1=='AFT') {
			$amax=$amax+1;
			$amin=$amax;
		}
	if ($bdate->qual1=='BEF') {
		$bmin=$bmin-1;
		$bmax=$bmin;
	} else
		if ($bdate->qual1=='AFT') {
			$bmax=$bmax+1;
			$bmin=$bmax;
		}

	if ($amax<$bmin)
		return -1;
	else
		if ($amin>$bmax)
			return 1;
		else {
			//-- ranged date... take the type of fact sorting into account
			$factWeight = 0;
			if (preg_match('/2 _SORT (\d+)/', $arec, $match1) && preg_match('/2 _SORT (\d+)/', $brec, $match2)) {
				$factWeight = $match1[1]-$match2[1];
			}
			//-- fact is prefered to come before, so compare using the minimum ranges
			if ($factWeight < 0 && $amin!=$bmin) {
				return ($amin-$bmin);
			} else
				if ($factWeight > 0 && $bmax!=$amax) {
					//-- fact is prefered to come after, so compare using the max of the ranges
					return ($bmax-$amax);
				} else {
					//-- facts are the same or the ranges don't give enough info, so use the average of the range
					$aavg = ($amin+$amax)/2;
					$bavg = ($bmin+$bmax)/2;
					if ($aavg<$bavg)
						return -1;
					else
						if ($aavg>$bavg)
							return 1;
						else
							return $factWeight;
				}

			return 0;
		}
}

/**
 * A multi-key sort
 * 1. First divide the facts into two arrays one set with dates and one set without dates
 * 2. Sort each of the two new arrays, the date using the compare date function, the non-dated
 * using the compare type function
 * 3. Then merge the arrays back into the original array using the compare type function
 *
 * @param unknown_type $arr
 */
function sort_facts(&$arr) {
	$dated = array();
	$nondated = array();
	//-- split the array into dated and non-dated arrays
	$order = 0;
	foreach($arr as $event) {
		$event->sortOrder = $order;
		$order++;
		if ($event->getValue("DATE")==NULL) $nondated[] = $event;
		else $dated[] = $event;
	}

	//-- sort each type of array
	usort($dated, array("Event","CompareDate"));
	usort($nondated, array("Event","CompareType"));

	//-- merge the arrays back together comparing by Facts
	$dc = count($dated);
	$nc = count($nondated);
	$i = 0;
	$j = 0;
	$k = 0;
	// while there is anything in the dated array continue merging
	while($i<$dc) {
		// compare each fact by type to merge them in order
		if ($j<$nc && Event::CompareType($dated[$i],$nondated[$j])>0) {
			$arr[$k] = $nondated[$j];
			$j++;
		}
		else {
			$arr[$k] = $dated[$i];
			$i++;
		}
		$k++;
	}

	// get anything that might be left in the nondated array
	while($j<$nc) {
		$arr[$k] = $nondated[$j];
		$j++;
		$k++;
	}

}

function gedcomsort($a, $b) {
	$aname = UTF8_strtoupper($a["title"]);
	$bname = UTF8_strtoupper($b["title"]);

	return stringsort($aname, $bname);
}

// ************************************************* START OF MISCELLANIOUS FUNCTIONS ********************************* //
/**
 * Get relationship between two individuals in the gedcom
 *
 * function to calculate the relationship between two people it uses hueristics based on the
 * individuals birthdate to try and calculate the shortest path between the two individuals
 * it uses a node cache to help speed up calculations when using relationship privacy
 * this cache is indexed using the string "$pid1-$pid2"
 * @param string $pid1 the ID of the first person to compute the relationship from
 * @param string $pid2 the ID of the second person to compute the relatiohip to
 * @param bool $followspouse whether to add spouses to the path
 * @param int $maxlenght the maximim length of path
 * @param bool $ignore_cache enable or disable the relationship cache
 * @param int $path_to_find which path in the relationship to find, 0 is the shortest path, 1 is the next shortest path, etc
 */
function get_relationship($pid1, $pid2, $followspouse=true, $maxlength=0, $ignore_cache=false, $path_to_find=0) {
	global $TIME_LIMIT, $start_time, $pgv_lang, $NODE_CACHE, $NODE_CACHE_LENGTH, $USE_RELATIONSHIP_PRIVACY, $pgv_changes, $GEDCOM;

	$pid1 = strtoupper($pid1);
	$pid2 = strtoupper($pid2);
	if (isset($pgv_changes[$pid2."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
		$indirec = find_updated_record($pid2);
	else
		$indirec = find_person_record($pid2);
	//-- check the cache
	if ($USE_RELATIONSHIP_PRIVACY && !$ignore_cache) {
		if (isset($NODE_CACHE["$pid1-$pid2"])) {
			if ($NODE_CACHE["$pid1-$pid2"]=="NOT FOUND") return false;
			if (($maxlength==0)||(count($NODE_CACHE["$pid1-$pid2"]["path"])-1<=$maxlength))
				return $NODE_CACHE["$pid1-$pid2"];
			else
				return false;
		}
		//-- check the cache for person 2's children
		$famids = array();
		$ct = preg_match_all("/1\sFAMS\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
		for ($i=0; $i<$ct; $i++) {
			$famids[$i]=$match[$i][1];
		}
		foreach ($famids as $indexval => $fam) {
			if (isset($pgv_changes[$fam."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
				$famrec = find_updated_record($fam);
			else
				$famrec = find_family_record($fam);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				$child = $match[$i][1];
				if (!empty($child)) {
					if (isset($NODE_CACHE["$pid1-$child"])) {
						if (($maxlength==0)||(count($NODE_CACHE["$pid1-$child"]["path"])+1<=$maxlength)) {
							$node1 = $NODE_CACHE["$pid1-$child"];
							if ($node1!="NOT FOUND") {
								$node1["path"][] = $pid2;
								$node1["pid"] = $pid2;
								$ct = preg_match("/1 SEX F/", $indirec, $match);
								if ($ct>0)
									$node1["relations"][] = "mother";
								else
									$node1["relations"][] = "father";
							}
							$NODE_CACHE["$pid1-$pid2"] = $node1;
							if ($node1=="NOT FOUND")
								return false;
							return $node1;
						} else
							return false;
					}
				}
			}
		}

		if ((!empty($NODE_CACHE_LENGTH))&&($maxlength>0)) {
			if ($NODE_CACHE_LENGTH>=$maxlength)
				return false;
		}
	}
	//-- end cache checking

	//-- get the birth year of p2 for calculating heuristics
	$birthrec = get_sub_record(1, "1 BIRT", $indirec);
	$byear2 = -1;
	if ($birthrec!==false) {
		$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
		if ($dct>0)
			$byear2 = $match[1];
	}
	if ($byear2==-1) {
		$numfams = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $fmatch, PREG_SET_ORDER);
		for ($j=0; $j<$numfams; $j++) {
			// Get the family record
			if (isset($pgv_changes[$fmatch[$j][1]."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
				$famrec = find_updated_record($fmatch[$j][1]);
			else
				$famrec = find_family_record($fmatch[$j][1]);

			// Get the set of children
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $cmatch, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				// Get each child's record
				if (isset($pgv_changes[$cmatch[$i][1]."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
					$childrec = find_updated_record($cmatch[$i][1]);
				else
					$childrec = find_person_record($cmatch[$i][1]);
				$birthrec = get_sub_record(1, "1 BIRT", $childrec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $bmatch);
					if ($dct>0)
						$byear2 = $bmatch[1]-25;
						if ($byear2>2100) $byear2-=3760; // Crude conversion from jewish to gregorian
				}
			}
		}
	}
	//-- end of approximating birth year

	//-- current path nodes
	$p1nodes = array();
	//-- ids visited
	$visited = array();

	//-- set up first node for person1
	$node1 = array();
	$node1["path"] = array();
	$node1["path"][] = $pid1;
	$node1["length"] = 0;
	$node1["pid"] = $pid1;
	$node1["relations"] = array();
	$node1["relations"][] = "self";
	$p1nodes[] = $node1;

	$visited[$pid1] = true;

	$found = false;
	$count=0;
	while (!$found) {
		//-- the following 2 lines ensure that the user can abort a long relationship calculation
		//-- refer to http://www.php.net/manual/en/features.connection-handling.php for more
		//-- information about why these lines are included
		if (headers_sent()) {
			print " ";
			if ($count%100 == 0)
				flush();
		}
		$count++;
		$end_time = microtime(true);
		$exectime = $end_time - $start_time;
		if (($TIME_LIMIT>1)&&($exectime > $TIME_LIMIT-1)) {
			print "<span class=\"error\">".$pgv_lang["timeout_error"]."</span>\n";
			return false;
		}
		if (count($p1nodes)==0) {
			if ($maxlength!=0) {
				if (!isset($NODE_CACHE_LENGTH))
					$NODE_CACHE_LENGTH = $maxlength;
				else
					if ($NODE_CACHE_LENGTH<$maxlength)
						$NODE_CACHE_LENGTH = $maxlength;
			}
			if (headers_sent()) {
				print "\n<!-- Relationship $pid1-$pid2 NOT FOUND | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
				print_execution_stats();
				print "-->\n";
			}
			$NODE_CACHE["$pid1-$pid2"] = "NOT FOUND";
			return false;
		}
		//-- search the node list for the shortest path length
		$shortest = -1;
		foreach ($p1nodes as $index=>$node) {
			if ($shortest == -1)
				$shortest = $index;
			else {
				$node1 = $p1nodes[$shortest];
				if ($node1["length"] > $node["length"])
					$shortest = $index;
			}
		}
		if ($shortest==-1)
			return false;
		$node = $p1nodes[$shortest];
		if (($maxlength==0)||(count($node["path"])<=$maxlength)) {
			if ($node["pid"]==$pid2) {
			} else {
				//-- hueristic values
				$fatherh = 1;
				$motherh = 1;
				$siblingh = 2;
				$spouseh = 2;
				$childh = 3;

				//-- generate heuristic values based of the birthdates of the current node and p2
				if (isset($pgv_changes[$node["pid"]."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
					$indirec = find_updated_record($node["pid"]);
				else
					$indirec = find_person_record($node["pid"]);
				$byear1 = -1;
				$birthrec = get_sub_record(1, "1 BIRT", $indirec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
					if ($dct>0)
						$byear1 = $match[1];
						if ($byear1>2100) $byear1-=3760; // Crude conversion from jewish to gregorian
				}
				if (($byear1!=-1)&&($byear2!=-1)) {
					$yeardiff = $byear1-$byear2;
					if ($yeardiff < -140) {
						$fatherh = 20;
						$motherh = 20;
						$siblingh = 15;
						$spouseh = 15;
						$childh = 1;
					} else
						if ($yeardiff < -100) {
							$fatherh = 15;
							$motherh = 15;
							$siblingh = 10;
							$spouseh = 10;
							$childh = 1;
						} else
							if ($yeardiff < -60) {
								$fatherh = 10;
								$motherh = 10;
								$siblingh = 5;
								$spouseh = 5;
								$childh = 1;
							} else
								if ($yeardiff < -20) {
									$fatherh = 5;
									$motherh = 5;
									$siblingh = 3;
									$spouseh = 3;
									$childh = 1;
								} else
									if ($yeardiff<20) {
										$fatherh = 3;
										$motherh = 3;
										$siblingh = 1;
										$spouseh = 1;
										$childh = 5;
									} else
										if ($yeardiff<60) {
											$fatherh = 1;
											$motherh = 1;
											$siblingh = 5;
											$spouseh = 2;
											$childh = 10;
										} else
											if ($yeardiff<100) {
												$fatherh = 1;
												$motherh = 1;
												$siblingh = 10;
												$spouseh = 3;
												$childh = 15;
											} else {
												$fatherh = 1;
												$motherh = 1;
												$siblingh = 15;
												$spouseh = 4;
												$childh = 20;
											}
				}
				//-- check all parents and siblings of this node
				$famids = array();
				$ct = preg_match_all("/1\sFAMC\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for ($i=0; $i<$ct; $i++) {
					if (!isset($visited[$match[$i][1]]))
						$famids[$i]=$match[$i][1];
				}
				foreach ($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
						$famrec = find_updated_record($fam);
					else
						$famrec = find_family_record($fam);
					$parents = find_parents_in_record($famrec);
					if ((!empty($parents["HUSB"]))&&(!isset($visited[$parents["HUSB"]]))) {
						$node1 = $node;
						$node1["length"]+=$fatherh;
						$node1["path"][] = $parents["HUSB"];
						$node1["pid"] = $parents["HUSB"];
						$node1["relations"][] = "father";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0)
								$path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						} else
							$visited[$parents["HUSB"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					if ((!empty($parents["WIFE"]))&&(!isset($visited[$parents["WIFE"]]))) {
						$node1 = $node;
						$node1["length"]+=$motherh;
						$node1["path"][] = $parents["WIFE"];
						$node1["pid"] = $parents["WIFE"];
						$node1["relations"][] = "mother";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0)
								$path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						} else
							$visited[$parents["WIFE"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for ($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$siblingh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "sibling";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else
								$visited[$child] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
				//-- check all spouses and children of this node
				$famids = array();
				$ct = preg_match_all("/1\sFAMS\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for ($i=0; $i<$ct; $i++) {
					$famids[$i]=$match[$i][1];
				}
				foreach ($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".$GEDCOM]) && PGV_USER_CAN_EDIT)
						$famrec = find_updated_record($fam);
					else
						$famrec = find_family_record($fam);
					if ($followspouse) {
						$parents = find_parents_in_record($famrec);
						if ((!empty($parents["HUSB"]))&&((!in_arrayr($parents["HUSB"], $node1))||(!isset($visited[$parents["HUSB"]])))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["HUSB"];
							$node1["pid"] = $parents["HUSB"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else
								$visited[$parents["HUSB"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
						if ((!empty($parents["WIFE"]))&&((!in_arrayr($parents["WIFE"], $node1))||(!isset($visited[$parents["WIFE"]])))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["WIFE"];
							$node1["pid"] = $parents["WIFE"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else
								$visited[$parents["WIFE"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for ($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$childh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "child";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else {
								$visited[$child] = true;
							}
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
			}
		}
		unset($p1nodes[$shortest]);
	} //-- end while loop
	if (headers_sent()) {
		print "\n<!-- Relationship $pid1-$pid2 | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
		print_execution_stats();
		print "-->\n";
	}
	return $resnode;
}

// Calculate the relationship between two individuals.
// Return false if unrelated, or a structure like this if they are:
//
// array(4) {
//   ["path"]=>
//   array(3) {
//     [0]=>
//     string(2) "I1"
//     [1]=>
//     string(2) "I2"
//     [2]=>
//     string(3) "I27"
//   }
//   ["length"]=>
//   int(2)
//   ["pid"]=>
//   string(3) "I27"
//   ["relations"]=>
//   array(3) {
//     [0]=>
//     string(4) "self"
//     [1]=>
//     string(6) "father"
//     [2]=>
//     string(5) "child"
//   }
// }
//
// This is a new/experimental version of get_relationship().  It is not used by any live
// code.  It is here to allow certain users to test it.
function get_relationship1($pid1, $pid2, $followspouse=true, $maxlength=0) {
	global $pgv_changes, $GEDCOM, $TBLPREFIX, $gBitDb;
	static $RELA=null;
	static $PATHS=null;

	// Read all the relationships into a memory cache
	if (is_null($RELA)) {
		$RELA=array();
		$rows = $gBitDb->query(
			"SELECT f_id, f_husb, f_wife, TRIM(TRAILING ? FROM f_chil) AS f_chil FROM {$TBLPREFIX}families WHERE f_file=?"
			, array(';', PGV_GED_ID));
		$families=array();
		while ( $row = $rows->fetchRow() ) {
			$families[$row['f_id']] = array( $row['f_husb'], $row['f_wife'], $row['f_chil'] );
		}
		foreach ($families as $f_id=>$family) {
			// Include pending changes
			if (PGV_USER_CAN_EDIT && isset($pgv_changes[$f_id."_".$GEDCOM])) {
				$famrec=find_updated_record($f_id);
				$families[$f_id][0]=(preg_match('/1 HUSB @(.*)@/', $famrec, $match)) ? $match[1] : '';
				$families[$f_id][1]=(preg_match('/1 WIFE @(.*)@/', $famrec, $match)) ? $match[1] : '';
				$families[$f_id][2]=(preg_match_all('/1 CHIL @(.*)@/', $famrec, $match)) ? $match[1] : array();
			} else {
				if ($families[$f_id][2]) {
					$families[$f_id][2]=explode(';', $families[$f_id][2]);
				} else {
					$families[$f_id][2]=array();
				}
			}
		}
		// Convert gedcom family structure into relationships between individuals
		foreach ($families as $f_id=>$family) {
			if (count($family[2])>1) {
				foreach ($family[2] as $child1) {
					foreach ($family[2] as $child2) {
						if ($child1!=$child2) {
							$RELA[$child1][$child2]='sibling';
						}
					}
				}
			}
			if ($family[0]) {
				foreach ($family[2] as $child) {
					$RELA[$family[0]][$child]='child';
					$RELA[$child][$family[0]]='father';
				}
			}
			if ($family[1]) {
				foreach ($family[2] as $child) {
					$RELA[$family[1]][$child]='child';
					$RELA[$child][$family[1]]='mother';
				}
			}
			if ($followspouse && $family[0] && $family[1]) {
				$RELA[$family[0]][$family[1]]='spouse';
				$RELA[$family[1]][$family[0]]='spouse';
			}
		}
		unset($families);
		// $PATHS[n] = relationship paths of length n, for n>0
		// Just create n=1 for now.  Create longer ones as we need them.
		$PATHS=array();
		foreach ($RELA as $person1=>$relatives) {
			foreach ($relatives as $person2=>$relationship) {
				$PATHS[1][$person1][$person2]=array($person1, $person2);
			}
		}
	}

	// If $pid1 doesn't exist, give up here
	if (!isset($PATHS[1][$pid1])) {
		return false;
	}

	// Search for paths of lengths 1,2,3,...
	for ($n=1; ; ++$n) {
		// Nothing found within the required path length
		if ($maxlength && $maxlength<$n) {
			return false;
		}
		// If we haven't yet looked at paths of length n, do it now
		if (!isset($PATHS[$n][$pid1])) {
			$PATHS[$n][$pid1]=array();
			$p=$n-2;
			// For each path, extend it to all the next level of relatives
			foreach ($PATHS[$n-1][$pid1] as $p2=>$path) {
				foreach ($RELA[$p2] as $p3=>$rela) {
					// If the new relative is not previously visited (avoid circular loops) and
					// if not in the same family as the prev link (to avoid mother-spouse as option for father)
					if (!in_array($p3, $path) && !array_key_exists($path[$p], $RELA[$p3])) {
						$PATHS[$n][$pid1][$p3]=$path;
						$PATHS[$n][$pid1][$p3][]=$p3;
					}
				}
			}

			// We didn't extend any path
			if (!isset($PATHS[$n][$pid1])) {
				return false;
			}
		}

		// Does a path of length n exist?
		if (isset($PATHS[$n][$pid1][$pid2])) {
			return true;
		}
	}
}

function get_relationship2($pid1, $pid2, $followspouse=true, $maxlength=0, $ignore_cache=false, $path_to_find=0) {
	global $pgv_changes, $GEDCOM, $TBLPREFIX, $gBitDb;
	static $RELA=null;
	static $PATHS=null;

	// Read all the relationships into a memory cache
	if (is_null($RELA)) {
		$RELA=array();
		$rows = $gBitDb->query(
			"SELECT f_id, f_husb, f_wife, TRIM(TRAILING ? FROM f_chil) AS f_chil FROM {$TBLPREFIX}families WHERE f_file=?"
			, array(';', PGV_GED_ID));
		$families=array();
		while ( $row = $rows->fetchRow() ) {
			$families[$row['f_id']]=array($row['f_husb'], $row['f_wife'], $row['f_chil']);
		}
		foreach ($families as $f_id=>$family) {
			// Include pending changes
			if (PGV_USER_CAN_EDIT && isset($pgv_changes[$f_id."_".$GEDCOM])) {
				$famrec=find_updated_record($f_id);
				$families[$f_id][0]=(preg_match('/1 HUSB @(.*)@/', $famrec, $match)) ? $match[1] : '';
				$families[$f_id][1]=(preg_match('/1 WIFE @(.*)@/', $famrec, $match)) ? $match[1] : '';
				$families[$f_id][2]=(preg_match_all('/1 CHIL @(.*)@/', $famrec, $match)) ? $match[1] : array();
			} else {
				if ($families[$f_id][2]) {
					$families[$f_id][2]=explode(';', $families[$f_id][2]);
				} else {
					$families[$f_id][2]=array();
				}
			}
		}
		// Convert gedcom family structure into relationships between individuals
		foreach ($families as $f_id=>$family) {
			if (count($family[2])>1) {
				foreach ($family[2] as $child1) {
					foreach ($family[2] as $child2) {
						if ($child1!=$child2) {
							$RELA[$child1][$child2]='sibling';
						}
					}
				}
			}
			if ($family[0]) {
				foreach ($family[2] as $child) {
					$RELA[$family[0]][$child]='child';
					$RELA[$child][$family[0]]='father';
				}
			}
			if ($family[1]) {
				foreach ($family[2] as $child) {
					$RELA[$family[1]][$child]='child';
					$RELA[$child][$family[1]]='mother';
				}
			}
			if ($followspouse && $family[0] && $family[1]) {
				$RELA[$family[0]][$family[1]]='spouse';
				$RELA[$family[1]][$family[0]]='spouse';
			}
		}
		unset($families);
		// $PATHS[n] = relationship paths of length n, for n>0
		// Just create n=1 for now.  Create longer ones as we need them.
		$PATHS=array();
		foreach ($RELA as $person1=>$relatives) {
			foreach ($relatives as $person2=>$relationship) {
				$PATHS[1][$person1][$person2][]=array($person1, $person2);
			}
		}
	}

	// If $pid1 doesn't exist, give up here
	if (!isset($PATHS[1][$pid1])) {
		return false;
	}

	// Search for paths of lengths 1,2,3,...
	for ($n=1; ; ++$n) {
		// Nothing found within the required path length
		if ($maxlength && $maxlength<$n) {
			return false;
		}
		// If we haven't yet looked at paths of length n, do it now
		if (!isset($PATHS[$n][$pid1])) {
			$PATHS[$n][$pid1]=array();
			$p=$n-2;
			// For each path, extend it to all the next level of relatives
			foreach ($PATHS[$n-1][$pid1] as $p2=>$paths) {
				foreach ($RELA[$p2] as $p3=>$rela) {
					// If the new relative is not previously visited (avoid circular loops) and
					// if not in the same family as the prev link (to avoid mother-spouse as option for father)
					foreach ($paths as $num=>$path) {
						if (!in_array($p3, $path) && !array_key_exists($path[$p], $RELA[$p3])) {
							$PATHS[$n][$pid1][$p3][$num]=$path;
							$PATHS[$n][$pid1][$p3][$num][]=$p3;
						}
					}
				}
			}

			// We didn't extend any path
			if (!isset($PATHS[$n][$pid1])) {
				return false;
			}
		}

		// Does a path of length n exist?
		if (isset($PATHS[$n][$pid1][$pid2])) {
			foreach ($PATHS[$n][$pid1][$pid2] as $path) {
				if ($path_to_find) {
					--$path_to_find;
				} else {
					$return=array('path'=>$path, 'length'=>$n, 'pid'=>$pid2, 'relations'=>array());
					foreach ($path as $n=>$id) {
						if ($n==0) {
							$return['relations'][]='self';
						} else {
							$return['relations'][]=$RELA[$path[$n-1]][$path[$n]];
						}
					}
					return $return;
				}
			}
		}
	}
}

/**
 * write changes
 *
 * this function writes the $pgv_changes back to the <var>$INDEX_DIRECTORY</var>/pgv_changes.php
 * file so that it can be read in and checked to see if records have been updated.  It also stores
 * old records so that they can be undone.
 * @return bool true if successful false if there was an error
 */
function write_changes() {
	global $pgv_changes, $INDEX_DIRECTORY, $CONTACT_EMAIL, $LAST_CHANGE_EMAIL;

	//-- only allow 1 thread to write changes at a time
//	$mutex = new Mutex("pgv_changes");
//	$mutex->Wait();
	//-- what to do if file changed while waiting
	if (!isset($LAST_CHANGE_EMAIL))
		$LAST_CHANGE_EMAIL = time();
	//-- write the changes file
	$changestext = "<?php\n\$LAST_CHANGE_EMAIL = $LAST_CHANGE_EMAIL;\n\$pgv_changes = array();\n";
	foreach ($pgv_changes as $gid=>$changes) {
		if (count($changes)>0) {
			$changestext .= "\$pgv_changes[\"$gid\"] = array();\n";
			foreach ($changes as $indexval => $change) {
				$changestext .= "// Start of change record.\n";
				$changestext .= "\$change = array();\n";
				$changestext .= "\$change[\"gid\"] = '".$change["gid"]."';\n";
				$changestext .= "\$change[\"gedcom\"] = '".$change["gedcom"]."';\n";
				$changestext .= "\$change[\"type\"] = '".$change["type"]."';\n";
				$changestext .= "\$change[\"status\"] = '".$change["status"]."';\n";
				$changestext .= "\$change[\"user\"] = '".$change["user"]."';\n";
				$changestext .= "\$change[\"time\"] = '".$change["time"]."';\n";
				if (isset($change["linkpid"]))
					$changestext .= "\$change[\"linkpid\"] = '".$change["linkpid"]."';\n";
				$changestext .= "\$change[\"undo\"] = '".str_replace("\\\\'", "\\'", preg_replace("/'/", "\\'", $change["undo"]))."';\n";
				$changestext .= "// End of change record.\n";
				$changestext .= "\$pgv_changes[\"$gid\"][] = \$change;\n";
			}
		}
	}
	$fp = fopen($INDEX_DIRECTORY."pgv_changes.php", "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open changes file resource.  Unable to complete request.\n";
		return false;
	}
	$fw = fwrite($fp, $changestext);
	if ($fw===false) {
		print "ERROR 7: Unable to write to changes file.\n";
		fclose($fp);
		return false;
	}
	fclose($fp);

	//-- release the mutex acquired above
//	$mutex->Release();

	$logline = AddToLog("pgv_changes.php updated");
	check_in($logline, "pgv_changes.php", $INDEX_DIRECTORY);
	return true;
}

/**
 * get theme names
 *
 * function to get the names of all of the themes as an array
 * it searches the themes directory and reads the name from the theme_name variable
 * in the theme.php file.
 * @return array and array of theme names and their corresponding directory
 */
function get_theme_names() {
	$themes = array();
	$d = dir("themes");
	while (false !== ($entry = $d->read())) {
		if ($entry{0}!="." && $entry!="CVS" && !stristr($entry, "svn") && is_dir("themes/$entry") && file_exists("themes/$entry/theme.php")) {
			$themefile = implode("", file("themes/$entry/theme.php"));
			$tt = preg_match("/theme_name\s+=\s+\"(.*)\";/", $themefile, $match);
			if ($tt>0)
				$themename = trim($match[1]);
			else
				$themename = "themes/$entry";
			$themes[$themename] = "themes/$entry/";
		}
	}
	$d->close();
	uksort($themes, "stringsort");
	return $themes;
}

/**
 * decode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to decode the filename
 * before it can be used on the filesystem
 */
function filename_decode($filename) {
	if (DIRECTORY_SEPARATOR=='\\')
		return utf8_decode($filename);
	else
		return $filename;
}

/**
 * encode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to encode the filename
 * before it can be used in PGV
 */
function filename_encode($filename) {
	if (DIRECTORY_SEPARATOR=='\\')
		return utf8_encode($filename);
	else
		return $filename;
}

////////////////////////////////////////////////////////////////////////////////
// Remove empty and duplicate values from a URL query string
////////////////////////////////////////////////////////////////////////////////
function normalize_query_string($query) {
	$components=array();
	foreach (preg_split('/(^\?|\&(amp;)*)/', urldecode($query), -1, PREG_SPLIT_NO_EMPTY) as $component)
		if (strpos($component, '=')!==false) {
			list ($key, $data)=explode('=', $component, 2);
			if (!empty($data)) $components[$key]=$data;
		}
	$new_query='';
	foreach ($components as $key=>$data)
		$new_query.=(empty($new_query)?'?':'&amp;').$key.'='.$data;

	return $new_query;
}

function getAlphabet() {
	global $ALPHABET_upper, $ALPHABET_lower, $LANGUAGE;
	global $alphabet, $alphabet_lower, $alphabet_upper, $alphabet_lang;

	//-- setup the language alphabet string
	if (!isset($alphabet_lang) || $alphabet_lang!=$LANGUAGE) {
		$alphabet = "0123456789".$ALPHABET_upper[$LANGUAGE].$ALPHABET_lower[$LANGUAGE];
		$alphabet_lower = "0123456789".$ALPHABET_lower[$LANGUAGE];
		$alphabet_upper = "0123456789".$ALPHABET_upper[$LANGUAGE];
		foreach ($ALPHABET_upper as $l => $upper) {
			if ($l <> $LANGUAGE) {
				$alphabet .= $ALPHABET_upper[$l];
				$alphabet_upper .= $ALPHABET_upper[$l];
				$alphabet .= $ALPHABET_lower[$l];
				$alphabet_lower .= $ALPHABET_lower[$l];
			}
		}
		$alphabet_lang = $LANGUAGE;
	}
	return $alphabet;
}

/**
 * get a list of the reports in the reports directory
 *
 * When $force is false, the function will first try to read the reports list from the$INDEX_DIRECTORY."/reports.dat"
 * data file.  Otherwise the function will parse the report xml files and get the titles.
 * @param boolean $force	force the code to look in the directory and parse the files again
 * @return array 	The array of the found reports with indexes [title] [file]
 */
function get_report_list($force=false) {
	global $INDEX_DIRECTORY, $report_array, $vars, $xml_parser, $elementHandler, $LANGUAGE;

	$files = array();
	if (!$force) {
		//-- check if the report files have been cached
		if (file_exists($INDEX_DIRECTORY."/reports.dat")) {
			$reportdat = "";
			$fp = fopen($INDEX_DIRECTORY."/reports.dat", "r");
			while ($data = fread($fp, 4096)) {
				$reportdat .= $data;
			}
			fclose($fp);
			$files = unserialize($reportdat);
			foreach ($files as $indexval => $file) {
				if (isset($file["title"][$LANGUAGE]) && (strlen($file["title"][$LANGUAGE])>1))
					return $files;
			}
		}
	}

	//-- find all of the reports in the reports directory
	$d = dir("reports");
	while (false !== ($entry = $d->read())) {
		if (($entry{0}!=".") && ($entry!="CVS") && (preg_match('/\.xml$/i', $entry)>0)) {
			if (!isset($files[$entry]["file"]))
				$files[$entry]["file"] = "reports/".$entry;
		}
	}
	$d->close();

	require_once("../reportheader.php");
	$report_array = array();
	if (!function_exists("xml_parser_create"))
		return $report_array;
	foreach ($files as $file=>$r) {
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		if (file_exists($r["file"])) {
			//-- open the file
			if (!($fp = fopen($r["file"], "r"))) {
				die("could not open XML input");
			}
			//-- read the file and parse it 4kb at a time
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
				}
			}
			fclose($fp);
			xml_parser_free($xml_parser);
			if (isset($report_array["title"]) && isset($report_array["access"]) && isset($report_array["icon"])) {
				$files[$file]["title"][$LANGUAGE] = $report_array["title"];
				$files[$file]["access"] = $report_array["access"];
				$files[$file]["icon"] = $report_array["icon"];
			}
		}
	}

	$fp = @fopen($INDEX_DIRECTORY."/reports.dat", "w");
	@fwrite($fp, serialize($files));
	@fclose($fp);
	$logline = AddToLog("reports.dat updated");
 	check_in($logline, "reports.dat", $INDEX_DIRECTORY);

	return $files;
}

function getfilesize($bytes) {
	if ($bytes>=1099511627776) {
		return round($bytes/1099511627776, 2)." TB";
	}
	if ($bytes>=1073741824) {
		return round($bytes/1073741824, 2)." GB";
	}
	if ($bytes>=1048576) {
		return round($bytes/1048576, 2)." MB";
	}
	if ($bytes>=1024) {
		return round($bytes/1024, 2)." KB";
	}
	return $bytes." B";
}

/**
 * split multi-ged keys and return either key or gedcom
 *
 * @param string $key		the multi-ged key to be split
 * @param string $type		either "id" or "ged", depending on what must be returned
 * @return string			either the key or the gedcom name
 */
function splitkey($key, $type) {
	$p1 = strpos($key,"[");
	$id = substr($key,0,$p1);
	if ($type == "id")
		return $id;
	$p2 = strpos($key,"]");
	$ged = substr($key,$p1+1,$p2-$p1-1);
	if ($ged>=1)
		get_gedcom_from_id($ged);
	return $ged;
}

/**
 * array merge function for PGV
 * the PHP array_merge function will reindex all numerical indexes
 * This function should only be used for associative arrays
 * @param array $array1
 * @param array $array2
 */
function pgv_array_merge($array1, $array2) {
	foreach ($array2 as $key=>$value) {
		$array1[$key] = $value;
	}
	return $array1;
}

/**
 * checks if the value is in an array recursively
 * @param string $needle
 * @param array $haystack
 */
function in_arrayr($needle, $haystack) {
	foreach ($haystack as $v) {
		if ($needle == $v) return true;
		else if (is_array($v)) {
			if (in_arrayr($needle, $v) === true) return true;
		}
	}
	return false;
}

/**
 * function to build an URL querystring from GET or POST variables
 * @return string
 */
function get_query_string() {
	$qstring = "";
	if (!empty($_GET)) {
		foreach ($_GET as $key => $value) {
			if ($key != "view") {
				if (!is_array($value))
					$qstring .= "{$key}={$value}&";
				else
					foreach ($value as $k=>$v)
						$qstring .= "{$key}[{$k}]={$v}&";
			}
		}
	} else {
		if (!empty($_POST)) {
			foreach ($_POST as $key => $value) {
				if ($key != "view") {
					if (!is_array($value))
						$qstring .= "{$key}={$value}&";
					else
						foreach ($value as $k=>$v)
							if (!is_array($v))
								$qstring .= "{$key}[{$k}]={$v}&";
				}
			}
		}
	}
	$qstring = rtrim($qstring, "&");	// Remove trailing "&"
	return encode_url($qstring);
}

//This function works with a specified generation limit.  It will completely fill
//the pdf witout regard to if a known person exists in each generation.
//ToDo: If a known individual is found in a generation, add prior empty positions
//and add remaining empty spots automatically.
function add_ancestors(&$list, $pid, $children=false, $generations=-1, $show_empty=false) {
	$total_num_skipped = 0;
	$skipped_gen = 0;
	$num_skipped = 0;
	$genlist = array($pid);
	$list[$pid]->generation = 1;
	while (count($genlist)>0) {
		$id = array_shift($genlist);
		if (strpos($id,"empty")===0) continue; // id can be something like "empty7"
		$person = Person::getInstance($id);
		$famids = $person->getChildFamilies();
		if (count($famids)>0) {
			if ($show_empty) {
				for ($i=0;$i<$num_skipped;$i++) {
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					array_push($genlist, "empty" . $total_num_skipped);
					$total_num_skipped++;
				}
			}
			$num_skipped = 0;
			foreach ($famids as $famid => $family) {
				$husband = $family->getHusband();
				$wife = $family->getWife();
				if ($husband) {
					$list[$husband->getXref()] = $husband;
					$list[$husband->getXref()]->generation = $list[$id]->generation+1;
				} else
					if ($show_empty) {
						$list["empty" . $total_num_skipped] = new Person('');
						$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					}
				if ($wife) {
					$list[$wife->getXref()] = $wife;
					$list[$wife->getXref()]->generation = $list[$id]->generation+1;
				} else
					if ($show_empty) {
						$list["empty" . $total_num_skipped] = new Person('');
						$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					}
				if ($generations == -1 || $list[$id]->generation+1 < $generations) {
					$skipped_gen = $list[$id]->generation+1;
					if ($husband)
						array_push($genlist, $husband->getXref());
					else
						if ($show_empty)
							array_push($genlist, "empty" . $total_num_skipped);
					if ($wife)
						array_push($genlist, $wife->getXref());
					else
						if ($show_empty)
							array_push($genlist, "empty" . $total_num_skipped);
				}
				$total_num_skipped++;
				if ($children) {
					$childs = $family->getChildren();
					foreach($childs as $child) {
						$list[$child->getXref()] = $child;
						if (isset($list[$id]->generation))
							$list[$child->getXref()]->generation = $list[$id]->generation;
						else
							$list[$child->getXref()]->generation = 1;
					}
				}
			}
		} else
			if ($show_empty) {
				if ($skipped_gen > $list[$id]->generation) {
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					$total_num_skipped++;
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					array_push($genlist, "empty" . ($total_num_skipped - 1));
					array_push($genlist, "empty" . $total_num_skipped);
					$total_num_skipped++;
				} else
					$num_skipped += 2;
		}

	}
}

//--- copied from class_reportpdf.php
function add_descendancy(&$list, $pid, $parents=false, $generations=-1) {
	$person = Person::getInstance($pid);
	if ($person==null) return;
	if (!isset($list[$pid])) {
		$list[$pid] = $person;
	}
	if (!isset($list[$pid]->generation)) {
		$list[$pid]->generation = 0;
	}
	$famids = $person->getSpouseFamilies();
	if (count($famids)>0) {
		foreach ($famids as $famid => $family) {
			if ($family) {
				if ($parents) {
					$husband = $family->getHusband();
					$wife = $family->getWife();
					if ($husband) {
						$list[$husband->getXref()] = $husband;
						if (isset($list[$pid]->generation))
							$list[$husband->getXref()]->generation = $list[$pid]->generation-1;
						else
							$list[$husband->getXref()]->generation = 1;
					}
					if ($wife) {
						$list[$wife->getXref()] = $wife;
						if (isset($list[$pid]->generation))
							$list[$wife->getXref()]->generation = $list[$pid]->generation-1;
						else
							$list[$wife->getXref()]->generation = 1;
					}
				}
				$children = $family->getChildren();
				foreach($children as $child) {
					if ($child) {
						$list[$child->getXref()] = $child;
						if (isset($list[$pid]->generation))
							$list[$child->getXref()]->generation = $list[$pid]->generation+1;
						else
							$list[$child->getXref()]->generation = 2;
					}
				}
				if ($generations == -1 || $list[$pid]->generation+1 < $generations) {
					foreach($children as $child) {
						add_descendancy($list, $child->getXref(), $parents, $generations);	// recurse on the childs family
					}
				}
			}
		}
	}
}

/**
 * check if the page view rate for a session has been exeeded.
 */
function CheckPageViews() {
	global $SEARCH_SPIDER, $MAX_VIEWS, $MAX_VIEW_TIME;

	if ($MAX_VIEW_TIME == 0 || $MAX_VIEWS == 0 || !empty($SEARCH_SPIDER))
		return;

	// The media firewall should not be throttled
	if (strpos($_SERVER["SCRIPT_NAME"],"mediafirewall") > -1)
		return;

	if (!empty($_SESSION["pageviews"]["time"]) && !empty($_SESSION["pageviews"]["number"])) {
		$_SESSION["pageviews"]["number"] ++;
		if ($_SESSION["pageviews"]["number"] < $MAX_VIEWS)
			return;
		$sleepTime = $MAX_VIEW_TIME - time() + $_SESSION["pageviews"]["time"];
		if ($sleepTime > 0) {
			// The configured page view rate has been exceeded
			// - Log a message and then sleep to slow things down
			$text = print_text("maxviews_exceeded", 0, 1);
			AddToLog($text);
			sleep($sleepTime);
		}
	}
	$_SESSION["pageviews"] = array("time"=>time(), "number"=>1);
}

/**
 * get the next available xref
 * calculates the next available XREF id for the given type of record
 * @param string $type	the type of record, defaults to 'INDI'
 * @return string
 */
function get_new_xref($type='INDI', $use_cache=false) {
	global $fcontents, $SOURCE_ID_PREFIX, $REPO_ID_PREFIX, $pgv_changes, $GEDCOM, $TBLPREFIX, $GEDCOMS;
	global $MEDIA_ID_PREFIX, $FAM_ID_PREFIX, $GEDCOM_ID_PREFIX, $FILE, $MAX_IDS;
	global $gBitDb;
	
	//-- during online updates $FILE comes through as an array for some odd reason
	if (!empty($FILE) && !is_array($FILE)) {
		$gedid = $GEDCOMS[$FILE]["id"];
	} else
		$gedid = $GEDCOMS[$GEDCOM]["id"];

	$num = null;
	//-- check if an id is stored in MAX_IDS used mainly during the import
	//-- the number stored in the max_id is the next number to use... no need to increment it
	if ($use_cache && !empty($MAX_IDS)&& isset($MAX_IDS[$type])) {
		$num = 1;
		$num = $MAX_IDS[$type];
		$MAX_IDS[$type] = $num+1;
	} else {
		//-- check for the id in the nextid table
		$num = $gBitDb->getOne(
			"SELECT ni_id FROM {$TBLPREFIX}nextid WHERE ni_type=? AND ni_gedfile=?"
			, array($type, $gedid));

		//-- the id was not found in the table so try and find it in the file
		if (is_null($num) && !empty($fcontents)) {
			$ct = preg_match_all("/0 @(.*)@ $type/", $fcontents, $match, PREG_SET_ORDER);
			$num = 0;
			for ($i=0; $i<$ct; $i++) {
				$ckey = $match[$i][1];
				$bt = preg_match("/(\d+)/", $ckey, $bmatch);
				if ($bt>0) {
					$bnum = trim($bmatch[1]);
					if ($num < $bnum)
						$num = $bnum;
				}
			}
			$num++;
		}
		//-- type wasn't found in database or in file so make a new one
		if (is_null($num)) {
			$num = 1;
			$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}nextid VALUES(?, ?, ?)"
				, array($num+1, $type, $gedid));
		}
	}

	switch ($type) {
	case "INDI":
		$prefix = $GEDCOM_ID_PREFIX;
		break;
	case "FAM":
		$prefix = $FAM_ID_PREFIX;
		break;
	case "OBJE":
		$prefix = $MEDIA_ID_PREFIX;
		break;
	case "SOUR":
		$prefix = $SOURCE_ID_PREFIX;
		break;
	case "REPO":
		$prefix = $REPO_ID_PREFIX;
		break;
	default:
		$prefix = $type{0};
		break;
	}

	//-- make sure this number has not already been used
	if ($num>=2147483647 || $num<=0) { // Popular databases are only 32 bits (signed)
		$num=1;
	}
	while (find_gedcom_record($prefix.$num) || find_updated_record($prefix.$num)) {
		++$num;
		if ($num>=2147483647 || $num<=0) { // Popular databases are only 32 bits (signed)
			$num=1;
		}
	}

	//-- the key is the prefix and the number
	$key = $prefix.$num;

	//-- during the import we won't update the database at this time so return now
	if ($use_cache && isset($MAX_IDS[$type])) {
		return $key;
	}
	//-- update the next id number in the DB table
	$gBitDb->query(
		"UPDATE {$TBLPREFIX}nextid SET ni_id=? WHERE ni_type=? AND ni_gedfile=?"
		, array($num+1, $type, $gedid));
	return $key;
}

/**
 * check if the given string has UTF-8 characters
 *
 */
function has_utf8($string) {
	$len = strlen($string);
	for ($i=0; $i<$len; $i++) {
		$letter = substr($string, $i, 1);
		$ord = ord($letter);
		if ($ord==95 || $ord>=195)
			return true;
	}
	return false;
}

/**
 * check file in
 * @param  string  $logline  Log message
 * @param  string  $filename Filename
 * @param  string  $dirname  Directory
 * @param  boolean $bInsert  Insert Log message
 * @return boolean whether the file was checked in
 */
function check_in($logline, $filename, $dirname, $bInsert = false) {
	global $COMMIT_COMMAND;
	$bRetSts = false;
	if (!empty($COMMIT_COMMAND) && ($COMMIT_COMMAND=='svn' || $COMMIT_COMMAND=='cvs') && $logline && $filename) {
		$cwd = getcwd();
		if ($dirname) {
			chdir($dirname);
		}
		$cmdline= $COMMIT_COMMAND.' commit -m '.escapeshellarg($logline).' '.escapeshellarg($filename);
		$output = '';
		$retval = '';
		exec($cmdline, $output, $retval);
		if (!empty($output)) {
			if ($bInsert) {
				AddToChangeLog($logline);
			}
			$outputstring = implode(' ', $output);
			AddToChangeLog('System Output :'.$outputstring.', Return Value :'.$retval);
			$bRetSts = true;
		}
		if ($dirname) {
			chdir($cwd);
		}
	}
	return $bRetSts;
}

/**
 *	Load language files
 *	Load the contents of a specified language file
 *
 *	The input parameter lists the types of language files that should be loaded.
 *
 *	This routine will always load the English version of the specified language
 *	files first, followed by the same set of files in the currently active language.
 *	After that, the "extra.xx.php" files will always be loaded, but not trying for
 *	English first.
 *
 *	To load the "help_text.xx.php" file set, you'd call this function thus:
 *		loadLangFile("pgv_help");
 *	To load the "configure_help.xx.php" and the "faqlist.xx.php" file set, the function
 *	would be called thus:
 *		loadLangFile("pgv_confighelp, pgv_faqlib");
 *	To load all files, call the function this way:
 *		loadLangFile("all");
 *	To load the file XXX for module YYY, call
 *		loadLangFile("YYY:XXX");
 */
function loadLangFile($fileListNames="", $lang="") {
	global $pgv_language, $confighelpfile, $helptextfile, $factsfile, $adminfile, $editorfile, $countryfile, $faqlistfile, $extrafile;
	global $LANGUAGE, $lang_short_cut;
	global $pgv_lang, $countries, $altCountryNames, $factarray, $factAbbrev, $faqlist;
	if (empty($lang)) $lang=$LANGUAGE;
	$allLists = "pgv_lang, pgv_confighelp, pgv_help, pgv_facts, pgv_admin, pgv_editor, pgv_country, pgv_faqlib";

	// Empty list or "all" means "load complete file set"
	if (empty($fileListNames) || $fileListNames=="all")
		$fileListNames = $allLists;

	// Split input into a list of file types
	$fileListNames = str_replace(array(";", " "), array(",", ""), $fileListNames);
	$list = explode(",", $fileListNames);

	// Work on each input file type
	foreach ($list as $fileListName) {
		switch ($fileListName) {
		case "pgv_lang":
			$fileName1 = $pgv_language["english"];
			$fileName2 = $pgv_language[$lang];
			break;
		case "pgv_confighelp":
			$fileName1 = $confighelpfile["english"];
			$fileName2 = $confighelpfile[$lang];
			break;
		case "pgv_help":
			$fileName1 = $helptextfile["english"];
			$fileName2 = $helptextfile[$lang];
			break;
		case "pgv_facts":
			$fileName1 = $factsfile["english"];
			$fileName2 = $factsfile[$lang];
			break;
		case "pgv_admin":
			$fileName1 = $adminfile["english"];
			$fileName2 = $adminfile[$lang];
			break;
		case "pgv_editor":
			$fileName1 = $editorfile["english"];
			$fileName2 = $editorfile[$lang];
			break;
		case "pgv_country":
			$fileName1 = $countryfile["english"];
			$fileName2 = $countryfile[$lang];
			break;
		case "pgv_faqlib":
			$fileName1 = $faqlistfile["english"];
			$fileName2 = $faqlistfile[$lang];
			break;
		default:
			if (preg_match('/(.+):(.+)/', $fileListName, $match)) {
				$fileName1 = 'modules/'.$match[1].'/languages/'.$match[2].'.'.$lang_short_cut['english'].'.php';
				$fileName2 = 'modules/'.$match[1].'/languages/'.$match[2].'.'.$lang_short_cut[$lang].'.php';
				break;
			} else {
				continue 2;
			}
		}
		if (file_exists($fileName1)) {
			require $fileName1;
		}
		if ($lang!='english' && file_exists($fileName2)) {
			require $fileName2;
		}
	}

	// Now that the variables have been loaded in the desired language, load the optional
	// "extra.xx.php" file so that they can be over-ridden as desired by the site Admin
	// For compatibility reasons, we'll first look for optional file "lang.xx.extra.php"
	//
	// In contrast to the preceding logic, we will NOT first load the English extra.xx.php
	// file when trying for other languages.
	//
	if (file_exists("languages/lang.".$lang_short_cut[$lang].".extra.php")) {
		require "languages/lang.".$lang_short_cut[$lang].".extra.php";
	}
	if (file_exists($extrafile[$lang])) {
		require $extrafile[$lang];
	}

}


/**
 *	Load language variables
 *	Set language-dependent global variables
 *
 *	This function loads the variables for the language, as specified by the first
 *	input parameter. It also loads any existing language-specific functions such
 *	special date handling for Finnish and Turkish.
 *
 *	If the forceLoad parameter is true, English will be loaded first, followed by
 *	the desired language file.
 *
 */
function loadLanguage($desiredLanguage="english", $forceLoad=false) {
	global $LANGUAGE, $lang_short_cut;
	global $pgv_lang, $countries, $altCountryNames, $factarray, $factAbbrev, $faqlist;
	global $pgv_language, $factsfile, $adminfile, $editorfile, $extrafile, $pgv_lang_self;
	global $TEXT_DIRECTION, $TEXT_DIRECTION_array;
	global $DATE_FORMAT, $DATE_FORMAT_array, $CONFIGURED;
	global $TIME_FORMAT, $TIME_FORMAT_array;
	global $WEEK_START, $WEEK_START_array;
	global $NAME_REVERSE, $NAME_REVERSE_array;
	global $MULTI_LETTER_ALPHABET, $digraph, $trigraph, $quadgraph, $digraphAll, $trigraphAll, $quadgraphAll;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;
	global $unknownNN, $unknownPN;
	global $JEWISH_ASHKENAZ_PRONUNCIATION, $CALENDAR_FORMAT;
	global $DBTYPE, $DB_UTF8_COLLATION, $COLLATION, $DBCOLLATE;
	global $gBitDb;

	// Need to change the collation sequence each time we change language
	if ($DB_UTF8_COLLATION) {
		switch ($DBTYPE) {
		case 'mysql':
		case 'mysqli':
			$DBCOLLATE="COLLATE {$COLLATION[$LANGUAGE]}";
			break;
		default:
			$DBCOLLATE='';
			break;
		}
	} else {
		$DBCOLLATE='';
	}

	if (!isset($pgv_language[$desiredLanguage])) $desiredLanguage = "english";

	// Make sure we start with a clean slate
	$pgv_lang = $pgv_lang_self;
	$countries = array();
	$altCountryNames = array();
	$factarray = array();
	$factAbbrev = array();
	$faqlist = array();

	if ($forceLoad) {
		$LANGUAGE = "english";
		require( PHPGEDVIEW_PKG_PATH.$pgv_language[$LANGUAGE] );			// Load English
		require( PHPGEDVIEW_PKG_PATH.$factsfile[$LANGUAGE] );

		$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
		$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
		$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
		$WEEK_START		= $WEEK_START_array[$LANGUAGE];
		$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

		// Load functions that are specific to the active language
		$file = PHPGEDVIEW_PKG_PATH."includes/extras/functions.".$lang_short_cut[$LANGUAGE].".php";
		if (file_exists($file)) {
			include_once($file);
		}
		// load admin lang keys
		$file = $adminfile[$LANGUAGE];
		if (file_exists($file)) {
			if (!$CONFIGURED || !empty( $gBitDb->mDb ) || !adminUserExists() || PGV_USER_GEDCOM_ADMIN) {
				include($file);
			}
		}
		// load the edit lang keys
		$file = $editorfile[$LANGUAGE];
		if (file_exists($file)) {
			if (!empty( $gBitDb->mDb ) || !adminUserExists() || PGV_USER_GEDCOM_ADMIN || PGV_USER_CAN_EDIT) {
				include($file);
			}
		}
	}

	if ($desiredLanguage!=$LANGUAGE) {
		$LANGUAGE = $desiredLanguage;
		$file = $pgv_language[$LANGUAGE];
		if (file_exists($file)) {
			include($file);		// Load the requested language
		}
		$file = $factsfile[$LANGUAGE];
		if (file_exists($file)) {
			include($file);
		}

		$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
		$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
		$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
		$WEEK_START		= $WEEK_START_array[$LANGUAGE];
		$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

		// Load functions that are specific to the active language
		$file = "./includes/extras/functions.".$lang_short_cut[$LANGUAGE].".php";
		if (file_exists($file)) {
			include_once($file);
		}

		// load admin lang keys
		$file = $adminfile[$LANGUAGE];
		if (file_exists($file)) {
			if (!$CONFIGURED || empty($gBitDb->mDb) || !adminUserExists() || PGV_USER_GEDCOM_ADMIN) {
				include($file);
			}
		}
		// load the edit lang keys
		$file = $editorfile[$LANGUAGE];
		if (file_exists($file)) {
			if ( empty($gBitDb->mDb) || !adminUserExists() || PGV_USER_CAN_EDIT) {
				include($file);
			}
		}
	}

	// load the extra language file
	$file = "./languages/lang.".$lang_short_cut[$LANGUAGE].".extra.php";
	if (file_exists($file)) {
		include($file);
	}
	$file = $extrafile[$LANGUAGE];
	if (file_exists($file)) {
		include($file);
	}

	// Modify certain spellings if Ashkenazi pronounciations are in use.
	if ($JEWISH_ASHKENAZ_PRONUNCIATION)
		switch($lang_short_cut[$LANGUAGE]) {
		case 'en':
			$pgv_lang['csh']='Cheshvan';
			$pgv_lang['tvt']='Teves';
			break;
	}

	// Special formatting options; R selects conversion to a language-dependent calendar.
	// i.e. a French user will see conversion to the french calendar, a Hebrew user will
	// see conversion to the hebrew calendar, etc.
	if (strpos($DATE_FORMAT, 'R')!==false) {
		switch ($LANGUAGE) {
		case 'french':
		case 'hebrew':
		case 'arabic':
			// Two ways of doing this:
			$CALENDAR_FORMAT=$LANGUAGE; // override gedcom calendar choice
			// if (strpos($CALENDAR_FORMAT, $LANGUAGE)===false) $CALENDAR_FORMAT.="_and_{$language}"; // add to gedcom calendar choice
			break;
		}
		$DATE_FORMAT=trim(str_replace('R', '', $DATE_FORMAT));
	}

/**
 *	Build the tables of multi-character sequences that must be considered as a
 *	single character when sorting lists of names and titles.
 *		Reference http://en.wikipedia.org/wiki/Hungarian_alphabet
 *		Reference http://en.wikipedia.org/wiki/Alphabets_derived_from_the_Latin
 */
	$digraph = array();
	$trigraph = array();
	$quadgraph = array();
	if (!isset($MULTI_LETTER_ALPHABET[$LANGUAGE]))
		$MULTI_LETTER_ALPHABET[$LANGUAGE] = "";
	if ($MULTI_LETTER_ALPHABET[$LANGUAGE]!="") {
		$myList = UTF8_strtoupper($MULTI_LETTER_ALPHABET[$LANGUAGE]);
		$myList = str_replace(array(";", ","), " ", $myList);
		$myList = preg_replace("/\s\s+/", " ", $myList);
		$myList = trim($myList);
		$wholeList = explode(" ", $myList);
		$sortValue = array();
		foreach ($wholeList as $letter) {
			$first = substr($letter, 0, 1);
			if ($letter=="CH")
				$first = "H";	// This one doesn't follow the rule
			if (!isset($sortValue[$first]))
				$sortValue[$first] = 0;
			$sortValue[$first] ++;
			if (strlen($letter)==2)
				$digraph[$letter] = $sortValue[$first];
			if (strlen($letter)==3)
				$trigraph[$letter] = $sortValue[$first];
			if (strlen($letter)==4)
				$quadgraph[$letter] = $sortValue[$first];
		}
		$MULTI_LETTER_ALPHABET[$LANGUAGE] = " ".$myList." ";
	}

	$digraphAll = array();
	$trigraphAll = array();
	$quadgraphAll = array();
	$MULTI_LETTER_ALPHABET["all"] = "";
	foreach ($MULTI_LETTER_ALPHABET as $lang => $letters) {
		if ($lang!="all")
			$MULTI_LETTER_ALPHABET["all"] .= $letters." ";
	}
	$MULTI_LETTER_ALPHABET["all"] = UTF8_strtoupper($MULTI_LETTER_ALPHABET["all"]);
	$MULTI_LETTER_ALPHABET["all"] = str_replace(array(";", ","), " ", $MULTI_LETTER_ALPHABET["all"]);
	$MULTI_LETTER_ALPHABET["all"] = preg_replace("/\s\s+/", " ", $MULTI_LETTER_ALPHABET["all"]);
	$wholeList = explode(" ", $MULTI_LETTER_ALPHABET["all"]);
	$sortValue = array();
	foreach ($wholeList as $letter) {
		$first = substr($letter, 0, 1);
		if ($letter=="CH")
			$first = "H";	// This one doesn't follow the rule
		if (!isset($sortValue[$first]))
			$sortValue[$first] = 0;
		$sortValue[$first] ++;
		if (strlen($letter)==2)
			$digraphAll[$letter] = $sortValue[$first];
		if (strlen($letter)==3)
			$trigraphAll[$letter] = $sortValue[$first];
		if (strlen($letter)==4)
			$quadgraphAll[$letter] = $sortValue[$first];
	}
	$MULTI_LETTER_ALPHABET["all"] = " ".trim($MULTI_LETTER_ALPHABET["all"])." ";


/**
 *	Build the tables required for the Dictionary sort
 *
 *	A Dictionary sort is one where all letters with diacritics are considered to be
 *	identical to the base letter (without the mark).  Diacritics become important
 *	only when the two strings (without marks) are identical.
 *
 *	There are two sets of tables, one for the Upper Case version of a UTF8 character
 *	and the other for the lower-case version.  The two tables are not necessarily
 *	identical.  For example, the Turkish dotless i doesn't exist in the Upper case
 *	table.
 *
 *	Within each set, there are three lists which MUST have a one-to-one relationship.
 *	The "DiacritStrip" list gives the base letter of the corresponding "DiacritWhole"
 *	character.
 *	The "DiacritOrder" list assigns a sort value to the diacritic mark of the
 *	"DiacritWhole" character.  All letters that don't appear in these lists, including
 *	the base letter from which the one bearing diacritic marks is formed, are assigned
 *	a sort value of " ".  By using a single letter from the ASCII code chart, we can
 *	have 52 different UTF8 characters all mapping to the same base character.  This will
 *	handle Vietnamese, which is by far the richest language in terms of diacritic marks.
 */
 	require_once PHPGEDVIEW_PKG_PATH."/includes/sort_tables_utf8.php";
}

/**
 * determines whether the passed in filename is a link to an external source (i.e. contains '://')
 */
function isFileExternal($file) {
	return strpos($file, '://') !== false;
}

/*
 * Encrypt the input string
 *
 * This function is used when a file name needs to be passed to another script by means of the
 * GET method.  This method passes parameters to the script through the URL that launches the
 * script.
 *
 * File names could themselves be legitimate URLs.  These legitimate URLs would normally be
 * killed by the hacker detection code in "includes/session_spider.php".  This method avoids
 * that problem.
 *
 */
function encrypt($string, $key='') {
	if (empty($key)) $key = session_id();
	$result = '';

	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$newOrd = ord($char) + ord($keychar);
		if ($newOrd > 255) $newOrd -= 256;		// Make sure we stay within the 8-bit code table
		$result .= chr($newOrd);
	}
	$result = '*'.strtr(base64_encode($result), '+/=', '-_#');		// Avoid characters that mess up URLs

	return $result;
}

/*
 * Decrypt the input string
 *
 * See above.
 *
 */
function decrypt($string, $key='') {
	if (empty($key)) $key = session_id();

	if (substr($string,0,1)!='*') return $string;		// Input is not a valid encrypted string
	$string = base64_decode(strtr(substr($string,1), '-_#', '+/='));

	$result = '';
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$newOrd = ord($char) - ord($keychar);
		if ($newOrd < 0) $newOrd += 256;		// Make sure we stay within the 8-bit code table
		$result .= chr($newOrd);
	}

	return $result;
}

/*
 * Get useful information on how to handle this media file
 */
function mediaFileInfo($fileName, $thumbName, $mid, $name='', $notes='', $obeyViewerOption=true) {
	global $THUMBNAIL_WIDTH, $PGV_IMAGE_DIR, $PGV_IMAGES;
	global $LB_URL_WIDTH, $LB_URL_HEIGHT;
	global $SERVER_URL, $GEDCOM, $USE_MEDIA_VIEWER, $USE_MEDIA_FIREWALL, $MEDIA_FIREWALL_THUMBS;

	$result = array();

	// -- Classify the incoming media file
	if (eregi("^https?://", $fileName)) $type = "url_";
	else $type = "local_";
	if ((eregi("\.flv$", $fileName) || eregi("^https?://.*\.youtube\..*/watch\?", $fileName)) && is_dir('modules/JWplayer')) {
		$type .= "flv";
	} else if (eregi("^https?://picasaweb*\.google\..*/.*/", $fileName)) {
		$type .= "picasa";
	} else if (eregi("\.(jpg|jpeg|gif|png)$", $fileName)) {
		$type .= "image";
	} else if (eregi("\.(pdf|avi|txt)$", $fileName)) {
		$type .= "page";
	} else if (eregi("\.mp3$", $fileName)) {
		$type .= "audio";
	} else if (eregi("\.wmv$", $fileName)) {
		$type .= "wmv";
	} else $type .= "other";
	// $type is now: (url | local) _ (flv | picasa | image | page | audio | other)
	$result['type'] = $type;

	// -- Determine the correct URL to open this media file
 	while (true) {
		if (file_exists("modules/lightbox/album.php")) {
			// Lightbox is installed
			include_once('modules/lightbox/lb_defaultconfig.php');
			if (file_exists('modules/lightbox/lb_config.php')) {
				include_once('modules/lightbox/lb_config.php');
			}
			switch ($type) {
			case 'url_flv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($fileName)) . "\" rel='clearbox(500,392,click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')) . "::" . htmlspecialchars($notes,ENT_COMPAT,'UTF-8');
				break 2;
			case 'local_flv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($SERVER_URL.$fileName)) . "\" rel='clearbox(500,392,click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')) . "::" . htmlspecialchars($notes,ENT_COMPAT,'UTF-8');
				break 2;
			case 'url_wmv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($fileName)) . "\" rel='clearbox(500,392,click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')) . "::" . htmlspecialchars($notes,ENT_COMPAT,'UTF-8');
				break 2;
			case 'local_audio':
			case 'local_wmv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($SERVER_URL.$fileName)) . "\" rel='clearbox(500,392,click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')) . "::" . htmlspecialchars($notes,ENT_COMPAT,'UTF-8');
				break 2;
			case 'url_image':
			case 'local_image':
				$url = encode_url($fileName) . "\" rel=\"clearbox[general]\" rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')) . "::" . htmlspecialchars($notes,ENT_COMPAT,'UTF-8');
				break 2;
			case 'url_picasa':
			case 'url_page':
			case 'url_other':
			case 'local_page':
			// case 'local_other':
				$url = encode_url($fileName) . "\" rel='clearbox({$LB_URL_WIDTH},{$LB_URL_HEIGHT},click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')) . "::" . htmlspecialchars($notes,ENT_COMPAT,'UTF-8');
				break 2;
			}
		}

		// Lightbox is not installed or Lightbox is not appropriate for this media type
		switch ($type) {
		case 'url_flv':
			$url = "javascript:;\" onclick=\" var winflv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($fileName)) . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
			break 2;
		case 'local_flv':
			$url = "javascript:;\" onclick=\" var winflv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($SERVER_URL.$fileName)) . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
			break 2;
		case 'url_wmv':
			$url = "javascript:;\" onclick=\" var winwmv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($fileName)) . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
			break 2;
		case 'local_wmv':
		case 'local_audio':
			$url = "javascript:;\" onclick=\" var winwmv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($SERVER_URL.$fileName)) . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
			break 2;
		case 'url_image':
			$imgsize = findImageSize($fileName);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$url = "javascript:;\" onclick=\"var winimg = window.open('".encode_url($fileName)."', 'winimg', 'width=".$imgwidth.", height=".$imgheight.", left=200, top=200'); if (window.focus) {winimg.focus();}";
			break 2;
		case 'url_picasa':
		case 'url_page':
		case 'url_other':
		case 'local_other';
			$url = "javascript:;\" onclick=\"var winurl = window.open('".encode_url($fileName)."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
			break 2;
		case 'local_page':
			$url = "javascript:;\" onclick=\"var winurl = window.open('".encode_url($SERVER_URL.$fileName)."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
			break 2;
		}
		if ($USE_MEDIA_VIEWER && $obeyViewerOption) {
			$url = encode_url('mediaviewer.php?mid='.$mid);
		} else {
			$imgsize = findImageSize($fileName);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$url = "javascript:;\" onclick=\"return openImage('".encode_url(encrypt($fileName))."', $imgwidth, $imgheight);";
		}
		break;
	}
	// At this point, $url describes how to handle the image when its thumbnail is clicked
	$result['url'] = $url;

	// -- Determine the correct thumbnail or pseudo-thumbnail
	$width = '';
	switch ($type) {
		case 'url_flv':
			$thumb = isset($PGV_IMAGES["media"]["flashrem"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["flashrem"] : 'images/media/flashrem.png';
			break;
		case 'local_flv':
			$thumb = isset($PGV_IMAGES["media"]["flash"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["flash"] : 'images/media/flash.png';
			break;
		case 'url_wmv':
			$thumb = isset($PGV_IMAGES["media"]["wmvrem"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["wmvrem"] : 'images/media/wmvrem.png';
			break;
		case 'local_wmv':
			$thumb = isset($PGV_IMAGES["media"]["wmv"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["wmv"] : 'images/media/wmv.png';
			break;
		case 'url_picasa':
			$thumb = isset($PGV_IMAGES["media"]["picasa"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["picasa"] : 'images/media/picasa.png';
			break;
		case 'url_page':
		case 'url_other':
			$thumb = isset($PGV_IMAGES["media"]["globe"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["globe"] : 'images/media/globe.png';
			break;
		case 'local_page':
			$thumb = ($PGV_IMAGES["media"]["doc"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["doc"] : 'images/media/doc.gif';
			break;
		case 'url_audio':
		case 'local_audio':
			$thumb = isset($PGV_IMAGES["media"]["audio"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["audio"] : 'images/media/audio.png';
			break;
		default:
			$thumb = $thumbName;
			if (substr($type,0,4)=='url_') {
				$width = ' width="'.$THUMBNAIL_WIDTH.'"';
			}
	}

	// -- Use an overriding thumbnail if one has been provided
	// Don't accept any overriding thumbnails that are in the "images" or "themes" directories
	if (substr($thumbName,0,7)!='images/' && substr($thumbName,0,7)!='themes/') {
		if ($USE_MEDIA_FIREWALL && $MEDIA_FIREWALL_THUMBS) {
			$tempThumbName = get_media_firewall_path($thumbName);
		} else {
			$tempThumbName = $thumbName;
		}
		if (file_exists($tempThumbName)) {
			$thumb = $thumbName;
		}
	}

	// -- Use the theme-specific media icon if nothing else works
	$realThumb = $thumb;
	if (substr($type,0,6)=='local_' && !file_exists($thumb)) {
		if (!$USE_MEDIA_FIREWALL || !$MEDIA_FIREWALL_THUMBS) {
			$thumb = $PGV_IMAGE_DIR.'/'.$PGV_IMAGES['media']['large'];
			$realThumb = $thumb;
		} else {
			$realThumb = get_media_firewall_path($thumb);
			if (!file_exists($realThumb)) {
				$thumb = $PGV_IMAGE_DIR.'/'.$PGV_IMAGES['media']['large'];
				$realThumb = $thumb;
			}
		}
		$width = '';
	}

	// At this point, $width, $realThumb, and $thumb describe the thumbnail to be displayed
	$result['thumb'] = $thumb;
	$result['realThumb'] = $realThumb;
	$result['width'] = $width;

	return $result;
}

// PHP's native pathinfo() function does not work with filenames that contain UTF8 characters.
// See http://uk.php.net/pathinfo
function pathinfo_utf($path) {
	if (empty($path)) {
		return array('dirname'=>'', 'basename'=>'', 'extension'=>'', 'filename'=>'');
	}
	if (strpos($path, '/')!==false) {
		$basename = end(explode('/', $path));
		$dirname = substr($path, 0, strlen($path) - strlen($basename) - 1);
	} else if (strpos($path, '\\') !== false) {
		$basename = end(explode('\\', $path));
		$dirname = substr($path, 0, strlen($path) - strlen($basename) - 1);
	} else {
		$basename = $path;		// We have just a file name
		$dirname = '.';       // For compatibility with pathinfo()
	}

	if (strpos($basename, '.')!==false) {
		$extension = end(explode('.', $path));
		$filename = substr($basename, 0, strlen($basename) - strlen($extension) - 1);
	} else {
		$extension = '';
		$filename = $basename;
	}

	return array('dirname'=>$dirname, 'basename'=>$basename, 'extension'=>$extension, 'filename'=>$filename);
}

// optional extra file
if (file_exists('includes/functions.extra.php')) {
	require 'includes/functions.extra.php';
}

?>

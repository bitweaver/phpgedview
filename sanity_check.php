<?php
/**
* Checks to see if the version of php you are using is newer then 4.3.
* Checks to see if the config.php, the index directory, the media directory, and the media/thumbs directory are writable.
* Checks to see if the imagecreatefromjpeg, xml_parser_create, and GregorianToJD functions exist. 
* Checks to see if the DomDocument class exists.
* Checks to see if the database is configured correctly.
* Checks to see if the "config.php", "includes", "includes/session.php", "includes/functions.php",
"includes/functions_db.php", "themes/", "includes/lang_settings_std.php", "includes/functions_db.php",
"includes/authentication.php", "includes/functions_name.php", "includes/functions_print.php",
"includes/functions_rtl.php", "includes/functions_mediadb.php", "includes/functions_date.php", 
"includes/templecodes.php", "includes/functions_privacy.php", "includes/menu.php", "config_gedcom.php",
"privacy.php", and "hitcount.php" files exist.
* All of these things are checked when the editconfig.php file is first loaded. 
* If any of the checks fail the appropriate error or warning message will be displayed.
*  
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2007  John Finlay and Others
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
* @subpackage Admin
* @see editconfig.php
* @version $Id: sanity_check.php,v 1.1 2008/07/07 18:01:13 lsces Exp $
*/
global $errors, $warnings, $pgv_lang;
$errors = array();
$warnings = array();
if (version_compare(phpversion(), '4.3.5')<0) 
{
	$errors[] = "<span class=\"error\">".$pgv_lang["sanity_err1"]."</span>";
}

//-- define function
if (!function_exists('file_is_writable')) {
	function file_is_writable($file) {
		$err_write = false;
		$handle = @fopen($file,"r+");
		if	($handle)	{
			$i = fclose($handle);
			$err_write = true;
		}
		return($err_write);
	}
}

function print_sanity_errors() {
	global $warnings, $errors, $pgv_lang;
	if (preg_match("/\Weditconfig.php/", $_SERVER["SCRIPT_NAME"])>0)
	{
		//Prints warnings
		if (count($warnings)>0)
		{
//			print "<center><span style=\"color: green; font-weight: bold;\">Warnings: </span></center>";
			print "<center><span style=\"color: green; font-weight: bold;\">".$pgv_lang["sanity_warn0"]."</span></center>";
			foreach($warnings as $warning) 
			{
				print "<center><span style=\"color: blue; font-weight: bold;\">".$warning."</span></center><br />";
			}
		}
		//Prints errors
		if (count($errors)>0)
		{
//			print "<center><span style=\"color: green; font-weight: bold;\">Errors: </span></center>";
			print "<center><span style=\"color: green; font-weight: bold;\">".$pgv_lang["sanity_err0"]."</span></center>";
			foreach($errors as $error) 
			{
				print "<center><span style=\"color: red; font-weight: bold;\">".$error."</span></center><br />";
			}
			exit;
		}
	}
}

$arr = array("config.php", "includes", "includes/session.php", "includes/functions.php",
			 "includes/functions_db.php", "themes/", "includes/lang_settings_std.php", 
			 "includes/functions_db.php", "includes/authentication.php", "includes/functions_name.php", 
			 "includes/functions_print.php", "includes/functions_rtl.php", "includes/functions_mediadb.php", 
             "includes/functions_date.php", "includes/templecodes.php", "includes/functions_privacy.php",
             "includes/menu.php", "config_gedcom.php", "privacy.php", "hitcount.php");
global $whichFile;
foreach($arr as $k => $whichFile)
{
	if (!file_exists($whichFile))
	{
		// We can't be sure that function print_text() exists, so we'll do the safe thing here
		$message = str_replace("#GLOBALS[whichFile]#", $whichFile, $pgv_lang["sanity_err2"]);
		$errors[] = "<span class=\"error\">".$message."</span>";
	} else {
		if (!is_dir($whichFile) && filesize($whichFile)<10) {
			// We can't be sure that function print_text() exists, so we'll do the safe thing here
			$message = str_replace("#GLOBALS[whichFile]#", $whichFile, $pgv_lang["sanity_err3"]);
			$errors[] = "<span class=\"error\">".$message."</span>";
		}
	}
}

unset($CONFIGURED);
global $CONFIGURED;
@require("config.php");
if (!isset($CONFIGURED)) $errors[] = "<span class=\"error\">".$pgv_lang["sanity_err4"]."</span>";

if (count($errors)>0) {
	print_sanity_errors();
	exit;
}

//-- if we have a good configuration only allow admins to this page
if ($CONFIGURED && adminUserExists() && !PGV_USER_IS_ADMIN) exit;

if (!file_is_writable("config.php")) 
{
	//if (!@ chmod("config.php", 0777)) 
	//{
		if (!$CONFIGURED) $errors[] = "<span class=\"error\">".$pgv_lang["sanity_err5"]."</span>";
		else $warnings[] = "<span class=\"error\">".$pgv_lang["sanity_err5"]."</span>";
	//}
}

if (!is_writable($INDEX_DIRECTORY)) 
{
	//if (!@ chmod($INDEX_DIRECTORY, 0777)) 
	//{
		$errors[] = "<span class=\"error\">".print_text("sanity_err6",0,1)."</span>";
	//}
}

if (!is_writable($MEDIA_DIRECTORY)) 
{
	//if (!@ chmod($MEDIA_DIRECTORY, 0777)) 
	//{
		$warnings[] = print_text("sanity_warn1",0,1);
	//}
}
if (!is_writable($MEDIA_DIRECTORY . "thumbs")) 
{
	//if (!@ chmod($MEDIA_DIRECTORY . "thumbs", 0777)) 
	//{
		$warnings[] = print_text("sanity_warn2",0,1);
	//}
}

if (!function_exists('imagecreatefromjpeg')) 
{
	$warnings[] = $pgv_lang["sanity_warn3"];
}

if (!function_exists('xml_parser_create')) 
{
	$warnings[] = $pgv_lang["sanity_warn4"];
}

if (!class_exists('DomDocument')) 
{
	$warnings[] = $pgv_lang["sanity_warn5"];
	
}

if (!function_exists('GregorianToJD')) 
{
	$warnings[] = $pgv_lang["sanity_warn6"];
}

if (($CONFIGURED || (isset($_REQUEST['action']) && $_REQUEST['action']=="update")) && !check_db(true)) 
{
	loadLangFile("pgv_confighelp");
	$error = "";
	$error = "<span class=\"error\">".$pgv_lang["db_setup_bad"]."</span><br />";
	$error .= "<span class=\"error\">" . $DBCONN->getMessage() . " " . $DBCONN->getUserInfo() . "</span><br />";
	
	if ($CONFIGURED == true) 
	{
		//-- force the incoming user to enter the database password before they can configure the site for security.
		if (!isset ($_POST["security_check"]) || !isset ($_POST["security_user"]) || (($_POST["security_check"] != $DBPASS) && ($_POST["security_user"] == $DBUSER))) 
		{
			$error .= "<br /><br />".print_text("enter_db_pass", 0, 1);
			$errors[] = $error;
		}
		else $warnings[] = $error;
	}
	else $warnings[] = $error;
}
if (strstr($_SERVER['PHP_SELF'], "editconfig.php")===false) 
	if (count($warnings!=0) || count($errors!=0)) print_sanity_errors();
?>

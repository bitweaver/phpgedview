<?php
/**
 * Startup and session logic
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * @subpackage Reports
 * @version $Id: session.php,v 1.5 2006/04/14 20:25:52 squareing Exp $
 */
if (strstr($_SERVER["SCRIPT_NAME"],"session")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}

//-- check for the sanity worm to save bandwidth
if (eregi("LWP::Simple",getenv("HTTP_USER_AGENT"),$regs) or eregi("lwp-trivial",getenv("HTTP_USER_AGENT"),$regs)
		|| eregi("HTTrack",getenv("HTTP_USER_AGENT"),$regs)) {
	print "Bad Worm! Bad!  Crawl back into your hole.";
	exit;
}
@ini_set('arg_separator.output', '&amp;');
@ini_set('error_reporting', 0);
@ini_set('display_errors', '1');
@error_reporting(0);

//-- version of phpgedview
$VERSION = "4.0";
$VERSION_RELEASE = "beta 2";
$REQUIRED_PRIVACY_VERSION = "3.1";

set_magic_quotes_runtime(0);

if (phpversion()<4.2) {
	//-- detect old versions of PHP and display error message
	//-- cannot add this to the language files because the language has not been established yet.
	print "<html>\n<body><b style=\"color: red;\">PhpGedView requires PHP version 4.3 or later.</b><br /><br />\nYour server is running PHP version ".phpversion().".  Please ask your server's Administrator to upgrade the PHP installation.</body></html>";
	exit;
}

if (!empty($_SERVER["SCRIPT_NAME"])) $SCRIPT_NAME=$_SERVER["SCRIPT_NAME"];
else if (!empty($_SERVER["PHP_SELF"])) $SCRIPT_NAME=$_SERVER["PHP_SELF"];
if (!empty($_SERVER["QUERY_STRING"])) $QUERY_STRING = $_SERVER["QUERY_STRING"];
else $QUERY_STRING="";
$QUERY_STRING = preg_replace(array("/&/","/</"), array("&amp;","&lt;"), $QUERY_STRING);
$QUERY_STRING = preg_replace("/show_context_help=(no|yes)/", "", $QUERY_STRING);

if (empty($CONFIG_VERSION)) $CONFIG_VERSION = "2.65";
if (empty($SERVER_URL)) $SERVER_URL = stripslashes("http://".$_SERVER["SERVER_NAME"].dirname($SCRIPT_NAME)."/");
if (!isset($ALLOW_REMEMBER_ME)) $ALLOW_REMEMBER_ME = true;
if (!isset($PGV_SIMPLE_MAIL)) $PGV_SIMPLE_MAIL = false;
if (!isset($DBPERSIST)) $DBPERSIST = false;

if (empty($PGV_MEMORY_LIMIT)) $PGV_MEMORY_LIMIT = "32M";
@ini_set('memory_limit', $PGV_MEMORY_LIMIT);

//--load common functions
require_once( PHPGEDVIEW_PKG_PATH . "includes/functions.php");
//require_once($PGV_BASE_DIRECTORY."includes/menu.php");
//-- set the error handler
//$OLD_HANDLER = set_error_handler("pgv_error_handler");
//-- load db specific functions

//-- Setup array of media types
$MEDIATYPE = array("a11","acb","adc","adf","afm","ai","aiff","aif","amg","anm","ans","apd","asf","au","avi","awm","bga","bmp","bob","bpt","bw","cal","cel","cdr","cgm","cmp","cmv","cmx","cpi","cur","cut","cvs","cwk","dcs","dib","dmf","dng","doc","dsm","dxf","dwg","emf","enc","eps","fac","fax","fit","fla","flc","fli","fpx","ftk","ged","gif","gmf","hdf","iax","ica","icb","ico","idw","iff","img","jbg","jbig","jfif","jpe","jpeg","jp2","jpg","jtf","jtp","lwf","mac","mid","midi","miff","mki","mmm",".mod","mov","mp2","mp3","mpg","mpt","msk","msp","mus","mvi","nap","ogg","pal","pbm","pcc","pcd","pcf","pct","pcx","pdd","pdf","pfr","pgm","pic","pict","pk","pm3","pm4","pm5","png","ppm","ppt","ps","psd","psp","pxr","qt","qxd","ras","rgb","rgba","rif","rip","rla","rle","rpf","rtf","scr","sdc","sdd","sdw","sgi","sid","sng","swf","tga","tiff","tif","txt","text","tub","ul","vda","vis","vob","vpg","vst","wav","wdb","win","wk1","wks","wmf","wmv","wpd","wxf","wp4","wp5","wp6","wpg","wpp","xbm","xls","xpm","xwd","yuv","zgm");

//-- start the php session
$time = time() + $gBitSystem->getConfig( 'pgv_session_time' );
$date = date("D M j H:i:s T Y", $time);
//-- import the post, get, and cookie variable into the scope on new versions of php
if (phpversion() >= '4.1') {
	@import_request_variables("cgp");
}
if (phpversion() > '4.2.2') {
	//-- prevent sql and code injection
	foreach($_REQUEST as $key=>$value) {
		if (!is_array($value)) {
			if (preg_match("/((DELETE)|(INSERT)|(UPDATE)|(ALTER)|(CREATE)|( TABLE)|(DROP))\s[A-Za-z0-9 ]{0,200}(\s(FROM)|(INTO)|(TABLE)\s)/i", $value, $imatch)>0) {
				print "Possible SQL injection detected: $key=>$value.  <b>$imatch[0]</b> Script terminated.";
				require_once("includes/authentication.php");      // -- load the authentication system
				AddToLog("Possible SQL injection detected: $key=>$value. <b>$imatch[0]</b> Script terminated.");
				exit;
			}
			//-- don't let any html in
			if (!empty($value)) ${$key} = preg_replace(array("/</","/>/"), array("&lt;","&gt;"), $value);
		}
		else {
			foreach($value as $key1=>$val) {
				if (!is_array($val)) {
					if (preg_match("/((DELETE)|(INSERT)|(UPDATE)|(ALTER)|(CREATE)|( TABLE)|(DROP))\s[A-Za-z0-9 ]{0,200}(\s(FROM)|(INTO)|(TABLE)\s)/i", $val, $imatch)>0) {
						print "Possible SQL injection detected: $key=>$val <b>$imatch[0]</b>.  Script terminated.";
						require_once("includes/authentication.php");      // -- load the authentication system
						AddToLog("Possible SQL injection detected: $key=>$val <b>$imatch[0]</b>.  Script terminated.");
						exit;
					}
					//-- don't let any html in
					if (!empty($val)) ${$key}[$key1] = preg_replace(array("/</","/>/"), array("&lt;","&gt;"), $val);
				}
			}
		}
	}
}

//-- import the gedcoms array
require_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$GEDCOMS = new BitGEDCOM();

if (isset($_REQUEST["GEDCOM"])){
   $_REQUEST["GEDCOM"] = trim($_REQUEST["GEDCOM"]);
}
if (!isset($DEFAULT_GEDCOM)) $DEFAULT_GEDCOM = "";
if (empty($_REQUEST["GEDCOM"])) {
   if (isset($_SESSION["GEDCOM"])) $GEDCOM = $_SESSION["GEDCOM"];
   else {
      if ( (empty($GEDCOM)) || !$GEDCOMS->isValid() ) $GEDCOM=$DEFAULT_GEDCOM;
      else if ( (empty($GEDCOM)) && $GEDCOMS->getCount()>0 ) {
         foreach($GEDCOMS as $ged_file=>$ged_array) {
	         $GEDCOM = $ged_file;
	         if (check_for_import($ged_file)) break;
         }
      }
   }
}
else {
	$GEDCOM = $_REQUEST["GEDCOM"];
}
if (isset($_REQUEST["ged"])) {
	$GEDCOM = trim($_REQUEST["ged"]);
}
if (is_int($GEDCOM)) $GEDCOM = get_gedcom_from_id($GEDCOM);
$_SESSION["GEDCOM"] = $GEDCOM;
$INDILIST_RETRIEVED = false;
$FAMLIST_RETRIEVED = false;

//require_once($PGV_BASE_DIRECTORY."config_gedcom.php");
//require_once(get_config_file());

//require_once($PGV_BASE_DIRECTORY."includes/functions_name.php");

//require_once($PGV_BASE_DIRECTORY."includes/authentication.php");      // -- load the authentication system

//-- load media specific functions
// if ($MULTI_MEDIA) require_once($PGV_BASE_DIRECTORY."includes/functions_mediadb.php");
// Media will be handled by fisheye

/**
 * do not include print functions when using the gdbi protocol
if (strstr($SCRIPT_NAME, "client.php")===false) {
	require_once($PGV_BASE_DIRECTORY."includes/functions_print.php");
	require_once($PGV_BASE_DIRECTORY."includes/functions_rtl.php");
	require_once($PGV_BASE_DIRECTORY."includes/functions_date.php");
}
 */

if (empty($PEDIGREE_GENERATIONS)) $PEDIGREE_GENERATIONS = $gBitSystem->getConfig( 'pgv_default_pedigree_generations'  );

// Bodge until we can switch to bitweaver language kernel
$LANGUAGE = "english";
//-- load file for language settings
require_once( PHPGEDVIEW_PKG_PATH . "includes/lang_settings_std.php");
$Languages_Default = true;
if (file_exists( PHPGEDVIEW_PKG_PATH . "lang_settings.php")) {
	$DefaultSettings = $language_settings;		// Save default settings, so we can merge properly
	require_once( PHPGEDVIEW_PKG_PATH . "lang_settings.php");
	$ConfiguredSettings = $language_settings;	// Save configured settings, same reason
	$language_settings = array_merge($DefaultSettings, $ConfiguredSettings);	// Copy new langs into config
	unset($DefaultSettings);
	unset($ConfiguredSettings);		// We don't need these any more
	$Languages_Default = false;
}

/* Re-build the various language-related arrays
 *		Note:
 *		This code existed in both lang_settings_std.php and in lang_settings.php.
 *		It has been removed from both files and inserted here, where it belongs.
 */
$languages 				= array();
$pgv_lang_use 			= array();
$pgv_lang 				= array();
$lang_short_cut 		= array();
$lang_langcode 			= array();
$pgv_language 			= array();
$confighelpfile 		= array();
$helptextfile 			= array();
$flagsfile 				= array();
$factsfile 				= array();
$factsarray 			= array();
$pgv_lang_name 			= array();
$langcode				= array();
$ALPHABET_upper			= array();
$ALPHABET_lower			= array();
$DATE_FORMAT_array		= array();
$TIME_FORMAT_array		= array();
$WEEK_START_array		= array();
$TEXT_DIRECTION_array	= array();
$NAME_REVERSE_array		= array();

foreach ($language_settings as $key => $value) {
	$languages[$key] 			= $value["pgv_langname"];
	$pgv_lang_use[$key]			= $value["pgv_lang_use"];
	$pgv_lang[$key]				= $value["pgv_lang"];
	$lang_short_cut[$key]		= $value["lang_short_cut"];
	$lang_langcode[$key]		= $value["langcode"];
	$pgv_language[$key]			= $value["pgv_language"];
	$confighelpfile[$key]		= $value["confighelpfile"];
	$helptextfile[$key]			= $value["helptextfile"];
	$flagsfile[$key]			= $value["flagsfile"];
	$factsfile[$key]			= $value["factsfile"];
	$ALPHABET_upper[$key]		= $value["ALPHABET_upper"];
	$ALPHABET_lower[$key]		= $value["ALPHABET_lower"];
	$DATE_FORMAT_array[$key]	= $value["DATE_FORMAT"];
	$TIME_FORMAT_array[$key]	= $value["TIME_FORMAT"];;
	$WEEK_START_array[$key]		= $value["WEEK_START"];
	$TEXT_DIRECTION_array[$key]	= $value["TEXT_DIRECTION"];
	$NAME_REVERSE_array[$key]	= $value["NAME_REVERSE"];

	$pgv_lang["lang_name_$key"]	= $value["pgv_lang"];

	$dDummy = $value["langcode"];
	$ct = strpos($dDummy, ";");
	while ($ct > 1) {
		$shrtcut = substr($dDummy,0,$ct);
		$dDummy = substr($dDummy,$ct+1);
		$langcode[$shrtcut]		= $key;
		$ct = strpos($dDummy, ";");
	}
}

// Check for page views exceeding the limit
//CheckPageViews();

//require_once($PHPGEDVIEW_PKG_PATH . "includes/templecodes.php");		//-- load in the LDS temple code translations
//Replaced by database tables

/*
require_once("privacy.php");
//-- load the privacy file
require_once(get_privacy_file());
//-- load the privacy functions
require_once($PHPGEDVIEW_PKG_PATH."includes/functions_privacy.php");
 */
 
if (!isset($SCRIPT_NAME)) $SCRIPT_NAME=$_SERVER["SCRIPT_NAME"];

if (empty($TEXT_DIRECTION)) $TEXT_DIRECTION="ltr";
$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
$WEEK_START	= $WEEK_START_array[$LANGUAGE];
$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

// require_once($PHPGEDVIEW_PKG_PATH."hitcount.php"); //--load the hit counter

if ($Languages_Default) {					// If Languages not yet configured
	$pgv_lang_use["english"] = false;		//   disable English
	$pgv_lang_use["$LANGUAGE"] = true;		//     and enable according to Browser pref.
	$language_settings["english"]["pgv_lang_use"] = false;
	$language_settings["$LANGUAGE"]["pgv_lang_use"] = true;
}

?>

<?php
/**
 * Startup and session logic
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
 * @package PhpGedView
 * @subpackage Reports
 * @version $Id: session.php,v 1.19 2008/07/07 17:30:13 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

//-- version of PhpGedView  (Let's keep this in an obvious place -- not buried in code)
$VERSION = "4.1.5";
$VERSION_RELEASE = "";
$REQUIRED_PRIVACY_VERSION = "3.1";

function isAlphaNum($value) {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

function isAlpha($value) {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }

function gen_spider_session_name($bot_name, $bot_language) {
	// session names are limited to alphanum upper and lower only.
	// $outname = '__Spider-name-:/alphanum_only__';
	// Example  =  sess_xxGOOGLEBOTfsHTTPcffWWWdGOOGLxx
	// Matchable by "ls sess_xx??????????????????????????xx"
	$outname = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

	$bot_limit = strlen($bot_name);
        if($bot_limit > 27)
		$bot_limit = 27;
	for($x=0; $x < $bot_limit; $x++) {
		if(isAlphaNum($bot_name{$x}))
			$outname{$x+2} = strtoupper($bot_name{$x});
		else if ($bot_name{$x} == '.')
			$outname{$x+2} = 'd';
		else if ($bot_name{$x} == ':')
			$outname{$x+2} = 'c';
		else if ($bot_name{$x} == '/')
			$outname{$x+2} = 'f';
		else if ($bot_name{$x} == ' ')
			$outname{$x+2} = 's';
		else if ($bot_name{$x} == '-')
			$outname{$x+2} = 't';
		else if ($bot_name{$x} == '_')
			$outname{$x+2} = 'u';
		else
			$outname{$x+2} = 'o';
	}
	return($outname);
    }


// Search Engines are treated special, and receive only core data, without the
// pretty bells and whistles.  Recursion is also going to be kept to a minimum.
// Max uncompressed page output has to be under 100k.  Spiders do not index the
// rest of the file.

global $SEARCH_SPIDER;
$SEARCH_SPIDER = false;		// set empty at start
global $SESSION_HIDE_GOOGLEMAP;
$SESSION_HIDE_GOOGLEMAP = "empty";

$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";

$worms = array(
	'Super_Ale',
	'Wget',
	'DataCha',
	'libwww-perl',
	'LWP::Simple',
	'lwp-trivial',
	'HTTrack'
	);
$quitReason = "";

// check for attempt to redirect
if (eregi("=.*://", rawurldecode($_SERVER["REQUEST_URI"]))) {
	$quitReason = "Embedded URL detected";
}

// check for worms and bad bots
if ($quitReason == "") { 
	foreach ($worms as $worm) {
		if (eregi($worm, $ua)) {
			$quitReason = "Blocked crawler detected";
			break;
		}
	}
}

// Do we have a reason to quit now?
if ($quitReason != "") {
	if ((!ini_get('register_globals'))||(strtolower(ini_get('register_globals'))=="off")) {
		//-- load common functions
		require_once("includes/functions.php");
		//-- load db specific functions
		require_once("includes/functions_db.php");
		require_once("includes/authentication.php");      // -- load the authentication system
		AddToLog("MSG>{$quitReason}; script terminated.");
		AddToLog("UA>{$ua}<");
		AddToLog("URI>{$_SERVER["REQUEST_URI"]}<");
	}
	header("HTTP/1.0 403 Forbidden");
	print "Hackers are not welcome here.";
	exit;
}


// The search list has been reversed.  Whitelist all browsers, and
// mark everything else as a spider/bot.
// Java/ Axis/ and PEAR required for GDBI and our own cross site communication.
$real_browsers = array(
	'GENESIS',
	'PGVAgent',
	'MSIE ',
	'Opera',
	'Firefox',
	'Konqueror',
	'Gecko',
	'Safari',
	'http://www.avantbrowser.com',
	'BlackBerry',
	'Lynx',
	'Java/',
	'PEAR',
	'Axis/',
	'MSFrontPage',
	'RssReader',
	'W3C_Validator'
	);

// We overlay the following name with carefully selected characters.
// This is to avoid XSS problems.  Alpha : . / - _ only.  Yes, the following string is 72 chars.
$spider_name = '                                                                        ';

// If you want to disable spider detection, set real to true here.
$real = false;

if($ua != "") {
	foreach($real_browsers as $browser_check) {
		if (eregi($browser_check, $ua)) {
			$real = true;
			break;
		}
	}
	// check for old Netscapes.
	if (eregi("Mozilla/", $ua)) {
		if (!eregi("compatible", $ua)) {
			if (eregi("\[..\]", $ua)) {
				$real = true;
			}
			if (eregi("Macintosh", $ua)) {
				$real = true;
			}
		}
	}
}
else {
	// For the people who firewall identifying information
	// Switch real to false if you wish to restrict these connections.
	$ua = "Browser User Agent Empty";
	$real = true;
}

if(!$real) {
	$bot_name = $ua;
	// strip out several common strings that clutter the User Agent.
	$bot_name = eregi_replace("Mozilla\/... \(compatible;", "", $bot_name);
	$bot_name = eregi_replace("Mozilla\/... ", "", $bot_name);
	$bot_name = eregi_replace("Windows NT", "", $bot_name);
	$bot_name = eregi_replace("Windows; U;", "", $bot_name);
	$bot_name = eregi_replace("Windows", "", $bot_name);

	// Copy in characters, stripping out unwanteds until we are full, stopping at 70.
	$y = 0;
	$valid_char = false;
	$bot_limit = strlen($bot_name);
	for($x=0; $x < $bot_limit; $x++) {
		if(isAlpha($bot_name{$x})) {
			$spider_name{$y} = $bot_name{$x};
			$valid_char = true;
			$y++;
			if ($y > 70) break;
		}
		else if ($bot_name{$x} == ' ')	{
			if($valid_char) {
				$spider_name{$y} = ' ';
				$valid_char = false;
				$y++;
				if ($y > 70) break;
			}
		}
		else if ($bot_name{$x} == '.')	{
			if($valid_char) {
				$spider_name{$y} = '.';
				$valid_char = true;
				$y++;
				if ($y > 70) break;
			}
		}
		else if ($bot_name{$x} == ':')	{
			$spider_name{$y} = ':';
			$valid_char = true;
			$y++;
			if ($y > 70) break;
		}
		else if ($bot_name{$x} == '/')	{
			$spider_name{$y} = '/';
			$valid_char = true;
			$y++;
			if ($y > 70) break;
		}
		else if ($bot_name{$x} == '-')	{
			$spider_name{$y} = '-';
			$valid_char = true;
			$y++;
			if ($y > 70) break;
		}
		else if ($bot_name{$x} == '_')	{
			$spider_name{$y} = '_';
			$valid_char = true;
			$y++;
			if ($y > 70) break;
		}
		else { // Compress consecutive invalids down to one space char.
			if($valid_char) {
				$spider_name{$y} = ' ';
				$valid_char = false;
				$y++;
				if ($y > 70) break;
			}
		}
	}
	// The SEARCH_SPIDER is set to 70 vetted chars, the session to 26 chars.
	$SEARCH_SPIDER = $spider_name;
	$bot_session = gen_spider_session_name($spider_name, "");
	session_id($bot_session);
}

// stop spiders from accessing certain parts of the site
$bots_not_allowed = array(
	'/reports/',
	'/includes/',
	'config',
	'clippings',
);
if (!empty($SEARCH_SPIDER)) {
	foreach($bots_not_allowed as $place) {
		if (eregi($place, $_SERVER['PHP_SELF'])) {
			header("HTTP/1.0 403 Forbidden");
			print "Sorry, this page is not available for search engine bots.";
			exit;
		}
	}
}

/**
  * Manual Search Engine IP Address tagging
  *   Allow and admin to mark IP addresses as known search engines even if
  *   they are not automatically detected above.   Setting his own IP address
  *   in this file allows him to see exactly what the search engine receives.
  *   To return to normal, the admin MUST use a different IP to get to admin
  *   mode or edit search_engines.php by hand.
  */
if (file_exists($INDEX_DIRECTORY."search_engines.php")) {
	require($INDEX_DIRECTORY."search_engines.php");
	//loops through each ip in search_engines.php
	foreach($search_engines as $key=>$value) {
		//creates a regex foreach ip
		$ipRegEx = '';
		$arrayIP = explode('*', $value);
		$ipRegEx .= $arrayIP[0];
		if (count($arrayIP) > 1) {
			for($i=1; $i < count($arrayIP); $i++) {
				if($i == (count($arrayIP)))
		 			$ipRegEx .= "\d{0,3}";
	 			else
	 				$ipRegEx .= "\d{0,3}".$arrayIP[$i];
			}
		}
		//checks the remote ip address against each ip regex
		if (preg_match('/^'.$ipRegEx.'/', $_SERVER['REMOTE_ADDR'])) {
			if(empty($SEARCH_SPIDER))
				$SEARCH_SPIDER = "Manual Search Engine entry of ".$_SERVER['REMOTE_ADDR'];
			$bot_name = "MAN".$_SERVER['REMOTE_ADDR'];
			$bot_session = gen_spider_session_name($bot_name, "");
			session_id($bot_session);
			break;
 		}
	}
}

@ini_set('arg_separator.output', '&amp;');
@ini_set('error_reporting', 0);
@ini_set('display_errors', '1');
@error_reporting(0);

//-- required for running PHP in CGI Mode on Windows
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = "";

//-- list of critical configuration variables
$CONFIG_VARS = array(
	'PGV_BASE_DIRECTORY',
	'PGV_DATABASE',
	'DBTYPE',
	'DBHOST',
	'DBUSER',
	'DBPASS',
	'DBNAME',
	'TBLPREFIX',
	'INDEX_DIRECTORY',
	'AUTHENTICATION_MODULE',
	'USE_REGISTRATION_MODULE',
	'ALLOW_USER_THEMES',
	'ALLOW_REMEMBER_ME',
	'DEFAULT_GEDCOM',
	'ALLOW_CHANGE_GEDCOM',
	'LOGFILE_CREATE',
	'PGV_SESSION_SAVE_PATH',
	'PGV_SESSION_TIME',
	'GEDCOMS',
	'SERVER_URL',
	'LOGIN_URL',
	'PGV_MEMORY_LIMIT',
	'PGV_STORE_MESSAGES',
	'PGV_SIMPLE_MAIL',
	'CONFIG_VERSION',
	'CONFIGURED',
	'MANUAL_SESSON_START',
	'REQUIRE_ADMIN_AUTH_REGISTRATION',
	'COMMIT_COMMAND'
	);


//-- Detect and report Windows or OS/2 Server environment
//		Windows and OS/2 use the semi-colon as a separator in the "include_path",
//				*NIX uses a colon
//		Windows and OS/2 use the ISO character set in the server-side file system,
//				*NIX and PhpGedView use UTF-8.  Consequently, PGV needs to translate
//				from UTF-8 to ISO when handing a file/folder name to Windows and OS/2,
//				and all file/folder names received from Windows and OS/2 must be
//				translated from ISO to UTF-8 before they can be processed by PGV.
$WIN32 = false;
if(substr(PHP_OS, 0, 3) == 'WIN') $WIN32 = true;
if(substr(PHP_OS, 0, 4) == 'OS/2') $WIN32 = true;
if(substr(PHP_OS, 0, 7) == 'NetWare') $WIN32 = true;
if($WIN32) $seperator=";"; else $seperator = ":";
//-- append our 'includes/' path to the include_path ini setting for ease of use.
$ini_include_path = @ini_get('include_path');
$includes_dir = dirname(@realpath(__FILE__));
$includes_dir .= $seperator.dirname($includes_dir);
@ini_set('include_path', ".{$seperator}{$includes_dir}{$seperator}{$ini_include_path}");
unset($ini_include_path, $includes_dir); // destroy some variables for security reasons.

set_magic_quotes_runtime(0);

if (version_compare(phpversion(), '4.3.5')<0)
	die ("<html>\n<body><b style=\"color: red;\">PhpGedView requires PHP version 4.3.5 or later.</b><br /><br />\nYour server is running PHP version ".phpversion().".  Please ask your server's Administrator to upgrade the PHP installation.</body></html>");

//-- load file for language settings
require_once( "includes/lang_settings_std.php");
$Languages_Default = true;
if (!strstr($_SERVER["REQUEST_URI"], "INDEX_DIRECTORY=") && file_exists($INDEX_DIRECTORY . "lang_settings.php")) {
	$DefaultSettings = $language_settings;		// Save default settings, so we can merge properly
	require_once($INDEX_DIRECTORY . "lang_settings.php");
	$ConfiguredSettings = $language_settings;	// Save configured settings, same reason
	$language_settings = array_merge($DefaultSettings, $ConfiguredSettings);	// Copy new langs into config
	// Now copy new language settings into existing configuration
	foreach ($DefaultSettings as $lang => $settings) {
		foreach ($settings as $key => $value) {
			if (!isset($language_settings[$lang][$key])) $language_settings[$lang][$key] = $value;
		}
	}
	unset($DefaultSettings);
	unset($ConfiguredSettings);		// We don't need these any more
	$Languages_Default = false;
}

//-- build array of active languages (required for config override check)
$pgv_lang_use = array();
foreach ($language_settings as $key => $value) {
	$pgv_lang_use[$key] = $value["pgv_lang_use"];
}

/**
 *		Check for configuration variable override.
 *
 *		Each incoming URI is checked to see whether it contains any mention of
 *		certain critical global variables that should not be changed, or that
 *		can only be changed within limits.
 */
while (true) {
	$configOverride = true;
	// Check for override of $CONFIG_VARS
	if (array_key_exists("CONFIG_VARS", $_REQUEST)) break;

	// $CONFIG_VARS is safe: now check for any in its list
	foreach($CONFIG_VARS as $VAR) {
		if (array_key_exists($VAR, $_REQUEST)) break 2;
	}
	
	// Check for $LANGUAGE variable override
	//		Don't let incoming request change to an unsupported or inactive language
	if (isset($_REQUEST["NEWLANGUAGE"])) {
		if (empty($pgv_lang_use[$_REQUEST["NEWLANGUAGE"]])) break;
		if (!$pgv_lang_use[$_REQUEST["NEWLANGUAGE"]]) break;
	}
	
	$configOverride = false;
	break;
}

//-- CONFIG_VARS is still needed by the web services
//unset($CONFIG_VARS);

//-- check if they are trying to hack
if ($configOverride) {
	if ((!ini_get('register_globals'))||(strtolower(ini_get('register_globals'))=="off")) {
		//-- load common functions
		require_once("includes/functions.php");
		//-- load db specific functions
		require_once("includes/functions_db.php");
		require_once("includes/authentication.php");      // -- load the authentication system
		AddToLog("MSG>Configuration override detected; script terminated.");
		AddToLog("UA>{$ua}<");
		AddToLog("URI>{$_SERVER["REQUEST_URI"]}<");
	}
	header("HTTP/1.0 403 Forbidden");
	print "Hackers are not welcome here.";
	exit;
}

if ((isset($_REQUEST["HIDE_GOOGLEMAP"])) && (empty($SEARCH_SPIDER))) {
	if(stristr("true", $_REQUEST["HIDE_GOOGLEMAP"])) {
		$SESSION_HIDE_GOOGLEMAP = "true";
	}
	if(stristr("false", $_REQUEST["HIDE_GOOGLEMAP"])) {
		$SESSION_HIDE_GOOGLEMAP = "false";
	}
}

if (!empty($_SERVER["PHP_SELF"]))
	$SCRIPT_NAME=$_SERVER["PHP_SELF"];
else
	if (!empty($_SERVER["SCRIPT_NAME"]))
		$SCRIPT_NAME=$_SERVER["SCRIPT_NAME"];

$SCRIPT_NAME = preg_replace("~/+~", "/", $SCRIPT_NAME);
if (!empty($_SERVER["QUERY_STRING"])) $QUERY_STRING = $_SERVER["QUERY_STRING"];
else $QUERY_STRING="";
$QUERY_STRING = preg_replace(array("/&/","/</"), array("&amp;","&lt;"), $QUERY_STRING);
$QUERY_STRING = preg_replace("/show_context_help=(no|yes)/", "", $QUERY_STRING);

//-- if not configured then redirect to the configuration script
if (!$CONFIGURED) {
   if ((strstr($SCRIPT_NAME, "admin.php")===false)
   &&(strstr($SCRIPT_NAME, "login.php")===false)
   &&(strstr($SCRIPT_NAME, "editconfig.php")===false)
   &&(strstr($SCRIPT_NAME, "config_download.php")===false)
   &&(strstr($SCRIPT_NAME, "editconfig_help.php")===false)) {
      header("Location: editconfig.php");
      exit;
   }
}
//-- allow user to cancel
ignore_user_abort(false);

if (empty($CONFIG_VERSION)) $CONFIG_VERSION = "2.65";
if (empty($SERVER_URL)) {
	$SERVER_URL = "http://".$_SERVER["SERVER_NAME"];
	if (!empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"]!=80) $SERVER_URL .= ":".$_SERVER["SERVER_PORT"];
	$SERVER_URL .= dirname($SCRIPT_NAME)."/";
	$SERVER_URL = stripslashes($SERVER_URL);
}
if (substr($SERVER_URL,-1)!="/") $SERVER_URL .= "/";	// make SURE that trailing "/" is present
if (!isset($ALLOW_REMEMBER_ME)) $ALLOW_REMEMBER_ME = false;
if (!isset($PGV_SIMPLE_MAIL)) $PGV_SIMPLE_MAIL = false;
if (!isset($DBPERSIST)) $DBPERSIST = false;

if (empty($PGV_MEMORY_LIMIT)) $PGV_MEMORY_LIMIT = "32M";
@ini_set('memory_limit', $PGV_MEMORY_LIMIT);

//-- backwards compatibility with v < 3.1
if (isset($PGV_DATABASE) && $PGV_DATABASE=="mysql") {
	$DBTYPE = 'mysql';
	$PGV_DATABASE = 'db';
}
//--load common functions
require_once("includes/functions.php");
require_once("includes/menu.php");
//-- set the error handler
$OLD_HANDLER = set_error_handler("pgv_error_handler");
//-- load db specific functions
require_once("includes/functions_db.php");

//-- setup execution timer
$start_time = getmicrotime();

//-- Setup array of media types
$MEDIATYPE = array("a11","acb","adc","adf","afm","ai","aiff","aif","amg","anm","ans","apd","asf","au","avi","awm","bga","bmp","bob","bpt","bw","cal","cel","cdr","cgm","cmp","cmv","cmx","cpi","cur","cut","cvs","cwk","dcs","dib","dmf","dng","doc","dsm","dxf","dwg","emf","enc","eps","fac","fax","fit","fla","flc","fli","fpx","ftk","ged","gif","gmf","hdf","iax","ica","icb","ico","idw","iff","img","jbg","jbig","jfif","jpe","jpeg","jp2","jpg","jtf","jtp","lwf","mac","mid","midi","miff","mki","mmm",".mod","mov","mp2","mp3","mpg","mpt","msk","msp","mus","mvi","nap","ogg","pal","pbm","pcc","pcd","pcf","pct","pcx","pdd","pdf","pfr","pgm","pic","pict","pk","pm3","pm4","pm5","png","ppm","ppt","ps","psd","psp","pxr","qt","qxd","ras","rgb","rgba","rif","rip","rla","rle","rpf","rtf","scr","sdc","sdd","sdw","sgi","sid","sng","swf","tga","tiff","tif","txt","text","tub","ul","vda","vis","vob","vpg","vst","wav","wdb","win","wk1","wks","wmf","wmv","wpd","wxf","wp4","wp5","wp6","wpg","wpp","xbm","xls","xpm","xwd","yuv","zgm");
$BADMEDIA = array(".","..","CVS","thumbs","index.php","MediaInfo.txt", ".cvsignore", ".svn", "watermark");


//-- start the php session
$time = time()+$PGV_SESSION_TIME;
$date = date("D M j H:i:s T Y", $time);
//-- set the path to the pgv site so that users cannot login on one site
//-- and then automatically be logged in at another site on the same server
$pgv_path = "/";
if (!empty($SCRIPT_NAME)) $pgv_path = str_replace("\\", "/", dirname($SCRIPT_NAME));
session_set_cookie_params($date, $pgv_path);
if (($PGV_SESSION_TIME>0)&&(function_exists('session_cache_expire'))) session_cache_expire($PGV_SESSION_TIME/60);
if (!empty($PGV_SESSION_SAVE_PATH)) session_save_path($PGV_SESSION_SAVE_PATH);
if (isset($MANUAL_SESSION_START) && !empty($SID)) session_id($SID);

@session_start();

if((empty($SEARCH_SPIDER)) && (!empty($_SESSION['last_spider_name']))) // user following a search engine listing in,
	session_regenerate_id();

if(!empty($SEARCH_SPIDER)) {
	$spidertime = time();
	$spiderdate = date("d.m.Y", $spidertime);
	$_SESSION['last_spider_name'] = $SEARCH_SPIDER;
	if(isset($_SESSION['spider_count']))
		$spidercount = $_SESSION['spider_count'] + 1;
	else {
		$spidercount = 1;
		//adds a message to the log that a new spider session is starting
		require_once("includes/authentication.php");      // -- Loaded early so AddToLog works
		$outstr = preg_replace('/\s\s+/', ' ', $SEARCH_SPIDER); // trim trailing whitespace
		// Don't allow ' - ' because that is the log seperator
		$outstr = preg_replace('/ - /', ' ', $outstr);
		AddToLog("New search engine encountered: ->".$outstr."<-");
	}
	if(isset($_SESSION['last_spider_date'])) {
		if($spiderdate != $_SESSION['last_spider_date']) {
			//adds a message to the log that a new spider session is starting
			require_once("includes/authentication.php");      // -- Loaded early so AddToLog works
			$outstr = preg_replace('/\s\s+/', ' ', $SEARCH_SPIDER); // trim trailing whitespace
			// Don't allow ' - ' because that is the log seperator
			$outstr = preg_replace('/ - /', ' ', $outstr);
 			AddToLog("Returning search engine last seen ".$_SESSION['spider_count']." times on ".$_SESSION['last_spider_date']." from ".$_SESSION['last_spider_ip']." ->".$outstr."<-");
			$_SESSION['last_spider_date'] = $spiderdate;
			$spidercount = 1;
		}
	}
	$_SESSION['last_spider_date'] = $spiderdate;
	$_SESSION['spider_count'] = $spidercount;
	if(isset($_SERVER['REMOTE_ADDR']))
		$_SESSION['last_spider_ip'] = $_SERVER['REMOTE_ADDR'];
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		$_SESSION['last_spider_lang'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
}

if((!empty($SEARCH_SPIDER)) && (!empty($_SESSION['pgv_user'])) && ($_SESSION['pgv_user'] != "")) {
	$_SESSION['pgv_user'] = "";	// Don't allow search engine into user/admin mode.
}

if(!empty($SEARCH_SPIDER)) {
	// FIX SANITIZE: What other data do we need to reset back to anonymous default?
	// overkill to force a disk flush
	$_SESSION['CLANGUAGE'] = "";	// Force language to gedcom default language.
	session_write_close();
	session_start();
}

// change the session values and store if needed.
if($SESSION_HIDE_GOOGLEMAP == "true") $_SESSION['hide_googlemap'] = true;
if($SESSION_HIDE_GOOGLEMAP == "false") $_SESSION['hide_googlemap'] = false;
if($SESSION_HIDE_GOOGLEMAP == "empty") {
	if((isset($_SESSION['hide_googlemap'])) && ($_SESSION['hide_googlemap'] == true))
 		$SESSION_HIDE_GOOGLEMAP = "true";
	else 
 		$SESSION_HIDE_GOOGLEMAP = "false";
}

//-- import the post, get, and cookie variable into the scope on new versions of php
@import_request_variables("cgp");

//-- prevent sql and code injection
foreach($_REQUEST as $key=>$value) {
	if (!is_array($value)) {
		if (preg_match("/((DELETE)|(INSERT)|(UPDATE)|(ALTER)|(CREATE)|( TABLE)|(DROP))\s[A-Za-z0-9 ]{0,20}(\s(FROM)|(INTO)|(TABLE)\s)/i", $value, $imatch)>0) {
			print "Possible SQL injection detected: $key=>$value.  <b>$imatch[0]</b> Script terminated.";
			require_once("includes/authentication.php");      // -- load the authentication system
			AddToLog("Possible SQL injection detected: $key=>$value. <b>$imatch[0]</b> Script terminated.");
			exit;
		}
		//-- don't let any html in
		if (!empty($value)) $_REQUEST[$key] = preg_replace(array("/</","/>/"), array("&lt;","&gt;"), $value);
	}
	else {
		foreach($value as $key1=>$val) {
			if (!is_array($val)) {
				if (preg_match("/((DELETE)|(INSERT)|(UPDATE)|(ALTER)|(CREATE)|( TABLE)|(DROP))\s[A-Za-z0-9 ]{0,20}(\s(FROM)|(INTO)|(TABLE)\s)/i", $val, $imatch)>0) {
					print "Possible SQL injection detected: $key=>$val <b>$imatch[0]</b>.  Script terminated.";
					require_once("includes/authentication.php");      // -- load the authentication system
					AddToLog("Possible SQL injection detected: $key=>$val <b>$imatch[0]</b>.  Script terminated.");
					exit;
				}
				//-- don't let any html in
				if (!empty($val)) $_REQUEST[$key][$key1] = preg_replace(array("/</","/>/"), array("&lt;","&gt;"), $val);
			}
		}
	}
}

//-- import the gedcoms array
if (file_exists($INDEX_DIRECTORY."gedcoms.php")) {
	require_once($INDEX_DIRECTORY."gedcoms.php");
	if (!is_array($gGedcom)) $gGedcom = array();
	$i=0;
	foreach ($gGedcom as $key => $gedcom) {
		$i++;
		$gGedcom[$key]["commonsurnames"] = stripslashes($gedcom["commonsurnames"]);
		if (empty($gGedcom[$key]["id"])) $gGedcom[$key]["id"] = $i;
		if (empty($gGedcom[$key]["pgv_ver"])) $gGedcom[$key]["pgv_ver"] = $VERSION;

		// Force the gedcom to be re-imported if the code has been significantly upgraded
		if (substr($gGedcom[$key]["pgv_ver"], 0, 3) != substr($VERSION, 0, 3))
			$gGedcom[$key]["imported"] = false;
	}
}
else $gGedcom=array();

//-- connect to the database
$DBPASS = str_replace(array("\\\\", "\\\"", "\\\$"), array("\\", "\"", "\$"), $DBPASS);		// remove escape codes before using PW
check_db();

if (isset($_REQUEST["GEDCOM"])){
   $_REQUEST["GEDCOM"] = trim($_REQUEST["GEDCOM"]);
}
if (!isset($DEFAULT_GEDCOM)) $DEFAULT_GEDCOM = "";
if (empty($_REQUEST["GEDCOM"])) {
   if (isset($_SESSION["GEDCOM"])) $GEDCOM = $_SESSION["GEDCOM"];
   else {
      if (empty($GEDCOM) || empty($gGedcom[$GEDCOM])) $GEDCOM=$DEFAULT_GEDCOM;
      else if ((empty($GEDCOM))&&(count($gGedcom)>0)) {
         foreach($gGedcom as $ged_file=>$ged_array) {
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

// privacy constants moved from privacy.php
$PRIV_HIDE = -1;	//  Global constant privacy level to hide the item to all users including the admin
$PRIV_PUBLIC = 2;	//  Global constant privacy level that allows non-authenticated public visitors to view the marked information
$PRIV_USER = 1;	//  Global constant privacy level that only allows authenticated users to access the marked information
$PRIV_NONE = 0;	//  Global constant privacy level that only allows admin users to access the marked information

/**
 * Load GEDCOM configuration
 */
require_once("config_gedcom.php");
require_once(get_config_file());
if (empty($PHPGEDVIEW_EMAIL)) {
	$PHPGEDVIEW_EMAIL = "phpgedview-noreply@".preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
}

//-- make sure that the time limit is the true time limit
//-- commented out for now because PHP does not seem to be reporting it correctly on Linux
//$TIME_LIMIT = ini_get("max_execution_time");

require_once("includes/functions_name.php");

require_once("includes/authentication.php");      // -- load the authentication system

/**
  * Remote IP Address Banning
  */
 if (file_exists($INDEX_DIRECTORY."banned.php")) {
 	require($INDEX_DIRECTORY."banned.php");
 	//loops through each ip in banned.php
	foreach($banned as $key=>$value) {
		//creates a regex foreach ip
		$ipRegEx = '';
		$arrayIP = explode('*', $value);
		$ipRegEx .= $arrayIP[0];
		if (count($arrayIP) > 1) {
			for($i=1; $i < count($arrayIP); $i++) {
				if($i == (count($arrayIP)))
		 			$ipRegEx .= "\d{0,3}";
	 			else
	 				$ipRegEx .= "\d{0,3}".$arrayIP[$i];
			}
		}
		//checks the remote ip address against each ip regex
		if (preg_match('/^'.$ipRegEx.'/', $_SERVER['REMOTE_ADDR'])) {
			//adds a message to the log and exits with an Access Denied header
 			AddToLog("genservice.php blocked IP Address: ".$_SERVER['REMOTE_ADDR']." by regex: ".$ipRegEx);
 			header("HTTP/1.1 403 Access Denied");
 			exit;
 		}
	}
 }

/**
 * do not include print functions when using the gdbi protocol
 */
if (strstr($SCRIPT_NAME, "client.php")===false && strstr($SCRIPT_NAME, "genservice.php")===false) {
	//-- load media specific functions
	require_once("includes/functions_print.php");
	require_once("includes/functions_rtl.php");
}

if ($MULTI_MEDIA) require_once("includes/functions_mediadb.php");
require_once("includes/functions_date.php");

if (empty($PEDIGREE_GENERATIONS)) $PEDIGREE_GENERATIONS = $DEFAULT_PEDIGREE_GENERATIONS;

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
$adminfile 				= array();
$editorfile				= array();
$countryfile			= array();
$faqlistfile			= array();
$extrafile				= array();
$factsarray 			= array();
$pgv_lang_name 			= array();
$ALPHABET_upper			= array();
$ALPHABET_lower			= array();
$MULTI_LETTER_ALPHABET	= array();
$DICTIONARY_SORT		= array();
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
	$adminfile[$key]			= $value["adminfile"];
	$editorfile[$key]			= $value["editorfile"];
	$countryfile[$key]			= $value["countryfile"];
	$faqlistfile[$key]			= $value["faqlistfile"];
	$extrafile[$key]			= $value["extrafile"];
	$ALPHABET_upper[$key]		= $value["ALPHABET_upper"];
	$ALPHABET_lower[$key]		= $value["ALPHABET_lower"];
	$MULTI_LETTER_ALPHABET[$key] = $value["MULTI_LETTER_ALPHABET"];
	$DICTIONARY_SORT[$key]		= $value["DICTIONARY_SORT"];
	$DATE_FORMAT_array[$key]	= $value["DATE_FORMAT"];
	$TIME_FORMAT_array[$key]	= $value["TIME_FORMAT"];;
	$WEEK_START_array[$key]		= $value["WEEK_START"];
	$TEXT_DIRECTION_array[$key]	= $value["TEXT_DIRECTION"];
	$NAME_REVERSE_array[$key]	= $value["NAME_REVERSE"];

	$pgv_lang["lang_name_$key"]	= $value["pgv_lang"];
}

/**
 * The following business rules are used to choose currently active language
 * 1. Use the language in visitor's browser settings if it is supported in the PGV site.
 *    If it is not supported, use the GEDCOM configuration setting.
 * 2. If the user has chosen a language from the list or the flags, use their choice.
 * 3. When the user logs in, switch to the language in their user profile unless the 
 *    user made a language choice prior to logging in.
 * 4. When a user logs out their current language choice is ignored and the site will
 *    revert back to the language they first saw when arriving at the site according to
 *    rule 1.
 */
if ((!empty($logout))&&($logout==1)) unset($_SESSION["CLANGUAGE"]);		// user is about to log out

if (($ENABLE_MULTI_LANGUAGE)&&(empty($_SESSION["CLANGUAGE"]))&&(empty($SEARCH_SPIDER))) {
	if (isset($HTTP_ACCEPT_LANGUAGE)) $accept_langs = $HTTP_ACCEPT_LANGUAGE;
	else if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accept_langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	if (isset($accept_langs)) {
		// Seach list of supported languages for this Browser's preferred page languages
		$langs_array = preg_split("/(,\s*)|(;\s*)/", $accept_langs);
		$foundLanguage = false;
		foreach ($langs_array as $browserLang) {
			$browserLang = strtolower($browserLang).";";
			foreach ($pgv_lang_use as $language => $active) {
				if (!$active) continue;
				if (strpos($lang_langcode[$language], $browserLang) === false) continue;
				$LANGUAGE = $language;
				$foundLanguage = true;
				break;
			}
			if ($foundLanguage) break;
		}
	}
}
$deflang = $LANGUAGE;

if(empty($SEARCH_SPIDER)) {
   if (!empty($_SESSION['CLANGUAGE'])) $CLANGUAGE = $_SESSION['CLANGUAGE'];
   else if (!empty($HTTP_SESSION_VARS['CLANGUAGE'])) $CLANGUAGE = $HTTP_SESSION_VARS['CLANGUAGE'];
   if (!empty($CLANGUAGE)) {
      $LANGUAGE = $CLANGUAGE;
   }
}

if (($ENABLE_MULTI_LANGUAGE) && (empty($SEARCH_SPIDER))) {
	if ((isset($_REQUEST['changelanguage']))&&($_REQUEST['changelanguage']=="yes")) {
		if (!empty($_REQUEST['NEWLANGUAGE']) && isset($pgv_language[$_REQUEST['NEWLANGUAGE']])) {
			$LANGUAGE=$_REQUEST['NEWLANGUAGE'];
			unset($_SESSION["upcoming_events"]);
			unset($_SESSION["todays_events"]);
		}
	}
}

require_once("includes/templecodes.php");		//-- load in the LDS temple code translations

require_once("privacy.php");
//-- load the privacy file
require_once(get_privacy_file());
//-- load the privacy functions
require_once("includes/functions_privacy.php");

//-----------------------------------
//-- if user wishes to logout this is where we will do it
if ((!empty($logout))&&($logout==1)) {
	userLogout(getUserId());
	if ($REQUIRE_AUTHENTICATION) {
//		header("Location: ".$HOME_SITE_URL);
		header("Location: {$SERVER_URL}");
		exit;
	}
}

// Define some constants to save calculating the same value repeatedly.
define('PGV_USER_ID',           getUserId  ());
define('PGV_USER_NAME',         getUserName());
define('PGV_USER_IS_ADMIN',     userIsAdmin       (PGV_USER_ID));
define('PGV_USER_GEDCOM_ADMIN', userGedcomAdmin   (PGV_USER_ID));
define('PGV_USER_CAN_ACCESS',   userCanAccess     (PGV_USER_ID));
define('PGV_USER_CAN_EDIT',     userCanEdit       (PGV_USER_ID));
define('PGV_USER_CAN_ACCEPT',   userCanAccept     (PGV_USER_ID));
define('PGV_USER_AUTO_ACCEPT',  userAutoAccept    (PGV_USER_ID));
define('PGV_USER_ACCESS_LEVEL', getUserAccessLevel(PGV_USER_ID));
define('PGV_USER_GEDCOM_ID',    get_user_gedcom_setting(PGV_USER_ID, $GEDCOM, 'gedcomid'));
define('PGV_USER_ROOT_ID',      get_user_gedcom_setting(PGV_USER_ID, $GEDCOM, 'rootid'));
if (empty($gGedcom) || DB::isError($DBCONN)) {
	define('PGV_GED_ID', null);
} else {
	define('PGV_GED_ID', $DBCONN->escapeSimple($gGedcom->mGEDCOMId));
}

// Load all the language variables and language-specific functions
loadLanguage($LANGUAGE, true);

// Check for page views exceeding the limit
CheckPageViews();

if (!isset($SCRIPT_NAME)) $SCRIPT_NAME=$_SERVER["PHP_SELF"];

if (!isset($show_context_help)) $show_context_help = "";
if (!isset($_SESSION["show_context_help"])) $_SESSION["show_context_help"] = $SHOW_CONTEXT_HELP;
if (!isset($_SESSION["pgv_user"])) $_SESSION["pgv_user"] = "";
if (!isset($_SESSION["cookie_login"])) $_SESSION["cookie_login"] = false;
if (isset($SHOW_CONTEXT_HELP) && $show_context_help==='yes') $_SESSION["show_context_help"] = true;
if (isset($SHOW_CONTEXT_HELP) && $show_context_help==='no') $_SESSION["show_context_help"] = false;
if (!isset($USE_THUMBS_MAIN)) $USE_THUMBS_MAIN = false;
if ((strstr($SCRIPT_NAME, "editconfig.php")===false)
	&&(strstr($SCRIPT_NAME, "editconfig_help.php")===false)) {
	if ((!check_db())||(!adminUserExists())) {
		header("Location: editconfig.php");
		exit;
	}
	
	if ((count($gGedcom)==0)||(!check_for_import($GEDCOM))) {
		$scriptList = array("editconfig_gedcom.php", "help_text.php", "editconfig_help.php", "editgedcoms.php", "uploadgedcom.php", "login.php", "admin.php", "config_download.php", "addnewgedcom.php", "validategedcom.php", "addmedia.php", "importgedcom.php", "client.php", "edit_privacy.php", "upgrade33-40.php", "gedcheck.php", "printlog.php", "editlang.php", "editlang_edit.php" ,"useradmin.php");
		$inList = false;
		foreach ($scriptList as $key => $listEntry) {
			if (strstr($SCRIPT_NAME, $listEntry)) {
				$inList = true;
				break;
			}
		}
		if (!$inList) {
			header("Location: editgedcoms.php");
			exit;
		}
		unset($scriptList);
	}

	if ($REQUIRE_AUTHENTICATION) {
		if (!PGV_USER_ID) {
			if ((strstr($SCRIPT_NAME, "login.php")===false)
					&&(strstr($SCRIPT_NAME, "login_register.php")===false)
					&&(strstr($SCRIPT_NAME, "client.php")===false)
					&&(strstr($SCRIPT_NAME, "genservice.php")===false)
					&&(strstr($SCRIPT_NAME, "help_text.php")===false)
					&&(strstr($SCRIPT_NAME, "message.php")===false)) {
				if (!empty($_REQUEST['auth']) && $_REQUEST['auth']=="basic") { //if user is attempting basic authentication //TODO: Update if degest auth is ever implemented
						basicHTTPAuthenticateUser();
				} else {
					$url = basename($_SERVER["PHP_SELF"])."?".$QUERY_STRING;
					if (stristr($url, "index.php")!==false) {
						if (stristr($url, "ctype=")===false) {
							if ((!isset($_SERVER['HTTP_REFERER'])) || (stristr($_SERVER['HTTP_REFERER'],$SERVER_URL)===false)) $url .= "&ctype=gedcom";
						}
					}
					if (stristr($url, "ged=")===false)  {
						$url.="&ged=".$GEDCOM;
					}
					$url = str_replace("?&", "?", $url);
					header("Location: login.php?url=".urlencode($url));
					exit;
				}
			}
		}
	}

   // -- setup session information for tree clippings cart features
   if ((!isset($_SESSION['cart'])) || (!empty($_SESSION['last_spider_name']))) {	// reset cart everytime for spiders
     $_SESSION['cart'] = array();
   }
   $cart = $_SESSION['cart'];

   $_SESSION['CLANGUAGE'] = $LANGUAGE;
   if (!isset($_SESSION["timediff"])) {
	   $_SESSION["timediff"] = 0;
   }

	//-- load any editing changes
	if (PGV_USER_CAN_EDIT && file_exists($INDEX_DIRECTORY."pgv_changes.php")) {
		require_once($INDEX_DIRECTORY."pgv_changes.php");
	} else {
  	$pgv_changes = array();
	}

	if (empty($LOGIN_URL)) {
		$LOGIN_URL = "login.php";
	}

} else {
	check_db();
}

//-- load the user specific theme
if (PGV_USER_ID && !isset($_REQUEST['logout'])) {
	//-- update the login time every 5 minutes
	if (!isset($_SESSION['activity_time']) || (time()-$_SESSION['activity_time'])>300) {
		userUpdateLogin(PGV_USER_ID);
		$_SESSION['activity_time'] = time();
	}

	$usertheme = get_user_setting(PGV_USER_ID, 'theme');
	if ((!empty($_POST["user_theme"]))&&(!empty($_POST["oldusername"]))&&($_POST["oldusername"]==PGV_USER_ID)) $usertheme = $_POST["user_theme"];
	if ((!empty($usertheme)) && (file_exists($usertheme."theme.php")))  {
		$THEME_DIR = $usertheme;
	}
}

if (isset($_SESSION["theme_dir"]))
{
	$THEME_DIR = $_SESSION["theme_dir"];
	if (PGV_USER_ID) {
		if (get_user_setting(PGV_USER_ID, 'editaccount')=='Y') unset($_SESSION["theme_dir"]);
	}
}

if (empty($THEME_DIR)) $THEME_DIR="standard/";
if (file_exists($THEME_DIR."theme.php")) require_once($THEME_DIR."theme.php");
else {
	$THEME_DIR = "themes/standard/";
	require_once($THEME_DIR."theme.php");
}

require_once("hitcount.php"); //--load the hit counter

if ($Languages_Default) {					// If Languages not yet configured
	$pgv_lang_use["english"] = false;		//   disable English
	$pgv_lang_use["$LANGUAGE"] = true;		//     and enable according to Browser pref.
	$language_settings["english"]["pgv_lang_use"] = false;
	$language_settings["$LANGUAGE"]["pgv_lang_use"] = true;
}
?>

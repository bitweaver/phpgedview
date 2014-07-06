<?php
/**
 * Temporary bitweaver session logic pruning
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
 * @version $Id$
 */
if (strstr($_SERVER["SCRIPT_NAME"],"session")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}

if( !defined( 'PHPGEDVIEW_DB_PREFIX' ) ) {
	$lastQuote = strrpos( BIT_DB_PREFIX, '`' );
	if( $lastQuote != FALSE ) {
		$lastQuote++;
	}
	$prefix = substr( BIT_DB_PREFIX,  $lastQuote );
	define( 'PHPGEDVIEW_DB_PREFIX', $prefix.'pgv_' );
}

$TBLPREFIX = PHPGEDVIEW_DB_PREFIX;
/**
 * These need replacing with bitweaver 
 */
$INDEX_DIRECTORY = "./index/";					//-- Readable and Writeable Directory to store index files (include the trailing "/")
$PGV_STORE_MESSAGES = true;						//-- allow messages sent to users to be stored in the PGV system
$PGV_SIMPLE_MAIL = true;						//-- allow admins to set this so that they can override the name <emailaddress> combination in the emails
$USE_REGISTRATION_MODULE = false;				//-- turn on the user self registration module
$REQUIRE_ADMIN_AUTH_REGISTRATION = true;		//-- require an admin user to authorize a new registration before a user can login
$ALLOW_CHANGE_GEDCOM = true;					//-- A true value will provide a link in the footer to allow users to change the gedcom they are viewing
$LOGFILE_CREATE = "weekly";					//-- set how often new log files are created, "none" turns logs off, "daily", "weekly", "monthly", "yearly"
$LOG_LANG_ERROR = false;						//-- Set if non-existing language variables should be written to a logfile
$PGV_SESSION_TIME = "7200";						//-- number of seconds to wait before an inactive session times out
$SERVER_URL = "http://localhost/phpGedView/";	//-- the URL used to access this server
$MAX_VIEWS = "100";								//-- the maximum number of page views per xx seconds per session
$MAX_VIEW_TIME = "0";							//-- the number of seconds in which the maximum number of views must not be reached
$PGV_MEMORY_LIMIT = "32M";						//-- the maximum amount of memory that PGV should be allowed to consume
$ALLOW_REMEMBER_ME = true;						//-- whether the users have the option of being remembered on the current computer
$CONFIG_VERSION = "4.0";						//-- the version this config file goes to

$DIRECTORY_MODE = "ldap";						//-- User info stored in db or ldap directory
$COMMIT_COMMAND = "";							//-- Choices are empty string, cvs or svn

$CONFIGURED = true;

define('PGV_PHPGEDVIEW',      'PhpGedView');
define('PGV_VERSION',         '4.2.2');

// Enable debugging output?
define('PGV_DEBUG',       false);
define('PGV_DEBUG_SQL',   false);
define('PGV_DEBUG_PRIV',  false);

// Error reporting
define('PGV_ERROR_LEVEL', 2); // 0=none, 1=minimal, 2=full

// Environmental requirements
define('PGV_REQUIRED_PHP_VERSION',     '5.2.0'); // 5.2.3 is recommended
define('PGV_REQUIRED_PRIVACY_VERSION', '3.1');

// Regular expressions for validating user input, etc.
define('PGV_REGEX_XREF',     '[A-Za-z0-9:_-]+');
define('PGV_REGEX_TAG',      '[_A-Z][_A-Z0-9]*');
define('PGV_REGEX_INTEGER',  '-?\d+');
define('PGV_REGEX_ALPHA',    '[a-zA-Z]+');
define('PGV_REGEX_ALPHANUM', '[a-zA-Z0-9]+');
define('PGV_REGEX_BYTES',    '[0-9]+[bBkKmMgG]?');
define('PGV_REGEX_USERNAME', '[^<>"%{};]+');
define('PGV_REGEX_PASSWORD', '.{6,}');
define('PGV_REGEX_NOSCRIPT', '[^<>"&%{};]+');
define('PGV_REGEX_URL',      '[\/0-9A-Za-z_!~*\'().;?:@&=+$,%#-]+'); // Simple list of valid chars
define('PGV_REGEX_EMAIL',    '[^\s<>"&%{};@]+@[^\s<>"&%{};@]+');
define('PGV_REGEX_UNSAFE',   '[\x00-\xFF]*'); // Use with care and apply additional validation!

// UTF8 representation of various characters
define('PGV_UTF8_BOM',    "\xEF\xBB\xBF"); // U+FEFF
define('PGV_UTF8_LRM',    "\xE2\x80\x8E"); // U+200E
define('PGV_UTF8_RLM',    "\xE2\x80\x8F"); // U+200F
define('PGV_UTF8_MALE',   "\xE2\x99\x82"); // U+2642
define('PGV_UTF8_FEMALE', "\xE2\x99\x80"); // U+2640

// Alternatives to BMD events for lists, charts, etc.
define('PGV_EVENTS_BIRT', 'BIRT|CHR|BAPM|_BRTM|ADOP');
define('PGV_EVENTS_DEAT', 'DEAT|BURI|CREM');
define('PGV_EVENTS_MARR', 'MARR|MARB');
define('PGV_EVENTS_DIV',  'DIV|ANUL|_SEPR');

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

@ini_set('arg_separator.output', '&amp;');
@ini_set('error_reporting', 0);
@ini_set('display_errors', '1');
@error_reporting(0);

//-- required for running PHP in CGI Mode on Windows
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = "";

//-- list of critical configuration variables
$CONFIG_VARS = array(
	"PGV_BASE_DIRECTORY",
	"PGV_DATABASE",
	"DBTYPE",
	"DBHOST",
	"DBUSER",
	"DBPASS",
	"DBNAME",
	"INDEX_DIRECTORY",
	"USE_REGISTRATION_MODULE",
	"ALLOW_REMEMBER_ME",
	"DEFAULT_GEDCOM",
	"ALLOW_CHANGE_GEDCOM",
	"LOGFILE_CREATE",
	"PGV_SESSION_SAVE_PATH",
	"PGV_SESSION_TIME",
	"GEDCOMS",
	"SERVER_URL",
	"PGV_MEMORY_LIMIT",
	"PGV_STORE_MESSAGES",
	"PGV_SIMPLE_MAIL",
	"CONFIG_VERSION",
	"CONFIGURED",
	"MANUAL_SESSON_START"
);

//-- load file for language settings
require_once( PHPGEDVIEW_PKG_PATH."includes/lang_settings_std.php");
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
	if (strstr($_SERVER["REQUEST_URI"], "CONFIG_VARS=")) break;

	// $CONFIG_VARS is safe: now check for any in its list
	foreach($CONFIG_VARS as $indexval => $VAR) {
		if (strstr($_SERVER["REQUEST_URI"], $VAR."=")) break;
	}

	// Check for $LANGUAGE variable override, except from search engines
	//  Later, will fine tune to allow search engines multi-language support
	//  Currently, search engines get default language of gedcom only.
	if ((isset($_REQUEST["NEWLANGUAGE"])) && (empty($SEARCH_SPIDER))) {
		if (empty($language_settings[$_REQUEST["NEWLANGUAGE"]]["pgv_lang_use"])) break;
	}

	$configOverride = false;
	break;
}

unset($CONFIG_VARS);	// Not needed any more

//-- check if they are trying to hack
if ($configOverride) {
	print "Config variable override detected. Possible hacking attempt. Script terminated.\n";
	if ((!ini_get('register_globals'))||(ini_get('register_globals')=="Off")) {
		//-- load common functions
		require_once("functions/functions.php");
		//-- load db specific functions
		require_once("functions/functions_db.php");
		AddToLog("Config variable override detected. Possible hacking attempt. Script terminated.");
		AddToLog("URI>".$_SERVER["REQUEST_URI"]."<");
	}
	exit;
}

if (empty($CONFIG_VERSION)) $CONFIG_VERSION = "2.65";
if (!isset($PGV_SIMPLE_MAIL)) $PGV_SIMPLE_MAIL = false;

if (empty($PGV_MEMORY_LIMIT)) $PGV_MEMORY_LIMIT = "32M";

//--load common functions
require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions.php" );
//-- set the error handler
// $OLD_HANDLER = set_error_handler("pgv_error_handler");
//-- load db specific functions
require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions_db.php" );

//-- Setup array of media types
$MEDIATYPE = array("a11","acb","adc","adf","afm","ai","aiff","aif","amg","anm","ans","apd","asf","au","avi","awm","bga","bmp","bob","bpt","bw","cal","cel","cdr","cgm","cmp","cmv","cmx","cpi","cur","cut","cvs","cwk","dcs","dib","dmf","dng","doc","dsm","dxf","dwg","emf","enc","eps","fac","fax","fit","fla","flc","fli","fpx","ftk","ged","gif","gmf","hdf","iax","ica","icb","ico","idw","iff","img","jbg","jbig","jfif","jpe","jpeg","jp2","jpg","jtf","jtp","lwf","mac","mid","midi","miff","mki","mmm",".mod","mov","mp2","mp3","mpg","mpt","msk","msp","mus","mvi","nap","ogg","pal","pbm","pcc","pcd","pcf","pct","pcx","pdd","pdf","pfr","pgm","pic","pict","pk","pm3","pm4","pm5","png","ppm","ppt","ps","psd","psp","pxr","qt","qxd","ras","rgb","rgba","rif","rip","rla","rle","rpf","rtf","scr","sdc","sdd","sdw","sgi","sid","sng","swf","tga","tiff","tif","txt","text","tub","ul","vda","vis","vob","vpg","vst","wav","wdb","win","wk1","wks","wmf","wmv","wpd","wxf","wp4","wp5","wp6","wpg","wpp","xbm","xls","xpm","xwd","yuv","zgm");
$BADMEDIA = array(".","..","CVS","thumbs","index.php","MediaInfo.txt", ".cvsignore");

if ( isset($_REQUEST["ged"]) ) {
	$GEDCOM = trim($_REQUEST["ged"]);
} else $GEDCOM = null;
if ( $GEDCOM >= 1 ) $GEDCOM = get_gedcom_from_id($GEDCOM);
$_SESSION["GEDCOM"] = $GEDCOM;
$INDILIST_RETRIEVED = false;
$FAMLIST_RETRIEVED = false;

require_once( PHPGEDVIEW_PKG_PATH."config_gedcom.php" );
//require_once( get_config_file() );

//-- make sure that the time limit is the true time limit
//-- commented out for now because PHP does not seem to be reporting it correctly on Linux
//$TIME_LIMIT = ini_get("max_execution_time");

require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions_rtl.php" );
require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions_name.php" );

if ($MULTI_MEDIA) require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions_mediadb.php" );
require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions_date.php" );

if (empty($PEDIGREE_GENERATIONS)) $PEDIGREE_GENERATIONS = $DEFAULT_PEDIGREE_GENERATIONS;

/* Re-build the various language-related arrays
 *  Note:
 *  This code existed in both lang_settings_std.php and in lang_settings.php.
 *  It has been removed from both files and inserted here, where it belongs.
 */
$languages            =array();
$pgv_lang_use         =array();
$pgv_lang_self        =array();
$lang_short_cut       =array();
$lang_langcode        =array();
$pgv_language         =array();
$confighelpfile       =array();
$helptextfile         =array();
$flagsfile            =array();
$factsfile            =array();
$adminfile            =array();
$editorfile           =array();
$countryfile          =array();
$faqlistfile          =array();
$extrafile            =array();
$factsarray           =array();
$pgv_lang_name        =array();
$ALPHABET_upper       =array();
$ALPHABET_lower       =array();
$MULTI_LETTER_ALPHABET=array();
$MULTI_LETTER_EQUIV   =array();
$DICTIONARY_SORT      =array();
$COLLATION            =array();
$DATE_FORMAT_array    =array();
$TIME_FORMAT_array    =array();
$WEEK_START_array     =array();
$TEXT_DIRECTION_array =array();
$NAME_REVERSE_array   =array();

foreach ($language_settings as $key => $value) {
//	if (!isset($value['pgv_lang_self']) || !isset($value['pgv_language']) || !file_exists($value['pgv_language'])) continue;
	if (!isset($value['pgv_lang_self']) || !isset($value['pgv_language'])) continue;
	$languages[$key]            =$value["pgv_langname"];
	$pgv_lang_use[$key]         =$value["pgv_lang_use"];
	$pgv_lang_self[$key]        =$value["pgv_lang_self"];
	$lang_short_cut[$key]       =$value["lang_short_cut"];
	$lang_langcode[$key]        =$value["langcode"];
	$pgv_language[$key]         =$value["pgv_language"];
	$confighelpfile[$key]       =$value["confighelpfile"];
	$helptextfile[$key]         =$value["helptextfile"];
	$flagsfile[$key]            =$value["flagsfile"];
	$factsfile[$key]            =$value["factsfile"];
	$adminfile[$key]            =$value["adminfile"];
	$editorfile[$key]           =$value["editorfile"];
	$countryfile[$key]          =$value["countryfile"];
	$faqlistfile[$key]          =$value["faqlistfile"];
	$extrafile[$key]            =$value["extrafile"];
	$ALPHABET_upper[$key]       =$value["ALPHABET_upper"];
	$ALPHABET_lower[$key]       =$value["ALPHABET_lower"];
	$MULTI_LETTER_ALPHABET[$key]=$value["MULTI_LETTER_ALPHABET"];
	$MULTI_LETTER_EQUIV[$key]   =$value["MULTI_LETTER_EQUIV"];
	$DICTIONARY_SORT[$key]      =$value["DICTIONARY_SORT"];
	$COLLATION[$key]            =$value["COLLATION"];
	$DATE_FORMAT_array[$key]    =$value["DATE_FORMAT"];
	$TIME_FORMAT_array[$key]    =$value["TIME_FORMAT"];;
	$WEEK_START_array[$key]     =$value["WEEK_START"];
	$TEXT_DIRECTION_array[$key] =$value["TEXT_DIRECTION"];
	$NAME_REVERSE_array[$key]   =$value["NAME_REVERSE"];

	$pgv_lang["lang_name_$key"] =$value["pgv_lang_self"];
}

/**
 * The following business rules are used to choose currently active language
 * 1. If the user has chosen a language from the list or the flags, use their choice.
 * 2. When the user logs in, switch to the language in their user profile
 * 3. Use the language in visitor's browser settings if it is supported in the PGV site.
 *    If it is not supported, use the gedcom configuration setting.
 * 4. When a user logs out their current language choice is ignored and the site will
 *    revert back to the language they first saw when arriving at the site according to
 *    rule 3.
 */
if ((!empty($_REQUEST['logout']))&&($_REQUEST['logout']==1)) unset($_SESSION["CLANGUAGE"]);		// user is about to log out

if (($ENABLE_MULTI_LANGUAGE)&&(empty($_SESSION["CLANGUAGE"]))&&(empty($SEARCH_SPIDER))) {
   if (isset($HTTP_ACCEPT_LANGUAGE)) $accept_langs = $HTTP_ACCEPT_LANGUAGE;
   else if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accept_langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
   if (isset($accept_langs)) {
      if (strstr($accept_langs, ",")) {
         $langs_array = preg_split("/(,\s*)|(;\s*)/", $accept_langs);
         for ($i=0; $i<count($langs_array); $i++) {
            if (!empty($langcode[$langs_array[$i]]) && $pgv_lang_use[$langcode[$langs_array[$i]]]) {
               $LANGUAGE = $langcode[$langs_array[$i]];
               break;
            }
         }
      }
      else {
         if (!empty($langcode[$accept_langs])) $LANGUAGE = $langcode[$accept_langs];
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

// Load all the language variables and language-specific functions
loadLanguage($LANGUAGE, true);

require_once( PHPGEDVIEW_PKG_PATH."privacy.php" );
//-- load the privacy file
require_once( PHPGEDVIEW_PKG_PATH.get_privacy_file() );
//-- load the privacy functions
require_once( PHPGEDVIEW_PKG_PATH."includes/functions/functions_privacy.php");

if (!isset($SCRIPT_NAME)) $SCRIPT_NAME=$_SERVER["SCRIPT_NAME"];

$monthtonum = array();
$monthtonum["jan"] = 1;
$monthtonum["feb"] = 2;
$monthtonum["mar"] = 3;
$monthtonum["apr"] = 4;
$monthtonum["may"] = 5;
$monthtonum["jun"] = 6;
$monthtonum["jul"] = 7;
$monthtonum["aug"] = 8;
$monthtonum["sep"] = 9;
$monthtonum["oct"] = 10;
$monthtonum["nov"] = 11;
$monthtonum["dec"] = 12;
$monthtonum["tsh"] = 1;
$monthtonum["csh"] = 2;
$monthtonum["ksl"] = 3;
$monthtonum["tvt"] = 4;
$monthtonum["shv"] = 5;
$monthtonum["adr"] = 6;
$monthtonum["ads"] = 7;
$monthtonum["nsn"] = 8;
$monthtonum["iyr"] = 9;
$monthtonum["svn"] = 10;
$monthtonum["tmz"] = 11;
$monthtonum["aav"] = 12;
$monthtonum["ell"] = 13;

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
   if ($gBitUser->isAdmin()) {
      if (file_exists($INDEX_DIRECTORY."pgv_changes.php")) require_once($INDEX_DIRECTORY."pgv_changes.php");
      else $pgv_changes = array();
   }
   else $pgv_changes = array();

// require_once("hitcount.php"); //--load the hit counter

if ($Languages_Default) {					// If Languages not yet configured
	$pgv_lang_use["english"] = false;		//   disable English
	$pgv_lang_use["$LANGUAGE"] = true;		//     and enable according to Browser pref.
	$language_settings["english"]["pgv_lang_use"] = false;
	$language_settings["$LANGUAGE"]["pgv_lang_use"] = true;
}
?>
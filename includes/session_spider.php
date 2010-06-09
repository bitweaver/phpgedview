<?php
/**
 * Startup and session logic for handling Bots and Spiders
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2008 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage admin
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_SESSION_SPIDER_PHP', '');

/**
 * Changes the session same for known spiders
 * session names are limited to alphanum upper and lower only.
 * $outname = '__Spider-name-:/alphanum_only__';
 * Example  =  sess_xxGOOGLEBOTfsHTTPcffWWWdGOOGLxx
 * Matchable by "ls sess_xx??????????????????????????xx"
 *
 * @param string $bot_name
 * @param string $bot_language
 * @return string
 */
function gen_spider_session_name($bot_name, $bot_language) {
	$outname = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

	$bot_limit = strlen($bot_name);
	if($bot_limit > 27)
	$bot_limit = 27;
	for($x=0; $x < $bot_limit; $x++) {
		if(preg_match('/^[a-zA-Z0-9]+$/', $bot_name{$x}))
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


/**
  * Remote IP Address Banning
  */
if (file_exists($INDEX_DIRECTORY."banned.php")) {
	require($INDEX_DIRECTORY."banned.php");
	//loops through each ip in banned.php
	foreach($banned as $value) {
		//creates a regex foreach ip
		$ipRegEx = '';
		if (is_array($value)) {
			// New style: aa.bb.cc.dd,comment
			$arrayIP = explode('*', $value[0]);
			$comment = $value[1];
		} else {
			// Old style: aa.bb.cc.dd
			$arrayIP = explode('*', $value);
			$comment = '';
		}
		$ipRegEx .= $arrayIP[0];
		if (count($arrayIP) > 1) {
			for($i=1; $i < count($arrayIP); $i++) {
				if($i == (count($arrayIP))) $ipRegEx .= "\d{0,3}";
	 			else $ipRegEx .= "\d{0,3}".$arrayIP[$i];
			}
		}
		//checks the remote ip address against each ip regex
		if (preg_match('/^'.$ipRegEx.'/', $_SERVER['REMOTE_ADDR'])) {
			//adds a message to the log and exits with an Access Denied header
			if (empty($comment)) {
				AddToLog("genservice.php blocked IP Address: ".$_SERVER['REMOTE_ADDR']." by regex: ".$ipRegEx);
			} else {
				AddToLog("genservice.php blocked IP Address: ".$_SERVER['REMOTE_ADDR']." by regex: ".$ipRegEx.' ('.$comment.')');
			}
			header("HTTP/1.1 403 Access Denied");
			exit;
		}
	}
}

// Search Engines are treated special, and receive only core data, without the
// pretty bells and whistles.  Recursion is also going to be kept to a minimum.
// Max uncompressed page output has to be under 100k.  Spiders do not index the
// rest of the file.

global $SEARCH_SPIDER;
$SEARCH_SPIDER = false;		// set empty at start

$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";

$worms = array(
	'oBot',
	'Indy Library',
	'XXX',
//	'robotgenius',
	'Super_Ale',
	'Wget',
	'DataCha',
	'libwww-perl',
	'LWP::Simple',
	'lwp-trivial',
	'MJ.*bot',
//	'ru.*rv',
	'DotBot',
	'HTTrack',
	'AISearchBot',
	'panscient.com',
	'Plonebot',
//	'Mozilla([^\/])|(\/[\D])',	// legitimate Mozilla-based browsers have something like "Mozilla/5.0"
	'Mozilla[^\/]',		// legitimate Mozilla-based browsers have something like "Mozilla/5.0"
	'Mozilla\/[^456]',	// legitimate Mozilla-based browsers have something like "Mozilla/5.0"
	'^Mozilla\/[456]\.0$',	// legitimate Mozilla-based browsers have something following "Mozilla/5.0"
	'Speedy.*Spider',
	'KaloogaBot',		// Image search engines have no business searching a Genealogy site
	'DBLBot',
	'TurnitinBot',		// Plagiarism detectors have no business searching a Genealogy site
	'(Microsoft)|(Internet)|(Explorer)'		// Internet Explorer self-identifies with "MSIE"
	);

$quitReason = "";

// check for attempt to redirect
if (preg_match("/=.*:\/\//i", rawurldecode($_SERVER["REQUEST_URI"]))) {
	$quitReason = "Embedded URL detected";
}

// check for worms and bad bots
if ($quitReason == "") {
	foreach ($worms as $worm) {
		if (preg_match('/'.$worm.'/i', $ua)) {
			$quitReason = "Blocked crawler detected";
			break;
		}
	}
}

// Do we have a reason to quit now?
if ($quitReason != "") {
	if ((!ini_get('register_globals'))||(strtolower(ini_get('register_globals'))=="off")) {
		//-- load common functions
		require_once("includes/functions/functions.php");
		//-- load db specific functions
		require_once("includes/functions/functions_db.php");
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
	'PHP-SOAP',
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
	'Liferea/',
	'W3C_Validator'
	);

// Here we list the search engines whose accesses we don't need to log.
// This avoids cluttering the log files with useless entries
$known_spiders = array(
	'Googlebot',
	'Yahoo Slurp',
	'msnbot',
	'Ask Jeeves',
	'Mediapartners-Google',
	'Feedfetcher-Google',
	'Twiceler'
);

// We overlay the following name with carefully selected characters.
// This is to avoid XSS problems.  Alpha : . / - _ only.  Yes, the following string is 72 chars.
$spider_name = '                                                                        ';

// If you want to disable spider detection, set real to true here.
$real = false;

if($ua != "") {
	foreach($real_browsers as $browser_check) {
		if (eregi($browser_check, $ua)!==false) {
			$real = true;
			break;
		}
	}
	// check for old Netscapes.
	if (eregi("Mozilla/", $ua)) {
		if (eregi("compatible", $ua)===false) {
			if (eregi("\[..\]", $ua)!==false) {
				$real = true;
			}
			if (eregi("Macintosh", $ua)!==false) {
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
		if(preg_match('/^[a-zA-Z]+$/', $bot_name{$x})) {
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
'gedrecord.php'
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
 *   Allow an admin to mark IP addresses as known search engines even if
 *   they are not automatically detected above.   Setting his own IP address
 *   in this file allows him to see exactly what the search engine receives.
 *   To return to normal, the admin MUST use a different IP to get to admin
 *   mode or edit search_engines.php by hand.
 */
if (file_exists($INDEX_DIRECTORY."search_engines.php")) {
	require($INDEX_DIRECTORY."search_engines.php");
	//loops through each ip in search_engines.php
	foreach($search_engines as $value) {
		//creates a regex foreach ip
		$ipRegEx = '';
		if (is_array($value)) {
			// New style: aa.bb.cc.dd,comment
			$arrayIP = explode('*', $value[0]);
			$comment = $value[1];
		} else {
			// Old style: aa.bb.cc.dd
			$arrayIP = explode('*', $value);
			$comment = '';
		}
		$ipRegEx .= $arrayIP[0];
		if (count($arrayIP) > 1) {
			for($i=1; $i < count($arrayIP); $i++) {
				if ($i == (count($arrayIP))) $ipRegEx .= "\d{0,3}";
 				else $ipRegEx .= "\d{0,3}".$arrayIP[$i];
			}
		}
		//checks the remote ip address against each ip regex
		if (preg_match('/^'.$ipRegEx.'/', $_SERVER['REMOTE_ADDR'])) {
			if (empty($SEARCH_SPIDER)) {
				if (empty($comment)) $SEARCH_SPIDER = "Manual Search Engine entry of ".$_SERVER['REMOTE_ADDR'];
				else $SEARCH_SPIDER = "Manual Search Engine entry of ".$_SERVER['REMOTE_ADDR'].' ('.$comment.')';
			}
			$bot_name = "MAN".$_SERVER['REMOTE_ADDR'];
			$bot_session = gen_spider_session_name($bot_name, "");
			session_id($bot_session);
			break;
		}
	}
}

if((empty($SEARCH_SPIDER)) && (!empty($_SESSION['last_spider_name']))) // user following a search engine listing in,
session_regenerate_id();

if(!empty($SEARCH_SPIDER)) {
	$spidertime = time();
	$spiderdate = date("d.m.Y", $spidertime);
	// Do we need to log this spider access?
	$outstr = preg_replace('/\s+/', ' ', $SEARCH_SPIDER); 	// convert tabs etc. to blanks; trim extra blanks
	$outstr = str_replace(' - ', ' ', $outstr);				// Don't allow ' - ' because that is the log separator
	$logSpider = true;
	foreach ($known_spiders as $spider) {
		if (strpos($outstr, $spider) !== false) {
			$logSpider = false;
			break;
		}
	}
	if(isset($_SESSION['spider_count']))
	$spidercount = $_SESSION['spider_count'] + 1;
	else {
		$spidercount = 1;
		if ($logSpider) {
			//adds a message to the log that a new spider session is starting
			require_once("includes/authentication.php");      // -- Loaded early so AddToLog works
			AddToLog("New search engine encountered: ->".$outstr."<-");
			AddToLog("UA>{$ua}<");
			AddToLog("URI>{$_SERVER["REQUEST_URI"]}<");
		}
	}
	if(isset($_SESSION['last_spider_date'])) {
		if($spiderdate != $_SESSION['last_spider_date']) {
			//adds a message to the log that a new spider session is starting
			if ($logSpider) {
				require_once("includes/authentication.php");      // -- Loaded early so AddToLog works
				AddToLog("Returning search engine last seen ".$_SESSION['spider_count']." times on ".$_SESSION['last_spider_date']." from ".$_SESSION['last_spider_ip']." ->".$outstr."<-");
			}
			$_SESSION['last_spider_date'] = $spiderdate;
			$spidercount = 1;
		}
	}
	$_SESSION['last_spider_date'] = $spiderdate;
	$_SESSION['spider_count'] = $spidercount;
	if(isset($_SERVER['REMOTE_ADDR']))
	$_SESSION['last_spider_ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['last_spider_name'] = $SEARCH_SPIDER;
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
	$_SESSION['last_spider_lang'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

	$_SESSION['pgv_user'] = "";	// Don't allow search engine into user/admin mode.
	$_SESSION['CLANGUAGE'] = "";	// Force language to gedcom default language.
}

?>

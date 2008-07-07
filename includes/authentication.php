<?php
/**
 * MySQL User and Authentication functions
 *
 * This file contains the MySQL specific functions for working with users and authenticating them.
 * It also handles the internal mail messages, favorites, news/journal, and storage of MyGedView
 * customizations.  Assumes that a database connection has already been established.
 *
 * You can extend PhpGedView to work with other systems by implementing the functions in this file.
 * Other possible options are to use LDAP for authentication.
 *
 * $Id: authentication.php,v 1.19 2008/07/07 17:30:13 lsces Exp $
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008 John Finlay and Others.  All rights reserved.
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
 * @package PhpGedView
 * @subpackage DB
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}


/**
 * get the current username
 *
 * gets the username for the currently active user
 * 1. first checks the session
 * 2. then checks the remember cookie
 * @return string 	the username of the user or an empty string if the user is not logged in
 */

// Currently, a User ID is the same as a User Name.  In the future, User IDs will
// be numeric.  This function is part of the migration process.
function getUserName() {
	return get_user_name(getUserId());
}

function getUserId() {
	global $ALLOW_REMEMBER_ME, $DBCONN, $logout, $SERVER_URL;
	//-- this section checks if the session exists and uses it to get the username
	if (isset($_SESSION) && !empty($_SESSION['pgv_user'])) {
		return $_SESSION['pgv_user'];
	} else {
		if (isset($HTTP_SESSION_VARS) && !empty($HTTP_SESSION_VARS['pgv_user'])) {
			return $HTTP_SESSION_VARS['pgv_user'];
		} else {
			if ($ALLOW_REMEMBER_ME) {
				$tSERVER_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $SERVER_URL);
				if (empty($tSERVER_URL))
					$tSERVER_URL = $SERVER_URL; 	// cannot assume we had a match.
				if ((isset($_SERVER['HTTP_REFERER'])) && !empty($tSERVER_URL) && (stristr($_SERVER['HTTP_REFERER'],$tSERVER_URL)!==false))
					$referrer_found=true;
				if (!empty($_COOKIE["pgv_rem"])&& (empty($referrer_found)) && empty($logout)) {
					if (!is_object($DBCONN)) {
						return $_COOKIE["pgv_rem"];
					} else {	
						$session_time=get_user_setting($_COOKIE['pgv_rem'], 'sessiontime');
						if (is_null($session_time))
							$session_time=0;
						if (time() - $session_time < 60*60*24*7) {
							$_SESSION['pgv_user'] = $_COOKIE['pgv_rem'];
							$_SESSION['cookie_login'] = true;
							return $_COOKIE['pgv_rem'];
						} else {
							return "";
						}
					}
				} else {
					return "";
				}
			} else {
				return "";
			}
		}
	}
}

/**
 * check if given username is an admin
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files
 */
function userIsAdmin($user_id=null) {
	if (isset($_SESSION['cookie_login']) && $_SESSION['cookie_login']==true)
		return false;

	if (is_null($user_id))
		$user_id=getUserId();
	
	return get_user_setting($user_id, 'canadmin')=='Y';
	}

/**
 * check if given username is an admin for the current gedcom
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files for the currently active gedcom
 */
function userGedcomAdmin($username, $ged="") {
	global $GEDCOM, $gBitUser;

	if (empty($ged)) $ged = $GEDCOM;
vd($gBitUser);
	if ($gBitUser->isAdmin()) return true;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if (isset($user["canedit"][$ged])) {
		if ($user["canedit"][$ged]=="admin") return true;
		else return false;
	}
	else return false;
}

/**
 * check if the given user has access privileges on this gedcom
 *
 * takes a username and checks if the user has access privileges to view the private
 * gedcom data.
 * @param string $username the username of the user to check
 * @return boolean true if user can access false if they cannot
 */
function userCanAccess() {
	global $GEDCOM;
	static $cache=null;

	if (is_null($cache)) {
		$user_id=getUserId();
		$cache=get_user_setting($user_id, 'canadmin')=='Y' || get_user_gedcom_setting($user_id, $GEDCOM, 'canedit')!='none';
	}
	return $cache;
}

/**
 * check if the given user has write privileges on this gedcom
 *
 * takes a username and checks if the user has write privileges to change
 * the gedcom data. First check if the administrator has turned on editing privileges for this gedcom
 * @param string $username the username of the user to check
 * @return boolean true if user can edit false if they cannot
 */
function userCanEdit($username) {
	global $ALLOW_EDIT_GEDCOM, $GEDCOM, $gBitUser;

	if (!$ALLOW_EDIT_GEDCOM) return false;
	if ($gBitUser->isAdmin()) return true;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if ($user["canadmin"]) return true;
	if (isset($user["canedit"][$GEDCOM])) {
		if ($user["canedit"][$GEDCOM]=="yes" || $user["canedit"][$GEDCOM]=="edit" || $user["canedit"][$GEDCOM]=="admin"|| $user["canedit"][$GEDCOM]=="accept" || $user["canedit"][$GEDCOM]===true) return true;
		else return false;
	}
	else return false;
}

/**
 * Can user accept changes
 *
 * takes a username and checks if the user has write privileges to
 * change the gedcom data and accept changes
 * @param string $username	the username of the user check privileges
 * @return boolean true if user can accept false if user cannot accept
 */
function userCanAccept($user_id=null) {
	global $ALLOW_EDIT_GEDCOM, $GEDCOM;

	if (isset($_SESSION['cookie_login']) && ($_SESSION['cookie_login']==true))
		return false;

	if (is_null($user_id))
		$user_id=getUserId();

	if (get_user_setting($user_id, 'canadmin')=='Y')
		return true;

	if (!$ALLOW_EDIT_GEDCOM)
		return false;
	
	$tmp=get_user_gedcom_setting($user_id, $GEDCOM, 'canedit');
	return $tmp=='admin' || $tmp=='accept';
}

/**
 * Should user's changed automatically be accepted
 * @param string $username	the user name of the user to check
 * @return boolean 		true if the changes should automatically be accepted
 */
function userAutoAccept() {
	return get_user_setting(getUserId(), 'auto_accept')=='Y';
}

/**
 * does an admin user exits
 *
 * Checks to see if an admin user has been created
 * @return boolean true if an admin user has been defined
 */
function adminUserExists() {
	static $PGV_ADMIN_EXISTS=null;

	if (!is_null($PGV_ADMIN_EXISTS))
		return $PGV_ADMIN_EXISTS;

	if (checkTableExists()) {
		$PGV_ADMIN_EXISTS=admin_user_exists();
		return $PGV_ADMIN_EXISTS;
	}
	return false;
}

// Get the full name for a user
function getUserFullName($user) {
	global $NAME_REVERSE;

	$first_name=get_user_setting($user, 'firstname');
	$last_name =get_user_setting($user, 'lastname' );

	if ($NAME_REVERSE) {
		return $last_name.' '.$first_name;
	} else {
		return $first_name.' '.$last_name;
	}
}

/**
 * add a message into the log-file
 * @param string $LogString		the message to add
 * @param boolean $savelangerror
 * @return string returns the log line if successfully inserted into the log (used for CVS/SVN commit messages)
 */
function AddToLog($LogString, $savelangerror=false) {
	global $INDEX_DIRECTORY, $LOGFILE_CREATE, $argc;

	$wroteLogString = false;

	if ($LOGFILE_CREATE=="none")
		return;

	if (isset($_SERVER['REMOTE_ADDR']))
		$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	else
		if ($argc>1)
			$REMOTE_ADDR = "cli";
	if ($LOGFILE_CREATE !== "none" && $savelangerror === false) {
		if (empty($LOGFILE_CREATE))
			$LOGFILE_CREATE="daily";
		if ($LOGFILE_CREATE=="daily")
			$logfile = $INDEX_DIRECTORY."pgv-" . date("Ymd") . ".log";
		if ($LOGFILE_CREATE=="weekly")
			$logfile = $INDEX_DIRECTORY."pgv-" . date("Ym") . "-week" . date("W") . ".log";
		if ($LOGFILE_CREATE=="monthly")
			$logfile = $INDEX_DIRECTORY."pgv-" . date("Ym") . ".log";
		if ($LOGFILE_CREATE=="yearly")
			$logfile = $INDEX_DIRECTORY."pgv-" . date("Y") . ".log";
		if (is_writable($INDEX_DIRECTORY)) {
			$logline=
				date("d.m.Y H:i:s")." - ".
				$REMOTE_ADDR." - ".
				(getUserId() ? getUserName() : 'Anonymous')." - ".
				$LogString."\r\n";
			$fp = fopen($logfile, "a");
			flock($fp, 2);
			fputs($fp, $logline);
			flock($fp, 3);
			fclose($fp);
			$wroteLogString = true;
		}
	}
	if ($wroteLogString)
		return $logline;
	else
		return "";
}

//----------------------------------- AddToSearchLog
//-- requires a string to add into the searchlog-file
function AddToSearchLog($LogString, $allgeds) {
	global $INDEX_DIRECTORY, $SEARCHLOG_CREATE, $GEDCOM, $username;

	if (!isset($allgeds))
		return;
	if (count($allgeds) == 0)
		return;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	$oldged = $GEDCOM;
	foreach($allgeds as $indexval => $value) {
		$GEDCOM = $value;
		include(get_config_file());
		if ($SEARCHLOG_CREATE != "none") {
			$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
			if (empty($SEARCHLOG_CREATE))
				$SEARCHLOG_CREATE="daily";
			if ($SEARCHLOG_CREATE=="daily")
				$logfile = $INDEX_DIRECTORY."srch-" . $GEDCOM . date("Ymd") . ".log";
			if ($SEARCHLOG_CREATE=="weekly")
				$logfile = $INDEX_DIRECTORY."srch-" . $GEDCOM . date("Ym") . "-week" . date("W") . ".log";
			if ($SEARCHLOG_CREATE=="monthly")
				$logfile = $INDEX_DIRECTORY."srch-" . $GEDCOM . date("Ym") . ".log";
			if ($SEARCHLOG_CREATE=="yearly")
				$logfile = $INDEX_DIRECTORY."srch-" . $GEDCOM . date("Y") . ".log";
			if (is_writable($INDEX_DIRECTORY)) {
				$logline = "Date / Time: ".date("d.m.Y H:i:s") . " - IP: " . $REMOTE_ADDR . " - User: " .  PGV_USER_NAME . "<br />";
				if (count($allgeds) == count(get_all_gedcoms()))
					$logline .= "Searchtype: Global<br />";
				else
					$logline .= "Searchtype: Gedcom<br />";
				$logline .= $LogString . "<br /><br />\r\n";
				$fp = fopen($logfile, "a");
				flock($fp, 2);
				fputs($fp, $logline);
				flock($fp, 3);
				fclose($fp);
			}
		}
	}
	$GEDCOM = $oldged;
	include(get_config_file());
}

//----------------------------------- AddToChangeLog
//-- requires a string to add into the changelog-file
function AddToChangeLog($LogString, $ged="") {
	global $INDEX_DIRECTORY, $CHANGELOG_CREATE, $GEDCOM, $username, $SEARCHLOG_CREATE;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	if (empty($ged))
		$ged = $GEDCOM;
	$oldged = $GEDCOM;
	$GEDCOM = $ged;
	if ($ged!=$oldged)
		include(get_config_file());
	if ($CHANGELOG_CREATE != "none") {
		$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		if (empty($CHANGELOG_CREATE))
			$CHANGELOG_CREATE="daily";
		if ($CHANGELOG_CREATE=="daily")
			$logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Ymd") . ".log";
		if ($CHANGELOG_CREATE=="weekly")
			$logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Ym") . "-week" . date("W") . ".log";
		if ($CHANGELOG_CREATE=="monthly")
			$logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Ym") . ".log";
		if ($CHANGELOG_CREATE=="yearly")
			$logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Y") . ".log";
		if (is_writable($INDEX_DIRECTORY)) {
			$logline = date("d.m.Y H:i:s") . " - " . $REMOTE_ADDR . " - " . $LogString . "\r\n";
			$fp = fopen($logfile, "a");
			flock($fp, 2);
			fputs($fp, $logline);
			flock($fp, 3);
			fclose($fp);
		}

	}
	$GEDCOM = $oldged;
	if ($ged!=$oldged)
		include(get_config_file());
}

/**
 * stores a new favorite in the database
 * @param array $favorite	the favorite array of the favorite to add
 */
function addFavorite($favorite) {
	global $TBLPREFIX, $DBCONN;

	// -- make sure a favorite is added
	if (empty($favorite["gid"]) && empty($favorite["url"]))
		return false;

	//-- make sure this is not a duplicate entry
	$sql = "SELECT * FROM ".$TBLPREFIX."favorites WHERE ";
	if (!empty($favorite["gid"]))
		$sql .= "fv_gid='".$DBCONN->escapeSimple($favorite["gid"])."' ";
	if (!empty($favorite["url"]))
		$sql .= "fv_url='".$DBCONN->escapeSimple($favorite["url"])."' ";
	$sql .= "AND fv_file='".$DBCONN->escapeSimple($favorite["file"])."' AND fv_username='".$DBCONN->escapeSimple($favorite["username"])."'";
	$res =& dbquery($sql);
	if ($res->numRows()>0)
		return false;

	//-- get the next favorite id number for the primary key
	$newid = get_next_id("favorites", "fv_id");

	//-- add the favorite to the database
	$sql = "INSERT INTO ".$TBLPREFIX."favorites VALUES ($newid, '".$DBCONN->escapeSimple($favorite["username"])."'," .
			"'".$DBCONN->escapeSimple($favorite["gid"])."','".$DBCONN->escapeSimple($favorite["type"])."'," .
			"'".$DBCONN->escapeSimple($favorite["file"])."'," .
			"'".$DBCONN->escapeSimple($favorite["url"])."'," .
			"'".$DBCONN->escapeSimple($favorite["title"])."'," .
			"'".$DBCONN->escapeSimple($favorite["note"])."')";
	$res = dbquery($sql);

	if ($res)
		return true;
	else
		return false;
}

/**
 * deleteFavorite
 * deletes a favorite in the database
 * @param int $fv_id	the id of the favorite to delete
 */
function deleteFavorite($fv_id) {
	global $TBLPREFIX;

	$sql = "DELETE FROM ".$TBLPREFIX."favorites WHERE fv_id=".$fv_id;
	$res = dbquery($sql);

	if ($res)
		return true;
	else
		return false;
}

/**
 * Get a user's favorites
 * Return an array of a users messages
 * @param string $username		the username to get the favorites for
 */
function getUserFavorites($username) {
	global $gGedcom, $CONFIGURED;

	$favorites = array();
	//-- make sure we don't try to look up favorites for unconfigured sites
	return $favorites;
}

?>

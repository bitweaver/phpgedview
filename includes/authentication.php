<?php
/**
 * MySQL User and Authentication functions
 *
 * This file contains the MySQL specific functions for working with users and authenticating them.
 * It also handles the internal mail messages, favorites, journal, and storage of MyGedView
 * customizations.  Assumes that a database connection has already been established.
 *
 * You can extend PhpGedView to work with other systems by implementing the functions in this file.
 * Other possible options are to use LDAP for authentication.
 *
 * $Id: authentication.php,v 1.16 2007/05/28 08:25:52 lsces Exp $
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003	John Finlay and Others
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
if (strstr($_SERVER["SCRIPT_NAME"],"authentication")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}

/**
 * return a sorted array of user
 *
 * returns a sorted array of the users in the system
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#users
 * @param string $field the field in the user array to sort on
 * @param string $order asc or dec
 * @return array returns a sorted array of users
 */
function getUsers($field = "username", $order = "asc", $sort2 = "firstname") {
	global $usersortfields, $gBitUser;

	$listHash['last_get'] = 3600;
	$online_users = $gBitUser->getUserActivity( $listHash );

	$users = array();
	foreach( $online_users as $user_row ) {
		$user = array();
		$user["username"]=$user_row["login"];
		$user["firstname"]=$user_row["user_id"];
		$user["lastname"]=$user_row["real_name"];
		$user["gedcomid"]="";
		$user["rootid"]=1;
		$user["auto_accept"] = false;
		$users[$user_row["login"]] = $user;
	}
	return $users;
}

/**
 * get the current username
 *
 * gets the username for the currently active user
 * 1. first checks the session
 * 2. then checks the remember cookie
 * @return string 	the username of the user or an empty string if the user is not logged in
 */
function getUserName() {
	global $gBitUser;
	$user = getUser( $gBitUser );
	if ($user) {
		return $gBitUser->mUsername;
	}
	return "";
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
function userCanAccess($username) {
	global $GEDCOM, $gBitUser;

	if ($gBitUser->isAdmin()) return true;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if (isset($user["canedit"][$GEDCOM])) {
		if ($user["canedit"][$GEDCOM]!="none" || $user["canadmin"]) return true;
		else return false;
	}
	else return false;
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
function userCanAccept($username) {
	global $ALLOW_EDIT_GEDCOM, $GEDCOM, $gBitUser;

	if ($gBitUser->isAdmin()) return true;
	if (!$ALLOW_EDIT_GEDCOM) return false;
	if (empty($username)) return false;
	$user = getUser($username);
	if (!$user) return false;
	if (isset($user["canedit"][$GEDCOM])) {
		if ($user["canedit"][$GEDCOM]=="accept") return true;
		if ($user["canedit"][$GEDCOM]=="admin") return true;
		else return false;
	}
	else return false;
}

/**
 * Should user's changed automatically be accepted
 * @param string $username	the user name of the user to check
 * @return boolean 		true if the changes should automatically be accepted
 */
function userAutoAccept($username = "") {
	if (empty($username)) $username = getUserName();
	if (empty($username)) return false;

	if (!userCanAccept($username)) return false;
	$user = getUser($username);
//	if ($user["auto_accept"]) return true;
	return $user["auto_accept"];
}

/**
 * get a user array
 *
 * finds a user from the given username and returns a user array of the form
 * defined at {@link http://www.phpgedview.net/devdocs/arrays.php#user}
 * @param string $username the username of the user to return
 * @return array the user array to return
 */
function getUser($username) {
	global $gBitUser;

	$user = array();
//vd($gBitUser);
	if ( $gBitUser->isValid() )
		$user["username"]=$gBitUser->mInfo["login"];
	$user["firstname"]=$gBitUser->mUserId;
	$user["lastname"]=$gBitUser->mInfo["real_name"];
	$user["gedcomid"]=""; // unserialize($user_row["u_gedcomid"]);
	$user["rootid"]=1;
	if ( $gBitUser->isAdmin() ) {
		$user["canadmin"]=true;
		$user["sync_gedcom"] = 'Y';
	} else {
		$user["canadmin"]=false;
		$user["sync_gedcom"] = 'N';
	}
	$user["editaccount"]=false;
	$user["anon"]=$gBitUser->isValid();
	$user["email"] = $gBitUser->mInfo["email"];
	$user["default_tab"] = 0;
/*	$user["canedit"]=unserialize($user_row["u_canedit"]);
	-- convert old <3.1 access levels to the new 3.2 access levels
	foreach($user["canedit"] as $key=>$value) {
		if ($value=="no") $user["canedit"][$key] = "access";
		if ($value=="yes") $user["canedit"][$key] = "edit";
	}
	foreach($GEDCOMS as $ged=>$gedarray) {
		if (!isset($user["canedit"][$ged])) $user["canedit"][$ged] = "access";
	}
	$user["verified"] = $user_row["u_verified"];
	$user["verified_by_admin"] = $user_row["u_verified_by_admin"];
	$user["language"] = $user_row["u_language"];
	$user["pwrequested"] = $user_row["u_pwrequested"];
	$user["reg_timestamp"] = $user_row["u_reg_timestamp"];
	$user["reg_hashcode"] = $user_row["u_reg_hashcode"];
	$user["theme"] = $user_row["u_theme"];
	$user["loggedin"] = $user_row["u_loggedin"];
	$user["sessiontime"] = $user_row["u_sessiontime"];
	$user["contactmethod"] = $user_row["u_contactmethod"];
	if ($user_row["u_visibleonline"]!='N') $user["visibleonline"]=true;
		else $user["visibleonline"]=false;
	if ($user_row["u_editaccount"]!='N' || $user["canadmin"]) $user["editaccount"]=true;
		else $user["editaccount"]=false;
	$user["comment"] = $user_row["u_comment"];
	$user["comment_exp"] = $user_row["u_comment_exp"];
	$user["sync_gedcom"] = $user_row["u_sync_gedcom"];
	$user["relationship_privacy"] = $user_row["u_relationship_privacy"];
	$user["max_relation_path"] = $user_row["u_max_relation_path"];
*/
	$user["auto_accept"]=false;

	if (isset($user)) return $user;

	return false;
}

/**
 * get a user from a gedcom id
 *
 * finds a user from their gedcom id
 * @param string $id	the gedcom id to to search on
 * @param string $gedcom	the gedcom filename to match
 * @return array 	returns a user array
 */
function getUserByGedcomId($id, $gedcom) {
	global $users, $REGEXP_DB;

	if (empty($id) || empty($gedcom)) return false;

	$user = false;
	// to be replaced by bitweaver permissions entries
	return false;
}

/**
 * add a message into the log-file
 * @param string $LogString		the message to add
 * @param boolean $savelangerror
 * @return string returns the log line if successfully inserted into the log
 */
function AddToLog($LogString, $savelangerror=false) {
	global $INDEX_DIRECTORY, $LOGFILE_CREATE;

	$wroteLogString = false;

	if ($LOGFILE_CREATE=="none") return;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	if ($LOGFILE_CREATE !== "none" && $savelangerror === false) {
		if (empty($LOGFILE_CREATE)) $LOGFILE_CREATE="daily";
		if ($LOGFILE_CREATE=="daily") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ymd") . ".log";
		if ($LOGFILE_CREATE=="weekly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ym") . "-week" . date("W") . ".log";
		if ($LOGFILE_CREATE=="monthly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Ym") . ".log";
		if ($LOGFILE_CREATE=="yearly") $logfile = $INDEX_DIRECTORY."/pgv-" . date("Y") . ".log";
		if (is_writable($INDEX_DIRECTORY)) {
			$logline = date("d.m.Y H:i:s") . " - " . $REMOTE_ADDR . " - " . $LogString . "\r\n";
			$fp = fopen($logfile, "a");
			flock($fp, 2);
			fputs($fp, $logline);
			flock($fp, 3);
			fclose($fp);
			$wroteLogString = true;
		}
	}
	if ($wroteLogString) return $logline;
	else return "";
}

//----------------------------------- AddToSearchLog
//-- requires a string to add into the searchlog-file
function AddToSearchLog($LogString, $allgeds) {
	global $INDEX_DIRECTORY, $SEARCHLOG_CREATE, $GEDCOM, $GEDCOMS, $username;

	if (!isset($allgeds)) return;
	if (count($allgeds) == 0) return;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	$oldged = $GEDCOM;
	foreach($allgeds as $indexval => $value) {
		$GEDCOM = $value;
		include(get_config_file());
		if ($SEARCHLOG_CREATE != "none") {
			$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
			if (empty($SEARCHLOG_CREATE)) $SEARCHLOG_CREATE="daily";
			if ($SEARCHLOG_CREATE=="daily") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Ymd") . ".log";
			if ($SEARCHLOG_CREATE=="weekly") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Ym") . "-week" . date("W") . ".log";
			if ($SEARCHLOG_CREATE=="monthly") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Ym") . ".log";
			if ($SEARCHLOG_CREATE=="yearly") $logfile = $INDEX_DIRECTORY."/srch-" . $GEDCOM . date("Y") . ".log";
			if (is_writable($INDEX_DIRECTORY)) {
				$logline = "Date / Time: ".date("d.m.Y H:i:s") . " - IP: " . $REMOTE_ADDR . " - User: " .  getUserName() . "<br />";
				if (count($allgeds) == count($GEDCOMS)) $logline .= "Searchtype: Global<br />"; else $logline .= "Searchtype: Gedcom<br />";
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
	global $INDEX_DIRECTORY, $CHANGELOG_CREATE, $GEDCOM, $GEDCOMS, $username, $SEARCHLOG_CREATE;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	if (empty($ged)) $ged = $GEDCOM;
	$oldged = $GEDCOM;
	$GEDCOM = $ged;
	include(get_config_file());
	if ($CHANGELOG_CREATE != "none") {
		$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		if (empty($CHANGELOG_CREATE)) $CHANGELOG_CREATE="daily";
		if ($CHANGELOG_CREATE=="daily") $logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Ymd") . ".log";
		if ($CHANGELOG_CREATE=="weekly") $logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Ym") . "-week" . date("W") . ".log";
		if ($CHANGELOG_CREATE=="monthly") $logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Ym") . ".log";
		if ($CHANGELOG_CREATE=="yearly") $logfile = $INDEX_DIRECTORY."/ged-" . $GEDCOM . date("Y") . ".log";
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
	include(get_config_file());
}

/**
 * stores a new favorite in the database
 * @param array $favorite	the favorite array of the favorite to add
 */
function addFavorite($favorite) {

	// -- make sure a favorite is added
	if (empty($favorite["gid"]) && empty($favorite["url"])) return false;

	//-- make sure this is not a duplicate entry
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."favorites WHERE ";
	if (!empty($favorite["gid"])) $sql .= "fv_gid='".$favorite["gid"]."' ";
	if (!empty($favorite["url"])) $sql .= "fv_url='".$favorite["url"]."' ";
	$sql .= "AND fv_file='".$favorite["file"]."' AND fv_username='".$favorite["username"]."'";
	$res =& $gGedcom->mDb->query($sql);
	if ($res->numRows()>0) return false;

	//-- get the next favorite id number for the primary key
	$newid = get_next_id("favorites", "fv_id");

	//-- add the favorite to the database
	$sql = "INSERT INTO ".PHPGEDVIEW_DB_PREFIX."favorites VALUES ($newid, ?, ?, ?, ?, ?, ?, ?)";
	$res = $gGedcom->mDb->query($sql,array($favorite["username"],$favorite["gid"],$favorite["type"],$favorite["file"],$favorite["url"],$favorite["title"],$favorite["note"]));

	if ($res) return true;
	else return false;
}

/**
 * deleteFavorite
 * deletes a favorite in the database
 * @param int $fv_id	the id of the favorite to delete
 */
function deleteFavorite($fv_id) {

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."favorites WHERE fv_id=".$fv_id;
	$res = $gGedcom->mDb->query($sql);

	if ($res) return true;
	else return false;
}

/**
 * Get a user's favorites
 * Return an array of a users messages
 * @param string $username		the username to get the favorites for
 */
function getUserFavorites($username) {
	global $GEDCOMS, $gGedcom, $CONFIGURED;

	$favorites = array();
	//-- make sure we don't try to look up favorites for unconfigured sites
	return $favorites;
}

?>
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
 * $Id: authentication.php,v 1.9 2006/10/28 20:17:03 lsces Exp $
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
 * authenticate a username and password
 *
 * This function takes the given <var>$username</var> and <var>$password</var> and authenticates
 * them against the database.  The passwords are encrypted using the crypt() function.
 * The username is stored in the <var>$_SESSION["pgv_user"]</var> session variable.
 * @param string $username the username for the user attempting to login
 * @param string $password the plain text password to test
 * @param boolean $basic true if the userName and password were retrived via Basic HTTP authentication. Defaults to false. At this point, this is only used for logging
 * @return bool return true if the username and password credentials match a user in the database return false if they don't
 */
function authenticateUser($username, $password, $basic=false) {
	global $GEDCOM, $pgv_lang;
	checkTableExists();
	$user = getUser($username);
	//-- make sure that we have the actual username as it was stored in the DB
	$username = $user['username'];
	if ($user!==false) {
		if (crypt($password, $user["password"])==$user["password"]) {
	        if (!isset($user["verified"])) $user["verified"] = "";
	        if (!isset($user["verified_by_admin"])) $user["verified_by_admin"] = "";
	        if ((($user["verified"] == "yes") and ($user["verified_by_admin"] == "yes")) or ($user["canadmin"] != "")){
		        $sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."users SET u_loggedin='Y', u_sessiontime='".time()."' WHERE u_username='$username'";
		        $res = $gGedcom->mDb->query($sql);

				AddToLog(($basic ? "Basic HTTP Authentication" :"Login"). " Successful ->" . $username ."<-");
				//-- reset the user's session
				$_SESSION = array();
				$_SESSION['pgv_user'] = $username;
				//-- unset the cookie_login session var to show that they have logged in with their password
				$_SESSION['cookie_login'] = false;
				if (isset($pgv_lang[$user["language"]])) $_SESSION['CLANGUAGE'] = $user['language'];
				//-- only change the gedcom if the user does not have an gedcom id
				//-- for the currently active gedcom
				if (empty($user["gedcomid"][$GEDCOM])) {
					//-- if the user is not in the currently active gedcom then switch them
					//-- to the first gedcom for which they have an ID
					foreach($user["gedcomid"] as $ged=>$id) {
						if (!empty($id)) {
							$_SESSION['GEDCOM']=$ged;
							break;
						}
					}
				}
				return true;
			}
		}
	}
	AddToLog(($basic ? "Basic HTTP Authentication" : "Login") . " Failed ->" . $username ."<-");
	return false;
}

/**
 * logs a user out of the system
 * @param string $username	optional parameter to logout a specific user
 */
function userLogout($username = "") {
	global $GEDCOM, $LANGUAGE;

	if ($username=="") {
		if (isset($_SESSION["pgv_user"])) $username = $_SESSION["pgv_user"];
		else if (isset($_COOKIE["pgv_rem"])) $username = $_COOKIE["pgv_rem"];
		else return;
	}
	$sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."users SET u_loggedin='N' WHERE u_username='".$username."'";
	$res = $gGedcom->mDb->query($sql);

	AddToLog("Logout - " . $username);

	if ((isset($_SESSION['pgv_user']) && ($_SESSION['pgv_user']==$username)) || (isset($_COOKIE['pgv_rem'])&&$_COOKIE['pgv_rem']==$username)) {
		if ($_SESSION['pgv_user']==$username) {
			$_SESSION['pgv_user'] = "";
			unset($_SESSION['pgv_user']);
			if (isset($_SESSION["pgv_counter"])) $tmphits = $_SESSION["pgv_counter"];
			else $tmphits = -1;
			@session_destroy();
			$_SESSION["gedcom"]=$GEDCOM;
			$_SESSION["show_context_help"]="yes";
			@setcookie("pgv_rem", "", -1000);
			if($tmphits>=0) $_SESSION["pgv_counter"]=$tmphits; //set since it was set before so don't get double hits
		}
	}
}

/**
 * Updates the login time in the database of the given user
 * The login time is used to automatically logout users who have been
 * inactive for the defined session time
 * @param string $username	the username to update the login info for
 */
function userUpdateLogin($username) {
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
/*		$user["password"]=$user_row["u_password"];
		if ($user_row["u_canadmin"]=='Y') $user["canadmin"]=true;
		else $user["canadmin"]=false;
		$user["canedit"]=unserialize($user_row["u_canedit"]);
		-- convert old <3.1 access levels to the new 3.2 access levels
		foreach($user["canedit"] as $key=>$value) {
			if ($value=="no") $user["canedit"][$key] = "access";
			if ($value=="yes") $user["canedit"][$key] = "edit";
		}
		$user["email"] = $user_row["u_email"];
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
		if ($user_row["u_visibleonline"]!='N') $user["visibleonline"] = true;
		else $user["visibleonline"] = false;
		if ($user_row["u_editaccount"]!='N') $user["editaccount"] = true;
		else $user["editaccount"] = false;
		$user["default_tab"] = $user_row["u_defaulttab"];
		$user["comment"] = $user_row["u_comment"];
		$user["comment_exp"] = $user_row["u_comment_exp"];
		$user["sync_gedcom"] = $user_row["u_sync_gedcom"];
		$user["relationship_privacy"] = $user_row["u_relationship_privacy"];
		$user["max_relation_path"] = $user_row["u_max_relation_path"];
*/
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
 * check if given username is an admin
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files
 */
function userIsAdmin($username) {
	global $gBitUser;
	return $gBitUser->IsAdmin();
}

/**
 * check if given username is an admin for the current gedcom
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files for the currently active gedcom
 */
function userGedcomAdmin($username, $ged="") {
	global $GEDCOM;

	if (empty($ged)) $ged = $GEDCOM;

	if ($_SESSION['cookie_login']) return false;
	if (userIsAdmin($username)) return true;
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
	global $GEDCOM;

	if (userIsAdmin($username)) return true;
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
	global $ALLOW_EDIT_GEDCOM, $GEDCOM;

	if (!$ALLOW_EDIT_GEDCOM) return false;
	if (userIsAdmin($username)) return true;
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
	global $ALLOW_EDIT_GEDCOM, $GEDCOM;

	if ($_SESSION['cookie_login']) return false;
	if (userIsAdmin($username)) return true;
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
 * does an admin user exits
 *
 * Checks to see if an admin user has been created
 * @return boolean true if an admin user has been defined
 */
function adminUserExists() {
}

/**
 * check if the user database tables exist
 *
 * If the tables don't exist then create them
 * If the tables do exist check if they need to be upgraded
 * to the latest version of the database schema.
 */
function checkTableExists() {
	return true;
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
	if ( $gBitUser->isAdmin() ) $user["canadmin"]=true;
		else $user["canadmin"]=false;
	$user["editaccount"]=false;
	$user["anon"]=$gBitUser->isValid();
	$user["email"] = $gBitUser->mInfo["email"];
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
	$user["default_tab"] = $user_row["u_defaulttab"];
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
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."users WHERE ";
	$sql .= "u_gedcomid LIKE '%".$id."%'";
	$res = $gGedcom->mDb->query($sql, false);

	if (!$res) return false;
	if ($res->numRows()==0) return false;
	if ($res) {
		while($user_row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
			if ($user_row) {
				$gedcomid=unserialize($user_row["u_gedcomid"]);
				if (isset($gedcomid[$gedcom]) && ($gedcomid[$gedcom]==$id)) {
					$user = array();
					$user["username"]=$user_row["u_username"];
					$user["firstname"]=stripslashes($user_row["u_firstname"]);
					$user["lastname"]=stripslashes($user_row["u_lastname"]);
					$user["gedcomid"]=$gedcomid;
					$user["rootid"]=unserialize($user_row["u_rootid"]);
					$user["password"]=$user_row["u_password"];
					if ($user_row["u_canadmin"]=='Y') $user["canadmin"]=true;
					else $user["canadmin"]=false;
					$user["canedit"]=unserialize($user_row["u_canedit"]);
					//-- convert old <3.1 access levels to the new 3.2 access levels
					foreach($user["canedit"] as $key=>$value) {
						if ($value=="no") $user["canedit"][$key] = "access";
						if ($value=="yes") $user["canedit"][$key] = "edit";
					}
					$user["email"] = $user_row["u_email"];
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
					$user["default_tab"] = $user_row["u_defaulttab"];
					$user["comment"] = $user_row["u_comment"];
					$user["comment_exp"] = $user_row["u_comment_exp"];
					$user["sync_gedcom"] = $user_row["u_sync_gedcom"];
					$user["relationship_privacy"] = $user_row["u_relationship_privacy"];
					$user["max_relation_path"] = $user_row["u_max_relation_path"];
//					if ($user_row["u_auto_accept"]!='Y') $user["auto_accept"]=false;
//					else $user["auto_accept"]=true;
					if ($user_row["u_auto_accept"]!='N') $user["auto_accept"]=true;
					else $user["auto_accept"]=false;
					$users[$user_row["u_username"]] = $user;
				}
			}
		}
		$res->free();
		return $user;
	}
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
	global $DBCONN;

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
/*	if (!$CONFIGURED) return $favorites;
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."favorites WHERE fv_username=?";
	$result = $gGedcom->mDbx->query($sql, array($username));

	while( $row = $result->fetchRow()){
		if (isset($GEDCOMS[$row["fv_file"]])) {
			$favorite = array();
			$favorite["id"] = $row["fv_id"];
			$favorite["username"] = $row["fv_username"];
			$favorite["gid"] = $row["fv_gid"];
			$favorite["type"] = $row["fv_type"];
			$favorite["file"] = $row["fv_file"];
			$favorite["title"] = $row["fv_title"];
			$favorite["note"] = $row["fv_note"];
			$favorite["url"] = $row["fv_url"];
			$favorites[] = $favorite;
		}
	} */
	return $favorites;
}

?>
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
 * $Id: authentication.php,v 1.21 2009/04/30 18:32:43 lsces Exp $
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_AUTHENTICATION_PHP', '');

/**
 * authenticate a username and password
 *
 * This function takes the given <var>$username</var> and <var>$password</var> and authenticates
 * them against the database.  The passwords are encrypted using the crypt() function.
 * The username is stored in the <var>$_SESSION["pgv_user"]</var> session variable.
 * @param string $user_name the username for the user attempting to login
 * @param string $password the plain text password to test
 * @param boolean $basic true if the userName and password were retrived via Basic HTTP authentication. Defaults to false. At this point, this is only used for logging
 * @return the user_id if sucessful, false otherwise
 */
function authenticateUser($user_name, $password, $basic=false) {
//	checkTableExists();

	// If we were already logged in, log out first
	if (PGV_USER_ID) {
		userLogout(PGV_USER_ID);
	}

	if ($user_id=get_user_id($user_name)) {
		$dbpassword=get_user_password($user_id);
		if (crypt($password, $dbpassword)==$dbpassword) {
			if (get_user_setting($user_id, 'verified')=='yes' && get_user_setting($user_id, 'verified_by_admin')=='yes' || get_user_setting($user_id, 'canadmin')=='Y') {
				set_user_setting($user_id, 'loggedin', 'Y');
				//-- reset the user's session
				$_SESSION = array();
				$_SESSION['pgv_user'] = $user_id;
				// show that they have logged in with their password
				$_SESSION['cookie_login'] = false;
				AddToLog(($basic ? "Basic HTTP Authentication" :"Login"). " Successful");
				return $user_id;
			}
		}
	}
	AddToLog(($basic ? "Basic HTTP Authentication" : "Login") . " Failed ->" . $user_name ."<-");
	return false;
}

/**
 * authenticate a username and password using Basic HTTP Authentication
 *
 * This function uses authenticateUser(), for authentication, but retrives the userName and password provided via basic auth.
 * @return bool return true if the user is already logged in or the basic HTTP auth username and password credentials match a user in the database return false if they don't
 * @TODO Security audit for this functionality
 * @TODO Do we really need a return value here?
 * @TODO should we reauthenticate the user even if already logged in?
 * @TODO do we need to set the user language and other jobs done in login.php? Should that loading be moved to a function called from the authenticateUser function?
 */
function basicHTTPAuthenticateUser() {
	global $pgv_lang;
	$user_id = getUserId();
	if (empty($user_id)){ //not logged in.
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
				|| (! authenticateUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], true))) {
			header('WWW-Authenticate: Basic realm="' . $pgv_lang["basic_realm"] . '"');
			header('HTTP/1.0 401 Unauthorized');
			echo $pgv_lang["basic_auth_failure"] ;
			exit;
		}
	} else { //already logged in or successful basic authentication
		return true; //probably not needed
	}
}

/**
 * logs a user out of the system
 * @param string $user_id	logout a specific user
 */
function userLogout($user_id) {
	global $GEDCOM;

	if ($user_id) {
		set_user_setting($user_id, 'loggedin', 'N');

		AddToLog("Logout ".getUserName($user_id));

		if ((isset($_SESSION['pgv_user']) && ($_SESSION['pgv_user']==$user_id)) || (isset($_COOKIE['pgv_rem'])&&$_COOKIE['pgv_rem']==$user_id)) {
			if ($_SESSION['pgv_user']==$user_id) {
				$_SESSION['pgv_user'] = "";
				unset($_SESSION['pgv_user']);
				if (isset($_SESSION["pgv_counter"]))
					$tmphits = $_SESSION["pgv_counter"];
				else
					$tmphits = -1;
				@session_destroy();
				$_SESSION["gedcom"]=$GEDCOM;
				$_SESSION["show_context_help"]="yes";
				@setcookie("pgv_rem", "", -1000);
				if ($tmphits>=0)
					$_SESSION["pgv_counter"]=$tmphits; //set since it was set before so don't get double hits
			}
		}
	}
}

/**
 * Updates the login time in the database of the given user
 * The login time is used to automatically logout users who have been
 * inactive for the defined session time
 * @param string $username	the username to update the login info for
 */
function userUpdateLogin($user_id) {
	set_user_setting($user_id, 'sessiontime', time());
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
	} elseif ($ALLOW_REMEMBER_ME) {
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

/**
 * check if given username is an admin
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files
 */
function userIsAdmin($user_id=PGV_USER_ID) {
	if (isset($_SESSION['cookie_login']) && $_SESSION['cookie_login']==true)
		return false;

	return get_user_setting($user_id, 'canadmin')=='Y';
	}

/**
 * check if given username is an admin for the current gedcom
 *
 * takes a username and checks if the
 * user has administrative privileges
 * to change the configuration files for the currently active gedcom
 */
function userGedcomAdmin($user_id=PGV_USER_ID, $ged_id=PGV_GED_ID) {
	if (isset($_SESSION['cookie_login']) && ($_SESSION['cookie_login']==true))
		return false;

	return userIsAdmin($user_id, $ged_id) || get_user_gedcom_setting($user_id, $ged_id, 'canedit')=='admin';
}

/**
 * check if the given user has access privileges on this gedcom
 *
 * takes a username and checks if the user has access privileges to view the private
 * gedcom data.
 * @param string $user_id the id of the user to check
 * @param string $ged_id the id of the gedcom to check
 * @return boolean true if user can access false if they cannot
 */
function userCanAccess($user_id=PGV_USER_ID, $ged_id=PGV_GED_ID) {
	if (get_user_setting($user_id, 'canadmin')=='Y')
		return true;

	$tmp=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
	return $tmp=='admin' || $tmp=='accept' || $tmp=='edit' || $tmp=='access';
}

/**
 * check if the given user has write privileges on this gedcom
 *
 * takes a username and checks if the user has write privileges to change
 * the gedcom data. First check if the administrator has turned on editing privileges for this gedcom
 * @param string $username the username of the user to check
 * @return boolean true if user can edit false if they cannot
 */
function userCanEdit($user_id=PGV_USER_ID, $ged_id=PGV_GED_ID) {
	global $ALLOW_EDIT_GEDCOM;

	if (!$ALLOW_EDIT_GEDCOM)
		return false;

	if (get_user_setting($user_id, 'canadmin')=='Y')
		return true;

	$tmp=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
	return $tmp=='admin' || $tmp=='accept' || $tmp=='edit';
}

/**
 * Can user accept changes
 *
 * takes a username and checks if the user has write privileges to
 * change the gedcom data and accept changes
 * @param string $username	the username of the user check privileges
 * @return boolean true if user can accept false if user cannot accept
 */
function userCanAccept($user_id=PGV_USER_ID, $ged_id=PGV_GED_ID) {
	global $ALLOW_EDIT_GEDCOM;

	if (isset($_SESSION['cookie_login']) && ($_SESSION['cookie_login']==true))
		return false;

	// If we've disabled editing, an admin can still accept pending edits.
	if (get_user_setting($user_id, 'canadmin')=='Y')
		return true;

	if (!$ALLOW_EDIT_GEDCOM)
		return false;

	$tmp=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
	return $tmp=='admin' || $tmp=='accept';
}

/**
 * Should user's changed automatically be accepted
 * @param string $username	the user name of the user to check
 * @return boolean 		true if the changes should automatically be accepted
 */
function userAutoAccept($user_id=PGV_USER_ID) {
	return get_user_setting($user_id, 'auto_accept')=='Y';
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

//	if (checkTableExists()) {
		$PGV_ADMIN_EXISTS=admin_user_exists();
		return $PGV_ADMIN_EXISTS;
//	}
	return false;
}

/**
 * check if the user database tables exist
 *
 * If the tables don't exist then create them
 * If the tables do exist check if they need to be upgraded
 * to the latest version of the database schema.
 */
function checkTableExists() {
	global $TBLPREFIX, $DBCONN, $DBTYPE;

	$has_users = false;
	$has_gedcomid = false;
	$has_email = false;
	$has_messages = false;
	$has_favorites = false;
	$has_sessiontime = false;
	$has_blocks = false;
	$has_contactmethod = false;
	$has_news = false;
	$has_visible = false;
	$has_account = false;
	$has_defaulttab = false;
	$has_blockconfig = false;
	$has_comment = false;
	$has_comment_exp = false;
	$has_sync_gedcom = false;
	$has_first_name = false;
	$has_relation_privacy = false;
	$has_fav_note = false;
	$has_auto_accept = false;
	$has_mutex = false;

	$sqlite = ($DBTYPE == 'sqlite');

	if (DB::isError($DBCONN))
		return false;
	$data = $DBCONN->getListOf('tables');
	foreach($data as $indexval => $table) {
		if (empty($TBLPREFIX) || strpos($table, $TBLPREFIX) === 0) {
			switch(substr($table, strlen($TBLPREFIX))) {
				case "users":
					$has_users = true;
					$info = $DBCONN->tableInfo($TBLPREFIX."users");
					foreach($info as $indexval => $field) {
						switch ($field["name"]) {
							case "u_gedcomid":
								$has_gedcomid = true;
								break;
							case "u_email":
								$has_email = true;
								break;
							case "u_sessiontime":
								$has_sessiontime = true;
								break;
							case "u_contactmethod":
								$has_contactmethod = true;
								break;
							case "u_visibleonline":
								$has_visible = true;
								break;
							case "u_editaccount":
								$has_account = true;
								break;
							case "u_defaulttab":
								$has_defaulttab = true;
								break;
							case "u_comment":
								$has_comment = true;
								break;
							case "u_comment_exp":
								$has_comment_exp = true;
								break;
							case "u_sync_gedcom":
								$has_sync_gedcom = true;
								break;
							case "u_firstname":
								$has_first_name = true;
								break;
							case "u_relationship_privacy":
								$has_relation_privacy = true;
								break;
							case "u_auto_accept":
								$has_auto_accept = true;
								break;
						}
					}
					break;
				case "messages":
					$has_messages = true;
					break;
				case "favorites":
					$has_favorites = true;
					$info = $DBCONN->tableInfo($TBLPREFIX."favorites");
					foreach($info as $indexval => $field) {
						switch ($field["name"]) {
							case "fv_note":
								$has_fav_note = true;
								break;
						}
					}
					break;
				case "blocks":
					$has_blocks = true;
					$info = $DBCONN->tableInfo($TBLPREFIX."blocks");
					foreach($info as $indexval => $field) {
						switch ($field["name"]) {
							case "b_config":
								$has_blockconfig = true;
								break;
						}
					}
					break;
				case "news":
					$has_news = true;
					break;
				case "mutex":
					$has_mutex = true;
					break;
			}
		}
	}
	if (!$has_users || !$has_gedcomid ||$DBTYPE == "mssql" && !$has_first_name ||
			$sqlite && (!$has_email || !$has_sessiontime || !$has_contactmethod || !$has_visible || !$has_account || !$has_defaulttab || !$has_comment || !$has_sync_gedcom || !$has_first_name || !$has_relation_privacy || !$has_auto_accept)) {

		dbquery("DROP TABLE {$TBLPREFIX}users", false);
		dbquery(
			"CREATE TABLE {$TBLPREFIX}users (".
			" u_username             VARCHAR(30) NOT NULL,".
			" u_password             VARCHAR(255)    NULL,".
			" u_firstname            VARCHAR(255)    NULL,".
			" u_lastname             VARCHAR(255)    NULL,".
			" u_gedcomid             TEXT            NULL,".
			" u_rootid               TEXT            NULL,".
			" u_canadmin             VARCHAR(2)      NULL,".
			" u_canedit              TEXT            NULL,".
			" u_email                TEXT            NULL,".
			" u_verified             VARCHAR(20)     NULL,".
			" u_verified_by_admin    VARCHAR(20)     NULL,".
			" u_language             VARCHAR(50)     NULL,".
			" u_pwrequested          VARCHAR(20)     NULL,".
			" u_reg_timestamp        VARCHAR(50)     NULL,".
			" u_reg_hashcode         VARCHAR(255)    NULL,".
			" u_theme                VARCHAR(50)     NULL,".
			" u_loggedin             VARCHAR(2)      NULL,".
			" u_sessiontime          INT             NULL,".
			" u_contactmethod        VARCHAR(20)     NULL,".
			" u_visibleonline        VARCHAR(2)      NULL,".
			" u_editaccount          VARCHAR(2)      NULL,".
			" u_defaulttab           INT             NULL,".
			" u_comment              VARCHAR(255)    NULL,".
			" u_comment_exp          VARCHAR(20)     NULL,".
			" u_sync_gedcom          VARCHAR(2)      NULL,".
			" u_relationship_privacy VARCHAR(2)      NULL,".
			" u_max_relation_path    INT             NULL,".
			" u_auto_accept          VARCHAR(2)      NULL,".
			" PRIMARY KEY (u_username)".
			") ".PGV_DB_UTF8_TABLE
		);
	} else {
		if (!$has_email) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_email             TEXT         NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_verified          VARCHAR(20)  NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_verified_by_admin VARCHAR(20)  NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_language          VARCHAR(50)  NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_pwrequested       VARCHAR(20)  NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_reg_timestamp     VARCHAR(50)  NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_reg_hashcode      VARCHAR(255) NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_theme             VARCHAR(50)  NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_loggedin          VARCHAR(1)   NULL");
		}
		if (!$has_sessiontime) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_sessiontime INT NULL");
		}
		if (!$has_contactmethod) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_contactmethod VARCHAR(20) NULL");
		}
		if (!$has_visible) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_visibleonline VARCHAR(2) NULL");
		}
		if (!$has_account) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_editaccount VARCHAR(2) NULL");
		}
		if (!$has_defaulttab) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_defaulttab INT NULL");
		}
		if (!$has_comment) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_comment     VARCHAR(255) NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_comment_exp VARCHAR(20)  NULL");
		}
		if (!$has_sync_gedcom) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_sync_gedcom VARCHAR(2) NULL");
		}
		if (!$has_first_name) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_firstname VARCHAR(255) NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_lastname  VARCHAR(255) NULL");
			dbquery(
				"UPDATE {$TBLPREFIX}users SET".
				" u_lastname =SUBSTRING_INDEX(u_fullname, ' ', -1), ".
				" u_firstname=SUBSTRING_INDEX(u_fullname, ' ', 1)"
			);
			dbquery("ALTER TABLE {$TBLPREFIX}users DROP COLUMN u_fullname");
		}
		if (!$has_relation_privacy) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_relationship_privacy VARCHAR(2) NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_max_relation_path    INT        NULL");
		}
		if (!$has_auto_accept) {
			dbquery("ALTER TABLE {$TBLPREFIX}users ADD u_auto_accept VARCHAR(2) NULL");
			dbquery("UPDATE {$TBLPREFIX}users SET u_auto_accept='N'");
		}
	}
	if (!$has_messages) {
		$res = dbquery("DROP TABLE {$TBLPREFIX}messages", false);
		dbquery(
			"CREATE TABLE {$TBLPREFIX}messages (".
			" m_id      INT          NOT NULL,".
			" m_from    VARCHAR(255)     NULL,".
			" m_to      VARCHAR(30)      NULL,".
			" m_subject VARCHAR(255)     NULL,".
			" m_body    TEXT             NULL,".
			" m_created VARCHAR(255)     NULL,".
			" PRIMARY KEY (m_id)".
			") ".PGV_DB_UTF8_TABLE
		);
		dbquery("CREATE INDEX {$TBLPREFIX}messages_to ON {$TBLPREFIX}messages (m_to)");
	}
	if (!$has_favorites || $sqlite && (!$has_fav_note)) {
		dbquery("DROP TABLE {$TBLPREFIX}favorites", false);
		dbquery(
			"CREATE TABLE {$TBLPREFIX}favorites (".
			" fv_id       INT               NOT NULL,".
		 	" fv_username VARCHAR(30)       NULL,".
			" fv_gid    ".PGV_DB_COL_XREF." NULL,".
			" fv_type   ".PGV_DB_COL_TAG."  NULL,".
			" fv_file     VARCHAR(100)      NULL,".
			" fv_url      VARCHAR(255)      NULL,".
		 	" fv_title    VARCHAR(255)      NULL,".
			" fv_note     TEXT,".
			" PRIMARY KEY (fv_id)".
			") ".PGV_DB_UTF8_TABLE
		);
		dbquery("CREATE INDEX {$TBLPREFIX}favorites_username ON {$TBLPREFIX}favorites (fv_username)");
	} else {
		if (!$has_fav_note) {
			dbquery("ALTER TABLE {$TBLPREFIX}favorites ADD fv_url   VARCHAR(255) NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}favorites ADD fv_title VARCHAR(255) NULL");
			dbquery("ALTER TABLE {$TBLPREFIX}favorites ADD fv_note  TEXT         NULL");
		}
	}
	if (!$has_blocks || $sqlite && (!$has_blockconfig)) {
		dbquery("DROP TABLE {$TBLPREFIX}blocks", false);
		dbquery(
			"CREATE TABLE {$TBLPREFIX}blocks (".
			" b_id       INT          NOT NULL,".
			" b_username VARCHAR(100)     NULL,".
			" b_location VARCHAR(30)      NULL,".
		 	" b_order    INT              NULL,".
			" b_name     VARCHAR(255)     NULL,".
			" b_config   TEXT             NULL,".
			" PRIMARY KEY (b_id)".
			") ".PGV_DB_UTF8_TABLE
		);
		dbquery("CREATE INDEX {$TBLPREFIX}blocks_username ON {$TBLPREFIX}blocks (b_username)");
	} else {
		if (!$has_blockconfig) {
			dbquery("ALTER TABLE {$TBLPREFIX}blocks ADD b_config TEXT NOT NULL");
		}
	}
	if (!$has_news) {
		dbquery("DROP TABLE {$TBLPREFIX}news", false);
		dbquery(
			"CREATE TABLE {$TBLPREFIX}news (".
			" n_id       INT          NOT NULL,".
			" n_username VARCHAR(100)     NULL,".
			" n_date     INT              NULL,".
			" n_title    VARCHAR(255)     NULL,".
			" n_text     TEXT             NULL,".
			" PRIMARY KEY (n_id)".
			") ".PGV_DB_UTF8_TABLE
		);
		dbquery("CREATE INDEX {$TBLPREFIX}news_username ON {$TBLPREFIX}news (n_username)");
	}
	if (!$has_mutex) {
		dbquery("DROP TABLE {$TBLPREFIX}mutex", false);
		dbquery(
			"CREATE TABLE {$TBLPREFIX}mutex (".
			" mx_id     INT          NOT NULL,".
			" mx_name   VARCHAR(255)     NULL,".
		 	" mx_thread VARCHAR(255)     NULL,".
			" mx_time   INT              NULL,".
			" PRIMARY KEY (mx_id)".
			") ".PGV_DB_UTF8_TABLE
		);
		dbquery("CREATE INDEX {$TBLPREFIX}mutex_name ON {$TBLPREFIX}mutex (mx_name)");
	}
	return true;
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

//----------------------------------- addMessage
//-- stores a new message in the database
function addMessage($message) {
	global $TBLPREFIX, $CONTACT_METHOD, $pgv_lang,$CHARACTER_SET, $LANGUAGE, $PGV_STORE_MESSAGES, $SERVER_URL, $PGV_SIMPLE_MAIL, $WEBMASTER_EMAIL, $DBCONN;
	global $TEXT_DIRECTION, $TEXT_DIRECTION_array, $DATE_FORMAT, $DATE_FORMAT_array, $TIME_FORMAT, $TIME_FORMAT_array, $WEEK_START, $WEEK_START_array;
	global $PHPGEDVIEW_EMAIL;

	//-- do not allow users to send a message to themselves
	if ($message["from"]==$message["to"])
		return false;

	require_once('includes/functions/functions_mail.php');

	if (!get_user_id($message["to"])) {
			//-- the to user must be a valid user in the system before it will send any mails
			return false;
	}

	// Switch to the "from" user's language
	$oldLanguage = $LANGUAGE;
	$from_lang=get_user_setting($message["from"], 'language');
	if ($from_lang && $LANGUAGE!=$from_lang)
		loadLanguage($from_lang);

	//-- setup the message body for the "from" user
	$email2 = stripslashes($message["body"]);
	if (isset($message["from_name"]))
		$email2 = $pgv_lang["message_from_name"]." ".$message["from_name"]."\r\n".$pgv_lang["message_from"]." ".$message["from_email"]."\r\n\r\n".$email2;
	if (!empty($message["url"]))
		$email2 .= "\r\n\r\n--------------------------------------\r\n\r\n".$pgv_lang["viewing_url"]."\r\n".$SERVER_URL.$message["url"]."\r\n";
	$email2 .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\r\n";
	$email2 .= "DNS LOOKUP: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n";
	$email2 .= "LANGUAGE: $LANGUAGE\r\n";
	$subject2 = "[".$pgv_lang["phpgedview_message"].($TEXT_DIRECTION=="ltr"?"] ":" [").stripslashes($message["subject"]);
	$from ="";
	if (!get_user_id($message["from"])) {
		$from = $message["from"];
		$email2 = $pgv_lang["message_email3"]."\r\n\r\n".stripslashes($email2);
		$fromFullName = $message["from"];
	} else {
		$fromFullName = getUserFullName($message['from']);
		if (!$PGV_SIMPLE_MAIL)
			$from = hex4email(stripslashes($fromFullName),$CHARACTER_SET). " <".get_user_setting($message["from"], 'email').">";
		else
			$from = get_user_setting($message["from"], 'email');
		$email2 = $pgv_lang["message_email2"]."\r\n\r\n".stripslashes($email2);

	}
	if ($message["method"]!="messaging") {
		$subject1 = "[".$pgv_lang["phpgedview_message"].($TEXT_DIRECTION=="ltr"?"] ":" [").stripslashes($message["subject"]);
		if (!get_user_id($message["from"])) {
			$email1 = $pgv_lang["message_email1"];
			if (!empty($message["from_name"]))
				$email1 .= $message["from_name"]."\r\n\r\n".stripslashes($message["body"]);
			else
				$email1 .= $from."\r\n\r\n".stripslashes($message["body"]);
		} else {
			$email1 = $pgv_lang["message_email1"];
			$email1 .= stripslashes($fromFullName)."\r\n\r\n".stripslashes($message["body"]);
		}
		if (!isset($message["no_from"])) {
			if (stristr($from, $PHPGEDVIEW_EMAIL)){
				$from = get_user_setting($WEBMASTER_EMAIL, 'email');
			}
			if (!get_user_id($message["from"]))
				$header2 = $PHPGEDVIEW_EMAIL;
			else
				if (isset($to))
					$header2 = $to;
			if (!empty($header2))
				pgvMail($from, $header2, $subject2, $email2);
		}
	}

	//-- Load the "to" users language
	$to_lang=get_user_setting($message["to"], 'language');
	if ($to_lang && $LANGUAGE!=$to_lang)
		loadLanguage($to_lang);

	if (isset($message["from_name"]))
		$message["body"] = $pgv_lang["message_from_name"]." ".$message["from_name"]."\r\n".$pgv_lang["message_from"]." ".$message["from_email"]."\r\n\r\n".$message["body"];
	//-- [ phpgedview-Feature Requests-1588353 ] Supress admin IP address in Outgoing PGV Email
	if (!userIsAdmin(get_user_id($message["from"]))) {
		if (!empty($message["url"]))
			$message["body"] .= "\r\n\r\n--------------------------------------\r\n\r\n".$pgv_lang["viewing_url"]."\r\n".$SERVER_URL.$message["url"]."\r\n";
		$message["body"] .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\r\n";
		$message["body"] .= "DNS LOOKUP: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n";
		$message["body"] .= "LANGUAGE: $LANGUAGE\r\n";
	}
	if (!isset($message["created"]))
		$message["created"] = gmdate ("M d Y H:i:s");
	if ($PGV_STORE_MESSAGES && ($message["method"]!="messaging3" && $message["method"]!="mailto" && $message["method"]!="none")) {
		$newid = get_next_id("messages", "m_id");
		$sql = "INSERT INTO {$TBLPREFIX}messages VALUES ($newid, '".$DBCONN->escapeSimple($message["from"])."','".$DBCONN->escapeSimple($message["to"])."','".$DBCONN->escapeSimple($message["subject"])."','".$DBCONN->escapeSimple($message["body"])."','".$DBCONN->escapeSimple($message["created"])."')";
		$res = dbquery($sql);

	}
	if ($message["method"]!="messaging") {
		$subject1 = "[".$pgv_lang["phpgedview_message"].($TEXT_DIRECTION=="ltr"?"] ":" [").stripslashes($message["subject"]);
		if (!get_user_id($message["from"])) {
			$email1 = $pgv_lang["message_email1"];
			if (!empty($message["from_name"]))
				$email1 .= $message["from_name"]."\r\n\r\n".stripslashes($message["body"]);
			else
				$email1 .= $from."\r\n\r\n".stripslashes($message["body"]);
		} else {
			$email1 = $pgv_lang["message_email1"];
			$email1 .= stripslashes($fromFullName)."\r\n\r\n".stripslashes($message["body"]);
		}
		if (!get_user_id($message["to"])) {
			//-- the to user must be a valid user in the system before it will send any mails
			return false;
		} else {
			$toFullName=getUserFullName($message['to']);
			if (!$PGV_SIMPLE_MAIL)
				$to = hex4email(stripslashes($toFullName),$CHARACTER_SET). " <".get_user_setting($message["to"], 'email').">";
			else
				$to = get_user_setting($message["to"], 'email');
		}
		if (get_user_setting($message["to"], 'email'))
			pgvMail($to, $from, $subject1, $email1);
	}

	if ($LANGUAGE!=$oldLanguage)
		loadLanguage($oldLanguage);			// restore language settings if needed

	return true;
}

//----------------------------------- deleteMessage
//-- deletes a message in the database
function deleteMessage($message_id) {
	global $TBLPREFIX;

	$sql = "DELETE FROM {$TBLPREFIX}messages WHERE m_id=".$message_id;
	$res = dbquery($sql);

	if ($res)
		return true;
	else
		return false;
}

//----------------------------------- getUserMessages
//-- Return an array of a users messages
function getUserMessages($username) {
	global $TBLPREFIX;

	$messages = array();
	$sql = "SELECT * FROM {$TBLPREFIX}messages WHERE m_to='$username' ORDER BY m_id DESC";
	$res = dbquery($sql);

	while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$message = array();
		$message["id"] = $row["m_id"];
		$message["to"] = $row["m_to"];
		$message["from"] = $row["m_from"];
		$message["subject"] = stripslashes($row["m_subject"]);
		$message["body"] = stripslashes($row["m_body"]);
		$message["created"] = $row["m_created"];
		$messages[] = $message;
	}
	return $messages;
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
	$sql = "SELECT * FROM {$TBLPREFIX}favorites WHERE ";
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
	$sql = "INSERT INTO {$TBLPREFIX}favorites VALUES ($newid, '".$DBCONN->escapeSimple($favorite["username"])."'," .
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

	$sql = "DELETE FROM {$TBLPREFIX}favorites WHERE fv_id=".$fv_id;
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
	global $TBLPREFIX, $DBCONN, $CONFIGURED;

	$favorites = array();
	//-- make sure we don't try to look up favorites for unconfigured sites
	if (!$CONFIGURED || DB::isError($DBCONN))
		return $favorites;

	$sql = "SELECT * FROM {$TBLPREFIX}favorites WHERE fv_username='".$DBCONN->escapeSimple($username)."'";
	$res = dbquery($sql);

	if (DB::isError($res))
		return $favorites;
	while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (get_id_from_gedcom($row["fv_file"])) { // If gedcom exists
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
	}
	$res->free();
	return $favorites;
}

/**
 * get blocks for the given username
 *
 * retrieve the block configuration for the given user
 * if no blocks have been set yet, and the username is a valid user (not a gedcom) then try and load
 * the defaultuser blocks.
 * @param string $username	the username or gedcom name for the blocks
 * @return array	an array of the blocks.  The two main indexes in the array are "main" and "right"
 */
function getBlocks($username) {
	global $TBLPREFIX, $DBCONN;

	$blocks = array();
	$blocks["main"] = array();
	$blocks["right"] = array();
	$sql = "SELECT * FROM {$TBLPREFIX}blocks WHERE b_username='".$DBCONN->escapeSimple($username)."' ORDER BY b_location, b_order";
	$res = dbquery($sql);
	if (DB::isError($res))
		return $blocks;
	if ($res->numRows() > 0) {
		while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			$row = db_cleanup($row);
			if (!isset($row["b_config"]))
				$row["b_config"]="";
			if ($row["b_location"]=="main")
				// Were earlier versions of wrongly adding slashes to quotes in serialized data?
				// Unfortunately, we can't use stripslashes() as this breaks valid data.
				// Instead, use @unserialize to skip any errors.
				// TODO: try both with and without stripslashes and see which works ???
				$blocks["main"][$row["b_order"]] = array($row["b_name"], @unserialize($row["b_config"]));
			if ($row["b_location"]=="right")
				$blocks["right"][$row["b_order"]] = array($row["b_name"], @unserialize($row["b_config"]));
		}
	} else {
		if (get_user_id($username)) {
			//-- if no blocks found, check for a default block setting
			$sql = "SELECT * FROM {$TBLPREFIX}blocks WHERE b_username='defaultuser' ORDER BY b_location, b_order";
			$res2 = dbquery($sql);
			if (DB::isError($res2))
				return $blocks;
			while ($row =& $res2->fetchRow(DB_FETCHMODE_ASSOC)){
				$row = db_cleanup($row);
				if (!isset($row["b_config"]))
					$row["b_config"]="";
				if ($row["b_location"]=="main")
					$blocks["main"][$row["b_order"]] = array($row["b_name"], @unserialize($row["b_config"]));
				if ($row["b_location"]=="right")
					$blocks["right"][$row["b_order"]] = array($row["b_name"], @unserialize($row["b_config"]));
			}
			$res2->free();
		}
	}
	$res->free();
	return $blocks;
}

/**
 * Set Blocks
 *
 * Sets the blocks for a gedcom or user portal
 * the $setdefault parameter tells the program to also store these blocks as the blocks used by default
 * @param String $username the username or gedcom name to update the blocks for
 * @param array $ublocks the new blocks to set for the user or gedcom
 * @param boolean $setdefault	if true tells the program to also set these blocks as the blocks for the defaultuser
 */
function setBlocks($username, $ublocks, $setdefault=false) {
	global $TBLPREFIX, $DBCONN;

	$sql = "DELETE FROM {$TBLPREFIX}blocks WHERE b_username='".$DBCONN->escapeSimple($username)."' AND b_name!='faq'";
	$res = dbquery($sql);

	foreach($ublocks["main"] as $order=>$block) {
		$newid = get_next_id("blocks", "b_id");
		$sql = "INSERT INTO {$TBLPREFIX}blocks VALUES ($newid, '".$DBCONN->escapeSimple($username)."', 'main', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
		$res = dbquery($sql);

		if ($setdefault) {
			$newid = get_next_id("blocks", "b_id");
			$sql = "INSERT INTO {$TBLPREFIX}blocks VALUES ($newid, 'defaultuser', 'main', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
			$res = dbquery($sql);

		}
	}
	foreach($ublocks["right"] as $order=>$block) {
		$newid = get_next_id("blocks", "b_id");
		$sql = "INSERT INTO {$TBLPREFIX}blocks VALUES ($newid, '".$DBCONN->escapeSimple($username)."', 'right', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
		$res = dbquery($sql);

		if ($setdefault) {
			$newid = get_next_id("blocks", "b_id");
			$sql = "INSERT INTO {$TBLPREFIX}blocks VALUES ($newid, 'defaultuser', 'right', '$order', '".$DBCONN->escapeSimple($block[0])."', '".$DBCONN->escapeSimple(serialize($block[1]))."')";
			$res = dbquery($sql);

		}
	}
}

/**
 * Adds a news item to the database
 *
 * This function adds a news item represented by the $news array to the database.
 * If the $news array has an ["id"] field then the function assumes that it is
 * as update of an older news item.
 *
 * @author John Finlay
 * @param array $news a news item array
 */
function addNews($news) {
	global $TBLPREFIX, $DBCONN;

	if (!isset($news["date"]))
		$news["date"] = client_time();
	if (!empty($news["id"])) {
		// In case news items are added from usermigrate, it will also contain an ID.
		// So we check first if the ID exists in the database. If not, insert instead of update.
		$sql = "SELECT * FROM {$TBLPREFIX}news where n_id=".$news["id"];
		$res = dbquery($sql);

		if ($res->numRows() == 0) {
			$sql = "INSERT INTO {$TBLPREFIX}news VALUES (".$news["id"].", '".$DBCONN->escapeSimple($news["username"])."','".$DBCONN->escapeSimple($news["date"])."','".$DBCONN->escapeSimple($news["title"])."','".$DBCONN->escapeSimple($news["text"])."')";
		} else {
			$sql = "UPDATE {$TBLPREFIX}news SET n_date='".$DBCONN->escapeSimple($news["date"])."', n_title='".$DBCONN->escapeSimple($news["title"])."', n_text='".$DBCONN->escapeSimple($news["text"])."' WHERE n_id=".$news["id"];
		}
		$res->free();
	} else {
		$newid = get_next_id("news", "n_id");
		$sql = "INSERT INTO {$TBLPREFIX}news VALUES ($newid, '".$DBCONN->escapeSimple($news["username"])."','".$DBCONN->escapeSimple($news["date"])."','".$DBCONN->escapeSimple($news["title"])."','".$DBCONN->escapeSimple($news["text"])."')";
	}
	$res = dbquery($sql);

	if ($res)
		return true;
	else
		return false;
}

/**
 * Deletes a news item from the database
 *
 * @author John Finlay
 * @param int $news_id the id number of the news item to delete
 */
function deleteNews($news_id) {
	global $TBLPREFIX;

	$sql = "DELETE FROM {$TBLPREFIX}news WHERE n_id=".$news_id;
	$res = dbquery($sql);

	if ($res)
		return true;
	else
		return false;
}

/**
 * Gets the news items for the given user or gedcom
 *
 * @param String $username the username or gedcom file name to get news items for
 */
function getUserNews($username) {
	global $TBLPREFIX, $DBCONN;

	$news = array();
	$sql = "SELECT * FROM {$TBLPREFIX}news WHERE n_username='".$DBCONN->escapeSimple($username)."' ORDER BY n_date DESC";
	$res = dbquery($sql);

	while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$n = array();
		$n["id"] = $row["n_id"];
		$n["username"] = $row["n_username"];
		$n["date"] = $row["n_date"];
		$n["title"] = stripslashes($row["n_title"]);
		$n["text"] = stripslashes($row["n_text"]);
		$n["anchor"] = "article".$row["n_id"];
		$news[$row["n_id"]] = $n;
	}
	$res->free();
	return $news;
}

/**
 * Gets the news item for the given news id
 *
 * @param int $news_id the id of the news entry to get
 */
function getNewsItem($news_id) {
	global $TBLPREFIX;

	$news = array();
	$sql = "SELECT * FROM {$TBLPREFIX}news WHERE n_id='$news_id'";
	$res = dbquery($sql);

	while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$n = array();
		$n["id"] = $row["n_id"];
		$n["username"] = $row["n_username"];
		$n["date"] = $row["n_date"];
		$n["title"] = stripslashes($row["n_title"]);
		$n["text"] = stripslashes($row["n_text"]);
		$n["anchor"] = "article".$row["n_id"];
		$res->free();
		return $n;
	}
}

?>

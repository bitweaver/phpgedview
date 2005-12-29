<?php
/**
 * Main configuration file required by all other files in PGV
 *
 * The variables in this file are the main configuration variable for the site
 * Gedcom specific configuration variables are stored in the config_gedcom.php file.
 * Site administrators may edit these settings online through the editconfig.php file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id: config.php,v 1.2 2005/12/29 21:07:22 lsces Exp $
 */

if (preg_match("/\Wconfig.php/", $_SERVER["SCRIPT_NAME"])>0) {
	print "Got your hand caught in the cookie jar.";
	exit;
}

/**
 * Absolut Path to PhpGedView installation
 *
 * this is mostly used when running PGV as a module under a NUKE environment
 * @global $PGV_BASE_DIRECTORY
 */
$PGV_BASE_DIRECTORY = "";						//-- path to phpGedView (Only needed when running as phpGedView from another php program such as postNuke, otherwise leave it blank)
$DBTYPE = "ibase";								//-- type of database to connect when using the PEAR:DB module
$DBHOST = "localhost";							//-- Host where MySQL database is kept
$DBUSER = "SYSDBA";								//-- MySQL database User Name
$DBPASS = "generate";							//-- MySQL database User Password
$DBNAME = "phpgedview";							//-- The MySQL database name where you want PHPGedView to build its tables
$DBPERSIST = true;								//-- Use persistent database connections
$TBLPREFIX = "PGV_";							//-- prefix to include on table names
$INDEX_DIRECTORY = "./index/";					//-- Readable and Writeable Directory to store index files (include the trailing "/")
$AUTHENTICATION_MODULE = "authentication.php";	//-- File that contains authentication functions
$PGV_STORE_MESSAGES = true;						//-- allow messages sent to users to be stored in the PGV system
$PGV_SIMPLE_MAIL = true;						//-- allow admins to set this so that they can override the name <emailaddress> combination in the emails
$USE_REGISTRATION_MODULE = true;				//-- turn on the user self registration module
$REQUIRE_ADMIN_AUTH_REGISTRATION = true;		//-- require an admin user to authorize a new registration before a user can login
$ALLOW_USER_THEMES = true;						//-- Allow user to set their own theme
$ALLOW_CHANGE_GEDCOM = true;					//-- A true value will provide a link in the footer to allow users to change the gedcom they are viewing
$LOGFILE_CREATE = "monthly";					//-- set how often new log files are created, "none" turns logs off, "daily", "weekly", "monthly", "yearly"
$LOG_LANG_ERROR = false;						//-- Set if non-existing language variables should be written to a logfile
$PGV_SESSION_SAVE_PATH = 'c:\Network\Apache2\tmp';
												//-- Path to save PHP session Files -- DO NOT MODIFY unless you know what you are doing
												//-- leaving it blank will use the default path for your php configuration as found in php.ini
$PGV_SESSION_TIME = "7200";						//-- number of seconds to wait before an inactive session times out
$SERVER_URL = "http://study/phpgedview-4/";		//-- the URL used to access this server
$LOGIN_URL = "";								//-- the URL to use to go to the login page, use this value if you want to redirect to a different site when users login, useful for switching from http to https
$MAX_VIEWS = "100";								//-- the maximum number of page views per xx seconds per session
$MAX_VIEW_TIME = "0";							//-- the number of seconds in which the maximum number of views must not be reached
$PGV_MEMORY_LIMIT = "32M";						//-- the maximum amount of memory that PGV should be allowed to consume
$ALLOW_REMEMBER_ME = true;						//-- whether the users have the option of being remembered on the current computer
$CONFIG_VERSION = "4.0";						//-- the version this config file goes to

$DIRECTORY_MODE = "db";						//-- User info stored in db or ldap directory

$CONFIGURED = true;
require_once($PGV_BASE_DIRECTORY."includes/session.php");
?>

<?php
/**
 * Online UI for editing config.php site configuration variables
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
 * This Page Is Valid XHTML 1.0 Transitional! > 17 September 2005
 *
 * @package PhpGedView
 * @subpackage Admin
 * @see config.php
 * @version $Id: editconfig.php,v 1.5 2006/10/04 12:07:54 lsces Exp $
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

// leave manual config until we can move it to bitweaver table 
require "config.php";
require $confighelpfile["english"];
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];
require $helptextfile["english"];
if (file_exists($helptextfile[$LANGUAGE])) require $helptextfile[$LANGUAGE];

if (empty($action)) $action="";
if (!isset($LOGIN_URL)) $LOGIN_URL = "";
if (!isset($COMMIT_COMMAND)) $COMMIT_COMMAND="";

print_header($pgv_lang["configure_head"]);
if ($action=="update" && !isset($security_user)) {
	if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
	$boolarray = array();
	$boolarray["yes"]="true";
	$boolarray["no"]="false";
	$boolarray[false]="false";
	$boolarray[true]="true";
	print $pgv_lang["performing_update"];
	print "<br />";
	$configtext = implode('', file("config.php"));
	print $pgv_lang["config_file_read"];
	print "<br />\n";
	if (preg_match("'://'", $NEW_SERVER_URL)==0) $NEW_SERVER_URL = "http://".$NEW_SERVER_URL;
	if (preg_match("'/$'", $NEW_SERVER_URL)==0) $NEW_SERVER_URL .= "/";
	$_POST["NEW_INDEX_DIRECTORY"] = preg_replace('/\\\/','/',$_POST["NEW_INDEX_DIRECTORY"]);
	if (preg_match('/\$DBTYPE\s*=\s*".*";/', $configtext)>0) {
		$configtext = preg_replace('/\$DBTYPE\s*=\s*".*";/', "\$DBTYPE = \"".$_POST["NEW_DBTYPE"]."\";", $configtext);
	}
	else {
		$configtext = preg_replace('/\$DBHOST/', "\$DBTYPE = \"".$_POST["NEW_DBTYPE"]."\";\r\n\$DBHOST", $configtext);
	}
	if ($CONFIG_VERSION<4) {
		$configtext = preg_replace('/\PHPGEDVIEW_DB_PREFIX/', "\$DBPERSIST = false;\r\n\PHPGEDVIEW_DB_PREFIX", $configtext);
		$configtext = preg_replace('/\$CONFIG_VERSION\s*=\s*".*";/', "\$CONFIG_VERSION = \"4.0\";", $configtext);
	}
	$configtext = preg_replace('/\$DBHOST\s*=\s*".*";/', "\$DBHOST = \"".$_POST["NEW_DBHOST"]."\";", $configtext);
	$configtext = preg_replace('/\$DBUSER\s*=\s*".*";/', "\$DBUSER = \"".$_POST["NEW_DBUSER"]."\";", $configtext);
	if (!empty($_POST["NEW_DBPASS"])) $configtext = preg_replace('/\$DBPASS\s*=\s*".*";/', "\$DBPASS = \"".$_POST["NEW_DBPASS"]."\";", $configtext);
	$configtext = preg_replace('/\$DBNAME\s*=\s*".*";/', "\$DBNAME = \"".$_POST["NEW_DBNAME"]."\";", $configtext);
	$configtext = preg_replace('/\$DBPERSIST\s*=\s*.*;/', "\$DBPERSIST = ".$boolarray[$_POST["NEW_DBPERSIST"]].";", $configtext);
	$configtext = preg_replace('/\PHPGEDVIEW_DB_PREFIX\s*=\s*".*";/', "\PHPGEDVIEW_DB_PREFIX = \"".$_POST["NEW_TBLPREFIX"]."\";", $configtext);
	$configtext = preg_replace('/\$ALLOW_CHANGE_GEDCOM\s*=\s*.*;/', "\$ALLOW_CHANGE_GEDCOM = ".$boolarray[$_POST["NEW_ALLOW_CHANGE_GEDCOM"]].";", $configtext);
	$configtext = preg_replace('/\$USE_REGISTRATION_MODULE\s*=\s*.*;/', "\$USE_REGISTRATION_MODULE = ".$boolarray[$_POST["NEW_USE_REGISTRATION_MODULE"]].";", $configtext);
	$configtext = preg_replace('/\$REQUIRE_ADMIN_AUTH_REGISTRATION\s*=\s*.*;/', "\$REQUIRE_ADMIN_AUTH_REGISTRATION = ".$boolarray[$_POST["NEW_REQUIRE_ADMIN_AUTH_REGISTRATION"]].";", $configtext);
	$configtext = preg_replace('/\$PGV_SIMPLE_MAIL\s*=\s*.*;/', "\$PGV_SIMPLE_MAIL = ".$boolarray[$_POST["NEW_PGV_SIMPLE_MAIL"]].";", $configtext);
	$configtext = preg_replace('/\$PGV_STORE_MESSAGES\s*=\s*.*;/', "\$PGV_STORE_MESSAGES = ".$boolarray[$_POST["NEW_PGV_STORE_MESSAGES"]].";", $configtext);
	$configtext = preg_replace('/\$ALLOW_USER_THEMES\s*=\s*.*;/', "\$ALLOW_USER_THEMES = ".$boolarray[$_POST["NEW_ALLOW_USER_THEMES"]].";", $configtext);
	$configtext = preg_replace('/\$ALLOW_REMEMBER_ME\s*=\s*.*;/', "\$ALLOW_REMEMBER_ME = ".$boolarray[$_POST["NEW_ALLOW_REMEMBER_ME"]].";", $configtext);
	$configtext = preg_replace('/\$INDEX_DIRECTORY\s*=\s*".*";/', "\$INDEX_DIRECTORY = \"".$_POST["NEW_INDEX_DIRECTORY"]."\";", $configtext);
	$configtext = preg_replace('/\$LOGFILE_CREATE\s*=\s*".*";/', "\$LOGFILE_CREATE = \"".$_POST["NEW_LOGFILE_CREATE"]."\";", $configtext);
	$configtext = preg_replace('/\$PGV_SESSION_SAVE_PATH\s*=\s*".*";/', "\$PGV_SESSION_SAVE_PATH = \"".$_POST["NEW_PGV_SESSION_SAVE_PATH"]."\";", $configtext);
	$configtext = preg_replace('/\$PGV_SESSION_TIME\s*=\s*".*";/', "\$PGV_SESSION_TIME = \"".$_POST["NEW_PGV_SESSION_TIME"]."\";", $configtext);
	$configtext = preg_replace('/\$MAX_VIEWS\s*=\s*".*";/', "\$MAX_VIEWS = \"".$_POST["NEW_MAX_VIEWS"]."\";", $configtext);
	$configtext = preg_replace('/\$MAX_VIEW_TIME\s*=\s*".*";/', "\$MAX_VIEW_TIME = \"".$_POST["NEW_MAX_VIEW_TIME"]."\";", $configtext);
	$configtext = preg_replace('/\$SERVER_URL\s*=\s*".*";/', "\$SERVER_URL = \"".$_POST["NEW_SERVER_URL"]."\";", $configtext);
	$configtext = preg_replace('/\$COMMIT_COMMAND\s*=\s*".*";/', "\$COMMIT_COMMAND = \"".$_POST["NEW_COMMIT_COMMAND"]."\";", $configtext);
	if (preg_match('/\$DBTYPE\s*=\s*".*";/', $configtext)>0) {
		$configtext = preg_replace('/\$LOGIN_URL\s*=\s*".*";/', "\$LOGIN_URL = \"".$_POST["NEW_LOGIN_URL"]."\";", $configtext);
	}
	else {
		$configtext = preg_replace('/\$PGV_MEMORY_LIMIT/', "\$LOGIN_URL = \"".$_POST["NEW_LOGIN_URL"]."\";\r\n\$PGV_MEMORY_LIMIT", $configtext);
	}
	$configtext = preg_replace('/\$PGV_MEMORY_LIMIT\s*=\s*".*";/', "\$PGV_MEMORY_LIMIT = \"".$_POST["NEW_PGV_MEMORY_LIMIT"]."\";", $configtext);
	$DBHOST = $_POST["NEW_DBHOST"];
	$DBTYPE = $_POST["NEW_DBTYPE"];
	$DBUSER = $_POST["NEW_DBUSER"];
	$DBNAME = $_POST["NEW_DBNAME"];
	if (!empty($_POST["NEW_DBPASS"])) $DBPASS = $_POST["NEW_DBPASS"];

	//-- make sure the database configuration is set properly
	if (check_db(true)) {
		$configtext = preg_replace('/\$CONFIGURED\s*=\s*.*;/', "\$CONFIGURED = true;", $configtext);
		$CONFIGURED = true;
//		require_once("includes/functions_import.php");
	}

	// Save the languages the user has chosen to have active on the website
	$Filename = $INDEX_DIRECTORY . "lang_settings.php";
	if (!file_exists($Filename)) copy("includes/lang_settings_std.php", $Filename);

	if (isset($NEW_LANGS)) {
		// Set the chosen languages to active
		foreach ($NEW_LANGS as $key => $name) {
			$pgv_lang_use[$name] = true;
		}
	
		// Set the other languages to non-active
		foreach ($pgv_lang_use as $name => $value) {
			if (!isset($NEW_LANGS[$name])) $pgv_lang_use[$name] = false;
		}
		$error = "";
		if ($file_array = file($Filename)) {
			@copy($Filename, $Filename . ".old");
			if ($fp = @fopen($Filename, "w")) {
				for ($x = 0; $x < count($file_array); $x++) {
					fwrite($fp, $file_array[$x]);
					$dDummy00 = trim($file_array[$x]);
					if ($dDummy00 == "//-- NEVER manually delete or edit this entry and every line below this entry! --START--//") break;
				}
				fwrite($fp, "\r\n");
				fwrite($fp, "// Array definition of language_settings\r\n");
				fwrite($fp, "\$language_settings = array();\r\n");
				foreach ($language_settings as $key => $value) {
					fwrite($fp, "\r\n");
					fwrite($fp, "//-- settings for " . $languages[$key] . "\r\n");
					fwrite($fp, "\$lang = array();\r\n");
					fwrite($fp, "\$lang[\"pgv_langname\"]    = \"" . $languages[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"pgv_lang_use\"]    = ");
					if ($pgv_lang_use[$key]) fwrite($fp, "true"); else fwrite($fp, "false");
					fwrite($fp, ";\r\n");
					fwrite($fp, "\$lang[\"pgv_lang\"]    = \"" . $pgv_lang[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"lang_short_cut\"]    = \"" . $lang_short_cut[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"langcode\"]    = \"" . $lang_langcode[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"pgv_language\"]    = \"" . $pgv_language[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"confighelpfile\"]    = \"" . $confighelpfile[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"helptextfile\"]    = \"" . $helptextfile[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"flagsfile\"]    = \"" . $flagsfile[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"factsfile\"]    = \"" . $factsfile[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"DATE_FORMAT\"]    = \"" . $DATE_FORMAT_array[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"TIME_FORMAT\"]    = \"" . $TIME_FORMAT_array[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"WEEK_START\"]    = \"" . $WEEK_START_array[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"TEXT_DIRECTION\"]    = \"" . $TEXT_DIRECTION_array[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"NAME_REVERSE\"]    = ");
					if ($NAME_REVERSE_array[$key]) fwrite($fp, "true"); else fwrite($fp, "false");
					fwrite($fp, ";\r\n");
					fwrite($fp, "\$lang[\"ALPHABET_upper\"]    = \"" . $ALPHABET_upper[$key] . "\";\r\n");
					fwrite($fp, "\$lang[\"ALPHABET_lower\"]    = \"" . $ALPHABET_lower[$key] . "\";\r\n");
					fwrite($fp, "\$language_settings[\"" . $languages[$key] . "\"]  = \$lang;\r\n");
				}
				$end_found = false;
				for ($x = 0; $x < count($file_array); $x++) {
					$dDummy00 = trim($file_array[$x]);
					if ($dDummy00 == "//-- NEVER manually delete or edit this entry and every line above this entry! --END--//"){fwrite($fp, "\r\n"); $end_found = true;}
					if ($end_found) fwrite($fp, $file_array[$x]);
				}
				fclose($fp);
				$logline = AddToLog("Language settings file, lang_settings.php, updated by >".getUserName()."<");
	 			if (!empty($COMMIT_COMMAND)) check_in($logline, $Filename, $INDEX_DIRECTORY);	
			}
			else $error = "lang_config_write_error";
		}
		else $error = "lang_set_file_read_error";
	}

	if (!empty($error)) {
	    print "<span class=\"error\">" . $pgv_lang[$error] . "</span><br /><br />";
	}

	if (!isset($download)) {
		$res = @eval($configtext);
		if ($res===false) {
			$fp = fopen("config.php", "wb");
			if (!$fp) {
				print "<span class=\"error\">";
				print $pgv_lang["pgv_config_write_error"];
				print "<br /></span>\n";
			}
			else {
				fwrite($fp, $configtext);
				fclose($fp);
				$logline = AddToLog("config.php updated by >".getUserName()."<");
	 			if (!empty($COMMIT_COMMAND)) check_in($logline, "config.php", "");	
				if ($CONFIGURED) print "<script language=\"JavaScript\" type=\"text/javascript\">\nwindow.location = 'editconfig.php';\n</script>\n";
			}
		}
		else print "<span class=\"error\">There was an error in the generated config.php.</span>".htmlentities($configtext);
		foreach($_POST as $key=>$value) {
			$key=preg_replace("/NEW_/", "", $key);
			if ($value=='yes') $$key=true;
			else if ($value=='no') $$key=false;
			else $$key=$value;
		}
	}
	else {
		$_SESSION["config.php"]=$configtext;
		print "<br /><br /><a href=\"config_download.php?file=config.php\">";
		print $pgv_lang["download_here"];
		print "</a><br /><br />\n";
	}
}

?>
<script language="JavaScript" type="text/javascript">
<!--
	var helpWin;
	function helpPopup(which) {
		if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
		else helpWin.location = 'editconfig_help.php?help='+which;
		return false;
	}
	function getHelp(which) {
		if ((helpWin)&&(!helpWin.closed)) helpWin.location='editconfig_help.php?help='+which;
	}
	function closeHelp() {
		if (helpWin) helpWin.close();
	}
	function changeDBtype(dbselect) {
		if (dbselect.options[dbselect.selectedIndex].value=='sqlite') {
			document.configform.NEW_DBNAME.value='./index/phpgedview.db';
		}
		else {
			document.configform.NEW_DBNAME.value='phpgedview';
		}
	}
	//-->
</script>
<form method="post" name="configform" action="editconfig.php">
<input type="hidden" name="action" value="update" />
<?php
	if (($CONFIGURED || $action=="update") && !check_db(true)) {
		print "<span class=\"error\">";
		print $pgv_lang["db_setup_bad"];
		print "</span><br />";
		print "<span class=\"error\">".$DBCONN->getMessage()." ".$DBCONN->getUserInfo()."</span><br />";
		if ($CONFIGURED==true) {
			//-- force the incoming user to enter the database password before they can configure the site for security.
			if (!isset($_POST["security_check"]) || !isset($_POST["security_user"]) || (($_POST["security_check"]!=$DBPASS)&&($_POST["security_user"]==$DBUSER))) {
				print "<br /><br />";
				print_text("enter_db_pass");
				print "<br />";
				print $pgv_lang["DBUSER"];
				print " <input type=\"text\" name=\"security_user\" /><br />\n";
				print $pgv_lang["DBPASS"];
				print " <input type=\"password\" name=\"security_check\" /><br />\n";
				print "<input type=\"submit\" value=\"";
				print $pgv_lang["login"];
				print "\" />\n";
				print "</form>\n";
				print_footer();
				exit;
			}
		}
	}
	print "<table class=\"facts_table\">";
	print "<tr><td class=\"topbottombar\" colspan=\"2\">";
	print "<span class=\"subheaders\">";
	print $pgv_lang["configure"];
	print "</span><br /><br />";
	print "<div class=\"ltr\">".$pgv_lang["welcome"];
	print "<br />";
	print $pgv_lang["review_readme"];
	print_text("return_editconfig");
	if ($CONFIGURED) {
		print "<a href=\"editgedcoms.php\"><b>";
		print $pgv_lang["admin_gedcoms"];
		print "</b></a><br /><br />\n";
	}
	$i = 0;
	print "</div></td></tr>";
?>
	<table class="facts_table">
	<tr>
		<td class="descriptionbox width20 wrap"><?php print_help_link("DBTYPE_help", "qm", "DBTYPE"); print $pgv_lang["DBTYPE"];?></td>
		<td class="optionbox"><select name="NEW_DBTYPE" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBTYPE_help');" onchange="changeDBtype(this);">
				<!--<option value="dbase" <?php if ($DBTYPE=='dbase') print "selected=\"selected\""; ?>><?php print $pgv_lang["dbase"];?></option>-->
				<!--<option value="fbsql" <?php if ($DBTYPE=='fbsql') print "selected=\"selected\""; ?>><?php print $pgv_lang["fbsql"];?></option>-->
				<option value="ibase" <?php if ($DBTYPE=='ibase') print "selected=\"selected\""; ?>><?php print $pgv_lang["ibase"];?></option>
				<!--<option value="ifx" <?php if ($DBTYPE=='ifx') print "selected=\"selected\""; ?>><?php print $pgv_lang["ifx"];?></option>-->
				<!--<option value="msql" <?php if ($DBTYPE=='msql') print "selected=\"selected\""; ?>><?php print $pgv_lang["msql"];?></option>-->
				<option value="mssql" <?php if ($DBTYPE=='mssql') print "selected=\"selected\""; ?>><?php print $pgv_lang["mssql"];?></option>
				<option value="mysql" <?php if ($DBTYPE=='mysql') print "selected=\"selected\""; ?>><?php print $pgv_lang["mysql"];?></option>
				<option value="mysqli" <?php if ($DBTYPE=='mysqli') print "selected=\"selected\""; ?>><?php print $pgv_lang["mysqli"];?></option>
				<!--<option value="oci8" <?php if ($DBTYPE=='oci8') print "selected=\"selected\""; ?>><?php print $pgv_lang["oci8"];?></option>-->
				<option value="pgsql" <?php if ($DBTYPE=='pgsql') print "selected=\"selected\""; ?>><?php print $pgv_lang["pgsql"];?></option>
				<option value="sqlite" <?php if ($DBTYPE=='sqlite') print "selected=\"selected\""; ?>><?php print $pgv_lang["sqlite"];?></option>
				<!--<option value="txtdb" <?php if ($DBTYPE=='txtdbapi') print "selected=\"selected\""; ?>><?php /* print $pgv_lang["sqlite"]; */?>TxtDB</option>-->
				<!--<option value="sybase" <?php if ($DBTYPE=='sybase') print "selected=\"selected\""; ?>><?php print $pgv_lang["sybase"];?></option>-->
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php print_help_link("DBHOST_help", "qm", "DBHOST"); print $pgv_lang["DBHOST"];?></td>
		<td class="optionbox"><input type="text" dir="ltr" name="NEW_DBHOST" value="<?php print $DBHOST?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBHOST_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php print_help_link("DBUSER_help", "qm", "DBUSER"); print $pgv_lang["DBUSER"];?></td>
		<td class="optionbox"><input type="text" name="NEW_DBUSER" value="<?php print $DBUSER?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBUSER_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php print_help_link("DBPASS_help", "qm", "DBPASS"); print $pgv_lang["DBPASS"];?></td>
		<td class="optionbox"><input type="password" name="NEW_DBPASS" value="" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBPASS_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php print_help_link("DBNAME_help", "qm", "DBNAME"); print $pgv_lang["DBNAME"];?></td>
		<td class="optionbox"><input type="text" name="NEW_DBNAME" value="<?php print $DBNAME?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBNAME_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox width20 wrap"><?php print_help_link("DBPERSIST_help", "qm", "DBPERSIST"); print $pgv_lang["DBPERSIST"];?></td>
		<td class="optionbox"><select name="NEW_DBPERSIST" tabindex="<?php $i++; print $i?>" onfocus="getHelp('DBPERSIST_help');">
				<option value="yes" <?php if ($DBPERSIST) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$DBPERSIST) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php print_help_link("TBLPREFIX_help", "qm", "TBLPREFIX"); print $pgv_lang["TBLPREFIX"];?></td>
		<td class="optionbox"><input type="text" name="NEW_TBLPREFIX" value="<?php print PHPGEDVIEW_DB_PREFIX?>" size="40" tabindex="<?php $i++; print $i?>" onfocus="getHelp('TBLPREFIX_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox width20 wrap"><?php print_help_link("ALLOW_CHANGE_GEDCOM_help", "qm", "ALLOW_CHANGE_GEDCOM"); print $pgv_lang["ALLOW_CHANGE_GEDCOM"];?></td>
		<td class="optionbox"><select name="NEW_ALLOW_CHANGE_GEDCOM" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_CHANGE_GEDCOM_help');">
				<option value="yes" <?php if ($ALLOW_CHANGE_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALLOW_CHANGE_GEDCOM) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("INDEX_DIRECTORY_help", "qm", "INDEX_DIRECTORY"); print $pgv_lang["INDEX_DIRECTORY"];?></td>
		<td class="optionbox"><input type="text" size="50" name="NEW_INDEX_DIRECTORY" value="<?php print $INDEX_DIRECTORY?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('INDEX_DIRECTORY_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("PGV_STORE_MESSAGES_help", "qm", "PGV_STORE_MESSAGES"); print $pgv_lang["PGV_STORE_MESSAGES"];?></td>
		<td class="optionbox"><select name="NEW_PGV_STORE_MESSAGES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_STORE_MESSAGES_help');">
				<option value="yes" <?php if ($PGV_STORE_MESSAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$PGV_STORE_MESSAGES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("USE_REGISTRATION_MODULE_help", "qm", "USE_REGISTRATION_MODULE"); print $pgv_lang["USE_REGISTRATION_MODULE"];?></td>
		<td class="optionbox"><select name="NEW_USE_REGISTRATION_MODULE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('USE_REGISTRATION_MODULE_help');">
				<option value="yes" <?php if ($USE_REGISTRATION_MODULE) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$USE_REGISTRATION_MODULE) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

 	<tr>
 		<td class="descriptionbox wrap"><?php print_help_link("REQUIRE_ADMIN_AUTH_REGISTRATION_help", "qm", "REQUIRE_ADMIN_AUTH_REGISTRATION"); print $pgv_lang["REQUIRE_ADMIN_AUTH_REGISTRATION"];?></td>
 		<td class="optionbox"><select name="NEW_REQUIRE_ADMIN_AUTH_REGISTRATION" tabindex="<?php $i++; print $i?>" onfocus="getHelp('REQUIRE_ADMIN_AUTH_REGISTRATION_help');">
 				<option value="yes" <?php if ($REQUIRE_ADMIN_AUTH_REGISTRATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
 				<option value="no" <?php if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
 		</td>
 	</tr>

	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("PGV_SIMPLE_MAIL_help", "qm", "PGV_SIMPLE_MAIL"); print $pgv_lang["PGV_SIMPLE_MAIL"];?></td>
		<td class="optionbox"><select name="NEW_PGV_SIMPLE_MAIL" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_SIMPLE_MAIL_help');">
				<option value="yes" <?php if ($PGV_SIMPLE_MAIL) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$PGV_SIMPLE_MAIL) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("ALLOW_USER_THEMES_help", "qm", "ALLOW_USER_THEMES"); print $pgv_lang["ALLOW_USER_THEMES"];?></td>
		<td class="optionbox"><select name="NEW_ALLOW_USER_THEMES" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_USER_THEMES_help');">
				<option value="yes" <?php if ($ALLOW_USER_THEMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
				<option value="no" <?php if (!$ALLOW_USER_THEMES) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("ALLOW_REMEMBER_ME_help", "qm", "ALLOW_REMEMBER_ME"); print $pgv_lang["ALLOW_REMEMBER_ME"];?></td>
		<td class="optionbox"><select name="NEW_ALLOW_REMEMBER_ME" tabindex="<?php $i++; print $i?>" onfocus="getHelp('ALLOW_REMEMBER_ME_help');">
 				<option value="yes" <?php if ($ALLOW_REMEMBER_ME) print "selected=\"selected\""; ?>><?php print $pgv_lang["yes"];?></option>
 				<option value="no" <?php if (!$ALLOW_REMEMBER_ME) print "selected=\"selected\""; ?>><?php print $pgv_lang["no"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("LANG_SELECTION_help", "qm", "LANG_SELECTION"); print $pgv_lang["LANG_SELECTION"];?></td>
		<td class="optionbox">
			<table class="facts_table">
			<?php
			// Build a sorted list of language names in the currently active language
			foreach ($pgv_language as $key => $value){
				$d_LangName = "lang_name_".$key;
				$SortedLangs[$key] = $pgv_lang[$d_LangName];
			}
			asort($SortedLangs);

			// Build sorted list of languages, using numeric index
			// If necessary, insert one blank filler at the end of the 2nd column
			// Always insert a blank filler at the end of the 3rd column
			$lines = ceil(count($pgv_language) / 3);
			$BlankHere = 0;
			if (($lines * 3) != count($pgv_language)) {
				$BlankHere = $lines + $lines;
			}
			$i = 1;
			$LangsList = array();
			foreach ($SortedLangs as $key => $value) {
				$LangsList[$i] = $SortedLangs[$key];
				$i++;
				if ($i == $BlankHere) {
					$LangsList[$i] = "";
					$i++;
				}
			}
			$LangsList[$i] = "";

			// Print the languages in three columns
			$curline = 1;
			$SortedLangs = array_flip($SortedLangs);

			while ($curline <= $lines) {
				// Start each table row
				print "<tr>";
				$curcol = 0;
				// Print each column
				while ($curcol < 3) {
					$j = $curline + $lines * $curcol;
					$LocalName = $LangsList[$j];
					if ($LocalName != "") {
						$LangName = $SortedLangs[$LocalName];
						print "<td class=\"optionbox\"><input type=\"checkbox\" name=\"NEW_LANGS[".$LangName."]\" value=\"".$LangName."\"";
						if ($pgv_lang_use[$LangName] == true) {
							print "checked=\"checked\"";
						}
						print "/></td>";
						print "<td class=\"descriptionbox width30\">".$LocalName."</td>\n";
					} else {
						print "<td class=\"optionbox\">&nbsp;</td>";
						print "<td class=\"descriptionbox width30\">&nbsp;</td>\n";
					}
					$curcol++;
				}
				// Finish the table row
				print "</tr>";
				$curline++;
			}
			?>
			</table>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("LOGFILE_CREATE_help", "qm", "LOGFILE_CREATE"); print $pgv_lang["LOGFILE_CREATE"];?></td>
		<td class="optionbox"><select name="NEW_LOGFILE_CREATE" tabindex="<?php $i++; print $i?>" onfocus="getHelp('LOGFILE_CREATE_help');">
				<option value="none" <?php if ($LOGFILE_CREATE=="none") print "selected=\"selected\""; ?>><?php print $pgv_lang["no_logs"];?></option>
				<option value="daily" <?php if ($LOGFILE_CREATE=="daily") print "selected=\"selected\""; ?>><?php print $pgv_lang["daily"];?></option>
				<option value="weekly" <?php if ($LOGFILE_CREATE=="weekly") print "selected=\"selected\""; ?>><?php print $pgv_lang["weekly"];?></option>
				<option value="monthly" <?php if ($LOGFILE_CREATE=="monthly") print "selected=\"selected\""; ?>><?php print $pgv_lang["monthly"];?></option>
				<option value="yearly" <?php if ($LOGFILE_CREATE=="yearly") print "selected=\"selected\""; ?>><?php print $pgv_lang["yearly"];?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("SERVER_URL_help", "qm", "SERVER_URL"); print $pgv_lang["SERVER_URL"];?></td>
		<td class="optionbox wrap"><input type="text" name="NEW_SERVER_URL" value="<?php print $SERVER_URL?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('SERVER_URL_help');" size="100" />
		<br /><?php
			$GUESS_URL = stripslashes("http://".$_SERVER["SERVER_NAME"].dirname($SCRIPT_NAME)."/");
			print_text("server_url_note");
			?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("LOGIN_URL_help", "qm", "LOGIN_URL"); print $pgv_lang["LOGIN_URL"];?></td>
		<td class="optionbox"><input type="text" name="NEW_LOGIN_URL" value="<?php print $LOGIN_URL?>" dir="ltr" tabindex="<?php $i++; print $i?>" onfocus="getHelp('LOGIN_URL_help');" size="100" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("PGV_SESSION_SAVE_PATH_help", "qm", "PGV_SESSION_SAVE_PATH"); print $pgv_lang["PGV_SESSION_SAVE_PATH"];?></td>
		<td class="optionbox"><input type="text" dir="ltr" size="50" name="NEW_PGV_SESSION_SAVE_PATH" value="<?php print $PGV_SESSION_SAVE_PATH?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_SESSION_SAVE_PATH_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("PGV_SESSION_TIME_help", "qm", "PGV_SESSION_TIME"); print $pgv_lang["PGV_SESSION_TIME"];?></td>
		<td class="optionbox"><input type="text" name="NEW_PGV_SESSION_TIME" value="<?php print $PGV_SESSION_TIME?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_SESSION_TIME_help');" /></td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("MAX_VIEW_RATE_help", "qm", "MAX_VIEW_RATE"); print $pgv_lang["MAX_VIEW_RATE"];?></td>
		<td class="optionbox">
			<input type="text" name="NEW_MAX_VIEWS" value="<?php print $MAX_VIEWS?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MAX_VIEW_RATE_help');" />
			<?php
				if ($TEXT_DIRECTION == "ltr") print $pgv_lang["page_views"];
				else print $pgv_lang["seconds"];
			?>
			<input type="text" name="NEW_MAX_VIEW_TIME" value="<?php print $MAX_VIEW_TIME?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('MAX_VIEW_RATE_help');" />
			<?php
				if ($TEXT_DIRECTION == "ltr") print $pgv_lang["seconds"];
				else print $pgv_lang["page_views"];
			?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("COMMIT_COMMAND_help", "qm", "COMMIT_COMMAND"); print $pgv_lang['COMMIT_COMMAND'];?></td>
 		<td class="optionbox"><select name="NEW_COMMIT_COMMAND" tabindex="<?php $i++; print $i?>" onfocus="getHelp('COMMIT_COMMAND_help');">
				<option value="" <?php if ($COMMIT_COMMAND=="") print "selected=\"selected\""; ?>><?php print $pgv_lang["none"];?></option>
				<option value="cvs" <?php if ($COMMIT_COMMAND=="cvs") print "selected=\"selected\""; ?>>CVS</option>
				<option value="svn" <?php if ($COMMIT_COMMAND=="svn") print "selected=\"selected\""; ?>>SVN</option>
			</select>
		</td>
 	</tr>
 	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("PGV_MEMORY_LIMIT_help", "qm", "PGV_MEMORY_LIMIT"); print $pgv_lang["PGV_MEMORY_LIMIT"];?></td>
		<td class="optionbox"><input type="text" name="NEW_PGV_MEMORY_LIMIT" value="<?php print $PGV_MEMORY_LIMIT?>" tabindex="<?php $i++; print $i?>" onfocus="getHelp('PGV_MEMORY_LIMIT_help');" /></td>
	</tr>
	<tr>
		<td class="topbottombar" colspan="2"><input type="submit" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["save_config"];?>" onclick="closeHelp();" />
		&nbsp;&nbsp;
		<input type="reset" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["reset"];?>" />
		</td>
	</tr>
<?php
	if (!file_is_writeable("config.php")) {
			print "<tr><td class=\"descriptionbox wrap\" colspan=\"2\"><span class=\"largeError\">";
			print_text("not_writable");
			print "</span></td></tr>";
			print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"";
			print $pgv_lang["download_file"];
			print "\" name=\"download\" /></td></tr>\n";
	}
?>
</table>
</form>
<?php if (!$CONFIGURED) { ?>
<script language="JavaScript" type="text/javascript">
	helpPopup('welcome_new_help');
</script>
<?php
}
?>
<script language="JavaScript" type="text/javascript">
	document.configform.NEW_DBHOST.focus();
</script>
<?php
print_footer();

?>
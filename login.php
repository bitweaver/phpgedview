<?php
/**
 * Login Page.
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
 *
 * This Page Is Valid XHTML 1.0 Transitional! > 29 August 2005
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id$
 */ 

require 'config.php';

// Extract query parameters
$url     =safe_POST('url',      PGV_REGEX_URL);
$type    =safe_POST('type',     array('full', 'simple'));
$action  =safe_POST('action');
$username=safe_POST('username', PGV_REGEX_USERNAME);
$password=safe_POST('password', PGV_REGEX_PASSWORD);
$remember=safe_POST('remember', 'yes','no');
$usertime=safe_POST('usertime');
$pid     =safe_POST('pid',      PGV_REGEX_XREF);
$ged     =safe_POST('ged',      get_all_gedcoms(), $GEDCOM);
$help_message=safe_GET('help_messge');

// Some variables can come from the URL as well as the form
if (!$url)    $url   =safe_GET('url',  PGV_REGEX_URL);
if (!$type)   $type  =safe_GET('type', array('full', 'simple'), 'full');
if (!$action) $action=safe_GET('action');

if (empty($url)) {
	// If we came here by means of a URL like http://mysite.com/foo/login.php
	// we don't have a proper login URL, and session cookies haven't been set yet
	// We'll re-load the page to properly determine cookie support
	header("Location: login.php?url=index.php?ctype=user");
}

$message='';

if ($action=='login') {
	if ($user_id=authenticateUser($username, $password)) {
		if ($usertime) {
			$_SESSION['usertime']=@strtotime($usertime);
		} else {
			$_SESSION['usertime']=time();
		}
		$_SESSION['timediff']=time()-$_SESSION['usertime'];
		$MyLanguage = get_user_setting($user_id, 'language');
		if ($MyLanguage) {
			if (isset($_SESSION['CLANGUAGE'])) {
				$_SESSION['CLANGUAGE'] = $MyLanguage;
			}
		}

		// If we have no access rights to the current gedcom, switch to one where we do
		if (!userIsAdmin($user_id)) {
			if (!userCanAccess($user_id, PGV_GED_ID)) {
				foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
					if (userCanAccess($user_id, $ged_id)) {
						$ged=$ged_name;
						$url='index.php';
						break;
					}
				}
			}
		}

		//-- section added based on UI feedback
		// $url is set to individual.php below if a URL is not passed in... it will then be resent as "individual.php" when the user attempts to login
		if ($url=='individual.php') {
			foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
				if (get_user_gedcom_setting($user_id, $ged_id, 'gedcomid')) {
					$pid = get_user_gedcom_setting($user_id, $ged_id, 'gedcomid');
					$ged = $ged_name;
					break;
				}
			}
			if ($pid) {
				$url = "individual.php?pid=".$pid;
			} else {
				//-- user does not have a pid?  Go to mygedview portal
				$url = "index.php?ctype=user";
			}
		}

		session_write_close();

		// If we've clicked login from the login page, we don't want to go back there.
		if (substr($url, 0, 9)=='login.php') {
			$url='index.php';
		}

		$urlnew = $SERVER_URL;
		if (substr($urlnew,-1,1)!="/") $urlnew .= "/";
		$url = preg_replace("/logout=1/", "", $url);
		$url = $urlnew . $url;
		if ($remember=="yes") setcookie("pgv_rem", $username, time()+60*60*24*7);
		else setcookie("pgv_rem", "", time()-60*60*24*7);

		$url .= "&";	// Simplify the preg_replace following
		$url = preg_replace("/(&|\?)ged=.*&/", "$1", html_entity_decode(rawurldecode($url),ENT_COMPAT,'UTF-8'));	// Remove any existing &ged= parameter
		if (substr($url, -1)=="&") $url = substr($url, 0, -1);
		$url .= "&ged=".$ged;
		$url = str_replace(array("&&", ".php&", ".php?&"), array("&", ".php?", ".php?"), $url);

		header("Location: ".encode_url($url, false));
		exit;
	} else {
		$message = $pgv_lang["no_login"];
	}
} else {
	$tSERVER_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $SERVER_URL);
	$tLOGIN_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $LOGIN_URL);
	if (empty($url)) {
		if ((isset($_SERVER['HTTP_REFERER'])) && ((stristr($_SERVER['HTTP_REFERER'],$tSERVER_URL)!==false)||(stristr($_SERVER['HTTP_REFERER'],$tLOGIN_URL)!==false))) {
			$url = basename($_SERVER['HTTP_REFERER']);
			if (stristr($url, ".php")===false) {
				$url = "index.php?ctype=gedcom&ged=$GEDCOM";
			}
		}
		else {
			if (isset($url)) {
				if (stristr($url,$SERVER_URL)!==false) $url = $SERVER_URL;
			}
			//else $url = $SERVER_URL;
			/* - commented out based on UI feedback
			else $url = "index.php?ctype=user";
			*/
			else $url = "individual.php";
		}
	}
	else if (stristr($url, "index.php")&&!stristr($url, "ctype=")) {
		$url.="&ctype=gedcom";
	}
}

if ($type=="full") print_header($pgv_lang["login_head"]);
else print_simple_header($pgv_lang["login_head"]);
print "<div class=\"center\">\n";

if ($_SESSION["cookie_login"]) {
	print "<div style=\"width:70%\" align=\"left\">\n";
	print_text("cookie_login_help");
	print "</div><br /><br />\n";
}
//if ($REQUIRE_AUTHENTICATION) {
if ($WELCOME_TEXT_AUTH_MODE!="0") {
	loadLangFile("pgv_help");
	print "<table class=\"center width60 ".$TEXT_DIRECTION."\"><tr><td>";
	if (empty($help_message) || !isset($help_message)) {
		switch ($WELCOME_TEXT_AUTH_MODE){
			case "1":
				$help_message = "welcome_text_auth_mode_1";
				print_text($help_message);
				break;
			case "2":
				$help_message = "welcome_text_auth_mode_2";
				print_text($help_message);
				break;
			case "3":
				$help_message = "welcome_text_auth_mode_3";
				print_text($help_message);
				break;
			case "4":
				if ($WELCOME_TEXT_CUST_HEAD == "true"){
					$help_message = "welcome_text_cust_head";
					print_text($help_message);
				}
				print print_text($WELCOME_TEXT_AUTH_MODE_4,0,2);
				break;
		}
	}
	else print_text($help_message);
	print "</td></tr></table><br /><br />\n";
}
else {
	if (!empty($help_message) || isset($help_message)) {
		loadLangFile("pgv_help");
		print "<table class=\"center width60 ltr\"><tr><td>";
		print_text($help_message);
		print "</td></tr></table><br /><br />\n";
	}
}
$tab=0;		// initialize tab index
	?>
	<form name="loginform" method="post" action="<?php print $LOGIN_URL; ?>" onsubmit="t = new Date(); document.loginform.usertime.value=t.getFullYear()+'-'+(t.getMonth()+1)+'-'+t.getDate()+' '+t.getHours()+':'+t.getMinutes()+':'+t.getSeconds(); return true;">
		<input type="hidden" name="action" value="login" />
		<input type="hidden" name="url" value="<?php print htmlentities($url,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="ged" value="<?php if (isset($ged)) print htmlentities($ged,ENT_COMPAT,'UTF-8'); else print htmlentities($GEDCOM,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="pid" value="<?php if (isset($pid)) print htmlentities($pid,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="type" value="<?php print htmlentities($type,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="usertime" value="" />
		<?php
		if (!empty($message)) print "<span class='error'><br /><b>$message</b><br /><br /></span>\r\n";
		?>
		<!--table-->
		<table class="center facts_table width50">
			<tr><td class="topbottombar" colspan="2"><?php print $pgv_lang["login"]; ?></td></tr>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php print_help_link("username_help", "qm", "username"); print $pgv_lang["username"]; ?></td>
				<td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" tabindex="<?php echo ++$tab; ?>" name="username" value="<?php print htmlentities($username,ENT_COMPAT,'UTF-8'); ?>" size="20" class="formField" /></td>
			</tr>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php print_help_link("password_help", "qm", "password"); print $pgv_lang["password"]; ?></td>
				<td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="password" tabindex="<?php echo ++$tab; ?>" name="password" size="20" class="formField" /></td>
			</tr>
			<?php if ($ALLOW_REMEMBER_ME) { ?>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php print_help_link("remember_me_help", "qm", "remember_me"); ?><label for="remember"><?php print $pgv_lang["remember_me"]; ?></label></td>
				<td class="optionbox <?php print $TEXT_DIRECTION; ?> "><input type="checkbox" tabindex="<?php echo ++$tab; ?>" id="remember" name="remember" value="yes" <?php if (!empty($_COOKIE["pgv_rem"])) print "checked=\"checked\""; ?> class="formField" /></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="topbottombar" colspan="2">
					<?php
					if ($SHOW_CONTEXT_HELP) {
						if ($REQUIRE_AUTHENTICATION) {
							print_help_link("login_buttons_aut_help", "qm", "login");
						} else {
							print_help_link("login_buttons_help", "qm", "login");
						}
					}
					?>
					<input type="submit" tabindex="<?php echo ++$tab; ?>" value="<?php print $pgv_lang["login"]; ?>" />&nbsp;
				</td>
			</tr>
		</table>
</form><br /><br />
<?php

$sessname = session_name();
if (!isset($_COOKIE[$sessname])) print "<center><div class=\"error width50\">".$pgv_lang["cookie_help"]."</div></center><br /><br />";

if ($USE_REGISTRATION_MODULE) { ?>
	<table class="center facts_table width50">
	<tr><td class="topbottombar" colspan="2"><?php print $pgv_lang["account_information"]; ?></td></tr>
	<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php print_help_link("new_user_help", "qm", "requestaccount"); print $pgv_lang["no_account_yet"]; ?></td>
	<td class="optionbox <?php print $TEXT_DIRECTION; ?> wrap"><a href="login_register.php?action=register"><?php print $pgv_lang["requestaccount"]; ?></a></td></tr>
	<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width50"><?php print_help_link("new_password_help", "qm", "lost_password"); print $pgv_lang["lost_password"]; ?></td>
	<td class="optionbox <?php print $TEXT_DIRECTION; ?> wrap"><a href="login_register.php?action=pwlost"><?php print $pgv_lang["requestpassword"]; ?></a></td></tr>
	<tr><td class="topbottombar ltr" colspan="2">&nbsp;</td></tr>
	</table>
<?php
}
print "</div><br /><br />";
?>
<script language="JavaScript" type="text/javascript">
	document.loginform.username.focus();
</script>
<?php
if ($type=="full") print_footer();
else print_simple_footer();
?>

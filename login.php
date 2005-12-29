<?php
/**
 * Login Page.
 *
 * Provides links for administrators to get to other administrative areas of the site
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
 * This Page Is Valid XHTML 1.0 Transitional! > 29 August 2005
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: login.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */ 

require "config.php";
$message="";
if (!isset($action)) {
	$action="";
	$username="";
	$password="";
}

if (!isset($type)) $type = "full";

if ($action=="login") {
	if (isset($_POST['username'])) $username = $_POST['username'];
	else $username="";
	if (isset($_POST['password'])) $password = $_POST['password'];
	else $password="";
	if (isset($_POST['remember'])) $remember = $_POST['remember'];
	else $remember = "no";
	$auth = authenticateUser($username, $password);
	if ($auth) {
		if (!empty($_POST["usertime"])) {
			$_SESSION["usertime"]=@strtotime($_POST["usertime"]);
		}
		else $_SESSION["usertime"]=time();
		$_SESSION["timediff"]=time()-$_SESSION["usertime"];
		$MyUserName = getUserName();
		$MyUser = $users[$MyUserName];
		if (isset($MyUser["language"])) {
		  if (isset($_SESSION['CLANGUAGE']))$_SESSION['CLANGUAGE'] = $MyUser["language"];
		  else if (isset($HTTP_SESSION_VARS['CLANGUAGE'])) $HTTP_SESSION_VARS['CLANGUAGE'] = $MyUser["language"];
		}
		session_write_close();
		$urlnew = $SERVER_URL;
		if (substr($urlnew,-1,1)!="/") $urlnew .= "/";
		$url = preg_replace("/logout=1/", "", $url);
		$url = $urlnew . $url;
		if ($remember=="yes") setcookie("pgv_rem", $username, time()+60*60*24*7);
		else setcookie("pgv_rem", "", time()-60*60*24*7);
		header("Location: $url");
		exit;
	}
	else $message = $pgv_lang["no_login"];
}
else {
	$tSERVER_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $SERVER_URL);
	$tLOGIN_URL = preg_replace(array("'https?://'", "'www.'", "'/$'"), array("","",""), $LOGIN_URL);
	if (empty($url)) {
		if ((isset($_SERVER['HTTP_REFERER'])) && ((stristr($_SERVER['HTTP_REFERER'],$tSERVER_URL)!==false)||(stristr($_SERVER['HTTP_REFERER'],$tLOGIN_URL)!==false))) {
			$url = basename($_SERVER['HTTP_REFERER']);
			if (stristr($url, ".php")===false) {
				$url = "index.php?command=gedcom&amp;ged=$GEDCOM";
			}
		}
		else {
			if (isset($url)) {
				if (stristr($url,$SERVER_URL)!==false) $url = $SERVER_URL;
			}
			else $url = $SERVER_URL;
		}
	}
	else if (stristr($url, "index.php")&&!stristr($url, "command=")) {
		$url.="&amp;command=gedcom";
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
if ($REQUIRE_AUTHENTICATION) {
	require $PGV_BASE_DIRECTORY.$helptextfile["english"];
	if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];
	print "<table class=\"center width60 ltr\"><tr><td>";
	if (empty($help_message) || !isset($help_message)) {
		if (!empty($GEDCOM)) require($INDEX_DIRECTORY.$GEDCOM."_conf.php");
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
				print $WELCOME_TEXT_AUTH_MODE_4;
				break;
		}
		print "</td></tr></table><br /><br />\n";
	}
	else print_text($help_message);
}
else {
	if (!empty($help_message) || isset($help_message)) {
		require $PGV_BASE_DIRECTORY.$helptextfile["english"];
		if (file_exists($PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$helptextfile[$LANGUAGE];
		print "<table class=\"center width60 ltr\"><tr><td>";
		print_text($help_message);
		print "</td></tr></table><br /><br />\n";
	}
}
	?>
	<form name="loginform" method="post" action="<?php print $LOGIN_URL; ?>" onsubmit="t = new Date(); document.loginform.usertime.value=t.getFullYear()+'-'+(t.getMonth()+1)+'-'+t.getDate()+' '+t.getHours()+':'+t.getMinutes()+':'+t.getSeconds(); return true;">
		<?php $i = 0;?>
		<input type="hidden" name="action" value="login" />
		<input type="hidden" name="url" value="<?php print $url; ?>" />
		<input type="hidden" name="ged" value="<?php if (isset($ged)) print $ged; else print $GEDCOM; ?>" />
		<input type="hidden" name="pid" value="<?php if (isset($pid)) print $pid; ?>" />
		<input type="hidden" name="type" value="<?php print $type; ?>" />
		<input type="hidden" name="usertime" value="" />
		<span class="error"><b><?php print $message?></b></span>
		<!--table-->
		<table class="center facts_table width20">
		  <tr><td class="topbottombar" colspan="2"><?php print $pgv_lang["login"]?></td></tr>
		  <tr>
		    <td class="descriptionbox width20 ltr"><?php print_help_link("username_help", "qm", "username"); print $pgv_lang["username"]?></td>
		    <td class="optionbox ltr"><input type="text" tabindex="<?php $i++; print $i?>" name="username" value="<?php print $username?>" size="20" class="formField" /></td>
		  </tr>
		  <tr>
		    <td class="descriptionbox ltr"><?php print_help_link("password_help", "qm", "password"); print $pgv_lang["password"]?></td>
		    <td class="optionbox ltr"><input type="password" tabindex="<?php $i++; print $i?>" name="password" size="20" class="formField" /></td>
		  </tr>
		  <?php if ($ALLOW_REMEMBER_ME) { ?>
		  <tr>
		  	<td class="descriptionbox ltr"><?php print_help_link("remember_me_help", "qm", "remember_me"); print $pgv_lang["remember_me"]?></td>
		    <td class="optionbox ltr "><input type="checkbox" tabindex="<?php $i++; print $i?>" name="remember" value="yes" <?php if (!empty($_COOKIE["pgv_rem"])) print "checked=\"checked\""; ?> class="formField" /></td>
		  </tr>
		  <?php } ?>
		  <tr>
		    <td class="topbottombar" colspan="2">
		    <?php
		        if ($SHOW_CONTEXT_HELP) {
		          if ($REQUIRE_AUTHENTICATION) {
		            print_help_link("login_buttons_aut_help", "qm", "login");
		          }
		          else {
		            print_help_link("login_buttons_help", "qm", "login");
		          }
		        }
		    ?>
		      <input type="submit" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["login"]; ?>" />&nbsp;
		      <input type="submit" tabindex="<?php $i++; print $i?>" value="<?php print $pgv_lang["admin"]; ?>" onclick="document.loginform.url.value='admin.php';" />
		    </td>
		  </tr>
		</table>
</form><br /><br />
<?php
$sessname = session_name();
if (!isset($_COOKIE[$sessname])) print "<span class=\"error\">".$pgv_lang["cookie_help"]."</span><br /><br />";
if ($USE_REGISTRATION_MODULE) {?>
	<table class="center facts_table width20">
	<tr><td class="topbottombar" colspan="2"><?php print $pgv_lang["account_information"];?></td></tr>
	<tr><td class="descriptionbox width20 ltr"><?php print_help_link("new_user_help", "qm", "requestaccount"); print $pgv_lang["no_account_yet"];?></td>
	<td class="optionbox ltr"><a href="login_register.php?action=register"><?php print $pgv_lang["requestaccount"];?></a></td></tr>
	<tr><td class="descriptionbox ltr"><?php print_help_link("new_password_help", "qm", "lost_password"); print $pgv_lang["lost_password"];?></td>
	<td class="optionbox ltr"><a href="login_register.php?action=pwlost"><?php print $pgv_lang["requestpassword"];?></a></td></tr>
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
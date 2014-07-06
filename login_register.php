<?php
/**
 * Register as a new User or request new password if it is lost
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Admin
 * @version $Id$
 */

require './config.php';

loadLangFile("pgv_confighelp");

$action         =safe_POST('action');
$user_firstname =safe_POST('user_firstname');
$user_lastname  =safe_POST('user_lastname');
$url            =safe_POST('url',             PGV_REGEX_URL, 'index.php');
$time           =safe_POST('time');
$user_name      =safe_POST('user_name',       PGV_REGEX_USERNAME);
$user_email     =safe_POST('user_email',      PGV_REGEX_EMAIL);
$user_password01=safe_POST('user_password01', PGV_REGEX_PASSWORD);
$user_password02=safe_POST('user_password02', PGV_REGEX_PASSWORD);
$user_language  =safe_POST('user_language');
$user_gedcomid  =safe_POST('user_gedcomid');
$user_comments  =safe_POST('user_comments');
$user_password  =safe_POST('user_password');
$user_hashcode  =safe_POST('user_hashcode');
if (empty($action)) $action = safe_GET('action');
if (empty($user_name)) $user_name = safe_GET('user_name', PGV_REGEX_USERNAME);
if (empty($user_hashcode)) $user_hashcode = safe_GET('user_hashcode');

// Remove trailing slash from server URL
if (substr($SERVER_URL, -1) == "/") $serverURL = substr($SERVER_URL,0, -1);
else $serverURL = $SERVER_URL;

$message="";

switch ($action) {
	case "pwlost" :
		print_header($pgv_lang['lost_pw_reset']);
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
			function checkform(frm) {
				/*
				if (frm.user_email.value == "") {
					alert("<?php print $pgv_lang["enter_email"]; ?>");
					frm.user_email.focus();
					return false;
				}
				*/
				return true;
			}
		//-->
		</script>
		<div class="center">
			<form name="requestpwform" action="login_register.php" method="post" onsubmit="t = new Date(); document.requestpwform.time.value=t.toUTCString(); return checkform(this);">
			<input type="hidden" name="time" value="" />
			<input type="hidden" name="action" value="requestpw" />
			<span class="warning"><?php print $message?></span>
			<table class="center facts_table width25">
				<tr><td class="topbottombar" colspan="2"><?php print_help_link("pls_note11", "qm", "lost_pw_reset"); print $pgv_lang["lost_pw_reset"];?></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print $pgv_lang["username"]?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_name" value="" /></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php print $pgv_lang["lost_pw_reset"]; ?>" /></td></tr>
			</table>
			</form>
		</div>
		<script language="JavaScript" type="text/javascript">
			document.requestpwform.user_name.focus();
		</script>
		<?php
		break;

	case "requestpw" :
		$QUERY_STRING = "";
		print_header($pgv_lang['lost_pw_reset']);
		print "<div class=\"center\">";
		if (!get_user_id($user_name)) {
			AddToLog("New password requests for user ".$user_name." that does not exist");
			print "<span class=\"warning\">";
			print_text("user_not_found");
			print "</span><br />";
		} else {
			if (get_user_setting($user_name, 'email')=='') {
				AddToLog("Unable to send password to user ".$user_name." because they do not have an email address");
				print "<span class=\"warning\">";
				print_text("user_not_found");
				print "</span><br />";
			} else {
				$passchars = "abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$user_new_pw = "";
				$max = strlen($passchars)-1;
				for($i=0; $i<8; $i++) {
					$index = rand(0,$max);
					$user_new_pw .= $passchars{$index};
				}

				set_user_password($user_name, crypt($user_new_pw));
				set_user_setting($user_name, 'pwrequested', 1);

				// switch language to user settings
				$oldLanguage = $LANGUAGE;
				if ($LANGUAGE != get_user_setting($user_name, 'language')) loadLanguage(get_user_setting($user_name, 'language'));
				$newuserName=getUserFullName($user_name);

				$mail_body = "";
				$mail_body .= str_replace("#user_fullname#", $newuserName, $pgv_lang["mail04_line01"]) . "\r\n\r\n";
				$mail_body .= $pgv_lang["mail04_line02"] . "\r\n\r\n";
				$mail_body .= $pgv_lang["username"] . ": " . $user_name . "\r\n";

				$mail_body .= $pgv_lang["password"] . ": " . $user_new_pw . "\r\n\r\n";
				$mail_body .= $pgv_lang["mail04_line03"] . "\r\n";
				$mail_body .= $pgv_lang["mail04_line04"] . "\r\n\r\n";
				$mail_body .= print_text("mail04_line05", 0, 1) . "\r\n\r\n";

				if ($TEXT_DIRECTION=="rtl") $mail_body .= "<a href=\"".$serverURL."\">".$serverURL."</a>";
				else $mail_body .= $serverURL;

				require_once('includes/functions/functions_mail.php');
				pgvMail(get_user_setting($user_name, 'email'), $PHPGEDVIEW_EMAIL, str_replace("#SERVER_NAME#", $serverURL, $pgv_lang["mail04_subject"]), $mail_body);

				?>
				<table class="center facts_table">
				<tr><td class="wrap <?php print $TEXT_DIRECTION; ?>"><?php print str_replace("#user[email]#", $user_name, $pgv_lang["pwreqinfo"]);?></td></tr>
				</table>
				<?php
				AddToLog("Password request was sent to user: ".$user_name);

				if ($LANGUAGE != $oldLanguage) loadLanguage($oldLanguage);   // Reset language
			}
		}
		print "</div>";
		break;

	case "register" :
		$_SESSION["good_to_send"] = true;
		if (!$USE_REGISTRATION_MODULE) {
		header("Location: index.php");
		exit;
	}
	$message = "";
		if (!$user_name) {
			$message .= $pgv_lang["enter_username"]."<br />";
			$user_name_false = true;
		}
		else $user_name_false = false;

		if (!$user_password01) {
			$message .= $pgv_lang["enter_password"]."<br />";
			$user_password01_false = true;
		}
		else $user_password01_false = false;

		if (!$user_password02) {
			$message .= $pgv_lang["confirm_password"]."<br />";
			$user_password02_false = true;
		}
		else $user_password02_false = false;

		if ($user_password01 != $user_password02) {
			$message .= $pgv_lang["password_mismatch"]."<br />";
			$password_mismatch = true;
		}
		else $password_mismatch = false;

		if (!$user_firstname) $user_firstname_false = true;
		else $user_firstname_false = false;

		if (!$user_lastname) $user_lastname_false = true;
		else $user_lastname_false = false;

		if (!$user_email) $user_email_false = true;
		else $user_email_false = false;

		if (!$user_language) $user_language_false = true;
		else $user_language_false = false;

		if (!$user_comments) $user_comments_false = true;
		else $user_comments_false = false;

		if ($user_name_false == false && $user_password01_false == false && $user_password02_false == false && $user_firstname_false == false && $user_lastname_false == false && $user_email_false == false && $user_language_false == false && $user_comments_false == false && $password_mismatch == false) $action = "registernew";
		else {
			print_header($pgv_lang['requestaccount']);
			// Empty user array in case any details might be left
			// and faulty users are requested and created
			$user = array();

			?>
			<script language="JavaScript" type="text/javascript">
			<!--
				function checkform(frm) {
					if (frm.user_name.value == "") {
						alert("<?php print $pgv_lang["enter_username"]; ?>");
						frm.user_name.focus();
						return false;
					}
					if (frm.user_password01.value == "") {
						alert("<?php print $pgv_lang["enter_password"]; ?>");
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_password02.value == "") {
						alert("<?php print $pgv_lang["confirm_password"]; ?>");
						frm.user_password02.focus();
						return false;
					}
					if (frm.user_password01.value != frm.user_password02.value) {
						alert("<?php print $pgv_lang["password_mismatch"]; ?>");
						frm.user_password01.value = "";
						frm.user_password02.value = "";
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_password01.value.length < 6) {
						alert("<?php print $pgv_lang["passwordlength"]; ?>");
						frm.user_password01.value = "";
						frm.user_password02.value = "";
						frm.user_password01.focus();
						return false;
					}
					if (frm.user_firstname.value == "") {
						alert("<?php print $pgv_lang["enter_fullname"]; ?>");
						frm.user_firstname.focus();
						return false;
					}
					if (frm.user_lastname.value == "") {
						alert("<?php print $pgv_lang["enter_fullname"]; ?>");
						frm.user_lastname.focus();
						return false;
					}
					if ((frm.user_email.value == "")||(frm.user_email.value.indexOf('@')==-1)) {
						alert("<?php print $pgv_lang["enter_email"]; ?>");
						frm.user_email.focus();
						return false;
					}
					if (frm.user_comments.value == "") {
						alert("<?php print $pgv_lang["enter_comments"]; ?>");
						frm.user_comments.focus();
						return false;
					}
					return true;
				}

			var pastefield;
			function paste_id(value) {
				pastefield.value=value;
			}
			//-->
			</script>
			<?php
				if ($SHOW_REGISTER_CAUTION) {
					print "<center><table class=\"width50 ".$TEXT_DIRECTION."\"><tr><td>";
					print_text("acceptable_use");
					print "<br />";
					print "</td></tr></table></center>";
				}
			?>
			<div class="center">
				<form name="registerform" method="post" action="login_register.php" onsubmit="t = new Date(); document.registerform.time.value=t.toUTCString(); return checkform(this);">
					<input type="hidden" name="action" value="register" />
					<input type="hidden" name="time" value="" />
					<table class="center facts_table width50">
					<?php $i = 1;?>
						<tr><td class="topbottombar" colspan="2"><?php print_help_link("register_info_0".$WELCOME_TEXT_AUTH_MODE."", "qm", "requestaccount"); print $pgv_lang["requestaccount"];?><?php if (strlen($message) > 0) print $message; ?></td></tr>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("new_user_firstname_help", "qm", "firstname");print $pgv_lang["firstname"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_firstname" value="<?php if (!$user_firstname_false) print $user_firstname;?>" tabindex="<?php print $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("new_user_lastname_help", "qm", "lastname");print $pgv_lang["lastname"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_lastname" value="<?php if (!$user_lastname_false) print $user_lastname;?>" tabindex="<?php print $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("edituser_email_help", "qm", "emailadress");print $pgv_lang["emailadress"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" size="30" name="user_email" value="<?php if (!$user_email_false) print $user_email;?>" tabindex="<?php print $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("username_help", "qm", "username"); print $pgv_lang["choose_username"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_name" value="<?php if (!$user_name_false) print $user_name;?>" tabindex="<?php print $i;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("edituser_password_help", "qm", "password"); print $pgv_lang["choose_password"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="password" name="user_password01" value="" tabindex="<?php print $i++;?>" /> *</td></tr>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("edituser_conf_password_help", "qm", "confirm");print $pgv_lang["confirm"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="password" name="user_password02" value="" tabindex="<?php print $i++;?>" /> *</td></tr>
						<?php
						if ($ENABLE_MULTI_LANGUAGE) {
							print "<tr><td class=\"descriptionbox wrap ".$TEXT_DIRECTION."\">";
							print_help_link("edituser_change_lang_help", "qm", "change_lang");
							print $pgv_lang["change_lang"];
							print "</td><td class=\"optionbox ".$TEXT_DIRECTION."\"><select name=\"user_language\" tabindex=\"".($i++)."\">";
							foreach ($pgv_language as $key => $value) {
								if ($language_settings[$key]["pgv_lang_use"]) {
									print "\n\t\t\t<option value=\"$key\"";
									if (!$user_language_false) print " selected=\"selected\"";
									else if ($key == $LANGUAGE) print " selected=\"selected\"";
									print ">" . $pgv_lang[$key] . "</option>";
								}
							}
							print "</select>\n\t\t";
							print "</td></tr>\n";
						} else print "<input type=\"hidden\" name=\"user_language\" value=\"".$LANGUAGE."\" />";
						?>
						<?php if ($REQUIRE_AUTHENTICATION && $SHOW_LIVING_NAMES>=$PRIV_PUBLIC) { ?>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("register_gedcomid_help", "qm", "gedcomid");print $pgv_lang["gedcomid"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>" valign="top" ><input type="text" size="10" name="user_gedcomid" id="user_gedcomid" value="" tabindex="<?php print $i++;?>" /><?php print_findindi_link("user_gedcomid",""); ?></td></tr>
						<?php } ?>
						<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("register_comments_help", "qm", "comments");print $pgv_lang["comments"];?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>" valign="top" ><textarea cols="50" rows="5" name="user_comments" tabindex="<?php print $i++;?>"><?php if (!$user_comments_false) print $user_comments;?></textarea> *</td></tr>
						<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php print $pgv_lang["requestaccount"]; ?>" tabindex="<?php print $i++;?>" /></td></tr>
						<tr><td align="left" colspan="2" ><?php print $pgv_lang["mandatory"];?></td></tr>
					</table>
				</form>
			</div>
			<script language="JavaScript" type="text/javascript">
				document.registerform.user_name.focus();
			</script>
			<?php
			break;
		}

	case "registernew" :
		if (!$USE_REGISTRATION_MODULE) {
			header("Location: index.php");
			exit;
		}
		if (preg_match("/SUNTZU/i", $user_name) || preg_match("/SUNTZU/i", $user_email)) {
			AddToLog("SUNTZU hacker");
			print "Go Away!";
			exit;
		}

			//-- check referer for possible spam attack
			if (!isset($_SERVER['HTTP_REFERER']) || stristr($_SERVER['HTTP_REFERER'],"login_register.php")===false) {
				print "<center><br /><span class=\"error\">Invalid page referer.</span>\n";
				print "<br /><br /></center>";
				AddToLog('Invalid page referer while trying to register a user.  Possible spam attack.');
				exit;
			}

			if ((!isset($_SESSION["good_to_send"]))||($_SESSION["good_to_send"]!==true)) {
				AddToLog('Invalid session reference while trying to register a user.  Possible spam attack.');
				exit;
			}
			$_SESSION["good_to_send"] = false;

		$QUERY_STRING = "";
		if (isset($user_name)) {
		print_header($pgv_lang['registernew']);
			print "<div class=\"center\">";
			$alphabet = getAlphabet();
			$alphabet .= "_-. ";
			$i = 1;
			$pass = TRUE;
			while (strlen($user_name) > $i) {
				if (stristr($alphabet, $user_name{$i}) != TRUE) {
					$pass = FALSE;
					break;
				}
				$i++;
			}
			if ($pass == TRUE) {
				$user_created_ok = false;

				AddToLog("User registration requested for: ".$user_name);

				if (get_user_id($user_name)) {
					print "<span class=\"warning\">".print_text("duplicate_username",0,1)."</span><br /><br />";
					print "<a href=\"javascript:history.back()\">".$pgv_lang["back"]."</a><br />";
				}
				else if ($user_password01 == $user_password02) {
					if ($user_id=create_user($user_name, crypt($user_password01))) {
						set_user_setting($user_id, 'firstname',           $user_firstname);
						set_user_setting($user_id, 'lastname',            $user_lastname);
						set_user_setting($user_id, 'email',               $user_email);
						set_user_setting($user_id, 'language',            $user_language);
						set_user_setting($user_id, 'verified',            'no');
						set_user_setting($user_id, 'verified_by_admin',    $REQUIRE_ADMIN_AUTH_REGISTRATION ? 'no' : 'yes');
						set_user_setting($user_id, 'reg_timestamp',        date('U'));
						set_user_setting($user_id, 'reg_hashcode',         md5(crypt($user_name)));
						set_user_setting($user_id, 'contactmethod',        "messaging2");
						set_user_setting($user_id, 'defaulttab',           $GEDCOM_DEFAULT_TAB);
						set_user_setting($user_id, 'visibleonline',        'Y');
						set_user_setting($user_id, 'editaccount',          'Y');
						set_user_setting($user_id, 'relationship_privacy', $USE_RELATIONSHIP_PRIVACY ? 'Y' : 'N');
						set_user_setting($user_id, 'max_relation_path',    $MAX_RELATION_PATH_LENGTH);
						set_user_setting($user_id, 'auto_accept',          'N');
						set_user_setting($user_id, 'canadmin',             'N');
						set_user_setting($user_id, 'sync_gedcom',          'N');
						set_user_setting($user_id, 'loggedin',             'N');
						set_user_setting($user_id, 'sessiontime',          '0');
						if (!empty($user_gedcomid)) {
							set_user_gedcom_setting($user_id, $GEDCOM, 'gedcomid', $user_gedcomid);
							set_user_gedcom_setting($user_id, $GEDCOM, 'rootid',   $user_gedcomid);
						}
						$user_created_ok = true;
					} else {
						print "<span class=\"warning\">".print_text("user_create_error",0,1)."<br /></span>";
						print "<a href=\"javascript:history.back()\">".$pgv_lang["back"]."</a><br />";
					}
				} else {
					print "<span class=\"warning\">".print_text("password_mismatch",0,1)."</span><br />";
					print "<a href=\"javascript:history.back()\">".$pgv_lang["back"]."</a><br />";
				}
				if ($user_created_ok) {
					// switch to the user's language
					$oldLanguage = $LANGUAGE;
					if ($LANGUAGE != $user_language) loadLanguage($user_language);

 					if ($NAME_REVERSE) $fullName = $user_lastname." ".$user_firstname;
					else $fullName = $user_firstname." ".$user_lastname;

					$mail_body = "";
					$mail_body .= str_replace("#user_fullname#", $fullName, $pgv_lang["mail01_line01"]) . "\r\n\r\n";
					$mail_body .= str_replace("#user_email#", $user_email, str_replace("#SERVER_NAME#", $serverURL, $pgv_lang["mail01_line02"])) . "  ";
					$mail_body .= $pgv_lang["mail01_line03"] . "\r\n\r\n";
					$mail_body .= $pgv_lang["mail01_line04"] . "\r\n\r\n";
					if ($TEXT_DIRECTION=="rtl") {
						$mail_body .= "<a href=\"";
						$mail_body .= $serverURL . "/login_register.php?user_name=".urlencode($user_name)."&user_hashcode=".urlencode(get_user_setting($user_name, 'reg_hashcode'))."&action=userverify\">";
					}
					$mail_body .= $serverURL . "/login_register.php?user_name=".urlencode($user_name)."&user_hashcode=".urlencode(get_user_setting($user_name, 'reg_hashcode'))."&action=userverify";
					if ($TEXT_DIRECTION=="rtl") $mail_body .= "</a>";
					$mail_body .= "\r\n";
					$mail_body .= $pgv_lang["username"] . " " . $user_name . "\r\n";
					$mail_body .= $pgv_lang["hashcode"] . " " . get_user_setting($user_name, 'reg_hashcode') . "\r\n\r\n";
					$mail_body .= $pgv_lang["comments"].": " . $user_comments . "\r\n\r\n";
					$mail_body .= $pgv_lang["mail01_line05"] . "  ";
					$mail_body .= $pgv_lang["mail01_line06"] . "\r\n";
					require_once('includes/functions/functions_mail.php');
					pgvMail($user_email, $PHPGEDVIEW_EMAIL, str_replace("#SERVER_NAME#", $serverURL, $pgv_lang["mail01_subject"]), $mail_body);

					// switch language to webmaster settings
					$adm_lang=get_user_setting($WEBMASTER_EMAIL, 'language');
					if ($adm_lang && $LANGUAGE!=$adm_lang) loadLanguage($adm_lang);

					$mail_body = "";
					$mail_body .= $pgv_lang["mail02_line01"] . "\r\n\r\n";
					$mail_body .= str_replace("#SERVER_NAME#", $serverURL, $pgv_lang["mail02_line02"]) . "\r\n\r\n";
					$mail_body .= $pgv_lang["username"] . " " . $user_name . "\r\n";
					if ($NAME_REVERSE) {
						$mail_body .= $pgv_lang["lastname"] . " " . $user_lastname . "\r\n\r\n";
						$mail_body .= $pgv_lang["firstname"] . " " . $user_firstname . "\r\n";
					} else {
						$mail_body .= $pgv_lang["firstname"] . " " . $user_firstname . "\r\n";
						$mail_body .= $pgv_lang["lastname"] . " " . $user_lastname . "\r\n\r\n";
					}
					$mail_body .= $pgv_lang["comments"].": " . $user_comments . "\r\n\r\n";
					$mail_body .= $pgv_lang["mail02_line03"] . "\r\n\r\n";
					if ($REQUIRE_ADMIN_AUTH_REGISTRATION) $mail_body .= $pgv_lang["mail02_line04"] . "\r\n";
					else $mail_body .= $pgv_lang["mail02_line04a"] . "\r\n";

					$message = array();
					$message["to"]=$WEBMASTER_EMAIL;
					$message["from"]=$user_email;
					$message["subject"] = str_replace("#SERVER_NAME#", $serverURL, str_replace("#user_email#", $user_email, $pgv_lang["mail02_subject"]));
					$message["body"] = $mail_body;
					$message["created"] = $time;
					$message["method"] = $SUPPORT_METHOD;
					$message["no_from"] = true;
					addMessage($message);

					// switch language to user's settings
					if ($LANGUAGE != $user_language) loadLanguage($user_language);
					?>
					<table class="center facts_table">
						<tr><td class="wrap <?php print $TEXT_DIRECTION; ?>"><?php print str_replace("#user_fullname#", $user_firstname." ".$user_lastname, $pgv_lang["thankyou"]);?><br /><br />
						<?php
						if ($REQUIRE_ADMIN_AUTH_REGISTRATION) print str_replace("#user_email#", $user_email, $pgv_lang["pls_note06"]);
						else print str_replace("#user_email#", $user_email, $pgv_lang["pls_note06a"]);
						?>
						</td></tr>
					</table>
					<?php
					if ($LANGUAGE != $oldLanguage) loadLanguage($oldLanguage);		// Reset language
				}
				print "</div>";
			} else {
				print "<span class=\"error\">".print_text("invalid_username",0,1)."</span><br />";
				print "<a href=\"javascript:history.back()\">".$pgv_lang["back"]."</a><br />";
			}
		} else {
			header("Location: login.php");
			exit;
		}
		break;

	case "userverify" :
		if (!$USE_REGISTRATION_MODULE) {
			header("Location: index.php");
			exit;
		}

		// Change to the new user's language
		$oldLanguage = $LANGUAGE;
		$user_lang=get_user_setting($user_name, 'language');
		if ($user_lang && $LANGUAGE!=$user_lang) loadLanguage($user_lang);

		print_header($pgv_lang['user_verify']);
		print "<div class=\"center\">";
		?>
		<form name="verifyform" method="post" action="" onsubmit="t = new Date(); document.verifyform.time.value=t.toUTCString();">
			<input type="hidden" name="action" value="verify_hash" />
			<input type="hidden" name="time" value="" />
			<table class="center facts_table width25">
				<tr><td class="topbottombar" colspan="2"><?php print_help_link("pls_note07", "qm", "user_verify"); print $pgv_lang["user_verify"];?></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print $pgv_lang["username"]; ?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_name" value="<?php print $user_name; ?>" /></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print $pgv_lang["password"]; ?></td><td class="optionbox <?php print $TEXT_DIRECTION; ?>"><input type="password" name="user_password" value="" /></td></tr>
				<tr><td class="descriptionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print $pgv_lang["hashcode"]; ?></td><td class="facts_value <?php print $TEXT_DIRECTION; ?>"><input type="text" name="user_hashcode" value="<?php print $user_hashcode; ?>" /></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="<?php print $pgv_lang["send"]; ?>" /></td></tr>
			</table>
		</form>
		</div>
		<script language="JavaScript" type="text/javascript">
			document.verifyform.user_name.focus();
		</script>
		<?php
		break;

	case "verify_hash" :
		if (!$USE_REGISTRATION_MODULE) {
			header("Location: index.php");
			exit;
		}
		$QUERY_STRING = "";
		AddToLog("User attempted to verify hashcode: ".$user_name);

		// Change to the new user's language
		$user_lang=get_user_setting($user_name, 'language');
		if ($user_lang && $LANGUAGE!=$user_lang) loadLanguage($user_lang);
		$oldLanguage = $LANGUAGE;

		print_header($pgv_lang['user_verify']); // <-- better verification of authentication code
		print "<div class=\"center\">";
		print "<table class=\"center facts_table wrap ".$TEXT_DIRECTION."\">";
		print "<tr><td class=\"topbottombar\">".$pgv_lang["user_verify"]."</td></tr>";
		print "<tr><td class=\"optionbox\">";
		print str_replace("#user_name#", $user_name, $pgv_lang["pls_note08"]);
		if (get_user_id($user_name)) {
			$pw_ok = (get_user_password($user_name) == crypt($user_password, get_user_password($user_name)));
			$hc_ok = (get_user_setting($user_name, 'reg_hashcode') == $user_hashcode);
			if (($pw_ok) && ($hc_ok)) {
				set_user_setting($user_name, 'verified', 'yes');
				set_user_setting($user_name, 'pwrequested', '');
				set_user_setting($user_name, 'reg_timestamp', date("U"));
				set_user_setting($user_name, 'reg_hashcode', '');
				if (!$REQUIRE_ADMIN_AUTH_REGISTRATION) {
					set_user_setting($user_name, 'verified_by_admin', 'yes');
				}
				AddToLog("User verified: ".$user_name);

				// switch language to webmaster settings
				$adm_lang=get_user_setting($WEBMASTER_EMAIL, 'language');
				if ($adm_lang && $LANGUAGE!=$adm_lang) loadLanguage($adm_lang);

				$mail_body = "";
				$mail_body .= $pgv_lang["mail03_line01"] . "\r\n\r\n";
				$mail_body .= str_replace(array("#newuser[username]#", "#newuser[fullname]#"), array($user_name, getUserFullName($user_name)), $pgv_lang["mail03_line02"]) . "\r\n\r\n";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) $mail_body .= $pgv_lang["mail03_line03"] . "\r\n";
				else $mail_body .= $pgv_lang["mail03_line03a"] . "\r\n";

				$path = substr($SCRIPT_NAME, 0, strrpos($SCRIPT_NAME, "/"));
				if ($TEXT_DIRECTION=="rtl") {
					$mail_body .= "<a href=\"";
					$mail_body .= "http://".$_SERVER['SERVER_NAME'] . $path."/useradmin.php?action=edituser&username=" . urlencode($user_name) . "\">";
				}
				$mail_body .= "http://".$_SERVER['SERVER_NAME'] . $path."/useradmin.php?action=edituser&username=" . urlencode($user_name);
				if ($TEXT_DIRECTION=="rtl") $mail_body .= "</a>";
				$mail_body .= "\r\n";

				$message = array();
				$message["to"]=$WEBMASTER_EMAIL;
				$message["from"]=$PHPGEDVIEW_EMAIL;
				$message["subject"] = str_replace("#SERVER_NAME#", $serverURL, $pgv_lang["mail03_subject"]);
				$message["body"] = $mail_body;
				$message["created"] = $time;
				$message["method"] = $SUPPORT_METHOD;
				$message["no_from"] = true;
				addMessage($message);

				if ($LANGUAGE != $oldLanguage) loadLanguage($oldLanguage);		// Reset language

				print "<br /><br />".$pgv_lang["pls_note09"]."<br /><br />";
				if ($REQUIRE_ADMIN_AUTH_REGISTRATION) print $pgv_lang["pls_note10"];
				else print $pgv_lang["pls_note10a"];
				print "<br /><br /></td></tr>";
			} else {
				print "<br /><br />";
				print "<span class=\"warning\">";
				print $pgv_lang["data_incorrect"];
				print "</span><br /><br /></td></tr>";
			}
		} else {
			print "<br /><br />";
			print "<span class=\"warning\">";
			print $pgv_lang["user_not_found"];
			print "</span><br /><br /></td></tr>";
		}
		print "</table>";
		print "</div>";
		break;

	default :
		header("Location: ".encode_url($url));
		break;
}

print_footer();
?>

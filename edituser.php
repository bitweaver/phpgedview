<?php
/**
 * Administrative User Interface.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * This Page Is Valid XHTML 1.0 Transitional! > 19 August 2005
 *
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: edituser.php,v 1.3 2006/10/28 20:17:04 lsces Exp $
 */

/**
 * load the configuration and create the context
 */
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require "config.php";
require $confighelpfile["english"];
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];
require_once("includes/functions_print_lists.php");

if (!isset($action)) $action="";
if (isset($firstname)) $firstname = stripslashes($firstname);
if (isset($lastname)) $lastname = stripslashes($lastname);

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)||$_SESSION["cookie_login"]) {
	header("Location: login.php?url=edituser.php");
	exit;
}
$user = getUser($uname);
if (!isset($user["default_tab"])) $user["default_tab"]=0;
//-- prevent users with editing account disabled from being able to edit their account
if (!$user["editaccount"]) {
	header("Location: index.php?command=user");
	exit;
}
print_header("PhpGedView ".$pgv_lang["user_admin"]);
print "<div class=\"center\">\n";

//-- section to update a user by first deleting them
//-- and then adding them again
if ($action=="edituser2") {
	if (($username!=$oldusername)&&(getUser($username)!==false)) {
		print "<span class=\"error\">".$pgv_lang["duplicate_username"]."</span><br />";
	}
	else if ($pass1==$pass2) {
		$alphabet = getAlphabet();
		$alphabet .= "_-. ";
		$i = 1;
		$pass = TRUE;
		while (strlen($username) > $i) {
			if (stristr($alphabet, $username{$i}) != TRUE){
				$pass = FALSE;
				break;
			}
			$i++;
		}
		if ($pass) {
			$sync_data_changed = false;
			$newuser = array();
			$olduser = getUser($oldusername);
			$newuser = $olduser;
			if (empty($pass1)) $newuser["password"]=$olduser["password"];
			else $newuser["password"]=crypt($pass1);
			//deleteUser($oldusername, "changed");
			$newuser["username"]=$username;
			$newuser["firstname"]=$firstname;
			$newuser["lastname"]=$lastname;
			$newuser["rootid"][$GEDCOM] = $rootid;
			if (isset($user_language)) $newuser["language"]=$user_language;
			if ($olduser["email"]!=$user_email) $sync_data_changed = true;
			$newuser["email"] = $user_email;
			if (isset($user_theme)) $newuser["theme"] = $user_theme;
			if (isset($new_contact_method)) $newuser["contactmethod"] = $new_contact_method;
			if ((isset($new_visibleonline))&&($new_visibleonline=='yes')) $newuser["visibleonline"] = true;
			else $newuser["visibleonline"] = false;
			if (isset($new_default_tab)) $newuser["default_tab"] = $new_default_tab;
			updateUser($oldusername, $newuser, "changed");
			//-- if the username changed update the user's session
			if ($oldusername == getUserName()) $_SESSION['pgv_user'] = $newuser["username"];
			$user = $newuser;
			
			//-- update Gedcom record with new email address
			if ($user["sync_gedcom"]=="Y" && $sync_data_changed) {
				$oldged = $GEDCOM;
				foreach($user["gedcomid"] as $gedc=>$gedid) {
					if (!empty($gedid)) {
						include_once("includes/functions_edit.php");
						$GEDCOM = $gedc;
						$indirec = find_person_record($gedid);
						if (!empty($indirec)) {
							if (preg_match("/\d _?EMAIL/", $indirec)>0) {
								$indirec = preg_replace("/(\d _?EMAIL)[^\r\n]*/", "$1 ".$user["email"], $indirec);
								replace_gedrec($gedid, $indirec);
							}
							else {
								$indirec .= "\r\n1 EMAIL ".$user["email"];
								replace_gedrec($gedid, $indirec);
							}
						}
					}
				}
				$GEDCOM = $oldged;
			}
		}
		else {
			print "<span class=\"error\">".$pgv_lang["invalid_username"]."</span><br />";
		}
	}
	else {
		print "<span class=\"error\">".$pgv_lang["password_mismatch"]."</span><br />";
		$action="edituser";
	}
}
//-- print the form to edit a user
?>
<script language="JavaScript" type="text/javascript">
	function checkform(frm) {
		if (frm.username.value=="") {
			alert("<?php print $pgv_lang["enter_username"]; ?>");
			frm.username.focus();
			return false;
		}
		if (frm.firstname.value=="") {
			alert("<?php print $pgv_lang["enter_fullname"]; ?>");
			frm.firstname.focus();
			return false;
		}
		if (frm.lastname.value=="") {
			alert("<?php print $pgv_lang["enter_fullname"]; ?>");
			frm.lastname.focus();
			return false;
		}
		if ((frm.user_email.value=="")||(frm.user_email.value.indexOf("@")==-1)) {
			alert("<?php print $pgv_lang["enter_email"]; ?>");
			frm.user_email.focus();
			return false;
		}
		return true;
	}
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
</script>
<form name="editform" method="post" action="" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="edituser2" />
<input type="hidden" name="oldusername" value="<?php print $uname; ?>" />
<?php $tab=0; ?>
<table class="list_table <?php print $TEXT_DIRECTION; ?>">
	<tr><td class="topbottombar" colspan="2"><h2><?php print $pgv_lang["editowndata"];?></h2></td></tr>
	<tr><td class="descriptionbox width20 wrap"><?php print_help_link("edituser_username_help", "qm");print $pgv_lang["username"];?></td><td class="optionbox"><input type="text" name="username" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user['username']?>" /></td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_firstname_help", "qm");print $pgv_lang["firstname"];?></td><td class="optionbox"><input type="text" name="firstname" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user['firstname']?>" /></td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_lastname_help", "qm");print $pgv_lang["lastname"];?></td><td class="optionbox"><input type="text" name="lastname" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user['lastname']?>" /></td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_gedcomid_help", "qm");print $pgv_lang["gedcomid"];?></td><td class="optionbox">
		<?php
			if (!empty($user['gedcomid'][$GEDCOM])) {
				print "<ul>";
				print_list_person($user['gedcomid'][$GEDCOM], array(get_person_name($user['gedcomid'][$GEDCOM]), $GEDCOM));
				print "</ul>";
			}
			else print "&nbsp;";
		?>
	</td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_rootid_help", "qm"); print $pgv_lang["rootid"];?></td><td class="optionbox"><input type="text" name="rootid" id="rootid" tabindex="<?php $tab++; print $tab; ?>" value="<?php if (isset($user['rootid'][$GEDCOM])) print $user['rootid'][$GEDCOM]; ?>" />
	<?php print_findindi_link("rootid",""); ?>
	</td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_password_help", "qm"); print $pgv_lang["password"];?></td><td class="optionbox"><input type="password" name="pass1" tabindex="<?php $tab++; print $tab; ?>" /><br /><?php print $pgv_lang["leave_blank"];?></td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_conf_password_help", "qm"); print $pgv_lang["confirm"];?></td><td class="optionbox"><input type="password" name="pass2" tabindex="<?php $tab++; print $tab; ?>" /></td></tr>
	<tr><td class="descriptionbox wrap"><?php print_help_link("edituser_change_lang_help", "qm"); print $pgv_lang["change_lang"];?></td><td class="optionbox" valign="top"><?php
	if ($ENABLE_MULTI_LANGUAGE) {
		$tab++;
		print "<select name=\"user_language\" tabindex=\"".$tab."\" style=\"{ font-size: 9pt; }\">";
		foreach ($pgv_language as $key => $value) {
			if ($language_settings[$key]["pgv_lang_use"]) {
				print "\n\t\t\t<option value=\"$key\"";
				if ($key == $user["language"]) print " selected=\"selected\"";
				print ">" . $pgv_lang[$key] . "</option>";
			}
		}
		print "</select>\n\t\t";
	}
	else print "&nbsp;";
    ?></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("edituser_email_help", "qm"); print $pgv_lang["emailadress"];?></td><td class="optionbox" valign="top"><input type="text" name="user_email" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user["email"]; ?>" size="50" /></td></tr>
    <?php if ($ALLOW_USER_THEMES) { ?>
    <tr><td class="descriptionbox wrap"><?php print_help_link("edituser_user_theme_help", "qm"); print $pgv_lang["user_theme"];?></td><td class="optionbox" valign="top">
    	<select name="user_theme" tabindex="<?php $tab++; print $tab; ?>">
    	<option value=""><?php print $pgv_lang["site_default"]; ?></option>
				<?php
					$themes = get_theme_names();
					foreach($themes as $indexval => $themedir) {
						print "<option value=\"".$themedir["dir"]."\"";
						if ($themedir["dir"] == $user["theme"]) print " selected=\"selected\"";
						print ">".$themedir["name"]."</option>\n";
					}
				?>
			</select>
	</td></tr>
	<?php } ?>
	<tr>
		<td class="descriptionbox wrap"><?php print_help_link("edituser_user_contact_help", "qm"); print $pgv_lang["user_contact_method"];?></td>
		<td class="optionbox"><select name="new_contact_method" tabindex="<?php $tab++; print $tab; ?>">
		<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging" <?php if ($user['contactmethod']=='messaging') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging"];?></option>
				<option value="messaging2" <?php if ($user['contactmethod']=='messaging2') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging2"];?></option>
		<?php } else { ?>
				<option value="messaging3" <?php if ($user['contactmethod']=='messaging3') print "selected=\"selected\""; ?>><?php print $pgv_lang["messaging3"];?></option>
		<?php } ?>
				<option value="mailto" <?php if ($user['contactmethod']=='mailto') print "selected=\"selected\""; ?>><?php print $pgv_lang["mailto"];?></option>
				<option value="none" <?php if ($user['contactmethod']=='none') print "selected=\"selected\""; ?>><?php print $pgv_lang["no_messaging"];?></option>
			</select>
		</td>
	</tr>
	<tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_visibleonline_help", "qm"); print $pgv_lang["visibleonline"];?></td>
      <td class="optionbox"><input type="checkbox" name="new_visibleonline" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php if ($user['visibleonline']) print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
		<td class="descriptionbox wrap"><?php print_help_link("edituser_user_default_tab_help", "qm"); print $pgv_lang["user_default_tab"];?></td>
		<td class="optionbox"><select name="new_default_tab" tabindex="<?php $tab++; print $tab; ?>">
				<option value="0" <?php if ($user['default_tab']==0) print "selected=\"selected\""; ?>><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1" <?php if ($user['default_tab']==1) print "selected=\"selected\""; ?>><?php print $pgv_lang["notes"];?></option>
				<option value="2" <?php if ($user['default_tab']==2) print "selected=\"selected\""; ?>><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3" <?php if ($user['default_tab']==3) print "selected=\"selected\""; ?>><?php print $pgv_lang["media"];?></option>
				<option value="4" <?php if ($user['default_tab']==4) print "selected=\"selected\""; ?>><?php print $pgv_lang["relatives"];?></option>
				<option value="-1" <?php if ($user['default_tab']==-1) print "selected=\"selected\""; ?>><?php print $pgv_lang["all"];?></option>
			</select>
		</td>
	</tr>
	<tr><td class="topbottombar" colspan="2"><input type="submit" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $pgv_lang["update_myaccount"]; ?>" /></td></tr>
</table>
</form><br />
</div>
<?php
print_footer();
?>
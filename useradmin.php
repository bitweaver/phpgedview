<?php
/**
 * Administrative User Interface.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 30 August 2005
 *
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: useradmin.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

/**
 * load configuration and context
 */
require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
global $TEXT_DIRECTION;
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
include "includes/functions_edit.php";

// Remove slashes
if (isset($ufirstname)) $ufirstname = stripslashes($ufirstname);
if (isset($ulastname)) $ulastname = stripslashes($ulastname);

if (!isset($action)) $action="";
if (!isset($filter)) $filter="";
if (!isset($sort)) $sort="";
if (!isset($ged)) $ged="";
if (!isset($usrlang)) $usrlang="";

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (!userIsAdmin(getUserName())) {
	header("Location: login.php?url=useradmin.php");
	exit;
}
print_header("PhpGedView ".$pgv_lang["user_admin"]);

// Javascript for edit form
?>
<script language="JavaScript" type="text/javascript">
<!--
	function checkform(frm) {
		if (frm.uusername.value=="") {
			alert("<?php print $pgv_lang["enter_username"]; ?>");
			frm.uusername.focus();
			return false;
		}
		if (frm.ufirstname.value=="") {
			alert("<?php print $pgv_lang["enter_fullname"]; ?>");
			frm.ufirstname.focus();
			return false;
		}
	    if ((frm.pass1.value!="")&&(frm.pass1.value.length < 6)) {
	      alert("<?php print $pgv_lang["passwordlength"]; ?>");
	      frm.pass1.value = "";
	      frm.pass2.value = "";
	      frm.pass1.focus();
	      return false;
	    }
		if ((frm.emailadress.value!="")&&(frm.emailadress.value.indexOf("@")==-1)) {
			alert("<?php print $pgv_lang["enter_email"]; ?>");
			frm.emailadress.focus();
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
//-- section to create a new user
// NOTE: No table parts
if ($action=="createuser") {
	$alphabet = getAlphabet();
	$alphabet .= "_-. ";
	$i = 1;
	$pass = TRUE;
	while (strlen($uusername) > $i) {
		if (stristr($alphabet, $uusername{$i}) != TRUE){
			$pass = FALSE;
			break;
		}
		$i++;
	}
	if ($pass == TRUE){
		if (getUser($uusername)!==false) {
			print "<span class=\"error\">".$pgv_lang["duplicate_username"]."</span><br />";
		}
		else if ($pass1==$pass2) {
			$user = array();
			$user["username"]=$uusername;
			$user["firstname"]=$ufirstname;
			$user["lastname"]=$ulastname;
			$user["email"]=$emailadress;
			if (!isset($verified)) $verified = "";
			$user["verified"] = $verified;
			if (!isset($verified_by_admin)) $verified_by_admin = "";
			$user["verified_by_admin"] = $verified_by_admin;
			if (!empty($user_language)) $user["language"] = $user_language;
			else $user["language"] = $LANGUAGE;
			$user["pwrequested"] = $pwrequested;
			$user["reg_timestamp"] = $reg_timestamp;
			$user["reg_hashcode"] = $reg_hashcode;
			$user["gedcomid"]=array();
			$user["rootid"]=array();
			$user["canedit"]=array();
			foreach($GEDCOMS as $ged=>$gedarray) {
				$file = $ged;
				$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
				$varname = "gedcomid_$ged";
				if (isset($$varname)) $user["gedcomid"][$file]=$$varname;
				$varname = "rootid_$ged";
				if (isset($$varname)) $user["rootid"][$file]=$$varname;
				$varname = "canedit_$ged";
				if (isset($$varname)) $user["canedit"][$file]=$$varname;
				else $user["canedit"][$file]="access";
			}
			$user["password"]=crypt($pass1);
			if ((isset($canadmin))&&($canadmin=="yes")) $user["canadmin"]=true;
			else $user["canadmin"]=false;
			if ((isset($visibleonline))&&($visibleonline=="yes")) $user["visibleonline"]=true;
			else $user["visibleonline"]=false;
			if ((isset($editaccount))&&($editaccount=="yes")) $user["editaccount"]=true;
			else $user["editaccount"]=false;
			if (!isset($new_user_theme)) $new_user_theme="";
			$user["theme"] = $new_user_theme;
			$user["loggedin"] = "N";
			$user["sessiontime"] = 0;
			if (!isset($new_contact_method)) $new_contact_method="messaging2";
			$user["contactmethod"] = $new_contact_method;
			if (isset($new_default_tab)) $user["default_tab"] = $new_default_tab;
			if (isset($new_comment)) $user["comment"] = $new_comment;
			if (isset($new_comment_exp)) $user["comment_exp"] = $new_comment_exp;
			if (isset($new_sync_gedcom)) $user["sync_gedcom"] = $new_sync_gedcom;
			else $user["sync_gedcom"] = "N";
//			if (isset($new_relationship_privacy)) $user["relationship_privacy"] = $new_relationship_privacy;
			if (isset($new_relationship_privacy) && ($new_relationship_privacy=="Y")) $user["relationship_privacy"] = "Y";
			else $user["relationship_privacy"] = "N";
			if (isset($new_max_relation_path)) $user["max_relation_path"] = $new_max_relation_path;
			else $user["max_relation_path"] = 2;
//			$user["auto_accept"] = false;
//			if (isset($new_auto_accept))  $user["auto_accept"] = true;
			if ((isset($new_auto_accept)) && ($new_auto_accept=="Y")) $user["auto_accept"]=true;
			else $user["auto_accept"]=false;
			
			$au = addUser($user, "added");
			
			if ($au) {
				print $pgv_lang["user_created"]; print "<br />";
				//-- update Gedcom record with new email address
				if ($user["sync_gedcom"]=="Y" && !empty($user["email"])) {
					$oldged = $GEDCOM;
					foreach($user["gedcomid"] as $gedc=>$gedid) {
						if (!empty($gedid)) {
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
				}
			}
			else {
				print "<span class=\"error\">".$pgv_lang["user_create_error"]."<br /></span>";
			}
		}
		else {
			print "<span class=\"error\">".$pgv_lang["password_mismatch"]."</span><br />";
		}
	}
	else {
		print "<span class=\"error\">".$pgv_lang["invalid_username"]."</span><br />";
	}
}
//-- section to delete a user
// NOTE: No table parts
if ($action=="deleteuser") {
	deleteUser($username, "deleted");
}
//-- section to update a user by first deleting them
//-- and then adding them again
// NOTE: No table parts
if ($action=="edituser2") {
	$alphabet = getAlphabet();
	$alphabet .= "_-. ";
	$i = 1;
	$pass = TRUE;
	while (strlen($uusername) > $i) {
		if (stristr($alphabet, $uusername{$i}) != TRUE){
			$pass = FALSE;
			break;
		}
		$i++;
	}
	if ($pass == TRUE){
		if (($uusername!=$oldusername)&&(getUser($uusername)!==false)) {
			print "<span class=\"error\">".$pgv_lang["duplicate_username"]."</span><br />";
			$action="edituser";
		}
		else if ($pass1==$pass2) {
			$sync_data_changed = false;
			$newuser = array();
			$olduser = getUser($oldusername);
			$newuser = $olduser;

			if (empty($pass1)) $newuser["password"]=$olduser["password"];
			else $newuser["password"]=crypt($pass1);
			//deleteUser($oldusername, "changed");
			$newuser["username"]=$uusername;
			$newuser["firstname"]=$ufirstname;
			$newuser["lastname"]=$ulastname;

			if (!empty($user_language)) $newuser["language"] = $user_language;

			if ($olduser["email"]!=$emailadress) $sync_data_changed = true;
			$newuser["email"]=$emailadress;
			if (!isset($verified)) $verified = "";
			$newuser["verified"] = $verified;
			if (!isset($verified_by_admin)) $verified_by_admin = "";
			$newuser["verified_by_admin"] = $verified_by_admin;

			if (!empty($new_contact_method)) $newuser["contactmethod"] = $new_contact_method;
			if (isset($new_default_tab)) $newuser["default_tab"] = $new_default_tab;
			if (isset($new_comment)) $newuser["comment"] = $new_comment;
			if (isset($new_comment_exp)) $newuser["comment_exp"] = $new_comment_exp;
			if (isset($new_sync_gedcom)) $newuser["sync_gedcom"] = $new_sync_gedcom;
			else $newuser["sync_gedcom"] = "N";
//			if (isset($new_relationship_privacy)) $newuser["relationship_privacy"] = $new_relationship_privacy;
			if (isset($new_relationship_privacy) && ($new_relationship_privacy=="Y")) $newuser["relationship_privacy"] = "Y";
			else $newuser["relationship_privacy"] = "N";
			if (isset($new_max_relation_path)) $newuser["max_relation_path"] = $new_max_relation_path;
//			$newuser["auto_accept"] = false;
//			if (isset($new_auto_accept)) $newuser["auto_accept"] = true;
			if (isset($new_auto_accept) && ($new_auto_accept=="Y")) $newuser["auto_accept"] = true;
			else $newuser["auto_accept"] = false;

			if (!isset($user_theme)) $user_theme="";
			$newuser["theme"] = $user_theme;
			$newuser["gedcomid"]=array();
			$newuser["rootid"]=array();
			$newuser["canedit"]=array();
			foreach($GEDCOMS as $ged=>$gedarray) {
				$file = $ged;
				$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
				$varname = "gedcomid_$ged";
				if (isset($$varname)) $newuser["gedcomid"][$file]=$$varname;
				$varname = "rootid_$ged";
				if (isset($$varname)) $newuser["rootid"][$file]=$$varname;
				$varname = "canedit_$ged";
				if (isset($$varname)) $newuser["canedit"][$file]=$$varname;
				else $user["canedit"][$file]="none";
			}
			if ($olduser["username"]!=getUserName()) {
				if ((isset($canadmin))&&($canadmin=="yes")) $newuser["canadmin"]=true;
				else $newuser["canadmin"]=false;
			}
			else $newuser["canadmin"]=$olduser["canadmin"];
			if ((isset($visibleonline))&&($visibleonline=="yes")) $newuser["visibleonline"]=true;
			else $newuser["visibleonline"]=false;
			if ((isset($editaccount))&&($editaccount=="yes")) $newuser["editaccount"]=true;
			else $newuser["editaccount"]=false;
			
			updateUser($oldusername, $newuser, "changed");
			
			//-- update Gedcom record with new email address
			if ($newuser["sync_gedcom"]=="Y" && $sync_data_changed) {
				$oldged = $GEDCOM;
				foreach($newuser["gedcomid"] as $gedc=>$gedid) {
					if (!empty($gedid)) {
						$GEDCOM = $gedc;
						$indirec = find_person_record($gedid);
						if (!empty($indirec)) {
							if (preg_match("/\d _?EMAIL/", $indirec)>0) {
								$indirec = preg_replace("/(\d _?EMAIL)[^\r\n]*/", "$1 ".$newuser["email"], $indirec);
								replace_gedrec($gedid, $indirec);
							}
							else {
								$indirec .= "\r\n1 EMAIL ".$newuser["email"];
								replace_gedrec($gedid, $indirec);
							}
						}
					}
				}
			}

			//-- if the user was just verified by the admin, then send the user a message
			if (($olduser["verified_by_admin"]!=$newuser["verified_by_admin"])&&(!empty($newuser["verified_by_admin"]))) {

				// Switch to the users language
				$oldlanguage = $LANGUAGE;
				$LANGUAGE = $newuser["language"];
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

				$message = array();
				$message["to"] = $newuser["username"];
				$host = preg_replace("/^www\./i", "", $_SERVER["SERVER_NAME"]);
				$headers = "From: phpgedview-noreply@".$host;
				$message["from"] = getUserName();
				if (substr($SERVER_URL, -1) == "/"){
					$message["subject"] = str_replace("#SERVER_NAME#", substr($SERVER_URL,0, (strlen($SERVER_URL)-1)), $pgv_lang["admin_approved"]);
					$message["body"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["admin_approved"]).$pgv_lang["you_may_login"]."\r\n\r\n".substr($SERVER_URL,0, (strlen($SERVER_URL)-1))."/index.php?command=user\r\n";
				}
				else {
					$message["subject"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["admin_approved"]);
					$message["body"] = str_replace("#SERVER_NAME#", $SERVER_URL, $pgv_lang["admin_approved"]).$pgv_lang["you_may_login"]."\r\n\r\n".$SERVER_URL."/index.php?command=user\r\n";
				}
				$message["created"] = "";
				$message["method"] = "messaging2";
				addMessage($message);

				// Switch back to the page language
				$LANGUAGE = $oldlanguage;
				if (isset($pgv_language[$LANGUAGE]) && (file_exists($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]))) require($PGV_BASE_DIRECTORY . $pgv_language[$LANGUAGE]);	//-- load language file
				$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
				$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
				$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
				$WEEK_START	= $WEEK_START_array[$LANGUAGE];
				$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];
			}
		}
		else {
			print "<span class=\"error\">".$pgv_lang["password_mismatch"]."</span><br />";
			$action="edituser";
		}
	}
	else {
		print "<span class=\"error\">".$pgv_lang["invalid_username"]."</span><br />";
	}
}
//-- print the form to edit a user
// NOTE: WORKING
require_once("./includes/functions_edit.php");
init_calendar_popup();
if ($action=="edituser") {
	$user = getUser($username);
	if (!isset($user['contactmethod'])) $user['contactmethod'] = "none";
	?>
	<form name="editform" method="post" action="useradmin.php" onsubmit="return checkform(this);">
	<input type="hidden" name="action" value="edituser2" />
	<input type="hidden" name="filter" value="<?php print $filter; ?>" />
	<input type="hidden" name="sort" value="<?php print $sort; ?>" />
	<input type="hidden" name="ged" value="<?php print $ged; ?>" />
	<input type="hidden" name="usrlang" value="<?php print $usrlang; ?>" />
	<input type="hidden" name="oldusername" value="<?php print $username; ?>" />
	<?php $tab=0; ?>
	<table class="center list_table width80 <?php print $TEXT_DIRECTION; ?>">
	<tr><td colspan="2" class="facts_label"><?php
	print "<h2>".$pgv_lang["update_user"]."</h2>";
	?>
  </td>
  </tr>
    <tr>
      <td class="descriptionbox width20 wrap"><?php print_help_link("useradmin_username_help", "qm","username"); print $pgv_lang["username"];?></td>
      <td class="optionbox wrap"><input type="text" name="uusername" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user['username']?>" /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_firstname_help", "qm", "firstname"); print $pgv_lang["firstname"];?></td>
      <td class="optionbox wrap"><input type="text" name="ufirstname" tabindex="<?php $tab++; print $tab; ?>" value="<?php print PrintReady($user['firstname'])?>" size="50" /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_lastname_help", "qm","lastname");print $pgv_lang["lastname"];?></td>
      <td class="optionbox wrap"><input type="text" name="ulastname" tabindex="<?php $tab++; print $tab; ?>" value="<?php print PrintReady($user['lastname'])?>" size="50" /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_password_help", "qm","password"); print $pgv_lang["password"];?></td>
      <td class="optionbox wrap"><input type="password" name="pass1" tabindex="<?php $tab++; print $tab; ?>" /><br /><?php print $pgv_lang["leave_blank"];?></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_conf_password_help", "qm","confirm"); print $pgv_lang["confirm"];?></td>
      <td class="optionbox wrap"><input type="password" name="pass2" tabindex="<?php $tab++; print $tab; ?>" /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_gedcomid_help", "qm","gedcomid"); print $pgv_lang["gedcomid"];?></td>
      <td class="optionbox wrap">
	<table class="<?php print $TEXT_DIRECTION; ?>">
         	<?php
		foreach($GEDCOMS as $ged=>$gedarray) {
			$file = $ged;
			$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);			?>
			<tr>
			<td><?php print $file;?>:&nbsp;&nbsp;</td>
			<td> <input type="text" name="<?php print "gedcomid_$ged"; ?>" id="<?php print "gedcomid_$ged"; ?>" tabindex="<?php $tab++; print $tab; ?>" value="<?php
			if (isset($user['gedcomid'][$file])) print $user['gedcomid'][$file];
			print "\" />";
			print_findindi_link("gedcomid_$ged","");
			if (isset($user['gedcomid'][$file])) {
				$sged = $GEDCOM;
				$GEDCOM = $file;
				print "\n<span class=\"list_item\"> ".get_person_name($user['gedcomid'][$file]);
				print_first_major_fact($user['gedcomid'][$file]);
				$GEDCOM = $sged;
				print "</span>\n";
			}
			print "</td></tr>";
		} 
		?>
	</table>
      </td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_rootid_help", "qm", "rootid"); print $pgv_lang["rootid"];?></td>
      <td class="optionbox wrap">
	<table class="<?php print $TEXT_DIRECTION;?>">
	  <?php
	  foreach($GEDCOMS as $ged=>$gedarray) {
	    $file = $ged;
	    $ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
	  ?><tr>
	    <td><?php print $file;?>:&nbsp;&nbsp;</td>
	    <td> <input type="text" name="<?php print "rootid_$ged"; ?>" id="<?php print "rootid_$ged"; ?>" tabindex="<?php $tab++; print $tab; ?>" value="<?php
	    if (isset($user['rootid'][$file])) print $user['rootid'][$file];
	    print "\" />";
	    print_findindi_link("rootid_$ged","");
		if (isset($user['rootid'][$file])) {
			$sged = $GEDCOM;
			$GEDCOM = $file;
			print "\n<span class=\"list_item\">".get_person_name($user['rootid'][$file]);
			print_first_major_fact($user['rootid'][$file]);
			$GEDCOM = $sged;
			print "</span>\n";
		}
	    ?></td>
	  </tr>
	<?php } ?></table>
      </td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_sync_gedcom_help", "qm", "sync_gedcom"); print $pgv_lang["sync_gedcom"];?></td>
      <td class="optionbox wrap"><input type="checkbox" name="new_sync_gedcom" tabindex="<?php $tab++; print $tab; ?>" value="Y" <?php if ($user['sync_gedcom']=="Y") print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_can_admin_help", "qm", "can_admin"); print $pgv_lang["can_admin"];?></td>
      <td class="optionbox wrap"><input type="checkbox" name="canadmin" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php if ($user['canadmin']) print "checked=\"checked\""; if ($user["username"]==getUserName()) print " disabled=\"disabled\""; ?> /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_can_edit_help", "qm","can_edit"); print $pgv_lang["can_edit"];?></td>
      <td class="optionbox wrap">
	 <table class="<?php print $TEXT_DIRECTION; ?>">
      <?php
	foreach($GEDCOMS as $ged=>$gedarray) {
		$file = $ged;
		$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
		print "<tr><td>$file:&nbsp;&nbsp;</td><td>";
		if (isset($user['canedit'][$file])) {
			if ($user['canedit'][$file]===true) $user['canedit'][$file]="yes";
		}
		else $user['canedit'][$file]="no";
		$tab++;
		print "<select name=\"canedit_$ged\" tabindex=\"".$tab."\">\n";
		print "<option value=\"none\"";
		if ($user['canedit'][$file]=="none") print " selected=\"selected\"";
		print ">".$pgv_lang["none"]."</option>\n";
		print "<option value=\"access\"";
		if ($user['canedit'][$file]=="access") print " selected=\"selected\"";
		print ">".$pgv_lang["access"]."</option>\n";
		print "<option value=\"edit\"";
		if ($user['canedit'][$file]=="edit") print " selected=\"selected\"";
		print ">".$pgv_lang["edit"]."</option>\n";
		print "<option value=\"accept\"";
		if ($user['canedit'][$file]=="accept") print " selected=\"selected\"";
		print ">".$pgv_lang["accept"]."</option>\n";
		print "<option value=\"admin\"";
		if ($user['canedit'][$file]=="admin") print " selected=\"selected\"";
		print ">".$pgv_lang["admin_gedcom"]."</option>\n";
		print "</select>\n";
		print "</td></tr>";
	}
	?>
	</table>
      </td>
    </tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_auto_accept_help", "qm", "user_auto_accept"); print $pgv_lang["user_auto_accept"];?></td>
    <td class="optionbox wrap"><input type="checkbox" name="new_auto_accept" tabindex="<?php $tab++; print $tab; ?>" value="Y" <?php if ($user["auto_accept"]) print "checked=\"checked\"";?> /></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_relation_priv_help", "qm", "user_relationship_priv"); print $pgv_lang["user_relationship_priv"];?></td>
    <td class="optionbox wrap"><input type="checkbox" name="new_relationship_privacy" tabindex="<?php $tab++; print $tab; ?>" value="Y" <?php if ($user["relationship_privacy"]=="Y") print "checked=\"checked\"";?> /></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_path_length_help", "qm", "user_path_length"); print $pgv_lang["user_path_length"];?></td>
    <td class="optionbox wrap"><input type="text" name="new_max_relation_path" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user["max_relation_path"]; ?>" size="5" /></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_email_help", "qm", "emailadress"); print $pgv_lang["emailadress"];?></td><td class="optionbox wrap"><input type="text" name="emailadress" tabindex="<?php $tab++; print $tab; ?>" dir="ltr" value="<?php print $user['email']?>" size="50" /></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_verified_help", "qm", "verified"); print $pgv_lang["verified"];?></td><td class="optionbox wrap"><input type="checkbox" name="verified" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php if ($user['verified']) print "checked=\"checked\"";?> /></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_verbyadmin_help", "qm", "verified_by_admin"); print $pgv_lang["verified_by_admin"];?></td><td class="optionbox wrap"><input type="checkbox" name="verified_by_admin" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php if ($user['verified_by_admin']) print "checked=\"checked\""; ?> /></td></tr>
    <tr><td class="descriptionbox wrap"><?php print_help_link("edituser_change_lang_help", "qm", "change_lang");print $pgv_lang["change_lang"];?></td><td class="optionbox wrap" valign="top"><?php
	if ($ENABLE_MULTI_LANGUAGE) {
		$tab++;
		print "<select name=\"user_language\" tabindex=\"".$tab."\" dir=\"ltr\" style=\"{ font-size: 9pt; }\">";
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
    <?php if ($ALLOW_USER_THEMES) { ?>
    <tr><td class="descriptionbox wrap" valign="top" align="left"><?php print_help_link("useradmin_user_theme_help", "qm", "user_theme"); print $pgv_lang["user_theme"];?></td><td class="optionbox wrap" valign="top">
    	<select name="user_theme" tabindex="<?php $tab++; print $tab; ?>" dir="ltr">
    	  <option value=""><?php print $pgv_lang["site_default"]; ?></option>
    	  <?php
    	    $themes = get_theme_names();
    	    foreach($themes as $indexval => $themedir)
    	    {
    	      print "<option value=\"".$themedir["dir"]."\"";
    	      if ($themedir["dir"] == $user["theme"]) print " selected=\"selected\"";
    	      print ">".$themedir["name"]."</option>\n";
    	    }
	?></select>
      </td>
    </tr>
    <?php } ?>
    <tr>
		<td class="descriptionbox wrap"><?php print_help_link("useradmin_user_contact_help", "qm", "user_contact_method"); print $pgv_lang["user_contact_method"];?></td>
		<td class="optionbox wrap"><select name="new_contact_method" tabindex="<?php $tab++; print $tab; ?>">
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
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_visibleonline_help", "qm", "visibleonline"); print $pgv_lang["visibleonline"];?></td>
      <td class="optionbox wrap"><input type="checkbox" name="visibleonline" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php if ($user['visibleonline']) print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
      <td class="descriptionbox wrap"><?php print_help_link("useradmin_editaccount_help", "qm", "editaccount"); print $pgv_lang["editaccount"];?></td>
      <td class="optionbox wrap"><input type="checkbox" name="editaccount" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php if ($user['editaccount']) print "checked=\"checked\""; ?> /></td>
    </tr>
    <tr>
		<td class="descriptionbox wrap"><?php print_help_link("useradmin_user_default_tab_help", "qm", "user_default_tab"); print $pgv_lang["user_default_tab"];?></td>
		<td class="optionbox wrap"><select name="new_default_tab" tabindex="<?php $tab++; print $tab; ?>">
				<option value="0" <?php if (@$user['default_tab']==0) print "selected=\"selected\""; ?>><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1" <?php if (@$user['default_tab']==1) print "selected=\"selected\""; ?>><?php print $pgv_lang["notes"];?></option>
				<option value="2" <?php if (@$user['default_tab']==2) print "selected=\"selected\""; ?>><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3" <?php if (@$user['default_tab']==3) print "selected=\"selected\""; ?>><?php print $pgv_lang["media"];?></option>
				<option value="4" <?php if (@$user['default_tab']==4) print "selected=\"selected\""; ?>><?php print $pgv_lang["relatives"];?></option>
			</select>
		</td>
	</tr>
	<tr>
	  <td class="descriptionbox wrap"><?php print_help_link("useradmin_comment_help", "qm", "comment"); print $pgv_lang["comment"];?></td>
      <td class="optionbox wrap"><textarea cols="50" rows="5" name="new_comment" tabindex="<?php $tab++; print $tab; ?>" ><?php $tmp = stripslashes(PrintReady($user['comment'])); print $tmp; ?></textarea></td>
    </tr>
	<tr>
	  <td class="descriptionbox wrap"><?php print_help_link("useradmin_comment_exp_help", "qm", "comment_exp"); print $pgv_lang["comment_exp"];?></td>
      <td class="optionbox wrap"><input type="text" name="new_comment_exp" id="new_comment_exp" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $user["comment_exp"]; ?>" />&nbsp;&nbsp;<?php print_calendar_popup("new_comment_exp"); ?></td>
    </tr>
    <tr><td class="topbottombar" colspan="2">
  	<input type="submit" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $pgv_lang["update_user"]; ?>" />
	<input type="button" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $pgv_lang["back"];?>" onclick="window.location='useradmin.php?action=listusers&amp;sort=<?php print $sort;?>&amp;filter=<?php print $filter;?>&amp;usrlang=<?php print $usrlang;?>&amp;ged=<?php print $ged;?>';"/>
	</td></tr>
	</table>
	</form>
	<?php
  print_footer();
  exit;
}
//-- end of $action=='edituser'

//-- print out a list of the current users
// NOTE: WORKING
if (($action == "listusers") || ($action == "edituser2") || ($action == "deleteuser")) {
	if ($view != "preview") $showprivs = false;
	else $showprivs = true;

	switch ($sort){
		case "sortfname":
			$users = getUsers("firstname","asc", "lastname");
			break;
		case "sortlname":
			$users = getUsers("lastname","asc", "firstname");
			break;
		case "sortllgn":
			$users = getUsers("sessiontime","desc");
			break;
		case "sortuname":
			$users = getUsers("username","asc");
			break;
		case "sortreg":
			$users = getUsers("reg_timestamp","desc");
			break;
		case "sortver":
			$users = getUsers("verified","asc");
			break;
		case "sortveradm":
			$users = getUsers("verified_by_admin","asc");
			break;
		default: $users = getUsers("username","asc");
	}
	
	// First filter the users, otherwise the javascript to unfold priviledges gets disturbed
	foreach($users as $username=>$user) {
		if ($filter == "warnings") {
			if (!empty($user["comment_exp"])) {
				if ((strtotime($user["comment_exp"]) == "-1") || (strtotime($user["comment_exp"]) >= time("U"))) unset($users[$username]);
			}
			else if (((date("U") - $user["reg_timestamp"]) <= 604800) || ($user["verified"]=="yes")) unset($users[$username]);
		}
		else if ($filter == "adminusers") {
			if (!($user["canadmin"])) unset($users[$username]);
		}
		else if ($filter == "usunver") {
			if ($user["verified"] == "yes") unset($users[$username]);
		}
		else if ($filter == "admunver") {
			if (($user["verified_by_admin"] == "yes") || ($user["verified"] != "yes")) unset($users[$username]);
		}
		else if ($filter == "language") {
			if ($user["language"] != $usrlang) unset($users[$username]);
		}
		else if ($filter == "gedadmin") {
			if (isset($user["canedit"][$ged])) {
				if ($user["canedit"][$ged] != "admin") unset($users[$username]);
			}
		}
	}

	// Then show the users
	?>
	<table class="center list_table width80 <?php print $TEXT_DIRECTION; ?>">
	<tr><td colspan="<?php if ($view == "preview") print "8"; else print "9"; ?>" class="facts_label"><?php
		print "<h2>".$pgv_lang["current_users"]."</h2>";
	?>
	</td></tr>
    <tr>
	  <td colspan="<?php if ($view == "preview") print "8"; else print "9"; ?>" class="topbottombar rtl"><a href="useradmin.php"><?php if ($view != "preview") print $pgv_lang["back_useradmin"]; else print "&nbsp;";?></a></td>
    </tr>
	<tr>
		<?php if ($view != "preview") {
			print "<td class=\"descriptionbox wrap\">".$pgv_lang["delete"];
			print "<br />".$pgv_lang["edit"]."</td>";
		} ?>
		<td class="descriptionbox wrap"><?php print "<a href=\"useradmin.php?action=listusers&amp;sort=sortuname&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">"; ?><?php print $pgv_lang["username"]; ?></a></td>
		<td class="descriptionbox wrap"><?php print "<a href=\"useradmin.php?action=listusers&amp;sort=sortlname&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">"; ?><?php print $pgv_lang["full_name"]; ?></a></td>
		<td class="descriptionbox wrap"><?php print $pgv_lang["inc_languages"]; ?></td>
		<td class="descriptionbox" style="padding-left:2px"><a href="javascript: <?php print $pgv_lang["privileges"];?>" onclick="<?php
		$k = 1;
		for ($i=1, $max=count($users)+1; $i<=$max; $i++) print "expand_layer('user-geds".$i."'); ";
		print " return false;\"><img id=\"user-geds".$k."_img\" src=\"".$PGV_IMAGE_DIR."/";
		if ($showprivs == false) print $PGV_IMAGES["plus"]["other"];
		else print $PGV_IMAGES["minus"]["other"];
		print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
		print "<div id=\"user-geds".$k."\" style=\"display: ";
		if ($showprivs == false) print "none\">";
		else print "block\">";
		print "</div>&nbsp;";
		print $pgv_lang["privileges"];?>
		</td>
		<td class="descriptionbox wrap"><?php print "<a href=\"useradmin.php?action=listusers&amp;sort=sortreg&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">"; ?><?php print $pgv_lang["date_registered"]; ?></a></td>
		<td class="descriptionbox wrap"><?php print "<a href=\"useradmin.php?action=listusers&amp;sort=sortllgn&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">"; ?><?php print $pgv_lang["last_login"]; ?></a></td>
		<td class="descriptionbox wrap"><?php print "<a href=\"useradmin.php?action=listusers&amp;sort=sortver&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">"; ?><?php print $pgv_lang["verified"]; ?></a></td>
		<td class="descriptionbox wrap"><?php print "<a href=\"useradmin.php?action=listusers&amp;sort=sortveradm&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">"; ?><?php print $pgv_lang["verified_by_admin"]; ?></a></td>
	</tr>
	<?php
	$k++;
	foreach($users as $username=>$user) {
		if (empty($user["language"])) $user["language"]=$LANGUAGE;
		print "<tr>\n";
		if ($view != "preview") {
			if ($TEXT_DIRECTION=="ltr") print "\t<td class=\"optionbox wrap\"><a href=\"useradmin.php?action=deleteuser&amp;username=".urlencode($username)."&amp;sort=".$sort."&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\" onclick=\"return confirm('".$pgv_lang["confirm_user_delete"]." $username?');\">".$pgv_lang["delete"]."</a><br />\n";
			else if (begRTLText($username)) print "\t<td class=\"optionbox wrap\"><a href=\"useradmin.php?action=deleteuser&amp;username=".urlencode($username)."&amp;sort=".$sort."&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\" onclick=\"return confirm('?".$pgv_lang["confirm_user_delete"]." $username');\">".$pgv_lang["delete"]."</a><br />\n";
			else print "\t<td class=\"optionbox wrap\"><a href=\"useradmin.php?action=deleteuser&amp;username=".urlencode($username)."&amp;sort=".$sort."&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\" onclick=\"return confirm('?$username ".$pgv_lang["confirm_user_delete"]." ');\">".$pgv_lang["delete"]."</a><br />\n";
			print "<a href=\"useradmin.php?action=edituser&amp;username=".urlencode($username)."&amp;sort=".$sort."&amp;filter=".$filter."&amp;usrlang=".$usrlang."&amp;ged=".$ged."\">".$pgv_lang["edit"]."</a></td>\n";
		}
		if (!empty($user["comment_exp"])) {
			if ((strtotime($user["comment_exp"]) != "-1") && (strtotime($user["comment_exp"]) < time("U"))) print "\t<td class=\"optionbox red\">".$username;
			else print "\t<td class=\"optionbox wrap\">".$username;
		}
		else print "\t<td class=\"optionbox wrap\">".$username;
		if (!empty($user["comment"])) print "<br /><img class=\"adminicon\" width=\"20\" height=\"20\" align=\"top\" alt=\"".PrintReady(stripslashes($user["comment"]))."\"  title=\"".PrintReady(stripslashes($user["comment"]))."\"  src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["note"]["other"]."\">";
		print "</td>\n";
		if ($TEXT_DIRECTION=="ltr") print "\t<td class=\"optionbox wrap\">".$user["firstname"]." ".$user["lastname"]."&lrm;</td>\n";
		else                        print "\t<td class=\"optionbox wrap\">".$user["firstname"]." ".$user["lastname"]."&rlm;</td>\n";
		print "\t<td class=\"optionbox wrap\">".$pgv_lang["lang_name_".$user["language"]]."<br /><img src=\"".$language_settings[$user["language"]]["flagsfile"]."\" class=\"brightflag\" alt=\"".$pgv_lang["lang_name_".$user["language"]]."\" title=\"".$pgv_lang["lang_name_".$user["language"]]."\" /></td>\n";
		print "\t<td class=\"optionbox\">";
		print "<a href=\"javascript: ".$pgv_lang["privileges"]."\" onclick=\"expand_layer('user-geds".$k."'); return false;\"><img id=\"user-geds".$k."_img\" src=\"".$PGV_IMAGE_DIR."/";
		if ($showprivs == false) print $PGV_IMAGES["plus"]["other"];
		else print $PGV_IMAGES["minus"]["other"];
		print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" />";
		print "</a>";
		print "<div id=\"user-geds".$k."\" style=\"display: ";
		if ($showprivs == false) print "none\">";
		else print "block\">";
		print "<ul>";
		if ($user["canadmin"]) print "<li class=\"warning\">".$pgv_lang["can_admin"]."</li>\n";
		uksort($GEDCOMS, "strnatcasecmp");
		reset($GEDCOMS);
		foreach($GEDCOMS as $gedid=>$gedcom) {
			if (isset($user["canedit"][$gedid])) $vval = $user["canedit"][$gedid];
			else $vval = "none";
			if ($vval == "") $vval = "none";
			if (isset($user["gedcomid"][$gedid])) $uged = $user["gedcomid"][$gedid];
			else $uged = "";
			if ($vval=="accept" || $vval=="admin") print "<li class=\"warning\">"; 
			else print "<li>";
			print $pgv_lang[$vval]." ";
			if ($uged != "") print "<a href=\"individual.php?pid=".$uged."&amp;ged=".$gedid."\">".$gedid."</a></li>\n";
			else print $gedid."</li>\n";
		}
		print "</ul>";
		print "</div>";
		$k++;
		print "</td>\n";
		if (((date("U") - $user["reg_timestamp"]) > 604800) && ($user["verified"]!="yes")) print "\t<td class=\"optionbox red\">";
		else print "\t<td class=\"optionbox wrap\">";
		print get_changed_date(date("d", $user["reg_timestamp"])." ".date("M", $user["reg_timestamp"])." ".date("Y", $user["reg_timestamp"]))." - ".date($TIME_FORMAT, $user["reg_timestamp"]);
		print "</td>\n";
		print "\t<td class=\"optionbox wrap\">";
		if ($user["reg_timestamp"] > $user["sessiontime"]) {
			print $pgv_lang["never"];
		}
		else {
			print get_changed_date(date("d", $user["sessiontime"])." ".date("M", $user["sessiontime"])." ".date("Y", $user["sessiontime"]))." - ".date($TIME_FORMAT, $user["sessiontime"]);
		}
		print "</td>\n";
		print "\t<td class=\"optionbox wrap\">";
		if ($user["verified"]=="yes") print $pgv_lang["yes"];
		else print $pgv_lang["no"];
		print "</td>\n";
		print "\t<td class=\"optionbox wrap\">";
		if ($user["verified_by_admin"]=="yes") print $pgv_lang["yes"];
		else print $pgv_lang["no"];
		print "</td>\n";
		print "</tr>\n";
	}
	?>
	<tr><td colspan="<?php if ($view == "preview") print "8"; else print "10"; ?>" class="topbottombar rtl"><a href="useradmin.php"><?php  if ($view != "preview") print $pgv_lang["back_useradmin"]; else print "&nbsp;"; ?></a></td></tr><?php
	print "</table>";
	print_footer();
	exit;
}

// -- print out the form to add a new user
// NOTE: WORKING
if ($action == "createform") {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
		function checkform(frm) {
			if (frm.uusername.value=="") {
				alert("<?php print $pgv_lang["enter_username"]; ?>");
				frm.uusername.focus();
				return false;
			}
			if (frm.ufirstname.value=="") {
				alert("<?php print $pgv_lang["enter_fullname"]; ?>");
				frm.ufirstname.focus();
				return false;
			}
			if (frm.pass1.value=="") {
				alert("<?php print $pgv_lang["enter_password"]; ?>");
				frm.pass1.focus();
				return false;
			}
			if (frm.pass2.value=="") {
				alert("<?php print $pgv_lang["confirm_password"]; ?>");
				frm.pass2.focus();
				return false;
			}
		    if (frm.pass1.value.length < 6) {
		      alert("<?php print $pgv_lang["passwordlength"]; ?>");
			  frm.pass1.value = "";
		      frm.pass2.value = "";
		      frm.pass1.focus();
			  return false;
		    }
			if ((frm.emailadress.value!="")&&(frm.emailadress.value.indexOf("@")==-1)) {
				alert("<?php print $pgv_lang["enter_email"]; ?>");
				frm.emailadress.focus();
				return false;
			}
			return true;
		}
	//-->
	</script>
	
	<form name="newform" method="post" action="<?php print $SCRIPT_NAME;?>" onsubmit="return checkform(this);">
	<input type="hidden" name="action" value="createuser" />
	<!--table-->
	<?php $tab = 0; ?>
	<table class="center list_table width80 <?php print $TEXT_DIRECTION; ?>">
	<tr>
		<td class="facts_label" colspan="2">
		<h2><?php print $pgv_lang["add_user"];?></h2>
		</td>
	</tr>
		<tr><td class="descriptionbox wrap width20"><?php print_help_link("useradmin_username_help", "qm", "username"); print $pgv_lang["username"];?></td><td class="optionbox wrap"><input type="text" name="uusername" tabindex="<?php $tab++; print $tab; ?>" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_firstname_help", "qm","firstname"); print $pgv_lang["firstname"];?></td><td class="optionbox wrap"><input type="text" name="ufirstname" tabindex="<?php $tab++; print $tab; ?>" size="50" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_lastname_help", "qm", "lastname"); print $pgv_lang["lastname"];?></td><td class="optionbox wrap"><input type="text" name="ulastname" tabindex="<?php $tab++; print $tab; ?>" size="50" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_password_help", "qm", "password"); print $pgv_lang["password"];?></td><td class="optionbox wrap"><input type="password" name="pass1" tabindex="<?php $tab++; print $tab; ?>" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_conf_password_help", "qm", "confirm"); print $pgv_lang["confirm"];?></td><td class="optionbox wrap"><input type="password" name="pass2" tabindex="<?php $tab++; print $tab; ?>" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_gedcomid_help", "qm","gedcomid"); print $pgv_lang["gedcomid"];?></td><td class="optionbox wrap">

		<table class="<?php print $TEXT_DIRECTION; ?>">
		<?php
		foreach($GEDCOMS as $ged=>$gedarray) {
			$file = $ged;
			$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
			$tab++;
			print "<tr><td>$file:&nbsp;&nbsp;</td><td><input type=\"text\" name=\"gedcomid_$ged\" id=\"gedcomid_$ged\" tabindex=\"".$tab."\" value=\"";
			print "\" />\n";
			print_findindi_link("gedcomid_$ged","");
			print "</td></tr>\n";
		}
		?>
		</table>
		</td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_rootid_help", "qm","rootid"); print $pgv_lang["rootid"];?></td><td class="optionbox wrap">
		<table class="<?php print $TEXT_DIRECTION; ?>">
		<?php
		foreach($GEDCOMS as $ged=>$gedarray) {
			$file = $ged;
			$ged = preg_replace(array("/\./","/-/"), array("_","_"), $ged);
			$tab++;
			print "<tr><td>$file:&nbsp;&nbsp;</td><td><input type=\"text\" name=\"rootid_$ged\" id=\"rootid_$ged\" tabindex=\"".$tab."\" value=\"";
			print "\" />\n";
			print_findindi_link("rootid_$ged","");
			print "</td></tr>\n";
		}
		print "</table>";
		?>
		</td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_sync_gedcom_help", "qm","sync_gedcom"); print $pgv_lang["sync_gedcom"];?></td>
      		<td class="optionbox wrap"><input type="checkbox" name="new_sync_gedcom" tabindex="<?php $tab++; print $tab; ?>" value="Y" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_can_admin_help", "qm","can_admin"); print $pgv_lang["can_admin"];?></td><td class="optionbox wrap"><input type="checkbox" name="canadmin" tabindex="<?php $tab++; print $tab; ?>" value="yes" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_can_edit_help", "qm","can_edit");print $pgv_lang["can_edit"];?></td><td class="optionbox wrap">
		<?php
		foreach($GEDCOMS as $ged=>$gedarray) {
			$file = $ged;
			$ged = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged);
			$tab++;
			print "<select name=\"canedit_$ged\" tabindex=\"".$tab."\">\n";
			print "<option value=\"none\"";
			print ">".$pgv_lang["none"]."</option>\n";
			print "<option value=\"access\" selected=\"selected\"";
			print ">".$pgv_lang["access"]."</option>\n";
			print "<option value=\"edit\"";
			print ">".$pgv_lang["edit"]."</option>\n";
			print "<option value=\"accept\"";
			print ">".$pgv_lang["accept"]."</option>\n";
			print "<option value=\"admin\"";
			print ">".$pgv_lang["admin_gedcom"]."</option>\n";
			print "</select> $file<br />\n";
		}
		?>
		</td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_auto_accept_help", "qm", "user_auto_accept");print $pgv_lang["user_auto_accept"];?></td>
			<td class="optionbox wrap"><input type="checkbox" name="new_auto_accept" tabindex="<?php $tab++; print $tab; ?>" value="Y" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_relation_priv_help", "qm", "user_relationship_priv");print $pgv_lang["user_relationship_priv"];?></td>
			<td class="optionbox wrap"><input type="checkbox" name="new_relationship_privacy" tabindex="<?php $tab++; print $tab; ?>" value="Y" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_path_length_help", "qm", "user_path_length"); print $pgv_lang["user_path_length"];?></td>
			<td class="optionbox wrap"><input type="text" name="new_max_relation_path" tabindex="<?php $tab++; print $tab; ?>" value="0" size="5" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_email_help", "qm", "emailadress"); print $pgv_lang["emailadress"];?></td><td class="optionbox wrap"><input type="text" name="emailadress" tabindex="<?php $tab++; print $tab; ?>" value="" size="50" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_verified_help", "qm", "verified"); print $pgv_lang["verified"];?></td><td class="optionbox wrap"><input type="checkbox" name="verified" tabindex="<?php $tab++; print $tab; ?>" value="yes" checked="checked" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_verbyadmin_help", "qm", "verified_by_admin"); print $pgv_lang["verified_by_admin"];?></td><td class="optionbox wrap"><input type="checkbox" name="verified_by_admin" tabindex="<?php $tab++; print $tab; ?>" value="yes" checked="checked" /></td></tr>
		<tr><td class="descriptionbox wrap"><?php print_help_link("useradmin_change_lang_help", "qm", "change_lang");print $pgv_lang["change_lang"];?></td><td class="optionbox wrap" valign="top"><?php
		
		$user = GetUser(GetUserName());
		if ($ENABLE_MULTI_LANGUAGE) {
			$tab++;
	      	print "<select name=\"user_language\" tabindex=\"".$tab."\" style=\"{ font-size: 9pt; }\">";
		  	foreach ($pgv_language as $key => $value) {
			  	if ($language_settings[$key]["pgv_lang_use"]) {
		      		print "\n\t\t\t<option value=\"$key\"";
	      			if ($key == $user["language"]) {
			      	    print " selected=\"selected\"";
	      			}
			 		print ">" . $pgv_lang[$key] . "</option>";
		 		}
      		}
      		print "</select>\n\t\t";
		}
		else print "&nbsp;";
		?></td></tr>
		<?php if ($ALLOW_USER_THEMES) { ?>
			<tr><td class="descriptionbox wrap" valign="top" align="left"><?php print_help_link("useradmin_user_theme_help", "qm", "user_theme"); print $pgv_lang["user_theme"];?></td><td class="optionbox wrap" valign="top">
	    	<select name="new_user_theme" tabindex="<?php $tab++; print $tab; ?>">
			<option value="" selected="selected"><?php print $pgv_lang["site_default"]; ?></option>
			<?php
			$themes = get_theme_names();
			foreach($themes as $indexval => $themedir) {
				print "<option value=\"".$themedir["dir"]."\"";
				print ">".$themedir["name"]."</option>\n";
			}
			?>
			</select>
			</td></tr>
		<?php } ?>
		<tr>
			<td class="descriptionbox wrap"><?php print_help_link("useradmin_user_contact_help", "qm", "user_contact_method"); print $pgv_lang["user_contact_method"];?></td>
			<td class="optionbox wrap"><select name="new_contact_method" tabindex="<?php $tab++; print $tab; ?>">
			<?php if ($PGV_STORE_MESSAGES) { ?>
				<option value="messaging"><?php print $pgv_lang["messaging"];?></option>
				<option value="messaging2" selected="selected"><?php print $pgv_lang["messaging2"];?></option>
			<?php } else { ?>
				<option value="messaging3" selected="selected"><?php print $pgv_lang["messaging3"];?></option>
			<?php } ?>
				<option value="mailto"><?php print $pgv_lang["mailto"];?></option>
				<option value="none"><?php print $pgv_lang["no_messaging"];?></option>
			</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap"><?php print_help_link("useradmin_visibleonline_help", "qm", "visibleonline"); print $pgv_lang["visibleonline"];?></td>
			<td class="optionbox wrap"><input type="checkbox" name="visibleonline" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php print "checked=\"checked\""; ?> /></td>
		</tr>
		<tr>
			<td class="descriptionbox wrap"><?php print_help_link("useradmin_editaccount_help", "qm", "editaccount"); print $pgv_lang["editaccount"];?></td>
			<td class="optionbox wrap"><input type="checkbox" name="editaccount" tabindex="<?php $tab++; print $tab; ?>" value="yes" <?php print "checked=\"checked\""; ?> /></td>
		</tr>
		<tr>
			<td class="descriptionbox wrap"><?php print_help_link("useradmin_user_default_tab_help", "qm", "user_default_tab"); print $pgv_lang["user_default_tab"];?></td>
			<td class="optionbox wrap"><select name="new_default_tab" tabindex="<?php $tab++; print $tab; ?>">
				<option value="0"><?php print $pgv_lang["personal_facts"];?></option>
				<option value="1"><?php print $pgv_lang["notes"];?></option>
				<option value="2"><?php print $pgv_lang["ssourcess"];?></option>
				<option value="3"><?php print $pgv_lang["media"];?></option>
				<option value="4"><?php print $pgv_lang["relatives"];?></option>
				</select>
			</td>
		</tr>
		<?php if (userIsAdmin(GetUserName())) { ?>
		<tr>
			<td class="descriptionbox wrap"><?php print_help_link("useradmin_comment_help", "qm", "comment"); print $pgv_lang["comment"];?></td>
			<td class="optionbox wrap"><textarea cols="50" rows="5" name="new_comment" tabindex="<?php $tab++; print $tab; ?>" ></textarea></td>
		</tr>
		<tr>
			<td class="descriptionbox wrap"><?php print_help_link("useradmin_comment_exp_help", "qm", "comment_exp"); print $pgv_lang["comment_exp"];?></td>
			<td class="optionbox wrap"><input type="text" name="new_comment_exp" tabindex="<?php $tab++; print $tab; ?>" id="new_comment_exp" />&nbsp;&nbsp;<?php print_calendar_popup("new_comment_exp"); ?></td>
		</tr>
		<?php } ?>
	<tr><td class="topbottombar" colspan="2">
	<input type="hidden" name="pwrequested" value="" />
	<input type="hidden" name="reg_timestamp" value="<?php print date("U");?>" />
	<input type="hidden" name="reg_hashcode" value="" />
	<input type="submit" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $pgv_lang["create_user"]; ?>" />
	<input type="button" tabindex="<?php $tab++; print $tab; ?>" value="<?php print $pgv_lang["back"];?>" onclick="window.location='useradmin.php';"/>
	</td></tr></table>
	</form>
	<?php
	print_footer();
	exit;
}

// Cleanup users and user rights
//NOTE: WORKING
if ($action == "cleanup") {
	?>
	<form name="cleanupform" method="post" action="">
	<input type="hidden" name="action" value="cleanup2" />
	<table class="center list_table width80 <?php print $TEXT_DIRECTION; ?>">
	<tr>
		<td class="facts_label" colspan="2">
		<h2><?php print $pgv_lang["cleanup_users"];?></h2>
		</td>
	</tr>
	<?php
	// Check for idle users
	if (!isset($month)) $month = 1;
	print "<tr><td class=\"descriptionbox\">".$pgv_lang["usr_idle"]."</td>";
	print "<td class=\"optionbox\"><select onchange=\"document.location=options[selectedIndex].value;\">";
	for($i=1; $i<=12; $i++) { 
		print "<option value=\"useradmin.php?action=cleanup&amp;month=$i\"";
		if ($i == $month) print " selected=\"selected\"";
		print " >".$i."</option>";
	}
	print "</select></td></tr>";
	?>
	<tr><td class="topbottombar" colspan="2"><?php print $pgv_lang["options"]; ?></td></tr>
	<?php
	// Check users not logged in too long
	$users = GetUsers();
	$ucnt = 0;
	foreach($users as $key=>$user) {
		if ($user["sessiontime"] == "0") $datelogin = $user["reg_timestamp"];
		else $datelogin = $user["sessiontime"];
		if ((mktime(0, 0, 0, date("m")-$month, date("d"), date("Y")) > $datelogin) && ($user["verified"] == "yes") && ($user["verified_by_admin"] == "yes")) {
			?><tr><td class="descriptionbox"><?php print $user["username"]." - ".$user["firstname"]." ".$user["lastname"].":&nbsp;&nbsp;".$pgv_lang["usr_idle_toolong"];
			print get_changed_date(date("d", $datelogin)." ".date("M", $datelogin)." ".date("Y", $datelogin));
			$ucnt++;
			?></td><td class="optionbox"><input type="checkbox" name="<?php print "del_".preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $user["username"]); ?>" value="yes" /></td></tr><?php
		}
	}
		
	// Check unverified users
	foreach($users as $key=>$user) {
		if (((date("U") - $user["reg_timestamp"]) > 604800) && ($user["verified"]!="yes")) {
			?><tr><td class="descriptionbox"><?php print $user["username"]." - ".$user["firstname"]." ".$user["lastname"].":&nbsp;&nbsp;".$pgv_lang["del_unveru"]; 
			$ucnt++;
			?></td><td class="optionbox"><input type="checkbox" checked="checked" name="<?php print "del_".preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $user["username"]); ?>" value="yes" /></td></tr><?php
		}
	}

	// Check users not verified by admin
	foreach($users as $key=>$user) {
		if (($user["verified_by_admin"]!="yes") && ($user["verified"] == "yes")) {
			?><tr><td  class="descriptionbox"><?php print $user["username"]." - ".$user["firstname"]." ".$user["lastname"].":&nbsp;&nbsp;".$pgv_lang["del_unvera"]; 
			?></td><td class="optionbox"><input type="checkbox" name="<?php print "del_".preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $user["username"]); ?>" value="yes" /></td></tr><?php
			$ucnt++;
		}
	}
	
	// Then check obsolete gedcom rights
	$gedrights = array();
	foreach($users as $key=>$user) {
		foreach($user["canedit"] as $gedid=>$data) {
			if ((!isset($GEDCOMS[$gedid])) && (!in_array($gedid, $gedrights))) $gedrights[] = $gedid;
		}
		foreach($user["gedcomid"] as $gedid=>$data) {
			if ((!isset($GEDCOMS[$gedid])) && (!in_array($gedid, $gedrights))) $gedrights[] = $gedid;
		}
		foreach($user["rootid"] as $gedid=>$data) {
			if ((!isset($GEDCOMS[$gedid])) && (!in_array($gedid, $gedrights))) $gedrights[] = $gedid;
		}
	}
	ksort($gedrights);
	foreach($gedrights as $key=>$ged) {
		?><tr><td class="descriptionbox"><?php print $ged.":&nbsp;&nbsp;".$pgv_lang["del_gedrights"]; 
		?></td><td class="optionbox"><input type="checkbox" checked="checked" name="<?php print "delg_".preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $ged); ?>" value="yes" /></td></tr><?php
		$ucnt++;
	}
	if ($ucnt == 0) {
		print "<tr><td class=\"warning\">";
		print $pgv_lang["usr_no_cleanup"]."</td></tr>";
	}?>
	<tr><td class="topbottombar" colspan="2">
	<?php
	if ($ucnt >0) {
		?><input type="submit" value="<?php print $pgv_lang["del_proceed"]; ?>" />&nbsp;<?php
	}?>
	<input type="button" value="<?php print $pgv_lang["back"];?>" onclick="window.location='useradmin.php';"/>
	</td></tr></table>
	</form><?php
	print_footer();
	exit;
}
// NOTE: No table parts
if ($action == "cleanup2") {
	$users = getUsers();
	foreach($users as $key=>$user) {
		$var = "del_".preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $user["username"]);
		if (isset($$var)) {
			deleteUser($key);
			print $pgv_lang["usr_deleted"]; print $user["username"]."<br />";
		}
		else {
			foreach($user["canedit"] as $gedid=>$data) {
				$var = "delg_".preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $gedid);
				if (isset($$var)) {
					unset($user["canedit"][$gedid]);
					print $gedid.":&nbsp;&nbsp;".$pgv_lang["usr_unset_rights"].$user["username"]."<br />";
					if (isset($user["rootid"][$gedid])) {
						unset($user["rootid"][$gedid]);
						print $gedid.":&nbsp;&nbsp;".$pgv_lang["usr_unset_rootid"].$user["username"]."<br />";
					}
					if (isset($user["gedcomid"][$gedid])) {
						unset($user["gedcomid"][$gedid]);
						print $gedid.":&nbsp;&nbsp;".$pgv_lang["usr_unset_gedcomid"].$user["username"]."<br />";
					}
					//deleteUser($key, "changed");
					updateUser($key, $user, "changed");
				}
			}
		}
	}
	print "<br />";
}

// Print main menu
// NOTE: WORKING
?>
<table class="center list_table width40 <?php print $TEXT_DIRECTION; ?>">
	<tr>
		<td class="facts_label" colspan="3">
		<h2><?php print $pgv_lang["user_admin"];?></h2>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="topbottombar"><?php print $pgv_lang["select_an_option"]; ?></td>
	</tr>
	<tr>
		<td class="optionbox"><a href="useradmin.php?action=listusers"><?php print $pgv_lang["current_users"];?></a></td>
		<td class="optionbox"><a href="useradmin.php?action=createform"><?php print $pgv_lang["add_user"];?></a></td>
	</tr>
	<tr>
		<td class="optionbox"><a href="useradmin.php?action=cleanup"><?php print $pgv_lang["cleanup_users"];?></a></td>
		<td class="optionbox">
			<a href="javascript: <?php print $pgv_lang["message_to_all"]; ?>" onclick="message('all', 'messaging2', '', ''); return false;"><?php print $pgv_lang["message_to_all"]; ?></a><br />
			<a href="javascript: <?php print $pgv_lang["broadcast_never_logged_in"]; ?>" onclick="message('never_logged', 'messaging2', '', ''); return false;"><?php print $pgv_lang["broadcast_never_logged_in"]; ?></a><br />
			<a href="javascript: <?php print $pgv_lang["broadcast_not_logged_6mo"]; ?>" onclick="message('last_6mo', 'messaging2', '', ''); return false;"><?php print $pgv_lang["broadcast_not_logged_6mo"]; ?></a><br />
		</td>
	</tr>
	<tr>
		<td class="topbottombar" colspan="2" align="center" ><a href="admin.php"><?php print $pgv_lang["lang_back_admin"]; ?></a></td>
	</tr>
	<tr>
		<td colspan="3" class="topbottombar"><?php print $pgv_lang["admin_info"]; ?></td>
	</tr>
	<tr>
      	<td class="optionbox" colspan="3">
	<?php
	$users = getUsers();
	$totusers = 0;			// Total number of users
	$warnusers = 0;			// Users with warning
	$applusers = 0;			// Users who have not verified themselves
	$nverusers = 0;			// Users not verified by admin but verified themselves
	$adminusers = 0;		// Administrators
	$userlang = array();	// Array for user languages
	$gedadmin = array();	// Array for gedcom admins
	foreach($users as $username=>$user) {
		if (empty($user["language"])) $user["language"]=$LANGUAGE;
		$totusers = $totusers + 1;
		if (((date("U") - $user["reg_timestamp"]) > 604800) && ($user["verified"]!="yes")) $warnusers++;
		else {
			if (!empty($user["comment_exp"])) {
				if ((strtotime($user["comment_exp"]) != "-1") && (strtotime($user["comment_exp"]) < time("U"))) $warnusers++;
			}
		}
		if (($user["verified_by_admin"] != "yes") && ($user["verified"] == "yes")) $nverusers++;
		if ($user["verified"] != "yes") $applusers++;
		if ($user["canadmin"]) $adminusers++;
		foreach($user["canedit"] as $gedid=>$rights) {
			if ($rights == "admin") {
				if (isset($GEDCOMS[$gedid])) {
					if (isset($gedadmin[$GEDCOMS[$gedid]["title"]])) $gedadmin[$GEDCOMS[$gedid]["title"]]["number"]++;
					else {
						$gedadmin[$GEDCOMS[$gedid]["title"]]["name"] = $GEDCOMS[$gedid]["title"];
						$gedadmin[$GEDCOMS[$gedid]["title"]]["number"] = 1;
						$gedadmin[$GEDCOMS[$gedid]["title"]]["ged"] = $gedid;
					}
				}
			}
		}
		if (isset($userlang[$pgv_lang["lang_name_".$user["language"]]])) $userlang[$pgv_lang["lang_name_".$user["language"]]]["number"]++;
		else {
			$userlang[$pgv_lang["lang_name_".$user["language"]]]["langname"] = $user["language"];
			$userlang[$pgv_lang["lang_name_".$user["language"]]]["number"] = 1;
		}
	}
	print "<table class=\"width100 $TEXT_DIRECTION\">";
	print "<tr><td class=\"font11\">".$pgv_lang["users_total"]."</td><td class=\"font11\">".$totusers."</td></tr>";

	print "<tr><td class=\"font11\">";
	if ($adminusers == 0) print $pgv_lang["users_admin"];
	else print "<a href=\"useradmin.php?action=listusers&amp;filter=adminusers\">".$pgv_lang["users_admin"]."</a></td>";
	print "<td class=\"font11\">".$adminusers."</td></tr>";

	print "<tr><td class=\"font11\">".$pgv_lang["users_gedadmin"]."</td>";
	asort($gedadmin);
	$ind = 0;
	foreach ($gedadmin as $key=>$geds) {
		if ($ind !=0) print "<tr><td class=\"font11\"></td>";
		$ind = 1;
		print "<td class=\"font11\">";
		if ($geds["number"] == 0) print $geds["name"];
		else print "<a href=\"useradmin.php?action=listusers&amp;filter=gedadmin&amp;ged=".$geds["ged"]."\">".$geds["name"]."</a>";
		print "</td><td class=\"font11\">".$geds["number"]."</td></tr>";
	}
	print "<tr><td class=\"font11\"></td></tr><tr><td class=\"font11\">";
	if ($warnusers == 0) print $pgv_lang["warn_users"];
	else print "<a href=\"useradmin.php?action=listusers&amp;filter=warnings\">".$pgv_lang["warn_users"]."</a>";
	print "</td><td class=\"font11\">".$warnusers."</td></tr>";

	print "<tr><td class=\"font11\">";
	if ($applusers == 0) print $pgv_lang["users_unver"];
	else print "<a href=\"useradmin.php?action=listusers&amp;filter=usunver\">".$pgv_lang["users_unver"]."</a>";
	print "</td><td class=\"font11\">".$applusers."</td></tr>";
	
	print "<tr><td class=\"font11\">";
	if ($nverusers == 0) print $pgv_lang["users_unver_admin"];
	else print "<a href=\"useradmin.php?action=listusers&amp;filter=admunver\">".$pgv_lang["users_unver_admin"]."</a>";
	print "</td><td class=\"font11\">".$nverusers."</td></tr>";

	asort($userlang);
	print "<tr valign=\"middle\"><td class=\"font11\">".$pgv_lang["users_langs"]."</td>";
	foreach ($userlang as $key=>$ulang) {
		print "\t<td class=\"font11\"><img src=\"".$language_settings[$ulang["langname"]]["flagsfile"]."\" class=\"brightflag\" alt=\"".$key."\" title=\"".$key."\" /></td><td>&nbsp;<a href=\"useradmin.php?action=listusers&amp;filter=language&amp;usrlang=".$ulang["langname"]."\">".$key."</a></td><td>".$ulang["number"]."</td></tr><tr class=\"vmiddle\"><td></td>\n";
	}
	print "</tr></table>";
	print "</td></tr></table>";
	 ?>
<?php
print_footer();
?>

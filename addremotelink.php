<?php
/**
 *  Add Remote Link Page
 *
 *  Allow a user the ability to add links to people from other servers and other gedcoms.
 *
 * phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
 * @subpackage Charts
 * @version $Id$
 */

require 'config.php';

require './includes/controllers/remotelink_ctrl.php';

$controller=new RemoteLinkController();
$controller->init();

print_simple_header($pgv_lang['title_remote_link']);

$pid=safe_REQUEST($_REQUEST, 'pid', PGV_REGEX_XREF);
$action=safe_POST('action', array('addlink'));

//-- only allow gedcom admins to create remote links
if (!$controller->canAccess()) {
	echo '<span class="error">', $pgv_lang['access_denied'], '<br />';
	if (!PGV_USER_GEDCOM_ADMIN) {
		echo $pgv_lang['user_cannot_edit'];
	} else if (!$ALLOW_EDIT_GEDCOM) {
		echo $pgv_lang['gedcom_editing_disabled'];
	} else {
		echo $pgv_lang['privacy_prevented_editing'];
		if ($pid) {
			echo '<br />', $pgv_lang['privacy_not_granted'], ' ', $pid;
		}
	}
	echo '</span><br /><br /><div class="center"><a href="javascript://', $pgv_lang['close_window'], '" onclick="window.close();">', $pgv_lang['close_window'], '</a></div>';
	print_simple_footer();
	exit;
}

$success=$controller->runAction($action);

echo PGV_JS_START;
?>
function sameServer() {
	alert('<?php echo $pgv_lang["error_same"]; ?>');
}
function remoteServer() {
	alert('<?php echo $pgv_lang["error_remote"]; ?>');
}
function swapComponents(btnPressed) {
	var labelSite = document.getElementById('labelSite');
	var existingContent = document.getElementById('existingContent');
	var localContent = document.getElementById('localContent');
	var remoteContent = document.getElementById('remoteContent');
	if (btnPressed=="remote") {
		labelSite.innerHTML = '<?php echo $pgv_lang["label_site"]; ?>';
		existingContent.style.display='none';
		localContent.style.display='none';
		remoteContent.style.display='block';
	} else if (btnPressed=="local") {
		labelSite.innerHTML = '<?php echo $pgv_lang["label_gedcom_id"]; ?>';
		existingContent.style.display='none';
		localContent.style.display='block';
		remoteContent.style.display='none';
	} else {
		labelSite.innerHTML = '<?php echo $pgv_lang["label_site"]; ?>';
		existingContent.style.display='block';
		localContent.style.display='none';
		remoteContent.style.display='none';
	}
}
function edit_close() {
	if (window.opener.showchanges) window.opener.showchanges();
	window.close();
}
function checkform(frm) {
	if (frm.txtPID.value=='') {
		alert('Please enter all fields.');
		return false;
	}
	return true;
}
<?php
echo PGV_JS_END;

if (!$success) {
?>
<form method="post" name="addRemoteRelationship" action="addremotelink.php" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="addlink" />
<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
<span class="title">
	<?php echo PrintReady($controller->person->getFullName()), '&nbsp;', PrintReady("(".$controller->person->getXref().")"); ?>
</span><br /><br />
<table class="facts_table">
	<tr>
		<td class="title" colspan="2">
			<?php print_help_link("link_remote_help", "qm"); echo $pgv_lang['title_remote_link']; ?>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link('link_remote_rel_help', 'qm'); ?>
			<?php echo $pgv_lang["label_rel_to_current"]; ?>
		</td>
		<td class="optionbox">
			<select id="cbRelationship" name="cbRelationship">
				<?php
				foreach (array('current_person', 'mother', 'father', 'husband', 'wife', 'son', 'daughter') as $rel) {
					echo '<option value="', $rel, '"';
					if ($rel==$controller->form_cbRelationship) {
						echo ' checked="checked"';
					}
					echo '>', $pgv_lang[$rel], '</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<?php if ($controller->server_list || $controller->gedcom_list) { ?>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link('link_remote_location_help', 'qm'); ?>
		<?php echo $pgv_lang["label_location"]; ?></td>
		<td class="optionbox">
			<?php
				echo '<input type="radio" id="local" name="location" value="local" onclick="swapComponents(\'local\')"';
				if (!$controller->gedcom_list) {
					echo ' disabled';
				}
				if ($controller->form_location=='local') {
					echo ' checked="checked"';
				}
				echo '/>', $pgv_lang['label_same_server'], '&nbsp;&nbsp;&nbsp';
				echo '<input type="radio" id="existing" name="location" value="existing" onclick="swapComponents(\'existing\');"';
				if (!$controller->server_list) {
					echo ' disabled';
				}
				if ($controller->form_location=='existing') {
					echo ' checked="checked"';
				}
				echo '/>', $pgv_lang['lbl_server_list'], '&nbsp;&nbsp;&nbsp;';
				echo '<input type="radio" id="remote" name="location" value="remote" onclick="swapComponents(\'remote\');"';
				if ($controller->form_location=='remote') {
					echo ' checked="checked"';
				}
				echo '/>', $pgv_lang['label_diff_server'];
			?>
		</td>
	</tr>
	<?php } ?>

	<tr>
		<td class="descriptionbox wrap width20">
			<?php
				print_help_link('link_person_id_help', 'qm');
				echo $pgv_lang["label_local_id"];
			?>
		</td>
		<td class="optionbox">
			<input type="text" id="txtPID" name="txtPID" size="14" value="<?php echo $controller->form_txtPID; ?>" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox wrap width20"><?php print_help_link('link_remote_site_help', 'qm'); ?>
			<span id="labelSite">
				<?php echo $pgv_lang['label_site']; ?>
			</span>
		</td>
		<td class="optionbox" id="tdUrlText">
			<div id="existingContent">
				<?php echo $pgv_lang['lbl_server_list']; ?><br />
				<select id="cbExistingServers" name="cbExistingServers"	style="width: 400px;">
					<?php
						foreach ($controller->server_list as $key=>$server) {
							echo '<option value="', $key, '"';
							if ($key==$controller->form_cbExistingServers) {
								echo ' selected="selected"';
							}
							echo '/>', PrintReady($server['name']), '</option>';
						}
					?>
				</select><br /><br />
			</div>
			<div id="remoteContent">
				<?php echo $pgv_lang['lbl_type_server']; ?>
				<table>
					<tr>
						<td ><?php echo $pgv_lang["title"]; ?></td>
						<td><input type="text" id="txtTitle" name="txtTitle" size="66" value="<?php echo $controller->form_txtTitle; ?>" /></td>
					</tr><tr>
						<td valign="top"><?php echo $pgv_lang["label_site_url"]; ?></td>
						<td><input type="text" id="txtURL" name="txtURL" size="66" value="<?php echo $controller->form_txtURL; ?>" />
						<br /><?php echo $pgv_lang['example']; ?>&nbsp;&nbsp;http://www.remotesite.com/phpGedView/genservice.php?wsdl</td>
					</tr><tr>
						<td><?php echo $pgv_lang["label_gedcom_id2"]; ?></td>
						<td><input type="text" id="txtGID" name="txtGID" value="<?php echo $controller->form_txtGID; ?>" /></td>
					</tr><tr>
						<td><?php echo $pgv_lang["label_username_id2"]; ?></td>
						<td><input type="text" id="txtUsername" name="txtUsername" value="<?php echo $controller->form_txtUsername; ?>" /></td>
					</tr><tr>
						<td><?php echo $pgv_lang["label_password_id2"]; ?></td>
						<td><input type="password" id="txtPassword" name="txtPassword" value="<?php echo $controller->form_txtPassword; ?>" /></td>
					</tr>
				</table>
			</div>
			<div id="localContent">
			<table><tr>
					<td ><?php echo $pgv_lang["title"]; ?></td>
					<td><input type="text" id="txtCB_Title" name="txtCB_Title" size="66" value="<?php echo $controller->form_txtCB_Title; ?>" /></td>
				</tr><tr>
					<td valign="top"><?php echo $pgv_lang["gedcom_file"]; ?></td>
					<td><select id="txtCB_GID" name="txtCB_GID">
					<?php
						foreach ($controller->gedcom_list as $ged_name) {
							echo '<option value="', $ged_name, '"';
							if ($ged_name==$controller->form_txtCB_GID) {
								echo ' selected="selected"';
							}
							echo '>', $ged_name, '</option>';
						}
					?>
					</select></td>
				</tr></table>
			</div>
		</td>
	</tr>
</table>
<br />
<input type="submit" value="<?php echo $pgv_lang['label_add_remote_link']; ?>" id="btnSubmit" name="btnSubmit" />
</form>
<?php
	echo PGV_JS_START, 'swapComponents("', $controller->form_location, '");', PGV_JS_END;
}

// autoclose window when update successful
if ($success && $EDIT_AUTOCLOSE) {
	echo PGV_JS_START, 'edit_close();', PGV_JS_END;
} else {
	echo '<div class="center"><a href="javascript://', $pgv_lang['close_window'], '" onclick="edit_close();">', $pgv_lang['close_window'], '</a></div>';
	print_simple_footer();
}

?>

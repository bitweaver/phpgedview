<?php
/**
 *  Add Remote Link Page
 *
 *  Allow a user the ability to add links to people from other servers and other gedcoms.
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
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id: addremotelink.php,v 1.3 2007/05/27 17:49:22 lsces Exp $
 */

require_once("config.php");
require_once("includes/functions_edit.php");
require_once("includes/serviceclient_class.php");

//-- require that the user have entered their password
if ($_SESSION["cookie_login"]) {
	header("Location: login.php?type=simple&ged=$GEDCOM&url=edit_interface.php".urlencode("?".$QUERY_STRING));
	exit;
}
$success = false;
//check for pid
if(!isset($pid)){
	$pid="";
	$name="no name passed";
	$disp = false;
}
else{
	$pid = clean_input($pid);
	$name = get_person_name($pid);
	if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_person_record($pid);
	else $gedrec = find_updated_record($pid);
	if (empty($gedrec)) $gedrec =  find_record_in_file($pid);
	$disp = displayDetailsById($pid);
	$server_list = get_server_list();
}

if (!isset($action)) $action = "";
print_simple_header($pgv_lang["title_remote_link"]);

//-- only allow gedcom admins to create remote links
if ((!userGedcomAdmin(getUserName()))||(!$disp)||(!$ALLOW_EDIT_GEDCOM)) {
	//print "pid: $pid<br />";
	//print "gedrec: $gedrec<br />";
	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!userGedcomAdmin(getUserName())) print "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
		if (!empty($famid)) print "<br />".$pgv_lang["privacy_not_granted"]." famid $famid.";
	}
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".$pgv_lang["close_window"]."\" onclick=\"window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}

if ($action=="addlink") {
	$pid = $_POST["pid"];
	$link_pid = $_POST["txtPID"];
	//print "Link PID= ".$link_pid;
	$relation_type = $_POST["cbRelationship"];

	$is_remote = $_POST["location"];
	if($is_remote=="remote") {
		if(empty($_POST["txtURL"])) {
			$serverID = $_POST["cbExistingServers"];
			//print $_POST["cbExistingServers"];
		}
		else {
			if (isset($_POST["txtURL"])) $server_name = $_POST["txtURL"];
			else $server_name = "";
			if (isset($_POST["txtGID"]))$gedcom_id = $_POST["txtGID"];
			else $gedcom_id = "";
			if (isset($_POST["txtUsername"])) $username = $_POST["txtUsername"];
			else $username = "";
			if (isset($_POST["txtPassword"])) $password = $_POST["txtPassword"];
			else $password = "";
			$gedcom_string = "0 @new@ SOUR\r\n";
			$gedcom_string.= "1 URL ".$server_name."\r\n";
			$gedcom_string.= "1 _DBID ".$gedcom_id."\r\n";
			$gedcom_string.= "2 _USER ".$username."\r\n";
			$gedcom_string.= "2 _PASS ".$password."\r\n";
			//-- only allow admin users to see password
			$gedcom_string.= "2 RESN Confidential\r\n";
			$service = new ServiceClient($gedcom_string);
			$sid = $service->authenticate();
			if (PEAR::isError($sid)) {
				print "<span class=\"error\">failed to authenticate to remote site</span>";
				print_r($sid);
			}
			if (!empty($sid)) {
				$title = $service->getServiceTitle();
				$gedcom_string.= "1 TITL ".$title."\r\n";
				$serverID = append_gedrec($gedcom_string);
			}
			else print "<span class=\"error\">failed to authenticate to remote site</span>";
		}
	}
	else {
		$gedcom_id = $_POST["cbGedcomId"];
		$server_name = $SERVER_URL;

		$gedcom_string = "0 @new@ SOUR\r\n";
		$title = $server_name;
		if (isset($GEDCOMS[$gedcom_id])) $title = $GEDCOMS[$gedcom_id]["title"];
		$gedcom_string.= "1 TITL ".$title."\r\n";
		$gedcom_string.= "1 URL ".$SERVER_URL."\r\n";
		$gedcom_string.= "1 _DBID ".$gedcom_id."\r\n";
		$gedcom_string.= "2 _BLOCK false\r\n";
		$serverID = append_gedrec($gedcom_string);
	}

	if (!empty($serverID)&&!empty($link_pid)) {
		if (isset($pgv_changes[$pid."_".$GEDCOM])) $indirec = find_updated_record($pid);
		else $indirec = find_person_record($pid);

		if($relation_type=="father"){
			$indistub = "0 @new@ INDI\r\n";
			$indistub .= "1 SOUR @".$serverID."@\r\n";
			$indistub .= "2 PAGE ".$link_pid."\r\n";
			$indistub .= "1 RFN ".$serverID.":".$link_pid."\r\n";
			$stub_id = append_gedrec($indistub, false);
			$indistub = find_updated_record($stub_id);

			$gedcom_fam = "0 @new@ FAM\r\n";
			$gedcom_fam.= "1 HUSB @".$stub_id."@\r\n";
			$gedcom_fam.= "1 CHIL @".$pid."@\r\n";
			$fam_id = append_gedrec($gedcom_fam);

			$indirec.= "\r\n";
			$indirec.= "1 FAMC @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($pid, $indirec);

			$serviceClient = ServiceClient::getInstance($serverID);
			$indistub = $serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
			$indistub.= "\r\n1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($stub_id, $indistub, false);
		}else if($relation_type=="mother"){
			$indistub = "0 @NEW@ INDI\r\n";
			$indistub .= "1 SOUR @".$serverID."@\r\n";
			$indistub .= "2 PAGE ".$link_pid."\r\n";
			$indistub .= "1 RFN ".$serverID.":".$link_pid."\r\n";
			$stub_id = append_gedrec($indistub, false);
			$indistub = find_updated_record($stub_id);

			$gedcom_fam = "0 @NEW@ FAM\r\n";
			$gedcom_fam.= "1 WIFE @".$stub_id."@\r\n";
			$gedcom_fam.= "1 CHIL @".$pid."@\r\n";
			$fam_id = append_gedrec($gedcom_fam);

			$indirec.= "\r\n";
			$indirec.= "1 FAMC @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($pid, $indirec);

			$serviceClient = ServiceClient::getInstance($serverID);
			$indistub = $serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
			$indistub.= "\r\n1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($stub_id, $indistub, false);
		}else if($relation_type=="husband"){
			$indistub = "0 @NEW@ INDI\r\n";
			$indistub .= "1 SOUR @".$serverID."@\r\n";
			$indistub .= "2 PAGE ".$link_pid."\r\n";
			$indistub .= "1 RFN ".$serverID.":".$link_pid."\r\n";
			$stub_id = append_gedrec($indistub, false);
			$indistub = find_updated_record($stub_id);

			$gedcom_fam = "0 @NEW@ FAM\r\n";
			$gedcom_fam.= "1 WIFE @".$pid."@\r\n";
			$gedcom_fam.= "1 HUSB @".$stub_id."@\r\n";
			$fam_id = append_gedrec($gedcom_fam);

			$indirec.= "\r\n";
			$indirec.= "1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($pid, $indirec);

			$serviceClient = ServiceClient::getInstance($serverID);
			$indistub = $serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
			$indistub.= "\r\n1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($stub_id, $indistub, false);
		}else if($relation_type=="wife"){
			$indistub = "0 @NEW@ INDI\r\n";
			$indistub .= "1 SOUR @".$serverID."@\r\n";
			$indistub .= "2 PAGE ".$link_pid."\r\n";
			$indistub .= "1 RFN ".$serverID.":".$link_pid."\r\n";
			$stub_id = append_gedrec($indistub, false);
			$indistub = find_updated_record($stub_id);

			$gedcom_fam = "0 @NEW@ FAM\r\n";
			$gedcom_fam.= "1 WIFE @".$stub_id."@\r\n";
			$gedcom_fam.= "1 HUSB @".$pid."@\r\n";
			$fam_id = append_gedrec($gedcom_fam);

			$indirec.= "\r\n";
			$indirec.= "1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($pid, $indirec);

			$serviceClient = ServiceClient::getInstance($serverID);
			$indistub = $serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
			$indistub.= "\r\n1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($stub_id, $indistub, false);
		}else if($relation_type=="son"||$relation_type=="daughter"){
			$indistub = "0 @NEW@ INDI\r\n";
			$indistub .= "1 SOUR @".$serverID."@\r\n";
			$indistub .= "2 PAGE ".$link_pid."\r\n";
			$indistub .= "1 RFN ".$serverID.":".$link_pid."\r\n";
			$stub_id = append_gedrec($indistub, false);
			$indistub = find_updated_record($stub_id);

			$sex = get_gedcom_value("SEX", 1, $indirec, '', false);
			if($sex=="M"){
				$gedcom_fam = "0 @NEW@ FAM\r\n";
				$gedcom_fam.= "1 HUSB @".$pid."@\r\n";
				$gedcom_fam.= "1 CHIL @".$stub_id."@\r\n";
			}else{
				$gedcom_fam = "0 @NEW@ FAM\r\n";
				$gedcom_fam.= "1 WIFE @".$pid."@\r\n";
				$gedcom_fam.= "1 CHIL @".$stub_id."@\r\n";
			}
			$fam_id = append_gedrec($gedcom_fam);
			$indirec.= "\r\n";
			$indirec.= "1 FAMS @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($pid, $indirec);

			$serviceClient = ServiceClient::getInstance($serverID);
			$indistub = $serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
			$indistub.= "\r\n1 FAMC @".$fam_id."@\r\n";
			$answer2 = replace_gedrec($stub_id, $indistub,false);
		}else if($relation_type=="self"){
			$indirec.="\r\n";
			$indirec.="1 RFN ".$serverID.":".$link_pid."\r\n";
			$indirec.="1 SOUR @".$serverID."@\r\n";

			$serviceClient = ServiceClient::getInstance($serverID);
			if (!is_null($serviceClient)) {
				//-- get rid of change date
				$pos1 = strpos($indirec, "\n1 CHAN");
				if ($pos1!==false) {
					$pos2 = strpos($indirec, "\n1", $pos1+5);
					if ($pos2===false) $indirec = substr($indirec, 0, $pos1+1);
					else $indirec= substr($indirec, 0, $pos1+1).substr($indirec, $pos2+1);
				}
				//print "{".$indirec."}";
				$indirec = $serviceClient->mergeGedcomRecord($link_pid, $indirec, true, true);
			}
			else print "Unable to find server";
			//$answer2 = replace_gedrec($pid, $indirec);
		}
		print "<b>".$pgv_lang["link_success"]."</b>";
		$success = true;
	}
}
?>

<script language="JavaScript" type="text/javascript">
<!--
function sameServer(){
  alert('<?php print $pgv_lang["error_same"];?>');
}
function remoteServer(){
  alert('<?php print $pgv_lang["error_remote"];?>');
}
function swapComponents(btnPressed){
    var tdId = document.getElementById('tdId');
    var tdblah = document.getElementById('tdUrl');
    var tdblah2 = document.getElementById('tdUrlText');
    var tdGIDLBL = document.getElementById('tdGIDLBL');
    var tdGID = document.getElementById('tdGID');

    if(btnPressed=="remote"){
      tdblah.innerHTML = '<?php print preg_replace(array("/'/", "/[\r\n]+/"), array("\\'", " "), print_help_link("link_remote_site_help", "qm", "", false, true));?> <?php echo $pgv_lang["label_site"];?>';
      tdblah2.innerHTML =  '<?php echo $pgv_lang["lbl_server_list"]; ?><br /><select id="cbExistingServers" name="cbExistingServers" style="width: 400px;"><?php if(isset($server_list)){foreach($server_list as $key=>$server){?><option value="<?php echo $key; ?>"><?php print $server['name'];?></option><?php }}?></select><br /><br />-or-<br /><br /><?php echo $pgv_lang["lbl_type_server"];?><br /><?php echo $pgv_lang["label_site_url"];?><input type="text" id="txtURL" name="txtURL" size="66"><br /><?php echo $pgv_lang["label_gedcom_id2"];?><input type="text" id="txtGID" name="txtGID" size="14"/><br /><?php echo $pgv_lang["label_username_id2"];?><input type="text" id="txtUsername" name="txtUsername" size="20"/><br /><?php echo $pgv_lang["label_password_id2"];?>&nbsp;<input type="password" id="txtPassword" name="txtPassword" size="20"/>';
      tdId.innerHTML = '<?php print preg_replace(array("/'/", "/[\r\n]+/"), array("\\'", " "), print_help_link("link_person_id_help", "qm", "", false, true));?> <?php echo $pgv_lang['label_remote_id'];?>';

    }else{
        tdblah.innerHTML = '<?php print preg_replace(array("/'/", "/[\r\n]+/"), array("\\'", " "), print_help_link("link_gedcom_id_help", "qm", "", false, true));?> <?php echo $pgv_lang['label_gedcom_id'];?>';
        tdId.innerHTML = '<?php print preg_replace(array("/'/", "/[\r\n]+/"), array("\\'", " "), print_help_link("link_person_id_help", "qm", "", false, true));?> <?php echo $pgv_lang['label_local_id'];?>';
        tdblah2.innerHTML = '<select id="cbGedcomId" name="cbGedcomId" style="width: 200px;"><?php foreach($GEDCOMS as $ged){?><option><?php print $ged["gedcom"];?></option><?php }?></select><br />';

    }
}

function edit_close() {
	if (window.opener.showchanges) window.opener.showchanges();
	window.close();
}

function checkform(frm){
	if (frm.txtPID.value=='') {
		alert('Please enter all fields.');
		return false;
	}
	return true;
}
//-->
</script>
<?php if ($action!="addlink") { ?>
<form method="post" name="addRemoteRelationship"
	action="addremotelink.php" onsubmit="return checkform(this);"><input
	type="hidden" name="action" value="addlink" /> <input type="hidden"
	name="pid" value="<?php print $pid;?>"/> <input type="hidden" name="indi_rec"
	value="<?php print $indirec;?>"/> <?php echo $name;?> <br />
<br />
<table class="facts_table">
	<tr>
		<td class="title" colspan="2"><?php print_help_link("link_remote_help", "qm"); ?> <?php echo $pgv_lang["title_remote_link"];?></td>
	</tr>
	<tr>
		<td class="descriptionbox width20"><?php print_help_link('link_remote_rel_help', 'qm');?> <?php echo $pgv_lang["label_rel_to_current"];?></td>
		<td class="optionbox"><select id="cbRelationship"
			name="cbRelationship">
			<option value="mother" selected><?php echo $pgv_lang["mother"];?></option>
			<option value="father"><?php echo $pgv_lang["father"];?></option>
			<option value="husband"><?php echo $pgv_lang["husband"];?></option>
			<option value="wife"><?php echo $pgv_lang["wife"];?></option>
			<option value="son"><?php echo $pgv_lang["son"];?></option>
			<option value="daughter"><?php echo $pgv_lang["daughter"];?></option>
			<option value="self"><?php echo $pgv_lang["current_person"];?></option>
		</select></td>
	</tr>
	<tr>
		<td class="descriptionbox width20"><?php print_help_link('link_remote_location_help', 'qm');?> <?php echo $pgv_lang["label_location"];?></td>
		<td class="optionbox"><input type="radio" id="local"
			name="location" value="local" onclick="swapComponents('')" />
			<?php echo $pgv_lang["label_same_server"];?>&nbsp;&nbsp;&nbsp;
		<input type="radio" id="remote" name="location" value="remote" checked
			onclick="swapComponents('remote')" />
			<?php echo $pgv_lang["label_diff_server"];?></td>
	</tr>
	<tr>
		<td class="descriptionbox width20" id="tdUrl"><?php print_help_link('link_remote_site_help', 'qm');?> <?php echo $pgv_lang["label_site"];?></td>
		<td class="optionbox" id="tdUrlText">
		<?php echo $pgv_lang["lbl_server_list"]; ?><br />
		<select id="cbExistingServers" name="cbExistingServers"
			style="width: 400px;">
			<?php
			if(isset($server_list)){
				foreach($server_list as $key=>$server){?>

			<option value="<?php echo $key; ?>"><?php print $server['name'];?></option>

			<?php
			}
}
?>
		</select> <br />
		<br />
		-or-<br />
		<br />
		<?php echo $pgv_lang["lbl_type_server"];?><br />
		<?php echo $pgv_lang["label_site_url"];?><input type="text" id="txtURL" name="txtURL" size="66"><br />
		<?php echo $pgv_lang["label_gedcom_id2"];?><input
			type="text" id="txtGID" name="txtGID" size="14" /><br />
			<?php echo $pgv_lang["label_username_id2"];?><input
			type="text" id="txtUsername" name="txtUsername" size="20" /><br />
			<?php echo $pgv_lang["label_password_id2"];?>&nbsp;<input
			type="password" id="txtPassword" name="txtPassword" size="20" /></td>
	</tr>
	<tr>
		<td class="descriptionbox width20" id="tdId"><?php print_help_link('link_person_id_help', 'qm');?> <?php echo $pgv_lang["label_remote_id"];?></td>
		<td class="optionbox"><input type="text" id="txtPID"
			name="txtPID" size="14" /></td>
	</tr>
</table>
<br />
<input type="submit" value="<?php echo $pgv_lang['label_add_remote_link'];?>" id="btnSubmit" name="btnSubmit"
value="add"/></form>
<?php
}
// autoclose window when update successful
if ($success and $EDIT_AUTOCLOSE) print "\n<script type=\"text/javascript\">\n<!--\nedit_close();\n//-->\n</script>";

print "<div class=\"center\"><a href=\"javascript:// ".$pgv_lang["close_window"]."\" onclick=\"edit_close();\">".$pgv_lang["close_window"]."</a></div><br />\n";

print_simple_footer();
?>

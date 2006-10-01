<?php
/**
 * Allow an admin user to download the entire gedcom	file.
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
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: downloadgedcom.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 */

require "config.php";

if ((!userGedcomAdmin(getUserName()))||(empty($ged))) {
	header("Location: editgedcoms.php");
	exit;
}
if (!isset($action)) $action="";
if (!isset($remove)) $remove="no";
if (!isset($convert)) $convert="no";
if (!isset($zip)) $zip="no";
if (!isset($privatize_export)) $privatize_export = "";

if ($action=="download" && $zip == "yes") {
	require "includes/pclzip.lib.php";
	$temppath = $INDEX_DIRECTORY."tmp/";
	$zipname = "dl".adodb_date("YmdHis").$ged.".zip";
	$zipfile = $INDEX_DIRECTORY.$zipname;
	$gedname = $temppath.$ged;
	
	$removeTempDir = false;
	if (!is_dir(filename_decode($temppath))) {
		$res = mkdir(filename_decode($temppath));
		if ($res !== true) {
			print "Error : Could not create temporary path!";
			exit;
		}
		$removeTempDir = true;
	}
	$gedout = fopen(filename_decode($gedname), "w");
	print_gedcom();
	fclose($gedout);
	$comment = "Created by PhpGedView ".$VERSION." ".$VERSION_RELEASE." on ".adodb_date("r").".";
	$archive = new PclZip(filename_decode($zipfile));
	$v_list = $archive->create(filename_decode($gedname), PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, filename_decode($temppath));
	if ($v_list == 0) print "Error : ".$archive->errorInfo(true);
	else {
		unlink(filename_decode($gedname));
		if ($removeTempDir) rmdir(filename_decode($temppath));
		header("Location: downloadbackup.php?fname=".rawurlencode($zipname));
		exit;
	}
	exit;
}

if ($action=="download") {

	header("Content-Type: text/plain; charset=$CHARACTER_SET");
	header("Content-Disposition: attachment; filename=$ged; size=".filesize($GEDCOMS[$GEDCOM]["path"]));
	print_gedcom();
}
else {
	print_header($pgv_lang["download_gedcom"]);
	?>
	<div class="center">
	<h2><?php print $pgv_lang["download_gedcom"]; ?></h2>
	<br />
	<form name="convertform" method="post">
		<input type="hidden" name="action" value="download" />
		<input type="hidden" name="ged" value="<?php print $ged; ?>" />
		<table class="list_table" border="0" align="center" valign="top">
		<tr><td colspan="2" class="facts_label03" style="text-align:center;">
		<?php print $pgv_lang["options"]; ?>
		</td></tr>
		<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php  print_help_link("utf8_ansi_help", "qm"); print $pgv_lang["utf8_to_ansi"]; ?></td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><input type="checkbox" name="convert" value="yes" /></td></tr>
		<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print_help_link("remove_tags_help", "qm"); print $pgv_lang["remove_custom_tags"]; ?></td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><input type="checkbox" name="remove" value="yes" checked="checked" /></td></tr>
		<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print_help_link("download_zipped_help", "qm"); print $pgv_lang["download_zipped"]; ?></td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><input type="checkbox" name="zip" value="yes" checked="checked" /></td></tr>
		<tr><td class="list_label" valign="baseline" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print_help_link("apply_privacy_help", "qm"); print $pgv_lang["apply_privacy"]; ?>
			<div id="privtext" style="display: none"></div>
			</td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
			<input type="checkbox" name="privatize_export" value="yes" onclick="expand_layer('privtext'); expand_layer('privradio');" />
			<div id="privradio" style="display: none"><br /><?php print $pgv_lang["choose_priv"]; ?><br />
			<input type="radio" name="privatize_export_level" value="visitor" checked="checked" />
			<?php print $pgv_lang["visitor"]; ?><br />
			<input type="radio" name="privatize_export_level" value="user" /><?php print $pgv_lang["user"]; ?><br />
			<input type="radio" name="privatize_export_level" value="gedadmin" /><?php print $pgv_lang["gedadmin"]; ?><br />
			<input type="radio" name="privatize_export_level" value="admin" /><?php print $pgv_lang["siteadmin"]; ?><br />
		</div></td>
		</tr>
		<tr><td class="facts_label03" colspan="2" style="padding: 5px; ">
		<input type="submit" value="<?php print $pgv_lang["download_now"]; ?>" />
		<input type="button" value="<?php print $pgv_lang["back"];?>" onclick="window.location='editgedcoms.php';"/></td></tr>
		</table><br />
	<br /><br />
	</form>
	<?php
	print $pgv_lang["download_note"]."<br /><br /><br />\n";
	print "</div>";
	print_footer();
}

function print_gedcom() {
	global $GEDCOMS, $GEDCOM, $ged, $convert, $remove, $zip, $VERSION, $VERSION_RELEASE, $pgv_lang, $gedout;
	global $privatize_export, $privatize_export_level;
	global $TBLPREFIX;

	if ($privatize_export == "yes") {
		create_export_user($privatize_export_level);
		if (isset($_SESSION)) {
			$_SESSION["org_user"] = $_SESSION["pgv_user"];
			$_SESSION["pgv_user"] = "export";
		}
		if (isset($HTTP_SESSION_VARS)) {
			$HTTP_SESSION_VARS["org_user"] = $HTTP_SESSION_VARS["pgv_user"];
			$HTTP_SESSION_VARS["pgv_user"] = "export";
		}
	}
	
	$GEDCOM = $ged;
	//$indilist = get_indi_list();
	//$famlist = get_fam_list();
	//$sourcelist = get_source_list();
	//$otherlist = get_other_list();

	$head = find_gedcom_record("HEAD");
	if (!empty($head)) {
		$pos1 = strpos($head, "1 SOUR");
		if ($pos1!==false) {
			$pos2 = strpos($head, "\n1", $pos1+1);
			if ($pos2===false) $pos2 = strlen($head);
			$newhead = substr($head, 0, $pos1);
			$newhead .= substr($head, $pos2+1);
			$head = $newhead;
		}
		$pos1 = strpos($head, "1 DATE ");
		if ($pos1!=false) {
			$pos2 = strpos($head, "\n1", $pos1+1);
			if ($pos2===false) {
				$head = substr($head, 0, $pos1);
			}
			else {
				$head = substr($head, 0, $pos1).substr($head, $pos2+1);
			}
		}
		$head = trim($head);
		$head .= "\r\n1 SOUR PhpGedView\r\n2 NAME PhpGedView Online Genealogy\r\n2 VERS $VERSION $VERSION_RELEASE\r\n";
		$head .= "1 DATE ".date("j M Y")."\r\n";
		$head .= "2 TIME ".date("h:i:s")."\r\n";
		if (strstr($head, "1 PLAC")===false) {
			$head .= "1 PLAC\r\n2 FORM ".$pgv_lang["default_form"]."\r\n";
		}
	}
	else {
		$head = "0 HEAD\r\n1 SOUR PhpGedView\r\n2 NAME PhpGedView Online Genealogy\r\n2 VERS $VERSION $VERSION_RELEASE\r\n1 DEST DISKETTE\r\n1 DATE ".date("j M Y")."\r\n2 TIME ".date("h:i:s")."\r\n";
		$head .= "1 GEDC\r\n2 VERS 5.5\r\n2 FORM LINEAGE-LINKED\r\n1 CHAR $CHARACTER_SET\r\n1 PLAC\r\n2 FORM ".$pgv_lang["default_form"]."\r\n";
	}
	if ($convert=="yes") {
		$head = preg_replace("/UTF-8/", "ANSI", $head);
		$head = utf8_decode($head);
	}
	$head = remove_custom_tags($head, $remove);
	$head = preg_replace(array("/(\r\n)+/", "/\r+/", "/\n+/"), array("\r\n", "\r", "\n"), $head);
	if ($zip == "yes") fwrite($gedout, $head);
	else print $head;
	
	$sql = "SELECT i_gedcom FROM ".$TBLPREFIX."individuals WHERE i_file=".$GEDCOMS[$GEDCOM]['id']." ORDER BY i_id";
	$res = dbquery($sql); 
	while($row = $res->fetchRow()) {
		$rec = trim($row['i_gedcom'])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($privatize_export == "yes") $rec = privatize_gedcom($rec);
		if ($convert=="yes") $rec = utf8_decode($rec);
		if ($zip == "yes") fwrite($gedout, $rec);
		else print $rec;
	}
	$res->free();
	
	$sql = "SELECT f_gedcom FROM ".$TBLPREFIX."families WHERE f_file=".$GEDCOMS[$GEDCOM]['id']." ORDER BY f_id";
	$res = dbquery($sql); 
	while($row = $res->fetchRow()) {
		$rec = trim($row['f_gedcom'])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($privatize_export == "yes") $rec = privatize_gedcom($rec);
		if ($convert=="yes") $rec = utf8_decode($rec);
		if ($zip == "yes") fwrite($gedout, $rec);
		else print $rec;
	}
	$res->free();
	
	$sql = "SELECT s_gedcom FROM ".$TBLPREFIX."sources WHERE s_file=".$GEDCOMS[$GEDCOM]['id']." ORDER BY s_id";
	$res = dbquery($sql); 
	while($row = $res->fetchRow()) {
		$rec = trim($row['s_gedcom'])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($privatize_export == "yes") $rec = privatize_gedcom($rec);
		if ($convert=="yes") $rec = utf8_decode($rec);
		if ($zip == "yes") fwrite($gedout, $rec);
		else print $rec;
	}
	$res->free();
	
	$sql = "SELECT o_gedcom, o_type FROM ".$TBLPREFIX."other WHERE o_file=".$GEDCOMS[$GEDCOM]['id']." ORDER BY o_id";
	$res = dbquery($sql); 
	while($row = $res->fetchRow()) {
		$rec = trim($row['o_gedcom'])."\r\n";
		$key = $row['o_type'];
		if (($key!="HEAD")&&($key!="TRLR")) {
			$rec = remove_custom_tags($rec, $remove);
			if ($privatize_export == "yes") $rec = privatize_gedcom($rec);
			if ($convert=="yes") $rec = utf8_decode($rec);
			if ($zip == "yes") fwrite($gedout, $rec);
			else print $rec;
		}
	}
	$res->free();
	
	$sql = "SELECT o_gedcom FROM ".$TBLPREFIX."media WHERE m_gedfile=".$GEDCOMS[$GEDCOM]['id']." ORDER BY m_media";
	$res = dbquery($sql); 
	while($row = $res->fetchRow()) {
		$rec = trim($row['o_gedcom'])."\r\n";
		$rec = remove_custom_tags($rec, $remove);
		if ($privatize_export == "yes") $rec = privatize_gedcom($rec);
		if ($convert=="yes") $rec = utf8_decode($rec);
		if ($zip == "yes") fwrite($gedout, $rec);
		else print $rec;
	}
	$res->free();
	
	if ($zip == "yes") fwrite($gedout, "0 TRLR\r\n");
	else print "0 TRLR\r\n";
	
	if ($privatize_export == "yes") {
		if (isset($_SESSION)) {
			$_SESSION["pgv_user"] = $_SESSION["org_user"];
		}
		if (isset($HTTP_SESSION_VARS)) {
			$HTTP_SESSION_VARS["pgv_user"] = $HTTP_SESSION_VARS["org_user"];
		}
		deleteuser("export");
	}
}
?>

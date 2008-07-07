<?php

/**
 * Allow an admin user to download the entire gedcom	file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and Others, all rights reserved.
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
 * @version $Id: downloadgedcom.php,v 1.11 2008/07/07 18:01:12 lsces Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require_once("config.php");
require_once("includes/functions_export.php");

if ((!userGedcomAdmin(getUserName())) || (empty ($ged))) {
	header("Location: editgedcoms.php");
	exit;
}
if (!isset ($action))
	$action = "";
if (!isset ($remove))
	$remove = "no";
if (!isset ($convert))
	$convert = "no";
if (!isset ($zip))
	$zip = "no";
if (!isset ($privatize_export))
	$privatize_export = "no";

if ($action == "download" && $zip == "yes") {
	require "includes/pclzip.lib.php";

	$temppath = $INDEX_DIRECTORY . "tmp/";
	$fileName = $ged;
	if($filetype =="gramps")
		$fileName = $ged.".gramps";
	$zipname = "dl" . date("YmdHis") . $fileName . ".zip";
	$zipfile = $INDEX_DIRECTORY . $zipname;
	$gedname = $temppath . $fileName;

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
	switch ($filetype) {
	case 'gedcom':
		print_gedcom($privatize_export, $privatize_export_level, $convert, $remove, $gedout);
		break;
	case 'gramps':
		print_gramps($privatize_export, $privatize_export_level, $convert, $remove, $gedout);
		break;
	}
	fclose($gedout);
	$comment = "Created by PhpGedView " . $VERSION . " " . $VERSION_RELEASE . " on " . date("r") . ".";
	$archive = new PclZip(filename_decode($zipfile));
	$v_list = $archive->create(filename_decode($gedname), PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, filename_decode($temppath));
	if ($v_list == 0)
		print "Error : " . $archive->errorInfo(true);
	else {
		unlink(filename_decode($gedname));
		if ($removeTempDir)
			rmdir(filename_decode($temppath));
		header("Location: downloadbackup.php?fname=" . rawurlencode($zipname));
		exit;
	}
	exit;
}

if ($action == "download") {
	header("Content-Type: text/plain; charset=$CHARACTER_SET");
	// We could open "php://compress.zlib" to create a .gz file or "php://compress.bzip2" to create a .bz2 file
	$fp=fopen('php://output', 'w');
	switch ($filetype) {
	case 'gedcom':
		header("Content-Disposition: attachment; filename={$ged}");
		print_gedcom($privatize_export, $privatize_export_level, $convert, $remove, $fp);
		break;
	case 'gramps':
		header("Content-Disposition: attachment; filename={$ged}.gramps");
		print_gramps($privatize_export, $privatize_export_level, $convert, $remove, $fp);
		break;
	}
	fclose($fp);
	exit;
}

print_header($pgv_lang["download_gedcom"]);

?>
	<div class="center">
	<h2><?php print $pgv_lang["download_gedcom"]; ?></h2>
	<br />
	<form name="convertform" method="post">
		<input type="hidden" name="action" value="download" />
		<input type="hidden" name="ged" value="<?php print $ged; ?>" />
		<table class="list_table" border="0" align="center" valign="top">
		<tr><td colspan="2" class="facts_label03" style="text-align:left;">
		<?php print $pgv_lang["options"]; ?>
		</td></tr>
		<td class="descriptionbox wrap" align="left"><?php print $pgv_lang["choose_file_type"] ?></td>
		<td class="optionbox" align="left"><input type="radio" name="filetype" checked="checked"  value="gedcom" />GEDCOM 
		<?php print_help_link("def_gedcom_help", "qm"); ?>
		<br/>
		<input type="radio" name="filetype" value="gramps" />Gramps XML 
		<?php print_help_link("def_gramps_help", "qm"); ?>
		</td></tr>
		<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["utf8_to_ansi"]; ?></td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><input type="checkbox" name="convert" value="yes" /><?php print_help_link("utf8_ansi_help", "qm"); ?></td></tr>
		<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["remove_custom_tags"]; ?></td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><input type="checkbox" name="remove" value="yes" checked="checked" /><?php print_help_link("remove_tags_help", "qm"); ?></td></tr>
		<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["zip_files"]; ?></td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><input type="checkbox" name="zip" value="yes" checked="checked" /><?php print_help_link("download_zipped_help", "qm"); ?></td></tr>
		<tr><td class="list_label" valign="baseline" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["apply_privacy"]; ?>
			<div id="privtext" style="display: none"></div>
			</td>
			<td class="list_value" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; ">
			<input type="checkbox" name="privatize_export" value="yes" onclick="expand_layer('privtext'); expand_layer('privradio');" />
			<?php print_help_link("apply_privacy_help", "qm"); ?>
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

print $pgv_lang["download_note"] . "<br /><br /><br />\n";
print "</div>";
print_footer();
?>

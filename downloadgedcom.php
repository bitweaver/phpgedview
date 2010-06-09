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
 * @version $Id$
 */

/**
 * load the main configuration and context
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require_once("config.php");
require_once("includes/functions_export.php");
// Validate user parameters
if (!isset($_SESSION['exportConvPath'])) $_SESSION['exportConvPath'] = $MEDIA_DIRECTORY;
if (!isset($_SESSION['exportConvSlashes'])) $_SESSION['exportConvSlashes'] = 'forward';

$ged				= safe_GET('ged',				get_all_gedcoms());
$action				= safe_GET('action',			'download');
$remove				= safe_GET('remove',			'yes', 'no');
$convert			= safe_GET('convert',			'yes', 'no');
$zip				= safe_GET('zip',				'yes', 'no');
$conv_path			= safe_GET('conv_path',			PGV_REGEX_NOSCRIPT,				$_SESSION['exportConvPath']);
$conv_slashes		= safe_GET('conv_slashes',		array('forward', 'backward'),	$_SESSION['exportConvSlashes']);
$privatize_export	= safe_GET('privatize_export',	array('none', 'visitor', 'user', 'gedadmin', 'admin'));
$filetype			= safe_GET('filetype',			array('gedcom', 'gramps'));

$conv_path = stripLRMRLM($conv_path);
$_SESSION['exportConvPath'] = $conv_path;		// remember this for the next Download
$_SESSION['exportConvSlashes'] = $conv_slashes;


if ((!userGedcomAdmin(getUserName())) || (empty ($ged))) {
	header("Location: editgedcoms.php");
	exit;
}

if ($action == 'download') {
	$conv_path = rtrim(str_replace('\\', '/', trim($conv_path)), '/').'/';	// make sure we have a trailing slash here
	if ($conv_path=='/') $conv_path = '';

	$exportOptions = array();
	$exportOptions['privatize'] = $privatize_export;
	$exportOptions['toANSI'] = $convert;
	$exportOptions['noCustomTags'] = $remove;
	$exportOptions['path'] = $conv_path;
	$exportOptions['slashes'] = $conv_slashes;
}

if ($action == "download" && $zip == "yes") {
	require "includes/pclzip.lib.php";

	$temppath = $INDEX_DIRECTORY . "tmp/";
	$fileName = $ged;
	if ($filetype =="gramps") $fileName = $ged.".gramps";
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
		export_gedcom($GEDCOM, $gedout, $exportOptions);
		break;
	case 'gramps':
		export_gramps($GEDCOM, $gedout, $exportOptions);
		break;
	}
	fclose($gedout);
	$comment = "Created by ".PGV_PHPGEDVIEW." ".PGV_VERSION_TEXT." on " . date("r") . ".";
	$archive = new PclZip(filename_decode($zipfile));
	$v_list = $archive->create(filename_decode($gedname), PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, filename_decode($temppath));
	if ($v_list == 0) print "Error : " . $archive->errorInfo(true);
	else {
		unlink(filename_decode($gedname));
		if ($removeTempDir) rmdir(filename_decode($temppath));
		header("Location: ".encode_url("downloadbackup.php?fname={$zipname}", false));
		exit;
	}
	exit;
}

if ($action == "download") {
	header("Content-Type: text/plain; charset=$CHARACTER_SET");
	// We could open "php://compress.zlib" to create a .gz file or "php://compress.bzip2" to create a .bz2 file
	$gedout = fopen('php://output', 'w');
	switch ($filetype) {
	case 'gedcom':
		header('Content-Disposition: attachment; filename="'.$ged.'"');
		export_gedcom($GEDCOM, $gedout, $exportOptions);
		break;
	case 'gramps':
		header('Content-Disposition: attachment; filename="'.$ged.'.gramps"');
		export_gramps($GEDCOM, $gedout, $exportOptions);
		break;
	}
	fclose($gedout);
	exit;
}

print_header($pgv_lang["download_gedcom"]);

?>
<div class="center"><h2><?php print $pgv_lang["download_gedcom"]; ?></h2></div>
<br />
<form name="convertform" method="get">
	<input type="hidden" name="action" value="download" />
	<input type="hidden" name="ged" value="<?php print $ged; ?>" />
	<table class="list_table width50" border="0" valign="top">
	<tr><td colspan="2" class="facts_label03"><?php print $pgv_lang["options"]; ?></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("file_type_help", "qm"); print $pgv_lang["choose_file_type"] ?></td>
		<td class="optionbox">
		<?php if ($TEXT_DIRECTION=='ltr') { ?>
			<input type="radio" name="filetype" checked="checked" value="gedcom" />&nbsp;&nbsp;GEDCOM<br/><input type="radio" name="filetype" value="gramps" />&nbsp;&nbsp;Gramps XML
		<?php } else { ?>
			GEDCOM&nbsp;&nbsp;<?php print getLRM();?><input type="radio" name="filetype" checked="checked" value="gedcom" /><?php print getLRM();?><br />Gramps XML&nbsp;&nbsp;<?php print getLRM();?><input type="radio" name="filetype" value="gramps" /><?php print getLRM();?>
		<?php } ?>
		</td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("download_zipped_help", "qm"); print $pgv_lang["zip_files"]; ?></td>
		<td class="list_value"><input type="checkbox" name="zip" value="yes" checked="checked" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("apply_privacy_help", "qm"); print $pgv_lang["apply_privacy"]; ?></td>
		<td class="list_value">
		<?php if (PGV_USER_IS_ADMIN) { ?>
			<input type="radio" name="privatize_export" value="none" checked="checked" />&nbsp;&nbsp;<?php print $pgv_lang["none"]; ?><br />
			<input type="radio" name="privatize_export" value="visitor" />&nbsp;&nbsp;<?php print $pgv_lang["visitor"]; ?><br />
		<?php } else { ?>
			<input type="radio" name="privatize_export" value="none" DISABLED />&nbsp;&nbsp;<?php print $pgv_lang["none"]; ?><br />
			<input type="radio" name="privatize_export" value="visitor" checked="checked" />&nbsp;&nbsp;<?php print $pgv_lang["visitor"]; ?><br />
		<?php } ?>
		<input type="radio" name="privatize_export" value="user" />&nbsp;&nbsp;<?php print $pgv_lang["user"]; ?><br />
		<input type="radio" name="privatize_export" value="gedadmin" />&nbsp;&nbsp;<?php print $pgv_lang["gedadmin"]; ?><br />
		<input type="radio" name="privatize_export" value="admin"<?php if (!PGV_USER_IS_ADMIN) print " DISABLED"; ?> />&nbsp;&nbsp;<?php print $pgv_lang["siteadmin"]; ?>
		</td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("utf8_ansi_help", "qm"); print $pgv_lang["utf8_to_ansi"]; ?></td>
		<td class="list_value"><input type="checkbox" name="convert" value="yes" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("remove_tags_help", "qm"); print $pgv_lang["remove_custom_tags"]; ?></td>
		<td class="list_value"><input type="checkbox" name="remove" value="yes" checked="checked" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("convertPath_help", "qm"); print $pgv_lang["convertPath"];?></td>
		<td class="list_value"><input type="text" name="conv_path" size="30" value="<?php echo getLRM(), $conv_path, getLRM();?>" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php print_help_link("convertSlashes_help", "qm"); print $pgv_lang["convertSlashes"];?></td>
		<td class="list_value">
		<input type="radio" name="conv_slashes" value="forward" <?php if ($conv_slashes=='forward') print "checked=\"checked\" "; ?>/>&nbsp;&nbsp;<?php print $pgv_lang["forwardSlashes"];?><br />
		<input type="radio" name="conv_slashes" value="backward" <?php if ($conv_slashes=='backward') print "checked=\"checked\" "; ?>/>&nbsp;&nbsp;<?php print $pgv_lang["backSlashes"];?>
		</td></tr>
	<tr><td class="facts_label03" colspan="2">
	<input type="submit" value="<?php print $pgv_lang["download_now"]; ?>" />
	<input type="button" value="<?php print $pgv_lang["back"];?>" onclick="window.location='editgedcoms.php';"/></td></tr>
	</table><br />
	<br /><br />
</form>
<?php

print $pgv_lang["download_note"] . "<br /><br /><br />\n";
print_footer();
?>

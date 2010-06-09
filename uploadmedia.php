<?php
/**
 * Allow admin users to upload media files using a web interface.
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
 * @version $Id$
 * @package PhpGedView
 * @subpackage Media
 */

require './config.php';

require_once 'includes/functions/functions_mediadb.php';

/**
 * This functions checks if an existing directory is physically writeable
 * The standard PHP function only checks for the R/O attribute and doesn't
 * detect authorisation by ACL.
 */
function dir_is_writable($dir) {
	$err_write = false;
	$handle = @fopen(filename_decode($dir."x.y"),"w+");
	if	($handle) {
		$i = fclose($handle);
		$err_write = true;
		@unlink(filename_decode($dir."x.y"));
	}
	return($err_write);
}

if (!PGV_USER_CAN_EDIT) {
	header("Location: login.php?url=uploadmedia.php");
	exit;
}

if (isset($_SESSION["cookie_login"]) && $_SESSION["cookie_login"]==true) {
	header("Location: ".encode_url("login.php?ged={$GEDCOM}&url=uploadmedia.php", false));
	exit;
}

print_header($pgv_lang["upload_media"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
	function checkpath(folder) {
		value = folder.value;
		if (value.substr(value.length-1,1) == "/") value = value.substr(0, value.length-1);
		if (value.substr(0,1) == "/") value = value.substr(1, value.length-1);
		result = value.split("/");
		if (result.length > <?php print $MEDIA_DIRECTORY_LEVELS; ?>) {
			alert('<?php print_text("max_media_depth"); ?>');
			folder.focus();
			return false;
		}
	}
//-->
</script>
<center>
<?php
print "<span class=\"subheaders\">".UTF8_strtoupper($pgv_lang["upload_media"])."</span><br /><br />\n";
$action = safe_POST('action');
if ($action == "upload") {
	process_uploadMedia_form();
}

// Check if Media Directory is writeable or if Media features are enabled
// If one of these is not true then do not continue
if (!dir_is_writable($MEDIA_DIRECTORY) || !$MULTI_MEDIA) {
	print "<span class=\"error\"><b>";
	print $pgv_lang["no_upload"];
	print "</b></span><br />";
} else {
	show_mediaUpload_form('uploadmedia.php', false);		// We have the green light to upload media, print the form
}
print_footer();
?>

<?php
/**
 * Footer for Standard theme
 *
 * PhpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and others.  All rights resserved.
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
 * @subpackage Themes
 * @version $Id: footer.php,v 1.2 2009/08/03 20:15:56 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

echo "</div> <!-- closing div id=\"content\" -->";
?>
<div id="footer" class="<?php echo $TEXT_DIRECTION; ?>">
<?php echo contact_links(); ?>

<br /><div align="center" style="width:99%;">
<br />
<a href="<?php echo PGV_PHPGEDVIEW_URL; ?>" target="_blank">
	<img src="<?php echo $PGV_IMAGE_DIR.'/'.$PGV_IMAGES['gedview']['other'];?>" width="100" height="45" border="0" alt="<?php echo PGV_PHPGEDVIEW . (PGV_USER_IS_ADMIN? (" - " .PGV_VERSION_TEXT): ""); ?>"
		title="<?php echo PGV_PHPGEDVIEW . (PGV_USER_IS_ADMIN? (" - " .PGV_VERSION_TEXT): "");?>" /></a><br />
<br />
<?php print_help_link("preview_help", "qm"); ?>
<a href="<?php echo $SCRIPT_NAME."?view=preview&amp;".get_query_string(); ?>"><?php echo $pgv_lang["print_preview"];?></a>
<br />
<?php
if ($SHOW_STATS || PGV_DEBUG) {
	print_execution_stats();
}
if (exists_pending_change()) {?>
	<br />
	<?php echo $pgv_lang["changes_exist"]; ?>
	<a href="javascript:;" onclick="window.open('edit_changes.php','_blank','width=600,height=500,resizable=1,scrollbars=1'); return false;">
	<?php echo $pgv_lang["accept_changes"]; ?></a>
<?php } ?>
</div>
</div> <!-- close div id=\"footer\" -->

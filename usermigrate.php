<?php
/**
 * Exports users and their data to either SQL queries (Index mode) or 
 * authenticate.php and xxxxxx.dat files (MySQL mode).
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2006  PGV Development Team
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
 * @author Boudewijn Sjouke	sjouke@users.sourceforge.net
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: usermigrate.php,v 1.5 2008/07/07 18:01:13 lsces Exp $
 */
require_once("includes/controllers/usermigrate_ctrl.php");

print_header($controller->getPageTitle());

if (!empty($controller->errorMsg)) print "<br /><span class=\"error\">".$controller->errorMsg."</span><br />";

// Backup part of usermigrate
if ($controller->proceed == "backup") {
	// If first time, let the user choose the options
	if ((!isset($_POST["um_config"])) && (!isset($_POST["um_gedcoms"])) && (!isset($_POST["um_gedsets"])) &&(!isset($_POST["um_logs"])) &&(!isset($_POST["um_usinfo"]))) {
		?>
		<div class="center"><h2><?php print $pgv_lang["um_backup"]?></h2></div>
		<table align="center" >
			<tr class="label">
				<td style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right"; ?>;" >
					<?php print $pgv_lang["um_bu_explain"]; ?><br />
				</td>
			</tr>
		</table><br /><br />
		<form action="usermigrate.php" method="post">
		<table align="center">
			<tr class="label"><td style="padding: 5px" colspan="2" class="facts_label03"><?php print $pgv_lang["options"]; ?></td></tr><br />
			<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_bu_config"]; ?></td><td class="list_value" style="padding: 5px;"><input type="checkbox" name="um_config" value="yes" checked="checked" /></td></tr>
			<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_bu_gedcoms"]; ?></td><td class="list_value" style="padding: 5px;"><input type="checkbox" name="um_gedcoms" value="yes" checked="checked" /></td></tr>
			<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_bu_gedsets"]; ?></td><td class="list_value" style="padding: 5px;"><input type="checkbox" name="um_gedsets" value="yes" checked="checked" /></td></tr>
			<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_bu_logs"]; ?></td><td class="list_value" style="padding: 5px;"><input type="checkbox" name="um_logs" value="yes" checked="checked" /></td></tr>
			<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_bu_usinfo"]; ?></td><td class="list_value" style="padding: 5px;"><input type="checkbox" name="um_usinfo" value="yes" checked="checked" /></td></tr>
			<tr><td class="list_label" style="padding: 5px; text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_bu_media"]; ?></td><td class="list_value" style="padding: 5px;"><input type="checkbox" name="um_media" value="yes" checked="checked" /></td></tr>
			<tr><td style="padding: 5px" colspan="2" class="facts_label03"><button type="submit" name="submit"><?php print $pgv_lang["um_mk_bu"]; ?></button>
			<input type="button" value="<?php print $pgv_lang["lang_back_admin"];?>"  onclick="window.location='admin.php';"/></td></tr>
		</table></form><br /><br />
		<?php
		print_footer();
		exit;
	}
	?>
	<div class="center"><h2><?php print $pgv_lang["um_backup"]?></h2></div>
	<br /><br />
	<table align="center">
		<tr class="label">
			<td style="padding: 5px" class="facts_label03"><?php print $pgv_lang["um_results"]; ?></td>
		</tr>
	<?php	
	// Make the zip
	if (count($controller->flist) > 0) {
		?>
		<tr>
			<td class="list_label" style="padding: 5px;" >
			<?php if ($controller->v_list == 0) {?>
				<?php print $controller->errorMsg; ?>
			</td>
		</tr>
		<?php } else { ?>
			<?php print $pgv_lang["um_zip_succ"]; ?>
			</td>
		</tr>
		<tr>
			<td class="list_value" style="padding: 5px;" >
				<a href="downloadbackup.php?fname=<?php print $controller->buname; ?>" target="_blank"><?php print $pgv_lang["um_zip_dl"]; ?> <?php print $controller->fname; ?></a>  
				( <?php printf("%.0f Kb", (filesize($controller->fname)/1024)); ?> ) <br />
				<?php print $pgv_lang["files_in_backup"]; ?>
				<ul>
				<?php foreach($controller->flist as $f=>$file) { ?>
					<li><?php print $file; ?></li>
				<?php } ?>
				</ul>
			</td>
		</tr>
		<?php }
	}
	else { ?>
			<td style="padding: 5px" class="list_label"><?php print $pgv_lang["um_nofiles"]; ?></td>
		</tr>
	<?php } ?>
		<tr class="label">
			<td style="padding: 5px" class="facts_label03">
				<input type="button" value="<?php print $pgv_lang["lang_back_admin"]; ?>" onclick="window.location='admin.php';" />
			</td>
		</tr>
	</table>
	<br /><br />
<?php
	print_footer();
	exit;
}

// User Migration part of usermigrate. The function um_export is used by backup and migrate part.

if (($controller->proceed == "export") || ($controller->proceed == "exportovr")) { ?>
	<h2><?php print $pgv_lang["um_header"];?></h2>
	<br /><br />
	<?php print $pgv_lang["um_sql_index"]; ?>
<?php } 
if ($controller->proceed == "import") { ?>
	<h2><?php print $pgv_lang["um_header"]; ?></h2>
	<br /><br />
	<?php print $pgv_lang["um_index_sql"]; ?>
	<br /><br />
<?php }
if (($controller->proceed != "import") && ($controller->proceed != "export") && ($controller->proceed != "exportovr")) { ?>
	<div class="center">
		<h2><?php print $pgv_lang["um_header"]; ?></h2>
		<br /><br />
		<form action="usermigrate.php" method="post"><input type="hidden" name="proceed" value="import" />
		<table align="center" width="75%" ><tr class="label">
			<tr><td style="text-align:<?php if ($TEXT_DIRECTION == "ltr") print "left"; else print "right";?>; "><?php print $pgv_lang["um_explain"].$pgv_lang["um_proceed"]?><br />
			</td></tr>
			<tr><td><input type="submit" class="button" value="<?php print $pgv_lang["um_import"];?>" />
			<input type="button" class="button" value="<?php print $pgv_lang["um_export"];?>" onclick="window.location='usermigrate.php?proceed=export';"/>
			<input type="button" value="<?php print $pgv_lang["lang_back_admin"];?>"  onclick="window.location='admin.php';"/><br /><br />
			</td></tr>
		</table>
		</form>
	</div>
	<?php
	print_footer();
	exit;
}
if (($controller->proceed == "export") || ($controller->proceed == "exportovr")) {
	
	// Check if one of the files already exists
	if ($controller->fileExists) { ?>
		<br />
		<?php print $pgv_lang["um_files_exist"];?>
		<br /><br />
		<form action="usermigrate.php" method="post">
			<input type="hidden" class="button" value="<?php print $pgv_lang["yes"];?>" />
			<input type="button" class="button" value="<?php print $pgv_lang["yes"];?>" onclick="window.location='usermigrate.php?proceed=exportovr';"/>
			<input type="button" class="button" value="<?php print $pgv_lang["no"];?>" onclick="window.location='admin.php';"/>
		</form>
		<?php
		print_footer();
		exit;
	}
}
if ($controller->proceed == "import") { ?>
	<br /><br /><?php print $pgv_lang["um_imp_users"]; ?><br />
	<?php if ((file_exists($INDEX_DIRECTORY."authenticate.php")) == false) { ?>
		<?php print $pgv_lang["um_nousers"]; ?>
		<br /><br />
		<a href="admin.php"><?php print $pgv_lang["lang_back_admin"]; ?></a><br /><br />
		<?php exit;
	}
	
	if ($controller->impSuccess) {
		print $pgv_lang["um_imp_succ"]."<br /><br />";
	}
	else {
		print $pgv_lang["um_imp_fail"];
		exit;
	}

	// Get messages and import them
	print $pgv_lang["um_imp_messages"]."<br />";
	if ((file_exists($INDEX_DIRECTORY."messages.dat")) == false) {
		print $pgv_lang["um_nomsg"]."<br /><br />";
	}
	if ($controller->msgSuccess) print $pgv_lang["um_imp_succ"]."<br /><br />";

	// Get favorites and import them
	print $pgv_lang["um_imp_favorites"]."<br />";
	if ((file_exists($INDEX_DIRECTORY."favorites.dat")) == false) {
		print $pgv_lang["um_nofav"]."<br /><br />";
	}
	if ($controller->favSuccess) print $pgv_lang["um_imp_succ"]."<br /><br />";

	// Get news and import it
	print $pgv_lang["um_imp_news"]."<br />";
	if ((file_exists($INDEX_DIRECTORY."news.dat")) == false) {
		print $pgv_lang["um_nonews"]."<br /><br />";
	}
	if ($controller->newsSuccess) print $pgv_lang["um_imp_succ"]."<br /><br />";

	// Get blocks and import them
	print $pgv_lang["um_imp_blocks"]."<br />";
	if ((file_exists($INDEX_DIRECTORY."blocks.dat")) == false) {
		print $pgv_lang["um_noblocks"]."<br /><br />";
	}
	if ($controller->blockSuccess) print $pgv_lang["um_imp_succ"]."<br /><br />";
}
?>

<a href="admin.php"><?php print $pgv_lang["lang_back_admin"]; ?></a><br /><br />

<?php
print_footer();
?>

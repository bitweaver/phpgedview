<?php
/**
 * Command line utility for backups.
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  PGV Development Team
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
 * @author John Finlay
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: usermigrate_cli.php,v 1.1 2008/07/07 18:01:10 lsces Exp $
 */

function print_usage() {
	?>
	PhpGedView Command Line Backup Utility
	Usage: 
	usermigrate_cli.php command [options]
	Command should be one of the following:
	--backup [options]
		Create a backup of PhpGedView settings, GEDCOMs, users and media files.  The following options are available:
		-t=n	Attempt to set the PHP time limit to "n" where "n" should be a number
		-Xc		Exclude config.php file
		-Xu		Exclude user information
		-Xg		Exclude GEDCOM files
		-Xs		Exclude GEDCOM configuration and privacy settings
		-Xl		Exclude log files
		-Xm		Exclude media files
		
	--export 
		Export user settings to corresponding files in the index directory.
		
	--import
		Import user settings into databases from files in the index directory.
	<?php
	exit;
}

if ($argc==1 || $argv[1]=="-h" || $argv[1]=="--help") {
	print_usage();
}

$validargs = true;
if ($argv[1]=="--backup") {
	$_REQUEST['proceed'] = 'backup';
	$argsarray = array("-Xc"=>"um_config","-Xu"=>"um_usinfo","-Xg"=>"um_gedcoms","-Xs"=>"um_gedsets","-Xl"=>"um_logs","-Xm"=>"um_media");
	for($i=2; $i<$argc; $i++) {
		if (!isset($argsarray[$argv[$i]])) {
			$parts = preg_split("/=/", $argv[$i]);
			if ($parts[0]=="-t") {
				set_time_limit($parts[1]);
			}
			else $validargs = false;
		}
		else {
			unset($argsarray[$argv[$i]]);
		}
	}
}
else if ($argv[1]=="--export") {
	$argsarray = array();
	$_REQUEST['proceed'] = 'exportovr';
}
else $validargs = false;

if (!$validargs || !isset($argsarray)) print_usage();
else {
	foreach($argsarray as $key=>$value) {
		$_POST[$value] = "yes";
	}
}

require_once("includes/controllers/usermigrate_ctrl.php");
// load admin lang keys
$file = "./languages/admin.".$lang_short_cut[$LANGUAGE].".php";
if (file_exists($file)) include($file);

// load the edit lang keys
$file = "./languages/editor.".$lang_short_cut[$LANGUAGE].".php";
if (file_exists($file)) include($file);

if (!empty($controller->errorMsg)) print "\r\n\r\n*** ERROR: ".$controller->errorMsg." ***\r\n\r\n";

// Backup part of usermigrate
if ($controller->proceed == "backup") {	
	// Make the zip
	if (count($controller->flist) > 0) {
		if ($controller->v_list == 0) {
			print $controller->errorMsg;
		} else { 
			print $pgv_lang["um_zip_succ"]."\r\n";
			print $pgv_lang["um_zip_dl"]." ".$controller->fname;
			printf("(%.0f Kb)\r\n", (filesize($controller->fname)/1024));
			print $pgv_lang["files_in_backup"];
			foreach($controller->flist as $f=>$file) {
				print "\t".$file."\r\n";
			}
		}
	}
	else { 
		print $pgv_lang["um_nofiles"];
	}
	exit;
}

// User Migration part of usermigrate. The function um_export is used by backup and migrate part.
if (($controller->proceed == "export") || ($controller->proceed == "exportovr")) {
	print "\r\n".$pgv_lang["um_sql_index"]."\r\n";
}
if ($controller->proceed == "import") {
	if ((file_exists($INDEX_DIRECTORY."authenticate.php")) == false) { 
		print $pgv_lang["um_nousers"]; 
		exit;
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
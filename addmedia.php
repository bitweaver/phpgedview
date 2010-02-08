<?php
/**
 * Add media to gedcom file
 *
 * This file allows the user to maintain a seperate table
 * of media files and associate them with individuals in the gedcom
 * and then add these records later.
 * Requires SQL mode.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008, John Finlay and others, all rights reserved.
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
 * @package PhpGedView
 * @subpackage MediaDB
 * @version $Id: addmedia.php,v 1.12 2010/02/08 21:27:24 wjames5 Exp $
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
require("config.php");
require($factsfile["english"]);
if (file_exists($factsfile[$LANGUAGE])) require $factsfile[$LANGUAGE];

require './includes/functions/functions_print_lists.php';
require './includes/functions/functions_edit.php';

if (empty($ged)) $ged = $GEDCOM;
$GEDCOM = $ged;

if ($_SESSION["cookie_login"]) {
	header('Location: '.encode_url("login.php?type=simple&ged={$GEDCOM}&url=addmedia.php", false));
	exit;
}

// TODO use GET/POST, rather than $_REQUEST
// TODO decide what validation is required on these input parameters
$pid        =safe_REQUEST($_REQUEST, 'pid',         PGV_REGEX_XREF);
$mid        =safe_REQUEST($_REQUEST, 'mid',         PGV_REGEX_XREF);
$gid        =safe_REQUEST($_REQUEST, 'gid',         PGV_REGEX_XREF);
$linktoid   =safe_REQUEST($_REQUEST, 'linktoid',    PGV_REGEX_XREF);
$action     =safe_REQUEST($_REQUEST, 'action',      PGV_REGEX_NOSCRIPT, 'showmediaform');
$folder     =safe_REQUEST($_REQUEST, 'folder',      PGV_REGEX_UNSAFE);
$oldFolder  =safe_REQUEST($_REQUEST, 'oldFolder',   PGV_REGEX_UNSAFE);
$filename   =safe_REQUEST($_REQUEST, 'filename',    PGV_REGEX_UNSAFE);
$oldFilename=safe_REQUEST($_REQUEST, 'oldFilename', PGV_REGEX_UNSAFE, $filename);
$level      =safe_REQUEST($_REQUEST, 'level',       PGV_REGEX_UNSAFE);
$text       =safe_REQUEST($_REQUEST, 'text',        PGV_REGEX_UNSAFE);
$tag        =safe_REQUEST($_REQUEST, 'tag',         PGV_REGEX_UNSAFE);
$islink     =safe_REQUEST($_REQUEST, 'islink',      PGV_REGEX_UNSAFE);
$glevels    =safe_REQUEST($_REQUEST, 'glevels',     PGV_REGEX_UNSAFE);

$update_CHAN=!safe_POST_bool('preserve_last_changed');

$filename = decrypt($filename);
$oldFilename = decrypt($oldFilename);

print_simple_header($pgv_lang["add_media_tool"]);
$disp = true;
if (empty($pid) && !empty($mid)) $pid = $mid;
if (!empty($pid)) {
	if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_media_record($pid);
	else $gedrec = find_updated_record($pid);
	$disp = displayDetailsById($pid, "OBJE");
}
if ($action=="update" || $action=="newentry") {
	if (!isset($linktoid) || $linktoid=="new") $linktoid="";
	if (empty($linktoid) && !empty($gid)) $linktoid = $gid;
	if (!empty($linktoid)) {
		$disp = displayDetailsById($linktoid);
	}
}

if (!PGV_USER_CAN_EDIT || !$disp || !$ALLOW_EDIT_GEDCOM) {
	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!PGV_USER_CAN_EDIT) print "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
	}
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".$pgv_lang["close_window"]."\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}

if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';
echo PGV_JS_START;
?>
	// Shared Notes =========================
	function findnote(field) {
		pastefield = field;
		findwin = window.open('find.php?type=note', '_blank', 'left=50,top=50,width=600,height=520,resizable=1,scrollbars=1');
		return false;
	}
	var language_filter, magnify;
	var pastefield;
	language_filter = "";
	magnify = "";
	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}
	function paste_id(value) {
		pastefield.value = value;
	}
	function paste_char(value,lang,mag) {
		pastefield.value += value;
		language_filter = lang;
		magnify = mag;
	}
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
<?php
echo PGV_JS_END;

// Naming conventions used in this script:
// folderName - this is the link to the folder in the standard media directory; the one that is stored in the gedcom.
// serverFolderName - this is where the file is physically located.  if the media firewall is enabled it is in the protected media directory.  if not it is the same as folderName.
// thumbFolderName - this is the link to the thumb folder in the standard media directory
// serverThumbFolderName - this is where the thumbnail file is physically located

if (empty($action)) $action="showmediaform";

// **** begin action "newentry"
// NOTE: Store the entered data
if ($action=="newentry") {
	if (empty($level)) $level = 1;

	$error = "";
	$mediaFile = "";
	$thumbFile = "";
	if (!empty($_FILES['mediafile']["name"]) || !empty($_FILES['thumbnail']["name"])) {
		// Validate and correct folder names
		$folderName = trim(trim(safe_POST('folder', PGV_REGEX_NOSCRIPT)), '/');
		$folderName = check_media_depth($folderName."/y.z", "BACK");
		$folderName = dirname($folderName)."/";
		$thumbFolderName = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folderName);

		$_SESSION["upload_folder"] = $folderName; // store standard media folder in session
		$realFolderName = $folderName;
		$realThumbFolderName = $thumbFolderName;
		if ($USE_MEDIA_FIREWALL) {
			$realFolderName = get_media_firewall_path($folderName);
			if ($MEDIA_FIREWALL_THUMBS) $realThumbFolderName = get_media_firewall_path($thumbFolderName);
		}
		// make sure the dirs exist
		@mkdirs($realFolderName);
		@mkdirs($realThumbFolderName);

		$error = "";

		// Determine file name on server
		if (PGV_USER_GEDCOM_ADMIN && !empty($text[0])) $fileName = trim(trim($text[0]), '/');
		else $fileName = '';
		$parts = pathinfo_utf($fileName);
		if (!empty($parts["basename"])) {
			// User supplied a name to be used on the server
			$mediaFile = $parts["basename"];	// Use the supplied name
			if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
				// Strip invalid extension from supplied name
				$lastDot = strrpos($mediaFile, '.');
				if ($lastDot !== false) $mediaFile = substr($mediaFile, 0, $lastDot);
				// Use extension of original uploaded file name
				if (!empty($_FILES["mediafile"]["name"])) $parts = pathinfo_utf($_FILES["mediafile"]["name"]);
				else $parts = pathinfo_utf($_FILES["thumbnail"]["name"]);
				if (!empty($parts["extension"])) $mediaFile .= ".".$parts["extension"];
			}
		} else {
			// User did not specify a name to be used on the server:  use the original uploaded file name
			if (!empty($_FILES["mediafile"]["name"])) $parts = pathinfo_utf($_FILES["mediafile"]["name"]);
			else $parts = pathinfo_utf($_FILES["thumbnail"]["name"]);
			$mediaFile = $parts["basename"];
		}
		if (!empty($_FILES["mediafile"]["name"])) {
			$newFile = $realFolderName.$mediaFile;
			// Copy main media file into the destination directory
			if (file_exists(filename_decode($newFile))) {
				$error .= $pgv_lang["media_exists"]."&nbsp;&nbsp;".$newFile."<br />";
			} else {
				if (!move_uploaded_file($_FILES["mediafile"]["tmp_name"], filename_decode($newFile))) {
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".file_upload_error_text($_FILES["mediafile"]["error"])."<br />";
				} else {
					@chmod(filename_decode($newFile), PGV_PERM_FILE);
					AddToLog("Media file {$folderName}{$mediaFile} uploaded");
				}
			}
		}
		if ($error=="" && !empty($_FILES["thumbnail"]["name"])) {
			$newThum = $realThumbFolderName.$mediaFile;
			// Copy user-supplied thumbnail file into the destination directory
			if (file_exists(filename_decode($newThum))) {
				$error .= $pgv_lang["media_thumb_exists"]."&nbsp;&nbsp;".$newThum."<br />";
			} else {
				if (!move_uploaded_file($_FILES["thumbnail"]["tmp_name"], filename_decode($newThum))) {
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".file_upload_error_text($_FILES["thumbnail"]["error"])."<br />";
				} else {
					@chmod(filename_decode($newThum), PGV_PERM_FILE);
					AddToLog("Media file {$thumbFolderName}{$mediaFile} uploaded");
				}
			}
		}
		if ($error=="" && empty($_FILES["mediafile"]["name"]) && !empty($_FILES["thumbnail"]["name"])) {
			// Copy user-supplied thumbnail file into the main destination directory
			$whichFile1 = $realThumbFolderName.$mediaFile;
			$whichFile2 = $realFolderName.$mediaFile;
			if (!copy(filename_decode($whichFile1), filename_decode($whichFile2))) {
				// the file cannot be copied
				$error .= $pgv_lang["upload_error"]."<br />".print_text('copy_error', 0, 1)."<br />";
			} else {
				@chmod(filename_decode($whichFile2), PGV_PERM_FILE);
				AddToLog("Media file {$folderName}{$mediaFile} copied from {$thumbFolderName}{$mediaFile}");
			}
		}
		if ($error=="" && !empty($_FILES["mediafile"]["name"]) && empty($_FILES["thumbnail"]["name"])) {
			if (safe_POST('genthumb', 'yes', 'no') == 'yes') {
				// Generate thumbnail from main image
				$parts = pathinfo_utf($mediaFile);
				if (!empty($parts["extension"])) {
					$ext = strtolower($parts["extension"]);
					if (isImageTypeSupported($ext)) {
						$thumbnail = $thumbFolderName.$mediaFile;
						$okThumb = generate_thumbnail($folderName.$mediaFile, $thumbnail, "OVERWRITE");
						if (!$okThumb) {
							$error .= print_text("thumbgen_error",0,1);
						} else {
							print_text("thumb_genned");
							print "<br />";
							AddToLog("Media thumbnail {$thumbnail} generated");
						}
					}
				}
			}
		}
		// Let's see if there are any errors generated and print it
		if (!empty($error)) {
			echo '<span class="error">', $error, "</span><br />\n";
			$mediaFile = "";
			$finalResult = false;
		} else $finalResult = true;
	}
	if ($mediaFile=="") {
		// No upload: should be an existing file on server
		if ($tag[0]=="FILE") {
			if (!empty($text[0])) {
				$isExternal = isFileExternal($text[0]);
				if ($isExternal) {
					$fileName = $text[0];
					$mediaFile = $fileName;
					$folderName = "";
				} else {
					$fileName = check_media_depth($text[0], "BACK");
					$mediaFile = basename($fileName);
					$folderName = dirname($fileName)."/";
				}
			}
			if ($mediaFile=="") {
				echo '<span class="error">', $pgv_lang["illegal_chars"], "</span><br />\n";
				$finalResult = false;
			} else $finalResult = true;
		} else {
			//-- check if the file is used in more than one gedcom
			//-- do not allow it to be moved or renamed if it is
			$myFile = str_replace($MEDIA_DIRECTORY, "", $oldFolder.$oldFilename);
			$multi_gedcom=is_media_used_in_other_gedcom($myFile, PGV_GED_ID);

			// Handle Admin request to rename or move media file
			if ($filename!=$oldFilename) {
				$parts = pathinfo_utf($filename);
				if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
					$parts = pathinfo_utf($oldFilename);
					$filename .= ".".$parts["extension"];
				}
			}
			if (substr($folder,-1)!="/") $folder .= "/";
			if ($folder=="/") $folder = "";
			$folder = check_media_depth($folder."y.z", "BACK");
			$folder = dirname($folder)."/";
			if (substr($oldFolder,-1)!="/") $oldFolder .= "/";
			if ($oldFolder=="/") $oldFolder = "";
			$oldFolder = check_media_depth($oldFolder."y.z", "BACK");
			$oldFolder = dirname($oldFolder)."/";
			$_SESSION["upload_folder"] = $folder; // store standard media folder in session

			$finalResult = true;
			if ($filename!=$oldFilename || $folder!=$oldFolder) {
				if ($multi_gedcom) {
					echo '<span class="error">', $pgv_lang["multiple_gedcoms"], '<br /><br /><b>';
					if ($filename!=$oldFilename) print $pgv_lang["media_file_not_renamed"];
					else print $pgv_lang["media_file_not_moved"];
					print "</b></span><br />";
					$finalResult = false;
				} else {
					$oldMainFile = $oldFolder.$oldFilename;
					$newMainFile = $folder.$filename;
					$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldMainFile);
					$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $newMainFile);
					if (media_exists($oldMainFile) == 3) {
						// the file is in the media firewall directory
						$oldMainFile = get_media_firewall_path($oldMainFile);
						$newMainFile = get_media_firewall_path($newMainFile);
					}
					if (media_exists($oldThumFile) == 3) {
						$oldThumFile = get_media_firewall_path($oldThumFile);
						$newThumFile = get_media_firewall_path($newThumFile);
					}
					$isMain = file_exists(filename_decode($oldMainFile));
					$okMain = !file_exists(filename_decode($newMainFile));
					$isThum = file_exists(filename_decode($oldThumFile));
					$okThum = !file_exists(filename_decode($newThumFile));
					if ($okMain && $okThum) {
						// make sure the directories exist before moving the files
						mkdirs(dirname($newMainFile)."/");
						mkdirs(dirname($newThumFile)."/");
						if ($isMain) $okMain = @rename(filename_decode($oldMainFile), filename_decode($newMainFile));
						if ($isThum) $okThum = @rename(filename_decode($oldThumFile), filename_decode($newThumFile));
					}

					// Build text to tell Admin about the success or failure of the requested operation
					$GLOBALS["oldMediaName"] = $oldFilename;
					$GLOBALS["newMediaName"] = $filename;
					$GLOBALS["oldMediaFolder"] = $oldFolder;
					$GLOBALS["newMediaFolder"] = $folder;
					$GLOBALS["oldThumbFolder"] = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder);
					$GLOBALS["newThumbFolder"] = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder);
					$mediaAction = 0;
					if ($filename!=$oldFilename) $mediaAction = 1;
					if ($folder!=$oldFolder) $mediaAction = $mediaAction + 2;

					if (!$isMain) {
						print_text("main_media_fail0");
					} else {
						if ($okMain) print_text("main_media_ok".$mediaAction);
						else {
							$finalResult = false;
							echo '<span class="error">', print_text("main_media_fail".$mediaAction), '</span>';
						}
					}
					print "<br />";

					if (!$isThum) {
						print_text("thumb_media_fail0");
					} else {
						if ($okThum) print_text("thumb_media_ok".$mediaAction);
						else {
							$finalResult = false;
							echo '<span class="error">', print_text("thumb_media_fail".$mediaAction), '</span>';
						}
					}
					print "<br />";

					unset($GLOBALS["oldMediaName"]);
					unset($GLOBALS["newMediaName"]);
					unset($GLOBALS["oldMediaFolder"]);
					unset($GLOBALS["newMediaFolder"]);
					unset($GLOBALS["oldThumbFolder"]);
					unset($GLOBALS["newThumbFolder"]);
				}
			}

			// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
			$glevels = array_merge(array("1"), $glevels);
			$tag = array_merge(array("FILE"), $tag);
			$islink = array_merge(array(0), $islink);
			$text = array_merge(array($folder.$filename), $text);

			$mediaFile = $filename;
			$folderName = $folder;
		}
	}

	if ($finalResult && $mediaFile!="") {
		// NOTE: Build the gedcom record
		// NOTE: Level 0
		$media_id = get_new_xref("OBJE");
		$newged = "0 @".$media_id."@ OBJE\n";
		//-- set the FILE text to the correct file location in the standard media directory
		if (PGV_USER_GEDCOM_ADMIN) $text[0] = $folderName.$mediaFile;
		else $newged .= "1 FILE ".$folderName.$mediaFile."\n";

		$newged = handle_updates($newged);

		require_once 'includes/classes/class_media.php';
		$media_obje = new Media($newged);
		$mediaid = Media::in_obje_list($media_obje);
		if (!$mediaid) $mediaid = append_gedrec($newged, $linktoid);
		if ($mediaid) {
			AddToChangeLog("Media ID ".$mediaid." successfully added.");
			if ($linktoid!="") $link = linkMedia($mediaid, $linktoid, $level);
			else $link = false;
			if ($link) {
				AddToChangeLog("Media ID ".$media_id." successfully added to $linktoid.");
			} else {
				echo "<a href=\"javascript:// OBJE $mediaid\" onclick=\"openerpasteid('$mediaid'); return false;\">".$pgv_lang["paste_id_into_field"]." <b>$mediaid</b></a><br /><br />\n";
				echo PGV_JS_START;
				echo "openerpasteid('", $mediaid, "');";
				echo PGV_JS_END;
			}
		}
		print $pgv_lang["update_successful"];
	}
}
// **** end action "newentry"

// **** begin action "update"
if ($action == "update") {
	if (empty($level)) $level = 1;
	//-- check if the file is used in more than one gedcom
	//-- do not allow it to be moved or renamed if it is
	$myFile = str_replace($MEDIA_DIRECTORY, "", $oldFolder.$oldFilename);
	$multi_gedcom=is_media_used_in_other_gedcom($myFile, PGV_GED_ID);

	$isExternal = isFileExternal($oldFilename) || isFileExternal($filename);
	$finalResult = true;

	// Handle Admin request to rename or move media file
	if (!$isExternal) {
		if ($filename!=$oldFilename) {
			$parts = pathinfo_utf($filename);
			if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
				$parts = pathinfo_utf($oldFilename);
				$filename .= ".".$parts["extension"];
			}
		}
		if (!isset($folder) && isset($oldFolder)) $folder = $oldFolder;
		$folder = trim($folder);
		if (substr($folder,-1)!="/") $folder .= "/";
		if ($folder=="/") $folder = "";
		$folder = check_media_depth($folder."y.z", "BACK");
		$folder = dirname($folder)."/";
		if (substr($oldFolder,-1)!="/") $oldFolder .= "/";
		if ($oldFolder=="/") $oldFolder = "";
		$oldFolder = check_media_depth($oldFolder."y.z", "BACK");
		$oldFolder = dirname($oldFolder)."/";
	}

	if ($filename!=$oldFilename || $folder!=$oldFolder) {
		if ($multi_gedcom) {
			echo '<span class="error">', $pgv_lang["multiple_gedcoms"], '<br /><br /><b>';
			if ($filename!=$oldFilename) print $pgv_lang["media_file_not_renamed"];
			else print $pgv_lang["media_file_not_moved"];
			print "</b></span><br />";
			$finalResult = false;
		} else if (!$isExternal) {
			$oldMainFile = $oldFolder.$oldFilename;
			$newMainFile = $folder.$filename;
			$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldMainFile);
			$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $newMainFile);
			if (media_exists($oldMainFile) == 3) {
				// the file is in the media firewall directory
				$oldMainFile = get_media_firewall_path($oldMainFile);
				$newMainFile = get_media_firewall_path($newMainFile);
			}
			if (media_exists($oldThumFile) == 3) {
				$oldThumFile = get_media_firewall_path($oldThumFile);
				$newThumFile = get_media_firewall_path($newThumFile);
			}
			$isMain = file_exists(filename_decode($oldMainFile));
			$okMain = !file_exists(filename_decode($newMainFile));
			$isThum = file_exists(filename_decode($oldThumFile));
			$okThum = !file_exists(filename_decode($newThumFile));
			if ($okMain && $okThum) {
				// make sure the directories exist before moving the files
				mkdirs(dirname($newMainFile)."/");
				mkdirs(dirname($newThumFile)."/");
				if ($isMain) $okMain = @rename(filename_decode($oldMainFile), filename_decode($newMainFile));
				if ($isThum) $okThum = @rename(filename_decode($oldThumFile), filename_decode($newThumFile));
			}

			// Build text to tell Admin about the success or failure of the requested operation
			$GLOBALS["oldMediaName"] = $oldFilename;
			$GLOBALS["newMediaName"] = $filename;
			$GLOBALS["oldMediaFolder"] = $oldFolder;
			$GLOBALS["newMediaFolder"] = $folder;
			$GLOBALS["oldThumbFolder"] = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder);
			$GLOBALS["newThumbFolder"] = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder);
			$mediaAction = 0;
			if ($filename!=$oldFilename) $mediaAction = 1;
			if ($folder!=$oldFolder) $mediaAction = $mediaAction + 2;

			if (!$isMain) {
				print_text("main_media_fail0");
			} else {
				if ($okMain) print_text("main_media_ok".$mediaAction);
				else {
					$finalResult = false;
					echo '<span class="error">', print_text("main_media_fail".$mediaAction), '</span>';
				}
			}
			print "<br />";

			if (!$isThum) {
				print_text("thumb_media_fail0");
			} else {
				if ($okThum) print_text("thumb_media_ok".$mediaAction);
				else {
					$finalResult = false;
					echo '<span class="error">', print_text("thumb_media_fail".$mediaAction), '</span>';
				}
			}
			print "<br />";

			unset($GLOBALS["oldMediaName"]);
			unset($GLOBALS["newMediaName"]);
			unset($GLOBALS["oldMediaFolder"]);
			unset($GLOBALS["newMediaFolder"]);
			unset($GLOBALS["oldThumbFolder"]);
			unset($GLOBALS["newThumbFolder"]);
		}
	}

	if ($finalResult) {
		$_SESSION["upload_folder"] = $folder; // store standard media folder in session

		// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
		$glevels = array_merge(array("1"), $glevels);
		$tag = array_merge(array("FILE"), $tag);
		$islink = array_merge(array(0), $islink);
		$text = array_merge(array($folder.$filename), $text);

		if (!empty($pid)) {
			if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
			else $gedrec = find_updated_record($pid);
		}
		$newrec = "0 @$pid@ OBJE\n";
		$newrec = handle_updates($newrec);
		if (!$update_CHAN) {
			$newrec .= get_sub_record(1, "1 CHAN", $gedrec);
		}
		//print("[".$newrec."]");
		//-- look for the old record media in the file
		//-- if the old media record does not exist that means it was
		//-- generated at import and we need to append it
		if (replace_gedrec($pid, $newrec, $update_CHAN)) AddToChangeLog("Media ID ".$pid." successfully updated.");

		if ($pid && $linktoid!="") {
			$link = linkMedia($pid, $linktoid, $level);
			if ($link) {
				AddToChangeLog("Media ID ".$pid." successfully added to $linktoid.");
			}
		}
	}

	if ($finalResult) print $pgv_lang["update_successful"];
}
// **** end action "update"

// **** begin action "delete"
if ($action=="delete") {
	if (delete_gedrec($pid)) {
		AddToChangeLog("Media ID ".$pid." successfully deleted.");
		print $pgv_lang["update_successful"];
	}
}
// **** end action "delete"

// **** begin action "showmediaform"
if ($action=="showmediaform") {
	if (!isset($pid)) $pid = "";
	if (empty($level)) $level = 1;
	if (!isset($linktoid)) $linktoid = "";
	show_media_form($pid, "newentry", $filename, $linktoid, $level);
}
// **** end action "showmediaform"


// **** begin action "editmedia"
if ($action=="editmedia") {
	if (!isset($pid)) $pid = "";
	if (empty($level)) $level = 1;
	show_media_form($pid, "update", $filename, $linktoid, $level);
}
// **** end action "editmedia"

print "<br />";
print "<div class=\"center\"><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
print "<br />";
print_simple_footer();
?>

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
 * @package PhpGedView
 * @subpackage MediaDB
 * @version $Id: addmedia.php,v 1.3 2006/10/02 23:04:16 lsces Exp $
 */

/**
 * load config file
 */
require("config.php");
require($factsfile["english"]);
if (file_exists($factsfile[$LANGUAGE])) require $factsfile[$LANGUAGE];

require_once("includes/functions_print_lists.php");
require_once("includes/functions_edit.php");

if (empty($ged)) $ged = $GEDCOM;
$GEDCOM = $ged;

if ($_SESSION["cookie_login"]) {
	header("Location: login.php?ged=$GEDCOM&url=addmedia.php");
	exit;
}

print_simple_header($pgv_lang["add_media_tool"]);
$disp = true;
if (!empty($pid)) {
	$pid = clean_input($pid);
	if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_media_record($pid);
	else $gedrec = find_record_in_file($pid);
	if (empty($gedrec)) $gedrec =  find_record_in_file($pid);
	$disp = displayDetails($pid, "OBJE");
}
if ($action=="update" || $action=="newentry") {
	if (!isset($linktoid) || $linktoid=="new") $linktoid="";
	if (empty($linktoid) && !empty($gid)) $linktoid = $gid;
	if (!empty($linktoid)) {
		$linktoid = clean_input($linktoid);
		$disp = displayDetails(find_gedcom_record($linktoid));
	}
}

if ((!userCanEdit(getUserName()))||(!$disp)||(!$ALLOW_EDIT_GEDCOM)) {
	//print "pid: $pid<br />";
	//print "gedrec: $gedrec<br />";
	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!userCanEdit(getUserName())) print "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
	}
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".$pgv_lang["close_window"]."\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}

?>
<script language="JavaScript" type="text/javascript">
<!--
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
//-->
</script>

<?php
if (empty($action)) $action="showmediaform";

if (isset($filename)) {
	$filename = stripslashes($filename);
} else {
	$filename = "";
}

if (!isset($m_ext)) $m_ext="";
if (!isset($m_titl)) $m_titl="";
if (!isset($m_file)) $m_file="";

// NOTE: Store the entered data
if ($action=="newentry") {
	if (empty($level)) $level = 1;
	
	$error = "";
	$mediaFile = "";
	$thumbFile = "";
	if (!empty($_FILES['mediafile']["name"]) || !empty($_FILES['thumbnail']["name"])) {
		// NOTE: Check for file upload
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		$folderName = "";
		if (!empty($_POST["folder"])) $folderName = $_POST["folder"];
		// Validate and correct folder names
		$folderName = check_media_depth($folderName."/y.z", "BACK");
		$folderName = dirname($folderName)."/";
		$thumbFolderName = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folderName);

		if (!empty($folderName)) $_SESSION["upload_folder"] = $folderName;

		$error = "";
		
		// Determine file name on server
		if (!empty($text[0])) {
			$parts = pathinfo($text[0]);
			$mediaFile = $parts["basename"];
			if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
				if (!empty($_FILES["mediafile"]["name"])) {
					$parts = pathinfo($_FILES["mediafile"]["name"]);
				} else {
					$parts = pathinfo($_FILES["thumbnail"]["name"]);
				}
				$mediaFile .= ".".$parts["extension"];
			}
		} else {
			if (!empty($_FILES["mediafile"]["name"])) {
				$parts = pathinfo($_FILES["mediafile"]["name"]);
			} else {
				$parts = pathinfo($_FILES["thumbnail"]["name"]);
			}
			$mediaFile = $parts["basename"];
		}

		if (!empty($_FILES["mediafile"]["name"])) {
			$newFile = $folderName.$mediaFile;
			$fileExists = file_exists(filename_decode($newFile));
			// Copy main media file into the destination directory
			if ($fileExists) {
				$error .= $pgv_lang["media_exists"]."&nbsp;&nbsp;".$newFile."<br />";
			} else {
				if (!move_uploaded_file($_FILES["mediafile"]["tmp_name"], filename_decode($newFile))) { 
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES["mediafile"]["error"]]."<br />";
				} else {
					// Set file permission to read/write for everybody
//					@chmod(filename_decode($folderName.$mediaFile), 0644);
					AddToLog("Media file ".$folderName.$mediaFile." uploaded by >".getUserName()."<");
				}
			}
		}
		if ($error=="" && !empty($_FILES["thumbnail"]["name"])) {
			$newThum = $thumbFolderName.$mediaFile;
			$thumExists = file_exists(filename_decode($newThum));
			// Copy user-supplied thumbnail file into the destination directory
			if ($thumExists) {
				$error .= $pgv_lang["media_thumb_exists"]."&nbsp;&nbsp;".$newThum."<br />";
			} else {
				if (!move_uploaded_file($_FILES["thumbnail"]["tmp_name"], filename_decode($newThum))) { 
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES["thumbnail"]["error"]]."<br />";
				} else {
					// Set file permission to read/write for everybody
//					@chmod(filename_decode($thumbFolderName.$mediaFile), 0644);
					AddToLog("Media file ".$thumbFolderName.$mediaFile." uploaded by >".getUserName()."<");
				}
			}
		}
		if ($error=="" && empty($_FILES["mediafile"]["name"]) && !empty($_FILES["thumbnail"]["name"])) {
			// Copy user-supplied thumbnail file into the main destination directory
			if (!copy(filename_decode($thumbFolderName.$mediaFile), filename_decode($folderName.$mediaFile))) { 
				// the file cannot be copied
				$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES["thumbnail"]["error"]]."<br />";
			} else {
				// Set file permission to read/write for everybody
//				@chmod(filename_decode($folderName.$mediaFile), 0644);
				AddToLog("Media file ".$folderName.$mediaFile." uploaded by >".getUserName()."<");
			}
		}
		if ($error=="" && !empty($_FILES["mediafile"]["name"]) && empty($_FILES["thumbnail"]["name"])) {
			if (!empty($_POST['genthumb']) && ($_POST['genthumb']=="yes")) {
				// Generate thumbnail from main image
				$ct = preg_match("/\.([^\.]+)$/", $mediaFile, $match);
				if ($ct>0) {
					$ext = strtolower(trim($match[1]));
					if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png") {
						$okThumb = generate_thumbnail($folderName.$mediaFile, $thumbFolderName.$mediaFile, "OVERWRITE");
						$thumbnail = $thumbFolderName.$mediaFile;
						if (!$okThumb) {
							$error .= print_text("thumbgen_error",0,1);
						} else {
							// Set file permission on thumbnail to read/write for everybody
//							@chmod(filename_decode($thumbFolderName.$mediaFile), 0644);
							print_text("thumb_genned");
							print "<br />";
							AddToLog("Media thumbnail ".$thumbFolderName.$mediaFile." generated by >".getUserName()."<");
						}
					}
				}
			}
		}
		// Let's see if there are any errors generated and print it
		if (!empty($error)) {
			print "<span class=\"largeError\">".$error."</span><br />\n";
			$mediaFile = "";
			$finalResult = false;
		} else $finalResult = true;
	}
	if ($mediaFile=="") {
		// No upload: should be an existing file on server
		if ($tag[0]=="FILE") {
			if (!empty($text[0])) {
				$isExternal = strstr($text[0], "://");
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
				print "<span class=\"largeError\">".$pgv_lang["illegal_chars"]."</span><br />\n";
				$finalResult = false;
			} else $finalResult = true;
		} else {
			//-- check if the file is used in more than one gedcom
			//-- do not allow it to be moved or renamed if it is
			$myFile = str_replace($MEDIA_DIRECTORY, "", $oldFolder.$oldFilename);
			$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_file LIKE '%".$DBCONN->escape($myFile)."'";
			$res = dbquery($sql);
			$onegedcom = true;
			while($row=$res->fetchRow(DB_FETCHMODE_ASSOC)) {
				if ($row['m_gedfile']!=$GEDCOMS[$GEDCOM]['id']) $onegedcom = false;
			}
			$res->free();
			
			// Handle Admin request to rename or move media file
			if ($filename!=$oldFilename) {
				$parts = pathinfo($filename);
				if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
					$parts = pathinfo($oldFilename);
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
			
			$finalResult = true;
			if ($filename!=$oldFilename || $folder!=$oldFolder) {
				if (!$onegedcom) {
					print "<span class=\"largeError\">".$pgv_lang["multiple_gedcoms"]."<br /><br /><b>";
					if ($filename!=$oldFilename) print $pgv_lang["media_file_not_renamed"];
					else print $pgv_lang["media_file_not_moved"];
					print "</b></span><br />";
					$finalResult = false;
				} else {
					$oldMainFile = $oldFolder.$oldFilename;
					$newMainFile = $folder.$filename;
					$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldMainFile);
					$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $newMainFile);
					$isMain = file_exists(filename_decode($oldMainFile));
					$okMain = !file_exists(filename_decode($newMainFile));
					$isThum = file_exists(filename_decode($oldThumFile));
					$okThum = !file_exists(filename_decode($newThumFile));
					if ($okMain && $okThum) {
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
							print "<span class=\"largeError\">";
							print_text("main_media_fail".$mediaAction);
							print "</span>"; 
						}
					}
					print "<br />";
        			
					if (!$isThum) {
						print_text("thumb_media_fail0");
					} else {
						if ($okThum) print_text("thumb_media_ok".$mediaAction); 
						else {
							$finalResult = false;
							print "<span class=\"largeError\">";
							print_text("thumb_media_fail".$mediaAction);
							print "</span>"; 
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
		$newged = "0 @".$media_id."@ OBJE\r\n";
		//-- set the FILE text to the correct file location
		$text[0] = $folderName.$mediaFile;
    	
		$newged = handle_updates($newged);
		
		require_once 'includes/media_class.php';
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
				print "<a href=\"javascript:// OBJE $mediaid\" onclick=\"openerpasteid('$mediaid'); return false;\">".$pgv_lang["paste_id_into_field"]." <b>$mediaid</b></a><br /><br />\n";
				print "<script language=\"JavaScript\" type=\"text/javascript\">\n";
				print "openerpasteid('".$mediaid."');\n";
				print "</script>\n";
			}
		}
		print $pgv_lang["update_successful"];
	}
}

if ($action == "update") {
	if (empty($level)) $level = 1;
	//-- check if the file is used in more than one gedcom
	//-- do not allow it to be moved or renamed if it is
	$myFile = str_replace($MEDIA_DIRECTORY, "", $oldFolder.$oldFilename);
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_file LIKE '%".$DBCONN->escape($myFile)."'";
	$res = dbquery($sql);
	$onegedcom = true;
	while($row=$res->fetchRow(DB_FETCHMODE_ASSOC)) {
		if ($row['m_gedfile']!=$GEDCOMS[$GEDCOM]['id']) $onegedcom = false;
	}
	$res->free();
	
	$isExternal = strstr($oldFilename, "://") || strstr($filename, "://");
	$finalResult = true;
		
	// Handle Admin request to rename or move media file
	if (!$isExternal) {
		if ($filename!=$oldFilename) {
			$parts = pathinfo($filename);
			if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
				$parts = pathinfo($oldFilename);
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
	}
		
	if ($filename!=$oldFilename || $folder!=$oldFolder) {
		if (!$onegedcom) {
			print "<span class=\"largeError\">".$pgv_lang["multiple_gedcoms"]."<br /><br /><b>";
			if ($filename!=$oldFilename) print $pgv_lang["media_file_not_renamed"];
			else print $pgv_lang["media_file_not_moved"];
			print "</b></span><br />";
			$finalResult = false;
		} else if (!$isExternal) {
			$oldMainFile = $oldFolder.$oldFilename;
			$newMainFile = $folder.$filename;
			$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldMainFile);
			$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $newMainFile);
			$isMain = file_exists(filename_decode($oldMainFile));
			$okMain = !file_exists(filename_decode($newMainFile));
			$isThum = file_exists(filename_decode($oldThumFile));
			$okThum = !file_exists(filename_decode($newThumFile));
			if ($okMain && $okThum) {
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
					print "<span class=\"largeError\">";
					print_text("main_media_fail".$mediaAction);
					print "</span>"; 
				}
			}
			print "<br />";
        	
			if (!$isThum) {
				print_text("thumb_media_fail0");
			} else {
				if ($okThum) print_text("thumb_media_ok".$mediaAction); 
				else {
					$finalResult = false;
					print "<span class=\"largeError\">";
					print_text("thumb_media_fail".$mediaAction);
					print "</span>"; 
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
		// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
 		$glevels = array_merge(array("1"), $glevels);
 		$tag = array_merge(array("FILE"), $tag);
 		$islink = array_merge(array(0), $islink);
 		$text = array_merge(array($folder.$filename), $text);
 		
		$newrec = "0 @$pid@ OBJE\r\n";
		$newrec = handle_updates($newrec);
		//print("[".$newrec."]");
		//-- look for the old record media in the file
		//-- if the old media record does not exist that means it was 
		//-- generated at import and we need to append it
		$oldrec = find_record_in_file($pid);
		if (!empty($oldrec)) {
			if (replace_gedrec($pid, $newrec)) AddToChangeLog("Media ID ".$pid." successfully updated.");
		} else {
			$pid = append_gedrec($newrec);
			if ($pid) AddToChangeLog("Media ID ".$pid." successfully added.");
		}
    	
		if ($pid && $linktoid!="") {
			$link = linkMedia($pid, $linktoid, $level);
			if ($link) {
				AddToChangeLog("Media ID ".$pid." successfully added to $linktoid.");
			}
		}
	}

	if ($finalResult) print $pgv_lang["update_successful"];
}

if ($action=="delete") {
	if (delete_gedrec($pid)) {
		AddToChangeLog("Media ID ".$pid." successfully deleted.");
		print $pgv_lang["update_successful"];
	}
}

if ($action=="showmedia") {
	$medialist = get_db_media_list();
	if (count($medialist)>0) {
		print "<table class=\"list_table\">\n";
		print "<tr><td class=\"list_label\">".$pgv_lang["delete"]."</td><td class=\"list_label\">".$pgv_lang["title"]."</td><td class=\"list_label\">".$pgv_lang["gedcomid"]."</td>\n";
		print "<td class=\"list_label\">".$factarray["FILE"]."</td><td class=\"list_label\">".$pgv_lang["highlighted"]."</td><td class=\"list_label\">order</td><td class=\"list_label\">gedcom</td></tr>\n";
		foreach($medialist as $indexval => $media) {
			print "<tr>";
			print "<td class=\"list_value\"><a href=\"addmedia.php?action=delete&m_id=".$media["ID"]."\">delete</a></td>";
			print "<td class=\"list_value\"><a href=\"addmedia.php?action=edit&m_id=".$media["ID"]."\">edit</a></td>";
			print "<td class=\"list_value\">".$media["TITL"]."</td>";
			print "<td class=\"list_value\">";
			print_list_person($media["INDI"], array(get_person_name($media["INDI"]), $GEDCOM));
			print "</td>";
			print "<td class=\"list_value\">".$media["FILE"]."</td>";
			print "<td class=\"list_value\">".$media["_PRIM"]."</td>";
			print "<td class=\"list_value\">".$media["ORDER"]."</td>";
			print "<td class=\"list_value\">".$media["GEDFILE"]."</td>";
			print "</tr>\n";
		}
		print "</table>\n";
	}
}


if ($action=="showmediaform") {
	if (!isset($pid)) $pid = "";
	if (empty($level)) $level = 1;
	show_media_form($pid, "newentry", $filename, $linktoid, $level);
}

if ($action=="editmedia") {
	if (!isset($pid)) $pid = "";
	if (empty($level)) $level = 1;
	show_media_form($pid, "update", $filename, $linktoid, $level);
}

print "<br />";
print "<div class=\"center\"><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
print "<br />";
print_simple_footer();
?>
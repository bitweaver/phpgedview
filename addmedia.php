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
 * @version $Id: addmedia.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

/**
 * load config file
 */
require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

require("includes/functions_edit.php");

if (empty($ged)) $ged = $GEDCOM;
$GEDCOM = $ged;

print_simple_header($pgv_lang["add_media_tool"]);

//-- only allow users with edit privileges to access script.
if (!userIsAdmin(getUserName())) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

if ($_SESSION["cookie_login"]) {
	header("Location: login.php?ged=$GEDCOM&url=addmedia.php");
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

if (!isset($m_ext)) $m_ext="";
if (!isset($m_titl)) $m_titl="";
if (!isset($m_file)) $m_file="";

// NOTE: Store the entered data
if ($action=="newentry") {
	// NOTE: Setting the pid
	if (isset($gid)) $pid = $gid;
	
	// NOTE: Check for file upload
	if (count($_FILES)>0) {
		$uploaded_files = array();
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		if (!empty($folder)) {
			if (substr($folder,0,1) == "/") $folder = substr($folder,1);
			if (substr($folder,-1,1) != "/") $folder .= "/";
		}
		foreach($_FILES as $upload) {
			$filename = check_media_depth($folder.basename($upload['name']));
			$thumbnail = thumbnail_file($folder.basename($upload['name']));
			if (!empty($upload['tmp_name'])) {
				if (!move_uploaded_file($upload['tmp_name'], $filename)) {
					$error .= "<br />".$gm_lang["upload_error"]."<br />".$upload_errors[$upload['error']];
					$uploaded_files[] = "";
				}
				else {
					$uploaded_files[] = $filename;
					if (!is_dir($MEDIA_DIRECTORY.$folder."thumbs")) mkdir($MEDIA_DIRECTORY.$folder."thumbs");
					if (!empty($error)) {
						print "<span class=\"error\">".$error."</span>";
					}
				}
			}
			else $uploaded_files[] = "";
		}
	}
	
	// NOTE: Build the gedcom record
	// NOTE: Level 0
	$media_id = get_new_xref("OBJE");
	$newged = "0 @".$media_id."@ OBJE\r\n";
	
	// NOTE: File record
	$newged .= "1 FILE ";
	if (isset($filename) && !empty($filename)) $newged .= $folder.basename($filename);
	else $newged .= $text[0];
	$newged .= "\r\n";
	$newged .= "2 FORM ".$text[1]."\r\n";
	if (!empty($text[2])) $newged .= "3 TYPE ".$text[2]."\r\n";
	if (!empty($text[3])) $newged .= "2 TITL ".$text[3]."\r\n";
	
	// NOTE: Reference record
	if (!empty($text[4])) {
		$newged .= "1 REFN ".$text[4]."\r\n";
		if (!empty($text[5])) $newged .= "2 TYPE ".$text[5]."\r\n"; 
	}
	
	// NOTE: Record ID record
	if (!empty($text[6])) $newged .= "1 RIN ".$text[6]."\r\n";
	
	// NOTE: Note record
	if (!empty($text[7])) $newged .= trim(textblock_to_note(1,$text[7]))."\r\n";
	
	// NOTE: Source record
	if (!empty($text[8])) $newged .= "1 SOUR @".$text[8]."@\r\n";
	
	// NOTE: Primary record
	if (!empty($text[9])) $newged .= "1 _PRIM ".$text[9]."\r\n";
	
	// NOTE: Thumbnail record
	if (!empty($text[10])) $newged .= "1 _THUMB ".$text[10]."\r\n";
	
	// NOTE: Change record
	$newged .= "1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
	$newged .= "3 TIME ".date("H:i:s")."\r\n";
	$newged .= "2 _PGVU ".getUserName()."\r\n";
	
	$xref = append_gedrec($newged);
	// NOTE: Add media item to change file
	if (replace_gedrec($media_id, $newged, true, $pid)) AddToChangeLog("Media ID ".$media_id." successfully added.");
	
	$type = $pid{0};
	if ($type == $GEDCOM_ID_PREFIX) {
		$indilist = get_indi_list();
		$newrec = $indilist[$pid]["gedcom"];
		$newrec .= "\r\n1 OBJE @".$media_id."@\r\n";
	}
	if ($type == $FAM_ID_PREFIX) {
		$famlist = get_fam_list();
		$newrec = $famlist[$pid]["gedcom"];
		$newrec .= "\r\n1 OBJE @".$media_id."@\r\n";
	}
	if ($type == $SOURCE_ID_PREFIX) {
		$sourcelist = get_source_list();
		$newrec = $sourcelist[$pid]["gedcom"];
		$newrec .= "\r\n1 OBJE @".$media_id."@\r\n";
	}
	if ($type == $MEDIA_ID_PREFIX) {
		$medialist = get_media_list();
		$newrec = $medialist[$pid]["gedcom"];
		$newrec .= "\r\n1 OBJE @".$media_id."@\r\n";
	}
	// NOTE: Update record where media is added to
	if (replace_gedrec($pid, $newrec, true, $media_id)) AddToChangeLog("Media ID ".$media_id." successfully added to $pid.");
	print $pgv_lang["update_successful"];
}

if ($action == "update") {
	// $medialist = get_media_list();
	$newrec = "0 @$pid@ OBJE\r\n";
	$newrec = handle_updates($newrec);
	//print("[".$newrec."]");
	//-- look for the old record media in the file
	//-- if the old media record does not exist that means it was 
	//-- generated at import and we need to append it
	$oldrec = find_record_in_file($pid);
	if (!empty($oldrec)) {
		//print "old record found";
		if (replace_gedrec($pid, $newrec)) AddToChangeLog("Media ID ".$pid." successfully added to $pid.");
	}
	else {
		//print "old record not found";
		if (append_gedrec($newrec)) AddToChangeLog("Media ID ".$pid." successfully added to $pid.");
	}
	print $pgv_lang["update_successful"];
}

if ($action=="delete") {
	if (delete_gedrec($pid)) AddToChangeLog("Media ID ".$pid." successfully deleted.");
	print $pgv_lang["update_successful"];
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
	show_media_form($pid);
}

if ($action=="editmedia") {
	show_media_form($pid, "update");
}

if ($action=="injectmedia") {
	// NOTE: Inject media is used to put multimedia records into indi/fam/source records.
	$medialist = get_db_media_list();
	
	// check for already imported media
	$test = find_record_in_file($medialist[0]["XREF"]);
	if ($test) {
		print "<div align=\"center\" class=\"error\" ><h2>This gedcom has already had the media information inserted into it, operation aborted</h3></div>";
	} else {

		$ct = 0;
		$nct = 0;
		foreach($medialist as $indexval => $media) {
			$mediarec = "\r\n0 @".$media["XREF"]."@ OBJE";
			$mediarec .= "\r\n1 FILE ".$media["FILE"];
			$mediarec .= "\r\n1 TITL ".$media["TITL"];
			$mediarec .= "\r\n1 FORM ".$media["FORM"];
			if (strlen($media["NOTE"])>0) {$mediarec .= "\r\n".$media["NOTE"]; $nct++;};
			$pos1 = strrpos($fcontents, "0");
			$fcontents = substr($fcontents, 0, $pos1).trim($mediarec)."\r\n".substr($fcontents, $pos1);
			write_file();
			$ct++;
		}
		print "<center>$ct media items added, $nct with notes</center>";

		$ct = 0;
		$nct = 0;
		$mappinglist = get_db_mapping_list();
		$oldindi = "";
		for ($i=0; $i < count($mappinglist); $i++) {
			$media = $mappinglist[$i];
			$indi = $media["INDI"];
			if ($indi != $oldindi) {
				if ($i > 0) { db_replace_gedrec($oldindi, $indirec);};
				$oldindi = $indi;
				$indirec = find_record_in_file($indi);
			}
		    if (strlen($media["NOTE"])>0) {$indirec .= "\r\n".trim($media["NOTE"]); $nct++;};

		}
		db_replace_gedrec($indi, $indirec);

		print "<center>$ct link items added, $nct with notes</center>";
		print "<p><center>".$pgv_lang["adds_completed"]."<center></p><br /><br />\n";
	}
	print "<p><center><a href=\"#\" onclick=\"window.close();\">".$pgv_lang["close_window"]."</a></center></p><br /><br />\n";
} 
print "<br />";
print "<div class=\"center\"><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
print "<br />";
print_simple_footer();
?>
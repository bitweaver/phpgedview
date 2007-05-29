<?php
/**
 * Functions for exporting data
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * @version $Id: functions_export.php,v 1.6 2007/05/29 19:21:11 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/*
 * Create a header for a (newly-created or already-imported) gedcom file.
 */
function gedcom_header($gedfile)
{
	global $CHARACTER_SET, $GEDCOMS, $VERSION, $pgv_lang, $gBitSystem;

	// Default values for a new header
	$SOUR="1 SOUR PhpGedView\r\n2 NAME Bitweaver PhpGedView Online Genealogy\r\n2 VERS $VERSION\r\n";
	$DEST="1 DEST DISKETTE\r\n";
	$DATE="1 DATE ".strtoupper(date("d M Y"))."\r\n2 TIME ".date("H:i:s")."\r\n";
	$GEDC="1 GEDC\r\n2 VERS 5.5.1\r\n2 FORM Lineage-Linked\r\n";
	$CHAR="1 CHAR $CHARACTER_SET\r\n";
	$FILE="1 FILE $gedfile\r\n";
	$LANG="";
	$PLAC="1 PLAC\r\n2 FORM ".$pgv_lang["default_form"]."\r\n";
	$COPR="";
	$SUBN="";
	$SUBM="1 SUBM @SUBM@\r\n0 @SUBM@ SUBM\r\n1 NAME ".getUserName()."\r\n"; // The SUBM record is mandatory

	// Preserve some values from the original header
	if (isset($GEDCOMS[$gedfile])) {
		$head=find_gedcom_record("HEAD");
		if (preg_match("/(1 CHAR [^\r\n]+)/", $head, $match))
			$CHAR=$match[1]."\r\n";
		if (preg_match("/1 PLAC[\r\n]+2 FORM ([^\r\n]+)/", $head, $match))
			$PLAC="1 PLAC\r\n2 FORM ".$match[1]."\r\n";
		if (preg_match("/(1 LANG [^\r\n]+)/", $head, $match))
			$LANG=$match[1]."\r\n";
		if (preg_match("/(1 SUBN [^\r\n]+)/", $head, $match))
			$SUBN=$match[1]."\r\n";
		if (preg_match("/(1 COPR [^\r\n]+)/", $head, $match))
			$COPR=$match[1]."\r\n";
		// Link to SUBM/SUBN records, if they exist
		$sql="SELECT o_id FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_type='SUBN' AND o_file=".$GEDCOMS[$gedfile]["id"];
		$res = $gBitSystem->mDb->query($sql);
		if ($res->numRows()>0) {
			$row=$res->fetchRow();
			$SUBN="1 SUBN @".$row[0]."@\r\n";
		}
		$sql="SELECT o_id FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_type='SUBM' AND o_file=".$GEDCOMS[$gedfile]["id"];
		$res = $gBitSystem->mDb->query($sql);
		if ($res->numRows()>0) {
			$row=$res->fetchRow();
			$SUBM="1 SUBM @".$row[0]."@\r\n";
		}
	}

	return "0 HEAD\r\n".$SOUR.$DEST.$DATE.$GEDC.$CHAR.$FILE.$COPR.$LANG.$PLAC.$SUBN.$SUBM;
}

function print_gedcom($privatize_export='', $privatize_export_level='', $convert='', $remove='', $zip='', $gedout='') {
		global $GEDCOMS, $GEDCOM, $ged, $pgv_lang, $CHARACTER_SET, $gBitSystem;
		global $GEDCOM_ID_PREFIX, $SOURCE_ID_PREFIX, $FAM_ID_PREFIX, $REPO_ID_PREFIX, $MEDIA_ID_PREFIX;

		if ($privatize_export == "yes") {
			create_export_user($privatize_export_level);
			if (isset ($_SESSION)) {
				$_SESSION["org_user"] = $_SESSION["pgv_user"];
				$_SESSION["pgv_user"] = "export";
			}
			if (isset ($HTTP_SESSION_VARS)) {
				$HTTP_SESSION_VARS["org_user"] = $HTTP_SESSION_VARS["pgv_user"];
				$HTTP_SESSION_VARS["pgv_user"] = "export";
			}
		}

		$GEDCOM = $ged;

		$head=gedcom_header($ged);
		if ($convert == "yes") {
			$head = preg_replace("/UTF-8/", "ANSI", $head);
			$head = utf8_decode($head);
		}
		$head = remove_custom_tags($head, $remove);
		if ($zip == "yes")
			fwrite($gedout, $head);
		else
			print $head;

		$sql = "SELECT i_gedcom,i_id as id FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			if ($privatize_export == "yes")
				$rec = privatize_gedcom($rec);
			if ($convert == "yes")
				$rec = utf8_decode($rec);
			if ($zip == "yes")
				fwrite($gedout, $rec);
			else
				print $rec;
		}
		$res->free();

		$sql = "SELECT f_gedcom, f_id as id FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			if ($privatize_export == "yes")
				$rec = privatize_gedcom($rec);
			if ($convert == "yes")
				$rec = utf8_decode($rec);
			if ($zip == "yes")
				fwrite($gedout, $rec);
			else
				print $rec;
		}
		$res->free();

		$sql = "SELECT s_gedcom, s_id as id FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			if ($privatize_export == "yes")
				$rec = privatize_gedcom($rec);
			if ($convert == "yes")
				$rec = utf8_decode($rec);
			if ($zip == "yes")
				fwrite($gedout, $rec);
			else
				print $rec;
		}
		$res->free();

		$sql = "SELECT o_gedcom, o_type, o_id as id FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$key = $row[1];
			if (($key != "HEAD") && ($key != "TRLR")) {
				$rec = remove_custom_tags($rec, $remove);
				if ($privatize_export == "yes")
					$rec = privatize_gedcom($rec);
				if ($convert == "yes")
					$rec = utf8_decode($rec);
				if ($zip == "yes")
					fwrite($gedout, $rec);
				else
					print $rec;
			}
		}
		$res->free();

		$sql = "SELECT m_gedrec, m_media as id FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_gedfile=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			if ($privatize_export == "yes")
				$rec = privatize_gedcom($rec);
			if ($convert == "yes")
				$rec = utf8_decode($rec);
			if ($zip == "yes")
				fwrite($gedout, $rec);
			else
				print $rec;
		}
		$res->free();

		if ($zip == "yes")
			fwrite($gedout, "0 TRLR\r\n");
		else
			print "0 TRLR\r\n";

		if ($privatize_export == "yes") {
			if (isset ($_SESSION)) {
				$_SESSION["pgv_user"] = $_SESSION["org_user"];
			}
			if (isset ($HTTP_SESSION_VARS)) {
				$HTTP_SESSION_VARS["pgv_user"] = $HTTP_SESSION_VARS["org_user"];
			}
			deleteuser("export");
		}
	}
	function print_gramps($privatize_export='', $privatize_export_level='', $convert='', $remove='', $zip='', $gedout='') {
		global $GEDCOMS, $GEDCOM, $ged, $pgv_lang;
		global $gBitSystem;

		require_once ("includes/GEDownloadGedcom.php");
		$geDownloadGedcom = new GEDownloadGedcom();
		$geDownloadGedcom->begin_xml();

		$sql = "SELECT i_gedcom, i_id FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY i_id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			$geDownloadGedcom->create_person($rec, $row[1]);
		}
		$res->free();

		$sql = "SELECT f_gedcom, f_id FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY f_id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			$geDownloadGedcom->create_family($rec, $row[1]);
		}
		$res->free();

		$sql = "SELECT s_gedcom, s_id FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY s_id";
		$res = $gBitSystem->mDb->query($sql);
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			$geDownloadGedcom->create_source($row[1], $rec);
		}
		$res->free();

		$sql = "SELECT m_gedrec, m_media FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_gedfile=" . $GEDCOMS[$GEDCOM]['id'] . " ORDER BY m_media";
		$res = $gBitSystem->mDb->query($sql);
		
		while ($row = $res->fetchRow()) {
			$rec = trim($row[0]) . "\r\n";
			$rec = remove_custom_tags($rec, $remove);
			$mediaID = get_id_from_record($rec);
			$geDownloadGedcom->create_media($mediaID,$rec, $row[1]);
		}
		$res->free();
		if($zip !== "no")
		{
			fwrite($gedout,$geDownloadGedcom->dom->saveXML());
		}
		else
			print $geDownloadGedcom->dom->saveXML();
			//$geDownloadGedcom->validate($geDownloadGedcom->dom);
	}
	
	function get_id_from_record($record)
	{
		
		preg_match('~0 @(.*)@ (.*)~',$record, $varMatch);
		return $varMatch[1];
	}
	
function um_export($proceed) {
	global $INDEX_DIRECTORY, $pgv_lang, $gBitSystem;
	
	// Get user array and create authenticate.php
	if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_creating"]." \"authenticate.php\"<br /><br />";
	$authtext = "<?php\n\n\$users = array();\n\n";
	$users = GetUsers();
	foreach($users as $key=>$user) {
//		$user["firstname"] = $user["firstname"];
//		$user["lastname"] = $user["lastname"];
//		$user["comment"] = $user["comment"];
		$authtext .= "\$user = array();\n";
		foreach($user as $ukey=>$value) {
			if (!is_array($value)) {
				$value = preg_replace('/"/', '\\"', $value);
				$authtext .= "\$user[\"$ukey\"] = '$value';\n";
			}
			else {
				$authtext .= "\$user[\"$ukey\"] = array();\n";
				foreach($value as $subkey=>$subvalue) {
					$subvalue = preg_replace('/"/', '\\"', $subvalue);
					$authtext .= "\$user[\"$ukey\"][\"$subkey\"] = '$subvalue';\n";
				}
			}
		}
		$authtext .= "\$users[\"$key\"] = \$user;\n\n";
	}
	$authtext .= "?".">\n";
	if (file_exists($INDEX_DIRECTORY."authenticate.php")) {
		print $pgv_lang["um_file_create_fail1"]." ".$INDEX_DIRECTORY."authenticate.php<br /><br />";
	}
	else {
		$fp = fopen($INDEX_DIRECTORY."authenticate.php", "w");
		if ($fp) {
			fwrite($fp, $authtext);
			fclose($fp);
			$logline = AddToLog("authenticate.php updated by >".getUserName()."<");
 			if (!empty($COMMIT_COMMAND)) check_in($logline, "authenticate.php", $INDEX_DIRECTORY);	
			if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_file_create_succ1"]." authenticate.php<br /><br />";
		}
		else print $pgv_lang["um_file_create_fail2"]." ".$INDEX_DIRECTORY."authenticate.php. ".$pgv_lang["um_file_create_fail3"]."<br /><br />";
	}

	// Get favorites and create favorites.dat 
	if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_creating"]." \"favorites.dat\"<br /><br />";
	$favorites = array();
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."favorites";
	$res = $gBitSystem->mDb->query($sql);
	$favid = 1;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$favorite = array();
		$favorite["id"] = $favid;
		$favid = $favid + 1;
		$favorite["username"] = $row["fv_username"];
		$favorite["gid"] = $row["fv_gid"];
		$favorite["type"] = $row["fv_type"];
		$favorite["file"] = $row["fv_file"];
		$favorite["title"] = $row["fv_title"];
		$favorite["note"] = $row["fv_note"];
		$favorite["url"] = $row["fv_url"];
		$favorites[] = $favorite;
	}
	if ($favid > 1) {
		$mstring = serialize($favorites);
		if (file_exists($INDEX_DIRECTORY."favorites.dat")) {
			print $pgv_lang["um_file_create_fail1"]." ".$INDEX_DIRECTORY."favorites.dat<br /><br />";
			}
		else {
			$fp = fopen($INDEX_DIRECTORY."favorites.dat", "wb");
			if ($fp) {
				fwrite($fp, $mstring);
				fclose($fp);
				$logline = AddToLog("favorites.dat updated by >".getUserName()."<");
 				if (!empty($COMMIT_COMMAND)) check_in($logline, "favorites.dat", $INDEX_DIRECTORY);	
				if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_file_create_succ1"]." favorites.dat<br /><br />";
			}
			else print $pgv_lang["um_file_create_fail2"]." ".$INDEX_DIRECTORY."favorites.dat. ".$pgv_lang["um_file_create_fail3"]."<br /><br />";
		}
	}
	else {
		if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_nofav"]." ".$pgv_lang["um_file_not_created"]."<br /><br />";
	}

	// Get blocks and create blocks.dat 
	if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_creating"]." \"blocks.dat\"<br /><br />";
	$allblocks = array();
	$blocks["main"] = array();
	$blocks["right"] = array();
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."blocks ORDER BY b_location, b_order";
	$res = $gBitSystem->mDb->query($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$blocks = array();
		$blocks["username"] = $row["b_username"];
		$blocks["location"] = $row["b_location"];
		$blocks["order"] = $row["b_order"];
		$blocks["name"] = $row["b_name"];
		$blocks["config"] = unserialize($row["b_config"]);
		$allblocks[] = $blocks;
	}
	if (count($allblocks) > 0) {
		$mstring = serialize($allblocks);
		if (file_exists($INDEX_DIRECTORY."blocks.dat")) {
			print $pgv_lang["um_file_create_fail1"]." ".$INDEX_DIRECTORY."blocks.dat<br /><br />";
		}
		else {
			$fp = fopen($INDEX_DIRECTORY."blocks.dat", "wb");
			if ($fp) {
				fwrite($fp, $mstring);
				fclose($fp);
				$logline = AddToLog("blocks.dat updated by >".getUserName()."<");
 				if (!empty($COMMIT_COMMAND)) check_in($logline, "blocks.dat", $INDEX_DIRECTORY);	
				if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_file_create_succ1"]." blocks.dat<br /><br />";
			}
			else print $pgv_lang["um_file_create_fail2"]." ".$INDEX_DIRECTORY."blocks.dat. ".$pgv_lang["um_file_create_fail3"]."<br /><br />";
		}
	}
	else {
		if (($proceed == "export") || ($proceed == "exportovr")) print $pgv_lang["um_noblocks"]." ".$pgv_lang["um_file_not_created"]."<br /><br />";
	}
}
?>

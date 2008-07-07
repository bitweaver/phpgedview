<?php

/**
 * Various functions used by the media DB interface
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005 Peter Dyson, John Finlay and Others
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
 * @subpackage MediaDB
 * @version $Id: functions_mediadb.php,v 1.11 2008/07/07 17:30:13 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/*
 *******************************
 * Database Interface functions
 *******************************/

/**
 * Removes a media item from this gedcom.
 *
 * Removes the main media record and any associated links
 * to individuals.
 *
 * @param string $media The gid of the record to be removed in the form Mxxxx.
 * @param string $ged The gedcom file this action is to apply to.
 */
function remove_db_media($media, $ged) {
	global $gGedcom;

	$success = false;

	// remove the media record
	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_media='$media' AND m_gedfile='$ged'";
	if ($res =& $gGedcom->mDb->query($sql)) $success = true;

	// remove all links to this media item
	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_media='$media' AND mm_gedfile='$ged'";
	if ($res =& $gGedcom->mDb->query($sql)) $success = true;
		$success = true;

	return $success;
}

/**
 * Removes a media item from a individual.
 *
 * Removes this link to an individual from the database.
 * All records attached to this link are lost also.
 *
 * @param string $media The gid of the record to be removed in the form Mxxxx.
 * @param string $indi The gid that this media is linked to Ixxx Fxxx ect.
 * @param string $ged The gedcom file this action is to apply to.
 */
function unlink_db_item($media, $indi, $ged) {
	global $gGedcom;
	
	// remove link to this media item
	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE (mm_media='".addslashes($media)."' AND mm_gedfile='".addslashes($ged)."' AND mm_gid='".addslashes($indi)."')";
	$tempsql = $gGedcom->mDb->query($sql);
	$res = & $tempsql;

}

/**
 * Queries the existence of a link in the db.
 *
 * @param string $media The gid of the record to be removed in the form Mxxxx.
 * @param string $gedrec The gedcom record as a string without the gid.
 * @param string $indi The gid that this media is linked to Ixxx Fxxx ect.
 * @return boolean
 */
function exists_db_link($media, $indi, $ged) {
	global $gGedcom;

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_gedfile='".$gGedcom->mGEDCOMId."' AND mm_gid='".addslashes($indi)."' AND mm_media='".addslashes($media)."'";
	$tempsql = $gGedcom->mDb->query($sql);
	$exists = ($res->numRows())>0;
	return $exists;
}

/**
 * Updates any gedcom records associated with the media.
 *
 * Replace the gedrec for the media record.
 *
 * @param string $media The gid of the record to be removed in the form Mxxxx.
 * @param string $gedrec The gedcom record as a string without the gid.
 * @param string $ged The gedcom file this action is to apply to.
 */
function update_db_media($media, $gedrec, $ged) {
	global $gGedcom, $TBLPREFIX;

	// replace the gedrec for the media record
	$sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."media SET m_gedrec = '".addslashes($gedrec)."' WHERE (m_id = '".addslashes($media)."' AND m_gedfile = '".$gGedcom->mGEDCOMId."')";
	$tempsql = $gGedcom->mDb->query($sql);
	$res = & $tempsql;

}

/**
 * Updates any gedcom records associated with the link.
 *
 * Replace the gedrec for an existing link record.
 *
 * @param string $media The gid of the record to be updated in the form Mxxxx.
 * @param string $indi The gid that this media is linked to Ixxx Fxxx ect.
 * @param string $gedrec The gedcom record as a string without the gid.
 * @param string $ged The gedcom file this action is to apply to.
 * @param integer $order The order that this record should be displayed on the gid. If not supplied then
 *                       the order is not replaced.
 */
function update_db_link($media, $indi, $gedrec, $ged, $order = -1) {
	global $TBLPREFIX, $gGedcom;

	if (exists_db_link($media, $indi, $ged)) {
		// replace the gedrec for the media link record
		$sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."media_mapping SET mm_gedrec = '".addslashes($gedrec)."'";
		if ($order >= 0)
			$sql .= ", mm_order = $order";
		$sql .= " WHERE (mm_media = '" . addslashes($media) . "' AND mm_gedfile = '" . $gGedcom[$ged]["id"] . "' AND mm_gid = '" . addslashes($indi) . "')";
		$tempsql = $gBitSystem->mDb->query($sql);
		if ($res = & $tempsql) {
			AddToLog("Media record: " . $media . " updated successfully");
			return true;
		} else {
			AddToLog("There was a problem updating media record: " . $media);
			return false;
		}
	} else {
		add_db_link($media, $indi, $gedrec, $ged, $order = -1);
	}

}

/**
 * Adds a new link into the database.
 *
 * Replace the gedrec for an existing link record.
 *
 * @param string $media The gid of the record to be updated in the form Mxxxx.
 * @param string $indi The gid that this media is linked to Ixxx Fxxx ect.
 * @param string $gedrec The gedcom record as a string without the gid.
 * @param string $ged The gedcom file this action is to apply to.
 * @param integer $order The order that this record should be displayed on the gid. If not supplied then
 *                       the order is not replaced.
 */
function add_db_link($media, $indi, $gedrec, $ged, $order = -1) {
	global $TBLPREFIX, $gGedcom, $DBCONN;

	// if no preference to order find the number of records and add to the end
	if ($order = -1) {
		$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_gedfile='".$gGedcom->mGEDCOMId."' AND mm_gid='".addslashes($indi)."'";
		$tempsql = $gBitSystem->mDb->query($sql);
		$res =& $tempsql;
		$order=$res->numRows() +1;
		$res->free();
	}

	// add the new media link record
	$mm_id = get_next_id("media_mapping", "mm_id");
	$sql = "INSERT INTO ".PHPGEDVIEW_DB_PREFIX."media_mapping VALUES('".$mm_id."','".addslashes($media)."','".addslashes($indi)."','".addslashes($order)."','".$gGedcom->mGEDCOMId."','".addslashes($gedrec)."')";
	$tempsql = $gBitSystem->mDb->query($sql);
	if ($res =& $tempsql) {
		AddToChangeLog("New media link added to the database: " . $media);
		return true;
	} else {
		AddToChangeLog("There was a problem adding media record: " . $media);
		return false;
	}

}

/*
 ****************************
 * general functions
 ****************************/

/**
 * Get the list of media from the database
 *
 * Searches the media table of the database for media items that
 * are associated with the currently active GEDCOM.
 *
 * The medialist that is returned contains the following elements:
 * - $media["ID"] the unique id of this media item in the table (Mxxxx)
 * - $media["GEDFILE"] the gedcom file the media item should be added to
 * - $media["FILE"] the filename of the media item
 * - $media["FORM"] the format of the item (ie JPG, PDF, etc)
 * - $media["TITL"] a title for the item, used for list display
 * - $media["NOTE"] gedcom record snippet
 *
 * @return mixed A media list array.
 */
function get_db_media_list() {
	global $GEDCOM, $gGedcom;
	global $TBLPREFIX;

	$medialist = array ();
	$sql = "SELECT m_id, m_media, m_file, m_ext, m_titl, m_gedrec FROM {$TBLPREFIX}media WHERE m_gedfile='{$gGedcom->mGEDCOMId}' ORDER BY m_id";
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_gedfile='".$gGedcom->mGEDCOMId."' ORDER BY m_id";
	$res = $gBitSystem->mDb->query($sql);
	$res = & $tempsql;
	$ct = $res->numRows();
	while ($row = & $res->fetchRow(DB_FETCHMODE_ASSOC)) {
		$media = array ();
		$media["ID"] = $row["m_id"];
		$media["XREF"] = stripslashes($row["m_media"]);
		$media["GEDFILE"] = $gGedcom->mGEDCOMId;
		$media["FILE"] = stripslashes($row["m_file"]);
		$media["FORM"] = stripslashes($row["m_ext"]);
		$media["TITL"] = stripslashes($row["m_titl"]);
		$media["NOTE"] = stripslashes($row["m_gedrec"]);
		$medialist[] = $media;
	}
	return $medialist;

}

/**
 * Get the list of links to media from the database
 *
 * Searches the media table of the database for media items that
 * are associated with the currently active GEDCOM.
 *
 * The medialist that is returned contains the following elements:
 * - $mapping["ID"] Database id
 * - $mapping["INDI"] the gid of this media item is linked to (Ixxxx, Fxxx etc)
 * - $mapping["XREF"] the unique id of this media item in the table (Mxxxx)
 * - $mapping["ORDER"] the order the media item should be injected into the gedcom file
 * - $mapping["GEDFILE"] the gedcom file the media item should be added to
 * - $mapping["NOTE"] gedcom record snippet
 *
 * @return mixed A media list array.
 */
function get_db_mapping_list() {
	global $GEDCOM, $gGedcom, $TBLPREFIX;

	$mappinglist = array ();
	$sql="SELECT mm_id, mm_gid, mm_media, mm_order, mm_gedfile, mm_gedrec FROM {$TBLPREFIX}media_mapping WHERE mm_gedfile='{$gGedcom->mGEDCOMId}' ORDER BY mm_gid, mm_order";
	$res = $gBitSystem->mDb->query($sql);
	while($row =& $res->fetchRow()){
		$mapping=array ();
		$mapping["ID"] = $row["mm_id"];
		$mapping["INDI"] = stripslashes($row["mm_gid"]);
		$mapping["XREF"] = stripslashes($row["mm_media"]);
		$mapping["GEDFILE"] = $gGedcom->mGEDCOMId;
		$mapping["ORDER"] = stripslashes($row["mm_order"]);
		$mapping["NOTE"] = stripslashes($row["mm_gedrec"]);
		$mappinglist[] = $mapping;
	}
	$res->free();
	return $mappinglist;
}

/**
 * Get the list of links to media from the database for a person/family/source
 *
 * Searches the media table of the database for media items that
 * are associated with a person/family/source.
 *
 * The medialist that is returned contains the following elements:
 * - $mapping["ID"] Database id
 * - $mapping["INDI"] the gid of this media item is linked to (Ixxxx, Fxxx etc)
 * - $mapping["XREF"] the unique id of this media item in the table (Mxxxx)
 * - $mapping["ORDER"] the order the media item should be injected into the gedcom file
 * - $mapping["GEDFILE"] the gedcom file the media item should be added to
 * - $mapping["NOTE"] gedcom record snippet
 * - $mapping["CHECK"] boolean for calling routine use, false by default.
 *
 * @param string $indi The person/family/source item to find media links for
 * @return mixed A media list array.
 */
function get_db_indi_mapping_list($indi) {
	global $GEDCOM, $gGedcom, $TBLPREFIX, $DBCONN;

	$mappinglist = array ();
	$sql = "SELECT mm_id, mm_gid, mm_media, mm_order, mm_gedrec FROM " . $TBLPREFIX . "media_mapping WHERE mm_gedfile='{$gGedcom->mGEDCOMId}' AND mm_gid='".$DBCONN->escapeSimple($indi)."' ORDER BY mm_order";
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_gedfile=? AND mm_gid=? ORDER BY mm_order";
	$res = $gBitSystem->mDb->query( $sql, array( $gGedcom->mGEDCOMId, $indi ) );
	while ($row = & $res->fetchRow(DB_FETCHMODE_ASSOC)) {
		$mapping = array ();
		$mapping["ID"] = $row["mm_id"];
		$mapping["INDI"] = stripslashes($row["mm_gid"]);
		$mapping["XREF"] = stripslashes($row["mm_media"]);
		$mapping["ORDER"] = stripslashes($row["mm_order"]);
		$mapping["GEDFILE"] = $gGedcom->mGEDCOMId;
		$mapping["NOTE"] = stripslashes($row["mm_gedrec"]);
		$mapping["CHECK"] = false;
		$mappinglist[$row["mm_media"]] = $mapping;
	}
	return $mappinglist;
}

/**
 * Converts a block of text into a gedcom NOTE record.
 *
 * @param integer $level  The indent number for the NOTE record.
 * @param string $txt Block of text to convert.
 * @return gedrec Gedcom NOTE record.
*/
function textblock_to_note($level, $txt) {

	$newnote = breakConts("{$level} NOTE ". $txt);
	return $newnote;
}

/**
 * Removes /./  /../  from the middle of any given path
 * User function as the php variant will expand leading ./ to full path which is not
 * required and could be security issue.
 *
 * @param string $path Filepath to check.
 * @return string Cleaned up path.
 */
function real_path($path) {
	if ($path == "") {
		return false;
	}

	$path = trim(preg_replace("/\\\\/", "/", (string) $path));

	if (!preg_match("/(\.\w{1,4})$/", $path) && !preg_match("/\?[^\\/]+$/", $path) && !preg_match("/\\/$/", $path)) {
		$path .= '/';
	}

	$pattern = "/^(\\/|\w:\\/|https?:\\/\\/[^\\/]+\\/)?(.*)$/i";

	preg_match_all($pattern, $path, $matches, PREG_SET_ORDER);

	$path_tok_1 = $matches[0][1];
	$path_tok_2 = $matches[0][2];

	$path_tok_2 = preg_replace(array (
		"/^\\/+/",
		"/\\/+/"
	), array (
		"",
		"/"
	), $path_tok_2);

	$path_parts = explode("/", $path_tok_2);
	$real_path_parts = array ();

		for ($i = 0, $real_path_parts = array (); $i < count($path_parts); $i++) {
		if ($path_parts[$i] == '.') {
			continue;
		} else
			if ($path_parts[$i] == '..') {
				if ((isset ($real_path_parts[0]) && $real_path_parts[0] != '..') || ($path_tok_1 != "")) {
					array_pop($real_path_parts);
					continue;
				}
			}
		array_push($real_path_parts, $path_parts[$i]);
	}

	return $path_tok_1 . implode('/', $real_path_parts);
}

/**
 *
 * Construct the correct path for media files and thumbnails before moving them
 *
 * @param string $filename Filename including complete path
 * @param string $moveto Diretory where it should be moved to
 * @param boolean $thumb Specify whether it should be the thumbnail directory
 * @return string Cleaned up path.
 */
function set_media_path($filename, $moveto, $thumb = false) {
	$movefile = "";
	if ($moveto == "..") {
		$directories = preg_split("/\//", $filename);
		$ct = count($directories);
		foreach ($directories as $key => $value) {
			if ($key == 1 && $thumb == true)
				$movefile .= "thumbs/";
			if ($key != $ct -2)
				$movefile .= $value;
			if ($key != $ct -2 && $key != $ct -1)
				$movefile .= "/";
		}
	} else {
		$directories = preg_split("/\//", $filename);
		$ct = count($directories);
		foreach ($directories as $key => $value) {
			if ($key == 1 && $thumb == true)
				$movefile .= "thumbs/";
			if ($key == $ct -1)
				$movefile .= $moveto . "/";
			$movefile .= $value;
			if ($key != $ct -1)
				$movefile .= "/";
		}
	}
	return $movefile;
}

/**
 *
 * Sanity check for the media folder. We need to check if the media and the thumbs folder
 * exist. If they don't exist we will try to create them otherwise we can't continue.
 *
 * @return boolean Specify whether we succeeded to create the media and thumbnail folder
 */
function check_media_structure() {
	global $MEDIA_DIRECTORY, $pgv_lang;

	// Check if the media directory is not a .
	// If so, do not try to create it since it does exist
	if (substr($MEDIA_DIRECTORY, 0, 1) != ".") {
		// Check first if the $MEDIA_DIRECTORY exists
		if (!is_dir($MEDIA_DIRECTORY)) {
			if (!mkdir($MEDIA_DIRECTORY))
				return false;
			if (!file_exists($MEDIA_DIRECTORY . "index.php")) {
				$inddata = html_entity_decode("<?php\nheader(\"Location: ../medialist.php\");\nexit;\n?>");
				$fp = @ fopen($MEDIA_DIRECTORY . "index.php", "w+");
				if (!$fp)
					print "<div class=\"error\">" . $pgv_lang["security_no_create"] . $MEDIA_DIRECTORY . "thumbs</div>";
				else {
					// Write the index.php for the media folder
					fputs($fp, $inddata);
					fclose($fp);
				}
			}
		}
	}
	// Check if the thumbs folder exists
	if (!is_dir($MEDIA_DIRECTORY . "thumbs")) {
		print $MEDIA_DIRECTORY . "thumbs";
		if (!mkdir($MEDIA_DIRECTORY . "thumbs"))
			return false;
		if (file_exists($MEDIA_DIRECTORY . "index.php")) {
			$inddata = file_get_contents($MEDIA_DIRECTORY . "index.php");
			$inddatathumb = str_replace(": ../", ": ../../", $inddata);
			$fpthumb = @ fopen($MEDIA_DIRECTORY . "thumbs/index.php", "w+");
			if (!$fpthumb)
				print "<div class=\"error\">" . $pgv_lang["security_no_create"] . $MEDIA_DIRECTORY . "thumbs</div>";
			else {
				// Write the index.php for the thumbs media folder
				fputs($fpthumb, $inddatathumb);
				fclose($fpthumb);
			}
		}
	}
	return true;
}

/**
 * Get the list of media from the database
 *
 * Searches the media table of the database for media items that
 * are associated with the currently active GEDCOM.
 *
 * The medialist that is returned contains the following elements:
 * - $media["ID"] 		the unique id of this media item in the table (Mxxxx)
 * - $media["XREF"]		Another copy of the Media ID (not sure why there are two)
 * - $media["GEDFILE"] 	the gedcom file the media item should be added to
 * - $media["FILE"] 	the filename of the media item
 * - $media["EXISTS"] 	whether the file exists.  0=no, 1=external, 2=std dir, 3=protected dir
 * - $media["THUMB"]	the filename of the thumbnail
 * - $media["THUMBEXISTS"]	whether the thumbnail exists.  0=no, 1=external, 2=std dir, 3=protected dir
 * - $media["FORM"] 	the format of the item (ie bmp, gif, jpeg, pcx etc)
 * - $media["TYPE"]		the type of media item (ie certificate, document, photo, tombstone etc)
 * - $media["TITL"] 	a title for the item, used for list display
 * - $media["GEDCOM"] 	gedcom record snippet
 * - $media["LEVEL"]	level number (normally zero)
 * - $media["LINKED"] 	Flag for front end to indicate this is linked
 * - $media["LINKS"] 	Array of gedcom ids that this is linked to
 * - $media["CHANGE"]	Indicates the type of change waiting admin approval
 *
 * @param boolean $random If $random is true then the function will return 5 random pictures.
 * @return mixed A media list array.
 */

function get_medialist($currentdir = false, $directory = "", $linkonly = false, $random = false, $includeExternal = true) {
	global $MEDIA_DIRECTORY_LEVELS, $BADMEDIA, $thumbdir, $TBLPREFIX, $MEDIATYPE, $DBCONN;
	global $level, $dirs, $ALLOW_CHANGE_GEDCOM, $GEDCOM, $gGedcom, $MEDIA_DIRECTORY;
	global $MEDIA_EXTERNAL, $medialist, $pgv_changes, $DBTYPE, $USE_MEDIA_FIREWALL;

	// Retrieve the gedcoms to search in
	$sgeds = array ();
	$gedcoms = $gGedcom->getList();
	if (($ALLOW_CHANGE_GEDCOM) && (count($gGedcom) > 1)) {
		foreach ($gGedcom as $key => $ged) {
			$str = preg_replace(array (
				"/\./",
				"/-/",
				"/ /"
			), array (
				"_",
				"_",
				"_"
			), $key);
			if (isset ($$str))
				$sgeds[] = $key;
		}
	} else
		$sgeds[] = $GEDCOM;

	// Create the medialist array of media in the DB and on disk
	// NOTE: Get the media in the DB
	$medialist = array ();
	if (empty ($directory))
		$directory = $MEDIA_DIRECTORY;
	$myDir = str_replace($MEDIA_DIRECTORY, "", $directory);
	$sql = "SELECT m_id, m_file, m_media, m_gedrec, m_titl FROM {$TBLPREFIX}media WHERE m_gedfile={$gGedcom->mGEDCOMId}";
	if ($random == true) {
		$sql .= " ORDER BY ".DB_RANDOM."()";
		$res = & dbquery($sql, true, 5);
	} else {
		$sql .= " AND (m_file LIKE '%" . $DBCONN->escapeSimple($myDir) . "%' OR m_file LIKE '%://%') ORDER BY m_id desc";
		$res = & dbquery($sql);
	}
	$mediaObjects = array ();

	if (!DB::isError($res)) {
		$ct = $res->numRows();
	// Build the raw medialist array,
	// but weed out any folders we're not interested in
	while ($row = & $res->fetchRow(DB_FETCHMODE_ASSOC)) {
		if ($row) {
			if (!empty ($row["m_file"])) {
				$fileName = check_media_depth(stripslashes($row["m_file"]), "NOTRUNC", "QUIET");
				$isExternal = isFileExternal($fileName);
				if ( $isExternal && (!$MEDIA_EXTERNAL || !$includeExternal) ) {
					continue;
				}
				if ($isExternal || !$currentdir || $directory == dirname($fileName) . "/") {
					$media = array ();
					$media["ID"] = $row["m_id"];
					$media["XREF"] = stripslashes($row["m_media"]);
					$media["GEDFILE"] = $gGedcom->mGEDCOMId;
					$media["FILE"] = $fileName;
					if ($isExternal) {
						$media["THUMB"] = $fileName;
						$media["THUMBEXISTS"] = 1; // 1 means external
						$media["EXISTS"] = 1; // 1 means external
					} else {
						$media["THUMB"] = thumbnail_file($fileName);
						$media["THUMBEXISTS"] = media_exists($media["THUMB"]);
						$media["EXISTS"] = media_exists($fileName);
					}
					$media["TITL"] = stripslashes($row["m_titl"]);
					$media["GEDCOM"] = stripslashes($row["m_gedrec"]);
					$gedrec = & trim($row["m_gedrec"]);
					$media["LEVEL"] = $gedrec{0};
					$media["LINKED"] = false;
					$media["LINKS"] = array ();
					$media["CHANGE"] = "";
					// Extract Format and Type from GEDCOM record
					$media["FORM"] = strtolower(get_gedcom_value("FORM", 2, $gedrec));
					$media["TYPE"] = strtolower(get_gedcom_value("FORM:TYPE", 2, $gedrec));

					// Build a sortable key for the medialist
					$firstChar = substr($media["XREF"], 0, 1);
					$restChar = substr($media["XREF"], 1);
					if (is_numeric($firstChar)) {
						$firstChar = "";
						$restChar = $media["XREF"];
					}
					$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $media["GEDFILE"];
					$medialist[$keyMediaList] = $media;
					$mediaObjects[] = $media["XREF"];
				}
			}
		}
	}
	$res->free();
	}

	// Look for new Media objects in the list of changes pending approval
	// At the same time, accumulate a list of GEDCOM IDs that have changes pending approval
	$changedRecords = array ();
	foreach ($pgv_changes as $changes) {
		foreach ($changes as $change) {
			while (true) {
				if ($change["gedcom"] != $GEDCOM || $change["status"] != "submitted")
					break;

				$gedrec = $change['undo'];
				if (empty ($gedrec))
					break;

				$ct = preg_match("/0 @.*@ (\w*)/", $gedrec, $match);
				$type = trim($match[1]);
				if ($type != "OBJE") {
					$changedRecords[] = $change["gid"];
					break;
				}

				// Build a sortable key for the medialist
				$firstChar = substr($change["gid"], 0, 1);
				$restChar = substr($change["gid"], 1);
				if (is_numeric($firstChar)) {
					$firstChar = "";
					$restChar = $change["gid"];
				}
				$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $gGedcom[$GEDCOM]["id"];
				if (isset ($medialist[$keyMediaList])) {
					$medialist[$keyMediaList]["CHANGE"] = $change["type"];
					break;
				}

				// Build the entry for this new Media object
				$media = array ();
				$media["ID"] = $change["gid"];
				$media["XREF"] = $change["gid"];
				$media["GEDFILE"] = $gGedcom[$GEDCOM]["id"];
				$media["FILE"] = "";
				$media["THUMB"] = "";
				$media["THUMBEXISTS"] = false;
				$media["EXISTS"] = false;
				$media["FORM"] = "";
				$media["TYPE"] = "";
				$media["TITL"] = "";
				$media["GEDCOM"] = $gedrec;
				$media["LEVEL"] = "0";
				$media["LINKED"] = false;
				$media["LINKS"] = array ();
				$media["CHANGE"] = "append";

				// Now fill in the blanks
				$subrecs = get_all_subrecords($gedrec, "_PRIM,_THUM,CHAN");
				foreach ($subrecs as $subrec) {
					$pieces = explode("\r\n", $subrec);
					foreach ($pieces as $piece) {
						$ft = preg_match("/(\d) (\w+)(.*)/", $piece, $match);
						if ($ft > 0) {
							$subLevel = $match[1];
							$fact = trim($match[2]);
							$event = trim($match[3]);
							$event .= get_cont(($subLevel +1), $subrec, false);

							if ($fact == "FILE")
								$media["FILE"] = str_replace(array("\r", "\n"), "", $event);
							if ($fact == "FORM")
								$media["FORM"] =  str_replace(array("\r", "\n"), "", $event);
							if ($fact == "TITL")
								$media["TITL"] = $event;
						}
					}
				}

				// And a few more blanks
				if (empty ($media["FILE"]))
					break;
				$fileName = check_media_depth(stripslashes($media["FILE"]), "NOTRUNC", "QUIET");
				if ($MEDIA_EXTERNAL && isFileExternal($media["FILE"])) {
					$media["THUMB"] = $fileName;
					$media["THUMBEXISTS"] = 1;  // 1 means external
					$media["EXISTS"] = 1;  // 1 means external
				} else {
					// if currentdir is true, then we are only looking for files in $directory, no subdirs
					if ($currentdir && $directory != dirname($fileName) . "/")
						break;
					// if currentdir is false, then we are looking for all files recursively below $directory.  ignore anything outside of $directory
					if (!$currentdir && strpos(dirname($fileName),$directory . "/") === false )
						break;
					$media["THUMB"] = thumbnail_file($fileName);
					$media["THUMBEXISTS"] = media_exists($media["THUMB"]);
					$media["EXISTS"] = media_exists($fileName);
				}

				// Now save this for future use
				//print $keyMediaList.": "; print_r($media); print "<br/><br/>";
				$medialist[$keyMediaList] = $media;
				$mediaObjects[] = $media["XREF"];

				break;
			}
		}
	}

	$ct = count($medialist);

	// Search the database for the applicable cross-references
	// and fill in the Links part of the medialist
	if ($ct > 0) {
		$sql = "SELECT mm_gid, mm_media FROM {$TBLPREFIX}media_mapping WHERE mm_gedfile='{$gGedcom->mGEDCOMId}' AND mm_media IN (";
		$i = 0;
		foreach ($medialist as $key => $media) {
			$i++;
			$sql .= "'" . $media["XREF"] . "'";
			if ($i < $ct)
				$sql .= ", ";
			else
				$sql .= ")";
		}
		$sql .= " ORDER BY mm_gid";
		$res = dbquery($sql);
		$peopleIds = array();
		while ($row = & $res->fetchRow(DB_FETCHMODE_ASSOC)) {
			// Build the key for the medialist
			$temp = stripslashes($row["mm_media"]);
			$firstChar = substr($temp, 0, 1);
			$restChar = substr($temp, 1);
			if (is_numeric($firstChar)) {
				$firstChar = "";
				$restChar = $temp;
			}
			$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $gGedcom->mGEDCOMId;

			// Update the medialist with this cross-reference,
			// but only if the Media item actually exists (could be a phantom reference)
			if (isset ($medialist[$keyMediaList])) {
				$medialist[$keyMediaList]["LINKS"][stripslashes($row["mm_gid"])] = id_type(stripslashes($row["mm_gid"]));
				$medialist[$keyMediaList]["LINKED"] = true;
			}

			//-- store all of the ids in an array so that we can load up all of the people at once
			$peopleIds[] = stripslashes($row["mm_gid"]);
		}
		$res->free();

		//-- load up all of the related people into the cache
		load_people($peopleIds);
		load_families($peopleIds);
	}

	// Search the list of GEDCOM changes pending approval.  There may be some new
	// links to new or old media items that haven't been approved yet.
	// Logic:
	//   Make sure the array $changedRecords contains unique entries.  Ditto for array
	//   $mediaObjects.
	//   Read each of the entries in array $changedRecords.  Get the matching record from
	//   the GEDCOM file.  Search the GEDCOM record for each of the entries in array
	//   $mediaObjects.  A hit means that the GEDCOM record contains a link to the
	//   media object.  If we don't already know about the link, add it to that media
	//   object's link table.
	$mediaObjects = array_unique($mediaObjects);
	$changedRecords = array_unique($changedRecords);
	foreach ($changedRecords as $pid) {
		$gedrec = find_updated_record($pid);
		if ($gedrec) {
			foreach ($mediaObjects as $mediaId) {
				if (strpos($gedrec, "@" . $mediaId . "@")) {
					// Build the key for the medialist
					$firstChar = substr($mediaId, 0, 1);
					$restChar = substr($mediaId, 1);
					if (is_numeric($firstChar)) {
						$firstChar = "";
						$restChar = $mediaId;
					}
					$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $gGedcom[$GEDCOM]["id"];

					// Add this GEDCOM ID to the link list of the media object
					if (isset ($medialist[$keyMediaList])) {
						$medialist[$keyMediaList]["LINKS"][$pid] = id_type($pid);
						$medialist[$keyMediaList]["LINKED"] = true;
					}
				}
			}
		}
	}

	uasort($medialist, "mediasort");

	//-- for the media list do not look in the directory
	if ($linkonly)
		return $medialist;

	// The database part of the medialist is now complete.
	// We still have to get a list of all media items that exist as files but
	// have not yet been entered into the database.  We'll do this only for the
	// current folder.
	//
	// At the same time, we'll build a list of all the sub-folders in this folder.
	$temp = str_replace($MEDIA_DIRECTORY, "", $directory);
	if ($temp == "")
		$folderDepth = 0;
	else
		$folderDepth = count(explode("/", $temp)) - 1;
	$dirs = array ();
	$images = array ();

	$dirs_to_check = array ();
	if (is_dir(filename_decode($directory))) {
		array_push($dirs_to_check, $directory);
	}
	if ($USE_MEDIA_FIREWALL && is_dir(filename_decode(get_media_firewall_path($directory)))) {
		array_push($dirs_to_check, get_media_firewall_path($directory));
	}

	foreach ($dirs_to_check as $thedir) {
		$d = dir(filename_decode(substr($thedir, 0, -1)));
		while (false !== ($fileName = $d->read())) {
			$fileName = filename_encode($fileName);
			while (true) {
				// Make sure we only look at valid media files
				if (in_array($fileName, $BADMEDIA))
					break;
				if (is_dir(filename_decode($thedir . $fileName))) {
					if ($folderDepth < $MEDIA_DIRECTORY_LEVELS)
						$dirs[] = $fileName; // note: we will remove duplicates when the loop is complete
					break;
				}
				$exts = explode(".", $fileName);
				if (count($exts) == 1)
					break;
				$ext = strtolower($exts[count($exts) - 1]);
				if (!in_array($ext, $MEDIATYPE))
					break;
	
				// This is a valid media file:
				// now see whether we already know about it
				$mediafile = $directory . $fileName;
				$exist = false;
				$oldObject = false;
				foreach ($medialist as $key => $item) {
					if ($item["FILE"] == $directory . $fileName) {
						if ($item["CHANGE"] == "delete") {
							$exist = false;
							$oldObject = true;
						} else {
							$exist = true;
							$oldObject = false;
						}
					}
				}
				if ($exist)
					break;
	
				// This media item is not yet in the database
				$media = array ();
				$media["ID"] = "";
				$media["XREF"] = "";
				$media["GEDFILE"] = "";
				$media["FILE"] = $directory . $fileName;
				$media["THUMB"] = thumbnail_file($directory . $fileName, false);
				$media["THUMBEXISTS"] = media_exists($media["THUMB"]);
				$media["EXISTS"] = media_exists($media["FILE"]);
				$media["FORM"] = $ext;
				if ($ext == "jpg" || $ext == "jp2")
					$media["FORM"] = "jpeg";
				if ($ext == "tif")
					$media["FORM"] = "tiff";
				$media["TYPE"] = "";
				$media["TITL"] = "";
				$media["GEDCOM"] = "";
				$media["LEVEL"] = "0";
				$media["LINKED"] = false;
				$media["LINKS"] = array ();
				$media["CHANGE"] = "";
				if ($oldObject)
					$media["CHANGE"] = "append";
				$images[$fileName] = $media;
				break;
			}
		}
		$d->close();
	}
	//print_r($images); print "<br />";
	$dirs = array_unique($dirs); // remove duplicates that were added because we checked both the regular dir and the media firewall dir
	sort($dirs);
	//print_r($dirs); print "<br />";
	if (count($images) > 0) {
		ksort($images);
		$medialist = array_merge($images, $medialist);
	}
	//print_r($medialist); print "<br />";
	return $medialist;
}
/**
 * Determine whether the current Media item matches the filter criteria
 *
 * @param	array	$media		An item from the Media list produced by get_medialist()
 * @param	string	$filter		The filter to be looked for within various elements of
 *								the $media array
 * @param	string	$acceptExt	"http" if links to external media should be considered too
 * @return	bool				false if the Media item doesn't match the filter criteria
 */
function filterMedia($media, $filter, $acceptExt) {

	if (empty ($filter) || strlen($filter) < 2)
		$filter = "";
	if (empty ($acceptExt) || $acceptExt != "http")
		$acceptExt = "";

	$isEditor = PGV_USER_CAN_EDIT;

	while (true) {
		$isValid = true;

		//-- Check Privacy first.  No point in proceeding if Privacy says "don't show"
		$links = $media["LINKS"];
		if (count($links) != 0) {
			foreach ($links as $id => $type) {
				if (!displayDetailsByID($id, $type)) {
					$isValid = false;
					break;
				}
			}
		}

		//-- Accept external Media only if specifically told to do so
		if (isFileExternal($media["FILE"]) && $acceptExt != "http")
			$isValid = false;

		if (!$isValid)
			break;

		//-- Accept everything if filter string is empty
		if ($filter == "")
			break;

		//-- Accept when filter string contained in file name (but only for editing users)
		if ($isEditor && stristr(basename($media["FILE"]), $filter))
			break;

		//-- Accept when filter string contained in Media item's title
		if (stristr($media["TITL"], $filter))
			break;

		//-- Accept when filter string contained in name of any item
		//-- this Media item is linked to.  (Privacy already checked)
		$isValid = false;
		if (count($links) != 0)
			break;
		foreach ($links as $id => $type) {
			if ($type == "INDI" && stristr(get_person_name($id), $filter))
				$isValid = true;
			if ($type == "FAM" && stristr(get_family_descriptor($id), $filter))
				$isValid = true;
			if ($type == "SOUR" && stristr(get_source_descriptor($id), $filter))
				$isValid = true;
		}
		break;
	}

	return $isValid;
}
//-- search through the gedcom records for individuals
/**
 * Search the database for individuals that match the query
 *
 * uses a regular expression to search the gedcom records of all individuals and returns an
 * array list of the matching individuals
 *
 * @author roland-d
 * @param	string $query a regular expression query to search for
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myindilist array with all individuals that matched the query
 */
function search_media_pids($query, $allgeds = false, $ANDOR = "AND") {
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $gGedcom;
	$myindilist = array ();
	if ($REGEXP_DB)
		$term = "REGEXP";
	else
		$term = "LIKE";
	if (!is_array($query)) {
		$sql = "SELECT m_media as m_media FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE (m_gedrec $term ? OR m_gedrec $term ? OR m_gedrec $term ?)";
		$args[] = strtoupper($query);
		$args[] = str2upper($query);
		$args[] = str2lower($query);
	} else {
		$sql = "SELECT m_media FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(m_gedrec $term ? OR m_gedrec $term ?)";
			$args[] = str2upper($query);
			$args[] = str2lower($query);
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) {
		$sql .= " AND m_gedfile=?";
		$args[] = $gGedcom->mGEDCOMId;
	}
	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "m_gedfile=?";
			$args[] = $allgeds[$i];
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	$res =& $gBitSystem->mDb->query( $sql );
	if ($res) {
		while($row =& $res->fetchRow()){
			$sqlmm = "SELECT mm_gid FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping where mm_media = ? and mm_gedfile = ?";
			$resmm =& $gBitSystem->mDb->query( $sqlmm, array( $row['m_media'], $gGedcom->mGEDCOMId ) );
			while ($rowmm = & $resmm->fetchRow()) {
				$myindilist[$rowmm[0]] = id_type($rowmm[0]);
			}
		}
		$res->free();
	}
	return $myindilist;
}

/**
 * Generates the thumbnail filename and path
 *
 * The full file path is taken and turned into the location of the thumbnail file.
 *
 * @author	roland-d
 * @param	string 	$filename 		The full filename of the media item
 * @param	bool	$generateThumb	'true' when thumbnail should be generated,
 *									'false' when only the file name should be returned
 * @param	bool	$overwrite		'true' to replace existing thumbnail
 * @return 	string the location of the thumbnail
 */
function thumbnail_file($filename, $generateThumb = true, $overwrite = false) {
	global $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $AUTO_GENERATE_THUMBS, $MEDIA_DIRECTORY_LEVELS;
	global $MEDIA_EXTERNAL;

	if (strlen($filename) == 0)
		return false;
	if (!isset ($generateThumb))
		$generateThumb = true;
	if (!isset ($overwrite))
		$overwrite = false;

	// NOTE: Lets get the file details
	if (isFileExternal($filename))
		return $filename;

	$filename = check_media_depth($filename, "NOTRUNC");

	$parts = pathinfo($filename);
	$mainDir = $parts["dirname"] . "/";
	$thumbDir = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY . "thumbs/", $mainDir);
	$thumbName = $parts["basename"];
	if (isset ($parts["extension"]))
		$thumbExt = strtolower($parts["extension"]);
	else
		$thumbExt = "";

	if (!empty($pid)) {
		$media_id_number = get_media_id_from_file($filename);
		// run the clip method foreach person associated with the picture
		$thumbnail = picture_clip($pid, $media_id_number, $filename, $thumbDir);
		if (!empty($thumbnail)) return $thumbnail;
	}

	if (!$generateThumb)
		return $thumbDir . $thumbName;

	if (!$overwrite && media_exists($thumbDir . $thumbName))
		return $thumbDir . $thumbName;

	if ($AUTO_GENERATE_THUMBS && $generateThumb) {
		if (generate_thumbnail($mainDir . $thumbName, $thumbDir . $thumbName)) {
			return $thumbDir . $thumbName;
		}
	}

	// Thumbnail doesn't exist and could not be generated:
	//		Return an icon image instead
	switch ($thumbExt) {
		case "pdf" :
			$which = "pdf";
			break;
		case "doc" :
		case "txt" :
			$which = "doc";
			break;
		case "ged" :
			$which = "ged";
			break;
		default :
			$which = "large";
	}
	return $PGV_IMAGE_DIR . "/" . $PGV_IMAGES["media"][$which];
}

/**
 * Validate the media depth
 *
 * When the user has a media depth greater than 0, all media needs to be
 * checked against this to ensure the proper path is in place. This function
 * takes a filename, split it in parts and then recreates it according to the
 * chosen media depth
 *
 * When the input file name is a URL, this routine does nothing.  Only http:// URLs
 * are supported.
 *
 * @author	roland-d
 * @param	string	$filename	The filename that needs to be checked for media depth
 * @param	string	$truncate	Controls which part of folder structure to truncate when
 *								number of folders exceeds $MEDIA_DIRECTORY_LEVELS
 *									"NOTRUNC":	Don't truncate
 *									"BACK":		Truncate at end, keeping front part
 *									"FRONT":	Truncate at front, keeping back part
 * @param	string	$noise		Controls the amount of chatting done by this function
 *									"VERBOSE"	Print messages
 *									"QUIET"		Don't print messages
 * @return 	string	A filename validated for the media depth
 *
 * NOTE: 	The "NOTRUNC" option is required so that media that were inserted into the
 *			database before $MEDIA_DIRECTORY_LEVELS was reduced will display properly.
 * NOTE:	The "QUIET" option is used during GEDCOM import, where we really don't need
 *			to know about every Media folder that's being created.
 */
function check_media_depth($filename, $truncate = "FRONT", $noise = "VERBOSE") {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $MEDIA_EXTERNAL;
	global $pgv_lang;

	if (empty ($filename) || ($MEDIA_EXTERNAL && isFileExternal($filename)))
		return $filename;

	if (empty ($truncate) || ($truncate != "NOTRUNC" && $truncate != "BACK" && $truncate != "FRONT"))
		$truncate = "FRONT";
	if ($truncate == "NOTRUNC")
		$truncate = "FRONT"; // **** temporary over-ride *****

	if (strpos($_SERVER["SCRIPT_NAME"],"mediafirewall") > -1) {
		// no extraneous output while displaying images
		$noise = "QUIET";
	}

	if (empty ($noise) || ($noise != "VERBOSE" && $noise != "QUIET"))
		$noise = "VERBOSE";

	// NOTE: Check media depth
	$parts = pathinfo($filename);
	//print_r($parts); print "<br />";
	if (empty ($parts["dirname"]) || ($MEDIA_DIRECTORY_LEVELS == 0 && $truncate != "NOTRUNC"))
		return $MEDIA_DIRECTORY . $parts["basename"];

	$fileName = $parts["basename"];

	if (empty ($parts["dirname"]))
		$folderName = $MEDIA_DIRECTORY;
	else
		$folderName = $parts["dirname"] . "/";

	$folderName = trim($folderName);
	$folderName = str_replace(array (
		"\\",
		"//"
	), "/", $folderName);
	if (substr($folderName, 0, 1) == "/")
		$folderName = substr($folderName, 1);
	if (substr($folderName, 0, 2) == "./")
		$folderName = substr($folderName, 2);
	if (substr($folderName, 0, strlen($MEDIA_DIRECTORY)) == $MEDIA_DIRECTORY)
		$folderName = substr($folderName, strlen($MEDIA_DIRECTORY));
	$folderName = str_replace("../", "", $folderName);
	if (substr($folderName, 0, 7) == "thumbs/")
		$folderName = substr($folderName, 7);
	if (substr($folderName, 0, 4) == "CVS/")
		$folderName = substr($folderName, 4);

	if ($folderName == "")
		return $MEDIA_DIRECTORY . $fileName;
	$folderList = explode("/", $folderName);
	$folderCount = count($folderList) - 1;
	$folderDepth = min($folderCount, $MEDIA_DIRECTORY_LEVELS);
	if ($truncate == "NOTRUNC")
		$folderDepth = $folderCount;

	if ($truncate == "BACK") {
		$nStart = 0;
		$nEnd = min($folderCount, $folderDepth);
	} else {
		$nStart = max(0, ($folderCount - $folderDepth));
		$nEnd = $folderCount;
	}

	// Check for, and skip, device name used as the first folder name
	if (substr($folderList[$nStart], -1) == ":") {
		$nStart++;
		if ($nStart > $nEnd)
			return $MEDIA_DIRECTORY . $fileName;
	}
	// Now check for, and skip, "./" at the beginning of the folder list
	if ($folderList[$nStart] == ".") {
		$nStart++;
		if ($nStart > $nEnd)
			return $MEDIA_DIRECTORY . $fileName;
	}

	$folderName = "";
	$backPointer = "../../";
	// Check existing folder structure, and create as necessary
	$n = $nStart;
	while ($n < $nEnd) {
		$folderName .= $folderList[$n];
		if (!is_dir(filename_decode($MEDIA_DIRECTORY . $folderName))) {
			if (!mkdir(filename_decode($MEDIA_DIRECTORY . $folderName))) {
				if ($noise == "VERBOSE") {
					print "<div class=\"error\">" . $pgv_lang["folder_no_create"] . $MEDIA_DIRECTORY . $folderName . "</div>";
				}
			} else {
				if ($noise == "VERBOSE") {
					print $pgv_lang["folder_created"] . ": " . $MEDIA_DIRECTORY . $folderName . "/<br />";
				}
				$fp = @ fopen(filename_decode($MEDIA_DIRECTORY . $folderName . "/index.php"), "w+");
				if (!$fp) {
					if ($noise == "VERBOSE") {
						print "<div class=\"error\">" . $pgv_lang["security_no_create"] . $MEDIA_DIRECTORY . $folderName . "</div>";
					}
				} else {
					fwrite($fp, "<?php\r\n");
					fwrite($fp, "header(\"Location: " . $backPointer . "medialist.php\");\r\n");
					fwrite($fp, "exit;\r\n");
					fwrite($fp, "?>\r\n");
					fclose($fp);
				}
			}
		}
		if (!is_dir(filename_decode($MEDIA_DIRECTORY . "thumbs/" . $folderName))) {
			if (!mkdir(filename_decode($MEDIA_DIRECTORY . "thumbs/" . $folderName))) {
				if ($noise == "VERBOSE") {
					print "<div class=\"error\">" . $pgv_lang["folder_no_create"] . $MEDIA_DIRECTORY . "thumbs/" . $folderName . "</div>";
				}
			} else {
				if ($noise == "VERBOSE") {
					print $pgv_lang["folder_created"] . ": " . $MEDIA_DIRECTORY . "thumbs/" . $folderName . "/<br />";
				}
				$fp = @ fopen(filename_decode($MEDIA_DIRECTORY . "thumbs/" . $folderName . "/index.php"), "w+");
				if (!$fp) {
					if ($noise == "VERBOSE") {
						print "<div class=\"error\">" . $pgv_lang["security_no_create"] . $MEDIA_DIRECTORY . "thumbs/" . $folderName . "</div>";
					}
				} else {
					fwrite($fp, "<?php\r\n");
					fwrite($fp, "header(\"Location: " . $backPointer . "../medialist.php\");\r\n");
					fwrite($fp, "exit;\r\n");
					fwrite($fp, "?>\r\n");
					fclose($fp);
				}
			}
		}
		$folderName .= "/";
		$backPointer .= "../";
		$n++;
	}

	return $MEDIA_DIRECTORY . $folderName . $fileName;
}

function retrieve_media_object($gedrec, $gid) {
	$gedreclines = preg_split("/[\r\n]+/", $gedrec); // -- find the number of lines in the individuals record
	$linecount = count($gedreclines);
	$factrec = ""; // -- complete fact record
	$line = ""; // -- temporary line buffer
	$itemcounter = 0; // -- item counter per pid
	$objectline = 0; // -- linenumber where the object record starts
	$itemsfound = array (); // -- arryay for storing the found items
	// NOTE: Get the media record
	for ($linecounter = 1; $linecounter <= $linecount; $linecounter++) {
		if ($linecounter < $linecount)
			$line = $gedreclines[$linecounter];
		else
			$line = " ";
		if (empty ($line))
			$line = " ";
		if (preg_match("/[0-9]\sOBJE/", $line) > 0)
			$objectline = $linecounter;
		// Level 1 media
		if (($linecounter == $linecount) || ($line {
			0 }
		== 1)) {
			$ft = preg_match_all("/[1|2]\s(\w+)(.*)/", $factrec, $match, PREG_SET_ORDER);
			if ($ft > 0) {
				foreach ($match as $key => $hit) {
					if (!stristr($hit[0], "@") && stristr($hit[1], "OBJE")) {
						$key = $hit[0] { 0 };
						$fact = get_sub_record($key, "OBJE", $factrec);
						$itemsfound[$gid][$itemcounter] = array (
							($objectline
						), $key, $fact);
						$itemcounter++;
					}
				}
			}
			$factrec = $line;
		} else
			$factrec .= "\n" . $line;
	}
	return $itemsfound;
}

/**
 * get the list of current folders in the media directory
 * @return array
 */
function get_media_folders() {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS;

	$folderList = array ();
	$folderList[0] = $MEDIA_DIRECTORY;
	if ($MEDIA_DIRECTORY_LEVELS == 0)
		return $folderList;

	$currentFolderNum = 0;
	$nextFolderNum = 1;
	while ($currentFolderNum < count($folderList)) {
		$currentFolder = $folderList[$currentFolderNum];
		$currentFolderNum++;
		// get the folder depth
		$folders = explode($currentFolder, "/");
		$currentDepth = count($folders) - 2;
		// If we're not at the limit, look for more sub-folders within the current folder
		if ($currentDepth <= $MEDIA_DIRECTORY_LEVELS) {
			$dir = dir($currentFolder);
			while (true) {
				$entry = $dir->read();
				if (!$entry)
					break;
				if (is_dir($currentFolder . $entry . "/")) {
					// Weed out some folders we're not interested in
					if ($entry != "." && $entry != ".." && $entry != "CVS" && $entry != ".svn") {
						if ($currentFolder . $entry . "/" != $MEDIA_DIRECTORY . "thumbs/") {
							$folderList[$nextFolderNum] = $currentFolder . $entry . "/";
							$nextFolderNum++;
						}
					}
				}
			}
			$dir->close();
		}
	}
	sort($folderList);
	return $folderList;
}

/**
 * print a form for editing or adding media items
 * @param string $pid		the id of the media item to edit
 * @param string $action	the action to take after the form is posted
 * @param string $filename	allows you to provide a filename to go in the FILE tag for new media items
 * @param string $linktoid	the id of the person/family/source to link a new media item to
 * @param int    $level		The level at which this media item should be added
 * @param int    $line		The line number in the GEDCOM record where this media item belongs
 */
function show_media_form($pid, $action = "newentry", $filename = "", $linktoid = "", $level = 1, $line = 0) {
	global $GEDCOM, $pgv_lang, $TEXT_DIRECTION, $gGedcom, $WORD_WRAPPED_NOTES;
	global $pgv_changes, $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;
	global $AUTO_GENERATE_THUMBS, $THUMBNAIL_WIDTH;

	// NOTE: add a table and form to easily add new values to the table
	print "<form method=\"post\" name=\"newmedia\" action=\"addmedia.php\" enctype=\"multipart/form-data\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$action\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	if (!empty ($linktoid))
		print "<input type=\"hidden\" name=\"linktoid\" value=\"$linktoid\" />\n";
	print "<input type=\"hidden\" name=\"level\" value=\"$level\" />\n";
	print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
	print "<tr><td class=\"topbottombar\" colspan=\"2\">";
	if ($action == "newentry")
		print $pgv_lang["add_media"];
	else
		print $pgv_lang["edit_media"];
	print "</td></tr>";
	print "<tr><td colspan=\"2\" class=\"descriptionbox\"><input type=\"submit\" value=\"" . $pgv_lang["save"] . "\" /></td></tr>";
	if ($linktoid == "new" || ($linktoid == "" && $action != "update")) {
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("add_media_linkid", "qm");
		print $pgv_lang["add_fav_enter_id"] . "</td>";
		print "<td class=\"optionbox wrap\"><input type=\"text\" name=\"gid\" id=\"gid\" size=\"6\" value=\"\" />";
		print_findindi_link("gid", "");
		print_findfamily_link("gid");
		print_findsource_link("gid");
		print "<br /><sub>" . $pgv_lang["add_linkid_advice"] . "</sub></td></tr>\n";
	}
	if (isset ($pgv_changes[$pid . "_" . $GEDCOM]))
		$gedrec = find_updated_record($pid);
	else
		if (id_type($pid) == "OBJE")
			$gedrec = find_media_record($pid);
		else
			$gedrec = "";

	// 0 OBJE
	// 1 FILE
	if ($gedrec == "") {
		$gedfile = "FILE";
		if ($filename != "")
			$gedfile = "FILE " . $filename;
	} else {
		//		$gedfile = get_sub_record(1, "FILE", $gedrec);
		$gedfile = get_first_tag(1, "FILE", $gedrec);
		if (empty ($gedfile))
			$gedfile = "FILE";
	}
	if ($gedfile != "FILE") {
		$gedfile = "FILE " . check_media_depth(substr($gedfile, 5));
		$readOnly = "READONLY";
	} else {
		$readOnly = "";
	}
	if ($gedfile == "FILE") {
		// Box for user to choose to upload file from local computer
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("upload_media_file_help", "qm");
		print $pgv_lang["media_file"] . "</td><td class=\"optionbox wrap\"><input type=\"file\" name=\"mediafile\"";
		print " onchange=\"updateFormat(this.value);\"";
		print " size=\"40\"><br /><sub>" . $pgv_lang["use_browse_advice"] . "</sub></td></tr>";
		// Check for thumbnail generation support
		if (PGV_USER_GEDCOM_ADMIN) {
		$ThumbSupport = "";
		if (function_exists("imagecreatefromjpeg") and function_exists("imagejpeg"))
			$ThumbSupport .= ", JPG";
		if (function_exists("imagecreatefromgif") and function_exists("imagegif"))
			$ThumbSupport .= ", GIF";
		if (function_exists("imagecreatefrompng") and function_exists("imagepng"))
			$ThumbSupport .= ", PNG";
		if (!$AUTO_GENERATE_THUMBS)
			$ThumbSupport = "";

		if ($ThumbSupport != "") {
			$ThumbSupport = substr($ThumbSupport, 2); // Trim off first ", "
			print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
			print_help_link("generate_thumb_help", "qm", "generate_thumbnail");
			print $pgv_lang["auto_thumbnail"];
			print "</td><td class=\"optionbox wrap\">";
			print "<input type=\"checkbox\" name=\"genthumb\" value=\"yes\" checked />";
			print "&nbsp;&nbsp;&nbsp;" . $pgv_lang["generate_thumbnail"] . $ThumbSupport;
			print "</td></tr>";
		}
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("upload_thumbnail_file_help", "qm");
		print $pgv_lang["thumbnail"] . "</td><td class=\"optionbox wrap\"><input type=\"file\" name=\"thumbnail\" size=\"40\"><br /><sub>" . $pgv_lang["use_browse_advice"] . "</sub></td></tr>";
	}
		else print "<input type=\"hidden\" name=\"genthumb\" value=\"yes\" />";
	}
	// File name on server
	$isExternal = isFileExternal($gedfile);
	if ($gedfile == "FILE") {
		if (PGV_USER_GEDCOM_ADMIN) {
		add_simple_tag("1 $gedfile", "", $pgv_lang["server_file"], "", "NOCLOSE");
		print "<br /><sub>" . $pgv_lang["server_file_advice"];
		print "<br />" . $pgv_lang["server_file_advice2"] . "</sub></td></tr>";
		}
		$fileName = "";
		$folder = "";
	} else {
		if ($isExternal) {
			$fileName = substr($gedfile, 5);
			$folder = "";
		} else {
			$parts = pathinfo(substr($gedfile, 5));
			$fileName = $parts["basename"];
			$folder = $parts["dirname"] . "/";
		}

		print "\n<tr>";
		print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">\n";
		print "<input name=\"oldFilename\" type=\"hidden\" value=\"" . addslashes($fileName) . "\" />";
		print_help_link("upload_server_file_help", "qm", "upload_media");
		print $pgv_lang["server_file"];
		print "</td>\n";
		print "<td class=\"optionbox wrap $TEXT_DIRECTION wrap\">";
		if (PGV_USER_GEDCOM_ADMIN) {
			print "<input name=\"filename\" type=\"text\" value=\"" . htmlentities($fileName) . "\" size=\"40\"";
			if ($isExternal)
				print " />";
			else
				print " /><br /><sub>" . $pgv_lang["server_file_advice"] . "</sub>";
		}
		else {
			$thumbnail = thumbnail_file($fileName, true, false, $pid);
			if (!empty($thumbnail)) {
				print "<img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\"";
				if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
				print " alt=\"\" title=\"\" />";
			}
			print $fileName;
			print "<input name=\"filename\" type=\"hidden\" value=\"" . htmlentities($fileName) . "\" size=\"40\" />";
		}
		print "</td>";
		print "</tr>\n";

	}

	// Box for user to choose the folder to store the image
	if (!$isExternal && $MEDIA_DIRECTORY_LEVELS > 0) {
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("upload_server_folder_help", "qm");
		if (empty ($folder)) {
			if (!empty ($_SESSION['upload_folder']))
				$folder = $_SESSION['upload_folder'];
			else
				$folder = $MEDIA_DIRECTORY;
		}
		print $pgv_lang["server_folder"] . "</td><td class=\"optionbox wrap\">";
		//-- don't let regular users change the location of media items
		if ($action!='update' || PGV_USER_GEDCOM_ADMIN) {
		$folders = get_media_folders();
		print "<span dir=\"ltr\"><select name=\"folder_list\" onchange=\"document.newmedia.folder.value=this.options[this.selectedIndex].value;\">\n";
		foreach ($folders as $f) {
			if (!strpos($f, ".svn")){    //Do not print subversion directories
				print "<option value=\"$f\"";
				if ($folder == $f)
					print " selected=\"selected\"";
				print ">$f</option>\n";
			}
		}
			if (PGV_USER_GEDCOM_ADMIN) print "<option value=\"other\">" . $pgv_lang["add_media_other_folder"] . "</option>\n";
		print "</select></span>\n";
		}
		else print $folder;
		if (PGV_USER_GEDCOM_ADMIN) print "<span dir=\"ltr\"><input type=\"text\" name=\"folder\" size=\"30\" value=\"" . $folder . "\"></span>";
		else print "<input name=\"folder\" type=\"hidden\" value=\"" . addslashes($folder) . "\" />";
		if ($gedfile == "FILE") {
			print "<br /><sub>" . $pgv_lang["server_folder_advice2"] . "</sub></td></tr>";
		}
		print "</td></tr>";
	}
	else 
		print "<input name=\"folder\" type=\"hidden\" value=\"\" />";
	print "<input name=\"oldFolder\" type=\"hidden\" value=\"" . addslashes($folder) . "\" />";
	// 2 FORM
	if ($gedrec == "")
		$gedform = "FORM";
	else {
		$gedform = get_first_tag(2, "FORM", $gedrec);
		if (empty ($gedform))
			$gedform = "FORM";
	}
	$formid = add_simple_tag("2 $gedform");

	// 3 TYPE
	if ($gedrec == "")
		$gedtype = "TYPE";
	else {
		$temp = str_replace("\r\n", "\n", $gedrec) . "\n";
		$types = preg_match("/3 TYPE(.*)\n/", $temp, $matches);
		if (empty ($matches[0]))
			$gedtype = "TYPE";
		else
			$gedtype = "TYPE " . trim($matches[1]);
	}
	add_simple_tag("3 $gedtype");

	// 2 TITL
	if ($gedrec == "")
		$gedtitl = "TITL";
	else {
		$gedtitl = get_first_tag(2, "TITL", $gedrec);
		if (empty ($gedtitl))
			$gedtitl = get_first_tag(1, "TITL", $gedrec);
		if (empty ($gedtitl))
			$gedtitl = "TITL";
	}
	add_simple_tag("2 $gedtitl");

	// 3 _HEB
	if ($gedrec == "")
		$gedtitl = "_HEB";
	else {
		$gedtitl = get_first_tag(3, "_HEB", $gedrec);
		if (empty ($gedtitl))
			$gedtitl = "_HEB";
	}
	add_simple_tag("3 $gedtitl");

	// 3 ROMN
	if ($gedrec == "")
		$gedtitl = "ROMN";
	else {
		$gedtitl = get_first_tag(3, "ROMN", $gedrec);
		if (empty ($gedtitl))
			$gedtitl = "ROMN";
	}
	add_simple_tag("3 $gedtitl");

	// 2 _PRIM
	if ($gedrec == "")
		$gedprim = "_PRIM";
	else {
		//		$gedprim = get_sub_record(1, "_PRIM", $gedrec);
		$gedprim = get_first_tag(1, "_PRIM", $gedrec);
		if (empty ($gedprim))
			$gedprim = "_PRIM";
	}
	add_simple_tag("1 $gedprim");
	
	//-- don't show _THUM option to regular users
	if (PGV_USER_GEDCOM_ADMIN) {
		// 2 _THUM
		if ($gedrec == "")
			$gedthum = "_THUM";
		else {
			//		$gedthum = get_sub_record(1, "_THUM", $gedrec);
			$gedthum = get_first_tag(1, "_THUM", $gedrec);
			if (empty ($gedthum))
				$gedthum = "_THUM";
		}
		add_simple_tag("1 $gedthum");
	}

	//-- print out editing fields for any other data in the media record
	$sourceSOUR = "";
	if (!empty ($gedrec)) {
		$subrecs = get_all_subrecords($gedrec, "FILE,FORM,TYPE,TITL,_PRIM,_THUM,CHAN,DATA");
		foreach ($subrecs as $ind => $subrec) {
			$pieces = explode("\n", $subrec);
			foreach ($pieces as $piece) {
				$ft = preg_match("/(\d) (\w+)(.*)/", $piece, $match);
				if ($ft == 0) continue;
				$subLevel = $match[1];
				$fact = trim($match[2]);
				$event = trim($match[3]);
				if ($fact=="NOTE" || $fact=="TEXT") {
					$event .= get_cont(($subLevel +1), $subrec, false);
				}
				if ($sourceSOUR!="" && $subLevel<=$sourceLevel) {
					// Get rid of all saved Source data
					add_simple_tag($sourceLevel ." SOUR ". $sourceSOUR);
					add_simple_tag(($sourceLevel+1) ." PAGE ". $sourcePAGE);
					add_simple_tag(($sourceLevel+2) ." TEXT ". $sourceTEXT);
					add_simple_tag(($sourceLevel+2) ." DATE ". $sourceDATE, "", $pgv_lang["date_of_entry"]);
					add_simple_tag(($sourceLevel+1) ." QUAY ". $sourceQUAY);
					$sourceSOUR = "";
				}
				
				if ($fact=="SOUR") {
					$sourceLevel = $subLevel;
					$sourceSOUR = $event;
					$sourcePAGE = "";
					$sourceTEXT = "";
					$sourceDATE = "";
					$sourceQUAY = "";
					continue;
				}

				// Save all incoming data about this source reference
				if ($sourceSOUR!="") {
					if ($fact=="PAGE") {
						$sourcePAGE = $event;
						continue;
					}
					if ($fact=="TEXT") {
						$sourceTEXT = $event;
						continue;
					}
					if ($fact=="DATE") {
						$sourceDATE = $event;
						continue;
					}
					if ($fact=="QUAY") {
						$sourceQUAY = $event;
						continue;
					}
					continue;
				}

				// Output anything that isn't part of a source reference
				if (!empty ($fact) && $fact != "CONC" && $fact != "CONT" && $fact != "DATA") {
					add_simple_tag($subLevel ." ". $fact ." ". $event);
				}
			}
		}

		if ($sourceSOUR!="") {
			// Get rid of all saved Source data
			add_simple_tag($sourceLevel ." SOUR ". $sourceSOUR);
			add_simple_tag(($sourceLevel+1) ." PAGE ". $sourcePAGE);
			add_simple_tag(($sourceLevel+2) ." TEXT ". $sourceTEXT);
			add_simple_tag(($sourceLevel+2) ." DATE ". $sourceDATE, "", $pgv_lang["date_of_entry"]);
			add_simple_tag(($sourceLevel+1) ." QUAY ". $sourceQUAY);
		}
	}
	print "</table>\n";
?>
		<script language="JavaScript" type="text/javascript">
			var formid = '<?php print $formid; ?>';
			function updateFormat(filename) {
				var extsearch=/\.([a-zA-Z]{3,4})$/;
				ext='';
				if (extsearch.exec(filename)) {
					ext = RegExp.$1;
					if (ext=='jpg') ext='jpeg';
					if (ext=='tif') ext='tiff';
				}
				formfield = document.getElementById(formid);
				formfield.value = ext;
			}
		</script>
<?php

	print_add_layer("SOUR", 1);
	print_add_layer("NOTE", 1);
	print_add_layer("RESN", 1);
	print "<input type=\"submit\" value=\"" . $pgv_lang["save"] . "\" />";
	print "</form>\n";
}

// looks in both the standard and protected media directories
function findImageSize($file) {
	global $USE_MEDIA_FIREWALL;
	if (strtolower(substr($file, 0, 7)) == "http://")
		$file = "http://" . rawurlencode(substr($file, 7));
	else
		$file = filename_decode($file);
	$imgsize = @getimagesize($file);
	if ($USE_MEDIA_FIREWALL && !$imgsize) {
		$imgsize = @getimagesize(get_media_firewall_path($file));
	}
	if (!$imgsize) {
		$imgsize[0] = 300;
		$imgsize[1] = 300;
		$imgsize[2] = false;
	}
	return $imgsize;
}

/**
 * Print the list of persons, families, and sources that are mentioned in
 * the "LINKS" array of the current item from the Media list.
 *
 * This function is called from media.php, medialist.php, and random_media.php
 */

function PrintMediaLinks($links, $size = "small") {
	;
	global $TEXT_DIRECTION, $pgv_lang;

	if (count($links) == 0)
		return false;

	if ($size != "small")
		$size = "normal";

	$linkList = array ();

	foreach ($links as $id => $type) {
		$linkItem = array ();

		$linkItem["id"] = $id;
		$linkItem["type"] = $type;
		$linkItem["name"] = "";
		if ($type == "INDI" && displayDetailsByID($id)) {
			$linkItem["name"] = "A" . get_sortable_name($id);
			$linkItem["printName"] = get_person_name($id);
		} else
			if ($type == "FAM" && displayDetailsByID($id, "FAM")) {
				$linkItem["name"] = "B" . get_sortable_family_descriptor($id);
				$linkItem["printName"] = get_family_descriptor($id);
			} else
				if ($type == "SOUR" && displayDetailsByID($id, "SOUR")) {
					$linkItem["printName"] = get_source_descriptor($id);
					$linkItem["name"] = "C" . $linkItem["printName"];
				}

		if ($linkItem["name"] != "")
			$linkList[] = $linkItem;
	}
	uasort($linkList, "mediasort");

	$firstLink = true;
	$firstIndi = true;
	$firstFam = true;
	$firstSour = true;
	$firstObje = true;
	if ($size == "small")
		print "<sub>";
	foreach ($linkList as $linkItem) {
		if ($linkItem["type"] == "INDI") {
			if ($firstIndi && !$firstLink)
				print "<br />";
			$firstLink = false;
			$firstIndi = false;
			print "<br /><a href=\"individual.php?pid=" . $linkItem["id"] . "\">";
			if (begRTLText($linkItem["printName"]) && $TEXT_DIRECTION == "ltr") {
				print $pgv_lang["view_person"] . " -- ";
				print "(" . $linkItem["id"] . ")&nbsp;&nbsp;";
				print PrintReady($linkItem["printName"]);
			} else {
				print $pgv_lang["view_person"] . " -- ";
				print PrintReady($linkItem["printName"]) . "&nbsp;&nbsp;";
				if ($TEXT_DIRECTION == "rtl")
					print getRLM();
				print "(" . $linkItem["id"] . ")";
				if ($TEXT_DIRECTION == "rtl")
					print getRLM();
			}
			print "</a>";
		}
		if ($linkItem["type"] == "FAM") {
			if ($firstFam && !$firstLink)
				print "<br />";
			$firstLink = false;
			$firstFam = false;
			print "<br /><a href=\"family.php?famid=" . $linkItem["id"] . "\">";
			if (begRTLText($linkItem["printName"]) && $TEXT_DIRECTION == "ltr") {
				print $pgv_lang["view_family"] . " -- ";
				print "(" . $linkItem["id"] . ")&nbsp;&nbsp;";
				print PrintReady($linkItem["printName"]);
			} else {
				print $pgv_lang["view_family"] . " -- ";
				print PrintReady($linkItem["printName"]) . "&nbsp;&nbsp;";
				if ($TEXT_DIRECTION == "rtl")
					print getRLM();
				print "(" . $linkItem["id"] . ")";
				if ($TEXT_DIRECTION == "rtl")
					print getRLM();
			}
			print "</a>";
		}
		if ($linkItem["type"] == "SOUR") {
			if ($firstSour && !$firstLink)
				print "<br />";
			$firstLink = false;
			$firstSour = false;
			print "<br /><a href=\"source.php?sid=" . $linkItem["id"] . "\">";
			if (begRTLText($linkItem["printName"]) && $TEXT_DIRECTION == "ltr") {
				print $pgv_lang["view_source"] . " -- ";
				print "(" . $linkItem["id"] . ")&nbsp;&nbsp;";
				print PrintReady($linkItem["printName"]);
			} else {
				print $pgv_lang["view_source"] . " -- ";
				print PrintReady($linkItem["printName"]) . "&nbsp;&nbsp;";
				if ($TEXT_DIRECTION == "rtl")
					print getRLM();
				print "(" . $linkItem["id"] . ")";
				if ($TEXT_DIRECTION == "rtl")
					print getRLM();
			}
			print "</a>";
		}
	}
	if ($size == "small")
		print "</sub>";
	return true;
}

function get_media_id_from_file($filename){
	global $TBLPREFIX, $BUILDING_INDEX, $DBCONN, $gGedcom, $DBCONN;
	$dbq = "select m_media from ".$TBLPREFIX."media where m_file LIKE '%".$DBCONN->escapeSimple($filename)."'";
	$dbr = dbquery($dbq);
	$mid = $dbr->fetchRow();
	return $mid[0];
}
//returns an array of rows from the database containing the Person ID's for the people associated with this picture
function get_media_relations($mid){
	global $TBLPREFIX, $BUILDING_INDEX, $DBCONN, $gGedcom, $GEDCOM, $medialist;

	//-- check in the medialist cache first
	$firstChar = substr($mid, 0, 1);
	$restChar = substr($mid, 1);
	if (is_numeric($firstChar)) {
		$firstChar = "";
		$restChar = $mid;
	}
	$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $gGedcom->mGEDCOMId;
	if (isset ($medialist[$keyMediaList]['LINKS'])) {
		return $medialist[$keyMediaList]['LINKS'];
	}

	$media = array();

	$dbq = "SELECT mm_gid FROM ".$TBLPREFIX."media_mapping WHERE mm_media='".$mid."' AND mm_gedfile='".$gGedcom->mGEDCOMId."'";
	$dbr = dbquery($dbq);
	while($row = $dbr->fetchRow()) {
		if ($row[0] != $mid){
			$media[$row[0]] = id_type($row[0]);
		}
	}
	$medialist[$keyMediaList]['LINKS'] = $media;
	return $media;
}
//Basically calls the get_media_relations method but it uses a file name rather than a media id.
function get_media_relations_with_file_name($filename){
global $TBLPREFIX, $BUILDING_INDEX, $DBCONN, $gGedcom, $GEDCOM;
	$dbq = "select m_media from ".$TBLPREFIX."media where m_file='".$filename."' and m_gedfile='".$gGedcom->mGEDCOMId."'";
	$dbr = dbquery($dbq);
	if (isset($dbr)){
		while($result = $dbr->fetchRow()) {
			$media_id = $result[0];
			$media_array = get_media_relations($media_id);
			return $media_array;
		}
	}
	else{
		return array();
	}
}

// clips a media item based on data from the gedcom
function picture_clip($person_id, $image_id, $filename, $thumbDir)
{
	global $gGedcom,$GEDCOM,$TBLPREFIX,$MEDIA_DIRECTORY;
	// This gets the gedrec
	$query = "select m_gedrec from ".$TBLPREFIX."media where m_media='".$image_id."' AND m_gedfile=".$gGedcom->mGEDCOMId;
	$res = dbquery($query);
	$result = $res->fetchRow();
	//Get the location of the file, and then make a location for the clipped image

	//store values to the variables
	$top = get_gedcom_value("_TOP", 2, $result[0]);
	$bottom = get_gedcom_value("_BOTTOM", 2, $result[0]);
	$left = get_gedcom_value("_LEFT", 2, $result[0]);
	$right = get_gedcom_value("_RIGHT", 2, $result[0]);
	//check to see if all values were retrived
	if ($top != null || $bottom != null || $left != null || $right != null)
	{
		$image_filename = check_media_depth($filename);
		$image_dest = $thumbDir.$person_id."_".$image_filename[count($image_filename)-1].".jpg";
		//call the cropimage function
		cropimage($filename, $image_dest, $left, $top, $right, $bottom); //removed offset 50
		return  $image_dest;
	}
	return "";
}
function cropImage($image, $dest_image, $left, $top, $right, $bottom){ //$image is the string location of the original image, $dest_image is the string file location of the new image, $fx is the..., $fy is the...
	global $THUMBNAIL_WIDTH;
	$ims = @getimagesize($image);
	$cwidth = ($ims[0]-$right)-$left;
	$cheight = ($ims[1]-$bottom)-$top;
	$width = $THUMBNAIL_WIDTH;
	$height = round($cheight * ($width/$cwidth));
	if($ims['mime'] == "image/png") //if the type is png
	{
	$img = imagecreatetruecolor(($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
	//$org_img = imagecreatefromjpeg($image);
	$org_img = imagecreatefrompng($image);
	$ims = @getimagesize($image);
	imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
	//imagejpeg($img,$dest_image,90);
	imagepng($img,$dest_image);
	imagedestroy($img);
	}
	if($ims['mime'] == "image/jpeg") //if the type is jpeg
	{
	$img = imagecreatetruecolor($width, $height);
	$org_img = imagecreatefromjpeg($image);
	$ims = @getimagesize($image);
	imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
	imagejpeg($img,$dest_image,90);
	imagedestroy($img);
	}
	if($ims['mime'] == "image/gif") //if the type is gif
	{
	$img = imagecreatetruecolor(($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
	$org_img =  imagecreatefromgif($image);
	$ims = @getimagesize($image);
	imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
	imagegif($img,$dest_image);
	imagedestroy($img);
	}
}

// checks whether a media file exists.
// returns 1 for external media
// returns 2 if it was found in the standard directory
// returns 3 if it was found in the media firewall directory
// returns false if not found
function media_exists($filename) {
	global $USE_MEDIA_FIREWALL;
	if (empty($filename)) { return false; }
	if (isFileExternal($filename)) { return 1; }
	$filename = filename_decode($filename);
	if ( file_exists($filename) ) { return 2; }
	if ( $USE_MEDIA_FIREWALL && file_exists(get_media_firewall_path($filename)) ) { return 3; }
	return false;
}

// returns size of file.  looks in both the standard and protected media directories
function media_filesize($filename) {
	global $USE_MEDIA_FIREWALL;
	$filename = filename_decode($filename);
	if (file_exists($filename)) { return filesize($filename); }
	if ($USE_MEDIA_FIREWALL && file_exists(get_media_firewall_path($filename))) { return filesize(get_media_firewall_path($filename)); }
	return;
}

// returns path to file on server
function get_server_filename($filename) {
		global $USE_MEDIA_FIREWALL;
		if (file_exists($filename)){
			return($filename);
		}
		if ($USE_MEDIA_FIREWALL) {
			$protectedfilename = get_media_firewall_path($filename);
			if (file_exists($protectedfilename)){
				return($protectedfilename);
			}
		}
		return($filename);
}

// pass in the standard media directory
// returns protected media directory
// strips off any "../" which may be configured in your MEDIA_DIRECTORY variable 
function get_media_firewall_path($path) {
	global $MEDIA_FIREWALL_ROOTDIR;
	$path = str_replace("../", "", $path);
	return ($MEDIA_FIREWALL_ROOTDIR . $path);
}

// pass in the protected media directory
// returns standard media directory
function get_media_standard_path($path) {
	global $MEDIA_FIREWALL_ROOTDIR;
	$path = str_replace($MEDIA_FIREWALL_ROOTDIR, "", $path);
	return ($path);
}

// recursively make directories
// taken from http://us3.php.net/manual/en/function.mkdir.php#60861
function mkdirs($dir, $mode = 0777, $recursive = true) {
	if( is_null($dir) || $dir === "" ){
		return FALSE;
	}
	if( is_dir($dir) || $dir === "/" ){
		return TRUE;
	}
	if( mkdirs(dirname($dir), $mode, $recursive) ){
		return mkdir($dir, $mode);
	}
	return FALSE;
}

// pass in an image type and this will determine if your system supports editing of that image type 
function isImageTypeSupported($reqtype) {
	if (!function_exists("imagetypes")) return false;
	$reqtype = strtolower($reqtype);
	if ( ( ($reqtype == 'jpg') || ($reqtype == 'jpeg') ) && (imagetypes() & IMG_JPG)) {
		return ('jpeg');
	} else if (($reqtype == 'gif') && (imagetypes() & IMG_GIF)) {
		return ('gif');
	} else if (($reqtype == 'png') && (imagetypes() & IMG_PNG)) {
		return ('png');
	} else if ( ( ($reqtype == 'wbmp') || ($reqtype == 'bmp') ) && (imagetypes() & IMG_WBMP)) {
		return ('wbmp');
	} else {
		return false;
	}
}

// converts raw values from php.ini file into bytes 
// from http://www.php.net/manual/en/function.ini-get.php
function return_bytes($val) {
	if (!$val) {
		// no value was passed in, assume no limit and return -1 
		$val = -1; 
	}
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
	switch($last) {
		case 'g': $val *= 1024;  // fallthrough
		case 'm': $val *= 1024;  // fallthrough
		case 'k': $val *= 1024;
	}
	return $val;
}

// pass in the full path to an image, returns string with size/height/width/bits/channels
function getImageInfoForLog($filename) {
	$filesize = sprintf("%.2f", filesize($filename)/1024);
	$imgsize = @getimagesize($filename);
	$strinfo = $filesize."kb ";
	if (is_array($imgsize)) { $strinfo .= @$imgsize[0]."x".@$imgsize[1]." ".@$imgsize['bits']." bits ".@$imgsize['channels']. " channels"; }
	return ($strinfo);
}

// attempts to determine whether there is enough memory to load a particular image
function hasMemoryForImage($serverFilename, $debug_verboseLogging=false) {
	// find out how much total memory this script can access
	$memoryAvailable = return_bytes(@ini_get('memory_limit'));
	// if memory is unlimited, it will return -1 and we don't need to worry about it
	if ($memoryAvailable == -1) return true;
	
	// find out how much memory we are already using
	// if the memory_get_usage() function doesn't exist, assume we are using 900k to load the PGV framework
	$memoryUsed = ( function_exists('memory_get_usage') ) ? memory_get_usage() : 900000;

	$imgsize = @getimagesize($serverFilename);
	// find out how much memory this image needs for processing, probably only works for jpegs
	// from comments on http://www.php.net/imagecreatefromjpeg
	if (is_array($imgsize) && isset($imgsize['bits']) && (isset($imgsize['channels']))) {
		$memoryNeeded = Round(($imgsize[0] * $imgsize[1] * $imgsize['bits'] * $imgsize['channels'] / 8 + Pow(2, 16)) * 1.65);
		$memorySpare = $memoryAvailable - $memoryUsed - $memoryNeeded;
		if ($memorySpare > 0) {
			// we have enough memory to load this file
			if ($debug_verboseLogging) AddToLog("Media: >about to load< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory avail: ".$memoryAvailable." used: ".$memoryUsed." needed: ".$memoryNeeded." spare: ".$memorySpare);
			return true;
		} else {
			// not enough memory to load this file
			AddToLog("Media: >image too large to load< file >".$serverFilename."< (".getImageInfoForLog($serverFilename).") memory avail: ".$memoryAvailable." used: ".$memoryUsed." needed: ".$memoryNeeded." spare: ".$memorySpare);
			return false;
		}
	} else {
		// assume there is enough memory
		// TODO find out how to check memory needs for gif and png
		return true;
	}
}

/**
 * function to generate a thumbnail image
 * @param string $filename
 * @param string $thumbnail
 */
function generate_thumbnail($filename, $thumbnail) {
	global $MEDIA_DIRECTORY, $THUMBNAIL_WIDTH, $AUTO_GENERATE_THUMBS, $USE_MEDIA_FIREWALL, $MEDIA_FIREWALL_THUMBS;

	if (!$AUTO_GENERATE_THUMBS) return false;
	if (media_exists($thumbnail)) return false;
	if (!is_writable($MEDIA_DIRECTORY."thumbs")) return false;

/*	No references to "media/thumbs/urls" exist anywhere else
	if (!is_dir(filename_decode($MEDIA_DIRECTORY."thumbs/urls"))) {
		mkdir(filename_decode($MEDIA_DIRECTORY."thumbs/urls"), 0777);
		AddToLog("Folder ".$MEDIA_DIRECTORY."thumbs/urls created.");
	}
	if (!is_writable(filename_decode($MEDIA_DIRECTORY."thumbs/urls"))) return false;
*/

	$ext = "";
	$ct = preg_match("/\.([^\.]+)$/", $filename, $match);
	if ($ct>0) {
		$ext = strtolower(trim($match[1]));
	}

	$type = isImageTypeSupported($ext);
	if ( !$type ) return false;
	
	if (!isFileExternal($filename)) {
		// internal
		if (!file_exists(filename_decode($filename))) {
			if ($USE_MEDIA_FIREWALL) {
				// see if the file exists in the protected index directory
				$filename = get_media_firewall_path($filename); 
				if (!file_exists(filename_decode($filename))) return false;
				if ($MEDIA_FIREWALL_THUMBS) {
					// put the thumbnail in the protected directory too
					$thumbnail = get_media_firewall_path($thumbnail);
				}
				// ensure the directory exists
				if (!is_dir(dirname($thumbnail))) {
					if (!mkdirs(dirname($thumbnail))) {
						return false;
					}
				}
			} else {
				return false;
			}
		}
		$imgsize = getimagesize(filename_decode($filename));
		// Check if a size has been determined
		if (!$imgsize) return false;

		//-- check if file is small enough to be its own thumbnail
		if (($imgsize[0]<150)&&($imgsize[1]<150)) {
			@copy($filename, $thumbnail);
			return true;
		}
	}
	else {
		// external
		if ($fp = @fopen(filename_decode($filename), "rb")) {
			if ($fp===false) return false;
			$conts = "";
			while(!feof($fp)) {
				$conts .= fread($fp, 4098);
			}
			fclose($fp);
			$fp = fopen(filename_decode($thumbnail), "wb");
			if (!fwrite($fp, $conts)) return false;
			fclose($fp);
			if (!isFileExternal($filename)) $imgsize = getimagesize(filename_decode($filename));
			else $imgsize = getimagesize(filename_decode($thumbnail));
			if ($imgsize===false) return false;
			if (($imgsize[0]<150)&&($imgsize[1]<150)) return true;
		}
		else return false;
	}

	// make sure we have enough memory to process this file
	if (!hasMemoryForImage(filename_decode($filename))) return false;

	$width = $THUMBNAIL_WIDTH;
	$height = round($imgsize[1] * ($width/$imgsize[0]));

	$imCreateFunc = 'imagecreatefrom'.$type;
	$imSendFunc = 'image'.$type;
		
	// load the image into memory
	$im = @$imCreateFunc(filename_decode($filename));
	if (!$im) return false;
	// create a blank thumbnail image in memory
	$new = imagecreatetruecolor($width, $height);
	// resample the original image into the thumbnail
	imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
	// save the thumbnail to a file 
	$imSendFunc($new, filename_decode($thumbnail));
	// free up memory
	imagedestroy($im);
	imagedestroy($new);
	return true;
}

?>

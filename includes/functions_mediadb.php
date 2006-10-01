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
 * @version $Id: functions_mediadb.php,v 1.2 2006/10/01 22:44:03 lsces Exp $
 */

if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
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
function remove_db_media($media,$ged) {
	global $TBLPREFIX;

	$success = false;
	
	// remove the media record
	$sql = "DELETE FROM ".$TBLPREFIX."media WHERE m_media='$media' AND m_gedfile='$ged'";
	if ($res =& dbquery($sql)) $success = true;

	// remove all links to this media item
	$sql = "DELETE FROM ".$TBLPREFIX."media_mapping WHERE mm_media='$media' AND mm_gedfile='$ged'";
	if ($res =& dbquery($sql)) $success = true;

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
	global $TBLPREFIX;

	// remove link to this media item
	$sql = "DELETE FROM ".$TBLPREFIX."media_mapping WHERE (mm_media='".addslashes($media)."' AND mm_gedfile='".addslashes($ged)."' AND mm_gid='".addslashes($indi)."')";
	$tempsql = dbquery($sql);
	$res =& $tempsql;

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
	global $GEDCOMS, $TBLPREFIX;

	$sql = "SELECT * FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='".$GEDCOMS[$ged]["id"]."' AND mm_gid='".addslashes($indi)."' AND mm_media='".addslashes($media)."'";
	$tempsql = dbquery($sql);
	$res =& $tempsql;
	if ($res->numRows()) { return true;} else {return false;}
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
	global $GEDCOMS, $TBLPREFIX;

	// replace the gedrec for the media record
	$sql = "UPDATE ".$TBLPREFIX."media SET m_gedrec = '".addslashes($gedrec)."' WHERE (m_id = '".addslashes($media)."' AND m_gedfile = '".$GEDCOMS[$ged]["id"]."')";
	$tempsql = dbquery($sql);
	$res =& $tempsql;

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
function update_db_link($media, $indi, $gedrec, $ged, $order=-1) {
	global $TBLPREFIX, $GEDCOMS;

	if (exists_db_link($media, $indi, $ged)) {
		// replace the gedrec for the media link record
		$sql = "UPDATE ".$TBLPREFIX."media_mapping SET mm_gedrec = '".addslashes($gedrec)."'";
		if ($order >= 0) $sql .= ", mm_order = $order";
		$sql .= " WHERE (mm_media = '".addslashes($media)."' AND mm_gedfile = '".$GEDCOMS[$ged]["id"]."' AND mm_gid = '".addslashes($indi)."')";
		$tempsql = dbquery($sql);
		if ($res =& $tempsql) {
			AddToLog("Media record: ".$media." updated successfully");
			return true;
		}
		else {
			AddToLog("There was a problem updating media record: ".$media);
			return false;
		}
	}
	else {
		add_db_link($media, $indi, $gedrec, $ged, $order=-1);
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
function add_db_link($media, $indi, $gedrec, $ged, $order=-1) {
	global $TBLPREFIX, $GEDCOMS;


	// if no preference to order find the number of records and add to the end
	if ($order=-1) {
		$sql = "SELECT * FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='".$GEDCOMS[$ged]["id"]."' AND mm_gid='".addslashes($indi)."'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		$ct = $res->numRows();
		$order = $ct + 1;
	}

	// add the new media link record
	$mm_id = get_next_id("media_mapping", "mm_id");
	$sql = "INSERT INTO ".$TBLPREFIX."media_mapping VALUES('".$mm_id."','".addslashes($media)."','".addslashes($indi)."','".addslashes($order)."','".$GEDCOMS[$ged]["id"]."','".addslashes($gedrec)."')";
	$tempsql = dbquery($sql);
	if ($res =& $tempsql) {
		AddToChangeLog("New media link added to the database: ".$media);
		return true;
	}
	else {
		AddToChangeLog("There was a problem adding media record: ".$media);
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
	global $GEDCOM, $GEDCOMS;
	global $TBLPREFIX;

	$medialist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."media WHERE m_gedfile='".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY m_id";
	$tempsql = dbquery($sql);
	$res =& $tempsql;
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$media = array();
		$media["ID"] = $row["m_id"];
		$media["XREF"] = stripslashes($row["m_media"]);
		$media["GEDFILE"] = stripslashes($row["m_gedfile"]);
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
	global $GEDCOM, $GEDCOMS, $TBLPREFIX;

	$mappinglist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY mm_gid, mm_order";
	$tempsql = dbquery($sql);
	$res =& $tempsql;
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$mapping = array();
		$mapping["ID"] = $row["mm_id"];
		$mapping["INDI"] = stripslashes($row["mm_gid"]);
		$mapping["XREF"] = stripslashes($row["mm_media"]);
		$mapping["GEDFILE"] = stripslashes($row["mm_gedfile"]);
		$mapping["ORDER"] = stripslashes($row["mm_order"]);
		$mapping["GEDFILE"] = stripslashes($row["mm_gedfile"]);
		$mapping["NOTE"] = stripslashes($row["mm_gedrec"]);
		$mappinglist[] = $mapping;
	}
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
	global $GEDCOM, $GEDCOMS, $TBLPREFIX, $DBCONN;

	$mappinglist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='".$GEDCOMS[$GEDCOM]["id"]."' AND mm_gid='".$DBCONN->escape($indi)."' ORDER BY mm_order";
	$tempsql = dbquery($sql);
	$res =& $tempsql;
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$mapping = array();
		$mapping["ID"] = $row["mm_id"];
		$mapping["INDI"] = stripslashes($row["mm_gid"]);
		$mapping["XREF"] = stripslashes($row["mm_media"]);
		$mapping["GEDFILE"] = stripslashes($row["mm_gedfile"]);
		$mapping["ORDER"] = stripslashes($row["mm_order"]);
		$mapping["GEDFILE"] = stripslashes($row["mm_gedfile"]);
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

	$newnote = $level." NOTE\r\n";
	$indent = $level + 1;
	$newline = $indent." CONC ".$txt;
	$newlines = preg_split("/\r?\n/", $newline);
	for($k=0; $k<count($newlines); $k++) {
		if ($k>0) $newlines[$k] = $indent." CONT ".$newlines[$k];
		if (strlen($newlines[$k])>255) {
			while(strlen($newlines[$k])>255) {
				$newnote .= substr($newlines[$k], 0, 255)."\r\n";
				$newlines[$k] = substr($newlines[$k], 255);
				$newlines[$k] = $indent." CONC ".$newlines[$k];
			}
			$newnote .= trim($newlines[$k])."\r\n";
		}
		else {
			$newnote .= trim($newlines[$k])."\r\n";
		}
	}
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
function real_path($path)
{
   if ($path == "") { return false; }

   $path = trim(preg_replace("/\\\\/", "/", (string)$path));

   if (!preg_match("/(\.\w{1,4})$/", $path)  &&
       !preg_match("/\?[^\\/]+$/", $path)  &&
       !preg_match("/\\/$/", $path))
   {
       $path .= '/';
   }

   $pattern = "/^(\\/|\w:\\/|https?:\\/\\/[^\\/]+\\/)?(.*)$/i";

   preg_match_all($pattern, $path, $matches, PREG_SET_ORDER);

   $path_tok_1 = $matches[0][1];
   $path_tok_2 = $matches[0][2];

   $path_tok_2 = preg_replace(
                   array("/^\\/+/", "/\\/+/"),
                   array("", "/"),
                   $path_tok_2);

   $path_parts = explode("/", $path_tok_2);
   $real_path_parts = array();

   for ($i = 0, $real_path_parts = array(); $i < count($path_parts); $i++)
   {
       if ($path_parts[$i] == '.')
       {
           continue;
       }
       else if ($path_parts[$i] == '..')
       {
           if (  (isset($real_path_parts[0])  &&  $real_path_parts[0] != '..')
               || ($path_tok_1 != "")  )
           {
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
function set_media_path($filename, $moveto, $thumb=false) {
	$movefile = "";
	if ($moveto == "..") {
		$directories = preg_split("/\//", $filename);
		$ct = count($directories);
		foreach ($directories as $key => $value) {
			if ($key == 1 && $thumb == true) $movefile .= "thumbs/";
			if ($key != $ct-2) $movefile .= $value;
			if ($key != $ct-2 && $key != $ct-1) $movefile .= "/";
		}
	}
	else {
		$directories = preg_split("/\//", $filename);
		$ct = count($directories);
		foreach ($directories as $key => $value) {
			if ($key == 1 && $thumb == true) $movefile .= "thumbs/";
			if ($key == $ct-1) $movefile .= $moveto."/";
			$movefile .= $value;
			if ($key != $ct-1) $movefile .= "/";
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
	if (substr($MEDIA_DIRECTORY,0,1) != ".") {
		// Check first if the $MEDIA_DIRECTORY exists
		if (!is_dir($MEDIA_DIRECTORY)) {
			if (!mkdir($MEDIA_DIRECTORY)) return false;
			if (!file_exists($MEDIA_DIRECTORY."index.php")) {
				$inddata = html_entity_decode("<?php\nheader(\"Location: ../medialist.php\");\nexit;\n?>");
				$fp = @fopen($MEDIA_DIRECTORY."index.php","w+");
				if (!$fp) print "<div class=\"error\">".$pgv_lang["security_no_create"].$MEDIA_DIRECTORY."thumbs</div>";
				else {
					// Write the index.php for the media folder
					fputs($fp,$inddata);
					fclose($fp);
				}			
			}
		}
	}
	// Check if the thumbs folder exists
	if (!is_dir($MEDIA_DIRECTORY."thumbs")) {
		print $MEDIA_DIRECTORY."thumbs";
		if (!mkdir($MEDIA_DIRECTORY."thumbs")) return false;
		if (file_exists($MEDIA_DIRECTORY."index.php")) {
			$inddata = file_get_contents($MEDIA_DIRECTORY."index.php");
			$inddatathumb = str_replace(": ../",": ../../",$inddata);
			$fpthumb = @fopen($MEDIA_DIRECTORY."thumbs/index.php","w+");
			if (!$fpthumb) print "<div class=\"error\">".$pgv_lang["security_no_create"].$MEDIA_DIRECTORY."thumbs</div>";
			else {
				// Write the index.php for the thumbs media folder
				fputs($fpthumb,$inddatathumb);
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
 * - $media["FORM"] 	the format of the item (ie JPG, PDF, etc)
 * - $media["TITL"] 	a title for the item, used for list display
 * - $media["GEDCOM"] 	gedcom record snippet
 * - $media["LEVEL"]	level number (normally zero)
 * - $media["LINKED"] 	Flag for front end to indicate this is linked
 * - $media["LINKS"] 	Array of gedcom ids that this is linked to
 * - $media["CHANGE"]	Indicates the type of change waiting admin approval
 *
 * @return mixed A media list array.
 */

function get_medialist($currentdir=false, $directory="", $linkonly=false) {
	global $MEDIA_DIRECTORY_LEVELS, $BADMEDIA, $thumbdir, $TBLPREFIX, $MEDIATYPE, $DBCONN;
	global $level, $dirs, $ALLOW_CHANGE_GEDCOM, $GEDCOM, $GEDCOMS, $MEDIA_DIRECTORY;
	global $MEDIA_EXTERNAL, $medialist, $pgv_changes;
	
	
	// Retrieve the gedcoms to search in
	$sgeds = array();
	if (($ALLOW_CHANGE_GEDCOM) && (count($GEDCOMS) > 1)) {
		foreach ($GEDCOMS as $key=>$ged) {
			$str = preg_replace(array("/\./","/-/","/ /"), array("_","_","_"), $key);
			if (isset($$str)) $sgeds[] = $key;
		}
	}
	else $sgeds[] = $GEDCOM;
		
	// Create the medialist array of media in the DB and on disk
	// NOTE: Get the media in the DB
	$medialist = array();
	if (empty($directory)) $directory = $MEDIA_DIRECTORY;
	$myDir = str_replace($MEDIA_DIRECTORY, "", $directory); 
	$sql = "SELECT * FROM ".$TBLPREFIX."media WHERE m_gedfile='".$GEDCOMS[$GEDCOM]["id"]."'";
	$sql .= " AND (m_file LIKE '%".$DBCONN->escape($myDir)."%' OR m_file LIKE '%://%') ORDER BY m_id desc";
	//print "sql: ".$sql."<br />";
	$res =& dbquery($sql);
	$ct = $res->numRows();
	//print $directory.$sql;
	
	// Build the raw medialist array, 
	// but weed out any folders we're not interested in 
	$mediaObjects = array();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		if ($row) {
			if (!empty($row["m_file"])) {
				$fileName = check_media_depth(stripslashes($row["m_file"]), "NOTRUNC", "QUIET");
				if (($MEDIA_EXTERNAL && stristr($fileName, "://")) || !$currentdir || $directory==dirname($fileName)."/") {
					$media = array();
					$media["ID"] = $row["m_id"];
					$media["XREF"] = stripslashes($row["m_media"]);
					$media["GEDFILE"] = $row["m_gedfile"];
					$media["FILE"] = $fileName;
					if ($MEDIA_EXTERNAL && stristr($fileName, "://")) {
						$media["THUMB"] = $fileName;
						$media["EXISTS"] = true;
					} else {
						$media["THUMB"] = thumbnail_file($fileName);
						$media["EXISTS"] = file_exists(filename_decode($fileName));
					}
					$media["FORM"] = stripslashes($row["m_ext"]);
					$media["TITL"] = stripslashes($row["m_titl"]);
					$media["GEDCOM"] = stripslashes($row["m_gedrec"]);
					$gedrec =& trim($row["m_gedrec"]);
					$media["LEVEL"] = $gedrec{0};
					$media["LINKED"] = false;
					$media["LINKS"] = array();
					$media["CHANGE"] = "";
					
					// Build a sortable key for the medialist
					$firstChar = substr($media["XREF"], 0, 1);
					$restChar = substr($media["XREF"], 1);
					if (is_numeric($firstChar)) {
						$firstChar = "";
						$restChar = $media["XREF"];
					}
					$keyMediaList = $firstChar.substr("000000".$restChar, -6)."_".$media["GEDFILE"];
					$medialist[$keyMediaList] = $media;
					$mediaObjects[] = $media["XREF"];
				}
			}
		}
	}
	$res->free();
	//print "medialist: "; print_r($medialist); print "<br><br>";
	
	// Look for new Media objects in the list of changes pending approval
	// At the same time, accumulate a list of GEDCOM IDs that have changes pending approval
	$changedRecords = array();
	foreach($pgv_changes as $changes) {
		foreach ($changes as $change) {
			//print "change: "; print_r($change); print "<br><br>";
			while (true) {
				if ($change["gedcom"]!=$GEDCOM || $change["status"]!="submitted") break;

				$gedrec = find_gedcom_record($change["gid"]);
				if (empty($gedrec)) $gedrec = find_record_in_file($change["gid"]);
				if (empty($gedrec)) break;

				$ct = preg_match("/0 @.*@ (\w*)/", $gedrec, $match);
				$type = trim($match[1]);
				if ($type!="OBJE") {
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
				$keyMediaList = $firstChar.substr("000000".$restChar, -6)."_".$GEDCOMS[$GEDCOM]["id"];
				if (isset($medialist[$keyMediaList])) {
					$medialist[$keyMediaList]["CHANGE"] = $change["type"];
					break;
				}				

				// Build the entry for this new Media object
				$media = array();
				$media["ID"] = $change["gid"];
				$media["XREF"] = $change["gid"];
				$media["GEDFILE"] = $GEDCOMS[$GEDCOM]["id"];
				$media["FILE"] = "";
				$media["THUMB"] = "";
				$media["EXISTS"] = false;
				$media["FORM"] = "";
				$media["TITL"] = "";
				$media["GEDCOM"] = $gedrec;
				$media["LEVEL"] = "0";
				$media["LINKED"] = false;
				$media["LINKS"] = array();
				$media["CHANGE"] = "append";
				
				// Now fill in the blanks
				$subrecs = get_all_subrecords($gedrec, "_PRIM,_THUM,CHAN");
				foreach($subrecs as $subrec) {
					$pieces = explode("\r\n", $subrec);
					foreach($pieces as $piece) {
						$ft = preg_match("/(\d) (\w+)(.*)/", $piece, $match);
						if ($ft>0) {
							$subLevel = $match[1];
							$fact = trim($match[2]);
							$event = trim($match[3]);
							$event .= str_replace("<br />", "", get_cont(($subLevel+1), $subrec));
							
							if ($fact=="FILE") $media["FILE"] = $event;
							if ($fact=="FORM") $media["FORM"] = $event;
							if ($fact=="TITL") $media["TITL"] = $event;
						}
					}
				}
				
				// And a few more blanks
				if (empty($media["FILE"])) break;
				$fileName = check_media_depth(stripslashes($media["FILE"]), "NOTRUNC", "QUIET");
				if ($MEDIA_EXTERNAL && stristr($media["FILE"], "://")) {
					$media["THUMB"] = $fileName;
					$media["EXISTS"] = true;
				} else {
					if ($currentdir && $directory!=dirname($fileName)."/") break;
					$media["THUMB"] = thumbnail_file($fileName);
					$media["EXISTS"] = file_exists(filename_decode($fileName));
				}
				
				// Now save this for future use
				//print $keyMediaList.": "; print_r($media); print "<br><br>";
				$medialist[$keyMediaList] = $media;
				$mediaObjects[] = $media["XREF"];
				
				break;
			}
		}
	}
	
	$ct = count($medialist);
	//print "medialist: "; print_r($medialist); print "<br><br>";
	
	// Search the database for the applicable cross-references
	// and fill in the Links part of the medialist
	if ($ct > 0) {
		$sql = "SELECT * FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='".$GEDCOMS[$GEDCOM]["id"]."' AND (";
		$i = 0;
		foreach($medialist as $key => $media) {
			$i++;
			$sql .= "mm_media='".$media["XREF"]."'";
			if ($i < $ct) $sql .= " OR ";
			else $sql .= ")";
		}
		$sql .= " ORDER BY mm_gid";
		//print "sql: ".$sql."<br />";
		$res = dbquery($sql);
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			//print_r($row); print "<br />";
			// Build the key for the medialist
			$temp = stripslashes($row["mm_media"]);
			$firstChar = substr($temp, 0, 1);
			$restChar = substr($temp, 1);
			if (is_numeric($firstChar)) {
				$firstChar = "";
				$restChar = $temp;
			}
			$keyMediaList = $firstChar.substr("000000".$restChar, -6)."_".$row["mm_gedfile"];
			
			// Update the medialist with this cross-reference, 
			// but only if the Media item actually exists (could be a phantom reference)
			if (isset($medialist[$keyMediaList])) {
				$medialist[$keyMediaList]["LINKS"][stripslashes($row["mm_gid"])] = id_type(stripslashes($row["mm_gid"]));
				$medialist[$keyMediaList]["LINKED"] = true;
			} 
		}
		$res->free();
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
	foreach($changedRecords as $pid) {
		$gedrec = find_record_in_file($pid);
		if ($gedrec) {
			foreach($mediaObjects as $mediaId) {
				if (strpos($gedrec, "@".$mediaId."@")) {
					// Build the key for the medialist
					$firstChar = substr($mediaId, 0, 1);
					$restChar = substr($mediaId, 1);
					if (is_numeric($firstChar)) {
						$firstChar = "";
						$restChar = $mediaId;
					}
					$keyMediaList = $firstChar.substr("000000".$restChar, -6)."_".$GEDCOMS[$GEDCOM]["id"];
					
					// Add this GEDCOM ID to the link list of the media object
					if (isset($medialist[$keyMediaList])) {
						$medialist[$keyMediaList]["LINKS"][$pid] = id_type($pid);
						$medialist[$keyMediaList]["LINKED"] = true;
					}
				}
			}
		}
	}
	
	//ksort($medialist);
	uasort($medialist, "mediasort");
	
	//-- for the media list do not look in the directory
	if ($linkonly) return $medialist;
	
	// The database part of the medialist is now complete.
	// We still have to get a list of all media items that exist as files but
	// have not yet been entered into the database.  We'll do this only for the
	// current folder.
	//
	// At the same time, we'll build a list of all the sub-folders in this folder.
	$temp = str_replace($MEDIA_DIRECTORY, "", $directory);
	if ($temp=="") $folderDepth = 0;
	else $folderDepth = count(explode("/", $temp))-1;
	$dirs = array();
	
	$images = array();
	$d = dir(filename_decode(substr($directory, 0, -1)));
	while (false !== ($fileName = $d->read())) {
		$fileName = filename_encode($fileName);
		
		while (true) {
			// Make sure we only look at valid media files
			if (in_array($fileName, $BADMEDIA)) break;
			if (is_dir(filename_decode($directory.$fileName))) {
				if ($folderDepth < $MEDIA_DIRECTORY_LEVELS) $dirs[] = $fileName;
				break;
			}
			$exts = explode(".", $fileName);
			if (count($exts)==1) break;
			$ext = strtolower($exts[count($exts)-1]);
			if (!in_array($ext, $MEDIATYPE)) break;
			
			// This is a valid media file: 
			// now see whether we already know about it
			$mediafile = $directory.$fileName;
			$exist = false;
			$oldObject = false;
			foreach ($medialist as $key => $item) {
				if ($item["FILE"] == $directory.$fileName) {
					if ($item["CHANGE"]=="delete") {
						$exist = false;
						$oldObject = true;
					} else {
						$exist = true;
						$oldObject = false;
					}
				}
			}
			if ($exist) break;
			
			// This media item is not yet in the database
			$media = array();
			$media["ID"] = "";
			$media["XREF"] = "";
			$media["GEDFILE"] = "";
			$media["FILE"] = $directory.$fileName;
			$media["THUMB"] = thumbnail_file($directory.$fileName);
			$media["EXISTS"] = true;
			$media["FORM"] = $ext;
			if ($ext=="jpg" || $ext=="jp2") $media["FORM"] = "jpeg";
			$media["TITL"] = "";
			$media["GEDCOM"] = "";
			$media["LEVEL"] = "0";
			$media["LINKED"] = false;
			$media["LINKS"] = array();
			$media["CHANGE"] = "";
			if ($oldObject) $media["CHANGE"] = "append";
			$images[$fileName] = $media;
			break;
		}
	}
	$d->close();
	//print_r($images); print "<br />";
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

	if (empty($filter) || strlen($filter)<2) $filter = "";
	if (empty($acceptExt) || $acceptExt!="http") $acceptExt = "";
	
	$isEditor = userCanEdit(getUserName()); 

	while (true) {
		$isValid = true;
		
		//-- Check Privacy first.  No point in proceeding if Privacy says "don't show"
		$links = $media["LINKS"];
		if (count($links)!=0) {
			foreach($links as $id=>$type) {
				if (!displayDetailsByID($id, $type)) {
					$isValid = false;
					break;
				}
			}
		}
		
		//-- Accept external Media only if specifically told to do so
		if (stristr($media["FILE"],"://") && $acceptExt!="http") $isValid = false;
		
		if (!$isValid) break;

		//-- Accept everything if filter string is empty
		if ($filter=="") break;
		
		//-- Accept when filter string contained in file name (but only for editing users)
		if ($isEditor && stristr(basename($media["FILE"]), $filter)) break;
		
		//-- Accept when filter string contained in Media item's title
		if (stristr($media["TITL"], $filter)) break;
		
		//-- Accept when filter string contained in name of any item 
		//-- this Media item is linked to.  (Privacy already checked)
		$isValid = false;
		if (count($links)!=0) break;
		foreach($links as $id=>$type) {
			if ($type=="INDI" && stristr(get_person_name($id), $filter)) $isValid = true;
			if ($type=="FAM" && stristr(get_family_descriptor($id), $filter)) $isValid = true;
			if ($type=="SOUR" && stristr(get_source_descriptor($id), $filter)) $isValid = true;
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
function search_media_pids($query, $allgeds=false, $ANDOR="AND") {
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $GEDCOMS;
	$myindilist = array();
	if ($REGEXP_DB) $term = "REGEXP";
	else $term = "LIKE";
	if (!is_array($query)) $sql = "SELECT m_media as m_media FROM ".$TBLPREFIX."media WHERE (m_gedrec $term '".$DBCONN->escape(strtoupper($query))."' OR m_gedrec $term '".$DBCONN->escape(str2upper($query))."' OR m_gedrec $term '".$DBCONN->escape(str2lower($query))."')";
	else {
		$sql = "SELECT m_media FROM ".$TBLPREFIX."media WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(m_gedrec $term '".$DBCONN->escape(str2upper($q))."' OR m_gedrec $term '".$DBCONN->escape(str2lower($q))."')";
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND m_gedfile='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "m_gedfile='".$DBCONN->escape($allgeds[$i])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	$res =& dbquery($sql);
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()){
			$row = db_cleanup($row);
			$sqlmm = "select mm_gid as mm_gid from ".$TBLPREFIX."media_mapping where mm_media = '".$row['m_media']."' and mm_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
			$resmm =& dbquery($sqlmm);
			while ($rowmm =& $resmm->fetchRow()) {
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
function thumbnail_file($filename, $generateThumb=true, $overwrite=false) {
	global $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $AUTO_GENERATE_THUMBS, $MEDIA_DIRECTORY_LEVELS;
	global $MEDIA_EXTERNAL;
	
	if (strlen($filename) == 0) return false;
	if (!isset($generateThumb)) $generateThumb = true;
	if (!isset($overwrite)) $overwrite = false;
	
	// NOTE: Lets get the file details
	if (strstr($filename, "://")) return $filename;
	$filename = check_media_depth($filename, "NOTRUNC");
	$parts = pathinfo($filename);
	$mainDir = $parts["dirname"]."/";
	$thumbDir = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $mainDir);
	$thumbName = $parts["basename"];
	if (isset($parts["extension"])) $thumbExt = strtolower($parts["extension"]);
	else $thumbExt = "";
	
	if (!$generateThumb) return $thumbDir.$thumbName;
	
	if (!$overwrite && file_exists(filename_decode($thumbDir.$thumbName))) return $thumbDir.$thumbName;

	if ($AUTO_GENERATE_THUMBS) {
		if (generate_thumbnail($mainDir.$thumbName, $thumbDir.$thumbName)) {
			return $thumbDir.$thumbName;
		}
	}
	
	// Thumbnail doesn't exist and could not be generated:
	//		Return an icon image instead
	switch ($thumbExt) {
		case "pdf":
			$which = "pdf";
			break;
		case "doc":
		case "txt":
			$which = "doc";
			break;
		case "ged":
			$which = "ged";
			break;
		default:
			$which = "large";
	}
	return $PGV_IMAGE_DIR."/".$PGV_IMAGES["media"][$which];
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
function check_media_depth($filename, $truncate="FRONT", $noise="VERBOSE") {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $MEDIA_EXTERNAL;
	global $pgv_lang;
	
	if (empty($filename) || ($MEDIA_EXTERNAL && stristr($filename, "://"))) return $filename;

	if (empty($truncate) || ($truncate!="NOTRUNC" && $truncate!="BACK" && $truncate!="FRONT")) $truncate = "FRONT";
	if ($truncate=="NOTRUNC") $truncate = "FRONT";	// **** temporary over-ride *****
	
	if (empty($noise) || ($noise!="VERBOSE" && $noise!="QUIET")) $noise = "VERBOSE";
			
	// NOTE: Check media depth
	$parts = pathinfo($filename);
	//print_r($parts); print "<br />";
	if (empty($parts["dirname"]) || ($MEDIA_DIRECTORY_LEVELS==0 && $truncate!="NOTRUNC")) return $MEDIA_DIRECTORY.$parts["basename"];

	$fileName = $parts["basename"];

	if (empty($parts["dirname"])) $folderName = $MEDIA_DIRECTORY;
	else $folderName = $parts["dirname"]."/";

	$folderName = trim($folderName);
	$folderName = str_replace(array("\\", "//"), "/", $folderName);
	if (substr($folderName,0,1) == "/") $folderName = substr($folderName,1);
	if (substr($folderName,0,2) == "./") $folderName = substr($folderName,2);
	if (substr($folderName,0,strlen($MEDIA_DIRECTORY))==$MEDIA_DIRECTORY) $folderName = substr($folderName,strlen($MEDIA_DIRECTORY));
	$folderName = str_replace("../", "", $folderName);
	if (substr($folderName,0,7) == "thumbs/") $folderName = substr($folderName,7);
	if (substr($folderName,0,4) == "CVS/") $folderName = substr($folderName,4);

	if ($folderName=="") return $MEDIA_DIRECTORY.$fileName;
	$folderList = explode("/", $folderName);
	$folderCount = count($folderList) - 1;
	$folderDepth = min($folderCount, $MEDIA_DIRECTORY_LEVELS);
	if ($truncate=="NOTRUNC") $folderDepth = $folderCount;
	
	if ($truncate=="BACK") {
		$nStart = 0;
		$nEnd = min($folderCount, $folderDepth);
	} else {
		$nStart = max(0, ($folderCount-$folderDepth));
		$nEnd = $folderCount;
	}
	
	// Check for, and skip, device name used as the first folder name
	if (substr($folderList[$nStart], -1)==":") {
		$nStart++;
		if ($nStart>$nEnd) return $MEDIA_DIRECTORY.$fileName;
	} 
	// Now check for, and skip, "./" at the beginning of the folder list
	if ($folderList[$nStart]==".") {
		$nStart++;
		if ($nStart>$nEnd) return $MEDIA_DIRECTORY.$fileName;
	} 

	$folderName = "";
	$backPointer = "../../";
	// Check existing folder structure, and create as necessary
	$n = $nStart;
	while($n<$nEnd) {
		$folderName .= $folderList[$n];
		if (!is_dir(filename_decode($MEDIA_DIRECTORY.$folderName))) {
			if (!mkdir(filename_decode($MEDIA_DIRECTORY.$folderName))) {
				if ($noise=="VERBOSE") {
					print "<div class=\"error\">".$pgv_lang["folder_no_create"].$MEDIA_DIRECTORY.$folderName."</div>";
				}
			} else {
				if ($noise=="VERBOSE") {
					print $pgv_lang["folder_created"].": ".$MEDIA_DIRECTORY.$folderName."/<br />";
				}
				$fp = @fopen(filename_decode($MEDIA_DIRECTORY.$folderName."/index.php"),"w+");
				if (!$fp) {
					if ($noise=="VERBOSE") {
						print "<div class=\"error\">".$pgv_lang["security_no_create"].$MEDIA_DIRECTORY.$folderName."</div>";
					}
			 	} else {
					fwrite($fp, "<?php\r\n");
					fwrite($fp, "header(\"Location: ".$backPointer."medialist.php\");\r\n");
					fwrite($fp, "exit;\r\n");
					fwrite($fp, "?>\r\n");
					fclose($fp);
				}
			}
		}
		if (!is_dir(filename_decode($MEDIA_DIRECTORY."thumbs/".$folderName))) {
			if (!mkdir(filename_decode($MEDIA_DIRECTORY."thumbs/".$folderName))) {
				if ($noise=="VERBOSE") {
					print "<div class=\"error\">".$pgv_lang["folder_no_create"].$MEDIA_DIRECTORY."thumbs/".$folderName."</div>";
				}
			} else {
				if ($noise=="VERBOSE") {
					print $pgv_lang["folder_created"].": ".$MEDIA_DIRECTORY."thumbs/".$folderName."/<br />";
				}
				$fp = @fopen(filename_decode($MEDIA_DIRECTORY."thumbs/".$folderName."/index.php"),"w+");
				if (!$fp) {
					if ($noise=="VERBOSE") {
						print "<div class=\"error\">".$pgv_lang["security_no_create"].$MEDIA_DIRECTORY."thumbs/".$folderName."</div>";
					}
				} else {
					fwrite($fp, "<?php\r\n");
					fwrite($fp, "header(\"Location: ".$backPointer."../medialist.php\");\r\n");
					fwrite($fp, "exit;\r\n");
					fwrite($fp, "?>\r\n");
					fclose($fp);
				}
			}
		}
		$folderName .= "/";
		$backPointer .= "../";
		$n ++;
	}

	return $MEDIA_DIRECTORY.$folderName.$fileName;
}

function retrieve_media_object($gedrec, $gid) {
	$gedreclines = preg_split("/[\r\n]+/", $gedrec);   // -- find the number of lines in the individuals record
	$linecount = count($gedreclines);
	$factrec = "";	 // -- complete fact record
	$line = "";   // -- temporary line buffer
	$itemcounter = 0; // -- item counter per pid
	$objectline = 0; // -- linenumber where the object record starts
	$itemsfound = array(); // -- arryay for storing the found items
	// NOTE: Get the media record
	for($linecounter=1; $linecounter<=$linecount; $linecounter++) {
		if ($linecounter<$linecount) $line = $gedreclines[$linecounter];
		else $line=" ";
		if (empty($line)) $line=" ";
		if (preg_match("/[0-9]\sOBJE/", $line) > 0) $objectline = $linecounter;
		// Level 1 media
		if (($linecounter==$linecount)||($line{0}==1)) {
			$ft = preg_match_all("/[1|2]\s(\w+)(.*)/", $factrec, $match, PREG_SET_ORDER);
			if ($ft>0) {
				foreach ($match as $key => $hit) {
					if (!stristr($hit[0], "@") && stristr($hit[1], "OBJE")) {
						$key = $hit[0]{0};
						$fact = get_sub_record($key, "OBJE", $factrec);
						$itemsfound[$gid][$itemcounter] = array(($objectline), $key, $fact);
						$itemcounter++;
					}
				}
			}
			$factrec = $line;
		}
		else $factrec .= "\n".$line;
	}
	return $itemsfound;
}

/**
 * get the list of current folders in the media directory
 * @return array
 */
function get_media_folders() {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS;
	
	$folderList = array();
	$folderList[0] = $MEDIA_DIRECTORY;
	if ($MEDIA_DIRECTORY_LEVELS==0) return $folderList;
	
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
				if (!$entry) break;
				if (is_dir($currentFolder.$entry."/")) {
					// Weed out some folders we're not interested in
					if ($entry!="." && $entry!=".." && $entry!="CVS") {
						if ($currentFolder.$entry."/" != $MEDIA_DIRECTORY."thumbs/") {
							$folderList[$nextFolderNum] = $currentFolder.$entry."/";
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
function show_media_form($pid, $action="newentry", $filename="", $linktoid="", $level=1, $line=0) {
	global $GEDCOM, $pgv_lang, $TEXT_DIRECTION, $MEDIA_ID_PREFIX, $GEDCOMS, $WORD_WRAPPED_NOTES;
	global $pgv_changes, $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;
	global $AUTO_GENERATE_THUMBS;
	
	// NOTE: add a table and form to easily add new values to the table
	print "<form method=\"post\" name=\"newmedia\" action=\"addmedia.php\" enctype=\"multipart/form-data\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$action\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	if (!empty($linktoid)) print "<input type=\"hidden\" name=\"linktoid\" value=\"$linktoid\" />\n";
	print "<input type=\"hidden\" name=\"level\" value=\"$level\" />\n";
	print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
	print "<tr><td class=\"topbottombar\" colspan=\"2\">";
	if ($action=="newentry") print $pgv_lang["add_media"];
	else print $pgv_lang["edit_media"];
	print "</td></tr>";
	print "<tr><td><input type=\"submit\" value=\"".$pgv_lang["save"]."\" /></td></tr>";
	if ($linktoid=="new" || ($linktoid=="" && $action!="update")) {
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("add_media_linkid", "qm");
		print $pgv_lang["add_fav_enter_id"]."</td>";
		print "<td class=\"optionbox wrap\"><input type=\"text\" name=\"gid\" id=\"gid\" size=\"6\" value=\"\" />";
		print_findindi_link("gid","");
		print_findfamily_link("gid");
		print_findsource_link("gid");
		print "<br /><sub>".$pgv_lang["add_linkid_advice"]."</sub></td></tr>";
	}
	if (isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_record_in_file($pid);
	else if (id_type($pid) == "OBJE") $gedrec = find_media_record($pid);
	else $gedrec = "";
	
	// 0 OBJE
	// 1 FILE
	if ($gedrec == "") {
		$gedfile = "FILE";
		if ($filename != "") $gedfile = "FILE ".$filename;
 	} else {
//		$gedfile = get_sub_record(1, "FILE", $gedrec);
		$gedfile = get_first_tag(1, "FILE", $gedrec);
		if (empty($gedfile)) $gedfile = "FILE";
	}
	if ($gedfile!="FILE") {
		$gedfile = "FILE ".check_media_depth(substr($gedfile, 5));
		$readOnly = "READONLY";
	} else {
		$readOnly = "";
	}
	if ($gedfile == "FILE") {
		// Box for user to choose to upload file from local computer
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("upload_media_file_help", "qm");
		print $pgv_lang["media_file"]."</td><td class=\"optionbox wrap\"><input type=\"file\" name=\"mediafile\"";
		//print " onchange=\"updateFormat(this.value);\"";
		print " size=\"40\"><br /><sub>".$pgv_lang["use_browse_advice"]."</sub></td></tr>";
		// Check for thumbnail generation support
		$ThumbSupport = "";
		if (function_exists("imagecreatefromjpeg") and function_exists("imagejpeg")) $ThumbSupport .= ", JPG";
		if (function_exists("imagecreatefromgif") and function_exists("imagegif")) $ThumbSupport .= ", GIF";
		if (function_exists("imagecreatefrompng") and function_exists("imagepng")) $ThumbSupport .= ", PNG";
		if (!$AUTO_GENERATE_THUMBS) $ThumbSupport = "";
		
		if ($ThumbSupport != "") {
			$ThumbSupport = substr($ThumbSupport, 2);	// Trim off first ", "
			print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
			print_help_link("generate_thumb_help", "qm","generate_thumbnail");
			print $pgv_lang["auto_thumbnail"];
			print "</td><td class=\"optionbox wrap\">";
			print "<input type=\"checkbox\" name=\"genthumb\" value=\"yes\" checked />";
			print "&nbsp;&nbsp;&nbsp;".$pgv_lang["generate_thumbnail"].$ThumbSupport;
			print "</td></tr>";
		}
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("upload_thumbnail_file_help", "qm");
		print $pgv_lang["thumbnail"]."</td><td class=\"optionbox wrap\"><input type=\"file\" name=\"thumbnail\" size=\"40\"><br /><sub>".$pgv_lang["use_browse_advice"]."</sub></td></tr>";
	}
	// File name on server
	$isExternal = strstr($gedfile, "://");
	if ($gedfile=="FILE") {
		add_simple_tag("1 $gedfile", "", $pgv_lang["server_file"], "", "NOCLOSE");
		print "<br /><sub>".$pgv_lang["server_file_advice"];
		print "<br />".$pgv_lang["server_file_advice2"]."</sub></td></tr>";
		$fileName = "";
		$folder = "";
	} else {
		if ($isExternal) {
			$fileName = substr($gedfile,5);
			$folder = "";
		} else {
			$parts = pathinfo(substr($gedfile,5));
			$fileName = $parts["basename"];
			$folder = $parts["dirname"]."/";
		}
		print "<tr>";
			print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
			print_help_link("upload_server_file_help","qm", "upload_media");
			print $pgv_lang["server_file"];
			print "</td>";
			print "<td class=\"optionbox wrap $TEXT_DIRECTION wrap\">";
			print "<input name=\"filename\" type=\"text\" value=\"".addslashes($fileName)."\" size=\"40\"";
			if ($isExternal) print " />";
			else print " /><br /><sub>".$pgv_lang["server_file_advice"]."</sub>";
			print "</td>";
		print "</tr>";
	}
	print "<input name=\"oldFilename\" type=\"hidden\" value=\"".addslashes($fileName)."\" />";

	// Box for user to choose the folder to store the image
	if (!$isExternal && $MEDIA_DIRECTORY_LEVELS > 0) {
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
		print_help_link("upload_server_folder_help", "qm");
		if (empty($folder)) {
			if (!empty($_SESSION['upload_folder'])) $folder = $_SESSION['upload_folder'];
			else $folder = $MEDIA_DIRECTORY;
		}
		print $pgv_lang["server_folder"]."</td><td class=\"optionbox wrap\">";
		$folders = get_media_folders();
		print "<span dir=\"ltr\"><select name=\"folder_list\" onchange=\"document.newmedia.folder.value=this.options[this.selectedIndex].value;\">\n";
		foreach($folders as $f) {
			print "<option value=\"$f\"";
			if ($folder==$f) print " selected=\"selected\"";
			print ">$f</option>\n";
		}
		if (userGedcomAdmin(getUserName())) print "<option value=\"other\">".$pgv_lang["add_media_other_folder"]."</option>\n";
		print "</select></span>\n";
		if (userGedcomAdmin(getUserName())) print "<span dir=\"ltr\"><input type=\"text\" name=\"folder\" size=\"30\" value=\"".$folder."\"></span>";
		if ($gedfile=="FILE") {
			print "<br /><sub>".$pgv_lang["server_folder_advice2"]."</sub></td></tr>";
		}
		print "</td></tr>";
	}
	if ($isExternal || $MEDIA_DIRECTORY_LEVELS==0) print "<input name=\"folder\" type=\"hidden\" value=\"\" />";
	print "<input name=\"oldFolder\" type=\"hidden\" value=\"".addslashes($folder)."\" />";
	// 2 FORM
	if ($gedrec == "") $gedform = "FORM";
	else {
		$gedform = get_first_tag(2, "FORM", $gedrec);
		if (empty($gedform)) $gedform = "FORM";
	}
	$formid = add_simple_tag("2 $gedform");
	
	// 3 TYPE
	if ($gedrec == "") $gedtype = "TYPE";
	else {
		$temp = str_replace("\r\n", "\n", $gedrec)."\n";
		$types = preg_match("/3 TYPE(.*)\n/", $temp, $matches);
		if (empty($matches[0])) $gedtype = "TYPE";
		else $gedtype = "TYPE ".trim($matches[1]);
	}
	add_simple_tag("3 $gedtype");

	// 2 TITL
	if ($gedrec == "") $gedtitl = "TITL";
	else {
		$gedtitl = get_first_tag(2, "TITL", $gedrec);
		if (empty($gedtitl)) $gedtitl = "TITL";
	}
	add_simple_tag("2 $gedtitl");

	// 3 _HEB
	if ($gedrec == "") $gedtitl = "_HEB";
	else {
		$gedtitl = get_first_tag(3, "_HEB", $gedrec);
		if (empty($gedtitl)) $gedtitl = "_HEB";
	}
	add_simple_tag("3 $gedtitl");

	// 3 ROMN
	if ($gedrec == "") $gedtitl = "ROMN";
	else {
		$gedtitl = get_first_tag(3, "ROMN", $gedrec);
		if (empty($gedtitl)) $gedtitl = "ROMN";
	}
	add_simple_tag("3 $gedtitl");

	// 2 _PRIM
	if ($gedrec == "") $gedprim = "_PRIM";
	else {
//		$gedprim = get_sub_record(1, "_PRIM", $gedrec);
		$gedprim = get_first_tag(1, "_PRIM", $gedrec);
		if (empty($gedprim)) $gedprim = "_PRIM";
	}
	add_simple_tag("1 $gedprim");
	// 2 _THUM
	if ($gedrec == "") $gedthum = "_THUM";
	else {
//		$gedthum = get_sub_record(1, "_THUM", $gedrec);
		$gedthum = get_first_tag(1, "_THUM", $gedrec);
		if (empty($gedthum)) $gedthum = "_THUM";
	}
	add_simple_tag("1 $gedthum");
	
	//-- print out editing fields for any other data in the media record
	if (!empty($gedrec)) {
		$subrecs = get_all_subrecords($gedrec, "FILE,FORM,TYPE,TITL,_PRIM,_THUM,CHAN");
		foreach($subrecs as $ind=>$subrec) {
			$inSource = false;
			$pieces = explode("\r\n", $subrec);
			foreach($pieces as $piece) {
				$ft = preg_match("/(\d) (\w+)(.*)/", $piece, $match);
				if ($ft>0) {
					$subLevel = $match[1];
					$fact = trim($match[2]);
					$event = trim($match[3]);
					$event .= str_replace("<br />", "", get_cont(($subLevel+1), $subrec));
					if ($fact=="SOUR") {
						$sourceLevel = $subLevel;
						$inSource = true;
						$havePAGE = false;
						$haveTEXT = false;
						$haveDATE = false;
						$haveQUAY = false;
					}
					if ($fact=="PAGE") $havePAGE = true;
					if ($fact=="TEXT") {
						if (!$havePAGE) {
							add_simple_tag(($sourceLevel+1)." PAGE");
							$havePAGE = true;
						}
						$haveTEXT = true;
					}
					if ($fact=="DATE") {
						if (!$havePAGE) {
							add_simple_tag(($sourceLevel+1)." PAGE");
							$havePAGE = true;
						}
					}
					if ($fact=="DATE") $haveDATE = true;
					if ($fact=="QUAY") $haveQUAY = true;
				} else {
					$fact="";
					$event="";
				}
				if (!empty($fact) && $fact!="CONC" && $fact!="CONT"&& $fact!="DATA") {
					$subrecord = $subLevel." ".$fact." ".$event;
					if ($inSource && $fact=="DATE") add_simple_tag($subrecord, "", $pgv_lang["date_of_entry"]);
					else add_simple_tag($subrecord);
				}
			}
			if ($inSource) {
				if (!$havePAGE) add_simple_tag(($sourceLevel+1)." PAGE");
				if (!$haveTEXT) add_simple_tag(($sourceLevel+2)." TEXT");
				if (!$haveDATE) add_simple_tag(($sourceLevel+2)." DATE", "", $pgv_lang["date_of_entry"]);
				if (!$haveQUAY) add_simple_tag(($sourceLevel+1)." QUAY");
			}
		}
	}
	print "</table>\n";
?>
		<script language="JavaScript" type="text/javascript">
		<!--
			var formid = '$formid';
			function updateFormat(filename) {
				ext = filename.substr(filename.lastIndexOf(".")+1);
				formfield = document.getElementById(formid);
				formfield.value = ext;
			} 
		//-->
		</script>
<?php
	print_add_layer("SOUR", 1);
	print_add_layer("NOTE", 1);
	print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
	print "</form>\n";
}

function get_media_links($m_media) {
	global $DBCONN, $TBLPREFIX, $GEDCOMS, $GEDCOM;
	
	$sql = "SELECT mm_gid FROM ".$TBLPREFIX."media_mapping WHERE mm_media='".$DBCONN->escape($m_media)."' AND mm_gedfile='".$GEDCOMS[$GEDCOM]['id']."'";
	$res = dbquery($sql);
	$links = array();
	while($row =& $res->fetchRow()) {
		$links[] = $row['mm_gid'];
	}
	$res->free();
	return $links;
}

function findImageSize($file) {
	if (strtolower(substr($file,0,7))=="http://") $file = "http://".rawurlencode(substr($file,7));
	else $file = filename_decode($file);
	$imgsize = @getimagesize($file);
	if (!$imgsize) {
		$imgsize[0] = 300;
		$imgsize[1] = 300;
	}
	return $imgsize;
}

/**
 * Print the list of persons, families, and sources that are mentioned in
 * the "LINKS" array of the current item from the Media list.
 *
 * This function is called from media.php, medialist.php, and random_media.php
 */

function PrintMediaLinks($links, $size="small") {;
	global $TEXT_DIRECTION, $pgv_lang;
	
	if (count($links) == 0) return false;
	
	if ($size!="small") $size = "normal";

	$linkList = array();
	
	foreach($links as $id=>$type) {
		$linkItem = array();
		
		$linkItem["id"] = $id;
		$linkItem["type"] = $type;
		$linkItem["name"] = "";
		if ($type=="INDI" && displayDetailsByID($id)) {
			$linkItem["name"] = "A".get_sortable_name($id);
			$linkItem["printName"] = get_person_name($id);
		}
		else if ($type=="FAM" && displayDetailsByID($id, "FAM")) {
			$linkItem["name"] = "B".get_sortable_family_descriptor($id);
			$linkItem["printName"] = get_family_descriptor($id);
		}
		else if ($type=="SOUR" && displayDetailsByID($id, "SOUR")) {
			$linkItem["printName"] = get_source_descriptor($id);
			$linkItem["name"] = "C".$linkItem["printName"];
		}
		
		if ($linkItem["name"]!="") $linkList[] = $linkItem;
	}
	uasort($linkList, "mediasort");
	
	$firstLink = true;
	$firstIndi = true;
	$firstFam = true;
	$firstSour = true;
	$firstObje = true;
	if ($size=="small") print "<sub>";
	foreach ($linkList as $linkItem) {
		if ($linkItem["type"]=="INDI") {
			if ($firstIndi && !$firstLink) print "<br />";
			$firstLink = false;
			$firstIndi = false;
			print "<br /><a href=\"individual.php?pid=".$linkItem["id"]."\">";
			if (begRTLText($linkItem["printName"]) && $TEXT_DIRECTION=="ltr") {
				print $pgv_lang["view_person"]." -- ";
				print "(".$linkItem["id"].")&nbsp;&nbsp;";
				print PrintReady($linkItem["printName"]);
			} else {
				print $pgv_lang["view_person"]." -- ";
				print PrintReady($linkItem["printName"])."&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$linkItem["id"].")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			}
			print "</a>";
		}
		if ($linkItem["type"]=="FAM") {
			if ($firstFam && !$firstLink) print "<br />";
			$firstLink = false;
			$firstFam = false;
	   		print "<br /><a href=\"family.php?famid=".$linkItem["id"]."\">";
			if (begRTLText($linkItem["printName"]) && $TEXT_DIRECTION=="ltr") {
				print $pgv_lang["view_family"]." -- ";
				print "(".$linkItem["id"].")&nbsp;&nbsp;";
				print PrintReady($linkItem["printName"]);
			} else {
				print $pgv_lang["view_family"]." -- ";
				print PrintReady($linkItem["printName"])."&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$linkItem["id"].")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			}
			print "</a>";
		}
		if ($linkItem["type"]=="SOUR") {
			if ($firstSour && !$firstLink) print "<br />";
			$firstLink = false;
			$firstSour = false;
			print "<br /><a href=\"source.php?sid=".$linkItem["id"]."\">";
			if (begRTLText($linkItem["printName"]) && $TEXT_DIRECTION=="ltr") {
				print $pgv_lang["view_source"]." -- ";
				print "(".$linkItem["id"].")&nbsp;&nbsp;";
				print PrintReady($linkItem["printName"]);
			} else {
				print $pgv_lang["view_source"]." -- ";
				print PrintReady($linkItem["printName"])."&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
				print "(".$linkItem["id"].")";
				if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			}
			print "</a>";
		}
	}
	if ($size=="small") print "</sub>";
	return true;
}

?>
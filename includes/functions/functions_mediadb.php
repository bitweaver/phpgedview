<?php

/**
* Various functions used by the media DB interface
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
* @version $Id$
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_MEDIADB_PHP', '');

//-- Setup array of media types
$MEDIATYPE = array("a11","acb","adc","adf","afm","ai","aiff","aif","amg","anm","ans","apd","asf","au","avi","awm","bga","bmp","bob","bpt","bw","cal","cel","cdr","cgm","cmp","cmv","cmx","cpi","cur","cut","cvs","cwk","dcs","dib","dmf","dng","doc","dsm","dxf","dwg","emf","enc","eps","fac","fax","fit","fla","flc","fli","fpx","ftk","ged","gif","gmf","hdf","iax","ica","icb","ico","idw","iff","img","jbg","jbig","jfif","jpe","jpeg","jp2","jpg","jtf","jtp","lwf","mac","mid","midi","miff","mki","mmm",".mod","mov","mp2","mp3","mpg","mpt","msk","msp","mus","mvi","nap","ogg","pal","pbm","pcc","pcd","pcf","pct","pcx","pdd","pdf","pfr","pgm","pic","pict","pk","pm3","pm4","pm5","png","ppm","ppt","ps","psd","psp","pxr","qt","qxd","ras","rgb","rgba","rif","rip","rla","rle","rpf","rtf","scr","sdc","sdd","sdw","sgi","sid","sng","swf","tga","tiff","tif","txt","text","tub","ul","vda","vis","vob","vpg","vst","wav","wdb","win","wk1","wks","wmf","wmv","wpd","wxf","wp4","wp5","wp6","wpg","wpp","xbm","xls","xpm","xwd","yuv","zgm");
$BADMEDIA = array(".","..","CVS","thumbs","index.php","MediaInfo.txt", ".cvsignore", ".svn", "watermark");

/*
****************************
* general functions
****************************/

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
				$inddata = "<?php\nheader(\"Location: ../medialist.php\");\nexit;\n?>";
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
* - $media["ID"]          the unique id of this media item in the table (Mxxxx)
* - $media["XREF"]        Another copy of the Media ID (not sure why there are two)
* - $media["GEDFILE"]     the gedcom file the media item should be added to
* - $media["FILE"]        the filename of the media item
* - $media["EXISTS"]      whether the file exists.  0=no, 1=external, 2=std dir, 3=protected dir
* - $media["THUMB"]       the filename of the thumbnail
* - $media["THUMBEXISTS"] whether the thumbnail exists.  0=no, 1=external, 2=std dir, 3=protected dir
* - $media["FORM"]        the format of the item (ie bmp, gif, jpeg, pcx etc)
* - $media["TYPE"]        the type of media item (ie certificate, document, photo, tombstone etc)
* - $media["TITL"]        a title for the item, used for list display
* - $media["GEDCOM"]      gedcom record snippet
* - $media["LEVEL"]       level number (normally zero)
* - $media["LINKED"]      Flag for front end to indicate this is linked
* - $media["LINKS"]       Array of gedcom ids that this is linked to
* - $media["CHANGE"]      Indicates the type of change waiting admin approval
*
* @param boolean $random If $random is true then the function will return 5 random pictures.
* @return mixed A media list array.
*/

function get_medialist($currentdir = false, $directory = "", $linkonly = false, $random = false, $includeExternal = true) {
	global $MEDIA_DIRECTORY_LEVELS, $BADMEDIA, $thumbdir, $TBLPREFIX, $MEDIATYPE;
	global $level, $dirs, $ALLOW_CHANGE_GEDCOM, $GEDCOM, $GEDCOMS, $MEDIA_DIRECTORY;
	global $MEDIA_EXTERNAL, $pgv_changes, $USE_MEDIA_FIREWALL, $gBitDb;

	// Retrieve the gedcoms to search in
	$sgeds = array ();
	if (($ALLOW_CHANGE_GEDCOM) && (count($GEDCOMS) > 1)) {
		foreach ($GEDCOMS as $key => $ged) {
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
	if (empty($directory))
		$directory = $MEDIA_DIRECTORY;
	$myDir = str_replace($MEDIA_DIRECTORY, "", $directory);
	if ($random) {
		$rows =
			$gBitDb->getAll(
				"SELECT m_id, m_file, m_media, m_gedrec, m_titl, m_gedfile FROM {$TBLPREFIX}media WHERE m_gedfile=? ORDER BY ".$gBitDb->random()
				, array(PGV_GED_ID));
	} else {
		$rows=
			$gBitDb->getAll(
				"SELECT m_id, m_file, m_media, m_gedrec, m_titl, m_gedfile FROM {$TBLPREFIX}media WHERE m_gedfile=? AND (m_file LIKE ? OR m_file LIKE ?) ORDER BY m_id desc"
				, array(PGV_GED_ID, "%{$myDir}%", "%://%"));
	}
	$mediaObjects = array ();

	// Build the raw medialist array,
	// but weed out any folders we're not interested in
	foreach ($rows as $row) {
		$fileName = check_media_depth($row->m_file, "NOTRUNC", "QUIET");
		$isExternal = isFileExternal($fileName);
		if ( $isExternal && (!$MEDIA_EXTERNAL || !$includeExternal) ) {
			continue;
		}
		if ($isExternal || !$currentdir || $directory == dirname($fileName) . "/") {
			$media = array ();
			$media["ID"] = $row->m_id;
			$media["XREF"] = $row->m_media;
			$media["GEDFILE"] = $row->m_gedfile;
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
			$media["TITL"] = $row->m_titl;
			$media["GEDCOM"] = $row->m_gedrec;
			$media["LEVEL"] = '0';
			$media["LINKED"] = false;
			$media["LINKS"] = array ();
			$media["CHANGE"] = "";
			// Extract Format and Type from GEDCOM record
			$media["FORM"] = strtolower(get_gedcom_value("FORM", 2, $row->m_gedrec));
			$media["TYPE"] = strtolower(get_gedcom_value("FORM:TYPE", 2, $row->m_gedrec));

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

	// Look for new Media objects in the list of changes pending approval
	// At the same time, accumulate a list of GEDCOM IDs that have changes pending approval
	$changedRecords = array ();
	foreach ($pgv_changes as $changes) {
		foreach ($changes as $change) {
			while (true) {
				if ($change["gedcom"] != $GEDCOM || $change["status"] != "submitted")
					break;

				$gedrec = $change['undo'];
				if (empty($gedrec))
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
				$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $GEDCOMS[$GEDCOM]["id"];
				if (isset ($medialist[$keyMediaList])) {
					$medialist[$keyMediaList]["CHANGE"] = $change["type"];
					break;
				}

				// Build the entry for this new Media object
				$media = array ();
				$media["ID"] = $change["gid"];
				$media["XREF"] = $change["gid"];
				$media["GEDFILE"] = PGV_GED_ID;
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
				if (empty($media["FILE"]))
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

	foreach ($medialist as $key=>$media) {
		foreach (fetch_linked_indi($media["XREF"], 'OBJE', PGV_GED_ID) as $indi) {
			$medialist[$key]["LINKS"][$indi->getXref()]='INDI';
			$medialist[$key]["LINKED"]=true;
		}
		foreach (fetch_linked_fam($media["XREF"], 'OBJE', PGV_GED_ID) as $fam) {
			$medialist[$key]["LINKS"][$fam->getXref()]='FAM';
			$medialist[$key]["LINKED"]=true;
		}
		foreach (fetch_linked_sour($media["XREF"], 'OBJE', PGV_GED_ID) as $sour) {
			$medialist[$key]["LINKS"][$sour->getXref()]='SOUR';
			$medialist[$key]["LINKED"]=true;
		}
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
					$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $GEDCOMS[$GEDCOM]["id"];

					// Add this GEDCOM ID to the link list of the media object
					if (isset ($medialist[$keyMediaList])) {
						$medialist[$keyMediaList]["LINKS"][$pid] = gedcom_record_type($pid, get_id_from_gedcom($GEDCOM));
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
* @param array $media An item from the Media list produced by get_medialist()
* @param string $filter The filter to be looked for within various elements of the $media array
* @param string $acceptExt "http" if links to external media should be considered too
* @return bool false if the Media item doesn't match the filter criteria
*/
function filterMedia($media, $filter, $acceptExt) {

	if (empty($filter) || strlen($filter) < 2)
		$filter = "";
	if (empty($acceptExt) || $acceptExt != "http")
		$acceptExt = "";

	//-- Check Privacy first.  No point in proceeding if Privacy says "don't show"
	$links = $media["LINKS"];
	if (count($links) != 0) {
		foreach ($links as $id => $type) {
			if (!displayDetailsById($id, $type)) {
				return false;
			}
		}
	}

	//-- Accept when filter string contained in Media item's id
	if ($media["XREF"] == $filter) {
		return true;
	}

	//-- Accept external Media only if specifically told to do so
	if (isFileExternal($media["FILE"]) && $acceptExt != "http")
		return false;

	//-- Accept everything if filter string is empty
	if ($filter == "")
		return true;

	$filter=UTF8_strtoupper($filter);

	//-- Accept when filter string contained in file name (but only for editing users)
	if (PGV_USER_CAN_EDIT && strstr(UTF8_strtoupper(basename($media["FILE"])), $filter))
		return true;

	//-- Accept when filter string contained in Media item's title
	$record=Media::getInstance($media['XREF']);
	if ($record) {
		foreach ($record->getAllNames() as $name) {
			if (strpos(UTF8_strtoupper($name['full']), $filter)!==false) {
				return true;
			}
		}
	}

	if (strpos(UTF8_strtoupper($media["TITL"]), $filter)!==false)
		return true;

	//-- Accept when filter string contained in name of any item
	//-- this Media item is linked to.  (Privacy already checked)
	foreach ($links as $id=>$type) {
		$record=GedcomRecord::getInstance($id);
		foreach ($record->getAllNames() as $name) {
			if (strpos(UTF8_strtoupper($name['full']), $filter)!==false) {
				return true;
			}
		}
	}

	return false;
}
/**
* Generates the thumbnail filename and path
*
* The full file path is taken and turned into the location of the thumbnail file.
*
* @author roland-d
* @param string $filename The full filename of the media item
* @param bool $generateThumb 'true' when thumbnail should be generated, 'false' when only the file name should be returned
* @param bool $overwrite 'true' to replace existing thumbnail
* @return string the location of the thumbnail
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

	$parts = pathinfo_utf($filename);
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
	// Return an icon image instead
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
* @author roland-d
* @param string $filename The filename that needs to be checked for media depth
* @param string $truncate Controls which part of folder structure to truncate when
*  number of folders exceeds $MEDIA_DIRECTORY_LEVELS
*  "NOTRUNC": Don't truncate
*  "BACK":  Truncate at end, keeping front part
*  "FRONT": Truncate at front, keeping back part
* @param string $noise  Controls the amount of chatting done by this function
*  "VERBOSE" Print messages
*  "QUIET"  Don't print messages
* @return  string A filename validated for the media depth
*
* NOTE: The "NOTRUNC" option is required so that media that were inserted into the
*  database before $MEDIA_DIRECTORY_LEVELS was reduced will display properly.
* NOTE: The "QUIET" option is used during GEDCOM import, where we really don't need
*  to know about every Media folder that's being created.
*/
function check_media_depth($filename, $truncate = "FRONT", $noise = "VERBOSE") {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $MEDIA_EXTERNAL;
	global $pgv_lang;

	if (empty($filename) || ($MEDIA_EXTERNAL && isFileExternal($filename)))
		return $filename;

	if (empty($truncate) || ($truncate != "NOTRUNC" && $truncate != "BACK" && $truncate != "FRONT"))
		$truncate = "FRONT";
	if ($truncate == "NOTRUNC")
		$truncate = "FRONT"; // **** temporary over-ride *****

	if (strpos($_SERVER["SCRIPT_NAME"],"mediafirewall") > -1) {
		// no extraneous output while displaying images
		$noise = "QUIET";
	}

	if (empty($noise) || ($noise != "VERBOSE" && $noise != "QUIET"))
		$noise = "VERBOSE";

	// NOTE: Check media depth
	$parts = pathinfo_utf($filename);
	//print_r($parts); print "<br />";
	if (empty($parts["dirname"]) || ($MEDIA_DIRECTORY_LEVELS == 0 && $truncate != "NOTRUNC"))
		return $MEDIA_DIRECTORY . $parts["basename"];

	$fileName = $parts["basename"];

	if (empty($parts["dirname"]))
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
					fwrite($fp, "header(\"Location: {$backPointer}medialist.php\");\r\n");
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
					fwrite($fp, "header(\"Location: {$backPointer}../medialist.php\");\r\n");
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
* process the form for uploading media files
*/
function process_uploadMedia_form() {
	global $pgv_lang, $TEXT_DIRECTION;
	global $MEDIA_DIRECTORY, $USE_MEDIA_FIREWALL, $MEDIA_FIREWALL_THUMBS, $MEDIATYPE;
	global $thumbnail, $whichFile1, $whichFile2;

	print "<table class=\"list_table $TEXT_DIRECTION width100\">";
	print "<tr><td class=\"messagebox wrap\">";
	for($i=1; $i<6; $i++) {
		if (!empty($_FILES['mediafile'.$i]["name"]) || !empty($_FILES['thumbnail'.$i]["name"])) {
			$folderName = trim(trim(safe_POST('folder'.$i, PGV_REGEX_NOSCRIPT)), '/');
			// Validate and correct folder names
			$folderName = check_media_depth($folderName."/y.z", "BACK");
			$folderName = dirname($folderName)."/";
			$thumbFolderName = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folderName);

			$_SESSION["upload_folder"] = $folderName; // store standard media folder in session

			$destFolder = $folderName;  // This is where the actual image will be stored
			$destThumbFolder = $thumbFolderName;  // ditto for the thumbnail

			if ($USE_MEDIA_FIREWALL) {
				$destFolder = get_media_firewall_path($folderName);
				if ($MEDIA_FIREWALL_THUMBS) $destThumbFolder = get_media_firewall_path($thumbFolderName);
			}

			// make sure the dirs exist
			@mkdirs($folderName);
			@mkdirs($destFolder);
			@mkdirs($thumbFolderName);
			@mkdirs($destThumbFolder);

			$error = "";

			// Determine file name on server
			$fileName = trim(trim(safe_POST('filename'.$i, PGV_REGEX_NOSCRIPT)), '/');
			$parts = pathinfo_utf($fileName);
			if (!empty($parts["basename"])) {
				// User supplied a name to be used on the server
				$mediaFile = $parts["basename"]; // Use the supplied name
				if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
					// Strip invalid extension from supplied name
					$lastDot = strrpos($mediaFile, '.');
					if ($lastDot !== false) $mediaFile = substr($mediaFile, 0, $lastDot);
					// Use extension of original uploaded file name
					if (!empty($_FILES["mediafile".$i]["name"])) $parts = pathinfo_utf($_FILES["mediafile".$i]["name"]);
					else $parts = pathinfo_utf($_FILES["thumbnail".$i]["name"]);
					if (!empty($parts["extension"])) $mediaFile .= ".".$parts["extension"];
				}
			} else {
				// User did not specify a name to be used on the server:  use the original uploaded file name
				if (!empty($_FILES["mediafile".$i]["name"])) $parts = pathinfo_utf($_FILES["mediafile".$i]["name"]);
				else $parts = pathinfo_utf($_FILES["thumbnail".$i]["name"]);
				$mediaFile = $parts["basename"];
			}

			if (!empty($_FILES["mediafile".$i]["name"])) {
				// Copy main media file into the destination directory
				if (!move_uploaded_file($_FILES["mediafile".$i]["tmp_name"], filename_decode($destFolder.$mediaFile))) {
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".file_upload_error_text($_FILES["mediafile".$i]["error"])."<br />";
				} else {
					@chmod(filename_decode($destFolder.$mediaFile), PGV_PERM_FILE);
					AddToLog("Media file {$folderName}{$mediaFile} uploaded");
				}
			}
			if ($error=="" && !empty($_FILES["thumbnail".$i]["name"])) {
				// Copy user-supplied thumbnail file into the destination directory
				if (!move_uploaded_file($_FILES["thumbnail".$i]["tmp_name"], filename_decode($destThumbFolder.$mediaFile))) {
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".file_upload_error_text($_FILES["thumbnail".$i]["error"])."<br />";
				} else {
					@chmod(filename_decode($destThumbFolder.$mediaFile), PGV_PERM_FILE);
					AddToLog("Media file {$thumbFolderName}{$mediaFile} uploaded");
				}
			}
			if ($error=="" && empty($_FILES["mediafile".$i]["name"]) && !empty($_FILES["thumbnail".$i]["name"])) {
				// Copy user-supplied thumbnail file into the main destination directory
				if (!copy(filename_decode($destThumbFolder.$mediaFile), filename_decode($destFolder.$mediaFile))) {
					// the file cannot be copied
					$error .= $pgv_lang["upload_error"]."<br />".file_upload_error_text($_FILES["thumbnail".$i]["error"])."<br />";
				} else {
					@chmod(filename_decode($folderName.$mediaFile), PGV_PERM_FILE);
					AddToLog("Media file {$folderName}{$mediaFile} copied from {$thumbFolderName}{$mediaFile}");
				}
			}
			if ($error=="" && !empty($_FILES["mediafile".$i]["name"]) && empty($_FILES["thumbnail".$i]["name"])) {
				if (safe_POST('genthumb'.$i, 'yes', 'no') == 'yes') {
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
			if (!empty($error)) echo '<span class="error">', $error, "</span><br />\n";
			// No errors found then tell the user all is successful
			else {
				print $pgv_lang["upload_successful"]."<br /><br />";
				$imgsize = findImageSize($folderName.$mediaFile);
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;
				print "<a href=\"#\" onclick=\"return openImage('".encode_url($folderName.$mediaFile)."',$imgwidth, $imgheight);\">".$mediaFile."</a>";
				print"<br /><br />";
			}
		}
	}
	print "</td></tr></table>";
}

/**
* print a form for uploading media files
* @param string $URL  the URL the input form is to execute when the "Submit" button is pressed
* @param bool   $showthumb the setting of the "show thumbnail" option (required by media.php)
*/
function show_mediaUpload_form($URL='media.php', $showthumb=false) {
	global $AUTO_GENERATE_THUMBS, $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;
	global $pgv_lang, $TEXT_DIRECTION;

	$mediaFolders = get_media_folders();

	// Check for thumbnail generation support
	$thumbSupport = "";
	if ($AUTO_GENERATE_THUMBS) {
/*  "wbmp" is NOT "Windows BMP" -- it's "Wireless BMP", a simple B&W bit mapped format
		if (function_exists("imagecreatefromwbmp") && function_exists("imagewbmp")) $thumbSupport .= ", BMP";
*/
		if (function_exists("imagecreatefromgif") && function_exists("imagegif")) $thumbSupport .= ", GIF";
		if (function_exists("imagecreatefromjpeg") && function_exists("imagejpeg")) $thumbSupport .= ", JPG";
		if (function_exists("imagecreatefrompng") && function_exists("imagepng")) $thumbSupport .= ", PNG";
	}
	if ($thumbSupport != '') $thumbSupport = substr($thumbSupport, 2); // Trim off first ", "

	// Determine file size limit
	$filesize = ini_get('upload_max_filesize');
	if (empty($filesize)) $filesize = "2M";

	// Print the form
	echo '<form name="uploadmedia" enctype="multipart/form-data" method="post" action="', encode_url($URL), '">';
	echo '<input type="hidden" name="action" value="upload" />';
	echo '<input type="hidden" name="showthumb" value="', $showthumb, '" />';
	echo '<table class="list_table ', $TEXT_DIRECTION, ' width100">';
	echo '<tr><td class="topbottombar" colspan="2">';
		echo $pgv_lang["upload_media"], '<br />', $pgv_lang["max_upload_size"], $filesize;
	echo '</td></tr>';
	$tab = 1;
	// Print the Submit button for uploading the media
	echo '<tr><td class="topbottombar" colspan="2">';
		echo '<input type="submit" value="', $pgv_lang["upload"], '" tabindex="', $tab++, '" />';
	echo '</td></tr>';

	// Print 5 forms for uploading images
	for($i=1; $i<6; $i++) {
		echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, ' wrap width25">';
			print_help_link("upload_media_file_help","qm", "upload_media");
			echo $pgv_lang["media_file"];
			echo '</td>';
			echo '<td class="optionbox ', $TEXT_DIRECTION, ' wrap">';
			echo '<input name="mediafile', $i, '" type="file" size="40" tabindex="', $tab++, '" />';
			if ($i==1) echo '<br /><sub>', $pgv_lang["use_browse_advice"], '</sub>';
		echo '</td></tr>';

		if ($thumbSupport != "") {
			echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, ' wrap width25">';
				print_help_link("generate_thumb_help", "qm","generate_thumbnail");
				echo $pgv_lang["auto_thumbnail"];
				echo '</td><td class="optionbox ', $TEXT_DIRECTION, ' wrap">';
				echo '<input type="checkbox" name="genthumb', $i, '" value="yes" checked="checked" tabindex="', $tab++, '" />';
				echo '&nbsp;&nbsp;&nbsp;', $pgv_lang["generate_thumbnail"], $thumbSupport;
			echo '</td></tr>';
		}

		echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, ' wrap width25">';
			print_help_link("upload_thumbnail_file_help","qm", "upload_media");
			echo $pgv_lang["thumbnail"];
			echo '</td>';
			echo '<td class="optionbox ', $TEXT_DIRECTION, ' wrap">';
			echo '<input name="thumbnail', $i, '" type="file" tabindex="', $tab++, '" size="40" />';
			if ($i==1) echo '<br /><sub>', $pgv_lang["use_browse_advice"], '</sub>';
		echo '</td></tr>';

		if (PGV_USER_GEDCOM_ADMIN) {
			echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, ' wrap width25">';
				print_help_link("upload_server_file_help","qm", "upload_media");
				echo $pgv_lang["server_file"];
				echo '</td>';
				echo '<td class="optionbox ', $TEXT_DIRECTION, ' wrap">';
				echo '<input name="filename', $i, '" type="text" tabindex="', $tab++, '" size="40" />';
				if ($i==1) echo "<br /><sub>".$pgv_lang["server_file_advice"]."</sub>";
			echo '</td></tr>';
		} else {
			echo '<input type="hidden" name="filename', $i, '" value="" />';
		}

		if (PGV_USER_GEDCOM_ADMIN && $MEDIA_DIRECTORY_LEVELS>0) {
			echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, ' wrap width25">';
				print_help_link("upload_server_folder_help","qm", "upload_media");
				echo $pgv_lang["server_folder"];
				echo '</td>';
				echo '<td class="optionbox ', $TEXT_DIRECTION, ' wrap">';

				echo '<span dir="ltr"><select name="folder_list', $i, '" onchange="document.uploadmedia.folder', $i, '.value=this.options[this.selectedIndex].value;">', "\n";
				echo '<option';
				echo ' value="/"> ', $pgv_lang["choose"], ' </option>';
				if (PGV_USER_IS_ADMIN) echo '<option value="other" disabled>', $pgv_lang["add_media_other_folder"], "</option>\n";
				foreach ($mediaFolders as $f) {
					if (!strpos($f, ".svn")) {    //Do not print subversion directories
						// Strip $MEDIA_DIRECTORY from the folder name
						if (substr($f,0,strlen($MEDIA_DIRECTORY)) == $MEDIA_DIRECTORY) $f = substr($f, strlen($MEDIA_DIRECTORY));
						if ($f == '') $f = '/';
						echo '<option value="', $f, '"';
						echo '>', $f, "</option>\n";
					}
				}
				print "</select></span>\n";
				if (PGV_USER_IS_ADMIN) {
					echo '<br /><span dir="ltr"><input name="folder', $i, '" type="text" size="40" value="" tabindex="', $tab++, '" onblur="checkpath(this)" /></span>';
					if ($i==1) echo '<br /><sub>', print_text("server_folder_advice",0,1), '</sub>';
				} else echo '<input name="folder', $i, '" type="hidden" value="" />';
			echo '</td></tr>';
		} else {
			echo '<input name="folder', $i, '" type="hidden" value="" />';
		}

		if ($i!=5) {
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
		}
	}

	// Print the Submit button for uploading the media
	echo '<tr><td class="topbottombar" colspan="2">';
		echo '<input type="submit" value="', $pgv_lang["upload"], '" tabindex="', $tab++, '" />';
	echo '</td></tr>';

	echo '</table></form>';
}


/**
* print a form for editing or adding media items
* @param string $pid  the id of the media item to edit
* @param string $action the action to take after the form is posted
* @param string $filename allows you to provide a filename to go in the FILE tag for new media items
* @param string $linktoid the id of the person/family/source to link a new media item to
* @param int    $level  The level at which this media item should be added
* @param int    $line  The line number in the GEDCOM record where this media item belongs
*/
function show_media_form($pid, $action = "newentry", $filename = "", $linktoid = "", $level = 1, $line = 0) {
	global $GEDCOM, $pgv_lang, $factarray, $TEXT_DIRECTION, $GEDCOMS, $WORD_WRAPPED_NOTES, $ADVANCED_NAME_FACTS;
	global $pgv_changes, $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;
	global $AUTO_GENERATE_THUMBS, $THUMBNAIL_WIDTH;

	// NOTE: add a table and form to easily add new values to the table
	print "<form method=\"post\" name=\"newmedia\" action=\"addmedia.php\" enctype=\"multipart/form-data\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$action\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	if (!empty($linktoid)) print "<input type=\"hidden\" name=\"linktoid\" value=\"$linktoid\" />\n";
	print "<input type=\"hidden\" name=\"level\" value=\"$level\" />\n";
	print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
	print "<tr><td class=\"topbottombar\" colspan=\"2\">";
	if ($action == "newentry") {
		echo $pgv_lang["add_media"];
	} else {
		echo print_text('edit_media',0 , 1);
	}
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
		if (gedcom_record_type($pid, get_id_from_gedcom($GEDCOM)) == "OBJE")
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
		//  $gedfile = get_sub_record(1, "FILE", $gedrec);
		$gedfile = get_first_tag(1, "FILE", $gedrec);
		if (empty($gedfile))
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
		print " size=\"40\" /><br /><sub>" . $pgv_lang["use_browse_advice"] . "</sub></td></tr>";
		// Check for thumbnail generation support
		if (PGV_USER_GEDCOM_ADMIN) {
			$ThumbSupport = "";
		// Check for thumbnail generation support
			$thumbSupport = "";
			if ($AUTO_GENERATE_THUMBS) {
/*    "wbmp" is NOT "Windows BMP" -- it's "Wireless BMP", a simple B&W bit mapped format
				if (function_exists("imagecreatefromwbmp") && function_exists("imagewbmp")) $thumbSupport .= ", WBMP";
*/
				if (function_exists("imagecreatefromgif") && function_exists("imagegif")) $thumbSupport .= ", GIF";
				if (function_exists("imagecreatefromjpeg") && function_exists("imagejpeg")) $thumbSupport .= ", JPG";
				if (function_exists("imagecreatefrompng") && function_exists("imagepng")) $thumbSupport .= ", PNG";
			}

			if ($thumbSupport != "") {
				$thumbSupport = substr($thumbSupport, 2); // Trim off first ", "
				print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
				print_help_link("generate_thumb_help", "qm", "generate_thumbnail");
				print $pgv_lang["auto_thumbnail"];
				print "</td><td class=\"optionbox wrap\">";
				print "<input type=\"checkbox\" name=\"genthumb\" value=\"yes\" checked=\"checked\" />";
				print "&nbsp;&nbsp;&nbsp;" . $pgv_lang["generate_thumbnail"] . $thumbSupport;
				print "</td></tr>";
			}
			print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
			print_help_link("upload_thumbnail_file_help", "qm");
			print $pgv_lang["thumbnail"] . "</td><td class=\"optionbox wrap\"><input type=\"file\" name=\"thumbnail\" size=\"40\" /><br /><sub>" . $pgv_lang["use_browse_advice"] . "</sub></td></tr>";
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
			$parts = pathinfo_utf(substr($gedfile, 5));
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
			print "<input name=\"filename\" type=\"text\" value=\"" . htmlentities($fileName,ENT_COMPAT,'UTF-8') . "\" size=\"40\"";
			if ($isExternal)
				print " />";
			else
				print " /><br /><sub>" . $pgv_lang["server_file_advice"] . "</sub>";
		} else {
/*   $thumbnail = thumbnail_file($fileName, true, false, $pid);
			if (!empty($thumbnail)) {
				print "<img src=\"".$thumbnail."\" border=\"0\" align=\"" . ($TEXT_DIRECTION== "rtl"?"right": "left") . "\" class=\"thumbnail\"";
				if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
				print " alt=\"\" title=\"\" />";
			} */
			print $fileName;
			print "<input name=\"filename\" type=\"hidden\" value=\"" . htmlentities($fileName,ENT_COMPAT,'UTF-8') . "\" size=\"40\" />";
		}
		print "</td>";
		print "</tr>\n";

	}

	// Box for user to choose the folder to store the image
	if (!$isExternal && $MEDIA_DIRECTORY_LEVELS > 0) {
		echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, 'wrap width25">';
		print_help_link("upload_server_folder_help", "qm");
		if (empty($folder)) {
			if (!empty($_SESSION['upload_folder'])) $folder = $_SESSION['upload_folder'];
			else $folder = '';
		}
		// Strip $MEDIA_DIRECTORY from the folder name
		if (substr($folder,0,strlen($MEDIA_DIRECTORY)) == $MEDIA_DIRECTORY) $folder = substr($folder, strlen($MEDIA_DIRECTORY));
		echo $pgv_lang["server_folder"], '</td><td class="optionbox wrap">';
		//-- don't let regular users change the location of media items
		if ($action!='update' || PGV_USER_GEDCOM_ADMIN) {
			$mediaFolders = get_media_folders();
			echo '<span dir="ltr"><select name="folder_list" onchange="document.newmedia.folder.value=this.options[this.selectedIndex].value;">', "\n";
			echo '<option';
			if ($folder == '/') echo ' selected="selected"';
			echo ' value="/"> ', $pgv_lang["choose"], ' </option>';
			if (PGV_USER_IS_ADMIN) echo '<option value="other" disabled>', $pgv_lang["add_media_other_folder"], "</option>\n";
			foreach ($mediaFolders as $f) {
				if (!strpos($f, ".svn")) {    //Do not print subversion directories
					// Strip $MEDIA_DIRECTORY from the folder name
					if (substr($f,0,strlen($MEDIA_DIRECTORY)) == $MEDIA_DIRECTORY) $f = substr($f, strlen($MEDIA_DIRECTORY));
					if ($f == '') $f = '/';
					echo '<option value="', $f, '"';
					if ($folder == $f && $f != '/')
						echo ' selected="selected"';
					echo '>', $f, "</option>\n";
				}
			}
			print "</select></span>\n";
		}
		else echo $folder;
		echo '<input name="oldFolder" type="hidden" value="', addslashes($folder), '" />';
		if (PGV_USER_IS_ADMIN) {
			echo '<br /><span dir="ltr"><input type="text" name="folder" size="40" value="', $folder, '" onblur="checkpath(this)" /></span>';
			if ($MEDIA_DIRECTORY_LEVELS>0) {
				echo '<br /><sub>', print_text("server_folder_advice",0,1), '</sub>';
			}
			if ($gedfile == "FILE") {
				echo '<br /><sub>', $pgv_lang["server_folder_advice2"], '</sub>';
			}
		} else echo '<input name="folder" type="hidden" value="', addslashes($folder), '" />';
		echo '</td></tr>';
	} else {
		echo '<input name="oldFolder" type="hidden" value="" />';
		echo '<input name="folder" type="hidden" value="" />';
	}
	// 2 FORM
	if ($gedrec == "")
		$gedform = "FORM";
	else {
		$gedform = get_first_tag(2, "FORM", $gedrec);
		if (empty($gedform))
			$gedform = "FORM";
	}
	$formid = add_simple_tag("2 $gedform");

	// 3 TYPE
	if ($gedrec == "")
		$gedtype = "TYPE photo";		// default to "Photo" unless told otherwise
	else {
		$temp = str_replace("\r\n", "\n", $gedrec) . "\n";
		$types = preg_match("/3 TYPE(.*)\n/", $temp, $matches);
		if (empty($matches[0]))
			$gedtype = "TYPE photo";	// default to "Photo" unless told otherwise
		else
			$gedtype = "TYPE " . trim($matches[1]);
	}
	add_simple_tag("3 $gedtype");

	// 2 TITL
	if ($gedrec == "")
		$gedtitl = "TITL";
	else {
		$gedtitl = get_first_tag(2, "TITL", $gedrec);
		if (empty($gedtitl))
			$gedtitl = get_first_tag(1, "TITL", $gedrec);
		if (empty($gedtitl))
			$gedtitl = "TITL";
	}
	add_simple_tag("2 $gedtitl");
	
	if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) {
		// 3 _HEB
		if ($gedrec == "")
			$gedtitl = "_HEB";
		else {
			$gedtitl = get_first_tag(3, "_HEB", $gedrec);
			if (empty($gedtitl))
				$gedtitl = "_HEB";
		}
		add_simple_tag("3 $gedtitl");
	}

	if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) {
		// 3 ROMN
		if ($gedrec == "")
			$gedtitl = "ROMN";
		else {
			$gedtitl = get_first_tag(3, "ROMN", $gedrec);
			if (empty($gedtitl))
				$gedtitl = "ROMN";
		}
		add_simple_tag("3 $gedtitl");
	}

	//-- don't show _PRIM option to regular users
//	if (PGV_USER_GEDCOM_ADMIN) {
		// 2 _PRIM
		if ($gedrec == "")
			$gedprim = "_PRIM";
		else {
			//  $gedprim = get_sub_record(1, "_PRIM", $gedrec);
			$gedprim = get_first_tag(1, "_PRIM", $gedrec);
			if (empty($gedprim))
				$gedprim = "_PRIM";
		}
		add_simple_tag("1 $gedprim");
//	}

	//-- don't show _THUM option to regular users
//	if (PGV_USER_GEDCOM_ADMIN) {
		// 2 _THUM
		if ($gedrec == "")
			$gedthum = "_THUM N";
		else {
			//  $gedthum = get_sub_record(1, "_THUM", $gedrec);
			$gedthum = get_first_tag(1, "_THUM", $gedrec);
			if (empty($gedthum))
				$gedthum = "_THUM N";
		}
		add_simple_tag("1 $gedthum");
//	}

	//-- print out editing fields for any other data in the media record
	$sourceSOUR = "";
	if (!empty($gedrec)) {
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
					add_simple_tag(($sourceLevel+2) ." DATE ". $sourceDATE, "", $factarray["DATA:DATE"]);
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
				if (!empty($fact) && $fact != "CONC" && $fact != "CONT" && $fact != "DATA") {
					add_simple_tag($subLevel ." ". $fact ." ". $event);
				}
			}
		}

		if ($sourceSOUR!="") {
			// Get rid of all saved Source data
			add_simple_tag($sourceLevel ." SOUR ". $sourceSOUR);
			add_simple_tag(($sourceLevel+1) ." PAGE ". $sourcePAGE);
			add_simple_tag(($sourceLevel+2) ." TEXT ". $sourceTEXT);
			add_simple_tag(($sourceLevel+2) ." DATE ". $sourceDATE, "", $factarray["DATA:DATE"]);
			add_simple_tag(($sourceLevel+1) ." QUAY ". $sourceQUAY);
		}
	}
	if (PGV_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		print_help_link("no_update_CHAN_help", "qm");
		echo $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
		echo "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
		echo $pgv_lang["no_update_CHAN"]."<br />\n";
		$event = new Event(get_sub_record(1, "1 CHAN", $gedrec));
		echo format_fact_date($event, false, true);
		echo "</td></tr>\n";
	}
	print "</table>\n";
?>
		<script language="JavaScript" type="text/javascript">
			var formid = '<?php print $formid; ?>';
			function updateFormat(filename) {
				var extsearch=/\.([a-zA-Z]{3,4})$/;
				ext='';
				if (extsearch.exec(filename)) {
					ext = RegExp.$1.toLowerCase();
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
	print_add_layer("SHARED_NOTE", 1);
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
		$record=GedcomRecord::getInstance($id);
		if ($record && $record->canDisplaydetails()) {
			switch ($record->getType()) {
			case 'INDI':
				$linkItem = array ();
				$linkItem['name']='A'.$record->getSortName();
				$linkItem['record']=$record;
				$linkList[] = $linkItem;
				break;
			case 'FAM':
				$linkItem = array ();
				$linkItem['name']='B'.$record->getSortName();
				$linkItem['record']=$record;
				$linkList[] = $linkItem;
				break;
			case 'SOUR':
				$linkItem = array ();
				$linkItem['name']='C'.$record->getSortName();
				$linkItem['record']=$record;
				$linkList[] = $linkItem;
				break;

			}
		}
	}
	uasort($linkList, "mediasort");

	$firstLink = true;
	$firstIndi = true;
	$firstFam = true;
	$firstSour = true;
	$firstObje = true;
	if ($size == "small")
		print "<sub>";
	$prev_record=null;
	foreach ($linkList as $linkItem) {
		$record=$linkItem['record'];
		if ($prev_record && $prev_record->getType()!=$record->getType()) {
			echo '<br />';
		}
		echo '<br /><a href="', encode_url($record->getLinkUrl()), '">';
		switch ($record->getType()) {
		case 'INDI':
			echo $pgv_lang['view_person'];
			break;
		case 'FAM':
			echo $pgv_lang['view_family'];
			break;
		case 'SOUR':
			echo $pgv_lang['view_source'];
			break;
		}
		echo ' -- ';
		$name=$record->getFullname();
		if (begRTLText($name) && $TEXT_DIRECTION == 'ltr') {
			echo '('.$record->getXref().')&nbsp;&nbsp;';
			echo PrintReady($name);
		} else {
			echo PrintReady($name).'&nbsp;&nbsp;';
			if ($TEXT_DIRECTION=='rtl') {
				echo getRLM();
			}
			echo "(" . $record->getXref().')';
			if ($TEXT_DIRECTION=='rtl') {
				echo getRLM();
			}
		}
		echo '</a>';
		$prev_record=$record;
	}

	if ($size == "small")
		print "</sub>";
	return true;
}

function get_media_id_from_file($filename){
	global $TBLPREFIX, $gBitDb;
	return
		$gBitDb->getOne(
			"SELECT m_media FROM {$TBLPREFIX}media WHERE m_file LIKE ?"
			, array("%{$filename}"));
}
//returns an array of rows from the database containing the Person ID's for the people associated with this picture
function get_media_relations($mid){
	global $GEDCOMS, $GEDCOM, $medialist;

	//-- check in the medialist cache first
	$firstChar = substr($mid, 0, 1);
	$restChar = substr($mid, 1);
	if (is_numeric($firstChar)) {
		$firstChar = "";
		$restChar = $mid;
	}
	$keyMediaList = $firstChar . substr("000000" . $restChar, -6) . "_" . $GEDCOMS[$GEDCOM]['id'];
	if (isset ($medialist[$keyMediaList]['LINKS'])) {
		return $medialist[$keyMediaList]['LINKS'];
	}

	$media = array();
		foreach (fetch_linked_indi($mid, 'OBJE', PGV_GED_ID) as $indi) {
			if ($mid!=$indi->getXref()) {
				$media[$indi->getXref()]='INDI';
			}
		}
		foreach (fetch_linked_fam($mid, 'OBJE', PGV_GED_ID) as $fam) {
			if ($mid!=$fam->getXref()) {
				$media[$fam->getXref()]='FAM';
			}
		}
		foreach (fetch_linked_sour($mid, 'OBJE', PGV_GED_ID) as $sour) {
			if ($mid!=$sour->getXref()) {
				$media[$sour->getXref()]='SOUR';
			}
		}
	$medialist[$keyMediaList]['LINKS'] = $media;
	return $media;
}

// clips a media item based on data from the gedcom
function picture_clip($person_id, $image_id, $filename, $thumbDir)
{
	global $TBLPREFIX, $gBitDb;
	// This gets the gedrec
	$gedrec= $gBitDb->getOne(
		"SELECT m_gedrec FROM {$TBLPREFIX}media WHERE m_media=? AND m_gedfile=?"
		, array($image_id, PGV_GED_ID));

	//Get the location of the file, and then make a location for the clipped image

	//store values to the variables
	$top = get_gedcom_value("_TOP", 2, $gedrec);
	$bottom = get_gedcom_value("_BOTTOM", 2, $gedrec);
	$left = get_gedcom_value("_LEFT", 2, $gedrec);
	$right = get_gedcom_value("_RIGHT", 2, $gedrec);
	//check to see if all values were retrived
	if ($top != null || $bottom != null || $left != null || $right != null)
	{
		$image_filename = check_media_depth($filename);
		$image_dest = $thumbDir.$person_id."_".$image_filename[count($image_filename)-1].".jpg";
		//call the cropimage function
		cropImage($filename, $image_dest, $left, $top, $right, $bottom); //removed offset 50
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
	switch ($ims['mime']) {
	case 'image/png':
		if (!function_exists('imagecreatefrompng') || !function_exists('imagepng')) break;
		$img = imagecreatetruecolor(($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		$org_img = imagecreatefrompng($image);
		$ims = @getimagesize($image);
		imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		imagepng($img,$dest_image);
		imagedestroy($img);
		break;
	case 'image/jpeg':
		if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) break;
		$img = imagecreatetruecolor($width, $height);
		$org_img = imagecreatefromjpeg($image);
		$ims = @getimagesize($image);
		imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		imagejpeg($img,$dest_image,90);
		imagedestroy($img);
		break;
	case 'image/gif':
		if (!function_exists('imagecreatefromgif') || !function_exists('imagegif')) break;
		$img = imagecreatetruecolor(($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		$org_img = imagecreatefromgif($image);
		$ims = @getimagesize($image);
		imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		imagegif($img,$dest_image);
		imagedestroy($img);
		break;
/*  "wbmp" is NOT "Windows BMP" -- it's "Wireless BMP", a simple B&W bit mapped format
		if (!function_exists('imagecreatefromwbmp') || !function_exists('imagewbmp')) break;
		$img = imagecreatetruecolor(($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		$org_img = imagecreatefromwbmp($image);
		$ims = @getimagesize($image);
		imagecopyresampled($img,$org_img, 0, 0, $left, $top, $width, $height, ($ims[0]-$right)-$left,($ims[1]-$bottom)-$top);
		imagewbmp($img,$dest_image);
		imagedestroy($img);
		break;
*/
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
function mkdirs($dir, $mode = PGV_PERM_EXE, $recursive = true) {
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
	$supportByGD = array('jpg'=>'jpeg', 'jpeg'=>'jpeg', 'gif'=>'gif', 'png'=>'png');
	$reqtype = strtolower($reqtype);

	if (empty($supportByGD[$reqtype])) return false;
	$type = $supportByGD[$reqtype];

	if (function_exists('imagecreatefrom'.$type) && function_exists('image'.$type)) return $type;
	// Here we could check for image types that are supported by other than the GD library
	return false;
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
	$memoryUsed=memory_get_usage();

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
	if (!is_writable($MEDIA_DIRECTORY."thumbs")) return false;

	// Can we generate thumbnails for this file type?
	$parts = pathinfo_utf($filename);
	if (isset($parts["extension"])) $ext = strtolower($parts["extension"]);
	else $ext = "";
	$type = isImageTypeSupported($ext);
	if (!$type) return false;

	if (!isFileExternal($filename)) {
		// internal
		if ($USE_MEDIA_FIREWALL) {
			// Look for the original file in either possible location
			if (!file_exists(filename_decode($filename))) {
				$filename = get_media_firewall_path($filename);
			}
			if ($MEDIA_FIREWALL_THUMBS) {
				// Look for the thumbnail in either possible location (so we can overwrite it)
				if (!file_exists(filename_decode($thumbnail))) {
					$thumbnail = get_media_firewall_path($thumbnail);
				}
			}
			// Ensure the directory exists
			if (!is_dir(dirname($thumbnail))) {
				if (!mkdirs(dirname($thumbnail))) {
					return false;
				}
			}
		}
		if (!file_exists(filename_decode($filename))) return false;  // Can't thumbnail a non-existent image
		$imgsize = getimagesize(filename_decode($filename));
		if (!$imgsize) return false;  // Can't thumbnail an image of unknown size

		//-- check if file is small enough to be its own thumbnail
		if (($imgsize[0]<150)&&($imgsize[1]<150)) {
			@copy($filename, $thumbnail);
			return true;
		}
	} else {
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

<?php
/**
 * Index caching functions
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * @subpackage Display
 * @version $Id: index_cache.php,v 1.2 2007/06/09 21:11:04 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * load a cached block from a file
 * @param array $block	[0]:name of the block to load, [1]:block's configuration
 * @param int $index	An id for this block in the case of multiple instances of the same block on the page
 * @return boolean  returns false if the block could not be loaded from cache
 */
function loadCachedBlock($block, $index) {
	global $PGV_BLOCKS, $INDEX_DIRECTORY, $DEBUG, $lang_short_cut, $LANGUAGE, $GEDCOM;
	
	//-- ignore caching when DEBUG is set
	if (isset($DEBUG) && $DEBUG==true) return false;

	//-- ignore cache when its life is not configured or when its life is zero
	$cacheLife = 0;
	if (isset($block[1]['cache'])) $cacheLife = $block[1]['cache'];
	else {
		if (isset($PGV_BLOCKS[$block[0]]['config']['cache'])) $cacheLife = $PGV_BLOCKS[$block[0]]['config']['cache'];
	}
	if ($cacheLife==0) return false;

	//-- ignore caching for logged in users 
	$uname = getUserName();
	if (!empty($uname)) return false;

	//-- check for cache file
	if (!file_exists($INDEX_DIRECTORY."/cache")) {
		mkdir($INDEX_DIRECTORY."/cache");
		return false;
	}
	if (!file_exists($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE])) {
		mkdir($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]);
		return false;
	}
	if (!file_exists($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]."/".$GEDCOM)) {
		mkdir($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]."/".$GEDCOM);
		return false;
	}
	$fname = $INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]."/".$GEDCOM."/".$index."_".$block[0];
	if (file_exists($fname)) {
		// Check for expired cache (<0: no expiry), 0: immediate, >0: expires in x days)  Zero already checked
		if ($cacheLife > 0) {
			$modtime = filemtime($fname);
			//-- time should start at the beginning of the day
			$modtime = $modtime - (date("G",$modtime)*60*60 + date("i",$modtime)*60 + date("s",$modtime));
			$checktime = ($cacheLife*24*60*60);
			$modtime = $modtime+$checktime;
			if ($modtime<time()) return false;
		}		
		return @readfile($fname);
	}
	return false;
}

/**
 * Save a block's content to the cache file
 * @param array $block	[0]:name of the block to save, [1]:block's configuration
 * @param int $index	An id for this block in the case of multiple instances of the same block on the page
 * @param string $content	the actual content to save in the cache
 * @return boolean  returns false if the block could not be loaded from cache
 */
function saveCachedBlock($block, $index, $content) {
	global $PGV_BLOCKS, $INDEX_DIRECTORY, $DEBUG, $lang_short_cut, $LANGUAGE, $GEDCOM;
	
	//-- ignore caching when DEBUG is set
	if (isset($DEBUG) && $DEBUG==true) return false;

	//-- ignore cache when its life is not configured or when its life is zero
	$cacheLife = 0;
	if (isset($block[1]['cache'])) $cacheLife = $block[1]['cache'];
	else if (isset($PGV_BLOCKS[$block[0]]['config']['cache'])) $cacheLife = $PGV_BLOCKS[$block[0]]['config']['cache'];
	if ($cacheLife==0) return false;

	//-- ignore caching for logged in users 
	$uname = getUserName();
	if (!empty($uname)) return false;

	//-- check for cache file
	if (!file_exists($INDEX_DIRECTORY."/cache")) {
		mkdir($INDEX_DIRECTORY."/cache");
	}
	if (!file_exists($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE])) {
		mkdir($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]);
	}
	if (!file_exists($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]."/".$GEDCOM)) {
		mkdir($INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]."/".$GEDCOM);
	}
	$fname = $INDEX_DIRECTORY."/cache/".$lang_short_cut[$LANGUAGE]."/".$GEDCOM."/".$index."_".$block[0];
	$fp = fopen($fname, "wb");
	fwrite($fp, $content);
	fclose($fp);
	return true;
}

/**
 * clears the cache files
 */
function clearCache() {
	global $PGV_BLOCKS, $INDEX_DIRECTORY, $DEBUG, $lang_short_cut, $LANGUAGE, $GEDCOM;
	
	foreach($lang_short_cut as $key=>$value) {
		$fname = $INDEX_DIRECTORY."/cache/".$value."/".$GEDCOM;
		if (file_exists($fname)) {
			$dir = dir($fname);
			while (false !== ($entry = $dir->read())) {
			   if ($entry!="." && $entry!="..") unlink($fname."/".$entry);
			}
		}
	}
	
	if (file_exists($INDEX_DIRECTORY."/".$GEDCOM."_upcoming.php")) {
		unlink($INDEX_DIRECTORY."/".$GEDCOM."_upcoming.php");
	}
}
?>

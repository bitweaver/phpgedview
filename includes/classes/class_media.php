<?php
/**
 * Class that defines a media object
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Charts
 * @version $Id: class_media.php,v 1.4 2009/09/15 20:06:00 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_class_MEDIA_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_gedcomrecord.php');

class Media extends GedcomRecord {
	var $title         =null;
	var $file          =null;
	var $ext           ='';
	var $mime          ='';
	var $note          =null;
	var $filesizeraw   =-1;
	var $width         =0;
	var $height        =0;
	var $serverfilename='';
	var $fileexists    =false;
	var $filepropset   =false;

	// Create a Media object from either raw GEDCOM data or a database row
	function Media($data) {
		if (is_array($data)) {
			// Construct from a row from the database
			$this->title=$data['m_titl'];
			$this->file =$data['m_file'];
		} else {
			// Construct from raw GEDCOM data
			$this->title = get_gedcom_value('TITL', 1, $data);
			if (empty($this->title)) {
				$this->title = get_gedcom_value('TITL', 2, $data);
			}
			$this->file = get_gedcom_value('FILE', 1, $data);
		}
		if (empty($this->title)) $this->title = $this->file;

		parent::GedcomRecord($data);
	}

	// Get an instance of a Media object  We either specify
	// an XREF (in the current gedcom), or we can provide a row
	// from the database (if we anticipate the record hasn't
	// been fetched previously).
	static function &getInstance($data, $simple=true) {
		global $gedcom_record_cache, $GEDCOM, $pgv_changes;

		if (is_array($data)) {
			$ged_id=$data['ged_id'];
			$pid   =$data['xref'];
		} else {
			$ged_id=get_id_from_gedcom($GEDCOM);
			$pid   =$data;
		}

		// Check the cache first
		if (isset($gedcom_record_cache[$pid][$ged_id])) {
			return $gedcom_record_cache[$pid][$ged_id];
		}

		// Look for the record in the database
		if (!is_array($data)) {
			$data=fetch_media_record($pid, $ged_id);

			// If we didn't find the record in the database, it may be remote
			if (!$data && strpos($pid, ':')) {
				list($servid, $remoteid)=explode(':', $pid);
				$service=ServiceClient::getInstance($servid);
				if ($service) {
					// TYPE will be replaced with the type from the remote record
					$data=$service->mergeGedcomRecord($remoteid, "0 @{$pid}@ TYPE\n1 RFN {$pid}", false);
				}
			}

			// If we didn't find the record in the database, it may be new/pending
			if (!$data && PGV_USER_CAN_EDIT && isset($pgv_changes[$pid.'_'.$GEDCOM])) {
				$data=find_updated_record($pid);
				$fromfile=true;
			}

			// If we still didn't find it, it doesn't exist
			if (!$data) {
				return null;
			}
		}

		// Create the object
		$object=new Media($data, $simple);
		if (!empty($fromfile)) {
			$object->setChanged(true);
		}

		// Store it in the cache
		$gedcom_record_cache[$object->xref][$object->ged_id]=&$object;
		//-- also store it using its reference id (sid:pid and local gedcom for remote links)
		$gedcom_record_cache[$pid][$ged_id]=&$object;
		return $object;
	}

	/**
	 * get the media note
	 * @return string
	 */
	function getNote(){
		if (is_null($this->note)) {
			$this->note=get_gedcom_value('NOTE', 1, $this->getGedcomRecord());
		}
		return $this->note;
	}

	/**
	 * get the thumbnail filename
	 * @return string
	 */
	function getThumbnail($generateThumb = true) {
		return thumbnail_file($this->file,$generateThumb);
	}

	/**
	 * get the media file name
	 * @return string
	 */
	function getFilename() {
		return $this->file;
	}

	/**
	 * get the relative file path of the image on the server
	 * @return string
	 */
	function getLocalFilename() {
		return check_media_depth($this->file);
	}

	/**
	 * get the file name on the server
	 * @return string
	 */
	function getServerFilename() {
		global $USE_MEDIA_FIREWALL;
		if ($this->serverfilename) return $this->serverfilename;
		$localfilename = $this->getLocalFilename();
		if (!empty($localfilename)) {
			if (file_exists($localfilename)){
				// found image in unprotected directory
				$this->fileexists = 2;
				$this->serverfilename = $localfilename;
				return $this->serverfilename;
			}
			if ($USE_MEDIA_FIREWALL) {
				$protectedfilename = get_media_firewall_path($localfilename);
				if (file_exists($protectedfilename)){
					// found image in protected directory
					$this->fileexists = 3;
					$this->serverfilename = $protectedfilename;
					return $this->serverfilename;
				}
			}
		}
		// file doesn't exist, return the standard localfilename for backwards compatibility
		$this->fileexists = false;
		$this->serverfilename = $localfilename;
		return $this->serverfilename;
	}

	/**
	 * check if the file exists on this server
	 * @return boolean
	 */
	function fileExists() {
		if (!$this->serverfilename) $this->getServerFilename();
		return $this->fileexists;
	}

	/**
	 * get the media file size
	 * @return string
	 */
	function getFilesize() {
		if (!$this->filepropset) $this->setFileProperties();
		return(sprintf("%.2f", @$this->filesizeraw/1024));
	}

	/**
	 * get the media file size, unformatted
	 * @return number
	 */
	function getFilesizeraw() {
		if (!$this->filepropset) $this->setFileProperties();
		return $this->filesizeraw;
	}

	/**
	 * get the media type
	 * @return string
	 */
	function getMediatype() {
		$mediaType = strtolower(get_gedcom_value("FORM:TYPE", 2, $this->gedrec));
		return $mediaType;
	}

	/**
	 * get the media file type
	 * @return string
	 */
	function getFiletype() {
		if (!$this->filepropset) $this->setFileProperties();
		return $this->ext;
	}

	/**
	 * get the media mime type
	 * @return string
	 */
	function getMimetype() {
		if (!$this->filepropset) $this->setFileProperties();
		return $this->mime;
	}

	/**
	 * get the width of the image
	 * @return number (0 if not an image)
	 */
	function getWidth() {
		if (!$this->filepropset) $this->setFileProperties();
		return $this->width;
	}

	/**
	 * get the height of the image
	 * @return number (0 if not an image)
	 */
	function getHeight() {
		if (!$this->filepropset) $this->setFileProperties();
		return $this->height;
	}

	/**
	 * internal function, sets a number of properties
	 * no need to call directly
	 * @return nothing
	 */
	function setFileProperties() {
		global $pgv_lang;

		if ($this->fileExists()) {
			$this->filesizeraw = @filesize($this->getServerFilename());
			$imgsize=@getimagesize($this->getServerFilename()); // [0]=width [1]=height [2]=filetype ['mime']=mimetype
			if (is_array($imgsize)) {
				// this is an image
				$this->width =0+$imgsize[0];
				$this->height=0+$imgsize[1];
				$imageTypes  =array("","GIF","JPG","PNG","SWF","PSD","BMP","TIFF","TIFF","JPC","JP2","JPX","JB2","SWC","IFF","WBMP","XBM");
				$this->ext   =$imageTypes[0+$imgsize[2]];
				$this->mime  =$imgsize['mime'];
			}
		}
		if (!$this->mime) {
			// this is not an image, OR the file doesn't exist OR it is a url
			// set file type equal to the file extension - can't use parse_url because this may not be a full url
			$exp = explode("?", $this->file);
			$pathinfo = pathinfo($exp[0]);
			$this->ext = @strtoupper($pathinfo['extension']);
			// all mimetypes we wish to serve with the media firewall must be added to this array.
			$mime=array('DOC'=>'application/msword', 'MOV'=>'video/quicktime', 'MP3'=>'audio/mpeg', 'PDF'=>'application/pdf',
			'PPT'=>'application/vnd.ms-powerpoint', 'RTF'=>'text/rtf', 'SID'=>'image/x-mrsid', 'TXT'=>'text/plain', 'XLS'=>'application/vnd.ms-excel');
			if (empty($mime[$this->ext])) {
				// if we don't know what the mimetype is, use something ambiguous
				$this->mime='application/octet-stream';
				if ($this->fileExists()) {
					// alert the admin if we cannot determine the mime type of an existing file
					// as the media firewall will be unable to serve this file properly
					AddToLog($pgv_lang["unknown_mime"].' >'.$this->file.'<');
				}
			} else {
				$this->mime=$mime[$this->ext];
			}
		}
		$this->filepropset = true;
	}

	/**
	 * get the URL to link to this object
	 * @string a url that can be used to link to this object
	 */
	public function getLinkUrl() {
		return parent::_getLinkUrl('mediaviewer.php?mid=');
	}

	/**
	 * check if the given Media object is in the objectlist
	 * @param Media $obje
	 * @return mixed  returns the ID for the for the matching media or null if not found
	 */
	static function in_obje_list($obje) {
		global $TBLPREFIX, $gBitDb;

		return
			$gBitDb->getOne(
				"SELECT m_media FROM {$TBLPREFIX}media WHERE m_file=? AND m_titl LIKE ? AND m_gedfile=?"
				, array($obje->file, $obje->title, PGV_GED_ID));
	}

	/**
	 * check if this object is equal to the given object
	 * basically just checks if the IDs are the same
	 * @param GedcomRecord $obj
	 */
	function equals(&$obj) {
		if (is_null($obj)) return false;
		if ($this->xref==$obj->getXref()) return true;
		if ($this->title==$obj->title && $this->file==$obj->file) return true;
		return false;
	}

	// If this object has no name, what do we call it?
	function getFallBackName() {
		if ($this->canDisplayDetails()) {
			return UTF8_strtoupper(basename($this->file));
		} else {
			return $this->getXref();
		}
	}

	// Get an array of structures containing all the names in the record
	function getAllNames() {
		if (strpos($this->gedrec, "\n1 TITL ")) {
			// Earlier gedcom versions had level 1 titles
			return parent::getAllNames('TITL', 1);
		} else {
			// Later gedcom versions had level 2 titles
			return parent::getAllNames('TITL', 2);
		}
	}

	// Extra info to display when displaying this record in a list of
	// selection items or favourites.
	function format_list_details() {
		ob_start();
		print_media_links('1 OBJE @'.$this->getXref().'@', 1, $this->getXref());
		return ob_get_clean();
	}

}
?>

<?php
/**
 * Class that defines a media object
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * @version $Id: media_class.php,v 1.6 2007/06/09 21:11:04 lsces Exp $
 */

require_once(PHPGEDVIEW_PKG_PATH.'includes/gedcomrecord.php');

class Media extends GedcomRecord {
	var $title = "";
	var $file = "";
	var $ext = "";
	
	function Media($gedrec) {
		parent::GedcomRecord($gedrec);
		$this->title = get_gedcom_value("TITL", 1, $gedrec);
		if (empty($this->title)) $this->title = get_gedcom_value("TITL", 2, $gedrec);
		$this->file = get_gedcom_value("FILE", 1, $gedrec);
		$et = preg_match("/(\.\w+)$/", $this->file, $ematch);
		$this->ext = "";
		if ($et>0) $this->ext = substr(trim($ematch[1]),1);
	}
	
	/**
	 * check if the given Media object is in the objectlist
	 * @param Media $obje
	 * @return mixed  returns the ID for the for the matching media or false if not found
	 */
	function in_obje_list(&$obje) {
		global $GEDCOM, $FILE, $gBitSystem;
		
		if (is_null($obje)) return false;
		if (empty($FILE)) $FILE = $GEDCOM;
		$sql = "SELECT m_media FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_file=? AND m_titl LIKE ? AND m_gedfile=?";
		$res = $gBitSystem->mDb->query($sql, array( $obje->file, $obje->title[$FILE]['id'] ) );
	
		if ($res->numRows()>0) {
			$row = $res->fetchRow();
			return $row['m_media'];
		}
		
		return false;
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
} 
?>
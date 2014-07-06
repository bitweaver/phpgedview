<?php
/**
 * Class file for a Shared Note (NOTE) object
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage DataModel
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_CLASS_NOTE_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_gedcomrecord.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_serviceclient.php');

class Note extends GedcomRecord {
	// Get an instance of a Shared note.  We either specify
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
			$data=fetch_other_record($pid, $ged_id);

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
		$object=new Note($data, $simple);
		if (!empty($fromfile)) {
			$object->setChanged(true);
		}

		// Store it in the cache
		$gedcom_record_cache[$object->xref][$object->ged_id]=&$object;
		return $object;
	}

	/**
	 * get the URL to link to this shared note
	 * @string a url that can be used to link to this shared note
	 */
	public function getLinkUrl() {
		return parent::_getLinkUrl('note.php?nid=');
	}

	
	// The "name" of a note record is the first line.  This can be
	// somewhat unwieldy if lots of CONC records are used.  Limit to 100 chars
	function _addName($type, $value, $gedrec) {
		global $pgv_lang;
		if (UTF8_strlen($value)<100) parent::_addName($type, $value, $gedrec);
		else parent::_addName($type, UTF8_substr($value, 0, 100).$pgv_lang["ellipsis"], $gedrec);
	}

	// Get an array of structures containing all the names in the record
	function getAllNames() {
		// Uniquely, the NOTE objects have data in their level 0 record.
		// Hence the REGEX passed in the second parameter
		return parent::getAllNames('NOTE', "0 @".PGV_REGEX_XREF."@");
	}
}
?>

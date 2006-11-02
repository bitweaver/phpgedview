<?php
/**
 *
 * Import specific functions
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
 * @version $Id$
 * @package PhpGedView
 * @subpackage DB
 */
if (strstr($_SERVER["SCRIPT_NAME"], "functions")) {
	print "Now, why would you want to do that.	You're not hacking are you?";
	exit;
}

require_once ('includes/media_class.php');

/**
 * import record into database
 *
 * this function will parse the given gedcom record and add it to the database
 * @param string $indirec the raw gedcom record to parse
 * @param boolean $update whether or not this is an updated record that has been accepted
 */
function import_record($indirec, $update = false) {
	global $gGedcom, $gBitSystem, $gid, $type, $indilist, $famlist, $sourcelist, $otherlist, $TOTAL_QUERIES, $prepared_statement;
	global $GEDCOM_FILE, $FILE, $pgv_lang, $USE_RIN, $CREATE_GENDEX, $gdfp, $placecache;
	global $ALPHABET_upper, $ALPHABET_lower, $place_id, $WORD_WRAPPED_NOTES, $GEDCOMS;
	global $MAX_IDS, $fpnewged, $GEDCOM, $USE_RTL_FUNCTIONS, $GENERATE_UIDS;

	$FILE = $GEDCOM;

	//-- import different types of records
	$ct = preg_match("/0 @(.*)@ ([a-zA-Z_]+)/", $indirec, $match);
	if ($ct > 0) {
		$gid = $match[1];
		$type = trim($match[2]);
	} else {
		$ct = preg_match("/0 (.*)/", $indirec, $match);
		if ($ct > 0) {
			$gid = trim($match[1]);
			$type = trim($match[1]);
		} else {
			print $pgv_lang["invalid_gedformat"] . "<br /><pre>$indirec</pre>\n";
		}
	}

	//-- check for a _UID, if the record doesn't have one, add one
	if ($GENERATE_UIDS && $type != "HEAD" && $type != "TRLR" && preg_match("/1 _UID /", $indirec) == 0) {
		$indirec = trim($indirec) . "\r\n1 _UID " . uuid();
	}
	//-- uncomment to replace existing _UID, normally we want them to stay the same
	//	else {
	//		$indirec = preg_replace("/1 _UID (.*)/", "1 _UID ".uuid(), $indirec);
	//	}

	//-- keep track of the max id for each type as they are imported
	if (!isset ($MAX_IDS))
		$MAX_IDS = array ();
	$idnum = 0;
	$ct = preg_match("/(\d+)/", $gid, $match);
	if ($ct > 0)
		$idnum = $match[1];
	if (!isset ($MAX_IDS[$type]))
		$MAX_IDS[$type] = $idnum;
	else
		if ($MAX_IDS[$type] < $idnum)
			$MAX_IDS[$type] = $idnum;

	//-- remove double @ signs
	$indirec = preg_replace("/@+/", "@", $indirec);

	// remove heading spaces
	$indirec = preg_replace("/\n(\s*)/", "\n", $indirec);
	if ($USE_RTL_FUNCTIONS) {
		//-- replace any added ltr processing codes
		//		$indirec = preg_replace(array("/".html_entity_decode("&rlm;",ENT_COMPAT,"UTF-8")."/", "/".html_entity_decode("&lrm;",ENT_COMPAT,"UTF-8")."/"), array("",""), $indirec);
		// Because of a bug in PHP 4, the above generates an error message and does nothing.
		// see:  http://bugs.php.net/bug.php?id=25670
		// HTML entity &rlm; is the 3-byte UTF8 character 0xE2808F
		// HTML entity &lrm; is the 3-byte UTF8 character 0xE2808E
		$indirec = str_replace(array (
			chr(0xE2
		) . chr(0x80) . chr(0x8F), chr(0xE2) . chr(0x80) . chr(0x8E)), "", $indirec);
	}

	//-- if this is an import from an online update then import the places
	if ($update) {
		update_places($gid, $indirec, $update);
		update_dates($gid, $indirec);
	}

	$newrec = update_media($gid, $indirec, $update);
	if ($newrec != $indirec) {
		$indirec = $newrec;
		//-- make sure we have the correct media id
		$ct = preg_match("/0 @(.*)@ ([a-zA-Z_]+)/", $indirec, $match);
		if ($ct > 0) {
			$gid = $match[1];
			$type = trim($match[2]);
		} else
			$gid = '';
	}

	//-- set all remote link ids
	$ct = preg_match("/1 RFN (.*)/", $indirec, $rmatch);
	if ($ct) {
		$rfn = trim($rmatch[1]);
		$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "remotelinks VALUES ( ?, ?, ? )";
		$res = $gBitSystem->mDb->query($sql, array( $gid, $rfn, $GEDCOMS[$GEDCOM]["id"] ) );
	}

	if ($type == "INDI") {
		cleanup_tags_y($indirec);
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$sfams = "";
		for ($j = 0; $j < $ct; $j++) {
			$sfams .= $match[$j][1] . ";";
		}
		$ct = preg_match_all("/1 FAMC @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$cfams = "";
		for ($j = 0; $j < $ct; $j++) {
			$cfams .= $match[$j][1] . ";";
		}
		$isdead = -1;
		$indi = array ();
		$names = get_indi_names($indirec, true);
		$j = 0;
		foreach ($names as $indexval => $name) {
			if ($j > 0) {
				$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "names VALUES( ?, ?, ?, ?, ?, ? )";
				$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$FILE]["id"], $name[0], $name[1], $name[2], $name[3] ) );

			}
			$j++;
		}
		$indi["names"] = $names;
		$indi["isdead"] = $isdead;
		$indi["gedcom"] = $indirec;
		$indi["gedfile"] = $GEDCOMS[$FILE]["id"];
		if ($USE_RIN) {
			$ct = preg_match("/1 RIN (.*)/", $indirec, $match);
			if ($ct > 0)
				$rin = trim($match[1]);
			else
				$rin = $gid;
			$indi["rin"] = $rin;
		} else
			$indi["rin"] = $gid;

		$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "individuals VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )";
		$res = $gBitSystem->mDb->query($sql, array( $gid, $indi["gedfile"], $indi["rin"], $names[0][0], -1, $indi["gedcom"], $names[0][1], $names[0][2] ) );

		//-- PEAR supports prepared statements in mysqli we will use this code instead of the code above
		//if (!isset($prepared_statement)) $prepared_statement = $gBitSystem->mDb->prepare("INSERT INTO ".PHPGEDVIEW_DB_PREFIX."individuals VALUES (?,?,?,?,?,?,?,?)");
		//$data = array( $gid, $indi["file"], $indi["rin"], $names[0][0], -1, $indi["gedcom"], $names[0][1], $names[0][2]);
		//$res =& $gBitSystem->mDb->execute($prepared_statement, $data);
		//$TOTAL_QUERIES++;
//		if (DB :: isError($res)) {
			// die(__LINE__." ".__FILE__."  ".$res->getMessage());
//		}
	} else
		if ($type == "FAM") {
			cleanup_tags_y($indirec);
			$parents = array ();
			$ct = preg_match("/1 HUSB @(.*)@/", $indirec, $match);
			if ($ct > 0)
				$parents["HUSB"] = $match[1];
			else
				$parents["HUSB"] = false;
			$ct = preg_match("/1 WIFE @(.*)@/", $indirec, $match);
			if ($ct > 0)
				$parents["WIFE"] = $match[1];
			else
				$parents["WIFE"] = false;
			$ct = preg_match_all("/\d CHIL @(.*)@/", $indirec, $match, PREG_SET_ORDER);
			$chil = "";
			for ($j = 0; $j < $ct; $j++) {
				$chil .= $match[$j][1] . ";";
			}
			$fam = array ();
			$fam["HUSB"] = $parents["HUSB"];
			$fam["WIFE"] = $parents["WIFE"];
			$fam["CHIL"] = $chil;
			$fam["gedcom"] = $indirec;
			$fam["gedfile"] = $GEDCOMS[$FILE]["id"];
			//$famlist[$gid] = $fam;
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "families (f_id, f_file, f_husb, f_wife, f_chil, f_gedcom, f_numchil) VALUES ( ?, ?, ?, ?, ?, ?, ? )";
			$res = $gBitSystem->mDb->query($sql, array ( $gid, $fam["gedfile"], $fam["HUSB"], $fam["WIFE"], $fam["CHIL"], $fam["gedcom"], $ct ));

		} else
			if ($type == "SOUR") {
				$et = preg_match("/1 ABBR (.*)/", $indirec, $smatch);
				if ($et > 0)
					$name = $smatch[1];
				$tt = preg_match("/1 TITL (.*)/", $indirec, $smatch);
				if ($tt > 0)
					$name = $smatch[1];
				if (empty ($name))
					$name = $gid;
				$subindi = preg_split("/1 TITL /", $indirec);
				if (count($subindi) > 1) {
					$pos = strpos($subindi[1], "\n1", 0);
					if ($pos)
						$subindi[1] = substr($subindi[1], 0, $pos);
					$ct = preg_match_all("/2 CON[C|T] (.*)/", $subindi[1], $match, PREG_SET_ORDER);
					for ($i = 0; $i < $ct; $i++) {
						$name = trim($name);
						if ($WORD_WRAPPED_NOTES)
							$name .= " " . $match[$i][1];
						else
							$name .= $match[$i][1];
					}
				}
				$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "sources VALUES ( ?, ?, ?, ? )";
				$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$FILE]["id"], $name, $indirec ));

			} else
				if ($type == "OBJE") {
					//-- don't duplicate OBJE records
					//-- OBJE records are imported by update_media function
				} else
					if (preg_match("/_/", $type) == 0) {
						if ($type == "HEAD") {
							$ct = preg_match("/1 DATE (.*)/", $indirec, $match);
							if ($ct == 0) {
								$indirec = trim($indirec);
								$indirec .= "\r\n1 DATE " . date("d") . " " . date("M") . " " . date("Y");
							}
						}
						$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "other VALUES ( ?, ?, ?, ? )";
						$res = $gBitSystem->mDb->query( $sql, array( $gid, $GEDCOMS[$FILE]["id"], $type, $indirec ) );

					}

	//-- if this is not an update then write it to the new gedcom file
	if (!$update && !empty ($fpnewged) && !(empty ($indirec)))
		fwrite($fpnewged, trim($indirec) . "\r\n");
}

/**
 * Add a new calculated name to the individual names table
 *
 * this function will add a new name record for the given individual, this function is called from the
 * importgedcom.php script stage 5
 * @param string $gid	gedcom xref id of individual to update
 * @param string $newname	the new calculated name to add
 * @param string $surname	the surname for this name
 * @param string $letter	the letter for this name
 */
function add_new_name($gid, $newname, $letter, $surname, $indirec) {
	global $USE_RIN, $indilist, $FILE, $gGedcom, $gBitSystem, $GEDCOMS;

	$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "names VALUES( ?, ?, ?, ?, ?,'C' )";
	$res = $gBitSystem->mDb->query($sql, array ( $gid, $GEDCOMS[$FILE]["id"], $newname, $letter, $surname ) );

	$sql = "UPDATE " . PHPGEDVIEW_DB_PREFIX . "individuals SET i_gedcom=? WHERE i_id=? AND i_file=?";
	$res = $gBitSystem->mDb->query($sql, array( $indirec, $gid, $GEDCOMS[$FILE]["id"] ));

	$indilist[$gid]["names"][] = array (
		$newname,
		$letter,
		$surname,
		'C'
	);
	$indilist[$gid]["gedcom"] = $indirec;
}

/**
 * extract all places from the given record and insert them
 * into the places table
 * @param string $indirec
 */
function update_places($gid, $indirec, $update = false) {
	global $FILE, $placecache, $gGedcom, $gBitSystem, $GEDCOMS;

	if (!isset($placecache)) $placecache = array();
	$personplace = array();
	//-- import all place locations
	$pt = preg_match_all("/\d PLAC (.*)/", $indirec, $match, PREG_SET_ORDER);
	for ($i = 0; $i < $pt; $i++) {
		$place = trim($match[$i][1]);
		//-- if we have already visited this place for this person then we don't need to again
		if (isset($personplace[str2lower($place)])) continue;
		$personplace[str2lower($place)] = 1;
		$places = preg_split("/,/", $place);
		//-- reverse the array to start at the highest level
		$secalp = array_reverse($places);
		$parent_id = 0;
		$level = 0;
		$search = true;

		foreach ($secalp as $indexval => $place) {
			$place = trim($place);
			$place=preg_replace('/\\\"/', "", $place);
			$place=preg_replace("/[\><]/", "", $place);
			$key = strtolower($place."_".$level."_".$parent_id);
			//-- if this place has already been added then we don't need to add it again
			if (isset($placecache[$key])) {
				$parent_id = $placecache[$key];
				if (!isset($personplace[$key])) {
					$personplace[$key]=1;
					$sql = 'INSERT INTO ' . PHPGEDVIEW_DB_PREFIX . 'placelinks VALUES( ?, ?, ? )';
					$res2 = $gBitSystem->mDb->query($sql, array( $parent_id, $gid, $GEDCOMS[$FILE]["id"] ));
				}
				$level++;
				continue;
				}

			//-- only search the database while we are finding places in it
			if ($search) {
				//-- check if this place and level has already been added
				$sql = 'SELECT p_id FROM '.PHPGEDVIEW_DB_PREFIX.'places WHERE p_level=? AND p_file=? AND p_parent_id=? AND p_place LIKE ?';
				$res = $gBitSystem->mDb->query($sql, array( $level, $GEDCOMS[$FILE]['id'], $parent_id, $place ) );
				if ($res->numRows()>0) {
					$row = $res->fetchRow();
					$p_id = $row['p_id'];
				}
				else $search = false;
				$res->free();
			}
			
			//-- if we are not searching then we have to insert the place into the db
			if (!$search) {
				$p_id = get_next_id("places", "p_id");
				$sql = 'INSERT INTO '.PHPGEDVIEW_DB_PREFIX.'places (p_id, p_place, p_level, p_parent_id, p_file) VALUES( ?, ?, ?, ?, ? )';
				$res2 = $gBitSystem->mDb->query($sql, array( $p_id, $place, $level, $parent_id, $GEDCOMS[$FILE]["id"] ) );
					}

			$sql = 'INSERT INTO ' . PHPGEDVIEW_DB_PREFIX . 'placelinks VALUES( ?, ?, ? )';
			$res2 = $gBitSystem->mDb->query($sql, array( $p_id, $gid, $GEDCOMS[$FILE]["id"] ) );
			//-- increment the level and assign the parent id for the next place level
			$parent_id = $p_id;
			$placecache[$key] = $p_id;
			$personplace[$key]=1;
			$level++;
		}
	}
	return $pt;
}

/**
 * extract all date info from the given record and insert them
 * into the dates table
 * @param string $indirec
 */
function update_dates($gid, $indirec) {
	global $FILE, $gGedcom, $gBitSystem, $GEDCOMS;

	$count = 0;
	$pt = preg_match("/\d DATE (.*)/", $indirec, $match);
	if ($pt == 0)
		return 0;
	$facts = get_all_subrecords($indirec, "", false, false, false);
	foreach ($facts as $f => $factrec) {
		$fact = "EVEN";
		$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		if ($ft > 0) {
			$fact = trim($match[1]);
			$event = trim($match[2]);
		}
		$pt = preg_match_all("/2 DATE (.*)/", $factrec, $match, PREG_SET_ORDER);
		for ($i = 0; $i < $pt; $i++) {
			$datestr = trim($match[$i][1]);
			$date = parse_date($datestr);
			if (empty ($date[0]["day"]))
				$date[0]["day"] = 0;
			if (empty ($date[0]["mon"]))
				$date[0]["mon"] = 0;
			if (empty ($date[0]["year"]))
				$date[0]["year"] = 0;
			$datestamp = $date[0]['year'];
			if ($date[0]['mon'] < 10)
				$datestamp .= '0';
			$datestamp .= (int) $date[0]['mon'];
			if ($date[0]['day'] < 10)
				$datestamp .= '0';
			$datestamp .= (int) $date[0]['day'];
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "dates VALUES( ?, ?, ?, ?, ?, ?, ?, ?,";
			if (isset ($date[0]["ext"])) {
				preg_match("/@#D(.*)@/", $date[0]["ext"], $extract_type);
				$date_types = array (
					"@#DGREGORIAN@",
					"@#DJULIAN@",
					"@#DHEBREW@",
					"@#DFRENCH R@",
					"@#DROMAN@",
					"@#DUNKNOWN@"
				);
				if (isset ($extract_type[0]) && in_array($extract_type[0], $date_types))
					$sql .= "'" . $extract_type[0] . "')";
				else
					$sql .= "NULL)";
			} else
				$sql .= "NULL)";
			$res = $gBitSystem->mDb->query($sql, array ( $date[0]["day"], str2upper($date[0]["month"] ), $date[0]["mon"], $date[0]["year"], $datestamp, $fact, $gid, $GEDCOMS[$FILE]["id"] ) );

			$count++;
		}
	}
	return $count;
}

/**
 * import media items from record
 * @todo Decide whether or not to update the original gedcom file
 * @return string	an updated record
 */
function update_media($gid, $indirec, $update = false) {
	global $GEDCOMS, $FILE, $gGedcom, $gBitSystem, $MEDIA_ID_PREFIX, $media_count, $found_ids;
	global $zero_level_media, $fpnewged, $objelist, $MAX_IDS;

	if (!isset ($media_count))
		$media_count = 0;
	if (!isset ($found_ids))
		$found_ids = array ();
	if (!isset ($zero_level_media))
		$zero_level_media = false;
	if (!$update && !isset ($MAX_IDS["OBJE"]))
		$MAX_IDS["OBJE"] = 1;

	//-- handle level 0 media OBJE seperately
	$ct = preg_match("/0 @(.*)@ OBJE/", $indirec, $match);
	if ($ct > 0) {
		$old_m_media = $match[1];
		$m_id = get_next_id("media", "m_id");
		if ($update) {
			$new_m_media = $old_m_media;
		} else {
			if (isset ($found_ids[$old_m_media])) {
				$new_m_media = $found_ids[$old_m_media]["new_id"];
			} else {
				$new_m_media = get_new_xref("OBJE");
				$found_ids[$old_m_media]["old_id"] = $old_m_media;
				$found_ids[$old_m_media]["new_id"] = $new_m_media;
			}
		}
		$indirec = preg_replace("/@" . $old_m_media . "@/", "@" . $new_m_media . "@", $indirec);
		$media = new Media($indirec);
		//--check if we already have a similar object
		$new_media = Media :: in_obje_list($media);
		if ($new_media === false) {
			$objelist[$new_m_media] = $media;
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
			$sql .= " VALUES( ?, ?, ?, ?, ?, ?, ? )";
			$res = $gBitSystem->mDb->query($sql, array ( $m_id, $new_m_media, $media->ext, $media->title, $media->file, $GEDCOMS[$FILE]["id"], $indirec ) );
			$media_count++;
		} else {
			$new_m_media = $new_media;
			$found_ids[$old_m_media]["old_id"] = $old_m_media;
			$found_ids[$old_m_media]["new_id"] = $new_media;
			//$indirec = preg_replace("/0 @(.*)@ OBJE/", "0 @$new_media@ OBJE", $indirec);
			//-- record was replaced by a duplicate record so leave it out.
			return '';
		}
		return $indirec;
	}

	//-- check to see if there are any media records
	//-- if there aren't any media records then don't look for them just return
	$pt = preg_match("/\d OBJE/", $indirec, $match);
	if ($pt == 0)
		return $indirec;

	//-- go through all of the lines and replace any local
	//--- OBJE to referenced OBJEs
	$newrec = "";
	$lines = preg_split("/[\r\n]+/", trim($indirec));
	$ct_lines = count($lines);
	$inobj = false;
	$processed = false;
	$objlevel = 0;
	$objrec = "";
	$count = 1;
	foreach ($lines as $key => $line) {
		if (!empty ($line)) {
			// NOTE: Match lines that resemble n OBJE @0000@
			// NOTE: Renumber the old ID to a new ID and save the old ID
			// NOTE: in case there are more references to it
			if (preg_match("/[1-9]\sOBJE\s@(.*)@/", $line, $match) != 0) {
				// NOTE: Check if objlevel greater is than 0, if so then store the current object record
				if ($objlevel > 0) {
					$m_media = get_new_xref("OBJE");
					$objrec = preg_replace("/ OBJE/", " @" . $m_media . "@ OBJE", $objrec);
					$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
					$media = new Media($objrec);
					$new_media = Media :: in_obje_list($media);
					if ($new_media === false) {
						$m_id = get_next_id("media", "m_id");
						$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
						$sql .= " VALUES( ?, ?, ?, ?, ?, ?, ? )";
						$res = $gBitSystem->mDb->query($sql, array ( $m_id, $new_m_media, $media->ext, $media->title, $media->file, $GEDCOMS[$FILE]["id"], $objrec ));
						$media_count++;
						//-- if this is not an update then write it to the new gedcom file
						if (!$update && !empty ($fpnewged))
							fwrite($fpnewged, trim($objrec) . "\r\n");
						//print "LINE ".__LINE__;
						$objelist[$m_media] = $media;
					} else
						$m_media = $new_media;
					$mm_id = get_next_id("media_mapping", "mm_id");
					$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec)";
					$sql .= " VALUES ( ?, ?, ?, ?, ?, ? )";
					$res = $gBitSystem->mDb->query($sql, array( $mm_id, $m_media, $gid, $count, $GEDCOMS[$FILE]['id'], $objlevel . ' OBJE @' . $m_media . '@' ) );
					$count++;
					// NOTE: Add the new media object to the record
					$newrec .= $objlevel . " OBJE @" . $m_media . "@\r\n";

					// NOTE: Set the details for the next media record
					$objlevel = $match[0] { 0 };
					$inobj = true;
					$objrec = $line . "\r\n";
				} else {
					// NOTE: Set object level
					$objlevel = $match[0] { 0 };
					$inobj = true;
					$objrec = $line . "\r\n";
				}

				// NOTE: Retrieve the old media ID
				$old_mm_media = $match[1];

				//-- use the old id if we are updating from an online edit
				if ($update) {
					$new_mm_media = $old_mm_media;
				} else {
					// 	NOTE: Check if the id already exists and there is a value behind OBJE (n OBJE @M001@)
					if (!isset ($found_ids[$old_mm_media]) && !empty ($match[1])) {
						// NOTE: Get a new media ID
						$new_mm_media = get_new_xref("OBJE");
					} else {
						$new_mm_media = $found_ids[$old_mm_media]['new_id'];
					}
				}
				$m_id = get_next_id("media", "m_id");

				// NOTE: Put both IDs in the found_ids array in case we later find the 0-level
				// NOTE: The 0-level ID will have to be changed also
				$found_ids[$old_mm_media]["old_id"] = $old_mm_media;
				$found_ids[$old_mm_media]["new_id"] = $new_mm_media;
				$line = preg_replace("/@(.*)@/", "@$new_mm_media@", $line);
				// NOTE: We found an existing media reference, we only add it to the database, nothing else
				//-- don't need to cread a media record for linked media
				//$sql = "INSERT INTO ".PHPGEDVIEW_DB_PREFIX."media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec) VALUES('".$m_id."', '".$new_mm_media."', '', '', '', '".$GEDCOMS[$FILE]["id"]."', '')";
				//$res = $gBitSystem->mDb->query($sql);
				$mm_id = get_next_id("media_mapping", "mm_id");
				$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec) VALUES ( ?, ?, ?, ?, ?, ? )";
				$res = & $gBitSystem->mDb->query($sql, array ( $mm_id, $new_mm_media, $gid, $count, $GEDCOMS[$FILE]['id'], $line ) );
				//print "LINE ".__LINE__;
				$count++;
				$objlevel = 0;
				$objrec = "";
				$inobj = false;
			} else
				if (preg_match("/[1-9]\sOBJE/", $line, $match)) {
					if (!empty ($objrec)) {
						$m_id = get_next_id("media", "m_id");
						$m_media = get_new_xref("OBJE");
						$objrec = preg_replace("/ OBJE/", " @" . $m_media . "@ OBJE", $objrec);
						$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
						$media = new Media($objrec);
						$new_media = Media :: in_obje_list($media);
						if ($new_media === false) {
							$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
							$sql .= " VALUES( ?, ?, ?, ?, ?, ?, ? )";
							$res = $gBitSystem->mDb->query($sql, array( $m_id, $m_media, $media->ext, $media->title, $media->file, $GEDCOMS[$FILE]["id"], $objrec ) );
							//-- if this is not an update then write it to the new gedcom file
							if (!$update && !empty ($fpnewged))
								fwrite($fpnewged, trim($objrec) . "\r\n");
							//print "LINE ".__LINE__;
							$media_count++;
							$objelist[$m_media] = $media;
						} else
							$m_media = $new_media;
						$mm_id = get_next_id("media_mapping", "mm_id");
						$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec)";
						$sql .= " VALUES ( ?, ?, ?, ?, ?, ? )";
						$res = $gBitSystem->mDb->query($sql, array( $mm_id, $m_media, $gid, $count, $GEDCOMS[$FILE]['id'], $objlevel . ' OBJE @' . $m_media . '@' ) );
						$count++;
						// NOTE: Add the new media object to the record
						$newrec .= $objlevel . " OBJE @" . $m_media . "@\r\n";
					}
					// NOTE: Set the details for the next media record
					$objlevel = $match[0] { 0 };
					$inobj = true;
					$objrec = $line . "\r\n";
				} else {
					$ct = preg_match("/(\d+)\s(\w+)(.*)/", $line, $match);
					if ($ct > 0) {
						$level = $match[1];
						$fact = $match[2];
						$desc = trim($match[3]);
						if ($fact == "FILE") {
							// Correct Media depth and other common mistakes in file name
							//$desc = check_media_depth($desc, "FRONT", "QUIET");
							$match[3] = $desc;
							$line = $match[1] . " " . $match[2] . " " . $match[3];
						}
						if ($inobj && ($level <= $objlevel || $key == $ct_lines -1)) {
							if ($key == $ct_lines -1 && $level > $objlevel) {
								$objrec .= $line . "\r\n";
							}
							$m_id = get_next_id("media", "m_id");
							if ($objrec {
								0 }
							!= 0) {
								$m_media = get_new_xref("OBJE");
								$objrec = preg_replace("/ OBJE/", " @" . $m_media . "@ OBJE", $objrec);
								$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
								$media = new Media($objrec);
								$new_media = Media :: in_obje_list($media);
								if ($new_media === false) {
									$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
									$sql .= " VALUES( ?, ?, ?, ?, ?, ?, ? )";
									$res = $gBitSystem->mDb->query($sql, array( $m_id, $m_media, $media->ext, $media->title, $media->file, $GEDCOMS[$FILE]["id"], $objrec ) );
									//-- if this is not an update then write it to the new gedcom file
									if (!$update && !empty ($fpnewged))
										fwrite($fpnewged, trim($objrec) . "\r\n");
									//print "LINE ".__LINE__;
									$media_count++;
									$objelist[$m_media] = $media;
								} else
									$m_media = $new_media;
								$mm_id = get_next_id("media_mapping", "mm_id");
								$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec)";
								$sql .= " VALUES ( ?, ?, ?, ?, ?, ? )";
								$res = $gBitSystem->mDb->query($sql, array( $mm_id, $m_media, $gid, $count, $GEDCOMS[$FILE]['id'], $objlevel . ' OBJE @' . $m_media . '@' ) );
							}
							//-- what is this for?  it shouldn't be used anymore because of code above
							/*
							else {
								$oldid = preg_match("/0\s@(.*)@\sOBJE/", $objrec, $newmatch);
								$m_media = $newmatch[1];
								$sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."media SET m_ext = $ext, m_titl = $title, m_file = $file, m_gedrec = $objrec WHERE m_media = $m_media";
								$res = $gBitSystem->mDb->query($sql);
								//print "LINE ".__LINE__;
							}
							*/

							$count++;
							$objrec = "";
							$newrec .= $objlevel . " OBJE @" . $m_media . "@\r\n";
							$inobj = false;
							$objlevel = 0;
						} else {
							if ($inobj)
								$objrec .= $line . "\r\n";
						}
						if ($fact == "OBJE") {
							$inobj = true;
							$objlevel = $level;
							$objrec = "";
						}
					}
				}
			if (!$inobj)
				$newrec .= $line . "\r\n";
		}
	}
	return $newrec;
}
/**
 * Create database schema
 *
 * function that checks if the database exists and creates tables
 * automatically handles version updates
 */
function setup_database() {
	global $pgv_lang, $gGedcom, $gBitDbType;
}
/**
 * delete a gedcom from the database
 *
 * deletes all of the imported data about a gedcom from the database
 * @param string $FILE	the gedcom to remove from the database
 */
function empty_database($FILE) {
	global $gGedcom, $GEDCOMS, $gBitSystem;

	$FILE = $GEDCOMS[$FILE]["id"];
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "individuals WHERE i_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "families WHERE f_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "sources WHERE s_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "other WHERE o_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "places WHERE p_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "names WHERE n_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "dates WHERE d_file='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media WHERE m_gedfile='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media_mapping WHERE mm_gedfile='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "nextid WHERE ni_gedfile='$FILE'";
	$res = $gBitSystem->mDb->query($sql);

}

/**
 * Accpet changed gedcom record into database
 *
 * This function gets an updated record from the gedcom file and replaces it in the database
 * @author John Finlay
 * @param string $cid The change id of the record to accept
 */
function accept_changes($cid) {
	global $pgv_changes, $GEDCOM, $FILE, $gGedcom, $GEDCOMS, $MEDIA_ID_PREFIX;
	global $COMMIT_COMMAND, $INDEX_DIRECTORY;

	if (isset ($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[count($changes) - 1];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
		}
		$FILE = $GEDCOM;
		$gid = $change["gid"];
		$indirec = find_record_in_file($gid);
		if (empty ($indirec)) {
			$indirec = find_gedcom_record($gid);
		}

		update_record($indirec, $change["type"] == "delete");

		if ($change["type"] != "delete") {
			//-- synchronize the gedcom record with any user account
/*			$user = getUser($gid);
			if ($user && ($user["sync_gedcom"] == "Y")) {
				$firstname = get_gedcom_value("GIVN", 2, $indirec);
				$lastname = get_gedcom_value("SURN", 2, $indirec);
				if (empty ($lastname)) {
					$fullname = get_gedcom_value("NAME", 1, $indirec, "", false);
					$ct = preg_match("~(.*)/(.*)/~", $fullname, $match);
					if ($ct > 0) {
						$firstname = $match[1];
						$lastname = $match[2];
					} else
						$firstname = $fullname;
				}
				$email = get_gedcom_value("EMAIL", 1, $indirec);
				if (($lastname != $user["lastname"]) || ($firstname != $user["firstname"]) || ($email != $user["email"])) {
					//deleteUser($user["username"]);
					$user["email"] = $email;
					$user["firstname"] = $firstname;
					$user["lastname"] = $lastname;
					updateUser($user["username"], $user);
				}
*/			}
		}

		unset ($pgv_changes[$cid]);
		write_changes();
		if (isset ($_SESSION["recent_changes"]["user"][$GEDCOM]))
			unset ($_SESSION["recent_changes"]["user"][$GEDCOM]);
		if (isset ($_SESSION["recent_changes"]["gedcom"][$GEDCOM]))
			unset ($_SESSION["recent_changes"]["gedcom"][$GEDCOM]);
		$logline = AddToLog("Accepted change $cid " . $change["type"] . " into database ->" . getUserName() . "<-");
		if (!empty ($COMMIT_COMMAND))
			check_in($logline, $GEDCOM, dirname($GEDCOMS[$GEDCOMS]['path']));
		if (isset ($change["linkpid"]))
			accept_changes($change["linkpid"] . "_" . $GEDCOM);
		return true;
	}
	return false;
}

/**
 * update a record in the database
 * @param string $indirec
 */
function update_record($indirec, $delete = false) {
	global $GEDCOM, $gGedcom, $gBitSystem, $GEDCOMS, $FILE;

	if (empty ($FILE))
		$FILE = $GEDCOM;

	$tt = preg_match("/0 @(.+)@ (.+)/", $indirec, $match);
	if ($tt > 0) {
		$gid = trim($match[1]);
		$type = trim($match[2]);
	} else {
		print "ERROR: Invalid gedcom record.";
		return false;
	}

	$sql = "SELECT pl_p_id FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_gid=? AND pl_file=?";
	$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

	$placeids = array ();
	while ($row = & $res->fetchRow()) {
		$placeids[] = $row['pl_p_id'];
	}
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_gid=? AND pl_file=?";
	$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "dates WHERE d_gid=? AND d_file=?";
	$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

	//-- delete any unlinked places
	foreach ($placeids as $indexval => $p_id) {
		$sql = "SELECT count(pl_p_id) FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_p_id=? AND pl_file=?";
		$res = $gBitSystem->mDb->query($sql, array( $p_id, $GEDCOMS[$GEDCOM]["id"] ) );

		$row = & $res->fetchRow();
		if ($row['count'] == 0) {
			$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "places WHERE p_id=? AND p_file=?";
			$res = $gBitSystem->mDb->query($sql, array( $p_id, $GEDCOMS[$GEDCOM]["id"] ) );

		}
	}

	//-- delete any media mapping references
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media_mapping WHERE mm_gid LIKE ? AND mm_gedfile=?";
	$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "remotelinks WHERE r_gid=? AND r_file=?";
	$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

	if ($type == "INDI") {
		$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "individuals WHERE i_id LIKE ? AND i_file=?";
		$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

		$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "names WHERE n_gid LIKE ? AND n_file=?";
		$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

	} else
		if ($type == "FAM") {
			$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "families WHERE f_id LIKE ? AND f_file=?";
			$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

		} else
			if ($type == "SOUR") {
				$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "sources WHERE s_id LIKE ? AND s_file=?";
				$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

			} else
				if ($type == "OBJE") {
					$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media WHERE m_media LIKE ? AND m_gedfile=?";
					$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );
				} else {
					$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "other WHERE o_id LIKE ? AND o_file=?";
					$res = $gBitSystem->mDb->query($sql, array( $gid, $GEDCOMS[$GEDCOM]["id"] ) );

				}
	if (!$delete) {
		import_record($indirec, true);
	}
}

function cleanup_tags_y(& $irec) {
	$cleanup_facts = array ("ANUL","CENS","DIV","DIVF","ENGA","MARR","MARB",
		"MARC","MARL","MARS","BIRT","CHR","DEAT","BURI","CREM","ADOP","DSCR",
		"BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG",
		"IMMI","CENS","PROB","WILL","GRAD","RETI");
	$irec .= "\r\n1";
	//	$ft = preg_match_all("/1\s(_?\w+)\s/", $irec, $match);
	$ft = preg_match_all("/1\s(\w+)\s/", $irec, $match);
	for ($i = 0; $i < $ft; $i++) {
		$sfact = $match[1][$i];
		$sfact = trim($sfact);
		if (in_array($sfact, $cleanup_facts)) {
			$srchstr = "/1\s" . $sfact . "\sY\r\n2/";
			$replstr = "1 " . $sfact . "\r\n2";
			$srchstr2 = "/1\s" . $sfact . "(.{0,1})\r\n2/";
			$srchstr = "/1\s" . $sfact . "\sY\r\n2/";
			$srchstr3 = "/1\s" . $sfact . "\sY\r\n1/";
			$irec = preg_replace($srchstr, $replstr, $irec);
			if (preg_match($srchstr2, $irec)) {
				$irec = preg_replace($srchstr3, "1", $irec);
			}
		}
	}
	$irec = substr($irec, 0, -3);
	//	return $irec;
}

/**
 * Generates a Universally Unique IDentifier, version 4.
 *
 * RFC 4122 (http://www.ietf.org/rfc/rfc4122.txt) defines a special type of Globally
 * Unique IDentifiers (GUID), as well as several methods for producing them. One
 * such method, described in section 4.4, is based on truly random or pseudo-random
 * number generators, and is therefore implementable in a language like PHP.
 *
 * We choose to produce pseudo-random numbers with the Mersenne Twister, and to always
 * limit single generated numbers to 16 bits (ie. the decimal value 65535). That is
 * because, even on 32-bit systems, PHP's RAND_MAX will often be the maximum *signed*
 * value, with only the equivalent of 31 significant bits. Producing two 16-bit random
 * numbers to make up a 32-bit one is less efficient, but guarantees that all 32 bits
 * are random.
 *
 * The algorithm for version 4 UUIDs (ie. those based on random number generators)
 * states that all 128 bits separated into the various fields (32 bits, 16 bits, 16 bits,
 * 8 bits and 8 bits, 48 bits) should be random, except : (a) the version number should
 * be the last 4 bits in the 3rd field, and (b) bits 6 and 7 of the 4th field should
 * be 01. We try to conform to that definition as efficiently as possible, generating
 * smaller values where possible, and minimizing the number of base conversions.
 *
 * @copyright  Copyright (c) CFD Labs, 2006. This function may be used freely for
 *              any purpose ; it is distributed without any form of warranty whatsoever.
 * @author      David Holmes <dholmes@cfdsoftware.net>
 *
 * @return  string  A UUID, made up of 36 hex digits
 */
function uuid() {

	// The field names refer to RFC 4122 section 4.1.2

	return strtoupper(sprintf('%04x%04x%04x%03x4%04x%04x%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
	mt_rand(0, 65535), // 16 bits for "time_mid"
	mt_rand(0, 4095), // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
	bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
	// 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
	// (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
	// 8 bits for "clk_seq_low"
	mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node" 
	));
}
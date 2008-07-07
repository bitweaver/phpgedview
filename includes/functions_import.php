<?php
/**
 *
 * Import specific functions
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  PGV Development Team
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

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once(PHPGEDVIEW_PKG_PATH.'includes/media_class.php');
include_once(PHPGEDVIEW_PKG_PATH.'includes/functions_lang.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/mutex_class.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/index_cache.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions_privacy.php');

// Programs such as FTM use the "tag formal names" instead of the actual tags.  This list lets us convert.
$TRANSLATE_TAGS=array(
	// These are standard tags, as defined in the 5.5.1 gedcom spec.
	'ABBREVIATION'=>'ABBR', 'ADDRESS'=>'ADDR', 'ADDRESS1'=>'ADR1', 'ADDRESS2'=>'ADR2', 'ADDRESS3'=>'ADR3',
	'ADOPTION'=>'ADOP', 'ADULT_CHRISTENING'=>'CHRA', 'AGENCY'=>'AGNC', 'ALIAS'=>'ALIA', 'ANCESTORS'=>'ANCE',
	'ANCES_INTEREST'=>'ANCI', 'ANNULMENT'=>'ANUL', 'ASSOCIATES'=>'ASSO', 'AUTHOR'=>'AUTH', 'BAPTISM'=>'BAPM',
	'BAPTISM_LDS'=>'BAPL', 'BAR_MITZVAH'=>'BARM', 'BAS_MITZVAH'=>'BASM', 'BIRTH'=>'BIRT', 'BLESSING'=>'BLES',
	'BURIAL'=>'BURI', 'CALL_NUMBER'=>'CALN', 'CASTE'=>'CAST', 'CAUSE'=>'CAUS', 'CENSUS'=>'CENS',
	'CHANGE'=>'CHAN', 'CHARACTER'=>'CHAR', 'CHILD'=>'CHIL', 'CHILDREN_COUNT'=>'NCHI', 'CHRISTENING'=>'CHR',
	'CONCATENATION'=>'CONC', 'CONFIRMATION'=>'CONF', 'CONFIRMATION_LDS'=>'CONL', 'CONTINUED'=>'CONT',
	'COPYRIGHT'=>'COPR', 'CORPORATE'=>'CORP', 'COUNTRY'=>'CTRY', 'CREMATION'=>'CREM', 'DEATH'=>'DEAT',
	'_DEGREE'=>'_DEG', 'DESCENDANTS'=>'DESC', 'DESCENDANT_INT'=>'DESI', 'DESTINATION'=>'DEST',
	'DIVORCE'=>'DIV', 'DIVORCE_FILED'=>'DIVF', 'EDUCATION'=>'EDUC', 'EMIGRATION'=>'EMIG', 'ENDOWMENT'=>'ENDL',
	'ENGAGEMENT'=>'ENGA', 'EVENT'=>'EVEN', 'FACSIMILE'=>'FAX', 'FAMILY'=>'FAM', 'FAMILY_CHILD'=>'FAMC',
	'FAMILY_FILE'=>'FAMF', 'FAMILY_SPOUSE'=>'FAMS', 'FIRST_COMMUNION'=>'FCOM', 'FORMAT'=>'FORM',
	'GEDCOM'=>'GEDC', 'GIVEN_NAME'=>'GIVN', 'GRADUATION'=>'GRAD', 'HEADER'=>'HEAD', 'HUSBAND'=>'HUSB',
	'IDENT_NUMBER'=>'IDNO', 'IMMIGRATION'=>'IMMI', 'INDIVIDUAL'=>'INDI', 'LANGUAGE'=>'LANG',
	'LATITUDE'=>'LATI', 'LONGITUDE'=>'LONG', 'MARRIAGE'=>'MARR', 'MARRIAGE_BANN'=>'MARB',
	'MARRIAGE_COUNT'=>'NMR', 'MARR_CONTRACT'=>'MARC', 'MARR_LICENSE'=>'MARL', 'MARR_SETTLEMENT'=>'MARS',
	'MEDIA'=>'MEDI', '_MEDICAL'=>'_MDCL', '_MILITARY_SERVICE'=>'_MILT', 'NAME_PREFIX'=>'NPFX',
	'NAME_SUFFIX'=>'NSFX', 'NATIONALITY'=>'NATI', 'NATURALIZATION'=>'NATU', 'NICKNAME'=>'NICK',
	'OBJECT'=>'OBJE', 'OCCUPATION'=>'OCCU', 'ORDINANCE'=>'ORDI', 'ORDINATION'=>'ORDN', 'PEDIGREE'=>'PEDI',
	'PHONE'=>'PHON', 'PHONETIC'=>'FONE', 'PHY_DESCRIPTION'=>'DSCR', 'PLACE'=>'PLAC', 'POSTAL_CODE'=>'POST',
	'PROBATE'=>'PROB', 'PROPERTY'=>'PROP', 'PUBLICATION'=>'PUBL', 'QUALITY_OF_DATA'=>'QUAL',
	'REC_FILE_NUMBER'=>'RFN', 'REC_ID_NUMBER'=>'RIN', 'REFERENCE'=>'REFN', 'RELATIONSHIP'=>'RELA',
	'RELIGION'=>'RELI', 'REPOSITORY'=>'REPO', 'RESIDENCE'=>'RESI', 'RESTRICTION'=>'RESN', 'RETIREMENT'=>'RETI',
	'ROMANIZED'=>'ROMN', 'SEALING_CHILD'=>'SLGC', 'SEALING_SPOUSE'=>'SLGS', 'SOC_SEC_NUMBER'=>'SSN',
	'SOURCE'=>'SOUR', 'STATE'=>'STAE', 'STATUS'=>'STAT', 'SUBMISSION'=>'SUBN', 'SUBMITTER'=>'SUBM',
	'SURNAME'=>'SURN', 'SURN_PREFIX'=>'SPFX', 'TEMPLE'=>'TEMP', 'TITLE'=>'TITL', 'TRAILER'=>'TRLR',
	'VERSION'=>'VERS', 'WEB'=>'WWW'
	// TODO: FTM uses the follow tags.  What are their full forms?
	// _DETS, _SEPR, CITN, _HEIG, _WEIG, _EXCM, _ELEC, _NAME, _MISN, _UNKN
);

/**
 * import record into database
 *
 * this function will parse the given gedcom record and add it to the database
 * @param string $indirec the raw gedcom record to parse
 * @param boolean $update whether or not this is an updated record that has been accepted
 */
function import_record($indirec, $update = false) {
	global $gGedcom, $gid, $type, $indilist, $famlist, $sourcelist, $otherlist, $TOTAL_QUERIES, $prepared_statement;
	global $GEDCOM_FILE, $FILE, $pgv_lang, $USE_RIN, $CREATE_GENDEX, $gdfp, $placecache;
	global $ALPHABET_upper, $ALPHABET_lower, $place_id, $WORD_WRAPPED_NOTES;
	global $MAX_IDS, $fpnewged, $GEDCOM, $USE_RTL_FUNCTIONS, $GENERATE_UIDS;
	global $TRANSLATE_TAGS;

	$FILE = $GEDCOM;

	// Clean input record
	$indirec=preg_replace('/[\x00-\x09\x0B-\x0C\x0E-\x1F\x7F]+/', ' ', $indirec); // Illegal control characters
	$indirec=preg_replace('/[\r\n]+/', "\n", $indirec); // Standardise line endings
	// EEK! We only need to remove repeated spaces in certain circumstances.
	// This global replace breaks various other things, so take it out
	//$indirec=preg_replace('/ {2,}/', ' ', $indirec); // Repeated spaces
	$indirec=preg_replace('/(^ +| +$)/m', '', $indirec); // Leading/trailing space
	$indirec=preg_replace('/\n{2,}/', "\n", $indirec); // Blank lines
	if (!$update) $indirec=str_replace('@@', '@', $indirec); // Escaped @ signs (only if importing from file)
	
	// Replace TAG_FORMAL_NAME (as sometimes created by FTM) with TAG
	if (is_array($TRANSLATE_TAGS)) {
		foreach ($TRANSLATE_TAGS as $tag_full=>$tag_abbr)
			$indirec=preg_replace("/^(\d+ (@[^@]+@ )?){$tag_full}\b/m", '$1'.$tag_abbr, $indirec);
	}

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

	// Update the dates and places tables.
	update_places($gid, $indirec);
	update_dates($gid, $indirec);

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
		$res = $gGedcom->mDb->query($sql, array( $gid, $rfn, $gGedcom->mGEDCOMId ) );
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
				$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId, $name[0], $name[1], $name[2], $name[3] ) );
			}
			$j++;
/*			
			// Calculate Soundex Values and insert them into the database.
			$firstName = explode("/", $name[0]);
			$firstName = $firstName[0];
			$lastName = $name[2];
			
			
			// Start building the SQL Insert
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "soundex VALUES( ?, ?, ?, ?, ?, ?, ? )" .
			$param = array( $gid, $indexval, $gGedcom->mGEDCOMId );
			
			// Ensure there is a firstname or lastname.
			// If there is no name in a field, it will contain the string "@N.N."
			
			if(trim($firstName) != "@P.N.")
			{
				Character_Substitute($firstName);
				// Split the first name array
				$fnames = explode(" ", $firstName);
				
				$std_array = array();
				$firstName_std_soundex = "";
				$firstName_dm_soundex = "";
				$dm_array = array();
				
				$combined = "";
				foreach($fnames as $fn)
				{
					if(!empty($fn))
					{
						$std_array[] = soundex($fn);
						$dm_array = array_merge($dm_array, DMSoundex($fn));
					}
				}
				$fn_nospaces = strtr($firstName, " ", "");
				$dm_array = array_merge($dm_array, DMSoundex($fn_nospaces));
				
				$std_array[] = soundex($fn_nospaces);
				$firstName_dm_soundex .= ":" . implode(":", array_unique($dm_array));
				
				$std_array = array_unique($std_array);
				$firstName_std_soundex = implode(":", $std_array);
				$param[] = trim($firstName_std_soundex, ":");
				$param[] = trim($firstName_dm_soundex,":");
			}
			else
			{
				$param[] = "NULL";
				$param[] = "NULL";
			}
			
			if(trim($lastName) != "@N.N.")
			{
				Character_Substitute($lastName);
				$lnames = explode(" ", $lastName);
				$lastName_std_soundex = "";
				$lastName_dm_soundex = "";
				$dm_array = array();
				$std_array = array();
				
				foreach($lnames as $ln)
				{
					$std_array[] = soundex($ln);
					$dm_array = array_merge($dm_array, DMSoundex($ln));
				}
				
				$ln_nospaces = strtr($lastName, " ", "");
				
				$std_array[] =  soundex($ln_nospaces);
				$dm_array = array_merge($dm_array, DMSoundex($ln_nospaces));
				$lastName_std_soundex .= ":" . implode(":", array_unique($std_array));
				$lastName_dm_soundex .= ":" . implode(":", array_unique($dm_array));
				
				$param[] = trim( $lastName_std_soundex,":" );
				$param[] = trim( $lastName_dm_soundex,":" );
			}
			else
			{
				$param[] = "NULL";
				$param[] = "NULL";
			}

			$res = $gGedcom->mDb->query( $sql, $param );
*/
		}
		$indi["names"] = $names;
		$indi["isdead"] = $isdead;
		$indi["gedcom"] = $indirec;
		$indi["gedfile"] = $gGedcom->mGEDCOMId;
		if ($USE_RIN) {
			$ct = preg_match("/1 RIN (.*)/", $indirec, $match);
			if ($ct > 0)
				$rin = trim($match[1]);
			else
				$rin = $gid;
			$indi["rin"] = $rin;
		} else
			$indi["rin"] = $gid;

		$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "individuals VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		$res = $gGedcom->mDb->query($sql, array( $gid, $indi["gedfile"], $indi["rin"], $names[0][0], -1, $indi["gedcom"], $names[0][1], $names[0][2], 0 ) );

		//-- PEAR supports prepared statements in mysqli we will use this code instead of the code above
		//if (!isset($prepared_statement)) $prepared_statement = $gGedcom->mDb->prepare("INSERT INTO ".PHPGEDVIEW_DB_PREFIX."individuals VALUES (?,?,?,?,?,?,?,?)");
		//$data = array( $gid, $indi["file"], $indi["rin"], $names[0][0], -1, $indi["gedcom"], $names[0][1], $names[0][2]);
		//$res =& $gGedcom->mDb->execute($prepared_statement, $data);
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
			$nchi = get_gedcom_value("NCHI", 1, $indirec);
			if (!empty($nchi)) $ct = $nchi;
			$fam = array ();
			$fam["HUSB"] = $parents["HUSB"];
			$fam["WIFE"] = $parents["WIFE"];
			$fam["CHIL"] = $chil;
			$fam["gedcom"] = $indirec;
			$fam["gedfile"] = $gGedcom->mGEDCOMId;
			//$famlist[$gid] = $fam;
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "families (f_id, f_file, f_husb, f_wife, f_chil, f_gedcom, f_numchil) VALUES ( ?, ?, ?, ?, ?, ?, ? )";
			$res = $gGedcom->mDb->query($sql, array ( $gid, $fam["gedfile"], $fam["HUSB"], $fam["WIFE"], $fam["CHIL"], $fam["gedcom"], $ct ));

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
				$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId, $name, $indirec ));

			} else
				if ($type == "OBJE") {
					//-- don't duplicate OBJE records
					//-- OBJE records are imported by update_media function
				} else
				if ( isset($type) && preg_match("/_/", $type) == 0) {
						if ($type == "HEAD") {
							$ct = preg_match("/1 DATE (.*)/", $indirec, $match);
							if ($ct == 0) {
								$indirec = trim($indirec);
								$indirec .= "\r\n1 DATE " . date("d") . " " . date("M") . " " . date("Y");
							}
						}
						if ($gid=="") $gid = $type;
						$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "other VALUES ( ?, ?, ?, ? )";
						$res = $gGedcom->mDb->query( $sql, array( $gid, $gGedcom->mGEDCOMId, $type, $indirec ) );

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
	global $USE_RIN, $indilist, $FILE, $gGedcom;

	$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "names VALUES( ?, ?, ?, ?, ?,'C' )";
	$res = $gGedcom->mDb->query($sql, array ( $gid, $gGedcom->mGEDCOMId, $newname, $letter, $surname ) );

	$sql = "UPDATE " . PHPGEDVIEW_DB_PREFIX . "individuals SET i_gedcom=? WHERE i_id=? AND i_file=?";
	$res = $gGedcom->mDb->query($sql, array( $indirec, $gid, $gGedcom->mGEDCOMId ));

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
function update_places($gid, $indirec) {
	global $gGedcom;
	global $FILE, $placecache, $TBLPREFIX, $DBCONN, $gGedcom;

	if (!isset($placecache)) $placecache = array();
	$personplace = array();
	// import all place locations, but not control info such as
	// 0 HEAD/1 PLAC or 0 _EVDEF/1 PLAC
	$pt = preg_match_all("/[2-9] PLAC (.+)/", $indirec, $match, PREG_SET_ORDER);
	for ($i = 0; $i < $pt; $i++) {
		$place = trim($match[$i][1]);
		$lowplace = str2lower($place);
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
					$res2 = $gGedcom->mDb->query($sql, array( $parent_id, $gid, $gGedcom->mGEDCOMId ));
				}
				$level++;
				continue;
			}

			//-- only search the database while we are finding places in it
			if ($search) {
				//-- check if this place and level has already been added
				$sql = 'SELECT p_id FROM '.PHPGEDVIEW_DB_PREFIX.'places WHERE p_level=? AND p_file=? AND p_parent_id=? AND p_place LIKE ?';
				$res = $gGedcom->mDb->query($sql, array( $level, $gGedcom->mGEDCOMId, $parent_id, $place ) );
				if ($res->numRows()>0) {
					$row = $res->fetchRow();
					$p_id = $row['p_id'];
				}
				else $search = false;
			}

			//-- if we are not searching then we have to insert the place into the db
			if (!$search) {
				$std_soundex = soundex($place);
				$dm_soundex = DMSoundex($place);
				$p_id = get_next_id("places", "p_id");
				$sql = 'INSERT INTO '.PHPGEDVIEW_DB_PREFIX.'places (p_id, p_place, p_level, p_parent_id, p_file) VALUES( ?, ?, ?, ?, ? )';
				$res2 = $gGedcom->mDb->query($sql, array( $p_id, $place, $level, $parent_id, $gGedcom->mGEDCOMId ) );
					}

			$sql = 'INSERT INTO ' . PHPGEDVIEW_DB_PREFIX . 'placelinks VALUES( ?, ?, ? )';
			$res2 = $gGedcom->mDb->query($sql, array( $p_id, $gid, $gGedcom->mGEDCOMId ) );
			//-- increment the level and assign the parent id for the next place level
			$parent_id = $p_id;
			$placecache[$key] = $p_id;
			$personplace[$key]=1;
			$level++;
		}
	}
}

/**
 * extract all date info from the given record and insert them
 * into the dates table
 * @param string $indirec
 */
function update_dates($gid, $indirec) {
	global $FILE, $gGedcom;
	$count = 0;
	$pt = preg_match("/\d DATE (.*)/", $indirec, $match);
	if ($pt == 0)
		return 0;
	$facts = get_all_subrecords($indirec, "", false, false, false);
	foreach ($facts as $f => $factrec) {
		if (preg_match("/1 (\w+)/", $factrec, $match)) {
			$fact = trim($match[1]);
			// 1 FACT/2 TYPE XXXX gets recorded as XXXX to enable searching
			if (($fact=='FACT' || $fact=='EVEN') && preg_match("/\n2 TYPE (\w+)/", $factrec, $match) && array_key_exists($match[1], $factarray))
				$fact=$match[1];
			$pt = preg_match_all("/2 DATE (.*)/", $factrec, $match, PREG_SET_ORDER);
			for ($i = 0; $i < $pt; $i++) {
				$geddate=new GedcomDate($match[$i][1]);
				$dates=array($geddate->date1);
				if ($geddate->date2)
					$dates[]=$geddate->date2;
				foreach($dates as $date)
					$gGedcom->mDb->query("INSERT INTO `".PHPGEDVIEW_DB_PREFIX."dates` (d_day,d_month,d_mon,d_year,d_julianday1,d_julianday2,d_fact,d_gid,d_file,d_type)VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )", 
						array( $date->d, $date->Format('O') ,$date->m, $date->y, $date->minJD, $date->maxJD, $fact, $gid, $gGedcom->mGEDCOMId, $date->CALENDAR_ESCAPE) );
			}
		}
	}
}

/**
 * Insert media items into the database
 * This method is used in conjuction with the gedcom import/update routines
 * @param string $objrec	The OBJE subrecord
 * @param int $objlevel		The original level of this OBJE
 * @param boolean $update	Whether or not this is an update or an import
 * @param string $gid		The XREF ID of the record this OBJE is related to
 * @param int $count		The count of OBJE records in the parent record
 */
function insert_media($objrec, $objlevel, $update, $gid, $count) {
	global $TBLPREFIX, $media_count, $FILE, $found_ids, $fpnewged;

	//-- check for linked OBJE records
	//-- linked records don't need to insert to media table
	$ct = preg_match("/OBJE @(.*)@/", $objrec, $match);
	if ($ct>0) {
		//-- get the old id
		$old_m_media = $match[1];
		$objref = $objrec;
		/**
		 * Hiding some code in order to fix a very annoying bug
		 * [ 1579889 ] Upgrading breaks Media links
		 *
		 * Don't understand the logic of renumbering media objects ??
		 *
		//-- if this is an import not an update get the updated ID
		if (!$update) {
			if (isset ($found_ids[$old_m_media])) {
				$new_m_media = $found_ids[$old_m_media]["new_id"];
			} else {
				$new_m_media = get_new_xref("OBJE");
				$found_ids[$old_m_media]["old_id"] = $old_m_media;
				$found_ids[$old_m_media]["new_id"] = $new_m_media;
			}
		}
		//-- an update so the ID won't change
		else $new_m_media = $old_m_media;
		**/
		$new_m_media = $old_m_media;
		$m_media = $new_m_media;
		//print "LINK: old $old_m_media new $new_m_media $objref<br />";
		if ($m_media != $old_m_media) $objref = preg_replace("/@$old_m_media@/", "@$m_media@", $objref);
	}
	//-- handle embedded OBJE records
	else {
		$m_media = get_new_xref("OBJE", true);
		$objref = subrecord_createobjectref($objrec, $objlevel, $m_media);

		//-- restructure the record to be a linked record
		$objrec = preg_replace("/ OBJE/", " @" . $m_media . "@ OBJE", $objrec);
		//-- renumber the lines
		$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
		
		//-- check if another picture with the same file and title was previously imported
		$media = new Media($objrec);
		$new_media = Media :: in_obje_list($media);
		if ($new_media === false) {
			//-- add it to the media database table
			$m_id = get_next_id("media", "m_id");
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
			$sql .= " VALUES( ?, ?, ?, ?, ?, ?, ? )";
			$res = $gGedcom->mDb->query($sql, array ( $mm_id, $m_media, $media->ext, $media->title, $media->file[$FILE]['id'], $objref ));
			$media_count++;
			//-- if this is not an update then write it to the new gedcom file
			if (!$update && !empty ($fpnewged))
				fwrite($fpnewged, trim($objrec) . "\r\n");
			//print "LINE ".__LINE__;
		} else {
			//-- already added so update the local id
			$objref = preg_replace("/@$m_media@/", "@$new_media@", $objref);
			$m_media = $new_media;
		}
	}
	if (isset($m_media)) {
	//-- add the entry to the media_mapping table
	$mm_id = get_next_id("media_mapping", "mm_id");
	$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec) VALUES ( ?, ?, ?, ?, ?, ? )";
	$res = $gGedcom->mDb->query($sql, array ( $mm_id, $m_media, $gid, $count,  $gGedcom->mGEDCOMId, $objref ) );
	return $objref;
	}
	else {
		print "Media reference error ".$objrec;
		return "";
	}
} 
/**
 * import media items from record
 * @todo Decide whether or not to update the original gedcom file
 * @return string	an updated record
 */
function update_media($gid, $indirec, $update = false, $keepmedia = false) {
	global $FILE, $gGedcom, $MEDIA_ID_PREFIX, $media_count, $found_ids;
	global $zero_level_media, $fpnewged, $objelist, $MAX_IDS;

	if (!isset ($media_count))
		$media_count = 0;
	if (!isset ($found_ids))
		$found_ids = array ();
	if (!isset ($zero_level_media))
		$zero_level_media = false;
	if (!$update && !isset ($MAX_IDS["OBJE"])) {
		if (!$keepmedia) $MAX_IDS["OBJE"] = 1;
		else {
			$sql = "SELECT ni_id FROM " . PHPGEDVIEW_DB_PREFIX . "nextid WHERE ni_type='OBJE' AND ni_gedfile='".$gGedcom->mGEDCOMId."'";
			$res = $gGedcom->mDb->query($sql);
			$row = $res->fetchRow();
			$MAX_IDS["OBJE"] = $row[0];
			$res->free();
		}
	}

//		print substr($indirec, 0, 15)."<br />\n";
	//-- handle level 0 media OBJE seperately
	$ct = preg_match("/0 @(.*)@ OBJE/", $indirec, $match);
	if ($ct > 0) {
		$old_m_media = $match[1];
		$m_id = get_next_id("media", "m_id");
		/**
		 * Hiding some code in order to fix a very annoying bug
		 * [ 1579889 ] Upgrading breaks Media links
		 *
		 * Don't understand the logic of renumbering media objects ??
		 *
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
		**/
		$new_m_media = $old_m_media;
		//print "RECORD: old $old_m_media new $new_m_media<br />";
		$indirec = preg_replace("/@" . $old_m_media . "@/", "@" . $new_m_media . "@", $indirec);
		$media = new Media($indirec);
		//--check if we already have a similar object
		$new_media = Media :: in_obje_list($media);
		if ($new_media === false) {
			$objelist[$new_m_media] = $media;
			$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
			$sql .= " VALUES( ?, ?, ?, ?, ?, ?, ? )";
			$res = $gGedcom->mDb->query($sql, array ( $m_id, $new_m_media, $media->ext, $media->title, $media->file, $gGedcom->mGEDCOMId, $indirec ) );
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

	if ($keepmedia) {
		$sql = "SELECT mm_media, mm_gedrec FROM ".$TBLPREFIX."media_mapping WHERE mm_gid='".$gid."' AND mm_gedfile='".$gGedcom->mGEDCOMId."'";
		$res = dbquery($sql);
		$old_linked_media = array();
		while($row =& $res->fetchRow()) {
			$old_linked_media[] = $row;
		}
		$res->free();
	}

	//-- check to see if there are any media records
	//-- if there aren't any media records then don't look for them just return
	$pt = preg_match("/\d OBJE/", $indirec, $match);
	if ($pt > 0) {
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
			$level = $line{0};
			//-- putting this code back since $objlevel, $objrec, etc vars will be 
			//-- reset in sections after this
			if ($objlevel>0 && ($level<=$objlevel)) {
				$objref = insert_media($objrec, $objlevel, $update, $gid, $count);
				$count++;
				// NOTE: Add the new media object to the record
				//$newrec .= $objlevel . " OBJE @" . $m_media . "@\r\n";
				$newrec .= $objref;

				// NOTE: Set the details for the next media record
				$objlevel = 0;
				$inobj = false;
			}
			if (preg_match("/[1-9]\sOBJE\s@(.*)@/", $line, $match) != 0) {
					// NOTE: Set object level
					$objlevel = $level;
					$inobj = true;
					$objrec = $line . "\r\n";
			} 
			else if (preg_match("/[1-9]\sOBJE/", $line, $match)) {
				// NOTE: Set the details for the next media record
				$objlevel = $level;
				$inobj = true;
				$objrec = $line . "\r\n";
			} else {
				$ct = preg_match("/(\d+)\s(\w+)(.*)/", $line, $match);
				if ($ct > 0) {
					if ($inobj)
						$objrec .= $line . "\r\n";
					else $newrec .= $line . "\r\n";
				}
				else $newrec .= $line . "\r\n";
			}
		}
	}
	//-- make sure the last line gets handled
	if ($inobj) {
		$objref = insert_media($objrec, $objlevel, $update, $gid, $count);
		$count++;
		$newrec .= $objref;

		// NOTE: Set the details for the next media record
		$objlevel = 0;
		$inobj = false;
	}
	}
	else $newrec = $indirec;
	
	if ($keepmedia) {
		$newrec = trim($newrec)."\r\n";
		foreach($old_linked_media as $i=>$row) {
			$newrec .= trim($row[1])."\r\n";
		}
	}
	
	return $newrec;
}
/**
 * Create database schema
 *
 * function that checks if the database exists and creates tables
 * but has been replaced by the bitweaver package installer
 */
function setup_database() {
	global $pgv_lang, $gGedcom, $gBitDbType;
}
/**
 * delete a gedcom from the database
 *
 * deletes all of the imported data about a gedcom from the database
 * @param string $FILE	the gedcom to remove from the database
 * @param boolean $keepmedia	Whether or not to keep media and media links in the tables
 */
function empty_database($FILE, $keepmedia=false) {
	global $gGedcom;

	$FILE = $gGedcom->mGEDCOMId;
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "individuals WHERE i_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "families WHERE f_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "sources WHERE s_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "other WHERE o_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "places WHERE p_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "names WHERE n_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "dates WHERE d_file='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	if (!$keepmedia) {
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media WHERE m_gedfile='$FILE'";
	$res = $gGedcom->mDb->query($sql);

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media_mapping WHERE mm_gedfile='$FILE'";
	$res = $gGedcom->mDb->query($sql);
	}
	else {
		//-- make sure that we keep the correct IDs for media
		$sql = "SELECT ni_id FROM " . PHPGEDVIEW_DB_PREFIX . "nextid WHERE ni_type='OBJE' AND ni_gedfile='".$FILE."'";
		$res = $gGedcom->mDb->dbquery($sql);
		if ($res->numRows() > 0) {
			$row =& $res->fetchRow();
			$num = $row[0];
		}
	}

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "nextid WHERE ni_gedfile = ?";
	$res = $gGedcom->mDb->query($sql, array( $FILE ));
	if ($keepmedia && isset($num)) {
		$sql = "INSERT INTO " . PHPGEDVIEW_DB_PREFIX . "nextid VALUES( ?, 'OBJE', ? )";
		$res2 = $gGedcom->mDb->query($sql, array( $num-1, $FILE ));
	}
	
//	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "soundex WHERE sx_file='$FILE'";
//	$res = $gGedcom->mDb->query($sql);
	
	//-- clear all of the cache files for this gedcom
	clearCache();
}

/**
 * read the contents of a gedcom file
 *
 * opens a gedcom file and reads the contents into the <var>$fcontents</var> global string
 */
function read_gedcom_file() {
	global $fcontents;
	$fcontents = "";
	if (isset($gGedcom)) {
		//-- only allow one thread to write the file at a time
		$mutex = new Mutex($GEDCOM);
		$mutex->Wait();
		$fp = fopen($gGedcom->getPath(), "r");
		$fcontents = fread($fp, filesize($gGedcom->getPath()));
		fclose($fp);
		$mutex->Release();
	}
}

//-------------------------------------------- write_file
//-- this function writes the $fcontents back to the
//-- gedcom file
function write_file() {
	global $fcontents, $GEDCOM, $pgv_changes, $INDEX_DIRECTORY;

	if (empty($fcontents)) return;
	if (preg_match("/0 TRLR/", $fcontents)==0) $fcontents.="0 TRLR\n";
	//-- write the gedcom file
	if (!is_writable($gGedcom->getPath())) {
		print "ERROR 5: GEDCOM file is not writable.  Unable to complete request.\n";
		AddToChangeLog("ERROR 5: GEDCOM file is not writable.  Unable to complete request. ->" . PGV_USER_NAME ."<-");
		return false;
	}
	//-- only allow one thread to write the file at a time
	$mutex = new Mutex($GEDCOM);
	$mutex->Wait();
	//-- what to do if file changed while waiting
	
	$fp = fopen($gGedcom->getPath(), "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request.\n";
		AddToChangeLog("ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request. ->" . PGV_USER_NAME ."<-");
		return false;
	}
	$fl = @flock($fp, LOCK_EX);
	if (!$fl) {
//		print "ERROR 7: Unable to obtain file lock.\n";
		AddToChangeLog("ERROR 7: Unable to obtain file lock. ->" . getUserName() ."<-");
//		fclose($fp);
//		return false;
	}
	$fw = fwrite($fp, $fcontents);
	if ($fw===false) {
		print "ERROR 7: Unable to write to GEDCOM file.\n";
		AddToChangeLog("ERROR 7: Unable to write to GEDCOM file. ->" . getUserName() ."<-");
		$fl = @flock($fp, LOCK_UN);
		fclose($fp);
		return false;
	}
	$fl = @flock($fp, LOCK_UN);
	fclose($fp);
	//-- always release the mutex
	$mutex->Release();
	$logline = AddToLog($gGedcom->getPath()." updated by >".getUserName()."<");
 	if (!empty($COMMIT_COMMAND)) check_in($logline, basename($gGedcom->getPath()), dirname($gGedcom->getPath()));

	return true;;
}
/**
 * Accpet changed gedcom record into database
 *
 * This function gets an updated record from the gedcom file and replaces it in the database
 * @author John Finlay
 * @param string $cid The change id of the record to accept
 */
function accept_changes($cid) {
	global $pgv_changes, $GEDCOM, $FILE, $gGedcom, $MEDIA_ID_PREFIX;
	global $COMMIT_COMMAND, $INDEX_DIRECTORY;

	if (isset ($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[count($changes) - 1];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
		}
		$FILE = $GEDCOM;
		$gid = $change["gid"];
		$indirec = $change["undo"];
		if (empty ($indirec)) {
			$indirec = find_gedcom_record($gid);
		}

		update_record($indirec, $change["type"]=="delete");
		
		//-- write the changes back to the gedcom file
		if ($SYNC_GEDCOM_FILE) {
			if (!isset($manual_save) || $manual_save==false) {
				//-- only allow one thread to accept changes at a time
				$mutex = new Mutex("accept_changes");
				$mutex->Wait();
			}
			
			if (empty($fcontents)) read_gedcom_file();
			if ($change["type"]=="delete") {
				$pos1 = strpos($fcontents, "\n0 @".$gid."@");
				if ($pos1!==false) {
					$pos2 = strpos($fcontents, "\n0", $pos1+5);
					if ($pos2===false) {
						$fcontents = substr($fcontents, 0, $pos1+1)."0 TRLR";
						AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct");
					}
					else $fcontents = substr($fcontents, 0, $pos1+1).substr($fcontents, $pos2+1);
				}
				else {
					AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct.  Deleted gedcom record $gid was not found in the gedcom file.");
				}
			}
			else if ($change["type"]=="append") {
				$pos1 = strpos($fcontents, "\n0 TRLR");
				$fcontents = substr($fcontents, 0, $pos1+1).trim($indirec)."\r\n0 TRLR";
			}
			else if ($change["type"]=="replace") {
				$pos1 = strpos($fcontents, "\n0 @".$gid."@");
				if ($pos1!==false) {
					$pos2 = strpos($fcontents, "\n0", $pos1+5);
					if ($pos2===false) {
						$fcontents = substr($fcontents, 0, $pos1+1)."0 TRLR";
						AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct");
					}
					else $fcontents = substr($fcontents, 0, $pos1+1).trim($indirec)."\r\n".substr($fcontents, $pos2+1);
				}
				else {
					//-- attempted to replace a record that doesn't exist
					AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct.  Replaced gedcom record $gid was not found in the gedcom file.");
					$pos1 = strpos($fcontents, "\n0 TRLR");
					$fcontents = substr($fcontents, 0, $pos1+1).trim($indirec)."\r\n0 TRLR";
					AddToLog("Gedcom record $gid was appended back to the GEDCOM file.");
				}
			}
			if (!isset($manual_save) || $manual_save==false) {
				write_file();
				$mutex->Release();
			}
		}

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
			}
*/		}

		unset ($pgv_changes[$cid]);
		if (!isset($manual_save) || $manual_save==false) write_changes();
		if (isset ($_SESSION["recent_changes"]["user"][$GEDCOM]))
			unset ($_SESSION["recent_changes"]["user"][$GEDCOM]);
		if (isset ($_SESSION["recent_changes"]["gedcom"][$GEDCOM]))
			unset ($_SESSION["recent_changes"]["gedcom"][$GEDCOM]);
		$logline = AddToLog("Accepted change $cid " . $change["type"] . " into database ->" . getUserName() . "<-");
		if (!empty ($COMMIT_COMMAND))
			check_in($logline, $GEDCOM, dirname($gGedcom->getPath()));
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
	global $GEDCOM, $gGedcom, $FILE;

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
	$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

	$placeids = array ();
	while ($row = & $res->fetchRow()) {
		$placeids[] = $row['pl_p_id'];
	}
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_gid=? AND pl_file=?";
	$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "dates WHERE d_gid=? AND d_file=?";
	$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

	//-- delete any unlinked places
	foreach ($placeids as $indexval => $p_id) {
		$sql = "SELECT count(pl_p_id) FROM " . PHPGEDVIEW_DB_PREFIX . "placelinks WHERE pl_p_id=? AND pl_file=?";
		$res = $gGedcom->mDb->query($sql, array( $p_id, $gGedcom->mGEDCOMId ) );

		$row = & $res->fetchRow();
		if ($row['count'] == 0) {
			$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "places WHERE p_id=? AND p_file=?";
			$res = $gGedcom->mDb->query($sql, array( $p_id, $gGedcom->mGEDCOMId ) );

		}
	}

	//-- delete any media mapping references
	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media_mapping WHERE mm_gid LIKE ? AND mm_gedfile=?";
	$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

	$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "remotelinks WHERE r_gid=? AND r_file=?";
	$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

	if ($type == "INDI") {
		$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "individuals WHERE i_id LIKE ? AND i_file=?";
		$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

		$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "names WHERE n_gid LIKE ? AND n_file=?";
		$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );
		
		$sql = "DELETE FROM ".$TBLPREFIX."soundex WHERE sx_i_id LIKE ? AND sx_file = ?";
		$res = $gGedcom->mDb->query( $sql, array( $gid, $gGedcom->mGEDCOMId ) );

	} else
		if ($type == "FAM") {
			$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "families WHERE f_id LIKE ? AND f_file=?";
			$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

		} else
			if ($type == "SOUR") {
				$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "sources WHERE s_id LIKE ? AND s_file=?";
				$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

			} else
				if ($type == "OBJE") {
					$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "media WHERE m_media LIKE ? AND m_gedfile=?";
					$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );
				} else {
					$sql = "DELETE FROM " . PHPGEDVIEW_DB_PREFIX . "other WHERE o_id LIKE ? AND o_file=?";
					$res = $gGedcom->mDb->query($sql, array( $gid, $gGedcom->mGEDCOMId ) );

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
}

// Create a pseudo-random UUID, as per RFC4122
function uuid() {
	return sprintf(
		//'%02X%02X%02X%02X-%02X%02X-%02X%02X-%02X%02X-%02X%02X%02X%02X%02X%02X', // RFC4122 format (with hyphens)
		'%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X', // PAF format (without hyphens)
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255)&0x3f|0x80, // Set the version to random (10xxxxxx)
		rand(0, 255),
		rand(0, 255)&0x0f|0x40, // Set the variant to RFC4122 (0100xxxx)
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255)
	);
}

/**
 * parse out specific subrecords (NOTE, _PRIM, _THUM) from a given OBJE record
 * 
 * @author Joseph King 
 * @param string $objrec the OBJE record to retrieve the subrecords from
 * @param int $objlevel the level of the OBJE record
 * @param string $m_media that media id of the OBJE record
 * @return string containing NOTE, _PRIM, and _THUM subrecords parsed from the passed object record
 */
function subrecord_createobjectref($objrec, $objlevel, $m_media){
	
	//- level of subrecords is object record level + 1
	$level = $objlevel + 1;
	
	//- get and concatenate NOTE subrecords
	$n = 1;
	$nt = "";
	$note = "";
	do
	{
		$nt = get_sub_record($level, $level . " NOTE", $objrec, $n);
		if($nt != "") $note = $note . trim($nt)."\r\n";
		$n++;
	}while($nt != "");
	//- get and concatenate PRIM subrecords
	$n = 1;
	$pm = "";
	$prim = "";
	do
	{
		$pm = get_sub_record($level, $level . " _PRIM", $objrec, $n);
		if($pm != "") $prim = $prim . trim($pm)."\r\n";
		$n++;
	}while($pm != "");
	//- get and concatenate THUM subrecords
	$n = 1;
	$tm = "";
	$thum = "";
	do
	{
		$tm = get_sub_record($level, $level . " _THUM", $objrec, $n);
		if($tm != ""){
			//- call image cropping function ($tm contains thum data)
			$thum = $thum . trim($tm)."\r\n";
		}
		$n++;
	}while($tm != "");
	//- add object reference
	$objmed = addslashes($objlevel . ' OBJE @' . $m_media . "@\r\n" . $note . $prim . $thum);
	
	//- return the object media reference
	return $objmed;
}

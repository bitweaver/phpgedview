<?php
/**
* Returns data for autocompletion
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
* @subpackage Edit
* @version $Id$
*/

require './config.php';
header("Content-Type: text/plain; charset=$CHARACTER_SET");

// We have finished writing to $_SESSION, so release the lock
session_write_close();

//-- args
$FILTER=safe_GET('q', PGV_REGEX_UNSAFE); // we can search on '"><& etc.
$FILTER=UTF8_strtoupper($FILTER);
$OPTION=safe_GET('option');
$FORMAT=safe_GET('fmt');
$FIELD =safe_GET('field');

//-- database query
define('PGV_AUTOCOMPLETE_LIMIT', 50);

switch ($FIELD) {
case 'INDI':
	$data=autocomplete_INDI($FILTER, $OPTION);
	break;
case 'FAM':
	$data=autocomplete_FAM($FILTER, $OPTION);
	break;
case 'NOTE':
	$data=autocomplete_NOTE($FILTER);
	break;
case 'SOUR':
	$data=autocomplete_SOUR($FILTER);
	break;
case 'SOUR_TITL':
	$data=autocomplete_SOUR($FILTER);
	break;
case 'INDI_BURI_CEME':
	$data=autocomplete_INDI_BURI_CEME($FILTER);
	break;
case 'INDI_SOUR_PAGE':
	$data=autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION);
	break;
case 'FAM_SOUR_PAGE':
	$data=autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION);
	break;
case 'SOUR_PAGE':
	$data=autocomplete_SOUR_PAGE($FILTER, $OPTION);
	break;
case 'REPO':
	$data=autocomplete_REPO($FILTER);
	break;
case 'REPO_NAME':
	$data=autocomplete_REPO_NAME($FILTER);
	break;
case 'OBJE':
	$data=autocomplete_OBJE($FILTER);
	break;
case 'IFSRO':
	$data=autocomplete_IFSRO($FILTER);
	break;
case 'SURN':
	$data=autocomplete_SURN($FILTER);
	break;
case 'GIVN':
	$data=autocomplete_GIVN($FILTER);
	break;
case 'NAME':
	$data=autocomplete_NAME($FILTER);
	break;
case 'PLAC':
	$data=autocomplete_PLAC($FILTER, $OPTION);
	break;
default:
	die("Bad arg: field={$FIELD}");
}

//-- sort
$data = array_unique($data);
uasort($data, "stringsort");

//-- output
if ($FORMAT=="json") {
	//echo json_encode(array($FILTER, $data));//does not seem to work for some reason
	$results=array();
	foreach ($data as $k=>$v) {
		$results[]=$v;
	}
	printf('["%s", %s]',$FILTER, json_encode($results));
} else {
	foreach ($data as $k=>$v) {
		echo "$v|$k\n";
	}
}

/**
* returns INDIviduals matching filter
* @return Array of string
*/
function autocomplete_INDI($FILTER, $OPTION) {
	global $TBLPREFIX, $pgv_lang, $MAX_ALIVE_AGE, $gBitDb;

	// when adding ASSOciate $OPTION may contain :
	// current INDI/FAM [, current event date]
	if ($OPTION) {
		list($pid, $event_date) = explode("|", $OPTION."|");
		$record=GedcomRecord::getInstance($pid); // INDI or FAM
		$tmp=new GedcomDate($event_date);
		$event_jd=$tmp->JD();
		// INDI
		$indi_birth_jd = 0;
		if ($record && $record->getType()=="INDI") {
			$indi_birth_jd=$record->getEstimatedBirthDate()->minJD();
		}
		// HUSB & WIFE
		$husb_birth_jd = 0;
		$wife_birth_jd = 0;
		if ($record && $record->getType()=="FAM") {
			$husb=$record->getHusband();
			if ($husb) {
				$husb_birth_jd = $husb->getEstimatedBirthDate()->minJD();
			}
			$wife=$record->getWife();
			if ($wife) {
				$wife_birth_jd = $wife->getEstimatedBirthDate()->minJD();
			}
		}
	}

	$sql=
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
		" FROM {$TBLPREFIX}individuals, {$TBLPREFIX}name".
		" WHERE (i_id LIKE ? OR n_sort LIKE ?)".
		" AND i_id=n_id AND i_file=n_file AND i_file=?".
		" ORDER BY n_sort";
	$rows = $gBitDb->query($sql
			, array("%{$FILTER}%", "%{$FILTER}%", PGV_GED_ID)
			, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRow() ) {
		$person=Person::getInstance( $row );
		if ($person->canDisplayName()) {
			// filter ASSOciate
			if ($OPTION && $event_jd) {
				// no self-ASSOciate
				if ($pid && $person->getXref()==$pid) {
					continue;
				}
				// filter by birth date
				$person_birth_jd=$person->getEstimatedBirthDate()->minJD();
				if ($person_birth_jd) {
					// born after event or not a contemporary
					if ($event_jd && $person_birth_jd>$event_jd) {
						continue;
					} elseif ($indi_birth_jd && abs($indi_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					} elseif ($husb_birth_jd && $wife_birth_jd && abs($husb_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365 && abs($wife_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					} elseif ($husb_birth_jd && abs($husb_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					} elseif ($wife_birth_jd && abs($wife_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					}
				}
				// filter by death date
				$person_death_jd=$person->getEstimatedDeathDate()->MaxJD();
				if ($person_death_jd) {
					// dead before event or not a contemporary
					if ($event_jd && $person_death_jd<$event_jd) {
						continue;
					} elseif ($indi_birth_jd && $person_death_jd<$indi_birth_jd) {
						continue;
					} elseif ($husb_birth_jd && $wife_birth_jd && $person_death_jd<$husb_birth_jd && $person_death_jd<$wife_birth_jd) {
						continue;
					}	elseif ($husb_birth_jd && $person_death_jd<$husb_birth_jd) {
						continue;
					} elseif ($wife_birth_jd && $person_death_jd<$wife_birth_jd) {
						continue;
					}
				}
			}
			// display
			$data[$person->getXref()]=$person->getFullName();
			if ($OPTION && $event_date && $person->getBirthDate()->isOK()) {
				$data[$person->getXref()].=" <span class=\"age\">(".$pgv_lang["age"]." ".$person->getBirthDate()->MinDate()->getAge(false, $event_jd).")</span>";
			} else {
				$data[$person->getXref()].=" <u>".ltrim($person->getBirthYear(), "0")."-".ltrim($person->getDeathYear(), "0")."</u>";
			}
		}
	}
	return $data;
}

/**
* returns FAMilies matching filter
* @return Array of string
*/
function autocomplete_FAM($FILTER, $OPTION) {
	global $TBLPREFIX, $gBitDb;

	//-- search for INDI names
	$ids=array_keys(autocomplete_INDI($FILTER, $OPTION));

	$vars=array();
	if (empty($ids)) {
		//-- no match : search for FAM id
		$where = "f_id LIKE ?";
		$vars[]="%{$FILTER}%";
	} else {
		//-- search for spouses
		$qs=implode(',', array_fill(0, count($ids), '?'));
		$where = "(f_husb IN ($qs) OR f_wife IN ($qs))";
		$vars=array_merge($vars, $ids, $ids);
	}

	$sql="SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families WHERE {$where} AND f_file=?";
	$vars[]=PGV_GED_ID;
	$rows =
		$gBitDb->query($sql, $vars, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRow() ) {
		$family = Family::getInstance($row);
		if ($family->canDisplayName()) {
			$data[$row['xref']] =
				$family->getFullName().
				" <u>".
				ltrim($family->getMarriageYear(), "0").
				"</u>";
		}
	}
	return $data;
}

/**
* returns NOTEs (Shared) matching filter
* @return Array of string
*/
function autocomplete_NOTE($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE o_gedcom LIKE '%{$FILTER}%' AND o_type='NOTE' AND o_file=".PGV_GED_ID;
	$rows =	$gBitDb->query( $sql
		,array("%{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRow() ) {
		$note = Note::getInstance( $row );
		if ($note->canDisplayName()) {
			$data[$row['xref']] = $note->getFullName();
		}
	}
	return $data;
}

/**
* returns SOURces matching filter
* @return Array of string
*/
function autocomplete_SOUR($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}sources WHERE (s_name LIKE ? OR s_id LIKE ?) AND s_file=? ORDER BY s_name";
	$rows =	$gBitDb->query( $sql
		,array("%{$FILTER}%", "%{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$source = Source::getInstance( $row );
		if ($source->canDisplayName()) {
			$data[$row['xref']] = $source->getFullName();
		}
	}
	return $data;
}

/**
* returns SOUR:TITL matching filter
* @return Array of string
*/
function autocomplete_SOUR_TITL($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}sources WHERE s_name LIKE ? AND s_file=? ORDER BY s_name";
	$rows =	$gBitDb->query( $sql
		,array("%{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$source = Source::getInstance( $row );
		if ($source->canDisplayName()) {
			$data[] = $source->getFullName();
		}
	}
	return $data;
}

/**
* returns INDI_BURI_CEME matching filter
* @return Array of string
*/
function autocomplete_INDI_BURI_CEME($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql=
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
		" FROM {$TBLPREFIX}individuals".
		" WHERE i_gedcom LIKE ? AND i_file=?";
	$rows =	$gBitDb->query( $sql
		,array("%1 BURI%2 CEME %{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$person = Person::getInstance( $row );
		if ($person->canDisplayDetails()) {
			$i = 1;
			do {
				$srec = get_sub_record("BURI", 1, $person->gedrec, $i++);
				$ceme = get_gedcom_value("CEME", 2, $srec);
				if (stripos($ceme, $FILTER)!==false || empty($FILTER)) {
					$data[] = $ceme;
				}
			} while ($srec);
		}
	}
	return $data;
}

/**
* returns INDI:SOUR:PAGE matching filter
* @return Array of string
*/
function autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals WHERE i_gedcom LIKE ? AND i_file=?";
	$rows =	$gBitDb->query( $sql
		, array("% SOUR @{$OPTION}@% PAGE %{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$person = Person::getInstance( $row );
		if ($person->canDisplayDetails()) {
			// a single INDI may have multiple level 1 and level 2 sources
			for ($level=1; $level<=2; $level++) {
				$i = 1;
				do {
					$srec = get_sub_record("SOUR @{$OPTION}@", $level, $person->gedrec, $i++);
					$page = get_gedcom_value("PAGE", $level+1, $srec);
					if (stripos($page, $FILTER)!==false || empty($FILTER)) {
						$data[] = $page;
					}
				} while ($srec);
			}
		}
	}
	return $data;
}

/**
* returns FAM:SOUR:PAGE matching filter
* @return Array of string
*/
function autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION) {
	global $TBLPREFIX, $gBitDb;

	$sql=
		"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families WHERE f_gedcom LIKE ? AND f_file=?";
	$rows =	$gBitDb->query( $sql
		, array("% SOUR @{$OPTION}@% PAGE %{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$family = Family::getInstance($row);
		if ($family->canDisplayDetails()) {
			// a single FAM may have multiple level 1 and level 2 sources
			for ($level=1; $level<=2; $level++) {
				$i = 1;
				do {
					$srec = get_sub_record("SOUR @{$OPTION}@", $level, $family->gedrec, $i++);
					$page = get_gedcom_value("PAGE", $level+1, $srec);
					if (stripos($page, $FILTER)!==false || empty($FILTER)) {
						$data[] = $page;
					}
				} while ($srec);
			}
		}
	}
	return $data;
}

/**
* returns SOUR:PAGE matching filter
* @return Array of string
*/
function autocomplete_SOUR_PAGE($FILTER, $OPTION) {
	return array_merge(
		autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION),
		autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION));
}

/**
* returns REPOsitories matching filter
* @return Array of string
*/
function autocomplete_REPO($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql=
		"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
		" FROM {$TBLPREFIX}other".
		" WHERE (o_gedcom LIKE ? OR o_id LIKE ?) AND o_file=? AND o_type='REPO'";
	$rows =	$gBitDb->query( $sql
		, array("%1 NAME %{$FILTER}%", "%{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$repository = Repository::getInstance( $row );
		if ($repository->canDisplayName()) {
			$data[$row['xref']] = $repository->getFullName();
		}
	}
	return $data;
}

/**
* returns REPO:NAME matching filter
* @return Array of string
*/
function autocomplete_REPO_NAME($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql=
		"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
		" FROM {$TBLPREFIX}other".
		" WHERE o_gedcom LIKE ? AND o_file=? AND o_type='REPO'";
	$rows =	$gBitDb->query( $sql
		, array("%1 NAME %{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$repository = Repository::getInstance($row);
		if ($repository->canDisplayName()) {
			$data[] = $repository->getFullName();
		}
	}
	return $data;
}

/**
* returns OBJEcts matching filter
* @return Array of string
*/
function autocomplete_OBJE($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT m_media FROM {$TBLPREFIX}media WHERE (m_titl LIKE ? OR m_media LIKE ?) AND m_gedfile=?";
	$rows =	$gBitDb->query( $sql
		, array("%{$FILTER}%", "%{$FILTER}%", PGV_GED_ID)
		, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$media = Media::getInstance($row['m_media']);
		if ($media && $media->canDisplayDetails()) {
			$data[$row['m_media']] =
				"<img alt=\"".
				$media->getXref().
				"\" src=\"".
				$media->getThumbnail().
				"\" width=\"40\" /> ".
				$media->getFullName();
		}
	}
	return $data;
}

/**
* returns INDI FAM SOUR NOTE REPO OBJE matching filter
* @return Array of string
*/
function autocomplete_IFSRO() {
	global $GEDCOM_ID_PREFIX, $FAM_ID_PREFIX, $SOURCE_ID_PREFIX, $NOTE_ID_PREFIX, $REPO_ID_PREFIX, $MEDIA_ID_PREFIX, $FILTER;

	// is input text a gedcom xref ?
	$prefix = strtoupper(substr($FILTER, 0, 1));
	if (ctype_digit(substr($FILTER, 1))) {
		if ($prefix == $GEDCOM_ID_PREFIX) {
			return autocomplete_INDI($FILTER, '');
		} elseif ($prefix == $FAM_ID_PREFIX) {
			return autocomplete_FAM($FILTER, '');
		} elseif ($prefix == $SOURCE_ID_PREFIX) {
			return autocomplete_SOUR($FILTER);
		} elseif ($prefix == $NOTE_ID_PREFIX) {
			return autocomplete_NOTE($FILTER);
		} elseif ($prefix == $REPO_ID_PREFIX) {
			return autocomplete_REPO($FILTER);
		} elseif ($prefix == $MEDIA_ID_PREFIX) {
			return autocomplete_OBJE($FILTER);
		}
	}
	return array_merge(
		autocomplete_INDI($FILTER, ''),
		autocomplete_FAM($FILTER, ''),
		autocomplete_SOUR($FILTER),
		autocomplete_NOTE($FILTER),
		autocomplete_REPO($FILTER),
		autocomplete_OBJE($FILTER)
		);
}

/**
* returns SURNames matching filter
* @return Array of string
*/
function autocomplete_SURN($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT DISTINCT n_surname FROM {$TBLPREFIX}name WHERE n_surname LIKE ? AND n_file=? ORDER BY n_surname";
	return $rows =	$gBitDb->getOne( $sql
		, array("%{$FILTER}%", PGV_GED_ID));
}

/**
* returns GIVenNames matching filter
* @return Array of string
*/
function autocomplete_GIVN($FILTER) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT DISTINCT n_givn FROM {$TBLPREFIX}name WHERE n_givn LIKE ? AND n_file=? ORDER BY n_givn";
	$rows = $gBitDb->query( $sql
			,array("%{$FILTER}%", PGV_GED_ID)
			, PGV_AUTOCOMPLETE_LIMIT);

	$data=array();
	while ( $row = $rows->fetchRows() ) {
		$givn=$row['n_givn'];
		list($givn) = explode("/", $givn);
		list($givn) = explode(",", $givn);
		list($givn) = explode("*", $givn);
		list($givn) = explode(" ", $givn);
		if ($givn) {
			$data[]=$row['n_givn'];
		}
	}
	return $data;
}

/**
* returns NAMEs matching filter
* @return Array of string
*/
function autocomplete_NAME($FILTER) {
	return array_merge(autocomplete_GIVN($FILTER), autocomplete_SURN($FILTER));
}

/**
* returns PLACes matching filter
* @return Array of string City, County, State/Province, Country
*/
function autocomplete_PLAC($FILTER, $OPTION) {
	global $TBLPREFIX, $gBitDb, $USE_GEONAMES, $lang_short_cut, $LANGUAGE;

	$sql="SELECT p_id, p_place, p_parent_id FROM {$TBLPREFIX}places WHERE p_place LIKE ? AND p_file=? ORDER BY p_place";
	$rows = $gBitDb->query( $sql
			,array("%{$FILTER}%", PGV_GED_ID)
			, PGV_AUTOCOMPLETE_LIMIT);

	$place=array();
	$parent=array();
	do {
		while ( $row = $rows->fetchRows() ) {
			$place[$row['p_id']] = $row['p_place'];
			$parent[$row['p_id']] = $row['p_parent_id'];
		}
		//-- search for missing parents
		$missing = array();
		foreach($parent as $k=>$v) {
			if ($v && !isset($place[$v])) {
				$missing[] = $v;
			}
		}
		if (count($missing)==0) {
			break;
		}
		$qs=implode(',', array_fill(0, count($missing), '?'));
		$sql="SELECT p_id, p_place, p_parent_id FROM {$TBLPREFIX}places WHERE p_id IN ({$qs}) AND p_file=?";
		$vars=$missing;
		$vars[]=PGV_GED_ID;
		$rows = $gBitDb->getAll( $sql, $vars );

	} while (true);

	//-- build place list
	$place = array_reverse($place, true);
	$data = array();
	do {
		$repeat = false;
		foreach($place as $k=>$v) {
			if ($parent[$k]==0) {
				$data[$k] = $v;
			} else {
				if (isset($data[$parent[$k]])) {
					$data[$k] = $v.", ".$data[$parent[$k]];
				} else {
					$repeat = true;
				}
			}
		}
	} while ($repeat);

	//-- filter
	function place_ok($v) {
		global $FILTER;
		return (stripos($v, $FILTER)!==false);
	}
	$data = array_filter($data, "place_ok");

	//-- no match => perform a geoNames query if enabled
	if (empty($data) && $USE_GEONAMES) {
		$url = "http://ws5.geonames.org/searchJSON".
					"?name_startsWith=".urlencode($FILTER).
					"&lang=".$lang_short_cut[$LANGUAGE].
					"&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC".
					"&style=full";
		// try to use curl when file_get_contents not allowed
		if (ini_get('allow_url_fopen')) {
			$json = file_get_contents($url);
		} elseif (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$json = curl_exec($ch);
			curl_close($ch);
		} else {
			return $data;
		}
		$places = json_decode($json, true);
		if ($places["geonames"]) {
			foreach ($places["geonames"] as $k => $place) {
				$data[] = $place["name"].", ".
									$place["adminName2"].", ".
									$place["adminName1"].", ".
									$place["countryName"];
			}
		}
	}

	// split ?
	if ($OPTION=="split") {
		foreach ($data as $k=>$v) {
			list($data[$k]) = explode(",", $v);
		}
		$data = array_filter($data, "place_ok");
	}

	return $data;
}

?>

<?php
/**
* Functions to query the database.
*
* This file implements the datastore functions necessary for PhpGedView
* to use an SQL database as its datastore.
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
* @version $Id: functions_db.php,v 1.6 2009/11/01 21:38:18 lsces Exp $
* @package PhpGedView
* @subpackage DB
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_DB_PHP', '');

/**
* check if a gedcom has been imported into the database
*
* this function checks the database to see if the given gedcom has been imported yet.
* @param string $ged the filename of the gedcom to check for import
* @return bool return true if the gedcom has been imported otherwise returns false
*/
function check_for_import($ged) {
	global $TBLPREFIX, $GEDCOMS, $gBitDb;

	if ( empty($gBitDb->mDb) || count($GEDCOMS)==0 || !isset($GEDCOMS[$ged])) {
		return false;
	}

	if (!isset($GEDCOMS[$ged]["imported"])) {
		$GEDCOMS[$ged]["imported"]=(bool)
		$gBitDb->getOne("SELECT count(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=?", array($GEDCOMS[$ged]["id"]));
		store_gedcoms();
	}

	return $GEDCOMS[$ged]["imported"];
}

//-- gets the first record in the gedcom
function get_first_xref($type, $ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	switch ($type) {
	case "INDI":
		return
			$gBitDb->getOne(
				"SELECT MIN(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=?"
				, array($ged_id));
		break;
	case "FAM":
		return
			$gBitDb->getOne(
				"SELECT MIN(f_id) FROM {$TBLPREFIX}families WHERE f_file=?"
				, array($ged_id));
	case "SOUR":
		return
			$gBitDb->getOne(
				"SELECT MIN(s_id) FROM {$TBLPREFIX}sources WHERE s_file=?"
				, array($ged_id));
	case "OBJE":
		return
			$gBitDb->getOne(
				"SELECT MIN(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=?"
				, array($ged_id));
	default:
		return
			$gBitDb->getOne(
				"SELECT MIN(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=?"
				, array($ged_id, $type));
	}
}

//-- gets the last record in the gedcom
function get_last_xref($type, $ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	switch ($type) {
	case "INDI":
		return
			$gBitDb->getOne(
				"SELECT MAX(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=?"
				, array($ged_id));
		break;
	case "FAM":
		return
			$gBitDb->getOne(
				"SELECT MAX(f_id) FROM {$TBLPREFIX}families WHERE f_file=?"
				, array($ged_id));
	case "SOUR":
		return
			$gBitDb->getOne(
				"SELECT MAX(s_id) FROM {$TBLPREFIX}sources WHERE s_file=?"
				, array($ged_id));
	case "OBJE":
		return
			$gBitDb->getOne(
				"SELECT MAX(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=?"
				, array($ged_id));
	default:
		return
			$gBitDb->getOne(
				"SELECT MAX(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=?"
				, array($ged_id, $type));
	}
}

//-- gets the next person in the gedcom
function get_next_xref($pid, $ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	$type=gedcom_record_type($pid, $ged_id);
	switch ($type) {
	case "INDI":
		return
			$gBitDb->getOne(
				"SELECT MIN(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_id>?"
				, array($ged_id, $pid));
		break;
	case "FAM":
		return
			$gBitDb->getOne(
				"SELECT MIN(f_id) FROM {$TBLPREFIX}families WHERE f_file=? AND f_id>?"
				, array($ged_id, $pid));
	case "SOUR":
		return
			$gBitDb->getOne(
				"SELECT MIN(s_id) FROM {$TBLPREFIX}sources WHERE s_file=? AND s_id>?"
				, array($ged_id, $pid));
	case "OBJE":
		return
			$gBitDb->getOne(
				"SELECT MIN(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=? AND m_media>?"
				, array($ged_id, $pid));
	default:
		return
			$gBitDb->getOne(
				"SELECT MIN(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=? AND o_id>?"
				, array($ged_id, $type, $pid));
	}
}

//-- gets the previous person in the gedcom
function get_prev_xref($pid, $ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	$type=gedcom_record_type($pid, $ged_id);
	switch ($type) {
	case "INDI":
		return
			$gBitDb->getOne(
				"SELECT MAX(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_id<?"
				, array($ged_id, $pid));
		break;
	case "FAM":
		return
			$gBitDb->getOne(
				"SELECT MAX(f_id) FROM {$TBLPREFIX}families WHERE f_file=? AND f_id<?"
				, array($ged_id, $pid));
	case "SOUR":
		return
			$gBitDb->getOne(
				"SELECT MAX(s_id) FROM {$TBLPREFIX}sources WHERE s_file=? AND s_id<?"
				, array($ged_id, $pid));
	case "OBJE":
		return
			$gBitDb->getOne(
				"SELECT MAX(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=? AND m_media<?"
				, array($ged_id, $pid));
	default:
		return
			$gBitDb->getOne(
				"SELECT MAX(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=? AND o_id<?"
				, array($ged_id, $type, $pid));
	}
}

////////////////////////////////////////////////////////////////////////////////
// Generate a list of alternate initial letters for the indilist and famlist
////////////////////////////////////////////////////////////////////////////////
function db_collation_alternatives($letter) {
	global $UCDiacritWhole, $UCDiacritStrip, $DB_UTF8_COLLATION, $MULTI_LETTER_ALPHABET, $MULTI_LETTER_EQUIV, $LANGUAGE;

	// Multi-letter collation.
	// e.g. on czech pages, we don't include "CH" under "C"
	$include=array($letter);
	$exclude=array();
	foreach (preg_split('/[ ,;]/', UTF8_strtoupper($MULTI_LETTER_ALPHABET[$LANGUAGE])) as $digraph) {
		if ($letter && $digraph!=$letter && strpos($digraph, $letter)===0) {
			$exclude[]=$digraph;
		}
	}

	// Multi-letter equivalents
	// e.g. on danish pages, we include "AA" under "Aring", not under "A"
	foreach (preg_split('/[ ,;]/', $MULTI_LETTER_EQUIV[$LANGUAGE], -1, PREG_SPLIT_NO_EMPTY) as $digraph) {
		list($from, $to)=explode('=', $digraph);
		$from=UTF8_strtoupper($from);
		if ($from==$letter && strpos($from, $letter)===0) {
			$include[]=$to;
		}
		if ($letter && $from!=$letter && strpos($from, $letter)===0) {
			$exclude[]=$from;
		}
		if ($to==$letter) {
			$include[]=$from;
		}
	}

	// Non UTF8 aware databases need to be told that "e-ecute" is the same as "e"....
	if (!$DB_UTF8_COLLATION) {
		foreach (str_split($UCDiacritStrip) as $n=>$char) {
			if ($char==$letter) {
				$include[]=UTF8_substr($UCDiacritWhole, $n, 1);
			}
		}
	}

	return array($include, $exclude);
}

////////////////////////////////////////////////////////////////////////////////
// Generate a list of digraphs for the indilist and famlist
////////////////////////////////////////////////////////////////////////////////
function db_collation_digraphs() {
	global $MULTI_LETTER_ALPHABET, $MULTI_LETTER_EQUIV, $LANGUAGE;

	// Multi-letter collation.
	// e.g. on czech pages, we don't include "CH" under "C"
	$digraphs=array();
	foreach (preg_split('/[ ,;]/', UTF8_strtoupper($MULTI_LETTER_ALPHABET[$LANGUAGE]), -1, PREG_SPLIT_NO_EMPTY) as $digraph) {
		$digraphs[$digraph]=$digraph;
	}

	// Multi-letter equivalents
	// e.g. danish pages, we include "AE" under "AE-ligature"
	foreach (preg_split('/[ ,;]/', $MULTI_LETTER_EQUIV[$LANGUAGE], -1, PREG_SPLIT_NO_EMPTY) as $digraph) {
		list($from, $to)=explode('=', $digraph);
		$digraphs[$to]=UTF8_strtoupper($from);
	}

	return $digraphs;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of initial surname letters for indilist.php and famlist.php
// $marnm - if set, include married names
// $fams - if set, only consider individuals with FAMS records
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_indilist_salpha($marnm, $fams, $ged_id) {
	global $TBLPREFIX, $DB_UTF8_COLLATION, $DBCOLLATE, $gBitDb;

	$ged_id=(int)$ged_id;

	if ($fams) {
		$tables="{$TBLPREFIX}name, {$TBLPREFIX}individuals, {$TBLPREFIX}link";
		$join="n_file={$ged_id} AND i_file=n_file AND i_id=n_id AND l_file=n_file AND l_from=n_id AND l_type='FAMS'";
	} else {
		$tables="{$TBLPREFIX}name, {$TBLPREFIX}individuals";
		$join="n_file={$ged_id} AND i_file=n_file AND i_id=n_id";
	}
	if (!$marnm) {
		$join.=" AND n_type!='_MARNM'";
	}
	if ( $gBitDb->mType == 'firebird' ) {
		$column="SUBSTRING ( n_sort FROM 1 FOR 1)";
	} else if ($DB_UTF8_COLLATION) {
		$column="SUBSTR(n_sort {$DBCOLLATE}, 1, 1)";
	} else {
		$column="SUBSTR(n_sort {$DBCOLLATE}, 1, 3)";
	}

	$exclude='';
	$include='';
	$digraphs=db_collation_digraphs();
	foreach (array_unique($digraphs) as $digraph) { // Multi-character digraphs
		$exclude.=" AND n_sort NOT LIKE '{$digraph}%' {$DBCOLLATE}";
	}
	foreach ($digraphs as $to=>$from) { // Single-character digraphs
		$include.=" UNION SELECT UPPER('{$to}' {$DBCOLLATE}) AS alpha FROM {$tables} WHERE {$join} AND n_sort LIKE '{$from}%' {$DBCOLLATE} GROUP BY 1";
	}
	$alphas=
		$gBitDb->getAll("SELECT {$column} AS alpha FROM {$tables} WHERE {$join} {$exclude} GROUP BY 1 {$include} ORDER BY 1");

	$list=array();
	foreach ($alphas as $alpha) {
		if ($DB_UTF8_COLLATION) {
			$letter=$alpha['alpha'];
		} else {
			$letter=UTF8_strtoupper(UTF8_substr($alpha['alpha'],0,1));
		}
		$list[$letter]=$letter;
	}

	// If we didn't sort in the DB, sort ourselves
	if (!$DB_UTF8_COLLATION) {
		uasort($list, 'stringsort');
	}
	// sorting puts "," and "@" first, so force them to the end
	if (in_array(',', $list)) {
		unset($list[',']);
		$list[',']=',';
	}
	if (in_array('@', $list)) {
		unset($list['@']);
		$list['@']='@';
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of initial given name letters for indilist.php and famlist.php
// $surn - if set, only consider people with this surname
// $salpha - if set, only consider surnames starting with this letter
// $marnm - if set, include married names
// $fams - if set, only consider individuals with FAMS records
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_indilist_galpha($surn, $salpha, $marnm, $fams, $ged_id) {
	global $TBLPREFIX, $DB_UTF8_COLLATION, $DBCOLLATE, $gBitDb;

	if ($fams) {
		$tables="{$TBLPREFIX}name, {$TBLPREFIX}individuals, {$TBLPREFIX}link";
		$join="n_file=$ged_id AND i_file=n_file AND i_id=n_id AND l_file=n_file AND l_from=n_id AND l_type='FAMS'";
	} else {
		$tables="{$TBLPREFIX}name, {$TBLPREFIX}individuals";
		$join="n_file=$ged_id AND i_file=n_file AND i_id=n_id";
	}
	if ($marnm) {
		$join.=" AND n_type!='_MARNM'";
	}
	if ($surn) {
		$join.=" AND n_sort LIKE {$surn}";
	} elseif ($salpha) {
		$join.=" AND n_sort LIKE {$salpha}%";
	}

	if ($DB_UTF8_COLLATION) {
		$column="UPPER(SUBSTR(n_givn {$DBCOLLATE}, 1, 1))";
	} else {
		$column="UPPER(SUBSTR(n_givn {$DBCOLLATE}, 1, 3))";
	}

	$exclude='';
	$include='';
	$digraphs=db_collation_digraphs();
	foreach (array_unique($digraphs) as $digraph) { // Multi-character digraphs
		$exclude.=" AND n_sort NOT LIKE '{$digraph}%' {$DBCOLLATE}";
	}
	foreach ($digraphs as $to=>$from) { // Single-character digraphs
		$include.=" UNION SELECT UPPER('{$to}' {$DBCOLLATE}) AS alpha FROM {$tables} WHERE {$join} AND n_sort LIKE '{$from}%' {$DBCOLLATE} GROUP BY 1";
	}
	$alphas=
		$gBitDb->getOne("SELECT {$column} AS alpha FROM {$tables} WHERE {$join} {$exclude} GROUP BY 1 {$include} ORDER BY 1");
	$list=array();
	foreach ($alphas as $alpha) {
		if ($DB_UTF8_COLLATION) {
			$letter=$alpha;
		} else {
			$letter=UTF8_strtoupper(UTF8_substr($alpha,0,1));
		}
		$list[$letter]=$letter;
	}

	// If we didn't sort in the DB, sort ourselves
	if (!$DB_UTF8_COLLATION) {
		uasort($list, 'stringsort');
	}
	// sorting puts "," and "@" first, so force them to the end
	if (in_array(',', $list)) {
		unset($list[',']);
		$list[',']=',';
	}
	if (in_array('@', $list)) {
		unset($list['@']);
		$list['@']='@';
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of surnames for indilist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only consider surnames starting with this letter
// $marnm - if set, include married names
// $fams - if set, only consider individuals with FAMS records
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_indilist_surns($surn, $salpha, $marnm, $fams, $ged_id) {
	global $TBLPREFIX, $DB_UTF8_COLLATION, $DBCOLLATE, $gBitDb;

	$sql="SELECT DISTINCT n_surn, n_surname, n_id FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
	if ($fams) {
		$sql.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
	}
	$where = array("n_file = $ged_id ");
	if (!$marnm) {
		$where[]="n_type != '_MARNM'";
	}

	list($s_incl, $s_excl)=db_collation_alternatives($salpha);

	$includes=array();
	if ($surn) {
		// Match a surname
		$includes[]="n_surn {$DBCOLLATE} LIKE '{$surn}'";
	} elseif ($salpha==',') {
		// Match a surname-less name
		$includes[]="n_surn {$DBCOLLATE} = ''";
	} elseif ($salpha) {
		// Match a surname initial
		foreach ($s_incl as $s) {
			$includes[]="n_surn {$DBCOLLATE} LIKE '{$s}%'";
		}
		foreach ($s_excl as $s) {
			$where[]="n_surn {$DBCOLLATE} NOT LIKE '{$s}%'";
		}
	} else {
		// Match all individuals
		$where[]="n_surn {$DBCOLLATE} <>'@N.N.'";
		$where[]="n_surn {$DBCOLLATE} <> ''";
	}

	if ($includes) {
		$where[]='('.implode(' OR ', $includes).')';
	}

	$sql.=" WHERE ".implode(' AND ', $where)." ORDER BY n_surn";

	$list=array();
	$result = $gBitDb->query($sql);

	while ( $row = $result->fetchRow() ) {
		$list[$row['n_surn']][$row['n_surname']][$row['n_id']]=true;
	}
	if (!$DB_UTF8_COLLATION) {
		uksort($list, 'stringsort');
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of surnames for indilist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only consider surnames starting with this letter
// $marnm - if set, include married names
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_famlist_surns($surn, $salpha, $marnm, $ged_id) {
	global $TBLPREFIX, $DB_UTF8_COLLATION, $DBCOLLATE, $gBitDb;

	$sql="SELECT DISTINCT n_surn, n_surname, l_to FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file) JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
	$where=array("n_file={$ged_id}");
	if (!$marnm) {
		$where[]="n_type!='_MARNM'";
	}

	list($s_incl, $s_excl)=db_collation_alternatives($salpha);

	$includes=array();
	if ($surn) {
		// Match a surname
		$includes[]="n_surn {$DBCOLLATE} LIKE '{$surn}'";
	} elseif ($salpha==',') {
		// Match a surname-less name
		$includes[]="n_surn {$DBCOLLATE} = ''";
	} elseif ($salpha) {
		// Match a surname initial
		foreach ($s_incl as $s) {
			$includes[]="n_surn {$DBCOLLATE} LIKE '{$s}%'";
		}
		foreach ($s_excl as $s) {
			$where[]="n_surn {$DBCOLLATE} NOT LIKE '{$s}%'";
		}
	} else {
		// Match all individuals
		$where[]="n_surn {$DBCOLLATE} <> '@N.N.'";
		$where[]="n_surn {$DBCOLLATE} <> ''";
	}

	if ($includes) {
		$where[]='('.implode(' OR ', $includes).')';
	}

	$sql.=" WHERE ".implode(' AND ', $where)." ORDER BY n_surn";

	$list=array();
	$results = $gBitDb->query($sql);
	while ( $row = $results->fetchRow() ) {
		$list[$row['n_surn']][$row['n_surname']][$row['l_to']]=true;
	}
	if (!$DB_UTF8_COLLATION) {
		uksort($list, 'stringsort');
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of individuals for indilist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only fetch surnames starting with this letter
// $galpha - if set, only fetch given names starting with this letter
// $marnm - if set, include married names
// $fams - if set, only fetch individuals with FAMS records
// $ged_id - if set, only fetch individuals from this gedcom
//
// All parameters must be in upper case.  We search against a database column
// that contains uppercase values. This will allow non utf8-aware database
// to match diacritics.
//
// To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
// To search for names with no surnames, use $salpha=","
////////////////////////////////////////////////////////////////////////////////
function get_indilist_indis($surn='', $salpha='', $galpha='', $marnm=false, $fams=false, $ged_id=null) {
	global $TBLPREFIX, $DB_UTF8_COLLATION, $DBCOLLATE, $gBitDb;

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex, n_surn, n_surname, n_num FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
	if ($fams) {
		$sql.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file)";
	}
	$where=array();
	if ($ged_id) {
		$where[]="n_file={$ged_id}";
	}
	if (!$marnm) {
		$where[]="n_type!='_MARNM'";
	}

	list($s_incl, $s_excl)=db_collation_alternatives($salpha);
	list($g_incl, $g_excl)=db_collation_alternatives($galpha);

	$includes=array();
	if ($surn) {
		// Match a surname, with or without a given initial
		if ($galpha) {
			foreach ($g_incl as $g) {
				$includes[]="n_sort {$DBCOLLATE} LIKE '{$surn},{$g}%'";
			}
			foreach ($g_excl as $g) {
				$where[]="n_sort {$DBCOLLATE} NOT LIKE '{$surn},{$g}%'";
			}
		} else {
			$includes[]="n_sort {$DBCOLLATE} LIKE '{$surn},%'";
		}
	} elseif ($salpha==',') {
		// Match a surname-less name, with or without a given initial
		if ($galpha) {
			foreach ($g_incl as $g) {
				$includes[]="n_sort {$DBCOLLATE} LIKE ',{$g}%'";
			}
			foreach ($g_excl as $g) {
				$where[]="n_sort {$DBCOLLATE} NOT LIKE ',{$g}%'";
			}
		} else {
			$includes[]="n_sort {$DBCOLLATE} LIKE ',%'";
		}
	} elseif ($salpha) {
		// Match a surname initial, with or without a given initial
		if ($galpha) {
			foreach ($g_excl as $g) {
				foreach ($s_excl as $s) {
					$includes[]="n_sort {$DBCOLLATE} LIKE '{$s}%,{$g}%'";
				}
			}
			foreach ($g_excl as $g) {
				foreach ($s_excl as $s) {
					$where[]="n_sort {$DBCOLLATE} NOT LIKE '{$s}%,{$g}%'";
				}
			}
		} else {
			foreach ($s_incl as $s) {
				$includes[]="n_sort {$DBCOLLATE} LIKE '{$s}%'";
			}
			foreach ($s_excl as $s) {
				$where[]="n_sort {$DBCOLLATE} NOT LIKE '{$s}%'";
			}
		}
	} elseif ($galpha) {
		// Match all surnames with a given initial
		$includes[]="n_sort {$DBCOLLATE} LIKE '%,{$galpha}%'";
		foreach ($g_excl as $g) {
			$where[]="n_sort {$DBCOLLATE} NOT LIKE '{$g}%'";
		}
	} else {
		// Match all individuals
	}

	if ($includes) {
		$where[]='('.implode(' OR ', $includes).')';
	}

	$sql.=" WHERE ".implode(' AND ', $where)." ORDER BY n_sort";

	$list=array();
	$result = $gBitDb->query($sql);
	while ( $row = $result->fetchRow() ) {
		$person=Person::getInstance($row);
		$person->setPrimaryName($row['n_num']);
		// We need to clone $person, as we may have multiple references to the
		// same person in this list, and the "primary name" would otherwise
		// be shared amongst all of them.  This has some performance/memory
		// implications, and there is probably a better way.  This, however,
		// is clean, easy and works.
		$list[]=clone $person;
	}
	if (!$DB_UTF8_COLLATION) {
		usort($list, array('GedcomRecord', 'Compare'));
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of families for famlist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only fetch surnames starting with this letter
// $galpha - if set, only fetch given names starting with this letter
// $marnm - if set, include married names
// $ged_id - if set, only fetch individuals from this gedcom
//
// All parameters must be in upper case.  We search against a database column
// that contains uppercase values. This will allow non utf8-aware database
// to match diacritics.
//
// To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
// To search for names with no surnames, use $salpha=","
////////////////////////////////////////////////////////////////////////////////
function get_famlist_fams($surn='', $salpha='', $galpha='', $marnm, $ged_id=null) {
	global $TBLPREFIX, $gBitDb;

	$list=array();
	foreach (get_indilist_indis($surn, $salpha, $galpha, $marnm, true, $ged_id) as $indi) {
		foreach ($indi->getSpouseFamilies() as $family) {
			$list[$family->getXref()]=$family;
		}
	}
	// If we're searching for "Unknown surname", we also need to include families
	// with missing spouses
	if ($surn=='@N.N.' || $salpha=='@') {
		$rows=
			$gBitDb->getAssoc(
				"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families f WHERE f_file={$ged_id} AND (f_husb='' OR f_wife='')"
				, array($ged_id));

		foreach ($rows as $row) {
			$list[]=Family::getInstance($row);
		}
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Fetch a list of children for an individual, from all their partners.
////////////////////////////////////////////////////////////////////////////////
function fetch_child_ids($parent_id, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return $gBitDb->getOne(
		   "SELECT DISTINCT child.l_from AS xref FROM {$TBLPREFIX}link child, {$TBLPREFIX}link spouse WHERE child.l_type=? AND spouse.l_type=? AND child.l_file=spouse.l_file AND child.l_to=spouse.l_to AND spouse.l_from=? AND child.l_file=?"
		   , array('FAMC', 'FAMS', $parent_id, $ged_id));
}

////////////////////////////////////////////////////////////////////////////////
// Count the number of records of each type in the database.  Return an array
// of 'type'=>count for each type where records exist.
////////////////////////////////////////////////////////////////////////////////
function count_all_records($ged_id) {
	global $TBLPREFIX, $gBitDb;
	$test = array();
	$test['INDI'] = $gBitDb->getOne(
			"SELECT COUNT(*) AS num FROM {$TBLPREFIX}individuals WHERE i_file=?"
			, array($ged_id));
	$test['FAM'] = $gBitDb->getOne(
			"SELECT COUNT(*) AS num FROM {$TBLPREFIX}families WHERE f_file=?"
			, array($ged_id));
	$test['NOTE'] = $gBitDb->getOne(
			"SELECT COUNT(*) AS num FROM {$TBLPREFIX}other WHERE o_file=?"
			, array($ged_id));
	$test['SOUR'] = $gBitDb->getOne(
			"SELECT COUNT(*) AS num FROM {$TBLPREFIX}sources WHERE s_file=?"
			, array($ged_id));
	$test['OBJE'] = $gBitDb->getOne(
			"SELECT COUNT(*) AS num FROM {$TBLPREFIX}media WHERE m_gedfile=?"
			, array($ged_id));
	return
		$test;
}

////////////////////////////////////////////////////////////////////////////////
// Count the number of records linked to a given record
////////////////////////////////////////////////////////////////////////////////
function count_linked_indi($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE i_file=l_file AND i_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array($ged_id, $link, $xref));
}
function count_linked_fam($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}families WHERE f_file=l_file AND f_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array($ged_id, $link, $xref));
}
function count_linked_note($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}other WHERE o_file=l_file AND o_id=l_from AND o_type=? AND l_file=? AND l_type=? AND l_to=?"
			, array('NOTE', $ged_id, $link, $xref));
}
function count_linked_sour($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}sources WHERE s_file=l_file AND s_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array($ged_id, $link, $xref));
}
function count_linked_obje($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}media WHERE m_gedfile=l_file AND m_media=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array($ged_id, $link, $xref));
}

////////////////////////////////////////////////////////////////////////////////
// Fetch records linked to a given record
////////////////////////////////////////////////////////////////////////////////
function fetch_linked_indi($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows =
		$gBitDb->getAll(
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE i_file=l_file AND i_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array( $ged_id, $link, $xref));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Person::getInstance($row);
	}
	return $list;
}

function fetch_linked_fam($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows =
		$gBitDb->getAll(
			"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}link, {$TBLPREFIX}families f WHERE f_file=l_file AND f_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array( $ged_id, $link, $xref));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Family::getInstance($row);
	}
	return $list;
}

function fetch_linked_note($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows =
		$gBitDb->getAll(
			"SELECT 'NOTE' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}link, {$TBLPREFIX}other o WHERE o_file=l_file AND o_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array( $ged_id, $link, $xref));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Note::getInstance($row);
	}
	return $list;
}

function fetch_linked_sour($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows=
		$gBitDb->getAll(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}link, {$TBLPREFIX}sources s WHERE s_file=l_file AND s_id=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array( $ged_id, $link, $xref));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Source::getInstance($row);
	}
	return $list;
}

function fetch_linked_obje($xref, $link, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows=
		$gBitDb->getAll(
			"SELECT 'OBJE' AS type, m_media AS xref, m_gedfile AS ged_id, m_gedrec AS gedrec, m_titl, m_file FROM {$TBLPREFIX}link, {$TBLPREFIX}media m WHERE m_gedfile=l_file AND m_media=l_from AND l_file=? AND l_type=? AND l_to=?"
			, array( $ged_id, $link, $xref));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Media::getInstance($row);
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Fetch all records linked to a record - when deleting an object, we must
// also delete all links to it.
////////////////////////////////////////////////////////////////////////////////
function fetch_all_links($xref, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT l_from FROM {$TBLPREFIX}link WHERE l_file=? AND l_to=?"
			, array($ged_id, $xref));
}

////////////////////////////////////////////////////////////////////////////////
// Fetch a row from the database, corresponding to a gedcom record.
// These functions are used to create gedcom objects.
// To simplify common processing, the xref, gedcom id and gedcom record are
// renamed consistently.  The other columns are fetched as they are.
////////////////////////////////////////////////////////////////////////////////
function fetch_person_record($xref, $ged_id) {
	global $TBLPREFIX, $gBitDb;
	if ( empty($xref) || empty($ged_id) ) return '';
	return $gBitDb->getRow(
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex ".
		"FROM {$TBLPREFIX}individuals WHERE i_id=? AND i_file=?"
		,array($xref, $ged_id));
		
}
function fetch_family_record($xref, $ged_id) {
	global $TBLPREFIX, $gBitDb;
	if ( empty($xref) || empty($ged_id) ) return '';
	return $gBitDb->getRow(
		"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil ".
		"FROM {$TBLPREFIX}families WHERE f_id=? AND f_file=?"
		, array($xref, $ged_id));
}

function fetch_source_record($xref, $ged_id) {
	global $TBLPREFIX, $gBitDb;
	if ( empty($xref) || empty($ged_id) ) return '';
	return $gBitDb->getRow(
		"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec ".
		"FROM {$TBLPREFIX}sources WHERE s_id=? AND s_file=?"
		, array($xref, $ged_id));
}

function fetch_note_record($xref, $ged_id) {
	// Notes are (currently) stored in the other table
	return fetch_other_record($xref, $ged_id);
}

function fetch_media_record($xref, $ged_id) {
	global $TBLPREFIX, $gBitDb;
	if ( empty($xref) || empty($ged_id) ) return '';
	return $gBitDb->getRow(
			"SELECT 'OBJE' AS type, m_media AS xref, m_gedfile AS ged_id, m_gedrec AS gedrec, m_titl, m_file ".
			"FROM {$TBLPREFIX}media WHERE m_media=? AND m_gedfile=?"
			, array($xref, $ged_id));
}

function fetch_other_record($xref, $ged_id) {
	global $TBLPREFIX, $gBitDb;
	if ( empty($xref) || empty($ged_id) ) return '';
	return $gBitDb->getRow(
		"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
		"FROM {$TBLPREFIX}other WHERE o_id=? AND o_file=?"
		, array($xref, $ged_id));
}

function fetch_gedcom_record($xref, $ged_id) {
	if ($row=fetch_person_record($xref, $ged_id)) {
		return $row;
	} elseif ($row=fetch_family_record($xref, $ged_id)) {
		return $row;
	} elseif ($row=fetch_source_record($xref, $ged_id)) {
		return $row;
	} elseif ($row=fetch_media_record($xref, $ged_id)) {
		return $row;
	} else {
		return fetch_other_record($xref, $ged_id);
	}
}

/**
* find the gedcom record for a family
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#family
* @param string $famid the unique gedcom xref id of the family record to retrieve
* @return string the raw gedcom record is returned
*/
function find_family_record($xref) {
	global $TBLPREFIX, $GEDCOM, $gBitDb;
	if ( empty($xref) ) return '';
	$ged_id=get_id_from_gedcom($GEDCOM);

	$gBitDb->getOne(
		"SELECT f_gedcom FROM {$TBLPREFIX}families WHERE f_id=? AND f_file=?"
		, array($xref, $ged_id));
}

/**
* find the gedcom record for an individual
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#indi
* @param string $pid the unique gedcom xref id of the individual record to retrieve
* @return string the raw gedcom record is returned
*/
function find_person_record($xref) {
	global $TBLPREFIX, $GEDCOM, $gBitDb;
	if ( empty($xref) ) return '';
	$ged_id=get_id_from_gedcom($GEDCOM);

	return $gBitDb->getOne(
		"SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_id=? AND i_file=?"
		, array($xref, $ged_id));
}

/**
* find the gedcom record for a source
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#source
* @param string $sid the unique gedcom xref id of the source record to retrieve
* @return string the raw gedcom record is returned
*/
function find_source_record($xref) {
	global $TBLPREFIX, $GEDCOM, $gBitDb;
	if ( empty($xref) ) return '';
	$ged_id=get_id_from_gedcom($GEDCOM);

	return $gBitDb->getOne(
		"SELECT s_gedcom FROM {$TBLPREFIX}sources WHERE s_id=? AND s_file=?"
		, array($xref, $ged_id));
}

/**
* Find a repository record by its ID
* @param string $rid the record id
* @param string $gedfile the gedcom file id
*/
function find_other_record($xref) {
	global $TBLPREFIX, $GEDCOM, $gBitDb;
	if ( empty($xref) ) return '';
	$ged_id=get_id_from_gedcom($GEDCOM);

	return $gBitDb->getOne(
		"SELECT o_gedcom FROM {$TBLPREFIX}other WHERE o_id=? AND o_file=?"
		, array($xref, $ged_id));
}

/**
* Find a media record by its ID
* @param string $rid the record id
*/
function find_media_record($xref) {
	global $TBLPREFIX, $GEDCOM, $gBitDb;
	if ( empty($xref) ) return '';
	$ged_id=get_id_from_gedcom($GEDCOM);

	return $gBitDb->getOne(
		"SELECT m_gedrec FROM {$TBLPREFIX}media WHERE m_media=? AND m_gedfile=?"
		, array($xref, $ged_id));
}

/**
* find the gedcom record
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#other
* @param string $pid the unique gedcom xref id of the record to retrieve
* @param string $gedfile [optional] the gedcomfile to search in
* @return string the raw gedcom record is returned
*/
function find_gedcom_record($xref, $gedfile='') {
	global $TBLPREFIX, $GEDCOM, $DBTYPE, $gBitDb;
	if ( empty($xref) ) return '';

	if ($gedfile) {
		$ged_id=get_id_from_gedcom($gedfile);
	} else {
		$ged_id=get_id_from_gedcom($GEDCOM);
	}

	// Exact match on xref?
	$gedcom = $gBitDb->getOne(
		"SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_id   =? AND i_file   =? UNION ALL ".
		"SELECT f_gedcom FROM {$TBLPREFIX}families    WHERE f_id   =? AND f_file   =? UNION ALL ".
		"SELECT s_gedcom FROM {$TBLPREFIX}sources     WHERE s_id   =? AND s_file   =? UNION ALL ".
		"SELECT m_gedrec FROM {$TBLPREFIX}media       WHERE m_media=? AND m_gedfile=? UNION ALL ".
		"SELECT o_gedcom FROM {$TBLPREFIX}other       WHERE o_id   =? AND o_file   =?"
		, array($xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id));
	
	if (!$gedcom) {
		// Not found.  Maybe i123 instead of I123 on a DB with a case-sensitive collation?
		$gedcom = $gBitDb->getOne(
			"SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_id    LIKE ? ESCAPE '@' AND i_file   =? UNION ALL ".
			"SELECT f_gedcom FROM {$TBLPREFIX}families    WHERE f_id    LIKE ? ESCAPE '@' AND f_file   =? UNION ALL ".
			"SELECT s_gedcom FROM {$TBLPREFIX}sources     WHERE s_id    LIKE ? ESCAPE '@' AND s_file   =? UNION ALL ".
			"SELECT m_gedrec FROM {$TBLPREFIX}media       WHERE m_media LIKE ? ESCAPE '@' AND m_gedfile=? UNION ALL ".
			"SELECT o_gedcom FROM {$TBLPREFIX}other       WHERE o_id    LIKE ? ESCAPE '@' AND o_file   =?"
			, array($xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id));
	}

	return $gedcom;
}

// Find the type of a gedcom record. Check the cache before querying the database.
// Returns 'INDI', 'FAM', etc., or null if the record does not exist.
function gedcom_record_type($xref, $ged_id) {
	global $TBLPREFIX, $gedcom_record_cache, $gBitDb;

	if (isset($gedcom_record_cache[$xref][$ged_id])) {
		return $gedcom_record_cache[$xref][$ged_id]->getType();
	} else {
		return 
		$gBitDb->getOne(
			"SELECT 'INDI' FROM {$TBLPREFIX}individuals WHERE i_id   =? AND i_file   =? UNION ALL ".
			"SELECT 'FAM'  FROM {$TBLPREFIX}families    WHERE f_id   =? AND f_file   =? UNION ALL ".
			"SELECT 'SOUR' FROM {$TBLPREFIX}sources     WHERE s_id   =? AND s_file   =? UNION ALL ".
			"SELECT 'OBJE' FROM {$TBLPREFIX}media       WHERE m_media=? AND m_gedfile=? UNION ALL ".
			"SELECT o_type FROM {$TBLPREFIX}other       WHERE o_id   =? AND o_file   =?"
		, array( $xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id));
	}
}

/**
* update the is_dead status in the database
*
* this function will update the is_dead field in the individuals table with the correct value
* calculated by the is_dead() function.  To improve import performance, the is_dead status is first
* set to -1 during import.  The first time the is_dead status is retrieved this function is called to update
* the database.  This makes the first request for a person slower, but will speed up all future requests.
* @param string $xref id of individual to update
* @param string $ged_id gedcom to update
* @param bool $isdead true=dead
*/
function update_isdead($xref, $ged_id, $isdead) {
	global $TBLPREFIX, $gBitDb;

	$isdead=$isdead ? 1 : 0; // DB uses int, not bool
	$gBitDb->query("UPDATE {$TBLPREFIX}individuals SET i_isdead=? WHERE i_id=? AND i_file=?", array($isdead, $xref, $ged_id));
	return $isdead;
}

// Reset the i_isdead status for individuals
// This is necessary when we change the MAX_ALIVE_YEARS value
function reset_isdead($ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	$gBitDb->query("UPDATE {$TBLPREFIX}individuals SET i_isdead=-1 WHERE i_file=?", array($ged_id));
}

/**
* get a list of all the sources
*
* returns an array of all of the sources in the database.
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#sources
* @return array the array of sources
*/
function get_source_list($ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows=
		$gBitDb->getAll(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}sources s WHERE s_file=?"
			, array($ged_id));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Source::getInstance($row);
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}

// Get a list of repositories from the database
// $ged_id - the gedcom to search
function get_repo_list($ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows =
		$gBitDb->GetAssoc(
			"SELECT 'REPO' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE o_type='REPO' AND o_file=?"
			, array($ged_id));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Repository::getInstance($row);
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}

//-- get the shared note list from the datastore
function get_note_list($ged_id) {
	global $TBLPREFIX, $gBitDb;

	$rows =
		$gBitDb->GetAssoc(
			"SELECT 'NOTE' AS type, o_id AS xref, {$ged_id} AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE o_type=? AND o_file=?"
			, array('NOTE', $ged_id));

	$list=array();
	foreach ($rows as $row) {
		$list[]=Note::getInstance($row);
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}


// Search for INDIs using custom SQL generated by the report engine
function search_indis_custom($join, $where, $order) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals ".implode(' ', $join).' WHERE '.implode(' AND ', $where);
	if ($order) {
		$sql.=' ORDER BY '.implode(' ', $order);
	}

	$list=array();
	$rows = $gBitDb->getAssoc( $sql );
	$GED_ID=PGV_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$list[]=Person::getInstance($row);
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search for FAMs using custom SQL generated by the report engine
function search_fams_custom($join, $where, $order) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families ".implode(' ', $join).' WHERE '.implode(' AND ', $where);
	if ($order) {
		$sql.=' ORDER BY '.implode(' ', $order);
	}

	$list=array();
	$rows = $gBitDb->getAssoc( $sql );
	$GED_ID=PGV_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$list[]=Family::getInstance($row);
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search the gedcom records of indis
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_indis($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION, $gBitDb;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(UTF8_strtoupper($q), '/');
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="i_gedcom LIKE '%{$q}%";
		} else {
			$querysql[]="(i_gedcom LIKE '%{$q}%' OR i_gedcom LIKE '%'".UTF8_strtoupper($q)."%' OR i_gedcom LIKE '%".UTF8_strtolower($q)."%')";
		}
	}

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals WHERE (".implode(" {$match} ", $querysql).') AND i_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (PGV_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows = $gBitDb->getAll( $sql );
	$GED_ID=PGV_GED_ID;
	while ( $row = $roes->fetchRow() ) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Person::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=UTF8_strtoupper($indi->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/\n\d\ '.PGV_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}
		$list[]=$indi;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search the names of indis
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
function search_indis_names($query, $geds, $match) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION, $gBitDb;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	foreach ($query as $q) {
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="n_full LIKE '%{$q}%'";
		} else {
			$querysql[]="(n_full LIKE '%{$q}%' OR n_full LIKE '%".UTF8_strtoupper($q)."%' OR n_full LIKE '%".UTF8_strtolower($q)."%')";
		}
	}
	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex, n_num FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON i_id=n_id AND i_file=n_file WHERE (".implode(" {$match} ", $querysql).') AND i_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows = $gBitDb->getAssoc( $sql );
	$GED_ID=PGV_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Person::getInstance($row);
		if ($indi->canDisplayName()) {
			$indi->setPrimaryName($row['n_num']);
			// We need to clone $indi, as we may have multiple references to the
			// same person in this list, and the "primary name" would otherwise
			// be shared amongst all of them.  This has some performance/memory
			// implications, and there is probably a better way.  This, however,
			// is clean, easy and works.
			$list[]=clone $indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search for individuals names/places using soundex
// $soundex - standard or dm
// $lastname, $firstname, $place - search terms
// $geds - array of gedcoms to search
function search_indis_soundex($soundex, $lastname, $firstname, $place, $geds) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals";
	if ($place) {
		$sql.=" JOIN {$TBLPREFIX}placelinks ON (pl_file=i_file AND pl_gid=i_id)";
		$sql.=" JOIN {$TBLPREFIX}places ON (p_file=pl_file AND pl_p_id=p_id)";
	}
	if ($firstname || $lastname) {
		$sql.=" JOIN {$TBLPREFIX}name ON (i_file=n_file AND i_id=n_id)";
			}
	$sql.=' WHERE i_file IN ('.implode(',', $geds).')';
	switch ($soundex) {
	case 'Russell':
		$givn_sdx=explode(':', soundex_std($firstname));
		$surn_sdx=explode(':', soundex_std($lastname));
		$plac_sdx=explode(':', soundex_std($place));
		$field='std';
		break;
	default:
	case 'DaitchM':
		$givn_sdx=explode(':', soundex_dm($firstname));
		$surn_sdx=explode(':', soundex_dm($lastname));
		$plac_sdx=explode(':', soundex_dm($place));
		$field='dm';
		break;
	}
	if ($firstname && $givn_sdx) {
		foreach ($givn_sdx as $k=>$v) {
			$givn_sdx[$k]="n_soundex_givn_{$field} LIKE '%{$v}%'";
	}
		$sql.=' AND ('.implode(' OR ', $givn_sdx).')';
		}
	if ($lastname && $surn_sdx) {
		foreach ($surn_sdx as $k=>$v) {
			$surn_sdx[$k]="n_soundex_surn_{$field} LIKE '%{$v}%'";
		}
		$sql.=' AND ('.implode(' OR ', $surn_sdx).')';
			}
	if ($place && $plac_sdx) {
		foreach ($plac_sdx as $k=>$v) {
			$plac_sdx[$k]="p_{$field}_soundex LIKE '%{$v}%'";
		}
		$sql.=' AND ('.implode(' OR ', $plac_sdx).')';
	}

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows = $gBitDb->getAssoc( $sql );
	$GED_ID=PGV_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Person::getInstance($row);
		if ($indi->canDisplayName()) {
			$list[]=$indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

/**
* get recent changes since the given julian day inclusive
* @author yalnifj
* @param int $jd, leave empty to include all
*/
function get_recent_changes($jd=0, $allgeds=false) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT d_gid FROM {$TBLPREFIX}dates WHERE d_fact='CHAN' AND d_julianday1>=? AND d_gid NOT LIKE ?";
	$vars=array($jd, '%:%');
	if (!$allgeds) {
		$sql.=" AND d_file=?";
		$vars[]=PGV_GED_ID;
	}
	$sql.=" ORDER BY d_julianday1 DESC";

	return $gBitDb->getOne( $sql, $vars );
}

// Seach for individuals with events on a given day
function search_indis_dates($day, $month, $year, $facts) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}dates ON i_id=d_gid AND i_file=d_file WHERE i_file=?";
	$vars=array(PGV_GED_ID);
	if ($day) {
		$sql.=" AND d_day=?";
		$vars[]=$day;
	}
	if ($month) {
		$sql.=" AND d_month=?";
		$vars[]=$month;
	}
	if ($year) {
		$sql.=" AND d_year=?";
		$vars[]=$year;
	}
	if ($facts) {
		$facts=preg_split('/[, ;]+/', $facts);
		foreach ($facts as $key=>$value) {
			if ($value[0]=='!') {
				$facts[$key]="d_fact!=?";
				$vars[]=substr($value,1);
			} else {
				$facts[$key]="d_fact=?";
				$vars[]=$value;
			}
		}
		$sql.=' AND '.implode(' AND ', $facts);
	}

	$list=array();
	$rows = $gBitDb->getAssoc( $sql, $vars );
	foreach ($rows as $row) {
		$list[]=Person::getInstance($row);
	}
	return $list;
}

// Seach for individuals with events in a given date range
function search_indis_daterange($start, $end, $facts) {
	global $TBLPREFIX, $gBitDb;

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}dates ON i_id=d_gid AND i_file=d_file WHERE i_file=? AND d_julianday1 BETWEEN ? AND ?";
	$vars=array(PGV_GED_ID, $start, $end);
	
	if ($facts) {
		$facts=explode(',', $facts);
		foreach ($facts as $key=>$value) {
			$facts[$key]="?";
			$vars[]=$value;
		}
		$sql.=' AND d_fact IN ('.implode(',', $facts).')';
	}

	$list=array();
	$rows = $gBitDb->getAssoc( $sql, $vars );
	foreach ($rows as $row) {
		$list[]=Person::getInstance($row);
	}
	return $list;
}

// Search for people who had events in a given year range
function search_indis_year_range($startyear, $endyear) {
	// TODO: We should use Julian-days, rather than gregorian years,
	// to allow
	// the lifespan chart, etc., to use other calendars.
	$startjd=GregorianDate::YMDtoJD($startyear, 1, 1);
	$endjd  =GregorianDate::YMDtoJD($endyear+1, 1, 1)-1;

	return search_indis_daterange($startjd, $endjd, '');
}

// Search the gedcom records of families
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_fams($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION, $gBitDb;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(UTF8_strtoupper($q), '/');

		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="f_gedcom LIKE '%{$q}%'";
		} else {
			$querysql[]="(f_gedcom LIKE '%{$q}%' OR f_gedcom LIKE '%".UTF8_strtoupper($q)."%' OR f_gedcom LIKE '%".UTF8_strtolower($q)."%')";
		}
	}

	$sql="SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families WHERE (".implode(" {$match} ", $querysql).') AND f_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (PGV_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows = $gBitDb->query( $sql );
	$GED_ID=PGV_GED_ID;
	while ( $row = $rows->fetchRow() ) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$family=Family::getInstance( $row );
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=UTF8_strtoupper($family->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/\n\d\ '.PGV_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}

		$list[]=$family;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search the names of the husb/wife in a family
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
function search_fams_names($query, $geds, $match) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION, $gBitDb;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	foreach ($query as $q) {
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="(husb.n_full LIKE '%{$q}%' OR wife.n_full LIKE '%{$q}%')";
		} else {
			$querysql[]="(husb.n_full LIKE '%{$q}%' OR wife.n_full LIKE '%{$q}%' OR husb.n_full LIKE '%".UTF8_strtoupper($q)."%' OR husb.n_full LIKE '%".UTF8_strtolower($q)."%' OR wife.n_full LIKE '%".UTF8_strtoupper($q)."%' OR wife.n_full LIKE '%".UTF8_strtolower($q)."%' )";
		}
	}

	$sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families LEFT OUTER JOIN {$TBLPREFIX}name husb ON f_husb=husb.n_id AND f_file=husb.n_file LEFT OUTER JOIN {$TBLPREFIX}name wife ON f_wife=wife.n_id AND f_file=wife.n_file WHERE (".implode(" {$match} ", $querysql).') AND f_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows = $gBitDb->query( $sql );
	$GED_ID=PGV_GED_ID;
	while ( $row = $rows->fetchRow() ) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Family::getInstance( $row );
		if ($indi->canDisplayName()) {
			$list[]=$indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search the gedcom records of sources
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_sources($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION, $gBitDb;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(UTF8_strtoupper($q), '/');
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="s_gedcom LIKE '%{$q}%'";
		} else {
			$querysql[]="(s_gedcom LIKE '%{$q}%' OR s_gedcom LIKE '%".UTF8_strtoupper($q)."' OR s_gedcom LIKE '%".UTF8_strtolower($q)."%')";
		}
	}

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}sources WHERE (".implode(" {$match} ", $querysql).') AND s_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (PGV_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows = $gBitDb->query( $sql );
	$GED_ID=PGV_GED_ID;
	while ( $row= $rows->fetchRow() ) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$source=Source::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=UTF8_strtoupper($source->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/\n\d\ '.PGV_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}
		$list[]=$source;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

// Search the gedcom records of shared notes
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_notes($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION, $gBitDb;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();
	
	foreach ($query as $q) {
		$queryregex[]=preg_quote(UTF8_strtoupper($q), '/');
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="o_gedcom LIKE '%{$q}%'";
		} else {
			$querysql[]="(o_gedcom LIKE '%{$q}% OR o_gedcom LIKE '%".UTF8_strtoupper($q)."%' OR o_gedcom LIKE '%".UTF8_strtolower($q)."%'";
		}
	}

	$sql="SELECT 'NOTE' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE (".implode(" {$match} ", $querysql).") AND o_type='NOTE' AND o_file IN (".implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (PGV_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$result = $gBitDb->query($sql);
	$GED_ID=PGV_GED_ID;
	while ( $row = $result->fetchRow() ) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$note=Note::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=UTF8_strtoupper($note->getGedcomRecord());
		foreach ( $queryregex as $q ) {
			if (!preg_match('/(\n\d|^0 @'.PGV_REGEX_XREF.'@) '.PGV_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}
		$list[]=$note;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=PGV_GED_ID) {
		$GEDCOM=get_gedcom_from_id(PGV_GED_ID);
		load_privacy_file(PGV_GED_ID);
	}
	return $list;
}

/**
* get place parent ID
* @param array $parent
* @param int $level
* @return int
*/
function get_place_parent_id($parent, $level) {
	global $TBLPREFIX, $gBitDb;

	$parent_id=0;
	for ($i=0; $i<$level; $i++) {
		$p_id = $gBitDb->getOne( 
				"SELECT p_id FROM {$TBLPREFIX}places WHERE p_level=? AND p_parent_id=? AND p_place LIKE ? AND p_file=?"
				, array($i, $parent_id, $parent[$i], PGV_GED_ID));
		if (is_null($p_id)) {
			break;
		}
		$parent_id = $p_id;
	}
	return $parent_id;
}

/**
* find all of the places in the hierarchy
* The $parent array holds the parent hierarchy of the places
* we want to get.  The level holds the level in the hierarchy that
* we are at.
*/
function get_place_list($parent, $level) {
	global $TBLPREFIX, $gBitDb;

	// --- find all of the place in the file
	if ($level==0) {
		return
			$gBitDb->getOne("" .
				"SELECT p_place FROM {$TBLPREFIX}places WHERE p_level=? AND p_file=? ORDER BY p_place"
				, array(0, PGV_GED_ID));
	} else {
		return
			$gBitDb->getOne(
				"SELECT p_place FROM {$TBLPREFIX}places WHERE p_level=? AND p_parent_id=? AND p_file=? ORDER BY p_place"
				, array($level, get_place_parent_id($parent, $level), PGV_GED_ID));
	}
}

/**
* get all of the place connections
* @param array $parent
* @param int $level
* @return array
*/
function get_place_positions($parent, $level='') {
	global $TBLPREFIX, $gBitDb;

	// TODO: this function needs splitting into two

	if ($level!=='') {
		return
			$gBitDb->getOne(
				"SELECT DISTINCT pl_gid FROM {$TBLPREFIX}placelinks WHERE pl_p_id=? AND pl_file=?"
				, array(get_place_parent_id($parent, $level), PGV_GED_ID));
	} else {
		//-- we don't know the level so get the any matching place
		return
			$gBitDb->getOne(
				"SELECT DISTINCT pl_gid FROM {$TBLPREFIX}placelinks, {$TBLPREFIX}places WHERE p_place LIKE ? AND p_file=pl_file AND p_id=pl_p_id AND p_file=?"
				, array($parent, PGV_GED_ID));
	}
}

//-- find all of the places
function find_place_list($place) {
	global $TBLPREFIX, $gBitDb;

	$rows=
		$gBitDb->getAll(
		"SELECT p_id, p_place, p_parent_id  FROM {$TBLPREFIX}places WHERE p_file=? ORDER BY p_parent_id, p_id"
		, array(PGV_GED_ID));

	$placelist=array();
	foreach ($rows as $row) {
		if ($row->p_parent_id==0) {
			$placelist[$row->p_id] = $row->p_place;
		} else {
			$placelist[$row->p_id] = $placelist[$row->p_parent_id].", ".$row->p_place;
		}
	}
	if (!empty($place)) {
		$found = array();
		foreach ($placelist as $indexval => $pplace) {
			if (preg_match("/$place/i", $pplace)>0) {
				$upperplace = UTF8_strtoupper($pplace);
				if (!isset($found[$upperplace])) {
					$found[$upperplace] = $pplace;
				}
			}
		}
		$placelist = array_values($found);
	}
	return $placelist;
}

//-- function to find the gedcom id for the given rin
function find_rin_id($rin) {
	global $TBLPREFIX, $gBitDb;

	$xref=
		$gBitDb->getOne(
			"SELECT i_id FROM {$TBLPREFIX}individuals WHERE i_rin=? AND i_file=?"
			, array($rin, PGV_GED_ID));

	return $xref ? $xref : $rin;
}

/**
* Delete a gedcom from the database and the system
* Does not delete the file from the file system
* @param string $ged  the filename of the gedcom to delete
*/
function delete_gedcom($ged) {
	global $TBLPREFIX, $pgv_changes, $GEDCOMS, $gBitDb;

	if (!isset($GEDCOMS[$ged])) {
		return;
	}

	$ged_id=get_id_from_gedcom($ged);

	$gBitDb->query("DELETE FROM {$TBLPREFIX}blocks        WHERE b_username=?", array($ged));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}dates         WHERE d_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}families      WHERE f_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}favorites     WHERE fv_file   =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}individuals   WHERE i_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}link          WHERE l_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}media         WHERE m_gedfile =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}media_mapping WHERE mm_gedfile=?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}name          WHERE n_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}news          WHERE n_username=?", array($ged));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}nextid        WHERE ni_gedfile=?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}other         WHERE o_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}placelinks    WHERE pl_file   =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}places        WHERE p_file    =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}sources       WHERE s_file    =?", array($ged_id));

	if (isset($pgv_changes)) {
		//-- erase any of the changes
		foreach ($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$ged) {
				unset($pgv_changes[$cid]);
			}
		}
		write_changes();
	}

	unset($GEDCOMS[$ged]);
	store_gedcoms();

	if (get_site_setting('DEFAULT_GEDCOM')==$ged) {
		set_site_setting('DEFAULT_GEDCOM', '');
	}
}

/**
* get the top surnames
* @param int $num how many surnames to return
* @return array
*/
function get_top_surnames($num) {
	global $TBLPREFIX, $gBitDb;

	$result = $gBitDb->query(
			"SELECT COUNT(n_surname) AS sn_count, n_surn FROM {$TBLPREFIX}name WHERE n_file=? AND n_type!=? AND n_surn NOT IN (?, ?, ?, ?) GROUP BY n_surn ORDER BY sn_count DESC"
			, array(PGV_GED_ID, '_MARNM', '@N.N.', '', '?', 'UNKNOWN')
			, $num+1);
	while ( $row = $result->fetchRow() )
	{
		if (isset($surnames[$row['n_surn']]['match'])) {
			$surnames[$row['n_surn']]['match'] += $row['count'];
		} else {
			$surnames[$row['n_surn']]['name'] = $row['n_surn'];
			$surnames[$row['n_surn']]['match'] = $row['count'];
		}
	}
	return $surnames;
}

/**
* get next unique id for the given table
* @param string $table  the name of the table
* @param string $field the field to get the next number for
* @return int the new id
*/
function get_next_id($table, $field) {
	global $TBLPREFIX, $TABLE_IDS, $gBitDb;

	if (!isset($TABLE_IDS)) {
		$TABLE_IDS = array();
	}
	if (isset($TABLE_IDS[$table][$field])) {
		$TABLE_IDS[$table][$field]++;
		return $TABLE_IDS[$table][$field];
	}
	$newid = $gBitDb->getOne("SELECT MAX({$field}) FROM {$TBLPREFIX}{$table}");
	$newid++;
	$TABLE_IDS[$table][$field] = $newid;
	return $newid;
}

/**
* get a list of remote servers
*/
function get_server_list(){
	global $GEDCOM, $GEDCOMS, $TBLPREFIX, $sitelist, $gBitDb;

	$sitelist = array();

	if (isset($GEDCOMS[$GEDCOM]) && check_for_import($GEDCOM)) {
		$rows = $gBitDb->getAll(
			"SELECT s_id, s_name, s_gedcom, s_file FROM {$TBLPREFIX}sources WHERE s_file=? AND s_dbid=? ORDER BY s_name"
			, array(PGV_GED_ID, 'Y'));
		foreach ( $rows as $row ) {
			$source = array();
			$source["name"] = $row['s_name'];
			$source["gedcom"] = $row['s_gedcom'];
			$source["gedfile"] = $row['s_file'];
			$source["url"] = get_gedcom_value("URL", 1, $row['s_gedcom']);
			$sitelist[$row['s_id']] = $source;
		}
	}

	return $sitelist;
}

/**
* Retrieve the array of faqs from the DB table blocks
* @param int $id The FAQ ID to retrieve
* @return array $faqs The array containing the FAQ items
*/
function get_faq_data($id='') {
	global $TBLPREFIX, $GEDCOM, $gBitDb;

	$faqs = array();
	// Read the faq data from the DB
	$sql="SELECT b_id, b_location, b_order, b_config, b_username FROM {$TBLPREFIX}blocks WHERE b_username IN (?, ?) AND b_name=?";
	$vars=array($GEDCOM, '*all*', 'faq');
	if ($id!='') {
		$sql.=" AND b_order=?";
		$vars[]=$id;
	} else {
		$sql.=' ORDER BY b_order';
	}
	$rows = $gBitDb->getAll( $sql, $vars );

	foreach ($rows as $row) {
		$faqs[$row['b_order']][$row['b_location']]["text"  ]=unserialize($row['b_config']);
		$faqs[$row['b_order']][$row['b_location']]["pid"   ]=$row['b_id'];
		$faqs[$row['b_order']][$row['b_location']]["gedcom"]=$row['b_username'];
	}
	return $faqs;
}

function delete_fact($linenum, $pid, $gedrec) {
	global $linefix, $pgv_lang;

	if (!empty($linenum)) {
		if ($linenum==0) {
			if (delete_gedrec($pid)) {
				print $pgv_lang["gedrec_deleted"];
			}
		} else {
			$gedlines = explode("\n", $gedrec);
			// NOTE: The array_pop is used to kick off the last empty element on the array
			// NOTE: To prevent empty lines in the GEDCOM
			// DEBUG: Records without line breaks are imported as 1 big string
			if ($linefix > 0) {
				array_pop($gedlines);
			}
			$newged = "";
			// NOTE: Add all lines that are before the fact to be deleted
			for ($i=0; $i<$linenum; $i++) {
				$newged .= trim($gedlines[$i])."\n";
			}
			if (isset($gedlines[$linenum])) {
				$fields = explode(' ', $gedlines[$linenum]);
				$glevel = $fields[0];
				$ctlines = count($gedlines);
				$i++;
				if ($i<$ctlines) {
					// Remove the fact
					while ((isset($gedlines[$i]))&&($gedlines[$i]{0}>$glevel)) {
						$i++;
					}
					// Add the remaining lines
					while ($i<$ctlines) {
						$newged .= $gedlines[$i]."\n";
						$i++;
					}
				}
			}
			if ($newged != "") {
				return $newged;
			}
		}
	}
}

/**
* get_remote_id Recieves a RFN key and returns a Stub ID if the RFN exists
*
* @param mixed $rfn RFN number to see if it exists
* @access public
* @return gid Stub ID that contains the RFN number. Returns false if it didn't find anything
*/
function get_remote_id($rfn) {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne(
			"SELECT r_gid FROM {$TBLPREFIX}remotelinks WHERE r_linkid=? AND r_file=?"
			,array($rfn, PGV_GED_ID));
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of events whose anniversary occured on a given julian day.
// Used on the on-this-day/upcoming blocks and the day/month calendar views.
// $jd     - the julian day
// $facts  - restrict the search to just these facts or leave blank for all
// $ged_id - the id of the gedcom to search
////////////////////////////////////////////////////////////////////////////////
function get_anniversary_events($jd, $facts='', $ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	// If no facts specified, get all except these
	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";
	if ($facts!='_TODO') {
		$skipfacts.=',_TODO';
	}

	$found_facts=array();
	foreach (array(new GregorianDate($jd), new JulianDate($jd), new FrenchRDate($jd), new JewishDate($jd), new HijriDate($jd)) as $anniv) {
		// Build a SQL where clause to match anniversaries in the appropriate calendar.
		$where="WHERE d_type='".$anniv->CALENDAR_ESCAPE()."'";
		// SIMPLE CASES:
		// a) Non-hebrew anniversaries
		// b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
		if ($anniv->CALENDAR_ESCAPE()!='@#DHEBREW@' || in_array($anniv->m, array(1, 5, 9, 10, 11, 12, 13))) {
			// Dates without days go on the first day of the month
			// Dates with invalid days go on the last day of the month
			if ($anniv->d==1) {
				$where.=" AND d_day<=1";
			} else
				if ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d}";
				} else {
					$where.=" AND d_day={$anniv->d}";
				}
			$where.=" AND d_mon={$anniv->m}";
		} else {
			// SPECIAL CASES:
			switch ($anniv->m) {
			case 2:
				// 29 CSH does not include 30 CSH (but would include an invalid 31 CSH if there were no 30 CSH)
				if ($anniv->d==1) {
					$where.=" AND d_day<=1 AND d_mon=2";
				} elseif ($anniv->d==30) {
					$where.=" AND d_day>=30 AND d_mon=2";
				} elseif ($anniv->d==29 && $anniv->DaysInMonth()==29) {
					$where.=" AND (d_day=29 OR d_day>30) AND d_mon=2";
				} else {
					$where.=" AND d_day={$anniv->d} AND d_mon=2";
				}
				break;
			case 3:
				// 1 KSL includes 30 CSH (if this year didn't have 30 CSH)
				// 29 KSL does not include 30 KSL (but would include an invalid 31 KSL if there were no 30 KSL)
				if ($anniv->d==1) {
					$tmp=new JewishDate(array($anniv->y, 'csh', 1));
					if ($tmp->DaysInMonth()==29) {
						$where.=" AND (d_day<=1 AND d_mon=3 OR d_day=30 AND d_mon=2)";
					} else {
						$where.=" AND d_day<=1 AND d_mon=3";
					}
				} else
					if ($anniv->d==30) {
						$where.=" AND d_day>=30 AND d_mon=3";
					} elseif ($anniv->d==29 && $anniv->DaysInMonth()==29) {
						$where.=" AND (d_day=29 OR d_day>30) AND d_mon=3";
					} else {
						$where.=" AND d_day={$anniv->d} AND d_mon=3";
					}
				break;
			case 4:
				// 1 TVT includes 30 KSL (if this year didn't have 30 KSL)
				if ($anniv->d==1) {
					$tmp=new JewishDate($anniv->y, 'ksl', 1);
					if ($tmp->DaysInMonth()==29) {
						$where.=" AND (d_day<=1 AND d_mon=4 OR d_day=30 AND d_mon=3)";
					} else {
						$where.=" AND d_day<=1 AND d_mon=4";
					}
				} else
					if ($anniv->d==$anniv->DaysInMonth()) {
						$where.=" AND d_day>={$anniv->d} AND d_mon=4";
					} else {
						$where.=" AND d_day={$anniv->d} AND d_mon=4";
					}
				break;
			case 6: // ADR (non-leap) includes ADS (leap)
				if ($anniv->d==1) {
					$where.=" AND d_day<=1";
				} elseif ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d}";
				} else {
					$where.=" AND d_day={$anniv->d}";
				}
				if ($anniv->IsLeapYear()) {
					$where.=" AND (d_mon=6 AND ".$gBitDb->mod_function("7*d_year+1","19")."<7)";
				} else {
					$where.=" AND (d_mon=6 OR d_mon=7)";
				}
				break;
			case 7: // ADS includes ADR (non-leap)
				if ($anniv->d==1) {
					$where.=" AND d_day<=1";
				} elseif ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d}";
				} else {
					$where.=" AND d_day={$anniv->d}";
				}
				$where.=" AND (d_mon=6 AND ".$gBitDb->mod_function("7*d_year+1","19").">=7 OR d_mon=7)";
				break;
			case 8: // 1 NSN includes 30 ADR, if this year is non-leap
				if ($anniv->d==1) {
					if ($anniv->IsLeapYear()) {
						$where.=" AND d_day<=1 AND d_mon=8";
					} else {
						$where.=" AND (d_day<=1 AND d_mon=8 OR d_day=30 AND d_mon=6)";
					}
				} elseif ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d} AND d_mon=8";
				} else {
					$where.=" AND d_day={$anniv->d} AND d_mon=8";
				}
				break;
			}
		}
		// Only events in the past (includes dates without a year)
		$where.=" AND d_year<={$anniv->y}";
		// Restrict to certain types of fact
		if (empty($facts)) {
			$excl_facts="'".preg_replace('/\W+/', "','", $skipfacts)."'";
			$where.=" AND d_fact NOT IN ({$excl_facts})";
		} else {
			$incl_facts="'".preg_replace('/\W+/', "','", $facts)."'";
			$where.=" AND d_fact IN ({$incl_facts})";
		}
		// Only get events from the current gedcom
		$where.=" AND d_file=".$ged_id;

		// Now fetch these anniversaries
		$ind_sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex, d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}individuals {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_day ASC, d_year DESC";
		$fam_sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil, d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}families {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_day ASC, d_year DESC";
		foreach (array($ind_sql, $fam_sql) as $sql) {
			$rows = $gBitDb->getAll( $sql );
			foreach ($rows as $row) {
				if ($row['type']=='INDI') {
					$record=Person::getInstance($row);
				} else {
					$record=Family::getInstance($row);
				}
				// Generate a regex to match the retrieved date - so we can find it in the original gedcom record.
				// TODO having to go back to the original gedcom is lame.  This is why it is so slow, and needs
				// to be cached.  We should store the level1 fact here (or somewhere)
				if ($row['d_type']=='@#DJULIAN@') {
					if ($row['d_year']<0) {
						$year_regex=$row['d_year']." ?[Bb]\.? ?[Cc]\.\ ?";
					} else {
						$year_regex="({$row['d_year']}|".($row['d_year']-1)."\/".($row['d_year']%100).")";
					}
				} else
					$year_regex="0*".$row['d_year'];
				$ged_date_regex="/2 DATE.*(".($row['d_day']>0 ? "0?{$row['d_day']}\s*" : "").$row['d_month']."\s*".($row['d_year']!=0 ? $year_regex : "").")/i";
				foreach (get_all_subrecords($row['gedrec'], $skipfacts, false, false) as $factrec) {
					if (preg_match("/(^1 {$row['d_fact']}|^1 (FACT|EVEN).*\n2 TYPE {$row['d_fact']})/s", $factrec) && preg_match($ged_date_regex, $factrec) && preg_match('/2 DATE (.+)/', $factrec, $match)) {
						$date=new GedcomDate($match[1]);
						if (preg_match('/2 PLAC (.+)/', $factrec, $match)) {
							$plac=$match[1];
						} else {
							$plac='';
						}
						if (showFactDetails($row['d_fact'], $row['xref']) && !FactViewRestricted($row['xref'], $factrec)) {
							$found_facts[]=array(
								'record'=>$record,
								'id'=>$row['xref'],
								'objtype'=>$row['type'],
								'fact'=>$row['d_fact'],
								'factrec'=>$factrec,
								'jd'=>$jd,
								'anniv'=>($row['d_year']==0?0:$anniv->y-$row['d_year']),
								'date'=>$date,
								'plac'=>$plac
							);
						}
					}
				}
			}
		}
	}
	return $found_facts;
}


////////////////////////////////////////////////////////////////////////////////
// Get a list of events which occured during a given date range.
// TODO: Used by the recent-changes block and the calendar year view.
// $jd1, $jd2 - the range of julian day
// $facts     - restrict the search to just these facts or leave blank for all
// $ged_id    - the id of the gedcom to search
////////////////////////////////////////////////////////////////////////////////
function get_calendar_events($jd1, $jd2, $facts='', $ged_id=PGV_GED_ID) {
	global $TBLPREFIX, $gBitDb;

	// If no facts specified, get all except these
	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";
	if ($facts!='_TODO') {
		$skipfacts.=',_TODO';
	}

	$found_facts=array();

	// This where clause gives events that start/end/overlap the period
	// e.g. 1914-1918 would show up on 1916
	//$where="WHERE d_julianday1 <={$jd2} AND d_julianday2>={$jd1}";
	// This where clause gives only events that start/end during the period
	$where="WHERE (d_julianday1>={$jd1} AND d_julianday1<={$jd2} OR d_julianday2>={$jd1} AND d_julianday2<={$jd2})";

	// Restrict to certain types of fact
	if (empty($facts)) {
		$excl_facts="'".preg_replace('/\W+/', "','", $skipfacts)."'";
		$where.=" AND d_fact NOT IN ({$excl_facts})";
	} else {
		$incl_facts="'".preg_replace('/\W+/', "','", $facts)."'";
		$where.=" AND d_fact IN ({$incl_facts})";
	}
	// Only get events from the current gedcom
	$where.=" AND d_file=".$ged_id;

	// Now fetch these events
	$ind_sql="SELECT d_gid, i_gedcom, 'INDI', d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}individuals {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_julianday1";
	$fam_sql="SELECT d_gid, f_gedcom, 'FAM',  d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}families    {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_julianday1";
	foreach (array($ind_sql, $fam_sql) as $sql) {
		$rows = $gBitDb->getAll->fetchAll($sql);
		foreach ($rows as $row) {
			// Generate a regex to match the retrieved date - so we can find it in the original gedcom record.
			// TODO having to go back to the original gedcom is lame.  This is why it is so slow, and needs
			// to be cached.  We should store the level1 fact here (or somewhere)
			if ($row[8]=='@#DJULIAN@') {
				if ($row[6]<0) {
					$year_regex=$row[6]." ?[Bb]\.? ?[Cc]\.\ ?";
				} else {
					$year_regex="({$row[6]}|".($row[6]-1)."\/".($row[6]%100).")";
				}
			} else {
				$year_regex="0*".$row[6];
			}
			$ged_date_regex="/2 DATE.*(".($row[4]>0 ? "0?{$row[4]}\s*" : "").$row[5]."\s*".($row[6]!=0 ? $year_regex : "").")/i";
			foreach (get_all_subrecords($row[1], $skipfacts, false, false) as $factrec) {
				if (preg_match("/(^1 {$row[7]}|^1 (FACT|EVEN).*\n2 TYPE {$row[7]})/s", $factrec) && preg_match($ged_date_regex, $factrec) && preg_match('/2 DATE (.+)/', $factrec, $match)) {
					$date=new GedcomDate($match[1]);
					if (preg_match('/2 PLAC (.+)/', $factrec, $match)) {
						$plac=$match[1];
					} else {
						$plac='';
					}
					if (showFactDetails($row[7], $row[0]) && !FactViewRestricted($row[0], $factrec)) {
						$found_facts[]=array(
							'id'=>$row[0],
							'objtype'=>$row[2],
							'fact'=>$row[7],
							'factrec'=>$factrec,
							'jd'=>$jd1,
							'anniv'=>0,
							'date'=>$date,
							'plac'=>$plac
						);
					}
				}
			}
		}
	}
	return $found_facts;
}


/**
* Get the list of current and upcoming events, sorted by anniversary date
*
* This function is used by the Todays and Upcoming blocks on the Index and Portal
* pages.  It is also used by the RSS feed.
*
* Special note on unknown day-of-month:
* When the anniversary date is imprecise, the sort will pretend that the day-of-month
* is either tomorrow or the first day of next month.  These imprecise anniversaries
* will sort to the head of the chosen day.
*
* Special note on Privacy:
* This routine does not check the Privacy of the events in the list.  That check has
* to be done by the routine that makes use of the event list.
*/
function get_events_list($jd1, $jd2, $events='') {
	$found_facts=array();
	for ($jd=$jd1; $jd<=$jd2; ++$jd) {
		$found_facts=array_merge($found_facts, get_anniversary_events($jd, $events));
	}
	return $found_facts;
}

////////////////////////////////////////////////////////////////////////////////
// Check if a media file is shared (i.e. used by another gedcom)
////////////////////////////////////////////////////////////////////////////////
function is_media_used_in_other_gedcom($file_name, $ged_id) {
	global $TBLPREFIX, $gBitDb;

	return
		(bool)$gBitDb->getOne(
			"SELECT COUNT(*) FROM {$TBLPREFIX}media WHERE m_file LIKE ? AND m_gedfile<>?"
			, array("%{$file_name}", $ged_id));
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the PGV_SITE_SETTING table
////////////////////////////////////////////////////////////////////////////////
function get_site_setting($site_setting_name, $default=null) {
	global $TBLPREFIX, $gBitDb;

	return $gBitDb->getOne(
		"SELECT COALESCE( site_setting_value, '{$default}' ) FROM {$TBLPREFIX}site_setting WHERE site_setting_name=?"
		, array($site_setting_name) );
}

function set_site_setting($site_setting_name, $site_setting_value) {
	global $TBLPREFIX, $gBitDb;
	// We can't cache/reuse prepared statements here, as we need to call this
	// function after performing DDL statements, and these invalidate any
	// existing prepared statement handles in some databases.

	$old_site_setting_value=
		$gBitDb->getOne(
			"SELECT site_setting_value FROM {$TBLPREFIX}site_setting WHERE site_setting_name=?"
			, array($site_setting_name));

	if (is_null($old_site_setting_value)) {
		// Value doesn't exist - insert
		$gBitDb->query(
			"INSERT INTO {$TBLPREFIX}site_setting (site_setting_name, site_setting_value) VALUES (?, ?)"
			, array($site_setting_name, $site_setting_value));
	} elseif ($old_site_setting_value!=$site_setting_value) {
		// Value exists, and is different
		$gBitDb->query(
			"UPDATE {$TBLPREFIX}site_setting SET site_setting_value=? WHERE site_setting_name=?"
			, array($site_setting_value, $site_setting_name));	
	}
}
function delete_site_setting($site_setting_name) {
	global $TBLPREFIX, $gBitDb;
	$gBitDb->query(
		"DELETE FROM {$TBLPREFIX}site_setting WHERE site_setting_name=?"
		, array($site_setting_name));
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the PGV_GEDCOM table
// A future version of PGV will have a table PGV_GEDCOM, which will
// contain the values currently stored the array $GEDCOMS[].
//
// Until then, we use this "logical" structure, but with the access functions
// mapped onto the existing "physical" structure.
////////////////////////////////////////////////////////////////////////////////

function get_all_gedcoms() {
	global $GEDCOMS, $gGedcom;

	$gedcoms=array();
/*	foreach ($GEDCOMS as $key=>$value) {
		$gedcoms[$value['id']]=$key;
	}
*/	
	$gedcoms = $gGedcom->getList();
	uasort($gedcoms, 'stringsort');
	return $gedcoms;
}

function get_gedcom_from_id( $ged_id ) {
/*	global $GEDCOMS, $gGedcom;

	if (isset($GEDCOMS[$ged_id])) {
		return $ged_id;
	}
	foreach ($GEDCOMS as $ged=>$gedarray) {
		if ( $gedarray['id'] == $ged_id ) {
			return $ged;
		}
	}

*/
	return $ged_id;
}

function get_id_from_gedcom( $ged_name ) {
	global $GEDCOMS, $gGedcom;

//	if (array_key_exists($ged_name, $GEDCOMS)) {
//		return (int)$GEDCOMS[$ged_name]['id']; // Cast to (int) for safe use in SQL
//	} else {
		return $gGedcom->mGEDCOMId;
//	}
}


////////////////////////////////////////////////////////////////////////////////
// Functions to access the PGV_GEDCOM_SETTING table
// A future version of PGV will have a table PGV_GEDCOM_SETTING, which will
// contain the values currently stored the array $GEDCOMS[].
//
// Until then, we use this "logical" structure, but with the access functions
// mapped onto the existing "physical" structure.
////////////////////////////////////////////////////////////////////////////////

function get_gedcom_setting( $ged_id, $parameter ) {
	global $GEDCOMS;
	$ged_id = get_gedcom_from_id($ged_id);
	if (array_key_exists($ged_id, $GEDCOMS) && array_key_exists($parameter, $GEDCOMS[$ged_id])) {
		return $GEDCOMS[get_gedcom_from_id($ged_id)][$parameter];
	} else {
		return null;
	}
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the PGV_USER table
// Mapped to BW $gBitUser functions
////////////////////////////////////////////////////////////////////////////////

function get_user_count() {
	global $TBLPREFIX, $gBitDb;

	return
		$gBitDb->getOne("SELECT COUNT(*) FROM `".BIT_DB_PREFIX."users_users`");
}

// Get the ID for a username
// (Currently ID is the same as username, but this will change in the future)
function get_user_id( $username ) {
	global $TBLPREFIX, $gBitDb;

	return $gBitDb->getOne(
			"SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `login`=? OR `email`=?"
			, array($username, $username) );
}

// Get the username for a user ID
// (Currently ID is the same as username, but this will change in the future)
function get_user_name( $user_id ) {
	global $TBLPREFIX, $gBitDb;

	return $gBitDb->getOne(
			"SELECT `real_name` FROM `".BIT_DB_PREFIX."users_users` WHERE `user_id`=? OR `login`=?"
			, array($user_id, $user_id) );
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the PGV_USER_SETTING table
// A future version of PGV will have a table PGV_USER_SETTING, which will
// contain the values currently stored in columns in the table PGV_USERS.
//
// Until then, we use this "logical" structure, but with the access functions
// mapped onto the existing "physical" structure.
//
// $parameter is one of the column names in PGV_USERS, without the u_ prefix.
////////////////////////////////////////////////////////////////////////////////

function get_user_setting( $user_id, $parameter ) {
	global $TBLPREFIX, $gBitDb, $gBitUser;

	return $gBitUser->getPreference( $parameter ); 
	// $gBitDb->getOne("SELECT u_{$parameter} FROM {$TBLPREFIX}users WHERE u_username=?",array($user_id));
}

function set_user_setting( $user_id, $parameter, $value ) {
	global $TBLPREFIX, $gBitDb, $gBitUser;
	$gBitUser->setPreference( $parameter, $value );
//	$gBitDb->query("UPDATE {$TBLPREFIX}users SET u_{$parameter}=? WHERE u_username=?", array($value, $user_id));
}

function admin_user_exists() {
	global $TBLPREFIX, $gBitDb;

	return
		true;
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the PGV_USER_GEDCOM_SETTING table
// A future version of PGV will have a table PGV_USER_GEDCOM_SETTING, which will
// contain the values currently stored in serialized arrays in columns in the
// table PGV_USERS.
//
// Until then, we use this "logical" structure, but with the access functions
// mapped onto the existing "physical" structure.
//
// $parameter is one of: "gedcomid", "rootid" and "canedit".
////////////////////////////////////////////////////////////////////////////////

function get_user_gedcom_setting($user_id, $ged_id, $parameter) {
	$ged_name=get_gedcom_from_id($ged_id);

	$tmp=get_user_setting($user_id, $parameter);
	if (!is_string($tmp)) {
		return null;
	}
	$tmp_array=unserialize($tmp);
	if (!is_array($tmp_array)) {
		return null;
	}
	if (array_key_exists($ged_name, $tmp_array)) {
		// Convert old PGV3.1 values to PGV3.2 format
		if ($parameter=='canedit') {
			if ($tmp_array[$ged_name]=='yes') {
				$tmp_array[$ged_name]=='edit';
			}
			if ($tmp_array[$ged_name]=='no') {
				$tmp_array[$ged_name]=='access';
			}
		}
		return $tmp_array[$ged_name];
	} else {
		return null;
	}
}

function set_user_gedcom_setting($user_id, $ged_id, $parameter, $value) {
	$ged_name=get_gedcom_from_id($ged_id);

	$tmp=get_user_setting($user_id, $parameter);
	if (is_string($tmp)) {
		$tmp_array=unserialize($tmp);
		if (!is_array($tmp_array)) {
			$tmp_array=array();
		}
	} else {
		$tmp_array=array();
	}
	if (empty($value)) {
		// delete the value
		unset($tmp_array[$ged_name]);
	} else {
		// update the value
		$tmp_array[$ged_name]=$value;
	}
	set_user_setting($user_id, $parameter, serialize($tmp_array));

	global $PGV_USERS_cache;
	if (isset($PGV_USERS_cache[$user_id])) {
		unset($PGV_USERS_cache[$user_id]);
	}
}

function get_user_from_gedcom_xref( $ged_id, $xref) {
	global $TBLPREFIX, $gBitDb;

	$ged_name=get_gedcom_from_id( $ged_id );

	$rows = null; // $gBitDb->query("SELECT u_username, u_gedcomid FROM {$TBLPREFIX}users");
	$username=false;
	while ( $row = $rows->fetchRow() ) {
		if ($row['u_gedcomid']) {
			$tmp_array=unserialize($row['u_gedcomid']);
			if (array_key_exists($ged_name, $tmp_array) && $tmp_array[$ged_name]==$xref) {
				return $row['u_username'];
			}
		}
	}
	return null;
}

?>

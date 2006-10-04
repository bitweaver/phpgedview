<?php
/**
 * PEAR:DB specific functions file
 *
 * This file implements the datastore functions necessary for PhpGedView to use an SQL database as its
 * datastore. This file also implements array caches for the database tables.  Whenever data is
 * retrieved from the database it is stored in a cache.  When a database access is requested the
 * cache arrays are checked first before querying the database.
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
 * @version $Id: functions_db.php,v 1.9 2006/10/04 12:07:54 lsces Exp $
 * @package PhpGedView
 * @subpackage DB
 */
if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	print "Now, why would you want to do that.	You're not hacking are you?";
	exit;
}

//-- PEAR:DB replaced by ADOdb from bitweaver

//-- set the REGEXP status of databases
global $gBitDbType;
$REGEXP_DB = (stristr($gBitDbType,'mysql') !== false || $gBitDbType=='pgsql');

//-- uncomment the following line to turn on sql query logging
$SQL_LOG = false;

/**
 * query the database
 *
 * this function will perform the given SQL query on the database
 * @param string $sql		the sql query to execture
 * @param boolean $show_error	whether or not to show any error messages
 * @param int $count	the number of records to return, 0 returns all
 * @return Object the connection result
 */
function &dbquery($sql, $show_error=true, $count=0) {
	global $DBCONN, $TOTAL_QUERIES, $INDEX_DIRECTORY, $SQL_LOG, $LAST_QUERY, $CONFIGURED;

	if (!$CONFIGURED) return false;
	if (!isset($DBCONN)) {
		//print "No Connection";
		return false;
	}
	//-- make sure a database connection has been established
//	if (DB::isError($DBCONN)) {
//		print $DBCONN->getCode()." ".$DBCONN->getMessage();
//		return $DBCONN;
//	}
//print $TOTAL_QUERIES."-".$sql."<br />\n";
//debug_print_backtrace()."<br /><br />";
	if ($count == 0)
		$res =& $DBCONN->query($sql);
	else
		$res =& $DBCONN->limitQuery($sql, 0, $count);

	$LAST_QUERY = $sql;
	$TOTAL_QUERIES++;
	if (!empty($SQL_LOG)) {
		$fp = fopen($INDEX_DIRECTORY."/sql_log.txt", "a");
		fwrite($fp, date("Y-m-d h:i:s")."\t".$_SERVER["SCRIPT_NAME"]."\t".$TOTAL_QUERIES."-".$sql."\r\n");
		fclose($fp);
	}
//	if (DB::isError($res)) {
//		if ($show_error) print "<span class=\"error\"><b>ERROR:".$res->getCode()." ".$res->getMessage()." <br />SQL:</b>".$res->getUserInfo()."</span><br /><br />\n";
//	}
	return $res;
}

/**
 * check if a gedcom has been imported into the database
 *
 * this function checks the database to see if the given gedcom has been imported yet.
 * @param string $ged the filename of the gedcom to check for import
 * @return bool return true if the gedcom has been imported otherwise returns false
 */
function check_for_import($ged) {
	global $BUILDING_INDEX, $DBCONN, $GEDCOMS, $gGedcom;

	$sql = "SELECT count(i_id) FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file=?";
	$res = $gGedcom->mDb->query($sql, array($GEDCOMS[$ged]["id"]));

	if ($res) {
		$row = $res->fetchRow();
		if ($row['count']>0) return true;
	}
	return false;
}

/**
 * find the gedcom record for a family
 *
 * This function first checks the <var>$famlist</var> cache to see if the family has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * also lookup the husb and wife so that they are in the cache
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#family
 * @param string $famid the unique gedcom xref id of the family record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_family_record($famid, $gedfile="") {
	global $GEDCOMS, $GEDCOM, $famlist, $DBCONN, $gGedcom;

	if (empty($famid)) return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	if (isset($famlist[$famid]["gedcom"])&&($famlist[$famid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $famlist[$famid]["gedcom"];

	$sql = "SELECT f_gedcom, f_file, f_husb, f_wife FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_id LIKE ? AND f_file=?";
	$res = $gGedcom->mDb->query($sql, array($famid,$GEDCOMS[$gedfile]["id"]));

	$row = $res->fetchRow();

	$famlist[$famid]["gedcom"] = $row['f_gedcom'];
	$famlist[$famid]["gedfile"] = $row['f_file'];
	$famlist[$famid]["husb"] = $row['f_husb'];
	$famlist[$famid]["wife"] = $row['f_wife'];
	find_person_record($row['f_husb']);
	find_person_record($row['f_wife']);
	return $row['f_gedcom'];
}

/**
 * find the gedcom record for an individual
 *
 * This function first checks the <var>$indilist</var> cache to see if the individual has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#indi
 * @param string $pid the unique gedcom xref id of the individual record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_person_record($pid, $gedfile="") {
	global $pgv_lang;
	global $GEDCOM, $GEDCOMS;
	global $BUILDING_INDEX, $indilist, $DBCONN, $gGedcom;

	if (empty($pid)) return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	if ($gedfile>=1) $gedfile = get_gedcom_from_id($gedfile);
	//-- first check the indilist cache
	// cache is unreliable for use with different gedcoms in user favorites (sjouke)
	if ((isset($indilist[$pid]["gedcom"]))&&($indilist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $indilist[$pid]["gedcom"];

	$sql = "SELECT i_gedcom, i_name, i_isdead, i_file, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_id LIKE ? AND i_file=?";
	$res = $gGedcom->mDb->query($sql, array( $pid ,$GEDCOMS[$gedfile]["id"] ));

	if ($res) {
		if ($res->numRows()==0) {
			return false;
		}
		$row =& $res->fetchRow();

		$indilist[$pid]["gedcom"] = $row['i_gedcom'];
		$indilist[$pid]["names"] = get_indi_names($row['i_gedcom']);
		$indilist[$pid]["isdead"] = $row['i_isdead'];
		$indilist[$pid]["gedfile"] = $row['i_file'];
		$res->free();
		return $row['i_gedcom'];
	}
}

/**
 * find the gedcom record
 *
 * This function first checks the caches to see if the record has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#other
 * @param string $pid the unique gedcom xref id of the record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_gedcom_record($pid, $gedfile = "") {
	global $pgv_lang;
	global $GEDCOMS, $MEDIA_ID_PREFIX;
	global $GEDCOM, $indilist, $famlist, $sourcelist, $objectlist, $otherlist, $DBCONN;

	if (empty($pid)) return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	if ((isset($indilist[$pid]["gedcom"]))&&($indilist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $indilist[$pid]["gedcom"];
	if ((isset($famlist[$pid]["gedcom"]))&&($famlist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $famlist[$pid]["gedcom"];
	if ((isset($objectlist[$pid]["gedcom"]))&&($objectlist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $objectlist[$pid]["gedcom"];
	if ((isset($sourcelist[$pid]["gedcom"]))&&($sourcelist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $sourcelist[$pid]["gedcom"];
	if ((isset($repolist[$pid]["gedcom"])) && ($repolist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $repolist[$pid]["gedcom"];
	if ((isset($otherlist[$pid]["gedcom"]))&&($otherlist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $otherlist[$pid]["gedcom"];

	//-- try to look ahead and guess the best type of record to look for
	//-- NOTE: not foolproof so leave the other section in place
	$type = id_type($pid);
	switch($type) {
		case 'INDI':
			$gedrec = find_person_record($pid, $gedfile);
			break;
		case 'FAM':
			$gedrec = find_family_record($pid, $gedfile);
			break;
		case 'OBJE':
			$gedrec = find_media_record($pid, $gedfile);
			break;
		case 'SOUR':
			$gedrec = find_source_record($pid, $gedfile);
			break;
		case 'REPO':
			$gedrec = find_repo_record($pid, $gedfile);
			break;
	}

	//-- unable to guess the type so look in all the tables
	if (empty($gedrec)) {
		$sql = "SELECT o_gedcom, o_file FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_id LIKE '".$DBCONN->escape($pid)."' AND o_file='".$DBCONN->escape($GEDCOMS[$gedfile]["id"])."'";
		$res =& dbquery($sql);
		if ($res->numRows()!=0) {
			$row =& $res->fetchRow();
			$res->free();
			$otherlist[$pid]["gedcom"] = $row['o_gedcom'];
			$otherlist[$pid]["gedfile"] = $row['o_file'];
			return $row['o_gedcom'];
		}
		$gedrec = find_person_record($pid, $gedfile);
		if (empty($gedrec)) $gedrec = find_family_record($pid, $gedfile);
		if (empty($gedrec)) $gedrec = find_source_record($pid, $gedfile);
		if (empty($gedrec)) $gedrec = find_media_record($pid, $gedfile);
	}
	return $gedrec;
}

/**
 * find the gedcom record for a source
 *
 * This function first checks the <var>$sourcelist</var> cache to see if the source has already
 * been retrieved from the database.  If it hasn't been retrieved, then query the database and
 * add it to the cache.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#source
 * @param string $sid the unique gedcom xref id of the source record to retrieve
 * @return string the raw gedcom record is returned
 */
function find_source_record($sid, $gedfile="") {
	global $fcontents;
	global $pgv_lang;
	global $GEDCOMS;
	global $GEDCOM, $sourcelist, $DBCONN;

	if ($sid=="") return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	if (isset($sourcelist[$sid]["gedcom"]) && ($sourcelist[$sid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $sourcelist[$sid]["gedcom"];

	$sql = "SELECT s_gedcom, s_name, s_file FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_id LIKE '".$DBCONN->escape($sid)."' AND s_file='".$DBCONN->escape($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);

	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$sourcelist[$sid]["name"] = stripslashes($row['s_name']);
		$sourcelist[$sid]["gedcom"] = $row['s_gedcom'];
		$sourcelist[$sid]["gedfile"] = $row['s_file'];
		$res->free();
		return $row['s_gedcom'];
	}
	else {
		return false;

	}
}


/**
 * Find a repository record by its ID
 * @param string $rid	the record id
 * @param string $gedfile	the gedcom file id
 */
function find_repo_record($rid, $gedfile="") {
	global $GEDCOMS;
	global $GEDCOM, $repolist, $DBCONN;

	if ($rid=="") return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	if (isset($repolist[$rid]["gedcom"]) && ($repolist[$rid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $repolist[$rid]["gedcom"];

	$sql = "SELECT o_id, o_gedcom, o_file FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_type='REPO' AND o_id LIKE '".$DBCONN->escape($rid)."' AND o_file='".$DBCONN->escape($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);

	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$tt = preg_match("/1 NAME (.*)/", $row['o_gedcom'], $match);
		if ($tt == "0") $name = $row['o_id']; else $name = $match[1];
		$repolist[$rid]["name"] = stripslashes($name);
		$repolist[$rid]["gedcom"] = $row['o_gedcom'];
		$repolist[$rid]["gedfile"] = $row['o_file'];
		$res->free();
		return $row['o_gedcom'];
	}
	else {
		return false;
	}
}

/**
 * Find a media record by its ID
 * @param string $rid	the record id
 */
function find_media_record($rid, $gedfile='') {
	global $GEDCOMS;
	global $GEDCOM, $objectlist, $DBCONN, $MULTI_MEDIA;

	//-- don't look for a media record if not using media
	if (!$MULTI_MEDIA) return false;
	if ($rid=="") return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	//-- first check for the record in the cache
	if (empty($objectlist)) $objectlist = array();
	if (isset($objectlist[$rid]["gedcom"]) && ($objectlist[$rid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $objectlist[$rid]["gedcom"];

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_media LIKE '".$DBCONN->escape($rid)."' AND m_gedfile='".$DBCONN->escape($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);
	if (!$res) return false;
	if ($res->numRows()!=0) {
		$row = $res->fetchRow(DB_FETCHMODE_ASSOC);
		$objectlist[$rid]["ext"] = $row["m_ext"];
		$row["m_titl"] = trim($row["m_titl"]);
		if (empty($row["m_titl"])) $row["m_titl"] = $row["m_file"];
		$objectlist[$rid]["title"] = $row["m_titl"];
		$objectlist[$rid]["file"] = $row["m_file"];
		$objectlist[$rid]["gedcom"] = $row["m_gedrec"];
		$objectlist[$rid]["gedfile"] = $row["m_gedfile"];
		$res->free();
		return $row["m_gedrec"];
	}
	else {
		return false;
	}
}

/**
 * find and return the id of the first person in the gedcom
 * @return string the gedcom xref id of the first person in the gedcom
 */
function find_first_person() {
	global $GEDCOM, $GEDCOMS, $gBitDbType, $DBCONN;
	$sql = "SELECT i_id FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY i_id";
	if ($gBitDbType!="sqlite") $sql.=" LIMIT 1";
	$row = $DBCONN->getRow($sql);
	if (!$row) return $row['i_id'];
	else return "I1";
}

/**
 * update the is_dead status in the database
 *
 * this function will update the is_dead field in the individuals table with the correct value
 * calculated by the is_dead() function.  To improve import performance, the is_dead status is first
 * set to -1 during import.  The first time the is_dead status is retrieved this function is called to update
 * the database.  This makes the first request for a person slower, but will speed up all future requests.
 * @param string $gid	gedcom xref id of individual to update
 * @param array $indi	the $indi array struction for the individal as used in the <var>$indilist</var>
 * @return int	1 if the person is dead, 0 if living
 */
function update_isdead($gid, $indi) {
	global $USE_RIN, $indilist, $DBCONN, $gGedcom;
	$isdead = 0;
	$isdead = is_dead($indi["gedcom"]);
	if (empty($isdead)) $isdead = 0;
	$sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."individuals SET i_isdead=$isdead WHERE i_id LIKE ? AND i_file=?";
	$res = $gGedcom->mDb->query($sql, array($gid,$indi["gedfile"]));

	if (isset($indilist[$gid])) $indilist[$gid]["isdead"] = $isdead;
	return $isdead;
}

/**
 * reset the i_isdead column
 *
 * This function will reset the i_isdead column with the default -1 so that all is dead status
 * items will be recalculated.
 */
function reset_isdead() {
	global $GEDCOMS, $GEDCOM, $DBCONN;

	$sql = "UPDATE ".PHPGEDVIEW_DB_PREFIX."individuals SET i_isdead=-1 WHERE i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	dbquery($sql);
}

/**
 * get a list of all the source titles
 *
 * returns an array of all of the sourcetitles in the database.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#sources
 * @return array the array of source-titles
 */
function get_source_add_title_list() {
	global $sourcelist, $GEDCOM, $GEDCOMS;
	global $DBCONN;

	$sourcelist = array();

 	$sql = "SELECT s_id, s_file, s_file as s_name, s_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND ((s_gedcom LIKE '% _HEB %') OR (s_gedcom LIKE '% ROMN %'));";

	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$ct = preg_match("/\d ROMN (.*)/", $row["s_gedcom"], $match);
 		if ($ct==0) $ct = preg_match("/\d _HEB (.*)/", $row["s_gedcom"], $match);
		$source["name"] = $match[1];
		$source["gedcom"] = $row["s_gedcom"];
		$source["gedfile"] = $row["s_file"];
		$sourcelist[$row["s_id"]] = $source;
	}
	$res->free();

	return $sourcelist;
}

/**
 * get a list of all the sources
 *
 * returns an array of all of the sources in the database.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#sources
 * @return array the array of sources
 */
function get_source_list() {
	global $sourcelist, $GEDCOM, $GEDCOMS;
	global $DBCONN;

	$sourcelist = array();

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY s_name";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["name"] = $row["s_name"];
		$source["gedcom"] = $row["s_gedcom"];
		$source["gedfile"] = $row["s_file"];
//		$source["nr"] = 0;
		$sourcelist[$row["s_id"]] = $source;
	}
	$res->free();

	return $sourcelist;
}

//-- get the repositorylist from the datastore
function get_repo_list() {
	global $repolist, $GEDCOM, $GEDCOMS;
	global $DBCONN;

	$repolist = array();

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND o_type='REPO'";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$tt = preg_match("/1 NAME (.*)/", $row["o_gedcom"], $match);
		if ($tt == "0") $name = $row["o_id"]; else $name = $match[1];
		$repo["name"] = $name;
		$repo["id"] = $row["o_id"];
		$repo["gedfile"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$repo["gedcom"] = $row["o_gedcom"];
		$repolist[$row["o_id"]]= $repo;
	}
	$res->free();
	asort($repolist); // sort by repo name
	return $repolist;
}

//-- get the repositorylist from the datastore
function get_repo_id_list() {
	global $GEDCOM, $GEDCOMS;
	global $DBCONN;

	$repo_id_list = array();

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND o_type='REPO' ORDER BY o_id";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$tt = preg_match("/1 NAME (.*)/", $row["o_gedcom"], $match);
		if ($tt>0) $repo["name"] = $match[1];
		else $repo["name"] = "";
		$repo["gedfile"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$repo["gedcom"] = $row["o_gedcom"];
		$repo_id_list[$row["o_id"]] = $repo;
	}
	$res->free();
	return $repo_id_list;
}

/**
 * get a list of all the repository titles
 *
 * returns an array of all of the repositorytitles in the database.
 * @link http://phpgedview.sourceforge.net/devdocs/arrays.php#repositories
 * @return array the array of repository-titles
 */
function get_repo_add_title_list() {
	global $GEDCOM, $GEDCOMS;
	global $DBCONN;

	$repolist = array();

 	$sql = "SELECT o_id, o_file, o_file as o_name, o_type, o_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_type='REPO' AND o_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND ((o_gedcom LIKE '% _HEB %') OR (o_gedcom LIKE '% ROMN %'));";

	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$repo["gedcom"] = $row["o_gedcom"];
		$ct = preg_match("/\d ROMN (.*)/", $row["o_gedcom"], $match);
 		if ($ct==0) $ct = preg_match("/\d _HEB (.*)/", $row["o_gedcom"], $match);
		$repo["name"] = $match[1];
		$repo["id"] = $row["o_id"];
		$repo["gedfile"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$repolist[$row["o_id"]] = $repo;

	}
	$res->free();
	return $repolist;
}

//-- get the indilist from the datastore
function get_indi_list() {
	global $indilist, $GEDCOM, $DBCONN, $GEDCOMS;
	global $INDILIST_RETRIEVED;

	if ($INDILIST_RETRIEVED) return $indilist;
	$indilist = array();
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY i_surname";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$indi = array();
		$indi["gedcom"] = $row["i_gedcom"];
		$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "A"));
		$indi["isdead"] = $row["i_isdead"];
		$indi["gedfile"] = $row["i_file"];
		$indilist[$row["i_id"]] = $indi;
	}
	$res->free();

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."names WHERE n_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY n_surname";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		if (isset($indilist[$row["n_gid"]]) && ($indilist[$row["n_gid"]]["gedfile"]==$GEDCOMS[$GEDCOM]["id"])) {
			$indilist[$row["n_gid"]]["names"][] = array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]);
		}
	}
	$res->free();
	$INDILIST_RETRIEVED = true;
	return $indilist;
}


//-- get the assolist from the datastore
function get_asso_list($type = "all") {
	global $assolist, $GEDCOM, $DBCONN, $GEDCOMS, $gGedcom;
	global $ASSOLIST_RETRIEVED;

	if ($ASSOLIST_RETRIEVED) return $assolist;
	$assolist = array();

	$oldged = $GEDCOM;
	if (($type == "all") || ($type == "fam")) {
		$sql = "SELECT f_id, f_file, f_gedcom, f_husb, f_wife FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_gedcom LIKE '% ASSO %'";
		$res = $gGedcom->mDb->query($sql);

		$ct = $res->numRows();
		while( $row =& $res->fetchRow() ){
			$asso = array();
			$asso["type"] = "fam";
			$pid2 = $row["f_id"]."[".$row["f_file"]."]";
			$asso["gedcom"] = $row["f_gedcom"];
			$asso["gedfile"] = $row["f_file"];
			// Get the family names
			$GEDCOM = get_gedcom_from_id($row["f_file"]);
			$hname = get_sortable_name($row["f_husb"], "", "", true);
			$wname = get_sortable_name($row["f_wife"], "", "", true);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = array();
			foreach ($hname as $hkey => $hn) {
				foreach ($wname as $wkey => $wn) {
					$name[] = $hn." + ".$wn;
					$name[] = $wn." + ".$hn;
				}
			}
			$asso["name"] = $name;
			$ca = preg_match_all("/\d ASSO @(.*)@/", $row["f_gedcom"], $match, PREG_SET_ORDER);
			for ($i=0; $i<$ca; $i++) {
				$pid = $match[$i][1]."[".$row["f_file"]."]";
				$assolist[$pid][$pid2] = $asso;
			}
		}
	}

	if (($type == "all") || ($type == "indi")) {
		$sql = "SELECT i_id, i_file, i_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_gedcom LIKE '% ASSO %'";
		$res = $gGedcom->mDb->query($sql);

		$ct = $res->numRows();
		while( $row =& $res->fetchRow() ){
			$asso = array();
			$asso["type"] = "indi";
			$pid2 = $row["i_id"]."[".$row["i_file"]."]";
			$asso["gedcom"] = $row["i_gedcom"];
			$asso["gedfile"] = $row["i_file"];
			$asso["name"] = get_indi_names($row["i_gedcom"]);
			$ca = preg_match_all("/\d ASSO @(.*)@/", $row["i_gedcom"], $match, PREG_SET_ORDER);
			for ($i=0; $i<$ca; $i++) {
				$pid = $match[$i][1]."[".$row["i_file"]."]";
				$assolist[$pid][$pid2] = $asso;
			}
		}
	}

	$GEDCOM = $oldged;

	$ASSOLIST_RETRIEVED = true;
	return $assolist;
}

//-- get the famlist from the datastore
function get_fam_list() {
	global $famlist, $GEDCOM, $indilist, $DBCONN, $GEDCOMS;
	global $FAMLIST_RETRIEVED;

	if ($FAMLIST_RETRIEVED) return $famlist;
	$famlist = array();
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$fam = array();
		$fam["gedcom"] = $row["f_gedcom"];
		$hname = get_sortable_name($row["f_husb"]);
		$wname = get_sortable_name($row["f_wife"]);
		$name = "";
		if (!empty($hname)) $name = $hname;
		else $name = "@N.N., @P.N.";

		if (!empty($wname)) $name .= " + ".$wname;
		else $name .= " + @N.N., @P.N.";

		$fam["name"] = $name;
		$fam["HUSB"] = $row["f_husb"];
		$fam["WIFE"] = $row["f_wife"];
		$fam["CHIL"] = $row["f_chil"];
		$fam["gedfile"] = $row["f_file"];
		$famlist[$row["f_id"]] = $fam;
	}
	$res->free();
	$FAMLIST_RETRIEVED = true;
	return $famlist;
}

//-- get the otherlist from the datastore
function get_other_list() {
	global $otherlist, $GEDCOM, $DBCONN, $GEDCOMS;

	$otherlist = array();

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["o_gedcom"];
		$source["type"] = $row["o_type"];
		$source["gedfile"] = $row["o_file"];
		$otherlist[$row["o_id"]]= $source;
	}
	$res->free();
	return $otherlist;
}
/*
//-- get the otherlist from the datastore
function get_media_list() {
	global $medialist, $GEDCOM, $DBCONN, $GEDCOMS;
	global PHPGEDVIEW_DB_PREFIX;

	$medialist = array();

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_gedfile='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["m_gedrec"];
		$source["ext"] = $row["m_ext"];
		$source["titl"] = $row["m_titl"];
		$source["file"] = $row["m_file"];
		$source["gedfile"] = $row["m_gedfile"];
		$medialist[$row["m_media"]]= $source;
	}
	$res->free();
	return $medialist;
}
*/
//-- search through the gedcom records for individuals
/**
 * Search the database for individuals that match the query
 *
 * uses a regular expression to search the gedcom records of all individuals and returns an
 * array list of the matching individuals
 *
 * @author	yalnifj
 * @param	string $query a regular expression query to search for
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myindilist array with all individuals that matched the query
 */
function search_indis($query, $allgeds=false, $ANDOR="AND") {
	global $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;
	$myindilist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term = "LIKE";
	//-- if the query is a string
	if (!is_array($query)) {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE (";
		//-- make sure that MySQL matches the upper and lower case utf8 characters
		if (has_utf8($query)) $sql .= "i_gedcom $term '".$DBCONN->escape(str2upper($query))."' OR i_gedcom $term '".$DBCONN->escape(str2lower($query))."')";
		else $sql .= "i_gedcom $term '".$DBCONN->escape($query)."')";
	}
	//-- create a more complicated query if it is an array
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			if (has_utf8($q)) $sql .= "(i_gedcom $term '".$DBCONN->escape(str2upper($q))."' OR i_gedcom $term '".$DBCONN->escape(str2lower($q))."')";
			else $sql .= "(i_gedcom $term '".$DBCONN->escape($q)."')";
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "i_file='".$DBCONN->escape($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
//	print $sql;
	$res = dbquery($sql);

	if ($res) {
		while($row =& $res->fetchRow()){
			if (count($allgeds) > 1) {
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["names"] = get_indi_names($row['i_gedcom']);
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["gedfile"] = $row['i_file'];
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["gedcom"] = $row['i_gedcom'];
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["isdead"] = $row['i_isdead'];
				if ($myindilist[$row['i_id']."[".$row['i_file']."]"]["gedfile"] == $GEDCOM) $indilist[$row['i_id']] = $myindilist[$row['i_id']."[".$row['i_file']."]"];
			}
			else {
				$myindilist[$row['i_id']]["names"] = get_indi_names($row['i_gedcom']);
				$myindilist[$row['i_id']]["gedfile"] = $row['i_file'];
				$myindilist[$row['i_id']]["gedcom"] = $row['i_gedcom'];
				$myindilist[$row['i_id']]["isdead"] = $row['i_isdead'];
				if ($myindilist[$row['i_id']]["gedfile"] == $GEDCOM) $indilist[$row['i_id']] = $myindilist[$row['i_id']];
			}
		}
		$res->free();
	}
	return $myindilist;
}

//-- search through the gedcom records for individuals in families
function search_indis_fam($add2myindilist) {
	global $GEDCOM, $indilist, $myindilist;

	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		if (isset($add2myindilist[$row['i_id']])){
			$add2my_fam=$add2myindilist[$row['i_id']];
			$myindilist[$row['i_id']]["names"] = get_indi_names($row['i_gedcom']);
			$myindilist[$row['i_id']]["gedfile"] = $row['i_file'];
			$myindilist[$row['i_id']]["gedcom"] = $row['i_gedcom'].$add2my_fam;
			$myindilist[$row['i_id']]["isdead"] = $row['i_isdead'];
			$indilist[$row['i_id']] = $myindilist[$row['i_id']];
		}
	}
	$res->free();
	return $myindilist;
}

/**
 * Search for individuals who had dates within the given year ranges
 * @param int $startyear	the starting year
 * @param int $endyear		The ending year
 * @return array
 */
function search_indis_year_range($startyear, $endyear, $allgeds=false) {
	global $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;

	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	$myindilist = array();
	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals, ".PHPGEDVIEW_DB_PREFIX."dates WHERE i_id=d_gid AND i_file=d_file AND d_fact NOT IN('CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL') AND ";
	$sql .= "d_datestamp >= ".$startyear."0000 AND d_datestamp<".($endyear+1)."0000";
	/*
	$i=$startyear;
	while($i <= $endyear) {
		if ($i > $startyear) $sql .= " OR ";
		if ($REGEXP_DB) $sql .= "i_gedcom $term '".$DBCONN->escape("2 DATE[^\n]* ".$i)."'";
		else $sql .= "i_gedcom LIKE '".$DBCONN->escape("%2 DATE%".$i)."%'";
		$i++;
	}
	$sql .= ")";
	*/
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$myindilist[$row['i_id']]["names"] = get_indi_names($row['i_gedcom']);
		$myindilist[$row['i_id']]["gedfile"] = $row['i_file'];
		$myindilist[$row['i_id']]["gedcom"] = $row['i_gedcom'];
		$myindilist[$row['i_id']]["isdead"] = $row['i_isdead'];
		$indilist[$row['i_id']] = $myindilist[$row['i_id']];
	}
	$res->free();
	return $myindilist;
}


//-- search through the gedcom records for individuals
function search_indis_names($query, $allgeds=false) {
	global $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;

	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	//-- split up words and find them anywhere in the record... important for searching names
	//-- like "givenname surname"
	if (!is_array($query)) {
		$query = preg_split("/[\s,]+/", $query);
		if (!$REGEXP_DB) {
			for($i=0; $i<count($query); $i++){
				$query[$i] = "%".$query[$i]."%";
			}
		}
	}

	$myindilist = array();
	if (empty($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals";
	else if (!is_array($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_name $term '".$DBCONN->escape($query)."'";
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if (!empty($q)) {
				if ($i>0) $sql .= " AND ";
				$sql .= "i_name $term '".$DBCONN->escape($q)."'";
				$i++;
			}
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);
	while($row = $res->fetchRow()){
		if ($allgeds) $key = $row['i_id']."[".$row['i_file']."]";
		else $key = $row['i_id'];
		if (isset($indilist[$key])) $myindilist[$key] = $indilist[$key];
		else {
			$myindilist[$key]["names"] = get_indi_names($row['i_gedcom']);
			$myindilist[$key]["gedfile"] = $row['i_file'];
			$myindilist[$key]["gedcom"] = $row['i_gedcom'];
			$myindilist[$key]["isdead"] = $row['i_isdead'];
			$indilist[$key] = $myindilist[$key];
		}
	}
	$res->free();

	//-- search the names table too
	if (!is_array($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals, ".PHPGEDVIEW_DB_PREFIX."names WHERE i_id=n_gid AND i_file=n_file AND n_name $term '".$DBCONN->escape($query)."'";
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals, ".PHPGEDVIEW_DB_PREFIX."names WHERE i_id=n_gid AND i_file=n_file AND (";
		$i=0;
		foreach($query as $indexval => $q) {
			if (!empty($q)) {
				if ($i>0) $sql .= " AND ";
				$sql .= "n_name $term '".$DBCONN->escape($q)."'";
				$i++;
			}
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row = $res->fetchRow()){
		if ($allgeds) $key = $row['i_id']."[".$row['i_file']."]";
		else $key = $row['i_id'];
		if (!isset($myindilist[$key])) {
			if (isset($indilist[$key])) $myindilist[$key] = $indilist[$key];
			else {
				$myindilist[$key]["names"] = get_indi_names($row['i_gedcom']);
				$myindilist[$key]["gedfile"] = $row['i_file'];
				$myindilist[$key]["gedcom"] = $row['i_gedcom'];
				$myindilist[$key]["isdead"] = $row['i_isdead'];
				$indilist[$key] = $myindilist[$key];
			}
		}
	}
	$res->free();
	return $myindilist;
}

/**
 * get recent changes since the given date inclusive
 * @author	yalnifj
 * @param	int $day the day of the month to search for, leave empty to include all
 * @param	int $mon the integer value for the month to search for, leave empty to include all
 * @param	int $year the year to search for, leave empty to include all
 */
function get_recent_changes($day="", $mon="", $year="", $allgeds=false) {
	global $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $GEDCOMS;

	$changes = array();
	while(strlen($year)<4) $year ='0'.$year;
	while(strlen($mon)<2) $mon ='0'.$mon;
	while(strlen($day)<2) $day ='0'.$day;
	$datestamp = $year.$mon.$day;
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."dates WHERE d_fact='CHAN' AND d_datestamp>=".$datestamp;
	if (!$allgeds) $sql .= " AND d_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= " ORDER BY d_datestamp DESC";
	//print $sql;
	$res = dbquery($sql);

	if ($res) {
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			if (preg_match("/\w+:\w+/", $row['d_gid'])==0) {
				$changes[] = $row;
			}
		}
	}
	return $changes;
}

/**
 * Search the dates table for individuals that had events on the given day
 *
 * @author	yalnifj
 * @param	int $day the day of the month to search for, leave empty to include all
 * @param	string $month the 3 letter abbr. of the month to search for, leave empty to include all
 * @param	int $year the year to search for, leave empty to include all
 * @param	string $fact the facts to include (use a comma seperated list to include multiple facts)
 * 				prepend the fact with a ! to not include that fact
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myindilist array with all individuals that matched the query
 */
function search_indis_dates($day="", $month="", $year="", $fact="", $allgeds=false, $ANDOR="AND") {
	global $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS, $gGedcom;
	$myindilist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname, d_gid, d_fact FROM ".PHPGEDVIEW_DB_PREFIX."dates, ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_id=d_gid AND i_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$day."' ";
	if (!empty($month)) $sql .= "AND d_month='".str2upper($month)."' ";
	if (!empty($year)) $sql .= "AND d_year='".$year."' ";
	if (!empty($fact)) {
		$sql .= "AND (";
		$facts = preg_split("/[,:; ]/", $fact);
		$i=0;
		foreach($facts as $fact) {
			if ($i!=0) $sql .= " OR ";
			$ct = preg_match("/!(\w+)/", $fact, $match);
			if ($ct > 0) {
				$fact = $match[1];
				$sql .= "d_fact!='".str2upper($fact)."'";
			}
			else {
				$sql .= "d_fact='".str2upper($fact)."'";
			}
			$i++;
		}
		$sql .= ") ";
	}
	if (!$allgeds) $sql .= "AND d_file='".$GEDCOMS[$GEDCOM]["id"]."' ";
	$sql .= "ORDER BY d_year DESC, d_mon DESC, d_day DESC";
//	print $sql;
	$res = $gGedcom->mDb->query($sql);

	if ($res) {
		while( $row =& $res->fetchRow() ){
			if ($allgeds) {
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["names"] = get_indi_names($row['i_gedcom']);
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["gedfile"] = $row['i_file'];
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["gedcom"] = $row['i_gedcom'];
				$myindilist[$row['i_id']."[".$row['i_file']."]"]["isdead"] = $row['i_isdead'];
				if ($myindilist[$row['i_id']."[".$row['i_file']."]"]["gedfile"] == $GEDCOMS[$GEDCOM]['id']) $indilist[$row['i_id']] = $myindilist[$row['i_id']."[".$row['i_id']."]"];
			}
			else {
				$myindilist[$row['i_id']]["names"] = get_indi_names($row['i_gedcom']);
				$myindilist[$row['i_id']]["gedfile"] = $row['i_file'];
				$myindilist[$row['i_id']]["gedcom"] = $row['i_gedcom'];
				$myindilist[$row['i_id']]["isdead"] = $row['i_isdead'];
				if ($myindilist[$row['i_id']]["gedfile"] == $GEDCOMS[$GEDCOM]['id']) $indilist[$row['i_id']] = $myindilist[$row['i_id']];
			}
		}
	}
	return $myindilist;
}

//-- search through the gedcom records for families
function search_fams($query, $allgeds=false, $ANDOR="AND", $allnames=false) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';
	$myfamlist = array();
	if (!is_array($query)) $sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (f_gedcom $term '".$DBCONN->escape($query)."')";
	else {
		$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(f_gedcom $term '".$DBCONN->escape($q)."')";
			$i++;
		}
		$sql .= ")";
	}

	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0, $max=count($allgeds); $i<$max; $i++) {
			$sql .= "f_file='".$DBCONN->escape($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < $max-1) $sql .= " OR ";
		}
		$sql .= ")";
	}

	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$GEDCOM = get_gedcom_from_id($row['f_file']);
		if ($allnames == true) {
			$hname = get_sortable_name($row['f_husb'], "", "", true);
			$wname = get_sortable_name($row['f_wife'], "", "", true);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = array();
			foreach ($hname as $hkey => $hn) {
				foreach ($wname as $wkey => $wn) {
					$name[] = $hn." + ".$wn;
					$name[] = $wn." + ".$hn;
				}
			}
		}
		else {
			$hname = get_sortable_name($row['f_husb']);
			$wname = get_sortable_name($row['f_wife']);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = $hname." + ".$wname;
		}
		if (count($allgeds) > 1) {
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["name"] = $name;
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["gedfile"] = $row['f_file'];
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["gedcom"] = $row['f_gedcom'];
			$famlist[$row['f_id']] = $myfamlist[$row['f_id']."[".$row['f_file']."]"];
		}
		else {
			$myfamlist[$row['f_id']]["name"] = $name;
			$myfamlist[$row['f_id']]["gedfile"] = $row['f_file'];
			$myfamlist[$row['f_id']]["gedcom"] = $row['f_gedcom'];
			$famlist[$row['f_id']] = $myfamlist[$row['f_id']];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $myfamlist;
}

//-- search through the gedcom records for families
function search_fams_names($query, $ANDOR="AND", $allnames=false, $gedcnt=1) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $GEDCOMS;
	//if ($REGEXP_DB) $term = "REGEXP";
	//else $term = "LIKE";
	$myfamlist = array();
	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (";
	$i=0;
	foreach($query as $indexval => $q) {
		if ($i>0) $sql .= " $ANDOR ";
		$sql .= "((f_husb='".$DBCONN->escape($q[0])."' OR f_wife='".$DBCONN->escape($q[0])."') AND f_file='".$DBCONN->escape($q[1])."')";
		$i++;
	}
	$sql .= ")";

	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$GEDCOM = get_gedcom_from_id($row['f_file']);
		if ($allnames == true) {
			$hname = get_sortable_name($row['f_husb'], "", "", true);
			$wname = get_sortable_name($row['f_wife'], "", "", true);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = array();
			foreach ($hname as $hkey => $hn) {
				foreach ($wname as $wkey => $wn) {
					$name[] = $hn." + ".$wn;
					$name[] = $wn." + ".$hn;
				}
			}
		}
		else {
			$hname = get_sortable_name($row['f_husb']);
			$wname = get_sortable_name($row['f_wife']);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = $hname." + ".$wname;
		}
		if ($gedcnt > 1) {
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["name"] = $name;
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["gedfile"] = $row['f_file'];
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["gedcom"] = $row['f_gedcom'];
			$famlist[$row['f_id']] = $myfamlist[$row['f_id']."[".$row['f_file']."]"];
		}
		else {
			$myfamlist[$row['f_id']]["name"] = $name;
			$myfamlist[$row['f_id']]["gedfile"] = $row['f_file'];
			$myfamlist[$row['f_id']]["gedcom"] = $row['f_gedcom'];
			$famlist[$row['f_id']] = $myfamlist[$row['f_id']];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $myfamlist;
}

/**
 * Search the families table for individuals are part of that family
 * either as a husband, wife or child.
 *
 * @author	roland-d
 * @param	string $query the query to search for as a single string
 * @param	array $query the query to search for as an array
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @param	string $ANDOR setting if the sql query should be constructed with AND or OR
 * @param	boolean $allnames true returns all names in an array
 * @return	array $myfamlist array with all families that matched the query
 */
function search_fams_members($query, $allgeds=false, $ANDOR="AND", $allnames=false) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $GEDCOMS;
	$myfamlist = array();
	if (!is_array($query)) $sql = "SELECT f_id, f_husb, f_wife, f_file FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (f_husb='$query' OR f_wife='$query' OR f_chil LIKE '%$query;%')";
	else {
		$sql = "SELECT f_id, f_husb, f_wife, f_file FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(f_husb='$query' OR f_wife='$query' OR f_chil LIKE '%$query;%')";
			$i++;
		}
		$sql .= ")";
	}

	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0, $max=count($allgeds); $i<$max; $i++) {
			$sql .= "f_file='".$DBCONN->escape($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < $max-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	$res = dbquery($sql);

	$i=0;
	while($row =& $res->fetchRow()){
		if ($allnames == true) {
			$hname = get_sortable_name($row['f_husb'], "", "", true);
			$wname = get_sortable_name($row['f_wife'], "", "", true);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = array();
			foreach ($hname as $hkey => $hn) {
				foreach ($wname as $wkey => $wn) {
					$name[] = $hn." + ".$wn;
					$name[] = $wn." + ".$hn;
				}
			}
		}
		else {
			$hname = get_sortable_name($row['f_husb']);
			$wname = get_sortable_name($row['f_wife']);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = $hname." + ".$wname;
		}
		if (count($allgeds) > 1) {
			$myfamlist[$i]["name"] = $name;
			$myfamlist[$i]["gedfile"] = $row['f_id'];
			$myfamlist[$i]["gedcom"] = $row['f_husb'];
			$famlist[] = $myfamlist;
		}
		else {
			$myfamlist[$i][] = $name;
			$myfamlist[$i][] = $row['f_id'];
			$myfamlist[$i][] = $row['f_file'];
			$i++;
			$famlist[] = $myfamlist;
		}
	}
	$res->free();
	return $myfamlist;
}

//-- search through the gedcom records for families with daterange
function search_fams_year_range($startyear, $endyear, $allgeds=false) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;

	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	$myfamlist = array();
	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."families, ".PHPGEDVIEW_DB_PREFIX."dates WHERE f_id=d_gid AND f_file=d_file AND d_fact NOT IN('CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL') AND ";
	$sql .= "d_datestamp >= ".$startyear."0000 AND d_datestamp<".($endyear+1)."0000";
	/*
	$i=$startyear;
	while($i <= $endyear) {
		if ($i > $startyear) $sql .= " OR ";
		if ($REGEXP_DB) $sql .= "f_gedcom $term '".$DBCONN->escape("2 DATE[^\n]* ".$i)."'";
		else $sql .= "f_gedcom LIKE '".$DBCONN->escape("%2 DATE%".$i)."%'";
		$i++;
	}
	$sql .= ")";
	*/
	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$hname = get_sortable_name($row['f_husb']);
		$wname = get_sortable_name($row['f_wife']);
		if (empty($hname)) $hname = "@N.N.";
		if (empty($wname)) $wname = "@N.N.";
		$name = $hname." + ".$wname;
		$myfamlist[$row['f_id']]["name"] = $name;
		$myfamlist[$row['f_id']]["gedfile"] = $row['f_file'];
		$myfamlist[$row['f_id']]["gedcom"] = $row['f_gedcom'];
		$famlist[$row['f_id']] = $myfamlist[$row['f_id']];
	}
	$res->free();
	return $myfamlist;
}

/**
 * Search the dates table for families that had events on the given day
 *
 * @author	yalnifj
 * @param	int $day the day of the month to search for, leave empty to include all
 * @param	string $month the 3 letter abbr. of the month to search for, leave empty to include all
 * @param	int $year the year to search for, leave empty to include all
 * @param	string $fact the facts to include (use a comma seperated list to include multiple facts)
 * 				prepend the fact with a ! to not include that fact
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myfamlist array with all individuals that matched the query
 */
function search_fams_dates($day="", $month="", $year="", $fact="", $allgeds=false) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOM, $GEDCOMS, $gGedcom;
	$myfamlist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom, d_gid, d_fact FROM ".PHPGEDVIEW_DB_PREFIX."dates, ".PHPGEDVIEW_DB_PREFIX."families WHERE f_id=d_gid AND f_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$day."' ";
	if (!empty($month)) $sql .= "AND d_month='".str2upper($month)."' ";
	if (!empty($year)) $sql .= "AND d_year='".$year."' ";
	if (!empty($fact)) {
		$sql .= "AND (";
		$facts = preg_split("/[,:; ]/", $fact);
		$i=0;
		foreach($facts as $fact) {
			if ($i!=0) $sql .= " OR ";
			$ct = preg_match("/!(\w+)/", $fact, $match);
			if ($ct > 0) {
				$fact = $match[1];
				$sql .= "d_fact!='".str2upper($fact)."'";
			}
			else {
				$sql .= "d_fact='".str2upper($fact)."'";
			}
			$i++;
		}
		$sql .= ") ";
	}
	if (!$allgeds) $sql .= "AND d_file=? ";
	$sql .= "ORDER BY d_year, d_month, d_day DESC";

	$res = $gGedcom->mDb->query($sql, array($GEDCOMS[$GEDCOM]["id"]));

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$GEDCOM = get_gedcom_from_id($row['f_file']);
		$hname = get_sortable_name($row['f_husb']);
		$wname = get_sortable_name($row['f_wife']);
		if (empty($hname)) $hname = "@N.N.";
		if (empty($wname)) $wname = "@N.N.";
		$name = $hname." + ".$wname;
		if ($allgeds) {
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["name"] = $name;
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["gedfile"] = $row['f_gedcom'];
			$myfamlist[$row['f_id']."[".$row['f_file']."]"]["gedcom"] = $row['f_file'];
			$famlist[$row['f_id']] = $myfamlist[$row['f_id']."[".$row['f_gedcom']."]"];
		}
		else {
			$myfamlist[$row['f_id']]["name"] = $name;
			$myfamlist[$row['f_id']]["gedfile"] = $row['f_file'];
			$myfamlist[$row['f_id']]["gedcom"] = $row['f_gedcom'];
			$famlist[$row['f_id']] = $myfamlist[$row['f_id']];
		}
	}
	$GEDCOM = $gedold;
	return $myfamlist;
}

//-- search through the gedcom records for sources
function search_sources($query, $allgeds=false, $ANDOR="AND") {
	global $GEDCOM, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;
	$mysourcelist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';
	if (!is_array($query)) {
		$sql = "SELECT s_id, s_name, s_file, s_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE ";
		//-- make sure that MySQL matches the upper and lower case utf8 characters
		if (has_utf8($query)) $sql .= "(s_gedcom $term '".$DBCONN->escape(str2upper($query))."' OR s_gedcom $term '".$DBCONN->escape(str2lower($query))."')";
		else $sql .= "s_gedcom $term '".$DBCONN->escape($query)."'";
	}
	else {
		$sql = "SELECT s_id, s_name, s_file, s_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			if (has_utf8($q)) $sql .= "(s_gedcom $term '".$DBCONN->escape(str2upper($q))."' OR s_gedcom $term '".$DBCONN->escape(str2lower($q))."')";
			else $sql .= "(s_gedcom $term '".$DBCONN->escape($q)."')";
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND s_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "s_file='".$DBCONN->escape($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}

	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		if (count($allgeds) > 1) {
			$mysourcelist[$row['s_id']."[".$row['s_file']."]"]["name"] = $row['s_name'];
			$mysourcelist[$row['s_id']."[".$row['s_file']."]"]["gedfile"] = $row['s_file'];
			$mysourcelist[$row['s_id']."[".$row['s_file']."]"]["gedcom"] = $row['s_gedcom'];
		}
		else {
			$mysourcelist[$row['s_id']]["name"] = $row['s_name'];
			$mysourcelist[$row['s_id']]["gedfile"] = $row['s_file'];
			$mysourcelist[$row['s_id']]["gedcom"] = $row['s_gedcom'];
		}
	}
	$res->free();
	return $mysourcelist;
}

/**
 * Search the dates table for sources that had events on the given day
 *
 * @author	yalnifj
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myfamlist array with all individuals that matched the query
 */
function search_sources_dates($day="", $month="", $year="", $fact="", $allgeds=false) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;
	$mysourcelist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	$sql = "SELECT s_id, s_name, s_file, s_gedcom, d_gid FROM ".PHPGEDVIEW_DB_PREFIX."dates, ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_id=d_gid AND s_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$DBCONN->escape($day)."' ";
	if (!empty($month)) $sql .= "AND d_month='".$DBCONN->escape(str2upper($month))."' ";
	if (!empty($year)) $sql .= "AND d_year='".$DBCONN->escape($year)."' ";
	if (!empty($fact)) $sql .= "AND d_fact='".$DBCONN->escape(str2upper($fact))."' ";
	if (!$allgeds) $sql .= "AND d_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= "GROUP BY s_id ORDER BY d_year, d_month, d_day DESC";

	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		if ($allgeds) {
			$mysourcelist[$row['s_id']."[".$row['s_name']."]"]["name"] = $row['s_name'];
			$mysourcelist[$row['s_id']."[".$row['s_name']."]"]["gedfile"] = $row['s_name'];
			$mysourcelist[$row['s_id']."[".$row['s_name']."]"]["gedcom"] = $row['s_gedcom'];
		}
		else {
			$mysourcelist[$row['s_id']]["name"] = $row['s_name'];
			$mysourcelist[$row['s_id']]["gedfile"] = $row['s_name'];
			$mysourcelist[$row['s_id']]["gedcom"] = $row['s_gedcom'];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $mysourcelist;
}

//-- search through the gedcom records for sources
function search_other($query, $allgeds=false, $type="", $ANDOR="AND") {
	global $GEDCOM, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;
	$mysourcelist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';
	if (!is_array($query)) {
		$sql = "SELECT o_id, o_type, o_file, o_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE ";
		//-- make sure that MySQL matches the upper and lower case utf8 characters
		if (has_utf8($query)) $sql .= "(o_gedcom $term '".$DBCONN->escape(str2upper($query))."' OR o_gedcom $term '".$DBCONN->escape(str2lower($query))."')";
		else $sql .= "o_gedcom $term '".$DBCONN->escape($query)."'";
	}
	else {
		$sql = "SELECT o_id, o_type, o_file, o_gedcom FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			if (has_utf8($q)) $sql .= "(o_gedcom $term '".$DBCONN->escape(str2upper($q))."' OR o_gedcom $term '".$DBCONN->escape(str2lower($q))."')";
			else $sql .= "(o_gedcom $term '".$DBCONN->escape($q)."')";
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND o_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "o_file='".$DBCONN->escape($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}

	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		if (count($allgeds) > 1) {
			$mysourcelist[$row['o_id']."[".$row['o_file']."]"]["type"] = $row['o_type'];
			$mysourcelist[$row['o_id']."[".$row['o_file']."]"]["gedfile"] = $row['o_file'];
			$mysourcelist[$row['o_id']."[".$row['o_file']."]"]["gedcom"] = $row['o_gedcom'];
		}
		else {
			$mysourcelist[$row['o_id']]["type"] = $row['o_type'];
			$mysourcelist[$row['o_id']]["gedfile"] = $row['o_file'];
			$mysourcelist[$row['o_id']]["gedcom"] = $row['o_gedcom'];
		}
	}
	$res->free();
	return $mysourcelist;
}

/**
 * Search the dates table for other records that had events on the given day
 *
 * @author	yalnifj
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myfamlist array with all individuals that matched the query
 */
function search_other_dates($day="", $month="", $year="", $fact="", $allgeds=false) {
	global $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $gBitDbType, $GEDCOMS;
	$myrepolist = array();
	if (stristr($gBitDbType, "mysql")!==false) $term = "REGEXP";
	else if (stristr($gBitDbType, "pgsql")!==false) $term = "~*";
	else $term='LIKE';

	$sql = "SELECT o_id, o_file, o_type, o_gedcom, d_gid FROM ".PHPGEDVIEW_DB_PREFIX."dates, ".PHPGEDVIEW_DB_PREFIX."other WHERE o_id=d_gid AND o_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$DBCONN->escape($day)."' ";
	if (!empty($month)) $sql .= "AND d_month='".$DBCONN->escape(str2upper($month))."' ";
	if (!empty($year)) $sql .= "AND d_year='".$DBCONN->escape($year)."' ";
	if (!empty($fact)) $sql .= "AND d_fact='".$DBCONN->escape(str2upper($fact))."' ";
	if (!$allgeds) $sql .= "AND d_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= "GROUP BY o_id ORDER BY d_year, d_month, d_day DESC";

	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$tt = preg_match("/1 NAME (.*)/", $row['o_type'], $match);
		if ($tt == "0") $name = $row['o_id']; else $name = $match[1];
		if ($allgeds) {
			$myrepolist[$row['o_id']."[".$row['o_file']."]"]["name"] = $name;
			$myrepolist[$row['o_id']."[".$row['o_file']."]"]["gedfile"] = $row['o_file'];
			$myrepolist[$row['o_id']."[".$row['o_file']."]"]["type"] = $row['o_type'];
			$myrepolist[$row['o_id']."[".$row['o_file']."]"]["gedcom"] = $row['o_gedcom'];
		}
		else {
			$myrepolist[$row['o_id']]["name"] = $name;
			$myrepolist[$row['o_id']]["gedfile"] = $row['o_file'];
			$myrepolist[$row['o_id']]["type"] = $row['o_type'];
			$myrepolist[$row['o_id']]["gedcom"] = $row['o_gedcom'];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $myrepolist;
}

/**
 * get place parent ID
 * @param array $parent
 * @param int $level
 * @return int
 */
function get_place_parent_id($parent, $level) {
	global $DBCONN, $GEDCOM, $GEDCOMS;

	$parent_id=0;
	for($i=0; $i<$level; $i++) {
		$escparent=preg_replace("/\?/","\\\\\\?", $DBCONN->escape($parent[$i]));
		$psql = "SELECT p_id FROM ".PHPGEDVIEW_DB_PREFIX."places WHERE p_level=".$i." AND p_parent_id=$parent_id AND p_place LIKE '".$escparent."' AND p_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_place";
		$res = dbquery($psql);
		$row =& $res->fetchRow();
		$res->free();
		if (empty($row['p_id'])) break;
		$parent_id = $row['p_id'];
	}
	return $parent_id;
}

/**
 * find all of the places in the hierarchy
 * The $parent array holds the parent hierarchy of the places
 * we want to get.  The level holds the level in the hierarchy that
 * we are at.
 */
function get_place_list() {
	global $numfound, $j, $level, $parent, $found;
	global $GEDCOM, $placelist, $positions, $DBCONN, $GEDCOMS;

	// --- find all of the place in the file
	if ($level==0) $sql = "SELECT p_place FROM ".PHPGEDVIEW_DB_PREFIX."places WHERE p_level=0 AND p_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_place";
	else {
		$parent_id = get_place_parent_id($parent, $level);
		$sql = "SELECT p_place FROM ".PHPGEDVIEW_DB_PREFIX."places WHERE p_level=$level AND p_parent_id=$parent_id AND p_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_place";
	}
	$res = dbquery($sql);

	while ($row =& $res->fetchRow()) {
		$placelist[] = $row['p_place'];
		$numfound++;
	}
	$res->free();
}

/**
 * get all of the place connections
 * @param array $parent
 * @param int $level
 * @return array
 */
function get_place_positions($parent, $level='') {
	global $positions, $GEDCOM, $DBCONN, $GEDCOMS;

	if ($level!='') $p_id = get_place_parent_id($parent, $level);
	else {
		//-- we don't know the level so get the any matching place
		$sql = "SELECT DISTINCT pl_gid FROM ".PHPGEDVIEW_DB_PREFIX."placelinks, ".PHPGEDVIEW_DB_PREFIX."places WHERE p_place LIKE '".$DBCONN->escape($parent)."' AND p_file=pl_file AND p_id=pl_p_id AND p_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
		//print $sql;
		$res = dbquery($sql);
		while ($row =& $res->fetchRow()) {
			$positions[] = $row['pl_gid'];
		}
		$res->free();
		return $positions;
	}
	$sql = "SELECT DISTINCT pl_gid FROM ".PHPGEDVIEW_DB_PREFIX."placelinks WHERE pl_p_id=$p_id AND pl_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while ($row =& $res->fetchRow()) {
		$positions[] = $row['pl_gid'];
	}
	$res->free();
	return $positions;
}

//-- find all of the places
function find_place_list($place) {
	global $GEDCOM, $placelist, $indilist, $famlist, $sourcelist, $otherlist, $DBCONN, $GEDCOMS;

	$sql = "SELECT p_id, p_place, p_parent_id  FROM ".PHPGEDVIEW_DB_PREFIX."places WHERE p_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_parent_id, p_id";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()) {
		if ($row['p_parent_id']==0) $placelist[$row['p_id']] = $row['p_place'];
		else {
			$placelist[$row['p_id']] = $placelist[$row['p_parent_id']].", ".$row['p_place'];
		}
	}
	if (!empty($place)) {
		$found = array();
		foreach($placelist as $indexval => $pplace) {
			if (preg_match("/$place/i", $pplace)>0) {
				$upperplace = str2upper($pplace);
				if (!isset($found[$upperplace])) {
					$found[$upperplace] = $pplace;
				}
			}
		}
		$placelist = array_values($found);
	}
}

//-- find all of the media
function get_media_list() {
	global $GEDCOM, $medialist, $ct, $DBCONN, $GEDCOMS, $MEDIA_DIRECTORY;
	global $GEDCOM_ID_PREFIX, $FAM_ID_PREFIX, $SOURCE_ID_PREFIX;
	$ct = 0;
	if (!isset($medialinks)) $medialinks = array();
	$sqlmm = "SELECT mm_gid, mm_media FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY mm_id ASC";
	$resmm =@ dbquery($sqlmm);
	while($rowmm =& $resmm->fetchRow(DB_FETCHMODE_ASSOC)){
		$sqlm = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_media = '".$rowmm["mm_media"]."' AND m_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
		$resm =@ dbquery($sqlm);
		while($rowm =& $resm->fetchRow(DB_FETCHMODE_ASSOC)){
			$filename = check_media_depth($rowm["m_file"], "NOTRUNC");
			$thumbnail = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $filename);
			$title = $rowm["m_titl"];
			$mediarec = $rowm["m_gedrec"];
			$level = $mediarec{0};
			$isprim="N";
			$isthumb="N";
			$pt = preg_match("/\d _PRIM (.*)/", $mediarec, $match);
			if ($pt>0) $isprim = trim($match[1]);
			$pt = preg_match("/\d _THUM (.*)/", $mediarec, $match);
			if ($pt>0) $isthumb = trim($match[1]);
			$linkid = trim($rowmm["mm_gid"]);
			switch ($linkid{0}) {
				case $GEDCOM_ID_PREFIX:
					$type = "INDI";
					break;
				case $FAM_ID_PREFIX:
					$type = "FAM";
					break;
				case $SOURCE_ID_PREFIX:
					$type = "SOUR";
					break;
			}
			$medialinks[$ct][$linkid] = $type;
			$links = $medialinks[$ct];
			if (!isset($foundlist[$filename])) {
				$media = array();
				$media["file"] = $filename;
				$media["thumb"] = $thumbnail;
				$media["title"] = $title;
				$media["gedcom"] = $mediarec;
				$media["level"] = $level;
				$media["THUM"] = $isthumb;
				$media["PRIM"] = $isprim;
				$medialist[$ct]=$media;
				$foundlist[$filename] = $ct;
				$ct++;
			}
			$medialist[$foundlist[$filename]]["link"]=$links;
		}
	}
}

/**
 * get all first letters of individual's last names
 * @see indilist.php
 * @return array	an array of all letters
 */
function get_indi_alpha() {
	global $CHARACTER_SET, $GEDCOM, $LANGUAGE, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;
	global $MULTI_LETTER_ALPHABET;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;
	global $gGedcom;
	$indialpha = array();

	$danishex = array("OE", "AE", "AA");
	$danishFrom = array("AA", "AE", "OE");
	$danishTo = array("Å", "Æ", "Ø");

	$sql = "SELECT DISTINCT i_letter AS alpha FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file=? ORDER BY 1";
	$res = $gGedcom->mDb->query($sql,array($GEDCOMS[$GEDCOM]["id"]));

	while( $row = $res->fetchRow() ){
		$letter = str2upper($row["alpha"]);
		if ($LANGUAGE=="danish" || $LANGUAGE=="norwegian") $letter = str_replace($danishFrom, $danishTo, $letter);
		$inArray = strpos($MULTI_LETTER_ALPHABET[$LANGUAGE], " ".$letter." ");
		if ($inArray===false) {
			if ((ord(substr($letter, 0, 1)) & 0x80)==0x00) $letter = substr($letter, 0, 1);
		}
		if ($DICTIONARY_SORT[$LANGUAGE]) {
			$position = strpos($UCDiacritWhole, $letter);
			if ($position!==false) {
				$position = $position >> 1;
				$letter = substr($UCDiacritStrip, $position, 1);
			} else {
				$position = strpos($LCDiacritWhole, $letter);
				if ($position!==false) {
					$position = $position >> 1;
					$letter = substr($LCDiacritStrip, $position, 1);
				}
			}
		}
		$indialpha[$letter] = $letter;
	}
	$res->free();

	$sql = "SELECT DISTINCT n_letter AS alpha FROM ".PHPGEDVIEW_DB_PREFIX."names WHERE n_file=? ";
	if (!$SHOW_MARRIED_NAMES) $sql .= " AND n_type!='C'";
	$sql .= " ORDER BY 1";
	$res = $gGedcom->mDb->query($sql, array($GEDCOMS[$GEDCOM]["id"]));

	while( $row = $res->fetchRow() ){
		$letter = str2upper($row["alpha"]);
		if ($LANGUAGE=="danish" || $LANGUAGE=="norwegian") $letter = str_replace($danishFrom, $danishTo, $letter);
		$inArray = strpos($MULTI_LETTER_ALPHABET[$LANGUAGE], " ".$letter." ");
		if ($inArray===false) {
			if ((ord(substr($letter, 0, 1)) & 0x80)==0x00) $letter = substr($letter, 0, 1);
		}
		if ($DICTIONARY_SORT[$LANGUAGE]) {
			$position = strpos($UCDiacritWhole, $letter);
			if ($position!==false) {
				$position = $position >> 1;
				$letter = substr($UCDiacritStrip, $position, 1);
			} else {
				$position = strpos($LCDiacritWhole, $letter);
				if ($position!==false) {
					$position = $position >> 1;
					$letter = substr($LCDiacritStrip, $position, 1);
				}
			}
		}
		$indialpha[$letter] = $letter;
	}
	$res->free();

	return $indialpha;
}

//-- get the first character in the list
function get_fam_alpha() {
	global $CHARACTER_SET, $GEDCOM, $LANGUAGE, $famalpha, $DBCONN, $GEDCOMS;
	global $MULTI_LETTER_ALPHABET;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;

	$famalpha = array();

	$danishex = array("OE", "AE", "AA");
	$danishFrom = array("AA", "AE", "OE");
	$danishTo = array("Å", "Æ", "Ø");

	$sql = "SELECT DISTINCT i_letter AS alpha FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND i_gedcom LIKE '%1 FAMS%' ORDER BY 1";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = str2upper($row["alpha"]);
		if ($LANGUAGE=="danish" || $LANGUAGE=="norwegian") $letter = str_replace($danishFrom, $danishTo, $letter);
		$inArray = strpos($MULTI_LETTER_ALPHABET[$LANGUAGE], " ".$letter." ");
		if ($inArray===false) {
			if ((ord(substr($letter, 0, 1)) & 0x80)==0x00) $letter = substr($letter, 0, 1);
		}
		if ($DICTIONARY_SORT[$LANGUAGE]) {
			$position = strpos($UCDiacritWhole, $letter);
			if ($position!==false) {
				$position = $position >> 1;
				$letter = substr($UCDiacritStrip, $position, 1);
			} else {
				$position = strpos($LCDiacritWhole, $letter);
				if ($position!==false) {
					$position = $position >> 1;
					$letter = substr($LCDiacritStrip, $position, 1);
				}
			}
		}
		$famalpha[$letter] = $letter;
	}
	$res->free();

	$sql = "SELECT DISTINCT n_letter AS alpha FROM ".PHPGEDVIEW_DB_PREFIX."names, ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file=n_file AND i_id=n_gid AND n_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND i_gedcom LIKE '%1 FAMS%' ORDER BY 1";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = str2upper($row["alpha"]);
		if ($LANGUAGE=="danish" || $LANGUAGE=="norwegian") $letter = str_replace($danishFrom, $danishTo, $letter);
		$inArray = strpos($MULTI_LETTER_ALPHABET[$LANGUAGE], " ".$letter." ");
		if ($inArray===false) {
			if ((ord(substr($letter, 0, 1)) & 0x80)==0x00) $letter = substr($letter, 0, 1);
		}
		if ($DICTIONARY_SORT[$LANGUAGE]) {
			$position = strpos($UCDiacritWhole, $letter);
			if ($position!==false) {
				$position = $position >> 1;
				$letter = substr($UCDiacritStrip, $position, 1);
			} else {
				$position = strpos($LCDiacritWhole, $letter);
				if ($position!==false) {
					$position = $position >> 1;
					$letter = substr($LCDiacritStrip, $position, 1);
				}
			}
		}
		$famalpha[$letter] = $letter;
	}
	$res->free();

	$sql = "SELECT f_id FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND (f_husb='' OR f_wife='')";
	$res = dbquery($sql);

	if ($res->numRows()>0) {
		$famalpha["@"] = "@";
	}
	$res->free();

	return $famalpha;
}

/**
 * Get Individuals Starting with a letter
 *
 * This function finds all of the individuals who start with the given letter
 * @param string $letter	The letter to search on
 * @return array	$indilist array
 * @see http://www.phpgedview.net/devdocs/arrays.php#indilist
 */
function get_alpha_indis($letter) {
	global $GEDCOM, $LANGUAGE, $indilist, $surname, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;
	global $MULTI_LETTER_ALPHABET;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;
	global $gGedcom;
	
	$tindilist = array();

	$danishex = array("OE", "AE", "AA");
	$danishFrom = array("AA", "AE", "OE");
	$danishTo = array("Å", "Æ", "Ø");

	$checkDictSort = true;

	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE ";
	if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		if ($letter == "Ø") $text = "OE";
		else if ($letter == "Æ") $text = "AE";
		else if ($letter == "Å") $text = "AA";
		if (isset($text)) $sql .= "(i_letter = '".$letter."' OR i_letter = '".$text."') ";
		else if ($letter=="A") $sql .= "i_letter LIKE '".$letter."' ";
		else $sql .= "i_letter LIKE '".$letter."%' ";
		$checkDictSort = false;
	} else if ($MULTI_LETTER_ALPHABET[$LANGUAGE]!="") {
		$isMultiLetter = strpos($MULTI_LETTER_ALPHABET[$LANGUAGE], " ".$letter." ");
		if ($isMultiLetter!==false) {
			$sql .= "i_letter = '".$letter."' ";
			$checkDictSort = false;
		}
	}
	if ($checkDictSort) {
		$text = "";
		if ($DICTIONARY_SORT[$LANGUAGE]) {
			$inArray = strpos($UCDiacritStrip, $letter);
			if ($inArray!==false) {
				while (true) {
					$text .= " OR i_letter = '".substr($UCDiacritWhole, ($inArray+$inArray), 2)."'";
					$inArray ++;
					if ($inArray > strlen($UCDiacritStrip)) break;
					if (substr($UCDiacritStrip, $inArray, 1)!=$letter) break;
				}
				if ($MULTI_LETTER_ALPHABET[$LANGUAGE]=="") $sql .= "(i_letter LIKE '".$letter."%'".$text.") ";
				else $sql .= "(i_letter = '".$letter."'".$text.") ";
			} else {
				$inArray = strpos($LCDiacritStrip, $letter);
				if ($inArray!==false) {
					while (true) {
						$text .= " OR i_letter = '".substr($LCDiacritWhole, ($inArray+$inArray), 2)."'";
						$inArray ++;
						if ($inArray > strlen($LCDiacritStrip)) break;
						if (substr($LCDiacritStrip, $inArray, 1)!=$letter) break;
					}
					if ($MULTI_LETTER_ALPHABET[$LANGUAGE]=="") $sql .= "(i_letter LIKE '".$letter."%'".$text.") ";
					else $sql .= "(i_letter = '".$letter."'".$text.") ";
				}
			}
		}
		if ($text=="") {
			if ($MULTI_LETTER_ALPHABET[$LANGUAGE]=="") $sql .= "i_letter LIKE '".$letter."%'";
			else $sql .= "i_letter = '".$letter."'";
		}
	}

	//-- add some optimization if the surname is set to speed up the lists
	if (!empty($surname)) $sql .= "AND i_surname LIKE '%".$surname."%' ";
	$sql .= "AND i_file='".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY i_name";
	$res = $gGedcom->mDb->query($sql);

	while( $row = $res->fetchRow() ){
		//if (substr($row["i_letter"], 0, 1)==substr($letter, 0, 1)||(isset($text)?substr($row["i_letter"], 0, 1)==substr($text, 0, 1):FALSE)){
			$indi = array();
			$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], 'P'));
			$indi["isdead"] = $row["i_isdead"];
			$indi["gedcom"] = $row["i_gedcom"];
			$indi["gedfile"] = $row["i_file"];
			$tindilist[$row["i_id"]] = $indi;
			//-- cache the item in the $indilist for improved speed
			$indilist[$row["i_id"]] = $indi;
		//}
	}
	$res->free();

	$checkDictSort = true;

	$sql = "SELECT i_id, i_name, i_file, i_isdead, i_gedcom, i_letter, i_surname, n_letter, n_name, n_surname, n_letter, n_type FROM ".PHPGEDVIEW_DB_PREFIX."individuals, ".PHPGEDVIEW_DB_PREFIX."names WHERE i_id=n_gid AND i_file=n_file AND ";
	if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		if ($letter == "Ø") $text = "OE";
		else if ($letter == "Æ") $text = "AE";
		else if ($letter == "Å") $text = "AA";
		if (isset($text)) $sql .= "(n_letter = '".$letter."' OR n_letter = '".$text."') ";
		else if ($letter=="A") $sql .= "n_letter LIKE '".$letter."' ";
		else $sql .= "n_letter LIKE '".$letter."%' ";
		$checkDictSort = false;
	} else if ($MULTI_LETTER_ALPHABET[$LANGUAGE]!="") {
		$isMultiLetter = strpos($MULTI_LETTER_ALPHABET[$LANGUAGE], " ".$letter." ");
		if ($isMultiLetter!==false) {
			$sql .= "n_letter = '".$letter."' ";
			$checkDictSort = false;
		}
	}
	if ($checkDictSort) {
		$text = "";
		if ($DICTIONARY_SORT[$LANGUAGE]) {
			$inArray = strpos($UCDiacritStrip, $letter);
			if ($inArray!==false) {
				while (true) {
					$text .= " OR n_letter = '".substr($UCDiacritWhole, ($inArray+$inArray), 2)."'";
					$inArray ++;
					if ($inArray > strlen($UCDiacritStrip)) break;
					if (substr($UCDiacritStrip, $inArray, 1)!=$letter) break;
				}
				if ($MULTI_LETTER_ALPHABET[$LANGUAGE]=="") $sql .= "(n_letter LIKE '".$letter."%'".$text.")";
				else $sql .= "(n_letter = '".$letter."'".$text.")";
			} else {
				$inArray = strpos($LCDiacritStrip, $letter);
				if ($inArray!==false) {
					while (true) {
						$text .= " OR n_letter = '".substr($LCDiacritWhole, ($inArray+$inArray), 2)."'";
						$inArray ++;
						if ($inArray > strlen($LCDiacritStrip)) break;
						if (substr($LCDiacritStrip, $inArray, 1)!=$letter) break;
					}
					if ($MULTI_LETTER_ALPHABET[$LANGUAGE]=="") $sql .= "(n_letter LIKE '".$letter."%'".$text.")";
					else $sql .= "(n_letter = '".$letter."'".$text.")";
				}
			}
		}
		if ($text=="") {
			if ($MULTI_LETTER_ALPHABET[$LANGUAGE]=="") $sql .= "n_letter LIKE '".$letter."%'";
			else $sql .= "n_letter = '".$letter."'";
		}
	}
	//-- add some optimization if the surname is set to speed up the lists
	if (!empty($surname)) $sql .= "AND n_surname LIKE '%".$surname."%' ";
	if (!$SHOW_MARRIED_NAMES) $sql .= "AND n_type!='C' ";
	$sql .= "AND i_file='".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY i_name";
	$res = $gGedcom->mDb->query($sql);

	while( $row = $res->fetchRow() ){
		//if (substr($row["n_letter"], 0, strlen($letter))==$letter||(isset($text)?substr($row["n_letter"], 0, strlen($text))==$text:FALSE)){
			if (!isset($indilist[$row["i_id"]])) {
				$indi = array();
				$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"), array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]));
				$indi["isdead"] = $row["i_isdead"];
				$indi["gedcom"] = $row["i_gedcom"];
				$indi["gedfile"] = $row["i_file"];
				//-- cache the item in the $indilist for improved speed
				$indilist[$row["i_id"]] = $indi;
				$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
			}
			else {
				$indilist[$row["i_id"]]["names"][] = array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]);
				$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
			}
		//}
	}
	$res->free();

	return $tindilist;
}

/**
 * Get Individuals with a given surname
 *
 * This function finds all of the individuals who have the given surname
 * @param string $surname	The surname to search on
 * @return array	$indilist array
 * @see http://www.phpgedview.net/devdocs/arrays.php#indilist
 */
function get_surname_indis($surname) {
	global $GEDCOM, $LANGUAGE, $indilist, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS, $gGedcom;

	$tindilist = array();
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_surname LIKE ? AND i_file=?";
	$res = $gGedcom->mDb->query($sql, array( $surname, $GEDCOMS[$GEDCOM]["id"] ));

	while( $row =& $res->fetchRow() ){
		$indi = array();
		$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"));
		$indi["isdead"] = $row["i_isdead"];
		$indi["gedcom"] = $row["i_gedcom"];
		$indi["gedfile"] = $row["i_file"];
		$indilist[$row["i_id"]] = $indi;
		$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
	}

	$sql = "SELECT i_id, i_name, i_file, i_isdead, i_gedcom, i_letter, i_surname, n_letter, n_name, n_surname, n_letter, n_type FROM ".PHPGEDVIEW_DB_PREFIX."individuals, ".PHPGEDVIEW_DB_PREFIX."names WHERE i_id=n_gid AND i_file=n_file AND n_surname LIKE ? ";
	if (!$SHOW_MARRIED_NAMES) $sql .= "AND n_type!='C' ";
	$sql .= "AND i_file=? ORDER BY n_surname";
	$res = $gGedcom->mDb->query($sql, array( $surname, $GEDCOMS[$GEDCOM]["id"] ));

	while( $row = $res->fetchRow() ){
		if (isset($indilist[$row["i_id"]])) {
			$indilist[$row["i_id"]]["names"][] = array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]);
			$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
		}
		else {
			$indi = array();
			$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"), array($row["n_name"], $row["n_letter"], $row["n_surname"], $row["n_type"]));
			$indi["isdead"] = $row["i_isdead"];
			$indi["gedcom"] = $row["i_gedcom"];
			$indi["gedfile"] = $row["i_file"];
			$indilist[$row["i_id"]] = $indi;
			$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
		}
	}
	return $tindilist;
}

/**
 * Get Families Starting with a letter
 *
 * This function finds all of the families who start with the given letter
 * @param string $letter	The letter to search on
 * @return array	$indilist array
 * @see get_alpha_indis()
 * @see http://www.phpgedview.net/devdocs/arrays.php#famlist
 */
function get_alpha_fams($letter) {
	global $GEDCOM, $famlist, $indilist, $pgv_lang, $LANGUAGE, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;
	global $MULTI_LETTER_ALPHABET;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;

	$danishex = array("OE", "AE", "AA");
	$danishFrom = array("AA", "AE", "OE");
	$danishTo = array("Å", "Æ", "Ø");

	$tfamlist = array();
	$temp = $SHOW_MARRIED_NAMES;
	$SHOW_MARRIED_NAMES = false;
	$myindilist = get_alpha_indis($letter);
	$SHOW_MARRIED_NAMES = $temp;
	if ($letter=="(" || $letter=="[" || $letter=="?" || $letter=="/" || $letter=="*" || $letter=="+" || $letter==')') $letter = "\\".$letter;
	foreach($myindilist as $gid=>$indi) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
		$surnames = array();
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			$famrec = find_family_record($famid);
			if ($famlist[$famid]["husb"]==$gid) {
				$HUSB = $famlist[$famid]["husb"];
				$WIFE = $famlist[$famid]["wife"];
			}
			else {
				$HUSB = $famlist[$famid]["wife"];
				$WIFE = $famlist[$famid]["husb"];
			}
			$hname="";
			$surnames = array();
			foreach($indi["names"] as $indexval => $namearray) {
				//-- don't use married names in the family list
				if ($namearray[3]!='C') {
					$text = "";
					if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
						if ($letter == "Ø") $text = "OE";
						else if ($letter == "Æ") $text = "AE";
						else if ($letter == "Å") $text = "AA";
					}
					if ($DICTIONARY_SORT[$LANGUAGE]) {
						if (strlen($namearray[1])>1) {
							$aPos = strpos($UCDiacritWhole, $namearray[1]);
							if ($aPos!==false) {
								if ($letter==substr($UCDiacritStrip, ($aPos>>1), 1)) $text = $namearray[1];
							} else {
								$aPos = strpos($LCDiacritWhole, $namearray[1]);
								if ($aPos!==false) {
									if ($letter==substr($LCDiacritStrip, ($aPos>>1), 1)) $text = $namearray[1];
								}
							}
						}
					}
					if ((preg_match("/^$letter/", $namearray[1])>0)||(!empty($text)&&preg_match("/^$text/", $namearray[1])>0)) {
						$surnames[str2upper($namearray[2])] = $namearray[2];
						$hname = sortable_name_from_name($namearray[0]);
					}
				}
			}
			if (!empty($hname)) {
				$wname = get_sortable_name($WIFE);
				if (hasRTLText($hname)) {
					$indirec = find_person_record($WIFE);
					if (isset($indilist[$WIFE])) {
						foreach($indilist[$WIFE]["names"] as $n=>$namearray) {
							if (hasRTLText($namearray[0])) {
								$wname = sortable_name_from_name($namearray[0]);
								break;
							}
						}
					}
				}
				$name = $hname ." + ". $wname;
				$famlist[$famid]["name"] = $name;
				if (!isset($famlist[$famid]["surnames"])||count($famlist[$famid]["surnames"])==0) $famlist[$famid]["surnames"] = $surnames;
				else pgv_array_merge($famlist[$famid]["surnames"], $surnames);
				$tfamlist[$famid] = $famlist[$famid];
			}
		}
	}

	//-- handle the special case for @N.N. when families don't have any husb or wife
	//-- SHOULD WE SHOW THE UNDEFINED? MA
	if ($letter=="@") {
		$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (f_husb='' OR f_wife='') AND f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);

		if ($res->numRows()>0) {
			while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
				$fam = array();
				$hname = get_sortable_name($row["f_husb"]);
				$wname = get_sortable_name($row["f_wife"]);
				if (!empty($hname)) $name = $hname;
				else $name = "@N.N., @P.N.";
				if (!empty($wname)) $name .= " + ".$wname;
				else $name .= " + @N.N., @P.N.";
				$fam["name"] = $name;
				$fam["HUSB"] = $row["f_husb"];
				$fam["WIFE"] = $row["f_wife"];
				$fam["CHIL"] = $row["f_chil"];
				$fam["gedcom"] = $row["f_gedcom"];
				$fam["gedfile"] = $row["f_file"];
				$fam["surnames"] = array("@N.N.");
				$tfamlist[$row["f_id"]] = $fam;
				//-- cache the items in the lists for improved speed
				$famlist[$row["f_id"]] = $fam;
			}
		}
		$res->free();
	}
	return $tfamlist;
}

/**
 * Get Families with a given surname
 *
 * This function finds all of the individuals who have the given surname
 * @param string $surname	The surname to search on
 * @return array	$indilist array
 * @see http://www.phpgedview.net/devdocs/arrays.php#indilist
 */
function get_surname_fams($surname) {
	global $GEDCOM, $famlist, $indilist, $pgv_lang, $DBCONN, $SHOW_MARRIED_NAMES, $GEDCOMS;
	$tfamlist = array();
	$temp = $SHOW_MARRIED_NAMES;
	$SHOW_MARRIED_NAMES = false;
	$myindilist = get_surname_indis($surname);
	$SHOW_MARRIED_NAMES = $temp;
	foreach($myindilist as $gid=>$indi) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			$famrec = find_family_record($famid);
			if ($famlist[$famid]["husb"]==$gid) {
				$HUSB = $famlist[$famid]["husb"];
				$WIFE = $famlist[$famid]["wife"];
			}
			else {
				$HUSB = $famlist[$famid]["wife"];
				$WIFE = $famlist[$famid]["husb"];
			}
			$hname = "";
			foreach($indi["names"] as $indexval => $namearray) {
				if (stristr($namearray[2], $surname)!==false) $hname = sortable_name_from_name($namearray[0]);
			}
			if (!empty($hname)) {
				$wname = get_sortable_name($WIFE);
				if (hasRTLText($hname)) {
					$indirec = find_person_record($WIFE);
					if (isset($indilist[$WIFE])) {
						foreach($indilist[$WIFE]["names"] as $n=>$namearray) {
							if (hasRTLText($namearray[0])) {
								$wname = sortable_name_from_name($namearray[0]);
								break;
							}
						}
					}
				}
				$name = $hname ." + ". $wname;
				$famlist[$famid]["name"] = $name;
				$tfamlist[$famid] = $famlist[$famid];
			}
		}
	}

	//-- handle the special case for @N.N. when families don't have any husb or wife
	//-- SHOULD WE SHOW THE UNDEFINED? MA
	if ($surname=="@N.N.") {
		$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE (f_husb='' OR f_wife='') AND f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);

		if ($res->numRows()>0) {
			while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
				$fam = array();
				$hname = get_sortable_name($row["f_husb"]);
				$wname = get_sortable_name($row["f_wife"]);
				if (empty($hname)) $hname = "@N.N., @P.N.";
				if (empty($wname)) $wname = "@N.N., @P.N.";
				if (empty($row["f_husb"])) $name = $hname." + ".$wname;
				else $name = $wname." + ".$hname;
				$fam["name"] = $name;
				$fam["HUSB"] = $row["f_husb"];
				$fam["WIFE"] = $row["f_wife"];
				$fam["CHIL"] = $row["f_chil"];
				$fam["gedcom"] = $row["f_gedcom"];
				$fam["gedfile"] = $row["f_file"];
				$tfamlist[$row["f_id"]] = $fam;
				//-- cache the items in the lists for improved speed
				$famlist[$row["f_id"]] = $fam;
			}
		}
		$res->free();
	}
	return $tfamlist;
}

//-- function to find the gedcom id for the given rin
function find_rin_id($rin) {
	global $GEDCOM, $DBCONN, $GEDCOMS;

	$sql = "SELECT i_id FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_rin='$rin' AND i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		return $row["i_id"];
	}
	return $rin;
}

/**
 * Delete a gedcom from the database and the system
 * Does not delete the file from the file system
 * @param string $ged 	the filename of the gedcom to delete
 */
function delete_gedcom($ged) {
	global $INDEX_DIRECTORY, $pgv_changes, $DBCONN, $GEDCOMS;

	if (!isset($GEDCOMS[$ged])) return;
	$dbged = $GEDCOMS[$ged]["id"];

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."dates WHERE d_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."favorites WHERE fv_file='".$DBCONN->escape($ged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_gedfile='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_gedfile='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."names WHERE n_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."nextid WHERE ni_gedfile='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."placelinks WHERE pl_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."places WHERE p_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file='".$DBCONN->escape($dbged)."'";
	$res = dbquery($sql);

	if (isset($pgv_changes)) {
		//-- erase any of the changes
		foreach($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$ged) unset($pgv_changes[$cid]);
		}
		write_changes();
	}
}

//-- return the current size of the given list
//- list options are indilist famlist sourcelist and otherlist
function get_list_size($list) {
	global $GEDCOM, $DBCONN, $GEDCOMS;

	switch($list) {
		case "indilist":
			$sql = "SELECT count(i_id) FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);

			while($row =& $res->fetchRow()) return $row['count'];
		break;
		case "famlist":
			$sql = "SELECT count(f_id) FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);

			while($row =& $res->fetchRow()) return $row['count'];
		break;
		case "sourcelist":
			$sql = "SELECT count(s_id) FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);

			while($row =& $res->fetchRow()) return $row['count'];
		break;
		case "otherlist":
			$sql = "SELECT count(o_id) FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);

			while($row =& $res->fetchRow()) return $row['count'];
		break;
	}
	return 0;
}

/**
 * get the top surnames
 * @param int $num	how many surnames to return
 * @return array
 */
function get_top_surnames($num) {
	global $GEDCOM, $DBCONN, $GEDCOMS;

	$surnames = array();
	$sql = "SELECT COUNT(i_surname), i_surname FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' GROUP BY i_surname ORDER BY 1 DESC";
	$res = dbquery($sql);

	if ($res) {
		while($row =& $res->fetchRow()) {
			if (isset($surnames[str2upper($row['i_surname'])]["match"])) $surnames[str2upper($row['i_surname'])]["match"] += $row['count'];
			else {
				$surnames[str2upper($row['i_surname'])]["name"] = $row['i_surname'];
				$surnames[str2upper($row['i_surname'])]["match"] = $row['count'];
			}
		}
		$res->free();
	}
	$sql = "SELECT COUNT(n_surname), n_surname FROM ".PHPGEDVIEW_DB_PREFIX."names WHERE n_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND n_type!='C' GROUP BY n_surname ORDER BY 1 DESC";
	$res = dbquery($sql);

	if ($res) {
		while($row =& $res->fetchRow()) {
			if (isset($surnames[str2upper($row['n_surname'])]["match"])) $surnames[str2upper($row['n_surname'])]["match"] += $row['count'];
			else {
				$surnames[str2upper($row['n_surname'])]["name"] = $row['n_surname'];
				$surnames[str2upper($row['n_surname'])]["match"] = $row['count'];
			}
		}
		$res->free();
	}
	return $surnames;
}

/**
 * get next unique id for the given table
 * @param string $table 	the name of the table
 * @param string $field		the field to get the next number for
 * @return int the new id
JFileChooser chooser = new JFileChooser();
			//chooser.setFileSelectionMode(JFileChooser.DIRECTORIES_ONLY);
 */
function get_next_id($table, $field) {
	global $TABLE_IDS;

	if (!isset($TABLE_IDS)) $TABLE_IDS = array();
	if (isset($TABLE_IDS[$table][$field])) {
		$TABLE_IDS[$table][$field]++;
		return $TABLE_IDS[$table][$field];
	}
	$newid = 0;
	$sql = "SELECT MAX($field) FROM ".PHPGEDVIEW_DB_PREFIX.$table;
	$res = dbquery($sql);

	if ($res) {
		$row = $res->fetchRow();
		$res->free();
		$newid = $row['max'];
	}
	$newid++;
	$TABLE_IDS[$table][$field] = $newid;
	return $newid;
}

/**
 * get a list of remote servers
 */
function get_server_list(){
 	global $GEDCOM, $GEDCOMS;
	global $DBCONN, $sitelist, $sourcelist;

	//if (isset($sitelist)) return $sitelist;
	$sitelist = array();

	if (isset($GEDCOMS[$GEDCOM]) && check_for_import($GEDCOM)) {
		$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file='".$DBCONN->escape($GEDCOMS[$GEDCOM]["id"])."' AND s_gedcom LIKE '%1 _DBID%' ORDER BY s_name";
		$res = dbquery($sql);

		$ct = $res->numRows();
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			$source = array();
			$source["name"] = $row["s_name"];
			$source["gedcom"] = $row["s_gedcom"];
			$source["gedfile"] = $row["s_file"];
			$sitelist[$row["s_id"]] = $source;
			$sourcelist[$row["s_id"]] = $source;
		}
		$res->free();
	}

	return $sitelist;
}

function delete_fact($linenum, $pid, $gedrec) {
	global $record, $linefix, $pgv_lang, $DBCONN;
	if (!empty($linenum)) {
		if ($linenum==0) {
			if (delete_gedrec($pid)) print $pgv_lang["gedrec_deleted"];
		}
		else {
			$gedlines = preg_split("/[\r\n]+/", $gedrec);
			// NOTE: The array_pop is used to kick off the last empty element on the array
			// NOTE: To prevent empty lines in the GEDCOM
			// DEBUG: Records without line breaks are imported as 1 big string
			if ($linefix > 0) array_pop($gedlines);
			$newged = "";
			// NOTE: Add all lines that are before the fact to be deleted
			for($i=0; $i<$linenum; $i++) {
				$newged .= trim($gedlines[$i])."\r\n";
			}
			if (isset($gedlines[$linenum])) {
				$fields = preg_split("/\s/", $gedlines[$linenum]);
				$glevel = $fields[0];
				$ctlines = count($gedlines);
				$i++;
				if ($i<$ctlines) {
					// Remove the fact
					while((isset($gedlines[$i]))&&($gedlines[$i]{0}>$glevel)) $i++;
					// Add the remaining lines
					while($i<$ctlines) {
						$newged .= trim($gedlines[$i])."\r\n";
						$i++;
					}
				}
			}
			if ($newged != "")  return $newged;
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
global $DBCONN, $GEDCOMS, $GEDCOM;
	$sql = "SELECT r_gid FROM ".PHPGEDVIEW_DB_PREFIX."remotelinks WHERE r_linkid='".$DBCONN->escape($rfn)."' AND r_file='".$GEDCOMS[$GEDCOM]['id']."'";
	$res = dbquery($sql);

	if ($res->numRows()>0) {
		$row = $res->fetchRow();
		$res->free();
		return $row['r_gid'];
	}
	else {
		return false;
	}
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
function get_event_list() {
	global $month, $year, $day, $monthtonum, $USE_RTL_FUNCTIONS;
	global $INDEX_DIRECTORY, $GEDCOM, $DEBUG;
  	global $DAYS_TO_SHOW_LIMIT;

	if (!isset($DAYS_TO_SHOW_LIMIT)) $DAYS_TO_SHOW_LIMIT = 30;

	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL";	// These are always excluded
	$skipfacts .= ",CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";

	// Look for cached Facts data
	$found_facts = array();
	$cache_load = false;
	if ((file_exists($INDEX_DIRECTORY.$GEDCOM."_upcoming.php"))&&(!isset($DEBUG)||($DEBUG==false))) {
		$modtime = filemtime($INDEX_DIRECTORY.$GEDCOM."_upcoming.php");
		$mday = date("d", $modtime);
		if ($mday==$day) {
			$fp = fopen($INDEX_DIRECTORY.$GEDCOM."_upcoming.php", "rb");
			$fcache = fread($fp, filesize($INDEX_DIRECTORY.$GEDCOM."_upcoming.php"));
			fclose($fp);
			$found_facts = unserialize($fcache);
			$cache_load = true;
		}
	}

	if (!$cache_load) {
		$nmonth = $monthtonum[strtolower($month)];
		$dateRangeStart = mktime(0,0,0,$nmonth,$day,$year);
		$dateRangeEnd = $dateRangeStart+(60*60*24*$DAYS_TO_SHOW_LIMIT)-1;
		$mmon = strtolower(date("M", $dateRangeStart));
		$mmon3 = strtolower(date("M", $dateRangeEnd));
		$mmon2 = $mmon3;
		if ($mmon3=="mar" && $mmon=="jan") $mmon2="feb";

		// Search database for raw Indi data if no cache was found
		$dayindilist = array();
		$dayindilist = search_indis_dates("", $mmon);
		if ($mmon!=$mmon2) {
			$dayindilist2 = search_indis_dates("", $mmon2);
			$dayindilist = pgv_array_merge($dayindilist, $dayindilist2);
		}
		if ($mmon2!=$mmon3) {
		  	$dayindilist2 = search_indis_dates("", $mmon3);
		  	$dayindilist = pgv_array_merge($dayindilist, $dayindilist2);
		}

		// Search database for raw Family data if no cache was found
		$dayfamlist = array();
		$dayfamlist = search_fams_dates("", $mmon);
		if ($mmon!=$mmon2) {
			$dayfamlist2 = search_fams_dates("", $mmon2);
			$dayfamlist = pgv_array_merge($dayfamlist, $dayfamlist2);
		}
		if ($mmon2!=$mmon3) {
			$dayfamlist2 = search_fams_dates("", $mmon3);
			$dayfamlist = pgv_array_merge($dayfamlist, $dayfamlist2);
		}

// Apply filter criteria and perform other transformations on the raw data
		$found_facts = array();
		foreach($dayindilist as $gid=>$indi) {
			$facts = get_all_subrecords($indi["gedcom"], $skipfacts, false, false, false);
			foreach($facts as $key=>$factrec) {
				$date = 0; //--- MA @@@
				$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $factrec, $match);
				if ($hct>0) {
					if ($USE_RTL_FUNCTIONS) {
						$dct = preg_match("/2 DATE (.+)/", $factrec, $match);
						$hebrew_date = parse_date(trim($match[1]));
						$date = jewishGedcomDateToCurrentGregorian($hebrew_date);
					}
				} else {
				  	$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
				  	if ($ct>0) $date = parse_date(trim($match[1]));
				}
				if ($date != 0) {
					$startSecond = 1;
					if ($date[0]["day"]=="") {
						$startSecond = 0;
						$date[0]["day"] = ($date[0]["month"]==$nmonth) ? $day+1 : 1;
					}
					$anniversaryDate = mktime(0,0,$startSecond,(int)$date[0]["mon"],(int)$date[0]["day"],$year);
					if ($anniversaryDate<$dateRangeStart) $anniversaryDate = mktime(0,0,$startSecond,(int)$date[0]["mon"],(int)$date[0]["day"],$year+1);
					if ($anniversaryDate>=$dateRangeStart && $anniversaryDate<=$dateRangeEnd) {
						// Strip useless information:
						//   NOTE, ADDR, OBJE, SOUR, PAGE, DATA, TEXT, CONC, CONT
						$factrec = preg_replace("/\d\s+(NOTE|ADDR|OBJE|SOUR|PAGE|DATA|TEXT|CONC|CONT)\s+(.+)\n/", "", $factrec);
						$found_facts[] = array($gid, $factrec, "INDI", $anniversaryDate);
					}
				}
			}
		}
		foreach($dayfamlist as $gid=>$fam) {
			$facts = get_all_subrecords($fam["gedcom"], $skipfacts, false, false, false);
			foreach($facts as $key=>$factrec) {
				$date = 0; //--- MA @@@
				$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $factrec, $match);
				if ($hct>0) {
					if ($USE_RTL_FUNCTIONS) {
						$dct = preg_match("/2 DATE (.+)/", $factrec, $match);
						$hebrew_date = parse_date(trim($match[1]));
						$date = jewishGedcomDateToCurrentGregorian($hebrew_date);
					}
				} else {
					$ct = preg_match("/2 DATE (.+)/", $factrec, $match);
					if ($ct>0) $date = parse_date(trim($match[1]));
				}
				if ($date != 0) {
					$startSecond = 1;
					if ($date[0]["day"]=="") {
						$startSecond = 0;
						$date[0]["day"] = ($date[0]["month"]==$nmonth) ? $day+1 : 1;
					}
					$anniversaryDate = mktime(0,0,$startSecond,(int)$date[0]["mon"],(int)$date[0]["day"],$year);
					if ($anniversaryDate<$dateRangeStart) $anniversaryDate = mktime(0,0,$startSecond,(int)$date[0]["mon"],(int)$date[0]["day"],$year+1);
					if ($anniversaryDate>=$dateRangeStart && $anniversaryDate<=$dateRangeEnd) {
						// Strip useless information:
						//   NOTE, ADDR, OBJE, SOUR, PAGE, DATA, TEXT, CONC, CONT
						$factrec = preg_replace("/\d\s+(NOTE|ADDR|OBJE|SOUR|PAGE|DATA|TEXT|CONC|CONT)\s+(.+)\n/", "", $factrec);
						$found_facts[] = array($gid, $factrec, "FAM", $anniversaryDate);
					}
				}
			}
		}

// Sort the Facts data just found by anniversary date
		uasort($found_facts, "compare_foundFacts_datestamp");
		reset($found_facts);

// Cache the Facts data just found
		if (is_writable($INDEX_DIRECTORY)) {
			$fp = fopen($INDEX_DIRECTORY."/".$GEDCOM."_upcoming.php", "wb");
			fwrite($fp, serialize($found_facts));
			fclose($fp);
			$logline = AddToLog($GEDCOM."_upcoming.php updated by >".getUserName()."<");
 			if (!empty($COMMIT_COMMAND)) check_in($logline, $GEDCOM."_upcoming.php", $INDEX_DIRECTORY);
		}
	}

	return $found_facts;
}

/**
 * Helper function for sorting the $found_facts array
 */
function compare_foundFacts_datestamp($a, $b) {
	if ($a[3] == $b[3]) return 0;
	return ($a[3] < $b[3]) ? -1 : 1;
}

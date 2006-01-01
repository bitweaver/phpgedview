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
 * @version $Id: functions_db.php,v 1.2 2006/01/01 14:56:58 lsces Exp $
 * @package PhpGedView
 * @subpackage DB
 */
if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	print "Now, why would you want to do that.	You're not hacking are you?";
	exit;
}

require_once('includes/media_class.php');

//-- load the PEAR:DB files
if (!class_exists("DB")) require_once('includes/DB.php');

//-- set the REGEXP status of databases
$REGEXP_DB = true;
if ($DBTYPE=='sqlite') $REGEXP_DB = false;

//-- uncomment the following line to turn on sql query logging
//$SQL_LOG = true;

/**
 * query the database
 *
 * this function will perform the given SQL query on the database
 * @param string $sql		the sql query to execture
 * @param boolean $show_error	whether or not to show any error messages
 * @return Object the connection result
 */
function &dbquery($sql, $show_error=true) {
	global $DBCONN, $TOTAL_QUERIES, $INDEX_DIRECTORY, $SQL_LOG, $LAST_QUERY, $CONFIGURED;

	if (!$CONFIGURED) return false;
	if (!isset($DBCONN)) {
		//print "No Connection";
		return false;
	}
	//-- make sure a database connection has been established
	if (DB::isError($DBCONN)) {
		print $DBCONN->getCode()." ".$DBCONN->getMessage();
		return $DBCONN;
	}
//print $TOTAL_QUERIES."-".$sql."<br />\n";
//debug_print_backtrace()."<br /><br />";
	$res =& $DBCONN->query($sql);
	$LAST_QUERY = $sql;
	$TOTAL_QUERIES++;
	if (!empty($SQL_LOG)) {
		$fp = fopen($INDEX_DIRECTORY."/sql_log.txt", "a");
		fwrite($fp, date("Y-m-d h:i:s")."\t".$_SERVER["SCRIPT_NAME"]."\t".$TOTAL_QUERIES."-".$sql."\r\n");
		fclose($fp);
	}
	if (DB::isError($res)) {
		if ($show_error) print "<span class=\"error\"><b>ERROR:".$res->getCode()." ".$res->getMessage()." <br />SQL:</b>".$res->getUserInfo()."</span><br /><br />\n";
	}
	return $res;
}

/**
 * query the database and return the first row
 *
 * this function will perform the given SQL query on the database and return the first row in the result set
 * @param string $sql		the sql query to execture
 * @param boolean $show_error	whether or not to show any error messages
 * @return array the found row
 */
function dbgetrow($sql, $show_error=true) {
	global $DBCONN, $TOTAL_QUERIES, $INDEX_DIRECTORY, $SQL_LOG;
	//-- make sure a database connection has been established
	if (DB::isError($DBCONN)) {
		return false;
	}

	$row =& $DBCONN->getRow($sql);
	$TOTAL_QUERIES++;
	if (!empty($SQL_LOG)) {
		$fp = fopen($INDEX_DIRECTORY."/sql_log.txt", "a");
		fwrite($fp, date("Y-m-d h:m:s")."\t".$_SERVER["SCRIPT_NAME"]."\t$sql\n");
		fclose($fp);
	}
	if (DB::isError($row)) {
		if ($show_error) print "<span class=\"error\"><b>ERROR:".$row->getMessage()." <br />SQL:</b>$sql</span><br /><br />\n";
	}
	return $row;
}

/**
 * prepare an item to be updated in the database
 *
 * add slashes and convert special chars so that it can be added to db
 * @param mixed $item		an array or string to be prepared for the database
 */
function db_prep($item) {
	global $DBCONN;

	if (is_array($item)) {
		foreach($item as $key=>$value) {
			$item[$key]=db_prep($value);
		}
		return $item;
	}
	else {
		if (DB::isError($DBCONN)) return $item;
		if (is_object($DBCONN)) return $DBCONN->escapeSimple($item);
		//-- use the following commented line to convert between character sets
		//return $DBCONN->escapeSimple(iconv("iso-8859-1", "UTF-8", $item));
	}
}

/**
 * Clean up an item retrieved from the database
 *
 * clean the slashes and convert special
 * html characters to their entities for
 * display and entry into form elements
 * @param mixed $item	the item to cleanup
 * @return mixed the cleaned up item
 */
function db_cleanup($item) {
//	return $item;
	if (is_array($item)) {
		foreach($item as $key=>$value) {
			if ($key!="gedcom") $item[$key]=stripslashes($value);
			else $key=$value;
		}
		return $item;
	}
	else {
		return stripslashes($item);
	}
}

/**
 * check if a gedcom has been imported into the database
 *
 * this function checks the database to see if the given gedcom has been imported yet.
 * @param string $ged the filename of the gedcom to check for import
 * @return bool return true if the gedcom has been imported otherwise returns false
 */
function check_for_import($ged) {
	global $TBLPREFIX, $BUILDING_INDEX, $DBCONN, $GEDCOMS;

	$sql = "SELECT count(i_id) FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$ged]["id"])."'";
	$res = dbquery($sql, false);
	
	if (!DB::isError($res)) {
		$row =& $res->fetchRow();
		$res->free();
		if ($row[0]>0) return true;
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
	global $TBLPREFIX;
	global $GEDCOMS, $GEDCOM, $famlist, $DBCONN;

	if (empty($famid)) return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;
	
	if (isset($famlist[$famid]["gedcom"])&&($famlist[$famid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $famlist[$famid]["gedcom"];

	$sql = "SELECT f_gedcom, f_file, f_husb, f_wife FROM ".$TBLPREFIX."families WHERE f_id LIKE '".$DBCONN->escapeSimple($famid)."' AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);
	
	$row =& $res->fetchRow();

	$famlist[$famid]["gedcom"] = $row[0];
	$famlist[$famid]["gedfile"] = $row[1];
	$famlist[$famid]["husb"] = $row[2];
	$famlist[$famid]["wife"] = $row[3];
	find_person_record($row[2]);
	find_person_record($row[3]);
	$res->free();
	return $row[0];
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
	global $TBLPREFIX;
	global $GEDCOM, $GEDCOMS;
	global $BUILDING_INDEX, $indilist, $DBCONN;

	if (empty($pid)) return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;

	if ($gedfile>=1) $gedfile = get_gedcom_from_id($gedfile);
	//-- first check the indilist cache
	// cache is unreliable for use with different gedcoms in user favorites (sjouke)
	if ((isset($indilist[$pid]["gedcom"]))&&($indilist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $indilist[$pid]["gedcom"];

	$sql = "SELECT i_gedcom, i_name, i_isdead, i_file, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE i_id LIKE '".$DBCONN->escapeSimple($pid)."' AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);
	
	if (!DB::isError($res)) {
		if ($res->numRows()==0) {
			return false;
		}
		$row =& $res->fetchRow();
		$indilist[$pid]["gedcom"] = $row[0];
		$indilist[$pid]["names"] = get_indi_names($row[0]);
		$indilist[$pid]["isdead"] = $row[2];
		$indilist[$pid]["gedfile"] = $row[3];
		$res->free();
		return $row[0];
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
	global $TBLPREFIX, $GEDCOMS, $MEDIA_ID_PREFIX;
	global $GEDCOM, $indilist, $famlist, $sourcelist, $otherlist, $DBCONN;
	
	if (empty($pid)) return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;
	
	if ((isset($indilist[$pid]["gedcom"]))&&($indilist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $indilist[$pid]["gedcom"];
	if ((isset($famlist[$pid]["gedcom"]))&&($famlist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $famlist[$pid]["gedcom"];
	if ((isset($sourcelist[$pid]["gedcom"]))&&($sourcelist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $sourcelist[$pid]["gedcom"];
	if ((isset($repolist[$pid]["gedcom"])) && ($repolist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $repolist[$pid]["gedcom"];
	if ((isset($otherlist[$pid]["gedcom"]))&&($otherlist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $otherlist[$pid]["gedcom"];
	if ((isset($medialist[$pid]["gedcom"]))&&($medialist[$pid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $medialist[$pid]["gedcom"];
	
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
		case 'SOUR':
			$gedrec = find_source_record($pid, $gedfile);
			break;
		case 'REPO':
			$gedrec = find_repo_record($pid, $gedfile);
			break;
		case 'OBJE':
			$gedrec = find_media_record($pid, $gedfile);
			break;
	} 

	//-- unable to guess the type so look in all the tables
	if (empty($gedrec)) {
		$sql = "SELECT o_gedcom, o_file FROM ".$TBLPREFIX."other WHERE o_id LIKE '".$DBCONN->escapeSimple($pid)."' AND o_file='".$DBCONN->escapeSimple($GEDCOMS[$gedfile]["id"])."'";
		$res =& dbquery($sql);
		if ($res->numRows()!=0) {
			$row =& $res->fetchRow();
			$res->free();
			$otherlist[$pid]["gedcom"] = $row[0];
			$otherlist[$pid]["gedfile"] = $row[1];
			return $row[0];
		}
		$gedrec = find_person_record($pid, $gedfile);
		if (empty($gedrec)) {
			$gedrec = find_family_record($pid, $gedfile);
			if (empty($gedrec)) {
				$gedrec = find_source_record($pid, $gedfile);
				if (empty($gedrec)) $gedrec = find_media_record($pid, $gedfile);
			}
		}
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
	global $TBLPREFIX, $GEDCOMS;
	global $GEDCOM, $sourcelist, $DBCONN;

	if ($sid=="") return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;
	
	if (isset($sourcelist[$sid]["gedcom"]) && ($sourcelist[$sid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $sourcelist[$sid]["gedcom"];

	$sql = "SELECT s_gedcom, s_name, s_file FROM ".$TBLPREFIX."sources WHERE s_id LIKE '".$DBCONN->escapeSimple($sid)."' AND s_file='".$DBCONN->escapeSimple($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);
	
	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$sourcelist[$sid]["name"] = stripslashes($row[1]);
		$sourcelist[$sid]["gedcom"] = $row[0];
		$sourcelist[$sid]["gedfile"] = $row[2];
		$res->free();
		return $row[0];
	}
	else {
		return false;
		//return find_record_in_file($sid);
	}
}


/**
 * Find a repository record by its ID
 * @param string $rid	the record id
 * @param string $gedfile	the gedcom file id
 */
function find_repo_record($rid, $gedfile="") {
	global $TBLPREFIX, $GEDCOMS;
	global $GEDCOM, $repolist, $DBCONN;

	if ($rid=="") return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;
	
	if (isset($repolist[$rid]["gedcom"]) && ($repolist[$rid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $repolist[$rid]["gedcom"];

	$sql = "SELECT o_id, o_gedcom, o_file FROM ".$TBLPREFIX."other WHERE o_type='REPO' AND o_id LIKE '".$DBCONN->escapeSimple($rid)."' AND o_file='".$DBCONN->escapeSimple($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);
	
	if ($res->numRows()!=0) {
		$row =& $res->fetchRow();
		$tt = preg_match("/1 NAME (.*)/", $row[1], $match);
		if ($tt == "0") $name = $row[0]; else $name = $match[1];
		$repolist[$rid]["name"] = stripslashes($name);
		$repolist[$rid]["gedcom"] = $row[1];
		$repolist[$rid]["gedfile"] = $row[2];
		$res->free();
		return $row[1];
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
	global $TBLPREFIX, $GEDCOMS;
	global $GEDCOM, $medialist, $DBCONN, $MULTI_MEDIA;

	//-- don't look for a media record if not using media
	if (!$MULTI_MEDIA) return false;
	if ($rid=="") return false;
	if (empty($gedfile)) $gedfile = $GEDCOM;
	
	//-- first check for the record in the cache
	if (isset($medialist[$rid]["gedcom"]) && ($medialist[$rid]["gedfile"]==$GEDCOMS[$gedfile]["id"])) return $medialist[$rid]["gedcom"];

	$sql = "SELECT * FROM ".$TBLPREFIX."media WHERE m_media LIKE '".$DBCONN->escapeSimple($rid)."' AND m_gedfile='".$DBCONN->escapeSimple($GEDCOMS[$gedfile]["id"])."'";
	$res = dbquery($sql);
	
	if ($res->numRows()!=0) {
		$row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
		$medialist[$rid]["ext"] = $row["m_ext"];
		$medialist[$rid]["title"] = $row["m_titl"];
		$medialist[$rid]["file"] = $row["m_file"];
		$medialist[$rid]["gedcom"] = $row["m_gedrec"];
		$medialist[$rid]["gedfile"] = $row["m_gedfile"];
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
	global $GEDCOM, $TBLPREFIX, $GEDCOMS, $DBTYPE, $DBCONN;
	$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY i_id";
	if ($DBTYPE!="sqlite") $sql.=" LIMIT 1";
	$row =& dbgetrow($sql);
	return $row[0];
}

//=================== IMPORT FUNCTIONS ======================================

/**
 * import record into database
 *
 * this function will parse the given gedcom record and add it to the database
 * @param string $indirec the raw gedcom record to parse
 * @param boolean $update whether or not this is an updated record that has been accepted
 */
function import_record($indirec, $update=false) {
	global $DBCONN, $gid, $type, $indilist,$famlist,$sourcelist,$otherlist, $TOTAL_QUERIES, $prepared_statement;
	global $TBLPREFIX, $GEDCOM_FILE, $FILE, $pgv_lang, $USE_RIN, $CREATE_GENDEX, $gdfp, $placecache;
	global $ALPHABET_upper, $ALPHABET_lower, $place_id, $WORD_WRAPPED_NOTES, $GEDCOMS, $media_count;
	global $MAX_IDS, $fpnewged, $GEDCOM;

	$FILE = $GEDCOM;
	
	//-- import different types of records
	$ct = preg_match("/0 @(.*)@ ([a-zA-Z_]+)/", $indirec, $match);
	if ($ct > 0) {
		$gid = $match[1];
		$type = trim($match[2]);
	}
	else {
		$ct = preg_match("/0 (.*)/", $indirec, $match);
		if ($ct>0) {
			$gid = trim($match[1]);
			$type = trim($match[1]);
		}
		else {
			print $pgv_lang["invalid_gedformat"]; print "<br /><pre>$indirec</pre>\n";
		}
	}
	//-- keep track of the max id for each type as they are imported
	if (!isset($MAX_IDS)) $MAX_IDS = array();
	$idnum = 0;
	$ct = preg_match("/(\d+)/", $gid, $match);
	if ($ct>0) $idnum = $match[1];
	if (!isset($MAX_IDS[$type])) $MAX_IDS[$type] = $idnum;
	else if ($MAX_IDS[$type]<$idnum) $MAX_IDS[$type] = $idnum;

	//-- remove double @ signs
	$indirec = preg_replace("/@+/", "@", $indirec);

	// remove heading spaces
	$indirec = preg_replace("/\n(\s*)/", "\n", $indirec);

	//-- if this is an import from an online update then import the places
	if ($update) {
		update_places($gid, $indirec, $update);
		update_dates($gid, $indirec);
	}
	
	$indirec = update_media($gid, $indirec, $update);
	
	if ($type == "INDI") {
		$indirec = cleanup_tags_y($indirec);
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$sfams = "";
		for($j=0; $j<$ct; $j++) {
			$sfams .= $match[$j][1].";";
		}
		$ct = preg_match_all("/1 FAMC @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$cfams = "";
		for($j=0; $j<$ct; $j++) {
			$cfams .= $match[$j][1].";";
		}
		$isdead = -1;
		$indi = array();
		$names = get_indi_names($indirec, true);
		$j=0;
		foreach($names as $indexval => $name) {
			if ($j>0) {
				$sql = "INSERT INTO ".$TBLPREFIX."names VALUES('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."','".$DBCONN->escapeSimple($name[0])."','".$DBCONN->escapeSimple($name[1])."','".$DBCONN->escapeSimple($name[2])."','".$DBCONN->escapeSimple($name[3])."')";
				$res = dbquery($sql);
				
			}
			$j++;
		}
		$indi["names"] = $names;
		$indi["isdead"] = $isdead;
		$indi["gedcom"] = $indirec;
		$indi["gedfile"] = $GEDCOMS[$FILE]["id"];
		if ($USE_RIN) {
			$ct = preg_match("/1 RIN (.*)/", $indirec, $match);
			if ($ct>0) $rin = trim($match[1]);
			else $rin = $gid;
			$indi["rin"] = $rin;
		}
		else $indi["rin"] = $gid;

		$sql = "INSERT INTO ".$TBLPREFIX."individuals VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($indi["gedfile"])."','".$DBCONN->escapeSimple($indi["rin"])."','".$DBCONN->escapeSimple($names[0][0])."',-1,'".$DBCONN->escapeSimple($indi["gedcom"])."','".$DBCONN->escapeSimple($names[0][1])."','".$DBCONN->escapeSimple($names[0][2])."')";
		$res = dbquery($sql);
		
		//-- PEAR supports prepared statements in mysqli we will use this code instead of the code above
		//if (!isset($prepared_statement)) $prepared_statement = $DBCONN->prepare("INSERT INTO ".$TBLPREFIX."individuals VALUES (?,?,?,?,?,?,?,?)");
		//$data = array($DBCONN->escapeSimple($gid), $DBCONN->escapeSimple($indi["file"]), $indi["rin"], $names[0][0], -1, $indi["gedcom"], $DBCONN->escapeSimple($names[0][1]), $names[0][2]);
		//$res =& $DBCONN->execute($prepared_statement, $data);
		//$TOTAL_QUERIES++;
		if (DB::isError($res)) {
		   // die(__LINE__." ".__FILE__."  ".$res->getMessage());
		}
	}
	else if ($type == "FAM") {
		$indirec = cleanup_tags_y($indirec);
		$parents = array();
		$ct = preg_match("/1 HUSB @(.*)@/", $indirec, $match);
		if ($ct>0) $parents["HUSB"]=$match[1];
		else $parents["HUSB"]=false;
		$ct = preg_match("/1 WIFE @(.*)@/", $indirec, $match);
		if ($ct>0) $parents["WIFE"]=$match[1];
		else $parents["WIFE"]=false;
		$ct = preg_match_all("/\d CHIL @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		$chil = "";
		for($j=0; $j<$ct; $j++) {
			$chil .= $match[$j][1].";";
		}
		$fam = array();
		$fam["HUSB"] = $parents["HUSB"];
		$fam["WIFE"] = $parents["WIFE"];
		$fam["CHIL"] = $chil;
		$fam["gedcom"] = $indirec;
		$fam["gedfile"] = $GEDCOMS[$FILE]["id"];
		//$famlist[$gid] = $fam;
		$sql = "INSERT INTO ".$TBLPREFIX."families (f_id, f_file, f_husb, f_wife, f_chil, f_gedcom, f_numchil) VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($fam["gedfile"])."','".$DBCONN->escapeSimple($fam["HUSB"])."','".$DBCONN->escapeSimple($fam["WIFE"])."','".$DBCONN->escapeSimple($fam["CHIL"])."','".$DBCONN->escapeSimple($fam["gedcom"])."','".$DBCONN->escapeSimple($ct)."')";
		$res = dbquery($sql);
		
	}
	else if ($type=="SOUR") {
		$et = preg_match("/1 ABBR (.*)/", $indirec, $smatch);
		if ($et>0) $name = $smatch[1];
		$tt = preg_match("/1 TITL (.*)/", $indirec, $smatch);
		if ($tt>0) $name = $smatch[1];
		if (empty($name)) $name = $gid;
		$subindi = preg_split("/1 TITL /",$indirec);
		if (count($subindi)>1) {
			$pos = strpos($subindi[1], "\n1", 0);
			if ($pos) $subindi[1] = substr($subindi[1],0,$pos);
			$ct = preg_match_all("/2 CON[C|T] (.*)/", $subindi[1], $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$name = trim($name);
				if ($WORD_WRAPPED_NOTES) $name .= " ".$match[$i][1];
				else $name .= $match[$i][1];
			}
		}
		$sql = "INSERT INTO ".$TBLPREFIX."sources VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."','".$DBCONN->escapeSimple($name)."','".$DBCONN->escapeSimple($indirec)."')";
		$res = dbquery($sql);
		
	}
	else if ($type=="OBJE") {
		//-- don't duplicate OBJE records
		//-- OBJE records are imported by update_media function
	}
	else if (preg_match("/_/", $type)==0) {
		if ($type=="HEAD") {
			$ct=preg_match("/1 DATE (.*)/", $indirec, $match);
			if ($ct == 0) {
				$indirec = trim($indirec);
				$indirec .= "\r\n1 DATE ".date("d")." ".date("M")." ".date("Y");
			}
		}
		$sql = "INSERT INTO ".$TBLPREFIX."other VALUES ('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."','".$DBCONN->escapeSimple($type)."','".$DBCONN->escapeSimple($indirec)."')";
		$res = dbquery($sql);
		
	}
	
	//-- if this is not an update then write it to the new gedcom file
	if (!$update && !empty($fpnewged)) fwrite($fpnewged, trim($indirec)."\r\n");
	return $gid;
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
	global $TBLPREFIX, $USE_RIN, $indilist, $DBCONN;
	$isdead = 0;
	$isdead = is_dead($indi["gedcom"]);
	if (empty($isdead)) $isdead = 0;
	$sql = "UPDATE ".$TBLPREFIX."individuals SET i_isdead=$isdead WHERE i_id LIKE '".$DBCONN->escapeSimple($gid)."' AND i_file='".$DBCONN->escapeSimple($indi["gedfile"])."'";
	$res = dbquery($sql);
	
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
	global $TBLPREFIX, $GEDCOMS, $GEDCOM, $DBCONN;
	
	$sql = "UPDATE ".$TBLPREFIX."individuals SET i_isdead=-1 WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	dbquery($sql);
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
	global $TBLPREFIX, $USE_RIN, $indilist, $FILE, $DBCONN, $GEDCOMS;

	$sql = "INSERT INTO ".$TBLPREFIX."names VALUES('".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."','".$DBCONN->escapeSimple($newname)."','".$DBCONN->escapeSimple($letter)."','".$DBCONN->escapeSimple($surname)."','C')";
	$res = dbquery($sql);


	$sql = "UPDATE ".$TBLPREFIX."individuals SET i_gedcom='".$DBCONN->escapeSimple($indirec)."' WHERE i_id='".$DBCONN->escapeSimple($gid)."' AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."'";
	$res = dbquery($sql);


	$indilist[$gid]["names"][] = array($newname, $letter, $surname, 'C');
	$indilist[$gid]["gedcom"] = $indirec;
}

/**
 * extract all places from the given record and insert them
 * into the places table
 * @param string $indirec
 */
function update_places($gid, $indirec, $update=false) {
	global $FILE, $placecache, $TBLPREFIX, $DBCONN, $GEDCOMS;

	if (!isset($placecache)) $placecache = array();
	//-- import all place locations
	$pt = preg_match_all("/\d PLAC (.*)/", $indirec, $match, PREG_SET_ORDER);
	for($i=0; $i<$pt; $i++) {
		$place = trim($match[$i][1]);
		$places = preg_split("/,/", $place);
		$secalp = array_reverse($places);
		$parent_id = 0;
		$level = 0;
		foreach($secalp as $indexval => $place) {
			$place = trim($place);
			$place=preg_replace('/\\\"/', "", $place);
			$place=preg_replace("/[\><]/", "", $place);
			if (empty($parent_id)) $parent_id=0;
			$key = strtolower($place."_".$level."_".$parent_id);
			$addgid = true;
			if (isset($placecache[$key])) {
				$parent_id = $placecache[$key][0];
				if (strpos($placecache[$key][1], $gid.",")===false) {
					$placecache[$key][1] = "$gid,".$placecache[$key][1];
					$sql = "INSERT INTO ".$TBLPREFIX."placelinks VALUES($parent_id, '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."')";
					$res = dbquery($sql);
					
				}
			}
			else {
				$skip = false;
				if ($update) {
					$sql = "SELECT p_id FROM ".$TBLPREFIX."places WHERE p_place LIKE '".$DBCONN->escapeSimple($place)."' AND p_level=$level AND p_parent_id='$parent_id' AND p_file='".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."'";
					$res = dbquery($sql);
					
					if ($res->numRows()>0) {
						$row = $res->fetchRow(DB_FETCHMODE_ASSOC);
						$res->free();
						$parent_id = $row["p_id"];
						$skip=true;
						$placecache[$key] = array($parent_id, $gid.",");
						$sql = "INSERT INTO ".$TBLPREFIX."placelinks VALUES($parent_id, '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."')";
						$res = dbquery($sql);
						
					}
				}
				if (!$skip) {
					//if (!isset($place_id)) {
						$place_id = get_next_id("places", "p_id");
					//}
					//else $place_id++;
					$sql = "INSERT INTO ".$TBLPREFIX."places VALUES($place_id, '".$DBCONN->escapeSimple($place)."', $level, '$parent_id', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."')";
					$res = dbquery($sql);
					
					$parent_id = $place_id;
					$placecache[$key] = array($parent_id, $gid.",");
					$sql = "INSERT INTO ".$TBLPREFIX."placelinks VALUES($place_id, '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."')";
					$res = dbquery($sql);
					
				}
			}
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
	global $FILE, $TBLPREFIX, $DBCONN, $GEDCOMS;

	$count = 0;
	$pt = preg_match("/\d DATE (.*)/", $indirec, $match);
	if ($pt==0) return 0;
	$facts = get_all_subrecords($indirec, "", false, false, false);
	foreach($facts as $f=>$factrec) {
		$fact = "EVEN";
		$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		 if ($ft>0) {
			  $fact = trim($match[1]);
			  $event = trim($match[2]);
		 }
		$pt = preg_match_all("/2 DATE (.*)/", $factrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$pt; $i++) {
			$datestr = trim($match[$i][1]);
			$date = parse_date($datestr);
			if (empty($date[0]["day"])) $date[0]["day"] = 0;
			if (empty($date[0]["mon"])) $date[0]["mon"] = 0;
			if (empty($date[0]["year"])) $date[0]["year"] = 0;
			$datestamp = $date[0]['year'];
			if ($date[0]['mon']<10) $datestamp .= '0';
			$datestamp .= (int)$date[0]['mon'];
			if ($date[0]['day']<10) $datestamp .= '0';
			$datestamp .= (int)$date[0]['day']; 
			$sql = "INSERT INTO ".$TBLPREFIX."dates VALUES('".$DBCONN->escapeSimple($date[0]["day"])."','".$DBCONN->escapeSimple(str2upper($date[0]["month"]))."','".$DBCONN->escapeSimple($date[0]["mon"])."','".$DBCONN->escapeSimple($date[0]["year"])."','".$DBCONN->escapeSimple($datestamp)."','".$DBCONN->escapeSimple($fact)."','".$DBCONN->escapeSimple($gid)."','".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."',";
			if (isset($date[0]["ext"])) {
				preg_match("/@#D(.*)@/", $date[0]["ext"], $extract_type);
				$date_types = array("@#DGREGORIAN@","@#DJULIAN@","@#DHEBREW@","@#DFRENCH R@", "@#DROMAN@", "@#DUNKNOWN@");
				if (isset($extract_type[0]) && in_array($extract_type[0], $date_types)) $sql .= "'".$extract_type[0]."')";
				else $sql .= "NULL)";
			}
			else $sql .= "NULL)";
			$res = dbquery($sql);
			
			$count++;
		}
	}
	return $count;
}

/**
 * check if the given Media object is in the objectlist
 * @param Media $obje
 * @return mixed  returns the ID for the for the matching media or false if not found
 */
function in_obje_list(&$obje) {
	/* -- I changed my mind and decided that this method
	 * was going to be too slow and use up too much memory
	 * and since we don't want to serialize the $objelist into the
	 * session, it won't work accross import pauses
	 * probably better just to go ahead and make an SQL query
	 * SQL queries are expensive in time... but probably faster
	 * in the long run in this case
	 global $objelist;
	
	if (!isset($objelist)) $objelist = array();
	if (is_null($obje)) return false;
	
	foreach($objelist as $id=>$obj) {
		//print $obj->file."==".$obje->file."<br />";
		if ($obj->equals($obje)) return $id;
	}
	*/
	global $TBLPREFIX, $GEDCOMS, $GEDCOM, $FILE, $DBCONN;
	
	if (is_null($obje)) return false;
	$sql = "SELECT m_media FROM ".$TBLPREFIX."media WHERE m_file='".$DBCONN->escapeSimple($obje->file)."' AND m_titl LIKE '".$DBCONN->escapeSimple($obje->title)."' AND m_gedfile=".$GEDCOMS[$FILE]['id'];
	$res = dbquery($sql);

	if ($res->numRows()>0) {
		$row = $res->fetchRow();
		return $row[0];
	}
	
	return false;
}

/**
 * import media items from record
 * @todo Decide whether or not to update the original gedcom file
 * @return string	an updated record
 */
function update_media($gid, $indirec, $update=false) {
	global $GEDCOMS, $FILE, $TBLPREFIX, $DBCONN, $MEDIA_ID_PREFIX, $media_count, $found_ids;
	global $zero_level_media, $fpnewged, $objelist, $MAX_IDS;
	
	if (!isset($media_count)) $media_count = 0;
	if (!isset($found_ids)) $found_ids = array();
	if (!isset($zero_level_media)) $zero_level_media = false;
	if (!$update && !isset($MAX_IDS["OBJE"])) $MAX_IDS["OBJE"] = 1;
	
	//-- handle level 0 media OBJE seperately
	$ct = preg_match("/0 @(.*)@ OBJE/", $indirec, $match);
	if ($ct>0) {
		$old_m_media = $match[1];
		$m_id = get_next_id("media", "m_id");
		if ($update) {
			$new_m_media = $old_m_media;
		}
		else {
			if (isset($found_ids[$old_m_media])) {
				$new_m_media = $found_ids[$old_m_media]["new_id"];
			}
			else {
				$new_m_media = get_new_xref("OBJE");
				$found_ids[$old_m_media]["old_id"] = $old_m_media;
				$found_ids[$old_m_media]["new_id"] = $new_m_media;
			}
		}
		$indirec = preg_replace("/@".$old_m_media."@/", "@".$new_m_media."@", $indirec);
		$media = new Media($indirec);
		//--check if we already have a similar object
		$new_media = in_obje_list($media);
		if ($new_media===false) {
			$objelist[$new_m_media] = $media;
			$sql = "INSERT INTO ".$TBLPREFIX."media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
			$sql .= " VALUES('".$DBCONN->escapeSimple($m_id)."', '".$DBCONN->escapeSimple($new_m_media)."', '".$DBCONN->escapeSimple($media->ext)."', '".$DBCONN->escapeSimple($media->title)."', '".$DBCONN->escapeSimple($media->file)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."', '".$DBCONN->escapeSimple($indirec)."')";
			$res = dbquery($sql);
		}
		else {
			$new_m_media = $new_media;
			$found_ids[$old_m_media]["old_id"] = $old_m_media;
			$found_ids[$old_m_media]["new_id"] = $new_media;
		}
		return $indirec;
	}
	
	//-- check to see if there are any media records
	//-- if there aren't any media records then don't look for them just return
	$pt = preg_match("/\d OBJE/", $indirec, $match);
	if ($pt==0) return $indirec;
	
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
	foreach($lines as $key => $line) {
		if (!empty($line)) {
			// NOTE: Match lines that resemble n OBJE @0000@
			// NOTE: Renumber the old ID to a new ID and save the old ID
			// NOTE: in case there are more references to it
			if (preg_match("/[1-9]\sOBJE\s@(.*)@/", $line, $match) != 0) {
				// NOTE: Check if objlevel greater is than 0, if so then store the current object record
				if ($objlevel > 0) {
					$m_media = get_new_xref("OBJE");
					$objrec = preg_replace("/ OBJE/", " @".$m_media."@ OBJE", $objrec);
					$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
					$media = new Media($objrec);
					$new_media = in_obje_list($media);
					if ($new_media===false) {
						$m_id = get_next_id("media", "m_id");
						$sql = "INSERT INTO ".$TBLPREFIX."media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
						$sql .= " VALUES('".$DBCONN->escapeSimple($m_id)."', '".$DBCONN->escapeSimple($m_media)."', '".$DBCONN->escapeSimple($media->ext)."', '".$DBCONN->escapeSimple($media->title)."', '".$DBCONN->escapeSimple($media->file)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."', '".$DBCONN->escapeSimple($objrec)."')";
						$res = dbquery($sql);
						//-- if this is not an update then write it to the new gedcom file
						if (!$update && !empty($fpnewged)) fwrite($fpnewged, trim($objrec)."\r\n");
						//print "LINE ".__LINE__;
						$objelist[$m_media] = $media;
					}
					else $m_media = $new_media;
					$mm_id = get_next_id("media_mapping", "mm_id");
					$sql = "INSERT INTO ".$TBLPREFIX."media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec)";
					$sql .= " VALUES ('".$DBCONN->escapeSimple($mm_id)."', '".$DBCONN->escapeSimple($m_media)."', '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($count)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]['id'])."', '".addslashes(''.$objlevel.' OBJE @'.$m_media.'@')."')";
					$res = dbquery($sql);
					$media_count++;
					$count++;
					// NOTE: Add the new media object to the record
					$newrec .= $objlevel." OBJE @".$m_media."@\r\n";
					
					// NOTE: Set the details for the next media record
					$objlevel = $match[0]{0};
					$inobj = true;
					$objrec = $line."\r\n";
				}
				else {
					// NOTE: Set object level
					$objlevel = $match[0]{0};
					$inobj = true;
					$objrec = $line."\r\n";
				}
				
				// NOTE: Retrieve the old media ID
				$old_mm_media = $match[1];
				
				//-- use the old id if we are updating from an online edit
				if ($update) {
					$new_mm_media = $old_mm_media;
				}
				else {
					// 	NOTE: Check if the id already exists and there is a value behind OBJE (n OBJE @M001@)
					if (!isset($found_ids[$old_mm_media]) && !empty($match[1])) {
						// NOTE: Get a new media ID
						$new_mm_media = get_new_xref("OBJE");
					}
					else {
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
				//$sql = "INSERT INTO ".$TBLPREFIX."media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec) VALUES('".$DBCONN->escapeSimple($m_id)."', '".$DBCONN->escapeSimple($new_mm_media)."', '', '', '', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."', '')";
				//$res = dbquery($sql);
				$mm_id = get_next_id("media_mapping", "mm_id");
				$sql = "INSERT INTO ".$TBLPREFIX."media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec) VALUES ('".$DBCONN->escapeSimple($mm_id)."', '".$DBCONN->escapeSimple($new_mm_media)."', '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($count)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]['id'])."', '".$line."')";
				$res =& dbquery($sql);
				//print "LINE ".__LINE__;
				$media_count++;
				$count++;
				$objlevel = 0;
				$objrec = "";
				$inobj = false;
			}
			else if (preg_match("/[1-9]\sOBJE/", $line, $match)) {
				if (!empty($objrec)) {
					$m_id = get_next_id("media", "m_id");
					$m_media = get_new_xref("OBJE");
					$objrec = preg_replace("/ OBJE/", " @".$m_media."@ OBJE", $objrec);
					$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
					$media = new Media($objrec);
					$new_media = in_obje_list($media);
					if ($new_media===false) {
						$sql = "INSERT INTO ".$TBLPREFIX."media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
						$sql .= " VALUES('".$DBCONN->escapeSimple($m_id)."', '".$DBCONN->escapeSimple($m_media)."', '".$DBCONN->escapeSimple($media->ext)."', '".$DBCONN->escapeSimple($media->title)."', '".$DBCONN->escapeSimple($media->file)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."', '".$DBCONN->escapeSimple($objrec)."')";
						$res = dbquery($sql);
						//-- if this is not an update then write it to the new gedcom file
						if (!$update && !empty($fpnewged)) fwrite($fpnewged, trim($objrec)."\r\n");
						//print "LINE ".__LINE__;
						$objelist[$m_media] = $media;
					}
					else $m_media = $new_media;
					$mm_id = get_next_id("media_mapping", "mm_id");
					$sql = "INSERT INTO ".$TBLPREFIX."media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec)";
					$sql .= " VALUES ('".$DBCONN->escapeSimple($mm_id)."', '".$DBCONN->escapeSimple($m_media)."', '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($count)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]['id'])."', '".addslashes(''.$objlevel.' OBJE @'.$m_media.'@')."')";
					$res = dbquery($sql);
					$media_count++;
					$count++;
					// NOTE: Add the new media object to the record
					$newrec .= $objlevel." OBJE @".$m_media."@\r\n";
				}
				// NOTE: Set the details for the next media record
				$objlevel = $match[0]{0};
				$inobj = true;
				$objrec = $line."\r\n";
			}
			else {
				$ct = preg_match("/(\d+)\s(\w+)(.*)/", $line, $match);
				if ($ct > 0) {
					$level = $match[1];
					$fact = $match[2];
					$desc = trim($match[3]);
					if ($inobj && ($level<=$objlevel || $key == $ct_lines-1)) {
						if ($key == $ct_lines-1 && $level>$objlevel) {
							$objrec .= $line."\r\n";
						}
						$m_id = get_next_id("media", "m_id");
						if ($objrec{0} != 0) {
							$m_media = get_new_xref("OBJE");
							$objrec = preg_replace("/ OBJE/", " @".$m_media."@ OBJE", $objrec);
							$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);
							$media = new Media($objrec);
							$new_media = in_obje_list($media);
							if ($new_media===false) {
								$sql = "INSERT INTO ".$TBLPREFIX."media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec)";
								$sql .= " VALUES('".$DBCONN->escapeSimple($m_id)."', '".$DBCONN->escapeSimple($m_media)."', '".$DBCONN->escapeSimple($media->ext)."', '".$DBCONN->escapeSimple($media->title)."', '".$DBCONN->escapeSimple($media->file)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]["id"])."', '".$DBCONN->escapeSimple($objrec)."')";
								$res = dbquery($sql);
								//-- if this is not an update then write it to the new gedcom file
								if (!$update && !empty($fpnewged)) fwrite($fpnewged, trim($objrec)."\r\n");
								//print "LINE ".__LINE__;
								$objelist[$m_media] = $media;
							}
							else $m_media = $new_media;
							$mm_id = get_next_id("media_mapping", "mm_id");
							$sql = "INSERT INTO ".$TBLPREFIX."media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec)";
							$sql .= " VALUES ('".$DBCONN->escapeSimple($mm_id)."', '".$DBCONN->escapeSimple($m_media)."', '".$DBCONN->escapeSimple($gid)."', '".$DBCONN->escapeSimple($count)."', '".$DBCONN->escapeSimple($GEDCOMS[$FILE]['id'])."', '".addslashes(''.$level.' OBJE @'.$m_media.'@')."')";
							$res = dbquery($sql);
						}
						//-- what is this for?  it shouldn't be used anymore because of code above
						/*
						else {
							$oldid = preg_match("/0\s@(.*)@\sOBJE/", $objrec, $newmatch);
							$m_media = $newmatch[1];
							$sql = "UPDATE ".$TBLPREFIX."media SET m_ext = '".$DBCONN->escapeSimple($ext)."', m_titl = '".$DBCONN->escapeSimple($title)."', m_file = '".$DBCONN->escapeSimple($file)."', m_gedrec = '".$DBCONN->escapeSimple($objrec)."' WHERE m_media = '".$m_media."'";
							$res = dbquery($sql);
							//print "LINE ".__LINE__;
						}
						*/
						
						$media_count++;
						$count++;
						$objrec = "";
						$newrec .= $objlevel." OBJE @".$m_media."@\r\n";
						$inobj = false;
						$objlevel = 0;
					}
					else {
						if ($inobj) $objrec .= $line."\r\n";
					}
					if ($fact=="OBJE") {
						$inobj = true;
						$objlevel = $level;
						$objrec = "";
					}
				}
			}
			if (!$inobj) $newrec .= $line."\r\n";
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
// Provided by bitweaver package management
}

/**
 * delete a gedcom from the database
 *
 * deletes all of the imported data about a gedcom from the database
 * @param string $FILE	the gedcom to remove from the database
 */
function empty_database($FILE) {
	global $TBLPREFIX, $DBCONN, $GEDCOMS;

	$FILE = $DBCONN->escapeSimple($GEDCOMS[$FILE]["id"]);
	$sql = "DELETE FROM ".$TBLPREFIX."individuals WHERE i_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."families WHERE f_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."sources WHERE s_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."other WHERE o_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."places WHERE p_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."placelinks WHERE pl_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."names WHERE n_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."dates WHERE d_file='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."media WHERE m_gedfile='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='$FILE'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."nextid WHERE ni_gedfile='$FILE'";
	$res = dbquery($sql);
	
}

/**
 * perform any database cleanup
 *
 * during the import process it might be necessary to cleanup some database values.  In index mode
 * the file handles need to be closed.  For database mode we probably don't need to do anything in
 * this funciton.
 */
function cleanup_database() {
	global $DBTYPE, $DBCONN, $TBLPREFIX, $MAX_IDS, $GEDCOMS, $FILE;
	/*-- commenting out as it seems to cause more problems than it helps
	$sql = "UNLOCK TABLES";
	$res = dbquery($sql);
	
	*/
	//-- end the transaction
	if (isset($MAX_IDS)) {
		foreach($MAX_IDS as $type=>$id) {
			$sql = "INSERT INTO ".$TBLPREFIX."nextid VALUES('".$DBCONN->escapeSimple($id+1)."', '".$DBCONN->escapeSimple($type)."', '".$GEDCOMS[$FILE]["id"]."')";
			$res = dbquery($sql);
		}
	}
	$sql = "COMMIT";
	$res = dbquery($sql);
	
	if (preg_match("/mysql|pgsql/", $DBTYPE)>0) $DBCONN->autoCommit(false);
	RETURN;
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
	global $TBLPREFIX, $DBCONN;

	$sourcelist = array();

 	$sql = "SELECT s_id, s_file, s_file as s_name, s_gedcom FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' and ((s_gedcom LIKE '% _HEB %') || (s_gedcom LIKE '% ROMN %'));";

	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$row = db_cleanup($row);
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
	global $TBLPREFIX, $DBCONN;

	$sourcelist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY s_name";
	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["name"] = $row["s_name"];
		$source["gedcom"] = $row["s_gedcom"];
		$row = db_cleanup($row);
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
	global $TBLPREFIX, $DBCONN;

	$repolist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND o_type='REPO'";
	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$tt = preg_match("/1 NAME (.*)/", $row["o_gedcom"], $match);
		if ($tt == "0") $name = $row["o_id"]; else $name = $match[1];
		$repo["id"] = "@".$row["o_id"]."@";
		$repo["gedfile"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$repo["gedcom"] = $row["o_gedcom"];
		$row = db_cleanup($row);
		$repolist[$name]= $repo;
	}
	$res->free();
	ksort($repolist);
	return $repolist;
}

//-- get the repositorylist from the datastore
function get_repo_id_list() {
	global $GEDCOM, $GEDCOMS;
	global $TBLPREFIX, $DBCONN;

	$repo_id_list = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND o_type='REPO' ORDER BY o_id";
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
		$row = db_cleanup($row);
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
	global $TBLPREFIX, $DBCONN;

	$repolist = array();

 	$sql = "SELECT o_id, o_file, o_file as o_name, o_type, o_gedcom FROM ".$TBLPREFIX."other WHERE o_type='REPO' AND o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' and ((o_gedcom LIKE '% _HEB %') || (o_gedcom LIKE '% ROMN %'));";

	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$repo = array();
		$repo["gedcom"] = $row["o_gedcom"];
		$ct = preg_match("/\d ROMN (.*)/", $row["o_gedcom"], $match);
 		if ($ct==0) $ct = preg_match("/\d _HEB (.*)/", $row["o_gedcom"], $match);
		$repo["name"] = $match[1];
		$repo["id"] = "@".$row["o_id"]."@";
		$repo["gedfile"] = $row["o_file"];
		$repo["type"] = $row["o_type"];
		$row = db_cleanup($row);
		$repolist[$match[1]] = $repo;

	}
	$res->free();
	return $repolist;
}

//-- get the indilist from the datastore
function get_indi_list() {
	global $indilist, $GEDCOM, $DBCONN, $GEDCOMS;
	global $TBLPREFIX, $INDILIST_RETRIEVED;

	if ($INDILIST_RETRIEVED) return $indilist;
	$indilist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY i_surname";
	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$indi = array();
		$indi["gedcom"] = $row["i_gedcom"];
		$row = db_cleanup($row);
		$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "A"));
		$indi["isdead"] = $row["i_isdead"];
		$indi["gedfile"] = $row["i_file"];
		$indilist[$row["i_id"]] = $indi;
	}
	$res->free();

	$sql = "SELECT * FROM ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY n_surname";
	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
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
	global $assolist, $GEDCOM, $DBCONN, $GEDCOMS;
	global $TBLPREFIX, $ASSOLIST_RETRIEVED;

	if ($ASSOLIST_RETRIEVED) return $assolist;
	$assolist = array();

	$oldged = $GEDCOM;
	if (($type == "all") || ($type == "fam")) {
		$sql = "SELECT f_id, f_file, f_gedcom, f_husb, f_wife FROM ".$TBLPREFIX."families WHERE f_gedcom LIKE '% ASSO %'";
		$res = dbquery($sql);
		
		$ct = $res->numRows();
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
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
			$row = db_cleanup($row);
		}
		$res->free();
	}

	if (($type == "all") || ($type == "indi")) {
		$sql = "SELECT i_id, i_file, i_gedcom FROM ".$TBLPREFIX."individuals WHERE i_gedcom LIKE '% ASSO %'";
		$res = dbquery($sql);
		
		$ct = $res->numRows();
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
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
			$row = db_cleanup($row);
		}
		$res->free();
	}
	
	$GEDCOM = $oldged;

	$ASSOLIST_RETRIEVED = true;
	return $assolist;
}

//-- get the famlist from the datastore
function get_fam_list() {
	global $famlist, $GEDCOM, $indilist, $DBCONN, $GEDCOMS;
	global $TBLPREFIX, $FAMLIST_RETRIEVED;

	if ($FAMLIST_RETRIEVED) return $famlist;
	$famlist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);
	
	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$fam = array();
		$fam["gedcom"] = $row["f_gedcom"];
		$row = db_cleanup($row);
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
	global $TBLPREFIX;

	$otherlist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["o_gedcom"];
		$row = db_cleanup($row);
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
	global $TBLPREFIX;

	$medialist = array();

	$sql = "SELECT * FROM ".$TBLPREFIX."media WHERE m_gedfile='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	$ct = $res->numRows();
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$source = array();
		$source["gedcom"] = $row["m_gedrec"];
		$row = db_cleanup($row);
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
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	$myindilist = array();
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term = "LIKE";
	//-- if the query is a string
	if (!is_array($query)) {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (";
		//-- make sure that MySQL matches the upper and lower case utf8 characters
		if (has_utf8($query)) $sql .= "i_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR i_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
		else $sql .= "i_gedcom $term '".$DBCONN->escapeSimple($query)."')";
	}
	//-- create a more complicated query if it is an array
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			if (has_utf8($q)) $sql .= "(i_gedcom $term '".$DBCONN->escapeSimple(str2upper($q))."' OR i_gedcom $term '".$DBCONN->escapeSimple(str2lower($q))."')";
			else $sql .= "(i_gedcom $term '".$DBCONN->escapeSimple($q)."')";
			$i++;
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "i_file='".$DBCONN->escapeSimple($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	$res = dbquery($sql);

	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()){
			$row = db_cleanup($row);
			if (count($allgeds) > 1) {
				$myindilist[$row[0]."[".$row[2]."]"]["names"] = get_indi_names($row[3]);
				$myindilist[$row[0]."[".$row[2]."]"]["gedfile"] = $row[2];
				$myindilist[$row[0]."[".$row[2]."]"]["gedcom"] = $row[3];
				$myindilist[$row[0]."[".$row[2]."]"]["isdead"] = $row[4];
				if ($myindilist[$row[0]."[".$row[2]."]"]["gedfile"] == $GEDCOM) $indilist[$row[0]] = $myindilist[$row[0]."[".$row[2]."]"];
			}
			else {
				$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
				$myindilist[$row[0]]["gedfile"] = $row[2];
				$myindilist[$row[0]]["gedcom"] = $row[3];
				$myindilist[$row[0]]["isdead"] = $row[4];
				if ($myindilist[$row[0]]["gedfile"] == $GEDCOM) $indilist[$row[0]] = $myindilist[$row[0]];
			}
		}
		$res->free();
	}
	return $myindilist;
}

//-- search through the gedcom records for individuals in families
function search_indis_fam($add2myindilist) {
	global $TBLPREFIX, $GEDCOM, $indilist, $myindilist;

	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		if (isset($add2myindilist[$row[0]])){
			$add2my_fam=$add2myindilist[$row[0]];
			$row = db_cleanup($row);
			$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
			$myindilist[$row[0]]["gedfile"] = $row[2];
			$myindilist[$row[0]]["gedcom"] = $row[3].$add2my_fam;
			$myindilist[$row[0]]["isdead"] = $row[4];
			$indilist[$row[0]] = $myindilist[$row[0]];
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
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;

	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	
	$myindilist = array();
	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."dates WHERE i_id=d_gid AND i_file=d_file AND d_fact NOT IN('CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL') AND ";
	$sql .= "d_datestamp >= ".$startyear."0000 AND d_datestamp<".($endyear+1)."0000";
	/*
	$i=$startyear;
	while($i <= $endyear) {
		if ($i > $startyear) $sql .= " OR ";
		if ($REGEXP_DB) $sql .= "i_gedcom $term '".$DBCONN->escapeSimple("2 DATE[^\n]* ".$i)."'";
		else $sql .= "i_gedcom LIKE '".$DBCONN->escapeSimple("%2 DATE%".$i)."%'";
		$i++;
	}
	$sql .= ")";
	*/
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
		$myindilist[$row[0]]["gedfile"] = $row[2];
		$myindilist[$row[0]]["gedcom"] = $row[3];
		$myindilist[$row[0]]["isdead"] = $row[4];
		$indilist[$row[0]] = $myindilist[$row[0]];
	}
	$res->free();
	return $myindilist;
}


//-- search through the gedcom records for individuals
function search_indis_names($query, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;

	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
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
	if (empty($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals";
	else if (!is_array($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE i_name $term '".$DBCONN->escapeSimple($query)."'";
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if (!empty($q)) {
				if ($i>0) $sql .= " AND ";
				$sql .= "i_name $term '".$DBCONN->escapeSimple($q)."'";
				$i++;
			}
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		if ($allgeds) $key = $row[0]."[".$row[2]."]";
		else $key = $row[0];
		if (isset($indilist[$key])) $myindilist[$key] = $indilist[$key];
		else {
			$myindilist[$key]["names"] = get_indi_names($row[3]);
			$myindilist[$key]["gedfile"] = $row[2];
			$myindilist[$key]["gedcom"] = $row[3];
			$myindilist[$key]["isdead"] = $row[4];
			if ($allgeds) $indilist[$key] = $myindilist[$key];
			else $indilist[$key] = $myindilist[$key];
		}
	}
	$res->free();
	if (!is_array($query)) $sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."names WHERE i_id=n_gid AND i_file=n_file AND n_name $term '".$DBCONN->escapeSimple($query)."'";
	else {
		$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."names WHERE i_id=n_gid AND i_file=n_file AND (";
		$i=0;
		foreach($query as $indexval => $q) {
			if (!empty($q)) {
				if ($i>0) $sql .= " AND ";
				$sql .= "n_name $term '".$DBCONN->escapeSimple($q)."'";
				$i++;
			}
		}
		$sql .= ")";
	}
	if (!$allgeds) $sql .= " AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		if ($allgeds) $key = $row[0]."[".$row[2]."]";
		else $key = $row[0];
		if (!isset($myindilist[$key])) {
			if (isset($indilist[$key])) $myindilist[$key] = $indilist[$key];
			else {
				$myindilist[$key]["names"] = get_indi_names($row[3]);
				$myindilist[$key]["gedfile"] = $row[2];
				$myindilist[$key]["gedcom"] = $row[3];
				$myindilist[$key]["isdead"] = $row[4];
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
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	
	$changes = array();
	while(strlen($year)<4) $year ='0'.$year;
	while(strlen($mon)<2) $mon ='0'.$mon;
	while(strlen($day)<2) $day ='0'.$day;
	$datestamp = $year.$mon.$day;
	$sql = "SELECT * FROM ".$TBLPREFIX."dates WHERE d_fact='CHAN' AND d_datestamp>=".$datestamp;
	if (!$allgeds) $sql .= " AND d_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= " ORDER BY d_datestamp DESC";
	//print $sql;
	$res = dbquery($sql);
	
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			$changes[] = $row;
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
	global $TBLPREFIX, $GEDCOM, $indilist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	$myindilist = array();
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	
	$sql = "SELECT i_id, i_name, i_file, i_gedcom, i_isdead, i_letter, i_surname, d_gid, d_fact FROM ".$TBLPREFIX."dates, ".$TBLPREFIX."individuals WHERE i_id=d_gid AND i_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$DBCONN->escapeSimple($day)."' ";
	if (!empty($month)) $sql .= "AND d_month='".$DBCONN->escapeSimple(str2upper($month))."' ";
	if (!empty($year)) $sql .= "AND d_year='".$DBCONN->escapeSimple($year)."' ";
	if (!empty($fact)) {
		$sql .= "AND (";
		$facts = preg_split("/[,:; ]/", $fact);
		$i=0;
		foreach($facts as $fact) {
			if ($i!=0) $sql .= " OR ";
			$ct = preg_match("/!(\w+)/", $fact, $match);
			if ($ct > 0) {
				$fact = $match[1];
				$sql .= "d_fact!='".$DBCONN->escapeSimple(str2upper($fact))."'";
			}
			else {
				$sql .= "d_fact='".$DBCONN->escapeSimple(str2upper($fact))."'";
			}
			$i++;
		}
		$sql .= ") ";
	}
	if (!$allgeds) $sql .= "AND d_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= "GROUP BY i_id ORDER BY d_year DESC, d_mon DESC, d_day DESC";
	//print $sql;
	$res = dbquery($sql);
	
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()){
			$row = db_cleanup($row);
			if ($allgeds) {
				$myindilist[$row[0]."[".$row[2]."]"]["names"] = get_indi_names($row[3]);
				$myindilist[$row[0]."[".$row[2]."]"]["gedfile"] = $row[2];
				$myindilist[$row[0]."[".$row[2]."]"]["gedcom"] = $row[3];
				$myindilist[$row[0]."[".$row[2]."]"]["isdead"] = $row[4];
				if ($myindilist[$row[0]."[".$row[2]."]"]["gedfile"] == $GEDCOMS[$GEDCOM]['id']) $indilist[$row[0]] = $myindilist[$row[0]."[".$row[2]."]"];
			}
			else {
				$myindilist[$row[0]]["names"] = get_indi_names($row[3]);
				$myindilist[$row[0]]["gedfile"] = $row[2];
				$myindilist[$row[0]]["gedcom"] = $row[3];
				$myindilist[$row[0]]["isdead"] = $row[4];
				if ($myindilist[$row[0]]["gedfile"] == $GEDCOMS[$GEDCOM]['id']) $indilist[$row[0]] = $myindilist[$row[0]];
			}
		}
		$res->free();
	}
	return $myindilist;
}

//-- search through the gedcom records for families
function search_fams($query, $allgeds=false, $ANDOR="AND", $allnames=false) {
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	$myfamlist = array();
	if (!is_array($query)) $sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families WHERE (f_gedcom $term '".$DBCONN->escapeSimple($query)."')";
	else {
		$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(f_gedcom $term '".$DBCONN->escapeSimple($q)."')";
			$i++;
		}
		$sql .= ")";
	}
	
	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0, $max=count($allgeds); $i<$max; $i++) {
			$sql .= "f_file='".$DBCONN->escapeSimple($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < $max-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	
	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$GEDCOM = get_gedcom_from_id($row[3]);
		if ($allnames == true) {
			$hname = get_sortable_name($row[1], "", "", true);
			$wname = get_sortable_name($row[2], "", "", true);
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
			$hname = get_sortable_name($row[1]);
			$wname = get_sortable_name($row[2]);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = $hname." + ".$wname;
		}
		if (count($allgeds) > 1) {
			$myfamlist[$row[0]."[".$row[3]."]"]["name"] = $name;
			$myfamlist[$row[0]."[".$row[3]."]"]["gedfile"] = $row[3];
			$myfamlist[$row[0]."[".$row[3]."]"]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]."[".$row[3]."]"];
		}
		else {
			$myfamlist[$row[0]]["name"] = $name;
			$myfamlist[$row[0]]["gedfile"] = $row[3];
			$myfamlist[$row[0]]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $myfamlist;
}

//-- search through the gedcom records for families
function search_fams_names($query, $ANDOR="AND", $allnames=false, $gedcnt=1) {
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	//if ($REGEXP_DB) $term = "REGEXP";
	//else $term = "LIKE";
	$myfamlist = array();
	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families WHERE (";
	$i=0;
	foreach($query as $indexval => $q) {
		if ($i>0) $sql .= " $ANDOR ";
		$sql .= "((f_husb='".$DBCONN->escapeSimple($q[0])."' OR f_wife='".$DBCONN->escapeSimple($q[0])."') AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$q[1]]["id"])."')";
		$i++;
	}
	$sql .= ")";

	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$GEDCOM = get_gedcom_from_id($row[3]);
		if ($allnames == true) {
			$hname = get_sortable_name($row[1], "", "", true);
			$wname = get_sortable_name($row[2], "", "", true);
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
			$hname = get_sortable_name($row[1]);
			$wname = get_sortable_name($row[2]);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = $hname." + ".$wname;
		}
		if ($gedcnt > 1) {
			$myfamlist[$row[0]."[".$row[3]."]"]["name"] = $name;
			$myfamlist[$row[0]."[".$row[3]."]"]["gedfile"] = $row[3];
			$myfamlist[$row[0]."[".$row[3]."]"]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]."[".$row[3]."]"];
		}
		else {
			$myfamlist[$row[0]]["name"] = $name;
			$myfamlist[$row[0]]["gedfile"] = $row[3];
			$myfamlist[$row[0]]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]];
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
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	$myfamlist = array();
	if (!is_array($query)) $sql = "SELECT f_id, f_husb, f_wife, f_file FROM ".$TBLPREFIX."families WHERE (f_husb='$query' OR f_wife='$query' OR f_chil LIKE '%$query;%')";
	else {
		$sql = "SELECT f_id, f_husb, f_wife, f_file FROM ".$TBLPREFIX."families WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(f_husb='$query' OR f_wife='$query' OR f_chil LIKE '%$query;%')";
			$i++;
		}
		$sql .= ")";
	}
	
	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0, $max=count($allgeds); $i<$max; $i++) {
			$sql .= "f_file='".$DBCONN->escapeSimple($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < $max-1) $sql .= " OR ";
		}
		$sql .= ")";
	}
	$res = dbquery($sql);
	
	$i=0;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		if ($allnames == true) {
			$hname = get_sortable_name($row[1], "", "", true);
			$wname = get_sortable_name($row[2], "", "", true);
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
			$hname = get_sortable_name($row[1]);
			$wname = get_sortable_name($row[2]);
			if (empty($hname)) $hname = "@N.N.";
			if (empty($wname)) $wname = "@N.N.";
			$name = $hname." + ".$wname;
		}		
		if (count($allgeds) > 1) {
			$myfamlist[$i]["name"] = $name;
			$myfamlist[$i]["gedfile"] = $row[0];
			$myfamlist[$i]["gedcom"] = $row[1];
			$famlist[] = $myfamlist;
		}
		else {
			$myfamlist[$i][] = $name;
			$myfamlist[$i][] = $row[0];
			$myfamlist[$i][] = $row[3];
			$i++;
			$famlist[] = $myfamlist;
		}
	}
	$res->free();
	return $myfamlist;
}

//-- search through the gedcom records for families with daterange
function search_fams_year_range($startyear, $endyear, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;

	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	
	$myfamlist = array();
	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom FROM ".$TBLPREFIX."families, ".$TBLPREFIX."dates WHERE f_id=d_gid AND f_file=d_file AND d_fact NOT IN('CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL') AND ";
	$sql .= "d_datestamp >= ".$startyear."0000 AND d_datestamp<".($endyear+1)."0000";
	/*
	$i=$startyear;
	while($i <= $endyear) {
		if ($i > $startyear) $sql .= " OR ";
		if ($REGEXP_DB) $sql .= "f_gedcom $term '".$DBCONN->escapeSimple("2 DATE[^\n]* ".$i)."'";
		else $sql .= "f_gedcom LIKE '".$DBCONN->escapeSimple("%2 DATE%".$i)."%'";
		$i++;
	}
	$sql .= ")";
	*/
	if (!$allgeds) $sql .= " AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$hname = get_sortable_name($row[1]);
		$wname = get_sortable_name($row[2]);
		if (empty($hname)) $hname = "@N.N.";
		if (empty($wname)) $wname = "@N.N.";
		$name = $hname." + ".$wname;
		$myfamlist[$row[0]]["name"] = $name;
		$myfamlist[$row[0]]["gedfile"] = $row[3];
		$myfamlist[$row[0]]["gedcom"] = $row[4];
		$famlist[$row[0]] = $myfamlist[$row[0]];
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
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOM, $GEDCOMS;
	$myfamlist = array();
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	
	$sql = "SELECT f_id, f_husb, f_wife, f_file, f_gedcom, d_gid, d_fact FROM ".$TBLPREFIX."dates, ".$TBLPREFIX."families WHERE f_id=d_gid AND f_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$DBCONN->escapeSimple($day)."' ";
	if (!empty($month)) $sql .= "AND d_month='".$DBCONN->escapeSimple(str2upper($month))."' ";
	if (!empty($year)) $sql .= "AND d_year='".$DBCONN->escapeSimple($year)."' ";
	if (!empty($fact)) {
		$sql .= "AND (";
		$facts = preg_split("/[,:; ]/", $fact);
		$i=0;
		foreach($facts as $fact) {
			if ($i!=0) $sql .= " OR ";
			$ct = preg_match("/!(\w+)/", $fact, $match);
			if ($ct > 0) {
				$fact = $match[1];
				$sql .= "d_fact!='".$DBCONN->escapeSimple(str2upper($fact))."'";
			}
			else {
				$sql .= "d_fact='".$DBCONN->escapeSimple(str2upper($fact))."'";
			}
			$i++;
		}
		$sql .= ") ";
	}
	if (!$allgeds) $sql .= "AND d_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= "GROUP BY f_id ORDER BY d_year, d_month, d_day DESC";
	
	$res = dbquery($sql);
	
	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$GEDCOM = get_gedcom_from_id($row[3]);
		$hname = get_sortable_name($row[1]);
		$wname = get_sortable_name($row[2]);
		if (empty($hname)) $hname = "@N.N.";
		if (empty($wname)) $wname = "@N.N.";
		$name = $hname." + ".$wname;
		if ($allgeds) {
			$myfamlist[$row[0]."[".$row[3]."]"]["name"] = $name;
			$myfamlist[$row[0]."[".$row[3]."]"]["gedfile"] = $row[3];
			$myfamlist[$row[0]."[".$row[3]."]"]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]."[".$row[3]."]"];
		}
		else {
			$myfamlist[$row[0]]["name"] = $name;
			$myfamlist[$row[0]]["gedfile"] = $row[3];
			$myfamlist[$row[0]]["gedcom"] = $row[4];
			$famlist[$row[0]] = $myfamlist[$row[0]];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $myfamlist;
}

//-- search through the gedcom records for sources
function search_sources($query, $allgeds=false, $ANDOR="AND") {
	global $TBLPREFIX, $GEDCOM, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	$mysourcelist = array();	
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	if (!is_array($query)) $sql = "SELECT s_id, s_name, s_file, s_gedcom FROM ".$TBLPREFIX."sources WHERE (s_gedcom $term '".$DBCONN->escapeSimple(strtoupper($query))."' OR s_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR s_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
	else {
		$sql = "SELECT s_id, s_name, s_file, s_gedcom FROM ".$TBLPREFIX."sources WHERE (";
		$i=0;
		foreach($query as $indexval => $q) {
			if ($i>0) $sql .= " $ANDOR ";
			$sql .= "(s_gedcom $term '".$DBCONN->escapeSimple(str2upper($q))."' OR s_gedcom $term '".$DBCONN->escapeSimple(str2lower($q))."')";
			$i++;
		}
		$sql .= ")";
	}	
	if (!$allgeds) $sql .= " AND s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";

	if ((is_array($allgeds)) && (count($allgeds) != 0)) {
		$sql .= " AND (";
		for ($i=0; $i<count($allgeds); $i++) {
			$sql .= "s_file='".$DBCONN->escapeSimple($GEDCOMS[$allgeds[$i]]["id"])."'";
			if ($i < count($allgeds)-1) $sql .= " OR ";
		}
		$sql .= ")";
	}

	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		if (count($allgeds) > 1) {
			$mysourcelist[$row[0]."[".$row[2]."]"]["name"] = $row[1];
			$mysourcelist[$row[0]."[".$row[2]."]"]["gedfile"] = $row[2];
			$mysourcelist[$row[0]."[".$row[2]."]"]["gedcom"] = $row[3];
		}
		else {
			$mysourcelist[$row[0]]["name"] = $row[1];
			$mysourcelist[$row[0]]["gedfile"] = $row[2];
			$mysourcelist[$row[0]]["gedcom"] = $row[3];
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
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	$mysourcelist = array();
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	
	$sql = "SELECT s_id, s_name, s_file, s_gedcom, d_gid FROM ".$TBLPREFIX."dates, ".$TBLPREFIX."sources WHERE s_id=d_gid AND s_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$DBCONN->escapeSimple($day)."' ";
	if (!empty($month)) $sql .= "AND d_month='".$DBCONN->escapeSimple(str2upper($month))."' ";
	if (!empty($year)) $sql .= "AND d_year='".$DBCONN->escapeSimple($year)."' ";
	if (!empty($fact)) $sql .= "AND d_fact='".$DBCONN->escapeSimple(str2upper($fact))."' ";
	if (!$allgeds) $sql .= "AND d_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= "GROUP BY s_id ORDER BY d_year, d_month, d_day DESC";
	
	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		if ($allgeds) {
			$mysourcelist[$row[0]."[".$row[2]."]"]["name"] = $row[1];
			$mysourcelist[$row[0]."[".$row[2]."]"]["gedfile"] = $row[2];
			$mysourcelist[$row[0]."[".$row[2]."]"]["gedcom"] = $row[3];
		}
		else {
			$mysourcelist[$row[0]]["name"] = $row[1];
			$mysourcelist[$row[0]]["gedfile"] = $row[2];
			$mysourcelist[$row[0]]["gedcom"] = $row[3];
		}
	}
	$GEDCOM = $gedold;
	$res->free();
	return $mysourcelist;
}

//-- search through the gedcom records for sources
function search_repos($query, $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	$myrepolist = array();
	$sql = "SELECT o_id, o_file, o_gedcom FROM ".$TBLPREFIX."other WHERE o_type='REPO' AND (o_gedcom $term '".$DBCONN->escapeSimple(strtoupper($query))."' OR o_gedcom $term '".$DBCONN->escapeSimple(str2upper($query))."' OR o_gedcom $term '".$DBCONN->escapeSimple(str2lower($query))."')";
	if (!$allgeds) $sql .= " AND o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$tt = preg_match("/1 NAME (.*)/", $row[2], $match);
		if ($tt == "0") $name = $row[0]; else $name = $match[1];
		if ($allgeds) {
			$myrepolist[$row[0]."[".$row[1]."]"]["name"] = $name;
			$myrepolist[$row[0]."[".$row[1]."]"]["gedfile"] = $row[1];
			$myrepolist[$row[0]."[".$row[1]."]"]["gedcom"] = $row[2];
		}
		else {
			$myrepolist[$row[0]]["name"] = $name;
			$myrepolist[$row[0]]["gedfile"] = $row[1];
			$myrepolist[$row[0]]["gedcom"] = $row[2];
		}
	}
	$res->free();
	return $myrepolist;
}

/**
 * Search the dates table for other records that had events on the given day
 *
 * @author	yalnifj
 * @param	boolean $allgeds setting if all gedcoms should be searched, default is false
 * @return	array $myfamlist array with all individuals that matched the query
 */
function search_other_dates($day="", $month="", $year="", $fact="", $allgeds=false) {
	global $TBLPREFIX, $GEDCOM, $famlist, $DBCONN, $REGEXP_DB, $DBTYPE, $GEDCOMS;
	$myrepolist = array();
	if (stristr($DBTYPE, "mysql")!==false) $term = "REGEXP";
	else if (stristr($DBTYPE, "pgsql")!==false) $term = "~";
	else $term='LIKE';
	
	$sql = "SELECT o_id, o_file, o_type, o_gedcom, d_gid FROM ".$TBLPREFIX."dates, ".$TBLPREFIX."other WHERE o_id=d_gid AND o_file=d_file ";
	if (!empty($day)) $sql .= "AND d_day='".$DBCONN->escapeSimple($day)."' ";
	if (!empty($month)) $sql .= "AND d_month='".$DBCONN->escapeSimple(str2upper($month))."' ";
	if (!empty($year)) $sql .= "AND d_year='".$DBCONN->escapeSimple($year)."' ";
	if (!empty($fact)) $sql .= "AND d_fact='".$DBCONN->escapeSimple(str2upper($fact))."' ";
	if (!$allgeds) $sql .= "AND d_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ";
	$sql .= "GROUP BY o_id ORDER BY d_year, d_month, d_day DESC";
	
	$res = dbquery($sql);

	$gedold = $GEDCOM;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		$tt = preg_match("/1 NAME (.*)/", $row[2], $match);
		if ($tt == "0") $name = $row[0]; else $name = $match[1];
		if ($allgeds) {
			$myrepolist[$row[0]."[".$row[1]."]"]["name"] = $name;
			$myrepolist[$row[0]."[".$row[1]."]"]["gedfile"] = $row[1];
			$myrepolist[$row[0]."[".$row[1]."]"]["type"] = $row[2];
			$myrepolist[$row[0]."[".$row[1]."]"]["gedcom"] = $row[3];
		}
		else {
			$myrepolist[$row[0]]["name"] = $name;
			$myrepolist[$row[0]]["gedfile"] = $row[1];
			$myrepolist[$row[0]]["type"] = $row[2];
			$myrepolist[$row[0]]["gedcom"] = $row[3];
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
	global $DBCONN, $TBLPREFIX, $GEDCOM, $GEDCOMS;

	$parent_id=0;
	for($i=0; $i<$level; $i++) {
		$escparent=preg_replace("/\?/","\\\\\\?", $DBCONN->escapeSimple($parent[$i]));
		$psql = "SELECT p_id FROM ".$TBLPREFIX."places WHERE p_level=".$i." AND p_parent_id=$parent_id AND p_place LIKE '".$escparent."' AND p_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_place";
		$res = dbquery($psql);
		$row =& $res->fetchRow();
		$res->free();
		if (empty($row[0])) break;
		$parent_id = $row[0];
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
	global $GEDCOM, $TBLPREFIX, $placelist, $positions, $DBCONN, $GEDCOMS;

	// --- find all of the place in the file
	if ($level==0) $sql = "SELECT p_place FROM ".$TBLPREFIX."places WHERE p_level=0 AND p_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_place";
	else {
		$parent_id = get_place_parent_id($parent, $level);
		$sql = "SELECT p_place FROM ".$TBLPREFIX."places WHERE p_level=$level AND p_parent_id=$parent_id AND p_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_place";
	}
	$res = dbquery($sql);
	
	while ($row =& $res->fetchRow()) {
		$placelist[] = $row[0];
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
function get_place_positions($parent, $level) {
	global $positions, $TBLPREFIX, $GEDCOM, $DBCONN, $GEDCOMS;

	$p_id = get_place_parent_id($parent, $level);
	$sql = "SELECT DISTINCT pl_gid FROM ".$TBLPREFIX."placelinks WHERE pl_p_id=$p_id AND pl_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);
	
	while ($row =& $res->fetchRow()) {
		$positions[] = $row[0];
	}
	return $positions;
}

function search_places($sql, $splace) {
	global $placelist;

	$res = dbquery($sql);
	
	$k=0;
	while($row =& $res->fetchRow()){
		$row = db_cleanup($row);
		print " ";
		if ($k%4000 == 0) print "\n";
		// -- put all the places into an array
		if (empty($splace)) $ct = preg_match_all("/\d PLAC (.*)/", $row[1], $match, PREG_SET_ORDER);
		else $ct = preg_match_all("/\d PLAC (.*$splace.*)/i", $row[1], $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$place = $match[$i][1];
			$place=trim($place);

			$place=preg_replace("/[\.\"\><]/", "", $place);
			$levels = preg_split ("/,/", $place);		// -- split the place into comma seperated values
			$levels = array_reverse($levels);				// -- reverse the array so that we get the top level first
			$placetext="";
			$j=0;
			foreach($levels as $indexval => $level) {
				if ($j>0) $placetext .= ", ";
				$placetext .= trim($level);
				$j++;
			}
			$placelist[] = $placetext;
			$k++;
		}//--end for
	}//-- end while
	$res->free();
}

//-- find all of the places
function find_place_list($place) {
	global $GEDCOM, $TBLPREFIX, $placelist, $indilist, $famlist, $sourcelist, $otherlist, $DBCONN, $GEDCOMS;
/*
	// --- find all of the place in the file
	$sql = "SELECT i_id, i_gedcom FROM ".$TBLPREFIX."individuals WHERE i_gedcom LIKE '% PLAC %' AND i_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
	$sql = "SELECT f_id, f_gedcom FROM ".$TBLPREFIX."families WHERE f_gedcom LIKE '% PLAC %' AND f_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
	$sql = "SELECT s_id, s_gedcom FROM ".$TBLPREFIX."sources WHERE s_gedcom LIKE '% PLAC %' AND s_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
	$sql = "SELECT o_id, o_gedcom FROM ".$TBLPREFIX."other WHERE o_gedcom LIKE '% PLAC %' AND o_file='".$DBCONN->escapeSimple($GEDCOM)."'";
	search_places($sql, $place);
*/
	$sql = "SELECT p_id, p_place, p_parent_id  FROM ".$TBLPREFIX."places WHERE p_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY p_parent_id, p_id";
	$res = dbquery($sql);

	while($row =& $res->fetchRow()) {
		if ($row[2]==0) $placelist[$row[0]] = $row[1];
		else {
			$placelist[$row[0]] = $placelist[$row[2]].", ".$row[1];
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

function find_media($sql, $type) {
	global $ct, $medialist, $MEDIA_DIRECTORY, $foundlist, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$res = dbquery($sql);
	
	while($row =& $res->fetchRow()){
		print " ";
		find_media_in_record($row[0]);
	}
	$res->free();
}

//-- find all of the media
function get_media_list() {
	global $GEDCOM, $TBLPREFIX, $medialist, $ct, $DBCONN, $GEDCOMS;
	global $GEDCOM_ID_PREFIX, $FAM_ID_PREFIX, $SOURCE_ID_PREFIX;
	$ct = 0;
	if (!isset($medialinks)) $medialinks = array();
	$sqlmm = "SELECT mm_gid, mm_media FROM ".$TBLPREFIX."media_mapping where mm_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."' ORDER BY mm_id ASC";
	$resmm =@ dbquery($sqlmm);
	while($rowmm =& $resmm->fetchRow(DB_FETCHMODE_ASSOC)){
		$sqlm = "SELECT * FROM ".$TBLPREFIX."media where m_media = '".$rowmm["mm_media"]."' AND m_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
		$resm =@ dbquery($sqlm);
		while($rowm =& $resm->fetchRow(DB_FETCHMODE_ASSOC)){
			$filename = check_media_depth($rowm["m_file"]);
			$thumbnail = thumbnail_file($rowm["m_file"]);
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
	/*
	$ct=0;
	$sql = "SELECT i_gedcom, i_id FROM ".$TBLPREFIX."individuals WHERE i_gedcom LIKE '% OBJE%' AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	find_media($sql, 'INDI');
	$sql = "SELECT f_gedcom, f_id FROM ".$TBLPREFIX."families WHERE f_gedcom LIKE '% OBJE%' AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	find_media($sql, 'FAM');
	$sql = "SELECT s_gedcom, s_id FROM ".$TBLPREFIX."sources WHERE s_gedcom LIKE '% OBJE%' AND s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	find_media($sql, 'SOUR');
	$sql = "SELECT o_gedcom, o_id FROM ".$TBLPREFIX."other WHERE o_gedcom LIKE '% OBJE%' AND o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	find_media($sql, 'OTHER');
	*/

}

/**
 * get all first letters of individual's last names
 * @see indilist.php
 * @return array	an array of all letters
 */
function get_indi_alpha() {
	global $CHARACTER_SET, $TBLPREFIX, $GEDCOM, $LANGUAGE, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;
	$indialpha = array();
	$sql = "SELECT DISTINCT i_letter as alpha FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY alpha";
	$res = dbquery($sql);
	

	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($indialpha[$letter])) $indialpha[$letter]=$letter;
	}
	$res->free();

	$sql = "SELECT DISTINCT n_letter as alpha FROM ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	if (!$SHOW_MARRIED_NAMES) $sql .= " AND n_type!='C'";
	$sql .= " ORDER BY alpha";
	$res = dbquery($sql);


	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($indialpha[$letter])) $indialpha[$letter]=$letter;
	}
	$res->free();
	return $indialpha;
}

//-- get the first character in the list
function get_fam_alpha() {
	global $CHARACTER_SET, $TBLPREFIX, $GEDCOM, $LANGUAGE, $famalpha, $DBCONN, $GEDCOMS;

	$famalpha = array();
	$sql = "SELECT DISTINCT i_letter as alpha FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND i_gedcom LIKE '%1 FAMS%' ORDER BY alpha";
	$res = dbquery($sql);


	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($famalpha[$letter])) $famalpha[$letter]=$letter;
	}
	$res->free();
	$sql = "SELECT DISTINCT n_letter as alpha FROM ".$TBLPREFIX."names, ".$TBLPREFIX."individuals WHERE i_file=n_file AND i_id=n_gid AND n_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND i_gedcom LIKE '%1 FAMS%' ORDER BY alpha";
	$res = dbquery($sql);


	$hungarianex = array("DZS", "CS", "DZ" , "GY", "LY", "NY", "SZ", "TY", "ZS");
	$danishex = array("OE", "AE", "AA");
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$letter = $row["alpha"];
		if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian"){
			if (in_array(strtoupper($letter), $danishex)) {
				if (strtoupper($letter) == "OE") $letter = "Ø";
				else if (strtoupper($letter) == "AE") $letter = "Æ";
				else if (strtoupper($letter) == "AA") $letter = "Å";
			}
		}
		if (strlen($letter) > 1){
			if (ord($letter) < 92){
				if ($LANGUAGE != "hungarian" && in_array($letter, $hungarianex)) $letter = substr($letter, 0, 1);
				if (($LANGUAGE != "danish" || $LANGUAGE != "norwegian") && in_array($letter, $danishex)) $letter = substr($letter, 0, 1);
			}
		}

		if (!isset($famalpha[$letter])) $famalpha[$letter]=$letter;
	}
	$res->free();
	$sql = "SELECT f_id FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND (f_husb='' || f_wife='')";
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
	global $TBLPREFIX, $GEDCOM, $LANGUAGE, $indilist, $surname, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;

	$tindilist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."individuals WHERE ";
	if ($LANGUAGE == "hungarian"){
		if (strlen($letter) >= 2) $sql .= "i_letter = '".$DBCONN->escapeSimple($letter)."' ";
		else {
			if ($letter == "C") $text = "CS";
			else if ($letter == "D") $text = "DZ";
			else if ($letter == "G") $text = "GY";
			else if ($letter == "L") $text = "LY";
			else if ($letter == "N") $text = "NY";
			else if ($letter == "S") $text = "SZ";
			else if ($letter == "T") $text = "TY";
			else if ($letter == "Z") $text = "ZS";
			if (isset($text)) $sql .= "(i_letter = '".$DBCONN->escapeSimple($letter)."' AND i_letter != '".$DBCONN->escapeSimple($text)."') ";
			else $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
		}
	}
	else if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		if ($letter == "Ø") $text = "OE";
		else if ($letter == "Æ") $text = "AE";
		else if ($letter == "Å") $text = "AA";
		if (isset($text)) $sql .= "(i_letter = '".$DBCONN->escapeSimple($letter)."' OR i_letter = '".$DBCONN->escapeSimple($text)."') ";
		else if ($letter=="A") $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."' ";
		else $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
	}
	else $sql .= "i_letter LIKE '".$DBCONN->escapeSimple($letter)."%'";
	//-- add some optimization if the surname is set to speed up the lists
	if (!empty($surname)) $sql .= "AND i_surname LIKE '%".$DBCONN->escapeSimple($surname)."%' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY i_name";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (substr($row["i_letter"], 0, 1)==substr($letter, 0, 1)||(isset($text)?substr($row["i_letter"], 0, 1)==substr($text, 0, 1):FALSE)){
			$indi = array();
			$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], 'P'));
			$indi["isdead"] = $row["i_isdead"];
			$indi["gedcom"] = $row["i_gedcom"];
			$indi["gedfile"] = $row["i_file"];
			$tindilist[$row["i_id"]] = $indi;
			//-- cache the item in the $indilist for improved speed
			$indilist[$row["i_id"]] = $indi;
		}
	}
	$res->free();

	$sql = "SELECT i_id, i_name, i_file, i_isdead, i_gedcom, i_letter, i_surname, n_letter, n_name, n_surname, n_letter, n_type FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."names WHERE i_id=n_gid AND i_file=n_file AND ";
	if ($LANGUAGE == "hungarian"){
		if (strlen($letter) >= 2) $sql .= "n_letter = '".$DBCONN->escapeSimple($letter)."' ";
		else {
			if ($letter == "C") $text = "CS";
			else if ($letter == "D") $text = "DZ";
			else if ($letter == "G") $text = "GY";
			else if ($letter == "L") $text = "LY";
			else if ($letter == "N") $text = "NY";
			else if ($letter == "S") $text = "SZ";
			else if ($letter == "T") $text = "TY";
			else if ($letter == "Z") $text = "ZS";
			if (isset($text)) $sql .= "(n_letter = '".$DBCONN->escapeSimple($letter)."' AND n_letter != '".$DBCONN->escapeSimple($text)."') ";
			else $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
		}
	}
	else if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		if ($letter == "Ø") $text = "OE";
		else if ($letter == "Æ") $text = "AE";
		else if ($letter == "Å") $text = "AA";
		if (isset($text)) $sql .= "(n_letter = '".$DBCONN->escapeSimple($letter)."' OR n_letter = '".$DBCONN->escapeSimple($text)."') ";
		else if ($letter=="A") $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."' ";
		else $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."%' ";
	}
	else $sql .= "n_letter LIKE '".$DBCONN->escapeSimple($letter)."%'";
	//-- add some optimization if the surname is set to speed up the lists
	if (!empty($surname)) $sql .= "AND n_surname LIKE '%".$DBCONN->escapeSimple($surname)."%' ";
	if (!$SHOW_MARRIED_NAMES) $sql .= "AND n_type!='C' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY i_name";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		if (substr($row["n_letter"], 0, 1)==substr($letter, 0, 1)||(isset($text)?substr($row["n_letter"], 0, 1)==substr($text, 0, 1):FALSE)){
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
		}
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
	global $TBLPREFIX, $GEDCOM, $LANGUAGE, $indilist, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;

	$tindilist = array();
	$sql = "SELECT * FROM ".$TBLPREFIX."individuals WHERE i_surname LIKE '".$DBCONN->escapeSimple($surname)."' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
		$indi = array();
		$indi["names"] = array(array($row["i_name"], $row["i_letter"], $row["i_surname"], "P"));
		$indi["isdead"] = $row["i_isdead"];
		$indi["gedcom"] = $row["i_gedcom"];
		$indi["gedfile"] = $row["i_file"];
		$indilist[$row["i_id"]] = $indi;
		$tindilist[$row["i_id"]] = $indilist[$row["i_id"]];
	}
	$res->free();

	$sql = "SELECT i_id, i_name, i_file, i_isdead, i_gedcom, i_letter, i_surname, n_letter, n_name, n_surname, n_letter, n_type FROM ".$TBLPREFIX."individuals, ".$TBLPREFIX."names WHERE i_id=n_gid AND i_file=n_file AND n_surname LIKE '".$DBCONN->escapeSimple($surname)."' ";
	if (!$SHOW_MARRIED_NAMES) $sql .= "AND n_type!='C' ";
	$sql .= "AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' ORDER BY n_surname";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$row = db_cleanup($row);
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
	$res->free();
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
	global $TBLPREFIX, $GEDCOM, $famlist, $indilist, $pgv_lang, $LANGUAGE, $SHOW_MARRIED_NAMES, $DBCONN, $GEDCOMS;
	$tfamlist = array();
	$temp = $SHOW_MARRIED_NAMES;
	$SHOW_MARRIED_NAMES = false;
	$myindilist = get_alpha_indis($letter);
	$SHOW_MARRIED_NAMES = $temp;
	if ($letter=="(" || $letter=="[" || $letter=="?") $letter = "\\".$letter;
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
		$sql = "SELECT * FROM ".$TBLPREFIX."families WHERE (f_husb='' OR f_wife='') AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
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
	global $TBLPREFIX, $GEDCOM, $famlist, $indilist, $pgv_lang, $DBCONN, $SHOW_MARRIED_NAMES, $GEDCOMS;
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
		$sql = "SELECT * FROM ".$TBLPREFIX."families WHERE (f_husb='' OR f_wife='') AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
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
	global $TBLPREFIX, $GEDCOM, $DBCONN, $GEDCOMS;

	$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_rin='$rin' AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		return $row["i_id"];
	}
	return $rin;
}

function delete_gedcom($ged) {
	global $INDEX_DIRECTORY, $TBLPREFIX, $pgv_changes, $DBCONN, $GEDCOMS;

	$dbged = $GEDCOMS[$ged]["id"];
	$sql = "DELETE FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."places WHERE p_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."favorites WHERE fv_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."news WHERE n_username='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."blocks WHERE b_username='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."placelinks WHERE pl_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."dates WHERE d_file='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."media WHERE m_gedfile='".$DBCONN->escapeSimple($dbged)."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."media_mapping WHERE mm_gedfile='".$DBCONN->escapeSimple($dbged)."'";
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
	global $TBLPREFIX, $GEDCOM, $DBCONN, $GEDCOMS;

	switch($list) {
		case "indilist":
			$sql = "SELECT count(i_id) FROM ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);
			
			while($row =& $res->fetchRow()) return $row[0];
		break;
		case "famlist":
			$sql = "SELECT count(f_id) FROM ".$TBLPREFIX."families WHERE f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);
			
			while($row =& $res->fetchRow()) return $row[0];
		break;
		case "sourcelist":
			$sql = "SELECT count(s_id) FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);
			
			while($row =& $res->fetchRow()) return $row[0];
		break;
		case "otherlist":
			$sql = "SELECT count(o_id) FROM ".$TBLPREFIX."other WHERE o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);
			
			while($row =& $res->fetchRow()) return $row[0];
		break;
	}
	return 0;
}

/**
 * Accpet changed gedcom record into database
 *
 * This function gets an updated record from the gedcom file and replaces it in the database
 * @author John Finlay
 * @param string $cid The change id of the record to accept
 */
function accept_changes($cid) {
	global $pgv_changes, $GEDCOM, $TBLPREFIX, $FILE, $DBCONN, $GEDCOMS, $MEDIA_ID_PREFIX;

	if (isset($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[count($changes)-1];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
		}
		$FILE = $GEDCOM;
		$gid = $change["gid"];
		$indirec = find_record_in_file($gid);
		if (empty($indirec)) {
			$indirec = find_gedcom_record($gid);
		}
		
		update_record($indirec, $change["type"]=="delete");
		
		if ($change["type"]!="delete") {
			//-- synchronize the gedcom record with any user account
			$user = getUserByGedcomId($gid, $GEDCOM);
			if ($user && ($user["sync_gedcom"]=="Y")) {
				$firstname = get_gedcom_value("GIVN", 2, $indirec);
				$lastname = get_gedcom_value("SURN", 2, $indirec);
				if (empty($lastname)) {
					$fullname = get_gedcom_value("NAME", 1, $indirec, "", false);
					$ct = preg_match("~(.*)/(.*)/~", $fullname, $match);
					if ($ct>0) {
						$firstname = $match[1];
						$lastname = $match[2];
					}
					else $firstname = $fullname;
				}
				$email = get_gedcom_value("EMAIL", 1, $indirec);
				if (($lastname!=$user["lastname"]) || ($firstname!=$user["firstname"]) || ($email!=$user["email"])) {
					//deleteUser($user["username"]);
					$user["email"] = $email;
					$user["firstname"] = $firstname;
					$user["lastname"] = $lastname;
					updateUser($user["username"], $user);
				}
			}
		}
		
		unset($pgv_changes[$cid]);
		write_changes();
		if (isset($_SESSION["recent_changes"]["user"][$GEDCOM])) unset($_SESSION["recent_changes"]["user"][$GEDCOM]);
		if (isset($_SESSION["recent_changes"]["gedcom"][$GEDCOM])) unset($_SESSION["recent_changes"]["gedcom"][$GEDCOM]);
		AddToChangeLog("Accepted change $cid ".$change["type"]." into database ->" . getUserName() ."<-");
		if (isset($change["linkpid"])) accept_changes($change["linkpid"]."_".$GEDCOM);
		return true;
	}
	return false;
}

/**
 * update a record in the database
 * @param string $indirec
 */
function update_record($indirec, $delete=false) {
	global $TBLPREFIX, $GEDCOM, $DBCONN, $GEDCOMS, $FILE;
	
	if (empty($FILE)) $FILE = $GEDCOM;
	
	$tt = preg_match("/0 @(.+)@ (.+)/", $indirec, $match);
	if ($tt>0) {
		$gid = trim($match[1]);
		$type = trim($match[2]);
	}
	else {
		print "ERROR: Invalid gedcom record.";
		return false;
	}

	$sql = "SELECT pl_p_id FROM ".$TBLPREFIX."placelinks WHERE pl_gid='".$DBCONN->escapeSimple($gid)."' AND pl_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);
	
	$placeids = array();
	while($row =& $res->fetchRow()) {
		$placeids[] = $row[0];
	}
	$sql = "DELETE FROM ".$TBLPREFIX."placelinks WHERE pl_gid='".$DBCONN->escapeSimple($gid)."' AND pl_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);
	
	$sql = "DELETE FROM ".$TBLPREFIX."dates WHERE d_gid='".$DBCONN->escapeSimple($gid)."' AND d_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);
	

	//-- delete any unlinked places
	foreach($placeids as $indexval => $p_id) {
		$sql = "SELECT count(pl_p_id) FROM ".$TBLPREFIX."placelinks WHERE pl_p_id=$p_id AND pl_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
		
		$row =& $res->fetchRow();
		if ($row[0]==0) {
			$sql = "DELETE FROM ".$TBLPREFIX."places WHERE p_id=$p_id AND p_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
			$res = dbquery($sql);
			
		}
	}
	
	//-- delete any media mapping references
	$sql = "DELETE FROM ".$TBLPREFIX."media_mapping WHERE mm_gid LIKE '".$DBCONN->escapeSimple($gid)."' AND mm_gedfile='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
	$res = dbquery($sql);

	if ($type=="INDI") {
		$sql = "DELETE FROM ".$TBLPREFIX."individuals WHERE i_id LIKE '".$DBCONN->escapeSimple($gid)."' AND i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
		
		$sql = "DELETE FROM ".$TBLPREFIX."names WHERE n_gid LIKE '".$DBCONN->escapeSimple($gid)."' AND n_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
		
	}
	else if ($type=="FAM") {
		$sql = "DELETE FROM ".$TBLPREFIX."families WHERE f_id LIKE '".$DBCONN->escapeSimple($gid)."' AND f_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
		
	}
	else if ($type=="SOUR") {
		$sql = "DELETE FROM ".$TBLPREFIX."sources WHERE s_id LIKE '".$DBCONN->escapeSimple($gid)."' AND s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
		
	}
	else if ($type=="OBJE") {
		$sql = "DELETE FROM ".$TBLPREFIX."media WHERE m_media LIKE '".$DBCONN->escapeSimple($gid)."' AND m_gedfile='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
	}
	else {
		$sql = "DELETE FROM ".$TBLPREFIX."other WHERE o_id LIKE '".$DBCONN->escapeSimple($gid)."' AND o_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."'";
		$res = dbquery($sql);
		
	}
	if (!$delete) {
		import_record($indirec, true);
	}
}

/**
 * get the top surnames
 * @param int $num	how many surnames to return
 * @return array
 */
function get_top_surnames($num) {
	global $TBLPREFIX, $GEDCOM, $DBCONN, $GEDCOMS;

	$surnames = array();
	$sql = "SELECT COUNT(i_surname) as count, i_surname from ".$TBLPREFIX."individuals WHERE i_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' GROUP BY i_surname ORDER BY count DESC";
	$res = dbquery($sql);
	
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()) {
			if (isset($surnames[str2upper($row[1])]["match"])) $surnames[str2upper($row[1])]["match"] += $row[0];
			else {
				$surnames[str2upper($row[1])]["name"] = $row[1];
				$surnames[str2upper($row[1])]["match"] = $row[0];
			}
		}
		$res->free();
	}
	$sql = "SELECT COUNT(n_surname) as count, n_surname from ".$TBLPREFIX."names WHERE n_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND n_type!='C' GROUP BY n_surname ORDER BY count DESC";
	$res = dbquery($sql);
	
	if (!DB::isError($res)) {
		while($row =& $res->fetchRow()) {
			if (isset($surnames[str2upper($row[1])]["match"])) $surnames[str2upper($row[1])]["match"] += $row[0];
			else {
				$surnames[str2upper($row[1])]["name"] = $row[1];
				$surnames[str2upper($row[1])]["match"] = $row[0];
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
 */
function get_next_id($table, $field) {
	global $TBLPREFIX, $TABLE_IDS;

	if (!isset($TABLE_IDS)) $TABLE_IDS = array();
	if (isset($TABLE_IDS[$table][$field])) {
		$TABLE_IDS[$table][$field]++;
		return $TABLE_IDS[$table][$field];
	}
	$newid = 0;
	$sql = "SELECT MAX($field) FROM ".$TBLPREFIX.$table;
	$res = dbquery($sql);
	
	if (!DB::isError($res)) {
		$row = $res->fetchRow();
		$res->free();
		$newid = $row[0];
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
	global $TBLPREFIX, $DBCONN, $sitelist, $sourcelist;

	//if (isset($sitelist)) return $sitelist;
	$sitelist = array();
	
	if (isset($GEDCOMS[$GEDCOM])) {
		$sql = "SELECT * FROM ".$TBLPREFIX."sources WHERE s_file='".$DBCONN->escapeSimple($GEDCOMS[$GEDCOM]["id"])."' AND s_gedcom LIKE '%1 _DBID%' ORDER BY s_name";
		$res = dbquery($sql);
		
		$ct = $res->numRows();
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			$source = array();
			$source["name"] = $row["s_name"];
			$source["gedcom"] = $row["s_gedcom"];
			$row = db_cleanup($row);
			$source["gedfile"] = $row["s_file"];
			$sitelist[$row["s_id"]] = $source;
			$sourcelist[$row["s_id"]] = $source;
		}
		$res->free();
	}

	return $sitelist;
}

/**
 * Retrieve the array of faqs from the DB table blocks
 * @param int $id		The FAQ ID to retrieven
 * @return array $faqs	The array containing the FAQ items
 */
function get_faq_data($id='') {
	global $TBLPREFIX, $GEDCOM;
	
	$faqs = array();
	// Read the faq data from the DB
	$sql = "SELECT b_id, b_location, b_order, b_config FROM ".$TBLPREFIX."blocks WHERE b_username='$GEDCOM' AND (b_location='header' OR b_location = 'body')";
	if ($id != '') $sql .= "AND b_order='".$id."'";
	$res = dbquery($sql);
	
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		$faqs[$row["b_order"]][$row["b_location"]]["text"] = unserialize($row["b_config"]);
		$faqs[$row["b_order"]][$row["b_location"]]["pid"] = $row["b_id"];
	}
	ksort($faqs);
	return $faqs;
}

function delete_fact($linenum, $pid, $gedrec) {
	global $record, $linefix, $pgv_lang, $TBLPREFIX, $DBCONN;
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
	$GEDCOM = $oldged;
}
?>
<?php
/**
 * Various functions used by the Edit interface
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and Others
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
 * @see functions_places.php
 * @version $Id: functions_edit.php,v 1.6 2008/07/07 17:30:16 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * The DEBUG variable allows you to turn on debugging
 * which will write all communication output to the pgv log files
 * in the index directory and print other information to the screen.
 * Set this to true to enable debugging,
 * but be sure to set it back to false when you are done debugging.
 * @global boolean $DEBUG
 */
$DEBUG = false;

$NPFX_accept = array("Adm", "Amb", "Brig", "Can", "Capt", "Chan", "Chapln", "Cmdr", "Col", "Cpl", "Cpt", "Dr", "Gen", "Gov", "Hon", "Lady", "Lt", "Mr", "Mrs", "Ms", "Msgr", "Pfc", "Pres", "Prof", "Pvt", "Rabbi", "Rep", "Rev", "Sen", "Sgt", "Sir", "Sr", "Sra", "Srta", "Ven");
$SPFX_accept = array("al", "da", "de", "den", "dem", "der", "di", "du", "el", "la", "van", "von");
$NSFX_accept = array("Jr", "Sr", "I", "II", "III", "IV", "MD", "PhD");
$FILE_FORM_accept = array("avi", "bmp", "gif", "jpeg", "mp3", "ole", "pcx", "png", "tiff", "wav");
$emptyfacts = array("_HOL", "_NMR", "_SEPR", "ADOP", "ANUL", "BAPL", "BAPM", "BARM", "BASM",
"BIRT", "BLES", "BURI", "CENS", "CHAN", "CHR", "CHRA", "CONF", "CONL", "CREM",
"DATA", "DEAT", "DIV", "DIVF", "EMIG", "ENDL", "ENGA", "FCOM", "GRAD",
"HUSB", "IMMI", "MAP", "MARB", "MARC", "MARL", "MARR", "MARS", "NATU", "ORDN",
"PROB", "RESI", "RETI", "SLGC", "SLGS", "WIFE", "WILL");
$templefacts = array("SLGC","SLGS","BAPL","ENDL","CONL");
$nonplacfacts = array("ENDL","NCHI","SLGC","SLGS");
$nondatefacts = array("ABBR","ADDR","AFN","AUTH","EMAIL","FAX","NAME","NCHI","NOTE","OBJE",
"PHON","PUBL","REFN","REPO","SEX","SOUR","SSN","TEXT","TITL","WWW","_EMAIL");
$typefacts = array();	//-- special facts that go on 2 TYPE lines

// Next two vars used by insert_missing_subtags()
$date_and_time=array("BIRT","DEAT"); // Tags with date and time
$level2_tags=array( // The order of the $keys is significant
	"_HEB" =>array("NAME","TITL"),
	"ROMN" =>array("NAME","TITL"),
	"TYPE" =>array("GRAD","EVEN","FACT","IDNO","MARR","ORDN","SSN"),
	"AGNC" =>array("EDUC","GRAD","OCCU","RETI","ORDN"),
	"CAUS" =>array("DEAT"),
	"CALN" =>array("REPO"),
	"CEME" =>array("BURI"), // CEME is NOT a valid 5.5.1 tag
	"DATE" =>array("ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARR","MARL", "MARS","RESI","EVEN","EDUC","OCCU","PROP","RELI","RESI","BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","EVEN","BAPL","CONL","ENDL","SLGC","SLGS"),
	"TEMP" =>array("BAPL","CONL","ENDL","SLGC","SLGS"),
	"PLAC" =>array("ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARR","MARL", "MARS","RESI","EVEN","EDUC","OCCU","PROP","RELI","RESI","BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","EVEN","BAPL","CONL","ENDL","SLGC","SLGS","SSN"),
	"STAT" =>array("BAPL","CONL","ENDL","SLGC","SLGS"),
	"ADDR" =>array("BIRT","CHR","CHRA","DEAT","CREM","BURI","MARR","CENS","EDUC","GRAD","OCCU","PROP","ORDN","RESI","EVEN"),
	"PHON" =>array("OCCU","RESI"),
	"FAX"  =>array("OCCU","RESI"),
	"URL"  =>array("OCCU","RESI"),
	"EMAIL"=>array("OCCU","RESI"),
	"AGE"  =>array("CENS","DEAT"),
	"HUSB" =>array("MARR"),
	"WIFE" =>array("MARR"),
	"FAMC" =>array("ADOP","SLGC"),
	"FILE" =>array("OBJE"),
	"_PRIM"=>array("OBJE"),
	"EVEN" =>array("DATA")
);
$STANDARD_NAME_FACTS = array('NAME', 'NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX');

//-- this function creates a new unique connection
//-- and adds it to the connections file
//-- it returns the connection identifier
function newConnection() {
	return session_name()."\t".session_id()."\n";
}

//-- gets the next person in the gedcom, if we reach the end then
//-- returns false
function get_next_xref($gid, $type='INDI') {
	global $GEDCOM, $gGedcom, $TBLPREFIX, $pgv_changes, $DBCONN;

	switch($type) {
		case "INDI":
			$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(i_id,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(i_id,2)";
			break;
		case "FAM":
			$sql = "SELECT f_id FROM ".$TBLPREFIX."families WHERE f_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(f_id,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(f_id,2)";
			break;
		case "SOUR":
			$sql = "SELECT s_id FROM ".$TBLPREFIX."sources WHERE s_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(s_id,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(s_id,2)";
			break;
		case "REPO":
			$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='REPO' AND 0+SUBSTRING(o_id,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(o_id,2)";
			break;
		case "NOTE":
			$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='NOTE' AND 0+SUBSTRING(o_id,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(o_id,2)";
			break;
		case "OBJE":
			$sql = "SELECT m_media FROM ".$TBLPREFIX."media WHERE m_gedfile=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(m_media,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(m_media,2)";
			break;
		case "OTHER":
			$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(o_id,2)>0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(o_id,2)";
			break;
		}
	$res = dbquery($sql, true, 1);
	if ($res->numRows()>0) {
		$row = $res->fetchRow();
		$res->free();
		$xref = $row[0];
		return $xref;
	}
	return "";
}

//-- gets the previous person in the gedcom, if we reach the start then
//-- returns the last record
function get_prev_xref($gid, $type='INDI') {
	global $GEDCOM, $gGedcom, $TBLPREFIX, $pgv_changes, $DBCONN;

	switch($type) {
		case "INDI":
			$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(i_id,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(i_id,2) DESC";
			break;
		case "FAM":
			$sql = "SELECT f_id FROM ".$TBLPREFIX."families WHERE f_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(f_id,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(f_id,2) DESC";
			break;
		case "SOUR":
			$sql = "SELECT s_id FROM ".$TBLPREFIX."sources WHERE s_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(s_id,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(s_id,2) DESC";
			break;
		case "REPO":
			$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='REPO' AND 0+SUBSTRING(o_id,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(o_id,2) DESC";
			break;
		case "NOTE":
			$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='NOTE' AND 0+SUBSTRING(o_id,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(o_id,2) DESC";
			break;
		case "OBJE":
			$sql = "SELECT m_media FROM ".$TBLPREFIX."media WHERE m_gedfile=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(m_media,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(m_media,2) DESC";
			break;
		case "OTHER":
			$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND 0+SUBSTRING(o_id,2)<0+SUBSTRING('".$DBCONN->escapeSimple($gid)."',2) ORDER BY 0+SUBSTRING(o_id,2) DESC";
			break;
		}
	$res = dbquery($sql, true, 1);
	if ($res->numRows()>0) {
		$row = $res->fetchRow();
		$res->free();
		$xref = $row[0];
		return $xref;
	}
	return "";
}

/**
 * Check if the given gedcom record has changed since the last session access
 * This is used to check if the gedcom record changed between the time the user 
 * loaded the individual page and the time they clicked on a link to edit
 * the data.
 *
 * @param string $pid	The gedcom id of the record to check pgv_changes
 * @param string $gedrec	The latest gedcom record to check the CHAN:DATE:TIME (auto accept)
 */
function checkChangeTime($pid, $gedrec) {
	global $GEDCOM, $pgv_changes, $pgv_lang;
	//-- check if the record changes since last access
	$changeTime = 0;
	$changeUser = "";
	if (isset($pgv_changes[$pid."_".$GEDCOM])) {
		$change = end($pgv_changes[$pid."_".$GEDCOM]);
		$changeTime = $change['time'];
		$changeUser = $change['user'];
	}
	else {
		$changrec = get_sub_record(1, "1 CHAN", $gedrec);
		$cdate = get_gedcom_value("DATE", 2, $changrec, '', false);
		if (!empty($cdate)) {
			$ctime = get_gedcom_value("DATE:TIME", 2, $changrec);
			$changeUser = get_gedcom_value("_PGVU", 2, $changrec, '', false);
			$chan_date = new GedcomDate($cdate);
			$chan_date = $chan_date->MinDate();
			$chan_time = parse_time($ctime);
			$changeTime = mktime($chan_time[0], $chan_time[1], $chan_time[2], $chan_date->m, $chan_date->d, $chan_date->y);
		}
	}
	if (isset($_REQUEST['linenum']) && $changeTime!=0 && isset($_SESSION['last_access_time']) && $changeTime > $_SESSION['last_access_time']) {
		print "<span class=\"error\">".preg_replace("/#PID#/", $pid, $pgv_lang["edit_concurrency_msg2"])."<br /><br />";
		if (!empty($changeUser)) print preg_replace(array("/#CHANGEUSER#/", "/#CHANGEDATE#/"), array($changeUser,date("d M Y H:i:s", $changeTime)), $pgv_lang["edit_concurrency_change"])."<br /><br />";
		print $pgv_lang["edit_concurrency_reload"]."</span>";
		print_simple_footer();
		exit;
	}
	return false;
}

/**
 * This function will replace a gedcom record with
 * the id $gid with the $gedrec
 * @param string $gid	The XREF id of the record to replace
 * @param string $gedrec	The new gedcom record to replace with
 * @param boolean $chan		Whether or not to update/add the CHAN record
 * @param string $linkpid	Tells whether or not this record change is linked with the record change of another record identified by $linkpid
 */
function replace_gedrec($gid, $gedrec, $chan=true, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save, $pgv_private_records;

	$gid = strtoupper($gid);
	//-- restore any data that was hidden during privatizing
	if (isset($pgv_private_records[$gid])) {
		$privatedata = trim(get_last_private_data($gid));
		$subs = get_all_subrecords($privatedata, '', false, false, false);
		foreach($subs as $s=>$sub) {
			if (strstr($gedrec, $sub)===false) $gedrec = trim($gedrec)."\r\n".$privatedata;
		}
		unset($pgv_private_records[$gid]);
	}

	if (($gedrec = check_gedcom($gedrec, $chan))!==false) {
		//-- the following block of code checks if the XREF was changed in this record.
		//-- if it was changed we add a warning to the change log
		$ct = preg_match("/0 @(.*)@/", $gedrec, $match);
		if ($ct>0) {
			$oldgid = $gid;
			$gid = trim($match[1]);
			if ($oldgid!=$gid) {
				if ($gid=="REF" || $gid=="new" || $gid=="NEW") {
					$gedrec = preg_replace("/0 @(.*)@/", "0 @".$oldgid."@", $gedrec);
					$gid = $oldgid;
				}
				else {
					AddToChangeLog("Warning: $oldgid was changed to $gid");
					if (isset($pgv_changes[$oldgid."_".$GEDCOM])) unset($pgv_changes[$oldgid."_".$GEDCOM]);
				}
			}
		}

			$change = array();
			$change["gid"] = $gid;
			$change["gedcom"] = $GEDCOM;
			$change["type"] = "replace";
			$change["status"] = "submitted";
			$change["user"] = PGV_USER_NAME;
			$change["time"] = time();
			if (!empty($linkpid)) $change["linkpid"] = $linkpid;
			$change["undo"] = $gedrec;
			if (!isset($pgv_changes[$gid."_".$GEDCOM])) $pgv_changes[$gid."_".$GEDCOM] = array();
			else {
				$lastchange = end($pgv_changes[$gid."_".$GEDCOM]);
				if (!empty($lastchange)) {
					//-- append recods should continue to be marked as append
					if ($lastchange["type"]=="append") $change["type"] = "append";
					//-- delete records will be added back in when they are accepted
					//-- but we should add a warning to the log
					else if ($lastchange["type"]=="delete") {
						AddToLog("Possible GEDCOM corruption: Attempting to replace GEDCOM record $gid which has already been marked for deletion.");
					}
				}
			}
			$pgv_changes[$gid."_".$GEDCOM][] = $change;

		if (PGV_USER_AUTO_ACCEPT) {
			require_once("includes/functions_import.php");
			accept_changes($gid."_".$GEDCOM);
		}
		else {
			write_changes();
		}
		$backtrace = debug_backtrace();
		$temp = "";
		if (isset($backtrace[2])) $temp .= basename($backtrace[2]["file"])." (".$backtrace[2]["line"].")";
		if (isset($backtrace[1])) $temp .= basename($backtrace[1]["file"])." (".$backtrace[1]["line"].")";
		if (isset($backtrace[0])) $temp .= basename($backtrace[0]["file"])." (".$backtrace[0]["line"].")";
		$action=basename($_SERVER["SCRIPT_NAME"]);
		if (!empty($_REQUEST['action'])) $action .= " ".$_REQUEST['action'];
		AddToChangeLog($action." ".$temp." Replacing gedcom record $gid ->" . PGV_USER_NAME ."<-");
		return true;
	}
	return false;
}

//-- this function will append a new gedcom record at
//-- the end of the gedcom file.
function append_gedrec($gedrec, $chan=true, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save;

	if (($gedrec = check_gedcom($gedrec, $chan))!==false) {
		$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
		$gid = $match[1];
		$type = trim($match[2]);

		if (preg_match("/\d+/", $gid)==0) $xref = get_new_xref($type);
		else $xref = $gid;
		$gedrec = preg_replace("/0 @(.*)@/", "0 @$xref@", $gedrec);

		$change = array();
		$change["gid"] = $xref;
		$change["gedcom"] = $GEDCOM;
		$change["type"] = "append";
		$change["status"] = "submitted";
		$change["user"] = PGV_USER_NAME;
		$change["time"] = time();
		if (!empty($linkpid)) $change["linkpid"] = $linkpid;
		$change["undo"] = $gedrec;
		if (!isset($pgv_changes[$xref."_".$GEDCOM])) $pgv_changes[$xref."_".$GEDCOM] = array();
		$pgv_changes[$xref."_".$GEDCOM][] = $change;

		if (PGV_USER_AUTO_ACCEPT) {
			require_once("includes/functions_import.php");
			accept_changes($xref."_".$GEDCOM);
		}
		else {
			write_changes();
		}
		$backtrace = debug_backtrace();
		$temp = "";
		if (isset($backtrace[2])) $temp .= basename($backtrace[2]["file"])." (".$backtrace[2]["line"].")";
		if (isset($backtrace[1])) $temp .= basename($backtrace[1]["file"])." (".$backtrace[1]["line"].")";
		if (isset($backtrace[0])) $temp .= basename($backtrace[0]["file"])." (".$backtrace[0]["line"].")";
		$action=basename($_SERVER["SCRIPT_NAME"]);
		if (!empty($_REQUEST['action'])) $action .= " ".$_REQUEST['action'];
		AddToChangeLog($action." ".$temp." Appending new $type record $xref ->" . PGV_USER_NAME ."<-");
		return $xref;
	}
	return false;
}

//-- this function will delete the gedcom record with
//-- the given $gid
function delete_gedrec($gid, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save;

	//-- first check if the record is not already deleted
	if (isset($pgv_changes[$gid."_".$GEDCOM])) {
		$change = end($pgv_changes[$gid."_".$GEDCOM]);
		if ($change["type"]=="delete") return true;
	}

	$undo = find_gedcom_record($gid);
	if (empty($undo)) return false;
		$change = array();
		$change["gid"] = $gid;
		$change["gedcom"] = $GEDCOM;
		$change["type"] = "delete";
		$change["status"] = "submitted";
		$change["user"] = PGV_USER_NAME;
		$change["time"] = time();
		if (!empty($linkpid)) $change["linkpid"] = $linkpid;
		$change["undo"] = "";
		if (!isset($pgv_changes[$gid."_".$GEDCOM])) $pgv_changes[$gid."_".$GEDCOM] = array();
		$pgv_changes[$gid."_".$GEDCOM][] = $change;

	if (PGV_USER_AUTO_ACCEPT) {
		require_once("includes/functions_import.php");
		accept_changes($gid."_".$GEDCOM);
	}
	else {
		write_changes();
	}
	$backtrace = debug_backtrace();
	$temp = "";
	if (isset($backtrace[2])) $temp .= basename($backtrace[2]["file"])." (".$backtrace[2]["line"].")";
	if (isset($backtrace[1])) $temp .= basename($backtrace[1]["file"])." (".$backtrace[1]["line"].")";
	if (isset($backtrace[0])) $temp .= basename($backtrace[0]["file"])." (".$backtrace[0]["line"].")";
	$action=basename($_SERVER["SCRIPT_NAME"]);
	if (!empty($_REQUEST['action'])) $action .= " ".$_REQUEST['action'];
	AddToChangeLog($action." ".$temp." Deleting gedcom record $gid ->" . PGV_USER_NAME ."<-");
	return true;
}

//-- this function will check a GEDCOM record for valid gedcom format
function check_gedcom($gedrec, $chan=true) {
	global $pgv_lang, $DEBUG, $USE_RTL_FUNCTIONS;

	$gedrec = trim(stripslashes($gedrec));

	if ($USE_RTL_FUNCTIONS) {
		//-- replace any added ltr processing codes
//		$gedrec = preg_replace(array("/".html_entity_decode(getRLM(),ENT_COMPAT,"UTF-8")."/", "/".html_entity_decode("&lrm;",ENT_COMPAT,"UTF-8")."/"), array("",""), $gedrec);
		// Because of a bug in PHP 4, the above generates a run-time error message and does nothing.
		// see:  http://bugs.php.net/bug.php?id=25670
		// HTML entity &rlm; is the 3-byte UTF8 character 0xE2808F
		// HTML entity &lrm; is the 3-byte UTF8 character 0xE2808E
		$gedrec = str_replace(array(chr(0xE2).chr(0x80).chr(0x8F), chr(0xE2).chr(0x80).chr(0x8E)), "", $gedrec);
	}
	$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
	if ($ct==0) {
		print "ERROR 20: Invalid GEDCOM 5.5 format.\n";
		AddToChangeLog("ERROR 20: Invalid GEDCOM 5.5 format.->" . PGV_USER_NAME ."<-");
		if ($GLOBALS["DEBUG"]) {
			print "<pre>$gedrec</pre>\n";
			print debug_print_backtrace();
		}
		return false;
	}
	$gedrec = trim($gedrec);
	if ($chan) {
		$pos1 = strpos($gedrec, "1 CHAN");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+4);
			if ($pos2===false) $pos2 = strlen($gedrec);
			$newgedrec = substr($gedrec, 0, $pos1);
			$newgedrec .= "1 CHAN\r\n2 DATE ".strtoupper(date("d M Y"))."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU ".PGV_USER_NAME."\r\n";
			$newgedrec .= substr($gedrec, $pos2);
			$gedrec = $newgedrec;
		}
		else {
			$newgedrec = "\r\n1 CHAN\r\n2 DATE ".strtoupper(date("d M Y"))."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU ".PGV_USER_NAME;
			$gedrec .= $newgedrec;
		}
	}
	$gedrec = preg_replace('/\\\+/', "\\", $gedrec);

	//-- remove any empty lines
	$lines = preg_split("/\r?\n/", $gedrec);
	$newrec = "";
	foreach($lines as $ind=>$line) {
		//-- remove any whitespace
		$line = trim($line);
		if (!empty($line)) $newrec .= $line."\r\n";
	}

	$newrec = html_entity_decode($newrec);
	return $newrec;
}

/**
 * remove a subrecord from a parent record by gedcom tag
 *
 * @param string $oldrecord	the parent record to remove the subrecord from
 * @param string $tag	the GEDCOM subtag to start deleting at
 * @param string $gid	[optional] gid can be used to limit to @gid@
 * @param int $num		[optional] num specifies which multiple of the tag to remove, set to -1 to remove all 
 * @return string		returns the oldrecord minus the subrecord(s)
 */
function remove_subrecord($oldrecord, $tag, $gid='', $num=0) {
	$newrec = "";
	$gedlines = preg_split("/\n/", $oldrecord);

	$n = 0;
	$matchstr = $tag;
	if (!empty($gid)) $matchstr .= " @".$gid."@";
	for($i=0; $i<count($gedlines); $i++) {
		if (preg_match("/".$matchstr."/", $gedlines[$i])>0) {
			if ($num==-1 || $n==$num) {
				$glevel = $gedlines[$i]{0};
				$i++;
				while((isset($gedlines[$i]))&&(strlen($gedlines[$i])<4 || $gedlines[$i]{0}>$glevel)) $i++;
				$i--;
			}
			else $n++;
		}
		else $newrec .= $gedlines[$i]."\n";
	}
	
	return trim($newrec);
}

/**
 * delete a subrecord from a parent record using the linenumber
 *
 * @param string $oldrecord		parent record to delete from
 * @param int $linenum		linenumber where the subrecord to delete starts
 * @return string				the new record
 */
function remove_subline($oldrecord, $linenum) {
	$newrec = "";
	$gedlines = preg_split("/\n/", $oldrecord);

	for($i=0; $i<$linenum; $i++) {
		if (trim($gedlines[$i])!="") $newrec .= $gedlines[$i]."\n";
	}
	if (isset($gedlines[$linenum])) {
		$fields = preg_split("/\s/", $gedlines[$linenum]);
		$glevel = $fields[0];
		$i++;
		if ($i<count($gedlines)) {
			//-- don't put empty lines in the record
			while((isset($gedlines[$i]))&&(strlen($gedlines[$i])<4 || $gedlines[$i]{0}>$glevel)) $i++;
			while($i<count($gedlines)) {
				if (trim($gedlines[$i])!="") $newrec .= $gedlines[$i]."\n";
				$i++;
			}
		}
	}
	else return $oldrecord;
	
	$newrec = trim($newrec);
	return $newrec;
}

/**
 * Undo a change
 * this function will undo a change in the gedcom file
 * @param string $cid	the change id of the form gid_gedcom
 * @param int $index	the index of the change to undo
 * @return boolean	true if undo successful
 */
function undo_change($cid, $index) {
	global $fcontents, $pgv_changes, $GEDCOM, $manual_save;

	if (isset($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[$index];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
		}

		if ($index==0) unset($pgv_changes[$cid]);
		else {
			for($i=$index; $i<count($pgv_changes[$cid]); $i++) {
				unset($pgv_changes[$cid][$i]);
			}
			if (count($pgv_changes[$cid])==0) unset($pgv_changes[$cid]);
		}
		AddToChangeLog("Undoing change $cid - $index ".$change["type"]." ->" . PGV_USER_NAME ."<-");
		if (!isset($manual_save) || $manual_save==false) write_changes();
		return true;
	}
	return false;
}

/**
 * prints a form to add an individual or edit an individual's name
 *
 * @param string $nextaction	the next action the edit_interface.php file should take after the form is submitted
 * @param string $famid			the family that the new person should be added to
 * @param string $namerec		the name subrecord when editing a name
 * @param string $famtag		how the new person is added to the family
 */
function print_indi_form($nextaction, $famid, $linenum="", $namerec="", $famtag="CHIL", $sextag="") {
	global $pgv_lang, $factarray, $pid, $PGV_IMAGE_DIR, $PGV_IMAGES, $WORD_WRAPPED_NOTES;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $USE_RTL_FUNCTIONS, $GEDCOM;
	global $bdm, $TEXT_DIRECTION, $STANDARD_NAME_FACTS, $ADVANCED_NAME_FACTS, $ADVANCED_PLAC_FACTS, $SURNAME_TRADITION;

	$bdm = ""; // used to copy '1 SOUR' to '2 SOUR' for BIRT DEAT MARR
	init_calendar_popup();
	print "<form method=\"post\" name=\"addchildform\" onsubmit=\"return checkform();\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$nextaction\" />\n";
	print "<input type=\"hidden\" name=\"linenum\" value=\"$linenum\" />\n";
	print "<input type=\"hidden\" name=\"famid\" value=\"$famid\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	print "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\" />\n";
	print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />\n";
	print "<input type=\"hidden\" name=\"goto\" value=\"\" />\n";
	if (preg_match('/^add(child|spouse|newparent|newrepository)/', $nextaction)) {
		print "<input type=\"submit\" value=\"{$pgv_lang['saveandgo']}\" onclick=\"document.addchildform.goto.value='new';\"/>\n";
	}
	print "<table class=\"facts_table\">";

	// When adding a new child, specify the pedigree
	if ($nextaction=='addchildaction')
		add_simple_tag("0 PEDI");

	// Populate the standard NAME field and subfields
	$name_fields=array();
	foreach ($STANDARD_NAME_FACTS as $tag)
		$name_fields[$tag]=get_gedcom_value($tag, 0, $namerec);

	$new_marnm='';
	// Inherit surname from parents, spouse or child
	if (empty($namerec)) {
		// We'll need the parent's name to set the child's surname
		if (isset($pgv_changes[$famid."_".$GEDCOM]))
			$famrec=find_updated_record($famid);
		else
			$famrec=find_family_record($famid);
		if (empty($famrec))
			$famrec = find_record_in_file($famid);
		$parents=find_parents_in_record($famrec);
		$father_name=get_gedcom_value('NAME', 0, find_person_record($parents['HUSB']));
		$mother_name=get_gedcom_value('NAME', 0, find_person_record($parents['WIFE']));
		// We'll need the spouse/child's name to set the spouse/parent's surname
		if (isset($pgv_changes[$pid."_".$GEDCOM]))
			$prec=find_updated_record($pid);
		else
			$prec=find_person_record($pid);
		if (empty($prec))
			$prec = find_record_in_file($pid);
		$indi_name=get_gedcom_value('NAME', 0, $prec);
		// Different cultures do surnames differently
		switch ($SURNAME_TRADITION) {
		case 'spanish':
			//Mother: Maria /AAAA BBBB/
			//Father: Jose  /CCCC DDDD/
			//Child:  Pablo /CCCC AAAA/
			switch ($nextaction) {
			case 'addchildaction':
				if (preg_match('/\/(\S+)\s+\S+\//', $mother_name, $matchm) &&
				    preg_match('/\/(\S+)\s+\S+\//', $father_name, $matchf))
				$name_fields['SURN']=$matchf[1].' '.$matchm[1];
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/(\S+)\s+\S+\//', $indi_name, $match))
					$name_fields['SURN']=$match[1].' ';
				if ($famtag=='WIFE' && preg_match('/\/\S+\s+(\S+)\//', $indi_name, $match))
					$name_fields['SURN']=$match[1].' ';
				break;
			}
			break;
		case 'portuguese':
			//Mother: Maria /AAAA BBBB/
			//Father: Jose  /CCCC DDDD/
			//Child:  Pablo /BBBB DDDD/
			switch ($nextaction) {
			case 'addchildaction':
				if (preg_match('/\/\S+\s+(\S+)\//', $mother_name, $matchm) &&
				    preg_match('/\/\S+\s+(\S+)\//', $father_name, $matchf))
				$name_fields['SURN']=$matchf[1].' '.$matchm[1];
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/\S+\s+(\S+)\//', $indi_name, $match))
					$name_fields['SURN']=' '.$match[1];
				if ($famtag=='WIFE' && preg_match('/\/(\S+)\s+\S+\//', $indi_name, $match))
					$name_fields['SURN']=' '.$match[1];
				break;
			}
			break;
		case 'icelandic':
			// Sons get their father's given name plus "sson"
			// Daughters get their mother's given name plus "sdottir"
			switch ($nextaction) {
			case 'addchildaction':
				if ($sextag=='M' && preg_match('/(\S+)\s+\/.*\//', $father_name, $match))
					$name_fields['SURN']=preg_replace('/s$/', '', $match[1]).'sson';
				if ($sextag=='F' && preg_match('/(\S+)\s+\/.*\//', $mother_name, $match))
					$name_fields['SURN']=preg_replace('/s$/', '', $match[1]).'sdottir';
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/(\S+)sson\s+\/.*\//i', $indi_name, $match))
					$name_fields['GIVN']=$match[1];
				if ($famtag=='WIFE' && preg_match('/(\S+)sdottir\s+\/.*\//i', $indi_name, $match))
					$name_fields['GIVN']=$match[1];
				break;
			}
			break;
		case 'paternal':
			// Father gives his surname to his wife and children
			switch ($nextaction) {
			case 'addspouseaction':
				if ($famtag=='WIFE' && preg_match('/\/(.*)\//', $indi_name, $match))
					$new_marnm=$match[1];
				break;
			case 'addchildaction':
				if (preg_match('/\/([a-z]{2,3}\s+)*(.*)\//i', $father_name, $match)) {
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['SURN']=$match[2];
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/([a-z]{2,3}\s+)*(.*)\//i', $indi_name, $match)) {
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['SURN']=$match[2];
				}
				break;
			}
			break;
		}
	}

	// Make sure there are two slashes in the name
	if (!preg_match('/\//', $name_fields['NAME']))
		$name_fields['NAME'].=' /';
	if (!preg_match('/\/.*\//', $name_fields['NAME']))
		$name_fields['NAME'].='/';

	// Populate any missing 2 XXXX fields from the 1 NAME field
	$npfx_accept=implode('|', $NPFX_accept);
	if (preg_match ("/((($npfx_accept)\.?\s+)*)([^\r\n\/\"]*)(\"(.*)\")?\s*\/(([a-z]{2,3}\s+)*)(.*)\/\s*([^\r\n]*)/i", $name_fields['NAME'], $name_bits)) {
		if (empty($name_fields['NPFX'])) $name_fields['NPFX']=$name_bits[1];
		if (empty($name_fields['GIVN'])) $name_fields['GIVN']=$name_bits[4];
		if (empty($name_fields['SPFX']) && empty($name_fields['SURN'])) {
			$name_fields['SPFX']=trim($name_bits[7]);
			$name_fields['SURN']=$name_bits[9];
		}
		if (empty($name_fields['NSFX'])) $name_fields['NSFX']=$name_bits[10];
		// Don't automatically create an empty NICK - it is an "advanced" field.
		if (empty($name_fields['NICK']) && !empty($name_bits[6]) && !preg_match('/^2 NICK/m',$namerec))
			$name_fields['NICK']=$name_bits[6];
	}

	// Edit the standard name fields
	foreach($name_fields as $tag=>$value)
		add_simple_tag("0 $tag $value");

	// Get the advanced name fields
	$adv_name_fields=array();
	if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_NAME_FACTS, $match))
		foreach ($match[1] as $tag)
			$adv_name_fields[$tag]='';
	// This is a custom tag, but PGV uses it extensively.
	if ($SURNAME_TRADITION=='paternal' || preg_match('/2 _MARNM/', $namerec))
		$adv_name_fields['_MARNM']='';

	foreach ($adv_name_fields as $tag=>$dummy) {
		// Edit existing tags
		if (preg_match_all("/2 $tag (.+)/", $namerec, $match))
			foreach ($match[1] as $value) {
				if ($tag=='_MARNM') {
					$mnsct = preg_match('/\/(.+)\//', $value, $match2);
					$marnm_surn = "";
					if ($mnsct>0) $marnm_surn = $match2[1];
					add_simple_tag("2 _MARNM ".$value);
					add_simple_tag("2 _MARNM_SURN ".$marnm_surn);
				} else {
					add_simple_tag("2 $tag $value");
				}
			}
			// Allow a new row to be entered if there was no row provided
			if (count($match[1])==0 && empty($name_fields[$tag]) || $tag!='_HEB' && $tag!='NICK')
				if ($tag=='_MARNM') {
					add_simple_tag("0 _MARNM");
					add_simple_tag("0 _MARNM_SURN $new_marnm");
				} else
					add_simple_tag("0 $tag");
	}

	// Handle any other NAME subfields that aren't included above (SOUR, NOTE, _CUSTOM, etc)
	if ($namerec!="" && $namerec!="NEW") {
		$gedlines = split("\n", $namerec);	// -- find the number of lines in the record
		$fields = preg_split("/\s+/", $gedlines[0]);
		$glevel = $fields[0];
		$level = $glevel;
		$type = trim($fields[1]);
		$level1type = $type;
		$tags=array();
		$i = 0;
		do {
			if (!isset($name_fields[$type]) && !isset($adv_name_fields[$type])) {
				$text = "";
				for($j=2; $j<count($fields); $j++) {
					if ($j>2) $text .= " ";
					$text .= $fields[$j];
				}
				$iscont = false;
				while(($i+1<count($gedlines))&&(preg_match("/".($level+1)." (CON[CT])\s?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
					$iscont=true;
					if ($cmatch[1]=="CONT") $text.="\r\n";
					if ($WORD_WRAPPED_NOTES) $text .= " ";
					$text .= $cmatch[2];
					$i++;
				}
				add_simple_tag($level." ".$type." ".$text);
			}
			$tags[]=$type;
			$i++;
			if (isset($gedlines[$i])) {
				$fields = preg_split("/\s/", $gedlines[$i]);
				$level = $fields[0];
				if (isset($fields[1])) $type = trim($fields[1]);
			}
		} while (($level>$glevel)&&($i<count($gedlines)));
	}

	// If we are adding a new individual, add the basic details
	if ($nextaction!='update') {
		print '</table><br/><table class="facts_table">';
		// 1 SEX
		if ($famtag=="HUSB" or $sextag=="M") add_simple_tag("0 SEX M");
		else if ($famtag=="WIFE" or $sextag=="F") add_simple_tag("0 SEX F");
		else add_simple_tag("0 SEX");
		$bdm = "BD";
		add_simple_tag("0 BIRT");
		add_simple_tag("0 DATE", "BIRT");
		add_simple_tag("0 PLAC", "BIRT");
		if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_PLAC_FACTS, $match))
			foreach ($match[1] as $tag)
				add_simple_tag("0 $tag", 'BIRT');
		add_simple_tag("0 MAP", "BIRT");
		add_simple_tag("0 LATI", "BIRT");
		add_simple_tag("0 LONG", "BIRT");
		//-- if adding a spouse add the option to add a marriage fact to the new family
		if ($nextaction=='addspouseaction' || ($nextaction=='addnewparentaction' && $famid!='new')) {
			$bdm .= "M";
			add_simple_tag("0 MARR");
			add_simple_tag("0 DATE", "MARR");
			add_simple_tag("0 PLAC", "MARR");
			if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_PLAC_FACTS, $match))
				foreach ($match[1] as $tag)
					add_simple_tag("0 $tag", 'MARR');
			add_simple_tag("0 MAP", "MARR");
			add_simple_tag("0 LATI", "MARR");
			add_simple_tag("0 LONG", "MARR");
		}
		add_simple_tag("0 DEAT");
		add_simple_tag("0 DATE", "DEAT");
		add_simple_tag("0 PLAC", "DEAT");
		if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_PLAC_FACTS, $match))
			foreach ($match[1] as $tag)
				add_simple_tag("3 $tag", 'DEAT');
		add_simple_tag("0 MAP", "DEAT");
		add_simple_tag("0 LATI", "DEAT");
		add_simple_tag("0 LONG", "DEAT");
	}
	if (PGV_USER_IS_ADMIN) {
		print "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		print_help_link("no_update_CHAN_help", "qm");
		print $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
		print "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
		print $pgv_lang["no_update_CHAN"]."<br />\n";
		if (isset($famrec)) echo format_fact_date(get_sub_record(1, "1 CHAN", $famrec), false, true);
		print "</td></tr>\n";
	}
	print "</table>\n";
	if ($nextaction=='update') { // GEDCOM 5.5.1 spec says NAME doesn't get a OBJE
		print_add_layer('SOUR');
		print_add_layer('NOTE');
	} else {
		print_add_layer('SOUR', 1);
		print_add_layer('NOTE', 1);
		print_add_layer('OBJE', 1);
	}
	print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />\n";
	if (preg_match('/^add(child|spouse|newparent|source)/', $nextaction)) {
		print "<input type=\"submit\" value=\"{$pgv_lang['saveandgo']}\" onclick=\"document.addchildform.goto.value='new';\"/>\n";
	}
	print "</form>\n";
	?>
	<script type="text/javascript" src="autocomplete.js"></script>
	<script type="text/javascript">
	<!--
	//	copy php arrays into js arrays
	var npfx_accept = new Array(<?php foreach ($NPFX_accept as $indexval => $npfx) print "'".$npfx."',"; print "''";?>);
	var spfx_accept = new Array(<?php foreach ($SPFX_accept as $indexval => $spfx) print "'".$spfx."',"; print "''";?>);
	Array.prototype.in_array = function(val) {
		for (var i in this) {
			if (this[i] == val) return true;
		}
		return false;
	}
	function trim(str) {
		//-- See the NAME_TEXT portion of the GEDCOM spec
		//-- according to GEDCOM spec commas should not be allowed in NAME_TEXT, but
		//-- some localization requirements require commas
		//-- str=str.replace(/,/g," ");
		
		str=str.replace(/\s\s+/g," ");
		return str.replace(/(^\s+)|(\s+$)/g,'');
	}

	function lang_class(str) {
		if (str.match(/[\u0370-\u03FF]/)) return "greek";
		if (str.match(/[\u0400-\u04FF]/)) return "cyrillic";
		if (str.match(/[\u0590-\u05FF]/)) return "hebrew";
		if (str.match(/[\u0600-\u06FF]/)) return "arabic";
		return "latin"; // No matched text implies latin :-)
	}

	// Update the NAME and _MARNM fields from the name components
	// and also display the value in read-only "gedcom" format.
	function updatewholename() {
		// don't update the name if the user manually changed it
		if (manualChange) return;
		// Update NAME field from components and display it
		var frm =document.forms[0];
		var npfx=frm.NPFX.value;
		var givn=frm.GIVN.value;
		var spfx=frm.SPFX.value;
		var surn=frm.SURN.value;
		var nsfx=frm.NSFX.value;
		document.getElementById('NAME').value=trim(npfx+" "+givn+" /"+trim(spfx+" "+surn)+"/ "+nsfx);
		document.getElementById('NAME_display').innerHTML=frm.NAME.value;
		// Married names inherit some NSFX values, but not these
		nsfx=nsfx.replace(/^(I|II|III|IV|V|VI|Junior|Jr\.?|Senior|Sr\.?)$/i, '');
		// Update _MARNM field from _MARNM_SURN field and display it
		// Be careful of mixing latin/hebrew/etc. character sets.
		var ip=document.getElementsByTagName('input');
		var marnm_id='';
		var romn='';
		var heb='';
		for (var i=0; i<ip.length; i++) {
			var val=ip[i].value;
			if (ip[i].id.indexOf("_HEB")==0)
				heb=val;
			if (ip[i].id.indexOf("ROMN")==0)
				romn=val;
			if (ip[i].id.indexOf("_MARNM")==0) {
				if (ip[i].id.indexOf("_MARNM_SURN")==0) {
					var msurn='';
					if (val!='') {
						var lc=lang_class(document.getElementById(ip[i].id).value);
						if (lang_class(frm.NAME.value)==lc)
							msurn=trim(npfx+" "+givn+" /"+val+"/ "+nsfx);
						else if (lc=="hebrew")
							msurn=heb.replace(/\/.*\//, '/'+val+'/');
						else if (lang_class(romn)==lc)
							msurn=romn.replace(/\/.*\//, '/'+val+'/');
					}
					document.getElementById(marnm_id).value=msurn;
					document.getElementById(marnm_id+"_display").innerHTML=msurn;
				} else {
					marnm_id=ip[i].id;
				}
			}
		}
	}
	
	/**
	 * convert a hidden field to a text box
	 */
	var oldName = "";
	var manualChange = false;
	function convertHidden(eid) {
		var element = document.getElementById(eid);
		if (element) {
			if (element.type=="hidden") {
				//-- IE doesn't allow changing the "type" of an input field so we'll cludge it ( silly :P)
				if (IE) {
					var newInput = document.createElement('input');
					newInput.setAttribute("type", "text");
					newInput.setAttribute("name", element.Name);
					newInput.setAttribute("id", element.id);
					newInput.setAttribute("value", element.value);
					newInput.setAttribute("onchange", element.onchange);
					var parent = element.parentNode;
					parent.replaceChild(newInput, element);
					element = newInput;
				}
				else {
					element.type="text";
				}
				element.size="40";
				oldName = element.value;
				manualChange = true;
				var delement = document.getElementById(eid+"_display");
				if (delement) {
					delement.style.display='none';
					//-- force FF ui to update the display
					if (delement.innerHTML != oldName) {
						oldName = delement.innerHTML;
						element.value = oldName;
					}
				}
			}
			else {
				manualChange = false;
				//-- IE doesn't allow changing the "type" of an input field so we'll cludge it ( silly :P)
				if (IE) {
					var newInput = document.createElement('input');
					newInput.setAttribute("type", "hidden");
					newInput.setAttribute("name", element.Name);
					newInput.setAttribute("id", element.id);
					newInput.setAttribute("value", element.value);
					newInput.setAttribute("onchange", element.onchange);
					var parent = element.parentNode;
					parent.replaceChild(newInput, element);
					element = newInput;
				}
				else {
					element.type="hidden";
				}
				var delement = document.getElementById(eid+"_display");
				if (delement) {
					delement.style.display='inline';
				}
			}
		}
	}
	
	/**
	 * if the user manually changed the NAME field, then update the textual 
	 * HTML representation of it
	 * If the value changed set manualChange to true so that changing
	 * the other fields doesn't change the NAME line
	 */
	function updateTextName(eid) {
		var element = document.getElementById(eid);
		if (element) {
			if (element.value!=oldName) manualChange = true;
			var delement = document.getElementById(eid+"_display");
			if (delement) {
				delement.innerHTML = element.value;
			}
		}
	}

	function checkform() {
		// Make sure we have entered at least something for the name
		if (document.addchildform.NAME.value=="//") {
			alert('<?php print $pgv_lang["must_provide"]; print " ".$factarray["NAME"]; ?>');
			document.addchildform.NAME.focus();
			return false;
		}

		var ip=document.getElementsByTagName('input');
		for (var i=0; i<ip.length; i++) {
			// ADD slashes to _HEB and _AKA names
			if (ip[i].id.indexOf('_AKA')==0 || ip[i].id.indexOf('_HEB')==0 || ip[i].id.indexOf('ROMN')==0)
				if (ip[i].value.indexOf('/')<0 && ip[i].value!='')
					ip[i].value=ip[i].value.replace(/([^\s]+)\s*$/, "/$1/");
			// Blank out temporary _MARNM_SURN and empty name fields
			if (ip[i].id.indexOf("_MARNM_SURN")==0 || ip[i].value=='//')
					ip[i].value='';
		}
		return true;
	}
	//-->
	</script>
	<?php
	// Force the 1 NAME record to be rebuilt from the 2 XXXX parts
	// This tidies up whitespace and removes "nicknames".
	// See [ 1830176 ] - don't automatically update the 1 NAME line from 2 parts
	// in case the user wants them to be different
	// if the user actually makes a change in one of the parts then the updatewholename
	// will be called and the 1 NAME updated
	//print "<script type='text/javascript'>updatewholename();</script>";
}

/**
 * generates javascript code for calendar popup in user's language
 *
 * @param string id		form text element id where to return date value
 * @param boolean $asString	Whether or not to return this text as a string or print it
 * @see init_calendar_popup()
 */
function print_calendar_popup($id, $asString=false) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	// calendar button
	$text = $pgv_lang["select_date"];
	if (isset($PGV_IMAGES["calendar"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["button"]."\" name=\"img".$id."\" id=\"img".$id."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = "";
	$out .= "<a href=\"javascript: ".$text."\" onclick=\"cal_toggleDate('caldiv".$id."', '".$id."'); return false;\">";
	$out .= $Link;
	$out .= "</a>\n";
	$out .= "<div id=\"caldiv".$id."\" style=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white; z-index: 1000;\"></div>\n";
	if ($asString) return $out;
	else print $out;
}
/**
 * @todo add comments
 */
function print_addnewrepository_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["create_repository"];
	if (isset($PGV_IMAGES["addrepository"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addrepository"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"addnewrepository(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

/**
 * @todo add comments
 */
function print_addnewsource_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["create_source"];
	if (isset($PGV_IMAGES["addsource"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addsource"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	print "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"addnewsource(document.getElementById('".$element_id."')); return false;\">";
	print $Link;
	print "</a>";
}

/**
 * add a new tag input field
 *
 * called for each fact to be edited on a form.
 * Fact level=0 means a new empty form : data are POSTed by name
 * else data are POSTed using arrays :
 * glevels[] : tag level
 *  islink[] : tag is a link
 *     tag[] : tag name
 *    text[] : tag value
 *
 * @param string $tag			fact record to edit (eg 2 DATE xxxxx)
 * @param string $upperlevel	optional upper level tag (eg BIRT)
 * @param string $label			An optional label to print instead of the default from the $factarray
 * @param string $readOnly		optional, when "READONLY", fact data can't be changed
 * @param string $noClose		optional, when "NOCLOSE", final "</td></tr>" won't be printed
 *								(so that additional text can be printed in the box)
 * @param boolean $rowDisplay	True to have the row displayed by default, false to hide it by default
 */
function add_simple_tag($tag, $upperlevel="", $label="", $readOnly="", $noClose="", $rowDisplay=true) {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_DIRECTORY, $TEMPLE_CODES;
	global $assorela, $tags, $emptyfacts, $TEXT_DIRECTION, $pgv_changes, $GEDCOM;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $upload_count;
	global $tabkey, $STATUS_CODES, $SPLIT_PLACES, $pid, $linkToID;
	global $bdm, $PRIVACY_BY_RESN;

	if (!isset($noClose) && isset($readOnly) && $readOnly=="NOCLOSE") {
		$noClose = "NOCLOSE";
		$readOnly = "";
	}

	if (!isset($noClose) || $noClose!="NOCLOSE") $noClose = "";
	if (!isset($readOnly) || $readOnly!="READONLY") $readOnly = "";

	if (!isset($tabkey)) $tabkey = 1;

	if (empty($linkToID)) $linkToID = $pid;

	$subnamefacts = array("NPFX", "GIVN", "SPFX", "SURN", "NSFX", "_MARNM_SURN");
	@list($level, $fact, $value) = explode(" ", $tag);

	if ($fact=="LATI" || $fact=="LONG") {
	?>
	<script type="text/javascript">
	<!--
	function valid_lati_long(field, pos, neg) {
		// valid LATI or LONG according to Gedcom standard
		// pos (+) : N or E
		// neg (-) : S or W
		txt=field.value.toUpperCase();
		txt=txt.replace(/(^\s*)|(\s*$)/g,''); // trim
		txt=txt.replace(/ /g,':'); // N12 34 ==> N12:34
		txt=txt.replace(/\+/g,''); // +17.1234 ==> 17.1234
		txt=txt.replace(/-/g,neg);	// -0.5698 ==> W0.5698
		txt=txt.replace(/,/g,'.');	// 0,5698 ==> 0.5698
		// 0�34'11 ==> 0:34:11
		txt=txt.replace(/\uB0/g,':'); // �
		txt=txt.replace(/\u27/g,':'); // '
		// 0:34:11.2W ==> W0.5698
		txt=txt.replace(/^([0-9]+):([0-9]+):([0-9.]+)(.*)/g, function($0, $1, $2, $3, $4) { var n=parseFloat($1); n+=($2/60); n+=($3/3600); n=Math.round(n*1E4)/1E4; return $4+n; });
		// 0:34W ==> W0.5667
		txt=txt.replace(/^([0-9]+):([0-9]+)(.*)/g, function($0, $1, $2, $3) { var n=parseFloat($1); n+=($2/60); n=Math.round(n*1E4)/1E4; return $3+n; });
		// 0.5698W ==> W0.5698
		txt=txt.replace(/(.*)([N|S|E|W]+)$/g,'$2$1');
		// 17.1234 ==> N17.1234
		if (txt!='' && txt.charAt(0)!=neg && txt.charAt(0)!=pos) txt=pos+txt;
		field.value = txt;
	}
	function toggle_lati_long() {
		tr = document.getElementsByTagName('tr');
		for (var i=0; i<tr.length; i++) {
			if (tr[i].id.indexOf("LATI")>=0 || tr[i].id.indexOf("LONG")>=0) {
				var disp = tr[i].style.display;
				if (disp=="none") {
					disp="table-row";
					if (document.all && !window.opera) disp = "inline"; // IE
				}
				else disp="none";
				tr[i].style.display=disp;
			}
		}
	}
	//-->
	</script>
	<?php
	}

	// element name : used to POST data
	if ($level==0) {
		if ($upperlevel) $element_name=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE | ...
		else $element_name=$fact; // ex: OCCU
	} else $element_name="text[]";

	// element id : used by javascript functions
	if ($level==0) $element_id=$fact; // ex: NPFX | GIVN ...
	else $element_id=$fact.floor(microtime()*1000000); // ex: SOUR56402
	if ($upperlevel) $element_id=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE ...

	// field value
	$islink = (substr($value,0,1)=="@" and substr($value,0,2)!="@#");
	if ($islink) $value=trim($value, " @");
	else $value=trim(substr($tag, strlen($fact)+3));
	if ($fact=='REPO' || $fact=='SOUR' || $fact=='OBJE' || $fact=='FAMC')
		$islink = true;

	// rows & cols
	switch ($fact) {
	case 'FORM':
		$rows=1;
		$cols=5;
		break;
	case 'LATI': case 'LONG': case 'NPFX': case 'SPFX': case 'NSFX':
		$rows=1;
		$cols=12;
		break;
	case 'DATE': case 'TIME': case 'TYPE':
		$rows=1;
		$cols=20;
		break;
	case 'GIVN': case 'SURN': case '_MARNM':
		$rows=1;
		$cols=25;
		break;
	case '_UID':
		$rows=1;
		$cols=50;
		break;
	case 'TEXT': case 'PUBL': case 'NOTE':
		$rows=10;
		$cols=70;
		break;
	case 'ADDR':
		$rows=4;
		$cols=40;
		break;
	default:
		$rows=1;
		$cols=($islink ? 10 : 40);
		break;
	}

	// label
	$style="";
	print "<tr id=\"".$element_id."_tr\" ";
	if ($fact=="MAP" || $fact=="LATI" || $fact=="LONG") print " style=\"display:none;\"";
	print " >\n";
	if (in_array($fact, $subnamefacts) || $fact=="LATI" || $fact=="LONG")
			print "<td class=\"optionbox $TEXT_DIRECTION wrap width25\">";
	else	print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";

	// help link
	if (!in_array($fact, $emptyfacts)) {
		if ($fact=="DATE") print_help_link("def_gedcom_date_help", "qm", "date");
		else if ($fact=="RESN") print_help_link($fact."_help", "qm");
		else print_help_link("edit_".$fact."_help", "qm");
	}
	if ($GLOBALS["DEBUG"]) print $element_name."<br />\n";
	if (!empty($label)) print $label;
	else {
		if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
		else if (isset($factarray[$fact])) print $factarray[$fact];
		else print $fact;
	}
	print "\n";

	// tag level
	if ($level>0) {
		if ($fact=="TEXT" and $level>1) {
			print "<input type=\"hidden\" name=\"glevels[]\" value=\"".($level-1)."\" />";
			print "<input type=\"hidden\" name=\"islink[]\" value=\"0\" />";
			print "<input type=\"hidden\" name=\"tag[]\" value=\"DATA\" />";
			//-- leave data text[] value empty because the following TEXT line will
			//--- cause the DATA to be added
			print "<input type=\"hidden\" name=\"text[]\" value=\"\" />";
		}
		print "<input type=\"hidden\" name=\"glevels[]\" value=\"".$level."\" />\n";
		print "<input type=\"hidden\" name=\"islink[]\" value=\"".($islink)."\" />\n";
		print "<input type=\"hidden\" name=\"tag[]\" value=\"".$fact."\" />\n";
	}
	print "\n</td>";

	// value
	print "<td class=\"optionbox wrap\">\n";
	if ($GLOBALS["DEBUG"]) print $tag."<br />\n";

	// retrieve linked NOTE
	if ($fact=="NOTE" and $islink) {
		$noteid = $value;
		print "<input type=\"hidden\" name=\"text[]\" value=\"".$noteid."\" />\n";
		if (!isset($pgv_changes[$noteid."_".$GEDCOM])) $noterec = find_gedcom_record($noteid);
		else $noterec = find_updated_record($noteid);
		$n1match = array();
		$nt = preg_match("/0 @$value@ NOTE (.*)/", $noterec, $n1match);
		if ($nt!==false) $value=trim(strip_tags(@$n1match[1].get_cont(1, $noterec, false)));
		$element_name="NOTE[".$noteid."]";
	}

	if (in_array($fact, $emptyfacts)&& (empty($value) || $value=="y" || $value=="Y")) {
		$value = strtoupper($value);
		//-- don't default anything to Y when adding events through people
		//-- default to Y when specifically adding one of these events
		if ($level==1) $value="Y"; // default YES
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".$value."\" />";
		if ($level<=1) {
			print "<input type=\"checkbox\" ";
			if ($value=="Y") print " checked=\"checked\"";
			print " onClick=\"if (this.checked) ".$element_id.".value='Y'; else ".$element_id.".value=''; \" />";
			print $pgv_lang["yes"];
		}
	}
	else if ($fact=="TEMP") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		print "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
		foreach($TEMPLE_CODES as $code=>$temple) {
			print "<option value=\"$code\"";
			if ($code==$value) print " selected=\"selected\"";
			print ">$temple ($code)</option>\n";
		}
		print "</select>\n";
	}
	else if ($fact=="ADOP") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >";
		foreach (array("BOTH"=>$factarray["HUSB"]."+".$factarray["WIFE"],
		               "HUSB"=>$factarray["HUSB"],
		               "WIFE"=>$factarray["WIFE"]) as $k=>$v) {
			print "<option value='$k'";
			if ($value==$k)
				print " selected";
			print ">$v</option>";
		}
		print "</select>\n";
	}
	else if ($fact=="PEDI") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >";
		foreach (array(""       =>$pgv_lang["unknown"],
		               "birth"  =>$factarray["BIRT"],
		               "adopted"=>$pgv_lang["adopted"],
		               "foster" =>$pgv_lang["foster"],
									 "sealing"=>$pgv_lang["sealing"]) as $k=>$v) {
			print "<option value='$k'";
			if (str2lower($value)==$k)
				print " selected";
			print ">$v</option>";
		}
		print "</select>\n";
	}
	else if ($fact=="STAT") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		print "<option value=''>No special status</option>\n";
		foreach($STATUS_CODES as $code=>$status) {
			print "<option value=\"$code\"";
			if ($code==$value) print " selected=\"selected\"";
			print ">$status</option>\n";
		}
		print "</select>\n";
	}
	else if ($fact=="RELA") {
		$text=strtolower($value);
		// add current relationship if not found in default list
		if (!array_key_exists($text, $assorela)) $assorela[$text]=$text;
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		foreach ($assorela as $key=>$value) {
			print "<option value=\"". $key . "\"";
			if ($key==$text) print " selected=\"selected\"";
			print ">" . $assorela["$key"] . "</option>\n";
		}
		print "</select>\n";
	}
	else if ($fact=="RESN") {
		?>
		<script type="text/javascript">
		<!--
		function update_RESN_img(resn_val) {
			document.getElementById("RESN_none").style.display="none";
			document.getElementById("RESN_locked").style.display="none";
			document.getElementById("RESN_privacy").style.display="none";
			document.getElementById("RESN_confidential").style.display="none";
			document.getElementById("RESN_"+resn_val).style.display="inline";
			if (resn_val=='none') resn_val='';
			document.getElementById("<?php print $element_id?>").value=resn_val;
		}
		//-->
		</script>
		<?php
		if (!$PRIVACY_BY_RESN && $level==1) {
			// warn user that level 1 RESN tags have no effect when PRIVACY_BY_RESN is false 
			print "<small>".$pgv_lang["resn_disabled"]."</small>";
		}
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".$value."\" />\n";
		print "<table><tr valign=\"top\">\n";
		foreach (array("none", "locked", "privacy", "confidential") as $resn_index => $resn_val) {
			if ($resn_val=="none") $resnv=""; else $resnv=$resn_val;
			print "<td><input tabindex=\"".$tabkey."\" type=\"radio\" name=\"RESN_radio\" onclick=\"update_RESN_img('".$resn_val."')\"";
			print " value=\"".$resnv."\"";
			if ($value==$resnv) print " checked=\"checked\"";
			print " /><small>".$pgv_lang[$resn_val]."</small>";
			print "<br />&nbsp;<img id=\"RESN_".$resn_val."\" src=\"images/RESN_".$resn_val.".gif\"  alt=\"".$pgv_lang[$resn_val]."\" title=\"".$pgv_lang[$resn_val]."\" border=\"0\"";
			if ($value==$resnv) print " style=\"display:inline\""; else print " style=\"display:none\"";
			print " /></td>\n";
		}
		print "</tr></table>\n";
	}
	else if ($fact=="_PRIM" or $fact=="_THUM") {
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		print "<option value=\"\"></option>\n";
		print "<option value=\"Y\"";
		if ($value=="Y") print " selected=\"selected\"";
		print ">".$pgv_lang["yes"]."</option>\n";
		print "<option value=\"N\"";
		if ($value=="N") print " selected=\"selected\"";
		print ">".$pgv_lang["no"]."</option>\n";
		print "</select>\n";
	}
	else if ($fact=="SEX") {
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\">\n<option value=\"M\"";
		if ($value=="M") print " selected=\"selected\"";
		print ">".$pgv_lang["male"]."</option>\n<option value=\"F\"";
		if ($value=="F") print " selected=\"selected\"";
		print ">".$pgv_lang["female"]."</option>\n<option value=\"U\"";
		if ($value=="U" || empty($value)) print " selected=\"selected\"";
		print ">".$pgv_lang["unknown"]."</option>\n</select>\n";
	}
	else if ($fact == "TYPE" && $level == '3') {
		//-- Build array of currently defined values for this Media Fact
		foreach ($pgv_lang as $varname => $typeValue) {
			if (substr($varname, 0, 6) == "TYPE__") {
				if ($varname != "TYPE__other") $type[strtolower(substr($varname, 6))] = $typeValue;
			}
		}
		//-- Sort the array into a meaningful order
		array_flip($type);
		asort($type);
		array_flip($type);
		//-- Add "Other" at the end of the list
		$type["other"] = $pgv_lang["TYPE__other"];
		//-- Build the selector for the Media "TYPE" Fact
		print "<select tabindex=\"".$tabkey."\" name=\"text[]\">";
		print "<option selected=\"selected\" value=\"\"> ".$pgv_lang["choose"]." </option>";
		$selectedValue = strtolower($value);
		foreach ($type as $typeName => $typeValue) {
			print "<option value=\"".$typeName."\"";
			if ($selectedValue == $typeName) print "selected=\"selected\"";
			print "> ".$typeValue." </option>";
		}
		print "</select>";
	}
	else if (($fact=="NAME" && $upperlevel!='REPO') || $fact=="_MARNM") {
		// Populated in javascript from sub-tags
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" onchange=\"updateTextName('".$element_id."');\" value=\"".PrintReady(htmlspecialchars($value))."\" >";
		print "<span id=\"".$element_id."_display\">".PrintReady(htmlspecialchars($value))."</span>";
		print " <a href=\"#edit_name\" alt=\"".$pgv_lang["edit_name"]."\" onclick=\"convertHidden('".$element_id."'); return false;\"> ";
		if (isset($PGV_IMAGES["edit_indi"]["small"])) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_indi"]["small"]."\" border=\"0\" width=\"20\" alt=\"".$pgv_lang["edit_name"]."\" align=\"top\" />";
		else print "<span class=\"age\">[".$pgv_lang["edit_name"]."]</span>";
		print "</a>";
	} else {
		// textarea
		if ($rows>1) print "<textarea tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" rows=\"".$rows."\" cols=\"".$cols."\">".PrintReady(htmlspecialchars($value))."</textarea><br />\n";
		else {
			// text
			print "<input tabindex=\"".$tabkey."\" type=\"text\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".PrintReady(htmlspecialchars($value))."\" size=\"".$cols."\" dir=\"ltr\"";
			if ($fact=="NPFX") print " onkeyup=\"wactjavascript_autoComplete(npfx_accept,this,event)\" autocomplete=\"off\" ";
			// onKeyUp should suffice.  Why the others?
			if (in_array($fact, $subnamefacts)) print " onBlur=\"updatewholename();\" onKeyUp=\"updatewholename();\"";
			if ($fact=="DATE") print " onblur=\"valid_date(this);\" onmouseout=\"valid_date(this);\"";
			if ($fact=="LATI") print " onblur=\"valid_lati_long(this, 'N', 'S');\" onmouseout=\"valid_lati_long(this, 'N', 'S');\"";
			if ($fact=="LONG") print " onblur=\"valid_lati_long(this, 'E', 'W');\" onmouseout=\"valid_lati_long(this, 'E', 'W');\"";
			//if ($fact=="FILE") print " onchange=\"if (updateFormat) updateFormat(this.value);\"";
			print " ".$readOnly." />\n";
		}
		// split PLAC
		if ($fact=="PLAC" && $readOnly=="") {
			print "<div id=\"".$element_id."_pop\" style=\"display: inline;\">\n";
			print_specialchar_link($element_id, false);
			print_findplace_link($element_id);
			print "</div>\n";
			print "<a href=\"javascript:;\" onclick=\"toggle_lati_long();\"><img src=\"images/buttons/target.gif\" border=\"0\" align=\"middle\" alt=\"".$factarray["LATI"]." / ".$factarray["LONG"]."\" title=\"".$factarray["LATI"]." / ".$factarray["LONG"]."\" /></a>";
			if ($SPLIT_PLACES) {
				if (!function_exists("print_place_subfields")) require("includes/functions_places.php");
				setup_place_subfields($element_id);
				print_place_subfields($element_id);
			}
		}
		else if (($cols>20 || $fact=="NPFX") && $readOnly=="") print_specialchar_link($element_id, false);
	}
	// MARRiage TYPE : hide text field and show a selection list
	if ($fact=="TYPE" and $tags[0]=="MARR") {
		print "<script type='text/javascript'>";
		print "document.getElementById('".$element_id."').style.display='none'";
		print "</script>";
		print "<select tabindex=\"".$tabkey."\" id=\"".$element_id."_sel\" onchange=\"document.getElementById('".$element_id."').value=this.value;\" >\n";
		foreach (array("Unknown", "Civil", "Religious", "Partners") as $indexval => $key) {
			if ($key=="Unknown") print "<option value=\"\"";
			else print "<option value=\"".$key."\"";
			$a=strtolower($key);
			$b=strtolower($value);
			if (@strpos($a, $b)!==false or @strpos($b, $a)!==false) print " selected=\"selected\"";
			print ">".$factarray["MARR_".strtoupper($key)]."</option>\n";
		}
		print "</select>";
	}

	// popup links
	if ($readOnly=="") {
		if ($fact=="DATE") print_calendar_popup($element_id);
		if ($fact=="FAMC") print_findfamily_link($element_id, "");
		if ($fact=="FAMS") print_findfamily_link($element_id, "");
		if ($fact=="ASSO") print_findindi_link($element_id, get_person_name($value));
		if ($fact=="FILE") print_findmedia_link($element_id, "0file");
		if ($fact=="SOUR") {
			print_findsource_link($element_id);
			print_addnewsource_link($element_id);
			//print_autopaste_link($element_id, array("S1", "S2"), false, false, true);
			//-- checkboxes to apply '1 SOUR' to BIRT/MARR/DEAT as '2 SOUR'
			if ($level==1) {
				echo "<br />";
				echo "<input type=\"hidden\" id=\"SOUR_BIRT\" name=\"SOUR_BIRT\" value=\"\" />";
				echo "<input type=\"hidden\" id=\"SOUR_MARR\" name=\"SOUR_MARR\" value=\"\" />";
				echo "<input type=\"hidden\" id=\"SOUR_FAM\" name=\"SOUR_FAM\" value=\"\" />";
				echo "<input type=\"hidden\" id=\"SOUR_DEAT\" name=\"SOUR_DEAT\" value=\"\" />";
				if (strpos($bdm, "B")!==false) {
					echo "&nbsp;<input type=\"checkbox\" name=\"SOUR_INDI\" checked=\"checked\" value=\"Y\" />";
					echo $pgv_lang['individual'];
					echo "&nbsp;<input type=\"checkbox\" onclick=\"if (this.checked) SOUR_BIRT.value='Y'; else SOUR_BIRT.value='';\" />";
					echo $factarray["BIRT"];
				}
				if (strpos($bdm, "M")!==false) {
					echo "&nbsp;<input type=\"checkbox\" onclick=\"if (this.checked) SOUR_MARR.value='Y'; else SOUR_MARR.value='';\" />";
					echo $factarray["MARR"];
					echo "&nbsp;<input type=\"checkbox\" onclick=\"if (this.checked) SOUR_FAM.value='Y'; else SOUR_FAM.value='';\" />";
					echo $pgv_lang["family"];
				}
				if (strpos($bdm, "D")!==false) {
					echo "&nbsp;<input type=\"checkbox\" onclick=\"if (this.checked) SOUR_DEAT.value='Y'; else SOUR_DEAT.value='';\" />";
					echo $factarray["DEAT"];
				}
			}
		}
		if ($fact=="REPO") {
			print_findrepository_link($element_id);
			print_addnewrepository_link($element_id);
		}
		if ($fact=="OBJE") print_findmedia_link($element_id, "1media");
		if ($fact=="OBJE" && !$value) {
			print '<br /><a href="javascript:;" onclick="pastefield=document.getElementById(\''.$element_id.'\'); window.open(\'addmedia.php?action=showmediaform&amp;linktoid='.$linkToID.'&amp;level='.$level.'\', \'_blank\', \'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1\'); return false;">'.$pgv_lang["add_media"].'</a>';
			$value = "new";
		}
	}

	// current value
	if ($TEXT_DIRECTION=="ltr") {
		if ($fact=="DATE") {
			$date=new GedcomDate($value);
			print $date->Display(false);
		}
		if ($fact=="ASSO" and $value) print " ".PrintReady(get_person_name($value))." (".$value.")";
		if ($fact=="SOUR" and $value) print " ".PrintReady(get_source_descriptor($value))." (".$value.")";
	} else {
		if ($fact=="DATE") {
			$date=new GedcomDate($value);
			print getRLM().$date->Display(false).getRLM();
		}
		if ($fact=="ASSO" and $value) print " " . getRLM() . PrintReady(get_person_name($value))." (".$value.")" . getRLM();
		if ($fact=="SOUR" and $value) print " " . getRLM() .PrintReady(get_source_descriptor($value)). getRLM() . "&nbsp;&nbsp;" . getLRM() . "(".$value.")" . getLRM();
	}

	// pastable values
	if ($readOnly=="") {
		if ($fact=="NPFX") {
			$text = $pgv_lang["autocomplete"];
			if (isset($PGV_IMAGES["autocomplete"]["button"])) $Link = "<img id=\"".$element_id."_spec\" name=\"".$element_id."_spec\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["autocomplete"]["button"]."\"  alt=\"".$text."\"  title=\"".$text."\" border=\"0\" align=\"middle\" />";
			else $Link = $text;
			print "&nbsp;".$Link;
		}
		if ($fact=="SPFX") print_autopaste_link($element_id, $SPFX_accept);
		if ($fact=="NSFX") print_autopaste_link($element_id, $NSFX_accept);
		if ($fact=="FORM") print_autopaste_link($element_id, $FILE_FORM_accept, false, false);
	}

	if ($noClose != "NOCLOSE") print "</td></tr>\n";

	$tabkey++;
	return $element_id;
}

/**
 * prints collapsable fields to add ASSO/RELA, SOUR, OBJE ...
 *
 * @param string $tag		Gedcom tag name
 */
function print_add_layer($tag, $level=2, $printSaveButton=true) {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	global $MEDIA_DIRECTORY, $TEXT_DIRECTION, $PRIVACY_BY_RESN;
	global $gedrec;
	if ($tag=="SOUR") {
		//-- Add new source to fact
		print "<a href=\"javascript:;\" onclick=\"return expand_layer('newsource');\"><img id=\"newsource_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_source"]."</a>";
		print_help_link("edit_add_SOUR_help", "qm");
		print "<br />";
		print "<div id=\"newsource\" style=\"display: none;\">\n";
		if ($printSaveButton) print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		// 2 SOUR
		$source = "SOUR @";
		add_simple_tag("$level $source");
		// 3 PAGE
		$page = "PAGE";
		add_simple_tag(($level+1)." $page");
		// 3 DATA
		// 4 TEXT
		// 4 DATE
		$text = "TEXT";
		add_simple_tag(($level+2)." $text");
		add_simple_tag(($level+2)." DATE", "", $pgv_lang["date_of_entry"]);
		// 3 QUAY
		add_simple_tag(($level+1)." QUAY");
		// 3 OBJE
		add_simple_tag(($level+1)." OBJE @@");
		print "</table></div>";
	}
	if ($tag=="ASSO") {
		//-- Add a new ASSOciate
		print "<a href=\"javascript:;\" onclick=\"return expand_layer('newasso');\"><img id=\"newasso_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_asso"]."</a>";
		print_help_link("edit_add_ASSO_help", "qm");
		print "<br />";
		print "<div id=\"newasso\" style=\"display: none;\">\n";
		if ($printSaveButton) print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		// 2 ASSO
		add_simple_tag(($level)." ASSO @");
		// 3 RELA
		add_simple_tag(($level+1)." RELA");
		// 3 NOTE
		add_simple_tag(($level+1)." NOTE");
		print "</table></div>";
	}
	if ($tag=="NOTE") {
		//-- Retrieve existing note or add new note to fact
		$text = "";
		print "<a href=\"javascript:;\" onclick=\"return expand_layer('newnote');\"><img id=\"newnote_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_note"]."</a>";
		print_help_link("edit_add_NOTE_help", "qm");
		print "<br />\n";
		print "<div id=\"newnote\" style=\"display: none;\">\n";
		if ($printSaveButton) print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		// 2 NOTE
		add_simple_tag(($level)." NOTE ".$text);
		print "</table></div>";
	}
	if ($tag=="OBJE") {
		//-- Add new obje to fact
		print "<a href=\"javascript:;\" onclick=\"return expand_layer('newobje');\"><img id=\"newobje_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_obje"]."</a>";
		print_help_link("add_media_help", "qm");
		print "<br />";
		print "<div id=\"newobje\" style=\"display: none;\">\n";
		if ($printSaveButton) print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		add_simple_tag($level." OBJE @@");
		print "</table></div>";
	}
	if ($tag=="RESN") {
		if (!$PRIVACY_BY_RESN && $level==1) {
			// PRIVACY_BY_RESN is not active for level 1 tags
			// do not display
		} else {
			//-- Retrieve existing resn or add new resn to fact
			$text = "";
			print "<a href=\"javascript:;\" onclick=\"return expand_layer('newresn');\"><img id=\"newresn_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$factarray["RESN"]."</a>";
			print_help_link("RESN_help", "qm");
			print "<br />\n";
			print "<div id=\"newresn\" style=\"display: none;\">\n";
			if ($printSaveButton) print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
			print "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
			// 2 RESN
			add_simple_tag(($level)." RESN ".$text);
			print "</table></div>";
		}
	}
}
/**
 * Add Debug Log
 *
 * This function checks the if the global $DEBUG
 * variable is true and adds debugging information
 * to the log file
 * @param string $logstr	the string to add to the log
 */
function addDebugLog($logstr) {
	global $DEBUG;
	if ($DEBUG) AddToChangeLog($logstr);
}

/**
 * This function splits the $glevels, $tag, $islink, and $text arrays so that the
 * entries associated with a SOUR record are separate from everything else.
 *
 * Input arrays:
 * - $glevels[] - an array of the gedcom level for each line that was edited
 * - $tag[] - an array of the tags for each gedcom line that was edited
 * - $islink[] - an array of 1 or 0 values to indicate when the text is a link element
 * - $text[] - an array of the text data for each line
 *
 * Output arrays:
 * ** For the SOUR record:
 * - $glevelsSOUR[] - an array of the gedcom level for each line that was edited
 * - $tagSOUR[] - an array of the tags for each gedcom line that was edited
 * - $islinkSOUR[] - an array of 1 or 0 values to indicate when the text is a link element
 * - $textSOUR[] - an array of the text data for each line
 * ** For the remaining records:
 * - $glevelsRest[] - an array of the gedcom level for each line that was edited
 * - $tagRest[] - an array of the tags for each gedcom line that was edited
 * - $islinkRest[] - an array of 1 or 0 values to indicate when the text is a link element
 * - $textRest[] - an array of the text data for each line
 *
 */
function splitSOUR() {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	$glevelsSOUR = array();
	$tagSOUR = array();
	$islinkSOUR = array();
	$textSOUR = array();

	$glevelsRest = array();
	$tagRest = array();
	$islinkRest = array();
	$textRest = array();

	$inSOUR = false;

	for ($i=0; $i<count($glevels); $i++) {
		if ($inSOUR) {
			if ($levelSOUR<$glevels[$i]) {
				$dest = "S";
			} else {
				$inSOUR = false;
				$dest = "R";
			}
		} else {
			if ($tag[$i]=="SOUR") {
				$inSOUR = true;
				$levelSOUR = $glevels[$i];
				$dest = "S";
			} else {
				$dest = "R";
			}
		}
		if ($dest=="S") {
			$glevelsSOUR[] = $glevels[$i];
			$tagSOUR[] = $tag[$i];
			$islinkSOUR[] = $islink[$i];
			$textSOUR[] = $text[$i];
		} else {
			$glevelsRest[] = $glevels[$i];
			$tagRest[] = $tag[$i];
			$islinkRest[] = $islink[$i];
			$textRest[] = $text[$i];
		}
	}
}

/**
 * Add new GEDCOM lines from the $xxxSOUR interface update arrays, which
 * were produced by the splitSOUR() function.
 *
 * See the handle_updates() function for details.
 *
 */
function updateSOUR($inputRec, $levelOverride="no") {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	if (count($tagSOUR)==0) return $inputRec;		// No update required
	
	// Save original interface update arrays before replacing them with the xxxSOUR ones
	$glevelsSave = $glevels;
	$tagSave = $tag;
	$islinkSave = $islink;
	$textSave = $text;

	$glevels = $glevelsSOUR;
	$tag = $tagSOUR;
	$islink = $islinkSOUR;
	$text = $textSOUR;
	
	$myRecord = handle_updates($inputRec, $levelOverride);		// Now do the update
	
	// Restore the original interface update arrays (just in case ...)
	$glevels = $glevelsSave;
	$tag = $tagSave;
	$islink = $islinkSave;
	$text = $textSave;

	return $myRecord;
}

/**
 * Add new GEDCOM lines from the $xxxRest interface update arrays, which
 * were produced by the splitSOUR() function.
 *
 * See the handle_updates() function for details.
 *
 */
function updateRest($inputRec, $levelOverride="no") {
	global $glevels, $tag, $islink, $text;
	global $glevelsSOUR, $tagSOUR, $islinkSOUR, $textSOUR;
	global $glevelsRest, $tagRest, $islinkRest, $textRest;

	if (count($tagRest)==0) return $inputRec;		// No update required
	
	// Save original interface update arrays before replacing them with the xxxRest ones
	$glevelsSave = $glevels;
	$tagSave = $tag;
	$islinkSave = $islink;
	$textSave = $text;

	$glevels = $glevelsRest;
	$tag = $tagRest;
	$islink = $islinkRest;
	$text = $textRest;
	
	$myRecord = handle_updates($inputRec, $levelOverride);		// Now do the update
	
	// Restore the original interface update arrays (just in case ...)
	$glevels = $glevelsSave;
	$tag = $tagSave;
	$islink = $islinkSave;
	$text = $textSave;

	return $myRecord;
}

/**
 * Add new gedcom lines from interface update arrays
 * The edit_interface and add_simple_tag function produce the following
 * arrays incoming from the $_POST form
 * - $glevels[] - an array of the gedcom level for each line that was edited
 * - $tag[] - an array of the tags for each gedcom line that was edited
 * - $islink[] - an array of 1 or 0 values to tell whether the text is a link element and should be surrounded by @@
 * - $text[] - an array of the text data for each line
 * With these arrays you can recreate the gedcom lines like this
 * <code>$glevel[0]." ".$tag[0]." ".$text[0]</code>
 * There will be an index in each of these arrays for each line of the gedcom
 * fact that is being edited.
 * If the $text[] array is empty for the given line, then it means that the
 * user removed that line during editing or that the line is supposed to be
 * empty (1 DEAT, 1 BIRT) for example.  To know if the line should be removed
 * there is a section of code that looks ahead to the next lines to see if there
 * are sub lines.  For example we don't want to remove the 1 DEAT line if it has
 * a 2 PLAC or 2 DATE line following it.  If there are no sub lines, then the line
 * can be safely removed.
 * @param string $newged	the new gedcom record to add the lines to
 * @param int $levelOverride	Override GEDCOM level specified in $glevels[0]
 * @return string	The updated gedcom record
 */
function handle_updates($newged, $levelOverride="no") {
	global $glevels, $islink, $tag, $uploaded_files, $text, $NOTE, $WORD_WRAPPED_NOTES;
	
	if ($levelOverride=="no" || count($glevels)==0) $levelAdjust = 0;
	else $levelAdjust = $levelOverride - $glevels[0];

	for($j=0; $j<count($glevels); $j++) {
		//-- update external note records first
		if (($islink[$j])&&($tag[$j]=="NOTE")) {
			if (empty($NOTE[$text[$j]])) {
				delete_gedrec($text[$j]);
				$text[$j] = "";
			} else {
				$noterec = find_gedcom_record($text[$j]);
				$newnote = "0 @$text[$j]@ NOTE\r\n";
				$newline = "1 CONC ".rtrim(stripLRMRLM($NOTE[$text[$j]]));
				$newnote .= breakConts($newline);
				if ($GLOBALS["DEBUG"]) print "<pre>$newnote</pre>";
				replace_gedrec($text[$j], $newnote);
			}
		} //-- end of external note handling code

		//print $glevels[$j]." ".$tag[$j];

		// Look for empty SOUR reference with non-empty sub-records.
		// This can happen when the SOUR entry is deleted but its sub-records
		// were incorrectly left intact.
		// The sub-records should be deleted.
		if ($tag[$j]=="SOUR" && ($text[$j]=="@@" || $text[$j]=="")) {
			$text[$j] = "";
			$k = $j+1;
			while(($k<count($glevels))&&($glevels[$k]>$glevels[$j])) {
				$text[$k] = "";
				$k++;
			}
		}

//		if (!empty($text[$j])) {
		if (trim($text[$j])!='') {
			$pass = true;
		}
		else {
			//-- for facts with empty values they must have sub records
			//-- this section checks if they have subrecords
			$k=$j+1;
			$pass=false;
			while(($k<count($glevels))&&($glevels[$k]>$glevels[$j])) {
				if (!empty($text[$k])) {
					if (($tag[$j]!="OBJE")||($tag[$k]=="FILE")) {
						$pass=true;
						break;
					}
				}
				if (($tag[$k]=="FILE")&&(count($uploaded_files)>0)) {
					$filename = array_shift($uploaded_files);
					if (!empty($filename)) {
						$text[$k] = $filename;
						$pass=true;
						break;
					}
				}
				$k++;
			}
		}

		//-- if the value is not empty or it has sub lines
		//--- then write the line to the gedcom record
		//if ((($text[trim($j)]!="")||($pass==true)) && (strlen($text[$j]) > 0)) {
		//-- we have to let some emtpy text lines pass through... (DEAT, BIRT, etc)
		if ($pass==true) {
			$newline = $glevels[$j]+$levelAdjust." ".$tag[$j];
			//-- check and translate the incoming dates
			if ($tag[$j]=="DATE" && !empty($text[$j])) {
				$text[$j] = check_input_date($text[$j]);
			}
			// print $newline;
			if ($text[$j]!="") {
				if ($islink[$j]) $newline .= " @".$text[$j]."@";
				else $newline .= " ".$text[$j];
			}
			$newged .= breakConts($newline);
		}
	}

	return $newged;
}


/**
 * check the given date that was input by a user and convert it
 * to proper gedcom date if possible
 * @author John Finlay
 * @param string $datestr	the date input by the user
 * @return string	the converted date string
 */
function check_input_date($datestr) {
	global $lang_short_cut, $LANGUAGE;
	// Convert from natural language to gedcom format
	$conversion_function="edit_to_gedcom_date_{$lang_short_cut[$LANGUAGE]}";
	if (function_exists($conversion_function))
		$datestr=$conversion_function($datestr);
	else
		$datestr=default_edit_to_gedcom_date($datestr);

	return trim($datestr);
}

function print_quick_resn($name) {
	global $SHOW_QUICK_RESN, $align, $factarray, $pgv_lang, $tabkey;

	if ($SHOW_QUICK_RESN) {
		print "<tr><td class=\"descriptionbox\">";
		print_help_link("RESN_help", "qm");
		print $factarray["RESN"];
		print "</td>\n";
		print "<td class=\"optionbox\" colspan=\"3\">\n";
		print "<select name=\"$name\" tabindex=\"".$tabkey."\" ><option value=\"\"></option><option value=\"confidential\"";
		$tabkey++;
		print ">".$pgv_lang["confidential"]."</option><option value=\"locked\"";
		print ">".$pgv_lang["locked"]."</option><option value=\"privacy\"";
		print ">".$pgv_lang["privacy"]."</option>";
		print "</select>\n";
		print "</td>\n";
		print "</tr>\n";
	}
}


/**
 * Link Media ID to Indi, Family, or Source ID
 *
 * Code was removed from inverselink.php to become a callable function
 *
 * @param 	string 	$mediaid	Media ID to be linked
 * @param	string	$linktoid	Indi, Family, or Source ID that the Media ID should link to
 * @param	int		$level		Level where the Media Object reference should be created
 * @return 	bool				success or failure
 */
function linkMedia($mediaid, $linktoid, $level=1) {
	global $GEDCOM, $pgv_lang, $pgv_changes;

	if (empty($level)) $level = 1;
	//-- Make sure we only add new links to the media object
	if (exists_db_link($mediaid, $linktoid, $GEDCOM)) return false;
	if ($level!=1) return false;		// Level 2 items get linked elsewhere
	// find Indi, Family, or Source record to link to
	if (isset($pgv_changes[$linktoid."_".$GEDCOM])) {
		$gedrec = find_updated_record($linktoid);
	} else {
		$gedrec = find_gedcom_record($linktoid);
	}

	//-- check if we are re-editing an unaccepted link that is not already in the DB
	$ct = preg_match("/1 OBJE @$mediaid@/", $gedrec);
	if ($ct>0) return false;

	if ($gedrec) {
		// Changed to match format of all other data adds.
		//$mediarec = "1 OBJE @".$mediaid."@\r\n";
		//$newrec = trim($gedrec."\r\n".$mediarec);
		$newrec = $gedrec."\r\n1 OBJE @".$mediaid."@";

		replace_gedrec($linktoid, $newrec);

		return true;
	} else {
		print "<br /><center>".$pgv_lang["invalid_id"]."</center>";
		return false;
	}
}

/**
 * builds the form for adding new facts
 * @param string $fact	the new fact we are adding
 */
function create_add_form($fact) {
	global $tags, $pgv_lang;

	$tags = array();

	// handle  MARRiage TYPE
	if (substr($fact,0,5)=="MARR_") {
		$tags[0] = "MARR";
		add_simple_tag("1 MARR");
		insert_missing_subtags($fact);
	}
	else {
		$tags[0] = $fact;
		if ($fact=='_UID') {
			require_once ("functions_import.php");
			$fact.=" ".uuid();
		}
		add_simple_tag("1 ".$fact);
		insert_missing_subtags($tags[0]);
		//-- handle the special SOURce case for level 1 sources [ 1759246 ] 
		if ($fact=="SOUR") {
			add_simple_tag("2 PAGE");
			add_simple_tag("3 TEXT");
			add_simple_tag("3 DATE", "", $pgv_lang["date_of_entry"]);
			add_simple_tag("2 QUAY");
		}
	}
}

/**
 * creates the form for editing the fact within the given gedcom record at the
 * given line number
 * @param string $gedrec	the level 0 gedcom record
 * @param int $linenum		the line number of the fact to edit within $gedrec
 * @param string $level0type	the type of the level 0 gedcom record
 */
function create_edit_form($gedrec, $linenum, $level0type) {
	global $WORD_WRAPPED_NOTES, $pgv_lang, $factarray;
	global $tags, $ADVANCED_PLAC_FACTS, $date_and_time, $templefacts;
	global $lang_short_cut, $LANGUAGE;

	$gedlines = split("\n", $gedrec);	// -- find the number of lines in the record
	if (!isset($gedlines[$linenum])) {
		print "<span class=\"error\">".$pgv_lang["edit_concurrency_msg1"]."<br /><br />";
		print $pgv_lang["edit_concurrency_reload"]."</span>";
		return;
	}
	$fields = preg_split("/\s/", $gedlines[$linenum]);
	$glevel = $fields[0];
	$level = $glevel;
	if ($level!=1) {
		print "<span class=\"error\">".$pgv_lang["edit_concurrency_msg1"]."<br /><br />";
		print $pgv_lang["edit_concurrency_reload"]."</span>";
		return;
	}
	$type = trim($fields[1]);
	$level1type = $type;
	if (count($fields)>2) {
		$ct = preg_match("/@.*@/",$fields[2]);
		$levellink = $ct > 0;
	}
	else $levellink = false;
	$tags=array();
	$i = $linenum;
	$inSource = false;
	$levelSource = 0;
	// List of tags we would expect at the next level
	// NB add_missing_subtags() already takes care of the simple cases
	// where a level 1 tag is missing a level 2 tag.  Here we only need to
	// handle the more complicated cases.
	$expected_subtags=array(
		'SOUR'=>array('PAGE', 'DATA', 'QUAY'),
		'DATA'=>array('TEXT', 'DATE'),
		'PLAC'=>array('MAP'),
		'MAP' =>array('LATI', 'LONG')
	);
	if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_PLAC_FACTS, $match))
		$expected_subtags['PLAC']=array_merge($match[1], $expected_subtags['PLAC']);

	// Loop on existing tags :
	while (true) {
		$text = "";
		for($j=2; $j<count($fields); $j++) {
			if ($j>2) $text .= " ";
			$text .= $fields[$j];
		}
		$text = rtrim($text);
		while(($i+1<count($gedlines))&&(preg_match("/".($level+1)." (CON[CT])\s?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
			if ($cmatch[1]=="CONT") $text.="\n";
			else if ($WORD_WRAPPED_NOTES) $text .= " ";
			$conctxt = $cmatch[2];
			$conctxt = preg_replace("/[\r\n]/","",$conctxt);
			$text.=$conctxt;
			$i++;
		}

		if ($type=="SOUR") {
			$inSource = true;
			$levelSource = $level;
		}

		if ($type!="DATA" && $type!="CONC" && $type!="CONT") {
			$tags[]=$type;
			if ($type=='DATE') {
				// Allow the user to edit the date in his/her own natural language
				$conversion_function="gedcom_to_edit_date_{$lang_short_cut[$LANGUAGE]}";
				if (function_exists($conversion_function))
					$text=$conversion_function($text);
				else
					$text=default_gedcom_to_edit_date($text);
			}
			$subrecord = $level." ".$type." ".$text;
			if ($inSource && $type=="DATE") add_simple_tag($subrecord, "", $pgv_lang["date_of_entry"]);
			else add_simple_tag($subrecord, $level0type);
		}

		// Get a list of tags present at the next level
		$subtags=array();
		for ($ii=$i+1; isset($gedlines[$ii]) && preg_match('/^\s*(\d+)\s+(\S+)/', $gedlines[$ii], $mm) && $mm[1]>$level; ++$ii)
			if ($mm[1]==$level+1)
				$subtags[]=$mm[2];

		// Insert missing tags
		if (!empty($expected_subtags[$type]))
			foreach ($expected_subtags[$type] as $subtag)
				if (!in_array($subtag, $subtags)) {
					if (!$inSource || $subtag!="DATA") {
						add_simple_tag(($level+1).' '.$subtag);
					}
					if (!empty($expected_subtags[$subtag]))
						foreach ($expected_subtags[$subtag] as $subsubtag)
							if ($inSource && $subsubtag=="DATE") add_simple_tag(($level+2).' '.$subsubtag, "", $pgv_lang["date_of_entry"]);
							else add_simple_tag(($level+2).' '.$subsubtag);
				}

		// Awkward special cases
		if ($level==2 && $type=='DATE' && in_array($level1type, $date_and_time) && !in_array('TIME', $subtags))
			add_simple_tag("3 TIME"); // TIME is NOT a valid 5.5.1 tag
		if ($level==2 && $type=='STAT' && in_array($level1type, $templefacts) && !in_array('DATE', $subtags))
			add_simple_tag("3 DATE", "", $factarray['STAT:DATE']);

		$i++;
		if (isset($gedlines[$i])) {
			$fields = preg_split("/\s/", $gedlines[$i]);
			$level = $fields[0];
			if (isset($fields[1])) $type = trim($fields[1]);
			else $level = 0;
		} else $level = 0;

		if ($level<=$glevel) break;

	}

	insert_missing_subtags($level1type);
	return $level1type;
}

/**
 * Populates the global $tags array with any missing sub-tags.
 * @param string $level1tag	the type of the level 1 gedcom record
 */
function insert_missing_subtags($level1tag)
{
	global $tags, $date_and_time, $templefacts, $level2_tags, $ADVANCED_PLAC_FACTS, $factarray;

	// handle  MARRiage TYPE
	$type_val = "";
	if (substr($level1tag,0,5)=="MARR_") {
		$type_val = substr($level1tag,5);
		$level1tag = "MARR";
	}
	
	foreach ($level2_tags as $key=>$value) {
		if (in_array($level1tag, $value) && !in_array($key, $tags)) {
			if ($key=="TYPE") add_simple_tag("2 TYPE ".$type_val);
			else add_simple_tag("2 ".$key);
			switch ($key) { // Add level 3/4 tags as appropriate
				case "PLAC":
					if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_PLAC_FACTS, $match))
						foreach ($match[1] as $tag)
							add_simple_tag("3 $tag");
					add_simple_tag("3 MAP");
					add_simple_tag("4 LATI");
					add_simple_tag("4 LONG");
					break;
				case "FILE":
					add_simple_tag("3 FORM");
					break;
				case "EVEN":
					add_simple_tag("3 DATE");
					add_simple_tag("3 PLAC");
					break;
				case "STAT":
					if (in_array($level1tag, $templefacts))
						add_simple_tag("3 DATE", "", $factarray['STAT:DATE']);
					break;
				case "DATE":
					if (in_array($level1tag, $date_and_time))
						add_simple_tag("3 TIME"); // TIME is NOT a valid 5.5.1 tag
					break;
				case "HUSB":
				case "WIFE":
					add_simple_tag("3 AGE");
					break;
				case "FAMC":
					if ($level1tag=='ADOP')
						add_simple_tag("3 ADOP BOTH");
					break;
			}
		}
	}
	// Do something (anything!) with unrecognised custom tags
	if (substr($level1tag, 0, 1)=='_' && $level1tag!='_UID')
		foreach (array('DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE') as $tag)
			if (!in_array($tag, $tags)) {
				add_simple_tag("2 {$tag}");
				if ($tag=='PLAC') {
					if (preg_match_all('/([A-Z0-9_]+)/', $ADVANCED_PLAC_FACTS, $match))
						foreach ($match[1] as $atag)
							add_simple_tag("3 $atag");
					add_simple_tag("3 MAP");
					add_simple_tag("4 LATI");
					add_simple_tag("4 LONG");

				}
			}
}

/**
 * Delete a person and update all records that link to that person
 * @param string $pid	the id of the person to delete
 * @param string $gedrec	the gedcom record of the person to delete
 * @return boolean	true or false based on the successful completion of the deletion
 */
function delete_person($pid, $gedrec='') {
	global $pgv_lang, $GEDCOM;
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";

	if (empty($gedrec)) $gedrec = find_person_record($pid);
	if (!empty($gedrec)) {
		$success = true;
		$ct = preg_match_all("/1 FAM. @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_gedcom_record($famid);
			else $famrec = find_updated_record($famid);
			if (!empty($famrec)) {
				$lines = preg_split("/\n/", $famrec);
				$newfamrec = "";
				$lastlevel = -1;
				foreach($lines as $indexval => $line) {
					$ct = preg_match("/^(\d+)/", $line, $levelmatch);
					if ($ct>0) $level = $levelmatch[1];
					else $level = 1;
					//-- make sure we don't add any sublevel records
					if ($level<=$lastlevel) $lastlevel = -1;
					if ((preg_match("/@$pid@/", $line)==0) && ($lastlevel==-1)) $newfamrec .= $line."\n";
					else {
						$lastlevel=$level;
					}
				}
				//-- if there is not at least two people in a family then the family is deleted
				$pt = preg_match_all("/1 (HUSB|WIFE|CHIL) @(.*)@/", $newfamrec, $pmatch, PREG_SET_ORDER);
				if ($pt<2) {
					for ($j=0; $j<$pt; $j++) {
						$xref = $pmatch[$j][1];
						if($xref!=$pid) {
							if (!isset($pgv_changes[$xref."_".$GEDCOM])) $indirec = find_gedcom_record($xref);
							else $indirec = find_updated_record($xref);
							$indirec = preg_replace("/1.*@$famid@.*/", "", $indirec);
							if ($GLOBALS["DEBUG"]) print "<pre>$indirec</pre>";
							replace_gedrec($xref, $indirec);
						}
					}
					$success = $success && delete_gedrec($famid);
				}
				else $success = $success && replace_gedrec($famid, $newfamrec);
			}
		}
		if ($success) {
			$success = $success && delete_gedrec($pid);
		}
		return $success;
	}
	return false;
}

/**
 * Delete a person and update all records that link to that person
 * @param string $pid	the id of the person to delete
 * @param string $gedrec	the gedcom record of the person to delete
 * @return boolean	true or false based on the successful completion of the deletion
 */
function delete_family($pid, $gedrec='') {
	global $GEDCOM, $pgv_lang;
	if (empty($gedrec)) $gedrec = find_family_record($pid);
	if (!empty($gedrec)) {
		$success = true;
		$ct = preg_match_all("/1 (\w+) @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$type = $match[$i][1];
			$id = $match[$i][2];
			if ($GLOBALS["DEBUG"]) print $type." ".$id." ";
			if (!isset($pgv_changes[$id."_".$GEDCOM])) $indirec = find_gedcom_record($id);
			else $indirec = find_updated_record($id);
			if (!empty($indirec)) {
				$lines = preg_split("/\n/", $indirec);
				$newindirec = "";
				$lastlevel = -1;
				foreach($lines as $indexval => $line) {
					$lct = preg_match("/^(\d+)/", $line, $levelmatch);
					if ($lct>0) $level = $levelmatch[1];
					else $level = 1;
					//-- make sure we don't add any sublevel records
					if ($level<=$lastlevel) $lastlevel = -1;
					if ((preg_match("/@$pid@/", $line)==0) && ($lastlevel==-1)) $newindirec .= $line."\n";
					else {
						$lastlevel=$level;
					}
				}
				$success = $success && replace_gedrec($id, $newindirec);
			}
		}
		if ($success) {
			$success = $success && delete_gedrec($pid);
		}
		return $success;
	}
	return false;
}
?>

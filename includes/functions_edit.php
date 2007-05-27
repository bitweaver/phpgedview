<?php
/**
 * Various functions used by the Edit interface
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id: functions_edit.php,v 1.3 2007/05/27 10:31:40 lsces Exp $
 */

if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
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

$NPFX_accept = array("Adm", "Amb", "Brig", "Can", "Capt", "Chan", "Chapln", "Cmdr", "Col", "Cpl", "Cpt", "Dr", "Gen", "Gov", "Hon", "Lady", "Lt", "Mr", "Mrs", "Ms", "Msgr", "Pfc", "Pres", "Prof", "Pvt", "Rep", "Rev", "Sen", "Sgt", "Sir", "Sr", "Sra", "Srta", "Ven");
$SPFX_accept = array("al", "da", "de", "den", "dem", "der", "di", "du", "el", "la", "van", "von");
$NSFX_accept = array("Jr", "Sr", "I", "II", "III", "IV", "MD", "PhD");
$FILE_FORM_accept = array("avi", "bmp", "gif", "jpeg", "mp3", "ole", "pcx", "png", "tiff", "wav");
$emptyfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","BAPL","CONL","ENDL","SLGC","EVEN","MARR","SLGS","MARL","ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARS","CHAN","_SEPR","RESI", "DATA", "MAP", "_HOL", "_NMR");
$templefacts = array("SLGC","SLGS","BAPL","ENDL","CONL");
$nonplacfacts = array("ENDL","NCHI","SLGC","SLGS");
$nondatefacts = array("ABBR","ADDR","AFN","AUTH","EMAIL","FAX","NAME","NCHI","NOTE","OBJE","PHON","PUBL","REFN","REPO","SEX","SOUR","SSN","TEXT","TITL","WWW","_EMAIL");
$typefacts = array();	//-- special facts that go on 2 TYPE lines

/**
 * read the contents of a gedcom file
 *
 * opens a gedcom file and reads the contents into the <var>$fcontents</var> global string
 */
function read_gedcom_file() {
	global $fcontents;
	global $GEDCOM, $GEDCOMS;
	global $pgv_lang;
	$fcontents = "";
	if (isset($GEDCOMS[$GEDCOM])) {
		file_locked_wait();
		$fp = fopen($GEDCOMS[$GEDCOM]["path"], "r");
		$fcontents = fread($fp, filesize($GEDCOMS[$GEDCOM]["path"]));
		fclose($fp);
	}
}

//-- read the file onto the stack
read_gedcom_file();

//-------------------------------------------- newConnection
//-- this function creates a new unique connection
//-- and adds it to the connections file
//-- it returns the connection identifier
function newConnection() {
	return session_name()."\t".session_id()."\n";
}

//-------------------------------------------- get_next_record
//-- gets the next person in the gedcom, if we reach the end then
//-- returns false
function get_next_xref($gid, $type='INDI') {
	global $GEDCOM, $myindilist, $pgv_changes;

	if (!isset($myindilist[$gid])) {
		print "ERROR 4: Could not find gedcom record with xref:$gid\n";
		AddToChangeLog("ERROR 4: Could not find gedcom record with xref:$gid ->" . getUserName() ."<-");
		return false;
	}
	$found = false;
	foreach($myindilist as $key=>$value) {
		if ($found) {
			return $key;
		}
		if ($key==$gid) $found=true;
	}
	//print "ERROR 14: Reached the end of the list\n";
	return "";
}

//-------------------------------------------- get_prev_record
//-- gets the previous person in the gedcom, if we reach the start then
//-- returns the last record
function get_prev_xref($gid, $type='INDI') {
	global $GEDCOM, $myindilist, $pgv_changes;

	if (!isset($myindilist[$gid])) {
		print "ERROR 4: Could not find gedcom record with xref:$gid\n";
		AddToChangeLog("ERROR 4: Could not find gedcom record with xref:$gid ->" . getUserName() ."<-");
		return false;
	}
	$found = false;
	$prevkey = "";
	foreach($myindilist as $key=>$value) {
		if ($key==$gid) $found=true;
		if ($found) {
			if (isset($prev)) {
				return $prevkey;
			}
			else {
				//print "ERROR 15: Reached the beginning of the list\n";
				return "";
			}
		}
		$prev = $value;
		$prevkey = $key;
	}
	//print "ERROR 14: Reached the end of the list\n";
	return "";
}

//-------------------------------------------- replace_gedrec
/**
 * This function will replace a gedcom record with
 * the id $gid with the $gedrec
 * @param string $gid	The XREF id of the record to replace
 * @param string $gedrec	The new gedcom record to replace with
 * @param boolean $chan		Whether or not to update/add the CHAN record
 * @param string $linkpid	Tells whether or not this record change is linked with the record change of another record identified by $linkpid
 */
function replace_gedrec($gid, $gedrec, $chan=true, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save, $gBitUser;

	$gid = strtoupper($gid);
	$pos1 = strpos($fcontents, "0 @".$gid."@");
	if ($pos1===false) {
		print "ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."\n";
		AddToChangeLog("ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."->" . getUserName() ."<-");
		if (function_exists('debug_print_backtrace')) debug_print_backtrace();
		return false;
	}
	//-- restore any data that was hidden during privatizing
	if (isset($pgv_private_records[$gid])) $gedrec = trim($gedrec)."\r\n".trim(get_last_private_data($gid));
	
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
		$pos2 = strpos($fcontents, "\n0", $pos1+1);
		if ($pos2===false) {
			$undo = substr($fcontents, $pos1);
			$fcontents = substr($fcontents, 0,$pos1)."\r\n".trim($gedrec)."\r\n0 TRLR\r\n";
		}
		else {
			$pos2++;
			$undo = substr($fcontents, $pos1, $pos2-$pos1);
			$fcontents = substr($fcontents, 0,$pos1).trim($gedrec)."\r\n".substr($fcontents, $pos2);
		}
		if (userAutoAccept()) {
			require_once("includes/functions_import.php");
			update_record($gedrec);
		}
		else {
			$change = array();
			$change["gid"] = $gid;
			$change["gedcom"] = $GEDCOM;
			$change["type"] = "replace";
			$change["status"] = "submitted";
			$change["user"] = $gBitUser->mUsername;
			$change["time"] = time();
			if (!empty($linkpid)) $change["linkpid"] = $linkpid;
			$change["undo"] = $undo;
			if (!isset($pgv_changes[$gid."_".$GEDCOM])) $pgv_changes[$gid."_".$GEDCOM] = array();
			$pgv_changes[$gid."_".$GEDCOM][] = $change;
		}
		if (!isset($manual_save) || ($manual_save==false)) {
			AddToChangeLog("Replacing gedcom record $gid ->" . $gBitUser->mUsername ."<-");
			return write_file();
		}
		else return true;
	}
	return false;
}

/**-------------------------------------------- append_gedrec
 *-- this function will append a new gedcom record at
 *-- the end of the gedcom file.
 */
function append_gedrec($gedrec, $chan=true, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save, $gBitUser;

	if (($gedrec = check_gedcom($gedrec, $chan))!==false) {
		$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
		$gid = $match[1];
		$type = trim($match[2]);

		if (preg_match("/\d+/", $gid)==0) $xref = get_new_xref($type);
		else $xref = $gid;
		$gedrec = preg_replace("/0 @(.*)@/", "0 @$xref@", $gedrec);
		$pos1 = strrpos($fcontents, "0");
		$fcontents = substr($fcontents, 0, $pos1).trim($gedrec)."\r\n".substr($fcontents, $pos1);
		if (userAutoAccept()) {
			require_once("includes/functions_import.php");
			update_record($gedrec);
		}
		else {
			$change = array();
			$change["gid"] = $xref;
			$change["gedcom"] = $GEDCOM;
			$change["type"] = "append";
			$change["status"] = "submitted";
			$change["user"] = $gBitUser->mUsername;
			$change["time"] = time();
			if (!empty($linkpid)) $change["linkpid"] = $linkpid;
			$change["undo"] = "";
			if (!isset($pgv_changes[$xref."_".$GEDCOM])) $pgv_changes[$xref."_".$GEDCOM] = array();
			$pgv_changes[$xref."_".$GEDCOM][] = $change;
		}
		AddToChangeLog("Appending new $type record $xref ->" . $gBitUser->mUsername ."<-");
		if (!isset($manual_save) || ($manual_save==false)) {
			if (write_file()) return $xref;
			else return false;
		}
		else return $xref;
	}
	return false;
}

//-------------------------------------------- delete_gedrec
//-- this function will delete the gedcom record with
//-- the given $gid
function delete_gedrec($gid, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save, $gBitUser;
	$pos1 = strpos($fcontents, "0 @$gid@");
	if ($pos1===false) {
		//-- first check if the record is not already deleted
		if (isset($pgv_changes[$gid."_".$GEDCOM])) {
			$change = end($pgv_changes[$gid."_".$GEDCOM]);
			if ($change["type"]=="delete") return true;
		}
		print "ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."\n";
		AddToChangeLog("ERROR 4: Could not find gedcom record with xref:$gid Line ".__LINE__."->" . $gBitUser->mUsername ."<-");
		return false;
	}
	$pos2 = strpos($fcontents, "\n0", $pos1+1);
	if ($pos2===false) $pos2=strpos($fcontents, "0 TRLR", $pos1+1);
	else $pos2++;
	$undo = substr($fcontents, $pos1, $pos2-$pos1);
	$fcontents = substr($fcontents, 0,$pos1).substr($fcontents, $pos2);
	if (userAutoAccept()) {
		require_once("includes/functions_import.php");
		update_record($undo, true);
	}
	else {
		$change = array();
		$change["gid"] = $gid;
		$change["gedcom"] = $GEDCOM;
		$change["type"] = "delete";
		$change["status"] = "submitted";
		$change["user"] = $gBitUser->mUsername;
		$change["time"] = time();
		if (!empty($linkpid)) $change["linkpid"] = $linkpid;
		$change["undo"] = $undo;
		if (!isset($pgv_changes[$gid."_".$GEDCOM])) $pgv_changes[$gid."_".$GEDCOM] = array();
		$pgv_changes[$gid."_".$GEDCOM][] = $change;
	}
	AddToChangeLog("Deleting gedcom record $gid ->" . $gBitUser->mUsername ."<-");
	if (!isset($manual_save)) return write_file();
	else return true;
}

//-------------------------------------------- check_gedcom
//-- this function will check a GEDCOM record for valid gedcom format
function check_gedcom($gedrec, $chan=true) {
	global $pgv_lang, $DEBUG, $USE_RTL_FUNCTIONS, $gBitUser;

	$gedrec = trim(stripslashes($gedrec));

	if ($USE_RTL_FUNCTIONS) {
		//-- replace any added ltr processing codes
//		$gedrec = preg_replace(array("/".html_entity_decode("&rlm;",ENT_COMPAT,"UTF-8")."/", "/".html_entity_decode("&lrm;",ENT_COMPAT,"UTF-8")."/"), array("",""), $gedrec);
		// Because of a bug in PHP 4, the above generates a run-time error message and does nothing.
		// see:  http://bugs.php.net/bug.php?id=25670
		// HTML entity &rlm; is the 3-byte UTF8 character 0xE2808F
		// HTML entity &lrm; is the 3-byte UTF8 character 0xE2808E
		$gedrec = str_replace(array(chr(0xE2).chr(0x80).chr(0x8F), chr(0xE2).chr(0x80).chr(0x8E)), "", $gedrec);
	}
	$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
	if ($ct==0) {
		print "ERROR 20: Invalid GEDCOM 5.5 format.\n";
		AddToChangeLog("ERROR 20: Invalid GEDCOM 5.5 format.->" . $gBitUser->mUsername ."<-");
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
			$newgedrec .= "2 _PGVU ".$gBitUser->mUsername."\r\n";
			$newgedrec .= substr($gedrec, $pos2);
			$gedrec = $newgedrec;
		}
		else {
			$newgedrec = "\r\n1 CHAN\r\n2 DATE ".strtoupper(date("d M Y"))."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU ".$gBitUser->mUsername;
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
	global $fcontents, $pgv_changes, $GEDCOMS, $GEDCOM, $manual_save, $gBitUser;

	if (isset($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[$index];
		$change["undo"] = $change["undo"];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
			read_gedcom_file();
		}
		if ($change["type"]=="delete") {
			$pos1 = strrpos($fcontents, "0");
			$fcontents = substr($fcontents, 0, $pos1).trim($change["undo"])."\r\n".substr($fcontents, $pos1);
		}
		else if ($change["type"]=="append") {
			$pos1 = strpos($fcontents, "0 @".$change["gid"]."@");
			if ($pos1===false) {
				print "ERROR 4: Could not find gedcom record with gid:".$change["gid"]."\n";
				AddToChangeLog("ERROR 4: Could not find gedcom record with gid:".$change["gid"]." ->" . $gBitUser->mUsername ."<-");
				return false;
			}
			$pos2 = strpos($fcontents, "\n0", $pos1+1);
			if ($pos2===false) $pos2=strpos($fcontents, "0 TRLR", $pos1+1);
			else $pos2++;
			if ($pos2!==false) $fcontents = substr($fcontents, 0,$pos1).substr($fcontents, $pos2);
		}
		else if ($change["type"]=="replace") {
			$pos1 = strpos($fcontents, "0 @".$change["gid"]."@");
			if ($pos1===false) {
				$ct = preg_match("/0 @(.*)@/", $change["undo"], $match);
				if ($ct>0) {
					$gid = trim($match[1]);
					$pos1 = strpos($fcontents, "0 @".$gid."@");
				}
			}
			if ($pos1===false) {
				//print "ERROR 4: Could not find gedcom record with gid:".$change["gid"]."\n";
				//return false;
				if (!empty($change["undo"])) {
					$fcontents .= "\r\n".$change["undo"];
				}
			}
			else {
				$pos2 = strpos($fcontents, "\n0", $pos1+1);
				if ($pos2===false) $pos2=strpos($fcontents, "0 TRLR", $pos1+1);
				else $pos2++;
				$fcontents = substr($fcontents, 0,$pos1).trim($change["undo"])."\r\n".substr($fcontents, $pos2);
			}
		}
		if ($index==0) unset($pgv_changes[$cid]);
		else {
			for($i=$index; $i<count($pgv_changes[$cid]); $i++) {
				unset($pgv_changes[$cid][$i]);
			}
			if (count($pgv_changes[$cid])==0) unset($pgv_changes[$cid]);
		}
		AddToChangeLog("Undoing change $cid - $index ".$change["type"]." ->" . $gBitUser->mUsername ."<-");
		if (!isset($manual_save) || ($manual_save==false)) {
			return write_file();
		}
		else return true;
	}
	return false;
}

//-------------------------------------------- write_file
//-- this function writes the $fcontents back to the
//-- gedcom file
function write_file() {
	global $fcontents, $GEDCOMS, $GEDCOM, $pgv_changes, $INDEX_DIRECTORY, $gBitUser;

	if (preg_match("/0 TRLR/", $fcontents)==0) $fcontents.="0 TRLR\n";
	//-- write the gedcom file
	if (!is_writable($GEDCOMS[$GEDCOM]["path"])) {
		print "ERROR 5: GEDCOM file is not writable.  Unable to complete request.\n";
		AddToChangeLog("ERROR 5: GEDCOM file is not writable.  Unable to complete request. ->" . $gBitUser->mUsername ."<-");
		return false;
	}
	lock_file();
	$fp = fopen($GEDCOMS[$GEDCOM]["path"], "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request.\n";
		AddToChangeLog("ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request. ->" . $gBitUser->mUsername ."<-");
		return false;
	}
// 	$fl = flock($fp, LOCK_EX);
// 	if (!$fl) {
// 		print "ERROR 7: Unable to obtain file lock.\n";
// 		AddToChangeLog("ERROR 7: Unable to obtain file lock. ->" . $gBitUser->mUsername ."<-");
// 		fclose($fp);
// 		return false;
// 	}
	$fw = fwrite($fp, $fcontents);
	if ($fw===false) {
		print "ERROR 7: Unable to write to GEDCOM file.\n";
		AddToChangeLog("ERROR 7: Unable to write to GEDCOM file. ->" . $gBitUser->mUsername ."<-");
//		$fl = flock($fp, LOCK_UN);
		fclose($fp);
		return false;
	}
//	$fl = flock($fp, LOCK_UN);
	fclose($fp);
	unlock_file();
	$logline = AddToLog($GEDCOMS[$GEDCOM]["path"]." updated by >".$gBitUser->mUsername."<");
 	if (!empty($COMMIT_COMMAND)) check_in($logline, basename($GEDCOMS[$GEDCOM]["path"]), dirname($GEDCOMS[$GEDCOM]["path"]));

	return write_changes();
}

/**
 * obtain a lock on the current GEDCOM file
 */
function lock_file() {
	global $GEDCOMS, $GEDCOM, $INDEX_DIRECTORY;

	file_locked_wait();
	$fp = fopen($INDEX_DIRECTORY.$GEDCOM.".lock", "wb");
	fclose($fp);
}

/**
 * block until the file lock is released
 */
function file_locked_wait() {
	global $GEDCOMS, $GEDCOM, $INDEX_DIRECTORY;

	$sleep_count = 0;
	while(file_exists($INDEX_DIRECTORY.$GEDCOM.".lock") && $sleep_count<100) {
		usleep(100000);
		$sleep_count++;
	}
	if ($sleep_count>100) {
		print "ERROR 30: Unable to obtain lock on file after 10 seconds.";
		debug_print_backtrace();
		AddToChangeLog("ERROR 30: Unable to obtain lock on file after 10 seconds. ->" . $gBitUser->mUsername ."<-");
		exit;
	}
}

/**
 * unlock the GEDCOM file
 */
function unlock_file() {
	global $GEDCOMS, $GEDCOM, $INDEX_DIRECTORY;

	@unlink($INDEX_DIRECTORY.$GEDCOM.".lock");
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
	global $pgv_lang, $factarray, $pid, $PGV_IMAGE_DIR, $PGV_IMAGES, $monthtonum, $WORD_WRAPPED_NOTES;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $USE_RTL_FUNCTIONS, $GEDCOM;

	init_calendar_popup();
	print "<form method=\"post\" name=\"addchildform\" onsubmit=\"return checkform();\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"$nextaction\" />\n";
	print "<input type=\"hidden\" name=\"linenum\" value=\"$linenum\" />\n";
	print "<input type=\"hidden\" name=\"famid\" value=\"$famid\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	print "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\" />\n";

	print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";

	print "<table class=\"facts_table\">";

	// preset child/father SURN
	$surn = "";
	if (empty($namerec)) {
		$indirec = "";
		if ($famtag=="CHIL" and $nextaction=="addchildaction") {
			$famrec = find_family_record($famid);
			if (empty($famrec)) $famrec = find_record_in_file($famid);
			$parents = find_parents_in_record($famrec);
			$indirec = find_person_record($parents["HUSB"]);
		}
		if ($famtag=="HUSB" and $nextaction=="addnewparentaction") {
			$indirec = find_person_record($pid);
		}
		$nt = preg_match("/\d SURN (.*)/", $indirec, $ntmatch);
		if ($nt) $surn = $ntmatch[1];
		else {
			$nt = preg_match("/1 NAME (.*)[\/](.*)[\/]/", $indirec, $ntmatch);
			if ($nt) $surn = $ntmatch[2];
		}
		if ($surn) $namerec = "1 NAME  /".trim($surn,"\r\n")."/";
	}
	// handle PAF extra NPFX [ 961860 ]
	$nt = preg_match("/\d NPFX (.*)/", $namerec, $nmatch);
	$npfx=trim(@$nmatch[1]);
	// 1 NAME = NPFX GIVN /SURN/ NSFX
	$nt = preg_match("/\d NAME (.*)/", $namerec, $nmatch);
	$name=@$nmatch[1];
	if (strlen($npfx) and strpos($name, $npfx)===false) $name = $npfx." ".$name;
	add_simple_tag("0 NAME ".$name);
	// 2 NPFX
	add_simple_tag("0 NPFX ".$npfx);
	// 2 GIVN
	$nt = preg_match("/\d GIVN (.*)/", $namerec, $nmatch);
	add_simple_tag("0 GIVN ".@$nmatch[1]);
	// 2 NICK
	$nt = preg_match("/\d NICK (.*)/", $namerec, $nmatch);
	add_simple_tag("0 NICK ".@$nmatch[1]);
	// 2 SPFX
	$nt = preg_match("/\d SPFX (.*)/", $namerec, $nmatch);
	add_simple_tag("0 SPFX ".@$nmatch[1]);
	// 2 SURN
	$nt = preg_match("/\d SURN (.*)/", $namerec, $nmatch);
	add_simple_tag("0 SURN ".@$nmatch[1]);
	// 2 NSFX
	$nt = preg_match("/\d NSFX (.*)/", $namerec, $nmatch);
	add_simple_tag("0 NSFX ".@$nmatch[1]);
	// 2 _HEB
	$nt = preg_match("/\d _HEB (.*)/", $namerec, $nmatch);
	if ($nt>0 || $USE_RTL_FUNCTIONS) {
		add_simple_tag("0 _HEB ".@$nmatch[1]);
	}
	// 2 ROMN
	$nt = preg_match("/\d ROMN (.*)/", $namerec, $nmatch);
	add_simple_tag("0 ROMN ".@$nmatch[1]);

	if ($surn) $namerec = ""; // reset if modified

	if (empty($namerec)) {
		// 2 _MARNM
		add_simple_tag("0 _MARNM");
		// 1 SEX
		if ($famtag=="HUSB" or $sextag=="M") add_simple_tag("0 SEX M");
		else if ($famtag=="WIFE" or $sextag=="F") add_simple_tag("0 SEX F");
		else add_simple_tag("0 SEX");
		// 1 BIRT
		// 2 DATE
		// 2 PLAC
		// 3 MAP
		// 4 LATI
		// 4 LONG
		add_simple_tag("0 BIRT");
		add_simple_tag("0 DATE", "BIRT");
		add_simple_tag("0 PLAC", "BIRT");
		add_simple_tag("0 MAP", "BIRT");
		add_simple_tag("0 LATI", "BIRT");
		add_simple_tag("0 LONG", "BIRT");
		// 1 DEAT
		// 2 DATE
		// 2 PLAC
		// 3 MAP
		// 4 LATI
		// 4 LONG
		add_simple_tag("0 DEAT");
		add_simple_tag("0 DATE", "DEAT");
		add_simple_tag("0 PLAC", "DEAT");
		add_simple_tag("0 MAP", "DEAT");
		add_simple_tag("0 LATI", "DEAT");
		add_simple_tag("0 LONG", "DEAT");
		print "</table>\n";
		//-- if adding a spouse add the option to add a marriage fact to the new family
		if ($nextaction=='addspouseaction' || ($nextaction=='addnewparentaction' && $famid!='new')) {
			print "<br />\n";
			print "<table class=\"facts_table\">";
			// 1 MARR
			// 2 DATE
			// 2 PLAC
			// 3 MAP
			// 4 LATI
			// 4 LONG
			add_simple_tag("0 MARR");
			add_simple_tag("0 DATE", "MARR");
			add_simple_tag("0 PLAC", "MARR");
			add_simple_tag("0 MAP", "MARR");
			add_simple_tag("0 LATI", "MARR");
			add_simple_tag("0 LONG", "MARR");
			print "</table>\n";
		}
		print_add_layer("SOUR", 1);
		print_add_layer("NOTE", 1);
		print_add_layer("OBJE", 1);
		print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";
	}
	else {
		if ($namerec!="NEW") {
			$gedlines = split("\n", $namerec);	// -- find the number of lines in the record
			$fields = preg_split("/\s/", $gedlines[0]);
			$glevel = $fields[0];
			$level = $glevel;
			$type = trim($fields[1]);
			$level1type = $type;
			$tags=array();
			$i = 0;
			$namefacts = array("NPFX", "GIVN", "NICK", "SPFX", "SURN", "NSFX", "NAME", "_HEB", "ROMN");
			do {
				if (!in_array($type, $namefacts)) {
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
		// 2 _MARNM
		add_simple_tag("0 _MARNM");
		print "</tr>\n";
		print "</table>\n";
		print_add_layer("SOUR");
		print_add_layer("NOTE");
		print "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";
	}
	print "</form>\n";
	?>
	<script type="text/javascript" src="./js/autocomplete.js"></script>
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
		return str.replace(/(^\s*)|(\s*$)/g,'');
	}
	function updatewholename() {
		frm = document.forms[0];
		var npfx=trim(frm.NPFX.value);
		if (npfx) npfx+=" ";
		var givn=trim(frm.GIVN.value);
		var spfx=trim(frm.SPFX.value);
		if (spfx) spfx+=" ";
		var surn=trim(frm.SURN.value);
		var nsfx=trim(frm.NSFX.value);
		frm.NAME.value = npfx + givn + " /" + spfx + surn + "/ " + nsfx;
	}
	function togglename() {
		frm = document.forms[0];

		// show/hide NAME
		var ronly = frm.NAME.readOnly;
		if (ronly) {
			updatewholename();
			frm.NAME.readOnly=false;
			if (frm.NAME_spec) frm.NAME_spec.style.display="inline";
			if (frm.NAME_plus) frm.NAME_plus.style.display="inline";
			if (frm.NAME_minus) frm.NAME_minus.style.display="none";
			disp="none";
		}
		else {
			// split NAME = (NPFX) GIVN / (SPFX) SURN / (NSFX)
			var name=frm.NAME.value+'//';
			var name_array=name.split("/");
			var givn=trim(name_array[0]);
			var givn_array=givn.split(" ");
			var surn=trim(name_array[1]);
			var surn_array=surn.split(" ");
			var nsfx=trim(name_array[2]);

			// NPFX
			var npfx='';
			do {
				search=givn_array[0]; // first word
				search=search.replace(/(\.*$)/g,''); // remove trailing '.'
				if (npfx_accept.in_array(search)) npfx+=givn_array.shift()+' ';
				else break;
			} while (givn_array.length>0);
			frm.NPFX.value=trim(npfx);

			// GIVN
			frm.GIVN.value=trim(givn_array.join(' '));

			// SPFX
			var spfx='';
			do {
				search=surn_array[0]; // first word
				search=search.replace(/(\.*$)/g,''); // remove trailing '.'
				if (spfx_accept.in_array(search)) spfx+=surn_array.shift()+' ';
				else break;
			} while (surn_array.length>0);
			frm.SPFX.value=trim(spfx);

			// SURN
			frm.SURN.value=trim(surn_array.join(' '));

			// NSFX
			frm.NSFX.value=trim(nsfx);

			// NAME
			frm.NAME.readOnly=true;
			if (frm.NAME_spec) frm.NAME_spec.style.display="none";
			if (frm.NAME_plus) frm.NAME_plus.style.display="none";
			if (frm.NAME_minus) frm.NAME_minus.style.display="inline";
			disp="table-row";
			if (document.all) disp="inline"; // IE
		}
		// show/hide
		document.getElementById("NPFX_tr").style.display=disp;
		document.getElementById("GIVN_tr").style.display=disp;
		document.getElementById("NICK_tr").style.display=disp;
		document.getElementById("SPFX_tr").style.display=disp;
		document.getElementById("SURN_tr").style.display=disp;
		document.getElementById("NSFX_tr").style.display=disp;
	}
	function checkform() {
		frm = document.addchildform;
		/* if (frm.GIVN.value=="") {
			alert('<?php print $pgv_lang["must_provide"]; print $pgv_lang["given_name"]; ?>');
			frm.GIVN.focus();
			return false;
		}
		if (frm.SURN.value=="") {
			alert('<?php print $pgv_lang["must_provide"]; print $pgv_lang["surname"]; ?>');
			frm.SURN.focus();
			return false;
		}*/
		var fname=frm.NAME.value;
		fname=fname.replace(/ /g,'');
		fname=fname.replace(/\//g,'');
		if (fname=="") {
			alert('<?php print $pgv_lang["must_provide"]; print " ".$factarray["NAME"]; ?>');
			frm.NAME.focus();
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php
	// force name expand on form load (maybe optional in a further release...)
	print "<script type='text/javascript'>togglename();</script>";
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
	$out .= "<div id=\"caldiv".$id."\" style=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\"></div>\n";
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
 */
function add_simple_tag($tag, $upperlevel="", $label="", $readOnly="", $noClose="") {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_DIRECTORY, $TEMPLE_CODES;
	global $assorela, $tags, $emptyfacts, $TEXT_DIRECTION, $confighelpfile;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $upload_count;
	global $tabkey, $STATUS_CODES, $REPO_ID_PREFIX, $SPLIT_PLACES, $pid, $linkToID;
	
	if (!isset($noClose) && isset($readOnly) && $readOnly=="NOCLOSE") {
		$noClose = "NOCLOSE";
		$readOnly = "";
	}

	if (!isset($noClose) || $noClose!="NOCLOSE") $noClose = "";
	if (!isset($readOnly) || $readOnly!="READONLY") $readOnly = "";

	if (!isset($tabkey)) $tabkey = 1;

	if (empty($linkToID)) $linkToID = $pid;

    // Work around for $emptyfacts being mysteriously unset
    if (empty($emptyfacts))
        $emptyfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","BAPL","CONL","ENDL","SLGC","EVEN","MARR","SLGS","MARL","ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARS","CHAN","_SEPR","RESI", "DATA", "MAP");

	$largetextfacts = array("TEXT","PUBL","NOTE");
	$subnamefacts = array("NPFX", "GIVN", "NICK", "SPFX", "SURN", "NSFX");

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
	if ($fact=="REPO") $islink = true;

	// rows & cols
	$rows=1;
	$cols=40;
	if ($islink) $cols=10;
	if ($fact=="FORM") $cols=5;
	if ($fact=="DATE" or $fact=="TIME" or $fact=="TYPE") $cols=20;
	if ($fact=="LATI" or $fact=="LONG") $cols=12;
	if (in_array($fact, $subnamefacts)) $cols=25;
	if ($fact=="GIVN" or $fact=="SURN") $cols=25;
	if ($fact=="NPFX" or $fact=="SPFX" or $fact=="NSFX") $cols=12;
	if (in_array($fact, $largetextfacts)) { $rows=10; $cols=70; }
	if ($fact=="ADDR") $rows=5;
	if ($fact=="REPO") $cols = strlen($REPO_ID_PREFIX) + 4;

	// label
	$style="";
	print "<tr id=\"".$element_id."_tr\" ";
	if (in_array($fact, $subnamefacts)) print " style=\"display:none;\""; // hide subname facts
	if ($fact=="MAP") print " style=\"display:none;\""; // MAP is preceding LATI and LONG
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
		$noterec = find_gedcom_record($noteid);
		$n1match = array();
		$nt = preg_match("/0 @$value@ NOTE (.*)/", $noterec, $n1match);
		if ($nt!==false) $value=trim(strip_tags(@$n1match[1].get_cont(1, $noterec)));
		$element_name="NOTE[".$noteid."]";
	}

	if (in_array($fact, $emptyfacts)&&empty($value)) {
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".$value."\" />";
	}
	else if ($fact=="TEMP") {
		print "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		print "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
		foreach($TEMPLE_CODES as $code=>$temple) {
			print "<option value=\"$code\"";
			if ($code==$value) print " selected=\"selected\"";
			print ">$temple</option>\n";
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
		print "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" />\n";
		print "<table><tr valign=\"top\">\n";
		foreach (array("none", "locked", "privacy", "confidential") as $resn_index => $resn_val) {
			if ($resn_val=="none") $resnv=""; else $resnv=$resn_val;
			print "<td><input tabindex=\"".$tabkey."\" type=\"radio\" name=\"RESN_radio\" onclick=\"update_RESN_img('".$resn_val."')\"";
			print " value=\"".$resnv."\"";
			if ($value==$resnv) print " checked=\"checked\"";
			print " /><small>".$pgv_lang[$resn_val]."</small>";
			print "<br />&nbsp;<img id=\"RESN_".$resn_val."\" src=\"image/RESN_".$resn_val.".gif\"  alt=\"".$pgv_lang[$resn_val]."\" title=\"".$pgv_lang[$resn_val]."\" border=\"0\"";
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
				$type[strtolower(substr($varname, 6))] = $typeValue;
			}
		}
		//-- Sort the array into a meaningful order
		array_flip($type);
		asort($type);
		array_flip($type);
		//-- Build the selector for the Media "TYPE" Fact
		print "<select name=\"text[]\">";
		print "<option selected=\"selected\" value=\"\"> ".$pgv_lang["choose"]." </option>";
		$selectedValue = strtolower($value);
		foreach ($type as $typeName => $typeValue) {
			print "<option value=\"".$typeName."\"";
			if ($selectedValue == $typeName) print "selected=\"selected\"";
			print "> ".$typeValue." </option>";
		}
		print "</select>";
	}
	else {
		// textarea
		if ($rows>1) print "<textarea tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" rows=\"".$rows."\" cols=\"".$cols."\">".PrintReady(htmlspecialchars($value))."</textarea><br />\n";
		// text
		else {
			print "<input tabindex=\"".$tabkey."\" type=\"text\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".PrintReady(htmlspecialchars($value))."\" size=\"".$cols."\" dir=\"ltr\"";
			if ($fact=="NPFX") print " onkeyup=\"wactjavascript_autoComplete(npfx_accept,this,event)\" autocomplete=\"off\" ";
			if (in_array($fact, $subnamefacts)) print " onchange=\"updatewholename();\"";
			if ($fact=="DATE") print " onblur=\"valid_date(this);\"";
			if ($fact=="LATI") print " onblur=\"valid_lati_long(this, 'N', 'S');\"";
			if ($fact=="LONG") print " onblur=\"valid_lati_long(this, 'E', 'W');\"";
			//if ($fact=="FILE") print " onchange=\"if (updateFormat) updateFormat(this.value);\"";
			print " ".$readOnly." />\n";
		}
		// split PLAC
		if ($fact=="PLAC" && $readOnly=="") {
			print "<div id=\"".$element_id."_pop\" style=\"display: inline;\">\n";
			print_specialchar_link($element_id, false);
			print_findplace_link($element_id);
			print "</div>\n";
			if ($SPLIT_PLACES) {
				if (!function_exists("print_place_subfields")) require("includes/functions_places.php");
				print_place_subfields($element_id);
			}
		}
		else if ($cols>20 and $fact!="NPFX" && $readOnly=="") print_specialchar_link($element_id, false);
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
		if ($fact=="DATE") print get_changed_date($value);
		if ($fact=="ASSO" and $value) print " ".PrintReady(get_person_name($value))." (".$value.")";
		if ($fact=="SOUR" and $value) print " ".PrintReady(get_source_descriptor($value))." (".$value.")";
	} else {
		if ($fact=="DATE") print "&rlm;".get_changed_date($value)."&rlm;";
		if ($fact=="ASSO" and $value) print " &rlm;".PrintReady(get_person_name($value))." (".$value.")&rlm;";
		if ($fact=="SOUR" and $value) print " &rlm;".PrintReady(get_source_descriptor($value))."&rlm;&nbsp;&nbsp;&lrm(".$value.")&lrm;";
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

		// split NAME
		// Do this only for real names.  REPO uses "NAME" instead of "TITL".
		if ($fact=="NAME" && $upperlevel!="REPO") {
			print "&nbsp;<a href=\"javascript: ".$pgv_lang["show_details"]."\" onclick=\"togglename(); return false;\"><img id=\"".$element_id."_plus\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /></a>\n";
			print "<a href=\"javascript: ".$pgv_lang["show_details"]."\" onclick=\"togglename(); return false;\"><img style=\"display:none;\" id=\"".$element_id."_minus\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /></a>\n";
		}
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
	global $MEDIA_DIRECTORY, $TEXT_DIRECTION;
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
		$text = "TEXT";
		add_simple_tag(($level+2)." $text");
		add_simple_tag(($level+2)." DATE", "", $pgv_lang["date_of_entry"]);
		// 3 OBJE
		add_simple_tag(($level+1)." OBJE @@");
		// 3 QUAY
		add_simple_tag(($level+1)." QUAY");
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
 * @return string	The updated gedcom record
 */
function handle_updates($newged) {
	global $glevels, $islink, $tag, $uploaded_files, $text, $NOTE;

	for($j=0; $j<count($glevels); $j++) {
		//-- update external note records first
		if (($islink[$j])&&($tag[$j]=="NOTE")) {
			if (empty($NOTE[$text[$j]])) {
				delete_gedrec($text[$j]);
				$text[$j] = "";
			}
			else {
				$noterec = find_gedcom_record($text[$j]);
				$newnote = "0 @$text[$j]@ NOTE\r\n";
				$newline = "1 CONC ".$NOTE[$text[$j]];
				$newlines = preg_split("/\r?\n/", $newline);
				for($k=0; $k<count($newlines); $k++) {
					if ($k>0) $newlines[$k] = "1 CONT ".$newlines[$k];
					if (strlen($newlines[$k])>255) {
						while(strlen($newlines[$k])>255) {
							// Make sure this piece doesn't end on a blank
							// (Blanks belong at the start of the next piece)
							$thisPiece = rtrim(substr($newlines[$k], 0, 255));
							$newnote .= $thisPiece."\r\n";
							$newlines[$k] = substr($newlines[$k], strlen($thisPiece));
							$newlines[$k] = "1 CONC ".$newlines[$k];
						}
						$newnote .= trim($newlines[$k])."\r\n";
					}
					else {
						$newnote .= trim($newlines[$k])."\r\n";
					}
				}
				$notelines = preg_split("/\r?\n/", $noterec);
				for($k=1; $k<count($notelines); $k++) {
					if (preg_match("/1 CON[CT] /", $notelines[$k])==0) $newnote .= trim($notelines[$k])."\r\n";
				}
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
			if ($islink[$j]) $text[$j]="@".$text[$j]."@";
			$newline = $glevels[$j]." ".$tag[$j];
			//-- check and translate the incoming dates
			if ($tag[$j]=="DATE" && !empty($text[$j])) {
				$text[$j] = check_input_date($text[$j]);
			}
			// print $newline;
//			if (!empty($text[$j])) $newline .= " ".$text[$j];
			if ($text[$j]!="") $newline .= " ".$text[$j];
			$newged .= breakConts($newline, $glevels[$j]+1);
		}
	}

	return $newged;
}

/**
 * break up a line of gedcom text into multiple CONT/CONC lines
 * @param string $newline	the line of text to break
 * @param int $level		the GEDCOM level that new lines should have
 * @return string			returns the updated gedcom record
 */
function breakConts($newline, $level) {
	$newged = "";
	//-- convert returns to CONT lines and break up lines longer than 255 chars
	$newlines = preg_split("/\r?\n/", $newline);
	for($k=0; $k<count($newlines); $k++) {
		if ($k>0) $newlines[$k] = $level." CONT ".$newlines[$k];
		if (strlen($newlines[$k])>255) {
			while(strlen($newlines[$k])>255) {
				// Make sure this piece doesn't end on a blank
				// (Blanks belong at the start of the next piece)
				$thisPiece = rtrim(substr($newlines[$k], 0, 255));
				$newged .= $thisPiece."\r\n";
				$newlines[$k] = substr($newlines[$k], strlen($thisPiece));
				$newlines[$k] = $level." CONC ".$newlines[$k];
			}
			$newged .= trim($newlines[$k])."\r\n";
		}
		else {
			$newged .= trim($newlines[$k])."\r\n";
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
	if (preg_match("/^\d+ \w\w\w \d\d\d\d$/", $datestr)>0) return $datestr;
	$date = parse_date($datestr);
	//print_r($date);
	if ((count($date)==1)&&empty($date[0]['ext'])&&!empty($date[0]['month'])&&!empty($date[0]['year'])) {
		$datestr = strtoupper($date[0]['day']." ".$date[0]['month']." ".$date[0]['year']);
	}
	return $datestr;
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
		$gedrec = find_record_in_file($linktoid);
	} else {
		$gedrec = find_gedcom_record($linktoid);
	}

	if ($gedrec) {
		$mediarec = "1 OBJE @".$mediaid."@\r\n";
		$newrec = trim($gedrec."\r\n".$mediarec);

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
	global $templefacts, $nondatefacts, $nonplacfacts;
	global $tags;

	// handle  MARRiage TYPE
	$type_val="";
	if (substr($fact,0,5)=="MARR_") {
		$type_val=substr($fact,5);
		$fact="MARR";
	}

	$tags=array();
	$tags[0]=$fact;

	if ($fact=="SOUR") add_simple_tag("1 SOUR @");
	else add_simple_tag("1 ".$fact);

	if ($fact=="EVEN" or $fact=="GRAD" or $fact=="MARR") {
		// 1 EVEN|GRAD|MARR
		// 2 TYPE
		add_simple_tag("2 TYPE ".$type_val);
	}
	if (in_array($fact, $templefacts)) {
		// 2 TEMP
		add_simple_tag("2 TEMP");
		// 2 STAT
		add_simple_tag("2 STAT");
	}
	if ($fact=="SOUR") {
		// 1 SOUR
		// 2 PAGE
		add_simple_tag("2 PAGE");
		// 2 DATA
		// 3 TEXT
		add_simple_tag("3 TEXT");
	}
	if ($fact=="EDUC" or $fact=="GRAD" or $fact=="OCCU") {
		// 1 EDUC|GRAD|OCCU
		// 2 CORP
		add_simple_tag("2 CORP");
	}
	if (!in_array($fact, $nondatefacts)) {
		// 2 DATE
		add_simple_tag("2 DATE");
		// 3 TIME
		add_simple_tag("3 TIME");
		// 2 PLAC
		// 3 MAP
		// 4 LATI
		// 4 LONG
		if (!in_array($fact, $nonplacfacts)) {
			add_simple_tag("2 PLAC");
			add_simple_tag("3 MAP");
			add_simple_tag("4 LATI");
			add_simple_tag("4 LONG");
		}
	}
	if ($fact=="BURI") {
		// 1 BURI
		// 2 CEME
		add_simple_tag("2 CEME");
	}
	if ($fact=="BIRT" or $fact=="DEAT" or $fact=="MARR"
	or $fact=="CENS" or $fact=="EDUC" or $fact=="GRAD"
	or $fact=="OCCU" or $fact=="ORDN" or $fact=="RESI") {
		// 1 BIRT|DEAT|MARR|CENS|EDUC|GRAD|OCCU|ORDN|RESI
		// 2 ADDR
		add_simple_tag("2 ADDR");
	}
	if ($fact=="OCCU" or $fact=="RESI") {
		// 1 OCCU|RESI
		// 2 PHON|FAX|EMAIL|URL
		add_simple_tag("2 PHON");
		add_simple_tag("2 FAX");
		add_simple_tag("2 EMAIL");
		add_simple_tag("2 URL");
	}
	if ($fact=="DEAT") {
		// 1 DEAT
		// 2 CAUS
		add_simple_tag("2 CAUS");
	}
	if ($fact=="REPO") {
		//1 REPO
		//2 CALN
		add_simple_tag("2 CALN");
	}
	if ($fact!="OBJE") {
		// 2 RESN
		add_simple_tag("2 RESN");
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
	global $WORD_WRAPPED_NOTES, $pgv_lang, $templefacts, $nondatefacts, $nonplacfacts;
	global $tags;

	$gedlines = split("\n", $gedrec);	// -- find the number of lines in the record
	$fields = preg_split("/\s/", $gedlines[$linenum]);
	$glevel = $fields[0];
	$level = $glevel;
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
	// Loop on existing tags :
	while (true) {
		$text = "";
		for($j=2; $j<count($fields); $j++) {
			if ($j>2) $text .= " ";
			$text .= $fields[$j];
		}
		while(($i+1<count($gedlines))&&(preg_match("/".($level+1)." (CON[CT])\s?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
			if ($cmatch[1]=="CONT") $text.="\n";
			else if ($WORD_WRAPPED_NOTES) $text .= " ";
			$conctxt = $cmatch[2];
			$conctxt = preg_replace("/[\r\n]/","",$conctxt);
			$text.=$conctxt;
			$i++;
		}

		if ($type!="DATA" && $type!="CONC" && $type!="CONT") {
			$tags[]=$type;
			$subrecord = $level." ".$type." ".$text;
			if ($inSource && $type=="DATE") add_simple_tag($subrecord, "", $pgv_lang["date_of_entry"]);
			else add_simple_tag($subrecord, $level0type);
		}
		if (!$inSource && $type=="DATE" && !strpos(@$gedlines[$i+1], " TIME")) add_simple_tag(($level+1)." TIME");
		if ($type=="MARR" && !strpos(@$gedlines[$i+1], " TYPE")) add_simple_tag(($level+1)." TYPE");
		if ($type=="PLAC" && !strpos(@$gedlines[$i+1], " MAP")) {
			add_simple_tag(($level+1)." MAP");
			add_simple_tag(($level+2)." LATI");
			add_simple_tag(($level+2)." LONG");
		}

		if ($type=="SOUR") {
			$inSource = true;
			$levelSource = $level;
			$haveSourcePage = false;
			$haveSourceText = false;
			$haveSourceDate = false;
			$haveSourceQuay = false;
		}

		$i++;
		if (isset($gedlines[$i])) {
			$fields = preg_split("/\s/", $gedlines[$i]);
			$level = $fields[0];
			if (isset($fields[1])) $type = trim($fields[1]);
			else $level = 0;
		} else $level = 0;

		// Check for, and add, missing tags subordinate to SOUR
		// The logic here is complicated because the missing tags MUST
		// be in the right order.
		if ($inSource) {
			if ($levelSource < $level) {
				if ($type=="PAGE") $haveSourcePage = true;
				if ($type=="TEXT") {
					if (!$haveSourcePage) {
						add_simple_tag(($levelSource+1)." PAGE");
						$haveSourcePage = true;
					}
					$haveSourceText = true;
				}
				if ($type=="DATE") {
					if (!$haveSourceText) {
						if (!$haveSourcePage) {
							add_simple_tag(($levelSource+1)." PAGE");
							$haveSourcePage = true;
						}
						add_simple_tag($levelSource." TEXT");
						$haveSourceText = true;
					}
				}
				if ($type=="DATE") $haveSourceDate = true;
				if ($type=="QUAY") $haveSourceQuay = true;
			} else {
				if (!$haveSourcePage) add_simple_tag(($levelSource+1)." PAGE");
				if (!$haveSourceText) add_simple_tag(($levelSource+2)." TEXT");
				if (!$haveSourceDate) add_simple_tag(($levelSource+2)." DATE", "", $pgv_lang["date_of_entry"]);
				if (!$haveSourceQuay) add_simple_tag(($levelSource+1)." QUAY");
				$inSource = false;
			}
		}

		if ($level<=$glevel) break;

	}

	// Now add some missing tags :
	if (in_array($tags[0], $templefacts)) {
		// 2 TEMP
		if (!in_array("TEMP", $tags)) add_simple_tag("2 TEMP");
		// 2 STAT
		if (!in_array("STAT", $tags)) add_simple_tag("2 STAT");
	}
	if ($level1type=="NAME" || $level1type=="TITL") {
		// 1 NAME
		// 2 _HEB
		// 2 ROMN
		if (!in_array("_HEB", $tags)) add_simple_tag("2 _HEB");
		if (!in_array("ROMN", $tags)) add_simple_tag("2 ROMN");
	}
	if ($level1type=="GRAD") {
		// 1 GRAD
		// 2 TYPE
		if (!in_array("TYPE", $tags)) add_simple_tag("2 TYPE");
	}
	if ($level1type=="EDUC" or $level1type=="GRAD" or $level1type=="OCCU") {
		// 1 EDUC|GRAD|OCCU
		// 2 CORP
		if (!in_array("CORP", $tags)) add_simple_tag("2 CORP");
	}
	if ($level1type=="DEAT") {
		// 1 DEAT
		// 2 CAUS
		if (!in_array("CAUS", $tags)) add_simple_tag("2 CAUS");
	}
	// "SOUR" is handled earlier; this tag can occur at levels other than 1.
	if ($level1type=="REPO") {
		// 1 REPO
		// 2 CALN
		if (!in_array("CALN", $tags)) add_simple_tag("2 CALN");
	}
	if (!in_array($level1type, $nondatefacts)) {
		// 2 DATE
		// 3 TIME
		if (!in_array("DATE", $tags)) {
			add_simple_tag("2 DATE");
			add_simple_tag("3 TIME");
		}
		// 2 PLAC
		// 3 MAP
		// 4 LATI
		// 4 LONG
		if (!in_array("PLAC", $tags) && !in_array($level1type, $nonplacfacts) && !in_array("TEMP", $tags)) {
			add_simple_tag("2 PLAC");
			add_simple_tag("3 MAP");
			add_simple_tag("4 LATI");
			add_simple_tag("4 LONG");
		}
	}
	if ($level1type=="BURI") {
		// 1 BURI
		// 2 CEME
		if (!in_array("CEME", $tags)) add_simple_tag("2 CEME");
	}
	if ($level1type=="BIRT" or $level1type=="DEAT" or $level1type=="MARR"
	or $level1type=="CENS" or $level1type=="EDUC" or $level1type=="GRAD"
	or $level1type=="OCCU" or $level1type=="ORDN" or $level1type=="RESI") {
		// 1 BIRT|DEAT|MARR|CENS|EDUC|GRAD|OCCU|ORDN|RESI
		// 2 ADDR
		if (!in_array("ADDR", $tags)) add_simple_tag("2 ADDR");
	}
	if ($level1type=="OCCU" or $level1type=="RESI") {
		// 1 OCCU|RESI
		// 2 PHON|FAX|EMAIL|URL
		if (!in_array("PHON", $tags)) add_simple_tag("2 PHON");
		if (!in_array("FAX", $tags)) add_simple_tag("2 FAX");
		if (!in_array("EMAIL", $tags)) add_simple_tag("2 EMAIL");
		if (!in_array("URL", $tags)) add_simple_tag("2 URL");
	}
	if ($level1type=="OBJE") {
		// 1 OBJE

		if (!$levellink) {
			// 2 FORM
			if (!in_array("FORM", $tags)) add_simple_tag("2 FORM");
			// 2 FILE
			if (!in_array("FILE", $tags)) add_simple_tag("2 FILE");
			// 2 TITL
			if (!in_array("TITL", $tags)) add_simple_tag("2 TITL");
		}
		// 2 _PRIM
		if (!in_array("_PRIM", $tags)) add_simple_tag("2 _PRIM");
		// 2 _THUM
		if (!in_array("_THUM", $tags)) add_simple_tag("2 _THUM");
	}
	// 2 RESN
	if (!in_array("RESN", $tags)) add_simple_tag("2 RESN");

	return $level1type;
}

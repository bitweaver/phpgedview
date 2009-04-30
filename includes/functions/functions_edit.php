<?php
/**
* Various functions used by the Edit interface
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
* @see functions_places.php
* @version $Id: functions_edit.php,v 1.1 2009/04/30 17:51:51 lsces Exp $
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_EDIT_PHP', '');

require_once 'includes/functions/functions_import.php';

$NPFX_accept = array( "Adm", "Amb", "Brig", "Can", "Capt", "Chan", "Chapln", "Cmdr", "Col", "Cpl", "Cpt", "Dr", "Gen", "Gov", "Hon", "Lady", "Lt", "Mr", "Mrs", "Ms", "Msgr", "Pfc", "Pres", "Prof", "Pvt", "Rabbi", "Rep", "Rev", "Sen", "Sgt", "Sir", "Sr", "Sra", "Srta", "Ven");
$SPFX_accept = array("al", "da", "de", "den", "dem", "der", "di", "du", "el", "la", "van", "von");
$NSFX_accept = array( "I", "II", "III", "IV", "V", "VI", "Jr", "Junior", "MD", "PhD", "Senior", "Sr");
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
$typefacts = array(); //-- special facts that go on 2 TYPE lines

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
	"RELA" =>array("ASSO"),
	"DATE" =>array("ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARR","MARL", "MARS","RESI","EVEN","EDUC","OCCU","PROP","RELI","RESI","BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","EVEN","BAPL","CONL","ENDL","SLGC","SLGS","_TODO"),
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
	"EVEN" =>array("DATA"),
	"_PGVU"=>array("_TODO")
);
$STANDARD_NAME_FACTS = array('NAME', 'NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX');
$REVERSED_NAME_FACTS = array('NAME', 'NPFX', 'SPFX', 'SURN', 'NSFX', 'GIVN');

//-- this function creates a new unique connection
//-- and adds it to the connections file
//-- it returns the connection identifier
function newConnection() {
	return session_name()."\t".session_id()."\n";
}

/**
* Check if the given gedcom record has changed since the last session access
* This is used to check if the gedcom record changed between the time the user
* loaded the individual page and the time they clicked on a link to edit
* the data.
*
* @param string $pid The gedcom id of the record to check pgv_changes
* @param string $gedrec The latest gedcom record to check the CHAN:DATE:TIME (auto accept)
*/
function checkChangeTime($pid, $gedrec, $last_time) {
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
	if (isset($_REQUEST['linenum']) && $changeTime!=0 && $last_time && $changeTime > $last_time) {
		echo "<span class=\"error\">".preg_replace("/#PID#/", $pid, $pgv_lang["edit_concurrency_msg2"])."<br /><br />";
		if (!empty($changeUser)) echo preg_replace(array("/#CHANGEUSER#/", "/#CHANGEDATE#/"), array($changeUser,date("d M Y H:i:s", $changeTime)), $pgv_lang["edit_concurrency_change"])."<br /><br />";
		echo $pgv_lang["edit_concurrency_reload"]."</span>";
		print_simple_footer();
		exit;
	}
}

/**
* This function will replace a gedcom record with
* the id $gid with the $gedrec
* @param string $gid The XREF id of the record to replace
* @param string $gedrec The new gedcom record to replace with
* @param boolean $chan Whether or not to update/add the CHAN record
* @param string $linkpid Tells whether or not this record change is linked with the record change of another record identified by $linkpid
*/
function replace_gedrec($gid, $gedrec, $chan=true, $linkpid='') {
	global $fcontents, $GEDCOM, $pgv_changes, $manual_save, $pgv_private_records;

	$gid = strtoupper($gid);
	//-- restore any data that was hidden during privatizing
	if (isset($pgv_private_records[$gid])) {
		$privatedata = trim(get_last_private_data($gid));
		$subs = get_all_subrecords("\n".$privatedata, '', false, false);
		foreach($subs as $s=>$sub) {
			if (strstr($gedrec, $sub)===false) $gedrec = trim($gedrec)."\n".$sub;
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
			$change["undo"] = reformat_record_import($gedrec);
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
			accept_changes($gid."_".$GEDCOM);
		} else {
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
		$change["undo"] = reformat_record_import($gedrec);
		if (!isset($pgv_changes[$xref."_".$GEDCOM])) $pgv_changes[$xref."_".$GEDCOM] = array();
		$pgv_changes[$xref."_".$GEDCOM][] = $change;

		if (PGV_USER_AUTO_ACCEPT) {
			accept_changes($xref."_".$GEDCOM);
		} else {
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
	global $pgv_lang;

	$gedrec = trim(stripslashes(stripLRMRLM($gedrec)));

	$ct = preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
	if ($ct==0) {
		echo "ERROR 20: Invalid GEDCOM 5.5 format.\n";
		AddToChangeLog("ERROR 20: Invalid GEDCOM 5.5 format.->" . PGV_USER_NAME ."<-");
		if (PGV_DEBUG) {
			echo "<pre>$gedrec</pre>\n";
			echo debug_print_backtrace();
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
			$newgedrec .= "1 CHAN\n2 DATE ".strtoupper(date("d M Y"))."\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\n";
			$newgedrec .= "2 _PGVU ".PGV_USER_NAME."\n";
			$newgedrec .= substr($gedrec, $pos2);
			$gedrec = $newgedrec;
		}
		else {
			$newgedrec = "\n1 CHAN\n2 DATE ".strtoupper(date("d M Y"))."\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\n";
			$newgedrec .= "2 _PGVU ".PGV_USER_NAME;
			$gedrec .= $newgedrec;
		}
	}
	$gedrec = preg_replace('/\\\+/', "\\", $gedrec);

	//-- remove any empty lines
	$lines = explode("\n", $gedrec);
	$newrec = "";
	foreach($lines as $ind=>$line) {
		//-- remove any whitespace
		$line = trim($line);
		if (!empty($line)) $newrec .= $line."\n";
	}

	$newrec = html_entity_decode($newrec,ENT_COMPAT,'UTF-8');
	return $newrec;
}

/**
* remove a subrecord from a parent record by gedcom tag
*
* @param string $oldrecord the parent record to remove the subrecord from
* @param string $tag the GEDCOM subtag to start deleting at
* @param string $gid [optional] gid can be used to limit to @gid@
* @param int $num [optional] num specifies which multiple of the tag to remove, set to -1 to remove all
* @return string returns the oldrecord minus the subrecord(s)
*/
function remove_subrecord($oldrecord, $tag, $gid='', $num=0) {
	$newrec = "";
	$gedlines = explode("\n", $oldrecord);

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
* @param string $oldrecord parent record to delete from
* @param int $linenum linenumber where the subrecord to delete starts
* @return string the new record
*/
function remove_subline($oldrecord, $linenum) {
	$newrec = "";
	$gedlines = explode("\n", $oldrecord);

	for($i=0; $i<$linenum; $i++) {
		if (trim($gedlines[$i])!="") $newrec .= $gedlines[$i]."\n";
	}
	if (isset($gedlines[$linenum])) {
		$fields = explode(' ', $gedlines[$linenum]);
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
* @param string $cid the change id of the form gid_gedcom
* @param int $index the index of the change to undo
* @return boolean true if undo successful
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
* @param string $nextaction the next action the edit_interface.php file should take after the form is submitted
* @param string $famid the family that the new person should be added to
* @param string $namerec the name subrecord when editing a name
* @param string $famtag how the new person is added to the family
*/
function print_indi_form($nextaction, $famid, $linenum="", $namerec="", $famtag="CHIL", $sextag="") {
	global $pgv_lang, $factarray, $pid, $PGV_IMAGE_DIR, $PGV_IMAGES, $WORD_WRAPPED_NOTES;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $GEDCOM, $NAME_REVERSE;
	global $bdm, $TEXT_DIRECTION, $STANDARD_NAME_FACTS, $REVERSED_NAME_FACTS, $ADVANCED_NAME_FACTS, $ADVANCED_PLAC_FACTS, $SURNAME_TRADITION;
	global $QUICK_REQUIRED_FACTS, $QUICK_REQUIRED_FAMFACTS;

	$bdm = ""; // used to copy '1 SOUR' to '2 SOUR' for BIRT DEAT MARR
	init_calendar_popup();
	echo "<form method=\"post\" name=\"addchildform\" onsubmit=\"return checkform();\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"$nextaction\" />\n";
	echo "<input type=\"hidden\" name=\"linenum\" value=\"$linenum\" />\n";
	echo "<input type=\"hidden\" name=\"famid\" value=\"$famid\" />\n";
	echo "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	echo "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\" />\n";
	echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />\n";
	echo "<input type=\"hidden\" name=\"goto\" value=\"\" />\n";
	if (preg_match('/^add(child|spouse|newparent|newrepository)/', $nextaction)) {
		echo "<input type=\"submit\" value=\"{$pgv_lang['saveandgo']}\" onclick=\"document.addchildform.goto.value='new';\"/>\n";
	}
	echo "<table class=\"facts_table\">";

	// When adding a new child, specify the pedigree
	if ($nextaction=='addchildaction') {
		add_simple_tag("0 PEDI");
	}

	// Populate the standard NAME field and subfields
	$name_fields=array();
	if (!$NAME_REVERSE) {
		foreach ($STANDARD_NAME_FACTS as $tag) {
			$name_fields[$tag]=get_gedcom_value($tag, 0, $namerec);
		}
	} else {
		foreach ($REVERSED_NAME_FACTS as $tag) {
			$name_fields[$tag]=get_gedcom_value($tag, 0, $namerec);
		}
	}

	$new_marnm='';
	// Inherit surname from parents, spouse or child
	if (empty($namerec)) {
		// We'll need the parent's name to set the child's surname
		if (isset($pgv_changes[$famid."_".$GEDCOM]))
			$famrec=find_updated_record($famid);
		else
			$famrec=find_family_record($famid);
		$parents=find_parents_in_record($famrec);
		$father_name=get_gedcom_value('NAME', 0, find_person_record($parents['HUSB']));
		$mother_name=get_gedcom_value('NAME', 0, find_person_record($parents['WIFE']));
		// We'll need the spouse/child's name to set the spouse/parent's surname
		if (isset($pgv_changes[$pid."_".$GEDCOM]))
			$prec=find_updated_record($pid);
		else
			$prec=find_person_record($pid);
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
						preg_match('/\/(\S+)\s+\S+\//', $father_name, $matchf)) {
					$name_fields['SURN']=$matchf[1].' '.$matchm[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/(\S+)\s+\S+\//', $indi_name, $match)) {
					$name_fields['SURN']=$match[1].' ';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				if ($famtag=='WIFE' && preg_match('/\/\S+\s+(\S+)\//', $indi_name, $match)) {
					$name_fields['SURN']=$match[1].' ';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
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
						preg_match('/\/\S+\s+(\S+)\//', $father_name, $matchf)) {
					$name_fields['SURN']=$matchf[1].' '.$matchm[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/\S+\s+(\S+)\//', $indi_name, $match)) {
					$name_fields['SURN']=' '.$match[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				if ($famtag=='WIFE' && preg_match('/\/(\S+)\s+\S+\//', $indi_name, $match)) {
					$name_fields['SURN']=' '.$match[1];
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			}
			break;
		case 'icelandic':
			// Sons get their father's given name plus "sson"
			// Daughters get their father's given name plus "sdottir"
			switch ($nextaction) {
			case 'addchildaction':
				if ($sextag=='M' && preg_match('/(\S+)\s+\/.*\//', $father_name, $match)) {
					$name_fields['SURN']=preg_replace('/s$/', '', $match[1]).'sson';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				if ($sextag=='F' && preg_match('/(\S+)\s+\/.*\//', $father_name, $match)) {
					$name_fields['SURN']=preg_replace('/s$/', '', $match[1]).'sdottir';
					$name_fields['NAME']='/'.$name_fields['SURN'].'/';
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/(\S+)sson\s+\/.*\//i', $indi_name, $match)) {
					$name_fields['GIVN']=$match[1];
					$name_fields['NAME']=$name_fields['GIVN'].' //';
				}
				if ($famtag=='WIFE' && preg_match('/(\S+)sdottir\s+\/.*\//i', $indi_name, $match)) {
					$name_fields['GIVN']=$match[1];
					$name_fields['NAME']=$name_fields['GIVN'].' //';
				}
				break;
			}
			break;
		case 'paternal':
		case 'polish':
			// Father gives his surname to his wife and children
			switch ($nextaction) {
			case 'addspouseaction':
				if ($famtag=='WIFE' && preg_match('/\/(.*)\//', $indi_name, $match)) {
					if ($SURNAME_TRADITION=='polish') {
						$match[1]=preg_replace(array('/ski$/','/cki$/','/dzki$/'), array('ska', 'cka', 'dzka'), $match[1]);
					}
					$new_marnm=$match[1];
				}
				break;
			case 'addchildaction':
				if (preg_match('/\/((?:[a-z]{2,3}\s+)*)(.*)\//i', $father_name, $match)) {
					if ($SURNAME_TRADITION=='polish' && $sextag=='F') {
						$match[2]=preg_replace(array('/ski$/','/cki$/','/dzki$/'), array('ska', 'cka', 'dzka'), $match[2]);
					}
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['SURN']=$match[2];
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
				}
				break;
			case 'addnewparentaction':
				if ($famtag=='HUSB' && preg_match('/\/((?:[a-z]{2,3}\s+)*)(.*)\//i', $indi_name, $match)) {
					if ($SURNAME_TRADITION=='polish' && $sextag=='M') {
						$match[2]=preg_replace(array('/ska$/','/cka$/','/dzka$/'), array('ski', 'cki', 'dzki'), $match[2]);
					}
					$name_fields['SPFX']=trim($match[1]);
					$name_fields['SURN']=$match[2];
					$name_fields['NAME']="/{$match[1]}{$match[2]}/";
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
	if (preg_match ("/((($npfx_accept)\.? +)*)([^\n\/\"]*)(\"(.*)\")? *\/(([a-z]{2,3} +)*)(.*)\/ *(.*)/i", $name_fields['NAME'], $name_bits)) {
		if (empty($name_fields['NPFX'])) $name_fields['NPFX']=$name_bits[1];
		if (!$NAME_REVERSE && empty($name_fields['GIVN'])) $name_fields['GIVN']=$name_bits[4];
		if (empty($name_fields['SPFX']) && empty($name_fields['SURN'])) {
			$name_fields['SPFX']=trim($name_bits[7]);
			$name_fields['SURN']=$name_bits[9];
		}
		if (empty($name_fields['NSFX'])) $name_fields['NSFX']=$name_bits[10];
		if ($NAME_REVERSE && empty($name_fields['GIVN'])) $name_fields['GIVN']=$name_bits[4];
		// Don't automatically create an empty NICK - it is an "advanced" field.
		if (empty($name_fields['NICK']) && !empty($name_bits[6]) && !preg_match('/^2 NICK/m',$namerec))
			$name_fields['NICK']=$name_bits[6];
	}

	// Edit the standard name fields
	foreach ($name_fields as $tag=>$value) {
		add_simple_tag("0 $tag $value");
	}

	// Get the advanced name fields
	$adv_name_fields=array();
	if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_NAME_FACTS, $match))
		foreach ($match[1] as $tag)
			$adv_name_fields[$tag]='';
	// This is a custom tag, but PGV uses it extensively.
	if ($SURNAME_TRADITION=='paternal' || $SURNAME_TRADITION=='polish' || preg_match('/2 _MARNM/', $namerec))
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
					add_simple_tag("2 $tag $value", "", fact_label("NAME:{$tag}"));
				}
			}
			// Allow a new row to be entered if there was no row provided
			if (count($match[1])==0 && empty($name_fields[$tag]) || $tag!='_HEB' && $tag!='NICK')
				if ($tag=='_MARNM') {
					add_simple_tag("0 _MARNM");
					add_simple_tag("0 _MARNM_SURN $new_marnm");
				} else {
					add_simple_tag("0 $tag", "", fact_label("NAME:{$tag}"));
				}
	}

	// Handle any other NAME subfields that aren't included above (SOUR, NOTE, _CUSTOM, etc)
	if ($namerec!="" && $namerec!="NEW") {
		$gedlines = split("\n", $namerec); // -- find the number of lines in the record
		$fields = explode(' ', $gedlines[0]);
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
					if ($cmatch[1]=="CONT") $text.="\n";
					if ($WORD_WRAPPED_NOTES) $text .= " ";
					$text .= $cmatch[2];
					$i++;
				}
				add_simple_tag($level." ".$type." ".$text);
			}
			$tags[]=$type;
			$i++;
			if (isset($gedlines[$i])) {
				$fields = explode(' ', $gedlines[$i]);
				$level = $fields[0];
				if (isset($fields[1])) $type = $fields[1];
			}
		} while (($level>$glevel)&&($i<count($gedlines)));
	}

	// If we are adding a new individual, add the basic details
	if ($nextaction!='update') {
		echo '</table><br/><table class="facts_table">';
		// 1 SEX
		if ($famtag=="HUSB" || $sextag=="M") {
			add_simple_tag("0 SEX M");
		} elseif ($famtag=="WIFE" || $sextag=="F") {
			add_simple_tag("0 SEX F");
		}	else {
			add_simple_tag("0 SEX");
		}
		$bdm = "BD";
		if (preg_match_all('/('.PGV_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
			foreach ($matches[1] as $match) {
				if (!in_array($match, explode('|', PGV_EVENTS_DEAT))) {
					addSimpleTags($match);
				}
			}
		}
		//-- if adding a spouse add the option to add a marriage fact to the new family
		if ($nextaction=='addspouseaction' || ($nextaction=='addnewparentaction' && $famid!='new')) {
			$bdm .= "M";
			if (preg_match_all('/('.PGV_REGEX_TAG.')/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
				foreach ($matches[1] as $match) {
					addSimpleTags($match);
				}
			}
		}
		if (preg_match_all('/('.PGV_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
			foreach ($matches[1] as $match) {
				if (in_array($match, explode('|', PGV_EVENTS_DEAT))) {
					addSimpleTags($match);
				}
			}
		}
	}
	if (PGV_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		print_help_link("no_update_CHAN_help", "qm");
		echo $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
		echo "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
		echo $pgv_lang["no_update_CHAN"]."<br />\n";
		if (isset($famrec)) {
			$event = new Event(get_sub_record(1, "1 CHAN", $famrec));
			echo format_fact_date($event, false, true);
		}
		echo "</td></tr>\n";
	}
	echo "</table>\n";
	if ($nextaction=='update') { // GEDCOM 5.5.1 spec says NAME doesn't get a OBJE
		print_add_layer('SOUR');
		print_add_layer('NOTE');
	} else {
		print_add_layer('SOUR', 1);
		print_add_layer('NOTE', 1);
		print_add_layer('OBJE', 1);
	}
	echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />\n";
	if (preg_match('/^add(child|spouse|newparent|source)/', $nextaction)) {
		echo "<input type=\"submit\" value=\"{$pgv_lang['saveandgo']}\" onclick=\"document.addchildform.goto.value='new';\"/>\n";
	}
	echo "</form>\n";
	?>
	<script type="text/javascript">
	<!--
	function trim(str) {
		// Commas are used in the GIVN and SURN field to separate lists of surnames.
		// For example, to differentiate the two Spanish surnames from an English
		// double-barred name.
		// Commas *may* be used in the NAME field, and will form part of the displayed
		// name.  This is not encouraged, as it may confuse some logic that assumes
		// "list" format names are always "surn, givn".
		str=str.replace(/,/g," ");

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

	// Generate a full name from the name components
	function generate_name() {
		var frm =document.forms[0];
		var npfx=frm.NPFX.value;
		var givn=frm.GIVN.value;
		var spfx=frm.SPFX.value;
		var surn=frm.SURN.value;
		var nsfx=frm.NSFX.value;
		return trim(npfx+" "+givn+" /"+trim(spfx+" "+surn.replace(/ *, */, " "))+"/ "+nsfx);
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
		document.getElementById('NAME').value=generate_name();
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
				// IE doesn't allow changing the "type" of an input field so we'll cludge it ( silly :P)
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
					// force FF ui to update the display
					if (delement.innerHTML != oldName) {
						oldName = delement.innerHTML;
						element.value = oldName;
					}
				}
			}
			else {
				manualChange = false;
				// IE doesn't allow changing the "type" of an input field so we'll cludge it ( silly :P)
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
		var ip=document.getElementsByTagName('input');
		for (var i=0; i<ip.length; i++) {
			// ADD slashes to _HEB and _AKA names
			if (ip[i].id.indexOf('_AKA')==0 || ip[i].id.indexOf('_HEB')==0 || ip[i].id.indexOf('ROMN')==0)
				if (ip[i].value.indexOf('/')<0 && ip[i].value!='')
					ip[i].value=ip[i].value.replace(/([^\s]+)\s*$/, "/$1/");
			// Blank out temporary _MARNM_SURN and empty name fields
			if (ip[i].id.indexOf("_MARNM_SURN")==0 || ip[i].value=='//')
					ip[i].value='';
			// Convert "xxx yyy" and "xxx y yyy" surnames to "xxx,yyy"
			if ('<?php echo $SURNAME_TRADITION; ?>'=='spanish' || '<?php echo $SURNAME_TRADITION; ?>'=='portuguese')
				if (ip[i].id.indexOf("SURN")==0) ip[i].value=document.forms[0].SURN.value.replace(/^\s*([^\s,]{2,})\s+([iIyY] +)?([^\s,]{2,})\s*$/, "$1,$3");;
		}
		return true;
	}

	// If the name isn't initially formed from the components in a standard way,
	// then don't automatically update it.
	if (document.getElementById("NAME").value!=generate_name() && document.getElementById("NAME").value!="//") convertHidden("NAME");
	//-->
	</script>
	<?php
}

/**
* generates javascript code for calendar popup in user's language
*
* @param string id form text element id where to return date value
* @param boolean $asString Whether or not to return this text as a string or echo it
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
	else echo $out;
}
/**
* @todo add comments
*/
function print_addnewmedia_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $pid;
	
	$text = $pgv_lang["add_media"];
	if (isset($PGV_IMAGES["addmedia"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addmedia"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	echo '&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="pastefield=document.getElementById(\''.$element_id.'\'); window.open(\'addmedia.php?action=showmediaform&linktoid={$linkToID}&level={$level}\', \'_blank\', \'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1\'); return false;">';
	echo $Link;
	echo "</a>";
}
/**
* @todo add comments
*/
function print_addnewrepository_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["create_repository"];
	if (isset($PGV_IMAGES["addrepository"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addrepository"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"addnewrepository(document.getElementById('".$element_id."')); return false;\">";
	echo $Link;
	echo "</a>";
}

/**
* @todo add comments
*/
function print_addnewnote_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $pid;
	
	$text = $pgv_lang["create_shared_note"];
	if (isset($PGV_IMAGES["addnote"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addnote"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:ADD;\" onclick=\"addnewnote(document.getElementById('".$element_id."')); return false;\">";
	echo $Link;
	echo "</a>";
}

/**
* @todo add comments
*/
function print_addnewnote_assisted_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $pid;
	
	$text = $pgv_lang["create_shared_note_assisted"];
	if (isset($PGV_IMAGES["addnote"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addnote"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:ADD;\" onclick=\"addnewnote_assisted(document.getElementById('".$element_id."'), '".$pid."' ); return false;\">";
	echo $Link;
	echo "</a>";
}

/**
* @todo add comments
*/

function print_editnote_link($note_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	$text = $pgv_lang["edit_shared_note"];
	if (isset($PGV_IMAGES["note"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["note"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	echo "<a href=\"javascript: var win02=window.open('edit_interface.php?action=editnote&pid=$note_id', 'win02', 'top=70, left=70, width=620, height=500, resizable=1, scrollbars=1 ' )\">";
	echo $Link;
	echo "</a><br />";
}

/**
* @todo add comments
*/
function print_addnewsource_link($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;

	$text = $pgv_lang["create_source"];
	if (isset($PGV_IMAGES["addsource"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["addsource"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"addnewsource(document.getElementById('".$element_id."')); return false;\">";
	echo $Link;
	echo "</a>";
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
* @param string $tag fact record to edit (eg 2 DATE xxxxx)
* @param string $upperlevel optional upper level tag (eg BIRT)
* @param string $label An optional label to echo instead of the default from the $factarray
* @param string $readOnly optional, when "READONLY", fact data can't be changed
* @param string $noClose optional, when "NOCLOSE", final "</td></tr>" won't be printed
* (so that additional text can be printed in the box)
* @param boolean $rowDisplay True to have the row displayed by default, false to hide it by default
*/
function add_simple_tag($tag, $upperlevel="", $label="", $readOnly="", $noClose="", $rowDisplay=true) {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_DIRECTORY, $TEMPLE_CODES;
	global $assorela, $tags, $emptyfacts, $main_fact, $TEXT_DIRECTION, $pgv_changes, $GEDCOM;
	global $NPFX_accept, $SPFX_accept, $NSFX_accept, $FILE_FORM_accept, $upload_count;
	global $tabkey, $STATUS_CODES, $SPLIT_PLACES, $pid, $linkToID;
	global $bdm, $PRIVACY_BY_RESN;
	global $lang_short_cut, $LANGUAGE;
	global $QUICK_REQUIRED_FACTS, $QUICK_REQUIRED_FAMFACTS, $PREFER_LEVEL2_SOURCES;

	if (substr($tag, 0, strpos($tag, "PLAC"))) {
		?>
<script type="text/javascript">
		<!--
		function valid_lati_long(field, pos, neg) {
			// valid LATI or LONG according to Gedcom standard
			// pos (+) : N or E
			// neg (-) : S or W
			txt=field.value.toUpperCase();
			txt=txt.replace(/(^\s*)|(\s*$)/g,''); // trim
			txt=txt.replace(/ /g,':'); // N12 34 ==> N12.34
			txt=txt.replace(/\+/g,''); // +17.1234 ==> 17.1234
			txt=txt.replace(/-/g,neg); // -0.5698 ==> W0.5698
			txt=txt.replace(/,/g,'.'); // 0,5698 ==> 0.5698
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

	// element name : used to POST data
	if ($level==0) {
		if ($upperlevel) $element_name=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE | ...
		else $element_name=$fact; // ex: OCCU
	} else $element_name="text[]";
	if ($level==1) $main_fact=$fact;

	// element id : used by javascript functions
	if ($level==0) $element_id=$fact; // ex: NPFX | GIVN ...
	else $element_id=$fact.floor(microtime()*1000000); // ex: SOUR56402
	if ($upperlevel) $element_id=$upperlevel."_".$fact; // ex: BIRT_DATE | DEAT_DATE ...

	// field value
	$islink = (substr($value,0,1)=="@" and substr($value,0,2)!="@#");
	if ($islink) $value=trim(trim(substr($tag, strlen($fact)+3)), " @\r");
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
	case 'TEXT': case 'PUBL':
		$rows=10;
		$cols=70;
		break;
	case 'SHARED_NOTE_EDIT':
		$islink=1;
		$fact="NOTE";
		$rows=15;
		$cols=88;
		break;
	case 'SHARED_NOTE':
		$islink=1;
		$fact="NOTE";
		$rows=1;
		$cols=($islink ? 8 : 40);
		break;
	case 'NOTE':
		if ($islink) {
			$rows=1;
			$cols=($islink ? 8 : 40);
			break;
		} else {
			$rows=10;
			$cols=70;
			break;
		}
	case 'ADDR':
		$rows=4;
		$cols=40;
		break;
	case 'PAGE':
		$rows=1;
		$cols=50;
		break;
	default:
		$rows=1;
		$cols=($islink ? 8 : 40);
		break;
	}

	// label
	$style="";
	echo "<tr id=\"".$element_id."_tr\" ";
	if ($fact=="MAP" || $fact=="LATI" || $fact=="LONG") echo " style=\"display:none;\"";
	echo " >\n";
	if (in_array($fact, $subnamefacts) || $fact=="LATI" || $fact=="LONG")
			echo "<td class=\"optionbox $TEXT_DIRECTION wrap width25\">";
	else echo "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";

	// help link
	if (!in_array($fact, $emptyfacts)) {
		if ($fact=="DATE") {
			print_help_link("def_gedcom_date_help", "qm", "date");
		} else if ($fact=="RESN") {
			print_help_link($fact."_help", "qm");
		} else {
			if ($fact=="NOTE" && $islink){
				print_help_link("edit_add_SHARED_NOTE_help", "qm");
			} else {
				print_help_link("edit_".$fact."_help", "qm");
			}
		}
	}
	if ($fact=="_AKAN" || $fact=="_AKA" || $fact=="ALIA") {
		// Allow special processing for different languages
		$func="fact_AKA_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (function_exists($func)) {
			// Localise the AKA fact
			$func($fact, $pid);
		}
	}
	else if ($fact=="AGNC" && !empty($main_fact)) {
		// Allow special processing for different languages
		$func="fact_AGNC_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (function_exists($func)) {
			// Localise the AGNC fact
			$func($fact, $main_fact);
		}
	}
	if (PGV_DEBUG) {
		echo $element_name."<br />\n";
	}

	// tag name
	if (!empty($label)) {
		if ($label=="Note" && $islink){
			echo $pgv_lang["shared_note"];
		}else{
			 echo $label;
		}
	} else {
		if ($fact=="NOTE" && $islink){
			echo $pgv_lang["shared_note"];
		} else if (isset($pgv_lang[$fact])) {
			echo $pgv_lang[$fact];
		} else if (isset($factarray[$fact])) {
			echo $factarray[$fact];
		}else{
			echo $fact;
		}
	}
	echo "\n";

	// tag level
	if ($level>0) {
		if ($fact=="TEXT" and $level>1) {
			echo "<input type=\"hidden\" name=\"glevels[]\" value=\"".($level-1)."\" />";
			echo "<input type=\"hidden\" name=\"islink[]\" value=\"0\" />";
			echo "<input type=\"hidden\" name=\"tag[]\" value=\"DATA\" />";
			//-- leave data text[] value empty because the following TEXT line will
			//--- cause the DATA to be added
			echo "<input type=\"hidden\" name=\"text[]\" value=\"\" />";
		}
		echo "<input type=\"hidden\" name=\"glevels[]\" value=\"".$level."\" />\n";
		echo "<input type=\"hidden\" name=\"islink[]\" value=\"".($islink)."\" />\n";
		echo "<input type=\"hidden\" name=\"tag[]\" value=\"".$fact."\" />\n";
		// Shared Notes Debug --------------------
			// echo "<br />Label = ".$label;
			// echo "<br />Level = ".$level;
			// echo "<br />Link = ".$islink;
			// echo "<br />Fact = ".$fact;
			// echo "<br />Value = ".$value;
		// End Debug -------------------
	}
	echo "\n</td>";

	// value
	echo "<td class=\"optionbox wrap\">\n";
	if (PGV_DEBUG) {
		echo $tag."<br />\n";
	}

	// retrieve linked NOTE
	if ($fact=="NOTE" && $islink) {
		$noteid = $value;
	}

	if (in_array($fact, $emptyfacts)&& (empty($value) || $value=="y" || $value=="Y")) {
		$value = strtoupper($value);
		//-- don't default anything to Y when adding events through people
		//-- default to Y when specifically adding one of these events
		if ($level==1) $value="Y"; // default YES
		echo "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".$value."\" />";
		if ($level<=1) {
			echo "<input type=\"checkbox\" ";
			if ($value=="Y") echo " checked=\"checked\"";
			echo " onclick=\"if (this.checked) ".$element_id.".value='Y'; else ".$element_id.".value=''; \" />";
			echo $pgv_lang["yes"];
		}
	}
	else if ($fact=="TEMP") {
		echo "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		echo "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
		foreach($TEMPLE_CODES as $code=>$temple) {
			echo "<option value=\"$code\"";
			if ($code==$value) echo " selected=\"selected\"";
			echo ">$temple ($code)</option>\n";
		}
		echo "</select>\n";
	}
	else if ($fact=="ADOP") {
		echo "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >";
		foreach (array("BOTH"=>$factarray["HUSB"]."+".$factarray["WIFE"],
										"HUSB"=>$factarray["HUSB"],
										"WIFE"=>$factarray["WIFE"]) as $k=>$v) {
			echo "<option value='$k'";
			if ($value==$k)
				echo " selected=\"selected\"";
			echo ">$v</option>";
		}
		echo "</select>\n";
	}
	else if ($fact=="PEDI") {
		echo "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >";
		foreach (array(""       =>$pgv_lang["unknown"],
										"birth"  =>$factarray["BIRT"],
										"adopted"=>$pgv_lang["adopted"],
										"foster" =>$pgv_lang["foster"],
										"sealing"=>$pgv_lang["sealing"]) as $k=>$v) {
			echo "<option value='$k'";
			if (UTF8_strtolower($value)==$k)
				echo " selected=\"selected\"";
			echo ">$v</option>";
		}
		echo "</select>\n";
	}
	else if ($fact=="STAT") {
		echo "<select tabindex=\"".$tabkey."\" name=\"".$element_name."\" >\n";
		echo "<option value=''>No special status</option>\n";
		foreach($STATUS_CODES as $code=>$status) {
			echo "<option value=\"$code\"";
			if ($code==$value) echo " selected=\"selected\"";
			echo ">$status</option>\n";
		}
		echo "</select>\n";
	}
	else if ($fact=="RELA") {
		$text=strtolower($value);
		// add current relationship if not found in default list
		if (!array_key_exists($text, $assorela)) $assorela[$text]=$text;
		echo "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		foreach ($assorela as $key=>$value) {
			echo "<option value=\"". $key . "\"";
			if ($key==$text) echo " selected=\"selected\"";
			echo ">" . $assorela["$key"] . "</option>\n";
		}
		echo "</select>\n";
	}
	else if ($fact=="_PGVU") {
		$text=strtolower($value);
		echo "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		echo '<option value=""';
		if (''==$text) echo ' selected="selected"';
		echo ">-</option>\n";
		foreach (get_all_users('asc', 'username') as $user_id=>$user_name) {
			echo "<option value=\"". $user_id . "\"";
			if ($user_id==$text) echo " selected=\"selected\"";
			echo ">" . $user_name . "</option>\n";
		}
		echo "</select>\n";
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
			document.getElementById("<?php echo $element_id?>").value=resn_val;
		}
		//-->
		</script>
		<?php
		if (!$PRIVACY_BY_RESN && $level==1) {
			// warn user that level 1 RESN tags have no effect when PRIVACY_BY_RESN is false
			echo "<small>".$pgv_lang["resn_disabled"]."</small>";
		}
		echo "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".$value."\" />\n";
		echo "<table><tr valign=\"top\">\n";
		foreach (array("none", "locked", "privacy", "confidential") as $resn_index => $resn_val) {
			if ($resn_val=="none") $resnv=""; else $resnv=$resn_val;
			echo "<td><input tabindex=\"".$tabkey."\" type=\"radio\" name=\"RESN_radio\" onclick=\"update_RESN_img('".$resn_val."')\"";
			echo " value=\"".$resnv."\"";
			if ($value==$resnv) echo " checked=\"checked\"";
			echo " /><small>".$pgv_lang[$resn_val]."</small>";
			echo "<br />&nbsp;<img id=\"RESN_".$resn_val."\" src=\"images/RESN_".$resn_val.".gif\"  alt=\"".$pgv_lang[$resn_val]."\" title=\"".$pgv_lang[$resn_val]."\" border=\"0\"";
			if ($value==$resnv) echo " style=\"display:inline\""; else echo " style=\"display:none\"";
			echo " /></td>\n";
		}
		echo "</tr></table>\n";
	}
	else if ($fact=="_PRIM" or $fact=="_THUM") {
		echo "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" >\n";
		echo "<option value=\"\"></option>\n";
		echo "<option value=\"Y\"";
		if ($value=="Y") echo " selected=\"selected\"";
		echo ">".$pgv_lang["yes"]."</option>\n";
		echo "<option value=\"N\"";
		if ($value=="N") echo " selected=\"selected\"";
		echo ">".$pgv_lang["no"]."</option>\n";
		echo "</select>\n";
	}
	else if ($fact=="SEX") {
		echo "<select tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\">\n<option value=\"M\"";
		if ($value=="M") echo " selected=\"selected\"";
		echo ">".$pgv_lang["male"]."</option>\n<option value=\"F\"";
		if ($value=="F") echo " selected=\"selected\"";
		echo ">".$pgv_lang["female"]."</option>\n<option value=\"U\"";
		if ($value=="U" || empty($value)) echo " selected=\"selected\"";
		echo ">".$pgv_lang["unknown"]."</option>\n</select>\n";
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
		echo "<select tabindex=\"".$tabkey."\" name=\"text[]\">";
		if ($value=="") echo "<option selected=\"selected\" value=\"\" > ".$pgv_lang["choose"]." </option>";
		$selectedValue = strtolower($value);
		foreach ($type as $typeName => $typeValue) {
			echo "<option value=\"".$typeName."\" ";
			if ($selectedValue == $typeName) echo "selected=\"selected\" ";
			echo "> ".$typeValue." </option>";
		}
		echo "</select>";
	}
	else if (($fact=="NAME" && $upperlevel!='REPO') || $fact=="_MARNM") {
		// Populated in javascript from sub-tags
		echo "<input type=\"hidden\" id=\"".$element_id."\" name=\"".$element_name."\" onchange=\"updateTextName('".$element_id."');\" value=\"".PrintReady(htmlspecialchars($value,ENT_COMPAT,'UTF-8'))."\" />";
		echo "<span id=\"".$element_id."_display\">".PrintReady(htmlspecialchars($value,ENT_COMPAT,'UTF-8'))."</span>";
		echo " <a href=\"#edit_name\" onclick=\"convertHidden('".$element_id."'); return false;\"> ";
		if (isset($PGV_IMAGES["edit_indi"]["small"])) echo "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_indi"]["small"]."\" border=\"0\" width=\"20\" alt=\"".$pgv_lang["edit_name"]."\" align=\"top\" />";
		else echo "<span class=\"age\">[".$pgv_lang["edit_name"]."]</span>";
		echo "</a>";
	} else {
		// textarea
		if ($rows>1) echo "<textarea tabindex=\"".$tabkey."\" id=\"".$element_id."\" name=\"".$element_name."\" rows=\"".$rows."\" cols=\"".$cols."\">".PrintReady(htmlspecialchars($value,ENT_COMPAT,'UTF-8'))."</textarea><br />\n";
		else {
			// text
			echo "<input tabindex=\"".$tabkey."\" type=\"text\" id=\"".$element_id."\" name=\"".$element_name."\" value=\"".PrintReady(htmlspecialchars($value,ENT_COMPAT,'UTF-8'))."\" size=\"".$cols."\" dir=\"ltr\"";
			echo " class=\"{$fact}\"";
			echo " autocomplete=\"off\"";
			if (in_array($fact, $subnamefacts)) echo " onblur=\"updatewholename();\" onkeyup=\"updatewholename();\"";
			if ($fact=="DATE") echo " onblur=\"valid_date(this);\" onmouseout=\"valid_date(this);\"";
			if ($fact=="LATI") echo " onblur=\"valid_lati_long(this, 'N', 'S');\" onmouseout=\"valid_lati_long(this, 'N', 'S');\"";
			if ($fact=="LONG") echo " onblur=\"valid_lati_long(this, 'E', 'W');\" onmouseout=\"valid_lati_long(this, 'E', 'W');\"";
			//if ($fact=="FILE") echo " onchange=\"if (updateFormat) updateFormat(this.value);\"";
			echo " ".$readOnly." />\n";
		}
		// split PLAC
		if ($fact=="PLAC" && $readOnly=="") {
			echo "<div id=\"".$element_id."_pop\" style=\"display: inline;\">\n";
			print_specialchar_link($element_id, false);
			print_findplace_link($element_id);
			echo "</div>\n";
			echo "<a href=\"javascript:;\" onclick=\"toggle_lati_long();\"><img src=\"images/buttons/target.gif\" border=\"0\" align=\"middle\" alt=\"".$factarray["LATI"]." / ".$factarray["LONG"]."\" title=\"".$factarray["LATI"]." / ".$factarray["LONG"]."\" /></a>";
			if ($SPLIT_PLACES) {
				if (!function_exists("print_place_subfields")) require("includes/functions/functions_places.php");
				setup_place_subfields($element_id);
				print_place_subfields($element_id);
			}
		}
		else if (($cols>20 || $fact=="NPFX") && $readOnly=="") print_specialchar_link($element_id, false);
	}
	// MARRiage TYPE : hide text field and show a selection list
	if ($fact=="TYPE" and $tags[0]=="MARR") {
		echo "<script type='text/javascript'>";
		echo "document.getElementById('".$element_id."').style.display='none'";
		echo "</script>";
		echo "<select tabindex=\"".$tabkey."\" id=\"".$element_id."_sel\" onchange=\"document.getElementById('".$element_id."').value=this.value;\" >\n";
		foreach (array("Unknown", "Civil", "Religious", "Partners") as $indexval => $key) {
			if ($key=="Unknown") echo "<option value=\"\"";
			else echo "<option value=\"".$key."\"";
			$a=strtolower($key);
			$b=strtolower($value);
			if (@strpos($a, $b)!==false or @strpos($b, $a)!==false) echo " selected=\"selected\"";
			echo ">".$factarray["MARR_".strtoupper($key)]."</option>\n";
		}
		echo "</select>";
	}

	// popup links
	if ($readOnly=="") {
		if ($fact=="DATE") print_calendar_popup($element_id);
		if ($fact=="FAMC") print_findfamily_link($element_id, "");
		if ($fact=="FAMS") print_findfamily_link($element_id, "");
		if ($fact=="ASSO") print_findindi_link($element_id, "");
		if ($fact=="FILE") print_findmedia_link($element_id, "0file");
		if ($fact=="SOUR") {
			print_findsource_link($element_id);
			print_addnewsource_link($element_id);
			//print_autopaste_link($element_id, array("S1", "S2"), false, false, true);
			//-- checkboxes to apply '1 SOUR' to BIRT/MARR/DEAT as '2 SOUR'
			if ($level==1) {
				echo '<br />';
				if ($PREFER_LEVEL2_SOURCES==='0') {
					$level1_checked='';
					$level2_checked='';
				} else if ($PREFER_LEVEL2_SOURCES==='1' || $PREFER_LEVEL2_SOURCES===true) {
					$level1_checked='';
					$level2_checked=' checked="checked"';
				} else {
					$level1_checked=' checked="checked"';
					$level2_checked='';

				}
				if (strpos($bdm, 'B')!==false) {
					echo '&nbsp;<input type="checkbox" name="SOUR_INDI" ', $level1_checked, ' value="Y" />';
					echo $pgv_lang['individual'];
					if (preg_match_all('/('.PGV_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
						foreach ($matches[1] as $match) {
							if (!in_array($match, explode('|', PGV_EVENTS_DEAT))) {
								echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="Y" />';
								echo $factarray[$match];
							}
						}
					}
				}
				if (strpos($bdm, 'D')!==false) {
					if (preg_match_all('/('.PGV_REGEX_TAG.')/', $QUICK_REQUIRED_FACTS, $matches)) {
						foreach ($matches[1] as $match) {
							if (in_array($match, explode('|', PGV_EVENTS_DEAT))) {
								echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="Y" />';
								echo $factarray[$match];
							}
						}
					}
				}
				if (strpos($bdm, 'M')!==false) {
					echo '&nbsp;<input type="checkbox" name="SOUR_FAM" ', $level1_checked, ' value="Y" />';
					echo $pgv_lang["family"];
					if (preg_match_all('/('.PGV_REGEX_TAG.')/', $QUICK_REQUIRED_FAMFACTS, $matches)) {
						foreach ($matches[1] as $match) {
							echo '&nbsp;<input type="checkbox" name="SOUR_', $match, '"', $level2_checked, ' value="Y" />';
							echo $factarray[$match];
						}
					}
				}
			}
		}
		if ($fact=="REPO") {
			print_findrepository_link($element_id);
			print_addnewrepository_link($element_id);
		}

		// Shared Notes Icons ========================================
		if ($fact=="NOTE" && $islink) {
			print_findnote_link($element_id);
			print_addnewnote_link($element_id);
			if (file_exists('modules/GEDFact_assistant/CENS/census_1_ctrl.php')) {
				print_addnewnote_assisted_link($element_id);
			}
			echo "&nbsp;&nbsp;&nbsp;";
			$record=GedcomRecord::getInstance($value);
		}
		if ($fact=="NOTE" && $islink && $value!="") {
			print_editnote_link($value);
		}
		// ===========================================================

		if ($fact=="OBJE") print_findmedia_link($element_id, "1media");
		if ($fact=="OBJE" && !$value) {
			print_addnewmedia_link($element_id);
			$value = "new";
		}
	}

	// current value
	if ($TEXT_DIRECTION=="ltr") {
		if ($fact=="DATE") {
			$date=new GedcomDate($value);
			echo $date->Display(false);
		}
		// if (($fact=="ASSO" || $fact=="SOUR") && $value) {
		if (($fact=="ASSO" || $fact=="SOUR" || ($fact=="NOTE" && $islink)) && $value) {
			$record=GedcomRecord::getInstance($value);
			if ($record) {
				echo ' ', PrintReady($record->getFullName()), ' (', $value, ')';
			}
		}
	} else {
		if ($fact=="DATE") {
			$date=new GedcomDate($value);
			echo getRLM(), $date->Display(false), getRLM();
		}
		if (($fact=="ASSO" || $fact=="SOUR") && $value) {
			$record=GedcomRecord::getInstance($value);
			if ($record) {
				echo ' ', PrintReady($record->getFullName()), ' ', getLRM(), '(', $value, ')', getLRM();
			}
		}
	}

	// pastable values
	if ($readOnly=="") {
		if ($fact=="SPFX") print_autopaste_link($element_id, $SPFX_accept);
		if ($fact=="NSFX") print_autopaste_link($element_id, $NSFX_accept);
		if ($fact=="FORM") print_autopaste_link($element_id, $FILE_FORM_accept, false, false);
	}

	if ($noClose != "NOCLOSE") echo "</td></tr>\n";

	$tabkey++;
	return $element_id;
}

/**
* prints collapsable fields to add ASSO/RELA, SOUR, OBJE ...
*
* @param string $tag Gedcom tag name
*/
function print_add_layer($tag, $level=2, $printSaveButton=true) {
	global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
	global $MEDIA_DIRECTORY, $TEXT_DIRECTION, $PRIVACY_BY_RESN;
	global $gedrec, $FULL_SOURCES;
	global $islink;
	if ($tag=="SOUR") {
		//-- Add new source to fact
		echo "<a href=\"javascript:;\" onclick=\"return expand_layer('newsource');\"><img id=\"newsource_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_source"]."</a>";
		print_help_link("edit_add_SOUR_help", "qm");
		echo "<br />";
		echo "<div id=\"newsource\" style=\"display: none;\">\n";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		echo "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
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
		if ($FULL_SOURCES) {
			// 4 DATE
			add_simple_tag(($level+2)." DATE", "", $factarray["DATA:DATE"]);
			// 3 QUAY
			add_simple_tag(($level+1)." QUAY");
		}
		// 3 OBJE
		add_simple_tag(($level+1)." OBJE");
		// 3 SHARED_NOTE
		add_simple_tag(($level+1)." SHARED_NOTE");
		echo "</table></div>";
	}
	if ($tag=="ASSO") {
		//-- Add a new ASSOciate
		echo "<a href=\"javascript:;\" onclick=\"return expand_layer('newasso');\"><img id=\"newasso_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_asso"]."</a>";
		print_help_link("edit_add_ASSO_help", "qm");
		echo "<br />";
		echo "<div id=\"newasso\" style=\"display: none;\">\n";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		echo "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		// 2 ASSO
		add_simple_tag(($level)." ASSO @");
		// 3 RELA
		add_simple_tag(($level+1)." RELA");
		// 3 NOTE
		add_simple_tag(($level+1)." NOTE");
		// 3 SHARED_NOTE
		add_simple_tag(($level+1)." SHARED_NOTE");
		echo "</table></div>";
	}
	if ($tag=="NOTE") {
		//-- Retrieve existing note or add new note to fact
		$text = "";
		echo "<a href=\"javascript:;\" onclick=\"return expand_layer('newnote');\"><img id=\"newnote_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_note"]."</a>";
		print_help_link("edit_add_NOTE_help", "qm");
		echo "<br />\n";
		echo "<div id=\"newnote\" style=\"display: none;\">\n";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		echo "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		// 2 NOTE
		add_simple_tag(($level)." NOTE ".$text);
		echo "</table></div>";
	}
	if ($tag=="SHARED_NOTE") {
		//-- Retrieve existing shared note or add new shared note to fact
		$text = "";
		echo "<a href=\"javascript:;\" onclick=\"return expand_layer('newshared_note');\"><img id=\"newshared_note_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_shared_note"]."</a>";
		print_help_link("edit_add_SHARED_NOTE_help", "qm");
		echo "<br />\n";
		echo "<div id=\"newshared_note\" style=\"display: none;\">\n";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		echo "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		// 2 SHARED NOTE
		add_simple_tag(($level)." SHARED_NOTE ");

		echo "</table></div>";
	}
	if ($tag=="OBJE") {
		//-- Add new obje to fact
		echo "<a href=\"javascript:;\" onclick=\"return expand_layer('newobje');\"><img id=\"newobje_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$pgv_lang["add_obje"]."</a>";
		print_help_link("add_media_help", "qm");
		echo "<br />";
		echo "<div id=\"newobje\" style=\"display: none;\">\n";
		if ($printSaveButton) echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		echo "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
		add_simple_tag($level." OBJE");
		echo "</table></div>";
	}
	if ($tag=="RESN") {
		if (!$PRIVACY_BY_RESN && $level==1) {
			// PRIVACY_BY_RESN is not active for level 1 tags
			// do not display
		} else {
			//-- Retrieve existing resn or add new resn to fact
			$text = "";
			echo "<a href=\"javascript:;\" onclick=\"return expand_layer('newresn');\"><img id=\"newresn_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" /> ".$factarray["RESN"]."</a>";
			print_help_link("RESN_help", "qm");
			echo "<br />\n";
			echo "<div id=\"newresn\" style=\"display: none;\">\n";
			if ($printSaveButton) echo "<input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
			echo "<table class=\"facts_table center $TEXT_DIRECTION\">\n";
			// 2 RESN
			add_simple_tag(($level)." RESN ".$text);
			echo "</table></div>";
		}
	}
}

// Add some empty tags to create a new fact
function addSimpleTags($fact) {
	global $ADVANCED_PLAC_FACTS, $factarray;

	add_simple_tag("0 {$fact}");
	add_simple_tag("0 DATE", $fact, fact_label("{$fact}:DATE"));
	add_simple_tag("0 PLAC", $fact, fact_label("{$fact}:PLAC"));

	if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
		foreach ($match[1] as $tag) {
			add_simple_tag("0 {$tag}", $fact, fact_label("{$fact}:PLAC:{$tag}"));
		}
	}
	add_simple_tag("0 MAP", $fact);
	add_simple_tag("0 LATI", $fact);
	add_simple_tag("0 LONG", $fact);
}

// Assemble the pieces of a newly created record into gedcom
function addNewName() {
	global $ADVANCED_NAME_FACTS;

	$gedrec='1 NAME '.safe_POST('NAME', PGV_REGEX_UNSAFE, '//')."\n";

	$tags=array('TYPE', 'NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX', '_MARNM');

	if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_NAME_FACTS, $match)) {
		$tags=array_merge($tags, $match[1]);
	}
	foreach ($tags as $tag) {
		$TAG=safe_POST($tag, PGV_REGEX_UNSAFE);
		if ($TAG) {
			$gedrec.="2 {$tag} {$TAG}\n";
		}
	}
	return $gedrec;
}
function addNewSex() {
	switch (safe_POST('SEX', '[MF]', 'U')) {
	case 'M':
		return "1 SEX M\n";
	case 'F':
		return "1 SEX F\n";
	default:
		return "1 SEX U\n";
	}
}
function addNewFact($fact) {
	global $tagSOUR, $ADVANCED_PLAC_FACTS;

	$FACT=safe_POST($fact,          PGV_REGEX_UNSAFE);
	$DATE=safe_POST("{$fact}_DATE", PGV_REGEX_UNSAFE);
	$PLAC=safe_POST("{$fact}_PLAC", PGV_REGEX_UNSAFE);
	if ($DATE || $PLAC || $FACT && $FACT!='Y') {
		if ($FACT && $FACT!='Y') {
			$gedrec="1 {$fact} {$FACT}\n";
		} else {
			$gedrec="1 {$fact}\n";
		}
		if ($DATE) {
			$DATE=check_input_date($DATE);
			$gedrec.="2 DATE {$DATE}\n";
		}
		if ($PLAC) {
			$gedrec.="2 PLAC {$PLAC}\n";

			if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
				foreach ($match[1] as $tag) {
					$TAG=safe_POST("{$fact}_{$tag}", PGV_REGEX_UNSAFE);
					if ($TAG) {
						$gedrec.="3 {$tag} {$TAG}\n";
					}
				}
			}
			$LATI=safe_POST("{$fact}_LATI", PGV_REGEX_UNSAFE);
			$LONG=safe_POST("{$fact}_LONG", PGV_REGEX_UNSAFE);
			if ($LATI || $LONG) {
				$gedrec.="3 MAP\n4 LATI {$LATI}\n4 LONG {$LONG}\n";
			}
		}
		if (safe_POST_bool("SOUR_{$fact}")) {
			return updateSOUR($gedrec, 2);
		} else {
			return $gedrec;
		}
	} elseif ($FACT=='Y') {
		if (safe_POST_bool("SOUR_{$fact}")) {
			return updateSOUR("1 {$fact} Y\n", 2);
		} else {
			return "1 {$fact} Y\n";
		}
	} else {
		return '';
	}
}

/**
* Add Debug Log
*
* This function adds debugging information to the log file
* only if debugging output is enabled in session.php
* @param string $logstr the string to add to the log
*/
function addDebugLog($logstr) {
	if (PGV_DEBUG) {
		AddToChangeLog($logstr);
	}
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

	if (count($tagSOUR)==0) return $inputRec; // No update required

	// Save original interface update arrays before replacing them with the xxxSOUR ones
	$glevelsSave = $glevels;
	$tagSave = $tag;
	$islinkSave = $islink;
	$textSave = $text;

	$glevels = $glevelsSOUR;
	$tag = $tagSOUR;
	$islink = $islinkSOUR;
	$text = $textSOUR;

	$myRecord = handle_updates($inputRec, $levelOverride); // Now do the update

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

	if (count($tagRest)==0) return $inputRec; // No update required

	// Save original interface update arrays before replacing them with the xxxRest ones
	$glevelsSave = $glevels;
	$tagSave = $tag;
	$islinkSave = $islink;
	$textSave = $text;

	$glevels = $glevelsRest;
	$tag = $tagRest;
	$islink = $islinkRest;
	$text = $textRest;

	$myRecord = handle_updates($inputRec, $levelOverride); // Now do the update

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
* @param string $newged the new gedcom record to add the lines to
* @param int $levelOverride Override GEDCOM level specified in $glevels[0]
* @return string The updated gedcom record
*/
function handle_updates($newged, $levelOverride="no") {
	global $glevels, $islink, $tag, $uploaded_files, $text, $NOTE, $WORD_WRAPPED_NOTES;

	if ($levelOverride=="no" || count($glevels)==0) $levelAdjust = 0;
	else $levelAdjust = $levelOverride - $glevels[0];

	for($j=0; $j<count($glevels); $j++) {

// BH These following lines destroyed the Shared Note
// Therefore they have been removed for now
/*
		//-- update external note records first
		if (($islink[$j])&&($tag[$j]=="NOTE")) {
			if (empty($NOTE[$text[$j]])) {
				delete_gedrec($text[$j]);
				$text[$j] = "";
			} else {
				$noterec = find_gedcom_record($text[$j]);
				$newnote = "0 @$text[$j]@ NOTE\n";
				$newline = "1 CONC ".rtrim(stripLRMRLM($NOTE[$text[$j]]));
				$newnote .= breakConts($newline);
				if (PGV_DEBUG) {
					echo "<pre>$newnote</pre>";
				}
				replace_gedrec($text[$j], $newnote);
			}
		} //-- end of external note handling code
*/

		//echo $glevels[$j]." ".$tag[$j];

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
			// echo $newline;
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
* @param string $datestr the date input by the user
* @return string the converted date string
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
		echo "<tr><td class=\"descriptionbox\">";
		print_help_link("RESN_help", "qm");
		echo $factarray["RESN"];
		echo "</td>\n";
		echo "<td class=\"optionbox\" colspan=\"3\">\n";
		echo "<select name=\"$name\" tabindex=\"".$tabkey."\" ><option value=\"\"></option><option value=\"confidential\"";
		$tabkey++;
		echo ">".$pgv_lang["confidential"]."</option><option value=\"locked\"";
		echo ">".$pgv_lang["locked"]."</option><option value=\"privacy\"";
		echo ">".$pgv_lang["privacy"]."</option>";
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
}


/**
* Link Media ID to Indi, Family, or Source ID
*
* Code was removed from inverselink.php to become a callable function
*
* @param  string  $mediaid Media ID to be linked
* @param string $linktoid Indi, Family, or Source ID that the Media ID should link to
* @param int $level Level where the Media Object reference should be created
* @return  bool success or failure
*/
function linkMedia($mediaid, $linktoid, $level=1) {
	global $GEDCOM, $pgv_lang, $pgv_changes;

	if (empty($level)) $level = 1;
	//-- Make sure we only add new links to the media object
	if (exists_db_link($mediaid, $linktoid, $GEDCOM)) return false;
	if ($level!=1) return false; // Level 2 items get linked elsewhere
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
		//$mediarec = "1 OBJE @".$mediaid."@\n";
		//$newrec = trim($gedrec."\n".$mediarec);
		$newrec = $gedrec."\n1 OBJE @".$mediaid."@";

		replace_gedrec($linktoid, $newrec);

		return true;
	} else {
		echo "<br /><center>".$pgv_lang["invalid_id"]."</center>";
		return false;
	}
}

/**
* builds the form for adding new facts
* @param string $fact the new fact we are adding
*/
function create_add_form($fact) {
	global $tags, $pgv_lang, $factarray, $FULL_SOURCES;

	$tags = array();

	// handle  MARRiage TYPE
	if (substr($fact,0,5)=="MARR_") {
		$tags[0] = "MARR";
		add_simple_tag("1 MARR");
		insert_missing_subtags($fact);
	} else {
		$tags[0] = $fact;
		if ($fact=='_UID') {
			$fact.=" ".uuid();
		}
		// These new level 1 tags need to be turned into links
		if (in_array($fact, array('ASSO'))) {
			$fact.=' @';
		}
		add_simple_tag("1 ".$fact);
		insert_missing_subtags($tags[0]);
		//-- handle the special SOURce case for level 1 sources [ 1759246 ]
		if ($fact=="SOUR") {
			add_simple_tag("2 PAGE");
			add_simple_tag("3 TEXT");
			if ($FULL_SOURCES) {
				add_simple_tag("3 DATE", "", $factarray["DATA:DATE"]);
				add_simple_tag("2 QUAY");
			}
		}
	}
}

/**
* creates the form for editing the fact within the given gedcom record at the
* given line number
* @param string $gedrec the level 0 gedcom record
* @param int $linenum the line number of the fact to edit within $gedrec
* @param string $level0type the type of the level 0 gedcom record
*/
function create_edit_form($gedrec, $linenum, $level0type) {
	global $WORD_WRAPPED_NOTES, $pgv_lang, $factarray;
	global $pid, $tags, $ADVANCED_PLAC_FACTS, $date_and_time, $templefacts;
	global $lang_short_cut, $LANGUAGE, $FULL_SOURCES;

	$tags=array();
	$gedlines = split("\n", $gedrec); // -- find the number of lines in the record
	if (!isset($gedlines[$linenum])) {
		echo "<span class=\"error\">".$pgv_lang["edit_concurrency_msg1"]."<br /><br />";
		echo $pgv_lang["edit_concurrency_reload"]."</span>";
		return;
	}
	$fields = explode(' ', $gedlines[$linenum]);
	$glevel = $fields[0];
	$level = $glevel;

	if ($level!=1 && eregi("/@.*/@", trim($fields[1]))) {
		echo "<span class=\"error\">".$pgv_lang["edit_concurrency_msg1"]."<br /><br />";
		echo $pgv_lang["edit_concurrency_reload"]."</span>";
		return;
	}

	$type = trim($fields[1]);
	$level1type = $type;
	if (count($fields)>2) {
		$ct = preg_match("/@.*@/",$fields[2]);
		$levellink = $ct > 0;
	} else {
		$levellink = false;
	}
	$i = $linenum;
	$inSource = false;
	$levelSource = 0;
	$add_date = true;
	// List of tags we would expect at the next level
	// NB add_missing_subtags() already takes care of the simple cases
	// where a level 1 tag is missing a level 2 tag.  Here we only need to
	// handle the more complicated cases.
	$expected_subtags=array(
		'SOUR'=>array('PAGE', 'DATA'),
		'DATA'=>array('TEXT'),
		'PLAC'=>array('MAP'),
		'MAP' =>array('LATI', 'LONG')
	);
	if ($FULL_SOURCES) {
		$expected_subtags['SOUR'][]='QUAY';
		$expected_subtags['DATA'][]='DATE';
	}
	if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
		$expected_subtags['PLAC']=array_merge($match[1], $expected_subtags['PLAC']);
	}

	$stack=array(0=>$level0type);
	// Loop on existing tags :
	while (true) {
		// Keep track of our hierarchy, e.g. 1=>BIRT, 2=>PLAC, 3=>FONE
		$stack[(int)$level]=$type;
		// Merge them together, e.g. BIRT:PLAC:FONE
		$label=implode(':', array_slice($stack, 1, $level));

		$text = "";
		for($j=2; $j<count($fields); $j++) {
			if ($j>2) $text .= " ";
			$text .= $fields[$j];
		}
		$text = rtrim($text);
		while(($i+1<count($gedlines))&&(preg_match("/".($level+1)." CONT ?(.*)/", $gedlines[$i+1], $cmatch)>0)) {
			$text.="\n".$cmatch[1];
			$i++;
		}

		// Shared Note -------------
		//if (eregi("@N.*@", $type)) {
		//	$type="note";
		//}

		if ($type=="SOUR") {
			$inSource = true;
			$levelSource = $level;
		} elseif ($levelSource>=$level){
			$inSource = false;
		}

		if ($type!="DATA" && $type!="CONT") {
			$tags[]=$type;
			if ($type=='DATE') {
				// Allow the user to edit the date in his/her own natural language
				$conversion_function="gedcom_to_edit_date_{$lang_short_cut[$LANGUAGE]}";
				if (function_exists($conversion_function))
					$text=$conversion_function($text);
				else
					$text=default_gedcom_to_edit_date($text);
			}
			if ($type=="_AKAN" || $type=="_AKA" || $type=="ALIA") {
				// Allow special processing for different languages
				$func="fact_AKA_localisation_{$lang_short_cut[$LANGUAGE]}";
				if (function_exists($func)) {
					// Localise the AKA fact
					$func($type, $pid);
				}
			}
			$subrecord = $level." ".$type." ".$text;
			if ($inSource && $type=="DATE") {
				add_simple_tag($subrecord, "", fact_label($label));
			} elseif (!$inSource && $type=="DATE") {
				add_simple_tag($subrecord, $level1type, fact_label($label));
				$add_date = false;
			} else {
				add_simple_tag($subrecord, $level0type, fact_label($label));
			}
		}

		// Get a list of tags present at the next level
		$subtags=array();
		for ($ii=$i+1; isset($gedlines[$ii]) && preg_match('/^\s*(\d+)\s+(\S+)/', $gedlines[$ii], $mm) && $mm[1]>$level; ++$ii)
			if ($mm[1]==$level+1)
				$subtags[]=$mm[2];

		// Insert missing tags
		if (!empty($expected_subtags[$type])) {
			foreach ($expected_subtags[$type] as $subtag) {
				if (!in_array($subtag, $subtags)) {
					if (!$inSource || $subtag!="DATA") {
						add_simple_tag(($level+1).' '.$subtag, '', fact_label("{$label}:{$subtag}"));
					}
					if (!empty($expected_subtags[$subtag])) {
						foreach ($expected_subtags[$subtag] as $subsubtag) {
							add_simple_tag(($level+2).' '.$subsubtag, '', fact_label("{$label}:{$subtag}:{$subsubtag}"));
						}
					}
				}
			}
		}

		// Awkward special cases
		if ($level==2 && $type=='DATE' && in_array($level1type, $date_and_time) && !in_array('TIME', $subtags)) {
			add_simple_tag("3 TIME"); // TIME is NOT a valid 5.5.1 tag
		}
		if ($level==2 && $type=='STAT' && in_array($level1type, $templefacts) && !in_array('DATE', $subtags)) {
			add_simple_tag("3 DATE", "", $factarray['STAT:DATE']);
		}

		$i++;
		if (isset($gedlines[$i])) {
			$fields = explode(' ', $gedlines[$i]);
			$level = $fields[0];
			if (isset($fields[1])) {
				$type = trim($fields[1]);
			} else {
				$level = 0;
			}
		} else {
			$level = 0;
		}
		if ($level<=$glevel) break;
	}

	insert_missing_subtags($level1type, $add_date);
	return $level1type;
}

/**
* Populates the global $tags array with any missing sub-tags.
* @param string $level1tag the type of the level 1 gedcom record
*/
function insert_missing_subtags($level1tag, $add_date=false)
{
	global $tags, $date_and_time, $templefacts, $level2_tags, $ADVANCED_PLAC_FACTS, $factarray;
	global $nondatefacts, $nonplacfacts;

	// handle  MARRiage TYPE
	$type_val = "";
	if (substr($level1tag,0,5)=="MARR_") {
		$type_val = substr($level1tag,5);
		$level1tag = "MARR";
	}

	foreach ($level2_tags as $key=>$value) {
		if ($key=='DATE' && in_array($level1tag, $nondatefacts) || $key=='PLAC' && in_array($level1tag, $nonplacfacts)) {
			break;
		}
		if (in_array($level1tag, $value) && !in_array($key, $tags)) {
			if ($key=="TYPE") {
				add_simple_tag("2 TYPE ".$type_val);
			} elseif ($level1tag=='_TODO' && $key=='DATE') {
				add_simple_tag("2 ".$key." ".strtoupper(date('d F Y')));
			} elseif ($level1tag=='_TODO' && $key=='_PGVU') {
				add_simple_tag("2 ".$key." ".PGV_USER_NAME);
			} else {
				add_simple_tag("2 ".$key);
			}
			switch ($key) { // Add level 3/4 tags as appropriate
				case "PLAC":
					if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
						foreach ($match[1] as $tag) {
							add_simple_tag("3 $tag", "", fact_label("{$level1tag}:PLAC:{$tag}"));
						}
					}
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
		} elseif ($key=="DATE" && $add_date) {
			add_simple_tag("2 DATE", $level1tag, fact_label("{$level1tag}:DATE"));
		}
	}
	// Do something (anything!) with unrecognised custom tags
	if (substr($level1tag, 0, 1)=='_' && $level1tag!='_UID' && $level1tag!='_TODO')
		foreach (array('DATE', 'PLAC', 'ADDR', 'AGNC', 'TYPE', 'AGE') as $tag)
			if (!in_array($tag, $tags)) {
				add_simple_tag("2 {$tag}");
				if ($tag=='PLAC') {
					if (preg_match_all('/('.PGV_REGEX_TAG.')/', $ADVANCED_PLAC_FACTS, $match)) {
						foreach ($match[1] as $tag) {
							add_simple_tag("3 $tag", "", fact_label("{$level1tag}:PLAC:{$tag}"));
						}
					}
					add_simple_tag("3 MAP");
					add_simple_tag("4 LATI");
					add_simple_tag("4 LONG");
				}
			}
}

/**
* Delete a person and update all records that link to that person
* @param string $pid the id of the person to delete
* @param string $gedrec the gedcom record of the person to delete
* @return boolean true or false based on the successful completion of the deletion
*/
function delete_person($pid, $gedrec='') {
	// NOTE: $pgv_changes isn't a global.  Making it global appears to cause problems.
	global $pgv_lang, $GEDCOM;
	if (PGV_DEBUG) {
		phpinfo(INFO_VARIABLES);
		echo "<pre>$gedrec</pre>";
	}

	if (empty($gedrec)) $gedrec = find_person_record($pid);
	if (!empty($gedrec)) {
		$success = true;
		$ct = preg_match_all("/1 FAM. @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famid = $match[$i][1];
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_gedcom_record($famid);
			else $famrec = find_updated_record($famid);
			if (!empty($famrec)) {
				$lines = explode("\n", $famrec);
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
				$pt = preg_match_all("/1 (?:HUSB|WIFE|CHIL) @(.*)@/", $newfamrec, $pmatch, PREG_SET_ORDER);
				if ($pt<2) {
					for ($j=0; $j<$pt; $j++) {
						$xref = $pmatch[$j][1];
						if($xref!=$pid) {
							if (!isset($pgv_changes[$xref."_".$GEDCOM])) $indirec = find_gedcom_record($xref);
							else $indirec = find_updated_record($xref);
							$indirec = preg_replace("/1.*@$famid@.*/", "", $indirec);
							if (PGV_DEBUG) {
								echo "<pre>$indirec</pre>";
							}
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
* @param string $pid the id of the person to delete
* @param string $gedrec the gedcom record of the person to delete
* @return boolean true or false based on the successful completion of the deletion
*/
function delete_family($pid, $gedrec='') {
	// NOTE: $pgv_changes isn't a global.  Making it global appears to cause problems.
	global $GEDCOM, $pgv_lang;
	if (empty($gedrec)) $gedrec = find_family_record($pid);
	if (!empty($gedrec)) {
		$success = true;
		$ct = preg_match_all("/1 (\w+) @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$type = $match[$i][1];
			$id = $match[$i][2];
			if (PGV_DEBUG) {
				echo $type." ".$id." ";
			}
			if (!isset($pgv_changes[$id."_".$GEDCOM])) $indirec = find_gedcom_record($id);
			else $indirec = find_updated_record($id);
			if (!empty($indirec)) {
				$lines = explode("\n", $indirec);
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

// Create a label for editing tags, such as BIRT:DATE, BIRT:PLAC:_HEB
// or PLAC:FONE.  Use specific labels, if available.  Use general ones if not.
function fact_label($tag) {
	global $factarray;

	while ($tag) {
		if (array_key_exists($tag, $factarray)) {
			return $factarray[$tag];
		} elseif (strpos($tag, ':')) {
			list(, $tag)=explode(':', $tag, 2);
		} else {
			return '';
		}
	}
	return '';
}

?>

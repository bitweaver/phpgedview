<?php
/**
 * Defines a protocol for interfacing remote requests over a http connection
 *
 * When $action is 'get' then the gedcom record with the given $xref is retrieved.
 * When $action is 'update' the gedcom record matching $xref is replaced with the data in $gedrec.
 * When $action is 'append' the gedcom record in $gedrec is appended to the end of the gedcom file.
 * When $action is 'delete' the gedcom record with $xref is removed from the file.
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
 * @version $Id: client.php,v 1.8 2008/08/10 11:37:23 lsces Exp $
 */

require "config.php";

require "includes/functions_edit.php";
header("Content-Type: text/plain; charset=$CHARACTER_SET");

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];

$pgv_user = getUserName();
$READ_ONLY = 0;
if ((isset($_SESSION["readonly"]))&&($_SESSION["readonly"]==true)) $READ_ONLY = 1;
if (!empty($_REQUEST["GEDCOM"])) {
	if (!isset($gGedcom[$_REQUEST["GEDCOM"]])) {
		addDebugLog("ERROR 21: Invalid GEDCOM specified.  Remember that the GEDCOM is case sensitive.");
		print "ERROR 21: Invalid GEDCOM specified.  Remember that the GEDCOM is case sensitive.\n";
		exit;
	}
}
if (empty($action)) {
	addDebugLog("ERROR 1: No action specified.");
	print "ERROR 1: No action specified.\n";
}
else if (count($gGedcom)==0) {
	addDebugLog($action." ERROR 21: No Gedcoms available on this site.");
	print "ERROR 21: No Gedcoms available on this site.\n";
	exit;
}
else if (!check_for_import($GEDCOM)) {
	addDebugLog($action." ERROR 22: Gedcom [$GEDCOM] needs to be imported.");
	print "ERROR 22: Gedcom [$GEDCOM] needs to be imported.\n";
	exit;
}
else if ($action=='version') {
	addDebugLog($action." SUCCESS\n$VERSION $VERSION_RELEASE\n");
	print "SUCCESS\n$VERSION $VERSION_RELEASE\n";
}
else if ($action=='connect') {
	if (!empty($username)) {
		$userStat = authenticateUser($username,$password);
		if (!$userStat) {
			addDebugLog($action." username=$username ERROR 10: Username and password key failed to authenticate.");
			print "ERROR 10: Username and password key failed to authenticate.\n";
		}
		else {
			$stat = newConnection();
			if ($stat!==false) {
				addDebugLog($action." username=$username SUCCESS\n".$stat);
				print "SUCCESS\n".$stat;
			}
			$_SESSION['connected']=$username;
			$canedit = userCanEdit($username);
			if (!$canedit) {
				AddToLog('Read-Only Client connection from '.$username);
			}
			/*else {
				print "ERROR 11: Username $username does not have write permissions.\n";
			}*/
		}
	}
	else {
		$stat = newConnection();
		if ($stat!==false) {
			addDebugLog($action." SUCCESS\n".$stat);
			print "SUCCESS\n".$stat;
		}
		AddToLog('Read-Only Anonymous Client connection.');
		$_SESSION['connected']='Anonymous';
		$_SESSION['readonly']=1;
		//print "ERROR 9: Could not connect to GEDCOM.  No username specified.\n";
	}
	if (!empty($readonly)) $_SESSION['readonly']=1;
}
else if ($action=='listgedcoms') {
	$out_msg = "SUCCESS\n";
	foreach($gGedcom as $ged=>$gedarray) {
		$out_msg .= "$ged\t".$gedarray["title"]."\n";
	}
	addDebugLog($action." ".$out_msg);
	print $out_msg;
}
else if (empty($_SESSION['connected'])){
	addDebugLog($action." ERROR 12: use 'connect' action to initiate a session.");
	print "ERROR 12: use 'connect' action to initiate a session.\n";
}
else if ($action=='get') {
	if (isset($_REQUEST['xref'])) $xref = $_REQUEST['xref'];
	if (!empty($xref)) {
		$xrefs = preg_split("/[;, ]/", $xref);
		$success = true;
		$gedrecords="";
		foreach($xrefs as $indexval => $xref1) {
			$gedrec = "";
			$xref1 = trim($xref1);
			$xref1 = clean_input($xref1);
			if (!empty($xref1)) {
				if (isset($pgv_changes[$xref1."_".$GEDCOM])) $gedrec = find_updated_record($xref1);
				if (empty($gedrec)) $gedrec = find_gedcom_record($xref1);
				if (!empty($gedrec)) {
					$gedrec = trim($gedrec);
					preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
					$type = trim($match[2]);
					if (displayDetails($gedrec)) $gedrecords = $gedrecords . "\n".trim($gedrec);
					else {
						//-- do not have full access to this record, so privatize it
						$gedrec = privatize_gedcom($gedrec);
						$gedrecords = $gedrecords . "\n".trim($gedrec);
						//$success=false;
						//print "ERROR 18: Access denied for individual xref:$xref1.\n";
					}
				}
				else {
					// finding nothing is not an error
				}
			}
		} //-- end for loop
		if ($success) {
			if (empty($_REQUEST['keepfile'])) {
				$ct = preg_match_all("/ FILE (.*)/", $gedrecords, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
					$mediaurl = $SERVER_URL.$MEDIA_DIRECTORY.extract_filename($match[$i][1]);
					$gedrecords = str_replace($match[$i][1], $mediaurl, $gedrecords);
				}
			}
			addDebugLog($action." xref=$xref ".$gedrecords);
			print "SUCCESS\n".trim($gedrecords);
		}
	}
	else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
}
else if ($action=='getvar') {
	if (isset($_REQUEST['var'])) $var = $_REQUEST['var'];
	$public_vars = array("READ_ONLY","CHARACTER_SET","GEDCOM","PEDIGREE_ROOT_ID");
	if (!empty($var) && (in_array($var, $public_vars)) && isset($$var)) {
		addDebugLog($action." var=$var SUCCESS\n".$$var);
		print "SUCCESS\n".$$var;
	}
	else if ((!empty($pgv_user))&&(!empty($var))&&(isset($$var))&&(!in_array($var, $CONFIG_VARS))) {
		addDebugLog($action." var=$var SUCCESS\n".$$var);
		print "SUCCESS\n".$$var;
	}
	else {
		addDebugLog($action." var=$var ERROR 13: Invalid variable specified.  Please provide a variable.");
		print "ERROR 13: Invalid variable specified.\n";
	}
}
else if ($action=='update') {
	if (isset($_REQUEST['xref'])) $xref = $_REQUEST['xref'];
	if (!empty($xref)) {
		if (isset($_REQUEST['gedrec'])) $gedrec = $_REQUEST['gedrec'];
		if (empty($gedrec)) $gedrec = trim($HTTP_RAW_POST_DATA);
		if (!empty($gedrec)) {
			if ((empty($_SESSION['readonly']))&&(userCanEdit($pgv_user))&&(displayDetails($gedrec))) {
				$gedrec = preg_replace(array("/\\\\+r/","/\\\\+n/"), array("\r","\n"), $gedrec);
				$success = replace_gedrec($xref, $gedrec);
				if ($success) {
					addDebugLog($action." xref=$xref gedrec=$gedrec SUCCESS");
					print "SUCCESS\n";
				}
			}
			else {
				addDebugLog($action." xref=$xref ERROR 11: No write privileges for this record.");
				print "ERROR 11: No write privileges for this record.\n";
			}
		}
		else {
			addDebugLog($action." xref=$xref ERROR 8: No gedcom record provided.  Unable to process request.");
			print "ERROR 8: No gedcom record provided.  Unable to process request.\n";
		}
	}
	else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
}
else if ($action=='append') {
	if (isset($_REQUEST['gedrec'])) $gedrec = $_REQUEST['gedrec'];
	if (empty($gedrec)) $gedrec = trim($HTTP_RAW_POST_DATA);
	if (!empty($gedrec)) {
		if ((empty($_SESSION['readonly']))&&(userCanEdit($pgv_user))) {
			$gedrec = preg_replace(array("/\\\\+r/","/\\\\+n/"), array("\r","\n"), $gedrec);
			$xref = append_gedrec($gedrec);
			if ($xref) {
				addDebugLog($action." gedrec=$gedrec SUCCESS\n$xref");
				print "SUCCESS\n$xref\n";
			}
		}
		else {
			addDebugLog($action." gedrec=$gedrec ERROR 11: No write privileges for this record.");
			print "ERROR 11: No write privileges for this record.\n";
		}
	}
	else {
		addDebugLog($action." ERROR 8: No gedcom record provided.  Unable to process request.");
		print "ERROR 8: No gedcom record provided.  Unable to process request.\n";
	}
}
else if ($action=='delete') {
	if (isset($_REQUEST['xref'])) $xref = $_REQUEST['xref'];
	if (!empty($xref)) {
		if ((empty($_SESSION['readonly']))&&(userCanEdit($pgv_user))&&(displayDetailsById($xref))) {
			$success = delete_gedrec($xref);
			if ($success) {
				addDebugLog($action." xref=$xref SUCCESS");
				print "SUCCESS\n";
			}
		}
		else {
			addDebugLog($action." xref=$xref ERROR 11: No write privileges for this record.");
			print "ERROR 11: No write privileges for this record.\n";
		}
	}
	else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
}
else if ($action=='getnext') {
	if (isset($_REQUEST['xref'])) $xref = $_REQUEST['xref'];
	$myindilist = get_indi_list();
	$gedrec="";
	if (!empty($xref)) {
		$xref1 = get_next_xref($xref);
		if (isset($pgv_changes[$xref1."_".$GEDCOM])) $gedrec = @find_updated_record($xref1);
		if (empty($gedrec)) $gedrec = @find_gedcom_record($xref1);
		if (!displayDetails($gedrec)) {
			//-- do not have full access to this record, so privatize it
			$gedrec = privatize_gedcom($gedrec);
		}
		addDebugLog($action." xref=$xref SUCCESS\n".trim($gedrec));
		print "SUCCESS\n".trim($gedrec);
	}
	else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
}
else if ($action=='getprev') {
	if (isset($_REQUEST['xref'])) $xref = $_REQUEST['xref'];
	$myindilist = get_indi_list();
	$gedrec="";
	if (!empty($xref)) {
		$xref1 = get_prev_xref($xref);
		if (isset($pgv_changes[$xref1."_".$GEDCOM])) $gedrec = @find_updated_record($xref1);
		if (empty($gedrec)) $gedrec = @find_gedcom_record($xref1);
		if (!displayDetails($gedrec)) {
			//-- do not have full access to this record, so privatize it
			$gedrec = privatize_gedcom($gedrec);
		}
		addDebugLog($action." xref=$xref SUCCESS\n".trim($gedrec));
		print "SUCCESS\n".trim($gedrec);
	}
	else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
}
else if ($action=='search') {
	if (isset($_REQUEST['query'])) $query = $_REQUEST['query'];
	if (!empty($query)) {
		$sindilist = search_indis($query);
		uasort($sindilist, "itemsort");
		$msg_out = "SUCCESS\n";
		foreach($sindilist as $xref=>$indi) {
			if (displayDetailsById($xref)) $msg_out .= "$xref\n";
		}
		addDebugLog($action." query=$query ".$msg_out);
		print $msg_out;
	}
	else {
		addDebugLog($action." ERROR 15: No query specified.  Please specify a query.");
		print "ERROR 15: No query specified.  Please specify a query.\n";
	}
}
else if ($action=='soundex') {
	if (isset($_REQUEST['lastname'])) $lastname = $_REQUEST['lastname'];
	if (isset($_REQUEST['firstname'])) $firstname = $_REQUEST['firstname'];
	if (isset($_REQUEST['place'])) $place = $_REQUEST['$place'];
	if (isset($_REQUEST['soundex'])) $soundex = $_REQUEST['$soundex'];
	
	if(empty($soundex)) $soundex = "Russell";
	if ((!empty($lastname))||(!empty($firstname))) {
		$res = search_indis_soundex($soundex, $lastname, $firstname, $place);
		$msg_out = "SUCCESS\n";
		// -- only get the names who match soundex
		while( $value = $res->fetchRow() ) {
			$indilist[$row['sx_n_id']]["gedcom"] = $row['i_gedcom'];
			$indilist[$row['sx_n_id']]["names"] = get_indi_names($row['i_gedcom']);
			$indilist[$row['sx_n_id']]["isdead"] = $row['i_isdead'];
			$indilist[$row['sx_n_id']]["gedfile"] = $row['i_file'];
			if (displayDetailsById($xref)) $msg_out .= "$xref\n";
		}
		addDebugLog($action." lastname=$lastname firstname=$firstname ".$msg_out);
		print $msg_out;
	}
	else {
		addDebugLog($action." ERROR 16: No names specified.  Please specify a firstname or a lastname.");
		print "ERROR 16: No names specified.  Please specify a firstname or a lastname.\n";
	}
}
else if ($action=='getxref') {
	if (isset($_REQUEST['position'])) $position = $_REQUEST['position'];
	if (isset($_REQUEST['type'])) $type = $_REQUEST['type'];
	if (empty($position)) $position='first';
	if (empty($type)) $type='INDI';
	if ((empty($type))||(!in_array($type, array("INDI","FAM","SOUR","REPO","NOTE","OBJE","OTHER")))) {
		addDebugLog($action." type=$type position=$position ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER");
		print "ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER\n";
		exit;
	}
	
	if ($position=='first') {
		switch($type) {
			case "INDI":
				$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(i_id,2)";
				break;
			case "FAM":
				$sql = "SELECT f_id FROM ".$TBLPREFIX."families WHERE f_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(f_id,2)";
				break;
			case "SOUR":
				$sql = "SELECT s_id FROM ".$TBLPREFIX."sources WHERE s_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(s_id,2)";
				break;
			case "REPO":
				$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='REPO' ORDER BY 0+SUBSTRING(o_id,2)";
				break;
			case "NOTE":
				$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='NOTE' ORDER BY 0+SUBSTRING(o_id,2)";
				break;
			case "OBJE":
				$sql = "SELECT m_media FROM ".$TBLPREFIX."media WHERE m_gedfile=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(m_media,2)";
				break;
			case "OTHER":
				$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(o_id,2)";
				break;
		}
		$xref = $gGedcom->mDb->getOne($sql);
		addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
		print "SUCCESS\n$xref\n";
	}
	else if ($position=='last') {
		switch($type) {
			case "INDI":
				$sql = "SELECT i_id FROM ".$TBLPREFIX."individuals WHERE i_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(i_id,2)";
				break;
			case "FAM":
				$sql = "SELECT f_id FROM ".$TBLPREFIX."families WHERE f_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(f_id,2)";
				break;
			case "SOUR":
				$sql = "SELECT s_id FROM ".$TBLPREFIX."sources WHERE s_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(s_id,2)";
				break;
			case "REPO":
				$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='REPO' ORDER BY 0+SUBSTRING(o_id,2)";
				break;
			case "NOTE":
				$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='NOTE' ORDER BY 0+SUBSTRING(o_id,2)";
				break;
			case "OBJE":
				$sql = "SELECT m_media FROM ".$TBLPREFIX."media WHERE m_gedfile=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(m_media,2)";
				break;
			case "OTHER":
				$sql = "SELECT o_id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(o_id,2)";
				break;
		}
		$sql .= " DESC";
		$xref = $gGedcom->mDb->getOne($sql);
		addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
		print "SUCCESS\n$xref\n";
	}
	else if ($position=='next') {
		if (!empty($xref)) {
			$xref1 = get_next_xref($xref, $type);
			if ($xref1!==false) {
				addDebugLog($action." type=$type position=$position xref=$xref SUCCESS\n$xref1");
				print "SUCCESS\n$xref1\n";
			}
		}
		else {
			addDebugLog($action." type=$type position=$position ERROR 3: No gedcom id specified.  Please specify a xref.");
			print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
		}
	}
	else if ($position=='prev') {
		if (!empty($xref)) {
			$xref1 = get_prev_xref($xref, $type);
			if ($xref1!==false) {
				addDebugLog($action." type=$type position=$position xref=$xref SUCCESS\n$xref1");
				print "SUCCESS\n$xref1\n";
			}
		}
		else {
			addDebugLog($action." type=$type position=$position ERROR 3: No gedcom id specified.  Please specify a xref.");
			print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
		}
	}
	else if ($position=='all') {
		$msg_out = "SUCCESS\n";
		switch($type) {
			case "INDI":
				$sql = "SELECT i_id AS id FROM ".$TBLPREFIX."individuals WHERE i_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(i_id,2)";
				break;
			case "FAM":
				$sql = "SELECT f_id AS id FROM ".$TBLPREFIX."families WHERE f_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(f_id,2)";
				break;
			case "SOUR":
				$sql = "SELECT s_id AS id FROM ".$TBLPREFIX."sources WHERE s_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(s_id,2)";
				break;
			case "REPO":
				$sql = "SELECT o_id AS id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='REPO' ORDER BY 0+SUBSTRING(o_id,2)";
				break;
			case "NOTE":
				$sql = "SELECT o_id AS id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." AND o_type='NOTE' ORDER BY 0+SUBSTRING(o_id,2)";
				break;
			case "OBJE":
				$sql = "SELECT m_media AS id FROM ".$TBLPREFIX."media WHERE m_gedfile=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(m_media,2)";
				break;
			case "OTHER":
				$sql = "SELECT o_id AS id FROM ".$TBLPREFIX."other WHERE o_file=".$gGedcom->mGEDCOMId." ORDER BY 0+SUBSTRING(o_id,2)";
				break;
		}
		$res = $gGedcom->mDb->query($sql);
		while ($row = $res->fetchRow()) {		
			$msg_out .= "$row['id']\n";
		}
		$res->free();
		addDebugLog($action." type=$type position=$position ".$msg_out);
		print $msg_out;
	}
	else if ($position=='new') {
		if ((empty($_SESSION['readonly']))&&(userCanEdit($pgv_user))) {
			if ((empty($type))||(!in_array($type, array("INDI","FAM","SOUR","REPO","NOTE","OBJE")))) {
				addDebugLog($action." type=$type position=$position ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE");
				print "ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE\n";
				exit;
			}
			$gedrec = "0 @REF@ $type";
			$xref = append_gedrec($gedrec);
			if ($xref) {
				addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
				print "SUCCESS\n$xref\n";
			}
		}
		else {
			addDebugLog($action." type=$type position=$position ERROR 11: No write privileges for this record.");
			print "ERROR 11: No write privileges for this record.\n";
		}
	}
	else {
		addDebugLog($action." type=$type position=$position ERROR 17: Unknown position reference.  Valid values are first, last, prev, next.");
		print "ERROR 17: Unknown position reference.  Valid values are first, last, prev, next.\n";
	}
}
else if ($action=="uploadmedia") {
	$error="";
	$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
	if (isset($_FILES['mediafile'])) {
		if (!move_uploaded_file($_FILES['mediafile']['tmp_name'], $MEDIA_DIRECTORY.$_FILES['mediafile']['name'])) {
			$error .= "ERROR 19: ".$pgv_lang["upload_error"]." ".$upload_errors[$_FILES['mediafile']['error']];
		}
		else if (!isset($_FILES['thumbnail'])) {
			$filename = $MEDIA_DIRECTORY.$_FILES['mediafile']['name'];
			$thumbnail = $MEDIA_DIRECTORY."thumbs/".$_FILES['mediafile']['name'];
			generate_thumbnail($filename, $thumbnail);
			//if (!$thumbgenned) $error .= "ERROR 19: ".$pgv_lang["thumbgen_error"].$filename;
		}
	}
	if (isset($_FILES['thumbnail'])) {
		if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $MEDIA_DIRECTORY."thumbs/".$_FILES['thumbnail']['name'])) {
			$error .= "\nERROR 19: ".$pgv_lang["upload_error"]." ".$upload_errors[$_FILES['thumbnail']['error']];
		}
	}
	if (!empty($error)) {
		addDebugLog($action." $error");
		print $error."\n";
	}
	else {
		addDebugLog($action." SUCCESS");
		print "SUCCESS\n";
	}
}
else if ($action=="getchanges") {
	if (isset($_REQUEST['date'])) $date = $_REQUEST['date'];
	if (empty($date)) {
		addDebugLog($action." ERROR 23: Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.");
		print "ERROR 23: Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.\n";
	}
	else {
		$lastdate = new GedcomDate($date);
		
		if (!$lastdate->isOK()) {
			addDebugLog($action." ERROR 23: Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.");
			print "ERROR 23: Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.\n";
		} else {
			print "SUCCESS\n";
			if ($lastdate->MinJD()<server_jd()-180) {
				addDebugLog($action." ERROR 24: You cannot retrieve updates for more than 180 days.");
				print "ERROR 24: You cannot retrieve updates for more than 180 days.\n";
			} else {
				$changes = get_recent_changes($lastdate->MinJD());
				$results = array();
				foreach($changes as $id=>$change) {
					print $change['d_gid']."\n";
				}
			}
		}
	}
}
else {
	addDebugLog($action." ERROR 2: Unable to process request.  Unknown action.");
	print "ERROR 2: Unable to process request.  Unknown action.\n";
}
?>

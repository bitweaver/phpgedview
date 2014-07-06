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
* Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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

require "config.php";

require_once 'includes/functions/functions_edit.php';

header("Content-Type: text/plain; charset=$CHARACTER_SET");

$READ_ONLY = ((isset($_SESSION['readonly']))&&($_SESSION['readonly']==true)) ? 1 : 0;

// Make sure there is at least one gedcom.
if (count(get_all_gedcoms())==0) {
	addDebugLog($action." ERROR 21: No Gedcoms available on this site.");
	print "ERROR 21: No Gedcoms available on this site.\n";
	exit;
}

$gedcom=safe_REQUEST($_REQUEST,'GEDCOM');
if ($gedcom) {
	if (!in_array($gedcom, get_all_gedcoms())) {
		addDebugLog("ERROR 21: Invalid GEDCOM specified.  Remember that the GEDCOM is case sensitive.");
		print "ERROR 21: Invalid GEDCOM specified.  Remember that the GEDCOM is case sensitive.\n";
		exit;
	}
	$GEDCOM=$gedcom;
}
$GED_ID=get_id_from_gedcom($GEDCOM);

if (!check_for_import($GEDCOM)) {
	addDebugLog($action." ERROR 22: Gedcom [$GEDCOM] needs to be imported.");
	print "ERROR 22: Gedcom [$GEDCOM] needs to be imported.\n";
	exit;
}

$action=safe_REQUEST($_REQUEST,'action');

// The following actions can be performed without being connected.
switch ($action) {
case '':
	addDebugLog("ERROR 1: No action specified.");
	print "ERROR 1: No action specified.\n";
	exit;
case 'version':
	addDebugLog($action." SUCCESS\n".PGV_VERSION_TEXT."\n");
	print "SUCCESS\n".PGV_VERSION_TEXT."\n";
	exit;
case 'connect':
	$username=safe_REQUEST($_REQUEST,'username');
	if ($username) {
		$password=safe_REQUEST($_REQUEST,'password');
		$user_id=authenticateUser($username, $password);
		if ($user_id) {
			$stat=newConnection();
			if ($stat!==false) {
				addDebugLog($action." username=$username SUCCESS\n".$stat);
				print "SUCCESS\n".$stat;
			}
			$_SESSION['connected']=$user_id;
		} else {
			addDebugLog($action." username=$username ERROR 10: Username and password key failed to authenticate.");
			print "ERROR 10: Username and password key failed to authenticate.\n";
		}
	} else {
		$stat=newConnection();
		if ($stat!==false) {
			addDebugLog($action." SUCCESS\n".$stat);
			print "SUCCESS\n".$stat;
		}
		AddToLog('Read-Only Anonymous Client connection.');
		$_SESSION['connected']='Anonymous';
		$_SESSION['readonly']=1;
	}
	exit;
case 'listgedcoms':
	$out_msg = "SUCCESS\n";
	foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
		$out_msg.="$gedcom\t".get_gedcom_setting($ged_id, 'title')."\n";
	}
	addDebugLog($action." ".$out_msg);
	print $out_msg;
	exit;
default:
	// All other actions require an authenticated connection
	if (empty($_SESSION['connected'])){
		addDebugLog($action." ERROR 12: use 'connect' action to initiate a session.");
		print "ERROR 12: use 'connect' action to initiate a session.\n";
		exit;
	}
	break;
}

// The following actions can only be performed when connected
switch ($action) {
case 'get':
	$xref=safe_REQUEST($_REQUEST,'xref', PGV_REGEX_XREF.'([ ,;]+'.PGV_REGEX_XREF.')*');
	$view=safe_REQUEST($_REQUEST,'view', PGV_REGEX_ALPHANUM);
	if ($xref) {
		$xrefs = preg_split("/[;, ]/", $xref, 0, PREG_SPLIT_NO_EMPTY);
		$gedrecords="";
		foreach ($xrefs as $xref1) {
			if (!empty($xref1)) {
				$gedrec=find_updated_record($xref1);
				if (!$gedrec) {
					$gedrec=find_gedcom_record($xref1);
					if ($gedrec) {
						preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
						$type = trim($match[2]);
						if (!displayDetailsById($xref1, $type)) {
							//-- do not have full access to this record, so privatize it
							$gedrec = privatize_gedcom($gedrec);
						}
						else if ($view=='version' || $view=='change') {
							$chan = get_gedcom_value('CHAN', 1, $gedrec);
							if (empty($chan)) {
								$head = find_gedcom_record("HEAD");
								$head_date = get_sub_record(1, "1 DATE", $head);
								$lines = explode("\n", $head_date);
								$head_date = "";
								foreach($lines as $line) {
									$num = $line{0};
									$head_date.=($num+1).substr($line, 1)."\n";
								}
								$chan = "1 CHAN\n".$head_date;
							}
							$gedrec = '0 @'.$xref1.'@ '.$type."\n".$chan;
						}
						if (!empty($gedrec)) $gedrecords = $gedrecords . "\n".trim($gedrec);
					}
				}
			}
		}
		if (!safe_REQUEST($_REQUEST,'keepfile')) {
			$ct = preg_match_all("/ FILE (.*)/", $gedrecords, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$mediaurl = $SERVER_URL.$MEDIA_DIRECTORY.extract_filename($match[$i][1]);
				$gedrecords = str_replace($match[$i][1], $mediaurl, $gedrecords);
			}
		}
		addDebugLog($action." xref=$xref ".$gedrecords);
		print "SUCCESS\n".$gedrecords;
	} else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
	exit;
case 'getvar':
	$var=safe_REQUEST($_REQUEST,'var', '[A-Za-z0-9_]+');
	$public_vars = array("READ_ONLY","CHARACTER_SET","GEDCOM","PEDIGREE_ROOT_ID");
	if ($var && in_array($var, $public_vars) && isset($$var)) {
		addDebugLog($action." var=$var SUCCESS\n".$$var);
		print "SUCCESS\n".$$var;
	} else if (PGV_USER_ID && $var && isset($$var) && !in_array($var, $CONFIG_VARS)) {
		addDebugLog($action." var=$var SUCCESS\n".$$var);
		print "SUCCESS\n".$$var;
	} else {
		addDebugLog($action." var=$var ERROR 13: Invalid variable specified.  Please provide a variable.");
		print "ERROR 13: Invalid variable specified.\n";
	}
	exit;
case 'update':
	$xref=safe_REQUEST($_REQUEST,'xref', PGV_REGEX_XREF);
	if ($xref) {
		$gedrec=safe_REQUEST($_REQUEST,'gedrec', PGV_REGEX_UNSAFE); // raw data may contain any characters
		if ($gedrec) {
			if (empty($_SESSION['readonly']) && PGV_USER_CAN_EDIT && displayDetailsById($xref)) {
				$gedrec = preg_replace(array("/\\\\+r/","/\\\\+n/"), array("\r","\n"), $gedrec);
				$success = replace_gedrec($xref, $gedrec);
				if ($success) {
					addDebugLog($action." xref=$xref gedrec=$gedrec SUCCESS");
					print "SUCCESS\n";
				}
			} else {
				addDebugLog($action." xref=$xref ERROR 11: No write privileges for this record.");
				print "ERROR 11: No write privileges for this record.\n";
			}
		} else {
			addDebugLog($action." xref=$xref ERROR 8: No gedcom record provided.  Unable to process request.");
			print "ERROR 8: No gedcom record provided.  Unable to process request.\n";
		}
	} else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
	exit;
case 'append':
	$gedrec=safe_REQUEST($_REQUEST,'gedrec', '.*'); // raw data may contain any characters
	if ($gedrec) {
		if (empty($_SESSION['readonly']) && PGV_USER_CAN_EDIT) {
			$gedrec = preg_replace(array("/\\\\+r/","/\\\\+n/"), array("\r","\n"), $gedrec);
			$xref = append_gedrec($gedrec);
			if ($xref) {
				addDebugLog($action." gedrec=$gedrec SUCCESS\n$xref");
				print "SUCCESS\n$xref\n";
			}
		} else {
			addDebugLog($action." gedrec=$gedrec ERROR 11: No write privileges for this record.");
			print "ERROR 11: No write privileges for this record.\n";
		}
	} else {
		addDebugLog($action." ERROR 8: No gedcom record provided.  Unable to process request.");
		print "ERROR 8: No gedcom record provided.  Unable to process request.\n";
	}
	exit;
case 'delete':
	$xref=safe_REQUEST($_REQUEST,'xref', PGV_REGEX_XREF);
	if ($xref) {
		if (empty($_SESSION['readonly']) && PGV_USER_CAN_EDIT && displayDetailsById($xref)) {
			$success = delete_gedrec($xref);
			if ($success) {
				addDebugLog($action." xref=$xref SUCCESS");
				print "SUCCESS\n";
			}
		} else {
			addDebugLog($action." xref=$xref ERROR 11: No write privileges for this record.");
			print "ERROR 11: No write privileges for this record.\n";
		}
	} else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
	exit;
case 'getnext':
	$xref=safe_REQUEST($_REQUEST,'xref', PGV_REGEX_XREF);
	if ($xref) {
		$xref1 = get_next_xref($xref, $GED_ID);
		$gedrec = find_updated_record($xref1);
		if (!$gedrec) {
			$gedrec = find_gedcom_record($xref1);
		}
		if (!displayDetailsById($xref1)) {
			//-- do not have full access to this record, so privatize it
			$gedrec = privatize_gedcom($gedrec);
		}
		addDebugLog($action." xref=$xref SUCCESS\n".trim($gedrec));
		print "SUCCESS\n".trim($gedrec);
	} else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
	exit;
case 'getprev':
	$xref=safe_REQUEST($_REQUEST,'xref', PGV_REGEX_XREF);
	if ($xref) {
		$xref1 = get_prev_xref($xref, $GED_ID);
		$gedrec = find_updated_record($xref1);
		if (!$gedrec) {
			$gedrec = find_gedcom_record($xref1);
		}
		if (!displayDetailsById($xref1)) {
			//-- do not have full access to this record, so privatize it
			$gedrec = privatize_gedcom($gedrec);
		}
		addDebugLog($action." xref=$xref SUCCESS\n".trim($gedrec));
		print "SUCCESS\n".trim($gedrec);
	} else {
		addDebugLog($action." ERROR 3: No gedcom id specified.  Please specify a xref.");
		print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
	}
	exit;
case 'search':
	$query=safe_REQUEST($_REQUEST,'query');
	if ($query) {
		$sindilist=search_indis(array($query), array(PGV_GED_ID), 'AND', true);
		print "SUCCESS\n";
		addDebugLog($action." query=$query SUCCESS");
		foreach($sindilist as $indi) {
			echo $indi->getXref(), "\n";
		}
	} else {
		addDebugLog($action." ERROR 15: No query specified.  Please specify a query.");
		print "ERROR 15: No query specified.  Please specify a query.\n";
	}
	exit;
case 'soundex':
	$lastname=safe_REQUEST($_REQUEST,'lastname');
	$firstname=safe_REQUEST($_REQUEST,'firstname');
	$place=safe_REQUEST($_REQUEST,'place');
	$soundex=safe_REQUEST($_REQUEST,'soundex', '\w+', 'Russell');

	if ($lastname || $firstname) {
		$sindilist=search_indis_soundex($soundex, $lastname, $firstname, $place, array(PGV_GED_ID));
		print "SUCCESS\n";
		addDebugLog($action." lastname=$lastname firstname=$firstname SUCCESS");
		foreach($sindilist as $indi) {
			echo $indi->getXref(), "\n";
		}
	} else {
		addDebugLog($action." ERROR 16: No names specified.  Please specify a firstname or a lastname.");
		print "ERROR 16: No names specified.  Please specify a firstname or a lastname.\n";
	}
	exit;
case 'getxref':
	$position=safe_REQUEST($_REQUEST,'position', array('first','last','next','prev','new','all'));
	$type=safe_REQUEST($_REQUEST,'type', array('INDI','FAM','SOUR','REPO','NOTE','OBJE','OTHER'));
	$xref=safe_REQUEST($_REQUEST,'xref', PGV_REGEX_XREF);

	if ($position=='next' && !$xref) {
		$position='first';
	}
	if ($position=='prev' && !$xref) {
		$position='last';
	}

	if (!$position || !$type) {
		addDebugLog($action." type=$type position=$position ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER");
		print "ERROR 18: Invalid \$type or \$position specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER\n";
		exit;
	}
	global $gBitDb;
	
	switch ($position) {
	case 'first':
		$xref=get_first_xref($type, $GED_ID);
		addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
		print "SUCCESS\n$xref\n";
		break;
	case 'last':
		$xref=get_last_xref($type, $GED_ID);
		addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
		print "SUCCESS\n$xref\n";
		break;
	case 'next':
		$xref=get_next_xref($xref, $GED_ID);
		addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
		print "SUCCESS\n$xref\n";
		break;
	case 'prev':
		$xref=get_prev_xref($xref, $GED_ID);
		addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
		print "SUCCESS\n$xref\n";
		break;
	case 'all':
		switch($type) {
			case "INDI":
				$rows = $gBitDb->query(
					"SELECT i_id FROM {$TBLPREFIX}individuals WHERE i_file=? ORDER BY i_id"
					, array($GED_ID));
				break;
			case "FAM":
				$rows = $gBitDb->query(
					"SELECT f_id FROM {$TBLPREFIX}families WHERE f_file=? ORDER BY f_id"
					, array($GED_ID));
				break;
			case "SOUR":
				$rows = $gBitDb->query(
					"SELECT s_id FROM {$TBLPREFIX}sources WHERE s_file=? ORDER BY s_id"
					, array($GED_ID));
			case "OBJE":
				$rows = $gBitDb->query(
					"SELECT m_media FROM {$TBLPREFIX}media WHERE m_gedfile=? ORDER BY m_media"
					, array($GED_ID));
			case "OTHER":
				$rows = $gBitDb->query(
					"SELECT o_id FROM {$TBLPREFIX}other WHERE o_file=? AND o_type NOT IN ('REPO', 'NOTE') ORDER BY o_id"
					, array($GED_ID));
				break;
			default:
				$rows = $gBitDb->query(
					"SELECT o_id FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=? ORDER BY o_id"
					, array($GED_ID, $type));
		}
		print "SUCCESS\n";
		while ( $id = $rows->fetchColumn(0) ) {
			print "{$id}\n";
		}
		addDebugLog($action." type=$type position=$position ");
		break;
	case 'new':
		if (empty($_SESSION['readonly']) && PGV_USER_CAN_EDIT) {
			$gedrec = "0 @REF@ $type";
			$xref = append_gedrec($gedrec);
			if ($xref) {
				addDebugLog($action." type=$type position=$position SUCCESS\n$xref");
				print "SUCCESS\n$xref\n";
			}
		} else {
			addDebugLog($action." type=$type position=$position ERROR 11: No write privileges for this record.");
			print "ERROR 11: No write privileges for this record.\n";
		}
		break;
	}
	exit;
case 'uploadmedia':
	$error="";
	if (isset($_FILES['mediafile'])) {
		if (!move_uploaded_file($_FILES['mediafile']['tmp_name'], $MEDIA_DIRECTORY.$_FILES['mediafile']['name'])) {
			$error .= "ERROR 19: ".$pgv_lang["upload_error"]." ".file_upload_error_text($_FILES['mediafile']['error']);
		} else if (!isset($_FILES['thumbnail'])) {
			$filename = $MEDIA_DIRECTORY.$_FILES['mediafile']['name'];
			$thumbnail = $MEDIA_DIRECTORY."thumbs/".$_FILES['mediafile']['name'];
			generate_thumbnail($filename, $thumbnail);
			//if (!$thumbgenned) $error .= "ERROR 19: ".$pgv_lang["thumbgen_error"].$filename;
		}
	}
	if (isset($_FILES['thumbnail'])) {
		if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $MEDIA_DIRECTORY."thumbs/".$_FILES['thumbnail']['name'])) {
			$error .= "\nERROR 19: ".$pgv_lang["upload_error"]." ".file_upload_error_text($_FILES['thumbnail']['error']);
		}
	}
	if (!empty($error)) {
		addDebugLog($action." $error");
		print $error."\n";
	} else {
		addDebugLog($action." SUCCESS");
		print "SUCCESS\n";
	}
	exit;
case 'getchanges':
	$lastdate = new GedcomDate(safe_REQUEST($_REQUEST,'date', '\d\d \w\w\w \d\d\d\d'));
	if ($lastdate->isOK()) {
		if ($lastdate->MinJD()<server_jd()-180) {
			addDebugLog($action." ERROR 24: You cannot retrieve updates for more than 180 days.");
			print "ERROR 24: You cannot retrieve updates for more than 180 days.\n";
		} else {
			print "SUCCESS\n";
			foreach(get_recent_changes($lastdate->MinJD()) as $xref) {
				echo "{$xref}\n";
			}
		}
	} else {
		addDebugLog($action." ERROR 23: Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.");
		print "ERROR 23: Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.\n";
	}
	exit;
default:
	addDebugLog($action." ERROR 2: Unable to process request.  Unknown action.");
	print "ERROR 2: Unable to process request.  Unknown action.\n";
}
?>

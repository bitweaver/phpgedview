<?php
/**
* PopUp Window to provide users with a simple quick update form.
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
* This Page Is Valid XHTML 1.0 Transitional! > 19 August 2005
*
* @package PhpGedView
* @subpackage Edit
* @version $Id: edit_quickupdate.php,v 1.8 2010/02/08 21:27:24 wjames5 Exp $
*/

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require 'config.php';

require_once './includes/functions/functions_edit.php';
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

if ((isset($_POST["preserve_last_changed"])) && ($_POST["preserve_last_changed"] == "on"))
	$update_CHAN = false;
else
	$update_CHAN = true;

$addfacts = preg_split("/[,; ]/", $QUICK_ADD_FACTS);
usort($addfacts, "factsort");
$reqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FACTS);

$famaddfacts = preg_split("/[,; ]/", $QUICK_ADD_FAMFACTS);
usort($famaddfacts, "factsort");
$famreqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FAMFACTS);

$align="right";
if ($TEXT_DIRECTION=="rtl") $align="left";

print_simple_header($pgv_lang["quick_update_title"]);

if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';

//-- only allow logged in users to access this page
if (!$ALLOW_EDIT_GEDCOM || !$USE_QUICK_UPDATE || !PGV_USER_ID) {
	echo $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

if (!isset($closewin)) {
	$closewin=0;
}

// TODO Decide whether to use GET/POST and appropriate validation
$pid     =safe_REQUEST($_REQUEST, 'pid', PGV_REGEX_XREF, PGV_USER_GEDCOM_ID);
$action  =safe_REQUEST($_REQUEST, 'action');
$closewin=safe_REQUEST($_REQUEST, 'closewin', '1', '0');

//-- only allow editors or users who are editing their own individual or their immediate relatives
if (!PGV_USER_CAN_EDIT) {
	$famids = pgv_array_merge(find_sfamily_ids(PGV_USER_GEDCOM_ID), find_family_ids(PGV_USER_GEDCOM_ID));
	$related=false;
	foreach ($famids as $famid) {
		if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
		else $famrec = find_updated_record($famid);
		if (preg_match("/1 (HUSB|WIFE|CHIL) @$pid@/", $famrec)) {
			$related=true;
			break;
		}
	}
	if (!$related) {
		echo $pgv_lang["access_denied"];
		print_simple_footer();
		exit;
	}
}

//-- find the latest gedrec for the individual
if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
else $gedrec = find_updated_record($pid);

// Don't allow edits if the record has changed since the edit-link was created
checkChangeTime($pid, $gedrec, safe_GET('accesstime', PGV_REGEX_INTEGER));


//-- only allow edit of individual records
$disp = true;
$ct = preg_match("/0 @$pid@ (.*)/", $gedrec, $match);
if ($ct>0) {
	$type = trim($match[1]);
	if ($type=="INDI") {
		$disp = displayDetailsById($pid);
	}
	else {
		echo $pgv_lang["access_denied"];
		print_simple_footer();
		exit;
	}
}

if ((!$disp)||(!$ALLOW_EDIT_GEDCOM)) {

	echo $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!PGV_USER_CAN_EDIT) echo "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) echo "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		echo "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) echo "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
	}
	print_simple_footer();
	exit;
}

//-- privatize the record so that line numbers etc. match what was in the display
//-- data that is hidden because of privacy is stored in the $pgv_private_records array
//-- any private data will be restored when the record is replaced
$gedrec = privatize_gedcom($gedrec);

//-- put the updates into the gedcom record
if ($action=="update") {
	function check_updated_facts($i, &$famrec, $TAGS, $prefix){
		global $typefacts, $pid, $pgv_lang, $factarray;

		$famrec = trim($famrec);
		$famupdate = false;
		$repeat_tags = array();
		$var = $prefix.$i."DESCS";
		if (!empty($_POST[$var])) $DESCS = $_POST[$var];
		else $DESCS = array();
		$var = $prefix.$i."DATES";
		if (!empty($_POST[$var])) $DATES = $_POST[$var];
		else $DATES = array();
		$var = $prefix.$i."PLACS";
		if (!empty($_POST[$var])) $PLACS = $_POST[$var];
		else $PLACS = array();
		$var = $prefix.$i."TEMPS";
		if (!empty($_POST[$var])) $TEMPS = $_POST[$var];
		else $TEMPS = array();
		$var = $prefix.$i."RESNS";
		if (!empty($_POST[$var])) $RESNS = $_POST[$var];
		else $RESNS = array();
		$var = $prefix.$i."REMS";
		if (!empty($_POST[$var])) $REMS = $_POST[$var];
		else $REMS = array();
		$var = "F".$i."MARRY";
		if (!empty($_REQUEST[$var])) $FMARRY = $_REQUEST[$var];
		else $FMARRY = "";
		$var = "F".$i."DIVY";
		if (!empty($_REQUEST[$var])) $FDIVY = $_REQUEST[$var];
		else $FDIVY = "";

		for($j=0; $j<count($TAGS); $j++) {
			if (!empty($TAGS[$j])) {
				$fact = $TAGS[$j];
				if (!isset($repeat_tags[$fact])) $repeat_tags[$fact] = 1;
				else $repeat_tags[$fact]++;

				$DATES[$j] = check_input_date($DATES[$j]);
				if (!isset($REMS[$j])) $REMS[$j] = 0;
				if ($REMS[$j]==1) {
					$DESCS[$j]="";
					$DATES[$j]="";
					$PLACS[$j]="";
					$TEMPS[$j]="";
					$RESNS[$j]="";
				}
				if ((empty($DATES[$j]))&&(empty($PLACS[$j]))&&(empty($TEMPS[$j]))&&(empty($RESNS[$j]))) {
					if (!empty($FMARRY)) $factrec = "1 MARR Y\n";
					else if (!empty($FDIVY)) $factrec = "1 DIV Y\n";
					else $factrec="";
				}
				else {
					if (!in_array($fact, $typefacts)) $factrec = "1 $fact";
					else $factrec = "1 EVEN\n2 TYPE $fact";
					if (!empty($DESCS[$j])) $factrec .= " $DESCS[$j]\n";
					else $factrec .= "\n";
					if (!empty($DATES[$j])) $factrec .= "2 DATE $DATES[$j]\n";
					if (!empty($PLACS[$j])) $factrec .= "2 PLAC $PLACS[$j]\n";
					if (!empty($TEMPS[$j])) $factrec .= "2 TEMP $TEMPS[$j]\n";
					if (!empty($RESNS[$j])) $factrec .= "2 RESN $RESNS[$j]\n";
				}
				if (!in_array($fact, $typefacts)) $lookup = "1 $fact";
				else $lookup = "1 EVEN\n2 TYPE $fact\n";
				$pos1 = strpos($famrec, $lookup);
				$k=1;
				//-- make sure we are working with the correct fact
				while($k<$repeat_tags[$fact]) {
					$pos1 = strpos($famrec, $lookup, $pos1+5);
					$k++;
					if ($pos1===false) break;
				}
				$noupdfact = false;
				if ($pos1!==false) {
					$pos2 = strpos($famrec, "\n1 ", $pos1+5);
					if ($pos2===false) $pos2 = strlen($famrec);
					$oldfac = trim(substr($famrec, $pos1, $pos2-$pos1));
					$noupdfact = FactEditRestricted($pid, $oldfac);
					if ($noupdfact) {
						echo "<br />".$pgv_lang["update_fact_restricted"]." ".$factarray[$fact]."<br /><br />";
					}
					else {
						//-- delete the fact
						if ($REMS[$j]==1) {
							$famupdate = true;
							$famrec = substr($famrec, 0, $pos1) . "\n". substr($famrec, $pos2);
						}
						else if (!empty($oldfac) && !empty($factrec)) {
							$factrec = $oldfac;
							if (!empty($DESCS[$j])) {
								if (strstr($factrec, "1 $fact")) $factrec = preg_replace("/1 $fact.*/", "1 $fact $DESCS[$j]", $factrec);
								else $factrec = $factrec."\n1 $fact $DESCS[$j]";
							}
							if (!empty($DATES[$j])) {
								if (strstr($factrec, "\n2 DATE")) $factrec = preg_replace("/2 DATE.*/", "2 DATE $DATES[$j]", $factrec);
								else $factrec = $factrec."\n2 DATE $DATES[$j]";
							}
							if (!empty($PLACS[$j])) {
								if (strstr($factrec, "\n2 PLAC")) $factrec = preg_replace("/2 PLAC.*/", "2 PLAC $PLACS[$j]", $factrec);
								else $factrec = $factrec."\n2 PLAC $PLACS[$j]";
							}
							if (!empty($TEMPS[$j])) {
								if (strstr($factrec, "\n2 TEMP")) $factrec = preg_replace("/2 TEMP.*/", "2 TEMP $TEMPS[$j]", $factrec);
								else $factrec = $factrec."\n2 TEMP $TEMPS[$j]";
							}
							if (!empty($RESNS[$j])) {
								if (strstr($factrec, "\n2 RESN")) $factrec = preg_replace("/2 RESN.*/", "2 RESN $RESNS[$j]", $factrec);
								else $factrec = $factrec."\n2 RESN $RESNS[$j]";
							}

							$factrec = preg_replace("/[\r\n]+/", "\n", $factrec);
							$oldfac = preg_replace("/[\r\n]+/", "\n", $oldfac);
							if (trim($factrec) != trim($oldfac)) {
								$famupdate = true;
								$famrec = substr($famrec, 0, $pos1) . trim($factrec)."\n" . substr($famrec, $pos2);
							}
						}
					}
				}
				else if (!empty($factrec)) {
					$famrec .= "\n".$factrec;
					$famupdate = true;
				}
			}
		}
		return $famupdate;
	}

	$person=Person::getInstance($pid);
	echo "<h2>".$pgv_lang["quick_update_title"]."</h2>\n";
	echo "<b>".PrintReady(stripLRMRLM($person->getFullName()))."</b><br /><br />";

	AddToChangeLog("Quick update attempted for $pid by >".PGV_USER_NAME."<");

	$updated = false;
	$error = "";
	$oldgedrec = $gedrec;
	//-- check for name update
	if (isset($_REQUEST['GIVN'])) $GIVN = $_REQUEST['GIVN'];
	if (isset($_REQUEST['SURN'])) $SURN = $_REQUEST['SURN'];
	if (isset($_REQUEST['MRSURN'])) $MRSURN = $_REQUEST['MRSURN'];
	if (isset($GIVN) || isset($SURN) || isset($MRSURN)) {
		$namerec = trim(get_sub_record(1, "1 NAME", $gedrec));
		if (!empty($namerec)) {
			if (isset($GIVN)) {
				//-- check if name line has a GIVN and a SURN
				if (preg_match("~1 NAME.+/.*/~", $namerec)>0) {
					$namerec = preg_replace("/1 NAME.+\/(.*)\//", "1 NAME $GIVN /$1/", $namerec);
				}
				else {
					$namerec = preg_replace("/1 NAME.+/", "1 NAME $GIVN", $namerec);
				}
				if (preg_match("/2 GIVN/", $namerec)>0) $namerec = preg_replace("/2 GIVN.*/", "2 GIVN $GIVN\n", $namerec);
				else $namerec.="\n2 GIVN $GIVN";
			}
			if (isset($SURN)) {
				//-- check if name line has a GIVN and a SURN
				if (preg_match("~1 NAME.+/.*/~", $namerec)>0) {
					$namerec = preg_replace("/1 NAME(.+)\/.*\//", "1 NAME$1/$SURN/", $namerec);
				}
				else {
					$namerec = preg_replace("/1 NAME ([\w.\ -_]+)/", "1 NAME $1 /$SURN/\n", $namerec);
				}
				if (preg_match("/2 SPFX (.*)/", $namerec, $match)>0) {
					$SURN = str_replace(trim($match[1])." ", "", $SURN);
				}
				if (preg_match("/2 SURN/", $namerec)>0) $namerec = preg_replace("/2 SURN.*/", "2 SURN $SURN\n", $namerec);
				else $namerec.="\n2 SURN $SURN";
			}
			//-- update the married surname
			if (isset($MRSURN) && !empty($MRSURN)) {
				if (preg_match("/2 _MARNM/", $namerec)>0) $namerec = preg_replace("/2 _MARNM.*/", "2 _MARNM /$MRSURN/\n", $namerec);
				else $namerec.="\n2 _MARNM /$MRSURN/";
			}
			$pos1 = strpos($gedrec, "1 NAME");
			if ($pos1!==false) {
				$pos2 = strpos($gedrec, "\n1", $pos1+5);
				if ($pos2===false) {
					$gedrec = substr($gedrec, 0, $pos1).$namerec;
				}
				else {
					$gedrec = substr($gedrec, 0, $pos1).$namerec."\n".substr($gedrec, $pos2+1);
				}
			}
		}
		else $gedrec .= "\n1 NAME $GIVN /$SURN/\n2 GIVN $GIVN\n2 SURN $SURN\n2 _MARNM /$MRSURN/";
		$updated = true;
	}

	//-- update the person's gender
	if (isset($_REQUEST['GENDER'])) $GENDER = $_REQUEST['GENDER'];
	if (!empty($GENDER)) {
		if (preg_match("/1 SEX (\w*)/", $gedrec, $match)>0) {
			if ($match[1] != $GENDER) {
				$gedrec = preg_replace("/1 SEX (\w*)/", "1 SEX $GENDER", $gedrec);
				$updated = true;
			}
		}
		else {
			$gedrec .= "\n1 SEX $GENDER";
			$updated = true;
		}
	}
	//-- rtl name update
	if (isset($_REQUEST['HSURN'])) $HSURN = $_REQUEST['HSURN'];
	if (isset($_REQUEST['HGIVN'])) $HGIVN = $_REQUEST['HGIVN'];
	if (!empty($HSURN) || !empty($HGIVN)) {
		if (preg_match("/2 _HEB/", $gedrec)>0) {
			if (!empty($HGIVN)) {
				$gedrec = preg_replace("/2 _HEB.+\/(.*)\//", "2 _HEB $HGIVN /$1/", $gedrec);
			}
			if (!empty($HSURN)) {
				$gedrec = preg_replace("/2 _HEB(.+)\/.*\//", "2 _HEB$1/$HSURN/", $gedrec);
			}
		}
		else {
			$pos1 = strpos($gedrec, "1 NAME");
			if ($pos1!==false) {
				$pos1 = strpos($gedrec, "\n1", $pos1+5);
				if ($pos1===false) $pos1 = strlen($gedrec)-1;
				$gedrec = substr($gedrec, 0, $pos1)."\n2 _HEB $HGIVN /$HSURN/\n".substr($gedrec, $pos1+1);
			}
			else $gedrec .= "\n1 NAME $HGIVN /$HSURN/\n2 _HEB $HGIVN /$HSURN/\n";
		}
		$updated = true;
	}
	if (isset($_REQUEST['RSURN'])) $RSURN = $_REQUEST['RSURN'];
	if (isset($_REQUEST['RGIVN'])) $RGIVN = $_REQUEST['RGIVN'];
	if (!empty($RSURN) || !empty($RGIVN)) {
		if (preg_match("/2 ROMN/", $gedrec)>0) {
			if (!empty($RGIVN)) {
				$gedrec = preg_replace("/2 ROMN.+\/(.*)\//", "2 ROMN $RGIVN /$1/", $gedrec);
			}
			if (!empty($RSURN)) {
				$gedrec = preg_replace("/2 ROMN(.+)\/.*\//", "2 ROMN$1/$RSURN/", $gedrec);
			}
		}
		else {
			$pos1 = strpos($gedrec, "1 NAME");
			if ($pos1!==false) {
				$pos1 = strpos($gedrec, "\n1", $pos1+5);
				if ($pos1===false) $pos1 = strlen($gedrec)-1;
				$gedrec = substr($gedrec, 0, $pos1)."\n2 ROMN $RGIVN /$RSURN/\n".substr($gedrec, $pos1+1);
			}
			else $gedrec .= "\n1 NAME $RGIVN /$RSURN/\n2 ROMN $RGIVN /$RSURN/\n";
		}
		$updated = true;
	}

	//-- check for updated facts
	if (isset($_REQUEST['TAGS'])) $TAGS = $_REQUEST['TAGS'];
	if (count($TAGS)>0) {
		$updated |= check_updated_facts("", $gedrec, $TAGS, "");
	}

	//-- check for new fact
	if (isset($_REQUEST['newfact'])) $newfact = $_REQUEST['newfact'];
	if (isset($_REQUEST['newClipboardFact'])) $newfact = $_REQUEST['newClipboardFact'];		// The name mis-match is deliberate
	if (isset($_REQUEST['DATE'])) $DATE = $_REQUEST['DATE'];
	if (isset($_REQUEST['PLAC'])) $PLAC = $_REQUEST['PLAC'];
	if (isset($_REQUEST['TEMP'])) $TEMP = $_REQUEST['TEMP'];
	if (isset($_REQUEST['RESN'])) $RESN = $_REQUEST['RESN'];
	if (isset($_REQUEST['DESC'])) $DESC = $_REQUEST['DESC'];
	if (!empty($newfact)) {
		if (!in_array($newfact, $typefacts)) $factrec = "1 $newfact";
		else $factrec = "1 EVEN\n2 TYPE $newfact";
		if (!empty($DESC)) $factrec .= " $DESC\n";
		else $factrec .= "\n";
		if (!empty($DATE)) {
			$DATE = check_input_date($DATE);
			$factrec .= "2 DATE $DATE\n";
		}
		if (!empty($PLAC)) $factrec .= "2 PLAC $PLAC\n";
		if (!empty($TEMP)) $factrec .= "2 TEMP $TEMP\n";
		if (!empty($RESN)) $factrec .= "2 RESN $RESN\n";
		//-- make sure that there is at least a Y
		if (preg_match("/\n2 \w*/", $factrec)==0 && empty($DESC)) $factrec = "1 $newfact Y\n";
		$gedrec .= "\n".$factrec;
		$updated = true;
	}

	//-- check for photo update
	if (!empty($_FILES["FILE"]['tmp_name'])) {
		if (!move_uploaded_file($_FILES['FILE']['tmp_name'], $MEDIA_DIRECTORY.basename($_FILES['FILE']['name']))) {
			$error .= "<br />".$pgv_lang["upload_error"]."<br />".file_upload_error_text($_FILES['FILE']['error']);
		}
		else {
			$filename = $MEDIA_DIRECTORY.basename($_FILES['FILE']['name']);
			$thumbnail = $MEDIA_DIRECTORY."thumbs/".basename($_FILES['FILE']['name']);
			generate_thumbnail($filename, $thumbnail);

			if (isset($_REQUEST['TITL'])) $TITL = $_REQUEST['TITL'];
			$objrec = "0 @new@ OBJE\n";
			$objrec .= "1 FILE ".$filename."\n";
			if (!empty($TITL)) $objrec .= "2 TITL $TITL\n";
			$objid = append_gedrec($objrec);

			$factrec = "1 OBJE @".$objid."@\n";
			if (empty($replace)) $gedrec .= "\n".$factrec;
			else {
				$fact = "OBJE";
				$pos1 = strpos($gedrec, "1 $fact");
				if ($pos1!==false) {
					$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
					if ($pos2===false) $pos2 = strlen($gedrec)-1;
					$gedrec = substr($gedrec, 0, $pos1) . "\n".$factrec . substr($gedrec, $pos2);
				}
				else $gedrec .= "\n".$factrec;
			}
			$updated = true;
		}
	}

	if (isset($_REQUEST['ADDR'])) $ADDR = $_REQUEST['ADDR'];
	if (isset($_REQUEST['ADR1'])) $ADR1 = $_REQUEST['ADR1'];
	if (isset($_REQUEST['POST'])) $POST = $_REQUEST['POST'];
	if (isset($_REQUEST['ADR2'])) $ADR2 = $_REQUEST['ADR2'];
	if (isset($_REQUEST['CITY'])) $CITY = $_REQUEST['CITY'];
	if (isset($_REQUEST['STAE'])) $STAE = $_REQUEST['STAE'];
	if (isset($_REQUEST['CTRY'])) $CTRY = $_REQUEST['CTRY'];
	//--address phone email
	$factrec = "";
	if (!empty($ADDR)) {
		if (!empty($ADR1)||!empty($CITY)||!empty($POST)) {
			$factrec = "1 ADDR $ADDR\n";
			if (!empty($_NAME)) $factrec.="2 _NAME ".$_NAME."\n";
			if (!empty($ADR1)) $factrec.="2 ADR1 ".$ADR1."\n";
			if (!empty($ADR2)) $factrec.="2 ADR2 ".$ADR2."\n";
			if (!empty($CITY)) $factrec.="2 CITY ".$CITY."\n";
			if (!empty($STAE)) $factrec.="2 STAE ".$STAE."\n";
			if (!empty($POST)) $factrec.="2 POST ".$POST."\n";
			if (!empty($CTRY)) $factrec.="2 CTRY ".$CTRY."\n";
		}
		else {
			$factrec = "1 ADDR ";
			$lines = preg_split("/\r*\n/", $ADDR);
			$factrec .= $lines[0]."\n";
			for($i=1; $i<count($lines); $i++) $factrec .= "2 CONT ".$lines[$i]."\n";
		}
	}
	$pos1 = strpos($gedrec, "1 ADDR");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\n".$factrec;
		$updated = true;
	}

	if (isset($_REQUEST['PHON'])) $PHON = $_REQUEST['PHON'];
	$factrec = "";
	if (!empty($PHON)) $factrec = "1 PHON $PHON\n";
	$pos1 = strpos($gedrec, "1 PHON");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\n".$factrec;
		$updated = true;
	}

	if (isset($_REQUEST['FAX'])) $FAX = $_REQUEST['FAX'];
	$factrec = "";
	if (!empty($FAX)) $factrec = "1 FAX $FAX\n";
	$pos1 = strpos($gedrec, "1 FAX");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\n".$factrec;
		$updated = true;
	}

	if (isset($_REQUEST['EMAIL'])) $EMAIL = $_REQUEST['EMAIL'];
	$factrec = "";
	if (!empty($EMAIL)) $factrec = "1 EMAIL $EMAIL\n";
	$pos1 = strpos($gedrec, "1 EMAIL");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\n".$factrec;
		$updated = true;
	}

	//-- spouse family tabs
	$sfams = find_families_in_record($gedrec, "FAMS");
	for($i=1; $i<=count($sfams); $i++) {
		$famupdate = false;
		$famid = $sfams[$i-1];
		if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
		else $famrec = find_updated_record($famid);
		$oldfamrec = $famrec;
		$parents = find_parents($famid);
		//-- update the spouse
		$spid = "";
		if($parents) {
			if($pid!=$parents["HUSB"]) {
				$tag="HUSB";
				$spid = $parents['HUSB'];
			}
			else {
				$tag = "WIFE";
				$spid = $parents['WIFE'];
			}
		}

		if (isset($_REQUEST['SGIVN'.$i])) $sgivn = $_REQUEST['SGIVN'.$i];
		if (isset($_REQUEST['SSURN'.$i])) $ssurn = $_REQUEST['SSURN'.$i];
		if (isset($_REQUEST['MSSURN'.$i])) $mssurn = $_REQUEST['MSSURN'.$i];
		//--add new spouse name, birth
		if (!empty($sgivn) || !empty($ssurn)) {
			//-- first add the new spouse
			$spouserec = "0 @REF@ INDI\n";
			$spouserec .= "1 NAME ".$sgivn." /".$ssurn."/\n";
			if (!empty($sgivn)) $spouserec .= "2 GIVN ".$sgivn."\n";
			if (!empty($ssurn)) $spouserec .= "2 SURN ".$ssurn."\n";
			if (!empty($mssurn)) $spouserec .= "2 _MARNM /".$mssurn."/\n";

			if (isset($_REQUEST['HSGIVN'.$i])) $hsgivn = $_REQUEST['HSGIVN'.$i];
			if (isset($_REQUEST['HSSURN'.$i])) $hssurn = $_REQUEST['HSSURN'.$i];
			if (!empty($hsgivn) || !empty($hssurn)) {
				$spouserec .= "2 _HEB ".$hsgivn." /".$hssurn."/\n";
			}
			if (isset($_REQUEST['RSGIVN'.$i])) $rsgivn = $_REQUEST['RSGIVN'.$i];
			if (isset($_REQUEST['RSSURN'.$i])) $rssurn = $_REQUEST['RSSURN'.$i];
			if (!empty($rsgivn) || !empty($rssurn)) {
				$spouserec .= "2 ROMN ".$rsgivn." /".$rssurn."/\n";
			}
			if (isset($_REQUEST['SSEX'.$i])) $ssex = $_REQUEST['SSEX'.$i];
			if (!empty($ssex)) $spouserec .= "1 SEX ".$ssex."\n";

			if (isset($_REQUEST['BDATE'.$i])) $bdate = $_REQUEST['BDATE'.$i];
			if (isset($_REQUEST['BPLAC'.$i])) $bplac = $_REQUEST['BPLAC'.$i];
			if (!empty($bdate)||!empty($bplac)) {
				$spouserec .= "1 BIRT\n";
				if (!empty($bdate)) {
					$bdate = check_input_date($bdate);
					$spouserec .= "2 DATE $bdate\n";
				}
				if (!empty($bplac)) $spouserec .= "2 PLAC ".$bplac."\n";
				if (isset($_REQUEST['BRESN'.$i])) $bresn = $_REQUEST['BRESN'.$i];
				if (!empty($bresn)) $spouserec .= "2 RESN ".$bresn."\n";
			}
			if (isset($_REQUEST['DDATE'.$i])) $bdate = $_REQUEST['DDATE'.$i];
			if (isset($_REQUEST['DPLAC'.$i])) $bplac = $_REQUEST['DPLAC'.$i];
			if (!empty($bdate)||!empty($bplac)) {
				$spouserec .= "1 DEAT\n";
				if (!empty($bdate)) {
					$bdate = check_input_date($bdate);
					$spouserec .= "2 DATE $bdate\n";
				}
				if (!empty($bplac)) $spouserec .= "2 PLAC ".$bplac."\n";
				if (isset($_REQUEST['DRESN'.$i])) $bresn = $_REQUEST['DRESN'.$i];
				if (!empty($bresn)) $spouserec .= "2 RESN ".$bresn."\n";
			}
			$spouserec .= "\n1 FAMS @$famid@\n";
			$SPID[$i] = append_gedrec($spouserec);
		}

		if (!empty($SPID[$i]) && $spid!=$SPID[$i]) {
			if (strstr($famrec, "1 $tag")!==false) $famrec = preg_replace("/1 $tag @.*@/", "1 $tag @$SPID[$i]@", $famrec);
			else $famrec .= "\n1 $tag @$SPID[$i]@";
			$famupdate = true;
		}

		//-- check for updated facts
		$var = "F".$i."TAGS";
		if (isset($_REQUEST[$var])) $TAGS = $_REQUEST[$var];
		else $TAGS = array();
		if (count($TAGS)>0) {
			$famupdate |= check_updated_facts($i, $famrec, $TAGS, "F");
		}

		//-- check for new fact
		$var = "F".$i."newfact";
		if (!empty($_REQUEST[$var])) $newfact = $_REQUEST[$var];
		else $newfact = "";
		if (!empty($newfact)) {
			if (!in_array($newfact, $typefacts)) $factrec = "1 $newfact\n";
			else $factrec = "1 EVEN\n2 TYPE $newfact\n";
			$var = "F".$i."DATE";
			if (!empty($_REQUEST[$var])) $FDATE = $_REQUEST[$var];
			else $FDATE = "";
			if (!empty($FDATE)) {
				$FDATE = check_input_date($FDATE);
				$factrec .= "2 DATE $FDATE\n";
			}
			$var = "F".$i."PLAC";
			if (!empty($_REQUEST[$var])) $FPLAC = $_REQUEST[$var];
			else $FPLAC = "";
			if (!empty($FPLAC)) $factrec .= "2 PLAC $FPLAC\n";
			$var = "F".$i."TEMP";
			if (!empty($_REQUEST[$var])) $FTEMP = $_REQUEST[$var];
			else $FTEMP = "";
			if (!empty($FTEMP)) $factrec .= "2 TEMP $FTEMP\n";
			$var = "F".$i."RESN";
			if (!empty($_REQUEST[$var])) $FRESN = $_REQUEST[$var];
			else $FRESN = "";
			if (!empty($FRESN)) $factrec .= "2 RESN $FRESN\n";
			//-- make sure that there is at least a Y
			if (preg_match("/\n2 \w*/", $factrec)==0) $factrec = "1 $newfact Y\n";
			$famrec .= "\n".$factrec;
			$famupdate = true;
		}

		if (isset($_REQUEST['CHIL'])) $CHIL = $_REQUEST['CHIL'];
		if (!empty($CHIL[$i])) {
			$famupdate = true;
			$famrec .= "\n1 CHIL @".$CHIL[$i]."@";
			if (!isset($pgv_changes[$CHIL[$i]."_".$GEDCOM])) $childrec = find_person_record($CHIL[$i]);
			else $childrec = find_updated_record($CHIL[$i]);
			if (preg_match("/1 FAMC @$famid@/", $childrec)==0) {
				$childrec = "\n1 FAMC @$famid@";
				replace_gedrec($CHIL[$i], $childrec, $update_CHAN);
			}
		}

		$var = "F".$i."CDEL";
		if (!empty($_REQUEST[$var])) $fcdel = $_REQUEST[$var];
		else $fcdel = "";
		if (!empty($fcdel)) {
			$famrec = preg_replace("/1 CHIL @$fcdel@/", "", $famrec);
			$famupdate = true;
		}

		//--add new child, name, birth
		$cgivn = "";
		$var = "C".$i."GIVN";
		if (!empty($_REQUEST[$var])) $cgivn = $_REQUEST[$var];
		else $cgivn = "";
		$csurn = "";
		$var = "C".$i."SURN";
		if (!empty($_REQUEST[$var])) $csurn = $_REQUEST[$var];
		else $csurn = "";
		if (!empty($cgivn) || !empty($csurn)) {
			//-- first add the new child
			$childrec = "0 @REF@ INDI\n";
			$childrec .= "1 NAME $cgivn /$csurn/\n";
			if (!empty($cgivn)) $childrec .= "2 GIVN $cgivn\n";
			if (!empty($csurn)) $childrec .= "2 SURN $csurn\n";
			if (isset($_REQUEST["HC{$i}GIVN"])) $hsgivn = $_REQUEST["HC{$i}GIVN"];
			if (isset($_REQUEST["HC{$i}SURN"])) $hssurn = $_REQUEST["HC{$i}SURN"];
			if (!empty($hsgivn) || !empty($hssurn)) {
				$childrec .= "2 _HEB ".$hsgivn." /".$hssurn."/\n";
			}
			if (isset($_REQUEST["RC{$i}GIVN"])) $rsgivn = $_REQUEST["RC{$i}GIVN"];
			if (isset($_REQUEST["RC{$i}SURN"])) $rssurn = $_REQUEST["RC{$i}SURN"];
			if (!empty($rsgivn) || !empty($rssurn)) {
				$childrec .= "2 ROMN ".$rsgivn." /".$rssurn."/\n";
			}
			$var = "C".$i."SEX";
			$csex = "";
			if (!empty($_REQUEST[$var])) $csex = $_REQUEST[$var];
			if (!empty($csex)) $childrec .= "1 SEX $csex\n";
			//--child birth
			$var = "C".$i."DATE";
			$cdate = "";
			if (!empty($_REQUEST[$var])) $cdate = $_REQUEST[$var];
			$var = "C".$i."PLAC";
			$cplac = "";
			if (!empty($_REQUEST[$var])) $cplac = $_REQUEST[$var];
			if (!empty($cdate)||!empty($cplac)) {
				$childrec .= "1 BIRT\n";
				$cdate = check_input_date($cdate);
				if (!empty($cdate)) $childrec .= "2 DATE $cdate\n";
				if (!empty($cplac)) $childrec .= "2 PLAC $cplac\n";
				$var = "C".$i."RESN";
				$cresn = "";
				if (!empty($_REQUEST[$var])) $cresn = $_REQUEST[$var];
				if (!empty($cresn)) $childrec .= "2 RESN $cresn\n";
			}
			//--child death
			$var = "C".$i."DDATE";
			$cdate = "";
			if (!empty($_REQUEST[$var])) $cdate = $_REQUEST[$var];
			$var = "C".$i."DPLAC";
			$cplac = "";
			if (!empty($_REQUEST[$var])) $cplac = $_REQUEST[$var];
			if (!empty($cdate)||!empty($cplac)) {
				$childrec .= "1 DEAT\n";
				$cdate = check_input_date($cdate);
				if (!empty($cdate)) $childrec .= "2 DATE $cdate\n";
				if (!empty($cplac)) $childrec .= "2 PLAC $cplac\n";
				$var = "C".$i."DRESN";
				$cresn = "";
				if (!empty($_REQUEST[$var])) $cresn = $_REQUEST[$var];
				if (!empty($cresn)) $childrec .= "2 RESN $cresn\n";
			}
			$childrec .= "1 FAMC @$famid@\n";
			$cxref = append_gedrec($childrec);
			$famrec .= "\n1 CHIL @$cxref@";
			$famupdate = true;
		}

		if ($famupdate && ($famrec!=$oldfamrec)) replace_gedrec($famid, $famrec, $update_CHAN);
	}

	//--add new spouse name, birth, marriage
	if (isset($_REQUEST['SGIVN'])) $SGIVN = $_REQUEST['SGIVN'];
	if (isset($_REQUEST['SSURN'])) $SSURN = $_REQUEST['SSURN'];
	if (isset($_REQUEST['MSSURN'])) $MSSURN = $_REQUEST['MSSURN'];
	if (isset($_REQUEST['SSEX'])) $SSEX = $_REQUEST['SSEX'];
	if (!empty($SGIVN) || !empty($SSURN)) {
		//-- first add the new spouse
		$spouserec = "0 @REF@ INDI\n";
		$spouserec .= "1 NAME $SGIVN /$SSURN/\n";
		if (!empty($SGIVN)) $spouserec .= "2 GIVN $SGIVN\n";
		if (!empty($SSURN)) $spouserec .= "2 SURN $SSURN\n";
		if (!empty($MSSURN)) $spouserec .= "2 _MARNM /$MSSURN/\n";
		if (!empty($SSEX)) $spouserec .= "1 SEX $SSEX\n";
		if (isset($_REQUEST['BDATE'])) $BDATE = $_REQUEST['BDATE'];
		if (isset($_REQUEST['BPLAC'])) $BPLAC = $_REQUEST['BPLAC'];
		if (isset($_REQUEST['BRESN'])) $BRESN = $_REQUEST['BRESN'];
		if (!empty($BDATE)||!empty($BPLAC)) {
			$spouserec .= "1 BIRT\n";
			if (!empty($BDATE)) $spouserec .= "2 DATE $BDATE\n";
			if (!empty($BPLAC)) $spouserec .= "2 PLAC $BPLAC\n";
			if (!empty($BRESN)) $spouserec .= "2 RESN $BRESN\n";
		}
		if (isset($_REQUEST['DDATE'])) $DDATE = $_REQUEST['DDATE'];
		if (isset($_REQUEST['DPLAC'])) $DPLAC = $_REQUEST['DPLAC'];
		if (isset($_REQUEST['DRESN'])) $DRESN = $_REQUEST['DRESN'];
		if (!empty($DDATE)||!empty($DPLAC)) {
			$spouserec .= "1 DEAT\n";
			if (!empty($DDATE)) $spouserec .= "2 DATE $DDATE\n";
			if (!empty($DPLAC)) $spouserec .= "2 PLAC $DPLAC\n";
			if (!empty($DRESN)) $spouserec .= "2 RESN $DRESN\n";
		}
		$xref = append_gedrec($spouserec);

		//-- next add the new family record
		$famrec = "0 @REF@ FAM\n";
		if ($SSEX=="M") $famrec .= "1 HUSB @$xref@\n1 WIFE @$pid@\n";
		else $famrec .= "1 HUSB @$pid@\n1 WIFE @$xref@\n";
		$newfamid = append_gedrec($famrec);

		//-- add the new family id to the new spouse record
		$spouserec = find_updated_record($xref);
		if (empty($spouserec)) $spouserec = find_person_record($xref);
		$spouserec .= "\n1 FAMS @$newfamid@\n";
		replace_gedrec($xref, $spouserec, $update_CHAN);

		//-- last add the new family id to the persons record
		$gedrec .= "\n1 FAMS @$newfamid@\n";
		$updated = true;
	}
	if (isset($_REQUEST['MARRY'])) $MARRY = $_REQUEST['MARRY'];
	if (isset($_REQUEST['MDATE'])) $MDATE = $_REQUEST['MDATE'];
	if (isset($_REQUEST['MPLAC'])) $MPLAC = $_REQUEST['MPLAC'];
	if (isset($_REQUEST['MRESN'])) $MRESN = $_REQUEST['MRESN'];
	if (!empty($MDATE)||!empty($MPLAC)||!empty($MARRY)) {
		if (empty($newfamid)) {
			$famrec = "0 @REF@ FAM\n";
			if (preg_match("/1 SEX M/", $gedrec)>0) $famrec .= "1 HUSB @$pid@\n";
			else $famrec .= "1 WIFE @$pid@";
			$newfamid = append_gedrec($famrec);
			$gedrec .= "\n1 FAMS @$newfamid@";
			$updated = true;
		}
		if (!empty($MDATE)||!empty($MPLAC)) {
			$factrec = "1 MARR\n";
		}
		else if (!empty($MARRY)) {
			$factrec = "1 MARR Y\n";
		}
		$MDATE = check_input_date($MDATE);
		if (!empty($MDATE)) $factrec .= "2 DATE $MDATE\n";
		if (!empty($MPLAC)) $factrec .= "2 PLAC $MPLAC\n";
		if (!empty($MRESN)) $factrec .= "2 RESN $MRESN\n";
		$famrec .= "\n".$factrec;
	}

	//--add new child, name, birth
	if (isset($_REQUEST['CGIVN'])) $CGIVN = $_REQUEST['CGIVN'];
	if (isset($_REQUEST['CSURN'])) $CSURN = $_REQUEST['CSURN'];
	if (isset($_REQUEST['CSEX'])) $CSEX = $_REQUEST['CSEX'];
	if (isset($_REQUEST['HCGIVN'])) $HCGIVN = $_REQUEST['HCGIVN'];
	if (isset($_REQUEST['HCSURN'])) $HCSURN = $_REQUEST['HCSURN'];
	if (!empty($CGIVN) || !empty($CSURN)) {
		//-- first add the new child
		$childrec = "0 @REF@ INDI\n";
		$childrec .= "1 NAME $CGIVN /$CSURN/\n";
		if (!empty($CGIVN)) $childrec .= "2 GIVN $CGIVN\n";
		if (!empty($CSURN)) $childrec .= "2 SURN $CSURN\n";
		if (!empty($HCGIVN) || !empty($HCSURN)) {
			$childrec .= "2 _HEB $HCGIVN /$HCSURN/\n";
		}
		if (!empty($CSEX)) $childrec .= "1 SEX $CSEX\n";
		if (isset($_REQUEST['CDATE'])) $CDATE = $_REQUEST['CDATE'];
		if (isset($_REQUEST['CPLAC'])) $CPLAC = $_REQUEST['CPLAC'];
		if (isset($_REQUEST['CRESN'])) $CRESN = $_REQUEST['CRESN'];
		if (!empty($CDATE)||!empty($CPLAC)) {
			$childrec .= "1 BIRT\n";
			$CDATE = check_input_date($CDATE);
			if (!empty($CDATE)) $childrec .= "2 DATE $CDATE\n";
			if (!empty($CPLAC)) $childrec .= "2 PLAC $CPLAC\n";
			if (!empty($CRESN)) $childrec .= "2 RESN $CRESN\n";
		}
		if (isset($_REQUEST['CDDATE'])) $CDDATE = $_REQUEST['CDDATE'];
		if (isset($_REQUEST['CDPLAC'])) $CDPLAC = $_REQUEST['CDPLAC'];
		if (isset($_REQUEST['CDRESN'])) $CDRESN = $_REQUEST['CDRESN'];
		if (!empty($CDDATE)||!empty($CDPLAC)) {
			$childrec .= "1 DEAT\n";
			$CDDATE = check_input_date($CDDATE);
			if (!empty($CDDATE)) $childrec .= "2 DATE $CDDATE\n";
			if (!empty($CDPLAC)) $childrec .= "2 PLAC $CDPLAC\n";
			if (!empty($CDRESN)) $childrec .= "2 RESN $CDRESN\n";
		}
		$cxref = append_gedrec($childrec);

		//-- if a new family was already made by adding a spouse or a marriage
		//-- then use that id, otherwise create a new family
		if (empty($newfamid)) {
			$famrec = "0 @REF@ FAM\n";
			if (preg_match("/1 SEX M/", $gedrec)>0) $famrec .= "1 HUSB @$pid@\n";
			else $famrec .= "1 WIFE @$pid@\n";
			$famrec .= "1 CHIL @$cxref@\n";
			$newfamid = append_gedrec($famrec);

			//-- add the new family to the new child
			$childrec = find_updated_record($cxref);
			if (empty($childrec)) $childrec = find_person_record($cxref);
			$childrec .= "\n1 FAMC @$newfamid@\n";
			replace_gedrec($cxref, $childrec, $update_CHAN);

			//-- add the new family to the original person
			$gedrec .= "\n1 FAMS @$newfamid@";
			$updated = true;
		}
		else {
			$famrec .= "\n1 CHIL @$cxref@\n";

			//-- add the family to the new child
			$childrec = find_updated_record($cxref);
			if (empty($childrec)) $childrec = find_person_record($cxref);
			$childrec .= "\n1 FAMC @$newfamid@\n";
			replace_gedrec($cxref, $childrec, $update_CHAN);
		}
		echo $pgv_lang["update_successful"]."<br />\n";;
	}
	if (!empty($newfamid)) {
		$famrec = preg_replace("/0 @(.*)@/", "0 @".$newfamid."@", $famrec);
		replace_gedrec($newfamid, $famrec, $update_CHAN);
	}

	//------------------------------------------- updates for family with parents
	$cfams = find_families_in_record($gedrec, "FAMC");
	if (count($cfams)==0) $cfams[] = "";
	$i++;
	for($j=1; $j<=count($cfams); $j++) {
		$famid = $cfams[$j-1];
		$famupdate = false;
		if (!empty($famid)) {
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
			else $famrec = find_updated_record($famid);
			$oldfamrec = $famrec;
		}
		else {
			$famrec = "0 @REF@ FAM\n1 CHIL @$pid@";
			$oldfamrec = "";
		}

		if (isset($_REQUEST['FATHER'])) $FATHER = $_REQUEST['FATHER'];
		if (empty($FATHER[$i])) {
			//-- update the parents
			$sgivn = "";
			$ssurn = "";
			$smsurn = "";
			//--add new spouse name, birth
			if (isset($_REQUEST["FGIVN$i"])) $sgivn = $_REQUEST["FGIVN$i"];
			if (isset($_REQUEST["FSURN$i"])) $ssurn = $_REQUEST["FSURN$i"];
			if (isset($_REQUEST["FMRSURN$i"])) $smsurn = $_REQUEST["FMRSURN$i"];
			if (!empty($sgivn) || !empty($ssurn)) {
				//-- first add the new spouse
				$spouserec = "0 @REF@ INDI\n";
				$spouserec .= "1 NAME ".$sgivn." /".$ssurn."/\n";
				if (!empty($sgivn)) $spouserec .= "2 GIVN ".$sgivn."\n";
				if (!empty($ssurn)) $spouserec .= "2 SURN ".$ssurn."\n";
				if (!empty($smsurn)) $spouserec .= "2 _MARNM /".$smsurn."/\n";
				$hsgivn = "";
				$hssurn = "";
				if (isset($_REQUEST["HFGIVN$i"])) $hsgivn = $_REQUEST["HFGIVN$i"];
				if (isset($_REQUEST["HFSURN$i"])) $hssurn = $_REQUEST["HFSURN$i"];
				if (!empty($hsgivn) || !empty($hssurn)) {
					$spouserec .= "2 _HEB ".$hsgivn." /".$hssurn."/\n";
				}
				$rsgivn = "";
				$rssurn = "";
				if (isset($_REQUEST["RFGIVN$i"])) $rsgivn = $_REQUEST["RFGIVN$i"];
				if (isset($_REQUEST["RFSURN$i"])) $rssurn = $_REQUEST["RFSURN$i"];
				if (!empty($rsgivn) || !empty($rssurn)) {
					$spouserec .= "2 ROMN ".$rsgivn." /".$rssurn."/\n";
				}
				$ssex = "";
				if (isset($_REQUEST["FSEX$i"])) $ssex = $_REQUEST["FSEX$i"];
				if (!empty($ssex)) $spouserec .= "1 SEX ".$ssex."\n";
				$bdate = "";
				$bplac = "";
				if (isset($_REQUEST["FBDATE$i"])) $bdate = $_REQUEST["FBDATE$i"];
				if (isset($_REQUEST["FBPLAC$i"])) $bplac = $_REQUEST["FBPLAC$i"];
				if (!empty($bdate)||!empty($bplac)) {
					$spouserec .= "1 BIRT\n";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\n";
					if (!empty($bplac)) $spouserec .= "2 PLAC ".$bplac."\n";
					$bresn = "";
					if (isset($_REQUEST["FBRESN$i"])) $bresn = $_REQUEST["FBRESN$i"];
					if (!empty($bresn)) $spouserec .= "2 RESN ".$bresn."\n";
				}
				$bdate = "";
				$bplac = "";
				if (isset($_REQUEST["FDDATE$i"])) $bdate = $_REQUEST["FDDATE$i"];
				if (isset($_REQUEST["FDPLAC$i"])) $bplac = $_REQUEST["FDPLAC$i"];
				if (!empty($bdate)||!empty($bplac)) {
					$spouserec .= "1 DEAT\n";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\n";
					if (!empty($bplac)) $spouserec .= "2 PLAC ".$bplac."\n";
					$bresn = "";
					if (isset($_REQUEST["FDRESN$i"])) $bresn = $_REQUEST["FDRESN$i"];
					if (!empty($bresn)) $spouserec .= "2 RESN ".$bresn."\n";
				}
				if (empty($famid)) {
					//echo "HERE 1";
					$famid = append_gedrec($famrec);
					//echo "<pre>$famrec</pre>";
					$gedrec .= "\n1 FAMC @$famid@\n";
					$updated = true;
				}
				$spouserec .= "\n1 FAMS @$famid@\n";
				$FATHER[$i] = append_gedrec($spouserec);
			}
		}
		else {
			if (empty($famid)) {
				//echo "HERE 2";
				$famid = append_gedrec($famrec);
				$gedrec .= "\n1 FAMC @$famid@\n";
				$updated = true;
			}
			if (empty($oldfamrec)) {
				$spouserec = find_updated_record($FATHER[$i]);
				if (empty($spouserec)) $spouserec = find_person_record($FATHER[$i]);
				$spouserec .= "\n1 FAMS @$famid@";
				replace_gedrec($FATHER[$i], $spouserec, $update_CHAN);
			}
		}

		$parents = find_parents_in_record($famrec);
		if (!empty($FATHER[$i]) && $parents['HUSB']!=$FATHER[$i]) {
			if (strstr($famrec, "1 HUSB")!==false) $famrec = preg_replace("/1 HUSB @.*@/", "1 HUSB @$FATHER[$i]@", $famrec);
			else $famrec .= "\n1 HUSB @$FATHER[$i]@";
			$famupdate = true;
		}

		if (isset($_REQUEST['MOTHER'])) $MOTHER = $_REQUEST['MOTHER'];
		if (empty($MOTHER[$i])) {
			//-- update the parents
			$sgivn = "";
			$ssurn = "";
			$smsurn = "";
			if (isset($_REQUEST["MGIVN$i"])) $sgivn = $_REQUEST["MGIVN$i"];
			if (isset($_REQUEST["MSURN$i"])) $ssurn = $_REQUEST["MSURN$i"];
			if (isset($_REQUEST["MMRSURN$i"])) $smsurn = $_REQUEST["MMRSURN$i"];
			//--add new spouse name, birth
			if (!empty($sgivn) || !empty($ssurn)) {
				//-- first add the new spouse
				$spouserec = "0 @REF@ INDI\n";
				$spouserec .= "1 NAME ".$sgivn." /".$ssurn."/\n";
				if (!empty($sgivn)) $spouserec .= "2 GIVN ".$sgivn."\n";
				if (!empty($ssurn)) $spouserec .= "2 SURN ".$ssurn."\n";
				if (!empty($smsurn)) $spouserec .= "2 _MARNM /".$smsurn."/\n";
				$hsgivn = "";
				$hssurn = "";
				if (isset($_REQUEST["HMGIVN$i"])) $hsgivn = $_REQUEST["HMGIVN$i"];
				if (isset($_REQUEST["HMSURN$i"])) $hssurn = $_REQUEST["HMSURN$i"];
				if (!empty($hsgivn) || !empty($hssurn)) {
					$spouserec .= "2 _HEB ".$hsgivn." /".$hssurn."/\n";
				}
				$rsgivn = "";
				$rssurn = "";
				if (isset($_REQUEST["RMGIVN$i"])) $rsgivn = $_REQUEST["RMGIVN$i"];
				if (isset($_REQUEST["RMSURN$i"])) $rssurn = $_REQUEST["RMSURN$i"];
				if (!empty($rsgivn) || !empty($rssurn)) {
					$spouserec .= "2 ROMN ".$rsgivn." /".$rssurn."/\n";
				}
				$ssex = "";
				if (isset($_REQUEST["MSEX$i"])) $ssex = $_REQUEST["MSEX$i"];
				if (!empty($ssex)) $spouserec .= "1 SEX ".$ssex."\n";
				$bdate = "";
				$bplac = "";
				if (isset($_REQUEST["MBDATE$i"])) $bdate = $_REQUEST["MBDATE$i"];
				if (isset($_REQUEST["MBPLAC$i"])) $bplac = $_REQUEST["MBPLAC$i"];
				if (!empty($bdate)||!empty($bplac)) {
					$spouserec .= "1 BIRT\n";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\n";
					if (!empty($bplac)) $spouserec .= "2 PLAC ".$bplac."\n";
					$bresn = "";
					if (isset($_REQUEST["MBRESN$i"])) $bplac = $_REQUEST["MBRESN$i"];
					if (!empty($bresn)) $spouserec .= "2 RESN ".$bresn."\n";
				}
				$bdate = "";
				$bplac = "";
				if (isset($_REQUEST["MDDATE$i"])) $bdate = $_REQUEST["MDDATE$i"];
				if (isset($_REQUEST["MDPLAC$i"])) $bplac = $_REQUEST["MDPLAC$i"];
				if (!empty($bdate)||!empty($bplac)) {
					$spouserec .= "1 DEAT\n";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\n";
					if (!empty($bplac)) $spouserec .= "2 PLAC ".$bplac."\n";
					$bresn = "";
					if (isset($_REQUEST["MDRESN$i"])) $bplac = $_REQUEST["MDRESN$i"];
					if (!empty($bresn)) $spouserec .= "2 RESN ".$bresn."\n";
				}
				if (empty($famid)) {
					//echo "HERE 3";
					$famid = append_gedrec($famrec);
					$gedrec .= "\n1 FAMC @$famid@\n";
					$updated = true;
				}
				$spouserec .= "\n1 FAMS @$famid@\n";
				$MOTHER[$i] = append_gedrec($spouserec);
			}
		}
		else {
			if (empty($famid)) {
				$famid = append_gedrec($famrec);
				$gedrec .= "\n1 FAMC @$famid@\n";
				$updated = true;
			}
			if (empty($oldfamrec)) {
				$spouserec = find_updated_record($MOTHER[$i]);
				if (empty($spouserec)) $spouserec = find_person_record($MOTHER[$i]);
				$spouserec .= "\n1 FAMS @$famid@";
				replace_gedrec($MOTHER[$i], $spouserec, $update_CHAN);
			}
		}
		if (!empty($MOTHER[$i]) && $parents['WIFE']!=$MOTHER[$i]) {
			if (strstr($famrec, "1 WIFE")!==false) $famrec = preg_replace("/1 WIFE @.*@/", "1 WIFE @$MOTHER[$i]@", $famrec);
			else $famrec .= "\n1 WIFE @$MOTHER[$i]@";
			$famupdate = true;
		}

		//-- check for updated facts
		$var = "F".$i."TAGS";
		if (isset($_REQUEST[$var])) $TAGS = $_REQUEST[$var];
		else $TAGS = array();
		if (count($TAGS)>0) {
			$famupdate |= check_updated_facts($i, $famrec, $TAGS, "F");
		}

		//-- check for new fact
		$var = "F".$i."newfact";
		$newfact = "";
		if (isset($_REQUEST[$var])) $newfact = $_REQUEST[$var];
		if (!empty($newfact)) {
			if (empty($famid)) {
				//echo "HERE 6";
				$famid = append_gedrec($famrec);
				$gedrec .= "\n1 FAMC @$famid@\n";
				$updated = true;
			}
			if (!in_array($newfact, $typefacts)) $factrec = "1 $newfact\n";
			else $factrec = "1 EVEN\n2 TYPE $newfact\n";
			$var = "F".$i."DATE";
			if (isset($_REQUEST[$var])) $FDATE = $_REQUEST[$var];
			else $FDATE = "";
			$FDATE = check_input_date($FDATE);
			if (!empty($FDATE)) $factrec .= "2 DATE $FDATE\n";
			$var = "F".$i."PLAC";
			if (isset($_REQUEST[$var])) $FPLAC = $_REQUEST[$var];
			else $FPLAC = "";
			if (!empty($FPLAC)) $factrec .= "2 PLAC $FPLAC\n";
			$var = "F".$i."TEMP";
			if (isset($_REQUEST[$var])) $FTEMP = $_REQUEST[$var];
			else $FTEMP = "";
			if (!empty($FTEMP)) $factrec .= "2 TEMP $FTEMP\n";
			$var = "F".$i."RESN";
			if (isset($_REQUEST[$var])) $FRESN = $_REQUEST[$var];
			else $FRESN;
			if (!empty($FRESN)) $factrec .= "2 RESN $FRESN\n";
			//-- make sure that there is at least a Y
			if (preg_match("/\n2 \w*/", $factrec)==0) $factrec = "1 $newfact Y\n";
			$famrec .= "\n".$factrec;
			$famupdate = true;
		}

		if (isset($_REQUEST['CHIL'])) $CHIL = $_REQUEST['CHIL'];
		if (!empty($CHIL[$i])) {
			if (empty($famid)) {
				//echo "HERE 7";
				$famid = append_gedrec($famrec);
				$gedrec .= "\n1 FAMC @$famid@\n";
				$updated = true;
			}
			$famrec .= "\n1 CHIL @".$CHIL[$i]."@";
			if (!isset($pgv_changes[$CHIL[$i]."_".$GEDCOM])) $childrec = find_person_record($CHIL[$i]);
			else $childrec = find_updated_record($CHIL[$i]);
			if (preg_match("/1 FAMC @$famid@/", $childrec)==0) {
				$childrec = "\n1 FAMC @$famid@";
				replace_gedrec($CHIL[$i], $childrec, $update_CHAN);
			}
			$famupdate = true;
		}

		$var = "F".$i."CDEL";
		if (isset($_REQUEST[$var])) $fcdel = $_REQUEST[$var];
		else $fcdel = "";
		if (!empty($fcdel)) {
			$famrec = preg_replace("/1 CHIL @$fcdel@/", "", $famrec);
			$famupdate = true;
		}

		//--add new child, name, birth
		$cgivn = "";
		$csurn = "";
		if (isset($_REQUEST["C".$i."GIVN"])) $cgivn = $_REQUEST["C".$i."GIVN"];
		if (isset($_REQUEST["C".$i."SURN"])) $csurn = $_REQUEST["C".$i."SURN"];
		if (!empty($cgivn) || !empty($csurn)) {
			if (empty($famid)) {
				//echo "HERE 8";
				$famid = append_gedrec($famrec);
				$gedrec .= "\n1 FAMC @$famid@\n";
				$updated = true;
			}
			//-- first add the new child
			$childrec = "0 @REF@ INDI\n";
			$childrec .= "1 NAME ".$cgivn." /".$csurn."/\n";
			if (!empty($cgivn)) $childrec .= "2 GIVN ".$cgivn."\n";
			if (!empty($csurn)) $childrec .= "2 SURN ".$csurn."\n";
			$hcgivn = "";
			$hcsurn = "";
			if (isset($_REQUEST["HC".$i."GIVN"])) $hcgivn = $_REQUEST["HC".$i."GIVN"];
			if (isset($_REQUEST["HC".$i."SURN"])) $hcsurn = $_REQUEST["HC".$i."SURN"];
			if (!empty($hcgivn) || !empty($hcsurn)) {
				$childrec .= "2 _HEB ".$hcgivn." /".$hcsurn."/\n";
			}
			$rsgivn = "";
			$rssurn = "";
			if (isset($_REQUEST["RC".$i."GIVN"])) $rsgivn = $_REQUEST["RC".$i."GIVN"];
			if (isset($_REQUEST["RC".$i."SURN"])) $rssurn = $_REQUEST["RC".$i."SURN"];
			if (!empty($rsgivn) || !empty($rssurn)) {
				$childrec .= "2 ROMN ".$rsgivn." /".$rssurn."/\n";
			}
			if (isset($_REQUEST["C".$i."SEX"])) $csex = $_REQUEST["C".$i."SEX"];
			else $csex = "";
			if (!empty($csex)) $childrec .= "1 SEX $csex\n";
			//-- child birth
			if (isset($_REQUEST["C".$i."DATE"])) $cdate = $_REQUEST["C".$i."DATE"];
			else $cdate = "";
			$var = "C".$i."PLAC";
			if (isset($_REQUEST[$var])) $cplac = $_REQUEST[$var];
			else $cplac = "";
			if (!empty($cdate)||!empty($cplac)) {
				$childrec .= "1 BIRT\n";
				$cdate = check_input_date($cdate);
				if (!empty($cdate)) $childrec .= "2 DATE $cdate\n";
				if (!empty($cplac)) $childrec .= "2 PLAC $cplac\n";
				$var = "C".$i."RESN";
				if (isset($_REQUEST[$var])) $cresn = $_REQUEST[$var];
				else $cresn = "";
				if (!empty($cresn)) $childrec .= "2 RESN $cresn\n";
			}
			//-- child death
			$var = "C".$i."DDATE";
			if (isset($_REQUEST[$var])) $cdate = $_REQUEST[$var];
			else $cdate = "";
			$var = "C".$i."DPLAC";
			if (isset($_REQUEST[$var])) $cplac = $_REQUEST[$var];
			else $cplac = "";
			if (!empty($cdate)||!empty($cplac)) {
				$childrec .= "1 DEAT\n";
				$cdate = check_input_date($cdate);
				if (!empty($cdate)) $childrec .= "2 DATE $cdate\n";
				if (!empty($cplac)) $childrec .= "2 PLAC $cplac\n";
				$var = "C".$i."DRESN";
				if (isset($_REQUEST[$var])) $cresn = $_REQUEST[$var];
				else $cresn = "";
				if (!empty($cresn)) $childrec .= "2 RESN $cresn\n";
			}
			$childrec .= "1 FAMC @$famid@\n";
			$cxref = append_gedrec($childrec);
			$famrec .= "\n1 CHIL @$cxref@";
			$famupdate = true;
		}
		if ($famupdate &&($oldfamrec!=$famrec)) {
			$famrec = preg_replace("/0 @(.*)@/", "0 @".$famid."@", $famrec);
			replace_gedrec($famid, $famrec, $update_CHAN);
		}
		$i++;
	}

	if ($updated && empty($error)) {
		echo $pgv_lang["update_successful"]."<br />";
		AddToChangeLog("Quick update for $pid by >".PGV_USER_NAME."<");
		//echo "<pre>$gedrec</pre>";
		if ($oldgedrec!=$gedrec) replace_gedrec($pid, $gedrec, $update_CHAN);
	}
	if (!empty($error)) {
		echo "<span class=\"error\">".$error."</span>";
	}

	if ($closewin) {
		// autoclose window when update successful
		if ($EDIT_AUTOCLOSE && !PGV_DEBUG) {
			echo "\n<script type=\"text/javascript\">\n<!--\nif (window.opener.showchanges) window.opener.showchanges(); window.close();\n//-->\n</script>";
		}
		echo "<center><br /><br /><br />";
		echo "<a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
		print_simple_footer();
		exit;
	}
}

if ($action!="update") echo "<h2>".$pgv_lang["quick_update_title"]."</h2>\n";
echo $pgv_lang["quick_update_instructions"]."<br /><br />";

init_calendar_popup();
?>
<script language="JavaScript" type="text/javascript">
<!--
var pastefield;
function paste_id(value) {
	pastefield.value = value;
}

var helpWin;
function helpPopup(which) {
	if ((!helpWin)||(helpWin.closed)) helpWin = window.open('help_text.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
	else helpWin.location = 'help_text.php?help='+which;
	return false;
}
//-->
</script>
<?php
if ($action=="choosepid") {
	?>
	<form method="post" action="edit_quickupdate.php?pid=<?php echo $pid;?>" name="quickupdate" enctype="multipart/form-data">
	<input type="hidden" name="action" value="" />
	<table>
	<tr>
		<td><?php echo $pgv_lang["enter_pid"]; ?></td>
		<td><input type="text" size="6" name="pid" id="pid" />
		<?php print_findindi_link("pid","");?>
		</td>
	</tr>
	</table>
	<input type="submit" value="<?php echo $pgv_lang["continue"]; ?>" />
	</form>
		<?php
} else {
	$SEX = get_gedcom_value("SEX", 1, $gedrec, '', false);
	$child_surname = "";
	$GIVN = "";
	$SURN = "";
	$MRSURN = "";
	$subrec = get_sub_record(1, "1 NAME", $gedrec);
	if (!empty($subrec)) {
		$ct = preg_match("/2 GIVN (.*)/", $subrec, $match);
		if ($ct>0) $GIVN = trim($match[1]);
		else {
			$ct = preg_match("/1 NAME (.*)/", $subrec, $match);
			if ($ct>0) {
				$GIVN = preg_replace("~/.*/~", "", trim($match[1]));
			}
		}
		$ct = preg_match("/2 SURN (.*)/", $subrec, $match);
		if ($ct>0) {
			$SURN = trim($match[1]);
			$ct = preg_match("/2 SPFX (.*)/", $subrec, $match);
			if ($ct>0) {
				$SURN = trim($match[1])." ".$SURN;
			}
		}
		else {
			$ct = preg_match("/1 NAME (.*)/", $subrec, $match);
			if ($ct>0) {
				$st = preg_match("~/(.*)/~", $match[1], $smatch);
				if ($st>0) $SURN = $smatch[1];
			}
		}
		$ct = preg_match("/2 _MARNM (.*)/", $subrec, $match);
		if ($ct>0) $MRSURN = trim(str_replace("/", "", $match[1]));
		//else {
		//	$ct = preg_match("/1 NAME (.*)/", $subrec, $match);
		//	if ($ct>0) {
		//		$st = preg_match("~/(.*)/~", $match[1], $smatch);
		//		if ($st>0) $MRSURN = $smatch[1];
		//	}
		//}
		$HGIVN = "";
		$HSURN = "";
		$RGIVN = "";
		$RSURN = "";
		$hname = get_gedcom_value("_HEB", 2, $subrec, '', false);
		if (!empty($hname)) {
			$ct = preg_match("~(.*)/(.*)/(.*)~", $hname, $matches);
			if ($ct>0) {
				$HSURN = $matches[2];
				$HGIVN = trim($matches[1]).trim($matches[3]);
			}
			else $HGIVN = $hname;
		}
		$rname = get_gedcom_value("ROMN", 2, $subrec, '', false);
		if (!empty($rname)) {
			$ct = preg_match("~(.*)/(.*)/(.*)~", $rname, $matches);
			if ($ct>0) {
				$RSURN = $matches[2];
				$RGIVN = trim($matches[1]).trim($matches[3]);
			}
			else $RGIVN = $rname;
		}
	}
	$ADDR = "";
	$subrec = get_sub_record(1, "1 ADDR", $gedrec);
	if (!empty($subrec)) {
		$ct = preg_match("/1 ADDR (.*)/", $subrec, $match);
		if ($ct>0) $ADDR = trim($match[1]);
		$ADDR_CONT = get_cont(2, $subrec);
		if (!empty($ADDR_CONT)) {
			$ADDR .= $ADDR_CONT;
			$ADDR = str_replace("<br />", "\n", $ADDR);
		}
		else {
			$_NAME = get_gedcom_value("_NAME", 2, $subrec);
			if (!empty($_NAME)) $ADDR .= "\n". $_NAME;
			$ADR1 = get_gedcom_value("ADR1", 2, $subrec);
			if (!empty($ADR1)) $ADDR .= "\n". $ADR1;
			$ADR2 = get_gedcom_value("ADR2", 2, $subrec);
			if (!empty($ADR2)) $ADDR .= "\n". $ADR2;
			$cityspace = "\n";
			if (!$POSTAL_CODE) {
				$POST = get_gedcom_value("POST", 2, $subrec);
				if (!empty($POST)) $ADDR .= "\n". $POST;
				else $ADDR .= "\n";
				$cityspace = " ";
			}
			$CITY = get_gedcom_value("CITY", 2, $subrec);
			if (!empty($CITY)) $ADDR .= $cityspace. $CITY;
			else $ADDR .= $cityspace;
			$STAE = get_gedcom_value("STAE", 2, $subrec);
			if (!empty($STAE)) $ADDR .= ", ". $STAE;
			if ($POSTAL_CODE) {
				$POST = get_gedcom_value("POST", 2, $subrec);
				if (!empty($POST)) $ADDR .= "  ". $POST;
			}
			$CTRY = get_gedcom_value("CTRY", 2, $subrec);
			if (!empty($CTRY)) $ADDR .= "\n". $CTRY;
		}
		/**
		* @todo add support for ADDR subtags ADR1, CITY, STAE etc
		*/
	}
	$PHON = "";
	$subrec = get_sub_record(1, "1 PHON", $gedrec);
	if (!empty($subrec)) {
		$ct = preg_match("/1 PHON (.*)/", $subrec, $match);
		if ($ct>0) $PHON = trim($match[1]);
		$PHON .= get_cont(2, $subrec);
	}
	$EMAIL = "";
	$ct = preg_match("/1 (_?EMAIL) (.*)/", $gedrec, $match);
	if ($ct>0) {
		$EMAIL = trim($match[2]);
		$subrec = get_sub_record(1, "1 ".$match[1], $gedrec);
		$EMAIL .= get_cont(2, $subrec);
	}
	$FAX = "";
	$subrec = get_sub_record(1, "1 FAX", $gedrec);
	if (!empty($subrec)) {
			$ct = preg_match("/1 FAX (.*)/", $subrec, $match);
			if ($ct>0) $FAX = trim($match[1]);
			$FAX .= get_cont(2, $subrec);
	}

	$indifacts = array();
	$person = Person::getInstance($pid);
	$facts = $person->getIndiFacts();
	$repeat_tags = array();

	foreach($facts as $event) {
		$fact = $event->getTag();
		if ($fact=="EVEN" || $fact=="FACT") $fact = $event->getType();
		if (in_array($fact, $addfacts)) {
			if (!isset($repeat_tags[$fact])) $repeat_tags[$fact]=1;
			else $repeat_tags[$fact]++;
			$newreqd = array();
			foreach($reqdfacts as $r=>$rfact) {
				if ($rfact!=$fact) $newreqd[] = $rfact;
			}
			$reqdfacts = $newreqd;
			$indifacts[] = $event;
		}
	}
	foreach($reqdfacts as $ind=>$fact) {
		$e = new Event("1 $fact\n");
		$e->temp = true;
		$indifacts[] = $e;
	}

	sort_facts($indifacts);
	$sfams = find_families_in_record($gedrec, "FAMS");
	$cfams = find_families_in_record($gedrec, "FAMC");
	if (count($cfams)==0) $cfams[] = "";

	$tabkey = 1;
	$person=Person::getInstance($pid);
	echo '<b>', PrintReady(stripLRMRLM($person->getFullName()));
	if ($SHOW_ID_NUMBERS) {
		echo PrintReady("&nbsp;&nbsp;(".$pid.")");
	}
	echo '</b><br />';
?>
<script language="JavaScript" type="text/javascript">
<!--
var tab_count = <?php echo (count($sfams)+count($cfams)); ?>;
function switch_tab(tab) {
	for(i=0; i<=tab_count+1; i++) {
		var pagetab = document.getElementById('pagetab'+i);
		var pagetabbottom = document.getElementById('pagetab'+i+'bottom');
		var tabdiv = document.getElementById('tab'+i);
		if (i==tab) {
			pagetab.className='tab_cell_active';
			tabdiv.style.display = 'block';
			pagetabbottom.className='tab_active_bottom';
		}
		else {
			pagetab.className='tab_cell_inactive';
			tabdiv.style.display = 'none';
			pagetabbottom.className='tab_inactive_bottom';
		}
	}
}

function checkform(frm) {
	if (frm.EMAIL) {
		if ((frm.EMAIL.value!="") &&
			((frm.EMAIL.value.indexOf("@")==-1) ||
			(frm.EMAIL.value.indexOf("<")!=-1) ||
			(frm.EMAIL.value.indexOf(">")!=-1))) {
			alert("<?php echo $pgv_lang["enter_email"]; ?>");
			frm.EMAIL.focus();
			return false;
		}
	}
	return true;
}
//-->
</script>
<form method="post" action="edit_quickupdate.php?pid=<?php echo $pid;?>" name="quickupdate" enctype="multipart/form-data" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="closewin" value="1" />
<br /><input type="submit" value="<?php echo $pgv_lang["save"]; ?>" /><br /><br />
<table class="tabs_table">
	<tr>
		<td id="pagetab0" class="tab_cell_active"><a href="javascript: <?php echo $pgv_lang["personal_facts"];?>" onclick="switch_tab(0); return false;"><?php echo $pgv_lang["personal_facts"]?></a></td>
		<?php
		for($i=1; $i<=count($sfams); $i++) {
			$famid = $sfams[$i-1];
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
			else $famrec = find_updated_record($famid);
			$parents = find_parents_in_record($famrec);
			$spid = "";
			if($parents) {
				if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
				else $spid=$parents["WIFE"];
			}
			echo "<td id=\"pagetab$i\" class=\"tab_cell_inactive\" onclick=\"switch_tab($i); return false;\"><a href=\"javascript: ".$pgv_lang["family_with"]."&nbsp;";
			$person=Person::getInstance($spid);
			if ($person) {
				echo PrintReady(stripLRMRLM(strip_tags($person->getFullName())));
				echo "\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["family_with"]." ";
				echo PrintReady(stripLRMRLM($person->getFullName()));
			} else {
				echo "\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["family_with"]." ".$pgv_lang["unknown"];
			}
			echo "</a></td>\n";
		}
		?>
		<td id="pagetab<?php echo $i; ?>" class="tab_cell_inactive" onclick="switch_tab(<?php echo $i; ?>); return false;"><a href="javascript: <?php echo $pgv_lang["add_new_wife"];?>" onclick="switch_tab(<?php echo $i; ?>); return false;">
		<?php if (preg_match("/1 SEX M/", $gedrec)>0) echo $pgv_lang["add_new_wife"]; else echo $pgv_lang["add_new_husb"]; ?></a></td>
		<?php
		$i++;
		for($j=1; $j<=count($cfams); $j++) {
			echo "<td id=\"pagetab$i\" class=\"tab_cell_inactive\" onclick=\"switch_tab($i); return false;\"><a href=\"javascript: ".$pgv_lang["as_child"]."\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["as_child"];
			echo "</a></td>\n";
			$i++;
		}
		?>
		</tr>
		<tr>
			<td id="pagetab0bottom" class="tab_active_bottom"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]; ?>" width="1" height="1" alt="" /></td>
			<?php
		for($i=1; $i<=count($sfams); $i++) {
			echo "<td id=\"pagetab{$i}bottom\" class=\"tab_inactive_bottom\"><img src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["spacer"]["other"]."\" width=\"1\" height=\"1\" alt=\"\" /></td>\n";
		}
		for($j=1; $j<=count($cfams); $j++) {
			echo "<td id=\"pagetab{$i}bottom\" class=\"tab_inactive_bottom\"><img src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["spacer"]["other"]."\" width=\"1\" height=\"1\" alt=\"\" /></td>\n";
			$i++;
		}
		?>
			<td id="pagetab<?php echo $i; ?>bottom" class="tab_inactive_bottom"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]; ?>" width="1" height="1" alt="" /></td>
			<td class="tab_inactive_bottom_right" style="width:10%;"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]; ?>" width="1" height="1" alt="" /></td>
	</tr>
</table>
<div id="tab0">
<table class="<?php echo $TEXT_DIRECTION; ?> width80">
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_name_help", "qm"); ?><?php echo $pgv_lang["update_name"]; ?></td></tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SURN" value="<?php echo PrintReady(htmlspecialchars($SURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="GIVN" value="<?php echo PrintReady(htmlspecialchars($GIVN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SURN" value="<?php echo PrintReady(htmlspecialchars($SURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_MARNM")!==false) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["_MARNM"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MRSURN" value="<?php echo PrintReady(htmlspecialchars($MRSURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) {?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSURN" value="<?php echo PrintReady(htmlspecialchars($HSURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HGIVN" value="<?php echo PrintReady(htmlspecialchars($HGIVN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSURN" value="<?php echo PrintReady(htmlspecialchars($HSURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSURN" value="<?php echo PrintReady(htmlspecialchars($RSURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RGIVN" value="<?php echo PrintReady(htmlspecialchars($RGIVN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSURN" value="<?php echo PrintReady(htmlspecialchars($RSURN,ENT_COMPAT,'UTF-8')); ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="GENDER" tabindex="<?php echo $tabkey; ?>">
			<option value="M"<?php if ($SEX=="M") echo " selected=\"selected\""; ?>><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"<?php if ($SEX=="F") echo " selected=\"selected\""; ?>><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"<?php if ($SEX=="U") echo " selected=\"selected\""; ?>><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php
// NOTE: Update fact
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); echo $pgv_lang["update_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"]; ?></td>
	<td class="descriptionbox"><?php echo $pgv_lang["delete"]; ?></td>
</tr>
<?php
foreach($indifacts as $f=>$fact) {
	$fact_tag = $fact->getTag();
	$fact_num = $fact->getLineNumber();
	$date = $fact->getValue("DATE");
	$plac = $fact->getPlace();
	$temp = $fact->getValue("TEMP");
	$desc = $fact->getDetail();
	?>
<tr>
	<td class="descriptionbox">
		<?php echo $fact->getLabel();
		?>
		<input type="hidden" name="TAGS[]" value="<?php echo $fact_tag; ?>" />
		<input type="hidden" name="NUMS[]" value="<?php echo $fact_num; ?>" />
	</td>
	<?php if (!in_array($fact_tag, $emptyfacts)) { ?>
	<td class="optionbox" colspan="2">
		<input type="text" name="DESCS[]" size="61" value="<?php echo PrintReady(htmlspecialchars($desc,ENT_COMPAT,'UTF-8')); ?>" />
		<input type="hidden" name="DATES[]" value="<?php echo htmlspecialchars($date,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="PLACS[]" value="<?php echo htmlspecialchars($plac,ENT_COMPAT,'UTF-8'); ?>" />
		<input type="hidden" name="TEMPS[]" value="<?php echo htmlspecialchars($temp,ENT_COMPAT,'UTF-8'); ?>" />
	</td>
	<?php } else {
		if (!in_array($fact_tag, $nondatefacts)) { ?>
			<td class="optionbox">
				<input type="hidden" name="DESCS[]" value="<?php echo htmlspecialchars($desc,ENT_COMPAT,'UTF-8'); ?>" />
				<input type="text" dir="ltr" tabindex="<?php echo $tabkey; $tabkey++;?>" size="15" name="DATES[]" id="DATE<?php echo $f; ?>" onblur="valid_date(this);" value="<?php echo PrintReady(htmlspecialchars($date,ENT_COMPAT,'UTF-8')); ?>" />&nbsp;<?php print_calendar_popup("DATE$f");?>
			</td>
		<?php }
		if (empty($temp) && (!in_array($fact_tag, $nonplacfacts))) { ?>
			<td class="optionbox">
				<input type="text" size="35" tabindex="<?php echo $tabkey; $tabkey++; ?>" name="PLACS[]" id="place<?php echo $f; ?>" value="<?php echo PrintReady(htmlspecialchars($plac,ENT_COMPAT,'UTF-8')); ?>" />
				<?php print_findplace_link("place$f"); ?>
				<input type="hidden" name="TEMPS[]" value="" />
			</td>
		<?php
		}
		else {
			echo "<td class=\"optionbox\"><select tabindex=\"".$tabkey."\" name=\"TEMPS[]\" >\n";
			echo "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
			foreach($TEMPLE_CODES as $code=>$temple) {
				echo "<option value=\"$code\"";
				if ($code==$temp) echo " selected=\"selected\"";
				echo ">$temple</option>\n";
			}
			echo "</select>\n";
			echo "<input type=\"hidden\" name=\"PLACS[]\" value=\"\" />\n";
			echo "</td>\n";
			$tabkey++;
		}
	}
	if (!$fact->temp) { ?>
		<td class="optionbox center">
			<input type="hidden" name="REMS[<?php echo $f; ?>]" id="REM<?php echo $f; ?>" value="0" />
			<a href="javascript: <?php echo $pgv_lang["delete"]; ?>" onclick="if (confirm('<?php echo $pgv_lang["check_delete"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.REM<?php echo $f; ?>.value='1'; document.quickupdate.submit(); } return false;">
				<img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php echo $pgv_lang["delete"]; ?>" />
			</a>
		</td>
	</tr>
	<?php }
	else {?>
		<td class="optionbox">&nbsp;</td>
	</tr>
	<?php }
	if ($SHOW_QUICK_RESN) {
		print_quick_resn("RESNS[]");
	}
}

// NOTE: Add fact
if (count($addfacts)>0) { ?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); echo $pgv_lang["add_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"]; ?></td>
	<td class="descriptionbox">&nbsp;</td>
	</tr>
<tr><td class="optionbox">
	<script language="JavaScript" type="text/javascript">
	<!--
	function checkDesc(newfactSelect) {
		if (newfactSelect.selectedIndex==0) return;
		var fact = newfactSelect.options[newfactSelect.selectedIndex].value;
		var emptyfacts = "<?php foreach($emptyfacts as $ind=>$efact) echo $efact.","; ?>";
		descFact = document.getElementById('descFact');
		if (!descFact) return;
		if (emptyfacts.indexOf(fact)!=-1) {
			descFact.style.display='none';
		}
		else {
			descFact.style.display='block';
		}
	}
	//-->
	</script>
	<select name="newfact" tabindex="<?php echo $tabkey; ?>" onchange="checkDesc(this);">
		<option value=""><?php echo $pgv_lang["select_fact"]; ?></option>
	<?php $tabkey++; ?>
	<?php
	foreach($addfacts as $indexval => $fact) {
		$found = false;
		foreach($indifacts as $ind=>$value) {
			if ($fact==$value->getTag()) {
				$found=true;
				break;
			}
		}
		if (!$found) echo "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
	}
	?>
		</select>
		<div id="descFact" style="display:none;"><br />
			<?php echo $pgv_lang["description"]." "; ?><input type="text" size="35" name="DESC" />
		</div>
	</td>
	<td class="optionbox"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="DATE" id="DATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("DATE");?></td>
	<?php $tabkey++; ?>
	<td class="optionbox"><input type="text" size="35" tabindex="<?php echo $tabkey; ?>" name="PLAC" id="place" />
	<?php print_findplace_link("place"); ?>
	</td>
	<td class="optionbox">&nbsp;</td></tr>
	<?php $tabkey++; ?>
	<?php print_quick_resn("RESN"); ?>
<?php }

// Address update
if ($person && !$person->isDead() || !empty($ADDR) || !empty($PHON) || !empty($FAX) || !empty($EMAIL)) { //-- don't show address for dead people
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_address_help", "qm"); echo $pgv_lang["update_address"]; ?></td></tr>
<tr>
	<td class="descriptionbox">
		<?php echo $factarray["ADDR"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<?php if (!empty($CITY)&&!empty($POST)) { ?>
			<?php  if (empty($ADDR)) { ?><input type="hidden" name="ADDR" value="<?php echo PrintReady(htmlspecialchars(strip_tags($ADDR),ENT_COMPAT,'UTF-8')); ?>" /><?php } ?>
			<table>
			<?php if (!empty($_NAME)) { ?><tr><td><?php echo $factarray["NAME"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($_NAME)) echo "dir=\"ltr\""; ?> name="_NAME" size="35" value="<?php echo PrintReady(htmlspecialchars(strip_tags($_NAME),ENT_COMPAT,'UTF-8')); ?>" /></td></tr><?php } ?>
			<?php  if (!empty($ADDR)) { ?><tr><td><?php echo $factarray["ADDR"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($ADR1)) echo "dir=\"ltr\""; ?> name="ADDR" size="35" value="<?php echo PrintReady(htmlspecialchars(strip_tags($ADDR),ENT_COMPAT,'UTF-8')); ?>" /></td></tr><?php } ?>
			<tr><td><?php echo $factarray["ADR1"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($ADR1)) echo "dir=\"ltr\""; ?> name="ADR1" size="35" value="<?php echo PrintReady(htmlspecialchars(strip_tags($ADR1),ENT_COMPAT,'UTF-8')); ?>" /></td></tr>
			<tr><td><?php echo $factarray["ADR2"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($ADR2)) echo "dir=\"ltr\""; ?> name="ADR2" size="35" value="<?php echo PrintReady(htmlspecialchars(strip_tags($ADR2),ENT_COMPAT,'UTF-8')); ?>" /></td></tr>
			<tr><td><?php echo $factarray["CITY"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($CITY)) echo "dir=\"ltr\""; ?> name="CITY" value="<?php echo PrintReady(htmlspecialchars(strip_tags($CITY),ENT_COMPAT,'UTF-8')); ?>" />
			<?php echo $factarray["STAE"]; ?> <input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($STAE)) echo "dir=\"ltr\""; ?> name="STAE" value="<?php echo PrintReady(htmlspecialchars(strip_tags($STAE),ENT_COMPAT,'UTF-8')); ?>" /></td></tr>
			<tr><td><?php echo $factarray["POST"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($POST)) echo "dir=\"ltr\""; ?> name="POST" value="<?php echo PrintReady(htmlspecialchars(strip_tags($POST),ENT_COMPAT,'UTF-8')); ?>" /></td></tr>
			<tr><td><?php echo $factarray["CTRY"]; ?></td><td><input type="text" <?php if ($TEXT_DIRECTION=="rtl" && !hasRTLText($CTRY)) echo "dir=\"ltr\""; ?> name="CTRY" value="<?php echo PrintReady(htmlspecialchars(strip_tags($CTRY),ENT_COMPAT,'UTF-8')); ?>" /></td></tr>
			</table>

		<?php } else { ?>
		<textarea name="ADDR" tabindex="<?php echo $tabkey; ?>" cols="40" rows="4"><?php echo PrintReady(htmlspecialchars(strip_tags($ADDR),ENT_COMPAT,'UTF-8')); ?></textarea>
		<?php } ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox">
		<?php echo $factarray["PHON"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<input type="text" dir="ltr" tabindex="<?php echo $tabkey; $tabkey++; ?>" name="PHON" size="20" value="<?php echo PrintReady(htmlspecialchars($PHON,ENT_COMPAT,'UTF-8')); ?>" />
	</td>
</tr>
<tr>
		<td class="descriptionbox">
				<?php echo $factarray["FAX"]; ?>
		</td>
		<td class="optionbox" colspan="3">
				<input type="text" dir="ltr" tabindex="<?php echo $tabkey; $tabkey++; ?>" name="FAX" size="20" value="<?php echo PrintReady(htmlspecialchars($FAX,ENT_COMPAT,'UTF-8')); ?>" />
	</td>
</tr>
<tr>
	<td class="descriptionbox">
		<?php echo $factarray["EMAIL"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" name="EMAIL" size="40" value="<?php echo PrintReady(htmlspecialchars($EMAIL,ENT_COMPAT,'UTF-8')); ?>" />
	</td>
	<?php $tabkey++; ?>
</tr>
<tr><td colspan="4"><br /></td></tr>
<?php } ?>
</table>
</div>

<?php
//------------------------------------------- FAMILY WITH SPOUSE TABS ------------------------
for($i=1; $i<=count($sfams); $i++) {
	?>
<div id="tab<?php echo $i; ?>" style="display: none;">
<table class="<?php echo $TEXT_DIRECTION; ?> width80">
<tr><td class="topbottombar" colspan="4">
<?php
	$famreqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FAMFACTS);
	$famid = $sfams[$i-1];
	$family=Family::getInstance($famid);
	$famrec = $family->getGedcomRecord();
	if (isset($pgv_changes[$famid."_".$GEDCOM])) {
		$famrec = find_updated_record($famid);
		$family = new Family($famrec);
	}
	echo $pgv_lang["family_with"]." ";
	$parents = find_parents_in_record($famrec);
	$spid = "";
	if($parents) {
		if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
		else $spid=$parents["WIFE"];
	}
	$person=Person::getInstance($spid);
	if ($person) {
		echo "<a href=\"#\" onclick=\"return quickEdit('".$person->getXref()."','','{$GEDCOM}');\">";
		$name = PrintReady(stripLRMRLM($person->getFullName()));
		if ($SHOW_ID_NUMBERS) $name .= PrintReady(" (".$person->getXref().")");
		$name .= " [".$pgv_lang["edit"]."]";
		echo $name."</a>\n";
	}
	else echo $pgv_lang["unknown"];
	$subrecords = $family->getFacts(array("HUSB","WIFE","CHIL"));
	$famfacts = array();
	foreach($subrecords as $ind=>$eventObj) {
		$fact = $eventObj->getTag();
		$event = $eventObj->getDetail();
		if ($fact=="EVEN" || $fact=="FACT") $fact = $eventObj->getValue("TYPE");
		if (in_array($fact, $famaddfacts)) {
			$newreqd = array();
			foreach($famreqdfacts as $rfact) {
				if ($rfact!=$fact) $newreqd[] = $rfact;
			}
			$famreqdfacts = $newreqd;
			$famfacts[] = $eventObj;
		}
	}

	foreach($famreqdfacts as $fact) {
		$e = new Event("1 $fact\n");
		$e->temp = true;
		$famfacts[] = $e;
	}
	sort_facts($famfacts);
?>
</td></tr>
<tr>
	<td class="descriptionbox"><?php echo $pgv_lang["enter_pid"]; ?></td>
	<td class="optionbox" colspan="3"><input type="text" size="10" name="SPID[<?php echo $i; ?>]" id="SPID<?php echo $i; ?>" value="<?php echo $spid; ?>" />
		<?php print_findindi_link("SPID$i","");?>
		</td>
	</tr>
<?php if (empty($spid)) { ?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); if (preg_match("/1 SEX M/", $gedrec)>0) echo $pgv_lang["add_new_wife"]; else echo $pgv_lang["add_new_husb"];?></td></tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_MARNM")!==false) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["_MARNM"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MSSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="SSEX<?php echo $i; ?>" tabindex="<?php echo $tabkey; ?>">
			<option value="M"<?php if (preg_match("/1 SEX F/", $gedrec)>0) echo " selected=\"selected\""; ?>><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"<?php if (preg_match("/1 SEX M/", $gedrec)>0) echo " selected=\"selected\""; ?>><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"<?php if (preg_match("/1 SEX U/", $gedrec)>0) echo " selected=\"selected\""; ?>><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?></td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="BDATE<?php echo $i; ?>" id="BDATE<?php echo $i; ?>" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("BDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="35" type="text" tabindex="<?php echo $tabkey; ?>" name="BPLAC<?php echo $i; ?>" id="bplace<?php echo $i; ?>" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("bplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?></td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="DDATE<?php echo $i; ?>" id="DDATE<?php echo $i; ?>" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("DDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="35" type="text" tabindex="<?php echo $tabkey; ?>" name="DPLAC<?php echo $i; ?>" id="dplace<?php echo $i; ?>" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("dplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("BRESN".$i);
}
//NOTE: Update fact
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); echo $pgv_lang["update_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"]; ?></td>
	<td class="descriptionbox"><?php echo $pgv_lang["delete"]; ?></td>
	</tr>
<?php
foreach($famfacts as $f=>$eventObj) {
		$fact_tag = $eventObj->getTag();
		$date = $eventObj->getValue("DATE");
		$plac = $eventObj->getValue("PLAC");
		$temp = $eventObj->getValue("TEMP");
		$check = false;
	?>
			<tr>
				<td class="descriptionbox">
				<?php if (isset($factarray[$fact_tag])) echo $factarray[$fact_tag];
					else if (isset($pgv_lang[$fact_tag])) echo $pgv_lang[$fact_tag];
					else echo $fact_tag;
				?>
					<input type="hidden" name="F<?php echo $i; ?>TAGS[]" value="<?php echo $fact_tag; ?>" />
				<?php
					if ($fact_tag=='MARR') {
						$factdetail = explode(' ', trim($eventObj->getGedComRecord("MARR")));
						if (isset($factdetail[2]))
							if (count($factdetail) > 3 || strtoupper($factdetail[2]) == "Y")
								$check = true;
						?>
						&nbsp;&nbsp;
						<input type="checkbox" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="5" name="F<?php echo $i; ?>MARRY" id="F<?php echo $i; ?>MARRY"
						<?php
						if ($check) {
							echo 'checked="checked">';
						}
						else echo '>';
						?>
						<label for="F<?php echo $i; ?>MARRY"><?php echo $pgv_lang["yes"]; ?></label>
						<?php
					}
					else if ($fact_tag=='DIV') {
						$factdetail = explode(' ', trim($eventObj->getGedComRecord("DIV")));
						if (isset($factdetail[2]))
							if (count($factdetail) > 3 || strtoupper($factdetail[2]) == "Y")
								$check = true;
						?>
						&nbsp;&nbsp;
						<input type="checkbox" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="5" name="F<?php echo $i; ?>DIV" id="F<?php echo $i; ?>DIV"
						<?php
						if ($check) {
							echo 'checked="checked">';
						}
						else echo '>';
						?>
						<label for="F<?php echo $i; ?>DIV"><?php echo $pgv_lang["yes"]; ?></label>
						<?php
					}
				?>
				</td>
				<td class="optionbox"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; $tabkey++;?>" size="15" name="F<?php echo $i; ?>DATES[]" id="F<?php echo $i; ?>DATE<?php echo $f; ?>" onblur="valid_date(this);" value="<?php echo PrintReady(htmlspecialchars($date,ENT_COMPAT,'UTF-8')); ?>" />&nbsp;<?php print_calendar_popup("F{$i}DATE{$f}");?></td>
				<?php if (empty($temp) && (!in_array($fact_tag, $nonplacfacts))) { ?>
					<td class="optionbox"><input type="text" size="35" tabindex="<?php echo $tabkey; $tabkey++; ?>" name="F<?php echo $i; ?>PLACS[]" id="F<?php echo $i; ?>place<?php echo $f; ?>" value="<?php echo PrintReady(htmlspecialchars($plac,ENT_COMPAT,'UTF-8')); ?>" />
					<?php print_findplace_link("F{$i}place{$f}"); ?>
					</td>
				<?php }
				else {
					echo "<td class=\"optionbox\"><select tabindex=\"".$tabkey."\" name=\"F".$i."TEMP[]\" >\n";
					echo "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
					foreach($TEMPLE_CODES as $code=>$temple) {
						echo "<option value=\"$code\"";
						if ($code==$temp) echo " selected=\"selected\"";
						echo ">$temple</option>\n";
					}
					echo "</select>\n</td>\n";
					$tabkey++;
				}
				?>
				<td class="optionbox center">
					<input type="hidden" name="F<?php echo $i; ?>REMS[<?php echo $f; ?>]" id="F<?php echo $i; ?>REM<?php echo $f; ?>" value="0" />
					<?php if ($date!='' || $plac!='' || $temp!='' || $check==true) { ?>
					<a href="javascript: <?php echo $pgv_lang["delete"]; ?>" onclick="if (confirm('<?php echo $pgv_lang["check_delete"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>REM<?php echo $f; ?>.value='1'; document.quickupdate.submit(); } return false;">
						<img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php echo $pgv_lang["delete"]; ?>" />
					</a>
					<?php } ?>
				</td>
			</tr>
			<?php if ($SHOW_QUICK_RESN) {
				print_quick_resn("F".$i."RESNS[]");
			} ?>
	<?php
}
// Note: add fact
if (count($famaddfacts)>0) { ?>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); echo $pgv_lang["add_fact"]; ?></td></tr>
	<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"]; ?></td>
	<td class="descriptionbox">&nbsp;</td>
	</tr>
	<tr>
	<td class="optionbox"><select name="F<?php echo $i; ?>newfact" tabindex="<?php echo $tabkey; ?>">
		<option value=""><?php echo $pgv_lang["select_fact"]; ?></option>
	<?php $tabkey++; ?>
	<?php
	foreach($famaddfacts as $indexval => $fact) {
		$found = false;
		foreach($famfacts as $ind=>$value) {
			if ($fact==$value->getTag()) {
				$found=true;
				break;
			}
		}
		if (!$found) echo "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
	}
	?>
		</select>
	</td>
	<td class="optionbox"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="F<?php echo $i; ?>DATE" id="F<?php echo $i; ?>DATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("F".$i."DATE");?></td>
	<?php $tabkey++; ?>
	<td class="optionbox"><input type="text" size="35" tabindex="<?php echo $tabkey; ?>" name="F<?php echo $i; ?>PLAC" id="F<?php echo $i; ?>place" />
	<?php print_findplace_link("F".$i."place"); ?>
	</td>
	<?php $tabkey++; ?>
	<td class="optionbox">&nbsp;</td>
	</tr>
	<?php print_quick_resn("F".$i."RESN"); ?>
<?php }
// NOTE: Children
$chil = find_children_in_record($famrec);
	?>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="topbottombar" colspan="4"><?php echo $pgv_lang["children"]; ?></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo $pgv_lang["name"]; ?></td>
		<td class="descriptionbox center"><?php echo $factarray["SEX"]; ?></td>
		<td class="descriptionbox"><?php echo $factarray["BIRT"]; ?></td>
		<td class="descriptionbox"><input type="hidden" name="F<?php echo $i; ?>CDEL" value="" /><?php echo $pgv_lang["remove"]; ?></td>
	</tr>
			<?php
				foreach($chil as $c=>$child) {
					$person=Person::getInstance($child);
					echo "<tr><td class=\"optionbox\">";
					$name = $person->getFullName();
					if ($SHOW_ID_NUMBERS) $name .= " (".$child.")";
					$name .= " [".$pgv_lang["edit"]."]";
					echo "<a href=\"#\" onclick=\"return quickEdit('".$child."','','{$GEDCOM}');\">";
					echo PrintReady(stripLRMRLM($name));
					echo "</a>";
					$childrec = find_person_record($child);
					echo "</td>\n<td class=\"optionbox center\">";
					if ($disp) {
						$sex = $person->getSex();
						if ($sex=='M') {
							echo Person::sexImage('M', 'small'), $pgv_lang['male'];
						} else if ($sex=='F') {
							echo Person::sexImage('F', 'small'), $pgv_lang['female'];
						} else {
							echo Person::sexImage('U', 'small'), $pgv_lang['unknown'];
						}
					}
					echo "</td>\n<td class=\"optionbox\">";
					if ($disp) {
						echo $person->format_first_major_fact(PGV_EVENTS_BIRT, 2);
					}
					echo "</td>\n";
					?>
					<td class="optionbox center">
						<a href="javascript: <?php echo $pgv_lang["remove_child"]; ?>" onclick="if (confirm('<?php echo $pgv_lang["confirm_remove"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>CDEL.value='<?php echo $child; ?>'; document.quickupdate.submit(); } return false;">
							<img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php echo $pgv_lang["remove_child"]; ?>" />
						</a>
					</td>
					<?php
					echo "</tr>\n";
				}
// NOTE: Add a child
if (empty($child_surname)) $child_surname = "";
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><b><?php print_help_link("quick_update_child_help", "qm"); echo $pgv_lang["add_new_chil"]; ?></b></td></tr>
<tr>
	<td class="descriptionbox"><?php echo $pgv_lang["add_new_chil"]; ?></td>
	<td class="optionbox" colspan="3"><input type="text" size="10" name="CHIL[]" id="CHIL<?php echo $i; ?>" />
		<?php print_findindi_link("CHIL$i","");?>
	</td>
</tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>SURN" value="<?php if (!empty($child_surname)) echo $child_surname; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>GIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>SURN" value="<?php if (!empty($child_surname)) echo $child_surname; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HC<?php echo $i; ?>GIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RC<?php echo $i; ?>GIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="C<?php echo $i; ?>SEX" tabindex="<?php echo $tabkey; ?>">
			<option value="M"><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="C<?php echo $i; ?>DATE" id="C<?php echo $i; ?>DATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("C{$i}DATE");?></td>
	<?php $tabkey++; ?>
	</tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>PLAC" id="c<?php echo $i; ?>place" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("c".$i."place"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="C<?php echo $i; ?>DDATE" id="C<?php echo $i; ?>DDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("C{$i}DDATE");?></td>
	<?php $tabkey++; ?>
	</tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>DPLAC" id="c<?php echo $i; ?>dplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("c".$i."dplace"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("C".$i."RESN"); ?>
<tr><td colspan="4"><br /></td></tr>
</table>
</div>
	<?php
}

//------------------------------------------- NEW SPOUSE TAB ------------------------
?>
<div id="tab<?php echo $i; ?>" style="display: none;">
<table class="<?php echo $TEXT_DIRECTION;?> width80">
<?php
// NOTE: New wife
?>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); if (preg_match("/1 SEX M/", $gedrec)>0) echo $pgv_lang["add_new_wife"]; else echo $pgv_lang["add_new_husb"]; ?></td></tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SGIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="SSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_MARNM")!==false) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["_MARNM"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MSSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSGIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HSSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSGIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RSSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="SSEX" tabindex="<?php echo $tabkey; ?>">
			<option value="M"<?php if (preg_match("/1 SEX F/", $gedrec)>0) echo " selected=\"selected\""; ?>><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"<?php if (preg_match("/1 SEX M/", $gedrec)>0) echo " selected=\"selected\""; ?>><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"<?php if (preg_match("/1 SEX U/", $gedrec)>0) echo " selected=\"selected\""; ?>><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="BDATE" id="BDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("BDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="BPLAC" id="bplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("bplace"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("BRESN"); ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="DDATE" id="DDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("DDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="DPLAC" id="dplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("dplace"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("DRESN");

// NOTE: Marriage
?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td class="topbottombar" colspan="4"><?php print_help_link("quick_update_marriage_help", "qm"); echo $factarray["MARR"]; ?></td>
</tr>
<tr><td class="descriptionbox">
	<?php print_help_link("quick_update_marriage_help", "qm"); echo $factarray["MARR"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="checkbox" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="MARRY" id="MARRY">
		<label for="MARRY"><?php echo $pgv_lang["yes"]; ?></label></td>
	</tr>
	<?php $tabkey++; ?>
	<tr><td class="descriptionbox">
		<?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="MDATE" id="MDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("MDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="MPLAC" id="mplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="manchor1x" id="manchor1x" alt="" />
	<?php print_findplace_link("mplace"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("MRESN");

// NOTE: New child
if (empty($child_surname)) $child_surname = "";
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><b><?php print_help_link("quick_update_child_help", "qm"); echo $pgv_lang["add_new_chil"]; ?></b></td></tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="CSURN" value="<?php if (!empty($child_surname)) echo $child_surname; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="CGIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="CSURN" value="<?php if (!empty($child_surname)) echo $child_surname; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HCSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HCGIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HCSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RCSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_name_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RCGIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RCSURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="CSEX" tabindex="<?php echo $tabkey; ?>">
			<option value="M"><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="CDATE" id="CDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("CDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="CPLAC" id="cplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("cplace"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("CRESN"); ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="CDDATE" id="CDDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("CDDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="CDPLAC" id="cdplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("cdplace"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("CDRESN"); ?>
</table>
</div>

<?php //------------------------------------------- FAMILY AS CHILD TABS ------------------------
$i++;
for($j=1; $j<=count($cfams); $j++) {
	?>
<div id="tab<?php echo $i; ?>" style="display: none;">
<table class="<?php echo $TEXT_DIRECTION; ?> width80">
<?php
	$famreqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FAMFACTS);
	$parents = find_parents($cfams[$j-1]);
	$famid = $cfams[$j-1];
	$family=Family::getInstance($famid);
	if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
	else $famrec = find_updated_record($famid);

	if ($family) $subrecords = $family->getFacts(array("HUSB","WIFE","CHIL"));
	else $subrecords=array();
	$famfacts = array();
	foreach($subrecords as $ind=>$eventObj) {
		$fact = $eventObj->getTag();
		$event = $eventObj->getDetail();
		if ($fact=="EVEN" || $fact=="FACT") $fact = $eventObj->getValue("TYPE");

		if (in_array($fact, $famaddfacts)) {
			$newreqd = array();
			foreach($famreqdfacts as $r=>$rfact) {
				if ($rfact!=$fact) $newreqd[] = $rfact;
			}
			$famreqdfacts = $newreqd;
			$famfacts[] = $eventObj;
		}
	}
	foreach($famreqdfacts as $ind=>$fact) {
		$newEvent = new Event("1 $fact\n");
		$famfacts[] = $newEvent;
	}
	sort_facts($famfacts);
	$spid = "";
	if($parents) {
		if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
		else $spid=$parents["WIFE"];
	}

// NOTE: Father
?>
	<tr><td class="topbottombar" colspan="4">
<?php
	echo $pgv_lang['father'], ' ';
	$person=Person::getInstance($parents['HUSB']);
	if ($person) {
		$fatherrec = $person->getGedcomRecord();
		$child_surname = "";
		$ct = preg_match("~1 NAME.*/(.*)/~", $fatherrec, $match);
		if ($ct>0) $child_surname = $match[1];
		if ($person->getSex()=="F") $label = $pgv_lang["mother"];
		echo "<a href=\"#\" onclick=\"return quickEdit('".$parents["HUSB"]."','','{$GEDCOM}');\">";
		$name = $person->getFullname();
		if ($SHOW_ID_NUMBERS) $name .= " (".$parents["HUSB"].")";
		$name .= " [".$pgv_lang["edit"]."]";
		echo PrintReady(stripLRMRLM($name))."</a>\n";		
	} else {
		echo $pgv_lang["unknown"];
	}
	echo "</td></tr>";
	echo "<tr><td class=\"descriptionbox\">".$pgv_lang["enter_pid"]."</td><td  class=\"optionbox\" colspan=\"3\"><input type=\"text\" size=\"10\" name=\"FATHER[$i]\" id=\"FATHER$i\" value=\"".$parents['HUSB']."\" />";
	print_findindi_link("FATHER$i","");
	echo "</td></tr>";
?>
<?php if (empty($parents["HUSB"])) { ?>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); echo $pgv_lang["add_father"]; ?></td></tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="FSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="FGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="FSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_MARNM")!==false) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["_MARNM"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="FMRSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HFSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HFGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HFSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RFSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RFGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RFSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="FSEX<?php echo $i; ?>" tabindex="<?php echo $tabkey; ?>">
			<option value="M" selected="selected"><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
	</tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="FBDATE<?php echo $i; ?>" id="FBDATE<?php echo $i; ?>" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("FBDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="FBPLAC<?php echo $i; ?>" id="Fbplace<?php echo $i; ?>" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("Fbplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
	</tr>
	<?php print_quick_resn("FBRESN$i"); ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="FDDATE<?php echo $i; ?>" id="FDDATE<?php echo $i; ?>" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("FDDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="FDPLAC<?php echo $i; ?>" id="Fdplace<?php echo $i; ?>" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("Fdplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
	</tr>
	<?php print_quick_resn("FDRESN$i");
}
?>
<?php
// NOTE: Mother
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4">
<?php
	echo $pgv_lang['mother'], ' ';
	$person=Person::getInstance($parents["WIFE"]);
	if ($person) {
		$motherrec = $person->getGedcomRecord();
		if ($person->getSex()=="M") $label = $pgv_lang["father"];
		echo "<a href=\"#\" onclick=\"return quickEdit('".$parents["WIFE"]."','','{$GEDCOM}');\">";
		$name = $person->getFullName();
		if ($SHOW_ID_NUMBERS) $name .= " (".$parents["WIFE"].")";
		$name .= " [".$pgv_lang["edit"]."]";
		echo PrintReady(stripLRMRLM($name))."</a>\n";
	} else {
		echo $pgv_lang['unknown'];
	}
	echo "</td></tr>\n";
	echo "<tr><td  class=\"descriptionbox\">".$pgv_lang["enter_pid"]."</td><td  class=\"optionbox\" colspan=\"3\"><input type=\"text\" size=\"10\" name=\"MOTHER[$i]\" id=\"MOTHER$i\" value=\"".$parents['WIFE']."\" />";
	print_findindi_link("MOTHER$i","");
	?>
</td></tr>
<?php if (empty($parents["WIFE"])) { ?>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); echo $pgv_lang["add_mother"]; ?></td></tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_MARNM")!==false) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["_MARNM"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="MMRSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
</tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HMSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HMGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
</tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HMSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
</tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RMSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_name_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RMGIVN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
</tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RMSURN<?php echo $i; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="MSEX<?php echo $i; ?>" tabindex="<?php echo $tabkey; ?>">
			<option value="M"><?php echo $pgv_lang["male"]; ?></option>
			<option value="F" selected="selected"><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="MBDATE<?php echo $i; ?>" id="MBDATE<?php echo $i; ?>" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("MBDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="MBPLAC<?php echo $i; ?>" id="Mbplace<?php echo $i; ?>" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("Mbplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("MBRESN$i"); ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="MDDATE<?php echo $i; ?>" id="MDDATE<?php echo $i; ?>" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("MDDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="MDPLAC<?php echo $i; ?>" id="Mdplace<?php echo $i; ?>" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("Mdplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("MDRESN$i");
}
// NOTE: Update fact
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); echo $pgv_lang["update_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"]; ?></td>
	<td class="descriptionbox"><?php echo $pgv_lang["delete"]; ?></td>
</tr>
<?php
foreach($famfacts as $f=>$eventObj) {
		$fact_tag = $eventObj->getTag();
		$date = $eventObj->getValue("DATE");
		$plac = $eventObj->getValue("PLAC");
		$temp = $eventObj->getValue("TEMP");
		$check = false;
	?>
	<tr>
		<td class="descriptionbox">
		<?php if (isset($factarray[$fact_tag])) echo $factarray[$fact_tag];
			else if (isset($pgv_lang[$fact_tag])) echo $pgv_lang[$fact_tag];
			else echo $fact_tag;
		?>
			<input type="hidden" name="F<?php echo $i; ?>TAGS[]" value="<?php echo $fact_tag; ?>" />
			<?php
			if ($fact_tag=='MARR') {
				$factdetail = explode(' ', trim($eventObj->getGedComRecord("MARR")));
				if (isset($factdetail[2]))
					if (count($factdetail) > 3 || strtoupper($factdetail[2]) == "Y")
						$check = true;
				?>
				&nbsp;&nbsp;
				<input type="checkbox" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="5" name="F<?php echo $i; ?>MARRY" id="F<?php echo $i; ?>MARRY"
				<?php
				if ($check) {
					echo 'checked="checked">';
				}
				else echo '>';
				?>
				<label for="F<?php echo $i; ?>MARRY"><?php echo $pgv_lang["yes"]; ?></label>
				<?php
			}
			else if ($fact_tag=='DIV') {
				$factdetail = explode(' ', trim($eventObj->getGedComRecord("DIV")));
				if (isset($factdetail[2]))
					if (count($factdetail) > 3 || strtoupper($factdetail[2]) == "Y")
						$check = true;
				?>
				&nbsp;&nbsp;
				<input type="checkbox" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="5" name="F<?php echo $i; ?>DIV" id="F<?php echo $i; ?>DIV"
				<?php
				if ($check) {
					echo 'checked="checked">';
				}
				else echo '>';
				?>
				<label for="F<?php echo $i; ?>DIV"><?php echo $pgv_lang["yes"]; ?></label>
				<?php
			}
			?>
		</td>
		<td class="optionbox"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; $tabkey++;?>" size="15" name="F<?php echo $i; ?>DATES[]" id="F<?php echo $i; ?>DATE<?php echo $f; ?>" onblur="valid_date(this);" value="<?php echo PrintReady(htmlspecialchars($date,ENT_COMPAT,'UTF-8')); ?>" />&nbsp;<?php print_calendar_popup("F{$i}DATE$f");?></td>
		<?php if (empty($temp) && (!in_array($fact_tag, $nonplacfacts))) { ?>
			<td class="optionbox"><input size="35" type="text" tabindex="<?php echo $tabkey; $tabkey++; ?>" name="F<?php echo $i; ?>PLACS[]" id="F<?php echo $i; ?>place<?php echo $f; ?>" value="<?php echo PrintReady(htmlspecialchars($plac,ENT_COMPAT,'UTF-8')); ?>" />
			<?php print_findplace_link("F".$i."place$f"); ?>
			</td>
		<?php }
		else {
			echo "<td class=\"optionbox\"><select tabindex=\"".$tabkey."\" name=\"F".$i."TEMP[]\" >\n";
			echo "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
			foreach($TEMPLE_CODES as $code=>$temple) {
				echo "<option value=\"$code\"";
				if ($code==$temp) echo " selected=\"selected\"";
				echo ">$temple</option>\n";
			}
			echo "</select>\n</td>\n";
			$tabkey++;
		}
		?>
		<td class="optionbox center">
			<input type="hidden" name="F<?php echo $i; ?>REMS[<?php echo $f; ?>]" id="F<?php echo $i; ?>REM<?php echo $f; ?>" value="0" />
			<?php if ($date!='' || $plac!='' || $temp!='' || $check==true) { ?>
			<a href="javascript: <?php echo $pgv_lang["delete"]; ?>" onclick="if (confirm('<?php echo $pgv_lang["check_delete"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>REM<?php echo $f; ?>.value='1'; document.quickupdate.submit(); } return false;">
				<img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php echo $pgv_lang["delete"]; ?>" />
			</a>
			<?php } ?>
		</td>
	</tr>
	<?php if ($SHOW_QUICK_RESN) {
		print_quick_resn("F".$i."RESNS[]");
	} ?>
	<?php
}
?>
<?php
// NOTE: Add new fact
?>
<?php if (count($famaddfacts)>0) { ?>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); echo $pgv_lang["add_fact"]; ?></td></tr>
	<tr>
		<td class="descriptionbox">&nbsp;</td>
		<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DATE"]; ?></td>
		<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["PLAC"]; ?></td>
		<td class="descriptionbox">&nbsp;</td>
		</tr>
	<tr>
		<td class="optionbox"><select name="F<?php echo $i; ?>newfact" tabindex="<?php echo $tabkey; ?>">
			<option value=""><?php echo $pgv_lang["select_fact"]; ?></option>
		<?php $tabkey++; ?>
		<?php
		foreach($famaddfacts as $indexval => $fact) {
			$found = false;
			foreach($famfacts as $ind=>$value) {
				if ($fact==$value->getTag()) {
					$found=true;
					break;
				}
			}
			if (!$found) echo "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
		}
		?>
			</select>
		</td>
		<td class="optionbox"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="F<?php echo $i; ?>DATE" id="F<?php echo $i; ?>DATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("F".$i."DATE");?></td>
		<?php $tabkey++; ?>
		<td class="optionbox"><input size="35" type="text" tabindex="<?php echo $tabkey; ?>" name="F<?php echo $i; ?>PLAC" id="F<?php echo $i; ?>place" />
		<?php print_findplace_link("F".$i."place"); ?>
		</td>
		<?php $tabkey++; ?>
		<td class="optionbox">&nbsp;</td>
	</tr>
	<?php print_quick_resn("RESN"); ?>
<?php }
// NOTE: Children
$chil = find_children_in_record($famrec, $pid);
	?>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="topbottombar" colspan="4"><?php echo $pgv_lang["children"]; ?></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo $pgv_lang["name"]; ?></td>
		<td class="descriptionbox center"><?php echo $factarray["SEX"]; ?></td>
		<td class="descriptionbox"><?php echo $factarray["BIRT"]; ?></td>
		<td class="descriptionbox"><?php echo $pgv_lang["remove"]; ?><input type="hidden" name="F<?php echo $i; ?>CDEL" value="" /></td>
	</tr>
	<?php
		foreach($chil as $c=>$child) {
			$person=Person::getInstance($child);
			echo "<tr><td class=\"optionbox\">";
			$name = $person->getFullName();
			if ($SHOW_ID_NUMBERS) $name .= " (".$child.")";
			$name .= " [".$pgv_lang["edit"]."]";
			echo "<a href=\"#\" onclick=\"return quickEdit('".$child."','','{$GEDCOM}');\">";
			echo PrintReady(stripLRMRLM($name));
			echo "</a>";
			echo "</td>\n<td class=\"optionbox center\">";
			$sex = $person->getSex();
			if ($sex=='M') {
				echo Person::sexImage('M', 'small'), $pgv_lang['male'];
			} elseif ($sex=='F') {
				echo Person::sexImage('F', 'small'), $pgv_lang['female'];
			} else {
				echo Person::sexImage('U', 'small'), $pgv_lang['unknown'];
			}
			echo "</td>\n<td class=\"optionbox\">";
			echo $person->format_first_major_fact(PGV_EVENTS_BIRT, 2);
			echo "</td>\n";
			?>
			<td class="optionbox center">
				<a href="javascript: <?php echo $pgv_lang["remove_child"]; ?>" onclick="if (confirm('<?php echo $pgv_lang["confirm_remove"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>CDEL.value='<?php echo $child; ?>'; document.quickupdate.submit(); } return false;">
					<img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php echo $pgv_lang["remove_child"]; ?>" />
				</a>
			</td>
			<?php
			echo "</tr>\n";
		}
// NOTE: Add a child
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_child_help", "qm"); echo $pgv_lang["add_child_to_family"]; ?></td></tr>
<tr>
	<td class="descriptionbox"><?php echo $pgv_lang["add_child_to_family"]; ?></td>
	<td class="optionbox" colspan="3"><input type="text" size="10" name="CHIL[]" id="CHIL<?php echo $i; ?>" />
	<?php print_findindi_link("CHIL$i","");?>
	</td>
</tr>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>SURN" value="<?php //if (!empty($child_surname)) echo $child_surname; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); echo $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>GIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); echo $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>SURN" value="<?php //if (!empty($child_surname)) echo $child_surname; ?>" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "_HEB")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_GIVN_help", "qm"); echo $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HC<?php echo $i; ?>GIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit__HEB_SURN_help", "qm"); echo $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="HC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<?php if (strstr($ADVANCED_NAME_FACTS, "ROMN")!==false) { ?>
<?php if ($NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_GIVN_help", "qm"); echo $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RC<?php echo $i; ?>GIVN" /></td>
</tr>
<?php $tabkey++; ?>
<?php if (!$NAME_REVERSE) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_ROMN_SURN_help", "qm"); echo $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php echo $tabkey; ?>" name="RC<?php echo $i; ?>SURN" /></td>
</tr>
<?php $tabkey++; ?>
<?php } ?>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); echo $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="C<?php echo $i; ?>SEX" tabindex="<?php echo $tabkey; ?>">
			<option value="M"><?php echo $pgv_lang["male"]; ?></option>
			<option value="F"><?php echo $pgv_lang["female"]; ?></option>
			<option value="U"><?php echo $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["BIRT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="C<?php echo $i; ?>DATE" id="C<?php echo $i; ?>DATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("C{$i}DATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["BIRT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>PLAC" id="c<?php echo $i; ?>place" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("c".$i."place"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("C".$i."RESN"); ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); echo $factarray["DEAT:DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" dir="ltr" tabindex="<?php echo $tabkey; ?>" size="15" name="C<?php echo $i; ?>DDATE" id="C<?php echo $i; ?>DDATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("C{$i}DDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); echo $factarray["DEAT:PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="45" type="text" tabindex="<?php echo $tabkey; ?>" name="C<?php echo $i; ?>DPLAC" id="c<?php echo $i; ?>dplace" /><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" alt="" />
	<?php print_findplace_link("c".$i."dplace"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("C".$i."DRESN"); ?>
</table>
</div>
	<?php
	$i++;
}
if (PGV_USER_IS_ADMIN) {
	echo "<table class=\"facts_table width80\">\n";
	echo "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap\">";
	print_help_link("no_update_CHAN_help", "qm");
	echo $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
	echo "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
	echo $pgv_lang["no_update_CHAN"]."<br />\n";
	echo "</td></tr>\n";
	echo "</table>";
}
?>
<input type="submit" value="<?php echo $pgv_lang["save"]; ?>" />
</form>
<?php
}
print_simple_footer();
?>

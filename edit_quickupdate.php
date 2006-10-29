<?php
/**
 * PopUp Window to provide users with a simple quick update form.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @version $Id: edit_quickupdate.php,v 1.3 2006/10/29 11:02:13 lsces Exp $
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require("includes/functions_edit.php");
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

if ($_SESSION["cookie_login"]) {
	header("Location: login.php?type=simple&url=edit_interface.php");
	exit;
}

//-- @TODO make list a configurable list
$addfacts = preg_split("/[,; ]/", $QUICK_ADD_FACTS);
usort($addfacts, "factsort");

$reqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FACTS);

//-- @TODO make list a configurable list
$famaddfacts = preg_split("/[,; ]/", $QUICK_ADD_FAMFACTS);
usort($famaddfacts, "factsort");
$famreqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FAMFACTS);

$align="right";
if ($TEXT_DIRECTION=="rtl") $align="left";

print_simple_header($pgv_lang["quick_update_title"]);

//-- only allow logged in users to access this page
$uname = getUserName();
if ((!$ALLOW_EDIT_GEDCOM)||(!$USE_QUICK_UPDATE)||(empty($uname))) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

$user = getUser($uname);

if (!isset($action)) $action="";
if (!isset($closewin)) $closewin=0;
if (empty($pid)) {
	if (!empty($user["gedcomid"][$GEDCOM])) $pid = $user["gedcomid"][$GEDCOM];
	else $pid = "";
}
$pid = clean_input($pid);

//-- only allow editors or users who are editing their own individual or their immediate relatives
$pass = false;
if (!empty($user["gedcomid"][$GEDCOM])) {
	if ($pid==$user["gedcomid"][$GEDCOM]) $pass=true;
	else {
		$famids = pgv_array_merge(find_sfamily_ids($user["gedcomid"][$GEDCOM]), find_family_ids($user["gedcomid"][$GEDCOM]));
		foreach($famids as $indexval => $famid) {
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
			else $famrec = find_record_in_file($famid);
			if (preg_match("/1 HUSB @$pid@/", $famrec)>0) $pass=true;
			if (preg_match("/1 WIFE @$pid@/", $famrec)>0) $pass=true;
			if (preg_match("/1 CHIL @$pid@/", $famrec)>0) $pass=true;
		}
	}
}
if (empty($pid)) $pass=false;
if ((!userCanEdit($uname))&&(!$pass)) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

//-- find the latest gedrec for the individual
if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
else $gedrec = find_record_in_file($pid);

//-- only allow edit of individual records
$disp = true;
$ct = preg_match("/0 @$pid@ (.*)/", $gedrec, $match);
if ($ct>0) {
	$type = trim($match[1]);
	if ($type=="INDI") {
		$disp = displayDetailsById($pid);
	}
	else {
		print $pgv_lang["access_denied"];
		print_simple_footer();
		exit;
	}
}

if ((!$disp)||(!$ALLOW_EDIT_GEDCOM)) {

	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!userCanEdit(getUserName())) print "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
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
		$var = $prefix.$i."DATES";
		if (!empty($GLOBALS[$var])) $DATES = $GLOBALS[$var];
		else $DATES = array();
		$var = $prefix.$i."PLACS";
		if (!empty($GLOBALS[$var])) $PLACS = $GLOBALS[$var];
		else $PLACS = array();
		$var = $prefix.$i."TEMPS";
		if (!empty($GLOBALS[$var])) $TEMPS = $GLOBALS[$var];
		else $TEMPS = array();
		$var = $prefix.$i."RESNS";
		if (!empty($GLOBALS[$var])) $RESNS = $GLOBALS[$var];
		else $RESNS = array();
		$var = $prefix.$i."REMS";
		if (!empty($GLOBALS[$var])) $REMS = $GLOBALS[$var];
		else $REMS = array();
		
		for($j=0; $j<count($TAGS); $j++) {
			if (!empty($TAGS[$j])) {
				$fact = $TAGS[$j];
//				print $fact;
				if (!isset($repeat_tags[$fact])) $repeat_tags[$fact] = 1;
				else $repeat_tags[$fact]++;
				
				$DATES[$j] = check_input_date($DATES[$j]);
				if (!isset($REMS[$j])) $REMS[$j] = 0;
				if ($REMS[$j]==1) {
					$DATES[$j]="";
					$PLACS[$j]="";
					$TEMPS[$j]="";
					$RESNS[$j]="";
				}
				if ((empty($DATES[$j]))&&(empty($PLACS[$j]))&&(empty($TEMPS[$j]))&&(empty($RESNS[$j]))) {
					$factrec="";
				}
				else {
					if (!in_array($fact, $typefacts)) $factrec = "1 $fact\r\n";
					else $factrec = "1 EVEN\r\n2 TYPE $fact\r\n";
					if (!empty($DATES[$j])) $factrec .= "2 DATE $DATES[$j]\r\n";
					if (!empty($PLACS[$j])) $factrec .= "2 PLAC $PLACS[$j]\r\n";
					if (!empty($TEMPS[$j])) $factrec .= "2 TEMP $TEMPS[$j]\r\n";
					if (!empty($RESNS[$j])) $factrec .= "2 RESN $RESNS[$j]\r\n";
				}
				if (!in_array($fact, $typefacts)) $lookup = "1 $fact";
				else $lookup = "1 EVEN\r\n2 TYPE $fact\r\n";
				$pos1 = strpos($famrec, $lookup);
//				print $pos1."=pos1";
				$k=1;
				//-- make sure we are working with the correct fact
				while($k<$repeat_tags[$fact]) {
					$pos1 = strpos($famrec, $lookup, $pos1+5);
					$k++;
					if ($pos1===false) break;
				}
//				print $pos1."=pos1";
				$noupdfact = false;
				if ($pos1!==false) {
					$pos2 = strpos($famrec, "\n1 ", $pos1+5);
					if ($pos2===false) $pos2 = strlen($famrec);
					$oldfac = trim(substr($famrec, $pos1, $pos2-$pos1));
					$noupdfact = FactEditRestricted($pid, $oldfac);
					if ($noupdfact) {
						print "<br />".$pgv_lang["update_fact_restricted"]." ".$factarray[$fact]."<br /><br />";
					}
					else {
						//-- delete the fact
						if ($REMS[$j]==1) { 
							$famupdate = true;
							$famrec = substr($famrec, 0, $pos1) . "\r\n". substr($famrec, $pos2);
//							print "sfamupdate_del [".$factrec."]{".$oldfac."}";
						}
						else if (!empty($oldfac) && !empty($factrec)) {
							$factrec = $oldfac;
							if (!empty($DATES[$j])) {
								if (strstr($factrec, "\n2 DATE")) $factrec = preg_replace("/2 DATE.*/", "2 DATE $DATES[$j]", $factrec);
								else $factrec = $factrec."\r\n2 DATE $DATES[$j]";
							}
							if (!empty($PLACS[$j])) {
								if (strstr($factrec, "\n2 PLAC")) $factrec = preg_replace("/2 PLAC.*/", "2 PLAC $PLACS[$j]", $factrec);
								else $factrec = $factrec."\r\n2 PLAC $PLACS[$j]";
							}
							if (!empty($TEMPS[$j])) {
								if (strstr($factrec, "\n2 TEMP")) $factrec = preg_replace("/2 TEMP.*/", "2 TEMP $TEMPS[$j]", $factrec);
								else $factrec = $factrec."\r\n2 TEMP $TEMPS[$j]";
							}
							if (!empty($RESNS[$j])) {
								if (strstr($factrec, "\n2 RESN")) $factrec = preg_replace("/2 RESN.*/", "2 RESN $RESNS[$j]", $factrec);
								else $factrec = $factrec."\r\n2 RESN $RESNS[$j]";
							}
						
							$factrec = preg_replace("/[\r\n]+/", "\r\n", $factrec);
							$oldfac = preg_replace("/[\r\n]+/", "\r\n", $oldfac);
//			print "<table><tr><th>new</th><th>old</th></tr><tr><td><pre>$factrec</pre></td><td><pre>$oldfac</pre></td></tr></table>";
							if (trim($factrec) != trim($oldfac)) {
								$famupdate = true;
								$famrec = substr($famrec, 0, $pos1) . trim($factrec)."\r\n" . substr($famrec, $pos2);
//			print "sfamupdate3 [".$factrec."]{".$oldfac."}";
							}
						}
					}
				}
				else if (!empty($factrec)) {
					$famrec .= "\r\n".$factrec;
					$famupdate = true;
//						print "sfamupdate2";
				}
			}
		}
		return $famupdate;
	}
	
	print "<h2>".$pgv_lang["quick_update_title"]."</h2>\n";
	print "<b>".PrintReady(get_person_name($pid))."</b><br /><br />";
	
	AddToChangeLog("Quick update attempted for $pid by >".getUserName()."<");

	$updated = false;
	$error = "";
	$oldgedrec = $gedrec;
	//-- check for name update
	if (!empty($GIVN) || !empty($SURN)) {
		$namerec = trim(get_sub_record(1, "1 NAME", $gedrec));
		if (!empty($namerec)) {
			if (!empty($GIVN)) {
				$namerec = preg_replace("/1 NAME.+\/(.*)\//", "1 NAME $GIVN /$1/", $namerec);
				if (preg_match("/2 GIVN/", $namerec)>0) $namerec = preg_replace("/2 GIVN.+/", "2 GIVN $GIVN\r\n", $namerec);
				else $namerec.="\r\n2 GIVN $GIVN";
			}
			if (!empty($SURN)) {
				$namerec = preg_replace("/1 NAME(.+)\/.*\//", "1 NAME$1/$SURN/", $namerec);
				if (preg_match("/2 SURN/", $namerec)>0) $namerec = preg_replace("/2 SURN.+/", "2 SURN $SURN\r\n", $namerec);
				else $namerec.="\r\n2 SURN $SURN";
			}
			$pos1 = strpos($gedrec, "1 NAME");
			if ($pos1!==false) {
				$pos2 = strpos($gedrec, "\n1", $pos1+5);
				if ($pos2===false) {
					$gedrec = substr($gedrec, 0, $pos1).$namerec;
				}
				else {
					$gedrec = substr($gedrec, 0, $pos1).$namerec."\r\n".substr($gedrec, $pos2+1);
				}
			}
		}
		else $gedrec .= "\r\n1 NAME $GIVN /$SURN/\r\n2 GIVN $GIVN\r\n2 SURN $SURN";
		$updated = true;
//		print "<pre>NAME\n".$gedrec."</pre>\n";
	}
	
	//-- update the person's gender
	if (!empty($SEX)) {
		if (preg_match("/1 SEX (\w*)/", $gedrec, $match)>0) {
			if ($match[1] != $SEX) {
				$gedrec = preg_replace("/1 SEX (\w*)/", "1 SEX $SEX", $gedrec);
				$updated = true;
			}
		}
		else {
			$gedrec .= "\r\n1 SEX $SEX";
			$updated = true;
		}
	}
	//-- rtl name update
	if ($USE_RTL_FUNCTIONS) {
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
					$gedrec = substr($gedrec, 0, $pos1)."\r\n2 _HEB $HGIVN /$HSURN/\r\n".substr($gedrec, $pos1+1);
				}
				else $gedrec .= "\r\n1 NAME $HGIVN /$HSURN/\r\n2 _HEB $HGIVN /$HSURN/\r\n"; 
			}
			$updated = true;
		}
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
					$gedrec = substr($gedrec, 0, $pos1)."\r\n2 ROMN $RGIVN /$RSURN/\r\n".substr($gedrec, $pos1+1);
				}
				else $gedrec .= "\r\n1 NAME $RGIVN /$RSURN/\r\n2 ROMN $RGIVN /$RSURN/\r\n"; 
			}
			$updated = true;
		}
	}
	
	//-- check for updated facts
	if (count($TAGS)>0) {
		$updated |= check_updated_facts("", $gedrec, $TAGS, "");
//		print "FACTS <pre>$gedrec</pre>";
	}

	//-- check for new fact
	if (!empty($newfact)) {
		if (!in_array($newfact, $typefacts)) $factrec = "1 $newfact\r\n";
		else $factrec = "1 EVEN\r\n2 TYPE $newfact\r\n";
		if (!empty($DATE)) {
			$DATE = check_input_date($DATE);
			$factrec .= "2 DATE $DATE\r\n";
		}
		if (!empty($PLAC)) $factrec .= "2 PLAC $PLAC\r\n";
		if (!empty($TEMP)) $factrec .= "2 TEMP $TEMP\r\n";
		if (!empty($RESN)) $factrec .= "2 RESN $RESN\r\n";
		//-- make sure that there is at least a Y
		if (preg_match("/\n2 \w*/", $factrec)==0) $factrec = "1 $newfact Y\r\n";
		$gedrec .= "\r\n".$factrec;
		$updated = true;
	}

	//-- check for photo update
	if (!empty($_FILES["FILE"]['tmp_name'])) {
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		if (!move_uploaded_file($_FILES['FILE']['tmp_name'], $MEDIA_DIRECTORY.basename($_FILES['FILE']['name']))) {
			$error .= "<br />".$pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['FILE']['error']];
		}
		else {
			$filename = $MEDIA_DIRECTORY.basename($_FILES['FILE']['name']);
			$thumbnail = $MEDIA_DIRECTORY."thumbs/".basename($_FILES['FILE']['name']);
			generate_thumbnail($filename, $thumbnail);

			$factrec = "1 OBJE\r\n";
			$factrec .= "2 FILE ".$filename."\r\n";
			if (!empty($TITL)) $factrec .= "2 TITL $TITL\r\n";

			if (empty($replace)) $gedrec .= "\r\n".$factrec;
			else {
				$fact = "OBJE";
				$pos1 = strpos($gedrec, "1 $fact");
				if ($pos1!==false) {
					$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
					if ($pos2===false) $pos2 = strlen($gedrec)-1;
					$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
				}
				else $gedrec .= "\r\n".$factrec;
			}
			$updated = true;
		}
	}

	//--address phone email
	$factrec = "";
	if (!empty($ADDR)) {
		if (!empty($ADR1)||!empty($CITY)||!empty($POST)) {
			$factrec = "1 ADDR\r\n";
			if (!empty($_NAME)) $factrec.="2 _NAME ".$_NAME."\r\n";
			if (!empty($ADR1)) $factrec.="2 ADR1 ".$ADR1."\r\n";
			if (!empty($ADR2)) $factrec.="2 ADR2 ".$ADR2."\r\n";
			if (!empty($CITY)) $factrec.="2 CITY ".$CITY."\r\n";
			if (!empty($STAE)) $factrec.="2 STAE ".$STAE."\r\n";
			if (!empty($POST)) $factrec.="2 POST ".$POST."\r\n";
			if (!empty($CTRY)) $factrec.="2 CTRY ".$CTRY."\r\n";
		}
		else {
			$factrec = "1 ADDR ";
			$lines = preg_split("/\r*\n/", $ADDR);
			$factrec .= $lines[0]."\r\n";
			for($i=1; $i<count($lines); $i++) $factrec .= "2 CONT ".$lines[$i]."\r\n";
		}
	}
	$pos1 = strpos($gedrec, "1 ADDR");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\r\n".$factrec;
		$updated = true;
	}

	$factrec = "";
	if (!empty($PHON)) $factrec = "1 PHON $PHON\r\n";
	$pos1 = strpos($gedrec, "1 PHON");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\r\n".$factrec;
		$updated = true;
	}

	$factrec = "";
	$factrec = "";
	if (!empty($FAX)) $factrec = "1 FAX $FAX\r\n";
	$pos1 = strpos($gedrec, "1 FAX");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\r\n".$factrec;
		$updated = true;
	}

	$factrec = "";
	if (!empty($EMAIL)) $factrec = "1 EMAIL $EMAIL\r\n";
	$pos1 = strpos($gedrec, "1 EMAIL");
	if ($pos1!==false) {
		$pos2 = strpos($gedrec, "\n1 ", $pos1+1);
		if ($pos2===false) $pos2 = strlen($gedrec)-1;
		$gedrec = substr($gedrec, 0, $pos1) . "\r\n".$factrec . substr($gedrec, $pos2);
		$updated = true;
	}
	else if (!empty($factrec)) {
		$gedrec .= "\r\n".$factrec;
		$updated = true;
	}
	
	//-- spouse family tabs
	$sfams = find_families_in_record($gedrec, "FAMS");
	for($i=1; $i<=count($sfams); $i++) {
		$famupdate = false;
		$famid = $sfams[$i-1];
		if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
		else $famrec = find_record_in_file($famid);
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
		
		$sgivn = "SGIVN$i";
		$ssurn = "SSURN$i";
		//--add new spouse name, birth
		if (!empty($$sgivn) || !empty($$ssurn)) {
			//-- first add the new spouse
			$spouserec = "0 @REF@ INDI\r\n";
			$spouserec .= "1 NAME ".$$sgivn." /".$$ssurn."/\r\n";
			if (!empty($$sgivn)) $spouserec .= "2 GIVN ".$$sgivn."\r\n";
			if (!empty($$ssurn)) $spouserec .= "2 SURN ".$$ssurn."\r\n";
			$hsgivn = "HSGIVN$i";
			$hssurn = "HSSURN$i";
			if (!empty($$hsgivn) || !empty($$hssurn)) {
				$spouserec .= "2 _HEB ".$$hsgivn." /".$$hssurn."/\r\n";
			}
			$rsgivn = "RSGIVN$i";
			$rssurn = "RSSURN$i";
			if (!empty($$rsgivn) || !empty($$rssurn)) {
				$spouserec .= "2 ROMN ".$$rsgivn." /".$$rssurn."/\r\n";
			}
			$ssex = "SSEX$i";
			if (!empty($$ssex)) $spouserec .= "1 SEX ".$$ssex."\r\n";
			$bdate = "BDATE$i";
			$bplac = "BPLAC$i";
			if (!empty($bdate)||!empty($bplac)) {
				$spouserec .= "1 BIRT\r\n";
				if (!empty($$bdate)) {
					$bdate = $$bdate;
					$bdate = check_input_date($bdate);
					$spouserec .= "2 DATE $bdate\r\n";
				}
				if (!empty($$bplac)) $spouserec .= "2 PLAC ".$$bplac."\r\n";
				$bresn = "BRESN$i";
				if (!empty($$bresn)) $spouserec .= "2 RESN ".$$bresn."\r\n";
			}
			$spouserec .= "\r\n1 FAMS @$famid@\r\n";
			$SPID[$i] = append_gedrec($spouserec);
		}
		
		if (!empty($SPID[$i]) && $spid!=$SPID[$i]) {
			if (strstr($famrec, "1 $tag")!==false) $famrec = preg_replace("/1 $tag @.*@/", "1 $tag @$SPID[$i]@", $famrec);
			else $famrec .= "\r\n1 $tag @$SPID[$i]@";
			$famupdate = true;
//			print "sfamupdate1";
		}
		
		//-- check for updated facts
		$var = "F".$i."TAGS";
		if (!empty($$var)) $TAGS = $$var;
		else $TAGS = array();
		if (count($TAGS)>0) {
			$famupdate |= check_updated_facts($i, $famrec, $TAGS, "F");
		}
		
		//-- check for new fact
		$var = "F".$i."newfact";
		if (!empty($$var)) $newfact = $$var;
		else $newfact = "";
		if (!empty($newfact)) {
			if (!in_array($newfact, $typefacts)) $factrec = "1 $newfact\r\n";
			else $factrec = "1 EVEN\r\n2 TYPE $newfact\r\n";
			$var = "F".$i."DATE";
			if (!empty($$var)) $FDATE = $$var;
			else $FDATE = "";
			if (!empty($FDATE)) {
				$FDATE = check_input_date($FDATE);
				$factrec .= "2 DATE $FDATE\r\n";
			}
			$var = "F".$i."PLAC";
			if (!empty($$var)) $FPLAC = $$var;
			else $FPLAC = "";
			if (!empty($FPLAC)) $factrec .= "2 PLAC $FPLAC\r\n";
			$var = "F".$i."TEMP";
			if (!empty($$var)) $FTEMP = $$var;
			else $FTEMP = "";
			if (!empty($FTEMP)) $factrec .= "2 TEMP $FTEMP\r\n";
			$var = "F".$i."RESN";
			if (!empty($$var)) $FRESN = $$var;
			else $FRESN = "";
			if (!empty($FRESN)) $factrec .= "2 RESN $FRESN\r\n";
			//-- make sure that there is at least a Y
			if (preg_match("/\n2 \w*/", $factrec)==0) $factrec = "1 $newfact Y\r\n";
			$famrec .= "\r\n".$factrec;
			$famupdate = true;
//			print "sfamupdate4";
		}
		
		if (!empty($CHIL[$i])) {
			$famupdate = true;
//			print "sfamupdate5";
			$famrec .= "\r\n1 CHIL @".$CHIL[$i]."@";
			$childrec = find_record_in_file($CHIL[$i]);
			if (preg_match("/1 FAMC @$famid@/", $childrec)==0) {
				$childrec = "\r\n1 FAMC @$famid@";
				replace_gedrec($CHIL[$i], $childrec);
			}
		}
		
		$var = "F".$i."CDEL";
		if (!empty($$var)) $fcdel = $$var;
		else $fcdel = "";
		if (!empty($fcdel)) {
			$famrec = preg_replace("/1 CHIL @$fcdel@/", "", $famrec);
			$famupdate = true;
//			print "sfamupdate6";
		}
		
		//--add new child, name, birth
		$cgivn = "";
		$var = "C".$i."GIVN";
		if (!empty($$var)) $cgivn = $$var;
		else $cgivn = "";
		$csurn = "";
		$var = "C".$i."SURN";
		if (!empty($$var)) $csurn = $$var;
		else $csurn = "";
		if (!empty($cgivn) || !empty($csurn)) {
			//-- first add the new child
			$childrec = "0 @REF@ INDI\r\n";
			$childrec .= "1 NAME $cgivn /$csurn/\r\n";
			if (!empty($cgivn)) $childrec .= "2 GIVN $cgivn\r\n";
			if (!empty($csurn)) $childrec .= "2 SURN $csurn\r\n";
			$hsgivn = "HC{$i}GIVN";
			$hssurn = "HC{$i}SURN";
			if (!empty($$hsgivn) || !empty($$hssurn)) {
				$childrec .= "2 _HEB ".$$hsgivn." /".$$hssurn."/\r\n";
			}
			$rsgivn = "RC{$i}GIVN";
			$rssurn = "RC{$i}SURN";
			if (!empty($$rsgivn) || !empty($$rssurn)) {
				$childrec .= "2 ROMN ".$$rsgivn." /".$$rssurn."/\r\n";
			}
			$var = "C".$i."SEX";
			$csex = "";
			if (!empty($$var)) $csex = $$var;
			if (!empty($csex)) $childrec .= "1 SEX $csex\r\n";
			$var = "C".$i."DATE";
			$cdate = "";
			if (!empty($$var)) $cdate = $$var;
			$var = "C".$i."PLAC";
			$cplac = "";
			if (!empty($$var)) $cplac = $$var;
			if (!empty($cdate)||!empty($cplac)) {
				$childrec .= "1 BIRT\r\n";
				$cdate = check_input_date($cdate);
				if (!empty($cdate)) $childrec .= "2 DATE $cdate\r\n";
				if (!empty($cplac)) $childrec .= "2 PLAC $cplac\r\n";
				$var = "C".$i."RESN";
				$cresn = "";
				if (!empty($$var)) $cresn = $$var;
				if (!empty($cresn)) $childrec .= "2 RESN $cresn\r\n";
			}
			$childrec .= "1 FAMC @$famid@\r\n";
			$cxref = append_gedrec($childrec);
			$famrec .= "\r\n1 CHIL @$cxref@";
			$famupdate = true;
//			print "sfamupdate7";
		}
		
		if ($famupdate && ($famrec!=$oldfamrec)) replace_gedrec($famid, $famrec);
	}

	//--add new spouse name, birth, marriage
	if (!empty($SGIVN) || !empty($SSURN)) {
		//-- first add the new spouse
		$spouserec = "0 @REF@ INDI\r\n";
		$spouserec .= "1 NAME $SGIVN /$SSURN/\r\n";
		if (!empty($SGIVN)) $spouserec .= "2 GIVN $SGIVN\r\n";
		if (!empty($SSURN)) $spouserec .= "2 SURN $SSURN\r\n";
		if (!empty($SSEX)) $spouserec .= "1 SEX $SSEX\r\n";
		if (!empty($BDATE)||!empty($BPLAC)) {
			$spouserec .= "1 BIRT\r\n";
			if (!empty($BDATE)) $spouserec .= "2 DATE $BDATE\r\n";
			if (!empty($BPLAC)) $spouserec .= "2 PLAC $BPLAC\r\n";
			if (!empty($BRESN)) $spouserec .= "2 RESN $BRESN\r\n";
		}
		$xref = append_gedrec($spouserec);

		//-- next add the new family record
		$famrec = "0 @REF@ FAM\r\n";
		if ($SSEX=="M") $famrec .= "1 HUSB @$xref@\r\n1 WIFE @$pid@\r\n";
		else $famrec .= "1 HUSB @$pid@\r\n1 WIFE @$xref@\r\n";
		$newfamid = append_gedrec($famrec);

		//-- add the new family id to the new spouse record
		$spouserec = find_record_in_file($xref);
		$spouserec .= "\r\n1 FAMS @$newfamid@\r\n";
		replace_gedrec($xref, $spouserec);
		
		//-- last add the new family id to the persons record
		$gedrec .= "\r\n1 FAMS @$newfamid@\r\n";
		$updated = true;
	}
	if (!empty($MDATE)||!empty($MPLAC)) {
		if (empty($newfamid)) {
			$famrec = "0 @REF@ FAM\r\n";
			if (preg_match("/1 SEX M/", $gedrec)>0) $famrec .= "1 HUSB @$pid@\r\n";
			else $famrec .= "1 WIFE @$pid@";
			$newfamid = append_gedrec($famrec);
			$gedrec .= "\r\n1 FAMS @$newfamid@";
			$updated = true;
		}
		$factrec = "1 MARR\r\n";
		$MDATE = check_input_date($MDATE);
		if (!empty($MDATE)) $factrec .= "2 DATE $MDATE\r\n";
		if (!empty($MPLAC)) $factrec .= "2 PLAC $MPLAC\r\n";
		if (!empty($MRESN)) $factrec .= "2 RESN $MRESN\r\n";
		$famrec .= "\r\n".$factrec;
	}

	//--add new child, name, birth
	if (!empty($CGIVN) || !empty($CSURN)) {
		//-- first add the new child
		$childrec = "0 @REF@ INDI\r\n";
		$childrec .= "1 NAME $CGIVN /$CSURN/\r\n";
		if (!empty($CGIVN)) $childrec .= "2 GIVN $CGIVN\r\n";
		if (!empty($CSURN)) $childrec .= "2 SURN $CSURN\r\n";
		if (!empty($HCGIVN) || !empty($HCSURN)) {
			$childrec .= "2 _HEB $HCGIVN /$HCSURN/\r\n";
		}
		if (!empty($CSEX)) $childrec .= "1 SEX $CSEX\r\n";
		if (!empty($CDATE)||!empty($CPLAC)) {
			$childrec .= "1 BIRT\r\n";
			$CDATE = check_input_date($CDATE);
			if (!empty($CDATE)) $childrec .= "2 DATE $CDATE\r\n";
			if (!empty($CPLAC)) $childrec .= "2 PLAC $CPLAC\r\n";
			if (!empty($CRESN)) $childrec .= "2 RESN $CRESN\r\n";
		}
		$cxref = append_gedrec($childrec);

		//-- if a new family was already made by adding a spouse or a marriage
		//-- then use that id, otherwise create a new family
		if (empty($newfamid)) {
			$famrec = "0 @REF@ FAM\r\n";
			if (preg_match("/1 SEX M/", $gedrec)>0) $famrec .= "1 HUSB @$pid@\r\n";
			else $famrec .= "1 WIFE @$pid@\r\n";
			$famrec .= "1 CHIL @$cxref@\r\n";
			$newfamid = append_gedrec($famrec);
			
			//-- add the new family to the new child
			$childrec = find_record_in_file($cxref);
			$childrec .= "\r\n1 FAMC @$newfamid@\r\n";
			replace_gedrec($cxref, $childrec);
			
			//-- add the new family to the original person
			$gedrec .= "\r\n1 FAMS @$newfamid@";
			$updated = true;
		}
		else {
			$famrec .= "\r\n1 CHIL @$cxref@\r\n";
			
			//-- add the family to the new child
			$childrec = find_record_in_file($cxref);
			$childrec .= "\r\n1 FAMC @$newfamid@\r\n";
			replace_gedrec($cxref, $childrec);
		}
		print $pgv_lang["update_successful"]."<br />\n";;
	}
	if (!empty($newfamid)) {
		$famrec = preg_replace("/0 @(.*)@/", "0 @".$newfamid."@", $famrec);
		replace_gedrec($newfamid, $famrec);
	}
	
	//------------------------------------------- updates for family with parents
	$cfams = find_families_in_record($gedrec, "FAMC");
	if (count($cfams)==0) $cfams[] = "";
	$i++;
	for($j=1; $j<=count($cfams); $j++) {
		$famid = $cfams[$j-1];
//		print $famid;
		$famupdate = false;
		if (!empty($famid)) {
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
			else $famrec = find_record_in_file($famid);
			$oldfamrec = $famrec;
		}
		else {
			$famrec = "0 @REF@ FAM\r\n1 CHIL @$pid@";
			$oldfamrec = "";
		}
		
		if (empty($FATHER[$i])) {
			//-- update the parents
			$sgivn = "FGIVN$i";
			$ssurn = "FSURN$i";
			//--add new spouse name, birth
			if (!empty($$sgivn) || !empty($$ssurn)) {
				//-- first add the new spouse
				$spouserec = "0 @REF@ INDI\r\n";
				$spouserec .= "1 NAME ".$$sgivn." /".$$ssurn."/\r\n";
				if (!empty($$sgivn)) $spouserec .= "2 GIVN ".$$sgivn."\r\n";
				if (!empty($$ssurn)) $spouserec .= "2 SURN ".$$ssurn."\r\n";
				$hsgivn = "HFGIVN$i";
				$hssurn = "HFSURN$i";
				if (!empty($$hsgivn) || !empty($$hssurn)) {
					$spouserec .= "2 _HEB ".$$hsgivn." /".$$hssurn."/\r\n";
				}
				$rsgivn = "RFGIVN$i";
				$rssurn = "RFSURN$i";
				if (!empty($$rsgivn) || !empty($$rssurn)) {
					$spouserec .= "2 ROMN ".$$rsgivn." /".$$rssurn."/\r\n";
				}
				$ssex = "FSEX$i";
				if (!empty($$ssex)) $spouserec .= "1 SEX ".$$ssex."\r\n";
				$bdate = "FBDATE$i";
				$bplac = "FBPLAC$i";
				if (!empty($$bdate)||!empty($$bplac)) {
					$spouserec .= "1 BIRT\r\n";
					if (!empty($$bdate)) $bdate = $$bdate;
					else $bdate = "";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\r\n";
					if (!empty($$bplac)) $spouserec .= "2 PLAC ".$$bplac."\r\n";
					$bresn = "FBRESN$i";
					if (!empty($$bresn)) $spouserec .= "2 RESN ".$$bresn."\r\n";
				}
				$bdate = "FDDATE$i";
				$bplac = "FDPLAC$i";
				if (!empty($$bdate)||!empty($$bplac)) {
					$spouserec .= "1 DEAT\r\n";
					if (!empty($$bdate)) $bdate = $$bdate;
					else $bdate = "";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\r\n";
					if (!empty($$bplac)) $spouserec .= "2 PLAC ".$$bplac."\r\n";
					$bresn = "FDRESN$i";
					if (!empty($$bresn)) $spouserec .= "2 RESN ".$$bresn."\r\n";
				}
				if (empty($famid)) {
					//print "HERE 1";
					$famid = append_gedrec($famrec);
					//print "<pre>$famrec</pre>";
					$gedrec .= "\r\n1 FAMC @$famid@\r\n";
					$updated = true;
				}
				$spouserec .= "\r\n1 FAMS @$famid@\r\n";
				$FATHER[$i] = append_gedrec($spouserec);
			}
		}
		else {
			if (empty($famid)) {
				//print "HERE 2";
				$famid = append_gedrec($famrec);
				$gedrec .= "\r\n1 FAMC @$famid@\r\n";
				$updated = true;
			}
			if (empty($oldfamrec)) {
				$spouserec = find_record_in_file($FATHER[$i]);
				$spouserec .= "\r\n1 FAMS @$famid@";
				replace_gedrec($FATHER[$i], $spouserec);
			}
		}
		
		$parents = find_parents_in_record($famrec);
		if (!empty($FATHER[$i]) && $parents['HUSB']!=$FATHER[$i]) {
			if (strstr($famrec, "1 HUSB")!==false) $famrec = preg_replace("/1 HUSB @.*@/", "1 HUSB @$FATHER[$i]@", $famrec);
			else $famrec .= "\r\n1 HUSB @$FATHER[$i]@";
			$famupdate = true;
//			print "famupdate1";
		}
		
		if (empty($MOTHER[$i])) {
			//-- update the parents
			$sgivn = "MGIVN$i";
			$ssurn = "MSURN$i";
			//--add new spouse name, birth
			if (!empty($$sgivn) || !empty($$ssurn)) {
				//-- first add the new spouse
				$spouserec = "0 @REF@ INDI\r\n";
				$spouserec .= "1 NAME ".$$sgivn." /".$$ssurn."/\r\n";
				if (!empty($$sgivn)) $spouserec .= "2 GIVN ".$$sgivn."\r\n";
				if (!empty($$ssurn)) $spouserec .= "2 SURN ".$$ssurn."\r\n";
				$hsgivn = "HMGIVN$i";
				$hssurn = "HMSURN$i";
				if (!empty($$hsgivn) || !empty($$hssurn)) {
					$spouserec .= "2 _HEB ".$$hsgivn." /".$$hssurn."/\r\n";
				}
				$rsgivn = "RMGIVN$i";
				$rssurn = "RMSURN$i";
				if (!empty($$rsgivn) || !empty($$rssurn)) {
					$spouserec .= "2 ROMN ".$$rsgivn." /".$$rssurn."/\r\n";
				}
				$ssex = "MSEX$i";
				if (!empty($$ssex)) $spouserec .= "1 SEX ".$$ssex."\r\n";
				$bdate = "MBDATE$i";
				$bplac = "MBPLAC$i";
				if (!empty($$bdate)||!empty($$bplac)) {
					$spouserec .= "1 BIRT\r\n";
					if (!empty($$bdate)) $bdate = $$bdate;
					else $bdate = "";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\r\n";
					if (!empty($$bplac)) $spouserec .= "2 PLAC ".$$bplac."\r\n";
					$bresn = "MBRESN$i";
					if (!empty($$bresn)) $spouserec .= "2 RESN ".$$bresn."\r\n";
				}
				$bdate = "MDDATE$i";
				$bplac = "MDPLAC$i";
				if (!empty($$bdate)||!empty($$bplac)) {
					$spouserec .= "1 DEAT\r\n";
					if (!empty($$bdate)) $bdate = $$bdate;
					else $bdate = "";
					$bdate = check_input_date($bdate);
					if (!empty($bdate)) $spouserec .= "2 DATE $bdate\r\n";
					if (!empty($$bplac)) $spouserec .= "2 PLAC ".$$bplac."\r\n";
					$bresn = "MDRESN$i";
					if (!empty($$bresn)) $spouserec .= "2 RESN ".$$bresn."\r\n";
				}
				if (empty($famid)) {
					//print "HERE 3";
					$famid = append_gedrec($famrec);
					$gedrec .= "\r\n1 FAMC @$famid@\r\n";
					$updated = true;
				}
				$spouserec .= "\r\n1 FAMS @$famid@\r\n";
				$MOTHER[$i] = append_gedrec($spouserec);
			}
		}
		else {
			if (empty($famid)) {
// 				print "HERE 4";
				$famid = append_gedrec($famrec);
				$gedrec .= "\r\n1 FAMC @$famid@\r\n";
				$updated = true;
			}
			if (empty($oldfamrec)) {
				$spouserec = find_record_in_file($MOTHER[$i]);
				$spouserec .= "\r\n1 FAMS @$famid@";
				replace_gedrec($MOTHER[$i], $spouserec);
			}
		}
		if (!empty($MOTHER[$i]) && $parents['WIFE']!=$MOTHER[$i]) {
			if (strstr($famrec, "1 WIFE")!==false) $famrec = preg_replace("/1 WIFE @.*@/", "1 WIFE @$MOTHER[$i]@", $famrec);
			else $famrec .= "\r\n1 WIFE @$MOTHER[$i]@";
			$famupdate = true;
//			print "famupdate2";
		}
		
		//-- check for updated facts
		$var = "F".$i."TAGS";
		if (!empty($$var)) $TAGS = $$var;
		else $TAGS = array();
		if (count($TAGS)>0) {
			$famupdate |= check_updated_facts($i, $famrec, $TAGS, "F");
		}
		
		//-- check for new fact
		$var = "F".$i."newfact";
		$newfact = "";
		$newfact = $$var;
		if (!empty($newfact)) {
			if (empty($famid)) {
				//print "HERE 6";
				$famid = append_gedrec($famrec);
				$gedrec .= "\r\n1 FAMC @$famid@\r\n";
				$updated = true;
			}
			if (!in_array($newfact, $typefacts)) $factrec = "1 $newfact\r\n";
			else $factrec = "1 EVEN\r\n2 TYPE $newfact\r\n";
			$var = "F".$i."DATE";
			if (!empty($$var)) $FDATE = $$var;
			else $FDATE = "";
			$FDATE = check_input_date($FDATE);
			if (!empty($FDATE)) $factrec .= "2 DATE $FDATE\r\n";
			$var = "F".$i."PLAC";
			if (!empty($$var)) $FPLAC = $$var;
			else $FPLAC = "";
			if (!empty($FPLAC)) $factrec .= "2 PLAC $FPLAC\r\n";
			$var = "F".$i."TEMP";
			if (!empty($$var)) $FTEMP = $$var;
			else $FTEMP = "";
			if (!empty($FTEMP)) $factrec .= "2 TEMP $FTEMP\r\n";
			$var = "F".$i."RESN";
			if (!empty($$var)) $FRESN = $$var;
			else $FRESN;
			if (!empty($FRESN)) $factrec .= "2 RESN $FRESN\r\n";
			//-- make sure that there is at least a Y
			if (preg_match("/\n2 \w*/", $factrec)==0) $factrec = "1 $newfact Y\r\n";
			$famrec .= "\r\n".$factrec;
			$famupdate = true;
//			print "famupdate5";
		}
		
		if (!empty($CHIL[$i])) {
			if (empty($famid)) {
				//print "HERE 7";
				$famid = append_gedrec($famrec);
				$gedrec .= "\r\n1 FAMC @$famid@\r\n";
				$updated = true;
			}
			$famrec .= "\r\n1 CHIL @".$CHIL[$i]."@";
			$childrec = find_record_in_file($CHIL[$i]);
			if (preg_match("/1 FAMC @$famid@/", $childrec)==0) {
				$childrec = "\r\n1 FAMC @$famid@";
				replace_gedrec($CHIL[$i], $childrec);
			}
			$famupdate = true;
//			print "famupdate6";
		}
		
		$var = "F".$i."CDEL";
		if (!empty($$var)) $fcdel = $$var;
		else $fcdel = "";
		if (!empty($fcdel)) {
			$famrec = preg_replace("/1 CHIL @$fcdel@/", "", $famrec);
			$famupdate = true;
//			print "famupdate7";
		}
		
		//--add new child, name, birth
		$cgivn = "C".$i."GIVN";
		$csurn = "C".$i."SURN";
		if (!empty($$cgivn) || !empty($$csurn)) {
			if (empty($famid)) {
				//print "HERE 8";
				$famid = append_gedrec($famrec);
				$gedrec .= "\r\n1 FAMC @$famid@\r\n";
				$updated = true;
			}
			//-- first add the new child
			$childrec = "0 @REF@ INDI\r\n";
			$childrec .= "1 NAME ".$$cgivn." /".$$csurn."/\r\n";
			if (!empty($$cgivn)) $childrec .= "2 GIVN ".$$cgivn."\r\n";
			if (!empty($$csurn)) $childrec .= "2 SURN ".$$csurn."\r\n";
			$hsgivn = "HC{$i}GIVN";
			$hssurn = "HC{$i}SURN";
			if (!empty($$hsgivn) || !empty($$hssurn)) {
				$childrec .= "2 _HEB ".$$hsgivn." /".$$hssurn."/\r\n";
			}
			$rsgivn = "RC{$i}GIVN";
			$rssurn = "RC{$i}SURN";
			if (!empty($$rsgivn) || !empty($$rssurn)) {
				$childrec .= "2 ROMN ".$$rsgivn." /".$$rssurn."/\r\n";
			}
			$var = "C".$i."SEX";
			if (!empty($$var)) $csex = $$var;
			else $csex = "";
			if (!empty($csex)) $childrec .= "1 SEX $csex\r\n";
			$var = "C".$i."DATE";
			if (!empty($$var)) $cdate = $$var;
			else $cdate = "";
			$var = "C".$i."PLAC";
			if (!empty($$var)) $cplac = $$var;
			else $cplac = "";
			if (!empty($cdate)||!empty($cplac)) {
				$childrec .= "1 BIRT\r\n";
				$cdate = check_input_date($cdate);
				if (!empty($cdate)) $childrec .= "2 DATE $cdate\r\n";
				if (!empty($cplac)) $childrec .= "2 PLAC $cplac\r\n";
				$var = "C".$i."RESN";
				if (!empty($$var)) $cresn = $$var;
				else $cresn = "";
				if (!empty($cresn)) $childrec .= "2 RESN $cresn\r\n";
			}
			$childrec .= "1 FAMC @$famid@\r\n";
			$cxref = append_gedrec($childrec);
			$famrec .= "\r\n1 CHIL @$cxref@";
			$famupdate = true;
//			print "famupdate8";
		}
		if ($famupdate &&($oldfamrec!=$famrec)) {
			$famrec = preg_replace("/0 @(.*)@/", "0 @".$famid."@", $famrec);
//			print $famrec;
			replace_gedrec($famid, $famrec);
		}
		$i++;
	}

	if ($updated && empty($error)) {
		print $pgv_lang["update_successful"]."<br />";
		AddToChangeLog("Quick update for $pid by >".getUserName()."<");
		//print "<pre>$gedrec</pre>";
		if ($oldgedrec!=$gedrec) replace_gedrec($pid, $gedrec);
	}
	if (!empty($error)) {
		print "<span class=\"error\">".$error."</span>";
	}

	if ($closewin) {
		// autoclose window when update successful
		if ($EDIT_AUTOCLOSE and !$GLOBALS["DEBUG"]) print "\n<script type=\"text/javascript\">\n<!--\nif (window.opener.showchanges) window.opener.showchanges(); window.close();\n//-->\n</script>";
		
		print "<center><br /><br /><br />";
		print "<a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
		print_simple_footer();
		exit;
	}
}

if ($action!="update") print "<h2>".$pgv_lang["quick_update_title"]."</h2>\n";
print $pgv_lang["quick_update_instructions"]."<br /><br />";

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
	<form method="post" action="edit_quickupdate.php?pid=<?php print $pid;?>" name="quickupdate" enctype="multipart/form-data">
	<input type="hidden" name="action" value="" />
	<table>
	<tr>
		<td><?php print $pgv_lang["enter_pid"]; ?></td>
		<td><input type="text" size="6" name="pid" id="pid" />
		<?php print_findindi_link("pid","");?>
                </td>
	</tr>
	</table>
	<input type="submit" value="<?php print $pgv_lang["continue"]; ?>" />
	</form>
		<?php
	}
	else {
		$SEX = get_gedcom_value("SEX", 1, $gedrec, '', false);
		$child_surname = "";
		//if ($SEX=="M") {
		//	$ct = preg_match("~1 NAME.*/(.*)/~", $gedrec, $match);
		//	if ($ct>0) $child_surname = $match[1];
		//}
	$GIVN = "";
	$SURN = "";
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
		if ($ct>0) $SURN = trim($match[1]);
		else {
			$ct = preg_match("/1 NAME (.*)/", $subrec, $match);
			if ($ct>0) {
				$st = preg_match("~/(.*)/~", $match[1], $smatch);
				if ($st>0) $SURN = $smatch[1];
			}
		}
		$HGIVN = "";
		$HSURN = "";
		$RGIVN = "";
		$RSURN = "";
		if ($USE_RTL_FUNCTIONS) {
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
	}
	$ADDR = "";
	$subrec = get_sub_record(1, "1 ADDR", $gedrec);
	if (!empty($subrec)) {
		$ct = preg_match("/1 ADDR (.*)/", $subrec, $match);
		if ($ct>0) $ADDR = trim($match[1]);
		$ADDR_CONT = get_cont(2, $subrec);
		if (!empty($ADDR_CONT)) $ADDR .= $ADDR_CONT;
		else {
			$_NAME = get_gedcom_value("_NAME", 2, $subrec);
			if (!empty($_NAME)) $ADDR .= "\r\n". check_NN($_NAME);
			$ADR1 = get_gedcom_value("ADR1", 2, $subrec);
			if (!empty($ADR1)) $ADDR .= "\r\n". $ADR1;
			$ADR2 = get_gedcom_value("ADR2", 2, $subrec);
			if (!empty($ADR2)) $ADDR .= "\r\n". $ADR2;
			$cityspace = "\r\n";
			if (!$POSTAL_CODE) {
				$POST = get_gedcom_value("POST", 2, $subrec);
				if (!empty($POST)) $ADDR .= "\r\n". $POST;
				else $ADDR .= "\r\n";
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
			if (!empty($CTRY)) $ADDR .= "\r\n". $CTRY;
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
	$subrecords = get_all_subrecords($gedrec, "ADDR,PHON,FAX,EMAIL,_EMAIL,NAME,FAMS,FAMC,_UID", false, false, false);
	$repeat_tags = array();
	foreach($subrecords as $ind=>$subrec) {
		$ft = preg_match("/1 (\w+)(.*)/", $subrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			$event = trim($match[2]);
		}
		else {
			$fact="";
			$event="";
		}
		if ($fact=="EVEN" || $fact=="FACT") $fact = get_gedcom_value("TYPE", 2, $subrec, '', false);
		if (in_array($fact, $addfacts)) {
			if (!isset($repeat_tags[$fact])) $repeat_tags[$fact]=1;
			else $repeat_tags[$fact]++;
			$newreqd = array();
			foreach($reqdfacts as $r=>$rfact) {
				if ($rfact!=$fact) $newreqd[] = $rfact;
			}
			$reqdfacts = $newreqd;
			$indifacts[] = array($fact, $subrec, false, $repeat_tags[$fact]);
		}
	}
	foreach($reqdfacts as $ind=>$fact) {
		$indifacts[] = array($fact, "1 $fact\r\n", true, 0);
	}
	usort($indifacts, "compare_facts");
	$sfams = find_families_in_record($gedrec, "FAMS");
	$cfams = find_families_in_record($gedrec, "FAMC");
	if (count($cfams)==0) $cfams[] = "";
		
	$tabkey = 1;
	$name = PrintReady(get_person_name($pid));
	print "<b>".$name;
	if ($SHOW_ID_NUMBERS) print "&nbsp;&nbsp;(".$pid.")";
	print "</b><br />";
?>
<script language="JavaScript" type="text/javascript">
<!--
var tab_count = <?php print (count($sfams)+count($cfams)); ?>;
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
			alert("<?php print $pgv_lang["enter_email"]; ?>");
			frm.EMAIL.focus();
			return false;
		} 
	}
	return true;
}
//-->
</script>
<form method="post" action="edit_quickupdate.php?pid=<?php print $pid;?>" name="quickupdate" enctype="multipart/form-data" onsubmit="return checkform(this);">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="closewin" value="1" />
<br /><input type="submit" value="<?php print $pgv_lang["save"]; ?>" /><br /><br />
<table class="tabs_table">
   <tr>
		<td id="pagetab0" class="tab_cell_active"><a href="javascript: <?php print $pgv_lang["personal_facts"];?>" onclick="switch_tab(0); return false;"><?php print $pgv_lang["personal_facts"]?></a></td>
		<?php
		for($i=1; $i<=count($sfams); $i++) {
			$famid = $sfams[$i-1];
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
			else $famrec = find_record_in_file($famid);
			$parents = find_parents_in_record($famrec);
			$spid = "";
			if($parents) {
				if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
				else $spid=$parents["WIFE"];
			}			
			print "<td id=\"pagetab$i\" class=\"tab_cell_inactive\" onclick=\"switch_tab($i); return false;\"><a href=\"javascript: ".$pgv_lang["family_with"]."&nbsp;";
			if (!empty($spid)) {
				if (displayDetailsById($spid) && showLivingNameById($spid)) {
					print PrintReady(str_replace(array("<span class=\"starredname\">", "</span>"), "", get_person_name($spid)));
					print "\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["family_with"]." ";
					print PrintReady(get_person_name($spid));
				}
				else {
					print $pgv_lang["private"];
					print "\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["family_with"]." ";
					print $pgv_lang["private"];
				}
			}
			else print "\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["family_with"]." ".$pgv_lang["unknown"];
			print "</a></td>\n";
		}
		?>
		<td id="pagetab<?php echo $i; ?>" class="tab_cell_inactive" onclick="switch_tab(<?php echo $i; ?>); return false;"><a href="javascript: <?php print $pgv_lang["add_new_wife"];?>" onclick="switch_tab(<?php echo $i; ?>); return false;">
		<?php if (preg_match("/1 SEX M/", $gedrec)>0) print $pgv_lang["add_new_wife"]; else print $pgv_lang["add_new_husb"]; ?></a></td>
		<?php
		$i++;
		for($j=1; $j<=count($cfams); $j++) {
			print "<td id=\"pagetab$i\" class=\"tab_cell_inactive\" onclick=\"switch_tab($i); return false;\"><a href=\"#\" onclick=\"switch_tab($i); return false;\">".$pgv_lang["as_child"];
			print "</a></td>\n";
			$i++;
		}
		?>
		</tr>
		<tr>
		  <td id="pagetab0bottom" class="tab_active_bottom"><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]; ?>" width="1" height="1" alt="" /></td>
		  <?php
		for($i=1; $i<=count($sfams); $i++) {
			print "<td id=\"pagetab{$i}bottom\" class=\"tab_inactive_bottom\"><img src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["spacer"]["other"]."\" width=\"1\" height=\"1\" alt=\"\"/></td>\n";
		}
		for($j=1; $j<=count($cfams); $j++) {
			print "<td id=\"pagetab{$i}bottom\" class=\"tab_inactive_bottom\"><img src=\"$PGV_IMAGE_DIR/".$PGV_IMAGES["spacer"]["other"]."\" width=\"1\" height=\"1\" alt=\"\" /></td>\n";
			$i++;
		}
		?>
			<td id="pagetab<?php echo $i; ?>bottom" class="tab_inactive_bottom"><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]; ?>" width="1" height="1" alt="" /></td>
			<td class="tab_inactive_bottom_right" style="width:10%;"><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]; ?>" width="1" height="1" alt="" /></td>
   </tr>
</table>
<div id="tab0">
<table class="<?php print $TEXT_DIRECTION; ?> width80">
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_name_help", "qm"); ?><?php print $pgv_lang["update_name"]; ?></td></tr>
<tr><td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="GIVN" value="<?php print PrintReady(htmlspecialchars($GIVN)); ?>" /></td></tr>
<?php $tabkey++; ?>
<tr><td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="SURN" value="<?php print PrintReady(htmlspecialchars($SURN)); ?>" /></td></tr>
<?php $tabkey++; ?>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr><td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HGIVN" value="<?php print PrintReady(htmlspecialchars($HGIVN)); ?>" /></td></tr>
<?php $tabkey++; ?>
<tr><td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HSURN" value="<?php print PrintReady(htmlspecialchars($HSURN)); ?>" /></td></tr>
<?php $tabkey++; ?>
</tr>
<tr><td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RGIVN" value="<?php print PrintReady(htmlspecialchars($RGIVN)); ?>" /></td></tr>
<?php $tabkey++; ?>
<tr><td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RSURN" value="<?php print PrintReady(htmlspecialchars($RSURN)); ?>" /></td></tr>
<?php $tabkey++; ?>
</tr>
<?php } ?>

<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="SEX" tabindex="<?php print $tabkey; ?>">
			<option value="M"<?php if ($SEX=="M") print " selected=\"selected\""; ?>><?php print $pgv_lang["male"]; ?></option>
			<option value="F"<?php if ($SEX=="F") print " selected=\"selected\""; ?>><?php print $pgv_lang["female"]; ?></option>
			<option value="U"<?php if ($SEX=="U") print " selected=\"selected\""; ?>><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php
// NOTE: Update fact
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); print $pgv_lang["update_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print $factarray["PLAC"]; ?></td>
	<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
</tr>
<?php
foreach($indifacts as $f=>$fact) {
	$fact_tag = $fact[0];
	$fact_num = $fact[3];
	$date = get_gedcom_value("DATE", 2, $fact[1], '', false);
	$plac = get_gedcom_value("PLAC", 2, $fact[1], '', false);
	$temp = get_gedcom_value("TEMP", 2, $fact[1], '', false);
	$desc = get_gedcom_value($fact_tag, 1, $fact[1], '', false);
	?>
<tr>
	<td class="descriptionbox">
		<?php if (isset($factarray[$fact_tag])) print $factarray[$fact_tag]; 
		else if (isset($pgv_lang[$fact_tag])) print $pgv_lang[$fact_tag]; 
		else print $fact_tag;
		?>		
		<input type="hidden" name="TAGS[]" value="<?php echo $fact_tag; ?>" />
		<input type="hidden" name="NUMS[]" value="<?php echo $fact_num; ?>" />
	</td>
	<?php if (!in_array($fact_tag, $emptyfacts)) { ?>
	<td class="optionbox" colspan="2">
		<input type="text" name="DESCS[]" size="40" value="<?php print htmlspecialchars($desc); ?>" />
		<input type="hidden" name="DATES[]" value="<?php print htmlspecialchars($date); ?>" />
		<input type="hidden" name="PLACS[]" value="<?php print htmlspecialchars($plac); ?>" />
		<input type="hidden" name="TEMPS[]" value="<?php print htmlspecialchars($temp); ?>" />
	</td>
	<?php }	else {
		if (!in_array($fact_tag, $nondatefacts)) { ?>
			<td class="optionbox">
				<input type="hidden" name="DESCS[]" value="<?php print htmlspecialchars($desc); ?>" />
				<input type="text" tabindex="<?php print $tabkey; $tabkey++;?>" size="15" name="DATES[]" id="DATE<?php echo $f; ?>" onblur="valid_date(this);" value="<?php echo htmlspecialchars($date); ?>" />&nbsp;<?php print_calendar_popup("DATE$f");?>
			</td>
		<?php }
		if (empty($temp) && (!in_array($fact_tag, $nonplacfacts))) { ?>
			<td class="optionbox">
				<input type="text" size="30" tabindex="<?php print $tabkey; $tabkey++; ?>" name="PLACS[]" id="place<?php echo $f; ?>" value="<?php print PrintReady(htmlspecialchars($plac)); ?>" />
				<?php print_findplace_link("place$f"); ?>
				<input type="hidden" name="TEMPS[]" value="" />
			</td>
		<?php
		}
		else {
			print "<td class=\"optionbox\"><select tabindex=\"".$tabkey."\" name=\"TEMPS[]\" >\n";
			print "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
			foreach($TEMPLE_CODES as $code=>$temple) {
				print "<option value=\"$code\"";
				if ($code==$temp) print " selected=\"selected\"";
				print ">$temple</option>\n";
			}
			print "</select>\n";
			print "<input type=\"hidden\" name=\"PLACS[]\" value=\"\" />\n";
			print "</td>\n";
			$tabkey++;
		}
	}
	if (!$fact[2]) { ?>
		<td class="optionbox center">
			<input type="hidden" name="REMS[<?php echo $f; ?>]" id="REM<?php echo $f; ?>" value="0" />
			<a href="javascript: <?php print $pgv_lang["delete"]; ?>" onclick="document.quickupdate.closewin.value='0'; document.quickupdate.REM<?php echo $f; ?>.value='1'; document.quickupdate.submit(); return false;">
				<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php print $pgv_lang["delete"]; ?>" />
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
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); print $pgv_lang["add_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"]; ?></td>
	<td class="descriptionbox">&nbsp;</td>
	</tr>
<tr><td class="optionbox">
	<script language="JavaScript" type="text/javascript">
	<!--
	function checkDesc(newfactSelect) {
		if (newfactSelect.selectedIndex==0) return;
		var fact = newfactSelect.options[newfactSelect.selectedIndex].value;
		var emptyfacts = "<?php foreach($emptyfacts as $ind=>$efact) print $efact.","; ?>";
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
	<select name="newfact" tabindex="<?php print $tabkey; ?>" onchange="checkDesc(this);">
		<option value=""><?php print $pgv_lang["select_fact"]; ?></option>
	<?php $tabkey++; ?>
	<?php
	foreach($addfacts as $indexval => $fact) {
		$found = false;
		foreach($indifacts as $ind=>$value) {
			if ($fact==$value[0]) {
				$found=true;
				break;
			}
		}
		if (!$found) print "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
	}
	?>
		</select>
		<div id="descFact" style="display:none;"><br />
			<?php print $pgv_lang["description"]; ?><input type="text" size="35" name="DESC" />
		</div>
	</td>
	<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="DATE" id="DATE" onblur="valid_date(this);" />&nbsp;<?php print_calendar_popup("DATE");?></td>
	<?php $tabkey++; ?>
	<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; ?>" name="PLAC" id="place" />
	<?php print_findplace_link("place"); ?>
	</td>
	<td class="optionbox">&nbsp;</td></tr>
	<?php $tabkey++; ?>
	<?php print_quick_resn("RESN"); ?>
<?php }

// NOTE: Add photo
if ($MULTI_MEDIA && (is_writable($MEDIA_DIRECTORY))) { ?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><b><?php print_help_link("quick_update_photo_help", "qm"); print $pgv_lang["update_photo"]; ?></b></td></tr>
<tr>
	<td class="descriptionbox">
		<?php print $factarray["TITL"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<input type="text" tabindex="<?php print $tabkey; ?>" name="TITL" size="40" />
	</td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox">
		<?php print $factarray["FILE"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<input type="file" tabindex="<?php print $tabkey; ?>" name="FILE" size="40" />
	</td>
	<?php $tabkey++; ?>
</tr>
<?php if (preg_match("/1 OBJE/", $gedrec)>0) { ?>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="optionbox" colspan="3">
		<input type="checkbox" tabindex="<?php print $tabkey; ?>" name="replace" value="yes" /> <?php print $pgv_lang["photo_replace"]; ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<?php }

// Address update
if (!is_dead_id($pid) || !empty($ADDR) || !empty($PHON) || !empty($FAX) || !empty($EMAIL)) { //-- don't show address for dead people 
	 ?>
<tr><td>&nbsp;</td></tr> 
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_address_help", "qm"); print $pgv_lang["update_address"]; ?></td></tr>
<tr>
	<td class="descriptionbox">
		<?php print $factarray["ADDR"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<?php if (!empty($CITY)&&!empty($POST)) { ?>
			<?php if (!empty($_NAME)) { ?><?php print $factarray["NAME"]; ?><input type="text" name="_NAME" size="35" value="<?php print PrintReady(strip_tags($_NAME)); ?>" /><br /><?php } ?>
			<?php print $factarray["ADR1"]; ?><input type="text" name="ADR1" size="35" value="<?php print PrintReady(strip_tags($ADR1)); ?>" /><br />
			<?php print $factarray["ADR2"]; ?><input type="text" name="ADR2" size="35" value="<?php print PrintReady(strip_tags($ADR2)); ?>" /><br />
			<?php print $factarray["CITY"]; ?><input type="text" name="CITY" value="<?php print PrintReady(strip_tags($CITY)); ?>" />
			<?php print $factarray["STAE"]; ?><input type="text" name="STAE" value="<?php print PrintReady(strip_tags($STAE)); ?>" /><br />
			<?php print $factarray["POST"]; ?><input type="text" name="POST" value="<?php print PrintReady(strip_tags($POST)); ?>" /><br />
			<?php print $factarray["CTRY"]; ?><input type="text" name="CTRY" value="<?php print PrintReady(strip_tags($CTRY)); ?>" />
			<input type="hidden" name="ADDR" value="<?php print PrintReady(strip_tags($ADDR)); ?>" />
		<?php } else { ?>
		<textarea name="ADDR" tabindex="<?php print $tabkey; ?>" cols="35" rows="4"><?php print PrintReady(strip_tags($ADDR)); ?></textarea>
		<?php } ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox">
		<?php print $factarray["PHON"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<input type="text" tabindex="<?php print $tabkey; $tabkey++; ?>" name="PHON" size="20" value="<?php print PrintReady($PHON); ?>" />
	</td>
</tr>
<tr>
		<td class="descriptionbox">
				<?php print $factarray["FAX"]; ?>
		</td>
		<td class="optionbox" colspan="3">
				<input type="text" tabindex="<?php print $tabkey; $tabkey++; ?>" name="FAX" size="20" value="<?php print PrintReady($FAX); ?>" />
	</td>
</tr>
<tr>
	<td class="descriptionbox">
		<?php print $factarray["EMAIL"]; ?>
	</td>
	<td class="optionbox" colspan="3">
		<input type="text" tabindex="<?php print $tabkey; ?>" name="EMAIL" size="40" value="<?php print PrintReady($EMAIL); ?>" />
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
<table class="<?php print $TEXT_DIRECTION; ?> width80">
<tr><td class="topbottombar" colspan="4">
<?php
	$famreqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FAMFACTS);
	$famid = $sfams[$i-1];
	if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
	else $famrec = find_record_in_file($famid);
	print $pgv_lang["family_with"]." ";
	$parents = find_parents_in_record($famrec);
	$spid = "";
	if($parents) {
		if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
		else $spid=$parents["WIFE"];
	}
	if (!empty($spid)) {
		if (displayDetailsById($spid) && showLivingNameById($spid)) {
			print "<a href=\"#\" onclick=\"return quickEdit('".$spid."');\">";
			$name = PrintReady(get_person_name($spid));
			if ($SHOW_ID_NUMBERS) $name .= " (".$spid.")";
			$name .= " [".$pgv_lang["edit"]."]";
			print $name."</a>\n";
		}
		else print $pgv_lang["private"];
	}
	else print $pgv_lang["unknown"];
	$subrecords = get_all_subrecords($famrec, "HUSB,WIFE,CHIL", false, false, false);
	$famfacts = array();
	foreach($subrecords as $ind=>$subrec) {
		$ft = preg_match("/1 (\w+)(.*)/", $subrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			$event = trim($match[2]);
		}
		else {
			$fact="";
			$event="";
		}
		if ($fact=="EVEN" || $fact=="FACT") $fact = get_gedcom_value("TYPE", 2, $subrec, '', false);
		if (in_array($fact, $famaddfacts)) {
			$newreqd = array();
			foreach($famreqdfacts as $r=>$rfact) {
				if ($rfact!=$fact) $newreqd[] = $rfact;
			}
			$famreqdfacts = $newreqd;
			$famfacts[] = array($fact, $subrec, 0);
		}
	}
	foreach($famreqdfacts as $ind=>$fact) {
		$famfacts[] = array($fact, "1 $fact\r\n", 1);
	}
	usort($famfacts, "compare_facts");
?>
</td></tr>
<tr>
	<td class="descriptionbox"><?php print $pgv_lang["enter_pid"]; ?></td>
	<td class="optionbox" colspan="3"><input type="text" size="10" name="SPID[<?php echo $i; ?>]" id="SPID<?php echo $i; ?>" value="<?php echo $spid; ?>" />
		<?php print_findindi_link("SPID$i","");?>
     </td>
	</tr>
<?php if (empty($spid)) { ?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); if (preg_match("/1 SEX M/", $gedrec)>0) print $pgv_lang["add_new_wife"]; else print $pgv_lang["add_new_husb"];?></td></tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="SGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="SSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HSGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HSSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RSGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RSSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="SSEX<?php echo $i; ?>" tabindex="<?php print $tabkey; ?>">
			<option value="M"<?php if (preg_match("/1 SEX F/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["male"]; ?></option>
			<option value="F"<?php if (preg_match("/1 SEX M/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["female"]; ?></option>
			<option value="U"<?php if (preg_match("/1 SEX U/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?><?php print $factarray["DATE"];?></td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="BDATE<?php echo $i; ?>" id="BDATE<?php echo $i; ?>" onblur="valid_date(this);" /><?php print_calendar_popup("BDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="BPLAC<?php echo $i; ?>" id="bplace<?php echo $i; ?>" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="banchor1x" id="banchor1x" alt="" />
	<?php print_findplace_link("place$f"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("BRESN".$i); 
}
//NOTE: Update fact
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); print $pgv_lang["update_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print $factarray["PLAC"]; ?></td>
	<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
	</tr>
<?php
foreach($famfacts as $f=>$fact) {
	$fact_tag = $fact[0];
	$date = get_gedcom_value("DATE", 2, $fact[1], '', false);
	$plac = get_gedcom_value("PLAC", 2, $fact[1], '', false);
	$temp = get_gedcom_value("TEMP", 2, $fact[1], '', false);
	?>
			<tr>
				<td class="descriptionbox">
				<?php if (isset($factarray[$fact_tag])) print $factarray[$fact_tag]; 
					else if (isset($pgv_lang[$fact_tag])) print $pgv_lang[$fact_tag]; 
					else print $fact_tag;
				?>
					<input type="hidden" name="F<?php echo $i; ?>TAGS[]" value="<?php echo $fact_tag; ?>" />
				</td>
				<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; $tabkey++;?>" size="15" name="F<?php echo $i; ?>DATES[]" id="F<?php echo $i; ?>DATE<?php echo $f; ?>" onblur="valid_date(this);" value="<?php echo htmlspecialchars($date); ?>" /><?php print_calendar_popup("F{$i}DATE{$f}");?></td>
				<?php if (empty($temp) && (!in_array($fact_tag, $nonplacfacts))) { ?>
					<td class="optionbox"><input type="text" size="30" tabindex="<?php print $tabkey; $tabkey++; ?>" name="F<?php echo $i; ?>PLACS[]" id="F<?php echo $i; ?>place<?php echo $f; ?>" value="<?php print PrintReady(htmlspecialchars($plac)); ?>" />
                                        <?php print_findplace_link("F'.$i.'place$f"); ?>
                                        </td>
				<?php }
				else {
					print "<td class=\"optionbox\"><select tabindex=\"".$tabkey."\" name=\"F".$i."TEMP[]\" >\n";
					print "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
					foreach($TEMPLE_CODES as $code=>$temple) {
						print "<option value=\"$code\"";
						if ($code==$temp) print " selected=\"selected\"";
						print ">$temple</option>\n";
					}
					print "</select>\n</td>\n";
					$tabkey++;
				}
				?>
				<td class="optionbox center">
					<input type="hidden" name="F<?php echo $i; ?>REMS[<?php echo $f; ?>]" id="F<?php echo $i; ?>REM<?php echo $f; ?>" value="0" />
					<?php if (!$fact[2]) { ?>
					<a href="javascript: <?php print $pgv_lang["delete"]; ?>" onclick="document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>REM<?php echo $f; ?>.value='1'; document.quickupdate.submit(); return false;">
						<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php print $pgv_lang["delete"]; ?>" />
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
	<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); print $pgv_lang["add_fact"]; ?></td></tr>
	<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"]; ?></td>
	<td class="descriptionbox">&nbsp;</td>
	</tr>
	<tr>
	<td class="optionbox"><select name="F<?php echo $i; ?>newfact" tabindex="<?php print $tabkey; ?>">
		<option value=""><?php print $pgv_lang["select_fact"]; ?></option>
	<?php $tabkey++; ?>
	<?php
	foreach($famaddfacts as $indexval => $fact) {
		$found = false;
		foreach($famfacts as $ind=>$value) {
			if ($fact==$value[0]) {
				$found=true;
				break;
			}
		}
		if (!$found) print "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
	}
	?>
		</select>
	</td>
	<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="F<?php echo $i; ?>DATE" id="F<?php echo $i; ?>DATE" onblur="valid_date(this);" /><?php print_calendar_popup("F".$i."DATE");?></td>
	<?php $tabkey++; ?>
	<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; ?>" name="F<?php echo $i; ?>PLAC" id="F<?php echo $i; ?>place" />
	<?php print_findplace_link("F'.$i.'place"); ?>
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
		<td class="topbottombar" colspan="4"><?php print $pgv_lang["children"]; ?></td>
	</tr>
	<tr>
			<input type="hidden" name="F<?php echo $i; ?>CDEL" value="" />
					<td class="descriptionbox"><?php print $pgv_lang["name"]; ?></td>
					<td class="descriptionbox"><?php print $factarray["SEX"]; ?></td>
					<td class="descriptionbox"><?php print $factarray["BIRT"]; ?></td>
					<td class="descriptionbox"><?php print $pgv_lang["remove"]; ?></td>
	</tr>
			<?php
				foreach($chil as $c=>$child) {
					print "<tr><td class=\"optionbox\">";
					$name = get_person_name($child);
					$disp = displayDetailsById($child);
					if ($SHOW_ID_NUMBERS) $name .= " (".$child.")";
					$name .= " [".$pgv_lang["edit"]."]";
					if ($disp||showLivingNameById($child)) {
						print "<a href=\"#\" onclick=\"return quickEdit('".$child."');\">";
						print PrintReady($name);
						print "</a>";
					}
					else print $pgv_lang["private"];
					$childrec = find_person_record($child);
					print "</td>\n<td class=\"optionbox center\">";
					if ($disp) {
						print get_gedcom_value("SEX", 1, $childrec);
					}
					print "</td>\n<td class=\"optionbox\">";
					if ($disp) {
						$birtrec = get_sub_record(1, "BIRT", $childrec);
						if (!empty($birtrec)) {
							if (showFact("BIRT", $child) && !FactViewRestricted($child, $birtrec)) {
								print get_gedcom_value("DATE", 2, $birtrec);
								print " -- ";
								print get_gedcom_value("PLAC", 2, $birtrec);
							}
						}
					}
					print "</td>\n";
					?>
					<td class="optionbox center" colspan="3">
						<a href="javascript: <?php print $pgv_lang["remove_child"]; ?>" onclick="if (confirm('<?php print $pgv_lang["confirm_remove"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>CDEL.value='<?php echo $child; ?>'; document.quickupdate.submit(); } return false;">
							<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php print $pgv_lang["remove_child"]; ?>" />
						</a>
					</td>
					<?php
					print "</tr>\n";
				}
			?>
			<tr>
				<td class="descriptionbox"><?php print $pgv_lang["add_new_chil"]; ?></td>
				<td class="optionbox" colspan="3"><input type="text" size="10" name="CHIL[]" id="CHIL<?php echo $i; ?>" />
                                <?php print_findindi_link("CHIL$i","");?>
                                </td>
			</tr>
<?php 
// NOTE: Add a child
if (empty($child_surname)) $child_surname = "";
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><b><?php print_help_link("quick_update_child_help", "qm"); print $pgv_lang["add_new_chil"]; ?></b></td></tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="C<?php echo $i; ?>GIVN" /></td>
</tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="C<?php echo $i; ?>SURN" value="<?php if (!empty($child_surname)) print $child_surname; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HC<?php echo $i; ?>GIVN" /></td>
</tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HC<?php echo $i; ?>SURN" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RC<?php echo $i; ?>GIVN" /></td>
</tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RC<?php echo $i; ?>SURN" /></td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="C<?php echo $i; ?>SEX" tabindex="<?php print $tabkey; ?>">
			<option value="M"><?php print $pgv_lang["male"]; ?></option>
			<option value="F"><?php print $pgv_lang["female"]; ?></option>
			<option value="U"><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="C<?php echo $i; ?>DATE" id="C<?php echo $i; ?>DATE" onblur="valid_date(this);" /><?php print_calendar_popup("C{$i}DATE");?></td>
	<?php $tabkey++; ?>
	</tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="C<?php echo $i; ?>PLAC" id="c<?php echo $i; ?>place" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="canchor1x" id="canchor1x" alt="" />
	<?php print_findplace_link("c'.$i.'place"); ?>
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
<table class="<?php print $TEXT_DIRECTION;?> width80">
<?php
// NOTE: New wife
?>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); if (preg_match("/1 SEX M/", $gedrec)>0) print $pgv_lang["add_new_wife"]; else print $pgv_lang["add_new_husb"]; ?></td></tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="SGIVN" /></td></tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="SSURN" /></td>
	<?php $tabkey++; ?>
</tr>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HSGIVN" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HSSURN" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RSGIVN" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RSSURN" /></td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="SSEX" tabindex="<?php print $tabkey; ?>">
			<option value="M"<?php if (preg_match("/1 SEX F/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["male"]; ?></option>
			<option value="F"<?php if (preg_match("/1 SEX M/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["female"]; ?></option>
			<option value="U"<?php if (preg_match("/1 SEX U/", $gedrec)>0) print " selected=\"selected\""; ?>><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="BDATE" id="BDATE" onblur="valid_date(this);" /><?php print_calendar_popup("BDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="BPLAC" id="bplace" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="banchor1x" id="banchor1x" alt="" />
	<?php print_findplace_link("bplace"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("BRESN"); 

// NOTE: Marriage
?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td class="topbottombar" colspan="4"><?php print_help_link("quick_update_marriage_help", "qm"); print $factarray["MARR"]; ?></td>
</tr>
<tr><td class="descriptionbox">
		<?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="MDATE" id="MDATE" onblur="valid_date(this);" /><?php print_calendar_popup("MDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="MPLAC" id="mplace" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="manchor1x" id="manchor1x" alt="" />
	<?php print_findplace_link("mplace"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("MRESN");

// NOTE: New child
if (empty($child_surname)) $child_surname = "";
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><b><?php print_help_link("quick_update_child_help", "qm"); print $pgv_lang["add_new_chil"]; ?></b></td></tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="CGIVN" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="CSURN" value="<?php if (!empty($child_surname)) print $child_surname; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HCGIVN" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HCSURN" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RCGIVN" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RCSURN" /></td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="CSEX" tabindex="<?php print $tabkey; ?>">
			<option value="M"><?php print $pgv_lang["male"]; ?></option>
			<option value="F"><?php print $pgv_lang["female"]; ?></option>
			<option value="U"><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="CDATE" id="CDATE" onblur="valid_date(this);" /><?php print_calendar_popup("CDATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="CPLAC" id="cplace" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="canchor2x" id="canchor2x" alt="" />
	<?php print_findplace_link("cplace"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("CRESN"); ?>
</table>
</div>

<?php //------------------------------------------- FAMILY AS CHILD TABS ------------------------ 
$i++;
for($j=1; $j<=count($cfams); $j++) {
	?>
<div id="tab<?php echo $i; ?>" style="display: none;">
<table class="<?php print $TEXT_DIRECTION; ?> width80">
<?php
	$famreqdfacts = preg_split("/[,; ]/", $QUICK_REQUIRED_FAMFACTS);
	$parents = find_parents($cfams[$j-1]);
	$famid = $cfams[$j-1];
	if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
	else $famrec = find_record_in_file($famid);
	
	$subrecords = get_all_subrecords($famrec, "HUSB,WIFE,CHIL", false, false, false);
	$famfacts = array();
	foreach($subrecords as $ind=>$subrec) {
		$ft = preg_match("/1 (\w+)(.*)/", $subrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			$event = trim($match[2]);
		}
		else {
			$fact="";
			$event="";
		}
		if ($fact=="EVEN" || $fact=="FACT") $fact = get_gedcom_value("TYPE", 2, $subrec, '', false);
		if (in_array($fact, $famaddfacts)) {
			$newreqd = array();
			foreach($famreqdfacts as $r=>$rfact) {
				if ($rfact!=$fact) $newreqd[] = $rfact;
			}
			$famreqdfacts = $newreqd;
			$famfacts[] = array($fact, $subrec, 0);
		}
	}
	foreach($famreqdfacts as $ind=>$fact) {
		$famfacts[] = array($fact, "1 $fact\r\n", 1);
	}
	usort($famfacts, "compare_facts");
	$spid = "";
	if($parents) {
		if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
		else $spid=$parents["WIFE"];
	}

// NOTE: Father
?>
	<tr><td class="topbottombar" colspan="4">
	<?php
	$label = $pgv_lang["father"];
	if (!empty($parents["HUSB"])) {
		if (displayDetailsById($parents["HUSB"]) && showLivingNameById($parents["HUSB"])) {
			$fatherrec = find_person_record($parents["HUSB"]);
			$fsex = get_gedcom_value("SEX", 1, $fatherrec, '', false);
			$child_surname = "";
			$ct = preg_match("~1 NAME.*/(.*)/~", $fatherrec, $match);
			if ($ct>0) $child_surname = $match[1];
			if ($fsex=="F") $label = $pgv_lang["mother"];
			print $label." ";
			print "<a href=\"#\" onclick=\"return quickEdit('".$parents["HUSB"]."');\">";
			$name = get_person_name($parents["HUSB"]);
			if ($SHOW_ID_NUMBERS) $name .= " (".$parents["HUSB"].")";
			$name .= " [".$pgv_lang["edit"]."]";
			print PrintReady($name)."</a>\n";
		}
		else print $label." ".$pgv_lang["private"];
	}
	else print $label." ".$pgv_lang["unknown"];
	print "</td></tr>";
	print "<tr><td class=\"descriptionbox\">".$pgv_lang["enter_pid"]."<td  class=\"optionbox\" colspan=\"3\"><input type=\"text\" size=\"10\" name=\"FATHER[$i]\" id=\"FATHER$i\" value=\"".$parents['HUSB']."\" />";
	print_findindi_link("FATHER$i","");
	print "</td></tr>";
?>
<?php if (empty($parents["HUSB"])) { ?>
	<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); print $pgv_lang["add_father"]; ?></td></tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="FGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="FSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
	</tr>
	<?php if ($USE_RTL_FUNCTIONS) { ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HFGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HFSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
	</tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RFGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RFSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
	</tr>
	<?php } ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="FSEX<?php echo $i; ?>" tabindex="<?php print $tabkey; ?>">
			<option value="M" selected="selected"><?php print $pgv_lang["male"]; ?></option>
			<option value="F"><?php print $pgv_lang["female"]; ?></option>
			<option value="U"><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
	</tr>
	<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="FBDATE<?php echo $i; ?>" id="FBDATE<?php echo $i; ?>" onblur="valid_date(this);" /><?php print_calendar_popup("FBDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="FBPLAC<?php echo $i; ?>" id="Fbplace<?php echo $i; ?>" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="banchor1x" id="banchor1x" alt="" />
	<?php print_findplace_link("Fbplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
	</tr>
	<?php print_quick_resn("FBRESN$i"); ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["DEAT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="FDDATE<?php echo $i; ?>" id="FDDATE<?php echo $i; ?>" onblur="valid_date(this);" /><?php print_calendar_popup("FDDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="FDPLAC<?php echo $i; ?>" id="Fdplace<?php echo $i; ?>" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="danchor1x" id="danchor1x" alt="" />
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
	$label = $pgv_lang["mother"];
	if (!empty($parents["WIFE"])) {
		if (displayDetailsById($parents["WIFE"]) && showLivingNameById($parents["WIFE"])) {
			$motherrec = find_person_record($parents["WIFE"]);
			$msex = get_gedcom_value("SEX", 1, $motherrec, '', false);
			if ($msex=="M") $label = $pgv_lang["father"];
			print $label." ";
			print "<a href=\"#\" onclick=\"return quickEdit('".$parents["WIFE"]."');\">";
			$name = get_person_name($parents["WIFE"]);
			if ($SHOW_ID_NUMBERS) $name .= " (".$parents["WIFE"].")";
			$name .= " [".$pgv_lang["edit"]."]";
			print PrintReady($name)."</a>\n";
		}
		else print $label." ".$pgv_lang["private"];
	}
	else print $label." ".$pgv_lang["unknown"];
	print "</td></tr>\n";
	print "<tr><td  class=\"descriptionbox\">".$pgv_lang["enter_pid"]."<td  class=\"optionbox\" colspan=\"3\"><input type=\"text\" size=\"10\" name=\"MOTHER[$i]\" id=\"MOTHER$i\" value=\"".$parents['WIFE']."\" />";
	print_findindi_link("MOTHER$i","");
	?>
</td></tr>
<?php if (empty($parents["WIFE"])) { ?>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_spouse_help", "qm"); print $pgv_lang["add_mother"]; ?></td></tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="MGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="MSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HMGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	</tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HMSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RMGIVN<?php echo $i; ?>" /></td>
	</tr>
	<?php $tabkey++; ?>
	</tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RMSURN<?php echo $i; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="MSEX<?php echo $i; ?>" tabindex="<?php print $tabkey; ?>">
			<option value="M"><?php print $pgv_lang["male"]; ?></option>
			<option value="F" selected="selected"><?php print $pgv_lang["female"]; ?></option>
			<option value="U"><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	<?php $tabkey++; ?>
	</td>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="MBDATE<?php echo $i; ?>" id="MBDATE<?php echo $i; ?>" onblur="valid_date(this);" /><?php print_calendar_popup("MBDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="MBPLAC<?php echo $i; ?>" id="Mbplace<?php echo $i; ?>" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="banchor1x" id="banchor1x" alt="" />
	<?php print_findplace_link("Mbplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("MBRESN$i"); ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["DEAT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="MDDATE<?php echo $i; ?>" id="MDDATE<?php echo $i; ?>" onblur="valid_date(this);" /><?php print_calendar_popup("MDDATE$i");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="MDPLAC<?php echo $i; ?>" id="Mdplace<?php echo $i; ?>" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="danchor1x" id="danchor1x" alt="" />
	<?php print_findplace_link("Mdplace$i"); ?>
	<?php $tabkey++; ?>
	</td>
</tr>
<?php print_quick_resn("MDRESN$i"); 
}
// NOTE: Update fact 
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); print $pgv_lang["update_fact"]; ?></td></tr>
<tr>
	<td class="descriptionbox">&nbsp;</td>
	<td class="descriptionbox"><?php print $factarray["DATE"]; ?></td>
	<td class="descriptionbox"><?php print $factarray["PLAC"]; ?></td>
	<td class="descriptionbox"><?php print $pgv_lang["delete"]; ?></td>
<?php
foreach($famfacts as $f=>$fact) {
	$fact_tag = $fact[0];
	$date = get_gedcom_value("DATE", 2, $fact[1], '', false);
	$plac = get_gedcom_value("PLAC", 2, $fact[1], '', false);
	$temp = get_gedcom_value("TEMP", 2, $fact[1], '', false);
	?>
			<tr>
				<td class="descriptionbox">
				<?php if (isset($factarray[$fact_tag])) print $factarray[$fact_tag]; 
					else if (isset($pgv_lang[$fact_tag])) print $pgv_lang[$fact_tag]; 
					else print $fact_tag;
				?>
					<input type="hidden" name="F<?php echo $i; ?>TAGS[]" value="<?php echo $fact_tag; ?>" />
				</td>
				<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; $tabkey++;?>" size="15" name="F<?php echo $i; ?>DATES[]" id="F<?php echo $i; ?>DATE<?php echo $f; ?>" onblur="valid_date(this);" value="<?php echo htmlspecialchars($date); ?>" /><?php print_calendar_popup("F{$i}DATE$f");?></td>
				<?php if (empty($temp) && (!in_array($fact_tag, $nonplacfacts))) { ?>
					<td class="optionbox"><input size="30" type="text" tabindex="<?php print $tabkey; $tabkey++; ?>" name="F<?php echo $i; ?>PLACS[]" id="F<?php echo $i; ?>place<?php echo $f; ?>" value="<?php print PrintReady(htmlspecialchars($plac)); ?>" />
					<?php print_findplace_link("F'.$i.'place$f"); ?>
                         </td>
				<?php }
				else {
					print "<td class=\"optionbox\"><select tabindex=\"".$tabkey."\" name=\"F".$i."TEMP[]\" >\n";
					print "<option value=''>".$pgv_lang["no_temple"]."</option>\n";
					foreach($TEMPLE_CODES as $code=>$temple) {
						print "<option value=\"$code\"";
						if ($code==$temp) print " selected=\"selected\"";
						print ">$temple</option>\n";
					}
					print "</select>\n</td>\n";
					$tabkey++;
				}
				?>
				<td class="optionbox center">
					<input type="hidden" name="F<?php echo $i; ?>REMS[<?php echo $f; ?>]" id="F<?php echo $i; ?>REM<?php echo $f; ?>" value="0" />
					<?php if (!$fact[2]) { ?>
					<a href="javascript: <?php print $pgv_lang["delete"]; ?>" onclick="if (confirm('<?php print $pgv_lang["confirm_remove"]; ?>')) { document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>REM<?php echo $f; ?>.value='1'; document.quickupdate.submit(); } return false;">
						<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php print $pgv_lang["delete"]; ?>" />
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
</tr>
<?php
// NOTE: Add new fact
?>
<?php if (count($famaddfacts)>0) { ?>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_fact_help", "qm"); print $pgv_lang["add_fact"]; ?></td></tr>
	<tr>
		<td class="descriptionbox">&nbsp;</td>
		<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["DATE"]; ?></td>
		<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"]; ?></td>
		<td class="descriptionbox">&nbsp;</td>
		</tr>
	<tr>
		<td class="optionbox"><select name="F<?php echo $i; ?>newfact" tabindex="<?php print $tabkey; ?>">
			<option value=""><?php print $pgv_lang["select_fact"]; ?></option>
		<?php $tabkey++; ?>
		<?php
		foreach($famaddfacts as $indexval => $fact) {
			$found = false;
			foreach($famfacts as $ind=>$value) {
				if ($fact==$value[0]) {
					$found=true;
					break;
				}
			}
			if (!$found) print "\t\t<option value=\"$fact\">".$factarray[$fact]."</option>\n";
		}
		?>
			</select>
		</td>
		<td class="optionbox"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="F<?php echo $i; ?>DATE" id="F<?php echo $i; ?>DATE" onblur="valid_date(this);" /><?php print_calendar_popup("F".$i."DATE");?></td>
		<?php $tabkey++; ?>
		<td class="optionbox"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="F<?php echo $i; ?>PLAC" id="F<?php echo $i; ?>place" />
		<?php print_findplace_link("F'.$i.'place"); ?>
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
		<td class="topbottombar" colspan="4"><?php print $pgv_lang["children"]; ?></td>
	</tr>
	<tr>
		<input type="hidden" name="F<?php echo $i; ?>CDEL" value="" />
					<td class="descriptionbox"><?php print $pgv_lang["name"]; ?></td>
					<td class="descriptionbox"><?php print $factarray["SEX"]; ?></td>
					<td class="descriptionbox"><?php print $factarray["BIRT"]; ?></td>
					<td class="descriptionbox"><?php print $pgv_lang["remove"]; ?></td>
				</tr>
			<?php
				foreach($chil as $c=>$child) {
					print "<tr><td class=\"optionbox\">";
					$name = get_person_name($child);
					$disp = displayDetailsById($child);
					if ($SHOW_ID_NUMBERS) $name .= " (".$child.")";
					$name .= " [".$pgv_lang["edit"]."]";
					if ($disp||showLivingNameById($child)) {
						print "<a href=\"#\" onclick=\"return quickEdit('".$child."');\">";
						print PrintReady($name);
						print "</a>";
					}
					else print $pgv_lang["private"];
					$childrec = find_person_record($child);
					print "</td>\n<td class=\"optionbox center\">";
					if ($disp) {
						print get_gedcom_value("SEX", 1, $childrec);
					}
					print "</td>\n<td class=\"optionbox\">";
					if ($disp) {
						$birtrec = get_sub_record(1, "BIRT", $childrec);
						if (!empty($birtrec)) {
							if (showFact("BIRT", $child) && !FactViewRestricted($child, $birtrec)) {
								print get_gedcom_value("DATE", 2, $birtrec);
								print " -- ";
								print get_gedcom_value("PLAC", 2, $birtrec);
							}
						}
					}
					print "</td>\n";
					?>
					<td class="optionbox center" colspan="3">
						<a href="javascript: <?php print $pgv_lang["remove_child"]; ?>" onclick="document.quickupdate.closewin.value='0'; document.quickupdate.F<?php echo $i; ?>CDEL.value='<?php echo $child; ?>'; document.quickupdate.submit(); return false;">
							<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]; ?>" border="0" alt="<?php print $pgv_lang["remove_child"]; ?>" />
						</a>
					</td>
					<?php
					print "</tr>\n";
				}
			?>
			<tr>
				<td class="descriptionbox"><?php print $pgv_lang["add_child_to_family"]; ?></td>
				<td class="optionbox" colspan="3"><input type="text" size="10" name="CHIL[]" id="CHIL<?php echo $i; ?>" />
                                <?php print_findindi_link("CHIL$i","");?>
                                </td>
			</tr>
<?php
// NOTE: Add a child
?>
<tr><td>&nbsp;</td></tr>
<tr><td class="topbottombar" colspan="4"><?php print_help_link("quick_update_child_help", "qm"); print $pgv_lang["add_child_to_family"]; ?></td></tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $factarray["GIVN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="C<?php echo $i; ?>GIVN" /></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $factarray["SURN"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="C<?php echo $i; ?>SURN" value="<?php //if (!empty($child_surname)) print $child_surname; ?>" /></td>
	<?php $tabkey++; ?>
</tr>
<?php if ($USE_RTL_FUNCTIONS) { ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["hebrew_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HC<?php echo $i; ?>GIVN" /></td>
	<?php $tabkey++; ?>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["hebrew_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="HC<?php echo $i; ?>SURN" /></td>
	<?php $tabkey++; ?>
</tr>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_given_name_help", "qm"); print $pgv_lang["roman_givn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RC<?php echo $i; ?>GIVN" /></td>
	<?php $tabkey++; ?>
	<td class="descriptionbox"><?php print_help_link("edit_surname_help", "qm"); print $pgv_lang["roman_surn"];?></td>
	<td class="optionbox" colspan="3"><input size="50" type="text" tabindex="<?php print $tabkey; ?>" name="RC<?php echo $i; ?>SURN" /></td>
	<?php $tabkey++; ?>
</tr>
<?php } ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("edit_sex_help", "qm"); print $pgv_lang["sex"];?></td>
	<td class="optionbox" colspan="3">
		<select name="C<?php echo $i; ?>SEX" tabindex="<?php print $tabkey; ?>">
			<option value="M"><?php print $pgv_lang["male"]; ?></option>
			<option value="F"><?php print $pgv_lang["female"]; ?></option>
			<option value="U"><?php print $pgv_lang["unknown"]; ?></option>
		</select>
	</td></tr>
	<?php $tabkey++; ?>
<tr>
	<td class="descriptionbox"><?php print_help_link("def_gedcom_date_help", "qm"); print $factarray["BIRT"]; ?>
		<?php print $factarray["DATE"];?>
	</td>
	<td class="optionbox" colspan="3"><input type="text" tabindex="<?php print $tabkey; ?>" size="15" name="C<?php echo $i; ?>DATE" id="C<?php echo $i; ?>DATE" onblur="valid_date(this);" /><?php print_calendar_popup("C{$i}DATE");?></td>
	</tr>
	<?php $tabkey++; ?>
	<tr>
	<td class="descriptionbox"><?php print_help_link("edit_PLAC_help", "qm"); print $factarray["PLAC"];?></td>
	<td class="optionbox" colspan="3"><input size="30" type="text" tabindex="<?php print $tabkey; ?>" name="C<?php echo $i; ?>PLAC" id="c<?php echo $i; ?>place" /><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"];?>" name="canchor3x" id="canchor3x" alt="" />
	<?php print_findplace_link("c'.$i.'place"); ?>
	</td>
	<?php $tabkey++; ?>
</tr>
<?php print_quick_resn("C".$i."RESN"); ?>
</table>
</div>
	<?php
	$i++;
}
?>
<input type="submit" value="<?php print $pgv_lang["save"]; ?>" />
</form>
<?php
}
print_simple_footer();
?>
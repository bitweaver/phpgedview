<?php
/**
 * Allow admin users to upload a new gedcom using a web interface.
 * 
 * When importing a gedcom file, some of the gedcom structure is changed
 * so a new file is written during the import and then copied over the old
 * file.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 12 September 2005
 * 
 * @author PGV Development Team
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: uploadgedcom.php,v 1.5 2006/03/01 20:16:20 spiderr Exp $
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'phpgedview' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_admin_phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
require_once( PHPGEDVIEW_PKG_PATH.'includes/session.php' );
//vd($_REQUEST);

 // TODO: Progress bars don't show until </table> or </div>
 // TODO: Upload ZIP support alternative path and name
 
 // NOTE: $GEDFILENAME = The filename of the uploaded GEDCOM
 // NOTE: $action = Which form we should present
 // NOTE: $check = Which check to be performed
 // NOTE: $timelimit = The time limit for the import process
 // NOTE: $cleanup = If set to yes, the GEDCOM contains invalid tags
 // NOTE: $no_upload = When the user cancelled, we want to restore the original settings
 // NOTE: $path = The path to the GEDCOM file
 // NOTE: $contine = When the user decided to move on to the next step
 // NOTE: $import_existing = See if we are just importing an existing GEDCOM
 // NOTE: $replace_gedcom = When uploading a GEDCOM, user will be asked to replace an existing one. If yes, overwrite
 // NOTE: $bakfile = Name and path of the backupfile, this file is created if a file with the same name exists
//require "config.php";
//require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
//if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];

$gBitSystem->verifyPermission( 'bit_p_admin' );

if (empty($action)) $action = "upload_form";
if (!isset($path)) $path = "";
if (!isset($check)) $check = "";
if (!isset($errors)) $errors = "";
if (!isset($verify)) $verify = "";
if (!isset($import)) $import = false;
if (!isset($bakfile)) $bakfile = "";
if (!isset($cleanup_needed)) $cleanup_needed = false;
if (!isset($ok)) $ok = false;
if (!isset($startimport)) $startimport = false;
if (!isset($timelimit)) $timelimit = $home_blog = $gBitSystem->getConfig("time_limit", 60);
if (!isset($importtime)) $importtime = 0;
if (!isset($no_upload)) $no_upload = false;
if (!isset($override)) $override = false;
if ($no_upload == "cancel_upload" || $override == "no")  $check = "cancel_upload";
if (!isset($exists)) $exists = false;
if (!isset($config_gedcom)) $config_gedcom = "";
if (!isset($continue)) $continue = false;
if (!isset($import_existing)) $import_existing = false;
if (!isset($skip_cleanup)) $skip_cleanup = false;

// NOTE: GEDCOM was uploaded
if ($check == "upload") {
	$verify = "verify_gedcom";
	$ok = true;
}
// NOTE: GEDCOM was added
else if ($check == "add") {
	$verify = "verify_gedcom";
	$ok = true;
}
else if ($check == "add_new") {
	if (((!file_exists($GEDCOMS->getPath($GEDFILENAME))) && !file_exists($path.$GEDFILENAME))  || $override == "yes") {
		if ($path != "") $fp = fopen($path.$GEDFILENAME, "wb");
		else	$fp = fopen($GEDCOMS->getPath($GEDFILENAME), "wb");
		if ($fp) {
			$newgedcom = '0 HEAD
1 SOUR PhpGedView
2 VERS '.$VERSION.' '.$VERSION_RELEASE.'
1 DEST ANSTFILE
1 GEDC
2 VERS 5.5
2 FORM Lineage-Linked
1 CHAR UTF-8
0 @I1@ INDI
1 NAME Given Names /Surname/
1 SEX M
1 BIRT
2 DATE 01 JAN 1850
2 PLAC Click edit and change me
0 TRLR';
			fwrite($fp, $newgedcom);
			fclose($fp);
			$verify = "validate_form";
			$exists = true;
			// NOTE: Go straight to import, no other settings needed
			$marr_names = "no";
			$xreftype = "NA";
			$utf8convert = "no";
			$ged = $GEDFILENAME;
			$startimport = "true";
		}
	}
	else {
		if ($path != "") $fp = fopen($path.$GEDFILENAME.".bak", "wb");
		else	$fp = fopen($GEDCOMS->getPath($GEDFILENAME).".bak", "wb");
		if ($fp) {
			$newgedcom = '0 HEAD
1 SOUR PhpGedView
2 VERS '.$VERSION.' '.$VERSION_RELEASE.'
1 DEST ANSTFILE
1 GEDC
2 VERS 5.5
2 FORM Lineage-Linked
1 CHAR UTF-8
0 @I1@ INDI
1 NAME Given Names /Surname/
1 SEX M
1 BIRT
2 DATE 01 JAN 1850
2 PLAC Click edit and change me
0 TRLR';
			fwrite($fp, $newgedcom);
			fclose($fp);
			if ($path != "") $bakfile = $path.$GEDFILENAME.".bak";
			else	$bakfile = $GEDCOMS->getPath($GEDFILENAME).".bak";
			$ok = false;
			$verify = "verify_gedcom";
			$exists = true;
		}
	}
}
else if ($check == "cancel_upload") {
	if ($exists) {
		$GEDCOMS->expunge();
//		if ($action == "add_new_form") @unlink($INDEX_DIRECTORY.$GEDFILENAME);
	}
	// NOTE: Cleanup everything no longer needed
	if (isset($bakfile) && file_exists($bakfile)) unlink($bakfile);
	if ($verify) $verify = "";
	if ($GEDFILENAME) unset($GEDFILENAME);
	if ($startimport) $startimport="";
	if ($import) $import = false;
	if ($cleanup_needed) $cleanup_needed = false;
	$noupload = true;
	header("Location: index.php");
}
if ($cleanup_needed == "cleanup_needed" && $continue ) {
require_once("includes/functions_tools.php");
// development hook
$GEDFILENAME = "CAINEFull07022004.GED";	
	$filechanged=false;
	if (file_is_writeable( $GEDCOMS->getPath($GEDFILENAME) ) && (file_exists($GEDCOMS->getPath($GEDFILENAME)))) {
		$l_headcleanup = false;
		$l_macfilecleanup = false;
		$l_lineendingscleanup = false;
		$l_placecleanup = false;
		$l_datecleanup=false;
		$l_isansi = false;
		$fp = fopen($GEDCOMS->getPath($GEDFILENAME), "rb");
		$fw = fopen("c:\\Data\\".$GEDFILENAME.".bak", "wb");
		//-- read the gedcom and test it in 8KB chunks
		while(!feof($fp)) {
			$fcontents = fread($fp, 1024*8);
			$lineend = "\n";
			if (need_macfile_cleanup()) {
				$l_macfilecleanup=true;
				$lineend = "\r";
			}
			
			//-- read ahead until the next line break
			$byte = "";
			while((!feof($fp)) && ($byte!=$lineend)) {
				$byte = fread($fp, 1);
				$fcontents .= $byte;
			}
			
			if (!$l_headcleanup && need_head_cleanup()) {
				head_cleanup();
				$l_headcleanup = true;
			}
	
			if ($l_macfilecleanup) {
				macfile_cleanup();
			}
	
			if (isset($_POST["cleanup_places"]) && $_POST["cleanup_places"]=="YES") {
				if(($sample = need_place_cleanup()) !== false) {
					$l_placecleanup=true;
					place_cleanup();
				}
			}
	
			if (line_endings_cleanup()) {
				$filechanged = true;
			}
	
			if(isset($_POST["datetype"])) {
				$filechanged=true;
				//month first
				date_cleanup($_POST["datetype"]);
			}
			/**
			if($_POST["xreftype"]!="NA") {
				$filechanged=true;
				xref_change($_POST["xreftype"]);
			}
			**/
			if (isset($_POST["utf8convert"])=="YES") {
				$filechanged=true;
				convert_ansi_utf8();
			}
			fwrite($fw, $fcontents);
		}
		fclose($fp);
		fclose($fw);
		copy( 'c:\\Data\\'.$GEDFILENAME.".bak", $GEDCOMS->getPath($GEDFILENAME) );
		$cleanup_needed = false;
		$import = "true";
	}
	else {
		$errors = str_replace("#GEDCOM#", $GEDFILENAME, "The GEDCOM file, <b>#GEDCOM#</b>, is not writable. Please check attributes and access rights.");
		$gBitSmarty->assign( 'error', $errors );
	}
}

// NOTE: Change header depending on action
if ($action == "upload_form") $header = "upload_gedcom";
else if ($action == "add_form") $header = "add_gedcom";
else if ($action == "add_new_form") $header = "add_new_gedcom";
else $header = "ged_import";

$gBitSmarty->assign( 'startimport', $startimport );
$text_dir = $gBitSystem->getConfig("text_direction", " ");
$gBitSmarty->assign( 'text_dir', $text_dir );
$gBitSmarty->assign( 'action', $action );

// NOTE: Add GEDCOM form
if ($action == "add_form") {
	$gBitSmarty->assign( 'error', "" );
}
// NOTE: Upload GEDCOM form
else if ($action == "upload_form") {
	$gBitSmarty->assign( 'error', "" );
	if (!$filesize = ini_get('upload_max_filesize')) $filesize = "2M";
	$gBitSmarty->assign( 'filesize', $filesize ); 
	if ( isset($UPFILE) )
		$gBitSmarty->assign( 'file', $UPFILE );
	else if ( isset($GEDFILENAME) )
		$gBitSmarty->assign( 'file', $path.$GEDFILENAME );
	$action_form = "upload_form";
}
// NOTE: Add new GEDCOM form
else if ($action == "add_new_form") {
	$gBitSmarty->assign( 'error', "" );
}
if (isset($GEDFILENAME))
	$gBitSmarty->assign( 'GEDFILENAME', $GEDFILENAME );
else
	$gBitSmarty->assign( 'GEDFILENAME', '' );
if (isset($bakfile))
	$gBitSmarty->assign( 'bakfile', $bakfile );
else
	$gBitSmarty->assign( 'bakfile', '' );
if (isset($path))
	$gBitSmarty->assign( 'path', $path );
else
	$gBitSmarty->assign( 'path', '' );

$imported = $GEDCOMS->isValid(); // $GEDFILENAME);
if ($verify=="verify_gedcom") {
	// NOTE: Check if GEDCOM has been imported into DB
	if ( $imported || $bakfile != "") {
		$gBitSmarty->assign( 'imported', 'true' );
		$action_form = "check";
	}
	else $verify = "validate_form";
}

if ($verify == "validate_form") {
require_once("includes/functions_tools.php");
	$action_form = "validate_form";
	if (isset($no_upload))
		$gBitSmarty->assign( 'no_upload', $no_upload );
	else
		$gBitSmarty->assign( 'no_upload', '' );
	if (isset($ok))
		$gBitSmarty->assign( 'ok', $ok );
	else
		$gBitSmarty->assign( 'ok', '' );
	if (isset($no_upload))
		$gBitSmarty->assign( 'override', $override );
	else
		$gBitSmarty->assign( 'override', '' );
	if ($import != true && $skip_cleanup != "skip_cleanup") {
		// require_once("includes/functions_tools.php");
		if ($override == "yes") {
			copy($bakfile, $GEDCOMS->getPath());
			if (file_exists($bakfile)) unlink($bakfile);
			$bakfile = false;
		}
		$l_headcleanup = false;
		$l_macfilecleanup = false;
		$l_lineendingscleanup = false;
		$l_placecleanup = false;
		$l_datecleanup=false;
		$l_isansi = false;
		$fp = fopen($GEDCOMS->getPath(), "r");
		//-- read the gedcom and test it in 8KB chunks
		while(!feof($fp)) {
			$fcontents = fread($fp, 1024*8);
			if (!$l_headcleanup && need_head_cleanup()) $l_headcleanup = true;
			if (!$l_macfilecleanup && need_macfile_cleanup()) $l_macfilecleanup = true;
			if (!$l_lineendingscleanup && need_line_endings_cleanup()) $l_lineendingscleanup = true;
			if (!$l_placecleanup && ($placesample = need_place_cleanup()) !== false) {
				 $l_placecleanup = true;
				$gBitSmarty->assign( 'placesample', $datesample );
			}
			if (!$l_datecleanup && ($datesample = need_date_cleanup()) !== false) {
				$l_datecleanup = true;
				$gBitSmarty->assign( 'datesample', $datesample );
			}
			if (!$l_isansi && is_ansi()) $l_isansi = true;
		}
		fclose($fp);
		
		if ( !file_is_writeable($GEDCOMS->getPath()) && (file_exists($GEDCOMS->getPath())) )
			$gBitSmarty->assign( 'l_write', 'false' );	
		if (!$l_datecleanup && !$l_isansi  && !$l_headcleanup && !$l_macfilecleanup &&!$l_placecleanup && !$l_lineendingscleanup) {
			$cleanup_needed = true;
			$import = true;
			$gBitSmarty->assign( 'l_headcleanup', $l_headcleanup_);
			$gBitSmarty->assign( 'l_macfilecleanup', $l_macfilecleanup_);
			$gBitSmarty->assign( 'l_lineendingscleanup', $l_lineendingscleanup_);
			$gBitSmarty->assign( 'l_placecleanup', $l_placecleanup_);
			$gBitSmarty->assign( 'l_datecleanup', $l_datecleanup_);
			$gBitSmarty->assign( 'l_isansi', $l_isansi_);
		}	
		if (!isset($cleanup_needed)) $cleanup_needed = false;
		$gBitSmarty->assign( 'cleanup_needed', $cleanup_needed );
		$gBitSmarty->assign( 'import', $import );
	}	
}

if ($import == true) {
	$action_form = "import_options";
}

if ($startimport == "true") {
	//-- set the building index flag to tell the rest of the program that we are importing and so shouldn't
	//-- perform some of the same checks
	$BUILDING_INDEX = true;
	
	if (isset($exectime)){
		$oldtime=time()-$exectime;
		$skip_table=0;
	}
	else $oldtime=time();
	
	/**
	 * function that sets up the html required to run the progress bar
	 * @param long $FILE_SIZE	the size of the file

	function setup_progress_bar($FILE_SIZE) {
		global $pgv_lang, $ged, $timelimit;
		?>
	<script type="text/javascript">
	<!--
	function complete_progress(time, exectext, go_pedi, go_welc) {
		progress = document.getElementById("progress_header");
		if (progress) progress.innerHTML = '<?php print "<span class=\"error\"><b>".$pgv_lang["import_complete"]."</b></span><br />";?>'+exectext+' '+time+' '+"<?php print $pgv_lang["sec"]; ?>";
		progress = document.getElementById("link1");
		if (progress) progress.innerHTML = '<a href="pedigree.php?ged=<?php print preg_replace("/'/", "\'", $ged); ?>">'+go_pedi+'</a>';
		progress = document.getElementById("link2");
		if (progress) progress.innerHTML = '<a href="index.php?command=gedcom&ged=<?php print preg_replace("/'/", "\'", $ged); ?>">'+go_welc+'</a>';
		progress = document.getElementById("link3");
		if (progress) progress.innerHTML = '<a href="editgedcoms.php">'+"<?php print $pgv_lang["manage_gedcoms"]."</a>"; ?>";
	}
	function wait_progress() {
		progress = document.getElementById("progress_header");
		if (progress) progress.innerHTML = '<?php print $pgv_lang["please_be_patient"]; ?>';
	}
	
	var FILE_SIZE = <?php print $FILE_SIZE; ?>;
	var TIME_LIMIT = <?php print $timelimit; ?>;
	function update_progress(bytes, time) {
		perc = Math.round(100*(bytes / FILE_SIZE));
		if (perc>100) perc = 100;
		progress = document.getElementById("progress_div");
		if (progress) {
			progress.style.width = perc+"%";
			progress.innerHTML = perc+"%";
		}

		perc = Math.round(100*(time / TIME_LIMIT));
		if (perc>100) perc = 100;
		progress = document.getElementById("time_div");
		if (progress) {
			progress.style.width = perc+"%";
			progress.innerHTML = perc+"%";
		}
	}
		//-->
		</script>
	<?php
		print "<table style=\"width: 800px;\"><tr><td>";
		print "<div id=\"progress_header\" class=\"person_box\" style=\"width: 350px; margin: 10px; text-align: center;\">\n";
		print "<b>".$pgv_lang["import_progress"]."</b>";
		print "<div style=\"left: 10px; right: 10px; width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
		print "<div id=\"progress_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
		print "</div>\n";
		print "</div>\n";
		print "</td><td style=\"text-align: center;\"><div id=\"link1\">&nbsp;</div>";
		print "<div id=\"link2\">&nbsp;</div><div id=\"link3\">&nbsp;</div>";
		print "</td></tr></table>";
		print "<table style=\"width: 800px;\"><tr><td>";
		print "<div id=\"progress_header\" class=\"person_box\" style=\"width: 350px; margin: 10px; text-align: center;\">\n";
		if ($timelimit == 0) print "<b>".$pgv_lang["time_limit"]." ".$pgv_lang["none"]."</b>";
		else print "<b>".$pgv_lang["time_limit"]." ".$timelimit." ".$pgv_lang["sec"]."</b>";
		print "<div style=\"left: 10px; right: 10px; width: 300px; height: 20px; border: inset #CCCCCC 3px; background-color: #000000;\">\n";
		print "<div id=\"time_div\" class=\"person_box\" style=\"width: 1%; height: 18px; text-align: center; overflow: hidden;\">1%</div>\n";
		print "</div>\n";
		print "</div>\n";
		print "</td><td style=\"text-align: center;\"><div id=\"link1\">&nbsp;</div>";
		print "<div id=\"link2\">&nbsp;</div><div id=\"link3\">&nbsp;</div>";
		print "</td></tr></table>";
		flush();
	}
	//-- end of setup_progress_bar function
	 */
	
	if (!isset($stage)) $stage = 0;
	if ((empty($ged))||(!isset($GEDCOMS[$ged]))) $ged = $GEDCOM;
	
	$GEDCOMS->load($ged);
	$GEDCOM_FILE = $GEDCOMS->getPath();
	$FILE = $ged;
	$TITLE = $GEDCOMS->getTitle();
	
	if (isset($GEDCOM_FILE)) {
		if ((!strstr($GEDCOM_FILE, "://"))&&(!file_exists($GEDCOM_FILE))) {
			$errors = "Could not locate gedcom file at $GEDCOM_FILE";
			unset($GEDCOM_FILE);
		}
	}
	
	setup_database($stage);
	if ($stage==0) {
		$_SESSION["resumed"] = 0;
		if (file_exists($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new")) 
			unlink($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new");
		empty_database($FILE);
		//-- erase any of the changes
		foreach($pgv_changes as $cid=>$changes) {
			if ($changes[0]["gedcom"]==$ged) unset($pgv_changes[$cid]);
		}
		write_changes();
		$stage=1;
	}
//	flush();
	
	if ($stage==1) {
		@set_time_limit($timelimit);
		$FILE_SIZE = filesize($GEDCOM_FILE);
		$header = "Reading GEDCOM file";
//		setup_progress_bar($FILE_SIZE);
//		print "</td></tr>";
//		flush();
		// ------------------------------------------------------ Begin importing data
		// -- array of names
		if (!isset($indilist)) $indilist = array();
		if (!isset($famlist)) $famlist = array();
		$sourcelist = array();
		$otherlist = array();
		$i=0;
	
		//-- as we are importing the file, a new file is being written to store any
		//-- changes that might have occurred to the gedcom file (eg. conversion of 
		//-- media objects).  After the import is complete the new file is
		//-- copied over the old file.
		//-- The records are written during the import_record() method and the
		//-- update_media() method
		
		//-- open handle to read file 
		$fpged = fopen($GEDCOM_FILE, "rb");
		//-- open handle to write changed file
		$fpnewged = fopen($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new", "ab");
		$BLOCK_SIZE = 1024*4;	//-- 4k bytes per read
		$fcontents = "";
		$TOTAL_BYTES = 0;
		$place_count = 0;
		$date_count = 0;
		$media_count = 0;
		$listtype= array();
		//-- resume a halted import from the session
		if (!empty($_SESSION["resumed"])) {
			$place_count = $_SESSION["place_count"];
			$date_count = $_SESSION["date_count"];
			$TOTAL_BYTES = $_SESSION["TOTAL_BYTES"];
			$fcontents = $_SESSION["fcontents"];
			$listtype = $_SESSION["listtype"];
			$exectime_start = $_SESSION["exectime_start"];
			$i = $_SESSION["i"];
			fseek($fpged, $TOTAL_BYTES);
		}
		else $_SESSION["resumed"] = 0;
		while(!feof($fpged)) {
			$fcontents .= fread($fpged, $BLOCK_SIZE);
			$TOTAL_BYTES += $BLOCK_SIZE;
			$pos1 = 0;
			while($pos1!==false) {
				//-- find the start of the next record
				$pos2 = strpos($fcontents, "\n0", $pos1+1);
				while((!$pos2)&&(!feof($fpged))) {
					$fcontents .= fread($fpged, $BLOCK_SIZE);
					$TOTAL_BYTES += $BLOCK_SIZE;
					$pos2 = strpos($fcontents, "\n0", $pos1+1);
				}
				
				//-- pull the next record out of the file
				if ($pos2) $indirec = substr($fcontents, $pos1, $pos2-$pos1);
				else $indirec = substr($fcontents, $pos1);
				
				//-- remove any extra slashes
				$indirec = preg_replace("/\\\/", "/", $indirec);
				
				//-- import anything that is not a blob
				if (preg_match("/\n1 BLOB/", $indirec)==0) {
					$gid = import_record(trim($indirec));
					$place_count += update_places($gid, $indirec);
					$date_count += update_dates($gid, $indirec);
				}
				
				//-- move the cursor to the start of the next record
				$pos1 = $pos2;
				
				//-- calculate some statistics
				if (!isset($show_type)){
					$show_type=$type;
					$i_start=1;
					$exectime_start=0;
					$type_BYTES=0;
				}
				$i++;
				if ($show_type!=$type) {
					$newtime = time();
					$exectime = $newtime - $oldtime;
					$show_exectime = $exectime - $exectime_start;
					$show_i=$i-$i_start;
					$type_BYTES=$TOTAL_BYTES-$type_BYTES;
					if (!isset($listtype[$show_type]["type"])) {
						$listtype[$show_type]["exectime"]=$show_exectime;
						$listtype[$show_type]["bytes"]=$type_BYTES;
						$listtype[$show_type]["i"]=$show_i;
						$listtype[$show_type]["type"]=$show_type;
					}
					else {
						$listtype[$show_type]["exectime"]+=$show_exectime;
						$listtype[$show_type]["bytes"]+=$type_BYTES;
						$listtype[$show_type]["i"]+=$show_i;
					}
					$show_type=$type;
					$i_start=$i;
					$exectime_start=$exectime;
					$type_BYTES=$TOTAL_BYTES;
				}
				//-- update the progress bars at every 10 records
				if ($i%10==0) {
					$newtime = time();
					$exectime = $newtime - $oldtime;
//					print "\n<script type=\"text/javascript\">update_progress($TOTAL_BYTES, $exectime);</script>\n";
//					flush();
				}
//				else print " ";
				$show_gid=$gid;
				
				//-- check if we are getting close to timing out
				if ($i%10==0) {
					$newtime = time();
					$exectime = $newtime - $oldtime;
					if (($timelimit != 0) && ($timelimit - $exectime) < 2) {
						$importtime = $importtime + $exectime;
						$fcontents = substr($fcontents, $pos2);
						//-- store the resume information in the session
						$_SESSION["place_count"] = $place_count;
						$_SESSION["date_count"] = $date_count;
						$_SESSION["media_count"] = $media_count;
						$_SESSION["TOTAL_BYTES"] = $TOTAL_BYTES;
						$_SESSION["fcontents"] = $fcontents;
						$_SESSION["listtype"] = $listtype;
						$_SESSION["exectime_start"] = $exectime_start;
						$_SESSION["importtime"] = $importtime;
						$_SESSION["i"] = $i;
						
						//-- close the file connection
						fclose($fpged);
						fclose($fpnewged);
						$_SESSION["resumed"]++;
						$gBitSmarty->assign( 'header', $header );
						$gBitSystem->display( "bitpackage:phpgedview/gedcom_resume.tpl" );
						exit;
					}
				}
			}
			$fcontents = substr($fcontents, $pos2);
		}
		fclose($fpged);
		fclose($fpnewged);
		//-- as we are importing the file, a new file is being written to store any
		//-- changes that might have occurred to the gedcom file (eg. conversion of 
		//-- media objects).  After the import is complete the new file is
		//-- copied over the old file.
		//-- The records are written during the import_record() method and the
		//-- update_media() method
		$res = @copy($GEDCOM_FILE, $INDEX_DIRECTORY.basename($GEDCOM_FILE).".bak");
		if (!$res) $errors =  "Unable to create backup of the GEDCOM file at ".$GEDCOM_FILE;
		//unlink($GEDCOM_FILE);
		$res = @copy($INDEX_DIRECTORY.basename($GEDCOM_FILE).".new", $GEDCOM_FILE);
		if (!$res) $errors =  "Unable to copy updated GEDCOM file ".$GEDCOM_FILE;
		$newtime = time();
		$exectime = $newtime - $oldtime;
		$importtime = $importtime + $exectime;
		$exec_text = $pgv_lang["exec_time"];
		$go_pedi = $pgv_lang["click_here_to_go_to_pedigree_tree"];
		$go_welc = $pgv_lang["welcome_page"];
/*		if ($LANGUAGE=="french" || $LANGUAGE=="italian"){
			print "<script type=\"text/javascript\">complete_progress($importtime, \"$exec_text\", \"$go_pedi\", \"$go_welc\");</script>";
		}
		else print "<script type=\"text/javascript\">complete_progress($importtime, '$exec_text', '$go_pedi', '$go_welc');</script>";
		flush();
*/				
		if ($marr_names == "yes") {
			$GEDCOM = $FILE;
			get_indi_list();
			get_fam_list();
			$header = "Calculating Married Names";
//			setup_progress_bar(count($indilist));
//			print "</td></tr>";
		
			$i=0;
			$newtime = time();
			$exectime = $newtime - $oldtime;
			$exectime_start = $exectime;
			$names_added = 0;
			include_once("includes/functions_edit.php");
			$manual_save = true;
			foreach($indilist as $gid=>$indi) {
				if (preg_match("/1 SEX F/", $indi["gedcom"])>0) {
					$ct = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indi["gedcom"], $match, PREG_SET_ORDER);
					if ($ct>0){
						for($j=0; $j<$ct; $j++) {
							if (isset($famlist[$match[$j][1]])) {
								$marrrec = get_sub_record(1, "1 MARR", $famlist[$match[$j][1]]["gedcom"]);
								if ($marrrec) {
									$parents = find_parents_in_record($famlist[$match[$j][1]]["gedcom"]);
									if ($parents["HUSB"]!=$gid) $spid = $parents["HUSB"];
									else $spid = $parents["WIFE"];
									if (isset($indilist[$spid])) {
										$surname = $indilist[$spid]["names"][0][2];
										$letter = $indilist[$spid]["names"][0][1];
										//-- uncomment the next line to put the maiden name in the given name area
										//$newname = preg_replace("~/(.*)/~", " $1 /".$surname."/", $indi["names"][0][0]);
										$newname = preg_replace("~/(.*)/~", "/".$surname."/", $indi["names"][0][0]);
										if (strpos($indi["gedcom"], "_MARNM $newname")===false) {
											$pos1 = strpos($indi["gedcom"], "1 NAME");
											if ($pos1!==false) {
												$pos1 = strpos($indi["gedcom"], "\n1", $pos1+1);
												if ($pos1!==false) $indi["gedcom"] = substr($indi["gedcom"], 0, $pos1)."\n2 _MARNM $newname\r\n".substr($indi["gedcom"], $pos1+1);
												else $indi["gedcom"]= trim($indi["gedcom"])."\r\n2 _MARNM $newname\r\n";
												$indi["gedcom"] = check_gedcom($indi["gedcom"], false);
												$pos1 = strpos($fcontents, "0 @$gid@");
												$pos2 = strpos($fcontents, "0 @", $pos1+1);
												if ($pos2===false) $pos2=strlen($fcontents);
												$fcontents = substr($fcontents, 0,$pos1).trim($indi["gedcom"])."\r\n".substr($fcontents, $pos2);
												add_new_name($gid, $newname, $letter, $surname, $indi["gedcom"]);
												$names_added++;
											}
										}
									}
								}
							}
						}
					}
				}
				$i++;
				if ($i%10==0) {
					$newtime = time();
					$exectime = $newtime - $oldtime;
//					print "\n<script type=\"text/javascript\">update_progress($i, $exectime);</script>\n";
//					flush();

					//-- check if we are getting close to timing out
					$newtime = time();
					$exectime = $newtime - $oldtime;
					if (($timelimit != 0) && ($timelimit - $exectime) < 2) {
						$importtime = $importtime + $exectime;
						$fcontents = substr($fcontents, $pos2);
						//-- store the resume information in the session
						$_SESSION["place_count"] = $place_count;
						$_SESSION["date_count"] = $date_count;
						$_SESSION["media_count"] = $media_count;
						$_SESSION["TOTAL_BYTES"] = $TOTAL_BYTES;
						$_SESSION["fcontents"] = $fcontents;
						$_SESSION["listtype"] = $listtype;
						$_SESSION["exectime_start"] = $exectime_start;
						$_SESSION["importtime"] = $importtime;
						$_SESSION["i"] = $i;
						
						//-- close the file connection
						fclose($fpged);
						$_SESSION["resumed"]++;
						$gBitSmarty->assign( 'header', $header );
						$gBitSystem->display( "bitpackage:phpgedview/gedcom_resume.tpl" );
						exit;
					}
				}
			}
			write_file();
			$show_table_marr = "<table class=\"list_table\"><tr>";
			$show_table_marr .= "<tr><td class=\"topbottombar\" colspan=\"3\">".$pgv_lang["import_marr_names"]."</td></tr>";
			$show_table_marr .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>";
			$show_table_marr .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
			$show_table_marr .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
			$newtime = time();
			$exectime = $newtime - $oldtime;
			$show_exectime = $exectime - $exectime_start;
			$show_table_marr .= "<tr><td class=\"optionbox indent_rtl rtl\">$show_exectime ".$pgv_lang["sec"]."</td>\n";
			$show_table_marr .= "<td class=\"optionbox indent_rtl rtl\">$names_added<script type=\"text/javascript\">update_progress($i, $exectime);</script></td>";
			$show_table_marr .= "<td class=\"optionbox\">&nbsp;INDI&nbsp;</td></tr>\n";
			$show_table_marr .= "</table>\n";
			
			$gBitSmarty->assign( 'show_table_marr', $show_table_marr );
			$stage=10;
			$record_count=0;
//			flush();
		}
		$show_table1 = "<table class=\"list_table\"><tr>";
		$show_table1 .= "<tr><td class=\"topbottombar\" colspan=\"4\">".$pgv_lang["ged_import"]."</td></tr>";
		$show_table1 .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["exec_time"]."&nbsp;</td>";
		$show_table1 .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["bytes_read"]."&nbsp;</td>\n";
		$show_table1 .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["found_record"]."&nbsp;</td>";
		$show_table1 .= "<td class=\"descriptionbox\">&nbsp;".$pgv_lang["type"]."&nbsp;</td></tr>\n";
		foreach($listtype as $indexval => $type) {
			$show_table1 .= "<tr><td class=\"optionbox indent_rtl rtl \">".$type["exectime"]." ".$pgv_lang["sec"]."</td>";
			$show_table1 .= "<td class=\"optionbox indent_rtl rtl \">".($type["bytes"]=="0"?"++":$type["bytes"])."</td>\n";
			$show_table1 .= "<td class=\"optionbox indent_rtl rtl \">".$type["i"]."</td>";
			$show_table1 .= "<td class=\"optionbox rtl\">&nbsp;".$type["type"]."&nbsp;</td></tr>\n";
		}
		$show_table1 .= "<tr><td class=\"optionbox indent_rtl rtl \">$importtime ".$pgv_lang["sec"]."</td>";
		$show_table1 .= "<td class=\"optionbox indent_rtl rtl \">$TOTAL_BYTES<script type=\"text/javascript\">update_progress($TOTAL_BYTES, $exectime);</script></td>\n";
		$show_table1 .= "<td class=\"optionbox indent_rtl rtl \">".($i-1)."</td>";
		$show_table1 .= "<td class=\"optionbox\">&nbsp;</td></tr>\n";
		$show_table1 .= "</table>\n";
		cleanup_database();
		$gBitSmarty->assign( 'show_table1', $show_table1 );
		$action_form = "statistics";

		$record_count=0;
		$_SESSION["resumed"] = 0;
		unset($_SESSION["place_count"]);
		unset($_SESSION["date_count"]);
		unset($_SESSION["TOTAL_BYTES"]);
		unset($_SESSION["fcontents"]);
		unset($_SESSION["listtype"]);
		unset($_SESSION["exectime_start"]);
		unset($_SESSION["i"]);
		@set_time_limit($TIME_LIMIT);
	}
}
?>
<?php
// Display the template
$gBitSystem->display( "bitpackage:phpgedview/gedcom_$action_form.tpl" );
?>
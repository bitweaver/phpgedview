<?php
/**
 * Popup window that will allow a user to search for a media
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
 * @author PGV Development Team
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: media.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

 /* TODO:
 *	Add check for missing index.php files when creating a directory
 *	Add an option to generate thumbnails for all files on the page
 *	Add filter for correct media like php, gif etc.
 *  Check for URL instead of physical file
 *	Check array buld up use ID_GEDCOM for aray key
 */

 /* Standard variable convention media.php
 *	$filename = Filename of the media item
 *	$thumbnail = Filename of the thumbnail of the media item
 *	$gedfile = Name of the GEDCOM file
 *	$medialist = Array with all media items
 */
 
require("config.php");
require("includes/functions_edit.php");

if (!isset($action)) {
	if (isset($search)) $action = "filter";
	else $action="";
}
if (empty($action)) $action="filter";

if (!isset($media)) $media="";
if (!isset($filter)) $filter="";
if (!isset($directory)) $directory = $MEDIA_DIRECTORY;
if (!isset($external_links)) $external_links = "";
if (!isset($level)) $level=0;
if (!isset($start)) $start = 0;
if (!isset($max)) $max = 20;

$badmedia = array(".","..","CVS","thumbs","index.php","MediaInfo.txt", ".cvsignore");

if (!isset($showthumb)) $showthumb = false;
else $showthumb= isset($showthumb);
if (count($_POST) == 0) $showthumb = true;

$thumbget = "";
if ($showthumb) {$thumbget = "&amp;showthumb=true";}

//-- prevent script from accessing an area outside of the media directory
//-- and keep level consistency
if (($level < 0) || ($level > $MEDIA_DIRECTORY_LEVELS)){
	$directory = $MEDIA_DIRECTORY;
	$level = 0;
} elseif (preg_match("'^$MEDIA_DIRECTORY'", $directory)==0){
	$directory = $MEDIA_DIRECTORY;
	$level = 0;
}

//-- force the thumbnail directory to have the same layout as the media directory
//-- Dots and slashes should be escaped for the preg_replace
$srch = "/".addcslashes($MEDIA_DIRECTORY,'/.')."/";
$repl = addcslashes($MEDIA_DIRECTORY."thumbs/",'/.');
$thumbdir = stripcslashes(preg_replace($srch, $repl, $directory));

//-- only allow users with edit privileges to access script.
if ((!userCanEdit(getUserName())) || (!$ALLOW_EDIT_GEDCOM)) {
	header("Location: login.php?url=media.php");
	exit;
}

//-- check for admin once (used a bit in this script)
$isadmin =  userIsAdmin(getUserName());

//-- TODO add check for -- admin can manipulate files
$fileaccess = false;
if ($isadmin) {
	$fileaccess = true;
}

// Print the header of the page
print_header($pgv_lang["manage_media"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
	function pasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}
	function openImage(filename, width, height) {
		height=height+50;
		screenW = screen.width;
	 	screenH = screen.height;
	 	if (width>screenW) width=screenW;
	 	if (height>screenH) height=screenH;
		if ((filename.search(/\.jpg$/gi)!=-1)||(filename.search(/\.gif$/gi)!=-1)) window.open('imageview.php?filename='+filename,'','top=50,left=50,height='+height+',width='+width+',scrollbars=1,resizable=1');
		else window.open(unescape(filename),'','top=50,left=50,height='+height+',width='+width+',scrollbars=1,resizable=1');
		return false;
	}
	function ilinkitem(mediaid, type) {
		window.open('inverselink.php?mediaid='+mediaid+'&linkto='+type+'&'+sessionname+'='+sessionid, '', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}

	function checknames(frm) {
		if (document.managemedia.subclick) button = document.managemedia.subclick.value;
		if (button == "reset") {
			frm.filter.value = "";
			return true;
		}
		else if (frm.filter.value.length < 2) {
			alert("<?php print $pgv_lang["search_more_chars"]?>");
			frm.filter.focus();
			return false;
		}
		return true;
	}
	
	function checkpath(folder) {
		value = folder.value;
		if (value.substr(value.length-1,1) == "/") value = value.substr(0, value.length-1);
		result = value.split("/");
		if (result.length > <?php print $MEDIA_DIRECTORY_LEVELS;?>) {
			alert('<?php print $pgv_lang["max_media_depth"] ;?>');
			folder.focus();
			return false;
		}
	}

//-->
</script>
<script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>
<form name="managemedia" method="post" onsubmit="return checknames(this);" action="media.php">
	<input type="hidden" name="directory" value="<?php $action == "deletedir"?print $parentdir:print $directory; ?>" />
	<input type="hidden" name="thumbdir" value="<?php print $thumbdir; ?>" />
	<input type="hidden" name="level" value="<?php print $level; ?>" />
	<input type="hidden" name="all" value="true" />
	<input type="hidden" name="subclick" />
	<table class="fact_table center width90 <?php print $TEXT_DIRECTION; ?>">
	<tr><td class="topbottombar" colspan="6"><?php print_help_link("manage_media_help","qm","manage_media");print $pgv_lang["manage_media"];?></td></tr>
	<!-- // NOTE: Filter options -->
	<tr><td class="descriptionbox width20"><?php print_help_link("filter_help","qm","filter"); print $pgv_lang["filter"];?></td>
	<td class="optionbox"><input type="text" name="filter" value="<?php if(isset($filter)) print $filter; ?>"/>&nbsp;<input type="submit" name="search" value="<?php print $pgv_lang["filter"];?>" onclick="this.form.subclick.value=this.name" />&nbsp;<input type="submit" name="reset" value="<?php print $pgv_lang["reset"];?>" onclick="this.form.subclick.value=this.name" /></td>
	
	<!-- // NOTE: Upload media files -->
	<td class="descriptionbox wrap"><?php print_help_link("upload_media_help","qm","upload_media"); print $pgv_lang["upload_media"];?></td>
	<td class="optionbox"><?php print "<a href=\"#\" onclick=\"expand_layer('uploadmedia');\">".$pgv_lang["upload_media"]."</a>";?></td></tr>
	
	<!-- // NOTE: Show thumbnails -->
	<td class="descriptionbox"><?php print_help_link("show_thumb_help","qm", "show_thumbnail");?><?php print $pgv_lang["show_thumbnail"];?></td>
	<td class="optionbox"><input type="checkbox" name="showthumb" value="true" <?php if( $showthumb) print "checked=\"checked\""; ?> onclick="submit();" /></td>
	
	<!-- // NOTE: Empty entry to fill table -->
	<td class="descriptionbox"><?php print_help_link("add_media_help", "qm"); ?><?php print $pgv_lang["add_media_lbl"]; ?></td>
	<td class="optionbox">
	<a href="javascript: <?php echo $pgv_lang["add_media_lbl"]; ?>" onclick="window.open('addmedia.php?action=showmediaform', '', 'top=50,left=50,width=900,height=650,resizable=1,scrollbars=1'); return false;"> <?php echo $pgv_lang["add_media"]; ?></a>
	</td>
	</tr>
	</table>
</form>
<?php

// NOTE: Here is checked if the media structure is OK, if not , we cannot continue
if (check_media_structure()) {
	/**
	 * This action creates a new directory in the current directory
	 *
	 * Checks are made for relative filename exploits.
	 * The index.php is created which points back to the medialist page
	 * The same is done for the thumbnail directory to keep filesystem consistent
	 *
	 * Directory access checks are done during menu creation so the user
	 * cannot create directories deeper than the configured level
	 *
	 * @name $action->newdir
	 */
	if ($action == "newdir") {
		// security checks, no names with . .. / \ in them
		// add more if required
		$clean = true;
		$badchars = array(".","/","\\");
		for ($i = 0; $i < sizeof($badchars); $i++) {
			$pos = strpos($newdir,$badchars[$i]);
			if (is_bool($pos)) continue;
			$clean = false;
			break;
		}
		if ($clean) {
			$res = mkdir($directory.$newdir);
			$res = mkdir($thumbdir.$newdir);
			if (file_exists($directory."index.php")) {
				$inddata = file_get_contents($directory."index.php");
				$inddata = str_replace(": ../",": ../../",$inddata);
				$fp = @fopen($directory.$newdir."/index.php","w+");
				if (!$fp) print "<div class=\"error\">".$pgv_lang["security_no_create"].$directory.$newdir."</div>";
				else {
					fputs($fp,$inddata);
					fclose($fp);
				}
			}
			else print "<div class=\"error\">".$pgv_lang["security_not_exist"].$directory."</div>";
			
			if (file_exists($thumbdir."index.php")) {
				$inddata = file_get_contents($thumbdir."index.php");
				$inddata = str_replace(": ../",": ../../",$inddata);
				$fp = @fopen($thumbdir.$newdir."/index.php","w+");
				if (!$fp) print "<div class=\"error\">".$pgv_lang["security_no_create"].$thumbdir.$newdir."</div>";
				else {
					fputs($fp,$inddata);
					fclose($fp);
				}
			}
			else print "<div class=\"error\">".$pgv_lang["security_not_exist"].$thumbdir."</div>";
		}
		else {
			print "<div class=\"error\">".$pgv_lang["illegal_chars"]."</div>";
		}
		$medialist = get_medialist();
		$action="filter";
	}
	
	if ($action == "deletedir") {
		print "<table class=\"list_table center width50\">";
		print "<tr><td class=\"messagebox\">";
		// Check if media directory and thumbs directory are empty
		$clean = false;
		$files = array();
		$thumbfiles = array();
		$resdir = false;
		$resthumb = false;
		// Media directory check
		if (is_dir($directory)) {
		   	$handle = opendir($directory);
		   	$files = array();
		   	while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $badmedia)) $files[] = $file;
		   	}
		}
		else {
			print "<div class=\"error\">".$directory." ".$pgv_lang["directory_not_exist"]."</div>";
			AddToLog($directory." ".$pgv_lang["directory_not_exist"]);
		}
		
	   	// Thumbs directory check
	   	if (is_dir($thumbdir)) {
		   	$handle = opendir($thumbdir);
		   	$thumbfiles = array();
		   	while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $badmedia)) $thumbfiles[] = $file;
		   	}
	   		closedir($handle);
	   	}
	   	else {
			print "<div class=\"error\">".$thumbdir." ".$pgv_lang["directory_not_exist"]."</div>";
			AddToLog($thumbdir." ".$pgv_lang["directory_not_exist"]);
		}
	
		if (!isset($error)) {
			if (count($files) > 0 ) {
				print "<div class=\"error\">".$directory." -- ".$pgv_lang["directory_not_empty"]."</div>";
				AddToLog($directory." -- ".$pgv_lang["directory_not_empty"]);
				$clean = false;
			}
			if (count($thumbfiles) > 0) {
				print "<div class=\"error\">".$thumbdir." -- ".$pgv_lang["directory_not_empty"]."</div>";
				AddToLog($thumbdir." -- ".$pgv_lang["directory_not_empty"]);
				$clean = false;
			}
			else $clean = true;
		}
		
		// Only start deleting if both media and thumbnail directory exist
		if ($clean) {
			if (file_exists($directory."index.php")) @unlink($directory."index.php");
			if (file_exists($thumbdir."index.php")) @unlink($thumbdir."index.php");
			if (is_dir($directory)) $resdir = @rmdir(substr($directory,0,-1));
			if (is_dir($thumbdir)) $resthumb = @rmdir(substr($thumbdir,0,-1));
			if ($resdir && $resthumb) {
				print $pgv_lang["delete_dir_success"];
				AddToLog($directory." -- ".$pgv_lang["delete_dir_success"]);
			}
			else {
				if (!$resdir) {
					print "<div class=\"error\">".$pgv_lang["media_not_deleted"]."</div>";
					AddToLog($directory." -- ".$pgv_lang["media_not_deleted"]);
				}
				else {
					print $pgv_lang["media_deleted"];
					AddToLog($directory." -- ".$pgv_lang["media_deleted"]);
				}
				if (!$resthumb) {
					print "<div class=\"error\">".$pgv_lang["thumbs_not_deleted"]."</div>";
					AddToLog($thumbdir." -- ".$pgv_lang["thumbs_not_deleted"]);
				}
				else {
					print $pgv_lang["thumbs_deleted"];
					AddToLog($thumbdir." -- ".$pgv_lang["thumbs_deleted"]);
				}
				
			}
		}
		$directory = $parentdir;
		$medialist = get_medialist();
		$action="filter";
		print "</td></tr></table>";
	}
	
	
	/**
	 * This action moves a file one directory level at a time
	 *
	 * Note: this will generally not work with default install.
	 * files and dirs need to be writable by the php/web user,
	 *
	 * Thumbnails are moved as well forcing a consistent directory structure
	 * Directory access checks are done during menu creation so directories
	 * deeper than the configured level will not be shown even if they exist.
	 *
	 * @name $action->moveto
	 */
	if ($action=="moveto") {
		// just in case someone fashions the right url
		if ($isadmin && $fileaccess) {
			$exists = false;
			
			// file details
			$filename = $_REQUEST["movefile"];
			$moveto = $_REQUEST["movetodir"];
			$resfile = false;
			// and the thumbnail
			$tdirfrom = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$filename);
			$tdirto = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$directory).$moveto;
			$resthumb = false;
			// Check if the files do not yet exist on the new location
			$movefile = set_media_path($filename, $moveto);
			$movethumb = set_media_path($filename, $moveto, true);
			
			if (file_exists($movefile)) {
				print "<span class=\"error\">".$pgv_lang["media_exists"]."</span>";
				AddToLog($movefile." ".$pgv_lang["media_exists"]);
				$exists = true;
			}
			if (file_exists($movethumb)) {
				print "<span class=\"error\">".$pgv_lang["media_thumb_exists"]."</span>";
				AddToLog($movethumb." ".$pgv_lang["media_thumb_exists"]);
				$exists = true;
			}
			
			if ($exists == true) {
				$action = "filter";
			}
			else {
				// Moving the thumbnail file if it exists
				// no error if admin chooses not to have a thumbnail file
				if (file_exists($tdirfrom)) {
					if (is_dir($tdirto)) $resthumb = @rename($tdirfrom, $movethumb);
					else {
						// however if there is a thumbnail file and no directory to put it in
						if (mkdir($tdirto,0777)) $resthumb = @rename($tdirfrom, $movethumb);
						else {
							// abort the whole operation to keep the directory structure valid
							print $tdirto.$pgv_lang["no_thumb_dir"];
							AddToLog($tdirto.$pgv_lang["no_thumb_dir"]);
							print_simple_footer();
							exit;
						}
					}
				}
				
				// no major error from thumbnail so move the file
				$resfile = @rename($filename, $movefile);
				
				if ($resthumb && $resfile) {
					print $pgv_lang["move_file_success"];
					AddToLog($tdirto.$pgv_lang["no_thumb_dir"]);
				}
				
				// also inform the database of this move if from the embedded admin page
				if (strlen($_REQUEST["xref"])>0) {
					move_db_media($filename, $movefile, $GEDCOM);
				}
			}
		}
		$medialist = get_medialist();
		$action="filter";
	}
	
	/**
	 * This action generates a thumbnail for the file
	 *
	 * @name $action->thumbnail
	 */
	if ($action == "thumbnail") {
		print "<table class=\"list_table $TEXT_DIRECTION width50\">";
		print "<tr><td class=\"messagebox wrap\">";
		// TODO: add option to generate thumbnails for all images on page
		// Cycle through $medialist and skip all exisiting thumbs
		
		// Check if $all is true, if so generate thumbnails for all files that do 
		// not yet have any thumbnails created. Otherwise only the file specified.
		if ($all == true) {
			foreach ($medialist as $key => $media) {
				$thumbnail = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$media["FILE"]);
				if (!file_exists($thumbnail)) {
					if (generate_thumbnail($media["FILE"],$thumbnail)) {
						print_text("thumb_genned");
						AddToChangeLog(print_text("thumb_genned",0,1));
					}
					else {
						print "<span class=\"error\">";
						print_text("thumbgen_error");
						print "</span>";
						AddToChangeLog(print_text("thumbgen_error",0,1));
					}
					print "<br />";
				}
			}
		}
		else if ($all == false) {
			$filename = $_REQUEST["file"];
			$thumbnail = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$filename);
			if (generate_thumbnail($filename,$thumbnail)) {
				print_text("thumb_genned");
				AddToChangeLog(print_text("thumb_genned",0,1));
			}
			else {
				print "<span class=\"error\">";
				print_text("thumbgen_error");
				print "</span>";
				AddToChangeLog(print_text("thumbgen_error",0,1));
			}
		}
		$medialist = get_medialist();
		$action = "filter";
		print "</td></tr></table>";
	}
		
	// Upload media items
	if ($action == "upload") {
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		?>
		<?php
		// Sanity check for the thumbs folder, does it exist?
		for($i=1; $i<6; $i++) {
			// Check if the folder name does not start with a /. If so, remove it.
			if (substr($_POST["folder".$i],0,1) == "/") $_POST["folder".$i] = substr($_POST["folder".$i],1);
			// Check if the folder name does end with a /. If no so, add it.
			if (substr($_POST["folder".$i],-1,1) != "/") $_POST["folder".$i] .= "/";
			$error="";
			if (!empty($_FILES['mediafile'.$i]["name"])) {
				$thumbgenned = false;
				// Check if the folder exists
				if (!is_dir($MEDIA_DIRECTORY.$_POST["folder".$i])) {
					$dirs = array();
					// Split folder path into seperate directories
					if (stristr($_POST["folder".$i], "/")) {
						$dirs = preg_split("/\//", $_POST["folder".$i]);
						array_pop($dirs);
					}
					// If there is only one folder specified create this folder and its thumbnail folder
					if (count($dirs) == 0) {
						if (!is_dir($MEDIA_DIRECTORY.$_POST["folder".$i])) mkdir($MEDIA_DIRECTORY.$_POST["folder".$i]);
						// Sanity check for the thumbs folder, does it exist?
						if (!is_dir($MEDIA_DIRECTORY."thumbs")) mkdir($MEDIA_DIRECTORY."thumbs");
						if (!is_dir($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i])) mkdir($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i]);
					}
					// There are multiple directories specified, so go through all directories and check if they exist
					// if they don't exist create them, create their thumbnail directory
					// and put an index.php file in each folder created
					else {
						$tempdir = "";
						foreach ($dirs as $key => $dir) {
							$tempdir .= $dir;
							if (!is_dir($MEDIA_DIRECTORY.$tempdir)) mkdir($MEDIA_DIRECTORY.$tempdir);
							if (!is_dir($MEDIA_DIRECTORY."thumbs/".$tempdir)) mkdir($MEDIA_DIRECTORY."thumbs/".$tempdir);
							$tempdir .= "/";
							if (file_exists($MEDIA_DIRECTORY."index.php")) {
								$inddata = file_get_contents($MEDIA_DIRECTORY."index.php");
								$strdots = ": ".str_repeat("../", $key+2);
								$inddata = str_replace(": ../",$strdots,$inddata);
								$inddatathumb = str_replace(": ../",$strdots."../",$inddata);
								$fp = @fopen($MEDIA_DIRECTORY.$tempdir."index.php","w+");
								$fpthumb = @fopen($MEDIA_DIRECTORY."thumbs/".$tempdir."index.php","w+");
								if (!$fp) print "<div class=\"error\">".$pgv_lang["security_no_create"].$MEDIA_DIRECTORY.$tempdir."</div>";
								if (!$fpthumb) print "<div class=\"error\">".$pgv_lang["security_no_create"].$MEDIA_DIRECTORY."thumbs/".$tempdir."</div>";
								else {
									// Write the index.php for the normal media folder
									fputs($fp,$inddata);
									fclose($fp);
									// Write the index.php for the thumbs media folder
									fputs($fpthumb,$inddatathumb);
									fclose($fpthumb);
								}
							}
							else print "<div class=\"error\">".$pgv_lang["security_not_exist"].$MEDIA_DIRECTORY."</div>";
						}
					}
				}
				// Copy file into the destination directory
				if (move_uploaded_file($_FILES['mediafile'.$i]['tmp_name'], $MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']))) {
					// Set file permission to read/write for everybody
					@chmod($MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']), 0644);
					AddToLog("Media file ".$MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name'])." uploaded by >".getUserName()."<");
					// Check if a thumbnail file is specified by the user
					// if so copy it to the thumbnail folder
					if (!empty($_FILES['thumbnail'.$i]["name"])) {
						if (!move_uploaded_file($_FILES['thumbnail'.$i]['tmp_name'], $MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['thumbnail'.$i]['name']))) {
							
							$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['thumbnail'.$i]['error']]."<br />";
						}
						else {
							// Set file permission on thumbnail to read/write for everybody
							@chmod($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['thumbnail'.$i]['name']), 0644);
							AddToLog("Media thumbnail ".$MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['thumbnail'.$i]['name'])." uploaded by >".getUserName()."<");
						}
					}
					// The user did not specify a thumbnail but let's see if the user want
					// a thumbnail generated. Let's do it.
					else if (!empty($_POST['genthumb'.$i]) && ($_POST['genthumb'.$i]=="yes")) {
						// Check if the filename has an extension that can be used for thumbnail generation
						$filename = $MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']);
						$thumbnail = $MEDIA_DIRECTORY."thumbs".$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']);
						$ct = preg_match("/\.([^\.]+)$/", $filename, $match);
						if ($ct>0) {
							$ext = strtolower(trim($match[1]));
							if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png") {
								$thumbgenned = generate_thumbnail($filename, $thumbnail);
								if (!$thumbgenned) $error .= $pgv_lang["thumbgen_error"].$filename."<br />";
								else {
									// Set file permission on thumbnail to read/write for everybody
									@chmod($thumbnail, 0644);
									print_text("thumb_genned");
									print "<br />";
									AddToLog("Media thumbnail ".$MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['thumbnail'.$i]['name'])." generated by >".getUserName()."<");
								}
							}
						}
					}
				}
				// the file cannot be copied then print the error and stop processing
				else $error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['mediafile'.$i]['error']]."<br />";
	
				// Let's see if there are any errors generated and print it
				if (!empty($error)) print "<span class=\"error\">".$error."</span><br />\n";
				// No errors found then tell the user all is successful
				else {
					print $pgv_lang["upload_successful"]."<br /><br />";
					$imgsize = getimagesize($MEDIA_DIRECTORY.$_POST["folder".$i].$_FILES['mediafile'.$i]['name']);
					$imgwidth = $imgsize[0]+50;
					$imgheight = $imgsize[1]+50;
					print "<a href=\"#\" onclick=\"return openImage('".urlencode($MEDIA_DIRECTORY.$_POST["folder".$i].$_FILES['mediafile'.$i]['name'])."',$imgwidth, $imgheight);\">".$_FILES['mediafile'.$i]['name']."</a>";
					print"<br /><br />";
				}
			}
		}
		$medialist = get_medialist();
		$action = "filter";
	}
	
	print "<div id=\"uploadmedia\" style=\"display:none\">";
	// Check if Media Directory is writeable or if Media features are enabled
	// If one of these is not true then do not continue
	if (!dir_is_writable($MEDIA_DIRECTORY) || !$MULTI_MEDIA) {
		print "<span class=\"error\"><b>";
		print $pgv_lang["no_upload"];
		print "</b></span><br />";
	}
	// We have the green light to upload media, print the form
	else {
		print "<form name=\"uploadmedia\" enctype=\"multipart/form-data\" method=\"post\" action=\"media.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"upload\" />";
		print "<input type=\"hidden\" name=\"showthumb\" value=\"$showthumb\" />";
		print "<table class=\"list_table $TEXT_DIRECTION width60\">";
		if (!$filesize = ini_get('upload_max_filesize')) $filesize = "2M";
		print "<tr><td class=\"topbottombar\" colspan=\"2\">".$pgv_lang["upload_media"]."<br />".$pgv_lang["max_upload_size"].$filesize."</td></tr>";
		$tab = 1;
		// Print 6 forms for uploading images
		for($i=1; $i<6; $i++) {
			print "<tr>";
				print "<td class=\"descriptionbox width20\">";
				print_help_link("upload_media_help","qm", "upload_media");
				print $pgv_lang["folder"];
				print "</td>";
				print "<td class=\"optionbox\">";
				// Check is done here if the folder specified is not longer than the
				// media depth. If it is, a JS popup informs the user. User cannot leave
				// the input box until corrected. This does not work in Firefox.
				print "<input name=\"folder".$i."\" type=\"text\" size=\"60\" tabindex=\"".$tab++."\" onblur=\"checkpath(this)\" />";
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td class=\"descriptionbox\">";
				print $pgv_lang["media_file"];
				print "</td>";
				print "<td class=\"optionbox\">";
				print "<input name=\"mediafile".$i."\" type=\"file\" size=\"60\" tabindex=\"".$tab++."\" />";
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td class=\"descriptionbox\">";
				print $pgv_lang["thumbnail"];
				print "</td>";
				print "<td class=\"optionbox\">";
				print "<input name=\"thumbnail".$i."\" type=\"file\" tabindex=\"".$tab++."\" size=\"60\" />";
				print "</td>";
			print "</tr>";
	
			// Check for thumbnail generation support
			$ThumbSupport = "";
			if (function_exists("imagecreatefromjpeg") and function_exists("imagejpeg")) $ThumbSupport .= ", JPG";
			if (function_exists("imagecreatefromgif") and function_exists("imagegif")) $ThumbSupport .= ", GIF";
			if (function_exists("imagecreatefrompng") and function_exists("imagepng")) $ThumbSupport .= ", PNG";
			if (!$AUTO_GENERATE_THUMBS) $ThumbSupport = "";
			
			if ($ThumbSupport != "") {
				$ThumbSupport = substr($ThumbSupport, 2);	// Trim off first ", "
				print "<tr><td class=\"descriptionbox wrap\">";
				print_help_link("generate_thumb_help", "qm","generate_thumbnail");
				print $pgv_lang["generate_thumbnail"].$ThumbSupport;
				print "</td><td class=\"optionbox\">";
				print "<input type=\"checkbox\" name=\"genthumb".$i."\" value=\"yes\" tabindex=\"".$tab++."\" />";
				print "</td></tr>";
			}
		}

		// Print the submit button for uploading the media
		print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["upload"]."\" tabindex=\"".$tab++."\" /></td></tr>";
		print "</table></form>";
	}
	print "</div><br />";
	
	// Delete file
	if ($action == "deletefile") {
		print "<table class=\"list_table $TEXT_DIRECTION width50\">";
		print "<tr><td class=\"messagebox wrap\">";
		// Check if file exist. If so, delete it
		if (file_exists($filename)) {
			if (@unlink($filename)) {
				print $pgv_lang["media_file_deleted"]."<br />";
				AddToChangeLog($filename." -- ".$pgv_lang["media_file_deleted"]);
			}
			else {
				print "<span class=\"error\">".$pgv_lang["media_file_not_deleted"]."</span><br />";
				AddToChangeLog($filename." -- ".$pgv_lang["media_file_not_deleted"]);
			}
		}
		
		// Check if thumbnail exists. If so, delete it.
		$thumbnail = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$filename);
		if (file_exists($thumbnail)) {
			if (@unlink($thumbnail)) {
				print $pgv_lang["thumbnail_deleted"]."<br />";
				AddToChangeLog($thumbnail." -- ".$pgv_lang["thumbnail_deleted"]);
			}
			else {
				print "<span class=\"error\">".$pgv_lang["thumbnail_not_deleted"]."</span><br />";
				AddToChangeLog($thumbnail." -- ".$pgv_lang["thumbnail_not_deleted"]);
			}
		}
		
		// Check if file is in media database
		if ($xref != "") {
			if (remove_db_media($xref, $gedfile)) {
				print $pgv_lang["media_record_deleted"]."<br />";
				AddToChangeLog($xref." -- ".$pgv_lang["media_record_deleted"]);
			}
			else {
				print "<span class=\"error\">".$pgv_lang["media_record_not_deleted"]."</span><br />";
				AddToChangeLog($xref." -- ".$pgv_lang["media_record_not_deleted"]);
			}
		}
		// Remove references to media file from gedcom and database
		// Check for XREF and FILE
		if ($xref != "" || $filename) {
			
			// Combine the searchquery of filename and xref
			if ($xref != "") $query[] = $xref;
			$query[] = $filename;

			// Find the INDIS with the mediafile in it
			$foundindis = search_indis($query, true, "OR");
			
			// Now update the record
			if ($xref != "") $regex = "/OBJE @".$xref."@|FILE ".preg_replace("/\//","\\/",$filename)."/";
			else $regex = "/FILE ".preg_replace("/\//","\\/",$filename)."/";
			foreach ($foundindis as $pid => $person) {
				// Check if changes to the record exist
				if (isset($pgv_changes[$pid."_".$person["gedfile"]])) $person["gedcom"] = find_record_in_file($pid);
				$subrecs = get_all_subrecords($person["gedcom"], "", false, false, false);
				$newrec = "0 @$pid@ INDI\r\n";
				foreach($subrecs as $ind=>$subrec) {
			    	if (preg_match($regex, $subrec)==0) $newrec .= $subrec."\r\n";
		    	}
				// If the record has changed save it
				if (isset($newrec)) {
					if (replace_gedrec($pid, $newrec)) {
						print_text("record_updated");
						print "<br />";
						AddToChangeLog(print_text("record_updated"));
					}
					else {
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span><br />";
						AddToChangeLog(print_text("record_updated"));
					}
				}
			}
			
			// Find the FAMS with the mediafile in it
			$foundfams = search_fams($query, true, "OR");
			
			// Now update the record
			if ($xref != "") $regex = "/OBJE @".$xref."@|FILE ".preg_replace("/\//","\\/",$filename)."/";
			else $regex = "/FILE ".preg_replace("/\//","\\/",$filename)."/";
			foreach ($foundfams as $pid => $family) {
				if (isset($pgv_changes[$pid."_".$family["gedfile"]])) $family["gedcom"] = find_record_in_file($pid);
				$subrecs = get_all_subrecords($family["gedcom"], "", false, false, false);
				$newrec = "0 @$pid@ FAM\r\n";
				foreach($subrecs as $fam=>$subrec) {
			    	if (preg_match($regex, $subrec)==0) $newrec .= $subrec."\r\n";
		    	}
				// If the record has changed save it
				if (isset($newrec)) {
					print "<br />"; 
					if (replace_gedrec($pid, $newrec)) {
						print_text("record_updated");
						print "<br />";
						AddToChangeLog(print_text("record_updated"));
					}
					else {
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span><br />";
						AddToChangeLog(print_text("record_not_updated"));
					}
				}
			}			

			// Find the SOURCE with the mediafile in it
			$foundsources = search_sources($query, true, "OR");
			
			// Now update the record
			if ($xref != "") $regex = "/OBJE @".$xref."@|FILE ".preg_replace("/\//","\\/",$filename)."/";
			else $regex = "/FILE ".preg_replace("/\//","\\/",$filename)."/";
			foreach ($foundsources as $pid => $source) {
				if (isset($pgv_changes[$pid."_".$source["gedfile"]])) $source["gedcom"] = find_record_in_file($pid);
				$subrecs = get_all_subrecords($source["gedcom"], "", false, false, false);
				$newrec = "0 @$pid@ SOUR\r\n";
				foreach($subrecs as $src=>$subrec) {
			    	if (preg_match($regex, $subrec)==0) $newrec .= $subrec."\r\n";
		    	}
				// If the record has changed save it
				if (isset($newrec)) {
					print "<br />"; 
					if (replace_gedrec($pid, $newrec)) {
						print_text("record_updated");
						print "<br />";
						AddToChangeLog(print_text("record_updated"));
					}
					else {
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span><br />";
						AddToChangeLog(print_text("record_not_updated"));
					}
				}
			}						
		
			// Remove mediarecord from gedcom
			if ($xref != "") {
				print "<br />";
				if (delete_gedrec($xref)) {
					print_text("record_removed");
					AddToChangeLog(print_text("record_removed"));
				}
				else {
					print "<span class=\"error\">";
					print_text("record_not_removed");
					print "</span>";
					AddToChangeLog(print_text("record_not_removed"));
				}
			}
		}
		$medialist = get_medialist();
		$action = "filter";
		print "</td></tr></table>";
	}
	
	if ($action=="showmedia") {
		$medialist = get_medialist();
		if (count($medialist)>0) {
			print "<table class=\"list_table\">\n";
			print "<tr><td class=\"list_label\">".$pgv_lang["delete"]."</td><td class=\"list_label\">".$pgv_lang["title"]."</td><td class=\"list_label\">".$pgv_lang["gedcomid"]."</td>\n";
			print "<td class=\"list_label\">".$factarray["FILE"]."</td><td class=\"list_label\">".$pgv_lang["highlighted"]."</td><td class=\"list_label\">order</td><td class=\"list_label\">gedcom</td></tr>\n";
			foreach($medialist as $indexval => $media) {
				print "<tr>";
				print "<td class=\"list_value\"><a href=\"addmedia.php?action=delete&m_id=".$media["ID"]."\">delete</a></td>";
				print "<td class=\"list_value\"><a href=\"addmedia.php?action=edit&m_id=".$media["ID"]."\">edit</a></td>";
				print "<td class=\"list_value\">".$media["TITL"]."</td>";
				print "<td class=\"list_value\">";
				print_list_person($media["INDI"], array(get_person_name($media["INDI"]), $GEDCOM));
				print "</td>";
				print "<td class=\"list_value\">".$media["FILE"]."</td>";
				print "<td class=\"list_value\">".$media["_PRIM"]."</td>";
				print "<td class=\"list_value\">".$media["ORDER"]."</td>";
				print "<td class=\"list_value\">".$media["GEDFILE"]."</td>";
				print "</tr>\n";
			}
			print "</table>\n";
		}
	}
	
	
	if ($action=="showmediaform") {
	
	?>
		<script language="JavaScript" type="text/javascript">
		<!--
		var pasteto;
	
		function paste_id(value) {
			pasteto.value=value;
		}
		function findMedia(field) {
			pasteto = field;
			findwin = window.open('find.php?type=media', '', 'left=50,top=50,width=850,height=450,resizable=1,scrollbars=1');
			return false;
		}
		//-->
		</script>
	<?php
	
	//-- add a table and form to easily add new values to the table
		print "<form method=\"post\" name=\"newmedia\" action=\"addmedia.php\">\n";
		print "<input type=\"hidden\" name=\"action\" value=\"newentry\" />\n";
		print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
		print "<table class=\"facts_table\">\n";
		print "<tr><td class=\"facts_label\">".$factarray["FILE"];
		print_help_link("edit_FILE_help","qm");
		print "</td><td class=\"facts_value\"><input type=\"text\" id=\"m_file\" name=\"m_file\" />";
		print_specialchar_link("m_file","");
		print_findmedia_link("m_file");
		print "</td></tr>\n";
		print "<tr><td class=\"facts_label\">".$pgv_lang["extension"];
		print_help_link("edit_FORM_help","qm");
		print "</td><td class=\"facts_value\"><input type=\"text\" id=\"m_ext\" width=\"4\" name=\"m_ext\" value=\"jpeg\" />";
		print_autopaste_link("m_ext", array("avi", "bmp", "gif", "jpeg", "mp3", "ole", "pcx", "tiff", "wav"), false);
		print "<tr><td class=\"facts_label\">".$pgv_lang["title"];
		print_help_link("edit_TITL_help","qm");
		print "</td><td class=\"facts_value\"><input id=\"titl\" type=\"text\" name=\"m_titl\" />";
		print_specialchar_link("titl","");
		print "</td></tr>\n";
		print "<tr><td class=\"facts_label\">".$pgv_lang["gedcom_file"];
		print_help_link("edit_ged_list_help","qm");
		print "</td><td class=\"facts_value\"><select name=\"m_gedfile\">";
		foreach($GEDCOMS as $ged=>$gedarray) {
			print "<option value=\"$ged\"";
			if ($ged==$GEDCOM) print " selected=\"selected\"";
			print ">".PrintReady($gedarray["title"])."</option>\n";
		}
		print "</select></td></tr>\n";
		print "</table>\n";
		print_add_layer("NOTE");
	
		print "<center><br /><input type=\"submit\" value=\"".$pgv_lang["add_media_button"]."\" /><br /><br />\n";
		print "</form>\n";
	}
	
	if ($action=="injectmedia") {
	
		$medialist = get_db_media_list();
	
		// check for already imported media
		$test = find_record_in_file($medialist[0]["XREF"]);
		if ($test) {
			print "<div align=\"center\" class=\"error\" ><h2>This gedcom has already had the media information inserted into it, operation aborted</h3></div>";
		} else {
	
			$ct = 0;
			$nct = 0;
			foreach($medialist as $indexval => $media) {
				$mediarec = "\r\n0 @".$media["XREF"]."@ OBJE";
				$mediarec .= "\r\n1 FILE ".$media["FILE"];
				$mediarec .= "\r\n1 TITL ".$media["TITL"];
				$mediarec .= "\r\n1 FORM ".$media["FORM"];
				if (strlen($media["NOTE"])>0) {$mediarec .= "\r\n".$media["NOTE"]; $nct++;};
				$pos1 = strrpos($fcontents, "0");
				$fcontents = substr($fcontents, 0, $pos1).trim($mediarec)."\r\n".substr($fcontents, $pos1);
				write_file();
				$ct++;
			}
			print "<center>$ct media items added, $nct with notes</center>";
	
			$ct = 0;
			$nct = 0;
			$mappinglist = get_db_mapping_list();
			$oldindi = "";
			for ($i=0; $i < count($mappinglist); $i++) {
				$media = $mappinglist[$i];
				$indi = $media["INDI"];
				if ($indi != $oldindi) {
					if ($i > 0) { db_replace_gedrec($oldindi, $indirec);};
					$oldindi = $indi;
					$indirec = find_record_in_file($indi);
				}
			    if (strlen($media["NOTE"])>0) {$indirec .= "\r\n".trim($media["NOTE"]); $nct++;};
	
			}
			db_replace_gedrec($indi, $indirec);
	
			print "<center>$ct link items added, $nct with notes</center>";
			print "<p><center>".$pgv_lang["adds_completed"]."<center></p><br /><br />\n";
		}
	}
	
	/**
	 * Generate Move To flyout menu
	 *
	 * Access control to directories are in this routine
	 *
	 * @param mixed $dirlist array() list of subdirectories
	 * @param string $directory string current working directory
	 * @param sring $filename filename to generate this menu and links for
	 */
	function print_link_menu($mediaid) {
		global $pgv_lang;
	
		// main link displayed on page
		$menu = array();
		$menu["label"] = $pgv_lang["set_link"];
		$menu["link"] = "#";
		$menu["class"] = "thememenuitem";
		$menu["hoverclass"] = "";
		$menu["submenuclass"] = "submenu";
		$menu["flyout"] = "left";
		$menu["items"] = array();
	
		$submenu = array();
		$submenu["label"] = $pgv_lang["to_person"];
		$submenu["link"] = "#";
		$submenu["hoverclass"] = "submenuitem";
		$submenu["onclick"] = "return ilinkitem('$mediaid','person')";
		$submenu["class"] = "submenuitem";
		$menu["items"][] = $submenu;
	
		$submenu = array();
		$submenu["label"] = $pgv_lang["to_family"];
		$submenu["link"] = "#";
		$submenu["hoverclass"] = "submenuitem";
		$submenu["onclick"] = "return ilinkitem('$mediaid','family')";
		$submenu["class"] = "submenuitem";
		$menu["items"][] = $submenu;
	
		$submenu = array();
		$submenu["label"] = $pgv_lang["to_source"];
		$submenu["link"] = "#";
		$submenu["hoverclass"] = "submenuitem";
		$submenu["onclick"] = "return ilinkitem('$mediaid','source')";
		$submenu["class"] = "submenuitem";
		$menu["items"][] = $submenu;
	
		print_menu($menu);
	}
	
	/**
	 * Generate Move To flyout menu
	 *
	 * Access control to directories are in this routine
	 *
	 * @param mixed $dirlist array() list of subdirectories
	 * @param string $directory string current working directory
	 * @param sring $filename filename to generate this menu and links for
	 */
	function print_move_to_menu($dirlist,$directory, $filename, $xref) {
		global $level, $MEDIA_DIRECTORY_LEVELS, $pgv_lang, $thumbget;
	
		// main link displayed on page
		$menu = array();
		$menu["label"] = $pgv_lang["move_to"];
		$menu["link"] = "#";
		$menu["class"] = "";
		$menu["hoverclass"] = "thememenuitem_hover";
		$menu["submenuclass"] = "submenu";
		$menu["flyout"] = "left";
		$menu["items"] = array();
	
		// add option to move file up a level
		// Sanity check 2 Don't allow file move above the main media directory
		if ($level>0) {
			$submenu = array();
			$submenu["label"] = "<b>&nbsp;&nbsp;&nbsp;<--&nbsp;&nbsp;&nbsp;</b>";
			$submenu["link"] = "media.php?directory=$directory&action=moveto&level=$level&movetodir=..&movefile=$filename&xref=$xref".$thumbget;
			$submenu["class"] = "submenuitem";
			$menu["items"][] = $submenu;
	
		}
		// Add lower level directories
		// Sanity check 3 Don't list directories which are at a lower level
		//                than configured in the xxxxx_conf.php
		if ($level < $MEDIA_DIRECTORY_LEVELS) {
			foreach ($dirlist as $indexval => $dir) {
				$submenu = array();
				$submenu["label"] = $dir;
				$submenu["link"] = "media.php?directory=$directory&action=moveto&level=$level&movetodir=$dir&movefile=$filename&xref=$xref".$thumbget;
				$submenu["class"] = "submenuitem";
				$menu["items"][] = $submenu;
			}
		}
		print_menu($menu);
	}
	
	if ($action == "filter") {
		global $dirs;
		
		// Get the list of media items
		$medialist = get_medialist(true);
		/**
		 * This is the default action for the page
		 *
		 * Displays a list of dirs and files. Displaying only
		 * thumbnails as the images may be large and we do not want large delays
		 * while adminstering the file structure
		 *
		 * @name $action->filter
		 */
		// Show link to previous folder
		if ($level>0) {
			$levels = preg_split("'/'", $directory);
			$pdir = "";
			for($i=0; $i<count($levels)-2; $i++) $pdir.=$levels[$i]."/";
			$levels = preg_split("'/'", $thumbdir);
			$pthumb = "";
			for($i=0; $i<count($levels)-2; $i++) $pthumb.=$levels[$i]."/";
			$uplink = "<a href=\"media.php?directory=$pdir&thumbdir=$pthumb&level=".($level-1).$thumbget."\">&nbsp;&nbsp;&nbsp;&lt;-- $pdir&nbsp;&nbsp;&nbsp;</a>\n";
		}
		
		// Start of media directory table
		print "<table class=\"list_table width50 $TEXT_DIRECTION\">";
	
		// Tell the user where he is
		print "<tr><td class=\"topbottombar\" colspan=\"2\">".$pgv_lang["current_dir"].substr($directory,0,-1)."</td></tr>";

		// display the directory list
		if (count($dirs) || $level) {
			sort($dirs);
			if ($level){
				print "<tr><td class=\"optionbox $TEXT_DIRECTION\" colspan=\"2\">";
				print $uplink."</td></tr>";
			}
			print "<tr><td class=\"descriptionbox $TEXT_DIRECTION\" colspan=\"2\">";
			print "<a href=\"media.php?directory=$directory&amp;thumbdir=$directory&amp;level=".$level.$thumbget."&amp;external_links=http\">External media</a>";
			print "</td></tr>";
			foreach ($dirs as $indexval => $dir) {
				print "<tr><td class=\"descriptionbox $TEXT_DIRECTION\">";
				print "<a href=\"media.php?directory=$directory$dir/&thumbdir=$directory$dir/&level=".($level+1).$thumbget."\">$dir</a>";
				print "</td>";
				print "<td class=\"optionbox $TEXT_DIRECTION\">";
				// Delete directory option
				print "<form name=\"blah\" action=\"media.php\" method=\"post\">";
				print "<input type=\"hidden\" name=\"directory\" value=\"".$directory.$dir."/\" />";
				print "<input type=\"hidden\" name=\"thumbdir\" value=\"".$directory.$dir."/\" />";
				print "<input type=\"hidden\" name=\"parentdir\" value=\"".$directory."\" />";
				print "<input type=\"hidden\" name=\"level\" value=\"".($level)."\" />";
				print "<input type=\"hidden\" name=\"dir\" value=\"".$dir."\" />";
				print "<input type=\"hidden\" name=\"action\" value=\"deletedir\" />";
				print "<input type=\"image\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]."\" onclick=\"return confirm('".$pgv_lang["confirm_folder_delete"]."');\">";
				print "</form></td></tr>";
				
			}
		}
		print "<tr><td class=\"list_value $TEXT_DIRECTION\">";
		// Form for ceating a new directory
		// Checks admin user can write and is not trying to create lower level dir
		// than the configured number of levels
		if ($isadmin && $fileaccess && ($level < $MEDIA_DIRECTORY_LEVELS)) {
			print "<form action=\"media.php\" method=\"get\">";
			print "<input type=\"hidden\" name=\"directory\" value=\"".$directory."\" />";
			print "<input type=\"hidden\" name=\"thumbdir\" value=\"".$thumbdir."\" />";
			print "<input type=\"hidden\" name=\"level\" value=\"".$level."\" />";
			print "<input type=\"hidden\" name=\"action\" value=\"newdir\" />";
			print "<input type=\"submit\" value=\"".$pgv_lang["add"]."\" /></td>";
			print "<td class=\"list_value $TEXT_DIRECTION\"><input type=\"text\" name=\"newdir\"/></form>";
		}
		print "</td></tr></table>";
		$applyfilter = ($filter != "");
		print "<br />";
		
		// display the images TODO x across if lots of files??
		if (count($medialist)) {
			print "\n\t<table class=\"list_table\">";
			foreach ($medialist as $indexval => $media) {
				
				// Check if the media belongs to the current folder
				preg_match_all("/\//", $media["FILE"], $hits);
				$ct = count($hits[0]);
				
				if (($ct <= $level+1 && $external_links != "http") || (stristr($media["FILE"],"://") && $external_links == "http")) {
					// simple filter to reduce the number of items to view
					if ($applyfilter) $isvalid = (strpos(str2lower($media["FILE"]),str2lower($filter)) !== false);
					else $isvalid = true;
					
					if ($isvalid) {
						if (is_file($media["FILE"]) && filesize($media["FILE"]) != 0){
							$imgsize = getimagesize($media["FILE"]);
							if ($imgsize) {
								$imgwidth = $imgsize[0]+50;
								$imgheight = $imgsize[1]+50;
							}
							else {
								$imgwidth = 300;
								$imgheight = 300;
							}
						}
						else {
							$imgsize = array();
							$imgsize[0] = false;
							$imgsize[1] = false;
							$imgwidth = 0;
							$imgheight = 0;
						}
						
						// Show column with file operations options
						print "<tr><td class=\"optionbox $TEXT_DIRECTION\">";
						
						// Move To menu
						// Sanity check 1 user must be admin and no point doing file admin if all in one dir
						if ( $MEDIA_DIRECTORY_LEVELS && $fileaccess && count($pgv_changes) == 0) print_move_to_menu($dirs,$directory,$media["FILE"], $media["XREF"]);
	
						// NOTE: Edit File
						if ($media["XREF"] != "") print "<a href=\"javascript:".$pgv_lang["edit"]."\" onclick=\"window.open('addmedia.php?action=editmedia&amp;pid=".$media["XREF"]."', '', 'top=50,left=50,width=900,height=650,resizable=1,scrollbars=1'); return false;\">".$pgv_lang["edit"]."</a><br />";
						
						// Delete File
						// do not forget to remove links to this file from the gedcom and DB
						if (!stristr($media["FILE"], "://")) print "<a href=\"media.php?action=deletefile&amp;showthumb=".$showthumb."&amp;filename=".$media["FILE"]."&amp;xref=".$media["XREF"]."&amp;gedfile=".$media["GEDFILE"]."\" onclick=\"return confirm('".$pgv_lang["confirm_delete_file"]."');\">".$pgv_lang["delete_file"]."</a><br />";
						
						// Set Link
						// Only set link on media that is in the DB
						// print_help_link("admin_set_link_help","qm");
						if ($media["XREF"] != "") print_link_menu($media["XREF"]);
						
						// Generate thumbnail
						$ct = preg_match("/\.([^\.]+)$/", $media["FILE"], $match);
						if ($ct>0) $ext = strtolower(trim($match[1]));
						if (!isset($media["THUMB"]) && ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png")) print "<a href=\"media.php?directory=$directory&amp;action=thumbnail&amp;all=0&amp;level=$level&amp;file=".$media["FILE"]."$thumbget\">".$pgv_lang["gen_thumb"]."</a>";
						
						// NOTE: Close column for file operations
						print "</td>";
						
						//-- thumbnail field
						if ($showthumb) {
							print "\n\t\t\t<td class=\"optionbox $TEXT_DIRECTION\">";
							// if (isset($media["THUMB"])) {
								// $thumbnail = thumbnail_file($media["FILE"]);
								print "<a href=\"#\" onclick=\"return openImage('".preg_replace("/'/", "\'", rawurlencode($media["FILE"]))."',$imgwidth, $imgheight);\"><img src=\"".filename_encode($media["THUMB"])."\" border=\"0\" alt=\"";
								if (isset($media["TITL"])) print $media["TITL"];
								else print "";
								print "\"/></a>\n";
							//}
							// else print "&nbsp;";
							print "</td>";
						}
						
						//-- name and size field
						print "\n\t\t\t<td class=\"optionbox $TEXT_DIRECTION\">";
						if ($media["TITL"] != "") print PrintReady("<b>".$media["TITL"]."</b> (".$media["XREF"].")")."<br />";
						if (!file_exists($media["FILE"]) && !stristr($media["FILE"], "://")) print filename_encode($media["FILE"])."<br /><span class=\"error\">".$pgv_lang["file_not_exists"]."</span><br />";
						else if (!stristr($media["FILE"], "://")) {
							print "<a href=\"#\" onclick=\"return openImage('".preg_replace("/'/", "\'", filename_encode($media["FILE"]))."',$imgwidth, $imgheight);\">".filename_encode($media["FILE"])."</a>";
							print "<br /><sub>&nbsp;&nbsp;".$pgv_lang["image_size"]." -- ".$imgsize[0]."x".$imgsize[1]."</sub><br />";
						}
						else print filename_encode($media["FILE"])."<br />";
						if ($media["LINKED"]) {
							print $pgv_lang["media_linked"]."<br />";
							foreach ($media["LINKS"] as $indi => $type) {
								if ($type=="INDI") {
						            print " <br /><a href=\"individual.php?pid=".$indi."&amp;ged=".$GEDCOM."\"> ".$pgv_lang["view_person"]." - ".PrintReady(get_person_name($indi))."</a>";
								}
								else if ($type=="FAM") {
						           	print "<br /> <a href=\"family.php?famid=".$indi."&amp;ged=".$GEDCOM."\"> ".$pgv_lang["view_family"]." - ".PrintReady(get_family_descriptor($indi))."</a>";
								}
								else if ($type=="SOUR") {
						            	print "<br /> <a href=\"source.php?sid=".$indi."&amp;ged=".$GEDCOM."\"> ".$pgv_lang["view_source"]." - ".PrintReady(get_source_descriptor($indi))."</a>";
								}
								//-- no reason why we might not get media linked to media. eg stills from movie clip, or differents resolutions of the same item
								else if ($type=="OBJE") {
									//-- TODO add a similar function get_media_descriptor($gid)
								}
							}
						}
						else {
							print $pgv_lang["media_not_linked"];
						}
						print "\n\t\t\t</td></tr>";
					}
				}
			}
			print "\n\t\t</tr>\n\t</table><br />";
		}
	}
	
	?>
<?php
}
else print $pgv_lang["media_folder_corrupt"];
print_footer();
?>
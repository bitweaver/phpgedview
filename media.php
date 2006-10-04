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
 * @version $Id: media.php,v 1.4 2006/10/04 12:07:54 lsces Exp $
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
 *  $directory = Current directory, starting with $MEDIA_DIRECTORY.  Has trailing "/".
 *  $dirs = list of subdirectories within current directory.  Built with medialist.
 */

require("config.php");
require_once("includes/functions_print_lists.php");
require_once("includes/functions_print_facts.php");
require_once("includes/functions_edit.php");

if (isset($_REQUEST['DEBUG'])) $DEBUG = $_REQUEST['DEBUG'];
else $DEBUG = false;

/**
 * This functions checks if an existing directory is physically writeable
 * The standard PHP function only checks for the R/O attribute and doesn't
 * detect authorisation by ACL.
 */
function dir_is_writable($dir) {
	$err_write = false;
	$handle = @fopen(filename_decode($dir."x.y"),"w+");
	if	($handle) {
		$i = fclose($handle);
		$err_write = true;
		@unlink(filename_decode($dir."x.y"));
	}
	return($err_write);
}

// Get rid of extra slashes in input variables
if (isset($filename)) $filename = stripslashes($filename);
if (isset($directory))$directory = stripslashes($directory);
if (isset($movetodir))$movetodir = stripslashes($movetodir);
if (isset($movefile))$movefile = stripslashes($movefile);

if (!isset($action)) {
	if (isset($search)) $action = "filter";
	else $action="";
}
if (empty($action)) $action="filter";

if (!isset($media)) $media="";
if (!isset($filter) || strlen($filter)<2) $filter="";
if (!isset($directory)) $directory = $MEDIA_DIRECTORY;
if (!isset($level)) $level=0;
if (!isset($start)) $start = 0;
if (!isset($max)) $max = 20;

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

$thumbdir = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $directory);


//-- check for admin once (used a bit in this script)
$isadmin =  userIsAdmin(getUserName());

//-- only allow users with Admin privileges to access script.
if (!$isadmin || !$ALLOW_EDIT_GEDCOM) {
	header("Location: login.php?url=media.php");
	exit;
}

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
	 	if (width>screenW-100) width=screenW-100;
	 	if (height>screenH-110) height=screenH-120;
		if ((filename.search(/\.je?pg$/gi)!=-1)||(filename.search(/\.gif$/gi)!=-1)||(filename.search(/\.png$/gi)!=-1)||(filename.search(/\.bmp$/gi)!=-1)) window.open('imageview.php?filename='+filename,'_blank','top=50,left=50,height='+height+',width='+width+',scrollbars=1,resizable=1');
		else window.open(unescape(filename),'_blank','top=50,left=50,height='+height+',width='+width+',scrollbars=1,resizable=1');
		return false;
	}

	function ilinkitem(mediaid, type) {
		window.open('inverselink.php?mediaid='+mediaid+'&linkto='+type+'&'+sessionname+'='+sessionid, '_blank', 'top=50,left=50,width=400,height=300,resizable=1,scrollbars=1');
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

	function showchanges() {
		window.location = '<?php print $SCRIPT_NAME."?show_changes=yes&directory=".$directory; ?>';
	}

//-->
</script>
<script src="./js/phpgedview.js" language="JavaScript" type="text/javascript"></script>
<form name="managemedia" method="post" onsubmit="return checknames(this);" action="media.php">
	<input type="hidden" name="directory" value="<?php $action == "deletedir"?print $parentdir:print $directory; ?>" />
	<input type="hidden" name="thumbdir" value="<?php print $thumbdir; ?>" />
	<input type="hidden" name="level" value="<?php print $level; ?>" />
	<input type="hidden" name="all" value="true" />
	<input type="hidden" name="subclick" />
	<table class="fact_table center width90 <?php print $TEXT_DIRECTION; ?>">
	<tr><td class="topbottombar" colspan="6"><?php print_help_link("manage_media_help","qm","manage_media");print $pgv_lang["manage_media"];?></td></tr>
	<!-- // NOTE: Filter options -->
	<tr><td class="descriptionbox wrap width25"><?php print_help_link("filter_help","qm","filter"); print $pgv_lang["filter"];?></td>
	<td class="optionbox wrap"><input type="text" name="filter" value="<?php if(isset($filter)) print $filter; ?>"/><br /><input type="submit" name="search" value="<?php print $pgv_lang["filter"];?>" onclick="this.form.subclick.value=this.name" />&nbsp;&nbsp;&nbsp;<input type="submit" name="reset" value="<?php print $pgv_lang["reset"];?>" onclick="this.form.subclick.value=this.name" /></td>

	<!-- // NOTE: Upload media files -->
	<td class="descriptionbox wrap width25"><?php print_help_link("upload_media_help","qm","upload_media"); print $pgv_lang["upload_media"];?></td>
	<td class="optionbox wrap"><?php print "<a href=\"#\" onclick=\"expand_layer('uploadmedia');\">".$pgv_lang["upload_media"]."</a>";?></td></tr>

	<!-- // NOTE: Show thumbnails -->
	<td class="descriptionbox wrap width25"><?php print_help_link("show_thumb_help","qm", "show_thumbnail");?><?php print $pgv_lang["show_thumbnail"];?></td>
	<td class="optionbox wrap"><input type="checkbox" name="showthumb" value="true" <?php if ($showthumb) print "checked=\"checked\""; ?> onclick="submit();" /></td>

	<!-- // NOTE: Add media -->
	<td class="descriptionbox wrap width25"><?php print_help_link("add_media_help", "qm"); ?><?php print $pgv_lang["add_media_lbl"]; ?></td>
	<td class="optionbox wrap">
	<a href="javascript: <?php echo $pgv_lang["add_media_lbl"]; ?>" onclick="window.open('addmedia.php?action=showmediaform&amp;linktoid=new', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;"> <?php echo $pgv_lang["add_media"]; ?></a>
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
		print "<table class=\"list_table $TEXT_DIRECTION width50\">";
		print "<tr><td class=\"messagebox wrap\">";
		// security checks, no names with . .. / \ in them
		// add more if required
		$clean = !empty($newdir);
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

		$action="filter";
		print "</td></tr></table>";
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
		if (is_dir(filename_decode($directory))) {
		   	$handle = opendir(filename_decode($directory));
		   	$files = array();
		   	while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $BADMEDIA)) $files[] = $file;
		   	}
		}
		else {
			print "<div class=\"error\">".$directory." ".$pgv_lang["directory_not_exist"]."</div>";
			AddToLog($directory." ".$pgv_lang["directory_not_exist"]);
		}

	   	// Thumbs directory check
	   	$thumbDirExists = false;
	   	if (@is_dir(filename_decode($thumbdir))) {
		   	$thumbDirExists = true;
		   	$handle = opendir(filename_decode($thumbdir));
		   	$thumbfiles = array();
		   	while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $BADMEDIA)) $thumbfiles[] = $file;
		   	}
	   		closedir($handle);
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
			if (file_exists(filename_decode($directory."index.php"))) @unlink(filename_decode($directory."index.php"));
			if (is_dir(filename_decode($directory))) $resdir = @rmdir(filename_decode(substr($directory,0,-1)));
			$resthumb = true;
			if ($thumbDirExists) {
				if (file_exists(filename_decode($thumbdir."index.php"))) @unlink(filename_decode($thumbdir."index.php"));
				if (@is_dir(filename_decode($thumbdir))) $resthumb = @rmdir(filename_decode(substr($thumbdir,0,-1)));
			}
			if ($resdir && $resthumb) {
				print $pgv_lang["delete_dir_success"];
				AddToLog($directory." -- ".$pgv_lang["delete_dir_success"]);
			} else {
				if (!$resdir) {
					print "<div class=\"error\">".$pgv_lang["media_not_deleted"]."</div>";
					AddToLog($directory." -- ".$pgv_lang["media_not_deleted"]);
				} else {
					print $pgv_lang["media_deleted"];
					AddToLog($directory." -- ".$pgv_lang["media_deleted"]);
				}
				if (!$resthumb) {
					print "<div class=\"error\">".$pgv_lang["thumbs_not_deleted"]."</div>";
					AddToLog($thumbdir." -- ".$pgv_lang["thumbs_not_deleted"]);
				} else {
					print $pgv_lang["thumbs_deleted"];
					AddToLog($thumbdir." -- ".$pgv_lang["thumbs_deleted"]);
				}

			}
		}

		$directory = $parentdir;
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
		print "<table class=\"list_table $TEXT_DIRECTION width50\">";
		print "<tr><td class=\"messagebox wrap\">";
		// just in case someone fashions the right url

		//-- check if the file is used in more than one gedcom
		//-- do not allow it to be moved if it is
		$myFile = str_replace($MEDIA_DIRECTORY, "", $directory.$movefile);
		$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_file LIKE '%".$DBCONN->escape($myFile)."'";
		$res = $gGedcom->mDb->query($sql);
		$onegedcom = true;
		while($row=$res->fetchRow()) {
			if ($row['m_gedfile']!=$GEDCOMS[$GEDCOM]['id']) $onegedcom = false;
		}
		if (!$onegedcom) {
			print "<span class=\"error\">".$pgv_lang["multiple_gedcoms"]."<br /><br /><b>".$pgv_lang["media_file_not_moved"]."</b></span><br />";
		}

		while ($isadmin && $fileaccess && $onegedcom) {
			$exists = false;

			// file details
			$fileName = $movefile;
			$moveFromDir = $directory;
			$moveToDir = $moveFromDir.$movetodir."/";
			if ($movetodir=="..") {
				$moveToDir = "";
				$folders = explode("/", $moveFromDir);
				for($i=0; $i<count($folders)-2; $i++) {
					$moveToDir .= $folders[$i]."/";
				}
			}

			// and the thumbnail
			$moveFromThumbDir = str_replace("$MEDIA_DIRECTORY",$MEDIA_DIRECTORY."thumbs/",$moveFromDir);
			$moveToThumbDir = str_replace("$MEDIA_DIRECTORY",$MEDIA_DIRECTORY."thumbs/",$moveToDir);

			// Check if the files do not yet exist on the new location
			$moveFileFrom = $moveFromDir.$fileName;
			$moveFileTo = $moveToDir.$fileName;
			$moveThumbFrom = $moveFromThumbDir.$fileName;
			$moveThumbTo = $moveToThumbDir.$fileName;

			$exists = false;
			if (file_exists(filename_decode($moveFileTo))) {
				print "<span class=\"error\">".$pgv_lang["media_exists"]."</span><br />";
				AddToLog($moveFileTo." ".$pgv_lang["media_exists"]);
				$exists = true;
			}
			if (file_exists(filename_decode($moveThumbTo))) {
				print "<span class=\"error\">".$pgv_lang["media_thumb_exists"]."</span><br />";
				AddToLog($moveThumbTo." ".$pgv_lang["media_thumb_exists"]);
				$exists = true;
			}

			if (!$exists) {
				// Moving the thumbnail file if it exists
				// no error if admin chooses not to have a thumbnail file
				$thumbOK = true;
				if (file_exists(filename_decode($moveThumbFrom))) {
					if (is_dir(filename_decode($moveToThumbDir))) {
						$thumbOK = @rename(filename_decode($moveThumbFrom), filename_decode($moveThumbTo));
				 	} else {
						if (mkdir(filename_decode($moveToThumbDir),0777)) {
							$thumbOK = @rename(filename_decode($moveThumbFrom), filename_decode($moveThumbTo));
						} else {
							// abort the whole operation to keep the directory structure valid
							print $moveToThumbDir." ".$pgv_lang["no_thumb_dir"]."<br />";
							AddToLog($moveToThumbDir." ".$pgv_lang["no_thumb_dir"]);
							break;
						}
					}
				}

				// no major error from thumbnail so move the file
				$fileOK = @rename(filename_decode($moveFileFrom), filename_decode($moveFileTo));

				if ($thumbOK && $fileOK) {
					print $pgv_lang["move_file_success"]."<br />";
					AddToLog($moveFileTo." ".$pgv_lang["move_file_success"]);
				} else break;

				// also update the gedcom record
				if (!empty($_REQUEST["xref"])) {
					//-- we don't need to update the database, the database will be
					//-- updated automatically when the changes are accepted
					if (isset($pgv_changes[$_REQUEST['xref']."_".$GEDCOM])) $objerec = find_record_in_file($_REQUEST['xref']);
					else $objerec = find_media_record($_REQUEST['xref']);
					if (!empty($objerec)) {
						$objerec = preg_replace("/ FILE ([^\r\n]*)/", " FILE ".$moveFileTo, $objerec);
						replace_gedrec($_REQUEST['xref'], $objerec);

						// This can't be undone through the normal "reject change",
						// so we'll auto-accept all changes to this media object
						// regardless of config settings
						include_once('includes/functions_import.php');
						accept_changes($_REQUEST['xref']."_".$GEDCOM);
					}
				}
			}
			break;		// Everything is OK
		}
		$action = "filter";
		print "</td></tr></table>";
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
				if (!($MEDIA_EXTERNAL && stristr($filename, "://"))) {
					$thumbnail = str_replace("$MEDIA_DIRECTORY",$MEDIA_DIRECTORY."thumbs/",check_media_depth($media["FILE"], "NOTRUNC"));
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
		}
		else if ($all == false) {
			if (!($MEDIA_EXTERNAL && stristr($filename, "://"))) {
				$thumbnail = str_replace("$MEDIA_DIRECTORY",$MEDIA_DIRECTORY."thumbs/",check_media_depth($filename, "NOTRUNC"));
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
		}
		$action = "filter";
		print "</td></tr></table>";
	}

	// Upload media items
	if ($action == "upload") {
		print "<table class=\"list_table $TEXT_DIRECTION width50\">";
		print "<tr><td class=\"messagebox wrap\">";
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		?>
		<?php
		for($i=1; $i<6; $i++) {
			if (!empty($_FILES['mediafile'.$i]["name"]) || !empty($_FILES['thumbnail'.$i]["name"])) {
				$folderName = "";
				if (!empty($_POST["folder".$i])) $folderName = $_POST["folder".$i];
				// Validate and correct folder names
				$folderName = check_media_depth($folderName."/y.z", "BACK");
				$folderName = dirname($folderName)."/";
				$thumbFolderName = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folderName);

				if (!empty($folderName)) $_SESSION["upload_folder"] = $folderName;

				$error = "";

				// Determine file name on server
				if (!empty($_POST["filename".$i])) {
					$parts = pathinfo($_POST["filename".$i]);
					$mediaFile = $parts["basename"];
					if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
						if (!empty($_FILES["mediafile".$i]["name"])) {
							$parts = pathinfo($_FILES["mediafile".$i]["name"]);
						} else {
							$parts = pathinfo($_FILES["thumbnail".$i]["name"]);
						}
						$mediaFile .= ".".$parts["extension"];
					}
				} else {
					if (!empty($_FILES["mediafile".$i]["name"])) {
						$parts = pathinfo($_FILES["mediafile".$i]["name"]);
					} else {
						$parts = pathinfo($_FILES["thumbnail".$i]["name"]);
					}
					$mediaFile = $parts["basename"];
				}

				if (!empty($_FILES["mediafile".$i]["name"])) {
					// Copy main media file into the destination directory
					if (!move_uploaded_file($_FILES["mediafile".$i]["tmp_name"], filename_decode($folderName.$mediaFile))) {
						// the file cannot be copied
						$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES["mediafile".$i]["error"]]."<br />";
					} else {
						// Set file permission to read/write for everybody
//						@chmod(filename_decode($folderName.$mediaFile), 0644);
						AddToLog("Media file ".$folderName.$mediaFile." uploaded by >".getUserName()."<");
					}
				}
				if ($error=="" && !empty($_FILES["thumbnail".$i]["name"])) {
					// Copy user-supplied thumbnail file into the destination directory
					if (!move_uploaded_file($_FILES["thumbnail".$i]["tmp_name"], filename_decode($thumbFolderName.$mediaFile))) {
						// the file cannot be copied
						$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES["thumbnail".$i]["error"]]."<br />";
					} else {
						// Set file permission to read/write for everybody
//						@chmod(filename_decode($thumbFolderName.$mediaFile), 0644);
						AddToLog("Media file ".$thumbFolderName.$mediaFile." uploaded by >".getUserName()."<");
					}
				}
				if ($error=="" && empty($_FILES["mediafile".$i]["name"]) && !empty($_FILES["thumbnail".$i]["name"])) {
					// Copy user-supplied thumbnail file into the main destination directory
					if (!copy(filename_decode($thumbFolderName.$mediaFile), filename_decode($folderName.$mediaFile))) {
						// the file cannot be copied
						$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES["thumbnail".$i]["error"]]."<br />";
					} else {
						// Set file permission to read/write for everybody
//						@chmod(filename_decode($folderName.$mediaFile), 0644);
						AddToLog("Media file ".$folderName.$mediaFile." uploaded by >".getUserName()."<");
					}
				}
				if ($error=="" && !empty($_FILES["mediafile".$i]["name"]) && empty($_FILES["thumbnail".$i]["name"])) {
					if (!empty($_POST['genthumb'.$i]) && ($_POST['genthumb'.$i]=="yes")) {
						// Generate thumbnail from main image
						$ct = preg_match("/\.([^\.]+)$/", $mediaFile, $match);
						if ($ct>0) {
							$ext = strtolower(trim($match[1]));
							if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png") {
								$okThumb = generate_thumbnail($folderName.$mediaFile, $thumbFolderName.$mediaFile, "OVERWRITE");
								$thumbnail = $thumbFolderName.$mediaFile;
								if (!$okThumb) {
									$error .= print_text("thumbgen_error",0,1);
								} else {
									// Set file permission on thumbnail to read/write for everybody
//									@chmod(filename_decode($thumbFolderName.$mediaFile), 0644);
									print_text("thumb_genned");
									print "<br />";
									AddToLog("Media thumbnail ".$thumbFolderName.$mediaFile." generated by >".getUserName()."<");
								}
							}
						}
					}
				}
				// Let's see if there are any errors generated and print it
				if (!empty($error)) print "<span class=\"error\">".$error."</span><br />\n";
				// No errors found then tell the user all is successful
				else {
					print $pgv_lang["upload_successful"]."<br /><br />";
					$imgsize = findImageSize($folderName.$mediaFile);
					$imgwidth = $imgsize[0]+40;
					$imgheight = $imgsize[1]+150;
					print "<a href=\"#\" onclick=\"return openImage('".rawurlencode($folderName.$mediaFile)."',$imgwidth, $imgheight);\">".$mediaFile."</a>";
					print"<br /><br />";
				}
			}
		}
		$medialist = get_medialist();
		$action = "filter";
		print "</td></tr></table>";
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
		print "<table class=\"list_table $TEXT_DIRECTION width90\">";
		if (!$filesize = ini_get('upload_max_filesize')) $filesize = "2M";
		print "<tr><td class=\"topbottombar\" colspan=\"2\">".$pgv_lang["upload_media"]."<br />".$pgv_lang["max_upload_size"].$filesize."</td></tr>";
		$tab = 1;
		// Print the submit button for uploading the media
		print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["upload"]."\" tabindex=\"".$tab++."\" /></td></tr>";
		// Print 5 forms for uploading images
		for($i=1; $i<6; $i++) {
			print "<tr>";
				print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
				print_help_link("upload_media_file_help","qm", "upload_media");
				print $pgv_lang["media_file"];
				print "</td>";
				print "<td class=\"optionbox $TEXT_DIRECTION wrap\">";
				print "<input name=\"mediafile".$i."\" type=\"file\" size=\"40\" tabindex=\"".$tab++."\" />";
				if ($i==1) print "<br /><sub>".$pgv_lang["use_browse_advice"]."</sub>";
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
				print "<tr><td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
				print_help_link("generate_thumb_help", "qm","generate_thumbnail");
				print $pgv_lang["auto_thumbnail"];
				print "</td><td class=\"optionbox $TEXT_DIRECTION wrap\">";
				print "<input type=\"checkbox\" name=\"genthumb".$i."\" value=\"yes\" checked tabindex=\"".$tab++."\" />";
				print "&nbsp;&nbsp;&nbsp;".$pgv_lang["generate_thumbnail"].$ThumbSupport;
				print "</td></tr>";
			}
			print "<tr>";
				print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
				print_help_link("upload_thumbnail_file_help","qm", "upload_media");
				print $pgv_lang["thumbnail"];
				print "</td>";
				print "<td class=\"optionbox $TEXT_DIRECTION wrap\">";
				print "<input name=\"thumbnail".$i."\" type=\"file\" tabindex=\"".$tab++."\" size=\"40\" />";
				if ($i==1) print "<br /><sub>".$pgv_lang["use_browse_advice"]."</sub>";
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
				print_help_link("upload_server_file_help","qm", "upload_media");
				print $pgv_lang["server_file"];
				print "</td>";
				print "<td class=\"optionbox $TEXT_DIRECTION wrap\">";
				print "<input name=\"filename".$i."\" type=\"text\" tabindex=\"".$tab++."\" size=\"40\" />";
				if ($i==1) print "<br /><sub>".$pgv_lang["server_file_advice"]."</sub>";
				print "</td>";
			print "</tr>";
			if ($MEDIA_DIRECTORY_LEVELS>0) {
				print "<tr>";
					print "<td class=\"descriptionbox $TEXT_DIRECTION wrap width25\">";
					print_help_link("upload_server_folder_help","qm", "upload_media");
					print $pgv_lang["server_folder"];
					print "</td>";
					print "<td class=\"optionbox $TEXT_DIRECTION wrap\">";
					// Check is done here if the folder specified is not longer than the
					// media depth. If it is, a JS popup informs the user. User cannot leave
					// the input box until corrected. This does not work in Firefox.
					print "<input name=\"folder".$i."\" type=\"text\" size=\"40\" tabindex=\"".$tab++."\" onblur=\"checkpath(this)\" />";
					if ($i==1) print "<br /><sub>".print_text("server_folder_advice",0,1)."</sub>";
					print "</td>";
				print "</tr>";
			} else {
				print "<input name=\"folder".$i."\" type=\"hidden\" value=\"\" />";
			}
			if ($i!=5) {
				print "<tr>";
					print "<td colspan=\"2\" />";
					print "&nbsp;";
					print "</td>";
				print "</tr>";
			}
		}

		// Print the submit button for uploading the media
		print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["upload"]."\" tabindex=\"".$tab++."\" /></td></tr>";
		print "</table></form>";
	}
	print "</div><br />";

	$allowDelete = true;
	$removeObject = true;
	// Remove object: same as Delete file, except file isn't deleted
	if ($action == "removeobject") {
		$action = "deletefile";
		$allowDelete = false;
		$removeObject = true;
	}

	// Remove link: same as Delete file, except file isn't deleted
	if ($action == "removelinks") {
		$action = "deletefile";
		$allowDelete = false;
		$removeObject = false;
	}

	// Delete file
	if ($action == "deletefile") {
		print "<table class=\"list_table $TEXT_DIRECTION width50\">";
		print "<tr><td class=\"messagebox wrap\">";
		$xrefs = array($xref);
		$onegedcom = true;
		if ($allowDelete) {
			//-- get all of the XREFS associated with this record
			//-- and check if the file is used in multiple gedcoms
			$myFile = str_replace($MEDIA_DIRECTORY, "", $filename);
			$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_file LIKE '%".$DBCONN->escape($myFile)."'";
			$res = $gGedcom->mDb->query($sql);

			while($row=$res->fetchRow()) {
				if ($row["m_gedfile"]!=$GEDCOMS[$GEDCOM]["id"]) $onegedcom = false;
				else $xrefs[] = $row["m_media"];
			}
			$xrefs = array_unique($xrefs);
		}

		$finalResult = true;
		while ($allowDelete) {
			if (!$onegedcom) {
				print "<span class=\"error\">".$pgv_lang["multiple_gedcoms"]."<br /><br /><b>".$pgv_lang["media_file_not_deleted"]."</b></span><br />";
				$finalResult = false;
				break;
			}
			if (strstr($filename, "://")) {
				print "<span class=\"error\">".$pgv_lang["external_file"]."<br /><br /><b>".$pgv_lang["media_file_not_deleted"]."</b></span><br />";
				$finalResult = false;
				break;
			}
			// Check if file exists. If so, delete it
			if (file_exists($filename) && $allowDelete) {
				if (@unlink($filename)) {
					print $pgv_lang["media_file_deleted"]."<br />";
					AddToChangeLog($filename." -- ".$pgv_lang["media_file_deleted"]);
				} else {
					$finalResult = false;
					print "<span class=\"error\">".$pgv_lang["media_file_not_deleted"]."</span><br />";
					AddToChangeLog($filename." -- ".$pgv_lang["media_file_not_deleted"]);
				}
			}

			// Check if thumbnail exists. If so, delete it.
			$thumbnail = str_replace("$MEDIA_DIRECTORY",$MEDIA_DIRECTORY."thumbs/",$filename);
			if (file_exists($thumbnail) && $allowDelete) {
				if (@unlink($thumbnail)) {
					print $pgv_lang["thumbnail_deleted"]."<br />";
					AddToChangeLog($thumbnail." -- ".$pgv_lang["thumbnail_deleted"]);
				} else {
					$finalResult = false;
					print "<span class=\"error\">".$pgv_lang["thumbnail_not_deleted"]."</span><br />";
					AddToChangeLog($thumbnail." -- ".$pgv_lang["thumbnail_not_deleted"]);
				}
			}
			break;
		}

		//-- loop through all of the found xrefs and delete any references to them
		foreach($xrefs as $ind=>$xref) {
			// Remove references to media file from gedcom and database
			// Check for XREF
			if ($xref != "") {
				// Combine the searchquery of filename and xref
				$mediaRef = "@".$xref."@";
				$query[] = $mediaRef;

				// Find the INDIS with the mediafile in it
				$foundindis = search_indis($query);

				// Now update the record
				foreach ($foundindis as $pid => $person) {
					// Check if changes to the record exist
					if (isset($pgv_changes[$pid."_".$person["gedfile"]])) $person["gedcom"] = find_record_in_file($pid);
					$subrecs = get_all_subrecords($person["gedcom"], "", false, false, false);
					$newrec = "0 @$pid@ INDI\r\n";
					foreach($subrecs as $ind=>$subrec) {
				    	if (strstr($subrec, $mediaRef)) {
					    	$pieces = explode("\r\n", $subrec);
					    	$skip = false;
					    	foreach ($pieces as $n=>$nibble) {
						    	if (!$skip) {
							    	if (strstr($nibble, $mediaRef)) {
								    	$skip = true;
								    	$refLevel = substr($nibble, 0, 1);
							    	}
						    	} else {
							    	if (substr($nibble, 0, 1)==$refLevel) {
								    	$skip = false;
							    	}
						    	}
						    	if ($skip || empty($pieces)) unset($pieces[$n]);
					    	}
					    	if (count($pieces)>0) {
					    		$subrec = implode("\r\n", $pieces);
					    		$newrec .= $subrec."\r\n";
				    		}
				    	} else $newrec .= $subrec."\r\n";
			    	}
					// Save the changed INDI record
					if (replace_gedrec($pid, $newrec, true, $xref)) {
						print_text("record_updated");
						AddToChangeLog(print_text("record_updated",0,1));
					} else {
						$finalResult = false;
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span>";
						AddToChangeLog(print_text("record_not_updated",0,1));
					}
					print "<br />";
				}

				// Find the FAMS with the mediafile in it
				$foundfams = search_fams($query);

				// Now update the record
				foreach ($foundfams as $pid => $family) {
					if (isset($pgv_changes[$pid."_".$family["gedfile"]])) $family["gedcom"] = find_record_in_file($pid);
					$subrecs = get_all_subrecords($family["gedcom"], "", false, false, false);
					$newrec = "0 @$pid@ FAM\r\n";
					foreach($subrecs as $fam=>$subrec) {
				    	if (strstr($subrec, $mediaRef)) {
					    	$pieces = explode("\r\n", $subrec);
					    	$skip = false;
					    	foreach ($pieces as $n=>$nibble) {
						    	if (!$skip) {
							    	if (strstr($nibble, $mediaRef)) {
								    	$skip = true;
								    	$refLevel = substr($nibble, 0, 1);
							    	}
						    	} else {
							    	if (substr($nibble, 0, 1)==$refLevel) {
								    	$skip = false;
							    	}
						    	}
						    	if ($skip || empty($pieces)) unset($pieces[$n]);
					    	}
					    	if (count($pieces)>0) {
					    		$subrec = implode("\r\n", $pieces);
					    		$newrec .= $subrec."\r\n";
				    		}
				    	} else $newrec .= $subrec."\r\n";
			    	}
					// Save the changed FAM record
					if (replace_gedrec($pid, $newrec)) {
						print_text("record_updated");
						AddToChangeLog(print_text("record_updated",0,1));
					} else {
						$finalResult = false;
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span>";
						AddToChangeLog(print_text("record_not_updated",0,1));
					}
					print "<br />";
				}

				// Find the SOURCE with the mediafile in it
				$foundsources = search_sources($query);

				// Now update the record
				foreach ($foundsources as $pid => $source) {
					if (isset($pgv_changes[$pid."_".$source["gedfile"]])) $source["gedcom"] = find_record_in_file($pid);
					$subrecs = get_all_subrecords($source["gedcom"], "", false, false, false);
					$newrec = "0 @$pid@ SOUR\r\n";
					foreach($subrecs as $src=>$subrec) {
				    	if (strstr($subrec, $mediaRef)) {
					    	$pieces = explode("\r\n", $subrec);
					    	$skip = false;
					    	foreach ($pieces as $n=>$nibble) {
						    	if (!$skip) {
							    	if (strstr($nibble, $mediaRef)) {
								    	$skip = true;
								    	$refLevel = substr($nibble, 0, 1);
							    	}
						    	} else {
							    	if (substr($nibble, 0, 1)==$refLevel) {
								    	$skip = false;
							    	}
						    	}
						    	if ($skip || empty($pieces)) unset($pieces[$n]);
					    	}
					    	if (count($pieces)>0) {
					    		$subrec = implode("\r\n", $pieces);
					    		$newrec .= $subrec."\r\n";
				    		}
				    	} else $newrec .= $subrec."\r\n";
			    	}
					// Save the changed SOUR record
					if (replace_gedrec($pid, $newrec)) {
						print_text("record_updated");
						AddToChangeLog(print_text("record_updated",0,1));
					} else {
						$finalResult = false;
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span>";
						AddToChangeLog(print_text("record_not_updated",0,1));
					}
					print "<br />";
				}

				// Find any other records with the mediafile in it
				$foundsources = search_other($query);

				// Now update the record
				foreach ($foundsources as $pid => $source) {
					if (isset($pgv_changes[$pid."_".$source["gedfile"]])) $source["gedcom"] = find_record_in_file($pid);
					$subrecs = get_all_subrecords($source["gedcom"], "", false, false, false);
					$newrec = "0 @$pid@ SOUR\r\n";		//-- Not sure WHY this is "SOUR"
					foreach($subrecs as $src=>$subrec) {
				    	if (strstr($subrec, $mediaRef)) {
					    	$pieces = explode("\r\n", $subrec);
					    	$skip = false;
					    	foreach ($pieces as $n=>$nibble) {
						    	if (!$skip) {
							    	if (strstr($nibble, $mediaRef)) {
								    	$skip = true;
								    	$refLevel = substr($nibble, 0, 1);
							    	}
						    	} else {
							    	if (substr($nibble, 0, 1)==$refLevel) {
								    	$skip = false;
							    	}
						    	}
						    	if ($skip || empty($pieces)) unset($pieces[$n]);
					    	}
					    	if (count($pieces)>0) {
					    		$subrec = implode("\r\n", $pieces);
					    		$newrec .= $subrec."\r\n";
				    		}
				    	} else $newrec .= $subrec."\r\n";
			    	}
					// Save the changed record
					if (replace_gedrec($pid, $newrec)) {
						print_text("record_updated");
						AddToChangeLog(print_text("record_updated",0,1));
					} else {
						$finalResult = false;
						print "<span class=\"error\">";
						print_text("record_not_updated");
						print "</span>";
						AddToChangeLog(print_text("record_not_updated",0,1));
					}
					print "<br />";
				}

				// Record changes to the Media object
				if ($xref!="") {
					include_once('includes/functions_import.php');
					accept_changes($xref."_".$GEDCOM);
					$objerec = find_record_in_file($xref);

					// Remove media object from gedcom
					if (delete_gedrec($xref)) {
						print_text("record_removed");
						AddToChangeLog(print_text("record_removed",0,1));
					} else {
						$finalResult = false;
						print "<span class=\"error\">";
						print_text("record_not_removed");
						print "</span>";
						AddToChangeLog(print_text("record_not_removed",0,1));
					}
					print "<br />";

					// Add the same file as a new object
					if ($finalResult && !$removeObject && $objerec!="") {
						$xref = get_new_xref("OBJE");
						$objerec = preg_replace("/0 @.*@ OBJE/", "0 @".$xref."@ OBJE", $objerec);
						if(append_gedrec($objerec)) {
							print_text("record_added");
							AddToChangeLog(print_text("record_added",0,1));
						} else {
							$finalResult = false;
							print "<span class=\"error\">";
							print_text("record_not_added");
							print "</span>";
							AddToChangeLog(print_text("record_not_added",0,1));
						}
						print "<br />";
					}
				}
			}
		}
		if ($finalResult) print $pgv_lang["update_successful"];
		$action = "filter";
		print "</td></tr></table>";
	}

	/**
	 * Generate link flyout menu
	 *
	 * @param string $mediaid
	 */
	function print_link_menu($mediaid) {
		global $pgv_lang, $TEXT_DIRECTION;

		$classSuffix = "";
		if ($TEXT_DIRECTION=="rtl") $classSuffix = "_rtl";

		// main link displayed on page
		$menu = array();
		$menu["label"] = $pgv_lang["set_link"];
		$menu["link"] = "#";
		$menu["onclick"] = "return ilinkitem('$mediaid','person')";
//		$menu["class"] = "thememenuitem";
		$menu["class"] = "";
		$menu["hoverclass"] = "";
		$menu["submenuclass"] = "submenu";
		$menu["flyout"] = "left";
		$menu["items"] = array();

		$submenu = array();
		$submenu["label"] = $pgv_lang["to_person"];
		$submenu["link"] = "#";
		$submenu["class"] = "submenuitem".$classSuffix;
		$submenu["hoverclass"] = "submenuitem".$classSuffix;
		$submenu["onclick"] = "return ilinkitem('$mediaid','person')";
		$menu["items"][] = $submenu;

		$submenu = array();
		$submenu["label"] = $pgv_lang["to_family"];
		$submenu["link"] = "#";
		$submenu["class"] = "submenuitem".$classSuffix;
		$submenu["hoverclass"] = "submenuitem".$classSuffix;
		$submenu["onclick"] = "return ilinkitem('$mediaid','family')";
		$menu["items"][] = $submenu;

		$submenu = array();
		$submenu["label"] = $pgv_lang["to_source"];
		$submenu["link"] = "#";
		$submenu["class"] = "submenuitem".$classSuffix;
		$submenu["hoverclass"] = "submenuitem".$classSuffix;
		$submenu["onclick"] = "return ilinkitem('$mediaid','source')";
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
		global $TEXT_DIRECTION, $MEDIA_DIRECTORY, $MEDIA_EXTERNAL;

		if ($MEDIA_EXTERNAL && stristr($filename, "://")) return false;

		$classSuffix = "";
		if ($TEXT_DIRECTION=="rtl") $classSuffix = "_rtl";

		$filename = basename($filename);

		// main link displayed on page
		$menu = array();
		$menu["label"] = $pgv_lang["move_to"];
		$menu["link"] = "#";
		$menu["class"] = "";
//		$menu["hoverclass"] = "thememenuitem_hover";
		$menu["hoverclass"] = "";
		$menu["submenuclass"] = "submenu";
		$menu["flyout"] = "left";
		$menu["items"] = array();

		// add option to move file up a level
		// Sanity check 2 Don't allow file move above the main media directory
		if ($level>0) {
			$submenu = array();
			$submenu["label"] = "<b>&nbsp;&nbsp;&nbsp;<--&nbsp;&nbsp;&nbsp;</b>";
			$submenu["link"] = "media.php?action=moveto&amp;level=$level&amp;directory=".rawurlencode($directory)."&amp;movetodir=..&amp;movefile=".rawurlencode($filename)."&amp;xref=$xref".$thumbget;
			$submenu["class"] = "submenuitem".$classSuffix;
			$submenu["hoverclass"] = "submenuitem_hover".$classSuffix;
			$menu["items"][] = $submenu;

		}
		// Add lower level directories
		// Sanity check 3 Don't list directories which are at a lower level
		//                than configured in the xxxxx_conf.php
		if ($level < $MEDIA_DIRECTORY_LEVELS) {
			foreach ($dirlist as $indexval => $dir) {
				$submenu = array();
				$submenu["label"] = $dir;
				$submenu["link"] = "media.php?action=moveto&amp;level=$level&amp;directory=".rawurlencode($directory)."&amp;movetodir=".rawurlencode($dir)."&amp;movefile=".rawurlencode($filename)."&amp;xref=$xref".$thumbget;
				$submenu["class"] = "submenuitem".$classSuffix;
				$submenu["hoverclass"] = "submenuitem_hover".$classSuffix;
				$menu["items"][] = $submenu;
			}
		}
		if (count($menu["items"])>0) print_menu($menu);
	}

	if ($action == "filter") {
		if (empty($directory)) $directory = $MEDIA_DIRECTORY;
		$medialist = get_medialist(true, $directory);
		// Get the list of media items
		/**
		 * This is the default action for the page
		 *
		 * Displays a list of dirs and files. Displaying only
		 * thumbnails as the images may be large and we do not want large delays
		 * while administering the file structure
		 *
		 * @name $action->filter
		 */
		// Show link to previous folder
		if ($level>0) {
			$levels = explode("/", $directory);
			$pdir = "";
			for($i=0; $i<count($levels)-2; $i++) $pdir.=$levels[$i]."/";

			$uplink = "<a href=\"media.php?directory=".rawurlencode($pdir)."&amp;level=".($level-1).$thumbget."\">";
			if ($TEXT_DIRECTION=="rtl") $uplink .= "&lrm;";
			$uplink .= $pdir;
			if ($TEXT_DIRECTION=="rtl") $uplink .= "&lrm;";
			$uplink .= "</a>\n";

			$uplink2 = "<a href=\"media.php?directory=".rawurlencode($pdir)."&amp;level=".($level-1).$thumbget."\"><img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/";
			if ($TEXT_DIRECTION=="ltr") $uplink2 .= $PGV_IMAGES["larrow"]["other"];
			else $uplink2 .= $PGV_IMAGES["rarrow"]["other"];
			$uplink2 .= "\" alt=\"".PrintReady($pdir)."\" title=\"".PrintReady($pdir)."\"></a>\n";
		}

		// Start of media directory table
		print "<table class=\"list_table width50 $TEXT_DIRECTION\">";

		// Tell the user where he is
		print "<tr>";
			print "<td class=\"topbottombar\" colspan=\"2\">";
				print $pgv_lang["current_dir"];
				print "<br />";
				print PrintReady(substr($directory,0,-1));
			print "</td>";
		print "</tr>";

		// display the directory list
		if (count($dirs) || $level) {
			sort($dirs);
			if ($level){
				print "<tr>";
					print "<td class=\"optionbox $TEXT_DIRECTION width10\">";
						print $uplink2;
					print "</td>";
					print "<td class=\"descriptionbox $TEXT_DIRECTION\">";
						print $uplink;
					print "</td>";
				print "</tr>";
			}

			foreach ($dirs as $indexval => $dir) {
				print "<tr>";
					print "<td class=\"optionbox $TEXT_DIRECTION width10\">";
						// Delete directory option
						print "<form name=\"blah\" action=\"media.php\" method=\"post\">";
						print "<input type=\"hidden\" name=\"directory\" value=\"".$directory.$dir."/\" />";
						print "<input type=\"hidden\" name=\"parentdir\" value=\"".$directory."\" />";
						print "<input type=\"hidden\" name=\"level\" value=\"".($level)."\" />";
						print "<input type=\"hidden\" name=\"dir\" value=\"".$dir."\" />";
						print "<input type=\"hidden\" name=\"action\" value=\"deletedir\" />";
						print "<input type=\"image\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"]."\" alt=\"".$pgv_lang['delete']."\" onclick=\"return confirm('".$pgv_lang["confirm_folder_delete"]."');\">";
						print "</form>";
					print "</td>";
					print "<td class=\"descriptionbox $TEXT_DIRECTION\">";
						print "<a href=\"media.php?directory=".rawurlencode($directory.$dir."/")."&amp;level=".($level+1).$thumbget."\">";
						if ($TEXT_DIRECTION=="rtl") print "&rlm;";
						print $dir;
						if ($TEXT_DIRECTION=="rtl") print "&rlm;";
						print "</a>";
					print "</td>";
				print "</tr>";

			}
		}
		// Form for ceating a new directory
		// Checks admin user can write and is not trying to create deeper level dir
		// than the configured number of levels
		if ($isadmin && $fileaccess && ($level < $MEDIA_DIRECTORY_LEVELS)) {
			print "<tr>";
				print "<td class=\"list_value $TEXT_DIRECTION\">";
					print "<form action=\"media.php\" method=\"get\">";
					print "<input type=\"hidden\" name=\"directory\" value=\"".$directory."\" />";
					print "<input type=\"hidden\" name=\"level\" value=\"".$level."\" />";
					print "<input type=\"hidden\" name=\"action\" value=\"newdir\" />";
					print "<input type=\"submit\" value=\"".$pgv_lang["add"]."\" />";
				print "</td>";
				print "<td class=\"descriptionbox $TEXT_DIRECTION\">";
					print "<input type=\"text\" name=\"newdir\" size=\"50\"/></form>";
				print "</td>";
			print "</tr>";
		}
		print "</table>";
		print "<br />";

		// display the images TODO x across if lots of files??
		if (count($medialist)) {
			print "\n\t<table class=\"list_table width90\">";
			if ($directory==$MEDIA_DIRECTORY) {
				$httpFilter = "http";
				$passStart = 1;
			} else {
				$httpFilter = "";
				$passStart = 2;
			}
			for ($passCount=$passStart; $passCount<3; $passCount++) {
				$printDone = false;
				foreach ($medialist as $indexval => $media) {
					while (true) {
						if (!filterMedia($media, $filter, $httpFilter)) break;
						$isExternal = stristr($media["FILE"],"://");
						if ($passCount==1 && !$isExternal) break;
						if ($passCount==2 && $isExternal) break;
						$imgsize = findImageSize($media["FILE"]);
						$imgwidth = $imgsize[0]+40;
						$imgheight = $imgsize[1]+150;

						$changeClass = "";
						if ($media["CHANGE"]=="delete") $changeClass = "change_old";
						if ($media["CHANGE"]=="replace") $changeClass = "change_new";
						if ($media["CHANGE"]=="append") $changeClass = "change_new";

						// Show column with file operations options
						$printDone = true;
						print "<tr><td class=\"optionbox $changeClass $TEXT_DIRECTION width10\">";

						if ($media["CHANGE"]!="delete") {
							// Edit File
							print "<a href=\"javascript:".$pgv_lang["edit"]."\" onclick=\"window.open('addmedia.php?action=";
							if ($media["XREF"] != "") {
								print "editmedia&amp;pid=".$media["XREF"]."&amp;linktoid=";
								if (!$media["LINKED"]) {
									print "new";
								} else {
									foreach ($media["LINKS"] as $linkToID => $temp) break;
									print $linkToID;
								}
							} else {
								print "showmediaform&amp;filename=".rawurlencode($media["FILE"])."&amp;linktoid=new";
							}
							print "', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;\">".$pgv_lang["edit"]."</a><br />";
							
							// Edit Raw
							if ($media["XREF"] != "") {
								print "<a href=\"javascript:".$pgv_lang["edit_raw"]."\" onclick=\"return edit_raw('".$media['XREF']."');\">".$pgv_lang['edit_raw']."</a><br />\n";
							}
							
							// Delete File
							// 		don't delete external files
							//		don't delete files linked to more than 1 object
							$objectCount = 0;
							if (!$isExternal) {
								foreach ($medialist as $tempMedia) {
									if ($media["EXISTS"] && $media["FILE"]==$tempMedia["FILE"]) $objectCount++;
								}
								unset($tempMedia);
							}
							if (!$isExternal && $objectCount<2) {
								print "<a href=\"media.php?";
								if (!empty($filter)) print "filter=".rawurlencode($filter)."&amp;";
								print "action=deletefile&amp;showthumb=".$showthumb."&amp;filename=".rawurlencode($media["FILE"])."&amp;directory=".rawurlencode($directory)."&amp;level=".$level."&amp;xref=".$media["XREF"]."&amp;gedfile=".$media["GEDFILE"]."\" onclick=\"return confirm('".$pgv_lang["confirm_delete_file"]."');\">".$pgv_lang["delete_file"]."</a><br />";
							}

							// Remove Object
							if (!empty($media["XREF"])) {
								print "<a href=\"media.php?";
								if (!empty($filter)) print "filter=".rawurlencode($filter)."&amp;";
								print "action=removeobject&amp;showthumb=".$showthumb."&amp;filename=".rawurlencode($media["FILE"])."&amp;directory=".rawurlencode($directory)."&amp;level=".$level."&amp;xref=".$media["XREF"]."&amp;gedfile=".$media["GEDFILE"]."\" onclick=\"return confirm('".$pgv_lang["confirm_remove_object"]."');\">".$pgv_lang["remove_object"]."</a><br />";
							}

							// Remove links
							if ($media["LINKED"]) {
								print "<a href=\"media.php?";
								if (!empty($filter)) print "filter=".rawurlencode($filter)."&amp;";
								print "action=removelinks&amp;showthumb=".$showthumb."&amp;filename=".rawurlencode($media["FILE"])."&amp;directory=".rawurlencode($directory)."&amp;level=".$level."&amp;xref=".$media["XREF"]."&amp;gedfile=".$media["GEDFILE"]."\" onclick=\"return confirm('".$pgv_lang["confirm_remove_links"]."');\">".$pgv_lang["remove_links"]."</a><br />";
							}

							// Set Link
							// Only set link on media that is in the DB
							if ($media["XREF"] != "") {
								print_link_menu($media["XREF"]);
							}

							// Generate thumbnail
							if (!$isExternal && (empty($media["THUMB"]) || !file_exists(filename_decode($media["THUMB"])))) {
								$ct = preg_match("/\.([^\.]+)$/", $media["FILE"], $match);
								if ($ct>0) $ext = strtolower(trim($match[1]));
								if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png") {
									print "<a href=\"media.php?";
									if (!empty($filter)) print "filter=".rawurlencode($filter)."&amp;";
									print "action=thumbnail&amp;all=0&amp;level=$level&amp;directory=".rawurlencode($directory)."&amp;filename=".rawurlencode($media["FILE"])."$thumbget\">".$pgv_lang["gen_thumb"]."</a>";
								}
							}

							// Move To menu -- Not really needed any more.  (leave in for now)
							if (!$isExternal && $MEDIA_DIRECTORY_LEVELS && $fileaccess && count($pgv_changes)==0) {
								// print_move_to_menu($dirs,$directory,$media["FILE"], $media["XREF"]);
							}

						}
						// NOTE: Close column for file operations
						print "</td>";

						//-- thumbnail field
						if ($showthumb) {
							$mediaTitle = "";
							if (isset($media["TITL"])) $mediaTitle = PrintReady($media["TITL"]);
							else $mediaTitle = PrintReady(basename($media["FILE"]));
							print "\n\t\t\t<td class=\"optionbox $changeClass $TEXT_DIRECTION width10\">";
							if (!$isExternal) {
								$thumbnail = thumbnail_file($media["FILE"]);
								print "<a href=\"#\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\">";
								print "<img src=\"".$thumbnail."\" class=\"thumbnail\" border=\"0\" alt=\"" . $mediaTitle . "\" title=\"" . $mediaTitle . "\"/></a>\n";
							} else {
								print "<a href=\"#\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\">";
								print "<img src=\"".$media["FILE"]."\" class=\"thumbnail\" width=\"".$THUMBNAIL_WIDTH."\" border=\"0\" alt=\"" . $mediaTitle . "\" title=\"" . $mediaTitle . "\"/></a>\n";
							}
							print "</td>";
						}

						//-- name and size field
						print "\n\t\t\t<td class=\"optionbox $changeClass $TEXT_DIRECTION wrap\">";
    					if ($media["TITL"]!="" && begRTLText($media["TITL"]) && $TEXT_DIRECTION=="ltr") {
							if (!empty($media["XREF"])) {
								print "(".$media["XREF"].")";
								print "&nbsp;&nbsp;&nbsp;";
							}
							if ($media["TITL"]!="") print "<b>".PrintReady($media["TITL"])."</b><br />";
    					} else {
							if ($media["TITL"]!="") print "<b>".PrintReady($media["TITL"])."</b>&nbsp;&nbsp;&nbsp;";
							if (!empty($media["XREF"])) {
								if ($TEXT_DIRECTION=="rtl") print "&rlm;";
								print "(".$media["XREF"].")";
								if ($TEXT_DIRECTION=="rtl") print "&rlm;";
								print "<br />";
							}
						}
						if (!$isExternal && !file_exists(filename_decode($media["FILE"]))) print "<span dir=\"ltr\">".PrintReady($media["FILE"])."</span><br /><span class=\"error\">".$pgv_lang["file_not_exists"]."</span><br />";
						else if ($isExternal) {
							print "<a href=\"#\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\"><b>URL</b></a><br />";
						} else {
							print "<a href=\"#\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\"><span dir=\"ltr\">".PrintReady($media["FILE"])."</span></a><br />";
							if (!empty($imgsize[0])) {
								print "<sub>&nbsp;&nbsp;".$pgv_lang["image_size"]." -- ".$imgsize[0]."x".$imgsize[1]."</sub><br />";
							}
						}
						if ($media["LINKED"]) {
							print "<br />".$pgv_lang["media_linked"];
							PrintMediaLinks($media["LINKS"], "normal");
						} else {
							print "<br />".$pgv_lang["media_not_linked"];
						}

						print "<br /><br />";
						print_fact_sources($media["GEDCOM"], 1);
						print_fact_notes($media["GEDCOM"], 1);

						print "\n\t\t\t</td></tr>";
						break;
					}
				}
				if ($passCount==1 && $printDone) print "<tr><td class=\"optionbox\" colspan=\"3\">&nbsp;</td></tr>";
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
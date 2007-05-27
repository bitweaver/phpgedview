<?php
/**
 * Popup window that will allow a user to search for a media
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
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: findmedia.php,v 1.3 2007/05/27 14:45:30 lsces Exp $
 */
require("config.php");
require("includes/functions_edit.php");

if (!isset($action)) $action="";
if (!isset($media)) $media="";
if (!isset($embed)) $embed=false;
if (!isset($filter)) $filter="";
if (!isset($directory)) $directory = $MEDIA_DIRECTORY;
$showthumb= isset($showthumb);

if ($embed)  check_media_db();

$thumbget = "";
if ($showthumb) {$thumbget = "&showthumb=true";}

if (!isset($level)) $level=0;
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

//-- popup or embedded as part of admin page
if ($embed) {
	$thistitle = $pgv_lang["manage_media"];
	print_header($thistitle);
}else{
	$thistitle = $pgv_lang["find_media"];
	print_simple_header($thistitle);
}

//-- only allow users with edit privileges to access script.
if ((!userCanEdit(getUserName())) || (!$ALLOW_EDIT_GEDCOM)) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

//-- check for admin once (used a bit in this script)
$isadmin =  userIsAdmin(getUserName());

//-- TODO add check for -- admin can manipulate files
$fileaccess = false;
if ($isadmin) {
	$fileaccess = true;
}

?>
<script language="JavaScript" type="text/javascript">
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
</script>
<script src="./js/phpgedview.js" language="JavaScript" type="text/javascript"></script>
<div class="center">
<span class="subheaders"><?php print $thistitle; ?>
<?php
if ($embed) { print_help_link("manage_media_help","qm"); }
	else 	{ print_help_link("find_media_help","qm"); }
?>
</span>
</div>

<form action="findmedia.php" method="get">
	<input type="hidden" name="embed" value="<?php print $embed; ?>" />
	<input type="hidden" name="directory" value="<?php print $directory; ?>" />
	<input type="hidden" name="thumbdir" value="<?php print $thumbdir; ?>" />
	<input type="hidden" name="level" value="<?php print $level; ?>" />
	<input type="hidden" name="action" value="filter" />

	<div class="center">
		<table class="list-table">
			<tr>
				<td class="list-label <?php print $TEXT_DIRECTION; ?>"><?php print $pgv_lang["filter"]; ?></td>
		    	<td class="list-label <?php print $TEXT_DIRECTION; ?>">&nbsp;<input id="filter" name="filter" value="<?php print $filter; ?>"/></td>
		    	<td class="list-label <?php print $TEXT_DIRECTION; ?>"><input type="submit" value=" &gt; "/>
		    	<?php print_help_link("simple_filter_help","qm"); ?></td>
			</tr>
			<tr>
				<td />
		    	<td class="list-label <?php print $TEXT_DIRECTION; ?>" ><input type="checkbox" name="showthumb" value="true" <?php if( $showthumb) {print "checked=\"checked\"";} ?>" onclick="javascript: this.form.submit()"><?php print $pgv_lang["show_thumbnail"];?>
		    	<?php print_help_link("show_thumb_help","qm"); ?></td>
		    	<td />
			</tr>
		</table>
	</div>
</form>
<?php

$badmedia = array(".","..","CVS","thumbs","index.php","MediaInfo.txt");
if (empty($action)) {
	$action="filter";
}

print "<center>";



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
			if (!$fp) {
				print "<div class=\"error\">".$pgv_lang["security_no_create"].$directory.$newdir."</div>";
			} else {
				fputs($fp,$inddata);
				fclose($fp);
			}
		} else {
			print "<div class=\"error\">".$pgv_lang["security_not_exist"].$directory."</div>";
		}
		if (file_exists($thumbdir."index.php")) {
			$inddata = file_get_contents($thumbdir."index.php");
			$inddata = str_replace(": ../",": ../../",$inddata);
			$fp = @fopen($thumbdir.$newdir."/index.php","w+");
			if (!$fp) {
				print "<div class=\"error\">".$pgv_lang["security_no_create"].$thumbdir.$newdir."</div>";
			} else {
				fputs($fp,$inddata);
				fclose($fp);
			}
		} else {
			print "<div class=\"error\">".$pgv_lang["security_not_exist"].$thumbdir."</div>";
		}
	} else {
		print "<div class=\"error\">".$pgv_lang["illegal_chars"]."</div>";
	}
	$action="filter";
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

		// file details
		$filename = $_REQUEST["movefile"];
		$moveto = $_REQUEST["movetodir"];
		// and the thumbnail
		$tdirfrom = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$directory);
		$tdirto = preg_replace("'$MEDIA_DIRECTORY'",$MEDIA_DIRECTORY."thumbs/",$directory).$moveto;

		// no error if admin chooses not to have a thumbnail file
		if (file_exists($tdirfrom.$filename)) {
			if (is_dir($tdirto)) {
				$movethumb = $tdirto."/".$filename;
				rename($tdirfrom.$filename, $movethumb);
			}else{
				// however if there is a thumbnail file and no directory to put it in
				if (mkdir($tdirto,0777)) {
					$movethumb = $tdirto."/".$filename;
					rename($tdirfrom.$filename, $movethumb);
				}else{
					// abort the whole operation to keep the directory structure valid
					print $tdirto.$pgv_lang["no_thumb_dir"];
					print_simple_footer();
					exit;
				}
			}
		}
		// no major error from thumbnail so move the file
		$movefile = real_path($directory.$moveto."/".$filename);
		rename($directory.$filename, $movefile);
		// also inform the database of this move if from the embedded admin page
		move_db_media($directory.$filename, $movefile, $GEDCOM);

		//-- TODO add function to rename file in main gedcom making this suitable for all users
// 		move_media_file($directory.$filename, $movefile, $GEDCOM);
	}
	$action="filter";
}

/**
 * This action generates a thumbnail for the file
 *
 * @name $action->thumbnail
 */
if ($action=="thumbnail") {
	$filename = $_REQUEST["file"];
	generate_thumbnail($directory.$filename,$thumbdir.$filename);
	$action = "filter";
}


/**
 * This is the default action for the page
 *
 * Displays a list of dirs and files. Displaying only
 * thumbnails as the images may be large and we do not want large delays
 * while adminstering the file structure
 *
 * @name $action->filter
 */
if ($action=="filter") {

	$d = dir($directory);
	if ($level>0) {
		$levels = preg_split("'/'", $directory);
		$pdir = "";
		for($i=0; $i<count($levels)-2; $i++) $pdir.=$levels[$i]."/";
		$levels = preg_split("'/'", $thumbdir);
		$pthumb = "";
		for($i=0; $i<count($levels)-2; $i++) $pthumb.=$levels[$i]."/";
		$uplink = "<a href=\"findmedia.php?embed=$embed&directory=$pdir&thumbdir=$pthumb&level=".($level-1).$thumbget."\">&nbsp;&nbsp;&nbsp;&lt;-- $pdir&nbsp;&nbsp;&nbsp;</a><br />\n";
	}

	$dirs = array();
	$images = array();
	while (false !== ($media = $d->read())) {
		if (!in_array($media, $badmedia)) {
			$mediafile = $directory.$media;
			if (is_dir($mediafile) ) {
				// do not allow the web interface to go to lower levels than configured
				if ($level < $MEDIA_DIRECTORY_LEVELS ) $dirs[] = $media;
			}
			else {
				$images[] = $media;
			}
		}
	}

	// display the directory list
	if (count($dirs) || $level) {
		sort($dirs);
		print "\n\t<table class=\"list_table\">";
		if ($level){
			print "\n\t\t<tr>\n\t\t\t<td class=\"list_value $TEXT_DIRECTION\">";
			print $uplink."</tr>";
		}
		foreach ($dirs as $indexval => $dir) {
			print "\n\t\t<tr>\n\t\t\t<td class=\"list_value $TEXT_DIRECTION\">";
			print "<a href=\"findmedia.php?embed=$embed&directory=$directory$dir/&thumbdir=$directory$dir/&level=".($level+1).$thumbget."\">$dir</a><br /></td>\n\t\t</tr>";
		}
		print "\n\t</table>";
	}

	// ceate directory code
	// checks admin user can write and is not trying to create lower level dir
	// than the configured number of levels
	if ($isadmin && $fileaccess && ($level < $MEDIA_DIRECTORY_LEVELS)) {

		print "<form action=\"findmedia.php\" method=\"get\"><input type=\"hidden\" name=\"embed\" value=\"".$embed."\" /><input type=\"hidden\" name=\"directory\" value=\"".$directory."\" />";
		print "<input type=\"hidden\" name=\"thumbdir\" value=\"".$thumbdir."\" /><input type=\"hidden\" name=\"level\" value=\"".$level."\" /><input type=\"hidden\" name=\"action\" value=\"newdir\" />";
		print "<a href=\"javascript:;\" onclick=\"return expand_layer('newdir');\"><img id=\"newnote_img\" src=\"images/plus.gif\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" />".$pgv_lang["add_directory"]."</a>\n";
		print_help_link("new_dir_help","qm");
		print "<br /><div id=\"newdir\" style=\"display: none;\"><table class=\"list_table\"><td class=\"list_value\"><input name=\"newdir\"/>";
		print "<input type=\"submit\" value=\" &gt; \"</td></td></tr></table></div></div><form><center>";
	}

	$applyfilter = ($filter != "");

	// display the images TODO x across if lots of files??
	if (count($images)) {
		sort($images);
		print "\n\t<table class=\"list_table\">";
		foreach ($images as $indexval => $imag) {

			// simple filter to reduce the number of items to view
			if ($applyfilter) {
				$isvalid = (strpos(str2lower($imag),str2lower($filter)) !== false);
			} else { $isvalid = true; }

			if ($isvalid) {
				$imgsize = getimagesize($directory.$imag);
				if ($imgsize) {
					$imgwidth = $imgsize[0]+50;
					$imgheight = $imgsize[1]+50;
				}
				else {
					$imgwidth = 300;
					$imgheight = 300;
				}

				print "\n\t\t<tr>";
				//-- thumbnail field
				if ($showthumb) {
					print "\n\t\t\t<td class=\"list_value $TEXT_DIRECTION\">";
					if (file_exists($thumbdir.$imag)) {
						print "<a href=\"javascript:;\" onclick=\"return openImage('".preg_replace("/'/", "\'", urlencode($directory.$imag))."',$imgwidth, $imgheight);\"><img src=\"".filename_encode($thumbdir.$imag)."\" border=\"0\" width=\"50\"></a>\n";
					}
					else {
						print "<a href=\"findmedia.php?embed=$embed&directory=$directory&action=thumbnail&level=$level&file=$imag$thumbget\">".$pgv_lang["gen_thumb"]."</a></td>";
					}
				}

				//-- name and size field
				print "\n\t\t\t<td class=\"list_value $TEXT_DIRECTION\">";
				if (!$embed){
				print "&nbsp;<a href=\"javascript:;\" onclick=\"pasteid('".preg_replace("/'/", "\'", filename_encode($directory.$imag))."');\">".filename_encode($imag)."</a> -- ";
				}else{ print "&nbsp;".$imag." -- ";}
				print "<a href=\"javascript:;\" onclick=\"return openImage('".preg_replace("/'/", "\'", filename_encode($directory.$imag))."',$imgwidth, $imgheight);\">".$pgv_lang["view"]."</a>";
				print "<br /><sub>&nbsp;&nbsp;".$pgv_lang["image_size"]. " -- " . $imgsize[0]."x".$imgsize[1]."</sub>\n";
				// Sanity check 1 user must be admin and no point doing file admin if all in one dir
				if ( $MEDIA_DIRECTORY_LEVELS && $fileaccess && $embed) {
					print "\n\t\t\t</td><td class=\"list_value $TEXT_DIRECTION\">";
					print_move_to_menu($dirs,$directory,$imag);
				}
				print "\n\t\t\t</td>";
			}
		}
		print "\n\t\t</tr>\n\t</table><br />";
	}
	$d->close();
}
print "</center>";
print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";

print_simple_footer();

/**
 * Generate Move To flyout menu
 *
 * Access control to directories are in this routine
 *
 * @param mixed $dirlist array() list of subdirectories
 * @param string $directory string current working directory
 * @param sring $filename filename to generate this menu and links for
 */
function print_move_to_menu($dirlist,$directory, $filename) {
	global $level, $MEDIA_DIRECTORY_LEVELS, $embed, $pgv_lang, $thumbget;

	// main link displayed on page
	$menu = array();
	$menu["label"] = "&nbsp;&nbsp;".$pgv_lang["move_to"]."&nbsp;&nbsp;";
	$menu["link"] = "#";
    $menu["class"] = "";
    $menu["hoverclass"] = "menuitem_hover";
    $menu["submenuclass"] = "submenu";
    $menu["flyout"] = "left";
	$menu["items"] = array();

	// add option to move file up a level
	// Sanity check 2 Don't allow file move above the main media directory
	if ($level>0) {
		$submenu = array();
		$submenu["label"] = "<b>&nbsp;&nbsp;&nbsp;<--&nbsp;&nbsp;&nbsp;</b>";
		$submenu["link"] = "findmedia.php?embed=$embed&directory=$directory&action=moveto&level=$level&movetodir=..&movefile=$filename".$thumbget;
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
			$submenu["link"] = "findmedia.php?embed=$embed&directory=$directory&action=moveto&level=$level&movetodir=$dir&movefile=$filename".$thumbget;
			$submenu["class"] = "submenuitem";
			$menu["items"][] = $submenu;
		}
	}
	print_menu($menu);
}

?>

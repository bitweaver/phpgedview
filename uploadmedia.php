<?php
/**
 * Allow admin users to upload media files using a web interface.
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
 * @version $Id: uploadmedia.php,v 1.3 2007/05/28 08:25:52 lsces Exp $
 * @package PhpGedView
 * @subpackage Media
 */
require "config.php";

if (!userCanEdit(getUserName())) {
	header("Location: login.php?url=uploadmedia.php");
	exit;
}

print_header($pgv_lang["upload_media"]);
$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
?>
<center>
<?php
	print "<span class=\"subheaders\">".str2upper($pgv_lang["upload_media"])."</span><br /><br />\n";
	if ((isset($action)) && ($action=="upload")) {
		for($i=1; $i<6; $i++) {
			$error="";
			if (!empty($_FILES['mediafile'.$i]["name"])) {
				//-- ensure sub-folder name is properly formatted
				if (substr($_POST["folder".$i],0,1) == "/") $_POST["folder".$i] = substr($_POST["folder".$i],1);
				$_POST["folder".$i] .= "/";
				$_POST["folder".$i] = str_replace("//", "/", $_POST["folder".$i]);
				if ($_POST["folder".$i] == "/") $_POST["folder".$i] = "";
				
				AddToLog("Media file ".$MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name'])." uploaded by >".getUserName()."<");
				$thumbgenned = false;
				if (!move_uploaded_file($_FILES['mediafile'.$i]['tmp_name'], $MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']))) {
					$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['mediafile'.$i]['error']]."<br />";
				}
				else {
					//-- automatically generate thumbnail
					if (!empty($_POST['genthumb'.$i]) && ($_POST['genthumb'.$i]=="yes")) {
						$filename = $MEDIA_DIRECTORY.$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']);
						if (!is_dir($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i])) mkdir($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i]);
						$thumbnail = $MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['mediafile'.$i]['name']);
						$thumbgenned = generate_thumbnail($filename, $thumbnail);
						if (!$thumbgenned) $error .= str_replace("#thumbnail#", $thumbnail, $pgv_lang["thumbgen_error"])."<br />";
						else print str_replace("#thumbnail#", $thumbnail, $pgv_lang["thumb_genned"])."<br />";
					}
				}
				AddToLog("Media thumbnail ".$MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['thumbnail'.$i]['name'])." uploaded by >".getUserName()."<");
				if (!$thumbgenned) {
					if (!is_dir($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i])) mkdir($MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i]);
					if (!move_uploaded_file($_FILES['thumbnail'.$i]['tmp_name'], $MEDIA_DIRECTORY."thumbs/".$_POST["folder".$i].basename($_FILES['thumbnail'.$i]['name']))) {
						$error .= $pgv_lang["upload_error"]."<br />".$upload_errors[$_FILES['thumbnail'.$i]['error']]."<br />";
					}
				}
				if (!empty($error)) print "<span class=\"error\">".$error."</span><br />\n";
				else {
					print $pgv_lang["upload_successful"]."<br /><br />";
					$imgsize = findImageSize($MEDIA_DIRECTORY.$_POST["folder".$i].$_FILES['mediafile'.$i]['name']);
					$imgwidth = $imgsize[0]+40;
					$imgheight = $imgsize[1]+150;
					print "<a href=\"javascript:;\" onclick=\"return openImage('".urlencode($MEDIA_DIRECTORY.$_POST["folder".$i].$_FILES['mediafile'.$i]['name'])."',$imgwidth, $imgheight);\">".$_FILES['mediafile'.$i]['name']."</a>";
					print"<br /><br />";
				}
			}
		}
	}

	if (!is_writable($MEDIA_DIRECTORY) || !$MULTI_MEDIA) {
		print "<span class=\"error\"><b>";
		print $pgv_lang["no_upload"];
		print "</b></span>";
	} else {
		print "<table width=\"70%\" class=\"$TEXT_DIRECTION\"><tr><td>";
		print_text("upload_media_help");
		if (!$filesize = ini_get('upload_max_filesize')) {
			$filesize = "2M";
		}
		print "<br />".$pgv_lang["max_upload_size"];
		print " $filesize<br /><br />";
		print "</td></tr></table>";

		print "<form enctype=\"multipart/form-data\" method=\"post\" action=\"uploadmedia.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"upload\" />";
		print "<table border=0 cellpadding=0 cellspacing=0>";
		for($i=1; $i<6; $i++) {
			print "<tr>";
				print "<td ";
				write_align_with_textdir_check("right");
				print ">";
					print $pgv_lang["folder"];
					print "&nbsp;";
				print "</td>";
				print "<td>";
					print "<input type=\"text\" name=\"folder".$i."\" size=60 />";
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td ";
				write_align_with_textdir_check("right");
				print ">";
					print $pgv_lang["media_file"];
					print "&nbsp;";
				print "</td>";
				print "<td>";
					print "<input name=\"mediafile".$i."\" type=\"file\" size=60 />";
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td ";
				write_align_with_textdir_check("right");
				print ">";
					print $pgv_lang["thumbnail"];
					print "&nbsp;";
				print "</td>";
				print "<td>";
					print "<input name=\"thumbnail".$i."\" type=\"file\" size=60 />";
				print "</td>";
			print "</tr>";

			$ThumbSupport = "";
			if (function_exists("imagecreatefromjpeg") and function_exists("imagejpeg")) $ThumbSupport .= ", JPG";
			if (function_exists("imagecreatefromgif") and function_exists("imagegif")) $ThumbSupport .= ", GIF";
			if (function_exists("imagecreatefrompng") and function_exists("imagepng")) $ThumbSupport .= ", PNG";
			if (!$AUTO_GENERATE_THUMBS) $ThumbSupport = "";

			if ($ThumbSupport != "") {
				$ThumbSupport = substr($ThumbSupport, 2);	// Trim off first ", "
				print "<tr>";
					print "<td colspan=\"2\" class=\"center\">";
						print "<input type=\"checkbox\" name=\"genthumb".$i."\" value=\"yes\" checked/> ";
						print $pgv_lang["generate_thumbnail"];
						print $ThumbSupport;
						print_help_link("generate_thumb_help", "qm");
					print "</td>";
				print "</tr>";
			}
			print "<tr><td><br /><br /></td></tr>";
		}
		print "</table>";
		print "<br />";
		print "<input type=\"submit\" value=\"";
		print $pgv_lang["upload"];
		print "\" />";
		print "</form>";
		print "<br />";
		print "</center>";
	}
	print_footer();
?>

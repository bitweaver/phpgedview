<?php
/**
 * Displays a list of the multimedia objects
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008 John Finlay and Others.  All rights reserved.
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
 * @subpackage Lists
 * @version $Id: medialist.php,v 1.7 2009/09/15 20:06:00 lsces Exp $
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
require_once('includes/functions/functions_print_facts.php');


loadLangFile("lightbox:lang");

// $LB_SS_SPEED = "5";
$level = safe_GET("level","","0");
$action = safe_GET('action');
$filter = safe_GET('filter');
$filter = stripLRMRLM(stripslashes($filter));
$search = safe_GET('search');
$sortby = safe_GET('sortby', 'file', 'title');
$max = safe_GET('max', array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200'), '20');
$folder = safe_GET('folder');
if (empty($folder)) $folder = $MEDIA_DIRECTORY;
if (!isset($_SESSION["medialist"])) $search = "yes";

$currentdironly = (isset($_REQUEST['subdirs']) && $_REQUEST['subdirs']=="on") ? false : true;
print_header($pgv_lang["multi_title"]);

if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';

print "\n\t<div class=\"center\"><h2>".$pgv_lang["multi_title"]."</h2></div>\n\t";

// Get Javascript variables from lb_config.php ---------------------------
if (file_exists("modules/lightbox/album.php")) {
	include('modules/lightbox/lb_defaultconfig.php');
	if (file_exists('modules/lightbox/lb_config.php')) include('modules/lightbox/lb_config.php');
	include('modules/lightbox/functions/lb_call_js.php');

	if ($theme_name=="Minimal") {
		// Force icon options to "text" when we're dealing with the Minimal theme
		if ($LB_AL_HEAD_LINKS!="none") $LB_AL_HEAD_LINKS = "text";
		if ($LB_AL_THUMB_LINKS!="none") $LB_AL_THUMB_LINKS = "text";
		if ($LB_ML_THUMB_LINKS!="none") $LB_ML_THUMB_LINKS = "text";
	}
}


//-- automatically generate an image
if (PGV_USER_IS_ADMIN && $action=="generate" && !empty($file) && !empty($thumb)) {
	generate_thumbnail($file, $thumb);
}
if ($search == "yes") {
	if ($folder == "ALL") {
		$folder = $MEDIA_DIRECTORY;
		$currentdironly = false;
	}
	// show external links only if looking at top level directory
	$showExternal = ($folder == $MEDIA_DIRECTORY) ? true : false;
	$medialist=get_medialist($currentdironly, $folder, true, false, $showExternal);

	//-- remove all private media objects
	foreach($medialist as $key => $media) {
			print " ";

			// Display when user has Edit rights or when object belongs to current GEDCOM
			$disp = PGV_USER_CAN_EDIT || $media["GEDFILE"]==PGV_GED_ID;
			// Display when Media objects aren't restricted by global privacy
			$disp &= displayDetailsById($media["XREF"], "OBJE");
			// Display when this Media object isn't restricted
			$disp &= !FactViewRestricted($media["XREF"], $media["GEDCOM"]);
			/** -- already included in the displayDetailsById() function
		if ($disp) {
				$links = $media["LINKS"];
				//-- make sure that only media with links are shown
			if (count($links) != 0) {
						foreach($links as $id=>$type) {
							$disp &= displayDetailsById($id, $type);
						}
				}
		}
		*/
		if (!$disp) unset($medialist[$key]);
	}
	usort($medialist, "mediasort"); // Reset numbering of medialist array
}

// A form for filtering the media items
?>
<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter" />
	<input type="hidden" name="search" value="yes" />
	<table class="list-table center width75 <?php print $TEXT_DIRECTION; ?>">
	<?php
	if ($TEXT_DIRECTION=='ltr') $legendAlign = 'align="right"';
	else $legendAlign = 'align="left"';
	?>

	<!-- // NOTE: Row 1, left: Sort sequence -->
	<tr><td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php print_help_link("sortby_help","qm", "sortby"); ?><?php print $pgv_lang["sortby"]; ?></td>
	<td class="optionbox wrap"><select name="sortby">
		<option value="title" <?php if ($sortby=='title') print "selected=\"selected\"";?>><?php print $factarray["TITL"];?></option>
		<option value="file" <?php if ($sortby=='file') print "selected=\"selected\"";?>><?php print $factarray["FILE"];?></option>
	</select></td>

	<!-- // NOTE: Row 1, right: Objects per page -->
	<td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php print $pgv_lang["per_page"]; ?></td>
	<td class="optionbox wrap width25">
		<select name="max">
		<?php
			foreach (array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200') as $selectEntry) {
				print "<option value=\"$selectEntry\"";
				if ($selectEntry==$max) print " selected=\"selected\"";
				print ">".$selectEntry."</option>";
			}
		?>
		</select>
	</td></tr>

	<!-- // NOTE: Row 2 left: Filter options -->
	<tr><td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php print_help_link("simple_filter_help","qm","filter"); print $pgv_lang["filter"];?></td>
	<td class="optionbox wrap width25">
		<?php
		// Directory pick list
		if ($MEDIA_DIRECTORY_LEVELS > 0) {
			if (empty($folder)) {
				if (!empty($_SESSION['upload_folder'])) $folder = $_SESSION['upload_folder'];
				else $folder = "ALL";
			}
			$folders = array_merge(array("ALL"), get_media_folders());
			print "<span dir=\"ltr\"><select name=\"folder\">\n";
			foreach($folders as $f) {
				print "<option value=\"".$f."\"";
				if ($folder==$f) print " selected=\"selected\"";
				print ">";
				if ($f=="ALL") print $pgv_lang["all"];
				else print $f;
				print "</option>\n";
			}
			print "</select></span><br />";
		} else print "<input name=\"folder\" type=\"hidden\" value=\"ALL\" />";
		// Text field for filter and "submit" button
		?>
		<input id="filter" name="filter" value="<?php print PrintReady($filter); ?>"/><br />
		<input type="submit" value="<?php echo $pgv_lang["apply_filter"];?>" />
	</td>

	<!-- // NOTE: Row 2 right: Recursive directory list -->
	<?php if ($MEDIA_DIRECTORY_LEVELS > 0) { ?>
	<td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php print_help_link("medialist_recursive_help","qm","medialist_recursive"); print $pgv_lang["medialist_recursive"];?></td>
	<td class="optionbox wrap width25">
		<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?> />
	</td></tr>
	<?php } else { ?>
	<td class="descriptionbox wrap width25" <?php print $legendAlign;?>>&nbsp;</td>
	<td class="optionbox wrap">
		&nbsp;
	</td></tr>
	<?php } ?>

	</table>
</form>
<?php

if ($action=="filter") {
	if (strlen($filter) > 1) {
		foreach($medialist as $key => $media) {
			if (!filterMedia($media, $filter, "http")) unset($medialist[$key]);
		}
	}
	usort($medialist, "mediasort"); // Reset numbering of medialist array
}
if ($search=="yes") {
	$_SESSION["medialist"] = $medialist;
} else {
	$medialist = $_SESSION["medialist"];
}

// Sort the media list according to the user's wishes
$sortedMediaList = $medialist;	// Default sort (by title) has already been done
if ($sortby=='file') usort($sortedMediaList, 'filesort');

// Count the number of items in the medialist
$ct=count($sortedMediaList);
$start = 0;
$max = 20;
if (isset($_GET["start"])) $start = $_GET["start"];
if (isset($_GET["max"])) $max = $_GET["max"];
$count = $max;
if ($start+$count > $ct) $count = $ct-$start;

print "\n\t<div align=\"center\">".$ct." ".$pgv_lang["media_found"]." <br /><br />";
if ($ct>0) {

	$currentPage = ((int) ($start / $max)) + 1;
	$lastPage = (int) (($ct + $max - 1) / $max);
	$IconRarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconLarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconRDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rdarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconLDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["ldarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";

	print"\n\t<table class=\"list_table\">\n";

	// print page back, page number, page forward controls
	print "\n<tr><td colspan=\"2\">\n";
	print "\n\t<table class=\"list_table width100\">\n";
	print "\n<tr>\n";
	print "<td class=\"width30\" align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($currentPage > 1) {
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}")."\">".$IconLDarrow."</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconLarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}")."\">".$IconRDarrow."</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconRarrow."</a>\n";
			}
		}
	}
	print "</td>";
	print "<td align=\"center\">".print_text("page_x_of_y", 0, 1)."</td>";
	print "<td class=\"width30\" align=\"" . ($TEXT_DIRECTION == "ltr"?"right":"left") . "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconRarrow."</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}")."\">".$IconRDarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconLarrow."</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}")."\">".$IconLDarrow."</a>\n";
			}
		}
	}
	print "</td>";
	print "</tr>\n</table></td></tr>";

	// -- print the array
	print "\n<tr>\n";

	for ($i=0; $i<$count; $i++) {
		$media = $sortedMediaList[$start+$i];
		$isExternal = isFileExternal($media["FILE"]);
		$imgsize = findImageSize($media["FILE"]);
		$imgwidth = $imgsize[0]+40;
		$imgheight = $imgsize[1]+150;
		$name = trim($media["TITL"]);
//		$name1 = addslashes($media["TITL"]);
		$showFile = PGV_USER_CAN_EDIT;
		if ($name=="") {
			//$showFile = false;
			if ($isExternal) $name = "URL";
			else $name = basename($media["FILE"]);
		}
		print "\n\t\t\t<td class=\"list_value_wrap\" width=\"50%\">";
		print "<table class=\"$TEXT_DIRECTION\">\n\t<tr>\n\t\t<td valign=\"top\" style=\"white-space: normal;\">";

		//Get media item Notes
		$haystack = $media["GEDCOM"];
		$needle   = "1 NOTE";
		$before   = substr($haystack, 0, strpos($haystack, $needle));
		$after    = substr(strstr($haystack, $needle), strlen($needle));
		$worked   = str_replace("1 NOTE", "1 NOTE<br />", $after);
		$final    = $before.$needle.$worked;
		$notes    = PrintReady(htmlspecialchars(addslashes(print_fact_notes($final, 1, true, true)),ENT_COMPAT,'UTF-8'));

		// Get info on how to handle this media file
		$mediaInfo = mediaFileInfo($media["FILE"], $media["THUMB"], $media["XREF"], $name, $notes);

		//-- Thumbnail field
		echo '<a href="', $mediaInfo['url'], '">';
		echo '<img src="', $mediaInfo['thumb'], '" align="center" class="thumbnail" border="none"', $mediaInfo['width'];
		echo ' alt="', PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')), '" title="', PrintReady(htmlspecialchars($name,ENT_COMPAT,'UTF-8')), '" /></a>';
		echo "</td>\n\t\t", '<td class="list_value_wrap" style="border: none;" width="100%">';

		if (file_exists('modules/lightbox/album.php')) {
			if (PGV_USER_CAN_EDIT) {

				if ($LB_ML_THUMB_LINKS != "none") {
					print "<table border=0><tr>";

					// ---------- Edit Media --------------------
					print "<td class=\"width33 wrap center font9\" valign=\"top\">";
					print "<a href=\"javascript:;\" title=\"" . $pgv_lang["lb_edit_media"] . "\" onclick=\" return window.open('addmedia.php?action=editmedia&pid={$media['XREF']}&linktoid=', '_blank', 'top=50,left=50,width=600,height=600,resizable=1,scrollbars=1');\">";
					if ($LB_ML_THUMB_LINKS == "icon" || $LB_ML_THUMB_LINKS == "both") {
						print "<img src=\"modules/lightbox/images/image_edit.gif\" alt=\"\" class=\"icon\" title=\"" . $pgv_lang["lb_edit_media"] . "\" />&nbsp;&nbsp;&nbsp;" ;
					}
					if ($LB_ML_THUMB_LINKS == "both") {
						print "<br />";
					}
					if ($LB_ML_THUMB_LINKS == "both" || $LB_ML_THUMB_LINKS == "text") {
						print $pgv_lang["lb_edit_details"] ;
					}
					print "</a>" . "\n";
					print "</td>";

					// ---------- Link Media to person, family or source  ---------------
					print "<td class=\"width33 wrap center font9\" valign=\"top\">";
					include ("modules/lightbox/functions/lb_link.php");
					print "</td>";

					// ---------- View Media Details (mediaviewer) --------------------
					print "<td class=\"width33 wrap center font9\" valign=\"top\">";
					print "<a href=\"mediaviewer.php?mid=" . $media["XREF"] . "\" title=\"" . $pgv_lang["lb_view_media"] . "\">";
					if ($LB_ML_THUMB_LINKS == "icon" || $LB_ML_THUMB_LINKS == "both") {
						print "&nbsp;&nbsp;&nbsp;<img src=\"modules/lightbox/images/image_view.gif\" alt=\"\" class=\"icon\" title=\"" . $pgv_lang["lb_view_media"] . "\" />";
					}
					if ($LB_ML_THUMB_LINKS == "both") {
						print "<br />";
					}
					if ($LB_ML_THUMB_LINKS == "both" || $LB_ML_THUMB_LINKS == "text") {
						print $pgv_lang["lb_view_details"] ;
					}
					print "</a>" . "\n" ;
					print "</td>";

					print "</tr></table>";
				}
				// ------------ Linespace ---------------------
				print "<br />";
			}
		}

			print "<a href=\"mediaviewer.php?mid=".$media["XREF"]."\">";

			if (begRTLText($name) && $TEXT_DIRECTION=="ltr") {
			print "(".$media["XREF"].")&nbsp;&nbsp;&nbsp;";
			print "<b>".PrintReady($name)."</b>";
			} else {
			print "<b>".PrintReady($name)."</b>&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print getRLM();
			print "(".$media["XREF"].")";
			if ($TEXT_DIRECTION=="rtl") print getRLM();
		}
		if ($showFile) {
			if ($isExternal) print "<br /><sub>URL</sub>";
			else print "<br /><sub><span dir=\"ltr\">".PrintReady($media["FILE"])."</span></sub>";
		}
		print "</a><br />";

		if (!$isExternal && !$media["EXISTS"]) {
			print "<br /><span class=\"error\">".$pgv_lang["file_not_found"]." <span dir=\"ltr\">".PrintReady($media["FILE"])."</span></span>";
		}
		if (!$isExternal && $media["EXISTS"]) {
			$imageTypes = array("","GIF", "JPG", "PNG", "SWF", "PSD", "BMP", "TIFF", "TIFF", "JPC", "JP2", "JPX", "JB2", "SWC", "IFF", "WBMP", "XBM");
			if (!empty($imgsize[2])) {
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_format"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imageTypes[$imgsize[2]] . "</span>";
			} else if (empty($imgsize[2])) {
				$path_end=substr($media["FILE"], strlen($media["FILE"])-5);
				$imageType = strtoupper(substr($path_end, strpos($path_end, ".")+1));
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_format"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imageType . "</span>";
			}

			$fileSize = media_filesize($media["FILE"]);
			$sizeString = getfilesize($fileSize);
			print "&nbsp;&nbsp;&nbsp;<span class=\"field\" style=\"direction: ltr;\">" . $sizeString . "</span>";

			if ($imgsize[2]!==false) {
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?(" " . getRLM() . "x" . getRLM() . " ") : " x ") . $imgsize[1] . "</span>";
			}
		}

			print "<div style=\"white-space: normal; width: 95%;\">";
			print_fact_sources($media["GEDCOM"], $media["LEVEL"]+1);
			print_fact_notes($media["GEDCOM"], $media["LEVEL"]+1);
			print "</div>";

		PrintMediaLinks($media["LINKS"], "small");

			print "</td></tr></table>\n";
			print "</td>";
			if ($i%2 == 1 && $i < ($count-1)) print "\n\t\t</tr>\n\t\t<tr>";
	}
	print "\n\t\t</tr>";

	// print page back, page number, page forward controls
	print "\n<tr><td colspan=\"2\">\n";
	print"\n\t<table class=\"list_table width100\">\n";
	print "\n<tr>\n";
	print "<td class=\"width30\" align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($currentPage > 1) {
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}")."\">".$IconLDarrow."</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconLarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}")."\">".$IconRDarrow."</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconRarrow."</a>\n";
			}
		}
	}
	print "</td>";
	print "<td align=\"center\">".print_text("page_x_of_y", 0, 1)."</td>";
	print "<td class=\"width30\" align=\"" . ($TEXT_DIRECTION == "ltr"?"right":"left") . "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconRarrow."</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}")."\">".$IconRDarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}")."\">".$IconLarrow."</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"".encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}")."\">".$IconLDarrow."</a>\n";
			}
		}
	}
	print "</td>";
	print "</tr>\n</table></td></tr>";
	print "</table><br />";
}
print "\n</div>\n";
// -- load up the slideshow code
if (!file_exists("modules/lightbox/album.php")) {
	if (file_exists("modules/slideshow/slideshow.php")) {
		include_once("modules/slideshow/slideshow.php");
	}
}
print_footer();

?>

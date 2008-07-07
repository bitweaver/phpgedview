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
 * @version $Id: medialist.php,v 1.5 2008/07/07 18:01:11 lsces Exp $
 */
require_once("config.php");
require_once('includes/functions_print_facts.php');


// LBox -------------------------------------------------------
loadLangFile("lb_lang");
// LBox --------------------------------------------------------

// $LB_SS_SPEED = "5";	

if (!isset($level)) $level = 0;
if (!isset($action)) $action = "";
if (!isset($filter)) $filter = "";
else $filter = stripLRMRLM(stripslashes($filter));
if (!isset($search)) $search = "yes";
if (!isset($folder)) $folder = $MEDIA_DIRECTORY;
if (!isset($_SESSION["medialist"])) $search = "yes";
$currentdironly = (isset($subdirs) && $subdirs=="on") ? false : true;
print_header($pgv_lang["multi_title"]);
print "\n\t<div class=\"center\"><h2>".$pgv_lang["multi_title"]."</h2></div>\n\t";



// LBox ============================================================================= 
// Get Javascript variables from lb_config.php --------------------------- 
 if (file_exists("modules/lightbox/album.php")) {
	include('modules/lightbox/lb_config.php');
	include('modules/lightbox/functions/lb_call_js.php');	
	
	if ($theme_name=="Minimal") {
		// Force icon options to "text" when we're dealing with the Minimal theme
		if ($LB_AL_HEAD_LINKS!="none") $LB_AL_HEAD_LINKS = "text";
		if ($LB_AL_THUMB_LINKS!="none") $LB_AL_THUMB_LINKS = "text";
		if ($LB_ML_THUMB_LINKS!="none") $LB_ML_THUMB_LINKS = "text";
	}

}

// LBox  ================================================================================

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
	$medialist = array();
	get_medialist($currentdironly, $folder, true, false, $showExternal);

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
		        	$disp &= displayDetailsByID($id, $type);
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
	<table class="list-table center width50 <?php print $TEXT_DIRECTION; ?>">
		<?php
			// Box for user to choose the folder
			print "<tr><td class=\"list_label width25\">";
			if ($MEDIA_DIRECTORY_LEVELS > 0) {
				print_help_link("view_server_folder_help", "qm");
				if (empty($folder)) {
					if (!empty($_SESSION['upload_folder'])) $folder = $_SESSION['upload_folder'];
					else $folder = "ALL";
				}
				print $pgv_lang["server_folder"]."</td><td class=\"list_label wrap\">";
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
				print "</select>";
			} else print "<input name=\"folder\" type=\"hidden\" value=\"ALL\" />";
			print "</td></tr>";
		?>
    <?php if ($MEDIA_DIRECTORY_LEVELS > 0) { ?>
		<tr>
			<td class="list_label" colspan="2">
				<label for="subdirs"><?php print $pgv_lang["medialist_recursive"]; ?></label>
				&nbsp;<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?> />
			</td>
		</tr>
    <?php } ?>
		<tr>
			<td class="list_label" colspan="2">
				<?php print_help_link("simple_filter_help","qm"); print $pgv_lang["filter"]; ?>
				&nbsp;<input id="filter" name="filter" value="<?php print PrintReady($filter); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="list_label" colspan="2">
				<select name="max">
				<?php
					if (empty($max)) $max=20;
					foreach (array("10", "20", "30", "40", "50", "75", "100", "125", "150", "200") as $selectEntry) {
						print "<option value=\"$selectEntry\"";
						if ($selectEntry==$max) print " selected=\"selected\"";
						print ">".$selectEntry."</option>";
					}
					print "</select> ".$pgv_lang["per_page"];
				?>
			</td>
		</tr>
		<tr>
			<td class="list_label" colspan="2">
				<input type="submit" value=" &gt; "/>
			</td>
		</tr>
<!-- LBox =========================== added for Lightbox Album ============================== --> 	
		<?php 
		if (file_exists("modules/lightbox/album.php")) { 
			// do not need slideshow bar
		}elseif (file_exists("modules/slideshow.php")) { ?>
<!-- LBox ========================= end addition for Lightbox Album ========================== --> 
		<tr>
			<td class="list_label" colspan="2">
  				<?php
  				print "<a href=\"#\" onclick=\"runSlideShow(); showMe(); return false;\">$pgv_lang[view_slideshow]</a>\n";
  				?>
			</td>
		</tr>
<!-- LBox ======================= BH changed for Lightbox Album ============================ --> 			
		<?php }else{} ?>
<!-- LBox ======================= end change for Lightbox Album ============================= --> 

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
// Count the number of items in the medialist
$ct=count($medialist);
if (!isset($start)) $start = 0;
if (!isset($max)) $max = 20;
$count = $max;
if ($start+$count > $ct) $count = $ct-$start;

print "\n\t<div align=\"center\">".$ct." ".$pgv_lang["media_found"]." <br /><br />";
if ($ct>0){
	if (false) {
		print "<form action=\"$SCRIPT_NAME\" method=\"get\" > ".$pgv_lang["medialist_show"];
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"search\" value=\"yes\" />";
		print "<input type=\"hidden\" name=\"filter\" value=".$filter." />";
		print "<select name=\"max\" onchange=\"javascript:submit();\">";
		for ($i=1;($i<=20&&$i-1<ceil($ct/10));$i++) {
		        print "<option value=\"".($i*10)."\" ";
		        if ($i*10==$max) print "selected=\"selected\" ";
		        print " >".($i*10)."</option>";
		}
		print "</select> ".$pgv_lang["per_page"];
		print "</form>";
	}

	$currentPage = ((int) ($start / $max)) + 1;
	$lastPage = (int) (($ct + $max - 1) / $max);
	$IconRarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconLarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconRDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rdarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconLDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["ldarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";

	print"\n\t<table class=\"list_table\">\n";

	// print page back, page number, page forward controls
	print "\n<tr><td colspan=\"2\">\n";
	print"\n\t<table class=\"list_table width100\">\n";
	print "\n<tr>\n";
	print "<td class=\"width30\" align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($currentPage > 1) {
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=0&amp;max=$max\">".$IconLDarrow."</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconLarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$lastStart&amp;max=$max\">".$IconRDarrow."</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconRarrow."</a>\n";
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
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconRarrow."</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$lastStart&amp;max=$max\">".$IconRDarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconLarrow."</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=0&amp;max=$max\">".$IconLDarrow."</a>\n";
			}
		}
	}
	print "</td>";
	print "</tr>\n</table></td></tr>";

	// -- print the array
	print "\n<tr>\n";

	for($i=0; $i<$count; $i++) {
	    $media = $medialist[$start+$i];

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

		
//LBox --------  change for Lightbox Album --------------------------------------------
		if ( file_exists("modules/lightbox/album.php") && ( eregi("\.jpg",$media["FILE"]) || eregi("\.jpeg",$media["FILE"]) || eregi("\.gif",$media["FILE"]) || eregi("\.png",$media["FILE"]) ) ) { 
//			print "<a href=\"" . $media["FILE"] . "\" rel=\"clearbox[general]\" title=\"" . stripslashes(PrintReady($name1)) . "\">" . "\n";
			print "<a href=\"" . $media["FILE"] . "\" rel=\"clearbox[general]\" title=\"" . $media["XREF"] . ":" . $GEDCOM . ":" . PrintReady($name) . "\">" . "\n";
			
// title=\"" . $rowm["m_media"] . ":" . $GEDCOM . ":" . $mediaTitle . "\"\"

        }elseif ($USE_MEDIA_VIEWER) {
			print "<a href=\"mediaviewer.php?mid=".$media["XREF"]."\">";
		} else {
			print "<a href=\"#\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\">";	
		}
//LBox ----------- end change for Lightbox Album ----------------------------------		

		print "<img src=\"".$media["THUMB"]."\" align=\"left\" class=\"thumbnail\" border=\"none\"";
		if ($isExternal) print " width=\"".$THUMBNAIL_WIDTH."\"";
		print " alt=\"" . PrintReady($name) . "\" title=\"" . PrintReady($name) . "\" /></a>";
		print "</td>\n\t\t<td class=\"list_value_wrap\" style=\"border: none;\" width=\"100%\">";
		
//LBox --------  added for Lightbox Album --------------------------------------------
		if ( file_exists("modules/lightbox/album.php"  )) {
			if (PGV_USER_CAN_EDIT) {
			
				if ($LB_ML_THUMB_LINKS != "none") {
					print "<table border=0><tr>";
		
					// ---------- Edit Media --------------------
					print "<td class=\"width33 wrap center font9\" valign=\"top\">";
					print "<a href=\"javascript:;\" title=\"" . $pgv_lang["lb_edit_media"] . "\" onclick=\" return window.open('addmedia.php?action=editmedia&amp;pid=" . $media["XREF"] . "&amp;linktoid=', '_blank', 'top=50,left=50,width=600,height=600,resizable=1,scrollbars=1');\">";
					if ($LB_ML_THUMB_LINKS == "icon" || $LB_ML_THUMB_LINKS == "both") {
						print "<img src=\"modules/lightbox/images/image_edit.gif\" alt=\"\" class=\"icon\" title=\"" . $pgv_lang["lb_edit_media"] . "\" />&nbsp;&nbsp;&nbsp;" ;
					}
					if($LB_ML_THUMB_LINKS == "both") { 
						print "<br />";
					}
					if($LB_ML_THUMB_LINKS == "both" || $LB_ML_THUMB_LINKS == "text") { 
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
					if($LB_ML_THUMB_LINKS == "both" || $LB_ML_THUMB_LINKS == "text") { 
						print $pgv_lang["lb_view_details"] ;
					}					
					print "</a>" . "\n" ;
					print "</td>";

					print "</tr></table>";	
			}else{
			}
				// ------------ Linespace ---------------------
				print "<br />";
			}else{
			}
		}else{
		}
//LBox ----------- end addition for Lightbox Album ---------------------------------------	

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

		if (!$isExternal && !$media["EXISTS"] ) {
			print "<br /><span class=\"error\">".$pgv_lang["file_not_found"]." <span dir=\"ltr\">".PrintReady($media["FILE"])."</span></span>";
		}
		if (!$isExternal && $media["EXISTS"]){
			$imageTypes = array("","GIF", "JPG", "PNG", "SWF", "PSD", "BMP", "TIFF", "TIFF", "JPC", "JP2", "JPX", "JB2", "SWC", "IFF", "WBMP", "XBM");
			if(!empty($imgsize[2])){
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_format"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imageTypes[$imgsize[2]] . "</span>";
			} else if(empty($imgsize[2])){
				$path_end=substr($media["FILE"], strlen($media["FILE"])-5);
				$imageType = strtoupper(substr($path_end, strpos($path_end, ".")+1));
				print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_format"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imageType . "</span>";
			}

			$fileSize = media_filesize($media["FILE"]);
			$sizeString = getfilesize($fileSize);
			print "&nbsp;&nbsp;&nbsp;<span class=\"field\" style=\"direction: ltr;\">" . $sizeString . "</span>";

			if($imgsize[2]!==false){
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
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=0&amp;max=$max\">".$IconLDarrow."</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconLarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$lastStart&amp;max=$max\">".$IconRDarrow."</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconRarrow."</a>\n";
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
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconRarrow."</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$lastStart&amp;max=$max\">".$IconRDarrow."</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$IconLarrow."</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				print "<a href=\"medialist.php?folder=$folder&amp;filter=$filter&amp;search=no&amp;start=0&amp;max=$max\">".$IconLDarrow."</a>\n";
			}
		}
	}
	print "</td>";
	print "</tr>\n</table></td></tr>";
	print "</table><br />";
}
print "\n</div>\n";
/** not ready for 4.1
require_once("includes/functions_print_lists.php");
$legend = $pgv_lang["media"];
if ($filter) $legend .= " : &laquo;".$filter."&raquo;";
print_media_table($medialist, $legend);
**/
// -- load up the slideshow code
if (file_exists("modules/slideshow/slideshow.php")) include_once("modules/slideshow/slideshow.php");
print_footer();

?>

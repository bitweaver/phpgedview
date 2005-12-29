<?php
/**
 * Displays a list of the multimedia objects
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
 * @subpackage Lists
 * @version $Id: medialist.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */
require("config.php");
global $MEDIA_EXTERNAL;
//global $TEXT_DIRECTION;

  //if ($TEXT_DIRECTION == "ltr")
  //{print "<td class=\"facts_value\" style=\"text-align:left; \">";}
  //else
  //{print "<td class=\"facts_value\" style=\"text-align:right; \">";}

function mediasort($a, $b) {
        return strnatcasecmp($a["TITL"], $b["TITL"]);
}

if (!isset($level)) $level=0;
if (!isset($action)) $action="";
if (!isset($filter)) $filter="";
else $filter = stripslashes($filter);
if (!isset($search)) $search="yes";
if (!isset($medialist) && !isset($_SESSION["medialist"])) $medialist = array();
print_header($pgv_lang["multi_title"]);
print "\n\t<div class=\"center\"><h2>".$pgv_lang["multi_title"]."</h2></div>\n\t";

//-- automatically generate an image
if (userIsAdmin(getUserName()) && $action=="generate" && !empty($file) && !empty($thumb)) {
	generate_thumbnail($file, $thumb);
}
if ($search == "yes") {
	// -- array of names
	$foundlist = array();

	$medialist = array();
	get_medialist();
	
	//-- sort the media by title
	usort($medialist, "mediasort");

	//-- remove all private media objects
	$newmedialist = array();
	foreach($medialist as $indexval => $media) {
	        print " ";
	        $disp = true;
	        $links = $media["LINKS"];
		if (count($links) != 0) {
	        foreach($links as $id=>$type) {
	        	$disp = $disp && displayDetailsByID($id, $type);
	        }
	        if ($disp) $newmedialist[] = $media;
	    }
	    else $newmedialist[] = $media;
	}
	$medialist = $newmedialist;
	$_SESSION["medialist"] = $medialist;
}
else {
	$medialist = $_SESSION["medialist"];
}

// A form for filtering the media items
?>
<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter" />
	<input type="hidden" name="search" value="yes" />
	<table class="list-table center">
		<tr>
		<td class="list-label <?php print $TEXT_DIRECTION; ?>"><?php print_help_link("simple_filter_help","qm"); print $pgv_lang["filter"]; ?></td>
		<td class="list-label <?php print $TEXT_DIRECTION; ?>">&nbsp;<input id="filter" name="filter" value="<?php print $filter; ?>"/></td>
		<td class="list-label <?php print $TEXT_DIRECTION; ?>"><input type="submit" value=" &gt; "/></td>
		</tr>
	</table>
</form>
<?php
if ($action=="filter") {
	if (strlen($filter) >= 1) {
		foreach($medialist as $key => $value) {
			if (stristr($value["TITL"], $filter) === false) {
				$links = $value["LINKS"];
				$person = false;
				$family = false;
				$source = false;
				if (count($links) != 0){
					foreach($links as $id=>$type) {
						if ($type == "INDI") {
							if (!stristr(get_person_name($id), $filter)) $person = false;
							else {
								$person = true;
								break;
							}
						}
						if ($type=="FAM") {
							if (!stristr(get_family_descriptor($id), $filter)) $family = false;
							else {
								$family = true;
								break;
							}
						}
						if ($type=="SOUR") {
							if (!stristr(get_source_descriptor($id), $filter)) $source = false;
							else {
								$source = true;
								break;
							}
						}
						if ($person == false && $family == false && $source == false) unset($medialist[$key]);
					}
				}
				if ($person == false && $family == false && $source == false) unset($medialist[$key]);
			}
		}
		usort($medialist, "mediasort"); // Reset numbering of medialist array
		$_SESSION["medialist"] = $medialist;
	}
}
// Count the number of items in the medialist
$ct=count($medialist);
if (!isset($start)) $start = 0;
if (!isset($max)) $max = 20;
$count = $max;
if ($start+$count > $ct) $count = $ct-$start;

print "\n\t<div align=\"center\">$ct ".$pgv_lang["media_found"]." <br />";
if ($ct>0){
	print "<form action=\"$SCRIPT_NAME\" method=\"get\" > ".$pgv_lang["medialist_show"]." <select name=\"max\" onchange=\"javascript:submit();\">";
	for ($i=1;($i<=20&&$i-1<ceil($ct/10));$i++) {
	        print "<option value=\"".($i*10)."\" ";
	        if ($i*10==$max) print "selected=\"selected\" ";
	        print " >".($i*10)."</option>";
	}
	print "</select> ".$pgv_lang["per_page"];
	print "</form>";
}

print"\n\t<table class=\"list_table\">\n";
if ($ct>$max) {
        print "\n<tr>\n";
        print "<td align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
        if ($start>0) {
                $newstart = $start-$max;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$pgv_lang["prev"]."</a>\n";
        }
        print "</td><td align=\"" . ($TEXT_DIRECTION == "ltr"?"right":"left") . "\">";
        if ($start+$max < $ct) {
                $newstart = $start+$count;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$pgv_lang["next"]."</a>\n";
        }
        print "</td></tr>\n";
}
print"\t\t<tr>\n\t\t";
// -- print the array

for($i=0; $i<$count; $i++) {
    $value = $medialist[$start+$i];
	if ($MEDIA_EXTERNAL && (strstr($value["FILE"], "://")||stristr($value["FILE"], "mailto:"))){
		$image_type = array("bmp", "gif", "jpeg", "jpg", "pcx", "png", "tiff");
		$path_end=substr($value["FILE"], strlen($value["FILE"])-5);
		$type=strtolower(substr($path_end, strpos($path_end, ".")+1));
		if (in_array($type, $image_type)){
		   $imgwidth = 400;
		   $imgheight = 500;
		} else {
		   $imgwidth = 800;
		   $imgheight = 400;
		}
    }
	else if (file_exists(filename_decode($value["FILE"]))) {
		$imgsize = getimagesize(filename_decode($value["FILE"]));
	    $imgwidth = $imgsize[0]+50;
	    $imgheight = $imgsize[1]+50;
	}
	else {
		$imgwidth=300;
		$imgheight=200;
	}
    print "\n\t\t\t<td class=\"list_value_wrap\" width=\"50%\">";
    print "<table class=\"$TEXT_DIRECTION\">\n\t<tr>\n\t\t<td valign=\"top\" style=\"white-space: normal;\">";

   	if (stristr($value["FILE"], "mailto:")){
		if ($MEDIA_EXTERNAL) print "<a href=\"".$value["FILE"]."\">";
	}
    else print "<a href=\"#\" onclick=\"return openImage('".urlencode($value["FILE"])."',$imgwidth, $imgheight);\">";
    if (file_exists(filename_decode($value["THUMB"])) || strstr($value["THUMB"], "://")) {
	    print "<img src=\"".$value["THUMB"]."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" />";
	    $nothumb = false;
    }
	else {
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["media"]["large"]."\" border=\"0\" align=\"left\" class=\"thumbnail\" alt=\"\" />";
		$nothumb = true;
	}
	if (!($MEDIA_EXTERNAL) && stristr($value["FILE"], "mailto:"));
	else print "</a>";
	print "</td>\n\t\t<td class=\"list_value_wrap\" style=\"border: none;\" width=\"100%\">";
	/*
	if (userIsAdmin(getUserName()) && $nothumb && function_exists("imagecreatefromjpeg")
		&& function_exists("imagejpeg") && $AUTO_GENERATE_THUMBS) {
		if ((!strstr($value["file"], "mailto:"))&&(file_exists(filename_decode($value["file"]))||(strstr($value["file"], "://")))) {
			$ct = preg_match("/\.([^\.]+)$/", $value["file"], $match);
			if ($ct>0) {
				$ext = strtolower(trim($match[1]));
				if ($ext=="jpg" || $ext=="jpeg") print "<a href=\"medialist.php?action=generate&amp;max=$max&amp;start=$start&amp;file=".urlencode($value["file"])."&amp;thumb=".urlencode($value["thumb"])."\">".$pgv_lang["generate_thumbnail"]."JPG</a><br />";
				if ($ext=="gif" && function_exists("imagecreatefromgif") && function_exists("imagegif")) print "<a href=\"medialist.php?action=generate&amp;max=$max&amp;start=$start&amp;file=".urlencode($value["file"])."&amp;thumb=".urlencode($value["thumb"])."\">".$pgv_lang["generate_thumbnail"]."GIF</a><br />";
				if ($ext=="png" && function_exists("imagecreatefrompng") && function_exists("imagepng")) print "<a href=\"medialist.php?action=generate&amp;max=$max&amp;start=$start&amp;file=".urlencode($value["file"])."&amp;thumb=".urlencode($value["thumb"])."\">".$pgv_lang["generate_thumbnail"]."PNG</a><br />";
			}
		}
	}
	*/
   	if (stristr($value["FILE"], "mailto:")){
		if ($MEDIA_EXTERNAL) print "<a href=\"".$value["FILE"]."\">";
	}
    else print "<a href=\"#\" onclick=\"return openImage('".urlencode($value["FILE"])."',$imgwidth, $imgheight);\">";
    if ($value["TITL"]==$value["FILE"]) print "<b>&lrm;".$value["TITL"]."</b>";
	else if (trim($value["TITL"]) != "") print "<b>".PrintReady($value["TITL"])."</b>";
	else print "<b>".PrintReady($value["FILE"])."</b>";
	if (!($MEDIA_EXTERNAL) && stristr($value["FILE"], "mailto:"));
	else print "</a>";

    $links = $value["LINKS"];
    if (count($links) != 0){
		$indiexists = 0;
		$famexists = 0;
		foreach($links as $id=>$type) {
			if (($type=="INDI")&&(displayDetailsByID($id))) {
				print " <br /><a href=\"individual.php?pid=".$id."\"> ".$pgv_lang["view_person"]." - ".PrintReady(get_person_name($id))."</a>";
				$indiexists = 1;
			}
			if ($type=="FAM") {
				if ($indiexists && !$famexists) print "<br />";
				$famexists = 1;
           		print "<br /> <a href=\"family.php?famid=".$id."\"> ".$pgv_lang["view_family"]." - ".PrintReady(get_family_descriptor($id))."</a>";
			}
			if ($type=="SOUR") {
				if ($indiexists || $famexists) {
					print "<br />";
					$indiexists = 0;
					$famexists = 0;
				}
				print "<br /> <a href=\"source.php?sid=".$id."\"> ".$pgv_lang["view_source"]." - ".PrintReady(get_source_descriptor($id))."</a>";
   			}
        }
    }
    $value["FILE"] = filename_decode($value["FILE"]);
    if ((!strstr($value["FILE"], "://"))&&(!strstr($value["FILE"], "mailto:"))&&(!file_exists($value["FILE"]))) {
	    print "<br /><span class=\"error\">".$pgv_lang["file_not_found"]." ".$value["FILE"]."</span>";
    }
    print "<br /><br /><div class=\"indent\" style=\"white-space: normal; width: 95%;\">";
    print_fact_notes($value["GEDCOM"], $value["LEVEL"]+1);

    print "</div>";
    if (file_exists($value["FILE"])){
		$imageTypes = array("","GIF", "JPG", "PNG", "SWF", "PSD", "BMP", "TIFF", "TIFF", "JPC", "JP2", "JPX", "JB2", "SWC", "IFF", "WBMP", "XBM");
		if(!empty($imgsize[2])){
	    	print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_format"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imageTypes[$imgsize[2]] . "</span>";
		} else if(empty($imgsize[2])){
		    $path_end=substr($value["FILE"], strlen($value["FILE"])-5);
		    $imageType = strtoupper(substr($path_end, strpos($path_end, ".")+1));
	    	print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_format"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imageType . "</span>";
		}

		if(!empty($imgsize[0]) && !empty($imgsize[0])){
	    	print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["image_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $imgsize[0] . ($TEXT_DIRECTION =="rtl"?" &rlm;x&rlm; " : " x ") . $imgsize[1] . "</span>";
		}

		$fileSize = filesize($value["FILE"]);
		$sizeString = getfilesize($fileSize);
		print "\n\t\t\t<span class=\"label\"><br />".$pgv_lang["media_file_size"].": </span> <span class=\"field\" style=\"direction: ltr;\">" . $sizeString . "</span>";
	}
    print "</td></tr></table>\n";
    print "</td>";
    if ($i%2 == 1 && $i < ($count-1)) print "\n\t\t</tr>\n\t\t<tr>";
}
print "\n\t\t</tr>";
if ($ct>$max) {
        print "\n<tr>\n";
        print "<td align=\"" . ($TEXT_DIRECTION == "ltr"?"left":"right") . "\">";
        if ($start>0) {
                $newstart = $start-$max;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$pgv_lang["prev"]."</a>\n";
        }
        print "</td><td align=\"" . ($TEXT_DIRECTION == "ltr"?"right":"left") . "\">";
        if ($start+$max < $ct) {
                $newstart = $start+$count;
                if ($start<0) $start = 0;
                print "<a href=\"medialist.php?filter=$filter&amp;search=no&amp;start=$newstart&amp;max=$max\">".$pgv_lang["next"]."</a>\n";
        }
        print "</td></tr>\n";
}
print "</table><br />";
print "\n</div>\n";
print_footer();

?>
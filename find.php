<?php
/**
 * Popup window that will allow a user to search for a family id, person id
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
 * @subpackage Display
 * @version $Id: find.php,v 1.5 2008/07/07 18:01:12 lsces Exp $
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
require_once("includes/functions_print_lists.php");
if (!isset($type)) $type = "indi";
if (!isset($filter)) $filter="";
else $filter = trim($filter);
if (!isset($callback)) $callback="paste_id";

// Variables for find media
if (!isset($create)) $create="";
if (!isset($media)) $media="";
if (!isset($external_links)) $external_links = "";
if (!isset($directory)) $directory = $MEDIA_DIRECTORY;
if (!isset($multiple)) $multiple = false;
if (!isset($showthumb)) $showthumb = true;
$thumbget = "";
if ($showthumb) {$thumbget = "&amp;showthumb=true";}
if (!isset($choose)) $choose = "0all";
$embed = substr($choose,0,1)=="1";
$chooseType = substr($choose,1);
if ($chooseType!="media" && $chooseType!="file") $chooseType = "all";

//-- force the thumbnail directory to have the same layout as the media directory
//-- Dots and slashes should be escaped for the preg_replace
$srch = "/".addcslashes($MEDIA_DIRECTORY,'/.')."/";
$repl = addcslashes($MEDIA_DIRECTORY."thumbs/",'/.');
$thumbdir = stripcslashes(preg_replace($srch, $repl, $directory));
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
// End variables for find media

// Variables for Find Special Character
if (!isset($language_filter)) $language_filter="";
if (empty($language_filter)) {
	if (!empty($_SESSION["language_filter"])) $language_filter = $_SESSION["language_filter"];
	else $language_filter=$lang_short_cut[$LANGUAGE];
}
if (!isset($magnify)) $magnify=false;
require 'includes/specialchars.php';

// End variables for Find Special Character

switch ($type) {
	case "indi" :
		print_simple_header($pgv_lang["find_individual"]);
		break;
	case "fam" :
		print_simple_header($pgv_lang["find_fam_list"]);
		break;
	case "media" :
		print_simple_header($pgv_lang["find_media"]);
		$action="filter";
		break;
	case "place" :
		print_simple_header($pgv_lang["find_place"]);
		$action="filter";
		break;
	case "repo" :
		print_simple_header($pgv_lang["repo_list"]);
		$action="filter";
		break;
	case "source" :
		print_simple_header($pgv_lang["find_source"]);
		$action="filter";
		break;
	case "specialchar" :
		print_simple_header($pgv_lang["find_specialchar"]);
		$action="filter";
		break;
}

?>
<script language="JavaScript" type="text/javascript">
<!--
	function pasteid(id, name,thumb) {

	if(thumb)
	{
	window.opener.<?php print $callback; ?>(id,name,thumb);
		<?php if (!$multiple) print "window.close();"; ?>
	}
	else
	{
		window.opener.<?php print $callback; ?>(id);
		if (window.opener.pastename) window.opener.pastename(name);
		<?php if (!$multiple) print "window.close();"; ?>
	}
	}

	var language_filter;
	function paste_char(selected_char,language_filter,magnify) {
		window.opener.paste_char(selected_char,language_filter,magnify);
		return false;
	}

	function setMagnify() {
		document.filterspecialchar.magnify.value = '<?PHP print !$magnify; ?>';
		document.filterspecialchar.submit();
	}

	function checknames(frm) {
		if (document.forms[0].subclick) button = document.forms[0].subclick.value;
		else button = "";
		if (frm.filter.value.length<2&button!="all") {
			alert("<?php print $pgv_lang["search_more_chars"]; ?>");
			frm.filter.focus();
			return false;
		}
		if (button=="all") {
			frm.filter.value = "";
		}
		return true;
	}
//-->
</script>
<?php
$options = array();
$options["option"][]= "findindi";
$options["option"][]= "findfam";
$options["option"][]= "findmedia";
$options["option"][]= "findplace";
$options["option"][]= "findrepo";
$options["option"][]= "findsource";
$options["option"][]= "findspecialchar";
$options["form"][]= "formindi";
$options["form"][]= "formfam";
$options["form"][]= "formmedia";
$options["form"][]= "formplace";
$options["form"][]= "formrepo";
$options["form"][]= "formsource";
$options["form"][]= "formspecialchar";

global $TEXT_DIRECTION, $MULTI_MEDIA;
print "<div align=\"center\">";
print "<table class=\"list_table $TEXT_DIRECTION width90\" border=\"0\">";
print "<tr><td style=\"padding: 10px;\" valign=\"top\" class=\"facts_label03 width90\">"; // start column for find text header

switch ($type) {
	case "indi" :
		print $pgv_lang["find_individual"];
		break;
	case "fam" :
		print $pgv_lang["find_fam_list"];
		break;
	case "media" :
		print $pgv_lang["find_media"];
		break;
	case "place" :
		print $pgv_lang["find_place"];
		break;
	case "repo" :
		print $pgv_lang["repo_list"];
		break;
	case "source" :
		print $pgv_lang["find_source"];
		break;
	case "specialchar" :
		print $pgv_lang["find_specialchar"];
		break;
}

	print "</td>"; // close column for find text header

	// start column for find options
	print "</tr><tr><td class=\"list_value\" style=\"padding: 5px;\">";

	// Show indi and hide the rest
	if ($type == "indi") {
		print "<div align=\"center\">";
		print "<form name=\"filterindi\" method=\"post\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"indi\" />";
		print "<input type=\"hidden\" name=\"multiple\" value=\"$multiple\" />";
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print $pgv_lang["name_contains"]." <input type=\"text\" name=\"filter\" value=\"";
		if (isset($filter)) print $filter;
		print "\" />";
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print "<input type=\"submit\" value=\"".$pgv_lang["filter"]."\" /><br />";
		print "</td></tr></table>";
		print "</form></div>";
	}

	// Show fam and hide the rest
	if ($type == "fam") {
		print "<div align=\"center\">";
		print "<form name=\"filterfam\" method=\"post\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"fam\" />";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"multiple\" value=\"$multiple\" />";
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print $pgv_lang["name_contains"]." <input type=\"text\" name=\"filter\" value=\"";
		if (isset($filter)) print $filter;
		print "\" />";
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print "<input type=\"submit\" value=\"".$pgv_lang["filter"]."\" /><br />";
		print "</td></tr></table>";
		print "</form></div>";
	}

	// Show media and hide the rest
	if ($type == "media" && $MULTI_MEDIA) {
		print "<div align=\"center\">";
		print "<form name=\"filtermedia\" method=\"post\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"choose\" value=\"".$choose."\" />";
		print "<input type=\"hidden\" name=\"directory\" value=\"".$directory."\" />";
		print "<input type=\"hidden\" name=\"thumbdir\" value=\"".$thumbdir."\" />";
		print "<input type=\"hidden\" name=\"level\" value=\"".$level."\" />";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"media\" />";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print $pgv_lang["media_contains"]." <input type=\"text\" name=\"filter\" value=\"";
		if (isset($filter)) print $filter;
		print "\" />";
		print_help_link("simple_filter_help","qm");
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" wstyle=\"padding: 5px;\">";
		print "<input type=\"checkbox\" name=\"showthumb\" value=\"true\"";
		if( $showthumb) print "checked=\"checked\"";
		print "onclick=\"javascript: this.form.submit();\" />".$pgv_lang["show_thumbnail"];
		print_help_link("show_thumb_help","qm");
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print "<input type=\"submit\" name=\"search\" value=\"".$pgv_lang["filter"]."\" onclick=\"this.form.subclick.value=this.name\" />&nbsp;";
		print "<input type=\"submit\" name=\"all\" value=\"".$pgv_lang["display_all"]."\" onclick=\"this.form.subclick.value=this.name\" />";
		print "</td></tr></table>";
		print "</form></div>";
	}

	// Show place and hide the rest
	if ($type == "place") {
		print "<div align=\"center\">";
		print "<form name=\"filterplace\" method=\"post\"  onsubmit=\"return checknames(this);\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"place\" />";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print $pgv_lang["place_contains"]." <input type=\"text\" name=\"filter\" value=\"";
		if (isset($filter)) print $filter;
		print "\" />";
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print "<input type=\"submit\" name=\"search\" value=\"".$pgv_lang["filter"]."\" onclick=\"this.form.subclick.value=this.name\" />&nbsp;";
		print "<input type=\"submit\" name=\"all\" value=\"".$pgv_lang["display_all"]."\" onclick=\"this.form.subclick.value=this.name\" />";
		print "</td></tr></table>";
		print "</form></div>";
	}

	// Show repo and hide the rest
	if ($type == "repo" && $SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL) {
		print "<div align=\"center\">";
		print "<form name=\"filterrepo\" method=\"post\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"repo\" />";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print $pgv_lang["repo_contains"]." <input type=\"text\" name=\"filter\" value=\"";
		if (isset($filter)) print $filter;
		print "\" />";
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print "<input type=\"submit\" name=\"search\" value=\"".$pgv_lang["filter"]."\" onclick=\"this.form.subclick.value=this.name\" />&nbsp;";
		print "<input type=\"submit\" name=\"all\" value=\"".$pgv_lang["display_all"]."\" onclick=\"this.form.subclick.value=this.name\" />";
		print "</td></tr></table>";
		print "</form></div>";
	}

	// Show source and hide the rest
	if ($type == "source" && $SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL) {
		print "<div align=\"center\">";
		print "<form name=\"filtersource\" method=\"post\" onsubmit=\"return checknames(this);\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"source\" />";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"subclick\">"; // This is for passing the name of which submit button was clicked
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print $pgv_lang["source_contains"]." <input type=\"text\" name=\"filter\" value=\"";
		if (isset($filter)) print $filter;
		print "\" />";
		print "</td></tr>";
		print "<tr><td class=\"list_label width10\" style=\"padding: 5px;\">";
		print "<input type=\"submit\" name=\"search\" value=\"".$pgv_lang["filter"]."\" onclick=\"this.form.subclick.value=this.name\" />&nbsp;";
		print "<input type=\"submit\" name=\"all\" value=\"".$pgv_lang["display_all"]."\" onclick=\"this.form.subclick.value=this.name\" />";
		print "</td></tr></table>";
		print "</form></div>";
	}

	// Show specialchar and hide the rest
	if ($type == "specialchar") {
		print "<div align=\"center\">";
		print "<form name=\"filterspecialchar\" method=\"post\" action=\"find.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"filter\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"specialchar\" />";
		print "<input type=\"hidden\" name=\"callback\" value=\"$callback\" />";
		print "<input type=\"hidden\" name=\"magnify\" value=\"".$magnify."\" />";
		print "<table class=\"list_table $TEXT_DIRECTION width100\" border=\"0\">";
		print "<tr><td class=\"list_label\" style=\"padding: 5px;\">";
		print "<select id=\"language_filter\" name=\"language_filter\" onchange=\"submit();\">";
		print "\n\t<option value=\"\">".$pgv_lang["change_lang"]."</option>";
		$language_options = "";
		foreach($specialchar_languages as $key=>$value) {
			$language_options.= "\n\t<option value=\"$key\">$value</option>";
		}
		$language_options = str_replace("\"$language_filter\"","\"$language_filter\" selected",$language_options);
		print $language_options;
		print "</select><br /><a href=\"javascript:;\" onclick=\"setMagnify()\">".$pgv_lang["magnify"]."</a>";
		print "</td></tr></table>";
		print "</form></div>";
	}
	// end column for find options
print "</td></tr>";
print "</table>"; // Close table with find options

print "<br />";
print "<a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br />\n";
print "<br />";

if ($action=="filter") {
	$filter = trim($filter);
	// Output Individual
	if ($type == "indi") {
		$oldged = $GEDCOM;
		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t<tr>";
		$myindilist = search_indis_names($filter);
		$cti=count($myindilist);
		if ($cti>0) {
			$curged = $GEDCOM;
			$printname = array();
			$names = preg_split("/[\s,]+/", $filter);
			print "<td class=\"list_value_wrap\"><ul>";
			foreach ($myindilist as $key => $value) {
				foreach($value["names"] as $indexval => $namearray) {
					foreach($names as $ni=>$name) {
						$found = true;
						if (preg_match("/".$name."/i", $namearray[0])==0) $found=false;
					}
					if ($found) $printname[] = array(sortable_name_from_name($namearray[0]), $key, get_gedcom_from_id($value["gedfile"]));
				}
			}
			uasort($printname, "itemsort");
			foreach($printname as $pkey => $pvalue) {
				$GEDCOM = $pvalue[2];
				if ($GEDCOM != $curged) {
					include get_privacy_file();
					$curged = $GEDCOM;
				}
				echo format_list_person($pvalue[1], array(check_NN($pvalue[0]), $pvalue[2]), true);
				print "\n";
			}
			print "\n\t\t</ul></td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include get_privacy_file();
				$curged = $GEDCOM;
			}
			print "</tr>";
			if ($cti > 0) {
				print "<tr><td class=\"list_value\">".$pgv_lang["total_indis"]." ".$cti;
//				if (count($indi_private)>0) print "  (".$pgv_lang["private"]." ".count($indi_private).")";
//				if (count($indi_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($indi_hide);
//				if (count($indi_private)>0 || count($indi_hide)>0) print_help_link("privacy_error_help", "qm");
				print "</td></tr>";
			}
		}
		else {
			print "<td class=\"list_value_wrap\">";
			print $pgv_lang["no_results"];
			print "</td></tr>";
		}
		print "</table>";
	}

	// Output Family
	if ($type == "fam") {
		$oldged = $GEDCOM;
		$myindilist = array();
		$myfamlist = array();
		$myfamlist2 = array();
		$famquery = array();

		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t<tr>";
		if (find_person_record($filter)) {
			$printname = search_fams_members($filter);
			$ctf = count($printname);
		}
		else {
			$myindilist = search_indis_names($filter);
			foreach($myindilist as $key1 => $myindi) {
				$famquery[] = array($key1, $gGedcom->mGEDCOMId);
			}
			$cti=count($famquery);
			$printname = array();
			if ($cti>0) {
				// Get the famrecs with hits on names from the family table
				$myfamlist = search_fams_names($famquery, "OR", true);
				// Get the famrecs with hits in the gedcom record from the family table
				$myfamlist2 = search_fams($filter, false, "OR", true);
				$myfamlist = pgv_array_merge($myfamlist, $myfamlist2);
				foreach ($myfamlist as $key => $value) {
					// lets see where the hit is
					// makes sure that the spouse name with the hit is listed first
					$found = false;
					foreach($value["name"] as $nkey => $famname) {
						$famsplit = preg_split("/(\s\+\s)/", trim($famname));
						if (preg_match("/".preg_replace("/\s+/", "|", $filter)."/i", $famsplit[0]) != 0) {
							$printname[]=array(check_NN($famname), $key, get_gedcom_from_id($value["gedfile"]));
							$found = true;
							break;
						}
					}
					if (!$found) $printname[] = array(check_NN($value["name"][0]), $key, get_gedcom_from_id($value["gedfile"]));
				}
				$ctf = count($printname);
			}
		}

		if (count($printname)>0) {
			$curged = $GEDCOM;
			print "\n\t\t<td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>";
			uasort($printname, "itemsort");
			foreach($printname as $pkey => $pvalue) {
				$GEDCOM = $pvalue[2];
				if ($GEDCOM != $curged) {
					include get_privacy_file();
					$curged = $GEDCOM;
				}
				echo format_list_family($pvalue[1], array($pvalue[0], $pvalue[2]), true);
			}
			print "\n\t\t</ul></td>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include get_privacy_file();
				$curged = $GEDCOM;
			}
			print "</tr>\n";

			print "<tr><td class=\"list_label\">".$pgv_lang["total_fams"]." ".count($myfamlist);
			if (count($fam_private)>0) print "  (".$pgv_lang["private"]." ".count($fam_private).")";
			if (count($fam_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($fam_hide);
			if (count($fam_private)>0 || count($fam_hide)>0) print_help_link("privacy_error_help", "qm");

			print "</tr></td>";
		}
		else {
			print "<td class=\"list_value_wrap\">";
			print $pgv_lang["no_results"];
			print "</td></tr>";
		}
		print "</table>";
	}

	// Output Media
	if ($type == "media") {
		global $dirs;

		$medialist = get_medialist(true, $directory);

		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t";
		// Show link to previous folder
		if ($level>0) {
			$levels = explode("/", $directory);
			$pdir = "";
			for($i=0; $i<count($levels)-2; $i++) $pdir.=$levels[$i]."/";
			$levels = explode("/", $thumbdir);
			$pthumb = "";
			for($i=0; $i<count($levels)-2; $i++) $pthumb.=$levels[$i]."/";
			$uplink = "<a href=\"find.php?directory=".rawurlencode($pdir)."&amp;thumbdir=".rawurlencode($pthumb)."&amp;level=".($level-1).$thumbget."&type=media&amp;choose=".$choose."\">&nbsp;&nbsp;&nbsp;&lt;-- <span dir=\"ltr\">".$pdir."</span>&nbsp;&nbsp;&nbsp;</a><br />\n";
		}

		// Start of media directory table
		print "<table class=\"list_table $TEXT_DIRECTION width90\">";

		// Tell the user where he is
		print "<tr>";
			print "<td class=\"topbottombar\" colspan=\"2\">";
				print $pgv_lang["current_dir"];
				print "<br />";
				print substr($directory,0,-1);
			print "</td>";
		print "</tr>";

		// display the directory list
		if (count($dirs) || $level) {
			sort($dirs);
			if ($level){
				print "<tr><td class=\"list_value $TEXT_DIRECTION\" colspan=\"2\">";
				print $uplink."</td></tr>";
			}
			print "<tr><td class=\"descriptionbox $TEXT_DIRECTION\" colspan=\"2\">";
			print "<a href=\"find.php?directory=".rawurlencode($directory)."&amp;thumbdir=".rawurlencode(str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $directory))."&amp;level=".$level.$thumbget."&amp;external_links=http&amp;type=media&amp;choose=".$choose."\">".$pgv_lang["external_objects"]."</a>";
			print "</td></tr>";
			foreach ($dirs as $indexval => $dir) {
				print "<tr><td class=\"list_value $TEXT_DIRECTION\" colspan=\"2\">";
				print "<a href=\"find.php?directory=".rawurlencode($directory.$dir."/")."&thumbdir=".rawurlencode($directory.$dir."/")."&level=".($level+1).$thumbget."&amp;type=media&amp;choose=".$choose."\"><span dir=\"ltr\">".$dir."</span></a>";
				print "</td></tr>";
			}
		}
		print "<tr><td class=\"descriptionbox $TEXT_DIRECTION\" colspan=\"2\"></td></tr>";

		/**
		 * This action generates a thumbnail for the file
		 *
		 * @name $create->thumbnail
		 */
		if ($create=="thumbnail") {
			$filename = $_REQUEST["file"];
			generate_thumbnail($directory.$filename,$thumbdir.$filename);
		}

		$applyfilter = ($filter != "");
		print "<br />";

		// display the images TODO x across if lots of files??
		if (count($medialist) > 0) {
			foreach ($medialist as $indexval => $media) {

				// Check if the media belongs to the current folder
				preg_match_all("/\//", $media["FILE"], $hits);
				$ct = count($hits[0]);

				if (($ct <= $level+1 && $external_links != "http" && !isFileExternal($media["FILE"])) || (isFileExternal($media["FILE"]) && $external_links == "http")) {
					// simple filter to reduce the number of items to view
					if ($applyfilter) $isvalid = (strpos(str2lower($media["FILE"]),str2lower($filter)) !== false);
					else $isvalid = true;
					if ($isvalid && $chooseType!="all") {
						if ($chooseType=="file" && !empty($media["XREF"])) $isvalid = false;
						if ($chooseType!="file" && empty($media["XREF"])) $isvalid = false;
					}

					if ($isvalid) {
						if ($media["EXISTS"] && media_filesize($media["FILE"]) != 0){
							$imgsize = findImageSize($media["FILE"]);
							$imgwidth = $imgsize[0]+40;
							$imgheight = $imgsize[1]+150;
						}
						else {
							$imgwidth = 0;
							$imgheight = 0;
						}

						print "<tr>";

						//-- thumbnail field
						if ($showthumb) {
							print "\n\t\t\t<td class=\"list_value $TEXT_DIRECTION width10\">";
							if (isset($media["THUMB"])) print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\"><img src=\"".filename_decode($media["THUMB"])."\" border=\"0\" width=\"50\"></a>\n";
							else print "&nbsp;";
						}

						//-- name and size field
						print "\n\t\t\t<td class=\"list_value $TEXT_DIRECTION\">";
						if ($media["TITL"] != "") {
							print "<b>".PrintReady($media["TITL"])."</b>&nbsp;&nbsp;";
							if ($TEXT_DIRECTION=="rtl") print getRLM();
							print "(".$media["XREF"].")";
							if ($TEXT_DIRECTION=="rtl") print getRLM();
							print "<br />";
						}
						if (!$embed){
							print "<a href=\"javascript:;\" onclick=\"pasteid('".addslashes($media["FILE"])."');\"><span dir=\"ltr\">".$media["FILE"]."</span></a> -- ";
						}
						else print "<a href=\"javascript:;\" onclick=\"pasteid('".$media["XREF"]."','".addslashes($media["TITL"])."','".addslashes($media["THUMB"])."');\"><span dir=\"ltr\">".$media["FILE"]."</span></a> -- ";
						print "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($media["FILE"])."',$imgwidth, $imgheight);\">".$pgv_lang["view"]."</a><br />";
						if (!$media["EXISTS"] && !isFileExternal($media["FILE"])) print $media["FILE"]."<br /><span class=\"error\">".$pgv_lang["file_not_exists"]."</span><br />";
						else if (!isFileExternal($media["FILE"]) && !empty($imgsize[0])) {
							print "<br /><sub>&nbsp;&nbsp;".$pgv_lang["image_size"]." -- ".$imgsize[0]."x".$imgsize[1]."</sub><br />";
						}
						if ($media["LINKED"]) {
							print $pgv_lang["media_linked"]."<br />";
							foreach ($media["LINKS"] as $indi => $type_record) {
								if (isset($pgv_changes[$indi."_".$GEDCOM])) $indirec = find_updated_record($indi);
								else $indirec = find_gedcom_record($indi);
								if ($type_record=="INDI") {
									print " <br /><a href=\"individual.php?pid=".$indi."\"> ".$pgv_lang["view_person"]." - ".PrintReady(get_person_name($indi))."</a>";
								}
								else if ($type_record=="FAM") {
									print "<br /> <a href=\"family.php?famid=".$indi."\"> ".$pgv_lang["view_family"]." - ".PrintReady(get_family_descriptor($indi))."</a>";
								}
								else if ($type_record=="SOUR") {
									print "<br /> <a href=\"source.php?sid=".$indi."\"> ".$pgv_lang["view_source"]." - ".PrintReady(get_source_descriptor($indi))."</a>";
								}
								//-- no reason why we might not get media linked to media. eg stills from movie clip, or differents resolutions of the same item
								else if ($type_record=="OBJE") {
									//print "<br /> <a href=\"media.php?gid=".$indi."\"> ".$pgv_lang["view_object"]." - ".PrintReady(get_source_descriptor($indi))."</a>";
								}
							}
						}
						else {
							print $pgv_lang["media_not_linked"];
						}
						print "\n\t\t\t</td>";
					}
				}
			}
		}
		else {
			print "<tr><td class=\"list_value_wrap\">";
			print $pgv_lang["no_results"];
			print "</td></tr>";
		}
		print "</table>";
	}

	// Output Places
	if ($type == "place") {
		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t<tr>";
		$placelist = array();
		if ( isset($all) || !empty($filter) )
		{
			find_place_list($filter);
			uasort($placelist, "stringsort");
			$ctplace = count($placelist);
			if ($ctplace>0) {
				print "\n\t\t<td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>";
				foreach($placelist as $indexval => $revplace) {
					$levels = preg_split ("/,/", $revplace);		// -- split the place into comma seperated values
					$levels = array_reverse($levels);				// -- reverse the array so that we get the top level first
					$placetext="";
					$j=0;
					foreach($levels as $indexval => $level) {
						if ($j>0) $placetext .= ", ";
						$placetext .= trim($level);
						$j++;
					}
					print "<li><a href=\"javascript:;\" onclick=\"pasteid('".preg_replace(array("/'/",'/"/'), array("\'",'&quot;'), $placetext)."');\">".PrintReady($revplace)."</a></li>\n";
				}
				print "\n\t\t</ul></td></tr>";
				print "<tr><td class=\"list_label\">".$pgv_lang["total_places"]." ".$ctplace;
				print "</td></tr>";
			}
			else {
				print "<tr><td class=\"list_value_wrap $TEXT_DIRECTION\"><ul>";
				print $pgv_lang["no_results"];
				print "</td></tr>";
			}
		}
		print "</table>";
	}

	// Output Repositories
	if ($type == "repo") {
		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t<tr>";
		$repolist = get_repo_list();
		$ctrepo = count($repolist);
		if ($ctrepo>0) {
			print "\n\t\t<td class=\"list_value_wrap\"><ul>";
			foreach ($repolist as $key => $value) {
				$id = $value["id"];
					print "<li><a href=\"javascript:;\" onclick=\"pasteid('$id');\"><span class=\"list_item\">".PrintReady(get_repo_descriptor($key))."&nbsp;&nbsp;&nbsp;";
					if ($TEXT_DIRECTION=="rtl") print getRLM();
					print "(".$key.")";
					if ($TEXT_DIRECTION=="rtl") print getRLM();
					print "</span></a></li>";
			}
			print "</ul></td></tr>";
			print "<tr><td class=\"list_label\">".$pgv_lang["repos_found"]." ".$ctrepo;
			print "</td></tr>";
		}
		else {
			print "<tr><td class=\"list_value_wrap\">";
			print $pgv_lang["no_results"];
			print "</td></tr>";
		}
		print "</table>";

	}
	// Output Sources
	if ($type == "source") {
		$oldged = $GEDCOM;
		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t<tr>\n\t\t<td class=\"list_value\"><tr>";
		if (!isset($filter) || !$filter) $mysourcelist = get_source_list();
		else $mysourcelist = search_sources($filter);
		uasort($mysourcelist, "itemsort");
		$cts=count($mysourcelist);
		if ($cts>0) {
			$curged = $GEDCOM;
			print "\n\t\t<td class=\"list_value_wrap\"><ul>";
			foreach ($mysourcelist as $key => $value) {
				print "<li>";
				print "<a href=\"javascript:;\" onclick=\"pasteid('$key', '".preg_replace("/(['\"])/", "\\$1", PrintReady($value["name"]))."'); return false;\"><span class=\"list_item\">".PrintReady($value["name"])."</span></a>\n";
				print "</li>\n";
			}
			print "</ul></td></tr>";
			$GEDCOM = $oldged;
			if ($GEDCOM != $curged) {
				include get_privacy_file();
				$curged = $GEDCOM;
			}
			if ($cts > 0) print "<tr><td class=\"list_label\">".$pgv_lang["total_sources"]." ".$cts."</td></tr>";
		}
		else {
			print "<tr><td class=\"list_value_wrap\">";
			print $pgv_lang["no_results"];
			print "</td></tr>";
		}
		print "</table>";
		if (PGV_USER_CAN_EDIT) {
			print_help_link("edit_add_unlinked_source_help", "qm"); ?><a href="javascript: <?php print $pgv_lang["add_unlinked_source"]; ?>" onclick="addnewsource(''); return false;"><?php print $pgv_lang["add_unlinked_source"]; ?></a>
		<?php
		}
	}

	// Output Special Characters
	if ($type == "specialchar") {
		print "\n\t<table class=\"tabs_table $TEXT_DIRECTION width90\">\n\t\t<tr>\n\t\t<td class=\"list_value\">";
		//upper case special characters
		foreach($ucspecialchars as $key=>$value) {
			$value = str_replace("'","\'",$value);
			print "\n\t\t\t<a href=\"javascript:;\" onclick=\"return paste_char('$value','$language_filter','$magnify');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">";
			if ($magnify) print "<span class=\"largechars\">";
			print $key;
			if ($magnify) print "</span>";
			print "</span></a><br />";
		}
		print "</td>\n\t\t<td class=\"list_value\">";
		// lower case special characters
		foreach($lcspecialchars as $key=>$value) {
			$value = str_replace("'","\'",$value);
			print "\n\t\t\t<a href=\"javascript:;\" onclick=\"return paste_char('$value','$language_filter','$magnify');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">";
			if ($magnify) print "<span class=\"largechars\">";
			print $key;
			if ($magnify) print "</span>";
			print "</span></a><br />\n";
		}
		print "</td>\n\t\t<td class=\"list_value\">";
		// other special characters (not letters)
		foreach($otherspecialchars as $key=>$value) {
			$value = str_replace("'","\'",$value);
			print "\n\t\t\t<a href=\"javascript:;\" onclick=\"return paste_char('$value','$language_filter','$magnify');\"><span class=\"list_item\" dir=\"$TEXT_DIRECTION\">";
			if ($magnify) print "<span class=\"largechars\">";
			print $key;
			if ($magnify) print "</span>";
			print "</span></a><br />\n";
		}
		print "\n\t\t</td>\n\t\t</tr>\n\t</table>";
	}
}
print "</div>"; // Close div that centers table
?>
<script language="JavaScript" type="text/javascript">
<!--
	document.filter<?php print $type; ?>.filter.focus();
//-->
</script>
<?php
print_simple_footer();

?>

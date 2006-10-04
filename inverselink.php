<?php
/**
 * Link media items to indi, sour and fam records
 *
 * This is the page that does the work of linking items.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @subpackage MediaDB
 * @version $Id: inverselink.php,v 1.4 2006/10/04 12:07:54 lsces Exp $
 */
require("config.php");
require("includes/functions_edit.php");
require($factsfile["english"]);

//-- page parameters and checking
$paramok = true;
if (!isset($mediaid)) $mediaid = ""; 
if (!isset($linkto)) {$linkto = ""; $paramok = false;}
if (!isset($action)) $action = "choose";
if ($linkto == "person") $toitems = $pgv_lang["to_person"];
else if ($linkto == "source") $toitems = $pgv_lang["to_source"];
else if ($linkto == "family") $toitems = $pgv_lang["to_family"];
else {
	$toitems = "???";
	$paramok = false;
}

if (!empty($mediaid)) {
	//-- evil script protection
	if ( preg_match("/M\d{4,8}/",$mediaid, $matches) == 1 ) {
		$mediaid=$matches[0];
	}
	else $paramok = false;
}
else if (empty($linktoid)) $paramok = false;


print_simple_header($pgv_lang["link_media"]." ".$toitems);

//-- check for admin
$paramok =  userCanEdit(getUserName());
if (!empty($linktoid)) $paramok = displayDetails(find_gedcom_record($linktoid));

if ($action == "choose" && $paramok) {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	var language_filter, magnify;
	language_filter = "";
	magnify = "";

	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}

	function paste_id(value) {
		pastefield.value = value;
	}

	function paste_char(value,lang,mag) {
		pastefield.value += value;
		language_filter = lang;
		magnify = mag;
	}
	//-->
	</script>
	<script src="./js/phpgedview.js" language="JavaScript" type="text/javascript"></script>

	<?php
	print "<form name=\"link\" method=\"post\" action=\"inverselink.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
	if (!empty($mediaid)) print "<input type=\"hidden\" name=\"mediaid\" value=\"".$mediaid."\" />\n";
	if (!empty($linktoid)) print "<input type=\"hidden\" name=\"linktoid\" value=\"".$linktoid."\" />\n";
	print "<input type=\"hidden\" name=\"linkto\" value=\"".$linkto."\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"".$GEDCOM."\" />\n";
	print "<table class=\"facts_table center ".$TEXT_DIRECTION."\">";
	print "\n\t<tr><td class=\"topbottombar\" colspan=\"2\">";
	print_help_link("add_media_linkid","qm", "link_media");
	print $pgv_lang["link_media"]." ".$toitems."</td></tr>";
	print "<tr><td class=\"descriptionbox width20 wrap\">".$pgv_lang["media_id"]."</td>";
	if (!empty($mediaid)) {
		//-- Get the title of this existing Media item
		$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."media where m_media = '".$mediaid."' AND m_gedfile = '".$GEDCOMS[$GEDCOM]["id"]."'";
		$tempsql = $gGedcom->mDb->query($sql);
		$res =& $tempsql;
		$row =& $res->fetchRow();
		if (trim($row["m_titl"])=="") {
			print "<td class=\"optionbox wrap\"><b>".$mediaid."</b></td></tr>";
		} else {
			print "<td class=\"optionbox wrap\"><b>".PrintReady($row["m_titl"])."</b>&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			print "(".$mediaid.")";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			print "</td></tr>";
		}
 	} else {
		print "<td class=\"optionbox wrap\"><input type=\"text\" name=\"mediaid\" id=\"mediaid\" size=\"5\" />";
		print_findmedia_link("mediaid","1media");
		print "</td></tr>";
	}

	if (!isset($linktoid)) $linktoid = "";
	print "<tr><td class=\"descriptionbox\">";
	if ($linkto == "person") {
		print $pgv_lang["enter_pid"]."</td>";
		print "<td class=\"optionbox wrap\">";
		if ($linktoid=="") {
			print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
			print_findindi_link("linktoid","");
		} else {
			print "<b>".PrintReady(get_person_name($linktoid))."</b>";
			print "&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			print "(".$linktoid.")";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
		}
	}
    
	if ($linkto == "family") {
		print $pgv_lang["enter_famid"]."</td>";
		print "<td class=\"optionbox wrap\">";
		if ($linktoid=="") {
			print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
			print_findfamily_link("linktoid");
		} else {
			print "<b>".PrintReady(get_family_descriptor($linktoid))."</b>";
			print "&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			print "(".$linktoid.")";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
		}
	}
    
	if ($linkto == "source") {
		print $pgv_lang["source"]."</td>";
		print "<td  class=\"optionbox wrap\">";
		if ($linktoid=="") {
			print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
			print_findsource_link("linktoid");
		} else {
			print "<b>".PrintReady(get_source_descriptor($linktoid))."</b>";
			print "&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
			print "(".$linktoid.")";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;";
		}
	}
	print "</td></tr>";
	print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["set_link"]."\" /></td></tr>";
	print "</table>";
	print "</form>\n";
	print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	print_simple_footer();

}
elseif ($action == "update" && $paramok) {
	linkMedia($mediaid, $linktoid);
	print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	print_simple_footer();
}
else {
	print "<center>nothing to do<center>";

	print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";

	print_simple_footer();

} // $paramok


?>

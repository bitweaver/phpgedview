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
 * @version $Id: inverselink.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */
require("config.php");
require("includes/functions_edit.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);

//-- page parameters and checking
$paramok = true;
if (!isset($mediaid)) {$mediaid = ""; $paramok = false;}
if (!isset($linkto)) {$linkto = ""; $paramok = false;}
if (!isset($action)) $action = "choose";
if ($linkto == "person") $toitems = $pgv_lang["to_person"];
else if ($linkto == "source") $toitems = $pgv_lang["to_source"];
else if ($linkto == "family") $toitems = $pgv_lang["to_family"];
else {
	$toitems = "???";
	$paramok = false;
}

//-- evil script protection
if ( preg_match("/M\d{4,8}/",$mediaid, $matches) == 1 ) {
	$mediaid=$matches[0];
}
else $paramok = false;


print_simple_header($pgv_lang["link_media"]." ".$toitems);

//-- check for admin
$paramok =  userIsAdmin(getUserName());

if ($action == "choose" && $paramok) {
	?>
	<script language="JavaScript" type="text/javascript">
	var pastefield;
	var language_filter, magnify;
	language_filter = "";
	magnify = "";

	function addnewsource(field) {
		pastefield = field;
		window.open('edit_interface.php?action=addnewsource&amp;pid=newsour', '', 'top=70,left=70,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}

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
	</script>
	<script src="phpgedview.js" language="JavaScript" type="text/javascript"></script>

	<?php
	if (!isset($linktoid)) $linktoid = "";
	print "<form name=\"link\" method=\"post\" action=\"inverselink.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
	print "<input type=\"hidden\" name=\"mediaid\" value=\"$mediaid\" />\n";
	print "<input type=\"hidden\" name=\"linkto\" value=\"$linkto\" />\n";
	print "<input type=\"hidden\" name=\"ged\" value=\"$GEDCOM\" />\n";
	print "<table class=\"facts_table center $TEXT_DIRECTION\">";
	print "\n\t<tr><td class=\"topbottombar\" colspan=\"2\">";
	print_help_link("admin_link_media_help","qm", "link_media");
	print $pgv_lang["link_media"]." ".$toitems."</td></tr>";
	print "<tr><td class=\"descriptionbox width20 wrap\">".$pgv_lang["media_id"]."</td><td class=\"optionbox\">".$mediaid."</td></tr>";

	print "<tr><td class=\"descriptionbox\">";
	if ($linkto == "person") {
		print $pgv_lang["enter_pid"]."</td>";
		print "<td class=\"optionbox\">";
		print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
		print_findindi_link("linktoid","");
	}

	if ($linkto == "family") {
		print $pgv_lang["enter_famid"]."</td>";
		print "<td class=\"optionbox\">";
		print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
		print_findfamily_link("linktoid");
	}

	if ($linkto == "source") {
		print $pgv_lang["source"]."</td>";
		print "<td  class=\"optionbox\">";
		print "<input class=\"pedigree_form\" type=\"text\" name=\"linktoid\" id=\"linktoid\" size=\"3\" value=\"$linktoid\" />";
		print_findsource_link("linktoid");
	}
	print "</td></tr>";
	print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["set_link"]."\" /></td></tr>";
	print "</table>";
	print "</form>\n";
	print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	print_simple_footer();

}
elseif ($action == "update" && $paramok) {
	// find indi
	$indirec = find_gedcom_record($linktoid);

	if ($indirec) {
		$mediarec = "1 OBJE @".$mediaid."@\r\n";
		$newrec = trim($indirec."\r\n".$mediarec);

		// update the database
		if (update_db_link($mediaid, $linktoid, $mediarec, $ged, -1)) {
			AddToChangeLog("Database link update is OK");
			// TODO: Add variable
			print "DB update OK";
		}
		else {
			// TODO: Add variable
			AddToChangeLog("There was an error updating the database link");
			print "DB upate KO";
		}
		replace_gedrec($linktoid, $newrec);

	}
	else print "<br /><center>".$pgv_lang["invalid_id"]."</center>";
	print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
	print_simple_footer();
}
else {
	print "<center>nothing to do<center>";

	print "<br/><br/><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";

	print_simple_footer();

} // $paramok


?>

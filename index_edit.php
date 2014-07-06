<?php
/**
 * MyGedView page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and Others
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
 * This Page Is Valid XHTML 1.0 Transitional! > 13 August 2005
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id$
 */

/**
 * load the main configuration and context
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/index_cache.php");

/**
 * Block definition array
 *
 * The following block definition array defines the
 * blocks that can be used to customize the portals
 * their names and the function to call them
 * "name" is the name of the block in the lists
 * "descr" is the name of a pgv_lang var to describe this block.  Eg: if the block is
 * 			described by $pgv_lang["my_block_text"], put "my_block_text" here.
 * "type" the options are "user" or "gedcom" or undefined
 * - The type determines which lists the block is available in.
 * - Leaving the type undefined allows it to be on both the user and gedcom portal
 * @global $PGV_BLOCKS
 */

loadLangFile("pgv_confighelp");

global $pgv_lang, $PGV_USE_HELPIMG, $PGV_IMAGES, $PGV_IMAGE_DIR, $TEXT_DIRECTION;
global $GEDCOM_TITLE;

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['ctype'])) $ctype = $_REQUEST['ctype'];
if (isset($_REQUEST['main'])) $main = $_REQUEST['main'];
if (isset($_REQUEST['right'])) $right = $_REQUEST['right'];
if (isset($_REQUEST['setdefault'])) $setdefault = $_REQUEST['setdefault'];
if (isset($_REQUEST['side'])) $side = $_REQUEST['side'];
if (isset($_REQUEST['index'])) $index = $_REQUEST['index'];
if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];

//-- make sure that they have user status before they can use this page
//-- otherwise have them login again
if (!PGV_USER_ID) {
	print_simple_header("");
	print $pgv_lang["access_denied"];
	print "<div class=\"center\"><a href=\"javascript:;\" onclick=\"self.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}
if (!PGV_USER_IS_ADMIN) $setdefault=false;

if (!isset($action)) $action="";
if (!isset($ctype)) $ctype="user";
if (!isset($main)) $main=array();
if (!isset($right)) $right=array();
if (!isset($setdefault)) $setdefault=false;
if (!isset($side)) $side="main";
if (!isset($index)) $index=1;

// Define all the icons we're going to use
$IconHelp = $pgv_lang["qm"];
if ($PGV_USE_HELPIMG) {
	$IconHelp = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["help"]["small"]."\" class=\"icon\" width=\"15\" height=\"15\" alt=\"\" />";
}
$IconUarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["uarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconRarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconLarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconRDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rdarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconLDarrow = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["ldarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";

if ($ctype=="user") {
	print_simple_header($pgv_lang["mygedview"]);
} else {
	print_simple_header(get_gedcom_setting(PGV_GED_ID, 'title'));
}

$GEDCOM_TITLE = PrintReady($gGedcom->mInfo['title']);  // needed in $pgv_lang["rss_descr"]

?>
<script language="JavaScript" type="text/javascript">
<!--
function parentrefresh() {
	opener.window.location.reload();
	window.close();
}
//-->
</script>
	<?php
	//--------------------------------Start 1st tab Configuration page
	?>
	<div id="configure" class="tab_page center" style="position: absolute; display: block; top: auto; left: auto; z-index: 1; ">
	<br />
	<form name="config_setup" method="post" action="index_edit.php">
	<input type="hidden" name="ctype" value="<?php print $ctype;?>" />
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="name" value="<?php print $name;?>" />
	<table dir="ltr" border="1" width="400px">
	<tr><td class="topbottombar" colspan="7">
	<?php
	print_help_link("portal_config_intructions", "qm");
	if ($ctype=="user") print "<b>".str2upper($pgv_lang["customize_page"])."</b>";
	else print "<b>".str2upper($pgv_lang["customize_gedcom_page"])."</b>";
	print "</td></tr>";
	// NOTE: Row 1: Column legends
	print "<tr>";
		print "<td class=\"descriptionbox center vmiddle\" colspan=\"2\">\n";
			print "<b>".$pgv_lang["main_section"]."</b>";
		print "</td>\n";
		print "<td class=\"descriptionbox center vmiddle\" colspan=\"3\">";
			print " <a href=\"javascript:;\" class=\"help\" tabindex=\"0\" onclick=\"expand_layer('help',true); expand_layer('configure', false);\">".$IconHelp."</a> \n";
			// Need to create a real popup for this some time ;)
			print "<b>".$pgv_lang["available_blocks"]."</b>";
		print "</td>\n";
		print "<td class=\"descriptionbox center vmiddle\" colspan=\"2\">";
			print "<b>".$pgv_lang["right_section"]."</b>";
		print "</td>";
	print "</tr>\n";
	print "<tr>";
	// NOTE: Row 2 column 1: Up/Down buttons for left (main) block list
	print "<td class=\"optionbox width20px center vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_up_block('main_select');\" title=\"".$pgv_lang["move_up"]."\">".$IconUarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_down_block('main_select');\" title=\"".$pgv_lang["move_down"]."\">".$IconDarrow."</a>";
		print "<br /><br />";
		print_help_link("block_move_up_help", "qm");

	print "</td>";
	// NOTE: Row 2 column 2: Left (Main) block list
	print "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">\n";
		print "<select multiple=\"multiple\" id=\"main_select\" name=\"main[]\" size=\"10\" onchange=\"show_description('main_select');\">\n";
		foreach($ublocks["main"] as $indexval => $block) {
			if (function_exists($block[0])) {
				print "<option value=\"$block[0]\">".$PGV_BLOCKS[$block[0]]["name"]."</option>\n";
			}
		}
		print "</select>\n";
	print "</td>";
	// NOTE: Row 2 column 3: Left/Right buttons for left (main) block list
	print "<td class=\"optionbox width20 vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('main_select', 'right_select');\" title=\"".$pgv_lang["move_right"]."\">".$IconRDarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('main_select', 'available_select');\" title=\"".$pgv_lang["remove"]."\">".$IconRarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('available_select', 'main_select');\" title=\"".$pgv_lang["add"]."\">".$IconLarrow."</a>";
		print "<br /><br />";
		print_help_link("block_move_right_help", "qm");

	print "</td>";
	// Row 2 column 4: Middle (Available) block list
	print "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">";
		print "<select id=\"available_select\" name=\"available[]\" size=\"10\" onchange=\"show_description('available_select');\">\n";
		foreach($SortedBlocks as $key => $value) {
			if (!isset($PGV_BLOCKS[$key]["type"])) $PGV_BLOCKS[$key]["type"]=$ctype;
			print "<option value=\"$key\">".$SortedBlocks[$key]."</option>\n";
		}
		print "</select>\n";
	print "</td>";
	// NOTE: Row 2 column 5: Left/Right buttons for right block list
	print "<td class=\"optionbox width20 vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('right_select', 'main_select');\" title=\"".$pgv_lang["move_left"]."\">".$IconLDarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('right_select', 'available_select');\" title=\"".$pgv_lang["remove"]."\">".$IconLarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('available_select', 'right_select');\" title=\"".$pgv_lang["add"]."\">".$IconRarrow."</a>";
		print "<br /><br />";
		print_help_link("block_move_right_help", "qm");
	print "</td>";
	// NOTE: Row 2 column 6: Right block list
	print "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">";
		print "<select multiple=\"multiple\" id=\"right_select\" name=\"right[]\" size=\"10\" onchange=\"show_description('right_select');\">\n";
		foreach($ublocks["right"] as $indexval => $block) {
			if (function_exists($block[0])) {
				print "<option value=\"$block[0]\">".$PGV_BLOCKS[$block[0]]["name"]."</option>\n";
			}
		}
		print "</select>\n";
	print "</td>";
	// NOTE: Row 2 column 7: Up/Down buttons for right block list
	print "<td class=\"optionbox width20 vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_up_block('right_select');\" title=\"".$pgv_lang["move_up"]."\">".$IconUarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_down_block('right_select');\" title=\"".$pgv_lang["move_down"]."\">".$IconDarrow."</a>";
		print "<br /><br />";
		print_help_link("block_move_up_help", "qm");
	print "</td>";
	print "</tr>";
	// NOTE: Row 3 columns 1-7: Summary description of currently selected block
	print "<tr><td class=\"descriptionbox wrap\" colspan=\"7\" dir=\"".$TEXT_DIRECTION."\"><div id=\"instructions\">";
	print $pgv_lang["index_edit_advice"];
	print "</div></td></tr>";
	print "<tr><td class=\"topbottombar\" colspan=\"7\">";

	if (PGV_USER_IS_ADMIN && $ctype=='user') {
		print $pgv_lang["use_blocks_for_default"]."<input type=\"checkbox\" name=\"setdefault\" value=\"1\" /><br /><br />\n";
	}

	if ($ctype=='user') {
		print_help_link("block_default_portal", "qm");
	}
	else {
		print_help_link("block_default_index", "qm");
	}
	print "<input type=\"button\" value=\"".$pgv_lang["reset_default_blocks"]."\" onclick=\"window.location='index_edit.php?ctype=$ctype&amp;action=reset&amp;name=".preg_replace("/'/", "\'", $name)."';\" />\n";
	print "&nbsp;&nbsp;";
	print_help_link("click_here_help", "qm");
	print "<input type=\"button\" value=\"".$pgv_lang["click_here"]."\" onclick=\"select_options(); save_form();\" />\n";
	print "&nbsp;&nbsp;";
	print "<input type =\"button\" value=\"".$pgv_lang["cancel"]."\" onclick=\"window.close()\" />";
	if (PGV_USER_GEDCOM_ADMIN && $ctype!="user") {
		print "<br />";
		print_help_link("clear_cache_help", "qm");
		print "<input type =\"button\" value=\"".$pgv_lang["clear_cache"]."\" onclick=\"window.location='index_edit.php?ctype=$ctype&amp;action=clearcache&amp;name=".preg_replace("/'/", "\'", $name)."';\" />";
	}
	print "</td></tr></table>";
	print "</form>\n";

	// end of 1st tab
	print "</div>\n";

	//--------------------------------Start 2nd tab Help page
	print "\n\t<div id=\"help\" class=\"tab_page\" style=\"position: absolute; display: none; top: auto; left: auto; z-index: 2; \">\n\t";

	print "<br /><center><input type=\"button\" value=\"".$pgv_lang["click_here"]."\" onclick=\"expand_layer('configure', true); expand_layer('help', false);\" /></center><br /><br />\n";
	print_text("block_summaries");

	// end of 2nd tab
	print "</div>\n";
}

print "</body></html>";		// Yes! Absolutely NOTHING at page bottom, please.
?>

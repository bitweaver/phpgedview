<?php
/**
 * Interface to review/accept/reject changes made by editing online.
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
 * @subpackage Edit
 * @version $Id: edit_changes.php,v 1.4 2007/05/27 10:31:35 lsces Exp $
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
require "includes/functions_edit.php";
require "includes/functions_import.php";
require $INDEX_DIRECTORY."pgv_changes.php";
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

if (!userCanAccept(getUserName())) {
	header("Location: login.php?url=edit_changes.php");
	exit;
}

if (empty($action)) $action="";

print_simple_header($pgv_lang["review_changes"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record(xref) {
		var recwin = window.open("gedrecord.php?fromfile=1&pid="+xref, "_blank", "top=50,left=50,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
	}
	function showchanges() {
	   window.location = '<?php print $SCRIPT_NAME; ?>';
   }

	function show_diff(diffurl) {
		window.opener.location = diffurl;
		return false;
	}
//-->
</script>
<?php
print "<div class=\"center\">\n";
print "<span class=\"subheaders\">";
print $pgv_lang["review_changes"];
print "</span><br /><br />\n";

if (!isset($cid)) $cid = "";

if ($action=="undo" && isset($pgv_changes[$cid])) {
	if (undo_change($cid, $index)) {
		print "<br /><br /><b>";
		print $pgv_lang["undo_successful"];
		print "</b><br /><br />";
	}
}
if ($action=="undoall") {
	//-- alert that we only want to save the file and changes once
	$manual_save = true;
	$temp_changes = $pgv_changes;
	foreach($temp_changes as $cid=>$changes) {
		$change = $changes[0];
		if ($change["gedcom"]==$ged) undo_change($cid, 0);
	}
	write_file();
	$manual_save = false;
	print "<br /><br /><b>";
	print $pgv_lang["undo_successful"];
	print "</b><br /><br />";
}

if ($action=="accept" && isset($pgv_changes[$cid])) {
	if (accept_changes($cid)) {
		print "<br /><br /><b>";
		print $pgv_lang["accept_successful"];
		print "</b><br /><br />";
	}
}
if ($action=="acceptall") {
	$temp_changes = $pgv_changes;
	//-- only save the file and changes once
	$manual_save = true;
	foreach($temp_changes as $cid=>$changes) {
		for($i=0; $i<count($changes); $i++) {
			$change = $changes[$i];
			if ($change["gedcom"]==$ged) accept_changes($cid);
		}
	}
	write_changes();
	$manual_save = false;
	print "<br /><br /><b>";
	print $pgv_lang["accept_successful"];
	print "</b><br /><br />";
}

if (count($pgv_changes)==0) {
	print "<br /><br /><b>";
	print $pgv_lang["no_changes"];
	print "</b>";
}
else {
	$output = "<br /><table class=\"list_table\">";
	$output .= "<tr><td class=\"list_value $TEXT_DIRECTION\">";
	$changedgedcoms = array();
	foreach($pgv_changes as $cid=>$changes) {
		for($i=0; $i<count($changes); $i++) {
			$change = $changes[$i];
			if ($i==0) {
				$changedgedcoms[$change["gedcom"]] = true;
				if ($GEDCOM != $change["gedcom"]) {
					$GEDCOM = $change["gedcom"];
				}
				$gedrec = find_record_in_file($change["gid"]);
				if (empty($gedrec)) $gedrec = $change["undo"];
				$ct = preg_match("/0 @(.*)@(.*)/", $gedrec, $match);
				if ($ct>0) $type = trim($match[2]);
				else $type = "INDI";
				if ($type=="INDI") {
					$names = get_indi_names($gedrec);
					$output .= "<b>".PrintReady(check_NN($names[0][0]))."</b> &lrm;(".$change["gid"].")&lrm;<br />\n";
				}
				else if ($type=="FAM") $output .= "<b>".PrintReady(get_family_descriptor($change["gid"]))."</b> &lrm;(".$change["gid"].")&lrm;<br />\n";
				else if ($type=="SOUR") {
					$name = get_gedcom_value("ABBR", 1, $gedrec);
					if (empty($name)) $name = get_gedcom_value("TITL", 1, $gedrec);
					$output .= "<b>".PrintReady($name)."</b> &lrm;(".$change["gid"].")&lrm;<br />\n";
				}
				else $output .= "<b>".$factarray[$type]."</b> &lrm;(".$change["gid"].")&lrm;<br />\n";
				if ($type=="INDI") $output .= "<a href=\"javascript:;\" onclick=\"return show_diff('individual.php?pid=".$change["gid"]."&amp;ged=".$change["gedcom"]."&amp;show_changes=yes');\">".$pgv_lang["view_change_diff"]."</a> | \n";
				if ($type=="FAM") $output .= "<a href=\"javascript:;\" onclick=\"return show_diff('family.php?famid=".$change["gid"]."&amp;ged=".$change["gedcom"]."&amp;show_changes=yes');\">".$pgv_lang["view_change_diff"]."</a> | \n";
				if ($type=="SOUR") $output .= "<a href=\"javascript:;\" onclick=\"return show_diff('source.php?sid=".$change["gid"]."&amp;ged=".$change["gedcom"]."&amp;show_changes=yes');\">".$pgv_lang["view_change_diff"]."</a> | \n";
				$output .= "<a href=\"javascript:show_gedcom_record('".$change["gid"]."');\">".$pgv_lang["view_gedcom"]."</a> | ";
				$output .= "<a href=\"javascript:;\" onclick=\"return edit_raw('".$change["gid"]."');\">".$pgv_lang["edit_raw"]."</a><br />";
				$output .= "<div class=\"indent\">\n";
				$output .= $pgv_lang["changes_occurred"]."<br />\n";
				$output .= "<table class=\"list_table\">\n";
				$output .= "<tr><td class=\"list_label\">".$pgv_lang["undo"]."</td>";
				$output .= "<td class=\"list_label\">".$pgv_lang["accept"]."</td>";
				$output .= "<td class=\"list_label\">".$pgv_lang["type"]."</td><td class=\"list_label\">".$pgv_lang["username"]."</td><td class=\"list_label\">".$pgv_lang["date"]."</td><td class=\"list_label\">GEDCOM</td></tr>\n";
			}
			if ($i==count($changes)-1) {
				$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"edit_changes.php?action=undo&amp;cid=$cid&amp;index=$i\">".$pgv_lang["undo"]."</a></td>";
				$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"edit_changes.php?action=accept&amp;cid=$cid\">".$pgv_lang["accept"]."</a></td>\n";
			}
			else {
				$output .= "<td class=\"list_value $TEXT_DIRECTION\"><br /></td>";
				$output .= "<td class=\"list_value $TEXT_DIRECTION\"><br /></td>";
			}
			$output .= "<td class=\"list_value $TEXT_DIRECTION\"><b>".$pgv_lang[$change["type"]]."</b></td>\n";
			$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"javascript:;\" onclick=\"return reply('".$change["user"]."','".$pgv_lang["review_changes"]."')\" alt=\"".$pgv_lang["message"]."\">";
			$cuser = getUser($change["user"]);
			if ($cuser) {
				$output .= PrintReady($cuser["firstname"]." ".$cuser["lastname"]);
			}
 			$output .= PrintReady("&nbsp;(".$change["user"].")")."</a></td>\n";
 			$output .= "<td class=\"list_value $TEXT_DIRECTION\">".get_changed_date(date("j M Y",$change["time"]))." ".date($TIME_FORMAT, $change["time"])."</td>\n";
			$output .= "<td class=\"list_value $TEXT_DIRECTION\">".$change["gedcom"]."</td>\n";
			$output .= "</tr>\n";
			if ($i==count($changes)-1) {
				$output .= "</table>\n";
				$output .= "</div><br />";
			}
		}
	}
	$output .= "</td></tr></table>";
	
	//-- Now for the global Action bar:
	$output2 = "<br /><table class=\"list_table\">";
	// Row 1 column 1: title "Accept all"
	$output2 .= "<tr><td class=\"list_label\">".$pgv_lang["accept_all"]."</td>";
	// Row 1 column 2: separator
	$output2 .= "<td class=\"list_label width10\">&nbsp;</td>";
	// Row 1 column 3: title "Undo all"
	$output2 .= "<td class=\"list_label\">".$pgv_lang["undo_all"]."</td></tr>";
	
	// Row 2 column 1: action "Accept all"
	$output2 .= "<tr><td class=\"list_value\">";
	foreach($changedgedcoms as $ged=>$value) {
		$output2 .= "<a href=\"edit_changes.php?action=acceptall&amp;ged=$ged\">$ged - ".$pgv_lang["accept_all"]."</a><br />\n";
	}
	$output2 .= "</td>";
	// Row 2 column 2: separator
	$output2 .= "<td class=\"list_value width10\">&nbsp;</td>";
	// Row 2 column 3: action "Undo all"
	$output2 .= "<td class=\"list_value\">";
	foreach($changedgedcoms as $ged=>$value) {
		$output2 .= "<a href=\"edit_changes.php?action=undoall&amp;ged=$ged\" onclick=\"return confirm('".$pgv_lang["undo_all_confirm"]."');\">$ged - ".$pgv_lang["undo_all"]."</a><br />\n";
	}
	$output2 .= "</td></tr>";
	$output2 .= "</table>";
	
	print "<center>";
	print $pgv_lang["accept_gedcom"]."<br />";
	print $output2;
	print $output;
	print $output2;
	print "</center>";
}
	

print "<br /><br />\n</center></div>\n";
print "<center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>\n";
print_simple_footer();
?>

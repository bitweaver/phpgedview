<?php
/**
 * Customizable FAQ page
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team
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
 * This Page Is Valid XHTML 1.0 Transitional! > 01 September 2005
 *
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id: faq.php,v 1.4 2008/07/07 18:01:13 lsces Exp $
 */
 
require("config.php");

loadLangFile("pgv_confighelp");

global $PGV_IMAGES, $faqs;

if (PGV_USER_GEDCOM_ADMIN) $canconfig = true;
else $canconfig = false;
if (!isset($action)) $action = "show";
if (!isset($adminedit) && $canconfig) $adminedit = true;
else if (!isset($adminedit)) $adminedit = false;

// -- print html header information
$pgv_lang["faq_page"] = "Frequently Asked Questions";
print_header($pgv_lang["faq_page"]);

// NOTE: Commit the faq data to the DB
if ($action=="commit") {
	if (empty($whichGEDCOM)) $whichGEDCOM = $GEDCOM;
	if (empty($oldGEDCOM)) $oldGEDCOM = $whichGEDCOM;
	if (empty($order)) $order = 0;
	if ($type == "update") {
		$faqs = get_faq_data();
		if (isset($faqs[$order]) && $order!=$oldOrder) {
			// New position number is already in use: find next higher one that isn't used
			while (true) {
				$order++;
				if (!isset($faqs[$order])) break;
				if ($order==$oldOrder) break;
			}
		}
		$header = str_replace(array('&lt;', '&gt;'), array('<', '>'), $header);
		$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".$order."', b_username='".$whichGEDCOM."', b_config='".$DBCONN->escapeSimple(serialize($header))."' WHERE b_id='".$pidh."' and b_username='".$oldGEDCOM."' and b_location='header'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		$body = str_replace(array('&lt;', '&gt;'), array('<', '>'), $body);
		$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".$order."', b_username='".$whichGEDCOM."', b_config='".$DBCONN->escapeSimple(serialize($body))."' WHERE b_id='".$pidb."' and b_username='".$oldGEDCOM."' and b_location='body'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;		
		AddToChangeLog("FAQ item has been edited.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $GEDCOM);
		$action = "show";
	}
	else if ($type == "delete") {
		$sql = "DELETE FROM ".$TBLPREFIX."blocks WHERE b_order='".$id."' AND b_name='faq' AND b_username='".$oldGEDCOM."'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		AddToChangeLog("FAQ item has been deleted.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $oldGEDCOM);
		$action = "show";
	}
	else if ($type == "add") {
		$faqs = get_faq_data();
		if (isset($faqs[$order])) {
			// New position number is already in use: find next higher one that isn't used
			while (true) {
				$order++;
				if (!isset($faqs[$order])) break;
			}
		}
		$newid = get_next_id("blocks", "b_id");
		$header = str_replace(array('&lt;', '&gt;'), array('<', '>'), $header);
		$sql = "INSERT INTO ".$TBLPREFIX."blocks VALUES ($newid, '".$whichGEDCOM."', 'header', '$order', 'faq', '".$DBCONN->escapeSimple(serialize($header))."')";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		$body = str_replace(array('&lt;', '&gt;'), array('<', '>'), $body);
		$sql = "INSERT INTO ".$TBLPREFIX."blocks VALUES (".($newid+1).", '".$whichGEDCOM."', 'body', '".$order."', 'faq', '".$DBCONN->escapeSimple(serialize($body))."')";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		AddToChangeLog("FAQ item has been added.<br />Header ID: ".$newid.".<br />Body ID: ".($newid+1), $whichGEDCOM);
		$action = "show";
	}
	else if ($type == "moveup") {
		$faqs = get_faq_data();
		if (isset($faqs[$id-1])) {
			$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id)."' WHERE b_id='".$faqs[$id-1]["header"]["pid"]."' and b_location='header'";;
			$tempsql = dbquery($sql);
			$res =& $tempsql;
			$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id)."' WHERE b_id='".$faqs[$id-1]["body"]["pid"]."' and b_location='body'";
			$tempsql = dbquery($sql);
			$res =& $tempsql;
		}
		$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id-1)."' WHERE b_id='".$pidh."' and b_location='header'";;
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id-1)."' WHERE b_id='".$pidb."' and b_location='body'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;		
		AddToChangeLog("FAQ item has been moved up.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $oldGEDCOM);
		$action = "show";
	}
	else if ($type == "movedown") {
		$faqs = get_faq_data();
		if (isset($faqs[$id+1])) {
			$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id)."' WHERE b_id='".$faqs[$id+1]["header"]["pid"]."' and b_location='header'";;
			$tempsql = dbquery($sql);
			$res =& $tempsql;
			$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id)."' WHERE b_id='".$faqs[$id+1]["body"]["pid"]."' and b_location='body'";
			$tempsql = dbquery($sql);
			$res =& $tempsql;
		}
		
		$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id+1)."' WHERE b_id='".$pidh."' and b_location='header'";;
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		$sql = "UPDATE ".$TBLPREFIX."blocks SET b_order='".($id+1)."' WHERE b_id='".$pidb."' and b_location='body'";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
		AddToChangeLog("FAQ item has been moved down.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $GEDCOM);
		$action = "show";
	}	
	$action = "show";
}

if ($action=="add") {
	$i=1;
	print "<form name=\"addfaq\" method=\"post\" action=\"faq.php\">";
	print "<input type=\"hidden\" name=\"action\" value=\"commit\" />";
	print "<input type=\"hidden\" name=\"type\" value=\"add\" />";
	print "<input type=\"hidden\" name=\"oldGEDCOM\" value=\"\" />";
	print "<input type=\"hidden\" name=\"oldOrder\" value=\"\" />";
	print "<table class=\"center list_table $TEXT_DIRECTION\">";
	print "<tr><td class=\"topbottombar\" colspan=\"2\">";
	print_help_link("add_faq_item_help","qm","add_faq_item");
	print $pgv_lang["add_faq_item"]."</td></tr>";
	print "<tr><td class=\"descriptionbox\" colspan=\"2\">";
	print_help_link("add_faq_header_help","qm","add_faq_header");
	print $pgv_lang["add_faq_header"]."</td></tr>";
	print "<tr><td class=\"optionbox\" colspan=\"2\"><input type=\"text\" name=\"header\" size=\"90\" tabindex=\"".$i++."\" /></td></tr>";
	print "<tr><td class=\"descriptionbox\" colspan=\"2\">";
	print_help_link("add_faq_body_help","qm","add_faq_body");
	print $pgv_lang["add_faq_body"]."</td></tr>";
	print "<tr><td class=\"optionbox\" colspan=\"2\"><textarea name=\"body\" rows=\"10\" cols=\"90\" tabindex=\"".$i++."\"></textarea></td></tr>";
	print "<tr><td class=\"descriptionbox\">";
	print_help_link("add_faq_order_help","qm","add_faq_order");
	print $pgv_lang["add_faq_order"]."</td><td class=\"descriptionbox\">";
	print_help_link("add_faq_visibility_help","qm","add_faq_order");
	print $pgv_lang["add_faq_visibility"]."</td></tr>";
	print "<tr><td class=\"optionbox\"><input type=\"text\" name=\"order\" size=\"3\" tabindex=\"".$i++."\" /></td>";
	print "<td class=\"optionbox\">";
		print "<select name=\"whichGEDCOM\" tabindex=\"".$i++."\" />";
			print "<option value=\"*all*\">".$pgv_lang["all"]."</option>";
			print "<option value=\"".$GEDCOM."\" selected=\"selected\">".$GEDCOM."</option";
		print "</select>";
	print "</td></tr>";
	print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["save"]."\" tabindex=\"".$i++."\" />";
	print "&nbsp;<input type=\"button\" value=\"".$pgv_lang["cancel"]."\" onclick=window.location=\"faq.php\"; tabindex=\"".$i++."\" /></td></tr>";
	print "</table>";
	print "</form>";
}

if ($action == "edit") {
	if (!isset($id)) {
		$error = true;
		$error_message =  $pgv_lang["no_id"];
		$action = "show";
	}
	else {
		$faqs = get_faq_data($id);
		
		$i=1;
		print "<form name=\"editfaq\" method=\"post\" action=\"faq.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"commit\" />";
		print "<input type=\"hidden\" name=\"type\" value=\"update\" />";
		print "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";
		print "<table class=\"center list_table $TEXT_DIRECTION\">";
		print "<tr><td class=\"topbottombar\" colspan=\"2\">";
		print_help_link("edit_faq_item_help","qm","edit_faq_item");
		print $pgv_lang["edit_faq_item"]."</td></tr>";
		foreach ($faqs as $id => $data) {
			print "<input type=\"hidden\" name=\"pidh\" value=\"".$data["header"]["pid"]."\" />";
			print "<input type=\"hidden\" name=\"pidb\" value=\"".$data["body"]["pid"]."\" />";
			print "<input type=\"hidden\" name=\"oldGEDCOM\" value=\"".$data["header"]["gedcom"]."\" />";
			print "<input type=\"hidden\" name=\"oldOrder\" value=\"".$id."\" />";
			$header = str_replace(array('&', '<', '>',), array('&amp;', '&lt;', '&gt;'), stripslashes($data["header"]["text"]));
			print "<tr><td class=\"descriptionbox\" colspan=\"2\">";
			print_help_link("add_faq_header_help","qm","add_faq_header");
			print $pgv_lang["add_faq_header"]."</td></tr>";
			print "<tr><td class=\"optionbox\" colspan=\"2\"><input type=\"text\" name=\"header\" size=\"90\" tabindex=\"".$i++."\" value=\"".$header."\" /></td></tr>";
			print "<tr><td class=\"descriptionbox\" colspan=\"2\">";
			print_help_link("add_faq_body_help","qm","add_faq_body");
			print $pgv_lang["add_faq_body"]."</td></tr>";
			$body = str_replace(array('&', '<', '>',), array('&amp;', '&lt;', '&gt;'), stripslashes($data["body"]["text"]));
			print "<tr><td class=\"optionbox\" colspan=\"2\"><textarea name=\"body\" rows=\"10\" cols=\"90\" tabindex=\"".$i++."\">".$body."</textarea></td></tr>";
			print "<tr><td class=\"descriptionbox\">";
			print_help_link("add_faq_order_help","qm","add_faq_order");
			print $pgv_lang["add_faq_order"]."</td><td class=\"descriptionbox\">";
			print_help_link("add_faq_visibility_help","qm","add_faq_order");
			print $pgv_lang["add_faq_visibility"]."</td></tr>";
			print "<tr><td class=\"optionbox\"><input type=\"text\" name=\"order\" size=\"3\" tabindex=\"".$i++."\" value=\"".$id."\" /></td>";
			print "<td class=\"optionbox\">";
				print "<select name=\"whichGEDCOM\" tabindex=\"".$i++."\" />";
					print "<option value=\"*all*\"";if ($data["header"]["gedcom"]=="*all*") print " selected=\"selected\"";print ">".$pgv_lang["all"]."</option>";
					print "<option value=\"".$GEDCOM."\"";if ($data["header"]["gedcom"]==$GEDCOM) print " selected=\"selected\"";print ">".$GEDCOM."</option";
				print "</select>";
			print "</td></tr>";
		}
		print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["save"]."\" tabindex=\"".$i++."\" />";
		print "&nbsp;<input type=\"button\" value=\"".$pgv_lang["cancel"]."\" onclick=window.location=\"faq.php\"; tabindex=\"".$i++."\" /></td></tr>";
		print "</table>";
		print "</form>";
	}
}

if ($action == "show") {
	loadLangFile("pgv_faqlib");	// Load FAQ library from language files
	
	$faqs = get_faq_data();
	print "<table class=\"list_table width100\">";
	if (count($faqs) == 0 && $canconfig) {
		print "<tr><td class=\"width20 list_label\">";
		print_help_link("add_faq_item_help","qm","add_faq_item");
		print "<a href=\"faq.php?action=add\">".$pgv_lang["add_faq_item"]."</a>";
		print "</td></tr>";
	}
	else if (count($faqs) == 0 && !$canconfig) print "<tr><td class=\"error center\">".$pgv_lang["no_faq_items"]."</td></tr>";
	else {
		// NOTE: Add a preview link
		if ($canconfig) {
			print "<tr>";
			if ($adminedit) {
				print "<td class=\"descriptionbox center\" colspan=\"2\">";
				print_help_link("add_faq_item_help","qm","add_faq_item");
				print "<a href=\"faq.php?action=add\">".$pgv_lang["add"]."</a></td>";
			}
			print "<td class=\"descriptionbox center\" colspan=\"2\">";
			
			if ($adminedit) {
				print_help_link("preview_faq_item_help","qm","preview_faq_item");
				print "<a href=\"faq.php?adminedit=0\">".$pgv_lang["preview"]."</a>";
			} else {
				print_help_link("restore_faq_edits_help","qm","restore_faq_edits");
				print "<a href=\"faq.php?adminedit=1\">".$pgv_lang["edit"]."</a>";
			}
			print "</td>";
			
			if ($adminedit) {
				if (isset($error)) print "<td class=\"topbottombar red\">".$error_message."</td>";
				else print "<td class=\"topbottombar\">&nbsp;</td>";
			}
			print "</tr>";
		}
		
		foreach($faqs as $id => $data) {
			if ($data["header"] && $data["body"]) {
				print "<tr>";
				// NOTE: Print the position of the current item
				if ($canconfig && $adminedit) {
					print "<td class=\"descriptionbox width20 $TEXT_DIRECTION\" colspan=\"4\">";
					print $pgv_lang["position_item"].": ".$id.", ";
					if ($data["header"]["gedcom"]=="*all*") print $pgv_lang["all"];
					else print PrintReady($data["header"]["gedcom"]);
					print "</td>";
				}
				// NOTE: Print the header of the current item
				$header = str_replace(array('&lt;', '&gt;'), array('<', '>'), stripslashes(print_text($data["header"]["text"], 0, 2)));
				print "<td class=\"list_label wrap\">".$header."</td></tr>";
				$body = str_replace(array('&lt;', '&gt;'), array('<', '>'), stripslashes(print_text($data["body"]["text"], 0, 2)));
				print "<tr>";
				// NOTE: Print the edit options of the current item
				if ($canconfig && $adminedit) {
					print "<td class=\"optionbox center\">";
					print_help_link("moveup_faq_item_help","qm","moveup_faq_item");
					print "<a href=\"faq.php?action=commit&amp;type=moveup&amp;id=".$id."&amp;pidh=".$data["header"]["pid"]."&amp;pidb=".$data["body"]["pid"]."\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["uarrow"]["other"]."\" border=\"0\" alt=\"\" /></a>\n</td>";
					print "\n<td class=\"optionbox center\">";
					print_help_link("movedown_faq_item_help","qm","movedown_faq_item");
					print "<a href=\"faq.php?action=commit&amp;type=movedown&amp;id=".$id."&amp;pidh=".$data["header"]["pid"]."&amp;pidb=".$data["body"]["pid"]."\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow"]["other"]."\" border=\"0\" alt=\"\" /></a>";
					print "\n</td>\n<td class=\"optionbox center\">";					
					print_help_link("edit_faq_item_help","qm","edit_faq_item");
					print "<a href=\"faq.php?action=edit&amp;id=".$id."\">".$pgv_lang["edit"]."</a>";
					print "\n</td><td class=\"optionbox center\">";
					print_help_link("delete_faq_item_help","qm","delete_faq_item");
					print "<a href=\"faq.php?action=commit&amp;type=delete&amp;id=".$id."&amp;pidh=".$data["header"]["pid"]."&amp;pidb=".$data["body"]["pid"]."&amp;oldGEDCOM=".$data["header"]["gedcom"]."\" onclick=\"return confirm('".$pgv_lang["confirm_faq_delete"]."');\">".$pgv_lang["delete"]."</a>\n";
					print "</td>";
				}
				// NOTE: Print the body text of the current item
				print "<td class=\"list_value wrap\">".nl2br($body)."</td></tr>";				
			}
		}
	}
	print "</table>";
}
if ($action != "show") {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
		document.<?php print $action;?>faq.header.focus();
	//-->
	</script>
	<?php
}
print_footer();
?>

<?php
/**
 * Customizable FAQ page
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 */

require 'config.php';

loadLangFile("pgv_confighelp");

global $PGV_IMAGES, $faqs;

// -- print html header information
$pgv_lang["faq_page"] = "Frequently Asked Questions";
print_header($pgv_lang["faq_page"]);

// -- Get all of the _POST variables we're interested in
$action     = safe_REQUEST($_REQUEST, 'action',      PGV_REGEX_UNSAFE, 'show');
$adminedit  = safe_REQUEST($_REQUEST, 'adminedit',   PGV_REGEX_UNSAFE, PGV_USER_GEDCOM_ADMIN);
$type       = safe_REQUEST($_REQUEST, 'type',        PGV_REGEX_UNSAFE);
$oldGEDCOM  = safe_REQUEST($_REQUEST, 'oldGEDCOM',   PGV_REGEX_UNSAFE);
$whichGEDCOM= safe_REQUEST($_REQUEST, 'whichGEDCOM', PGV_REGEX_UNSAFE);
$oldOrder   = safe_REQUEST($_REQUEST, 'oldOrder',    PGV_REGEX_UNSAFE);
$order      = safe_REQUEST($_REQUEST, 'order',       PGV_REGEX_UNSAFE);
$header     = safe_REQUEST($_POST,    'header',      PGV_REGEX_UNSAFE);
$body       = safe_REQUEST($_POST,    'body',        PGV_REGEX_UNSAFE);
$pidh       = safe_REQUEST($_REQUEST, 'pidh',        PGV_REGEX_UNSAFE);
$pidb       = safe_REQUEST($_REQUEST, 'pidb',        PGV_REGEX_UNSAFE);
$id         = safe_REQUEST($_REQUEST, 'id',          PGV_REGEX_UNSAFE);

// NOTE: Commit the faq data to the DB
if ($action=="commit") {
	if (empty($whichGEDCOM)) $whichGEDCOM = $GEDCOM;
	if (empty($oldGEDCOM)) $oldGEDCOM = $whichGEDCOM;
	if (empty($order)) $order = 0;

	switch ($type) {
	case 'update':
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
		PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=?, b_username=?, b_config=? WHERE b_id=? and b_username=? and b_location=?")
			->execute(array($order, $whichGEDCOM, serialize($header), $pidh, $oldGEDCOM, 'header'));

		$body = str_replace(array('&lt;', '&gt;'), array('<', '>'), $body);
		PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=?, b_username=?, b_config=? WHERE b_id=? and b_username=? and b_location=?")
			->execute(array($order, $whichGEDCOM, serialize($body), $pidb, $oldGEDCOM, 'body'));

		AddToChangeLog("FAQ item has been edited.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $GEDCOM);
		break;

	case 'delete':
		PGV_DB::prepare("DELETE FROM {$TBLPREFIX}blocks WHERE b_order=? AND b_name=? AND b_username=?")
			->execute(array($id, 'faq', $oldGEDCOM));

		AddToChangeLog("FAQ item has been deleted.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $oldGEDCOM);
		break;

	case 'add':
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
		PGV_DB::prepare("INSERT INTO {$TBLPREFIX}blocks (b_id, b_username, b_location, b_order, b_name, b_config) VALUES (?, ?, ?, ?, ?, ?)")
			->execute(array($newid, $whichGEDCOM, 'header', $order, 'faq', serialize($header)));

		$body = str_replace(array('&lt;', '&gt;'), array('<', '>'), $body);
		PGV_DB::prepare("INSERT INTO {$TBLPREFIX}blocks (b_id, b_username, b_location, b_order, b_name, b_config) VALUES (?, ?, ?, ?, ?, ?)")
			->execute(array($newid+1, $whichGEDCOM, 'body', $order, 'faq', serialize($body)));

		AddToChangeLog("FAQ item has been added.<br />Header ID: ".$newid.".<br />Body ID: ".($newid+1), $whichGEDCOM);
		break;

	case 'moveup':
		$faqs = get_faq_data();
		if (isset($faqs[$id-1])) {
			PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
				->execute(array($id, $faqs[$id-1]["header"]["pid"], 'header'));

			PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
				->execute(array($id, $faqs[$id-1]["body"]["pid"], 'body'));
		}
		PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
			->execute(array($id-1, $pidh, 'header'));

		PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
			->execute(array($id-1, $pidb, 'body'));

		AddToChangeLog("FAQ item has been moved up.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $oldGEDCOM);
		break;

	case 'movedown':
		$faqs = get_faq_data();
		if (isset($faqs[$id+1])) {
			PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
				->execute(array($id, $faqs[$id+1]["header"]["pid"], 'header'));

			PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
				->execute(array($id, $faqs[$id+1]["body"]["pid"], 'body'));
		}
		PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
			->execute(array($id+1, $pidh, 'header'));

		PGV_DB::prepare("UPDATE {$TBLPREFIX}blocks SET b_order=? WHERE b_id=? and b_location=?")
			->execute(array($id+1, $pidb, 'body'));

		AddToChangeLog("FAQ item has been moved down.<br />Header ID: ".$pidh.".<br />Body ID: ".$pidb, $GEDCOM);
		break;
	}

	$action = "show";
}

if ($action=="add") {
	$i=1;
	echo '<form name="addfaq" method="post" action="faq.php">';
	echo '<input type="hidden" name="action" value="commit" />';
	echo '<input type="hidden" name="type" value="add" />';
	echo '<input type="hidden" name="oldGEDCOM" value="" />';
	echo '<input type="hidden" name="oldOrder" value="" />';
	echo '<table class="center list_table ', $TEXT_DIRECTION, '">';
	echo '<tr><td class="topbottombar" colspan="2">';
	print_help_link("add_faq_item_help","qm","add_faq_item");
	echo $pgv_lang["add_faq_item"], '</td></tr>';
	echo '<tr><td class="descriptionbox" colspan="2">';
	print_help_link("add_faq_header_help","qm","add_faq_header");
	echo $pgv_lang["add_faq_header"], '</td></tr>';
	echo '<tr><td class="optionbox" colspan="2"><input type="text" name="header" size="90" tabindex="', $i++, '" /></td></tr>';
	echo '<tr><td class="descriptionbox" colspan="2">';
	print_help_link("add_faq_body_help","qm","add_faq_body");
	echo $pgv_lang["add_faq_body"], '</td></tr>';
	echo '<tr><td class="optionbox" colspan="2"><textarea name="body" rows="10" cols="90" tabindex="', $i++, '"></textarea></td></tr>';
	echo '<tr><td class="descriptionbox">';
	print_help_link("add_faq_order_help","qm","add_faq_order");
	echo $pgv_lang["add_faq_order"], '</td><td class="descriptionbox">';
	print_help_link("add_faq_visibility_help","qm","add_faq_order");
	echo $pgv_lang["add_faq_visibility"], '</td></tr>';
	echo '<tr><td class="optionbox"><input type="text" name="order" size="3" tabindex="', $i++, '" /></td>';
	echo '<td class="optionbox">';
		echo '<select name="whichGEDCOM" tabindex="', $i++, '" />';
			echo '<option value="*all*">', $pgv_lang["all"], '</option>';
			echo '<option value="', $GEDCOM, '" selected="selected">', $GEDCOM, '</option';
		echo '</select>';
	echo '</td></tr>';
	echo '<tr><td class="topbottombar" colspan="2"><input type="submit" value="', $pgv_lang["save"], '" tabindex="', $i++, '" />';
	echo '&nbsp;<input type="button" value="', $pgv_lang["cancel"], '" onclick="window.location=\'faq.php\'"; tabindex="', $i++, '" /></td></tr>';
	echo '</table>';
	echo '</form>';
}

if ($action == "edit") {
	if ($id == NULL) {
		$error = true;
		$error_message =  $pgv_lang["no_id"];
		$action = "show";
	} else {
		$faqs = get_faq_data($id);

		$i=1;
		echo '<form name="editfaq" method="post" action="faq.php">';
		echo '<input type="hidden" name="action" value="commit" />';
		echo '<input type="hidden" name="type" value="update" />';
		echo '<input type="hidden" name="id" value="', $id, '" />';
		echo '<table class="center list_table ', $TEXT_DIRECTION, '">';
		echo '<tr><td class="topbottombar" colspan="2">';
		print_help_link("edit_faq_item_help","qm","edit_faq_item");
		echo $pgv_lang["edit_faq_item"], '</td></tr>';
		foreach ($faqs as $id => $data) {
			echo '<input type="hidden" name="pidh" value="', htmlspecialchars($data["header"]["pid"]), '" />';
			echo '<input type="hidden" name="pidb" value="', htmlspecialchars($data["body"]["pid"]), '" />';
			echo '<input type="hidden" name="oldGEDCOM" value="', htmlspecialchars($data["header"]["gedcom"]), '" />';
			echo '<input type="hidden" name="oldOrder" value="', htmlspecialchars($id), '" />';
			echo '<tr><td class="descriptionbox" colspan="2">';
			print_help_link("add_faq_header_help","qm","add_faq_header");
			echo $pgv_lang["add_faq_header"], '</td></tr>';
			echo '<tr><td class="optionbox" colspan="2"><input type="text" name="header" size="90" tabindex="', $i++, '" value="', htmlspecialchars($data["header"]["text"]), '" /></td></tr>';
			echo '<tr><td class="descriptionbox" colspan="2">';
			print_help_link("add_faq_body_help","qm","add_faq_body");
			echo $pgv_lang["add_faq_body"], '</td></tr>';
			echo '<tr><td class="optionbox" colspan="2"><textarea name="body" rows="10" cols="90" tabindex="', $i++, '">', htmlspecialchars($data["body"]["text"]), '</textarea></td></tr>';
			echo '<tr><td class="descriptionbox">';
			print_help_link("add_faq_order_help","qm","add_faq_order");
			echo $pgv_lang["add_faq_order"], '</td><td class="descriptionbox">';
			print_help_link("add_faq_visibility_help","qm","add_faq_order");
			echo $pgv_lang["add_faq_visibility"], '</td></tr>';
			echo '<tr><td class="optionbox"><input type="text" name="order" size="3" tabindex="', $i++, '" value="', $id, '" /></td>';
			echo '<td class="optionbox">';
				echo '<select name="whichGEDCOM" tabindex="', $i++, '" />';
					echo '<option value="*all*"';if ($data["header"]["gedcom"]=="*all*") echo ' selected="selected"';echo '>', $pgv_lang["all"], '</option>';
					echo '<option value="', $GEDCOM, '"';
					if ($data["header"]["gedcom"]==$GEDCOM) echo ' selected="selected"';
					echo '>', $GEDCOM, '</option';
				echo '</select>';
			echo '</td></tr>';
		}
		echo '<tr><td class="topbottombar" colspan="2"><input type="submit" value="', $pgv_lang["save"], '" tabindex="', $i++, '" />';
		echo '&nbsp;<input type="button" value="', $pgv_lang["cancel"], '" onclick=window.location="faq.php"; tabindex="', $i++, '" /></td></tr>';
		echo '</table>';
		echo '</form>';
	}
}

if ($action == "show") {
	loadLangFile("pgv_faqlib");

	$faqs = get_faq_data();
	echo '<table class="list_table width100">';
	if (count($faqs) == 0) {
		if (PGV_USER_GEDCOM_ADMIN) {
			echo '<tr><td class="width20 list_label">';
			print_help_link("add_faq_item_help","qm","add_faq_item");
			echo '<a href="faq.php?action=add">', $pgv_lang["add_faq_item"], '</a>';
			echo '</td></tr>';
		} else {
			echo '<tr><td class="error center">', $pgv_lang["no_faq_items"], '</td></tr>';
		}
	} else {
		// NOTE: Add a preview link
		if (PGV_USER_GEDCOM_ADMIN) {
			echo '<tr>';
			if ($adminedit) {
				echo '<td class="descriptionbox center" colspan="2">';
				print_help_link("add_faq_item_help","qm","add_faq_item");
				echo '<a href="faq.php?action=add">', $pgv_lang["add"], '</a></td>';
			}
			echo '<td class="descriptionbox center" colspan="2">';

			if ($adminedit) {
				print_help_link("preview_faq_item_help","qm","preview_faq_item");
				echo '<a href="faq.php?adminedit=0">', $pgv_lang["preview"], '</a>';
			} else {
				print_help_link("restore_faq_edits_help","qm","restore_faq_edits");
				echo '<a href="faq.php?adminedit=1">', $pgv_lang["edit"], '</a>';
			}
			echo '</td>';

			if ($adminedit) {
				if (isset($error)) echo '<td class="topbottombar red">', $error_message, '</td>';
				else echo '<td class="topbottombar">&nbsp;</td>';
			}
			echo '</tr>';
		}

		foreach($faqs as $id => $data) {
			if ($data["header"] && $data["body"]) {
				echo '<tr>';
				// NOTE: Print the position of the current item
				if ($adminedit) {
					echo '<td class="descriptionbox width20 $TEXT_DIRECTION" colspan="4">';
					echo $pgv_lang["position_item"], ': ', $id, ', ';
					if ($data["header"]["gedcom"]=="*all*") echo $pgv_lang["all"];
					else echo PrintReady($data["header"]["gedcom"]);
					echo '</td>';
				}
				// NOTE: Print the header of the current item
				$header = str_replace(array('&lt;', '&gt;'), array('<', '>'), stripslashes(print_text($data["header"]["text"], 0, 2)));
				echo '<td class="list_label wrap">', $header, '</td></tr>';
				$body = str_replace(array('&lt;', '&gt;'), array('<', '>'), stripslashes(print_text($data["body"]["text"], 0, 2)));
				echo '<tr>';
				// NOTE: Print the edit options of the current item
				if (PGV_USER_GEDCOM_ADMIN && $adminedit) {
					echo '<td class="optionbox center">';
					print_help_link("moveup_faq_item_help","qm","moveup_faq_item");
					echo '<a href="', encode_url('faq.php?action=commit&type=moveup&id='.$id.'&pidh='.$data["header"]["pid"].'&pidb='.$data["body"]["pid"]), '"><img src="', $PGV_IMAGE_DIR, '/', $PGV_IMAGES["uarrow"]["other"], '" border="0" alt="" /></a></td>';
					echo '<td class="optionbox center">';
					print_help_link("movedown_faq_item_help","qm","movedown_faq_item");
					echo '<a href="', encode_url('faq.php?action=commit&type=movedown&id='.$id.'&pidh='.$data["header"]["pid"].'&pidb='.$data["body"]["pid"]), '"><img src="', $PGV_IMAGE_DIR, '/', $PGV_IMAGES["darrow"]["other"], '" border="0" alt="" /></a>';
					echo '</td><td class="optionbox center">';
					print_help_link("edit_faq_item_help","qm","edit_faq_item");
					echo '<a href="', encode_url('faq.php?action=edit&id='.$id), '">', $pgv_lang["edit"], '</a>';
					echo '</td><td class="optionbox center">';
					print_help_link("delete_faq_item_help","qm","delete_faq_item");
					echo '<a href="', encode_url('faq.php?action=commit&type=delete&id='.$id.'&pidh='.$data["header"]["pid"].'&amp;pidb='.$data["body"]["pid"].'&oldGEDCOM='.$data["header"]["gedcom"]), '" onclick="return confirm(\'', $pgv_lang["confirm_faq_delete"], '\');">', $pgv_lang["delete"], '</a>';
					echo '</td>';
				}
				// NOTE: Print the body text of the current item
				echo '<td class="list_value_wrap">', nl2br($body), '</td></tr>';
			}
		}
	}
	echo '</table>';
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

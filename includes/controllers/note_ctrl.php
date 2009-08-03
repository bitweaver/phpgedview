<?php
/**
* Controller for the shared note page view
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2009 PGV Development Team.  All rights reserved.
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
* @subpackage Charts
* @version $Id: note_ctrl.php,v 1.2 2009/08/03 20:10:43 lsces Exp $
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_NOTE_CTRL_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print_facts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_note.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_menu.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_import.php');

$nonfacts = array();
/**
* Main controller class for the shared note page.
*/
class NoteControllerRoot extends BaseController {
	var $nid;
	/* @var Note */
	var $note = null;
	var $uname = "";
	var $diffnote = null;
	var $accept_success = false;
	var $canedit = false;

	/**
	* constructor
	*/
	function NoteRootController() {
		parent::BaseController();
	}

	/**
	* initialize the controller
	*/
	function init() {
		global $pgv_lang, $CONTACT_EMAIL, $GEDCOM, $pgv_changes;

		$this->nid = safe_GET_xref('nid');

		$noterec = find_other_record($this->nid);

		if (isset($pgv_changes[$this->nid."_".$GEDCOM])){
			$noterec = "0 @".$this->nid."@ NOTE\n";
		} else if (!$noterec) {
			return false;
		}

		$this->note = new Note($noterec);
		$this->note->ged_id=PGV_GED_ID; // This record is from a file

		if (!$this->note->canDisplayDetails()) {
			print_header($pgv_lang["private"]." ".$pgv_lang["shared_note_info"]);
			print_privacy_error($CONTACT_EMAIL);
			print_footer();
			exit;
		}

		$this->uname = PGV_USER_NAME;

		//-- perform the desired action
		switch($this->action) {
			case "addfav":
				$this->addFavorite();
				break;
			case "accept":
				$this->acceptChanges();
				break;
			case "undo":
				$this->note->undoChange();
				break;
		}

		//-- check for the user
		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && PGV_USER_CAN_EDIT && isset($pgv_changes[$this->nid."_".$GEDCOM])) {
			$newrec = find_updated_record($this->nid);
			$this->diffnote = new Note($newrec);
			$this->diffnote->setChanged(true);
			$noterec = $newrec;
		}

		if ($this->note->canDisplayDetails()) {
			$this->canedit = PGV_USER_CAN_EDIT;
		}

		if ($this->show_changes && $this->canedit) {
			$this->note->diffMerge($this->diffnote);
		}
	}

	/**
	* Add a new favorite for the action user
	*/
	function addFavorite() {
		global $GEDCOM;
		if (empty($this->uname)) return;
		if (!empty($_REQUEST["gid"])) {
			$gid = strtoupper($_REQUEST["gid"]);
			$indirec = find_other_record($gid);
			if ($indirec) {
				$favorite = array();
				$favorite["username"] = $this->uname;
				$favorite["gid"] = $gid;
				$favorite["type"] = "NOTE";
				$favorite["file"] = $GEDCOM;
				$favorite["url"] = "";
				$favorite["note"] = "";
				$favorite["title"] = "";
				addFavorite($favorite);
			}
		}
	}
	/**
	* Accept any edit changes into the database
	* Also update the indirec we will use to generate the page
	*/
	function acceptChanges() {
		global $GEDCOM;

		if (!PGV_USER_CAN_ACCEPT) return;
		if (accept_changes($this->nid."_".$GEDCOM)) {
			$this->show_changes=false;
			$this->accept_success=true;
			$indirec = find_other_record($this->nid);
			//-- check if we just deleted the record and redirect to index
			if (empty($indirec)) {
				header("Location: index.php?ctype=gedcom");
				exit;
			}
			$this->note = new Note($indirec);
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		global $pgv_lang;
		if ($this->note) {
			return $this->note->getFullName()." - ".$this->nid." - ".$pgv_lang["shared_note_info"];
		}
		else {
			return $pgv_lang["unable_to_find_record"];
		}
	}
	/**
	* check if use can edit this person
	* @return boolean
	*/
	function userCanEdit() {
		return $this->canedit;
	}

	/**
	* get edit menut
	* @return Menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $pgv_changes;
		global $SHOW_GEDCOM_RECORD;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		if (!$this->userCanEdit()) {
			$tempvar = false;
			return $tempvar;
		}

		// edit shared note menu
		$menu = new Menu($pgv_lang['edit_shared_note']);
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN)
			$menu->addOnclick('return edit_note(\''.$this->nid.'\');');
		if (!empty($PGV_IMAGES["notes"]["small"]))
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// edit shared note / edit_raw
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN) {
			$submenu = new Menu($pgv_lang['edit_raw']);
			$submenu->addOnclick("return edit_raw('".$this->nid."');");
			if (!empty($PGV_IMAGES["notes"]["small"]))
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		// edit shared note / delete_shared note
		$submenu = new Menu($pgv_lang['delete_shared_note']);
		$submenu->addOnclick("if (confirm('".$pgv_lang["confirm_delete_shared_note"]."')) return deletenote('".$this->nid."'); else return false;");
		if (!empty($PGV_IMAGES["notes"]["small"]))
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		if (isset($pgv_changes[$this->nid.'_'.$GEDCOM]))
		{
			// edit_note / separator
			$submenu = new Menu();
			$submenu->isSeparator();
			$menu->addSubmenu($submenu);

			// edit_note / show/hide changes
			if (!$this->show_changes)
			{
				$submenu = new Menu($pgv_lang['show_changes'], encode_url("note.php?nid={$this->nid}&show_changes=yes"));
				if (!empty($PGV_IMAGES["notes"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}
			else
			{
				$submenu = new Menu($pgv_lang['hide_changes'], encode_url("note.php?nid={$this->nid}&show_changes=no"));
				if (!empty($PGV_IMAGES["notes"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}

			if (PGV_USER_CAN_ACCEPT)
			{
				// edit_shared note / accept_all
				$submenu = new Menu($pgv_lang["undo_all"], encode_url("note.php?nid={$this->nid}&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				if (!empty($PGV_IMAGES["notes"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang['accept_all'], encode_url("note.php?nid={$this->nid}&action=accept"));
				if (!empty($PGV_IMAGES["notes"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['notes']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the other menu
	* @return Menu
	*/
	function &getOtherMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		if (!$this->note->canDisplayDetails() || (!$SHOW_GEDCOM_RECORD && $ENABLE_CLIPPINGS_CART < PGV_USER_ACCESS_LEVEL)) {
			$tempvar = false;
			return $tempvar;
		}

			// other menu
		$menu = new Menu($pgv_lang['other']);
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		if ($SHOW_GEDCOM_RECORD)
		{
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
			if ($this->show_changes && $this->userCanEdit())
			{
				$menu->addLink("javascript:show_gedcom_record('new');");
			}
			else
			{
				$menu->addLink("javascript:show_gedcom_record();");
			}
		}
		else
		{
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['clippings']['small']}");
			$menu->addLink(encode_url("clippings.php?action=add&id={$this->nid}&type=note"));
		}
		if ($SHOW_GEDCOM_RECORD)
		{
				// other / view_gedcom
				$submenu = new Menu($pgv_lang['view_gedcom']);
				if ($this->show_changes && $this->userCanEdit())
				{
					$submenu->addLink("javascript:show_gedcom_record('new');");
				}
				else
				{
					$submenu->addLink("javascript:show_gedcom_record();");
				}
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($ENABLE_CLIPPINGS_CART >= PGV_USER_ACCESS_LEVEL)
		{
				// other / add_to_cart
				$submenu = new Menu($pgv_lang['add_to_cart'], encode_url("clippings.php?action=add&id={$this->nid}&type=note"));
				if (!empty($PGV_IMAGES["clippings"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['clippings']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($this->note->canDisplayDetails() && !empty($this->uname))
		{
				// other / add_to_my_favorites
				$submenu = new Menu($pgv_lang['add_to_my_favorites'], encode_url("note.php?action=addfav&nid={$this->nid}&gid={$this->nid}"));
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		return $menu;
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/note_ctrl_user.php'))
{
	include_once 'includes/controllers/note_ctrl_user.php';
}
else
{
	class NoteController extends NoteControllerRoot
	{
	}
}

?>

<?php
/**
* Controller for the source page view
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
* @version $Id$
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_SOURCE_CTRL_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print_facts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_source.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_menu.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_import.php');

$nonfacts = array();
/**
* Main controller class for the source page.
*/
class SourceControllerRoot extends BaseController {
	var $sid;
	/* @var Source */
	var $source = null;
	var $uname = "";
	var $diffsource = null;
	var $accept_success = false;
	var $canedit = false;

	/**
	* constructor
	*/
	function SourceRootController() {
		parent::BaseController();
	}

	/**
	* initialize the controller
	*/
	function init() {
		global $pgv_lang, $CONTACT_EMAIL, $GEDCOM, $pgv_changes;

		$this->sid = safe_GET_xref('sid');

		$sourcerec = find_source_record($this->sid);

		if (isset($pgv_changes[$this->sid."_".$GEDCOM])){
			$sourcerec = "0 @".$this->sid."@ SOUR\n";
		} else if (!$sourcerec) {
			return false;
		}

		$this->source = new Source($sourcerec);
		$this->source->ged_id=PGV_GED_ID; // This record is from a file

		if (!$this->source->canDisplayDetails()) {
			print_header($pgv_lang["private"]." ".$pgv_lang["source_info"]);
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
				$this->source->undoChange();
				break;
		}

		//-- check for the user
		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && PGV_USER_CAN_EDIT && isset($pgv_changes[$this->sid."_".$GEDCOM])) {
			$newrec = find_updated_record($this->sid);
			$this->diffsource = new Source($newrec);
			$this->diffsource->setChanged(true);
			$sourcerec = $newrec;
		}

		if ($this->source->canDisplayDetails()) {
			$this->canedit = PGV_USER_CAN_EDIT;
		}

		if ($this->show_changes && $this->canedit) {
			$this->source->diffMerge($this->diffsource);
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
			$indirec = find_source_record($gid);
			if ($indirec) {
				$favorite = array();
				$favorite["username"] = $this->uname;
				$favorite["gid"] = $gid;
				$favorite["type"] = "SOUR";
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
		if (accept_changes($this->sid."_".$GEDCOM)) {
			$this->show_changes=false;
			$this->accept_success=true;
			$indirec = find_source_record($this->sid);
			//-- check if we just deleted the record and redirect to index
			if (empty($indirec)) {
				header("Location: index.php?ctype=gedcom");
				exit;
			}
			$this->source = new Source($indirec);
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		global $pgv_lang;
		if ($this->source) {
			return $this->source->getFullName()." - ".$this->sid." - ".$pgv_lang["source_info"];
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

		// edit source menu
		$menu = new Menu($pgv_lang['edit_source']);
		$menu->addOnclick('return edit_source(\''.$this->sid.'\');');
		if (!empty($PGV_IMAGES["edit_sour"]["small"]))
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// edit source / edit_source
		$submenu = new Menu($pgv_lang['edit_source']);
		$submenu->addOnclick('return edit_source(\''.$this->sid.'\');');
		if (!empty($PGV_IMAGES["edit_sour"]["small"]))
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		$menu->addSubmenu($submenu);

		// edit source / edit_raw
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN) {
			$submenu = new Menu($pgv_lang['edit_raw']);
			$submenu->addOnclick("return edit_raw('".$this->sid."');");
			if (!empty($PGV_IMAGES["edit_sour"]["small"]))
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		// edit source / delete_source
		$submenu = new Menu($pgv_lang['delete_source']);
		$submenu->addOnclick("if (confirm('".$pgv_lang["confirm_delete_source"]."')) return deletesource('".$this->sid."'); else return false;");
		if (!empty($PGV_IMAGES["edit_sour"]["small"]))
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		if (isset($pgv_changes[$this->sid.'_'.$GEDCOM]))
		{
			// edit_sour / separator
			$submenu = new Menu();
			$submenu->isSeparator();
			$menu->addSubmenu($submenu);

			// edit_sour / show/hide changes
			if (!$this->show_changes)
			{
				$submenu = new Menu($pgv_lang['show_changes'], encode_url("source.php?sid={$this->sid}&show_changes=yes"));
				if (!empty($PGV_IMAGES["edit_sour"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}
			else
			{
				$submenu = new Menu($pgv_lang['hide_changes'], encode_url("source.php?sid={$this->sid}&show_changes=no"));
				if (!empty($PGV_IMAGES["edit_sour"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}

			if (PGV_USER_CAN_ACCEPT)
			{
				// edit_source / accept_all
				$submenu = new Menu($pgv_lang["undo_all"], encode_url("source.php?sid={$this->sid}&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				if (!empty($PGV_IMAGES["edit_sour"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang['accept_all'], encode_url("source.php?sid={$this->sid}&action=accept"));
				if (!empty($PGV_IMAGES["edit_sour"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_sour']['small']}");
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

		if (!$this->source->canDisplayDetails() || (!$SHOW_GEDCOM_RECORD && $ENABLE_CLIPPINGS_CART < PGV_USER_ACCESS_LEVEL)) {
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
			$menu->addLink(encode_url("clippings.php?action=add&id={$this->sid}&type=sour"));
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
				$submenu = new Menu($pgv_lang['add_to_cart'], encode_url("clippings.php?action=add&id={$this->sid}&type=sour"));
				if (!empty($PGV_IMAGES["clippings"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['clippings']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($this->source->canDisplayDetails() && !empty($this->uname))
		{
				// other / add_to_my_favorites
				$submenu = new Menu($pgv_lang['add_to_my_favorites'], encode_url("source.php?action=addfav&sid={$this->sid}&gid={$this->sid}"));
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		return $menu;
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/source_ctrl_user.php'))
{
	include_once 'includes/controllers/source_ctrl_user.php';
}
else
{
	class SourceController extends SourceControllerRoot
	{
	}
}

?>

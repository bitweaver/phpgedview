<?php
/**
* Controller for the repository page view
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2008 PGV Development Team.  All rights reserved.
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

define('PGV_REPOSITORY_CTRL_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print_facts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_repository.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_menu.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_import.php');

$nonfacts = array();
/**
* Main controller class for the repository page.
*/
class RepositoryControllerRoot extends BaseController {
	var $rid;
	var $repository = null;
	var $uname = "";
	var $diffrepository = null;
	var $accept_success = false;
	var $canedit = false;

	/**
	* constructor
	*/
	function RepositoryRootController() {
		parent::BaseController();
	}

	/**
	* initialize the controller
	*/
	function init() {
		global $pgv_lang, $CONTACT_EMAIL, $GEDCOM, $pgv_changes;

		$this->rid = safe_GET_xref('rid');

		$repositoryrec = find_other_record($this->rid);

		if (isset($pgv_changes[$this->rid."_".$GEDCOM])){
			$repositoryrec = "0 @".$this->rid."@ REPO\n";
		} else if (!$repositoryrec) {
			return false;
		}

		$this->repository = new Repository($repositoryrec);
		$this->repository->ged_id=PGV_GED_ID; // This record is from a file

		if (!$this->repository->canDisplayDetails()) {
			print_header($pgv_lang["private"]." ".$pgv_lang["repo_info"]);
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
				$this->repository->undoChange();
				break;
		}

		//-- check for the user
		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && PGV_USER_CAN_EDIT && isset($pgv_changes[$this->rid."_".$GEDCOM])) {
			$newrec = find_updated_record($this->rid);
			$this->diffrepository = new Repository($newrec);
			$this->diffrepository->setChanged(true);
			$repositoryrec = $newrec;
		}

		if ($this->repository->canDisplayDetails()) {
			$this->canedit = PGV_USER_CAN_EDIT;
		}

		if ($this->show_changes && $this->canedit) {
			$this->repository->diffMerge($this->diffrepository);
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
				$favorite["type"] = "REPO";
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
		if (accept_changes($this->rid."_".$GEDCOM)) {
			$this->show_changes=false;
			$this->accept_success=true;
			$indirec = find_other_record($this->rid);
			//-- check if we just deleted the record and redirect to index
			if (empty($indirec)) {
				header("Location: index.php?ctype=gedcom");
				exit;
			}
			$this->repository = new Repository($indirec);
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		global $pgv_lang;
		if ($this->repository) {
			return $this->repository->getFullName()." - ".$this->rid." - ".$pgv_lang["repo_info"];
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

		// edit repository menu
		$menu = new Menu($pgv_lang['edit_repo']);
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN)
			$menu->addOnclick('return edit_raw(\''.$this->rid.'\');');
		if (!empty($PGV_IMAGES["edit_repo"]["small"]))
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// edit repository / edit_raw
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN) {
			$submenu = new Menu($pgv_lang['edit_raw']);
			$submenu->addOnclick("return edit_raw('".$this->rid."');");
			if (!empty($PGV_IMAGES["edit_repo"]["small"]))
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		// edit repository / delete_repository
		$submenu = new Menu($pgv_lang['delete_repo']);
		$submenu->addOnclick("if (confirm('".$pgv_lang["confirm_delete_repo"]."')) return deleterepository('".$this->rid."'); else return false;");
		if (!empty($PGV_IMAGES["edit_repo"]["small"]))
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		if (isset($pgv_changes[$this->rid.'_'.$GEDCOM]))
		{
			// edit_repo / separator
			$submenu = new Menu();
			$submenu->isSeparator();
			$menu->addSubmenu($submenu);

			// edit_repo / show/hide changes
			if (!$this->show_changes)
			{
				$submenu = new Menu($pgv_lang['show_changes'], encode_url("repo.php?rid={$this->rid}&show_changes=yes"));
				if (!empty($PGV_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}
			else
			{
				$submenu = new Menu($pgv_lang['hide_changes'], encode_url("repo.php?rid={$this->rid}&show_changes=no"));
				if (!empty($PGV_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}

			if (PGV_USER_CAN_ACCEPT)
			{
				// edit_repository / accept_all
				$submenu = new Menu($pgv_lang["undo_all"], encode_url("repo.php?rid={$this->rid}&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				if (!empty($PGV_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang['accept_all'], encode_url("repo.php?rid={$this->rid}&action=accept"));
				if (!empty($PGV_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_repo']['small']}");
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

		if (!$this->repository->canDisplayDetails() || (!$SHOW_GEDCOM_RECORD && $ENABLE_CLIPPINGS_CART < PGV_USER_ACCESS_LEVEL)) {
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
			$menu->addLink(encode_url("clippings.php?action=add&id={$this->rid}&type=repo"));
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
				$submenu = new Menu($pgv_lang['add_to_cart'], encode_url("clippings.php?action=add&id={$this->rid}&type=repo"));
				if (!empty($PGV_IMAGES["clippings"]["small"]))
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['clippings']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($this->repository->canDisplayDetails() && !empty($this->uname))
		{
				// other / add_to_my_favorites
				$submenu = new Menu($pgv_lang['add_to_my_favorites'], encode_url("repo.php?action=addfav&rid={$this->rid}&gid={$this->rid}"));
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		return $menu;
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/repository_ctrl_user.php'))
{
	include_once 'includes/controllers/repository_ctrl_user.php';
}
else
{
	class RepositoryController extends RepositoryControllerRoot
	{
	}
}

?>

<?php
/**
* Parses gedcom file and gives access to information about a family.
*
* You must supply a $famid value with the identifier for the family.
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
* @subpackage Controllers
* @version $Id$
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FAMILY_CTRL_PHP', '');

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
//require_once 'config.php';
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print_facts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_charts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_family.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_menu.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_import.php');

class FamilyRoot extends BaseController {
	var $user = null;
	var $showLivingHusb = true;
	var $showLivingWife = true;
	var $parents = '';
	var $display = false;
	var $accept_success = false;
	var $show_changes = true;
	var $famrec = '';
	var $link_relation = 0;
	var $title = '';
	var $famid = '';
	var $family = null;
	var $difffam = null;

	/**
	* constructor
	*/
	function FamilyRoot() {
		parent::BaseController();
	}

	function init() {
		global
			$Dbwidth,
			$bwidth,
			$pbwidth,
			$pbheight,
			$bheight,
			$GEDCOM,
			$pgv_lang,
			$CONTACT_EMAIL,
			$show_famlink,
			$pgv_changes
		;
		$bwidth = $Dbwidth;
		$pbwidth = $bwidth + 12;
		$pbheight = $bheight + 14;

		$show_famlink = $this->view!='preview';

		$this->famid       =safe_GET_xref('famid');

		$this->family      =Family::getInstance($this->famid);

		if (empty($this->famrec)) {
			$ct = preg_match("/(\w+):(.+)/", $this->famid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				include_once('includes/classes/class_serviceclient.php');
				$service = ServiceClient::getInstance($servid);
				if (!is_null($service)) {
					$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$this->famid."@ FAM\n1 RFN ".$this->famid, false);
					$this->famrec = $newrec;
				}
			}
		}

		//-- if no record was found create a default empty one
		if (empty($this->family)) {
			$this->famrec = "0 @".$this->famid."@ FAM\n";
			$this->family = new Family($this->famrec);
		}
		$this->famrec = $this->family->getGedcomRecord();
		$this->display = displayDetailsById($this->famid, 'FAM');

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && PGV_USER_CAN_EDIT && isset($pgv_changes[$this->famid."_".$GEDCOM])) {
			$newrec = find_updated_record($this->famid);
			if (empty($newrec)) $newrec = find_family_record($this->famid);
			$this->difffam = new Family($newrec);
			$this->difffam->setChanged(true);
			$this->family->diffMerge($this->difffam);
			//$this->famrec = $newrec;
			//$this->family = new Family($this->famrec);
		}
		$this->parents = array('HUSB'=>$this->family->getHusbId(), 'WIFE'=>$this->family->getWifeId());

		//-- check if we can display both parents
		if ($this->display == false) {
			$this->showLivingHusb = showLivingNameById($this->parents['HUSB']);
			$this->showLivingWife = showLivingNameById($this->parents['WIFE']);
		}

		//-- add favorites action
		if ($this->action=='addfav' && !empty($_REQUEST['gid']) && PGV_USER_NAME) {
			$_REQUEST['gid'] = strtoupper($_REQUEST['gid']);
			$indirec = find_family_record($_REQUEST['gid']);
			if ($indirec) {
				$favorite = array(
					'username' => PGV_USER_NAME,
					'gid' => $_REQUEST['gid'],
					'type' => 'FAM',
					'file' => $GEDCOM,
					'url' => '',
					'note' => '',
					'title' => ''
				);
				addFavorite($favorite);
			}
		}

		if (PGV_USER_CAN_ACCEPT) {
			if ($this->action=='accept') {
				if (accept_changes($_REQUEST['famid'].'_'.$GEDCOM)) {
					$this->show_changes = false;
					$this->accept_success = true;
					//-- check if we just deleted the record and redirect to index
					$famrec = find_family_record($_REQUEST['famid']);
					if (empty($famrec)) {
						header("Location: index.php?ctype=gedcom");
						exit;
					}
					$this->family = new Family($famrec);
					$this->parents = find_parents($_REQUEST['famid']);
				}
			}

			if ($this->action=='undo') {
				$this->family->undoChange();
				$this->parents = find_parents($_REQUEST['famid']);
			}
		}

		//-- make sure we have the true id from the record
		$ct = preg_match("/0 @(.*)@/", $this->famrec, $match);
		if ($ct > 0) {
			$this->famid = trim($match[1]);
		}

		if ($this->showLivingHusb == false && $this->showLivingWife == false) {
			print_header($pgv_lang['private']." ".$pgv_lang['family_info']);
			print_privacy_error($CONTACT_EMAIL);
			print_footer();
			exit;
		}

		$this->title=$this->family->getFullName();

		if (empty($this->parents['HUSB']) || empty($this->parents['WIFE'])) {
			$this->link_relation = 0;
		} else {
			$this->link_relation = 1;
		}
	}

	function getFamilyID() {
		return $this->famid;
	}

	function getFamilyRecord() {
		return $this->famrec;
	}

	function getHusband() {
		if (!is_null($this->difffam)) return $this->difffam->getHusbId();
		return $this->parents['HUSB'];
	}

	function getWife() {
		if (!is_null($this->difffam)) return $this->difffam->getWifeId();
		return $this->parents['WIFE'];
	}

	function getChildren() {
		return find_children_in_record($this->famrec);
	}

	function getChildrenUrlTimeline($start=0) {
		$children = $this->getChildren();
		$c = count($children);
		for ($i = 0; $i < $c; $i++) {
			$children[$i] = 'pids['.($i + $start).']='.$children[$i];
		}
		return join('&amp;', $children);
	}

	function getPageTitle() {
		return PrintReady($this->title);
	}

	/**
	* get the family page charts menu
	* @return Menu
	*/
	function &getChartsMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		// charts menu
		$menu = new Menu($pgv_lang['charts'], encode_url('timeline.php?pids[0]='.$this->getHusband().'&pids[1]='.$this->getWife()));
		if (!empty($PGV_IMAGES["timeline"]["small"])) {
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['timeline']['small']}");
		}
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["parentTimeLine"] = $pgv_lang['parents_timeline'];
		$menuList["childTimeLine"] = $pgv_lang['children_timeline'];
		$menuList["familyTimeLine"] = $pgv_lang['family_timeline'];
		asort($menuList);

		// Produce the submenus in localized name order

		foreach($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "parentTimeLine":
				// charts / parents_timeline
				$submenu = new Menu($pgv_lang['parents_timeline'], encode_url('timeline.php?pids[0]='.$this->getHusband().'&pids[1]='.$this->getWife()));
				if (!empty($PGV_IMAGES["timeline"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['timeline']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
				break;

			case "childTimeLine":
				// charts / children_timeline
				$submenu = new Menu($pgv_lang['children_timeline'], encode_url('timeline.php?'.$this->getChildrenUrlTimeline()));
				if (!empty($PGV_IMAGES["timeline"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['timeline']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
				break;

			case "familyTimeLine":
				// charts / family_timeline
				$submenu = new Menu($pgv_lang['family_timeline'], encode_url('timeline.php?pids[0]='.$this->getHusband().'&pids[1]='.$this->getWife().'&'.$this->getChildrenUrlTimeline(2)));
				if (!empty($PGV_IMAGES["timeline"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['timeline']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
				break;

			}
		}

		return $menu;
	}

	/**
	* get the family page reports menu
	* @deprecated This function has been deprecated by the getReportsMenu function in menu.php
	* @return Menu
	*/
	function &getReportsMenu() {
	/**
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		$menu = new Menu($pgv_lang['reports'], encode_url('reportengine.php?action=setup&report=reports/familygroup.xml&famid='.$this->getFamilyID()));
		if (!empty($PGV_IMAGES["reports"]["small"])) {
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['reports']['small']}");
		}
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// reports / family_group_report
		$submenu = new Menu($pgv_lang['family_group_report'], encode_url('reportengine.php?action=setup&report=reports/familygroup.xml&famid='.$this->getFamilyID()));
		if (!empty($PGV_IMAGES["reports"]["small"])) {
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['reports']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		return $menu;
		**/
	}

	/**
	* get the family page edit menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $pgv_changes;
		global $SHOW_GEDCOM_RECORD;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		// edit_fam menu
		$menu = new Menu($pgv_lang['edit_fam']);
		if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
		}
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// edit_fam / members
		$submenu = new Menu($pgv_lang['change_family_members']);
		$submenu->addOnclick("return change_family_members('".$this->getFamilyID()."');");
		if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		// edit_fam / add child
		$submenu = new Menu($pgv_lang['add_child_to_family']);
		$submenu->addOnclick("return addnewchild('".$this->getFamilyID()."');");
		if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		// edit_fam / reorder_children
		if ($this->family->getNumberOfChildren() > 1) {
			$submenu = new Menu($pgv_lang['reorder_children']);
			$submenu->addOnclick("return reorder_children('".$this->getFamilyID()."');");
			if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		if (isset($pgv_changes[$this->getFamilyID().'_'.$GEDCOM])) {
			// edit_fam / separator
			$menu->addSeparator();

			// edit_fam / show/hide changes
			if (!$this->show_changes) {
				$submenu = new Menu($pgv_lang['show_changes'], encode_url('family.php?famid='.$this->getFamilyID().'&show_changes=yes'));
				if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			} else {
				$submenu = new Menu($pgv_lang['hide_changes'], encode_url('family.php?famid='.$this->getFamilyID().'&show_changes=no'));
				if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}

			if (PGV_USER_CAN_ACCEPT) {
				// edit_fam / accept_all
				$submenu = new Menu($pgv_lang["undo_all"], encode_url("family.php?famid={$this->famid}&action=undo"));
				if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang["accept_all"], encode_url("family.php?famid={$this->famid}&action=accept"));
				if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}

		// edit_fam / separator
		$menu->addSeparator();

		// edit_fam / edit_raw
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN) {
			$submenu = new Menu($pgv_lang['edit_raw']);
			$submenu->addOnclick("return edit_raw('".$this->getFamilyID()."');");
			if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		// edit_fam / delete_family
		$submenu = new Menu($pgv_lang['delete_family']);
		$submenu->addOnclick("if (confirm('".$pgv_lang["delete_family_confirm"]."')) return delete_family('".$this->getFamilyID()."'); else return false;");
		if (!empty($PGV_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
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

			// other menu
		$menu = new Menu($pgv_lang['other']);
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		if ($SHOW_GEDCOM_RECORD) {
			$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
			if ($this->show_changes && PGV_USER_CAN_EDIT) {
				$menu->addLink("javascript:show_gedcom_record('new');");
			} else {
				$menu->addLink("javascript:show_gedcom_record();");
			}
		} else {
			if (!empty($PGV_IMAGES["clippings"]["small"])) {
				$menu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['clippings']['small']}");
			}
			$menu->addLink(encode_url('clippings.php?action=add&id='.$this->getFamilyID().'&type=fam'));
		}
		if ($SHOW_GEDCOM_RECORD) {
				// other / view_gedcom
				$submenu = new Menu($pgv_lang['view_gedcom']);
				if ($this->show_changes && PGV_USER_CAN_EDIT) {
					$submenu->addLink("javascript:show_gedcom_record('new');");
				} else {
					$submenu->addLink("javascript:show_gedcom_record();");
				}
				$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($ENABLE_CLIPPINGS_CART >= PGV_USER_ACCESS_LEVEL) {
				// other / add_to_cart
				$submenu = new Menu($pgv_lang['add_to_cart'], encode_url('clippings.php?action=add&id='.$this->getFamilyID().'&type=fam'));
				if (!empty($PGV_IMAGES["clippings"]["small"])) {
					$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['clippings']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($this->display && PGV_USER_ID) {
			// other / add_to_my_favorites
			$submenu = new Menu($pgv_lang['add_to_my_favorites'], encode_url('family.php?action=addfav&famid='.$this->getFamilyID().'&gid='.$this->getFamilyID()));
			$submenu->addIcon("{$PGV_IMAGE_DIR}/{$PGV_IMAGES['gedcom']['small']}");
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}
}

if (file_exists('includes/controllers/family_ctrl_user.php')) {
	require_once 'includes/controllers/family_ctrl_user.php';
} else {
	class FamilyController extends FamilyRoot {
	}
}

?>

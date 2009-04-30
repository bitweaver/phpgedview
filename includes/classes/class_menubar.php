<?php
/**
* System for generating menus.
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team. All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* @package PhpGedView
* @version $Id: class_menubar.php,v 1.1 2009/04/30 18:30:36 lsces Exp $
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_CLASS_MENUBAR_PHP', '');

require_once 'includes/classes/class_menu.php';

class MenuBar
{
	/**
	* get the home menu
	* @return Menu the menu item
	*/
	static function &getHomeMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $HOME_SITE_URL, $HOME_SITE_TEXT, $pgv_lang;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		//-- main home menu item
		$menu = new Menu($HOME_SITE_TEXT, $HOME_SITE_URL, "down");
		if (!empty($PGV_IMAGES["home"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["home"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		return $menu;
	}

	/**
	* get the menu with links to the gedcom portals
	* @return Menu the menu item
	*/
	static function &getGedcomMenu() {
		global $GEDCOMS, $ALLOW_CHANGE_GEDCOM;
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		//-- main menu
		$menu = new Menu($pgv_lang["welcome_page"], "index.php?ctype=gedcom", "down");
		if (!empty($PGV_IMAGES["gedcom"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		$menu->addAccesskey($pgv_lang["accesskey_home_page"]);
		//-- gedcom list
		if ($ALLOW_CHANGE_GEDCOM && count($GEDCOMS)>1) {
			foreach($GEDCOMS as $ged=>$gedarray) {
				$submenu = new Menu(PrintReady($gedarray["title"], true), encode_url('index.php?ctype=gedcom&ged='.$ged));
				if (!empty($PGV_IMAGES["gedcom"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}

		//-- Welcome Menu customization
		$filename = "includes/extras/custom_welcome_menu.php";
		if (file_exists($filename)) {
			include $filename;
		}

		return $menu;
	}

	/**
	* get the mygedview menu
	* @return Menu the menu item
	*/
	static function &getMygedviewMenu() {
		global $GEDCOMS, $MEDIA_DIRECTORY, $MULTI_MEDIA;
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT, $USE_QUICK_UPDATE;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		$username = PGV_USER_NAME;
		if (!$username) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
		}

		//-- main menu
		$menu = new Menu($pgv_lang["mygedview"], "index.php?ctype=user", "down");
		if (!empty($PGV_IMAGES["mygedview"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["large"]);
		else if (!empty($PGV_IMAGES["gedcom"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		$menu->addAccesskey($pgv_lang["accesskey_home_page"]);

		//-- mygedview submenu
		$submenu = new Menu($pgv_lang["mgv"], "index.php?ctype=user");
		if (!empty($PGV_IMAGES["mygedview"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (get_user_setting($username, 'editaccount')) {
			$submenu = new Menu($pgv_lang["editowndata"], "edituser.php");
			if (!empty($PGV_IMAGES["mygedview"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["mygedview"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if (PGV_USER_GEDCOM_ID) {
			// Determine whether the Quick Update form can be shown
			$showQuickForm = false;
			if ($USE_QUICK_UPDATE) {
				if ($USE_QUICK_UPDATE==='1' && PGV_USER_IS_ADMIN) $showQuickForm = true;
				else if ($USE_QUICK_UPDATE==='2' && PGV_USER_GEDCOM_ADMIN) $showQuickForm = true;
				else if (($USE_QUICK_UPDATE==='3' || $USE_QUICK_UPDATE===true) && PGV_USER_CAN_EDIT) $showQuickForm = true;
			}
			if ($showQuickForm) {
				//-- quick_update submenu
				$submenu = new Menu($pgv_lang["quick_update_title"], "#");
				$submenu->addOnclick("return quickEdit('".PGV_USER_GEDCOM_ID."', '', '".PGV_GEDCOM."');");
				if (!empty($PGV_IMAGES["indis"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
			//-- my_pedigree submenu
			$submenu = new Menu($pgv_lang["my_pedigree"], encode_url("pedigree.php?rootid=".PGV_USER_GEDCOM_ID."&show_full={$showFull}&talloffset={$showLayout}"));
			if (!empty($PGV_IMAGES["pedigree"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"]);
			//$submenu->addIcon($PGV_IMAGE_DIR."/small/pedigree.gif");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new Menu($pgv_lang["my_indi"], "individual.php?pid=".PGV_USER_GEDCOM_ID);
			if (!empty($PGV_IMAGES["indis"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if (PGV_USER_GEDCOM_ADMIN){
			$menu->addSeparator();
			//-- admin submenu
			$submenu = new Menu($pgv_lang["admin"], "admin.php");
			if (!empty($PGV_IMAGES["admin"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
			//-- manage_gedcoms submenu
			$submenu = new Menu($pgv_lang["manage_gedcoms"], "editgedcoms.php");
			if (!empty($PGV_IMAGES["admin"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
			//-- user_admin submenu
			if (PGV_USER_IS_ADMIN) {
				$submenu = new Menu($pgv_lang["user_admin"], "useradmin.php");
				if (!empty($PGV_IMAGES["admin"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["admin"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				//-- manage_media submenu
				if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
					$submenu = new Menu($pgv_lang["manage_media"], "media.php");
					if (!empty($PGV_IMAGES["menu_media"]["small"]))
						$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_media"]["small"]);
					$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
					$menu->addSubmenu($submenu);
				}
			}
		}
		if (PGV_USER_CAN_EDIT) {
			//-- upload_media submenu
			if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
				$menu->addSeparator();
				$submenu = new Menu($pgv_lang["upload_media"], "uploadmedia.php");
				if (!empty($PGV_IMAGES["menu_media"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_media"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the menu for the charts
	* @return Menu the menu item
	*/
	static function &getChartsMenu($rootid='',$myid='') {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $SEARCH_SPIDER;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
		}

		$ged = $GEDCOM;

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		//-- main charts menu item
		$link = "pedigree.php?ged={$ged}&show_full={$showFull}&talloffset={$showLayout}";
		if ($rootid) {
			$link .= "&rootid={$rootid}";
			$menu = new Menu($pgv_lang["charts"], encode_url($link));
			if (!empty($PGV_IMAGES["pedigree"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"]);
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		}
		else {
			// top menubar
			$menu = new Menu($pgv_lang["charts"], encode_url($link), "down");
			if (!empty($PGV_IMAGES["pedigree"]["large"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["large"]);
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		}

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["pedigree"] = $pgv_lang["pedigree_chart"];
		if (file_exists("descendancy.php")) $menuList["descendancy"] = $pgv_lang["descend_chart"];
		if (file_exists("ancestry.php")) $menuList["ancestry"] = $pgv_lang["ancestry_chart"];
		if (file_exists("compact.php")) $menuList["compact"] = $pgv_lang["compact_chart"];
		if (file_exists("fanchart.php") && function_exists("imagettftext")) $menuList["fanchart"] = $pgv_lang["fan_chart"];
		if (file_exists("hourglass.php")) $menuList["hourglass"] = $pgv_lang["hourglass_chart"];
		if (file_exists("familybook.php")) $menuList["familybook"] = $pgv_lang["familybook_chart"];
		if (file_exists("timeline.php")) $menuList["timeline"] = $pgv_lang["timeline_chart"];
		if (file_exists("lifespan.php")) $menuList["lifespan"] = $pgv_lang["lifespan_chart"];
		if (file_exists("relationship.php")) $menuList["relationship"] = $pgv_lang["relationship_chart"];
		if (file_exists("statistics.php")) $menuList["statistics"] = $pgv_lang["statistics"];
		if (file_exists("treenav.php")) $menuList["treenav"] = $pgv_lang["interactive_tree"];
		asort($menuList);

		// Produce the submenus in localized name order
		foreach($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "pedigree":
				//-- pedigree
				$link = "pedigree.php?ged={$ged}&show_full={$showFull}&talloffset={$showLayout}";
				if ($rootid) $link .= "&rootid={$rootid}";
				$submenu = new Menu($pgv_lang["pedigree_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["pedigree"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["pedigree"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "descendancy":
				//-- descendancy
				$link = "descendancy.php?ged=${ged}";
				if ($rootid) $link .= "&pid={$rootid}&show_full={$showFull}";
				$submenu = new Menu($pgv_lang["descend_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["descendant"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["descendant"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "ancestry":
				//-- ancestry
				$link = "ancestry.php?ged=".$ged;
				if ($rootid) $link .= "&rootid={$rootid}&show_full={$showFull}";
				$submenu = new Menu($pgv_lang["ancestry_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["ancestry"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["ancestry"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "compact":
				//-- compact
				$link = "compact.php?ged=".$ged;
				if ($rootid) $link .= "&rootid=".$rootid;
				$submenu = new Menu($pgv_lang["compact_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["ancestry"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["ancestry"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "fanchart":
				//-- fan chart
				$link = "fanchart.php?ged=".$ged;
				if ($rootid) $link .= "&rootid=".$rootid;
				$submenu = new Menu($pgv_lang["fan_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["fanchart"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["fanchart"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "hourglass":
				//-- hourglass
				$link = "hourglass.php?ged=".$ged;
				if ($rootid) $link .= "&pid={$rootid}&show_full={$showFull}";
				$submenu = new Menu($pgv_lang["hourglass_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["hourglass"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["hourglass"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "familybook":
				//-- familybook
				$link = "familybook.php?ged=".$ged;
				if ($rootid) $link .= "&pid={$rootid}&show_full={$showFull}";
				$submenu = new Menu($pgv_lang["familybook_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["fambook"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["fambook"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "timeline":
				//-- timeline
				$link = "timeline.php?ged=".$ged;
				if ($rootid) $link .= "&amp;pids[]=".$rootid;
				$submenu = new Menu($pgv_lang["timeline_chart"], $link);
				if (!empty($PGV_IMAGES["timeline"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["timeline"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "lifespan":
				//-- lifespan
				$link = "lifespan.php?ged=".$ged;
				if ($rootid) $link .= "&pids[]={$rootid}&addFamily=1";
				$submenu = new Menu($pgv_lang["lifespan_chart"], encode_url($link));
				if (!empty($PGV_IMAGES["timeline"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["timeline"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "relationship":
				//-- relationship
				$pids[] = $myid;
				if ($rootid && empty($myid)) {
					if (PGV_USER_ID) {
						$pids[] = PGV_USER_GEDCOM_ID;
						$pids[] = PGV_USER_ROOT_ID;
					}
				}
				if ($rootid) {
					foreach (getUserFavorites(PGV_USER_NAME) as $key=>$favorite) {
						$pid = $favorite["gid"];
						if (displayDetailsById($pid, $favorite["type"])) {
							if ($favorite["type"]=="INDI" && $favorite["file"]==$GEDCOM) $pids[]=$pid;
						}
					}
				}
				$pids = array_unique($pids);
				foreach ($pids as $key=>$pid) {
					$person=Person::getInstance($pid);
					if (($person && $pid!=$rootid) || empty($rootid)) {
						$link = "relationship.php?ged=".$ged;
						if ($rootid) {
							$link .= "&pid1={$pid}&pid2={$rootid}&pretty=2&followspouse=1";
							$label = $pgv_lang["relationship_chart"].": ".PrintReady(strip_tags($person->getFullName()));
							$submenu = new Menu($label, encode_url($link));
						} else {
							$submenu = new Menu($pgv_lang["relationship_chart"], encode_url($link));
						}
						if (!empty($PGV_IMAGES["relationship"]["small"]))
							$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["relationship"]["small"]);
						$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
						$menu->addSubmenu($submenu);
					}
				}
				break;

			case "statistics":
				//-- statistics plot
				$submenu = new Menu($pgv_lang["statistics"], "statistics.php");
				if (!empty($PGV_IMAGES["statistic"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["statistic"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "treenav":
				//-- interactive tree
				$link = "treenav.php?ged={$ged}&rootid={$rootid}";
				$submenu = new Menu($pgv_lang["interactive_tree"], encode_url($link));
				if (!empty($PGV_IMAGES["gedcom"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;
			}
		}
		return $menu;
	}

	/**
	* get the menu for the lists
	* @return Menu the menu item
	*/
	static function &getListsMenu($surname="") {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		global $SHOW_SOURCES, $MULTI_MEDIA, $SEARCH_SPIDER;
		global $GEDCOMS, $ALLOW_CHANGE_GEDCOM, $DEFAULT_GEDCOM;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		if (!empty($SEARCH_SPIDER)) { // Only want the indi list for search engines.
			//-- main lists menu item
			$link = "indilist.php?ged={$GEDCOM}";
			if ($surname) {
				$link .= "&surname={$surname}";
				$menu = new Menu($pgv_lang["lists"], encode_url($link));
				if (!empty($PGV_IMAGES["indis"]["small"]))
					$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]);
				$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
			}
			else {
				$menu = new Menu($pgv_lang["lists"], encode_url($link), "down");
				if (!empty($PGV_IMAGES["indis"]["large"]))
					$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]);
				$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
			}

			//-- gedcom list
			if ($ALLOW_CHANGE_GEDCOM && count($GEDCOMS)>1) {
				foreach($GEDCOMS as $ged=>$gedarray) {
					$submenu = new Menu(($pgv_lang["individual_list"]." - ".PrintReady($gedarray["title"])), encode_url('indilist.php?ged='.$ged));
					if (!empty($PGV_IMAGES["gedcom"]["small"]))
						$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
					$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
					$menu->addSubmenu($submenu);
				}
			}
			return $menu;
		}
		//-- main lists menu item
		$link = "indilist.php?ged=".$GEDCOM;
		if ($surname) {
			$link .= "&surname=".$surname;
			$menu = new Menu($pgv_lang["lists"], encode_url($link));
			if (!empty($PGV_IMAGES["indis"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]);
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		}
		else {
			$menu = new Menu($pgv_lang["lists"], $link, "down");
			if (!empty($PGV_IMAGES["indis"]["large"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["large"]);
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		}

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["individual"] = $pgv_lang["individual_list"];
		if (file_exists("famlist.php")) $menuList["family"] = $pgv_lang["family_list"];
		if (!$surname && file_exists("sourcelist.php") && $SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL) $menuList["source"] = $pgv_lang["source_list"];
		if (!$surname && file_exists("notelist.php") && $SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL) $menuList["note"] = $pgv_lang["shared_note_list"];
		if (!$surname && file_exists("repolist.php")) $menuList["repository"] = $pgv_lang["repo_list"];
		if (!$surname && file_exists("placelist.php")) $menuList["places"] = $pgv_lang["place_list"];
		if (!$surname && file_exists("medialist.php") && $MULTI_MEDIA) $menuList["media"] = $pgv_lang["media_list"];
		asort($menuList);

		// Produce the submenus in localized name order

		foreach($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "individual":
				//-- indi list sub menu
				$link = "indilist.php?ged=".$GEDCOM;
				if ($surname) $link .= "&surname=".$surname;
				$submenu = new Menu($pgv_lang["individual_list"], encode_url($link));
				if (!empty($PGV_IMAGES["indis"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "family":
				//-- famlist sub menu
				$link = "famlist.php?ged=$GEDCOM";
				if ($surname) $link .= "&amp;surname=".$surname;
				$submenu = new Menu($pgv_lang["family_list"], $link);
				if (!empty($PGV_IMAGES["cfamily"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["cfamily"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "source":
				//-- source
				$submenu = new Menu($pgv_lang["source_list"], encode_url('sourcelist.php?ged='.$GEDCOM));
				if (!empty($PGV_IMAGES["menu_source"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_source"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "note":
				//-- shared note
				$submenu = new Menu($pgv_lang["shared_note_list"], encode_url('notelist.php?ged='.$GEDCOM));
				if (!empty($PGV_IMAGES["notes"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["notes"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "repository":
				//-- repository
				$submenu = new Menu($pgv_lang["repo_list"], "repolist.php");
				if (!empty($PGV_IMAGES["menu_repository"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_repository"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "places":
				//-- places
				$submenu = new Menu($pgv_lang["place_list"], encode_url('placelist.php?ged='.$GEDCOM));
				if (!empty($PGV_IMAGES["place"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["place"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;

			case "media":
				//-- medialist
				$submenu = new Menu($pgv_lang["media_list"], encode_url('medialist.php?ged='.$GEDCOM));
				if (!empty($PGV_IMAGES["menu_media"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_media"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;
			}
		}

		return $menu;
	}

	/**
	* get the menu for the calendar
	* @return Menu the menu item
	*/
	static function &getCalendarMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $SEARCH_SPIDER, $GEDCOM;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists("calendar.php")) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
			}
		//-- main calendar menu item
		$menu = new Menu($pgv_lang["anniversary_calendar"], encode_url('calendar.php?ged='.$GEDCOM), "down");
		if (!empty($PGV_IMAGES["calendar"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		//-- viewday sub menu
		$submenu = new Menu($pgv_lang["viewday"], encode_url('calendar.php?ged='.$GEDCOM));
		if (!empty($PGV_IMAGES["calendar"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		//-- viewmonth sub menu
		$submenu = new Menu($pgv_lang["viewmonth"], encode_url("calendar.php?ged={$GEDCOM}&action=calendar"));
		if (!empty($PGV_IMAGES["calendar"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		//-- viewyear sub menu
		$submenu = new Menu($pgv_lang["viewyear"], encode_url("calendar.php?ged={$GEDCOM}&action=year"));
		if (!empty($PGV_IMAGES["calendar"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the reports menu
	* @return Menu the menu item
	*/
	static function &getReportsMenu($pid="", $famid="") {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOMS, $GEDCOM, $pgv_lang;
		global $LANGUAGE, $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE, $PRIV_HIDE, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists("reportengine.php")) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
			}

		//-- main reports menu item
		if ($pid || $famid) {
			$menu = new Menu($pgv_lang["reports"], "#");
			if (!empty($PGV_IMAGES["reports"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["reports"]["small"]);
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		}
		else {
			// top menubar
			$menu = new Menu($pgv_lang["reports"], encode_url('reportengine.php?ged='.$GEDCOM), "down");
			if (!empty($PGV_IMAGES["reports"]["large"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["reports"]["large"]);
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		}

		// Build a list of reports and sort that list into localized title order
		$reports = get_report_list();
		$menuList = array();
		foreach ($reports as $file=>$report) {
			if (!empty($report["title"][$LANGUAGE])) $label = $report["title"][$LANGUAGE];
			else $label = implode("", $report["title"]);
			$menuList[$file] = trim($label);
		}
		asort($menuList);

		// Produce those submenus in localized name order

		//print_r($reports);
		$username = PGV_USER_NAME;
		foreach($menuList as $file=>$label) {
			$report = $reports[$file];
			if (!isset($report["access"])) $report["access"] = $PRIV_PUBLIC;
			if ($report["access"]>=PGV_USER_ACCESS_LEVEL) {
				if (!empty($report["title"][$LANGUAGE])) $label = $report["title"][$LANGUAGE];
				else $label = implode("", $report["title"]);
				// indi report
				if ($pid) $submenu = new Menu($label, encode_url("reportengine.php?ged={$GEDCOM}&action=setup&report={$report['file']}&pid={$pid}"));
				// family report
				else if ($famid) $submenu = new Menu($label, encode_url("reportengine.php?ged={$GEDCOM}&action=setup&report={$report['file']}&famid={$famid}"));
				// default
				else $submenu = new Menu($label, encode_url("reportengine.php?ged={$GEDCOM}&action=setup&report={$report['file']}"));
				if (isset($PGV_IMAGES["reports"]["small"]) && isset($PGV_IMAGES[$report["icon"]]["small"])) $iconfile=$PGV_IMAGE_DIR."/".$PGV_IMAGES[$report["icon"]]["small"];
				if (isset($iconfile) && file_exists($iconfile)) $submenu->addIcon($iconfile);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				// indi report
				if ($pid && $report["icon"]!="sfamily" && $report["icon"]!="place") $menu->addSubmenu($submenu);
				// family report
				else if ($famid && $report["icon"]=="sfamily") $menu->addSubmenu($submenu);
				// default
				else if (empty($pid) && empty($famid)) $menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the clipping menu
	* @return Menu the menu item
	*/
	static function &getClippingsMenu() {
		global $ENABLE_CLIPPINGS_CART;
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists("clippings.php")) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
		}
		if ($ENABLE_CLIPPINGS_CART <PGV_USER_ACCESS_LEVEL) return null;
		//-- main clippings menu item
		$menu = new Menu($pgv_lang["clippings_cart"], encode_url('clippings.php?ged='.$GEDCOM), "down");
		if (!empty($PGV_IMAGES["clippings"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		return $menu;
	}

	/**
	* get the optional site-specific menu
	* @return Menu the menu item
	*/
	static function &getOptionalMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!file_exists("includes/extras/optional_menu.php") || !empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
		}
		require "includes/extras/optional_menu.php";
		return $menu;
	}

	/**
	* get the print_preview menu
	* @return Menu the menu item
	*/
	static function &getPreviewMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $SCRIPT_NAME, $QUERY_STRING, $pgv_lang;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
			}
		//-- main print_preview menu item
		$menu = new Menu($pgv_lang["print_preview"], $SCRIPT_NAME.normalize_query_string($QUERY_STRING."&amp;view=preview"), "down");
		if (!empty($PGV_IMAGES["printer"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["printer"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		return $menu;
	}

	/**
	* get the search menu
	* @return Menu the menu item
	*/
	static function &getSearchMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang;
		global $SHOW_MULTISITE_SEARCH, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists("search.php")) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
			}
		//-- main search menu item
		$menu = new Menu($pgv_lang["search"], encode_url('search.php?ged='.$GEDCOM), "down");
		if (!empty($PGV_IMAGES["search"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
		//-- search_general sub menu
		$submenu = new Menu($pgv_lang["search_general"], encode_url("search.php?ged={$GEDCOM}&action=general"));
		if (!empty($PGV_IMAGES["search"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new Menu($pgv_lang["search_soundex"], encode_url("search.php?ged={$GEDCOM}&action=soundex"));
		if (!empty($PGV_IMAGES["search"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new Menu($pgv_lang["advanced_search"], encode_url("search_advanced.php?ged={$GEDCOM}"));
		if (!empty($PGV_IMAGES["search"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (PGV_USER_CAN_EDIT) {
			$submenu = new Menu($pgv_lang["search_replace"], encode_url("search.php?ged={$GEDCOM}&action=replace"));
			if (!empty($PGV_IMAGES["search"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}

		//-- search_multisite sub menu
		if ($SHOW_MULTISITE_SEARCH >= PGV_USER_ACCESS_LEVEL) {
			$sitelist = get_server_list();
			if (count($sitelist)>0) {
				$submenu = new Menu($pgv_lang["multi_site_search"], encode_url("search.php?ged={$GEDCOM}&action=multisite"));
				if (!empty($PGV_IMAGES["search"]["small"]))
					$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get an array of module menu objects
	* @return array
	*/
	function getModuleMenus() {
		if (!empty($this->modules)) return $this->modules;
		$this->modules = array();
		if (!file_exists("modules")) return $this->modules;
		$d = dir("modules");
		while (false !== ($entry = $d->read())) {
			if ($entry{0}!="." && $entry!="CVS" && is_dir("modules/$entry")) {
				if (file_exists("modules/$entry/menu.php")) {
					include_once("modules/$entry/menu.php");
					$menu_class = $entry."_ModuleMenu";
					$obj = new $menu_class();
					if (method_exists($obj, "getMenu")) {
						$menu = $obj->getMenu();
						if (is_object($menu)) $this->modules[] = $menu;
					}
				}
			}
		}
		$d->close();

		return $this->modules;
	}

	/**
	* get the help menu
	* @return Menu the menu item
	*/
	static function &getHelpMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $pgv_lang, $SEARCH_SPIDER;
		global $SHOW_CONTEXT_HELP, $SCRIPT_NAME, $QUERY_STRING, $helpindex, $action;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
			$menu->print_menu = null;
			return $menu;
			}
		//-- main help menu item
		$menu = new Menu($pgv_lang["page_help"], "#", "down");
		if (!empty($PGV_IMAGES["help"]["large"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["help"]["large"]);
		if (empty($helpindex))
			$menu->addOnclick("return helpPopup('help_".basename($SCRIPT_NAME)."&amp;action=".$action."');");
		else
			$menu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");

		//-- help_for_this_page sub menu
		$submenu = new Menu($pgv_lang["help_for_this_page"], "#");
		if (!empty($PGV_IMAGES["menu_help"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_help"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		if (empty($helpindex))
			$submenu->addOnclick("return helpPopup('help_".basename($SCRIPT_NAME)."&amp;action=".$action."');");
		else
			$submenu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addSubmenu($submenu);
		//-- help_contents sub menu
		$submenu = new Menu($pgv_lang["help_contents"], "#");
		if (!empty($PGV_IMAGES["menu_help"]["small"]))
			$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_help"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$submenu->addOnclick("return helpPopup('help_contents_help');");
		$menu->addSubmenu($submenu);
		//-- faq sub menu
		if (file_exists("faq.php")) {
			$submenu = new Menu($pgv_lang["faq_list"], "faq.php");
			if (!empty($PGV_IMAGES["menu_help"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["menu_help"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		//-- searchhelp sub menu
		if (file_exists("searchhelp.php")) {
			$submenu = new Menu($pgv_lang["hs_title"], "#");
			if (!empty($PGV_IMAGES["search"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["search"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$submenu->addOnclick("window.open('searchhelp.php', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');");
			$menu->addSubmenu($submenu);
		}

		//-- add wiki links
		$menu->addSeparator();
		$submenu = new Menu($pgv_lang["wiki_main_page"], PGV_PHPGEDVIEW_WIKI.'/en/index.php?title=Main_Page" target="_blank');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);

		$submenu = new Menu($pgv_lang["wiki_users_guide"], PGV_PHPGEDVIEW_WIKI.'/en/index.php?title=Users_Guide" target="_blank');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);

		if (PGV_USER_GEDCOM_ADMIN) {
			$submenu = new Menu($pgv_lang["wiki_admin_guide"], PGV_PHPGEDVIEW_WIKI.'/en/index.php?title=Administrators_Guide" target="_blank');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}

		//-- add contact links to help menu
		$menu->addSeparator();
		$menuitems = contact_menus();
		foreach($menuitems as $menuitem) {
			$submenu = new Menu($menuitem["label"], $menuitem["link"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			if (!empty($menuitem["onclick"])) $submenu->addOnclick($menuitem["onclick"]);
			$menu->addSubmenu($submenu);
		}
		//-- add show/hide context_help
		$menu->addSeparator();
		if ($_SESSION["show_context_help"])
			$submenu = new Menu($pgv_lang["hide_context_help"], "$SCRIPT_NAME".normalize_query_string($QUERY_STRING."&amp;show_context_help=no"));
		else
			$submenu = new Menu($pgv_lang["show_context_help"], "$SCRIPT_NAME".normalize_query_string($QUERY_STRING."&amp;show_context_help=yes"));
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the menu with links change to each theme
	* @return Menu the menu item
	*/
	static function &getThemeMenu() {
		global $SEARCH_SPIDER, $ALLOW_THEME_DROPDOWN, $ALLOW_USER_THEMES, $THEME_DIR, $pgv_lang;

		$current=$THEME_DIR;
		foreach (get_theme_names() as $themedir) {
			if ($themedir==get_user_setting(PGV_USER_ID, 'theme')) {
				$current=$themedir;
			}
		}

		if ($ALLOW_THEME_DROPDOWN && $ALLOW_USER_THEMES && !$SEARCH_SPIDER) {
			isset($_SERVER["QUERY_STRING"]) == true?$tqstring = "?".$_SERVER["QUERY_STRING"]:$tqstring = "";
			$frompage = $_SERVER["SCRIPT_NAME"].decode_url($tqstring);
			if (isset($_REQUEST['mod'])) {
				if (!strstr("?", $frompage)) {
					if (!strstr("%3F", $frompage)) ;
					else $frompage .= "?";
				}
				if (!strstr("&mod",$frompage)) $frompage .= "&mod=".$_REQUEST['mod'];
			}
			if (substr($frompage,-1) == "?") $frompage = substr($frompage,0,-1);
			if (substr($frompage,-1) == "&") $frompage = substr($frompage,0,-1);
			$menu=new Menu($pgv_lang['change_theme']);
			$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu');
			$menu->print_menu = null;
			foreach (get_theme_names() as $themename=>$themedir) {
				$submenu=new Menu($themename, encode_url("themechange.php?frompage={$frompage}&mytheme={$themedir}"));
				if ($themedir==$current) {
					$submenu->addClass('favsubmenuitem_selected', 'favsubmenuitem_hover');
				} else {
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				}
				$menu->addSubMenu($submenu);
			}
			return $menu;
		} else {
			return new Menu('', '');
		}
	}
	/**
	* get the menu with links to change language
	* @return Menu the menu item
	*/
	static function &getLanguageMenu() {
		global $ENABLE_MULTI_LANGUAGE, $LANGUAGE, $pgv_lang, $pgv_lang_self, $language_settings, $flagsfile, $QUERY_STRING, $SCRIPT_NAME, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		if (PGV_USER_ID) {
			$current=$LANGUAGE;
		} else {
			$current=get_user_setting(PGV_USER_ID, 'language');
		}

		if ($ENABLE_MULTI_LANGUAGE) {
			$menu=new Menu($pgv_lang['change_lang'], '#', 'down');
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");

			$menu->print_menu = null;
			foreach ($language_settings as $lang=>$language) {
				if ($language['pgv_lang_use'] && isset($language['pgv_lang_self']) && isset($language['pgv_language']) && file_exists($language['pgv_language'])) {
					$submenu=new Menu($language['pgv_lang_self'], $SCRIPT_NAME.normalize_query_string($QUERY_STRING.'&amp;changelanguage=yes&amp;NEWLANGUAGE='.$lang));
					if ($lang==$LANGUAGE) {
						$submenu->addClass('activeflag', 'brightflag');
					} else {
						$submenu->addClass('dimflag', 'brightflag');
					}
					$submenu->addIcon($flagsfile[$lang]);
					$menu->addSubMenu($submenu);
				}
			}
			if (count($menu->submenus)>1) {
				return $menu;
			} else {
				return new Menu('', '');
			}
		} else {
			return new Menu('', '');
		}
	}
	/**
	* get the menu with links to the user/gedcom favorites
	* @return Menu the menu item
	*/
	static function &getFavouritesMenu() {		// Don't break custom themes using the old name
		return self::getFavoritesMenu();
	}
	static function &getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $pgv_lang, $GEDCOM, $QUERY_STRING, $SCRIPT_NAME, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION;
		global $SEARCH_SPIDER;
		global $controller; // Pages with a controller can be added to the favorites

		if (!empty($SEARCH_SPIDER)) {
			return false; // show no favorites, because they taint every page that is indexed.
		}

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		if (PGV_USER_ID || !$REQUIRE_AUTHENTICATION) {
			$menu=new Menu($pgv_lang['favorites'], '#', 'down');
			if (!empty($PGV_IMAGES['gedcom']['large'])) {
				$menu->addIcon($PGV_IMAGE_DIR.'/'.$PGV_IMAGES['gedcom']['large']);
			}
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff");
			$menu->print_menu = NULL;

			$userfavs=getUserFavorites(PGV_USER_ID);
			$gedfavs=getUserFavorites($GEDCOM);

			// User favorites
			if ($userfavs || PGV_USER_ID) {
				$submenu=new Menu('<strong>'.$pgv_lang['my_favorites'].'</strong>');
				$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				$menu->addSubMenu($submenu);
				while (PGV_USER_ID && isset($controller)) {
					// Get the right $gid from each supported controller type
					switch (get_class($controller)) {
					case 'IndividualController':
						$gid = $controller->pid;
						break;
					case 'FamilyController':
						$gid = $controller->famid;
						break;
					case 'MediaController':
						$gid = $controller->mid;
						break;
					case 'SourceController':
						$gid = $controller->sid;
						break;
					case 'RepositoryController':
						$gid = $controller->rid;
						break;
					default:
						break 2;
					}
					$submenu=new Menu('<em>'.$pgv_lang['add_to_my_favorites'].'</em>', $SCRIPT_NAME.normalize_query_string($QUERY_STRING.'&amp;action=addfav&amp;gid='.$gid));
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
					$menu->addSubMenu($submenu);
					break;
				}
				foreach ($userfavs as $fav) {
					$OLD_GEDCOM=$GEDCOM;
					$GEDCOM=$fav['file'];
					switch($fav['type']) {
					case 'URL':
						$submenu=new Menu(PrintReady($fav['title']), $fav['url']);
						$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
						$menu->addSubMenu($submenu);
						break;
					case 'INDI':
					case 'FAM':
					case 'SOUR':
					case 'OBJE':
						if (displayDetailsById($fav['gid'], $fav['type'])) {
							$obj=GedcomRecord::getInstance($fav['gid']);
							if ($obj) {
								$submenu=new Menu(PrintReady($obj->getFullName()), encode_url($obj->getLinkUrl()));
								$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
								$menu->addSubMenu($submenu);
							}
						}
						break;
					}
					$GEDCOM=$OLD_GEDCOM;
				}
				if ($gedfavs) {
					$menu->addSeparator();
				}
			}
			// Gedcom favorites
			if ($gedfavs) {
				$submenu=new Menu('<strong>'.$pgv_lang['gedcom_favorites'].'</strong>');
				$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				$menu->addSubMenu($submenu);
				foreach ($gedfavs as $fav) {
					$OLD_GEDCOM=$GEDCOM;
					$GEDCOM=$fav['file'];
					switch($fav['type']) {
					case 'URL':
						$submenu=new Menu(PrintReady($fav['title']), $fav['url']);
						$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
						$menu->addSubMenu($submenu);
						break;
					case 'INDI':
					case 'FAM':
					case 'SOUR':
					case 'OBJE':
						if (displayDetailsById($fav['gid'], $fav['type'])) {
							$obj=GedcomRecord::getInstance($fav['gid']);
							if ($obj) {
								$submenu=new Menu(PrintReady($obj->getFullName()), encode_url($obj->getLinkUrl()));
								$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
								$menu->addSubMenu($submenu);
							}
						}
						break;
					}
					$GEDCOM=$OLD_GEDCOM;
				}
			}
		}
		return $menu;
	}
}

?>

<?php
/**
* Controller for the Media Menu
* Extends the IndividualController class and overrides the getEditMenu() function
* Menu options are changed to apply to a media object instead of an individual
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
*
* @package PhpGedView
* @subpackage Charts
* @version $Id$
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_MEDIA_CTRL_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/individual_ctrl.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_media.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_import.php');

class MediaControllerRoot extends IndividualController{

	var $mid;
	var $mediaobject;
	var $show_changes=true;

	function init() {
		global $MEDIA_DIRECTORY, $USE_MEDIA_FIREWALL, $GEDCOM, $pgv_changes;

		$filename = decrypt(safe_GET('filename'));
		$this->mid = safe_GET_xref('mid');

		if ($USE_MEDIA_FIREWALL && empty($filename) && empty($this->mid)) {
			// this section used by mediafirewall.php to determine what media file was requested

			if (isset($_SERVER['REQUEST_URI'])) {
				// NOTE: format of this server variable:
				// Apache: /phpGedView/media/a.jpg
				// IIS:    /phpGedView/mediafirewall.php?404;http://server/phpGedView/media/a.jpg
				$requestedfile = $_SERVER['REQUEST_URI'];
				// urldecode the request
				$requestedfile = rawurldecode($requestedfile);
				// make sure the requested file is in the media directory
				if (strpos($requestedfile, $MEDIA_DIRECTORY) !== false) {
					// strip off the pgv directory and media directory from the requested url so just the image information is left
					$filename = substr($requestedfile, strpos($requestedfile, $MEDIA_DIRECTORY) + strlen($MEDIA_DIRECTORY) - 1);
					// if user requested a thumbnail, lookup permissions based on the original image
					$filename = str_replace('/thumbs', '', $filename);
				}
			}
		}

		//Checks to see if the File Name ($filename) exists
		if (!empty($filename)){
			//If the File Name ($filename) is set, then it will call the method to get the Media ID ($this->mid) from the File Name ($filename)
			$this->mid = get_media_id_from_file($filename);
			if (!$this->mid){
				//This will set the Media ID to be false if the File given doesn't match to anything in the database
				$this->mid = false;
				// create a very basic gedcom record for this file so that the functions of the media object will work
				// this is used by the media firewall when requesting an object that exists in the media firewall directory but not in the gedcom
				$this->mediaobject = new Media("0 @"."0"."@ OBJE\n1 FILE ".$filename);
			}
		}

		//checks to see if the Media ID ($this->mid) is set. If the Media ID isn't set then there isn't any information avaliable for that picture the picture doesn't exist.
		if ($this->mid){
			//This creates a Media Object from the getInstance method of the Media Class. It takes the Media ID ($this->mid) and creates the object.
			$this->mediaobject = Media::getInstance($this->mid);
			//This sets the controller ID to be the Media ID
			$this->pid = $this->mid;
		}

		if (is_null($this->mediaobject)) return false;

		$this->mediaobject->ged_id=PGV_GED_ID; // This record is from a file

		//-- perform the desired action
		switch($this->action) {
			case "addfav":
				$this->addFavorite();
				break;
			case "accept":
				$this->acceptChanges();
				break;
			case "undo":
				$this->mediaobject->undoChange();
				break;
		}

		if ($this->mediaobject->canDisplayDetails()) {
			$this->canedit = PGV_USER_CAN_EDIT;
		}
	}

	/**
	* Add a new favorite for the action user
	*/
	function addFavorite() {
		global $GEDCOM;
		if (!PGV_USER_ID) {
			return;
		}
		if (!empty($_REQUEST["gid"])) {
			$gid = strtoupper($_REQUEST["gid"]);
			$mediarec = find_media_record($gid);
			if ($mediarec) {
				$favorite = array();
				$favorite["username"] = PGV_USER_NAME;
				$favorite["gid"] = $gid;
				$favorite["type"] = "OBJE";
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
	* Also update the mediarec we will use to generate the page
	*/
	function acceptChanges() {
		global $GEDCOM;
		if (!PGV_USER_CAN_ACCEPT) return;
		if (accept_changes($this->pid."_".$GEDCOM)) {
			$this->show_changes=false;
			$this->accept_success=true;
			$mediarec = find_media_record($this->pid);
			//-- check if we just deleted the record and redirect to index
			if (empty($mediarec)) {
				header("Location: index.php?ctype=gedcom");
				exit;
			}
			//$this->mediaobject = Media::getInstance($this->pid);
			$this->mediaobject = new Media($mediarec);
		}
		//This sets the controller ID to be the Media ID
		if (is_null($this->mediaobject)) $this->mediaobject = new Media("0 @".$this->pid."@ OBJE");
	}

	/**
	* return the title of this page
	* @return string the title of the page to go in the <title> tags
	*/
	function getPageTitle() {
		global $pgv_lang, $GEDCOM;

		if (!is_null($this->mediaobject)) {
			$name = $this->mediaobject->getFullName();
			return $name." - ".$this->mediaobject->getXref();
		}
		else return $pgv_lang["unable_to_find_record"];
	}

	function canDisplayDetails() {
		return $this->mediaobject->canDisplayDetails();
	}

	/**
	* get the edit menu
	* @return Menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $TOTAL_NAMES;
		global $NAME_LINENUM, $SEX_LINENUM, $pgv_lang, $pgv_changes, $USE_QUICK_UPDATE;
		global $SHOW_GEDCOM_RECORD;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";
		$links = get_media_relations($this->pid);
		$linktoid = "new";
		foreach ($links as $linktoid => $type) {
			break; // we're only interested in the key of the first list entry
		}
		//-- main edit menu
		$menu = new Menu($pgv_lang["edit"]);
		$click_link = "window.open('addmedia.php?action=editmedia&pid={$this->pid}&linktoid={$linktoid}', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1')";
		$menu->addOnclick($click_link);
		if (!empty($PGV_IMAGES["edit_indi"]["small"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_indi"]["small"]);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if (PGV_USER_CAN_EDIT) {
			//- plain edit option
			$submenu = new Menu($pgv_lang["edit"]);
			$click_link = "window.open('addmedia.php?action=editmedia&pid={$this->pid}&linktoid={$linktoid}', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1')";
			$submenu->addOnclick($click_link);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN) {
				$submenu = new Menu($pgv_lang["edit_raw"]);
				$submenu->addOnclick("return edit_raw('".$this->pid."');");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
			//- end plain edit option
			if (PGV_USER_GEDCOM_ADMIN) {
				//- remove object option
				$submenu = new Menu($pgv_lang["remove_object"]);
				$submenu->addLink(encode_url("media.php?action=removeobject&xref=".$this->pid));
				$submenu->addOnclick("return confirm('".$pgv_lang["confirm_remove_object"]."')");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			// main link displayed on page
			if (PGV_USER_GEDCOM_ADMIN && file_exists('modules/GEDFact_assistant/_MEDIA/media_1_ctrl.php')) {
				$submenu = new Menu($pgv_lang["add_or_remove_links"]);
			} else {	
				$submenu = new Menu($pgv_lang["set_link"]);
			}
			
			// GEDFact assistant Add Media Links =======================
			if (PGV_USER_GEDCOM_ADMIN && file_exists('modules/GEDFact_assistant/_MEDIA/media_1_ctrl.php')) {
				$submenu->addOnclick("return ilinkitem('".$this->pid."','manage');");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
				// Do not print ssubmunu
			} else {
				$submenu->addOnclick("return ilinkitem('".$this->pid."','person');");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");

				$ssubmenu = new Menu($pgv_lang["to_person"]);
				$ssubmenu->addOnclick("return ilinkitem('".$this->pid."','person');");
				$ssubmenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$submenu->addSubMenu($ssubmenu);

				$ssubmenu = new Menu($pgv_lang["to_family"]);
				$ssubmenu->addOnclick("return ilinkitem('".$this->pid."','family');");
				$ssubmenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$submenu->addSubMenu($ssubmenu);

				$ssubmenu = new Menu($pgv_lang["to_source"]);
				$ssubmenu->addOnclick("return ilinkitem('".$this->pid."','source');");
				$ssubmenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$submenu->addSubMenu($ssubmenu);
			}
			$menu->addSubmenu($submenu);
		}
		if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
			$menu->addSeparator();
			if (!$this->show_changes) {
				$label = $pgv_lang["show_changes"];
				$link = "mediaviewer.php?mid={$this->pid}&show_changes=yes";
			}
			else {
				$label = $pgv_lang["hide_changes"];
				$link = "mediaviewer.php?mid={$this->pid}&show_changes=no";
			}
			$submenu = new Menu($label, encode_url($link));
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if (PGV_USER_CAN_ACCEPT) {
				$submenu = new Menu($pgv_lang["undo_all"], encode_url("mediaviewer.php?mid={$this->pid}&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang["accept_all"], encode_url("mediaviewer.php?mid={$this->pid}&action=accept"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* check if we can show the other menu
	* @return boolean
	*/
	function canShowOtherMenu() {
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART;
		if ($this->mediaobject->canDisplayDetails() && ($SHOW_GEDCOM_RECORD || $ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL))
			return true;
		return false;
	}

	/**
	* get the "other" menu
	* @return Menu
	*/
	function &getOtherMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $THEME_DIR;
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART, $pgv_lang;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";
		//-- main other menu item
		$menu = new Menu($pgv_lang["other"]);
		if ($SHOW_GEDCOM_RECORD) {
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			if ($this->show_changes && PGV_USER_CAN_EDIT)
				$menu->addOnclick("return show_gedcom_record('new');");
			else
				$menu->addOnclick("return show_gedcom_record('');");
		}
		else {
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$menu->addLink(encode_url("clippings.php?action=add&id={$this->pid}&type=obje"));
		}
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if ($this->canShowGedcomRecord()) {
			$submenu = new Menu($pgv_lang["view_gedcom"]);
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			if ($this->show_changes && PGV_USER_CAN_EDIT) $submenu->addOnclick("return show_gedcom_record('new');");
			else $submenu->addOnclick("return show_gedcom_record();");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->mediaobject->canDisplayDetails() && $ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL) {
			$submenu = new Menu($pgv_lang["add_to_cart"], encode_url("clippings.php?action=add&id={$this->pid}&type=obje"));
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->mediaobject->canDisplayDetails() && PGV_USER_ID) {
			$submenu = new Menu($pgv_lang["add_to_my_favorites"], encode_url("mediaviewer.php?action=addfav&mid={$this->pid}&gid={$this->pid}"));
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}

	/**
	* check if we can show the gedcom record
	* @return boolean
	*/
	function canShowGedcomRecord() {
		global $SHOW_GEDCOM_RECORD;
		if ($SHOW_GEDCOM_RECORD && $this->mediaobject->canDisplayDetails())
			return true;
	}

	/**
	* return a list of facts
	* @return array
	*/
	function getFacts($includeFileName=true) {
		global $pgv_changes, $GEDCOM, $pgv_lang;

		$ignore = array("TITL","FILE");
		if ($this->show_changes) $ignore = array();
		else if (PGV_USER_GEDCOM_ADMIN) $ignore = array("TITL");

		$facts = $this->mediaobject->getFacts($ignore);
		sort_facts($facts);
//		if ($includeFileName) $facts[] = new Event("1 FILE ".$this->mediaobject->getFilename());
		$mediaType = $this->mediaobject->getMediatype();
		if (isset($pgv_lang["TYPE__".$mediaType])) $facts[] = new Event("1 TYPE ".$pgv_lang["TYPE__".$mediaType]);
		else $facts[] = new Event("1 TYPE ".$pgv_lang["TYPE__other"]);

		if (isset($pgv_changes[$this->pid."_".$GEDCOM]) && ($this->show_changes)) {
			$newrec = find_updated_record($this->pid);
			$newmedia = new Media($newrec);
			$newfacts = $newmedia->getFacts($ignore);
			if ($includeFileName) $newfacts[] = new Event("1 TYPE ".$pgv_lang["TYPE__".$mediaType]);
			$newfacts[] = new Event("1 FORM ".$newmedia->getFiletype());
			$mediaType = $newmedia->getMediatype();
			if (isset($pgv_lang["TYPE__".$mediaType])) $newfacts[] = new Event("1 TYPE ".$mediaType);
			else $newfacts[] = new Event("1 TYPE ".$pgv_lang["TYPE__other"]);
			//-- loop through new facts and add them to the list if they are any changes
			//-- compare new and old facts of the Personal Fact and Details tab 1
			for($i=0; $i<count($facts); $i++) {
				$found=false;
				foreach($newfacts as $indexval => $newfact) {
					if (trim($newfact->gedcomRecord)==trim($facts[$i]->gedcomRecord)) {
						$found=true;
						break;
					}
				}
				if (!$found) {
					$facts[$i]->gedcomRecord.="\nPGV_OLD\n";
				}
			}
			foreach($newfacts as $indexval => $newfact) {
				$found=false;
				foreach($facts as $indexval => $fact) {
					if (trim($fact->gedcomRecord)==trim($newfact->gedcomRecord)) {
						$found=true;
						break;
					}
				}
				if (!$found) {
					$newfact->gedcomRecord.="\nPGV_NEW\n";
					$facts[]=$newfact;
				}
			}
		}

		if ($this->mediaobject->fileExists()) {
			// get height and width of image, when available
			if ($this->mediaobject->getWidth()) {
				$facts[] = new Event("1 EVEN " . '<span dir="ltr">' . $this->mediaobject->getWidth()." x ".$this->mediaobject->getHeight() . '</span>' . "\n2 TYPE image_size");
			}
			//Prints the file size
			//Rounds the size of the image to 2 decimal places
			$facts[] = new Event("1 EVEN " . '<span dir="ltr">' . round($this->mediaobject->getFilesizeraw()/1024, 2)." kb" . '</span>' . "\n2 TYPE file_size");
		}

		sort_facts($facts);
		return $facts;
	}

	/**
	* get the relative file path of the image on the server
	* @return string
	*/
	function getLocalFilename() {
		global $pgv_lang;
		if ($this->mediaobject) {
			return $this->mediaobject->getLocalFilename();
		}
		else {
			return false;
		}
	}

	/**
	* get the file name on the server
	* @return string
	*/
	function getServerFilename() {
		return $this->mediaobject->getServerFilename();
	}

}
// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/media_ctrl_user.php')) {
	include_once 'includes/controllers/media_ctrl_user.php';
} else {
	class MediaController extends MediaControllerRoot {
	}
}

?>

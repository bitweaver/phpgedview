<?php
/**
 * Controller for the Media Menu
 * Extends the IndividualController class and overrides the getEditMenu() function
 * Menu options are changed to apply to a media object instead of an individual
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008	John Finlay and Others
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
 * @version $Id: media_ctrl.php,v 1.1 2008/07/07 17:57:40 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once('includes/controllers/individual_ctrl.php');
require_once("includes/media_class.php");

class MediaControllerRoot extends IndividualController{

	var $mediaobject;

	function init() {
		global $MEDIA_DIRECTORY, $USE_MEDIA_FIREWALL;
		if (isset($_REQUEST['filename'])) $filename = $_REQUEST['filename'];
		if (isset($_REQUEST['mid'])) $mid = $_REQUEST['mid'];
		if (!empty($_REQUEST["show_changes"])) $this->show_changes = $_REQUEST["show_changes"];
		if (!empty($_REQUEST["action"])) $this->action = $_REQUEST["action"];
		
		//-- keep the time of this access to help with concurrent edits
		$_SESSION['last_access_time'] = time();

		if ($USE_MEDIA_FIREWALL && empty($filename) && empty($mid)) {
			// this section used by mediafirewall.php to determine what media file was requested

			if (isset($_SERVER['REQUEST_URI'])) {
				// NOTE: format of this server variable:
				//   Apache: /phpGedView/media/a.jpg
				//   IIS:    /phpGedView/mediafirewall.php?404;http://server/phpGedView/media/a.jpg
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
			//If the File Name ($filename) is set, then it will call the method to get the Media ID ($mid) from the File Name ($filename)
			$mid = get_media_id_from_file($filename);
			if (!$mid){
				//This will set the Media ID to be false if the File given doesn't match to anything in the database
				$mid = false;
				// create a very basic gedcom record for this file so that the functions of the media object will work
				// this is used by the media firewall when requesting an object that exists in the media firewall directory but not in the gedcom
				$this->mediaobject = new Media("0 @"."0"."@ OBJE\n1 FILE ".$filename);
			}
		}
	
		//checks to see if the Media ID ($mid) is set. If the Media ID isn't set then there isn't any information avaliable for that picture the picture doesn't exist.
		if (isset($mid) && $mid!=false){
			$mid = clean_input($mid);
			//This creates a Media Object from the getInstance method of the Media Class. It takes the Media ID ($mid) and creates the object.
			$this->mediaobject = Media::getInstance($mid);
			//This sets the controller ID to be the Media ID
			$this->pid = $mid;

			if (is_null($this->mediaobject)) $this->mediaobject = new Media("0 @".$mid."@ OBJE");
		}

		// one final test to be sure we have a media object defined
		// ways this can happen:
		//   if user failed to pass in a filename or mid to mediaviewer.php
		//   if the server did not set the environmental variables correctly for the media firewall
		//   if media firewall is being called from outside the media directory  
		if (is_null($this->mediaobject)) $this->mediaobject = new Media("0 @"."0"."@ OBJE");

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
			$indirec = find_gedcom_record($gid);
			if ($indirec) {
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
	 * Also update the indirec we will use to generate the page
	 */
	function acceptChanges() {
		global $GEDCOM, $medialist;
		if (!PGV_USER_CAN_ACCEPT) return;
		require_once("includes/functions_import.php");
		if (accept_changes($this->pid."_".$GEDCOM)) {
			$this->show_changes="no";
			$this->accept_success=true;
			//-- delete the record from the cache and refresh it
			if (isset($medialist[$this->pid])) unset($medialist[$this->pid]);
			$indirec = find_gedcom_record($this->pid);
			//-- check if we just deleted the record and redirect to index
			if (empty($indirec)) {
				header("Location: index.php?ctype=gedcom");
				exit;
			}
			$this->mediaobject = Media::getInstance($this->pid);
		}
		//This sets the controller ID to be the Media ID
		if (is_null($this->mediaobject)) $this->mediaobject = new Media("0 @".$this->pid."@ OBJE");
	}

	/**
	 * return the title of this page
	 * @return string	the title of the page to go in the <title> tags
	 */
	function getPageTitle() {
		global $pgv_lang, $GEDCOM;

		if (!is_null($this->mediaobject)) {
			$name = $this->mediaobject->getTitle();
			return $name." - ".$this->mediaobject->getXref();
		}
		else return $pgv_lang["unknown"];
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
			break;		// we're only interested in the key of the first list entry
		}
		//-- main edit menu
		$menu = new Menu($pgv_lang["edit"]);
		$click_link = "";
		$click_link .= "window.open('addmedia.php?action=";
		$click_link .= "editmedia&amp;pid=".$this->pid;
		$click_link .= "&amp;linktoid=$linktoid";
		$click_link .= "', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1')";
		$menu->addOnclick($click_link);
		if (!empty($PGV_IMAGES["edit_indi"]["small"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_indi"]["small"]);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if (PGV_USER_CAN_EDIT) {
			//- plain edit option
			$submenu = new Menu($pgv_lang["edit"]);
			$click_link = "";
			$click_link .= "window.open('addmedia.php?action=";
			$click_link .= "editmedia&amp;pid=".$this->pid;
			$click_link .= "&amp;linktoid=$linktoid";
			$click_link .= "', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1')";
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
				$submenu->addLink("media.php?action=removeobject&amp;xref=".$this->pid);
				$submenu->addOnclick("return confirm('".$pgv_lang["confirm_remove_object"]."')");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			// main link displayed on page
			$submenu = new Menu($pgv_lang["set_link"]." >");
			$submenu->addOnclick("return ilinkitem('".$this->pid."','person');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
//			$submenu->addFlyout('right');

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
			$menu->addSubmenu($submenu);
		}
		if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
			$menu->addSeperator();
			if ($this->show_changes=="no") {
				$label = $pgv_lang["show_changes"];
				$link = "mediaviewer.php?mid=".$this->pid."&amp;show_changes=yes";
			}
			else {
				$label = $pgv_lang["hide_changes"];
				$link = "mediaviewer.php?mid=".$this->pid."&amp;show_changes=no";
			}
			$submenu = new Menu($label, $link);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if (PGV_USER_CAN_ACCEPT) {
				$submenu = new Menu($pgv_lang["undo_all"], "mediaviewer.php?mid=".$this->pid."&amp;action=undo");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang["accept_all"], "mediaviewer.php?mid=".$this->pid."&amp;action=accept");
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
			if ($this->show_changes=="yes"  && PGV_USER_CAN_EDIT)
				$menu->addOnclick("return show_gedcom_record('new');");
			else
				$menu->addOnclick("return show_gedcom_record('');");
		}
		else {
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$menu->addLink("clippings.php?action=add&amp;id=".$this->pid."&amp;type=obje");
		}
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if ($this->canShowGedcomRecord()) {
			$submenu = new Menu($pgv_lang["view_gedcom"]);
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			if ($this->show_changes=="yes"  && PGV_USER_CAN_EDIT) $submenu->addOnclick("return show_gedcom_record('new');");
			else $submenu->addOnclick("return show_gedcom_record();");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->mediaobject->canDisplayDetails() && $ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL) {
			$submenu = new Menu($pgv_lang["add_to_cart"], "clippings.php?action=add&amp;id=".$this->pid."&amp;type=obje");
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->mediaobject->canDisplayDetails() && PGV_USER_ID) {
			$submenu = new Menu($pgv_lang["add_to_my_favorites"], "mediaviewer.php?action=addfav&amp;mid=".$this->pid."&amp;gid=".$this->pid);
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

		$ignore = "TITL,FILE";
		if ($this->show_changes=='yes') $ignore = '';
		else if (PGV_USER_GEDCOM_ADMIN) $ignore = "TITL";
		$facts = get_all_subrecords($this->mediaobject->getGedcomRecord(), $ignore);
		if ($includeFileName) $facts[] = "1 FILE ".$this->mediaobject->getFilename();
		$facts[] = "1 FORM ".$this->mediaobject->getFiletype();
		$mediaType = $this->mediaobject->getMediatype();
		if (isset($pgv_lang["TYPE__".$mediaType])) $facts[] = "1 TYPE ".$pgv_lang["TYPE__".$mediaType];
		else $facts[] = "1 TYPE ".$pgv_lang["TYPE__other"];

		if (isset($pgv_changes[$this->pid."_".$GEDCOM]) && ($this->show_changes=="yes")) {
			$newrec = find_updated_record($this->pid);
			$newfacts = get_all_subrecords($newrec);
			$newmedia = new Media($newrec);
			if ($includeFileName) $newfacts[] = "1 TYPE ".$pgv_lang["TYPE__".$mediaType];
			$newfacts[] = "1 FORM ".$newmedia->getFiletype();
			$mediaType = $newmedia->getMediatype();
			if (isset($pgv_lang["TYPE__".$mediaType])) $newfacts[] = "1 TYPE ".$mediaType;
			else $newfacts[] = "1 TYPE ".$pgv_lang["TYPE__other"];
			//print_r($newfacts);
			//-- loop through new facts and add them to the list if they are any changes
			//-- compare new and old facts of the Personal Fact and Details tab 1
			for($i=0; $i<count($facts); $i++) {
				$found=false;
				foreach($newfacts as $indexval => $newfact) {
					if (trim($newfact)==trim($facts[$i])) {
						$found=true;
						break;
					}
				}
				if (!$found) {
					$facts[$i].="\r\nPGV_OLD\r\n";
				}
			}
			foreach($newfacts as $indexval => $newfact) {
				$found=false;
				foreach($facts as $indexval => $fact) {
					if (trim($fact)==trim($newfact)) {
						$found=true;
						break;
					}
				}
				if (!$found) {
					$newfact.="\r\nPGV_NEW\r\n";
					$facts[]=$newfact;
				}
			}
		}

		if ($this->mediaobject->fileExists()) {
			// get height and width of image, when available
			if ($this->mediaobject->getWidth()) {
				$facts[] = "1 EVEN " . getLRM() . $this->mediaobject->getWidth()." x ".$this->mediaobject->getHeight() . getLRM() . "\r\n2 TYPE image_size";
			}
			//Prints the file size
			//Rounds the size of the image to 2 decimal places
			$size = getLRM() . round($this->mediaobject->getFilesizeraw()/1024, 2)." kb" . getLRM();
			$facts[] = "1 EVEN ".$size."\r\n2 TYPE file_size";
		}

		sort_facts($facts);
		return $facts;
	}

	/**
	 * get the relative file path of the image on the server
	 * @return string
	 */
	function getLocalFilename() {
		return $this->mediaobject->getLocalFilename();
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
if (file_exists('includes/controllers/media_ctrl_user.php'))
{
	include_once 'includes/controllers/media_ctrl_user.php';
}
else
{
	class MediaController extends MediaControllerRoot
	{
	}
}
$controller = new MediaController();
$controller->init();

?>

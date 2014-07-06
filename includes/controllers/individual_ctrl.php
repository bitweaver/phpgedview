<?php
/**
* Controller for the Individual Page
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
* @subpackage Charts
* @version $Id$
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_INDIVIDUAL_CTRL_PHP', '');

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

// leave manual config until we can move it to bitweaver table 
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print_facts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_menu.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_person.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_family.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_import.php');

$indifacts = array(); // -- array to store the fact records in for sorting and displaying
$globalfacts = array();
$otheritems = array(); //-- notes, sources, media objects
$FACT_COUNT=0;
// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts[] = "FAMS";
$nonfacts[] = "FAMC";
$nonfacts[] = "MAY";
$nonfacts[] = "BLOB";
$nonfacts[] = "CHIL";
$nonfacts[] = "HUSB";
$nonfacts[] = "WIFE";
$nonfacts[] = "RFN";
$nonfacts[] = "_PGV_OBJS";
$nonfacts[] = "";

//$nonfamfacts[] = "NCHI"; // Turning back on NCHI display for the indi page.
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";

// SET Family Navigator for each Tab as necessary  - SHOW/HIDE ===============
$NAV_FACTS	 = "SHOW";		// Facts and Details Tab Navigator
$NAV_NOTES	 = "SHOW";		// Notes Tab Navigator
$NAV_SOURCES = "SHOW";		// Sources Tab Navigator
$NAV_MEDIA	 = "SHOW";		// Media Tab Navigator
$NAV_ALBUM	 = "SHOW";		// Album Tab Navigator
// ========================================================================

/**
* Main controller class for the individual page.
*/
class IndividualControllerRoot extends BaseController {
	var $pid = "";
	var $default_tab = 0;
	var $indi = null;
	var $diffindi = null;
	var $NAME_LINENUM = 1;
	var $accept_success = false;
	var $visibility = "visible";
	var $position = "relative";
	var $display = "block";
	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $sexarray = array();
	var $tabarray = array("facts","notes","sources","media","relatives","tree","research","map","lightbox","spare","nav");

	/**
	* constructor
	*/
	function IndividualControllerRoot() {
		parent::BaseController();
	}

	/**
	* Initialization function
	*/
	function init() {
		global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $GEDCOM_DEFAULT_TAB, $pgv_changes, $pgv_lang, $CHARACTER_SET;
		global $USE_QUICK_UPDATE, $pid;
		global $Fam_Navigator, $gBitUser, $gGedcom;

		$this->sexarray["M"] = $pgv_lang["male"];
		$this->sexarray["F"] = $pgv_lang["female"];
		$this->sexarray["U"] = $pgv_lang["unknown"];

		$this->pid = safe_GET_xref('pid');

		$show_famlink = $this->view!='preview';

		$pid = $this->pid;

		$this->default_tab = $GEDCOM_DEFAULT_TAB;
		$indirec = find_person_record($this->pid);

		if ($USE_RIN && $indirec==false) {
			$this->pid = find_rin_id($this->pid);
			$indirec = find_person_record($this->pid);
		}
		if (empty($indirec)) {
			$ct = preg_match("/(\w+):(.+)/", $this->pid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				include_once('includes/classes/class_serviceclient.php');
				$service = ServiceClient::getInstance($servid);
				if ($service != null) {
					$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$this->pid."@ INDI\n1 RFN ".$this->pid, false);
					$indirec = $newrec;
				}
			} else if (isset($pgv_changes[$this->pid."_".$GEDCOM])){
				$indirec = "0 @".$this->pid."@ INDI\n";
			} else {
				return false;
			}
		}
		//-- check for the user
		if ( $gBitUser->isRegistered() ) {
			$this->default_tab = get_user_setting( $gBitUser->mUserId, 'defaulttab');
		}

		//-- check for a cookie telling what the last tab was when they were last
		//-- visiting this individual
		if($this->default_tab == -2)
		{
			if (isset($_COOKIE['lasttabs'])) {
				$ct = preg_match("/".$this->pid."=(\d+)/", $_COOKIE['lasttabs'], $match);
				if ($ct>0) {
					$this->default_tab = $match[1]-1;
				}
			}
		}

		//-- if the action is a research assistant action then default to the RA tab
		if (strstr($this->action, 'ra_')!==false) $this->default_tab = 6;

		//-- set the default tab from a request parameter
		if (isset($_REQUEST['tab'])) {
			$this->default_tab = $_REQUEST['tab'];
		}

		if ($this->default_tab<-2 || $this->default_tab>9) $this->default_tab=0;

		$this->indi = new Person($indirec, false);
		$this->indi->ged_id = $gGedcom->mGEDCOMId; // This record is from a file

		//-- if the person is from another gedcom then forward to the correct site
		/*
		if ($this->indi->isRemote()) {
			header('Location: '.encode_url(decode_url($this->indi->getLinkUrl()), false));
			exit;
		}
		*/
		if (!$this->isPrintPreview()) {
			$this->visibility = "hidden";
			$this->position = "absolute";
			$this->display = "none";
		}
		//-- perform the desired action
		switch($this->action) {
			case "addfav":
				$this->addFavorite();
				break;
			case "accept":
				$this->acceptChanges();
				break;
			case "undo":
				$this->indi->undoChange();
				break;
		}

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && $gGedcom->isEditable() ) {
			if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
				//-- get the changed record from the file
				$newrec = find_updated_record($this->pid);
				//print("jkdsakjhdkjsadkjsakjdhsakd".$newrec);
				$remoterfn = get_gedcom_value("RFN", 1, $newrec);
			} else {
				$remoterfn = get_gedcom_value("RFN", 1, $indirec);
			}
			// print "remoterfn=".$remoterfn;
			//-- get an updated record from the web service
			if (!empty($remoterfn)) {
				$parts = explode(':', $remoterfn);
				if (count($parts)==2) {
					$servid = $parts[0];
					$aliaid = $parts[1];
					if (!empty($servid)&&!empty($aliaid)) {
						require_once("includes/classes/class_serviceclient.php");
						$serviceClient = ServiceClient::getInstance($servid);
						if (!is_null($serviceClient)) {
							if (!empty($newrec)) $mergerec = $serviceClient->mergeGedcomRecord($aliaid, $newrec, true);
							else $mergerec = $serviceClient->mergeGedcomRecord($aliaid, $indirec, true);
							if ($serviceClient->type=="remote") {
								$newrec = $mergerec;
							}
							else {
								$indirec = $mergerec;
							}
						}
					}
				}
			}
			if (!empty($newrec)) {
				$this->diffindi = new Person($newrec, false);
				$this->diffindi->setChanged(true);
				$indirec = $newrec;
			}
		}

		if ($this->show_changes) {
			$this->indi->diffMerge($this->diffindi);
		}

		//-- only allow editors or users who are editing their own individual or their immediate relatives
		if ($this->indi->canDisplayDetails()) {
			$this->canedit = $gGedcom->isEditable();
/* Disable self-editing completely until we have a GEDCOM config option to control this
			if (!$this->canedit && $USE_QUICK_UPDATE) {
				$my_id=PGV_USER_GEDCOM_ID;
				if ($my_id) {
					if ($this->pid==$my_id) $this->canedit=true;
					else {
						$famids = array_merge(find_sfamily_ids($my_id), find_family_ids($my_id));
						foreach($famids as $indexval => $famid) {
							if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_family_record($famid);
							else $famrec = find_updated_record($famid);
							if (preg_match("/1 (HUSB|WIFE|CHIL) @$this->pid@/", $famrec)>0) $this->canedit=true;
						}
					}
				}
			}
*/
		}

		//-- handle ajax calls
		if ($this->action=="ajax") {
			$tab = 0;
			if (isset($_REQUEST['tab'])) $tab = $_REQUEST['tab']-1;
			header("Content-Type: text/html; charset=$CHARACTER_SET");//AJAX calls do not have the meta tag headers and need this set
			$this->getTab($tab);
			//-- only get the requested tab and then exit
/* TODO
			if (PGV_DEBUG_SQL) {
				echo ADOdb::getQueryLog();
			}
 */
 			exit;
		}
	}
	//-- end of init function
	/**
	* Add a new favorite for the action user
	*/
	function addFavorite() {
		global $GEDCOM;
		if (PGV_USER_ID && !empty($_REQUEST["gid"])) {
			$gid = strtoupper($_REQUEST["gid"]);
			$indirec = find_person_record($gid);
			if ($indirec) {
				$favorite = array();
				$favorite["username"] = PGV_USER_NAME;
				$favorite["gid"] = $gid;
				$favorite["type"] = "INDI";
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
		if (accept_changes($this->pid."_".$GEDCOM)) {
			$this->show_changes=false;
			$this->accept_success=true;
			//-- delete the record from the cache and refresh it
			$indirec = find_person_record($this->pid);
			//-- check if we just deleted the record and redirect to index
			if (empty($indirec)) {
				header("Location: index.php?ctype=gedcom");
				exit;
			}
			$this->indi = new Person($indirec);
		}
	}

	/**
	* return the title of this page
	* @return string the title of the page to go in the <title> tags
	*/
	function getPageTitle() {
		global $pgv_lang;
		if ($this->indi) {
			$name = $this->indi->getFullName();
			return $name." - ".$this->indi->getXref()." - ".$pgv_lang["indi_info"];
		}
		else {
			return $pgv_lang["unable_to_find_record"];
		}
	}

	/**
	* gets a string used for setting the value of a cookie using javascript
	*/
	function getCookieTabString() {
		$str = "";
		if (isset($_COOKIE['lasttabs'])) {
			$parts = explode(':', $_COOKIE['lasttabs']);
			foreach($parts as $i=>$val) {
				$inner = explode('=', $val);
				if (count($inner)>1) {
					if ($inner[0]!=$this->pid) $str .= $val.":";
				}
			}
		}
		return $str;
	}
	/**
	* check if we can show the highlighted media object
	* @return boolean
	*/
	function canShowHighlightedObject() {
		global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES;

		if (($this->indi->canDisplayDetails()) && ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES)) {
			$firstmediarec = $this->indi->findHighlightedMedia();
			if ($firstmediarec) return true;
		}
		return false;
	}
	/**
	* check if we can show the gedcom record
	* @return boolean
	*/
	function canShowGedcomRecord() {
		global $SHOW_GEDCOM_RECORD;
		if (PGV_USER_CAN_EDIT && $SHOW_GEDCOM_RECORD && $this->indi->canDisplayDetails())
			return true;
	}
	/**
	* check if use can edit this person
	* @return boolean
	*/
	function userCanEdit() {
		return $this->canedit;
	}
	/**
	* get the highlighted object HTML
	* @return string HTML string for the <img> tag
	*/
	function getHighlightedObject() {
		global $USE_THUMBS_MAIN, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER, $GEDCOM;
		if ($this->canShowHighlightedObject()) {
			$firstmediarec = $this->indi->findHighlightedMedia();
			if (!empty($firstmediarec)) {
				$filename = thumb_or_main($firstmediarec);		// Do we send the main image or a thumbnail?
				if (!$USE_THUMBS_MAIN || $firstmediarec["_THUM"]=='Y') {
					$class = "image";
				} else {
					$class = "thumbnail";
				}
				$isExternal = isFileExternal($filename);
				if ($isExternal && $class=="thumbnail") $class .= "\" width=\"".$THUMBNAIL_WIDTH;
				if (!empty($filename)) {
					$result = "";
					$imgsize = findImageSize($firstmediarec["file"]);
					$imgwidth = $imgsize[0]+40;
					$imgheight = $imgsize[1]+150;
					//Gets the Media View Link Information and Concatenate
					$mid = $firstmediarec['mid'];

					$name = $this->indi->getFullName();
					if (file_exists("modules/lightbox/album.php")) {
						print "<a href=\"" . $firstmediarec["file"] . "\" rel=\"clearbox[general_1]\" rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name,ENT_QUOTES,'UTF-8')) . "\">" . "\n";
					}else

					if (!$USE_MEDIA_VIEWER && $imgsize) {
						$result .= "<a href=\"javascript:;\" onclick=\"return openImage('".encode_url(encrypt($firstmediarec["file"]))."',$imgwidth, $imgheight);\">";
					}else{
						$result .= "<a href=\"mediaviewer.php?mid={$mid}\">";
					}
					$result .= "<img src=\"$filename\" align=\"left\" class=\"".$class."\" border=\"none\" title=\"".PrintReady(htmlspecialchars(strip_tags($name),ENT_QUOTES,'UTF-8'))."\" alt=\"".PrintReady(htmlspecialchars(strip_tags($name),ENT_QUOTES,'UTF-8'))."\" />";
					$result .= "</a>";
					return $result;
				}
			}
		}
	}

	/**
	* print information for a name record
	*
	* Called from the individual information page
	* @see individual.php
	* @param Event $event the event object
	*/
	function print_name_record(&$event) {
		global $pgv_lang, $factarray, $UNDERLINE_NAME_QUOTES, $NAME_REVERSE;
		global $lang_short_cut, $LANGUAGE;

		if (!$event->canShowDetails()) return false;
		$factrec = $event->getGedComRecord();
		$linenum = $event->getLineNumber();

		$this->name_count++;
		print "<td valign=\"top\"";
		if (preg_match("/PGV_OLD/", $factrec)>0) print " class=\"namered\"";
		if (preg_match("/PGV_NEW/", $factrec)>0) print " class=\"nameblue\"";
		print ">";
		if (!preg_match("/^2 (SURN)|(GIVN)/m", $factrec)) {
			$dummy=new Person($factrec);
			$dummy->setPrimaryName(0);
			echo '<span class="label">', $pgv_lang['name'], ': </span><br />';
			echo PrintReady($dummy->getFullName()), '<br />';
		}
		$ct = preg_match_all("/\n2 (\w+) (.*)/", $factrec, $nmatch, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$fact = trim($nmatch[$i][1]);
			if (($fact!="SOUR")&&($fact!="NOTE")) {
				if ($fact=="_AKAN" || $fact=="_AKA" || $fact=="ALIA") {
					// Allow special processing for different languages
					$func="fact_AKA_localisation_{$lang_short_cut[$LANGUAGE]}";
					if (function_exists($func)) {
						// Localise the AKA fact
						$func($fact, $this->pid);
					}
				}
				print "\n\t\t\t<span class=\"label\">";
				if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
				else if (isset($factarray[$fact])) print $factarray[$fact];
				else print $fact;
				print ":</span><span class=\"field\"> ";
				if (isset($nmatch[$i][2])) {
					$name = trim($nmatch[$i][2]);
					$name = preg_replace("'/,'", ",", $name);
					$name = preg_replace("'/'", " ", $name);
					if ($UNDERLINE_NAME_QUOTES) $name=preg_replace('/"([^"]*)"/', '<span class="starredname">\\1</span>', $name);
					$name=preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $name);
					print PrintReady($name);
				}
				print " </span><br />";
			}
		}
		if ($this->total_names>1 && !$this->isPrintPreview() && $this->userCanEdit() && !strpos($factrec, 'PGV_OLD')) {
			print "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" class=\"font9\" onclick=\"edit_name('".$this->pid."', ".$linenum."); return false;\">".$pgv_lang["edit_name"]."</a> | ";
			print "<a class=\"font9\" href=\"javascript:;\" onclick=\"delete_record('".$this->pid."', ".$linenum."); return false;\">".$pgv_lang["delete_name"]."</a>\n";
			if ($this->name_count==2) print_help_link("delete_name_help", "qm");
			print "<br />\n";
		}
		$ct = preg_match("/\d (NOTE)|(SOUR)/", $factrec);
		if ($ct>0) {
			// -- find sources for this name
			print "<div class=\"indent\">";
			print_fact_sources($factrec, 2);
			//-- find the notes for this name
			print "&nbsp;&nbsp;&nbsp;";
			print_fact_notes($factrec, 2);
			print "</div><br />";
		}
		print "</td>\n";
	}

	/**
	* print information for a sex record
	*
	* Called from the individual information page
	* @see individual.php
	* @param Event $event the Event object
	*/
	function print_sex_record(&$event) {
		global $pgv_lang, $sex;

		if (!$event->canShowDetails()) return false;
		$sex = $event->getDetail();
		if (empty($sex)) $sex = "U";
		print "<td valign=\"top\"><span class=\"label\">".$pgv_lang["sex"].": </span><span class=\"field\">".$this->sexarray[$sex];
		if ($sex=='M') {
			echo Person::sexImage('M', 'small', '', $pgv_lang['male']);
		} elseif ($sex=='F') {
			echo Person::sexImage('F', 'small', '', $pgv_lang['female']);
		} else {
			echo Person::sexImage('U', 'small', '', $pgv_lang['unknown']);
		}
		if ($this->SEX_COUNT>1) {
			if ((!$this->isPrintPreview()) && ($this->userCanEdit()) && (preg_match("/PGV_OLD/", $event->getGedComRecord())==0)) {
				if ($event->getLineNumber()=="new") {
					print "<br /><a class=\"font9\" href=\"javascript:;\" onclick=\"add_new_record('".$this->pid."', 'SEX'); return false;\">".$pgv_lang["edit"]."</a>";
				} else {
						print "<br /><a class=\"font9\" href=\"javascript:;\" onclick=\"edit_record('".$this->pid."', ".$event->getLineNumber()."); return false;\">".$pgv_lang["edit"]."</a> | ";
						print "<a class=\"font9\" href=\"javascript:;\" onclick=\"delete_record('".$this->pid."', ".$event->getLineNumber()."); return false;\">".$pgv_lang["delete"]."</a>\n";
				}
			}
		}
		print "<br /></span>";
		// -- find sources
		print "&nbsp;&nbsp;&nbsp;";
		print_fact_sources($event->getGedComRecord(), 2);
		//-- find the notes
		print "&nbsp;&nbsp;&nbsp;";
		print_fact_notes($event->getGedComRecord(), 2);
		print "</td>";
	}
	/**
	* get the edit menu
	* @return Menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM;
		global $NAME_LINENUM, $SEX_LINENUM, $pgv_lang, $pgv_changes, $USE_QUICK_UPDATE;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";
		//-- main edit menu
		$menu = new Menu($pgv_lang["edit"]);
		if (!empty($PGV_IMAGES["edit_indi"]["small"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_indi"]["small"]);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		// Determine whether the Quick Update form can be shown
		$showQuickForm = false;
		if ($USE_QUICK_UPDATE) {
			if ($USE_QUICK_UPDATE==='1' && PGV_USER_IS_ADMIN) $showQuickForm = true;
			else if ($USE_QUICK_UPDATE==='2' && PGV_USER_GEDCOM_ADMIN) $showQuickForm = true;
			else if (($USE_QUICK_UPDATE==='3' || $USE_QUICK_UPDATE===true) && PGV_USER_CAN_EDIT) $showQuickForm = true;
		}
		if ($showQuickForm) {
			$submenu = new Menu($pgv_lang["quick_update_title"]);
			$submenu->addOnclick("return quickEdit('".$this->pid."','','".$GEDCOM."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			$menu->addSeparator();
		}

		if (PGV_USER_CAN_EDIT) {
			if (count($this->indi->getSpouseFamilyIds())>1) {
				$submenu = new Menu($pgv_lang["reorder_families"]);
				$submenu->addOnclick("return reorder_families('".$this->pid."');");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			if ($this->total_names<2) {
				$submenu = new Menu($pgv_lang["edit_name"]);
				$submenu->addOnclick("return edit_name('".$this->pid."', $NAME_LINENUM);");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			$submenu = new Menu($pgv_lang["add_name"]);
			$submenu->addOnclick("return add_name('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if ($this->SEX_COUNT<2) {
				$submenu = new Menu($pgv_lang["edit_sex"]);
				if ($SEX_LINENUM=="new") $submenu->addOnclick("return add_new_record('".$this->pid."', 'SEX');");
				else $submenu->addOnclick("return edit_record('".$this->pid."', $SEX_LINENUM);");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
			if (!$this->show_changes) {
				$label = $pgv_lang["show_changes"];
				$link = $this->indi->getLinkUrl()."&show_changes=yes";
			} else {
				$label = $pgv_lang["hide_changes"];
				$link = $this->indi->getLinkUrl()."&show_changes=no";
			}
			$submenu = new Menu($label, encode_url($link));
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if (PGV_USER_CAN_ACCEPT) {
				$submenu = new Menu($pgv_lang["undo_all"], encode_url($this->indi->getLinkUrl()."&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang["accept_all"], encode_url($this->indi->getLinkUrl()."&action=accept"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		if (PGV_USER_IS_ADMIN || $this->canShowGedcomRecord()) {
			$submenu = new Menu($pgv_lang["edit_raw"]);
			$submenu->addOnclick("return edit_raw('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}

		$submenu = new Menu($pgv_lang["delete_person"]);
		$submenu->addOnclick("return deleteperson('".$this->pid."');");
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
		$menu->addSubmenu($submenu);

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}
		return $menu;
	}
	/**
	* check if we can show the other menu
	* @return boolean
	*/
	function canShowOtherMenu() {
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART;
		if ($this->indi->canDisplayDetails() && ($SHOW_GEDCOM_RECORD || $ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL))
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
			if (!empty($PGV_IMAGES["gedcom"]["small"])) $menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			if ($this->show_changes && PGV_USER_CAN_EDIT) $menu->addOnclick("return show_gedcom_record('new');");
			else $menu->addOnclick("return show_gedcom_record('');");
		} else {
			if (!empty($PGV_IMAGES["clippings"]["small"])) $menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$menu->addLink(encode_url("clippings.php?action=add&id={$this->pid}&type=indi"));
		}
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if ($SHOW_GEDCOM_RECORD) {
			$submenu = new Menu($pgv_lang["view_gedcom"]);
			if (!empty($PGV_IMAGES["gedcom"]["small"])) $submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			if ($this->show_changes && PGV_USER_CAN_EDIT) $submenu->addOnclick("return show_gedcom_record('new');");
			else $submenu->addOnclick("return show_gedcom_record();");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->indi->canDisplayDetails() && $ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL) {
			$submenu = new Menu($pgv_lang["add_to_cart"], encode_url("clippings.php?action=add&id={$this->pid}&type=indi"));
			if (!empty($PGV_IMAGES["clippings"]["small"])) $submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->indi->canDisplayDetails() && PGV_USER_NAME) {
			$submenu = new Menu($pgv_lang["add_to_my_favorites"], encode_url($this->indi->getLinkUrl()."&action=addfav&gid={$this->pid}"));
			if (!empty($PGV_IMAGES["gedcom"]["small"])) $submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}
	/**
	* get global facts
	* global facts are NAME and SEX
	* @return array return the array of global facts
	*/
	function getGlobalFacts() {
		global $NAME_LINENUM, $SEX_LINENUM;

		$globalfacts = $this->indi->getGlobalFacts();
		foreach ($globalfacts as $key => $value) {
			$fact = $value->getTag();
			if ($fact=="SEX") {
				$this->SEX_COUNT++;
				$SEX_LINENUM = $value->getLineNumber();
			}
			if ($fact=="NAME") {
				$this->total_names++;
				$NAME_LINENUM = $value->getLineNumber();
			}
			}
		return $globalfacts;
	}
	/**
	* get the individual facts shown on tab 1
	* @return array
	*/
	function getIndiFacts() {
		$indifacts = $this->indi->getIndiFacts();
		sort_facts($indifacts);
		return $indifacts;
	}
	/**
	* get the other facts shown on tab 2
	* @return array
	*/
	function getOtherFacts() {
		$otherfacts = $this->indi->getOtherFacts();
		return $otherfacts;
	}
	/**
	* get the person box stylesheet class
	* for the given person
	* @param Person $person
	* @return string returns 'person_box', 'person_boxF', or 'person_boxNN'
	*/
	function getPersonStyle(&$person) {
		$sex = $person->getSex();
		switch($sex) {
			case "M":
				$isf = "";
				break;
			case "F":
				$isf = "F";
				break;
			default:
				$isf = "NN";
				break;
		}
		return "person_box".$isf;
	}
	/**
	* build an array of Person that will be used to build a list
	* of family members on the close relatives tab
	* @param Family $family the family we are building for
	* @return array an array of Person that will be used to iterate through on the indivudal.php page
	*/
	function buildFamilyList(&$family, $type) {
		global $factarray, $pgv_lang;
		$people = array();
		if (!is_object($family)) return $people;
		$labels = array();
		if ($type=="parents") {
			$labels["parent"] = $pgv_lang["parent"];
			$labels["mother"] = $pgv_lang["mother"];
			$labels["father"] = $pgv_lang["father"];
			$labels["sibling"] = $pgv_lang["sibling"];
			$labels["sister"] = $pgv_lang["sister"];
			$labels["brother"] = $pgv_lang["brother"];
		}
		if ($type=="step"){
			$labels["parent"] = $pgv_lang["stepparent"];
			$labels["mother"] = $pgv_lang["stepmom"];
			$labels["father"] = $pgv_lang["stepdad"];
			$labels["sibling"] = $pgv_lang["halfsibling"];
			$labels["sister"] = $pgv_lang["halfsister"];
			$labels["brother"] = $pgv_lang["halfbrother"];
		}
		if ($type=="spouse") {
			if ($family->isDivorced()) {
				$labels["parent"] = $pgv_lang["ex-spouse"];
				$labels["mother"] = $pgv_lang["ex-wife"];
				$labels["father"] = $pgv_lang["ex-husband"];
			}
			else {
				$marr_rec = $family->getMarriageRecord();
				if (!empty($marr_rec)) {
					$type = $family->getMarriageType();
					if (empty($type) || stristr($type, "partner")===false) {
						$labels["parent"] = $pgv_lang["spouse"];
						$labels["mother"] = $pgv_lang["wife"];
						$labels["father"] = $pgv_lang["husband"];
					}
					else {
						if (isset($pgv_lang[$type])) $label = $pgv_lang[$type];
						else $label = $pgv_lang["partner"];
						$labels["parent"] = $label;
						$labels["mother"] = $label;
						$labels["father"] = $label;
					}
				}
				else {
					$labels["parent"] = $pgv_lang["spouse"];
					$labels["mother"] = $pgv_lang["wife"];
					$labels["father"] = $pgv_lang["husband"];
				}
			}
			$labels["sibling"] = $pgv_lang["child"];
			$labels["sister"] = $pgv_lang["daughter"];
			$labels["brother"] = $pgv_lang["son"];
		}
		$newhusb = null;
		$newwife = null;
		$newchildren = array();
		$delchildren = array();
		$children = array();
		$husb = null;
		$wife = null;
		if (!$family->getChanged()) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();
		}
		//-- step families : set the label for the common parent
		if ($type=="step") {
			$fams = $this->indi->getChildFamilies();
			foreach($fams as $key=>$fam) {
				if ($fam->hasParent($husb)) $labels["father"] = $pgv_lang["father"];
				if ($fam->hasParent($wife)) $labels["mother"] = $pgv_lang["mother"];
			}
		}
		//-- set the label for the husband
		if (!is_null($husb)) {
			$label = $labels["parent"];
			$sex = $husb->getSex();
			if ($sex=="F") {
				$label = $labels["mother"];
			}
			if ($sex=="M") {
				$label = $labels["father"];
			}
			if ($husb->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$husb->setLabel($label);
		}
		//-- set the label for the wife
		if (!is_null($wife)) {
			$label = $labels["parent"];
			$sex = $wife->getSex();
			if ($sex=="F") {
				$label = $labels["mother"];
			}
			if ($sex=="M") {
				$label = $labels["father"];
			}
			if ($wife->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$wife->setLabel($label);
		}
		if ($this->show_changes) {
			$newfamily = $family->getUpdatedFamily();
			if (!is_null($newfamily)) {
				$newhusb = $newfamily->getHusband();
				//-- check if the husband in the family has changed
				if (!is_null($newhusb) && !$newhusb->equals($husb)) {
					$label = $labels["parent"];
					$sex = $newhusb->getSex();
					if ($sex=="F") {
						$label = $labels["mother"];
					}
					if ($sex=="M") {
						$label = $labels["father"];
					}
					if ($newhusb->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
					$newhusb->setLabel($label);
				}
				else $newhusb = null;
				$newwife = $newfamily->getWife();
				//-- check if the wife in the family has changed
				if (!is_null($newwife) && !$newwife->equals($wife)) {
					$label = $labels["parent"];
					$sex = $newwife->getSex();
					if ($sex=="F") {
						$label = $labels["mother"];
					}
					if ($sex=="M") {
						$label = $labels["father"];
					}
					if ($newwife->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
					$newwife->setLabel($label);
				}
				else $newwife = null;
				//-- check for any new children
				$merged_children = array();
				$new_children = $newfamily->getChildren();
				$num = count($children);
				for($i=0; $i<$num; $i++) {
					$child = $children[$i];
					if (!is_null($child)) {
						$found = false;
						foreach($new_children as $key=>$newchild) {
							if (!is_null($newchild)) {
								if ($child->equals($newchild)) {
									$found = true;
									break;
			}
		}
		}
						if (!$found) $delchildren[] = $child;
						else $merged_children[] = $child;
					}
			}
				foreach($new_children as $key=>$newchild) {
					if (!is_null($newchild)) {
						$found = false;
						foreach($children as $key1=>$child) {
							if (!is_null($child)) {
								if ($child->equals($newchild)) {
									$found = true;
									break;
								}
							}
						}
						if (!$found) $newchildren[] = $newchild;
					}
				}
				$children = $merged_children;
			}
		}
		//-- set the labels for the children
		$num = count($children);
		for($i=0; $i<$num; $i++) {
			if (!is_null($children[$i])) {
				$label = $labels["sibling"];
				$sex = $children[$i]->getSex();
				if ($sex=="F") {
				$label = $labels["sister"];
				}
				if ($sex=="M") {
				$label = $labels["brother"];
				}
				if ($children[$i]->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
				$famcrec = get_sub_record(1, "1 FAMC @".$family->getXref()."@", $children[$i]->gedrec);
				$pedi = get_gedcom_value("PEDI", 2, $famcrec, '', false);
				if ($pedi) {
					if ($pedi=="birth") $label .= " (".$factarray["BIRT"].")";
					else if (isset($pgv_lang[$pedi])) $label .= " (".$pgv_lang[$pedi].")";
				}
				$children[$i]->setLabel($label);
			}
		}
		$num = count($newchildren);
		for($i=0; $i<$num; $i++) {
				$label = $labels["sibling"];
			$sex = $newchildren[$i]->getSex();
			if ($sex=="F") {
				$label = $labels["sister"];
			}
			if ($sex=="M") {
				$label = $labels["brother"];
		}
			if ($newchildren[$i]->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$pedi = $newchildren[$i]->getChildFamilyPedigree($family->getXref());
			if ($pedi && isset($pgv_lang[$pedi])) $label .= " (".$pgv_lang[$pedi].")";
			$newchildren[$i]->setLabel($label);
		}
		$num = count($delchildren);
		for($i=0; $i<$num; $i++) {
				$label = $labels["sibling"];
			$sex = $delchildren[$i]->getSex();
			if ($sex=="F") {
				$label = $labels["sister"];
			}
			if ($sex=="M") {
				$label = $labels["brother"];
			}
			if ($delchildren[$i]->getXref()==$this->pid) $label = "<img src=\"images/selected.png\" alt=\"\" />";
			$pedi = $delchildren[$i]->getChildFamilyPedigree($family->getXref());
			if ($pedi && isset($pgv_lang[$pedi])) $label .= " (".$pgv_lang[$pedi].")";
			$delchildren[$i]->setLabel($label);
		}
		if (!is_null($newhusb)) $people['newhusb'] = $newhusb;
		if (!is_null($husb)) $people['husb'] = $husb;
		if (!is_null($newwife)) $people['newwife'] = $newwife;
		if (!is_null($wife)) $people['wife'] = $wife;
		$people['children'] = $children;
		$people['newchildren'] = $newchildren;
		$people['delchildren'] = $delchildren;
		return $people;
	}

	/**
	* print family header
	* @param String family id
	* @param String family label
	* @return html table
	*/
	function printFamilyHeader($famid, $label) {
		global $pgv_lang;
		global $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_ID_NUMBERS, $SEARCH_SPIDER;
	?>
		<table>
			<tr>
				<td><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["cfamily"]["small"]; ?>" border="0" class="icon" alt="" /></td>
				<td><span class="subheaders"><?php print PrintReady($label); ?></span>
				<?php if ((!$this->isPrintPreview())&&(empty($SEARCH_SPIDER))) { ?>
					- <a href="family.php?famid=<?php print $famid; ?>">[<?php print $pgv_lang["view_family"]; ?><?php if ($SHOW_ID_NUMBERS) print " " . getLRM() . "($famid)" . getLRM(); ?>]</a>
				<?php }?>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	* print parents informations
	* @param Family family
	* @param Array people
	* @param String family type
	* @return html table rows
	*/
	function printParentsRows(&$family, &$people, $type) {
		global $personcount, $pgv_changes, $pgv_lang, $factarray;
		global $PGV_IMAGE_DIR, $PGV_IMAGES;
		global $lang_short_cut, $LANGUAGE;
		$elderdate = "";
		//-- new father/husband
		$styleadd = "";
		if (isset($people["newhusb"])) {
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_labelblue"><?php print $people["newhusb"]->getLabel(); ?></td>
				<td class="<?php print $this->getPersonStyle($people["newhusb"]); ?>">
					<?php print_pedigree_person($people["newhusb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			$elderdate = $people["newhusb"]->getBirthDate();
		}
		//-- father/husband
		if (isset($people["husb"])) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $people["husb"]->getLabel(); ?></td>
				<td class="<?php print $this->getPersonStyle($people["husb"]); ?>">
					<?php print_pedigree_person($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			$elderdate = $people["husb"]->getBirthDate();
		}
		//-- missing father
		if ($type=="parents" && !isset($people["husb"]) && !isset($people["newhusb"])) {
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
				?>
				<tr>
					<td class="facts_label"><?php print $pgv_lang["add_father"]; ?></td>
					<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?> <a href="javascript <?php print $pgv_lang["add_father"]; ?>" onclick="return addnewparentfamily('<?php print $this->pid; ?>', 'HUSB', '<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_father"]; ?></a></td>
				</tr>
				<?php
			}
		}
		//-- missing husband
		if ($type=="spouse" && $this->indi->equals($people["wife"]) && !isset($people["husb"]) && !isset($people["newhusb"])) {
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
				?>
				<tr>
					<td class="facts_label"><?php print $pgv_lang["add_husb"]; ?></td>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php print $family->getXref(); ?>', 'HUSB');"><?php print $pgv_lang["add_husb_to_family"]; ?></a></td>
				</tr>
				<?php
			}
		}
		//-- new mother/wife
		$styleadd = "";
		if (isset($people["newwife"])) {
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_labelblue"><?php print $people["newwife"]->getLabel($elderdate); ?></td>
				<td class="<?php print $this->getPersonStyle($people["newwife"]); ?>">
					<?php print_pedigree_person($people["newwife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		//-- mother/wife
		if (isset($people["wife"])) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $people["wife"]->getLabel($elderdate); ?></td>
				<td class="<?php print $this->getPersonStyle($people["wife"]); ?>">
					<?php print_pedigree_person($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		//-- missing mother
		if ($type=="parents" && !isset($people["wife"]) && !isset($people["newwife"])) {
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
				?>
				<tr>
					<td class="facts_label"><?php print $pgv_lang["add_mother"]; ?></td>
					<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?> <a href="javascript:;" onclick="return addnewparentfamily('<?php print $this->pid; ?>', 'WIFE', '<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_mother"]; ?></a></td>
				</tr>
				<?php
			}
		}
		//-- missing wife
		if ($type=="spouse" && $this->indi->equals($people["husb"]) && !isset($people["wife"]) && !isset($people["newwife"])) {
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
				?>
				<tr>
					<td class="facts_label"><?php print $pgv_lang["add_wife"]; ?></td>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php print $family->getXref(); ?>', 'WIFE');"><?php print $pgv_lang["add_wife_to_family"]; ?></a></td>
				</tr>
				<?php
			}
		}
		//-- marriage row
		if ($family->getMarriageRecord()!="" || PGV_USER_CAN_EDIT) {
			$styleadd = "";
			$date = $family->getMarriageDate();
			$place = $family->getMarriagePlace();
			$famid = $family->getXref();
			if (!$date && $this->show_changes && isset($pgv_changes[$famid."_".$GEDCOM])) {
				$famrec = find_updated_record($famid);
				$marrrec = get_sub_record(1, "1 MARR", $famrec);
				if ($marrrec!=$family->getMarriageRecord()) {
					$date = new GedcomDate(get_gedcom_value("MARR:DATE", 1, $marrrec, '', false));
					$place = get_gedcom_value("MARR:PLAC", 1, $marrrec, '', false);
					$styleadd = "blue";
				}
			}
			?>
			<tr>
				<td class="facts_label"><br />
				</td>
				<td class="facts_value<?php print $styleadd ?>">
					<?php //echo "<span class=\"details_label\">".$factarray["NCHI"].": </span>".$family->getNumberOfChildren()."<br />";?>
					<?php if ($date && $date->isOK() || $place) {
						$marr_type = "MARR_".strtoupper($family->getMarriageType());
						if (isset($factarray[$marr_type])) echo "<span class=\"details_label\">".$factarray[$marr_type].": </span>";
						else echo "<span class=\"details_label\">".$factarray["MARR"].": </span>".$family->getMarriageType();
						if ($date) {
							echo $date->Display(false);
							if (!empty($place)) echo ' -- ';
						}
						if (!empty($place)) echo $place;
					}
					else if (get_sub_record(1, "1 _NMR", find_family_record($famid))) {
						// Allow special processing for different languages
						$func="fact_NMR_localisation_{$lang_short_cut[$LANGUAGE]}";
						if (function_exists($func)) {
							// Localise the _NMR facts
							$func("_NMR", $famid);
						}
						echo $factarray["_NMR"];
					}
					else if (get_sub_record(1, "1 _NMAR", find_family_record($famid))) {
						// Allow special processing for different languages
						$func="fact_NMR_localisation_{$lang_short_cut[$LANGUAGE]}";
						if (function_exists($func)) {
							// Localise the _NMR facts
							$func("_NMAR", $famid);
						}
						echo $factarray["_NMAR"];
					}
					else if ($family->getMarriageRecord()=="" && PGV_USER_CAN_EDIT) {
						print "<a href=\"#\" onclick=\"return add_new_record('".$famid."', 'MARR');\">".$pgv_lang['add_marriage']."</a>";
					}
					else {
						$factdetail = explode(' ', trim($family->getMarriageRecord()));
						if ($family->getMarriageType())
							$marr_type = "MARR_".strtoupper($family->getMarriageType());
						else
							$marr_type = "MARR";
						if (isset($factarray[$marr_type])) {
							if (isset($factdetail))
								if (count($factdetail) == 3)
									if (strtoupper($factdetail[2]) == "Y")
										echo "<span class=\"details_label\">".$factarray[$marr_type].": </span>".$pgv_lang["yes"];
									else if (strtoupper($factdetail[2]) == "N")
										echo "<span class=\"details_label\">".$factarray[$marr_type].": </span>".$pgv_lang["no"];
						}
						else echo "<span class=\"details_label\">".$factarray["MARR"].": </span>".$family->getMarriageType();
					}
					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* print children informations
	* @param Family family
	* @param Array people
	* @param String family type
	* @return html table rows
	*/
	function printChildrenRows(&$family, &$people, $type) {
		global $personcount, $pgv_lang, $factarray;
		global $PGV_IMAGE_DIR, $PGV_IMAGES;
		$elderdate = $family->getMarriageDate();
		foreach($people["children"] as $key=>$child) {
			$label = $child->getLabel();
			if ($label[0]=='+')
				$styleadd = "blue";
			else if ($label[0]=='-')
				$styleadd = "red";
			else
				$styleadd = "";
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php if ($styleadd=="red") print $child->getLabel(); else print $child->getLabel($elderdate, $key+1); ?></td>
				<td class="<?php print $this->getPersonStyle($child); ?>">
				<?php
				print_pedigree_person($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				?>
				</td>
			</tr>
			<?php
			$elderdate = $child->getBirthDate();
		}
		if (isset($family) && !$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
			if ($type == "spouse") {
				$action = "add_son_daughter";
				$child_m = "son";
				$child_f = "daughter";
			}
			else {
				$action = "add_sibling";
				$child_m = "brother";
				$child_f = "sister";
			}
		?>
			<tr>
				<td class="facts_label">
					<?php if (PGV_USER_CAN_EDIT && isset($people["children"][1])) {?>
					<a href="javascript:;" onclick="reorder_children('<?php print $family->getXref(); ?>');tabswitch(5);"><img src="images/topdown.gif" alt="" border="0" /> <?php print $pgv_lang['reorder_children']; ?></a>
					<?php }?>
				</td>
				<td class="facts_value"><?php print_help_link($action."_help", "qm"); ?>
					<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>');"><?php print $pgv_lang[$action]; ?></a>
					<span style='white-space:nowrap;'>
						<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>','M');"><?php echo Person::sexImage('M', 'small', '', $pgv_lang[$child_m]); ?></a>
						<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>','F');"><?php echo Person::sexImage('F', 'small', '', $pgv_lang[$child_f]); ?></a>
					</span>
				</td>
			</tr>
			<?php
		}
	}

	function getTab($tab) {

// LB Fix for no googlemaps ==========================================================================

		if (file_exists("modules/googlemap/defaultconfig.php")) {
			$tab_array = array("facts","notes","sources","media","relatives","tree","research","map","lightbox","spare","nav");
		}else{
			$tab_array = array("facts","notes","sources","media","relatives","tree","research","lightbox","spare","nav");
		}
		$tabType = $tab_array[$tab];

// ================================================================================================

		switch($tabType) {
			case "facts":
				$this->print_facts_tab();
				break;
			case "notes":
				$this->print_notes_tab();
				break;
			case "sources":
				$this->print_sources_tab();
				break;
			case "media":
				$this->print_media_tab();
				break;
			case "relatives":
				$this->print_relatives_tab();
				break;
			case "research":
				$this->print_research_tab();
				break;
			case "map":
				$this->print_map_tab();
				break;
			case "lightbox":
				$this->print_lightbox_tab();
				break;
			case "tree":
				$this->print_tree_tab();
				break;
			case "spare":
				$this->print_spare_tab();
				break;
			case "nav":
				$this->print_navigator_tab();
				break;
			default:
				print "No tab found";
				break;
		}
	}

	function print_facts_tab() {
		global $FACT_COUNT, $CONTACT_EMAIL, $PGV_IMAGE_DIR, $PGV_IMAGES, $pgv_lang, $EXPAND_RELATIVES_EVENTS;
		global $n_chil, $n_gchi, $n_ggch;
		global $EXPAND_RELATIVES_EVENTS, $LANGUAGE, $lang_short_cut;
		global $Fam_Navigator, $NAV_FACTS;

		/*if (isset($_COOKIE['row_rela'])) $EXPAND_RELATIVES_EVENTS = ($_COOKIE['row_rela']);
		if (isset($_COOKIE['row_histo'])) $EXPAND_HISTO_EVENTS = ($_COOKIE['row_histo']);
		else*/ $EXPAND_HISTO_EVENTS = false;

		//-- only need to add family facts on this tab
		$this->indi->add_family_facts();

		// Use Show or Hide Navigator Cookie -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide/Show the Family Navigator on this tab =========
		if (isset($NAV_FACTS) && $NAV_FACTS=="SHOW" ) {
			$Fam_Navigator="YES";
		}else{
			$Fam_Navigator="HIDE";
		}
		// ===============================================

		if ($Fam_Navigator=="YES") {
			print "<table cellpadding=\"0\" ><tr><td valign=\"top\" width=\"100%\" >";
		}

		?>
		<table class="facts_table" style="margin-top:-2px;" cellpadding="0">
		<?php if (!$this->indi->canDisplayDetails()) {
			print "<tr><td class=\"facts_value\" colspan=\"2\">";
			print_privacy_error($CONTACT_EMAIL);
			print "</td></tr>";
		}
		else {
			$indifacts = $this->getIndiFacts();
			if (count($indifacts)==0) {?>
				<tr>
					<td id="no_tab1" colspan="2" class="facts_value"><?php echo $pgv_lang["no_tab1"]?>
					</td>
				</tr>
			<?php }?>
			<tr id="row_top">
				<td valign="top"></td>
				<td class="descriptionbox rela">
					<input id="checkbox_rela" type="checkbox" <?php if ($EXPAND_RELATIVES_EVENTS) echo " checked=\"checked\""?> onclick="toggleByClassName('TR', 'row_rela');" />
					<label for="checkbox_rela"><?php echo $pgv_lang["relatives_events"]?></label>
					<?php if (file_exists("languages/histo.".$lang_short_cut[$LANGUAGE].".php")) {?>
						<input id="checkbox_histo" type="checkbox" <?php if ($EXPAND_HISTO_EVENTS) echo " checked=\"checked\""?> onclick="toggleByClassName('TR', 'row_histo');" />
						<label for="checkbox_histo"><?php echo $pgv_lang["historical_facts"]?></label>
					<?php }?>
				</td>

			</tr>
			<?php
			$yetdied=false;
			$n_chil=1;
			$n_gchi=1;
			$n_ggch=1;
			foreach ($indifacts as $key => $value) {
				if ($value->getTag() == "DEAT") $yetdied = true;
				if ($value->getTag() == "CREM") $yetdied = true;
				if ($value->getTag() == "BURI") $yetdied = true;

				if (!is_null($value->getFamilyId())) {
					if (!$yetdied) {
						print_fact($value);
					}
				}
				else print_fact($value);
				$FACT_COUNT++;
			}
		}
		//-- new fact link
		if ((!$this->isPrintPreview()) && PGV_USER_CAN_EDIT && ($this->indi->canDisplayDetails())) {
			print_add_new_fact($this->pid, $indifacts, "INDI");
		}
		?>
		</table>
		<?php
		// ==================== Start Details Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_details"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
				</td></tr></table>
			</td></tr></table>
			<?php
		}
		// ==================== End Details Tab Navigator ========================================= */
		?>
		<br />
		<script language="JavaScript" type="text/javascript">
		<!--
		<?php
		if (!$EXPAND_RELATIVES_EVENTS) print "toggleByClassName('TR', 'row_rela');\n";
		if (!$EXPAND_HISTO_EVENTS) print "toggleByClassName('TR', 'row_histo');\n";
		?>
		//-->
		</script>
		<?php
	}

	function get_note_count() {
		$ct = preg_match_all("/\d NOTE /", $this->indi->gedrec, $match, PREG_SET_ORDER);
		foreach ($this->indi->getSpouseFamilies() as $k => $sfam)
			$ct += preg_match("/\d NOTE /", $sfam->getGedcomRecord());
		return $ct;
	}

	function print_notes_tab() {
		global $pgv_lang, $factarray, $CONTACT_EMAIL, $FACT_COUNT;
		global $SHOW_LEVEL2_NOTES;
		global $Fam_Navigator, $NAV_NOTES;

		//if (isset($_COOKIE['row_note2'])) $SHOW_LEVEL2_NOTES = ($_COOKIE['row_note2']);

		// Use Show or Hide Navigator Cookie -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide/Show the Family Navigator on this tab =========
		if (isset($NAV_NOTES) && $NAV_NOTES=="SHOW" ) {
			$Fam_Navigator="YES";
		}else{
			$Fam_Navigator="HIDE";
		}
		// ===============================================

		if ($Fam_Navigator=="YES") {
			print "<table cellpadding=\"0\" ><tr><td valign=\"top\" width=\"100%\" >";
		}
		?>
		<table class="facts_table" >
		<?php
		if (!$this->indi->canDisplayDetails()) {
			print "<tr><td class=\"facts_value\">";
			print_privacy_error($CONTACT_EMAIL);
			print "</td></tr>";
		} else {
		?>
			<tr>
				<td></td>
				<td class="descriptionbox rela">
					<input id="checkbox_note2" type="checkbox" <?php if ($SHOW_LEVEL2_NOTES) echo " checked=\"checked\""?> onclick="toggleByClassName('TR', 'row_note2');" />
					<label for="checkbox_note2"><?php echo $pgv_lang["show_fact_notes"];?></label>
					<?php print_help_link("show_fact_sources_help", "qm", "show_fact_notes");?>
				</td>
			</tr>
			<?php
			$otherfacts = $this->getOtherFacts();
			foreach ($otherfacts as $key => $event) {
				$fact = $event->getTag();
				if ($fact=="NOTE") {
					print_main_notes($event->getGedcomRecord(), 1, $this->pid, $event->getLineNumber());
				}
				$FACT_COUNT++;
			}
			// 2nd to 5th level notes/sources
			$this->indi->add_family_facts(false);
			foreach ($this->getIndiFacts() as $key => $factrec) {
				for ($i=2; $i<6; $i++) {
					print_main_notes($factrec->getGedcomRecord(), $i, $this->pid, $factrec->getLineNumber(), true);
				}
			}
			if ($this->get_note_count()==0) print "<tr><td id=\"no_tab2\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab2"]."</td></tr>\n";
			//-- New Note Link
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
			?>
				<tr>
					<td class="facts_label"><?php print_help_link("add_note_help", "qm"); ?><?php echo $pgv_lang["add_note_lbl"]; ?></td>
					<td class="facts_value"><a href="javascript:;" onclick="add_new_record('<?php echo $this->pid; ?>','NOTE'); return false;"><?php echo $pgv_lang["add_note"]; ?></a>
					<br />
					</td>
				</tr>
				<tr>
					<td class="facts_label"><?php print_help_link("add_shared_note_help", "qm"); ?><?php echo $pgv_lang["add_shared_note_lbl"]; ?></td>
					<td class="facts_value">
					<a href="javascript:;" onclick="add_new_record('<?php echo $this->pid; ?>','SHARED_NOTE'); return false;"><?php echo $pgv_lang["add_shared_note"]; ?></a>
					<br />
					</td>
				</tr>
			<?php
			}
		}
		?>
		</table>
		<br />

		<?php
		// ==================== Start Notes Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_notes"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
				</td></tr></table>
			</td></tr></table>
			<?php
		}
		// ==================== End Notes Tab Navigator =========================================
		?>

		<?php
		if (!$SHOW_LEVEL2_NOTES) {
		?>
			<script language="JavaScript" type="text/javascript">
			<!--
			toggleByClassName('TR', 'row_note2');
			//-->
			</script>
		<?php
		}
	}

	function get_source_count() {
		$ct = preg_match_all("/\d SOUR @(.*)@/", $this->indi->gedrec, $match, PREG_SET_ORDER);
		foreach ($this->indi->getSpouseFamilies() as $k => $sfam)
			$ct += preg_match("/\d SOUR /", $sfam->getGedcomRecord());
		return $ct;
	}

	function print_sources_tab() {
		global $CONTACT_EMAIL, $pgv_lang, $FACT_COUNT;
		global $SHOW_LEVEL2_NOTES;
		global $Fam_Navigator, $NAV_SOURCES;

		/*if (isset($_COOKIE['row_sour2'])) $SHOW_LEVEL2_SOURCES = ($_COOKIE['row_sour2']);
		else*/ $SHOW_LEVEL2_SOURCES = $SHOW_LEVEL2_NOTES;

		// Use Show or Hide Navigator Cookie -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide/Show the Family Navigator on this tab =========
		if (isset($NAV_SOURCES) && $NAV_SOURCES=="SHOW" ) {
			$Fam_Navigator="YES";
		}else{
			$Fam_Navigator="HIDE";
		}
		// ===============================================

		if ($Fam_Navigator=="YES") {
			print "<table cellpadding=\"0\" ><tr><td valign=\"top\" width=\"100%\" >";
		}
		?>
		<table class="facts_table">
		<?php
		if (!$this->indi->canDisplayDetails()) {
			print "<tr><td class=\"facts_value\">";
			print_privacy_error($CONTACT_EMAIL);
			print "</td></tr>";
		} else {
		?>
			<tr>
				<td></td>
				<td class="descriptionbox rela">
					<input id="checkbox_sour2" type="checkbox" <?php if ($SHOW_LEVEL2_SOURCES) echo " checked=\"checked\""?> onclick="toggleByClassName('TR', 'row_sour2');" />
					<label for="checkbox_sour2"><?php echo $pgv_lang["show_fact_sources"];?></label>
					<?php print_help_link("show_fact_sources_help", "qm", "show_fact_sources");?>
				</td>
			</tr>
			<?php
			$otheritems = $this->getOtherFacts();
				foreach ($otheritems as $key => $event) {
					if ($event->getTag()=="SOUR") print_main_sources($event->getGedcomRecord(), 1, $this->pid, $event->getLineNumber());
				$FACT_COUNT++;
			}
		}
			// 2nd level sources [ 1712181 ]
			$this->indi->add_family_facts(false);
			foreach ($this->getIndiFacts() as $key => $factrec) {
					print_main_sources($factrec->getGedcomRecord(), 2, $this->pid, $factrec->getLineNumber(), true);
			}
			if ($this->get_source_count()==0) print "<tr><td id=\"no_tab3\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab3"]."</td></tr>\n";
			//-- New Source Link
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
			?>
				<tr>
					<td class="facts_label"><?php print_help_link("add_source_help", "qm"); ?><?php echo $pgv_lang["add_source_lbl"]; ?></td>
					<td class="facts_value">
					<a href="javascript:;" onclick="add_new_record('<?php echo $this->pid; ?>','SOUR'); return false;"><?php echo $pgv_lang["add_source"]; ?></a>
					<br />
					</td>
				</tr>
			<?php
			}
		?>
		</table>
		<br />

		<?php
		// ==================== Start Sources Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_sources"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
				</td></tr></table>
			</td></tr></table>
		<?php
		}
		// ==================== End Sources Tab Navigator =========================================
		?>

		<?php
		if (!$SHOW_LEVEL2_SOURCES) {
		?>
			<script language="JavaScript" type="text/javascript">
			<!--
			toggleByClassName('TR', 'row_sour2');
			//-->
			</script>


		<?php
		}
	}

	/**
	* get the number of media items for this person
	* @return int
	*/
	function get_media_count() {
		$ct = preg_match("/\d OBJE/", $this->indi->getGedcomRecord());
		foreach ($this->indi->getSpouseFamilies() as $k=>$sfam)
			$ct += preg_match("/\d OBJE/", $sfam->getGedcomRecord());
		return $ct;
	}

	/**
	* print the media tab
	*/
	function print_media_tab() {
		global $CONTACT_EMAIL, $pgv_lang, $MULTI_MEDIA;
		global $Fam_Navigator, $NAV_MEDIA;

		// Use Show or Hide Navigator Cookie -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide/Show the Family Navigator on this tab =========
		if (isset($NAV_MEDIA) && $NAV_MEDIA=="SHOW" ) {
			$Fam_Navigator="YES";
		}else{
			$Fam_Navigator="HIDE";
		}
		// ===============================================

		if ($Fam_Navigator=="YES") {
			print "<table cellpadding=\"0\" ><tr><td valign=\"top\" width=\"100%\" >";
		}
		?>
		<table class="facts_table">
		<?php
		$media_found = false;
		if (!$this->indi->canDisplayDetails()) {
			print "<tr><td class=\"facts_value\">";
			print_privacy_error($CONTACT_EMAIL);
			print "</td></tr>";
		}
		else {
			$media_found = print_main_media($this->pid, 0, true);
			if (!$media_found) print "<tr><td id=\"no_tab4\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab4"]."</td></tr>\n";
			//-- New Media link
			if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
		?>
				<tr>
					<td class="facts_label"><?php print_help_link("add_media_help", "qm"); ?><?php print $pgv_lang["add_media_lbl"]; ?></td>
					<td class="facts_value">
						<a href="javascript:;" onclick="window.open('addmedia.php?action=showmediaform&linktoid=<?php print $this->pid; ?>', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;"> <?php echo $pgv_lang["add_media"]; ?></a><br />
						<a href="javascript:;" onclick="window.open('inverselink.php?linktoid=<?php print $this->pid; ?>&linkto=person', '_blank', 'top=50,left=50,width=400,height=300,resizable=1,scrollbars=1'); return false;"><?php echo $pgv_lang["link_to_existing_media"]; ?></a>
					</td>
				</tr>
			<?php
			}
		}
		?>
		</table>
		<?php
		// ==================== Start Media Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_media"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
				</td></tr></table>
			</td></tr></table>
			<?php
		}
		// ==================== End Media Tab Navigator =========================================
		?>

		<?php
	}

	function print_relatives_tab() {
		global $pgv_lang, $factarray, $SHOW_ID_NUMBERS, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_AGE_DIFF;
		global $pgv_changes, $GEDCOM, $ABBREVIATE_CHART_LABELS;
		global $show_full, $personcount;

		if (isset($show_full)) $saved_show_full = $show_full; // We always want to see full details here
		$show_full = 1;

		$saved_ABBREVIATE_CHART_LABELS = $ABBREVIATE_CHART_LABELS;
		$ABBREVIATE_CHART_LABELS = false; // Override GEDCOM configuration

		// Show or Hide Navigator -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		if ($Fam_Navigator=="HIDE") {
			print "<table border=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td valign=\"top\" width=\"100%\" >";
		}
		//if (isset($_COOKIE['elderdate'])) $SHOW_AGE_DIFF = ($_COOKIE['elderdate']);
		if (!$this->isPrintPreview()) {
		?>
		<table class="facts_table"><tr><td style="width:20%; padding:4px"></td><td class="descriptionbox rela">
		<input id="checkbox_elder" type="checkbox" onclick="toggleByClassName('DIV', 'elderdate');" <?php if ($SHOW_AGE_DIFF) echo "checked=\"checked\"";?>/>
		<label for="checkbox_elder"><?php print_help_link("age_differences_help", "qm"); print $pgv_lang['age_differences'] ?></label>
		</td></tr></table>
		<?php
		}
		$personcount=0;
		$families = $this->indi->getChildFamilies();
		if (count($families)==0) {
			print "<span class=\"subheaders\">".$pgv_lang["relatives"]."</span>";
			if (/**!$this->isPrintPreview() &&**/ PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
				?>
				<table class="facts_table">
					<tr>
						<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?><a href="javascript:;" onclick="return addnewparent('<?php print $this->pid; ?>', 'HUSB');"><?php print $pgv_lang["add_father"]; ?></a></td>
					</tr>
					<tr>
						<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?><a href="javascript:;" onclick="return addnewparent('<?php print $this->pid; ?>', 'WIFE');"><?php print $pgv_lang["add_mother"]; ?></a></td>
					</tr>
				</table>
				<?php
			}
		}
		//-- parent families
		foreach($families as $famid=>$family) {
			$people = $this->buildFamilyList($family, "parents");
			$this->printFamilyHeader($famid, $this->indi->getChildFamilyLabel($family));
			?>
			<table class="facts_table">
				<?php
				$this->printParentsRows($family, $people, "parents");
				$this->printChildrenRows($family, $people, "parents");
				?>
			</table>
		<?php
		}

		//-- step families
		foreach($this->indi->getStepFamilies() as $famid=>$family) {
			$people = $this->buildFamilyList($family, "step");
			$this->printFamilyHeader($famid, $this->indi->getStepFamilyLabel($family));
			?>
			<table class="facts_table">
				<?php
				$this->printParentsRows($family, $people, "step");
				$this->printChildrenRows($family, $people, "step");
				?>
			</table>
		<?php
		}

		//-- spouses and children
		$families = $this->indi->getSpouseFamilies();
		foreach($families as $famid=>$family) {
			$people = $this->buildFamilyList($family, "spouse");
			$this->printFamilyHeader($famid, $this->indi->getSpouseFamilyLabel($family));
			?>
			<table class="facts_table">
				<?php
				$this->printParentsRows($family, $people, "spouse");
				$this->printChildrenRows($family, $people, "spouse");
				?>
			</table>
		<?php
		}

		// ==================== Start Relatives Tab Navigator ========================================
		if ($Fam_Navigator=="HIDE") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
					<b><?php print $pgv_lang["view_fam_nav_relatives"]; ?></b><br /><br />
					<?php include_once('includes/family_nav.php'); ?>
					<br />
				</td></tr></table>
			</td></tr></table>
			<?php
		}
		// ==================== End Tree Tab Navigator =========================================

		if ($personcount==0) print "<table><tr><td id=\"no_tab5\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab5"]."</td></tr></table>\n";
		?>
		<script type="text/javascript">
		<!--
			<?php if (!$SHOW_AGE_DIFF) echo "toggleByClassName('DIV', 'elderdate');";?>
		//-->
		</script>
		<br />
		<?php
		if (!$this->isPrintPreview() && PGV_USER_CAN_EDIT && $this->indi->canDisplayDetails()) {
		?>
		<table class="facts_table">
		<?php if (count($families)>1) { ?>
			<tr>
				<td class="facts_value">
				<?php print_help_link("reorder_families_help", "qm"); ?>
				<a href="javascript:;" onclick="return reorder_families('<?php print $this->pid; ?>');"><?php print $pgv_lang["reorder_families"]; ?></a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td class="facts_value">
				<?php print_help_link("link_child_help", "qm"); ?>
				<a href="javascript:;" onclick="return add_famc('<?php print $this->pid; ?>');"><?php print $pgv_lang["link_as_child"]; ?></a>
				</td>
			</tr>
			<?php if ($this->indi->getSex()!="F") { ?>
			<tr>
				<td class="facts_value">
				<?php print_help_link("add_wife_help", "qm"); ?>
				<a href="javascript:;" onclick="return addspouse('<?php print $this->pid; ?>','WIFE');"><?php print $pgv_lang["add_new_wife"]; ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<?php print_help_link("link_new_wife_help", "qm"); ?>
				<a href="javascript:;" onclick="return linkspouse('<?php print $this->pid; ?>','WIFE');"><?php print $pgv_lang["link_new_wife"]; ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<?php print_help_link("link_new_husb_help", "qm"); ?>
				<a href="javascript:;" onclick="return add_fams('<?php print $this->pid; ?>','HUSB');"><?php print $pgv_lang["link_as_husband"]; ?></a>
				</td>
			</tr>
			<?php }
			if ($this->indi->getSex()!="M") { ?>
			<tr>
				<td class="facts_value">
				<?php print_help_link("add_husband_help", "qm"); ?>
				<a href="javascript:;" onclick="return addspouse('<?php print $this->pid; ?>','HUSB');"><?php print $pgv_lang["add_new_husb"]; ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<?php print_help_link("link_husband_help", "qm"); ?>
				<a href="javascript:;" onclick="return linkspouse('<?php print $this->pid; ?>','HUSB');"><?php print $pgv_lang["link_new_husb"]; ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<?php print_help_link("link_wife_help", "qm"); ?>
				<a href="javascript:;" onclick="return add_fams('<?php print $this->pid; ?>','WIFE');"><?php print $pgv_lang["link_as_wife"]; ?></a>
				</td>
			</tr>
			<?php } ?>
<?php if (PGV_USER_CAN_ACCEPT) { // NOTE this function is restricted to ACCEPTORS because another bug prevents pending changes being shown on the close relatives tab of the indi page. Once that bug is fixed, this function can be opened up to all! ?>
			<tr>
				<td class="facts_value">
				<?php print_help_link("add_opf_child_help", "qm"); ?>
				<a href="javascript:;" onclick="return addopfchild('<?php print $this->pid; ?>','U');"><?php print $pgv_lang["add_opf_child"]; ?></a>
				</td>
			</tr>
<?php } ?>
			<?php if (PGV_USER_GEDCOM_ADMIN) { ?>
			<tr>
				<td class="facts_value">
				<?php print_help_link("link_remote_help", "qm"); ?>
				<a href="javascript:;" onclick="return open_link_remote('<?php print $this->pid; ?>');"><?php print $pgv_lang["link_remote"]; ?></a>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>
		<br />
		<?php

		$ABBREVIATE_CHART_LABELS = $saved_ABBREVIATE_CHART_LABELS; // Restore GEDCOM configuration
		unset($show_full);
		if (isset($saved_show_full)) $show_full = $saved_show_full;
	}


	function print_research_tab() {
		global $pgv_lang, $SHOW_RESEARCH_ASSISTANT, $CONTACT_EMAIL, $GEDCOM, $INDEX_DIRECTORY, $factarray, $templefacts, $nondatefacts, $nonplacfacts;
		global $LANGUAGE, $lang_short_cut;
		if (file_exists("modules/research_assistant/research_assistant.php") && ($SHOW_RESEARCH_ASSISTANT>=PGV_USER_ACCESS_LEVEL)) {
			if (!$this->indi->canDisplayDetails()) { ?>
				<table class="facts_table">
			<tr><td class="facts_value">
			<?php print_privacy_error($CONTACT_EMAIL); ?>
			</td></tr>
			</table>
			<br />
			<?php
			} else {
				include_once 'modules/research_assistant/research_assistant.php';
				$mod = new ra_functions();
				$mod->init();
				$out = $mod->tab($this->indi);
				print $out;
			}
		}
		else print "<table class=\"facts_table\"><tr><td id=\"no_tab6\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab6"]."</td></tr></table>\n";
	}

	function print_map_tab() {
		global $SEARCH_SPIDER, $SESSION_HIDE_GOOGLEMAP, $pgv_lang, $CONTACT_EMAIL, $PGV_IMAGE_DIR, $PGV_IMAGES;
		global $LANGUAGE;
		global $GOOGLEMAP_API_KEY, $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM, $GEDCOM;
		global $GOOGLEMAP_XSIZE, $GOOGLEMAP_YSIZE, $pgv_lang, $factarray, $SHOW_LIVING_NAMES, $PRIV_PUBLIC;
		global $GOOGLEMAP_ENABLED, $TEXT_DIRECTION, $GM_DEFAULT_TOP_VALUE, $GOOGLEMAP_COORD;
		global $GM_MARKER_COLOR, $GM_MARKER_SIZE, $GM_PREFIX, $GM_POSTFIX, $GM_PRE_POST_MODE;

		// Use Show or Hide Navigator Cookie -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide the Family Navigator on this tab ==============
		$Fam_Navigator="HIDE";
		// ===============================================

		// LB Fix if no googlemaps ========================================================
		if (file_exists("modules/googlemap/googlemap.php")) {
			include_once('modules/googlemap/googlemap.php');
		}
		// LB Fix in no googlemaps ========================================================

		if ($GOOGLEMAP_ENABLED == "false") {
			print "<table class=\"facts_table\">\n";
			print "<tr><td colspan=\"2\" class=\"facts_value\">".$pgv_lang["gm_disabled"]."</td></tr>\n";
			if (PGV_USER_IS_ADMIN) {
				print "<tr><td align=\"center\" colspan=\"2\">\n";
				print "<a href=\"".encode_url("module.php?mod=googlemap&pgvaction=editconfig")."\">".$pgv_lang["gm_manage"]."</a>";
				print "</td></tr>\n";
			}
			print "\n\t</table>\n<br />";
				?>
					<script language="JavaScript" type="text/javascript">
					<!--
						tabstyles[5]='tab_cell_inactive_empty';
						document.getElementById('pagetab5').className='tab_cell_inactive_empty';
						document.getElementById("googlemap_left").innerHTML = document.getElementById("googlemap_content").innerHTML;
						document.getElementById("googlemap_content").innerHTML = "";
						function ResizeMap () {}
						function SetMarkersAndBounds () {}
					//-->
					</script>
				<?php
			return;
		} else {
			$famids = array();
			$families = $this->indi->getSpouseFamilies();
			foreach($families as $famid=>$family) {
				$famids[] = $family->getXref();
			}
				$this->indi->add_family_facts(false);
				// LB Fix if no googlemaps ========================================================
				if (file_exists("modules/googlemap/googlemap.php")) {
					create_indiv_buttons();
					build_indiv_map($this->getIndiFacts(), $famids);
				}
				// LB Fix if no googlemaps ========================================================
		}
		// ==================== Start Map Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			<table id="map_nav" class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_map"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
			</td></tr></table>
			<?php
		}
		// ==================== End Map Tab Navigator =========================================
	}


	function print_tree_tab() {

		//-- nothing to do here
		//-- the tree is already ajax enabled

	/*
		global $pgv_lang, $pgv_changes, $factarray, $view;
		// Show or Hide Navigator -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// ==================== Start Tree Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			<table id="tree_nav" class="optionbox" width="220px" cellpadding=\"0\"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_tree"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
			</td></tr></table>
			<?php
		}
		// ==================== End Tree Tab Navigator =========================================
	*/

	}


	/** =================================================
	* print the lightbox tab, ( which = getTab8() )
	*/
	function print_lightbox_tab() {
		global $MULTI_MEDIA, $SHOW_ID_NUMBERS, $MEDIA_EXTERNAL;
		global $pgv_lang, $pgv_changes, $factarray, $view;
		global $GEDCOM, $MEDIATYPE, $pgv_changes;
		global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION, $is_media;
		global $cntm1, $cntm2, $cntm3, $cntm4, $t, $mgedrec ;
		global $edit ;
		global $CONTACT_EMAIL, $pid, $tabno;
		global $Fam_Navigator, $NAV_ALBUM;

		// Use Show or Hide Navigator Cookie -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide/Show the Family Navigator on this tab =========
		if (isset($NAV_ALBUM) && $NAV_ALBUM=="SHOW" ) {
			$Fam_Navigator="YES";
		}else{
			$Fam_Navigator="HIDE";
		}
		// ===============================================

		if ($Fam_Navigator=="YES") {
			print "<table cellpadding=\"0\" ><tr><td valign=\"top\" width=\"100%\" >";
		}

		$media_found = false;
		if (!$this->indi->canDisplayDetails()) {
			print "<table class=\"facts_table\" cellpadding=\"0\">\n";
			print "<tr><td class=\"facts_value\">";
			print_privacy_error($CONTACT_EMAIL);
			print "</td></tr>";
			print "</table>";
		}else{
			if (file_exists("modules/lightbox/album.php")) {
				include_once('modules/lightbox/album.php');
			}
		}

		// ==================== Start Album Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding="0"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_album"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
				</td></tr></table>
			</td></tr></table>
			<?php
		}
		// ==================== End Album Tab Navigator ========================================= */
	}

	/** =================================================
	* print the spare tab, ( which = getTab9() )
	*/
	function print_spare_tab() {
	/*
		global $MULTI_MEDIA, $SHOW_ID_NUMBERS, $MEDIA_EXTERNAL;
		global $pgv_lang, $pgv_changes, $factarray, $view;
		global $GEDCOM, $MEDIATYPE;
		global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $PGV_IMAGE_DIR, $PGV_IMAGES, $TEXT_DIRECTION, $is_media;
		global $mgedrec ;
		global $CONTACT_EMAIL, $pid, $tabno;
		global $Fam_Navigator, $NAV_SPARE;

		// Show or Hide Navigator -----------
		if (isset($_COOKIE['famnav'])) {
			$Fam_Navigator=$_COOKIE['famnav'];
		}else{
			$Fam_Navigator="YES";
		}
		// Hide/Show the Family Navigator on this tab =========
		if (isset($NAV_SPARE) && $NAV_SPARE=="SHOW" ) {
			$Fam_Navigator="YES";
		}else{
			$Fam_Navigator="HIDE";
		}
		// ===============================================
		if ($Fam_Navigator=="YES") {
			print "<table cellpadding=\"0\" ><tr><td valign=\"top\" width=\"100%\" >";
		}

		if (!$this->indi->canDisplayDetails()) {
			print "<table class=\"facts_table\" cellpadding=\"0\">\n";
			print "<tr><td class=\"facts_value\">";
			print_privacy_error($CONTACT_EMAIL);
			print "</td></tr>";
			print "</table>";
		}else{
			//if (file_exists("modules/lightbox/album.php")) {
				//include_once('modules/lightbox/album.php');
			print "<table class=\"facts_table\" cellpadding=\"0\">\n";
			print "<tr><td class=\"facts_value\">";
					echo "<h2><center>Spare Tab</center></h2>";
					echo "<center>This is where the Spare Tab info goes</center>";
					echo "<br />";
					echo "<center>You will also need to write your own help (Top left of this div. It shows the Lightbox help for now)</center>";
					echo "<br />";
					echo "<center>For the moment Lightbox MUST be installed (I'll fix this later if necessary)</center>";
					echo "<br />";
					echo "<br />";
			print "</td></tr>";
			print "</table>";
			//}
		}

		// ==================== Start Spare Tab Navigator ========================================
		if ($Fam_Navigator=="YES") {
			?>
			</td>
			<td valign="top">
				<table class="optionbox" width="220px" cellpadding=\"0\"><tr><td align="center">
				<b><?php print $pgv_lang["view_fam_nav_spare"]; ?></b><br /><br />
				<?php include_once('includes/family_nav.php'); ?>
				<br />
				</td></tr></table>
			</td></tr></table>
			<?php
		}
		// ==================== End Spare Tab Navigator =========================================
	*/
	}


// -----------------------------------------------------------------------------
// Functions for GedFact Assistant
// -----------------------------------------------------------------------------
	/**
	* include GedFact controller
	*/
	function census_assistant() {
		require 'modules/GEDFact_assistant/_CENS/census_1_ctrl.php';
	}
	function medialink_assistant() {
		require 'modules/GEDFact_assistant/_MEDIA/media_1_ctrl.php';
	}
// -----------------------------------------------------------------------------
// End GedFact Assistant Functions
// -----------------------------------------------------------------------------



}
// -- end of class

//-- load a user extended class if one exists
if (file_exists('includes/controllers/individual_ctrl_user.php'))
{
	include_once 'includes/controllers/individual_ctrl_user.php';
}
else
{
	class IndividualController extends IndividualControllerRoot
	{
	}
}

?>

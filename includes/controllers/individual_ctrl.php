<?php
/**
 * Controller for the Individual Page
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * Page does not validate see line number 1109 -> 15 August 2005
 *
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id$
 */
require_once("config.php");
require_once 'includes/functions_print_facts.php';
require_once 'includes/controllers/basecontrol.php';
require_once($factsfile["english"]);
if (file_exists($factsfile[$LANGUAGE])) require_once($factsfile[$LANGUAGE]);
require_once 'includes/menu.php';
require_once 'includes/person_class.php';

$indifacts = array();			 // -- array to store the fact records in for sorting and displaying
$globalfacts = array();
$otheritems = array();			  //-- notes, sources, media objects
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
$nonfacts[] = "";

$nonfamfacts[] = "NCHI";
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";
/**
 * Main controller class for the individual page.
 */
class IndividualControllerRoot extends BaseController {
	var $show_changes = "yes";
	var $action = "";
	var $pid = "";
	var $default_tab = 0;
	var $indi = null;
	var $diffindi = null;
	var $NAME_LINENUM = 1;
	var $uname = "";
	var $user = false;
	var $accept_success = false;
	var $visibility = "visible";
	var $position = "relative";
	var $display = "block";
	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $sexarray = array();

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
		global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $GEDCOM_DEFAULT_TAB, $pgv_changes, $pgv_lang;

		$this->sexarray["M"] = $pgv_lang["male"];
		$this->sexarray["F"] = $pgv_lang["female"];
		$this->sexarray["U"] = $pgv_lang["unknown"];

		if (!empty($_REQUEST["show_changes"])) $this->show_changes = $_REQUEST["show_changes"];
		if (!empty($_REQUEST["action"])) $this->action = $_REQUEST["action"];
		if (!empty($_REQUEST["pid"])) $this->pid = strtoupper($_REQUEST["pid"]);
		$this->pid = clean_input($this->pid);
		$this->default_tab = $GEDCOM_DEFAULT_TAB;
		$indirec = find_person_record($this->pid);
//		print_r($indirec);
		if (($USE_RIN)&&($indirec==false)) {
		   $this->pid = find_rin_id($this->pid);
		   $indirec = find_person_record($this->pid);
		}
		if (empty($indirec)) {
			$ct = preg_match("/(\w+):(.+)/", $this->pid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				$service = ServiceClient::getInstance($servid);
				//$indirec =
				$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$this->pid."@ INDI\r\n1 RFN ".$this->pid, false);

				//$newrec= $service->mergeGedcomRecord($remoteid, "");
				//print $newrec;
				$indirec = $newrec;
			}
			else {
				$indirec = "0 @".$this->pid."@ INDI\r\n";
			}
		}
		//-- check for the user
		$this->uname = getUserName();
		if (!empty($this->uname)) {
			$this->user = getUser($this->uname);
			if ($this->user["default_tab"] != $this->default_tab) $this->default_tab = $this->user["default_tab"];
		}
		
		//-- check for a cookie telling what the last tab was when they were last
		//-- visiting this individual
		if (isset($_COOKIE['lasttabs'])) {
			$ct = preg_match("/".$this->pid."=(\d+)/", $_COOKIE['lasttabs'], $match);
			if ($ct>0) {
				$this->default_tab = $match[1]-1;
//				print "{".$this->default_tab."}";
			} 
		}
		
		$this->indi = new Person($indirec, false);

		//-- if the person is from another gedcom then forward to the correct site
		/*
		if ($this->indi->isRemote()) {
			header('Location: '.preg_replace("/&amp;/", "&", $this->indi->getLinkUrl()));
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
		if ($this->show_changes=="yes" && userCanEdit($this->uname)) {
			if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
				 //-- get the changed record from the file
				$newrec = find_record_in_file($this->pid);
				//print("jkdsakjhdkjsadkjsakjdhsakd".$newrec);
				$remoterfn = get_gedcom_value("RFN", 1, $newrec);
			 }
			 else {
				$remoterfn = get_gedcom_value("RFN", 1, $indirec);
			 }
			// print "remoterfn=".$remoterfn;
			//-- get an updated record from the web service
			if (!empty($remoterfn)) {
				$parts = preg_split("/:/", $remoterfn);
				if (count($parts)==2) {
					$servid = $parts[0];
					$aliaid = $parts[1];
					if (!empty($servid)&&!empty($aliaid)) {
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

		if ($this->show_changes=="yes") {
			$this->indi->diffMerge($this->diffindi);
		}

		$this->indi->add_family_facts();
		//-- only allow editors or users who are editing their own individual or their immediate relatives
		if ($this->indi->canDisplayDetails()) {
			$this->canedit = userCanEdit($this->uname);
			if (!$this->canedit) {
				if (!empty($this->user["gedcomid"][$GEDCOM])) {
					if ($this->pid==$this->user["gedcomid"][$GEDCOM]) $this->canedit=true;
					else {
						$famids = array_merge(find_sfamily_ids($this->user["gedcomid"][$GEDCOM]), find_family_ids($this->user["gedcomid"][$GEDCOM]));
						foreach($famids as $indexval => $famid) {
							if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_gedcom_record($famid);
							else $famrec = find_record_in_file($famid);
							if (preg_match("/1 HUSB @$this->pid@/", $famrec)>0) $this->canedit=true;
							if (preg_match("/1 WIFE @$this->pid@/", $famrec)>0) $this->canedit=true;
							if (preg_match("/1 CHIL @$this->pid@/", $famrec)>0) $this->canedit=true;
						}
					}
				}
			}
		}
	}
	//-- end of init function
	/**
	 * Add a new favorite for the action user
	 */
	function addFavorite() {
		global $GEDCOM;
		if (empty($this->uname)) return;
		if (!empty($_REQUEST["gid"])) {
			$gid = strtoupper($_REQUEST["gid"]);
			$indirec = find_person_record($gid);
			if ($indirec) {
				$favorite = array();
				$favorite["username"] = $this->uname;
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
		if (!userCanAccept($this->uname)) return;
		require_once("includes/functions_import.php");
		if (accept_changes($this->pid."_".$GEDCOM)) {
			$this->show_changes="no";
			$this->accept_success=true;
			$indirec = find_record_in_file($this->pid);
			$this->indi = new Person($indirec);
		}
	}

	/**
	 * return the title of this page
	 * @return string	the title of the page to go in the <title> tags
	 */
	function getPageTitle() {
		global $pgv_lang, $GEDCOM;
		$name = $this->indi->getName();
		return $name." - ".$this->indi->getXref()." - ".$pgv_lang["indi_info"];
	}
	
	/**
	 * gets a string used for setting the value of a cookie using javascript
	 */
	function getCookieTabString() {
		$str = "";
		if (isset($_COOKIE['lasttabs'])) {
			$parts = preg_split("/:/", $_COOKIE['lasttabs']);
			foreach($parts as $i=>$val) {
				$inner = preg_split("/=/", $val);
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
		if ($SHOW_GEDCOM_RECORD && $this->indi->canDisplayDetails())
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
	 * @return string	HTML string for the <img> tag
	 */
	function getHighlightedObject() {
		global $USE_THUMBS_MAIN, $THUMBNAIL_WIDTH;
		if ($this->canShowHighlightedObject()) {
			$firstmediarec = $this->indi->findHighlightedMedia();
			if (!empty($firstmediarec)) {
				if ($USE_THUMBS_MAIN) {
					$filename = $firstmediarec["thumb"];
					$class = "thumbnail";
				} else {
					$filename = $firstmediarec["file"];
					$class = "image";
				}
				if (empty($filename)) {
					$filename = $firstmediarec["thumb"];
					$class = "thumbnail";
				}
				$isExternal = stristr($filename,"://");
				if ($isExternal && $class=="thumbnail") $class .= "\" width=\"".$THUMBNAIL_WIDTH;
				if (!empty($filename)) {
					$result = "";
					$imgsize = findImageSize($firstmediarec["file"]);
					$imgwidth = $imgsize[0]+40;
					$imgheight = $imgsize[1]+150;
					$result .= "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($firstmediarec["file"])."',$imgwidth, $imgheight);\">";

					$result .= "<img src=\"$filename\" align=\"left\" class=\"".$class."\" border=\"none\" alt=\"".$firstmediarec["file"]."\" />";
					if ($imgsize) {
						$result .= "</a>";
					}

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
	 * @param string $factrec	the raw gedcom record of the name to print
	 * @param int $linenum		the line number from the original INDI gedcom record where this name record started, used for editing
	 */
	function print_name_record($factrec, $linenum) {
		global $pgv_lang, $factarray, $NAME_REVERSE;
		if ((!showFact("NAME", $this->pid))||(!showFactDetails("NAME", $this->pid))) return false;
		$lines = split("\n", $factrec);
		$this->name_count++;
		print "<td valign=\"top\"";
		if (preg_match("/PGV_OLD/", $factrec)>0) print " class=\"namered\"";
		if (preg_match("/PGV_NEW/", $factrec)>0) print " class=\"nameblue\"";
		print ">";
		if ($this->name_count>1) print "\n\t\t<span class=\"label\">".$pgv_lang["aka"]." </span><br />\n";
		$ct = preg_match_all("/2 (SURN)|(GIVN) (.*)/", $factrec, $nmatch, PREG_SET_ORDER);
		if ($ct==0) {
			$nt = preg_match("/1 NAME (.*)/", $factrec, $nmatch);
			if ($nt>0){
				print "\n\t\t<span class=\"label\">".$pgv_lang["name"].": </span><br />";
				$name = trim($nmatch[1]);
				if ($NAME_REVERSE) $name = reverse_name($name);
				$name = preg_replace("'/,'", ",", $name);
	   			$name = preg_replace("'/'", " ", $name);
				// handle PAF extra NPFX [ 961860 ]
				$ct = preg_match("/2 NPFX (.*)/", $factrec, $match);
				if ($ct>0) {
					$npfx = trim($match[1]);
					if (strpos($name, $npfx)===false) $name = $npfx." ".$name;
				}
				print PrintReady($name)."<br />\n";
			}
		}
		$ct = preg_match_all("/\n2 (\w+) (.*)/", $factrec, $nmatch, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$fact = trim($nmatch[$i][1]);
			if (($fact!="SOUR")&&($fact!="NOTE")) {
				print "\n\t\t\t<span class=\"label\">";
				if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
				else if (isset($factarray[$fact])) print $factarray[$fact];
				else print $fact;
				print ":</span><span class=\"field\"> ";
				if (isset($nmatch[$i][2])) {
			  		$name = trim($nmatch[$i][2]);
			  		$name = preg_replace("'/,'", ",", $name);
					$name = preg_replace("'/'", " ", $name);
					print PrintReady(check_NN($name));
				}
				print " </span><br />";
			}
		}
		if ($this->total_names>1 && !$this->isPrintPreview() && $this->userCanEdit()) {
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
	 * @param string $factrec	the raw gedcom record to print
	 * @param int $linenum		the line number from the original INDI gedcom record where this sex record started, used for editing
	 */
	function print_sex_record($factrec, $linenum) {
	   global $pgv_lang, $sex, $PGV_IMAGE_DIR, $PGV_IMAGES;
	   if ((!showFact("SEX", $this->pid))||(!showFactDetails("SEX", $this->pid))) return false;
	   $ft = preg_match("/\d\s(\w+)(.*)/", $factrec, $match);
	   $sex = trim($match[2]);
	   if (empty($sex)) $sex = "U";
		print "<td valign=\"top\"><span class=\"label\">".$pgv_lang["sex"].":    </span><span class=\"field\">".$this->sexarray[$sex];
		print " <img src=\"$PGV_IMAGE_DIR/";
		if ($sex=="M") print $PGV_IMAGES["sex"]["small"]."\" title=\"".$pgv_lang["male"]."\" alt=\"".$pgv_lang["male"];
		else if ($sex=="F") print $PGV_IMAGES["sexf"]["small"]."\" title=\"".$pgv_lang["female"]."\" alt=\"".$pgv_lang["female"];
		else print $PGV_IMAGES["sexn"]["small"]."\" title=\"".$pgv_lang["unknown"]."\" alt=\"".$pgv_lang["unknown"];
		print "\" width=\"0\" height=\"0\" class=\"sex_image\" border=\"0\" />";
		if ($this->SEX_COUNT>1) {
			if ((!$this->isPrintPreview()) && ($this->userCanEdit()) && (preg_match("/PGV_OLD/", $factrec)==0)) {
			    if ($linenum=="new") print "<br /><a class=\"font9\" href=\"javascript:;\" onclick=\"add_new_record('".$this->pid."', 'SEX'); return false;\">".$pgv_lang["edit"]."</a>";
			    else {
			    	print "<br /><a class=\"font9\" href=\"javascript:;\" onclick=\"edit_record('".$this->pid."', $linenum); return false;\">".$pgv_lang["edit"]."</a> | ";
			    	print "<a class=\"font9\" href=\"javascript:;\" onclick=\"delete_record('".$this->pid."', ".$linenum."); return false;\">".$pgv_lang["delete"]."</a>\n";
			    }
			}
		}
		print "<br /></span>";
	   // -- find sources
	   print "&nbsp;&nbsp;&nbsp;";
	   print_fact_sources($factrec, 2);
	   //-- find the notes
	   print "&nbsp;&nbsp;&nbsp;";
	   print_fact_notes($factrec, 2);
	   print "</td>";
	}
	/**
	 * get the edit menu
	 * @return Menu
	 */
	function &getEditMenu() {
		global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $GEDCOM, $TOTAL_NAMES;
		global $NAME_LINENUM, $SEX_LINENUM, $pgv_lang, $pgv_changes, $USE_QUICK_UPDATE;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";
		//-- main edit menu
		$menu = new Menu($pgv_lang["edit"]);
		if ($USE_QUICK_UPDATE) $link = "return quickEdit('".$this->pid."');";
		else $link = "return edit_raw('".$this->pid."');";
		$menu->addOnclick($link);
		if (!empty($PGV_IMAGES["edit_indi"]["small"]))
			$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["edit_indi"]["small"]);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		//-- quickedit sub menu
		if ($USE_QUICK_UPDATE) {
			$submenu = new Menu($pgv_lang["quick_update_title"]);
			$submenu->addOnclick("return quickEdit('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if (userCanEdit($this->uname)) {
			$submenu = new Menu($pgv_lang["edit_raw"]);
			$submenu->addOnclick("return edit_raw('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
			if (count($this->indi->getSpouseFamilyIds())>0) {
				$submenu = new Menu($pgv_lang["reorder_families"]);
				$submenu->addOnclick("return reorder_families('".$this->pid."');");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
			$submenu = new Menu($pgv_lang["delete_person"]);
			$submenu->addOnclick("return deleteperson('".$this->pid."');");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
			$menu->addSeperator();
			if ($TOTAL_NAMES<2) {
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
				$submenu = new Menu($pgv_lang["edit"]." ".$pgv_lang["sex"]);
				if ($SEX_LINENUM=="new") $submenu->addOnclick("return add_new_record('".$this->pid."', 'SEX');");
				else $submenu->addOnclick("return edit_record('".$this->pid."', $SEX_LINENUM);");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}
		if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
			$menu->addSeperator();
			if ($this->show_changes=="no") {
				$label = $pgv_lang["show_changes"];
				$link = "individual.php?pid=".$this->pid."&amp;show_changes=yes";
			}
			else {
				$label = $pgv_lang["hide_changes"];
				$link = "individual.php?pid=".$this->pid."&amp;show_changes=no";
			}
			$submenu = new Menu($label, $link);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);

			if (userCanAccept($this->uname)) {
				$submenu = new Menu($pgv_lang["undo_all"], "individual.php?pid=".$this->pid."&amp;action=undo");
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu($pgv_lang["accept_all"], "individual.php?pid=".$this->pid."&amp;action=accept");
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
		if ($this->indi->canDisplayDetails() && ($SHOW_GEDCOM_RECORD || $ENABLE_CLIPPINGS_CART>=getUserAccessLevel()))
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
			if ($this->show_changes=="yes"  && userCanEdit($this->uname))
				$menu->addOnclick("return show_gedcom_record('new');");
			else
				$menu->addOnclick("return show_gedcom_record('');");
		}
		else {
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$menu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$menu->addLink("clippings.php?action=add&amp;id=".$this->pid."&amp;type=indi");
		}
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff");
		if ($this->canShowGedcomRecord()) {
			$submenu = new Menu($pgv_lang["view_gedcom"]);
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			if ($this->show_changes=="yes"  && userCanEdit($this->uname)) $submenu->addOnclick("return show_gedcom_record('new');");
			else $submenu->addOnclick("return show_gedcom_record();");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->indi->canDisplayDetails() && $ENABLE_CLIPPINGS_CART>=getUserAccessLevel()) {
			$submenu = new Menu($pgv_lang["add_to_cart"], "clippings.php?action=add&amp;id=".$this->pid."&amp;type=indi");
			if (!empty($PGV_IMAGES["clippings"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		if ($this->indi->canDisplayDetails() && !empty($this->uname)) {
			$submenu = new Menu($pgv_lang["add_to_my_favorites"], "individual.php?action=addfav&amp;pid=".$this->pid."&amp;gid=".$this->pid);
			if (!empty($PGV_IMAGES["gedcom"]["small"]))
				$submenu->addIcon($PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}
	/**
	 * get global facts
	 * global facts are NAME and SEX
	 * @return array	return the array of global facts
	 */
	function getGlobalFacts() {
		global $NAME_LINENUM, $SEX_LINENUM;

		$globalfacts = $this->indi->getGlobalFacts();
		foreach ($globalfacts as $key => $value) {
			$ft = preg_match("/\d\s(\w+)(.*)/", $value[1], $match);
			if ($ft>0) $fact = $match[1];
			else $fact="";
			$fact = trim($fact);
			if ($fact=="SEX") {
				$this->SEX_COUNT++;
				$SEX_LINENUM = $value[0];
			}
			if ($fact=="NAME") {
				$this->total_names++;
				$NAME_LINENUM = $value[0];
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
		//-- sort the facts
		//usort($indifacts, "compare_facts");
		sort_facts($indifacts);
		//-- remove duplicate facts
		foreach ($indifacts as $key => $value) $indifacts[$key] = serialize($value);
		$indifacts = array_unique($indifacts);
		foreach ($indifacts as $key => $value) $indifacts[$key] = unserialize($value);
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
	 * @return string	returns 'person_box', 'person_boxF', or 'person_boxNN'
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
	 * of family memebers on the close relatives tab
	 * @param Family $family	the family we are building for
	 * @return array			an array of Person that will be used to iterate through on the indivudal.php page
	 */
	function buildFamilyList(&$family, $type) {
		global $pgv_lang;
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
			$labels["parent"] = "";		//print $pgv_lang["stepparent"];
			$labels["mother"] = "";		//print $pgv_lang["stepmom"];
			$labels["father"] = "";		//print $pgv_lang["stepdad"];
			$labels["sibling"] = $pgv_lang["halfsibling"];
			$labels["sister"] = $pgv_lang["halfsister"];
			$labels["brother"] = $pgv_lang["halfbrother"];
		}
		if ($type=="spouse") {
			$div_rec = $family->getDivorceRecord();
			if (!empty($div_rec)) {
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
						$label = $type;
						if (isset($pgv_lang[$type])) $label = $pgv_lang[$type];
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
		//-- step families should only show the step parent
		if ($type=="step") {
			$fams = $this->indi->getChildFamilies();
			foreach($fams as $key=>$fam) {
				if ($fam->hasParent($husb)) $husb = null;
				if ($fam->hasParent($wife)) $wife = null;
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
			if ($husb->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
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
			if ($wife->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
			$wife->setLabel($label);
		}
		if ($this->show_changes=="yes") {
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
					if ($newhusb->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
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
					if ($newwife->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
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
				if ($children[$i]->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
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
			if ($newchildren[$i]->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
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
			if ($delchildren[$i]->getXref()==$this->pid) $label = "<img src=\"image/selected.png\" alt=\"\" />";
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
$controller = new IndividualController();
$controller->init();
?>

<?php
/**
 * Controller for the Hourglass Page
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
require_once 'includes/controllers/basecontrol.php';
require_once 'includes/person_class.php';
require_once("includes/functions_charts.php");
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

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
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";
/**
 * Main controller class for the individual page.
 */
class HourglassControllerRoot extends BaseController {
	var $show_changes = "yes";
	var $action = "";
	var $pid = "";
	var $default_tab = 0;
	var $hourPerson = null;

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
	var $show_full;
	var $show_spouse;
	var $generations;
	var $dgenerations;
	var $view;
	var $box_width;
	var $name;

	/**
	 * constructor
	 */
	function HourglassControllerRoot() {
		parent::BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
	global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $bheight, $bwidth, $GEDCOM_DEFAULT_TAB, $pgv_changes, $pgv_lang, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;

		if (!empty($_REQUEST["action"])) $this->action = $_REQUEST["action"];
	if (!empty($_REQUEST["pid"])) $this->pid = strtoupper($_REQUEST["pid"]);

	//Checks query strings to see if they exist else assign a default value
	if (isset($_REQUEST["show_full"])) $this->show_full = $_REQUEST["show_full"];
	else $this->show_full = 1;
	if (isset($_REQUEST["show_spouse"])) $this->show_spouse=$_REQUEST["show_spouse"];
	else $this->show_spouse=0;
	if (isset($_REQUEST["generations"])) $this->generations=$_REQUEST["generations"];
	else $this->generations = 3;
	if ($this->generations > $MAX_DESCENDANCY_GENERATIONS) $this->generations = $MAX_DESCENDANCY_GENERATIONS;
	if (!isset($this->view)) $this->view="";
	
	// -- Sets the sizes of the boxes
	if (isset($_REQUEST["box_width"])) $this->box_width=$_REQUEST["box_width"];
	else $this->box_width=100;
	if (empty($this->box_width)) $this->box_width = "100";
	$this->box_width=max($this->box_width, 50);
	$this->box_width=min($this->box_width, 300);
	// If show details is unchecked it makes the boxes smaller
	if (!$this->show_full) {
		$bwidth *= $this->box_width / 150;
	}
	else {
		$bwidth*=$this->box_width/100;
	}

	if (!$this->show_full) {
		$bheight = $bheight / 2;
	}
	
	
	// -- root id
	if (!isset($this->pid)) $this->pid="";
	$this->pid=check_rootid($this->pid);
	if ((DisplayDetailsByID($this->pid))||(showLivingNameByID($this->pid))) $this->name = get_person_name($this->pid);
	else $this->name = $pgv_lang["private"];
	
	//-- check for the user
	$this->uname = getUserName();
	if (!empty($this->uname)) {
		$this->user = getUser($this->uname);
		if (!empty($this->user["default_tab"])) $this->default_tab = $this->user["default_tab"];
	}
	$this->hourPerson = Person::getInstance($this->pid);

	//Checks how many generations of descendency is for the person for formatting purposes
	$this->dgenerations = $this->max_descendency_generations($this->pid, 0);


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
			$this->hourPerson->undoChange();
			break;
	}

	}

/**
 * Prints pedigree of the person passed in. Which is the descendancy 
 * 
 * @param mixed $pid ID of person to print the pedigree for 
 * @param mixed $count generation count, so it recursively calls itself
 * @access public
 * @return void
 */
function print_person_pedigree($pid, $count) {
	global $SHOW_EMPTY_BOXES, $PGV_IMAGE_DIR, $PGV_IMAGES;
	if ($count>=$this->generations) return;
	$famids = find_family_ids($pid);
	foreach($famids as $indexval => $famid) {
		print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">\n";
		$parents = find_parents($famid);
		$height="100%";
		print "<tr>";
		if ($count<$this->generations-1) print "<td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
		if ($count<$this->generations-1) print "<td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>\n";
		print "<td rowspan=\"2\">\n";
		print_pedigree_person($parents["HUSB"]);
		print "</td>\n";
		print "<td rowspan=\"2\">\n";
		$this->print_person_pedigree($parents["HUSB"], $count+1);
		print "</td>\n";
		print "</tr>\n<tr>\n<td height=\"50%\"";
		if ($count<$this->generations-1) print " style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\" ";
		print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n<tr>\n";
		if ($count<$this->generations-1) print "<td height=\"50%\" style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>";
		if ($count<$this->generations-1) print "<td rowspan=\"2\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>\n";
		print "<td rowspan=\"2\">\n";
		print_pedigree_person($parents["WIFE"]);
		print "</td>\n";
		print "<td rowspan=\"2\">\n";
		$this->print_person_pedigree($parents["WIFE"], $count+1);
		print "</td>\n";
		print "</tr>\n";
		if ($count<$this->generations-1) print "<tr>\n<td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
		print "</table>\n";
		break;
	}
}

/**
 * Prints descendency of passed in person 
 * 
 * @param mixed $pid ID of person to print descendency for
 * @param mixed $count count of generations to print
 * @access public
 * @return void
 */
function print_descendency($pid, $count) {
	global $TEXT_DIRECTION, $PGV_IMAGE_DIR, $PGV_IMAGES, $pgv_lang, $bheight, $bwidth;
		if ($count>=$this->dgenerations) return 0;
	//	print $this->dgenerations;
	print "\n<!-- print_descendency for $pid -->\n";
	print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	print "<tr>";
	print "<td width=\"$bwidth\">\n";
	$numkids = 0;
	$famids = find_sfamily_ids($pid);
	if (count($famids)>0) {
		$firstkids = 0;
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			if ($ct>0) {
			print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
			for($i=0; $i<$ct; $i++) {
				$rowspan = 2;
				if (($i>0)&&($i<$ct-1)) $rowspan=1;
				$chil = trim($match[$i][1]);
				print "<tr><td rowspan=\"$rowspan\" width=\"$bwidth\" style=\"padding-top: 2px;\">\n";
				if ($count+1 < $this->dgenerations) {
					$kids = $this->print_descendency($chil, $count+1);
					if ($i==0) $firstkids = $kids;
					$numkids += $kids;
				}
				else {
					print_pedigree_person($chil);
//					$this->dgenerations = $this->max_descendency_generations($pid, 0);
					$numkids++;
				}
				print "</td>\n";
				$twidth = 7;
				if ($ct==1) $twidth+=3;
				print "<td rowspan=\"$rowspan\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"$twidth\" height=\"3\" alt=\"\" /></td>\n";
				if ($ct>1) {
					if ($i==0) {
						print "<td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
						print "<tr><td height=\"50%\" style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
					else if ($i==$ct-1) {
						print "<td height=\"50%\" style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
						print "<tr><td height=\"50%\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
					else {
						print "<td style=\"background: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."');\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
				}
				print "</tr>\n";
			}
			print "</table>\n";
			}
		}
		print "</td>\n";
		print "<td width=\"$bwidth\">\n";
	}
	// NOTE: If statement OK
	if ($numkids==0) {
		$numkids = 1;
		$tbwidth = $bwidth+16;
		for($j=$count; $j<$this->dgenerations; $j++) {
			print "<div style=\"width: ".($tbwidth)."px;\"><br /></div>\n</td>\n<td width=\"$bwidth\">\n";
		}
	}
	//-- add offset divs to make things line up better
	if ($this->show_spouse) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			if (!empty($famrec)) {
				$marrec = get_sub_record(1, "1 MARR", $famrec);
				if (!empty($marrec)) {
					print "<br />";
				}
				print "<div style=\"height: ".$bheight."px; width: ".$bwidth."px;\"><br /></div>\n";
			}
		}
	}
	print_pedigree_person($pid);
	// NOTE: If statement OK
	if ($this->show_spouse) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			if (!empty($famrec)) {
				$parents = find_parents_in_record($famrec);
				$marrec = get_sub_record(1, "1 MARR", $famrec);
				if (!empty($marrec)) {
					print "<br />";
					print_simple_fact($famrec, "1 MARR", $famid);
				}
				if ($parents["HUSB"]!=$pid) print_pedigree_person($parents["HUSB"]);
				else print_pedigree_person($parents["WIFE"]);
			}
		}
	}
	// NOTE: If statement OK
	if ($count==0) {
		$indirec = find_person_record($pid);
		// NOTE: If statement OK
		if (displayDetails($indirec) || showLivingName($indirec)) {
			// -- print left arrow for decendants so that we can move down the tree
			//$famids = find_sfamily_ids($pid);
			$person = Person::getInstance($pid);
			$famids = $person->getSpouseFamilies();
			//-- make sure there is more than 1 child in the family with parents
			//$cfamids = find_family_ids($pid);
			$cfamids = $person->getChildFamilies();
			$num=0;
			foreach($cfamids as $famid=>$family) {
				if (!is_null($family)) {
					$num += $family->getNumberOfChildren();
				}
			}
			// NOTE: If statement OK
			if ($famids||($num>1)) {
				print "\n\t\t<div id=\"childarrow\" dir=\"";
				if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; ";
				else print "ltr\" style=\"position:absolute; ";
				print "width:10px; height:10px; \">";
				if ($this->view!="preview") {
					print "<a href=\"javascript: ".$pgv_lang["show"]."\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow',3);\" onmouseout=\"swap_image('larrow',3);\">";
					print "<img id=\"larrow\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow"]["other"]."\" border=\"0\" alt=\"\" />";
					print "</a>";
				}
				print "\n\t\t<div id=\"childbox\" dir=\"";
				if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; right: 20px; ";
				else print "ltr\" style=\"position:absolute; left: 20px;";
				print " width:".$bwidth."px; height:".$bheight."px; visibility: hidden;\">";
				print "\n\t\t\t<table class=\"person_box\"><tr><td>";
				foreach($famids as $famid=>$family) {
					if (!is_null($family)) {
						if($pid!=$family->getHusbId()) $spid=$family->getHusbId();
						else $spid=$family->getWifeId();
						if (!empty($spid)) {
							print "\n\t\t\t\t<a href=\"hourglass.php?pid=$spid&amp;show_spouse=$this->show_spouse&amp;show_full=$this->show_full&amp;generations=$this->generations&amp;box_width=$this->box_width\"><span ";
							if (displayDetailsById($spid) || showLivingNameById($spid)) {
								$name = get_person_name($spid);
								$name = rtrim($name);
								if (hasRTLText($name))
								     print "class=\"name2\">";
				   				else print "class=\"name1\">";
								print PrintReady($name);
							}
							else print $pgv_lang["private"];
							print "<br /></span></a>";
						}

						$children = $family->getChildren();
						foreach($children as $id=>$child) {
							$cid = $child->getXref();
							print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"hourglass.php?pid=$cid&amp;show_spouse=$this->show_spouse&amp;show_full=$this->show_full&amp;generations=$this->generations&amp;box_width=$this->box_width\"><span ";
							if (displayDetailsById($cid) || showLivingNameById($cid)) {
								$name = get_person_name($cid);
								$name = rtrim($name);
								if (hasRTLText($name))
								     print "class=\"name2\">&lt; ";
					   			else print "class=\"name1\">&lt; ";
								print PrintReady($name);
							}
							else print ">" . $pgv_lang["private"];
							print "<br /></span></a>";
						}
					}
				}
				//-- print the siblings
				foreach($cfamids as $famid=>$family) {
					if (!is_null($family)) {
						$parents = find_parents($famid);
						if($parents) {
							print "<span class=\"name1\"><br />".$pgv_lang["parents"]."<br /></span>";
							if (!empty($parents["HUSB"])) {
								$spid = $parents["HUSB"];
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"hourglass.php?pid=$spid&amp;show_spouse=$this->show_spouse&amp;show_full=$this->show_full&amp;generations=$this->generations&amp;box_width=$this->box_width\"><span ";
								if (displayDetailsById($spid) || showLivingNameById($spid)) {
									$name = get_person_name($spid);
									$name = rtrim($name);
									if (hasRTLText($name))
									     print "class=\"name2\">";
					   				else print "class=\"name1\">";
									print PrintReady($name);
								}
								else print $pgv_lang["private"];
								print "<br /></span></a>";
							}
							if (!empty($parents["WIFE"])) {
								$spid = $parents["WIFE"];
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"hourglass.php?pid=$spid&amp;show_spouse=$this->show_spouse&amp;show_full=$this->show_full&amp;generations=$this->generations&amp;box_width=$this->box_width\"><span ";
								if (displayDetailsById($spid) || showLivingNameById($spid)) {
									$name = get_person_name($spid);
									$name = rtrim($name);
									if (hasRTLText($name))
									     print "class=\"name2\">";
					   				else print "class=\"name1\">";
									print PrintReady($name);
								}
								else print $pgv_lang["private"];
								print "<br /></span></a>";
							}
						}
						$children = $family->getChildren();
						$num = $family->getNumberOfChildren();
						if ($num>1) print "<span class=\"name1\"><br />".$pgv_lang["siblings"]."<br /></span>";
						foreach($children as $id=>$child) {
							$cid = $child->getXref();
							if ($cid!=$pid) {
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"hourglass.php?pid=$cid&amp;show_spouse=$this->show_spouse&amp;show_full=$this->show_full&amp;generations=$this->generations&amp;box_width=$this->box_width\"><span ";
								if (displayDetailsById($cid) || showLivingNameById($cid)) {
									$name = get_person_name($cid);
									$name = rtrim($name);
									if (hasRTLText($name))
									print "class=\"name2\"> ";
					   				else print "class=\"name1\"> ";
									print PrintReady($name);
								}
								else print ">". $pgv_lang["private"];
								print "<br /></span></a>";
							}
						}
					}
				}
				print "\n\t\t\t</td></tr></table>";
				print "\n\t\t</div>";
				print "\n\t\t</div>";
			}
		}
	}
	print "</td></tr>\n";
	print "</table>\n";
	return $numkids;
}

/**
 * Calculates number of generations a person has
 * 
 * @param mixed $pid ID of person to see how far down the descendency goes
 * @param mixed $depth Pass in 0 and it calculates how far down descendency goes
 * @access public
 * @return maxdc Amount of generations the descendency actually goes
 */
function max_descendency_generations($pid, $depth) {
	//print "\n<br />".$pid."=".$depth;
	if ($depth >= $this->generations) return $depth;
	//$famids = find_sfamily_ids($pid);
	$person = Person::getInstance($pid);
	if (is_null($person)) return $depth;
	$famids = $person->getSpouseFamilies();
	//print " famcount=".count($famids);
//	$famids = $this->hour->getSpouseFamilies();
	$maxdc = $depth;
	//foreach($famids as $indexval => $famid) {
		//$famrec = find_family_record($famid);
	foreach($famids as $famid => $family){
		//print "famid=".$famid." ";
		$ct = preg_match_all("/1 CHIL @(.*)@/", $family->gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$chil = trim($match[$i][1]);
			$dc = $this->max_descendency_generations($chil, $depth+1);
			//print " dc=".$dc;
			if ($dc >= $this->generations) return $dc;
			if ($dc > $maxdc) $maxdc = $dc;
		}
	}
	if ($maxdc==0) $maxdc++;
	return $maxdc;
}

}

// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/hourglass_ctrl_user.php'))
{
	include_once 'includes/controllers/hourglass_ctrl_user.php';
}
else
{
	class HourglassController extends HourglassControllerRoot
	{
	}
}

$controller = new HourglassController();
$controller->init();
?>

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
require_once("includes/functions_charts.php");
require_once 'includes/controllers/basecontrol.php';
require( $factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];
require $confighelpfile["english"];
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];


$indifacts = array();			 // -- array to store the fact records in for sorting and displaying
$globalfacts = array();
$otheritems = array();			  //-- notes, sources, media objects
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
 * Main controller class for the Ancestry page.
 */
class AncestryControllerRoot extends BaseController {
	var $show_changes = "yes";
	var $action = "";
	var $pid = "";

	var $user = false;
	var $accept_success = false;
	var $visibility = "visible";
	var $position = "relative";
	var $display = "block";
	var $view;
	var $max_generation;
	var $show_cousins;
	var $rootid;
	var $min_generation;
	//var	$Dbwidth;
	//var $Dbheight;
	var $name;
	var $addname;
	var $OLD_PGENS;
	var $chart_style;
	var $show_full;
	var $cellwidth;

	/**
	 * constructor
	 */
	function AncestryControllerRoot() {
		parent::BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
	global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $bwidth, $bheight, $pbwidth, $pbheight, $GEDCOM_DEFAULT_TAB, $pgv_changes, $pgv_lang, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;
	global $DEFAULT_PEDIGREE_GENERATIONS, $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $OLD_PGENS, $box_width, $Dbwidth, $Dbheight;

	// -- args
	if (isset($_REQUEST["show_full"])) $this->show_full = $_REQUEST["show_full"];
	else $this->show_full = 1;
	if (isset($_REQUEST["chart_style"])) $this->chart_style = $_REQUEST["chart_style"];
	else $this->chart_style = 0;
	if (isset($_REQUEST["show_cousins"])) $this->show_cousins = $_REQUEST["show_cousins"];
	else $this->show_cousins = 0;
	//if (!isset($this->chart_style)) $this->chart_style = 0;
	//if ($this->chart_style=="") $this->chart_style = 0;
	//if (!isset($this->show_cousins)) $this->show_cousins = 0;
	//if ($this->show_cousins == "") $this->show_cousins = 0;
	if ((!isset($PEDIGREE_GENERATIONS)) || ($PEDIGREE_GENERATIONS == "")) $PEDIGREE_GENERATIONS = $DEFAULT_PEDIGREE_GENERATIONS;

	if ($PEDIGREE_GENERATIONS > $MAX_PEDIGREE_GENERATIONS) {
		$PEDIGREE_GENERATIONS = $MAX_PEDIGREE_GENERATIONS;
		$this->max_generation = true;
	}

	if ($PEDIGREE_GENERATIONS < 2) {
		$PEDIGREE_GENERATIONS = 2;
		$thmin_generation = true;
	}
	$OLD_PGENS = $PEDIGREE_GENERATIONS;
	
	if (empty($_REQUEST["rootid"])){
	if (!isset($this->rootid)) $this->rootid = "";
	$this->rootid = clean_input($this->rootid);
	$this->rootid = check_rootid($this->rootid);
	}
	else $this->rootid = $_REQUEST["rootid"];

	// -- size of the boxes
	if (!isset($box_width)) $box_width = "100";
	if (empty($box_width)) $box_width = "100";
	$box_width=max($box_width, 50);
	$box_width=min($box_width, 300);
	$Dbwidth*=$box_width/100;
	$bwidth=$Dbwidth;
	if (!$this->show_full) {
		$bwidth = $bwidth / 1.5;
	}
	$bheight=$Dbheight;
	if (!$this->show_full) {
		$bheight = $bheight / 1.5;
	}

	$pbwidth = $bwidth+12;
	$pbheight = $bheight+14;

	

	if ((DisplayDetailsByID($this->rootid)) || (showLivingNameByID($this->rootid))) {
		$this->name = get_person_name($this->rootid);
		$this->addname = get_add_person_name($this->rootid);
	}
	else {
		$this->name = $pgv_lang["private"];
		$this->addname = "";
	}

	if (strlen($this->name)<30) $this->cellwidth="420";
	else $this->cellwidth=(strlen($this->name)*14);

	
	$this->ancestry = Person::getInstance($this->pid);

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
			$this->hour->undoChange();
			break;
	}

	}

/**
 * print a child ascendancy
 *
 * @param string $pid individual Gedcom Id
 * @param int $sosa child sosa number
 * @param int $depth the ascendancy depth to show
 */
function print_child_ascendancy($pid, $sosa, $depth) {
	global $pgv_lang, $OLD_PGENS;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $Dindent;
	global $SHOW_EMPTY_BOXES, $pidarr, $box_width;

	$person = Person::getInstance($pid);
	// child
	print "<li>";
	print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a name=\"sosa".$sosa."\"></a>";
	$new=($pid=="" or !isset($pidarr["$pid"]));
	if ($sosa==1) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	else print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	print_pedigree_person($pid, 1, $this->view!="preview");
	print "</td>";
	print "<td>";
	if ($sosa>1) print_url_arrow($pid, "?rootid=$pid&amp;PEDIGREE_GENERATIONS=$OLD_PGENS&amp;show_full=$this->show_full&amp;box_width=$box_width&amp;chart_style=$this->chart_style", $pgv_lang["ancestry_chart"], 3);
	print "</td>";
	print "<td class=\"details1\">&nbsp;<span class=\"person_box". (($sosa==1) ? "NN" : (($sosa%2) ? "F" : "")) . "\">&nbsp;$sosa&nbsp;</span>&nbsp;";
	print "</td><td class=\"details1\">";
	$relation ="";
	if (!$new) $relation = "<br />[=<a href=\"#sosa".$pidarr["$pid"]."\">".$pidarr["$pid"]."</a> - ".get_sosa_name($pidarr["$pid"])."]";
	else $pidarr["$pid"]=$sosa;
	print get_sosa_name($sosa).$relation;
	print "</td>";
	print "</tr></table>";

	if (is_null($person)) return;
	// parents
	$famids = $person->getChildFamilies();
	$parents = false;
	$famrec = "";
	$famid = "";
	foreach($famids as $famid=>$family) {
		if (!is_null($family)) {
			$famrec = $family->getGedcomRecord();
			$parents = find_parents_in_record($famrec);
			if ($parents) break;			
		}
	}
	
	if (($parents or $SHOW_EMPTY_BOXES) and $new and $depth>0) {
		// print marriage info
		print "<span class=\"details1\" style=\"white-space: nowrap;\" >";
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" align=\"middle\" alt=\"\" /><a href=\"javascript: ".$pgv_lang["view_family"]."\" onclick=\"expand_layer('sosa_".$sosa."'); return false;\" class=\"top\"><img id=\"sosa_".$sosa."_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" alt=\"".$pgv_lang["view_family"]."\" /></a> ";
		print "&nbsp;<span class=\"person_box\">&nbsp;".($sosa*2)."&nbsp;</span>&nbsp;".$pgv_lang["and"];
 		print "&nbsp;<span class=\"person_boxF\">&nbsp;".($sosa*2+1)." </span>&nbsp;";
		if (showFact("MARR", $famid)) print_simple_fact($famrec, "MARR", $parents["WIFE"]); else print $pgv_lang["private"];
		print "</span>";
		// display parents recursively
		print "<ul style=\"list-style: none; display: block;\" id=\"sosa_$sosa\">";
		$this->print_child_ascendancy($parents["HUSB"], $sosa*2, $depth-1);
		$this->print_child_ascendancy($parents["WIFE"], $sosa*2+1, $depth-1);
		print "</ul>\r\n";
	}
	print "</li>\r\n";
}

}

// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/ancestry_ctrl_user.php'))
{
	include_once 'includes/controllers/ancestry_ctrl_user.php';
}
else
{
	class AncestryController extends AncestryControllerRoot
	{
	}
}

$controller = new AncestryController();
$controller->init();
?>
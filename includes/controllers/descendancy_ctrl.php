<?php
/**
 * Controller for the Descendancy Page
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
require_once("includes/functions_charts.php");
require($factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];
require $confighelpfile["english"];
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];

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
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";
/**
 * Main controller class for the individual page.
 */
class DescendancyControllerRoot extends BaseController {
	var $show_changes = "yes";
	var $action = "";
	var $pid = "";
	var $default_tab = 0;
	var $descPerson = null;

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
	var $show_full;
	var $chart_style;
	var $sexarray = array();
	var $generations;
	var $view;
	var $personcount;
	var $box_width;
	var $Dbwidth;
	var $Dbheight;
	var $pbwidth;
	var $pbheight;
	// d'Aboville numbering system [ http://www.saintclair.org/numbers/numdob.html ]
	var $dabo_num=array();
	var $dabo_sex=array();
	var $name;
	var $cellwidth;
	var $show_cousins;

	/**
	 * constructor
	 */
	function DescendancyRootController() {
		parent::BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
	global $USE_RIN, $MAX_ALIVE_AGE, $bwidth, $bheight, $pbwidth, $pbheight, $GEDCOM, $GEDCOM_DEFAULT_TAB, $pgv_changes, $pgv_lang, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;

	$this->sexarray["M"] = $pgv_lang["male"];
	$this->sexarray["F"] = $pgv_lang["female"];
	$this->sexarray["U"] = $pgv_lang["unknown"];

	//set arguments
	if (isset($_REQUEST["show_full"])) $this->show_full = $_REQUEST["show_full"];
	else $this->show_full = 1;
	if (!empty($_REQUEST["chart_style"])) $this->chart_style = $_REQUEST["chart_style"];
	else $this->chart_style = 0;
	if (!empty($_REQUEST["generations"])) $this->generations = $_REQUEST["generations"];
	else $this->generations = 2;
	if ($this->generations > $MAX_DESCENDANCY_GENERATIONS) $this->generations = $MAX_DESCENDANCY_GENERATIONS;
	if (!isset($this->view)) $this->view="";
	if (!isset($this->personcount)) $this->personcount = 1;

	// -- size of the boxes
	if (!isset($_REQUEST["box_width"])) $this->box_width = "100";
	else $this->box_width = $_REQUEST["box_width"];
	if (empty($this->box_width)) $this->box_width = "100";
	$this->box_width=max($this->box_width, 50);
	$this->box_width=min($this->box_width, 300);
	$this->Dbwidth*=$this->box_width/100;

	if (!$this->show_full) {
		$bwidth *= $this->box_width / 150;
	}
	else {
		$bwidth*=$this->box_width/100;
	}

	if (!$this->show_full) {
		$bheight = $bheight / 1.5;
	}

	$pbwidth = $bwidth+12;
	$pbheight = $bheight+14;

	$this->default_tab = $GEDCOM_DEFAULT_TAB;

	if (!empty($_REQUEST["show_changes"])) $this->show_changes = $_REQUEST["show_changes"];
	if (!empty($_REQUEST["action"])) $this->action = $_REQUEST["action"];
	if (!empty($_REQUEST["pid"])) $this->pid = strtoupper($_REQUEST["pid"]);

	// -- root id
	if (!isset($this->pid)) $this->pid="";
	$this->pid = clean_input($this->pid);
	$this->pid=check_rootid($this->pid);

	if ((DisplayDetailsByID($this->pid))||(showLivingNameByID($this->pid))) $this->name = get_person_name($this->pid);
	else $this->name = $pgv_lang["private"];

	if (strlen($this->name)<30) $this->cellwidth="420";
	else $this->cellwidth=(strlen($this->name)*14);

	//-- check for the user
	$this->uname = getUserName();
	if (!empty($this->uname)) {
		$this->user = getUser($this->uname);
		if (!empty($this->user["default_tab"])) $this->default_tab = $this->user["default_tab"];
	}

	$this->descPerson = Person::getInstance($this->pid);

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
			$this->desc->undoChange();
			break;
	}

	}

	/**
	 * print a child family
	 *
	 * @param string $pid individual Gedcom Id
	 * @param int $depth the descendancy depth to show
	 */
	function print_child_family(&$person, $depth, $label="1.", $gpid="") {
		global $pgv_lang;
		global $PGV_IMAGE_DIR, $PGV_IMAGES, $personcount;

		if (is_null($person)) return;
		$families = $person->getSpouseFamilies();
		if ($depth<1) return;
		foreach($families as $famid => $family) {
			print_sosa_family($family->getXref(), "", -1, $label, $person->getXref(), $gpid, $personcount);
			$personcount++;
			//$children = get_children_ids($famids);
			$children = $family->getChildren();
			$i=1;

			foreach ($children as $childid => $child) {
			$this->print_child_family($child, $depth-1, $label.($i++).".", $person->getXref());
			}
		}
	}

/**
 * print a child descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param int $depth the descendancy depth to show
 */
function print_child_descendancy(&$person, $depth) {
	global $pgv_lang;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $Dindent;
	global $personcount;

	if (is_null($person)) return;
	//print_r($person);
	print "<li>";
	print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
	if ($depth==$this->generations) print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	else print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	print_pedigree_person($person->getXref(), 1, $this->view!="preview",'',$personcount);
	print "</td>";

	// check if child has parents and add an arrow
	print "<td>&nbsp;</td>";
	print "<td>";
	$sfamids = $person->getChildFamilies();
	foreach($sfamids as $famid => $family) {
		$parents = find_parents($famid);
		if ($parents) {
			$parid=$parents["HUSB"];
			if ($parid=="") $parid=$parents["WIFE"];
			if ($parid!="") {
				print_url_arrow($parid.$personcount.$person->getXref(), "?pid=$parid&amp;generations=$this->generations&amp;chart_style=$this->chart_style&amp;show_full=$this->show_full&amp;box_width=$this->box_width", $pgv_lang["start_at_parents"], 2);
				$personcount++;
			}
		}
	}

	// d'Aboville child number
	$level =$this->generations-$depth;
	if ($this->show_full) print "<br /><br />&nbsp;";
	print "<span dir=\"ltr\">"; //needed so that RTL languages will display this properly
	if (!isset($this->dabo_num[$level])) $this->dabo_num[$level]=0;
	$this->dabo_num[$level]++;
	$this->dabo_num[$level+1]=0;
	$this->dabo_sex[$level]=$person->getSex();
	for ($i=0; $i<=$level;$i++) {
		$isf=$this->dabo_sex[$i];
		if ($isf=="M") $isf="";
		if ($isf=="U") $isf="NN";
		print "<span class=\"person_box".$isf."\">&nbsp;".$this->dabo_num[$i]."&nbsp;</span>";
		if ($i<$level) echo ".";
	}
	print "</span>";
	print "</td></tr>";
	print "</table>";
	print "</li>\r\n";

	// loop for each spouse
	$sfam = $person->getSpouseFamilies();
	foreach ($sfam as $famid => $family) {
		$personcount++;
		$this->print_family_descendancy($person, $family, $depth);
	}
}

/**
 * print a family descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param Family $famid family record
 * @param int $depth the descendancy depth to show
 */
function print_family_descendancy(&$person, &$family, $depth) {
	global $pgv_lang, $factarray;
	global $GEDCOM, $PGV_IMAGE_DIR, $PGV_IMAGES, $Dindent, $personcount;

	if (is_null($family)) return;
	if (is_null($person)) return;

	$famrec = $family->getGedcomRecord();
	$famid = $family->getXref();
	$parents = find_parents($famid);
	if ($parents) {

		// spouse id
		$id = $parents["WIFE"];
		if ($id==$person->getXref()) $id = $parents["HUSB"];

		// print marriage info
		print "<li>";
		print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" alt=\"\" />";
		print "<span class=\"details1\" style=\"white-space: nowrap; \" >";
		print "<a href=\"#\" onclick=\"expand_layer('".$famid.$personcount."'); return false;\" class=\"top\"><img id=\"".$famid.$personcount."_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" alt=\"".$pgv_lang["view_family"]."\" /></a> ";
		echo "<a href=\"family.php?famid=$famid&amp;ged=$GEDCOM\" class=\"details1\">";
		if (showFact("MARR", $famid)) print_simple_fact($famrec, "MARR", $id);
		else print $pgv_lang["private"];
		echo "</a>";
		print "</span>";

		// print spouse
		print "<ul style=\"list-style: none; display: block;\" id=\"".$famid.$personcount."\">";
		print "<li>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
		print_pedigree_person($id, 1, $this->view!="preview",''.$personcount);
		print "</td>";

		// check if spouse has parents and add an arrow
		print "<td>&nbsp;</td>";
		print "<td>";
		$sfamids = find_family_ids($id);
		foreach($sfamids as $indexval => $sfamid) {
			$parents = find_parents($sfamid);
			if ($parents) {
				$parid=$parents["HUSB"];
				if ($parid=="") $parid=$parents["WIFE"];
				if ($parid!="") {
					print_url_arrow($parid.$personcount.$person->getXref(), "?pid=$parid&amp;generations=$this->generations&amp;show_full=$this->show_full&amp;box_width=$this->box_width", $pgv_lang["start_at_parents"], 2);
					$personcount++;
				}
			}
		}
		if ($this->show_full) print "<br /><br />&nbsp;";
		print "</td></tr>";

		// children
		$children = $family->getChildren();
		print "<tr><td colspan=\"3\" class=\"details1\" >&nbsp;";
		if (count($children)<1) print $pgv_lang["no_children"];
		else print $factarray["NCHI"].": ".count($children);
		print "</td></tr></table>";
		print "</li>\r\n";
		if ($depth>0) foreach ($children as $childid => $child) {
			$personcount++;
			$this->print_child_descendancy($child, $depth-1);
		}
		print "</ul>\r\n";
		print "</li>\r\n";
	}
}

}

// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/descendancy_ctrl_user.php'))
{
	include_once 'includes/controllers/descendancy_ctrl_user.php';
}
else
{
	class DescendancyController extends DescendancyControllerRoot
	{
	}
}

$controller = new DescendancyController();
$controller->init();
?>

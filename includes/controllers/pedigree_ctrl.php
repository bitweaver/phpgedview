<?php
/**
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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

define('PGV_PEDIGREE_CTRL_PHP', '');

/**
 * Initialization
 */
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_charts.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_person.php');

/**
 * Main controller class for the Ancestry page.
 */
class PedigreeControllerRoot extends BaseController {
	var $log2;
	var $show_famlink = true;
	var $rootid;
	var $rootPerson;
	var $show_full;
	var $talloffset;
	var $PEDIGREE_GENERATIONS;
	var $pbwidth;
	var $pbheight;
	var $treeid;
	var $treesize;
	var $curgen;
	var $yoffset;
	var $xoffset;
	var $prevyoffset;
	var $offsetarray;
	var $minyoffset;

	/**
	 * Initialization function
	 */
	function init() {
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT, $MAX_PEDIGREE_GENERATIONS;
		global $DEFAULT_PEDIGREE_GENERATIONS, $SHOW_EMPTY_BOXES;
		global $bwidth, $bheight, $baseyoffset, $basexoffset, $byspacing, $bxspacing;
		global $TEXT_DIRECTION, $BROWSER_TYPE, $show_full, $talloffset;

		$this->log2 = log(2);
		if ($this->isPrintPreview()) {
			$this->show_famlink = false;
		}

		$this->rootid    =safe_GET_xref('rootid');
		$this->show_full =safe_GET('show_full', array('0', '1'), $PEDIGREE_FULL_DETAILS);
		$this->talloffset=safe_GET('talloffset', array('0', '1'), $PEDIGREE_LAYOUT);
		$this->PEDIGREE_GENERATIONS=safe_GET_integer('PEDIGREE_GENERATIONS', 2, $MAX_PEDIGREE_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);

		// TODO: some library functions expect this as a global.
		// Passing a function parameter would be much better.
		global $PEDIGREE_GENERATIONS;
		$PEDIGREE_GENERATIONS=$this->PEDIGREE_GENERATIONS;

		// This is passed as a global.  A parameter would be better...
		$this->show_full = ($this->show_full) ? 1 : 0;		// Make SURE this is an integer
		$this->talloffset = ($this->talloffset) ? 1 : 0;
		$show_full = $this->show_full;
		$talloffset = $this->talloffset;

		// Validate parameters
		$this->rootid=check_rootid($this->rootid);

		$this->rootPerson = Person::getInstance($this->rootid);
		if (is_null($this->rootPerson)) $this->rootPerson = new Person('');

		//-- adjustments for hide details
		if ($this->show_full==false) {
			$bheight=30;
			$bwidth-=30;
			$baseyoffset+=30;
		}
		//-- adjustments for portrait mode
		if ($this->talloffset==0) {
			$bxspacing+=12;
			$bwidth+=20;
			$baseyoffset -= 20*($this->PEDIGREE_GENERATIONS-1);
		}
		//-- adjustments for preview
		if ($this->isPrintPreview()) {
			$baseyoffset -= 20;
		}

		$this->pbwidth = $bwidth+6;
		$this->pbheight = $bheight+5;

		$this->treeid = ancestry_array($this->rootid);
		$this->treesize = pow(2, (int)($this->PEDIGREE_GENERATIONS))-1;

		//-- ancestry_array puts everyone at $i+1
		for($i=0; $i<$this->treesize; $i++) $this->treeid[$i] = $this->treeid[$i+1];

		if (($this->PEDIGREE_GENERATIONS < 5)&&($this->show_full==false)) {
			$baseyoffset+=($this->PEDIGREE_GENERATIONS*$this->pbheight/2)+50;
			if ($this->isPrintPreview()) $baseyoffset-=120;
		}

		if ($this->PEDIGREE_GENERATIONS==3) {
			$baseyoffset+=$this->pbheight*1.6;
		}

		// -- this next section will create and position the DIV layers for the pedigree tree
		$this->curgen = 1;			// -- variable to track which generation the algorithm is currently working on
		$this->yoffset=0;				// -- used to offset the position of each box as it is generated
		$this->xoffset=0;
		$this->prevyoffset=0;		// -- used to track the y position of the previous box
		$this->offsetarray = array();
		$this->minyoffset = 0;
		if ($this->treesize<3) $this->treesize=3;
		// -- loop through all of id's in the array starting at the last and working to the first
		//-- calculation the box positions
		for($i=($this->treesize-1); $i>=0; $i--) {
			// -- check to see if we have moved to the next generation
			if ($i < floor($this->treesize / (pow(2, $this->curgen)))) {
				$this->curgen++;
			}
			//-- box position in current generation
			$boxpos = $i-pow(2, $this->PEDIGREE_GENERATIONS-$this->curgen);
			//-- offset multiple for current generation
			$genoffset = pow(2, $this->curgen-$this->talloffset);
			$boxspacing = $this->pbheight+$byspacing;
			// -- calculate the yoffset		Position in the generation		Spacing between boxes		put child between parents
			$this->yoffset = $baseyoffset+($boxpos * ($boxspacing * $genoffset))+(($boxspacing/2)*$genoffset)+($boxspacing * $genoffset);
			if ($this->talloffset==0) {
				//-- compact the tree
				if ($this->curgen<$this->PEDIGREE_GENERATIONS) {
					$parent = floor(($i-1)/2);
					if ($i%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * ($this->curgen-1));
					else $this->yoffset=$this->yoffset + (($boxspacing/2) * ($this->curgen-1));

					$pgen = $this->curgen;
					while($parent>0) {
						if ($parent%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * $pgen);
						else $this->yoffset=$this->yoffset + (($boxspacing/2) * $pgen);
						$pgen++;
						if ($pgen>3) {
							$temp=0;
							for($j=1; $j<($pgen-2); $j++) $temp += (pow(2, $j)-1);
							if ($parent%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * $temp);
							else $this->yoffset=$this->yoffset + (($boxspacing/2) * $temp);
						}
						$parent = floor(($parent-1)/2);
					}
					if ($this->curgen>3) {
						$temp=0;
							for($j=1; $j<($this->curgen-2); $j++) $temp += (pow(2, $j)-1);
						if ($i%2 == 0) $this->yoffset=$this->yoffset - (($boxspacing/2) * $temp);
						else $this->yoffset=$this->yoffset + (($boxspacing/2) * $temp);
					}
				}
			}
			// -- calculate the xoffset
			if ($this->talloffset) {
				// -- Landscape
				$this->xoffset = 10 + $basexoffset + (($this->PEDIGREE_GENERATIONS - $this->curgen) * ($this->pbwidth+$bxspacing));
				if ($this->curgen == $this->PEDIGREE_GENERATIONS) $this->xoffset += 10;
			} else {
				// -- Portrait
				$this->xoffset = 20 + $basexoffset + (($this->PEDIGREE_GENERATIONS - $this->curgen) * (($this->pbwidth+$bxspacing) / 2));
			}
			if ($this->curgen == 1 && $this->talloffset) $this->xoffset += 10;
			$this->offsetarray[$i]["x"]=$this->xoffset;
			$this->offsetarray[$i]["y"]=$this->yoffset;
		}

		//-- collapse the tree if boxes are missing
		if (!$SHOW_EMPTY_BOXES) {
			if ($this->PEDIGREE_GENERATIONS>1) $this->collapse_tree(0, 1, 0);
		}

		//-- calculate the smallest yoffset and adjust the tree to that offset
		$minyoffset = 0;
		for($i=0; $i<count($this->treeid); $i++) {
			if ($SHOW_EMPTY_BOXES || !empty($treeid[$i])) {
				if (!empty($offsetarray[$i])) {
					if (($minyoffset==0)||($minyoffset>$this->offsetarray[$i]["y"]))  $minyoffset = $this->offsetarray[$i]["y"];
				}
			}
		}

		$ydiff = $baseyoffset+35-$minyoffset;
		$this->adjust_subtree(0, $ydiff);

		//-- if no father keep the tree off of the pedigree form
		if (($this->isPrintPreview())&&($this->offsetarray[0]["y"]+$baseyoffset<300)) $this->adjust_subtree(0, 300-($this->offsetarray[0]["y"]+$baseyoffset));
	}

	/**
	 * return the title of this page
	 * @return string	the title of the page to go in the <title> tags
	 */
	function getPageTitle() {
		global $pgv_lang, $GEDCOM;

		$name = $pgv_lang['unknown'];
		if (!is_null($this->rootPerson)) $name = $this->rootPerson->getFullName();
		return $name." ".$pgv_lang["index_header"];
	}

	function getPersonName() {
		global $pgv_lang;

		$name = $pgv_lang['unknown'];
		if (!is_null($this->rootPerson)) $name = $this->rootPerson->getFullName();
		return $name;
	}

	function adjust_subtree($index, $diff) {
		global $offsetarray, $treeid, $log2, $talloffset,$boxspacing, $mdiff, $SHOW_EMPTY_BOXES;
		$f = ($index*2)+1; //-- father index
		$m = $f+1; //-- mother index

		if (!$SHOW_EMPTY_BOXES && empty($treeid[$index])) return;
		if (empty($offsetarray[$index])) return;
		$offsetarray[$index]["y"] += $diff;
		if ($f<count($treeid)) adjust_subtree($f, $diff);
		if ($m<count($treeid)) adjust_subtree($m, $diff);
	}

	function collapse_tree($index, $curgen, $diff) {
		global $offsetarray, $treeid, $log2, $talloffset,$boxspacing, $mdiff, $minyoffset;

		//print "$index:$curgen:$diff<br />\n";
		$f = ($index*2)+1; //-- father index
		$m = $f+1; //-- mother index
		if (empty($treeid[$index])) {
			$pgen=$curgen;
			$genoffset=0;
			while($pgen<=$this->PEDIGREE_GENERATIONS) {
				$genoffset += pow(2, ($this->PEDIGREE_GENERATIONS-$pgen));
				$pgen++;
			}
			if ($talloffset==1) $diff+=.5*$genoffset;
			else $diff+=$genoffset;
			if (isset($offsetarray[$index]["y"])) $offsetarray[$index]["y"]-=($boxspacing*$diff)/2;
			return $diff;
		}
		if ($curgen==$this->PEDIGREE_GENERATIONS) {
			$offsetarray[$index]["y"] -= $boxspacing*$diff;
			//print "UP $index BY $diff<br />\n";
			return $diff;
		}
		$odiff=$diff;
		$fdiff = collapse_tree($f, $curgen+1, $diff);
		if (($curgen<($this->PEDIGREE_GENERATIONS-1))||($index%2==1)) $diff=$fdiff;
		if (isset($offsetarray[$index]["y"])) $offsetarray[$index]["y"] -= $boxspacing*$diff;
		//print "UP $index BY $diff<br />\n";
		$mdiff = collapse_tree($m, $curgen+1, $diff);
		$zdiff = $mdiff - $fdiff;
		if (($zdiff>0)&&($curgen<$this->PEDIGREE_GENERATIONS-2)) {
			$offsetarray[$index]["y"] -= $boxspacing*$zdiff/2;
			//print "UP $index BY ".($zdiff/2)."<br />\n";
			if ((empty($treeid[$m]))&&(!empty($treeid[$f]))) adjust_subtree($f, -1*($boxspacing*$zdiff/4));
			$diff+=($zdiff/2);
		}
		return $diff;
	}
}

// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/pedigree_ctrl_user.php'))
{
	include_once 'includes/controllers/pedigree_ctrl_user.php';
}
else
{
	class PedigreeController extends PedigreeControllerRoot
	{
	}
}

?>

<?php
/**
 * Parses gedcom file and displays a pedigree tree.
 *
 * Specify a $rootid to root the pedigree tree at a certain person
 * with id = $rootid in the GEDCOM file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 22 August 2005
 *
 * $Id$
 * @package PhpGedView
 * @subpackage Charts
 */

/**
 * Initialization
 */ 
require_once( "../kernel/setup_inc.php" );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'includes/bitsession.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();
if (isset($_REQUEST['rootid'])) $gGedcom->rootId($_REQUEST['rootid']);
else $gGedcom->rootId();

require_once ( PHPGEDVIEW_PKG_PATH.'includes/controllers/pedigree_ctrl.php' );

$controller = new PedigreeController();
$controller->init();

// -- echo html header information
//require_once( PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print.php' );
//print_header($controller->getPageTitle());

// if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';

//-- echo the boxes
$curgen = 1;
$xoffset = 0;
$yoffset = 0;			// -- used to offset the position of each box as it is generated
$prevxoffset = 0;		// -- used to track the x position of the previous box
$prevyoffset = 0;		// -- used to track the y position of the previous box
$maxyoffset = 0;
$linesize = 3;
if (!isset($brborder)) $brborder = 1;	// Avoid errors from old custom themes
for($i=($controller->treesize-1); $i>=0; $i--) {
	// -- check to see if we have moved to the next generation
	if ($i < floor($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}
	$prevxoffset = $xoffset;
	$prevyoffset = $yoffset;
	if ($talloffset < 2) {
		$xoffset = $controller->offsetarray[$i]["x"];
		$yoffset = $controller->offsetarray[$i]["y"];
	}
	else {
		$xoffset = $controller->offsetarray[$i]["y"];
		$yoffset = $controller->offsetarray[$i]["x"];
	}
	// -- if we are in the middle generations then we need to draw the connecting lines
	if (($curgen > 0 && $talloffset > 1) || (($curgen > $talloffset) && ($curgen < $controller->PEDIGREE_GENERATIONS))) {
		if ($i%2==1) {
			if ($SHOW_EMPTY_BOXES || ($controller->treeid[$i]) || ($controller->treeid[$i+1])) {
				if ($talloffset < 2) {
					$vlength = $prevyoffset-$yoffset;
				}
				else {
					$vlength = $prevxoffset-$xoffset;
				}
				if (!$SHOW_EMPTY_BOXES && (empty($controller->treeid[$i+1]))) {
					$parent = ceil(($i-1)/2);
					$vlength = $controller->offsetarray[$parent]["y"]-$yoffset;
				}
				$linexoffset = $xoffset;
			}
		}
	}
	// -- draw the box
	if (!empty($controller->treeid[$i]) || $SHOW_EMPTY_BOXES) {
		// Work around a bug in FireFox that mis-places some boxes in Portrait RTL, resulting in
		// vertical lines that themselves appear to be mis-placed.
		if ($TEXT_DIRECTION=="rtl" && $BROWSERTYPE=="mozilla" && ($curgen <= 2)) $xoffset += 10;
		if ($TEXT_DIRECTION=="rtl") $xoffset += $brborder;		// Account for thickness of right box border

		if ($yoffset>$maxyoffset) $maxyoffset=$yoffset;
		$widthadd = 0;
		if ($i==0) $iref = rand();
		else $iref = $i;

		if (($curgen==1)&&(!empty($controller->treeid[$i]))&&(count(find_family_ids($controller->treeid[$i]))>0)) $widthadd = 20;
		if (($curgen >2) && ($curgen < $controller->PEDIGREE_GENERATIONS)) $widthadd = 10;

		if ($talloffset == 2) $zindex = $PEDIGREE_GENERATIONS-$curgen;
		else $zindex = 0;

		$mfstyle = "";
		if (!empty($controller->treeid[$i])) {
			$person = Person::getInstance($controller->treeid[$i]);
			$indirec = $person->getGedcomRecord();
			$ct = preg_match("/1 SEX F/", $indirec);
			if ($ct>0) $mfstyle="F";
		}
		if (!isset($controller->treeid[$i])) $controller->treeid[$i] = false;
//		print_pedigree_person($controller->treeid[$i], 1, $controller->show_famlink, $iref, 1);
		if (($curgen==1) && (count(find_family_ids($controller->treeid[$i]))>0) ) {
			$did = 1;
			if ($i > ($controller->treesize/2) + ($controller->treesize/4)-1) $did++;
		}
	}
}

if ($controller->rootPerson->canDisplayDetails()) {
	// -- echo left arrow for decendants so that we can move down the tree
	$yoffset += ($controller->pbheight / 2)-10;
	$famids = $controller->rootPerson->getSpouseFamilies();
	//-- make sure there is more than 1 child in the family with parents
	$cfamids = $controller->rootPerson->getChildFamilies();
	if (count($famids)>0) {
		if ($talloffset == 0) {
			if ($PEDIGREE_GENERATIONS<6) {
				$addxoffset = 60*(5-$PEDIGREE_GENERATIONS);
			}
			else {
				$addxoffset = 0;
			}
			$pos[$i]['left'] = $addxoffset;
			$pos[$i]['top'] = $yoffset;
		}
		else if ($talloffset == 1) {
			if ($PEDIGREE_GENERATIONS<4)	$basexoffset += 60;
		}
		else if ($talloffset==3) {
			$pos[$i]['left'] = $linexoffset-10+$controller->pbwidth/2+$vlength/2;
			$pos[$i]['top'] = $yoffset-$controller->pbheight/2-10;
		}
		else {
			$pos[$i]['left'] = $linexoffset-10+$controller->pbwidth/2+$vlength/2;
			$pos[$i]['top'] = $yoffset+$controller->pbheight/2+10;
		}
		$yoffset += ($controller->pbheight / 2)+10;
		foreach($famids as $ind=>$family) {
			if ($family!=null) {
				$husb = $family->getHusbId();
				$wife = $family->getWifeId();
				if($controller->rootid!=$husb) $spid=$family->getHusband();
				else $spid=$family->getWife();
				if (!empty($spid)) {
					if ($spid->canDisplayName()) {
						$name = $spid->getFullName();
						$name = rtrim($name);
					} else $name = $pgv_lang["private"];
				}

				$children = $family->getChildren();
				foreach($children as $ind2=>$child) {
					if ($child->canDisplayName()) {
						$name = $child->getFullName();
						$name = rtrim($name);
					} else $name = $pgv_lang["private"];
				}
			}
		}
		//-- echo the siblings
		foreach($cfamids as $ind=>$family) {
			if ($family!=null) {
				$children = $family->getChildren();
				foreach($children as $ind2=>$child) {
					if (!$controller->rootPerson->equals($child) && !is_null($child)) {
						if ($child->canDisplayName()) {
							$name = $child->getFullName();
							$name = rtrim($name);
						} else $name = $pgv_lang["private"];
					}
				}
			}
		}
	}
}

$gBitSmarty->assign_by_ref( 'pos', $pos );
$gBitSmarty->assign_by_ref( 'boxes', $famids );

// Display the template
$doctitle = 'Pedigree Chart';
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSmarty->assign( "name", 'name' );
$gBitSystem->display( 'bitpackage:phpgedview/pedigree.tpl', tra( 'Pedigree Chart' ) );
?>

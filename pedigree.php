<?php
/**
 * Parses gedcom file and displays a pedigree tree.
 *
 * Specify a $rootid to root the pedigree tree at a certain person
 * with id = $rootid in the GEDCOM file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * $Id: pedigree.php,v 1.6 2008/06/25 22:21:15 spiderr Exp $
 * @package PhpGedView
 * @subpackage Charts
 */

/**
 * Initialization
 */ 
require_once( "../bit_setup_inc.php" );

$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();
if (isset($_REQUEST['rootid'])) $gGedcom->rootId($_REQUEST['rootid']);
else $gGedcom->rootId();

require_once("includes/controllers/pedigree_ctrl.php");
$controller->init($gGedcom);
require_once("includes/functions_mediadb.php");

// -- print html header information
$gBitSmarty->assign( "pagetitle", $PEDIGREE_GENERATIONS." ".tra( 'Generation Pedigree Chart' ) );
$controller->getPersonName();
$gBitSmarty->assign( "name", $controller->getPersonName() );

//-- print the boxes
$curgen = 1;
$yoffset=0;			// -- used to offset the position of each box as it is generated
$xoffset=0;
$prevyoffset=0;		// -- used to track the y position of the previous box
$maxyoffset = 0;
$pos = array();
$boxes = array();
for($i=($controller->treesize-1); $i>=0; $i--) {
	// -- check to see if we have moved to the next generation
	if ($i < floor($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}
	$prevyoffset = $yoffset;
	$xoffset = $controller->offsetarray[$i]["x"];
	$yoffset = $controller->offsetarray[$i]["y"];
	// -- if we are in the middle generations then we need to draw the connecting lines
	if ( ($curgen > (1+$controller->talloffset) ) && ( $curgen < $controller->PEDIGREE_GENERATIONS ) ) {
		if ($i%2==1) {
			if ( $gBitSystem->getConfig('pgv_show_empty_boxes', 'y') == 'y' || ($controller->treeid[$i]) || ($controller->treeid[$i+1])) {
				$vlength = ($prevyoffset-$yoffset);
				if ( $gBitSystem->getConfig('pgv_show_empty_boxes', 'y') == 'n' && ( empty($controller->treeid[$i+1]) ) ) {
					$parent = ceil(($i-1)/2);
					$vlength = $controller->offsetarray[$parent]["y"]-$yoffset;
				}
				$boxes[$i]['top'] = $yoffset+1+$controller->pbheight/2;
				$boxes[$i]['left'] = $xoffset-1;
				$boxes[$i]['height'] = $vlength-1;
			}
		}
	}
	// -- draw the box
	if ( !empty( $controller->treeid[$i] ) || $SHOW_EMPTY_BOXES ) {
		if ($yoffset>$maxyoffset) $maxyoffset=$yoffset;
		$widthadd = 0;
		$pos[$i]['left'] = $xoffset;
		$pos[$i]['top'] = $yoffset;
		$pos[$i]['height'] = $controller->pbheight;
		$pos[$i]['width'] = $controller->pbwidth;
		
		if (($curgen==1)&&(!empty($controller->treeid[$i]))&&(count(find_family_ids($controller->treeid[$i]))>0)) $widthadd = 20;
		if (($curgen >2) && ($curgen < $controller->PEDIGREE_GENERATIONS)) $widthadd = 10;
		$mfstyle = "";
		
		if (($curgen > (1+$controller->talloffset)) && ($curgen < $controller->PEDIGREE_GENERATIONS)) {
			$pos[$i]['tall'] = 1;
		}

		if (!empty($controller->treeid[$i])) {
			$person = Person::getInstance($controller->treeid[$i]);
			$pos[$i]['id'] = $controller->treeid[$i];
			$pos[$i]['name'] = $person->getName();
			$pos[$i]['sex'] = $person->getSex();
			$pos[$i]['seximage'] = $person->getSexImage();
			if ( $pos[$i]['sex'] == "M" ) $s = "";
			else if ( $pos[$i]['sex'] == "F" ) $s = "F";
			else $s = "NN";
			$pos[$i]['sexflag'] = $s;
			$pos[$i]['dob'] = $person->getBirthDate();
			$pos[$i]['pob'] = $person->getBirthPlace();
			if ( showFact("OBJE", $controller->treeid[$i]) ) {
				$object = find_highlighted_object( $controller->treeid[$i], $person );
				if (!empty($object["thumb"])) {
					$size = findImageSize($object["file"]);
					$pos[$i]['imageclass'] = "pedigree_image_portrait";
					if ($size[0]>$size[1]) $pos[$i]['imageclass'] = "pedigree_image_landscape";
					$imgsize = findImageSize($object["file"]);
					$imgwidth = $imgsize[0]+50;
					$imgheight = $imgsize[1]+150;

					$pos[$i]['imagepop'] = "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode(PHPGEDVIEW_PKG_URL."media/".$object["file"])."',$imgwidth, $imgheight);\">";
					$pos[$i]['image'] = $object["file"];
				}
			}
		}

		if (($curgen==1)&&(count(find_family_ids($controller->treeid[$i]))>0)) {
			$did = 1;
			if ($i > ($controller->treesize/2) + ($controller->treesize/4)-1) $did++;
			$pos[$i]['rarrow'] = "<a href=\"pedigree.php?PEDIGREE_GENERATIONS=".$controller->OLD_PGENS."&amp;rootid=".$controller->treeid[$did]."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\" ".
				"onmouseover=\"swap_image('arrow$i',0);\" onmouseout=\"swap_image('arrow$i',0);\">".
				"<img id=\"arrow$i\" src=\"".PHPGEDVIEW_PKG_URL."images/rarrow.gif\" border=\"0\" alt=\"\" />";
		}
	}
}

if ($controller->rootPerson->canDisplayDetails()) {
	// -- print left arrow for decendants so that we can move down the tree
	$yoffset += ($controller->pbheight / 2)-10;
	//$famids = find_sfamily_ids($rootid);
	$famids = $controller->rootPerson->getSpouseFamilies();
	//-- make sure there is more than 1 child in the family with parents
	//$cfamids = find_family_ids($rootid);
	$cfamids = $controller->rootPerson->getChildFamilies();
	/*
	$num=0;
	foreach($famids as $ind=>$family) {
		$num += $family->getNumberOfChildren();
	}
	*/
	if (count($famids)>0) {
//		print "<div id=\"childarrow\" dir=\"";
//		else print "ltr\" style=\"position:absolute; left:";
//		print $basexoffset."px; top:".$yoffset."px; width:10px; height:10px; \">";
/*			if ($TEXT_DIRECTION=="rtl") print "<a href=\"javascript: ".$pgv_lang["show"]."\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow',1);\" onmouseout=\"swap_image('larrow',1);\">";
			else print "<a href=\"javascript: ".$pgv_lang["show"]."\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow',0);\" onmouseout=\"swap_image('larrow',0);\">";
			if ($TEXT_DIRECTION=="rtl") print "<img id=\"larrow\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow"]["other"]."\" border=\"0\" alt=\"\" />";
			else print ;
		"<img id=\"larrow\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow"]["other"]."\" border=\"0\" alt=\"\" /></a>"
*/
		foreach($famids as $ind=>$family) {
			if ($family!=null) {
				$husb = $family->getHusbId();
				$wife = $family->getWifeId();
				if( $gGedcom->mRootId != $husb ) $spid=$family->getHusband();
				else $spid=$family->getWife();
				if (!empty($spid)) {
//					print "\n\t\t\t\t<a href=\"pedigree.php?PEDIGREE_GENERATIONS=$controller->OLD_PGENS&amp;rootid=".$spid->getXref()."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\"><span ";
					if ($spid->canDisplayName()) {
						$name = $spid->getName();
						$name = rtrim($name);
					}
				}
			
				$children = $family->getChildren();
				foreach($children as $ind2=>$child) {
//					print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"pedigree.php?PEDIGREE_GENERATIONS=$controller->OLD_PGENS&amp;rootid=".$child->getXref()."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\"><span ";
					if ($child->canDisplayName()) {
						$name = $child->getName();
						$name = rtrim($name);
					}
				}
			}
		}
		//-- print the siblings
		foreach($cfamids as $ind=>$family) {
			if ($family!=null) {
				$children = $family->getChildren();
				if (count($children)>1) 
//					$pos[$i]['siblings'] = "<span class=\"name1\"><br />".$pgv_lang["siblings"]."<br /></span>";
				foreach($children as $ind2=>$child) {
					if (!$controller->rootPerson->equals($child) && !is_null($child)) {
						if ($child->canDisplayName()) {
							$name = $child->getName();
							$name = rtrim($name);
						}
					}
				}
			}
		}
	}
}
// -- print html footer
$maxyoffset+=120;
$gBitSmarty->assign_by_ref( "boxes", $boxes );
$gBitSmarty->assign_by_ref( "pos", $pos );
$gBitSmarty->assign( "maxyoffset", $maxyoffset );
$gBitSystem->display( 'bitpackage:phpgedview/pedigree.tpl', tra( 'Pedigree display chart' ) , array( 'display_mode' => 'display' ));
?>
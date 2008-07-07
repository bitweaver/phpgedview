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
 * $Id: pedigree.php,v 1.7 2008/07/07 18:01:12 lsces Exp $
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
$yoffset=0;				// -- used to offset the position of each box as it is generated
$xoffset=0;
$prevyoffset=0;		// -- used to track the y position of the previous box
$maxyoffset = 0;
if (!isset($brborder)) $brborder = 1;	// Avoid errors from old custom themes
for($i=($controller->treesize-1); $i>=0; $i--) {
	// -- check to see if we have moved to the next generation
	if ($i < floor($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}
	$prevyoffset = $yoffset;
	$xoffset = $controller->offsetarray[$i]["x"];
	$yoffset = $controller->offsetarray[$i]["y"];
	// -- if we are in the middle generations then we need to draw the connecting lines
	if (($curgen > $controller->talloffset) && ($curgen < $controller->PEDIGREE_GENERATIONS)) {
		if ($i%2==1) {
			if ($SHOW_EMPTY_BOXES || ($controller->treeid[$i]) || ($controller->treeid[$i+1])) {
				$vlength = ($prevyoffset-$yoffset);
				if (!$SHOW_EMPTY_BOXES && (empty($controller->treeid[$i+1]))) {
					$parent = ceil(($i-1)/2);
					$vlength = $controller->offsetarray[$parent]["y"]-$yoffset;
				}
				$linexoffset = $xoffset;
				print "<div id=\"line$i\" dir=\"";
				if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; right:";
				else print "ltr\" style=\"position:absolute; left:";
				print $linexoffset."px; top:".($yoffset+1+$controller->pbheight/2)."px; z-index: 0;\">";
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" width=\"3\" height=\"".($vlength-1)."\" alt=\"\" />";
				print "</div>";
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
		print "\n\t\t<div id=\"box";
		if (empty($controller->treeid[$i])) print "$iref";
		else print $controller->treeid[$i];
		if ($TEXT_DIRECTION=="rtl") print ".1.$iref\" style=\"position:absolute; right:";
		else print ".1.$iref\" style=\"position:absolute; left:";
		print $xoffset."px; top:".$yoffset."px; width:".($controller->pbwidth+$widthadd)."px; height:".$controller->pbheight."px; z-index: 0;\">";
		print "\n\t\t\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" dir=\"$TEXT_DIRECTION\">";
		if (($curgen > $controller->talloffset) && ($curgen < $controller->PEDIGREE_GENERATIONS)) {
			print "<tr><td>";
			print "\n\t\t\t<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" align=\"left\" hspace=\"0\" vspace=\"0\" alt=\"\" />";
			print "\n\t\t\t</td><td width=\"100%\">";
		}
		else print "<tr><td width=\"100%\">";
		$mfstyle = "";
		if (!empty($controller->treeid[$i])) {
			$person = Person::getInstance($controller->treeid[$i]);
			$indirec = $person->getGedcomRecord();
			$ct = preg_match("/1 SEX F/", $indirec);
			if ($ct>0) $mfstyle="F";
		}
		if (!isset($controller->treeid[$i])) $controller->treeid[$i] = false;
		print_pedigree_person($controller->treeid[$i], 1, $controller->show_famlink, $iref, 1);
		
		if (($curgen==1)&&(count(find_family_ids($controller->treeid[$i]))>0)) {
			$did = 1;
			if ($i > ($controller->treesize/2) + ($controller->treesize/4)-1) $did++;
			print "\n\t\t\t\t</td><td valign=\"middle\">";
			if ($view!="preview") {
				print "<a href=\"pedigree.php?PEDIGREE_GENERATIONS=".$controller->OLD_PGENS."&amp;rootid=".$controller->treeid[$did]."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\" ";
				if ($TEXT_DIRECTION=="rtl") {
					print "onmouseover=\"swap_image('arrow$i',0);\" onmouseout=\"swap_image('arrow$i',0);\">";
					print "<img id=\"arrow$i\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow"]["other"]."\" border=\"0\" alt=\"\" />";
				}
				else {
					print "onmouseover=\"swap_image('arrow$i',1);\" onmouseout=\"swap_image('arrow$i',1);\">";
					print "<img id=\"arrow$i\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow"]["other"]."\" border=\"0\" alt=\"\" />";
				}
				print "</a>";
			}
		}
		print "\n\t\t\t</td></tr></table>\n\t\t</div>";
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
		print "<div id=\"childarrow\" dir=\"";
		if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; right:";
		else print "ltr\" style=\"position:absolute; left:";
		print $basexoffset."px; top:".$yoffset."px; width:10px; height:10px; \">";
		if ($view!="preview") {
			if ($TEXT_DIRECTION=="rtl") print "<a href=\"javascript: ".$pgv_lang["show"]."\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow',1);\" onmouseout=\"swap_image('larrow',1);\">";
			else print "<a href=\"javascript: ".$pgv_lang["show"]."\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow',0);\" onmouseout=\"swap_image('larrow',0);\">";
			if ($TEXT_DIRECTION=="rtl") print "<img id=\"larrow\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["rarrow"]["other"]."\" border=\"0\" alt=\"\" />";
			else print "<img id=\"larrow\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["larrow"]["other"]."\" border=\"0\" alt=\"\" />";
			print "</a>";
		}
		print "\n\t\t</div>";
		$yoffset += ($controller->pbheight / 2)+10;
		print "\n\t\t<div id=\"childbox\" dir=\"";
		if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; right:";
		else print "ltr\" style=\"position:absolute; left:";
		print $xoffset."px; top:".$yoffset."px; width:".$controller->pbwidth."px; height:".$controller->pbheight."px; visibility: hidden;\">";
		print "\n\t\t\t<table class=\"person_box\"><tr><td>";
		foreach($famids as $ind=>$family) {
			if ($family!=null) {
				$husb = $family->getHusbId();
				$wife = $family->getWifeId();
				if($controller->rootid!=$husb) $spid=$family->getHusband();
				else $spid=$family->getWife();
				if (!empty($spid)) {
					print "\n\t\t\t\t<a href=\"pedigree.php?PEDIGREE_GENERATIONS=$controller->OLD_PGENS&amp;rootid=".$spid->getXref()."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\"><span ";
					if ($spid->canDisplayName()) {
						$name = $spid->getName();
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
				foreach($children as $ind2=>$child) {
					print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"pedigree.php?PEDIGREE_GENERATIONS=$controller->OLD_PGENS&amp;rootid=".$child->getXref()."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\"><span ";
					if ($child->canDisplayName()) {
						$name = $child->getName();
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
		foreach($cfamids as $ind=>$family) {
			if ($family!=null) {
				$children = $family->getChildren();
				if (count($children)>1) print "<span class=\"name1\"><br />".$pgv_lang["siblings"]."<br /></span>";
				foreach($children as $ind2=>$child) {
					if (!$controller->rootPerson->equals($child) && !is_null($child)) {
						print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"pedigree.php?PEDIGREE_GENERATIONS=$controller->OLD_PGENS&amp;rootid=".$child->getXref()."&amp;show_full=$controller->show_full&amp;talloffset=$controller->talloffset\"><span ";
						if ($child->canDisplayName()) {
							$name = $child->getName();
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
	}
}
// -- print html footer
$maxyoffset+=120;
$gBitSmarty->assign_by_ref( "boxes", $boxes );
$gBitSmarty->assign_by_ref( "pos", $pos );
$gBitSmarty->assign( "maxyoffset", $maxyoffset );
$gBitSystem->display( 'bitpackage:phpgedview/pedigree.tpl', tra( 'Pedigree display chart' ) );
?>
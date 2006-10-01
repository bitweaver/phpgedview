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
 * $Id: pedigree.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 * @package PhpGedView
 * @subpackage Charts
 */
require("includes/controllers/pedigree_ctrl.php");

// -- print html header information
print_header($controller->getPageTitle());
print "<div style=\"position: relative; z-index: 1;\">\n";
if ($controller->isPrintPreview()) print "<h2>".str_replace("#PEDIGREE_GENERATIONS#", convert_number($PEDIGREE_GENERATIONS), $pgv_lang["gen_ped_chart"]).":";
else print "<h2>".$pgv_lang["index_header"].":";
print "<br />".PrintReady($controller->getPersonName())."</h2>";

// -- print the form to change the number of displayed generations
if (!$controller->isPrintPreview()) {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
	</script>
	<?php
	if ($controller->max_generation) print "<span class=\"error\">".str_replace("#PEDIGREE_GENERATIONS#", convert_number($PEDIGREE_GENERATIONS), $pgv_lang["max_generation"])."</span>";
	if ($controller->min_generation) print "<span class=\"error\">".$pgv_lang["min_generation"]."</span>";
	?>
	<form name="people" method="get" action="pedigree.php">
		<input type="hidden" name="show_full" value="<?php print $controller->show_full; ?>" />
		<input type="hidden" name="talloffset" value="<?php print $controller->talloffset; ?>" />
		<table class="pedigree_table <?php print $TEXT_DIRECTION; ?>" width="225">
			<tr>
				<td colspan="2" class="topbottombar" style="text-align:center; ">
					<?php print $pgv_lang["options"]; ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap"><?php print_help_link("rootid_help", "qm"); ?>
					<?php print $pgv_lang["root_person"]; ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="<?php print $controller->rootid; ?>" />
        			<?php print_findindi_link("rootid",""); ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
				<?php print_help_link("PEDIGREE_GENERATIONS_help", "qm"); ?>
				<?php print $pgv_lang["generations"]; ?>
				</td>
				<td class="optionbox">
					<select name="PEDIGREE_GENERATIONS">
					<?php
						for ($i=3; $i<=$MAX_PEDIGREE_GENERATIONS; $i++) {
							print "<option value=\"".$i."\"" ;
							if ($i == $controller->OLD_PGENS) print "selected=\"selected\" ";
							print ">".$i."</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("talloffset_help", "qm"); ?>
					<?php print $pgv_lang["orientation"]; ?>
				</td>
				<td class="optionbox">
					<input type="radio" name="talloffset" value="0" <?php if (!$controller->talloffset) print " checked=\"checked\" "; ?> onclick="document.people.talloffset.value='1';" /> <?php print $pgv_lang["portrait"]; ?>
					<br /><input type="radio" name="talloffset" value="1" <?php if ($controller->talloffset) print "checked=\"checked\" "; ?> onclick="document.people.talloffset.value='0';" /><?php print $pgv_lang["landscape"]; ?>
					<br />
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("show_full_help", "qm"); ?>
					<?php print $pgv_lang["show_details"]; ?>
				</td>
				<td class="optionbox">
					<input type="checkbox" value="<?php if ($controller->show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';"; else print "0\" onclick=\"document.people.show_full.value='1';"; ?>" />
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="2">
					<input type="submit" value="<?php print $pgv_lang["view"]; ?>" />
				</td>
			</tr>
		</table>
	</form>
<?php } ?>
</div>
<div id="pedigree_chart<?php if ($TEXT_DIRECTION=="rtl") print "_rtl"; ?>" <?php if ($controller->isPrintPreview()) print " style=\"top: 1px;\""; else print "style=\"z-index: 1;\""; ?> >
<?php
//-- print the boxes
$curgen = 1;
$yoffset=0;				// -- used to offset the position of each box as it is generated
$xoffset=0;
$prevyoffset=0;		// -- used to track the y position of the previous box
$maxyoffset = 0;
for($i=($controller->treesize-1); $i>=0; $i--) {
	// -- check to see if we have moved to the next generation
	if ($i < floor($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}
	$prevyoffset = $yoffset;
	$xoffset = $controller->offsetarray[$i]["x"];
	$yoffset = $controller->offsetarray[$i]["y"];
	// -- if we are in the middle generations then we need to draw the connecting lines
	if (($curgen >(1+$controller->talloffset)) && ($curgen < $controller->PEDIGREE_GENERATIONS)) {
		if ($i%2==1) {
			if ($SHOW_EMPTY_BOXES || ($controller->treeid[$i]) || ($controller->treeid[$i+1])) {
				$vlength = ($prevyoffset-$yoffset);
				if (!$SHOW_EMPTY_BOXES && (empty($controller->treeid[$i+1]))) {
					$parent = ceil(($i-1)/2);
					$vlength = $controller->offsetarray[$parent]["y"]-$yoffset;
				}
				$linexoffset = $xoffset-1;
				print "<div id=\"line$i\" dir=\"";
				if ($TEXT_DIRECTION=="rtl") print "rtl\" style=\"position:absolute; right:";
				else print "ltr\" style=\"position:absolute; left:";
				print $linexoffset."px; top:".($yoffset+$controller->pbheight/2)."px; z-index: 0;\">";
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]."\" width=\"3\" height=\"".$vlength."\" alt=\"\" />";
				print "</div>";
			}
		}
	}
	// -- draw the box
	if (!empty($controller->treeid[$i]) || $SHOW_EMPTY_BOXES) {
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
		if (($curgen >(1+$controller->talloffset)) && ($curgen < $controller->PEDIGREE_GENERATIONS)) {
			print "<tr><td>";
			print "\n\t\t\t<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" align=\"left\" hspace=\"0\" vspace=\"0\" alt=\"\" />";
			print "\n\t\t\t</td><td width=\"100%\">";
		}
		else print "<tr><td width=\"100%\">";
		$mfstyle = "";
		if (!empty($controller->treeid[$i])) {
			$indirec = find_person_record($controller->treeid[$i]);
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
?>
</div>
<script language="JavaScript" type="text/javascript">
	content_div = document.getElementById("content");
	if (content_div) {
		content_div.style.height = <?php print $maxyoffset; ?> + "px";
	}
	<?php if ($view=="preview") { ?>
	pedigree_div = document.getElementById("pedigree_chart");
	if (pedigree_div) {
		pedigree_div.style.height = <?php print $maxyoffset; ?> + "px";
	}
	<?php } ?>
</script>
<?php
if ($controller->isPrintPreview()) print "<br /><br /><br />";
print_footer();
?>
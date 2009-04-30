<?php
/**
* Family Navigator for phpGedView
*
* Display immediate family members table for fast navigation
* ( Currently used with Facts and Details tab, and Album Tab pages )
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2007 to 2008  PGV Development Team.  All rights reserved.
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
* @subpackage Includes
* @version $Id: family_nav.php,v 1.1 2009/04/30 18:32:43 lsces Exp $
* @author Brian Holland
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FAMILY_NAV_PHP', '');

// -----------------------------------------------------------------------------
// Function Family Nav for PHPGedView - called by individual_ctrl.php
// -----------------------------------------------------------------------------
// function family_nav() {
// ------------------------------------------------------------------------------

global $edit, $tabno, $mediacnt, $GEDCOM, $pid;
$edit=$edit;
global $show_full, $tabno;
$show_full="1";

// Gets current clicked tab to set $tabno -----------
if (isset($_COOKIE['lastclick'])) {
	$tabno=$_COOKIE['lastclick']-1;
}else{
	$tabno=0;
}


// Debug only -----------------------------------------
// echo "Lastclick =" . $_COOKIE['lastclick'];
//echo "<br />";
//print "TAB =" . $tabno;

// =====================================================================

//     Start Family Nav Table ----------------------------
	echo "<table class=\"facts_table\" width='230' cellpadding=\"0\">";
		global $pgv_lang, $SHOW_ID_NUMBERS, $PGV_IMAGE_DIR, $PGV_IMAGES;
		global $spouselinks, $parentlinks, $DeathYr, $BirthYr;
		global $TEXT_DIRECTION;

		$personcount=0;
		$families = $this->indi->getChildFamilies();

		//-- parent families -------------------------------------------------------------
		foreach($families as $famid=>$family) {
			$label = $this->indi->getChildFamilyLabel($family);
			$people = $this->buildFamilyList($family, "parents");
			$styleadd = "";
			?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php 
				echo "<a href=family.php?famid=".$famid.">";
				//echo "<b>". $pgv_lang["parent_family"] ."&nbsp;&nbsp;(".$famid.")</b>";
				echo "<b>".$pgv_lang["parent_family"]."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				echo "</a>"; 
				?>
				</td>
			</tr>
			<?php
			if (isset($people["husb"])) {
				$menu = array();
				$menu["label"] = "&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n";
				$menu["submenuclass"] = "submenu";
				$menu["items"] =  array();
				$submenu = array();
				$submenu["label"]  = print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$submenu["label"] .= PrintReady($parentlinks);
				$menu["items"][] = $submenu;

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php // print $people["husb"]->getLabel(); ?>
						<?php
							print_menu($menu);
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["husb"]); ?>">
						<?php
						print "<a href=\"".encode_url($people["husb"]->getLinkUrl()."&tab={$tabno}")."\">";
						print PrintReady($people["husb"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["wife"])) {
				$menu = array();
				$menu["label"] = "&nbsp;" . $people["wife"]->getLabel() . "&nbsp;". "\n";
				$menu["submenuclass"] = "submenu";
				$menu["items"] =  array();
				$submenu = array();
				$submenu["label"]  = print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$submenu["label"] .= PrintReady($parentlinks);
				$menu["items"][] = $submenu;

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>">
						<?php //print $people["wife"]->getLabel(); ?>
						<?php
							print_menu($menu);
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["wife"]); ?>">
						<?php
						print "<a href=\"".encode_url($people["wife"]->getLinkUrl()."&tab={$tabno}")."\">";
						print PrintReady($people["wife"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach($people["children"] as $key=>$child) {
				if ($pid == $child->getXref() ){
				}else{
					$menu = array();
					$menu["label"] = $child->getLabel() . "\n";
					$menu["submenuclass"] = "submenu";
					$menu["items"] =  array();
					$submenu = array();
					$submenu["label"]  = print_pedigree_person_nav($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
					$submenu["label"] .= PrintReady($spouselinks);
					$menu["items"][] = $submenu;
				}
				if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
				if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }

					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>">
						<?php
						if ($pid == $child->getXref() ) {
							print $child->getLabel();
						}else{
							print_menu($menu);
						}
						?>
						</td>
						<td align="center" class="<?php print $this->getPersonStyle($child); ?>">
							<?php
							if ($pid == $child->getXref()) {
								print PrintReady($child->getFullName());
								print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							}else{
								print "<a href=\"".encode_url($child->getLinkUrl()."&tab={$tabno}")."\">";
								print PrintReady($child->getFullName());
								print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
								print "</a>";
							}
							?>
						</td>
					</tr>
					<?php
					$elderdate = $child->getBirthDate();
				}
			}
		}

		//-- step families ----------------------------------------------------------------
		foreach($this->indi->getStepFamilies() as $famid=>$family) {
			$label = $this->indi->getStepFamilyLabel($family);
			$people = $this->buildFamilyList($family, "step");
			if ($people){
				echo "<tr><td><br /></td><td></td></tr>";
			}
			$styleadd = "";
			$elderdate = "";
			?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php 
				echo "<a href=family.php?famid=".$famid.">"; 
				echo "<b>".$pgv_lang["step_parent_family"]."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				echo "</a>"; 
				?>
				</td>
			</tr>
			<?php
			
			//if (isset($people["husb"]) && $people["husb"]->getLabel() == ".") {
			if (isset($people["husb"]) ) {
				$menu = array();
				if ($people["husb"]->getLabel() == ".") {
					$menu["label"] = "&nbsp;" . $pgv_lang["stepdad"] . "&nbsp;". "\n";
				}else{
					$menu["label"] = "&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n";
				}
				$menu["submenuclass"] = "submenu";
				$menu["items"] =  array();
				$submenu = array();
				$submenu["label"]  = print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$submenu["label"] .= PrintReady($parentlinks);
				$menu["items"][] = $submenu;

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>

				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php
							print_menu($menu);
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["husb"]); ?>" >
						<?php
						print "<a href=\"".encode_url($people["husb"]->getLinkUrl()."&tab={$tabno}")."\">";
						print PrintReady($people["husb"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
				$elderdate = $people["husb"]->getBirthDate();
			}

			$styleadd = "";
			//if (isset($people["wife"]) && $people["wife"]->getLabel() == ".") {
			if (isset($people["wife"]) ) {
				$menu = array();
				if ($people["wife"]->getLabel() == ".") {
					$menu["label"] = "&nbsp;" . $pgv_lang["stepmom"] . "&nbsp;". "\n";
				}else{
					$menu["label"] = "&nbsp;" . $people["wife"]->getLabel() . "&nbsp;". "\n";
				}
				$menu["submenuclass"] = "submenu";
				$menu["items"] =  array();
				$submenu = array();
				$submenu["label"]  = print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$submenu["label"] .= PrintReady($parentlinks);
				$menu["items"][] = $submenu;

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php
							print_menu($menu);
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["wife"]); ?>">
						<?php
						print "<a href=\"".encode_url($people["wife"]->getLinkUrl()."&tab={$tabno}")."\">";
						print PrintReady($people["wife"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			$styleadd = "";
			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach($people["children"] as $key=>$child) {
					$menu = array();
					$menu["label"] = $child->getLabel() . "\n";
					$menu["submenuclass"] = "submenu";
					$menu["items"] =  array();
					$submenu = array();
					$submenu["label"]  = print_pedigree_person_nav($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
					$submenu["label"] .= PrintReady($spouselinks);
					$menu["items"][] = $submenu;

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
							<?php //print $child->getLabel(); ?>
							<?php
								print_menu($menu);
							?>
						</td>
						<td align="center" class="<?php print $this->getPersonStyle($child); ?>">
							<?php
							print "<a href=\"".encode_url($child->getLinkUrl()."&tab={$tabno}")."\">";
							print PrintReady($child->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
							?>
						</td>
					</tr>
					<?php
					//$elderdate = $child->getBirthDate();
				}
			}
		}

		//-- spouse and children --------------------------------------------------
		$families = $this->indi->getSpouseFamilies();
		foreach($families as $famid=>$family) {
		echo "<tr><td><br /></td><td></td></tr>";
		?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php 
				echo "<a href=family.php?famid=".$famid.">"; 
				echo "<b>".$pgv_lang["immediate_family"]."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				echo "</a>"; 
				?>
				</td>
			</tr>
		<?php

			//$personcount = 0;
			$people = $this->buildFamilyList($family, "spouse");
			if ($this->indi->equals($people["husb"])){
				$spousetag = 'WIFE';
			}else{
				$spousetag = 'HUSB';
			}
			$styleadd = "";
			if ( isset($people["husb"]) && $spousetag == 'HUSB' ) {
				$menu = array();
				$menu["label"] = "&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n";
				$menu["submenuclass"] = "submenu";
				$menu["items"] =  array();
				$submenu = array();
				$submenu["label"]  = print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$submenu["label"] .= PrintReady($parentlinks);
				$menu["items"][] = $submenu;

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php // print $people["husb"]->getLabel(); ?>
						<?php
							print_menu($menu);
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["husb"]); ?>">
						<?php
						if ($pid == $people["husb"]->getXref()) {
							print PrintReady($people["husb"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						}else{
							print "<a href=\"".encode_url($people["husb"]->getLinkUrl()."&tab={$tabno}")."\">";
							print PrintReady($people["husb"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
						}
						?>
					</td>
				</tr>
				<?php
			}

			if ( isset($people["wife"]) && $spousetag == 'WIFE') {
				$menu = array();
				$menu["label"] = "&nbsp;" . $people["wife"]->getLabel() . "&nbsp;". "\n";
				$menu["submenuclass"] = "submenu";
				$menu["items"] =  array();
				$submenu = array();
				$submenu["label"]  = print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$submenu["label"] .= PrintReady($parentlinks);
				$menu["items"][] = $submenu;

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php // print $people["wife"]->getLabel(); ?>
						<?php
							print_menu($menu);
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["wife"]); ?>">
						<?php
						if ($pid == $people["wife"]->getXref()) {
							print PrintReady($people["wife"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						}else{
							print "<a href=\"".encode_url($people["wife"]->getLinkUrl()."&tab={$tabno}")."\">";
							print PrintReady($people["wife"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
						}
						?>
					</td>
				</tr>
				<?php
			}

			$styleadd = "";
			if (isset($people["children"])) {
				foreach($people["children"] as $key=>$child) {
					$menu = array();
					$menu["label"] = "&nbsp;" . $child->getLabel() . "&nbsp;". "\n";
					$menu["submenuclass"] = "submenu";
					$menu["items"] =  array();
					$submenu = array();
					$submenu["label"]  = print_pedigree_person_nav($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
					$submenu["label"] .= PrintReady($spouselinks);
					$menu["items"][] = $submenu;

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
							<?php //print $child->getLabel(); ?>
							<?php
								print_menu($menu);
							?>
						</td>
						<td align="center" class="<?php print $this->getPersonStyle($child); ?>">
							<?php
							print "<a href=\"".encode_url($child->getLinkUrl()."&tab={$tabno}")."\">";
							print PrintReady($child->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
							?>
						</td>
					</tr>
					<?php
				}
			}

		}
		echo "</table>";


// -----------------------------------------------------------------------------
// }
// -----------------------------------------------------------------------------
// End Family Nav Table
// -----------------------------------------------------------------------------

// ==================================================================
require_once 'includes/functions/functions_charts.php';
/**
* print the information for an individual chart box
*
* find and print a given individuals information for a pedigree chart
* @param string $pid the Gedcom Xref ID of the   to print
* @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
* @param boolean $show_famlink set to true to show the icons for the popup links and the zoomboxes
* @param int $count on some charts it is important to keep a count of how many boxes were printed
*/
function print_pedigree_person_nav($pid, $style=1, $show_famlink=true, $count=0, $personcount="1") {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $PRIV_PUBLIC, $factarray, $ZOOM_BOXES, $LINK_ICONS, $view, $SCRIPT_NAME, $GEDCOM;
	global $pgv_lang, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_ID_NUMBERS, $SHOW_PEDIGREE_PLACES;
	global $CONTACT_EMAIL, $CONTACT_METHOD, $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $PGV_IMAGE_DIR, $PGV_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno, $theme_name, $spousetag;
	global $natdad, $natmom;

	if ($style != 2) $style=1;
	if (empty($show_full)) $show_full = 0;
	if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

	if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;

	$person=Person::getInstance($pid);
	if ($pid==false || empty($person)) {
		$spouselinks  = false;
		$parentlinks  = false;
		$step_parentlinks = false;
	}

	$tmp=array('M'=>'','F'=>'F', 'U'=>'NN');
	$isF=$tmp[$person->getSex()];
	$spouselinks = "";
	$parentlinks = "";
	$step_parentlinks   = "";
	$disp=$person->canDisplayDetails();

	if ($person->canDisplayName()) {
		if ($show_famlink && (empty($SEARCH_SPIDER))) {
			if ($LINK_ICONS!="disabled") {
				//-- draw a box for the family popup
				if ($TEXT_DIRECTION=="rtl") {
				$spouselinks .= "\n\t\t\t<table class=\"person_box$isF\" style=\" position: absolute; top: -19px; left: -1px; \"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$spouselinks .= "<font size=\"1\"><b>" . $pgv_lang['family'] . "</b><br /></font>";
				$parentlinks .= "\n\t\t\t<table class=\"person_box$isF\" style=\" position: absolute; top: -19px; left: -1px; \"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$parentlinks .= "<font size=\"1\"><b>" . $pgv_lang['parents'] . "</b><br /></font>";
				$step_parentlinks .= "\n\t\t\t<table class=\"person_box$isF\" style=\" position: absolute; top: -19px; left: -1px; \"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$step_parentlinks .= "<font size=\"1\"><b>" . $pgv_lang['parents'] . "</b><br /></font>";
				}else{
				$spouselinks .= "\n\t\t\t<table class=\"person_box$isF\" style=\" position: absolute; top: -19px; right: -1px; \"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$spouselinks .= "<font size=\"1\"><b>" . $pgv_lang['family'] . "</b><br /></font>";
				$parentlinks .= "\n\t\t\t<table class=\"person_box$isF\" style=\" position: absolute; top: -19px; right: -1px; \"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$parentlinks .= "<font size=\"1\"><b>" . $pgv_lang['parents'] . "</b><br /></font>";
				$step_parentlinks .= "\n\t\t\t<table class=\"person_box$isF\" style=\" position: absolute; top: -19px; right: -1px; \"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$step_parentlinks .= "<font size=\"1\"><b>" . $pgv_lang['parents'] . "</b><br /></font>";
				}
				$persons       = "";
				$person_parent = "";
				$person_step   = "";



				//-- parent families --------------------------------------
				$fams = $person->getChildFamilies();
				foreach($fams as $famid=>$family) {

					if (!is_null($family)) {
						$husb = $family->getHusband($person);
						$wife = $family->getWife($person);
						// $spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						// Husband ------------------------------
						if ($husb || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = $pgv_lang["familybook_chart"].": ".$famid;
							}else{
								$title = $famid." :".$pgv_lang["familybook_chart"];
							}
							if ($husb) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = $pgv_lang["indi_info"].": ".$husb->getXref();
								}else{
									$title = $husb->getXref()." :".$pgv_lang["indi_info"];
								}
								$parentlinks .= "<a href=\"".encode_url($husb->getLinkUrl()."&amp;tab={$tabno}")."\">";
								$parentlinks .= PrintReady($husb->getFullName());
								$parentlinks .= "</a>";
								$parentlinks .= "<br />";
								$natdad = "yes";
							}
						}

						// Wife ------------------------------
						if ($wife || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = $pgv_lang["familybook_chart"].": ".$famid;
							}else{
								$title = $famid." :".$pgv_lang["familybook_chart"];
							}
							if ($wife) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = $pgv_lang["indi_info"].": ".$wife->getXref();
								}else{
									$title = $wife->getXref()." :".$pgv_lang["indi_info"];
								}
								$parentlinks .= "<a href=\"".encode_url($wife->getLinkUrl()."&amp;tab={$tabno}")."\">";
								$parentlinks .= PrintReady($wife->getFullName());
								$parentlinks .= "</a>";
								$parentlinks .= "<br />";
								$natmom = "yes";
							}
						}
					}
				}

				//-- step families -----------------------------------------
				$fams = $person->getStepFamilies();
				foreach($fams as $famid=>$family) {
					if (!is_null($family)) {
						$husb = $family->getHusband($person);
						$wife = $family->getWife($person);
						// $spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						if ($natdad == "yes") {
						}else{
							// Husband -----------------------
							if ( ($husb || $num>0) && $husb->getLabel() != "." ) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = $pgv_lang["familybook_chart"].": ".$famid;
								}else{
									$title = $famid." :".$pgv_lang["familybook_chart"];
								}
								if ($husb) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = $pgv_lang["indi_info"].": ".$husb->getXref();
									}else{
										$title = $husb->getXref()." :".$pgv_lang["indi_info"];
									}
									$parentlinks .= "<a href=\"".encode_url($husb->getLinkUrl()."&amp;tab={$tabno}")."\">";
									$parentlinks .= PrintReady($husb->getFullName());
									$parentlinks .= "</a>";
									$parentlinks .= "<br />";
								}
							}
						}

						if ($natmom == "yes") {
						}else{
							// Wife ----------------------------
							if ($wife || $num>0) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = $pgv_lang["familybook_chart"].": ".$famid;
								}else{
									$title = $famid." :".$pgv_lang["familybook_chart"];
								}
								if ($wife) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = $pgv_lang["indi_info"].": ".$wife->getXref();
									}else{
										$title = $wife->getXref()." :".$pgv_lang["indi_info"];
									}
									$parentlinks .= "<a href=\"".encode_url($wife->getLinkUrl()."&amp;tab={$tabno}")."\">";
									$parentlinks .= PrintReady($wife->getFullName());
									$parentlinks .= "</a>";
									$parentlinks .= "<br />";
								}
							}
						}
					}
				}

				// Spouse Families -------------------------------------- @var $family Family
				$fams = $person->getSpouseFamilies();
				foreach($fams as $famid=>$family) {
					if (!is_null($family)) {
						$spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						// Spouse ------------------------------
						if ($spouse || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = $pgv_lang["familybook_chart"].": ".$famid;
							}else{
								$title = $famid." :".$pgv_lang["familybook_chart"];
							}
							if ($spouse) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = $pgv_lang["indi_info"].": ".$spouse->getXref();
								}else{
									$title = $spouse->getXref()." :".$pgv_lang["indi_info"];
								}
								$spouselinks .= "<a href=\"".encode_url($spouse->getLinkUrl()."&amp;tab={$tabno}")."\">";
								$spouselinks .= PrintReady($spouse->getFullName());
								$spouselinks .= "</a><br />";
								if ($spouse->getFullName() != "") {
									$persons = "Yes";
								}
							}
						}

						// Children ------------------------------   @var $child Person
						foreach($children as $c=>$child) {
							if ($child) {
								$persons="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = $pgv_lang["indi_info"].": ".$child->getXref();
									$spouselinks .= "o&nbsp;&nbsp;";
									$spouselinks .= "<a href=\"".encode_url($child->getLinkUrl()."&amp;tab={$tabno}")."\">";
									$spouselinks .= PrintReady($child->getFullName());
									$spouselinks .= "</a>";
									$spouselinks .= "<br />";
								}else{
									$title = $child->getXref()." :".$pgv_lang["indi_info"];
									$spouselinks .= "<a href=\"".encode_url($child->getLinkUrl()."&amp;tab={$tabno}")."\">";
									$spouselinks .= PrintReady($child->getFullName() );
									$spouselinks .= "</a>";
									$spouselinks .= "&nbsp;&nbsp;o";
									$spouselinks .= "<br />";
								}
							}
						}
					}
				}
				?>

				<?php if ($theme_name=="Xenea" || $theme_name=="Standard" || $theme_name=="Wood" || $theme_name=="Ocean") { ?>
				<style type="text/css" rel="stylesheet">
					a:hover .name2 { color: #222222; }
				</style>
				<?php } ?>

				<?php
				if ($persons != "Yes") {
					$spouselinks  .= "(" . $pgv_lang['none'] . ")</td></tr></table>\n\t\t";
				}else{
					$spouselinks  .= "</td></tr></table>\n\t\t";
				}

				if ($person_parent != "Yes") {
					$parentlinks .= "(" . $pgv_lang['unknown'] . ")</td></tr></table>\n\t\t";
				}else{
					$parentlinks .= "</td></tr></table>\n\t\t";
				}

				if ($person_step != "Yes") {
					$step_parentlinks .= "(" . $pgv_lang['unknown'] . ")</td></tr></table>\n\t\t";
				}else{
					$step_parentlinks .= "</td></tr></table>\n\t\t";
				}
			}
		}
	}
}
// ==============================================================
?>

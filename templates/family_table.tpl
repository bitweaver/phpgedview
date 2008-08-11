	<table class="facts_table">
		<?php
		//$personcount = 0;
		$people = $controller->buildFamilyList($family, "parents");
		$styleadd = "";
		if (isset($people["newhusb"])) {
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_labelblue"><?php print $people["newhusb"]->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($people["newhusb"]); ?>">
				<?php print_pedigree_person($people["newhusb"]->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		if (isset($people["husb"])) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $people["husb"]->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($people["husb"]); ?>">
				<?php print_pedigree_person($people["husb"]->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		else if (!isset($people["newhusb"])) {
			if ((!$controller->isPrintPreview()) && ($gGedcom->isEditable())&&($controller->indi->canDisplayDetails())) {
			?>
			<tr><td class="facts_label"><?php print $pgv_lang["add_father"]; ?></td>
			<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?> <a href="javascript <?php print $pgv_lang["add_father"]; ?>" onclick="return addnewparentfamily('<?php print $controller->pid; ?>', 'HUSB', '<?php print $famid; ?>');"><?php print $pgv_lang["add_father"]; ?></a></td>
			</tr>
			<?php
			}
		}
		$styleadd = "";
		if (isset($people["newwife"])) {
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_labelblue"><?php print $people["newwife"]->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($people["newwife"]); ?>">
				<?php print_pedigree_person($people["newwife"]->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		if (isset($people["wife"])) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $people["wife"]->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($people["wife"]); ?>">
				<?php print_pedigree_person($people["wife"]->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		else if (!isset($people["newwife"])) {
			if ((!$controller->isPrintPreview()) && ($gGedcom->isEditable())&&($controller->indi->canDisplayDetails())) {
				?>
				<tr><td class="facts_label"><?php print $pgv_lang["add_mother"]; ?></td>
				<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?> <a href="javascript:;" onclick="return addnewparentfamily('<?php print $controller->pid; ?>', 'WIFE', '<?php print $famid; ?>');"><?php print $pgv_lang["add_mother"]; ?></a></td>
				</tr>
				<?php
			}
		}
		$styleadd = "blue";
		if (isset($people["newchildren"])) {
			foreach($people["newchildren"] as $key=>$child) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $child->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($child); ?>">
				<?php print_pedigree_person($child->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			}
		}
		$styleadd = "";
		if (isset($people["children"])) {
			foreach($people["children"] as $key=>$child) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $child->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($child); ?>">
				<?php print_pedigree_person($child->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			}
		}
		$styleadd = "red";
		if (isset($people["delchildren"])) {
			foreach($people["delchildren"] as $key=>$child) {
			?>
			<tr>
				<td class="facts_label<?php print $styleadd; ?>"><?php print $child->getLabel(); ?></td>
				<td class="<?php print $controller->getPersonStyle($child); ?>">
				<?php print_pedigree_person($child->getXref(), 2, !$controller->isPrintPreview(), 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			}
		}
		if (isset($family) && (!$controller->isPrintPreview()) && ($gGedcom->isEditable())&&($controller->indi->canDisplayDetails())) {
			?>
			<tr>
				<td class="facts_label"><?php echo $pgv_lang["add_child_to_family"]; ?></td>
				<td class="facts_value"><?php print_help_link("add_sibling_help", "qm"); ?>
					<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_sibling"]; ?></a>
					<span style='white-space:nowrap;'>
						<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>','M');"><?php print "[<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["brother"] . "\" alt=\"" . $pgv_lang["brother"] . "\" class=\"sex_image\" />]"?></a>
						<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>','F');"><?php print "[<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sexf"]["small"] . "\" title=\"" . $pgv_lang["sister"] . "\" alt=\"" . $pgv_lang["sister"] . "\" class=\"sex_image\" />]"?></a>
					</span>
				</td>
			</tr>
			<?php
		}
		?>
	</table>

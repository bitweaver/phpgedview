	<table class="facts_table">
		<?php
		//$personcount = 0;
		$people = $controller->buildFamilyList($family, "spouse");
		$styleadd = "";
		if ($controller->indi->equals($people["husb"])) $spousetag = 'WIFE';
		else $spousetag = 'HUSB';
//		if (isset($people["newhusb"]) && !$people["newhusb"]->equals($controller->indi)) {
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
//		if (isset($people["husb"]) && !$people["husb"]->equals($controller->indi)) {
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
		$styleadd = "";
//		if (isset($people["newwife"]) && !$people["newwife"]->equals($controller->indi)) {
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
//		if (isset($people["wife"]) && !$people["wife"]->equals($controller->indi)) {
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
		if ($spousetag=="WIFE" && !isset($people["newwife"]) && !isset($people["wife"])) {
			if ((!$controller->isPrintPreview()) && ($gGedcom->isEditable())&&($controller->indi->canDisplayDetails())) {
				?>
				<tr><td class="facts_label"><?php print $pgv_lang["add_wife"]; ?></td>
				<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php print $famid; ?>', 'WIFE');"><?php print $pgv_lang["add_wife_to_family"]; ?></a></td>
				</tr>
				<?php
			}
		}
		if ($spousetag=="HUSB" && !isset($people["newhusb"]) && !isset($people["husb"])) {
			if ((!$controller->isPrintPreview()) && ($gGedcom->isEditable())&&($controller->indi->canDisplayDetails())) {
				?>
				<tr><td class="facts_label"><?php print $pgv_lang["add_husb"]; ?></td>
				<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php print $famid; ?>', 'HUSB');"><?php print $pgv_lang["add_husb_to_family"]; ?></a></td>
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
				<td class="facts_value"><?php print_help_link("add_son_daughter_help", "qm"); ?>
					<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_son_daughter"]; ?></a>
					<span style='white-space:nowrap;'>
						<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>','M');"><?php print "[<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sex"]["small"] . "\" title=\"" . $pgv_lang["son"] . "\" alt=\"" . $pgv_lang["son"] . "\" class=\"sex_image\" />]"?></a>
						<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>','F');"><?php print "[<img src=\"$PGV_IMAGE_DIR/" . $PGV_IMAGES["sexf"]["small"] . "\" title=\"" . $pgv_lang["daughter"] . "\" alt=\"" . $pgv_lang["daughter"] . "\" class=\"sex_image\" />]"?></a>
					</span>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
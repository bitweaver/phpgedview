		<td class="<?php echo $TEXT_DIRECTION; ?> noprint" valign="top">
			<div class="accesskeys">
			<a class="accesskeys" href="<?php print "pedigree.php?rootid=$pid";?>" title="<?php print $pgv_lang["pedigree_chart"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_individual_pedigree"]; ?>"><?php print $pgv_lang["pedigree_chart"] ?></a>
			<a class="accesskeys" href="<?php print "descendancy.php?pid=$pid";?>" title="<?php print $pgv_lang["descend_chart"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_individual_descendancy"]; ?>"><?php print $pgv_lang["descend_chart"] ?></a>
			<a class="accesskeys" href="<?php print "timeline.php?pids[]=$pid";?>" title="<?php print $pgv_lang["timeline_chart"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_individual_timeline"]; ?>"><?php print $pgv_lang["timeline_chart"] ?></a>
			<?php
				if (!empty($controller->user["gedcomid"][$GEDCOM])) {
			?>
			<a class="accesskeys" href="<?php print "relationship.php?pid1=".$controller->user["gedcomid"][$GEDCOM]."&amp;pid2=".$controller->pid;?>" title="<?php print $pgv_lang["relationship_to_me"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_individual_relation_to_me"]; ?>"><?php print $pgv_lang["relationship_to_me"] ?></a>
			<?php 	}
			if ($controller->canShowGedcomRecord()) {
			?>
			<a class="accesskeys" href="javascript:show_gedcom_record();" title="<?php print $pgv_lang["view_gedcom"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_individual_gedcom"]; ?>"><?php print $pgv_lang["view_gedcom"] ?></a>
			<?php
			}
		?>
		</div>
		<table class="sublinks_table" cellspacing="4" cellpadding="0">
			<tr>
				<td class="list_label <?php echo $TEXT_DIRECTION; ?>" colspan="5"><?php echo $pgv_lang["indis_charts"]; ?></td>
			</tr>
			<tr>
				<td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
				<?php
				//-- get charts menu from menubar
				$menubar = new MenuBar();
				$menu = $menubar->getChartsMenu($controller->pid); $menu->printMenu();
				?>
				</td>
				<td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
				<?php
				$menu = $menubar->getListsMenu($controller->indi->getSurname()); $menu->printMenu();
				if (file_exists("reports/individual.xml")) {?>
					</td><td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
					<?php
					//-- get reports menu from menubar
					$menu = $menubar->getReportsMenu($controller->pid); $menu->printMenu();
				}
				if ($controller->userCanEdit()) {
				?>
				</td>
				<td class="sublinks_cell <?php echo $TEXT_DIRECTION;?>">
				<?php $menu = $controller->getEditMenu(); $menu->printMenu();
				}
				if ($controller->canShowOtherMenu()) {
				?>
				</td>
				<td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
				<?php $menu = $controller->getOtherMenu(); $menu->printMenu();
				}
				?>
				</td>
			</tr>
		</table><br />

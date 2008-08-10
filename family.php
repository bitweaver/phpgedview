<?php
/**
 * Parses gedcom file and displays information about a family.
 *
 * You must supply a $famid value with the identifier for the family.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * @version $Id: family.php,v 1.5 2008/08/10 11:46:26 lsces Exp $
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require_once 'includes/controllers/family_ctrl.php';

print_header($controller->getPageTitle());
// completely prevent display if privacy dictates so
if (!$controller->family->disp) {
	print_privacy_error($CONTACT_EMAIL);
	print_footer();
	exit;
}
?>
<?php if ($controller->family->isMarkedDeleted()) print "<span class=\"error\">".$pgv_lang["record_marked_deleted"]."</span>"; ?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record(shownew) {
		fromfile="";
		if (shownew=="yes") fromfile='&fromfile=1';
		var recwin = window.open("gedrecord.php?pid=<?php print $controller->getFamilyID(); ?>"+fromfile, "_blank", "top=50,left=50,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
	}
	function showchanges() {
		window.location = 'family.php?famid=<?php print $controller->famid; ?>&show_changes=yes';
	}
//-->
</script>
<table>
	<tr>
		<td>
		<?php
		print print_family_parents($controller->getFamilyID());
		if (!$controller->isPrintPreview() && $controller->display && PGV_USER_CAN_EDIT) {
		$husb = $controller->getHusband();
		if (empty($husb)) { ?>
			<?php print_help_link("edit_add_parent_help", "qm"); ?>
			<a href="javascript <?php print $pgv_lang["add_father"]; ?>" onclick="return addnewparentfamily('', 'HUSB', '<?php print $controller->famid; ?>');"><?php print $pgv_lang["add_father"]; ?></a><br />
		<?php }
		$wife = $controller->getWife();
		if (empty($wife))  { ?>
			<?php print_help_link("edit_add_parent_help", "qm"); ?>
			<a href="javascript <?php print $pgv_lang["add_mother"]; ?>" onclick="return addnewparentfamily('', 'WIFE', '<?php print $controller->famid; ?>');"><?php print $pgv_lang["add_mother"]; ?></a><br />
		<?php }
		}
		?></td>
		<td valign="top" class="noprint">
			<div class="accesskeys">
			<?php
                        if (empty($SEARCH_SPIDER)) {
                        ?>
				<a class="accesskeys" href="<?php print 'timeline.php?pids[0]=' . $controller->parents['HUSB'].'&amp;pids[1]='.$controller->parents['WIFE'];?>" title="<?php print $pgv_lang['parents_timeline'] ?>" tabindex="-1" accesskey="<?php print $pgv_lang['accesskey_family_parents_timeline']; ?>"><?php print $pgv_lang['parents_timeline'] ?></a>
				<a class="accesskeys" href="<?php print 'timeline.php?' . $controller->getChildrenUrlTimeline();?>" title="<?php print $pgv_lang["children_timeline"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang['accesskey_family_children_timeline']; ?>"><?php print $pgv_lang['children_timeline'] ?></a>
				<a class="accesskeys" href="<?php print 'timeline.php?pids[0]=' .$controller->getHusband().'&amp;pids[1]='.$controller->getWife().'&amp;'.$controller->getChildrenUrlTimeline(2);?>" title="<?php print $pgv_lang['family_timeline'] ?>" tabindex="-1" accesskey="<?php print $pgv_lang['accesskey_family_timeline']; ?>"><?php print $pgv_lang['family_timeline'] ?></a>
				<?php if ($SHOW_GEDCOM_RECORD) { ?>
				<a class="accesskeys" href="javascript:show_gedcom_record();" title="<?php print $pgv_lang["view_gedcom"] ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_family_gedcom"]; ?>"><?php print $pgv_lang["view_gedcom"] ?></a>
				<?php } ?>
			<?php } ?>
			</div>
			<?php
			if (empty($SEARCH_SPIDER) && ($_REQUEST['view'] != 'preview')) :
			?>
			<table class="sublinks_table" cellspacing="4" cellpadding="0">
				<tr>
					<td class="list_label <?php print $TEXT_DIRECTION?>" colspan="4"><?php print $pgv_lang['fams_charts']?></td>
				</tr>
				<tr>
					<td class="sublinks_cell <?php print $TEXT_DIRECTION?>">
					<?php $menu = $controller->getChartsMenu(); $menu->printMenu();
					if (file_exists('reports/familygroup.xml')) :
					?>
					</td>
					<td class="sublinks_cell <?php print $TEXT_DIRECTION?>">
					<?php
					//-- get reports menu from menubar
					$menubar = new MenuBar(); $menu = $menubar->getReportsMenu("", $controller->getFamilyID()); $menu->printMenu();
					//$menu = $controller->getReportsMenu();
					//$menu->printMenu();
					endif; // reports
					if (userCanEdit() && ($controller->display)) :
					?>
					</td>
					<td class="sublinks_cell <?php print $TEXT_DIRECTION?>">
					<?php
					$menu = $controller->getEditMenu();
					$menu->printMenu();
					endif; // edit_fam
					if ($controller->display && ($SHOW_GEDCOM_RECORD || $ENABLE_CLIPPINGS_CART >= PGV_USER_ACCESS_LEVEL)) :
					?>
					</td>
					<td class="sublinks_cell <?php print $TEXT_DIRECTION?>">
					<?php
					$menu = $controller->getOtherMenu();
					$menu->printMenu();
					endif; // other
					?>
					</td>
				</tr>
			</table>
			<?php
				if ($controller->accept_success)
				{
					print "<b>".$pgv_lang["accept_successful"]."</b><br />";
				}
			endif;	// view != preview
			?>
		</td>
	</tr>
</table>
<table class="width90">
	<tr>
		<td valign="top" style="width: <?php print $pbwidth?>px;">
			<?php print_family_children($controller->getFamilyID());?>
		</td>
		<td valign="top">
			<?php print_family_facts($controller->getFamilyID());?>
		</td>
	</tr>
</table>
<br />
<?php
if(empty($SEARCH_SPIDER))
        print_footer();
else {
        if($SHOW_SPIDER_TAGLINE)
                print $pgv_lang["label_search_engine_detected"].": ".$SEARCH_SPIDER;
        print "\n</div>\n\t</body>\n</html>";
}

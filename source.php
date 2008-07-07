<?php
/**
 * Displays the details about a source record.  Also shows how many people and families
 * reference this source.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007 PGV Development Team
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
 * @version $Id: source.php,v 1.5 2008/07/07 18:01:11 lsces Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/functions_print_lists.php");
require_once("includes/controllers/source_ctrl.php");

global $linkToID;

print_header($controller->getPageTitle());
$linkToID = $controller->sid;	// -- Tell addmedia.php what to link to
?>
<?php if ($controller->source->isMarkedDeleted()) print "<span class=\"error\">".$pgv_lang["record_marked_deleted"]."</span>"; ?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record() {
		var recwin = window.open("gedrecord.php?pid=<?php print $controller->sid ?>", "_blank", "top=0,left=0,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
	}
	function showchanges() {
		window.location = '<?php print $SCRIPT_NAME.normalize_query_string($QUERY_STRING."&show_changes=yes"); ?>';
	}
//-->
</script>
<table class="list_table">
	<tr>
		<td>
<?php
	if ($controller->accept_success) print "<b>".$pgv_lang["accept_successful"]."</b><br />";
?>
			<span class="name_head"><?php print PrintReady($controller->source->getTitle()); if ($SHOW_ID_NUMBERS) print " " . getLRM() . "(".$controller->sid.")" . getLRM(); ?></span><br />
		</td>
		<td valign="top" class="noprint">
		<?php if (!$controller->isPrintPreview()) {
			 $editmenu = $controller->getEditMenu();
			 $othermenu = $controller->getOtherMenu();
			 if ($editmenu!==false || $othermenu!==false) {
		?>
			<table class="sublinks_table" cellspacing="4" cellpadding="0">
				<tr>
					<td class="list_label <?php print $TEXT_DIRECTION?>" colspan="2"><?php print $pgv_lang['source_menu']?></td>
				</tr>
				<tr>
					<?php if ($editmenu!==false) { ?>
					<td class="sublinks_cell <?php print $TEXT_DIRECTION?>">
					<?php $editmenu->printMenu(); ?>
					</td>
					<?php
					}
					if ($othermenu!==false) {
					?>

					<td class="sublinks_cell <?php print $TEXT_DIRECTION?>">
					<?php $othermenu->printMenu(); ?>
					</td>
					<?php } ?>
				</tr>
			</table>
			<?php }
		}
		?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="facts_table">
<?php
$sourcefacts = $controller->source->getSourceFacts();
foreach($sourcefacts as $indexval => $fact) {
	$factrec = $fact[0];
	$linenum = $fact[1];
	$ft = preg_match("/1\s(_?\w+)\s(.*)/", $factrec, $match);
	if ($ft>0) $fact = $match[1];
	else $fact="";
	$fact = trim($fact);
	if (!empty($fact)) {
		if ($fact=="NOTE") {
			print_main_notes($factrec, 1, $controller->sid, $linenum);
		}
		else {
			print_fact($factrec, $controller->sid, $linenum);
		}
	}
}
// Print media
print_main_media($controller->sid);

//-- new fact link
if ((!$controller->isPrintPreview())&&($controller->userCanEdit())) {
	print_add_new_fact($controller->sid, $sourcefacts, "SOUR");
		// -- new media
	print "<tr><td class=\"descriptionbox\">";
	print_help_link("add_media_help", "qm", "add_media_lbl");
	print $pgv_lang["add_media_lbl"] . "</td>";
	print "<td class=\"optionbox\">";
	print "<a href=\"javascript: ".$pgv_lang["add_media_lbl"]."\" onclick=\"window.open('addmedia.php?action=showmediaform&amp;linktoid=$controller->sid', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;\">".$pgv_lang["add_media"]."</a>";
	print "<br />\n";
	print '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid='.$controller->sid.'&amp;linkto=source\', \'_blank\', \'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1\'); return false;">'.$pgv_lang["link_to_existing_media"].'</a>';
	print "</td></tr>\n";

}
?>
		</table>
		<br /><br />
		</td></tr>
		<tr class="center"><td colspan="2">
<?php
//Print the tasks table
if (file_exists("modules/research_assistant/research_assistant.php") && ($SHOW_RESEARCH_ASSISTANT>=PGV_USER_ACCESS_LEVEL)) {
 include_once('modules/research_assistant/research_assistant.php');
 $mod = new ra_functions();
	$mod->Init();
 $out = $mod->getSourceTasks($controller->sid);
 print $out;
	echo "</td></tr>";
	echo "<tr class=\"center\"><td colspan=\"2\">";
}


// -- array of names
$myindilist = $controller->source->getSourceIndis();
$myfamlist = $controller->source->getSourceFams();
$ci=count($myindilist);
$cf=count($myfamlist);

if ($ci>0) print_indi_table($myindilist, $controller->source->getTitle());
if ($cf>0) print_fam_table($myfamlist, $controller->source->getTitle());

?>
	<br />
	<br />
	</td>
</tr>
</table>
<br /><br />
<?php print_footer(); ?>

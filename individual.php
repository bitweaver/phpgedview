<?php
/**
 * Individual Page
 *
 * Display all of the information about an individual
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
 * @version $Id: individual.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

require_once("includes/controllers/individual_ctrl.php");

print_header($controller->getPageTitle());

if (!$controller->indi->canDisplayName()) {
   print_privacy_error($CONTACT_EMAIL);
   print_footer();
   exit;
}
?>
<table border="0" cellspacing="0" cellpadding="0" class="facts_table">
	<tr>
	<?php if ($controller->canShowHighlightedObject()) { ?>
		<td rowspan="2" width="100" valign="top">
			<?php print $controller->getHighlightedObject(); ?>
		</td>
	<?php } ?>
	<td valign="top">
		<?php if ($controller->accept_success) print "<b>".$pgv_lang["accept_successful"]."</b><br />"; ?>
		<span class="name_head"><?php print PrintReady($controller->indi->getName()); ?>
			<span dir="ltr">(<?php print $controller->pid; ?>)</span>
		</span><br />
		<?php if (strlen($controller->indi->getAddName()) > 0) print "<span class=\"name_head\">".PrintReady($controller->indi->getAddName())."</span><br />"; ?>
		<?php if ($controller->indi->canDisplayDetails()) { ?>
		<table><tr>
		<?php
			$i=0;
			$maxi=0;
			$globalfacts = $controller->getGlobalFacts();
			foreach ($globalfacts as $key => $value) {
				$ft = preg_match("/\d\s(\w+)(.*)/", $value[1], $match);
				if ($ft>0) $fact = $match[1];
				else $fact="";
				$fact = trim($fact);
				if ($fact=="SEX") $controller->print_sex_record($value[1], $value[0]);
				if ($fact=="NAME") $controller->print_name_record($value[1], $value[0]);
				$FACT_COUNT++;
				print "<td width=\"10\"><br /></td>\n";
				$i++;
				$maxi++;
				if ($i>3) {
					print "</tr><tr>";
					$i=0;
				}
			}
			//-- - put the birth info in this section
			$birthrec = $controller->indi->getBirthRecord();
			$deathrec = $controller->indi->getDeathRecord();
			if ((!empty($birthrec)) || (!empty($deathrec)) || $SHOW_LDS_AT_GLANCE) {
				$colspan = 0;
				if ($i<$maxi) $colspan = $maxi-$i;
			?>
			<td valign="top" colspan="<?php print $colspan; ?>">
			<?php if (!empty($birthrec)) { ?>
				<span class="label"><?php print $factarray["BIRT"].":"; ?></span>
				<span class="field">
					<?php print_fact_date($birthrec); ?>
					<?php print_fact_place($birthrec); ?>
				</span><br />
			<?php } ?>
			<?php
				// RFE [ 1229233 ] "DEAT" vs "DEAT Y"
				// The check $deathrec != "1 DEAT" will not show any records that only have 1 DEAT in them
				if ((!empty($deathrec)) && (trim($deathrec) != "1 DEAT")) {
			?>
				<span class="label"><?php print $factarray["DEAT"].":"; ?></span>
				<span class="field">
				<?php
					print_fact_date($deathrec);
					print_fact_place($deathrec);
				?>
				</span><br />
			<?php }
				if ($SHOW_LDS_AT_GLANCE) print "<b>".get_lds_glance($controller->indi->getGedcomRecord())."</b>";
			?>
			</td>
			<?php } ?>
		</tr>
		</table>
		<?php
		if($SHOW_COUNTER) {
			//print indi counter only if displaying a non-private person
			require("hitcount.php");
			print "\n<br />".$pgv_lang["hit_count"]."	".$hits."\n";
		}
		if ($controller->indi->isRemote()) {
			?>
			<br /><?php print $pgv_lang["indi_is_remote"]; ?><br />
			<a href="<?php print $controller->indi->getLinkUrl(); ?>"><?php print $controller->indi->getLinkUrl(); ?></a>
			<?php
		}
	}
	if (!$controller->isPrintPreview()) {
	?>
	</td><td class="<?php echo $TEXT_DIRECTION; ?>" valign="top">
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
				<td class="list_label <?php echo $TEXT_DIRECTION; ?>" colspan="4"><?php echo $pgv_lang["indis_charts"]; ?></td>
			</tr>
			<tr>
				<td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
				<?php
				//$menu = $controller->getChartsMenu(); $menu->printMenu();
				//-- get charts menu from menubar
				$menubar = new MenuBar(); $menu = $menubar->getChartsMenu($controller->pid); $menu->printMenu();
				if (file_exists("reports/individual.xml")) {?>
					</td><td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
				<?php
				// $menu = $controller->getReportsMenu();	$menu->printMenu();
				//-- get reports menu from menubar
				$menubar = new MenuBar(); $menu = $menubar->getReportsMenu($controller->pid); $menu->printMenu();
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
	<?php } ?>
	</td>
	<td width="10"><br /></td>
	</tr>
	<tr>
	<td valign="bottom" colspan="2">
<script language="JavaScript" type="text/javascript">
<!--
function open_link_remote(pid){
	window.open("addremotelink.php?pid="+pid, "", "top=50,left=50,width=700,height=500,scrollbars=1,scrollable=1,resizable=1");
	return false;
}

// javascript function to open a window with the raw gedcom in it
function show_gedcom_record(shownew) {
	fromfile="";
	if (shownew=="yes") fromfile='&fromfile=1';
	var recwin = window.open("gedrecord.php?pid=<?php print $controller->pid; ?>"+fromfile, "", "top=50,left=50,width=300,height=400,scrollbars=1,scrollable=1,resizable=1");
}

function showchanges() {
	window.location = '<?php print $SCRIPT_NAME."?pid=".$controller->pid."&show_changes=yes"; ?>';
}
// The function below does not go well with validation.
// The option to use getElementsByName is used in connection with code from
// the functions_print.php file.
function togglerow(label) {
	ebn = document.getElementsByName(label);
	if (ebn.length) disp = ebn[0].style.display;
	else disp="";
	if (disp=="none") {
		disp="table-row";
		if (document.all) disp="inline"; // IE
		document.getElementById('rela_plus').style.display="none";
		document.getElementById('rela_minus').style.display="inline";
	}
	else {
		disp="none";
		document.getElementById('rela_plus').style.display="inline";
		document.getElementById('rela_minus').style.display="none";
	}
	for (i=0; i<ebn.length; i++) ebn[i].style.display=disp;
}
function tabswitch(n) {
	var tabid = new Array('0', 'facts','notes','sources','media','relatives','researchlog');
	// show all tabs ?
	var disp='none';
	if (n==0) disp='block';
	// reset all tabs areas
	for (i=1; i<tabid.length; i++) document.getElementById(tabid[i]).style.display=disp;
	// current tab area
	if (n>0) document.getElementById(tabid[n]).style.display='block';
	// empty tabs
	for (i=0; i<tabid.length; i++) {
		var elt = document.getElementById('door'+i);
		if (document.getElementById('no_tab'+i)) { // empty ?
			if (<?php if (userCanEdit(getUserName())) echo 'true'; else echo 'false';?>) {
				elt.style.display='block';
				elt.style.opacity='0.4';
				elt.style.filter='alpha(opacity=40)';
			}
			else elt.style.display='none'; // empty and not editable ==> hide
			if (i==3 && <?php if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) echo 'true'; else echo 'false';?>) elt.style.display='none'; // no sources
			if (i==4 && <?php if (!$MULTI_MEDIA) echo 'true'; else echo 'false';?>) elt.style.display='none'; // no multimedia
			if (i==6) elt.style.display='none'; // hide researchlog
			// ALL : hide empty contents
			if (n==0) document.getElementById(tabid[i]).style.display='none';
		}
		else elt.style.display='block';
	}
	// current door
	for (i=0; i<tabid.length; i++) {
		document.getElementById('door'+i).className='door optionbox rela';
		//document.getElementById('door'+i).className='tab_cell_inactive';
	}
	document.getElementById('door'+n).className='door optionbox';
	//document.getElementById('door'+n).className='tab_cell_active';
	return false;
}
//-->
</script>
<?php
if (!$controller->isPrintPreview()) {
?>
<div class="door">
<dl>
<dd id="door1"><a href="javascript:;" onclick="tabswitch(1)" ><?php print $pgv_lang["personal_facts"]?></a></dd>
<dd id="door2"><a href="javascript:;" onclick="tabswitch(2)" ><?php print $pgv_lang["notes"]?></a></dd>
<dd id="door3"><a href="javascript:;" onclick="tabswitch(3)" ><?php print $pgv_lang["ssourcess"]?></a></dd>
<dd id="door4"><a href="javascript:;" onclick="tabswitch(4)" ><?php print $pgv_lang["media"]?></a></dd>
<dd id="door5"><a href="javascript:;" onclick="tabswitch(5)" ><?php print $pgv_lang["relatives"]?></a></dd>
<dd id="door6"><a href="javascript:;" onclick="tabswitch(6)" ><?php print $pgv_lang["research_assistant"]?></a></dd>
<dd id="door0"><a href="javascript:;" onclick="tabswitch(0)" ><?php print $pgv_lang["all"]?></a></dd>
</dl>
</div>
<br />
<?php
}
?>
	</td>
	</tr>
</table>

<!-- ======================== Start 1st tab individual page ============ Personal Facts and Details -->
<div id="facts" class="tab_page" style="display:none;" >
<?php /**if ($controller->isPrintPreview())**/ print "<span class=\"subheaders\">".$pgv_lang["personal_facts"]."</span>";
$indifacts = $controller->getIndiFacts();
?>
<table class="facts_table">
<?php if (!$controller->indi->canDisplayDetails()) {
	$user = getUser($CONTACT_EMAIL);
	print "<tr><td class=\"facts_value\" colspan=\"2\">";
	print_privacy_error($CONTACT_EMAIL);
	print "</td></tr>";
}
else {
	if (count($indifacts)==0) print "<tr><td id=\"no_tab1\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab1"]."</td></tr>\n";
	print "<tr id=\"row_top\"><td></td><td class=\"descriptionbox rela\">";
	print "<a href=\"javascript:;\" onclick=\"togglerow('row_rela'); return false;\">";
	print "<img style=\"display:none;\" id=\"rela_plus\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".$pgv_lang["show_details"]."\" title=\"".$pgv_lang["show_details"]."\" />";
	print "<img id=\"rela_minus\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".$pgv_lang["hide_details"]."\" title=\"".$pgv_lang["hide_details"]."\" />";
	print " ".$pgv_lang["relatives_events"];
	print "</a></td></tr>\n";
	$yetdied=false;
	$n_chil=1;
	$n_gchi=1;
	foreach ($indifacts as $key => $value) {
		if (stristr($value[1], "1 DEAT")) $yetdied=true;
		if (preg_match("/1 _PGVFS @(.*)@/", $value[1], $match)>0) {
			// do not show family events after death
			if (!$yetdied) {
				print_fact($value[1],trim($match[1]),$value[0], $controller->indi->getGedcomRecord());
			}
		}
		else print_fact($value[1],$controller->pid,$value[0], $controller->indi->getGedcomRecord());
		$FACT_COUNT++;
	}
}
//-- new fact link
if ((!$controller->isPrintPreview()) &&(userCanEdit($controller->uname))&&($controller->indi->canDisplayDetails())) {
	print_add_new_fact($pid, $indifacts, "INDI");
}
?>
</table>
<br />
</div>
<script language="JavaScript" type="text/javascript">
<!--
	// hide button if list is empty
	ebn = document.getElementsByName('row_rela');
	if (ebn.length==0) document.getElementById('row_top').style.display="none";
	<?php if (!$EXPAND_RELATIVES_EVENTS) print "togglerow('row_rela');"?>
//-->
</script>
<!-- ======================== Start 2nd tab individual page ==== Notes ======= -->
<div id="notes" class="tab_page" style="display:none;" >
<?php /**if ($controller->isPrintPreview())**/ print "<span class=\"subheaders\">".$pgv_lang["notes"]."</span>"; ?>
<table class="facts_table">
<?php if (!$controller->indi->canDisplayDetails()) {
   print "<tr><td class=\"facts_value\">";
   print_privacy_error($CONTACT_EMAIL);
   print "</td></tr>";
}
else {
	$notecount=0;
	$otherfacts = $controller->getOtherFacts();
	foreach ($otherfacts as $key => $factrec) {
		$ft = preg_match("/\d\s(\w+)(.*)/", $factrec[1], $match);
		if ($ft>0) $fact = $match[1];
		else $fact="";
		$fact = trim($fact);
		if ($fact=="NOTE") {
			print_main_notes($factrec[1], 1, $pid, $factrec[0]);
			$notecount++;
		}
		$FACT_COUNT++;
	}
   if ($notecount==0) print "<tr><td id=\"no_tab2\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab2"]."</td></tr>\n";
	//-- New Note Link
	if (!$controller->isPrintPreview() && (userCanEdit($controller->uname))&&$controller->indi->canDisplayDetails()) {
	?>
		<tr>
			<td class="facts_label"><?php print_help_link("add_note_help", "qm"); ?><?php echo $pgv_lang["add_note_lbl"]; ?></td>
			<td class="facts_value"><a href="javascript:;" onclick="add_new_record('<?php echo $controller->pid; ?>','NOTE'); return false;"><?php echo $pgv_lang["add_note"]; ?></a>
			<br />
			</td>
		</tr>
	<?php
	}
}
?>
</table>
<br />
</div>
<!-- =========================== Start 3rd tab individual page === Sources -->
<div id="sources" class="tab_page" style="display:none;" >
<?php
if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) {
	/**if ($controller->isPrintPreview())**/ print "<span class=\"subheaders\">".$pgv_lang["ssourcess"]."</span>"; ?>
<table class="facts_table">
<?php	if (!$controller->indi->canDisplayDetails()) {
		print "<tr><td class=\"facts_value\">";
		print_privacy_error($CONTACT_EMAIL);
		print "</td></tr>";
	}
	else {
		$sourcecount = 0;
		$otheritems = $controller->getOtherFacts();
		foreach ($otheritems as $key => $factrec) {
			$ft = preg_match("/\d\s(\w+)(.*)/", $factrec[1], $match);
			if ($ft>0) $fact = $match[1];
			else $fact="";
			$fact = trim($fact);
			if ($fact=="SOUR") {
				$sourcecount++;
				print_main_sources($factrec[1], 1, $pid, $factrec[0]);
			}
			$FACT_COUNT++;
		}
	   if ($sourcecount==0) print "<tr><td id=\"no_tab3\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab3"]."</td></tr>\n";
		//-- New Source Link
		if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
		?>
			<tr>
				<td class="facts_label"><?php print_help_link("add_source_help", "qm"); ?><?php echo $pgv_lang["add_source_lbl"]; ?></td>
				<td class="facts_value">
				<a href="javascript:;" onclick="add_new_record('<?php echo $controller->pid; ?>','SOUR'); return false;"><?php echo $pgv_lang["add_source"]; ?></a>
				<br />
				</td>
			</tr>
		<?php
		}
	}
	?>
</table>
<br />
<?php
}
?>
</div>
<!-- ==================== Start 4th tab individual page ==== Media -->
<div id="media" class="tab_page" style="display:none;" >
<?php
/**if ($controller->isPrintPreview())**/ print "<span class=\"subheaders\">".$pgv_lang["media"]."</span>";
if ($MULTI_MEDIA) {
?>
<table class="facts_table">
<?php
$media_found = false;
if (!$controller->indi->canDisplayDetails()) {
	print "<tr><td class=\"facts_value\">";
	print_privacy_error($CONTACT_EMAIL);
	print "</td></tr>";
}
else {
	// if (preg_match("/PGV_FAMILY_ID: (.*)/", $factrec[1], $match)>0) print_main_media($factrec[1], 1, trim($match[1]), $factrec[0]);
	$media_found = print_main_media('', '', $pid, '');
	if (!$media_found) print "<tr><td id=\"no_tab4\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab4"]."</td></tr>\n";

	//-- New Media link
	if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
   	?>
		<tr>
			<td class="facts_label"><?php print_help_link("add_media_help", "qm"); ?><?php print $pgv_lang["add_media_lbl"]; ?></td>
			<td class="facts_value">
			<a href="javascript:;" onclick="window.open('addmedia.php?action=showmediaform&amp;pid=<?php echo $controller->pid; ?>', '', 'top=50,left=50,width=900,height=650,resizable=1,scrollbars=1'); return false;"> <?php echo $pgv_lang["add_media"]; ?></a>
			</td>
		</tr>
	<?php
   }
}
?>
</table>
<?php
}
else print "<table class=\"facts_table\"><tr><td id=\"no_tab4\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab4"]."</td></tr></table>\n";
?>
<br />
</div>
<!-- ============================= Start 5th tab individual page ==== Close relatives -->
<div id="relatives" class="tab_page" style="display:none;" >
<?php
$personcount=0;
$families = $controller->indi->getChildFamilies();
if (count($families)==0) {
	print "<span class=\"subheaders\">".$pgv_lang["relatives"]."</span>";
	if (/**(!$controller->isPrintPreview()) &&**/ (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
		?>
	<table class="facts_table">
			<tr>
				<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?><a href="javascript:;" onclick="return addnewparent('<?php print $controller->pid; ?>', 'HUSB');"><?php print $pgv_lang["add_father"]; ?></a></td>
			</tr>
			<tr>
				<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?><a href="javascript:;" onclick="return addnewparent('<?php print $controller->pid; ?>', 'WIFE');"><?php print $pgv_lang["add_mother"]; ?></a></td>
			</tr>
		</table>
		<?php
	}
}
//-- parent families
foreach($families as $famid=>$family) {
	?>
	<table>
		<tr>
			<td><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["cfamily"]["small"]; ?>" border="0" class="icon" alt="" /></td>
			<td><span class="subheaders"><?php print PrintReady($controller->indi->getChildFamilyLabel($family)); ?></span>
		<?php if (!$controller->isPrintPreview()) { ?>
			 - <a href="family.php?famid=<?php print $famid; ?>">[<?php print $pgv_lang["view_family"]; ?><?php if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;"; ?>]</a>
		<?php } ?>
			</td>
		</tr>
	</table>
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
			if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
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
			if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
				?>
				<tr><td class="facts_label"><?php print $pgv_lang["add_mother"]; ?></td>
				<td class="facts_value"><?php print_help_link("edit_add_parent_help", "qm"); ?> <a href="javascript:;" onclick="return addnewparentfamily('<?php print $controller->pid; ?>', 'WIFE', '<?php print $famid; ?>');"><?php print $pgv_lang["add_mother"]; ?></a></td>
				</tr>
				<?php
			}
		}
		$styleadd = "blue";
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
		$styleadd = "";
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
		$styleadd = "red";
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
		if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
			?>
			<tr>
				<td class="facts_label"><?php echo $pgv_lang["add_child_to_family"]; ?></td>
				<td class="facts_value"><?php print_help_link("add_sibling_help", "qm"); ?>
					<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_sibling"]; ?></a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
<?php
}

//-- step families
$stepfams = $controller->indi->getStepFamilies();
foreach($stepfams as $famid=>$family) {
	?>
	<table>
		<tr>
			<td><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["cfamily"]["small"]; ?>" border="0" class="icon" alt="" /></td>
			<td><span class="subheaders"><?php print PrintReady($controller->indi->getStepFamilyLabel($family)); ?></span>
		<?php if (!$controller->isPrintPreview()) { ?>
			 - <a href="family.php?famid=<?php print $famid; ?>">[<?php print $pgv_lang["view_family"]; ?><?php if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;"; ?>]</a>
		<?php } ?>
			</td>
		</tr>
	</table>
	<table class="facts_table">
		<?php
		//$personcount = 0;
		$people = $controller->buildFamilyList($family, "step");
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
		$styleadd = "blue";
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
		$styleadd = "";
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
		$styleadd = "red";
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
		if (($controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
			?>
			<tr>
				<td class="facts_label"><?php echo $pgv_lang["add_child_to_family"]; ?></td>
				<td class="facts_value"><?php print_help_link("add_sibling_help", "qm"); ?>
					<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_sibling"]; ?></a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
<?php
}

//-- spouses and children
$families = $controller->indi->getSpouseFamilies();
foreach($families as $famid=>$family) {
	?>
	<table>
		<tr>
			<td><img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["cfamily"]["small"]; ?>" border="0" class="icon" alt="" /></td>
			<td><span class="subheaders"><?php print PrintReady($controller->indi->getSpouseFamilyLabel($family)); ?></span>
		<?php if (!$controller->isPrintPreview()) { ?>
			 - <a href="family.php?famid=<?php print $famid; ?>">[<?php print $pgv_lang["view_family"]; ?><?php if ($SHOW_FAM_ID_NUMBERS) print " &lrm;($famid)&lrm;"; ?>]</a>
		<?php } ?>
			</td>
		</tr>
	</table>
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
			if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
				?>
				<tr><td class="facts_label"><?php print $pgv_lang["add_wife"]; ?></td>
				<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php print $famid; ?>', 'WIFE');"><?php print $pgv_lang["add_wife_to_family"]; ?></a></td>
				</tr>
				<?php
			}
		}
		if ($spousetag=="HUSB" && !isset($people["newhusb"]) && !isset($people["husb"])) {
			if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
				?>
				<tr><td class="facts_label"><?php print $pgv_lang["add_husb"]; ?></td>
				<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php print $famid; ?>', 'HUSB');"><?php print $pgv_lang["add_husb_to_family"]; ?></a></td>
				</tr>
				<?php
			}
		}
		$styleadd = "blue";
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
		$styleadd = "";
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
		$styleadd = "red";
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
		if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
			?>
			<tr>
				<td class="facts_label"><?php echo $pgv_lang["add_child_to_family"]; ?></td>
				<td class="facts_value"><?php print_help_link("add_son_daughter_help", "qm"); ?>
					<a href="javascript:;" onclick="return addnewchild('<?php print $family->getXref(); ?>');"><?php print $pgv_lang["add_son_daughter"]; ?></a>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
<?php
}
if ($personcount==0) print "<tr><td id=\"no_tab5\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab5"]."</td></tr>\n";
?>
<br />
<?php
if ((!$controller->isPrintPreview()) && (userCanEdit(getUserName()))&&($controller->indi->canDisplayDetails())) {
?>
<table class="facts_table">
<?php if (count($families)>1) { ?>
	<tr>
		<td class="facts_value">
		<?php print_help_link("reorder_families_help", "qm"); ?>
		<a href="javascript:;" onclick="return reorder_families('<?php print $controller->pid; ?>');"><?php print $pgv_lang["reorder_families"]; ?></a>
		</td>
	</tr>
<?php } ?>
	<tr>
		<td class="facts_value">
		<?php print_help_link("link_child_help", "qm"); ?>
		<a href="javascript:;" onclick="return add_famc('<?php print $controller->pid; ?>');"><?php print $pgv_lang["link_as_child"]; ?></a>
		</td>
	</tr>
	<?php if ($controller->indi->getSex()!="F") { ?>
	<tr>
		<td class="facts_value">
		<?php print_help_link("add_wife_help", "qm"); ?>
		<a href="javascript:;" onclick="return addspouse('<?php print $controller->pid; ?>','WIFE');"><?php print $pgv_lang["add_new_wife"]; ?></a>
		</td>
	</tr>
	<tr>
		<td class="facts_value">
		<?php print_help_link("link_new_wife_help", "qm"); ?>
		<a href="javascript:;" onclick="return linkspouse('<?php print $controller->pid; ?>','WIFE');"><?php print $pgv_lang["link_new_wife"]; ?></a>
		</td>
	</tr>
	<tr>
		<td class="facts_value">
		<?php print_help_link("link_new_husband_help", "qm"); ?>
		<a href="javascript:;" onclick="return add_fams('<?php print $controller->pid; ?>','HUSB');"><?php print $pgv_lang["link_as_husband"]; ?></a>
		</td>
	</tr>
   <?php }
	if ($controller->indi->getSex()!="M") { ?>
	<tr>
		<td class="facts_value">
		<?php print_help_link("add_husband_help", "qm"); ?>
		<a href="javascript:;" onclick="return addspouse('<?php print $controller->pid; ?>','HUSB');"><?php print $pgv_lang["add_new_husb"]; ?></a>
		</td>
	</tr>
	<tr>
		<td class="facts_value">
		<?php print_help_link("link_new_husband_help", "qm"); ?>
		<a href="javascript:;" onclick="return linkspouse('<?php print $controller->pid; ?>','HUSB');"><?php print $pgv_lang["link_new_husb"]; ?></a>
		</td>
	</tr>
	<tr>
		<td class="facts_value">
		<?php print_help_link("link_wife_help", "qm"); ?>
		<a href="javascript:;" onclick="return add_fams('<?php print $controller->pid; ?>','WIFE');"><?php print $pgv_lang["link_as_wife"]; ?></a>
		</td>
	</tr>
	<?php } if (userGedcomAdmin($controller->uname)) { ?>
	<tr>
		<td class="facts_value">
		<?php print_help_link("link_remote_help", "qm"); ?>
		<a href="javascript:;" onclick="return open_link_remote('<?php print $controller->pid; ?>');"><?php print $pgv_lang["link_remote"]; ?></a>
    	</td>
    </tr>
    <?php } ?>
</table>
<?php } ?>
<br />
</div>

<!-- ===================================== Start 6th tab individual page === Research Log -->
<div id="researchlog" class="tab_page" style="display:none;" >
<?php
/**if ($controller->isPrintPreview())**/ print "<span class=\"subheaders\">".$pgv_lang["research_assistant"]."</span>";
if (file_exists("modules/researchlog/researchlog.php") && ($SHOW_RESEARCH_LOG>=getUserAccessLevel())) {
	if (!$controller->indi->canDisplayDetails()) { ?>
		<table class="facts_table">
	    <tr><td class="facts_value">
	    <?php print_privacy_error($CONTACT_EMAIL); ?>
	    </td></tr>
	    </table>
	    <br />
	<?php
	}
	else {
	   include_once('modules/researchlog/researchlog.php');
	   $mod = new researchlog();
	   $out = $mod->tab($pid);
	   print $out;
	}
}
else print "<table class=\"facts_table\"><tr><td id=\"no_tab6\" colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_tab6"]."</td></tr></table>\n";
print "</div>\n";

// active tab
print "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n";
if ($controller->isPrintPreview()) print "tabswitch(0)";
else print "tabswitch(". ($GEDCOM_DEFAULT_TAB + 1) .")";
print "\n//-->\n</script>\n";
print_footer();
?>

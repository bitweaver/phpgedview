<?php
/**
 * Parses gedcom file and displays a descendancy tree.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  John Finlay and Others
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
 * @version $Id: descendancy.php,v 1.4 2008/07/07 18:01:13 lsces Exp $
 */

// -- include config file
require_once("includes/controllers/descendancy_ctrl.php");

// -- print html header information
print_header($controller->name." ".$pgv_lang["descend_chart"]);

// LBox =====================================================================================
if ($MULTI_MEDIA && file_exists("modules/lightbox/album.php")) {
	include('modules/lightbox/lb_config.php');
	include('modules/lightbox/functions/lb_call_js.php');
}	
// ==========================================================================================

?>
<table class="list_table <?php print $TEXT_DIRECTION?>"><tr><td width="<?php print $controller->cellwidth?>px" valign="top">
<h2><?php print $pgv_lang["descend_chart"].":<br />".PrintReady($controller->name)."</h2>";
//print "\n\t<h2>".$pgv_lang["descend_chart"].":<br />".$controller->name."</h2>";?>

<script type="text/javascript">
<!--
var pastefield;
function paste_id(value) {
	pastefield.value=value;
}
//-->
</script>

<?php
$gencount=0;
if ($view!="preview") {
	$show_famlink = true;
?>
	</td><td><form method="get" name="people" action="?">
	<table class="<?php print "list_table".$TEXT_DIRECTION ?>">
	
		<!-- NOTE: rootid -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("desc_rootid_help", "qm");
	print $pgv_lang["root_person"]."&nbsp;</td>";
	?>
	<td class="optionbox vmiddle">
	<input class="pedigree_form" type="text" id="pid" name="pid" size="3" value="<?php print $controller->pid ?>" />
	<?php	print_findindi_link("pid",""); ?>
	</td>

	<!-- // NOTE: box width -->
	<td class="descriptionbox">
	<?php
	print_help_link("box_width_help", "qm");
	print $pgv_lang["box_width"] . "&nbsp;</td>";?>
	<td class="optionbox vmiddle"><input type="text" size="3" name="box_width" value="<?php print $controller->box_width ?>" />
	<b>%</b>
	</td>

	<!-- // NOTE: chart style -->
	<td rowspan="2" class="descriptionbox">
	<!-- //	print_help_link("chart_style_help", "qm"); -->
	<?php
	print $pgv_lang["displ_layout_conf"];?>
	</td>
	<td rowspan="2" class="optionbox vmiddle">

	<input type="radio" name="chart_style" value="0"
	<?php
	if ($controller->chart_style == "0") print " checked=\"checked\" ";
	print "/>".$pgv_lang["chart_list"];
	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"1\"";
	if ($controller->chart_style == "1") print " checked=\"checked\" ";
	print "/>".$pgv_lang["chart_booklet"];
	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"2\"";
	if ($controller->chart_style == "2") print " checked=\"checked\" ";
	print " />".$pgv_lang["individual_list"];
	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"3\"";
	if ($controller->chart_style == "3") print " checked=\"checked\" ";
	print " />".$pgv_lang["family_list"];
	?>
	</td>

	<!-- // NOTE: submit -->
	<td rowspan="2" class="topbottombar">
	<input type="submit" value="<?php print $pgv_lang["view"] ?>" />
	</td></tr>

	<!-- // NOTE: generations -->
	<tr><td class="descriptionbox">
	<?php print_help_link("desc_generations_help", "qm");
	print $pgv_lang["generations"] . "&nbsp;</td>";
	?>
	
	<td class="optionbox vmiddle">
	<select name="generations">
	<?php
	for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
	print "<option value=\"".$i."\"" ;
	if ($i == $controller->generations) print "selected=\"selected\" ";
		print ">".$i."</option>";
	}
	?>
	</select>
	
	</td>
	<!-- // NOTE: show full -->
	<td class="descriptionbox">
	<input type="hidden" name="show_full" value="<?php print $controller->show_full ?>" />
	<?php print_help_link("show_full_help", "qm");
	print $pgv_lang["show_details"];
	?>
	</td>
	<td class="optionbox vmiddle">
	<input type="checkbox" value="
	<?php
	if ($controller->show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
	else print "0\" onclick=\"document.people.show_full.value='1';";
	?>"
	/>
	</td></tr>

	</table>
	</form>
<?php } ?>
</td></tr></table>
<?php
if (is_null($controller->descPerson)) {
	print "<span class=\"error\">".$pgv_lang["record_not_found"]."</span>";
}
$controller->generations -= 1; // [ 1757792 ] Charts : wrong generations count
//-- list
if ($controller->chart_style==0) {
	echo "<ul style=\"list-style: none; display: block;\" id=\"descendancy_chart".($TEXT_DIRECTION=="rtl" ? "_rtl" : "")."\">";
	$controller->print_child_descendancy($controller->descPerson, $controller->generations);
	echo "</ul><br />";
}
//-- booklet
if ($controller->chart_style==1) {
	$show_cousins = true;
	$famids = find_sfamily_ids($controller->pid);
	if (count($famids)) {
		$controller->print_child_family($controller->descPerson, $controller->generations);
		print_footer();
		exit;
	}
}
//-- Individual list
if ($controller->chart_style==2) {
	require_once("includes/functions_print_lists.php");
	$datalist = array();
	function indi_desc($pid, $n) {
		if ($n<0) return;
		global $datalist;
		$person = Person::getInstance($pid);
		if (is_null($person)) return;
		//-- add indi
		$datalist[] = $person->xref;
		foreach ($person->getSpouseFamilyIds() as $f=>$fams) {
			$family = Family::getInstance($fams);
			if (is_null($family)) continue;
			//-- add spouse
			$datalist[] = $family->getSpouseId($pid);
			//-- recursive call for each child
			foreach ($family->getChildren() as $c=>$child) indi_desc($child->xref, $n-1);
		}
	}
	indi_desc($controller->pid, $controller->generations);
	echo "<div class=\"center\">";
	print_indi_table(array_unique($datalist), $pgv_lang["descend_chart"]." : ".PrintReady($controller->name));
	echo "</div>";
}
//-- Family list
if ($controller->chart_style==3) {
	require_once("includes/functions_print_lists.php");
	$datalist = array();
	function fam_desc($pid, $n) {
		if ($n<0) return;
		global $datalist;
		$person = Person::getInstance($pid);
		if (is_null($person)) return;
		foreach ($person->getSpouseFamilyIds() as $f=>$fams) {
			$family = Family::getInstance($fams);
			if (is_null($family)) continue;
			$datalist[] = $family->xref;
			//-- recursive call for each child
			foreach ($family->getChildren() as $c=>$child) fam_desc($child->xref, $n-1);
		}
	}
	fam_desc($controller->pid, $controller->generations);
	echo "<div class=\"center\">";
	print_fam_table(array_unique($datalist), $pgv_lang["descend_chart"]." : ".PrintReady($controller->name));
	echo "</div>";
}
print_footer();
?>

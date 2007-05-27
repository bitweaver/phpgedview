<?php
/**
 * Displays pedigree tree as a printable booklet
 * with Sosa-Stradonitz numbering system
 * ($rootid=1, father=2, mother=3 ...)
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2006  John Finlay and Others
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
 * @version $Id: ancestry.php,v 1.3 2007/05/27 17:49:22 lsces Exp $
 */

require_once("includes/controllers/ancestry_ctrl.php");

// -- print html header information
print_header($controller->name . " " . $pgv_lang["ancestry_chart"]);
print "\n\t<table class=\"list_table $TEXT_DIRECTION\"><tr><td width=\"".$controller->cellwidth."px\" valign=\"top\">\n\t\t";
if ($view == "preview") print "<h2>" . str_replace("#PEDIGREE_GENERATIONS#", $PEDIGREE_GENERATIONS, $pgv_lang["gen_ancestry_chart"]) . ":";
else print "<h2>" . $pgv_lang["ancestry_chart"] . ":";
print "<br />".PrintReady($controller->name);
if ($controller->addname != "") print "<br />" . PrintReady($controller->addname);
print "</h2>";
// -- print the form to change the number of displayed generations
if ($view != "preview") {
	$show_famlink = true;
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
	</script>
	<?php if (isset($controller->max_generation) == true) 
	print "<span class=\"error\">" . str_replace("#PEDIGREE_GENERATIONS#", $PEDIGREE_GENERATIONS, $pgv_lang["max_generation"]) . "</span>";
	if (isset($min_generation) == true) print "<span class=\"error\">" . $pgv_lang["min_generation"] . "</span>";?>
	</td><td><form name="people" id="people" method="get" action="?">
	<input type="hidden" name="show_full" value="<?php print $controller->show_full; ?>" />
	<input type="hidden" name="show_cousins" value="<?php print $controller->show_cousins; ?>" />
	<table class="list_table <?php print $TEXT_DIRECTION; ?>">

		<!-- // NOTE: Root ID -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("rootid_help", "qm");
	print $pgv_lang["root_person"]?></td>
	<td class="optionbox vmiddle">
	<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php print $controller->rootid ?>" />
	<?php print_findindi_link("rootid",""); ?>
	</td>

	<!-- // NOTE: Box width -->
	<td class="descriptionbox">
	<?php
	print_help_link("box_width_help", "qm");
	print $pgv_lang["box_width"]?></td>
	<td class="optionbox vmiddle"><input type="text" size="3" name="box_width" value="<?php print $box_width ?>" /> <b>%</b>
	</td>

	<!-- // NOTE: chart style -->
	<td rowspan="2" class="descriptionbox">
	<?php
	print_help_link("chart_style_help", "qm");
	print $pgv_lang["displ_layout_conf"];?>
	</td>
	<td rowspan="2" class="optionbox vmiddle">
	<input type="radio" name="chart_style" value="0"
	<?php
	if ($controller->chart_style == "0") print " checked=\"checked\" ";
	print "onclick=\"toggleStatus('cousins');";
	//if ($controller->chart_style != "1") print " document.people.chart_style.value='1';";
	print "\" />".$pgv_lang["chart_list"];
	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"1\"";
	if ($controller->chart_style == "1") print " checked=\"checked\" ";
	print "onclick=\"toggleStatus('cousins');";
	//if ($controller->chart_style != "1") print " document.people.chart_style.value='0';";
	print "\" />".$pgv_lang["chart_booklet"];
	?>

		<!-- // NOTE: show cousins -->
	<br />
	<?php
	print_help_link("show_cousins_help", "qm");
	print "<input ";
	if ($controller->chart_style == "0") print "disabled=\"disabled\" ";
	print "id=\"cousins\" type=\"checkbox\" value=\"";
	if ($controller->show_cousins) print "1\" checked=\"checked\" onclick=\"document.people.show_cousins.value='0';\"";
	else print "0\" onclick=\"document.people.show_cousins.value='1';\"";
	print " />";
	print $pgv_lang["show_cousins"];

	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"2\"";
	if ($controller->chart_style == "2") print " checked=\"checked\" ";
	print " />".$pgv_lang["individual_list"];
	print "<br /><input type=\"radio\" name=\"chart_style\" value=\"3\"";
	if ($controller->chart_style == "3") print " checked=\"checked\" ";
	print " />".$pgv_lang["family_list"];
	?>
	</td>

	<!-- // NOTE: submit -->
	<td rowspan="2" class="facts_label03">
	<input type="submit" value="<?php print $pgv_lang["view"] ?>" />
	</td></tr>

	<!-- // NOTE: generations -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("PEDIGREE_GENERATIONS_help", "qm");
	print $pgv_lang["generations"]?></td>

	<td class="optionbox vmiddle">
	<select name="PEDIGREE_GENERATIONS">
	<?php
	for ($i=2; $i<=$MAX_PEDIGREE_GENERATIONS; $i++) {
	print "<option value=\"".$i."\"" ;
	if ($i == $OLD_PGENS) print "selected=\"selected\" ";
		print ">".$i."</option>";
	}?>
	</select>
	
	</td>
	
	<!-- // NOTE: show full -->
	
	<td class="descriptionbox">
		<?php
	print_help_link("show_full_help", "qm");
	print $pgv_lang["show_details"]; ?>
	</td>
	<td class="optionbox vmiddle">
	<input type="checkbox" value="
	<?php
	if ($controller->show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
	else print "0\" onclick=\"document.people.show_full.value='1';";?>"
	/>
	</td></tr>
	</table>
	</form>
<?php } ?>

</td></tr></table>

<?php
//-- list
if ($controller->chart_style==0) {
	$pidarr=array();
	print "<ul style=\"list-style: none; display: block;\" id=\"ancestry_chart".($TEXT_DIRECTION=="rtl" ? "_rtl" : "") ."\">\r\n";
	$controller->print_child_ascendancy($controller->rootid, 1, $OLD_PGENS);
	print "</ul>";
	print "<br />";
}
//-- booklet
if ($controller->chart_style==1) {
	// first page : show indi facts
	print_pedigree_person($controller->rootid, 2, false, 1);
	// expand the layer
	echo <<< END
	<script language="JavaScript" type="text/javascript">
		expandbox("$controller->rootid.1", 2);
	</script>
	<br />
END;
	// process the tree
	$treeid = pedigree_array($controller->rootid);
	$treesize = pow(2, (int)($PEDIGREE_GENERATIONS))-1;
	for ($i = 0; $i < $treesize; $i++) {
		$pid = $treeid[$i];
		if ($pid) {
			$person = Person::getInstance($pid);
			if (!is_null($person)) {
				$famids = $person->getChildFamilies();
				foreach($famids as $famid=>$family) {
					$parents = find_parents_in_record($family->getGedcomRecord());
					if ($parents) print_sosa_family($famid, $pid, $i + 1);
					// 	show empty family only if it is the first and only one
					else if ($i == 0) print_sosa_family("", $pid, $i + 1);
				}
			}
		}
	}
}
//-- Individual list
if ($controller->chart_style==2) {
	require_once("includes/functions_print_lists.php");
	$treeid = ancestry_array($controller->rootid);
	echo "<div class=\"center\">";
	print_indi_table($treeid, $pgv_lang["ancestry_chart"]." : ".PrintReady($controller->name), "sosa");
	echo "</div>";
}
//-- Family list
if ($controller->chart_style==3) {
	require_once("includes/functions_print_lists.php");
	$treeid = ancestry_array($controller->rootid);
	$famlist = array();
	foreach ($treeid as $p=>$pid) {
	  $person = Person::getInstance($pid);
		if (is_null($person)) continue;
	  foreach ($person->getChildFamilyIds() as $f=>$famc) $famlist[] = $famc;
	}
	echo "<div class=\"center\">";
	print_fam_table(array_unique($famlist), $pgv_lang["ancestry_chart"]." : ".PrintReady($controller->name));
	echo "</div>";
}
print_footer();
?>

<?php
/**
* Displays pedigree tree as a printable booklet
* with Sosa-Stradonitz numbering system
* ($rootid=1, father=2, mother=3 ...)
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
* @version $Id: ancestry.php,v 1.5 2009/09/15 20:06:00 lsces Exp $
*/

require 'config.php';

require 'includes/controllers/ancestry_ctrl.php';
require 'includes/functions/functions_print_lists.php';

$controller=new AncestryController();
$controller->init();

print_header($controller->name . " " . $pgv_lang['ancestry_chart']);

if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';

// LightBox
if ($MULTI_MEDIA && file_exists('./modules/lightbox.php')) {
	include './modules/lightbox/lb_defaultconfig.php';
	if (file_exists('modules/lightbox/lb_config.php')) {
		include './modules/lightbox/lb_config.php';
	}
	include './modules/lightbox/functions/lb_call_js.php';
}

echo '<table><tr><td valign="middle">';
if ($view=="preview") {
	echo '<h2>', str_replace('#PEDIGREE_GENERATIONS#', $PEDIGREE_GENERATIONS, $pgv_lang['gen_ancestry_chart']) . ':';
} else {
	echo '<h2>', $pgv_lang['ancestry_chart'] . ':';
}
echo '<br />',PrintReady($controller->name);
if ($controller->addname!="") {
	echo '<br />', PrintReady($controller->addname);
}
echo '</h2>';
// -- print the form to change the number of displayed generations
if ($view!="preview") {
	$show_famlink=true;
	echo PGV_JS_START, 'var pastefield; function paste_id(value) {pastefield.value=value;}', PGV_JS_END;
	?>
	</td><td width="50px">&nbsp;</td><td><form name="people" id="people" method="get" action="?">
	<input type="hidden" name="show_full" value="<?php echo $controller->show_full; ?>" />
	<input type="hidden" name="show_cousins" value="<?php echo $controller->show_cousins; ?>" />
	<table class="list_table <?php echo $TEXT_DIRECTION; ?>">

		<!-- // NOTE: Root ID -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("rootid_help", "qm");
	echo $pgv_lang['root_person']; ?></td>
	<td class="optionbox vmiddle">
	<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php echo htmlentities($controller->rootid,ENT_COMPAT,'UTF-8'); ?>" />
	<?php print_findindi_link("rootid",""); ?>
	</td>

	<!-- // NOTE: Box width -->
	<td class="descriptionbox">
	<?php
	print_help_link("box_width_help", "qm");
	echo $pgv_lang['box_width']; ?></td>
	<td class="optionbox vmiddle"><input type="text" size="3" name="box_width" value="<?php echo htmlentities($box_width,ENT_COMPAT,'UTF-8'); ?>" /> <b>%</b>
	</td>

	<!-- // NOTE: chart style -->
	<td rowspan="2" class="descriptionbox">
	<?php
	print_help_link("chart_style_help", "qm");
	echo $pgv_lang['displ_layout_conf']; ?>
	</td>
	<td rowspan="2" class="optionbox vmiddle">
	<input type="radio" name="chart_style" value="0"
	<?php
	if ($controller->chart_style=="0") {
		echo ' checked="checked"';
	}
	echo ' onclick="statusDisable(\'cousins\');';
	echo '" />', $pgv_lang['chart_list'];
	echo '<br /><input type="radio" name="chart_style" value="1"';
	if ($controller->chart_style=="1") {
		echo ' checked="checked"';
	}
	echo ' onclick="statusEnable(\'cousins\');';
	echo '" />', $pgv_lang['chart_booklet'];
	?>

	<!-- // NOTE: show cousins -->
	<br />
	<?php
	print_help_link("show_cousins_help", "qm");
	echo '<input ';
	if ($controller->chart_style=="0") {
		echo 'disabled="disabled" ';
	}
	echo 'id="cousins" type="checkbox" value="';
	if ($controller->show_cousins) {
		echo '1" checked="checked" onclick="document.people.show_cousins.value=\'0\';"';
	} else {
		echo '0" onclick="document.people.show_cousins.value=\'1\';"';
	}
	echo ' />';
	echo $pgv_lang['show_cousins'];

	echo '<br /><input type="radio" name="chart_style" value="2"';
	if ($controller->chart_style=="2") {
		echo ' checked="checked" ';
	}
	echo ' onclick="statusDisable(\'cousins\');"';
	echo ' />', $pgv_lang['individual_list'];
	echo '<br /><input type="radio" name="chart_style" value="3"';
	echo ' onclick="statusDisable(\'cousins\');"';
	if ($controller->chart_style=="3") {
		echo ' checked="checked" ';
	}
	echo ' />', $pgv_lang['family_list'];
	?>
	</td>

	<!-- // NOTE: submit -->
	<td rowspan="2" class="facts_label03">
	<input type="submit" value="<?php echo $pgv_lang['view']; ?>" />
	</td></tr>

	<!-- // NOTE: generations -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("PEDIGREE_GENERATIONS_help", "qm");
	echo $pgv_lang['generations']; ?></td>

	<td class="optionbox vmiddle">
	<select name="PEDIGREE_GENERATIONS">
	<?php
	for ($i=2; $i<=$MAX_PEDIGREE_GENERATIONS; $i++) {
	echo '<option value="', $i, '"';
	if ($i==$OLD_PGENS) {
		echo ' selected="selected"';
	}
		echo '>', $i, '</option>';
	}?>
	</select>

	</td>

	<!-- // NOTE: show full -->

	<td class="descriptionbox">
	<?php
	print_help_link("show_full_help", "qm");
	echo $pgv_lang['show_details'];
	?>
	</td>
	<td class="optionbox vmiddle">
	<input type="checkbox" value="
	<?php
	if ($controller->show_full) {
		echo '1" checked="checked" onclick="document.people.show_full.value=\'0\';';
	} else {
		echo '0" onclick="document.people.show_full.value=\'1\';';
	}
	?>"
	/>
	</td></tr>
	</table>
	</form>
<?php } ?>

</td></tr></table>

<?php
if ($show_full==0) {
	echo '<span class="details2">', $pgv_lang['charts_click_box'], '</span><br /><br />';
}

switch ($controller->chart_style) {
case 0:
	// List
	$pidarr=array();
	echo '<ul style="list-style: none; display: block;" id="ancestry_chart', ($TEXT_DIRECTION=='rtl' ? '_rtl' : ''), '">';
	$controller->print_child_ascendancy($controller->rootid, 1, $OLD_PGENS-1);
	echo '</ul>';
	echo '<br />';
	break;
case 1:
	// TODO: this should be a parameter to a function, not a global
	$show_cousins=$controller->show_cousins;

	// Booklet
	// first page : show indi facts
	print_pedigree_person($controller->rootid, 2, false, 1);
	// expand the layer
	echo PGV_JS_START, 'expandbox("', $controller->rootid, '.1", 2);', PGV_JS_END;
	// process the tree
	$treeid=ancestry_array($controller->rootid, $PEDIGREE_GENERATIONS-1);
	foreach ($treeid as $i=>$pid) {
		if ($pid) {
			$person=Person::getInstance($pid);
			if (!is_null($person)) {
				$famids=$person->getChildFamilies();
				foreach($famids as $famid=>$family) {
					$parents=find_parents_in_record($family->getGedcomRecord());
					if ($parents) {
						print_sosa_family($famid, $pid, $i);
					} elseif ($i==1) {
						// show empty family only if it is the first and only one
						print_sosa_family('', $pid, $i);
					}
				}
			}
		}
	}
	break;
case 2:
	// Individual list
	$treeid=ancestry_array($controller->rootid, $PEDIGREE_GENERATIONS);
	echo '<div class="center">';
	print_indi_table($treeid, $pgv_lang['ancestry_chart'].' : '.PrintReady($controller->name), 'sosa');
	echo '</div>';
	break;
case 3:
	// Family list
	$treeid=ancestry_array($controller->rootid, $PEDIGREE_GENERATIONS-1);
	$famlist=array();
	foreach ($treeid as $pid) {
		$person=Person::getInstance($pid);
		if (is_null($person)) {
			continue;
		}
		foreach ($person->getChildFamilies() as $famc) {
			$famlist[$famc->getXref()]=$famc;
		}
	}
	echo '<div class="center">';
	print_fam_table($famlist, $pgv_lang['ancestry_chart'].' : '.PrintReady($controller->name));
	echo '</div>';
	break;
}
print_footer();
?>

<?php

/**
 * Searches based on user query.
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
 * @subpackage Display
 * @version $Id$
 */
// Include the search controller from now on refered to as $controller
require_once ("includes/controllers/search_ctrl.php");
require_once ("includes/functions_print_lists.php");
// Print the top header
print_header($pgv_lang["search"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
	function checknames(frm) {
		action = "<?php print $controller->action ?>";
		if (action == "general")
		{
			if (frm.query.value.length<2) {
				alert("<?php print $pgv_lang["search_more_chars"]?>");
				frm.query.focus();
				return false;
			}
		}
		else if (action == "soundex")
		{
			year = frm.year.value;
			fname = frm.firstname.value;
			lname = frm.lastname.value;
			place = frm.place.value;

			// display an error message if there is insufficient data to perform a search on
			if (year == "") {
				message = true;
				if (fname.length >= 2)
					message = false;
				if (lname.length >= 2)
					message = false;
				if (place.length >= 2)
					message = false;
				if(message) {
					alert("<?php print $pgv_lang["search_more_chars"]?>");
					return false;
				}
			}

			// display a special error if the year is entered without a valid Given Name, Last Name, or Place
			if (year != "") {
				message = true;
				if (fname != "")
					message = false;
				if (lname != "")
					message = false;
				if (place != "")
					message = false;
				if (message) {
					alert("<?php print $pgv_lang["invalid_search_input"]?>");
					frm.firstname.focus();
					return false;
				}
			}
			return true;
		}
		else if (action == "multisite")
		{
			if(frm.subaction.value=='basic')
			{
				if (frm.multiquery.value.length < 2) {
					alert("<?php print $pgv_lang["search_more_chars"]?>");
					return false;
				}
			}
			else if(frm.subaction.value == 'advanced')
			{
				message = true;
				name = frm.name.value;
				bdate = frm.birthdate.value;
				bplace = frm.birthplace.value;
				ddate = frm.deathdate.value;
				dplace = frm.deathplace.value;
				gender = frm.gender.value;

				if(name.length > 1)
					message = false;
				if(bdate.length > 1)
					message = false;
				if (bplace.length > 1)
					message = false;
				if (ddate.length > 1)
					message = false;
				if (dplace.length > 1)
					message = false;
				if(message)
				{
					<?php if ($SHOW_MULTISITE_SEARCH >= PGV_USER_ACCESS_LEVEL) { ?>
					if(gender.length < 1)
					{
						alert("<?php print $pgv_lang["invalid_search_multisite_input"]?>");
						return false;
					}
					alert("<?php print $pgv_lang["invalid_search_multisite_input_gender"]?>");
					<?php } ?>
					return false;
				}
			}
		}
		return true;
	}

	function open_link(server, pid, indiName){
		window.open("addsearchlink.php?server="+server+"&pid="+pid+"&indiName="+indiName, "_blank", "top=50,left=50,width=600,height=500,scrollbars=1,scrollable=1,resizable=1");
		return false;
	}

//-->
</script>

<h2 class="center"><?php print $controller->getPageTitle(); ?></h2>
<?php $somethingPrinted = $controller->printResults(); ?>
<!--	/*************************************************** Search Form Outer Table **************************************************/ -->
<form method="post" name="searchform" onsubmit="return checknames(this);" action="search.php">
<input type="hidden" name="action" value="<?php print $controller->action; ?>" />
<input type="hidden" name="isPostBack" value="true" />
<table class="list_table $TEXT_DIRECTION" width="35%" border="0">
	<tr>

<!--	/**************************************************** General Search Form *************************************************************/ -->
			<?php if($controller->action == "general") { ?>
				<td colspan="3" class="facts_label03" style="text-align:center;">
					<?php print $pgv_lang["search_general"]; print_help_link("search_enter_terms_help", "qm"); ?>
				</td>
	</tr>
	<!-- // search terms -->
	<tr>
		<td class="list_label" style="padding: 5px;">
			<?php print $pgv_lang["enter_terms"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;">
			<input tabindex="1" id="firstfocus" type="text" name="query" value="<?php if (isset($controller->myquery)) print $controller->myquery; ?>" />
		</td>
		<td class="list_value" style="vertical-align: middle; text-align: center; padding: 5px;"  rowspan="4">
			<input tabindex="2" type="submit" value="<?php print $pgv_lang["search"] ?>" />
		</td>
	</tr>
	<!-- // Choice where to search -->
	<tr>
		<td class="list_label" style="padding: 5px;">
			<?php print $pgv_lang["search_inrecs"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;">
			<input type="checkbox"
				<?php
	if (isset ($controller->srindi) || !$controller->isPostBack)
		print " checked=\"checked\" ";
?>
				value="yes" name="srindi" />
				<?php print $pgv_lang["search_indis"]; ?><br />
			<input type="checkbox"
				<?php
	if (isset ($controller->srfams))
		print " checked=\"checked\" ";
?>
				value="yes" name="srfams" />
				<?php print $pgv_lang["search_fams"]; ?><br />
			<input type="checkbox"
				<?php
	if (isset ($controller->srsour))
		print " checked=\"checked\" ";
?>
				value="yes" name="srsour" />
				<?php print $pgv_lang["search_sources"]; ?><br />
		</td>
	</tr>
	<!-- Choice to Exclude non-genealogical data -->
	<tr>
		<td class="list_label" style="padding: 5px;">
			<?php print_help_link("search_exclude_tags_help", "qm"); print $pgv_lang["search_tagfilter"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;">
			<input type="radio" name="tagfilter" value="on"
				<?php
	if (($controller->tagfilter == "on") || ($controller->tagfilter == ""))
		print " checked=\"checked\" ";
?> />
				<?php print $pgv_lang["search_tagfon"]; ?><br />
			<input type="radio" name="tagfilter" value="off"
				<?php

	if ($controller->tagfilter == "off")
		print " checked=\"checked\" ";
?> />
				<?php print $pgv_lang["search_tagfoff"]; ?>
		</td>
	</tr>
	<!-- Choice to show related persons/families (associates) -->
	<tr>
		<td class="list_label" style="padding: 5px;">
			<?php print_help_link("search_include_ASSO_help", "qm"); print $pgv_lang["search_asso_label"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;">
			<input type="checkbox" name="showasso" value="on"
				<?php
	if ($controller->showasso == "on") print " checked=\"checked\" "; ?> />
				<?php print $pgv_lang["search_asso_text"]; ?>
		</td>
	</tr>
			<?php

}
/**************************************************** Search and Replace Search Form ****************************************************/
if ($controller->action == "replace")
{
	if (PGV_USER_CAN_EDIT) {
?>
				<td colspan="3" class="facts_label03" style="text-align: center;">
					<?php print $pgv_lang["search_replace"]; print_help_link('search_replace_help', 'qm'); ?>
				</td>
	</tr>
	<!-- // search terms -->
	<tr>
		<td class="list_label" style="padding: 5px;"><?php print $pgv_lang["enter_terms"]; ?></td>
		<td class="list_value" style="padding: 5px;"><input tabindex="1" id="firstfocus" name="query" value="" type="text"/></td>
			<td class="list_value" style="vertical-align: middle; text-align: center; padding: 5px;"  rowspan="3">
			<input tabindex="2" type="submit" value="<?php print $pgv_lang["search"]; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label" style="padding: 5px;"><?php print $pgv_lang["replace_with"]; ?></td>
		<td class="list_value" style="padding: 5px;"><input tabindex="1" name="replace" value="" type="text"/></td>
	</tr>
	<!-- // Choice where to search -->
	<tr>
		<td class="list_label" style="padding: 5px;"><?php print $pgv_lang["search_inrecs"]; ?></td>
		<td class="list_value" style="padding: 5px;">
			<script type="text/javascript">
			<!--
				function checkAll(box) {
					if (!box.checked) {
						box.form.replaceNames.disabled = false;
						box.form.replacePlaces.disabled = false;
						box.form.replacePlacesWord.disabled = false;
					}
					else {
						box.form.replaceNames.disabled = true;
						box.form.replacePlaces.disabled = true;
						box.form.replacePlacesWord.disabled = true;
					}
				}
			//-->
			</script>
			<input checked="checked" onclick="checkAll(this);" value="yes" name="replaceAll" type="checkbox"/><?php print $pgv_lang["search_record"]; ?>
			<br/>
			<hr />
			<input checked="checked" disabled="disabled" value="yes" name="replaceNames" type="checkbox"/><?php print $pgv_lang["search_indis"]; ?>
			<br/>
			<input checked="checked" disabled="disabled" value="yes" name="replacePlaces" type="checkbox"/><?php print $pgv_lang["search_place"]; ?>
			<input checked="checked" disabled="disabled" value="yes" name="replacePlacesWord" type="checkbox"/><?php print $pgv_lang["search_place_word"]; ?>
			<br/>

		</td>
	</tr>
<?php
}
}

/**************************************************** Soundex Search Form *************************************************************/
if ($controller->action == "soundex") {
?>
				<td colspan="3" class="facts_label03" style="text-align:center; ">
					<?php print $pgv_lang["soundex_search"]; print_help_link("soundex_search_help", "qm"); ?>
				</td>
	</tr>
	<!-- // search terms -->
	<tr>
		<td class="list_label" width="35%">
			<?php print $pgv_lang["firstname_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="3" type="text" id="firstfocus" name="firstname" value="<?php print $controller->myfirstname; ?>" />
		</td>
		<td class="list_value" style="vertical-align: middle; text-align: center; padding: 5px;"  rowspan="6">
			<input tabindex="7" type="submit" value="<?php print $pgv_lang["search"]; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["lastname_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="4" type="text" name="lastname" value="<?php print $controller->mylastname; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["search_place"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="5" type="text" name="place" value="<?php print $controller->myplace; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["search_year"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="6" type="text" name="year" value="<?php print $controller->myyear; ?>" />
		</td>
	</tr>
	<!-- Soundex type options (Russell, DaitchM) -->
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["search_soundextype"]; ?>
		</td>
		<td class="list_value" >
			<input type="radio" name="soundex" value="Russell"
				<?php if (($controller->soundex == "Russell") || ($controller->soundex == "")) print " checked=\"checked\" "; ?> />
			<?php print $pgv_lang["search_russell"]; ?><br />
			<input type="radio" name="soundex" value="DaitchM"
				<?php if ($controller->soundex == "DaitchM") print " checked=\"checked\" "; ?> />
			<?php print $pgv_lang["search_DM"]; ?>
		</td>
	</tr>

	<!-- Individuals' names to print options (Names with hit, All names) -->
	<!-- <tr>
		<td class="list_label">
			<?php 	print $pgv_lang["search_prtnames"]; ?>
		</td>
		<td class="list_value">
			<input type="radio" name="nameprt" value="hit"
				<?php if (($controller->nameprt == "hit") || ($controller->nameprt == "")) print " checked=\"checked\" "; ?> />
				<?php print $pgv_lang["search_prthit"] ?><br />
			<input type="radio" name="nameprt" value="all"
				<?php if ($controller->nameprt == "all") print " checked=\"checked\" "; ?> />
				<?php print $pgv_lang["search_prtall"]; ?>
		</td>
	</tr> -->
	<tr>
		<td class="list_label" style="padding: 5px;">
			<?php print $pgv_lang["search_asso_label"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;">
			<input type="checkbox" name="showasso" value="on"
				<?php if ($controller->showasso == "on") print " checked=\"checked\" "; ?> />
				<?php print $pgv_lang["search_asso_text"]; ?>
		</td>
	</tr>
				<?php

}

/**************************************************** Multi Site Search Form *************************************************************/
if ($controller->action == "multisite") {
?>
					<input type="hidden" name="subaction" value="basic" />
					<td colspan="3" class="facts_label03" style="text-align:center; ">
						<?php print $pgv_lang["multi_site_search"]; print_help_link("multi_site_search_help", "qm"); ?>
					</td>
	</tr>
	<tr>
		<td class="list_label" >
			<?php print $pgv_lang["search_sites"]; ?>
		</td>
		<td colspan="2" class="list_value" align="center">
			<table>
				<tr>
					<td align="left" >
						<?php

	$i = 0;
	if ($controller->Sites) {
		foreach ($controller->Sites as $server) {
			print "<input tabindex=\"$i\" type=\"checkbox\" ";
			$vartemp = "server".$i;
			if (isset ($_REQUEST["$vartemp"])) {
				if ($_REQUEST["$vartemp"] == "on")
					print "checked=\"checked\" value=\"on\" ";
			} else
				if (!$controller->isPostBack)
					print "checked=\"checked\" value=\"on\" ";
			$controller->inputFieldNames[] = "server".$i;
			print "name=\"server".$i."\" />".$server['name']."<br />";
			$i ++;
		}
	} else {
		print $pgv_lang["no_known_servers"];
	}
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- // this is for the basic site search involving just a query string text -->
	<tr>
		<td colspan="3" class="facts_label02">
			<?php print $pgv_lang["basic_search_discription"]; ?>
		</td>
	</tr>
		<td class="list_label">
			<?php print $pgv_lang["basic_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?php print $i ?>" type="text" name="multiquery" value="<?php print $controller->mymultiquery; ?>" />
		</td>
		<td class="list_value" style="vertical-align: middle; text-align: center; padding: 5px;"  rowspan="1">
			<input tabindex="<?php print ($i+2); ?>" type="submit" value="<?print $pgv_lang["search"]; ?>" onclick="document.searchform.subaction.value='basic';"/>
		</td>
	</tr>
	<!-- // this is for the advanced site search -->
	<tr>
		<td class="facts_label02" colspan="3">
			<?php print $pgv_lang["advanced_search_discription"]; ?>
		</td>
	</tr>
	<!-- // Advanced search terms -->
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["name_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?php print ($i+3); ?>" type="text" name="name" value="<?php print $controller->myname; ?>" />
		</td>
		<td class="list_value" style="vertical-align: middle; text-align: center; padding: 5px;"  rowspan="6">
			<input tabindex="<?php print ($i+9); ?>" type="submit" value="<?php print $pgv_lang["search"]; ?>"
				onclick="document.searchform.subaction.value='advanced';"/>
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["birthdate_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?print ($i+4); ?>" type="text" name="birthdate" value="<?php print $controller->mybirthdate; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["birthplace_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?php print ($i+5); ?> " type="text" name="birthplace" value="<?php print $controller->mybirthplace; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["deathdate_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?php print ($i+6); ?>" type="text" name="deathdate" value="<?php print $controller->mydeathdate; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["deathplace_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?php ($i+7); ?>" type="text" name="deathplace" value="<?php print $controller->mydeathplace; ?>" />
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php print $pgv_lang["gender_search"]; ?>
		</td>
		<td class="list_value">
			<input tabindex="<?php ($i+8); ?>" type="text" name="gender" value="<?php print $controller->mygender; ?>" />
		</td>
	</tr>
	<?php

}
// If the search is a general or soundex search then possibly display checkboxes for the gedcoms
if ($controller->action == "general" || $controller->action == "soundex") {
	// If more than one GEDCOM, switching is allowed AND DB mode is set, let the user select
	if ((count($gGedcom) > 1) && ($ALLOW_CHANGE_GEDCOM)) {
?>
	<tr>
		<td class="list_label" style="padding: 5px;">
			<?php print $pgv_lang["search_geds"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;" colspan="2">
			<?php

		$i = 0;
		foreach ($gGedcom as $key => $gedarray) {
			$str = preg_replace(array ("/\./", "/-/", "/ /"), array ("_", "_", "_"), $key);
			$controller->inputFieldNames[] = "$str";
			print "<input type=\"checkbox\" ";
			if (isset ($_REQUEST["$str"]))
				print "checked=\"checked\" ";
			print "value=\"yes\" name=\"".$str."\""." />".$gedarray["title"]."<br />";
			$i ++;
		}
?>
		</td>
	</tr>
	<?php

	}
}
?>
<!--  not currently used
	<tr>
		<td class="list_label" style="padding: 5px;" >
			<?php print $pgv_lang["results_per_page"]; ?>
		</td>
		<td class="list_value" style="padding: 5px;" colspan="2">
			<select name="resultsPerPage">
				<option value="10" <?php if ($controller->resultsPerPage == 10) print " selected=\"selected\""; ?> >10</option>
				<option value="20" <?php if ($controller->resultsPerPage == 20) print " selected=\"selected\""; ?> >20</option>
				<option value="30" <?php if ($controller->resultsPerPage == 30) print " selected=\"selected\""; ?> >30</option>
				<option value="50" <?php if ($controller->resultsPerPage == 50) print " selected=\"selected\""; ?> >50</option>
				<option value="100"<?php if ($controller->resultsPerPage == 100)print " selected=\"selected\""; ?>>100</option>
			</select>
		</td>
	</tr>
	-->
	<tr>
		<td class="list_label" style="padding: 5px;" >
			<?php print $pgv_lang["other_searches"]; ?>
		</td>
		<td class="list_value" style="padding: 5px; text-align:center; " colspan="2" >
			<?php

if ($controller->action == "general") {
	print "<a href='?action=soundex'>".$pgv_lang["search_soundex"]."</a>";
	if(PGV_USER_CAN_EDIT) {
		print " | <a href='?action=replace'>".$pgv_lang["search_replace"]."</a>";
	}
	if ($SHOW_MULTISITE_SEARCH >= PGV_USER_ACCESS_LEVEL) {
		if (count($controller->Sites) > 0) {


			print " | <a href='?action=multisite'>".$pgv_lang["multi_site_search"]."</a></td></tr>";
		}
	}
}
else if ($controller->action == "replace")
{
	print "<a href='?action=general'>".$pgv_lang["search_general"]."</a> | ";
	print "<a href='?action=soundex'>".$pgv_lang["search_soundex"]."</a>";
		if ($SHOW_MULTISITE_SEARCH >= PGV_USER_ACCESS_LEVEL) {
			if (count($controller->Sites) > 0) {

				print " | <a href='?action=multisite'>".$pgv_lang["multi_site_search"]."</a></td></tr>";
			}
		}
}
else
	if ($controller->action == "soundex") {
		print "<a href='?action=general'>".$pgv_lang["search_general"]."</a>";
		if(PGV_USER_CAN_EDIT)
		{
			print " | <a href='?action=replace'>".$pgv_lang["search_replace"]."</a>";
		}
		if ($SHOW_MULTISITE_SEARCH >= PGV_USER_ACCESS_LEVEL) {
			if (count($controller->Sites) > 0) {
				print " | <a href='?action=multisite'>".$pgv_lang["multi_site_search"]."</a></td></tr>";
			}
		}
	}
	 else
		if ($controller->action == "multisite")
		{
			if(PGV_USER_CAN_EDIT)
			{
				print "<a href='?action=replace'>".$pgv_lang["search_replace"]."</a> | ";
			}

			print "<a href='?action=general'>".$pgv_lang["search_general"]."</a> | ";
			print "<a href='?action=soundex'>".$pgv_lang["search_soundex"]."</a></td></tr>";
		}

?>
		</td>
	</tr>
</table>
</form>
<br />
<?php

echo "<br /><br /><br />";
// set the focus on the first field unless multisite or some search results have been printed
if (($controller->action != "multisite") && !$somethingPrinted ) {
?>
	<script language="JavaScript" type="text/javascript">
	<!--
		document.getElementById('firstfocus').focus();
	//-->
	</script>
<?php
}
//-- somewhere the session gedcom gets changed, so we will change it back
$_SESSION['GEDCOM'] = $GEDCOM;
print_footer();
?>

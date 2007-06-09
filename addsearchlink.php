<?php
/**
 * Allows user to select a person on their server to create a remote link
 * to a person selected from the search results.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @version $Id: addsearchlink.php,v 1.3 2007/06/09 21:11:02 lsces Exp $
 */

require ("config.php");

print_simple_header($pgv_lang["title_search_link"]);

//-- only allow users with editing access to this page
if (!$gGedcom->isEditable()) {
	print $pgv_lang["access_denied"];
	print_simple_footer();
	exit;
}

//To use addsearchlink you should have come from a multisearch result link
if(isset($pid) && isset($server) && isset($indiName))
{
?>

<br/>
<center><font size="4"><?php echo $indiName ?></font><center><br/>
<table align="center">
	<tr>
		<td>
			<form method="post" name="addRemoteRelationship" action="addremotelink.php">
				<input type="hidden" name="action" value="addlink" />
				<input type="hidden" name="location" value="remote" />
				<input type="hidden" name="cbExistingServers" value="<?php print $server; ?>" />
				<input type="hidden" name="txtPID" value="<?php print $pid; ?>" />
		
				<table class="facts_table" align="center">
					<tr>
						<td class="facts_label03" colspan="3" align="center">
							<?php print_help_link("link_remote_help", "qm"); ?> <?php echo $pgv_lang["title_remote_link"];?>
						</td>
					</tr>
					<tr>
						<td class="descriptionbox width20" id="tdId"><?php print_help_link('link_person_id_help', 'qm');?> Local Person ID</td>
						<td class="optionbox"><input type="text" id="pid" name="pid" size="14"/></td>
						<td class="optionbox" rowspan="2"><br/>
							<input type="submit" value="<?php echo $pgv_lang['label_add_remote_link'];?>" id="btnSubmit" name="btnSubmit" value="add"/>
						</td>
					</tr>
					<tr>
						<td class="descriptionbox width20"><?php print_help_link('link_remote_rel_help', 'qm');?> <?php echo $pgv_lang["label_rel_to_current"];?></td>
						<td class="optionbox">
							<select id="cbRelationship" name="cbRelationship">
								<option value="self" selected><?php echo $pgv_lang["current_person"];?></option>
								<option value="mother"><?php echo $pgv_lang["mother"];?></option>
								<option value="father"><?php echo $pgv_lang["father"];?></option>
								<option value="husband"><?php echo $pgv_lang["husband"];?></option>
								<option value="wife"><?php echo $pgv_lang["wife"];?></option>
								<option value="son"><?php echo $pgv_lang["son"];?></option>
								<option value="daughter"><?php echo $pgv_lang["daughter"];?></option>
							</select>
						</td>		
					</tr>
    			</table><br/>
    		</form>
		</td>
	</tr>
</table>

<?php
}
else {
	print "<br/><center><b><font color=\"red\">Oh, now you're hacking!</font></b></center><br/>";
}
print_footer(); ?>
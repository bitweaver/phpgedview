	print "<tr><td class=\"topbottombar $text_dir\" colspan=\"2\">";
	print "<a href=\"javascript: ";
	if ($import_existing) print "ged_import";
	else print "add_gedcom";
	print "\" onclick=\"expand_layer('add-form');return false\"><img id=\"add-form_img\" src=\"images/";
	if ($startimport != "true") print "minus.gif";
	else print "plus.gif";
	print "\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" /></a>";
	print_help_link("add_gedcom_help", "qm","add_gedcom");
	print "&nbsp;<a href=\"javascript: ";
	if ($import_existing) print "ged_import";
	else print "add_gedcom";
	print "\" onclick=\"expand_layer('add-form');return false\">";
	if ($import_existing) print "ged_import";
	else print "add_gedcom";
	print "</a>";
	print "</td></tr>";
	print "<tr><td class=\"optionbox\">";
	print "<div id=\"add-form\" style=\"display: ";
	if ($startimport != "true") print "block ";
	else print "none ";
	print "\">";
		?>
		<input type="hidden" name="check" value="add" />
		<input type="hidden" name="action" value="<?php print $action; ?>" />
		<input type="hidden" name="import_existing" value="<?php print $import_existing; ?>" />
		<table class="facts_table">
			<?php
			$i = 0;
			if (!empty($error)) {
				print "<tr><td class=\"optionbox wrap\" colspan=\"2\">";
				print "<span class=\"error\">".$error."</span>\n";
				print "</td></tr>";
			}
			?>
			<tr>
				<td class="descriptionbox width20 wrap">
				<?php print_help_link("gedcom_path_help", "qm","gedcom_path");?>
				<?php print $pgv_lang["gedcom_file"]; ?></td>
				<td class="optionbox"><input type="text" name="GEDFILENAME" value="<?php if (isset($GEDFILENAME) && strlen($GEDFILENAME) > 4) print $GEDCOMS[$GEDFILENAME]["path"]; ?>" 
				size="60" dir ="ltr" tabindex="<?php $i++; print $i?>"	<?php if ((!$no_upload && isset($GEDFILENAME)) && (empty($error))) print "disabled "; ?> />
				</td>
			</tr>
		</table>
		<?php
	print "</div>";
	print "</td></tr>";

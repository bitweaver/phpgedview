
	<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
	</script>
	<?php
	if ($controller->max_generation) print "<span class=\"error\">".str_replace("#PEDIGREE_GENERATIONS#", convert_number($PEDIGREE_GENERATIONS), $pgv_lang["max_generation"])."</span>";
	if ($controller->min_generation) print "<span class=\"error\">".$pgv_lang["min_generation"]."</span>";
	?>
	<form name="people" method="get" action="pedigree.php">
		<input type="hidden" name="show_full" value="<?php print $controller->show_full; ?>" />
		<input type="hidden" name="talloffset" value="<?php print $controller->talloffset; ?>" />
		<table class="pedigree_table width="225">
			<tr>
				<td colspan="2" class="topbottombar" style="text-align:center; ">
					<?php print $pgv_lang["options"]; ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap"><?php print_help_link("rootid_help", "qm"); ?>
					<?php print $pgv_lang["root_person"]; ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="<?php print $controller->rootid; ?>" />
        			<?php print_findindi_link("rootid",""); ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
				<?php print_help_link("PEDIGREE_GENERATIONS_help", "qm"); ?>
				<?php print $pgv_lang["generations"]; ?>
				</td>
				<td class="optionbox">
					<select name="PEDIGREE_GENERATIONS">
					<?php
						for ($i=3; $i<=$MAX_PEDIGREE_GENERATIONS; $i++) {
							print "<option value=\"".$i."\"" ;
							if ($i == $controller->OLD_PGENS) print "selected=\"selected\" ";
							print ">".$i."</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("talloffset_help", "qm"); ?>
					<?php print $pgv_lang["orientation"]; ?>
				</td>
				<td class="optionbox">
					<input type="radio" name="talloffset" value="0" <?php if (!$controller->talloffset) print " checked=\"checked\" "; ?> onclick="document.people.talloffset.value='1';" /> <?php print $pgv_lang["portrait"]; ?>
					<br /><input type="radio" name="talloffset" value="1" <?php if ($controller->talloffset) print "checked=\"checked\" "; ?> onclick="document.people.talloffset.value='0';" /><?php print $pgv_lang["landscape"]; ?>
					<br />
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php print_help_link("show_full_help", "qm"); ?>
					<?php print $pgv_lang["show_details"]; ?>
				</td>
				<td class="optionbox">
					<input type="checkbox" value="<?php if ($controller->show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';"; else print "0\" onclick=\"document.people.show_full.value='1';"; ?>" />
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="2">
					<input type="submit" class="btn" value="<?php print $pgv_lang["view"]; ?>" />
				</td>
			</tr>
		</table>
	</form>
<?php } ?>
</div>

{strip}
<tr><td class="topbottombar" colspan="2">
<?php
	if ($startimport != "true") print "<input type=\"submit\" name=\"continue\" value=\"del_proceed\" />&nbsp;";
		if ($cleanup_needed && $skip_cleanup != $pgv_lang["skip_cleanup"]) {
//			print_help_link("skip_cleanup_help", "qm", "skip_cleanup");
			print "<input type=\"submit\" name=\"skip_cleanup\" value=\"".$pgv_lang["skip_cleanup"]."\" />&nbsp;\n";
		}
		if ($verify && $startimport != "true") print "<input type=\"button\" name=\"cancel\" value=\"".$pgv_lang["cancel"]."\" onclick=\"document.configform.override.value='no'; document.configform.no_upload.value='cancel_upload'; document.configform.submit(); \" />";
	?>
</td></tr>
{/strip}
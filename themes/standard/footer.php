<?php
if (!isset($without_close)) print "</div> <!-- closing div id=\"content\" -->\n";//FIXME uncomment as soon as ready

print "<div id=\"footer\" class=\"$TEXT_DIRECTION\">";

print contact_links();

print "\n\t<br /><div align=\"center\" style=\"width:99%;\">";
print "\n\t<a href=\"http://www.phpgedview.net\" target=\"_blank\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["gedview"]["other"]."\" width=\"100\" height=\"45\" border=\"0\" alt=\"PhpGedView Version $VERSION $VERSION_RELEASE - $DBTYPE\" title=\"PhpGedView Version $VERSION $VERSION_RELEASE - $DBTYPE\" /></a><br />";
print "\n\t<br />";
print_help_link("preview_help", "qm");
print "<a href=\"$SCRIPT_NAME?view=preview&amp;".get_query_string()."\">".$pgv_lang["print_preview"]."</a>";
print "<br />";
if ($SHOW_STATS || (isset($DEBUG) && ($DEBUG==true))) print_execution_stats();
if ($buildindex) print " ".$pgv_lang["build_error"]."  <a href=\"editgedcoms.php\">".$pgv_lang["rebuild_indexes"]."</a>\n";
if ((count($pgv_changes) > 0) && PGV_USER_CAN_ACCEPT)
{
print "<br />".$pgv_lang["changes_exist"].
" <a href=\"javascript:;\" onclick=\"window.open('edit_changes.php','_blank','width=600,height=500,resizable=1,scrollbars=1'); return false;\">".
$pgv_lang["accept_changes"]."</a>\n";
}
print "</div>";
print "</div> <!-- close div id=\"footer\" -->\n";
?>

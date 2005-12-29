<?php
/**
 * Repositories List
 *
 * Parses gedcom file and displays a list of the repositories in the file.
 *
 * The alphabet bar shows all the available letters users can click. The bar is built
 * up from the lastnames first letter. Added to this bar is the symbol @, which is
 * shown as a translated version of the variable <var>pgv_lang["NN"]</var>, and a
 * translated version of the word ALL by means of variable <var>$pgv_lang["all"]</var>.
 *
 * The details can be shown in two ways, with surnames or without surnames. By default
 * the user first sees a list of surnames of the chosen letter and by clicking on a
 * surname a list with names of people with that chosen surname is displayed.
 *
 * Beneath the details list is the option to skip the surname list or show it.
 * Depending on the current status of the list.
 *
 * @package PhpGedView
 * @subpackage Lists
 * @version $Id: repolist.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

require("config.php");

$repolist = get_repo_list();               //-- array of regular repository titles 
$addrepolist = get_repo_add_title_list();  //-- array of additional repository titlesadd

$cr = count($repolist);
$ca = count($addrepolist);
$ctot = $cr + $ca;

print_header($pgv_lang["repo_list"]);
print "<div class=\"center\">";
print "<h2>".$pgv_lang["repo_list"]."</h2>\n\t";

print "\n\t<table class=\"list_table $TEXT_DIRECTION\">\n\t\t<tr><td class=\"list_label\"";
if($cr>12)	print " colspan=\"2\"";
print "><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["repository"]["small"]."\" border=\"0\" title=\"".$pgv_lang["titles_found"]."\" alt=\"".$pgv_lang["titles_found"]."\" />&nbsp;&nbsp;";
print $pgv_lang["titles_found"];
print "</td></tr><tr><td class=\"$TEXT_DIRECTION list_value_wrap\"><ul>";

if ($cr>0){
	$i=1;
	// -- print the array
	foreach ($repolist as $key => $value) {
		print_list_repository($key, $value);
		if ($i==ceil($cr/2) && $cr>12) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		$i++;
	}
	// -- print the additional array
	foreach ($addrepolist as $key => $value) {
		print_list_repository($key, $value);
		if ($i==ceil($cr/2) && $cr>12) print "</ul></td><td class=\"list_value_wrap\"><ul>\n";
		$i++;
	}

	print "\n\t\t</ul></td>\n\t\t";
 
	print "</tr><tr><td>".$pgv_lang["total_repositories"]." ".count($repo_total);
	if (count($repo_hide)>0) print "  --  ".$pgv_lang["hidden"]." ".count($repo_hide);
}
else print "<span class=\"warning\"><i>".$pgv_lang["no_results"]."</span>";

print "</td>\n\t\t</tr>\n\t</table>";

print_help_link("repolist_listbox_help", "qm");
print "</div>";
print "<br /><br />";
print_footer();
?>
<?php
/**
 * Displays the details about a repository record.
 * Also shows how many sources reference this repository.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * @subpackage Lists
 * @version $Id: repo.php,v 1.4 2008/07/07 18:01:13 lsces Exp $
 */

require("config.php");
require_once 'includes/functions_print_facts.php';
require_once("includes/functions_print_lists.php");

if ($SHOW_SOURCES<PGV_USER_ACCESS_LEVEL) {
	header("Location: index.php");
	exit;
}

//-- keep the time of this access to help with concurrent edits
$_SESSION['last_access_time'] = time();

if (empty($action)) $action="";
if (empty($show_changes)) $show_changes = "yes";
if (empty($rid)) $rid = " ";
$rid = clean_input($rid);

global $PGV_IMAGES;

$accept_success=false;
if (PGV_USER_CAN_ACCEPT) {
	if ($action=="accept") {
		require_once("includes/functions_import.php");
		if (accept_changes($rid."_".$GEDCOM)) {
			$show_changes="no";
			$accept_success=true;
		}
	}
}

$nonfacts = array();

$repo = find_repo_record($rid);
//-- make sure we have the true id from the record
$ct = preg_match("/0 @(.*)@/", $repo, $match);
if ($ct>0) $rid = trim($match[1]);

$name = get_repo_descriptor($rid);
$add_descriptor = get_add_repo_descriptor($rid);
if ($add_descriptor) $name .= " - ".$add_descriptor;


print_header("$name - $rid - ".$pgv_lang["repo_info"]);

?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record() {
		var recwin = window.open("gedrecord.php?pid=<?php print $rid ?>", "_blank", "top=50,left=50,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
	}
	function showchanges() {
		window.location = '<?php print $SCRIPT_NAME.normalize_query_string($QUERY_STRING."&show_changes=yes"); ?>';
	}
//-->
</script>
<table class="list_table"><tr><td>
<?php
if ($accept_success) print "<b>".$pgv_lang["accept_successful"]."</b><br />";
print "\n\t<span class=\"name_head\">".PrintReady($name);

if ($SHOW_ID_NUMBERS) print " " . getLRM() . "($rid)" . getLRM();
print "</span><br />";
if (PGV_USER_CAN_EDIT) {
	if ($view!="preview") {
		if (isset($pgv_changes[$rid."_".$GEDCOM])) {
			if (!isset($show_changes)) {
				print "<a href=\"repo.php?rid=$rid&amp;show_changes=yes\">".$pgv_lang["show_changes"]."</a>"."  ";
			}
			else {
				if (PGV_USER_CAN_ACCEPT) print "<a href=\"repo.php?rid=$rid&amp;action=accept\">".$pgv_lang["accept_all"]."</a> | ";
				print "<a href=\"repo.php?rid=$rid\">".$pgv_lang["hide_changes"]."</a>"."  ";
			}
			print_help_link("show_changes_help", "qm");
			print "<br />";
		}
		if ($SHOW_GEDCOM_RECORD || PGV_USER_IS_ADMIN) {
			print "<a href=\"javascript:;\" onclick=\"return edit_raw('$rid');\">".$pgv_lang["edit_raw"]."</a>";
			print_help_link("edit_raw_gedcom_help", "qm");
			print " | ";
		}
		print "<a href=\"javascript:;\" onclick=\"return deleterepository('$rid');\">".$pgv_lang["delete_repo"]."</a>";
		print_help_link("delete_repo_help", "qm");
		print "<br />\n";
	}
	if (isset($show_changes)) {
		$newrepo = trim(find_updated_record($rid));
	}
}
print "<br />";

$repo = array();
if (isset($repo_id_list[$rid])) $repo = $repo_id_list[$rid];
else {
	print "&nbsp;&nbsp;&nbsp;<span class=\"warning\"><i>".$pgv_lang["no_results"]."</i></span>";
	print "<br /><br /><br /><br /><br /><br />\n";
	print_footer();
	exit;
}
$repofacts = array();
$gedlines = preg_split("/\n/", $repo["gedcom"]);
$lct = count($gedlines);
$factrec = "";	// -- complete fact record
$line = "";	// -- temporary line buffer
$linenum = 1;
for($i=1; $i<=$lct; $i++) {
	if ($i<$lct) $line = $gedlines[$i];
	else $line=" ";
	if (empty($line)) $line=" ";
	if (($i==$lct)||($line{0}==1)) {
		if (!empty($factrec) ) {
			$repofacts[] = array($factrec, $linenum);
		}
		$factrec = $line;
		$linenum = $i;
	}
	else $factrec .= "\n".$line;
}

//-- get new repo records
if (!empty($newrepo)) {
	$newrepofacts = array();
	$gedlines = preg_split("/\n/", $newrepo);
	$lct = count($gedlines);
	$factrec = "";	// -- complete fact record
	$line = "";	// -- temporary line buffer
	$linenum = 0;
	for($i=1; $i<=$lct; $i++) {
		if ($i<$lct) $line = $gedlines[$i];
		else $line=" ";
		if (empty($line)) $line=" ";
		if (($i==$lct)||($line{0}==1)) {
			$newrepofacts[] = array($factrec, $linenum);
			$factrec = $line;
			$linenum = $i;
		}
		else $factrec .= "\n".$line;
	}

	if (!empty($show_changes)) {
		//-- update old facts
		foreach($repofacts as $key=>$fact) {
			$found = false;
			foreach($newrepofacts as $indexval => $newfact) {
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$repofacts[$key][0].="\nPGV_OLD\n";
			}
		}
		//-- look for new facts
		foreach($newrepofacts as $key=>$newfact) {
			$found = false;
			foreach($repofacts as $indexval => $fact) {
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$newfact[0].="\nPGV_NEW\n";
				$repofacts[]=$newfact;
			}
		}
	}
}
print "\n<table class=\"facts_table\">";
foreach($repofacts as $indexval => $fact) {
	$factrec = $fact[0];
	$linenum = $fact[1];
//	$ft = preg_match("/1\s(_?\w+)\s(.*)/", $factrec, $match);
	$ft = preg_match("/1\s(\w+)\s(.*)/", $factrec, $match);
	if ($ft>0) $fact = $match[1];
	else $fact="";
	$fact = trim($fact);
	if (!empty($fact)) {
		if (showFact($fact, $rid)) {
			if ($fact=="OBJE") {
				print_main_media($rid);
			}
			else if ($fact=="NOTE") {
				print_main_notes($factrec, 1, $rid, $linenum);
			}
			else {
				print_fact($factrec, $rid, $linenum);
			}
		}
	}
}
//-- new fact link
if (($view!="preview") && PGV_USER_CAN_EDIT) {
	print_add_new_fact($rid, $repofacts, "REPO");
}
print "</table>\n\n";
//print "\n\t\t<br /><br /><span class=\"label\">".$pgv_lang["other_repo_records"]."</span>";
flush();

$query = "REPO @$rid@";
// -- array of sources
$mysourcelist = array();

$mysourcelist = search_sources($query);
uasort($mysourcelist, "itemsort");
$cs=count($mysourcelist);

print_sour_table($mysourcelist, PrintReady($name));  //@@@@@

print "<br /><br /></td><td valign=\"top\" class=\"noprint\">";

if ($view!="preview" &&(empty($SEARCH_SPIDER))) {
	print "\n\t<table cellspacing=\"10\" align=\"right\"><tr>";
	if ($SHOW_GEDCOM_RECORD) {
		print "\n\t\t<td align=\"center\" valign=\"top\"><span class=\"link\"><a href=\"javascript:show_gedcom_record();\"><img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["gedcom"]["small"]."\" border=\"0\" alt=\"\" /><br />".$pgv_lang["view_gedcom"]."</a>";
		print_help_link("show_repo_gedcom_help", "qm");
		print "</span></td>";
	}
	if ($SHOW_GEDCOM_RECORD && $ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL){
		print "</tr>\n\t\t<tr>";
	}
	if ($ENABLE_CLIPPINGS_CART>=PGV_USER_ACCESS_LEVEL) {
		print "<td align=\"center\" valign=\"top\"><span class=\"link\"><a href=\"clippings.php?action=add&amp;id=$rid&amp;type=repository\"><img class=\"icon\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["clippings"]["small"]."\" border=\"0\" alt=\"\" /><br />".$pgv_lang["add_to_cart"]."</a>";
		print_help_link("add_repository_clip_help", "qm");
		print "</span></td>";
	}
	if(!$SHOW_GEDCOM_RECORD && ($ENABLE_CLIPPINGS_CART<PGV_USER_ACCESS_LEVEL)){
		print "<td>&nbsp;</td>";
	}
	print "</tr></table>";
}
print "&nbsp;</td></tr></table>\n";
print_footer();

?>

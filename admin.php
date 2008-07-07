<?php
/**
 * Administrative User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team
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
 * @subpackage Admin
 * @version $Id: admin.php,v 1.10 2008/07/07 18:01:10 lsces Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require "includes/bitsession.php";

require  $confighelpfile["english"];
if (file_exists( $confighelpfile[$LANGUAGE])) require  $confighelpfile[$LANGUAGE];

if (!isset($action)) $action="";

print_header($pgv_lang["administration"]);

$d_pgv_changes = "";
if (count($pgv_changes) > 0) $d_pgv_changes = "<a href=\"javascript:;\" onclick=\"window.open('edit_changes.php','_blank','width=600,height=500,resizable=1,scrollbars=1'); return false;\">" . $pgv_lang["accept_changes"] . "</a>\n";

if (!isset($logfilename)) $logfilename = "";
$file_nr = 0;
$dir_var = opendir ($INDEX_DIRECTORY);
$dir_array = array();
while ($file = readdir ($dir_var)) {
	if (substr($file,-4)==".log" && substr($file,0,4)== "pgv-") {
		$dir_array[$file_nr] = $file; 
		$file_nr++;
	}
}
closedir($dir_var);
$d_logfile_str = "&nbsp;";
if (count($dir_array)>0) {
	sort($dir_array);
	$d_logfile_str = "<form name=\"logform\" action=\"admin.php\" method=\"post\">";
	$d_logfile_str .= $pgv_lang["view_logs"] . ": ";
	$d_logfile_str .= "\n<select name=\"logfilename\">\n";
	$ct = count($dir_array);
	for($x = 0; $x < $file_nr; $x++)

	{
		$ct--;
		$d_logfile_str .= "<option value=\"";
		$d_logfile_str .= $dir_array[$ct];
		if ($dir_array[$ct] == $logfilename) $d_logfile_str .= "\" selected=\"selected";
		$d_logfile_str .= "\">";
		$d_logfile_str .= $dir_array[$ct];
		$d_logfile_str .= "</option>\n";
	}
	$d_logfile_str .= "</select>\n";
// $d_logfile_str .= "<input type=\"submit\" name=\"logfile\" value=\" &gt; \" />";
	$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+document.logform.logfilename.options[document.logform.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\" />";
	$d_logfile_str .= "</form>";
}

$usermanual_filename = "docs/english/PGV-manual-en.html";
$d_LangName = "lang_name_" . "english";
$doc_lang = $pgv_lang[$d_LangName];
$new_usermanual_filename = "docs/" . $languages[$LANGUAGE] . "/PGV-manual-" . $language_settings[$LANGUAGE]["lang_short_cut"] . ".html";
if (file_exists($new_usermanual_filename)){$usermanual_filename = $new_usermanual_filename; $d_LangName = "lang_name_" . $languages[$LANGUAGE]; $doc_lang = $pgv_lang[$d_LangName];}

$d_img_module_str = "&nbsp;";
if (file_exists("img_editconfig.php")) $d_img_module_str = "<a href=\"img_editconfig.php?action=edit\">".$pgv_lang["img_admin_settings"]."</a><br />";

$err_write = file_is_writeable("config.php");

$verify_msg = false;
$warn_msg = false;
foreach(get_all_users() as $user_id=>$user_name) {
	if (get_user_setting($user_id, 'verified_by_admin')!='yes' && get_user_setting($user_id, 'verified')=='yes')  {
		$verify_msg = true;
	}
	$comment_exp=get_user_setting($user_id, 'comment_exp');
	if (!empty($comment_exp) && (strtotime($comment_exp) != "-1") && (strtotime($comment_exp) < time("U"))) {
		$warn_msg = true;
	}
	if ($verify_msg && $warn_msg) {
		break;
	}
}

?>
<script type="text/javascript">
<!--
function showchanges() {
	window.location.reload();
}
//-->
</script>
<?php
?>
	<table class="center <?php print $TEXT_DIRECTION ?> width90">
		<tr>
			<td colspan="2" class="topbottombar">
			<?php
      	global $gBitUser;
      	print "<h2>Bitweaver PhpGedView Port" . $VERSION . "<br />";
      	print tra("Administration");
      	print "</h2>";
      	print tra("Current System Time");
      	print " ".get_changed_date(date("j M Y"))." - ".date($TIME_FORMAT);
      	print "<br />".tra("User Time");
      	print " ".get_changed_date(date("j M Y", time()-$_SESSION["timediff"]))." - ".date($TIME_FORMAT, time()-$_SESSION["timediff"]);
      	if ( $gBitUser->IsAdmin() ) {
			if ($err_write) {
				print "<br /><span class=\"error\">";
				print $pgv_lang["config_still_writable"];
				print "</span><br /><br />";
			}
			if ($verify_msg) {
				print "<br />";
				print "<a href=\"useradmin.php?action=listusers&amp;filter=admunver\" class=\"error\">".$pgv_lang["admin_verification_waiting"]."</a>";
				print "<br /><br />";
			}
			if ($warn_msg) {
				print "<br />";
				print "<a href=\"useradmin.php?action=listusers&amp;filter=warnings\" class=\"error\" >".$pgv_lang["admin_user_warnings"]."</a>";
				print "<br /><br />";
			}
			}
		?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="descriptionbox" style="text-align:center; "><?php print $pgv_lang["select_an_option"]; ?></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	<tr>
		<td colspan="2" class="topbottombar" style="text-align:center; "><?php print $pgv_lang["admin_info"]; ?></td>
	</tr>
	<tr>
		<td class="optionbox width50"><?php print_help_link("readmefile_help", "qm"); ?><a href="readme.txt" target="manual" title="<?php print $pgv_lang["view_readme"]; ?>"><?php print $pgv_lang["readme_documentation"];?></a></td>
			<td class="optionbox width50"><?php print_help_link("config_help_help", "qm"); ?><a href="pgvinfo.php?action=confighelp"><?php print $pgv_lang["config_help"];?></a></td>
	</tr>
	<tr>
			<td class="optionbox width50"><?php print_help_link("registry_help", "qm"); ?><a href="http://phpgedview.sourceforge.net/registry.php" target="_blank"><?php print $pgv_lang["pgv_registry"];?></a></td>
		<td class="optionbox width50">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="topbottombar" style="text-align:center; "><?php print $pgv_lang["admin_geds"]; ?></td>
	</tr>
	<tr>
		<td class="optionbox width50"><?php print_help_link("edit_gedcoms_help", "qm"); ?><a href="editgedcoms.php"><?php print $pgv_lang["manage_gedcoms"];?></a></td>
		<td class="optionbox width50"><?php print_help_link("help_edit_merge.php", "qm"); ?><a href="edit_merge.php"><?php print $pgv_lang["merge_records"]; ?></a></td>
	</tr>
<?php if ($gBitUser->isAdmin()) { ?>
	<tr>
		<td class="optionbox with50"><?php print_help_link("edit_add_unlinked_person_help", "qm"); ?><a href="javascript: <?php print $pgv_lang["add_unlinked_person"]; ?>" onclick="addnewchild(''); return false;"><?php print $pgv_lang["add_unlinked_person"]; ?></a></td>
		<td class="optionbox width50"><?php print_help_link("edit_add_unlinked_source_help", "qm"); ?><a href="javascript: <?php print $pgv_lang["add_unlinked_source"]; ?>" onclick="addnewsource(''); return false;"><?php print $pgv_lang["add_unlinked_source"]; ?></a></td>
	</tr>
<?php } ?>
	<tr>
      <td class="optionbox width50">&nbsp;</td>
			<td class="optionbox width50"><?php if ($d_pgv_changes != "") print $d_pgv_changes; else print "&nbsp;"; ?></td>
	</tr>
   <?php if ( $gBitUser->IsAdmin() ) { ?>
	<tr>
		<td colspan="2" class="topbottombar" style="text-align:center; "><?php print $pgv_lang["admin_site"]; ?></td>
	</tr>
	<tr>
			<td class="optionbox width50"><?php print_help_link("help_editconfig.php", "qm"); ?><a href="editconfig.php"><?php print $pgv_lang["configuration"];?></a></td>
		<td class="optionbox width50"><?php print_help_link("help_faq.php", "qm"); ?><a href="faq.php"><?php print $pgv_lang["faq_list"];?></a></td>
   </tr>
   <tr>
	<td class="optionbox width50"><?php print_help_link("help_managesites", "qm"); ?><a href="manageservers.php"><?php print $pgv_lang["link_manage_servers"];?></a></td>
		</td>
			<td class="optionbox width50"><?php print $d_logfile_str; ?></td>
	</tr>
   <?php } ?>
	</table>

<?php
	if (isset($logfilename) and ($logfilename != "")) {
		print "<hr><table align=\"center\" width=\"70%\"><tr><td class=\"listlog\">";
		print "<strong>";
		print $pgv_lang["logfile_content"];
		print " [" . $INDEX_DIRECTORY . $logfilename . "]</strong><br /><br />";
		$lines=file($INDEX_DIRECTORY . $logfilename);
		$num = sizeof($lines);
		for ($i = 0; $i < $num ; $i++) {
			print $lines[$i] . "<br />";
		}
		print "</td></tr></table><hr>";
	}
?>
<script language="javascript" type="text/javascript">
<!--
function manageservers(){
	window.open("manageservers.php", "", "top=50,left=50,width=700,height=500,scrollbars=1,resizable=1");
}
//-->
</script>
<br /><br />
<?php
print_footer();
?>

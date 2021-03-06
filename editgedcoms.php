<?php
/**
 * UI for online updating of the config file.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 12 September 2005
 *
 * @author PGV Development Team
 * @package PhpGedView
 * @subpackage Admin
 * @see index/gedcoms.php
 * @version $Id$
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'phpgedview' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_admin_phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

/**
 * load the main configuration and context
 */
require "includes/bitsession.php";
require $confighelpfile["english"];
global $TEXT_DIRECTION;
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];

$action     =safe_GET('action', array('delete', 'setdefault'));
$ged        =safe_GET('ged',         get_all_gedcoms());
$default_ged=safe_GET('default_ged', get_all_gedcoms());

/**
 * Check if a gedcom file is downloadable over the internet
 *
 * @author opus27
 * @param string $gedfile gedcom file
 * @return mixed 	$url if file is downloadable, false if not
 */
function check_gedcom_downloadable($gedfile) {
	global $SERVER_URL, $pgv_lang;

	//$url = $SERVER_URL;
	$url = "http://localhost/";
	if (substr($url,-1,1)!="/") $url .= "/";
	$url .= rawurlencode($gedfile);
	@ini_set('user_agent','MSIE 4\.0b2;'); // force a HTTP/1.0 request
	@ini_set('default_socket_timeout', '10'); // timeout
	$handle = @fopen ($url, "r");
	if ($handle==false) return false;
	// open successfull : now make sure this is a GEDCOM file
	$txt = fread ($handle, 80);
	fclose($handle);
	if (strpos($txt, " HEAD")==false) return false;
	return $url;
}

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (!PGV_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=editgedcoms.php");
	exit;
}
print_header($pgv_lang["gedcom_adm_head"]);
print "<center>\n";
if ($action=="delete") {
	delete_gedcom($ged);
	print "<br />".str_replace("#GED#", $ged, $pgv_lang["gedcom_deleted"])."<br />\n";
}

if (($action=="setdefault") && in_array($default_ged, get_all_gedcoms())) {
	set_site_setting('DEFAULT_GEDCOM', $default_ged);
	$DEFAULT_GEDCOM=$default_ged;
} else {
	$DEFAULT_GEDCOM=get_site_setting('DEFAULT_GEDCOM');
}

print "<br /><br />";
?>
<span class="subheaders"><?php print_text("current_gedcoms"); ?></span><br />
<form name="defaultform" method="get" action="editgedcoms.php">
<input type="hidden" name="action" value="setdefault" />
<?php
// Default gedcom choice
print "<br />";
if ($gBitUser->isAdmin() && count(get_all_gedcoms())>1) {
	print_help_link("default_gedcom_help", "qm");
	print $pgv_lang["DEFAULT_GEDCOM"]."&nbsp;";
	print "<select name=\"default_ged\" class=\"header_select\" onchange=\"document.defaultform.submit();\">";
	if (!in_array($DEFAULT_GEDCOM, get_all_gedcoms())) {
		echo '<option value="" selected="selected" onclick="document.defaultform.submit();">', htmlspecialchars($DEFAULT_GEDCOM), '</option>';
	}
	foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
		print "<option value=\"".urlencode($ged_name)."\"";
		if ($DEFAULT_GEDCOM==$ged_name) print " selected=\"selected\"";
		print " onclick=\"document.defaultform.submit();\">";
		print PrintReady(get_gedcom_setting($ged_id, 'title'))."</option>";
	}
	print "</select><br /><br />";
}

print_help_link('SECURITY_CHECK_GEDCOM_DOWNLOADABLE_help', 'qm');
print '<a href="editgedcoms.php?check_download=true">'.$pgv_lang['SECURITY_CHECK_GEDCOM_DOWNLOADABLE']."</a>\n";
// Print table heading
print "<table class=\"gedcom_table\">";
if ($gBitUser->isAdmin()) {
	print "<tr><td class=\"list_label\">";
	print_help_link("help_addgedcom.php", "qm");
	print "<a href=\"editconfig_gedcom.php?source=add_form\">".$pgv_lang["add_gedcom"]."</a>";
	print "</td>";
	print "<td class=\"list_label\">";
	print_help_link("help_uploadgedcom.php", "qm");
	print "<a href=\"editconfig_gedcom.php?source=upload_form\">".$pgv_lang["upload_gedcom"]."</a>";
	print "</td>";
}
if ($gBitUser->isAdmin()) {
	print "<td class=\"list_label\">";
	print_help_link("help_addnewgedcom.php", "qm");
	print "<a href=\"editconfig_gedcom.php?source=add_new_form\">".$pgv_lang["add_new_gedcom"]."</a>";
	print "</td>";
}
print  "<td class=\"list_label\"><a href=\"admin.php\">" . $pgv_lang["lang_back_admin"] . "</a></td></tr>";
print "</table>";
print "<table class=\"gedcom_table\">";
$GedCount = 0;

// Print the table of available GEDCOMs
foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
	if (userGedcomAdmin(PGV_USER_ID, $ged_id)) {
		// Row 0: Separator line
		if ($GedCount!=0) {
			print "<tr>";
			print "<td colspan=\"6\">";
			print "<br /><hr class=\"gedcom_table\" /><br />";
			print "</td>";
			print "</tr>";
		}
		$GedCount++;

		// Row 1: Heading
		print "<tr>";
		print "<td colspan=\"1\" class=\"list_label\">".$pgv_lang["ged_title"]."</td>";
		print "<td colspan=\"6\" class=\"list_value_wrap\">";
		if ($DEFAULT_GEDCOM==$ged_name) print "<span class=\"label\">";
		print PrintReady(get_gedcom_setting($ged_id, 'title'))."&nbsp;&nbsp;";
		if ($TEXT_DIRECTION=="rtl") print getRLM() . "(".$ged_id.")" . getRLM();
		else print getLRM() . "(".$ged_id.")" . getLRM();
		if ($DEFAULT_GEDCOM==$ged_name) print "</span>";
		print "&nbsp;&nbsp;<a href=\"".encode_url("editconfig_gedcom.php?source=replace_form&path=".get_gedcom_setting($ged_id, 'path'))."&oldged=".get_gedcom_setting($ged_id, 'gedcom')."\">".$pgv_lang['upload_replacement']."</a>\n";
		print "</td>";
		print "</tr>";


		// Row 2: GEDCOM file name & functions
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1 (row legend)
		print_text("ged_gedcom");
		print "</td>";

		print "<td valign=\"top\">";		// Column 2 (file name & notices)
		if (file_exists(get_gedcom_setting($ged_id, 'path'))) {
			if ($TEXT_DIRECTION=="ltr") print get_gedcom_setting($ged_id, 'path')." (";
			else print getLRM() . get_gedcom_setting($ged_id, 'path')." " . getRLM() . "(";
			printf("%.2fKb", (filesize(get_gedcom_setting($ged_id, 'path'))/1024));
			print ")";
			/** deactivate [ 1573749 ]
			 * -- activating based on a request parameter instead of a config parameter
			 */
			if(!empty($_REQUEST['check_download'])){
				$url = check_gedcom_downloadable(get_gedcom_setting($ged_id, 'path'));
				if ($url!==false) {
					print "<br />\n";
					print "<span class=\"error\">".$pgv_lang["gedcom_downloadable"]." :</span>";
					print "<br /><a href=\"$url\">$url</a>";
				}
				else print "<br /><b>".str_replace("#GEDCOM#", get_gedcom_setting($ged_id, 'path'), $pgv_lang['gedcom_download_secure'])."</b><br />";
			}
		}
		else print "<span class=\"error\">".$pgv_lang["file_not_found"]."</span>";
		print "</td>";

		print "<td valign=\"top\">";		// Column 3  (Import action)
		print "<a href=\"".encode_url("uploadgedcom.php?GEDFILENAME={$ged_name}&verify=verify_gedcom&action=add_form&import_existing=1")."\">".$pgv_lang["ged_import"]."</a>";
		if (!check_for_import($ged_name)) {
			print "<br /><span class=\"error\">".$pgv_lang["gedcom_not_imported"]."</span>";
		}
		print "&nbsp;&nbsp;";
		print "</td>";

		echo '<td valign="top">';		// Column 4  (Export action)
		echo '<a href="javascript:" onclick="window.open(\'', encode_url("export_gedcom.php?export={$ged_name}"), '\', \'_blank\',\'left=50,top=50,width=500,height=500,resizable=1,scrollbars=1\');">', $pgv_lang['ged_export'], '</a>';
		echo '</td>';

		print "<td valign=\"top\">";		// Column 5  (Delete action)
		print "<a href=\"".encode_url("editgedcoms.php?action=delete&ged={$ged_name}")."\" onclick=\"return confirm('".$pgv_lang["confirm_gedcom_delete"]." ".preg_replace("/'/", "\'", $ged_name)."?');\">".$pgv_lang["delete"]."</a>";
		print "&nbsp;&nbsp;";
		print "</td>";

		print "<td valign=\"top\">";		// Column 6  (Download action)
		print "<a href=\"".encode_url("downloadgedcom.php?ged={$ged_name}")."\">".$pgv_lang["ged_download"]."</a>";
		print "&nbsp;&nbsp;";
		print "</td>";

		print "<td valign=\"top\">";		// Column 7  (Check action)
		print "<a href=\"".encode_url("gedcheck.php?ged={$ged_name}")."\">".$pgv_lang["ged_check"]."</a>";
		print "&nbsp;&nbsp;";
		print "</td>";

		print "</tr>";


		// Row 3: Configuration file
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		print_text("ged_config");
		print "</td>";

		print "<td valign=\"top\">";		// Column 2  (file name & notices)
		print getLRM() . get_gedcom_setting($ged_id, 'config');
		print "</td>";

		print "<td valign=\"top\">";		// Column 3  (Edit action)
		print "<a href=\"".encode_url("editconfig_gedcom.php?ged={$ged_name}")."\">".$pgv_lang["edit"]."</a>";
		print "</td>";

		print "<td colspan=\"4\" valign=\"top\">";		// Columns 4-7  (blank)
		print "&nbsp;";
		print "</td>";
		print "</tr>";

		// Row 4: Privacy File
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		print_text("ged_privacy");
		print "</td>";

		print "<td valign=\"top\">";		// Column 2  (file name & notices)
		print getLRM() . get_gedcom_setting($ged_id, 'privacy');
		print "</td>";

		print "<td valign=\"top\">";		// Column 3  (Edit action)
		print "<a href=\"".encode_url("edit_privacy.php?ged={$ged_name}")."\">".$pgv_lang["edit"]."</a>";
		print "</td>";

		print "<td colspan=\"4\" valign=\"top\">";		// Columns 4-7  (blank)
		print "&nbsp;";
		print "</td>";
		print "</tr>";

		// Row 5: Search Log File
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		print_text("ged_search");
		print "</td>";

		unset($SEARCHLOG_CREATE);
		unset($CHANGELOG_CREATE);
		if (file_exists(get_gedcom_setting($ged_id, 'config'))) {
			require get_gedcom_setting($ged_id, 'config');
		}
		print "<td valign=\"top\">";		// Column 2  (notices)
		if (!isset($SEARCHLOG_CREATE)) {
			print getLRM() . $pgv_lang["none"];
		}
		else {
			print getLRM() . $pgv_lang[$SEARCHLOG_CREATE];
		}
		print "</td>";

		print "<td colspan=\"5\" valign=\"top\">";		// Columns 3-7  (file name selector)
		// Get the logfiles
		if (!isset($logfilename)) $logfilename = "";
		$file_nr = 0;
		if (isset($dir_array)) unset($dir_array);
		$dir_var = opendir ($INDEX_DIRECTORY);
		while ($file = readdir ($dir_var))
		{
			if ((strpos($file, ".log") > 0) && (strstr($file, "srch-".$ged_name) !== false )) {$dir_array[$file_nr] = $file; $file_nr++;}
		}
		closedir($dir_var);
		$d_logfile_str  = "<form name=\"logform\" action=\"editgedcoms.php\" method=\"post\">";
		$d_logfile_str .= "\n<select name=\"logfilename\">\n";
		if(isset($dir_array)) {
			sort($dir_array);
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
			$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+this.form.logfilename.options[this.form.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\" />";
			$d_logfile_str .= "</form>";
			print $d_logfile_str;
		}
		print "</td>";

		print "</tr>";


		// Row 6: Change Log File
		print "<tr>";
		print "<td valign=\"top\">";		// Column 1  (row legend)
		print_text("ged_change");
		print "</td>";
		print "<td valign=\"top\">";		// Column 2  (notices)
		if (!isset($CHANGELOG_CREATE)) {
			print getLRM() . $pgv_lang["none"];
		}
		else {
			print getLRM() . $pgv_lang[$CHANGELOG_CREATE];
		}
		print "</td>";

		print "<td colspan=\"5\" valign=\"top\">";		// Columns 3-7  (file name selector)
		// Get the logfiles
		if (!isset($logfilename)) $logfilename = "";
		$file_nr = 0;
		if (isset($dir_array)) unset($dir_array);
		$dir_var = opendir ($INDEX_DIRECTORY);
		while ($file = readdir ($dir_var))
		{
			if ((strpos($file, ".log") > 0) && (strstr($file, "ged-".$ged_name) !== false )) {$dir_array[$file_nr] = $file; $file_nr++;}
		}
		closedir($dir_var);
		$d_logfile_str  = "<form name=\"logform2\" action=\"editgedcoms.php\" method=\"post\">";
		$d_logfile_str .= "\n<select name=\"logfilename\">\n";
		if(isset($dir_array)) {
			sort($dir_array);
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
			$d_logfile_str .= "<input type=\"button\" name=\"logfile\" value=\" &gt; \" onclick=\"window.open('printlog.php?logfile='+this.form.logfilename.options[this.form.logfilename.selectedIndex].value, '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\" />";
			$d_logfile_str .= "</form>";
			echo $d_logfile_str;
		}
		echo '</td></tr>';
	}
}
echo '</table></form></center>';

if (file_exists(get_gedcom_setting(PGV_GED_ID, 'config'))) {
	require get_gedcom_setting(PGV_GED_ID, 'config');
}

print_footer();
?>

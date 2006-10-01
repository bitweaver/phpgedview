<?php
/**
 * UI for online updating of the config file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * @version $Id: editgedcoms.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 */

require "config.php";
require $confighelpfile["english"];
global $TEXT_DIRECTION;
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];

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

if (!isset($action)) $action="";
if (!isset($ged)) $ged = "";

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$username = getUserName();
if (!userGedcomAdmin($username)) {
	header("Location: login.php?url=editgedcoms.php");
	exit;
}
print_header($pgv_lang["gedcom_adm_head"]);
print "<center>\n";
if ($action=="delete") {
	delete_gedcom($ged);
	unset($GEDCOMS[$ged]);
	store_gedcoms();
	print "<br />".str_replace("#GED#", $ged, $pgv_lang["gedcom_deleted"])."<br />\n";
}

if (($action=="setdefault") && isset($default_ged)) {
	$configtext = implode('', file($INDEX_DIRECTORY."gedcoms.php"));
	$configtext = preg_replace('/\$DEFAULT_GEDCOM\s*=\s*".*";/', "\$DEFAULT_GEDCOM = \"".urldecode($_POST["default_ged"])."\";", $configtext);
	$fp = fopen($INDEX_DIRECTORY."gedcoms.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		$DEFAULT_GEDCOM = urldecode($_POST["default_ged"]);
		fwrite($fp, $configtext);
		fclose($fp);
		$logline = AddToLog("gedcoms.php updated by >".getUserName()."<");
 		if (!empty($COMMIT_COMMAND)) check_in($logline, "gedcoms.php", $INDEX_DIRECTORY);	
	}
}

print "<br /><br />";
?>
<span class="subheaders"><?php print_text("current_gedcoms"); ?></span><br />
<form name="defaultform" method="post" action="editgedcoms.php">
<input type="hidden" name="action" value="setdefault" />
<?php
// Default gedcom choice
print "<br />";
if (count($GEDCOMS)>0) {
	if (userIsAdmin($username)) {
		print_help_link("default_gedcom_help", "qm");
		print $pgv_lang["DEFAULT_GEDCOM"]."&nbsp;";
		print "<select name=\"default_ged\" class=\"header_select\" onchange=\"document.defaultform.submit();\">";
		foreach($GEDCOMS as $gedc=>$gedarray) {
			if (empty($DEFAULT_GEDCOM)) $DEFAULT_GEDCOM = $gedc;
			print "<option value=\"".urlencode($gedc)."\"";
			if ($DEFAULT_GEDCOM==$gedc) print " selected=\"selected\"";
			print " onclick=\"document.defaultform.submit();\">";
			print PrintReady($gedarray["title"])."</option>";
		}
		print "</select><br /><br />";
	}
}

// Print table heading
print "<table class=\"gedcom_table\">";
if (userIsAdmin($username)) {
	print "<tr><td class=\"list_label\">";
	print_help_link("help_addgedcom.php", "qm");
	print "<a href=\"editconfig_gedcom.php?source=add_form\">".$pgv_lang["add_gedcom"]."</a>";
	print "</td>";
}
print "<td class=\"list_label\">";
print_help_link("help_uploadgedcom.php", "qm");
print "<a href=\"editconfig_gedcom.php?source=upload_form\">".$pgv_lang["upload_gedcom"]."</a>";
print "</td>";
if (userIsAdmin($username)) {
	print "<td class=\"list_label\">";
	print_help_link("help_addnewgedcom.php", "qm");
	print "<a href=\"editconfig_gedcom.php?source=add_new_form\">".$pgv_lang["add_new_gedcom"]."</a>";
	print "</td>";
}
print  "<td class=\"list_label\"><a href=\"admin.php\">" . $pgv_lang["lang_back_admin"] . "</a></td></tr>";
print "</table>";
$current_ged = $GEDCOM;
if (count($GEDCOMS)>0) {
print "<table class=\"gedcom_table\">";
$GedCount = 0;

// Print the table of available GEDCOMs
	foreach($GEDCOMS as $gedc=>$gedarray) {
		if (userGedcomAdmin($username, $gedc)) {
			if (empty($DEFAULT_GEDCOM)) $DEFAULT_GEDCOM = $gedc;

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
			print "<td colspan=\"4\" class=\"list_value_wrap\">";
			if ($DEFAULT_GEDCOM==$gedc) print "<span class=\"label\">";
			print PrintReady($gedarray["title"])."&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") print "&rlm;(".$gedarray["id"].")&rlm;";
			else print "&lrm;(".$gedarray["id"].")&lrm;";
			if ($DEFAULT_GEDCOM==$gedc) print "</span>";
			print "&nbsp;&nbsp;<a href=\"editconfig_gedcom.php?source=replace_form&amp;path=".urlencode($gedarray['path'])."\">".$pgv_lang['upload_replacement']."</a>\n";
			print "</td>";
			print "</tr>";


			// Row 2: GEDCOM file name & functions
			print "<tr>";
			print "<td valign=\"top\">";		// Column 1 (row legend)
			print_text("ged_gedcom");
			print "</td>";

			print "<td valign=\"top\">";		// Column 2 (file name & notices)
			if (file_exists($gedarray["path"])) {
				if ($TEXT_DIRECTION=="ltr") print $gedarray["path"]." (";
				else print "&lrm;".$gedarray["path"]." &rlm;(";
				printf("%.2fKb", (filesize($gedarray["path"])/1024));
				print ")";
				if($SECURITY_CHECK_GEDCOM_DOWNLOADABLE){
					$url = check_gedcom_downloadable($gedarray["path"]);
					if ($url!==false) {
						print "<br />\n";
						print "<span class=\"error\">".$pgv_lang["gedcom_downloadable"]." :</span>";
						print "<br /><a href=\"$url\">$url</a>";
					}
				}
			}
			else print "<span class=\"error\">".$pgv_lang["file_not_found"]."</span>";
			print "</td>";

			print "<td valign=\"top\">";		// Column 3  (Import action)
			print "<a href=\"uploadgedcom.php?GEDFILENAME=$gedc&amp;verify=verify_gedcom&amp;action=add_form&amp;import_existing=1\">".$pgv_lang["ged_import"]."</a>";
			if (!check_for_import($gedc)) {
				print "<br /><span class=\"error\">".$pgv_lang["gedcom_not_imported"]."</span>";
			}
			print "&nbsp;&nbsp;";
			print "</td>";

			print "<td valign=\"top\">";		// Column 4  (Delete action)
			print "<a href=\"editgedcoms.php?action=delete&amp;ged=".urlencode($gedc)."\" onclick=\"return confirm('".$pgv_lang["confirm_gedcom_delete"]." ".preg_replace("/'/", "\'", $gedc)."?');\">".$pgv_lang["delete"]."</a>";
			print "&nbsp;&nbsp;";
			print "</td>";

			print "<td valign=\"top\">";		// Column 5  (Download action)
			print "<a href=\"downloadgedcom.php?ged=$gedc\">".$pgv_lang["ged_download"]."</a>";
			print "&nbsp;&nbsp;";
			print "</td>";

			print "</tr>";


			// Row 3: Configuration file
			print "<tr>";
			print "<td valign=\"top\">";		// Column 1  (row legend)
			print_text("ged_config");
			print "</td>";

			print "<td valign=\"top\">";		// Column 2  (file name & notices)
			print "&lrm;".$gedarray["config"];
			print "</td>";

			print "<td valign=\"top\">";		// Column 3  (Edit action)
			print "<a href=\"editconfig_gedcom.php?ged=".urlencode($gedc)."\">".$pgv_lang["edit"]."</a>";
			print "</td>";

			print "<td colspan=\"2\" valign=\"top\">";		// Columns 4-6  (blank)
			print "&nbsp;";
			print "</td>";
			print "</tr>";

			// Row 4: Privacy File
			print "<tr>";
			print "<td valign=\"top\">";		// Column 1  (row legend)
			print_text("ged_privacy");
			print "</td>";

			print "<td valign=\"top\">";		// Column 2  (file name & notices)
			print "&lrm;".$gedarray["privacy"];
			print "</td>";

			print "<td valign=\"top\">";		// Column 3  (Edit action)
			print "<a href=\"edit_privacy.php?action=edit&amp;ged=".urlencode($gedc)."\">".$pgv_lang["edit"]."</a>";
			print "</td>";

			print "<td colspan=\"2\" valign=\"top\">";		// Columns 4-6  (blank)
			print "&nbsp;";
			print "</td>";
			print "</tr>";

			// Row 5: Search Log File
			print "<tr>";
			print "<td valign=\"top\">";		// Column 1  (row legend)
			print_text("ged_search");
			print "</td>";

			unset($SEARCHLOG_CREATE);
			require($gedarray["config"]);
			print "<td valign=\"top\">";		// Column 2  (notices)
			if (!isset($SEARCHLOG_CREATE)) {
				print "&lrm;".$pgv_lang["none"];
			}
			else {
				print "&lrm;".$pgv_lang[$SEARCHLOG_CREATE];
			}
			print "</td>";

			print "<td colspan=\"3\" valign=\"top\">";		// Columns 3-6  (file name selector)
			// Get the logfiles
			if (!isset($logfilename)) $logfilename = "";
			$file_nr = 0;
			if (isset($dir_array)) unset($dir_array);
			$dir_var = opendir ($INDEX_DIRECTORY);
			while ($file = readdir ($dir_var))
			{
				if ((strpos($file, ".log") > 0) && (strstr($file, "srch-".$gedc) !== false )) {$dir_array[$file_nr] = $file; $file_nr++;}
			}
			closedir($dir_var);
			$d_logfile_str  = "<form name=\"logform\" action=\"editgedcoms.php\" method=\"post\">";
			$d_logfile_str .= "\n<select name=\"logfilename\">\n";
			if(isset($dir_array)) {
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

			unset($CHANGELOG_CREATE);
			require($gedarray["config"]);
			print "<td valign=\"top\">";		// Column 2  (notices)
			if (!isset($CHANGELOG_CREATE)) {
				print "&lrm;".$pgv_lang["none"];
			}
			else {
				print "&lrm;".$pgv_lang[$CHANGELOG_CREATE];
			}
			print "</td>";

			print "<td colspan=\"3\" valign=\"top\">";		// Columns 3-6  (file name selector)
			// Get the logfiles
			if (!isset($logfilename)) $logfilename = "";
			$file_nr = 0;
			if (isset($dir_array)) unset($dir_array);
			$dir_var = opendir ($INDEX_DIRECTORY);
			while ($file = readdir ($dir_var))
			{
				if ((strpos($file, ".log") > 0) && (strstr($file, "ged-".$gedc) !== false )) {$dir_array[$file_nr] = $file; $file_nr++;}
			}
			closedir($dir_var);
			$d_logfile_str  = "<form name=\"logform2\" action=\"editgedcoms.php\" method=\"post\">";
			$d_logfile_str .= "\n<select name=\"logfilename\">\n";
			if(isset($dir_array)) {
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
		}
	}

print "</table>\n";
}
if (isset($GEDCOMS[$current_ged]) && file_exists($GEDCOMS[$current_ged]["config"])) require($GEDCOMS[$current_ged]["config"]);

print "</form>";
if (count($GEDCOMS)>2) {
	print "<table class=\"gedcom_table\">";
	if (userIsAdmin($username)) {
		print "<tr><td class=\"list_label\">";
		print_help_link("help_addgedcom.php", "qm");
		print "<a href=\"editconfig_gedcom.php?source=add_form\">".$pgv_lang["add_gedcom"]."</a>";
		print "</td>";
	}

	print "<td class=\"list_label\">";
	print_help_link("help_uploadgedcom.php", "qm");
	print "<a href=\"editconfig_gedcom.php?source=upload_form\">".$pgv_lang["upload_gedcom"]."</a>";
	print "</td>";
	if (userIsAdmin($username)) {
		print "<td class=\"list_label\">";
		print_help_link("help_addnewgedcom.php", "qm");
		print "<a href=\"editconfig_gedcom.php?source=add_new_form\">".$pgv_lang["add_new_gedcom"]."</a>";
		print "</td>";
	}
	print  "<td class=\"list_label\"><a href=\"admin.php\">" . $pgv_lang["lang_back_admin"] . "</a></td></tr>";
	print "</table>";
}

print "<br /><br />\n";
print "</center>";

print_footer();

?>

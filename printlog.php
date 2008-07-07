<?php
/**
 * Print logfiles
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
 * @version $Id: printlog.php,v 1.5 2008/07/07 18:01:12 lsces Exp $
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
require "config.php";

//-- only allow admins
if (!PGV_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=admin.php");
	exit;
}

loadLangFile("pgv_confighelp");

print_simple_header("Print logfile");

// Check for logtype
if (!isset($logfile)) exit;
if (substr($logfile,-4) != ".log") exit;
if (substr($logfile,0,4) == "pgv-") $logtype = "syslog";
else if (substr($logfile,0,4) == "srch") $logtype = "searchlog";
else if (substr($logfile,0,4) == "ged-") $logtype = "gedlog";
if (!isset($logtype)) exit;

// if it's a gedlog or searchlog, get the gedcom name from the filename
if ($logtype == "gedlog") {
	$p2 = strpos($logfile, ".ged");
	$gedname = substr($logfile, 4, $p2);
}
if ($logtype == "searchlog") {
	$p2 = strpos($logfile, ".ged");
	$gedname = substr($logfile, 5, $p2-1);
}

//-- make sure that they have admin status before they can use this page
$auth = false;
if (($logtype == "syslog") && PGV_USER_IS_ADMIN) $auth = true;
if ((($logtype == "gedlog") || ($logtype == "searchlog"))  && (userGedcomAdmin(PGV_USER_ID, $gedname))) $auth = true;

if ($auth) {

	// Read the file
	$lines=file($INDEX_DIRECTORY . $logfile);
	$lines = array_reverse($lines);
	$num = sizeof($lines);

	// Print
	print "<table class=\"facts_table ".$TEXT_DIRECTION."\">";

	if (($logtype == "syslog") || ($logtype == "gedlog")) {
		print "<tr><td colspan=\"3\" class=\"topbottombar\">".$pgv_lang["logfile_content"]." [" . getLRM() .$INDEX_DIRECTORY.$logfile."]</td></tr>";
		print "<tr><td colspan=\"3\" class=\"topbottombar\">";
		print"<input type=\"button\" value=\"".$pgv_lang["back"]."\" onclick='self.close()';/>&nbsp;<input type=\"button\" value=\"".$pgv_lang["refresh"]."\" onclick='window.location.reload()';/></td></tr>";
		print "<tr><td class=\"list_label width10\">".$pgv_lang["date_time"]."</td><td class=\"list_label width10\">".$pgv_lang["ip_address"]."</td><td class=\"list_label width80\">".$pgv_lang["log_message"]."</td></tr>";
		for ($i = 0; $i < $num ; $i++)	{
			print "<tr>";
			$result = preg_split("/ - /", $lines[$i], 3);
			//-- properly handle lines that may not have the correct format
			if (count($result)<3) {
				print "<td class=\"optionbox\" colspan=\"3\" dir=\"ltr\">".PrintReady($lines[$i])."</td>";
			}
			else {
				$result[2] = PrintReady($result[2]);
				for ($j = 0; $j < 3; $j++) {
					print "<td class=\"optionbox\" dir=\"ltr\">".$result[$j]."</td>";
				}
			}
			print "</tr>";
		}
		print "<tr><td colspan=\"3\" class=\"topbottombar\">";
	}

	if ($logtype == "searchlog") {
		print "<tr><td colspan=\"6\" class=\"topbottombar\">".$pgv_lang["logfile_content"]." [" . getLRM() .$INDEX_DIRECTORY.$logfile."]</td></tr>";
		print "<tr><td colspan=\"6\" class=\"topbottombar\">";
		print"<input type=\"button\" value=\"".$pgv_lang["back"]."\" onclick='self.close()';/>&nbsp;<input type=\"button\" value=\"".$pgv_lang["refresh"]."\" onclick='window.location.reload()';/></td></tr>";
		print "<tr><td class=\"list_label width10\">".$pgv_lang["date_time"]."</td><td class=\"list_label width10\">".$pgv_lang["ip_address"]."</td><td class=\"list_label width10\">".$pgv_lang["user_name"]."</td><td class=\"list_label width10\">".$pgv_lang["searchtype"]."</td><td class=\"list_label width10\">".$pgv_lang["type"]."</td><td class=\"list_label width50\">".$pgv_lang["query"]."</td></tr>";
		for ($i = 0; $i < $num ; $i++)	{
			print "<tr>";
			$result1 = preg_split("/<br \/>/", $lines[$i], 4);
			$result2 = preg_split("/ - /", $result1[0], 3);
			print "<td class=\"optionbox\" dir=\"ltr\">".substr($result2[0],13)."</td>";
			print "<td class=\"optionbox\" dir=\"ltr\">".substr($result2[1], 4)."</td>";
			print "<td class=\"optionbox\" dir=\"ltr\">";
			$suser = substr($result2[2], 6);
			if (empty($suser)) print "&nbsp;";
			else print $suser;
			print "</td>";
			if (substr($result1[1], 0, 4) == "Type") {
				print "<td class=\"optionbox\" dir=\"ltr\">&nbsp;</td>";
				print "<td class=\"optionbox\" dir=\"ltr\">".substr($result1[1], 6)."</td>";
				$result1[2] = trim($result1[2]);
				while (substr($result1[2],-6) == "<br />") $result1[2] = substr($result1[2],0,-6);
				if (substr($result1[1], -7) == "General")print "<td class=\"optionbox\" dir=\"ltr\">".substr($result1[2], 7)."</td>";
				else print "<td class=\"optionbox\">".$result1[2]."</td>";
			}
			else {
				print "<td class=\"optionbox\" dir=\"ltr\">".substr($result1[1], 12)."</td>";
				print "<td class=\"optionbox\" dir=\"ltr\">".substr($result1[2], 6)."</td>";
				$result1[3] = trim($result1[3]);
				while (substr($result1[3],-6) == "<br />") $result1[3] = substr($result1[3],0,-6);
				if (substr($result1[2], -7) == "General")print "<td class=\"optionbox\" dir=\"ltr\">".substr($result1[3], 7)."</td>";
				else print "<td class=\"optionbox\" dir=\"ltr\">".$result1[3]."</td>";
			}
			print "</tr>";
		}
		print "<tr><td colspan=\"6\" class=\"topbottombar\">";
	}
	print"<input type=\"button\" value=\"".$pgv_lang["back"]."\" onclick='self.close()';/>&nbsp;<input type=\"button\" value=\"".$pgv_lang["refresh"]."\" onclick='window.location.reload()';/></td></tr>";
	print "</table>";
	print "<br /><br />";
}
else {
	print "Not authorized!<br /><br />";
	print "<input type=\"button\" value=\"".$pgv_lang["back"]."\" onclick='self.close()';/><br /><br />";
}

print_simple_footer();
?>

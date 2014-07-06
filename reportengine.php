<?php
/**
 * Report Engine
 *
 * Processes PGV XML Reports and generates a report
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
 * @subpackage Reports
 * @version $Id$
 */

/**
 * Initialization
 */ 
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

require_once(PHPGEDVIEW_PKG_PATH."includes/functions/functions_charts.php");
require(PHPGEDVIEW_PKG_PATH."languages/lang.en.php");
//if (file_exists($factsfile[$LANGUAGE])) require($factsfile[$LANGUAGE]);

//-- try to increase the time limit because reports can take a long time
@set_time_limit($TIME_LIMIT*2);

$famid=safe_GET('famid');
$pid  =safe_GET('pid');

/**
 * function to get the values for the given tag
 */
function get_tag_values($tag) {
	global $tags, $values;

	$indexes = $tags[$tag];
	$vals = array();
	foreach($indexes as $indexval => $i) {
		$vals[] = $values[$i];
	}
	return $vals;
}

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['report'])) $report = $_REQUEST['report'];
if (isset($_REQUEST['output'])) $output = $_REQUEST['output'];
if (isset($_REQUEST['vars'])) $vars = $_REQUEST['vars'];
if (isset($_REQUEST['varnames'])) $varnames = $_REQUEST['varnames'];
if (isset($_REQUEST['type'])) $type = $_REQUEST['type'];

if (empty($action)) $action = "choose";
if (!isset($report)) $report = "";
if (!isset($output)) $output = "PDF";
if (!isset($vars)) $vars = array();
if (!isset($varnames)) $varnames = array();
if (!isset($type)) $type = array();

//-- setup the arrays
$newvars = array();
foreach($vars as $name=>$var) {
	$newvars[$name]["id"] = $var;
	if (!empty($type[$name]) && (($type[$name]=="INDI")||($type[$name]=="FAM")||($type[$name]=="SOUR"))) {
		$gedcom = find_gedcom_record($var);
		if (empty($gedcom)) $action="setup";
		if ($type[$name]=="FAM") {
			if (preg_match("/0 @.*@ INDI/", $gedcom)>0) {
				$fams = find_sfamily_ids($var);
				if (!empty($fams[0])) {
					$gedcom = find_family_record($fams[0]);
					if (!empty($gedcom)) $vars[$name] = $fams[0];
					else $action="setup";
				}
			}
		}
		$newvars[$name]["gedcom"] = $gedcom;
	}
}
$vars = $newvars;

foreach($varnames as $indexval => $name) {
	if (!isset($vars[$name])) {
		$vars[$name]["id"] = "";
	}
}

$reports = get_report_list();
if (!empty($report)) {
	$r = basename($report);
	if (!isset($reports[$r]["access"])) $action = "choose";
	else if ($reports[$r]["access"]<PGV_USER_ACCESS_LEVEL) $action = "choose";
}

//-- choose a report to run
if ($action=="choose") {
	// Get the list of available reports in sorted localized title order
	$reportList = get_report_list(true);
	$reportTitles = array();
	foreach ($reportList as $file=>$report) {
		$reportTitles[$file] = $report["title"][$LANGUAGE];
	}
	asort($reportTitles);
	$reports = array();
	foreach ($reportTitles as $file=>$title) {
		$reports[$file] = $reportList[$file];
	}
	
	print_header($pgv_lang["choose_report"]);

	print "<br /><br />\n";
	print "<form name=\"choosereport\" method=\"get\" action=\"reportengine.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"setup\" />\n";
	print "<input type=\"hidden\" name=\"output\" value=\"$output\" />\n";
	print "<table class=\"facts_table width40 center $TEXT_DIRECTION\">";
	print "<tr><td class=\"topbottombar\" colspan=\"2\">".$pgv_lang["choose_report"]."</td></tr>";
	print "<tr><td class=\"descriptionbox wrap width33 vmiddle\">".$pgv_lang["select_report"]."</td>";
	print "<td class=\"optionbox\">";
	print "<select name=\"report\">\n";
	foreach($reports as $file=>$report) {
		if ($report["access"]>=PGV_USER_ACCESS_LEVEL)
			print "<option value=\"".$report["file"]."\">".$report["title"][$LANGUAGE]."</option>\n";
	}
	print "</select></td></tr>\n";
	print "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["click_here"]."\" /></td></tr>";
	print "</table>";
	print "</form>\n";
	print "<br /><br />\n";

	print_footer();
}

//-- setup report to run
else if ($action=="setup") {
	print_header($pgv_lang["enter_report_values"]);

	if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';

	//-- make sure the report exists
	if (!file_exists($report)) {
		print "<span class=\"error\">".$pgv_lang["file_not_found"]."</span> ".$report."\n";
	}
	else {
		require_once 'includes/reportheader.php';
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		//-- open the file
		if (!($fp = fopen($report, "r"))) {
			die("could not open XML input");
		}
		//-- read the file and parse it 4kb at a time
		while ($data = fread($fp, 4096)) {
			if (!xml_parse($xml_parser, $data, feof($fp))) {
				die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
			}
		}
		xml_parser_free($xml_parser);

		?>
<script type="text/javascript">
<!--
var pastefield;
function paste_id(value) {
	pastefield.value=value;
}
//-->
</script>
		<?php
		init_calendar_popup();
		print "<form name=\"setupreport\" method=\"get\" target=\"_blank\" action=\"reportengine.php\">\n";
		print "<input type=\"hidden\" name=\"action\" value=\"run\" />\n";
		print "<input type=\"hidden\" name=\"report\" value=\"$report\" />\n";
		print "<input type=\"hidden\" name=\"download\" value=\"\" />\n";
		//print "<input type=\"hidden\" name=\"output\" value=\"PDF\" />\n";

		print "<table class=\"facts_table width50 center $TEXT_DIRECTION\">";
		print "<tr><td class=\"topbottombar\" colspan=\"2\">".$pgv_lang["enter_report_values"]."</td></tr>";
		print "<tr><td class=\"descriptionbox width30 wrap\">".$pgv_lang["selected_report"]."</td><td class=\"optionbox\">".$report_array["title"]."</td></tr>\n";

		$doctitle = trim($report_array["title"]);
		$firstrun = 0;
		if (!isset($report_array["inputs"])) $report_array["inputs"] = array();
		foreach($report_array["inputs"] as $indexval => $input) {
			if ((($input["name"] == "sources") && ($SHOW_SOURCES>=PGV_USER_ACCESS_LEVEL)) || ($input["name"] != "sources")) {
				if (($input["name"] != "photos") || ($MULTI_MEDIA)) {
					// url forced default value ?
					if (isset($_REQUEST[$input["name"]])) {
						$input["default"]=$_REQUEST[$input["name"]];
						// update doc title for bookmarking
						$doctitle .= " ";
						if (strpos($input["name"],"date2")) $doctitle .= "-";
						$doctitle .= $input["default"];
						if (strpos($input["name"],"date1")) $doctitle .= "-";
					}
					print "<tr><td class=\"descriptionbox wrap\">\n";
					print "<input type=\"hidden\" name=\"varnames[]\" value=\"".$input["name"]."\" />\n";
					print $input["value"]."</td><td class=\"optionbox\">";
					if (!isset($input["type"])) $input["type"] = "text";
					if (!isset($input["default"])) $input["default"] = "";
					if (isset($input["lookup"])) {
						if ($input["lookup"]=="INDI") {
							if (!empty($pid)) $input["default"] = $pid;
							else $input["default"] = check_rootid($input["default"]);
						}
						if ($input["lookup"]=="FAM") {
							if (!empty($famid)) $input["default"] = $famid;
							else {
								$famid = find_sfamily_ids(check_rootid($input["default"]));
								if (empty($famid)) $famid = find_family_ids(check_rootid($input["default"]));
								if (isset($famid[0])) $input["default"] = $famid[0];
							}
						}
						if ($input["lookup"]=="SOUR") {
							if (!empty($sid)) $input["default"] = $sid;
						}
					}
					if ($input["type"]=="text") {
						print "<input type=\"text\" name=\"vars[".$input["name"]."]\" id=\"".$input["name"]."\" ";
						print "value=\"".$input["default"]."\" ";
						print " style=\"direction: ltr;\" ";
						print "/>";
					}
					if ($firstrun == 0) {
						?>
						<script language="JavaScript" type="text/javascript">
						<!--
							//document.getElementById('<?php print $input["name"]; ?>').focus();
						//-->
						</script>
						<?php
						$firstrun++;
					}
					if ($input["type"]=="checkbox") {
						print "<input type=\"checkbox\" name=\"vars[".$input["name"]."]\" id=\"".$input["name"]."\" value=\"1\"";
						if ($input["default"]=="1") print " checked=\"checked\"";
						print " />";
					}
					if ($input["type"]=="select") {
						print "<select name=\"vars[".$input["name"]."]\" id=\"".$input["name"]."_var\">\n";
						$options = preg_split("/[, ]+/", $input["options"]);
						foreach($options as $indexval => $option) {
							print "\t<option value=\"$option\"";
							if ($option==$input["default"]) print " selected=\"selected\"";
							print ">";
							if (isset($pgv_lang[$option])) print $pgv_lang[$option];
							else if (isset($factarray[$option])) print $factarray[$option];
							else print $option;
							print "</option>\n";
						}
						print "</select>\n";
					}
					if (isset($input["lookup"])) {
						print "<input type=\"hidden\" name=\"type[".$input["name"]."]\" value=\"".$input["lookup"]."\" />";
						if ($input["lookup"]=="FAM") print_findfamily_link("famid");
						if ($input["lookup"]=="INDI") print_findindi_link("pid","");
						if ($input["lookup"]=="PLAC") print_findplace_link($input["name"]);
						if ($input["lookup"]=="DATE") {
							$text = $pgv_lang["select_date"];
							if (isset($PGV_IMAGES["calendar"]["button"])) $Link = "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["calendar"]["button"]."\" name=\"a_".$input["name"]."\" id=\"a_".$input["name"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
							else $Link = $text;

							?>
							<a href="javascript: <?php print $input["name"]; ?>" onclick="cal_toggleDate('div_<?php print $input["name"]; ?>', '<?php print $input["name"]; ?>'); return false;">
							<?php print $Link;?>
							</a>
							<div id="div_<?php print $input["name"]; ?>" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
							<?php
						}
					}
					print "</td></tr>\n";
				}
			}
		}
		?>
		<tr><td class="descriptionbox width30 wrap"></td>
		<td class="optionbox">
		<table><tr>
		<td><img src="<?php echo isset($PGV_IMAGES["media"]["pdf"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["pdf"] : 'images/media/pdf.gif';?>" alt="PDF" title="PDF" /></td>
		<td><img src="<?php echo isset($PGV_IMAGES["media"]["html"]) ? $PGV_IMAGE_DIR.'/'.$PGV_IMAGES["media"]["html"] : 'images/media/html.gif';?>" alt="HTML" title="HTML" /></td>
		</tr><tr>
		<td><center><input type="radio" name="output" value="PDF" checked="checked" /></center></td>
		<td><center><input type="radio" name="output" value="HTML" <?php if ($output=="HTML") echo " checked=\"checked\"";?> /></center></td>
		</tr></table>
		</td></tr>
		<?php
		print "<tr><td class=\"topbottombar\" colspan=\"2\">";
		print " <input type=\"submit\" value=\"".$pgv_lang["download_report"]."\" onclick=\"document.setupreport.elements['download'].value='1';\"/>";
		print " <input type=\"submit\" value=\"".$pgv_lang["cancel"]."\" onclick=\"document.setupreport.elements['action'].value='setup';document.setupreport.target='';\"/>";
		print "</td></tr>\n";
		print "</table>\n";
		print "</form>\n";
		print "<br /><br />\n";
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
			document.title = '<?php print $doctitle; ?>';
		//-->
		</script>
		<?php
	}
	print_footer();
}
//-- run the report
else if ($action=="run") {
	//-- load the report generator
	switch ($output) {
	case 'HTML':
		require 'includes/classes/class_reporthtml.php';
		break;
	case 'PDF':
	default:
		require 'includes/classes/class_reportpdf.php';
		break;
	}

	//-- start the sax parser
	$xml_parser = xml_parser_create();
	//-- make sure everything is case sensitive
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	//-- set the main element handler functions
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	//-- set the character data handler
	xml_set_character_data_handler($xml_parser, "characterData");

	//-- open the file
	if (!($fp = fopen($report, "r"))) {
		die("could not open XML input");
	}
	//-- read the file and parse it 4kb at a time
	while ($data = fread($fp, 4096)) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);

}

?>

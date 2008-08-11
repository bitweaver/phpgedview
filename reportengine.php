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
 * @version $Id: reportengine.php,v 1.8 2008/08/11 15:27:25 lsces Exp $
 */

/**
 * Initialization
 */ 
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

require_once("includes/functions_charts.php");
require(PHPGEDVIEW_PKG_PATH."languages/lang.en.php");
//if (file_exists($factsfile[$LANGUAGE])) require($factsfile[$LANGUAGE]);

//-- try to increase the time limit because reports can take a long time
@set_time_limit($TIME_LIMIT*2);

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
	$var = clean_input($var);
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
//	if (!isset($reports[$r]["access"])) $action = "choose";
//	else if ($reports[$r]["access"]<PGV_USER_ACCESS_LEVEL) $action = "choose";
}

//-- choose a report to run
if ($action=="choose") {
	$reports = get_report_list(true);
	$gBitSmarty->assign_by_ref( "reports", $reports );

	$doctitle = "Report Generator";
	$gBitSmarty->assign( "pagetitle", $doctitle );
	$gBitSystem->display( 'bitpackage:phpgedview/report_select.tpl', tra( 'Report Generator' ) );
}

//-- setup report to run
else if ($action=="setup") {
	$doctitle = "Report ".$report." Settings";
	$gBitSmarty->assign( "pagetitle", $doctitle );
	$gBitSystem->display( 'bitpackage:phpgedview/report_setup.tpl', tra( 'Report Generator Setup' ) );
}
//-- run the report
else if ($action=="run") {
	//-- load the report generator
	if ($output=="HTML") require("includes/reporthtml.php");
	else if ($output=="TEX") require("includes/reportlatex.php");
	else require("includes/reportpdf.php");

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

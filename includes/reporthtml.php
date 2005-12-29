<?php
/**
 * HTML Report Generator
 *
 * used by the SAX parser to generate HTML reports from the XML report file.
 * @package PhpGedView
 * @subpackage Reports
 */
 
/**
 * element handlers array
 *
 * An array of element handler functions
 * @global array $elementHandler
 */
$elementHandler = array();
$elementHandler["PGVRDoc"]["start"] 		= "PGVRDocSHandler";
$elementHandler["PGVRDoc"]["end"] 			= "PGVRDocEHandler";
$elementHandler["PGVRHeader"]["start"] 		= "PGVRHeaderSHandler";
$elementHandler["PGVRHeader"]["end"] 		= "PGVRHeaderEHandler";
$elementHandler["PGVRFooter"]["start"] 		= "PGVRFooterSHandler";
$elementHandler["PGVRFooter"]["end"] 		= "PGVRFooterEHandler";
$elementHandler["PGVRBody"]["start"] 		= "PGVRBodySHandler";
$elementHandler["PGVRBody"]["end"] 			= "PGVRBodyEHandler";
$elementHandler["PGVRCell"]["start"] 		= "PGVRCellSHandler";
$elementHandler["PGVRCell"]["end"] 			= "PGVRCellEHandler";
$elementHandler["PGVRPageNum"]["start"]		= "PGVRPageNumSHandler";
$elementHandler["PGVRTotalPages"]["start"]	= "PGVRTotalPagesSHandler";
$elementHandler["PGVRNow"]["start"]			= "PGVRNowSHandler";

/**
 * should character data be printed
 *
 * this variable is turned on or off by the element handlers to tell whether the inner character 
 * data should be printed
 * @global bool $printData
 */
$printData = false;

/**
 * print data stack
 *
 * as the xml is being processed there will be times when we need to turn on and off the 
 * <var>$printData</var> variable as we encounter entinties in the xml.  The stack allows us to
 * keep track of when to turn <var>$printData</var> on and off.
 * @global array $printDataStack
 */
$printDataStack = array();

$pagenum = 1;
$totalpages = 1;

/**
 * page sized
 *
 * an array map of common page sizes
 * @global array $pageSizes
 */
$pageSizes["A4"]["width"] = "8.5in";
$pageSizes["A4"]["height"] = "11in";
 
/**
 * xml start element handler
 *
 * this function is called whenever a starting element is reached
 * @param resource $parser the resource handler for the xml parser
 * @param string $name the name of the xml element parsed
 * @param array $attrs an array of key value pairs for the attributes
 */
function startElement($parser, $name, $attrs) {
	global $elementHandler;
	
	if (isset($elementHandler[$name]["start"])) eval($elementHandler[$name]["start"]."(\$attrs);");
}

/**
 * xml end element handler
 *
 * this function is called whenever an ending element is reached
 * @param resource $parser the resource handler for the xml parser
 * @param string $name the name of the xml element parsed
 */
function endElement($parser, $name) {
	global $elementHandler;
	if (isset($elementHandler[$name]["end"])) eval($elementHandler[$name]["end"]."();");
}

/**
 * xml character data handler
 *
 * this function is called whenever raw character data is reached
 * just print it to the screen
 * @param resource $parser the resource handler for the xml parser
 * @param string $data the name of the xml element parsed
 */
function characterData($parser, $data) 
{
	global $printData;
	if ($printData) print $data;
}

function PGVRDocSHandler($attrs) {
	global $pageSizes;
	
	$CHARACTER_SET = "UTF-8";
	$pageSize = $attrs["pageSize"];
	$orientation = $attrs["orientation"];

	if (!isset($pageSizes[$pageSize])) $pageSize="A4";
	$pagew = $pageSizes[$pageSize]["width"];
	$pageh = $pageSizes[$pageSize]["height"];
	
	if ($orientation=="L") {
		$pagew = $pageSizes[$pageSize]["height"];
		$pageh = $pageSizes[$pageSize]["width"];
	}
	
	$margin = "1cm";
	
	//-- start html
	header("Content-Type: text/html; charset=$CHARACTER_SET");
	print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	print "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n\t<head>\n\t\t";
	print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$CHARACTER_SET\" />\n\t\t";
	print "<style>\n";
	print "@page { size: $pagew $pageh; margin: $margin; }\n";
	print ".page { page-break-before: always; }\n";
	print "#header { position: fixed; width: 100%; height: 1in; top: 0px; left: 0px; }\n";
	//print "#body { position: fixed; width: 100%; height: 10in; top: auto; left: 0px; }\n";
	print "#footer { position: fixed; width: 100%; height: 1in; top: 10in; left: 0px; }\n";
	//print "DIV { border: solid red 1px; }\n";
	print "</style>\n";
	print "</head>\n";
	print "<body>\n";
}

function PGVRDocEHandler() {
	print "</body>\n";
	print "</html>\n";
}

function PGVRHeaderSHandler($attrs) {
	print "<div id=\"header\">\n";
}

function PGVRHeaderEHandler() {
	print "</div>\n";
}

function PGVRFooterSHandler($attrs) {
	print "<div id=\"footer\">\n";
}

function PGVRFooterEHandler() {
	print "</div>\n";
}

function PGVRBodySHandler($attrs) {
	print "<div id=\"body\">\n";
}

function PGVRBodyEHandler() {
	print "</div>\n";
}

function PGVRCellSHandler($attrs) {
	global $printData, $printDataStack;
	
	array_push($printDataStack, $printData);
	$printData = true;
	
	$width = 0;
	$height= 0;
	$align= "left";
	
	if (isset($attrs["width"])) $width = $attrs["width"];
	if ($width=="0") $width="100%";
	if (isset($attrs["height"])) $height = $attrs["height"];
	if (isset($attrs["align"])) $align = $attrs["align"];
	
	print "<div style=\"width: $width; height: $height; text-align: $align; \">\n";
}

function PGVRCellEHandler() {
	global $printData, $printDataStack;
	
	print "</div>\n";
	$printData = array_pop($printDataStack);
}

function PGVRNowSHandler($attrs) {
	print date("d M Y");
}

function PGVRPageNumSHandler($attrs) {
	global $pagenum;
	
	print $pagenum;
}

function PGVRTotalPagesSHandler($attrs) {
	global $totalpages;
	
	print $totalpages;
}
?>
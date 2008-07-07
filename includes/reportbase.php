<?php
/**
 * Base Report Generator
 *
 * used by the SAX parser to generate reports from the XML report file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @version $Id: reportbase.php,v 1.1 2008/07/07 17:30:14 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * page sizes
 *
 * an array map of common page sizes
 * Page sizes should be specified in inches
 * @global array $pageSizes
 */
$pageSizes["A4"]["width"] = "8.27";		// 210 mm
$pageSizes["A4"]["height"] = "11.73";	// 297 mm
$pageSizes["A3"]["width"] = "11.73";	// 297 mm
$pageSizes["A3"]["height"] = "16.54";	// 420 mm
$pageSizes["A5"]["width"] = "5.83";		// 148 mm
$pageSizes["A5"]["height"] = "8.27";	// 210 mm
$pageSizes["letter"]["width"] = "8.5";	// 216 mm
$pageSizes["letter"]["height"] = "11";	// 279 mm
$pageSizes["legal"]["width"] = "8.5";	// 216 mm
$pageSizes["legal"]["height"] = "14";	// 356 mm

$ascii_langs = array("english", "danish", "dutch", "french", "german", "norwegian", "spanish", "spanish-ar");


//-- setup special characters array to force embedded fonts
$SpecialOrds = $RTLOrd;
for($i=195; $i<215; $i++) $SpecialOrds[] = $i;

if (!isset($embed_fonts)) {
	if (in_array($LANGUAGE, $ascii_langs)) $embed_fonts = false;
	else $embed_fonts = true;
}
//print "embed = $embed_fonts";

class PGVReportBase {
	/**
	 * PGVRStyles array
	 *
	 * an array of the PGVRStyles elements found in the document
	 * @var array $PGVRStyles
	 */
	var $PGVRStyles = array();

	var $pagew;
	var $pageh;
	var $orientation;
	var $margin;
	var $processing;
	var $title = "";
	
	function setup($pw, $ph, $pageSize, $o, $m, $showGenText=true) {
		global $pgv_lang, $VERSION, $vars, $pageSizes;

		// Determine the page dimensions
		$this->pageFormat = strtoupper($pageSize);
		if ($this->pageFormat == "LETTER") $this->pageFormat = "letter";
		if ($this->pageFormat == "LEGAL") $this->pageFormat = "legal";

		if (isset($pageSizes[$this->pageFormat]["width"])) {
			$this->pagew = $pageSizes[$this->pageFormat]["width"];
			$this->pageh = $pageSizes[$this->pageFormat]["height"];
		} else {
			if ($pw==0 || $ph==0) {
				$this->pageFormat = "A4";
				$this->pagew = $pageSizes["A4"]["width"];
				$this->pageh = $pageSizes["A4"]["height"];
			} else {
				$this->pageFormat = "";
				$this->pagew = $pw;
				$this->pageh = $ph;
			}
		}

		$this->orientation = strtoupper($o);
		if ($this->orientation == "L") {
			$temp = $this->pagew;
			$this->pagew = $this->pageh;
			$this->pageh = $temp;
		} else {
			$this->orientation = "P";
		}

		$this->margin = $m;
		$vars['pageWidth']['id'] = $this->pagew*72;
		$vars['pageHeight']['id'] = $this->pageh*72;
		
		$this->processing = "H";
	}
	
	function get_type() {
		return 'PGVReport';
	}
	
	function setProcessing($p) {
		$this->processing = $p;
	}
	
	function addElement(&$element) {
		print "PGVReportBase::addElement Not Implemented";
		return false;
	}
	
	function addTitle($data) {
		$this->title .= $data;
	}
	
	function addText($data) {
		$this->title .= $data;
	}
	
	function addStyle($style) {
		$this->PGVRStyles[$style["name"]] = $style;
	}

	function getStyle($s) {
		if (!isset($this->PGVRStyles[$s])) {
			return current($this->PGVRStyles);
		}
		return $this->PGVRStyles[$s];
	}
	
	function run() {
		print "PGVReportBase::run Not Implemented";
		return false;
	}
	
	function createCell($width, $height, $align, $style, $top=".", $left=".") {
		return new PGVRCell($width, $height, $align, $style, $top, $left);
	}
	
	function createTextBox($width, $height, $border, $fill, $newline, $left=".", $top=".", $pagecheck="true") {
		return new PGVRTextBox($width, $height, $border, $fill, $newline, $left, $top, $pagecheck);
	}
	
	function createText($style, $color) {
		return new PGVRText($style, $color);
	}
	
	function createFootnote($style="") {
		return new PGVRFootnote($style);
	}
	
	function createPageHeader() {
		return new PGVRPageHeader();
	}
	
	function createImage($file, $x, $y, $w, $h) {
		return new PGVRImage($file, $x, $y, $w, $h);
	}
	
	function createLine($x1, $y1, $x2, $y2) {
		return new PGVRLine($x1, $y1, $x2, $y2);
	}
	
	function createHTML($tag, $attrs) {
		return new PGVRHtml($tag, $attrs);
	}
}

/**
 * main PGV Report Element class that all other page elements are extended from
 */
class PGVRElement {
	var $text;

	function render(&$renderer) {
//		print "Nothing rendered.  Something bad happened";
//		debug_print_backtrace();
		//-- to be implemented in inherited classes
	}

	function getHeight(&$renderer) {
		return 0;
	}

	function getWidth(&$renderer) {
		return 0;
	}

	function addText($t) {
		global $embed_fonts, $TEXT_DIRECTION, $SpecialOrds;

		if (!isset($this->text)) $this->text = "";

		//$ord = ord(substr($t, 0, 1));
		//print "[".substr($t, 0, 1)."=$ord]";
		$found=false;
		foreach($SpecialOrds as $indexval => $ord) {
   			if (strpos($t, chr($ord))!==false) {
				$found=true;
			}
		}
   		if ($found) $embed_fonts = true;
   		$t = trim($t, "\r\n\t");
		$t = preg_replace(array("/<br \/>/", "/&nbsp;/"), array("\n", " "), $t);
		$t = strip_tags($t);
		$t = unhtmlentities($t);
		if ($embed_fonts) $t = bidi_text($t);
		//else $t = smart_utf8_decode($t);
		$this->text .= $t;
	}

	function addNewline() {
		if (!isset($this->text)) $this->text = "";
		$this->text .= "\n";
	}

	function getValue() {
		if (!isset($this->text)) $this->text = "";
		return $this->text;
	}
	
	function setWrapWidth($width) {
		return;
	}

	function renderFootnote(&$renderer) {
		return false;
		//-- to be implemented in inherited classes
	}

	function get_type() {
		return "PGVRElementBase";
	}
	
	function setText($text) {
		$this->text = $text;
	}
} //-- END PGVRElement

class PGVRHtml extends PGVRElement {
	var $tag;
	var $attrs;
	var $elements = array();
	
	function PGVRHtml($tag, $attrs) {
		$this->tag = $tag;
		$this->attrs = $attrs;
	}
	
	function getStart() {
		$str = "<".$this->tag." ";
		foreach($this->attrs as $key=>$value) {
			$str .= $key.'="'.$value.'" ';
		}
		$str .= ">";
		return $str;
	}
	
	function getEnd() {
		return "</".$this->tag.">";
	}
	
	
	function addElement(&$element) {
		$this->elements[] = $element;
	}
	
	function get_type() {
		return "PGVRHtml";
	}
}

/**
 * Cell element
 */
class PGVRCell extends PGVRElement {
	var $styleName;
	var $width;
	var $height;
	var $align;
	var $url;
	var $top;
	var $left;

	function PGVRCell($width, $height, $align, $style, $top=".", $left=".") {
		$this->text = "";
		$this->width = $width;
		$this->height = $height;
		$this->align = $align;
		$this->styleName = $style;
		$this->url = "";
		$this->top = $top;
		$this->left = $left;
	}

	function getHeight(&$renderer) {
		return $this->height;
	}

	function setUrl($url) {
		$this->url = $url;
	}

	function getWidth(&$renderer) {
		return $this->width;
	}

	function get_type() {
		return "PGVRCell";
	}
}

/**
 * TextBox element
 */
class PGVRTextBox extends PGVRElement {
	var $style;
	var $width;
	var $height;
	var $border;
	var $fill;
	var $newline;
	var $top;
	var $left;
	var $elements = array();
	var $pagecheck;

	function get_type() {
		return "PGVRTextBox";
	}

	function PGVRTextBox($width, $height, $border, $fill, $newline, $left=".", $top=".", $pagecheck="true") {
		$this->width = $width;
		$this->height = $height;
		$this->border = $border;
		$this->fill = $fill;
		$this->newline = $newline;
		if ($border>0) $this->style = "D";
		else $this->style = "";
		$this->top = $top;
		$this->left = $left;
		if ($pagecheck=="true") $this->pagecheck = true;
		else $this->pagecheck = false;
	}

	function addElement(&$element) {
		$this->elements[] = $element;
	}
}

/**
 * Text element
 */
class PGVRText extends PGVRElement {
	var $styleName;
	var $wrapWidth;
	var $wrapWidth2;

	function get_type() {
		return "PGVRText";
	}

	function PGVRText($style, $color) {
		$this->text = "";
		$this->color = $color;
		$this->wrapWidth = 0;
		$this->styleName = $style;
	}

	function setWrapWidth($width, $width2) {
		//print "setting wrap widths $width $width2\n";
		$this->wrapWidth = $width;
		if (preg_match("/^\n/", $this->text)>0) $this->wrapWidth=$width2;
		$this->wrapWidth2 = $width2;
		return $this->wrapWidth;
	}

	function getStyleName() {
		return $this->styleName;
	}
}

/**
 * Footnote element
 */
class PGVRFootnote extends PGVRElement {
	var $styleName;
	var $addlink;
	var $num;

	function get_type() {
		return "PGVRFootnote";
	}

	function PGVRFootnote($style="") {
		$this->text = "";
		if (!empty($style)) $this->styleName = $style;
		else $this->styleName="footnote";
	}

	function rerender(&$renderer) {
		global $footnote_count;
		return false;
	}

	function addText($t) {
		global $embed_fonts, $TEXT_DIRECTION, $SpecialOrds;

		if (!isset($this->text)) $this->text = "";

		$found=false;
		foreach($SpecialOrds as $indexval => $ord) {
   			if (strpos($t, chr($ord))!==false) $found=true;
		}
   		if ($found) $embed_fonts = true;

		$t = trim($t, "\r\n\t");
		$t = preg_replace("/<br \/>/", "\n", $t);
		// NOTE -- this will cause user added <brackets> to disappear?
		// TODO find out why strip_tags was added and how to fix the problem without it
		$t = strip_tags($t);
		$t = unhtmlentities($t);
		if ($embed_fonts) $t = bidi_text($t);
		else $t = smart_utf8_decode($t);
		$this->text .= $t;
	}

	function setNum($n) {
		$this->num = $n;
	}

	function setAddlink(&$a) {
		$this->addlink = $a;
	}
}

/**
 * PageHeader element
 */
class PGVRPageHeader extends PGVRElement {
	var $elements = array();

	function get_type() {
		return "PGVRPageHeader";
	}

	function PGVRTextBox() {
		$this->elements = array();
	}
	
	function PGVRPageHeader() {
		$this->elements = array();
	}

	function addElement($element) {
		$this->elements[] = $element;
	}
}

/**
 * image element
 */
class PGVRImage extends PGVRElement {
	var $width;
	var $height;
	var $file;
	var $x;
	var $y;

	function get_type() {
		return "PGVRImage";
	}

	function PGVRImage($file, $x, $y, $w, $h) {
//		print "$file $x $y $w $h";
		$this->file = $file;
		$this->x = $x;
		$this->y = $y;
		$this->width = $w;
		//print "height: $h ";
		$this->height = $h;
	}

	function getHeight(&$pdf) {
		return $this->height;
	}

	function getWidth(&$pdf) {
		return $this->width;
	}
} //-- END PGVRImage

/**
 * line element
 */
class PGVRLine extends PGVRElement {
	var $x1;
	var $y1;
	var $x2;
	var $y2;

	function get_type() {
		return "PGVRLine";
	}

	function PGVRLine($x1, $y1, $x2, $y2) {
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
	}

	function getHeight(&$pdf) {
		return abs($this->y2 - $this->y1);
	}

	function getWidth(&$pdf) {
		return abs($this->x2 - $this->x1);
	}
} //-- END PGVRLine

/**
 * element handlers array
 *
 * An array of element handler functions
 * @global array $elementHandler
 */
$elementHandler = array();
$elementHandler["PGVRStyle"]["start"] 		= "PGVRStyleSHandler";
$elementHandler["PGVRTitle"]["start"]		= "PGVRTitleSHandler";
$elementHandler["PGVRTitle"]["end"]			= "PGVRTitleEHandler";
$elementHandler["PGVRDescription"]["start"]	= "";
$elementHandler["PGVRInput"]["end"]			= "";
$elementHandler["PGVRInput"]["start"]		= "";
$elementHandler["PGVReport"]["end"]			= "";
$elementHandler["PGVReport"]["start"]		= "";
$elementHandler["PGVRDescription"]["end"]	= "";
$elementHandler["PGVRDoc"]["start"] 		= "PGVRDocSHandler";
$elementHandler["PGVRDoc"]["end"] 			= "PGVRDocEHandler";
$elementHandler["PGVRHeader"]["start"] 		= "PGVRHeaderSHandler";
$elementHandler["PGVRFooter"]["start"] 		= "PGVRFooterSHandler";
$elementHandler["PGVRBody"]["start"] 		= "PGVRBodySHandler";
$elementHandler["PGVRCell"]["start"] 		= "PGVRCellSHandler";
$elementHandler["PGVRCell"]["end"] 			= "PGVRCellEHandler";
$elementHandler["PGVRPageNum"]["start"]		= "PGVRPageNumSHandler";
$elementHandler["PGVRTotalPages"]["start"]	= "PGVRTotalPagesSHandler";
$elementHandler["PGVRNow"]["start"]			= "PGVRNowSHandler";
$elementHandler["PGVRGedcom"]["start"]		= "PGVRGedcomSHandler";
$elementHandler["PGVRGedcom"]["end"]		= "PGVRGedcomEHandler";
$elementHandler["PGVRTextBox"]["start"] 	= "PGVRTextBoxSHandler";
$elementHandler["PGVRTextBox"]["end"] 		= "PGVRTextBoxEHandler";
$elementHandler["PGVRText"]["start"] 		= "PGVRTextSHandler";
$elementHandler["PGVRText"]["end"] 			= "PGVRTextEHandler";
$elementHandler["PGVRGetPersonName"]["start"]	= "PGVRGetPersonNameSHandler";
$elementHandler["PGVRGedcomValue"]["start"]	= "PGVRGedcomValueSHandler";
$elementHandler["PGVRRepeatTag"]["start"]	= "PGVRRepeatTagSHandler";
$elementHandler["PGVRRepeatTag"]["end"]		= "PGVRRepeatTagEHandler";
$elementHandler["PGVRvar"]["start"]			= "PGVRvarSHandler";
$elementHandler["PGVRvarLetter"]["start"]	= "PGVRvarLetterSHandler";
$elementHandler["PGVRFacts"]["start"]		= "PGVRFactsSHandler";
$elementHandler["PGVRFacts"]["end"]			= "PGVRFactsEHandler";
$elementHandler["PGVRSetVar"]["start"]		= "PGVRSetVarSHandler";
$elementHandler["PGVRif"]["start"]			= "PGVRifSHandler";
$elementHandler["PGVRif"]["end"]			= "PGVRifEHandler";
$elementHandler["PGVRFootnote"]["start"]	= "PGVRFootnoteSHandler";
$elementHandler["PGVRFootnote"]["end"]		= "PGVRFootnoteEHandler";
$elementHandler["PGVRFootnoteTexts"]["start"]	= "PGVRFootnoteTextsSHandler";
$elementHandler["br"]["start"]				= "brSHandler";
$elementHandler["sp"]["start"]				= "spSHandler";
$elementHandler["PGVRPageHeader"]["start"] 		= "PGVRPageHeaderSHandler";
$elementHandler["PGVRPageHeader"]["end"] 		= "PGVRPageHeaderEHandler";
$elementHandler["PGVRHighlightedImage"]["start"] 		= "PGVRHighlightedImageSHandler";
$elementHandler["PGVRImage"]["start"] 		= "PGVRImageSHandler";
$elementHandler["PGVRLine"]["start"] 		= "PGVRLineSHandler";
$elementHandler["PGVRList"]["start"] 		= "PGVRListSHandler";
$elementHandler["PGVRList"]["end"] 		= "PGVRListEHandler";
$elementHandler["PGVRListTotal"]["start"]       = "PGVRListTotalSHandler";
$elementHandler["PGVRRelatives"]["start"] 		= "PGVRRelativesSHandler";
$elementHandler["PGVRRelatives"]["end"] 		= "PGVRRelativesEHandler";
$elementHandler["PGVRGeneration"]["start"]      = "PGVRGenerationSHandler";
$elementHandler["PGVRNewPage"]["start"]			= "PGVRNewPageSHandler";

$pgvreportStack = array();
$currentElement = new PGVRElement();

/**
 * should character data be printed
 *
 * this variable is turned on or off by the element handlers to tell whether the inner character
 * data should be printed
 * @global bool $printData
 */
$printData = false;
$reportTitle = false;

/**
 * print data stack
 *
 * as the xml is being processed there will be times when we need to turn on and off the
 * <var>$printData</var> variable as we encounter entinties in the xml.  The stack allows us to
 * keep track of when to turn <var>$printData</var> on and off.
 * @global array $printDataStack
 */
$printDataStack = array();

$gedrec = "";
$gedrecStack = array();

$repeats = array();
$repeatBytes = 0;
$repeatsStack = array();
$parser = "";
$parserStack = array();
$processRepeats = 0;
$processIfs = 0;
$processGedcoms = 0;

/**
 * xml start element handler
 *
 * this function is called whenever a starting element is reached
 * @param resource $parser the resource handler for the xml parser
 * @param string $name the name of the xml element parsed
 * @param array $attrs an array of key value pairs for the attributes
 */
function startElement($parser, $name, $attrs) {
	global $elementHandler, $processIfs, $processGedcoms, $processRepeats, $vars;

	$newattrs = array();
	$temp = "";
	foreach($attrs as $key=>$value) {
		$ct = preg_match("/^\\$(\w+)$/", $value, $match);
		if ($ct>0) {
			if ((isset($vars[$match[1]]["id"]))&&(!isset($vars[$match[1]]["gedcom"]))) $value = $vars[$match[1]]["id"];
			//print "$match[0]=$value\n";
		}
		$newattrs[$key] = $value;
	}
	$attrs = $newattrs;
	if (($processIfs==0 || $name=="PGVRif")&&($processGedcoms==0 || $name=="PGVRGedcom")&&($processRepeats==0 || $name=="PGVRFacts" || $name=="PGVRRepeatTag")) {
		if (isset($elementHandler[$name]["start"])) {
			if ($elementHandler[$name]["start"]!="") call_user_func($elementHandler[$name]["start"], $attrs);
		}
		else if (!isset($elementHandler[$name]["end"])) HTMLSHandler($name, $attrs);
	}
}

/**
 * xml end element handler
 *
 * this function is called whenever an ending element is reached
 * @param resource $parser the resource handler for the xml parser
 * @param string $name the name of the xml element parsed
 */
function endElement($parser, $name) {
	global $elementHandler, $processIfs, $processGedcoms, $processRepeats;

	if (($processIfs==0 || $name=="PGVRif")&&($processGedcoms==0 || $name=="PGVRGedcom")&&($processRepeats==0 || $name=="PGVRFacts" || $name=="PGVRRepeatTag" || $name=="PGVRList" || $name=="PGVRRelatives")) {
		if (isset($elementHandler[$name]["end"])) {
			if ($elementHandler[$name]["end"]!="") call_user_func($elementHandler[$name]["end"]);
		}
		else if (!isset($elementHandler[$name]["start"])) HTMLEHandler($name);
	}
}

/**
 * xml character data handler
 *
 * this function is called whenever raw character data is reached
 * just print it to the screen
 * @param resource $parser the resource handler for the xml parser
 * @param string $data the name of the xml element parsed
 */
function characterData($parser, $data) {
	global $printData, $currentElement, $processGedcoms, $processIfs, $reportTitle, $pgvreport;
	if ($reportTitle) $pgvreport->addTitle($data);
	if ($printData && ($processGedcoms==0) && ($processIfs==0)) $currentElement->addText($data);
}

function PGVRStyleSHandler($attrs) {
	global $pgvreport;

	if (empty($attrs["name"])) return;

	$name = $attrs["name"];
	$font = "Times";
	$size = 12;
	$style = "";
	if (isset($attrs["font"])) $font = $attrs["font"];
	if (isset($attrs["size"])) $size = $attrs["size"];
	if (isset($attrs["style"])) $style = $attrs["style"];

	$s = array();
	$s["name"] = $name;
	$s["font"] = $font;
	$s["size"] = $size;
	$s["style"] = $style;
	$pgvreport->addStyle($s);
}

function PGVRDocSHandler($attrs) {
	global $pageSizes, $parser, $xml_parser, $pgvreport;

	$parser = $xml_parser;

	$pageSize = $attrs["pageSize"];
	$orientation = $attrs["orientation"];
	$showGenText = true;
	if (isset($attrs['showGeneratedBy'])) $showGenText = $attrs['showGeneratedBy'];

	$margin = "";
	$margin = $attrs["margin"];

	$pgvreport->setup(0, 0, $pageSize, $orientation, $margin, $showGenText);
}

function PGVRDocEHandler() {
	global $pgvreport;

	$pgvreport->run();
}

function PGVRHeaderSHandler($attrs) {
	global $pgvreport;

	$pgvreport->setProcessing("H");
}

function PGVRPageHeaderSHandler($attrs) {
	global $printDataStack, $printData, $pgvreportStack, $pgvreport, $PGVReportRoot;

	array_push($printDataStack, $printData);
	$printData = false;

	array_push($pgvreportStack, $pgvreport);
	$pgvreport = $PGVReportRoot->createPageHeader();
}

function PGVRPageHeaderEHandler() {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement, $pgvreportStack;

	$printData = array_pop($printDataStack);
	$currentElement = $pgvreport;
	$pgvreport = array_pop($pgvreportStack);
	$pgvreport->addElement($currentElement);
}

function PGVRFooterSHandler($attrs) {
	global $pgvreport;

	$pgvreport->setProcessing("F");
}

function PGVRBodySHandler($attrs) {
	global $pgvreport;

	$pgvreport->setProcessing("B");
}


function PGVRCellSHandler($attrs) {
	global $printData, $printDataStack, $currentElement, $PGVReportRoot;

	array_push($printDataStack, $printData);
	$printData = true;

	$width = 0;
	$height= 0;
	$align= "left";
	$style= "";

	if (isset($attrs["width"])) $width = $attrs["width"];
	if (isset($attrs["height"])) $height = $attrs["height"];
	if (isset($attrs["align"])) $align = $attrs["align"];
	if ($align=="left") $align="L";
	if ($align=="right") $align="R";
	if ($align=="center") $align="C";
	if ($align=="justify") $align="J";

	if (isset($attrs["style"])) $style = $attrs["style"];

	$currentElement = $PGVReportRoot->createCell($width, $height, $align, $style);
}

function PGVRCellEHandler() {
	global $printData, $printDataStack, $currentElement, $pgvreport;

	$printData = array_pop($printDataStack);
	$pgvreport->addElement($currentElement);
}

function PGVRNowSHandler($attrs) {
	global $currentElement;

	$g = new GedcomDate(date("j M Y", client_time()));
	$currentElement->addText($g->Display());
}

function PGVRPageNumSHandler($attrs) {
	global $currentElement;

	$currentElement->addText("#PAGENUM#");
}

function PGVRTotalPagesSHandler($attrs) {
	global $currentElement;

	$currentElement->addText("{nb}");
}

function PGVRGedcomSHandler($attrs) {
	global $vars, $gedrec, $gedrecStack, $processGedcoms, $fact, $desc, $ged_level;

	if ($processGedcoms>0) {
		$processGedcoms++;
		return;
	}

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$tag = $attrs["id"];
	$tag = preg_replace("/@fact/", $fact, $tag);
	//print "[$tag]";
	$tags = preg_split("/:/", $tag);
	$newgedrec = "";
	if (count($tags)<2) {
		$newgedrec = find_gedcom_record($attrs["id"]);
	}
	if (empty($newgedrec)) {
		$tgedrec = $gedrec;
		$newgedrec = "";
		foreach($tags as $indexval => $tag) {
			//print "[$tag]";
			$ct = preg_match("/\\$(.+)/", $tag, $match);
			if ($ct>0) {
				if (isset($vars[$match[1]]["gedcom"])) $newgedrec = $vars[$match[1]]["gedcom"];
				else $newgedrec = find_gedcom_record($match[1]);
			}
			else {
				$ct = preg_match("/@(.+)/", $tag, $match);
				if ($ct>0) {
					$gt = preg_match("/\d $match[1] @([^@]+)@/", $tgedrec, $gmatch);
					//print $gt;
					if ($gt > 0) {
						//print "[".$gmatch[1]."]";
						$newgedrec = find_gedcom_record($gmatch[1]);
						//print $newgedrec;
						$tgedrec = $newgedrec;
					}
					else {
						//print "[$tgedrec]";
						$newgedrec = "";
						break;
					}
				}
				else {
					//$newgedrec = find_gedcom_record($gmatch[1]);
					$temp = preg_split("/\s+/", trim($tgedrec));
					$level = $temp[0] + 1;
					if (showFact($tag, $id)&&showFactDetails($tag,$id)) {
						$newgedrec = get_sub_record($level, "$level $tag", $tgedrec);
						$tgedrec = $newgedrec;
					}
					else {
						$newgedrec = "";
						break;
					}
				}
			}
		}
	}
	if (!empty($newgedrec)) {
		//$newgedrec = privatize_gedcom($newgedrec);
		$gedObj = new GedcomRecord($newgedrec);
		array_push($gedrecStack, array($gedrec, $fact, $desc));
		//print "[$newgedrec]";
		$gedrec = $gedObj->getGedcomRecord();
		$ct = preg_match("/(\d+) (_?[A-Z0-9]+) (.*)/", $gedrec, $match);
		if ($ct>0) {
			$ged_level = $match[1];
			$fact = $match[2];
			$desc = trim($match[3]);
		}
	}
	else {
		$processGedcoms++;
	}
}

function PGVRGedcomEHandler() {
	global $gedrec, $gedrecStack, $processGedcoms, $fact, $desc;

	if ($processGedcoms>0) {
		$processGedcoms--;
	}
	else {
		$temp = array_pop($gedrecStack);
		$gedrec = $temp[0];
		$fact = $temp[1];
		$desc = $temp[2];
	}
}

function PGVRTextBoxSHandler($attrs) {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement, $pgvreportStack, $PGVReportRoot;

	$width = 0;
	$height= 0;
	$border= 0;
	$newline = 0;
	$fill = "";
	$style = "D";
	$left = ".";
	$top = ".";
	$pagecheck="true";

	if (isset($attrs["width"])) $width = $attrs["width"];
	if (isset($attrs["height"])) $height = $attrs["height"];
	if (isset($attrs["border"])) $border = $attrs["border"];
	if (isset($attrs["newline"])) $newline = $attrs["newline"];
	if (isset($attrs["fill"])) $fill = $attrs["fill"];
	if (isset($attrs["left"])) $left = $attrs["left"];
	if (isset($attrs["top"])) $top = $attrs["top"];
	if (isset($attrs["pagecheck"])) $pagecheck = $attrs["pagecheck"];

	array_push($printDataStack, $printData);
	$printData = false;

	array_push($pgvreportStack, $pgvreport);
	$pgvreport = $PGVReportRoot->createTextBox($width, $height, $border, $fill, $newline, $left, $top, $pagecheck);
}

function PGVRTextBoxEHandler() {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement, $pgvreportStack;

	$printData = array_pop($printDataStack);
	$currentElement = $pgvreport;
	$pgvreport = array_pop($pgvreportStack);
	$pgvreport->addElement($currentElement);
}

function PGVRTextSHandler($attrs) {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement, $PGVReportRoot;

	array_push($printDataStack, $printData);
	$printData = true;

	$style = "";
	if (isset($attrs["style"])) $style = $attrs["style"];

	$color = "#000000";
	if (isset($attrs["color"])) $color = $attrs["color"];

	$currentElement = $PGVReportRoot->createText($style, $color);
}

function PGVRTextEHandler() {
	global $printData, $printDataStack, $pgvreport, $currentElement;

	$printData = array_pop($printDataStack);
	$pgvreport->addElement($currentElement);
}

function PGVRGetPersonNameSHandler($attrs) {
	global $currentElement, $vars, $gedrec, $gedrecStack, $pgv_lang;
	global $SHOW_ID_NUMBERS;

	$id = "";
	if (empty($attrs["id"])) {
		$ct = preg_match("/0 @(.+)@/", $gedrec, $match);
		if ($ct>0) $id = $match[1];
	}
	else {
		$ct = preg_match("/\\$(.+)/", $attrs["id"], $match);
		if ($ct>0) {
			if (isset($vars[$match[1]]["id"])) {
				$id = $vars[$match[1]]["id"];
			}
		}
		else {
			$ct = preg_match("/@(.+)/", $attrs["id"], $match);
			if ($ct>0) {
				$gt = preg_match("/\d $match[1] @([^@]+)@/", $gedrec, $gmatch);
				//print $gt;
				if ($gt > 0) {
					$id = $gmatch[1];
					//print "[$id]";
				}
			}
			else {
				$id = $attrs["id"];
			}
		}
	}
	if (!empty($id)) {
		$record = GedcomRecord::getInstance($id);
		if (is_null($record)) return;
		if (!$record->canDisplayDetails()) $currentElement->addText($pgv_lang["private"]);
		else {
			$name = $record->getName();
			$addname = $record->getAddName();
			if (hasRTLText($addname)) {
				$addname .= " ".$name;
				$name = $addname;
			}
			else if (!empty($addname)) $name .= " ".$addname;
			if (!empty($attrs["truncate"])) {
				if (strlen($name)>$attrs["truncate"]) {
					$name = preg_replace("/\(.*\) ?/", "", $name);
				}
				if (strlen($name)>$attrs["truncate"]) {
					$words = preg_split("/ /", $name);
					$name = $words[count($words)-1];
					for($i=count($words)-2; $i>=0; $i--) {
						$len = strlen($name);
						for($j=count($words)-3; $j>=0; $j--) {
							$len += strlen($words[$j]);
						}
						if ($len>$attrs["truncate"]) $name = get_first_letter($words[$i]).". ".$name;
						else $name = $words[$i]." ".$name;
					}
				}
			}
			$currentElement->addText(trim($name));
		}
	}
}

function PGVRGedcomValueSHandler($attrs) {
	global $currentElement, $vars, $gedrec, $gedrecStack, $fact, $desc, $type;
	global $SHOW_PEDIGREE_PLACES, $pgv_lang;

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$tag = $attrs["tag"];
	// print $tag;
	if (!empty($tag)) {
		if ($tag=="@desc") {
			if (showFact($fact, $id)&&showFactDetails($fact,$id)) $value = $desc;
			else $value = "";
			$value = trim($value);
			$currentElement->addText($value);
		}
		if ($tag=="@id") {
			$currentElement->addText($id);
		}
		else {
			$tag = preg_replace("/@fact/", $fact, $tag);
			if (empty($attrs["level"])) {
				$temp = preg_split("/\s+/", trim($gedrec));
				$level = $temp[0];
				if ($level==0) $level++;
			}
			else $level = $attrs["level"];
			$truncate = "";
			if (isset($attrs["truncate"])) $truncate=$attrs["truncate"];
			$tags = preg_split("/:/", $tag);
			//-- check all of the tags for privacy
			foreach($tags as $t=>$subtag) {
				if (!empty($subtag)) {
					if (!showFact($tag, $id)||!showFactDetails($tag,$id)) return;
				}
			}
			$value = get_gedcom_value($tag, $level, $gedrec, $truncate);
			if (showFact($fact, $id)&&showFactDetails($fact,$id)) $currentElement->addText($value);
		}
	}
}

function PGVRRepeatTagSHandler($attrs) {
	global $repeats, $repeatsStack, $gedrec, $repeatBytes, $parser, $parserStack, $processRepeats;
	global $fact, $desc;

	$processRepeats++;
	if ($processRepeats>1) return;

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	array_push($repeatsStack, array($repeats, $repeatBytes));
	$repeats = array();
	$repeatBytes = xml_get_current_line_number($parser);

	$tag = "";
	if (isset($attrs["tag"])) $tag = $attrs["tag"];
	if (!empty($tag)) {
		if ($tag=="@desc") {
			if (showFact($fact, $id)&&showFactDetails($fact,$id)) $value = $desc;
			else $value = "";
			$value = trim($value);
			$currentElement->addText($value);
		}
		else {
			$tag = preg_replace("/@fact/", $fact, $tag);
			$tags = preg_split("/:/", $tag);
			$temp = preg_split("/\s+/", trim($gedrec));
			$level = $temp[0];
			if ($level==0) $level++;
			$subrec = $gedrec;
			$t = $tag;
			for($i=0; $i<count($tags); $i++) {
				$t = $tags[$i];
				if (!empty($t)) {
				if ($level==1 && strstr("CHIL,FAMS,FAMC", $t)===false && (!showFact($t, $id) || !showFactDetails($t,$id))) return;
				if ($i<count($tags)-1) {
					$subrec = get_sub_record($level, "$level $t", $subrec);
					if (empty($subrec)) {
						$level--;
						$subrec = get_sub_record($level, "@ $t", $gedrec);
						if (empty($subrec)) return;
					}
				}
				//print "[$level $t] ";
				$level++;
			}
			}
			$level--;
			if ($level!=1 || strstr("CHIL,FAMS,FAMC", $t)!==false || (showFact($t, $id) && showFactDetails($t,$id))) {
				$ct = preg_match_all("/$level $t(.*)/", $subrec, $match, PREG_SET_ORDER);
				//print "$ct $subrec";
				for($i=0; $i<$ct; $i++) {
					$rec = get_sub_record($level, "$level $t", $gedrec, $i+1);
					$repeats[] = $rec;
				}
				//$repeats = array_reverse($repeats);
				//print_r($repeats);
			}
		}
	}
}

function PGVRRepeatTagEHandler() {
	global $repeats, $repeatsStack, $repeatBytes, $parser, $parserStack, $report, $pgvreport, $gedrec, $processRepeats;

	$processRepeats--;
	if ($processRepeats>0) return;

	$line = xml_get_current_line_number($parser)-1;
	$lineoffset = 0;
	for($i=0; $i<count($repeatsStack); $i++) {
		$p = $repeatsStack[$i];
		$l = $p[1];
		$lineoffset += $l;
	}
	//-- read the xml from the file
	$lines = file($report);
	$reportxml = "<tempdoc>\n";
	while(strstr($lines[$lineoffset+$repeatBytes], "<PGVRRepeatTag")===false) $lineoffset--;
	$lineoffset++;
	$line1 = $repeatBytes;
	$ct = 1;
	while(($ct>0)&&($line1<$line+2)) {
		if (strstr($lines[$lineoffset+$line1], "<PGVRRepeatTag")!==false) $ct++;
		if (strstr($lines[$lineoffset+$line1], "</PGVRRepeatTag")!==false) $ct--;
		$line1++;
	}
	$line = $line1-1;
	for($i=$repeatBytes+$lineoffset; $i<$line+$lineoffset; $i++) $reportxml .= $lines[$i];
	$reportxml .= "</tempdoc>\n";
	//print $reportxml."\n";
	array_push($parserStack, $parser);

	$oldgedrec = $gedrec;
	for($i=0; $i<count($repeats); $i++) {
		$gedrec = $repeats[$i];
		//-- start the sax parser
		$repeat_parser = xml_parser_create();
		$parser = $repeat_parser;
		//-- make sure everything is case sensitive
		xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($repeat_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($repeat_parser, "characterData");

		if (!xml_parse($repeat_parser, $reportxml, true)) {
			printf($reportxml."\nPGVRRepeatTagEHandler XML error: %s at line %d", xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser));
			print_r($repeatsStack);
			debug_print_backtrace();
			exit;
		}
		xml_parser_free($repeat_parser);
	}
	$parser = array_pop($parserStack);

	$gedrec = $oldgedrec;
	$temp = array_pop($repeatsStack);
	$repeats = $temp[0];
	$repeatBytes = $temp[1];
}

function PGVRvarSHandler($attrs) {
	global $currentElement, $vars, $gedrec, $gedrecStack, $pgv_lang, $factarray, $fact, $desc, $type;

	$var = $attrs["var"];
	if (!empty($var)) {
		if (!empty($vars[$var]['id'])) {
			$var = $vars[$var]['id'];
		}
		else {
			$tfact = $fact;
			if ($fact=="EVEN" || $fact=="FACT") $tfact = $type;
			$var = preg_replace(array("/\[/","/\]/","/@fact/","/@desc/"), array("['","']",$tfact,$desc), $var);
			eval("if (!empty(\$$var)) \$var = \$$var;");
			$ct = preg_match("/factarray\['(.*)'\]/", $var, $match);
			if ($ct>0) $var = $match[1];
		}
		if (!empty($attrs["date"])) {
			$g = new GedcomDate($var);
			$var = $g->Display();
		}
		$currentElement->addText($var);
	}
}

function PGVRvarLetterSHandler($attrs) {
	global $currentElement, $factarray, $fact, $desc;

	$var = $attrs["var"];
	if (!empty($var)) {
		$tfact = $fact;
		$var = preg_replace(array("/\[/","/\]/","/@fact/","/@desc/"), array("['","']",$tfact,$desc), $var);
		eval("if (!empty(\$$var)) \$var = \$$var;");

		$letter = get_first_letter($var);

		$currentElement->addText($letter);
	}
}

function PGVRFactsSHandler($attrs) {
	global $repeats, $repeatsStack, $gedrec, $parser, $parserStack, $repeatBytes, $processRepeats, $vars;

	$processRepeats++;
	if ($processRepeats>1) return;

	$families = 1;
	if (isset($attrs["families"])) $families = $attrs["families"];

	array_push($repeatsStack, array($repeats, $repeatBytes));
	$repeats = array();
	$repeatBytes = xml_get_current_line_number($parser);

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$tag = "";
	if (isset($attrs["ignore"])) $tag .= $attrs["ignore"];
	$ct = preg_match("/\\$(.+)/", $tag, $match);
	if ($ct>0) {
		$tag = $vars[$match[1]]["id"];
	}

	if (empty($attrs["diff"])) {
	$repeats = get_all_subrecords($gedrec, $tag, $families);
	}
	else {
		global $nonfacts;
		$nonfacts = preg_split("/[\s,;:]/", $tag);
		$person = new Person($gedrec);
//		print "<pre>".$gedrec."</pre>";
		$oldPerson = Person::getInstance($person->getXref());
//		print "<pre>".$oldPerson->getGedcomRecord()."</pre>";
		$oldPerson->diffMerge($person);
		$facts = $oldPerson->getIndiFacts();
		foreach($facts as $f=>$fact) {
			if (strstr($fact[1], "PGV_NEW")!==false) $repeats[] = $fact[1];
//			else if (strstr($fact[1], "PGV_OLD")!==false) $repeats[] = $fact[1];
		}
//		var_dump($repeats);
	}
}

function PGVRFactsEHandler() {
	global $repeats, $repeatsStack, $repeatBytes, $parser, $parserStack, $report, $gedrec, $fact, $desc, $type, $processRepeats;

	$processRepeats--;
	if ($processRepeats>0) return;

	$line = xml_get_current_line_number($parser)-1;
	$lineoffset = 0;
	for($i=0; $i<count($repeatsStack); $i++) {
		$p = $repeatsStack[$i];
		$l = $p[1];
		$lineoffset += $l;
	}
	//-- read the xml from the file
	$lines = file($report);
	$reportxml = "<tempdoc>\n";
	while($lineoffset+$repeatBytes>0 && strstr($lines[$lineoffset+$repeatBytes], "<PGVRFacts ")===false) $lineoffset--;
	$lineoffset++;
//	var_dump($lineoffset);
	for($i=$repeatBytes+$lineoffset; $i<$line+$lineoffset; $i++) {
//		print $i." ".htmlentities($lines[$i]);
		$reportxml .= $lines[$i];
	}
	$reportxml .= "</tempdoc>\n";

	array_push($parserStack, $parser);
	$oldgedrec = $gedrec;
	for($i=0; $i<count($repeats); $i++) {
		$gedrec = $repeats[$i];
		$ft = preg_match("/1 (\w+)(.*)/", $gedrec, $match);
		$fact = "";
		$desc = "";
		if ($ft > 0) {
			$fact = $match[1];
			if ($fact=="EVEN" || $fact=="FACT") {
				$tt = preg_match("/2 TYPE (.+)/", $gedrec, $tmatch);
				if ($tt>0) {
					$type = trim($tmatch[1]);
				}
			}
			$desc = trim($match[2]);
//			print $fact."[".$desc."]";
			$desc .= get_cont(2, $gedrec);
		}
		//-- start the sax parser
		$repeat_parser = xml_parser_create();
		$parser = $repeat_parser;
		//-- make sure everything is case sensitive
		xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($repeat_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($repeat_parser, "characterData");

		if (!xml_parse($repeat_parser, $reportxml, true)) {
			die(sprintf($reportxml."\nPGVRFactsEHandler XML error: %s at line %d", xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser)));
			debug_print_backtrace();
		}
		xml_parser_free($repeat_parser);
	}
	$parser = array_pop($parserStack);
	$gedrec = $oldgedrec;
	$temp = array_pop($repeatsStack);
	$repeats = $temp[0];
	$repeatBytes = $temp[1];
}

function PGVRSetVarSHandler($attrs) {
	global $vars, $gedrec, $gedrecStack, $pgv_lang, $factarray, $fact, $desc, $type, $generation;

	$name = $attrs["name"];
	$value = $attrs["value"];
	if ($value=="@ID") {
		$ct = preg_match("/0 @(.+)@/", $gedrec, $match);
		if ($ct>0) $value = $match[1];
	}
	if ($value=="@fact") {
		$value = $fact;
	}
	if ($value=="@desc") {
		$value = $desc;
	}
	if ($value=="@generation") {
		$value = $generation;
	}
	$ct = preg_match("/\\$(\w+)/", $name, $match);
	if ($ct>0) {
		$name = $vars["'".$match[1]."'"]["id"];
	}

	$ct = preg_match("/@(\w+)/", $value, $match);
	if ($ct>0) {
		$gt = preg_match("/\d $match[1] (.+)/", $gedrec, $gmatch);
		if ($gt > 0) $value = preg_replace("/@/", "", trim($gmatch[1]));
	}

	if ((substr($value, 0, 10) == "\$pgv_lang[") || (substr($value, 0, 11) == "\$factarray[")) {
		$var = preg_replace(array("/\[/","/\]/"), array("['","']"), $value);
		eval("\$value = $var;");
	}

	$ct = preg_match_all("/\\$(\w+)/", $value, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		// print $match[$i][1]."<br />";
		$t = $vars[$match[$i][1]]["id"];
		$value = preg_replace("/\\$".$match[$i][1]."/", $t, $value, 1);
	}

	$ct = preg_match("/(\d+)\s*([\-\+\*\/])\s*(\d+)/", $value, $match);
	if ($ct>0) {
		switch($match[2]) {
			case '-':
				$t = $match[1] - $match[3];
				$value = preg_replace("/".$match[1]."\s*([\-\+\*\/])\s*".$match[3]."/", $t, $value);
				break;
			case '+':
				$t = $match[1] + $match[3];
				$value = preg_replace("/".$match[1]."\s*([\-\+\*\/])\s*".$match[3]."/", $t, $value);
				break;
			case '*':
				$t = $match[1] * $match[3];
				$value = preg_replace("/".$match[1]."\s*([\-\+\*\/])\s*".$match[3]."/", $t, $value);
				break;
			case '/':
				$t = $match[1] / $match[3];
				$value = preg_replace("/".$match[1]."\s*([\-\+\*\/])\s*".$match[3]."/", $t, $value);
				break;
		}
	}
//	print "$name=[$value] ";
	if (strstr($value, "@")!==false) $value="";
	$vars[$name]["id"]=$value;
}

function PGVRifSHandler($attrs) {
	global $vars, $gedrec, $processIfs, $fact, $desc, $generation, $POSTAL_CODE;

	if ($processIfs>0) {
		$processIfs++;
		return;
	}

	$vars['POSTAL_CODE']['id'] = $POSTAL_CODE;
	$condition = $attrs["condition"];
	$condition = preg_replace("/\\$(\w+)/", "\$vars['$1'][\"id\"]", $condition);
	$condition = preg_replace(array("/ LT /", "/ GT /"), array("<", ">"), $condition);
	$ct = preg_match_all("/@([\w:\.]+)/", $condition, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$id = $match[$i][1];
		$value="''";
		if ($id=="ID") {
			$ct = preg_match("/0 @(.+)@/", $gedrec, $match);
			if ($ct>0) $value = "'".$match[1]."'";
		}
		else if ($id=="fact") {
			$value = "'$fact'";
		}
		else if ($id=="desc") {
			$value = "'$desc'";
		}
		else if ($id=="generation") {
			$value = "'$generation'";
		}
		else {
			$temp = preg_split("/\s+/", trim($gedrec));
			$level = $temp[0];
			if ($level==0) $level++;
			$value = get_gedcom_value($id, $level, $gedrec, "", false);
			//print "level:$level id:$id value:$value ";
			if (empty($value)) {
				$level++;
				$value = get_gedcom_value($id, $level, $gedrec, "", false);
				//print "level:$level id:$id value:$value gedrec:$gedrec<br />\n";
			}
			$value = "'".preg_replace("/'/", "\\'", $value)."'";
		}
		$condition = preg_replace("/@$id/", $value, $condition);
	}
	$condition = "if ($condition) return true; else return false;";
	$ret = @eval($condition);
//	print $condition."<br />";
	//print_r($vars);
//	if ($ret) print " true<br />"; else print " false<br />";
	if (!$ret) {
		$processIfs++;
	}
}

function PGVRifEHandler() {
	global $vars, $gedrec, $processIfs;

	if ($processIfs>0) $processIfs--;
}

function PGVRFootnoteSHandler($attrs) {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement, $footnoteElement, $PGVReportRoot;

	array_push($printDataStack, $printData);
	$printData = true;

	$style = "";
	if (isset($attrs["style"])) $style=$attrs["style"];
	$footnoteElement = $currentElement;
	$currentElement = $PGVReportRoot->createFootnote($style);
}

function PGVRFootnoteEHandler() {
	global $printData, $printDataStack, $pgvreport, $currentElement, $footnoteElement;

	$printData = array_pop($printDataStack);
	$temp = trim($currentElement->getValue());
	if (strlen($temp)>3) $pgvreport->addElement($currentElement);
	$currentElement = $footnoteElement;
}

function PGVRFootnoteTextsSHandler($attrs) {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement;

	$temp = "footnotetexts";
	$pgvreport->addElement($temp);
}

function brSHandler($attrs) {
	global $printData, $currentElement, $processGedcoms;
	if ($printData && ($processGedcoms==0)) $currentElement->addText("<br />");
}

function spSHandler($attrs) {
	global $printData, $currentElement, $processGedcoms;
	if ($printData && ($processGedcoms==0)) $currentElement->addText(" ");
}

function PGVRHighlightedImageSHandler($attrs) {
	global $gedrec, $pgvreport, $PGVReportRoot;

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$left = 0;
	$top = 0;
	$width = 0;
	$height = 0;
	if (isset($attrs["left"])) $left = $attrs["left"];
	if (isset($attrs["top"])) $top = $attrs["top"];
	if (isset($attrs["width"])) $width = $attrs["width"];
	if (isset($attrs["height"])) $height = $attrs["height"];

	if (showFact("OBJE", $id)) {
		$media = find_highlighted_object($id, $gedrec);
		if (!empty($media["file"])) {
			if (preg_match("/(jpg)|(jpeg)|(png)$/i", $media["file"])>0) {
				if (file_exists($media["file"])) {
					$size = findImageSize($media["file"]);
					if (($width>0)&&($size[0]>$size[1])) {
						$perc = $width / $size[0];
						$height= round($size[1]*$perc);
					}
					if (($height>0)&&($size[1]>$size[0])) {
						$perc = $height / $size[1];
						$width= round($size[0]*$perc);
					}
					$image = $PGVReportRoot->createImage($media["file"], $left, $top, $width, $height);
					$pgvreport->addElement($image);
				}
			}
		}
	}
}

function PGVRImageSHandler($attrs) {
	global $gedrec, $pgvreport, $MEDIA_DIRECTORY, $PGVReportRoot;

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$left = 0;
	$top = 0;
	$width = 0;
	$height = 0;
	if (isset($attrs["left"])) $left = $attrs["left"];
	if (isset($attrs["top"])) $top = $attrs["top"];
	if (isset($attrs["width"])) $width = $attrs["width"];
	if (isset($attrs["height"])) $height = $attrs["height"];
	$file = "";
	if (isset($attrs["file"])) $file = $attrs["file"];
	if ($file=="@FILE") {
		$ct = preg_match("/\d OBJE @(.+)@/", $gedrec, $match);
		if ($ct>0) $orec = find_gedcom_record($match[1]);
		else $orec = $gedrec;
		if (!empty($orec)) {
			$fullpath = extract_fullpath($orec);
			$filename = "";
			$filename = extract_filename($fullpath);
			$filename = $MEDIA_DIRECTORY.$filename;
			$filename = trim($filename);
			if (!empty($filename)) {
				if (preg_match("/(jpg)|(jpeg)|(png)$/i", $filename)>0) {
					if (file_exists($filename)) {
						$size = findImageSize($filename);
						if (($width>0)&&($height==0)) {
							$perc = $width / $size[0];
							$height= round($size[1]*$perc);
						}
						if (($height>0)&&($width==0)) {
							$perc = $height / $size[1];
							$width= round($size[0]*$perc);
						}
						//print "1 width:$width height:$height ";
						$image = $PGVReportRoot->createImage($filename, $left, $top, $width, $height);
						$pgvreport->addElement($image);
					}
				}
			}
		}
	}
	else {
		$filename = $file;
		if (preg_match("/(jpg)|(jpeg)|(png)$/i", $filename)>0) {
			if (file_exists($filename)) {
				$size = findImageSize($filename);
				if (($width>0)&&($size[0]>$size[1])) {
					$perc = $width / $size[0];
					$height= round($size[1]*$perc);
				}
				if (($height>0)&&($size[1]>$size[0])) {
					$perc = $height / $size[1];
					$width= round($size[0]*$perc);
				}
				//print "2 width:$width height:$height ";
				$image = $PGVReportRoot->createImage($filename, $left, $top, $width, $height);
				$pgvreport->addElement($image);
			}
		}
	}
}

function PGVRLineSHandler($attrs) {
	global $pgvreport,$PGVReportRoot;

	$x1 = 0;
	$y1 = 0;
	$x2 = 0;
	$y2 = 0;
	if (isset($attrs["x1"])) $x1 = $attrs["x1"];
	if (isset($attrs["y1"])) $y1 = $attrs["y1"];
	if (isset($attrs["x2"])) $x2 = $attrs["x2"];
	if (isset($attrs["y2"])) $y2 = $attrs["y2"];

	$line = $PGVReportRoot->createLine($x1, $y1, $x2, $y2);
	$pgvreport->addElement($line);
}

function PGVRListSHandler($attrs) {
	global $pgvreport, $gedrec, $repeats, $repeatBytes, $list, $repeatsStack, $processRepeats, $parser, $vars, $sortby;
	global $pgv_changes, $GEDCOM;

	$processRepeats++;
	if ($processRepeats>1) return;

	$sortby = "NAME";
	if (isset($attrs["sortby"])) $sortby = $attrs["sortby"];
	if (preg_match("/\\$(\w+)/", $sortby, $vmatch)>0) {
		$sortby = $vars[$vmatch[1]]["id"];
		$sortby = trim($sortby);
	}
	$list = array();
	$listname = "individual";
	if (isset($attrs["list"])) $listname=$attrs["list"];

	$filters = array();
	$filters2 = array();
	if (isset($attrs["filter1"])) {
		$j=0;
		foreach($attrs as $key=>$value) {
			$ct = preg_match("/filter(\d)/", $key, $match);
			if ($ct>0) {
				$condition = $value;
				$ct = preg_match("/@(\w+)/", $condition, $match);
				if ($ct > 0) {
					$id = $match[1];
					$value="''";
					if ($id=="ID") {
						$ct = preg_match("/0 @(.+)@/", $gedrec, $match);
						if ($ct>0) $value = "'".$match[1]."'";
					}
					else if ($id=="fact") {
						$value = "'$fact'";
					}
					else if ($id=="desc") {
						$value = "'$desc'";
					}
					else {
						$ct = preg_match("/\d $id (.+)/", $gedrec, $match);
						if ($ct>0) $value = "'".preg_replace("/@/", "", trim($match[1]))."'";
					}
					$condition = preg_replace("/@$id/", $value, $condition);
				}
				//-- handle regular expressions
				$ct = preg_match("/([A-Z:]+)\s*([^\s]+)\s*(.+)/", $condition, $match);
				if ($ct>0) {
					$tag = trim($match[1]);
					$expr = trim($match[2]);
					$val = trim($match[3]);
					if (preg_match("/\\$(\w+)/", $val, $vmatch)>0) {
						$val = $vars[$vmatch[1]]["id"];
						$val = trim($val);
					}
					$searchstr = "";
					$tags = preg_split("/:/", $tag);
					//-- only limit to a level number if we are specifically looking at a level
					if (count($tags)>1) {
						$level = 1;
						foreach($tags as $indexval => $t) {
							if (!empty($searchstr)) $searchstr.="[^\n]*(\n[2-9][^\n]*)*\n";
							//-- search for both EMAIL and _EMAIL... silly double gedcom standard
							if ($t=="EMAIL" || $t=="_EMAIL") $t="_?EMAIL";
							$searchstr .= $level." ".$t;
							$level++;
						}
					}
					else {
						if ($tag=="EMAIL" || $tag=="_EMAIL") $tag="_?EMAIL";
						$t = $tag;
						$searchstr = "1 ".$tag;
					}
					switch ($expr) {
						case "CONTAINS":
							if ($t=="PLAC") $searchstr.="[^\n]*[, ]".$val;
							else $searchstr.="[^\n]*".$val;
							$filters[] = $searchstr;
							break;
						default:
							if (!empty($val)) $filters2[] = array("tag"=>$tag, "expr"=>$expr, "val"=>$val);
							break;
					}
				}
			}
			$j++;
		}
	}
	switch($listname) {
		case "family":
			if (count($filters)>0) $list = search_fams($filters);
			else $list = get_fam_list();
			break;
		/*
		case "source":
			$list = get_source_list();
			break;
		case "other":
			$list = get_other_list();
			break; */
		case "pending":
			$list = array();
			foreach($pgv_changes as $cid=>$changes) {
				$change = end($changes);
				if ($change["gedcom"]==$GEDCOM) {
					$list[$change['gid']] = $change;
				}
			}
			break;
		default:
			if (count($filters)>0) $list = search_indis($filters);
			//-- handle date specific searches
			foreach($filters2 as $f=>$filter) {
				$tags = preg_split("/:/", $filter["tag"]);
				if (end($tags)=="DATE") {
					if ($filter['expr']=='LTE') {
						$enddate = new GedcomDate($filter['val']);
						$endtag = $tags[0];
					}
					if ($filter['expr']=='GTE') {
						$startdate = new GedcomDate($filter['val']);
						$starttag = $tags[0];
					}
				}
			}
			if (isset($startdate) && isset($enddate)) {
				$dlist = search_indis_daterange($startdate->MinJD(), $enddate->MaxJD(), $starttag.",".$endtag);
				if (!isset($list) || count($list)==0)
					$list = $dlist;
				else {
					//-- intersect the lists
					$newlist = array();
					foreach($list as $id=>$indi) {
						if (isset($dlist[$id])) $newlist[$id] = $indi;
					}
					$list = $newlist;
				}
			}
			if (!isset($list)) $list = get_indi_list();
			break;
	}
	//-- apply other filters to the list that could not be added to the search string
	if (count($filters2)>0) {
		$mylist = array();
		foreach($list as $key=>$value) {
			$keep = true;
			foreach($filters2 as $indexval => $filter) {
				if ($keep) {
					$tag = $filter["tag"];
					$expr = $filter["expr"];
					$val = $filter["val"];
					if ($val=="''") $val = "";
					$tags = preg_split("/:/", $tag);
					$t = end($tags);
					$v = get_gedcom_value($tag, 1, $value["gedcom"], '', false);
					//-- check for EMAIL and _EMAIL (silly double gedcom standard :P)
					if ($t=="EMAIL" && empty($v)) {
						$tag = preg_replace("/EMAIL/", "_EMAIL", $tag);
						$tags = preg_split("/:/", $tag);
						$t = end($tags);
						$v = get_sub_record(1, $tag, $value["gedcom"]);
					}
					
					
					$level = count($tags);
					switch ($expr) {
						case "GTE":
								if ($t=="DATE") {
									$date1 = new GedcomDate($v);
									$date2 = new GedcomDate($val);
									$keep = (GedcomDate::Compare($date1, $date2)>0);
								}
								else if ($val >= $v) $keep=true;
							break;
						case "LTE":
								if ($t=="DATE") {
									$date1 = new GedcomDate($v);
									$date2 = new GedcomDate($val);
									$keep = (GedcomDate::Compare($date1, $date2)<0);
								}
								else if ($val >= $v) $keep=true;
							break;
						case "SUBCONTAINS":
							$v = get_sub_record($level, $level." ".$tag, $value["gedcom"]);
							if (empty($v) && $tag=="ADDR") $v = get_sub_record($level+1, ($level+1)." ".$tag, $value["gedcom"]);
							$ct = preg_match("/$val\b/i", $v);
							if ($ct>0) $keep = true;
							else $keep = false;
							break;
						default:
							if ($v==$val) $keep=true;
							else $keep = false;
							break;
					}
				}
			}
			if ($keep) $mylist[$key]=$value;
		}
		$list = $mylist;
	}
	if ($sortby=="NAME") uasort($list, "itemsort");
	else if ($sortby=="ID") uasort($list, "idsort");
	else if ($sortby=="CHAN") uasort($list, "compare_date_descending");
	else uasort($list, "compare_date");
	//print count($list);
	array_push($repeatsStack, array($repeats, $repeatBytes));
	$repeatBytes = xml_get_current_line_number($parser)+1;
}

function PGVRListEHandler() {
	global $currentElement, $list, $repeats, $repeatsStack, $repeatBytes, $parser, $parserStack, $report, $pgvreport, $gedrec, $processRepeats, $list_total, $list_private;
	$processRepeats--;
	if ($processRepeats>0) return;

	//-- reset any text that may have been added parsing to the end of the loop
	$currentElement->setText("");
	
	$line = xml_get_current_line_number($parser)-1;
	$lineoffset = 0;
	for($i=0; $i<count($repeatsStack); $i++) {
		$p = $repeatsStack[$i];
		$l = $p[1];
		$lineoffset += $l;
	}
	//-- read the xml from the file
	$lines = file($report);
	$reportxml = "<tempdoc>\n";
	while(strstr($lines[$lineoffset+$repeatBytes], "<PGVRList")===false && $lineoffset+$repeatBytes>0) $lineoffset--;
	$lineoffset++;
	$line1 = $repeatBytes;
	$ct = 1;
	while(($ct>0)&&($line1<$line+2)) {
		if (strstr($lines[$lineoffset+$line1], "<PGVRList")!==false) $ct++;
		if (strstr($lines[$lineoffset+$line1], "</PGVRList")!==false) $ct--;
		$line1++;
	}
	$line = $line1-1;
	for($i=$repeatBytes+$lineoffset; $i<$line+$lineoffset; $i++) $reportxml .= $lines[$i];
	$reportxml .= "</tempdoc>\n";
	//print htmlentities($reportxml)."\n";
	array_push($parserStack, $parser);

	$oldgedrec = $gedrec;
	$list_total = count($list);
	$list_private = 0;
	foreach($list as $key=>$value) {
		if (displayDetailsById($key)) {
			if (isset($value["undo"])) $gedrec = $value["undo"];
			else $gedrec = find_gedcom_record($key);
			//-- start the sax parser
			$repeat_parser = xml_parser_create();
			$parser = $repeat_parser;
			//-- make sure everything is case sensitive
			xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
			//-- set the main element handler functions
			xml_set_element_handler($repeat_parser, "startElement", "endElement");
			//-- set the character data handler
			xml_set_character_data_handler($repeat_parser, "characterData");

			if (!xml_parse($repeat_parser, $reportxml, true)) {
				printf($reportxml."\nPGVRRepeatTagEHandler XML error: %s at line %d", xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser));
				print_r($repeatsStack);
				debug_print_backtrace();
				exit;
			}
			xml_parser_free($repeat_parser);
		}
		else $list_private++;
	}
	$parser = array_pop($parserStack);

	$gedrec = $oldgedrec;
	$temp = array_pop($repeatsStack);
	$repeats = $temp[0];
	$repeatBytes = $temp[1];
}

function PGVRListTotalSHandler($attrs) {
	global $list_total, $list_private, $currentElement;

	if (empty($list_total)) $list_total = 0;

	if ($list_private==0) {
		$currentElement->addText($list_total);
	} else {
		$currentElement->addText(($list_total - $list_private)." / ".$list_total);
	} 
}

function PGVRRelativesSHandler($attrs) {
	global $pgvreport, $gedrec, $repeats, $repeatBytes, $list, $repeatsStack, $processRepeats, $parser, $vars, $sortby, $indilist;

	$processRepeats++;
	if ($processRepeats>1) return;

	$sortby = "NAME";
	if (isset($attrs["sortby"])) $sortby = $attrs["sortby"];
	if (preg_match("/\\$(\w+)/", $sortby, $vmatch)>0) {
		$sortby = $vars[$vmatch[1]]["id"];
		$sortby = trim($sortby);
	}

	$maxgen = -1;
	if (isset($attrs["maxgen"])) $maxgen = $attrs["maxgen"];
	if ($maxgen=="*") $maxgen = -1;

	$group = "child-family";
	if (isset($attrs["group"])) $group = $attrs["group"];
	if (preg_match("/\\$(\w+)/", $group, $vmatch)>0) {
		$group = $vars[$vmatch[1]]["id"];
		$group = trim($group);
	}

	$id = "";
	if (isset($attrs["id"])) $id = $attrs["id"];
	if (preg_match("/\\$(\w+)/", $id, $vmatch)>0) {
		$id = $vars[$vmatch[1]]["id"];
		$id = trim($id);
	}
	
	$showempty = false;
	if (isset($attrs["showempty"])) $showempty = $attrs["showempty"];
	if (preg_match("/\\$(\w+)/", $showempty, $vmatch)>0) {
		$showempty = $vars[$vmatch[1]]["id"];
		$showempty = trim($showempty);
	}
	

	$list = array();
	$indirec = find_person_record($id);
	if (!empty($indirec)) {
		$list[$id] = $indilist[$id];
		switch ($group) {
			case "child-family":
				$famids = find_family_ids($id);
				foreach($famids as $indexval => $famid) {
					$parents = find_parents($famid);
					if (!empty($parents["HUSB"])) {
						$temp = find_person_record($parents["HUSB"]);
						if (!empty($temp)) $list[$parents["HUSB"]] = $indilist[$parents["HUSB"]];
					}
					if (!empty($parents["WIFE"])) {
						$temp = find_person_record($parents["WIFE"]);
						if (!empty($temp)) $list[$parents["WIFE"]] = $indilist[$parents["WIFE"]];
					}
					$famrec = find_family_record($famid);
					$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
					for($i=0; $i<$num; $i++) {
						$temp = find_person_record($smatch[$i][1]);
						if (!empty($temp)) $list[$smatch[$i][1]] = $indilist[$smatch[$i][1]];
					}
				}
				break;
			case "spouse-family":
				$famids = find_sfamily_ids($id);
				foreach($famids as $indexval => $famid) {
					$parents = find_parents($famid);
					$temp = find_person_record($parents["HUSB"]);
					if (!empty($temp)) $list[$parents["HUSB"]] = $indilist[$parents["HUSB"]];
					$temp = find_person_record($parents["WIFE"]);
					if (!empty($temp)) $list[$parents["WIFE"]] = $indilist[$parents["WIFE"]];
					$famrec = find_family_record($famid);
					$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
					for($i=0; $i<$num; $i++) {
						$temp = find_person_record($smatch[$i][1]);
						if (!empty($temp)) $list[$smatch[$i][1]] = $indilist[$smatch[$i][1]];
					}
				}
				break;
			case "direct-ancestors":
				add_ancestors($id,false,$maxgen, $showempty);
				break;
			case "ancestors":
				add_ancestors($id,true,$maxgen,$showempty);
				break;
			case "descendants":
				$list[$id]["generation"] = 1;
				add_descendancy($id,false,$maxgen);
				break;
			case "all":
				add_ancestors($id,true,$maxgen,$showempty);
				add_descendancy($id,true,$maxgen);
				break;
		}
	}

	if ($sortby!="none") {
		if ($sortby=="NAME") uasort($list, "itemsort");
		else if ($sortby=="ID") uasort($list, "idsort");
		else if ($sortby=="generation") {
			$newarray = array();
			reset($list);
			$genCounter = 1;
			while (count($newarray) < count($list)) {
		        	foreach ($list as $key => $value) {
			                $generation = $value["generation"];
			                if ($generation == $genCounter) {
						$newarray[$key]["generation"]=$generation;
					}
				}
				$genCounter++;
			}
			$list = $newarray;
		}
		else uasort($list, "compare_date");
	}
//	print count($list);
	array_push($repeatsStack, array($repeats, $repeatBytes));
	$repeatBytes = xml_get_current_line_number($parser)+1;
}

function PGVRRelativesEHandler() {
	global $list, $repeats, $repeatsStack, $repeatBytes, $parser, $parserStack, $report, $pgvreport, $gedrec, $processRepeats, $list_total, $list_private, $generation;
	$processRepeats--;
	if ($processRepeats>0) return;

	$line = xml_get_current_line_number($parser)-1;
	$lineoffset = 0;
	for($i=0; $i<count($repeatsStack); $i++) {
		$p = $repeatsStack[$i];
		$l = $p[1];
		$lineoffset += $l;
	}
	//-- read the xml from the file
	$lines = file($report);
	$reportxml = "<tempdoc>\n";
	while(strstr($lines[$lineoffset+$repeatBytes], "<PGVRRelatives")===false && $lineoffset+$repeatBytes>0) $lineoffset--;
	$lineoffset++;
	$line1 = $repeatBytes;
	$ct = 1;
	while(($ct>0)&&($line1<$line+2)) {
		if (strstr($lines[$lineoffset+$line1], "<PGVRRelatives")!==false) $ct++;
		if (strstr($lines[$lineoffset+$line1], "</PGVRRelatives")!==false) $ct--;
		$line1++;
	}
	$line = $line1-1;
	for($i=$repeatBytes+$lineoffset; $i<$line+$lineoffset; $i++) $reportxml .= $lines[$i];
	$reportxml .= "</tempdoc>\n";
//	print htmlentities($reportxml)."\n";
	array_push($parserStack, $parser);

	$oldgedrec = $gedrec;
	$list_total = count($list);
	$list_private = 0;
	foreach($list as $key=>$value) {
		if (isset($value["generation"])) $generation = $value["generation"];
//KN		if (displayDetailsById($key)) {
			$gedrec = find_gedcom_record($key);
			//-- start the sax parser
			$repeat_parser = xml_parser_create();
			$parser = $repeat_parser;
			//-- make sure everything is case sensitive
			xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
			//-- set the main element handler functions
			xml_set_element_handler($repeat_parser, "startElement", "endElement");
			//-- set the character data handler
			xml_set_character_data_handler($repeat_parser, "characterData");

			if (!xml_parse($repeat_parser, $reportxml, true)) {
				printf($reportxml."\nPGVRRelativesEHandler XML error: %s at line %d", xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser));
				print_r($repeatsStack);
				debug_print_backtrace();
				exit;
			}
			xml_parser_free($repeat_parser);
//KN		}
//KN		else $list_private++;
	}
	$parser = array_pop($parserStack);

	$gedrec = $oldgedrec;
	$temp = array_pop($repeatsStack);
	$repeats = $temp[0];
	$repeatBytes = $temp[1];
}

function PGVRGenerationSHandler($attrs) {
	global $list_total, $list_private, $generation, $currentElement;

	if (empty($generation)) $generation = 1;

	$currentElement->addText($generation);
}

function PGVRNewPageSHandler($attrs) {
	global $pgvreport;
	$temp = "addpage";
	$pgvreport->addElement($temp);
}

function HTMLSHandler($tag, $attrs) {
	global $printData, $printDataStack, $pgvreportStack;
	global $pgvreport, $currentElement, $PGVReportRoot;

	if ($tag=="tempdoc") return;
	
	array_push($pgvreportStack, $pgvreport);
	$pgvreport = $PGVReportRoot->createHTML($tag, $attrs);
	$currentElement = $pgvreport;
	
	array_push($printDataStack, $printData);
	$printData = true;
//	print "[".$tag."] ";
}

function HTMLEHandler($tag) {
	global $printData, $printDataStack;
	global $pgvreport, $currentElement, $pgvreportStack;

	if ($tag=="tempdoc") return;
	
	$printData = array_pop($printDataStack);
	$currentElement = $pgvreport;
	$pgvreport = array_pop($pgvreportStack);
//	print "{".get_class($pgvreport)."} ";
	if (!is_null($pgvreport)) $pgvreport->addElement($currentElement);
	else $pgvreport = $currentElement;
}

function PGVRTitleSHandler($attrs) {
	global $reportTitle, $printData, $printDataStack;
	
	$reportTitle = true;
}

function PGVRTitleEHandler() {
	global $reportTitle, $printData, $printDataStack;
	
	$reportTitle = false;
	
}
?>

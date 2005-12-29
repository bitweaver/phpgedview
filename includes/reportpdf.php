<?php
/**
 * PDF Report Generator
 *
 * used by the SAX parser to generate PDF reports from the XML report file.
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
 * @version $Id: reportpdf.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */

//-- do not allow direct access to this file
if (strstr($_SERVER["SCRIPT_NAME"],"reportpdf.php")) {
	print "Why do you want to do that?";
	exit;
}

define('FPDF_FONTPATH','fonts/');

$ascii_langs = array("english", "danish", "dutch", "french", "german", "norwegian", "spanish", "spanish-ar");

//-- setup special characters array to force embedded fonts
$SpecialOrds = $RTLOrd;
for($i=195; $i<215; $i++) $SpecialOrds[] = $i;

if (!isset($embed_fonts)) {
	if (in_array($LANGUAGE, $ascii_langs)) $embed_fonts = false;
	else $embed_fonts = true;
}
//print "embed = $embed_fonts";
/**
 * load the FPDF class
 *
 * the FPDF class allows you to create PDF documents in PHP
 */
require "ufpdf/ufpdf.php";

/**
 * main PGV Report Class
 * @package PhpGedView
 * @subpackage Reports
 */
class PGVReport {
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
	var $pdf;
	var $processing;

	function setup($pw, $ph, $o, $m) {
		global $pgv_lang, $VERSION;

		$this->pagew = $pw;
		$this->pageh = $ph;
		$this->orientation = $o;
		$this->margin = $m;
		$this->pdf = new PGVRPDF('P', 'pt', array($pw*72,$ph*72));
		$this->pdf->setMargins($m, $m);
		$this->pdf->SetCompression(true);
		$this->pdf->setReport($this);
		$this->processing = "H";
		$element = new PGVRCell(0,10, "C", "");
		$element->addText("$pgv_lang[generated_by] PhpGedView $VERSION");
		$element->setUrl("http://www.phpgedview.net/");
		$this->pdf->addFooter($element);
	}

	function setProcessing($p) {
		$this->processing = $p;
	}

	function addElement(&$element) {
		if ($this->processing=="H") return $this->pdf->addHeader($element);
		if ($this->processing=="PH") return $this->pdf->addPageHeader($element);
		if ($this->processing=="F") return $this->pdf->addFooter($element);
		if ($this->processing=="B") return $this->pdf->addBody($element);
	}

	function addStyle($style) {
		$this->PGVRStyles[$style["name"]] = $style;
	}

	function getStyle($s) {
		if (!isset($this->PGVRStyles[$s])) $s = $this->pdf->getCurrentStyle();
		return $this->PGVRStyles[$s];
	}

	function run() {
		global $download, $embed_fonts;

		$this->pdf->SetEmbedFonts($embed_fonts);
		if ($embed_fonts) $this->pdf->AddFont('LucidaSansUnicode', '', 'LucidaSansRegular.php');
		$this->pdf->AliasNbPages();
		$this->pdf->Body();
		header("Expires:");
		header("Pragma:");
		header("Cache-control:");
//		if (!isset($download)) $this->pdf->Output();
		if ($download=="") $this->pdf->Output();
		else $this->pdf->Output("pgv_report_".basename($_REQUEST["report"], ".xml").".pdf", "D");
	}

	function getMaxWidth() {
		$w = (($this->pagew * 72) - ($this->margin+10)) - $this->pdf->GetX();
		return $w;
	}

	function getPageHeight() {
		return ($this->pageh*72)-72;
	}

	function clearPageHeader() {
		$this->pdf->clearPageHeader();
	}
} //-- end PGVReport

/**
 * PGV Report PDF Class
 *
 * This class inherits from the FPDF class and is used to generate the PDF document
 * @package PhpGedView
 * @subpackage Reports
 */
class PGVRPDF extends UFPDF {
	/**
	 * array of elements in the header
	 */
	var $headerElements = array();
	/**
	 * array of elements in the header
	 */
	var $pageHeaderElements = array();
	/**
	 * array of elements in the footer
	 */
	var $footerElements = array();
	/**
	 * array of elements in the body
	 */
	var $bodyElements = array();
	var $printedfootnotes = array();

	var $pgvreport;
	var $currentStyle;

	function Header() {
		if (!isset($this->currentStyle)) $this->currentStyle = "";
		$temp = $this->currentStyle;
		foreach($this->headerElements as $indexval => $element) {
			if ($element=="footnotetexts") $this->Footnotes();
			else $element->render($this);
		}
		foreach($this->pageHeaderElements as $indexval => $element) {
			if ($element=="footnotetexts") $this->Footnotes();
			else $element->render($this);
		}
		$this->currentStyle = $temp;
	}

	function Footer() {
		$this->SetY(-36);
		$this->currentStyle = "";
		foreach($this->footerElements as $indexval => $element) {
			if ($element=="footnotetexts") $this->Footnotes();
			else $element->render($this);
		}
	}

	function Body() {
		global $TEXT_DIRECTION;
		$this->AddPage();
		$this->currentStyle = "";
		foreach($this->bodyElements as $indexval => $element) {
			if ($element=="footnotetexts") $this->Footnotes();
			else $element->render($this);
		}
	}

	function Footnotes() {
		$this->currentStyle = "";
		foreach($this->printedfootnotes as $indexval => $element) {
			$element->renderFootnote($this);
		}
	}

	function getFootnotesHeight() {
		$h=0;
		foreach($this->printedfootnotes as $indexval => $element) {
			$h+=$element->getHeight($this);
		}
		return $h;
	}

	function addHeader(&$element) {
		$this->headerElements[] = $element;
		return count($this->headerElements)-1;
	}

	function addPageHeader(&$element) {
		$this->pageHeaderElements[] = $element;
		return count($this->headerElements)-1;
	}

	function addFooter(&$element) {
		$this->footerElements[] = $element;
		return count($this->footerElements)-1;
	}

	function addBody(&$element) {
		$this->bodyElements[] = $element;
		return count($this->bodyElements)-1;
	}

	function removeHeader($index) {
		unset($this->headerElements[$index]);
	}

	function removePageHeader($index) {
		unset($this->pageHeaderElements[$index]);
	}

	function removeFooter($index) {
		unset($this->footerElements[$index]);
	}

	function removeBody($index) {
		unset($this->bodyElements[$index]);
	}

	function clearPageHeader() {
		$this->pageHeaderElements = array();
	}

	function setReport(&$r) {
		$this->pgvreport = $r;
	}

	function getCurrentStyle() {
		return $this->currentStyle;
	}

	function setCurrentStyle($s) {
		$this->currentStyle = $s;
		$style = $this->pgvreport->getStyle($s);
		$this->SetFont($style["font"], $style["style"], $style["size"]);
	}

	function getStyle($s) {
		$style = $this->pgvreport->getStyle($s);
		return $style;
	}

	function getMaxWidth() {
		return $this->pgvreport->getMaxWidth();
	}

	function getCurrentStyleHeight() {
		if (empty($this->currentStyle)) return 12;
		$style = $this->pgvreport->getStyle($this->currentStyle);
		return $style["size"];
	}

	function checkFootnote(&$footnote) {
		for($i=0; $i<count($this->printedfootnotes); $i++) {
			if ($this->printedfootnotes[$i]->getValue() == $footnote->getValue()) {
				return $this->printedfootnotes[$i];
			}
		}
		$footnote->setNum(count($this->printedfootnotes)+1);
		$link = $this->AddLink();
		$footnote->setAddlink($link);
		$this->printedfootnotes[] = $footnote;
		return false;
	}

	function getPageHeight() {
		return $this->pgvreport->getPageHeight();
	}
} //-- END PGVRPDF

/**
 * main PGV Report Element class that all other page elements are extended from
 */
class PGVRElement {
	var $text;

	function render(&$pdf) {
		print "Nothing rendered.  Something bad happened";
		//-- to be implemented in inherited classes
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
		$t = preg_replace("/<br \/>/", "\n", $t);
		$t = strip_tags($t);
		$t = unhtmlentities($t);
		if ($embed_fonts) $t = bidi_text($t);
		else $t = smart_utf8_decode($t);
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

	function getHeight(&$pdf) {
		return 0;
	}

	function getWidth(&$pdf) {
		return 0;
	}

	function setWrapWidth($width) {
		return;
	}

	function renderFootnote(&$pdf) {
		return false;
		//-- to be implemented in inherited classes
	}

	function get_type() {
		return "PGVRElement";
	}
} //-- END PGVRElement

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

	function render(&$pdf) {
		global $TEXT_DIRECTION, $embed_fonts;
		/* -- commenting out because it causes too many problems
		if ($TEXT_DIRECTION=='rtl') {
			if ($this->align=='L') $this->align='R';
			else if ($this->align=='R') $this->align='L';
		}*/
		if ($pdf->getCurrentStyle()!=$this->styleName)
			$pdf->setCurrentStyle($this->styleName);
		$temptext = preg_replace("/#PAGENUM#/", $pdf->PageNo(), $this->text);
		$curx = $pdf->GetX();
		$cury = $pdf->GetY();
		if (($this->top!=".")||($this->left!=".")) {
			if ($this->top==".") $this->top = $cury;
			if ($this->left==".") $this->left = $cury;
			$pdf->SetXY($this->left, $this->top);
		}
		$pdf->MultiCell($this->width,$this->height,$temptext,0,$this->align);
		if (!empty($url)) {
			$pdf->Link($curx, $cury, $this->width, $this->height, $url);
		}
	}

	function getHeight(&$pdf) {
		return $this->height;
	}

	function setUrl($url) {
		$this->url = $url;
	}

	function getWidth(&$pdf) {
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

	function get_type() {
		return "PGVRTextBox";
	}

	function PGVRTextBox($width, $height, $border, $fill, $newline, $left=".", $top=".") {
		$this->width = $width;
		$this->height = $height;
		$this->border = $border;
		$this->fill = $fill;
		$this->newline = $newline;
		if ($border>0) $this->style = "D";
		else $this->style = "";
		$this->top = $top;
		$this->left = $left;
	}

	function render(&$pdf) {
		global $lastheight;

		if (!empty($lastheight)) {
			if ($this->height < $lastheight) $this->height = $lastheight;
		}

		$startX = $pdf->GetX();
		$startY = $pdf->GetY();
		if (!empty($this->fill)) {
			$ct = preg_match("/#?(..)(..)(..)/", $this->fill, $match);
			if ($ct>0) {
				$this->style .= "F";
				$r = hexdec($match[1]);
				$g = hexdec($match[2]);
				$b = hexdec($match[3]);
				$pdf->SetFillColor($r, $g, $b);
			}
		}
		if ($this->width==0) {
			$this->width = $pdf->getMaxWidth();
		}

		$newelements = array();
		$lastelement = "";
		//-- collapse duplicate elements
		for($i=0; $i<count($this->elements); $i++) {
			$element = $this->elements[$i];
			if ($element!="footnotetexts") {
				if ($element->get_type()=="PGVRText") {
					if ($lastelement == "") $lastelement = $element;
					else {
						if ($element->getStyleName()==$lastelement->getStyleName()) {
							$lastelement->addText(preg_replace("/\n/", "<br />", $element->getValue()));
						}
						else {
							if ($lastelement != "") {
								$newelements[] = $lastelement;
								$lastelement = $element;
							}
						}
					}
				}
				//-- do not keep empty footnotes
				else if (($element->get_type()!="PGVRFootnote")||(trim($element->getValue())!="")) {
					if ($lastelement != "") {
						$newelements[] = $lastelement;
						$lastelement = "";
					}
					$newelements[] = $element;
				}
			}
			else {
				if ($lastelement != "") {
					$newelements[] = $lastelement;
					$lastelement = "";
				}
				$newelements[] = $element;
			}
		}
		if ($lastelement!="") $newelements[] = $lastelement;
		$this->elements = $newelements;

		//-- calculate the text box height
		$h = 0;
		$w = 0;
		for($i=0; $i<count($this->elements); $i++) {
			if ($this->elements[$i]!="footnotetexts") {
				$ew = $this->elements[$i]->setWrapWidth($this->width-$w, $this->width);
				if ($ew==$this->width) $w=0;
				//-- $lw is an array 0=>last line width, 1=1 if text was wrapped, 0 if text did not wrap
				$lw = $this->elements[$i]->getWidth($pdf);
				if ($lw[1]==1) $w = $lw[0];
				else if ($lw[1]==2) $w=0;
				else $w += $lw[0];
				if ($w>$this->width) $w = $lw[0];
				$eh = $this->elements[$i]->getHeight($pdf);
				//if ($eh>$h) $h = $eh;
				//else if ($lw[1]) $h+=$eh;
				$h+=$eh;
			}
			else {
				$h += $pdf->getFootnotesHeight();
			}
		}
		if ($h>$this->height) $this->height=$h;
		//if (($this->width>0)&&($this->width<$w)) $this->width=$w;

		$curx = $pdf->GetX();
		$cury = $pdf->GetY();
		$curn = $pdf->PageNo();
		if (($this->top!=".")||($this->left!=".")) {
			if ($this->top==".") $this->top = $cury;
			if ($this->left==".") $this->left = $curx;
			$pdf->SetXY($this->left, $this->top);
			$startY = $this->top;
			$startX = $this->left;
			$cury = $startY;
			$curx = $startX;
		}

		$newpage = false;
		$ph = $pdf->getPageHeight();
		if ($pdf->GetY()+$this->height > $ph) {
			if ($this->border==1) {
				//print "HERE2";
				$pdf->AddPage();
				$newpage = true;
				$startX = $pdf->GetX();
				$startY = $pdf->GetY();
			}
			else if ($pdf->GetY()>$ph-36) {
				//print "HERE1";
				$pdf->AddPage();
				$startX = $pdf->GetX();
				$startY = $pdf->GetY();
			}
			else {
				//print "HERE3";
				$th = $this->height;
				$this->height = ($ph - $pdf->GetY())+36;
				$newpage = true;
			}
		}

		if (!empty($this->style)) $pdf->Rect($pdf->GetX(), $pdf->GetY(), $this->width, $this->height, $this->style);
		$pdf->SetXY($pdf->GetX(), $pdf->GetY()+1);
		$curx = $pdf->GetX();
		foreach($this->elements as $indexval => $element) {
			if ($element=="footnotetexts") $pdf->Footnotes();
			else $element->render($pdf, $curx);
		}
		if ($curn != $pdf->PageNo()) $cury = $pdf->GetY();
		if ($this->newline) {
			$lastheight = 0;
			$ty = $pdf->GetY();
			if ($curn != $pdf->PageNo()) $ny = $cury+$pdf->getCurrentStyleHeight();
			else $ny = $cury+$this->height;
			if ($ty > $ny) $ny = $ty;
			$pdf->SetY($ny);
			//Here1 ty:71 ny:185 cury:169
			//print "Here1 ty:$ty ny:$ny cury:$cury ";
		}
		else {
			//print "Here2 ";
			$ty = $pdf->GetY()-1;
			if (($ty > $startY) && ($ty < $startY + $this->height)) $ty = $startY;
			$pdf->SetXY($curx+$this->width, $ty);
			$lastheight = $this->height;
		}
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

	function PGVRText($style) {
		$this->text = "";
		$this->wrapWidth = 0;
		$this->styleName = $style;
	}

	function render(&$pdf, $curx=0) {
		global $embed_fonts;
		$pdf->setCurrentStyle($this->styleName);
		$temptext = preg_replace("/#PAGENUM#/", $pdf->PageNo(), $this->text);
		//print $this->text;
		$x = $pdf->GetX();
		$cury = $pdf->GetY();
		$lines = preg_split("/\n/", $temptext);
		$styleh = $pdf->getCurrentStyleHeight();
		if (count($lines)>0) {
			foreach($lines as $indexval => $line) {
				$pdf->SetXY($x, $cury);
				//print "[$x $cury $line]";
				$pdf->Write($styleh,$line);
				$cury+=$styleh+1;
				if ($cury>$pdf->getPageHeight()) $cury = $pdf->getY()+$styleh+1;
				$x = $curx;
			}
		}
		else $pdf->Write($pdf->getCurrentStyleHeight(),$temptext);
		$ct = preg_match_all("/".chr(215)."/", $temptext, $match);
		if ($ct>1) {
			$x = $pdf->GetX();
			$x = $x - pow(1.355, $ct);
			$pdf->SetX($x);
		}
	}

	function getHeight(&$pdf) {
		$ct = substr_count($this->text, "\n");
		if ($ct>0) $ct+=1;
		$style = $pdf->getStyle($this->styleName);
		$h = (($style["size"]+1)*$ct);
		//print "[".$this->text." $ct $h]";
		return $h;
	}

	function getWidth(&$pdf) {
		$pdf->setCurrentStyle($this->styleName);
		if (!isset($this->text)) $this->text = "";
		$lw = $pdf->GetStringWidth($this->text);
		if ($this->wrapWidth > 0) {
			if ($lw > $this->wrapWidth) {
				$lines = preg_split("/\n/", $this->text);
				$newtext = "";
				$wrapwidth = $this->wrapWidth;
				foreach($lines as $indexval => $line) {
					$w = $pdf->GetStringWidth($line)+10;
					if ($w>$wrapwidth) {
						$words = preg_split("/\s/", $line);
						$lw = 0;
						foreach($words as $indexval => $word) {
							$lw += $pdf->GetStringWidth($word." ");
							if ($lw <= $wrapwidth) $newtext.=$word." ";
							else {
								//print "NEWLNE $word\n";
								$lw = $pdf->GetStringWidth($word." ");
								$newtext .= "\n$word ";
								$wrapwidth = $this->wrapWidth2;
							}
						}
						$newtext .= "\n";
					}
					else $newtext .= $line."\n";
				}
				$this->text = $newtext;
				//$this->text = preg_replace("/\n/", "\n~", $this->text);
				//print $this->wrapWidth." $lw [".$this->text."]1 ";
				return array($lw, 1);
			}
		}
		$l = 0;
		if (preg_match("/\n$/", $this->text)>0) $l=2;
		//print $this->wrapWidth." $lw [".$this->text."]$l ";
		return array($lw, $l);
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

	function render(&$pdf) {
		global $footnote_count, $embed_fonts;

		$fn = $pdf->checkFootnote($this);
		if ($fn===false) {
			$pdf->setCurrentStyle("footnotenum");
			$pdf->Write($pdf->getCurrentStyleHeight(),$this->num." ", $this->addlink);
		}
		else {
			$fn->rerender($pdf);
		}
	}

	function rerender(&$pdf) {
		global $footnote_count;
		if (empty($this->num)) {
			if (empty($footnote_count)) $footnote_count = 1;
			else $footnote_count++;

			$this->num = $footnote_count;
		}
		$pdf->setCurrentStyle("footnotenum");
		$pdf->Write($pdf->getCurrentStyleHeight(),$this->num." ", $this->addlink);
	}

	function renderFootnote(&$pdf) {
		global $embed_fonts;
		if ($pdf->getCurrentStyle()!=$this->styleName)
			$pdf->setCurrentStyle($this->styleName);
		$temptext = preg_replace("/#PAGENUM#/", $pdf->PageNo(), $this->text);

		$pdf->SetLink($this->addlink, -1);
		$pdf->Write($pdf->getCurrentStyleHeight(),$this->num.". ".$temptext."\n\n");
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

	function render(&$pdf) {
		$pdf->clearPageHeader();
		foreach($this->elements as $indexval => $element) {
			$pdf->addPageHeader($element);
		}
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

	function render(&$pdf) {
		global $lastpicbottom, $lastpicpage, $lastpicleft, $lastpicright;;
		if ($this->x==0) $this->x=$pdf->GetX();
		if ($this->y==0) {
			//-- first check for a collision with the last picture
			if (isset($lastpicbottom)) {
				if (($pdf->PageNo()==$lastpicpage)&&($lastpicbottom >= $pdf->GetY())&&($this->x>=$lastpicleft)&&($this->x<=$lastpicright))
					$pdf->SetY($lastpicbottom+5);
			}
			$this->y=$pdf->GetY();
		}
		$pdf->Image($this->file, $this->x, $this->y, $this->width, $this->height);
		$lastpicbottom = $this->y + $this->height;
		$lastpicpage = $pdf->PageNo();
		$lastpicleft=$this->x;
		$lastpicright=$this->x+$this->width;
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

	function render(&$pdf) {
		if ($this->x1==".") $this->x1=$pdf->GetX();
		if ($this->y1==".") $this->y1=$pdf->GetY();
		if ($this->x2==".") $this->x2=$pdf->GetX();
		if ($this->y2==".") $this->y2=$pdf->GetY();
		$pdf->Line($this->x1, $this->y1, $this->x2, $this->y2);
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
$elementHandler["PGVRGeneration"]["start"]       = "PGVRGenerationSHandler";

$pgvreport = new PGVReport();
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
 * page sizes
 *
 * an array map of common page sizes
 * @global array $pageSizes
 */
$pageSizes["A4"]["width"] = "8.5";
$pageSizes["A4"]["height"] = "11";

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
//		$ct = preg_match("/^\\$([a-zA-Z0-9\-_]+)$/", $value, $match);
		$ct = preg_match("/^\\$(\w+)$/", $value, $match);
		if ($ct>0) {
			if ((isset($vars[$match[1]]["id"]))&&(!isset($vars[$match[1]]["gedcom"]))) $value = $vars[$match[1]]["id"];
			//print "$match[0]=$value\n";
		}
		$newattrs[$key] = $value;
	}
	$attrs = $newattrs;
	if (($processIfs==0 || $name=="PGVRif")&&($processGedcoms==0 || $name=="PGVRGedcom")&&($processRepeats==0 || $name=="PGVRFacts" || $name=="PGVRRepeatTag")) {
		if (isset($elementHandler[$name]["start"])) call_user_func($elementHandler[$name]["start"], $attrs);
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
		if (isset($elementHandler[$name]["end"])) call_user_func($elementHandler[$name]["end"]);
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
	global $printData, $currentElement, $processGedcoms, $processIfs;
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

	if (!isset($pageSizes[$pageSize])) $pageSize="A4";
	$pagew = $pageSizes[$pageSize]["width"];
	$pageh = $pageSizes[$pageSize]["height"];

	if ($orientation=="L") {
		$pagew = $pageSizes[$pageSize]["height"];
		$pageh = $pageSizes[$pageSize]["width"];
	}

	$margin = "";
	$margin = $attrs["margin"];

	$pgvreport->setup($pagew, $pageh, $orientation, $margin);
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
	global $printDataStack, $printData, $pgvreportStack, $pgvreport;

	array_push($printDataStack, $printData);
	$printData = false;

	array_push($pgvreportStack, $pgvreport);
	$pgvreport = new PGVRPageHeader();
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
	global $printData, $printDataStack, $currentElement;

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

	$currentElement = new PGVRCell($width, $height, $align, $style);
}

function PGVRCellEHandler() {
	global $printData, $printDataStack, $currentElement, $pgvreport;

	$printData = array_pop($printDataStack);
	$pgvreport->addElement($currentElement);
}

function PGVRNowSHandler($attrs) {
	global $currentElement;

	$currentElement->addText(get_changed_date(date("j", time()-(isset($_SESSION["timediff"])?$_SESSION["timediff"]:0))." ".date("M", time()-(isset($_SESSION["timediff"])?$_SESSION["timediff"]:0))." ".date("Y", time()-(isset($_SESSION["timediff"])?$_SESSION["timediff"]:0))));
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
		$newgedrec = privatize_gedcom($newgedrec);
		array_push($gedrecStack, array($gedrec, $fact, $desc));
		//print "[$newgedrec]";
		$gedrec = $newgedrec;
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
	global $pgvreport, $currentElement, $pgvreportStack;

	$width = 0;
	$height= 0;
	$border= 0;
	$newline = 0;
	$fill = "";
	$style = "D";
	$left = ".";
	$top = ".";

	if (isset($attrs["width"])) $width = $attrs["width"];
	if (isset($attrs["height"])) $height = $attrs["height"];
	if (isset($attrs["border"])) $border = $attrs["border"];
	if (isset($attrs["newline"])) $newline = $attrs["newline"];
	if (isset($attrs["fill"])) $fill = $attrs["fill"];
	if (isset($attrs["left"])) $left = $attrs["left"];
	if (isset($attrs["top"])) $top = $attrs["top"];

	array_push($printDataStack, $printData);
	$printData = false;

	array_push($pgvreportStack, $pgvreport);
	$pgvreport = new PGVRTextBox($width, $height, $border, $fill, $newline, $left, $top);
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
	global $pgvreport, $currentElement;

	array_push($printDataStack, $printData);
	$printData = true;

	$style = "";

	if (isset($attrs["style"])) {
		$style = $attrs["style"];
	}
	$currentElement = new PGVRText($style);
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
		if (!displayDetailsById($id) && !showLivingNameByID($id)) $currentElement->addText($pgv_lang["private"]);
		else {
			$name = trim(get_person_name($id));
			$addname = trim(get_add_person_name($id));
			if (!empty($addname)) $name .= " ".$addname;
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

	$repeats = get_all_subrecords($gedrec, $tag, $families);
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
	if ($lineoffset>0) $lineoffset--;
	for($i=$repeatBytes+$lineoffset; $i<$line+$lineoffset; $i++) $reportxml .= $lines[$i];
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

	$ct = preg_match_all("/\\$(\w+)/", $value, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		//print $match[$i][1];
		$t = $vars[$match[$i][1]]["id"];
		$value = preg_replace("/\\$".$match[$i][1]."/", $t, $value);
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
	//print "[$value]";
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
	while (preg_match("/@([\w:]+)/", $condition, $match) > 0) {
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
		else if ($id=="generation") {
			$value = "'$generation'";
		}
		else {
			$temp = preg_split("/\s+/", trim($gedrec));
			$level = $temp[0];
			if ($level==0) $level++;
			$value = get_gedcom_value($id, $level, $gedrec);
			//print "level:$level id:$id value:$value ";
			if (empty($value)) {
				$level++;
				$value = get_gedcom_value($id, $level, $gedrec);
				//print "level:$level id:$id value:$value gedrec:$gedrec<br />\n";
			}
			$value = "'".$value."'";
		}
		$condition = preg_replace("/@$id/", $value, $condition);
	}
	$condition = "if ($condition) return true; else return false;";
	$ret = @eval($condition);
	//print $condition."<br />";
	//print_r($vars);
	//if ($ret) print " true<br />"; else print " false<br />";
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
	global $pgvreport, $currentElement, $footnoteElement;

	array_push($printDataStack, $printData);
	$printData = true;

	$style = "";
	if (isset($attrs["style"])) $style=$attrs["style"];
	$footnoteElement = $currentElement;
	$currentElement = new PGVRFootnote($style);
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

function PGVRHighlightedImageSHandler($attrs) {
	global $gedrec, $pgvreport;

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
					$size = getimagesize($media["file"]);
					if (($width>0)&&($size[0]>$size[1])) {
						$perc = $width / $size[0];
						$height= round($size[1]*$perc);
					}
					if (($height>0)&&($size[1]>$size[0])) {
						$perc = $height / $size[1];
						$width= round($size[0]*$perc);
					}
					$image = new PGVRImage($media["file"], $left, $top, $width, $height);
					$pgvreport->addElement($image);
				}
			}
		}
	}
}

function PGVRImageSHandler($attrs) {
	global $gedrec, $pgvreport, $MEDIA_DIRECTORY;

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
						$size = getimagesize($filename);
						if (($width>0)&&($height==0)) {
							$perc = $width / $size[0];
							$height= round($size[1]*$perc);
						}
						if (($height>0)&&($width==0)) {
							$perc = $height / $size[1];
							$width= round($size[0]*$perc);
						}
						//print "1 width:$width height:$height ";
						$image = new PGVRImage($filename, $left, $top, $width, $height);
						$pgvreport->addElement($image);
					}
				}
			}
		}
	}
	else {
		if (preg_match("/(jpg)|(jpeg)|(png)$/i", $filename)>0) {
			if (file_exists($filename)) {
				$size = getimagesize($filename);
				if (($width>0)&&($size[0]>$size[1])) {
					$perc = $width / $size[0];
					$height= round($size[1]*$perc);
				}
				if (($height>0)&&($size[1]>$size[0])) {
					$perc = $height / $size[1];
					$width= round($size[0]*$perc);
				}
				//print "2 width:$width height:$height ";
				$image = new PGVRImage($filename, $left, $top, $width, $height);
				$pgvreport->addElement($image);
			}
		}
	}
}

function PGVRLineSHandler($attrs) {
	global $pgvreport;

	$x1 = 0;
	$y1 = 0;
	$x2 = 0;
	$y2 = 0;
	if (isset($attrs["x1"])) $x1 = $attrs["x1"];
	if (isset($attrs["y1"])) $y1 = $attrs["y1"];
	if (isset($attrs["x2"])) $x2 = $attrs["x2"];
	if (isset($attrs["y2"])) $y2 = $attrs["y2"];

	$line = new PGVRLine($x1, $y1, $x2, $y2);
	$pgvreport->addElement($line);
}

function PGVRListSHandler($attrs) {
	global $pgvreport, $gedrec, $repeats, $repeatBytes, $list, $repeatsStack, $processRepeats, $parser, $vars, $sortby;

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
					$level = 1;
					foreach($tags as $indexval => $t) {
						if (!empty($searchstr)) $searchstr.="[^\n]*(\n[2-9][^\n]*)*\n";
						//-- search for both EMAIL and _EMAIL... silly double gedcom standard
						if ($t=="EMAIL" || $t=="_EMAIL") $t="_?EMAIL";
						$searchstr .= $level." ".$t;
						$level++;
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
	/*	case "family":
			$list = get_family_list();
			break;
		case "source":
			$list = get_source_list();
			break;
		case "other":
			$list = get_other_list();
			break; */
		default:
			if (count($filters)>0) $list = search_indis($filters);
			else $list = get_indi_list();
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
					$level = 1;
					$subrec = $value["gedcom"];
					foreach($tags as $indexval => $t) {
						$oldsub = $subrec;
						$subrec = get_sub_record($level, $level." ".$t, $subrec);
						if ($t=='EMAIL' && empty($subrec)) {
							$t = "_EMAIL";
							$subrec = get_sub_record($level, $level." ".$t, $oldsub);
						}
						$level++;
					}
					$level--;
					switch ($expr) {
						case "GTE":
							$ct = preg_match("/$level $t(.*)/", $subrec, $match);
							if ($ct>0) {
								$v = trim($match[1]);
								if ($t=="DATE") {
									$date1 = parse_date($v);
									$date2 = parse_date($val);
									if ($date1[0]["year"] > $date2[0]["year"]) $keep = true;
									else if ($date1[0]["year"] == $date2[0]["year"]) {
										if ($date1[0]["mon"] > $date2[0]["mon"] or empty($date1[0]["mon"]) or empty($date2[0]["mon"])) $keep = true;
										else if ($date1[0]["mon"] == $date2[0]["mon"]) {
											if ($date1[0]["day"] >= $date2[0]["day"] or empty($date1[0]["day"]) or empty($date2[0]["day"])) $keep = true;
											else $keep = false;
										} else $keep = false;
									} else $keep = false;
									//print "[$key ".implode(" ", $date1[0])." ".implode(" ", $date2[0])." keep=$keep] ";
								}
								else if ($val >= $v) $keep=true;
							}
							else $keep=false;
							break;
						case "LTE":
							$ct = preg_match("/$level $t(.*)/", $subrec, $match);
							if ($ct>0) {
								$v = trim($match[1]);
								if ($t=="DATE") {
									$date1 = parse_date($v);
									$date2 = parse_date($val);
									if ($date1[0]["year"] < $date2[0]["year"]) $keep = true;
									else if ($date1[0]["year"] == $date2[0]["year"]) {
										if ($date1[0]["mon"] < $date2[0]["mon"] or empty($date1[0]["mon"]) or empty($date2[0]["mon"])) $keep = true;
										else if ($date1[0]["mon"] == $date2[0]["mon"]) {
											if ($date1[0]["day"] <= $date2[0]["day"] or empty($date1[0]["day"]) or empty($date2[0]["day"])) $keep = true;
											else $keep = false;
										} else $keep = false;
									} else $keep = false;
									//print "[$key ".implode(" ", $date1[0])." ".implode(" ", $date2[0])." keep=$keep] ";
								}
								else if ($val >= $v) $keep=true;
							}
							else $keep=false;
							break;
						case "SUBCONTAINS":
							$ct = preg_match("/$val\W/i", $subrec);
							if ($ct>0) $keep = true;
							else $keep = false;
							break;
						default:
							$v = get_gedcom_value($t, $level, $subrec);
							//-- check for EMAIL and _EMAIL (silly double gedcom standard :P)
							if ($t=="EMAIL"&&empty($v)) {
								$t = "_EMAIL";
								$v = get_gedcom_value($t, $level, $subrec);
							}
							//print "[$key $t $v == $val $subrec]<br />";
							if ($v==$val) $keep=true;
							else $keep = false;
							//print $keep;
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
	else uasort($list, "compare_date");
	//print count($list);
	array_push($repeatsStack, array($repeats, $repeatBytes));
	$repeatBytes = xml_get_current_line_number($parser)+1;
}

function PGVRListEHandler() {
	global $list, $repeats, $repeatsStack, $repeatBytes, $parser, $parserStack, $report, $pgvreport, $gedrec, $processRepeats, $list_total, $list_private;
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
				printf($reportxml."\nPGVRRepeatTagEHandler XML error: %s at line %d", xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser));
				print_r($repeatsStack);
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

	$currentElement->addText(($list_total - $list_private)." / ".$list_total);
}

function PGVRRelativesSHandler($attrs) {
	global $pgvreport, $gedrec, $repeats, $repeatBytes, $list, $repeatsStack, $processRepeats, $parser, $vars, $sortby, $indilist;

	$processRepeats++;
	if ($processRepeats>1) return;

	$sortby = "NAME";
	$group = "child-family";
	$id = "";
	if (isset($attrs["sortby"])) $sortby = $attrs["sortby"];
	if (preg_match("/\\$(\w+)/", $sortby, $vmatch)>0) {
		$sortby = $vars[$vmatch[1]]["id"];
		$sortby = trim($sortby);
	}
	if (isset($attrs["group"])) $group = $attrs["group"];
	if (preg_match("/\\$(\w+)/", $group, $vmatch)>0) {
		$group = $vars[$vmatch[1]]["id"];
		$group = trim($sortby);
	}

	if (isset($attrs["id"])) $id = $attrs["id"];
	if (preg_match("/\\$(\w+)/", $id, $vmatch)>0) {
		$id = $vars[$vmatch[1]]["id"];
		$id = trim($id);
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
						find_person_record($parents["HUSB"]);
						$list[$parents["HUSB"]] = $indilist[$parents["HUSB"]];
					}
					if (!empty($parents["WIFE"])) {
						find_person_record($parents["WIFE"]);
						$list[$parents["WIFE"]] = $indilist[$parents["WIFE"]];
					}
					$famrec = find_family_record($famid);
					$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
					for($i=0; $i<$num; $i++) {
						find_person_record($smatch[$i][1]);
						$list[$smatch[$i][1]] = $indilist[$smatch[$i][1]];
					}
				}
				break;
			case "spouse-family":
				$famids = find_sfamily_ids($id);
				foreach($famids as $indexval => $famid) {
					$parents = find_parents($famid);
					find_person_record($parents["HUSB"]);
					find_person_record($parents["WIFE"]);
					$list[$parents["HUSB"]] = $indilist[$parents["HUSB"]];
					$list[$parents["WIFE"]] = $indilist[$parents["WIFE"]];
					$famrec = find_family_record($famid);
					$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
					for($i=0; $i<$num; $i++) {
						find_person_record($smatch[$i][1]);
						$list[$smatch[$i][1]] = $indilist[$smatch[$i][1]];
					}
				}
				break;
			case "direct-ancestors":
				add_ancestors($id);
				break;
			case "ancestors":
				add_ancestors($id,true);
				break;
			case "descendants":
				add_descendancy($id);
				break;
			case "all":
				add_ancestors($id,true);
				add_descendancy($id,true);
				break;
		}
	}

	if ($sortby!="none") {
		if ($sortby=="NAME") uasort($list, "itemsort");
		else if ($sortby=="ID") uasort($list, "idsort");
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
		if (displayDetailsById($key)) {
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

function PGVRGenerationSHandler($attrs) {
	global $list_total, $list_private, $generation, $currentElement;

	if (empty($generation)) $generation = 1;

	$currentElement->addText($generation);
}
?>

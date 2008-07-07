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
 * @version $Id: reportpdf.php,v 1.5 2008/07/07 17:30:14 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once("includes/reportbase.php");
require_once('tcpdf/config/lang/eng.php');
require_once("tcpdf/tcpdf.php");

/**
 * main PGV Report Class
 * @package PhpGedView
 * @subpackage Reports
 */
class PGVReport extends PGVReportBase {
	var $pdf;
	
	function setup($pw, $ph, $pageSize, $o, $m, $showGenText=true) {
		global $pgv_lang, $VERSION;
		parent::setup($pw, $ph, $pageSize, $o, $m, $showGenText);
		
		if (empty($this->pageFormat)) {		//-- send a custom size
			$this->pdf = new PGVRPDF($this->orientation, 'pt', array($pw*72,$ph*72));
		} else {							//-- send a known size
			$this->pdf = new PGVRPDF($this->orientation, 'pt', $this->pageFormat);
		}

		$this->pdf->setMargins($m, $m);
		$this->pdf->SetCompression(true);
		$this->pdf->setReport($this);
		
		if ($showGenText) {
			$element = new PGVRCellPDF(0,10, "C", "");
			$element->addText("$pgv_lang[generated_by] PhpGedView $VERSION");
			$element->setUrl("http://www.phpgedview.net/");
			$this->pdf->addFooter($element);
		}
		//TCPDF $this->pdf->SetAutoPageBreak(false);
		//TCPDF $this->pdf->SetAutoLineWrap(false);
	}

	function addElement(&$element) {
		if ($this->processing=="H") return $this->pdf->addHeader($element);
		if ($this->processing=="PH") return $this->pdf->addPageHeader($element);
		if ($this->processing=="F") return $this->pdf->addFooter($element);
		if ($this->processing=="B") return $this->pdf->addBody($element);
	}

	function run() {
		global $download, $embed_fonts;

		//TCPDF $this->pdf->SetEmbedFonts($embed_fonts);
		if ($embed_fonts) $this->pdf->AddFont('dejavusans', '', 'dejavusans.php');
		$this->pdf->setCurrentStyle(key($this->PGVRStyles));
		$this->pdf->AliasNbPages();
		$this->pdf->Body();
		header("Expires:");
		header("Pragma:");
		header("Cache-control:");
//		if (!isset($download)) $this->pdf->Output();
		if ($download=="") $this->pdf->Output();
		else $this->pdf->Output("pgv_report_".basename($_REQUEST["report"], ".xml").".pdf", "D");
	}
	
	function getStyle($s) {
		if (!isset($this->PGVRStyles[$s])) {
			$s = $this->pdf->getCurrentStyle();
			$this->PGVRStyles[$s] = $s;
		}
		return $this->PGVRStyles[$s];
	}

	function getMaxWidth() {
		$w = (($this->pagew * 72) - ($this->margin)) - $this->pdf->GetX();
		return $w;
	}

	function getPageHeight() {
		return ($this->pageh*72)-$this->margin;
	}

	function clearPageHeader() {
		$this->pdf->clearPageHeader();
	}
	
	function createCell($width, $height, $align, $style, $top=".", $left=".") {
		return new PGVRCellPDF($width, $height, $align, $style, $top, $left);
	}
	
	function createTextBox($width, $height, $border, $fill, $newline, $left=".", $top=".", $pagecheck="true") {
		return new PGVRTextBoxPDF($width, $height, $border, $fill, $newline, $left, $top, $pagecheck);
	}
	
	function createText($style, $color) {
		return new PGVRTextPDF($style, $color);
	}
	
	function createFootnote($style="") {
		return new PGVRFootnotePDF($style);
	}
	
	function createPageHeader() {
		return new PGVRPageHeaderPDF();
	}
	
	function createImage($file, $x, $y, $w, $h) {
		return new PGVRImagePDF($file, $x, $y, $w, $h);
	}
	
	function createLine($x1, $y1, $x2, $y2) {
		return new PGVRLinePDF($x1, $y1, $x2, $y2);
	}
	
	function createHTML($tag, $attrs) {
		return new PGVRHtmlPDF($tag, $attrs);
	}
} //-- end PGVReport

/**
 * PGV Report PDF Class
 *
 * This class inherits from the FPDF class and is used to generate the PDF document
 * @package PhpGedView
 * @subpackage Reports
 */
class PGVRPDF extends TCPDF {
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
			if (is_string($element) && $element=="footnotetexts") $this->Footnotes();
			else if (is_string($element) && $element=="addpage") $this->AddPage();
			else $element->render($this);
		}
		foreach($this->pageHeaderElements as $indexval => $element) {
			if (is_string($element) && $element=="footnotetexts") $this->Footnotes();
			else if (is_string($element) && $element=="addpage") $this->AddPage();
			else if (is_object($element)) $element->render($this);
		}
		$this->currentStyle = $temp;
	}

	function Footer() {
		$this->SetY(-36);
		$this->currentStyle = "";
		foreach($this->footerElements as $indexval => $element) {
			if (is_string($element) && $element=="footnotetexts") $this->Footnotes();
			else if (is_string($element) && $element=="addpage") $this->AddPage();
			else if (is_object($element)) $element->render($this);
		}
	}

	function Body() {
		global $TEXT_DIRECTION;
		$this->AddPage();
		$this->currentStyle = "";
		foreach($this->bodyElements as $indexval => $element) {
			if (is_string($element) && $element=="footnotetexts") $this->Footnotes();
			else if (is_string($element) && $element=="addpage") $this->AddPage();
			else if (is_object($element)) $element->render($this);
		}
	}

	function Footnotes() {
		$this->currentStyle = "";
		foreach($this->printedfootnotes as $indexval => $element) {
			//print ($this->GetY() + $element->getFootnoteHeight($this)).">".$this->getPageHeight();
			if (($this->GetY() + $element->getFootnoteHeight($this)) > $this->getPageHeight()) $this->AddPage();
			$element->renderFootnote($this);
			
			if ($this->GetY() > $this->getPageHeight()) $this->AddPage();
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
		//print_r($style);
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


$pgvreport = new PGVReport();
$PGVReportRoot = $pgvreport;

/**
 * Cell element
 */
class PGVRCellPDF extends PGVRCell {
	
	function PGVRCellPDF($width, $height, $align, $style, $top=".", $left=".") {
		parent::PGVRCell($width, $height, $align, $style, $top, $left);
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
}

/**
 * Cell element
 */
class PGVRHtmlPDF extends PGVRHtml {
	
	function PGVRHtmlPDF($tag, $attrs) {
		parent::PGVRHtml($tag, $attrs);
	}
	
	function render(&$pdf, $sub = false) {
		global $TEXT_DIRECTION, $embed_fonts;
		//print "[".$this->text."] ";

		if (!empty($this->attrs['pgvrstyle'])) $pdf->setCurrentStyle($this->attrs['pgvrstyle']);
		if (!empty($this->attrs['width'])) $this->attrs['width'] *= 3.9;
		
		$this->text = $this->getStart().$this->text;
		foreach($this->elements as $k=>$element) {
			if (is_string($element) && $element=="footnotetexts") $pdf->Footnotes();
			else if (is_string($element) && $element=="addpage") $pdf->AddPage();
			else if ($element->get_type()=='PGVRHtml') {
//				$this->text .= $element->getStart(); 
				$this->text .= $element->render($pdf, true);
			}
			else $element->render($pdf);
		}
		$this->text .= $this->getEnd();
		if ($sub) return $this->text;
//		print "[".htmlentities($this->text)."] ";
		$pdf->writeHTML($this->text);
	}

}

/**
 * TextBox element
 */
class PGVRTextBoxPDF extends PGVRTextBox {
	
	function PGVRTextBoxPDF($width, $height, $border, $fill, $newline, $left=".", $top=".", $pagecheck="true") {
		parent::PGVRTextBox($width, $height, $border, $fill, $newline, $left, $top, $pagecheck);
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
			if (is_object($element)) {
				if ($element->get_type()=="PGVRText") {
					if (empty($lastelement)) $lastelement = $element;
					else {
						if ($element->getStyleName()==$lastelement->getStyleName()) {
							$lastelement->addText(preg_replace("/\n/", "<br />", $element->getValue()));
						}
						else {
							if (!empty($lastelement)) {
								$newelements[] = $lastelement;
								$lastelement = $element;
							}
						}
					}
				}
				//-- do not keep empty footnotes
				else if (($element->get_type()!="PGVRFootnote")||(trim($element->getValue())!="")) {
					if (!empty($lastelement)) {
						$newelements[] = $lastelement;
						$lastelement = "";
					}
					$newelements[] = $element;
				}
			}
			else {
				if (!empty($lastelement)) {
					$newelements[] = $lastelement;
					$lastelement = "";
				}
				$newelements[] = $element;
			}
		}
		if (!empty($lastelement)) $newelements[] = $lastelement;
		$this->elements = $newelements;

		//-- calculate the text box height
		$h = 0;
		$w = 0;
		for($i=0; $i<count($this->elements); $i++) {
			if (is_object($this->elements[$i])) {
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
		if ($this->pagecheck) {
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
		}

		if (!empty($this->style)) $pdf->Rect($pdf->GetX(), $pdf->GetY(), $this->width, $this->height, $this->style);
		$pdf->SetXY($pdf->GetX(), $pdf->GetY()+1);
		$curx = $pdf->GetX();
		foreach($this->elements as $indexval => $element) {
			if (is_string($element) && $element=="footnotetexts") $pdf->Footnotes();
			else if (is_string($element) && $element=="addpage") $pdf->AddPage();
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
}

/**
 * Text element
 */
class PGVRTextPDF extends PGVRText {

	function PGVRTextPDF($style, $color) {
		parent::PGVRText($style, $color);
	}
	
	function render(&$pdf, $curx=0) {
		global $embed_fonts;
		$pdf->setCurrentStyle($this->styleName);
		$temptext = preg_replace("/#PAGENUM#/", $pdf->PageNo(), $this->text);
		//print $this->text;
		$x = $pdf->GetX();
		$cury = $pdf->GetY();

		if (!empty($this->color)) {
			$ct = preg_match("/#?(..)(..)(..)/", $this->color, $match);
			if ($ct>0) {
				//$this->style .= "F";
				$r = hexdec($match[1]);
				$g = hexdec($match[2]);
				$b = hexdec($match[3]);
				$pdf->SetTextColor($r, $g, $b);
			}
		}

		$lines = preg_split("/\n/", $temptext);
		$styleh = $pdf->getCurrentStyleHeight();
		if (count($lines)>0) {
			foreach($lines as $indexval => $line) {
				$pdf->SetXY($x, $cury);
//				print "[$x $cury $line]";
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
				$i=0;
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
					else {
						if ($i>0) $newtext .= "\n";
						$newtext .= $line;
					}
					$i++;
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
}

/**
 * Footnote element
 */
class PGVRFootnotePDF extends PGVRFootnote {
	var $styleName;
	var $addlink;
	var $num;
	
	function PGVRFootnotePDF($style="") {
		parent::PGVRFootnote($style);
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
	
	function getFootnoteHeight(&$pdf) {
		$ct = substr_count($this->text, "\n");
		if ($ct>0) $ct+=1;
		$style = $pdf->getStyle($this->styleName);
		$h = (($style["size"]+1)*$ct);
		//print "[".$this->text." $ct $h]";
		return $h;
	}
}

/**
 * PageHeader element
 */
class PGVRPageHeaderPDF extends PGVRPageHeader {
	
	function PGVRPageHeaderPDF() {
		parent::PGVRPageHeader();
	}
	function render(&$pdf) {
		$pdf->clearPageHeader();
		foreach($this->elements as $indexval => $element) {
			$pdf->addPageHeader($element);
		}
	}
}

/**
 * image element
 */
class PGVRImagePDF extends PGVRImage {
	
	function PGVRImagePDF($file, $x, $y, $w, $h) {
		parent::PGVRImage($file, $x, $y, $w, $h);
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
class PGVRLinePDF extends PGVRLine {
	
	function PGVRLinePDF($x1, $y1, $x2, $y2) {
		parent::PGVRLine($x1, $y1, $x2, $y2);
	}
	
	function render(&$pdf) {
		if ($this->x1==".") $this->x1=$pdf->GetX();
		if ($this->y1==".") $this->y1=$pdf->GetY();
		if ($this->x2==".") $this->x2=$pdf->GetX();
		if ($this->y2==".") $this->y2=$pdf->GetY();
		$pdf->Line($this->x1, $this->y1, $this->x2, $this->y2);
	}
} //-- END PGVRLine

?>
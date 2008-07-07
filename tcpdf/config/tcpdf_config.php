<?php
//============================================================+
// File name   : tcpdf_config.php
// Begin       : 2004-06-11
// Last Update : 2007-07-30
//
// Description : Congiguration file for TCPDF.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Tecnick.com S.r.l.
//               Via Ugo Foscolo n.19
//               09045 Quartu Sant'Elena (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Configuration file for TCPDF.
 * @author Nicola Asuni
 * @copyright Copyright &copy; 2004, Tecnick.com S.r.l. - Via Ugo Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @package com.tecnick.tcpdf
 * @version 1.53.0.TC033
 * @link http://tcpdf.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2004-10-27
 */

// If you define the constant K_TCPDF_EXTERNAL_CONFIG, the following settings will be ignored.

if (!defined("K_TCPDF_EXTERNAL_CONFIG")) {
	
	// PLEASE SET THE FOLLOWING CONSTANTS:
	
	/**
	 * installation path
	 */
	define ("K_PATH_MAIN", "./tcpdf/");
	
	/**
	 * url path
	 */
	define ("K_PATH_URL", "./tcpdf/");
	
	/**
	 * path for PDF fonts
	 */
	define ("FPDF_FONTPATH", "includes/fonts/");
	
	/**
	 * cache directory for temporary files (full path)
	 */
	//define ("K_PATH_CACHE", K_PATH_MAIN."cache/");
	define ("K_PATH_CACHE", $INDEX_DIRECTORY);
	
	/**
	 * cache directory for temporary files (url path)
	 */
	define ("K_PATH_URL_CACHE", $INDEX_DIRECTORY);
	
	/**
	 *images directory
	 */
	define ("K_PATH_IMAGES", K_PATH_MAIN."images/");
	
	/**
	 * blank image
	 */
	define ("K_BLANK_IMAGE", K_PATH_IMAGES."_blank.png");
	
	/**
	 * page format
	 */
	define ("PDF_PAGE_FORMAT", "A4");
	
	/**
	 * page orientation (P=portrait, L=landscape)
	 */
	define ("PDF_PAGE_ORIENTATION", "P");
	
	/**
	 * document creator
	 */
	define ("PDF_CREATOR", "TCPDF");
	
	/**
	 * document author
	 */
	define ("PDF_AUTHOR", "TCPDF");
	
	/**
	 * header title
	 */
	define ("PDF_HEADER_TITLE", "header title");
	
	/**
	 * header description string
	 */
	define ("PDF_HEADER_STRING", "first row\nsecond row\nthird row");
	
	/**
	 * image logo
	 */
	define ("PDF_HEADER_LOGO", "logo_example.png");
	
	/**
	 * header logo image width [mm]
	 */
	define ("PDF_HEADER_LOGO_WIDTH", 20);
	
	/**
	 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
	 */
	define ("PDF_UNIT", "mm");
	
	/**
	 * header margin
	 */
	define ("PDF_MARGIN_HEADER", 5);
	
	/**
	 * footer margin
	 */
	define ("PDF_MARGIN_FOOTER", 10);
	
	/**
	 * top margin
	 */
	define ("PDF_MARGIN_TOP", 27);
	
	/**
	 * bottom margin
	 */
	define ("PDF_MARGIN_BOTTOM", 25);
	
	/**
	 * left margin
	 */
	define ("PDF_MARGIN_LEFT", 15);
	
	/**
	 * right margin
	 */
	define ("PDF_MARGIN_RIGHT", 15);
	
	/**
	 * main font name
	 */
	define ("PDF_FONT_NAME_MAIN", "freeserif"); //vera
	
	/**
	 * main font size
	 */
	define ("PDF_FONT_SIZE_MAIN", 10);
	
	/**
	 * data font name
	 */
	define ("PDF_FONT_NAME_DATA", "freeserif"); //verase
	
	/**
	 * data font size
	 */
	define ("PDF_FONT_SIZE_DATA", 8);
	
	/**
	 *  scale factor for images (number of points in user unit)
	 */
	define ("PDF_IMAGE_SCALE_RATIO", 4);
	
	/**
	 * magnification factor for titles
	 */
	define("HEAD_MAGNIFICATION", 1.1);
	
	/**
	 * height of cell repect font height
	 */
	define("K_CELL_HEIGHT_RATIO", 1.25);
	
	/**
	 * title magnification respect main font size
	 */
	define("K_TITLE_MAGNIFICATION", 1.3);
	
	/**
	 * reduction factor for small font
	 */
	define("K_SMALL_RATIO", 2/3);
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>

<?php
/**
 * Popup window for viewing images
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
 * @version $Id: imageview.php,v 1.5 2007/06/10 09:43:47 lsces Exp $
 * @package PhpGedView
 * @subpackage Media
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();
if (isset($_REQUEST['filename'])) $filename = $_REQUEST['filename'];
if (!isset($filename)) $filename = "";
$filename = stripslashes($filename);
$imgsize = @getimagesize($filename);
if (!$imgsize) {
	$imgwidth = 300;
	$imgheight = 300;
} else {
	$imgwidth = $imgsize[0]+2;
	$imgheight = $imgsize[1]+2;
}
$gBitSystem->mDisplayOnlyContent = true;
$gBitSmarty->assign( "imgwidth", $imgwidth );
$gBitSmarty->assign( "imgheight", $imgheight );
$gBitSmarty->assign( "filename", $filename );
$gBitSystem->display( 'bitpackage:phpgedview/imageview.tpl', tra( 'Image popup' ) );
?>

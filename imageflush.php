<?php
/**
 * Flush an image to the browser
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * $Id: imageflush.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 *
 * @package PhpGedView
 * @subpackage Charts
 */
require("config.php");

/**
 * display any message as a PNG image
 *
 * @param string $txt message to be displayed
 */
function ImageFlushError($txt) {
	Header('Content-Type: image/png');
	$image = ImageCreate (400, 40);
	$bg = ImageColorAllocate($image, 0xEE, 0xEE, 0xEE);
	$red = ImageColorAllocate($image, 0xFF, 0x00, 0x00);
	ImageString($image, 2, 10, 10, $txt, $red);
	ImagePng($image);
	return;
}

// get image_type
$image_type = @$_GET['image_type'];
$image_type = strtolower($image_type);
if ($image_type=="jpg") $image_type="jpeg";
if ($image_type=="") $image_type="png";
//ImageFlushError($image_type);

// read image_data from SESSION variable
//session_start();
$image_data = @$_SESSION['image_data'];
$image_data = @unserialize($image_data);
unset($_SESSION['image_data']);
if (empty($image_data)) ImageFlushError("Error : \$_SESSION['image_data'] is empty");

// send data to browser
Header("Content-Type: image/$image_type");
Header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
Header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
Header('Pragma: no-cache');
print $image_data;
?>
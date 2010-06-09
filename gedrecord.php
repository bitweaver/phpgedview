<?php
/**
 * Parses gedcom file and displays record for given id in raw text
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 * @package PhpGedView
 * @subpackage Charts
 */

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/classes/class_gedcomrecord.php");
header("Content-Type: text/html; charset=$CHARACTER_SET");

$pid=safe_GET_xref('pid');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php print $CHARACTER_SET; ?>" />
		<title><?php print "$pid Record"; ?></title>
	</head>
	<body><?php

if (!$SHOW_GEDCOM_RECORD && !PGV_USER_CAN_ACCEPT) {
	print "<span class=\"error\">".$pgv_lang["ged_noshow"]."</span>\n";
	print "</body></html>";
	exit;
}

$obj = GedcomRecord::getInstance($pid);

if (is_null($obj) || !$obj->canDisplayDetails()) {
	print_privacy_error($CONTACT_EMAIL);
	print "</body></html>";
	exit;
}
if (!isset($fromfile)) $indirec = $obj->getGedcomRecord();
else  {
	$indirec = find_updated_record($pid);
	$indirec = privatize_gedcom($indirec);
}
$indirec = preg_replace("/@(\w+)@/", "@<a href=\"gedrecord.php?pid=$1\">$1</a>@", $indirec);
print "<pre>$indirec</pre>";
print "</body></html>";

?>

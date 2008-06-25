<?php
/**
 * MyGedView page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
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
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: index.php,v 1.13 2008/06/25 22:21:15 spiderr Exp $
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

if (!isset($CONFIGURED)) {
//	print "Unable to include the config.php file.  Make sure that . is in your PHP include path in the php.ini file.";
//	exit;
}

if (isset($_REQUEST['content_id'])) {
	$gGedcom = new BitGEDCOM( NULL , $_REQUEST['content_id'] );
	$gGedcom->load();
} 
else
	$gGedcom = new BitGEDCOM();

//if ( isset($gGedcom->mGedcomName) ) {
//	header("Location: individual.php?pid=I1&ged=".$gGedcom->mGedcomName."#content");
//	exit;
//}

$gBitSmarty->assign( 'pagetitle', 'Default GEDCOM' );

$listHash = $_REQUEST;
$listgedcoms = $gGedcom->getList( $listHash );
$gBitSmarty->assign_by_ref( 'listgedcoms', $listgedcoms );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

// Display the template
$gBitSystem->display( 'bitpackage:phpgedview/main_menu.tpl', tra( 'GEDCOM Main Menu' ) , array( 'display_mode' => 'display' ));
?>

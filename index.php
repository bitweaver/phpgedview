<?php
/**
 * MyGedView page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team, all rights reserved
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
 * @version $Id: index.php,v 1.16 2010/02/08 21:27:24 wjames5 Exp $
 */

// Initialization
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'includes/bitsession.php' );

//$THEME_DIR = "themes/bitweaver/";
//require_once('themes/bitweaver/theme.php');

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
//-- handle block AJAX calls
/**
 * In order for a block to make an AJAX call the following request parameters must be set
 * block = the method name of the block to call (e.g. 'print_random_media')
 * side = the side of the page the block is on (e.g. 'main' or 'right')
 * bindex = the number of the block on that side, first block = 0
 */
/**
if ($action=="ajax") {
	//--  if a block wasn't sent then exit with nothing
	if (!isset($_REQUEST['block'])) {
		print "Block not sent";
		exit;
	}
	$block = $_REQUEST['block'];
	//-- set which side the block is on
	$side = "main";
	if (isset($_REQUEST['side'])) $side = $_REQUEST['side'];
	//-- get the block number
	if (isset($_REQUEST['bindex'])) {
		if (isset($ublocks[$side][$_REQUEST['bindex']])) {
			$blockval = $ublocks[$side][$_REQUEST['bindex']];
			if ($blockval[0]==$block && function_exists($blockval[0])) {
				if ($side=="main") $param1 = "false";
				else $param1 = "true";
				if (function_exists($blockval[0]) && !loadCachedBlock($blockval, $side.$_REQUEST['bindex'])) {
					ob_start();
					eval($blockval[0]."($param1, \$blockval[1], \"$side\", ".$_REQUEST['bindex'].");");
					$content = ob_get_contents();
					saveCachedBlock($blockval, $side.$_REQUEST['bindex'], $content);
					ob_end_flush();
				}
				exit;
			}
		}
	}
	
	//-- not sure which block to call so call the first one we find
	foreach($ublocks["main"] as $bindex=>$blockval) {
		if (isset($DEBUG)&&($DEBUG==true)) print_execution_stats();
		if ($blockval[0]==$block && function_exists($blockval[0])) eval($blockval[0]."(false, \$blockval[1], \"main\", $bindex);");
	}
	foreach($ublocks["right"] as $bindex=>$blockval) {
		if (isset($DEBUG)&&($DEBUG==true)) print_execution_stats();
		if ($blockval[0]==$block && function_exists($blockval[0])) eval($blockval[0]."(true, \$blockval[1], \"right\", $bindex);");
	}
	exit;
}
//-- end of ajax call handler
*/

$gBitSmarty->assign( 'pagetitle', 'Default GEDCOM' );

$listHash = $_REQUEST;
$listgedcoms = $gGedcom->getList( $listHash );
$gBitSmarty->assign_by_ref( 'listgedcoms', $listgedcoms );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

// Display the template
$gBitSystem->display( 'bitpackage:phpgedview/main_menu.tpl', tra( 'GEDCOM Main Menu' ) );
?>

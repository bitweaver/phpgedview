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
 * @version $Id: index.php,v 1.10 2007/05/30 07:24:17 lsces Exp $
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

if (!isset($CONFIGURED)) {
//	print "Unable to include the config.php file.  Make sure that . is in your PHP include path in the php.ini file.";
//	exit;
}

if (!isset($action)) $action="";

$uname = $gBitUser->mUsername;
if ( !$gBitUser->isValid() ) {
	if (!empty($command)) {
		if ($command=="user") {
			header("Location: login.php?help_message=mygedview_login_help&url=".urlencode("index.php?command=user"));
			exit;
		}
	}
	$command="gedcom";
}

	// Display the template
	$gBitSystem->display( 'bitpackage:phpgedview/main_menu.tpl', tra( 'GEDCOM Main Menu' ) );
?>

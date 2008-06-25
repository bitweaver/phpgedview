<?php
/**
 * Individual Page
 *
 * Display all of the information about an individual
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
 * @subpackage Charts
 * @version $Id: individual.php,v 1.7 2008/06/25 22:21:15 spiderr Exp $
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();
global $GEDCOM;
	$GEDCOM = "CAINEFull.GED";

// leave manual config until we can move it to bitweaver table 
require_once("includes/controllers/individual_ctrl.php");
//require_once("includes/serviceclient_class.php");

//if (file_exists("modules/googlemap/".$pgv_language["english"])) require("modules/googlemap/".$pgv_language["english"]);
//if (file_exists("modules/googlemap/".$pgv_language[$LANGUAGE])) require("modules/googlemap/".$pgv_language[$LANGUAGE]);

global $USE_THUMBS_MAIN;
global $linkToID;
global $SEARCH_SPIDER;

// Display the template
$gBitSystem->display( 'bitpackage:phpgedview/main_menu.tpl', tra( 'GEDCOM Main Menu' ) , array( 'display_mode' => 'display' ));
?>

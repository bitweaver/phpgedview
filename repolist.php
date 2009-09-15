<?php
/**
 * Repositories List
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  PGV Development Team
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
 * @subpackage Lists
 * @version $Id: repolist.php,v 1.7 2009/09/15 20:06:00 lsces Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
//require("config.php");
require_once 'includes/functions/functions_print_lists.php';
$repolist = get_repo_list();               //-- array of regular repository titles
$addrepolist = get_repo_add_title_list();  //-- array of additional repository titlesadd

$cr = count($repolist);
$ca = count($addrepolist);
$ctot = $cr + $ca;

$gBitSmarty->assign_by_ref( "repolist", $repolist );
$gBitSmarty->assign_by_ref( "addrepolist", $addrepolist );

$doctitle = "Full Repository List";
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSystem->display( 'bitpackage:phpgedview/repolist.tpl', tra( 'Repository list' ) );?>

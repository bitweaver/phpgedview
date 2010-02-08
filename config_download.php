<?php
/**
* Download config files that could not be saved.
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team
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
* @version $Id: config_download.php,v 1.7 2010/02/08 21:27:24 wjames5 Exp $
* @package PhpGedView
* @subpackage Admin
*/

/**
 * load the main configuration and context
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require "config.php";
require $confighelpfile["english"];
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];

global $gBitUser;
if ( !$gBitUser->IsAdmin() && $CONFIGURED ) {
	header('Location: admin.php');
	exit;
}

$file=safe_GET('file', PGV_REGEX_NOSCRIPT, 'config.php');

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="'.$file.'"');

echo $_SESSION[$file];

?>

<?php
/**
 * Shows helptext to the users
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
 * This Page Is Valid XHTML 1.0 Transitional! > 12 September 2005
 *
 * @author PGV Development Team
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: help_text.php,v 1.7 2009/09/15 20:06:00 lsces Exp $
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require "config.php";

loadLangFile("pgv_help, pgv_admin, pgv_editor, ra_lang, gm_lang, gm_help, sm_lang, sm_help");	// Load language keys

require 'includes/help_text_vars.php';

print_simple_header($pgv_lang['help_header']);

echo '<a name="top"></a><span class="helpheader">', $pgv_lang['help_header'], '</span><br /><br /><div class="helptext">';

$help =safe_GET('help');
$action=safe_GET('action');

if ($help=='help_useradmin.php' && $action=='edituser') {
	$help='edit_useradmin_help';
}
if ($help=='help_login_register.php' && $action=='pwlost') {
	$help='help_login_lost_pw.php';
}
if ($help=='help_contents_help') {
	if (PGV_USER_IS_ADMIN) {
		$help='admin_help_contents_help';
		echo $pgv_lang['admin_help_contents_head_help'];
	} else {
		echo $pgv_lang['help_contents_head_help'];
	}
	print_help_index($help);
} else {
	print_text($help);
}
echo '</div><br /><br /><br />';
echo '<a href="#top" title="', $pgv_lang['move_up'], '">', $UpArrow, '</a><br />';
echo '<a href="help_text.php?help=help_contents_help"><b>', $pgv_lang['help_contents'], '</b></a><br />';
echo '<a href="javascript:;" onclick="window.close();"><b>', $pgv_lang['close_window'], '</b></a>';
print_simple_footer();
?>

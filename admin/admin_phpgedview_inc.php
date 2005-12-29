<?php
// $Header: /cvsroot/bitweaver/_bit_phpgedview/admin/admin_phpgedview_inc.php,v 1.1 2005/12/29 18:23:19 lsces Exp $
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$phpgedviewValues = array(
	'pgv_week_offset',
	'pgv_hour_fraction',
	'pgv_day_start',
	'pgv_day_end',
	'pgv_user_prefs',
);

// this function only exists if it's been included by the index.php page. if
// it's been included from anywhere else, we don't execute this section
if( function_exists( 'simple_set_value' ) && $gBitUser->isAdmin() && !empty( $_REQUEST['phpedview_submit'] ) ) {
	foreach( $phpgedviewValues as $item ) {
		simple_set_value( $item, PHPGEDVIEW_PKG_NAME );
	}
}
?>

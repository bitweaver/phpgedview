<?php
global $gBitSystem;

$gBitSystem->registerPackage( 'phpgedview',  dirname( __FILE__ ).'/' );

if( $gBitSystem->isPackageActive( PHPGEDVIEW_PKG_NAME ) ) {
	$gBitSystem->registerAppMenu( PHPGEDVIEW_PKG_DIR, 'Genealogy', PHPGEDVIEW_PKG_URL.'index.php', 'bitpackage:phpgedview/menu_phpgedview.tpl', PHPGEDVIEW_PKG_NAME );
}
?>

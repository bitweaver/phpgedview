<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'phpgedview',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( PHPGEDVIEW_PKG_NAME ) ) {
		$menuHash = array(
			'package_name'  => PHPGEDVIEW_PKG_NAME,
			'index_url'     => PHPGEDVIEW_PKG_URL.'index.php',
			'menu_template' => 'bitpackage:phpgedview/menu_phpgedview.tpl',
		);
	$gBitSystem->registerAppMenu( $menuHash );
}

if( !defined( 'PHPGEDVIEW_DB_PREFIX' ) ) {
	$lastQuote = strrpos( BIT_DB_PREFIX, '`' );
	if( $lastQuote != FALSE ) {
		$lastQuote++;
	}
	$prefix = substr( BIT_DB_PREFIX,  $lastQuote );
	define( 'PHPGEDVIEW_DB_PREFIX', $prefix.'pgv_' );
}

$TBLPREFIX = PHPGEDVIEW_DB_PREFIX;
?>

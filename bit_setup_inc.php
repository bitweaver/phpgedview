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

//--load common functions
require_once("includes/functions/functions.php");
//-- set the error handler
$OLD_HANDLER = set_error_handler("pgv_error_handler");
//-- load db specific functions
require_once("includes/functions/functions_db.php");
require_once("includes/functions/functions_name.php");
require_once("includes/functions/functions_date.php");
require_once("themes/bitweaver/theme.php");
$TBLPREFIX = PHPGEDVIEW_DB_PREFIX;
?>

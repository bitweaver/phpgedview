<?php
// $Header: /cvsroot/bitweaver/_bit_phpgedview/admin/admin_gedcoms.php,v 1.7 2009/11/01 20:53:55 lsces Exp $
require_once( '../../bit_setup_inc.php' );

require_once( PHPGEDVIEW_PKG_PATH.'includes/bitsession.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
if ( isset($_REQUEST['g_id']) ) {
	$gGedcom = new BitGEDCOM( $_REQUEST['g_id'] );
	$gGedcom->load();
	
}
else 
	$gGedcom = new BitGEDCOM();

// Define some constants to save calculating the same value repeatedly.
// To be replaced with GGedcom object inside PGV
define('PGV_GEDCOM', $gGedcom->mGedcomName);
define('PGV_GED_ID', $gGedcom->mGEDCOMId);

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
$gBitSystem->verifyPermission( 'p_phpgedview_admin' );

if( isset( $_REQUEST['fSubmitAddGedcom'] ) ) {
	$gGedcom->storeGedcom( $_REQUEST );
	if ( !empty( $gGedcom->mErrors ) ) {
		$gBitSmarty->assign_by_ref('errors', $gGedcom->mErrors );
	}
} elseif( !empty( $_REQUEST['fActivateTopic'] )&& $gGedcom ) {
//	$gGedcom->activateTopic();
} elseif( !empty( $_REQUEST['fDeactivateTopic'] )&& $gGedcom ) {
//	$gGedcom->deactivateTopic();
} elseif( !empty( $_REQUEST['fRemoveGedcom'] )&& $gGedcom ) {
	$gGedcom->expunge();
} elseif( !empty( $_REQUEST['fUpload'] )&& $gGedcom ) {
	$gGedcom->expungeGedcom( 1 ); //$_REQUEST['g_id']);
	$gGedcom->importGedcom();
}

$gBitSmarty->assign_by_ref( "gContent", $gGedcom );

$listHash = $_REQUEST;
$listgedcoms = $gGedcom->getList( $listHash );

$gBitSmarty->assign_by_ref( 'listgedcoms', $listgedcoms );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

$gBitSystem->display( 'bitpackage:phpgedview/admin_gedcoms.tpl', tra( 'Manage Gedcoms' ) );
?>

<?php
// $Header: /cvsroot/bitweaver/_bit_phpgedview/admin/admin_gedcoms.php,v 1.4 2007/06/09 21:11:05 lsces Exp $
require_once( '../../bit_setup_inc.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
if ( isset($_REQUEST['g_id']) ) {
	$gGedcom = new BitGEDCOM( $_REQUEST['g_id'] );
	$gGedcom->load();
}
else 
	$gGedcom = new BitGEDCOM();
//include_once( PHPGEDVIEW_PKG_PATH.'lookup_article_topic_inc.php' );

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
	$this->expungeGedcom($_REQUEST['g_id']);
	$this->importGedcom();
}

$gBitSmarty->assign_by_ref( "gContent", $gGedcom );

$listHash = $_REQUEST;
$listgedcoms = $gGedcom->getList( $listHash );

$gBitSmarty->assign_by_ref( 'listgedcoms', $listgedcoms );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

$gBitSystem->display( 'bitpackage:phpgedview/admin_gedcoms.tpl', tra( 'Manage Gedcoms' ) );
?>

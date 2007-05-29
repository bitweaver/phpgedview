<?php
// $Header: /cvsroot/bitweaver/_bit_phpgedview/admin/admin_gedcoms.php,v 1.2 2007/05/29 08:42:29 lsces Exp $
require_once( '../../bit_setup_inc.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gContent = new BitGEDCOM();
//include_once( PHPGEDVIEW_PKG_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
$gBitSystem->verifyPermission( 'p_phpgedview_admin' );

if( isset( $_REQUEST["fSubmitAddGedcom"] ) ) {
	vd($_REQUEST);
	$gContent->storeGedcom( $_REQUEST );
	if ( !empty( $gContent->mErrors ) ) {
		$gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
	}
} elseif( !empty( $_REQUEST['fActivateTopic'] )&& $gContent ) {
	$gContent->activateTopic();
} elseif( !empty( $_REQUEST['fDeactivateTopic'] )&& $gContent ) {
	$gContent->deactivateTopic();
} elseif( !empty( $_REQUEST['fRemoveTopic'] )&& $gContent ) {
	$gContent->removeTopic();
} elseif( !empty( $_REQUEST['fRemoveTopicAll'] )&& $gContent ) {
	$gContent->removeTopic( TRUE );
}

$gBitSmarty->assign_by_ref( "gContent", $gContent );

$listHash = $_REQUEST;
$listgedcoms = $gContent->getList( $listHash );

$gBitSmarty->assign_by_ref( 'listgedcoms', $listgedcoms );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

$gBitSystem->display( 'bitpackage:phpgedview/admin_gedcoms.tpl', tra( 'Manage Gedcoms' ) );
?>

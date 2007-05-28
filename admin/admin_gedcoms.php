<?php
// $Header: /cvsroot/bitweaver/_bit_phpgedview/admin/admin_gedcoms.php,v 1.1 2007/05/28 19:22:18 lsces Exp $
require_once( '../../bit_setup_inc.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
//include_once( PHPGEDVIEW_PKG_PATH.'lookup_article_topic_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
$gBitSystem->verifyPermission( 'p_phpgedview_admin' );

if( isset( $_REQUEST["fSubmitAddTopic"] ) ) {
	$gContent->storeTopic( $_REQUEST );
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

$gGedcom = new BitGEDCOM();
$gedcoms = $gGedcom->getList();
$gBitSmarty->assign( 'gedcoms', $gedcoms );

$gBitSystem->display( 'bitpackage:phpgedview/admin_gedcoms.tpl', tra( 'Manage Gedcoms' ) );
?>

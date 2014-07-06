<?php
// $Header$
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

$calendar = array( "gregorian", "julian", "french", "jewish", 
	"jewish_and_gregorian", "hebrew", "hebrew_and_gregorian" );
$gBitSmarty->assign( 'calendar', $calendar );
$generation = range( 0, 20 );
$gBitSmarty->assign( 'generation', $generation );

$gedcomPrefixValues = array(
	"pgv_gedcom_prefix_id" => array(
		'label' => 'Individual entry prefix',
	),
	"pgv_source_prefix_id" => array(
		'label' => 'Source entry prefix',
	),
	"pgv_repo_prefix_id" => array(
		'label' => 'Repo entry prefix',
	),
	"pgv_fam_prefix_id" => array(
		'label' => 'Family entry prefix',
	),
	"pgv_media_prefix_id" => array(
		'label' => 'Media entry prefix',
	),
);
$gBitSmarty->assign( 'gedcomPrefixValues', $gedcomPrefixValues );

$formGedcomFeatures = array(
	"pgv_default_pedigree_generations" => array(
		'label' => 'Default Pedigree Generations',
		'note' => 'Default number of generations in pedigree charts.',
	),
	"pgv_max_pedigree_generations" => array(
		'label' => 'Maximum Pedigree Generations',
		'note' => 'Maximum number of generations in pedigree charts.',
	),
	"pgv_max_descendancy_generations" => array(
		'label' => 'Maximum Descendancy Generations',
		'note' => 'Maximum number of generations in descendancy charts.',
	),
);
$gBitSmarty->assign( 'formGedcomFeatures',$formGedcomFeatures );

$formGedcomLists = array(
	"gedcom_list_title" => array(
		'label' => 'Title',
	),
	"gedcom_list_description" => array(
		'label' => 'Description',
	),
	"gedcom_list_created" => array(
		'label' => 'Creation date',
		'note' => 'Date the gedcoms were created.',
	),
	"gedcom_list_lastmodif" => array(
		'label' => 'Last modification time',
		'note' => 'Date the gedcoms were last updated.',
	),
	"gedcom_list_user" => array(
		'label' => 'Creator',
		'note' => 'The creator of a particular gedcom.',
	),
	"gedcom_list_posts" => array(
		'label' => 'Posts',
		'note' => 'Number of entries submitted to any given gedcom.',
	),
	"gedcom_list_visits" => array(
		'label' => 'Visits',
		'note' => 'Number of times a given gedcom has been visited.',
	),
	"gedcom_list_activity" => array(
		'label' => 'Activity',
		'note' => 'This number is an indication of how active a given gedcom is.',
	),
);
$gBitSmarty->assign( 'formGedcomLists',$formGedcomLists );


// this function only exists if it's been included by the index.php page. if
// it's been included from anywhere else, we don't execute this section
if( function_exists( 'simple_set_value' ) && $gBitUser->isAdmin() ) {
	if (!empty( $_REQUEST['gedcomTabSubmit'] ) ) {
		simple_set_value( 'pgv_calendar_format', PHPGEDVIEW_PKG_NAME );
		simple_set_value( 'pgv_default_pedigree_generations', PHPGEDVIEW_PKG_NAME );
		simple_set_value( 'pgv_max_pedigree_generations', PHPGEDVIEW_PKG_NAME );
		simple_set_value( 'pgv_max_descendancy_generations', PHPGEDVIEW_PKG_NAME );
		simple_set_toggle( 'pgv_use_RIN' );
	}
	if (!empty( $_REQUEST['gedcomPrefixSubmit'] ) ) {
		foreach( $gedcomPrefixValues as $item => $data ) {
			simple_set_value( $item, PHPGEDVIEW_PKG_NAME );
		}
	}
	if (!empty( $_REQUEST['featuresTabSubmit'] ) ) {
		foreach( $phpgedviewValues as $item ) {
			simple_set_value( $item, PHPGEDVIEW_PKG_NAME );
		}
	}
	if (!empty( $_REQUEST['listTabSubmit'] ) ) {
		simple_set_value( 'gedcom_list_order', PHPGEDVIEW_PKG_NAME );
		foreach( $formGedcomLists as $item => $data ) {
			simple_set_toggle( $item );
		}
	}
}
?>

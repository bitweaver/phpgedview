<?php
  /**
 * Individual Page
 *
 * Display all of the information about an individual
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
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id: individual.php,v 1.13 2010/02/08 21:27:24 wjames5 Exp $
 */

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'includes/bitsession.php' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM( 1 );

// leave manual config until we can move it to bitweaver table 
require_once 'includes/controllers/individual_ctrl.php';
require_once 'includes/bit_print.php';

$controller=new IndividualController();
$controller->init();

global $USE_THUMBS_MAIN, $mediacnt, $tabno;
global $linkToID;
global $SEARCH_SPIDER;

	global $factarray, $pgv_lang;
	global $INDI_FACTS_ADD;
	global $INDI_FACTS_UNIQUE;
	global $INDI_FACTS_QUICK;

$addfacts    = preg_split("/[, ;:]+/", $INDI_FACTS_ADD,    -1, PREG_SPLIT_NO_EMPTY);
$uniquefacts = preg_split("/[, ;:]+/", $INDI_FACTS_UNIQUE, -1, PREG_SPLIT_NO_EMPTY);
$quickfacts  = preg_split("/[, ;:]+/", $INDI_FACTS_QUICK,  -1, PREG_SPLIT_NO_EMPTY);

$addfacts=array_merge( CheckFactUnique( $uniquefacts, $quickfacts, "INDI" ), $addfacts );
$quickfacts=array_intersect($quickfacts, $addfacts);

usort($addfacts, "factsort");
$gBitSmarty->assign_by_ref( "addfacts", $addfacts);
$gBitSmarty->assign_by_ref( "factarray", $factarray);

$globalfacts = $controller->getGlobalFacts();
$indifacts = $controller->getIndiFacts();
$otherfacts = $controller->getOtherFacts();
$family = $controller->indi->getSpouseFamilies();
$families = $controller->indi->getChildFamilies();
$stepfams = $controller->indi->getStepFamilies();

$gBitSmarty->assign_by_ref( "globalfacts", $globalfacts->globalfacts);
$gBitSmarty->assign_by_ref( "indifacts", $indifacts->indifacts);
$gBitSmarty->assign_by_ref( "otherfacts", $otherfacts->otherfacts);
$gBitSmarty->assign_by_ref( "family", $family);
$gBitSmarty->assign_by_ref( "families", $families);
$gBitSmarty->assign_by_ref( "stepfams", $stepfams);
$linkToID = $controller->pid;	// -- Tell addmedia.php what to link to
$gBitSmarty->assign_by_ref( "controller", $controller);

$doctitle = "Individual Summary : ".$controller->indi->GetFullName();
$gBitSmarty->assign( "pagetitle", $doctitle );
$gBitSystem->display( 'bitpackage:phpgedview/individual.tpl', tra( 'Individual Summary' ) );
?>
<?php
/**
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @version $Id: functions.sl.php,v 1.1 2009/08/03 20:10:42 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_SL_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Localise a date. Lokalizacja datumov
////////////////////////////////////////////////////////////////////////////////
function date_localisation_sl(&$q1, &$d1, &$q2, &$d2, &$q3) {
	global $pgv_lang;
	static $NOMINATIVE_MONTHS=NULL;
	static $GENITIVE_MONTHS=NULL;
	static $INSTRUMENTAL_MONTHS=NULL;
	static $LOCATIVE_MONTHS=NULL;

	if (empty($NOMINATIVE_MONTHS)) {
		$NOMINATIVE_MONTHS=array($pgv_lang['jan'], $pgv_lang['feb'], $pgv_lang['mar'], $pgv_lang['apr'], $pgv_lang['may'], $pgv_lang['jun'], $pgv_lang['jul'], $pgv_lang['aug'], $pgv_lang['sep'], $pgv_lang['oct'], $pgv_lang['nov'], $pgv_lang['dec']);
		$GENITIVE_MONTHS=array('januarja', 'februarja', 'marca', 'aprila', 'maja', 'junija', 'julija', 'avgusta', 'septembra', 'oktobra', 'novembra', 'decembra');
		$INSTRUMENTAL_MONTHS=array('januarjem', 'feruarjem', 'marcem', 'aprilom', 'majem', 'julijem', 'junijem', 'avgustom', 'septembrom', 'oktobrom', 'novembrom', 'decembrom');
		$LOCATIVE_MONTHS=array('januarju', 'februarju', 'marcu', 'aprilu', 'maju', 'juniju', 'juliju', 'avgustu', 'septembru', 'oktobru', 'novembru', 'decembru');
	}

	// Months with a day number are genitive, regardless of qualifier
	for ($i=0; $i<12; ++$i) {
		$d1=preg_replace("/(\d+\. ){$NOMINATIVE_MONTHS[$i]}/", "$1{$GENITIVE_MONTHS[$i]}", $d1);
		$d2=preg_replace("/(\d+\. ){$NOMINATIVE_MONTHS[$i]}/", "$1{$GENITIVE_MONTHS[$i]}", $d2);
	}

	// Months without a day number (i.e. month at start) depend on the qualifier
	switch ($q1) {
	case 'from': case 'to': case 'abt': case 'apx': case 'cir':
		for ($i=0; $i<12; ++$i)
			$d1=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $GENITIVE_MONTHS[$i], $d1);
		break;
	case 'bet': case 'bef':
		for ($i=0; $i<12; ++$i)
			$d1=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $INSTRUMENTAL_MONTHS[$i], $d1);
		break;
	case 'aft':
		for ($i=0; $i<12; ++$i)
			$d1=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $LOCATIVE_MONTHS[$i], $d1);
	 	break;
	}
	switch ($q2) {
	case 'to':
		for ($i=0; $i<12; ++$i)
			$d2=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $GENITIVE_MONTHS[$i], $d2);
		break;
	case 'and':
		for ($i=0; $i<12; ++$i)
			$d2=preg_replace("/^{$NOMINATIVE_MONTHS[$i]}/", $INSTRUMENTAL_MONTHS[$i], $d2);
		break;
	}

	// The qualifiers are simple translations
	if (isset($pgv_lang[$q1]))
		$q1=$pgv_lang[$q1];
	if (isset($pgv_lang[$q2]))
		$q2=$pgv_lang[$q2];
}

////////////////////////////////////////////////////////////////////////////////
// Localise an age. Lokalizacja starosti.
////////////////////////////////////////////////////////////////////////////////
function age_localisation_sl(&$agestring, &$show_years) {
	global $pgv_lang;

	$show_years=true;
	$agestring=preg_replace(
		array(
			'/\bchi(ld)?\b/i',
			'/\binf(ant)?\b/i',
			'/\bsti(llborn)?\b/i',
			'/\b1y/i',
			'/\b2y/i','/\b3y/i','/\b4y/i',
			'/\b101y/i','/\b102y/i','/\b103y/i',
			'/\b104y/i',
			'/(\d+)y/i',
			'/\b1m/i',
			'/\b2m/i','/\b3m/i','/\b4m/i',
			'/(\d+)m/i',
			'/\b1d/i',
			'/(\d+)d/i'
		),
		array(
			$pgv_lang['child'],
			$pgv_lang['infant'],
	 		$pgv_lang['stillborn'],
			$show_years ? '1 '.$pgv_lang['year1'] : '1',
			$show_years ? '2 '."leti" : '2', $show_years ? '3 '."leta" : '3', $show_years ? '4 '."leta" : '4',
			$show_years ? '101 '."leto" : '101', $show_years ? '102 '."leti" : '102', $show_years ? '103 '."leta" : '103',
			$show_years ? '104 '."leta" : '104',
			$show_years ? '$1 '.$pgv_lang['years'] : '$1',
			'1 '.$pgv_lang['month1'],
			'2 '."meseca", '3 '."mesece", '4 '."mesece",
	 		'$1 '.$pgv_lang['months'],
			'1 '.$pgv_lang['day1'],
			'$1 '.$pgv_lang['days']
		),
		$agestring
	);
}
////////////////////////////////////////////////////////////////////////////////
// Localise a date differences. Lokalizacja razlik datumov.
////////////////////////////////////////////////////////////////////////////////
function date_diff_localisation_sl(&$label, &$gap) {
	global $pgv_lang;

	$yrs = round($gap/12);
	if ($gap == 12 || $gap == -12) $label .= $yrs." ".$pgv_lang["year1"]; // 1 leto
	else if (($yrs == 2 ) || ($yrs == -2 )) $label .= $yrs." leti"; // 2 leti
	else if (($yrs > 2 && $yrs < 5) || ($yrs < -2 && $yrs > -5)) $label .= $yrs." leta"; // +- 3 in 4 leta
	else if ($yrs > 4 or $yrs < -4) $label .= $yrs." ".$pgv_lang["years"]; // x let
	else if ($gap == 1 || $gap == -1) $label .= $gap." ".$pgv_lang["month1"]; // 1 meses
	else if (($gap == 2 ) || ($gap == -2 )) $label .= $gap." meseca"; // 2 meseca
	else if (($gap > 2 && $gap < 5) || ($gap < -2 && $gap > -5)) $label .= $gap." mesece"; // 3-4 mesece
	else if ($gap != 0) $label .= $gap." ".$pgv_lang["months"]; // x mesecev
}
////////////////////////////////////////////////////////////////////////////////
// Localise a number of people. Lokalizacija števila oseb. //Glej  lifespan.php
////////////////////////////////////////////////////////////////////////////////
function num_people_localisation_sl(&$count) {
	global $pgv_lang;

	if ($count == 1)
		print "<br /><b>".$count." ".$pgv_lang["individual"]."</b>"; // 1 oseba
	else if ($count == 2)
		print "<br /><b>".$count." osebi</b>"; // 2 osebi
	else if ($count > 2 && $count < 5)
		print "<br /><b>".$count." osebe</b>"; // 3-4 osebe
	else
		print "<br /><b>".$count." ".$pgv_lang["stat_individuals"]."</b>"; // x oseb
}
///////////////////////////////////////////////////////////////////////////////////////////
// Localise the _AKAN, _AKA, ALIA and _INTE facts. Lokalizacja dejstev _AKAN, _AKA, ALIA i _INTE.
///////////////////////////////////////////////////////////////////////////////////////////
function fact_AKA_localisation_sl(&$fact, &$pid) {
	global $factarray;

	$person = Person::getInstance($pid);
	$sex = $person->getSex();
	if ($fact == "_INTE") {
		if ($sex == "M")      $factarray[$fact] = "Pokopan"; // moški
		else if ($sex == "F") $factarray[$fact] = "Pokopana"; // ženska
	}
	else {
		if ($sex == "M")      $factarray[$fact] = "Znan tudi kot"; // moški
		else if ($sex == "F") $factarray[$fact] = "Znana tudi kot "; // ženska
	}
}
///////////////////////////////////////////////////////////////////////////////////////////
// Localise the _NMR facts. Lokalizacja dejstev _NMR.
///////////////////////////////////////////////////////////////////////////////////////////
function fact_NMR_localisation_sl($fact, &$fid) {
	global $factarray;

	$family = Family::getInstance($fid);
	$husb = $family->getHusband();
	$wife = $family->getWife();
	if ($fact == "_NMR") {
		if (empty($wife) && !empty($husb))	$factarray[$fact] = "Samski"; // moški
		else if (empty($husb) && !empty($wife))	$factarray[$fact] = "Samska"; // ženska
	}
	else if ($fact == "_NMAR") {
		if (empty($wife) && !empty($husb))	$factarray[$fact] = "Nikoli poroèen"; // moški
		else if (empty($husb) && !empty($wife))	$factarray[$fact] = "Nikoli poroèena"; // ženska
	}
}


////////////////////////////////////////////////////////////////////////////////
// Localise the relationships. Lokalizacja pokrewieñstwa.
////////////////////////////////////////////////////////////////////////////////
function rela_localisation_sl(&$rela) {

	return " ".ucfirst($rela).": ";
}
?>

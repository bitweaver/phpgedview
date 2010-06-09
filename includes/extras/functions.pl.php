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
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_PL_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Localise a date. Lokalizacja daty.
////////////////////////////////////////////////////////////////////////////////
function date_localisation_pl(&$q1, &$d1, &$q2, &$d2, &$q3) {
	global $pgv_lang;
	static $NOMINATIVE_MONTHS=NULL;
	static $GENITIVE_MONTHS=NULL;
	static $INSTRUMENTAL_MONTHS=NULL;
	static $LOCATIVE_MONTHS=NULL;

	if (empty($NOMINATIVE_MONTHS)) {
		$NOMINATIVE_MONTHS=array($pgv_lang['jan'], $pgv_lang['feb'], $pgv_lang['mar'], $pgv_lang['apr'], $pgv_lang['may'], $pgv_lang['jun'], $pgv_lang['jul'], $pgv_lang['aug'], $pgv_lang['sep'], $pgv_lang['oct'], $pgv_lang['nov'], $pgv_lang['dec']);
		$GENITIVE_MONTHS=array('stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia');
		$INSTRUMENTAL_MONTHS=array('styczniem', 'lutym', 'marcem', 'kwietniem', 'majem', 'czerwcem', 'lipcem', 'sierpniem', 'wrześniem', 'październikiem', 'listopadem', 'grudniem');
		$LOCATIVE_MONTHS=array('styczniu', 'lutym', 'marcu', 'kwietniu', 'maju', 'czerwcu', 'lipcu', 'sierpniu', 'wrześniu', 'październiku', 'listopadzie', 'grudniu');
	}

	// Months with a day number are genitive, regardless of qualifier
	for ($i=0; $i<12; ++$i) {
		$d1=preg_replace("/(\d+ ){$NOMINATIVE_MONTHS[$i]}/", "$1{$GENITIVE_MONTHS[$i]}", $d1);
		$d2=preg_replace("/(\d+ ){$NOMINATIVE_MONTHS[$i]}/", "$1{$GENITIVE_MONTHS[$i]}", $d2);
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
// Localise an age. Lokalizacja wieku.
////////////////////////////////////////////////////////////////////////////////
function age_localisation_pl(&$agestring, &$show_years) {
	global $pgv_lang;

	$show_years=true;
	$agestring=preg_replace(
		array(
			'/\bchi(ld)?\b/i',
			'/\binf(ant)?\b/i',
			'/\bsti(llborn)?\b/i',
			'/\b1y/i',
			'/\b2y/i','/\b3y/i','/\b4y/i',
			'/\b22y/i','/\b23y/i','/\b24y/i',
			'/\b32y/i','/\b33y/i','/\b34y/i',
			'/\b42y/i','/\b43y/i','/\b44y/i',
			'/\b52y/i','/\b53y/i','/\b54y/i',
			'/\b62y/i','/\b63y/i','/\b64y/i',
			'/\b72y/i','/\b73y/i','/\b74y/i',
			'/\b82y/i','/\b83y/i','/\b84y/i',
			'/\b92y/i','/\b93y/i','/\b94y/i',
			'/\b102y/i','/\b103y/i','/\b104y/i',
			'/\b122y/i','/\b123y/i','/\b124y/i',
			'/\b132y/i','/\b133y/i','/\b134y/i',
			'/(\d+)y/i',
			'/\b1m/i',
			'/\b2m/i','/\b3m/i','/\b4m/i',
			'/\b22m/i','/\b23m/i','/\b24m/i',
			'/(\d+)m/i',
			'/\b1d/i',
			'/(\d+)d/i',
			'/\b1w/i',
			'/\b2w/i','/\b3w/i','/\b4w/i',
			'/\b22w/i','/\b23w/i','/\b24w/i',
			'/(\d+)w/i'
		),
		array(
			$pgv_lang['child'],
			$pgv_lang['infant'],
	 		$pgv_lang['stillborn'],
			$show_years ? '1 '.$pgv_lang['year1'] : '1',
			$show_years ? '2 '.$pgv_lang["years2"] : '2', $show_years ? '3 '.$pgv_lang["years2"] : '3', $show_years ? '4 '.$pgv_lang["years2"] : '4',
			$show_years ? '22 '.$pgv_lang["years2"] : '22', $show_years ? '23 '.$pgv_lang["years2"] : '23', $show_years ? '24 '.$pgv_lang["years2"] : '24',
			$show_years ? '32 '.$pgv_lang["years2"] : '32', $show_years ? '33 '.$pgv_lang["years2"] : '33', $show_years ? '34 '.$pgv_lang["years2"] : '34',
			$show_years ? '42 '.$pgv_lang["years2"] : '42', $show_years ? '43 '.$pgv_lang["years2"] : '43', $show_years ? '44 '.$pgv_lang["years2"] : '44',
			$show_years ? '52 '.$pgv_lang["years2"] : '52', $show_years ? '53 '.$pgv_lang["years2"] : '53', $show_years ? '54 '.$pgv_lang["years2"] : '54',
			$show_years ? '62 '.$pgv_lang["years2"] : '62', $show_years ? '63 '.$pgv_lang["years2"] : '63', $show_years ? '64 '.$pgv_lang["years2"] : '64',
			$show_years ? '72 '.$pgv_lang["years2"] : '72', $show_years ? '73 '.$pgv_lang["years2"] : '73', $show_years ? '74 '.$pgv_lang["years2"] : '74',
			$show_years ? '82 '.$pgv_lang["years2"] : '82', $show_years ? '83 '.$pgv_lang["years2"] : '83', $show_years ? '84 '.$pgv_lang["years2"] : '84',
			$show_years ? '92 '.$pgv_lang["years2"] : '92', $show_years ? '93 '.$pgv_lang["years2"] : '93', $show_years ? '94 '.$pgv_lang["years2"] : '94',
			$show_years ? '102 '.$pgv_lang["years2"] : '102', $show_years ? '103 '.$pgv_lang["years2"] : '103', $show_years ? '104 '.$pgv_lang["years2"] : '104',
			$show_years ? '122 '.$pgv_lang["years2"] : '122', $show_years ? '123 '.$pgv_lang["years2"] : '123', $show_years ? '124 '.$pgv_lang["years2"] : '124',
			$show_years ? '132 '.$pgv_lang["years2"] : '132', $show_years ? '133 '.$pgv_lang["years2"] : '133', $show_years ? '134 '.$pgv_lang["years2"] : '134',
			$show_years ? '$1 '.$pgv_lang['years'] : '$1',
			'1 '.$pgv_lang['month1'],
			'2 miesiące', '3 miesiące', '4 miesiące',
			'22 miesiące', '23 miesiące', '24 miesiące',
	 		'$1 '.$pgv_lang['months'],
			'1 '.$pgv_lang['day1'],
			'$1 '.$pgv_lang['days'],
			'1 '.$pgv_lang['week1'],
			'2 tygodnie', '3 tygodnie', '4 tygodnie',
			'22 tygodnie', '23 tygodnie', '24 tygodnie',
			'$1 '.$pgv_lang['weeks']
		),
		$agestring
	);
}
function age2_localisation_pl($years) {
	global $pgv_lang;

	if ($years==1) $years .= " ".$pgv_lang["year1"];
	else if ($years > 1 && $years < 5) $years .= " ".$pgv_lang["years2"];
	else if (substr($years, -1, 1) > 1 && substr($years, -1, 1) < 5 && substr($years, -2, 1) != 1) $years .= " ".$pgv_lang["years2"];
	else $years .= " ".$pgv_lang["years"];
	return $years;
}
////////////////////////////////////////////////////////////////////////////////
// Localise a date differences. Lokalizacja różnic dat.
////////////////////////////////////////////////////////////////////////////////
function date_diff_localisation_pl(&$label, &$gap) {
	global $pgv_lang;

	$yrs = round($gap/12);
	if ($gap == 12 || $gap == -12) $label .= $yrs." ".$pgv_lang["year1"]; // 1 rok
	else if (($yrs > 1 && $yrs < 5) || ($yrs < -1 && $yrs > -5)) $label .= $yrs." ".$pgv_lang["years2"]; // 2-4 lata
	else if (($yrs > 21 || $yrs < -21) && substr($yrs, -1, 1) > 1 && substr($yrs, -1, 1) < 5 && substr($yrs, -2, 1) != 1) $label .= $yrs." ".$pgv_lang["years2"];
	else if ($gap > 20 or $gap < -20) $label .= $yrs." ".$pgv_lang["years"]; // x lat
	else if ($gap == 1 || $gap == -1) $label .= $gap." ".$pgv_lang["month1"]; // 1 miesiąc
	else if (($gap > 1 && $gap < 5) || ($gap < -1 && $gap > -5)) $label .= $gap." miesiące"; // 2-4 miesiące
	else if ($gap != 0) $label .= $gap." ".$pgv_lang["months"]; // x miesięcy
}
////////////////////////////////////////////////////////////////////////////////
// Localise a number of people. Lokalizacja liczby osób.
////////////////////////////////////////////////////////////////////////////////
function num_people_localisation_pl(&$count) {
	global $pgv_lang;

	if ($count == 1)
		print "<br /><b>".$count." ".$pgv_lang["individual"]."</b>"; // 1 osoba
	else if ($count > 1 && $count < 5)
		print "<br /><b>".$count." ".$pgv_lang["individuals"]."</b>"; // 2-4 osoby
	else if ($count > 21 && substr($count, -1, 1) > 1 && substr($count, -1, 1) < 5 && substr($count, -2, 1) != 1)
		print "<br /><b>".$count." ".$pgv_lang["individuals"]."</b>"; // x2-x4 osoby
	else
		print "<br /><b>".$count." ".$pgv_lang["stat_individuals"]."</b>"; // x osób
}
///////////////////////////////////////////////////////////////////////////////////////////
// Localise the _AKAN, _AKA, ALIA and _INTE facts. Lokalizacja faktów _AKAN, _AKA, ALIA i _INTE.
///////////////////////////////////////////////////////////////////////////////////////////
function fact_AKA_localisation_pl(&$fact, &$pid) {
	global $factarray;

	$person = Person::getInstance($pid);
	$sex = $person->getSex();
	if ($fact == "_INTE") {
		if ($sex == "M")	  $factarray[$fact] = "Pochowany"; // mężczyzna
		else if ($sex == "F") $factarray[$fact] = "Pochowana"; // kobieta
	}
	else {
		if ($sex == "M")	  $factarray[$fact] = "Znany także jako"; // mężczyzna
		else if ($sex == "F") $factarray[$fact] = "Znana także jako"; // kobieta
	}
}
///////////////////////////////////////////////////////////////////////////////////////////
// Localise the _NMR facts. Lokalizacja faktów _NMR.
///////////////////////////////////////////////////////////////////////////////////////////
function fact_NMR_localisation_pl($fact, &$fid) {
	global $factarray;

	$family = Family::getInstance($fid);
	$husb = $family->getHusband();
	$wife = $family->getWife();
	if ($fact == "_NMR") {
		if (empty($wife) && !empty($husb))		$factarray[$fact] = "Nieżonaty"; // mężczyzna
		else if (empty($husb) && !empty($wife))	$factarray[$fact] = "Niezamężna"; // kobieta
	}
	else if ($fact == "_NMAR") {
		if (empty($wife) && !empty($husb))		$factarray[$fact] = "Nigdy nieżonaty"; // mężczyzna
		else if (empty($husb) && !empty($wife))	$factarray[$fact] = "Nigdy niezamężna"; // kobieta
	}
}
///////////////////////////////////////////////////////////////////////////////////////////
// Localise the AGNC fact. Lokalizacja faktu AGNC.
///////////////////////////////////////////////////////////////////////////////////////////
function fact_AGNC_localisation_pl($fact, $main_fact) {
	global $factarray;

	if ($main_fact == "EDUC") {
		$factarray[$fact] = "Szkoła/uczelnia";
	}
	else if ($main_fact == "OCCU") {
		$factarray[$fact] = "Miejsce pracy";
	}
	else if ($main_fact == "ORDN") {
		$factarray[$fact] = "Seminarium duchowne";
	}
}
////////////////////////////////////////////////////////////////////////////////
// Localise the close relatives facts. Lokalizacja faktów dotyczących bliskich
////////////////////////////////////////////////////////////////////////////////
function cr_facts_localisation_pl(&$factrec, &$fact, &$explode_fact, &$pid) {
	global $factarray;

	$ct = preg_match_all("/\d ASSO @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	if ($ct>0) $pid2 = $match[0][1];
	if (isset($pid2)) {
		$sex1 = Person::getInstance($pid)->getSex();
		$sex2 = Person::getInstance($pid2)->getSex();

		if ($explode_fact[1] == "BIRT") {
			switch ($explode_fact[2]) {
			case "SIBL":
				if ($sex2 == "M")		$factarray[$fact] = "Narodziny brata";
				else if ($sex2 == "F")  $factarray[$fact] = "Narodziny siostry";
				break;
			case "GCHI":
				if ($sex2 == "M")		$factarray[$fact] = "Narodziny wnuka";
				else if ($sex2 == "F")  $factarray[$fact] = "Narodziny wnuczki";
				break;
			case "GGCH":
				if ($sex2 == "M")		$factarray[$fact] = "Narodziny prawnuka";
				else if ($sex2 == "F")  $factarray[$fact] = "Narodziny prawnuczki";
				break;
			case "COUS":
				if ($sex2 == "M")		$factarray[$fact] = "Narodziny kuzyna";
				else if ($sex2 == "F")  $factarray[$fact] = "Narodziny kuzynki";
				break;
			case "FSIB":
				if ($sex2 == "M")		$factarray[$fact] = "Narodziny brata ojca";
				else if ($sex2 == "F")  $factarray[$fact] = "Narodziny siostry ojca";
				break;
			case "MSIB":
				if ($sex2 == "M")		$factarray[$fact] = "Narodziny brata matki";
				else if ($sex2 == "F")  $factarray[$fact] = "Narodziny siostry matki";
				break;
			case "NEPH":
				$node = get_relationship($pid, $pid2);
				if (isset($node["path"][1])) {
					$sex3 = Person::getInstance($node["path"][1])->getSex();
					if ($sex2 == "M") {
						if ($sex3 == "M")		$factarray[$fact] = "Narodziny bratanka";
						else if ($sex3 == "F")	$factarray[$fact] = "Narodziny siostrzeńca";
					}
					else if ($sex2 == "F") {
						if ($sex3 == "M")		$factarray[$fact] = "Narodziny bratanicy";
						else if ($sex3 == "F")	$factarray[$fact] = "Narodziny siostrzenicy";
					}
				}
				break;
			}
		}
		else if ($explode_fact[1] != "") {
			switch ($explode_fact[2]) {
			case "SIBL":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." brata";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." siostry";
				break;
			case "GCHI":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." wnuka";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." wnuczki";
				break;
			case "GGCH":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." prawnuka";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." prawnuczki";
				break;
			case "GPAR":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." dziadka";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." babci";
				break;
			case "GGPA":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." pradziadka";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." prababci";
				break;
			case "COUS":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." kuzyna";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." kuzynki";
				break;
			case "FSIB":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." brata ojca";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." siostry ojca";
				break;
			case "MSIB":
				if ($sex2 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." brata matki";
				else if ($sex2 == "F")  $factarray[$fact] = $factarray[$explode_fact[1]]." siostry matki";
				break;
			case "SPOU":
				if ($sex2 == "M"  || $sex1 == "F") 		$factarray[$fact] = $factarray[$explode_fact[1]]." męża";
				else if ($sex2 == "F"  || $sex1 == "M") $factarray[$fact] = $factarray[$explode_fact[1]]." żony";
				break;
			case "NEPH":
				$node = get_relationship($pid, $pid2);
				if (isset($node["path"][1])) {
					$sex3 = Person::getInstance($node["path"][1])->getSex();
					if ($sex2 == "M") {
						if ($sex3 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." bratanka";
						else if ($sex3 == "F")	$factarray[$fact] = $factarray[$explode_fact[1]]." siostrzeńca";
					}
					else if ($sex2 == "F") {
						if ($sex3 == "M")		$factarray[$fact] = $factarray[$explode_fact[1]]." bratanicy";
						else if ($sex3 == "F")	$factarray[$fact] = $factarray[$explode_fact[1]]." siostrzenicy";
					}
				}
				break;
			}
		}
	}
}
////////////////////////////////////////////////////////////////////////////////
// Localise the relationships. Lokalizacja pokrewieństwa.
////////////////////////////////////////////////////////////////////////////////
function rela_localisation_pl(&$rela, &$pid2) {
	global $pgv_lang;

	if (isset($pid2)) {
		$record = Person::getInstance($pid2);
		if (!empty($record)) {
			$sex = $record->getSex();
			switch ($rela) {
				case $pgv_lang["informant"]:
					if ($sex == "M")		$rela = "Informator";
					else if ($sex == "F")   $rela = "Informatorka";
					break;
				case $pgv_lang["lodger"]:
					if ($sex == "M")		$rela = "Lokator";
					else if ($sex == "F")   $rela = "Lokatorka";
					break;
				case $pgv_lang["slave"]:
					if ($sex == "M")		$rela = "Niewolnik";
					else if ($sex == "F")   $rela = "Niewolnica";
					break;
				case $pgv_lang["attending"]:
					if ($sex == "M")		$rela = "Obsługujący";
					else if ($sex == "F")   $rela = "Obsługująca";
					break;
				case $pgv_lang["guardian"]:
					if ($sex == "M")		$rela = "Opiekun";
					else if ($sex == "F")   $rela = "Opiekunka";
					break;
				case $pgv_lang["nurse"]:
					if ($sex == "M")		$rela = "Pielęgniarz";
					else if ($sex == "F")   $rela = "Pielęgniarka";
					break;
				case $pgv_lang["attendant"]:
					if ($sex == "M")		$rela = "Pomocnik";
					else if ($sex == "F")   $rela = "Pomocnica";
					break;
				case $pgv_lang["employee"]:
					if ($sex == "M")		$rela = "Pracownik";
					else if ($sex == "F")   $rela = "Pracownica";
					break;
				case $pgv_lang["friend"]:
					if ($sex == "M")		$rela = "Przyjaciel";
					else if ($sex == "F")   $rela = "Przyjaciółka";
					break;
				case $pgv_lang["servant"]:
					if ($sex == "M")		$rela = "Służący";
					else if ($sex == "F")   $rela = "Służąca";
					break;
				case $pgv_lang["seller"]:
					if ($sex == "M")		$rela = "Sprzedawca";
					else if ($sex == "F")   $rela = "Sprzedawczyni";
					break;
				case $pgv_lang["owner"]:
					if ($sex == "M")		$rela = "Właściciel";
					else if ($sex == "F")   $rela = "Właścicielka";
					break;
				case $pgv_lang["ward"]:
					if ($sex == "M")		$rela = "Wychowanek";
					else if ($sex == "F")   $rela = "Wychowanka";
					break;
			}
		}
	}
	
	return " ".ucfirst($rela).": ";
}

function getRelationshipText_pl($relationshipDescription, $node, $pid1, $pid2) {
	if ($relationshipDescription != false) {
		return UTF8_strtolower($relationshipDescription);
	}
	return false;
}

//-- functions to calculate polish specific genitive names
function getFirstRelationsName_pl($pid) {
	// In Polish we want the genitive form of the name
	$person=Person::getInstance($pid);
	if ($person) {
		$fname=$person->getFullName();
	}
	else {
		$fname='';
	}
	return $fname;
	
	// tested
	$pname='';
	$sex = Person::getInstance($pid)->getSex();
	if ($sex == "M") {
		$names = explode(" ", $fname);
		foreach ($names as $name) {
			if (preg_match('/ski$/', $name)) {
				$pname .= " ".preg_replace('/ski$/', 'skiego', $name);
			}
			else if (preg_match('/cki$/', $name)) {
				$pname .= " ".preg_replace('/cki$/', 'ckiego', $name);
			}
			else if (preg_match('/dzki$/', $name)) {
				$pname .= " ".preg_replace('/dzki$/', 'dzkiego', $name);
			}
			else if (preg_match('/żki$/', $name)) {
				$pname .= " ".preg_replace('/żki$/', 'żkiego', $name);
			}
			else if (preg_match('/y$/', $name)) {
				$pname .= " ".preg_replace('/y$/','ego', $name);
			}
			else if (preg_match('/i$/', $name)) {
				$pname .= " ".preg_replace('/i$/','iego', $name);
			}
			else if (preg_match('/ek$/', $name)) {
				$pname .= " ".preg_replace('/ek$/','ka', $name);
			}
			else if (preg_match('/eł$/', $name)) {
				$pname .= " ".preg_replace('/eł$/','ła', $name);
			}
			else if (preg_match('/el$/', $name)) {
				$pname .= " ".preg_replace('/el$/','la', $name);
			}
			else if (preg_match('/ń$/', $name)) {
				$pname .= " ".preg_replace('/ń$/','nia', $name);
			}
			else if (preg_match('/ź$/', $name)) {
				$pname .= " ".preg_replace('/ź$/','zia', $name);
			}
			else if (preg_match('/niec$/', $name)) {
				$pname .= " ".preg_replace('/niec$/','ńca', $name);
			}
			else if (preg_match('/iec$/', $name)) {
				$pname .= " ".preg_replace('/iec$/','ca', $name);
			}
			else if (preg_match('/ec$/', $name)) {
				$pname .= " ".preg_replace('/ec$/','ca', $name);
			}
			else if (preg_match('/er$/', $name)) {
				$pname .= " ".preg_replace('/er$/','ra', $name);
			}
			// go
			else if (preg_match('/go$/', $name)) {
				$pname .= " ".preg_replace('/go$/','gi', $name);
			}
			// io
			else if (preg_match('/io$/', $name)) {
				$pname .= " ".preg_replace('/io$/','ii', $name);
			}
			// jo
			else if (preg_match('/jo$/', $name)) {
				$pname .= " ".preg_replace('/jo$/','ji', $name);
			}
			// ko
			else if (preg_match('/ko$/', $name)) {
				$pname .= " ".preg_replace('/ko$/','ki', $name);
			}
			// bo, co, do, fo, ho, lo, ło, mo, no, po, ro, so, to, wo, zo
			else if (preg_match('/o$/', $name)) {
				$pname .= " ".preg_replace('/o$/','y', $name);
			}
			// ga
			else if (preg_match('/ga$/', $name)) {
				$pname .= " ".preg_replace('/ga$/','gi', $name);
			}
			// ia
			else if (preg_match('/ia$/', $name)) {
				$pname .= " ".preg_replace('/ia$/','i', $name);
			}
			// ja
			else if (preg_match('/ja$/', $name)) {
				$pname .= " ".preg_replace('/ja$/','ji', $name);
			}
			// ka
			else if (preg_match('/ka$/', $name)) {
				$pname .= " ".preg_replace('/ka$/','ki', $name);
			}
			// ba, ca, da, fa, ha, la, ła, ma, na, pa, ra, sa, ta, wa, za
			else if (preg_match('/a$/', $name)) {
				$pname .= " ".preg_replace('/a$/','y', $name);
			}
			else if (preg_match('/ek]$/', $name)) {
				$pname .= " ".preg_replace('/ek]$/','ka]', $name);
			}
			else if (preg_match('/"$/', $name)) {
				$pname .= " ".preg_replace('/"$/','a"', $name);
			}
			else
				$pname .= " ".$name."a";
		}
	}
	else if ($sex == "F") {
		$names = explode(" ", $fname);
		foreach ($names as $name) {
			if (preg_match('/raska$/', $name)) {
				$pname .= " ".preg_replace('/ska$/', 'ski', $name);
			}
			else if (preg_match('/ska$/', $name)) {
				$pname .= " ".preg_replace('/ska$/', 'skiej', $name);
			}
			else if (preg_match('/cka$/', $name)) {
				$pname .= " ".preg_replace('/cka$/', 'ckiej', $name);
			}
			else if (preg_match('/dzka$/', $name)) {
				$pname .= " ".preg_replace('/dzka$/', 'dzkiej', $name);
			}
			else if (preg_match('/żka$/', $name)) {
				$pname .= " ".preg_replace('/żka$/', 'żkiej', $name);
			}
			else if (preg_match('/ska]$/', $name)) {
				$pname .= " ".preg_replace('/ska]$/', 'skiej]', $name);
			}
			else if (preg_match('/cka]$/', $name)) {
				$pname .= " ".preg_replace('/cka]$/', 'ckiej]', $name);
			}
			else if (preg_match('/dzka]$/', $name)) {
				$pname .= " ".preg_replace('/dzka]$/', 'dzkiej]', $name);
			}
			else if (preg_match('/żka]$/', $name)) {
				$pname .= " ".preg_replace('/żka]$/', 'żkiej]', $name);
			}
			else if (preg_match('/ka]$/', $name)) {
				$pname .= " ".preg_replace('/ka]$/', 'ki]', $name);
			}
			else if (preg_match('/a]$/', $name)) {
				$pname .= " ".preg_replace('/a]$/','y]', $name);
			}
			else if (preg_match('/ka"$/', $name)) {
				$pname .= " ".preg_replace('/ka"$/', 'ki"', $name);
			}
			else
				$pname .= " ".preg_replace(array('/eja$/','/ja$/','/ia$/','/la$/','/ga$/','/ea$/','/a$/'), array('ei','ji','ii','li','gi','ei','y'), $name);
		}
	}
	else {
		$pname = "osoby: ".$pname;
	}
	if (!empty($pname)) return trim($pname);
	else return $fname;
}

function century_localisation_pl($n, $show=true) {
	$arab = array(1, 4, 5, 9, 10);
	$roman = array("I", "IV", "V", "IX", "X");
	$roman_century = "";
	for ($i=4; $i>=0; $i--) {
		while ($n>=$arab[$i]) {
			$n-=$arab[$i];
			$roman_century .= $roman[$i];
		}
	}
	if ($show) return $roman_century." w.";
	else return $roman_century;
}
?>

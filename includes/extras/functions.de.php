<?php
/**
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2008  PGV Development Team
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
 * @version $Id: functions.de.php,v 1.1 2009/04/30 17:52:57 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_DE_PHP', '');

/*
 * The following routine is designed to produce text to describe the relationship
 * between two people.  It's a direct copy of the corresponding English routine, except
 * that the output is NOT converted to lower-case.
 */
function getRelationshipText_de($relationshipDescription, $node, $pid1, $pid2) {
	global $pgv_lang, $lang_short_cut, $LANGUAGE;
	$started = false;
	$finished = false;
	$numberOfSiblings = 0;
	$generationsOlder = 0;
	$generationsYounger = 0;
	$sosa = 1;
	$bosa = 1;
	$numberOfSpouses = 0;
	$lastRelationshipIsSpouse = false;

	// sanity check - helps to prevent the possibility of recursing too deeply
	if ($pid1 == $pid2) return false;

	foreach ($node["path"] as $index=>$pid) {
		// only start looking for relationships from the first pid passed in
		if ($pid == $pid1) {
			$started = true;
			continue;
		}

		if ($started) {
			$lastRelationshipIsSpouse = false;
			// look to see if we can find a relationship
			switch ($node["relations"][$index]) {
				case "self":
					break;

				case "sister":
				case "brother":
					$numberOfSiblings++;
					break;

				case "mother":
					$generationsOlder++;
					$sosa = $sosa * 2 + 1;
					break;

				case "father":
					$generationsOlder++;
					$sosa = $sosa * 2;
					break;

				case "son":
					$generationsYounger++;
					$bosa = $bosa * 2;
					break;

				case "daughter":
					$generationsYounger++;
					$bosa = $bosa * 2 + 1;
					break;

				case "husband":
				case "wife":
					$numberOfSpouses++;
					$lastRelationshipIsSpouse = true;
					break;
			}
		}

		if ($pid == $pid2) {
			// we have found the second individual - look no further
			$finished = true;
			break;
		}

	}
	// sanity check
	if (!$started || !$finished) {
		// passed in pid's are not found in the array!!!
		return false;
	}

	$person2 = find_person_record($_SESSION["pid2"]);
	$person1 = find_person_record($_SESSION["pid1"]);
	$mf = "NN";
	if (preg_match("/1 SEX F/", $person2, $smatch)>0) $mf="F";
	if (preg_match("/1 SEX M/", $person2, $smatch)>0) $mf="M";

	//checks for nth cousin n times removed
	if ($numberOfSpouses == 0 && $numberOfSiblings == 1 && $generationsYounger > 0 && $generationsOlder > 0 && ($generationsYounger != $generationsOlder)) {
		$degree = min($generationsOlder, $generationsYounger);

		if ($mf=="F") $relName = "female_cousin_" . $degree;
		else $relName = "male_cousin_" . $degree;

		if (isset($pgv_lang[$relName])) $relationshipDescription = $pgv_lang[$relName];

		if ($relationshipDescription != false) {
			$removed = $generationsOlder-$generationsYounger;
			if ($removed != 0) {
				// relationship description should already be set for the Nth cousin
				if ($removed > 0) $relRemoved = "removed_ascending_" . $removed;
				else $relRemoved = "removed_descending_" . -$removed;

				if (isset($pgv_lang[$relRemoved])) $relationshipDescription .= $pgv_lang[$relRemoved];
			}
		}
	}


	if ($relationshipDescription != false) {
		return $relationshipDescription;
	}
	return false;
}
?>

<?php
/**
 * File used to display Historical facts on individual page
 *
 * Each line is a GEDCOM style record to describe an event, including newline chars (\n)
 * File to be renamed : histo.xx.php where xx is language code
 * File included in : person_class.php
 *
 * $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access this file directly.";
	exit;
}

$histo[] = "1 EVEN\n2 TYPE History\n2 DATE 11 NOV 1918\n2 NOTE WW1 Armistice";
$histo[] = "1 EVEN\n2 TYPE History\n2 DATE 8 MAY 1945\n2 NOTE WW2 Armistice";
?>

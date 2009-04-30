<?php
/**
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2007 Greg Roach
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
 * @version $Id: functions.fr.php,v 1.1 2009/04/30 17:52:57 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_FR_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Create an ordinal suffix for a number.
////////////////////////////////////////////////////////////////////////////////
function ordinal_suffix_fr($n) {
	if ($n==1)
		return 'er';
	return 'éme';
}

////////////////////////////////////////////////////////////////////////////////
// Localise a date. "[qualifier] date [qualifier date] [qualifier]"
////////////////////////////////////////////////////////////////////////////////
function date_localisation_fr(&$q1, &$d1, &$q2, &$d2, &$q3) {
	global $pgv_lang;

	// Years in the french republican calendar are displayed in roman numerals.
	// They need a prefix of "an"
	$d1=preg_replace("/(\b[IVX]+$)/", "an $1", $d1);
	$d2=preg_replace("/(\b[IVX]+$)/", "an $1", $d2);

	if (isset($pgv_lang[$q1]))
		$q1=$pgv_lang[$q1];
	if (isset($pgv_lang[$q2]))
		$q2=$pgv_lang[$q2];
}

?>

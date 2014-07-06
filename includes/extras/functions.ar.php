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
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_AR_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Localise a date. "[qualifier] date [qualifier date] [qualifier]"
////////////////////////////////////////////////////////////////////////////////
function date_localisation_ar(&$q1, &$d1, &$q2, &$d2, &$q3) {
	global $pgv_lang;

	// Simple substitution of arabic digits
	$latin =array('0', '1', '2', '3', '4', '5', '6' ,'7', '8', '9' );
	$arabic=array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

	$d1=str_replace($latin, $arabic, $d1);
	$d2=str_replace($latin, $arabic, $d2);

	if (isset($pgv_lang[$q1]))
		$q1=$pgv_lang[$q1];
	if (isset($pgv_lang[$q2]))
		$q2=$pgv_lang[$q2];
}

?>

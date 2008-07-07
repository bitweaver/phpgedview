<?php
/**
 * Display changelog file with clickable bugs and RFEs
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @subpackage Admin
 * @version $Id$
 */

require('config.php');
if (!PGV_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=admin.php");
	exit;
}

$search = @$HTTP_GET_VARS["search"];
print "<title>PhpGedView : changelog (".htmlentities($search).")</title>\n";

$text = file_get_contents("changelog.txt");
$wait = @file_get_contents("changelog.local.txt");
$text = $wait.$text;

// disable HTML tags
$text = preg_replace("/</", "&lt;", $text);
$text = preg_replace("/>/", "&gt;", $text);

// highlight search text (caseless)
if (!empty($search)) {
	$text = preg_replace("/(.*)(?i)($search)(.*)\\n/", "<span style=\"background-color:#DADADA\">\\0</span>", $text);
	$text = preg_replace("/(?i)$search/", "<span style=\"background-color:Yellow\">\\0</span>", $text);
}

// add link to tracker
$text = preg_replace("/RFE(\d{6,7})/", "RFE \\1", $text);	// RFE1234567 ==> RFE 1234567
$text = preg_replace("/#(\d{6,7})/", "# \\1", $text);		// #1234567 ==> # 1234567
$text = preg_replace("/\[(\d{6,7})/", "[ \\1", $text);		// [1234567 ==> [ 1234567
$text = preg_replace("/(\d{6,7})\]/", "\\1 ]", $text);		// 1234567] ==> 1234567 ]
$text = preg_replace("/\((\d{6,7})/", "( \\1", $text);		// (1234567 ==> ( 1234567
$text = preg_replace("/(\d{6,7})\)/", "\\1 )", $text);		// 1234567) ==> 1234567 )
$text = preg_replace("/(\d{6,7})\,/", "\\1 ,", $text);		// 1234567, ==> 1234567 ,
$text = preg_replace("/ (\d{6,7}) /", " <a name=\\1 href=http://sourceforge.net/support/tracker.php?aid=\\1#innerframe>\\1</a> ", $text);

$text = preg_replace("/ \(([-\w]{4,13})\)\r\n/", " (<a name=\\1 href=?search=\\1>\\1</a>)\r\n", $text);
$text = preg_replace("/  /", " ", $text);

print "<pre>\n$text\n</pre>\n";
?>

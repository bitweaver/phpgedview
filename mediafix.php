<?php
/**
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * @version $Id: mediafix.php,v 1.1 2008/07/07 18:01:11 lsces Exp $
 */

require("config.php");
require("includes/functions_edit.php");

if (!PGV_USER_GEDCOM_ADMIN) {
	header('Location: index.php');
}

function fixmedia($oldrecord) {
	$newrec = "";
	$objelinks = array();
	$lines = preg_split("/[\r\n]+/", $oldrecord);
	for($i=0; $i<count($lines); $i++) {
		$line = $lines[$i];
		if (!empty($line)) {
			$mt = preg_match("/OBJE @(\w*)@/", $line, $match);
			if ($mt==0 || !isset($objelinks[$match[1]])) {
				$newrec .= $line."\r\n";
				if ($mt>0) $objelinks[$match[1]]=$i;
			}
			else {
				$level = $line{0};
				$sublevel = $level + 1;
				$oldi = $i;
				do {
					$i++;
					if (!empty($lines[$i])) {
						$line = $lines[$i];
						$sublevel = $line{0};
					}
				} while($sublevel>$level && $i<count($lines));
				if ($i!=$oldi && $i<count($lines)) $i--;//$newrec .= $line."\r\n";
			}
		}
	}
	$newrec = trim($newrec);
	return $newrec;
}

print_header('Fix Media Errors');

print "Finding media errors in individuals...";
$indis = search_indis("[1-9]+ OBJE @.*@");
print " Found ".count($indis)."<br /><br />\n";

foreach($indis as $pid=>$indi) {
	print "Checking record ".$pid."... ";
	
	$oldrecord = $indi['gedcom'];
	if (isset($pgv_changes[$pid."_".$GEDCOM])) $oldrecord = find_updated_record($pid);
	$newrec = fixmedia($oldrecord);
	if ($newrec!=trim($oldrecord)) {
		print "Fixing record ".$pid."<br />\n";
		replace_gedrec($pid, $newrec, false);
	}
	else print "No changes needed for record ".$pid."<br />\n";
}

print "<br /><br />Finding media errors in families...";
$indis = search_fams("[1-9]+ OBJE @.*@");
print " Found ".count($indis)."<br /><br />\n";

foreach($indis as $pid=>$indi) {
	print "Checking record ".$pid."... ";
	if (isset($pgv_changes[$pid."_".$GEDCOM])) $oldrecord = find_updated_record($pid);
	
	$newrec = fixmedia($oldrecord);
	if ($newrec!=trim($oldrecord)) {
		print "Fixing record ".$pid."<br />\n";
		replace_gedrec($pid, $newrec, false);
	}
	else print "No changes needed for record ".$pid."<br />\n";
}

print "<br /><br />Finding media errors in sources...";
$indis = search_sources("[1-9]+ OBJE @.*@");
print " Found ".count($indis)."<br /><br />\n";

foreach($indis as $pid=>$indi) {
	print "Checking record ".$pid."... ";
	if (isset($pgv_changes[$pid."_".$GEDCOM])) $oldrecord = find_updated_record($pid);
	
	$newrec = fixmedia($oldrecord);
	if ($newrec!=trim($oldrecord)) {
		print "Fixing record ".$pid."<br />\n";
		replace_gedrec($pid, $newrec, false);
	}
	else print "No changes needed for record ".$pid."<br />\n";
}

print "<br /><b>Updates completed</b><br />";
print_footer();
?>

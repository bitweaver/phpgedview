<?php
/**
 * GET data from a server file to populate a contextual place list
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * @subpackage Edit
 * @version $Id$
 * @see functions_places.php
 */

$localized=@$_GET["localized"];
$field=@$_GET["field"];
//print $field."|";
$ctry=@$_GET["ctry"];
$stae=@$_GET["stae"];
$cnty=@$_GET["cnty"];
$city=@$_GET["city"];
if (empty($ctry)) return;

$mapname="";
if (strpos($field, "PLAC_STAE")!==false) $mapname=$ctry;
if (strpos($field, "PLAC_CNTY")!==false) $mapname=$ctry."_".$stae;
if (strpos($field, "PLAC_CITY")!==false) $mapname=$ctry."_".$stae."_".$cnty;
//print $mapname."|";
if (empty($mapname)) return;
$data = "";
// user map file
$filename=$ctry."/".$ctry.".extra.htm";
$data .= @file_get_contents($filename);
// localized map file
$filename=$ctry."/".$ctry.".".$localized.".htm";
$data .= @file_get_contents($filename);
// default map file
$filename=$ctry."/".$ctry.".htm";
$data .= @file_get_contents($filename);
// remove HTML comments
$data = str_replace("\r", "",$data);
$data = preg_replace("/<!--.*?-->\n/is", "", $data);
// search <map id="..." ...>...</map>
$p = strpos($data, "<map id=\"".$mapname."\"");
// map not found : use txt file
if ($p === false) {
	$filename=$ctry."/".$mapname.".txt";
	$data = @file_get_contents($filename);
	$data = str_replace("\r", "",$data);
	$data = preg_replace("/<!--.*?-->\n/is", "", $data);
	$data = str_replace("\n", "|",$data);
	$data = trim($data,"|");
	print $data;
	exit;
}
$data = substr($data, $p);
$p = strpos($data, "</map>");
if ($p === false) return;
$data = substr($data, 0, $p);
// match : alt="text"
     if (strpos($field, "PLAC_STAE")!==false) $found = preg_match_all("/setPlaceState\('([^']+)'\)/", $data, $match, PREG_PATTERN_ORDER);
else if (strpos($field, "PLAC_CNTY")!==false) $found = preg_match_all("/setPlaceCounty\('([^']+)'\)/", $data, $match, PREG_PATTERN_ORDER);
else if (strpos($field, "PLAC_CITY")!==false) $found = preg_match_all("/setPlaceCity\('([^']+)'\)/", $data, $match, PREG_PATTERN_ORDER);
if (!$found) $found = preg_match_all('/alt="([^"]+)"/', $data, $match, PREG_PATTERN_ORDER);
if (!$found) return;
// sort results
$resu = $match[1];
sort($resu);
$resu = array_unique($resu);
// add separator
$data = "";
foreach ($resu as $k=>$v) if ($v!="default") $data.=$v."|";
//$data = str_replace("\n", "|",$data);
$data = trim($data,"|");
print $data;
?>

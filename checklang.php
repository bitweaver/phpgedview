<?php
/**
 * Check lang files
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @subpackage Languages
 * @version $Id: checklang.php,v 1.3 2006/10/02 12:48:41 lsces Exp $
 */
// -- include config file
require("config.php");
require( $factsfile["english"]);
if (file_exists( $factsfile[$LANGUAGE])) require  $factsfile[$LANGUAGE];

print_header("checklang");

// args
$lang=@$HTTP_GET_VARS["lang"];
if (!isset($lang)) $lang="";

// reading flags directory
$flags = array();
$rep = opendir('../users/icons/flags/');
while ($file = readdir($rep)) {
	if (stristr($file, ".gif") and $file!="new.gif" and $file!="en.gif") {
		$flags[] = substr($file, 0, strlen($file)-4);
	}
}

// show menu
print "<center>";
print "<h2>Check lang files</h2>";

echo <<< END1
This tool performs a check between english files and
<br />
selected language(s) files to help translators keeping files up-to-date.
<br />
The table shows <i>translated vars / english vars</i> ratio.
<br />
<span class="warning">Results files (*.NEW.php) have to be carefully verified before use.</span>
<br /><br />
END1;

print "<form action=\"?\">";
print "Select a lang code : <select name=\"lang\">";
print "<option value=\"\">Select...</option>";
print "<option value=\"all\">all</option>";
foreach ($flags as $indexval => $flag) {
	print "<option value=\"$flag\"";
	if ($flag==$lang) print " selected";
	print ">$flag</option>";
}
print "</select>";
print " <input type=\"submit\" value=\"Check\" />";
print "</form>";
print "<br />";
closedir($rep);
clearstatcache();

// show empty table
print "<table border=\"0\" class=\"facts_table\">";
//print "<table class=\"list_table $TEXT_DIRECTION\">";
print "<tr class=\"facts_label03\">";
print "<td width=\"20%\">lang</td>";
print "<td width=\"20%\">facts</td>";
print "<td width=\"20%\">lang</td>";
print "<td width=\"20%\">configure_help</td>";
print "<td width=\"20%\">help_text</td>";
print "</tr>";


if ($lang!="all") {
	unset($flags);
	if ($lang!="") $flags[] = $lang;
}
if (isset($flags)) foreach ($flags as $indexval => $flag) {
	print "<tr class=\"facts_label\"><td><img src=\"../users/icons/flags//$flag.gif\" width=\"32\" border=0 alt=\"$flag\" align=\"middle\" /> $flag</td>";
	print "<td id=\"$flag.f\" class=\"facts_value\">...</td>";
	print "<td id=\"$flag.l\" class=\"facts_value\">...</td>";
	print "<td id=\"$flag.c\" class=\"facts_value\">...</td>";
	print "<td id=\"$flag.h\" class=\"facts_value\">...</td>";
	print "</tr>";
}
print "</table>";
print "</center>";
print "<br /><br />";
print_footer();
flush();

// process each lang
$path = "languages";
//chdir($path);
if (isset($flags)) foreach ($flags as $indexval => $flag) {
	unset($target);
	checkfile("$path/facts.en.php");
	checkfile("$path/lang.en.php");
	checkfile("$path/configure_help.en.php");
	checkfile("$path/help_text.en.php");
}

exit;

function checkfile($filename) {
	global $flag;
	global $target;

	set_time_limit(0); //

	// loading source data
	if (!$fd = fopen($filename, 'r')) die("Cannot open $filename");
	while ($data = fgets($fd)) $source[] = $data;
	fclose($fd);

	// loading target data
	$filename = str_replace(".en.", ".$flag.", $filename);
	if (!$fd = fopen($filename, 'r')) print("Cannot open $filename");
	while ($data = @fgets($fd)) {
		if (substr($data, 0, 1) == "\$") $target[] = $data; //smart_utf8_decode($data);
	}
	@fclose($fd);
	$target[] = ""; // DO NOT DELETE THIS

	// output file
	$filename = str_replace(".php", ".NEW.php", $filename);
	if (!$fd = fopen($filename, 'w')) die("Cannot open $filename");

	// process source data
	$ok = 0;
	$nok = 0;
	foreach($source as $indexval => $english) {
		if (substr($english, 0, 1) == "\$") {
			// print "<br />$english";
			$p = strpos($english, "]");
			$keyword = substr($english, 0, $p + 1);

			// search for existing translation
			foreach($target as $indexval => $translated) {
				if (stristr($translated, $keyword)) break;
			}
			if ($translated == "") {
				$nok++;
				$translated = "#" . substr($english, 1);
			} else $ok++;

			// delete comment
//			$p = @strpos($translated, "//");
//			if ($p > 5) $translated = trim(substr($translated, 0, $p-1));

			// output record
			$z = $translated;
		} else $z = str_replace(".en.", ".$flag.", $english);

		// output string
//		$z = utf8_encode($z);
		$z = str_replace("\r", "", $z);
		$z = str_replace("\n", "", $z);
		$z = set_tab($z);
		$z = $z . "\r\n";
		if (strlen($z) > 0) {
			if (!fputs($fd, $z, strlen($z))) die("Cannot write to $filename");
		}
	}
	fclose($fd);
	?>
   <script type="text/javascript">
		var OK = <?php print $ok;?>;
		var TOT = <?php print ($ok + $nok);?>;
		var ELT = "<?php print "$flag." . substr(basename($filename), 0, 1);?>";
		//alert(ELT);
		perc = Math.round(100*(OK / TOT));
		progress = document.getElementById(ELT);
		progress.innerHTML = perc+"%";
	</script>
	<?php
	flush();
}

function set_tab($z) {
	$p = strpos($z, "]");
	if ($p < 5) return $z;
	if ($p > 50) return $z;

	$q = strpos($z, "=");
	if ($q < $p) return $z;

	$label = substr($z, 0, $p + 1);
	$value = substr($z, $q);

//	$space = str_repeat("\t", max(0, (40 - $p) / 4));
	$space = str_repeat(" ", max(0, (39 - $p)));

	return $label . $space . $value;
}

?>

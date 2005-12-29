<?php
/**
 * Compiles a list of all functions in all PGV files.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * @version $Id: extract.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 * @package PhpGedView
 * @subpackage External
 */
require "config.php";
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
print_header("Extract Tools");
print "<br /><br />";
print "Search functions:";
print "<form name=\"variablesearch\" action=\"$SCRIPT_NAME\">";
print "Variable to search for: <input type=\"text\" size=\"30\" name=\"var\" /> ";
print "<input type=\"submit\" name=\"action\" value=\"Submit\" />";
print "<input type=\"submit\" name=\"action\" value=\"List functions\" />";
print "</form>";
print "<br /><br />";
print "Help file functions: <br />	";
print "<form name=\"variablesearch\" action=\"$SCRIPT_NAME\" method=\"post\">";
print "<input type=\"hidden\" name=\"action\" value=\"Double\" />";
// print "Language file to check (i.e. nl.php): <input type=\"text\" size=\"30\" name=\"ext\" /> ";
print "Language file to check: <select name=\"language\" style=\"{ font-size: 9pt; }\">";
foreach ($pgv_language as $key => $value)
{
  print "\n\t\t\t<option value=\"$key\"";
  print ">" . $pgv_lang["lang_name_".$key] . "</option>";
}
print "</select>\n\t\t";
print "<input type=\"submit\" name=\"action\" value=\"Double\" />";
print "</form>";
print "<br /><br />";
print "Unused language variable check: <br />	";
print "<form name=\"langsearch\" action=\"$SCRIPT_NAME\" method=\"post\">";
print "<input type=\"hidden\" name=\"action\" value=\"Unused\" />";
print "Language file to check: <select name=\"language\" style=\"{ font-size: 9pt; }\">";
foreach ($pgv_language as $key => $value)
{
  print "\n\t\t\t<option value=\"$key\"";
  print ">" . $pgv_lang["lang_name_".$key] . "</option>";
}
print "</select>\n\t\t";
print "<input type=\"submit\" name=\"action\" value=\"Unused\" />";
print "</form>";
switch ($action) {
	case "List functions":
	$filesused = array();
	print "<table width=\"100%\" border=\"0\">";
	print "<tr><td><b>File</b></td><td><b>Functions</b></td><td><b>Used in</b></td></tr>";
	if ($handle = opendir(".")) {
		while($file = readdir($handle)) {
			if (substr($file,-3) == "php" || substr($file,-2) == "js") {
				$fd = @fopen ($file, "r");
				if (!$fd){
					print "Error opening file:".$file;
				}
				else {
					$filestring = file($file); // Put contents of file into a string
				}
				// Finished. File can be closed.
				fclose ($fd);
				foreach ($filestring as $line => $text){
					if (substr($text,0,8) == "function"){
						$findfunction = preg_replace(array("/function /","/\(.+/"),array("",""), substr($text,0,-3));
						$filesused[$findfunction]["function"] = substr($text,0,-3);
						$filesused[$findfunction]["homefile"] = $file;
					}
				}
			}
		}
	}
	$i = 0;
	if ($handle = opendir(".")) {
		$found = "";
		while($file = readdir($handle)) {
			if (substr($file,-3) == "php") {
				$fd = @fopen ($file, "r");
				if (!$fd){
					print "Error opening file:".$file;
				}
				else {
					$filestring = file_get_contents($file); // Put contents of file into a string
				}
				// Finished. File can be closed.
				fclose ($fd);
				foreach ($filesused as $searchfunction => $fileused){
					if (stristr($filestring, $searchfunction) !== FALSE){
						if (!isset($filesused[$searchfunction][$file]) && $filesused[$searchfunction]["homefile"] <> $file && $found <> $searchfunction){
							$found = $searchfunction;
							$filesused[$searchfunction][] = $file;
						}
					}
					set_time_limit(10) ;
				}
			}
		}
	}
	foreach ($filesused as $k => $file){
		$ct = (count($file)-2);
		if (isset($currenthome)) {
			if ($currenthome == $file["homefile"]) {
				print "<tr><td></td><td>".$file["function"]."</td></tr>";
				$i = 0;
				if ($ct == '0') print "<tr><td></td><td></td><td>".$file["homefile"]."</td></tr>";
				else {
					while ($i <= $ct) {
						if (isset($file[$i])) print "<tr><td></td><td></td><td>".$file[$i]."</td></tr>";
						$i++;
					}
				}

			}
			else {
				$currenthome = $file["homefile"];
				print "<tr><td>".$file["homefile"]."</td></tr>";
				print "<tr><td></td><td>".$file["function"]."</td></tr>";
				$i = 0;
				if ($ct == '0') print "<tr><td></td><td></td><td>".$file["homefile"]."</td></tr>";
				else {
					while ($i <= $ct) {
						if (isset($file[$i])) print "<tr><td></td><td></td><td>".$file[$i]."</td></tr>";
						$i++;
					}
				}
			}
		}
		if (!isset($currenthome)) {
			$currenthome = $file["homefile"];
			print "<tr><td>".$file["homefile"]."<td></tr>";
			print "<tr><td></td><td>".$file["function"]."</td></tr>";
			$i = 0;
			if ($ct == '0') print "<tr><td></td><td></td><td>".$file["homefile"]."</td></tr>";
			else {
				while ($i <= $ct) {
					print "<tr><td></td><td></td><td>".$file[$i]."</td></tr>";
					$i++;
				}
			}
		}
	}
	break;
	case "Submit":
		print "Variable found in: <br />";
		$match = FALSE;
		if ($handle = opendir(".")) {
			while($file = readdir($handle)) {
				if (substr($file,-3) == "php" || substr($file,-2) == "js") {
					$fd = @fopen ($file, "r");
					if (!$fd){
						print "Error opening file:".$file;
					}
					else {
						$filestring = file($file); // Put contents of file into a string
					}
					// Finished. File can be closed.
					fclose ($fd);
					$i = 0;
					$var = $_GET["var"];
					foreach ($filestring as $nr => $line){
						if (stristr($line, $var)){
							if (substr($var,0,1) == "$") $varpreg = substr($var, 1);
							if (preg_match("/\b$varpreg\b/i", htmlentities($line))){
								if ($i == '0') {
									print "<br />".$file."<br />";
									$i++;
								}
								$match = TRUE;
								$nr++;
								print "Line ".$nr.": ".htmlentities($line)."<br />";
							}
						}
					}
				}
			}
		}
		if ($match == FALSE) print "No matches found.";
	break;
	case "Double":
		$i = 0;
		$totalvars = array();
		if ($language == "czech") $ext = "cz.php";
		else if ($language == "danish") $ext = "da.php";
		else if ($language == "german") $ext = "de.php";
		else if ($language == "english") $ext = "en.php";
		else if ($language == "spanish") $ext = "es.php";
		else if ($language == "spanish-ar") $ext = "ar.php";
		else if ($language == "french") $ext = "fr.php";
		else if ($language == "italian") $ext = "it.php";
		else if ($language == "hungarian") $ext = "hu.php";
		else if ($language == "dutch") $ext = "nl.php";
		else if ($language == "norwegian") $ext = "no.php";
		else if ($language == "polish") $ext = "pl.php";
		else if ($language == "portuguese-br") $ext = "br.php";
		else if ($language == "finnish") $ext = "fi.php";
		else if ($language == "swedish") $ext = "se.php";
		else if ($language == "turkish") $ext = "tr.php";
		else if ($language == "chinese") $ext = "zh.php";
		else if ($language == "hebrew") $ext = "he.php";
		else if ($language == "russian") $ext = "ru.php";
		else if ($language == "lithuanian") $ext = "lt.php";
		else if ($language == "vietnamese") $ext = "vi.php";
		if ($handle = opendir("./languages")) {
			while($file = readdir($handle)) {
				$openfile = "./languages/".$file;
				if (substr($openfile,-6) == $ext) {
					$fd = fopen ($openfile, "r");
					if (!$fd){
						print "Error opening file:".$file;
					}
					else {
						$i++;
						$filestring[$i] = file($openfile); // Put contents of file into a string
						foreach ($filestring[$i] as $k => $line){
							if (strtoupper(substr(trim($line), 0, 4)) !== strtoupper("\$pgv")) unset($filestring[$i][$k]);
							else {
								$var = preg_split('/=/', $filestring[$i][$k]);
								$var = trim($var[0]);
								if (!isset($totalvars[$var]["file"])) {
									$totalvars[$var]["file"] = $file;
									$totalvars[$var]["line"] = $line;
								}
							}
								
							
							
						}
					}
					// Finished. File can be closed.
					fclose ($fd);
				}
			}
		}
		print "<br />";
		foreach ($totalvars as $k => $var) {
			if ($var["match"] >= 2) {
				print "==============================";
				print "<br />";
				for ($l=0; $l <= $var["match"]; $l++) {
					if ($l == 0) {
						print "<b>".$var["file"]."</b>";
						print "<br />";
						print $var["line"];
						$l++;
					}
					else {
						print "<br />";
						print "<b>".$var["file".$l]."</b>";
						print "<br />";
						print $var["line".$l];
					}
				}
				print "<br />";
				print "==============================";
				print "<br />";
			}
		}
		break;
	case "Unused":
		$i = 0;
		$totalvars = array();
		if ($language == "czech") $ext = "cz.php";
		else if ($language == "danish") $ext = "da.php";
		else if ($language == "german") $ext = "de.php";
		else if ($language == "english") $ext = "en.php";
		else if ($language == "spanish") $ext = "es.php";
		else if ($language == "spanish-ar") $ext = "ar.php";
		else if ($language == "french") $ext = "fr.php";
		else if ($language == "italian") $ext = "it.php";
		else if ($language == "hungarian") $ext = "hu.php";
		else if ($language == "dutch") $ext = "nl.php";
		else if ($language == "norwegian") $ext = "no.php";
		else if ($language == "polish") $ext = "pl.php";
		else if ($language == "portuguese-br") $ext = "br.php";
		else if ($language == "finnish") $ext = "fi.php";
		else if ($language == "swedish") $ext = "se.php";
		else if ($language == "turkish") $ext = "tr.php";
		else if ($language == "chinese") $ext = "zh.php";
		else if ($language == "hebrew") $ext = "he.php";
		else if ($language == "russian") $ext = "ru.php";
		else if ($language == "lithuanian") $ext = "lt.php";
		else if ($language == "vietnamese") $ext = "vi.php";
		// Read the language variables from the language files
		if ($handle = opendir("./languages")) {
			while($file = readdir($handle)) {
				$openfile = "./languages/".$file;
				if (substr($openfile,-6) == $ext) {
					$fd = fopen ($openfile, "r");
					if (!$fd){
						print "Error opening file:".$file;
					}
					else {
						$i++;
						$filestring[$i] = file($openfile); // Put contents of file into an array
						foreach ($filestring[$i] as $k => $line){
							if (strtoupper(substr(trim($line), 0, 10)) !== strtoupper("\$pgv_lang[")) unset($filestring[$i][$k]);
							else {
								$var = preg_split('/=/', $filestring[$i][$k]);
								$var = trim($var[0]);
								$var = substr($var, 11, -2);
								if (!isset($totalvars[$var]["file"])) {
									$totalvars[$var]["file"] = $file;
									$totalvars[$var]["line"] = $line;
								}
							}
						}
					}
					// Finished. File can be closed.
					fclose ($fd);
				}
			}
		}
		print "<br />";
		// Scan the root files
		if ($handle = opendir("./")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== ".." && substr($file, -3) !== "txt" && $file !== "lang_settings_std.php") {
						$openfile = "./$file";
						$fd = fopen ($openfile, "r");
						if (!$fd) print "Error opening file:".$file;
						else {
							$filestring .= file_get_contents($openfile);
						}
					}
				}
			}
		}
		// Scan the theme files
		// Standard theme
		if ($handle = opendir("./themes/standard")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/standard/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}
		// Cloudy theme
		if ($handle = opendir("./themes/cloudy")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/cloudy/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}		
		// Minimal theme
		if ($handle = opendir("./themes/minimal")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/minimal/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}		
		// Ocean theme
		if ($handle = opendir("./themes/ocean")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/ocean/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}		
		// Simplygreen theme
		if ($handle = opendir("./themes/simplygreen")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/simplygreen/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}		
		
		// Xenea theme
		if ($handle = opendir("./themes/xenea")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/xenea/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}		
		
		// Wood theme
		if ($handle = opendir("./themes/wood")) {
			while($file = readdir($handle)) {
				if (!is_dir($file)) {
					if ($file !== "." && $file !== "..") $openfile = "./themes/wood/$file";
					$fd = fopen ($openfile, "r");
					if (!$fd) print "Error opening file:".$file;
					else {
						$filestring .= file_get_contents($openfile);
					}
				}
			}
		}
		
		if (!isset($configure)) $configure = array();
		if (!isset($facts)) $facts = array();		
		if (!isset($help)) $help = array();
		if (!isset($langvars)) $langvars = array();
				
		foreach ($totalvars as $k => $var) {
			if (preg_match("/[^\$]$k/i", htmlentities($filestring))) unset($totalvars[$k]);
			else {
				if ($var["file"] == "configure_help.$ext") $configure[$k] = $var["line"];
				else if ($var["file"] == "facts.$ext") $facts[$k] = $var["line"];
				else if ($var["file"] == "help_text.$ext") $help[$k] = $var["line"];
				else if ($var["file"] == "lang.$ext") $langvars[$k] = $var["line"];
			}
		}
		if (count($totalvars) > 0) {
			print "==============================";
			print "<br />";
			if (count($langvars) > 0) {
				print "<b>lang.".$ext."</b>";
				print "<br />";
				foreach ($langvars as $k => $var) {
					print htmlentities($var);
					print "<br />";
				}
			}
			if (count($configure) > 0) {
				print "<b>configure_help.".$ext."</b>";
				print "<br />";
				foreach ($configure as $k => $var) {
					print htmlentities($var);
					print "<br />";
				}
			}
			if (count($help) > 0) {			
				print "<b>help_text.".$ext."</b>";
				print "<br />";
				foreach ($help as $k => $var) {
					print htmlentities($var);
					print "<br />";
				}
			}
			if (count($facts) > 0) {			
				print "<b>facts.".$ext."</b>";
				print "<br />";
				foreach ($help as $k => $var) {
					print htmlentities($var);
					print "<br />";
				}
			}
			print "==============================";
		}
		else { 
			print "No unused language variables found";
		}
	break;
}
print_footer();
?>
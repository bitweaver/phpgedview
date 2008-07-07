<?php
/**
 * Create "admin.xx.php" and "editor.xx.php" language files
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
 * This Page Is Valid XHTML 1.0 Transitional! > 23 August 2005
 *
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: lang_move.php,v 1.1 2008/07/07 18:01:11 lsces Exp $
 */

require 'config.php';
if (!PGV_USER_IS_ADMIN) {
	header('Location: index.php');
	exit;
}

if (!empty($_REQUEST['action'])) $action = $_REQUEST['action'];
else $action = "";

$datastore = "./index/langref.php";
$datastore2 = "./index/plangref.php";
if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
function findReferences($directory) {
	global $lang, $files, $poundlang;
	$d = dir($directory);
	
	while (false !== ($entry = $d->read())) {
		if ($entry{0} !=".") {
			$filename = $directory."/".$entry;
			if (is_dir($filename)) findReferences($filename);
			else {
				$parts = preg_split("/\./", $entry);
				$ext = strtolower(end($parts));
				if ($ext != "txt" && $ext != "gif" && $ext != "jpg" && $ext!="z" && $ext!="ttf" && $ext!="log" && $ext!="bak") {
					$fcontents = file_get_contents($filename);
					$ct = preg_match_all("/pgv_lang\[([\"'_\w]+)\](.)/", $fcontents, $match, PREG_SET_ORDER);
					print "Found $ct pgv_lang references in $filename<br />";
					for($i=0; $i<$ct; $i++) {
						$key = trim($match[$i][1], "\"'");
						$lang[$key][] = $filename;
						$lang[$filename][] = $key;
						if ($match[$i][2]=="#") {
							//print "<i>".$filename.$key."</i> ";
							$poundlang[$filename][] = $key;
						}
					}
					$ct = preg_match_all("/print_text\(([\"'_\w]+)/", $fcontents, $match, PREG_SET_ORDER);
					print "Found $ct print_text references in $filename<br />";
					for($i=0; $i<$ct; $i++) {
						$key = trim($match[$i][1], "\"'");
						$lang[$key][] = $filename;
						$lang[$filename][] = $key;
					}
					$ct = preg_match_all("/print_help_link\(.*[\"']([_\w]+)[\"'],/", $fcontents, $match, PREG_SET_ORDER);
					print "Found $ct print_help_link references in $filename<br />";
					for($i=0; $i<$ct; $i++) {
						$key = trim($match[$i][1], "\"'");
						$lang[$key][] = $filename;
						$lang[$filename][] = $key;
					}
				}
			}
		}
	}
}

$lang = array();
if (file_exists($datastore)) {
	$fcontents = file_get_contents($datastore);
	$lang = unserialize($fcontents);
	$fcontents = file_get_contents($datastore2);
	$poundlang = unserialize($fcontents);
}
else $action = "find";

if ($action=="find") {
	$lang=array();
	$poundlang = array();
	@unlink($datastore);
	@unlink($datastore2);
	findReferences(".");
	foreach($lang as $key=>$value) $lang[$key]=array_unique($value);
	$fp = fopen($datastore, "wb");
	$temp = serialize($lang);
	fwrite($fp, $temp);
	fclose($fp);
	
	$fp = fopen($datastore2, "wb");
	$temp = serialize($poundlang);
	fwrite($fp, $temp);
	fclose($fp);
}

?>
<table border="1">
<tr>
	<td><a href="lang_move.php?action=find">Parse Files</a></td>
	<td><a href="lang_move.php?action=list">List All</a></td>
	<td><a href="lang_move.php?action=adminonly">List Admin Only</a></td>
	<td><a href="lang_move.php?action=editoronly">List Editor Only</a></td>
	<td><form name="fileform" action="lang_move.php" method="get">List keys from a File
	<select name="file" onchange="document.fileform.submit();">
	<?php
		foreach($lang as $key=>$entries) {
			if (substr($key, 0, 2)=="./") print "<option value=\"".$key."\">".$key."</option>\n";
		}
	?>
	</select><input type="submit" value="go" /></form></td>
	<td><form name="keyform" action="lang_move.php" method="get">Search for a lang key:
	<br /><input type="text" name="file" value="" /><input type="submit" value="go" /></form></td>
	<td><a href="lang_move.php">Conflicts</a></td>
</tr>
</table>
<?php


if ($action=="list") {
	print "<ul>";
	foreach($lang as $key=>$value) {
		print "<li><b>$key</b> - ".implode(", ",$value)."</li>\n";
	}
	foreach($poundlang as $file=>$keys) {
		print "<li style=\"color:blue;\"><b>$file</b> - ".implode(", ",$keys)."</li>\n";
	}
	print "</ul>";
}
else if ($action=="adminonly") {
	$files = array("./admin.php", "./downloadgedcom.php", "./edit_privacy.php", "./editconfig_gedcom.php", "./editconfig.php",
	"./editgedcoms.php", "./editlang_edit_settings.php", "./editlang_edit.php", "./editlang.php", "./manageservers.php", 
	"./media.php", "./pgvinfo.php", "./printlog.php", "./uploadgedcom.php", "./useradmin.php", "./usermigrate.php", 
	"./viewconnections.php", "./includes/functions_editlang.php", "./includes/functions_export.php", "./includes/functions_tools.php",
	"./sanity_check.php");
	$adminlang = array();
	foreach($files as $i=>$file) {
		if (isset($lang[$file])) {
			$adminlang = array_merge($adminlang, $lang[$file]);
		}
	}
	$adminlang = array_unique($adminlang);
	asort($adminlang);
	print "<ul>";
	foreach($adminlang as $i=>$key) {
		print "<li>";
//		else {
			$nonadmin = array();
			foreach($lang[$key] as $f=>$file) {
				if (!stristr($file, "languages") && !in_array($file, $files)) $nonadmin[] = $file;
			}
			if (count($nonadmin)==0) {
				//-- check not in confighelp
				if (in_array($key, $lang['./languages/configure_help.en.php'])) print "<span style=\"color:green\">".$key."</span> - ";
				else if (in_array($key, $lang['./languages/editor.en.php'])) print "<span style=\"color:orange\">".$key."</span> - ";
				else if (in_array($key, $lang['./languages/admin.en.php'])) print $key." - ";
				else print "<span style=\"color:red\">".$key."</span> - ";
				foreach($lang[$key] as $f=>$file) {
					if (!stristr($file, "languages")) print $file." ";
				}
			}
			else {
				if (in_array($key, $lang['./languages/configure_help.en.php'])) print "<span style=\"color:green\">*".$key."</span> - ";
				else if (in_array($key, $lang['./languages/editor.en.php'])) print "<span style=\"color:orange\">*".$key."</span> - ";
				else print "<span style=\"color:blue\">*".$key."</span> - ";
				foreach($nonadmin as $f=>$file) {
					print $file." ";
				}
			}
//		}
		print "</li>\n";
	}
	print "</ul>\n";
}
else if ($action=="editoronly") {
	$files = array("./addmedia.php", "./addremotelink.php", "./addsearchlink.php", "./edit_changes.php", 
	"./edit_interface.php", "./edit_merge.php", "./edit_quickupdate.php", "./inverselink.php", "./uploadmedia.php",
	"./blocks/review_changes.php", "./includes/functions_edit.php");
	$adminlang = array();
	foreach($files as $i=>$file) {
		if (isset($lang[$file])) {
			$adminlang = array_merge($adminlang, $lang[$file]);
		}
	}
	$adminlang = array_unique($adminlang);
	asort($adminlang);
	print "<ul>";
	foreach($adminlang as $i=>$key) {
		print "<li>";
//		else {
			$nonadmin = array();
			foreach($lang[$key] as $f=>$file) {
				if (!stristr($file, "languages") && !in_array($file, $files)) $nonadmin[] = $file;
			}
			if (count($nonadmin)==0) {
				//-- check not in confighelp
				if (in_array($key, $lang['./languages/configure_help.en.php'])) print "<span style=\"color:green\">".$key."</span> - ";
				else if (in_array($key, $lang['./languages/admin.en.php'])) print "<span style=\"color:orange\">".$key."</span> - ";
				else if (in_array($key, $lang['./languages/editor.en.php'])) print $key." - ";
				else print "<span style=\"color:red\">".$key."</span> - ";
				foreach($lang[$key] as $f=>$file) {
					if (!stristr($file, "languages")) print $file." ";
				}
			}
			else {
				if (in_array($key, $lang['./languages/configure_help.en.php'])) print "<span style=\"color:green\">*".$key."</span> - ";
				else if (in_array($key, $lang['./languages/admin.en.php'])) print "<span style=\"color:orange\">*".$key."</span> - ";
				else print "<span style=\"color:blue\">*".$key."</span> - ";
				foreach($nonadmin as $f=>$file) {
					print $file." ";
				}
			}
//		}
		print "</li>\n";
	}
	print "</ul>\n";
}
else if ($action=="fix_lang" && isset($_REQUEST['langcode'])) {
	$langcode = $_REQUEST['langcode'];
	if (!file_exists("languages/admin.".$langcode.".php")) {
		$fileconts = file_get_contents("languages/admin.en.php");
		$pos1 = strpos($fileconts, "*/");
		$temp = substr($fileconts, 0, $pos1+2);
		print "<br />Creating file languages/admin.".$langcode.".php";
		$fp = fopen("languages/admin.".$langcode.".php", "wb");
		fwrite($fp, $temp."\r\n");
		$langconts = file_get_contents("./languages/lang.".$langcode.".php");
		foreach($lang["./languages/admin.en.php"] as $k=>$key) {
			if (in_array($key, $lang["./languages/lang.".$langcode.".php"])) {
				$pos1 = strpos($langconts, '$pgv_lang["'.$key.'"]');
				if ($pos1!==false) {
					$pos2 = strpos($langconts, '";', $pos1+10);
					if ($pos2!==false) {
						$pos2+=2;
						$def = substr($langconts, $pos1, $pos2-$pos1);
						print "<br />Moving lang key $key";
						fwrite($fp, $def."\r\n");
						$langconts = substr($langconts, 0, $pos1).substr($langconts, $pos2);
					}
				}
			}
		}
		fwrite($fp, "\r\n\r\n?>");
		fclose($fp);
		$b=0;
		while(file_exists("./languages/lang.".$langcode.".php.bak".$b)) $b++;
		copy("./languages/lang.".$langcode.".php", "./languages/lang.".$langcode.".php.bak".$b);
		$langconts = preg_replace("/[\r\n]+/", "\r\n", $langconts);
		$fp = fopen("./languages/lang.".$langcode.".php", "wb");
		fwrite($fp, $langconts);
		fclose($fp);
	}
	else {
		print "<br />Updating file languages/admin.".$langcode.".php";
		$adminconts = file_get_contents("./languages/admin.".$langcode.".php");
		$adminconts = substr($adminconts, 0, strpos($adminconts, "?>"));
		$b=0;
		while(file_exists("./languages/admin.".$langcode.".php.bak".$b)) $b++;
		copy("./languages/admin.".$langcode.".php", "./languages/admin.".$langcode.".php.bak".$b);
		$fp = fopen("languages/admin.".$langcode.".php", "wb");
		fwrite($fp, $adminconts);
		$langconts = file_get_contents("./languages/lang.".$langcode.".php");
		foreach($lang["./languages/admin.en.php"] as $k=>$key) {
			if (in_array($key, $lang["./languages/lang.".$langcode.".php"]) 
						&& stristr($adminconts, '$pgv_lang["'.$key.'"]')===false) {
				$pos1 = strpos($langconts, '$pgv_lang["'.$key.'"]');
				if ($pos1!==false) {
					$pos2 = strpos($langconts, '";', $pos1+10);
					if ($pos2!==false) {
						$pos2+=2;
						$def = substr($langconts, $pos1, $pos2-$pos1);
						print "<br />Moving lang key $key";
						fwrite($fp, $def."\r\n");
						$langconts = substr($langconts, 0, $pos1).substr($langconts, $pos2);
					}
				}
			}
		}
		fwrite($fp, "\r\n\r\n?>");
		fclose($fp);
		$b=0;
		while(file_exists("./languages/lang.".$langcode.".php.bak".$b)) $b++;
		copy("./languages/lang.".$langcode.".php", "./languages/lang.".$langcode.".php.bak".$b);
		$langconts = preg_replace("/[\r\n]+/", "\r\n", $langconts);
		$fp = fopen("./languages/lang.".$langcode.".php", "wb");
		fwrite($fp, $langconts);
		fclose($fp);
	}
	if (!file_exists("languages/editor.".$langcode.".php")) {
		$fileconts = file_get_contents("languages/editor.en.php");
		$pos1 = strpos($fileconts, "*/");
		$temp = substr($fileconts, 0, $pos1+2);
		print "<br /><br />Creating file languages/editor.".$langcode.".php";
		$fp = fopen("languages/editor.".$langcode.".php", "wb");
		fwrite($fp, $temp."\r\n");
		$langconts = file_get_contents("./languages/lang.".$langcode.".php");
		foreach($lang["./languages/editor.en.php"] as $k=>$key) {
			if (in_array($key, $lang["./languages/lang.".$langcode.".php"])) {
				$pos1 = strpos($langconts, '$pgv_lang["'.$key.'"]');
				if ($pos1!==false) {
					$pos2 = strpos($langconts, '";', $pos1+10);
					if ($pos2!==false) {
						$pos2+=2;
						$def = substr($langconts, $pos1, $pos2-$pos1);
						print "<br />Moving lang key $key";
						fwrite($fp, $def."\r\n");
						$langconts = substr($langconts, 0, $pos1).substr($langconts, $pos2);
					}
				}
			}
		}
		fwrite($fp, "\r\n\r\n?>");
		fclose($fp);
		$b=0;
		while(file_exists("./languages/lang.".$langcode.".php.bak".$b)) $b++;
		copy("./languages/lang.".$langcode.".php", "./languages/lang.".$langcode.".php.bak".$b);
		$langconts = preg_replace("/[\r\n]+/", "\r\n", $langconts);
		$fp = fopen("./languages/lang.".$langcode.".php", "wb");
		fwrite($fp, $langconts);
		fclose($fp);
	}
	else {
		print "<br />Updating file languages/editor.".$langcode.".php";
		$adminconts = file_get_contents("./languages/editor.".$langcode.".php");
		$adminconts = substr($adminconts, 0, strpos($adminconts, "?>"));
		$b=0;
		while(file_exists("./languages/editor.".$langcode.".php.bak".$b)) $b++;
		copy("./languages/editor.".$langcode.".php", "./languages/editor.".$langcode.".php.bak".$b);
		$fp = fopen("languages/editor.".$langcode.".php", "wb");
		fwrite($fp, $adminconts);
		$langconts = file_get_contents("./languages/lang.".$langcode.".php");
		foreach($lang["./languages/editor.en.php"] as $k=>$key) {
			if (in_array($key, $lang["./languages/lang.".$langcode.".php"]) 
						&& stristr($adminconts, '$pgv_lang["'.$key.'"]')===false) {
				$pos1 = strpos($langconts, '$pgv_lang["'.$key.'"]');
				if ($pos1!==false) {
					$pos2 = strpos($langconts, '";', $pos1+10);
					if ($pos2!==false) {
						$pos2+=2;
						$def = substr($langconts, $pos1, $pos2-$pos1);
						print "<br />Moving lang key $key";
						fwrite($fp, $def."\r\n");
						$langconts = substr($langconts, 0, $pos1).substr($langconts, $pos2);
					}
				}
			}
		}
		fwrite($fp, "\r\n\r\n?>");
		fclose($fp);
		$b=0;
		while(file_exists("./languages/lang.".$langcode.".php.bak".$b)) $b++;
		copy("./languages/lang.".$langcode.".php", "./languages/lang.".$langcode.".php.bak".$b);
		$langconts = preg_replace("/[\r\n]+/", "\r\n", $langconts);
		$fp = fopen("./languages/lang.".$langcode.".php", "wb");
		fwrite($fp, $langconts);
		fclose($fp);
	}
}
else if (isset($_REQUEST['file'])) {
	print "<b>Language keys for <i>".$_REQUEST['file']."</i></b>";
	print "<ul>";
	if (isset($_REQUEST['file'])) $file = $_REQUEST['file'];
	else $file = "./admin.php";
	
	$temp = $lang[$file];
	foreach($temp as $i=>$key) {
		print "<li><b>".$key."</b> - ";
		foreach($lang[$key] as $j=>$value) {
			if ($value!=$file) {
				if (!stristr($value, "languages/")) print $value .", ";
				else if (stristr($value,".en.")) print "<span style=\"color:red\">".$value ."</span>, ";
			}
		}
		print "</li>\n";
	}
	print "</ul>\n";
}
else {
	$conflicts = array();
	$langfiles = array('./languages/lang.en.php', './languages/admin.en.php', './languages/editor.en.php', './languages/configure_help.en.php', './modules/research_assistant/languages/lang.en.php');
	for($i=0; $i<count($langfiles)-1; $i++) {
		for ($j=$i+1; $j<count($langfiles); $j++) {
			if (!isset($poundlang[$langfiles[$i]])) $poundlang[$langfiles[$i]] = array();
			if (!isset($poundlang[$langfiles[$j]])) $poundlang[$langfiles[$j]] = array();
			$temp1 = array_diff($lang[$langfiles[$i]],$poundlang[$langfiles[$i]]);
			$temp2 = array_diff($lang[$langfiles[$j]],$poundlang[$langfiles[$j]]);
			//$temp1 = $lang[$langfiles[$i]];
			//$temp2 = $lang[$langfiles[$j]];
			$conflicts = array_merge($conflicts, array_intersect($temp1, $temp2));
		}
	}
	print "<b>Found ".count($conflicts)." conflicts<br /></b>\n";
	print "<ul>";
	foreach($conflicts as $c=>$key) {
		print "<li><span style=\"color:blue\">".$key."</span> - ";
		foreach($lang[$key] as $f=>$file) if (!stristr($file, "languages") || stristr($file, ".en.")) print $file." ";
		print "</li>\n";
	}
	print "</ul>";
	
	$unused = array();
	foreach($langfiles as $f=>$file) {
		$temp = $lang[$file];
		foreach($temp as $i=>$key) {
			if (!isset($lang[$key])) $unused[] = $key;
			else {
				$found = false;
				foreach($lang[$key] as $f1=>$file1) {
					if (!stristr($file1, "languages")) {
						$found = true;
						break;
					}
				}
				if (!$found) $unused[] = $key;
			}
		}
	}
	print "<b>Found ".count($unused)." possible unused<br /></b>\n";
	print "<ul>";
	foreach($unused as $c=>$key) {
		print "<li><span style=\"color:blue\">".$key."</span> - ";
		foreach($lang[$key] as $f=>$file) if (!stristr($file, "languages") || stristr($file, ".en.")) print $file." ";
		print "</li>\n";
	}
	print "</ul>";
	
	$undefined = array();
	print "<b>Found ".count($undefined)." possible undefined<br /></b>\n";
	print "<ul>";
	foreach($undefined as $c=>$key) {
		print "<li><span style=\"color:blue\">".$key."</span> - ";
		foreach($lang[$key] as $f=>$file) if (!stristr($file, "languages") || stristr($file, ".en.")) print $file." ";
		print "</li>\n";
	}
	print "</ul>";
}
?>

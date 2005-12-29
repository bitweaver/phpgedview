<?php
/**
 * Various functions used by the language editor of PhpGedView
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
 * @subpackage Admin
 * @version $Id: functions_editlang.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */

if (strstr($_SERVER["SCRIPT_NAME"],"functions_editlang.php")) {
	print "Why do you want to do that?";
	exit;
}

//-----------------------------------------------------------------
function add_backslash_before_dollarsign($dstring)
{
  $dummy = str_replace(chr(92) . chr(36), chr(36), $dstring);
  $dummy = str_replace(chr(36), chr(92) . chr(36), $dummy);
  return $dummy;
}

//-----------------------------------------------------------------
function crlf_lf_to_br($dstring)
{
  $dummy = str_replace("\r\n", "<br />", $dstring);
  $dummy = str_replace("\n", "<br />", $dummy);
  return $dummy;
}

//-----------------------------------------------------------------
function mask_lt($dstring)
{
  $dummy = str_replace("<", "&lt;", $dstring);
  return $dummy;
}

//-----------------------------------------------------------------
function mask_gt($dstring)
{
  $dummy = str_replace(">", "&gt;", $dstring);
  return $dummy;
}

//-----------------------------------------------------------------
function mask_quot($dstring)
{
  $dummy = str_replace("\"", "&quot;", $dstring);
  return $dummy;
}

//-----------------------------------------------------------------
function mask_amp($dstring)
{
  $dummy = str_replace("&", "&amp;", $dstring);
  return $dummy;
}

//-----------------------------------------------------------------
function mask_all($dstring)
{
  $dummy = mask_lt(mask_gt(mask_quot(mask_amp($dstring))));
  return $dummy;
}

//-----------------------------------------------------------------
function LockFile($Temp_Filename)
{
  while (file_exists($Temp_Filename . ".tmp")){clearstatcache();} # wait till file is deleted
  $fp = fopen($Temp_Filename . ".tmp", "w");
  fclose($fp); # temp-file to block all access to $Filename
}

//-----------------------------------------------------------------
function UnLockFile($Temp_Filename)
{
  while (!@unlink($Temp_Filename . ".tmp")){clearstatcache();} # remove file block
}




//-----------------------------------------------------------------
function read_complete_file_into_array($dFileName, $string_needle)
{
  global $file_type, $language2, $PGV_BASE_DIRECTORY, $lang_shortcut;

  $Filename = $PGV_BASE_DIRECTORY . $dFileName;
  LockFile($Filename);

  $LineCounter = 0;
  $InfoArray = array();
  $dFound = ($fp = @fopen($Filename, "r"));

  if (!$dFound)
  {
    $dUserName = getUserName();
    $dUser = getUser($dUserName);
    $dUserRealName = $dUser["firstname"]." ".$dUser["lastname"];

    $dFound = ($fp = @fopen($Filename, "w"));
    fwrite($fp, "<?php\r\n");

    switch ($file_type)
    {
      case "lang"		: fwrite($fp, "/*=============================================================" . "\r\n");
      			  	  fwrite($fp, "   charset=utf-8" . "\r\n");
      			  	  fwrite($fp, "   Project:	phpGedView" . "\r\n");
      			  	  fwrite($fp, "   File:	" . $dFileName . "\r\n");
      			  	  fwrite($fp, "   Author:	John Finlay" . "\r\n");
      			  	  fwrite($fp, "   Translation:	" . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   Comments:	" . $language2 . " Language file for PHPGedView" . "\r\n");
      			  	  fwrite($fp, "   Change Log:	" . date("Y") . "-" . date("m") . "-" . date("d") . " - File created by " . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   		For other changes read:" . "\r\n");
      			  	  fwrite($fp, "   		languages/LANG_CHANGELOG.txt" . "\r\n");
      			  	  fwrite($fp, "=============================================================*/" . "\r\n");
      			  	  fwrite($fp, "# \$Id\$" . "\r\n");
      			  	  fwrite($fp, "if (preg_match(\"/lang\\...\\.php\$/\", \$_SERVER[\"SCRIPT_NAME\"])>0) {" . "\r\n");
      			  	  fwrite($fp, "	print \"You cannot access a language file directly.\";" . "\r\n");
      			  	  fwrite($fp, "	exit;" . "\r\n");
      			  	  fwrite($fp, "}" . "\r\n");
      			  	  fwrite($fp, "//-- GENERAL HELP MESSAGES" . "\r\n");
      			  	  break;
      case "facts"		: fwrite($fp, "/*=============================================================" . "\r\n");
      			  	  fwrite($fp, "   charset=utf-8" . "\r\n");
      			  	  fwrite($fp, "   Project:	phpGedView" . "\r\n");
      			  	  fwrite($fp, "   File:	" . $dFileName . "\r\n");
      			  	  fwrite($fp, "   Author:	John Finlay" . "\r\n");
      			  	  fwrite($fp, "   Translation:	" . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   Comments:	Defines an array of GEDCOM codes and the " . $language2 . " name facts that they represent." . "\r\n");
      			  	  fwrite($fp, "   Change Log:	" . date("Y") . "-" . date("m") . "-" . date("d") . " - File created by " . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   		For other changes read:" . "\r\n");
      			  	  fwrite($fp, "   		languages/LANG_CHANGELOG.txt" . "\r\n");
      			  	  fwrite($fp, "=============================================================*/" . "\r\n");
      			  	  fwrite($fp, "# \$Id\$" . "\r\n");
      			  	  fwrite($fp, "if (preg_match(\"/facts\\...\\.php\$/\", \$_SERVER[\"SCRIPT_NAME\"])>0) {" . "\r\n");
      			  	  fwrite($fp, "	print \"You cannot access a language file directly.\";" . "\r\n");
      			  	  fwrite($fp, "	exit;" . "\r\n");
      			  	  fwrite($fp, "}" . "\r\n");
      			  	  fwrite($fp, "// -- Define a fact array to map Gedcom tags with their " . $language2 . " values" . "\r\n");
      			  	  break;
      case "configure_help"	: fwrite($fp, "/*=============================================================" . "\r\n");
      			  	  fwrite($fp, "   charset=utf-8" . "\r\n");
      			  	  fwrite($fp, "   Project:	phpGedView" . "\r\n");
      			  	  fwrite($fp, "   File:	" . $dFileName . "\r\n");
      			  	  fwrite($fp, "   Author:	John Finlay" . "\r\n");
      			  	  fwrite($fp, "   Translation:	" . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   Comments:	" . $language2 . " Language Configure Help file for PHPGedView" . "\r\n");
      			  	  fwrite($fp, "   Change Log:	" . date("Y") . "-" . date("m") . "-" . date("d") . " - File created by " . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   		For other changes read:" . "\r\n");
      			  	  fwrite($fp, "   		languages/LANG_CHANGELOG.txt" . "\r\n");
      			  	  fwrite($fp, "=============================================================*/" . "\r\n");
      			  	  fwrite($fp, "# \$Id\$" . "\r\n");
      			  	  fwrite($fp, "if (preg_match(\"/configure_help\\...\\.php\$/\", \$_SERVER[\"SCRIPT_NAME\"])>0) {" . "\r\n");
      			  	  fwrite($fp, "	print \"You cannot access a language file directly.\";" . "\r\n");
      			  	  fwrite($fp, "	exit;" . "\r\n");
      			  	  fwrite($fp, "}" . "\r\n");
      			  	  fwrite($fp, "//-- CONFIGURE FILE MESSAGES" . "\r\n");
      			  	  break;
      case "help_text"		: fwrite($fp, "/*=============================================================" . "\r\n");
      			  	  fwrite($fp, "   charset=utf-8" . "\r\n");
      			  	  fwrite($fp, "   Project:	phpGedView" . "\r\n");
      			  	  fwrite($fp, "   File:	" . $dFileName . "\r\n");
      			  	  fwrite($fp, "   Author:	John Finlay" . "\r\n");
      			  	  fwrite($fp, "   Translation:	" . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   Comments:	" . $language2 . " Language Help-file for PHPGedView" . "\r\n");
      			  	  fwrite($fp, "   Change Log:	" . date("Y") . "-" . date("m") . "-" . date("d") . " - File created by " . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   		For other changes read:" . "\r\n");
      			  	  fwrite($fp, "   		languages/LANG_CHANGELOG.txt" . "\r\n");
      			  	  fwrite($fp, "=============================================================*/" . "\r\n");
      			  	  fwrite($fp, "# \$Id\$" . "\r\n");
      			  	  fwrite($fp, "\r\n");
      			  	  fwrite($fp, "//-- GENERAL HELP HEADER" . "\r\n");
      			  	  break;
      case "rs_lang"		: fwrite($fp, "/*=============================================================" . "\r\n");
      			  	  fwrite($fp, "   charset=utf-8" . "\r\n");
      			  	  fwrite($fp, "   Project:	phpGedView" . "\r\n");
      			  	  fwrite($fp, "   File:	" . $dFileName . "\r\n");
      			  	  fwrite($fp, "   Author:	John Finlay" . "\r\n");
      			  	  fwrite($fp, "   Developer:	Roland Dalmulder" . "\r\n");
      			  	  fwrite($fp, "   Translation:	" . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   Comments:	" . $language2 . " Language file for PHPGedView Researchlog" . "\r\n");
      			  	  fwrite($fp, "   Change Log:	" . date("Y") . "-" . date("m") . "-" . date("d") . " - File created by " . $dUserRealName . "\r\n");
      			  	  fwrite($fp, "   		For other changes read:" . "\r\n");
      			  	  fwrite($fp, "   		languages/LANG_CHANGELOG.txt" . "\r\n");
      			  	  fwrite($fp, "=============================================================*/" . "\r\n");
      			  	  fwrite($fp, "# \$Id\$" . "\r\n");
      			  	  fwrite($fp, "\r\n");
      			  	  fwrite($fp, "// -- RS GENERAL MESSAGES" . "\r\n");
      			  	  break;
    }
    fwrite($fp, "\r\n");
    fwrite($fp, "?>");
    fclose($fp);

    $dFound = ($fp = @fopen($Filename, "r"));
  }

  if ($dFound)
  {
    while (!feof($fp))
    {
      $line = fgets($fp, (6 * 1024));
      if ($x = strpos(trim($line), $string_needle))
      {
      	if ($x == 1)
      	{
          $line_mine = $line;
          $line = trim($line);
          $key = trim(substr($line, 0, strpos($line, "]") + 1));
          $ct = preg_match("/=\s*\"(.*)\"/", $line, $match);
          # if ($ct>0) $content = trim($match[1]);
          if ($ct>0) $content = $match[1];
          else $content = "";
          $InfoArray[$LineCounter][0] = $key;				// keystring
          # print "#".$key."# ";
          $InfoArray[$LineCounter][1] = $content;			// message of keystring

          # print "#".$content."#<br />";
          if ($content != "")
          {
            $InfoArray[$LineCounter][2] = get_last_string($line_mine, $content);	// pos of the first char of the message
          }
          else $InfoArray[$LineCounter][2] = "";

          $InfoArray[$LineCounter][3] = $line_mine;			// complete line
        }
        else {$InfoArray[$LineCounter][0] = $line;}
      }
      else {$InfoArray[$LineCounter][0] = $line;}
      $LineCounter++;
    }
    fclose($fp);
  }
  else print "E R R O R !!!";
  # exit;

  UnLockFile($Filename);

  return $InfoArray;
}

//-----------------------------------------------------------------
function find_in_file($MsgNr, $dlang_file)
{
  global $PGV_BASE_DIRECTORY;
  $openfilename = $PGV_BASE_DIRECTORY . $dlang_file;
  $my_array = @file($openfilename);

  $my_Dummy = $my_array[$MsgNr];

  $my_Dummy = trim(substr($my_Dummy, strpos($my_Dummy, "=") + 1));
  $my_Dummy = substr($my_Dummy, strpos($my_Dummy, "\"") + 1);
  $my_Dummy = substr($my_Dummy, 0, strrpos($my_Dummy, ";") - 1);

  return $my_Dummy;
}

//-----------------------------------------------------------------
function find_key_in_new_language_old($new_array, $string_needle)
{
  $dummy = "";
  $dcount = 0;
  while ($new_array[$dcount] != "")
  {
    if ($new_array[$dcount][0] == $string_needle){return $dcount;}
    $dcount++;
  }
  return false;
}

//-----------------------------------------------------------------
function write_array_into_file($dFileName01, $writeArray, $add_new_message_at_line, $new_message_string)
{
  global $PGV_BASE_DIRECTORY;

  $Filename = $PGV_BASE_DIRECTORY . $dFileName01;
  # $Filename = $PGV_BASE_DIRECTORY . "languages/test.php";
  LockFile($Filename);

  $LineCounter = 0;
  if ($fp = @fopen($Filename, "w"))
  {
  $could_write = true;
  foreach($writeArray as $indexval => $var)
  {
    /* A new message which didn't exist before inside the language file */
    if ($LineCounter == $add_new_message_at_line)
    {
      fwrite($fp, $new_message_string . "\r\n");
      $LineCounter++;
    }

    if (empty($var[1]))
    {
      if (isset($var[3]))
      {
         /* Message content is empty */
        # print "var[3]= -" . $var[3]."-";
        # exit;
        fwrite($fp, $var[3]);
      }
      else
      {
         /* Outlined file content */
        # print "var[0]= -" . $var[0]."-";
        # exit;
        fwrite($fp, $var[0]);
      }
    }
    else
    {
      /* Real message content */
      # print "var[3]= -" . $var[3]."-<br />";
      # print "var[2]= -" . $var[2]."-<br />";

      fwrite($fp, substr($var[3], 0, $var[2]));
      # print "substr= -" . substr($var[3], 0, $var[2])."-<br />";

      // Hier irgendwo auf den zu schreibenden string stripslashes anwenden um danach
      // $text = preg_replace('/("\)/', '\', $text); anzuwenden und nur \ und " zu escapen

      $d_Var_01 = stripslashes($var[1]);
      # print "dvar_01= -" . $d_Var_01."-<br />";
      # print "var[1]= -" . $var[1]."-<br />";

      $d_Var_01 = addcslashes($d_Var_01,"\\\"\$");
      # print "dvar_01= -" . $d_Var_01."-<br />";
      # print "var[1]= -" . $var[1]."-<br />";
      # exit;

      # fwrite($fp, $var[1]);
      fwrite($fp, $d_Var_01);
      # print "var[1]= -" . $var[1]."-<br />";

      fwrite($fp, "\";\r\n");
      # print "<br />";
      # exit;
    }
    $LineCounter++;
  }
  fclose($fp);
  }
  else $could_write = false;

  UnLockFile($Filename);
  return $could_write;
}

//-----------------------------------------------------------------
function read_export_file_into_array($dFileName, $string_needle)
{
  $Filename = $dFileName;

  $LineCounter = 0;
  $InfoArray = array();
  $dFound = ($fp = @fopen($Filename, "r"));

  if (!$dFound)
  {
    print "Error file not found"; Exit;
  }
  else
  {
    while (!feof($fp))
    {
      $line = fgets($fp, (6 * 1024));
      if ($x = strpos(trim($line), $string_needle))
      {
      	if ($x == 1)
      	{
          $line_mine = $line;
          $line = trim($line);
          $key = trim(substr($line, 0, strpos($line, "]") + 1));
          $ct = preg_match("/=\s*\"(.*)\"/", $line, $match);
          if ($ct>0) $content = $match[1];
          else $content = "";
          $InfoArray[$LineCounter][0] = $key;				// keystring
          $InfoArray[$LineCounter][1] = $content;			// message of keystring
        }
        $LineCounter++;
      }
    }
    fclose($fp);
  }

  return $InfoArray;
}
//-----------------------------------------------------------------
function get_last_string($hay, $need){
	$getLastStr = 0;
	$pos = strpos($hay, $need);
	if (is_int ($pos)){ //this is to decide whether it is "false" or "0"
		while($pos) {
			$getLastStr = $getLastStr + $pos + strlen($need);
			$hay = substr ($hay , $pos + strlen($need));
			$pos = strpos($hay, $need);
		}
		return $getLastStr - strlen($need);
	}
	else {
		return -1; //if $need wasnt found it returns "-1" , because it could return "0" if it´s found on position "0".
	}
}
//-----------------------------------------------------------------
function check_bom(){
	global $language_settings, $pgv_lang;
	$check = false;
	foreach ($language_settings as $key => $language) {
		// Check if language is active
		if ($language["pgv_lang_use"] == true) {
			// Check language file
			if (file_exists($language["pgv_language"])) $str = file_get_contents($language["pgv_language"]);
			else {
				print "<span class=\"warning\">";
				print str_replace("#lang_filename#", substr($language["pgv_language"], 10), $pgv_lang["no_open"]) . "<br /><br />";
				print "</span>";
			}
			if (ord($str{0}) == 239 && ord($str{1}) == 187 && ord($str{2}) == 191) {
				$check = true;
				print "<span class=\"warning\">".$pgv_lang["bom_found"].substr($language["pgv_language"], 10).".</span>";
				print "<br />";
				$writetext = htmlentities(substr($str,3, strlen($str)));
				if (!$handle = @fopen($language["pgv_language"], "w")){
					print "<span class=\"warning\">";
					print str_replace("#lang_filename#", substr($language["pgv_language"], 10), $pgv_lang["no_open"]) . "<br /><br />";
					print "</span>";
				}
				if (@fwrite($handle,html_entity_decode($writetext)) === FALSE) {
	       			print "<span class=\"warning\">";
	          		print str_replace("#lang_filename#", substr($language["pgv_language"], 10), $pgv_lang["lang_file_write_error"]) . "<br /><br />";
	          		print "</span>";
	   			}
			}

			// Check configuration file
			if (file_exists($language["confighelpfile"])) $str = file_get_contents($language["confighelpfile"]);
			else {
				print "<span class=\"warning\">";
				print str_replace("#lang_filename#", substr($language["confighelpfile"], 10), $pgv_lang["no_open"]) . "<br /><br />";
				print "</span>";
			}
			if (ord($str{0}) == 239 && ord($str{1}) == 187 && ord($str{2}) == 191) {
				$check = true;
				print "<span class=\"warning\">".$pgv_lang["bom_found"].substr($language["confighelpfile"], 10).".</span>";
				print "<br />";
				$writetext = htmlentities(substr($str,3, strlen($str)));
				if (!$handle = @fopen($language["confighelpfile"], "w")){
					print "<span class=\"warning\">";
					print str_replace("#lang_filename#", substr($language["confighelpfile"], 10), $pgv_lang["no_open"]) . "<br /><br />";
					print "</span>";
				}
				if (@fwrite($handle,html_entity_decode($writetext)) === FALSE) {
	       			print "<span class=\"warning\">";
	          		print str_replace("#lang_filename#", substr($language["confighelpfile"], 10), $pgv_lang["lang_file_write_error"]) . "<br /><br />";
	          		print "</span>";
	   			}
			}

			// Check help file
			if (file_exists($language["helptextfile"])) $str = file_get_contents($language["helptextfile"]);
			else {
				print "<span class=\"warning\">";
				print str_replace("#lang_filename#", substr($language["helptextfile"], 10), $pgv_lang["no_open"]) . "<br /><br />";
				print "</span>";
			}
			if (ord($str{0}) == 239 && ord($str{1}) == 187 && ord($str{2}) == 191) {
				$check = true;
				print "<span class=\"warning\">".$pgv_lang["bom_found"].substr($language["helptextfile"], 10).".</span>";
				print "<br />";
				$writetext = htmlentities(substr($str,3, strlen($str)));
				if (!$handle = @fopen($language["helptextfile"], "w")){
					print "<span class=\"warning\">";
					print str_replace("#lang_filename#", substr($language["helptextfile"], 10), $pgv_lang["no_open"]) . "<br /><br />";
					print "</span>";
				}
				if (@fwrite($handle,html_entity_decode($writetext)) === FALSE) {
	       			print "<span class=\"warning\">";
	          		print str_replace("#lang_filename#", substr($language["helptextfile"], 10), $pgv_lang["lang_file_write_error"]) . "<br /><br />";
	          		print "</span>";
	   			}
			}

			// Check facts file
			if (file_exists($language["factsfile"])) $str = file_get_contents($language["factsfile"]);
			else {
				print "<span class=\"warning\">";
				print str_replace("#lang_filename#", substr($language["factsfile"], 10), $pgv_lang["no_open"]) . "<br /><br />";
				print "</span>";
			}
			if (ord($str{0}) == 239 && ord($str{1}) == 187 && ord($str{2}) == 191) {
				$check = true;
				print "<span class=\"warning\">".$pgv_lang["bom_found"].substr($language["factsfile"], 10).".</span>";
				print "<br />";
				$writetext = htmlentities(substr($str,3, strlen($str)));
				if (!$handle = @fopen($language["factsfile"], "w")){
					print "<span class=\"warning\">";
					print str_replace("#lang_filename#", substr($language["factsfile"], 10), $pgv_lang["no_open"]) . "<br /><br />";
					print "</span>";
				}
				if (@fwrite($handle,html_entity_decode($writetext)) === FALSE) {
	       			print "<span class=\"warning\">";
	          		print str_replace("#lang_filename#", substr($language["factsfile"], 10), $pgv_lang["lang_file_write_error"]) . "<br /><br />";
	          		print "</span>";
	   			}
			}
		}
	}
	if ($check == false) print $pgv_lang["bom_not_found"];
}

?>

<?php
/**
 * Edit a language file
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
 * @version $Id: editlang_edit.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 * @package PhpGedView
 * @subpackage EditLang
 */
require "config.php";
require $PGV_BASE_DIRECTORY . $confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $confighelpfile[$LANGUAGE];

require $PGV_BASE_DIRECTORY . "includes/functions_editlang.php";
if (file_exists($PGV_BASE_DIRECTORY . "rs_functions.php")) require $PGV_BASE_DIRECTORY . "rs_functions.php";

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)) {
	print "Please close this window and do a Login in the former window first...";
	exit;
}

if (!isset($lang_filename)) $lang_filename = "";
if (!isset($file_type)) $file_type = "";
if (!isset($language2)) $language2 = "";
if (!isset($ls01)) $ls01 = "";
if (!isset($ls02)) $ls02 = "";
if (!isset($lang_filename_orig)) $lang_filename_orig = "";

print_simple_header($pgv_lang["editlang_help"]);

print "<script language=\"JavaScript\" type=\"text/javascript\">";
  print "self.focus();";
print "</script>\n";

switch ($file_type)
{
  case "lang"		: $lang_filename = $pgv_language[$language2];
  			  $lang_filename_orig = $pgv_language["english"];
  			  break;
  case "facts"		: $lang_filename = $factsfile[$language2];
  			  $lang_filename_orig = $factsfile["english"];
  			  break;
  case "configure_help"	: $lang_filename = $confighelpfile[$language2];
  			  $lang_filename_orig = $confighelpfile["english"];
  			  break;
  case "help_text"	: $lang_filename = $helptextfile[$language2];
  			  $lang_filename_orig = $helptextfile["english"];
  			  break;
}

if ($action != "save")
{
  print "<div align=\"center\"><center>";
    print "<table class=\"facts_table\">";
      print "<tr>";
        print "<td class=\"facts_label03\">";
        print_text("editlang_help");
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td class=\"facts_value\" style=\"text-align:center; \">" . "(" . substr($lang_filename, strpos($lang_filename, "/") + 1) . ")" . "</td>";
      print "</tr>";
    print "</table>";

    print "<form name=\"Form1\" method=\"post\" action=\"" .$PHP_SELF. "\">";
      print "<input type=\"hidden\" name=\"".session_name()."\" value=\"".session_id()."\" />";
      print "<input type=\"hidden\" name=\"action\" value=\"save\" />";
      print "<input type=\"hidden\" name=\"anchor\" value=\"".$anchor."\" />";
      print "<input type=\"hidden\" name=\"language2\" value=\"" . $language2 . "\" />";
      print "<input type=\"hidden\" name=\"ls01\" value=\"" . $ls01 . "\" />";
      print "<input type=\"hidden\" name=\"ls02\" value=\"" . $ls02 . "\" />";
      print "<input type=\"hidden\" name=\"file_type\" value=\"" . $file_type . "\" />";

      print "<table class=\"facts_table\">";
        print "<tr>";
          print "<td class=\"facts_label03\" style=\"color: #0000FF; font-weight: bold; \">";
            print_text("original_message");
          print "</td>";
        print "</tr>";
        print "<tr>";
          print "<td class=\"facts_value\" style=\"text-align:center; color: #0000FF\" >";
            print "<strong style=\"color: red\">|</strong>" . stripslashes(mask_all(find_in_file($ls01, $lang_filename_orig))) . "<strong style=\"color: red\">|</strong>";
          print "</td>";
        print "</tr>";
      print "</table>";
      print "<br />";
      print "<table class=\"facts_table\">";
        print "<tr>";
          print "<td class=\"facts_label03\" style=\"color: #FF0000; font-weight: bold; \" >";
            print_text("message_to_edit");
          print "</td>";
        print "</tr>";
        print "<tr>";
          print "<td class=\"facts_value\" style=\"text-align:center; \" >";
            print "<textarea rows=\"10\" name=\"new_message\" cols=\"75\" style=\"color: #FF0000\" >";
              if ($ls02>0) print stripslashes(mask_all(find_in_file($ls02, $lang_filename)));
            print "</textarea>";
          print "</td>";
        print "</tr>";
      print "</table>";
      print "<br />";
      print "<table class=\"facts_table\">";
        print "<tr>";
          print "<td class=\"facts_value\" style=\"text-align:center; \" >";
            print "<input type=\"submit\" value=\"";
            print_text("lang_save");
            print "\" />";
            print "&nbsp;&nbsp;";
            print "<input type=\"submit\" value=\"";
            print_text("cancel");
            print "\"" . " onclick=\"self.close()\" />";
          print "</td>";
        print "</tr>";
      print "</table>";
    print "</form>";
  print "</center></div>";
}

if ($action == "save")
{
  if (!isset($_POST)) $_POST = $HTTP_POST_VARS;

  // Post-parameters
  // $new_message is the edited message
  // $language2 is the name of the language to edit
  // $ls01 is the number of the message in english language file
  // $ls02 is the number of the message in the edited language file
  // $file_type defines which language file

  switch ($file_type)
  {
    case "lang"			: // read the english lang.en.php file into array
        			  $english_language_array = array();
        			  $english_language_array = read_complete_file_into_array($pgv_language["english"], "pgv_lang[");
        			  // read the chosen lang.xx.php file into array
        			  $new_language_array = array();
        			  $new_language_file = $PGV_BASE_DIRECTORY . $pgv_language[$language2];
        			  $new_language_array = read_complete_file_into_array($new_language_file, "pgv_lang[");
        			  break;
    case "facts"		: // read the english lang.en.php file into array
        			  $english_language_array = array();
        			  $english_language_array = read_complete_file_into_array($factsfile["english"], "factarray[");
        			  // read the chosen lang.xx.php file into array
        			  $new_language_array = array();
        			  $new_language_file = $PGV_BASE_DIRECTORY . $factsfile[$language2];
        			  $new_language_array = read_complete_file_into_array($new_language_file, "factarray[");
        			  break;
    case "configure_help"	: // read the english lang.en.php file into array
        			  $english_language_array = array();
        			  $english_language_array = read_complete_file_into_array($confighelpfile["english"], "pgv_lang[");
        			  // read the chosen lang.xx.php file into array
        			  $new_language_array = array();
        			  $new_language_file = $PGV_BASE_DIRECTORY . $confighelpfile[$language2];
        			  $new_language_array = read_complete_file_into_array($new_language_file, "pgv_lang[");
        			  break;
    case "help_text"	: // read the english lang.en.php file into array
        			  $english_language_array = array();
        			  $english_language_array = read_complete_file_into_array($helptextfile["english"], "pgv_lang[");
        			  // read the chosen lang.xx.php file into array
        			  $new_language_array = array();
        			  $new_language_file = $PGV_BASE_DIRECTORY . $helptextfile[$language2];
        			  $new_language_array = read_complete_file_into_array($new_language_file, "pgv_lang[");
        			  break;
  }

  $new_message = add_backslash_before_dollarsign($new_message);
  $new_message = preg_replace(array("/&amp;/","/&lt;/","/&gt;/"), array("&","<",">"), $new_message);
  $new_message_line = (-1);
  if (isset($new_language_array[$ls02])) $dummyArray = $new_language_array[$ls02];
  else $dummyArray = array();

  if ($ls02 < 1)
  {
    $dummyArray = $english_language_array[$ls01];
    $new_message_line = abs($ls02);
  }
  if (($new_message_line == 0)||($new_message_line>sizeof($new_language_array))) {
	  $new_message_line = sizeof($new_language_array) - 2;
  }

  $new_message = crlf_lf_to_br($new_message);
  $dummyArray[1] = $new_message;
  $dummyArray[3] = substr($dummyArray[3], 0, $dummyArray[2]) . $new_message . "\";";

  if ($ls02 > 0)
  {
    $new_language_array[$ls02] = $dummyArray;
  }

  if ($ls02 == 0)
  {
    # $new_language_array[$ls02] = $dummyArray;
    $ls02 = $new_message_line;
  }

  @copy($new_language_file, $new_language_file . ".old");
  $Write_Ok = write_array_into_file($new_language_file, $new_language_array, $new_message_line, $dummyArray[3]);

  print "<div align=\"center\"><center>";

    print "<table class=\"facts_table\">";
      print "<tr>";
        print "<td class=\"facts_label03\">";
        print_text("savelang_help");
        print "</td>";
      print "</tr>";
      print "<tr>";
        print "<td class=\"facts_value\" style=\"text-align:center; \">" . "(" . substr($lang_filename, strpos($lang_filename, "/") + 1) . ")" . "</td>";
      print "</tr>";
    print "</table>";

  print "<form name=\"Form2\" method=\"post\" action=\"" .$PHP_SELF. "\">";
    print "<table class=\"facts_table\">";
      print "<tr>";
        if ($Write_Ok) print "<td class=\"facts_label03\" style=\"color: #0000FF; font-weight: bold; \">".print_text("original_message",0,1);
        else
        {
        print "<td class=\"warning\" >";
          print str_replace("#lang_filename#", $lang_filename, $pgv_lang["lang_file_write_error"]) . "<br /><br />";
        }
        print "</td>";
      print "</tr>";
      if ($Write_Ok)
      {
      print "<tr>";
        print "<td class=\"facts_value\" style=\"text-align:center; color: #0000FF\" >";
          print "<strong style=\"color: red\">|</strong>".stripslashes(mask_all(find_in_file($ls01, $lang_filename_orig)))."<strong style=\"color: red\">|</strong>";
        print "</td>";
      print "</tr>";
      }
    print "</table>";

    if ($Write_Ok)
    {
    print "<br />";

    print "<table class=\"facts_table\">";
      print "<tr>";
        print "<td class=\"facts_label03\" style=\"color: #0000FF; font-weight: bold; \">";
          print_text("changed_message");
        print "</td>";
      print "</tr>";

      print "<tr>";
        print "<td class=\"facts_value\" style=\"text-align:center; color: #0000FF\" >";
          print "<strong style=\"color: red; \">|</strong>" . stripslashes(mask_all($new_message)) . "<strong style=\"color: red\">|</strong>";
        print "</td>";
      print "</tr>";
    print "</table>";

    print "<br />";
    }

    print "<table class=\"facts_table\">";
      print "<tr>";
          print "<td class=\"facts_value\" style=\"text-align:center; \" >";
          srand((double)microtime()*1000000);
          print "<input type=\"submit\" value=\"" . $pgv_lang["close_window"] . "\"" . " onclick=\"window.opener.showchanges('&dv=".rand()."#".$anchor."'); self.close();\" />";
        print "</td>";
      print "</tr>";
      if ($Write_Ok)
      {
      print "<tr>";
        print "<td class=\"facts_value\" style=\"text-align:center; \" >";
        print "<br /><br /><input type=\"submit\" value=\"";
        print_text("close_window_without_refresh");
        print "\"" . " onclick=\"self.close();\" /><br /><br />";
        print "<div class=\"error\">";
        print_text("edit_lang_utility_warning");
        print "</div></td>";
      print "</tr>";
      }
    print "</table>";

  print "</form>";
  print "</center></div>";
}

print_simple_footer();

?>
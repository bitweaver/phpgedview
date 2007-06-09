<?php
/**
 * File to edit the language settings of PHPGedView
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
 * @version $Id: editlang_edit_settings.php,v 1.5 2007/06/09 21:11:02 lsces Exp $
 */

require "config.php";
require  $confighelpfile["english"];
if (file_exists( $confighelpfile[$LANGUAGE])) require  $confighelpfile[$LANGUAGE];

if (!isset($ln)) $ln = "";
if (!isset($action)) $action = "";
if ($action == "" and $ln == "") {
  header("Location: admin.php");
  exit;
}

if ($action == "cancel") {
  header("Location: changelanguage.php");
  exit;
}

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)) {
  print "Please close this window and do a Login in the former window first...";
  exit;
}

// Create array with configured languages in gedcoms and users
$configuredlanguages = array();

// Determine whether this language's Active status should be protected
$protectActive = false;
if (array_key_exists($ln, $configuredlanguages["gedcom"]) or
  array_key_exists($ln, $configuredlanguages["users"])) {
  $protectActive = true;
}

$d_LangName = "lang_name_" . $ln;
$sentHeader = false;    // Indicates whether HTML headers have been sent
if ($action !="save" and $action != "toggleActive") {
  print_simple_header($pgv_lang["config_lang_utility"]);
  $sentHeader = true;

  print "<script language=\"JavaScript\" type=\"text/javascript\">";
  print "self.focus();";
  print "</script>\n";

  print "<style type=\"text/css\">FORM { margin-top: 0px; margin-bottom: 0px; }</style>";
  print "<div class=\"center\"><center>";
}

/* ------------------------------------------------------------------------------------- */
function write_td_with_textdir_check(){
  global $TEXT_DIRECTION;

  if ($TEXT_DIRECTION == "ltr")
  {print "<td class=\"facts_value\" style=\"text-align:left; \" >";}
  else
  {print "<td class=\"facts_value\" style=\"text-align:right; \">";}
}

/* ------------------------------------------------------------------------------------- */

if ($action == "new_lang") {
  require( "includes/lang_codes_std.php");
  $ln = strtolower($lng_codes[$new_shortcut][0]);

  $d_LangName      = "lang_name_" . $ln;
  $languages[$ln]     = $ln;
  $pgv_lang_use[$ln]    = true;
  $pgv_lang[$ln]    = $lng_codes[$new_shortcut][0];
  $lang_short_cut[$ln]    = $new_shortcut;
  $lang_langcode[$ln]    = $new_shortcut . ";";
  if (array_key_exists($new_shortcut, $lng_synonyms)) $lang_langcode[$ln] .= $lng_synonyms[$new_shortcut];
  $pgv_language[$ln]    = "languages/lang.".$new_shortcut.".php";
  $confighelpfile[$ln]  = "languages/configure_help.".$new_shortcut.".php";
  $helptextfile[$ln]    = "languages/help_text.".$new_shortcut.".php";

  // Suggest a suitable flag file
  $temp = strtolower($lng_codes[$new_shortcut][1]).".gif";
  if (file_exists("../users/icons/flags/".$temp)) {
    $flag = $temp;						// long name takes precedence
  } else if (file_exists("../users/icons/flags/".$new_shortcut.".gif")) {
	$flag = $new_shortcut.".gif";		// use short name if long name doesn't exist
  } else $flag = "new.gif";				// default if neither a long nor a short name exist
  $flagsfile[$ln] = "../users/icons/flags/" . $flag;

  $factsfile[$ln]    = "languages/facts.".$new_shortcut.".php";
  $DATE_FORMAT_array[$ln]  = "D M Y";
  $TIME_FORMAT_array[$ln]  = "g:i:sa";
  $WEEK_START_array[$ln]  = "0";
  $TEXT_DIRECTION_array[$ln]  = "ltr";
  $NAME_REVERSE_array[$ln]  = false;
  $ALPHABET_upper[$ln]    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $ALPHABET_lower[$ln]    = "abcdefghijklmnopqrstuvwxyz";
  $DICTIONARY_SORT[$ln]   = true;

  $pgv_lang[$d_LangName]  = $lng_codes[$new_shortcut][0];
}
else if(!isset($v_flagsfile) && isset($flagsfile[$ln])) $v_flagsfile=$flagsfile[$ln];
else if(!isset($v_flagsfile)) $v_flagsfile = "";

if ($action != "save" and $action != "toggleActive") {
  print "<script language=\"JavaScript\" type=\"text/javascript\">\n";
  print "var helpWin;\n";
  print "function helpPopup(which) {\n";
  print "if ((!helpWin)||(helpWin.closed)) helpWin = window.open('editconfig_help.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');\n";
  print "else helpWin.location = 'editconfig_help.php?help='+which;\n";
  print "return false;\n";
  print "}\n";
  print "function CheckFileSelect() {\n";
  print "if (document.Form1.v_u_lang_filename.value != \"\"){\n";
  print "document.Form1.v_lang_filename.value = document.Form1.v_u_lang_filename.value;\n";
  print "}\n";
  print "}\n";
  print "// -->\n";
  print "</script>\n";

  print "<table class=\"facts_table\">";
  print "<tr>";
  if ($action == "new_lang") {
    print "<td class=\"facts_label\">" . $pgv_lang["add_new_language"] . "</td>";
  }
  else {
    print "<td class=\"facts_label\">" . $pgv_lang["config_lang_utility"] . "</td>";
  }
  print "</tr>";
  print "<tr>";
  print "<td class=\"facts_value\" style=\"text-align:center; \"><b>" . $pgv_lang[$d_LangName];
  print "</b></td></tr>";
  print "</table>\n";

  print "<form name=\"Form1\" method=\"post\" action=\"editlang_edit_settings.php\">";
  print "<input type=\"hidden\" name=\"".session_name()."\" value=\"".session_id()."\" />";
  print "<input type=\"hidden\" name=\"action\" value=\"save\" />";
  print "<input type=\"hidden\" name=\"ln\" value=\"" . $ln . "\" />";
  if ($action == "new_lang") {
    print "<input type=\"hidden\" name=\"new_old\" value=\"new\" />";
  }
  else print "<input type=\"hidden\" name=\"new_old\" value=\"old\" />";

  print "<br /><center>";
  print "<input type=\"submit\" value=\"" . $pgv_lang["lang_save"] . "\" />";
  print "&nbsp;&nbsp;";
  print "<input type=\"submit\" value=\"" . $pgv_lang["cancel"] . "\" onclick=\"document.Form1.action.value='cancel'\" />";
  print "</center><br />";

  print "<table class=\"facts_table\">";

  if ($action != "new_lang") {
    if ($protectActive) $v_lang_use = true;
    if (!isset($v_lang_use)) $v_lang_use = $pgv_lang_use[$ln];
    print "<tr>";
    print "<td class=\"facts_label\" >";
    print $pgv_lang["active"];
    print " <a href=\"javascript:;\" onclick=\"return helpPopup('active_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
    print "</td>";
    write_td_with_textdir_check();

    if ($v_lang_use) {
      print "<input";
      if ($protectActive) print " disabled";
      print " type=\"checkbox\" name=\"v_lang_use\" value=\"true\" checked=\"checked\" />";
    }
    else print "<input type=\"checkbox\" name=\"v_lang_use\" value=\"true\" />";
    print "</td>";
    print "</tr>";
  }
  else print "<input type=\"hidden\" name=\"v_lang_use\" value=\"".$pgv_lang_use[$ln]."\" />";

  print "<tr>";
  if (!isset($v_original_lang_name)) $v_original_lang_name = $pgv_lang[$ln];
  print "<td class=\"facts_label\" >";
  print str_replace("#D_LANGNAME#", $pgv_lang[$d_LangName], $pgv_lang["original_lang_name"]);
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('original_lang_name_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_original_lang_name\" size=\"30\" value=\"" . $v_original_lang_name . "\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_lang_shortcut)) $v_lang_shortcut = $lang_short_cut[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["lang_shortcut"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('lang_shortcut_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_lang_shortcut\" size=\"2\" value=\"" . $v_lang_shortcut . "\" onchange=\"document.Form1.action.value=''; submit();\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_lang_langcode)) $v_lang_langcode = $lang_langcode[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["lang_langcode"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('lang_langcode_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_lang_langcode\" size=\"70\" value=\"" . $v_lang_langcode . "\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_flagsfile)) $v_flagsfile = $flagsfile[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["flagsfile"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('flagsfile_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  $dire = "../users/icons/flags";
  if ($handle = opendir($dire)) {
    $flagfiles = array();
    $sortedflags = array();
    $cf=0;
    print $dire."/";
    while (false !== ($file = readdir($handle))) {
      $pos1 = strpos($file, "gif");
      if ($file != "." && $file != ".." && $pos1) {
        $filelang = substr($file, 0, $pos1-1);
        $fileflag = $dire."/".$filelang.".gif";
        $flagfiles["file"][$cf]=$file;
        $flagfiles["path"][$cf]=$fileflag;
        $sortedflags[$file]=$cf;
        $cf++;
      }
    }
    closedir($handle);
    $sortedflags = array_flip($sortedflags);
    asort($sortedflags);
    $sortedflags = array_flip($sortedflags);
    reset($sortedflags);
    if ($action != "new_lang") {
      print "&nbsp;&nbsp;&nbsp;<select name=\"v_flagsfile\" onchange=\"document.Form1.action.value=''; submit();\">\n";
      foreach ($sortedflags as $key=>$value) {
        $i = $sortedflags[$key];
        print "<option value=\"".$flagfiles["path"][$i]."\" ";
        if ($v_flagsfile == $flagfiles["path"][$i]){
          print "selected ";
          $flag_i = $i;
        }
      print "/>".$flagfiles["file"][$i]."</option>\n";
      }
      print "</select>\n";
    } else {
      foreach ($sortedflags as $key=>$value) {
        $i = $sortedflags[$key];
        if ($v_flagsfile == $flagfiles["path"][$i]){
          $flag_i = $i;
          break;
        }
      }
      print $flagfiles["file"][$i];
    }
  }
  if (isset($flag_i) && isset($flagfiles["path"][$flag_i])){
    print "<div id=\"flag\" style=\"display: inline; padding-left: 7px;\">";
    print " <img src=\"".$flagfiles["path"][$flag_i]."\" alt=\"\" class=\"brightflag\" style=\"border: solid black 1px\" /></div>\n";
  }
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_date_format)) $v_date_format = $DATE_FORMAT_array[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["date_format"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('date_format_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_date_format\" size=\"30\" value=\"" . $v_date_format . "\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_time_format)) $v_time_format = $TIME_FORMAT_array[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["time_format"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('time_format_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_time_format\" size=\"30\" value=\"" . $v_time_format . "\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_week_start)) $v_week_start = $WEEK_START_array[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["week_start"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('week_start_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();

  print "<select size=\"1\" name=\"v_week_start\">";
  $dayArray = array($pgv_lang["sunday"],$pgv_lang["monday"],$pgv_lang["tuesday"],$pgv_lang["wednesday"],$pgv_lang["thursday"],$pgv_lang["friday"],$pgv_lang["saturday"]);

  for ($x = 0; $x <= 6; $x++)  {
    print "<option";
    if ($v_week_start == $x) print " selected=\"selected\"";
    print " value=\"";
    print $x;
    print "\">";
    print $dayArray[$x];
    print "</option>";
  }
  print "</select>";

  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_text_direction)) $v_text_direction = $TEXT_DIRECTION_array[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["text_direction"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('text_direction_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<select size=\"1\" name=\"v_text_direction\">";
  print "<option";
  if ($v_text_direction == "ltr") print " selected=\"selected\"";
  print " value=\"";
  print "0";
  print "\">";
  print $pgv_lang["ltr"];
  print "</option>";
  print "<option";
  if ($v_text_direction == "rtl") print " selected=\"selected\"";
  print " value=\"";
  print "1";
  print "\">";
  print $pgv_lang["rtl"];
  print "</option>";
  print "</select>";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_name_reverse)) $v_name_reverse = $NAME_REVERSE_array[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["name_reverse"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('name_reverse_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<select size=\"1\" name=\"v_name_reverse\">";
  print "<option";
  if (!$v_name_reverse) print " selected=\"selected\"";
  print " value=\"";
  print "0";
  print "\">";
  print $pgv_lang["no"];
  print "</option>";
  print "<option";
  if ($v_name_reverse) print " selected=\"selected\"";
  print " value=\"";
  print "1";
  print "\">";
  print $pgv_lang["yes"];
  print "</option>";
  print "</select>";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_alphabet_upper)) $v_alphabet_upper = $ALPHABET_upper[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["alphabet_upper"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('alphabet_upper_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_alphabet_upper\" size=\"50\" value=\"" . $v_alphabet_upper . "\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_alphabet_lower)) $v_alphabet_lower = $ALPHABET_lower[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["alphabet_lower"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('alphabet_lower_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<input type=\"text\" name=\"v_alphabet_lower\" size=\"50\" value=\"" . $v_alphabet_lower . "\" />";
  print "</td>";
  print "</tr>";

  print "<tr>";
  if (!isset($v_dictionary_sort)) $v_dictionary_sort = $DICTIONARY_SORT[$ln];
  print "<td class=\"facts_label\" >";
  print $pgv_lang["dictionary_sort"];
  print " <a href=\"javascript:;\" onclick=\"return helpPopup('dictionary_sort_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
  print "</td>";
  write_td_with_textdir_check();
  print "<select size=\"1\" name=\"v_dictionary_sort\">";
  print "<option";
  if (!$v_dictionary_sort) print " selected=\"selected\"";
  print " value=\"";
  print "0";
  print "\">";
  print $pgv_lang["no"];
  print "</option>";
  print "<option";
  if ($v_dictionary_sort) print " selected=\"selected\"";
  print " value=\"";
  print "1";
  print "\">";
  print $pgv_lang["yes"];
  print "</option>";
  print "</select>";
  print "</td>";
  print "</tr>";

  if (!isset($v_lang_filename)) $v_lang_filename = "languages/lang.".$v_lang_shortcut.".php";
  if (!isset($v_config_filename)) $v_config_filename = "languages/configure_help.".$v_lang_shortcut.".php";
  if (!isset($v_factsfile)) $v_factsfile = "languages/facts.".$v_lang_shortcut.".php";
  if (!isset($v_helpfile)) $v_helpfile = "languages/help_text.".$v_lang_shortcut.".php";

  if ($action != "new_lang"){
    print "<tr>";
    print "<td class=\"facts_label\" >";
    print $pgv_lang["lang_filenames"];
    print " <a href=\"javascript:;\" onclick=\"return helpPopup('lang_filenames_help'); \"><b style=\"color: red; cursor: help; \">?</b></a>";
    print "</td>";
    write_td_with_textdir_check();

    print $v_config_filename;
    if (!file_exists($v_config_filename)) print "&nbsp;&nbsp;<b class=\"error\">" . $pgv_lang["file_does_not_exist"] . "</b>";
    print "<br />";

    print $v_factsfile;
    if (!file_exists($v_factsfile)) print "&nbsp;&nbsp;<b class=\"error\">" . $pgv_lang["file_does_not_exist"] . "</b>";
    print "<br />";

    print $v_helpfile;
    if (!file_exists($v_helpfile)) print "&nbsp;&nbsp;<b class=\"error\">" . $pgv_lang["file_does_not_exist"] . "</b>";
    print "<br />";

    print $v_lang_filename;
    if (!file_exists($v_lang_filename)) print "&nbsp;&nbsp;<b class=\"error\">" . $pgv_lang["file_does_not_exist"] . "</b>";

    print "</td>";
    print "</tr>";
  }

  print "</table>";

  print "<br />";
  print "<center>";
  print "<input type=\"submit\" value=\"" . $pgv_lang["lang_save"] . "\" />";
  print "&nbsp;&nbsp;";
  print "<input type=\"submit\" value=\"" . $pgv_lang["cancel"] . "\" onclick=\"document.Form1.action.value='cancel'\" />";
  print "</center>";
  print "</form>";
}

if ($action == "toggleActive") {
  if ($language_settings[$ln]["pgv_lang_use"] == true) $pgv_lang_use[$ln] = false;
  else $pgv_lang_use[$ln] = true;
}

if ($action == "save") {
  if (!isset($_POST)) $_POST = $HTTP_POST_VARS;
  if ($protectActive) $_POST["v_lang_use"] = true;
  if (!isset($_POST["v_lang_use"])) $_POST["v_lang_use"] = false;
  if ($_POST["new_old"] == "new") {
    $lang = array();
    $d_LangName      = "lang_name_".$ln;
    $pgv_lang[$d_LangName]  = $v_original_lang_name;
    $pgv_lang[$ln]    = $ln;
    $pgv_language[$ln]    = "languages/lang.".$v_lang_shortcut.".php";
    $confighelpfile[$ln]  = "languages/configure_help.".$v_lang_shortcut.".php";
    $helptextfile[$ln]    = "languages/help_text.".$v_lang_shortcut.".php";
    $factsfile[$ln]    = "languages/facts.".$v_lang_shortcut.".php";
    $language_settings[$ln]  = $lang;
    $languages[$ln]    = $ln;
  }

  $flagsfile[$ln]    = $v_flagsfile;
  $pgv_lang[$ln]  = $_POST["v_original_lang_name"];
  $pgv_lang_use[$ln]  = $_POST["v_lang_use"];
  $lang_short_cut[$ln]  = $_POST["v_lang_shortcut"];
  $lang_langcode[$ln]  = $_POST["v_lang_langcode"];

  if (substr($lang_langcode[$ln],strlen($lang_langcode[$ln])-1,1) != ";") $lang_langcode[$ln] .= ";";

  $ALPHABET_upper[$ln]  = $_POST["v_alphabet_upper"];
  $ALPHABET_lower[$ln]  = $_POST["v_alphabet_lower"];
  $DICTIONARY_SORT[$ln]  = $_POST["v_dictionary_sort"];
  $DATE_FORMAT_array[$ln]  = $_POST["v_date_format"];
  $TIME_FORMAT_array[$ln]  = $_POST["v_time_format"];
  $WEEK_START_array[$ln]  = $_POST["v_week_start"];
  if ($_POST["v_text_direction"] == "0") $TEXT_DIRECTION_array[$ln] = "ltr"; else $TEXT_DIRECTION_array[$ln] = "rtl";
  $NAME_REVERSE_array[$ln]  = $_POST["v_name_reverse"];
}

if ($action == "save" or $action=="toggleActive") {
  $Filename = $INDEX_DIRECTORY . "lang_settings.php";
  if (!file_exists($Filename)) copy("includes/lang_settings_std.php", $Filename);

  $error = "";
  if ($file_array = file($Filename)) {
    @copy($Filename, $Filename . ".old");
    if ($fp = @fopen($Filename, "w")) {
      for ($x = 0; $x < count($file_array); $x++) {
        fwrite($fp, $file_array[$x]);
        $dDummy00 = trim($file_array[$x]);
        if ($dDummy00 == "//-- NEVER manually delete or edit this entry and every line below this entry! --START--//") break;
      }
      fwrite($fp, "\r\n");
      fwrite($fp, "// Array definition of language_settings\r\n");
      fwrite($fp, "\$language_settings = array();\r\n");
      foreach ($language_settings as $key => $value) {
        fwrite($fp, "\r\n");
        fwrite($fp, "//-- settings for " . $languages[$key] . "\r\n");
        fwrite($fp, "\$lang = array();\r\n");
        fwrite($fp, "\$lang[\"pgv_langname\"]    = \"" . $languages[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"pgv_lang_use\"]    = ");
        if ($pgv_lang_use[$key]) fwrite($fp, "true"); else fwrite($fp, "false");
        fwrite($fp, ";\r\n");
        fwrite($fp, "\$lang[\"pgv_lang\"]    = \"" . $pgv_lang[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"lang_short_cut\"]    = \"" . $lang_short_cut[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"langcode\"]    = \"" . $lang_langcode[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"pgv_language\"]    = \"" . $pgv_language[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"confighelpfile\"]    = \"" . $confighelpfile[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"helptextfile\"]    = \"" . $helptextfile[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"flagsfile\"]    = \"" . $flagsfile[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"factsfile\"]    = \"" . $factsfile[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"DATE_FORMAT\"]    = \"" . $DATE_FORMAT_array[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"TIME_FORMAT\"]    = \"" . $TIME_FORMAT_array[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"WEEK_START\"]    = \"" . $WEEK_START_array[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"TEXT_DIRECTION\"]    = \"" . $TEXT_DIRECTION_array[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"NAME_REVERSE\"]    = ");
        if ($NAME_REVERSE_array[$key]) fwrite($fp, "true"); else fwrite($fp, "false");
        fwrite($fp, ";\r\n");
        fwrite($fp, "\$lang[\"ALPHABET_upper\"]    = \"" . $ALPHABET_upper[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"ALPHABET_lower\"]    = \"" . $ALPHABET_lower[$key] . "\";\r\n");
        fwrite($fp, "\$lang[\"DICTIONARY_SORT\"]    = ");
        if ($DICTIONARY_SORT[$key]) fwrite($fp, "true"); else fwrite($fp, "false");
        fwrite($fp, ";\r\n");
        fwrite($fp, "\$language_settings[\"" . $languages[$key] . "\"]  = \$lang;\r\n");
      }
/*
      $end_found = false;
      for ($x = 0; $x < count($file_array); $x++) {
        $dDummy00 = trim($file_array[$x]);
        if ($dDummy00 == "//-- NEVER manually delete or edit this entry and every line above this entry! --END--//"){fwrite($fp, "\r\n"); $end_found = true;}
        if ($end_found) fwrite($fp, $file_array[$x]);
      } */

      fwrite($fp, "\r\n");
      fwrite($fp, "//-- NEVER manually delete or edit this entry and every line above this entry! --END--//\r\n");
      fwrite($fp, "\r\n");
      fwrite($fp, "?>");
      fclose($fp);
	  $logline = AddToLog("lang_settings.php updated by >".getUserName()."<");
 	  if (!empty($COMMIT_COMMAND)) check_in($logline, $Filename, $INDEX_DIRECTORY);	
    } else $error = "lang_config_write_error";
  } else $error = "lang_set_file_read_error";

  if ($error != "") {
    if (!$sentHeader) {
      print_simple_header($pgv_lang["config_lang_utility"]);
      $sentHeader = true;
      print "<div class=\"center\"><center>";
    }
    print "<span class=\"error\">" . $pgv_lang[$error] . "</span><br /><br />";
    print "<form name=\"Form2\" method=\"post\" action=\"" .$SCRIPT_NAME. "\">";
    print "<table class=\"facts_table\">";
    print "<tr>";
    print "<td class=\"facts_value\" style=\"text-align:center; \" >";
    srand((double)microtime()*1000000);
    print "<input type=\"submit\" value=\"" . $pgv_lang["close_window"] . "\"" . " onclick=\"window.opener.showchanges(); self.close();\" />";
    print "</td>";
    print "</tr>";
    print "</table>";
    print "</form>";
  }

}
if ($sentHeader) {
  print "</center></div>";

  print_simple_footer();
} else {
  header("Location: changelanguage.php");
  exit;
}

?>

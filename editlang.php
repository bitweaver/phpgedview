<?php
/**
 * Display a diff between two language files to help in translating.
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
 * @subpackage Languages
 * @version $Id: editlang.php,v 1.4 2008/07/07 18:01:10 lsces Exp $
 */

require "config.php";

loadLangFile("pgv_confighelp");

require  "includes/functions_editlang.php";

if (!isset($action)) $action="";
if (!isset($hide_translated)) $hide_translated=false;
if (!isset($language2)) $language2 = $LANGUAGE;
if (!isset($file_type)) $file_type = "lang";
if (!isset($language1)) $language1="english";
$lang_shortcut = $language_settings[$language2]["lang_short_cut"];

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (!PGV_USER_IS_ADMIN) {
	header("Location: login.php?url=editlang.php");
	exit;
}

$fromEscapedChars	= array("&",     "<",    ">"   );
$toPlainChars		= array("&amp;", "&lt;", "&gt;");

$fromPlainChars		= array("&gt;", "&lt;", "&amp;");
$toEscapedChars		= array(">",    "<",    "&"    );

switch ($action){
  case "edit"	: print_header($pgv_lang["edit_lang_utility"]); break;
  case "export"	: print_header($pgv_lang["export_lang_utility"]); break;
  case "compare": print_header($pgv_lang["compare_lang_utility"]); break;
  default	: print_header($pgv_lang["edit_langdiff"]); break;
}
if (isset($execute) && $action == "debug") {
	if (isset($_POST["DEBUG_LANG"])) $_SESSION["DEBUG_LANG"] = $_POST["DEBUG_LANG"];
	else $_SESSION["DEBUG_LANG"] = "no";
	$DEBUG_LANG = $_SESSION["DEBUG_LANG"];
}

$QUERY_STRING = str_replace(array("&amp;", "&&"), "&", $QUERY_STRING);
if (strpos($QUERY_STRING,"&dv="))$QUERY_STRING = substr($QUERY_STRING,0,strpos($QUERY_STRING,"&dv="));

print "<script language=\"JavaScript\" type=\"text/javascript\">\n";
print "<!--\n";
print "var helpWin;\n";
print "function helpPopup00(which) {\n";
print "if ((!helpWin)||(helpWin.closed)){helpWin = window.open('editlang_edit.php?' + which, '_blank' , 'left=50, top=30, width=600, height=500, resizable=1, scrollbars=1'); helpWin.focus();}\n";
print "else helpWin.location = 'editlang_edit.php?' + which;\n";
print "return false;\n";
print "}\n";
print "function showchanges(which2) {\n";
print "\twindow.location = '$SCRIPT_NAME?$QUERY_STRING'+which2;\n";
print "}\n";
print "//-->\n";
print "</script>\n";

print "<div class=\"center\">";

// Sort the Language table into localized language name order
foreach ($pgv_language as $key => $value){
	$d_LangName = "lang_name_".$key;
	$Sorted_Langs[$key] = $pgv_lang[$d_LangName];
}
asort($Sorted_Langs);

/* Language File Edit Mask */

switch ($action) {
	case "bom" :
		print "<table class=\"facts_table $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"2\">";
		print $pgv_lang["bom_check"];
	    print "</td></tr>";
	    print "<tr><td class=\"facts_value center\">";
	    check_bom();
	    print "</td></tr>";
	    print  "<tr><td class=\"facts_value center\"><a href=\"editlang.php\"><b>";
	    print $pgv_lang["lang_back"];
	    print "</b></a></td></tr></table>";
		break;
	case "edit" :
		print "<form name=\"choose_form\" method=\"get\" action=\"$SCRIPT_NAME\">";
		print "<input type=\"hidden\" name=\"action\" value=\"edit\" />";
		print "<input type=\"hidden\" name=\"execute\" value=\"true\" />";
		print "<table class=\"facts_table $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"4\">";
	    print $pgv_lang["edit_lang_utility"];
	    print "</td></tr>";
		print "<tr>";
		print "<td class=\"facts_value\">";
		print $pgv_lang["language_to_edit"];
		print ":";
		print_help_link("language_to_edit_help", "qm");
		print "<br />";
		print "<select name=\"language2\">";
		foreach ($Sorted_Langs as $key => $value){
			print "\n\t\t\t<option value=\"$key\"";
		    if ($key == $language2) print " selected=\"selected\"";
		    print ">".$pgv_lang["lang_name_".$key]."</option>";
		}
		print "</select>";
		print "</td>";
		print "<td class=\"facts_value\">".$pgv_lang["file_to_edit"].":";
		print_help_link("file_to_edit_help", "qm");
		print "<br />";
		print "<select name=\"file_type\">";

		print "\n\t\t\t<option value=\"admin\"";
		if ($file_type == "admin") print " selected=\"selected\"";
		print ">" . "admin.xx.php" . "</option>";

		print "\n\t\t\t<option value=\"configure_help\"";
		if ($file_type == "configure_help") print " selected=\"selected\"";
		print ">" . "configure_help.xx.php" . "</option>";
		
		print "\n\t\t\t<option value=\"countries\"";
		if ($file_type == "countries") print " selected=\"selected\"";
		print ">" . "countries.xx.php" . "</option>";
		
		print "\n\t\t\t<option value=\"editor\"";
		if ($file_type == "editor") print " selected=\"selected\"";
		print ">" . "editor.xx.php" . "</option>";

		print "\n\t\t\t<option value=\"extra\"";
		if ($file_type == "extra") print " selected=\"selected\"";
		print ">" . "extra.xx.php" . "</option>";

		print "\n\t\t\t<option value=\"facts\"";
		if ($file_type == "facts") print " selected=\"selected\"";
		print ">" . "facts.xx.php" . "</option>";

		print "\n\t\t\t<option value=\"faqlist\"";
		if ($file_type == "faqlist") print " selected=\"selected\"";
		print ">" . "faqlist.xx.php" . "</option>";

		print "\n\t\t\t<option value=\"help_text\"";
		if ($file_type == "help_text") print " selected=\"selected\"";
		print ">" . "help_text.xx.php" . "</option>";
		
		print "\n\t\t\t<option value=\"lang\"";
		if ($file_type == "lang") print " selected=\"selected\"";
		print ">"."lang.xx.php"."</option>";

		print "</select>";
		print "</td>";

		print "<td class=\"facts_value\">";
		print $pgv_lang["hide_translated"];
		print ":";
		print_help_link("hide_translated_help", "qm");
		print "<br />";
		print "<select name=\"hide_translated\">";
		print "<option";
		if (!$hide_translated) print " selected=\"selected\"";
		print " value=\"";
		print "0";
		print "\">";
		print $pgv_lang["no"];
		print "</option>";
		print "<option";
		if ($hide_translated) print " selected=\"selected\"";
		print " value=\"";
		print "1";
		print "\">";
		print $pgv_lang["yes"];
		print "</option>";
		print "</select>";
		print "</td>";
		print "<td class=\"facts_value\" style=\"text-align: center; \">";
		print "<input type=\"submit\" value=\"" . $pgv_lang["edit"] . "\" />";
		print "</td>";
		print "</tr>";
	    print  "<tr><td class=\"facts_value center\" colspan=\"4\"><a href=\"editlang.php\"><b>";
	    print $pgv_lang["lang_back"];
	    print "</b></a></td></tr>";
		print "</table>";
		print "</form>";
		if (isset($execute)) {
		    switch ($file_type) {
		      case "facts":
		        $whichFile = $factsfile;
		        $whichVars = "factarray[";
		        break;
		      case "configure_help":
		        $whichFile = $confighelpfile;
		        $whichVars = "pgv_lang[";
		        break;
		      case "help_text":
		        $whichFile = $helptextfile;
		        $whichVars = "pgv_lang[";
		        break;
		      case "admin":
		        $whichFile = $adminfile;
		        $whichVars = "pgv_lang[";
		        break;
		      case "editor":
		        $whichFile = $editorfile;
		        $whichVars = "pgv_lang[";
		        break;
		      case "countries":
		        $whichFile = $countryfile;
		        $whichVars = "countries[";
		        break;
		      case "faqlist":
		        $whichFile = $faqlistfile;
		        $whichVars = "faqlist[";
		        break;
		      case "extra":
		        $whichFile = $extrafile;
		        $whichVars = array("pgv_lang[", "factarray[", "countries[", "faqlist[");
		        break;
		      case "lang":
		      default:
		        $whichFile = $pgv_language;
		        $whichVars = "pgv_lang[";
		        break;
		    }
			// read the english language file into array
			$english_language_array = array();
			$english_language_array = read_complete_file_into_array($whichFile["english"], $whichVars);
			// read the chosen language file into array
			$new_language_array = array();
			$new_language_array = read_complete_file_into_array($whichFile[$language2], $whichVars);

			print "<table class=\"facts_table $TEXT_DIRECTION\" style=\"width:70%; \">";
		    print "<tr><td class=\"facts_value center\" colspan=\"2\"><span class=\"subheaders\">".$pgv_lang["listing"].":&nbsp;&nbsp;&nbsp;";
		    print $whichFile["english"]."&nbsp;&nbsp;&nbsp;".$pgv_lang["and"]."&nbsp;&nbsp;&nbsp;".$whichFile[$language2];
		    print "</span><br /><br />\n";
		    print "<span class=\"subheaders\">" . $pgv_lang["contents"] . ":</span></td></tr>";
			$lastfound = (-1);
			for ($z = 0; $z < sizeof($english_language_array); $z++) {
				if (isset($english_language_array[$z][1])) {
					$dummy_output = "";
					$dummy_output .= "<tr>";
					$dummy_output .= "<td class=\"facts_label\" rowspan=\"2\" dir=\"ltr\">";
				  	$dummy_output .= $english_language_array[$z][0];
					$dummy_output .= "</td>\n";
					$dummy_output .= "<td class=\"facts_value wrap\">";
				  	$dummy_output .= "\n<a name=\"a1_".$z."\"></a>\n";
				  	$temp = str_replace($fromEscapedChars, $toPlainChars, $english_language_array[$z][1]);
				  	if ($temp == "") {
				    	$dummy_output .= "<strong style=\"color: #FF0000\">" . str_replace("#LANGUAGE_FILE#", $pgv_language[$language1], $pgv_lang["message_empty_warning"]) . "</strong>";
				  	}
				  	else $dummy_output .= "<i>" . $temp . "</i>";
					$dummy_output .= "</td>";
					$dummy_output .= "</tr>\n";
					$dummy_output_02 = "";
					$dummy_output_02 .= "<tr>\n";
					$dummy_output_02 .= "<td class=\"facts_value wrap\">";
				  	$found = false;
				  	for ($y = 0; $y < sizeof($new_language_array); $y++) {
				    	if (isset($new_language_array[$y][1])) {
				      		if ($new_language_array[$y][0] == $english_language_array[$z][0]) {
				        		$dDummy =  $new_language_array[$y][1];
				        		$dummy_output_02 .= "<a href=\"javascript:;\" onclick=\"return helpPopup00('" . "ls01=" . $z . "&amp;ls02=" . $y . "&amp;language2=" . $language2 . "&amp;file_type=" . $file_type . "&amp;" . session_name() . "=" . session_id() . "&amp;anchor=a1_" . $z . "');\">";
				        		$temp = str_replace($fromEscapedChars, $toPlainChars, $dDummy);
				        		$dummy_output_02 .= $temp;
				        		if ($temp == "") {
				          			$dummy_output_02 .= "<strong style=\"color: #FF0000\">" . str_replace("#LANGUAGE_FILE#", $pgv_language[$language2], $pgv_lang["message_empty_warning"]) . "</strong>";
				        		}
				        		$dummy_output_02 .= "</a>";
				        		$found = true;
				        		$lastfound = $y;
				        		break;
				      		}
				    	}
				  	}
				  	if ((($hide_translated) and (!$found)) or (!$hide_translated)) {
						print $dummy_output;
				  		print $dummy_output_02;
				  		if (!$found) {
				    		print "<a style=\"color: #FF0000\" href=\"javascript:;\" onclick=\"return helpPopup00('" . "ls01=" . $z . "&amp;ls02=" . (0 - intval($lastfound) - 1) . "&amp;language2=" . $language2 . "&amp;file_type=" . $file_type . "&amp;anchor=a1_" . $z . "');\">";
				    		print "<i>";
				    		$temp = str_replace($fromEscapedChars, $toPlainChars, $english_language_array[$z][1]);
				    		if ($temp == "") print "&nbsp;";
				    		else print $temp;
				    		print "</i>";
				    		print "</a>";
				  		}
						print "</td>";
							print "</tr>\n";
				  	}
				}
			}
			print "</table>";
		}
		break;
	case "debug" :
		print "<form name=\"debug_form\" method=\"post\" action=\"editlang.php\">";
		print "<input type=\"hidden\" name=\"action\" value=\"debug\" />";
		print "<input type=\"hidden\" name=\"execute\" value=\"true\" />";
		print "<table class=\"facts_table $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"3\">";
		print $pgv_lang["lang_debug"];
	    print "</td></tr>";
	    print "<tr>";
		print "<td class=\"facts_value\" >";
		print "<input type=\"checkbox\" name=\"DEBUG_LANG\" value=\"yes\" ";
		if (isset($_SESSION["DEBUG_LANG"])) {
			if (($_SESSION["DEBUG_LANG"]) == "yes") print "checked=\"checked\"";
		}
		print " />";
		print $pgv_lang["lang_debug_use"]."&nbsp;&nbsp;</td>";
		print "<td class=\"facts_value\" align=\"center\" ><input type=\"submit\" value=\"".$pgv_lang["save"]."\" />";
		print "</td>";
	    print "</tr>";
	    print  "<tr><td class=\"facts_value center\" colspan=\"4\"><a href=\"editlang.php\"><b>";
	    print $pgv_lang["lang_back"];
	    print "</b></a></td></tr>";
	    print "</table>";
		print "</form>";
		break;
	case "export" :
		print "<form name=\"export_form\" method=\"get\" action=\"$SCRIPT_NAME\">";
		print "<input type=\"hidden\" name=\"action\" value=\"export\" />";
		print "<input type=\"hidden\" name=\"execute\" value=\"true\" />";
		print "<table class=\"facts_table $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"3\">";
		print $pgv_lang["export_lang_utility"];
	    print "</td></tr>";
		print "<tr>";
		print "<td class=\"facts_value\">";
		print $pgv_lang["language_to_export"];
		print ":";
		print_help_link("language_to_export_help", "qm");
		print "<br />";
		print "<select name=\"language2\">";
		foreach ($Sorted_Langs as $key => $value){
			print "\n\t\t\t<option value=\"$key\"";
			if ($key == $language2) print " selected=\"selected\"";
			print ">".$pgv_lang["lang_name_".$key]."</option>";
		}
		print "</select>";
		print "</td>";

		print "<td class=\"facts_value\">";
		print "<input type=\"checkbox\" name=\"configure_help\" value=\"true\" checked=\"checked\"/>configure_help<br/>";
		print "<input type=\"checkbox\" name=\"help_text\" value=\"true\"/>help_text<br/>";
		print "<input type=\"checkbox\" name=\"lang\" value=\"true\"/>lang<br/>";
		print "</td>";

		print "<td class=\"facts_value\" style=\"text-align: center; \">";
		print "<input type=\"submit\" value=\"" . $pgv_lang["export"] . "\" />";
		print "</td></tr>";
	    print  "<tr><td class=\"facts_value center\" colspan=\"4\"><a href=\"editlang.php\"><b>";
	    print $pgv_lang["lang_back"];
	    print "</b></a></td></tr>";
	    print "</table></form>";
		if (isset($execute)) {
		    $FileName = $pgv_language[$language2] . ".html";
		    $fp = @fopen($FileName, "w");

		    fwrite($fp, "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n");
		    fwrite($fp, "<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n");
		    fwrite($fp, "<head><title>PGV ".$language2."</title>\r\n");
		    fwrite($fp, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\r\n");
			fwrite($fp, "<style type=\"text/css\">\r\n");
			fwrite($fp, ".helpstart {\r\n\tfont-family: Arial, sans-serif;\r\n\tcolor: Blue;\r\n\tfont-size: 14px;\r\n\tfont-weight : bold;\r\n\ttext-decoration : none;\r\n}\r\n");
			fwrite($fp, ".helptext {\r\n\tfont-family: Arial, sans-serif;\r\n\tcolor: black;\r\n\tfont-size: 12px;\r\n}\r\n");
			fwrite($fp, ".helpstart0 {\r\n\tfont-family: Arial, sans-serif;\r\n\tfont-size: 16px;\r\n\tfont-weight: bold;\r\n}\r\n");
			fwrite($fp, ".helpstart1 {\r\n\tfont-family: Arial, sans-serif;\r\n\tfont-size: 14px;\r\n\tfont-weight: bold;\r\n}\r\n");
			fwrite($fp, ".helpstart2 {\r\n\tfont-family: Arial, sans-serif;\r\n\tfont-size: 12px;\r\n\tfont-weight: bold;\r\n\t}\r\n");
			fwrite($fp, "</style>\r\n");
		    fwrite($fp, "</head>\r\n<body>\r\n");

			set_time_limit(300);

		    $language_array = array();
		    if(isset($configure_help))
		    	$language_array = array_merge($language_array, read_export_file_into_array($confighelpfile[$language2], "pgv_lang["));
		    if(isset($help_text))
		    	$language_array = array_merge($language_array, read_export_file_into_array($helptextfile[$language2], "pgv_lang["));
		    if(isset($lang))
		    	$language_array = array_merge($language_array, read_export_file_into_array($pgv_language[$language2], "pgv_lang["));
		    $new_language_array = array();
		    $new_language_array_counter = 0;;

		    for ($z = 0; $z < sizeof($language_array); $z++)
		    {
		      if (isset($language_array[$z][0]))
		      {
		        $language_array[$z][0] = substr($language_array[$z][0], strpos($language_array[$z][0], "\"") + 1);
		        $language_array[$z][0] = substr($language_array[$z][0], 0, strpos($language_array[$z][0], "\""));
		        $new_language_array[$new_language_array_counter] = $language_array[$z];
		        $new_language_array_counter++;
		      }
		    }

		    fwrite($fp, "<ol>\r\n");

		    for ($z = 0; $z < sizeof($new_language_array); $z++)
		    {
		      for ($x = 0; $x < sizeof($language_array); $x++)
		      {
		        $dDummy = $new_language_array[$z][0];
		        $dDummy = substr($dDummy, 0, strpos($dDummy, "_help"));

		        if (isset($language_array[$x][0]))
		        {
		        if (strpos($language_array[$x][0], "\"" . $dDummy . "\"") > 0)
		        {
		          if ($new_language_array[$z][0] != "config_help" and $new_language_array[$z][0] != "welcome_help")
		          {
		            $new_language_array[$z][0] = $language_array[$x][1];
		          }
		          break;
		        }
		        }
		      }
		    }

		    // Temporarily switch languages to match the language selected for Export,
		    //   so that function print_text will substitute text in the correct language
		    $savedLanguage = $LANGUAGE;
		    $LANGUAGE = $language2;
		    loadLangFile("pgv_lang, pgv_admin, pgv_editor, pgv_facts, pgv_help, pgv_confighelp");
		    $LANGUAGE = $savedLanguage;

		    for ($z = 0; $z < sizeof($new_language_array); $z++)
		    {
		      if ($new_language_array[$z][0] != "config_help" and $new_language_array[$z][0] != "welcome_help")
		      {
		        fwrite($fp, "<li>");
		        fwrite($fp, stripslashes(print_text($new_language_array[$z][1],0,2)) . "<br /><br /></li>\r\n");
		      }
		    }

		    // Restore language to original setting -- we're done
			if ($language2 != $LANGUAGE) {			// Only necessary when languages differ
		    	loadLangFile("pgv_lang, pgv_admin, pgv_editor, pgv_facts, pgv_help, pgv_confighelp");
		  	}

		    fwrite($fp, "</ol>\r\n");
		    fwrite($fp, "</body>\r\n</html>");
		    fclose($fp);
		    print "<br /><strong>";
		    print $pgv_lang["export_ok"];
		    print "</strong><br />";
		    print $pgv_lang["export_filename"];
		    print " <a href=\"".$FileName."\">".$FileName."</a>";
	    }
		break;
	case "compare" :
		print "<form name=\"langdiff_form\" method=\"get\" action=\"$SCRIPT_NAME\">";
		print "<input type=\"hidden\" name=\"action\" value=\"compare\" />";
		print "<input type=\"hidden\" name=\"execute\" value=\"true\" />";
		print "<table class=\"facts_table $TEXT_DIRECTION\" style=\"width:70%; \">";
		print "<tr><td class=\"facts_label03\" colspan=\"3\">";
		print $pgv_lang["compare_lang_utility"];
	    print "</td></tr>";
		print "<tr>";
		print "<td class=\"facts_value\">";
		print $pgv_lang["new_language"];
		print ":";
		print_help_link("new_language_help", "qm");
		print "<br />";
		print "<select name=\"language1\">";
		foreach ($Sorted_Langs as $key => $value){
			print "\n\t\t\t<option value=\"$key\"";
			if ($key == $language1) print " selected=\"selected\"";
			print ">".$pgv_lang["lang_name_".$key]."</option>";
		}
		print "</select>";
		print "</td>";
		print "<td class=\"facts_value\">";
		print $pgv_lang["old_language"];
		print ":";
		print_help_link("old_language_help", "qm");
		print "<br />";
		print "<select name=\"language2\">";
		foreach ($Sorted_Langs as $key => $value){
			print "\n\t\t\t<option value=\"$key\"";
			if ($key == $language2) print " selected=\"selected\"";
			print ">".$pgv_lang["lang_name_".$key]."</option>";
		}
		print "</select>";
		print "</td>";

		print "<td class=\"facts_value\" style=\"text-align: center; \">";
		print "<input type=\"submit\" value=\"" . $pgv_lang["compare"] . "\" />";
		print "</td>";
		print "</tr>";
	    print  "<tr><td class=\"facts_value center\" colspan=\"4\"><a href=\"editlang.php\"><b>";
	    print $pgv_lang["lang_back"];
	    print "</b></a></td></tr>";
		print "</table>";
		print "</form>";
		if (isset($execute)) {
    		foreach(array("admin", "configure_help", "countries", "editor", "extra", "facts", "faqlist", "help_text", "lang") as $file_type) {
		      switch ($file_type) {
		        case "facts":
		          $whichFile = $factsfile;
		          $whichVars = "factarray[";
		          break;
		        case "configure_help":
		          $whichFile = $confighelpfile;
		          $whichVars = "pgv_lang[";
		          break;
		        case "help_text":
		          $whichFile = $helptextfile;
		          $whichVars = "pgv_lang[";
		          break;
		        case "admin":
		          $whichFile = $adminfile;
		          $whichVars = "pgv_lang[";
		          break;
		        case "editor":
		          $whichFile = $editorfile;
		          $whichVars = "pgv_lang[";
		          break;
		        case "countries":
		          $whichFile = $countryfile;
		          $whichVars = "countries[";
		          break;
		        case "faqlist":
		          $whichFile = $faqlistfile;
		          $whichVars = "faqlist[";
		          break;
		        case "extra":
		          $whichFile = $extrafile;
		          $whichVars = array("pgv_lang[", "factarray[", "countries[", "faqlist[");
		          break;
		        case "lang":
		        default:
		          $whichFile = $pgv_language;
		          $whichVars = "pgv_lang[";
		          break;
		      }
		      
		      // -------------- Compare the current file type -----------------------
		      if (file_exists($whichFile[$language1]) || file_exists($whichFile[$language2])) {
			    $list = array();
			    if (file_exists($whichFile[$language1])) $list = read_export_file_into_array($whichFile[$language1], $whichVars);
			    $list1 = array();
			    foreach($list as $key => $item) $list1[$item[0]] = $item[1];
			    $list = array();
			    if (file_exists($whichFile[$language2])) $list = read_export_file_into_array($whichFile[$language2], $whichVars);
			    $list2 = array();
			    foreach($list as $key => $item) $list2[$item[0]] = $item[1];
			    unset($list);

		        print "<br /><span class=\"subheaders\">".$pgv_lang["comparing"]."<br />".$whichFile[$language1]."&nbsp;&nbsp;&nbsp;&lt;---&gt;&nbsp;&nbsp;&nbsp;".$whichFile[$language2]."</span><br /><br />\n";
		        // ---- Look for additions
		        //      These are entries that exist in the first but don't exist in the second file
		        print "<span class=\"subheaders\">".$pgv_lang["additions"].":</span>";
		        print "<table class=\"facts_table $TEXT_DIRECTION\">\n";
		        $count=0;
		        foreach($list1 as $key=>$value) {
		          if (!array_key_exists($key, $list2)) {
		          	print "<tr><td class=\"facts_label\">".$key."</td>\n";
		          	print "<td class=\"facts_value\">".str_replace($fromEscapedChars, $toPlainChars, $value)."</td></tr>\n";
		          	$count++;
		          }
		        }
		        if ($count==0) print "<tr><td colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_additions"]."</td></tr>\n";
		        print "</table><br /><br />\n";
		        // ---- Look for subtractions
		        //      These are entries that exist in the second but don't exist in the first file
		        print "<span class=\"subheaders\">".$pgv_lang["subtractions"].":</span>";
		        print "<table class=\"facts_table $TEXT_DIRECTION\">\n";
		        $count=0;
		        foreach($list2 as $key=>$value) {
		          if (!array_key_exists($key, $list1)) {
		          	print "<tr><td class=\"facts_label\">".$key."</td>\n";
		          	print "<td class=\"facts_value\">".str_replace($fromEscapedChars, $toPlainChars, $value)."</td></tr>\n";
		          	$count++;
		          }
		        }
		        if ($count==0) print "<tr><td colspan=\"2\" class=\"facts_value\">".$pgv_lang["no_subtractions"]."</td></tr>\n";
		        print "</table><br /><br />\n";
                
		        print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" width=\"100%\" height=\"6\" alt=\"\" /><br />\n";
		      }
    		}
		}
		break;
	default :?>
		<br />
		<table class="facts_table <?php print $TEXT_DIRECTION ?>" >
		<tr>
			<td class="facts_label03" colspan="2">
				<?php print $pgv_lang["edit_langdiff"]; ?>
			</td>
		</tr>
		<tr>
			<td class="facts_value width50"><?php
				print_help_link("bom_check_help", "qm");
				print "<a href=\"editlang.php?action=bom\">".$pgv_lang["bom_check"]."</a>";
	    	?></td>
	      	<td class="facts_value width50"><?php
				print_help_link("edit_lang_utility_help", "qm");
	      		print "<a href=\"editlang.php?action=edit\">".$pgv_lang["edit_lang_utility"]."</a>";
	    	?></td>
	    </tr>
	    <tr>
	    	<td class="facts_value width50"><?php
	    		print_help_link("lang_debug_help", "qm");
	        	print "<a href=\"editlang.php?action=debug\">".$pgv_lang["lang_debug"]."</a>";
	    	?></td>
		  	<td class="facts_value width50"><?php
				print_help_link("export_lang_utility_help", "qm");
		  		print "<a href=\"editlang.php?action=export\">".$pgv_lang["export_lang_utility"]."</a>";
			?></td>
		</tr>
		<tr>
			<td class="facts_value width50"><?php
				print_help_link("translation_forum_desc", "qm"); ?>
				<a href="http://sourceforge.net/forum/forum.php?forum_id=294245" target="_blank" ><?php
				print $pgv_lang["translation_forum"];
	      	?></td>
		  	<td class="facts_value width50"><?php
				print_help_link("compare_lang_utility_help", "qm");
	      		print "<a href=\"editlang.php?action=compare\">".$pgv_lang["compare_lang_utility"]."</a>";
		  	?></td>
		</tr>
		<tr>
		  	<td class="facts_value" colspan="2">
		  	<div class="center">
				<a href="admin.php"><b><?php print $pgv_lang["lang_back_admin"];?></a>
			</div>
			</td>
		</tr>
		</table>
		<br />
		<?php
}
?>
</div>
<?php

//-- load file for language settings
require("includes/lang_settings_std.php");
$Languages_Default = true;
if (file_exists($INDEX_DIRECTORY . "lang_settings.php")) {
	$DefaultSettings = $language_settings;		// Save default settings, so we can merge properly
	require($INDEX_DIRECTORY . "lang_settings.php");
	$ConfiguredSettings = $language_settings;	// Save configured settings, same reason
	$language_settings = array_merge($DefaultSettings, $ConfiguredSettings);	// Copy new langs into config
	// Now copy new language settings into existing configuration
	foreach ($DefaultSettings as $lang => $settings) {
		foreach ($settings as $key => $value) {
			if (!isset($language_settings[$lang][$key])) $language_settings[$lang][$key] = $value;
		}
	}
	unset($DefaultSettings);
	unset($ConfiguredSettings);		// We don't need these any more
	$Languages_Default = false;
}

/* Re-build the various language-related arrays
 *		Note:
 *		This code existed in both lang_settings_std.php and in lang_settings.php.
 *		It has been removed from both files and inserted here, where it belongs.
 */
$languages 				= array();
$pgv_lang_use 			= array();
$pgv_lang 				= array();
$lang_short_cut 		= array();
$lang_langcode 			= array();
$pgv_language 			= array();
$confighelpfile 		= array();
$helptextfile 			= array();
$flagsfile 				= array();
$factsfile 				= array();
$factsarray 			= array();
$adminfile	 			= array();
$editorfile 			= array();
$faqlistfile	 		= array();
$extrafile		 		= array();
$pgv_lang_name 			= array();
$langcode				= array();
$ALPHABET_upper			= array();
$ALPHABET_lower			= array();
$MULTI_LETTER_ALPHABET	= array();
$DICTIONARY_SORT		= array();
$DATE_FORMAT_array		= array();
$TIME_FORMAT_array		= array();
$WEEK_START_array		= array();
$TEXT_DIRECTION_array	= array();
$NAME_REVERSE_array		= array();

foreach ($language_settings as $key => $value) {
	$languages[$key] 			= $value["pgv_langname"];
	$pgv_lang_use[$key]			= $value["pgv_lang_use"];
	$pgv_lang[$key]				= $value["pgv_lang"];
	$lang_short_cut[$key]		= $value["lang_short_cut"];
	$lang_langcode[$key]		= $value["langcode"];
	$pgv_language[$key]			= $value["pgv_language"];
	$confighelpfile[$key]		= $value["confighelpfile"];
	$helptextfile[$key]			= $value["helptextfile"];
	$flagsfile[$key]			= $value["flagsfile"];
	$factsfile[$key]			= $value["factsfile"];
	$adminfile[$key]			= $value["adminfile"];
	$editorfile[$key]			= $value["editorfile"];
	$faqlistfile[$key]			= $value["faqlistfile"];
	$extrafile[$key]			= $value["extrafile"];
	$ALPHABET_upper[$key]		= $value["ALPHABET_upper"];
	$ALPHABET_lower[$key]		= $value["ALPHABET_lower"];
	$MULTI_LETTER_ALPHABET[$key] = $value["ALPHABET_lower"];
	$DICTIONARY_SORT[$key]		= $value["ALPHABET_lower"];
	$DATE_FORMAT_array[$key]	= $value["DATE_FORMAT"];
	$TIME_FORMAT_array[$key]	= $value["TIME_FORMAT"];;
	$WEEK_START_array[$key]		= $value["WEEK_START"];
	$TEXT_DIRECTION_array[$key]	= $value["TEXT_DIRECTION"];
	$NAME_REVERSE_array[$key]	= $value["NAME_REVERSE"];

	$pgv_lang["lang_name_$key"]	= $value["pgv_lang"];

	$dDummy = $value["langcode"];
	$ct = strpos($dDummy, ";");
	while ($ct > 1) {
		$shrtcut = substr($dDummy,0,$ct);
		$dDummy = substr($dDummy,$ct+1);
		$langcode[$shrtcut]		= $key;
		$ct = strpos($dDummy, ";");
	}
}

loadLangFile("pgv_lang, pgv_editor");

print_footer();
?>

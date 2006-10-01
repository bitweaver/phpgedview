<?php
/**
 * Displays information on the PHP installation
 *
 * Provides links for administrators to get to other administrative areas of the site
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
	$Id: pgvinfo.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 */

require "config.php";
if (!userGedcomAdmin(getUserName())) {
	 header("Location: login.php?url=pgvinfo.php?action=".$action);
exit;
}

require  $confighelpfile["english"];
if (file_exists( $confighelpfile[$LANGUAGE])) require  $confighelpfile[$LANGUAGE];

if (!isset($action)) $action = "";

if ($action == "phpinfo") {
	$helpindex = "phpinfo_help";
	print_header($pgv_lang["phpinfo"]);
	 ?>
	<div class="center">
		<?php
		
		ob_start();
		  
		   phpinfo();
		   $php_info = ob_get_contents();
		      
		ob_end_clean();
		
		$php_info    = str_replace(" width=\"600\"", " width=\"\"", $php_info);
		$php_info    = str_replace("</body></html>", "", $php_info);
		$php_info    = str_replace("<table", "<table class=\"center facts_table ltr\"", $php_info);
		$php_info    = str_replace("td class=\"e\"", "td class=\"facts_value\"", $php_info);
		$php_info    = str_replace("td class=\"v\"", "td class=\"facts_value\"", $php_info);
		$php_info    = str_replace("tr class=\"v\"", "tr", $php_info);
		$php_info    = str_replace("tr class=\"h\"", "tr", $php_info);
		
		$php_info    = str_replace(";", "; ", $php_info);
		$php_info    = str_replace(",", ", ", $php_info);
		
		// Put logo in table header
		
		$logo_offset = strpos($php_info, "<td>");
		$php_info = substr_replace($php_info, "<td colspan=\"3\" class=\"facts_label03\">", $logo_offset, 4);
		$logo_width_offset = strpos($php_info, "width=\"\"");
		$php_info = substr_replace($php_info, "width=\"800\"", $logo_width_offset, 8);
		$php_info    = str_replace(" width=\"\"", "", $php_info);
		
		
		$offset          = strpos($php_info, "<table");
		$php_info	= substr($php_info, $offset);
		
		print $php_info;
		
		?>		
	</div>
	<?php
//	exit;
}

if ($action=="confighelp") {

	require "includes/functions_editlang.php";
	$helpindex = "config_help_help";
	print_header($pgv_lang["config_help"]);
	print "<h2 class=\"center\">".str2upper($pgv_lang["config_help"])."</h2><br />";
	$language_array = array();
    $language_array = read_export_file_into_array($confighelpfile[$LANGUAGE], "pgv_lang[");
    $new_language_array = array();
    $new_language_array_counter = 0;

    for ($z = 0; $z < sizeof($language_array); $z++) {
		if (isset($language_array[$z][0])) {
			if (strpos($language_array[$z][0], "_help") > 0) {
				$language_array[$z][0] = substr($language_array[$z][0], strpos($language_array[$z][0], "\"") + 1);
		        $language_array[$z][0] = substr($language_array[$z][0], 0, strpos($language_array[$z][0], "\""));
				$new_language_array[$new_language_array_counter] = $language_array[$z];
				$new_language_array_counter++;
			}
		}
	}

    print "<ol>";

    for ($z = 0; $z < sizeof($new_language_array); $z++) {
	    for ($x = 0; $x < sizeof($language_array); $x++) {
		    $dDummy = $new_language_array[$z][0];
			$dDummy = substr($dDummy, 0, strpos($dDummy, "_help"));

			if (isset($language_array[$x][0])) {
		        if (strpos($language_array[$x][0], "\"" . $dDummy . "\"") > 0) {
			        if ($new_language_array[$z][0] != "config_help") {
			            if ($new_language_array[$z][0] != "welcome_help") {
				            $new_language_array[$z][0] = $language_array[$x][1];
						}
					}
					break;
				}
			}
		}
    }
	
    for ($z = 0; $z < sizeof($new_language_array); $z++) {
		if ($new_language_array[$z][0] != "config_help" and $new_language_array[$z][0] != "welcome_help") {
			print "<li>";
			print stripslashes(print_text($new_language_array[$z][1],0,2)) . "<br /><br /></li>\r\n";
		}
    }
    print "</ol>";
}

print_footer();
?>
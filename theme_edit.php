<?php
/**
 * Modifies the themes by means of a user friendly interface
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
 * @subpackage Themes
 * @version $Id: theme_edit.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

// -- include config file

require("config.php");
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY . $factsfile[$LANGUAGE];

if (!isset($action)) $action="";
if (!isset($choose_theme)) $choose_theme="";

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)) {
	header("Location: login.php?url=theme_edit.php");
	exit;
}
$user = getUser($uname);

// -- print html header information
print_header("Theme editor");

?>
<form name="editform" method="post";">
<input type="hidden" name="oldusername" value="<?php print $uname; ?>" />
<table class="list_table <?php print $TEXT_DIRECTION; ?>">
<tr><td class="facts_label"><?php print $pgv_lang["user_theme"];print_help_link("edituser_user_theme_help", "qm");?></td><td class="facts_value" valign="top">
	<select name="choose_theme">
	<option value=""><?php print $pgv_lang["site_default"]; ?></option>
			<?php
				$themes = get_theme_names();
				foreach($themes as $indexval => $themedir) {
					print "<option value=\"".$themedir["dir"]."\"";
					if ($themedir["dir"] == $choose_theme) print " selected=\"selected\"";
					print ">".$themedir["name"]."</option>\n";
				}
			?>
		</select>
</td></tr>
</table>
<input type="submit" value="Change stylesheet" />
</form>
<?php
if (strlen($choose_theme) == 0) $choose_theme = $THEME_DIR;
$output = file($choose_theme."/style.css");
$start = FALSE;
$empty = TRUE;
$level = "";
foreach ($output as $l => $tag) {
	if (stristr($tag, ".something") || $empty == TRUE) {
		if (stristr($tag, "{") == TRUE) {
			$pos = strpos($tag, "{");
			$level = substr($tag, 0, $pos);
			$tags[$level]["id"][] = ".something {";
			$tags[$level]["names"][] = ".something";
			$tags[$level]["definitions"][] = "/*empty style to make sure that the BODY style is not ignored */";
			$tags[$level]["close"] = "}";
		}
		if (stristr($tag, "}") == TRUE) $empty = FALSE;
	}
	else {
		if (stristr($tag, "{") == TRUE && stristr($tag, "}") == TRUE) {
			$pos = strpos($tag, "{");
			$level = substr($tag, 0, $pos);
			$class = substr($tag, $pos+1);
			// Continue
// 			$items = preg_split("/;/", $tag);
// 			?><pre><?php
// 			print_r ($items);
// 			exit;
// 			?></pre><?php
			if (stristr($level, "{") != TRUE) $heading = $level."{";
			if (stristr($class, "}") == TRUE) $class = substr(trim($class), 0, -1);
			$tags[$level]["id"][] = $heading;
			$tags[$level]["names"][] = $heading;
			$tags[$level]["definitions"][] = $class;
			$tags[$level]["close"] = "}";
			$level = "";
			$start = FALSE;
		}
		else if ($start == TRUE && stristr($tag, "}") != TRUE) {
			$tagnamepos = strpos(trim($tag), ":");
			$tagname = substr(trim($tag), 0, $tagnamepos);
			$tagdef = substr(trim($tag), $tagnamepos+1);
			if (substr($tagdef,-1) == ";") $tagdef = substr($tagdef, 0, -1);
			$names[] = $tagname;
			$defs[] = $tagdef;
		}
		else if (stristr($tag, "{")){
			$start = TRUE;
			$level = trim(preg_replace("/{/", "", $tag));
			$tags[$level]["id"] = $tag;
		}
		else if (stristr($tag, "}")){
			$start = FALSE;
			if (stristr($tag, "}") == TRUE && strlen(trim($tag) > "1")) {
				$class = substr(trim($tag), 0, -1);
				$tagnamepos = strpos(trim($class), ":");
				$tagname = substr(trim($class), 0, $tagnamepos);
				$tagdef = substr(trim($class), $tagnamepos+1);
				if (substr($tagdef,-1) == ";") $tagdef = substr($tagdef, 0, -1);
			}
			else {
				$tagname = trim($tag);
				$tagdef = trim($tag);
			}
			$names[] = $tagname;
			$defs[] = $tagdef;
			$tags[$level]["names"] = $names;
			$tags[$level]["definitions"] = $defs;
			$tags[$level]["close"] = "}";
			$level = "";
			$names = array();
			$defs = array();
		}
		else {
			$level = "";
			$names = array();
			$defs = array();
			$start = FALSE;
		}
	}
}
print "<table width=\"50%\" class=\"facts_table\" border=\"3\" cellspacing=\"0\" cellpadding=\"0\">";
foreach ($tags as $l => $tag){
	print "<tr><th class=\"label\" colspan=\"3\">".trim($l)."</th></tr>";
	$i = 0;
	foreach ($tag["names"] as $n => $name) {
		print "<tr><td width=\"15%\">$name</td><td width=\"10%\">".$tag["definitions"][$n]."</td>";
		// Loop is only entered first time array is accessed
		if ($i == "0") {
			$t = 0;
			while (count($tag["names"]) > $t) {
				if (!isset($style)) $style = "style=\"";
				$style .= $tag["names"][$t].":".$tag["definitions"][$t]."; ";
				$t++;
			}
			$i = 1;
			$style .= "\">PhpGedView";

			// Build up third block
			$message = "<td rowspan=\"".count($tag["names"])."\" valign=\"top\" width=\"25%\" ";
			$message .= $style."</td></tr>\r\n";
			print $message;
			unset($style);
		}
		else print "<td width=\"25%\"></td></tr>";
	}
	print "<tr><td><br /></td><td><br /></td><td><br /></td></tr>\r\n";
}
print "</table>";
print_footer();
print "\n\t</div>\n</body>\n</html>";
?>

<?php
/**
 * Allow visitor to change the theme
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
 * @author John Finlay
 * @package PhpGedView
 * @subpackage Themes
 * @version $Id: themechange.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 */

require("config.php");

	if (!empty($_POST["mytheme"]) || !empty($_GET["mytheme"])) {
		if (isset($_POST["mytheme"])) $theme_dir = $_POST["mytheme"];
		else if (isset($_GET["mytheme"])) $theme_dir = $_GET["mytheme"];
		$_SESSION["theme_dir"] = "$theme_dir";
	}
	$uname = getUserName();
	if ($uname) {
		$olduser = getUser($uname);
		if ($olduser["editaccount"]) {
			$newuser = array();
			$newuser = $olduser;
			//deleteUser($uname, "changed");
			$newuser["theme"] = $theme_dir;
			updateUser($uname, $newuser, "changed");
			$user = $newuser;
		}
	}
	
//where do we return ?
if (isset($_POST["frompage"])) $frompage = $_POST["frompage"];
else if (isset($_GET["frompage"])) $frompage = $_GET["frompage"];
header("Location: ".urldecode($frompage));
?>

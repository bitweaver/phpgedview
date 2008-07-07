<?php
/**
 * Top menu for Standard theme
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007 John Finlay and Others
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package PhpGedView
 * @subpackage Themes
 * @version $Id: toplinks.php,v 1.1 2008/07/07 17:53:02 lsces Exp $
 */
$menubar = new MenuBar();
?>
<div style="width: 98%">
		<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]; ?>" width="99%" height="3" alt="" />
	<table id="topMenu">
		<tr>
			<?php 		
			$menu = $menubar->getGedcomMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	
			$menu = $menubar->getMygedviewMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	
			$menu = $menubar->getChartsMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	
			$menu = $menubar->getListsMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	
			$menu = $menubar->getCalendarMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	
			$menu = $menubar->getReportsMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}
			$menu = $menubar->getClippingsMenu(); 
			if ((!is_null($menu)) && ($menu->link != "")) { ?> 
				<td width="7%" valign="top"><?php $menu->printMenu(); ?></td>
			<?php 
			} 

			$menu = $menubar->getSearchMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	

			$menu = $menubar->getOptionalMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	

			$menus = $menubar->getModuleMenus(); 
			foreach($menus as $m=>$menu) {
				if($menu->link != "") {
					print "\t<td width=\"7%\" valign=\"top\">\n";
					$menu->printMenu(); 
					print "\t</td>\n";
				}
			} 

			$menu = $menubar->getHelpMenu(); 
			if($menu->link != "") {
				print "\t<td width=\"7%\" valign=\"top\">\n";
				$menu->printMenu(); 
				print "\t</td>\n";
			}	
			?>
		</tr>
	</table>
	<img align="middle" src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]; ?>" width="99%" height="3" alt="" />	
</div>
<?php include("accesskeyHeaders.php"); ?>
</div>
<div id="content">

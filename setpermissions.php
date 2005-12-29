<?php
/**
 * Use this file to try and use PHP to set permissions on the neccessary files.
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
 * @version $Id: setpermissions.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 * @package PhpGedView
 */

if (chmod("config.php", 0777)) print "Successfully set permissions for config.php.<br />";
else print "<font color=\"red\"><b>Unable to set permissions for config.php<br /></b></font>";

if (chmod("index", 0777)) print "Successfully set permissions for index directory.<br />";
else print "<font color=\"red\"><b>Unable to set permissions for index directory<br /></b></font>";

if (chmod("index/*", 0777)) print "Successfully set permissions for files in the index directory.<br />";
else print "<font color=\"red\"><b>Unable to set permissions for files in the index directory<br /></b></font>";
?>
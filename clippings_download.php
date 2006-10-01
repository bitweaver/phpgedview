<?php
/**
 * Download clipping gedcom files that could not be saved.
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
 * @version $Id: clippings_download.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 * @package PhpGedView
 * @subpackage DB
 */
require "config.php";
require $confighelpfile["english"];
if (file_exists($confighelpfile[$LANGUAGE])) require $confighelpfile[$LANGUAGE];

header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=clipping.ged");
header("Content-length: ".strlen($_SESSION['clippings']));

print_r ($_SESSION["clippings"]);


?>

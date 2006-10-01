<?php
/**
 * Allow an admin user to download the backup file.
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
 * @version $Id: downloadbackup.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 */

require "config.php";

if ((!userGedcomAdmin(getUserName()))||(empty($fname))) {
	print $pgv_lang['access_denied'];
	exit;
}

if(ini_get('zlib.output_compression')) @ini_set('zlib.output_compression', 'Off');

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=$fname");
header("Content-length: ".filesize($INDEX_DIRECTORY.$fname));
header("Content-Transfer-Encoding: binary");
readfile($INDEX_DIRECTORY.basename($fname));
exit();
?>
<?php
/**
 * Upgrade datastore from PGV 3.3 to 4.0
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003	John Finlay and Others
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
 * @package    PhpGedView
 * @subpackage	DB
 * @version    $Id: upgrade33-40.php,v 1.3 2008/07/07 18:01:13 lsces Exp $
 */

require "config.php";
require_once("includes/functions_import.php");

if (!PGV_USER_IS_ADMIN) {
	header("Location: login.php?url=upgrade33-40.php");
	exit;
}

print_header("UPGRADE 3.3 to 4.0");

print "<h2>Upgrading database.</h2>\n";

//-- make sure the gedcoms have an ID associated
if (empty($gGedcom->mGEDCOMId)) { 
	$i = 1;
	foreach($gGedcom as $ged=>$gedarray) {
	$gGedcom[$ged]["id"] = $i;
	$i++;
	}
	store_gedcoms();
	
	print "New GEDCOM IDs stored<br />\n";
}

//-- get a list of the current tables from the database
$tables = $DBCONN->getListOf('tables');
// NOTE: Check if the media table exists
if (in_array($TBLPREFIX."media", $tables)) $media_table = true;
else $media_table = false;
// NOTE: Check if the media_mapping table exists
if (in_array($TBLPREFIX."media_mapping", $tables)) $media_mapping = true;
else $media_mapping = false;

//-- print an error message to SQLite users
if ($DBTYPE=='sqlite') {

	print "<br /><br />Collecting user information<br />\n";

	print "Dropping users table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."users";
	$res = dbquery($sql);

	print "Dropping dates table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."dates";
	$res = dbquery($sql);

	print "Dropping individuals table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."individuals";
	$res = dbquery($sql);

	print "Dropping families table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."families";
	$res = dbquery($sql);

	print "Dropping sources table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."sources";
	$res = dbquery($sql);

	print "Dropping other table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."other";
	$res = dbquery($sql);

	print "Dropping places table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."places";
	$res = dbquery($sql);

	print "Dropping placelinks table<br />";
	$sql = "DROP TABLE ".$TBLPREFIX."placelinks";
	$res = dbquery($sql);

	if ($media_table) {
		print "Dropping media table<br />";
		$sql = "DROP TABLE ".$TBLPREFIX."media";
		$res = dbquery($sql);
	}

	if ($media_mapping) {
		print "Dropping media mapping table<br />";
		$sql = "DROP TABLE ".$TBLPREFIX."media_mapping";
		$res = dbquery($sql);
	}

	print "<br />Recreating tables<br />";
	checkTableExists();
	setup_database();
	cleanup_database();

	print "<br />Storing user information<br />";
	foreach(get_all_users() as $user_id=>$user_name) {
		print "Storing <i>".$user_name."</i> user information<br />";
		$fullname=get_user_setting($user_id, 'fullname');
		if (preg_match('/(.*) (.*)$/', $fullname, $match)) {
			set_user_setting($user_id, 'firstname', trim($match[1]));
			set_user_setting($user_id, 'lastname',  trim($match[2]));
		} else {
			set_user_setting($user_id, 'firstname', $fullname);
			set_user_setting($user_id, 'lastname',  $fullname);
		}
	}
}
else {
		//-- drop all the tables and recreate them
        create_dates_table();
		create_individuals_table();
		create_families_table();
		create_individuals_table();
		create_sources_table();
		create_other_table();
		create_placelinks_table();
		create_places_table();
		create_names_table();
		create_remotelinks_table();
                create_media_table();
                create_media_mapping_table();
		create_nextid_table();
}


print "<br /><br />If there were no errors above, then your database has been successfully upgraded.";
print "<br /><br />You should now reimport your GEDCOM files by going to the <a href=\"editgedcoms.php\">Manage Gedcoms and Edit Privacy</a> page and selecting the import link next to each GEDCOM file.";

print_footer();

?>

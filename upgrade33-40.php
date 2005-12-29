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
 * @version    $Id: upgrade33-40.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

require "config.php";

if (!userIsAdmin(getUserName())) {
	header("Location: login.php?url=upgrade33-40.php");
	exit;
}

print_header("UPGRADE 3.3 to 4.0");

print "<h2>Upgrading database.</h2>\n";

//-- make sure the gedcoms have an ID associated
$i = 1;
foreach($GEDCOMS as $ged=>$gedarray) {
	$GEDCOMS[$ged]["id"] = $i;
	$i++;
}
store_gedcoms();

print "New GEDCOM IDs stored<br />\n";

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
	$users = getUsers();

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
	foreach($users as $username=>$user) {
		print "Storing <i>".$username."</i> user information<br />";
		$ct = preg_match('/(.*) (.*)$/', $user['fullname'], $match);
		if ($ct>0) {
			$user['firstname'] = trim($match[1]);
			$user['lastname'] = trim($match[2]);
		}
		else {
			$user['firstname'] = $user['fullname'];
			$user['lastname'] = $user['fullname'];
		}
		addUser($user);
	}
}
else {

if (in_array($TBLPREFIX."dates", $tables)) {
	// NOTE: Check if the dates column exists
	$sql = "describe ".$TBLPREFIX."dates";
	$res = dbquery($sql);
	$type_exists = false;
	$has_d_mon = false;
	while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		if ($row["field"] == "d_type") {
			$type_exists = true;
		}
		if ($row['field'] == "d_mon") {
			$has_d_mon = true;
		}
	}

	if (!$type_exists) {
		$sql = "ALTER TABLE ".$TBLPREFIX."dates add column d_type varchar (13) NULL  after d_file";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully added column d_type to ".$TBLPREFIX."dates table\n";
		$sql = "CREATE INDEX date_type ON ".$TBLPREFIX."dates (d_type)";
		$tempsql = dbquery($sql);
		$res =& $tempsql;
	}

	if (!$has_d_mon) {
		$sql = "ALTER TABLE ".$TBLPREFIX."dates add column d_mon INT after d_month";
		$res = dbquery($sql);
		$sql = "ALTER TABLE ".$TBLPREFIX."dates add column d_datestamp INT after d_year";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully added column d_mon to ".$TBLPREFIX."dates table\n";
		$sql = "CREATE INDEX date_mon ON ".$TBLPREFIX."dates (d_mon)";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=01 WHERE d_month LIKE 'JAN'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=02 WHERE d_month LIKE 'FEB'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=03 WHERE d_month LIKE 'MAR'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=04 WHERE d_month LIKE 'APR'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=05 WHERE d_month LIKE 'MAY'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=06 WHERE d_month LIKE 'JUN'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=07 WHERE d_month LIKE 'JUL'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=08 WHERE d_month LIKE 'AUG'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=09 WHERE d_month LIKE 'SEP'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=10 WHERE d_month LIKE 'OCT'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=11 WHERE d_month LIKE 'NOV'";
		$res = dbquery($sql);
		$sql = "UPDATE ".$TBLPREFIX."dates SET d_mon=12 WHERE d_month LIKE 'DEC'";
		$res = dbquery($sql);
	}
}

	print "<br />\n";
	//-- alter the file fields
	$sql = "ALTER TABLE ".$TBLPREFIX."individuals CHANGE i_file i_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."individuals table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."families CHANGE f_file f_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."families table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."sources CHANGE s_file s_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."sources table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."other CHANGE o_file o_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."other table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."places CHANGE p_file p_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."places table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."dates CHANGE d_file d_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."dates table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."names CHANGE n_file n_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."names table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."placelinks CHANGE pl_file pl_file INT";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."placelinks table\n";
	if ($media_table) {
		$sql = "ALTER TABLE ".$TBLPREFIX."media CHANGE m_gedfile m_gedfile INT";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully altered gedfile field in ".$TBLPREFIX."media table\n";
		$sql = "ALTER TABLE ".$TBLPREFIX."media CHANGE m_file m_file varchar(255)";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully altered file field in ".$TBLPREFIX."media table\n";
		$sql = "ALTER TABLE ".$TBLPREFIX."media CHANGE m_titl m_titl varchar(255)";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully altered titl field in ".$TBLPREFIX."media table\n";
	}
	if ($media_mapping) {
		$sql = "describe ".$TBLPREFIX."media_mapping";
		$res = dbquery($sql);
		while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			if ($row["field"] == "m_indi") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_indi mm_gid varchar(15) NOT NULL default ''";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_indi to mm_gid in ".$TBLPREFIX."media_mapping table\n";
			}
			if ($row["field"] == "m_gid") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_gid mm_gid varchar(15) NOT NULL default ''";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_gid to mm_gid in ".$TBLPREFIX."media_mapping table\n";
			}
			if ($row["field"] == "m_id") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_id mm_id int(11)";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_id to mm_id in ".$TBLPREFIX."media_mapping table\n";
			}
			if ($row["field"] == "m_media") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_media mm_media varchar(15) NOT NULL default ''";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_media to mm_media in ".$TBLPREFIX."media_mapping table\n";
			}
			if ($row["field"] == "m_order") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_order mm_order int(11) NOT NULL default '0'";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_order to mm_order in ".$TBLPREFIX."media_mapping table\n";
			}
			if ($row["field"] == "m_gedfile") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_gedfile mm_gedfile int(11) default NULL";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_gedfile to mm_gedfile in ".$TBLPREFIX."media_mapping table\n";
			}
			if ($row["field"] == "m_gedrec") {
				$sql = "ALTER TABLE ".$TBLPREFIX."media_mapping CHANGE m_gedrec mm_gedrec text";
				$resmm = dbquery($sql);
				if ($resmm) print "<br />Successfully changed column m_gedrec to mm_gedrec in ".$TBLPREFIX."media_mapping table\n";
			}
		}
	}
	//-- alter the id field
	$sql = "ALTER TABLE ".$TBLPREFIX."individuals CHANGE i_id i_id VARCHAR(255)";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."individuals table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."families CHANGE f_id f_id VARCHAR(255)";
	$res = dbquery($sql);
	$sql = "ALTER TABLE ".$TBLPREFIX."families CHANGE f_husb f_husb VARCHAR(255)";
	$res = dbquery($sql);
	$sql = "ALTER TABLE ".$TBLPREFIX."families CHANGE f_wife f_wife VARCHAR(255)";
	$res = dbquery($sql);
	$sql = "ALTER TABLE ".$TBLPREFIX."families CHANGE f_chil f_chil TEXT";
	$res = dbquery($sql);
	$sql = "describe ".$TBLPREFIX."families";
	$chilres = dbquery($sql);
	$f_numchil = false;
	while($row =& $chilres->fetchRow(DB_FETCHMODE_ASSOC)){
		if ($row["field"] == "f_numchil") {
			$f_numchil = true;
			break;
		}

	}
	if (!$f_numchil) {
		$fsql = "ALTER TABLE ".$TBLPREFIX."families ADD f_numchil INT";
		$fres =& dbquery($fsql);
		if ($fres) print "<br />Successfully added f_numchil colomn to ".$TBLPREFIX."families table\n";
	}
	// if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."families table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."sources CHANGE s_id s_id VARCHAR(255)";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."sources table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."other CHANGE o_id o_id VARCHAR(255)";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."other table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."dates CHANGE d_gid d_gid VARCHAR(255)";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."dates table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."names CHANGE n_gid n_gid VARCHAR(255)";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."names table\n";
	$sql = "ALTER TABLE ".$TBLPREFIX."placelinks CHANGE pl_gid pl_gid VARCHAR(255)";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully altered id field in ".$TBLPREFIX."placelinks table\n";

foreach($GEDCOMS as $ged=>$gedarray) {
	$sql = "UPDATE ".$TBLPREFIX."individuals SET i_file='".$gedarray["id"]."' WHERE i_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."individuals table\n";
	$sql = "UPDATE ".$TBLPREFIX."families SET f_file='".$gedarray["id"]."' WHERE f_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."families table\n";
	$sql = "UPDATE ".$TBLPREFIX."sources SET s_file='".$gedarray["id"]."' WHERE s_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."sources table\n";
	$sql = "UPDATE ".$TBLPREFIX."other SET o_file='".$gedarray["id"]."' WHERE o_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."other table\n";
	$sql = "UPDATE ".$TBLPREFIX."places SET p_file='".$gedarray["id"]."' WHERE p_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."places table\n";
	$sql = "UPDATE ".$TBLPREFIX."placelinks SET pl_file='".$gedarray["id"]."' WHERE pl_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."placelinks table\n";
	$sql = "UPDATE ".$TBLPREFIX."names SET n_file='".$gedarray["id"]."' WHERE n_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."names table\n";
	$sql = "UPDATE ".$TBLPREFIX."dates SET d_file='".$gedarray["id"]."' WHERE d_file='".$DBCONN->escapeSimple($ged)."'";
	$res = dbquery($sql);
	if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."dates table\n";
	if ($media_table) {
		$sql = "UPDATE ".$TBLPREFIX."media SET m_gedfile='".$gedarray["id"]."' WHERE m_gedfile='".$DBCONN->escapeSimple($ged)."'";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."media table\n";
	}
	if ($media_mapping) {
		$sql = "UPDATE ".$TBLPREFIX."media_mapping SET mm_gedfile='".$gedarray["id"]."' WHERE mm_gedfile='".$DBCONN->escapeSimple($ged)."'";
		$res = dbquery($sql);
		if ($res) print "<br />Successfully changed gedcom file to id in ".$TBLPREFIX."media_mapping table\n";
	}
}

//-- create the table for storing the next ID number
if (!in_array($TBLPREFIX."nextid", $tables)) {
	$sql = "CREATE TABLE ".$TBLPREFIX."nextid (ni_id INT, ni_type VARCHAR(30), ni_gedfile INT)";
	$res = dbquery($sql);
}
}

print "<br /><br />If there were no errors above, then your database has been successfully upgraded.";
if ($DBTYPE=="sqlite") print "<br /><br />You should now reimport your GEDCOM files by going to the <a href=\"editgedcoms.php\">Manage Gedcoms and Edit Privacy</a> page and selecting the import link next to each GEDCOM file.";

print_footer();

?>

<?php
/**
 * MyGedView page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
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
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: index.php,v 1.8 2006/10/04 12:07:54 lsces Exp $
 */

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
if (!isset($CONFIGURED)) {
	print "Unable to include the config.php file.  Make sure that . is in your PHP include path in the php.ini file.";
	exit;
}

require(PHPGEDVIEW_PKG_PATH.'languages/lang.en.php');

if (isset($_SESSION["timediff"])) $time = time()-$_SESSION["timediff"];
else $time = time();
$day = date("j", $time);
$month = date("M", $time);
$year = date("Y", $time);
if ($USE_RTL_FUNCTIONS) {
	//-------> Today's Hebrew Day with Gedcom Month
	$datearray = array();
 	$datearray[0]["day"]   = $day;
 	$datearray[0]["mon"]   = $monthtonum[str2lower(trim($month))];
 	$datearray[0]["year"]  = $year;
 	$datearray[0]["month"] = $month;

    $date   = gregorianToJewishGedcomDate($datearray);
    $hDay   = $date[0]["day"];
    $hMonth = $date[0]["month"];
    $hYear	= $date[0]["year"];

//    $currhDay   = $hDay;
//    $currhMon   = trim($date[0]["month"]);
//    $currhMonth = $monthtonum[str2lower($currhMon)];
    $currhYear 	= $hYear;
}

if (!isset($action)) $action="";

$uname = $gBitUser->mUsername;
if ( !$gBitUser->isValid() ) {
	if (!empty($command)) {
		if ($command=="user") {
			header("Location: login.php?help_message=mygedview_login_help&url=".urlencode("index.php?command=user"));
			exit;
		}
	}
	$command="gedcom";
}
// else $user = fillUser($gBitUser);

if (empty($command)) $command="user";

if (!empty($uname)) {
	//-- add favorites action
	if (($action=="addfav")&&(!empty($gid))) {
		$gid = strtoupper($gid);
		if (!isset($favnote)) $favnote = "";
		$indirec = find_gedcom_record($gid);
		$ct = preg_match("/0 @(.*)@ (.*)/", $indirec, $match);
		if ($indirec && $ct>0) {
			$favorite = array();
			if (!isset($favtype)) {
				if ($command=="user") $favtype = "user";
				else $favtype = "gedcom";
			}
			if ($favtype=="gedcom") $favtype = $GEDCOM;
			else $favtype=$uname;
			$favorite["username"] = $favtype;
			$favorite["gid"] = $gid;
			$favorite["type"] = trim($match[2]);
			$favorite["file"] = $GEDCOM;
			$favorite["url"] = "";
			$favorite["note"] = $favnote;
			$favorite["title"] = "";
			addFavorite($favorite);
		}
	}
	if (($action=="addfav")&&(!empty($url))) {
		if (!isset($favnote)) $favnote = "";
		if (empty($favtitle)) $favtitle = $url;
		$favorite = array();
		if (!isset($favtype)) {
			if ($command=="user") $favtype = "user";
			else $favtype = "gedcom";
		}
		if ($favtype=="gedcom") $favtype = $GEDCOM;
		else $favtype=$uname;
		$favorite["username"] = $favtype;
		$favorite["gid"] = "";
		$favorite["type"] = "URL";
		$favorite["file"] = $GEDCOM;
		$favorite["url"] = $url;
		$favorite["note"] = $favnote;
		$favorite["title"] = $favtitle;
		addFavorite($favorite);
	}
	if (($action=="deletefav")&&(isset($fv_id))) {
		deleteFavorite($fv_id);
	}
}

if ($command=="user") {
	$helpindex = "index_myged_help";
	print_header($pgv_lang["mygedview"]);
}
else {
	print_header($GEDCOMS[$GEDCOM]["title"]);
}
?>
<script language="JavaScript" type="text/javascript">
<!--
	function refreshpage() {
		window.location = 'index.php?command=<?php print $command; ?>';
	}
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
//-->
</script>
<?php
//-- start of main content section
print "<table width=\"100%\"><tr><td>";		// This is needed so that page footers print in the right place
if ($command=="user") {
	print "<div align=\"center\" style=\"width: 99%;\">";
	print "<h1>".$pgv_lang["mygedview"]."</h1>";
	print $pgv_lang["mygedview_desc"];
	print "<br /><br /></div>\n";
}
//-- end of main content section

print "</td></tr></table><br />";		// Close off that table

if (($command=="user")) {
	print "<div align=\"center\" style=\"width: 99%;\">";
	print_help_link("mygedview_customize_help", "qm");
	print "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=".$gBitUser->mUsername."&amp;command=user', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\">".$pgv_lang["customize_page"]."</a>\n";
	print "</div>";
}
if (($command=="gedcom")) {
	if ( $gBitUser->isAdmin() ) {
		print "<div align=\"center\" style=\"width: 99%;\">";
		print "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=$GEDCOM&amp;command=gedcom', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\">".$pgv_lang["customize_gedcom_page"]."</a>\n";
		print "</div>";
	}
}

print_footer();
?>

<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_phpgedview/index.php,v 1.3 2005/12/31 12:52:02 lsces Exp $
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * @package PhpGedView
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'phpgedview' );

// $gBitSystem->verifyFeature( 'feature_listGEDCOM' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'bit_p_view_phpgedview' );

require_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
require_once( PHPGEDVIEW_PKG_PATH.'includes/session.php' );
//vd($_REQUEST);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'last_modified_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use last_modified_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST['page'])) {
	$page = &$_REQUEST['page'];
	$offset = ($page - 1) * $maxRecords;
}
$gBitSmarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
// If we leave $_REQUEST["find"] set, it also seems to affect other places
// like the shoutbox.
	$_REQUEST["find"] = "";
} else {
	$find = '';
}
$gBitSmarty->assign_by_ref('find', $find);
// Get a list of last changes to the Wiki database
$Content = new BitGEDCOM();
$sort_mode = preg_replace( '/^user_/', 'creator_user_', $sort_mode );
$listgedcom = $Content->getList( $offset, $maxRecords, $sort_mode, $find, NULL, TRUE );
// If there're more records then assign next_offset
$cant_pages = ceil($listgedcom["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign_by_ref('pagecount', $listgedcom['cant']);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));
if ($listgedcom["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}
// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('listgedcom', $listgedcom["data"]);
//print_r($listgedcom["data"]);

// Display the template
$gBitSystem->display( 'bitpackage:phpgedview/list_gedcom.tpl');

/*
require("config.php");

if (!isset($CONFIGURED)) {
	print "Unable to include the config.php file.  Make sure that . is in your PHP include path in the php.ini file.";
	exit;
}

require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);

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

//-- make sure that they have user status before they can use this page
//-- otherwise have them login again
$uname = getUserName();
if (empty($uname)) {
	if (!empty($command)) {
		if ($command=="user") {
			header("Location: login.php?help_message=mygedview_login_help&url=".urlencode("index.php?command=user"));
			exit;
		}
	}
	$command="gedcom";
}
else $user = getUser($uname);

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
	else if ($action=="deletemessage") {
		if (isset($message_id)) {
			if (!is_array($message_id)) deleteMessage($message_id);
			else {
				foreach($message_id as $indexval => $mid) {
					if (isset($mid)) deleteMessage($mid);
				}
			}
		}
	}
	else if (($action=="deletenews")&&(isset($news_id))) {
		deleteNews($news_id);
	}
}

//-- get the blocks list
if ($command=="user") {
	$ublocks = getBlocks($uname);
	if ((count($ublocks["main"])==0) and (count($ublocks["right"])==0)) {
		$ublocks["main"][] = array("print_todays_events", "");
		$ublocks["main"][] = array("print_user_messages", "");
		$ublocks["main"][] = array("print_user_favorites", "");

		$ublocks["right"][] = array("print_welcome_block", "");
		$ublocks["right"][] = array("print_random_media", "");
		$ublocks["right"][] = array("print_upcoming_events", "");
		$ublocks["right"][] = array("print_logged_in_users", "");
	}
}
else {
	$ublocks = getBlocks($GEDCOM);
	if ((count($ublocks["main"])==0) and (count($ublocks["right"])==0)) {
		$ublocks["main"][] = array("print_gedcom_stats", "");
		$ublocks["main"][] = array("print_gedcom_news", "");
		$ublocks["main"][] = array("print_gedcom_favorites", "");
		$ublocks["main"][] = array("review_changes_block", "");

		$ublocks["right"][] = array("print_gedcom_block", "");
		$ublocks["right"][] = array("print_random_media", "");
		$ublocks["right"][] = array("print_todays_events", "");
		$ublocks["right"][] = array("print_logged_in_users", "");
	}
}

//-- Set some behaviour controls that depend on which blocks are selected
$welcome_block_present = false;
$gedcom_block_present = false;
$top10_block_present = false;
$login_block_present = false;
foreach($ublocks["right"] as $block) {
	if ($block[0]=="print_welcome_block") $welcome_block_present = true;
	if ($block[0]=="print_gedcom_block") $gedcom_block_present = true;
	if ($block[0]=="print_block_name_top10") $top10_block_present = true;
	if ($block[0]=="print_login_block") $login_block_present = true;
}
foreach($ublocks["main"] as $block) {
	if ($block[0]=="print_welcome_block") $welcome_block_present = true;
	if ($block[0]=="print_gedcom_block") $gedcom_block_present = true;
	if ($block[0]=="print_block_name_top10") $top10_block_present = true;
	if ($block[0]=="print_login_block") $login_block_present = true;
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
	function addnews(uname) {
		window.open('editnews.php?uname='+uname, '', 'top=50,left=50,width=800,height=500,resizable=1,scrollbars=1');
	}
	function editnews(news_id) {
		window.open('editnews.php?news_id='+news_id, '', 'top=50,left=50,width=800,height=500,resizable=1,scrollbars=1');
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
if (count($ublocks["main"])!=0) {
	if (count($ublocks["right"])!=0) {
		print "\t<div id=\"index_main_blocks\">\n";
	} else {
		print "\t<div id=\"index_full_blocks\">\n";
	}

	foreach($ublocks["main"] as $bindex=>$block) {
		if (isset($DEBUG)&&($DEBUG==true)) print_execution_stats();
		if (function_exists($block[0])) eval($block[0]."(false, \$block[1], \"main\", $bindex);");
	}
	print "</div>\n";
}
//-- end of main content section

//-- start of blocks section
if (count($ublocks["right"])!=0) {
	if (count($ublocks["main"])!=0) {
		print "\t<div id=\"index_small_blocks\">\n";
	} else {
		print "\t<div id=\"index_full_blocks\">\n";
	}
	foreach($ublocks["right"] as $bindex=>$block) {
		if (isset($DEBUG)&&($DEBUG==true)) print_execution_stats();
		if (function_exists($block[0])) eval($block[0]."(true, \$block[1], \"right\", $bindex);");
	}
	print "\t</div>\n";
}
//-- end of blocks section

print "</td></tr></table><br />";		// Close off that table

if (($command=="user") and (!$welcome_block_present)) {
	print "<div align=\"center\" style=\"width: 99%;\">";
	print_help_link("mygedview_customize_help", "qm");
	print "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=".getUserName()."&amp;command=user', '', 'top=50,left=10,width=1000,height=400,scrollbars=1,resizable=1');\">".$pgv_lang["customize_page"]."</a>\n";
	print "</div>";
}
if (($command=="gedcom") and (!$gedcom_block_present)) {
	if (userIsAdmin(getUserName())) {
		print "<div align=\"center\" style=\"width: 99%;\">";
		print "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=$GEDCOM&amp;command=gedcom', '', 'top=50,left=10,width=1000,height=400,scrollbars=1,resizable=1');\">".$pgv_lang["customize_gedcom_page"]."</a>\n";
		print "</div>";
	}
}
*/
?>

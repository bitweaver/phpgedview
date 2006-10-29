<?php
/**
 * Family Tree Clippings Cart
 *
 * Uses the $_SESSION["cart"] to store the ids of clippings to download
 * @TODO print a message if people are not included due to privacy
 *
 * XHTML Validated 12 Feb 2006
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2006  John Finlay and Others
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
 * @subpackage Charts
 * @version $Id: clippings.php,v 1.3 2006/10/29 16:45:27 lsces Exp $
 */

/**
 * Initialization
 */ 
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");

if (!isset($ENABLE_CLIPPINGS_CART)) $ENABLE_CLIPPINGS_CART = $PRIV_HIDE;
if ($ENABLE_CLIPPINGS_CART===true) $ENABLE_CLIPPING_CART=$PRIV_PUBLIC;
if ($ENABLE_CLIPPINGS_CART<getUserAccessLevel()) {
  header("Location: index.php");
  exit;
}

if (!isset($action)) $action="";
if (!isset($id)) $id = "";
if (!isset($remove)) $remove = "no";
if (!isset($convert)) $convert = "no";
$id = clean_input($id);

// -- print html header information
print_header($pgv_lang["clip_cart"]);
print "\r\n\t<h2>".$pgv_lang["clippings_cart"]."</h2>";


function same_group($a, $b) {
	if ($a['type']==$b['type']) return strnatcasecmp($a['id'], $b['id']);
	if ($a['type']=='source') return 1;
	if ($a['type']=='indi') return -1;
	if ($b['type']=='source') return -1;
	if ($b['type']=='indi') return 1;
	return 0;
}

function id_in_cart($id) {
	global $cart, $GEDCOM;
	$ct = count($cart);
	for($i=0; $i<$ct; $i++) {
		$temp = $cart[$i];
		if ($temp['id']==$id && $temp['gedcom']==$GEDCOM) {
			return true;
		}
	}
	return false;
}

function add_clipping($clipping) {
	global $cart, $pgv_lang, $SHOW_SOURCES, $MULTI_MEDIA, $GEDCOM;
	if (($clipping['id']==false)||($clipping['id']=="")) return false;

	if (!id_in_cart($clipping['id'])) {
		$clipping['gedcom'] = $GEDCOM;
		if ($clipping['type']=="indi") {
			if (displayDetailsById($clipping['id'])||showLivingNameById($clipping['id'])) {
				$cart[]=$clipping;
			}
			else return false;
		}
		else if ($clipping['type']=="fam") {
			$parents = find_parents($clipping['id']);
			if ((displayDetailsById($parents['HUSB'])||showLivingNameById($parents['HUSB']))&&(displayDetailsById($parents['WIFE'])||showLivingNameById($parents['WIFE']))) {
				$cart[]=$clipping;
			}
			else return false;
		}
		else {
			if (displayDetailsById($clipping['id'], strtoupper($clipping['type']))) $cart[]=$clipping;
			else return false;
		}
		//-- look in the gedcom record for any linked SOUR, NOTE, or OBJE and also add them to the
		//- clippings cart
		$gedrec = find_gedcom_record($clipping['id']);
		if ($SHOW_SOURCES>=getUserAccessLevel(getUserName())) {
			$st = preg_match_all("/\d SOUR @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$st; $i++) {
				// add SOUR
				$clipping = array();
				$clipping['type']="source";
				$clipping['id']=$match[$i][1];
				$clipping['gedcom'] = $GEDCOM;
				add_clipping($clipping);
				// add REPO
				$sourec = find_gedcom_record($match[$i][1]);
				$rt = preg_match_all("/\d REPO @(.*)@/", $sourec, $rmatch, PREG_SET_ORDER);
				for($j=0; $j<$rt; $j++) {
					$clipping = array();
					$clipping['type']="repository";
					$clipping['id']=$rmatch[$j][1];
					$clipping['gedcom'] = $GEDCOM;
					add_clipping($clipping);
				}
			}
		}
		$nt = preg_match_all("/\d NOTE @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$nt; $i++) {
			$clipping = array();
			$clipping['type']="note";
			$clipping['id']=$match[$i][1];
			$clipping['gedcom'] = $GEDCOM;
			add_clipping($clipping);
		}
		if ($MULTI_MEDIA) {
			$nt = preg_match_all("/\d OBJE @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$nt; $i++) {
				$clipping = array();
				$clipping['type']="obje";
				$clipping['id']=$match[$i][1];
				$clipping['gedcom'] = $GEDCOM;
				add_clipping($clipping);
			}
		}
	}
	return true;
}

// --------------------------------- Recursive function to traverse the tree
function add_family_descendancy($famid) {
	global $cart;

	if (!$famid) return;
	//print "add_family_descendancy(" . $famid . ")<br />";					# --------------
	$famrec = find_family_record($famid);
	if ($famrec) {
		$parents = find_parents_in_record($famrec);
		if (!empty($parents["HUSB"])) {
			$clipping = array();
			$clipping['type']="indi";
			$clipping['id']=$parents["HUSB"];
			add_clipping($clipping);
		}
		if (!empty($parents["WIFE"])) {
			$clipping = array();
			$clipping['type']="indi";
			$clipping['id']=$parents["WIFE"];
			add_clipping($clipping);
		}
		$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
		for($i=0; $i<$num; $i++) {
			$cfamids = find_sfamily_ids($smatch[$i][1]);
			if (count($cfamids)>0) {
				foreach($cfamids as $indexval => $cfamid) {
					if (!id_in_cart($cfamid)) {
						$clipping = array();
						$clipping['type']="fam";
						$clipping['id']=$cfamid;
						$ret = add_clipping($clipping);		// add the childs family
						add_family_descendancy($cfamid);	// recurse on the childs family
					}
				}
			}
			else {
				$clipping = array();
				$clipping['type']="indi";
				$clipping['id']=$smatch[$i][1];
				add_clipping($clipping);
			}
		}
	}
}

function add_family_members($famid) {
	global $cart;
	$parents = find_parents($famid);
	if (!empty($parents["HUSB"])) {
		$clipping = array();
		$clipping['type']="indi";
		$clipping['id']=$parents["HUSB"];
		add_clipping($clipping);
	}
	if (!empty($parents["WIFE"])) {
		$clipping = array();
		$clipping['type']="indi";
		$clipping['id']=$parents["WIFE"];
		add_clipping($clipping);
	}
	$famrec = find_family_record($famid);
	if ($famrec) {
		$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
		for($i=0; $i<$num; $i++) {
			$clipping = array();
			$clipping['type']="indi";
			$clipping['id']=$smatch[$i][1];
			add_clipping($clipping);
		}
	}
}

//-- recursively adds direct-line ancestors to cart
function add_ancestors_to_cart($pid) {
	global $cart;
	$famids = find_family_ids($pid);
	if (count($famids)>0) {
		foreach($famids as $indexval => $famid) {
			$clipping = array();
			$clipping['type']="fam";
			$clipping['id']=$famid;
			$ret = add_clipping($clipping);
			if ($ret) {
				$parents = find_parents($famid);
				if (!empty($parents["HUSB"])) {
					$clipping = array();
					$clipping['type']="indi";
					$clipping['id']=$parents["HUSB"];
					add_clipping($clipping);
					add_ancestors_to_cart($parents["HUSB"]);
				}
				if (!empty($parents["WIFE"])) {
					$clipping = array();
					$clipping['type']="indi";
					$clipping['id']=$parents["WIFE"];
					add_clipping($clipping);
					add_ancestors_to_cart($parents["WIFE"]);
				}
			}
		}
	}
}

//-- recursively adds direct-line ancestors and their families to the cart
function add_ancestors_to_cart_families($pid) {
	global $cart;
	$famids = find_family_ids($pid);
	if (count($famids)>0) {
		foreach($famids as $indexval => $famid) {
			$clipping = array();
			$clipping['type']="fam";
			$clipping['id']=$famid;
			$ret = add_clipping($clipping);
			if ($ret) {
				$parents = find_parents($famid);
				if (!empty($parents["HUSB"])) {
					$clipping = array();
					$clipping['type']="indi";
					$clipping['id']=$parents["HUSB"];
					$ret = add_clipping($clipping);
					add_ancestors_to_cart_families($parents["HUSB"]);
				}
				if (!empty($parents["WIFE"])) {
					$clipping = array();
					$clipping['type']="indi";
					$clipping['id']=$parents["WIFE"];
					$ret = add_clipping($clipping);
					add_ancestors_to_cart_families($parents["WIFE"]);
				}
				$famrec = find_family_record($famid);
				if ($famrec) {
					$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
					for($i=0; $i<$num; $i++) {
						$clipping = array();
						$clipping['type']="indi";
						$clipping['id']=$smatch[$i][1];
						add_clipping($clipping);
					}
				}
			}
		}
	}
}

//---------------------------- End function definition

if ($action=='add') {
	if ($type=='fam') {
		print "\r\n<form action=\"clippings.php\" method=\"get\">\r\n".$pgv_lang["which_links"]."<br />";
		print "\r\n\t<input type=\"hidden\" name=\"id\" value=\"$id\" />";
		print "\r\n\t<input type=\"hidden\" name=\"type\" value=\"$type\" />";
		print "\r\n\t<input type=\"hidden\" name=\"action\" value=\"add1\" />";
		print "\r\n\t<input type=\"radio\" name=\"others\" checked value=\"none\" />".$pgv_lang["just_family"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"parents\" />".$pgv_lang["parents_and_family"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"members\" />".$pgv_lang["parents_and_child"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"descendants\" />".$pgv_lang["parents_desc"]."<br />";
		print "\r\n\t<input type=\"submit\" value=\"".$pgv_lang["continue"]."\" /><br />\r\n\t</form>";
	}
	else if ($type=='indi') {
		print "\r\n<form action=\"clippings.php\" method=\"get\">\r\n".$pgv_lang["which_p_links"]."<br />";
		print "\r\n\t<input type=\"hidden\" name=\"id\" value=\"$id\" />";
		print "\r\n\t<input type=\"hidden\" name=\"type\" value=\"$type\" />";
		print "\r\n\t<input type=\"hidden\" name=\"action\" value=\"add1\" />";
		print "\r\n\t<input type=\"radio\" name=\"others\" checked value=\"none\" />".$pgv_lang["just_person"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"parents\" />".$pgv_lang["person_parents_sibs"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"ancestors\" />".$pgv_lang["person_ancestors"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"ancestorsfamilies\" />".$pgv_lang["person_ancestor_fams"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"members\" />".$pgv_lang["person_spouse"]."<br />";
		print "\r\n\t<input type=\"radio\" name=\"others\" value=\"descendants\" />".$pgv_lang["person_desc"]."<br />";
		print "\r\n\t<input type=\"submit\" value=\"".$pgv_lang["continue"]."\" /><br />\r\n\t</form>";
	}
	else {
		$action='add1';
	}
}

if ($action=='add1') {
	$clipping = array();
	$clipping['type']=$type;
	$clipping['id']=$id;
	$clipping['gedcom'] = $GEDCOM;
	$ret = add_clipping($clipping);
	if ($ret) {
		if ($type=='fam') {
			if ($others=='parents') {
				$parents = find_parents($id);
				if (!empty($parents["HUSB"])) {
					$clipping = array();
					$clipping['type']="indi";
					$clipping['id']=$parents["HUSB"];
					$ret = add_clipping($clipping);
				}
				if (!empty($parents["WIFE"])) {
					$clipping = array();
					$clipping['type']="indi";
					$clipping['id']=$parents["WIFE"];
					$ret = add_clipping($clipping);
				}
			}
			else if ($others=="members") {
				add_family_members($id);
			}
			else if ($others=="descendants") {
				add_family_descendancy($id);
			}
		}
		else if ($type=='indi') {
			if ($others=='parents') {
				$famids = find_family_ids($id);
				foreach($famids as $indexval => $famid) {
					$clipping = array();
					$clipping['type']="fam";
					$clipping['id']=$famid;
					$ret = add_clipping($clipping);
					if ($ret) add_family_members($famid);
				}
			}
			else if ($others=='ancestors') {
				add_ancestors_to_cart($id);
			}
			else if ($others=='ancestorsfamilies') {
				add_ancestors_to_cart_families($id);
			}
			else if ($others=='members') {
				$famids = find_sfamily_ids($id);
				foreach($famids as $indexval => $famid) {
					$clipping = array();
					$clipping['type']="fam";
					$clipping['id']=$famid;
					$ret = add_clipping($clipping);
					if ($ret) add_family_members($famid);
				}
			}
			else if ($others=='descendants') {
				$famids = find_sfamily_ids($id);
				foreach($famids as $indexval => $famid) {
					$clipping = array();
					$clipping['type']="fam";
					$clipping['id']=$famid;
					$ret = add_clipping($clipping);
					if ($ret) add_family_descendancy($famid);
				}
			}
		}
	}
}
else if($action=='remove') {
	$ct = count($cart);
	for($i=$item+1; $i<$ct; $i++) {
		$cart[$i-1] = $cart[$i];
	}
	unset($cart[$ct-1]);
}
else if($action=='empty') {
	$cart = array();
	$_SESSION["clippings"] = "";
}
else if($action=='download') {
	$path = substr($SCRIPT_NAME, 0, strrpos($SCRIPT_NAME, "/"));
	if (empty($path)) $path="/";
	if ($path[strlen($path)-1]!="/") $path .= "/";
	if ($SERVER_URL[strlen($SERVER_URL)-1] == "/")
	{
	  $dSERVER_URL = substr($SERVER_URL, 0, strlen($SERVER_URL) - 1);
	}
	else $dSERVER_URL = $SERVER_URL;
	usort($cart, "same_group");
	$media = array();
	$mediacount=0;
	$ct = count($cart);
	$filetext = "0 HEAD\r\n1 SOUR PhpGedView\r\n2 NAME PhpGedView Online Genealogy\r\n2 VERS $VERSION $VERSION_RELEASE\r\n1 DEST DISKETTE\r\n1 DATE ".date("j M Y")."\r\n2 TIME ".date("h:i:s")."\r\n";
	$filetext .= "1 GEDC\r\n2 VERS 5.5\r\n2 FORM LINEAGE-LINKED\r\n1 CHAR $CHARACTER_SET\r\n";
	$head = find_gedcom_record("HEAD");
	$placeform = trim(get_sub_record(1, "1 PLAC", $head));
	if (!empty($placeform)) $filetext .= $placeform."\r\n";
//	else $filetext .= "1 PLAC\r\n2 FORM ".$pgv_lang["default_form"]."\r\n";
	else $filetext .= "1 PLAC\r\n2 FORM "."City, County, State/Province, Country"."\r\n";
	if ($convert=="yes") {
		$filetext = preg_replace("/UTF-8/", "ANSI", $filetext);
		$filetext = utf8_decode($filetext);
	}
	for($i=0; $i<$ct; $i++)
	{
		$clipping = $cart[$i];
		if ($clipping['gedcom']==$GEDCOM) {
			$record = find_gedcom_record($clipping['id']);
			$record = privatize_gedcom($record);
			$record = remove_custom_tags($record, $remove);
			if ($convert=="yes") $record = utf8_decode($record);
			if ($clipping['type']=='indi') {
				$ft = preg_match_all("/1 FAMC @(.*)@/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++) {
					if (!id_in_cart($match[$k][1])) {
						$record = preg_replace("/1 FAMC @".$match[$k][1]."@.*/", "", $record);
					}
				}
				$ft = preg_match_all("/1 FAMS @(.*)@/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++) {
					if (!id_in_cart($match[$k][1])) {
						$record = preg_replace("/1 FAMS @".$match[$k][1]."@.*/", "", $record);
					}
				}
				$ft = preg_match_all("/\d FILE (.*)/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++) {
					$filename = extract_filename(trim($match[$k][1]));
					$media[$mediacount]=$filename;
					$filename = substr($match[$k][1], strrpos($match[$k][1], "\\"));
					$mediacount++;
					$record = preg_replace("|(\d FILE )".addslashes($match[$k][1])."|", "$1".$filename, $record);
				}
				$filetext .= trim($record)."\r\n";
				$filetext .= "1 SOUR @SPGV1@\r\n";
				$filetext .= "2 PAGE ".$dSERVER_URL."/individual.php?pid=".$clipping['id']."\r\n";
				$filetext .= "2 DATA\r\n";
				$filetext .= "3 TEXT ".$pgv_lang["indi_downloaded_from"]."\r\n";
				$filetext .= "4 CONT ".$dSERVER_URL."/individual.php?pid=".$clipping['id']."\r\n";
			}
			else if ($clipping['type']=='fam') {
				$ft = preg_match_all("/1 CHIL @(.*)@/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++) {
					 if (!id_in_cart($match[$k][1])) {
					   /* if the child is not in the list delete the record of it */
					   $record = preg_replace("/1 CHIL @".$match[$k][1]."@.*/", "", $record);
					 }
				}

				$ft = preg_match_all("/1 HUSB @(.*)@/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++)
				{
					 if (!id_in_cart($match[$k][1]))
					 {
					   /* if the husband is not in the list delete the record of him */
					   $record = preg_replace("/1 HUSB @".$match[$k][1]."@.*/", "", $record);
					 }
				}

				$ft = preg_match_all("/1 WIFE @(.*)@/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++)
				{
					 if (!id_in_cart($match[$k][1]))
					 {
					   /* if the wife is not in the list delete the record of her */
					   $record = preg_replace("/1 WIFE @".$match[$k][1]."@.*/", "", $record);
					 }
				}

				$ft = preg_match_all("/\d FILE (.*)/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++) {
					$filename = extract_filename($match[$k][1]);
					 	$media[$mediacount]=$filename;
					 		$mediacount++;
				   	 	$record = preg_replace("@(\d FILE )".addslashes($match[$k][1])."@", "$1".$filename, $record);
				}

				$filetext .= trim($record)."\r\n";
				$filetext .= "1 SOUR @SPGV1@\r\n";
				$filetext .= "2 PAGE ".$dSERVER_URL.$path."family.php?famid=".$clipping['id']."\r\n";
				$filetext .= "2 DATA\r\n";
				$filetext .= "3 TEXT ".$pgv_lang["family_downloaded_from"]."\r\n";
				$filetext .= "4 CONT ".$dSERVER_URL."/family.php?famid=".$clipping['id']."\r\n";
			}
			else if($clipping['type']=="source") {
				$filetext .= trim($record)."\r\n";
				$filetext .= "1 NOTE ".$pgv_lang["source_downloaded_from"]."\r\n";
				$filetext .= "2 CONT ".$dSERVER_URL."/source.php?sid=".$clipping['id']."\r\n";
			}
			else {
				$ft = preg_match_all("/\d FILE (.*)/", $record, $match, PREG_SET_ORDER);
				for ($k=0; $k<$ft; $k++) {
					$filename = extract_filename(trim($match[$k][1]));
					$media[$mediacount]=$filename;
					$filename = substr($match[$k][1], strrpos($match[$k][1], "\\"));
					$mediacount++;
					$record = preg_replace("|(\d FILE )".addslashes($match[$k][1])."|", "$1".$filename, $record);
				}
				$filetext .= trim($record)."\r\n";
			}
		}
	}
	$filetext .= "0 @SPGV1@ SOUR\r\n";
	$tuser = getUser($CONTACT_EMAIL);
	if ($tuser) {
		$filetext .= "1 AUTH ".$tuser["firstname"]." ".$tuser["lastname"]."\r\n";
	}
	$filetext .= "1 TITL ".$HOME_SITE_TEXT."\r\n";
	$filetext .= "1 ABBR ".$HOME_SITE_TEXT."\r\n";
	$filetext .= "1 PUBL ".$HOME_SITE_URL."\r\n";
	$filetext .= "0 TRLR\r\n";
	//-- make sure the gedcom doesn't have any empty lines
	$filetext = preg_replace("/(\r?\n)+/", "\r\n", $filetext);
	//-- make sure DOS line endings are used
	$filetext = preg_replace("/\r?\n/", "\r\n", $filetext);

	$_SESSION["clippings"] = $filetext;
	print "\r\n\t<br /><br />".$pgv_lang["download"]."<br /><br /><ul><li>".$pgv_lang["gedcom_file"]."</li><ul><li><a href=\"clippings_download.php\">clipping.ged</a></li></ul><br />";
	if ($mediacount>0) {
		// -- create zipped media file
		print "<li>".$pgv_lang["media_files"]."</li><ul>";
		for($m=0; $m<$mediacount; $m++) {
			print "<li><a href=\"".$MEDIA_DIRECTORY."$media[$m]\">".substr($media[$m], strrpos($media[$m], "/"))."</a></li>";
		}
		print "</ul>";
	}
	print "</ul><br /><br />";
}
$ct = count($cart);
if($ct==0) {

	// -- new lines, added by Jans, to display helptext when cart is empty
	if ($action!='add') {
		require $helptextfile["english"];
		if (file_exists($helptextfile[$LANGUAGE])) require $helptextfile[$LANGUAGE];
		print_text("help_clippings.php");
	}
	// -- end new lines
	print "\r\n\t\t<br /><br />".$pgv_lang["cart_is_empty"]."<br /><br />";
}
else {
?>
	<table class="list_table">
		<tr>
			<td class="list_label"><?php echo $pgv_lang["type"]?></td>
			<td class="list_label"><?php echo $pgv_lang["id"]?></td>
			<td class="list_label"><?php echo $pgv_lang["name_description"]?></td>
			<td class="list_label"><?php echo $pgv_lang["remove"]?></td>
		</tr>
<?php
	for ($i=0; $i<$ct; $i++) {
		$clipping = $cart[$i];
		$tag = strtoupper(substr($clipping['type'],0,4)); // source => SOUR
		//print_r($clipping);
		//-- don't show clippings from other gedcoms
		if ($clipping['gedcom']==$GEDCOM) {
			if ($tag=='INDI') $icon = "indis";
			if ($tag=='FAM' ) $icon = "sfamily";
			if ($tag=='SOUR') $icon = "source";
			if ($tag=='REPO') $icon = "repository";
			if ($tag=='NOTE') $icon = "note";
			if ($tag=='OBJE') $icon = "media";
			?>
			<tr><td class="list_value"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES[$icon]["small"];?>" border="0" alt="<?php echo $tag;?>" title="<?php echo $tag;?>" /></td>
			<td class="list_value"><?php echo $clipping['id']?></td>
			<td class="list_value">
			<?php
			$id_ok = true;
			if ($tag=='INDI') {
			  $indirec = find_person_record($clipping['id']);
			  if (displayDetails($indirec) or showLivingName($indirec))
			  {
			    $id_ok = true;
			  }
			  else
			  {
			    $id_ok = false;
			  }
			  if ($id_ok) $dName = get_sortable_name($clipping['id']); else $dName = $pgv_lang["person_private"];
			  	$names = preg_split("/,/", $dName);
				$dName = check_NN($names);
			  	print "<a href=\"individual.php?pid=".$clipping['id']."\">".PrintReady($dName)."</a>";
			}
			if ($tag=='FAM') {
			    $famrec = find_family_record($clipping['id']);

			    $husb_ok = true;
			    $ct01 = preg_match("/1 HUSB @(.*)@/", $famrec, $match);
			    if ($ct01 > 0)
			    {
			      $indirec = find_person_record($match[1]);
			      if (displayDetails($indirec) or showLivingName($indirec))
			      {
			      	$husb_ok = true;
			      }
			      else
			      {
			      	$husb_ok = false;
			      }
			    }

			    $wife_ok = true;
			    $ct02 = preg_match("/1 WIFE @(.*)@/", $famrec, $match);
			    if ($ct02 > 0)
			    {
			      $indirec = find_person_record($match[1]);
			      if (displayDetails($indirec) or showLivingName($indirec))
			      {
			      	$wife_ok = true;
			      }
			      else
			      {
			      	$wife_ok = false;
			      }
			    }
			    if (($husb_ok) and ($wife_ok)) $dName = get_family_descriptor($clipping['id']); else $dName = $pgv_lang["family_private"];
			    $names = preg_split("/,/", $dName);
				$dName = check_NN($names);
			    print "<a href=\"family.php?famid=".$clipping['id']."\">".PrintReady($dName)."</a>";
			  }
			if ($tag=='SOUR') print "<a href=\"source.php?sid=".$clipping['id']."\">".PrintReady(get_source_descriptor($clipping['id']))."</a>";
			if ($tag=='REPO') print "<a href=\"repo.php?rid=".$clipping['id']."\">".PrintReady(get_repo_descriptor($clipping['id']))."</a>";
			if ($tag=="OBJE") {
			  	print PrintReady(get_media_descriptor($clipping['id']));
			  }
			?>
			</td>
			<td class="list_value center vmiddle"><a href="clippings.php?action=remove&amp;item=<?php echo $i;?>"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"];?>" border="0" alt="<?php echo $pgv_lang["remove"]?>" title="<?php echo $pgv_lang["remove"];?>" /></a></td>
		</tr>
		<?php
		}
	}
	?>
	</table>
	<?php	if ($action != 'download') {
		print "<form method=\"post\" action=\"clippings.php\">\n<input type=\"hidden\" name=\"action\" value=\"download\" />\n";
		?>
		<table>
		<tr><td><input type="checkbox" name="convert" value="yes" /></td><td><?php print $pgv_lang["utf8_to_ansi"]; print_help_link("utf8_ansi_help", "qm"); ?></td></tr>
		<tr><td><input type="checkbox" name="remove" value="yes" checked="checked" /></td><td><?php print $pgv_lang["remove_custom_tags"]; print_help_link("remove_tags_help", "qm"); ?></td></tr>
		</table>
		<input type="submit" value="<?php print $pgv_lang["download_now"]; ?>" />
		<?php
		print_help_link("clip_download_help", "qm");
		print "<br />";
		print "</form>\n";
	}
	print "\r\n\t<br /><a href=\"clippings.php?action=empty\">".$pgv_lang["empty_cart"]."  "."</a>";
	print_help_link("empty_cart_help", "qm");
}
if (isset($_SESSION["cart"])) $_SESSION["cart"]=$cart;
print_footer();
?>

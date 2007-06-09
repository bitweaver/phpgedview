<?php
/**
 * PopUp Window to provide editing features.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  PGV Development Team
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
 * @subpackage Edit
 * @version $Id: edit_interface.php,v 1.7 2007/06/09 21:11:02 lsces Exp $
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
require("includes/functions_edit.php");

require("languages/countries.en.php");
if (file_exists("languages/countries.".$lang_short_cut[$LANGUAGE].".php")) require("languages/countries.".$lang_short_cut[$LANGUAGE].".php");
asort($countries);

// Remove slashes
if (isset($text)){
	foreach ($text as $l => $line){
		$text[$l] = stripslashes($line);
	}
}
//$DEBUG=1;
if (!isset($action)) $action="";
if (!isset($linenum)) $linenum="";
if ((isset($_POST["preserve_last_changed"])) && ($_POST["preserve_last_changed"] == "on"))
	$update_CHAN = false;
else
	$update_CHAN = true;

$uploaded_files = array();

// items for ASSO RELA selector :
$assokeys = array(
"attendant",
"attending",
"best_man",
"bridesmaid",
"buyer",
"circumciser",
"civil_registrar",
"friend",
"godfather",
"godmother",
"godparent",
"informant",
"lodger",
"nurse",
"priest",
"rabbi",
"registry_officer",
"seller",
"servant",
"twin",
"twin_brother",
"twin_sister",
"witness",
"" // DO NOT DELETE
);
$assorela = array();
foreach ($assokeys as $indexval => $key) {
  if (isset($pgv_lang["$key"])) $assorela["$key"] = $pgv_lang["$key"];
  else $assorela["$key"] = "? $key";
}
natsort($assorela);

print_simple_header("Edit Interface $VERSION");

?>
<script type="text/javascript">
<!--
	var locale_date_format='<?php print preg_replace('/[^DMY]/', '', $DATE_FORMAT); ?>';

	function findIndi(field, indiname) {
		pastefield = field;
		findwin = window.open('find.php?type=indi', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}
	function findPlace(field) {
		pastefield = field;
		findwin = window.open('find.php?type=place', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}
	function findMedia(field, choose, ged) {
		pastefield = field;
		if (!choose) choose="0all";
		findwin = window.open('find.php?type=media&choose='+choose+'&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}
	function findSource(field) {
		pastefield = field;
		findwin = window.open('find.php?type=source', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}
	function findRepository(field) {
		pastefield = field;
		findwin = window.open('find.php?type=repo', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}
	function findFamily(field) {
		pastefield = field;
		findwin = window.open('find.php?type=fam', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}

	function addnewrepository(field) {
		pastefield = field;
		window.open('edit_interface.php?action=addnewrepository&pid=newrepo', '_blank', 'top=70,left=70,width=600,height=500,resizable=1,scrollbars=1');
		return false;
	}

	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}

	function paste_id(value) {
		pastefield.value = value;
	}

	function paste_char(value,lang,mag) {
		pastefield.value += value;
		language_filter = lang;
		magnify = mag;
	}

	function edit_close() {
		if (window.opener.showchanges) window.opener.showchanges();
		window.close();
	}
//-->
</script>
<?php
//-- check if user has access to the gedcom record
$disp = false;
$success = false;
$factdisp = true;
$factedit = true;
if (!empty($pid)) {
	$pid = clean_input($pid);
	if (($pid!="newsour") and ($pid!="newrepo")) {
		if (!isset($pgv_changes[$pid."_".$GEDCOM])) $gedrec = find_gedcom_record($pid);
		else $gedrec = find_updated_record($pid);
		if (empty($gedrec)) $gedrec =  find_record_in_file($pid);
		$ct = preg_match("/0 @$pid@ (.*)/", $gedrec, $match);
		if ($ct>0) {
			$type = trim($match[1]);
			//-- if the record is for an INDI then check for display privileges for that indi
			if ($type=="INDI") {
				$disp = displayDetailsById($pid);
				//-- if disp is true, also check for resn access
				if ($disp == true){
					$subs = get_all_subrecords($gedrec, "", false, false);
					foreach($subs as $indexval => $sub) {
						if (FactViewRestricted($pid, $sub)==true) $factdisp = false;
						if (FactEditRestricted($pid, $sub)==true) $factedit = false;
					}
				}
			}
			//-- for FAM check for display privileges on both parents
			else if ($type=="FAM") {
				//-- check if there are restrictions on the facts
				$subs = get_all_subrecords($gedrec, "", false, false);
				foreach($subs as $indexval => $sub) {
					if (FactViewRestricted($pid, $sub)==true) $factdisp = false;
					if (FactEditRestricted($pid, $sub)==true) $factedit = false;
				}
				//-- check if we can display both parents
				$parents = find_parents_in_record($gedrec);
				$disp = displayDetailsById($parents["HUSB"]);
				if ($disp) {
					$disp = displayDetailsById($parents["WIFE"]);
				}
			}
			else {
				$disp=true;
			}
		}
	}
	else {
		$disp = true;
	}
}
else if (!empty($famid)) {
	$famid = clean_input($famid);
	if ($famid != "new") {
		if (!isset($pgv_changes[$famid."_".$GEDCOM])) $gedrec = find_gedcom_record($famid);
		else $gedrec = find_updated_record($famid);
		if (empty($gedrec)) $gedrec =  find_record_in_file($famid);
		$ct = preg_match("/0 @$famid@ (.*)/", $gedrec, $match);
		if ($ct>0) {
			$type = trim($match[1]);
			//-- if the record is for an INDI then check for display privileges for that indi
			if ($type=="INDI") {
				$disp = displayDetailsById($famid);
				//-- if disp is true, also check for resn access
				if ($disp == true){
					$subs = get_all_subrecords($gedrec, "", false, false);
					foreach($subs as $indexval => $sub) {
						if (FactViewRestricted($famid, $sub)==true) $factdisp = false;
						if (FactEditRestricted($famid, $sub)==true) $factedit = false;
					}
				}
			}
			//-- for FAM check for display privileges on both parents
			else if ($type=="FAM") {
				//-- check if there are restrictions on the facts
				$subs = get_all_subrecords($gedrec, "", false, false);
				foreach($subs as $indexval => $sub) {
					if (FactViewRestricted($famid, $sub)==true) $factdisp = false;
					if (FactEditRestricted($famid, $sub)==true) $factedit = false;
				}
				//-- check if we can display both parents
				$parents = find_parents_in_record($gedrec);
				$disp = displayDetailsById($parents["HUSB"]);
				if ($disp) {
					$disp = displayDetailsById($parents["WIFE"]);
				}
			}
			else {
				$disp=true;
			}
		}
	}
}
else if (($action!="addchild")&&($action!="addchildaction")&&($action!="addnewsource")&&($action!="mod_edit_fact")) {
	print "<span class=\"error\">The \$pid variable was empty.	Unable to perform $action.</span>";
	print_simple_footer();
	$disp = true;
}
else {
	$disp = true;
}

if ((!$gGedcom->isEditable())||(!$disp)||(!$ALLOW_EDIT_GEDCOM)) {
	//print "pid: $pid<br />";
	//print "gedrec: $gedrec<br />";
	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!$gGedcom->isEditable()) print "<br />".$pgv_lang["user_cannot_edit"];
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".$pgv_lang["gedcom_editing_disabled"];
	if (!$disp) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
		if (!empty($famid)) print "<br />".$pgv_lang["privacy_not_granted"]." famid $famid.";
	}
	if (empty($gedrec)) print "<br /><span class=\"error\">".$pgv_lang["record_not_found"]."</span>";
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".$pgv_lang["close_window"]."\" onclick=\"window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}

//-- privatize the record so that line numbers etc. match what was in the display
//-- data that is hidden because of privacy is stored in the $pgv_private_records array
//-- any private data will be restored when the record is replaced
if (isset($gedrec)) $gedrec = privatize_gedcom($gedrec);

if (!isset($type)) $type="";
$level0type = $type;
if ($type=="INDI") {
	print "<b>".PrintReady(get_person_name($pid))."</b><br />";
}
else if ($type=="FAM") {
	if (!empty($pid)) print "<b>".PrintReady(get_family_descriptor($pid))."</b><br />";
	else print "<b>".PrintReady(get_family_descriptor($famid))."</b><br />";
}
else if ($type=="SOUR") {
	print "<b>".PrintReady(get_source_descriptor($pid))."&nbsp;&nbsp;&nbsp;";
	if ($TEXT_DIRECTION=="rtl") print "&rlm;";
	print "(".$pid.")";
	if ($TEXT_DIRECTION=="rtl") print "&rlm;";
	print "</b><br />";
}
if (strstr($action,"addchild")) {
	if (empty($famid)) {
		print_help_link("edit_add_unlinked_person_help", "qm");
		print "<b>".$pgv_lang["add_unlinked_person"]."</b>\n";
	}
	else {
		print_help_link("edit_add_child_help", "qm");
		print "<b>".$pgv_lang["add_child"]."</b>\n";
	}
}
else if (strstr($action,"addspouse")) {
	print_help_link("edit_add_spouse_help", "qm");
	print "<b>".$pgv_lang["add_".strtolower($famtag)]."</b>\n";
}
else if (strstr($action,"addnewparent")) {
	print_help_link("edit_add_parent_help", "qm");
	if ($famtag=="WIFE") print "<b>".$pgv_lang["add_mother"]."</b>\n";
	else print "<b>".$pgv_lang["add_father"]."</b>\n";
}
else {
	if (isset($factarray[$type])) print "<b>".$factarray[$type]."</b>";
}
//------------------------------------------------------------------------------
switch ($action) {
case 'delete':
	global $MEDIA_ID_PREFIX;
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if (!empty($linenum)) {
		if ($linenum===0) {
			if (delete_gedrec($pid)) print $pgv_lang["gedrec_deleted"];
		}
		else {
			$gedlines = preg_split("/\n/", $gedrec);

			//-- when deleting a media link
			//-- $linenum comes is an OBJE and the $mediaid to delete should be set
			if ($linenum=='OBJE') {
				if (!empty($mediaid)) {
					for($i=0; $i<count($gedlines); $i++) {
						if (preg_match("/OBJE @".$mediaid."@/", $gedlines[$i])>0) {
							$linenum = $i;
							break;
						}
					}
				}
			}
			$newged = "";
			for($i=0; $i<$linenum; $i++) {
				if (trim($gedlines[$i])!="") $newged .= $gedlines[$i]."\n";
			}
			if (isset($gedlines[$linenum])) {
				$fields = preg_split("/\s/", $gedlines[$linenum]);
				$glevel = $fields[0];
				$i++;
				if ($i<count($gedlines)) {
					//-- don't put empty lines in the record
					while((isset($gedlines[$i]))&&(strlen($gedlines[$i])<4 || $gedlines[$i]{0}>$glevel)) $i++;
					while($i<count($gedlines)) {
						if (trim($gedlines[$i])!="") $newged .= $gedlines[$i]."\n";
						$i++;
					}
				}
			}
			$success = (replace_gedrec($pid, $newged));
			if ($success) print "<br /><br />".$pgv_lang["gedrec_deleted"];
		}
	}
	break;
//------------------------------------------------------------------------------
//-- print a form to edit the raw gedcom record in a large textarea
case 'editraw':
	if (!$factedit) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
		if (!empty($famid)) print "<br />".$pgv_lang["privacy_not_granted"]." famid $famid.";
		print_simple_footer();
		exit;
	}
	else {
		print "<br /><b>".$pgv_lang["edit_raw"]."</b>";
		print_help_link("edit_edit_raw_help", "qm");
		print "<form method=\"post\" action=\"edit_interface.php\">\n";
		print "<input type=\"hidden\" name=\"action\" value=\"updateraw\" />\n";
		print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
		print_specialchar_link("newgedrec",true);
		print "<br />\n";
		print "<textarea name=\"newgedrec\" id=\"newgedrec\" rows=\"20\" cols=\"60\" dir=\"ltr\">".$gedrec."</textarea>\n<br />";
		if (UserIsAdmin(GetUserName())) {
			print "<table class=\"facts_table\">\n";
			print "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
			print_help_link("no_update_CHAN_help", "qm");
			print $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
			print "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
			print $pgv_lang["no_update_CHAN"]."<br />\n";
			print_fact_date(get_sub_record(1, "1 CHAN", $gedrec), false, true);
			print "</td></tr>\n";
			print "</table>";
		}

		print "<input id=\"savebutton\" type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";
		print "</form>\n";
		print "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\ntextbox = document.getElementById('newgedrec');\n";
		print "savebutton = document.getElementById('savebutton');\n";
		print "if (textbox && savebutton) {\nx = textbox.offsetLeft+textbox.offsetWidth+40;\ny = savebutton.offsetTop+80;\n";
		print "window.resizeTo(x,y);\n}\n";
		print "\n//-->\n</script>\n";
	}
	break;
//------------------------------------------------------------------------------
//-- edit a fact record in a form
case 'edit':
	init_calendar_popup();
	print "<form method=\"post\" action=\"edit_interface.php\" enctype=\"multipart/form-data\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
	print "<input type=\"hidden\" name=\"linenum\" value=\"$linenum\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	print "<br /><input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";

	print "<table class=\"facts_table\">";
	$level1type = create_edit_form($gedrec, $linenum, $level0type);
	if (UserIsAdmin(GetUserName())) {
		print "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		print_help_link("no_update_CHAN_help", "qm");
		print $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
		print "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
		print $pgv_lang["no_update_CHAN"]."<br />\n";
		print_fact_date(get_sub_record(1, "1 CHAN", $gedrec), false, true);
		print "</td></tr>\n";
		}
	print "</table>";
	if ($level0type=="SOUR" || $level0type=="REPO" || $level0type=="OBJE") {
		if ($level1type!="NOTE") print_add_layer("NOTE");
	} else {
		if ($level1type!="SEX") {
			if ($level1type!="ASSO" && $level1type!="REPO") print_add_layer("ASSO");
			if ($level1type!="SOUR" && $level1type!="REPO") print_add_layer("SOUR");
			if ($level1type!="NOTE") print_add_layer("NOTE");
			if ($level1type!="OBJE" && $level1type!="REPO" && $level1type!="NOTE" && $MULTI_MEDIA) print_add_layer("OBJE");
			//-- RESN missing in new structure, RESN can be added to all level 1 tags
			if (!in_array("RESN", $tags)) print_add_layer("RESN");
		}
	}

	print "<br /><input type=\"submit\" value=\"".$pgv_lang["save"]."\" /><br />\n";
	print "</form>\n";
	break;
//------------------------------------------------------------------------------
case 'add':
	//
	// Start of add section...
	//
	init_calendar_popup();
	print "<form method=\"post\" action=\"edit_interface.php\" enctype=\"multipart/form-data\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
	print "<input type=\"hidden\" name=\"linenum\" value=\"new\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";

	print "<br /><input type=\"submit\" value=\"".$pgv_lang["add"]."\" /><br />\n";
	print "<table class=\"facts_table\">";

	create_add_form($fact);

	if (UserIsAdmin(GetUserName())) {
		print "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		print_help_link("no_update_CHAN_help", "qm");
		print $pgv_lang["admin_override"]."</td><td class=\"optionbox wrap\">\n";
		print "<input type=\"checkbox\" name=\"preserve_last_changed\" />\n";
		print $pgv_lang["no_update_CHAN"]."<br />\n";
		print_fact_date(get_sub_record(1, "1 CHAN", $gedrec), false, true);
		print "</td></tr>\n";
	}
	print "</table>";

	if ($level0type=="SOUR" || $level0type=="REPO") {
		if ($fact!="NOTE") print_add_layer("NOTE");
	} else {
		if ($fact!="OBJE") {
			if ($fact!="ASSO" && $fact!="SOUR" && $fact!="REPO") print_add_layer("ASSO");
			if ($fact!="SOUR" && $fact!="REPO") print_add_layer("SOUR");
			if ($fact!="NOTE") print_add_layer("NOTE");
			if ($fact!="REPO") print_add_layer("OBJE");
		}
	}
	//-- RESN missing in new structure, RESN can be added to all level 1 tags
	if (!in_array("RESN", $tags)) print_add_layer("RESN");

	print "<br /><input type=\"submit\" value=\"".$pgv_lang["add"]."\" /><br />\n";
	print "</form>\n";
	break;
//------------------------------------------------------------------------------
case 'addchild':
	print_indi_form("addchildaction", $famid, "", "", "CHIL", @$_REQUEST["sex"]);
	break;
//------------------------------------------------------------------------------
case 'addspouse':
	print_indi_form("addspouseaction", $famid, "", "", $famtag);
	break;
//------------------------------------------------------------------------------
case 'addnewparent':
	print_indi_form("addnewparentaction", $famid, "", "", $famtag);
	break;
//------------------------------------------------------------------------------
case 'addfamlink':
	print "<form method=\"post\" name=\"addchildform\" action=\"edit_interface.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"linkfamaction\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	print "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\" />\n";
	print "<table class=\"facts_table\">";
	print "<tr><td class=\"facts_label\">".$pgv_lang["family"]."</td>";
	print "<td class=\"facts_value\"><input type=\"text\" name=\"famid\" size=\"8\" /> ";
	print_findfamily_link("famid");
	print "\n</td></tr>";
	if ($famtag=="CHIL") {
		print "<tr><td class=\"facts_label\">".$factarray["PEDI"]."</td>";
		print "<td class=\"facts_value\"><select name=\"pedigree\">";
		print "<option value=\"\"></option>";
		print "<option value=\"birth\">".$pgv_lang["birth"]."</option>";
		print "<option value=\"adopted\">".$pgv_lang["adopted"]."</option>";
		print "<option value=\"foster\">".$pgv_lang["foster"]."</option>";
		print "<option value=\"sealing\">".$pgv_lang["sealing"]."</option>";
		print "</select></tr>";
	}
	print "</table>\n";
	print "<input type=\"submit\" value=\"".$pgv_lang["set_link"]."\" /><br />\n";
	print "</form>\n";
	break;
//------------------------------------------------------------------------------
case 'linkspouse':
	init_calendar_popup();
	print "<form method=\"post\" name=\"addchildform\" action=\"edit_interface.php\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"linkspouseaction\" />\n";
	print "<input type=\"hidden\" name=\"pid\" value=\"$pid\" />\n";
	print "<input type=\"hidden\" name=\"famid\" value=\"new\" />\n";
	print "<input type=\"hidden\" name=\"famtag\" value=\"$famtag\" />\n";
	print "<table class=\"facts_table\">";
	print "<tr><td class=\"facts_label\">";
	if ($famtag=="WIFE") print $pgv_lang["wife"];
	else print $pgv_lang["husband"];
	print "</td>";
	print "<td class=\"facts_value\"><input id=\"spouseid\" type=\"text\" name=\"spid\" size=\"8\" /> ";
	print_findindi_link("spouseid", "");
	print "\n</td></tr>";
	add_simple_tag("0 MARR");
	add_simple_tag("0 DATE", "MARR");
	add_simple_tag("0 PLAC", "MARR");
	print "</table>\n";
	print "<input type=\"submit\" value=\"".$pgv_lang["set_link"]."\" /><br />\n";
	print "</form>\n";
	break;
//------------------------------------------------------------------------------
case 'linkfamaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if (!isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_gedcom_record($famid);
	else $famrec = find_updated_record($famid);
	$famrec = trim($famrec);
	if (!empty($famrec)) {
		$itag = "FAMC";
		if ($famtag=="HUSB" || $famtag=="WIFE") $itag="FAMS";

		//-- update the individual record for the person
		if (preg_match("/1 $itag @$famid@/", $gedrec)==0) {
			$gedrec = trim($gedrec)."\r\n1 $itag @$famid@";
			if ($itag=="FAMC" && !empty($pedigree)) {
				$gedrec.="\r\n2 PEDI $pedigree";
				if ($pedigree=="adopted")
					$gedrec.="\r\n1 ADOP\r\n2 FAMC @$famid@\r\n3 ADOP BOTH";
			}
			if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
			replace_gedrec($pid, $gedrec);
		}

		//-- if it is adding a new child to a family
		if ($famtag=="CHIL") {
			if (preg_match("/1 $famtag @$pid@/", $famrec)==0) {
				$famrec = trim($famrec)."\r\n1 $famtag @$pid@";
				if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
				replace_gedrec($famid, $famrec);
			}
		}
		//-- if it is adding a husband or wife
		else {
			//-- check if the family already has a HUSB or WIFE
			$ct = preg_match("/1 $famtag @(.*)@/", $famrec, $match);
			if ($ct>0) {
				//-- get the old ID
				$spid = trim($match[1]);
				//-- only continue if the old husb/wife is not the same as the current one
				if ($spid!=$pid) {
					//-- change a of the old ids to the new id
					$famrec = preg_replace("/1 $famtag @$spid@/", "1 $famtag @$pid@", $famrec);
					if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
					replace_gedrec($famid, $famrec);
					//-- remove the FAMS reference from the old husb/wife
					if (!empty($spid)) {
						if (!isset($pgv_changes[$spid."_".$GEDCOM])) $srec = find_gedcom_record($spid);
						else $srec = find_updated_record($spid);
						if ($srec) {
							$srec = preg_replace("/1 $itag @$famid@\s*/", "", $srec);
							if ($GLOBALS["DEBUG"]) print "<pre>$srec</pre>";
							replace_gedrec($spid, $srec);
						}
					}
				}
			}
			else {
				$famrec .= "\r\n1 $famtag @$pid@";
				if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
				replace_gedrec($famid, $famrec);
			}
		}
	}
	else print "Family record not found";
	break;
//------------------------------------------------------------------------------
//-- add new source
case 'addnewsource':
	?>
	<script type="text/javascript">
	<!--
		function check_form(frm) {
			if (frm.TITL.value=="") {
				alert('<?php print $pgv_lang["must_provide"].$factarray["TITL"]; ?>');
				frm.TITL.focus();
				return false;
			}
			return true;
		}
	//-->
	</script>
	<b><?php print $pgv_lang["create_source"];
	$tabkey = 1;
	 ?></b>
	<form method="post" action="edit_interface.php" onSubmit="return check_form(this);">
		<input type="hidden" name="action" value="addsourceaction" />
		<input type="hidden" name="pid" value="newsour" />
		<table class="facts_table">
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_ABBR_help", "qm"); print $factarray["ABBR"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="ABBR" id="ABBR" value="" size="40" maxlength="255" /> <?php print_specialchar_link("ABBR",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_TITL_help", "qm"); print $factarray["TITL"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="TITL" id="TITL" value="" size="60" /> <?php print_specialchar_link("TITL",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit__HEB_help", "qm"); print $factarray["_HEB"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="_HEB" id="_HEB" value="" size="60" /> <?php print_specialchar_link("_HEB",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_ROMN_help", "qm"); print $factarray["ROMN"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="ROMN" id="ROMN" value="" size="60" /> <?php print_specialchar_link("ROMN",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_AUTH_help", "qm"); print $factarray["AUTH"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="AUTH" id="AUTH" value="" size="40" maxlength="255" /> <?php print_specialchar_link("AUTH",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_PUBL_help", "qm"); print $factarray["PUBL"]; ?></td>
			<td class="optionbox wrap"><textarea tabindex="<?php print $tabkey; ?>" name="PUBL" id="PUBL" rows="5" cols="60"></textarea><br /><?php print_specialchar_link("PUBL",true); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_REPO_help", "qm"); print $factarray["REPO"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="REPO" id="REPO" value="" size="<?php print (strlen($REPO_ID_PREFIX) + 4); ?>" /> <?php print_findrepository_link("REPO"); print_addnewrepository_link("REPO"); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_CALN_help", "qm"); print $factarray["CALN"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="CALN" id="CALN" value="" /></td></tr>
		</table>
			<?php print_help_link("edit_SOUR_EVEN_help", "qm"); ?><a href="#"  onclick="return expand_layer('events');"><img id="events_img" src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"];?>" border="0" width="11" height="11" alt="" title="" /> 
			<?php print $pgv_lang["source_events"]; ?></a>
			<div id="events" style="display: none;">
			<table class="facts_table">
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_SOUR_EVEN_help", "qm"); print $pgv_lang['select_events']; ?></td>
				<td class="optionbox wrap"><select name="EVEN[]" mulitple="multiple" size="5">
					<?php
					$parts = preg_split("/,/", $INDI_FACTS_ADD);
					foreach($parts as $p=>$key) {
						?><option value="<?php print $key; ?>"><?php print $factarray[$key]. " ($key)"; ?></option>
					<?php
					}
					$parts = preg_split("/,/", $FAM_FACTS_ADD);
					foreach($parts as $p=>$key) {
						?><option value="<?php print $key; ?>"><?php print $factarray[$key]. " ($key)"; ?></option>
					<?php
					}
					?>
				</select></td>
			</tr>
			<?php
			add_simple_tag("0 DATE", "EVEN");
			add_simple_tag("0 PLAC", "EVEN");
			add_simple_tag("0 AGNC");
			?>
			</table>
			</div>
		<br/><br/>
		<input type="submit" value="<?php print $pgv_lang["create_source"]; ?>" />
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
//-- create a source record from the incoming variables
case 'addsourceaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	$newgedrec = "0 @XREF@ SOUR\r\n";
	if (!empty($EVEN) && count($EVEN)>0) {
		$newgedrec .= "1 DATA\r\n";
		$newgedrec .= "2 EVEN ".implode(",", $EVEN)."\r\n";
		if (!empty($EVEN_DATE)) $newgedrec .= "3 DATE ".check_input_date($EVEN_DATE)."\r\n";
		if (!empty($EVEN_PLAC)) $newgedrec .= "3 PLAC ".$EVEN_PLAC."\r\n";
		if (!empty($AGNC))	$newgedrec .= "2 AGNC ".$AGNC."\r\n";
	}
	if (!empty($ABBR)) $newgedrec .= "1 ABBR $ABBR\r\n";
	if (!empty($TITL)) {
		$newgedrec .= "1 TITL $TITL\r\n";
		if (!empty($_HEB)) $newgedrec .= "2 _HEB $_HEB\r\n";
		if (!empty($ROMN)) $newgedrec .= "2 ROMN $_HEB\r\n";
	}
	if (!empty($AUTH)) $newgedrec .= "1 AUTH $AUTH\r\n";
	if (!empty($PUBL)) $newgedrec .= "1 PUBL $PUBL\r\n";
	if (!empty($REPO)) {
		$newgedrec .= "1 REPO @$REPO@\r\n";
		if (!empty($CALN)) $newgedrec .= "2 CALN $CALN\r\n";
	}
	$newlines = preg_split("/\r?\n/", $newgedrec);
	$newged = $newlines[0]."\r\n";
	for($k=1; $k<count($newlines); $k++) {
		if (((preg_match("/\d .... .*/", $newlines[$k])==0) and strlen($newlines[$k])!=0)) $newlines[$k] = "2 CONT ".$newlines[$k];
		if (strlen($newlines[$k])>255) {
			while(strlen($newlines[$k])>255) {
				$newPiece = rtrim(substr($newlines[$k], 0, 255));
				$newged .= $newPiece."\r\n";
				$newlines[$k] = substr($newlines[$k], strlen($newPiece));
				$newlines[$k] = "2 CONC ".$newlines[$k];
			}
			$newged .= trim($newlines[$k])."\r\n";
		}
		else {
			$newged .= trim($newlines[$k])."\r\n";
		}
	}
	if ($GLOBALS["DEBUG"]) print "<pre>$newged</pre>";
	$xref = append_gedrec($newged);
	if ($xref) {
		print "<br /><br />\n".$pgv_lang["new_source_created"]."<br /><br />";
		print "<a href=\"javascript:// SOUR $xref\" onclick=\"openerpasteid('$xref'); return false;\">".$pgv_lang["paste_id_into_field"]." <b>$xref</b></a>\n";
	}
	break;
//------------------------------------------------------------------------------
//-- add new repository
case 'addnewrepository':
	?>
	<script type="text/javascript">
	<!--
		function check_form(frm) {
			if (frm.NAME.value=="") {
				alert('<?php print $pgv_lang["must_provide"]." ".$factarray["NAME"]; ?>');
				frm.NAME.focus();
				return false;
			}
			return true;
		}
	//-->
	</script>
	<b><?php print $pgv_lang["create_repository"];
	$tabkey = 1;
	?></b>
	<form method="post" action="edit_interface.php" onSubmit="return check_form(this);">
		<input type="hidden" name="action" value="addrepoaction" />
		<input type="hidden" name="pid" value="newrepo" />
		<table class="facts_table">
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_REPO_NAME_help", "qm"); print $factarray["NAME"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="NAME" id="NAME" value="" size="40" maxlength="255" /> <?php print_specialchar_link("NAME",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit__HEB_help", "qm"); print $factarray["_HEB"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="_HEB" id="_HEB" value="" size="40" maxlength="255" /> <?php print_specialchar_link("_HEB",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_ROMN_help", "qm"); print $factarray["ROMN"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="ROMN" id="ROMN" value="" size="40" maxlength="255" /> <?php print_specialchar_link("ROMN",false); ?></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_ADDR_help", "qm"); print $factarray["ADDR"]; ?></td>
			<td class="optionbox wrap"><textarea tabindex="<?php print $tabkey; ?>" name="ADDR" id="ADDR" rows="5" cols="60"></textarea><?php print_specialchar_link("ADDR",true); ?> </td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_PHON_help", "qm"); print $factarray["PHON"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="PHON" id="PHON" value="" size="40" maxlength="255" /> </td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_FAX_help", "qm"); print $factarray["FAX"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="FAX" id="FAX" value="" size="40" /></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_EMAIL_help", "qm"); print $factarray["EMAIL"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="EMAIL" id="EMAIL" value="" size="40" maxlength="255" /></td></tr>
			<?php $tabkey++; ?>
			<tr><td class="descriptionbox <?php print $TEXT_DIRECTION; ?> wrap width25"><?php print_help_link("edit_WWW_help", "qm"); print $factarray["WWW"]; ?></td>
			<td class="optionbox wrap"><input tabindex="<?php print $tabkey; ?>" type="text" name="WWW" id="WWW" value="" size="40" maxlength="255" /> </td></tr>
		</table>
		<input type="submit" value="<?php print $pgv_lang["create_repository"]; ?>" />
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
//-- create a repository record from the incoming variables
case 'addrepoaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	$newgedrec = "0 @XREF@ REPO\r\n";
	if (!empty($NAME)) {
		$newgedrec .= "1 NAME $NAME\r\n";
		if (!empty($_HEB)) $newgedrec .= "2 _HEB $_HEB\r\n";
		if (!empty($ROMN)) $newgedrec .= "2 ROMN $_HEB\r\n";
	}
	if (!empty($ADDR)) $newgedrec .= "1 ADDR $ADDR\r\n";
	if (!empty($PHON)) $newgedrec .= "1 PHON $PHON\r\n";
	if (!empty($FAX)) $newgedrec .= "1 FAX $FAX\r\n";
	if (!empty($EMAIL)) $newgedrec .= "1 EMAIL $EMAIL\r\n";
	if (!empty($WWW)) $newgedrec .= "1 WWW $WWW\r\n";
	$newlines = preg_split("/\r?\n/", $newgedrec);
	$newged = $newlines[0]."\r\n";
	for($k=1; $k<count($newlines); $k++) {
		if ((preg_match("/\d (.....|....|...) .*/", $newlines[$k])==0) and (strlen($newlines[$k])!=0)) $newlines[$k] = "2 CONT ".$newlines[$k];
		if (strlen($newlines[$k])>255) {
			while(strlen($newlines[$k])>255) {
				$newPiece = rtrim(substr($newlines[$k], 0, 255));
				$newged .= $newPiece."\r\n";
				$newlines[$k] = substr($newlines[$k], strlen($newPiece));
				$newlines[$k] = "2 CONC ".$newlines[$k];
			}
			$newged .= trim($newlines[$k])."\r\n";
		}
		else {
			$newged .= trim($newlines[$k])."\r\n";
		}
	}
	if ($GLOBALS["DEBUG"]) print "<pre>$newged</pre>";
	$xref = append_gedrec($newged);
	if ($xref) {
		print "<br /><br />\n".$pgv_lang["new_repo_created"]."<br /><br />";
		print "<a href=\"javascript:// REPO $xref\" onclick=\"openerpasteid('$xref'); return false;\">".$pgv_lang["paste_rid_into_field"]." <b>$xref</b></a>\n";
	}
	break;
//------------------------------------------------------------------------------
//-- get the new incoming raw gedcom record and store it in the file
case 'updateraw':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$newgedrec</pre>";
	$newgedrec = trim($newgedrec);
	$success = (!empty($newgedrec)&&(replace_gedrec($pid, $newgedrec, $update_CHAN)));
	if ($success) print "<br /><br />".$pgv_lang["update_successful"];
	break;
//------------------------------------------------------------------------------
//-- reconstruct the gedcom from the incoming fields and store it in the file
case 'update':
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	// add or remove Y
	if ($text[0]=="Y" or $text[0]=="y") $text[0]="";
	if (in_array($tag[0], $emptyfacts) && array_unique($text)==array("") && !$islink[0]) $text[0]="Y";
	//-- check for photo update
	if (count($_FILES)>0) {
		$uploaded_files = array();
		$upload_errors = array($pgv_lang["file_success"], $pgv_lang["file_too_big"], $pgv_lang["file_too_big"],$pgv_lang["file_partial"], $pgv_lang["file_missing"]);
		if (substr($folder,0,1) == "/") $folder = substr($folder,1);
		if (substr($folder,-1,1) != "/") $folder .= "/";
		foreach($_FILES as $upload) {
			if (!empty($upload['tmp_name'])) {
				if (!move_uploaded_file($upload['tmp_name'], $MEDIA_DIRECTORY.$folder.basename($upload['name']))) {
					$error .= "<br />".$pgv_lang["upload_error"]."<br />".$upload_errors[$upload['error']];
					$uploaded_files[] = "";
				}
				else {
					$filename = $MEDIA_DIRECTORY.$folder.basename($upload['name']);
					$uploaded_files[] = $MEDIA_DIRECTORY.$folder.basename($upload['name']);
					if (!is_dir($MEDIA_DIRECTORY."thumbs/".$folder)) mkdir($MEDIA_DIRECTORY."thumbs/".$folder);
					$thumbnail = $MEDIA_DIRECTORY."thumbs/".$folder.basename($upload['name']);
					generate_thumbnail($filename, $thumbnail);
					if (!empty($error)) {
						print "<span class=\"error\">".$error."</span>";
					}
				}
			}
			else $uploaded_files[] = "";
		}
	}
	$gedlines = preg_split("/\n/", trim($gedrec));
	//-- for new facts set linenum to number of lines
	if ($linenum=="new") $linenum = count($gedlines);
	$newged = "";
	for($i=0; $i<$linenum; $i++) {
		$newged .= $gedlines[$i]."\n";
	}
	//-- for edits get the level from the line
	if (isset($gedlines[$linenum])) {
		$fields = preg_split("/\s/", $gedlines[$linenum]);
		$glevel = $fields[0];
		$i++;
		while(($i<count($gedlines))&&($gedlines[$i]{0}>$glevel)) $i++;
	}
	if (!isset($glevels)) $glevels = array();
	if (!empty($NAME)) $newged .= "1 NAME $NAME\r\n";
	if (!empty($TYPE)) $newged .= "2 TYPE $TYPE\r\n";
	if (!empty($NPFX)) $newged .= "2 NPFX $NPFX\r\n";
	if (!empty($GIVN)) $newged .= "2 GIVN $GIVN\r\n";
	if (!empty($NICK)) $newged .= "2 NICK $NICK\r\n";
	if (!empty($SPFX)) $newged .= "2 SPFX $SPFX\r\n";
	if (!empty($SURN)) $newged .= "2 SURN $SURN\r\n";
	if (!empty($NSFX)) $newged .= "2 NSFX $NSFX\r\n";

	//-- Refer to Bug [ 1329644 ] Add Married Name - Wrong Sequence
	$newged = handle_updates($newged);
	
	if (!empty($ROMN)) $newged .= "2 ROMN $ROMN\r\n";
	if (!empty($FONE)) $newged .= "2 FONE $FONE\r\n";
	if (!empty($_AKA)) $newged .= "2 _AKA $_AKA\r\n";
	if (!empty($_HEB)) $newged .= "2 _HEB $_HEB\r\n";
	if (!empty($_MARNM)) $newged .= "2 _MARNM $_MARNM\r\n";

	while($i<count($gedlines)) {
		$newged .= trim($gedlines[$i])."\r\n";
		$i++;
	}
	if ($GLOBALS["DEBUG"]) print "<pre>$newged</pre>";
	$success = (replace_gedrec($pid, $newged, $update_CHAN));
	if ($success) print "<br /><br />".$pgv_lang["update_successful"];
	break;
//------------------------------------------------------------------------------
case 'addchildaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	$gedrec = "0 @REF@ INDI\r\n1 NAME $NAME\r\n";
	if (!empty($TYPE)) $gedrec .= "2 TYPE $TYPE\r\n";
	if (!empty($NPFX)) $gedrec .= "2 NPFX $NPFX\r\n";
	if (!empty($GIVN)) $gedrec .= "2 GIVN $GIVN\r\n";
	if (!empty($NICK)) $gedrec .= "2 NICK $NICK\r\n";
	if (!empty($SPFX)) $gedrec .= "2 SPFX $SPFX\r\n";
	if (!empty($SURN)) $gedrec .= "2 SURN $SURN\r\n";
	if (!empty($NSFX)) $gedrec .= "2 NSFX $NSFX\r\n";
	if (!empty($ROMN)) $gedrec .= "2 ROMN $ROMN\r\n";
	if (!empty($FONE)) $gedrec .= "2 FONE $FONE\r\n";
	if (!empty($_AKA)) $gedrec .= "2 _AKA $_AKA\r\n";
	if (!empty($_HEB)) $gedrec .= "2 _HEB $_HEB\r\n";
	if (!empty($_MARNM)) $gedrec .= "2 _MARNM $_MARNM\r\n";
	$gedrec .= "1 SEX $SEX\r\n";
	if ((!empty($BIRT_DATE))||(!empty($BIRT_PLAC))) {
		$gedrec .= "1 BIRT\r\n";
		if (!empty($BIRT_DATE)) {
			$BIRT_DATE = check_input_date($BIRT_DATE);
			$gedrec .= "2 DATE $BIRT_DATE\r\n";
		}
		if (!empty($BIRT_PLAC)) {
			$gedrec .= "2 PLAC $BIRT_PLAC\r\n";
			if (!empty($BIRT_ROMN)) $gedrec .= "3 ROMN $BIRT_ROMN\r\n";
			if (!empty($BIRT_FONE)) $gedrec .= "3 FONE $BIRT_FONE\r\n";
			if (!empty($BIRT__HEB)) $gedrec .= "3 _HEB $BIRT__HEB\r\n";
			if ((!empty($BIRT_LATI))||(!empty($BIRT_LONG))) {
				$gedrec .= "3 MAP\r\n";
				$gedrec .= "4 LATI $BIRT_LATI\r\n";
				$gedrec .= "4 LONG $BIRT_LONG\r\n";
			}
		}
		if (isset($SOUR_BIRT) and $SOUR_BIRT and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
			$gedrec .= "2 SOUR @".$text[0]."@\r\n";
			if ($tag[1]=="PAGE" and $text[1]!="") $gedrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
		}
	}
	else if (!empty($BIRT)) $gedrec .= "1 BIRT Y\r\n";
	if ((!empty($DEAT_DATE))||(!empty($DEAT_PLAC))) {
		$gedrec .= "1 DEAT\r\n";
		if (!empty($DEAT_DATE)) {
			$DEAT_DATE = check_input_date($DEAT_DATE);
			$gedrec .= "2 DATE $DEAT_DATE\r\n";
		}
		if (!empty($DEAT_PLAC)) {
			$gedrec .= "2 PLAC $DEAT_PLAC\r\n";
			if (!empty($DEAT_ROMN)) $gedrec .= "3 ROMN $DEAT_ROMN\r\n";
			if (!empty($DEAT_FONE)) $gedrec .= "3 FONE $DEAT_FONE\r\n";
			if (!empty($DEAT__HEB)) $gedrec .= "3 _HEB $DEAT__HEB\r\n";
			if ((!empty($DEAT_LATI))||(!empty($DEAT_LONG))) {
				$gedrec .= "3 MAP\r\n";
				$gedrec .= "4 LATI $DEAT_LATI\r\n";
				$gedrec .= "4 LONG $DEAT_LONG\r\n";
			}
		}
		if (isset($SOUR_DEAT) and $SOUR_DEAT and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
			$gedrec .= "2 SOUR @".$text[0]."@\r\n";
			if ($tag[1]=="PAGE" and $text[1]!="") $gedrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
		}
	}
	else if (!empty($DEAT)) $gedrec .= "1 DEAT Y\r\n";
	if (!empty($famid)) {
		$gedrec.="1 FAMC @$famid@\r\n";
		if (!empty($PEDI))
			$gedrec.="2 PEDI $PEDI\r\n";
	}

	$gedrec = handle_updates($gedrec);

	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	$xref = append_gedrec($gedrec);
	if ($xref) {
		print "<br /><br />".$pgv_lang["update_successful"];
		$gedrec = "";
		if (!empty($famid)) {
			if (!isset($pgv_changes[$famid."_".$GEDCOM])) $gedrec = find_gedcom_record($famid);
			else $gedrec = find_updated_record($famid);
			if (!empty($gedrec)) {
				$gedrec = trim($gedrec);
				$gedrec .= "\r\n1 CHIL @$xref@";
				if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
				replace_gedrec($famid, $gedrec);
			}
		}
		$success = true;
	}
	break;
//------------------------------------------------------------------------------
case 'addspouseaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	$gedrec = "0 @REF@ INDI\r\n1 NAME $NAME\r\n";
	if (!empty($TYPE)) $gedrec .= "2 TYPE $TYPE\r\n";
	if (!empty($NPFX)) $gedrec .= "2 NPFX $NPFX\r\n";
	if (!empty($GIVN)) $gedrec .= "2 GIVN $GIVN\r\n";
	if (!empty($NICK)) $gedrec .= "2 NICK $NICK\r\n";
	if (!empty($SPFX)) $gedrec .= "2 SPFX $SPFX\r\n";
	if (!empty($SURN)) $gedrec .= "2 SURN $SURN\r\n";
	if (!empty($NSFX)) $gedrec .= "2 NSFX $NSFX\r\n";
	if (!empty($ROMN)) $gedrec .= "2 ROMN $ROMN\r\n";
	if (!empty($FONE)) $gedrec .= "2 FONE $FONE\r\n";
	if (!empty($_AKA)) $gedrec .= "2 _AKA $_AKA\r\n";
	if (!empty($_HEB)) $gedrec .= "2 _HEB $_HEB\r\n";
	if (!empty($_MARNM)) $gedrec .= "2 _MARNM $_MARNM\r\n";
	$gedrec .= "1 SEX $SEX\r\n";
	if ((!empty($BIRT_DATE))||(!empty($BIRT_PLAC))) {
		$gedrec .= "1 BIRT\r\n";
		if (!empty($BIRT_DATE)) {
			$BIRT_DATE = check_input_date($BIRT_DATE);
			$gedrec .= "2 DATE $BIRT_DATE\r\n";
		}
		if (!empty($BIRT_PLAC)) {
			$gedrec .= "2 PLAC $BIRT_PLAC\r\n";
			if (!empty($BIRT_ROMN)) $gedrec .= "3 ROMN $BIRT_ROMN\r\n";
			if (!empty($BIRT_FONE)) $gedrec .= "3 FONE $BIRT_FONE\r\n";
			if (!empty($BIRT__HEB)) $gedrec .= "3 _HEB $BIRT__HEB\r\n";
			if ((!empty($BIRT_LATI))||(!empty($BIRT_LONG))) {
				$gedrec .= "3 MAP\r\n";
				$gedrec .= "4 LATI $BIRT_LATI\r\n";
				$gedrec .= "4 LONG $BIRT_LONG\r\n";
			}
		}
		if (isset($SOUR_BIRT) and $SOUR_BIRT and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
			$gedrec .= "2 SOUR @".$text[0]."@\r\n";
			if ($tag[1]=="PAGE" and $text[1]!="") $gedrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
		}
	}
	else if (!empty($BIRT)) $gedrec .= "1 BIRT Y\r\n";
	if ((!empty($DEAT_DATE))||(!empty($DEAT_PLAC))) {
		$gedrec .= "1 DEAT\r\n";
		if (!empty($DEAT_DATE)) {
			$DEAT_DATE = check_input_date($DEAT_DATE);
			$gedrec .= "2 DATE $DEAT_DATE\r\n";
		}
		if (!empty($DEAT_PLAC)) {
			$gedrec .= "2 PLAC $DEAT_PLAC\r\n";
			if (!empty($DEAT_ROMN)) $gedrec .= "3 ROMN $DEAT_ROMN\r\n";
			if (!empty($DEAT_FONE)) $gedrec .= "3 FONE $DEAT_FONE\r\n";
			if (!empty($DEAT__HEB)) $gedrec .= "3 _HEB $DEAT__HEB\r\n";
			if ((!empty($DEAT_LATI))||(!empty($DEAT_LONG))) {
				$gedrec .= "3 MAP\r\n";
				$gedrec .= "4 LATI $DEAT_LATI\r\n";
				$gedrec .= "4 LONG $DEAT_LONG\r\n";
			}
		}
		if (isset($SOUR_DEAT) and $SOUR_DEAT and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
			$gedrec .= "2 SOUR @".$text[0]."@\r\n";
			if ($tag[1]=="PAGE" and $text[1]!="") $gedrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
		}
	}
	else if (!empty($DEAT)) $gedrec .= "1 DEAT Y\r\n";
	$gedrec = handle_updates($gedrec);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	$xref = append_gedrec($gedrec);
	if ($xref) print "<br /><br />".$pgv_lang["update_successful"];
	else exit;
	$spouserec = $gedrec;
	$success = true;
	if ($famid=="new") {
		$famrec = "0 @new@ FAM\r\n";
		if ($SEX=="M") $famtag = "HUSB";
		if ($SEX=="F") $famtag = "WIFE";
		if ($famtag=="HUSB") {
			$famrec .= "1 HUSB @$xref@\r\n";
			$famrec .= "1 WIFE @$pid@\r\n";
		}
		else {
			$famrec .= "1 WIFE @$xref@\r\n";
			$famrec .= "1 HUSB @$pid@\r\n";
		}
		if ((!empty($MARR_DATE))||(!empty($MARR_PLAC))) {
			$famrec .= "1 MARR\r\n";
			if (!empty($MARR_DATE)) {
				$MARR_DATE = check_input_date($MARR_DATE);
				$famrec .= "2 DATE $MARR_DATE\r\n";
			}
			if (!empty($MARR_PLAC)) {
				$famrec .= "2 PLAC $MARR_PLAC\r\n";
				if (!empty($MARR_ROMN)) $famrec .= "3 ROMN $MARR_ROMN\r\n";
				if (!empty($MARR_FONE)) $famrec .= "3 FONE $MARR_FONE\r\n";
				if (!empty($MARR__HEB)) $famrec .= "3 _HEB $MARR__HEB\r\n";
				if ((!empty($MARR_LATI))||(!empty($MARR_LONG))) {
					$famrec .= "3 MAP\r\n";
					$famrec .= "4 LATI $MARR_LATI\r\n";
					$famrec .= "4 LONG $MARR_LONG\r\n";
				}
			}
			if (isset($SOUR_MARR) and $SOUR_MARR and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
				$famrec .= "2 SOUR @".$text[0]."@\r\n";
				if ($tag[1]=="PAGE" and $text[1]!="") $famrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
			}
		}
		else if (!empty($MARR)) $famrec .= "1 MARR Y\r\n";
		if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
		$famid = append_gedrec($famrec);
	}
	else if (!empty($famid)) {
		$famrec = "";
		if (isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_updated_record($famid);
		else $famrec = find_family_record($famid);
		if (!empty($famrec)) {
			$famrec = trim($famrec);
			$famrec .= "\r\n1 $famtag @$xref@\r\n";
			if ((!empty($MARR_DATE))||(!empty($MARR_PLAC))) {
				$famrec .= "1 MARR\r\n";
				if (!empty($MARR_DATE)) {
					$MARR_DATE = check_input_date($MARR_DATE);
					$famrec .= "2 DATE $MARR_DATE\r\n";
				}
				if (!empty($MARR_PLAC)) {
					$famrec .= "2 PLAC $MARR_PLAC\r\n";
					if (!empty($MARR_ROMN)) $famrec .= "3 ROMN $MARR_ROMN\r\n";
					if (!empty($MARR_FONE)) $famrec .= "3 FONE $MARR_FONE\r\n";
					if (!empty($MARR__HEB)) $famrec .= "3 _HEB $MARR__HEB\r\n";
					if ((!empty($MARR_LATI))||(!empty($MARR_LONG))) {
						$famrec .= "3 MAP\r\n";
						$famrec .= "4 LATI $MARR_LATI\r\n";
						$famrec .= "4 LONG $MARR_LONG\r\n";
					}
				}
				if (isset($SOUR_MARR) and $SOUR_MARR and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
					$famrec .= "2 SOUR @".$text[0]."@\r\n";
					if ($tag[1]=="PAGE" and $text[1]!="") $famrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
				}
			}
			else if (!empty($MARR)) $famrec .= "1 MARR Y\r\n";
			if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
			replace_gedrec($famid, $famrec);
		}
	}
	if ((!empty($famid))&&($famid!="new")) {
		/**
		$gedrec = "";
		$gedrec = find_updated_record($xref);
		**/
		$gedrec = $spouserec;
		$gedrec = trim($gedrec);
		$gedrec .= "\r\n1 FAMS @$famid@\r\n";
		if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
		replace_gedrec($xref, $gedrec);
	}
	if (!empty($pid)) {
		$indirec="";
		if (!isset($pgv_changes[$pid."_".$GEDCOM])) $indirec = find_gedcom_record($pid);
		else $indirec = find_updated_record($pid);
		if ($indirec) {
			$indirec = trim($indirec);
			$indirec .= "\r\n1 FAMS @$famid@\r\n";
			if ($GLOBALS["DEBUG"]) print "<pre>$indirec</pre>";
			replace_gedrec($pid, $indirec);
		}
	}
	break;
//------------------------------------------------------------------------------
case 'linkspouseaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if (!empty($spid)) {
		if (isset($pgv_changes[$spid.'_'.$GEDCOM])) $gedrec = find_updated_record($spid);
		else $gedrec = find_person_record($spid);
		$gedrec = trim($gedrec);
		if (!empty($gedrec)) {
			if ($famid=="new") {
				$famrec = "0 @new@ FAM\r\n";
				$SEX = get_gedcom_value("SEX", 1, $gedrec, '', false);
				if ($SEX=="M") $famtag = "HUSB";
				if ($SEX=="F") $famtag = "WIFE";
				if ($famtag=="HUSB") {
					$famrec .= "1 HUSB @$spid@\r\n";
					$famrec .= "1 WIFE @$pid@\r\n";
				}
				else {
					$famrec .= "1 WIFE @$spid@\r\n";
					$famrec .= "1 HUSB @$pid@\r\n";
				}
				if ((!empty($MARR_DATE))||(!empty($MARR_PLAC))) {
					$famrec .= "1 MARR\r\n";
					if (!empty($MARR_DATE)) {
						$MARR_DATE = check_input_date($MARR_DATE);
						$famrec .= "2 DATE $MARR_DATE\r\n";
					}
					if (!empty($MARR_PLAC)) {
						$famrec .= "2 PLAC $MARR_PLAC\r\n";
						if (!empty($MARR_ROMN)) $famrec .= "3 ROMN $MARR_ROMN\r\n";
						if (!empty($MARR_FONE)) $famrec .= "3 FONE $MARR_FONE\r\n";
						if (!empty($MARR__HEB)) $famrec .= "3 _HEB $MARR__HEB\r\n";
						if ((!empty($MARR_LATI))||(!empty($MARR_LONG))) {
							$famrec .= "3 MAP\r\n";
							$famrec .= "4 LATI $MARR_LATI\r\n";
							$famrec .= "4 LONG $MARR_LONG\r\n";
						}
					}
					if (isset($SOUR_MARR) and $SOUR_MARR and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
						$famrec .= "2 SOUR @".$text[0]."@\r\n";
						if ($tag[1]=="PAGE" and $text[1]!="") $famrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
					}
				}
				else if (!empty($MARR)) $famrec .= "1 MARR Y\r\n";
				if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
				$famid = append_gedrec($famrec);
			}
			if ((!empty($famid))&&($famid!="new")) {
				$gedrec .= "\r\n1 FAMS @$famid@\r\n";
				if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
				replace_gedrec($spid, $gedrec);
			}
			if (!empty($pid)) {
				$indirec="";
				if (!isset($pgv_changes[$pid."_".$GEDCOM])) $indirec = find_gedcom_record($pid);
				else $indirec = find_updated_record($pid);
				if (!empty($indirec)) {
					$indirec = trim($indirec);
					$indirec .= "\r\n1 FAMS @$famid@\r\n";
					if ($GLOBALS["DEBUG"]) print "<pre>$indirec</pre>";
					replace_gedrec($pid, $indirec);
				}
			}
		}
	}
	break;
//------------------------------------------------------------------------------
case 'addnewparentaction':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	$gedrec = "0 @REF@ INDI\r\n1 NAME $NAME\r\n";
	if (!empty($TYPE)) $gedrec .= "2 TYPE $TYPE\r\n";
	if (!empty($NPFX)) $gedrec .= "2 NPFX $NPFX\r\n";
	if (!empty($GIVN)) $gedrec .= "2 GIVN $GIVN\r\n";
	if (!empty($NICK)) $gedrec .= "2 NICK $NICK\r\n";
	if (!empty($SPFX)) $gedrec .= "2 SPFX $SPFX\r\n";
	if (!empty($SURN)) $gedrec .= "2 SURN $SURN\r\n";
	if (!empty($NSFX)) $gedrec .= "2 NSFX $NSFX\r\n";
	if (!empty($ROMN)) $gedrec .= "2 ROMN $ROMN\r\n";
	if (!empty($FONE)) $gedrec .= "2 FONE $FONE\r\n";
	if (!empty($_AKA)) $gedrec .= "2 _AKA $_AKA\r\n";
	if (!empty($_HEB)) $gedrec .= "2 _HEB $_HEB\r\n";
	if (!empty($_MARNM)) $gedrec .= "2 _MARNM $_MARNM\r\n";
	$gedrec .= "1 SEX $SEX\r\n";
	if ((!empty($BIRT_DATE))||(!empty($BIRT_PLAC))) {
		$gedrec .= "1 BIRT\r\n";
		if (!empty($BIRT_DATE)) {
			$BIRT_DATE = check_input_date($BIRT_DATE);
			$gedrec .= "2 DATE $BIRT_DATE\r\n";
		}
		if (!empty($BIRT_PLAC)) {
			$gedrec .= "2 PLAC $BIRT_PLAC\r\n";
			if (!empty($BIRT_ROMN)) $gedrec .= "3 ROMN $BIRT_ROMN\r\n";
			if (!empty($BIRT_FONE)) $gedrec .= "3 FONE $BIRT_FONE\r\n";
			if (!empty($BIRT__HEB)) $gedrec .= "3 _HEB $BIRT__HEB\r\n";
			if ((!empty($BIRT_LATI))||(!empty($BIRT_LONG))) {
				$gedrec .= "3 MAP\r\n";
				$gedrec .= "4 LATI $BIRT_LATI\r\n";
				$gedrec .= "4 LONG $BIRT_LONG\r\n";
			}
		}
		if (isset($SOUR_BIRT) and $SOUR_BIRT and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
			$gedrec .= "2 SOUR @".$text[0]."@\r\n";
			if ($tag[1]=="PAGE" and $text[1]!="") $gedrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
		}
	}
	else if (!empty($BIRT)) $gedrec .= "1 BIRT Y\r\n";
	if ((!empty($DEAT_DATE))||(!empty($DEAT_PLAC))) {
		$gedrec .= "1 DEAT\r\n";
		if (!empty($DEAT_DATE)) {
			$DEAT_DATE = check_input_date($DEAT_DATE);
			$gedrec .= "2 DATE $DEAT_DATE\r\n";
		}
		if (!empty($DEAT_PLAC)) {
			$gedrec .= "2 PLAC $DEAT_PLAC\r\n";
			if (!empty($DEAT_ROMN)) $gedrec .= "3 ROMN $DEAT_ROMN\r\n";
			if (!empty($DEAT_FONE)) $gedrec .= "3 FONE $DEAT_FONE\r\n";
			if (!empty($DEAT__HEB)) $gedrec .= "3 _HEB $DEAT__HEB\r\n";
			if ((!empty($DEAT_LATI))||(!empty($DEAT_LONG))) {
				$gedrec .= "3 MAP\r\n";
				$gedrec .= "4 LATI $DEAT_LATI\r\n";
				$gedrec .= "4 LONG $DEAT_LONG\r\n";
			}
		}
		if (isset($SOUR_DEAT) and $SOUR_DEAT and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
			$gedrec .= "2 SOUR @".$text[0]."@\r\n";
			if ($tag[1]=="PAGE" and $text[1]!="") $gedrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
		}
	}
	else if (!empty($DEAT)) $gedrec .= "1 DEAT Y\r\n";
	$gedrec = handle_updates($gedrec);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	$xref = append_gedrec($gedrec);
	if ($xref) print "<br /><br />".$pgv_lang["update_successful"];
	else exit;
	$spouserec = $gedrec;
	$success = true;
	if ($famid=="new") {
		$famrec = "0 @new@ FAM\r\n";
		if ($famtag=="HUSB") {
			$famrec .= "1 HUSB @$xref@\r\n";
			$famrec .= "1 CHIL @$pid@\r\n";
		}
		else {
			$famrec .= "1 WIFE @$xref@\r\n";
			$famrec .= "1 CHIL @$pid@\r\n";
		}
		if ((!empty($MARR_DATE))||(!empty($MARR_PLAC))) {
			$famrec .= "1 MARR\r\n";
			if (!empty($MARR_DATE)) {
				$MARR_DATE = check_input_date($MARR_DATE);
				$famrec .= "2 DATE $MARR_DATE\r\n";
			}
			if (!empty($MARR_PLAC)) {
				$famrec .= "2 PLAC $MARR_PLAC\r\n";
				if (!empty($MARR_ROMN)) $famrec .= "3 ROMN $MARR_ROMN\r\n";
				if (!empty($MARR_FONE)) $famrec .= "3 FONE $MARR_FONE\r\n";
				if (!empty($MARR__HEB)) $famrec .= "3 _HEB $MARR__HEB\r\n";
				if ((!empty($MARR_LATI))||(!empty($MARR_LONG))) {
					$famrec .= "3 MAP\r\n";
					$famrec .= "4 LATI $MARR_LATI\r\n";
					$famrec .= "4 LONG $MARR_LONG\r\n";
				}
			}
			if (isset($SOUR_MARR) and $SOUR_MARR and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
				$famrec .= "2 SOUR @".$text[0]."@\r\n";
				if ($tag[1]=="PAGE" and $text[1]!="") $famrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
			}
		}
		else if (!empty($MARR)) $famrec .= "1 MARR Y\r\n";

		if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
		$famid = append_gedrec($famrec);
	}
	else if (!empty($famid)) {
		$famrec = "";
		if (isset($pgv_changes[$famid."_".$GEDCOM])) $famrec = find_updated_record($famid);
		else $famrec = find_family_record($famid);
		if (!empty($famrec)) {
			$famrec = trim($famrec);
			$famrec .= "\r\n1 $famtag @$xref@\r\n";
			if ((!empty($MARR_DATE))||(!empty($MARR_PLAC))) {
				$famrec .= "1 MARR\r\n";
				if (!empty($MARR_DATE)) {
					$MARR_DATE = check_input_date($MARR_DATE);
					$famrec .= "2 DATE $MARR_DATE\r\n";
				}
				if (!empty($MARR_PLAC)) {
					$famrec .= "2 PLAC $MARR_PLAC\r\n";
					if (!empty($MARR_ROMN)) $famrec .= "3 ROMN $MARR_ROMN\r\n";
					if (!empty($MARR_FONE)) $famrec .= "3 FONE $MARR_FONE\r\n";
					if (!empty($MARR__HEB)) $famrec .= "3 _HEB $MARR__HEB\r\n";
					if ((!empty($MARR_LATI))||(!empty($MARR_LONG))) {
						$famrec .= "3 MAP\r\n";
						$famrec .= "4 LATI $MARR_LATI\r\n";
						$famrec .= "4 LONG $MARR_LONG\r\n";
					}
				}
				if (isset($SOUR_MARR) and $SOUR_MARR and $tag[0]=="SOUR" and $text[0]!="" and $islink[0]) {
					$famrec .= "2 SOUR @".$text[0]."@\r\n";
					if ($tag[1]=="PAGE" and $text[1]!="") $famrec .= "3 ".$tag[1]." ".$text[1]."\r\n";
				}
			}
			else if (!empty($MARR)) $famrec .= "1 MARR Y\r\n";
			if ($GLOBALS["DEBUG"]) print "<pre>$famrec</pre>";
			replace_gedrec($famid, $famrec);
		}
	}
	if ((!empty($famid))&&($famid!="new")) {
			/**
			$gedrec = "";
			if (isset($pgv_changes[$xref."_".$GEDCOM])) $gedrec = find_updated_record($xref);
			else $gedrec = find_person_record($xref);
			**/
			$gedrec = $spouserec;
			$gedrec = trim($gedrec);
			$gedrec .= "\r\n1 FAMS @$famid@\r\n";
			if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
			replace_gedrec($xref, $gedrec);
	}
	if (!empty($pid)) {
		$indirec="";
		if (!isset($pgv_changes[$pid."_".$GEDCOM])) $indirec = find_gedcom_record($pid);
		else $indirec = find_updated_record($pid);
		$indirec = trim($indirec);
		if ($indirec) {
			$ct = preg_match("/1 FAMC @$famid@/", $indirec);
			if ($ct==0) {
				$indirec = trim($indirec);
				$indirec .= "\r\n1 FAMC @$famid@\r\n";
				if ($GLOBALS["DEBUG"]) print "<pre>$indirec</pre>";
				replace_gedrec($pid, $indirec);
			}
		}
	}
	break;
//------------------------------------------------------------------------------
case 'deleteperson':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	if (!$factedit) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
		if (!empty($famid)) print "<br />".$pgv_lang["privacy_not_granted"]." famid $famid.";
	}
	else {
		if (delete_person($pid, $gedrec)) print "<br /><br />".$pgv_lang["gedrec_deleted"];
	}
	break;
//------------------------------------------------------------------------------
case 'deletefamily':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	if (!$factedit) {
		print "<br />".$pgv_lang["privacy_prevented_editing"];
		if (!empty($pid)) print "<br />".$pgv_lang["privacy_not_granted"]." pid $pid.";
		if (!empty($famid)) print "<br />".$pgv_lang["privacy_not_granted"]." famid $famid.";
	}
	else
	{
		if (delete_family($famid, $gedrec)) print "<br /><br />".$pgv_lang["gedrec_deleted"];
	}
	break;
//------------------------------------------------------------------------------
case 'deletesource':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	if (!empty($gedrec)) {
		$success = true;
		$query = "SOUR @$pid@";
		// -- array of names
		$myindilist = array();
		$myfamlist = array();

		$myindilist = search_indis($query);
		foreach($myindilist as $key=>$value) {
			if (!isset($pgv_changes[$key."_".$GEDCOM])) $indirec = $value["gedcom"];
			else $indirec = find_updated_record($key);
			$lines = preg_split("/\n/", $indirec);
			$newrec = "";
			$skipline = false;
			$glevel = 0;
			foreach($lines as $indexval => $line) {
				if ((preg_match("/@$pid@/", $line)==0)&&(!$skipline)) $newrec .= $line."\n";
				else {
					if (!$skipline) {
						$glevel = $line{0};
						$skipline = true;
					}
					else {
						if ($line{0}<=$glevel) {
							$skipline = false;
							$newrec .= $line."\n";
						}
					}
				}
			}
			if ($GLOBALS["DEBUG"]) print "<pre>$newrec</pre>";
			$success = $success && replace_gedrec($key, $newrec);
		}
		$myfamlist = search_fams($query);
		foreach($myfamlist as $key=>$value) {
			if (!isset($pgv_changes[$key."_".$GEDCOM])) $indirec = $value["gedcom"];
			else $indirec = find_updated_record($key);
			$lines = preg_split("/\n/", $indirec);
			$newrec = "";
			$skipline = false;
			$glevel = 0;
			foreach($lines as $indexval => $line) {
				if ((preg_match("/@$pid@/", $line)==0)&&(!$skipline)) $newrec .= $line."\n";
				else {
					if (!$skipline) {
						$glevel = $line{0};
						$skipline = true;
					}
					else {
						if ($line{0}<=$glevel) {
							$skipline = false;
							$newrec .= $line."\n";
						}
					}
				}
			}
			if ($GLOBALS["DEBUG"]) print "<pre>$newrec</pre>";
			$success = $success && replace_gedrec($key, $newrec);
		}
		if ($success) {
			$success = $success && delete_gedrec($pid);
		}
		if ($success) print "<br /><br />".$pgv_lang["gedrec_deleted"];
	}
	break;
//------------------------------------------------------------------------------
case 'deleterepo':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	if (!empty($gedrec)) {
		$success = true;
		$query = "REPO @$pid@";
		// -- array of names
		$mysourlist = array();

		$mysourlist = search_sources($query);
		foreach($mysourlist as $key=>$value) {
			if (!isset($pgv_changes[$key."_".$GEDCOM])) $sourrec = $value["gedcom"];
			else $sourrec = find_updated_record($key);
			$lines = preg_split("/\n/", $sourrec);
			$newrec = "";
			$skipline = false;
			$glevel = 0;
			foreach($lines as $indexval => $line) {
				if ((preg_match("/@$pid@/", $line)==0)&&(!$skipline)) $newrec .= $line."\n";
				else {
					if (!$skipline) {
						$glevel = $line{0};
						$skipline = true;
					}
					else {
						if ($line{0}<=$glevel) {
							$skipline = false;
							$newrec .= $line."\n";
						}
					}
				}
			}
			if ($GLOBALS["DEBUG"]) print "<pre>$newrec</pre>";
			$success = $success && replace_gedrec($key, $newrec);
		}
		if ($success) {
			$success = $success && delete_gedrec($pid);
		}
		if ($success) print "<br /><br />".$pgv_lang["gedrec_deleted"];
	}
	break;
//------------------------------------------------------------------------------
case 'editname':
	$gedlines = preg_split("/\n/", trim($gedrec));
	$fields = preg_split("/\s/", $gedlines[$linenum]);
	$glevel = $fields[0];
	$i = $linenum+1;
	$namerec = $gedlines[$linenum];
	while(($i<count($gedlines))&&($gedlines[$i]{0}>$glevel)) {
		$namerec.="\n".$gedlines[$i];
		$i++;
	}
	print_indi_form("update", "", $linenum, $namerec);
	break;
//------------------------------------------------------------------------------
case 'addname':
	print_indi_form("update", "", "new", "NEW");
	break;
//------------------------------------------------------------------------------
case 'copy':
	//-- handle media differently now :P
	if ($linenum=='media') {
		$factrec = "1 OBJE @".$pid."@";
		$type="all";
		print "<br />";
	}
	else {
		$gedlines = preg_split("/\n/", trim($gedrec));
		$fields = preg_split("/\s/", $gedlines[$linenum]);
		$glevel = $fields[0];
		$i = $linenum+1;
		$factrec = $gedlines[$linenum];
		while(($i<count($gedlines))&&($gedlines[$i]{0}>$glevel)) {
			$factrec.="\n".$gedlines[$i];
			$i++;
		}
	}
	if (!isset($_SESSION["clipboard"])) $_SESSION["clipboard"] = array();
	$ft = preg_match("/1 (_?[A-Z]{3,5})(.*)/", $factrec, $match);
	if ($ft>0) {
		$fact = trim($match[1]);
		if ($fact=="EVEN" || $fact=="FACT") {
			$ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
			if ($ct>0) $fact = trim($match[1]);
		}
		if (count($_SESSION["clipboard"])>4) array_pop($_SESSION["clipboard"]);
		$_SESSION["clipboard"][] = array("type"=>$type, "factrec"=>$factrec, "fact"=>$fact);
		print "<b>".$pgv_lang["record_copied"]."</b>\n";
		$success = true;
	}
	break;
//------------------------------------------------------------------------------
case 'paste':
	$gedrec .= "\r\n".$_SESSION["clipboard"][$fact]["factrec"];
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	if ($GLOBALS["DEBUG"]) print "<pre>$gedrec</pre>";
	$success = replace_gedrec($pid, $gedrec);
	if ($success) print "<br /><br />".$pgv_lang["update_successful"];
	break;
//------------------------------------------------------------------------------
case 'reorder_children':
	require_once("js/prototype.js.htm");
	require_once("js/scriptaculous.js.htm");
	print "<br /><b>".$pgv_lang["reorder_children"]."</b>";
	print_help_link("reorder_children_help", "qm");
	?>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="reorder_update" />
		<input type="hidden" name="pid" value="<?php print $pid; ?>" />
		<input type="hidden" name="option" value="bybirth" />
		<ul id="reorder_list">
		<?php
			$children = array();
			$ct = preg_match_all("/1 CHIL @(.+)@/", $gedrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$child = trim($match[$i][1]);
				$irec = find_person_record($child);
				if ($irec===false) $irec = find_updated_record($child);
				if (isset($indilist[$child])) $children[$child] = $indilist[$child];
			}
			if ((!empty($option))&&($option=="bybirth")) {
				uasort($children, "compare_date");
			}
			$i=0;
			foreach($children as $pid=>$child) {
				print "<li class=\"facts_value\" style=\"cursor:move;margin-bottom:2px;\" id=\"li_$pid\" >";
				//print_pedigree_person($pid,2,false);
				print "<span class=\"name2\">".PrintReady(get_person_name($pid))."</span>";
				print_first_major_fact($pid);
				print "<input type=\"hidden\" name=\"order[$pid]\" value=\"$i\"/>";
				print "</li>";
				$i++;
			}
		?>
		</ul>
<script type="text/javascript" language="javascript">
// <![CDATA[
	new Effect.BlindDown('reorder_list', {duration: 1});
	Sortable.create('reorder_list',
		{
			onUpdate : function() {
				inputs = $('reorder_list').getElementsByTagName("input");
				for (var i = 0; i < inputs.length; i++) inputs[i].value = i;
			}
		}
	);
// ]]>
</script>
		<button type="submit"><?php print $pgv_lang["save"];?></button>
		<button type="submit" onclick="document.reorder_form.action.value='reorder_children'; document.reorder_form.submit();"><?php print $pgv_lang["sort_by_birth"];?></button>
		<button type="submit" onclick="window.close();"><?php print $pgv_lang["cancel"];?></button>
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
case 'changefamily':
	require_once 'includes/family_class.php';
	$family = new Family($gedrec);
	$father = $family->getHusband();
	$mother = $family->getWife();
	$children = $family->getChildren();
	if (count($children)>0) {
		if (!is_null($father)) {
			if ($father->getSex()=="F") $father->setLabel($pgv_lang["mother"]);
			else $father->setLabel($pgv_lang["father"]);
		}
		if (!is_null($mother)) {
			if ($mother->getSex()=="M") $mother->setLabel($pgv_lang["father"]);
			else $mother->setLabel($pgv_lang["mother"]);
		}
		for($i=0; $i<count($children); $i++) {
			if (!is_null($children[$i])) {
				if ($children[$i]->getSex()=="M") $children[$i]->setLabel($pgv_lang["son"]);
				else if ($children[$i]->getSex()=="F") $children[$i]->setLabel($pgv_lang["daughter"]);
				else $children[$i]->setLabel($pgv_lang["child"]);
			}
		}
	}
	else {
		if (!is_null($father)) {
			if ($father->getSex()=="F") $father->setLabel($pgv_lang["wife"]);
			else if ($father->getSex()=="M") $father->setLabel($pgv_lang["husband"]);
			else $father->setLabel($pgv_lang["spouse"]);
		}
		if (!is_null($mother)) {
			if ($mother->getSex()=="F") $mother->setLabel($pgv_lang["wife"]);
			else if ($mother->getSex()=="M") $mother->setLabel($pgv_lang["husband"]);
			else $father->setLabel($pgv_lang["spouse"]);
		}
	}
	?>
	<script type="text/javascript">
	<!--
	var nameElement = null;
	var remElement = null;
	function pastename(name) {
		if (nameElement) {
			nameElement.innerHTML = name;
		}
		if (remElement) {
			remElement.style.display = 'block';
		}
	}
	//-->
	</script>
	<br /><br />
	<?php print $pgv_lang["change_family_instr"]; ?>
	<form name="changefamform" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="changefamily_update" />
		<input type="hidden" name="famid" value="<?php print $famid;?>" />
		<table class="width50 <?php print $TEXT_DIRECTION; ?>">
			<tr><td colspan="3" class="topbottombar"><?php print $pgv_lang["change_family_members"]; ?></td></tr>
			<tr>
			<?php
			if (!is_null($father)) {
			?>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?>"><b><?php print $father->getLabel(); ?></b><input type="hidden" name="HUSB" value="<?php print $father->getXref();?>" /></td>
				<td id="HUSBName" class="optionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print PrintReady($father->getName()); ?></td>
			<?php
			}
			else {
			?>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?>"><b><?php print $pgv_lang["spouse"]; ?></b><input type="hidden" name="HUSB" value="" /></td>
				<td id="HUSBName" class="optionbox wrap <?php print $TEXT_DIRECTION; ?>"></td>
			<?php
			}
			?>
				<td class="optionbox wrap <?php print $TEXT_DIRECTION; ?>">
					<a href="javascript:;" id="husbrem" style="display: <?php print is_null($father) ? 'none':'block'; ?>;" onclick="document.changefamform.HUSB.value=''; document.getElementById('HUSBName').innerHTML=''; this.style.display='none'; return false;"><?php print $pgv_lang["remove"]; ?></a>
					<a href="javascript:;" onclick="nameElement = document.getElementById('HUSBName'); remElement = document.getElementById('husbrem'); return findIndi(document.changefamform.HUSB);"><?php print $pgv_lang["change"]; ?></a><br />
				</td>
			</tr>
			<tr>
			<?php
			if (!is_null($mother)) {
			?>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?>"><b><?php print $mother->getLabel(); ?></b><input type="hidden" name="WIFE" value="<?php print $mother->getXref();?>" /></td>
				<td id="WIFEName" class="optionbox wrap <?php print $TEXT_DIRECTION; ?>"><?php print PrintReady($mother->getName()); ?></td>
			<?php
			}
			else {
			?>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?>"><b><?php print $pgv_lang["spouse"]; ?></b><input type="hidden" name="WIFE" value="" /></td>
				<td id="WIFEName" class="optionbox wrap <?php print $TEXT_DIRECTION; ?>"></td>
			<?php
			}
			?>
				<td class="optionbox wrap <?php print $TEXT_DIRECTION; ?>">
					<a href="javascript:;" id="wiferem" style="display: <?php print is_null($mother) ? 'none':'block'; ?>;" onclick="document.changefamform.WIFE.value=''; document.getElementById('WIFEName').innerHTML=''; this.style.display='none'; return false;"><?php print $pgv_lang["remove"]; ?></a>
					<a href="javascript:;" onclick="nameElement = document.getElementById('WIFEName'); remElement = document.getElementById('wiferem'); return findIndi(document.changefamform.WIFE);"><?php print $pgv_lang["change"]; ?></a><br />
				</td>
			</tr>
			<?php
			$i=0;
			foreach($children as $key=>$child) {
				if (!is_null($child)) {
				?>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?>"><b><?php print $child->getLabel(); ?></b><input type="hidden" name="CHIL<?php print $i; ?>" value="<?php print $child->getXref();?>" /></td>
				<td id="CHILName<?php print $i; ?>" class="optionbox wrap"><?php print PrintReady($child->getName()); ?></td>
				<td class="optionbox wrap <?php print $TEXT_DIRECTION; ?>">
					<a href="javascript:;" id="childrem<?php print $i; ?>" style="display: block;" onclick="document.changefamform.CHIL<?php print $i; ?>.value=''; document.getElementById('CHILName<?php print $i; ?>').innerHTML=''; this.style.display='none'; return false;"><?php print $pgv_lang["remove"]; ?></a>
					<a href="javascript:;" onclick="nameElement = document.getElementById('CHILName<?php print $i; ?>'); remElement = document.getElementById('childrem<?php print $i; ?>'); return findIndi(document.changefamform.CHIL<?php print $i; ?>);"><?php print $pgv_lang["change"]; ?></a><br />
				</td>
			</tr>
				<?php
					$i++;
				}
			}
				?>
			<tr>
				<td class="descriptionbox <?php print $TEXT_DIRECTION; ?>"><b><?php print $pgv_lang["add_child"]; ?></b><input type="hidden" name="CHIL<?php print $i; ?>" value="" /></td>
				<td id="CHILName<?php print $i; ?>" class="optionbox wrap"></td>
				<td class="optionbox wrap <?php print $TEXT_DIRECTION; ?>">
					<a href="javascript:;" id="childrem<?php print $i; ?>" style="display: none;" onclick="document.changefamform.CHIL<?php print $i; ?>.value=''; document.getElementById('CHILName<?php print $i; ?>').innerHTML=''; this.style.display='none'; return false;"><?php print $pgv_lang["remove"]; ?></a>
					<a href="javascript:;" onclick="nameElement = document.getElementById('CHILName<?php print $i; ?>'); remElement = document.getElementById('childrem<?php print $i; ?>'); return findIndi(document.changefamform.CHIL<?php print $i; ?>);"><?php print $pgv_lang["change"]; ?></a><br />
				</td>
			</tr>
		</table>
		<!-- <a href="javascript: <?php print $pgv_lang["add_unlinked_person"]; ?>" onclick="addnewchild(''); return false;"><?php print $pgv_lang["add_unlinked_person"]; ?></a><br />-->
		<br />
		<input type="submit" value="<?php print $pgv_lang["save"]; ?>" /><input type="button" value="<?php print $pgv_lang["cancel"]; ?>" onclick="window.close();" />
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
case 'changefamily_update':
	require_once 'includes/family_class.php';
	$family = new Family($gedrec);
	$father = $family->getHusband();
	$mother = $family->getWife();
	$children = $family->getChildren();
	$updated = false;
	//-- add the new father link
	if (!empty($HUSB) && (is_null($father) || $father->getXref()!=$HUSB)) {
		if (strstr($gedrec, "1 HUSB")!==false)
			$gedrec = preg_replace("/1 HUSB @.*@/", "1 HUSB @$HUSB@", $gedrec);
		else $gedrec .= "\r\n1 HUSB @$HUSB@";
		if (isset($pgv_changes[$HUSB."_".$GEDCOM])) $indirec = find_updated_record($HUSB);
		else $indirec = find_person_record($HUSB);
		if (!empty($indirec) && (preg_match("/1 FAMS @$famid@/", $indirec)==0)) {
			$indirec .= "\r\n1 FAMS @$famid@";
			replace_gedrec($HUSB, $indirec);
		}
		$updated = true;
	}
	//-- remove the father link
	if (empty($HUSB)) {
		$pos1 = strpos($gedrec, "1 HUSB @");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+5);
			if ($pos2===false) $pos2 = strlen($gedrec);
			else $pos2++;
			$gedrec = substr($gedrec, 0, $pos1) . substr($gedrec, $pos2);
		}
		$updated = true;
	}
	//-- remove the FAMS link from the old father
	if (!is_null($father) && $father->getXref()!=$HUSB) {
		if (isset($pgv_changes[$father->getXref()."_".$GEDCOM])) $indirec = find_updated_record($father->getXref());
		else $indirec = find_person_record($father->getXref());
		$pos1 = strpos($indirec, "1 FAMS @$famid@");
		if ($pos1!==false) {
			$pos2 = strpos($indirec, "\n1", $pos1+5);
			if ($pos2===false) $pos2 = strlen($indirec);
			else $pos2++;
			$indirec = substr($indirec, 0, $pos1) . substr($indirec, $pos2);
			replace_gedrec($father->getXref(), $indirec);
		}
	}
	//-- add the new mother link
	if (!empty($WIFE) && (is_null($mother) || $mother->getXref()!=$WIFE)) {
		if (strstr($gedrec, "1 WIFE")!==false)
			$gedrec = preg_replace("/1 WIFE @.*@/", "1 WIFE @$WIFE@", $gedrec);
		else $gedrec .= "\r\n1 WIFE @$WIFE@";
		if (isset($pgv_changes[$WIFE."_".$GEDCOM])) $indirec = find_updated_record($WIFE);
		else $indirec = find_person_record($WIFE);
		if (!empty($indirec) && (preg_match("/1 FAMS @$famid@/", $indirec)==0)) {
			$indirec .= "\r\n1 FAMS @$famid@";
			replace_gedrec($WIFE, $indirec);
		}
		$updated = true;
	}
	//-- remove the father link
	if (empty($WIFE)) {
		$pos1 = strpos($gedrec, "1 WIFE @");
		if ($pos1!==false) {
			$pos2 = strpos($gedrec, "\n1", $pos1+5);
			if ($pos2===false) $pos2 = strlen($gedrec);
			else $pos2++;
			$gedrec = substr($gedrec, 0, $pos1) . substr($gedrec, $pos2);
		}
		$updated = true;
	}
	//-- remove the FAMS link from the old father
	if (!is_null($mother) && $mother->getXref()!=$WIFE) {
		if (isset($pgv_changes[$mother->getXref()."_".$GEDCOM])) $indirec = find_updated_record($mother->getXref());
		else $indirec = find_person_record($mother->getXref());
		$pos1 = strpos($indirec, "1 FAMS @$famid@");
		if ($pos1!==false) {
			$pos2 = strpos($indirec, "\n1", $pos1+5);
			if ($pos2===false) $pos2 = strlen($indirec);
			else $pos2++;
			$indirec = substr($indirec, 0, $pos1) . substr($indirec, $pos2);
			replace_gedrec($mother->getXref(), $indirec);
		}
	}

	//-- update the children
	$i=0;
	$var = "CHIL".$i;
	$newchildren = array();
	while(isset($$var)) {
		$CHIL = $$var;
		if (!empty($CHIL)) {
			$newchildren[] = $CHIL;
			if (preg_match("/1 CHIL @$CHIL@/", $gedrec)==0) {
				$gedrec .= "\r\n1 CHIL @$CHIL@";
				$updated = true;
				if (isset($pgv_changes[$CHIL."_".$GEDCOM])) $indirec = find_updated_record($CHIL);
				else $indirec = find_person_record($CHIL);
				if (!empty($indirec) && (preg_match("/1 FAMC @$famid@/", $indirec)==0)) {
					$indirec .= "\r\n1 FAMC @$famid@";
					replace_gedrec($CHIL, $indirec);
				}
			}
		}
		$i++;
		$var = "CHIL".$i;
	}

	//-- remove the old children
	foreach($children as $key=>$child) {
		if (!is_null($child)) {
			if (!in_array($child->getXref(), $newchildren)) {
				//-- remove the CHIL link from the family record
				$pos1 = strpos($gedrec, "1 CHIL @".$child->getXref()."@");
				if ($pos1!==false) {
					$pos2 = strpos($gedrec, "\n1", $pos1+5);
					if ($pos2===false) $pos2 = strlen($gedrec);
					else $pos2++;
					$gedrec = substr($gedrec, 0, $pos1) . substr($gedrec, $pos2);
					$updated = true;
				}
				//-- remove the FAMC link from the child record
				if (isset($pgv_changes[$child->getXref()."_".$GEDCOM])) $indirec = find_updated_record($child->getXref());
				else $indirec = find_person_record($child->getXref());
				$pos1 = strpos($indirec, "1 FAMC @$famid@");
				if ($pos1!==false) {
					$pos2 = strpos($indirec, "\n1", $pos1+5);
					if ($pos2===false) $pos2 = strlen($indirec);
					else $pos2++;
					$indirec = substr($indirec, 0, $pos1) . substr($indirec, $pos2);
					replace_gedrec($child->getXref(), $indirec);
				}
			}
		}
	}

	if ($updated) {
		$success = replace_gedrec($famid, $gedrec);
		if ($success) print "<br /><br />".$pgv_lang["update_successful"];
	}
	break;
//------------------------------------------------------------------------------
case 'reorder_update':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	asort($order);
	reset($order);
	$newgedrec = $gedrec;
	foreach($order as $child=>$num) {
		// move each child subrecord to the bottom
		$subrec = get_sub_record(1, "1 CHIL @".$child."@", $gedrec);
		$subrec = trim($subrec,"\r\n");
		if ($GLOBALS["DEBUG"]) echo "<pre>[".$subrec."]</pre>";
		$newgedrec = str_replace($subrec,"", $newgedrec);
		$newgedrec .= $subrec."\r\n";
	}
	if ($GLOBALS["DEBUG"]) print "<pre>$newgedrec</pre>";
	$success = (replace_gedrec($pid, $newgedrec));
	if ($success) print "<br /><br />".$pgv_lang["update_successful"];
	break;
//------------------------------------------------------------------------------
case 'reorder_fams':
	require_once("js/prototype.js.htm");
	require_once("js/scriptaculous.js.htm");
	print "<br /><b>".$pgv_lang["reorder_families"]."</b>";
	print_help_link("reorder_families_help", "qm");
	?>
	<form name="reorder_form" method="post" action="edit_interface.php">
		<input type="hidden" name="action" value="reorder_fams_update" />
		<input type="hidden" name="pid" value="<?php print $pid; ?>" />
		<input type="hidden" name="option" value="bymarriage" />
		<ul id="reorder_list">
		<?php
			$fams = array();
			$ct = preg_match_all("/1 FAMS @(.+)@/", $gedrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$famid = trim($match[$i][1]);
				$frec = find_family_record($famid);
				if ($frec===false) $frec = find_updated_record($famid);
				if (isset($famlist[$famid])) $fams[$famid] = $famlist[$famid];
			}
			if ((!empty($option))&&($option=="bymarriage")) {
				$sortby = "MARR";
				uasort($fams, "compare_date");
			}
			$i=0;
			foreach($fams as $famid=>$fam) {
				print "<li class=\"facts_value\" style=\"cursor:move;margin-bottom:2px;\" id=\"li_$famid\" >";
				print "<span class=\"name2\">".PrintReady(get_family_descriptor($famid))."</span><br />";
				print_simple_fact($fam["gedcom"], "MARR", $famid);
				print "<input type=\"hidden\" name=\"order[$famid]\" value=\"$i\"/>";
				print "</li>";
				$i++;
			}
		?>
		</ul>
<script type="text/javascript" language="javascript">
// <![CDATA[
	new Effect.BlindDown('reorder_list', {duration: 1});
	Sortable.create('reorder_list',
		{
			onUpdate : function() {
				inputs = $('reorder_list').getElementsByTagName("input");
				for (var i = 0; i < inputs.length; i++) inputs[i].value = i;
			}
		}
	);
// ]]>
</script>
		<button type="submit"><?php print $pgv_lang["save"];?></button>
		<button type="submit" onclick="document.reorder_form.action.value='reorder_fams'; document.reorder_form.submit();"><?php print $pgv_lang["sort_by_marriage"];?></button>
		<button type="submit" onclick="window.close();"><?php print $pgv_lang["cancel"];?></button>
	</form>
	<?php
	break;
//------------------------------------------------------------------------------
case 'reorder_fams_update':
	if ($GLOBALS["DEBUG"]) phpinfo(32);
	asort($order);
	reset($order);
	$lines = preg_split("/\n/", $gedrec);
	$newgedrec = "";
	for($i=0; $i<count($lines); $i++) {
		if (preg_match("/1 FAMS/", $lines[$i])==0) $newgedrec .= $lines[$i]."\n";
	}
	foreach($order as $famid=>$num) {
		$newgedrec .= "1 FAMS @".$famid."@\r\n";
	}
	if ($GLOBALS["DEBUG"]) print "<pre>$newgedrec</pre>";
	$success = (replace_gedrec($pid, $newgedrec));
	if ($success) print "<br /><br />".$pgv_lang["update_successful"];
	break;
//------------------------------------------------------------------------------
//-- the following section provides a hook for modules
//-- for reuse of editing functions from forms
case 'mod_edit_fact':
	include_once('modules/'.$mod.'/'.$mod.'.php');
	$module = new $mod();
	if (method_exists($module, "edit_fact")) {
		$module->edit_fact();
	}
	break;
}
//------------------------------------------------------------------------------
// autoclose window when update successful
if ($success and $EDIT_AUTOCLOSE and !$GLOBALS["DEBUG"]) {
	if ($action=="copy") print "\n<script type=\"text/javascript\">\n<!--\nwindow.close();\n//-->\n</script>";
	else print "\n<script type=\"text/javascript\">\n<!--\nedit_close();\n//-->\n</script>";
}

print "<div class=\"center\"><a href=\"javascript:;\" onclick=\"edit_close();\">".$pgv_lang["close_window"]."</a></div><br />\n";
print_simple_footer();
?>

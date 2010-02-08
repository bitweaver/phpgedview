<?php
/**
 * Family Tree Clippings Cart
 *
 * Uses the $_SESSION["cart"] to store the ids of clippings to download
 * @TODO print a message if people are not included due to privacy
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @version $Id: clippings.php,v 1.7 2010/02/08 21:27:24 wjames5 Exp $
 */

/**
 * Initialization
 */ 
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/controllers/clippings_ctrl.php");
$controller = new ClippingsController();
$controller->init();

// -- print html header information
print_header($pgv_lang["clip_cart"]);

if ($ENABLE_AUTOCOMPLETE) require './js/autocomplete.js.htm';
require './js/sorttable.js.htm';

echo PGV_JS_START;
echo 'function radAncestors(elementid) {var radFamilies=document.getElementById(elementid);radFamilies.checked=true;}';
echo PGV_JS_END;

if (count($cart)==0) {?>
<h2><?php print $pgv_lang["clippings_cart"];?></h2>
<?php }

if ($controller->action=='add') {
	$person = GedcomRecord::getInstance($controller->id);
	print "<b>".$person->getFullName()."</b>";
	if ($controller->type=='fam') {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print $pgv_lang["which_links"]?>
			<input type="hidden" name="id" value="<?php print $controller->id; ?>" />
			<input type="hidden" name="type" value="<?php print $controller->type ?>" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print $pgv_lang["just_family"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" /><?php print $pgv_lang["parents_and_family"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" /><?php print $pgv_lang["parents_and_child"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" /><?php print $pgv_lang["parents_desc"]?></td></tr>
			<tr><td class="topbottombar"><input type="submit" value="<?php print $pgv_lang["continue"]?>" /></td></tr>

		</table>
		</form>
	<?php }
	else if ($controller->type=='indi') {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print $pgv_lang["which_p_links"]?>
			<input type="hidden" name="id" value="<?php print $controller->id; ?>" />
			<input type="hidden" name="type" value="<?php print $controller->type ?>" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print $pgv_lang["just_person"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" /><?php print $pgv_lang["person_parents_sibs"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="ancestors" id="ancestors" /><?php print $pgv_lang["person_ancestors"]?><br />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $pgv_lang["enter_person_generations"] ?> <input type="text" size="5" name="level1" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestors');"/></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="ancestorsfamilies" id="ancestorsfamilies" /><?php print $pgv_lang["person_ancestor_fams"]?><br >
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $pgv_lang["enter_person_generations"] ?> <input type="text" size="5" name="level2" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('ancestorsfamilies');" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" /><?php print $pgv_lang["person_spouse"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" id="descendants" /><?php print $pgv_lang["person_desc"]?><br >
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $pgv_lang["enter_person_generations"] ?> <input type="text" size="5" name="level3" value="<?php print $MAX_PEDIGREE_GENERATIONS; ?>" onfocus="radAncestors('descendants');" /></td></tr>
			<tr><td class="topbottombar"><input type="submit" value="<?php print $pgv_lang["continue"]?>" />
		</table>
		</form>
	<?php } else if ($controller->type=='sour')  {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print $pgv_lang["which_s_links"]?>
			<input type="hidden" name="id" value="<?php print $controller->id; ?>" />
			<input type="hidden" name="type" value="<?php print $controller->type ?>" />
			<input type="hidden" name="action" value="add1" /></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print $pgv_lang["just_source"]?></td></tr>
			<tr><td class="optionbox"><input type="radio" name="others" value="linked" /><?php print $pgv_lang["linked_source"]?></td></tr>
			<tr><td class="topbottombar"><input type="submit" value="<?php print $pgv_lang["continue"]?>" />
		</table>
		</form>
	<?php }
	}
$ct = count($cart);

if ($controller->privCount>0) {
	print "<span class=\"error\">".$pgv_lang["clipping_privacy"]."</span><br /><br />\n";
}

if ($ct==0) {

	// -- new lines, added by Jans, to display helptext when cart is empty
	if ($controller->action!='add') {

		loadLangFile("pgv_help");
		print_text("help_clippings.php");

		echo PGV_JS_START;
		echo 'var pastefield;';
		echo 'function paste_id(value) {pastefield.value=value;}';
		echo PGV_JS_END;
		?>
		<form method="get" name="addin" action="clippings.php">
		<table>
		<tr>
			<td colspan="2" class="topbottombar" style="text-align:center; ">
				<?php print $pgv_lang["add_individual_by_id"];
				print_help_link("add_by_id_help", "qm");?>
			</td>
		</tr>
		<tr>
			<td class="optionbox">
				<input type="hidden" name="action" value="add"/>
				<input type="text" name="id" id="cart_item_id" size="5"/>
			</td>
			<td class="optionbox">
				<?php print_findindi_link('cart_item_id',''); ?>
				<?php print_findfamily_link('cart_item_id',''); ?>
				<?php print_findsource_link('cart_item_id',''); ?>
				<input type="submit" value="<?php print $pgv_lang["add"];?>"/>

			</td>
		</tr>
		</table>
		</form>
		<?php
	}

	// -- end new lines
	print "\r\n\t\t<br /><br />".$pgv_lang["cart_is_empty"]."<br /><br />";
} else {
	if ($controller->action != 'download' && $controller->action != 'add') { ?>
		<form method="get" action="clippings.php">
		<input type="hidden" name="action" value="download" />
		<table><tr><td class="width33" valign="top" rowspan="3">
		<table>
		<tr><td colspan="2" class="topbottombar"><h2><?php print $pgv_lang["file_information"] ?></h2></td></tr>
		<tr>
		<td class="descriptionbox width50 wrap"><?php print_help_link("file_type_help", "qm"); print $pgv_lang["choose_file_type"] ?></td>
		<td class="optionbox">
		<?php if ($TEXT_DIRECTION=='ltr') { ?>
			<input type="radio" name="filetype" checked="checked" value="gedcom" />&nbsp;GEDCOM<br/><input type="radio" name="filetype" value="gramps" DISABLED />&nbsp;Gramps XML <!-- GRAMPS doesn't work right now -->
		<?php } else { ?>
			GEDCOM&nbsp;<?php print getLRM();?><input type="radio" name="filetype" checked="checked" value="gedcom" /><?php print getLRM();?><br />Gramps XML&nbsp;<?php print getLRM();?><input type="radio" name="filetype" value="gramps" /><?php print getLRM();?>
		<?php } ?>
		</td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("zip_help", "qm"); print $pgv_lang["zip_files"]; ?></td>
		<td class="optionbox"><input type="checkbox" name="Zip" value="yes" checked="checked" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("include_media_help", "qm"); print $pgv_lang["include_media"]; ?></td>
		<td class="optionbox"><input type="checkbox" name="IncludeMedia" value="yes" checked="checked" /></td></tr>

		<?php
		// Determine the Privatize options available to this user
		if (PGV_USER_IS_ADMIN) {
			$radioPrivatizeNone = 'checked="checked" ';
			$radioPrivatizeVisitor = '';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = '';
			$radioPrivatizeAdmin = '';
		} else if (PGV_USER_GEDCOM_ADMIN) {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" ';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = '';
			$radioPrivatizeAdmin = 'DISABLED ';
		} else if (PGV_USER_ID) {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" ';
			$radioPrivatizeUser = '';
			$radioPrivatizeGedadmin = 'DISABLED ';
			$radioPrivatizeAdmin = 'DISABLED ';
		} else {
			$radioPrivatizeNone = 'DISABLED ';
			$radioPrivatizeVisitor = 'checked="checked" DISABLED ';
			$radioPrivatizeUser = 'DISABLED ';
			$radioPrivatizeGedadmin = 'DISABLED ';
			$radioPrivatizeAdmin = 'DISABLED ';
		}
		?>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("apply_privacy_help", "qm"); print $pgv_lang["apply_privacy"]; ?></td>
		<td class="list_value">
		<input type="radio" name="privatize_export" value="none" <?php print $radioPrivatizeNone; ?>/>&nbsp;<?php print $pgv_lang["none"]; ?><br />
		<input type="radio" name="privatize_export" value="visitor" <?php print $radioPrivatizeVisitor; ?>/>&nbsp;<?php print $pgv_lang["visitor"]; ?><br />
		<input type="radio" name="privatize_export" value="user" <?php print $radioPrivatizeUser; ?>/>&nbsp;<?php print $pgv_lang["user"]; ?><br />
		<input type="radio" name="privatize_export" value="gedadmin" <?php print $radioPrivatizeGedadmin; ?>/>&nbsp;<?php print $pgv_lang["gedadmin"]; ?><br />
		<input type="radio" name="privatize_export" value="admin" <?php print $radioPrivatizeAdmin; ?>/>&nbsp;<?php print $pgv_lang["siteadmin"]; ?>
		</td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("utf8_ansi_help", "qm"); print $pgv_lang["utf8_to_ansi"]; ?></td>
		<td class="optionbox"><input type="checkbox" name="convert" value="yes" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("remove_tags_help", "qm"); print $pgv_lang["remove_custom_tags"]; ?></td>
		<td class="optionbox"><input type="checkbox" name="remove" value="yes" checked="checked" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("convertPath_help", "qm"); print $pgv_lang["convertPath"];?></td>
		<td class="list_value"><input type="text" name="conv_path" size="30" value="<?php echo getLRM(), $controller->conv_path, getLRM();?>" /></td></tr>

		<tr><td class="descriptionbox width50 wrap"><?php print_help_link("convertSlashes_help", "qm"); print $pgv_lang["convertSlashes"];?></td>
		<td class="list_value">
		<input type="radio" name="conv_slashes" value="forward" <?php if ($controller->conv_slashes=='forward') print "checked=\"checked\" "; ?>/>&nbsp;<?php print $pgv_lang["forwardSlashes"];?><br />
		<input type="radio" name="conv_slashes" value="backward" <?php if ($controller->conv_slashes=='backward') print "checked=\"checked\" "; ?>/>&nbsp;<?php print $pgv_lang["backSlashes"];?>
		</td></tr>

		<tr><td class="topbottombar" colspan="2">
		<input type="submit" value="<?php print $pgv_lang["download_now"]; ?>" />
		</td></tr>
		</form>

		</td></tr>
		</table>
		<br />

		<script language="JavaScript" type="text/javascript">
		<!--
		var pastefield;
		function paste_id(value)
		{
			pastefield.value=value;
		}
		//-->
		</script>
		<form method="get" name="addin" action="clippings.php">
		<table>
		<tr>
			<td colspan="2" class="topbottombar" style="text-align:center; ">
				<?php print_help_link("add_by_id_help", "qm"); print $pgv_lang["add_individual_by_id"]; ?>
			</td>
		</tr>
		<tr>
			<td class="optionbox">
				<input type="hidden" name="action" value="add"/>
				<input type="text" name="id" id="cart_item_id" size="8" />
			</td>
			<td class="optionbox">
				<?php print_findindi_link('cart_item_id',''); ?>
				<?php print_findfamily_link('cart_item_id',''); ?>
				<?php print_findsource_link('cart_item_id',''); ?>
				<input type="submit" value="<?php print $pgv_lang["add"];?>"/>

			</td>
		</tr>
		</table>
		</form>


	<?php } ?>
	<br /><?php print_help_link("empty_cart_help", "qm");?><a href="clippings.php?action=empty"><?php print $pgv_lang["empty_cart"];?></a>
	</td></tr>

	<tr><td class="topbottombar"><h2><?php print_help_link("clip_cart_help", "qm"); print $pgv_lang["clippings_cart"];?></h2></td></tr>

	<tr><td valign="top">
	<table id="mycart" class="sortable list_table width100">
		<tr>
			<th class="list_label"><?php echo $pgv_lang["type"]?></th>
			<th class="list_label"><?php echo $pgv_lang["id"]?></th>
			<th class="list_label"><?php echo $pgv_lang["name_description"]?></th>
			<th class="list_label"><?php echo $pgv_lang["remove"]?></th>
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
			if ($tag=='NOTE') $icon = "notes";
			if ($tag=='OBJE') $icon = "media";
			?>
			<tr><td class="list_value">
				<?php if (!empty($icon)) { ?><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES[$icon]["small"];?>" border="0" alt="<?php echo $tag;?>" title="<?php echo $tag;?>" /><?php } ?>
			</td>
			<td class="list_value ltr"><?php echo $clipping['id']?></td>
			<td class="list_value">
			<?php
			$record=GedcomRecord::getInstance($clipping['id']);
			if ($record) echo '<a href="'.encode_url($record->getLinkUrl()).'">'.PrintReady($record->getListName()).'</a>';
			?>
			</td>
			<td class="list_value center vmiddle"><a href="clippings.php?action=remove&amp;item=<?php echo $i;?>"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES["remove"]["other"];?>" border="0" alt="<?php echo $pgv_lang["remove"]?>" title="<?php echo $pgv_lang["remove"];?>" /></a></td>
		</tr>
		<?php
		}
	}
?>
	</table>
	</td></tr></table>
<?php
}
if (isset($_SESSION["cart"])) $_SESSION["cart"]=$cart;
print_footer();
?>

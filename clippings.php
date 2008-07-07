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
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * @version $Id: clippings.php,v 1.5 2008/07/07 18:01:11 lsces Exp $
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
require_once("includes/controllers/clippings_ctrl.php");

// -- print html header information
print_header($pgv_lang["clip_cart"]); ?>
<h2><?php $pgv_lang["clippings_cart"] ?></h2>
<?php

if ($action=='add') {
	if ($type=='fam') {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print $pgv_lang["which_links"]?>
			<input type="hidden" name="id" value="<?php print $id; ?>" />
			<input type="hidden" name="type" value="<?php print $type ?>" />
			<input type="hidden" name="action" value="add1" /></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print $pgv_lang["just_family"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" /><?php print $pgv_lang["parents_and_family"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" /><?php print $pgv_lang["parents_and_child"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" /><?php print $pgv_lang["parents_desc"]?></tr></td>
			<tr><td class="topbottombar"><input type="submit" value="<?php print $pgv_lang["continue"]?>" /></tr></td>

		</table>
		</form>
	<?php }
	else if ($type=='indi') {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print $pgv_lang["which_p_links"]?>
			<input type="hidden" name="id" value="<?php print $id; ?>" />
			<input type="hidden" name="type" value="<?php print $type ?>" />
			<input type="hidden" name="action" value="add1" /></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print $pgv_lang["just_person"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="parents" /><?php print $pgv_lang["person_parents_sibs"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="ancestors" /><?php print $pgv_lang["person_ancestors"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="ancestorsfamilies" /><?php print $pgv_lang["person_ancestor_fams"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="members" /><?php print $pgv_lang["person_spouse"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="descendants" /><?php print $pgv_lang["person_desc"]?></tr></td>
			<tr><td class="topbottombar"><input type="submit" value="<?php print $pgv_lang["continue"]?>" />
		</table>
		</form>
	<?php } else if ($type=='sour')  {?>
		<form action="clippings.php" method="get">
		<table>
			<tr><td class="topbottombar"><?php print $pgv_lang["which_s_links"]?>
			<input type="hidden" name="id" value="<?php print $id; ?>" />
			<input type="hidden" name="type" value="<?php print $type ?>" />
			<input type="hidden" name="action" value="add1" /></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" checked value="none" /><?php print $pgv_lang["just_source"]?></tr></td>
			<tr><td class="optionbox"><input type="radio" name="others" value="linked" /><?php print $pgv_lang["linked_source"]?></tr></td>
			<tr><td class="topbottombar"><input type="submit" value="<?php print $pgv_lang["continue"]?>" />
		</table>
		</form>
	<?php }
	}
$ct = count($cart);

if ($controller->privCount>0) {
	print "<span class=\"error\">".$pgv_lang["clipping_privacy"]."</span><br /><br />\n";
}

if($ct==0) {

	// -- new lines, added by Jans, to display helptext when cart is empty
	if ($action!='add') {
		
		require $helptextfile["english"];
		if (file_exists($helptextfile[$LANGUAGE])) require $helptextfile[$LANGUAGE];
		print_text("help_clippings.php");

		?>
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
				<?php print $pgv_lang["add_individual_by_id"];
				 print_help_link("add_by_id_help", "qm");?>
			</td>
		</tr>
		<tr>
			<td class="optionbox">
				<input type="hidden" name="action" value="add"/>
				<input type="text" name="id" id="pid" size="5"/>
			</td>
			<td class="optionbox">
				<?php print_findindi_link('pid',''); ?>
				<?php print_findfamily_link('pid',''); ?>
				<?php print_findsource_link('pid',''); ?>
				<input type="submit" value="<?php print $pgv_lang["add"];?>"/>

			</td>
		</tr>
		</table>
		</form>
		<?php
	}

	// -- end new lines
	print "\r\n\t\t<br /><br />".$pgv_lang["cart_is_empty"]."<br /><br />";
}
else {
	if ($action != 'download' && $action != 'add') { ?>
		<form method="post" action="clippings.php">
		<input type="hidden" name="action" value="download" />
		<table><tr><td valign="top">
		<table>
		<tr><td colspan="2" class="topbottombar"><h2><?php print $pgv_lang["file_information"] ?></h2></td></tr>
		<tr>
		<td class="descriptionbox wrap"><?php print $pgv_lang["choose_file_type"] ?></td>
		<td class="optionbox">
		<?php print getLRM();?><input type="radio" name="filetype" checked="checked"  value="gedcom" /> GEDCOM <?php print_help_link("def_gedcom_help", "qm"); ?><?php print getLRM();?>
		<br/>
		<?php print getLRM();?><input type="radio" name="filetype" value="gramps" /> Gramps XML <?php print_help_link("def_gramps_help", "qm"); ?><?php print getLRM();?>
		</td></tr>
		</td></tr>

		<tr><td class="descriptionbox wrap"><?php print $pgv_lang["zip_files"]; ?> </td>
		<td class="optionbox"><input type="checkbox" name="Zip" value="yes" checked="checked" /><?php print_help_link("zip_help", "qm"); ?></td></tr>
		<tr><td class="descriptionbox wrap"><?php print $pgv_lang["include_media"]; ?></td>
		<td class="optionbox"> <input type="checkbox" name="IncludeMedia" value="yes" checked="checked" /><?php print_help_link("include_media_help", "qm"); ?></td></tr>

		<tr><td class="optionbox" colspan="2">
		<br/>
		<a href="javascript:;" onclick="return expand_layer('advanced');"><img id="advanced_img" src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]; ?>" border="0" width="11" height="11" alt="" title="" /> <?php print $pgv_lang["advanced_options"]; ?></a>
		<div id="advanced" style="display: none;">
		<table>
		<tr><td><input type="checkbox" name="convert" value="yes" /></td><td><?php print $pgv_lang["utf8_to_ansi"]; print_help_link("utf8_ansi_help", "qm"); ?></td></tr>
		<tr><td><input type="checkbox" name="remove" value="yes" checked="checked" /></td><td><?php print $pgv_lang["remove_custom_tags"]; print_help_link("remove_tags_help", "qm"); ?></td></tr>
		</table>
		</div>
		<br/>
		<tr><td class="topbottombar" colspan="2">
		<input type="submit" value="<?php print $pgv_lang["download_now"]; ?>" />
		<?php print_help_link("clip_download_help", "qm"); ?>
		</tr></td>
		</form>

		</td></tr>
		</table>
		<br/>

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
				<?php print $pgv_lang["add_individual_by_id"];
				 print_help_link("add_by_id_help", "qm");?>
			</td>
		</tr>
		<tr>
			<td class="optionbox">
				<input type="hidden" name="action" value="add"/>
				<input type="text" name="id" id="pid" size="5"/>
			</td>
			<td class="optionbox">
				<?php print_findindi_link('pid',''); ?>
				<?php print_findfamily_link('pid',''); ?>
				<?php print_findsource_link('pid',''); ?>
				<input type="submit" value="<?php print $pgv_lang["add"];?>"/>

			</td>
		</tr>
		</table>
		</form>


	<?php } ?>
	<br /><a href="clippings.php?action=empty"><?php print $pgv_lang["empty_cart"]."  "; ?></a>
	<?php print_help_link("empty_cart_help", "qm"); ?>
	</td><td valign="top">
	<table class="list_table">
		<tr>
			<td class="list_label"><?php echo $pgv_lang["type"]?></td>
			<td class="list_label"><?php echo $pgv_lang["id"]?></td>
			<td class="list_label"><?php echo $pgv_lang["name_description"]?></td>
			<td class="list_label"><?php echo $pgv_lang["remove"]?></td>
			<td class="list_label"><?php print_help_link("clip_cart_help", "qm"); ?></td>
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
			<tr><td class="list_value"><img src="<?php echo $PGV_IMAGE_DIR."/".$PGV_IMAGES[$icon]["small"];?>" border="0" alt="<?php echo $tag;?>" title="<?php echo $tag;?>" /></td>
			<td class="list_value ltr"><?php echo $clipping['id']?></td>
			<td class="list_value">
			<?php
			$id_ok = true;
			if(displayDetailsByID($clipping['id'],$tag)){
				if ($tag=='INDI'){
					  if ($id_ok)
					  	$dName = get_sortable_name($clipping['id']);
					  else
					  	$dName = $pgv_lang["person_private"];
				  	$names = preg_split("/,/", $dName);
					$dName = check_NN($names);
				  	print "<a href=\"individual.php?pid=".$clipping['id']."\">".PrintReady($dName)."</a>";
				}
				if ($tag=='FAM') {
					$dName = get_family_descriptor($clipping['id']);
				    $names = preg_split("/,/", $dName);
					$dName = check_NN($names);
				    print "<a href=\"family.php?famid=".$clipping['id']."\">".PrintReady($dName)."</a>";
				}
				if ($tag=='SOUR')
					print "<a href=\"source.php?sid=".$clipping['id']."\">".PrintReady(get_source_descriptor($clipping['id']))."</a>";
				if ($tag=='REPO')
					print "<a href=\"repo.php?rid=".$clipping['id']."\">".PrintReady(get_repo_descriptor($clipping['id']))."</a>";
				if ($tag=="OBJE") {
				  	print "<a href=\"mediaviewer.php?mid=".$clipping['id']."\">".PrintReady(get_media_descriptor($clipping['id']))."</a>";
				  }
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
	</td></tr></table>
<?php
}
if (isset($_SESSION["cart"])) $_SESSION["cart"]=$cart;
print_footer();
?>

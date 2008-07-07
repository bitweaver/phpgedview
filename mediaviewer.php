<?php
/**
 * Media View Page
 *
 * This page displays all information about media that is selected in PHPGedView.
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
 * @subpackage Display
 * @version $Id: mediaviewer.php,v 1.1 2008/07/07 18:01:10 lsces Exp $
 * @TODO use more theme specific CSS, allow a more fluid layout to take advantage of the page width
 */
//These files are required for this page to work
require_once("includes/controllers/media_ctrl.php");


/* Note:
 *  if $controller->getLocalFilename() is not set, then an invalid MID was passed in
 *  if $controller->pid is not set, then a filename was passed in that is not in the gedcom
 */

$filename = $controller->getLocalFilename();

	print_header($controller->getPageTitle());

global $tmb;	
	
// LBox ============================================================================= 
// Get Javascript variables from lb_config.php --------------------------- 
 if (file_exists("modules/lightbox/album.php")) {
	include('modules/lightbox/lb_config.php');
	include('modules/lightbox/functions/lb_call_js.php');	
}
// LBox  ============================================================================	

loadLangFile("lb_lang");	// Load Lightbox language file	
	
	//The following lines of code are used to print the menu box on the top right hand corner
	if ((!$controller->isPrintPreview())&&(empty($SEARCH_SPIDER))&&!empty($controller->pid)&&!empty($filename)) {
		if ($controller->userCanEdit() || $controller->canShowOtherMenu()) { ?>
			<table class="sublinks_table rtl noprint" style="margin: 10px;" cellspacing="4" cellpadding="0" align="<?php print $TEXT_DIRECTION=='ltr'?'right':'left';?>">
				<tr>
					<td class="list_label <?php echo $TEXT_DIRECTION; ?>" colspan="5"><?php print $pgv_lang["media_options"]; ?></td>
				</tr>
				<tr>
					<?php
					if ($controller->userCanEdit()) {
					?>
					<td class="sublinks_cell <?php echo $TEXT_DIRECTION;?>">
						<?php $menu = $controller->getEditMenu(); $menu->printMenu(); ?>
					</td>
					<?php }
					if ($controller->canShowOtherMenu()) {
					?>
					<td class="sublinks_cell <?php echo $TEXT_DIRECTION; ?>">
					<?php $menu = $controller->getOtherMenu(); $menu->printMenu(); ?>
					</td>
					<?php }	?>
				</tr>
			</table>
			<?php
		}
	}
	
	
		//The next set of code draws the table that displays information about the person
		?>
		<table width="70%">
			<tr>
				<td class="name_head" colspan="2">
					 <?php print PrintReady($controller->mediaobject->getTitle()); if ($SHOW_ID_NUMBERS && !empty($controller->pid)) print " " . getLRM() . "(".$controller->pid.")" . getLRM(); ?>
					 <?php print PrintReady($controller->mediaobject->getAddTitle()); ?> <br /><br />
					 <?php if ($controller->mediaobject->isMarkedDeleted()) print "<span class=\"error\">".$pgv_lang["record_marked_deleted"]."</span>"; ?>
				</td>
			</tr>
			<tr>
				<td align="center">
					<?php
					if ($controller->canDisplayDetails()) {
					//Checks to see if the File exist in the system.
					if (isFileExternal($filename) || $controller->mediaobject->fileExists()) {
						// the file is external, or it exists locally 
						// attempt to get the image size
						if ($controller->mediaobject->getWidth()) {
							// this is an image
							$imgwidth = $controller->mediaobject->getWidth()+40;
							$imgheight = $controller->mediaobject->getHeight()+150;
							if (file_exists("modules/lightbox/album.php")) {							
								$dwidth = 150;
							}else{
								$dwidth = 300;							
							}
							if ($imgwidth<$dwidth) $dwidth = $imgwidth;
							
							// If Lightbox installed, open image with Lightbox
							if ( file_exists("modules/lightbox/album.php") && ( eregi("\.jpg",$filename) || eregi("\.jpeg",$filename) || eregi("\.gif",$filename) || eregi("\.png",$filename) ) ) { 
								//			print "<a href=\"" . $media["FILE"] . "\" rel=\"clearbox[general]\" title=\"" . stripslashes(PrintReady($name1)) . "\">" . "\n";
								print "<a 
									href=\"" . $filename . "\" 
									onmouseover=\"window.status='javascript:;'; return true;\" 
									onmouseout=\"window.status=''; return true;\"
									rel=\"clearbox[general]\" title=\"" . $controller->pid . ":" . $GEDCOM . ":" . PrintReady(htmlspecialchars($controller->mediaobject->getTitle())) . "\">" . "\n";	
							}else{
								//Else open image with the Image View Page
								?>
								<a href="javascript:;" onclick="return openImage('<?php print rawurlencode($filename); ?>', <?php print $imgwidth; ?>, <?php print $imgheight; ?>);"> 
								<?php 
							} ?>
							<img src="<?php if (!$USE_THUMBS_MAIN) print $filename; else print $controller->mediaobject->getThumbnail(); ?>" border="0" <?php if (!$USE_THUMBS_MAIN) print "width=\"" . $dwidth . "\"";?> alt="<?php print $controller->mediaobject->getTitle(); ?>" title="<?php print PrintReady(htmlspecialchars($controller->mediaobject->getTitle())); ?>" />
							</a>
							<?php
						}
						else{
							// this is not an image
							?>
							<a href="<?php print $filename; ?>" target="_BLANK">
							<img src="<?php print $controller->mediaobject->getThumbnail(); ?>" border="0" width="150" alt="<?php print $controller->mediaobject->getTitle(); ?>" title="<?php print PrintReady(htmlspecialchars($controller->mediaobject->getTitle())); ?>" />
							</a>
							<?php
						}
						if ($SHOW_MEDIA_DOWNLOAD) print "<br /><br /><a href=\"".$filename."\">".$pgv_lang["download_image"]."</a><br/>";
					}
					else{
						// the file is not external and does not exist
						?>
						<img src="<?php print $controller->mediaobject->getThumbnail(); ?>" border="0" width="100" alt="<?php print $controller->mediaobject->getTitle(); ?>" title="<?php print PrintReady(htmlspecialchars($controller->mediaobject->getTitle())); ?>" />
						<br /><span class="error"><?php print $pgv_lang["file_not_found"];?></span>
						<?php
					}
					}
					?>
				</td>
				<td valign="top">
					<table width="100%">
						<tr>
							<td>
								<table class="facts_table<?php print $TEXT_DIRECTION=='ltr'?'':'_rtl';?>">
									<?php
										$facts = $controller->getFacts($SHOW_MEDIA_FILENAME);
										foreach($facts as $f=>$factrec) {
											print_fact($factrec, $controller->pid, 1, false, true);
										}
									?>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="center" colspan="2">
					<?php
					$links = get_media_relations($controller->pid);
					if (isset($links) && !empty($links)){
					?>
					 <br /><b><?php print $pgv_lang["relations_heading"]; ?></b><br /><br />
					<?php
						// PrintMediaLinks($links, "");
						require_once 'includes/functions_print_lists.php';
						print_changes_table($links);
					}	?>
				</td>
			</tr>
		</table>
<?php

// These JavaScript functions are needed for the code to work properly with the menu.
?>
<script language="JavaScript" type="text/javascript">
<!--

// javascript function to open the lightbox view
function lightboxView(){
//	var string = "<?php print $tmb; ?>";
//	alert(string);
//    	document.write(string);
//	<?php print $tmb; ?>
	return false;
}

// javascript function to open the original imageviewer.php page
function openImageView(){
	window.open("imageview.php?filename=<?php print $filename ?>", "Image View");
	return false;
}
// javascript function to open a window with the raw gedcom in it
function show_gedcom_record(shownew) {
	fromfile="";
	if (shownew=="yes") fromfile='&fromfile=1';
	var recwin = window.open("gedrecord.php?pid=<?php print $controller->pid; ?>"+fromfile, "_blank", "top=50,left=50,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
}

function showchanges() {
	window.location = 'mediaviewer.php?mid=<?php print $controller->pid; ?>&showchanges=yes';
}

function ilinkitem(mediaid, type) {
	window.open('inverselink.php?mediaid='+mediaid+'&linkto='+type+'&'+sessionname+'='+sessionid, '_blank', 'top=50,left=50,width=400,height=300,resizable=1,scrollbars=1');
	return false;
}
//-->
</script>


<br /><br /><br />
<?php
print_footer();
?>

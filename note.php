<?php
/**
* Displays the details about a shared note record.  Also shows how many people and families
* reference this shared note.
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2009 PGV Development Team.  All rights reserved.
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
* @version $Id$
*/

require './config.php';
require './includes/controllers/note_ctrl.php';
require './includes/functions/functions_print_lists.php';

$controller=new NoteController();
$controller->init();


// Tell addmedia.php what to link to
$linkToID=$controller->nid;

print_header($controller->getPageTitle());

// LightBox
if ($MULTI_MEDIA && is_dir('./modules/lightbox')) {
	include './modules/lightbox/lb_defaultconfig.php';
	if (file_exists('modules/lightbox/lb_config.php')) {
		include './modules/lightbox/lb_config.php';
	}
	include './modules/lightbox/functions/lb_call_js.php';
	loadLangFile('lightbox:lang');
}

if (!$controller->note){
	echo "<b>".$pgv_lang["unable_to_find_record"]."</b><br /><br />";
	print_footer();
	exit;
}
else if ($controller->note->isMarkedDeleted()) {
	echo '<span class="error">', $pgv_lang['record_marked_deleted'], '</span>';
}

echo PGV_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->nid, '", "_blank", "top=0,left=0,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");';
echo '}';
echo 'function showchanges() {';
echo ' window.location="note.php?nid=', $controller->nid, '&show_changes=yes"';
echo '}';
echo 'function edit_note() {';
echo ' var win04 = window.open("edit_interface.php?action=editnote&pid='.$linkToID.'", "win04", "top=70,left=70,width=620,height=500,resizable=1,scrollbars=1");';
echo ' if (window.focus) {win04.focus();}';
echo '}';
echo PGV_JS_END;

echo '<table class="list_table width80"><tr><td>';
if ($controller->accept_success) {
	echo '<b>', $pgv_lang['accept_successful'], '</b><br />';
}
echo '<span class="name_head">', PrintReady($controller->note->getFullName());
if ($SHOW_ID_NUMBERS) {
	echo ' ', getLRM(), '(', $controller->nid, ')', getLRM();
}
echo '</span><br /></td><td valign="top" class="noprint">';
if (!$controller->isPrintPreview()) {
	$editmenu=$controller->getEditMenu();
	$othermenu=$controller->getOtherMenu();
	if ($editmenu || $othermenu) {
		if (!$PGV_MENUS_AS_LISTS) {
			echo '<table class="sublinks_table" cellspacing="4" cellpadding="0">';
			echo '<tr><td class="list_label ', $TEXT_DIRECTION, '" colspan="2">', $pgv_lang['shared_note_menu'], '</td></tr>';
			echo '<tr>';
		} else { 
			echo '<div id="optionsmenu" class="sublinks_table">';
			echo '<div class="list_label ', $TEXT_DIRECTION, '">', $pgv_lang["fams_charts"], '</div>';
		} 
		if ($editmenu) {
			if (!$PGV_MENUS_AS_LISTS) {
				echo '<td class="sublinks_cell ', $TEXT_DIRECTION, '">', $editmenu->printMenu(), '</td>';
			} else { 
				echo '<ul class="sublinks_cell ', $TEXT_DIRECTION, '">', $editmenu->printMenu(), '</ul>';
			}
		}
		if ($othermenu) {
			if (!$PGV_MENUS_AS_LISTS) {
				echo '<td class="sublinks_cell ', $TEXT_DIRECTION, '">', $othermenu->printMenu(), '</td>';
			} else { 
				echo '<ul class="sublinks_cell ', $TEXT_DIRECTION, '">', $othermenu->printMenu(), '</ul>';
			}
		}
		if (!$PGV_MENUS_AS_LISTS) {
			echo '</tr></table>';
		} else { 
			echo '</div>';
		}
	}
}
echo '</td></tr><tr><td colspan="2"><table border="0" class="facts_table width100 center">';
echo '<tr class="'.$TEXT_DIRECTION.'"><td><table class="width100">';
	// Shared Note details ---------------------
	$noterec = find_gedcom_record($controller->nid);
	$nt = preg_match("/0 @$controller->nid@ NOTE(.*)/", $noterec, $n1match);
	if ($nt==1) {
		$note = print_note_record("<br />".$n1match[1], 1, $noterec, false, true);
	}else{
		$note = "No Text";
	}
	echo '<tr><td align="left" class="descriptionbox '.$TEXT_DIRECTION.'">';
		echo '<center>';
		if (!empty($PGV_IMAGES["notes"]["small"]) && $SHOW_FACT_ICONS)
			echo '<img src="'.$PGV_IMAGE_DIR."/".$PGV_IMAGES["notes"]["small"].'" alt="'.$pgv_lang["shared_note"].'" title="'.$pgv_lang["shared_note"].'" align="middle" /> ';
		echo $pgv_lang["shared_note"]."</center>";
		echo '<br /><br />';
		if (PGV_USER_CAN_EDIT) {
			echo "<a href=\"javascript: edit_note()\"> ";
			echo $pgv_lang['edit'];
			echo "</a>";
		}
		echo '</td><td class="optionbox wrap width80 '.$TEXT_DIRECTION.'">';
		echo $note;
		echo "<br />";
	echo "</td></tr>";

	$notefacts=$controller->note->getFacts();
	foreach ($notefacts as $fact) {
		if ($fact && $fact->getTag()!='CONT') {
			if ($fact->getTag()=='NOTE' ) {
				print_fact($fact);
			} else {
				print_fact($fact);
			}
		}
	}

	// Print media
	print_main_media($controller->nid);

	// new fact link
	if (!$controller->isPrintPreview() && $controller->userCanEdit()) {
		print_add_new_fact($controller->nid, $notefacts, 'NOTE');
		// new media
		echo '<tr><td class="descriptionbox '.$TEXT_DIRECTION.'">';
		print_help_link('add_media_help', 'qm', 'add_media_lbl');
		echo $pgv_lang['add_media_lbl'] . '</td>';
		echo '<td class="optionbox '.$TEXT_DIRECTION.'">';
		echo '<a href="javascript: ', $pgv_lang['add_media_lbl'], '" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=', $controller->nid, '\', \'_blank\', \'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1\'); return false;">', $pgv_lang['add_media'], '</a>';
		echo '<br />';
		echo '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid='.$controller->nid.'&linkto=note\', \'_blank\', \'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1\'); return false;">'.$pgv_lang['link_to_existing_media'].'</a>';
		echo '</td></tr>';
	}
echo '</table></td></tr>';
echo '</table><br /><br /></td></tr><tr class="center"><td colspan="2">';


// Print the tasks table
// NOT WORKING YET
/*
if (file_exists('./modules/research_assistant/research_assistant.php') && $SHOW_RESEARCH_ASSISTANT>=PGV_USER_ACCESS_LEVEL) {
	include_once './modules/research_assistant/research_assistant.php';
	$mod=new ra_functions();
	$mod->Init();
	echo $mod->getNoteTasks($controller->nid), '</td></tr><tr class="center"><td colspan="2">';
}
*/

// Individuals linked to this shared note
if ($controller->note->countLinkedIndividuals()) {
	print_indi_table($controller->note->fetchLinkedIndividuals(), $controller->note->getFullName());
}

// Families linked to this shared note
if ($controller->note->countLinkedFamilies()) {
	print_fam_table($controller->note->fetchLinkedFamilies(), $controller->note->getFullName());
}

// Media Items linked to this shared note
if ($controller->note->countLinkedMedia()) {
	print_media_table($controller->note->fetchLinkedMedia(), $controller->note->getFullName());
}

// Sources linked to this shared note
if ($controller->note->countLinkedSources()) {
	print_sour_table($controller->note->fetchLinkedSources(), $controller->note->getFullName());
}

echo '</td></tr></table>';

print_footer();
?>

<?php
/**
 * Chinese Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development
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
if (preg_match("/configure_help\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
//-- CONFIGURE FILE MESSAGES
$pgv_lang["configure"]			= "Configure PhpGedView";

//-- edit privacy messages
$pgv_lang["edit_privacy"]		= "Configuration of the privacy-file";

//-- language edit utility

//-- language edit utility
$pgv_lang["edit_langdiff"]		= "編輯語言文件的內容";
$pgv_lang["edit_lang_utility"]		= "語言文件編輯公共事業";
$pgv_lang["edit_lang_utility_help"]	= "您能使用這項公共事業編輯語言文件的內容由使用內容英國一個。<br />它將列出您原始的英語文件的內容和您選上的語言內容<br />在點擊在您選上的文件消息以後一個新窗口將打開您能改變和保存您選上的語言消息的地方。";
$pgv_lang["language_to_edit"]		= "語言編輯";
$pgv_lang["file_to_edit"]		= "語言文件類型編輯";
$pgv_lang["lang_save"]			= "之外";
$pgv_lang["contents"]			= "內容";
$pgv_lang["listing"]			= "目錄";
$pgv_lang["no_content"]			= "沒有內容";
$pgv_lang["editlang_help"]		= "編輯消息從語言文件";
$pgv_lang["cancel"]			= "取消";
$pgv_lang["savelang_help"]		= "保存被編輯的消息";
$pgv_lang["original_message"]		= "原始的消息";
$pgv_lang["message_to_edit"]		= "消息編輯";

?>
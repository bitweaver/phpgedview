<?php
/**
 * Arabic Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Ezz (sfezz)
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
 * @author Ezz (sfezz)
 * @version $Id$
 */
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

//-- CONFIG FILE MESSAGES
$pgv_lang["yes"]					= "نعم";
$pgv_lang["no"] 					= "أحد";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["birth"]					= "الوِدة:";
$pgv_lang["death"]					= "الموت:";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["male"]					= "رجولي";
$pgv_lang["female"] 				= "أنثوي";
$pgv_lang["name"]					= "اسم";
$pgv_lang["given_name"] 			= "اِسم الخاص:";
$pgv_lang["surname"]				= "إسم العائلة:";
$pgv_lang["sex"]					= "جنس";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]					= "عمر";

//-- MONTH NAMES
$pgv_lang["jan"]					= "ناير";
$pgv_lang["feb"]					= "شباط";
$pgv_lang["mar"]					= "مارس";
$pgv_lang["apr"]					= "مم";
$pgv_lang["may"]					= "مم";
$pgv_lang["jun"]					= "مم";
$pgv_lang["jul"]					= "مم";
$pgv_lang["aug"]					= "مم";
$pgv_lang["sep"]					= "مم";
$pgv_lang["oct"]					= "مم";
$pgv_lang["nov"]					= "مم";
$pgv_lang["dec"]					= "مم";
$pgv_lang["apx"]					= "زهاء تقريبا";

if (file_exists("languages/lang.ar.extra.php")) require "languages/lang.ar.extra.php";

?>
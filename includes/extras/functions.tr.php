<?php
/**
 * Turkish Date Functions that can be used by any page in PGV
 * Other functions that are specific to Turkish can be added here too
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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

/**
 * security check to prevent hackers from directly accessing this file
 */
if (strstr($_SERVER["SCRIPT_NAME"],"functions.tr.php")) {
	print "Why do you want to do that?";
	exit;
}

//-- functions to take a date and display it in Turkish.
//-- moved to lang file to reduce overall memory usage for non-turkish users
//-- provided by: KurtNorgaz
function getTurkishDate($datestr) {
	global $pgv_lang;

	$array_short = array("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec", "est");
	foreach($array_short as $indexval => $value)
	{
	  $datestr = preg_replace("/$value([^a-zA-Z])/i", $pgv_lang[$value] . "\$1", $datestr);
	}

	$array_short = array("abt", "aft", "and", "bef", "bet", "cal", "from", "int", "to", "cir");

	foreach($array_short as $indexval => $value)
	{
	  $oldDateStr = $datestr;
	  $newdatestr = preg_replace("/$value([^a-zA-Z])/i", "" . "\$1", $datestr);

	  if ($newdatestr != $datestr)
	  {
		$pos_of_value = strpos(" " . $datestr, $value);
		$datestr = $newdatestr;

		switch ($value)
		{
		  case "from"	: $datestr = trim($datestr);
					  $pos_of_to = strpos(" " . $datestr, "to");
					  $datestr_01 = trim(substr($datestr, 0, $pos_of_to - 1));
					  $datestr_02 = substr($datestr, $pos_of_to - 2);

					  if (strlen($datestr_01) > 0)
					  {
						$last_char = $datestr[strlen($datestr_01)-1];
					  }
					  else $last_char = "";
					  switch ($last_char)
					  {
						case "0" : if (strlen($datestr_01) > 1)
								   {
									 $last_two_char = substr($datestr_01,-2);
								   }
								   else $last_two_char = "";
								   switch ($last_two_char)
								   {
									 case "00" : $extension = "den"; break;
									 case "20" : $extension = "den"; break;
									 case "50" : $extension = "den"; break;
									 case "70" : $extension = "den"; break;
									 case "80" : $extension = "den"; break;
									 default   : $extension = "dan"; break;
								   }
								   break;
						case "6" : $extension = "dan"; break;
						case "9" : $extension = "dan"; break;
						default  : $extension = "den"; break;
					  }
					  $datestr_01 .= stripslashes($pgv_lang[$value]);
					  $datestr_01 = str_replace("#EXT#", $extension, $datestr_01);

					  $datestr = $datestr_01 . $datestr_02;
					  break;

		  case "to" 	: $datestr = trim($datestr);
					  if (strlen($datestr) > 0)
					  {
						$last_char = $datestr[strlen($datestr)-1];
					  }
					  else $last_char = "";
					  switch ($last_char)
					  {
						case "0" : $extension = "a"; break;
						case "9" : $extension = "a"; break;
						case "2" : $extension = "ye"; break;
						case "7" : $extension = "ye"; break;
						case "6" : $extension = "ya"; break;
						default  : $extension = "e"; break;
					  }
					  $datestr .= stripslashes($pgv_lang[$value]);
					  $datestr = str_replace("#EXT#", $extension, $datestr);
					  break;

		  case "bef"	: $datestr = trim($datestr);
					  if (strlen($datestr) > 0)
					  {
						$last_char = $datestr[strlen($datestr)-1];
					  }
					  else $last_char = "";
					  switch ($last_char)
					  {
						case "0" : if (strlen($datestr) > 1)
								   {
									 $last_two_char = substr($datestr,-2);
								   }
								   else $last_two_char = "";
								   switch ($last_two_char)
								   {
									 case "00" : $extension = "den"; break;
									 case "20" : $extension = "den"; break;
									 case "50" : $extension = "den"; break;
									 case "70" : $extension = "den"; break;
									 case "80" : $extension = "den"; break;
									 default   : $extension = "dan"; break;
								   }
								   break;
						case "6" : $extension = "dan"; break;
						case "9" : $extension = "dan"; break;
						default  : $extension = "den"; break;
					  }
					  $datestr .= stripslashes($pgv_lang[$value]);
					  $datestr = str_replace("#EXT#", $extension, $datestr);
					  break;

		  case "cir"	: $datestr .= stripslashes($pgv_lang[$value]);
					  break;

		  default		: $datestr = $oldDateStr;
					  break;
		}
	  }
	}

	return $datestr;
}

?>
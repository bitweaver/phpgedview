<?php
/**
* Date Functions that can be used by any page in PGV
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_DATE_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_date.php');

/**
* translate gedcom age string
*
* Examples:
* 4y 8m 10d.
* Chi
* INFANT
*
* @param string $agestring gedcom AGE field value
* @param bool $show_years;
* @return string age in user language
* @see http://homepages.rootsweb.com/~pmcbride/gedcom/55gcch2.htm#AGE_AT_EVENT
*/
function get_age_at_event($agestring, $show_years) {
	global $pgv_lang, $lang_short_cut, $LANGUAGE;

	// Allow special processing for different languages
	$func="age_localisation_{$lang_short_cut[$LANGUAGE]}";
	if (!function_exists($func)) {
		$func="DefaultAgeLocalisation";
	}
	// Localise the age
	$func($agestring, $show_years);

	return $agestring;
}

// Localise an age.  This is a default function, and may be overridden in includes/extras/functions.xx.php
function DefaultAgeLocalisation(&$agestring, &$show_years) {
	global $pgv_lang;

	$agestring=preg_replace(
		array(
			'/\bchi(ld)?\b/i',
			'/\binf(ant)?\b/i',
			'/\bsti(llborn)?\b/i',
			'/\b1y/i',
			'/(\d+)y/i',
			'/\b1m/i',
			'/(\d+)m/i',
			'/\b1d/i',
			'/(\d+)d/i',
			'/\b1w/i',
			'/(\d+)w/i'
		),
		array(
			$pgv_lang["child"],
			$pgv_lang["infant"],
			$pgv_lang["stillborn"],
			($show_years || preg_match('/[dm]/', $agestring)) ? '1 '.$pgv_lang["year1"] : '1',
			($show_years || preg_match('/[dm]/', $agestring)) ? '$1 '.$pgv_lang["years"] : '$1',
			'1 '.$pgv_lang["month1"],
			'$1 '.$pgv_lang["months"],
			'1 '.$pgv_lang["day1"],
			'$1 '.$pgv_lang["days"],
	  	'1 '.$pgv_lang["week1"],
			'$1 '.$pgv_lang["weeks"]
		),
		$agestring
	);
}

/*
 * Format elapsed time
 *
 * The output of this function is a string, expressed as "i years, j months, k days, l hours, m minutes",
 * ready to be inserted into a message such as "xxx ago" or "after xxx" or "in xxx".  
 *
 * The output is NOT suitable for insertion into a message such as "xxx after death" in all languages 
 * because cases of words such as "months" and "days" can vary according to context.
 *
 * Example:  
 * In English you say "after 2 months" and "2 months after death".  The German equivalents of these
 * expressions use different forms of the plural for "month": "nach 2 Monaten" and "2 Monate nach Tod"
 *
 * The input parameter $truncate determines whether the full string should be output or whether the output
 * should be truncated after "days" when the time span is 7 days or more.
 */
function formatElapsedTime($elapsedTime, $truncate=true) {
	global $pgv_lang;
	$rtn = '';

	$years = floor($elapsedTime / 31536000);		// 365 * 24 * 60 * 60 seconds per year
	if ($years > 0) {
		if ($years==1) {
			$rtn .= $pgv_lang["elapsedYear1"];
		} else {
			$pgv_lang["global_num1"] = $years;		// Make this visible to function print_text()
			// Polish requires special handling of 2,3,4 or 22,23,24 or 32,33,34 etc.
			$units = substr($years,-1,1);
			$tens = substr('0'.$years,-2,1);
			if ($tens!='1' && ($units=='2' || $units=='3' || $units=='4')) $rtn .= print_text("elapsedYear2", 0, 1);
			else $rtn .= print_text("elapsedYears", 0, 1);
		}
		$rtn .= ", ";
		$elapsedTime -= $years * 31536000;
	}


	$months = floor($elapsedTime / 2592000);		// 30 * 24 * 60 * 60 seconds per month
	if ($months > 0) {
		if ($months==1) {
			$rtn .= $pgv_lang["elapsedMonth1"];
		} else {
			$pgv_lang["global_num1"] = $months;		// Make this visible to function print_text()
			// Polish requires special handling of 2,3,4 or 22,23,24 or 32,33,34 etc.
			$units = substr($months,-1,1);
			$tens = substr('0'.$months,-2,1);
			if ($tens!='1' && ($units=='2' || $units=='3' || $units=='4')) $rtn .= print_text("elapsedMonth2", 0, 1);
			else $rtn .= print_text("elapsedMonths", 0, 1);
		}
		$rtn .= ", ";
		$elapsedTime -= $months * 2592000;
	}

	$days = floor($elapsedTime / 86400);			// 24 * 60 * 60 seconds per day
	if ($days > 0) {
		if ($days==1) {
			$rtn .= $pgv_lang["elapsedDay1"];
		} else {
			$pgv_lang["global_num1"] = $days;		// Make this visible to function print_text()
			// Polish requires special handling of 2,3,4 or 22,23,24 or 32,33,34 etc.
			$units = substr($days,-1,1);
			$tens = substr('0'.$days,-2,1);
			if ($tens!='1' && ($units=='2' || $units=='3' || $units=='4')) $rtn .= print_text("elapsedDay2", 0, 1);
			else $rtn .= print_text("elapsedDays", 0, 1);
		}
		$rtn .= ", ";
		$elapsedTime -= $days * 86400;
	}

	if (!$truncate || ($years==0 && $months==0 && $days<7)) {
		$hours = floor($elapsedTime / 3600);			// 60 * 60 seconds per hour
		if ($hours > 0) {
			if ($hours==1) {
				$rtn .= $pgv_lang["elapsedHour1"];
			} else {
				$pgv_lang["global_num1"] = $hours;		// Make this visible to function print_text()
				// Polish requires special handling of 2,3,4 or 22,23,24 or 32,33,34 etc.
				$units = substr($hours,-1,1);
				$tens = substr('0'.$hours,-2,1);
				if ($tens!='1' && ($units=='2' || $units=='3' || $units=='4')) $rtn .= print_text("elapsedHour2", 0, 1);
				else $rtn .= print_text("elapsedHours", 0, 1);
			}
			$rtn .= ", ";
			$elapsedTime -= $hours * 3600;
		}

		$mins = floor($elapsedTime / 60);				// 60 seconds per minute
		if ($mins > 0) {
			if ($mins==1) {
				$rtn .= $pgv_lang["elapsedMinute1"];
			} else {
				$pgv_lang["global_num1"] = $mins;		// Make this visible to function print_text()
				// Polish requires special handling of 2,3,4 or 22,23,24 or 32,33,34 etc.
				$units = substr($mins,-1,1);
				$tens = substr('0'.$mins,-2,1);
				if ($tens!='1' && ($units=='2' || $units=='3' || $units=='4')) $rtn .= print_text("elapsedMinute2", 0, 1);
				else $rtn .= print_text("elapsedMinutes", 0, 1);
			}
			$rtn .= ", ";
		}
	}
	if ($rtn=='') return $pgv_lang["elapsedMinute1"];
	return substr($rtn,0,-2);
}

/**
* Parse a time string into its different parts
* @param string $timestr the time as it was taken from the TIME tag
* @return array returns an array with the hour, minutes, and seconds
*/
function parse_time($timestr)
{
	$time = explode(':', $timestr.':0:0');
	$time[0] = min(((int) $time[0]), 23); // Hours: integer, 0 to 23
	$time[1] = min(((int) $time[1]), 59); // Minutes: integer, 0 to 59
	$time[2] = min(((int) $time[2]), 59); // Seconds: integer, 0 to 59
	$time["hour"] = $time[0];
	$time["minutes"] = $time[1];
	$time["seconds"] = $time[2];

	return $time;
}

////////////////////////////////////////////////////////////////////////////////
// This pair of functions converts between the internal gedcom date and the
// text that the user sees when editing a date on a form.
// They can be overridden by the presence of gedcom_to_edit_date_XX() in
// includes/extras/functions.XX.php
////////////////////////////////////////////////////////////////////////////////
function default_gedcom_to_edit_date($datestr)
{
	// Don't do too much here - it will annoy experienced PGV users.
	// Maybe just remove calendar escapes, which we will be able to automatically
	// recreate?
	return $datestr;
}
function default_edit_to_gedcom_date($datestr)
{
	global $pgv_lang;
	// The order of these keywords is significant, to avoid partial matches.  In particular:
	// ads:adr_leap_year:adr to prevent "Adar" matching "Adar Sheni" or "Adar I" matching "Adar II"
	// \b prevents the german JULI matching @#DJULIAN@, etc.

	foreach (array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec','vend','brum','frim','nivo','pluv','vent','germ','flor','prai','mess','ther','fruc','comp','tsh','csh','ksl','tvt','shv','nsn','iyr','svn','tmz','aav','ell','abt','aft','bef','bet','cal','est','from','int','to','b.c.') as $keyword) {
		$datestr=preg_replace("/\b".str_replace('.','[.]?',$pgv_lang[$keyword])."\b/i", strtoupper($keyword), $datestr);
	}

	foreach (array('ads','adr_leap_year','adr','jan_1st','feb_1st','mar_1st','apr_1st','may_1st','jun_1st','jul_1st','aug_1st','sep_1st','oct_1st','nov_1st','dec_1st') as $keyword) {
		$datestr=preg_replace("/\b".str_replace('.','[.]?',$pgv_lang[$keyword])."\b/i", strtoupper(substr($keyword,0,3)), $datestr);
	}

	foreach (array('and') as $keyword) {
		$datestr=preg_replace("/\b".str_replace('.','[.]?',$pgv_lang[$keyword])."\b/i", strtoupper($keyword), $datestr);
	}

	return $datestr;
}

////////////////////////////////////////////////////////////////////////////////
// Convert a unix timestamp into a formated date-time value, for logs, etc.
// We can't just use date("$DATE_FORMAT- $TIME_FORMAT") as this doesn't
// support internationalisation.
// Don't attempt to convert into other calendars, as not all days start at
// midnight, and we can only get it wrong.
////////////////////////////////////////////////////////////////////////////////
function format_timestamp($time) {
	global $DATE_FORMAT, $TIME_FORMAT;

	return
		PrintReady(timestamp_to_gedcom_date($time)->Display(false, $DATE_FORMAT).
		'<span class="date"> - '.date($TIME_FORMAT, $time).'</span>');
}

////////////////////////////////////////////////////////////////////////////////
// Get the current julian day on the server
////////////////////////////////////////////////////////////////////////////////
function server_jd() {
	return timestamp_to_jd(time());
}

////////////////////////////////////////////////////////////////////////////////
// Get the current julian day on the client
////////////////////////////////////////////////////////////////////////////////
function client_jd() {
	return timestamp_to_jd(client_time());
}

////////////////////////////////////////////////////////////////////////////////
// Convert a unix-style timestamp into a julian-day
////////////////////////////////////////////////////////////////////////////////
function timestamp_to_jd($time) {
	return timestamp_to_gedcom_date($time)->JD();
}

////////////////////////////////////////////////////////////////////////////////
// Convert a unix-style timestamp into a GedcomDate object
////////////////////////////////////////////////////////////////////////////////
function timestamp_to_gedcom_date($time) {
	return new GedcomDate(strtoupper(date('j M Y', $time)));
}

////////////////////////////////////////////////////////////////////////////////
// Get the current timestamp of the client, not the server
////////////////////////////////////////////////////////////////////////////////
function client_time() {
	if (isset($_SESSION["timediff"])) {
		return time()-$_SESSION["timediff"];
	} else {
		return time();
	}
}

?>

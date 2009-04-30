<?php
/**
 * Classes for Gedcom Date/Calendar functionality.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2007 to 2008  PGV Development Team.  All rights reserved.
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
 * @author Greg Roach
 * @version $Id: class_date.php,v 1.1 2009/04/30 18:30:36 lsces Exp $
 *
 * NOTE: Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 *
 * NOTE: We assume that years start on the first day of the first month.  Where
 * this is not the case (e.g. England prior to 1752), we need to use modified
 * years or the OS/NS notation "4 FEB 1750/51".
 *
 * NOTE: PGV should only be using the GedcomDate class.  The other classes
 * are all for internal use only.
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_CLASS_DATE_PHP', '');

////////////////////////////////////////////////////////////////////////////////
//
// CalendarDate is a base class for classes such as GregorianDate, etc.
//
// + All supported calendars have non-zero days/months/years.
// + We store dates as both Y/M/D and Julian Days.
// + For imprecise dates such as "JAN 2000" we store the start/end julian day.
//
////////////////////////////////////////////////////////////////////////////////
class CalendarDate {
	var $y, $m, $d;     // Numeric year/month/day
	var $minJD, $maxJD; // Julian Day numbers

	function CalendarDate($date) {
		// Construct from an integer (a julian day number)
		if (is_numeric($date)) {
			$this->minJD=$date;
			$this->maxJD=$date;
			list($this->y, $this->m, $this->d)=$this->JDtoYMD($date);
			return;
		}

		// Construct from an array (of three gedcom-style strings: "1900", "feb", "4")
		if (is_array($date)) {
			$this->d=(int)$date[2];
			if (!is_null($this->MONTH_TO_NUM($date[1]))) {
				$this->m=$this->MONTH_TO_NUM($date[1]);
			} else {
				$this->m=0;
				$this->d=0;
			}
			$this->y=$this->ExtractYear($date[0]);
			$this->SetJDfromYMD();
			return;
		}

		// Construct from an equivalent xxxxDate object
		if ($this->CALENDAR_ESCAPE()==$date->CALENDAR_ESCAPE()) {
			// NOTE - can't copy whole object - need to be able to copy Hebrew to Jewish, etc.
			$this->y=$date->y;
			$this->m=$date->m;
			$this->d=$date->d;
			$this->minJD=$date->minJD;
			$this->maxJD=$date->maxJD;
			return;
		}

		// ...else construct an inequivalent xxxxDate object
		if ($date->y==0) {
			// Incomplete date - convert on basis of anniversary in current year
			$today=$date->TodayYMD();
			$jd=$date->YMDtoJD($today[0], $date->m, $date->d==0?$today[2]:$date->d);
		} else {
			// Complete date
			$jd=floor(($date->maxJD+$date->minJD)/2);
		}
		list($this->y, $this->m, $this->d)=$this->JDtoYMD($jd);
		// New date has same precision as original date
		if ($date->y==0) $this->y=0;
		if ($date->m==0) $this->m=0;
		if ($date->d==0) $this->d=0;
		$this->SetJDfromYMD();
	}

	// Set the object's JD from a potentially incomplete YMD
	function SetJDfromYMD() {
		if ($this->y==0) {
			$this->minJD=0;
			$this->maxJD=0;
		} else
			if ($this->m==0) {
				$this->minJD=$this->YMDtoJD($this->y, 1, 1);
				$this->maxJD=$this->YMDtoJD($this->NextYear($this->y), 1, 1)-1;
			} else {
				if ($this->d==0) {
					list($ny,$nm)=$this->NextMonth();
					$this->minJD=$this->YMDtoJD($this->y, $this->m,  1);
					$this->maxJD=$this->YMDtoJD($ny, $nm, 1)-1;
				} else {
					$this->minJD=$this->YMDtoJD($this->y, $this->m, $this->d);
					$this->maxJD=$this->minJD;
				}
			}
	}

	// Calendars are defined in terms of the following static functions.
	// They should redefine them as necessary.
	static function CALENDAR_ESCAPE() {
		return '@#DUNKNOWN@';
	}
	static function NUM_MONTHS() {
		return 12;
	}
	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'jan'=>1, 'feb'=>2, 'mar'=>3, 'apr'=>4, 'may'=>5, 'jun'=>6, 'jul'=>7, 'aug'=>8, 'sep'=>9, 'oct'=>10, 'nov'=>11, 'dec'=>12);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH($n) {
		static $months=array(0=>'', 1=>'jan', 2=>'feb', 3=>'mar', 4=>'apr', 5=>'may', 6=>'jun', 7=>'jul', 8=>'aug', 9=>'sep', 10=>'oct', 11=>'nov', 12=>'dec');
		if (isset($months[$n])) {
			return $months[$n];
		} else {
			return null;
		}
	}
	static function CAL_START_JD() {
		return 0; // @#DJULIAN@ 01 JAN 4713B.C.
	}
	static function CAL_END_JD() {
		return 99999999;
	}
	static function NUM_DAYS_OF_WEEK() {
		return 7;
	}
	static function DAYS_OF_WEEK($n) {
		static $days=array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
		return $days[$n];
	}
	static function YMDtoJD($y, $m, $d) {
		return 0;
	}
	static function JDtoYMD($j) {
		return array(0, 0, 0);
	}
	// Most years are 1 more than the previous, but not always (e.g. 1BC->1AD)
	static function NextYear($y) {
		return $y+1;
	}
	// Calendars that use suffixes, etc. (e.g. 'B.C.') or OS/NS notation should redefine this.
	function ExtractYear($year) {
		return (int)$year;
	}
	// Leap years may have extra days, extra months, etc.
	function IsLeapYear() {
		return false;
	}

	// Compare two dates - helper function for sorting by date
	static function Compare($d1, $d2) {
		if ($d1->maxJD < $d2->minJD)
			return -1;
		if ($d2->minJD > $d1->maxJD)
			return 1;
		return 0;
	}

	// How long between an event and a given julian day
	// Return result as either a number of years or
	// a gedcom-style age string.
	// bool $full: true=gedcom style, false=just years
	// int $jd: date for calculation
	// TODO: JewishDate needs to redefine this to cope with leap months
	function GetAge($full, $jd) {
		if ($this->y==0 || $jd==0) {
			return '';
		}
		if ($this->minJD < $jd && $this->maxJD > $jd) {
			return '';
		}
		if ($this->minJD==$jd)
			return $full?'':'0';
		if ($jd<$this->minJD)
			return '<img alt="" src="images/warning.gif" />';
		list($y,$m,$d)=$this->JDtoYMD($jd);
		$dy=$y-$this->y;
		$dm=$m-max($this->m,1);
		$dd=$d-max($this->d,1);
		if ($dd<0) {
			$dd+=$this->DaysInMonth();
			$dm--;
		}
		if ($dm<0) {
			$dm+=$this->NUM_MONTHS();
			$dy--;
		}
		// Not a full age?  Then just the years
		if (!$full)
			return $dy;
		// Age in years?
		if ($dy>1)
			return $dy.'y';
		$dm+=$dy*$this->NUM_MONTHS();
		// Age in months?
		if ($dm>1)
			return $dm.'m';
		// Age in days?
		return ($jd-$this->minJD)."d";
	}

	// Convert a date from one calendar to another.
	function convert_to_cal($calendar) {
		global $LANGUAGE;
  	switch ($calendar) {
		case 'gregorian':
			return new GregorianDate($this);
		case 'julian':
			return new JulianDate($this);
		case 'jewish':
			if ($LANGUAGE!='hebrew')
				return new JewishDate($this);
			// no  break
		case 'hebrew':
			return new HebrewDate($this);
		case 'french':
			return new FrenchRDate($this);
		case 'arabic':
			if ($LANGUAGE!='arabic')
				return new ArabicDate($this);
			// no  break
		case 'hijri':
			return new HijriDate($this);
		default:
			return $this;
		}
	}

	// Is this date within the valid range of the calendar
	function InValidRange() {
		return $this->minJD>=$this->CAL_START_JD() && $this->maxJD<=$this->CAL_END_JD();
	}

	// How many days in the current month
	function DaysInMonth() {
		list($ny,$nm)=$this->NextMonth();
		return $this->YMDtoJD($ny, $nm, 1) - $this->YMDtoJD($this->y, $this->m, 1);
	}

	// How many days in the current week
	function DaysInWeek() {
		return $this->NUM_DAYS_OF_WEEK();
	}

	// Format a date
	// $format - format string: the codes are specified in http://php.net/date
	function Format($format) {
		// Legacy formats (DMY) become jFY
		if (preg_match('/^[DMY,. ;\/-]+$/', $format)) {
			$format=strtr($format, 'DM', 'jF');
		}
		// Don't show exact details for inexact dates
		if (!$this->d) {
			$format=str_replace(array('d', 'j', 'l', 'D', 'N', 'S', 'w', 'z'), '', $format);
		}
		if (!$this->m) {
			$format=str_replace(array('F', 'm', 'M', 'n', 't'), '', $format);
		}
		if (!$this->y) {
			$format=str_replace(array('t', 'L', 'G', 'y', 'Y'), '', $format);
		}
		// If we've trimmed the format, also trim the punctuation
		if (!$this->d || !$this->m || !$this->y) {
			$format=trim($format, ',. ;/-');
		}
		// Build up the formated date, character at a time
		$str='';
		foreach (str_split($format) as $code)
			switch ($code) {
			case 'd': $str.=$this->FormatDayZeros(); break;
			case 'j': $str.=$this->FormatDay(); break;
			case 'l': $str.=$this->FormatLongWeekday(); break;
			case 'D': $str.=$this->FormatShortWeekday(); break;
			case 'N': $str.=$this->FormatISOWeekday(); break;
			case 'S': $str.=$this->FormatOrdinalSuffix(); break;
			case 'w': $str.=$this->FormatNumericWeekday(); break;
			case 'z': $str.=$this->FormatDayOfYear(); break;
			case 'F': $str.=$this->FormatLongMonth(); break;
			case 'm': $str.=$this->FormatMonthZeros(); break;
			case 'M': $str.=$this->FormatShortMonth(); break;
			case 'n': $str.=$this->FormatMonth(); break;
			case 't': $str.=$this->DaysInMonth(); break;
			case 'L': $str.=(int)$this->IsLeapYear(); break;
			case 'Y': $str.=$this->FormatLongYear(); break;
			case 'y': $str.=$this->FormatShortYear(); break;
			// The 4 extensions might be useful for re-formatting gedcom dates.
			case '@': $str.=$this->CALENDAR_ESCAPE(); break;
			case 'A': $str.=$this->FormatGedcomDay(); break;
			case 'O': $str.=$this->FormatGedcomMonth(); break;
			case 'E': $str.=$this->FormatGedcomYear(); break;
			default:  $str.=$code; break;
			}
		return $str;
	}

	// Functions to extract bits of the date in various formats.  Individual calendars
	// will want to redefine some of these.
	function FormatDayZeros() {
		if ($this->d<10)
			return '0'.$this->d;
		else
			return $this->d;
	}

	function FormatDay() {
		return $this->d;
	}

	function FormatLongWeekday() {
		global $pgv_lang;
		$day=$this->DAYS_OF_WEEK($this->minJD % $this->NUM_DAYS_OF_WEEK());
		if (isset($pgv_lang[$day]))
			return $pgv_lang[$day];
		return $day;
	}

	function FormatShortWeekday() {
		global $pgv_lang;
		$day=$this->DAYS_OF_WEEK($this->minJD % $this->NUM_DAYS_OF_WEEK());
		if (isset($pgv_lang[$day.'_1st']))
			return $pgv_lang[$day.'_1st'];
		if (isset($pgv_lang[$day]))
			return $pgv_lang[$day];
		return $day;
	}

	function FormatISOWeekday() {
		return $this->minJD % 7 + 1;
	}

	function FormatOrdinalSuffix() {
		global $lang_short_cut, $LANGUAGE;
		$func="ordinal_suffix_{$lang_short_cut[$LANGUAGE]}";

		if (function_exists($func))
			return $func($this->d);
		else
			return '';
	}

	function FormatNumericWeekday() {
		return ($this->minJD + 1) % $NUM_DAYS_OF_WEEK();
	}

	function FormatDayOfYear() {
		return $this->minJD - $this->YMDtoJD($this->y, 1, 1);
	}

	function FormatMonth() {
		return $this->m;
	}

	function FormatMonthZeros() {
		if ($this->m > 9)
			return $this->m;
		else
			return '0'.$this->m;
	}

	function FormatLongMonth() {
		global $pgv_lang;
		$tmp=$this->NUM_TO_MONTH($this->m);
		if (isset($pgv_lang[$tmp]))
			return $pgv_lang[$tmp];
		else
			return $tmp;
	}

	function FormatShortMonth() {
		global $pgv_lang;
		$tmp=$this->NUM_TO_MONTH($this->m).'_1st';
		if (isset($pgv_lang[$tmp]))
			return $pgv_lang[$tmp];
		else
			return $this->FormatLongMonth();
	}

	// NOTE Short year is NOT a 2-digit year.  It is for calendars such as hebrew
	// which have a 3-digit form of 4-digit years.
	function FormatShortYear() {
		return $this->y;
	}

	function FormatGedcomDay() {
		if ($this->d==0)
			return '';
		else
			return sprintf('%02d', $this->d);
	}

	function FormatGedcomMonth() {
		return strtoupper($this->NUM_TO_MONTH($this->m));
	}

	function FormatGedcomYear() {
		if ($this->y==0)
			return '';
		else
			return sprintf('%04d', $this->y);
	}

	function FormatLongYear() {
		return $this->y;
	}

	// Calendars with leap-months should redefine this.
	function NextMonth() {
		return array(
			$this->m==$this->NUM_MONTHS() ? $this->NextYear($this->y) : $this->y,
			($this->m%$this->NUM_MONTHS())+1
		);
	}

	// Convert a decimal number to roman numerals
	static function NumToRoman($num) {
		static $lookup=array(1000=>'M', '900'=>'CM', '500'=>'D', 400=>'CD', 100=>'C', 90=>'XC', 50=>'L', 40=>'XL', 10=>'X', 9=>'IX', 5=>'V', 4=>'IV', 1=>'I');
  	if ($num<1) return $num;
		$roman='';
		foreach ($lookup as $key=>$value)
			while ($num>=$key) {
				$roman.=$value;
				$num-=$key;
			}
		return $roman;
	}

	// Convert a roman numeral to decimal
	static function RomanToNum($roman) {
		static $lookup=array(1000=>'M', '900'=>'CM', '500'=>'D', 400=>'CD', 100=>'C', 90=>'XC', 50=>'L', 40=>'XL', 10=>'X', 9=>'IX', 5=>'V', 4=>'IV', 1=>'I');
		$num=0;
		foreach ($lookup as $key=>$value)
			if (strpos($roman, $value)===0) {
				$num+=$key;
				$roman=substr($roman, strlen($value));
			}
		return $num;
	}

	// Get today's date in the current calendar
	function TodayYMD() {
		return $this->JDtoYMD(GregorianDate::YMDtoJD(date('Y'), date('n'), date('j')));
	}
	function Today() {
		$tmp=clone $this;
		$ymd=$tmp->TodayYMD();
		$tmp->y=$ymd[0];
		$tmp->m=$ymd[1];
		$tmp->d=$ymd[2];
		$tmp->SetJDfromYMD();
		return $tmp;
	}

	// Create a URL that links this date to the PGV calendar
	function CalendarURL($date_fmt="") {
		global $DATE_FORMAT;
		if (empty($date_fmt))
			$date_fmt=$DATE_FORMAT;
		$URL='calendar.php?cal='.$this->CALENDAR_ESCAPE();
		$action="year";
		if (strpos($date_fmt, "Y")!==false
		||  strpos($date_fmt, "y")!==false) {
			$URL.='&year='.$this->FormatGedcomYear();
		}
		if (strpos($date_fmt, "F")!==false
		||  strpos($date_fmt, "M")!==false
		||  strpos($date_fmt, "m")!==false
		||  strpos($date_fmt, "n")!==false) {
			$URL.='&month='.$this->FormatGedcomMonth();
			if ($this->m>0)
				$action="calendar";
		}
		if (strpos($date_fmt, "d")!==false
		||  strpos($date_fmt, "D")!==false
		||  strpos($date_fmt, "j")!==false) {
			$URL.='&day='.$this->FormatGedcomDay();
			if ($this->d>0)
				$action="today";
		}
		return encode_url($URL.'&action='.$action);
	}
} // class CalendarDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Gregorian calendar
////////////////////////////////////////////////////////////////////////////////
class GregorianDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DGREGORIAN@';
	}
	static function CAL_START_JD() {
		return 2299161; // 15 OCT 1582
	}

	function IsLeapYear() {
		return $this->y%4==0 && $this->y%100!=0 || $this->y%400==0;
	}

	static function YMDtoJD($y, $m, $d) {
		if ($y<0) // 0=1BC, -1=2BC, etc.
			++$y;
		$a=floor((14-$m)/12);
		$y=$y+4800-$a;
		$m=$m+12*$a-3;
		return $d+floor((153*$m+2)/5)+365*$y+floor($y/4)-floor($y/100)+floor($y/400)-32045;
	}

	static function JDtoYMD($j) {
		$a=$j+32044;
		$b=floor((4*$a+3)/146097);
		$c=$a-floor($b*146097/4);
		$d=floor((4*$c+3)/1461);
		$e=$c-floor((1461*$d)/4);
		$m=floor((5*$e+2)/153);
		$day=$e-floor((153*$m+2)/5)+1;
		$month=$m+3-12*floor($m/10);
		$year=$b*100+$d-4800+floor($m/10);
		if ($year<1) // 0=1BC, -1=2BC, etc.
			--$year;
		return array($year, $month, $day);
	}

} // class GregorianDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Julian Proleptic calendar
// (Proleptic means we extend it backwards, prior to its introduction in 46BC)
////////////////////////////////////////////////////////////////////////////////
class JulianDate extends CalendarDate {
	var $new_old_style=false;

	static function CALENDAR_ESCAPE() {
		return '@#DJULIAN@';
	}

	static function NextYear($y) {
		if ($y==-1)
			return 1;
		else
			return $y+1;
	}

	function IsLeapYear() {
		return $this->y%4==0;
	}

	static function YMDtoJD($y, $m, $d) {
		if ($y<0) // 0=1BC, -1=2BC, etc.
			++$y;
		$a=floor((14-$m)/12);
		$y=$y+4800-$a;
		$m=$m+12*$a-3;
		return $d+floor((153*$m+2)/5)+365*$y+floor($y/4)-32083;
	}

	static function JDtoYMD($j) {
		$c=$j+32082;
		$d=floor((4*$c+3)/1461);
		$e=$c-floor(1461*$d/4);
		$m=floor((5*$e+2)/153);
		$day=$e-floor((153*$m+2)/5)+1;
		$month=$m+3-12*floor($m/10);
		$year=$d-4800+floor($m/10);
		if ($year<1) // 0=1BC, -1=2BC, etc.
		--$year;
		return array($year, $month, $day);
	}

	// Process new-style/old-style years and years BC
	function ExtractYear($year) {
		if (preg_match('/^(\d\d\d\d) \/ \d{1,4}$/', $year, $match)) { // Assume the first year is correct
			$this->new_old_style=true;
			return $match[1]+1;
		} else
			if (preg_match('/^(\d+) b ?c$/', $year, $match))
				return -$match[1];
			else
				return (int)$year;
	}

	function FormatLongYear() {
		global $pgv_lang;
		if ($this->y<0)
			return (-$this->y).$pgv_lang['b.c.'];
		else
			if ($this->new_old_style) {
				return sprintf('%d/%02d', $this->y-1, $this->y % 100);
			} else
				return $this->y;
	}

	function FormatGedcomYear() {
		if ($this->y<0)
			return sprintf('%04dB.C.', -$this->y);
		else
			if ($this->new_old_style) {
				return sprintf('%04d/%02d', $this->y-1, $this->y % 100);
			} else
				return sprintf('%04d', $this->y);
	}
} // class JulianDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Jewish calendar
////////////////////////////////////////////////////////////////////////////////
class JewishDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DHEBREW@';
	}

	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'tsh'=>1, 'csh'=>2, 'ksl'=>3, 'tvt'=>4, 'shv'=>5, 'adr'=>6, 'ads'=>7, 'nsn'=>8, 'iyr'=>9, 'svn'=>10, 'tmz'=>11, 'aav'=>12, 'ell'=>13);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH($n) {
		static $months=array(0=>'', 1=>'tsh', 2=>'csh', 3=>'ksl', 4=>'tvt', 5=>'shv', 6=>'adr', 7=>'ads', 8=>'nsn', 9=>'iyr', 10=>'svn', 11=>'tmz', 12=>'aav', 13=>'ell');
		if (isset($months[$n])) {
			return $months[$n];
		} else {
			return null;
		}
	}
	static function NUM_MONTHS() {
		return 13;
	}
	static function CAL_START_JD() {
		return 347998; // 01 TSH 0001 = @#JULIAN@ 7 OCT 3761B.C.
	}

	function NextMonth() {
		if ($this->m==6 && !$this->IsLeapYear())
			return array($this->y, 8);
		else
			return array($this->y+($this->m==13?1:0), ($this->m%13)+1);
	}

	function IsLeapYear() {
		return ((7*$this->y+1)%19)<7;
	}

	// TODO implement this function locally
	static function YMDtoJD($y, $mh, $d) {
		if (function_exists('JewishToJD'))
			return JewishToJD($mh, $d, $y);
		else
			return 0;
	}

	// TODO implement this function locally
	static function JDtoYMD($j) {
		if (function_exists('JdToJewish'))
			list($m, $d, $y)=explode('/', JDToJewish($j));
		else
			list($m, $d, $y)=array(0, 0, 0);
		return array($y, $m, $d);
	}

	function FormatLongMonth() {
		global $pgv_lang;
		$mon=$this->NUM_TO_MONTH($this->m);
		if ($mon=='adr' && $this->IsLeapYear())
			$mon.='_leap_year';
		return $pgv_lang[$mon];
	}

	function FormatShortMonth() {
		return $this->FormatLongMonth();
	}
} // class JewishDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Hebrew calendar.
// NOTE - this is the same as the Jewish Calendar, but displays dates in hebrew
// rather than the local language.
////////////////////////////////////////////////////////////////////////////////
class HebrewDate extends JewishDate {
	static $HEBREW_MONTHS=array("", "תשרי", "חשוון", "כסלו", "טבת", "שבט", "אדר", "אדר ב'", "ניסן", "אייר", "סיוון", "תמוז", "אב", "אלול");
	static $HEBREW_DAYS=array("שני", "שלישי", "רביעי", "חמישי", "ששי", "שבת", "ראשון");

	const ALAFIM="אלפים";
	const GERSHAYIM="״";
	const GERSH="׳";

	function FormatDayZeros() {
		return self::NumToHebrew($this->d);
	}

	function FormatDay() {
		return self::NumToHebrew($this->d);
	}

	function FormatLongMonth() {
		$mon=$this->NUM_TO_MONTH($this->m);
		if ($mon=='adr' && $this->IsLeapYear())
			return "אדר א'";
		else
			return self::$HEBREW_MONTHS[$this->m];
	}

	function FormatLongWeekday() {
		return self::$HEBREW_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}

	function FormatShortWeekday() {
		return self::$HEBREW_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}

	function FormatShortYear() {
		return self::NumToHebrew($this->y%1000);
	}

	function FormatLongYear() {
		return self::NumToHebrew($this->y);
	}
	// Convert a decimal number to hebrew - like roman numerals, but with extra punctuation
	// and special rules.
	static function NumToHebrew($num) {
		global $DISPLAY_JEWISH_THOUSANDS;

		static $jHundreds = array("", "ק", "ר", "ש", "ת", "תק", "תר","תש", "תת", "תתק");
		static $jTens     = array("", "י", "כ", "ל", "מ", "נ", "ס", "ע", "פ", "צ");
		static $jTenEnds  = array("", "י", "ך", "ל", "ם", "ן", "ס", "ע", "ף", "ץ");
		static $tavTaz    = array("ט״ו", "ט״ז");
		static $jOnes     = array("", "א", "ב", "ג", "ד", "ה", "ו", "ז", "ח", "ט");

		$shortYear = $num %1000; //discard thousands
		//next check for all possible single Hebrew digit years
		$singleDigitYear=($shortYear < 11 || ($shortYear <100 && $shortYear % 10 == 0)  || ($shortYear <= 400 && $shortYear % 100 ==0));
		$thousands = $num / 1000; //get # thousands
		$sb = "";
		//append thousands to String
		if($num % 1000 == 0) { // in year is 5000, 4000 etc
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH;
			$sb .= " ";
			$sb .= self::ALAFIM; //add # of thousands plus word thousand (overide alafim boolean)
		} else if($DISPLAY_JEWISH_THOUSANDS) { // if alafim boolean display thousands
			$sb .= $jOnes[$thousands];
			$sb .= self::GERSH; //append thousands quote
			$sb .= " ";
		}
		$num = $num % 1000; //remove 1000s
		$hundreds = $num / 100; // # of hundreds
		$sb .= $jHundreds[$hundreds]; //add hundreds to String
		$num = $num % 100; //remove 100s
		if($num == 15) { //special case 15
			$sb .= $tavTaz[0];
		} else if($num == 16) { //special case 16
			$sb .= $tavTaz[1];
		} else {
			$tens = $num / 10;
			if($num % 10 == 0) {                                    // if evenly divisable by 10
				if($singleDigitYear == false) {
					$sb .= $jTenEnds[$tens]; // use end letters so that for example 5750 will end with an end nun
				} else {
					$sb .= $jTens[$tens]; // use standard letters so that for example 5050 will end with a regular nun
				}
			} else {
				$sb .= $jTens[$tens];
				$num = $num % 10;
				$sb .= $jOnes[$num];
			}
		}
		if ($singleDigitYear == true) {
			$sb .= self::GERSH; //append single quote
		} else { // append double quote before last digit
        	$pos1 = strlen($sb)-2;
 			$sb = substr($sb, 0, $pos1) . self::GERSHAYIM . substr($sb, $pos1);
			$sb = str_replace(self::GERSHAYIM . self::GERSHAYIM, self::GERSHAYIM, $sb); //replace double gershayim with single instance
		}
		return $sb;
	}

} // class HebrewDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the French Republican calendar
////////////////////////////////////////////////////////////////////////////////
class FrenchRDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DFRENCH R@';
	}

	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'vend'=>1, 'brum'=>2, 'frim'=>3, 'nivo'=>4, 'pluv'=>5, 'vent'=>6, 'germ'=>7, 'flor'=>8, 'prai'=>9, 'mess'=>10, 'ther'=>11, 'fruc'=>12, 'comp'=>13);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH($n) {
		static $months=array(0=>'', 1=>'vend', 2=>'brum', 3=>'frim', 4=>'nivo', 5=>'pluv', 6=>'vent', 7=>'germ', 8=>'flor', 9=>'prai', 10=>'mess', 11=>'ther', 12=>'fruc', 13=>'comp');
		if (isset($months[$n])) {
			return $months[$n];
		} else {
			return null;
		}
	}
	static function NUM_MONTHS() {
		return 13;
	}
	static function DAYS_OF_WEEK($n) {
		static $days=array('primidi', 'duodi', 'tridi', 'quartidi', 'quintidi', 'sextidi', 'septidi', 'octidi', 'nonidi', 'decidi');
		return $days[$n];
	}
	static function NUM_DAYS_OF_WEEK() {
		return 10; // A "metric" week of 10 unimaginatively named days.
	}
	static function CAL_START_JD() {
		return 2375840; // 22 SEP 1792 = 01 VEND 0001
	}
	static function CAL_END_JD() {
		return 2380687; // 31 DEC 1805 = 10 NIVO 0014
	}

	// Leap years were based on astronomical observations.  Only years 3, 7 and 11
	// were ever observed.  Moves to a gregorian-like (fixed) system were proposed
	// but never implemented.  These functions are valid over the range years 1-14.
	function IsLeapYear() {
		return $this->y%4==3;
	}

	static function YMDtoJD($y, $m, $d) {
		return 2375444+$d+$m*30+$y*365+floor($y/4);
	}

	static function JDtoYMD($j) {
		$y=floor(($j-2375109)*4/1461)-1;
		$m=floor(($j-2375475-$y*365-floor($y/4))/30)+1;
		$d=$j-2375444-$m*30-$y*365-floor($y/4);
		return array($y, $m, $d);
	}

	// Years were written using roman numerals
	function FormatLongYear() {
		return $this->NumToRoman($this->y);
	}
} // class FrenchRDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Hijri calendar.  Note that these are "theoretical" dates.
// "True" dates are based on local lunar observations, and can be a +/- one day.
////////////////////////////////////////////////////////////////////////////////
class HijriDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DHIJRI@';
	}
	static function MONTH_TO_NUM($m) {
		static $months=array(''=>0, 'muhar'=>1, 'safar'=>2, 'rabia'=>3, 'rabit'=>4, 'jumaa'=>5, 'jumat'=>6, 'rajab'=>7, 'shaab'=>8, 'ramad'=>9, 'shaww'=>10, 'dhuaq'=>11, 'dhuah'=>12);
		if (isset($months[$m])) {
			return $months[$m];
		} else {
			return null;
		}
	}
	static function NUM_TO_MONTH($n) {
		static $months=array(0=>'', 1=>'muhar', 2=>'safar', 3=>'rabia', 4=>'rabit', 5=>'jumaa', 6=>'jumat', 7=>'rajab', 8=>'shaab', 9=>'ramad', 10=>'shaww', 11=>'dhuaq', 12=>'dhuah');
		if (isset($months[$n])) {
			return $months[$n];
		} else {
			return null;
		}
	}
	static function CAL_START_JD() {
		return 1948440; // @#DHIJRI@ 1 MUHAR 0001 = @#JULIAN@ 16 JUL 0622
	}

	function IsLeapYear() {
		return ((11*$this->y+14)%30)<11;
	}

	static function YMDtoJD($y, $m, $d) {
		return $d+29*($m-1)+floor((6*$m-1)/11)+$y*354+floor((3+11*$y)/30)+1948085;
	}

	static function JDtoYMD($j) {
		$y=floor((30*($j-1948440)+10646)/10631);
		$m=floor((11*($j-$y*354-floor((3+11*$y)/30)-1948086)+330)/325);
		$d=$j-29*($m-1)-floor((6*$m-1)/11)-$y*354-floor((3+11*$y)/30)-1948085;
		return array($y, $m, $d);
	}
} // class HijriDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Arabic calendar.
// NOTE - this is the same as the Hijri Calendar, but displays dates in arabic
// rather than the local language.
////////////////////////////////////////////////////////////////////////////////
class ArabicDate extends HijriDate {
	static $ARABIC_MONTHS=array("", "محرّم", "صفر", "ربيع الأول", "ربيع الثانى", "جمادى الأول", "جمادى الثاني", "رجب", "شعبان", "رمضان", "شوّال", "ذو القعدة", "ذو الحجة");
	static $ARABIC_DAYS=array("الأثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعه", "السبت", "الأحد");

	function FormatLongMonth() {
		return self::$ARABIC_MONTHS[$this->m];
	}

	function FormatShortMonth() {
		return self::$ARABIC_MONTHS[$this->m];
	}

	function FormatLongWeekday() {
		return self::$ARABIC_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}

	function FormatShortWeekday() {
		return self::$ARABIC_DAYS[$this->minJD % $this->NUM_DAYS_OF_WEEK()];
	}
} // class ArabicDate

////////////////////////////////////////////////////////////////////////////////
// Definitions for the Roman calendar
// TODO The 5.5.1 gedcom spec mentions this calendar, but gives no details of
// how it is to be represented....  This class is just a place holder so that
// PGV won't compain if it receives one.
////////////////////////////////////////////////////////////////////////////////
class RomanDate extends CalendarDate {
	static function CALENDAR_ESCAPE() {
		return '@#DROMAN@';
	}

	function FormatGedcomYear() {
		return sprintf('%04dAUC',$this->y);
	}

	function FormatLongYear() {
		return $this->y.'AUC';
	}
} // class RomanDate

////////////////////////////////////////////////////////////////////////////////
//
// GedcomDate represents the date or date range from a gedcom DATE record.
//
////////////////////////////////////////////////////////////////////////////////
class GedcomDate {
	var $qual1=null; // Optional qualifier, such as BEF, FROM, ABT
	var $date1=null; // The first (or only) date
	var $qual2=null; // Optional qualifier, such as TO, AND
	var $date2=null; // Optional second date
	var $text =null; // Optional text, as included with an INTerpreted date

	function GedcomDate($date) {
		// Extract any explanatory text
		if (preg_match('/^(.*)( ?\(.*)$/', $date, $match)) {
			$date=$match[1];
			$this->text=$match[2];
		}
		// Ignore punctuation and normalise whitespace
		$date=preg_replace(
			array('/(\d+|@#[^@]+@)/', '/[\s;:.,-]+/', '/^ /', '/ $/'),
			array(' $1 ', ' ', '', ''),
			strtolower($date)
		);
		if (preg_match('/^(from|bet) (.+) (and|to) (.+)/', $date, $match)) {
			$this->qual1=$match[1];
			$this->date1=$this->ParseDate($match[2]);
			$this->qual2=$match[3];
			$this->date2=$this->ParseDate($match[4]);
		} elseif (preg_match('/^(from|bet|to|and|bef|aft|cal|est|int|abt) (.+)/', $date, $match)) {
			$this->qual1=$match[1];
			$this->date1=$this->ParseDate($match[2]);
		} else {
			$this->date1=$this->ParseDate($date);
		}
	}

	// Need to "deep-clone" nested objects
	function __clone() {
		$this->date1=clone $this->date1;
		if (is_object($this->date2)) {
			$this->date2=clone $this->date2;
		}
	}

	// Convert an individual gedcom date string into a CalendarDate object
	static function ParseDate($date) {
		global $LANGUAGE;
		// Calendar escape specified? - use it
		if (preg_match('/^(@#[^@]+@) ?(.*)/', $date, $match)) {
			$cal=$match[1];
			$date=$match[2];
		} else {
			$cal='';
		}
		// A date with a month: DM, M, MY or DMY
		if (preg_match('/^(\d?\d?) ?(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|tsh|csh|ksl|tvt|shv|adr|ads|nsn|iyr|svn|tmz|aav|ell|vend|brum|frim|nivo|pluv|vent|germ|flor|prai|mess|ther|fruc|comp|muhar|safar|rabi[at]|juma[at]|rajab|shaab|ramad|shaww|dhuaq|dhuah) ?((?:\d+(?: b ?c)?|\d\d\d\d \/ \d{1,4})?)$/', $date, $match)) {
			$d=$match[1];
			$m=$match[2];
			$y=$match[3];
		} else
			// A date with just a year
			if (preg_match('/^(\d+(?: b ?c)?|\d\d\d\d \/ \d{1,4})$/', $date, $match)) {
				$d='';
				$m='';
				$y=$match[1];
			} else {
				// An invalid date - do the best we can.
				$d='';
				$m='';
				$y='';
				// Look for a 3/4 digit year anywhere in the date
				if (preg_match('/\b(\d{3,4})\b/', $date, $match)) {
					$y=$match[1];
				}
				// Look for a month anywhere in the date
				if (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|tsh|csh|ksl|tvt|shv|adr|ads|nsn|iyr|svn|tmz|aav|ell|vend|brum|frim|nivo|pluv|vent|germ|flor|prai|mess|ther|fruc|comp|muhar|safar|rabi[at]|juma[at]|rajab|shaab|ramad|shaww|dhuaq|dhuah)/', $date, $match)) {
					$m=$match[1];
					// Look for a day number anywhere in the date
					if (preg_match('/\b(\d\d?)\b/', $date, $match))
						$d=$match[1];
				}
			}
		// Unambiguous dates - override calendar escape
		if (preg_match('/^(tsh|csh|ksl|tvt|shv|adr|ads|nsn|iyr|svn|tmz|aav|ell)$/', $m))
			$cal='@#dhebrew@';
		else
			if (preg_match('/^(vend|brum|frim|nivo|pluv|vent|germ|flor|prai|mess|ther|fruc|comp)$/', $m))
				$cal='@#dfrench r@';
			else
				if (preg_match('/^(muhar|safar|rabi[at]|juma[at]|rajab|shaab|ramad|shaww|dhuaq|dhuah)$/', $m))
					$cal='@#dhijri@'; // This is a PGV extension
				else
					if (preg_match('/^\d+( b ?c)|\d\d\d\d \/ \d{1,4}$/', $y))
						$cal='@#djulian@';
		// Ambiguous dates - don't override calendar escape
		if ($cal=='')
			if (preg_match('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)$/', $m))
				$cal='@#dgregorian@';
			else
				if (preg_match('/^[345]\d\d\d$/', $y)) // Year 3000-5999
					$cal='@#dhebrew@';
				else
					$cal='@#dgregorian@';
		// Now construct an object of the correct type
		switch ($cal) {
		case '@#dgregorian@':
			return new GregorianDate(array($y, $m, $d));
		case '@#djulian@':
	 		return new JulianDate(array($y, $m, $d));
		case '@#dhebrew@':
			if ($LANGUAGE=='hebrew')
	 			return new HebrewDate(array($y, $m, $d));
			else
	 			return new JewishDate(array($y, $m, $d));
		case '@#dhijri@':
			if ($LANGUAGE=='arabic')
				return new ArabicDate(array($y, $m, $d));
			else
				return new HijriDate(array($y, $m, $d));
		case '@#dfrench r@':
		 	return new FrenchRDate(array($y, $m, $d));
		case '@#droman@':
			return new RomanDate(array($y, $m, $d));
		}
	}

	// Convert a date to the prefered format and calendar(s) display.
	// Optionally make the date a URL to the calendar.
	function Display($url=false, $date_fmt=null, $cal_fmts=null) {
		global $pgv_lang, $lang_short_cut, $LANGUAGE, $TEXT_DIRECTION, $DATE_FORMAT, $CALENDAR_FORMAT;

		// EXPERIMENTAL CODE for [ 1050249 ] Privacy: year instead of complete date in public views
		// TODO If feedback is positive, create a GUI option to edit it.
		global $PUBLIC_DATE_FORMAT;
		if (!empty($PUBLIC_DATE_FORMAT) && is_null($date_fmt) && !PGV_USER_ID)
			$date_fmt=$PUBLIC_DATE_FORMAT;

		// Convert dates to given calendars and given formats
		if (!$date_fmt)
			$date_fmt=$DATE_FORMAT;
		if (is_null($cal_fmts))
			$cal_fmts=explode('_and_', $CALENDAR_FORMAT);

		// Allow special processing for different languages
		$func="date_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (!function_exists($func))
			$func="DefaultDateLocalisation";

		// Two dates with text before, between and after
		$q1=$this->qual1;
		$d1=$this->date1->Format($date_fmt);
		$q2=$this->qual2;
		if (is_null($this->date2))
			$d2='';
		else
			$d2=$this->date2->Format($date_fmt);
		$q3='';
		// Localise the date
		$func($q1, $d1, $q2, $d2, $q3);
		// Convert to other calendars, if requested
		$conv1='';
		$conv2='';
		foreach ($cal_fmts as $cal_fmt)
			if ($cal_fmt!='none')	{
				$d1conv=$this->date1->convert_to_cal($cal_fmt);
				if ($d1conv->InValidRange())
					$d1tmp=$d1conv->Format($date_fmt);
				else
					$d1tmp='';
				$q1tmp=$this->qual1;
				if (is_null($this->date2)) {
					$d2conv=null;
					$d2tmp='';
				} else {
					$d2conv=$this->date2->convert_to_cal($cal_fmt);
					if ($d2conv->InValidRange())
						$d2tmp=$d2conv->Format($date_fmt);
					else
						$d2tmp='';
				}
				$q2tmp=$this->qual2;
				$q3tmp='';
				// Localise the date
				$func($q1tmp, $d1tmp, $q2tmp, $d2tmp, $q3tmp);
				// If the date is different to the unconverted date, add it to the date string.
				if ($d1!=$d1tmp && $d1tmp!='')
					if ($url)
						if ($CALENDAR_FORMAT!="none")
							$conv1.=' <span dir="'.$TEXT_DIRECTION.'">(<a href="'.$d1conv->CalendarURL($date_fmt).'">'.$d1tmp.'</a>)</span>';
						else
							$conv1.=' <span dir="'.$TEXT_DIRECTION.'"><br /><a href="'.$d1conv->CalendarURL($date_fmt).'">'.$d1tmp.'</a></span>';
					else
						$conv1.=' <span dir="'.$TEXT_DIRECTION.'">('.$d1tmp.')</span>';
				if (!is_null($this->date2) && $d2!=$d2tmp && $d1tmp!='')
					if ($url)
						$conv2.=' <span dir="'.$TEXT_DIRECTION.'">(<a href="'.$d2conv->CalendarURL($date_fmt).'">'.$d2tmp.'</a>)</span>';
					else
						$conv2.=' <span dir="'.$TEXT_DIRECTION.'">('.$d2tmp.')</span>';
			}

		// Add URLs, if requested
		if ($url) {
			$d1='<a href="'.$this->date1->CalendarURL($date_fmt).'">'.$d1.'</a>';
			if (!is_null($this->date2))
				$d2='<a href="'.$this->date2->CalendarURL($date_fmt).'">'.$d2.'</a>';
		}

		// Return at least one printable character, for better formatting in tables.
		$tmp=trim("{$q1} {$d1}{$conv1} {$q2} {$d2}{$conv2} {$q3} {$this->text}");
		if (strip_tags($tmp)=='')
			return '&nbsp;';
		else
			return "<span class=\"date\">{$tmp}</span>";
	}

	// Get the earliest/latest date/JD from this date
	function MinDate() {
		return $this->date1;
	}
	function MaxDate() {
		if (is_null($this->date2))
			return $this->date1;
		else
			return $this->date2;
	}
	function MinJD() {
		$tmp=$this->MinDate();
		return $tmp->minJD;
	}
	function MaxJD() {
		$tmp=$this->MaxDate();
		return $tmp->maxJD;
	}
	function JD() {
		return floor(($this->MinJD()+$this->MaxJD())/2);
	}

	// Offset this date by N years, and round to the whole year
	function AddYears($n, $qual='') {
		$tmp=clone $this;
		$tmp->date1->y+=$n;
		$tmp->date1->m=0;
		$tmp->date1->d=0;
		$tmp->date1->SetJDfromYMD();
		$tmp->qual1=$qual;
		$tmp->qual2='';
		$tmp->date2=null;
		return $tmp;
	}

	// Calculate the number of full years between two events.
	// Return the result as either a number of years (for indi lists, etc.)
	static function GetAgeYears($d1, $d2=null) {
		if (!is_object($d1)) return;
		if (!is_object($d2))
			return $d1->date1->GetAge(false, client_jd());
		else
			return $d1->date1->GetAge(false, $d2->MinJD());
	}

	// Calculate the years/months/days between two events
	// Return a gedcom style age string: "1y 2m 3d" (for fact details)
	static function GetAgeGedcom($d1, $d2=null) {
		if (is_null($d2)) {
			return $d1->date1->GetAge(true, client_jd());
		} else {
			// If dates overlap, then can't calculate age.
			if (GedcomDate::Compare($d1, $d2)) {
				return $d1->date1->GetAge(true, $d2->MinJD());
			} if (GedcomDate::Compare($d1, $d2)==0 && $d1->date1->minJD==$d2->MinJD()) {
				return '0d';
			} else {
				return '';
			}
		}
	}

	// Static function to compare two dates.
	// return <0 if $a<$b
	// return >0 if $b>$a
	// return  0 if dates same/overlap/invalid
	// BEF/AFT sort as the day before/after.
	static function Compare(&$a, &$b) {
		// Incomplete dates can't be sorted
		if (!is_object($a) || !is_object($b) || !$a->isOK() || !$b->isOK())
			return 0;
		// Get min/max JD for each date.
		switch ($a->qual1) {
		case 'bef':
			$amin=$a->MinJD()-1;
			$amax=$amin;
			break;
		case 'aft':
			$amax=$a->MaxJD()+1;
			$amin=$amax;
			break;
		default:
			$amin=$a->MinJD();
			$amax=$a->MaxJD();
			break;
		}
		switch ($b->qual1) {
		case 'bef':
			$bmin=$b->MinJD()-1;
			$bmax=$bmin;
			break;
		case 'aft':
			$bmax=$b->MaxJD()+1;
			$bmin=$bmax;
			break;
		default:
			$bmin=$b->MinJD();
			$bmax=$b->MaxJD();
			break;
		}
		if ($amax<$bmin)
			return -1;
		else
			if ($amin>$bmax)
				return 1;
			else
				if ($amin<$bmin && $amax<=$bmax)
					return -1;
				else
					if ($amin>$bmin && $amax>=$bmax)
						return 1;
				else return 0;
	}

	// Check whether a gedcom date contains usable calendar date(s).
	function isOK() {
		return $this->MinJD() && $this->MaxJD();
	}

	// Calculate the gregorian year for a date.  This should NOT be used internally
	// within PGV - we should keep the code "calendar neutral" to allow support for
	// jewish/arabic users.  This is only for interfacing with external entities,
	// such as the ancestry.com search interface or the dated fact icons.
	function gregorianYear() {
		if ($this->isOK()) {
			list($y)=GregorianDate::JDtoYMD($this->JD());
			return $y;
		} else {
			return 0;
		}
	}
}

// Localise a date.  This is a default function, and may be overridden in extras.xx.php
function DefaultDateLocalisation(&$q1, &$d1, &$q2, &$d2, &$q3) {
	global $pgv_lang;

	if ($q1) {
		$q1=$pgv_lang[$q1];
	}
	if ($q2) {
		$q2=$pgv_lang[$q2];
	}
}
?>

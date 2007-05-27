<?php
/**
 * Hijri/Arabic Date Functions
 *
 * The functions in this file are used when converting dates to the Hebrew or Jewish Calendar
 * This file is only loaded if the $LANGUAGE is hebrew, or if the $CALENDAR_FORMAT is hebrew or jewish
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
 * @version $Id: functions_date_hijri.php,v 1.3 2007/05/27 14:45:33 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * Probably used to ensure floating points are rounded up and down properly
 *
 * @param float $float the number to round nicely
 * @return float a nicely rounded number?
 * @author VisualMind (visualmind@php.net)
 */
function _ardInt($float) 
{
       return ($float < -0.0000001) ? ceil($float-0.0000001) : floor($float+0.0000001);
 }

 
/**
 * Get the Hijri conversion from gregorian $d $m $y
 *
 * @param int $d	The gregorian day of month
 * @param int $m	The gregorian month number
 * @param int $y	The gregorian Year
 * @return string a string containing the required date in Hijri form (day arabic_month_as_text year).
 * @author VisualMind (visualmind@php.net).
 * @author sfezz (sfezz@users.sourceforge.net).
 */
function getHijri($d, $m, $y) 
{
	// note: see the $format string to change the form of the Hijri returned

	// manipulated by sfezz to use UTF-8 and to work nicely in phpGedView
	// Hijri dates run according to the moon cycle and loose about 11 days per year.
	// Due to the nature of the moon not being in sync with the sun, it is possible
	// for the Hijri date to loose or gain a day, which is subsequently gained or lost.
	// This means Hijri date calculations are only accurate to day +/- 1.
	
	// The days of the week are the same and start on Sunday. The translation of the dates are as
	// follows: The First, The Second, The Third, The Fourth, The Fifth, The Gathering, The Sabbath.
	// We can include the word 'day' before each of these, but it is usual to leave it out.
	
	// Hijri works on the time since the emigration of the Prophet Mohammad from Mecca to Madina
	// which occured in 622 AD.
	// see here for more: http://en.wikipedia.org/wiki/Islamic_calendar
	
	$use_span=true;

	$arDay = array("Sat"=>"السبت", 
	         "Sun"=>"الأحد", 
	         "Mon"=>"الأثنين", 
	         "Tue"=>"الثلاثاء", 
	         "Wed"=>"الأربعاء", 
	         "Thu"=>"الخميس", 
	         "Fri"=>"الجمعه");
	$ampm=array('am'=>'صباح','pm'=>'مساء'); 

	// -- commented out because the date function will not work on dates < 1970
	// list($d,$m,$y,$dayname,$monthname,$am)=explode(' ',date('d m Y D M a', $timestamp));


	if (($y>1582)||(($y==1582)&&($m>10))||(($y==1582)&&($m==10)&&($d>14))) {
		$jd = _ardInt((1461*($y+4800+ _ardInt(($m-14)/12)))/4);
		$jd += _ardInt((367*($m-2-12*( _ardInt(($m-14)/12))))/12);
		$jd -= _ardInt((3*( _ardInt(($y+4900 + _ardInt(($m-14)/12))/100)))/4);
		$jd +=$d-32075;
	} else 	{
		$jd = 367*$y- _ardInt((7*($y+5001+ _ardInt(($m-9)/7)))/4)+ _ardInt((275*$m)/9)+$d+1729777;
	}
	$l=$jd-1948440+10632;
	$n= _ardInt(($l-1)/10631);
	$l=$l-10631*$n+355;  // Correction: 355 instead of 354
	$j=( _ardInt((10985-$l)/5316))*( _ardInt((50*$l)/17719))+( _ardInt($l/5670))*( _ardInt((43*$l)/15238));
	$l=$l -( _ardInt((30-$j)/15))*( _ardInt((17719*$j)/50))-( _ardInt($j/16))*( _ardInt((15238*$j)/43))+29;
	$m=_ardInt((24*$l)/709);
	$d=$l- _ardInt((709*$m)/24);
	$y=30*$n+$j-30;		
	
	
	$hjMonth = array("محرّم", 
				"صفر", 
				"ربيع الأول", 
				"ربيع الثانى", 
				"جمادى الأول", 
				"جمادى الثاني", 
				"رجب", 
				"شعبان", 
				"رمضان", 
				"شوّال", 
				"ذو القعدة", 
				"ذو الحجة"); 
	
	$format = "F/j/Y"; // <------------- Change this to show different forms of the Hijri system
	
	$format=str_replace('j', $d, $format);
	$format=str_replace('d', str_pad($d,2,0,STR_PAD_LEFT), $format);
	//$format=str_replace('l', $arDay[$dayname], $format);
	if (isset($hjMonth[$m-1])) $format=str_replace('F', $hjMonth[$m-1], $format);
	$format=str_replace('m', str_pad($m,2,0,STR_PAD_LEFT), $format);
	$format=str_replace('n', $m, $format);
	$format=str_replace('Y', $y, $format);
	$format=str_replace('y', substr($y,2), $format);
	//$format=str_replace('a', substr($ampm[$am],0,1), $format);
	//$format=str_replace('A', $ampm[$am], $format);

	//$date = date($format, $timestamp);
	return $format;
  }


/**
 * Get the Gregorian from a Hijri date.
 *
 * @param int $d the Hijri day
 * @param int $m the Hijri month
 * @param int $y the Hijri year
 * @return string a string containing the required date in gregorian, (d-m-y)
 * @author VisualMind (visualmind@php.net)
 * @author sfezz (sfezz@users.sourceforge.net)
 */
function dateHijri2Greg($d, $m, $y) 
{
	
	$jd=_ardInt((11*$y+3)/30)+354*$y+30*$m-_ardInt(($m-1)/2)+$d+1948440-386;
	if ($jd> 2299160 ) {
		$l=$jd+68569;
		$n=_ardInt((4*$l)/146097);
		$l=$l-_ardInt((146097*$n+3)/4);
		$i=_ardInt((4000*($l+1))/1461001);
		$l=$l-_ardInt((1461*$i)/4)+31;
		$j=_ardInt((80*$l)/2447);
		$d=$l-_ardInt((2447*$j)/80);
		$l=_ardInt($j/11);
		$m=$j+2-12*$l;
		$y=100*($n-49)+$i+$l;
	} else	{
		$j=$jd+1402;
		$k=_ardInt(($j-1)/1461);
		$l=$j-1461*$k;
		$n=_ardInt(($l-1)/365)-_ardInt($l/1461);
		$i=$l-365*$n+30;
		$j=_ardInt((80*$i)/2447);
		$d=$i-_ardInt((2447*$j)/80);
		$i=_ardInt($j/11);
		$m=$j+2-12*$i;
		$y=4*$k+$n+$i-4716;
	}

	return "$d-$m-$y"; 
} 
	

 
/**
 * Get the Arabic form of a gregorian date
 *
 * @param int $d	The gregorian day of month
 * @param int $m	The gregorian month number
 * @param int $y	The gregorian Year
 * @return string a string containing the required date in Hijri form (day arabic_month_as_text year)
 * @author VisualMind (visualmind@php.net)
 * @author sfezz (sfezz@users.sourceforge.net)
 */
function getArabic($d, $m, $y) 
{

	// This is the same as the Gregorian date, although the names of things are different.

	$use_span=true;

		$arDay = array("Sat"=>"السبت", 
	         "Sun"=>"الأحد", 
	         "Mon"=>"الأثنين", 
	         "Tue"=>"الثلاثاء", 
	         "Wed"=>"الأربعاء", 
	         "Thu"=>"الخميس", 
	         "Fri"=>"الجمعه");
	$ampm=array('am'=>'صباح','pm'=>'مساء'); 

	// -- commented out because the date function will not work on dates < 1970
	// list($d,$m,$y,$dayname,$monthname,$am)=explode(' ',date('d m Y D M a', $timestamp));

	$arMonth=array("ياناير",
			"فبراير",
			"مارس",
			"ابريل",
			"مايو",
			"يونيو",
			"يوليو",
			"اغسطس",
			"سبتمبر",
			"اكتوبر",
			"نوفمبر",
			"ديسمبر");
			
	
	$format = "F/j/Y"; // <------------- Change this to show different forms of the Arabic date

	$format=str_replace('j', $d, $format);
	//$format=str_replace('l', $arDay[$dayname], $format);
	$format=str_replace('F', $arMonth[$m-1], $format);
	$format=str_replace('Y', $y, $format);
	//$format=str_replace('a', substr($ampm[$am],0,1), $format);
	//$format=str_replace('A', $ampm[$am], $format);
    
	return $format;
	//$date = date($format, $timestamp);
	//if ($use_span) return '<span dir="rtl" lang="ar-sa">'.$date.'</span>'; 
	//else return $date;
  }

?>
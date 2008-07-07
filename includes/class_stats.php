<?php
/**
 * GEDCOM Statistics Class
 *
 * This class provides a quick & easy method for accessing statistics
 * about the GEDCOM.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008 John Finlay and Others, all rights reserved
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
 * @version $Id: class_stats.php,v 1.1 2008/07/07 17:30:13 lsces Exp $
 * @author Patrick Kellum
 * @package PhpGedView
 * @subpackage Lists
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

// Helper defines
define('STATS_BIRTH', "'BIRT', 'CHR', 'BAPM'");
define('STATS_DEATH', "'DEAT', 'BURI', 'CREM'");
define('STATS_MARRIAGE', "'MARR'");

require_once 'includes/functions_print_lists.php';

// Methods not allowed to be used in a statistic
define('STATS_NOT_ALLOWED', 'stats,getAllTags,getTags');

class stats
{
	var $_gedcom;
	var $_server_url; // Absolute URL for generating external links.  e.g. in RSS feeds
	var $_compat=false;
	var $_not_allowed = false;

	function stats($gedcom, $server_url='')
	{
		$this->_not_allowed = explode(',', STATS_NOT_ALLOWED);
		$this->_setGedcom($gedcom);
		$this->_server_url=$server_url;
	}

	function _setGedcom($gedcom)
	{
		global $gGedcom;
		$this->_gedcom=$gGedcom[$gedcom];
	}

	/**
	 * Return an array of all supported tags and an example of its output.
	 */
	function getAllTags()
	{
		$examples=array();
		$methods=get_class_methods('stats');
		$c=count($methods);
		for ($i=0; $i < $c; $i++)
		{
			if($methods[$i][0] == '_' || in_array($methods[$i], $this->_not_allowed)){continue;}
			$examples[$methods[$i]]=$this->$methods[$i]();
			if (stristr($methods[$i], 'percentage') || $methods[$i]=='averageChildren') {
				$examples[$methods[$i]] .='%';
			}
			if (stristr($methods[$i], 'highlight')) {
				$examples[$methods[$i]]=str_replace(array(' align="left"', ' align="right"'), '', $examples[$methods[$i]]);
			}
		}
		ksort($examples);
		return $examples;
	}

	/**
	 * Return a string of all supported tags and an example of its output in table row form.
	 */
	function getAllTagsTable()
	{
		$examples=array();
		$methods=get_class_methods($this);
		$c=count($methods);
		for ($i=0; $i < $c; $i++)
		{
			if(in_array($methods[$i], $this->_not_allowed) || $methods[$i][0] == '_' || $methods[$i] == 'getAllTagsTable' || $methods[$i] == 'getAllTagsText'){continue;} // Include this method name to prevent bad stuff happining
			$examples[$methods[$i]]=$this->$methods[$i]();
			if (stristr($methods[$i], 'percentage') || $methods[$i]=='averageChildren') {
				$examples[$methods[$i]] .='%';
			}
			if (stristr($methods[$i], 'highlight')) {
				$examples[$methods[$i]]=str_replace(array(' align="left"', ' align="right"'), '', $examples[$methods[$i]]);
			}
		}
		$out = '';
		foreach($examples as $tag=>$v)
		{
			$out .= "\t<tr class=\"vevent\">"
				."<td class=\"list_value_wrap\" align=\"right\" valign=\"top\" style=\"padding:3px\">{$tag}</td>"
				."<td class=\"list_value_wrap\" align=\"left\" valign=\"top\">{$v}</td>"
				."</tr>\n"
			;
		}
		return $out;
	}

	/**
	 * Return a string of all supported tags in plain text.
	 */
	function getAllTagsText()
	{
		$examples=array();
		$methods=get_class_methods($this);
		$c=count($methods);
		for ($i=0; $i < $c; $i++)
		{
			if(in_array($methods[$i], $this->_not_allowed) || $methods[$i][0] == '_' || $methods[$i] == 'getAllTagsTable' || $methods[$i] == 'getAllTagsText'){continue;} // Include this method name to prevent bad stuff happining
			$examples[$methods[$i]] = $methods[$i];
		}
		$out = '';
		foreach($examples as $tag=>$v)
		{
			$out .= "{$tag}<br />\n";
		}
		return $out;
	}

	/*
	 * Get tags and their parsed results.
	 */
	function getTags($text)
	{
		global $pgv_lang, $factarray;

		$ct=preg_match_all("/#(.+)#/U", "{$text}", $match);
		$tags=$match[1];
		$new_tags=array();
		$new_values=array();
		$c=count($tags);

		static $funcs=null;
		if (!is_array($funcs)) {
			$funcs=get_class_methods($this);
		}

		/*
		 * Parse block tags.
		 */
		for ($i=0; $i < $c; $i++)
		{
			if(!array_search($tags[$i], $funcs) || $tags[$i][0] == '_' || in_array($tags[$i], $this->_not_allowed)){continue;}
			if (method_exists($this, $tags[$i])) {
				$new_tags[]="#{$tags[$i]}#";
				$new_values[]=$this->$tags[$i]();
				unset($tags[$i]);
			}
		}

		/*
		 * Parse language variables.
		 */
		foreach ($tags as $i=>$x) {
			// help link
			if (substr($x, 0, 5)=='help:') {
				$new_tags[]="#{$x}#";
				$new_values[]=print_help_link(substr($x, 5), 'qm', '', false, true);
				unset($tags[$i]);
			}
			// pgv_lang - long
			if (substr($x, 0, 5)=='lang:' && isset($pgv_lang[substr($x, 5)])) {
				$new_tags[]="#{$x}#";
				$new_values[]=print_text($pgv_lang[substr($x, 5)], 0, 2);
				unset($tags[$i]);
			}
			// pgv_lang
			else
				if (isset($pgv_lang[$x])) {
					$new_tags[]="#{$x}#";
					$new_values[]=print_text($pgv_lang[$x], 0, 2);
					unset($tags[$i]);
				}
				// factarray
				else
					if (isset($factarray[$x])) {
						$new_tags[]="#{$x}#";
						$new_values[]=$factarray[$x];
						unset($tags[$i]);
					}
					// GLOBALS
					else if (isset($GLOBALS[$x])) {
						$new_tags[]="#{$x}#";
						$new_values[]=$GLOBALS[$x];
						unset($tags[$i]);
					}
				}
		return array($new_tags, $new_values);
	}

///////////////////////////////////////////////////////////////////////////////
// GEDCOM                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function gedcomFilename(){return $this->_gedcom['gedcom'];}

	function gedcomID(){return $this->_gedcom['id'];}

	function gedcomTitle(){return $this->_gedcom['title'];}

	function _gedcomHead()
	{
		static $cache=null;
		if (is_array($cache)) {
			return $cache;
		}
		$head=find_gedcom_record('HEAD');
		$ct=preg_match("/1 SOUR (.*)/", $head, $match);
		if ($ct > 0) {
			$softrec=get_sub_record(1, '1 SOUR', $head);
			$tt=preg_match("/2 NAME (.*)/", $softrec, $tmatch);
			if ($tt > 0) {
				$title=trim($tmatch[1]);
			} else {
				$title=trim($match[1]);
			}
			if (!empty($title)) {
				$tt=preg_match("/2 VERS (.*)/", $softrec, $tmatch);
				if ($tt > 0) {
					$version=trim($tmatch[1]);
				} else {
					$version='';
				}
			} else {
				$version='';
			}
			$tt=preg_match("/1 SOUR (.*)/", $softrec, $tmatch);
			if ($tt > 0) {
				$source=trim($tmatch[1]);
			} else {
				$source=trim($match[1]);
			}
		}
		$cache=array($title, $version, $source);
		return $cache;
	}

	function gedcomCreatedSoftware()
	{
		$head=$this->_gedcomHead();
		return $head[0];
	}

	function gedcomCreatedVersion()
	{
		$head=$this->_gedcomHead();
		// fix broken version string in Family Tree Maker
		if (strstr($head[1], 'Family Tree Maker ')) {
			$p=strpos($head[1], '(') + 1;
			$p2=strpos($head[1], ')');
			$head[1]=substr($head[1], $p, ($p2 - $p));
		}
		// Fix EasyTree version
		if ($head[2]=='EasyTree') {
			$head[1]=substr($head[1], 1);
		}
		return $head[1];
	}

	function gedcomDate()
	{
		$head=find_gedcom_record('HEAD');
		if (preg_match("/1 DATE (.+)/", $head, $match)) {
			$date=new GedcomDate($match[1]);
			return $date->Display(false);
		}
		return '';
	}

	function gedcomUpdated()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT d_year, d_month, d_day FROM {$TBLPREFIX}dates WHERE d_file={$this->_gedcom['id']} AND d_fact='CHAN' ORDER BY d_julianday2 DESC, d_type", 1);
		if (isset($rows[0])) {
			$date=new GedcomDate("{$rows[0]['d_day']} {$rows[0]['d_month']} {$rows[0]['d_year']}");
			return $date->Display(false);
		}
		return $this->gedcomDate();
	}

	function gedcomHighlight()
	{
		$highlight=false;
		if(file_exists("images/gedcoms/{$this->_gedcom['gedcom']}.jpg"))
		{
			$highlight="images/gedcoms/{$this->_gedcom['gedcom']}.jpg";
		}
		elseif(file_exists("images/gedcoms/{$this->_gedcom['gedcom']}.png"))
		{
			$highlight="images/gedcoms/{$this->_gedcom['gedcom']}.png";
		}
		if(!$highlight){return '';}
		$imgsize=findImageSize($highlight);
		return "<a href=\"{$this->_server_url}index.php?ctype=gedcom&amp;ged={$this->_gedcom['gedcom']}\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" class=\"gedcom_highlight\" /></a>";
	}

	function gedcomHighlightLeft()
	{
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom['gedcom']}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom['gedcom']}.jpg";
		} else
			if (file_exists("images/gedcoms/{$this->_gedcom['gedcom']}.png")) {
				$highlight="images/gedcoms/{$this->_gedcom['gedcom']}.png";
			}
		if (!$highlight) {
			return '';
		}
		$imgsize=findImageSize($highlight);
		return "<a href=\"{$this->_server_url}index.php?ctype=gedcom&amp;ged={$this->_gedcom['gedcom']}\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"left\" class=\"gedcom_highlight\" /></a>";
	}

	function gedcomHighlightRight()
	{
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom['gedcom']}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom['gedcom']}.jpg";
		} else
			if (file_exists("images/gedcoms/{$this->_gedcom['gedcom']}.png")) {
				$highlight="images/gedcoms/{$this->_gedcom['gedcom']}.png";
			}
		if (!$highlight) {
			return '';
		}
		$imgsize=findImageSize($highlight);
		return "<a href=\"{$this->_server_url}index.php?ctype=gedcom&amp;ged={$this->_gedcom['gedcom']}\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"right\" class=\"gedcom_highlight\" /></a>";
	}

///////////////////////////////////////////////////////////////////////////////
// Totals                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _getPercentage($total, $type)
	{
		$per=null;
		switch($type)
		{
			default:
			case 'all':
				$per=round(100 * $total / ($this->totalIndividuals() + $this->totalFamilies() + $this->totalSources() + $this->totalOtherRecords()), 2);
				break;
			case 'individual':
				$per=round(100 * $total / $this->totalIndividuals(), 2);
				break;
			case 'family':
				$per=round(100 * $total / $this->totalFamilies(), 2);
				break;
			case 'source':
				$per=round(100 * $total / $this->totalSources(), 2);
				break;
			case 'other':
				$per=round(100 * $total / $this->totalOtherRecords(), 2);
				break;
		}
		return $per;
	}

	function totalIndividuals()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file=".$this->_gedcom['id']);
		return $rows[0]['tot'];
	}

	function totalIndividualsPercentage()
	{
		return $this->_getPercentage($this->totalIndividuals(), 'all', 2);
	}

	function totalFamilies()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(f_id) AS tot FROM {$TBLPREFIX}families WHERE f_file=".$this->_gedcom['id']);
		return $rows[0]['tot'];
	}

	function totalFamiliesPercentage()
	{
		return $this->_getPercentage($this->totalFamilies(), 'all', 2);
	}

	function totalSources()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(s_id) AS tot FROM {$TBLPREFIX}sources WHERE s_file=".$this->_gedcom['id']);
		return $rows[0]['tot'];
	}

	function totalSourcesPercentage()
	{
		return $this->_getPercentage($this->totalSources(), 'all', 2);
	}

	function totalOtherRecords()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(o_id) AS tot FROM {$TBLPREFIX}other WHERE o_file=".$this->_gedcom['id']);
		return $rows[0]['tot'];
	}

	function totalOtherPercentage()
	{
		return $this->_getPercentage($this->totalOtherRecords(), 'all', 2);
	}

	function totalSurnames()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(DISTINCT i_surname) AS tot FROM {$TBLPREFIX}individuals WHERE i_file=".$this->_gedcom['id']);
		return $rows[0]['tot'];
	}

	function totalEvents()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(d_gid) AS tot FROM {$TBLPREFIX}dates WHERE d_file={$this->_gedcom['id']} AND d_fact!='CHAN' AND d_gid!='HEAD'", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalEventsBirth()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT DISTINCT COUNT(d_gid) AS tot FROM {$TBLPREFIX}dates WHERE d_file={$this->_gedcom['id']} AND d_fact IN ('BIRT', 'CHR', 'BAPM')", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalEventsDeath()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT DISTINCT COUNT(d_gid) AS tot FROM {$TBLPREFIX}dates WHERE d_file={$this->_gedcom['id']} AND d_fact IN ('DEAT', 'BURI', 'CREM')", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalEventsMarriage()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(d_gid) AS tot FROM {$TBLPREFIX}dates WHERE d_file={$this->_gedcom['id']} AND d_fact='MARR'", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalEventsOther()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(d_gid) AS tot FROM {$TBLPREFIX}dates WHERE d_file={$this->_gedcom['id']} AND d_fact NOT IN ('BIRT', 'CHR', 'BAPM', 'DEAT', 'BURI', 'CREM', 'MARR', 'CHAN') AND d_gid!='HEAD'", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalSexMales()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT DISTINCT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file={$this->_gedcom['id']} AND i_gedcom LIKE '%1 SEX M%'", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}
	function totalSexMalesPercentage()
	{
		global $TBLPREFIX;
		return $this->_getPercentage($this->totalSexMales(), 'individual');
	}

	function totalSexFemales()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT DISTINCT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file={$this->_gedcom['id']} AND i_gedcom LIKE '%1 SEX F%'", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalSexFemalesPercentage()
	{
		global $TBLPREFIX;
		return $this->_getPercentage($this->totalSexFemales(), 'individual');
	}

	function totalSexUnknown()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT DISTINCT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file={$this->_gedcom['id']} AND (i_gedcom NOT LIKE '%1 SEX M%' AND i_gedcom NOT LIKE '%1 SEX F%')", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalSexUnknownPercentage()
	{
		global $TBLPREFIX;
		return $this->_getPercentage($this->totalSexUnknown(), 'individual');
	}

	function totalLiving()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file={$this->_gedcom['id']} AND i_isdead=0", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalLivingPercentage()
	{
		global $TBLPREFIX;
		return $this->_getPercentage($this->totalLiving(), 'individual');
	}

	function totalDeceased()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file={$this->_gedcom['id']} AND i_isdead=1", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalDeceasedPercentage()
	{
		global $TBLPREFIX;
		return $this->_getPercentage($this->totalDeceased(), 'individual');
	}

	function totalMortalityUnknown()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file={$this->_gedcom['id']} AND i_isdead=-1", 1);
		if(!isset($rows[0])){return '';}
		return $rows[0]['tot'];
	}

	function totalMortalityUnknownPercentage()
	{
		global $TBLPREFIX;
		return $this->_getPercentage($this->totalMortalityUnknown(), 'individual');
	}

	function totalUsers()
	{
		return get_user_count();
	}

	function totalMedia()
	{
		global $TBLPREFIX, $MULTI_MEDIA;
		if ($MULTI_MEDIA==true) {
			$rows=$this->_runSQL("SELECT COUNT(m_id) AS tot FROM {$TBLPREFIX}media WHERE m_gedfile='{$this->_gedcom['id']}'", 1);
			if(!isset($rows[0])){return '';}
			return $rows[0]['tot'];
		} else {
			return '';
		}
	}

///////////////////////////////////////////////////////////////////////////////
// Birth & Death                                                             //
///////////////////////////////////////////////////////////////////////////////

	function _mortalityQuery($type='full', $life_dir='ASC', $birth_death='BIRT')
	{
		global $TBLPREFIX, $pgv_lang, $SHOW_ID_NUMBERS, $listDir;
		if($birth_death == 'BIRT')
		{
			$query_field = STATS_BIRTH;
		}
		else
		{
			$birth_death = 'DEAT';
			$query_field = STATS_DEATH;
		}
		if($life_dir == 'ASC')
		{
			$dmod = 'MIN';
		}
		else
		{
			$dmod = 'MAX';
			$life_dir = 'DESC';
		}
		// Testing new style
		$rows=$this->_runSQL(''
			.' SELECT'
				.' d2.d_year,'
				.' d2.d_type,'
				.' d2.d_fact,'
				.' d2.d_gid'
			.' FROM'
				." {$TBLPREFIX}dates AS d2"
			.' WHERE'
				." d2.d_file={$this->_gedcom['id']} AND"
				." d2.d_fact IN ({$query_field}) AND"
				.' d2.d_julianday1=('
					.' SELECT'
						." {$dmod}(d1.d_julianday1)"
					.' FROM'
						." {$TBLPREFIX}dates AS d1"
					.' WHERE'
						." d1.d_file={$this->_gedcom['id']} AND"
						." d1.d_fact IN ({$query_field}) AND"
						.' d1.d_julianday1!=0'
				.' )'
			.' ORDER BY'
				." d_julianday1 {$life_dir}, d_type"
			.';'
		);
		if(!isset($rows[0])){return '';}
		$row=$rows[0];
		switch($type)
		{
			default:
			case 'full':
				if (displayDetailsById($row['d_gid'])) {
					$result=format_list_person($row['d_gid'], array(get_person_name($row['d_gid']), $this->_gedcom['gedcom']), false, '', 'span');
				} else {
					$result=$pgv_lang['privacy_error'];
				}
				break;
			case 'year':
				$date=new GedcomDate($row['d_type'].' '.$row['d_year']);
				$result=$date->Display(true);
				break;
			case 'name':
				$id='';
				if ($SHOW_ID_NUMBERS) {
					if ($listDir=='rtl') {
						$id="&nbsp;&nbsp;" . getRLM() . "({$row['d_gid']})" . getRLM();
					} else {
						$id="&nbsp;&nbsp;({$row['d_gid']})";
					}
				}
				$result="<a href=\"individual.php?pid={$row['d_gid']}&amp;ged={$this->_gedcom['gedcom']}\">".get_person_name($row['d_gid'])."{$id}</a>";
				break;
			case 'place':
				$result=format_fact_place(get_sub_record(1, "1 {$birth_death}", find_person_record($row['d_gid'])), true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	//
	// Birth
	//

	function firstBirth(){return $this->_mortalityQuery('full', 'ASC', 'BIRT');}
	function firstBirthYear(){return $this->_mortalityQuery('year', 'ASC', 'BIRT');}
	function firstBirthName(){return $this->_mortalityQuery('name', 'ASC', 'BIRT');}
	function firstBirthPlace(){return $this->_mortalityQuery('place', 'ASC', 'BIRT');}

	function lastBirth(){return $this->_mortalityQuery('full', 'DESC', 'BIRT');}
	function lastBirthYear(){return $this->_mortalityQuery('year', 'DESC', 'BIRT');}
	function lastBirthName(){return $this->_mortalityQuery('name', 'DESC', 'BIRT');}
	function lastBirthPlace(){return $this->_mortalityQuery('place', 'DESC', 'BIRT');}

	//
	// Death
	//

	function firstDeath(){return $this->_mortalityQuery('full', 'ASC', 'DEAT');}
	function firstDeathYear(){return $this->_mortalityQuery('year', 'ASC', 'DEAT');}
	function firstDeathName(){return $this->_mortalityQuery('name', 'ASC', 'DEAT');}
	function firstDeathPlace(){return $this->_mortalityQuery('place', 'ASC', 'DEAT');}

	function lastDeath(){return $this->_mortalityQuery('full', 'DESC', 'DEAT');}
	function lastDeathYear(){return $this->_mortalityQuery('year', 'DESC', 'DEAT');}
	function lastDeathName(){return $this->_mortalityQuery('name', 'DESC', 'DEAT');}
	function lastDeathPlace(){return $this->_mortalityQuery('place', 'DESC', 'DEAT');}

///////////////////////////////////////////////////////////////////////////////
// Lifespan                                                                  //
///////////////////////////////////////////////////////////////////////////////

	function _longlifeQuery($type='full', $sex='F')
	{
		global $TBLPREFIX, $pgv_lang, $SHOW_ID_NUMBERS, $listDir;
		$sex_search = ' 1=1';
		if($sex == 'F')
		{
			$sex_search = " i_gedcom LIKE '%1 SEX F%'";
		}
		elseif($sex == 'M')
		{
			$sex_search = " i_gedcom LIKE '%1 SEX M%'";
		}

		$rows=$this->_runSQL(''
			.' SELECT'
				.' death.d_gid AS id,'
				.' death.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_gedcom['id']} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1!=0 AND'
				.' death.d_julianday1!=0 AND'
				.$sex_search
			.' ORDER BY'
				.' age DESC'
		, 1);
		if(!isset($rows[0])){return '';}
		$row = $rows[0];
		switch($type)
		{
			default:
			case 'full':
				if (displayDetailsById($row['id'])) {
					$result=format_list_person($row['id'], array(get_person_name($row['id']), $this->_gedcom['gedcom']), false, '', 'span');
				} else {
					$result= $pgv_lang['privacy_error'];
				}
				break;
			case 'age':
				$result=floor($row['age']/365.25);
				break;
			case 'name':
				$id = '';
				if ($SHOW_ID_NUMBERS) {
					if ($listDir == 'rtl') {
						$id = "&nbsp;&nbsp;".getRLM()."({$row['id']})".getRLM();
					} else {
						$id = "&nbsp;&nbsp;({$row['id']})";
					}
				}
				$result="<a href=\"individual.php?pid={$row['id']}&amp;ged={$this->_gedcom['gedcom']}\">".get_person_name($row['id'])."{$id}</a>";
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenOldest($type='list', $sex='BOTH')
	{
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang;
		$sex_search = ' 1=1';
		if($sex == 'F')
		{
			$sex_search = " i_gedcom LIKE '%1 SEX F%'";
		}
		elseif($sex == 'M')
		{
			$sex_search = " i_gedcom LIKE '%1 SEX M%'";
		}
		$rows=$this->_runSQL(''
			.' SELECT'
				.' death.d_julianday2-birth.d_julianday1 AS age,'
				.' death.d_gid'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_gedcom['id']} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1!=0 AND'
				.' death.d_julianday1!=0 AND'
				.$sex_search
			.' ORDER BY'
				.' age DESC'
		, 10);
		if(!isset($rows[0])){return '';}
		$top10=array();
		for($c = 0; $c < 10; $c++)
		{
			if($type == 'list')
			{
				$top10[]="\t<li><a href=\"{$this->_server_url}individual.php?pid={$rows[$c]['d_gid']}&amp;ged={$this->_gedcom['gedcom']}\">".get_person_name($rows[$c]['d_gid'])."</a> [".floor($rows[$c]['age']/365.25)." {$pgv_lang['years']}]</li>\n";
			}
			else
			{
				$top10[]="<a href=\"{$this->_server_url}individual.php?pid={$rows[$c]['d_gid']}&amp;ged={$this->_gedcom['gedcom']}\">".get_person_name($rows[$c]['d_gid'])."</a> [".floor($rows[$c]['age']/365.25)." {$pgv_lang['years']}]";
			}
		}
		if($type == 'list')
		{
			$top10=join("\n", $top10);
		}
		else
		{
			$top10=join('; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		if($type == 'list')
		{
			return "<ul>\n{$top10}</ul>\n";
		}
		// Statstics are used by RSS feeds, etc., so need absolute URLs.
		return $top10;
	}

	function _averageLifespanQuery($sex='BOTH')
	{
		global $TBLPREFIX;
		$sex_search = ' 1=1';
		if($sex == 'F')
		{
			$sex_search = " i_gedcom LIKE '%1 SEX F%'";
		}
		elseif($sex == 'M')
		{
			$sex_search = " i_gedcom LIKE '%1 SEX M%'";
		}
		$rows=$this->_runSQL(''
			.' SELECT'
				.' AVG(death.d_julianday2-birth.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_gedcom['id']} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1!=0 AND'
				.' death.d_julianday1!=0 AND'
				.$sex_search
		, 1);
		if(!isset($rows[0])){return '';}
		$row=$rows[0];
		return floor($row['age']/365.25);
	}

	// Both Sexes

	function longestLife(){return $this->_longlifeQuery('full', 'BOTH');}
	function longestLifeAge(){return $this->_longlifeQuery('age', 'BOTH');}
	function longestLifeName(){return $this->_longlifeQuery('name', 'BOTH');}

	function topTenOldest(){return $this->_topTenOldest('nolist', 'BOTH');}
	function topTenOldestList(){return $this->_topTenOldest('list', 'BOTH');}

	function averageLifespan(){return $this->_averageLifespanQuery('BOTH');}

	// Female Only

	function longestLifeFemale(){return $this->_longlifeQuery('full', 'F');}
	function longestLifeFemaleAge(){return $this->_longlifeQuery('age', 'F');}
	function longestLifeFemaleNmae(){return $this->_longlifeQuery('name', 'F');}

	function topTenOldestFemale(){return $this->_topTenOldest('nolist', 'F');}
	function topTenOldestFemaleList(){return $this->_topTenOldest('list', 'F');}

	function averageLifespanFemale(){return $this->_averageLifespanQuery('F');}

	// Male Only

	function longestLifeMale(){return $this->_longlifeQuery('full', 'M');}
	function longestLifeMaleAge(){return $this->_longlifeQuery('age', 'M');}
	function longestLifeMaleNmae(){return $this->_longlifeQuery('name', 'M');}

	function topTenOldestMale(){return $this->_topTenOldest('nolist', 'M');}
	function topTenOldestMaleList(){return $this->_topTenOldest('list', 'M');}

	function averageLifespanMale(){return $this->_averageLifespanQuery('M');}

///////////////////////////////////////////////////////////////////////////////
// Events                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _eventQuery($type='full', $direction='ASC', $facts=STATS_BIRTH)
	{
		global $TBLPREFIX, $pgv_lang, $SHOW_ID_NUMBERS, $listDir;
		$eventTypes = array(
			'BIRT'=>$pgv_lang['htmlplus_block_birth'],
			'DEAT'=>$pgv_lang['htmlplus_block_death'],
			'MARR'=>$pgv_lang['htmlplus_block_marrage'],
			'ADOP'=>$pgv_lang['htmlplus_block_adoption'],
			'BURI'=>$pgv_lang['htmlplus_block_burial'],
			'CENS'=>$pgv_lang['htmlplus_block_census']
		);

		if($direction != 'ASC'){$direction = 'DESC';}
		$rows=$this->_runSQL(''
			.' SELECT'
				.' d_gid AS id,'
				.' d_year AS year,'
				.' d_fact AS fact,'
				.' d_type AS type'
			.' FROM'
				." {$TBLPREFIX}dates"
			.' WHERE'
				." d_file={$this->_gedcom['id']} AND"
				." d_gid!='HEAD' AND"
				." d_fact IN ({$facts}) AND"
				.' d_julianday1!=0'
			.' ORDER BY'
				." d_julianday1 {$direction}, d_type"
		, 1);
		if(!isset($rows[0])){return '';}
		$row=$rows[0];
		switch($type)
		{
			default:
			case 'full':
				if (displayDetailsById($row['id']))
				{
					switch($row['fact'])
					{
						default:
						case 'BIRT':
						case 'CHR':
						case 'BAPM':
						case 'DEAT':
						case 'BURI':
						case 'CREM':
						{
							$result=format_list_person($row['id'], array(get_person_name($row['id']), $this->_gedcom['gedcom']), false, '', 'span');
							break;
						}
						case 'MARR':
						{
							$result=format_list_family($row['id'], array(get_person_name($row['id']), $this->_gedcom['gedcom']), false, '', 'span');
							break;
						}
					}
				}
				else
				{
					$result=$pgv_lang['privacy_error'];
				}
				break;
			case 'year':
			{
				$date=new GedcomDate($row['type'].' '.$row['year']);
				$result=$date->Display(true);
			}
			case 'type':
				if(isset($eventTypes[$row['fact']])) {
					$result=$eventTypes[$row['fact']];
				} else {
					$result='';
				}
				break;
			case 'name':
				$id = '';
				if($SHOW_ID_NUMBERS)
				{
					if($listDir == 'rtl')
					{
						$id="&nbsp;&nbsp;" . getRLM() . "({$row['id']})" . getRLM();
					}
					else
					{
						$id="&nbsp;&nbsp;({$row['id']})";
					}
				}
				$result="<a href=\"individual.php?pid={$row['id']}&amp;ged={$this->_gedcom['gedcom']}\">".get_person_name($row['id'])."{$id}</a>";
				break;
			case 'place':
				$result=format_fact_place(get_sub_record(1, "1 {$row['fact']}", find_gedcom_record($row['id'])), true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function firstEvent(){return $this->_eventQuery('full', 'ASC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function firstEventYear(){return $this->_eventQuery('year', 'ASC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function firstEventType(){return $this->_eventQuery('type', 'ASC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function firstEventName(){return $this->_eventQuery('name', 'ASC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function firstEventPlace(){return $this->_eventQuery('place', 'ASC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}

	function lastEvent(){return $this->_eventQuery('full', 'DESC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function lastEventYear(){return $this->_eventQuery('year', 'DESC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function lastEventType(){return $this->_eventQuery('type', 'DESC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function lastEventName(){return $this->_eventQuery('name', 'DESC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}
	function lastEventPlace(){return $this->_eventQuery('place', 'DESC', STATS_BIRTH.', '.STATS_DEATH.', '.STATS_MARRIAGE.", 'ADOP'");}

///////////////////////////////////////////////////////////////////////////////
// Marriage                                                                  //
///////////////////////////////////////////////////////////////////////////////

	/*
	 * Query the database for marriage tags.
	 */
	function _marriageQuery($type='full', $age_dir='ASC', $sex='F')
	{
		global $TBLPREFIX, $pgv_lang;
		if($sex == 'F'){$sex_field = 'f_wife';}else{$sex_field = 'f_husb';}
		if($age_dir != 'ASC'){$age_dir = 'DESC';}
		$rows=$this->_runSQL(''
			.' SELECT'
				.' fam.f_id,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_gedcom['id']}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_gedcom['id']}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_gedcom['id']}"
			.' WHERE'
				.' birth.d_gid = indi.i_id AND'
				.' married.d_gid = fam.f_id AND'
				." indi.i_id = fam.{$sex_field} AND"
				." fam.f_file = {$this->_gedcom['id']} AND"
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM') AND"
				." married.d_fact = 'MARR' AND"
				.' birth.d_julianday1 != 0 AND'
				.' married.d_julianday1 != 0 AND'
				." i_gedcom LIKE '%1 SEX {$sex}%'"
			.' ORDER BY'
				." married.d_julianday2-birth.d_julianday1 {$age_dir}"
		, 1);
		if(!isset($rows[0])){return '';}
		$row=$rows[0];
		switch($type)
		{
			default:
			case 'full':
				if (displayDetailsById($row['f_id']) && displayDetailsById($row[$sex_field])) {
					$result=format_list_family($row['f_id'], array(get_person_name($row[$sex_field]), $this->_gedcom['gedcom']), false, '', 'span');
				} else {
					$result=$pgv_lang['privacy_error'];
				}
				break;;
			case 'name':
				$result="<a href=\"family.php?famid={$row['f_id']}&amp;ged={$this->_gedcom['gedcom']}\">".get_family_descriptor($row['f_id']).'</a>';
				break;
			case 'age':
				$result=floor($row['age']/365.25);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	//
	// Female only
	//

	function youngestMarriageFemale(){return $this->_marriageQuery('full', 'ASC', 'F');}
	function youngestMarriageFemaleName(){return $this->_marriageQuery('name', 'ASC', 'F');}
	function youngestMarriageFemaleAge(){return $this->_marriageQuery('age', 'ASC', 'F');}

	function oldestMarriageFemale(){return $this->_marriageQuery('full', 'DESC', 'F');}
	function oldestMarriageFemaleName(){return $this->_marriageQuery('name', 'DESC', 'F');}
	function oldestMarriageFemaleAge(){return $this->_marriageQuery('age', 'DESC', 'F');}

	//
	// Male only
	//

	function youngestMarriageMale(){return $this->_marriageQuery('full', 'ASC', 'M');}
	function youngestMarriageMaleName(){return $this->_marriageQuery('name', 'ASC', 'M');}
	function youngestMarriageMaleAge(){return $this->_marriageQuery('age', 'ASC', 'M');}

	function oldestMarriageMale(){return $this->_marriageQuery('full', 'DESC', 'M');}
	function oldestMarriageMaleName(){return $this->_marriageQuery('name', 'DESC', 'M');}
	function oldestMarriageMaleAge(){return $this->_marriageQuery('age', 'DESC', 'M');}

///////////////////////////////////////////////////////////////////////////////
// Family Size                                                               //
///////////////////////////////////////////////////////////////////////////////

	function _familyQuery($type='full')
	{
		global $TBLPREFIX, $pgv_lang;
		$rows=$this->_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_gedcom['id']}"
			.' ORDER BY'
				.' tot DESC'
		, 1);
		if(!isset($rows[0])){return '';}
		$row = $rows[0];
		switch($type)
		{
			default:
			case 'full':
				if(displayDetailsById($row['id'], 'FAM'))
				{
					$result=format_list_family($row['id'], array(get_family_descriptor($row['id']), $this->_gedcom['gedcom']), false, '', 'span');
				} else {
					$result = $pgv_lang['privacy_error'];
				}
				break;
			case 'size':
				$result=$row['tot'];
				break;
			case 'name':
				$result="<a href=\"family.php?famid={$row['id']}&amp;ged={$this->_gedcom['gedcom']}\">".get_family_descriptor($row['id']).'</a>';
				break;
		}
		// Statstics are used by RSS feeds, etc., so need absolute URLs.
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenFamilyQuery($type='list')
	{
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang;
		$rows=$this->_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_gedcom['id']}"
			.' ORDER BY'
				.' tot DESC'
		, 10);
		if(!isset($rows[0])){return '';}
		$top10 = array();
		for($c = 0; $c < 10; $c++)
		{
			if($type == 'list')
			{
				$top10[] = "\t<li><a href=\"{$this->_server_url}family.php?famid={$rows[$c]['id']}&amp;ged={$this->_gedcom['gedcom']}\">".get_family_descriptor($rows[$c]['id'])."</a> [{$rows[$c]['tot']} {$pgv_lang['children']}]</li>\n";
			}
			else
			{
				$top10[] = "<a href=\"{$this->_server_url}family.php?famid={$rows[$c]['id']}&amp;ged={$this->_gedcom['gedcom']}\">".get_family_descriptor($rows[$c]['id'])."</a> [{$rows[$c]['tot']} {$pgv_lang['children']}]";
			}
		}
		if($type == 'list')
		{
			$top10=join("\n", $top10);
		}
		else
		{
			$top10 = join('; ', $top10);
		}
		if($TEXT_DIRECTION == 'rtl')
		{
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if($type == 'list')
		{
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function largestFamily(){return $this->_familyQuery('full');}
	function largestFamilySize(){return $this->_familyQuery('size');}
	function largestFamilyName(){return $this->_familyQuery('name');}

	function topTenLargestFamily(){return $this->_topTenFamilyQuery('nolist');}
	function topTenLargestFamilyList(){return $this->_topTenFamilyQuery('list');}

	function averageChildren()
	{
		global $TBLPREFIX;
		$rows=$this->_runSQL("SELECT AVG(f_numchil) AS tot FROM {$TBLPREFIX}families WHERE f_file={$this->_gedcom['id']}", 1);
		$row=$rows[0];
		return sprintf('%.2f', $row['tot']);
	}

///////////////////////////////////////////////////////////////////////////////
// Surnames                                                                  //
///////////////////////////////////////////////////////////////////////////////

	function _commonSurnamesQuery($type='list', $show_tot=false)
	{
		global $TEXT_DIRECTION, $COMMON_NAMES_THRESHOLD;
		$surnames = get_common_surnames($COMMON_NAMES_THRESHOLD);
		if(count($surnames) > 0)
		{
			$common = array();
			foreach($surnames as $indexval=>$surname)
			{
				$tot = '';
				if($show_tot)
				{
					if($TEXT_DIRECTION == 'rtl')
					{
						$tot = " &rlm;[{$surname['match']}&rlm;]";
					}
					else
					{
						$tot = " [{$surname['match']}]";
					}
				}
				if($type == 'list')
				{
					$common[] = "\t<li><a href=\"{$this->_server_url}indilist.php?surname=".urlencode($surname['name'])."&amp;ged={$this->_gedcom['gedcom']}\">".PrintReady($surname['name'])."</a>{$tot}</li>\n";
				}
				else
				{
					$common[] = '<a href="'.$this->_server_url.'indilist.php?surname='.urlencode($surname['name'])."&amp;ged={$this->_gedcom['gedcom']}\">".PrintReady($surname['name'])."</a>{$tot}";
				}
			}
			if($type == 'list')
			{
				return "<ul>\n".join("\n", $common)."</ul>\n";
			}
			else
			{
				return join(', ', $common);
			}
		}
		return '';
	}

	function commonSurnames(){return $this->_commonSurnamesQuery('nolist', false);}
	function commonSurnamesTotals(){return $this->_commonSurnamesQuery('nolist', true);}
	function commonSurnamesList(){return $this->_commonSurnamesQuery('list', false);}
	function commonSurnamesListTotals(){return $this->_commonSurnamesQuery('list', true);}

///////////////////////////////////////////////////////////////////////////////
// Users                                                                     //
///////////////////////////////////////////////////////////////////////////////

	function _usersLoggedIn($type='nolist')
	{
		global $PGV_SESSION_TIME, $pgv_lang;
		// Log out inactive users
		foreach(get_idle_users(time() - $PGV_SESSION_TIME) as $user_id=>$user_name)
		{
			if($user_id != PGV_USER_ID)
			{
				userLogout($user_id);
			}
		}

		$content = '';

		// List active users
		$NumAnonymous = 0;
		$loggedusers = array ();
		$x = get_logged_in_users();
		foreach($x as $user_id=>$user_name)
		{
			if(PGV_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline') == 'Y')
			{
				$loggedusers[$user_id] = $user_name;
			}
			else
			{
				$NumAnonymous++;
			}
		}
		$LoginUsers = count($loggedusers);
		if(($LoginUsers == 0) and ($NumAnonymous == 0))
		{
			return $pgv_lang['no_login_users'];
		}
		$Advisory = 'anon_user';
		if($NumAnonymous > 1){$Advisory .= 's';}
		if($NumAnonymous > 0)
		{
			$pgv_lang['global_num1'] = $NumAnonymous; // Make it visible
			$content .= '<b>'.print_text($Advisory, 0, 1).'</b>';
		}
		$Advisory = 'login_user';
		if($LoginUsers > 1){$Advisory .= 's';}
		if($LoginUsers > 0)
		{
			$pgv_lang['global_num1'] = $LoginUsers; // Make it visible
			if($NumAnonymous)
			{
				if($type == 'list')
				{
					$content .= "<br /><br />\n";
				}
				else
				{
					$content .= " {$pgv_lang['and']} ";
				}
			}
			if($type == 'list')
			{
				$content .= '<b>'.print_text($Advisory, 0, 1)."</b>\n<ul>\n";
			}
			else
			{
				$content .= '<b>'.print_text($Advisory, 0, 1)."</b>: ";
			}
		}
		if(PGV_USER_ID)
		{
			foreach($loggedusers as $user_id=>$user_name)
			{
				if($type == 'list')
				{
					$content .= "\t<li>".PrintReady(getUserFullName($user_id))." - {$user_name}";
				}
				else
				{
					$content .= PrintReady(getUserFullName($user_id))." - {$user_name}";
				}
				if(PGV_USER_ID != $user_id && get_user_setting($user_id, 'contactmethod') != 'none')
				{
					if($type == 'list')
					{
						$content .= "<br /><a href=\"javascript:;\" onclick=\"return message('{$user_id}');\">{$pgv_lang['message']}</a>";
					}
					else
					{
						$content .= " <a href=\"javascript:;\" onclick=\"return message('{$user_id}');\">{$pgv_lang['message']}</a>";
					}
				}
				if($type == 'list')
				{
					$content .= "</li>\n";
				}
			}
		}
		if($type == 'list')
		{
			$content .= '</ul>';
		}
		return $content;
	}

	function _usersLoggedInTotal($type='all')
	{
		global $PGV_SESSION_TIME;

		foreach(get_idle_users(time() - $PGV_SESSION_TIME) as $user_id=>$user_name)
		{
			if($user_id != PGV_USER_ID){userLogout($user_id);}
		}

		$anon = 0;
		$visible = 0;
		$x = get_logged_in_users();
		foreach($x as $user_id=>$user_name)
		{
			if(PGV_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline') == 'Y'){$visible++;}else{$anon++;}
		}
		if($type == 'anon'){return $anon;}
		elseif($type == 'visible'){return $visible;}
		else{return $visible + $anon;}
	}

	function usersLoggedIn(){return $this->_usersLoggedIn('nolist');}
	function usersLoggedInList(){return $this->_usersLoggedIn('list');}

	function usersLoggedInTotal(){return $this->_usersLoggedInTotal('all');}
	function usersLoggedInTotalAnon(){return $this->_usersLoggedInTotal('anon');}
	function usersLoggedInTotalVisible(){return $this->_usersLoggedInTotal('visible');}

///////////////////////////////////////////////////////////////////////////////
// Contact                                                                   //
///////////////////////////////////////////////////////////////////////////////

	function contactWebmaster(){return user_contact_link($GLOBALS['WEBMASTER_EMAIL'], $GLOBALS['SUPPORT_METHOD']);}
	function contactGedcom(){return user_contact_link($GLOBALS['CONTACT_EMAIL'], $GLOBALS['CONTACT_METHOD']);}

///////////////////////////////////////////////////////////////////////////////
// Date & Time                                                               //
///////////////////////////////////////////////////////////////////////////////

	function serverDate() {$today=new GedcomDate(date('j M Y')); return $today->Display(false);}

	function serverTime() {return date('g:i a');}

	function serverTime24() {return date('G:i');}

	function serverTimezone() {return date('T');}

	function browserDate() {$today=new GedcomDate(date('j M Y'), client_time()); return $today->Display(false);}

	function browserTime() {return date('g:i a', client_time());}

	function browserTime24() {return date('G:i', client_time());}

	function browserTimezone() {return date('T', client_time());}

///////////////////////////////////////////////////////////////////////////////
// Tools                                                                     //
///////////////////////////////////////////////////////////////////////////////

	/*
	 * Leave for backwards compatability? Anybody using this?
	 */
	function _getEventType($type)
	{
		global $pgv_lang;
		$eventTypes=array(
			'BIRT'=>$pgv_lang['htmlplus_block_birth'],
			'DEAT'=>$pgv_lang['htmlplus_block_death'],
			'MARR'=>$pgv_lang['htmlplus_block_marrage'],
			'ADOP'=>$pgv_lang['htmlplus_block_adoption'],
			'BURI'=>$pgv_lang['htmlplus_block_burial'],
			'CENS'=>$pgv_lang['htmlplus_block_census']
		);
		if (isset($eventTypes[$type])) {
			return $eventTypes[$type];
		}
		return false;
	}

	function _runSQL($sql, $count=0)
	{
		global $DBTYPE;
		static $cache = array();
		$id = md5($sql)."_{$count}";
		if(isset($cache[$id]))
		{
			return $cache[$id];
		}
		// If we alter the SQL for a specific database for LIMIT reasons, clear the $count so we don't alter it more later
		switch($DBTYPE)
		{
			case 'mssql':
			case 'sybase':
			{
				if($count > 0)
				{
					$sql = preg_replace('/^([\s(])*SELECT/i', "SELECT TOP {$count}", $sql);
					$count = 0;
				}
				break;
			}
		}
		$rows = array();
		$tempsql = dbquery($sql, true, $count);
		if(!DB::isError($tempsql))
		{
			$res=& $tempsql;
			while($row =& $res->fetchRow(DB_FETCHMODE_ASSOC))
			{
				$rows[] = $row;
			}
			$res->free();
			$cache[$id] = $rows;
			return $rows;
		}
		return false;
	}
}

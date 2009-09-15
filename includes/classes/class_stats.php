<?php
/**
* GEDCOM Statistics Class
*
* This class provides a quick & easy method for accessing statistics
* about the GEDCOM.
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
* @version $Id: class_stats.php,v 1.3 2009/09/15 20:06:00 lsces Exp $
* @author Patrick Kellum
* @package PhpGedView
* @subpackage Lists
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_CLASS_STATS_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_print_lists.php');

// Methods not allowed to be used in a statistic
define('STATS_NOT_ALLOWED', 'stats,getAllTags,getTags');

class stats {
	var $_gedcom;
	var $_gedcom_url;
	var $_ged_id;
	var $_server_url; // Absolute URL for generating external links.  e.g. in RSS feeds
	static $_not_allowed = false;
	static $_media_types = array('audio', 'book', 'card', 'certificate', 'coat', 'document', 'electronic', 'magazine', 'manuscript', 'map', 'fiche', 'film', 'newspaper', 'painting', 'photo', 'tombstone', 'video', 'other');

	static $_xencoding = PGV_GOOGLE_CHART_ENCODING;

	function stats($gedcom, $server_url='') {
		self::$_not_allowed = explode(',', STATS_NOT_ALLOWED);
		$this->_setGedcom($gedcom);
		$this->_server_url = $server_url;
	}

	function _setGedcom($gedcom) {
		$this->_gedcom = $gedcom;
		$this->_ged_id = PrintReady(get_id_from_gedcom($gedcom));
		$this->_gedcom_url = encode_url($gedcom);
	}

	/**
	* Return an array of all supported tags and an example of its output.
	*/
	function getAllTags() {
		$examples = array();
		$methods = get_class_methods('stats');
		$c = count($methods);
		for ($i=0; $i < $c; $i++) {
			if ($methods[$i][0] == '_' || in_array($methods[$i], self::$_not_allowed)) {
				continue;
			}
			$examples[$methods[$i]] = $this->$methods[$i]();
			if (stristr($methods[$i], 'percentage')) {
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
	function getAllTagsTable() {
		global $TEXT_DIRECTION;
		$examples = array();
		$methods = get_class_methods($this);
		$c = count($methods);
		for ($i=0; $i < $c; $i++) {
			if (in_array($methods[$i], self::$_not_allowed) || $methods[$i][0] == '_' || $methods[$i] == 'getAllTagsTable' || $methods[$i] == 'getAllTagsText') {
				continue;
			} // Include this method name to prevent bad stuff happening
			$examples[$methods[$i]] = $this->$methods[$i]();
			if (stristr($methods[$i], 'percentage')) {
				$examples[$methods[$i]] .='%';
			}
			if (stristr($methods[$i], 'highlight')) {
				$examples[$methods[$i]]=str_replace(array(' align="left"', ' align="right"'), '', $examples[$methods[$i]]);
			}
		}
		$out = '';
		if ($TEXT_DIRECTION=='ltr') {
			$alignVar = 'right';
			$alignRes = 'left';
		} else {
			$alignVar = 'left';
			$alignRes = 'right';
		}
		foreach ($examples as $tag=>$v) {
			$out .= "\t<tr class=\"vevent\">"
				."<td class=\"list_value_wrap\" align=\"{$alignVar}\" valign=\"top\" style=\"padding:3px\">{$tag}</td>"
				."<td class=\"list_value_wrap\" align=\"{$alignRes}\" valign=\"top\">{$v}</td>"
				."</tr>\n"
			;
		}
		return $out;
	}

	/**
	* Return a string of all supported tags in plain text.
	*/
	function getAllTagsText() {
		$examples=array();
		$methods=get_class_methods($this);
		$c=count($methods);
		for ($i=0; $i < $c; $i++) {
			if (in_array($methods[$i], self::$_not_allowed) || $methods[$i][0] == '_' || $methods[$i] == 'getAllTagsTable' || $methods[$i] == 'getAllTagsText') {continue;} // Include this method name to prevent bad stuff happining
			$examples[$methods[$i]] = $methods[$i];
		}
		$out = '';
		foreach ($examples as $tag=>$v) {
			$out .= "{$tag}<br />\n";
		}
		return $out;
	}

	/*
	* Get tags and their parsed results.
	*/
	function getTags($text) {
		global $pgv_lang, $factarray;
		static $funcs;

		// Retrive all class methods
		isset($funcs) or $funcs = get_class_methods($this);

		// Extract all tags from the provided text
		$ct = preg_match_all("/#(.+)#/U", (string)$text, $match);
		$tags = $match[1];
		$c = count($tags);
		$new_tags = array(); // tag to replace
		$new_values = array(); // value to replace it with

		/*
		* Parse block tags.
		*/
		for($i=0; $i < $c; $i++)
		{
			$full_tag = $tags[$i];
			// Added for new parameter support
			$params = explode(':', $tags[$i]);
			if (count($params) > 1) {
				$tags[$i] = array_shift($params);
			} else {
				$params = null;
			}

			// Skip non-tags and non-allowed tags
			if ($tags[$i][0] == '_' || in_array($tags[$i], self::$_not_allowed)) {continue;}

			// Generate the replacement value for the tag
			if (method_exists($this, $tags[$i]))
			{
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = $this->$tags[$i]($params);
			}
			elseif ($tags[$i] == 'help')
			{
				// re-merge, just in case
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = print_help_link(join(':', $params), 'qm', '', false, true);
			}
			/*
			* Parse language variables.
			*/
			// pgv_lang - long
			elseif ($tags[$i] == 'lang')
			{
				// re-merge, just in case
				$params = join(':', $params);
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = print_text($pgv_lang[$params], 0, 2);
			}
			// pgv_lang
			elseif (isset($pgv_lang[$tags[$i]]))
			{
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = print_text($pgv_lang[$tags[$i]], 0, 2);
			}
			// factarray
			elseif (isset($factarray[$tags[$i]]))
			{
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = $factarray[$tags[$i]];
			}
			// GLOBALS
			elseif (isset($GLOBALS[$tags[$i]]))
			{
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = $GLOBALS[$tags[$i]];
			}
			// CONSTANTS
			elseif (substr($tags[$i], 0, 4) == 'PGV_' & defined($tags[$i]))
			{
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = constant($tags[$i]);
			}
			// OLD GLOBALS THAT ARE NOW CONSTANTS
			elseif (defined("PGV_{$tags[$i]}"))
			{
				$new_tags[] = "#PGV_{$tags[$i]}#";
				$new_values[] = constant("PGV_{$tags[$i]}");
			}
		}
		unset($tags);
		return array($new_tags, $new_values);
	}

///////////////////////////////////////////////////////////////////////////////
// GEDCOM                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function gedcomFilename() {return get_gedcom_from_id($this->_ged_id);}

	function gedcomID() {return $this->_ged_id;}

	function gedcomTitle() {return PrintReady(get_gedcom_setting($this->_ged_id, 'title'));}

	static function _gedcomHead() {
		$title = "";
		$version = '';
		$source = '';
		static $cache=null;
		if (is_array($cache)) {
			return $cache;
		}
		$head=find_other_record('HEAD');
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

	static function gedcomCreatedSoftware() {
		$head=self::_gedcomHead();
		return $head[0];
	}

	static function gedcomCreatedVersion() {
		$head=self::_gedcomHead();
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

	static function gedcomDate() {
		global $DATE_FORMAT;

		$head=find_other_record('HEAD');
		if (preg_match("/1 DATE (.+)/", $head, $match)) {
			$date=new GedcomDate($match[1]);
			return $date->Display(false, $DATE_FORMAT); // Override $PUBLIC_DATE_FORMAT
		}
		return '';
	}

	function gedcomUpdated() {
		global $TBLPREFIX, $gBitDb;

		$row =
			$gBitDb->getOne(
				"SELECT d_year, d_month, d_day FROM {$TBLPREFIX}dates WHERE d_file=? AND d_fact=? ORDER BY d_julianday1 DESC, d_type"
				, array($this->_ged_id, 'CHAN'));
		if ($row) {
			$date=new GedcomDate("{$row->d_day} {$row->d_month} {$row->d_year}");
			return $date->Display(false);
		} else {
			return self::gedcomDate();
		}
	}

	function gedcomHighlight() {
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom}.jpg";
		}
		elseif (file_exists("images/gedcoms/{$this->_gedcom}.png")) {
			$highlight="images/gedcoms/{$this->_gedcom}.png";
		}
		if (!$highlight) {return '';}
		$imgsize=findImageSize($highlight);
		return "<a href=\"".encode_url("{$this->_server_url}index.php?ctype=gedcom&ged={$this->_gedcom_url}")."\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

	function gedcomHighlightLeft() {
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom}.jpg";
		} else {
			if (file_exists("images/gedcoms/{$this->_gedcom}.png")) {
				$highlight="images/gedcoms/{$this->_gedcom}.png";
			}
		}
		if (!$highlight) {
			return '';
		}
		$imgsize=findImageSize($highlight);
		return "<a href=\"".encode_url("{$this->_server_url}index.php?ctype=gedcom&ged={$this->_gedcom_url}")."\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"left\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

	function gedcomHighlightRight() {
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom}.jpg";
		} else {
			if (file_exists("images/gedcoms/{$this->_gedcom}.png")) {
				$highlight="images/gedcoms/{$this->_gedcom}.png";
			}
		}
		if (!$highlight) {
			return '';
		}
		$imgsize=findImageSize($highlight);
		return "<a href=\"".encode_url("{$this->_server_url}index.php?ctype=gedcom&ged={$this->_gedcom_url}")."\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"right\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

///////////////////////////////////////////////////////////////////////////////
// Totals                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _getPercentage($total, $type) {
		$per=null;
		switch($type) {
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
			case 'note':
				$per=round(100 * $total / $this->totalNotes(), 2);
				break;
			case 'other':
				$per=round(100 * $total / $this->totalOtherRecords(), 2);
				break;
		}
		return $per;
	}

	function totalRecords() {
		return ($this->totalIndividuals() + $this->totalFamilies() + $this->totalSources() + $this->totalOtherRecords());
	}

	function totalIndividuals() {
		global $TBLPREFIX, $gBitDb;

		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=?"
				, array($this->_ged_id));
	}

	function totalIndisWithSources() {
		global $TBLPREFIX, $DBTYPE, $gBitDb;
		if ($DBTYPE=='sqlite') {
			// sqlite2 can't do subqueries or count distinct
			$rows=self::_runSQL("SELECT COUNT(i_id) AS tot FROM {$TBLPREFIX}individuals WHERE i_file=".$this->_ged_id." AND i_gedcom LIKE '%SOUR @%'");
		} else {
			$rows=self::_runSQL("SELECT COUNT(DISTINCT i_id) AS tot FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE i_id=l_from AND i_file=l_file AND l_file=".$this->_ged_id." AND l_type='SOUR'");
		}
		return $rows[0]['tot'];
	}

	function chartIndisWithSources($params=null) {
		global $pgv_lang, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividuals();
		$tot_sindi = $this->totalIndisWithSources();
		$tot_indi_per = round(100 *  ($tot_indi-$tot_sindi) / $tot_indi, 2);
		$tot_sindi_per = round(100 * $tot_sindi / $tot_indi, 2);
		$chd = self::_array_to_extended_encoding(array($tot_sindi_per, 100-$tot_sindi_per));
		$chl =  $pgv_lang["with_sources"].' - '.round($tot_sindi_per,1).'%|'.
				$pgv_lang["without_sources"].' - '.round($tot_indi_per,1).'%';
		$chart_title =  $pgv_lang["with_sources"].' ['.round($tot_sindi_per,1).'%], '.
						$pgv_lang["without_sources"].' ['.round($tot_indi_per,1).'%]';
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

	function totalIndividualsPercentage() {
		return $this->_getPercentage($this->totalIndividuals(), 'all', 2);
	}

	function totalFamilies() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}families WHERE f_file=?"
				, array($this->_ged_id));
	}

	function totalFamsWithSources() {
		global $TBLPREFIX, $DBTYPE;
		if ($DBTYPE=='sqlite') {
			// sqlite2 can't do subqueries or count distinct
			$rows=self::_runSQL("SELECT COUNT(f_id) AS tot FROM {$TBLPREFIX}families WHERE f_file=".$this->_ged_id." AND f_gedcom LIKE '%SOUR @%'");
		} else {
			$rows=self::_runSQL("SELECT COUNT(DISTINCT f_id) AS tot FROM {$TBLPREFIX}link, {$TBLPREFIX}families WHERE f_id=l_from AND f_file=l_file AND l_file=".$this->_ged_id." AND l_type='SOUR'");
		}
		return $rows[0]['tot'];
	}

	function chartFamsWithSources($params=null) {
		global $pgv_lang, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot_fam = $this->totalFamilies();
		$tot_sfam = $this->totalFamsWithSources();
		$tot_fam_per = round(100 *  ($tot_fam-$tot_sfam) / $tot_fam, 2);
		$tot_sfam_per = round(100 * $tot_sfam / $tot_fam, 2);
		$chd = self::_array_to_extended_encoding(array($tot_sfam_per, 100-$tot_sfam_per));
		$chl =  $pgv_lang["with_sources"].' - '.round($tot_sfam_per,1).'%|'.
				$pgv_lang["without_sources"].' - '.round($tot_fam_per,1).'%';
		$chart_title =  $pgv_lang["with_sources"].' ['.round($tot_sfam_per,1).'%], '.
						$pgv_lang["without_sources"].' ['.round($tot_fam_per,1).'%]';
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

	function totalFamiliesPercentage() {
		return $this->_getPercentage($this->totalFamilies(), 'all', 2);
	}

	function totalSources() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}sources WHERE s_file=?"
				, array($this->_ged_id));
	}

	function totalSourcesPercentage() {
		return $this->_getPercentage($this->totalSources(), 'all', 2);
	}

	function totalNotes() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}other WHERE o_type=? AND o_file=?"
				, array('NOTE', $this->_ged_id));
	}

	function totalNotesPercentage() {
		return $this->_getPercentage($this->totalNotes(), 'all', 2);
	}

	function totalOtherRecords() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}other WHERE o_type<>? AND o_file=?"
				, array('NOTE', $this->_ged_id));
	}

	function totalOtherPercentage() {
		return $this->_getPercentage($this->totalOtherRecords(), 'all', 2);
	}

	function totalSurnames($params = null) {
		global $DBTYPE, $TBLPREFIX, $gBitDb;
		if ($params) {
			$qs=implode(',', array_fill(0, count($params), '?'));
			$opt="IN ({$qs})";
			$vars=$params;
			$distinct='';
			$group_by='';
		} else {
			$opt ="IS NOT NULL";
			$vars='';
			$distinct='DISTINCT';
			$group_by='GROUP BY n_surn';
		}
		$vars[]=$this->_ged_id;
		return (int)
			$gBitDb->getOne(
				"SELECT COUNT({$distinct} n_surn) FROM {$TBLPREFIX}name WHERE n_surn {$opt} AND n_file=?"
				, $vars );
	}

	function totalGivennames($params = null) {
		global $DBTYPE, $TBLPREFIX, $gBitDb;
		if ($params) {
			$qs=implode(',', array_fill(0, count($params), '?'));
			$opt="IN ({$qs})";
			$vars=$params;
			$distinct='';
			$group_by='';
		} else {
			$opt ="IS NOT NULL";
			$vars='';
			$distinct='DISTINCT';
			$group_by='GROUP BY n_givn';
		}
		$vars[]=$this->_ged_id;
		return (int)
			$gBitDb->getOne(
				"SELECT COUNT({$distinct} n_givn) FROM {$TBLPREFIX}name WHERE n_givn {$opt} AND n_file=?"
				, $vars);
	}

	function totalEvents($params = null) {
		global $TBLPREFIX, $gBitDb;

		$sql="SELECT COUNT(*) AS tot FROM {$TBLPREFIX}dates WHERE d_file=?";
		$vars=array($this->_ged_id);

		$no_types=array('HEAD', 'CHAN');
		if ($params) {
			$types=array();
			foreach ($params as $type) {
				if (substr($type, 0, 1)=='!') {
					$no_types[]=substr($type, 1);
				} else {
					$types[]=$type;
				}
			}
			if ($types) {
				$sql.=' AND d_fact IN ('.implode(', ', array_fill(0, count($types), '?')).')';
				$vars=array_merge($vars, $types);
			}
		}
		$sql.=' AND d_fact NOT IN ('.implode(', ', array_fill(0, count($no_types), '?')).')';
		$vars=array_merge($vars, $no_types);
		return $gBirDb->getOne( $sql, $vars );
	}

	function totalEventsBirth() {
		return $this->totalEvents(explode('|',PGV_EVENTS_BIRT));
	}

	function totalBirths() {
		return $this->totalEvents(array('BIRT'));
	}

	function totalEventsDeath() {
		return $this->totalEvents(explode('|',PGV_EVENTS_DEAT));
	}

	function totalDeaths() {
		return $this->totalEvents(array('DEAT'));
	}

	function totalEventsMarriage() {
		return $this->totalEvents(explode('|',PGV_EVENTS_MARR));
	}

	function totalMarriages() {
		return $this->totalEvents(array('MARR'));
	}

	function totalEventsDivorce() {
		return $this->totalEvents(explode('|',PGV_EVENTS_DIV));
	}

	function totalDivorces() {
		return $this->totalEvents(array('DIV'));
	}

	function totalEventsOther() {
		$facts = array_merge(explode('|', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT));
		$no_facts = array();
		foreach ($facts as $fact) {
			$fact = '!'.str_replace('\'', '', $fact);
			$no_facts[] = $fact;
		}
		return $this->totalEvents($no_facts);
	}

	function totalSexMales() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_sex=?"
				, array($this->_ged_id, 'M'));
	}

	function totalSexMalesPercentage() {
		return $this->_getPercentage($this->totalSexMales(), 'individual');
	}

	function totalSexFemales() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_sex=?"
				, array($this->_ged_id, 'F'));
	}

	function totalSexFemalesPercentage() {
		return $this->_getPercentage($this->totalSexFemales(), 'individual');
	}

	function totalSexUnknown() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_sex=?"
				, array($this->_ged_id, 'U'));
	}

	function totalSexUnknownPercentage() {
		return $this->_getPercentage($this->totalSexUnknown(), 'individual');
	}

	function chartSex($params=null) {
		global $pgv_lang, $TEXT_DIRECTION, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_female = strtolower($params[1]);}else{$color_female = 'ffd1dc';}
		if (isset($params[2]) && $params[2] != '') {$color_male = strtolower($params[2]);}else{$color_male = '84beff';}
		if (isset($params[3]) && $params[3] != '') {$color_unknown = strtolower($params[3]);}else{$color_unknown = '777777';}
		$sizes = explode('x', $size);
		$tot_f = $this->totalSexFemalesPercentage();
		$tot_m = $this->totalSexMalesPercentage();
		$tot_u = $this->totalSexUnknownPercentage();
		if ($tot_u > 0) {
			$chd = self::_array_to_extended_encoding(array($tot_u, $tot_f, $tot_m));
			$chl =
				$pgv_lang['stat_unknown'].' - '.round($tot_u,1).'%|'.
				$pgv_lang['stat_females'].' - '.round($tot_f,1).'%|'.
				$pgv_lang['stat_males'].' - '.round($tot_m,1).'%';
			$chart_title =
				$pgv_lang['stat_males'].' ['.round($tot_m,1).'%], '.
				$pgv_lang['stat_females'].' ['.round($tot_f,1).'%], '.
				$pgv_lang['stat_unknown'].' ['.round($tot_u,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
		else {
			$chd = self::_array_to_extended_encoding(array($tot_f, $tot_m));
			$chl =
				$pgv_lang['stat_females'].' - '.round($tot_f,1).'%|'.
				$pgv_lang['stat_males'].' - '.round($tot_m,1).'%';
			$chart_title =  $pgv_lang['stat_males'].' ['.round($tot_m,1).'%], '.
							$pgv_lang['stat_females'].' ['.round($tot_f,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	function totalLiving() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_isdead=?"
				, array($this->_ged_id, 0));
	}

	function totalLivingPercentage() {
		return $this->_getPercentage($this->totalLiving(), 'individual');
	}

	function totalDeceased() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_isdead=?"
				, array($this->_ged_id, 1));
	}

	function totalDeceasedPercentage() {
		return $this->_getPercentage($this->totalDeceased(), 'individual');
	}

	function totalMortalityUnknown() {
		global $TBLPREFIX, $gBitDb;
		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_isdead=?"
				, array($this->_ged_id, -1));
	}

	function totalMortalityUnknownPercentage() {
		return $this->_getPercentage($this->totalMortalityUnknown(), 'individual');
	}

	function mortalityUnknown() {
		global $TBLPREFIX, $gBitDb;
		$rows = $gBitDb->getAll("SELECT i_id AS id FROM {$TBLPREFIX}individuals WHERE i_file={$this->_ged_id} AND i_isdead=-1");
		if (!isset($rows[0])) {return '';}
		return $rows;
	}

	function chartMortality($params=null) {
		global $pgv_lang, $TEXT_DIRECTION, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_living = strtolower($params[1]);}else{$color_living = 'ffffff';}
		if (isset($params[2]) && $params[2] != '') {$color_dead = strtolower($params[2]);}else{$color_dead = 'cccccc';}
		if (isset($params[3]) && $params[3] != '') {$color_unknown = strtolower($params[3]);}else{$color_unknown = '777777';}
		$sizes = explode('x', $size);
		$tot_l = $this->totalLivingPercentage();
		$tot_d = $this->totalDeceasedPercentage();
		$tot_u = $this->totalMortalityUnknownPercentage();
		if ($tot_u > 0) {
			$chd = self::_array_to_extended_encoding(array($tot_u, $tot_l, $tot_d));
			$chl =
				$pgv_lang['total_unknown'].' - '.round($tot_u,1).'%|'.
				$pgv_lang['total_living'].' - '.round($tot_l,1).'%|'.
				$pgv_lang['total_dead'].' - '.round($tot_d,1).'%';
			$chart_title =
				$pgv_lang['total_living'].' ['.round($tot_l,1).'%], '.
				$pgv_lang['total_dead'].' ['.round($tot_d,1).'%], '.
				$pgv_lang['total_unknown'].' ['.round($tot_u,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
		else {
			$chd = self::_array_to_extended_encoding(array($tot_l, $tot_d));
			$chl =
				$pgv_lang['total_living'].' - '.round($tot_l,1).'%|'.
				$pgv_lang['total_dead'].' - '.round($tot_d,1).'%|';
			$chart_title =  $pgv_lang['total_living'].' ['.round($tot_l,1).'%], '.
							$pgv_lang['total_dead'].' ['.round($tot_d,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	static function totalUsers($params=null) {
		if (!empty($params[0])) {
			return get_user_count() + (int)$params[0];
		} else {
			return get_user_count();
		}
	}

	static function totalAdmins() {
		return get_admin_user_count();
	}

	static function totalNonAdmins() {
		return get_non_admin_user_count();
	}

	function _totalMediaType($type='all') {
		global $TBLPREFIX, $MULTI_MEDIA, $gBitDb;

		if (!$MULTI_MEDIA || !in_array($type, self::$_media_types) && $type != 'all' && $type != 'unknown') {
			return 0;
		}
		$sql="SELECT COUNT(*) AS tot FROM {$TBLPREFIX}media WHERE m_gedfile=?";
		$vars=array($this->_ged_id);

		if ($type != 'all') {
			if ($type=='unknown') {
				// There has to be a better way then this :(
				foreach (self::$_media_types as $t) {
					$sql.=" AND m_gedrec NOT LIKE ?";
					$vars[]="%3 TYPE {$t}%";
				}
			} else {
				$sql.=" AND m_gedrec LIKE ?";
				$vars[]="%3 TYPE {$type}%";
			}
		}
		return $gBitDb->getOne( $sql, $vars );
	}

	function totalMedia() {return $this->_totalMediaType('all');}
	function totalMediaAudio() {return $this->_totalMediaType('audio');}
	function totalMediaBook() {return $this->_totalMediaType('book');}
	function totalMediaCard() {return $this->_totalMediaType('card');}
	function totalMediaCertificate() {return $this->_totalMediaType('certificate');}
	function totalMediaCoatOfArms() {return $this->_totalMediaType('coat');}
	function totalMediaDocument() {return $this->_totalMediaType('document');}
	function totalMediaElectronic() {return $this->_totalMediaType('electronic');}
	function totalMediaMagazine() {return $this->_totalMediaType('magazine');}
	function totalMediaManuscript() {return $this->_totalMediaType('manuscript');}
	function totalMediaMap() {return $this->_totalMediaType('map');}
	function totalMediaFiche() {return $this->_totalMediaType('fiche');}
	function totalMediaFilm() {return $this->_totalMediaType('film');}
	function totalMediaNewspaper() {return $this->_totalMediaType('newspaper');}
	function totalMediaPainting() {return $this->_totalMediaType('painting');}
	function totalMediaPhoto() {return $this->_totalMediaType('photo');}
	function totalMediaTombstone() {return $this->_totalMediaType('tombstone');}
	function totalMediaVideo() {return $this->_totalMediaType('video');}
	function totalMediaOther() {return $this->_totalMediaType('other');}
	function totalMediaUnknown() {return $this->_totalMediaType('unknown');}

	function chartMedia($params=null) {
		global $pgv_lang, $TEXT_DIRECTION, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot = $this->_totalMediaType('all');
		// Beware divide by zero
		if ($tot==0) {
			$tot=1;
		}
		// Build a table listing only the media types actually present in the GEDCOM
		$mediaCounts = array();
		$mediaTypes = "";
		$chart_title = "";
		$c = 0;
		foreach (self::$_media_types as $type) {
			$count = $this->_totalMediaType($type);
			if ($count != 0) {
				$mediaCounts[] = round(100 * $count / $tot, 0);
				$mediaTypes .= $pgv_lang['TYPE__'.$type];
				$mediaTypes .= ' - '.$count.'|';
				$c += $count;
				$chart_title .= $pgv_lang['TYPE__'.$type].' ['.$count.'], ';
			}
		}
		$count = $this->_totalMediaType('unknown');
		if ($count != 0) {
			$mediaCounts[] = round(100 * $count / $tot, 0);
			$mediaTypes .= $pgv_lang['unknown'];
			$mediaTypes .= ' - '.($tot-$c).'|';
			$chart_title .= $pgv_lang['unknown'].' ['.($tot-$c).']';
		}
		else {
			$chart_title = substr($chart_title,0,-2);
		}
		$chd = self::_array_to_extended_encoding($mediaCounts);
		$chl = substr($mediaTypes,0,-1);
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

///////////////////////////////////////////////////////////////////////////////
// Birth & Death                                                             //
///////////////////////////////////////////////////////////////////////////////

	function _mortalityQuery($type='full', $life_dir='ASC', $birth_death='BIRT') {
		global $TBLPREFIX, $pgv_lang, $SHOW_ID_NUMBERS, $listDir, $DBTYPE, $TEXT_DIRECTION;
		if ($birth_death == 'MARR') {
			$query_field = "'".str_replace('|', "','", PGV_EVENTS_MARR)."'";
		} else if ($birth_death == 'DIV') {
			$query_field = "'".str_replace('|', "','", PGV_EVENTS_DIV)."'";
		} else if ($birth_death == 'BIRT') {
			$query_field = "'".str_replace('|', "','", PGV_EVENTS_BIRT)."'";
		} else {
			$birth_death = 'DEAT';
			$query_field = "'".str_replace('|', "','", PGV_EVENTS_DEAT)."'";
		}
		if ($life_dir == 'ASC') {
			$dmod = 'MIN';
		} else {
			$dmod = 'MAX';
			$life_dir = 'DESC';
		}
		switch ($DBTYPE) {
			// Testing new style
			default:
			{
				$rows=self::_runSQL(''
					.' SELECT'
						.' d2.d_year,'
						.' d2.d_type,'
						.' d2.d_fact,'
						.' d2.d_gid'
					.' FROM'
						." {$TBLPREFIX}dates AS d2"
					.' WHERE'
						." d2.d_file={$this->_ged_id} AND"
						." d2.d_fact IN ({$query_field}) AND"
						.' d2.d_julianday1=('
							.' SELECT'
								." {$dmod}(d1.d_julianday1)"
							.' FROM'
								." {$TBLPREFIX}dates AS d1"
							.' WHERE'
								." d1.d_file={$this->_ged_id} AND"
								." d1.d_fact IN ({$query_field}) AND"
								.' d1.d_julianday1!=0'
						.' )'
					.' ORDER BY'
						." d_julianday1 {$life_dir}, d_type"
				);
				break;
			}
			// MySQL 4.0 can't handle nested queries, so we use the old style. Of course this hits the performance of PHP4 users a tiny bit, but it's the best we can do.
			case 'mysql':
			case 'sqlite':
			{
				$rows=self::_runSQL(''
					.' SELECT'
						.' d_year,'
						.' d_type,'
						.' d_fact,'
						.' d_gid'
					.' FROM'
						." {$TBLPREFIX}dates"
					.' WHERE'
						." d_file={$this->_ged_id} AND"
						." d_fact IN ({$query_field}) AND"
						.' d_julianday1!=0'
					.' ORDER BY'
						." d_julianday1 {$life_dir},"
						.' d_type ASC'
				, 1);
				break;
			}
		}
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		$record=GedcomRecord::getInstance($row['d_gid']);
		switch($type) {
			default:
			case 'full':
				if ($record->canDisplayDetails()) {
					$result=$record->format_list('span', false, $record->getFullName());
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
					if ($listDir=='rtl' || $TEXT_DIRECTION=='rtl') { //do we need $listDir here?
						$id="&nbsp;&nbsp;" . getRLM() . "({$row['d_gid']})" . getRLM();
					} else {
						$id="&nbsp;&nbsp;({$row['d_gid']})";
					}
				}
				$result="<a href=\"".$record->getLinkUrl()."\">".$record->getFullName()."{$id}</a>";
				break;
			case 'place':
				$result=format_fact_place(GedcomRecord::getInstance($row['d_gid'])->getFactByType($row['d_fact']), true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _statsPlaces($what='ALL', $fact=false, $parent=0, $country=false) {
		global $TBLPREFIX, $gBitDb;
		if ($fact) {
			if ($what=='INDI') {
				$rows =
					$gBitDb->query(
						"SELECT i_gedcom AS ged FROM ${TBLPREFIX}individuals WHERE i_file=?"
						, array($this->_ged_id));
			}
			else if ($what=='FAM') {
				$rows=
					$gBitDb->query(
						"SELECT f_gedcom AS ged FROM ${TBLPREFIX}families WHERE f_file=?"
						, array($this->_ged_id));
			}
			$placelist = array();
			while ( $row = $roes->fetchRow() ) {
				$factrec = trim(get_sub_record(1, "1 {$fact}", $row[ged], 1));
				if (!empty($factrec) && preg_match("/2 PLAC (.+)/", $factrec, $match)) {
					if ($country) {
						$place = getPlaceCountry(trim($match[1]));
					}
					else {
						$place = trim($match[1]);
					}
					if (!isset($placelist[$place])) {
						$placelist[$place] = 1;
					}
					else {
						$placelist[$place] ++;
					}
				}
			}
			return $placelist;
		}
		else if ($parent>0) {
			if ($what=='INDI') {
				$join = " JOIN {$TBLPREFIX}individuals ON pl_file = i_file AND pl_gid = i_id";
			}
			else if ($what=='FAM') {
				$join = " JOIN {$TBLPREFIX}families ON pl_file = f_file AND pl_gid = f_id";
			}
			else {
				$join = "";
			}
			$rows=self::_runSQL(''
				.' SELECT'
				.' p_place AS place,'
				.' COUNT(*)'
				.' FROM'
					." {$TBLPREFIX}places"
				." JOIN {$TBLPREFIX}placelinks ON pl_file=p_file AND p_id=pl_p_id"
				.$join
				.' WHERE'
					." p_id={$parent} AND"
					." p_file={$this->_ged_id}"
				.' GROUP BY place'
			);
			if (!isset($rows[0])) {return '';}
			return $rows;
		}
		else {
			if ($what=='INDI') {
				$join = " JOIN {$TBLPREFIX}individuals ON pl_file = i_file AND pl_gid = i_id";
			}
			else if ($what=='FAM') {
				$join = " JOIN {$TBLPREFIX}families ON pl_file = f_file AND pl_gid = f_id";
			}
			else {
				$join = "";
			}
			$rows=self::_runSQL(''
					.' SELECT'
						.' p_place AS country,'
						.' COUNT(*) AS tot'
					.' FROM'
						." {$TBLPREFIX}places"
					." JOIN {$TBLPREFIX}placelinks ON pl_file=p_file AND p_id=pl_p_id"
					.$join
					.' WHERE'
						." p_file={$this->_ged_id}"
						." AND p_parent_id='0'"
					.' GROUP BY country ORDER BY tot DESC, country ASC'
					);
			if (!isset($rows[0])) {return '';}
			return $rows;
		}
	}

	function totalPlaces() {
		global $TBLPREFIX, $gBitDb;

		return
			$gBitDb->getOne(
				"SELECT COUNT(*) FROM {$TBLPREFIX}places WHERE p_file=?"
				, array($this->_ged_id));
	}

	function chartDistribution($chart_shows='world', $chart_type='', $surname='') {
		global $pgv_lang, $pgv_lang_use, $countries;
		global $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_CHART_COLOR3, $PGV_STATS_MAP_X, $PGV_STATS_MAP_Y;
		// PGV uses 3-letter ISO/chapman codes, but google uses 2-letter ISO codes.  There is not a 1:1
		// mapping, so Wales/Scotland/England all become GB, etc.
		if (!isset($iso3166)) {
			$iso3166=array(
			'ABW'=>'AW', 'AFG'=>'AF', 'AGO'=>'AO', 'AIA'=>'AI', 'ALA'=>'AX', 'ALB'=>'AL', 'AND'=>'AD', 'ANT'=>'AN',
			'ARE'=>'AE', 'ARG'=>'AR', 'ARM'=>'AM', 'ASM'=>'AS', 'ATA'=>'AQ', 'ATF'=>'TF', 'ATG'=>'AG', 'AUS'=>'AU',
			'AUT'=>'AT', 'AZE'=>'AZ', 'BDI'=>'BI', 'BEL'=>'BE', 'BEN'=>'BJ', 'BFA'=>'BF', 'BGD'=>'BD', 'BGR'=>'BG',
			'BHR'=>'BH', 'BHS'=>'BS', 'BIH'=>'BA', 'BLR'=>'BY', 'BLZ'=>'BZ', 'BMU'=>'BM', 'BOL'=>'BO', 'BRA'=>'BR',
			'BRB'=>'BB', 'BRN'=>'BN', 'BTN'=>'BT', 'BVT'=>'BV', 'BWA'=>'BW', 'CAF'=>'CF', 'CAN'=>'CA', 'CCK'=>'CC',
			'CHE'=>'CH', 'CHL'=>'CL', 'CHN'=>'CN', 'CHI'=>'JE', 'CIV'=>'CI', 'CMR'=>'CM', 'COD'=>'CD', 'COG'=>'CG',
			'COK'=>'CK', 'COL'=>'CO', 'COM'=>'KM', 'CPV'=>'CV', 'CRI'=>'CR', 'CUB'=>'CU', 'CXR'=>'CX', 'CYM'=>'KY',
			'CYP'=>'CY', 'CZE'=>'CZ', 'DEU'=>'DE', 'DJI'=>'DJ', 'DMA'=>'DM', 'DNK'=>'DK', 'DOM'=>'DO', 'DZA'=>'DZ',
			'ECU'=>'EC', 'EGY'=>'EG', 'ENG'=>'GB', 'ERI'=>'ER', 'ESH'=>'EH', 'ESP'=>'ES', 'EST'=>'EE', 'ETH'=>'ET',
			'FIN'=>'FI', 'FJI'=>'FJ', 'FLK'=>'FK', 'FRA'=>'FR', 'FRO'=>'FO', 'FSM'=>'FM', 'GAB'=>'GA', 'GBR'=>'GB',
			'GEO'=>'GE', 'GHA'=>'GH', 'GIB'=>'GI', 'GIN'=>'GN', 'GLP'=>'GP', 'GMB'=>'GM', 'GNB'=>'GW', 'GNQ'=>'GQ',
			'GRC'=>'GR', 'GRD'=>'GD', 'GRL'=>'GL', 'GTM'=>'GT', 'GUF'=>'GF', 'GUM'=>'GU', 'GUY'=>'GY', 'HKG'=>'HK',
			'HMD'=>'HM', 'HND'=>'HN', 'HRV'=>'HR', 'HTI'=>'HT', 'HUN'=>'HU', 'IDN'=>'ID', 'IND'=>'IN', 'IOT'=>'IO',
			'IRL'=>'IE', 'IRN'=>'IR', 'IRQ'=>'IQ', 'ISL'=>'IS', 'ISR'=>'IL', 'ITA'=>'IT', 'JAM'=>'JM', 'JOR'=>'JO',
			'JPN'=>'JA', 'KAZ'=>'KZ', 'KEN'=>'KE', 'KGZ'=>'KG', 'KHM'=>'KH', 'KIR'=>'KI', 'KNA'=>'KN', 'KOR'=>'KO',
			'KWT'=>'KW', 'LAO'=>'LA', 'LBN'=>'LB', 'LBR'=>'LR', 'LBY'=>'LY', 'LCA'=>'LC', 'LIE'=>'LI', 'LKA'=>'LK',
			'LSO'=>'LS', 'LTU'=>'LT', 'LUX'=>'LU', 'LVA'=>'LV', 'MAC'=>'MO', 'MAR'=>'MA', 'MCO'=>'MC', 'MDA'=>'MD',
			'MDG'=>'MG', 'MDV'=>'MV', 'MEX'=>'ME', 'MHL'=>'MH', 'MKD'=>'MK', 'MLI'=>'ML', 'MLT'=>'MT', 'MMR'=>'MM',
			'MNG'=>'MN', 'MNP'=>'MP', 'MNT'=>'ME', 'MOZ'=>'MZ', 'MRT'=>'MR', 'MSR'=>'MS', 'MTQ'=>'MQ', 'MUS'=>'MU',
			'MWI'=>'MW', 'MYS'=>'MY', 'MYT'=>'YT', 'NAM'=>'NA', 'NCL'=>'NC', 'NER'=>'NE', 'NFK'=>'NF', 'NGA'=>'NG',
			'NIC'=>'NI', 'NIR'=>'GB', 'NIU'=>'NU', 'NLD'=>'NL', 'NOR'=>'NO', 'NPL'=>'NP', 'NRU'=>'NR', 'NZL'=>'NZ',
			'OMN'=>'OM', 'PAK'=>'PK', 'PAN'=>'PA', 'PCN'=>'PN', 'PER'=>'PE', 'PHL'=>'PH', 'PLW'=>'PW', 'PNG'=>'PG',
			'POL'=>'PL', 'PRI'=>'PR', 'PRK'=>'KP', 'PRT'=>'PO', 'PRY'=>'PY', 'PSE'=>'PS', 'PYF'=>'PF', 'QAT'=>'QA',
			'REU'=>'RE', 'ROM'=>'RO', 'RUS'=>'RU', 'RWA'=>'RW', 'SAU'=>'SA', 'SCT'=>'GB', 'SDN'=>'SD', 'SEN'=>'SN',
			'SER'=>'RS', 'SGP'=>'SG', 'SGS'=>'GS', 'SHN'=>'SH', 'SIC'=>'IT', 'SJM'=>'SJ', 'SLB'=>'SB', 'SLE'=>'SL',
			'SLV'=>'SV', 'SMR'=>'SM', 'SOM'=>'SO', 'SPM'=>'PM', 'STP'=>'ST', 'SUN'=>'RU', 'SUR'=>'SR', 'SVK'=>'SK',
			'SVN'=>'SI', 'SWE'=>'SE', 'SWZ'=>'SZ', 'SYC'=>'SC', 'SYR'=>'SY', 'TCA'=>'TC', 'TCD'=>'TD', 'TGO'=>'TG',
			'THA'=>'TH', 'TJK'=>'TJ', 'TKL'=>'TK', 'TKM'=>'TM', 'TLS'=>'TL', 'TON'=>'TO', 'TTO'=>'TT', 'TUN'=>'TN',
			'TUR'=>'TR', 'TUV'=>'TV', 'TWN'=>'TW', 'TZA'=>'TZ', 'UGA'=>'UG', 'UKR'=>'UA', 'UMI'=>'UM', 'URY'=>'UY',
			'USA'=>'US', 'UZB'=>'UZ', 'VAT'=>'VA', 'VCT'=>'VC', 'VEN'=>'VE', 'VGB'=>'VG', 'VIR'=>'VI', 'VNM'=>'VN',
			'VUT'=>'VU', 'WLF'=>'WF', 'WLS'=>'GB', 'WSM'=>'WS', 'YEM'=>'YE', 'ZAF'=>'ZA', 'ZMB'=>'ZM', 'ZWE'=>'ZW'
			);
		}
		// The country names can be specified in any language or in the chapman code.
		// Generate a combined list.
		if (!isset($country_to_iso3166)) {
			$country_to_iso3166=array();
			foreach ($iso3166 as $three=>$two) {
				$country_to_iso3166[UTF8_strtolower($three)]=$two;
			}
			foreach ($pgv_lang_use as $lang=>$use) {
				if ($use) {
					loadLangFile('pgv_country', $lang);
					foreach ($countries as $code => $country) {
						if (array_key_exists($code, $iso3166)) {
							$country_to_iso3166[UTF8_strtolower($country)]=$iso3166[$code];
						}
					}
				}
			}
		}
		switch ($chart_type) {
		case 'surname_distribution_chart':
			if ($surname=="") $surname = $this->getCommonSurname();
			$chart_title=$pgv_lang["surname_distribution_chart"].': '.$surname;
			// Count how many people are events in each country
			$surn_countries=array();
			$indis = get_indilist_indis(UTF8_strtoupper($surname), '', '', false, false, PGV_GED_ID);
			foreach ($indis as $person) {
				if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $person->gedrec, $matches)) {
					// PGV uses 3 letter country codes and localised country names, but google uses 2 letter codes.
					foreach ($matches[1] as $country) {
						$country=UTF8_strtolower(trim($country));
						if (array_key_exists($country, $country_to_iso3166)) {
							if (array_key_exists($country_to_iso3166[$country], $surn_countries)) {
								$surn_countries[$country_to_iso3166[$country]]++;
							} else {
								$surn_countries[$country_to_iso3166[$country]]=1;
							}
						}
					}
				}
			};
			break;
		case 'birth_distribution_chart':
			$chart_title=$pgv_lang["stat_2_map"];
			// Count how many people were born in each country
			$surn_countries=array();
			$countries=$this->_statsPlaces('INDI', 'BIRT', 0, true);
			foreach ($countries as $place=>$count) {
				$country=UTF8_strtolower($place);
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]]=$count;
					}
					else {
						$surn_countries[$country_to_iso3166[$country]]+=$count;
					}
				}
			}
			break;
		case 'death_distribution_chart':
			$chart_title=$pgv_lang["stat_3_map"];
			// Count how many people were death in each country
			$surn_countries=array();
			$countries=$this->_statsPlaces('INDI', 'DEAT', 0, true);
			foreach ($countries as $place=>$count) {
				$country=UTF8_strtolower($place);
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]]=$count;
					}
					else {
						$surn_countries[$country_to_iso3166[$country]]+=$count;
					}
				}
			}
			break;
		case 'marriage_distribution_chart':
			$chart_title=$pgv_lang["stat_4_map"];
			// Count how many families got marriage in each country
			$surn_countries=array();
			$countries=$this->_statsPlaces('FAM');
			// PGV uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			if (!empty($countries))
			  foreach ($countries as $place) {
				$country=UTF8_strtolower(trim($place['country']));
				if (array_key_exists($country, $country_to_iso3166)) {
					$surn_countries[$country_to_iso3166[$country]]=$place['tot'];
				}
			}
			break;
		case 'indi_distribution_chart':
		default:
			$chart_title=$pgv_lang["indi_distribution_chart"];
			// Count how many people are events in each country
			$surn_countries=array();
			$countries=$this->_statsPlaces('INDI');
			// PGV uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			if (!empty($countries))
			  foreach ($countries as $place) {
				$country=UTF8_strtolower(trim($place['country']));
				if (array_key_exists($country, $country_to_iso3166)) {
					$surn_countries[$country_to_iso3166[$country]]=$place['tot'];
				}
			}
			break;
		}
		$chart_url ="http://chart.apis.google.com/chart?cht=t&amp;chtm=".$chart_shows;
		$chart_url.="&amp;chco=".$PGV_STATS_CHART_COLOR1.",".$PGV_STATS_CHART_COLOR3.",".$PGV_STATS_CHART_COLOR2; // country colours
		$chart_url.="&amp;chf=bg,s,ECF5FF"; // sea colour
		$chart_url.="&amp;chs=".$PGV_STATS_MAP_X."x".$PGV_STATS_MAP_Y;
		$chart_url.="&amp;chld=".implode('', array_keys($surn_countries))."&amp;chd=s:";
		foreach ($surn_countries as $count) {
			$chart_url.=substr(PGV_GOOGLE_CHART_ENCODING, floor($count/max($surn_countries)*61), 1);
		}
		$chart = '<div id="google_charts" class="center">';
		$chart .= '<b>'.$chart_title.'</b><br /><br />';
		$chart .= '<div align="center"><img src="'.$chart_url.'" alt="'.$chart_title.'" title="'.$chart_title.'" class="gchart" /><br />';
		$chart .= '<table align="center" border="0" cellpadding="1" cellspacing="1"><tr>';
		$chart .= '<td bgcolor="#'.$PGV_STATS_CHART_COLOR2.'" width="12"></td><td>'.$pgv_lang["g_chart_high"].'&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#'.$PGV_STATS_CHART_COLOR3.'" width="12"></td><td>'.$pgv_lang["g_chart_low"].'&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#'.$PGV_STATS_CHART_COLOR1.'" width="12"></td><td>'.$pgv_lang["g_chart_nobody"].'&nbsp;&nbsp;</td>';
		$chart .= '</tr></table></div></div>';
		return $chart;
	}

	function commonCountriesList() {
		global $TEXT_DIRECTION;
		$countries = $this->_statsPlaces();
		$top10 = array();
		$i = 1;
		foreach ($countries as $country) {
			$place = '<a href="'.encode_url(get_place_url($country['country'])).'" class="list_item" title="'.$country['country'].'">'.PrintReady($country['country']).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$country['tot']."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonBirthPlacesList() {
		global $TEXT_DIRECTION;
		$places = $this->_statsPlaces('INDI', 'BIRT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.encode_url(get_place_url($place)).'" class="list_item" title="'.$place.'">'.PrintReady($place).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$count."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonDeathPlacesList() {
		global $TEXT_DIRECTION;
		$places = $this->_statsPlaces('INDI', 'DEAT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.encode_url(get_place_url($place)).'" class="list_item" title="'.$place.'">'.PrintReady($place).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$count."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonMarriagePlacesList() {
		global $TEXT_DIRECTION;
		$places = $this->_statsPlaces('FAM', 'MARR');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.encode_url(get_place_url($place)).'" class="list_item" title="'.$place.'">'.PrintReady($place).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$count."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		return "<ul>\n{$top10}</ul>\n";
	}

	function statsBirth($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='BIRT'";
		} else if ($sex) {
			$sql = "SELECT d_month, i_sex, COUNT(*) FROM {$TBLPREFIX}dates "
					."JOIN {$TBLPREFIX}individuals ON d_file = i_file AND d_gid = i_id "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='BIRT'";
		} else {
			$sql = "SELECT d_month, COUNT(*) FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='BIRT'";
		}
		if ($year1>=0 && $year2>=0) {
			$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
		}
		if ($simple) {
			$sql .= " GROUP BY century ORDER BY century";
		} else {
			$sql .= " GROUP BY d_month";
			if ($sex) $sql .= ", i_sex";
		}
		$rows=self::_runSQL($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['count(*)'];
			}
			// Beware divide by zero
			if ($tot==0) $tot=1;
			$centuries = "";
			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['count(*)'] / $tot, 0);;
				$centuries .= $century.' - '.$values['count(*)'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_5_birth"]."\" title=\"".$pgv_lang["stat_5_birth"]."\" />";
		}
		if (!isset($rows)) return 0;
		return $rows;
	}

	function statsDeath($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='DEAT'";
		} else if ($sex) {
			$sql = "SELECT d_month, i_sex, COUNT(*) FROM {$TBLPREFIX}dates "
					."JOIN {$TBLPREFIX}individuals ON d_file = i_file AND d_gid = i_id "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='DEAT'";
		} else {
			$sql = "SELECT d_month, COUNT(*) FROM {$TBLPREFIX}dates "
					."WHERE "
					."d_file={$this->_ged_id} AND "
					."d_fact='DEAT'";
		}
		if ($year1>=0 && $year2>=0) {
			$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
		}
		if ($simple) {
			$sql .= " GROUP BY century ORDER BY century";
		} else {
			$sql .= " GROUP BY d_month";
			if ($sex) $sql .= ", i_sex";
		}
		$rows=self::_runSQL($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['count(*)'];
			}
			// Beware divide by zero
			if ($tot==0) $tot=1;
			$centuries = "";
			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			$counts=array();
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['count(*)'] / $tot, 0);;
				$centuries .= $century.' - '.$values['count(*)'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_6_death"]."\" title=\"".$pgv_lang["stat_6_death"]."\" />";
		}
		if (!isset($rows)) {return 0;}
		return $rows;
	}

	//
	// Birth
	//

	function firstBirth() {return $this->_mortalityQuery('full', 'ASC', 'BIRT');}
	function firstBirthYear() {return $this->_mortalityQuery('year', 'ASC', 'BIRT');}
	function firstBirthName() {return $this->_mortalityQuery('name', 'ASC', 'BIRT');}
	function firstBirthPlace() {return $this->_mortalityQuery('place', 'ASC', 'BIRT');}

	function lastBirth() {return $this->_mortalityQuery('full', 'DESC', 'BIRT');}
	function lastBirthYear() {return $this->_mortalityQuery('year', 'DESC', 'BIRT');}
	function lastBirthName() {return $this->_mortalityQuery('name', 'DESC', 'BIRT');}
	function lastBirthPlace() {return $this->_mortalityQuery('place', 'DESC', 'BIRT');}

	//
	// Death
	//

	function firstDeath() {return $this->_mortalityQuery('full', 'ASC', 'DEAT');}
	function firstDeathYear() {return $this->_mortalityQuery('year', 'ASC', 'DEAT');}
	function firstDeathName() {return $this->_mortalityQuery('name', 'ASC', 'DEAT');}
	function firstDeathPlace() {return $this->_mortalityQuery('place', 'ASC', 'DEAT');}

	function lastDeath() {return $this->_mortalityQuery('full', 'DESC', 'DEAT');}
	function lastDeathYear() {return $this->_mortalityQuery('year', 'DESC', 'DEAT');}
	function lastDeathName() {return $this->_mortalityQuery('name', 'DESC', 'DEAT');}
	function lastDeathPlace() {return $this->_mortalityQuery('place', 'DESC', 'DEAT');}

///////////////////////////////////////////////////////////////////////////////
// Lifespan                                                                  //
///////////////////////////////////////////////////////////////////////////////

	function _longlifeQuery($type='full', $sex='F') {
		global $TBLPREFIX, $pgv_lang, $SHOW_ID_NUMBERS, $listDir;

		$sex_search = ' 1=1';
		if ($sex == 'F') {
			$sex_search = " i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " i_sex='M'";
		}

		$rows=self::_runSQL(''
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
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1!=0 AND'
				.' death.d_julianday1>birth.d_julianday2 AND'
				.$sex_search
			.' ORDER BY'
				.' age DESC'
		, 1);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$person=Person::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if (displayDetailsById($row['id'])) {
					$result=$person->format_list('span', false, $person->getFullName());
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
				$result="<a href=\"".$person->getLinkUrl()."\">".$person->getFullName()."{$id}</a>";
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenOldest($type='list', $sex='BOTH', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang, $lang_short_cut, $LANGUAGE;

		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT '
				.' MAX(death.d_julianday2-birth.d_julianday1) AS age,'
				.' death.d_gid AS deathdate'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1!=0 AND'
				.' death.d_julianday1>birth.d_julianday2'
				.$sex_search
			.' GROUP BY'
				.' deathdate'
			.' ORDER BY'
				.' age DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		$func = "age_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $row) {
			$person = Person::getInstance($row['deathdate']);
			$age = $row['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'list') {
				$top10[]="\t<li><a href=\"".$person->getLinkUrl()."\">".PrintReady($person->getFullName())."</a> [".$age."]</li>\n";
			} else {
				$top10[]="<a href=\"".$person->getLinkUrl()."\">".PrintReady($person->getFullName())."</a> [".$age."]";
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10=join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		// Statstics are used by RSS feeds, etc., so need absolute URLs.
		return $top10;
	}

	function _topTenOldestAlive($type='list', $sex='BOTH', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang, $lang_short_cut, $LANGUAGE;

		if (!PGV_USER_CAN_ACCESS) return $pgv_lang["privacy_error"];
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT'
				.' birth.d_gid AS id,'
				.' birth.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' indi.i_isdead=0 AND'
				." birth.d_file={$this->_ged_id} AND"
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				.' birth.d_julianday1!=0'
				.$sex_search
			.' GROUP BY'
				.' id'
			.' ORDER BY'
				.' age ASC'
		, $total);
		if (!isset($rows)) {return 0;}
		$top10 = array();
		$func = "age_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $row) {
			$person=Person::getInstance($row['id']);
			$age = (client_jd()-$row['age']);
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'list') {
				$top10[]="\t<li><a href=\"".$person->getLinkUrl()."\">".PrintReady($person->getFullName())."</a> [".$age."]</li>\n";
			} else {
				$top10[]="<a href=\"".$person->getLinkUrl()."\">".PrintReady($person->getFullName())."</a> [".$age."]";
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10=join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _averageLifespanQuery($sex='BOTH') {
		global $TBLPREFIX;
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		$rows=self::_runSQL(''
			.' SELECT'
				.' AVG(death.d_julianday2-birth.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1!=0 AND'
				.' death.d_julianday1>birth.d_julianday2'
				.$sex_search
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		return PrintReady(floor($row['age']/365.25));
	}

	function statsAge($simple=true, $related='BIRT', $sex='BOTH', $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE;

		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '230x250';}
			$sizes = explode('x', $size);
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(death.d_julianday2-birth.d_julianday1)/365.25,1) AS age,'
					.' ROUND((death.d_year+49.1)/100) AS century,'
					.' i_sex AS sex'
				.' FROM'
					." {$TBLPREFIX}dates AS death,"
					." {$TBLPREFIX}dates AS birth,"
					." {$TBLPREFIX}individuals AS indi"
				.' WHERE'
					.' indi.i_id=birth.d_gid AND'
					.' birth.d_gid=death.d_gid AND'
					." death.d_file={$this->_ged_id} AND"
					.' birth.d_file=death.d_file AND'
					.' birth.d_file=indi.i_file AND'
					." birth.d_fact='BIRT' AND"
					." death.d_fact='DEAT' AND"
					.' birth.d_julianday1!=0 AND'
					.' death.d_julianday1>birth.d_julianday2'
				.' GROUP BY century, sex ORDER BY century, sex');

			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			$chxl = "0:|";
			$male = true;
			$temp = "";
			$countsm = "";
			$countsf = "";
			$countsa = "";
			foreach ($rows as $values) {
				if ($temp!=$values['century']) {
					$temp = $values['century'];
					if ($sizes[0]<980) $sizes[0] += 50;
					if (function_exists($func)) {
						$century = $func($values['century'], false);
					} else {
						$century = $values['century'];
					}
					$chxl .= $century."|";
					if ($values['sex'] == "F") {
						if (!$male) {
							$countsm .= "0,";
							$countsa .= $fage.",";
						}
						$countsf .= $values['age'].",";
						$fage = $values['age'];
						$male = false;
					} else if ($values['sex'] == "M") {
						$countsf .= "0,";
						$countsm .= $values['age'].",";
						$countsa .= $values['age'].",";
					} else if ($values['sex'] == "U") {
						$countsf .= "0,";
						$countsm .= "0,";
						$countsa .= "0,";
					}
				}
				else if ($values['sex'] == "M") {
					$countsm .= $values['age'].",";
					$countsa .= round(($fage+$values['age'])/2,1).",";
					$male = true;
				}
			}
			$countsm = substr($countsm,0,-1);
			$countsf = substr($countsf,0,-1);
			$countsa = substr($countsa,0,-1);
			$chd = "t2:{$countsm}|{$countsf}|{$countsa}";
			$chxl .= "1:||".$pgv_lang["century"]."|2:|0|10|20|30|40|50|60|70|80|90|100|3:||".$pgv_lang["stat_age"]."|";
			$chtt = $pgv_lang["stat_18_aard"];
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|N*f1*,000000,0,-1,11|N*f1*,000000,1,-1,11&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt={$chtt}&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}&amp;chdl={$pgv_lang["male"]}|{$pgv_lang["female"]}|{$pgv_lang["stat_avg_age_at_death"]}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_18_aard"]."\" title=\"".$pgv_lang["stat_18_aard"]."\" />";
		} else {
			$sex_search = '';
			$years = '';
			if ($sex == 'F') {
				$sex_search = " AND i_sex='F'";
			} elseif ($sex == 'M') {
				$sex_search = " AND i_sex='M'";
			}
			if ($year1>=0 && $year2>=0) {
				if ($related=='BIRT') {
					$years = " AND birth.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
				else if ($related=='DEAT') {
					$years = " AND death.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			}
			$rows=self::_runSQL(''
				.' SELECT'
					.' death.d_julianday2-birth.d_julianday1 AS age'
				.' FROM'
					." {$TBLPREFIX}dates AS death,"
					." {$TBLPREFIX}dates AS birth,"
					." {$TBLPREFIX}individuals AS indi"
				.' WHERE'
					.' indi.i_id=birth.d_gid AND'
					.' birth.d_gid=death.d_gid AND'
					." death.d_file={$this->_ged_id} AND"
					.' birth.d_file=death.d_file AND'
					.' birth.d_file=indi.i_file AND'
					." birth.d_fact='BIRT' AND"
					." death.d_fact='DEAT' AND"
					.' birth.d_julianday1!=0 AND'
					.' death.d_julianday1>birth.d_julianday2'
					.$years
					.$sex_search
				.' ORDER BY age DESC');
			if (!isset($rows)) {return 0;}
			return $rows;
		}
	}

	// Both Sexes

	function longestLife() {return $this->_longlifeQuery('full', 'BOTH');}
	function longestLifeAge() {return $this->_longlifeQuery('age', 'BOTH');}
	function longestLifeName() {return $this->_longlifeQuery('name', 'BOTH');}

	function topTenOldest($params=null) {return $this->_topTenOldest('nolist', 'BOTH', $params);}
	function topTenOldestList($params=null) {return $this->_topTenOldest('list', 'BOTH', $params);}
	function topTenOldestAlive($params=null) {return $this->_topTenOldestAlive('nolist', 'BOTH', $params);}
	function topTenOldestListAlive($params=null) {return $this->_topTenOldestAlive('list', 'BOTH', $params);}

	function averageLifespan() {return $this->_averageLifespanQuery('BOTH');}

	// Female Only

	function longestLifeFemale() {return $this->_longlifeQuery('full', 'F');}
	function longestLifeFemaleAge() {return $this->_longlifeQuery('age', 'F');}
	function longestLifeFemaleName() {return $this->_longlifeQuery('name', 'F');}

	function topTenOldestFemale($params=null) {return $this->_topTenOldest('nolist', 'F', $params);}
	function topTenOldestFemaleList($params=null) {return $this->_topTenOldest('list', 'F', $params);}
	function topTenOldestFemaleAlive($params=null) {return $this->_topTenOldestAlive('nolist', 'F', $params);}
	function topTenOldestFemaleListAlive($params=null) {return $this->_topTenOldestAlive('list', 'F', $params);}

	function averageLifespanFemale() {return $this->_averageLifespanQuery('F');}

	// Male Only

	function longestLifeMale() {return $this->_longlifeQuery('full', 'M');}
	function longestLifeMaleAge() {return $this->_longlifeQuery('age', 'M');}
	function longestLifeMaleName() {return $this->_longlifeQuery('name', 'M');}

	function topTenOldestMale($params=null) {return $this->_topTenOldest('nolist', 'M', $params);}
	function topTenOldestMaleList($params=null) {return $this->_topTenOldest('list', 'M', $params);}
	function topTenOldestMaleAlive($params=null) {return $this->_topTenOldestAlive('nolist', 'M', $params);}
	function topTenOldestMaleListAlive($params=null) {return $this->_topTenOldestAlive('list', 'M', $params);}

	function averageLifespanMale() {return $this->_averageLifespanQuery('M');}

///////////////////////////////////////////////////////////////////////////////
// Events                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _eventQuery($type, $direction, $facts) {
		global $TBLPREFIX, $pgv_lang, $SHOW_ID_NUMBERS, $listDir;
		$eventTypes = array(
			'BIRT'=>$pgv_lang['htmlplus_block_birth'],
			'DEAT'=>$pgv_lang['htmlplus_block_death'],
			'MARR'=>$pgv_lang['htmlplus_block_marrage'],
			'ADOP'=>$pgv_lang['htmlplus_block_adoption'],
			'BURI'=>$pgv_lang['htmlplus_block_burial'],
			'CENS'=>$pgv_lang['htmlplus_block_census']
		);

		$fact_query = "IN ('".str_replace('|', "','", $facts)."')";

		if ($direction != 'ASC') {$direction = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT'
				.' d_gid AS id,'
				.' d_year AS year,'
				.' d_fact AS fact,'
				.' d_type AS type'
			.' FROM'
				." {$TBLPREFIX}dates"
			.' WHERE'
				." d_file={$this->_ged_id} AND"
				." d_gid!='HEAD' AND"
				." d_fact {$fact_query} AND"
				.' d_julianday1!=0'
			.' ORDER BY'
				." d_julianday1 {$direction}, d_type"
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		$record=GedcomRecord::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($record->canDisplayDetails()) {
					$result=$record->format_list('span', false, $record->getFullName());
				} else {
					$result=$pgv_lang['privacy_error'];
				}
				break;
			case 'year':
				$date=new GedcomDate($row['type'].' '.$row['year']);
				$result=$date->Display(true);
				break;
			case 'type':
				if (isset($eventTypes[$row['fact']])) {
					$result=$eventTypes[$row['fact']];
				} else {
					$result='';
				}
				break;
			case 'name':
				$id = '';
				if ($SHOW_ID_NUMBERS) {
					if ($listDir == 'rtl') {
						$id="&nbsp;&nbsp;" . getRLM() . "({$row['id']})" . getRLM();
					} else {
						$id="&nbsp;&nbsp;({$row['id']})";
					}
				}
				$result="<a href=\"".$record->getLinkUrl()."\">".PrintReady($record->getFullName())."{$id}</a>";
				break;
			case 'place':
				$result=format_fact_place($record->getFactByType($row['fact']), true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function firstEvent() {
		return $this->_eventQuery('full', 'ASC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function firstEventYear() {
		return $this->_eventQuery('year', 'ASC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function firstEventType() {
		return $this->_eventQuery('type', 'ASC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function firstEventName() {
		return $this->_eventQuery('name', 'ASC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function firstEventPlace() {
		return $this->_eventQuery('place', 'ASC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function lastEvent() {
		return $this->_eventQuery('full', 'DESC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function lastEventYear() {
		return $this->_eventQuery('year', 'DESC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function lastEventType() {
		return $this->_eventQuery('type', 'DESC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function lastEventName() {
		return $this->_eventQuery('name', 'DESC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}
	function lastEventPlace() {
		return $this->_eventQuery('place', 'DESC', PGV_EVENTS_BIRT.'|'.PGV_EVENTS_MARR.'|'.PGV_EVENTS_DIV.'|'.PGV_EVENTS_DEAT);
	}

///////////////////////////////////////////////////////////////////////////////
// Marriage                                                                  //
///////////////////////////////////////////////////////////////////////////////

	/*
	* Query the database for marriage tags.
	*/
	function _marriageQuery($type='full', $age_dir='ASC', $sex='F') {
		global $TBLPREFIX, $pgv_lang;
		if ($sex == 'F') {$sex_field = 'f_wife';}else{$sex_field = 'f_husb';}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT'
				.' fam.f_id AS famid,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age,'
				.' indi.i_id AS i_id'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' birth.d_gid = indi.i_id AND'
				.' married.d_gid = fam.f_id AND'
				." indi.i_id = fam.{$sex_field} AND"
				." fam.f_file = {$this->_ged_id} AND"
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." married.d_fact = 'MARR' AND"
				.' birth.d_julianday1 != 0 AND'
				.' married.d_julianday2 > birth.d_julianday1 AND'
				." i_sex='{$sex}'"
			.' ORDER BY'
				." married.d_julianday2-birth.d_julianday1 {$age_dir}"
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		if (isset($row['famid'])) $family=Family::getInstance($row['famid']);
		if (isset($row['i_id'])) $person=Person::getInstance($row['i_id']);
		switch($type) {
			default:
			case 'full':
				if ($family->canDisplayDetails()) {
					$result=$family->format_list('span', false, $person->getFullName());
				} else {
					$result=$pgv_lang['privacy_error'];
				}
				break;
			case 'name':
				$result="<a href=\"".$family->getLinkUrl()."\">".$person->getFullName().'</a>';
				break;
			case 'age':
				$result=floor($row['age']/365.25);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _ageOfMarriageQuery($type='list', $age_dir='ASC', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang, $lang_short_cut, $LANGUAGE;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$hrows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' husbdeath.d_julianday2-married.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS husbdeath ON husbdeath.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' husbdeath.d_gid = fam.f_husb AND'
				." husbdeath.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' married.d_gid = fam.f_id AND'
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday1 < husbdeath.d_julianday2 AND'
				.' married.d_julianday1 != 0'
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age {$age_dir}"
		,($total*3));
		$wrows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' wifedeath.d_julianday2-married.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS wifedeath ON wifedeath.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' wifedeath.d_gid = fam.f_wife AND'
				." wifedeath.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' married.d_gid = fam.f_id AND'
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday1 < wifedeath.d_julianday2 AND'
				.' married.d_julianday1 != 0'
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age {$age_dir}"
		,($total*3));
		$drows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' divorced.d_julianday2-married.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS divorced ON divorced.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' married.d_gid = fam.f_id AND'
				." married.d_fact = 'MARR' AND"
				.' divorced.d_gid = fam.f_id AND'
				." divorced.d_fact IN ('DIV', 'ANUL', '_SEPR', '_DETS') AND"
				.' married.d_julianday1 < divorced.d_julianday2 AND'
				.' married.d_julianday1 != 0'
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age {$age_dir}"
		,($total*3));
		if (!isset($hrows) && !isset($wrows) && !isset($drows)) {return 0;}
		$rows = array();
		foreach ($drows as $family) {
			$rows[$family['family']] = $family['age'];
		}
		foreach ($hrows as $family) {
			if (!isset($rows[$family['family']])) $rows[$family['family']] = $family['age'];
		}
		foreach ($wrows as $family) {
			if (!isset($rows[$family['family']])) $rows[$family['family']] = $family['age'];
			else if ($rows[$family['family']] > $family['age']) $rows[$family['family']] = $family['age'];
		}
		if ($age_dir == 'DESC') {arsort($rows);}
		else {asort($rows);}
		$top10 = array();
		$i = 0;
		$func = "age_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $fam=>$age) {
			$family = Family::getInstance($fam);
			if ($type == 'name') {
				return $family->format_list('span', false, $family->getFullName());
			}
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'age') {
				return $age;
			}
			$husb = $family->getHusband();
			$wife = $family->getWife();
			if (($husb->getAllDeathDates() && $wife->getAllDeathDates()) || !$husb->isDead() || !$wife->isDead()) {
				if ($family->canDisplayDetails()) {
					if ($type == 'list') {
						$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [".$age."]</li>\n";
					} else {
						$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [".$age."]";
					}
				}
				if (++$i==10) break;
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _ageBetweenSpousesQuery($type='list', $age_dir='DESC', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang, $lang_short_cut, $LANGUAGE;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		if ($age_dir=='DESC') {
			$query1 = ' wifebirth.d_julianday2-husbbirth.d_julianday1 AS age';
			$query2 = ' wifebirth.d_julianday2 >= husbbirth.d_julianday1 AND'
					 .' husbbirth.d_julianday1 != 0';
		} else {
			$query1 = ' husbbirth.d_julianday2-wifebirth.d_julianday1 AS age';
			$query2 = ' wifebirth.d_julianday1 < husbbirth.d_julianday2 AND'
					 .' wifebirth.d_julianday1 != 0';
		}
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.$query1
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS wifebirth ON wifebirth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS husbbirth ON husbbirth.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' husbbirth.d_gid = fam.f_husb AND'
				." husbbirth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				.' wifebirth.d_gid = fam.f_wife AND'
				." wifebirth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				.$query2
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age DESC"
		,$total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		$func = "age_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $fam) {
			$family=Family::getInstance($fam['family']);
			if ($fam['age']<0) break;
			$age = $fam['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [{$age}]</li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [{$age}]";
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _parentsQuery($type='full', $age_dir='ASC', $sex='F') {
		global $TBLPREFIX, $pgv_lang;
		if ($sex == 'F') {$sex_field = 'WIFE';}else{$sex_field = 'HUSB';}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' parentfamily.l_to AS id,'
				.' childbirth.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}link AS parentfamily"
			.' JOIN'
				." {$TBLPREFIX}link AS childfamily ON childfamily.l_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}dates AS childbirth ON childbirth.d_file = {$this->_ged_id}"
			.' WHERE'
				.' birth.d_gid = parentfamily.l_to AND'
				.' childfamily.l_to = childbirth.d_gid AND'
				." childfamily.l_type = 'CHIL' AND"
				." parentfamily.l_type = '{$sex_field}' AND"
				.' childfamily.l_from = parentfamily.l_from AND'
				." parentfamily.l_file = {$this->_ged_id} AND"
				." birth.d_fact = 'BIRT' AND"
				." childbirth.d_fact = 'BIRT' AND"
				.' birth.d_julianday1 != 0 AND'
				.' childbirth.d_julianday2 > birth.d_julianday1'
			.' ORDER BY'
				." age {$age_dir}"
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		if (isset($row['id'])) $person=Person::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($person->canDisplayDetails()) {
					$result=$person->format_list('span', false, $person->getFullName());
				} else {
					$result=$pgv_lang['privacy_error'];
				}
				break;
			case 'name':
				$result="<a href=\"".$person->getLinkUrl()."\">".$person->getFullName().'</a>';
				break;
			case 'age':
				$result=floor($row['age']/365.25);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function statsMarr($simple=true, $first=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='MARR'";
						if ($year1>=0 && $year2>=0) {
							$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
						}
					$sql .= " GROUP BY century ORDER BY century";
		} else if ($first) {
			$years = '';
			if ($year1>=0 && $year2>=0) {
				$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
			}
			$sql=''
			.' SELECT'
				.' fam.f_id AS fams,'
				.' fam.f_husb, fam.f_wife,'
				.' married.d_julianday2 AS age,'
				.' married.d_month AS month,'
				.' indi.i_id AS indi'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' married.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday2 != 0 AND'
				.$years
				.' (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)'
			.' ORDER BY fams, indi, age ASC';
		} else {
			$sql = "SELECT d_month, COUNT(*) FROM {$TBLPREFIX}dates "
				."WHERE "
				."d_file={$this->_ged_id} AND "
				."d_fact='MARR'";
				if ($year1>=0 && $year2>=0) {
					$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			$sql .= " GROUP BY d_month";
		}
		$rows=self::_runSQL($sql);
		if (!isset($rows)) {return 0;}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['count(*)'];
			}
			// Beware divide by zero
			if ($tot==0) $tot=1;
			$centuries = "";
			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			$counts=array();
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['count(*)'] / $tot, 0);
				$centuries .= $century.' - '.$values['count(*)'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_7_marr"]."\" title=\"".$pgv_lang["stat_7_marr"]."\" />";
		}
		return $rows;
	}

	function statsDiv($simple=true, $first=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact IN ('DIV', 'ANUL', '_SEPR')";
						if ($year1>=0 && $year2>=0) {
							$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
						}
					$sql .= " GROUP BY century ORDER BY century";
		} else if ($first) {
			$years = '';
			if ($year1>=0 && $year2>=0) {
				$years = " divorced.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
			}
			$sql=''
			.' SELECT'
				.' fam.f_id AS fams,'
				.' fam.f_husb, fam.f_wife,'
				.' divorced.d_julianday2 AS age,'
				.' divorced.d_month AS month,'
				.' indi.i_id AS indi'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS divorced ON divorced.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' divorced.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				." divorced.d_fact IN ('DIV', 'ANUL', '_SEPR') AND"
				.' divorced.d_julianday2 != 0 AND'
				.$years
				.' (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)'
			.' ORDER BY fams, indi, age ASC';
		} else {
			$sql = "SELECT d_month, COUNT(*) FROM {$TBLPREFIX}dates "
				."WHERE "
				."d_file={$this->_ged_id} AND "
				."d_fact IN ('DIV', 'ANUL', '_SEPR')";
				if ($year1>=0 && $year2>=0) {
					$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			$sql .= " GROUP BY d_month";
		}
		$rows=self::_runSQL($sql);
		if (!isset($rows)) {return 0;}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['count(*)'];
			}
			// Beware divide by zero
			if ($tot==0) $tot=1;
			$centuries = "";
			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			$counts=array();
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['count(*)'] / $tot, 0);
				$centuries .= $century.' - '.$values['count(*)'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_7_div"]."\" title=\"".$pgv_lang["stat_7_div"]."\" />";
		}
		return $rows;
	}

	//
	// Marriage
	//
	function firstMarriage() {return $this->_mortalityQuery('full', 'ASC', 'MARR');}
	function firstMarriageYear() {return $this->_mortalityQuery('year', 'ASC', 'MARR');}
	function firstMarriageName() {return $this->_mortalityQuery('name', 'ASC', 'MARR');}
	function firstMarriagePlace() {return $this->_mortalityQuery('place', 'ASC', 'MARR');}

	function lastMarriage() {return $this->_mortalityQuery('full', 'DESC', 'MARR');}
	function lastMarriageYear() {return $this->_mortalityQuery('year', 'DESC', 'MARR');}
	function lastMarriageName() {return $this->_mortalityQuery('name', 'DESC', 'MARR');}
	function lastMarriagePlace() {return $this->_mortalityQuery('place', 'DESC', 'MARR');}

	//
	// Divorce
	//
	function firstDivorce() {return $this->_mortalityQuery('full', 'ASC', 'DIV');}
	function firstDivorceYear() {return $this->_mortalityQuery('year', 'ASC', 'DIV');}
	function firstDivorceName() {return $this->_mortalityQuery('name', 'ASC', 'DIV');}
	function firstDivorcePlace() {return $this->_mortalityQuery('place', 'ASC', 'DIV');}

	function lastDivorce() {return $this->_mortalityQuery('full', 'DESC', 'DIV');}
	function lastDivorceYear() {return $this->_mortalityQuery('year', 'DESC', 'DIV');}
	function lastDivorceName() {return $this->_mortalityQuery('name', 'DESC', 'DIV');}
	function lastDivorcePlace() {return $this->_mortalityQuery('place', 'DESC', 'DIV');}

	function statsMarrAge($simple=true, $sex='M', $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE;

		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '200x250';}
			$sizes = explode('x', $size);
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25) AS age,'
					.' ROUND((married.d_year+49.1)/100) AS century,'
					.' indi.i_sex AS sex'
				.' FROM'
					." {$TBLPREFIX}families AS fam"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
				.' WHERE'
					.' birth.d_gid = indi.i_id AND'
					.' married.d_gid = fam.f_id AND'
					." (indi.i_id = fam.f_wife OR"
					." indi.i_id = fam.f_husb) AND"
					." fam.f_file = {$this->_ged_id} AND"
					." birth.d_fact = 'BIRT' AND"
					." married.d_fact = 'MARR' AND"
					.' birth.d_julianday1 != 0 AND'
					.' married.d_julianday2 > birth.d_julianday1'
				.' GROUP BY century, sex ORDER BY century, sex');
			$max = 0;
			foreach ($rows as $values) {
				if ($max<$values['age']) $max = $values['age'];
			}
			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			$chxl = "0:|";
			$chmm = "";
			$chmf = "";
			$i = 0;
			$male = true;
			$temp = "";
			$countsm = "";
			$countsf = "";
			$countsa = "";
			foreach ($rows as $values) {
				if ($max<=50) $chage = $values['age']*2;
				else $chage = $values['age'];
				if ($temp!=$values['century']) {
					$temp = $values['century'];
					if ($sizes[0]<1000) $sizes[0] += 50;
					if (function_exists($func)) {
						$century = $func($values['century'], false);
					} else {
						$century = $values['century'];
					}
					$chxl .= $century."|";
					if ($values['sex'] == "F") {
						if (!$male) {
							$countsm .= "0,";
							$chmm .= 't0,000000,0,'.($i-1).',11|';
							$countsa .= $fage.",";
						}
						$countsf .= $chage.",";
						$chmf .= 't'.$values['age'].',000000,1,'.$i.',11|';
						$fage = $chage;
						$male = false;
					} else if ($values['sex'] == "M") {
						$countsf .= "0,";
						$chmf .= 't0,000000,1,'.$i.',11|';
						$countsm .= $chage.",";
						$chmm .= 't'.$values['age'].',000000,0,'.$i.',11|';
						$countsa .= $chage.",";
					} else if ($values['sex'] == "U") {
						$countsf .= "0,";
						$chmf .= 't0,000000,1,'.$i.',11|';
						$countsm .= "0,";
						$chmm .= 't0,000000,0,'.$i.',11|';
						$countsa .= "0,";
					}
					$i++;
				}
				else if ($values['sex'] == "M") {
					$countsm .= $chage.",";
					$chmm .= 't'.$values['age'].',000000,0,'.($i-1).',11|';
					$countsa .= round(($fage+$chage)/2,1).",";
					$male = true;
				}
			}
			$countsm = substr($countsm,0,-1);
			$countsf = substr($countsf,0,-1);
			$countsa = substr($countsa,0,-1);
			$chmf = substr($chmf,0,-1);
			$chd = "t2:{$countsm}|{$countsf}|{$countsa}";
			if ($max<=50) $chxl .= "1:||".$pgv_lang["century"]."|2:|0|10|20|30|40|50|3:||".$pgv_lang["stat_age"]."|";
			else 	$chxl .= "1:||".$pgv_lang["century"]."|2:|0|10|20|30|40|50|60|70|80|90|100|3:||".$pgv_lang["stat_age"]."|";
			$chtt = $pgv_lang["stat_19_aarm"];
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|{$chmm}{$chmf}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt={$chtt}&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}&amp;chdl={$pgv_lang["male"]}|{$pgv_lang["female"]}|{$pgv_lang["avg_age"]}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_19_aarm"]."\" title=\"".$pgv_lang["stat_19_aarm"]."\" />";
		} else {
			$years = '';
			if ($year1>=0 && $year2>=0) {
				$years = " AND married.d_year BETWEEN '{$year1}' AND '{$year2}'";
			}
			if ($sex == 'F') {
				$sex_field = 'fam.f_wife,';
				$sex_field2 = " indi.i_id = fam.f_wife AND";
				$sex_search = " AND i_sex='F'";
			}
			else if ($sex == 'M') {
				$sex_field = 'fam.f_husb,';
				$sex_field2 = " indi.i_id = fam.f_husb AND";
				$sex_search = " AND i_sex='M'";
			}
			$rows=self::_runSQL(''
				.' SELECT'
					.' fam.f_id,'
					.$sex_field
					.' married.d_julianday2-birth.d_julianday1 AS age,'
					.' indi.i_id AS indi'
				.' FROM'
					." {$TBLPREFIX}families AS fam"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
				.' WHERE'
					.' birth.d_gid = indi.i_id AND'
					.' married.d_gid = fam.f_id AND'
					.$sex_field2
					." fam.f_file = {$this->_ged_id} AND"
					." birth.d_fact = 'BIRT' AND"
					." married.d_fact = 'MARR' AND"
					.' birth.d_julianday1 != 0 AND'
					.' married.d_julianday2 > birth.d_julianday1'
					.$sex_search
					.$years
				.' ORDER BY indi, age ASC');
			if (!isset($rows)) {return 0;}
			return $rows;
		}
	}

	//
	// Female only
	//
	function youngestMarriageFemale() {return $this->_marriageQuery('full', 'ASC', 'F');}
	function youngestMarriageFemaleName() {return $this->_marriageQuery('name', 'ASC', 'F');}
	function youngestMarriageFemaleAge() {return $this->_marriageQuery('age', 'ASC', 'F');}

	function oldestMarriageFemale() {return $this->_marriageQuery('full', 'DESC', 'F');}
	function oldestMarriageFemaleName() {return $this->_marriageQuery('name', 'DESC', 'F');}
	function oldestMarriageFemaleAge() {return $this->_marriageQuery('age', 'DESC', 'F');}

	//
	// Male only
	//
	function youngestMarriageMale() {return $this->_marriageQuery('full', 'ASC', 'M');}
	function youngestMarriageMaleName() {return $this->_marriageQuery('name', 'ASC', 'M');}
	function youngestMarriageMaleAge() {return $this->_marriageQuery('age', 'ASC', 'M');}

	function oldestMarriageMale() {return $this->_marriageQuery('full', 'DESC', 'M');}
	function oldestMarriageMaleName() {return $this->_marriageQuery('name', 'DESC', 'M');}
	function oldestMarriageMaleAge() {return $this->_marriageQuery('age', 'DESC', 'M');}

	function ageBetweenSpousesMF($params=null) {return $this->_ageBetweenSpousesQuery($type='nolist', $age_dir='DESC', $params=null);}
	function ageBetweenSpousesMFList($params=null) {return $this->_ageBetweenSpousesQuery($type='list', $age_dir='DESC', $params=null);}

	function ageBetweenSpousesFM($params=null) {return $this->_ageBetweenSpousesQuery($type='nolist', $age_dir='ASC', $params=null);}
	function ageBetweenSpousesFMList($params=null) {return $this->_ageBetweenSpousesQuery($type='list', $age_dir='ASC', $params=null);}

	function topAgeOfMarriageFamily() {return $this->_ageOfMarriageQuery('name', 'DESC', array('1'));}
	function topAgeOfMarriage() {return $this->_ageOfMarriageQuery('age', 'DESC', array('1'));}
	function topAgeOfMarriageFamilies($params=null) {return $this->_ageOfMarriageQuery('nolist', 'DESC', $params);}
	function topAgeOfMarriageFamiliesList($params=null) {return $this->_ageOfMarriageQuery('list', 'DESC', $params);}

	function minAgeOfMarriageFamily() {return $this->_ageOfMarriageQuery('name', 'ASC', array('1'));}
	function minAgeOfMarriage() {return $this->_ageOfMarriageQuery('age', 'ASC', array('1'));}
	function minAgeOfMarriageFamilies($params=null) {return $this->_ageOfMarriageQuery('nolist', 'ASC', $params);}
	function minAgeOfMarriageFamiliesList($params=null) {return $this->_ageOfMarriageQuery('list', 'ASC', $params);}

	//
	// Mother only
	//
	function youngestMother() {return $this->_parentsQuery('full', 'ASC', 'F');}
	function youngestMotherName() {return $this->_parentsQuery('name', 'ASC', 'F');}
	function youngestMotherAge() {return $this->_parentsQuery('age', 'ASC', 'F');}

	function oldestMother() {return $this->_parentsQuery('full', 'DESC', 'F');}
	function oldestMotherName() {return $this->_parentsQuery('name', 'DESC', 'F');}
	function oldestMotherAge() {return $this->_parentsQuery('age', 'DESC', 'F');}

	//
	// Father only
	//
	function youngestFather() {return $this->_parentsQuery('full', 'ASC', 'M');}
	function youngestFatherName() {return $this->_parentsQuery('name', 'ASC', 'M');}
	function youngestFatherAge() {return $this->_parentsQuery('age', 'ASC', 'M');}

	function oldestFather() {return $this->_parentsQuery('full', 'DESC', 'M');}
	function oldestFatherName() {return $this->_parentsQuery('name', 'DESC', 'M');}
	function oldestFatherAge() {return $this->_parentsQuery('age', 'DESC', 'M');}

///////////////////////////////////////////////////////////////////////////////
// Family Size                                                               //
///////////////////////////////////////////////////////////////////////////////

	function _familyQuery($type='full') {
		global $TBLPREFIX, $pgv_lang;
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC'
		, 1);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$family=Family::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($family->canDisplayDetails()) {
					$result=$family->format_list('span', false, $family->getFullName());
				} else {
					$result = $pgv_lang['privacy_error'];
				}
				break;
			case 'size':
				$result=$row['tot'];
				break;
			case 'name':
				$result="<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()).'</a>';
				break;
		}
		// Statistics are used by RSS feeds, etc., so need absolute URLs.
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenFamilyQuery($type='list', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		if(count($rows) < $total){$total = count($rows);}
		$top10 = array();
		for($c = 0; $c < $total; $c++) {
			$family=Family::getInstance($rows[$c]['id']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [{$rows[$c]['tot']} {$pgv_lang['lchildren']}]</li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [{$rows[$c]['tot']} {$pgv_lang['lchildren']}]";
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _ageBetweenSiblingsQuery($type='list', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang, $lang_short_cut, $LANGUAGE;
		if ($params === null) {$params = array();}
		if (isset($params[0])) {$total = $params[0];}else{$total = 10;}
		if (isset($params[1])) {$one = $params[1];}else{$one = false;} // each family only once if true
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' link1.l_from AS family,'
				.' link1.l_to AS ch1,'
				.' link2.l_to AS ch2,'
				.' child1.d_julianday2-child2.d_julianday2 AS age'
			.' FROM'
				." {$TBLPREFIX}link AS link1"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS child1 ON child1.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS child2 ON child2.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}link AS link2 ON link2.l_file = {$this->_ged_id}"
			.' WHERE'
				." link1.l_file = {$this->_ged_id} AND"
				.' link1.l_from = link2.l_from AND'
				." link1.l_type = 'CHIL' AND"
				.' child1.d_gid = link1.l_to AND'
				." child1.d_fact = 'BIRT' AND"
				." link2.l_type = 'CHIL' AND"
				.' child2.d_gid = link2.l_to AND'
				." child2.d_fact = 'BIRT' AND"
				.' child1.d_julianday2 > child2.d_julianday2 AND'
				.' child2.d_julianday2 != 0 AND'
				.' child1.d_gid != child2.d_gid'
			.' ORDER BY'
				." age DESC"
		,$total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		$func = "age_localisation_{$lang_short_cut[$LANGUAGE]}";
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		if ($one) $dist = array();
		foreach ($rows as $fam) {
			$family = Family::getInstance($fam['family']);
			$child1 = Person::getInstance($fam['ch1']);
			$child2 = Person::getInstance($fam['ch2']);
			if ($type == 'name') {
				if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = "<a href=\"".encode_url($child2->getLinkUrl())."\">".PrintReady($child2->getFullName())."</a> ";
					$return .= $pgv_lang["and"]." ";
					$return .= "<a href=\"".encode_url($child1->getLinkUrl())."\">".PrintReady($child1->getFullName())."</a>";
					$return .= " <a href=\"family.php?famid=".$fam['family']."\">[".$pgv_lang["view_family"]."]</a>\n";
				} else {
					$return = $pgv_lang['privacy_error'];
				}
				return $return;
			}
			$age = $fam['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'age') {
				return $age;
			}
			if ($type == 'list') {
				if ($one && !in_array($fam['family'], $dist)) {
					if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
						$return = "\t<li>";
						$return .= "<a href=\"".encode_url($child2->getLinkUrl())."\">".PrintReady($child2->getFullName())."</a> ";
						$return .= $pgv_lang["and"]." ";
						$return .= "<a href=\"".encode_url($child1->getLinkUrl())."\">".PrintReady($child1->getFullName())."</a>";
						$return .= " [".$age."]";
						$return .= " <a href=\"family.php?famid=".$fam['family']."\">[".$pgv_lang["view_family"]."]</a>";
						$return .= "\t</li>\n";
						$top10[] = $return;
						$dist[] = $fam['family'];
					}
				} else if (!$one && $child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = "\t<li>";
					$return .= "<a href=\"".encode_url($child2->getLinkUrl())."\">".PrintReady($child2->getFullName())."</a> ";
					$return .= $pgv_lang["and"]." ";
					$return .= "<a href=\"".encode_url($child1->getLinkUrl())."\">".PrintReady($child1->getFullName())."</a>";
					$return .= " [".$age."]";
					$return .= " <a href=\"family.php?famid=".$fam['family']."\">[".$pgv_lang["view_family"]."]</a>";
					$return .= "\t</li>\n";
					$top10[] = $return;
				}
			} else {
				if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = $child2->format_list('span', false, $child2->getFullName());
					$return .= "<br />".$pgv_lang["and"]."<br />";
					$return .= $child1->format_list('span', false, $child1->getFullName());
					//$return .= "<br />[".$age."]";
					$return .= "<br /><a href=\"family.php?famid=".$fam['family']."\">[".$pgv_lang["view_family"]."]</a>\n";
					return $return;
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function largestFamily() {return $this->_familyQuery('full');}
	function largestFamilySize() {return $this->_familyQuery('size');}
	function largestFamilyName() {return $this->_familyQuery('name');}

	function topTenLargestFamily($params=null) {return $this->_topTenFamilyQuery('nolist', $params);}
	function topTenLargestFamilyList($params=null) {return $this->_topTenFamilyQuery('list', $params);}

	function chartLargestFamilies($params=null) {
		global $TBLPREFIX, $pgv_lang, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_L_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_L_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$total = strtolower($params[3]);}else{$total = 10;}
		$sizes = explode('x', $size);
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		$tot = 0; 
		foreach ($rows as $row) {$tot += $row['tot'];}
		$chd = '';
		$chl = array();
		foreach ($rows as $row){
			$family=Family::getInstance($row['id']);
			if ($family->canDisplayDetails()) {
				if ($tot==0) {
					$per = 0;
				} else {
					$per = round(100 * $row['tot'] / $tot, 0);
				}
				$chd .= self::_array_to_extended_encoding(array($per));
				$chl[] = strip_tags(unhtmlentities($family->getFullName())).' - '.$row['tot'];
			}
		}
		$chl = join('|', $chl);

		// the following does not print Arabic letters in names - encode_url shows still the letters
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_largest_families"]."\" title=\"".$pgv_lang["stat_largest_families"]."\" />";
	}

	function totalChildren() {
		global $TBLPREFIX, $gBitDb;

		return
			$gBitDb->getOne(
				"SELECT SUM(f_numchil) FROM {$TBLPREFIX}families WHERE f_file={$this->_ged_id}"
				, array($this->_ged_id));
	}


	function averageChildren() {
		global $TBLPREFIX;
		$rows=self::_runSQL("SELECT AVG(f_numchil) AS tot FROM {$TBLPREFIX}families WHERE f_file={$this->_ged_id}");
		$row=$rows[0];
		return sprintf('%.2f', $row['tot']);
	}

	function statsChildren($simple=true, $sex='BOTH', $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE;

		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '220x200';}
			$sizes = explode('x', $size);
			$max = 0;
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(f_numchil),2) AS num,'
					.' ROUND((married.d_year+49.1)/100) AS century'
				.' FROM'
					." {$TBLPREFIX}families AS fam"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
				.' WHERE'
					.' married.d_gid = fam.f_id AND'
					." fam.f_file = {$this->_ged_id} AND"
					." married.d_fact = 'MARR'"
				.' GROUP BY century ORDER BY century');
			foreach ($rows as $values) {
				if ($max<$values['num']) $max = $values['num'];
			}
			$chm = "";
			$chxl = "0:|";
			$i = 0;
			$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
			$counts=array();
			foreach ($rows as $values) {
				if ($sizes[0]<980) $sizes[0] += 38;
				if (function_exists($func)) {
					$chxl .= $func($values['century'], false)."|";
				}
				else {
					$chxl .= $values['century']."|";
				}
				if ($max<=5) $counts[] = round($values['num']*819.2-1, 1);
				else $counts[] = round($values['num']*409.6, 1);
				$chm .= 't'.$values['num'].',000000,0,'.$i.',11|';
				$i++;
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chm = substr($chm,0,-1);
			if ($max<=5) $chxl .= "1:||".$pgv_lang["century"]."|2:|0|1|2|3|4|5|3:||".$pgv_lang["stat_21_nok"]."|";
			else $chxl .= "1:||".$pgv_lang["century"]."|2:|0|1|2|3|4|5|6|7|8|9|10|3:||".$pgv_lang["stat_21_nok"]."|";
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0,3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_average_children"]."\" title=\"".$pgv_lang["stat_average_children"]."\" />";
		} else {
			if ($sex=='M') {
				$sql = "SELECT num, COUNT(*) FROM "
						."(SELECT count(i_sex) AS num FROM {$TBLPREFIX}link "
							."LEFT OUTER JOIN {$TBLPREFIX}individuals "
							."ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' "
							."JOIN {$TBLPREFIX}families ON f_file=l_file AND f_id=l_to WHERE f_file={$this->_ged_id} GROUP BY l_to"
						.") boys"
						." GROUP BY num ORDER BY num ASC";
			}
			else if ($sex=='F') {
				$sql = "SELECT num, COUNT(*) FROM "
						."(SELECT count(i_sex) AS num FROM {$TBLPREFIX}link "
							."LEFT OUTER JOIN {$TBLPREFIX}individuals "
							."ON l_from=i_id AND l_file=i_file AND i_sex='F' AND l_type='FAMC' "
							."JOIN {$TBLPREFIX}families ON f_file=l_file AND f_id=l_to WHERE f_file={$this->_ged_id} GROUP BY l_to"
						.") girls"
						." GROUP BY num ORDER BY num ASC";
			}
			else {
				$sql = "SELECT f_numchil, COUNT(*) FROM {$TBLPREFIX}families ";
				if ($year1>=0 && $year2>=0) {
					$sql .= "AS fam LEFT JOIN {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
						.' WHERE'
						.' married.d_gid = fam.f_id AND'
						." fam.f_file = {$this->_ged_id} AND"
						." married.d_fact = 'MARR' AND"
						." married.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
				else {
					$sql .='WHERE '
						."f_file={$this->_ged_id}";
				}
				$sql .= ' GROUP BY f_numchil';
			}
			$rows=self::_runSQL($sql);
			if (!isset($rows)) {return 0;}
			return $rows;
		}
	}

	function topAgeBetweenSiblingsName($params=null) {return $this->_ageBetweenSiblingsQuery($type='name', $params=null);}
	function topAgeBetweenSiblings($params=null) {return $this->_ageBetweenSiblingsQuery($type='age', $params=null);}
	function topAgeBetweenSiblingsFullName($params=null) {return $this->_ageBetweenSiblingsQuery($type='nolist', $params=null);}
	function topAgeBetweenSiblingsList($params=null) {return $this->_ageBetweenSiblingsQuery($type='list', $params=null);}

	function noChildrenFamilies() {
		global $TBLPREFIX;
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS tot'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' WHERE'
				.' f_numchil = 0 AND'
				." fam.f_file = {$this->_ged_id}");
		$row=$rows[0];
		return $row['tot'];
	}

	function chartNoChildrenFamilies($year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $pgv_lang, $lang_short_cut, $LANGUAGE;

		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '220x200';}
		$sizes = explode('x', $size);
		if ($year1>=0 && $year2>=0) {
			$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
		} else {
			$years = "";
		}
		$max = 0;
		$tot = 0;
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*),'
				.' ROUND((married.d_year+49.1)/100) AS century'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' WHERE'
				.' f_numchil = 0 AND'
				.' married.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				.$years
				." married.d_fact = 'MARR'"
			.' GROUP BY century ORDER BY century');
		foreach ($rows as $values) {
			if ($max<$values['count']) $max = $values['count'];
			$tot += $values['count'];
		}
		$unknown = $this->noChildrenFamilies()-$tot;
		if ($unknown>$max) $max=$unknown;
		$chm = "";
		$chxl = "0:|";
		$i = 0;
		$func="century_localisation_{$lang_short_cut[$LANGUAGE]}";
		foreach ($rows as $values) {
			if ($sizes[0]<980) $sizes[0] += 38;
			if (function_exists($func)) {
				$chxl .= $func($values['century'], false)."|";
			}
			else {
				$chxl .= $values['century']."|";
			}
			$counts[] = round(4095*$values['count']/($max+1));
			$chm .= 't'.$values['count'].',000000,0,'.$i.',11|';
			$i++;
		}
		$counts[] = round(4095*$unknown/($max+1));
		$chd = self::_array_to_extended_encoding($counts);
		$chm .= 't'.$unknown.',000000,0,'.$i.',11';
		$chxl .= $pgv_lang["no_date_fam"]."|1:||".$pgv_lang["century"]."|2:|0|";
		$step = $max+1;
		for ($d=floor($max+1); $d>0; $d--) if (($max+1)<($d*10+1) && fmod(($max+1),$d)==0) $step = $d;
		if ($step==floor($max+1)) for ($d=floor($max); $d>0; $d--) if ($max<($d*10+1) && fmod($max,$d)==0) $step = $d;
		for ($n=$step; $n<=($max+1); $n+=$step) $chxl .= $n."|";
		$chxl .= "3:||".$pgv_lang["statnfam"]."|";
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:".($i-1).",3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$pgv_lang["stat_22_fwok"]."\" title=\"".$pgv_lang["stat_22_fwok"]."\" />";
	}

	function _topTenGrandFamilyQuery($type='list', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION, $pgv_lang;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' JOIN'
				." {$TBLPREFIX}link AS children ON children.l_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}link AS mchildren ON mchildren.l_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}link AS gchildren ON gchildren.l_file = {$this->_ged_id}"
			.' WHERE'
				." f_file={$this->_ged_id} AND"
				." children.l_from=f_id AND"
				." children.l_type='CHIL' AND"
				." children.l_to=mchildren.l_from AND"
				." mchildren.l_type='FAMS' AND"
				." mchildren.l_to=gchildren.l_from AND"
				." gchildren.l_type='CHIL'"
			.' GROUP BY'
				.' id'
			.' ORDER BY'
				.' tot DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $row) {
			$family=Family::getInstance($row['id']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [{$row['tot']} {$pgv_lang['grandchildren']}]</li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a> [{$row['tot']} {$pgv_lang['grandchildren']}]";
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function topTenLargestGrandFamily($params=null) {return $this->_topTenGrandFamilyQuery('nolist', $params);}
	function topTenLargestGrandFamilyList($params=null) {return $this->_topTenGrandFamilyQuery('list', $params);}

///////////////////////////////////////////////////////////////////////////////
// Surnames                                                                  //
///////////////////////////////////////////////////////////////////////////////

	static function _commonSurnamesQuery($type='list', $show_tot=false, $params=null) {
		global $TEXT_DIRECTION, $COMMON_NAMES_THRESHOLD, $SURNAME_LIST_STYLE;

		if (is_array($params) && isset($params[0]) && $params[0] != '') {$threshold = strtolower($params[0]);}else{$threshold = $COMMON_NAMES_THRESHOLD;}
		if(is_array($params) && isset($params[1]) && $params[1] != '' && $params[1] >= 0){$maxtoshow = strtolower($params[1]);}else{$maxtoshow = false;}
		if(is_array($params) && isset($params[2]) && $params[2] != ''){$sorting = strtolower($params[2]);}else{$sorting = 'alpha';}
		$surname_list = get_common_surnames($threshold);
		if (count($surname_list) == 0) return '';
		uasort($surname_list, array('stats', '_name_total_rsort'));
		if ($maxtoshow>0) $surname_list = array_slice($surname_list, 0, $maxtoshow);

		switch($sorting) {
			default:
			case 'alpha':
				uasort($surname_list, array('stats', '_name_name_sort'));
				break;
			case 'ralpha':
				uasort($surname_list, array('stats', '_name_name_rsort'));
				break;
			case 'count':
				uasort($surname_list, array('stats', '_name_total_sort'));
				break;
			case 'rcount':
				uasort($surname_list, array('stats', '_name_total_rsort'));
				break;
		}

		// Note that we count/display SPFX SURN, but sort/group under just SURN
		$surnames=array();
		foreach (array_keys($surname_list) as $surname) {
			$surnames=array_merge($surnames, get_indilist_surns($surname, '', false, false, PGV_GED_ID));
		}

		return format_surname_list($surnames, ($type=='list' ? 1 : 2), $show_tot);
	}

	function getCommonSurname($show_tot=false) {
		if ($show_tot) {
			return get_top_surnames(0);
		}
		else {
			foreach (array_keys(get_top_surnames(0)) as $surname) {
				return $surname;
			}
		}
	}

	static function commonSurnames($params=array('','','alpha')) {return self::_commonSurnamesQuery('nolist', false, $params);}
	static function commonSurnamesTotals($params=array('','','rcount')) {return self::_commonSurnamesQuery('nolist', true, $params);}
	static function commonSurnamesList($params=array('','','alpha')) {return self::_commonSurnamesQuery('list', false, $params);}
	static function commonSurnamesListTotals($params=array('','','rcount')) {return self::_commonSurnamesQuery('list', true, $params);}

	function chartCommonSurnames($params=null) {
		global $pgv_lang, $COMMON_NAMES_THRESHOLD, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$threshold = strtolower($params[3]);}else{$threshold = $COMMON_NAMES_THRESHOLD;}
		if (isset($params[4]) && $params[4] != '') {$maxtoshow = strtolower($params[4]);}else{$maxtoshow = 7;}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividuals();
		$surnames = get_common_surnames($threshold);
		uasort($surnames, array('stats', '_name_total_rsort'));
		$surnames = array_slice($surnames, 0, $maxtoshow);
		$all_surnames = array();
		foreach (array_keys($surnames) as $n=>$surname) {
			if ($n>=$maxtoshow) {
				break;
			}
			$all_surnames = array_merge($all_surnames, get_indilist_surns(UTF8_strtoupper($surname), '', false, false, PGV_GED_ID));
		}
		if (count($surnames) <= 0) {return '';}
		$tot = 0;
		foreach ($surnames as $indexval=>$surname) {$tot += $surname['match'];}
		$chart_title = "";
		$chd = '';
		$chl = array();
		foreach ($all_surnames as $surn=>$surns) {
			foreach ($surns as $spfxsurn=>$indis) {
				if ($tot==0) {
					$per = 0;
				} else {
					$per = round(100 * count($indis) / $tot_indi, 0);
				}
				$chd .= self::_array_to_extended_encoding($per);
				$chl[] = $spfxsurn.' - '.count($indis);
				$chart_title .= $spfxsurn.' ['.count($indis).'], ';
			}
		}
		$per = round(100 * ($tot_indi-$tot) / $tot_indi, 0);
		$chd .= self::_array_to_extended_encoding($per);
		$chl[] = $pgv_lang["other"].' - '.($tot_indi-$tot);
		$chart_title .= $pgv_lang["other"].' ['.($tot_indi-$tot).']';
		$chl = join('|', $chl);
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}


///////////////////////////////////////////////////////////////////////////////
// Given Names                                                               //
///////////////////////////////////////////////////////////////////////////////

	/*
	* [ 1977282 ] Most Common Given Names Block
	* Original block created by kiwi_pgv
	*/
	static function _commonGivenQuery($sex='B', $type='list', $show_tot=false, $params=null) {
		global $TEXT_DIRECTION, $GEDCOM, $TBLPREFIX, $pgv_lang, $gBitDb;
		static $sort_types = array('count'=>'asort', 'rcount'=>'arsort', 'alpha'=>'ksort', 'ralpha'=>'krsort');
		static $sort_flags = array('count'=>SORT_NUMERIC, 'rcount'=>SORT_NUMERIC, 'alpha'=>SORT_STRING, 'ralpha'=>SORT_STRING);

		if(is_array($params) && isset($params[0]) && $params[0] != '' && $params[0] >= 0){$threshold = strtolower($params[0]);}else{$threshold = 1;}
		if(is_array($params) && isset($params[1]) && $params[1] != '' && $params[1] >= 0){$maxtoshow = strtolower($params[1]);}else{$maxtoshow = 10;}
		if(is_array($params) && isset($params[2]) && $params[2] != '' && isset($sort_types[strtolower($params[2])])){$sorting = strtolower($params[2]);}else{$sorting = 'rcount';}

		switch ($sex) {
		case 'M':
			$sex_sql="i_sex='M'";
			break;
		case 'F':
			$sex_sql="i_sex='F'";
			break;
		case 'U':
			$sex_sql="i_sex='U'";
			break;
		case 'B':
			$sex_sql="i_sex!='U'";
			break;
		}
		$ged_id=get_id_from_gedcom($GEDCOM);

		$result = $gBitDb->query("SELECT n_givn, COUNT(*) AS num FROM {$TBLPREFIX}name JOIN {$TBLPREFIX}individuals ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type!='_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn");
		$nameList=array();
		while ( $row = $result->fetchRow ) {
			// Split "John Thomas" into "John" and "Thomas" and count against both totals
			foreach (explode(' ', $row[n_givn]) as $given) {
				$given=str_replace(array('*', '"'), '', $given);
				if (strlen($given)>1) {
					if (array_key_exists($given, $nameList)) {
						$nameList[$given]+=$row[num];
					} else {
						$nameList[$given]=$row[num];
					}
				}
			}
		}
		arsort($nameList, SORT_NUMERIC);
		$nameList=array_slice($nameList, 0, $maxtoshow);

		if (count($nameList)==0) return '';
		if ($type=='chart') return $nameList;
		$common = array();
		foreach ($nameList as $given=>$total) {
			if ($maxtoshow !== -1) {if($maxtoshow-- <= 0){break;}}
			if ($total < $threshold) {break;}
			if ($show_tot) {
				$tot = PrintReady("[{$total}]");
				if ($TEXT_DIRECTION=='ltr') {
					$totL = '';
					$totR = '&nbsp;'.$tot;
				} else {
					$totL = $tot.'&nbsp;';
					$totR = '';
				}
			} else {
				$totL = '';
				$totR = '';
			}
			switch ($type) {
			case 'table':
				$common[] = '<tr><td class="optionbox">'.PrintReady(UTF8_substr($given,0,1).UTF8_strtolower(UTF8_substr($given,1))).'</td><td class="optionbox">'.$total.'</td></tr>';
				break;
			case 'list':
				$common[] = "\t<li>{$totL}".PrintReady(UTF8_substr($given,0,1).UTF8_strtolower(UTF8_substr($given,1)))."{$totR}</li>\n";
				break;
			case 'nolist':
				$common[] = $totL.PrintReady(UTF8_substr($given,0,1).UTF8_strtolower(UTF8_substr($given,1))).$totR;
				break;
			}
		}
		if ($common) {
			switch ($type) {
			case 'table':
				$lookup=array('M'=>$pgv_lang['male'], 'F'=>$pgv_lang['female'], 'U'=>$pgv_lang['unknown'], 'B'=>$pgv_lang['all']);
				return '<table><tr><td colspan="2" class="descriptionbox center">'.$lookup[$sex].'</td></tr><tr><td class="descriptionbox center">'.$pgv_lang['names'].'</td><td class="descriptionbox center">'.$pgv_lang['count'].'</td></tr>'.join('', $common).'</table>';
			case 'list':
				return "<ul>\n".join("\n", $common)."</ul>\n";
			case 'nolist':
				return join(';&nbsp; ', $common);
			}
		} else {
			return '';
		}
	}

	static function commonGiven($params=array(1,10,'alpha')){return self::_commonGivenQuery('B', 'nolist', false, $params);}
	static function commonGivenTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('B', 'nolist', true, $params);}
	static function commonGivenList($params=array(1,10,'alpha')){return self::_commonGivenQuery('B', 'list', false, $params);}
	static function commonGivenListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('B', 'list', true, $params);}
	static function commonGivenTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('B', 'table', false, $params);}

	static function commonGivenFemale($params=array(1,10,'alpha')){return self::_commonGivenQuery('F', 'nolist', false, $params);}
	static function commonGivenFemaleTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('F', 'nolist', true, $params);}
	static function commonGivenFemaleList($params=array(1,10,'alpha')){return self::_commonGivenQuery('F', 'list', false, $params);}
	static function commonGivenFemaleListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('F', 'list', true, $params);}
	static function commonGivenFemaleTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('F', 'table', false, $params);}

	static function commonGivenMale($params=array(1,10,'alpha')){return self::_commonGivenQuery('M', 'nolist', false, $params);}
	static function commonGivenMaleTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('M', 'nolist', true, $params);}
	static function commonGivenMaleList($params=array(1,10,'alpha')){return self::_commonGivenQuery('M', 'list', false, $params);}
	static function commonGivenMaleListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('M', 'list', true, $params);}
	static function commonGivenMaleTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('M', 'table', false, $params);}

	static function commonGivenUnknown($params=array(1,10,'alpha')){return self::_commonGivenQuery('U', 'nolist', false, $params);}
	static function commonGivenUnknownTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('U', 'nolist', true, $params);}
	static function commonGivenUnknownList($params=array(1,10,'alpha')){return self::_commonGivenQuery('U', 'list', false, $params);}
	static function commonGivenUnknownListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('U', 'list', true, $params);}
	static function commonGivenUnknownTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('U', 'table', false, $params);}

	function chartCommonGiven($params=null) {
		global $pgv_lang, $COMMON_NAMES_THRESHOLD, $PGV_STATS_CHART_COLOR1, $PGV_STATS_CHART_COLOR2, $PGV_STATS_S_CHART_X, $PGV_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $PGV_STATS_S_CHART_X."x".$PGV_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $PGV_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $PGV_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$threshold = strtolower($params[3]);}else{$threshold = $COMMON_NAMES_THRESHOLD;}
		if (isset($params[4]) && $params[4] != '') {$maxtoshow = strtolower($params[4]);}else{$maxtoshow = 7;}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividuals();
		$given = self::_commonGivenQuery('B', 'chart', true);
		$given = array_slice($given, 0, $maxtoshow);
		if (count($given) <= 0) {return '';}
		$tot = 0;
		foreach ($given as $givn=>$count) {$tot += $count;}
		$chart_title = "";
		$chd = '';
		$chl = array();
		foreach ($given as $givn=>$count) {
			if ($tot==0) {
				$per = 0;
			} else {
				$per = round(100 * $count / $tot_indi, 0);
			}
			$chd .= self::_array_to_extended_encoding($per);
			$chl[] = $givn.' - '.$count;
			$chart_title .= $givn.' ['.$count.'], ';
		}
		$per = round(100 * ($tot_indi-$tot) / $tot_indi, 0);
		$chd .= self::_array_to_extended_encoding($per);
		$chl[] = $pgv_lang["other"].' - '.($tot_indi-$tot);
		$chart_title .= $pgv_lang["other"].' ['.($tot_indi-$tot).']';
		$chl = join('|', $chl);
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

///////////////////////////////////////////////////////////////////////////////
// Users                                                                     //
///////////////////////////////////////////////////////////////////////////////

	static function _usersLoggedIn($type='nolist') {
		global $PGV_SESSION_TIME, $pgv_lang;
		// Log out inactive users
		foreach (get_idle_users(time() - $PGV_SESSION_TIME) as $user_id=>$user_name) {
			if ($user_id != PGV_USER_ID) {
				userLogout($user_id);
			}
		}

		$content = '';
		// List active users
		$NumAnonymous = 0;
		$loggedusers = array ();
		$x = get_logged_in_users();
		foreach ($x as $user_id=>$user_name) {
			if (PGV_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline') == 'Y') {
				$loggedusers[$user_id] = $user_name;
			} else {
				$NumAnonymous++;
			}
		}
		$LoginUsers = count($loggedusers);
		if (($LoginUsers == 0) and ($NumAnonymous == 0)) {
			return $pgv_lang['no_login_users'];
		}
		$Advisory = 'anon_user';
		if ($NumAnonymous > 1) {$Advisory .= 's';}
		if ($NumAnonymous > 0) {
			$pgv_lang['global_num1'] = $NumAnonymous; // Make it visible
			$content .= '<b>'.print_text($Advisory, 0, 1).'</b>';
		}
		$Advisory = 'login_user';
		if ($LoginUsers > 1) {
			$Advisory .= 's';
		}
		if ($LoginUsers > 0) {
			$pgv_lang['global_num1'] = $LoginUsers; // Make it visible
			if ($NumAnonymous) {
				if ($type == 'list') {
					$content .= "<br /><br />\n";
				} else {
					$content .= " {$pgv_lang['and']} ";
				}
			}
			if ($type == 'list') {
				$content .= '<b>'.print_text($Advisory, 0, 1)."</b>\n<ul>\n";
			} else {
				$content .= '<b>'.print_text($Advisory, 0, 1)."</b>: ";
			}
		}
		if (PGV_USER_ID) {
			foreach ($loggedusers as $user_id=>$user_name) {
				if ($type == 'list') {
					$content .= "\t<li>".PrintReady(getUserFullName($user_id))." - {$user_name}";
				} else {
					$content .= PrintReady(getUserFullName($user_id))." - {$user_name}";
				}
				if (PGV_USER_ID != $user_id && get_user_setting($user_id, 'contactmethod') != 'none') {
					if ($type == 'list') {
						$content .= "<br /><a href=\"javascript:;\" onclick=\"return message('{$user_id}');\">{$pgv_lang['message']}</a>";
					} else {
						$content .= " <a href=\"javascript:;\" onclick=\"return message('{$user_id}');\">{$pgv_lang['message']}</a>";
					}
				}
				if ($type == 'list') {
					$content .= "</li>\n";
				}
			}
		}
		if ($type == 'list') {
			$content .= '</ul>';
		}
		return $content;
	}

	static function _usersLoggedInTotal($type='all') {
		global $PGV_SESSION_TIME;

		foreach (get_idle_users(time() - $PGV_SESSION_TIME) as $user_id=>$user_name) {
			if ($user_id != PGV_USER_ID) {
				userLogout($user_id);
			}
		}
		$anon = 0;
		$visible = 0;
		$x = get_logged_in_users();
		foreach ($x as $user_id=>$user_name) {
			if (PGV_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline') == 'Y') {$visible++;}else{$anon++;}
		}
		if ($type == 'anon') {return $anon;}
		elseif ($type == 'visible') {return $visible;}
		else{return $visible + $anon;}
	}

	static function usersLoggedIn() {return self::_usersLoggedIn('nolist');}
	static function usersLoggedInList() {return self::_usersLoggedIn('list');}

	static function usersLoggedInTotal() {return self::_usersLoggedInTotal('all');}
	static function usersLoggedInTotalAnon() {return self::_usersLoggedInTotal('anon');}
	static function usersLoggedInTotalVisible() {return self::_usersLoggedInTotal('visible');}

	static function userID() {return getUserId();}
	static function userName() {return getUserName();}
	static function userFullName() {return getUserFullName(getUserId());}
	static function userFirstName() {return get_user_setting(getUserId(), 'firstname');}
	static function userLastName() {return get_user_setting(getUserId(), 'lastname');}

	static function _getLatestUserData($type='userid', $params=null) {
		global $DATE_FORMAT, $TIME_FORMAT, $pgv_lang;
		static $user = null;

		if($user === null) {
			$users = get_all_users('DESC', 'reg_timestamp', 'username');
			$user = array_shift($users);
			unset($users);
		}

		switch($type) {
			default:
			case 'userid':
				return $user;
			case 'username':
				return get_user_name($user);
			case 'fullname':
				return getUserFullName($user);
			case 'firstname':
				return get_user_setting($user, 'firstname');
			case 'lastname':
				return get_user_setting($user, 'lastname');
			case 'regdate':
				if(is_array($params) && isset($params[0]) && $params[0] != ''){$datestamp = $params[0];}else{$datestamp = $DATE_FORMAT;}
				return date($datestamp, get_user_setting($user, 'reg_timestamp'));
			case 'regtime':
				if(is_array($params) && isset($params[0]) && $params[0] != ''){$datestamp = $params[0];}else{$datestamp = $TIME_FORMAT;}
				return date($datestamp, get_user_setting($user, 'reg_timestamp'));
			case 'loggedin':
				if(is_array($params) && isset($params[0]) && $params[0] != ''){$yes = $params[0];}else{$yes = $pgv_lang['yes'];}
				if(is_array($params) && isset($params[1]) && $params[1] != ''){$no = $params[1];}else{$no = $pgv_lang['no'];}
				return (get_user_setting($user, 'loggedin') == 'Y')?$yes:$no;
		}
	}

	static function latestUserId(){return self::_getLatestUserData('userid');}
	static function latestUserName(){return self::_getLatestUserData('username');}
	static function latestUserFullName(){return self::_getLatestUserData('fullname');}
	static function latestUserFirstName(){return self::_getLatestUserData('firstname');}
	static function latestUserLastName(){return self::_getLatestUserData('lastname');}
	static function latestUserRegDate($params=null){return self::_getLatestUserData('regdate', $params);}
	static function latestUserRegTime($params=null){return self::_getLatestUserData('regtime', $params);}
	static function latestUserLoggedin($params=null){return self::_getLatestUserData('loggedin', $params);}

///////////////////////////////////////////////////////////////////////////////
// Contact                                                                   //
///////////////////////////////////////////////////////////////////////////////

	static function contactWebmaster() {return user_contact_link($GLOBALS['WEBMASTER_EMAIL'], $GLOBALS['SUPPORT_METHOD']);}
	static function contactGedcom() {return user_contact_link($GLOBALS['CONTACT_EMAIL'], $GLOBALS['CONTACT_METHOD']);}

///////////////////////////////////////////////////////////////////////////////
// Date & Time                                                               //
///////////////////////////////////////////////////////////////////////////////

	static function serverDate() {return timestamp_to_gedcom_date(time())->Display(false);}

	static function serverTime() {return date('g:i a');}

	static function serverTime24() {return date('G:i');}

	static function serverTimezone() {return date('T');}

	static function browserDate() {return timestamp_to_gedcom_date(client_time())->Display(false);}

	static function browserTime() {return date('g:i a', client_time());}

	static function browserTime24() {return date('G:i', client_time());}

	static function browserTimezone() {return date('T', client_time());}

///////////////////////////////////////////////////////////////////////////////
// Tools                                                                     //
///////////////////////////////////////////////////////////////////////////////

	/*
	* Leave for backwards compatability? Anybody using this?
	*/
	static function _getEventType($type) {
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

	// http://bendodson.com/news/google-extended-encoding-made-easy/
	static function _array_to_extended_encoding($a) {
		if (!is_array($a)) {$a = array($a);}
		$encoding = '';
		foreach ($a as $value) {
			if ($value<0) $value = 0;
			$first = floor($value / 64);
			$second = $value % 64;
			$encoding .= self::$_xencoding[$first].self::$_xencoding[$second];
		}
		return $encoding;
	}

	static function _name_name_sort($a, $b) {
		return compareStrings(strip_prefix($a['name']), strip_prefix($b['name']), true);  // Case-insensitive compare
	}

	static function _name_name_rsort($a, $b) {
		return compareStrings(strip_prefix($b['name']), strip_prefix($a['name']), true);  // Case-insensitive compare
	}

	static function _name_total_sort($a, $b) {
		return $a['match']-$b['match'];
	}

	static function _name_total_rsort($a, $b) {
		return $b['match']-$a['match'];
	}

}

?>

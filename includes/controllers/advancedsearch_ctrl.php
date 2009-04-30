<?php

/**
 * Controller for the Advanced Search Page
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009	PGV Development Team. All rights reserved.
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
 *
 * @package PhpGedView
 * @subpackage Display
 * @version $Id: advancedsearch_ctrl.php,v 1.1 2009/04/30 19:09:48 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require_once (PHPGEDVIEW_PKG_PATH."includes/controllers/search_ctrl.php");

/**
 * Main controller class for the search page.
 */
class AdvancedSearchController extends SearchController {

	var $fields = array();
	var $values = array();
	var $plusminus = array();
	var $errors = array();

	/**
	 * constructor
	 */
	function AdvancedSearchController() {
		parent :: SearchController();
	}
	/**
	 * Initialization function
	 */
	function init() {
		global $SEARCH_FACTS_DEFAULT;
		parent :: init();
		if (empty($_REQUEST['action'])) $this->action="advanced";
		if ($this->action=="advanced") {
			if (isset($_REQUEST['fields'])) $this->fields = $_REQUEST['fields'];
			if (isset($_REQUEST['values'])) $this->values = $_REQUEST['values'];
			if (isset($_REQUEST['plusminus'])) $this->plusminus = $_REQUEST['plusminus'];
			$this->reorderFields();
			$this->advancedSearch();
		}
		if (count($this->fields)==0) {
			$this->fields = explode(",",$SEARCH_FACTS_DEFAULT);
			$this->fields[] = "FAMC:HUSB:NAME:GIVN:SDX";
			$this->fields[] = "FAMC:HUSB:NAME:SURN:SDX";
			$this->fields[] = "FAMC:WIFE:NAME:GIVN:SDX";
			$this->fields[] = "FAMC:WIFE:NAME:SURN:SDX";
		}
	}

	function getOtherFields() {
		global $INDI_FACTS_ADD;

		$ofields = array("ADDR","ADDR:CITY","ADDR:STAE","ADDR:CTRY","ADDR:POST",
			"AFN","EMAIL","FAX",
			"CHR:DATE","CHR:PLAC",
			"BURI:DATE","BURI:PLAC",
			"CREM:DATE","CREM:PLAC",
			"ADOP:DATE","ADOP:PLAC",
			"BAPM:DATE","BAPM:PLAC","BARM:DATE","BARM:PLAC","BASM:DATE","BASM:PLAC","BLES:DATE","BLES:PLAC",
			"EVEN","EVEN:DATE","EVEN:PLAC",
			"FCOM:DATE","FCOM:PLAC",
			"_MILI","_MILI:DATE","_MILI:PLAC",
			"ORDN:DATE","ORDN:PLAC",
			"NATU:DATE","NATU:PLAC","EMIG:DATE","EMIG:PLAC","IMMI:DATE","IMMI:PLAC",
			"CENS:DATE","CENS:PLAC",
			"CAST","DSCR",
			"NATI","OCCU","RELI","TITL",
			"RESI","RESI:DATE","RESI:PLAC",
			"NAME:NICK","NAME:_MARNM","NAME:_HEB","NAME:ROMN",
			"FAMS:CENS:DATE","FAMS:CENS:PLAC","FAMS:DIV:DATE","FAMS:DIV:PLAC",
			"NOTE","FAMS:NOTE",
			"BAPL:DATE","BAPL:PLAC","BAPL:TEMP",
			"ENDL:DATE","ENDL:PLAC","ENDL:TEMP",
			"SLGC:DATE","SLGC:PLAC","SLGC:TEMP",
			"FAMS:SLGS:DATE","FAMS:SLGS:PLAC","FAMS:SLGS:TEMP"
		);
		// Allow (some of) the user-specified fields to be selected
		foreach (explode(',', $INDI_FACTS_ADD) as $fact) {
			if (
				$fact!='BIRT' &&
				$fact!='DEAT' &&
				!in_array($fact, $ofields) &&
				!in_array("{$fact}:DATE", $ofields) &&
				!in_array("{$fact}:DATE", $ofields)
			) {
				$ofields[]=$fact;
			}
		}
		return $ofields;
	}

	function getPageTitle() {
		global $pgv_lang;
		if ($this->action=="advanced") return $pgv_lang["advanced_search"];
		else parent :: getPageTitle();
	}

	function getValue($i) {
		$val = "";
		if (isset($this->values[$i])) $val = $this->values[$i];
		return $val;
	}

	function getField($i) {
		$val = "";
		if (isset($this->fields[$i])) $val = htmlentities($this->fields[$i]);
		return $val;
	}

	function getIndex($field) {
		return array_search($field, $this->fields);
	}

	function getLabel($tag) {
		global $factarray, $pgv_lang;
		foreach (array('SDX', 'SDX_DM', 'SDX_STD', 'EXACT', 'BEGINS', 'CONTAINS') as $keyword) {
			$factarray["NAME:GIVN:$keyword"] = $factarray["GIVN"];
			$factarray["NAME:SURN:$keyword"] = $factarray["SURN"];
		}
		if (isset($factarray[$tag])) return $factarray[$tag];
		if (isset($pgv_lang[$tag])) return $pgv_lang[$tag];
		$label = "";
		$parts = preg_split("/:/", $tag);
		foreach($parts as $part) {
			if (isset($factarray[$part])) $label .= $factarray[$part]." ";
			else if (isset($pgv_lang[$part])) $label .= $pgv_lang[$part]." ";
			else $label .= $part." ";
		}
		return $label;
	}

	function reorderFields() {
		$i = 0;
		$newfields = array();
		$newvalues = array();
		$newplus = array();
		$rels = array();
		foreach($this->fields as $j=>$field) {
			if (strpos($this->fields[$j], "FAMC:HUSB:NAME")===0 || strpos($this->fields[$j], "FAMC:WIFE:NAME")===0) {
				$rels[$this->fields[$j]] = $this->values[$j];
				continue;
			}
			$newfields[$i] = $this->fields[$j];
			if (isset($this->values[$j])) $newvalues[$i] = $this->values[$j];
			if (isset($this->plusminus[$j])) $newplus[$i] = $this->plusminus[$j];
			$i++;
		}
		$this->fields = $newfields;
		$this->values = $newvalues;
		$this->plusminus = $newplus;
		foreach($rels as $field=>$value) {
			$this->fields[] = $field;
			$this->values[] = $value;
		}
	}

	function advancedSearch($justSql=false, $table="individuals", $prefix="i") {
		global $TBLPREFIX, $DBCONN, $DBTYPE;
		global $gedcom_record_cache;

		DMsoundex("", "opencache");
		$this->myindilist = array ();
		$fct = count($this->fields);
		if ($fct==0) return;

		$namesTable = false;
		$datesTable = false;
		$placesTable = false;
		$famsTable = false;
		$famcTable = false;

		$sql = '';
		if ($justSql) $sqlfields = "SELECT DISTINCT ".$prefix."_id, ".$prefix."_file";
		else $sqlfields = "SELECT i_id, i_gedcom, i_isdead, i_file, i_sex";
		$sqltables = " FROM ".$TBLPREFIX.$table;
		$sqlwhere = " WHERE ".$prefix."_file=".PGV_GED_ID;
		$keepfields = $this->fields;
		for($i=0; $i<$fct; $i++) {
			$field = $this->fields[$i];
			if (empty($field)) continue;
			$value='';
			if (isset($this->values[$i])) $value = $this->values[$i];
			if (empty($value)) continue;
			$parts = preg_split("/:/", $field);
			//-- handle names seperately
			if ($parts[0]=="NAME") {
				// The pgv_name table contains both names and soundex values
				if (!$namesTable) {
					$sqltables.=" JOIN ".$TBLPREFIX."name ON (i_file=n_file AND i_id=n_id) ";
					$namesTable = true;
				}
				switch (end($parts)) {
				case 'SDX_STD':
					$sdx=explode(':', soundex_std($value));
					foreach ($sdx as $k=>$v) {
						if ($parts[1]=='GIVN') {
							$sdx[$k]='n_soundex_givn_std '.PGV_DB_LIKE." '%{$v}%'";
						} else {
							$sdx[$k]='n_soundex_surn_std '.PGV_DB_LIKE." '%{$v}%'";
						}
					}
					$sqlwhere.=' AND ('.implode(' OR ', $sdx).')';
					break;
				case 'SDX':
					// SDX uses DM by default.
				case 'SDX_DM':
					$sdx=explode(':', soundex_dm($value));
					foreach ($sdx as $k=>$v) {
						if ($parts[1]=='GIVN') {
							$sdx[$k]='n_soundex_givn_dm '.PGV_DB_LIKE." '%{$v}%'";
						} else {
							$sdx[$k]='n_soundex_surn_dm '.PGV_DB_LIKE." '%{$v}%'";
						}
					}
					$sqlwhere.=' AND ('.implode(' OR ', $sdx).')';
					break;
				case 'EXACT':
					// Exact match.
					switch ($parts[1]) {
					case 'GIVN':
						// Allow for exact match on multiple given names.
						$sqlwhere.=' AND (n_givn '.PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)."' OR n_givn ".PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)." %' OR n_givn ".PGV_DB_LIKE." '% ".$DBCONN->escapeSimple($value)."' OR n_givn ".PGV_DB_LIKE." '% ".$DBCONN->escapeSimple($value)." %')";
						break;
					case 'SURN':
						$sqlwhere.=' AND n_surname '.PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)."'";
						break;
					default:
						$sqlwhere.=' AND n_full '.PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)."'";
						break;
					}
					break;
				case 'BEGINS':
					// "Begins with" match.
					switch ($parts[1]) {
					case 'GIVN':
						// Allow for match on start of multiple given names
						$sqlwhere.=' AND (n_givn '.PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)."%' OR n_givn ".PGV_DB_LIKE." '% ".$DBCONN->escapeSimple($value)."%')";
						break;
					case 'SURN':
						$sqlwhere.=' AND n_surname '.PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)."%'";
						break;
					default:
						$sqlwhere.=' AND n_full '.PGV_DB_LIKE." '".$DBCONN->escapeSimple($value)."%'";
						break;
					}
					break;
				case 'CONTAINS':
				default:
					// Partial match.
					switch ($parts[1]) {
					case 'GIVN':
						$sqlwhere.=' AND n_givn '.PGV_DB_LIKE." '%".$DBCONN->escapeSimple($value)."%'";
						break;
					case 'SURN':
						$sqlwhere.=' AND n_surname '.PGV_DB_LIKE." '%".$DBCONN->escapeSimple($value)."%'";
						break;
					default:
						$sqlwhere.=' AND n_full '.PGV_DB_LIKE." '%".$DBCONN->escapeSimple($value)."%'";
						break;
					}
					break;
				}
			}
			//-- handle dates
			else if (isset($parts[1]) && $parts[1]=="DATE") {
				if (!$datesTable) {
					$sqltables.=", ".$TBLPREFIX."dates";
					$sqlwhere .= " AND ".$prefix."_file=d_file AND ".$prefix."_id=d_gid";
					$datesTable = true;
				}
				$sqlwhere .= " AND (d_fact='".$parts[0]."'";
				$date = new GedcomDate($value);
				if ($date->isOK()) {
					$jd1 = $date->date1->minJD;
					if ($date->date2) $jd2 = $date->date2->maxJD;
					else $jd2 = $date->date1->maxJD;
					if (!empty($this->plusminus[$i])) {
						$adjd = $this->plusminus[$i]*365;
						//print $jd1.":".$jd2.":".$adjd;
						$jd1 = $jd1 - $adjd;
						$jd2 = $jd2 + $adjd;
					}
					$sqlwhere .= " AND d_julianday1>=".$jd1." AND d_julianday2<=".$jd2;
				}
				$sqlwhere .= ") ";
			}
			//-- handle places
			else if (isset($parts[1]) && $parts[1]=="PLAC") {
				if (!$placesTable) {
					$sqltables.=", ".$TBLPREFIX."places, ".$TBLPREFIX."placelinks";
					$sqlwhere .= " AND ".$prefix."_file=p_file AND p_file=pl_file AND ".$prefix."_id=pl_gid AND pl_p_id=p_id";
					$placesTable = true;
				}
				//-- soundex search
				//if (end($parts)=="SDX") {
					$places = preg_split("/[, ]+/", $value);
					$parr = array();
					for ($j = 0; $j < count($places); $j ++) {
						$parr[$j] = DMsoundex($places[$j]);
					}
					$sqlwhere .= " AND (";
					$fnc = 0;
					$field = "p_dm_soundex";
					foreach ($parr as $name) {
						foreach ($name as $name1) {
							if ($fnc>0)
								$sqlwhere .= " OR ";
							$fnc++;
							$sqlwhere .= $field." ".PGV_DB_LIKE." '%".$DBCONN->escapeSimple($name1)."%'";
						}
					}
					$sqlwhere .= ") ";
				//}
			}
			//-- handle parent/spouse names
			else if ($parts[0]=='FAMS') {
				if (!$famsTable) {
					$sqltables.=", ".$TBLPREFIX."families as FAMS";
					$sqlwhere .= " AND i_file=FAMS.f_file";
					$famsTable = true;
				}
				//-- alter the fields and recurse to generate a subquery for spouse/parent fields
				$oldfields = $this->fields;
				for($j=0; $j<$fct; $j++) {
					//-- if it doesn't start with FAMS or FAMC then remove that field
					if (preg_match("/^".$parts[0].":/", $this->fields[$j])==0) {
						$this->fields[$j]='';
					}
					else $this->fields[$j] = preg_replace("/^".$parts[0].":/","", $this->fields[$j]);
				}
				$sqlwhere .= " AND (FAMS.f_husb=i_id OR FAMS.f_wife=i_id)";
				$subsql = $this->advancedSearch(true,"families","f");
				$sqlwhere .= " AND ROW(FAMS.f_id, FAMS.f_file) IN (".$subsql.")";
				$this->fields = $oldfields;
				//-- remove all of the fam fields so they don't show up again
				for($j=0; $j<$fct; $j++) {
					//-- if it does start with FAMS or FAMC then remove that field
					if (preg_match("/^".$parts[0].":/", $this->fields[$j])>0) {
						$this->fields[$j]='';
					}
				}
			}
			else if ($parts[0]=='FAMC') {
				if (!$famcTable) {
					$sqltables.=", ".$TBLPREFIX."families as FAMC";
					$sqlwhere .= " AND i_file=FAMC.f_file";
					$famcTable = true;
				}
				//-- alter the fields and recurse to generate a subquery for spouse/parent fields
				$oldfields = $this->fields;
				for($j=0; $j<$fct; $j++) {
					//-- if it doesn't start with FAMS or FAMC then remove that field
					if (preg_match("/^".$parts[0].":/", $this->fields[$j])==0) {
						$this->fields[$j]='';
					}
					else $this->fields[$j] = preg_replace("/^".$parts[0].":/","", $this->fields[$j]);
				}
				$sqlwhere .= " AND (FAMC.f_chil ".PGV_DB_LIKE." CONCAT('%',i_id,';%'))";
				$subsql = $this->advancedSearch(true,"families","f");
				$sqlwhere .= " AND ROW(FAMC.f_id, FAMC.f_file) IN (".$subsql.")";
				$this->fields = $oldfields;
				//-- remove all of the fam fields so they don't show up again
				for($j=0; $j<$fct; $j++) {
					//-- if it does start with FAMS or FAMC then remove that field
					if (preg_match("/^".$parts[0].":/", $this->fields[$j])>0) {
						$this->fields[$j]='';
					}
				}
			}
			else if ($parts[0]=='HUSB' || $parts[0]=='WIFE') {
				if (!$famsTable) {
					$sqltables.=", ".$TBLPREFIX."individuals";
					$sqlwhere .= " AND i_file=f_file";
					$famsTable = true;
				}
				//-- alter the fields and recurse to generate a subquery for spouse/parent fields
				$oldfields = $this->fields;
				for($j=0; $j<$fct; $j++) {
					//-- if it doesn't start with FAMS or FAMC then remove that field
					if (preg_match("/^".$parts[0].":/", $this->fields[$j])==0) {
						$this->fields[$j]='';
					}
					else $this->fields[$j] = preg_replace("/^".$parts[0].":/","", $this->fields[$j]);
				}
				$subsql = $this->advancedSearch(true,"individuals","i");
				if ($parts[0]=='HUSB') $sqlwhere .= " AND ROW(f_husb, f_file) IN (".$subsql.")";
				if ($parts[0]=='WIFE') $sqlwhere .= " AND ROW(f_wife, f_file) IN (".$subsql.")";
				$this->fields = $oldfields;
				//-- remove all of the fam fields so they don't show up again
				for($j=0; $j<$fct; $j++) {
					//-- if it does start with HUSB or WIFE then remove that field
					if (preg_match("/^".$parts[0].":/", $this->fields[$j])>0) {
						$this->fields[$j]='';
					}
				}
			}
			//-- handle everything else
			else {
				$sqlwhere .= " AND i_gedcom ".PGV_DB_LIKE." '";

				$ct = count($parts);
				for($j=0; $j<$ct; $j++) {
					 $sqlwhere .= "%".($j+1)." ".$parts[$j]." %";
//					 if ($j<$ct-1) {
//					 	$sqlwhere .= "%";
//					 } else {
					 	$sqlwhere .= "%".$DBCONN->escapeSimple($value)."%";
//					 }
				}
				$sqlwhere .= "'";
			}
		}
		$sql = $sqlfields.$sqltables.$sqlwhere;
//		print $sql;
		if ($justSql) return $sql;
		$res = dbquery($sql);
		if (!DB::isError($res)) {
			while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)){
				$row['xref']=$row['i_id'];
				$row['ged_id']=$row['i_file'];
				$row['type'] = 'INDI';
				$row['gedrec'] = $row['i_gedcom'];
				$object = Person::getInstance($row);
				$this->myindilist[$row['i_id']] = $object;
			}
			$res->free();
		}
		$this->fields = $keepfields;
	}

	function PrintResults() {
		global $pgv_lang, $GEDCOMS;

		include_once ("includes/functions/functions_print_lists.php");
		$ret = true;
		if (count($this->myindilist)>0) {
			print "<br />";
			print "\n\t<div class=\"center\">\n";
			uasort($this->myindilist, array("GedcomRecord","Compare"));
			print_indi_table($this->myindilist, $pgv_lang["individuals"]." @ ".PrintReady($GEDCOMS[PGV_GEDCOM]["title"], true));
			print "</div>";
		}
		else {
			$ret = false;
			if ($this->isPostBack) print "<br /><div class=\"warning\" style=\" text-align: center;\"><i>".$pgv_lang["no_results"]."</i><br /></div>\n\t\t";
		}
		return $ret;
	}
}
// -- end of class

//-- load a user extended class if one exists
if (file_exists('includes/controllers/advancedsearch_ctrl_user.php')) {
	include_once 'includes/controllers/advancedsearch_ctrl_user.php';
}
?>

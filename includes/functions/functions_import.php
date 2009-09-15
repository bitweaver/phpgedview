<?php
/**
*
* Import specific functions
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
* @version $Id: functions_import.php,v 1.3 2009/09/15 20:06:02 lsces Exp $
* @package PhpGedView
* @subpackage DB
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_FUNCTIONS_IMPORT_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/index_cache.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_media.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_mutex.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_lang.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_name.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_export.php');

// Tidy up a gedcom record on import, so that we can access it consistently/efficiently.
function reformat_record_import($rec) {
	global $WORD_WRAPPED_NOTES;

	// Strip out UTF8 formatting characters
	$rec=str_replace(array(PGV_UTF8_BOM, PGV_UTF8_LRM, PGV_UTF8_RLM), '', $rec);

	// Strip out control characters and mac/msdos line endings
	static $control1="\r\x01\x02\x03\x04\x05\x06\x07\x08\x0B\x0C\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x7F";
	static $control2="\n?????????????????????????????";
	$rec=strtr($rec, $control1, $control2);

	// Extract lines from the record; lines consist of: level + optional xref + tag + optional data
	$num_matches=preg_match_all('/^[ \t]*(\d+)[ \t]*(@[^@]*@)?[ \t]*(\w+)[ \t]?(.*)$/m', $rec, $matches, PREG_SET_ORDER);

	// Process the record line-by-line
	$newrec='';
	foreach ($matches as $n=>$match) {
		list(, $level, $xref, $tag, $data)=$match;
		// Convert FTM-style "TAG_FORMAL_NAME" into "TAG".
		switch ($tag) {
		case 'ABBREVIATION':
			$tag='ABBR';
			break;
		case 'ADDRESS':
			$tag='ADDR';
			break;
		case 'ADDRESS1':
			$tag='ADR1';
			break;
		case 'ADDRESS2':
			$tag='ADR2';
			break;
		case 'ADDRESS3':
			$tag='ADR3';
			break;
		case 'ADOPTION':
			$tag='ADOP';
			break;
		case 'ADULT_CHRISTENING':
			$tag='CHRA';
			break;
		case 'AFN':
			// AFN values are upper case
			$data=strtoupper($data);
			break;
		case 'AGENCY':
			$tag='AGNC';
			break;
		case 'ALIAS':
			$tag='ALIA';
			break;
		case 'ANCESTORS':
			$tag='ANCE';
			break;
		case 'ANCES_INTEREST':
			$tag='ANCI';
			break;
		case 'ANNULMENT':
			$tag='ANUL';
			break;
		case 'ASSOCIATES':
			$tag='ASSO';
			break;
		case 'AUTHOR':
			$tag='AUTH';
			break;
		case 'BAPTISM':
			$tag='BAPM';
			break;
		case 'BAPTISM_LDS':
			$tag='BAPL';
			break;
		case 'BAR_MITZVAH':
			$tag='BARM';
			break;
		case 'BAS_MITZVAH':
			$tag='BASM';
			break;
		case 'BIRTH':
			$tag='BIRT';
			break;
		case 'BLESSING':
			$tag='BLES';
			break;
		case 'BURIAL':
			$tag='BURI';
			break;
		case 'CALL_NUMBER':
			$tag='CALN';
			break;
		case 'CASTE':
			$tag='CAST';
			break;
		case 'CAUSE':
			$tag='CAUS';
			break;
		case 'CENSUS':
			$tag='CENS';
			break;
		case 'CHANGE':
			$tag='CHAN';
			break;
		case 'CHARACTER':
			$tag='CHAR';
			break;
		case 'CHILD':
			$tag='CHIL';
			break;
		case 'CHILDREN_COUNT':
			$tag='NCHI';
			break;
		case 'CHRISTENING':
			$tag='CHR';
			break;
		case 'CONCATENATION':
			$tag='CONC';
			break;
		case 'CONFIRMATION':
			$tag='CONF';
			break;
		case 'CONFIRMATION_LDS':
			$tag='CONL';
			break;
		case 'CONTINUED':
			$tag='CONT';
			break;
		case 'COPYRIGHT':
			$tag='COPR';
			break;
		case 'CORPORATE':
			$tag='CORP';
			break;
		case 'COUNTRY':
			$tag='CTRY';
			break;
		case 'CREMATION':
			$tag='CREM';
			break;
		case 'DATE':
			// Preserve text from INT dates
			if (strpos($data, '(')!==false) {
				list($date, $text)=explode('(', $data, 2);
				$text=' ('.$text;
			} else {
				$date=$data;
				$text='';
			}
			// Capitals
			$date=strtoupper($date);
			// Temporarily add leading/trailing spaces, to allow efficient matching below
			$date=" {$date} ";
			// Ensure space digits and letters
			$date=preg_replace('/([A-Z])(\d)/', '$1 $2', $date);
			$date=preg_replace('/(\d)([A-Z])/', '$1 $2', $date);
			// Ensure space before/after calendar escapes
			$date=preg_replace('/@#[^@]+@/', ' $0 ', $date);
			// "BET." => "BET"
			$date=preg_replace('/(\w\w)\./', '$1', $date);
			// "CIR" => "ABT"
			$date=str_replace(' CIR ', ' ABT ', $date);
			$date=str_replace(' APX ', ' ABT ', $date);
			// B.C. => BC (temporarily, to allow easier handling of ".")
			$date=str_replace(' B.C. ', ' BC ', $date);
			// "BET X - Y " => "BET X AND Y"
			$date=preg_replace('/^(.* BET .+) - (.+)/', '$1 AND $2', $date);
			$date=preg_replace('/^(.* FROM .+) - (.+)/', '$1 TO $2', $date);
			// "@#ESC@ FROM X TO Y" => "FROM @#ESC@ X TO @#ESC@ Y"
			$date=preg_replace('/^ +(@#[^@]+@) +FROM +(.+) +TO +(.+)/', ' FROM $1 $2 TO $1 $3', $date);
			$date=preg_replace('/^ +(@#[^@]+@) +BET +(.+) +AND +(.+)/', ' BET $1 $2 AND $1 $3', $date);
			// "@#ESC@ AFT X" => "AFT @#ESC@ X"
			$date=preg_replace('/^ +(@#[^@]+@) +(FROM|BET|TO|AND|BEF|AFT|CAL|EST|INT|ABT) +(.+)/', ' $2 $1 $3', $date);
			// Ignore any remaining punctuation, e.g. "14-MAY, 1900" => "14 MAY 1900"
			// (don't change "/" - it is used in NS/OS dates)
			$date=preg_replace('/[.,:;-]/', ' ', $date);
			// BC => B.C.
			$date=str_replace(' BC ', ' B.C. ', $date);
			// Append the "INT" text
			$data=$date.$text;
			break;
		case 'DEATH':
			$tag='DEAT';
			break;
		case '_DEGREE':
			$tag='_DEG';
			break;
		case 'DESCENDANTS':
			$tag='DESC';
			break;
		case 'DESCENDANT_INT':
			$tag='DESI';
			break;
		case 'DESTINATION':
			$tag='DEST';
			break;
		case 'DIVORCE':
			$tag='DIV';
			break;
		case 'DIVORCE_FILED':
			$tag='DIVF';
			break;
		case 'EDUCATION':
			$tag='EDUC';
			break;
		case 'EMIGRATION':
			$tag='EMIG';
			break;
		case 'ENDOWMENT':
			$tag='ENDL';
			break;
		case 'ENGAGEMENT':
			$tag='ENGA';
			break;
		case 'EVENT':
			$tag='EVEN';
			break;
		case 'FACSIMILE':
			$tag='FAX';
			break;
		case 'FAMILY':
			$tag='FAM';
			break;
		case 'FAMILY_CHILD':
			$tag='FAMC';
			break;
		case 'FAMILY_FILE':
			$tag='FAMF';
			break;
		case 'FAMILY_SPOUSE':
			$tag='FAMS';
			break;
		case 'FIRST_COMMUNION':
			$tag='FCOM';
			break;
		case '_FILE':
			$tag='FILE';
			break;
		case 'FORMAT':
			$tag='FORM';
		case 'FORM':
			// Consistent commas
			$data=preg_replace('/ *, */', ', ', $data);
			break;
		case 'GEDCOM':
			$tag='GEDC';
			break;
		case 'GIVEN_NAME':
			$tag='GIVN';
			break;
		case 'GRADUATION':
			$tag='GRAD';
			break;
		case 'HEADER':
			$tag='HEAD';
		case 'HEAD':
			// HEAD records don't have an XREF or DATA
			if ($level=='0') {
				$xref='';
				$data='';
			}
			break;
		case 'HUSBAND':
			$tag='HUSB';
			break;
		case 'IDENT_NUMBER':
			$tag='IDNO';
			break;
		case 'IMMIGRATION':
			$tag='IMMI';
			break;
		case 'INDIVIDUAL':
			$tag='INDI';
			break;
		case 'LANGUAGE':
			$tag='LANG';
			break;
		case 'LATITUDE':
			$tag='LATI';
			break;
		case 'LONGITUDE':
			$tag='LONG';
			break;
		case 'MARRIAGE':
			$tag='MARR';
			break;
		case 'MARRIAGE_BANN':
			$tag='MARB';
			break;
		case 'MARRIAGE_COUNT':
			$tag='NMR';
			break;
		case 'MARR_CONTRACT':
			$tag='MARC';
			break;
		case 'MARR_LICENSE':
			$tag='MARL';
			break;
		case 'MARR_SETTLEMENT':
			$tag='MARS';
			break;
		case 'MEDIA':
			$tag='MEDI';
			break;
		case '_MEDICAL':
			$tag='_MDCL';
			break;
		case '_MILITARY_SERVICE':
			$tag='_MILT';
			break;
		case 'NAME_PREFIX':
			$tag='NPFX';
			break;
		case 'NAME_SUFFIX':
			$tag='NSFX';
			break;
		case 'NATIONALITY':
			$tag='NATI';
			break;
		case 'NATURALIZATION':
			$tag='NATU';
			break;
		case 'NICKNAME':
			$tag='NICK';
			break;
		case 'OBJECT':
			$tag='OBJE';
			break;
		case 'OCCUPATION':
			$tag='OCCU';
			break;
		case 'ORDINANCE':
			$tag='ORDI';
			break;
		case 'ORDINATION':
			$tag='ORDN';
			break;
		case 'PEDIGREE':
			$tag='PEDI';
		case 'PEDI':
			// PEDI values are lower case
			$data=strtolower($data);
			break;
		case 'PHONE':
			$tag='PHON';
			break;
		case 'PHONETIC':
			$tag='FONE';
			break;
		case 'PHY_DESCRIPTION':
			$tag='DSCR';
			break;
		case 'PLACE':
			$tag='PLAC';
		case 'PLAC':
			// Consistent commas
			$data=preg_replace('/ *, */', ', ', $data);
			break;
		case 'POSTAL_CODE':
			$tag='POST';
			break;
		case 'PROBATE':
			$tag='PROB';
			break;
		case 'PROPERTY':
			$tag='PROP';
			break;
		case 'PUBLICATION':
			$tag='PUBL';
			break;
		case 'QUALITY_OF_DATA':
			$tag='QUAL';
			break;
		case 'REC_FILE_NUMBER':
			$tag='RFN';
			break;
		case 'REC_ID_NUMBER':
			$tag='RIN';
			break;
		case 'REFERENCE':
			$tag='REFN';
			break;
		case 'RELATIONSHIP':
			$tag='RELA';
			break;
		case 'RELIGION':
			$tag='RELI';
			break;
		case 'REPOSITORY':
			$tag='REPO';
			break;
		case 'RESIDENCE':
			$tag='RESI';
			break;
		case 'RESTRICTION':
			$tag='RESN';
		case 'RESN':
			// RESN values are lower case (confidential, privacy, locked)
			$data=strtolower($data);
			break;
		case 'RETIREMENT':
			$tag='RETI';
			break;
		case 'ROMANIZED':
			$tag='ROMN';
			break;
		case 'SEALING_CHILD':
			$tag='SLGC';
			break;
		case 'SEALING_SPOUSE':
			$tag='SLGS';
			break;
		case 'SOC_SEC_NUMBER':
			$tag='SSN';
			break;
		case 'SEX':
			switch (trim($data)) {
			case 'M':
			case 'F':
			case 'U':
				break;
			case 'm':
				$data='M';
				break;
			case 'f':
				$data='F';
				break;
			default:
				$data='U';
				break;
			}
			break;
		case 'SOURCE':
			$tag='SOUR';
			break;
		case 'STATE':
			$tag='STAE';
			break;
		case 'STATUS':
			$tag='STAT';
			break;
		case 'SUBMISSION':
			$tag='SUBN';
			break;
		case 'SUBMITTER':
			$tag='SUBM';
			break;
		case 'SURNAME':
			$tag='SURN';
			break;
		case 'SURN_PREFIX':
			$tag='SPFX';
			break;
		case 'TEMPLE':
			$tag='TEMP';
		case 'TEMP':
			// Temple codes are upper case
			$data=strtoupper($data);
			break;
		case 'TITLE':
			$tag='TITL';
			break;
		case 'TRAILER':
			$tag='TRLR';
		case 'TRLR':
			// TRLR records don't have an XREF or DATA
			if ($level=='0') {
				$xref='';
				$data='';
			}
			break;
		case 'VERSION':
			$tag='VERS';
			break;
		case 'WEB':
			$tag='WWW';
			break;
		}
		// Suppress "Y", for facts/events with a DATE or PLAC
		if ($data=='y') {
			$data='Y';
		}
		if ($level=='1' && $data=='Y') {
			for ($i=$n+1; $i<$num_matches-1 && $matches[$i][1]!='1'; ++$i) {
				if ($matches[$i][3]=='DATE' || $matches[$i][3]=='PLAC') {
					$data='';
					break;
				}
			}
		}
		// Reassemble components back into a single line
		switch ($tag) {
		default:
			// Remove tabs and multiple/leading/trailing spaces
			if (strpos($data, "\t")!==false) {
				$data=str_replace("\t", ' ', $data);
			}
			if (substr($data, 0, 1)==' ' || substr($data, -1, 1)==' ') {
				$data=trim($data);
			}
			while (strpos($data, '  ')) {
				$data=str_replace('  ', ' ', $data);
			}
			// no break - just fall through
		case 'NOTE':
		case 'TEXT':
		case 'DATA':
		case 'CONT':
		case 'FILE':
			// Don't strip tabs, even though they are not valid in gedcom data.
			if ($newrec) {
				$newrec.="\n";
			}
			$newrec.=$level.' '.($level=='0' && $xref ? $xref.' ' : '').$tag.($data==='' ? '' : ' '.$data);
			break;
		case 'CONC':
			// Merge CONC lines, to simplify access later on.
			// For $n==1, we must be appending to a level 0 record, so add a space
			$newrec.=($WORD_WRAPPED_NOTES || $n==1 ? ' ' : '').$data;
			break;
		}
	}
	return $newrec;
}

/**
* import record into database
*
* this function will parse the given gedcom record and add it to the database
* @param string $gedrec the raw gedcom record to parse
* @param boolean $update whether or not this is an updated record that has been accepted
*/
function import_record($gedrec, $update) {
	global $xtype, $TBLPREFIX, $GEDCOM_FILE, $FILE, $pgv_lang, $USE_RIN;
	global $place_id, $WORD_WRAPPED_NOTES, $GEDCOMS, $MAX_IDS, $fpnewged, $GEDCOM, $GENERATE_UIDS;
	global $gBitDb;

	$FILE=$GEDCOM;

	// Escaped @ signs (only if importing from file)
	if (!$update) {
		$gedrec=str_replace('@@', '@', $gedrec);
	}

	// Standardise gedcom format
	$gedrec=reformat_record_import($gedrec);

	// import different types of records
	if (preg_match('/^0 @('.PGV_REGEX_XREF.')@ ('.PGV_REGEX_TAG.')/', $gedrec, $match) > 0) {
		list(,$gid, $type)=$match;
		// check for a _UID, if the record doesn't have one, add one
		if ($GENERATE_UIDS && !strpos($gedrec, "\n1 _UID ")) {
			$gedrec.="\n1 _UID ".uuid();
		}
	} elseif (preg_match('/0 ('.PGV_REGEX_TAG.')/', $gedrec, $match)) {
		$gid=$match[1];
		$type=$match[1];
	} else {
		echo $pgv_lang['invalid_gedformat'], '<br /><pre>', $gedrec, '</pre>';
		return;
	}

	// keep track of the max id for each type as they are imported
	if (!isset($MAX_IDS)) {
		$MAX_IDS=array ();
	}
	if (preg_match('/(\d+)/', $gid, $match)) {
		$idnum=(int)$match[1];
	} else {
		$idnum=0;
	}
	if (isset($MAX_IDS[$type])) {
		$MAX_IDS[$type]=max($MAX_IDS[$type], $idnum);
	} else {
		$MAX_IDS[$type]=$idnum;
	}

	$newrec = update_media($gid, $gedrec, $update);
	if ($newrec!=$gedrec) {
		$gedrec=$newrec;
		// make sure we have the correct media id
		if (preg_match('/0 @('.PGV_REGEX_XREF.')@ ('.PGV_REGEX_TAG.')/', $gedrec, $match)) {
			list(,$gid, $type)=$match;
		} else {
			echo $pgv_lang['invalid_gedformat'], '<br /><pre>', $gedrec, '</pre>';
			return;
		}
	}
 
	switch ($type) {
	case 'INDI':
		$record=new Person($gedrec);
		break;
	case 'FAM':
		$record=new Family($gedrec);
		break;
	case 'SOUR':
		$record=new Source($gedrec);
		break;
	case 'REPO':
		$record=new Repository($gedrec);
		break;
	case 'OBJE':
		$record=new Media($gedrec);
		break;
	default:
		$record=new GedcomRecord($gedrec);
		$type=$record->getType();
		break;
	}

	// Just in case the admin has blocked themself from seeing names!
	$record->disp=true;
	$record->dispname=true;

	// Update the cross-reference/index tables.
	$ged_id=$GEDCOMS[$GEDCOM]["id"];
	$xref  =$gid;

	update_places($xref, $ged_id, $gedrec);
	update_dates ($xref, $ged_id, $gedrec);
	update_links ($xref, $ged_id, $gedrec);
	update_rlinks($xref, $ged_id, $gedrec);
	update_names ($xref, $ged_id, $record);

	switch ($type) {
	case 'INDI':
		if ($USE_RIN && preg_match('/\n1 RIN (.+)/', $gedrec, $match)) {
			$rin=$match[1];
		} else {
			$rin=$xref;
		}
		$param = array($xref, $ged_id, $rin, is_dead($gedrec, '', true), $record->getSex(), $gedrec);
		$gBitDb->query(
			"INSERT INTO {$TBLPREFIX}individuals (i_id, i_file, i_rin, i_isdead, i_sex, i_gedcom) VALUES (?,?,?,?,?,?)"
			, $param );
		break;
	case 'FAM':
		if (preg_match('/\n1 HUSB @('.PGV_REGEX_XREF.')@/', $gedrec, $match)) {
			$husb=$match[1];
		} else {
			$husb='';
		}
		if (preg_match('/\n1 WIFE @('.PGV_REGEX_XREF.')@/', $gedrec, $match)) {
			$wife=$match[1];
		} else {
			$wife='';
		}
		if ($nchi=preg_match_all('/\n1 CHIL @('.PGV_REGEX_XREF.')@/', $gedrec, $match)) {
			$chil=implode(';', $match[1]).';';
		} else {
			$chil='';
		}
		if (preg_match('/\n1 NCHI (\d+)/', $gedrec, $match)) {
			$nchi=max($nchi, $match[1]);
		}
		$param = array($xref, $ged_id, $husb, $wife, $chil, $gedrec, $nchi);
		$gBitDb->query( 
			"INSERT INTO {$TBLPREFIX}families (f_id, f_file, f_husb, f_wife, f_chil, f_gedcom, f_numchil) VALUES (?,?,?,?,?,?,?)"
			, $param );
		break;
	case 'SOUR':
		if (preg_match('/\n1 TITL (.+)/', $gedrec, $match)) {
			$name=$match[1];
		} elseif (preg_match('/\n1 ABBR (.+)/', $gedrec, $match)) {
			$name=$match[1];
		} else {
			$name=$gid;
		}
		if (strpos($gedrec, "\n1 _DBID")) {
			$_dbid='Y';
		} else {
			$_dbid=null;
		}
		$gBitDb->query(
			"INSERT INTO {$TBLPREFIX}sources (s_id, s_file, s_name, s_gedcom, s_dbid) VALUES (?,?,?,?,?)"
			, array($xref, $ged_id, $name, $gedrec, $_dbid) );
		break;
	case 'OBJE':
		// OBJE records are imported by update_media function
		break;
	case 'HEAD':
		if (!strpos($gedrec, "\n1 DATE ")) {
			$gedrec.="\n1 DATE ".date('j M Y');
		}
		// no break
	default:
		if (substr($type, 0, 1)!='_') {
		$gBitDb->query(
			"INSERT INTO {$TBLPREFIX}other (o_id, o_file, o_type, o_gedcom) VALUES (?,?,?,?)"
			, array($xref, $ged_id, $type, $gedrec));
		}
		break;
	}


	// if this is not an update then write it to the new gedcom file
	if (!$update && !empty($fpnewged)) {
		fwrite($fpnewged, reformat_record_export($gedrec));
	}

	$xtype=$type; // Pass value back to uploadgedcom.php
}

/**
* extract all places from the given record and insert them
* into the places table
* @param string $gedrec
*/
function update_places($gid, $ged_id, $gedrec) {
	global $placecache, $TBLPREFIX, $gBitDb;

	if (!isset($placecache)) {
		$placecache = array();
	}
	$personplace = array();
	// import all place locations, but not control info such as
	// 0 HEAD/1 PLAC or 0 _EVDEF/1 PLAC
	$pt = preg_match_all("/^[2-9] PLAC (.+)/m", $gedrec, $match, PREG_SET_ORDER);
	for ($i = 0; $i < $pt; $i++) {
		$place = trim($match[$i][1]);
		$lowplace = UTF8_strtolower($place);
		//-- if we have already visited this place for this person then we don't need to again
		if (isset($personplace[$lowplace])) {
			continue;
		}
		$personplace[$lowplace] = 1;
		$places = explode(',', $place);
		//-- reverse the array to start at the highest level
		$secalp = array_reverse($places);
		$parent_id = 0;
		$level = 0;
		$search = true;

		foreach ($secalp as $indexval => $place) {
			$place = trim($place);
			$place=preg_replace('/\\\"/', "", $place);
			$place=preg_replace("/[\><]/", "", $place);
			$key = strtolower($place."_".$level."_".$parent_id);
			//-- if this place has already been added then we don't need to add it again
			if (isset($placecache[$key])) {
				$parent_id = $placecache[$key];
				if (!isset($personplace[$key])) {
					$personplace[$key]=1;
					$gBitDb->query( 
						"INSERT INTO {$TBLPREFIX}placelinks (pl_p_id, pl_gid, pl_file) VALUES (?,?,?)"
						, array($parent_id, $gid, $ged_id));
				}
				$level++;
				continue;
			}

			//-- only search the database while we are finding places in it
			if ($search) {
				//-- check if this place and level has already been added
				$tmp = $gBitDb->getOne( 
					"SELECT p_id FROM {$TBLPREFIX}places WHERE p_level=? AND p_file=? AND p_parent_id=? AND p_place LIKE ?"
					, array($level, $ged_id, $parent_id, $place));
				if ($tmp) {
					$p_id = $tmp;
				} else {
					$search = false;
				}
			}

			//-- if we are not searching then we have to insert the place into the db
			if (!$search) {
				$std_soundex = soundex_std($place);
				$dm_soundex = soundex_dm($place);
				$p_id = get_next_id("places", "p_id");
				$gBitDb->query( 
					"INSERT INTO {$TBLPREFIX}places (p_id, p_place, p_level, p_parent_id, p_file, p_std_soundex, p_dm_soundex) VALUES (?,?,?,?,?,?,?)"
					, array($p_id, $place, $level, $parent_id, $ged_id, $std_soundex, $dm_soundex));
			}

			$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}placelinks (pl_p_id, pl_gid, pl_file) VALUES (?,?,?)"
				, array($p_id, $gid, $ged_id));

			//-- increment the level and assign the parent id for the next place level
			$parent_id = $p_id;
			$placecache[$key] = $p_id;
			$personplace[$key]=1;
			$level++;
		}
	}
	return;
}

// extract all the dates from the given record and insert them into the database
function update_dates($xref, $ged_id, $gedrec) {
	global $TBLPREFIX, $factarray, $gBitDb;

	if (strpos($gedrec, '2 DATE ') && preg_match_all("/\n1 (\w+).*(?:\n[2-9].*)*(?:\n2 DATE (.+))(?:\n[2-9].*)*/", $gedrec, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$fact=$match[1];
			if (($fact=='FACT' || $fact=='EVEN') && preg_match("/\n2 TYPE (\w+)/", $match[0], $tmatch) && array_key_exists($tmatch[1], $factarray)) {
				$fact=$tmatch[1];
			}
			$date=new GedcomDate($match[2]);
			$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}dates (d_day,d_month,d_mon,d_year,d_julianday1,d_julianday2,d_fact,d_gid,d_file,d_type) VALUES (?,?,?,?,?,?,?,?,?,?)"
				, array($date->date1->d, $date->date1->Format('O'), $date->date1->m, $date->date1->y, $date->date1->minJD, $date->date1->maxJD, $fact, $xref, $ged_id, $date->date1->CALENDAR_ESCAPE()));
			if ($date->date2) {
				$gBitDb->query(
					"INSERT INTO {$TBLPREFIX}dates (d_day,d_month,d_mon,d_year,d_julianday1,d_julianday2,d_fact,d_gid,d_file,d_type) VALUES (?,?,?,?,?,?,?,?,?,?)"
					, arrayarray($date->date2->d, $date->date2->Format('O'), $date->date2->m, $date->date2->y, $date->date2->minJD, $date->date2->maxJD, $fact, $xref, $ged_id, $date->date2->CALENDAR_ESCAPE()));
			}
		}
	}
	return;
}

// extract all the remote links from the given record and insert them into the database
function update_rlinks($xref, $ged_id, $gedrec) {
	global $TBLPREFIX, $gBitDb;

	if (preg_match_all("/\n1 RFN (".PGV_REGEX_XREF.')/', $gedrec, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			// Ignore any errors, which may be caused by "duplicates" that differ on case/collation, e.g. "S1" and "s1"
			$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}remotelinks (r_gid,r_linkid,r_file) VALUES (?,?,?)"
				, array($xref, $match[1], $ged_id));
		}
	}
	return;
}

// extract all the links from the given record and insert them into the database
function update_links($xref, $ged_id, $gedrec) {
	global $TBLPREFIX, $gBitDb;

	if (preg_match_all('/^\d+ ('.PGV_REGEX_TAG.') @('.PGV_REGEX_XREF.')@/m', $gedrec, $matches, PREG_SET_ORDER)) {
		$data=array();
		foreach ($matches as $match) {
			// Include each link once only.
			if (!in_array($match[1].$match[2], $data)) {
				$data[]=$match[1].$match[2];
				// Ignore any errors, which may be caused by "duplicates" that differ on case/collation, e.g. "S1" and "s1"
				$gBitDb->query(
					"INSERT INTO {$TBLPREFIX}link (l_from,l_to,l_type,l_file) VALUES (?,?,?,?)"
					, array($xref, $match[2], $match[1], $ged_id));
			}
		}
	}
}

// extract all the names from the given record and insert them into the database
function update_names($xref, $ged_id, $record) {
	global $TBLPREFIX, $gBitDb;

	if ($record->getType()!='FAM' && $record->getXref()) {
		foreach ($record->getAllNames() as $n=>$name) {
			if ($record->getType()=='INDI') {
				if ($name['givn']=='@P.N.') {
					$soundex_givn_std=null;
					$soundex_givn_dm=null;
				} else {
					$soundex_givn_std="'".soundex_std($name['givn'])."'";
					$soundex_givn_dm="'".soundex_dm($name['givn'])."'";
				}
				if ($name['surn']=='@N.N.') {
					$soundex_surn_std=null;
					$soundex_surn_dm=null;
				} else {
					$soundex_surn_std="'".soundex_std($name['surname'])."'";
					$soundex_surn_dm="'".soundex_dm($name['surname'])."'";
				}
				$gBitDb->query(
					"INSERT INTO {$TBLPREFIX}name (n_file,n_id,n_num,n_type,n_sort,n_full,n_list,n_surname,n_surn,n_givn,n_soundex_givn_std,n_soundex_surn_std,n_soundex_givn_dm,n_soundex_surn_dm) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
					, array($ged_id, $xref, $n, $name['type'], $name['sort'], $name['fullNN'], $name['listNN'], $name['surname'], $name['surn'], $name['givn'], $soundex_givn_std, $soundex_surn_std, $soundex_givn_dm, $soundex_surn_dm));
			} else {
				$gBitDb->query(
					"INSERT INTO {$TBLPREFIX}name (n_file,n_id,n_num,n_type,n_sort,n_full,n_list) VALUES (?,?,?,?,?,?,?)"
					, array($ged_id, $xref, $n, $name['type'], $name['sort'], $name['full'], $name['list']));
			}
		}
	}
}
/**
* Insert media items into the database
* This method is used in conjuction with the gedcom import/update routines
* @param string $objrec The OBJE subrecord
* @param int $objlevel The original level of this OBJE
* @param boolean $update Whether or not this is an update or an import
* @param string $gid The XREF ID of the record this OBJE is related to
* @param int $count The count of OBJE records in the parent record
*/
function insert_media($objrec, $objlevel, $update, $gid, $count) {
	global $TBLPREFIX, $media_count, $GEDCOMS, $FILE, $found_ids, $fpnewged, $gBitDb;

	//-- check for linked OBJE records
	//-- linked records don't need to insert to media table
	$ct = preg_match("/OBJE @(.*)@/", $objrec, $match);
	if ($ct>0) {
		//-- get the old id
		$old_m_media = $match[1];
		$objref = $objrec;
		$new_m_media = $old_m_media;
		$m_media = $new_m_media;
		if ($m_media != $old_m_media) {
			$objref = preg_replace("/@$old_m_media@/", "@$m_media@", $objref);
		}
	}
	//-- handle embedded OBJE records
	else {
		$m_media = get_new_xref("OBJE", true);
		$objref = subrecord_createobjectref($objrec, $objlevel, $m_media);

		//-- restructure the record to be a linked record
		$objrec = preg_replace("/ OBJE/", " @" . $m_media . "@ OBJE", $objrec);
		//-- renumber the lines
		$objrec = preg_replace("/^(\d+) /me", "($1-$objlevel).' '", $objrec);

		//-- check if another picture with the same file and title was previously imported
		$media = new Media($objrec);
		$new_media = Media::in_obje_list($media);
		if (!$new_media) {
			//-- add it to the media database table
			$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec) VALUES (?, ?, ?, ?, ?, ?, ?)"
				, array(get_next_id('media', 'm_id'), $m_media, $media->ext, $media->title, $media->file, $GEDCOMS[$FILE]["id"], $objrec));
			$media_count++;
			//-- if this is not an update then write it to the new gedcom file
			if (!$update && !empty ($fpnewged)) {
				fwrite($fpnewged, reformat_record_export($objrec));
			}
		} else {
			//-- already added so update the local id
			$objref = preg_replace("/@$m_media@/", "@$new_media@", $objref);
			$m_media = $new_media;
		}
	}
	if (isset($m_media)) {
		//-- add the entry to the media_mapping table
		$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}media_mapping (mm_id, mm_media, mm_gid, mm_order, mm_gedfile, mm_gedrec) VALUES (?, ?, ?, ?, ?, ?)"
				, array(get_next_id('media_mapping', 'mm_id'), $m_media, $gid, $count, $GEDCOMS[$FILE]['id'], $objref));
		return $objref;
	} else {
		print "Media reference error ".$objrec;
		return "";
	}
}

/**
* import media items from record
* @todo Decide whether or not to update the original gedcom file
* @return string an updated record
*/
function update_media($gid, $gedrec, $update = false) {
	global $GEDCOMS, $FILE, $TBLPREFIX, $media_count, $found_ids;
	global $zero_level_media, $fpnewged, $MAX_IDS, $keepmedia, $gBitDb;

	if (!isset ($media_count)) {
		$media_count = 0;
	}
	if (!isset ($found_ids)) {
		$found_ids = array ();
	}
	if (!isset ($zero_level_media)) {
		$zero_level_media = false;
	}
	if (!$update && !isset ($MAX_IDS["OBJE"])) {
		if (!$keepmedia) {
			$MAX_IDS["OBJE"] = 1;
		} else {
			$MAX_IDS["OBJE"]=
				$gBitDb->getOne(
					"SELECT ni_id FROM {$TBLPREFIX}nextid WHERE ni_type=? AND ni_gedfile=?"
					, array('OBJE', $GEDCOMS[$FILE]['id']));
		}
	}

	//-- handle level 0 media OBJE seperately
	$ct = preg_match("/0 @(.*)@ OBJE/", $gedrec, $match);
	if ($ct > 0) {
		$old_m_media = $match[1];
		$m_id = get_next_id("media", "m_id");
		/**
		* Hiding some code in order to fix a very annoying bug
		* [ 1579889 ] Upgrading breaks Media links
		*
		* Don't understand the logic of renumbering media objects ??
		*
		if ($update) {
			$new_m_media = $old_m_media;
		} else {
			if (isset ($found_ids[$old_m_media])) {
				$new_m_media = $found_ids[$old_m_media]["new_id"];
			} else {
				$new_m_media = get_new_xref("OBJE");
				$found_ids[$old_m_media]["old_id"] = $old_m_media;
				$found_ids[$old_m_media]["new_id"] = $new_m_media;
			}
		}
		**/
		$new_m_media = $old_m_media;
		//print "RECORD: old $old_m_media new $new_m_media<br />";
		$gedrec = preg_replace("/@" . $old_m_media . "@/", "@" . $new_m_media . "@", $gedrec);
		$media = new Media($gedrec);
		//--check if we already have a similar object
		$new_media = Media::in_obje_list($media);
		if (!$new_media) {
			$gBitDb->query(
				"INSERT INTO {$TBLPREFIX}media (m_id, m_media, m_ext, m_titl, m_file, m_gedfile, m_gedrec) VALUES (?, ?, ?, ?, ?, ?, ?)"
				, array($m_id, $new_m_media, $media->ext, $media->title, $media->file, $GEDCOMS[$FILE]["id"], $gedrec));
			$media_count++;
		} else {
			$new_m_media = $new_media;
			$found_ids[$old_m_media]["old_id"] = $old_m_media;
			$found_ids[$old_m_media]["new_id"] = $new_media;
			//$gedrec = preg_replace("/0 @(.*)@ OBJE/", "0 @$new_media@ OBJE", $gedrec);
			//-- record was replaced by a duplicate record so leave it out.
			return '';
		}
		return $gedrec;
	}

	if ($keepmedia) {
		$old_linked_media=
			$gBitDb->getAll(
				"SELECT mm_media, mm_gedrec FROM {$TBLPREFIX}media_mapping WHERE mm_gid=? AND mm_gedfile=?"
				, array($gid, $GEDCOMS[$FILE]['id']));
	}

	//-- check to see if there are any media records
	//-- if there aren't any media records then don't look for them just return
	$pt = preg_match("/\d OBJE/", $gedrec, $match);
	if ($pt > 0) {
	//-- go through all of the lines and replace any local
	//--- OBJE to referenced OBJEs
	$newrec = "";
	$lines = explode("\n", $gedrec);
	$ct_lines = count($lines);
	$inobj = false;
	$processed = false;
	$objlevel = 0;
	$objrec = "";
	$count = 1;
	foreach ($lines as $key => $line) {
		if (!empty ($line)) {
			// NOTE: Match lines that resemble n OBJE @0000@
			// NOTE: Renumber the old ID to a new ID and save the old ID
			// NOTE: in case there are more references to it
			$level = $line{0};
			//-- putting this code back since $objlevel, $objrec, etc vars will be
			//-- reset in sections after this
			if ($objlevel>0 && ($level<=$objlevel)) {
				$objref = insert_media($objrec, $objlevel, $update, $gid, $count);
				$count++;
				// NOTE: Add the new media object to the record
				$newrec .= $objref;

				// NOTE: Set the details for the next media record
				$objlevel = 0;
				$inobj = false;
			}
			if (preg_match("/[1-9]\sOBJE\s@(.*)@/", $line, $match) != 0) {
					// NOTE: Set object level
					$objlevel = $level;
					$inobj = true;
						$objrec = $line . "\n";
			} elseif (preg_match("/[1-9]\sOBJE/", $line, $match)) {
				// NOTE: Set the details for the next media record
				$objlevel = $level;
				$inobj = true;
					$objrec = $line . "\n";
			} else {
				$ct = preg_match("/(\d+)\s(\w+)(.*)/", $line, $match);
				if ($ct > 0) {
					if ($inobj) {
						$objrec .= $line . "\n";
					} else {
						$newrec .= $line . "\n";
					}
				} else {
					$newrec .= $line . "\n";
				}
			}
		}
	}
	//-- make sure the last line gets handled
	if ($inobj) {
		$objref = insert_media($objrec, $objlevel, $update, $gid, $count);
		$count++;
		$newrec .= $objref;

		// NOTE: Set the details for the next media record
		$objlevel = 0;
		$inobj = false;
	}
	}
	else $newrec = $gedrec;

	if ($keepmedia) {
		$newrec = trim($newrec)."\n";
		foreach ($old_linked_media as $i=>$row) {
			$newrec .= trim($row[1])."\n";
		}
	}

	return trim($newrec);
}

/**
* delete a gedcom from the database
*
* deletes all of the imported data about a gedcom from the database
* @param string $FILE the gedcom to remove from the database
* @param boolean $keepmedia Whether or not to keep media and media links in the tables
*/
function empty_database($ged_id, $keepmedia) {
	global $TBLPREFIX, $gBitDb;

	$gBitDb->query("DELETE FROM {$TBLPREFIX}individuals WHERE i_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}families    WHERE f_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}sources     WHERE s_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}other       WHERE o_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}places      WHERE p_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}placelinks  WHERE pl_file=?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}remotelinks WHERE r_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}name        WHERE n_file =?", array($ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}dates       WHERE d_file =?", array($ged_id));;

	if ($keepmedia) {
		$gBitDb->query("DELETE FROM {$TBLPREFIX}link   WHERE l_file    =? AND l_type<> ?", array($ged_id, 'OBJE'));
		$gBitDb->query("DELETE FROM {$TBLPREFIX}nextid WHERE ni_gedfile=? AND ni_type<>?", array($ged_id, 'OBJE'));
	} else {
		$gBitDb->query("DELETE FROM {$TBLPREFIX}link          WHERE l_file    =?", array($ged_id));
		$gBitDb->query("DELETE FROM {$TBLPREFIX}nextid        WHERE ni_gedfile=?", array($ged_id));
		$gBitDb->query("DELETE FROM {$TBLPREFIX}media         WHERE m_gedfile =?", array($ged_id));
		$gBitDb->query("DELETE FROM {$TBLPREFIX}media_mapping WHERE mm_gedfile=?", array($ged_id));
	}

	//-- clear all of the cache files for this gedcom
	clearCache();
}

//
// While importing a gedcom, we keep track of the the maximum ID values for each
// record type.  Write these to the database in one go.
//
function import_max_ids($ged_id, $MAX_IDS) {
	global $TBLPREFIX, $gBitDb;

	$gBitDb->query("DELETE FROM {$TBLPREFIX}nextid WHERE ni_gedfile=?", array($ged_id));
	foreach ($MAX_IDS as $type => $id) {
		$gBitDb->query(
			"INSERT INTO {$TBLPREFIX}nextid (ni_id, ni_type, ni_gedfile) VALUES (?, ?, ?)"
			, array($id+1, $type, $ged_id));
	}
}

/**
* read the contents of a gedcom file
*
* opens a gedcom file and reads the contents into the <var>$fcontents</var> global string
*/
function read_gedcom_file() {
	global $fcontents;
	global $GEDCOM, $GEDCOMS;
	global $pgv_lang;
	$fcontents = "";
	if (isset($GEDCOMS[$GEDCOM])) {
		//-- only allow one thread to write the file at a time
//		$mutex = new Mutex($GEDCOM);
//		$mutex->Wait();
		$fp = fopen($GEDCOMS[$GEDCOM]["path"], "r");
		$fcontents = fread($fp, filesize($GEDCOMS[$GEDCOM]["path"]));
		fclose($fp);
//		$mutex->Release();
	}
}

//-------------------------------------------- write_file
//-- this function writes the $fcontents back to the
//-- gedcom file
function write_file() {
	global $fcontents, $GEDCOMS, $GEDCOM, $INDEX_DIRECTORY;

	if (empty($fcontents)) {
		return;
	}
	if (preg_match("/0 TRLR/", $fcontents)==0) {
		$fcontents.="0 TRLR\n";
	}
	//-- write the gedcom file
	if (!is_writable($GEDCOMS[$GEDCOM]["path"])) {
		print "ERROR 5: GEDCOM file is not writable.  Unable to complete request.\n";
		AddToChangeLog("ERROR 5: GEDCOM file is not writable.  Unable to complete request. ->" . PGV_USER_NAME ."<-");
		return false;
	}
	//-- only allow one thread to write the file at a time
//	$mutex = new Mutex($GEDCOM);
//	$mutex->Wait();
	//-- what to do if file changed while waiting

	$fp = fopen($GEDCOMS[$GEDCOM]["path"], "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request.\n";
		AddToChangeLog("ERROR 6: Unable to open GEDCOM file resource.  Unable to complete request. ->" . PGV_USER_NAME ."<-");
		return false;
	}
	$fl = @flock($fp, LOCK_EX);
	if (!$fl) {
		AddToChangeLog("ERROR 7: Unable to obtain file lock. ->" . PGV_USER_NAME ."<-");
	}
	$fw = fwrite($fp, $fcontents);
	if ($fw===false) {
		print "ERROR 7: Unable to write to GEDCOM file.\n";
		AddToChangeLog("ERROR 7: Unable to write to GEDCOM file. ->" . PGV_USER_NAME ."<-");
		$fl = @flock($fp, LOCK_UN);
		fclose($fp);
		return false;
	}
	$fl = @flock($fp, LOCK_UN);
	fclose($fp);
	//-- always release the mutex
//	$mutex->Release();
	$logline = AddToLog($GEDCOMS[$GEDCOM]["path"]." updated");
	check_in($logline, basename($GEDCOMS[$GEDCOM]["path"]), dirname($GEDCOMS[$GEDCOM]["path"]));

	return true;;
}
/**
* Accpet changed gedcom record into database
*
* This function gets an updated record from the gedcom file and replaces it in the database
* @author John Finlay
* @param string $cid The change id of the record to accept
*/
function accept_changes($cid) {
	global $pgv_changes, $GEDCOM, $TBLPREFIX, $FILE, $GEDCOMS;
	global $INDEX_DIRECTORY, $SYNC_GEDCOM_FILE, $fcontents, $manual_save;

	if (isset ($pgv_changes[$cid])) {
		$changes = $pgv_changes[$cid];
		$change = $changes[count($changes) - 1];
		if ($GEDCOM != $change["gedcom"]) {
			$GEDCOM = $change["gedcom"];
		}
		$FILE = $GEDCOM;
		$gid = $change["gid"];
		$gedrec = $change["undo"];
		if (empty($gedrec)) {
			$gedrec = find_gedcom_record($gid);
		}

		update_record($gedrec, $change["type"]=="delete");

		//-- write the changes back to the gedcom file
		if ($SYNC_GEDCOM_FILE) {
			// TODO: We merge CONC lines on import, so need to add them back on export
			if (!isset($manual_save) || $manual_save==false) {
				//-- only allow one thread to accept changes at a time
//				$mutex = new Mutex("accept_changes");
//				$mutex->Wait();
			}

			if (empty($fcontents)) {
				read_gedcom_file();
			}
			if ($change["type"]=="delete") {
				$pos1=find_newline_string($fcontents, "0 @{$gid}@");
				if ($pos1!==false) {
					$pos2=find_newline_string($fcontents, "0", $pos1+5);
					if ($pos2===false) {
						$fcontents=substr($fcontents, 0, $pos1).'0 TRLR'.PGV_EOL;
						AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct");
					} else {
						$fcontents=substr($fcontents, 0, $pos1).substr($fcontents, $pos2);
					}
				} else {
					AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct.  Deleted gedcom record $gid was not found in the gedcom file.");
				}
			} elseif ($change["type"]=="append") {
				$pos1=find_newline_string($fcontents, "0 TRLR");
				$fcontents=substr($fcontents, 0, $pos1).reformat_record_export($gedrec).'0 TRLR'.PGV_EOL;
			} elseif ($change["type"]=="replace") {
				$pos1=find_newline_string($fcontents, "0 @{$gid}@");
				if ($pos1!==false) {
					$pos2=find_newline_string($fcontents, "0", $pos1+5);
					if ($pos2===false) {
						$fcontents=substr($fcontents, 0, $pos1).'0 TRLR'.PGV_EOL;
						AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct");
					} else {
						$fcontents=substr($fcontents, 0, $pos1).reformat_record_export($gedrec).substr($fcontents, $pos2);
					}
				} else {
					//-- attempted to replace a record that doesn't exist
					AddToLog("Corruption found in GEDCOM $GEDCOM Attempted to correct.  Replaced gedcom record $gid was not found in the gedcom file.");
					$pos1=find_newline_string($fcontents, "0 TRLR");
					$fcontents=substr($fcontents, 0, $pos1).reformat_record_export($gedrec).'0 TRLR'.PGV_EOL;
					AddToLog("Gedcom record $gid was appended back to the GEDCOM file.");
				}
			}
			if (!isset($manual_save) || $manual_save==false) {
				write_file();
//				$mutex->Release();
			}
		}

		if ($change["type"] != "delete") {
			//-- synchronize the gedcom record with any user account
			$username = get_user_from_gedcom_xref($GEDCOM, $gid);
			if ($username && get_user_setting($username, 'sync_gedcom')=='Y') {
				$firstname = get_gedcom_value("GIVN", 2, $gedrec);
				$lastname = get_gedcom_value("SURN", 2, $gedrec);
				if (empty ($lastname)) {
					$fullname = get_gedcom_value("NAME", 1, $gedrec, "", false);
					$ct = preg_match("~(.*)/(.*)/~", $fullname, $match);
					if ($ct > 0) {
						$firstname = $match[1];
						$lastname = $match[2];
					} else
						$firstname = $fullname;
				}
				//-- SEE [ 1753047 ] Email/sync with account
				$email = get_gedcom_value("EMAIL", 1, $gedrec);
				if (empty($email)) {
					$email = get_gedcom_value("_EMAIL", 1, $gedrec);
				}
				if (!empty($email)) {
					set_user_setting($username, 'email', $email);
				}
				set_user_setting($username, 'firstname', $firstname);
				set_user_setting($username, 'lastname',  $lastname);
			}
		}

		unset ($pgv_changes[$cid]);
		if (!isset($manual_save) || $manual_save==false) {
			write_changes();
		}
		$logline = AddToLog("Accepted change $cid " . $change["type"] . " into database");
		check_in($logline, $GEDCOM, dirname($GEDCOMS[$GEDCOM]['path']));
		if (isset ($change["linkpid"])) {
			accept_changes($change["linkpid"] . "_" . $GEDCOM);
		}
		return true;
	}
	return false;
}

// Find a string in a file, preceded by a any form of line-ending.
// Although PGV always writes them as PGV_EOL, it is possible that the file was
// edited externally by an editor that uses different endings.
function find_newline_string($haystack, $needle, $offset=0) {
	if ($pos=strpos($haystack, "\r\n{$needle}", $offset)) {
		return $pos+2;
	} elseif ($pos=strpos($haystack, "\n{$needle}", $offset)) {
		return $pos+1;
	} elseif ($pos=strpos($haystack, "\r{$needle}", $offset)) {
		return $pos+1;
	} else {
		return false;
	}
}

/**
* update a record in the database
* @param string $gedrec
*/
function update_record($gedrec, $delete = false) {
	global $TBLPREFIX, $GEDCOM, $FILE, $gBitDb;

	if (empty ($FILE)) {
		$FILE = $GEDCOM;
	}

	if (preg_match('/^0 @('.PGV_REGEX_XREF.')@ ('.PGV_REGEX_TAG.')/', $gedrec, $match)) {
		list(,$gid, $type)=$match;
	} else {
		print "ERROR: Invalid gedcom record.";
		return false;
	}

	$ged_id=get_id_from_gedcom($FILE);

	// TODO deleting unlinked places can be done more efficiently in a single query
	$placeids=
		$gBitDb->getOne(
			"SELECT pl_p_id FROM {$TBLPREFIX}placelinks WHERE pl_gid=? AND pl_file=?"
			, array($gid, $ged_id));

	$gBitDb->query("DELETE FROM {$TBLPREFIX}placelinks WHERE pl_gid=? AND pl_file=?", array($gid, $ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}dates      WHERE d_gid =? AND d_file =?", array($gid, $ged_id));

	//-- delete any unlinked places
	foreach ($placeids as $p_id) {
		$num =
			$gBitDb->getOne(
				"SELECT count(pl_p_id) FROM {$TBLPREFIX}placelinks WHERE pl_p_id=? AND pl_file=?"
				, array($p_id, $ged_id));
		if ($num==0) {
			$gBitDb->query("DELETE FROM {$TBLPREFIX}places WHERE p_id=? AND p_file=?", array($p_id, $ged_id));
		}
	}

	$gBitDb->query("DELETE FROM {$TBLPREFIX}media_mapping WHERE mm_gid=? AND mm_gedfile=?", array($gid, $ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}remotelinks WHERE r_gid=? AND r_file=?", array($gid, $ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}name WHERE n_id=? AND n_file=?", array($gid, $ged_id));
	$gBitDb->query("DELETE FROM {$TBLPREFIX}link WHERE l_from=? AND l_file=?", array($gid, $ged_id));

	switch ($type) {
	case 'INDI':
		$gBitDb->query("DELETE FROM {$TBLPREFIX}individuals WHERE i_id=? AND i_file=?", array($gid, $ged_id));
		break;
	case 'FAM':
		$gBitDb->query("DELETE FROM {$TBLPREFIX}families WHERE f_id=? AND f_file=?", array($gid, $ged_id));
		break;
	case 'SOUR':
		$gBitDb->query("DELETE FROM {$TBLPREFIX}sources WHERE s_id=? AND s_file=?", array($gid, $ged_id));
		break;
	case 'OBJE':
		$gBitDb->query("DELETE FROM {$TBLPREFIX}media WHERE m_media=? AND m_gedfile=?", array($gid, $ged_id));
		break;
	default:
		$gBitDb->query("DELETE FROM {$TBLPREFIX}other WHERE o_id=? AND o_file=?", array($gid, $ged_id));
		break;
	}

	if (!$delete) {
		import_record($gedrec, true);
	}
}

// Create a pseudo-random UUID
function uuid() {
	if (defined('PGV_USE_RFC4122')) {
		// Standards purists want this format (RFC4122)
		$fmt='%02X%02X%02X%02X-%02X%02X-%02X%02X-%02X%02X-%02X%02X%02X%02X%02X%02X';
	} else {
		// Most users want this format (for compatibility with PAF)
		$fmt='%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X%02X';
	}
	return sprintf(
		$fmt,
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255)&0x3f|0x80, // Set the version to random (10xxxxxx)
		rand(0, 255),
		rand(0, 255)&0x0f|0x40, // Set the variant to RFC4122 (0100xxxx)
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255),
		rand(0, 255)
	);
}

/**
* parse out specific subrecords (NOTE, _PRIM, _THUM) from a given OBJE record
*
* @author Joseph King
* @param string $objrec the OBJE record to retrieve the subrecords from
* @param int $objlevel the level of the OBJE record
* @param string $m_media that media id of the OBJE record
* @return string containing NOTE, _PRIM, and _THUM subrecords parsed from the passed object record
*/
function subrecord_createobjectref($objrec, $objlevel, $m_media) {

	//- level of subrecords is object record level + 1
	$level = $objlevel + 1;

	//- get and concatenate NOTE subrecords
	$n = 1;
	$nt = "";
	$note = "";
	do {
		$nt = get_sub_record($level, $level . " NOTE", $objrec, $n);
		if ($nt != "") {
			$note = $note . trim($nt)."\n";
		}
		$n++;
	} while ($nt != "");
	//- get and concatenate PRIM subrecords
	$n = 1;
	$pm = "";
	$prim = "";
	do {
		$pm = get_sub_record($level, $level . " _PRIM", $objrec, $n);
		if ($pm != "") {
			$prim = $prim . trim($pm)."\n";
		}
		$n++;
	} while ($pm != "");
	//- get and concatenate THUM subrecords
	$n = 1;
	$tm = "";
	$thum = "";
	do {
		$tm = get_sub_record($level, $level . " _THUM", $objrec, $n);
		if ($tm != "") {
			//- call image cropping function ($tm contains thum data)
			$thum = $thum . trim($tm)."\n";
		}
		$n++;
	} while ($tm != "");
	//- add object reference
	$objmed = addslashes($objlevel . ' OBJE @' . $m_media . "@\n" . $note . $prim . $thum);

	//- return the object media reference
	return $objmed;
}

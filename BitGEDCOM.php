<?php
/**
 * @version $Header$
 * @package phpgedview
 *
 * @author lsces <lester@lsces.co.uk
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyAttachable.php' );

class BitGEDCOM extends LibertyAttachable {
	var $mGEDCOMId;
	var $mGedcomName;

	function BitGEDCOM( $pGEDCOMId=NULL, $pContentId=NULL ) {
		LibertyAttachable::LibertyAttachable();
		$this->registerContentType( 'BitGEDCOM', array(
				'content_type_guid' => 'BitGEDCOM',
				'content_description' => 'Gedcom Archive',
				'handler_class' => 'BitGEDCOM',
				'handler_package' => 'gedcom',
				'handler_file' => 'BitGEDCOM.php',
				'maintainer_url' => 'http://www.bitweaver.org'
			) );
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = 'BitGEDCOM';
	}

	/**
	 * Check for valid GEDCOMId
	 * @param find Filter to be applied to the list
	 */
	function isValid() {
		return true;
	}

	/**
	 * Get number of GEDCOM archives available
	 * @param pGEDCOMId Check for a specific id, other wise return count
	 */
	function getCount( $pGEDCOMId=NULL ) {
		$cant = 0;
		if( $pGEDCOMId ) {
			if ( $this->isValid() ) $cant = 1;
		}
		else
		{	$query_cant = "SELECT COUNT(*)
			FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged
			INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ged.`g_content_id`)
			WHERE tc.`content_type_guid`='BitGEDCOM'";
			$cant = $this->mDb->getOne( $query_cant );
		}
		return $cant;		
	}

	/**
	 * Get the path to the GEDCOM file copy
	 * @param GEDFILENAME If set, create path to this file
	 */
	function getPath( $GEDFILENAME = "" ) {
		if ( $GEDFILENAME == "" )
			return "c:\\Data\\".$mGedcomName;
		else
			return "c:\\Data\\".$GEDFILENAME;
	}

	/**
	 * Get the GEDCOM file name
	 * @param GEDFILENAME If set, return, otherwise return elected file name 
	 */
	function getTitle( $GEDFILENAME = "" ) {
		if ( $GEDFILENAME == "" )
			return $mGedcomName;
		else 
			return $GEDFILENAME;
	}

	/**
	 * Load a GEDCOM archives
	 */
	function load() {
		if( $this->isValid() ) {
			global $gBitSystem, $gBitUser;
			$gateSql = NULL;
			$mid = NULL;
			if ( @$this->verifyId( $this->mGEDCOMId ) ) {
				$mid = " WHERE ged.`g_id` = ?";
				$bindVars = array($this->mImageId);
			} elseif ( @$this->verifyId( $this->mContentId ) ) {
				$mid = " WHERE ged.`content_id` = ?";
				$bindVars = array($this->mContentId);
			}
			if( $gBitSystem->isPackageActive( 'gatekeeper' ) ) {
				$gateSql = ' ,ts.`security_id`, ts.`security_description`, ts.`is_private`, ts.`is_hidden`, ts.`access_question`, ts.`access_answer` ';
				$mid = " LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_content_security_map` tcs ON ( tc.`content_id`=tcs.`content_id` )  LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_security` ts ON ( tcs.`security_id`=ts.`security_id` )
 						".$mid;
			}
			$sql = "SELECT ged.*, tc.* $gateSql
						, uue.`login` AS `modifier_user`, uue.`real_name` AS `modifier_real_name`
						, uuc.`login` AS `creator_user`, uuc.`real_name` AS `creator_real_name`
					FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged, `".BIT_DB_PREFIX."tiki_content` tc
						LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON (uue.`user_id` = tc.`modifier_user_id`)
						LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON (uuc.`user_id` = tc.`user_id`)
					$mid AND tc.`content_id` = tfi.`content_id`";
			if( $rs = $this->mDb->query($sql, array($bindVars)) ) {
				$this->mInfo = $rs->fields;

				$this->mGEDCOMId = $this->mInfo['g_id'];
				$this->mContentId = $this->mInfo['content_id'];

				$this->mInfo['creator'] = (isset( $rs->fields['creator_real_name'] ) ? $rs->fields['creator_real_name'] : $rs->fields['creator_user'] );
				$this->mInfo['editor'] = (isset( $rs->fields['modifier_real_name'] ) ? $rs->fields['modifier_real_name'] : $rs->fields['modifier_user'] );

/*				if( $gBitSystem->isPackageActive( 'gatekeeper' ) && !@$this->verifyId( $this->mInfo['security_id'] ) ) {
					// check to see if this image is in a protected gallery
					// this burns an extra select but avoids an big and gnarly LEFT JOIN sequence that may be hard to optimize on all DB's
					$query = "SELECT ts.* FROM `".BIT_DB_PREFIX."tiki_fisheye_gallery_image_map` tfgim
								INNER JOIN `".BIT_DB_PREFIX."tiki_content_security_map` tsm ON(tfgim.`gallery_content_id`=tsm.`content_id` )
								INNER JOIN `".BIT_DB_PREFIX."tiki_security` ts ON(tsm.`security_id`=ts.`security_id` )
							  WHERE tfgim.`item_content_id`=?";
					$grs = $this->mDb->query($query, array( $this->mContentId ) );
					if( $grs && $grs->RecordCount() ) {
						$this->mInfo = array_merge( $this->mInfo, $grs->fields );
					}
				}
*/

				LibertyAttachable::load();

				if (!empty($this->mStorage) && count($this->mStorage) > 0) {
					reset($this->mStorage);
					$this->mInfo['gedcom_file'] = current($this->mStorage);
				} else {
					$this->mInfo['gedcom_file'] = NULL;
				}
			}
		} else {
			// We don't have an gedcom_id or a content_id so there is no way to know what to load
			return NULL;
		}

		return count($this->mInfo);
	}

	/**
	 * Generate list of GEDCOM archives
	 * @param offset Number of the first record to list
	 * @param maxRecords Number of records to list
	 * @param sort_mode Order in which the records will be sorted
	 * @param find Filter to be applied to the list
	 */
	function getList($offset = 0, $maxRecords = -1, $sort_mode = 'title_desc', $find = '' ) {
		global $gBitSystem;

		$mid = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		if (is_array($find)) { // you can use an array of pages
			$mid = " AND tc.`title` IN (".implode(',',array_fill(0,count($find),'?')).")";
			$bindVars = array_merge($bindVars,$find);
		} elseif ( is_string($find) and $find != '' ) { // or a string
			$mid = " AND UPPER(tc.`title`) LIKE ? ";
			$bindVars = array_merge($bindVars,array('%' . strtoupper( $find ) . '%'));
		}

		$query = "SELECT
			uue.`login` AS modifier_user,
			uue.`real_name` AS modifier_real_name,
			uuc.`login` AS creator_user,
			uuc.`real_name` AS creator_real_name,
			`content_id`,
			tc.`title`,
			tc.`format_guid`,
			ged.`g_name` AS `name`,
			ged.`g_path` AS `path`,
			ged.`g_config` AS `config`,
			ged.`g_privacy` AS `privacy`,
			tc.`last_modified`,
			tc.`created`,
			`ip`,
			tc.`content_id`
				FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged
				INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ged.`g_content_id`),
				`".BIT_DB_PREFIX."users_users` uue,
				`".BIT_DB_PREFIX."users_users` uuc
				  WHERE tc.`content_type_guid`=?
				  AND tc.`modifier_user_id`=uue.`user_id`
				  AND tc.`user_id`=uuc.`user_id` $mid
				  ORDER BY ".$this->mDb->convert_sortmode( $sort_mode );
		$query_cant = "SELECT COUNT(*)
			FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged
			INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ged.`g_content_id`)
			WHERE tc.`content_type_guid`=? $mid";

		// If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil

		$this->mDb->StartTrans();
		$result = $this->mDb->query( $query, $bindVars, $maxRecords, $offset );
		$cant = $this->mDb->getOne( $query_cant, $bindVars );
		$this->mDb->CompleteTrans();
		$ret = array();
		while( $res = $result->fetchRow() ) {
			$aux = array();
			$aux = $res;
			$aux['creator'] = (isset( $res['creator_real_name'] ) ? $res['creator_real_name'] : $res['creator_user'] );
			$aux['editor'] = (isset( $res['modifier_real_name'] ) ? $res['modifier_real_name'] : $res['modifier_user'] );
			$aux['display_link'] = $this->getListLink( $aux );
			$aux['display_url'] = $this->getDisplayUrl( $aux['title'], $aux );
			$ret[] = $aux;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	
/**
 * check if Gedcom needs HEAD cleanup
 *
 * Find where position of the 0 HEAD gedcom start element, if one does not exist then complain
 * about the file not being a Gedcom.  If it is not at the first position in the file then
 * we need to trim off all of the extra stuff before the 0 HEAD
 * @return boolean	returns true if we need to cleanup the head, false if we don't
 * @see head_cleanup()
 */
function need_head_cleanup() {
	global $fcontents;

	$pos1 = strpos($fcontents, "0 HEAD");
	//-- don't force BOM cleanup
	if ($pos1>3) return true;
	else return false;
}

/**
 * cleanup the HEAD
 *
 * Cleans up the Gedcom header making sure that the 0 HEAD record is the very first thing in the file.
 * @return boolean	whether or not the cleanup was successful
 * @see need_head_cleanup()
 */
function head_cleanup() {
	global $fcontents;

	$pos1 = strpos($fcontents, "0 HEAD");
	if ($pos1>0) {
		$fcontents = substr($fcontents, $pos1);
		return true;
	}
	return false;
}

/**
 * check if there are double line endings
 *
 * Normally a gedcom should not have empty lines, this will check if the file has any empty lines in it
 * @return boolean	return true if the cleanup is needed
 * @see line_endings_cleanup()
 */
function need_line_endings_cleanup() {
	global $fcontents;

	$ct = preg_match("/\r\n(\r\n)+/", $fcontents);
	$ct += preg_match("/\r\r+/", $fcontents);
	$ct += preg_match("/\n\n+/", $fcontents);
	if ($ct>0) {
		return true;
	}
	return false;
}

/**
 * cleanup line endings
 *
 * this will remove any empty lines from the file
 * @return boolean	returns true if the operation was successful
 * @see need_line_endings_cleanup()
 */
function line_endings_cleanup() {
	global $fcontents;

	$ct = preg_match("/\r\n(\r\n)+/", $fcontents);
	$ct += preg_match("/\r\r+/", $fcontents);
	$ct += preg_match("/\n\n+/", $fcontents);
	if ($ct>0) {
		$fcontents = preg_replace(array("/(\r\n)+/", "/\r+/", "/\n+/"), array("\r\n", "\r", "\n"), $fcontents);
		return true;
	}
	else return false;
}

/**
 * check if we need to cleanup the places
 *
 * some programs, most notoriously FTM, put data in the PLAC field when it should be on the same line
 * as the event.  For example:<code>1 SSN
 * 2 PLAC 123-45-6789</code> Should really be: <code>1 SSN 123-45-6789</code>
 * this function checks if this exists
 * @return boolean	returns true if the cleanup is needed
 * @see place_cleanup()
 */
function need_place_cleanup()
{
	global $fcontents;
	//$ct = preg_match("/SOUR.+(Family Tree Maker|FTW)/", $fcontents);
	//if ($ct==0) return false;
	$ct = preg_match_all ("/^1 (CAST|DSCR|EDUC|IDNO|NATI|NCHI|NMR|OCCU|PROP|RELI|SSN|TITL|_MILI|_FA1|_FA2|_FA3|_FA4|_FA5|_FA6)(\s*)$[\s]+(^2 TYPE(.*)[\s]+)?(^2 DATE(.*)[\s]+)?^2 PLAC (.*)$/m",$fcontents,$matches, PREG_SET_ORDER);
	if($ct>0)
	  return $matches[0];
	return false;
}

/**
 * clean up the bad places found by the need_place_cleanup() function
 * @return boolean	returns true if cleanup was successful
 * @see need_place_cleanup()
 */
function place_cleanup()
{
	global $fcontents;

//searchs for '1 CAST|DSCR|EDUC|IDNO|NATI|NCHI|NMR|OCCU|PROP|RELI|SSN|TITL #chars\n'
//				    'optional 2 TYPE #chars\n'
//						'optional 2 DATE #chars\n'
//						'2 PLAC #chars'
// and replaces the 1 level #chars with the PLAC #chars and blanks out the PLAC
$fcontents = preg_replace("/^1 (CAST|DSCR|EDUC|IDNO|NATI|NCHI|NMR|OCCU|PROP|RELI|SSN|TITL|_MILI|_FA1|_FA2|_FA3|_FA4|_FA5|_FA6)(\s*)$[\s]+(^2 TYPE(.*)[\s]+)?(^2 DATE(.*)[\s]+)?^2 PLAC (.*)$/m",
					 fixreplaceval('$1','$7','$3','$5'),$fcontents);
return true;
}

//used to create string to be replaced back into GEDCOM
function fixreplaceval($val1,$val7,$val3,$val5)
{
  $val = "1 ".$val1." ".trim($val7)."\n";
  //trim off trailing spaces
  $val3 = rtrim($val3);
	if(!empty($val3))
	  $val = $val.$val3;

	//trim off trailing spaces
  $val5 = rtrim($val5);
	if(!empty($val5))
	{
	  $val = $val.$val5;
	}

	//$val = $val."\r\n2 PLAC";
	return trim($val);
}


/**
 * check if we need to cleanup the dates
 *
 * Valid gedcom dates are in the form DD MMM YYYY (ie 01 JAN 2004).  However many people will enter
 * dates in an incorrect format.  This function checks if dates have been entered incorrectly.
 * This function will detect dates in the form YYYY-MM-DD, DD-MM-YYYY, and MM-DD-YYYY.  It will also 
 * look for \ / - and . as delimeters.
 * @return boolean	returns true if the cleanup is needed
 * @see date_cleanup()
 */
function need_date_cleanup()
{
	global $fcontents;
  $ct = preg_match_all ("/DATE[^\d]+(\d\d\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d)/",$fcontents,$matches, PREG_SET_ORDER);
	if($ct>0) {
		//print_r($matches);
	  	return $matches[0];
  	}
	else
	{
  		$ct = preg_match_all ("/DATE[^\d]+(\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d\d\d)/",$fcontents,$matches, PREG_SET_ORDER);
		if($ct>0) {
			//print_r($matches);
			$matches[0]["choose"] = true;
			return $matches[0];
		}
		else {
			$ct = preg_match_all ("/DATE ([^\d]+) [0-9]{1,2}, (\d\d\d\d)/",$fcontents,$matches, PREG_SET_ORDER);
			if($ct>0) {
				//print_r($matches);
				return $matches[0];
			}
			else {
				$ct = preg_match_all("/DATE (\d\d)[^\s]([^\d]+)[^\s](\d\d\d\d)/", $fcontents, $matches, PREG_SET_ORDER);
				if($ct>0) {
					//print_r($matches);
					return $matches[0];
				}
			}
		}
	}
	return false;
}

function changemonth($monval)
{
		if($monval=="01") return "JAN";
		else if($monval=="02") return "FEB";
		else if($monval=="03") return "MAR";
		else if($monval=="04") return "APR";
		else if($monval=="05") return "MAY";
		else if($monval=="06") return "JUN";
		else if($monval=="07") return "JUL";
		else if($monval=="08") return "AUG";
		else if($monval=="09") return "SEP";
		else if($monval=="10") return "OCT";
		else if($monval=="11") return "NOV";
		else if($monval=="12") return "DEC";
		return $monval;
}

function fix_date($datestr) {
	$date = parse_date($datestr);
	if (isset($date[0])) return $date[0]["day"]." ".str2upper($date[0]["month"])." ".$date[0]["year"];
	else return $datestr;
}
/**
 * clean up the bad dates found by the need_date_cleanup() function
 * @return boolean	returns true if cleanup was successful
 * @see need_date_cleanup()
 */
function date_cleanup($dayfirst=1)
{
	global $fcontents;

	// convert all dates with anything but spaces as delimmeters
	$fcontents = preg_replace("/DATE (\d\d)[^\s]([^\d]+)[^\s](\d\d\d\d)/", "DATE $1 $2 $3", $fcontents);
  //convert all dates in YYYY-MM-DD or YYYY/MM/DD or YYYY\MM\DD format to DD MMM YYYY format
	$fcontents = preg_replace("/DATE[^\d]+(\d\d\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d)/e", "'DATE $3 '.changemonth('$2').' $1'", $fcontents);
	$fcontents = preg_replace("/DATE ([^\d]+ [0-9]{1,2}, \d\d\d\d)/e", "'DATE '.fix_date('$1').''", $fcontents);

	//day first in date format
	if($dayfirst==1)
	{
  	//convert all dates in DD-MM-YYYY or DD/MM/YYYY or DD\MM\YYYY to DD MMM YYYY format
	  $fcontents = preg_replace("/DATE[^\d]+(\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d\d\d)/e", "'DATE $1 '.changemonth('$2').' $3'", $fcontents);
	}
	else if ($dayfirst==2) //month first
	{
	  //convert all dates in MM-DD-YYYY or MM/DD/YYYY or MM\DD\YYYY to DD MMM YYYY format
		$fcontents = preg_replace("/DATE[^\d]+(\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d\d\d)/e", "'DATE $2 '.changemonth('$1').' $3'", $fcontents);
	}

	return true;
}

/**
 * check if we need to cleanup the MAC style line endings
 *
 * PGV runs better with DOS (\r\n) or UNIX (\n) style line endings.  This function checks if 
 * Mac (\r) style line endings are used in the gedcom file.
 * @return boolean	returns true if the cleanup is needed
 * @see macfile_cleanup()
 */
function need_macfile_cleanup()
{
	global $fcontents;
  //check to see if need macfile cleanup
  $ct = preg_match_all ("/\x0d[\d]/m",$fcontents,$matches);
  if($ct > 0)
	  return true;
  return false;
}

/**
 * clean up the Mac (\r) line endings found by the need_macfile_cleanup() function
 * @return boolean	returns true if cleanup was successful
 * @see need_macfile_cleanup()
 */
function macfile_cleanup()
{
	global $fcontents;
  //replace all only \r (MAC files) with \r\n (DOS files)
  $fcontents = preg_replace("/\x0d([\d])/","\x0d\x0a$1", $fcontents);
  return true;
}

/**
 * convert XREFs to the value of another tag in the gedcom record
 *
 * Some genealogy applications do not maintain the gedcom XREF IDs between gedcom exports
 * but instead use another Identifying tag in the Gedcom record.  This function will allow
 * the admin to replace the XREF IDs with the value of another tag.  So for example you could replace
 * the 0 @I1@ INDI with the value of the RIN tag R101 making the line look like this 0 @R101@ INDI
 * @param String $tag	the alternate tag in the gedcom record to use when replacing the xref id, defaults to RIN
 */
function xref_change($tag="RIN")
{
	global $fcontents;
  //-- find all of the XREFS in the file
  $ct = preg_match_all("/0 @(.*)@ INDI/", $fcontents, $match, PREG_SET_ORDER);
  for($i=0; $i<$ct; $i++) {
  	$xref = trim($match[$i][1]);
  	$indirec = find_record_in_file($xref);
  	if ($indirec!==false) {
		  $rt = preg_match("/1 NAME (.*)/", $indirec, $rmatch);
			if($rt>0)
			{
			  $name = trim($rmatch[1])." (".$xref.")";
			  $name = preg_replace("/\//","",$name);
			}
			else
			  $name = $xref;
//  		print "Found record $i - $name: ";
  		$rt = preg_match("/1 $tag (.*)/", $indirec, $rmatch);
  		if ($rt>0) {
  			$rin = trim($rmatch[1]);
  			$fcontents = preg_replace("/@$xref@/", "@$rin@", $fcontents);
//  			print "successfully set to $rin<br />\n";
  		}
  		else   print "<span class=\"error\">No $tag found in record<br /></span>\n";
  	}
  }
	return true;
}

/**
 * Check for ANSI encoded file
 *
 * Check the gedcom for an ansi encoded file to convert to UTF-8
 * @return boolean 	returns true if the file claims to be ANSI encoded
 * @see convert_ansi_utf8()
 */
function is_ansi() {
	global $fcontents;

	return preg_match("/1 CHAR (ANSI|ANSEL)/", $fcontents);
}

/**
 * Convert an ANSI encoded file to UTF8
 *
 * converts an ANSI or ANSEL encoded file to UTF-8
 * @see is_ansi()
 */
function convert_ansi_utf8() {
	global $fcontents;

	$fcontents = utf8_encode($fcontents);
	$fcontents = preg_replace("/1 CHAR (ANSI|ANSEL)/", "1 CHAR UTF-8", $fcontents);
}
	
}
?>

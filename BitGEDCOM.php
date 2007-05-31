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
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
require_once('includes/functions.php');
require_once('includes/functions_import.php');

class BitGEDCOM extends LibertyContent {
	var $mGEDCOMId;
	var $mGedcomName;

	function BitGEDCOM( $pGEDCOMId=NULL, $pContentId=NULL ) {
		LibertyContent::LibertyContent();
		$this->registerContentType( 'BitGEDCOM', array(
				'content_type_guid' => 'bitGEDCOM',
				'content_description' => 'Gedcom Archive',
				'handler_class' => 'BitGEDCOM',
				'handler_package' => 'phpgedview',
				'handler_file' => 'BitGEDCOM.php',
				'maintainer_url' => 'http://home.lsces.co.uk/lsces'
			) );
		$this->mGEDCOMId = $pGEDCOMId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = 'bitGEDCOM';

		if( ! @$this->verifyId( $this->mGEDCOMId ) ) {
			$this->mGEDCOMId = NULL;
		}
		if( ! @$this->verifyId( $this->mContentId ) ) {
			$this->mContentId = NULL;
		}
	}

	/**
	 * Check for valid GEDCOMId
	 * @param find Filter to be applied to the list
	 */
	function isValid() {
		return( $this->verifyId( $this->mGEDCOMId ) );
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
			INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = ged.`g_content_id`)
			WHERE tc.`content_type_guid`='bitGEDCOM'";
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
		if( $this->verifyId( $this->mGEDCOMId ) || $this->verifyId( $this->mContentId ) ) {
			$lookupColumn = @BitBase::verifyId( $this->mGEDCOMId ) ? 'id' : 'content_id';

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mGEDCOMId )? $this->mGEDCOMId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$sql = "SELECT ged.*, lc.*
						, uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name
						, uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name $selectSql
					FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = ged.`g_content_id`) $joinSql 
						LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON (uue.`user_id` = lc.`modifier_user_id`)
						LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON (uuc.`user_id` = lc.`user_id`)
					WHERE ged.`g_$lookupColumn`=? $whereSql";

			if( $rs = $this->mDb->query($sql, array($bindVars)) ) {
				$this->mInfo = $rs->fields;

				$this->mGEDCOMId = $this->mInfo['g_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mGedcomName = $this->mInfo['g_name'];
				
				$this->mInfo['creator'] = (isset( $rs->fields['creator_real_name'] ) ? $rs->fields['creator_real_name'] : $rs->fields['creator_user'] );
				$this->mInfo['editor'] = (isset( $rs->fields['modifier_real_name'] ) ? $rs->fields['modifier_real_name'] : $rs->fields['modifier_user'] );

				LibertyContent::load();

				if (!empty($this->mStorage) && count($this->mStorage) > 0) {
					reset($this->mStorage);
					$this->mInfo['gedcom_file'] = current($this->mStorage);
				} else {
					$this->mInfo['gedcom_file'] = NULL;
				}
			}
		} else {
			// We don't have an gedcom_id or a content_id so there is no way to know what to load
			$this->mGEDCOMId = NULL;
			return NULL;
		}

		return count($this->mInfo);
	}

	/**
	* This is the ONLY method that should be called in order to store (create or update) a wiki page!
	* It is very smart and will figure out what to do for you. It should be considered a black box.
	*
	* @param array pParams hash of values that will be used to store the page
	*
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	*
	* @access public
	**/
	function store( &$pParamHash ) {
		$this->mDb->StartTrans();

		if( $this->verify( $pParamHash ) && LibertyContent::store( $pParamHash ) ) {

			$table = PHPGEDVIEW_DB_PREFIX."gedcom";
			if( $this->verifyId( $this->mGEDCOMId ) ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['gedcom_store'], array( "g_id" => $this->mGEDCOMId ) );

			} else {
				$pParamHash['gedcom_store']['g_content_id'] = $pParamHash['content_id'];
				if( @$this->verifyId( $pParamHash['g_id'] ) ) {
					// if pParamHash['g_id'] is set, someone is requesting a particular g_id. Use with caution!
					$pParamHash['gedcom_store']['g_id'] = $pParamHash['g_id'];
				} else {
					$pParamHash['gedcom_store']['g_id'] = $this->mDb->GenID( 'gedview_id_seq');
				}
				$this->mGEDCOMId = $pParamHash['gedcom_store']['g_id'];

				$result = $this->mDb->associateInsert( $table, $pParamHash['gedcom_store'] );
			}
			// Access new data for notifications
			$this->load();

		}
		$this->mDb->CompleteTrans();
		return( count( $this->mErrors ) == 0 );
	}
	
	/**
	* This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	* NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	*
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	*
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	*
	* @access private
	**/
	function verify( &$pParamHash ) {
		global $gBitUser;

		// make sure we're all loaded up of we have a mPageId
		if( $this->verifyId( $this->mGEDCOMId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( isset( $this->mInfo['content_id'] ) && $this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		if( @$this->verifyId( $pParamHash['content_id'] ) ) {
			$pParamHash['gedcom_store']['g_content_id'] = $pParamHash['content_id'];
		}

		// check for name issues, first truncate length if too long
		if( empty( $pParamHash['gedcom_name'] ) ) {
			$this->mErrors['title'] = 'You must specify a gedcom name';
		} elseif( !empty( $pParamHash['gedcom_name']) || !empty($this->mGedcomName))  {
			if( !$this->verifyId( $this->mGEDCOMId ) ) {
				if( empty( $pParamHash['gedcom_name'] ) ) {
					$this->mErrors['title'] = 'You must enter a name for this gedcom.';
				} else {
					$pParamHash['content_store']['title'] = substr( $pParamHash['gedcom_name'], 0, 160 );
				}
			} else {
				$pParamHash['content_store']['title'] = ( isset( $pParamHash['gedcom_name'] ) ) ? substr( $pParamHash['gedcom_name'], 0, 160 ) : $this->mGedcomName;
			}
		}
		
		if( empty( $pParamHash['source'] ) )
			$pParamHash['gedcom_store']['g_path'] = $pParamHash['content_store']['title'].".GED";
		else
			$pParamHash['gedcom_store']['g_path'] = $pParamHash['source'];
		if( empty( $pParamHash['name'] ) )
			$pParamHash['gedcom_store']['g_name'] = basename($pParamHash['gedcom_store']['g_path']);
		else
			$pParamHash['gedcom_store']['g_name'] = $pParamHash['name'];
		if( empty( $pParamHash['g_config'] ) )
			$pParamHash['gedcom_store']['g_config'] = $pParamHash['content_store']['title'].".GED_conf.GED";
		else
			$pParamHash['gedcom_store']['g_config'] = $pParamHash['g_config'];
		if( empty( $pParamHash['privacy'] ) )
			$pParamHash['gedcom_store']['g_privacy'] = $pParamHash['content_store']['title'].".GED_priv.GED";
		else
			$pParamHash['gedcom_store']['g_privacy'] = $pParamHash['privacy'];
		
		if( !empty( $pParamHash['minor'] ) && $this->isValid() ) {
			// we can only minor save over our own versions
			if( !$gBitUser->isRegistered() || ($this->mInfo['modifier_user_id'] != $gBitUser->mUserId && !$gBitUser->isAdmin()) ) {
				unset( $pParamHash['minor'] );
			}
		}

		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * Remove gedcom record from database
	 */
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$dbged = $this->mGEDCOMId;
			
			if ( expungeGedcom($dbged) ) {
				$this->mDb->StartTrans();
				$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."gedcom WHERE g_id=?";
				$res = $this->mDb->query( $sql, array( $dbged ) );
				if( LibertyContent::expunge() ) {
					$ret = TRUE;
					$this->mDb->CompleteTrans();
				} else {
					$this->mDb->RollbackTrans();
				}
			}
		}
		return $ret;
	}

	/**
	 * Remove gedcom content from database
	 * TODO - Monitor failures during delets and fail
	 */
	function expungeGedcom( $dbged ) {
		if ( $dbged > 0 ) {
			$this->mDb->StartTrans();
			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."dates WHERE d_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."families WHERE f_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."favorites WHERE fv_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."individuals WHERE i_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media WHERE m_gedfile=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."media_mapping WHERE mm_gedfile=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."names WHERE n_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."nextid WHERE ni_gedfile=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."other WHERE o_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."placelinks WHERE pl_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."places WHERE p_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );

			$sql = "DELETE FROM ".PHPGEDVIEW_DB_PREFIX."sources WHERE s_file=?";
			$res = $this->mDb->query( $sql, array( $dbged ) );
			$this->mDb->CompleteTrans();
		}
		return true;
	}
	
	/**
	 * Generate list of GEDCOM archives
	 * $pListHash valuse used
	 * @param offset Number of the first record to list
	 * @param maxRecords Number of records to list
	 * @param sort_mode Order in which the records will be sorted
	 * @param find Filter to be applied to the list
	 */
	function getList( &$pListHash ) {
		LibertyContent::prepGetList( $pListHash );

		$whereSql = $joinSql = $selectSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		$query = "SELECT
					uue.`login` AS modifier_user,
					uue.`real_name` AS modifier_real_name,
					uuc.`login` AS creator_user,
					uuc.`real_name` AS creator_real_name,
					lc.`title`, 
					lc.`format_guid`,
					ged.g_id,
					ged.`g_name` AS `name`,
					ged.`g_path` AS `path`,
					ged.`g_config` AS `config`,
					ged.`g_privacy` AS `privacy`,
					ged.`g_commonsurnames` AS `commonsurnames`,
					lc.`last_modified`,
					lc.`created`,
					lc.`ip`,
					lc.`content_id`
				FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = ged.`g_content_id`)
				INNER JOIN `".BIT_DB_PREFIX."users_users` uuc ON ( uuc.`user_id` = lc.`user_id` )
				INNER JOIN `".BIT_DB_PREFIX."users_users` uue ON ( uue.`user_id` = lc.`user_id` )
				$joinSql
				WHERE lc.`content_type_guid`=? $whereSql
				ORDER BY ".$this->mDb->convertSortmode( $pListHash['sort_mode'] );
		$query_cant = "SELECT COUNT(*)
			FROM `".PHPGEDVIEW_DB_PREFIX."gedcom` ged
			INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.`content_id` = ged.`g_content_id`)
			$joinSql
			WHERE lc.`content_type_guid`=? $whereSql";

		// If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil

		$ret = array();
		$this->mDb->StartTrans();
		$result = $this->mDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );
		$cant = $this->mDb->getOne( $query_cant, $bindVars );
		$this->mDb->CompleteTrans();
		while( $res = $result->fetchRow() ) {
			$aux = array();
			$aux = $res;
			$aux['creator'] = (isset( $res['creator_real_name'] ) ? $res['creator_real_name'] : $res['creator_user'] );
			$aux['editor'] = (isset( $res['modifier_real_name'] ) ? $res['modifier_real_name'] : $res['modifier_user'] );
//			$aux['display_link'] = $this->getListLink( $aux );
//			$aux['display_url'] = $this->getDisplayUrl( $aux['title'], $aux );
			$aux['individuals'] = $this->mDb->getOne( "SELECT COUNT(*) FROM `".PHPGEDVIEW_DB_PREFIX."INDIVIDUALS` WHERE `i_file`=?", array( $res["g_id"] ));
			$aux['families'] 	= $this->mDb->getOne( "SELECT COUNT(*) FROM `".PHPGEDVIEW_DB_PREFIX."FAMILIES` WHERE `f_file`=?", array( $aux["g_id"] ));
			$ret[] = $aux;
		}

		$pListHash['cant'] = $cant;
		LibertyContent::postGetList( $pListHash );
		return $ret;
	}
	
/**
 * Convert an ANSI encoded file to UTF8
 *
 * converts an ANSI or ANSEL encoded file to UTF-8
 * @see is_ansi()
 */
function storeGedcom( &$storeHash ) {
	$this->store( $storeHash );
	$this->importGedcom();
	$this->mErrors = "Loaded OK";
}

/**
 * Convert an ANSI encoded file to UTF8
 *
 * converts an ANSI or ANSEL encoded file to UTF-8
 * @see is_ansi()
 */
function importGedcom() {
	// -- array of names
	if (!isset ($indilist))
	$indilist = array ();
	if (!isset ($famlist))
	$famlist = array ();
	$sourcelist = array ();
	$otherlist = array ();

global $GEDCOMS, $GEDCOM;
$GEDCOM = basename($this->mInfo['g_path']);
$GEDCOMS[$GEDCOM]["id"] = $this->mInfo['g_id'];

	//-- as we are importing the file, a new file is being written to store any
	//-- changes that might have occurred to the gedcom file (eg. conversion of
	//-- media objects).  After the import is complete the new file is
	//-- copied over the old file.
	//-- The records are written during the import_record() method and the
	//-- update_media() method
	//-- open handle to read file
	$fpged = fopen($this->mInfo['g_path'], "rb");
	//-- open handle to write changed file
	$fpnewged = fopen(STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/".basename($this->mInfo['g_path']).".new", "ab");
	$BLOCK_SIZE = 1024 * 4; //-- 4k bytes per read (4kb is usually the page size of a virtual memory system)


		$fcontents = "";
		$TOTAL_BYTES = 0;
		$place_count = 0;
		$date_count = 0;
		$media_count = 0;
		$MAX_IDS = array();
		$listtype = array();
		$_SESSION["resumed"] = 1;

	while (!feof($fpged)) {
		$temp = fread($fpged, $BLOCK_SIZE);
		$fcontents .= $temp;
		$TOTAL_BYTES += strlen($temp);
		$pos1 = 0;
		while ($pos1 !== false) {
			//-- find the start of the next record
			$pos2 = strpos($fcontents, "\n0", $pos1 +1);
			while ((!$pos2) && (!feof($fpged))) {
				$temp = fread($fpged, $BLOCK_SIZE);
				$fcontents .= $temp;
				$TOTAL_BYTES += strlen($temp);
				$pos2 = strpos($fcontents, "\n0", $pos1 +1);
			}

			//-- pull the next record out of the file
			if ($pos2)
			$indirec = substr($fcontents, $pos1, $pos2 - $pos1);
			else
			$indirec = substr($fcontents, $pos1);

			//-- remove any extra slashes
			$indirec = preg_replace("/\\\/", "/", $indirec);

			//-- import anything that is not a blob
			if (preg_match("/\n1 BLOB/", $indirec) == 0) {
global $gid;
				import_record(trim($indirec));
				$place_count += update_places($gid, $indirec);
				$date_count += update_dates($gid, $indirec);
			}

			//-- move the cursor to the start of the next record
			$pos1 = $pos2;

		}
		$fcontents = substr($fcontents, $pos2);
	}
	fclose($fpged);
	fclose($fpnewged);
	//-- as we are importing the file, a new file is being written to store any
	//-- changes that might have occurred to the gedcom file (eg. conversion of
	//-- media objects).  After the import is complete the new file is
	//-- copied over the old file.
	//-- The records are written during the import_record() method and the
	//-- update_media() method
	$res = @ copy($this->mInfo['g_path'], PHPGEDVIEW_PKG_NAME.basename($GEDCOM_FILE).".bak");
	if (!$res)
		$this->mError = "Unable to create backup of the GEDCOM file at ".STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/".basename($this->mInfo['g_path']).".bak";
	//unlink($GEDCOM_FILE);
	$res = @ copy(STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/".basename($this->mInfo['g_path']).".new", $this->mInfo['g_path']);
	if (!$res) {
		$this->mError = "Unable to copy updated GEDCOM file ".STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/".basename($this->mInfo['g_path']).".new to ".$this->mInfo['g_path'];
	} else {
		@unlink(STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/".basename($this->mInfo['g_path']).".new");
		$logline = $this->addToLog($this->mInfo['g_path']." updated by >".getUserName()."<");
//		if (!empty ($COMMIT_COMMAND))
//		check_in($logline, $GEDCOM_FILE, $INDEX_DIRECTORY);
	}
}

/**
 * add a message into the log-file
 * @param string $LogString		the message to add
 * @param boolean $savelangerror
 * @return string returns the log line if successfully inserted into the log
 */
function addToLog($LogString, $savelangerror=false) {
	global $LOGFILE_CREATE;

	$wroteLogString = false;

	if (empty($LOGFILE_CREATE)) $LOGFILE_CREATE="daily";
	if ($LOGFILE_CREATE=="none") return;

	//-- do not allow code to be written to the log file
	$LogString = preg_replace("/<\?.*\?>/", "*** CODE DETECTED ***", $LogString);

	$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
	if ($LOGFILE_CREATE !== "none" && $savelangerror === false) {
		if ($LOGFILE_CREATE=="daily") $logfile = STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/pgv-" . date("Ymd") . ".log";
		if ($LOGFILE_CREATE=="weekly") $logfile = STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/pgv-" . date("Ym") . "-week" . date("W") . ".log";
		if ($LOGFILE_CREATE=="monthly") $logfile = STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/pgv-" . date("Ym") . ".log";
		if ($LOGFILE_CREATE=="yearly") $logfile = STORAGE_PKG_PATH.PHPGEDVIEW_PKG_NAME."/pgv-" . date("Y") . ".log";
		if (is_writable(PHPGEDVIEW_PKG_PATH."index")) {
			$logline = date("d.m.Y H:i:s") . " - " . $REMOTE_ADDR . " - " . $LogString . "\r\n";
			$fp = fopen($logfile, "a");
			flock($fp, 2);
			fputs($fp, $logline);
			flock($fp, 3);
			fclose($fp);
			$wroteLogString = true;
		}
	}
	if ($wroteLogString) return $logline;
	else return "";
}

}
?>

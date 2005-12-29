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
	var $mPageName;

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
				FROM `".BIT_DB_PREFIX."pgv_gedcom` ged
				INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON (tc.`content_id` = ged.`g_content_id`),
				`".BIT_DB_PREFIX."users_users` uue,
				`".BIT_DB_PREFIX."users_users` uuc
				  WHERE tc.`content_type_guid`=?
				  AND tc.`modifier_user_id`=uue.`user_id`
				  AND tc.`user_id`=uuc.`user_id` $mid
				  ORDER BY ".$this->mDb->convert_sortmode( $sort_mode );
		$query_cant = "SELECT COUNT(*)
			FROM `".BIT_DB_PREFIX."pgv_gedcom` ged
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
}
?>

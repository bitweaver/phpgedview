<?php
/**
 *  PGV SOAP implementation of the genealogy web service
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage Webservice
 * @version $Id: PGVServiceLogic.class.php,v 1.2 2009/04/30 17:41:29 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_PGV_SERVICELOGIC_CLASS_PHP', '');

require_once 'webservice/genealogyService.php';
require_once 'includes/functions/functions_edit.php';
require_once 'includes/classes/class_gewebservice.php';

class PGVServiceLogic extends GenealogyService {

	/**
	 * Check for the availability of compression libs
	 *
	 * @return string comma delimited list of supported libs
	 * @todo Change return type to an array
	 **/
	function getCompressionLibs() {
		$libs = 'none,';

		//zlib compression
		if (function_exists('gzcompress')) {

			$libs .= 'zlib,';
		}
		//pgv zip
		if (file_exists('includes/pclzip.lib.php')) {
			$libs .= 'zip,';
		}
		//trim the string
		$list = substr($libs,0,strlen($libs)-1);
		return $list;
	}


	/**
	 * Sets the compression lib to use for the authenticated user
	 *
	 * @param string $lib The string of the compression library to use
	 * @return string returns string name of compression to use
	 */
	function setCompression($lib) {
		//default compression if nothing is provided
		if ($lib == '' || $lib == null) {
			return 'none';
		}
		//get the list
		$compression_list = $this->getCompressionLibs();
		//set the compression, use none if they dont' match
		if (strstr($compression_list,$lib) !== false) {
			return $lib;
		} else {
			return 'none';
		}
	}
	/**
	 * Sets the default gedcom to use for all methods
	 *
	 * @param string gedcom id
	 * @return string gedcom id that will be used
	 **/
	function default_gedcom($gedcom_id='') {
		global $GEDCOMS, $GEDCOM;

		if (is_array($GEDCOMS)) {
			foreach ($GEDCOMS as $ged=>$gedarray) {
				if ($gedcom_id === $ged) {

					return $ged;
			}
		}
		}
		//return the first gedcom on the list if no gedcom was matched
		return $GEDCOM;
	}

	/**
	 * Authenticates user and password. Sets compression method to use
	 * on data transfers.  Also supports guests
	 *
	 * @param string $username the username for the user attempting to login
	 * @param string $password the plain text password to test
	 * @param string $compression the compression library to use
	 * @param string $type specifies a raw data type with current valid values of GEDCOM, or GRAMPS
	 * @return mixed If login sucessful: returns session id, message and compression
	 *		library that is being used. If login unsucessful: returns a SOAP_Fault
	 * @todo implement banning
	 */
	function postAuthenticate($username, $password, $gedcom_id, $compression,$data_type="GEDCOM") {
		global $GEDCOM;

		$GEDCOM = $this->default_gedcom($gedcom_id);
		$compress_method = $this->setCompression($compression);
		$sid = session_id();
		//guest auth
		if (empty($username) && !$REQUIRE_AUTHENTICATION) {
			$_SESSION["GEDCOM"] = $GEDCOM;
			$_SESSION["compression"] = $compress_method;
			$_SESSION["data_type"] = $data_type;
			$_SESSION["readonly"] = true;
			//soap return
			$return['SID'] = $sid;
			$return['data_type'] = $data_type;
			$return['message'] = 'Logged in as guest';
			$return['compressionMethod'] = $compress_method;
			$return['gedcom_id'] = $GEDCOM;
			$return = new SOAP_Value('result', '{urn:'.$this->__namespace.'}authResult', $return);
			$msg = "Guest Web Service Authenticate ";
			if (!empty($_SERVER['HTTP_USER_AGENT'])) $msg .= $_SERVER['HTTP_USER_AGENT'];
			AddToLog($msg);
			return $return;
		}

		//Call PGV authentication
		//-- NOTE: the authenticateUser function will reset the session
		if (authenticateUser($username, $password)) {
			$_SESSION["GEDCOM"] = $GEDCOM;
			$_SESSION["compression"] = $compress_method;
			$_SESSION["data_type"] = $data_type;
			if (isset( $_SESSION["readonly"] )) unset($_SESSION["readonly"]);
			$return['SID'] = $sid;
			$return['data_type'] = $data_type;
			$return['message'] = $username . " Logged in sucessfully";
			$return['compressionMethod'] = $compress_method;
			$return['gedcom_id'] = $GEDCOM;
			$return = new SOAP_Value('result', '{urn:'.$this->__namespace.'}authResult', $return);
			$msg = "$username Web Service Authenticate ";
			if (!empty($_SERVER['HTTP_USER_AGENT'])) $msg .= $_SERVER['HTTP_USER_AGENT'];
			AddToLog($msg);
			return $return;

		}
		//PGV auth failed
		return new SOAP_Fault('Unable to login',
							'Client',
							'',
							null);
	}

	/**
	 * Gets information of the current web service
	 * Also returns the list of gedcoms
	 *
	 * @return array Information of the web service
	 */
	function postServiceInfo() {
		global $GEDCOMS;
		//addDebugLog("in getServiceInfo ".$GEDCOMS);
		$return['compression'] = $this->getCompressionLibs();
		$return['apiVersion'] = $this->service_version;
		$return['server'] = PGV_PHPGEDVIEW.' '.PGV_VERSION_TEXT;

		$gedcomlist = array();
		$i = 0;
		if (is_array($GEDCOMS)) {
			//loop through the gedcoms available
			foreach ($GEDCOMS as $ged=>$gedarray) {
				$gedcominfo = array();
				$gedcominfo['title'] = $gedarray["title"];
				$gedcominfo['ID'] = $gedarray["gedcom"];
				$gedcomlist[$i] = $gedcominfo;
				//$gedcomlist[$i] = new SOAP_Value('item', '{urn:'.$this->__namespace.'}GedcomInfo', $gedcominfo);
				$i++;
			}
		}
		//$return[0]['gedcoms'] = $gedcomlist;
		$return['gedcoms'] = new SOAP_Value('gedcoms', '{urn:'.$this->__namespace.'}ArrayOfGedcomList', $gedcomlist);
		$return = new SOAP_Value('result', '{urn:'.$this->__namespace.'}serviceInfoResult', $return);
		return $return;
	}

	/**
	* Switches GEDCOM
	* @param string gedcom id of the gedcom to use
	* @return string	returns the id of the currently active gedcom
	*/
	function postChangeGedcom($gedcom) {
		global $GEDCOM;
		$gedcom = $this->default_gedcom($gedcom);
		$GEDCOM = $gedcom;
		$_SESSION['GEDCOM'] = $gedcom;
		return $gedcom;
	}

	/***
	* Get's a variables value
	*
	* @param string $SID session id
	* @param string $var variable name
	*
	* @return mixed SOAP_Fault or array of result
	*/
	function postGetVar($SID, $var) {
		global $CONFIG_VARS;
		$pgv_user = getUserName();
		$public_vars = array("CHARACTER_SET","GEDCOM","PEDIGREE_ROOT_ID","LANGUAGE");
		//-- only allow public vars to non authenticated users
		if (!empty($var) && (in_array($var, $public_vars)) && (isset($GLOBALS[$var]))) {
			addDebugLog("getVar var=$var SUCCESS ".$GLOBALS[$var]);
			return $GLOBALS[$var];
		}
		//-- authenticated users can access any var not in $CONFIG_VARS
		elseif ((!empty($pgv_user))&&(!empty($var))&&(isset($GLOBALS[$var]))&&(!in_array($var, $CONFIG_VARS))) {
			addDebugLog("getVar var=$var SUCCESS\n".$GLOBALS[$var]);
			return $GLOBALS[$var];
		} else {
			addDebugLog("getVar var=$var ERROR 13: Invalid variable specified.  Please provide a variable.");
			return new SOAP_Fault("ERROR 13: Invalid variable specified.\n", 'Client', '', null);
		}
	}

	/***
	* Returns the value of the specified variable
	*
	* @param string $SID session id of authenticated user
	* @param string $gedrec record to append
	*
	* @return mixed SOAP_Fault or array of result
	*/
	function postAppendRecord($SID, $gedrec) {
		if (!empty($gedrec)) {
			if ((empty($_SESSION['readonly']))&& PGV_USER_CAN_EDIT) {
				$gedrec = preg_replace(array("/\\\\+r/","/\\\\+n/"), array("\r","\n"), $gedrec);
				$xref = append_gedrec($gedrec);
				if ($xref) {
					addDebugLog("append gedrec=$gedrec SUCCESS\n$xref");
					return $xref;
				}
			} else {
				addDebugLog("append gedrec=$gedrec ERROR 11: No write privileges for this record.");
				return new SOAP_Fault("ERROR 11: No write privileges for this record.", 'Client', '', null);
			}
		} else {
			addDebugLog("append ERROR 8: No gedcom record provided.  Unable to process request.");
			return new SOAP_Fault("ERROR 8: No write privileges for this record.", 'Client', '', null);
		}
	}

	/***
	* Deletes the record with the provided record id
	*
	* @param string $SID session id of authenticated user
	* @param string $RID record id of record to delete
	*
	* @return mixed SOAP_Fault or array of result from the postAppendRecord method
	*/
	function postDeleteRecord($SID, $RID) {
		if (!empty($RID)) {
			if (((empty($_SESSION['readonly']))&& PGV_USER_CAN_EDIT)&&(displayDetailsById($RID))) {
				$success = delete_gedrec($RID);
				if ($success) {
					addDebugLog("delete RID=$RID SUCCESS");
					return "delete RID=$RID SUCCESS";
				}
			} else {
				addDebugLog("delete RID=$RID ERROR 11: No write privileges for this record.");
				return new SOAP_Fault("ERROR 11: No write privileges for this record.", 'Client', '', null);
			}
		} else {
			addDebugLog("delete ERROR 3: No gedcom id specified.  Please specify a xref.");
			return new SOAP_Fault("ERROR 3: No write privileges for this record.", 'Client', '', null);
		}
	}

	/**
	 * Updates a record with the provided gedcom
	 *
	 * @param string $SID session id of authenticated user
	 * @param string $RID record to be updated
	 * @param string $gedcom Updated gedcom
	 *
	 * @return mixed SOAP_Fault or array of result
	 */
	function postUpdateRecord($SID, $RID, $gedcom) {
		if (!empty($RID)) {
			if (!empty($gedcom)) {
				if (empty($_SESSION['readonly']) && PGV_USER_CAN_EDIT && displayDetailsById($RID)) {
					$gedrec = preg_replace(array("/\\\\+r/","/\\\\+n/"), array("\r","\n"), $gedcom);
					$success = replace_gedrec($RID, $gedrec);
					return 'Gedcom updated.';
				} else {
					return new SOAP_Fault("No write privileges for this record.", 'Client', '', null);
				}
			} else {
				return new SOAP_Fault("No gedcom record provided.  Unable to process request.", 'Client', '', null);
			}
		} else {
			return new SOAP_Fault("No gedcom id specified.  Please specify an id", 'Client', '', null);
		}
	}

	/**
	 * returns a person complex type
	 */
	function createPerson($PID, $gedrec, $soapval, $includeGedcom=true) {
		$gedrec = privatize_gedcom($gedrec);
		$person = array();
		$person['PID'] = $PID;
		$person['gedcomName'] = get_gedcom_value("NAME", 1, $gedrec, '', false);
		$person['birthDate'] = get_gedcom_value("BIRT:DATE", 1, $gedrec, '', false);
		$person['birthPlace'] = get_gedcom_value("BIRT:PLAC", 1, $gedrec, '', false);
		$person['deathDate'] = get_gedcom_value("DEAT:DATE", 1, $gedrec, '', false);
		$person['deathPlace'] = get_gedcom_value("DEAT:PLAC", 1, $gedrec, '', false);
		$person['gender'] = get_gedcom_value("SEX", 1, $gedrec, '', false);
		if ($includeGedcom ) {
			if ($_SESSION['data_type']=='GEDCOM') $person['gedcom'] = $gedrec;
			else {
				//-- get XML data here
				$ge = new GEWebService();
				$person['gedcom'] = $ge->create_person($gedrec, $PID);
			}
		} else {
			$person['gedcom'] = "";
		}
		$fams = find_families_in_record($gedrec, "FAMS");
		$familyS = array();
		foreach ($fams as $f=>$famid) {
//			$famrec = find_family_record($famid);
	//		$family = $this->createFamily($famid, $famrec, "item");
			$familyS[] = $famid;
		}
		$person['spouseFamilies'] = new SOAP_Value('spouseFamilies', '{urn:'.$this->__namespace.'}ArrayOfIds', $familyS);
		$famc = find_families_in_record($gedrec, "FAMC");
		$familyC = array();
		foreach ($famc as $f=>$famid) {
			$famrec = find_family_record($famid);
//			$family = $this->createFamily($famid, $famrec, "item");
			$familyC[] = $famid;
		}
		$person['childFamilies'] = new SOAP_Value('childFamilies', '{urn:'.$this->__namespace.'}ArrayOfIds', $familyC);
		$result = new SOAP_Value($soapval, '{urn:'.$this->__namespace.'}Person', $person);
		return $result;
	}

	/***
	* Get a gedcom of a person
	*
	* @param string SID
	* @param string PID person id
	*/
	function postGetPersonByID($SID, $PID) {
		global $pgv_changes, $GEDCOM, $SERVER_URL, $MEDIA_DIRECTORY;

		$returnType = 'gedcom';

		if (!empty($PID)) {
			$xrefs = explode(';', $PID);
			$success = true;
			$person = array();
			foreach ($xrefs as $indexval => $xref1) {
				$gedrec = "";
				$xref1 = trim($xref1);
				if (!empty($xref1)) {
					if (isset($pgv_changes[$xref1."_".$GEDCOM])) {
						$gedrec = find_updated_record($xref1);
					}

					if (empty($gedrec)) {
						$gedrec = find_person_record($xref1);
					}

					if (!empty($gedrec)) {
						$gedrec = trim($gedrec);
						preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
						$type = trim($match[2]);

						$result = $this->createPerson($PID, $gedrec, "result");
						$msg = "Person record $PID access through Web Service ";
						if (!empty($_SERVER['HTTP_USER_AGENT'])) $msg .= $_SERVER['HTTP_USER_AGENT'];
						AddToLog($msg);
						return $result;
					} else {
						return new SOAP_Fault("Unable to find person with ID ".$PID,'Client','',null);
					}
				}
			} //-- end for loop
		} else {
			return new SOAP_Fault("No gedcom id specified.  Please specify a PID",'Client','',null);
		}
	}

	/**
	 * create a Family complex type
	 */
	function createFamily($FID, $gedrec, $soapval) {
		$gedrec = privatize_gedcom($gedrec);
		$family = array();
		$family['FID'] = $FID;
		$family['HUSBID'] = get_gedcom_value("HUSB", 1, $gedrec, '', false);
		$family['WIFEID'] = get_gedcom_value("WIFE", 1, $gedrec, '', false);
		$CHILDREN = array();
		$ct = preg_match_all("/1 CHIL @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$child_id = $match[$i][1];
			$CHILDREN[] = $child_id;
		}
		$family['CHILDREN'] = new SOAP_Value('CHILDREN', '{urn:'.$this->__namespace.'}ArrayOfIds', $CHILDREN);
		//$family['CHILDREN'] = $CHILDREN;
		if ($_SESSION['data_type'] == 'GEDCOM') {
			$family['gedcom'] = $gedrec;
		} else {
			$ge= new GEWebService();
			$family['gedcom'] = $ge->create_family($gedrec, $FID);
			addToLog($family['gedcom']);
		}
		$result = new SOAP_Value($soapval, '{urn:'.$this->__namespace.'}Family', $family);
		return $result;
	}

	/***
	 * Retrieves a family with the given id
	 * @param string FID Family id
	 * @param string SID
	 ***/
	function postGetFamilyByID($SID, $FID) {
		global $pgv_changes, $GEDCOM, $SERVER_URL, $MEDIA_DIRECTORY;
		if ($data_type="GEDCOM") {
			$returnType = 'gedcom';
		} else {
			$returnType = 'gramps';
		}
		if (!empty($FID)) {
			$xrefs = explode(';', $FID);
			$success = true;
			$family = array();
			foreach ($xrefs as $indexval => $xref1) {
				$gedrec = "";
				$xref1 = trim($xref1);
				if (!empty($xref1)) {
					if (isset($pgv_changes[$xref1."_".$GEDCOM])) {
						$gedrec = find_updated_record($xref1);
					}

					if (empty($gedrec)) {
						$gedrec = find_family_record($xref1);
					}

					if (!empty($gedrec)) {
						$gedrec = trim($gedrec);
						preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
						$type = trim($match[2]);
						$result = $this->createFamily($FID, $gedrec, "result");
						return $result;
					} else {
						return new SOAP_Fault("Unable to find family with ID ".$FID,'Client','',null);
					}
				}
			} //-- end foreach loop
		} else {
			return new SOAP_Fault("No gedcom id specified.  Please specify a FID",'Client','',null);
		}
	}

	/**
	 * create a Source complex type
	 */
	function createSource($SCID, $gedrec) {
		$gedrec = privatize_gedcom($gedrec);

		$source = array();
		$source['SCID'] = $SCID;
		$source['title'] = get_gedcom_value("TITL", 1, $gedrec, '', false);
		$source['published'] = get_gedcom_value("PUBL", 1, $gedrec, '', false);
		$source['author'] = get_gedcom_value("AUTH", 1, $gedrec, '', false);
		if ($_SESSION['data_type']=='GEDCOM') $source['gedcom'] = $gedrec;
			else {
				//-- get XML data here
				$ge = new GEWebService();
				$source['gedcom'] = $ge->create_source($gedrec, $SCID);
			}
		$result = new SOAP_Value('result', '{urn:'.$this->__namespace.'}Source', $source);
		return $result;
	}

	/***
	 * Finds and returns a given Source type by ID
	 * @param string SID session id
	 * @param string SCID Source id
	 */
	function postGetSourceByID($SID, $SCID) {
		global $pgv_changes, $GEDCOM, $SERVER_URL, $MEDIA_DIRECTORY;

		$returnType = 'gedcom';

		if (!empty($SCID)) {
			$xrefs = explode(';', $SCID);
			$success = true;
			$source = array();
			foreach ($xrefs as $indexval => $xref1) {
				$gedrec = "";
				$xref1 = trim($xref1);
				if (!empty($xref1)) {
					if (isset($pgv_changes[$xref1."_".$GEDCOM])) {
						$gedrec = find_updated_record($xref1);
					}

					if (empty($gedrec)) {
						$gedrec = find_source_record($xref1);
					}

					if (!empty($gedrec)) {
						$gedrec = trim($gedrec);
						preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
						$type = trim($match[2]);
						$result = $this->createSource($SCID, $gedrec);
						return $result;
					} else {
						return new SOAP_Fault("Unable to find Source with ID ".$SCID,'Client','',null);
					}
				}
			} //-- end for loop
		} else {
			return new SOAP_Fault("No gedcom id specified.  Please specify a SCID",'Client','',null);
		}
	}

	/**
	 * Return a gedcom record
	 * Finds the record with the given ID
	 * @param string $SID	the session id
	 * @param string $PID	the gedcom xref id for the record to find
	 * @return string		the raw gedcom record is returned
	 */
	function postGetGedcomRecord($SID, $PID) {
		global $pgv_changes, $GEDCOM, $SERVER_URL, $MEDIA_DIRECTORY;

		if (!empty($PID)) {
			$xrefs = explode(';', $PID);
			$success = true;
			$gedrecords="";
			foreach ($xrefs as $indexval => $xref1) {
				$gedrec = "";
				$xref1 = trim($xref1);
				if (!empty($xref1)) {
					if (isset($pgv_changes[$xref1."_".$GEDCOM])) {
						$gedrec = find_updated_record($xref1);
					}

					if (empty($gedrec)) {
						$gedrec = find_gedcom_record($xref1);
					}

					if (!empty($gedrec)) {
						$gedrec = trim($gedrec);
						preg_match("/0 @(.*)@ (.*)/", $gedrec, $match);
						$type = trim($match[2]);
						//-- do not have full access to this record, so privatize it
						$gedrec = privatize_gedcom($gedrec);
						$gedrecords = $gedrecords . "\n".trim($gedrec);
						$msg = "GEDCOM record $xref1 accessed through Web Service ";
						if (!empty($_SERVER['HTTP_USER_AGENT'])) $msg .= $_SERVER['HTTP_USER_AGENT'];
						AddToLog($msg);
					} else {
						return new SOAP_Fault("No Results found for PID:".$PID,'Client','',null);
					}
				}
			} //-- end for loop
			if ($success) {
				if ($_SESSION['data_type'] == 'GEDCOM') {

					if (empty($_REQUEST['keepfile'])) {
						$ct = preg_match_all("/ FILE (.*)/", $gedrecords, $match, PREG_SET_ORDER);
						for($i=0; $i<$ct; $i++)
						{
							$mediaurl = $SERVER_URL.$MEDIA_DIRECTORY.extract_filename($match[$i][1]);
							$gedrecords = str_replace($match[$i][1], $mediaurl, $gedrecords);
						}
					}

					$return = trim($gedrecords);
					return $return;
				} else {
					$ge= new GEWebService();
					return $ge->create_record($PID);
				}
			}
		} else {
			return new SOAP_Fault("No gedcom id specified.  Please specify a PID",'Client','',null);
		}
	}

	function postSearch($SID, $query, $start, $maxResults) {

		// keyword anywhere;field=value&field2=value2
		// Known keywords NAME, BIRTHDATE, DEATHDATE, BIRTHPLACE, DEATHPLACE, GENDER
		// 10 JAN 2005 only will take the standard date format of a gedcom

		if (strlen($query) > 1) {
			AddToLog('Search query: ' . $query);
			// this is use to figure out if it is just a keyword or a advanced search
			if (!(strstr($query, 'NAME') || strstr($query, 'BIRTHDATE') ||
					strstr($query, 'DEATHDATE') || strstr($query, 'BIRTHPLACE') ||
					strstr($query, 'DEATHPLACE') || strstr($query, 'GENDER')))
			{
				//if its just a key word search
				$results = array();
				$results_array = array();
				$search_results = search_indis(array($query), array(PGV_GED_ID), 'AND', true);

				// loop thru the returned result of the method call

				foreach ($search_results as $gid=>$indi) {
					// privatize the gedcoms returned
					$gedrec = privatize_gedcom($indi["gedcom"]);
					// set the fields that exist and return all the results that are not private
					if (preg_match("~".$query."~i",$gedrec)>0) {
						$search_result_element = $this->createPerson($gid, $gedrec, "item", false);
						//$search_result_element['gedcom'] = $gedrec;
						$results_array[] = $search_result_element;
					}
				}
				// set the number of possible results
				$results['totalResults'] = count($results_array);
				// cut the array depending on start index and max results
				$results_array = array_slice($results_array,$start,$maxResults);
				// seting the value of search results to the results array
				$results['persons'] = new SOAP_Value('persons', '{urn:'.$this->__namespace.'}ArrayOfPerson', $results_array);
				$results = new SOAP_Value('Results', '{urn:'.$this->__namespace.'}SearchResult', $results);
				return $results;
			}

	// The code below is for when the user queried an advance search

			// array used to supply functions for searching with the correct information.
			$array_querys = array();

			// array used to split the string $query into parts
			$temp_queries = explode('&', $query);

			// each part is gone through to select the field and the values
			foreach ($temp_queries as $index=>$query) {
				$part = explode('=', $query);
				// $part[0] = field $part[1] = value;
				$array_querys[$part[0]] = $part[1];
			}
			/*
			$results_from_dates;
			$results_from_name;
			$results_from_birth_date;
			$results_from_death_date;
			$newarray;
			*/
			// a search on the name supply in $query if it exists
			if (array_key_exists('NAME', $array_querys)) {
				$results_from_name = search_indis_names($array_querys['NAME']);
			}

			// used to change if both dates exist in $query
			$both_dates_exist = true;
			// a search on the birthdate supply in $query  if it exists
			if (array_key_exists('BIRTHDATE', $array_querys)) {
				$date = new GedcomDate($array_querys['BIRTHDATE']);
				$date = $date->MinDate();
				//$day="", $month="", $year="", $fact="", $allgeds=false, $ANDOR="AND")
				$results_from_birth_date = search_indis_dates($date->d,$date->Format('O'),$date->y,'BIRT');
			} else {
				$both_dates_exist = false;
			}

			// a search on the deathdate supply in $query  if it exists
			if (array_key_exists('DEATHDATE', $array_querys)) {
				$date = new GedcomDate($array_querys['DEATHDATE']);
				$date = $date->MinDate();
				$results_from_death_date = search_indis_dates($date->d,$date->Format('O'),$date->y,'DEAT');
			} else {
				$both_dates_exist = false;
			}

			// if both exist then merge them
			// if not then is one set if so the set that to be the one that is merged with the $results_from_name array
			if ($both_dates_exist) {
				$results_from_dates = array_intersect_assoc($results_from_birth_date, $results_from_death_date);
			} elseif (isset($results_from_birth_date)|| isset($results_from_death_date)) {
				if (isset($results_from_birth_date)) {
					$results_from_dates = $results_from_birth_date;
				} else {
					$results_from_dates = $results_from_death_date;
				}
			}

			// this array is used for storing the information about the people
			// returned from the two searches and the unsimilar people are left out.
			// only merge them is both are set else then set the one that is to $newarray
			if (isset($results_from_name) && isset($results_from_dates)) {
				$newarray = array_intersect_assoc($results_from_name, $results_from_dates);
			} elseif (isset($results_from_name)|| isset($results_from_dates)) {
				if (isset($results_from_name)) {
					$newarray = $results_from_name;
				} else {
					$newarray = $results_from_dates;
				}
			}

			if (!isset($newarray)) {
				$queries = array();
				if (!empty($array_querys['BIRTHPLACE'])) {
					$queries[] = 'PLAC[^\n]*'.$array_querys['BIRTHPLACE'];
				} elseif (!empty($array_querys['DEATHPLACE'])) {
					$queries[] = 'PLAC[^\n]*'.$array_querys['DEATHPLACE'];
				} elseif (!empty($array_querys['GENDER'])) {
					if (count($queries)==0 && count($newarray)==0) return new SOAP_Fault("Please specify a more advanced search.", "SERVER");
					$queries[] = 'SEX '.$array_querys['GENDER'];
				} else {
					$newarray = array();
				}
				if (count($queries)>0) {
					$newarray = search_indis($queries, array(PGV_GED_ID), 'AND', true);
				}
			}
			$results = array();
			$results_array = array();
			foreach ($newarray as $gid=>$indi) {

				// need to check to see if all the values asked for in the query are still there after the privatizing
				$all_crit_exist_in_gedcom = true;
				$search_result_element = $this->createPerson($gid, $indi['gedcom'], "item", false);
				if (!empty($array_querys['NAME']) && (stristr($search_result_element->value['gedcomName'], $array_querys['NAME']) === false)) {
					$all_crit_exist_in_gedcom = false;
				}

				if (!empty($array_querys['BIRTHDATE']) && (stristr($search_result_element->value['birthDate'], $array_querys['BIRTHDATE']) === false)) {
					$all_crit_exist_in_gedcom = false;
				}

				if (!empty($array_querys['BIRTHPLACE']) && (stristr($search_result_element->value['birthPlace'], $array_querys['BIRTHPLACE']) === false)) {
					$all_crit_exist_in_gedcom = false;
				}

				if (!empty($array_querys['DEATHDATE']) && (stristr($search_result_element->value['deathDate'], $array_querys['DEATHDATE']) === false)) {
					$all_crit_exist_in_gedcom = false;
				}

				if (!empty($array_querys['DEATHPLACE']) && (stristr($search_result_element->value['deathPlace'], $array_querys['DEATHPLACE']) === false)) {
					$all_crit_exist_in_gedcom = false;
				}

				if (!empty($array_querys['GENDER']) && $search_result_element->value['gender'] != $array_querys['GENDER'] ) {
					$all_crit_exist_in_gedcom = false;
				}


				// if all the critian still exist after privatize thenset it to the array
				if ($all_crit_exist_in_gedcom) {
					$results_array[] = $search_result_element;
				}

				// sample how to get information for the result set
				//$birtdate = get_gedcom_value("BIRT:DATE", 1, $gedrec, '', false);
			}
			// set the number of possible results
			$results['totalResults'] = count($results_array);
			// cut the array depending on start index and max results
			$results_array = array_slice($results_array,$start,$start + $maxResults);
			// seting the value of search results to the results array
			$results['persons'] = new SOAP_Value('persons', '{urn:'.$this->__namespace.'}ArrayOfPerson', $results_array);
			$results = new SOAP_Value('Results', '{urn:'.$this->__namespace.'}SearchResult', $results);
			return $results;
		} else {
			// if the query string was less then 2 chars send soap fault
			return new SOAP_Fault('search','search query must cantain more then one character');
		}
	}

	function postCheckUpdatesByID($SID,$RID,$lastUpdate) {
		// Method call used to retrieve data by the Gedcom Id form PGV
		$indirec = find_person_record($RID);

		if (!empty($indirec)) {
			// MEthod call used to reteave data by the Person Created above by there gedcom id.
			// in this case the data reteaved is the data of the last submission for the person.
			$change_date = get_gedcom_value("CHAN:DATE", 1, $indirec, '', false);

			//If the date does not exist then return the sample data with the Status code of Update date unknown
			if (empty($change_date)) {
				return new SOAP_Fault('perform_update_check','Last update date Unknown');
			}

			$chan_date     = new GedcomDate($change_date);
			$incoming_date = new GedcomDate($lastUpdate);

			if ($change_time->MinJD() > $incoming_time->MinJD()) {
				$result = $this->createPerson($RID, $indirec, "result");
				return $result;
			} else {
				return new SOAP_Fault('perform_update_check','Update check performed but no update was made');
			}
		}
		return new SOAP_Fault('perform_update_check','No gedcom record found with that ID');

	}

	function postCheckUpdates($SID,$lastUpdate) {
		$date = new GedcomDate($lastUpdate);

		if (!$date->isOK()) {
			return new SOAP_Fault('perform_update_check','Invalid date parameter.  Please use a valid date in the GEDCOM format DD MMM YYYY.');
		}

		if ($date->MinJD()<server_jd()-180) {
			return new SOAP_Fault('checkUpdates', 'You cannot retrieve updates for more than 180 days.');
		}

		$changes = get_recent_changes($date->MinJD());
		$results = array();
		foreach ($changes as $id=>$change) {
			$results[] = $change['d_gid'];
		}
		$results = new SOAP_Value('result', '{urn:'.$this->__namespace.'}ArrayOfIds', $results);
		return $results;
	}

	function getKnownServers($SID,$limit) {
		// get_server_list(); returns array or false;
		$servers = get_server_list();
		if (count($servers)>0) {
	//		addtolog('servers = true');
			// the array to return
			$results_array = array();

			$count = 0;
			//Loop through the data and add each server and address to the results_array
			foreach ($servers as $server) {
	//			Addtolog('foreach fun!'.$server);
				if ($count >= $limit && $limit !== 0) {
					break;
				}
				// the array used inside the results array to hold both
				//the name and the address of each server that is known
				$server_results_array = array();
				$server_results_array['name'] = $server;
				$server_results_array['address'] = $server;
				$results_array[] = new SOAP_Value('server', '{urn:'.$this->__namespace.'}Server', $server_results_array);
				$count++;
			}
			// and return the array of results
			$results = new SOAP_Value('servers', '{urn:'.$this->__namespace.'}ArrayOfServer', $results_array);
			return $results;
		} else {
			return new SOAP_Fault("No known servers to report",'Server','',null);
		}
	}

	/***
	 * Returns the ancestry for the given root ID
	 * @param string SID
	 * @param string rootID person to start ancestry at
	 * @param string generations number of generations to go
	 * @param boolean  returnGedcom return gedcom with results
	***/
	function postGetAncestry($SID, $rootID, $generations, $returnGedcom) {
		$list = array();
		$list[$rootID] = Person::getInstance($rootID);

		add_ancestors($list, $rootID, false, $generations);

		if (empty($list)){
			return new SOAP_Fault('Could not retrieve ancestory', 'Server', '',null);
		} else {
			$count = 0;
			foreach ($list as $key => $value) {
				if ($value!=null) {
					$person = $this->createPerson($key, $value->getGedcomRecord(), "item", $returnGedcom);
					$result[] = $person;
					$count++;
				}
			}
		}

		if ($count === 0) {
			return new SOAP_Fault('No ancestry results for ' . $rootID, 'Server', '', null);
		} else {
			$result = new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfPerson', $result);
			return $result;
		}
	}

	/***
	* Returns the descendants of a person
	* @param string SID
	* @param string rootID person to start at
	* @param string generations generations to go
	* @param boolean returnGedcom return gedcom with results
	*/
	function postGetDescendants($SID, $rootID, $generations, $returnGedcom) {
		$list = array();

		add_descendancy($list, $rootID, false, $generations);

		if (empty($list)){
			return new SOAP_Fault('Could not retrieve descendancy', 'Server', '',null);
		} else {
			$count = 0;
			foreach ($list as $key => $value) {
				if ($value!=null) {
					$person = $this->createPerson($key, $value->getGedcomRecord(), "item", $returnGedcom);
					$result[] = $person;
					$count++;
				}
			}
		}
		if ($count === 0) {
			return new SOAP_Fault('No descendancy results for ' . $rootID, 'Server', '', null);
		} else {
			$result = new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfPerson', $result);
			return $result;
		}
	}

	/**
	 *
	 */
	/**
	* Method to override
	*/
	function postGetXref($SID, $position, $type) {
		global $fcontents;
		if (empty($position)) $position='first';
		if (empty($type)) $type='INDI';
		if ((empty($type))||(!in_array($type, array("INDI","FAM","SOUR","REPO","NOTE","OBJE","OTHER")))) {
			addDebugLog("getXref type=$type position=$position ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER");
			//print "ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER\n";
			return new SOAP_Fault('ERROR 18: Invalid $type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER', 'Server', '', null);
		}
		$myindilist = array();
		if ($type!="OTHER") {
			$ct = preg_match_all("/0 @(.*)@ $type/", $fcontents, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$xref1 = trim($match[$i][1]);
				$myindilist[$xref1] = $xref1;
			}
		} else {
			$ct = preg_match_all("/0 @(.*)@ (.*)/", $fcontents, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$xref1 = trim($match[$i][1]);
				$xtype = trim($match[$i][2]);
				if (($xtype!="INDI")&&($xtype!="FAM")&&($xtype!="SOUR")) $myindilist[$xref1] = $xref1;
			}
		}
		reset($myindilist);
		if ($position=='first') {
			$xref = current($myindilist);
			addDebugLog("getXref type=$type position=$position SUCCESS\n$xref");
			return new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfIds', array($xref));
		} elseif ($position=='last') {
			$xref = end($myindilist);
			addDebugLog("getXref type=$type position=$position SUCCESS\n$xref");
			return new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfIds', array($xref));
		} elseif ($position=='next') {
			// TODO: $xref can never be set?  This code looks like it was just copied from client.php
			if (!empty($xref)) {
				$xref1 = get_next_xref($xref);
				if ($xref1) {
					addDebugLog("getXref type=$type position=$position xref=$xref SUCCESS\n$xref1");
					return new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfIds', array($xref));
				}
			} else {
				addDebugLog("getXref type=$type position=$position ERROR 3: No gedcom id specified.  Please specify a xref.");
				//print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
				return new SOAP_Fault('ERROR 3: No gedcom id specified.  Please specify a xref.', 'Server', '', null);
			}
		} elseif ($position=='prev') {
			// TODO: $xref can never be set?  This code looks like it was just copied from client.php
			if (!empty($xref)) {
				$xref1 = get_prev_xref($xref);
				if ($xref1) {
					addDebugLog("getXref type=$type position=$position xref=$xref SUCCESS\n$xref1");
					return new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfIds', array($xref));
				}
			} else {
				addDebugLog("getXref type=$type position=$position ERROR 3: No gedcom id specified.  Please specify a xref.");
				//print "ERROR 3: No gedcom id specified.  Please specify a xref.\n";
				return new SOAP_Fault('ERROR 3: No gedcom id specified.  Please specify a xref.', 'Server', '', null);
			}
		} elseif ($position=='all') {
			addDebugLog("getXref type=$type position=$position ");
			return new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfIds', $myindilist);
		} elseif ($position=='new') {
			if (empty($_SESSION['readonly']) && PGV_USER_CAN_EDIT) {
				if ((empty($type))||(!in_array($type, array("INDI","FAM","SOUR","REPO","NOTE","OBJE","OTHER")))) {
					addDebugLog("getXref type=$type position=$position ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER");
					//print "ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER\n";
					return new SOAP_Fault('ERROR 18: Invalid \$type specification.  Valid types are INDI, FAM, SOUR, REPO, NOTE, OBJE, or OTHER', 'Server', '', null);
				}
				$gedrec = "0 @REF@ $type";
				$xref = append_gedrec($gedrec);
				if ($xref) {
					addDebugLog("getXref type=$type position=$position SUCCESS\n$xref");
					return new SOAP_Value('results', '{urn:'.$this->__namespace.'}ArrayOfIds', array($xref));
				}
			} else {
				addDebugLog("getXref type=$type position=$position ERROR 11: No write privileges for this record.");
				//print "ERROR 11: No write privileges for this record.\n";
				return new SOAP_Fault('ERROR 11: No write privileges for this record', 'Server', '', null);
			}
		} else {
			addDebugLog("getXref type=$type position=$position ERROR 17: Unknown position reference.  Valid values are first, last, prev, next.");
			//print "ERROR 17: Unknown position reference.  Valid values are first, last, prev, next.\n";
			return new SOAP_Fault('ERROR 17: Unknown position reference.  Valid values are first, last, prev, next', 'Server', '', null);
		}
	}
}

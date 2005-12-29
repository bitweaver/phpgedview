<?php
/**
 * Class used to access records and data on a remote server
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	PGV Development Team
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
 * @subpackage DataModel
 * @version $Id: serviceclient_class.php,v 1.1 2005/12/29 19:36:21 lsces Exp $
 */

require_once 'includes/source_class.php';
include_once('includes/SOAP/Client.php');
include_once('includes/PEAR.php');

class ServiceClient extends Source {
	var $url = "";
	var $soapClient = null;
	var $SID = "";
	var $gedfile = "";
	var $title = "";
	var $username = "";
	var $password = "";
	var $type = "";
	
	/**
	 * contstructor to create a new ServiceClient object
	 * @param string $gedrec	the SERV gedcom record 
	 */
	function ServiceClient($gedrec) {
		//parse url
		//crate soap client class
		//authenticate/get/set sid	
		parent::GedcomRecord($gedrec);
		//print "creating new service client ".$this->xref;
		//get the url from the gedcom
		$this->url = get_gedcom_value("URL",1,$gedrec);
		$this->gedfile = get_gedcom_value("_DBID", 1, $gedrec);
		$this->title = get_gedcom_value("TITL", 1, $gedrec);
		$this->username = get_gedcom_value("_USER", 2, $gedrec);
		$this->password = get_gedcom_value("_PASS", 2, $gedrec);
		$this->type = "remote";
		if (empty($this->url) && empty($this->gedfile))
			return null;
	}
	
	function getType() {
		return $this->type;
	}
	
	/**
	 * get the URL
	 */
	function getURL() {
		return $this->url;
	}
	
	/**
	 * get the gedcom file
	 */
	function getGedfile() {
		return $this->gedfile;
	}
	
	/**
	 * authenticate the client
	 */
	function authenticate() {
		if (!empty($this->SID)) return;
		if (is_null($this->soapClient)) {
			AddToLog("getting wsdl");
			//	get the wsdl and cache it
			$wsdl = new SOAP_WSDL($this->url);
			AddToLog("wsdl found");
			//change the encoding style
			$this->__change_encoding($wsdl);
			$this->soapClient = $wsdl->getProxy();
		}
		$res = $this->soapClient->Authenticate($this->username, $this->password, $this->gedfile, "");
		
		if (!is_object($res))
		{
			return false;
		}
		if (!isset($res->SID))
		{
			return false;
		}
		$this->SID = $res->SID;
		return $this->SID;
	}
	
	/**
	 * get the title for this service
	 * @return string
	 */
	function getServiceTitle()
	{
		if (!empty($this->title)) return $this->title;
		
		$this->authenticate();
		$info = $this->soapClient->ServiceInfo();
		
		foreach($info->gedcoms as $ind=>$gedobj)
		{
			if ($gedobj->ID==$this->gedfile) break;
		}
		$this->title = $gedobj->title;
		return $this->title;
	}
	
	// Merges two records
	function _merge($record1, $record2)
	{
		// Returns second record if first is empty, no merge needed
		if (empty($record1)) return $record2;
		// Returns first record if second is empty, no merge needed
		if (empty($record2)) return $record1;
		
		$remoterecs = get_all_subrecords($record2, "", false, false, false);
		$localrecs = get_all_subrecords($record1, "", false, false, false);
		
		$newrecs = $remoterecs;
		
		foreach($localrecs as $ind=>$subrec) {
			$found = false;
			if (preg_match("/1 CHAN/", $subrec)==0) {
				$subrec = trim($subrec);
				$orig_subrec = $subrec;
				$subrec = preg_replace("/\s+/", " ", $subrec);
				
				foreach($remoterecs as $ind2=>$subrec2)
				{
					$subrec2 = trim($subrec2);
					$subrec2 = preg_replace("/\s+/", " ", $subrec2);
					
					if ($subrec2 == $subrec)
					{
						$found = true;
						break;
					}
				}
			}
			else
			{
				$found = true;
			}
			if (!$found)
			{
				$newrecs[] = $orig_subrec;
			}
		}
		$pos1 = strpos($record1, "\n1");
		if ($pos1!==false) $localrec = substr($record1, 0, $pos1+1);
		else $localrec = $record1;
		foreach($newrecs as $ind=>$subrec) {
			$localrec .= $subrec."\r\n";
		}
		
		// Update the last change time
		$pos1 = strpos($localrec, "1 CHAN");
		if ($pos1!==false) {
			$pos2 = strpos($localrec, "\n1", $pos1+4);
			if ($pos2===false) $pos2 = strlen($localrec);
			$newgedrec = substr($localrec, 0, $pos1);
			$newgedrec .= "1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU @".$this->xref."@\r\n";
			$newgedrec .= substr($localrec, $pos2);
			$localrec = $newgedrec;
		}
		else {
			$newgedrec = "\r\n1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU @".$this->xref."@";
			$localrec .= $newgedrec;
		}
		
		//print "merged record is ".$localrec;
		return $localrec;
	}
	
	/**
	 * check if any there are any stub records with RFN tags that match the 
	 * ids in the gedcom record
	 * @param string $gedrec
	 * @return string
	 */
	function checkIds($gedrec) {
		$ct = preg_match_all("/@(".$this->xref.":.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$id = trim($match[$i][1]);
			$list = search_indis("1 RFN ".$id);
			if (count($list)>0) {
				foreach($list as $gid=>$indi) {
					if ($gid!=$id) {
						$gedrec = preg_replace("/@".$id."@/", "@".$gid."@", $gedrec);
					}
				}
			}
			$list = search_fams("1 RFN ".$id);
			if (count($list)>0) {
				foreach($list as $gid=>$indi) {
					if ($gid!=$id) {
						$gedrec = preg_replace("/@".$id."@/", "@".$gid."@", $gedrec);
					}
				}
			}
			$list = search_sources("1 RFN ".$id);
			if (count($list)>0) {
				foreach($list as $gid=>$indi) {
					if ($gid!=$id) {
						$gedrec = preg_replace("/@".$id."@/", "@".$gid."@", $gedrec);
					}
				}
			}
		}
		return $gedrec;
	}
	
	/**
	 * merge a local gedcom record with the information from the remote site
	 */
	function mergeGedcomRecord($xref, $localrec) {
		global $FILE, $GEDCOM, $indilist, $famlist, $sourcelist, $otherlist;
		global $TBLPREFIX, $GEDCOMS;
		
		$FILE = $GEDCOM;
		$gedrec = find_gedcom_record($this->xref.":".$xref);
		if (!empty($gedrec)) $localrec = $gedrec;
		//-- get the last change date of the record
		$change_date = get_gedcom_value("CHAN:DATE", 1, $localrec, '', false);
		if (empty($change_date)) {
			//print $xref." no change<br />";
			$this->authenticate();
			$result = $this->soapClient->getGedcomRecord($this->SID, $xref);
			if (PEAR::isError($result) || isset($result->faultcode)) {
				if (isset($result->faultstring)) {
					AddToLog($result->faultstring);
					print $result->faultstring;
				}
				return $localrec;
			}
			$gedrec = $result->result;
			$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
			$gedrec = $this->checkIds($gedrec);
			$localrec = $this->_merge($localrec, $gedrec);
			//	update_record($localrec);
			$ct=preg_match("/0 @(.*)@/", $localrec, $match);
			if ($ct>0)
			{
				include_once("includes/functions_edit.php");
				$pid = trim($match[1]);
				replace_gedrec($pid,$localrec);
			}
		}
		else {
			$chan_date = parse_date($change_date);
			$chan_time_str = get_gedcom_value("CHAN:DATE:TIME", 1, $localrec, '', false);
			$chan_time = parse_time($chan_time_str);
			$change_time = mktime($chan_time[0], $chan_time[1], $chan_time[2], $chan_date[0]['mon'], $chan_date[0]['day'], $chan_date[0]['year']);
			/**
			 * @todo make the timeout a config option
			 */
			// Time Clock (determines how often a record is checked)
			if ($change_time < time()-(60*60*24*14)) // if the last update (to the remote individual) was made more than 14 days ago
			{
				$this->authenticate();
				$person = $this->soapClient->checkUpdatesByID($this->SID, $xref, $change_date);
				
				// If there are no changes between the local and remote copies
				if (PEAR::isError($person) || isset($person->faultcode) || get_class($person)=='SOAP_Fault' || isset($person->error_message_prefix)) {
					
					if (isset($person->faultstring)) AddToLog($person->faultstring);
					else AddToLog($person->message);
					//-- update the last change time
					$pos1 = strpos($localrec, "1 CHAN");
					if ($pos1!==false) {
						$pos2 = strpos($localrec, "\n1", $pos1+4);
						if ($pos2===false) $pos2 = strlen($localrec);
						$newgedrec = substr($localrec, 0, $pos1);
						$newgedrec .= "1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
						$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
						$newgedrec .= "2 _PGVU @".$this->xref."@\r\n";
						$newgedrec .= substr($localrec, $pos2);
						$localrec = $newgedrec;
					}
					else {
						$newgedrec = "\r\n1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
						$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
						$newgedrec .= "2 _PGVU @".$this->xref."@";
						$localrec .= $newgedrec;
					}
					update_record($localrec);
				}
				// If changes have been made to the remote record
				else {
					$gedrec = $person->gedcom;
					$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
					$gedrec = $this->checkIds($gedrec);
					$localrec = find_record_in_file($xref);
					$localrec = $this->_merge($localrec, $gedrec);
					//	update_record($localrec);
					$ct=preg_match("/0 @(.*)@/", $localrec, $match);
					if ($ct>0)
					{
						include_once("includes/functions_edit.php");
						$pid = trim($match[1]);
						replace_gedrec($pid,$localrec);
					}
				}	
			}
			else {
				//-- no need to check the web service
				return $localrec;
			}
		}
		 
		//-- update the database

//		replace_gedrec(,$localrec)
		
		return $localrec;
	}
	
	/**
	 * get a singleton instance of the results
	 * returned by the soapClient search method
	 * 
	 * @param string $query - the query to search on
	 * @param integer $start - the start index of the results to return
	 * @param integer $max - the maximum number of results to return 
	 */
	function &search($query, $start=0, $max=100) {
		$this->authenticate();
		$result = $this->soapClient->search($this->SID, $query, $start, $max);
		return $result;
	}
	
	/***
	 * Change encoding style to literal
	 * used when calling a java service
	 *
	 * @param object $wsdl SOAP_WSDL object
	 * @returns object modified wsdl object
	 */
	function __change_encoding(&$wsdl)
	{
		$namespace = array_keys($wsdl->bindings);
		if (isset($namespace[0]) && isset($wsdl->bindings[$namespace[0]]['operations'])) {
			$operations = array_keys($wsdl->bindings[$namespace[0]]['operations']);
	
			for($i = 0; $i<count($operations); $i++)
			{
				$wsdl->bindings[$namespace[0]]['operations'][$operations[$i]]['input']['use'] = 'literal';
				$wsdl->bindings[$namespace[0]]['operations'][$operations[$i]]['output']['use'] = 'literal';
			}
		}
	}

	/**
	 * get a singleton instance of this record
	 * @return ServiceClient
	 */
	function &getInstance($id) {
		global $PGV_SERVERS, $SERVER_URL;
		
		if (isset($PGV_SERVERS[$id])) return $PGV_SERVERS[$id];
		$gedrec = find_gedcom_record($id);
		if (empty($gedrec)) $gedrec = find_record_in_file($id);
		if (!empty($gedrec)) {
			$url = get_gedcom_value("URL",1,$gedrec);
			$gedfile = get_gedcom_value("_GEDF", 1, $gedrec);
			if (empty($url) && empty($gedfile))
				return null;
			if (!empty($url) && (strtolower($url)!=strtolower($SERVER_URL))) { 
				$server = new ServiceClient($gedrec);
			}
			else {
				include_once('includes/localclient_class.php');
				$server = new LocalClient($gedrec);
			} 
			$PGV_SERVERS[$id] = $server;
			return $server;
		}
		return null; 
	}
	
	function getGeneologyService() {
		$this->authenticate();
		
		$_SESSION["SOAP_CONNECTED"] = true;
		
		include_once('webservice/PGVServiceLogic.class.php');
		
		return new PGVServiceLogic();
	}
}

?>

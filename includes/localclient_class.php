<?php
/**
 * 
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * @version $Id: localclient_class.php,v 1.3 2006/10/02 23:04:15 lsces Exp $
 */

require_once 'includes/serviceclient_class.php';

class LocalClient extends ServiceClient {
	/**
	 * constructor
	 * @param string $gedrec	the gedcom record
	 */
	function LocalClient($gedrec) {
		parent::ServiceClient($gedrec);
		$this->type = "local";
	}
	
	/**
	 * authenticate the client
	 */
	function authenticate() {
		//-- nothing to do in a local client
	}
	
	/**
	 * Get a record from the remote site
	 * @param string $remoteid	the id of the record to get
	 */
	function getRemoteRecord($remoteid) {
		$rec = find_gedcom_record($remoteid, $this->gedfile);
		$rec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $rec);
		return $rec;
	}
	
	/**
	 * merge a local gedcom record with the information from the remote site
	 */
	function mergeGedcomRecord($xref, $localrec, $isStub=false, $firstLink=false) {
		global $FILE, $GEDCOM, $indilist, $famlist, $sourcelist, $otherlist;
		global $GEDCOMS;
		
		$localkey = $this->xref.":".$xref;
		//-- check the memory cache
		if (!empty($indilist[$localkey]["gedcom"])) return $indilist[$localkey]["gedcom"];
		if (!empty($famlist[$localkey]["gedcom"])) return $famlist[$localkey]["gedcom"];
		if (!empty($otherlist[$localkey]["gedcom"])) return $otherlist[$localkey]["gedcom"];
		if (!empty($sourcelist[$localkey]["gedcom"])) return $sourcelist[$localkey]["gedcom"];
		//-- get the record from the database
		$gedrec = find_gedcom_record($xref, $this->gedfile);
		$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
		$gedrec = $this->checkIds($gedrec);
		if (empty($localrec)) return $gedrec;
		$localrec = $this->_merge($localrec, $gedrec);
		
		//-- used to force an update on the first time linking a person
		if ($firstLink) {
			include_once("includes/functions_edit.php");
			$ct=preg_match("/0 @(.*)@/", $localrec, $match);
			if ($ct>0)
			{
				$pid = trim($match[1]);
				$localrec = $this->UpdateFamily($localrec,$gedrec);
				$localrec = preg_replace("/0 @(.*)@/", "0 @$pid@", $localrec);
//				print $localrec;
				replace_gedrec($pid,$localrec);
			}
		}
		
		if (!empty($localrec)) {
			$gid=$localkey;
			$ct = preg_match("/0 @(.*)@/", $localrec, $match);
			if ($ct>0) $gid = trim($match[1]);
			if ($gid!=$localkey) {
				$localkey = $gid;
			} 
			//print "found record for ".$localkey;
			if (isset($indilist[$xref])) {
				$indi = $indilist[$xref];
				$indi["gedcom"] = $localrec;
				$indi["gedfile"] = $GEDCOMS[$GEDCOM]["id"];
				$indilist[$localkey] = $indi;
			}
			if (isset($famlist[$xref])) {
				$indi = $famlist[$xref];
				$indi["gedcom"] = $localrec;
				$indi["gedfile"] = $GEDCOMS[$GEDCOM]["id"];
				$famlist[$localkey] = $indi;
			}
			if (isset($otherlist[$xref])) {
				$indi = $otherlist[$xref];
				$indi["gedcom"] = $localrec;
				$indi["gedfile"] = $GEDCOMS[$GEDCOM]["id"];
				$otherlist[$localkey] = $indi;
			}
			if (isset($sourcelist[$xref])) {
				$indi = $sourcelist[$xref];
				$indi["gedcom"] = $localrec;
				$indi["gedfile"] = $GEDCOMS[$GEDCOM]["id"];
				$sourcelist[$localkey] = $indi;
			}
		}
//		print "<pre>$localrec</pre>";
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
		//$this->authenticate();
		//$result = $this->soapClient->search($this->SID, $query, $start, $max);
		$search_results = search_indis($query, array($this->gedfile));
				
		// loop thru the returned result of the method call
		foreach($search_results as $gid=>$indi)
		{
			// privatize the gedcoms returned
			$gedrec = privatize_gedcom($indi["gedcom"]);
			//AddToLog(substr($gedrec,0,50));
			// set the fields that exist and return all the results that are not private
			if(preg_match("~".$query."~i",$gedrec)>0)
			{
				$person = new SOAP_Value('person', 'person', "");
				$person->PID = $gid;
				$person->gedcomName = get_gedcom_value("NAME", 1, $gedrec, '', false);
				$person->birthDate = get_gedcom_value("BIRT:DATE", 1, $gedrec, '', false);
				$person->birthPlace = get_gedcom_value("BIRT:PLAC", 1, $gedrec, '', false);
				$person->deathDate = get_gedcom_value("DEAT:DATE", 1, $gedrec, '', false);
				$person->deathPlace = get_gedcom_value("DEAT:PLAC", 1, $gedrec, '', false);
				$person->gender = get_gedcom_value("SEX", 1, $gedrec, '', false);
				//$search_result_element['gedcom'] = $gedrec;
				$results_array[] = $person;
			}						
		}
//			AddToLog('Found '.count($results_array).' after privatizing');
		// set the number of possible results
		//$results[0]['totalResults'] = count($results_array);
		$results_array = array_slice($results_array,$start,$max);
		//$results[0]['persons'] = $results_array;
		$return = new SOAP_Value('searchResult', 'searchResult', "");
		$return->totalResults = count($results_array);
		$return->persons = $results_array;
		return $return;
	}
}
?>
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
 * @version $Id: serviceclient_class.php,v 1.5 2007/05/29 19:21:11 lsces Exp $
 */

require_once(PHPGEDVIEW_PKG_PATH.'includes/gedcomrecord.php');
include_once(PHPGEDVIEW_PKG_PATH.'SOAP/Client.php');
include_once(PHPGEDVIEW_PKG_PATH.'includes/family_class.php');

class ServiceClient extends GedcomRecord {
	var $url = "";
	var $soapClient = null;
	var $SID = "";
	var $gedfile = "";
	var $title = "";
	var $username = "";
	var $password = "";
	var $type = "";
	var $data_type = "";
	var $DEBUG = false;

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
		$this->data_type = "GEDCOM";
		if (empty($this->url) && empty($this->gedfile))
			return null;
	}

	/**
	 * check if the service returned an error
	 */
	function isError(&$result) {
		if (PEAR::isError($result) || isset($result->faultcode) || isset($result->message) || get_class($result)=='SOAP_Fault') {
			return true;
		}
		return false;
	}

	/**
	 * get the title of this source record
	 * @return string
	 */
	function getTitle() {
		global $pgv_lang;

		if (empty($this->name)) return $pgv_lang["unknown"];
		return $this->name;
	}

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
		if (!empty($this->SID)) return $this->SID;
		if (is_null($this->soapClient)) {
			AddToLog("getting wsdl");
			//	get the wsdl and cache it
			$wsdl = new SOAP_WSDL($this->url);
			AddToLog("wsdl found");
			//change the encoding style
			$this->__change_encoding($wsdl);
			$this->soapClient = $wsdl->getProxy();
		}
		if (!$this->isError($this->soapClient)) {
			$res = $this->soapClient->Authenticate($this->username, $this->password, $this->gedfile, "",$this->data_type);
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
		else {
			addToLog("Unable to generate web service proxy from WSDL. ".print_r($this->soapClient, true));
			return false;
		}
	}
	
	/**
	 * Get a record from the remote site
	 * @param string $remoteid	the id of the record to get
	 */
	function getRemoteRecord($remoteid) {
if ($this->DEBUG) print "In getRemoteRecord($remoteid)<br />";
		if (!is_object($this->soapClient)) $this->authenticate();
		if (!is_object($this->soapClient)||$this->isError($this->soapClient)) return false;
		$rec = $this->soapClient->getGedcomRecord($this->SID, $remoteid);
		$rec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $rec);
		return $rec;
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
		//print_r($info);
		foreach($info->gedcoms as $ind=>$gedobj)
		{
			if ($gedobj->ID==$this->gedfile) break;
		}
		$this->title = $gedobj->title;
		return $this->title;
	}

	/**
	 * Merge people together.
	 */
	function _merge($record1, $record2)
	{
if ($this->DEBUG) print "In _merge()<br />";
		// Returns second record if first is empty, no merge needed
		if (empty($record1)) return $record2;
		// Returns first record if second is empty, no merge needed
		if (empty($record2)) return $record1;

		$remoterecs = get_all_subrecords($record2, "", false, false, false);
		$localrecs = get_all_subrecords($record1, "", false, false, false);

		$newrecs = array();
		//-- make sure we don't get circular links
		foreach($remoterecs as $ind2=>$subrec2) {
			if (preg_match("/1 RFN/", $subrec2)==0) {
				$newrecs[] = $subrec2;
			}
		}

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
		
		//-- start with the first line from the local record
		$pos1 = strpos($record1, "\n1");
		if ($pos1!==false) $localrec = substr($record1, 0, $pos1+1);
		else $localrec = $record1;
		
		//-- update the type of the remote record
		$ct = preg_match("/0 @(.*)@ (\w*)/", $record2, $match);
		if ($ct>0) $localrec = preg_replace("/0 @(.*)@ (\w*)/", "0 @$1@ ".trim($match[2]), $localrec);
		//-- add all of the new records
		foreach($newrecs as $ind=>$subrec) {
			$localrec .= trim($subrec)."\r\n";
		}
		$localrec = trim($localrec);
//print "[<pre>$localrec</pre>]";
		// Update the last change time
		$pos1 = strpos($localrec, "1 CHAN");
		if ($pos1!==false) {
			$pos2 = strpos($localrec, "\n1", $pos1+4);
			if ($pos2===false) $pos2 = strlen($localrec);
			$newgedrec = substr($localrec, 0, $pos1);
			$newgedrec .= "1 CHAN\r\n2 DATE ".date("d M Y")."\r\n";
			$newgedrec .= "3 TIME ".date("H:i:s")."\r\n";
			$newgedrec .= "2 _PGVU @".$this->xref."@\r\n";
			$newgedrec .= trim(substr($localrec, $pos2+1));
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
	 * Updates Family Records such as children, spouse, and parents
	 */
	 function UpdateFamily($record1,$record2){
if ($this->DEBUG) print "In UpdateFamily()<br />";
		// This makes sure there is a record in both the server and client else it returns the record that
		// exist if any
		if (empty($record1)) {
			return $record2;
		}
		elseif (empty($record2)) {
			return $record1;
		}

		$this->authenticate();
		//this makes sure that the person is the one that was clicked so that this methade is not called more then ti needs to be
		$ct = preg_match("/0 @(.*)@ (.*)/", $record1, $match);
		$personId1=null;
		if ($ct>0) {
			$personId1 = $match[1];
			$type1 = trim($match[2]);
			if ($type1!="INDI"){
				return $record1;
			}
		}
		$ct = preg_match("/0 @(.*)@ (.*)/", $record2, $match);
		if ($ct>0) {
			$personId2 = $match[1];
			$type2 = trim($match[2]);
			if ($type2!="INDI"){
				return $record1;
			}
		}
		
		//-- remove all family records so that we can add them back in if we need to
		$record1 = preg_replace("/\d FAM[SC] @".$this->xref.":[\w\d]+@\r?\n/", "", $record1);
//		print "[<pre>$record1</pre>]";
		
		//debug_print_backtrace();
		// holds the arrays of the current individual Familys
		$List1FamilyChildID = find_families_in_record($record1, "FAMC");
		$List2FamilyChildID = find_families_in_record($record2, "FAMC");
		$List1FamilySpouseID = find_families_in_record($record1, "FAMS");
		$List2FamilySpouseID = find_families_in_record($record2, "FAMS");
		$FamilyListSpouse = array();
		$FamilyListChild = array();

		// bools used to make sure the same children and/or familys are not counted more then twice
		$firstTimeFamily=true;

		// starting the comparisons for family as child
		if(empty($List1FamilyChildID)){
			//-- add all remote ids
			foreach($List2FamilyChildID as $famc=>$famCild2){
				$FamilyListChild[] = $famCild2;
			}
		}
		elseif(empty($List2FamilyChildID)){
			//-- nothing to do if there are no remote families  
		}
		else{
			// Creating the first family
			foreach($List1FamilyChildID as $famc=>$famCild1){
				if(!empty($famCild1)){
					// Creating the Secound Family
					foreach($List2FamilyChildID as $famc=>$famCild2){
						if(!empty($famCild2)){
							if(!$this->CompairForUpdateFamily($famCild1,$famCild2)){
								if($firstTimeFamily){
									$FamilyListChild[] = $famCild2;
								}
							}
							else {
								$this->MergeForUpdateFamily($famCild1,$famCild2,$FamilyListChild,$FamilyListChild);
							}
						}
					}
				}
			}
		}
		// starting the comparisons for family as spouse
		if(empty($List1FamilySpouseID)){
			//-- add all remote ids
			foreach($List2FamilySpouseID as $fams=>$famSpouse2){
				if(!empty($famSpouse2)){
					$FamilyListSpouse[] = $famSpouse2;
				}
			}
		}
		elseif(empty($List2FamilySpouseID)){
			//-- don't do anything if there are no remote families
		}
		else{
			// Creating the first family
			foreach($List1FamilySpouseID as $fams=>$famSpouse1){
				if(!empty($famSpouse1)){
					// Creating the Secound Family
					foreach($List2FamilySpouseID as $fams=>$famSpouse2){
						if(!empty($famSpouse2)){
							if(!$this->CompairForUpdateFamily($famSpouse1,$famSpouse2)){
								if($firstTimeFamily){
									$FamilyListSpouse[] = $famSpouse2;
								}
							}
							else {
								$this->MergeForUpdateFamily($famSpouse1,$famSpouse2,$FamilyListSpouse,$FamilyListSpouse);
							}
						}
					}
				}
			}
		}
		// This Adds any new familys to the person.
		if(count($FamilyListChild)>0){
			for($i=0;$i<count($FamilyListChild);$i++){
				if (!empty($FamilyChildrenList[$i])){
					$record1.="\r\n1 FAMC @".$this->xref.":".$FamilyListChild[$i]."@";
//					replace_gedrec($personId1, $record1);
				}
			}
		}
		if(count($FamilyListSpouse)>0){
			for($i=0;$i<count($FamilyListSpouse);$i++){
				if (!empty($FamilyChildrenList[$i])){
					$record1.="\r\n1 FAMS @".$this->xref.":".$FamilyListSpouse[$i]."@";
//					replace_gedrec($personId1, $record1);
				}
			}
		}
		return $record1;
	}

	/**
	 * This mergest the the two familys together
	 */
	 function MergeForUpdateFamily($Family1,$Family2,$Familylist,&$FamilyListReturn){
		global $indilist, $famlist, $pgv_changes, $GEDCOM;
if ($this->DEBUG) print "In MergeForUpdateFamily()<br />";
		include_once('includes/functions_edit.php');

		//print "<br />In MergeForUpdateFamily ".$Family1." ".$Family2;
		//print_r($Familylist);
		$FamilyListReturn=$Familylist;

		if (isset($pgv_changes[$Family1."_".$GEDCOM])) $famrec1 = find_record_in_file($Family1);
		else $famrec1 = find_family_record($Family1);

		$ct = preg_match("/(\w+):(.+)/", $Family2, $match);
		if ($ct>0) {
			$servid = trim($match[1]);
			$remoteid = trim($match[2]);
			$famrec2 = $this->getRemoteRecord($remoteid);
		}
		else return $famrec1;
		
		// Creating the familys from the xref
		$family1 = Family::getInstance($Family1);
		$family2 = new Family($famrec2);

		// Creat the fathers if their is some
		$father1 = $family1->getHusband();
		$father2 = $family2->getHusband();

		// Creat the mothers if their is some
		$mother1=$family1->getWife();
		$mother2=$family2->getWife();

		// Creat an array of Children
		$children1=$family1->getChildren();
		$children2=$family2->getChildren();

		if(count($FamilyListReturn)>0){ // removes the updated family from the list so it does not get added later.
			$index=null;
			for($i=0; $i<count($FamilyListReturn); $i++){
				if($FamilyListReturn[$i]==$Family2){
					$ndex=$i;
					break;
				}
			}
			if($index!=null){
					unset($FamilyListReturn[$index]);
			}
		}

		$famupdated = false;
		// Merging starts here, the merging of children.
		if(empty($children1)){$children1=$children2;}
		elseif(empty($children2)){}
		else{
			// Children are looped to see if they need to be added or merged to an esisting child
			foreach($children2 as $childID2=>$Child2){
				if(!empty($Child2)) {
					//print "<br/>child 2 Xref ".$Child2->getXref()."-".$childID2;

					$found = false;
					//-- compare to children in local family
					foreach($children1 as $childID1=>$Child1){
						if(!empty($Child1)){
							//print "<br/>child 2 Xref ".$Child2->getXref()." == ".$Child1->getXref();
							$found=$this->ComparePeople($Child1,$Child2);
							if ($found) break;
						}
					}
					if($found){
						$childrec = $Child1->getGedcomRecord();
						if (preg_match("/1 RFN ".$this->xref.":/", $childrec)==0) {
							$childrec .= "\r\n1 RFN ".$Child2->getXref();
							//print "<br/> repalcing for child ".$Child1->getXref();
							$indilist[$Child1->getXref()]['gedcom']=$childrec;
							replace_gedrec($Child1->getXref(), $childrec);
							$this->setSameId($Child1->getXref(), $Child2->getXref());
						}
					}
					else {
						$famupdated = true;
						$famrec1 .="\r\n1 CHIL @".$Child2->getXref()."@";
						//print "<br/> adding for child ".$Child2->getXref();
					}
				}
			}
		}
		
		//-- update the family record
		if (preg_match("/1 RFN ".$this->xref.":/", $famrec1)==0) {
			$famrec1 .= "\r\n1 RFN ".$family2->getXref();
			$famupdated = true;
		}
		if ($famupdated) {
			//print "<br /> updating family record ".$family1->getXref();
			$famlist[$family1->getXref()]['gedcom']=$famrec1;
			replace_gedrec($family1->getXref(), $famrec1);
		}

		// Merge Father basicly they just add the rfn numer and let the merge handle it latter
		if(empty($father1)){
			if(!empty($father2)){
				$father1=$father2;
				$famrec1 .="\r\n1 HUSB @".$father1->getXref()."@";
				//print "<br/> adding for fahter ".$father1->getXref();
				$famlist[$family1->getXref()]['gedcom']=$famrec1;
				replace_gedrec($family1->getXref(), $famrec1);
			}
		}
		elseif(!empty($father2)){
			if($this->ComparePeople($father1,$father2)){
				$fatherrec = $father1->getGedcomRecord();
				if (preg_match("/1 RFN ".$this->xref.":/", $fatherrec)==0) {
					$fatherrec .= "\r\n1 RFN ".$father2->getXref();
					//print "<br/> repalcing for father ".$father1->getXref();
					$indilist[$father1->getXref()]['gedcom']=$fatherrec;
					replace_gedrec($father1->getXref(), $fatherrec);
					$this->setSameId($father1->getXref(), $father2->getXref());
				}
			}
		}
		// Merge Mother
		if(empty($mother1)){
			if(!empty($mother2)){
				$mother1=$mother2;
				$famrec1 .="\r\n1 WIFE @".$mother1->getXref()."@";
				//print "<br/> adding for mother ".$mother1->getXref();
				$famlist[$family1->getXref()]['gedcom']=$famrec1;
				replace_gedrec($family1->getXref(), $famrec1);
			}
		}
		else if(!empty($mother2)){
			if($this->ComparePeople($mother1,$mother2)){
				$motherrec = $mother1->getGedcomRecord();
				if (preg_match("/1 RFN ".$this->xref.":/", $motherrec)==0) {
					$motherrec .= "\r\n1 RFN ".$mother2->getXref();
					//print "<br/> repalcing for mother ".$mother1->getXref();
					$indilist[$mother1->getXref()]['gedcom']=$motherrec;
					replace_gedrec($mother1->getXref(), $motherrec);
					$this->setSameId($mother1->getXref(), $mother2->getXref());
				}
			}
		}
		$this->setSameId($Family1, $Family2);
	 }

	/**
	 * Compairs familys and then returns true if the have 50% or more chance of being the same family.
	 * Other wise it returns false.
	 */
	 function CompairForUpdateFamily($family1,$family2)
	 {
if ($this->DEBUG) print "In CompairForUpdateFamily()<br />";
		 // Values used to calculate the Percent of likley hood that the family is the same.
		$ChanceSameFamily=0.0;
		$CountFamily1=0.0;
		$CountFamily2=0.0;
		$ChanceSame=0.0;

		$firstTimeChildren=true;

		$famrec1 = find_family_record($family1);
		$ct = preg_match("/(\w+):(.+)/", $family2, $match);
		if ($ct>0) {
			$servid = trim($match[1]);
			$remoteid = trim($match[2]);
			$famrec2 = $this->getRemoteRecord($remoteid);
		}
		else return false;
		$family1 = Family::getInstance($family1);
		$family2 = new Family($famrec2);

		// Creat the fathers if their is some
		$father1 = $family1->getHusband();
		$father2 = $family2->getHusband();
		$CountFamily1+=1.0;
		$CountFamily2+=1.0;
		if(empty($father1)){
			unset($father1);
			$CountFamily1-=1.0;
		}
		if(empty($father2)){
			unset($father2);
			$CountFamily2-=1.0;
		}

		// Creat the mothers if their is some
		$mother1=$family1->getWife();
		$mother2=$family2->getWife();
		$CountFamily1+=1.0;
		$CountFamily2+=1.0;
		if(empty($mother1)){
			unset($mother1);
			$CountFamily1-=1.0;
		}
		if(empty($mother2)){
			unset($mother2);
			$CountFamily2-=1.0;
		}

		// Creat an array of Children
		$children1=$family1->getChildren();
		$children2=$family2->getChildren();

		// finds the probablity that they are the same family Bassed of both sites information
		$CountFamily1 += count($children1);
		$CountFamily2 += count($children2);
		foreach($children1 as $childID1=>$Person1){
			if (!empty($Person1)) {
				foreach($children2 as $childID2=>$Person2){
					if(!empty($Person2)){
						if($this->ComparePeople($Person1,$Person2)){
							$ChanceSameFamily+=1.0;
							//print "<br />".$Person1->getXref()." equals ".$Person2->getXref();
							break;
						}
					}
				}
			}
		}

		if(empty($father1)){}
		elseif(empty($father2)){}
		else{
			if($this->ComparePeople($father1,$father2)){
				$ChanceSameFamily+=1.0;
			}
		}
		if(empty($mother1)){}
		elseif(empty($mother2)){}
		else{
			if($this->ComparePeople($mother1,$mother2)){
				$ChanceSameFamily+=1.0;
			}
		}
		if($CountFamily1!=0&&$CountFamily2!=0){
			$ChanceSame=(($ChanceSameFamily/$CountFamily1)+($ChanceSameFamily/$CountFamily2))/2;
			//print "<br />chancesame=".$ChanceSameFamily." count1=".$CountFamily1." count2=".$CountFamily2." ".$family1->getXref()." compared to ".$family2->getXref()." is ".$ChanceSame;
		}
		else
			return false;

		if($ChanceSame<0.5){ // If the probabilty is less then 0.5 or 50% then the current family is stored here to be added later
			return false;
		}
		else { return true; }
	}
	
	/**
	 * set two ids in the same person
	 * @param string $local	The local id
	 * @param string $remote	the remote id that matches the $local id
	 */
	function setSameId($local, $remote) {
		global $gBitSystem, $GEDCOMS, $GEDCOM;
		
		if ($local == $remote) {
			debug_print_backtrace();
			return;
		}
		//-- check if the link already exists
		$gid = get_remote_id($remote);
		if (empty($gid)) {
			$sql = "INSERT INTO ".PHPGEDVIEW_DB_PREFIX."remotelinks VALUES ( ?, ?, ? )";
			$res = $gBitSystem->mDb->query( $sql, array( $local, $remote, $GEDCOMS[$GEDCOM]["id"] ));
		}
	}
	
	/**
	 * Merges two people together
	 * --not used
	 function MergePeople($Person1,$Person2){
		$PersonName1=$Person1->getName();
		$PersonSex1=$Person1->getSex();
		$PersonBirth1=$Person1->getBirthDate();
		$PersonDeath1=$Person1->getDeathDate();
		$PersonName2=$Person2->getName();
		$PersonSex2=$Person2->getSex();
		$PersonBirth2=$Person2->getBirthDate();
		$PersonDeath2=$Person2->getDeathDate();
		if (empty($PersonName1)){$PersonName1=$PersonName2;}
		elseif(empty($PersonName2)){}
		if (empty($PersonSex1)){$PersonSex1=$Person2;}
		elseif (empty($PersonSex2)){}
		if (empty($PersonBirth1)){$PersonBirth1=$PersonBirth2;}
		elseif(empty($PersonBirth2)){}
		if (empty($PersonDeath1)){$PersonDeath1=$PersonDeath2;}
		elseif(empty($PersonDeath2)){}
		return $Person1;
	 }
	 */

	/**
	 * Compares to see if two people are the same, and it returns true if they are, but
	 * false if they are not. It only compares the name, sex birthdate, and deathdate
	 * of the person
	 */
	 function ComparePeople(&$Person1,&$Person2){
		$PersonName1=$Person1->getName();
		$PersonSex1=$Person1->getSex();
		$PersonBirth1=$Person1->getBirthDate();
		$PersonDeath1=$Person1->getDeathDate();

		$PersonName2=$Person2->getName();
		$PersonSex2=$Person2->getSex();
		$PersonBirth2=$Person2->getBirthDate();
		$PersonDeath2=$Person2->getDeathDate();

		$count=0;
		$Probabilty=0;
		//print "<br/>".$PersonName1." == ".$PersonName2;
		//print "<br/>".$PersonBirth1." == ".$PersonBirth2;
		//print "<br/>".$PersonDeath1." == ".$PersonDeath2;
		//print "<br/>".$PersonSex1." == ".$PersonSex2;
		if (!empty($PersonName1)&&!empty($PersonName2)){
			$lev = levenshtein(str2lower($PersonName1), str2lower($PersonName2));
			//print "Levenshtein = $lev ";
			if($lev<4){
				$Probabilty+=2;
			}
			else
				$Probabilty-=2;
			$count+=2;
		}
		if (!empty($PersonSex1)&&!empty($PersonSex2)){
			if($PersonSex1==$PersonSex2){
				//print "same sex ";
				$Probabilty+=1;
			}
			else {
				//print "not same sex ";
				$Probabilty-=2;
			}
			$count++;
		}
		if (!empty($PersonBirth1)&&!empty($PersonBirth2)){
			if($PersonBirth1==$PersonBirth2){
				//print "same birth ";
				$Probabilty+=1;
			}
			else {
				//print "not same birth ";
				$Probabilty-=1;
			}
			$count++;
		}
		if (!empty($PersonDeath1)&&!empty($PersonDeath2)){
			if($PersonDeath1==$PersonDeath2){
				//print "same death ";
				$Probabilty+=1;
			}
			else {
				//print "not same death ";
				$Probabilty-=1;
			}
			$count++;
		}
		$prob=$Probabilty/$count;
		//print "<br/>Probabilty same person % is ".$prob."=".$Probabilty."/".$count." ID 1 ".$Person1->getXref()." ID 2 ".$Person2->getXref();
		if($prob<0.5){
			return false;
		}
		else{
			return true;
		}
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
			$gid = get_remote_id($id);
			if ($gid!==false)
			$gedrec = preg_replace("/@".$id."@/", "@".$gid."@", $gedrec);
			}
			return $gedrec;
		}

	/**
	 * merge a local gedcom record with the information from the remote site
	 */
	function mergeGedcomRecord($xref, $localrec, $isStub=false, $firstLink=false) {
		global $FILE, $GEDCOM, $indilist, $famlist, $sourcelist, $otherlist;
		global $GEDCOMS, $pgv_changes;
if ($this->DEBUG) print "In mergeGedcomRecord($xref)<br />";
		$FILE = $GEDCOM;
		if (!$isStub) {
			$gedrec = find_gedcom_record($this->xref.":".$xref);
			if (!empty($gedrec)) $localrec = $gedrec;
		}

		//-- used to force an update on the first time linking a person
		if ($firstLink) {
			$this->authenticate();
			$result = $this->soapClient->getGedcomRecord($this->SID, $xref);
			//print_r($result);
			if (PEAR::isError($result) || isset($result->faultcode) || get_class($result)=='SOAP_Fault' || is_object($result)) {
				if (isset($result->faultstring)) {
					AddToLog($result->faultstring);
					print $result->faultstring;
				}
				return $localrec;
			}
			$gedrec = $result;
			$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
			$gedrec = $this->checkIds($gedrec);
			$localrec = $this->_merge($localrec, $gedrec);
			//print "why<pre>".$localrec."</pre>Whynot?";
			include_once("includes/functions_edit.php");
			$localrec = $this->UpdateFamily($localrec,$gedrec);
			$ct=preg_match("/0 @(.*)@/", $localrec, $match);
			if ($ct>0)
			{
				$pid = trim($match[1]);
				replace_gedrec($pid,$localrec);
			}
		}

		//-- get the last change date of the record
		$change_date = get_gedcom_value("CHAN:DATE", 1, $localrec, '', false);
		if (empty($change_date)) {
			//print $xref." no change<br />";
			$this->authenticate();
			if (!is_object($this->soapClient) || $this->isError($this->soapClient)) return false;
			$result = $this->soapClient->getGedcomRecord($this->SID, $xref);
			//print_r($result);
			if (PEAR::isError($result) || isset($result->faultcode) || get_class($result)=='SOAP_Fault' || is_object($result)) {
				if (isset($result->faultstring)) {
					AddToLog($result->faultstring);
					print $result->faultstring;
				}
				return $localrec;
			}
			$gedrec = $result;
			$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
			$gedrec = $this->checkIds($gedrec);
			$localrec = $this->_merge($localrec, $gedrec);
			$ct=preg_match("/0 @(.*)@/", $localrec, $match);
			if ($ct>0)
			{
				$pid = trim($match[1]);
				if ($isStub) {
					include_once("includes/functions_edit.php");
					//$indilist[$localrec->getXref()]['gedcom']=$localrec;
					$localrec = $this->UpdateFamily($localrec,$gedrec);
					replace_gedrec($pid,$localrec);
				}
				else {
					require_once("includes/functions_import.php");
if ($this->DEBUG) debug_print_backtrace();
if ($this->DEBUG) print __LINE__."adding record to the database ".$localrec;
					update_record($localrec);
				}
			}
		}
		else {
			$chan_date = parse_date($change_date);
			$chan_time_str = get_gedcom_value("CHAN:DATE:TIME", 1, $localrec, '', false);
			$chan_time = parse_time($chan_time_str);
			$change_time = mktime($chan_time[0], $chan_time[1], $chan_time[2], (int)$chan_date[0]['mon'], (int)$chan_date[0]['day'], $chan_date[0]['year']);
			/**
			 * @todo make the timeout a config option
			 */
			// Time Clock (determines how often a record is checked)
			if ($change_time < time()-(60*60*24*14)) // if the last update (to the remote individual) was made more than 14 days ago
			{
				//$change_date= "1 JAN 2000";
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
					require_once("includes/functions_import.php");
if ($this->DEBUG) print __LINE__."adding record to the database ".$localrec;
					update_record($localrec);
				}
				// If changes have been made to the remote record
				else {
					$gedrec = $person->gedcom;
					$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
					$gedrec = $this->checkIds($gedrec);
					$ct=preg_match("/0 @(.*)@/", $localrec, $match);
					if ($ct>0)
					{
						$pid = trim($match[1]);
						if (isset($pgv_changes[$pid."_".$GEDCOM])) $localrec = find_record_in_file($pid);
						$localrec = $this->_merge($localrec, $gedrec);
						if ($isStub) {
							include_once("includes/functions_edit.php");
							//$indilist[$localrec->getXref()]['gedcom']=$localrec;
							$localrec = $this->UpdateFamily($localrec,$gedrec);
							replace_gedrec($pid,$localrec);
						}
						else {
							require_once("includes/functions_import.php");
if ($this->DEBUG) print __LINE__."adding record to the database ".$localrec;
							update_record($localrec);
						}
					}
				}
			}
		}

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
}
?>
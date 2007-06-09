<?php
/**
 * Class file for a Repository (REPO) object
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
 * @version $Id: repository_class.php,v 1.3 2007/06/09 21:11:04 lsces Exp $
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

require_once(PHPGEDVIEW_PKG_PATH.'includes/gedcomrecord.php');

class Repository extends GedcomRecord {
	var $disp = true;
	var $name = "";
	var $sourcelist = null;

	/**
	 * Constructor for repository object
	 * @param string $gedrec	the raw repository gedcom record
	 */
	function Repository($gedrec) {
		parent::GedcomRecord($gedrec);
		$this->disp = displayDetailsByID($this->xref, "REPO");
		$this->name = get_repo_descriptor($this->xref);
		$add_descriptor = get_add_repo_descriptor($this->xref);
		if ($add_descriptor) $this->name .= " - ".$add_descriptor;
	}

	/**
	 * Static function used to get an instance of a repository object
	 * @param string $pid	the ID of the repository to retrieve
	 */
	function &getInstance($pid, $simple=true) {
		global $repolist, $GEDCOM, $pgv_changes;

		if (isset($repolist[$pid]) && $repolist[$pid]['gedfile']==$gGedcom->mGEDCOMId) {
			if (isset($repolist[$pid]['object'])) return $repolist[$pid]['object'];
		}

		$repositoryrec = find_repository_record($pid);
		if (empty($repositoryrec)) {
			$ct = preg_match("/(\w+):(.+)/", $pid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				$service = ServiceClient::getInstance($servid);
				$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$pid."@ REPO\r\n1 RFN ".$pid, false);
				$repositoryrec = $newrec;
			}
		}
		if (empty($repositoryrec)) {
			if ($gGedcom->isEditable() && isset($pgv_changes[$pid."_".$GEDCOM])) {
				$repositoryrec = find_updated_record($pid);
				$fromfile = true;
			}
		}
		if (empty($repositoryrec)) return null;
		$repository = new Repository($repositoryrec, $simple);
		if (!empty($fromfile)) $repository->setChanged(true);
		$repolist[$pid]['object'] = &$repository;
		return $repository;
	}

	/**
	 * Check if privacy options allow this record to be displayed
	 * @return boolean
	 */
	function canDisplayDetails() {
		return $this->disp;
	}

	/**
	 * get the repository name
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * get the repository sortable name
	 * @return string
	 */
	function getSortableName($subtag="") {
		global $pgv_lang;
		if (!$this->canDisplayDetails()) {
			if (empty($subtag)) return $pgv_lang["private"];
			else return "";
		}
		if (empty($subtag)) return get_gedcom_value("NAME", 1, $this->gedrec, '', false);
		else return get_gedcom_value("NAME:".$subtag, 1, $this->gedrec, '', false);
	}

	/**
	 * get the repository additional name
	 * @return string
	 */
	function getAddName() {
		$addn = get_gedcom_value("NAME:_HEB", 1, $this->gedrec, '', false);
		if (empty($addn)) $addn = get_gedcom_value("NAME:ROMN", 1, $this->gedrec, '', false);
		return $addn;
	}

	/**
	 * get repository facts array
	 * @return array
	 */
	function getRepositoryFacts() {
		$this->parseFacts();
		return $this->repositoryfacts;
	}

	/**
	 * Parse the facts from the repository record
	 */
	function parseFacts() {
		if (!is_null($this->repositoryfacts)) return;
		$this->repositoryfacts = array();
		$gedlines = preg_split("/\n/", $this->gedrec);
		$lct = count($gedlines);
		$factrec = "";	// -- complete fact record
		$line = "";	// -- temporary line buffer
		$linenum = 1;
		for($i=1; $i<=$lct; $i++) {
			if ($i<$lct) $line = $gedlines[$i];
			else $line=" ";
			if (empty($line)) $line=" ";
			if (($i==$lct)||($line{0}==1)) {
				if (!empty($factrec) ) {
					$this->repositoryfacts[] = array($factrec, $linenum);
				}
				$factrec = $line;
				$linenum = $i;
			}
			else $factrec .= "\n".$line;
		}
	}

	/**
	 * Merge the facts from another Repository object into this object
	 * for generating a diff view
	 * @param Repository $diff	the repository to compare facts with
	 */
	function diffMerge(&$diff) {
		if (is_null($diff)) return;
		$this->parseFacts();
		$diff->parseFacts();

		//-- update old facts
		foreach($this->repositoryfacts as $key=>$fact) {
			$found = false;
			foreach($diff->repositoryfacts as $indexval => $newfact) {
				$newfact=preg_replace("/\\\/", "/", $newfact);
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$this->repositoryfacts[$key][0].="\nPGV_OLD\n";
			}
		}
		//-- look for new facts
		foreach($diff->repositoryfacts as $key=>$newfact) {
			$found = false;
			foreach($this->repositoryfacts as $indexval => $fact) {
				$newfact=preg_replace("/\\\/", "/", $newfact);
				if (trim($newfact[0])==trim($fact[0])) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$newfact[0].="\nPGV_NEW\n";
				$this->repositoryfacts[]=$newfact;
			}
		}
	}

	/**
	 * get the list of sources connected to this repository
	 * @return array
	 */
	function getRepositorySours() {
		global $REGEXP_DB;
		if (!is_null($this->sourcelist)) return $this->sourcelist;
		$query = "REPO @".$this->xref."@";
		if (!$REGEXP_DB) $query = "%".$query."%";

		$this->sourcelist = search_sources($query);
		uasort($this->sourcelist, "itemsort");
		return $this->sourcelist;
	}

	/**
	 * get the URL to link to this repository
	 * @string a url that can be used to link to this repository
	 */
	function getLinkUrl() {
		global $GEDCOM;

		$url = "repo.php?rid=".$this->getXref()."&amp;ged=".$GEDCOM;
		if ($this->isRemote()) {
			$parts = preg_split("/:/", $this->rfn);
			if (count($parts)==2) {
				$servid = $parts[0];
				$aliaid = $parts[1];
				if (!empty($servid)&&!empty($aliaid)) {
					$serviceClient = ServiceClient::getInstance($servid);
					if (!empty($serviceClient)) {
						$surl = $serviceClient->getURL();
						$url = "repo.php?rid=".$aliaid;
						if ($serviceClient->getType()=="remote") {
							if (!empty($surl)) $url = dirname($surl)."/".$url;
						}
						else {
							$url = $surl.$url;
						}
						$gedcom = $serviceClient->getGedfile();
						if (!empty($gedcom)) $url.="&amp;ged=".$gedcom;
					}
				}
			}
		}
		return $url;
	}
}
?>

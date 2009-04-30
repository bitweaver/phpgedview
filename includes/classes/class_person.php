<?php
/**
* Class file for a person
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
* @package PhpGedView
* @subpackage DataModel
* @version $Id: class_person.php,v 1.2 2009/04/30 21:39:51 lsces Exp $
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_CLASS_PERSON_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_gedcomrecord.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_event.php');

class Person extends GedcomRecord {
	var $indifacts = array();
	var $otherfacts = array();
	var $globalfacts = array();
	var $mediafacts = array();
	var $facts_parsed = false;
	var $fams = null;
	var $famc = null;
	var $spouseFamilies = null;
	var $childFamilies = null;
	var $label = "";
	var $highlightedimage = null;
	var $file = "";
	var $age = null;
	var $isdead = -1;
	var $sex=null;
	var $generation; // used in some lists to keep track of this Person's generation in that list

	// Cached results from various functions.
	private $_getBirthDate=null;
	private $_getBirthPlace=null;
	private $_getAllBirthDates=null;
	private $_getAllBirthPlaces=null;
	private $_getEstimatedBirthDate=null;
	private $_getDeathDate=null;
	private $_getDeathPlace=null;
	private $_getAllDeathDates=null;
	private $_getAllDeathPlaces=null;
	private $_getEstimatedDeathDate=null;

	// Create a Person object from either raw GEDCOM data or a database row
	function Person($data, $simple=true) {
		if (is_array($data)) {
			// Construct from a row from the database
			$this->isdead=$data['i_isdead'];
			$this->sex   =$data['i_sex'];
		} else {
			// Construct from raw GEDCOM data
		}

		parent::GedcomRecord($data, $simple);

		$this->dispname=$this->disp || showLivingNameById($this->xref);
	}

	// Get an instance of a Person.  We either specify
	// an XREF (in the current gedcom), or we can provide a row
	// from the database (if we anticipate the record hasn't
	// been fetched previously).
	static function &getInstance($data, $simple=true) {
		global $gedcom_record_cache, $GEDCOM, $pgv_changes;

		if (is_array($data)) {
			$ged_id=$data['ged_id'];
			$pid   =$data['xref'];
		} else {
			$ged_id=get_id_from_gedcom($GEDCOM);
			$pid   =$data;
		}

		// Check the cache first
		if (isset($gedcom_record_cache[$pid][$ged_id])) {
			return $gedcom_record_cache[$pid][$ged_id];
		}

		// Look for the record in the database
		if (!is_array($data)) {
			$data=fetch_person_record($pid, $ged_id);

			// If we didn't find the record in the database, it may be remote
			if (!$data && strpos($pid, ':')) {
				list($servid, $remoteid)=explode(':', $pid);
				$service=ServiceClient::getInstance($servid);
				if ($service) {
					// TYPE will be replaced with the type from the remote record
					$data=$service->mergeGedcomRecord($remoteid, "0 @{$pid}@ TYPE\n1 RFN {$pid}", false);
				}
			}

			// If we didn't find the record in the database, it may be new/pending
			if (!$data && PGV_USER_CAN_EDIT && isset($pgv_changes[$pid.'_'.$GEDCOM])) {
				$data=find_updated_record($pid);
				$fromfile=true;
			}

			// If we still didn't find it, it doesn't exist
			if (!$data) {
				return null;
			}
		}

		// Create the object
		$object=new Person($data, $simple);
		if (!empty($fromfile)) {
			$object->setChanged(true);
		}

		// Store it in the cache
		$gedcom_record_cache[$object->xref][$object->ged_id]=&$object;
		//-- also store it using its reference id (sid:pid and local gedcom for remote links)
		$gedcom_record_cache[$pid][$ged_id]=&$object;
		return $object;
	}

	// Static helper function to sort an array of people by birth date
	static function CompareBirtDate($x, $y) {
		return GedcomDate::Compare($x->getEstimatedBirthDate(), $y->getEstimatedBirthDate());
	}

	// Static helper function to sort an array of people by death date
	static function CompareDeatDate($x, $y) {
		return GedcomDate::Compare($x->getEstimatedDeathDate(), $y->getEstimatedDeathDate());
	}

	/**
	* Return whether or not this person is already dead
	* @return boolean true if dead, false if alive
	*/
	function isDead() {
		if ($this->isdead==-1) {
			$this->isdead=is_dead($this->gedrec);
		}
		return $this->isdead;
	}

	/**
	* get highlighted media
	* @return array
	*/
	function findHighlightedMedia() {
		if (is_null($this->highlightedimage)) {
			$this->highlightedimage = find_highlighted_object($this->xref, $this->gedrec);
		}
		return $this->highlightedimage;
	}

	/**
	* get birth date
	* @return GedcomDate the birth date
	*/
	function getBirthDate() {
		global $pgv_lang;

		if (is_null($this->_getBirthDate)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllBirthDates() as $date) {
					if ($date->isOK()) {
						$this->_getBirthDate=$date;
						break;
					}
				}
				if (is_null($this->_getBirthDate)) {
					$this->_getBirthDate=new GedcomDate('');
				}
			} else {
				$this->_getBirthDate=new GedcomDate("({$pgv_lang['private']})");
			}
		}
		return $this->_getBirthDate;
	}

	/**
	* get the birth place
	* @return string
	*/
	function getBirthPlace() {
		global $pgv_lang;
		if (is_null($this->_getBirthPlace)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllBirthPlaces() as $place) {
					if ($place) {
						$this->_getBirthPlace=$place;
						break;
					}
				}
				if (is_null($this->_getBirthPlace)) {
					$this->_getBirthPlace='';
				}
			} else {
				$this->_getBirthPlace=$pgv_lang['private'];
			}
		}
		return $this->_getBirthPlace;
	}

	/**
	* get the Census birth place (Town and County (reversed))
	* @return string
	*/
	function getCensBirthPlace() {
		global $pgv_lang;
		if (is_null($this->_getBirthPlace)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllBirthPlaces() as $place) {
					if ($place) {
						$this->_getBirthPlace=$place;
						break;
					}
				}
				if (is_null($this->_getBirthPlace)) {
					$this->_getBirthPlace='';
				}
			} else {
				$this->_getBirthPlace=$pgv_lang['private'];
			}
		}
		$censbirthplace = $this->_getBirthPlace;
		$censbirthplace = explode(", ", $censbirthplace);
		$censbirthplace = array_reverse($censbirthplace);
		$censbirthplace = array_slice($censbirthplace, 1);
		$censbirthplace = array_slice($censbirthplace, 0, 2);
		$censbirthplace = implode(", ", $censbirthplace);
		return $censbirthplace;
	}

	/**
	* get the birth year
	* @return string the year of birth
	*/
	function getBirthYear(){
		return $this->getBirthDate()->MinDate()->Format('Y');
	}

	/**
	* get death date
	* @return GedcomDate the death date in the GEDCOM format of '1 JAN 2006'
	*/
	function getDeathDate($estimate = true) {
		global $pgv_lang;

		if (is_null($this->_getDeathDate)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllDeathDates() as $date) {
					if ($date->isOK()) {
						$this->_getDeathDate=$date;
						break;
					}
				}
				if (is_null($this->_getDeathDate)) {
					$this->_getDeathDate=new GedcomDate('');
				}
			} else {
				$this->_getDeathDate=new GedcomDate("({$pgv_lang['private']})");
			}
		}
		return $this->_getDeathDate;
	}

	/**
	* get the death place
	* @return string
	*/
	function getDeathPlace() {
		global $pgv_lang;

		if (is_null($this->_getDeathPlace)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllDeathPlaces() as $place) {
					if ($place) {
						$this->_getDeathPlace=$place;
						break;
					}
				}
				if (is_null($this->_getDeathPlace)) {
					$this->_getDeathPlace='';
				}
			} else {
				$this->_getDeathPlace=$pgv_lang['private'];
			}
		}
		return $this->_getDeathPlace;
	}

	/**
	* get the death year
	* @return string the year of death
	*/
	function getDeathYear() {
		return $this->getDeathDate()->MinDate()->Format('Y');
	}

	// Get all the dates/places for births/deaths - for the INDI lists
	function getAllBirthDates() {
		if (is_null($this->_getAllBirthDates)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', PGV_EVENTS_BIRT) as $event) {
					if ($this->_getAllBirthDates=$this->getAllEventDates($event)) {
						break;
					}
				}
			} else {
				$this->_getAllBirthDates=array();
			}
		}
		return $this->_getAllBirthDates;
	}
	function getAllBirthPlaces() {
		if (is_null($this->_getAllBirthPlaces)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', PGV_EVENTS_BIRT) as $event) {
					if ($this->_getAllBirthPlaces=$this->getAllEventPlaces($event)) {
						break;
					}
				}
			} else {
				$this->_getAllBirthPlaces=array();
			}
		}
		return $this->_getAllBirthPlaces;
	}
	function getAllDeathDates() {
		if (is_null($this->_getAllDeathDates)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', PGV_EVENTS_DEAT) as $event) {
					if ($this->_getAllDeathDates=$this->getAllEventDates($event)) {
						break;
					}
				}
			} else {
				$this->_getAllDeathDates=array();
			}
		}
		return $this->_getAllDeathDates;
	}
	function getAllDeathPlaces() {
		if (is_null($this->_getAllDeathPlaces)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', PGV_EVENTS_DEAT) as $event) {
					if ($this->_getAllDeathPlaces=$this->getAllEventPlaces($event)) {
						break;
					}
				}
			} else {
				$this->_getAllDeathPlaces=array();
			}
		}
		return $this->_getAllDeathPlaces;
	}

	// Generate an estimate for birth/death dates, based on dates of parents/children/spouses
	function getEstimatedBirthDate() {
		if (is_null($this->_getEstimatedBirthDate)) {
			foreach ($this->getAllBirthDates() as $date) {
				if ($date->isOK()) {
					$this->_getEstimatedBirthDate=$date;
					break;
				}
			}
			if (is_null($this->_getEstimatedBirthDate)) {
				$min=array();
				$max=array();
				$tmp=$this->getDeathDate();
				if ($tmp->MinJD()) {
					global $MAX_ALIVE_AGE;
					$min[]=$tmp->MinJD()-$MAX_ALIVE_AGE*365;
					$max[]=$tmp->MaxJD();
				}
				foreach ($this->getChildFamilies() as $family) {
					foreach ($family->getChildren() as $child) {
						$tmp=$child->getBirthDate();
						if ($tmp->MinJD()) {
							$min[]=$tmp->MaxJD()-365*($this->getSex()=='F'?45:65);
							$max[]=$tmp->MinJD()-365*15;
						}
					}
				}
				foreach ($this->getSpouseFamilies() as $family) {
					$tmp=$family->getMarriageDate();
					if (is_object($tmp) && $tmp->MinJD()) {
						$min[]=$tmp->MaxJD()-365*45;
						$max[]=$tmp->MinJD()-365*15;
					}
					if ($spouse=$family->getSpouse($this)) {
						$tmp=$spouse->getBirthDate();
						if (is_object($tmp) && $tmp->MinJD()) {
							$min[]=$tmp->MaxJD()-365*25;
							$max[]=$tmp->MinJD()+365*25;
						}
					}
				}
				if ($min && $max) {
					list($y)=GregorianDate::JDtoYMD(floor((max($min)+min($max))/2));
					$this->_getEstimatedBirthDate=new GedcomDate("EST {$y}");
				} else {
					$this->_getEstimatedBirthDate=new GedcomDate(''); // always return a date object
				}
			}
		}
		return $this->_getEstimatedBirthDate;
	}
	function getEstimatedDeathDate() {
		if (is_null($this->_getEstimatedDeathDate)) {
			foreach ($this->getAllDeathDates() as $date) {
				if ($date->isOK()) {
					$this->_getEstimatedDeathDate=$date;
					break;
				}
			}
			if (is_null($this->_getEstimatedDeathDate)) {
				$tmp=$this->getEstimatedBirthDate();
				if ($tmp->MinJD()) {
					global $MAX_ALIVE_AGE;
					$tmp2=$tmp->AddYears($MAX_ALIVE_AGE, 'bef');
					if ($tmp2->MaxJD()<server_jd()) {
						$this->_getEstimatedDeathDate=$tmp2;
					} else {
						$this->_getEstimatedDeathDate=new GedcomDate(''); // always return a date object
					}
				} else {
					$this->_getEstimatedDeathDate=new GedcomDate(''); // always return a date object
				}
			}
		}
		return $this->_getEstimatedDeathDate;
	}

	/**
	* get the sex
	* @return string  return M, F, or U
	*/
	function getSex() {
		if (is_null($this->sex)) {
			if (preg_match('/\n1 SEX ([MF])/', $this->gedrec, $match)) {
				$this->sex=$match[1];
			} else {
				$this->sex='U';
			}
		}
		return $this->sex;
	}

	/**
	* get the person's sex image
	* NOTE: It would have been nice if we'd called the images sexM, sexF and sexU
	* @return string  <img ... />
	*/
	function getSexImage($size='small', $style='', $title='') {
		return self::sexImage($this->getSex(), $size, $style, $title);
	}

	static function sexImage($sex, $size='small', $style='', $title='') {
		global $PGV_IMAGE_DIR, $PGV_IMAGES;
		switch ($sex) {
		case 'M':
			if (isset($PGV_IMAGES['sex'][$size])) {
				return "<img src=\"{$PGV_IMAGE_DIR}/{$PGV_IMAGES['sex'][$size]}\" class=\"gender_image\" style=\"{$style}\" alt=\"{$title}\" title=\"{$title}\" />";
			} else {
				return '<span style="size:'.$size.'">'.PGV_UTF8_MALE.'</span>';
			}
		case 'F':
			if (isset($PGV_IMAGES['sex'][$size])) {
				return "<img src=\"{$PGV_IMAGE_DIR}/{$PGV_IMAGES['sexf'][$size]}\" class=\"gender_image\" style=\"{$style}\" alt=\"{$title}\" title=\"{$title}\" />";
			} else {
				return '<span style="size:'.$size.'">'.PGV_UTF8_FEMALE.'</span>';
			}
		default:
			if (isset($PGV_IMAGES['sex'][$size])) {
				return "<img src=\"{$PGV_IMAGE_DIR}/{$PGV_IMAGES['sexn'][$size]}\" class=\"gender_image\" style=\"{$style}\" alt=\"{$title}\" title=\"{$title}\" />";
			} else {
				return '<span style="size:'.$size.'">?</span>';
			}
		}
	}

	/**
	* set a label for this person
	* The label can be used when building a list of people
	* to display the relationship between this person
	* and the person listed on the page
	* @param string $label
	*/
	function setLabel($label) {
		$this->label = $label;
	}
	/**
	* get the label for this person
	* The label can be used when building a list of people
	* to display the relationship between this person
	* and the person listed on the page
	* @param string $elderdate optional elder sibling birthdate to calculate gap
	* @param int $counter optional children counter
	* @return string
	*/
	function getLabel($elderdate="", $counter=0) {
		global $pgv_lang, $lang_short_cut, $LANGUAGE, $TEXT_DIRECTION;
		$label = "";
		$gap = 0;
		if (is_object($elderdate) && $elderdate->isOK()) {
			$p2 = $this->getBirthDate();
			if ($p2->isOK()) {
				$gap = $p2->MinJD()-$elderdate->MinJD(); // days
				$label .= "<div class=\"elderdate age $TEXT_DIRECTION\">";
				// warning if negative gap : wrong order
				if ($gap<0 && $counter>0) $label .= "<img alt=\"\" src=\"images/warning.gif\" /> ";
				// warning if gap<6 months
				if ($gap>1 && $gap<180 && $counter>0) $label .= "<img alt=\"\" src=\"images/warning.gif\" /> ";
				// children with same date means twin
				/**if ($gap==0 && $counter>1) {
					if ($this->getSex()=="M") $label .= $pgv_lang["twin_brother"];
					else if ($this->getSex()=="F") $label .= $pgv_lang["twin_sister"];
					else $label .= $pgv_lang["twin"];
					}**/
				// gap in years or months
				$gap = round($gap*12/365.25); // months

				// Allow special processing for different languages
				$func="date_diff_localisation_{$lang_short_cut[$LANGUAGE]}";
				if (!function_exists($func))
					$func="DefaultGetLabel";
				// Localise the age diff
				$func($label, $gap);
				$label .= "</div>";
			}
		}
		if ($counter) $label .= "<div class=\"".strrev($TEXT_DIRECTION)."\">".$pgv_lang["number_sign"].$counter."</div>";
		$label .= $this->label;
		if ($gap!=0 && $counter<1) $label .= "<br />&nbsp;";
		return $label;
	}
	/**
	* get family with spouse ids
	* @return array array of the FAMS ids
	*/
	function getSpouseFamilyIds() {
		if (is_null($this->fams)) {
			preg_match_all('/\n1 FAMS @('.PGV_REGEX_XREF.')@/', $this->gedrec, $match);
			$this->fams=$match[1];
		}
		return $this->fams;
	}
	/**
	* get the families with spouses
	* @return array array of Family objects
	*/
	function getSpouseFamilies() {
		global $pgv_lang, $SHOW_LIVING_NAMES;
		if (is_null($this->spouseFamilies)) {
			$this->spouseFamilies=array();
			foreach ($this->getSpouseFamilyIds() as $famid) {
				$family=Family::getInstance($famid);
				if (is_null($family)) {
					echo '<span class="warning">', $pgv_lang['unable_to_find_family'], ' ', $famid, '</span>';
				} else {
					// only include family if it is displayable by current user
					if ($SHOW_LIVING_NAMES || $family->canDisplayDetails()) {
						$this->spouseFamilies[$famid] = $family;
					}
				}
			}
		}
		return $this->spouseFamilies;
	}

	/**
	* get the current spouse of this person
	* The current spouse is defined as the spouse from the latest family.
	* The latest family is defined as the last family in the GEDCOM record
	* @return Person  this person's spouse
	*/
	function getCurrentSpouse() {
		$family = end($this->getSpouseFamilies());
		if ($family) {
			return $family->getSpouse($this);
		} else {
			return null;
		}
	}

	// Get a count of the children for this individual
	function getNumberOfChildren() {
		$nchi1=(int)get_gedcom_value('NCHI', 1, $this->gedrec);
		$nchi2=count(fetch_child_ids($this->xref, $this->ged_id));
		return max($nchi1, $nchi2);
	}
	/**
	* get family with child ids
	* @return array array of the FAMC ids
	*/
	function getChildFamilyIds() {
		if (is_null($this->famc)) {
			preg_match_all('/\n1 FAMC @('.PGV_REGEX_XREF.')@/', $this->gedrec, $match);
			$this->famc=$match[1];
		}
		return $this->famc;
	}
	/**
	* get an array of families with parents
	* @return array array of Family objects indexed by family id
	*/
	function getChildFamilies() {
		global $pgv_lang, $SHOW_LIVING_NAMES;

		if (is_null($this->childFamilies)) {
			$this->childFamilies=array();
			foreach ($this->getChildFamilyIds() as $famid) {
				$family=Family::getInstance($famid);
				if (is_null($family)) {
					echo '<span class="warning">', $pgv_lang['unable_to_find_family'], ' ', $famid, '</span>';
				} else {
					// only include family if it is displayable by current user
					if ($SHOW_LIVING_NAMES || $family->canDisplayDetails()) {
						$this->childFamilies[$famid]=$family;
					}
				}
			}
		}
		return $this->childFamilies;
	}
	/**
	* get primary family with parents
	* @return Family object
	*/
	function getPrimaryChildFamily() {
		$families=$this->getChildFamilies();
		switch (count($families)) {
		case 0:
			return null;
		case 1:
			return reset($families);
		default:
			// If there is more than one FAMC record, choose the preferred parents:
			// a) records with "2 _PRIMARY"
			foreach ($families as $famid=>$fam) {
				if (preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 _PRIMARY Y)/", $this->gedrec)) {
					return $fam;
				}
			}
			// b) records with "2 PEDI birt"
			foreach ($families as $famid=>$fam) {
				if (preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 PEDI birth)/", $this->gedrec)) {
					return $fam;
				}
			}
			// c) records with no "2 PEDI"
			foreach ($families as $famid=>$fam) {
				if (!preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 PEDI)/", $this->gedrec)) {
					return $fam;
				}
			}
			// d) any record
			return reset($families);
		}
	}
	/**
	* get family with child pedigree
	* @return string FAMC:PEDI value [ adopted | birth | foster | sealing ]
	*/
	function getChildFamilyPedigree($famid) {
		$subrec = get_sub_record(1, "1 FAMC @".$famid."@", $this->gedrec);
		$pedi = get_gedcom_value("PEDI", 2, $subrec, '', false);
		// birth=default => return an empty string
		return ($pedi=='birth') ? '' : $pedi;
	}
	/**
	* get the step families from the parents
	* @return array array of Family objects
	*/
	function getStepFamilies() {
		$families = array();
		$fams = $this->getChildFamilies();
		foreach ($fams as $family) {
			if (!is_null($family)) {
				$father = $family->getHusband();
				if (!is_null($father)) {
					$pfams = $father->getSpouseFamilies();
					foreach ($pfams as $key1=>$fam) {
						if (!is_null($fam) && !isset($fams[$key1]) && ($fam->getNumberOfChildren() > 0)) {
							$families[$key1] = $fam;
						}
					}
				}
				$mother = $family->getWife();
				if (!is_null($mother)) {
					$pfams = $mother->getSpouseFamilies();
					foreach ($pfams as $key1=>$fam) {
						if (!is_null($fam) && !isset($fams[$key1]) && ($fam->getNumberOfChildren() > 0)) {
							$families[$key1] = $fam;
						}
					}
				}
			}
		}
		return $families;
	}
	/**
	* get global facts
	* @return array
	*/
	function getGlobalFacts() {
		$this->parseFacts();
		return $this->globalfacts;
	}
	/**
	* get indi facts
	* @return array
	*/
	function getIndiFacts($nfacts=NULL) {
		$this->parseFacts($nfacts);
		return $this->indifacts;
	}

	/**
	* get other facts
	* @return array
	*/
	function getOtherFacts() {
		$this->parseFacts();
		return $this->otherfacts;
	}
	/**
	* get the correct label for a family
	* @param Family $family the family to get the label for
	* @return string
	*/
	function getChildFamilyLabel(&$family) {
		global $pgv_lang;
		if (!is_null($family)) {
			$famlink = get_sub_record(1, "1 FAMC @".$family->getXref()."@", $this->gedrec);
			$ft = preg_match("/2 PEDI (.*)/", $famlink, $fmatch);
			if ($ft>0) {
				$temp = $fmatch[1];
				if ($temp!="birth" && isset($pgv_lang[$temp])) return $pgv_lang[$temp]." ";
			}
		}
		return $pgv_lang["as_child"];
	}
	/**
	* get the correct label for a step family
	* @param Family $family the family to get the label for
	* @return string
	*/
	function getStepFamilyLabel(&$family) {
		global $pgv_lang;
		$label = "Unknown Family";
		if (is_null($family)) return $label;
		$childfams = $this->getChildFamilies();
		$mother = $family->getWife();
		$father = $family->getHusband();
		foreach ($childfams as $fam) {
			if (!$fam->equals($family)) {
				$wife = $fam->getWife();
				$husb = $fam->getHusband();
				if ((is_null($husb) || !$husb->equals($father)) && (is_null($wife)||$wife->equals($mother))) {
					if ($mother->getSex()=="M") $label = $pgv_lang["fathers_family_with"];
					else $label = $pgv_lang["mothers_family_with"];
					if (!is_null($father)) $label .= $father->getFullName();
					else $label .= $pgv_lang["unknown"];
				}
				else if ((is_null($wife) || !$wife->equals($mother)) && (is_null($husb)||$husb->equals($father))) {
					if ($father->getSex()=="F") $label = $pgv_lang["mothers_family_with"];
					else $label = $pgv_lang["fathers_family_with"];
					if (!is_null($mother)) $label .= $mother->getFullName();
					else $label .= $pgv_lang["unknown"];
				}
				if ($label!="Unknown Family") return $label;
			}
		}
		return $label;
	}
	/**
	* get the correct label for a family
	* @param Family $family the family to get the label for
	* @return string
	*/
	function getSpouseFamilyLabel(&$family) {
		global $factarray, $pgv_lang;

		$label = $pgv_lang["family_with"] . " ";
		if (is_null($family)) return $label . $pgv_lang["unknown"];
		$famlink = get_sub_record(1, "1 FAMS @".$family->getXref()."@", $this->gedrec);
		$ft = preg_match("/2 PEDI (.*)/", $famlink, $fmatch);
		if ($ft>0) {
			$temp = $fmatch[1];
			if ($temp=="birth") $label = $factarray["BIRT"]." ";
			else if (isset($pgv_lang[$temp])) $label = $pgv_lang[$temp]." ";
		}
		$husb = $family->getHusband();
		$wife = $family->getWife();
		if ($this->equals($husb)) {
			if (!is_null($wife)) $label .= $wife->getFullName();
			else $label .= $pgv_lang["unknown"];
		}
		else {
			if (!is_null($husb)) $label .= $husb->getFullName();
			else $label .= $pgv_lang["unknown"];
		}
		return $label;
	}
	/**
	* get updated Person
	* If there is an updated individual record in the gedcom file
	* return a new person object for it
	* @return Person
	*/
	function &getUpdatedPerson() {
		global $GEDCOM, $pgv_changes;
		if ($this->getChanged()) {
			return null;
		}
		if (PGV_USER_CAN_EDIT && $this->canDisplayDetails()) {
			if (isset($pgv_changes[$this->xref."_".$GEDCOM])) {
				$newrec = find_updated_record($this->xref);
				if (!empty($newrec)) {
					$new = new Person($newrec);
					$new->setChanged(true);
					return $new;
				}
			}
		}
		return null;
	}
	/**
	* Parse the facts from the individual record
	*/
	function parseFacts($nfacts=NULL) {
		global $nonfacts;
		parent::parseFacts();
		if ($nfacts!=NULL) $nonfacts = $nfacts;
		//-- only run this function once
		if ($this->facts_parsed) return;
		//-- don't run this function if privacy does not allow viewing of details
		if (!$this->canDisplayDetails()) return;
		$sexfound = false;
		//-- run the parseFacts() method from the parent class
		$this->facts_parsed = true;

		//-- sort the fact info into different categories for people
		foreach ($this->facts as $f=>$event) {
			$fact = $event->getTag();
			// -- handle special name fact case
			if ($fact=="NAME") {
				$this->globalfacts[] = $event;
			}
			// -- handle special source fact case
			else if ($fact=="SOUR") {
				$this->otherfacts[] = $event;
			}
			// -- handle special note fact case
			else if ($fact=="NOTE") {
				$this->otherfacts[] = $event;
			}
			// -- handle special sex case
			else if ($fact=="SEX") {
				$this->globalfacts[] = $event;
				$sexfound = true;
			}
			else if ($fact=="OBJE") {}
			else if (!isset($nonfacts) || !in_array($fact, $nonfacts)) {
				$this->indifacts[] = $event;
			}
		}
		//-- add a new sex fact if one was not found
		if (!$sexfound) {
			$this->globalfacts[] = new Event("1 SEX U", 'new');
		}
	}
	/**
	* add facts from the family record
	* @param boolean $otherfacts whether or not to add other related facts such as parents facts, associated people facts, and historical facts
	*/
	function add_family_facts($otherfacts = true) {
		global $GEDCOM, $nonfacts, $nonfamfacts;

		if (!isset($nonfacts)) $nonfacts = array();
		if (!isset($nonfamfacts)) $nonfamfacts = array();

		if (!$this->canDisplayDetails()) return;
		$this->parseFacts();
		//-- Get the facts from the family with spouse (FAMS)
		$fams = $this->getSpouseFamilies();
		/* @var $family Family */
		foreach ($fams as $famid=>$family) {
			if (is_null($family)) continue;
			$updfamily = $family->getUpdatedFamily(); //-- updated family ?
			$spouse = $family->getSpouse($this);

			if ($updfamily) {
				$family->diffMerge($updfamily);
			}
			$facts = $family->getFacts();
			$hasdiv = false;
			/* @var $event Event */
			foreach ($facts as $event) {
				$fact = $event->getTag();
				if ($fact=="DIV") $hasdiv = true;
				// -- handle special source fact case
				if (($fact!="SOUR") && ($fact!="NOTE") && ($fact!="CHAN") && ($fact!="_UID") && ($fact!="RIN")) {
					if ((!in_array($fact, $nonfacts))&&(!in_array($fact, $nonfamfacts))) {
						$factrec = $event->getGedcomRecord();
						if (!is_null($spouse)) $factrec.="\n2 _PGVS @".$spouse->getXref()."@";
						$factrec.="\n2 _PGVFS @$famid@\n";
						$event->gedcomRecord = $factrec;
						if ($fact!="OBJE") $this->indifacts[] = $event;
						else $this->otherfacts[]=$event;
					}
				}
			}
			if($otherfacts){
				if (!$hasdiv && !is_null($spouse)) $this->add_spouse_facts($spouse, $family->getGedcomRecord());
				$this->add_children_facts($family);
			}
		}
		if($otherfacts){
			$this->add_parents_facts($this);
			$this->add_historical_facts();
			$this->add_asso_facts();
		}
	}
	/**
	* add parents events to individual facts array
	*
	* bdate = indi birth date record
	* ddate = indi death date record
	*
	* @param Person $person Person
	* @param int $sosa 2=father 3=mother ...
	* @return records added to indifacts array
	*/
	function add_parents_facts(&$person, $sosa=1) {
		global $SHOW_RELATIVES_EVENTS, $factarray;

		if (is_null($person)) return;
		if (!$SHOW_RELATIVES_EVENTS) return;
		if ($sosa>7) return; // sosa max for recursive call

		$fams = $person->getChildFamilies();
		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();

		//-- find family as child
		/* @var $family Family */
		foreach ($fams as $famid=>$family) {
			foreach (array($family->getWife(), $family->getHusband()) as $parent) {
				if ($parent) {
					// add parent death
					if ($sosa==1) {
						$fact=$parent->getSex()=='F' ? '_DEAT_MOTH' : '_DEAT_FATH';
					} elseif ($sosa<4) {
						$fact="_DEAT_GPAR";
					} else {
						$fact="_DEAT_GGPA";
					}
					if ($parent && strstr($SHOW_RELATIVES_EVENTS, $fact)) {
						foreach ($parent->getAllFactsByType(explode('|', PGV_EVENTS_DEAT)) as $sEvent) {
							$srec = $sEvent->getGedcomRecord();
							if (GedcomDate::Compare($bDate, $sEvent->getDate())<0 && GedcomDate::Compare($sEvent->getDate(), $dDate)<=0) {
								$fact=str_replace('DEAT', $sEvent->getTag(), $fact); // BURI, CREM, etc.
								$factrec="1 ".$fact."\n".get_sub_record(2, '2 DATE', $srec)."\n".get_sub_record(2, '2 PLAC', $srec);
								if (!$sEvent->canShow()) {
									$factrec .= "\n2 RESN privacy";
								}
								if ($parent->getSex()=='M')
									$factrec.="\n2 ASSO @".$parent->getXref()."@\n3 RELA *sosa_".($sosa*2);
								else if ($parent->getSex()=='F')
									$factrec.="\n2 ASSO @".$parent->getXref()."@\n3 RELA *sosa_".($sosa*2+1);
								$factrec.="\n".get_sub_record(2, '2 ASSO @'.$this->xref.'@', $srec);
								$event=new Event($factrec, 0);
								$event->setParentObject($this);
								$this->indifacts[] = $event;
							}
						}
					}
					if ($sosa==1) $this->add_stepsiblings_facts($parent, $famid); // stepsiblings with father
					if ($parent->getSex()=='M') {
						$this->add_parents_facts($parent, $sosa*2); // recursive call for father ancestors
					}
					else if ($parent->getSex()=='F') {
						$this->add_parents_facts($parent, $sosa*2+1); // recursive call for mother ancestors
					}
				}
			}
			if ($sosa>3) return;
			// add father/mother marriages
			$parents[] = $family->getHusband();
			$parents[] = $family->getWife();
			foreach ($parents as $indexval=>$parent) {
				if (is_null($parent)) continue;
				if ($sosa==1) {
					if ($parent->getSex()=='M') {
						$fact="_MARR_FATH";
						$rela="father";
					} else {
						$fact="_MARR_MOTH";
						$rela="mother";
					}
				} else {
					// Not currently used.  Do we want separate grandmother/grandfather events?
					$fact="_MARR_GPAR";
					$rela="grandparent";
				}
				if (strstr($SHOW_RELATIVES_EVENTS, $fact)) {
					$sfamids = $parent->getSpouseFamilies();
					/* @var $sfamily Family */
					foreach ($sfamids as $sfamid=>$sfamily) {
						if ($sfamid==$famid && $rela=="mother") continue; // show current family marriage only for father
						$sEvent = $sfamily->getMarriage();
						$srec = $sEvent->getGedcomRecord();
						if (GedcomDate::Compare($bDate, $sEvent->getDate())<0 && GedcomDate::Compare($sEvent->getDate(), $dDate)<=0) {
							if ($sfamid==$famid) $fact="_MARR_FAMC";
							$factrec = "1 ".$fact;
							$factrec.="\n".get_sub_record(2, '2 DATE', $srec)."\n".get_sub_record(2, '2 PLAC', $srec);
							if (!$sEvent->canShow()) $factrec .= "\n2 RESN privacy";
							$factrec .= "\n2 ASSO @".$parent->getXref()."@";
							$factrec .= "\n3 RELA *".$rela;
							if ($rela=="father") $rela2="stepmom";
							else $rela2="stepdad";
							if ($sfamid==$famid) $rela2="mother";
							$factrec .= "\n2 ASSO @".$sfamily->getSpouseId($parent->getXref())."@";
							$factrec .= "\n3 RELA *".$rela2;
							if ($sfamily->getHusbId() && $sfamily->getWifeId()) {
								$ct = preg_match('/^0 @('.PGV_REGEX_XREF.')@/', $sfamily->getGedcomRecord(), $match);
								if ($ct>0) $factrec .= "\n2 ASSO @".$match[1]."@";
							}
							//$this->indifacts[]=array(0, $factrec);
							$event = new Event($factrec, 0);
							$event->setParentObject($this);
							$this->indifacts[] = $event;
						}
					}
				}
			}
			//-- find siblings
			$this->add_children_facts($family, $sosa, $person->getXref());
		}
	}
	/**
	* add children events to individual facts array
	*
	* bdate = indi birth date record
	* ddate = indi death date record
	*
	* @param string $family Family object
	* @param string $option Family level indicator
	* @param string $except Gedcom childid already processed
	* @return records added to indifacts array
	*/
	function add_children_facts(&$family, $option="_CHIL", $except="") {
		global $SHOW_RELATIVES_EVENTS, $factarray;

		if ($option=="1") $option="_SIBL";
		if ($option=="2") $option="_FSIB";
		if ($option=="3") $option="_MSIB";
		if (strstr($SHOW_RELATIVES_EVENTS, $option)===false) return;

		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();

		$children = $family->getChildren();
		foreach ($children as $key=>$child) {
			$spid = $child->getXref();
			if ($spid!=$except) {
				$childrec =$child->getGedcomRecord();
				$sex = $child->getSex();
				// children
				$rela="child";
				if ($sex=="F") $rela="daughter";
				if ($sex=="M") $rela="son";
				// grandchildren
				if ($option=="_GCHI") {
					$rela="grandchild";
					if ($sex=="F") $rela="granddaughter";
					if ($sex=="M") $rela="grandson";
				}
				// great-grandchildren
				if ($option=="_GGCH") {
					$rela="greatgrandchild";
					if ($sex=="F") $rela="greatgranddaughter";
					if ($sex=="M") $rela="greatgrandson";
				}
				// stepsiblings
				if ($option=="_HSIB") {
					$rela="halfsibling";
					if ($sex=="F") $rela="halfsister";
					if ($sex=="M") $rela="halfbrother";
				}
				// siblings
				if ($option=="_SIBL") {
					$rela="sibling";
					if ($sex=="F") $rela="sister";
					if ($sex=="M") $rela="brother";
				}
				// uncles/aunts
				if ($option=="_FSIB" or $option=="_MSIB") {
					$rela="uncle/aunt";
					if ($sex=="F") $rela="aunt";
					if ($sex=="M") $rela="uncle";
				}
				// firstcousins
				if ($option=="_COUS") {
					$rela="firstcousin";
					if ($sex=="F") $rela="femalecousin";
					if ($sex=="M") $rela="malecousin";
				}
				// nephew/niece
				if ($option=="_NEPH") {
					$rela="nephew/niece";
					if ($sex=="F") $rela="niece";
					if ($sex=="M") $rela="nephew";
				}
				// add child birth
				if (strstr($SHOW_RELATIVES_EVENTS, '_BIRT'.$option)) {
					/* @var $child Person */
					/* @var $sEvent Event */
					foreach ($child->getAllFactsByType(explode('|', PGV_EVENTS_BIRT)) as $sEvent) {
						$srec = $sEvent->getGedcomRecord();
						$sgdate=$sEvent->getDate();
						if ($option=='_CHIL' || $sgdate->isOK() && GedcomDate::Compare($this->getEstimatedBirthDate(), $sgdate)<=0 && GedcomDate::Compare($sgdate, $this->getEstimatedDeathDate())<=0) {
							$factrec='1 _'.$sEvent->getTag().$option;
							$factrec.="\n".get_sub_record(2, '2 DATE', $srec)."\n".get_sub_record(2, '2 PLAC', $srec);
							if (!$sEvent->canShow()) {
								$factrec.='\n2 RESN privacy';
							}
							if (strstr($srec, "twin")) {
								$rela_sex = Person::getInstance($spid)->getSex();
								$rela="twin";
								if ($rela_sex=="F") $rela="twin_sister";
								else if ($rela_sex=="M") $rela="twin_brother";
							}
							$factrec.="\n2 ASSO @".$spid."@\n3 RELA *".$rela;

							// add parents on grandchildren, cousin or nephew's birth
							if ($option=='_GCHI' || $option=='_GGCH' || $option=='_COUS' || $option=='_NEPH') {
								if ($family->getHusbId()) {
									$factrec.="\n2 ASSO @".$family->getHusbId()."@\n3 RELA *father";
								}
								if ($family->getWifeId()) {
									$factrec.="\n2 ASSO @".$family->getWifeId()."@\n3 RELA *mother";
								}
								if ($family->getHusbId() && $family->getWifeId()) {
									$ct = preg_match('/^0 @('.PGV_REGEX_XREF.')@/', $family->getGedcomRecord(), $match);
									if ($ct>0) $factrec .= "\n2 ASSO @".$match[1]."@";
								}
							}
							$factrec.="\n".get_sub_record(2, '2 ASSO @'.$this->xref.'@', $srec);
							$event = new Event($factrec, 0);
							$event->setParentObject($this);
							$this->indifacts[]=$event;
							break;
						}
					}
				}
				// add child death
				if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT'.$option)) {
					/* @var $sEvent Event */
					foreach ($child->getAllFactsByType(explode('|', PGV_EVENTS_DEAT)) as $sEvent) {
						$sgdate=$sEvent->getDate();
						$srec = $sEvent->getGedcomRecord();
						if ($sgdate->isOK() && GedcomDate::Compare($this->getEstimatedBirthDate(), $sgdate)<=0 && GedcomDate::Compare($sgdate, $this->getEstimatedDeathDate())<=0) {
							$factrec='1 _'.$sEvent->getTag().$option;
							$factrec.="\n".get_sub_record(2, '2 DATE', $srec)."\n".get_sub_record(2, '2 PLAC', $srec);
							if (!$sEvent->canShow()) {
								$factrec.='\n2 RESN privacy';
							}
							$factrec.="\n2 ASSO @".$spid."@\n3 RELA *".$rela;
							$factrec.="\n".get_sub_record(2, '2 ASSO @'.$this->xref.'@', $srec);
							$event = new Event($factrec, 0);
							$event->setParentObject($this);
							$this->indifacts[] = $event;
						}
					}
				}
				// add child marriage
				if (strstr($SHOW_RELATIVES_EVENTS, '_MARR'.$option)) {
					foreach ($child->getSpouseFamilies() as $sfamid=>$sfamily) {
						$sEvent = $sfamily->getMarriage();
						$sgdate=$sEvent->getDate();
						$srec = $sEvent->getGedcomRecord();
						if ($sgdate->isOK() && GedcomDate::Compare($this->getEstimatedBirthDate(), $sgdate)<=0 && GedcomDate::Compare($sgdate, $this->getEstimatedDeathDate())<=0) {
							$factrec='1 _'.$sEvent->getTag().$option;
							$factrec.="\n".get_sub_record(2, '2 DATE', $srec)."\n".get_sub_record(2, '2 PLAC', $srec);
							if (!$sEvent->canShow()) {
								$factrec.='\n2 RESN privacy';
							}
							$factrec.="\n2 ASSO @".$spid."@\n3 RELA *".$rela;
							if ($rela=='son') $rela2='daughter_in_law';
							else if ($rela=='daughter') $rela2='son_in_law';
							else if ($rela=='brother' || $rela=='halfbrother') $rela2='sister_in_law';
							else if ($rela=='sister' || $rela=='halfsister') $rela2='brother_in_law';
							else if ($rela=='uncle') $rela2='aunt_in_law';
							else if ($rela=='aunt') $rela2='uncle_in_law';
							else if ($rela=='malecousin') $rela2='f_cousin_in_law';
							else if ($rela=='femalecousin') $rela2='m_cousin_in_law';
							else $rela2='spouse';
							$factrec.="\n2 ASSO @".$sfamily->getSpouseId($spid)."@\n3 RELA *".$rela2;
							$factrec.="\n".get_sub_record(2, "2 ASSO @".$this->xref."@", $srec);
							if ($sfamily->getHusbId() && $sfamily->getWifeId()) {
								$ct = preg_match('/^0 @('.PGV_REGEX_XREF.')@/', $sfamily->getGedcomRecord(), $match);
								if ($ct>0) $factrec .= "\n2 ASSO @".$match[1]."@";
							}
							$event = new Event($factrec, 0);
							$event->setParentObject($this);
							$this->indifacts[] = $event;
						}
					}
				}

				// add children of children = grandchildren
				if ($option=="_CHIL") {
					foreach ($child->getSpouseFamilies() as $sfamid=>$sfamily) {
						$this->add_children_facts($sfamily, "_GCHI");
					}
				}
				// add children of grandchildren = great-grandchildren
				if ($option=="_GCHI") {
					foreach ($child->getSpouseFamilies() as $sfamid=>$sfamily) {
						$this->add_children_facts($sfamily, "_GGCH");
					}
				}
				// add children of siblings = nephew/niece
				if ($option=="_SIBL") {
					foreach ($child->getSpouseFamilies() as $sfamid=>$sfamily) {
						$this->add_children_facts($sfamily, "_NEPH");
					}
				}
				// add children of uncle/aunt = firstcousins
				if ($option=="_FSIB" or $option=="_MSIB") {
					foreach ($child->getSpouseFamilies() as $sfamid=>$sfamily) {
						$this->add_children_facts($sfamily, "_COUS");
					}
				}
			}
		}
	}
	/**
	* add spouse events to individual facts array
	*
	* bdate = indi birth date record
	* ddate = indi death date record
	*
	* @param string $spouse Person object
	* @param string $famrec family Gedcom record
	* @return records added to indifacts array
	*/
	function add_spouse_facts(&$spouse, $famrec="") {
		global $SHOW_RELATIVES_EVENTS, $factarray;

		// do not show if divorced
		if (preg_match('/\n1 (?:'.PGV_EVENTS_DIV.')\b/', $famrec)) {
			return;
		}
		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();

		// add spouse death
		if ($spouse && strstr($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU')) {
			foreach ($spouse->getAllFactsByType(explode('|', PGV_EVENTS_DEAT)) as $sEvent) {
				$sdate=$sEvent->getDate();
				$srec = $sEvent->getGedcomRecord();
				if ($sdate->isOK() && GedcomDate::Compare($this->getEstimatedBirthDate(), $sdate)<=0 && GedcomDate::Compare($sdate, $this->getEstimatedDeathDate())<=0) {
					$srec=preg_replace('/^1 .*/', "1 _".$sEvent->getTag()."_SPOU ", $srec);
					$srec.="\n".get_sub_record(2, '2 ASSO @'.$this->xref.'@', $srec);
					$srec.="\n2 ASSO @".$spouse->getXref()."@\n3 RELA *spouse";
					$event = new Event($srec, 0);
					$event->setParentObject($this);
					$this->indifacts[] = $event;
				}
			}
		}
	}
	/**
	* add step-siblings events to individual facts array
	*
	* @param Person $spouse Father or mother Gedcom id
	* @param string $except Gedcom famid already processed
	* @return records added to indifacts array
	*/
	function add_stepsiblings_facts(&$spouse, $except="") {
		if (is_null($spouse)) return;
		foreach ($spouse->getSpouseFamilies() as $famid=>$family) {
			// process children from all step families
			if ($famid!=$except) $this->add_children_facts($family, "step");
		}
	}
	/**
	* add historical events to individual facts array
	*
	* @return records added to indifacts array
	*
	* Historical facts are imported from optional language file : histo.xx.php
	* where xx is language code
	* This file should contain records similar to :
	*
	* $histo[]="1 EVEN\n2 TYPE History\n2 DATE 11 NOV 1918\n2 NOTE WW1 Armistice";
	* $histo[]="1 EVEN\n2 TYPE History\n2 DATE 8 MAY 1945\n2 NOTE WW2 Armistice";
	* etc...
	*
	*/
	function add_historical_facts() {
		global $LANGUAGE, $lang_short_cut;
		global $SHOW_RELATIVES_EVENTS;
		if (!$SHOW_RELATIVES_EVENTS) return;

		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();
		if (!$bDate->isOK()) return;

		if ($SHOW_RELATIVES_EVENTS && file_exists('languages/histo.'.$lang_short_cut[$LANGUAGE].'.php')) {
			include('languages/histo.'.$lang_short_cut[$LANGUAGE].'.php');
			foreach ($histo as $indexval=>$hrec) {
				$sdate=new GedcomDate(get_gedcom_value('DATE', 2, $hrec, '', false));
				if ($sdate->isOK() && GedcomDate::Compare($this->getEstimatedBirthDate(), $sdate)<=0 && GedcomDate::Compare($sdate, $this->getEstimatedDeathDate())<=0) {
					$event = new Event($hrec);
					$event->setParentObject($this);
					$this->indifacts[] = $event;
				}
			}
		}
	}
	/**
	* add events where pid is an ASSOciate
	*
	* @return records added to indifacts array
	*
	*/
	function add_asso_facts() {
		global $factarray, $pgv_lang;

		$associates=array_merge(
			fetch_linked_indi($this->getXref(), 'ASSO', $this->ged_id),
			fetch_linked_fam ($this->getXref(), 'ASSO', $this->ged_id)
		);
		foreach ($associates as $associate) {
			foreach ($associate->getFacts() as $event) {
				$srec = $event->getGedcomRecord();
				$arec = get_sub_record(2, "2 ASSO @".$this->getXref()."@", $srec);
				if ($arec) {
					$fact = $event->getTag();
					$label = $event->getLabel();
					$sdate = get_sub_record(2, "2 DATE", $srec);
					// relationship ?
					$rrec = get_sub_record(3, "3 RELA", $arec);
					$rela = trim(substr($rrec, 7));
					if (empty($rela)) $rela = "ASSO";
					// add an event record
					$factrec = "1 EVEN\n2 TYPE ".$label."<br/>[ <span class=\"details_label\">";
					if (isset($pgv_lang[strtolower($rela)])) $factrec .= $pgv_lang[strtolower($rela)]."</span> ]";
					else if (isset($factarray[$rela])) $factrec .= $factarray[$rela]."</span> ]";
					$factrec.="\n".$sdate."\n".get_sub_record(2, '2 PLAC', $srec);
					if (!$event->canShow()) $factrec .= "\n2 RESN privacy";
					if ($associate->getType()=='FAM') {
						$famrec = find_family_record($associate->getXref());
						if ($famrec) {
							$parents = find_parents_in_record($famrec);
							if ($parents["HUSB"]) $factrec .= "\n2 ASSO @".$parents["HUSB"]."@"; //\n3 RELA ".$factarray[$fact];
							if ($parents["WIFE"]) $factrec .= "\n2 ASSO @".$parents["WIFE"]."@"; //\n3 RELA ".$factarray[$fact];
						}
					}
					else if ($fact=='BIRT') {
						$sex = $associate->getSex();
						if ($sex == "M") $rela_b="twin_brother";
						else if ($sex == "F") $rela_b="twin_sister";
						else $rela_b="twin";
						$factrec .= "\n2 ASSO @".$associate->getXref()."@\n3 RELA ".$rela_b;
					}
					else if ($fact=='CHR') {
						$sex = $associate->getSex();
						if ($sex == "M") $rela_chr="godson";
						else if ($sex == "F") $rela_chr="goddaughter";
						else $rela_chr="godchild";
						$factrec .= "\n2 ASSO @".$associate->getXref()."@\n3 RELA ".$rela_chr;
					}
					else $factrec .= "\n2 ASSO @".$associate->getXref()."@\n3 RELA ".$fact;
					//$factrec .= "\n3 NOTE ".$rela;
					$factrec .= "\n2 ASSO @".$this->getXref()."@\n3 RELA *".$rela;
					// check if this fact already exists in the list
					$found = false;
					if ($sdate) foreach ($this->indifacts as $k=>$v) {
						if (strpos($v->getGedcomRecord(), $sdate)
						&& strpos($v->getGedcomRecord(), "2 ASSO @".$this->getXref()."@")) {
							$found = true;
							break;
						}
					}
					if (!$found) $this->indifacts[] = new Event($factrec, 0);
				}
			}
		}
	}

	/**
	* Merge the facts from another Person object into this object
	* for generating a diff view
	* @param Person $diff the person to compare facts with
	*/
	function diffMerge(&$diff) {
		if (is_null($diff)) return;
		$this->parseFacts();
		$diff->parseFacts();
		//-- loop through new facts and add them to the list if they are any changes
		//-- compare new and old facts of the Personal Fact and Details tab 1
		for ($i=0; $i<count($this->indifacts); $i++) {
			$found=false;
			$oldfactrec = $this->indifacts[$i]->getGedcomRecord();
			foreach ($diff->indifacts as $newfact) {
				$newfactrec = $newfact->getGedcomRecord();
				//-- remove all whitespace for comparison
				$tnf = preg_replace("/\s+/", " ", $newfactrec);
				$tif = preg_replace("/\s+/", " ", $oldfactrec);
				if ($tnf==$tif) {
					$this->indifacts[$i] = $newfact; //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			//-- fact was deleted?
			if (!$found) {
				$this->indifacts[$i]->gedcomRecord.="\nPGV_OLD\n";
			}
		}
		//-- check for any new facts being added
		foreach ($diff->indifacts as $newfact) {
			$found=false;
			foreach ($this->indifacts as $fact) {
				$tif = preg_replace("/\s+/", " ", $fact->getGedcomRecord());
				$tnf = preg_replace("/\s+/", " ", $newfact->getGedcomRecord());
				if ($tif==$tnf) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact->gedcomRecord.="\nPGV_NEW\n";
				$this->indifacts[]=$newfact;
			}
		}
		//-- compare new and old facts of the Notes Sources and Media tab 2
		for ($i=0; $i<count($this->otherfacts); $i++) {
			$found=false;
			foreach ($diff->otherfacts as $newfact) {
				if (trim($newfact->getGedcomRecord())==trim($this->otherfacts[$i]->getGedcomRecord())) {
					$this->otherfacts[$i] = $newfact; //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->otherfacts[$i]->gedcomRecord.="\nPGV_OLD\n";
			}
		}
		foreach ($diff->otherfacts as $indexval => $newfact) {
			$found=false;
			foreach ($this->otherfacts as $indexval => $fact) {
				if (trim($fact->getGedcomRecord())==trim($newfact->getGedcomRecord())) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact->gedcomRecord.="\nPGV_NEW\n";
				$this->otherfacts[]=$newfact;
			}
		}

		//-- compare new and old facts of the Global facts
		for ($i=0; $i<count($this->globalfacts); $i++) {
			$found=false;
			foreach ($diff->globalfacts as $indexval => $newfact) {
				if (trim($newfact->getGedcomRecord())==trim($this->globalfacts[$i]->getGedcomRecord())) {
					$this->globalfacts[$i] = $newfact; //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->globalfacts[$i]->gedcomRecord.="\nPGV_OLD\n";
			}
		}
		foreach ($diff->globalfacts as $indexval => $newfact) {
			$found=false;
			foreach ($this->globalfacts as $indexval => $fact) {
				if (trim($fact->getGedcomRecord())==trim($newfact->getGedcomRecord())) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact->gedcomRecord.="\nPGV_NEW\n";
				$this->globalfacts[]=$newfact;
			}
		}
		$newfamids = $diff->getChildFamilyIds();
		if (is_null($this->famc)) $this->getChildFamilyIds();
		foreach ($newfamids as $id) {
			if (!in_array($id, $this->famc)) $this->famc[]=$id;
		}

		$newfamids = $diff->getSpouseFamilyIds();
		if (is_null($this->fams)) $this->getSpouseFamilyIds();
		foreach ($newfamids as $id) {
			if (!in_array($id, $this->fams)) $this->fams[]=$id;
		}
	}

	/**
	* get primary parents names for this person
	* @param string $classname optional css class
	* @param string $display optional css style display
	* @return string a div block with father & mother names
	*/
	function getPrimaryParentsNames($classname="", $display="") {
		global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES;
		$fam = $this->getPrimaryChildFamily();
		if (!$fam) return "";
		$txt = "<div";
		if ($classname) $txt .= " class=\"$classname\"";
		if ($display) $txt .= " style=\"display:$display\"";
		$txt .= ">";
		$husb = $fam->getHusband();
		if ($husb) $txt .= $pgv_lang["father"].": ".PrintReady($husb->getListName())."<br />";
		$wife = $fam->getWife();
		if ($wife) $txt .= $pgv_lang["mother"].": ".PrintReady($wife->getListName());
		$txt .= "</div>";
		return $txt;
	}

	/**
	* get the URL to link to this person
	* @string a url that can be used to link to this person
	*/
	public function getLinkUrl() {
		return parent::_getLinkUrl('individual.php?pid=');
	}

	// If this object has no name, what do we call it?
	function getFallBackName() {
		return '@P.N. /@N.N./';
	}

	// Convert a name record into "full", "sort" and "list" versions.
	// Use the NAME field to generate the "full" and "list" versions, as the
	// gedcom spec says that this is the person's name, as they would write it.
	// Use the SURN field to generate the sortable names.  Note that this field
	// may also be used for the "true" surname, perhaps spelt differently to that
	// recorded in the NAME field. e.g.
	//
	// 1 NAME Robert /de Gliderow/
	// 2 GIVN Robert
	// 2 SPFX de
	// 2 SURN CLITHEROW
	// 2 NICK The Bald
	//
	// full=>'Robert de Gliderow "The Bald"'
	// list=>'de Gliderow, Robert "The Bald"'
	// sort=>'CLITHEROW, ROBERT'
	//
	// Handle multiple surnames, either as;
	// 1 NAME Carlos /Vasquez/ y /Sante/
	// or
	// 1 NAME Carlos /Vasquez y Sante/
	// 2 GIVN Carlos
	// 2 SURN Vasquez,Sante
	function _addName($type, $full, $gedrec) {
		global $UNDERLINE_NAME_QUOTES, $NAME_REVERSE, $unknownNN, $unknownPN, $pgv_lang;

		// Look for GIVN/SURN at level n+1
		$sublevel=1+(int)$gedrec[0];

		// Fix bad slashes.  e.g. "John/Smith" => "John/Smith/"
		if (substr_count($full, '/')%2==1) {
			$full.='/';
		}

		// Need the GIVN and SURN to generate the sortable name.
		$givn=preg_match("/\n{$sublevel} GIVN (.+)/", $gedrec, $match) ? $match[1] : '';
		$surn=preg_match("/\n{$sublevel} SURN (.+)/", $gedrec, $match) ? $match[1] : '';
		$spfx=preg_match("/\n{$sublevel} SPFX (.+)/", $gedrec, $match) ? $match[1] : '';
		if ($givn || $surn) {
			// An empty surname won't have a SURN field
			if (strpos($full, '//')) {
				$surn='@N.N.';
			}
			// GIVN and SURN can be comma-separated lists.
			$surns=preg_split('/ *, */', $surn);
			$givn=str_replace(array(', ', ','), ' ', $givn);
			// SPFX+SURN for lists
			$surn=($spfx?$spfx.' ':'').$surn;
		} else {
			$name=$full;
			// We do not have a structured name - extract the GIVN and SURN(s) ourselves
			// Strip the NPFX
			if (preg_match('/^(?:(?:(?:ADM|AMB|BRIG|CAN|CAPT|CHAN|CHAPLN|CMDR|COL|CPL|CPT|DR|GEN|GOV|HON|LADY|LORD|LT|MR|MRS|MS|MSGR|PFC|PRES|PROF|PVT|RABBI|REP|REV|SEN|SGT|SIR|SR|SRA|SRTA|VEN)\.? +)+)(.+)/i', $name, $match)) {
				$name=$match[1];
			}
			// Strip the NSFX
			if (preg_match('/(.+)(?:(?: +(?:ESQ|ESQUIRE|JR|JUNIOR|SR|SENIOR|[IVX]+)\.?)+)$/i', $name, $match)) {
				$name=$match[1];
			}
			// Extract GIVN/SURN.
			if (strpos($full, '/')===false) {
				$givn=trim($name);
				$spfx='';
				$surns=array('');
			} else {
				// Extract SURN.  Split at "/".  Odd numbered parts are SURNs.
				$spfx='';
				$surns=array();
				foreach (preg_split(': */ *:', $name) as $key=>$value) {
					if ($key%2==1) {
						if ($value) {
							// Strip SPFX
							if (preg_match('/^((?:(?:A|AAN|AB|AF|AL|AP|AS|AUF|AV|BAT|BIJ|BIN|BINT|DA|DE|DEL|DELLA|DEM|DEN|DER|DI|DU|EL|FITZ|HET|IBN|LA|LAS|LE|LES|LOS|ONDER|OP|OVER|\'S|ST|\'T|TE|TEN|TER|TILL|TOT|UIT|UIJT|VAN|VANDEN|VON|VOOR|VOR)[ -]+)+(?:[DL]\')?)(.+)$/i', $value, $match)) {
								$spfx=trim($match[1]);
								$value=$match[2];
							}
							$surns[]=$value ? $value : '@N.N.';
						} else {
							$surns[]='@N.N.';
						}
					}
				}
				// SPFX+SURN for lists
				$surn=($spfx ? $spfx.' ' : '').implode(' ', $surns);
				// Extract the GIVN.  Before first "/" and after last.
				$pos1=strpos($name, '/');
				if ($pos1===false) {
					$givn=$name;
				} else {
					$pos2=strrpos($name, '/');
					$givn=trim(substr($name, 0, $pos1).' '.substr($name, $pos2+1));
				}
			}
		}

		// Tidy up whitespace
		$full=preg_replace('/  +/', ' ', trim($full));

		// Add placeholder for unknown surname
		if (preg_match(':/ */:', $full)) {
			$full=preg_replace(':/ */:', '/@N.N./', $full);
		}

		// Add placeholder for unknown given name
		if (!$givn) {
			$givn='@P.N.';
			$pos=strpos($full, '/');
			$full=substr($full, 0, $pos).'@P.N. '.substr($full, $pos);
		}

		// Some systems don't include the NPFX in the NAME record.
		$npfx=preg_match('/^'.$sublevel.' NPFX (.+)/m', $gedrec, $match) ? $match[1] : '';
		if ($npfx && stristr($full, $npfx)===false) {
			$full=$npfx.' '.$full;
		}

		// Make sure the NICK is included in the NAME record.
		if (preg_match('/^'.$sublevel.' NICK (.+)/m', $gedrec, $match)) {
			$pos=strpos($full, '/');
			if ($pos===false) {
				$full.=' "'.$match[1].'"';
			} else {
				$full=substr($full, 0, $pos).'"'.$match[1].'" '.substr($full, $pos);
			}
		}

		// Convert "user-defined" unknowns into PGV unknowns
		$full=preg_replace('/\/(_+|\?+|-+)\//',            '/@N.N./', $full);
		$full=preg_replace('/(?<= |^)(_+|\?+|-+)(?= |$)/', '@P.N.',   $full);
		$surn=preg_replace('/^(_+|\?+|-+)$/',              '@N.N.',   $surn);
		$givn=preg_replace('/(?<= |^)(_+|\?+|-+)(?= |$)/', '@P.N.',   $givn);
		foreach ($surns as $key=>$value) {
			$surns[$key]=preg_replace('/^(_+|\?+|-+)$/', '@N.N.', $value);
		}

		// Create the list (surname first) version of the name.  Note that zero
		// slashes are valid; they indicate NO surname as opposed to missing surname.
		$pos1=strpos($full, '/');
		if ($pos1===false) {
			$list=$full;
		} else {
			$pos2=strrpos($full, '/');
			$list=trim(substr($full, $pos1+1, $pos2-$pos1-1)).', '.substr($full, 0, $pos1).substr($full, $pos2+1);
			$list=trim(str_replace(array('/', '  '), array('', ' '), $list));
			$full=trim(str_replace(array('/', '  '), array('', ' '), $full));
		}

		// Need the "not known" place holders for the database
		$fullNN=$full;
		$listNN=$list;
		$surname=$surn;

		// Hungarians want the "full" name to be the surname first (i.e. "list") variant
		if ($NAME_REVERSE) {
			$full=$list;
		}

		// Some people put preferred names in quotes
		if ($UNDERLINE_NAME_QUOTES) {
			$full=preg_replace('/"([^"]*)"/', '<span class="starredname">\\1</span>', $full);
			$list=preg_replace('/"([^"]*)"/', '<span class="starredname">\\1</span>', $list);
		}

		// The standards say you should use a suffix of "*"
		$full=preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $full);
		$list=preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $list);

		// If the name is written in greek/cyrillic/hebrew/etc., use the "unknown" name
		// from that character set.  Otherwise use the one in the language file.
		if (strpos($givn, '@P.N.')!==false || $surn=='@N.N.' || $surns[0]=='@N.N.') {
			if (strpos($givn, '@P.N.')!==false && ($surn=='@N.N.' || $surns[0]=='@N.N.')) {
				$PN=$pgv_lang['PN'];
				$NN=$pgv_lang['NN'];
			} else {
				if ($surn!=='')
					$PN=$unknownPN[whatLanguage($surn)];
				else
					$PN=$unknownPN[whatLanguage($surns[0])];
				$NN=$unknownNN[whatLanguage($givn)];
			}
			$list=str_replace(array('@N.N.','@P.N.'), array($NN, $PN), $list);
			$full=str_replace(array('@N.N.','@P.N.'), array($NN, $PN), $full);
		}
		// A comma separated list of surnames (from the SURN, not from the NAME) indicates
		// multiple surnames (e.g. Spanish).  Each one is a separate sortable name.

		$GIVN=UTF8_strtoupper($givn);
		foreach ($surns as $n=>$surn) {
			$SURN=UTF8_strtoupper($surn);
			// Scottish "Mc and Mac" prefixes sort under "Mac"
			if (substr($SURN, 0, 2)=='MC'  ) { $SURN='MAC'.substr($SURN, 2); }
			if (substr($SURN, 0, 4)=='MAC ') { $SURN='MAC'.substr($SURN, 4); }

			$this->_getAllNames[]=array(
				'type'=>$type, 'full'=>$full, 'list'=>$list, 'sort'=>$SURN.','.$GIVN,
				// These extra parts used to populate the pgv_name table and the indi list
				// For these, we don't want to translate the @N.N. into local text
				'fullNN'=>$fullNN,
				'listNN'=>$listNN,
				'surname'=>$surname,
				'givn'=>$givn,
				'spfx'=>($n?'':$spfx),
				'surn'=>$SURN
			);
		}
	}

	// Get an array of structures containing all the names in the record
	function getAllNames() {
		return parent::getAllNames('NAME');
	}

	// Extra info to display when displaying this record in a list of
	// selection items or favourites.
	function format_list_details() {
		return
		$this->format_first_major_fact(PGV_EVENTS_BIRT, 1).
		$this->format_first_major_fact(PGV_EVENTS_DEAT, 1);
	}
}

// Localise a date differences.  This is a default function, and may be overridden in includes/extras/functions.xx.php
function DefaultGetLabel(&$label, &$gap) {
	global $pgv_lang;

	if (($gap==12)||($gap==-12)) $label .= round($gap/12)." ".$pgv_lang["year1"]; // 1 year
	else if ($gap>20 or $gap<-20) $label .= round($gap/12)." ".$pgv_lang["years"]; // x years
	else if (($gap==1)||($gap==-1)) $label .= $gap." ".$pgv_lang["month1"]; // 1 month
	else if ($gap!=0) $label .= $gap." ".$pgv_lang["months"]; // x months
}
?>

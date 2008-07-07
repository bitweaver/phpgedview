<?php
/**
 * Controller for the timeline chart
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
 * @subpackage Charts
 * @version $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access an include file directly.";
	exit;
}

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require_once("config.php");
require_once("includes/functions_charts.php");
require_once 'includes/controllers/basecontrol.php';
require_once 'includes/person_class.php';
/**
 * Main controller class for the timeline page.
 */
class TimelineControllerRoot extends BaseController {
	var $bheight = 30;
	var $placements = array();
	var $familyfacts = array();
	var $indifacts = array();						// array to store the fact records in for sorting and displaying
	var $birthyears=array();
	var $birthmonths=array();
	var $birthdays=array();
	var $baseyear=0;
	var $topyear=0;
	var $pids = array();
	var $people = array();
	var $pidlinks = "";
	var $scale = 2;
	// GEDCOM elements that will be found but should not be displayed
	var $nonfacts = "FAMS,FAMC,MAY,BLOB,OBJE,SEX,NAME,SOUR,NOTE,BAPL,ENDL,SLGC,SLGS,_TODO,CHAN,HUSB,WIFE,CHIL";
	/**
	 * constructor
	 */
	function TimelineRootController() {
		parent::BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
		$this->baseyear = date("Y");
		//-- new pid
		if (isset($_REQUEST['newpid'])) {
			$newpid = clean_input($_REQUEST['newpid']);
			$indirec = find_person_record($newpid);
			if (empty($indirec)) {
				if (stristr($newpid, "I")===false) $newpid = "I".$newpid;
			}
		}
		
		if (isset($_REQUEST['clear'])) unset($_SESSION['timeline_pids']);
		else {
			if (isset($_SESSION['timeline_pids'])) $this->pids = $_SESSION['timeline_pids'];
			//-- pids array
			if (isset($_REQUEST['pids'])){
				$this->pids = $_REQUEST['pids'];
			}
		}
		if (!is_array($this->pids)) $this->pids = array();
		else {
			//-- make sure that arrays are indexed by numbers
			$this->pids = array_values($this->pids);
		}
		if (!empty($newpid) && !in_array($newpid, $this->pids)) $this->pids[] = $newpid;
		if (count($this->pids)==0) $this->pids[] = check_rootid("");
		$remove = "";
		if (!empty($_REQUEST['remove'])) $remove = $_REQUEST['remove'];
		//-- cleanup user input
		$newpids = array();
		foreach($this->pids as $key=>$value) {
			if ($value!=$remove) {
				$value = clean_input($value);
				$newpids[] = $value;
				$person = Person::getInstance($value);
				if (!is_null($person)) $this->people[] = $person; 
			}
		}
		$this->pids = $newpids;
		$this->pidlinks = "";
		foreach($this->people as $p=>$indi) {
			if (!is_null($indi) && $indi->canDisplayDetails()) {
				//-- setup string of valid pids for links
				$this->pidlinks .= "pids[]=".$indi->getXref()."&amp;";
				$bdate = $indi->getBirthDate();
				if ($bdate->isOK()) {
					$date = $bdate->MinDate();
					$date = $date->convert_to_cal('gregorian');
					if ($date->y) {
						$this->birthyears [$indi->getXref()] = $date->y;
						$this->birthmonths[$indi->getXref()] = max(1, $date->m);
						$this->birthdays  [$indi->getXref()] = max(1, $date->d);
					}
				}
				// find all the fact information
				$facts = get_all_subrecords($indi->getGedcomRecord(), $this->nonfacts, true, false);
				foreach($facts as $indexval => $factrec) {
					//-- get the fact type
					$ct = preg_match("/1 (\w+)(.*)/", $factrec, $match);
					if ($ct > 0) {
						$fact = trim($match[1]);
						$desc = trim($match[2]);
						//-- check for a date
						if (preg_match("/2 DATE (.*)/", $factrec, $match)) {
							$date=new GedcomDate($match[1]);
							$date=$date->MinDate();
							$date=$date->convert_to_cal('gregorian');
							if ($date->y) {
								$this->baseyear=min($this->baseyear, $date->y);
								$this->topyear =max($this->topyear,  $date->y);

								if (!is_dead_id($indi->getXref()))
									$this->topyear=max($this->topyear, date('Y'));
								$tfact = array();
								$tfact["p"] = $p;
								$tfact["pid"] = $indi->getXref();
								$tfact[1] = $factrec;
								$this->indifacts[] = $tfact;
							}
						}
					}
				}
			}
		}
		$_SESSION['timeline_pids'] = $this->pids;
		if (empty($_REQUEST['scale'])) {
			$this->scale = round(($this->topyear-$this->baseyear)/20 * count($this->indifacts)/4);
			if ($this->scale<6) $this->scale = 6;
		}
		else $this->scale = $_REQUEST['scale'];
		if ($this->scale<2) $this->scale=2;
		$this->baseyear -= 5;
		$this->topyear += 5;
	}
	/**
	 * check the privacy of the incoming people to make sure they can be shown
	 */
	function checkPrivacy() {
		global $CONTACT_EMAIL;
		$printed = false;
		for($i=0; $i<count($this->people); $i++) {
			if (!is_null($this->people[$i])) {
				if (!$this->people[$i]->canDisplayDetails()) {
					if ($this->people[$i]->canDisplayName()) {
						print "&nbsp;<a href=\"individual.php?pid=".$this->people[$i]->getXref()."\">".PrintReady($this->people[$i]->getName())."</a>";
						print_privacy_error($CONTACT_EMAIL);
						print "<br />";
						$printed = true;
					}
					else if (!$printed) {
						print_privacy_error($CONTACT_EMAIL);
						print "<br />";
					}
				}
			}
		}
	}
	function print_time_fact($factitem) {
		global $basexoffset, $baseyoffset, $factcount, $TEXT_DIRECTION;
		global $factarray, $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $SHOW_PEDIGREE_PLACES, $placements;
		global $familyfacts, $GEDCOM;
		$factrec = $factitem[1];
		$ct = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		if ($ct > 0) {
			$fact = trim($match[1]);
			$desc = trim($match[2]);
			if ($fact=="EVEN" || $fact=="FACT") {
				$ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
				if ($ct>0) $fact = trim($match[1]);
			}
			$ct = preg_match("/2 DATE (.*)/", $factrec, $match);
			if ($ct>0) {
				//-- check if this is a family fact
				$ct = preg_match("/1 _PGVFS @(.*)@/", $factrec, $fmatch);
				if ($ct>0) {
					$famid = trim($fmatch[1]);
					//-- if we already showed this family fact then don't print it
					if (isset($familyfacts[$famid.$fact])&&($familyfacts[$famid.$fact]!=$factitem["p"])) return;
					$familyfacts[$famid.$fact] = $factitem["p"];
				}
				$gdate=new GedcomDate($match[1]);
				$date=$gdate->MinDate();
				$date=$date->convert_to_cal('gregorian');
				$year  = $date->y;
				$month = max(1, $date->m);
				$day   = max(1, $date->d);
				$xoffset = $basexoffset+20;
				$yoffset = $baseyoffset+(($year-$this->baseyear) * $this->scale)-($this->scale);
				$yoffset = $yoffset + (($month / 12) * $this->scale);
				$yoffset = $yoffset + (($day / 30) * ($this->scale/12));
				$yoffset = floor($yoffset);
				$place = round($yoffset / $this->bheight);
				$i=1;
				$j=0;
				$tyoffset = 0;
				while(isset($placements[$place])) {
					if ($i==$j) {
						$tyoffset = $this->bheight * $i;
						$i++;
					}
					else {
						$tyoffset = -1 * $this->bheight * $j;
						$j++;
					}
					$place = round(($yoffset+$tyoffset) / ($this->bheight));
				}
				$yoffset += $tyoffset;
				$xoffset += abs($tyoffset);
				$placements[$place] = $yoffset;

				print "\n\t\t<div id=\"fact$factcount\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($xoffset):"right: ".($xoffset))."px; top:".($yoffset)."px; font-size: 8pt; height: ".($this->bheight)."px; \" onmousedown=\"factMD(this, '".$factcount."', ".($yoffset-$tyoffset).");\">\n";
				print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"cursor: hand;\"><tr><td>\n";
				print "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" name=\"boxline$factcount\" id=\"boxline$factcount\" height=\"3\" align=\"left\" hspace=\"0\" width=\"10\" vspace=\"0\" alt=\"\" style=\"padding-";
				if ($TEXT_DIRECTION=="ltr") print "left";
				else print "right";
				print ": 3px;\" />\n";
				$col = $factitem["p"] % 6;
				print "</td><td valign=\"top\" class=\"person".$col."\">\n";
				if (count($this->pids) > 6)print get_person_name($factitem["pid"])." - ";
				if (isset($factarray[$fact])) print $factarray[$fact];
				else if (isset($pgv_lang[$fact])) print $pgv_lang[$fact];
				else print $fact;
				print "--";
				print $gdate->Display(false);
				$indi=Person::GetInstance($factitem["pid"]); // TODO we already have this object somewhere....
				$birth_date=$indi->getEstimatedBirthDate();
				$age=get_age_at_event(GedcomDate::GetAgeGedcom($birth_date, $gdate), false);
				if (!empty($age))
					print " ({$pgv_lang['age']} {$age})";
				print " {$desc}";
				if ($SHOW_PEDIGREE_PLACES>0) {
					$pct = preg_match("/2 PLAC (.*)/", $factrec, $match);
					if ($pct>0) {
						print " - ";
						$plevels = preg_split("/,/", $match[1]);
						for($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
							if (!empty($plevels[$plevel])) {
								if ($plevel>0) print ", ";
								print PrintReady($plevels[$plevel]);
							}
						}
					}
				}
				//-- print spouse name for marriage events
				$ct = preg_match("/1 _PGVS @(.*)@/", $factrec, $match);
				if ($ct>0) {
					$spouse=$match[1];
					if ($spouse!=="") {
						for($p=0; $p<count($this->pids); $p++) {
							if ($this->pids[$p]==$spouse) break;
						}
						if ($p==count($this->pids)) $p = $factitem["p"];
						$col = $p % 6;
						print " <span class=\"person$col\"> <a href=\"individual.php?pid=$spouse&amp;ged=$GEDCOM\">";
						if (displayDetailsById($spouse)||showLivingNameById($spouse)) print get_person_name($spouse);
						else print $pgv_lang["private"];
						print "</a> </span>";
					}
				}
				print "</td></tr></table>\n";
				print "</div>";
				if ($TEXT_DIRECTION=='ltr') {
					$img = "dline2";
					$ypos = "0%";
				}
				else {
					$img = "dline";
					$ypos = "100%";
				}
				$dyoffset = ($yoffset-$tyoffset)+$this->bheight/3;
				if ($tyoffset<0) {
					$dyoffset = $yoffset+$this->bheight/3;
					if ($TEXT_DIRECTION=='ltr') {
						$img = "dline";
						$ypos = "100%";
					}
					else {
						$img = "dline2";
						$ypos = "0%";
					}
				}
				//-- print the diagnal line
				print "\n\t\t<div id=\"dbox$factcount\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+23):"right: ".($basexoffset+23))."px; top:".($dyoffset)."px; font-size: 8pt; height: ".(abs($tyoffset))."px; width: ".(abs($tyoffset))."px;";
				print " background-image: url('".$PGV_IMAGE_DIR."/".$PGV_IMAGES[$img]["other"]."');";
				print " background-position: 0% $ypos; \" >\n";
				print "</div>\n";
			}
		}
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/timeline_ctrl_user.php'))
{
	include_once 'includes/controllers/timeline_ctrl_user.php';
}
else
{
	class TimelineController extends TimelineControllerRoot
	{
	}
}
$controller = new TimelineController();
$controller->init();
?>

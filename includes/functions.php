<?php
/**
 * Core Functions that can be used by any page in PGV
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2003  John Finlay and Others
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
 * @version $Id: functions.php,v 1.6 2006/10/02 22:47:24 lsces Exp $
 */

/**
 * security check to prevent hackers from directly accessing this file
 */
if (strstr($_SERVER["PHP_SELF"],"functions.php")) {
	print "Why do you want to do that?";
	exit;
}

/**
 * The level of error reporting
 * $ERROR_LEVEL = 0 will not print any errors
 * $ERROR_LEVEL = 1 will only print the last function that was called
 * $ERROR_LEVEL = 2 will print a full stack trace with function arguments.
 */
$ERROR_LEVEL = 2;
if (isset($DEBUG)) $ERROR_LEVEL = 2;

// ************************************************* START OF INITIALIZATION FUNCTIONS ********************************* //
/**
 * initialize and check the database
 *
 * this function will create a database connection and return false if any errors occurred
 * @param boolean $ignore_previous	whether or not to ignore a previous connection , this parameter is used mainly for the editconfig.php page when setting everything up
 * @return boolean true if database successfully connected, false if there was an error
 */
function check_db($ignore_previous=false) {
	global $gGedcom, $DBCONN, $TOTAL_QUERIES, $PHP_SELF, $DBPERSIST, $CONFIGURED;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	if (!$ignore_previous) {
		if ($gGedcom->mDb->isValid()) return true;
		if (!empty($$gGedcom->mDb->ErrorMsg )) {
			return false;
		}
	}
	//-- initialize query counter
	$TOTAL_QUERIES = 0;

	$DBCONN = $gGedcom->mDb;
	if (!empty($gGedcom->mDb->ErrorMsg )) {
		//die($DBCONN->getMessage());
		return false;
	}

	//-- protect the username and password on pages other than the Configuration page
	if (strpos($_SERVER["SCRIPT_NAME"], "editconfig.php") === false) {
		$DBUSER = "";
		$DBPASS = "";
	}
	return true;
}

/**
 * get gedcom configuration file
 *
 * this function returns the path to the currently active GEDCOM configuration file
 * @return string path to gedcom.ged_conf.php configuration file
 */
function get_config_file() {
	global $GEDCOMS, $GEDCOM;
	if (count($GEDCOMS)==0) {
		return "config_gedcom.php";
	}
	if ((!empty($GEDCOM))&&(isset($GEDCOMS[$GEDCOM]))) return $GEDCOMS[$GEDCOM]["config"];
	foreach($GEDCOMS as $GEDCOM=>$gedarray) {
		$_SESSION["GEDCOM"] = $GEDCOM;
		return $gedarray["config"];
	}
}

/**
 * Get the version of the privacy file
 *
 * This function opens the given privacy file and returns the privacy version from the file
 * @param string $privfile the path to the privacy file
 * @return string the privacy file version number
 */
function get_privacy_file_version($privfile) {
	$privversion = "0";

	//-- check to make sure that the privacy file is the current version
	if (file_exists($privfile)) {
		$privcontents = implode("", file($privfile));
		$ct = preg_match("/PRIVACY_VERSION.*=.*\"(.+)\"/", $privcontents, $match);
		if ($ct>0) {
			$privversion = trim($match[1]);
		}
	}

	return $privversion;
}

/**
 * Get the path to the privacy file
 *
 * Get the path to the privacy file for the currently active GEDCOM
 * @return string path to the privacy file
 */
function get_privacy_file() {
	global $GEDCOMS, $GEDCOM, $REQUIRED_PRIVACY_VERSION;

	$privfile = "privacy.php";
	if (count($GEDCOMS)==0) {
		$privfile = "privacy.php";
	}
	if ((!empty($GEDCOM))&&(isset($GEDCOMS[$GEDCOM]))) {
		if ((isset($GEDCOMS[$GEDCOM]["privacy"]))&&(file_exists($GEDCOMS[$GEDCOM]["privacy"]))) $privfile = $GEDCOMS[$GEDCOM]["privacy"];
		else $privfile = "privacy.php";
	}
	else {
		foreach($GEDCOMS as $GEDCOM=>$gedarray) {
			$_SESSION["GEDCOM"] = $GEDCOM;
			if ((isset($gedarray["privacy"]))&&(file_exists($gedarray["privacy"]))) $privfile = $gedarray["privacy"];
			else $privfile = "privacy.php";
		}
	}
	$privversion = get_privacy_file_version($privfile);
	if ($privversion<$REQUIRED_PRIVACY_VERSION) $privfile = "privacy.php";

	return $privfile;
}

/**
 * Get the current time in micro seconds
 *
 * returns a timestamp for the current time in micro seconds
 * obtained from online documentation for the microtime() function
 * on php.net
 * @return float time in micro seconds
 */
function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Store GEDCOMS array
 *
 * this function will store the <var>$GEDCOMS</var> array in the <var>$INDEX_DIRECTORY</var>/gedcoms.php
 * file.  The gedcoms.php file is included in session.php to create the <var>$GEDCOMS</var>
 * array with every page request.
 * @see session.php
 */
function store_gedcoms() {
	global $GEDCOMS, $pgv_lang, $INDEX_DIRECTORY, $DEFAULT_GEDCOM, $COMMON_NAMES_THRESHOLD, $GEDCOM, $CONFIGURED;
	global $COMMIT_COMMAND;

	if (!$CONFIGURED) return false;
	uasort($GEDCOMS, "gedcomsort");
	$gedcomtext = "<?php\n//--START GEDCOM CONFIGURATIONS\n";
	$gedcomtext .= "\$GEDCOMS = array();\n";
	$maxid = 0;
	foreach ($GEDCOMS as $name => $details) {
		if (isset($details["id"]) && $details["id"] > $maxid) $maxid = $details["id"];
	}
	if ($maxid !=0) $maxid++;
	foreach($GEDCOMS as $indexval => $GED) {
		$GED["config"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["config"]);
		if (isset($GED["privacy"])) $GED["privacy"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["privacy"]);
		else $GED["privacy"] = "privacy.php";
		$GED["path"] = str_replace($INDEX_DIRECTORY, "\${INDEX_DIRECTORY}", $GED["path"]);
		$GED["title"] = stripslashes($GED["title"]);
		$GED["title"] = preg_replace("/\"/", "\\\"", $GED["title"]);
		$gedcomtext .= "\$gedarray = array();\n";
		$gedcomtext .= "\$gedarray[\"gedcom\"] = \"".$GED["gedcom"]."\";\n";
		$gedcomtext .= "\$gedarray[\"config\"] = \"".$GED["config"]."\";\n";
		$gedcomtext .= "\$gedarray[\"privacy\"] = \"".$GED["privacy"]."\";\n";
		$gedcomtext .= "\$gedarray[\"title\"] = \"".$GED["title"]."\";\n";
		$gedcomtext .= "\$gedarray[\"path\"] = \"".$GED["path"]."\";\n";
		// TODO: Commonsurnames from an old gedcom are used
		// TODO: Default GEDCOM is changed to last uploaded GEDCOM

		// NOTE: Set the GEDCOM ID
		if (!isset($GED["id"]) && $maxid == 0) $GED["id"] = 1;
		else if (!isset($GED["id"]) && $maxid > 0) $GED["id"] = $maxid;
		else if (empty($GED["id"])) $GED["id"] = $maxid;

		$gedcomtext .= "\$gedarray[\"id\"] = \"".$GED["id"]."\";\n";
		if (empty($GED["commonsurnames"])) {
			if ($GED["gedcom"]==$GEDCOM) {
				$GED["commonsurnames"] = "";
				$surnames = get_common_surnames($COMMON_NAMES_THRESHOLD);
//				$GED["commonsurnames"] = ",";
				foreach($surnames as $indexval => $surname) {
					$GED["commonsurnames"] .= $surname["name"].", ";
				}
			}
			else $GED["commonsurnames"]="";
		}
		$GEDCOMS[$GED["gedcom"]]["commonsurnames"] = $GED["commonsurnames"];
		$gedcomtext .= "\$gedarray[\"commonsurnames\"] = \"".addslashes($GED["commonsurnames"])."\";\n";
		$gedcomtext .= "\$GEDCOMS[\"".$GED["gedcom"]."\"] = \$gedarray;\n";
	}
	$gedcomtext .= "\n\$DEFAULT_GEDCOM = \"$DEFAULT_GEDCOM\";\n";
	$gedcomtext .= "\n?".">";
	$fp = fopen($INDEX_DIRECTORY."gedcoms.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $gedcomtext);
		fclose($fp);
		if (!empty($COMMIT_COMMAND)) check_in("store_gedcoms() ->" . getUserName() ."<-", "gedcoms.php", $INDEX_DIRECTORY, true);
	}
}

/**
 * get a gedcom filename from its database id
 * @param int $ged_id	The gedcom database id to get the filename for
 * @return string
 */
function get_gedcom_from_id($ged_id) {
	global $GEDCOMS;

	if (isset($GEDCOMS[$ged_id])) return $ged_id;
	foreach($GEDCOMS as $ged=>$gedarray) {
		if ($gedarray["id"]==$ged_id) return $ged;
	}

	return $ged;
}

/**
 * Check if a person is dead
 *
 * For the given XREF id, this function will return true if the person is dead
 * and false if the person is alive.
 * @param string $pid		The Gedcom XREF ID of the person to check
 * @return boolean			True if dead, false if alive
 */
function is_dead_id($pid) {
	global $indilist, $BUILDING_INDEX, $GEDCOM, $GEDCOMS;

	if (empty($pid)) return true;

	//-- if using indexes then first check the indi_isdead array
	if ((!$BUILDING_INDEX)&&(isset($indilist))) {
		//-- check if the person is already in the $indilist cache
		if ((!isset($indilist[$pid]["isdead"]))||($indilist[$pid]["gedfile"]!=$GEDCOMS[$GEDCOM]['id'])) {
			//-- load the individual into the cache by calling the find_person_record function
			$gedrec = find_person_record($pid);
			if (empty($gedrec)) return true;
		}
		if ($indilist[$pid]["gedfile"]==$GEDCOMS[$GEDCOM]['id']) {
			if (!isset($indilist[$pid]["isdead"])) $indilist[$pid]["isdead"] = -1;
			if ($indilist[$pid]["isdead"]==-1) $indilist[$pid]["isdead"] = update_isdead($pid, $indilist[$pid]);
			return $indilist[$pid]["isdead"];
		}
	}
	return is_dead(find_person_record($pid));
}

// This functions checks if an existing file is physically writeable
// The standard PHP function only checks for the R/O attribute and doesn't
// detect authorisation by ACL.
function file_is_writeable($file) {
	$err_write = false;
	$handle = @fopen($file,"r+");
	if	($handle)	{
		$i = fclose($handle);
		$err_write = true;
	}
	return($err_write);
}

/**
 * PGV Error Handling function
 *
 * This function will be called by PHP whenever an error occurs.  The error handling
 * is set in the session.php
 * @see http://us2.php.net/manual/en/function.set-error-handler.php
 */
function pgv_error_handler($errno, $errstr, $errfile, $errline) {
	global $LAST_ERROR, $ERROR_LEVEL;

	if ((error_reporting() > 0)&&($errno<2048)) {
		$LAST_ERROR = $errstr." in ".$errfile." on line ".$errline;
		if ($ERROR_LEVEL==0) return;
		if (stristr($errstr,"by reference")==true) return;
		$msg = "\n<br />ERROR ".$errno.": ".$errstr."<br />\n";
		//$msg .= "Error occurred on line ".$errline." of file ".basename($errfile)."<br />\n";
		print $msg;
		//AddToLog($msg);
		if (($errno<16)&&(function_exists("debug_backtrace"))&&(strstr($errstr, "headers already sent by")===false)) {
			$backtrace = array();
			if (function_exists('debug_backtrace')) $backtrace = debug_backtrace();
			$num = count($backtrace);
			if ($ERROR_LEVEL==1) $num = 1;
			for($i=0; $i<$num; $i++) {
				print $i;
				if ($i==0) print " Error occurred on ";
				else print " called from ";
				if (isset($backtrace[$i]["line"]) && isset($backtrace[$i]["file"])) print "line <b>".$backtrace[$i]["line"]."</b> of file <b>".basename($backtrace[$i]["file"])."</b>";
				if ($i<$num-1) print " in function <b>".$backtrace[$i+1]["function"]."</b>";
				/*if ($i<$num-1) print " args(";
				if (isset($backtrace[$i]['args'])) {
					if (is_array($backtrace[$i]['args']))
						foreach($backtrace[$i]['args'] as $name=>$value) print $value.",";
					else print $backtrace[$i]['args'];
				}
				print ")";*/
				print "<br />\n";
			}
		}
		if ($errno==1) die();
	}
	return false;
}

// ************************************************* START OF GEDCOM FUNCTIONS ********************************* //

/**
 * Replacement function for strrpos()
 * Returns the numeric position of the last occurrence of needle in the haystack string.
 * Note that the needle in this case can only be a single character in PHP 4. If a string
 * is passed as the needle, then only the first character of that string will be used.
 * @author escii at hotmail dot com ( Brendan )
 * @param string $haystack The text to be searched through
 * @param string $needle The text to be found
 * @param int $ret The position at which the needle is found
 */
function strrpos4($haystack, $needle) {
       while($ret = strrpos($haystack,$needle)) {
		  if(strncmp(substr($haystack,$ret,strlen($needle)), $needle,strlen($needle)) == 0 ) return $ret;
            $haystack = substr($haystack,0,$ret -1 );
       }
       return $ret;
}

/**
 * Get first tag in GEDCOM sub-record
 *
 * This routine uses function get_sub_record to retrieve the specified sub-record
 * and then returns the first tag.
 *
 */
function get_first_tag($level, $tag, $gedrec, $num=1) {
	$temp = get_sub_record($level, $tag, $gedrec, $num)."\n";
	$temp = str_replace("\r\n", "\n", $temp);
	$length = strpos($temp, "\n");
	return substr($temp, 0, $length);
}

/**
 * get a gedcom subrecord
 *
 * searches a gedcom record and returns a subrecord of it.  A subrecord is defined starting at a
 * line with level N and all subsequent lines greater than N until the next N level is reached.
 * For example, the following is a BIRT subrecord:
 * <code>1 BIRT
 * 2 DATE 1 JAN 1900
 * 2 PLAC Phoenix, Maricopa, Arizona</code>
 * The following example is the DATE subrecord of the above BIRT subrecord:
 * <code>2 DATE 1 JAN 1900</code>
 * @author John Finlay (yalnifj)
 * @author Roland Dalmulder (roland-d)
 * @param int $level the N level of the subrecord to get
 * @param string $tag a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
 * @param string $gedrec the parent gedcom record to search in
 * @param int $num this allows you to specify which matching <var>$tag</var> to get.  Oftentimes a
 * gedcom record will have more that 1 of the same type of subrecord.  An individual may have
 * multiple events for example.  Passing $num=1 would get the first 1.  Passing $num=2 would get the
 * second one, etc.
 * @return string the subrecord that was found or an empty string "" if not found.
 */
function get_sub_record($level, $tag, $gedrec, $num=1) {
	$pos1=0;
	$subrec = "";
	$tag = trim($tag);
	$searchTarget = "/".preg_replace("~/~","\\/",$tag)."[\W]/"; // see [ 1492470 ] and [ 1507176 ]
	if (empty($gedrec)) return "";

		$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		if ($ct==0) {
			$tag = preg_replace("/(\w+)/", "_$1", $tag);
			$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
			if ($ct==0) return "";
		}
		//print_r($match);
		if (count($match)<$num) return "";
		$pos1 = $match[$num-1][0][1];
		if ($pos1===false) return "";
		$pos2 = @strpos($gedrec, "\n$level", $pos1+5);
		if (!$pos2) $pos2 = @strpos($gedrec, "\n1", $pos1+5);
		if (!$pos2) $pos2 = @strpos($gedrec, "\nPGV_", $pos1+5); // PGV_SPOUSE, PGV_FAMILY_ID ...
		if (!$pos2) {
			return substr($gedrec, $pos1);
		}
		$subrec = substr($gedrec, $pos1, $pos2-$pos1);
		//-- does anybody know what this is for?
		if ($num==1) {
			$lowtag = "\n".($level-1).(substr($tag, 1));
			if (phpversion() < 5) {
				if ($newpos = strrpos4($subrec, $lowtag)) {
				$pos2 = $pos2 - (strlen($subrec) - $newpos);
				$subrec = substr($gedrec, $pos1, $pos2-$pos1);
				}
			}
			else if ($newpos = strripos($subrec, $lowtag)) {
				$pos2 = $pos2 - (strlen($subrec) - $newpos);
				$subrec = substr($gedrec, $pos1, $pos2-$pos1);
			}
		}

	return $subrec;
}

/**
 * find all of the level 1 subrecords of the given record
 * @param string $gedrec the gedcom record to get the subrecords from
 * @param string $ignore a list of tags to ignore
 * @param boolean $families whether to include any records from the family
 * @param boolean $sort whether or not to sort the record by date
 * @param boolean $ApplyPriv whether to apply privacy right now or later
 * @return array an array of the raw subrecords to return
 */
function get_all_subrecords($gedrec, $ignore="", $families=true, $sort=true, $ApplyPriv=true) {
	global $ASC, $IGNORE_FACTS, $IGNORE_YEAR;
	$repeats = array();

	$id = "";
	$gt = preg_match("/0 @(.+)@/", $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}
	$prev_tags = array();
	$ct = preg_match_all("/\n1 (\w+)(.*)/", $gedrec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$fact = trim($match[$i][1]);
		if (strpos($ignore, $fact)===false) {
			if (!$ApplyPriv || (showFact($fact, $id)&& showFactDetails($fact,$id))) {
				if (isset($prev_tags[$fact])) $prev_tags[$fact]++;
				else $prev_tags[$fact] = 1;
				$subrec = get_sub_record(1, "1 $fact", $gedrec, $prev_tags[$fact]);
				if (!$ApplyPriv || !FactViewRestricted($id, $subrec)) {
					if ($fact=="EVEN") {
						$tt = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
						if ($tt>0) {
							$type = trim($tmatch[1]);
							if (!$ApplyPriv || (showFact($type, $id)&&showFactDetails($type,$id))) $repeats[] = trim($subrec)."\r\n";
						}
						else $repeats[] = trim($subrec)."\r\n";
					}
					else $repeats[] = trim($subrec)."\r\n";
				}
			}
		}
	}

	//-- look for any records in FAMS records
	if ($families) {
		$ft = preg_match_all("/1 FAMS @(.+)@/", $gedrec, $fmatch, PREG_SET_ORDER);
		for($f=0; $f<$ft; $f++) {
			$famid = $fmatch[$f][1];
			$famrec = find_gedcom_record($fmatch[$f][1]);
			$parents = find_parents_in_record($famrec);
			if ($id==$parents["HUSB"]) $spid = $parents["WIFE"];
			else $spid = $parents["HUSB"];
			$prev_tags = array();
			$ct = preg_match_all("/\n1 (\w+)(.*)/", $famrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$fact = trim($match[$i][1]);
				if (strpos($ignore, $fact)===false) {
					if (!$ApplyPriv || (showFact($fact, $id)&&showFactDetails($fact,$id))) {
						if (isset($prev_tags[$fact])) $prev_tags[$fact]++;
						else $prev_tags[$fact] = 1;
						$subrec = get_sub_record(1, "1 $fact", $famrec, $prev_tags[$fact]);
						$subrec .= "\r\n2 _PGVS @$spid@\r\n";
						$subrec .= "2 _PGVFS @$famid@\r\n";
						if ($fact=="EVEN") {
							$ct = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
							if ($ct>0) {
								$type = trim($tmatch[1]);
								if (!$ApplyPriv or (showFact($type, $id)&&showFactDetails($type,$id))) $repeats[] = trim($subrec)."\r\n";
							}
							else $repeats[] = trim($subrec)."\r\n";
						}
						else $repeats[] = trim($subrec)."\r\n";
					}
				}
			}
		}
	}

	if ($sort) {
		$ASC = 0;
  		$IGNORE_FACTS = 0;
  		$IGNORE_YEAR = 0;
		usort($repeats, "compare_facts");
	}
	return $repeats;
}

/**
 * get gedcom tag value
 *
 * returns the value of a gedcom tag from the given gedcom record
 * @param string $tag	The tag to find, use : to delineate subtags
 * @param int $level	The gedcom line level of the first tag to find
 * @param string $gedrec	The gedcom record to get the value from
 * @param int $truncate	Should the value be truncated to a certain number of characters
 * @param boolean $convert	Should data like dates be converted using the configuration settings
 * @return string
 */
function get_gedcom_value($tag, $level, $gedrec, $truncate='', $convert=true) {
	global $SHOW_PEDIGREE_PLACES, $pgv_lang;

	$tags = preg_split("/:/", $tag);

	$subrec = $gedrec;
	//print $level;
	foreach($tags as $indexval => $t) {
		$lastsubrec = $subrec;
		$subrec = get_sub_record($level, "$level $t", $subrec);
		if (empty($subrec)) {
			if ($t=="TITL") {
				$subrec = get_sub_record($level, "$level ABBR", $lastsubrec);
				if (!empty($subrec)) $t = "ABBR";
			}
			if (empty($subrec)) {
				if ($level>0) $level--;
				$subrec = get_sub_record($level, "@ $t", $gedrec);
				if (empty($subrec)) {
					return;
				}
			}
		}
		//print "[$level $t-:$subrec:]";
		$level++;
	}
	$level--;
	//print "[".$tag.":".$subrec."]";
	$ct = preg_match("/$level $t(.*)/", $subrec, $match);
	if ($ct==0) $ct = preg_match("/$level @.+@ (.+)/", $subrec, $match);
	if ($ct==0) $ct = preg_match("/@ $t (.+)/", $subrec, $match);
	//print $ct;
	if ($ct > 0) {
		$value = trim($match[1]);
		$ct = preg_match("/@(.*)@/", $value, $match);
		if (($ct > 0 ) && ($t!="DATE")){
			$oldsub = $subrec;
			$subrec = find_gedcom_record($match[1]);
			if ($subrec) {
				$value=$match[1];
				$ct = preg_match("/0 @$match[1]@ $t (.+)/", $subrec, $match);
				if ($ct>0) {
					$value = $match[1];
					$level = 0;
				}
				else $subrec = $oldsub;
			}
			//-- set the value to the id without the @
			else $value = $match[1];
		}
		if ($level!=0 || $t!="NOTE") $value .= get_cont($level+1, $subrec);
		$value = preg_replace("'\n'", "", $value);
		$value = preg_replace("'<br />'", "\n", $value);
		$value = trim($value);
		//-- if it is a date value then convert the date
		if ($convert && $t=="DATE") {
			$value = get_changed_date($value);
			if (!empty($truncate)) {
				if (strlen($value)>$truncate) {
					$value = preg_replace("/\(.+\)/", "", $value);
					if (strlen($value)>$truncate) {
						$value = preg_replace_callback("/([^0-9\W]+)/", create_function('$matches', 'return substr($matches[1], 0, 3);'), $value);
					}
				}
			}
		}
		//-- if it is a place value then apply the pedigree place limit
		else if ($convert && $t=="PLAC") {
			if ($SHOW_PEDIGREE_PLACES>0) {
				$plevels = preg_split("/,/", $value);
				$value = "";
				for($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
					if (!empty($plevels[$plevel])) {
						if ($plevel>0) $value .= ", ";
						$value .= trim($plevels[$plevel]);
					}
				}
			}
			if (!empty($truncate)) {
				if (strlen($value)>$truncate) {
					$plevels = preg_split("/,/", $value);
					$value = "";
					for($plevel=0; $plevel<count($plevels); $plevel++) {
						if (!empty($plevels[$plevel])) {
							if (strlen($plevels[$plevel])+strlen($value)+3 < $truncate) {
								if ($plevel>0) $value .= ", ";
								$value .= trim($plevels[$plevel]);
							}
							else break;
						}
					}
				}
			}
		}
		else if ($convert && $t=="SEX") {
			if ($value=="M") $value = get_first_letter($pgv_lang["male"]);
			else if ($value=="F") $value = get_first_letter($pgv_lang["female"]);
			else $value = get_first_letter($pgv_lang["unknown"]);
		}
		else {
			if (!empty($truncate)) {
				if (strlen($value)>$truncate) {
					$plevels = preg_split("/ /", $value);
					$value = "";
					for($plevel=0; $plevel<count($plevels); $plevel++) {
						if (!empty($plevels[$plevel])) {
							if (strlen($plevels[$plevel])+strlen($value)+3 < $truncate) {
								if ($plevel>0) $value .= " ";
								$value .= trim($plevels[$plevel]);
							}
							else break;
						}
					}
				}
			}
		}
		//print "\n[ $t $value] \n";
		return $value;
	}
	return "";
}

/**
 * get CONT lines
 *
 * get the N+1 CONT or CONC lines of a gedcom subrecord
 * @param int $nlevel the level of the CONT lines to get
 * @param string $nrec the gedcom subrecord to search in
 * @return string a string with all CONT or CONC lines merged
 */
function get_cont($nlevel, $nrec, $tobr=true) {
	global $WORD_WRAPPED_NOTES;
	$text = "";
	if ($tobr) $newline = "<br />\n";
	else $newline = "\r\n";
	$tt = preg_match_all("/$nlevel CON[CT](.*)/", $nrec, $cmatch, PREG_SET_ORDER);
	for($i=0; $i<$tt; $i++) {
		if (strstr($cmatch[$i][0], "CONT")) $text.=$newline;
		else if ($WORD_WRAPPED_NOTES) $text.=" ";
		$conctxt = $cmatch[$i][1];
		if (!empty($conctxt)) {
			if ($conctxt{0}==" ") $conctxt = substr($conctxt, 1);
			$conctxt = preg_replace("/[\r\n]/","",$conctxt);
			$text.=$conctxt;
		}
	}
	$text = preg_replace("/~~/", $newline, $text);
	return $text;
}

/**
 * find the parents in a family
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents($famid) {
	global $pgv_lang;

	$famrec = find_family_record($famid);
	if (empty($famrec)) {
		if (userCanEdit(getUserName())) {
			$famrec = find_record_in_file($famid);
			if (empty($famrec)) return false;
		}
		else return false;
	}
	return find_parents_in_record($famrec);
}

/**
 * find the parents in a family record
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents_in_record($famrec) {
	global $pgv_lang;

	if (empty($famrec)) return false;
	$parents = array();
	$ct = preg_match("/1 HUSB @(.*)@/", $famrec, $match);
	if ($ct>0) $parents["HUSB"]=$match[1];
	else $parents["HUSB"]="";
	$ct = preg_match("/1 WIFE @(.*)@/", $famrec, $match);
	if ($ct>0) $parents["WIFE"]=$match[1];
	else $parents["WIFE"]="";
	return $parents;
}

/**
 * find the children in a family
 *
 * find and return an array containing the children of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @param string $me	an xref id of a child to ignore, useful when you want to get a person's
 * siblings but do want to include them as well
 * @return array
 */
function find_children($famid, $me='') {
	global $pgv_lang;

	$famrec = find_family_record($famid);
	if (empty($famrec)) {
		if (userCanEdit(getUserName())) {
			$famrec = find_record_in_file($famid);
			if (empty($famrec)) return false;
		}
		else return false;
	}
	return find_children_in_record($famrec);
}

/**
 * find the children in a family record
 *
 * find and return an array containing the children of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @param string $me	an xref id of a child to ignore, useful when you want to get a person's
 * siblings but do want to include them as well
 * @return array
 */
function find_children_in_record($famrec, $me='') {
	global $pgv_lang;

	$children = array();
	if (empty($famrec)) return $children;

	$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $match,PREG_SET_ORDER);
	for($i=0; $i<$num; $i++) {
		$child = trim($match[$i][1]);
		if ($child!=$me) $children[] = $child;
	}
	return $children;
}

/**
 * find all child family ids
 *
 * searches an individual gedcom record and returns an array of the FAMC ids where this person is a
 * child in the family
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_family_ids($pid) {
	$families = array();
	if (!$pid) return $families;

	$indirec = find_person_record($pid);
	return find_families_in_record($indirec, "FAMC");
}

/**
 * find all spouse family ids
 *
 * searches an individual gedcom record and returns an array of the FAMS ids where this person is a
 * spouse in the family
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_sfamily_ids($pid) {
	$families = array();
	if (empty($pid)) return $families;
	$indirec = find_person_record($pid);
	return find_families_in_record($indirec, "FAMS");
}

/**
 * find all family ids in the given record
 *
 * searches an individual gedcom record and returns an array of the FAMS|C ids
 * @param string $indirec the gedcom record for the person to look in
 * @param string $tag 	The family tag to look for
 * @return array array of family ids
 */
function find_families_in_record($indirec, $tag) {
	$families = array();

	$ct = preg_match_all("/1\s*$tag\s*@(.*)@/", $indirec, $match,PREG_SET_ORDER);
	if ($ct>0){
		for($i=0; $i<$ct; $i++) {
			$families[$i] = $match[$i][1];
		}
	}
	return $families;
}

/**
 * find record in file
 *
 * this function finds a gedcom record in the gedcom file by searching through the file 4Kb at a
 * time
 * @param string $gid the gedcom xref id of the record to find
 */
function find_record_in_file($gid) {
	global $GEDCOMS, $GEDCOM, $indilist;
	$fpged = fopen($GEDCOMS[$GEDCOM]["path"], "r");
	if (!$fpged) return false;
	$BLOCK_SIZE = 1024*4;	//-- 4k bytes per read
	$fcontents = "";
	$count = 0;
	while(!feof($fpged)) {
		$fcontents .= fread($fpged, $BLOCK_SIZE);
		$count++;
		$pos1 = strpos($fcontents, "0 @$gid@", 0);
		if ($pos1===false)  {
			$pos1 = strrpos($fcontents, "\n");
		//	print $pos1."-".$count."<br /> ";
			$fcontents = substr($fcontents, $pos1);
		//	print "[".$fcontents."]";
		}
		else {
			$pos2 = strpos($fcontents, "\n0", $pos1+1);
			while((!$pos2)&&(!feof($fpged))) {
				$fcontents .= fread($fpged, $BLOCK_SIZE);
				$pos2 = strpos($fcontents, "\n0", $pos1+1);
			}
			if ($pos2) $indirec = substr($fcontents, $pos1, $pos2-$pos1);
			else $indirec = substr($fcontents, $pos1);
//			fclose($fpged);
			$ct = preg_match("/0 @.+@ (.+)/", $indirec, $match);
			if ($ct>0) {
				$type = trim($match[1]);
				//-- add record to indilist for caching
				if ($type=="INDI") {
					$indilist[$gid]["gedcom"]=$indirec;
					$indilist[$gid]["names"]=get_indi_names($indirec);
					$indilist[$gid]["gedfile"]=$GEDCOM;
					$indilist[$gid]["isdead"] = -1;
				}
			}
			fclose($fpged);
			return $indirec;
			break;
		}
	}
	fclose($fpged);
	return false;
}

/**
 * find and return an updated gedcom record
 * @param string $gid	the id of the record to find
 * @param string $gedfile	the gedcom file to get the record from.. defaults to currently active gedcom
 */
function find_updated_record($gid, $gedfile="") {
	return find_record_in_file();
}

// ************************************************* START OF MULTIMEDIA FUNCTIONS ********************************* //
/**
 * find the highlighted media object for a gedcom entity
 *
 * Rules for finding the highlighted media object:
 * 1. The first _THUM Y object will be used regardless of the object's level in the gedcom record
 * 2. The first _PRIM Y object will be used if no _THUM Y exists regardless of level in gedcom record
 * 3. The first level 1 object will be used if there is no _THUM Y or _PRIM Y and if its doesn't have _THUM N or _PRIM N (level 1 objects appear on the media tab on the individual page)
 * @param string $pid the individual, source, or family id
 * @param string $indirec the gedcom record to look in
 * @return array an object array with indexes "thumb" and "file" for thumbnail and filename
 */
function find_highlighted_object($pid, $indirec) {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $PGV_IMAGE_DIR, $PGV_IMAGES, $MEDIA_EXTERNAL;
	global $GEDCOMS, $GEDCOM, $TBLPREFIX, $DBCONN, $gGedcom;

	if (!showFactDetails("OBJE", $pid)) return false;
	$object = array();
	$media = array();

	//-- handle finding the media of remote objects
	$ct = preg_match("/(.*):(.*)/", $pid, $match);
	if ($ct>0) {
		if (class_exists("ServiceClient")) {
			$client = ServiceClient::getInstance($match[1]);
			if (!is_null($client)) {
				$mt = preg_match_all("/\d OBJE @(.*)@/", $indirec, $matches, PREG_SET_ORDER);
				for($i=0; $i<$mt; $i++) {
					$parts = preg_split("/:/", $matches[$i][1]);
					if (isset($parts[1])) {
						$mrec = $client->getRemoteRecord($parts[1]);
						if (!empty($mrec)) {
							$file = get_gedcom_value("FILE", 1, $mrec);
							$row = array($matches[$i][1], $file, $mrec, $matches[$i][0]);
							$media[] = $row;
						}
					}
				}
			}
		}
	}

	//-- find all of the media items for a person
	$sql = "SELECT m_media, m_file, m_gedrec, mm_gedrec FROM ".$TBLPREFIX."media, ".$TBLPREFIX."media_mapping WHERE m_media=mm_media AND m_gedfile=mm_gedfile AND m_gedfile=? AND mm_gid=? ORDER BY m_id";
	$res = $gGedcom->mDb->query($sql, array( $GEDCOMS[$GEDCOM]["id"], $pid ));
	while( $row = $res->fetchRow() ) {
		$media[] = $row;
	}

	//-- for the given media choose the
	foreach($media as $i=>$row) {
		if (displayDetailsById($row['m_media'], 'OBJE') && !FactViewRestricted($row['m_media'], $row['m_gedrec'])) {
			$thum = get_gedcom_value('_THUM', 1, $row['m_gedrec']);
			$prim = get_gedcom_value('_PRIM', 1, $row['m_gedrec']);
			$level=0;
			$ct = preg_match("/(\d+) OBJE/", $row['mm_gedrec'], $match);
			if ($ct>0) $level = $match[1];
			//-- always take _THUM Y objects
			if ($thum=='Y') {
				$object["file"] = check_media_depth($row['m_file']);
				$object["thumb"] = $object["file"];
				$object["level"] = $level;
				break;
			}
			//-- take the first _PRIM Y object... _PRIM Y overrides first level 1 object
			else if ($prim=='Y') {
				if (!isset($object['prim']) || !isset($object['level']) || $object['level']>$level) {
					$object["file"] = check_media_depth($row['m_file']);
					$object["thumb"] = thumbnail_file($row['m_file']);
					$object["prim"] = $prim;
					$object["level"] = $level;
				}
			}
			//-- take the first level 1 object if we don't already have one and it doesn't have _THUM N or _PRIM N
			else if (empty($object['file']) && $level==1 && $thum!='N' && $prim!='N') {
				$object["file"] = check_media_depth($row['m_file']);
				$object["thumb"] = thumbnail_file($row['m_file']);
				$object["level"] = $level;
			}
		}
	}

	return $object;
}

/**
 * get the full file path
 *
 * get the file path from a multimedia gedcom record
 * @param string $mediarec a OBJE subrecord
 * @return the fullpath from the FILE record
 */
function extract_fullpath($mediarec) {
	preg_match("/(\d) _*FILE (.*)/", $mediarec, $amatch);
	if (empty($amatch[2])) return "";
	$level = trim($amatch[1]);
	$fullpath = trim($amatch[2]);
	$filerec = get_sub_record($level, $amatch[0], $mediarec);
	$fullpath .= get_cont($level+1, $filerec);
	return $fullpath;
}

/**
 * get the relative filename for a media item
 *
 * gets the relative file path from the full media path for a media item.  checks the
 * <var>$MEDIA_DIRECTORY_LEVELS</var> to make sure the directory structure is maintained.
 * @param string $fullpath the full path from the media record
 * @return string a relative path that can be appended to the <var>$MEDIA_DIRECTORY</var> to reference the item
 */
function extract_filename($fullpath) {
	global $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;

	$filename="";
	$regexp = "'[/\\\]'";
	$srch = "/".addcslashes($MEDIA_DIRECTORY,'/.')."/";
	$repl = "";
	if (!strstr($fullpath, "://")) $nomedia = stripcslashes(preg_replace($srch, $repl, $fullpath));
	else $nomedia = $fullpath;
	$ct = preg_match($regexp, $nomedia, $match);
	if ($ct>0) {
		$subelements = preg_split($regexp, $nomedia);
		$subelements = array_reverse($subelements);
		$max = $MEDIA_DIRECTORY_LEVELS;
		if ($max>=count($subelements)) $max=count($subelements)-1;
		for($s=$max; $s>=0; $s--) {
			if ($s!=$max) $filename = $filename."/".$subelements[$s];
			else $filename = $subelements[$s];
		}
	}
	else $filename = $nomedia;
	return $filename;
}

/**
 * function to generate a thumbnail image
 * @param string $filename
 * @param string $thumbnail
 */
function generate_thumbnail($filename, $thumbnail) {
	global $MEDIA_DIRECTORY, $THUMBNAIL_WIDTH, $AUTO_GENERATE_THUMBS;

	if (!$AUTO_GENERATE_THUMBS) return false;
	if (file_exists($thumbnail)) return false;
	if (!is_writable($MEDIA_DIRECTORY."thumbs")) return false;

	if (!is_dir(filename_decode($MEDIA_DIRECTORY."thumbs/urls"))) {
		mkdir(filename_decode($MEDIA_DIRECTORY."thumbs/urls"), 0777);
		AddToLog("Folder ".$MEDIA_DIRECTORY."thumbs/urls created.");
	}
	if (!is_writable(filename_decode($MEDIA_DIRECTORY."thumbs/urls"))) return false;

	$ext = "";
	$ct = preg_match("/\.([^\.]+)$/", $filename, $match);
	if ($ct>0) {
		$ext = strtolower(trim($match[1]));
	}
	if ($ext!='jpg' && $ext!='jpeg' && $ext!='gif' && $ext!='png') return false;

	if (!strstr($filename, "://")) {
		if (!file_exists(filename_decode($filename))) return false;
		$imgsize = getimagesize(filename_decode($filename));
		// Check if a size has been determined
		if (!$imgsize) return false;

		//-- check if file is small enough to be its own thumbnail
		if (($imgsize[0]<150)&&($imgsize[1]<150)) {
			@copy($filename, $thumbnail);
			return true;
		}
	}
	else {
		if ($fp = @fopen(filename_decode($filename), "rb")) {
			if ($fp===false) return false;
			$conts = "";
			while(!feof($fp)) {
				$conts .= fread($fp, 4098);
			}
			fclose($fp);
			$fp = fopen(filename_decode($thumbnail), "wb");
			if (!fwrite($fp, $conts)) return false;
			fclose($fp);
			if (!stristr("://", $filename)) $imgsize = getimagesize(filename_decode($filename));
			else $imgsize = getimagesize(filename_decode($thumbnail));
			if ($imgsize===false) return false;
			if (($imgsize[0]<150)&&($imgsize[1]<150)) return true;
		}
		else return false;
	}

	$width = $THUMBNAIL_WIDTH;
	$height = round($imgsize[1] * ($width/$imgsize[0]));

	if ($ext=="gif") {
		if (function_exists("imagecreatefromgif") && function_exists("imagegif")) {
			$im = imagecreatefromgif(filename_decode($filename));
			if (empty($im)) return false;
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
			imagegif($new, filename_decode($thumbnail));
			imagedestroy($im);
			imagedestroy($new);
			return true;
		}
	}
	else if ($ext=="jpg" || $ext=="jpeg") {
		if (function_exists("imagecreatefromjpeg") && function_exists("imagejpeg")) {
			$im = imagecreatefromjpeg(filename_decode($filename));
			if (empty($im)) return false;
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
			imagejpeg($new, filename_decode($thumbnail));
			imagedestroy($im);
			imagedestroy($new);
			return true;
		}
	}
	else if ($ext=="png") {
		if (function_exists("imagecreatefrompng") && function_exists("imagepng")) {
			$im = imagecreatefrompng(filename_decode($filename));
			if (empty($im)) return false;
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
			imagepng($new, filename_decode($thumbnail));
			imagedestroy($im);
			imagedestroy($new);
			return true;
		}
	}

	return false;
}

// ************************************************* START OF SORTING FUNCTIONS ********************************* //
/**
 * Function to sort GEDCOM fact tags based on their tanslations
 */
function factsort($a, $b) {
   global $factarray;

   return stringsort(trim(strip_tags($factarray[$a])), trim(strip_tags($factarray[$b])));
}
/**
 * String sorting function
 * @param string $a
 * @param string $b
 * @return int negative numbers sort $a first, positive sort $b first
 */
function stringsort($aName, $bName) {
	return compareStrings($aName, $bName, true);		// Case-insensitive sort
}
function stringsort2($aName, $bName) {
	return compareStrings($aName, $bName, false);		// Case-sensitive sort
}
function compareStrings($aName, $bName, $ignoreCase=true) {
	global $LANGUAGE, $CHARACTER_SET;
	global $alphabet, $alphabet_lower, $alphabet_upper;
	global $digraph, $trigraph, $quadgraph;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;

	if (is_array($aName)) debug_print_backtrace();
	getAlphabet();

	if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
		$danishFrom = array("AA", "Aa", "AE", "Ae", "OE", "Oe", "aa", "ae", "oe");
		$danishTo 	= array("Å", "Å", "Æ", "Æ", "Ø", "Ø", "å", "æ", "ø");
	}

	if ($LANGUAGE == "german") {
		$germanFrom = array("AA", "Aa", "Æ", "AE", "Ae", "Ø", "OE", "Oe", "SS", "Ss", "UE", "Ue", "aa", "æ", "ae", "ø", "oe", "ss", "ue");
		$germanTo 	= array("Å", "Å", "Ä", "Ä", "Ä", "Ö", "Ö", "Ö", "ß", "ß", "Ü", "Ü", "å", "ä", "ä", "ö", "ö", "ß", "ü");
	}

	//-- split strings into strings and numbers
	$aParts = preg_split("/(\d+)/", $aName, -1, PREG_SPLIT_DELIM_CAPTURE);
	$bParts = preg_split("/(\d+)/", $bName, -1, PREG_SPLIT_DELIM_CAPTURE);

	//-- loop through the arrays of strings and numbers
	for($j=0; ($j<count($aParts) && $j<count($bParts)); $j++) {
		$aName = $aParts[$j];
		$bName = $bParts[$j];

		//-- sort numbers differently
		if (is_numeric($aName) && is_numeric($bName)) {
			if ($aName!=$bName) return $aName-$bName;
		}
		else {
			//-- Take care of Danish and Norwegian character transformations
			if ($LANGUAGE == "danish" || $LANGUAGE == "norwegian") {
				$aName = str_replace($danishFrom, $danishTo, $aName);
				$bName = str_replace($danishFrom, $danishTo, $bName);
			}
			// -- Take care of German character transformations
			if ($LANGUAGE == "german") {
				$aName = str_replace($germanFrom, $germanTo, $aName);
				$bName = str_replace($germanFrom, $germanTo, $bName);
			}

			//-- get the name lengths
			$alen = strlen($aName);
			$blen = strlen($bName);

			//-- loop through the characters in the string and if we find one that is different between the strings
			//-- return the difference
			$aIndex = 0;
			$bIndex = 0;
			$aDiacriticValue = "";
			$bDiacriticValue = "";
			while (true) {
				$aMultiLetter = false;
				$bMultiLetter = false;
				// Look for quadgraphs (4 letters that should be treated as 1)
				if (isset($quadgraphs[$LANGUAGE])) {
					$aLetter = strtoupper(substr($aName, $aIndex, 4));
					if (isset($quadgraphs[$LANGUAGE][$aLetter])) {
						$aMultiLetter = $quadgraphs[$LANGUAGE][$aLetter];
						$aCharLen = 4;
					}
					$bLetter = strtoupper(substr($bName, $bIndex, 4));
					if (isset($quadgraphs[$LANGUAGE][$bLetter])) {
						$bMultiLetter = $quadgraphs[$LANGUAGE][$bLetter];
						$bCharLen = 4;
					}
				}
				// Look for trigraphs (3 letters that should be treated as 1)
				if (isset($trigraphs[$LANGUAGE])) {
					if (!$aMultiLetter) {
						$aLetter = strtoupper(substr($aName, $aIndex, 3));
						if (isset($trigraphs[$LANGUAGE][$aLetter])) {
							$aMultiLetter = $trigraphs[$LANGUAGE][$aLetter];
							$aCharLen = 3;
						}
					}
					if (!$bMultiLetter) {
						$bLetter = strtoupper(substr($bName, $bIndex, 3));
						if (isset($trigraphs[$LANGUAGE][$bLetter])) {
							$bMultiLetter = $trigraphs[$LANGUAGE][$bLetter];
							$bCharLen = 3;
						}
					}
				}
				// Look for digraphs (2 letters that should be treated as 1)
				if (isset($digraphs[$LANGUAGE])) {
					if (!$aMultiLetter) {
					$aLetter = strtoupper(substr($aName, $aIndex, 2));
						if (isset($digraphs[$LANGUAGE][$aLetter])) {
							$aMultiLetter = $digraphs[$LANGUAGE][$aLetter];
							$aCharLen = 2;
						}
					}
					if (!$bMultiLetter) {
					$bLetter = strtoupper(substr($bName, $bIndex, 2));
						if (isset($digraphs[$LANGUAGE][$bLetter])) {
							$bMultiLetter = $digraphs[$LANGUAGE][$bLetter];
							$bCharLen = 2;
						}
					}
				}

				// Look for UTF-8 encoded characters
				if (!$aMultiLetter) {
					$aCharLen = 1;
					$aLetter = substr($aName, $aIndex, 1);
					if ((ord($aLetter) & 0xE0) == 0xC0) $aCharLen = 2;		// 2-byte sequence
					if ((ord($aLetter) & 0xF0) == 0xE0) $aCharLen = 3;		// 3-byte sequence
					if ((ord($aLetter) & 0xF8) == 0xF0) $aCharLen = 4;		// 4-byte sequence
				}

				if (!$bMultiLetter) {
					$bCharLen = 1;
					$bLetter = substr($bName, $bIndex, 1);
					if ((ord($bLetter) & 0xE0) == 0xC0) $bCharLen = 2;		// 2-byte sequence
					if ((ord($bLetter) & 0xF0) == 0xE0) $bCharLen = 3;		// 3-byte sequence
					if ((ord($bLetter) & 0xF8) == 0xF0) $bCharLen = 4;		// 4-byte sequence
				}

				$aLetter = substr($aName, $aIndex, $aCharLen);
				$bLetter = substr($bName, $bIndex, $bCharLen);

				if ($DICTIONARY_SORT[$LANGUAGE]) {
					//-- strip diacritics before checking equality
					if ($aCharLen==2) {
						$aPos = strpos($UCDiacritWhole, $aLetter);
						if ($aPos!==false) {
							$aPos = $aPos >> 1;
							$aLetter = substr($UCDiacritStrip, $aPos, 1);
							$aDiacriticValue .= substr($UCDiacritOrder, $aPos, 1);
						} else {
							$aPos = strpos($LCDiacritWhole, $aLetter);
							if ($aPos!==false) {
								$aPos = $aPos >> 1;
								$aLetter = substr($LCDiacritStrip, $aPos, 1);
								$aDiacriticValue .= substr($LCDiacritOrder, $aPos, 1);
							} else $aDiacriticValue .= " ";
						}
					} else $aDiacriticValue .= " ";

					if ($bCharLen==2) {
						$bPos = strpos($UCDiacritWhole, $bLetter);
						if ($bPos!==false) {
							$bPos = $bPos >> 1;
							$bLetter = substr($UCDiacritStrip, $bPos, 1);
							$bDiacriticValue .= substr($UCDiacritOrder, $bPos, 1);
						} else {
							$bPos = strpos($LCDiacritWhole, $bLetter);
							if ($bPos!==false) {
								$bPos = $bPos >> 1;
								$bLetter = substr($LCDiacritStrip, $bPos, 1);
								$bDiacriticValue .= substr($LCDiacritOrder, $bPos, 1);
							} else $bDiacriticValue .= " ";
						}
					} else $bDiacriticValue .= " ";
				}

				if ($ignoreCase) {
					$aLetter = str2upper($aLetter);
					$bLetter = str2upper($bLetter);
				}

				if ($aLetter!=$bLetter) {
					//-- get the position of the letter in the alphabet string
					if ($aMultiLetter) {
						$sortAfter = substr($aLetter,0,1);
						if ($aLetter=="CH") $sortAfter = "H";		// This one doesn't follow the rule
						if ($aLetter=="Ch") $sortAfter = "H";
						if ($aLetter=="ch") $sortAfter = "h";
						$aPos = strpos($alphabet_upper, $sortAfter);
						if ($aPos===false) $aPos = strpos($alphabet_lower, $sortAfter);
					} else {
						$aPos = @strpos($alphabet_upper, $aLetter);
						if ($aPos===false) $aPos = @strpos($alphabet_lower, $aLetter);
					}
					if ($bMultiLetter) {
						$sortAfter = substr($bLetter,0,1);
						if ($bLetter=="CH") $sortAfter = "H";		// This one doesn't follow the rule
						if ($bLetter=="Ch") $sortAfter = "H";
						if ($bLetter=="ch") $sortAfter = "h";
						$bPos = strpos($alphabet_upper, $sortAfter);
						if ($bPos===false) $bPos = strpos($alphabet_lower, $sortAfter);
					} else {
						$bPos = @strpos($alphabet_upper, $bLetter);
						if ($bPos===false) $bPos = @strpos($alphabet_lower, $bLetter);
					}

					// Insert digraphs and trigraphs into main sequence
					if ($aMultiLetter || $bMultiLetter) {
						$aPos = ((int) $aPos << 3) + (int) $aMultiLetter;
						$bPos = ((int) $bPos << 3) + (int) $bMultiLetter;
					}

					if ($aPos!=$bPos) {
						if ($aLetter=="@") return 1;		// Force "@" to the end
						if ($bLetter=="@") return -1;		// Force "@" to the end
						if (($bPos!==false)&&($aPos===false)) return -1;
						if (($bPos===false)&&($aPos!==false)) return 1;
						if (($bPos===false)&&($aPos===false)) {
							// Determine the binary value of both letters
							$aValue = ord_UTF8($aLetter);
							$bValue = ord_UTF8($bLetter);
							return $aValue - $bValue;
						}
						return ($aPos-$bPos);
					}
				}
				$aIndex += $aCharLen;			// advance to the 1st byte of the next sequence
				$bIndex += $bCharLen;			// advance to the 1st byte of the next sequence
				if ($aIndex >= $alen) break;
				if ($bIndex >= $blen) break;
			}
		}

		//-- if we made it through the loop then check if one name is longer than the
		//-- other, the shorter one should be first
		if ($alen!=$blen) return ($alen-$blen);

		//-- They're identical: let diacritics (if any) decide
		if ($aDiacriticValue < $bDiacriticValue) return -1;
		if ($aDiacriticValue > $bDiacriticValue) return 1;
	}
	if (count($aParts)!=count($bParts)) return (count($aParts)-count($bParts));

	//-- the strings are exactly the same so return 0
	return 0;
}

/**
 * User Name comparison Function
 *
 * This function just needs to call the itemsort function on the fullname
 * field of the array
 * @param array $a first user array
 * @param array $b second user array
 * @return int negative numbers sort $a first, positive sort $b first
 */
function usersort($a, $b) {
	global $usersortfields;

	$aname = "";
	$bname = "";
	if (!empty($usersortfields)) {
		foreach($usersortfields as $ind=>$field) {
			if (isset($a[$field])) $aname .= $a[$field];
			if (isset($b[$field])) $bname .= $b[$field];
		}
	}
	else {
		$aname = $a["lastname"]." ".$a["firstname"];
		$bname = $b["lastname"]." ".$b["firstname"];
	}
	return compareStrings($aname, $bname, true);		// Case-insensitive compare
}

/**
 * sort arrays or strings
 *
 * this function is called by the uasort PHP function to compare two items and tell which should be
 * sorted first.  It uses the language alphabets to create a string that will is used to compare the
 * strings.  For each letter in the strings, the letter's position in the alphabet string is found.
 * Whichever letter comes first in the alphabet string should be sorted first.
 * @param array $a first item
 * @param array $b second item
 * @return int negative numbers sort $a first, positive sort $b first
 */
function itemsort($a, $b) {
	if (isset($a["name"])) $aname = sortable_name_from_name($a["name"]);
	else if (isset($a["names"])) $aname = sortable_name_from_name($a["names"][0][0]);
	else if (is_array($a)) $aname = sortable_name_from_name($a[0]);
	else $aname=$a;
	if (isset($b["name"])) $bname = sortable_name_from_name($b["name"]);
	else if (isset($b["names"])) $bname = sortable_name_from_name($b["names"][0][0]);
	else if (is_array($b)) $bname = sortable_name_from_name($b[0]);
	else $bname=$b;

	$aname = strip_prefix($aname);
	$bname = strip_prefix($bname);
	$result = compareStrings($aname, $bname, true);		// Case-insensitive compare
	return $result;
}

/**
 * sort an array of media items
 *
 */

function mediasort($a, $b) {
	$aKey = "";
	if (!empty($a["TITL"])) $aKey = $a["TITL"];
	else if (!empty($a["titl"])) $aKey = $a["titl"];
	else if (!empty($a["NAME"])) $aKey = $a["NAME"];
	else if (!empty($a["name"])) $aKey = $a["name"];
	else if (!empty($a["FILE"])) $aKey = basename($a["FILE"]);
	else if (!empty($a["file"])) $aKey = basename($a["file"]);

	$bKey = "";
	if (!empty($b["TITL"])) $bKey = $b["TITL"];
	else if (!empty($b["titl"])) $bKey = $b["titl"];
	else if (!empty($b["NAME"])) $bKey = $b["NAME"];
	else if (!empty($b["name"])) $bKey = $b["name"];
	else if (!empty($b["FILE"])) $bKey = basename($b["FILE"]);
	else if (!empty($b["file"])) $bKey = basename($b["file"]);
	return compareStrings($aKey, $bKey, true);		// Case-insensitive compare
}

/**
 * sort a list by the gedcom xref id
 * @param array $a	the first $indi array to sort on
 * @param array $b	the second $indi array to sort on
 * @return int negative numbers sort $a first, positive sort $b first
 */
function idsort($a, $b) {
	if (isset($a["gedcom"])) {
		$ct = preg_match("/0 @(.*)@/", $a["gedcom"], $match);
		if ($ct>0) $aid = $match[1];
	}
	if (isset($b["gedcom"])) {
		$ct = preg_match("/0 @(.*)@/", $b["gedcom"], $match);
		if ($ct>0) $bid = $match[1];
	}
	if (empty($aid) || empty($bid)) return itemsort($a, $b);
	else return stringsort($aid, $bid);
}

//-- comparison function for usort
//-- used for index mode
function lettersort($a, $b) {
	return stringsort($a["letter"], $b["letter"]);
}

function compare_fact_type($arec, $brec) {
	global $factarray, $pgv_lang, $ASC, $IGNORE_YEAR, $IGNORE_FACTS, $DEBUG, $USE_RTL_FUNCTIONS;

	$bef = -1;
	$aft = 1;
	if ($ASC) {
		$bef = 1;
		$aft = -1;
	}

	$ft = preg_match("/1\s(\w+)(.*)/", $arec, $match);
	if ($ft>0) $afact = $match[1];
	else $afact="";
	$afact = trim($afact);

	$ft = preg_match("/1\s(\w+)(.*)/", $brec, $match);
	if ($ft>0) $bfact = $match[1];
	else $bfact="";
	$bfact = trim($bfact);

	//-- array for sorting facts in the correct order
	$factsort = array("BIRT"=>1, "FCOM"=>2, "CHR"=>3, "ADOP"=>4, "BAPM"=>5, "BARM"=>6, "BASM"=>6, "NATU"=>8, "EMIG"=>8, "IMMI"=>8, "MARR"=>7, "DIV"=>8, "RESI"=>8, "OCCU"=>8, "RETI"=>9, "DEAT"=>10, "BURI"=>11, "CREM"=>12, "BAPL"=>51, "ENDL"=>52, "SLGC"=>50, "SLGS"=>53, "CHAN"=>100);
	if (isset($factsort[$afact]) || isset($factsort[$bfact])) {
		if (!isset($factsort[$afact])) {
			if (preg_match("/ASSO/", $arec)>0) $factsort[$afact] = 9;
			else {
				//-- this line causes issues with the sorting algorithm
//				if ($bfact=="DEAT" || $bfact=="BURI") return $bef;
				if ($DEBUG) print " bdeat aft";
				return $aft;
			}
		}
		if (!isset($factsort[$bfact])) {
			if (preg_match("/ASSO/", $brec)>0) $factsort[$bfact] = 9;
			else {
//				if ($afact=="BIRT" || $afact=="CHR") return $aft;
				if ($DEBUG) print " adeat bef";
				return $bef;
			}
		}
		if ($DEBUG) print "factsort ".($factsort[$afact]-$factsort[$bfact]);
		return $factsort[$afact]-$factsort[$bfact];
	}

	//-- group address related data together
	$addr_group = array("ADDR"=>1,"PHON"=>2,"EMAIL"=>3,"FAX"=>4,"WWW"=>5);
	if (isset($addr_group[$afact]) && isset($addr_group[$bfact])) {
		if ($DEBUG) print ($addr_group[$afact]-$addr_group[$bfact]);
		return $addr_group[$afact]-$addr_group[$bfact];
	}
	if (isset($addr_group[$afact]) && !isset($addr_group[$bfact])) {
		if ($DEBUG) print "aft";
		return $aft;
	}
	if (!isset($addr_group[$afact]) && isset($addr_group[$bfact])) {
		if ($DEBUG) print "bef";
		return $bef;
	}

	return false;
}

/**
 * compare two fact records by date
 *
 * Compare facts function is used by the usort PHP function to sort fact baseds on date
 * it parses out the year and if the year is the same, it creates a timestamp based on
 * the current year and the month and day information of the fact
 * @param mixed $a an array with the fact record at index 1 or just a string with the factrecord
 * @param mixed $b an array with the fact record at index 1 or just a string with the factrecord
 * @return int -1 if $a should be sorted first, 0 if they are the same, 1 if $b should be sorted first
 */
function compare_facts($a, $b) {
	global $factarray, $pgv_lang, $ASC, $IGNORE_YEAR, $IGNORE_FACTS, $DEBUG, $USE_RTL_FUNCTIONS;

	if (!isset($ASC)) $ASC = 0;
	if (!isset($IGNORE_YEAR)) $IGNORE_YEAR = 0;
	if (!isset($IGNORE_FACTS)) $IGNORE_FACTS = 0;

	$adate=0;
	$bdate=0;

	$bef = -1;
	$aft = 1;
	if ($ASC) {
		$bef = 1;
		$aft = -1;
	}

	if (is_array($a)) $arec = $a[1];
	else $arec = $a;
	if (is_array($b)) $brec = $b[1];
	else $brec = $b;
	if ($DEBUG) print "\n<br />".substr($arec,0,8)."==".substr($brec,0,8)." ";

	// Float birth to the very top
	if ((preg_match("/1 BIRT/", $arec)>0) && (!preg_match("/1 BIRT/", $brec)>0)) {
		if ($DEBUG) print "birt bef";
		return $bef;
	}
	if ((preg_match("/1 BIRT/", $brec)>0) && (!preg_match("/1 BIRT/", $arec)>0)) {
		if ($DEBUG) print "birt aft";
		return $aft;
	}
	// Sinking "changed" to the very bottom
	if (preg_match("/1 CHAN/", $arec)>0) {
		if ($DEBUG) print "chan aft";
		return $aft;
	}
	if (preg_match("/1 CHAN/", $brec)>0) {
		if ($DEBUG) print "chan bef";
		return $bef;
	}
	// Putting Death before burial or cremation.
	if ((preg_match("/1 DEAT/", $arec)>0) && ((preg_match("/1 BURI/", $brec)>0) || (preg_match("/1 CREM/", $brec)>0))) {
		if ($DEBUG) print "deat bef buri";
		return $bef;
	}
	if ((preg_match("/1 DEAT/", $brec)>0) && ((preg_match("/1 BURI/", $arec)>0) || (preg_match("/1 CREM/", $arec)>0))) {
		if ($DEBUG) print "buri aft deat";
		return $aft;
	}
	// cremation then burial is possible, reverse is not logical.
	if ((preg_match("/1 CREM/", $arec)>0) && (preg_match("/1 BURI/", $brec)>0)) {
		if ($DEBUG) print "crem bef buri";
		return $bef;
	}
	if ((preg_match("/1 CREM/", $brec)>0) && (preg_match("/1 BURI/", $arec)>0)) {
		if ($DEBUG) print "crem aft deat";
		return $aft;
	}

	// Sink Death, burial and cremation to the bottom of the list.
	if ((preg_match("/1 DEAT/", $arec)>0) && ((stristr($arec, "UNKNOWN")) ||
	    (stristr($arec, "DATE Y")) || (!stristr($arec, "DATE")))) {
		if ($DEBUG) print "deat aft";
		return $aft;
	}
	if ((preg_match("/1 DEAT/", $brec)>0) && ((stristr($brec, "UNKNOWN")) ||
	    (stristr($brec, "DATE Y")) || (!stristr($brec, "DATE")))) {
		if ($DEBUG) print "deat bef";
		return $bef;
	}
	if ((preg_match("/1 BURI/", $arec)>0) && ((stristr($arec, "UNKNOWN")) ||
            (stristr($arec, "DATE Y")) || (!stristr($arec, "DATE")))) {
		if ($DEBUG) print "buri aft";
		return $aft;
	}
	if ((preg_match("/1 BURI/", $brec)>0) && ((stristr($brec, "UNKNOWN")) ||
            (stristr($brec, "DATE Y")) || (!stristr($brec, "DATE")))) {
		if ($DEBUG) print "buri bef";
		return $bef;
	}
	if ((preg_match("/1 CREM/", $arec)>0) && ((stristr($arec, "UNKNOWN")) ||
	    (stristr($arec, "DATE Y")) || (!stristr($arec, "DATE")))) {
		if ($DEBUG) print "crem aft";
		return $aft;
	}
	if ((preg_match("/1 CREM/", $brec)>0) && ((stristr($brec, "UNKNOWN")) ||
            (stristr($brec, "DATE Y")) || (!stristr($brec, "DATE")))) {
		if ($DEBUG) print "crem bef";
		return $bef;
	}

	$cta = preg_match("/2 DATE (.*)/", $arec, $match);
	if ($cta>0) $adate = parse_date(trim($match[1]));
	$ctb = preg_match("/2 DATE (.*)/", $brec, $match);
	if ($ctb>0) $bdate = parse_date(trim($match[1]));
	//-- if the date of one fact is missing, or it is missing a year, then sort
	//-- based on fact label
	if ($cta==0 || $ctb==0 || empty($adate[0]["year"]) || empty($bdate[0]["year"])) {
		if (!$IGNORE_FACTS) {
			$res = compare_fact_type($arec, $brec);
			if ($res!==false) return $res;
		}

		//-- check if both had a date
		if($cta<$ctb) {
			if ($DEBUG) print "daft";
			return $aft;
		}
		if($cta>$ctb) {
			if ($DEBUG) print "dbef";
			return $bef;
		}
		//-- neither had a date so sort by fact name
		if(($cta==0)&&($ctb==0)) {
			if (isset($afact)) {
				if ($afact=="EVEN" || $afact=="FACT") {
					$ft = preg_match("/2 TYPE (.*)/", $arec, $match);
					if ($ft>0) $afact = trim($match[1]);
				}
			}
			else $afact = "";
			if (isset($bfact)) {
				if ($bfact=="EVEN" || $bfact=="FACT") {
					$ft = preg_match("/2 TYPE (.*)/", $brec, $match);
					if ($ft>0) $bfact = trim($match[1]);
				}
			}
			else $bfact = "";
			if (isset($factarray[$afact])) $afact = $factarray[$afact];
			else if (isset($pgv_lang[$afact])) $afact = $pgv_lang[$afact];
			if (isset($factarray[$bfact])) $bfact = $factarray[$bfact];
			else if (isset($pgv_lang[$bfact])) $bfact = $pgv_lang[$bfact];
			$c = stringsort($afact, $bfact);
			if ($DEBUG==1) {
				if ($c<0) print "bef"; else print "aft";
			}
			return $c;
		}
	}

	if ($IGNORE_YEAR) {
    // Calculate Current year Gregorian date for Hebrew date
        if ($USE_RTL_FUNCTIONS && isset($adate[0]["ext"]) && strstr($adate[0]["ext"], "#DHEBREW")!==false) $adate = jewishGedcomDateToCurrentGregorian($adate);
		if ($USE_RTL_FUNCTIONS && isset($bdate[0]["ext"]) && strstr($bdate[0]["ext"], "#DHEBREW")!==false) $bdate = jewishGedcomDateToCurrentGregorian($bdate);
	}
	else {
    // Calculate Original year Gregorian date for Hebrew date
    	if ($USE_RTL_FUNCTIONS && isset($adate[0]["ext"]) && strstr($adate[0]["ext"], "#DHEBREW")!==false) $adate = jewishGedcomDateToGregorian($adate);
    	if ($USE_RTL_FUNCTIONS && isset($bdate[0]["ext"]) && strstr($bdate[0]["ext"], "#DHEBREW")!==false) $bdate = jewishGedcomDateToGregorian($bdate);
    }

if ($DEBUG) print $adate[0]["year"]."==".$bdate[0]["year"]." ";
	if ($adate[0]["year"]==$bdate[0]["year"] || $IGNORE_YEAR) {
		// Check month
		$montha = $adate[0]["mon"];
		$monthb = $bdate[0]["mon"];

		if ($montha == $monthb) {
		// Check day
			$newa = $adate[0]["day"]." ".$adate[0]["month"]." ".date("Y");
			$newb = $bdate[0]["day"]." ".$bdate[0]["month"]." ".date("Y");
			$astamp = strtotime($newa);
			$bstamp = strtotime($newb);
			if ($astamp==$bstamp) {
				if ($IGNORE_YEAR && ($adate[0]["year"]!=$bdate[0]["year"])) return ($adate[0]["year"] < $bdate[0]["year"]) ? $aft : $bef;
				$cta = preg_match("/[2-3] TIME (.*)/", $arec, $amatch);
				$ctb = preg_match("/[2-3] TIME (.*)/", $brec, $bmatch);
				//-- check if both had a time
				if($cta<$ctb) return $aft;
				if($cta>$ctb) return $bef;
				//-- neither had a time
				if(($cta==0)&&($ctb==0)) {
					$res = compare_fact_type($arec, $brec);
					if ($res!==false) return $res;
					return 0;
				}
				$atime = trim($amatch[1]);
				$btime = trim($bmatch[1]);
				$astamp = strtotime($newa." ".$atime);
				$bstamp = strtotime($newb." ".$btime);
				if ($astamp==$bstamp) return compare_fact_type($arec, $brec);
			}
			if ($DEBUG) print ($astamp < $bstamp) ? "bef".$bef : "aft".$aft;
			return ($astamp < $bstamp) ? $bef : $aft;
		}
		else return ($montha < $monthb) ? $bef : $aft;
	}
if ($DEBUG) print (($adate[0]["year"] < $bdate[0]["year"]) ? "bef".$bef : "aft".$aft)." ";
	return ($adate[0]["year"] < $bdate[0]["year"]) ? $bef : $aft;
}

/**
 * This function will sort a list of facts
 * It uses an insertion sort algorithm which is a slower algorithm
 * but it insures that all of the items get checked against each other
 * which works better with the way that facts are sorted.
 * @param $array $factlist	the array of facts to sort
 */
function sort_facts(&$factlist) {
	global $DEBUG;
	$count = count($factlist);
	if ($DEBUG==1) {
		for($i=0; $i<$count; $i++) {
			print "[".$i."=>".substr($factlist[$i][1], 0, 6)."]";
		}
	}
	$newfacts = array();
	if ($count==0) return;
	$newfacts[]=$factlist[0];
	//-- insertion sort algorithm
	for($i=1; $i<$count; $i++) {
		if ($DEBUG==1) print "<br /><br />";
		$countj = count($newfacts);

		$small = 0;
		for($j=0; $j<$countj; $j++) {
			$c = compare_facts($factlist[$i], $newfacts[$j]);
			if ($c<0) break;
			else $small = $j;
		}

		if ($small==0 && $j<1) array_unshift($newfacts, $factlist[$i]);
		else if ($c>0) array_push($newfacts, $factlist[$i]);
		else array_splice($newfacts, $small+1, 0, array($factlist[$i]));

		if ($DEBUG==1) {
			print "<br />Small=".$small." C=$c";
			for($k=0; $k<$countj+1; $k++) {
				print "[".$k."=>".substr($newfacts[$k][1], 0, 6)."]";
			}
		}
	}
	$factlist = $newfacts;
}

/**
 * fact date sort
 *
 * compare individuals by a fact date
 */
function compare_date($a, $b) {
	global $sortby;

	$tag = "BIRT";
	if (!empty($sortby)) $tag = $sortby;
	$abirt = get_sub_record(1, "1 $tag", $a["gedcom"]);
	$bbirt = get_sub_record(1, "1 $tag", $b["gedcom"]);
	$c = compare_facts($abirt, $bbirt);
	if ($c==0) return itemsort($a, $b);
	else return $c;
}

function gedcomsort($a, $b) {
	$aname = str2upper($a["title"]);
	$bname = str2upper($b["title"]);

	return stringsort($aname, $bname);
}

// ************************************************* START OF MISCELLANIOUS FUNCTIONS ********************************* //
/**
 * Get relationship between two individuals in the gedcom
 *
 * function to calculate the relationship between two people it uses hueristics based on the
 * individuals birthdate to try and calculate the shortest path between the two individuals
 * it uses a node cache to help speed up calculations when using relationship privacy
 * this cache is indexed using the string "$pid1-$pid2"
 * @param string $pid1 the ID of the first person to compute the relationship from
 * @param string $pid2 the ID of the second person to compute the relatiohip to
 * @param bool $followspouse whether to add spouses to the path
 * @param int $maxlenght the maximim length of path
 * @param bool $ignore_cache enable or disable the relationship cache
 * @param int $path_to_find which path in the relationship to find, 0 is the shortest path, 1 is the next shortest path, etc
 */
function get_relationship($pid1, $pid2, $followspouse=true, $maxlength=0, $ignore_cache=false, $path_to_find=0) {
	global $TIME_LIMIT, $start_time, $pgv_lang, $NODE_CACHE, $NODE_CACHE_LENGTH, $USE_RELATIONSHIP_PRIVACY, $pgv_changes, $GEDCOM;

	$pid1 = strtoupper($pid1);
	$pid2 = strtoupper($pid2);
	if (isset($pgv_changes[$pid2."_".$GEDCOM]) && userCanEdit(getUserName())) $indirec = find_record_in_file($pid2);
	else $indirec = find_person_record($pid2);
	//-- check the cache
	if ($USE_RELATIONSHIP_PRIVACY && !$ignore_cache) {
		if(isset($NODE_CACHE["$pid1-$pid2"])) {
			if ($NODE_CACHE["$pid1-$pid2"]=="NOT FOUND") return false;
			if (($maxlength==0)||(count($NODE_CACHE["$pid1-$pid2"]["path"])-1<=$maxlength)) return $NODE_CACHE["$pid1-$pid2"];
			else return false;
		}
		//-- check the cache for person 2's children
		$famids = array();
		$ct = preg_match_all("/1\sFAMS\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$famids[$i]=$match[$i][1];
		}
		foreach($famids as $indexval => $fam) {
			$famrec = find_family_record($fam);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				$child = $match[$i][1];
				if (!empty($child)){
					if(isset($NODE_CACHE["$pid1-$child"])) {
						if (($maxlength==0)||(count($NODE_CACHE["$pid1-$child"]["path"])+1<=$maxlength)) {
							$node1 = $NODE_CACHE["$pid1-$child"];
							if ($node1!="NOT FOUND") {
								$node1["path"][] = $pid2;
								$node1["pid"] = $pid2;
								$ct = preg_match("/1 SEX F/", $indirec, $match);
								if ($ct>0) $node1["relations"][] = "mother";
								else $node1["relations"][] = "father";
							}
							$NODE_CACHE["$pid1-$pid2"] = $node1;
							if ($node1=="NOT FOUND") return false;
							return $node1;
						}
						else return false;
					}
				}
			}
		}

		if ((!empty($NODE_CACHE_LENGTH))&&($maxlength>0)) {
			if ($NODE_CACHE_LENGTH>=$maxlength) return false;
		}
	}
	//-- end cache checking

	//-- get the birth year of p2 for calculating heuristics
	$birthrec = get_sub_record(1, "1 BIRT", $indirec);
	$byear2 = -1;
	if ($birthrec!==false) {
		$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
		if ($dct>0) $byear2 = $match[1];
	}
	if ($byear2==-1) {
		$numfams = preg_match_all("/1\s*FAMS\s*@(.*)@/", $indirec, $fmatch, PREG_SET_ORDER);
		for($j=0; $j<$numfams; $j++) {
			// Get the family record
			if (isset($pgv_changes[$fmatch[$j][1]."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($fmatch[$j][1]);
			else $famrec = find_family_record($fmatch[$j][1]);

			// Get the set of children
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $cmatch, PREG_SET_ORDER);
			for($i=0; $i<$ct; $i++) {
				// Get each child's record
				if (isset($pgv_changes[$cmatch[$i][1]."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($cmatch[$i][1]);
				else $childrec = find_person_record($cmatch[$i][1]);
				$birthrec = get_sub_record(1, "1 BIRT", $childrec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $bmatch);
					if ($dct>0) $byear2 = $bmatch[1]-25;
				}
			}
		}
	}
	//-- end of approximating birth year

	//-- current path nodes
	$p1nodes = array();
	//-- ids visited
	$visited = array();

	//-- set up first node for person1
	$node1 = array();
	$node1["path"] = array();
	$node1["path"][] = $pid1;
	$node1["length"] = 0;
	$node1["pid"] = $pid1;
	$node1["relations"] = array();
	$node1["relations"][] = "self";
	$p1nodes[] = $node1;

	$visited[$pid1] = true;

	$found = false;
	$count=0;
	while(!$found) {
		//-- the following 2 lines ensure that the user can abort a long relationship calculation
		//-- refer to http://www.php.net/manual/en/features.connection-handling.php for more
		//-- information about why these lines are included
		if (headers_sent()) {
			print " ";
			if ($count%100 == 0) flush();
		}
		$count++;
		$end_time = getmicrotime();
		$exectime = $end_time - $start_time;
		if (($TIME_LIMIT>1)&&($exectime > $TIME_LIMIT-1)) {
			print "<span class=\"error\">".$pgv_lang["timeout_error"]."</span>\n";
			return false;
		}
		if (count($p1nodes)==0) {
			if ($maxlength!=0) {
				if (!isset($NODE_CACHE_LENGTH)) $NODE_CACHE_LENGTH = $maxlength;
				else if ($NODE_CACHE_LENGTH<$maxlength) $NODE_CACHE_LENGTH = $maxlength;
			}
			if (headers_sent()) {
				print "\n<!-- Relationship $pid1-$pid2 NOT FOUND | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
				print_execution_stats();
				print "-->\n";
			}
			$NODE_CACHE["$pid1-$pid2"] = "NOT FOUND";
			return false;
		}
		//-- search the node list for the shortest path length
		$shortest = -1;
		foreach($p1nodes as $index=>$node) {
			if ($shortest == -1) $shortest = $index;
			else {
				$node1 = $p1nodes[$shortest];
				if ($node1["length"] > $node["length"]) $shortest = $index;
			}
		}
		if ($shortest==-1) return false;
		$node = $p1nodes[$shortest];
		if (($maxlength==0)||(count($node["path"])<=$maxlength)) {
			if ($node["pid"]==$pid2) {
			}
			else {
				//-- hueristic values
				$fatherh = 1;
				$motherh = 1;
				$siblingh = 2;
				$spouseh = 2;
				$childh = 3;

				//-- generate heuristic values based of the birthdates of the current node and p2
				if (isset($pgv_changes[$node["pid"]."_".$GEDCOM]) && userCanEdit(getUserName())) $indirec = find_record_in_file($node["pid"]);
				else $indirec = find_person_record($node["pid"]);
				$byear1 = -1;
				$birthrec = get_sub_record(1, "1 BIRT", $indirec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
					if ($dct>0) $byear1 = $match[1];
				}
				if (($byear1!=-1)&&($byear2!=-1)) {
					$yeardiff = $byear1-$byear2;
					if ($yeardiff < -140) {
						$fatherh = 20;
						$motherh = 20;
						$siblingh = 15;
						$spouseh = 15;
						$childh = 1;
					}
					else if ($yeardiff < -100) {
						$fatherh = 15;
						$motherh = 15;
						$siblingh = 10;
						$spouseh = 10;
						$childh = 1;
					}
					else if ($yeardiff < -60) {
						$fatherh = 10;
						$motherh = 10;
						$siblingh = 5;
						$spouseh = 5;
						$childh = 1;
					}
					else if ($yeardiff < -20) {
						$fatherh = 5;
						$motherh = 5;
						$siblingh = 3;
						$spouseh = 3;
						$childh = 1;
					}
					else if ($yeardiff<20) {
						$fatherh = 3;
						$motherh = 3;
						$siblingh = 1;
						$spouseh = 1;
						$childh = 5;
					}
					else if ($yeardiff<60) {
						$fatherh = 1;
						$motherh = 1;
						$siblingh = 5;
						$spouseh = 2;
						$childh = 10;
					}
					else if ($yeardiff<100) {
						$fatherh = 1;
						$motherh = 1;
						$siblingh = 10;
						$spouseh = 3;
						$childh = 15;
					}
					else {
						$fatherh = 1;
						$motherh = 1;
						$siblingh = 15;
						$spouseh = 4;
						$childh = 20;
					}
				}
				//-- check all parents and siblings of this node
				$famids = array();
				$ct = preg_match_all("/1\sFAMC\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
					if (!isset($visited[$match[$i][1]])) $famids[$i]=$match[$i][1];
				}
				foreach($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($fam);
					else $famrec = find_family_record($fam);
					$parents = find_parents_in_record($famrec);
					if ((!empty($parents["HUSB"]))&&(!isset($visited[$parents["HUSB"]]))) {
						$node1 = $node;
						$node1["length"]+=$fatherh;
						$node1["path"][] = $parents["HUSB"];
						$node1["pid"] = $parents["HUSB"];
						$node1["relations"][] = "father";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0) $path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						}
						else $visited[$parents["HUSB"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					if ((!empty($parents["WIFE"]))&&(!isset($visited[$parents["WIFE"]]))) {
						$node1 = $node;
						$node1["length"]+=$motherh;
						$node1["path"][] = $parents["WIFE"];
						$node1["pid"] = $parents["WIFE"];
						$node1["relations"][] = "mother";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0) $path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						}
						else $visited[$parents["WIFE"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$siblingh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "sibling";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$child] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
				//-- check all spouses and children of this node
				$famids = array();
				$ct = preg_match_all("/1\sFAMS\s@(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for($i=0; $i<$ct; $i++) {
//					if (!isset($visited[$match[$i][1]])) $famids[$i]=$match[$i][1];
					$famids[$i]=$match[$i][1];
				}
				foreach($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".$GEDCOM]) && userCanEdit(getUserName())) $famrec = find_record_in_file($fam);
					else $famrec = find_family_record($fam);
					if ($followspouse) {
						$parents = find_parents_in_record($famrec);
						if ((!empty($parents["HUSB"]))&&(!isset($visited[$parents["HUSB"]]))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["HUSB"];
							$node1["pid"] = $parents["HUSB"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$parents["HUSB"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
						if ((!empty($parents["WIFE"]))&&(!isset($visited[$parents["WIFE"]]))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["WIFE"];
							$node1["pid"] = $parents["WIFE"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$parents["WIFE"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$childh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "child";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0) $path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							}
							else $visited[$child] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
			}
		}
		unset($p1nodes[$shortest]);
	} //-- end while loop
	if (headers_sent()) {
		print "\n<!-- Relationship $pid1-$pid2 | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
		print_execution_stats();
		print "-->\n";
	}
	return $resnode;
}

/**
 * write changes
 *
 * this function writes the $pgv_changes back to the <var>$INDEX_DIRECTORY</var>/pgv_changes.php
 * file so that it can be read in and checked to see if records have been updated.  It also stores
 * old records so that they can be undone.
 * @return bool true if successful false if there was an error
 */
function write_changes() {
	global $GEDCOMS, $GEDCOM, $pgv_changes, $INDEX_DIRECTORY, $CONTACT_EMAIL, $LAST_CHANGE_EMAIL;

	if (!isset($LAST_CHANGE_EMAIL)) $LAST_CHANGE_EMAIL = time();
	//-- write the changes file
	$changestext = "<?php\n\$LAST_CHANGE_EMAIL = $LAST_CHANGE_EMAIL;\n\$pgv_changes = array();\n";
	foreach($pgv_changes as $gid=>$changes) {
		if (count($changes)>0) {
			$changestext .= "\$pgv_changes[\"$gid\"] = array();\n";
			foreach($changes as $indexval => $change) {
				$changestext .= "// Start of change record.\n";
				$changestext .= "\$change = array();\n";
				$changestext .= "\$change[\"gid\"] = '".$change["gid"]."';\n";
				$changestext .= "\$change[\"gedcom\"] = '".$change["gedcom"]."';\n";
				$changestext .= "\$change[\"type\"] = '".$change["type"]."';\n";
				$changestext .= "\$change[\"status\"] = '".$change["status"]."';\n";
				$changestext .= "\$change[\"user\"] = '".$change["user"]."';\n";
				$changestext .= "\$change[\"time\"] = '".$change["time"]."';\n";
				if (isset($change["linkpid"])) $changestext .= "\$change[\"linkpid\"] = '".$change["linkpid"]."';\n";
				$changestext .= "\$change[\"undo\"] = '".str_replace("\\\\'", "\\'", preg_replace("/'/", "\\'", $change["undo"]))."';\n";
				$changestext .= "// End of change record.\n";
				$changestext .= "\$pgv_changes[\"$gid\"][] = \$change;\n";
			}
		}
	}
	$changestext .= "\n?".">\n";
	$fp = fopen($INDEX_DIRECTORY."pgv_changes.php", "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open changes file resource.  Unable to complete request.\n";
		return false;
	}
	$fw = fwrite($fp, $changestext);
	if ($fw===false) {
		print "ERROR 7: Unable to write to changes file.\n";
		fclose($fp);
		return false;
	}
	fclose($fp);
 	if (!empty($COMMIT_COMMAND)) {
 		$logline = AddToLog("pgv_changes.php updated by >".getUserName()."<");
 		check_in($logline, "pgv_changes.php", $INDEX_DIRECTORY);
 	}
	return true;
}

/**
 * get theme names
 *
 * function to get the names of all of the themes as an array
 * it searches the themes directory and reads the name from the theme_name variable
 * in the theme.php file.
 * @return array and array of theme names and their corresponding directory
 */
function get_theme_names() {
	$themes = array();
	$d = dir("themes");
	while (false !== ($entry = $d->read())) {
		if ($entry{0}!="." && $entry!="CVS" && !stristr($entry, "svn") && is_dir("themes/$entry") && file_exists("themes/$entry/theme.php")) {
			$theme = array();
			$themefile = implode("", file("themes/$entry/theme.php"));
			$tt = preg_match("/theme_name\s+=\s+\"(.*)\";/", $themefile, $match);
			if ($tt>0) $themename = trim($match[1]);
			else $themename = "themes/$entry";
			$theme["name"] = $themename;
			$theme["dir"] = "themes/$entry/";
			$themes[] = $theme;
		}
	}
	$d->close();
	uasort($themes, "itemsort");
	return $themes;
}

/**
 * format a fact for calendar viewing
 *
 * @param string $factrec the fact record
 * @param string $action tells what type calendar the user is viewing
 * @param string $filter should the fact be filtered by living people etc
 * @param string $pid the gedcom xref id of the record this fact belongs to
 * @param string $filterev "all" to show all events; "bdm" to show only Births, Deaths, Marriages; Event code to show only that event
 * @return string a html text string that can be printed
 */
function get_calendar_fact($factrec, $action, $filterof, $pid, $filterev="all") {
	global $pgv_lang, $factarray, $year, $month, $day, $TEMPLE_CODES, $CALENDAR_FORMAT, $monthtonum, $TEXT_DIRECTION, $SHOW_PEDIGREE_PLACES, $caltype;
	global $CalYear, $currhYear, $USE_RTL_FUNCTIONS;
//	global $currhMonth;

	$Upcoming = false;
	if ($action == "upcoming") {
		$action = "today";
		$Upcoming = true;
	}

	$skipfacts = array("CHAN", "BAPL", "SLGC", "SLGS", "ENDL");
	$BDMfacts = array("BIRT", "DEAT", "MARR");

//	$ft = preg_match("/1\s(_?\w+)\s(.*)/", $factrec, $match);
	$ft = preg_match("/1\s(\w+)(.*)/", $factrec, $match);
	if ($ft>0) $fact = $match[1];
	else return "filter";

	if (in_array($fact, $skipfacts)) return "filter";
// visitor returns in the following for BIRT ??
// why does the visitor get a blank from showFactDetails($fact, $pid) - because he should not see data of live??
// A logged in user in FF sees I92, in IE sees 2 on 21.4

	if ((!showFact($fact, $pid))||(!showFactDetails($fact, $pid)))  return "";
	if (FactViewRestricted($pid, $factrec)) return "";

	$fact = trim($fact);
	$factref = $fact;
	if ($fact=="EVEN" || $fact=="FACT") {
		$ct = preg_match("/2 TYPE (.*)/", $factrec, $tmatch);
		if ($ct>0) {
			$factref = trim($tmatch[1]);
		    if ((!showFact($factref, $pid))||(!showFactDetails($factref, $pid))) return "";
	    }
	}

	// Use current year for age in dayview
	if ($action == "today"){
		$yearnow = getdate();
		$yearnow = $yearnow["year"];
	}
	else	{
		$yearnow = $year;
	}

	$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $factrec, $match);
	if ($hct>0 && $USE_RTL_FUNCTIONS)
		if ($action == "today") $yearnow = $currhYear;
		else $yearnow = $CalYear;

	$text = "";

	// See whether this Fact should be filtered out or not
	$Filtered = false;
	if (in_array($fact, $skipfacts) or in_array($factref, $skipfacts)) $Filtered = true;
	if ($filterev=="bdm") {
		if (!in_array($fact, $BDMfacts) and !in_array($factref, $BDMfacts)) $Filtered = true;
	}
	if ($filterev!="all" and $filterev!="bdm") {
		if ($fact!=$filterev and $factref!=$filterev) $Filtered = true;
	}

	if (!$Filtered) {
		if ($fact=="EVEN" || $fact=="FACT") {
			if ($ct>0) {
				if (isset($factarray["$factref"])) $text .= $factarray["$factref"];
				else $text .= $factref;
			}
			else $text .= $factarray[$fact];
		}
		else {
			if (isset($factarray[$fact])) $text .= $factarray[$fact];
			else $text .= $fact;
		}
//		if ($filterev!="all" && $filterev!=$fact && $filterev!=$factref) return "filter";

		if ($text!="") $text=PrintReady($text);

		$ct = preg_match("/\d DATE(.*)/", $factrec, $match);
		if ($ct>0) {
			$text .= " - <span class=\"date\">".get_date_url($match[1])."</span>";
//			$yt = preg_match("/ (\d\d\d\d)/", $match[1], $ymatch);
			$yt = preg_match("/ (\d\d\d\d|\d\d\d)/", $match[1], $ymatch);
			if ($yt>0) {

				$hct = preg_match("/2 DATE.*(@#DHEBREW@)/", $match[1], $hmatch);
	            if ($hct>0 && $USE_RTL_FUNCTIONS && $action=='today')

// should perhaps use the month of the fact to find if should use $currhYear or $currhYear+1 or $currhYear-1 to calculate age
// use $currhMonth and the fact month for this

                   $age = $currhYear - $ymatch[1];
				else
				   $age = $yearnow - $ymatch[1];
				$yt2 = preg_match("/(...) (\d\d\d\d|\d\d\d)/", $match[1], $bmatch);
				if ($yt2>0) {
					if (isset($monthtonum[strtolower(trim($bmatch[1]))])) {
						$emonth = $monthtonum[strtolower(trim($bmatch[1]))];
						if (!$Upcoming && ($emonth<$monthtonum[strtolower($month)])) $age--;
						$bt = preg_match("/(\d+) ... (\d\d\d\d|\d\d\d)/", $match[1], $bmatch);
						if ($bt>0) {
							$edate = trim($bmatch[1]);
							if (!$Upcoming && ($edate<$day)) $age--;
						}
					}
				}
				$yt3 = preg_match("/(.+) ... (\d\d\d\d|\d\d\d)/", $match[1], $bmatch);
				if ($yt3>0) {
					if (!$Upcoming && ($bmatch[1]>$day)) $age--;
				}
				if (($filterof=="recent")&&($age>100)) return "filter";
				// Limit facts to before the given year in monthview
				if (($age<0) && ($action == "calendar")) return "filter";
				if ($action!='year'){
					$text .= " (" . str_replace("#year_var#", convert_number($age), $pgv_lang["year_anniversary"]).")";
				}
 				if($TEXT_DIRECTION == "rtl"){
 					$text .= "&lrm;";
 				}
			}
			if (($action=='today')||($action=='year')) {
				// -- find place for each fact
				if ($SHOW_PEDIGREE_PLACES>0) {
					$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
					if ($ct>0) {
						$text .=($action=='today'?"<br />":" ");
						$plevels = preg_split("/,/", $match[1]);
						for($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
							if (!empty($plevels[$plevel])) {
								if ($plevel>0) $text .=", ";
								$text .= PrintReady($plevels[$plevel]);
							}
						}
					}
				}

				// -- find temple code for lds facts
				$ct = preg_match("/2 TEMP (.*)/", $factrec, $match);
				if ($ct>0) {
					$tcode = $match[1];
					$tcode = trim($tcode);
					if (array_key_exists($tcode, $TEMPLE_CODES)) $text .= "<br />".$pgv_lang["temple"].": ".$TEMPLE_CODES[$tcode];
					else $text .= "<br />".$pgv_lang["temple_code"].$tcode;
				}
			}
		}
		$text .= "<br />";
	}
	if ($text=="") return "filter";

	return $text;
}

//-- this function will convert a digit number to a number in a different language
function convert_number($num) {
	global $pgv_lang, $LANGUAGE;

	if ($LANGUAGE == "chinese") {
		$numstr = "$num";
		$zhnum = "";
		//-- currently limited to numbers <10000
		if (strlen($numstr)>4) return $numstr;

		$ln = strlen($numstr);
		$numstr = strrev($numstr);
		for($i=0; $i<$ln; $i++) {
			if (($i==1)&&($numstr{$i}!="0")) $zhnum = $pgv_lang["10"].$zhnum;
			if (($i==2)&&($numstr{$i}!="0")) $zhnum = $pgv_lang["100"].$zhnum;
			if (($i==3)&&($numstr{$i}!="0")) $zhnum = $pgv_lang["1000"].$zhnum;
			if (($i!=1)||($numstr{$i}!=1)) $zhnum = $pgv_lang[$numstr{$i}].$zhnum;
		}
		return $zhnum;
	}
	return $num;
}

/**
 * decode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to decode the filename
 * before it can be used on the filesystem
 */
function filename_decode($filename) {
	global $WIN32;
	if ($WIN32) return utf8_decode($filename);
	else return $filename;
}

/**
 * encode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to encode the filename
 * before it can be used in PGV
 */
function filename_encode($filename) {
	global $WIN32;
	if ($WIN32) return utf8_encode($filename);
	else return $filename;
}


/*
 * - Function is not used anywhere so it is commented out to save memory
//-- This function changes the used gedcom connected to a language
function change_gedcom_per_language($new_gedcom_name,$new_language_name)
{
  global $QUERY_STRING;
  global $PHP_SELF;

  $QUERY_STRING = preg_replace("/&amp;/", "&", $QUERY_STRING);
  $QUERY_STRING = preg_replace("/&&/", "&", $QUERY_STRING);
  $terms = preg_split("/&/", $QUERY_STRING);
  $vars = "";
  for ($i=0; $i<count($terms); $i++)
  {
	if (substr($terms[$i],0,7) == "gedcom=")$terms[$i]="";
	if ((!empty($terms[$i]))&&(strstr($terms[$i], "changelanguage")===false)&&(strpos($terms[$i], "NEWLANGUAGE")===false))
	{
	  $vars .= $terms[$i]."&";
	}
  }
  $QUERY_STRING = $vars;
  if (empty($QUERY_STRING))$QUERY_STRING = "GEDCOM=".$new_gedcom_name; else $QUERY_STRING = $QUERY_STRING . "&gedcom=".$new_gedcom_name;
  $QUERY_STRING = preg_replace("/&&/", "&", $QUERY_STRING);
  $_SESSION["GEDCOM"] = "GEDCOM=".$new_gedcom_name;
  $_SESSION['CLANGUAGE'] = $new_language_name;
  header("Location: ".$PHP_SELF."?".$QUERY_STRING);
  exit;
}
*/

function getAlphabet(){
	global $ALPHABET_upper, $ALPHABET_lower, $LANGUAGE;
	global $alphabet, $alphabet_lower, $alphabet_upper, $alphabet_lang;

	//-- setup the language alphabet string
	if (!isset($alphabet_lang) || $alphabet_lang!=$LANGUAGE) {
		$alphabet = "0123456789".$ALPHABET_upper[$LANGUAGE].$ALPHABET_lower[$LANGUAGE];
		$alphabet_lower = "0123456789".$ALPHABET_lower[$LANGUAGE];
		$alphabet_upper = "0123456789".$ALPHABET_upper[$LANGUAGE];
		foreach ($ALPHABET_upper as $l => $upper){
			if ($l <> $LANGUAGE) {
				$alphabet .= $ALPHABET_upper[$l];
				$alphabet_upper .= $ALPHABET_upper[$l];
				$alphabet .= $ALPHABET_lower[$l];
				$alphabet_lower .= $ALPHABET_lower[$l];
			}
		}
		$alphabet_lang = $LANGUAGE;
	}
	return $alphabet;
}

/**
 * get a list of the reports in the reports directory
 *
 * When $force is false, the function will first try to read the reports list from the$INDEX_DIRECTORY."/reports.dat"
 * data file.  Otherwise the function will parse the report xml files and get the titles.
 * @param boolean $force	force the code to look in the directory and parse the files again
 * @return array 	The array of the found reports with indexes [title] [file]
 */
function get_report_list($force=false) {
	global $INDEX_DIRECTORY, $report_array, $vars, $xml_parser, $elementHandler, $LANGUAGE;

	$files = array();
	if (!$force) {
		//-- check if the report files have been cached
		if (file_exists($INDEX_DIRECTORY."/reports.dat")) {
			$reportdat = "";
			$fp = fopen($INDEX_DIRECTORY."/reports.dat", "r");
			while ($data = fread($fp, 4096)) {
				$reportdat .= $data;
			}
			fclose($fp);
			$files = unserialize($reportdat);
			foreach($files as $indexval => $file) {
				if (isset($file["title"][$LANGUAGE]) && (strlen($file["title"][$LANGUAGE])>1)) return $files;
			}
		}
	}

	//-- find all of the reports in the reports directory
	$d = dir("reports");
	while (false !== ($entry = $d->read())) {
		if (($entry{0}!=".") && ($entry!="CVS") && (preg_match('/\.xml$/i', $entry)>0)) {
			if (!isset($files[$entry]["file"])) $files[$entry]["file"] = "reports/".$entry;
		}
	}
	$d->close();

	require_once("includes/reportheader.php");
	$report_array = array();
	if (!function_exists("xml_parser_create")) return $report_array;
	foreach($files as $file=>$r) {
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		if (file_exists($r["file"])) {
			//-- open the file
			if (!($fp = fopen($r["file"], "r"))) {
			   die("could not open XML input");
			}
			//-- read the file and parse it 4kb at a time
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
				}
			}
			fclose($fp);
			xml_parser_free($xml_parser);
			if (isset($report_array["title"]) && isset($report_array["access"]) && isset($report_array["icon"])) {
				$files[$file]["title"][$LANGUAGE] = $report_array["title"];
				$files[$file]["access"] = $report_array["access"];
				$files[$file]["icon"] = $report_array["icon"];
			}
		}
	}

	$fp = @fopen($INDEX_DIRECTORY."/reports.dat", "w");
	@fwrite($fp, serialize($files));
	@fclose($fp);
	$logline = AddToLog("reports.dat updated by >".getUserName()."<");
 	if (!empty($COMMIT_COMMAND)) check_in($logline, "reports.dat", $INDEX_DIRECTORY);

	return $files;
}

/**
 * clean up user submitted input before submitting it to the SQL query
 *
 * This function will take user submitted input string and remove any special characters
 * before they are submitted to the SQL query.
 * Examples of invalid characters are _ & ?
 * @param string $pid	The string to cleanup
 * @return string	The cleaned up string
 */
function clean_input($pid) {
	$pid = preg_replace("/[%?_]/", "", trim($pid));
	return $pid;
}

/**
 * remove any custom PGV tags from the given gedcom record
 * custom tags include _PGVU and _THUM
 * @param string $gedrec	the raw gedcom record
 * @return string		the updated gedcom record
 */
function remove_custom_tags($gedrec, $remove="no") {
	if ($remove=="yes") {
		//-- remove _PGVU
		$gedrec = preg_replace("/\d _PGVU .*/", "", $gedrec);
		//-- remove _THUM
		$gedrec = preg_replace("/\d _THUM .*/", "", $gedrec);
	}
	//-- cleanup so there are not any empty lines
	$gedrec = preg_replace(array("/(\r\n)+/", "/\r+/", "/\n+/"), array("\r\n", "\r", "\n"), $gedrec);
	//-- make downloaded file DOS formatted
	$gedrec = preg_replace("/([^\r])\n/", "$1\n", $gedrec);
	return $gedrec;
}

function getfilesize($bytes) {
   if ($bytes >= 1099511627776) {
       $return = round($bytes / 1024 / 1024 / 1024 / 1024, 2);
       $suffix = "TB";
   } elseif ($bytes >= 1073741824) {
       $return = round($bytes / 1024 / 1024 / 1024, 2);
       $suffix = "GB";
   } elseif ($bytes >= 1048576) {
       $return = round($bytes / 1024 / 1024, 2);
       $suffix = "MB";
   } elseif ($bytes >= 1024) {
       $return = round($bytes / 1024, 2);
       $suffix = "KB";
   } else {
       $return = $bytes;
       $suffix = "B";
   }
   /*if ($return == 1) {
       $return .= " " . $suffix;
   } else {
       $return .= " " . $suffix . "s";
   }*/
   $return .= " " . $suffix;
   return $return;
}

/**
 * split multi-ged keys and return either key or gedcom
 *
 * @param string $key		the multi-ged key to be split
 * @param string $type		either "id" or "ged", depending on what must be returned
 * @return string			either the key or the gedcom name
 */
function splitkey($key, $type) {
	$p1 = strpos($key,"[");
	$id = substr($key,0,$p1);
	if ($type == "id") return $id;
	$p2 = strpos($key,"]");
	$ged = substr($key,$p1+1,$p2-$p1-1);
	if ($ged>=1) get_gedcom_from_id($ged);
	return $ged;
}

/**
 * array merge function for PGV
 * the PHP array_merge function will reindex all numerical indexes
 * This function should only be used for associative arrays
 * @param array $array1
 * @param array $array2
 */
function pgv_array_merge($array1, $array2) {
	foreach($array2 as $key=>$value) {
		$array1[$key] = $value;
	}
	return $array1;
}

/**
 * function to build an URL querystring from GET or POST variables
 * @return string
 */
function get_query_string() {
	$qstring = "";
	if (!empty($_GET)) {
		foreach($_GET as $key => $value) {
			if($key != "view") {
				$qstring .= $key."=".$value."&amp;";
			}
		}
	}
	else {
		if (!empty($_POST)) {
			foreach($_POST as $key => $value) {
				if($key != "view") {
					$qstring .= $key."=".$value."&amp;";
				}
			}
		}
	}
	return $qstring;
}

//This function works with a specified generation limit.  It will completely fill
//the pdf witout regard to if a known person exists in each generation.
//ToDo: If a known individual is found in a generation, add prior empty positions
//and add remaining empty spots automatically.
function add_ancestors($pid, $children=false, $generations=-1, $show_empty=false) {
	global $list, $indilist, $genlist;
	$total_num_skipped = 0;
	$skipped_gen = 0;
	$num_skipped = 0;
	$genlist = array($pid);
	$list[$pid]["generation"] = 1;
	while(count($genlist)>0) {
		$id = array_shift($genlist);
		$famids = find_family_ids($id);
		if (count($famids)>0) {
			if ($show_empty){
				for ($i=0;$i<$num_skipped;$i++){
					$list["empty" . $total_num_skipped] = array();
					$list["empty" . $total_num_skipped]["generation"] = $list[$id]["generation"]+1;
					$list["empty" . $total_num_skipped]["gedcom"] = "";
					array_push($genlist, "empty" . $total_num_skipped);
					$total_num_skipped++;
				}
			}
			$num_skipped = 0;
			foreach($famids as $indexval => $famid) {
				$parents = find_parents($famid);
				if (!empty($parents["HUSB"])) {
					find_person_record($parents["HUSB"]);
					$list[$parents["HUSB"]] = $indilist[$parents["HUSB"]];
					$list[$parents["HUSB"]]["generation"] = $list[$id]["generation"]+1;
				}
				else if ($show_empty) {
					$list["empty" . $total_num_skipped] = array("empty");
					$list["empty" . $total_num_skipped]["generation"] = $list[$id]["generation"]+1;
					$list["empty" . $total_num_skipped]["gedcom"] = "";
				}
				if (!empty($parents["WIFE"])) {
					find_person_record($parents["WIFE"]);
					$list[$parents["WIFE"]] = $indilist[$parents["WIFE"]];
					$list[$parents["WIFE"]]["generation"] = $list[$id]["generation"]+1;
				}
				else if ($show_empty) {
					$list["empty" . $total_num_skipped] = array("empty");
					$list["empty" . $total_num_skipped]["generation"] = $list[$id]["generation"]+1;
					$list["empty" . $total_num_skipped]["gedcom"] = "";
				}
				if ($generations == -1 || $list[$id]["generation"]+1 < $generations) {
					$skipped_gen = $list[$id]["generation"]+1;
					if (!empty($parents["HUSB"])) array_push($genlist, $parents["HUSB"]);
					else if ($show_empty) array_push($genlist, "empty" . $total_num_skipped);
					if (!empty($parents["WIFE"])) array_push($genlist, $parents["WIFE"]);
					else if ($show_empty) array_push($genlist, "empty" . $total_num_skipped);
				}
				$total_num_skipped++;
				if ($children) {
					$famrec = find_family_record($famid);
					if ($famrec) {
						$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
						for($i=0; $i<$num; $i++) {
							find_person_record($smatch[$i][1]);
							$list[$smatch[$i][1]] = $indilist[$smatch[$i][1]];
							if (isset($list[$id]["generation"])) $list[$smatch[$i][1]]["generation"] = $list[$id]["generation"];
							else $list[$smatch[$i][1]]["generation"] = 1;
						}
					}
				}
			}
		}
		else if ($show_empty) {
			if ($skipped_gen > $list[$id]["generation"]){
				$list["empty" . $total_num_skipped] = array();
				$list["empty" . $total_num_skipped]["generation"] = $list[$id]["generation"]+1;
				$list["empty" . $total_num_skipped]["gedcom"] = "";
				$total_num_skipped++;
				$list["empty" . $total_num_skipped] = array();
				$list["empty" . $total_num_skipped]["generation"] = $list[$id]["generation"]+1;
				$list["empty" . $total_num_skipped]["gedcom"] = "";
				array_push($genlist, "empty" . ($total_num_skipped - 1));
				array_push($genlist, "empty" . $total_num_skipped);
				$total_num_skipped++;
			}
			else $num_skipped += 2;
		}

	}
}

//--- copied from reportpdf.php
function add_descendancy($pid, $parents=false, $generations=-1) {
	global $list, $indilist;

	if (!isset($list[$pid])) {
		$indirec = find_person_record($pid);
		if (!empty($indirec)) $list[$pid] = $indilist[$pid];
		else return;
	}
	if (!isset($list[$pid]["generation"])) {
		$list[$pid]["generation"] = 0;
	}
	$famids = find_sfamily_ids($pid);
	if (count($famids)>0) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid);
			if ($famrec) {
				if ($parents) {
					$parents = find_parents_in_record($famrec);
					if (!empty($parents["HUSB"])) {
						find_person_record($parents["HUSB"]);
						$list[$parents["HUSB"]] = $indilist[$parents["HUSB"]];
						if (isset($list[$pid]["generation"])) $list[$parents["HUSB"]]["generation"] = $list[$pid]["generation"]-1;
						else $list[$parents["HUSB"]]["generation"] = 1;
					}
					if (!empty($parents["WIFE"])) {
						find_person_record($parents["WIFE"]);
						$list[$parents["WIFE"]] = $indilist[$parents["WIFE"]];
						if (isset($list[$pid]["generation"])) $list[$parents["WIFE"]]["generation"] = $list[$pid]["generation"]-1;
						else $list[$parents["WIFE"]]["generation"] = 1;
					}
				}
				$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
				for($i=0; $i<$num; $i++) {
					$indirec = find_person_record($smatch[$i][1]);
					if (!empty($indirec)) {
						$list[$smatch[$i][1]] = $indilist[$smatch[$i][1]];
						if (isset($list[$pid]["generation"])) $list[$smatch[$i][1]]["generation"] = $list[$pid]["generation"]+1;
						else $list[$smatch[$i][1]]["generation"] = 2;
					}
				}
				if($generations == -1 || $list[$pid]["generation"]+1 < $generations)
				{
					for($i=0; $i<$num; $i++) {
						add_descendancy($smatch[$i][1], $parents, $generations);	// recurse on the childs family
					}
				}
			}
		}
	}
}

/**
 * check if the maximum number of page views per hour for a session has been exeeded.
 */
function CheckPageViews() {
	global $MAX_VIEWS, $MAX_VIEW_TIME, $pgv_lang;

	if ($MAX_VIEW_TIME == 0) return;

	if ((!isset($_SESSION["pageviews"])) || (time() - $_SESSION["pageviews"]["time"] > $MAX_VIEW_TIME)) {
		if (isset($_SESSION["pageviews"])) {
			$str = "Max pageview counter reset: max reached was ".$_SESSION["pageviews"]["number"];
			AddToLog($str);
		}
		$_SESSION["pageviews"]["time"] = time();
		$_SESSION["pageviews"]["number"] = 0;
	}

	$_SESSION["pageviews"]["number"]++;

	if ($_SESSION["pageviews"]["number"] > $MAX_VIEWS) {
		$time = time() - $_SESSION["pageviews"]["time"];
		print $pgv_lang["maxviews_exceeded"];
		$str = "Maximum number of pageviews exceeded after ".$time." seconds.";
		AddToLog($str);
		exit;
	}
	return;
}

/**
 * get the next available xref
 * calculates the next available XREF id for the given type of record
 * @param string $type	the type of record, defaults to 'INDI'
 * @return string
 */
function get_new_xref($type='INDI') {
	global $fcontents, $SOURCE_ID_PREFIX, $REPO_ID_PREFIX, $pgv_changes, $GEDCOM, $TBLPREFIX, $GEDCOMS;
	global $MEDIA_ID_PREFIX, $FAM_ID_PREFIX, $GEDCOM_ID_PREFIX, $FILE, $DBCONN, $MAX_IDS;

	//-- during online updates $FILE comes through as an array for some odd reason
	if (!empty($FILE) && !is_array($FILE)) {
		//print_r($FILE);
		$gedid = $GEDCOMS[$FILE]["id"];
	}
	else $gedid = $GEDCOMS[$GEDCOM]["id"];

	$num = null;
	//-- check if an id is stored in MAX_IDS used mainly during the import
	//-- the number stored in the max_id is the next number to use... no need to increment it
	if (!empty($MAX_IDS)&& isset($MAX_IDS[$type])) {
		$num = 1;
		$num = $MAX_IDS[$type];
		$MAX_IDS[$type] = $num+1;
	}
	else {
		//-- check for the id in the nextid table
		$sql = "SELECT * FROM ".$TBLPREFIX."nextid WHERE ni_type='".$DBCONN->escape($type)."' AND ni_gedfile='".$DBCONN->escape($gedid)."'";
		$res =& dbquery($sql);
		if ($res->numRows() > 0) {
			$row = $res->fetchRow();
			$num = $row['ni_id'];
		}
		//-- the id was not found in the table so try and find it in the file
		if (is_null($num) && !empty($fcontents)) {
			$ct = preg_match_all("/0 @(.*)@ $type/", $fcontents, $match, PREG_SET_ORDER);
			$num = 0;
			for($i=0; $i<$ct; $i++) {
				$ckey = $match[$i][1];
				$bt = preg_match("/(\d+)/", $ckey, $bmatch);
				if ($bt>0) {
					$bnum = trim($bmatch[1]);
					if ($num < $bnum) $num = $bnum;
				}
			}
			$num++;
		}
		//-- type wasn't found in database or in file so make a new one
		if (is_null($num)) {
			$num = 1;
			$sql = "INSERT INTO ".$TBLPREFIX."nextid VALUES('".$DBCONN->escape($num+1)."', '".$DBCONN->escape($type)."', '".$gedid."')";
			$res = dbquery($sql);
		}
	}

	// $prefix = $type{0};
	if ($type == "INDI") $prefix = $GEDCOM_ID_PREFIX;
	else if ($type == "FAM") $prefix = $FAM_ID_PREFIX;
	else if ($type == "OBJE") $prefix = $MEDIA_ID_PREFIX;
	else if ($type == "SOUR") $prefix = $SOURCE_ID_PREFIX;
	else if ($type == "REPO") $prefix = $REPO_ID_PREFIX;
	else $prefix = $type{0};

	//-- the key is the prefix and the number
	$key = $prefix.$num;

	//-- during the import we won't update the database at this time so return now
	if (isset($MAX_IDS[$type])) return $key;

	//-- make sure this number has not already been used by an
	//- item awaiting approval
	while(isset($pgv_changes[$key."_".$GEDCOM])) {
		$num++;
		$key = $prefix.$num;
	}
	$num++;
	//-- update the next id number in the DB table
	$sql = "UPDATE ".$TBLPREFIX."nextid SET ni_id='".$DBCONN->escape($num)."' WHERE ni_type='".$DBCONN->escape($type)."' AND ni_gedfile='".$DBCONN->escape($gedid)."'";
	$res = dbquery($sql);
	return $key;
}

/**
 * check if the given string has UTF-8 characters
 *
 */
function has_utf8($string) {
	$len = strlen($string);
	for($i=0; $i<$len; $i++) {
		$letter = substr($string, $i, 1);
		$ord = ord($letter);
		if ($ord==95 || $ord>=195) return true;
	}
	return false;
}

/**
 * Determine the type of ID
 * NOTE: Be careful when using this function as not all GEDCOMS have ID
 * prefixes.  Many GEDCOMS just use numbers like 100, 101, etc without
 * the I, F, etc prefixes.
 * @param string $id
 */
function id_type($id) {
	global $SOURCE_ID_PREFIX, $REPO_ID_PREFIX, $MEDIA_ID_PREFIX, $FAM_ID_PREFIX, $GEDCOM_ID_PREFIX;

	// NOTE: Set length for the ID's
	$indi_length = strlen($GEDCOM_ID_PREFIX);
	$fam_length = strlen($FAM_ID_PREFIX);
	$source_length = strlen($SOURCE_ID_PREFIX);
	$repo_length = strlen($REPO_ID_PREFIX);
	$media_length = strlen($MEDIA_ID_PREFIX);

	// NOTE: Check for individual ID
	if (substr($id, 0, $indi_length) == $GEDCOM_ID_PREFIX) return "INDI";
	else if (substr($id, 0, $fam_length) == $FAM_ID_PREFIX) return "FAM";
	else if (substr($id, 0, $source_length) == $SOURCE_ID_PREFIX) return "SOUR";
	else if (substr($id, 0, $repo_length) == $REPO_ID_PREFIX) return "REPO";
	else if (substr($id, 0, $media_length) == $MEDIA_ID_PREFIX) return "OBJE";
	else return "";
}

//-- only declare this function when it is necessary
if (!empty($COMMIT_COMMAND)) {
/**
 * check file in
 * @param string $logline	Log message
 * @param string $filename	Filename
 * @param string $dirname	Directory
 * @param boolean $bInsert	Insert Log message
 * @return boolean			whether the file was checked in
 */
function check_in($logline, $filename, $dirname, $bInsert = false) {
	global $COMMIT_COMMAND;
	$bRetSts = false;
        if(! empty($COMMIT_COMMAND))
        {
        	$cwd = getcwd();
			if(! empty($dirname))
				chdir($dirname);
        	$cmdline = $COMMIT_COMMAND." commit -m \"".$logline."\" ".$filename;
        	$output = "";
        	$retval = "";
	        exec($cmdline, $output, $retval);
        	if(! empty($output))
	        {
		        if($bInsert)
			        AddToChangeLog($logline);
        		$outputstring = implode('\n', $output);
	        	AddToChangeLog("System Output :".$outputstring.", Return Value :".$retval);
				$bRetSts = true;
        	}
			if(! empty($dirname))
				chdir($cwd);
        }
	return $bRetSts;
}
}

/**
 *		Load language variables
 *		Set language-dependent global variables
 *
 *		This function loads the variables for the language, as specified by the first
 *		input parameter. It also loads any existing language-specific functions such
 *		special date handling for Finnish and Turkish.
 *
 *		If the forceLoad parameter is true, English will be loaded first, followed by
 *		the desired language file.
 *
 */
function loadLanguage($desiredLanguage="english", $forceLoad=false) {
	global $LANGUAGE, $pgv_language, $lang_short_cut, $pgv_lang;
	global $TEXT_DIRECTION, $TEXT_DIRECTION_array;
	global $DATE_FORMAT, $DATE_FORMAT_array;
	global $TIME_FORMAT, $TIME_FORMAT_array;
	global $WEEK_START, $WEEK_START_array;
	global $NAME_REVERSE, $NAME_REVERSE_array;
	global $MULTI_LETTER_ALPHABET, $digraph, $trigraph, $quadgraph, $digraphAll, $trigraphAll, $quadgraphAll;
	global $DICTIONARY_SORT, $UCDiacritWhole, $UCDiacritStrip, $UCDiacritOrder, $LCDiacritWhole, $LCDiacritStrip, $LCDiacritOrder;
	global $unknownNN, $unknownPN;

	if (!isset($pgv_language[$desiredLanguage])) $desiredLanguage = "english";
	$result = false;
	if ($forceLoad) {
		$LANGUAGE = "english";
		require ($pgv_language[$LANGUAGE]);			// Load English

		$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
		$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
		$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
		$WEEK_START		= $WEEK_START_array[$LANGUAGE];
		$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

		// Load functions that are specific to the active language
		@include_once("./includes/extras/functions.".$lang_short_cut[$LANGUAGE].".php");

		$result = true;
	}

	if ($desiredLanguage!=$LANGUAGE && file_exists($pgv_language[$desiredLanguage])) {
		$LANGUAGE = $desiredLanguage;
		include($pgv_language[$LANGUAGE]);		// Load the requested language

		$TEXT_DIRECTION = $TEXT_DIRECTION_array[$LANGUAGE];
		$DATE_FORMAT	= $DATE_FORMAT_array[$LANGUAGE];
		$TIME_FORMAT	= $TIME_FORMAT_array[$LANGUAGE];
		$WEEK_START		= $WEEK_START_array[$LANGUAGE];
		$NAME_REVERSE	= $NAME_REVERSE_array[$LANGUAGE];

		// Load functions that are specific to the active language
		@include_once("./includes/extras/functions.".$lang_short_cut[$LANGUAGE].".php");

		$result = true;
	}

/**
 *		Build the tables of multi-character sequences that must be considered as a
 *		single character when sorting lists of names and titles.
 *			Reference http://en.wikipedia.org/wiki/Hungarian_alphabet
 *			Reference http://en.wikipedia.org/wiki/Alphabets_derived_from_the_Latin
 */
	$digraph = array();
	$trigraph = array();
	$quadgraph = array();
	if (!isset($MULTI_LETTER_ALPHABET[$LANGUAGE])) $MULTI_LETTER_ALPHABET[$LANGUAGE] = "";
	if ($MULTI_LETTER_ALPHABET[$LANGUAGE]!="") {
		$myList = str2upper($MULTI_LETTER_ALPHABET[$LANGUAGE]);
		$myList = str_replace(array(";", ","), " ", $myList);
		$myList = preg_replace("/\s\s+/", " ", $myList);
		$myList = trim($myList);
		$wholeList = explode(" ", $myList);
		$sortValue = array();
		foreach ($wholeList as $letter) {
			$first = substr($letter, 0, 1);
			if ($letter=="CH") $first = "H";	// This one doesn't follow the rule
			if (!isset($sortValue[$first])) $sortValue[$first] = 0;
			$sortValue[$first] ++;
			if (strlen($letter)==2) $digraph[$letter] = $sortValue[$first];
			if (strlen($letter)==3) $trigraph[$letter] = $sortValue[$first];
			if (strlen($letter)==4) $quadgraph[$letter] = $sortValue[$first];
		}
		$MULTI_LETTER_ALPHABET[$LANGUAGE] = " ".$myList." ";
	}

	$digraphAll = array();
	$trigraphAll = array();
	$quadgraphAll = array();
	$MULTI_LETTER_ALPHABET["all"] = "";
	foreach ($MULTI_LETTER_ALPHABET as $lang => $letters) {
		if ($lang!="all") $MULTI_LETTER_ALPHABET["all"] .= $letters." ";
	}
	$MULTI_LETTER_ALPHABET["all"] = str2upper($MULTI_LETTER_ALPHABET["all"]);
	$MULTI_LETTER_ALPHABET["all"] = str_replace(array(";", ","), " ", $MULTI_LETTER_ALPHABET["all"]);
	$MULTI_LETTER_ALPHABET["all"] = preg_replace("/\s\s+/", " ", $MULTI_LETTER_ALPHABET["all"]);
	$wholeList = explode(" ", $MULTI_LETTER_ALPHABET["all"]);
	$sortValue = array();
	foreach ($wholeList as $letter) {
		$first = substr($letter, 0, 1);
		if ($letter=="CH") $first = "H";	// This one doesn't follow the rule
		if (!isset($sortValue[$first])) $sortValue[$first] = 0;
		$sortValue[$first] ++;
		if (strlen($letter)==2) $digraphAll[$letter] = $sortValue[$first];
		if (strlen($letter)==3) $trigraphAll[$letter] = $sortValue[$first];
		if (strlen($letter)==4) $quadgraphAll[$letter] = $sortValue[$first];
	}
	$MULTI_LETTER_ALPHABET["all"] = " ".trim($MULTI_LETTER_ALPHABET["all"])." ";


/**
 *		Build the tables required for the Dictionary sort
 *
 *		A Dictionary sort is one where all letters with diacritics are considered to be
 *		identical to the base letter (without the mark).  Diacritics become important
 *		only when the two strings (without marks) are identical.
 *
 *		There are two sets of tables, one for the Upper Case version of a UTF8 character
 *		and the other for the lower-case version.  The two tables are not necessarily
 *		identical.  For example, the Turkish dotless i doesn't exist in the Upper case
 *		table.
 *
 *		Within each set, there are three lists which MUST have a one-to-one relationship.
 *		The "DiacritStrip" list gives the base letter of the corresponding "DiacritWhole"
 *		character.
 *		The "DiacritOrder" list assigns a sort value to the diacritic mark of the
 *		"DiacritWhole" character.  All letters that don't appear in these lists, including
 *		the base letter from which the one bearing diacritic marks is formed, are assigned
 *		a sort value of " ".  By using a single letter from the ASCII code chart, we can
 *		have 52 different UTF8 characters all mapping to the same base character.  This will
 *		handle Vietnamese, which is by far the richest language in terms of diacritic marks.
 */
 	require "includes/sort_tables_utf8.php";
	return $result;
}

// optional extra file
if (file_exists( "includes/functions.extra.php")) require  "includes/functions.extra.php";

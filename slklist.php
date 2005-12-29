<?php
/**
 * Displays a list of 'earthfathers' == patriarch.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @subpackage Lists
 * @version $Id: slklist.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */

/*=================================================
		This program was made in analogy of the family.php program
	==	You probably do not have to check the whole list but just select on -no- spouse in the list
	==	You still have to deal with the 'singles' and mother with children
	==	lOOKS LIKE: SELECT * FROM `pgv_individuals` WHERE i_file='$GEDCOM' and i_cfams IS NULL ORDER BY `i_cfams` ASC, 'i_sfams' ASC
	==	The IS NULL check does not work??? Set to another value?
	Change Log:
		9/8/03 - File Created
		25/10/03 - complete output as required by namen.pl (in excel format)
		19/12/03 - added EXCEL file name in SLK file
		23/02/04 - added an extra sort on birthdate of patriarch's with the same name
		25/05/04 - change. I now take most of the information out of the original GEDCOM file
			for every iem I collect the individual stuff (before only the first item was taken
			This will help also later on action to merge differt files

to be done;
- just run first part only if neccessary (file changes)
- add helpfile
- put different routines in subroutine file
- In the future I want to make a slk file defined by the user by way of unique codes. i.e. give a column number
	to codes like DEAT/PLACE, DEAT/DATE, NAME/NICK etc. A default can be prefixed.
===================================================*/


require("config.php");
//-- added for testing purposes
//--require("config_dk.php");
//--print ("starten<br />");


//-- locations in the EXCEL SLK file
define ("pos_type", 1);
define ("pos_RESN", 2);
define ("pos_GENlevel", 3);
define ("pos_GENgen", 4);
define ("pos_GENref", 5);
define ("pos_NAME_SURN", 6);
define ("pos_GENinitials", 7);
define ("pos_NAME_BIRT", 8);
define ("pos_NAME_NICK", 9);
define ("pos_SEX", 10);
define ("pos_CHR_DATE", 11);
define ("pos_BIRT_DATE", 12);
define ("pos_MARR_DATE", 12);
define ("pos_DEAT_DATE", 13);
define ("pos_DIV_DATE", 13);
define ("pos_FATHERref",14);
define ("pos_MOTHERref",15);
define ("pos_BIRT_PLAC",16);
define ("pos_MARR_PLAC",16);
define ("pos_DEAT_PLAC",17);
define ("pos_DIV_PLAC",17);
define ("pos_SOUR",18);
define ("pos_PICT",19);
define ("pos_OCCU",20);
define ("pos_REFN",21);
define ("pos_NOTE",22);
define ("pos_CHAN_DATE",23);
define ("pos_CHAN_NOTE",24);
define ("pos_max",24);
define ("pos_none",25);

//-- locations in the saving intermediate file
define ("mytype", 1);
define ("mylevel", 2);
define ("mygennum", 3);
define ("mykey", 4);
define ("myfather", 5);
define ("mymother", 6);
define ("myfam",7);
define ("mytabblad", 8);

//-- locations in the GEDCOM file
define ("inp_nr", 0);
define ("inp_naam", 1);

	// -- build index array in mem
	// -- array of names
	$myindilist = array();
	$myindialpha = array();
	$myfamlist= array();

function sort_patriarch_list()
{
global $ct,$myindilist,$myindialpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;
global $romeins;
$patriarch= array();
$exceltab = array();
global $numtabs,$patriarch,$exceltab;
global $match1,$match2,$usedinitials;

$personkey1= array();
$fatherkey1= array();
$motherkey1= array();
$famkey1= array();
$years= array();

	$keys = array_keys($myindilist);
	$values = array_values($myindilist);
	$i=0; $j=0; $oldnaam=""; $oldyear= 0;
	while ($i<$ct)
	{
		$ref= $keys[$i];
		$value= $values[$i];
//--		$person= find_person_record($key);
//--		$naam="";
//--		if (getnameitem($namen)!==false)
		$person= find_person_record($ref);
		$naam="";
		if (getnameitem($value)!==false)
		{	$naam= $match1[1];
		}
		if ($naam !== $oldnaam)
		{	$oldnaam= $naam; $j=$i; $oldyear=0;
		}
		$year= 10;
		if ($oldyear > $year)
		{	$k= $i;
			while ($k > $j)
			{
				$k--;
			}
		}
//--pak de naam
//--pak het geb jaar
//--check voor gelijke namen of laagste geb jaar als eerst is. anders omruilen
		$i++;
	}
}

function roots2number()
{
//--gaat dit wel goed
//-- print "start roots2number<br />";
	$notesingle= array();
//--	$maxgen= integer;
//--	$maxsingle= integer;
//--	$maxmulti= integer;
	$parents= array();
global $tabbladname, $tabbladnr, $tabbladnrreverse;
global $romeins;
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;


function fill_in($nr,$key,$value,$level,$nrgenstr,$father,$mother,$tabblad)
//--	nr= nr of familys found
//--	key= value of I number
//--	value= record belonging to key
//--	level= level of anchestors
//--	nrgenstr= string that contains anchestorline so far
{
//-- print "start fill_in<br />";
global $tabbladname, $tabbladnr, $tabbladnrreverse;
global $romeins;
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfather,$mymother,$mytabblad;
global $nrgen, $levelgen;

//-- print ("start fill:".$nr.":".$key.":".$value.":".$level.":".$nrgenstr.":".$romeins[$level]."<br />");
#regel 179
	$levelgen["$key"]= $romeins[$level];
	$nrgen["$key"]= $nrgenstr;
	$tabbladname["$key"]= $tabblad;
	$kk=0;

	$person= find_person_record($key);
	$fams="";
	$ctf= preg_match_all("/1\s*FAMS\s*@(.*)@/",$person,$match,PREG_SET_ORDER);
//--	If first call check if this dynasty is a single person or has a lot of children with the same name


//--	loop for the recursive trail
	$ii=0;
	//-- print ("aantal relaties:".$ctf.":"."<br />");
	while ($ii < $ctf)
//--	loop for every relation
	{
		$fams= $match[$ii][1]; $ii++;
		$famlines= find_family_record($fams);
//--if ($key == "I646") {print ("zoek2 I646:".$ctf.":".$key.":".$fams.":".$famlines.":"."<br />");}
//-- print ("fams,famlines:".$fams.":".$famlines."<br />");
//--	check if there is a husband. If so stop
		$parents= find_parents($fams);
		$stop=1;
		if ($parents["WIFE"] == $key)
		{
	//-- print ($key . "is vrouw<br />");
			if ($parents["HUSB"] != "")
			{
	//-- print ($key . "is vrouw met man<br />");
				$stop=0;
			}
		}
//-- print ("parents zijn:".$parents["HUSB"].":".$parents["WIFE"]."<br />");
	$xfather= $parents["HUSB"]; $xmother= $parents["WIFE"];

//--	loop for every child
		if ($stop > 0)
		{
		$chil="";
		$ctc= preg_match_all("/1\s*CHIL\s*@(.*)@/",$famlines,$match1,PREG_SET_ORDER);
	//-- print ("aantal kinderen van:".$fams.":".$ctc."<br />");

		$jj=0;
		while ($jj < $ctc)
		{	$chil= $match1[$jj][1]; $jj++;
			$kk= $kk+1;
			$fullname= get_sortable_name($chil);
			$nrgenstr1= $nrgenstr . $kk . ".";
//-- print ("volgnr en kind:".$kk.":".$chil."---".$nrgenstr1."---".$fullname."<br />");
			$maxgen1= fill_in($kk,$chil,$fullname,$level+1,$nrgenstr1,$xfather,$xmother,$tabblad);
		}
		}
	};
}

function fill_in_array($maxperson,$personkey,$famkey,$fatherkey,$motherkey,$level)
//--	maxperson = number of keys in personkey
//--	personkey = arry of keys
//--	level= level of anchestors
{
//-- print "start fill_in_array<br />";
global $tabbladname, $tabbladnr, $tabbladnrreverse;
global $romeins;
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
global $nrgen, $levelgen;

//--$maxperson1= integer;
$personkey1= array();
$fatherkey1= array();
$motherkey1= array();

	$maxperson1= 0;
	$ll=0;
	$lastfam= "";
	while ($ll < $maxperson)
{	$ll++;
	$key= $personkey[$ll];
	$myfam= $famkey[$ll];
	$myfather= $fatherkey[$ll];
	$mymother= $motherkey[$ll];
//--if ($key == "") {print ("zoek5 I646:".$key.":".$myfam.":".$myfather.":".$mymother.":"."<br />");}
	if ($key == "")
	{
		$lastfam= $myfam;
//--		put the relation record of this person in the list
		$mytype= 2; $mylevel= ""; $mygennum= ""; $mykey= $myfam;
		$myrecord++; putmylist();
		continue;
	}

//--	in all other cases put the record of this person in the list
	$mylevel= $levelgen["$key"];
	$mygennum= $nrgen["$key"];
	$mytabblad= $tabbladname["$key"];
	$mytype= 1; $mykey= $key;
	$myrecord++; putmylist();
$value="--";
$nr="--";
$nrgenstr="--";

//--	print ("start fillin arry:".$nr.":".$key.":".$value.":".$level.":".$nrgenstr.":".$romeins[$level]."<br />");
	$kk=0;

	$person= find_person_record($key);
	$fams="";
	$ctf= preg_match_all("/1\s*FAMS\s*@(.*)@/",$person,$match,PREG_SET_ORDER);
//--	If first call check if this dynasty is a single person or has a lot of children with the same name

	$ii=0;
//--	print ("aantal relaties:".$ctf.":"."<br />");
	while ($ii < $ctf)
//--	loop for every relation
	{
		$fams= $match[$ii][1]; $ii++;
		$famlines= find_family_record($fams);
//--if ($key == "I646") {print ("zoek3 I646:".$ctf.":".$key.":".$fams.":".$famlines.":"."<br />");}
//--	print ("fams,famlines:".$fams.":".$famlines."<br />");
//--	check if there is a husband. If so stop
		$parents= find_parents($fams);
		$stop=1;
		if ($parents["WIFE"] == $key)
		{
//--			print ($key . "is vrouw<br />");
			if ($parents["HUSB"] != "")
			{
//--				print ($key . "is vrouw met man<br />");
				$stop=0;
			}
		}
//-- 		print ("parents zijn:".$parents["HUSB"].":".$parents["WIFE"]."<br />");
		$xfather= $parents["HUSB"]; $xmother= $parents["WIFE"]; $xfam= $fams;

//--	fill in for every relation that has to be filled in
		if ($stop > 0)
		{
			$chil="";
//-- this is a dummy child just to recognize it is a relation
			$maxperson1++;
			$personkey1[$maxperson1]= $chil; $famkey1[$maxperson1]= $xfam;
			$fatherkey1[$maxperson1]= $xfather; $motherkey1[$maxperson1]= $xmother;

			$ctc= preg_match_all("/1\s*CHIL\s*@(.*)@/",$famlines,$match1,PREG_SET_ORDER);
//--	print ("aantal kinderen van:".$fams.":".$ctc."<br />");

			$jj=0;
//--	loop for every child
			while ($jj < $ctc)
			{	$chil= $match1[$jj][1]; $jj++;
				$maxperson1++;
				$personkey1[$maxperson1]= $chil; $famkey1[$maxperson1]= $xfam;
				$fatherkey1[$maxperson1]= $xfather; $motherkey1[$maxperson1]= $xmother;
			}
		}
	};
//-- 	if all children are in the array go for the next generation
}
//-- 	this was the loop for all elements in the array

	if ($maxperson1 > 0)
	{
		fill_in_array($maxperson1,$personkey1,$famkey1,$fatherkey1,$motherkey1,$level+1);
	}
}

function initbasetab()
{
//-- initialize $tabbladnr for all keys.
global $ct,$myindilist,$myindialpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;

	$i=0;

	while($i<$ct)
	{
		$value = $values[$i];
		$key = $keys[$i];
		$tabbladnr["$key"]= 0;
		$i++;
	}
}

function setbasetab($code,$tabblad,$name1)
{
//-- look for the different familys. Each should later on be put on a separate EXCEL tab.
//-- code=0 initialisation; 1= given family, 2=all familys not used before
//-- print "start setbasetab<br />";

global $ct,$myindilist,$myindialpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;

//--	$keys = array_keys($myindilist);
//--	$values = array_values($myindilist);
	$name= $name1;
	$i=0;

	while($i<$ct)
	{
		$value = $values[$i];
		$key = $keys[$i];
		if ($code < 2)
		{
			$namen= get_person_surname($key);
//--print(",key,namen,namen:".$code.":".$i.":".$ct.":".$key.":".$namen.":".$name.":"."<br />");
			if ($namen == $name)
			{	$maxmulti= $maxmulti + 1;
				$tabbladname["$key"]= $tabblad;
				$tabbladnr["$key"]= $maxmulti;
				$tabbladnrreverse[$maxmulti]= $i;
print ("gevonden:".$maxmulti.":".$key.":".$name.":".$tabbladnr["$key"]."<br />");
			}
		}
		elseif ($tabbladnr["$key"] < 1)
	{
//--		$person= find_person_record($key);
//--		$fams="";
//--		$ctf= preg_match_all("/1\s*FAMS\s*@(.*)@/",$person,$match,PREG_SET_ORDER);
//--	If first call check if this dynasty is a single person or has a lot of children with the same name
//--	if single and no children with same name add it to list of spouse

//--		$ii=0;
//--		$stop= 3;
//--		while ($ii < $ctf)
//--	loop for every relation
//--		{
//--			$fams= $match[$ii][1]; $ii++;
//--			$famlines= find_family_record($fams);
//--			$parents= find_parents($fams);
//--			$stop=2;
//--			if ($parents["WIFE"] == $key)
//--			{
//--	print ($key . "is vrouw<br />");
//--				if ($parents["HUSB"] != "")
//--				{
//--	print ($key . "is vrouw met man<br />");
//--					$stop=3;
//--				}
//--			}
//--		}
//--	loop on number of relations finished

//--			if ($stop == $code)
			{	$maxmulti= $maxmulti + 1;
				$tabbladname["$key"]= $tabblad;
				$tabbladnr["$key"]= $maxmulti;
				$tabbladnrreverse[$maxmulti]= $i;
 //--print ("gevonden:".$code.":".$tabbladnr["$key"].":".$maxmulti.":".$key.":".$tabblad."<br />");
			}
	}
	$i++;
	}
}
//-- end basetab

function fillpatriarch($par1,$par2)
{
global $numtabs,$patriarch,$exceltab;
	$numtabs++;
	$exceltab [$numtabs]= $par1;
	$patriarch[$numtabs]= $par2;
}

function filltabs()
{
//--	filltabs will read the list of patriarch-s and the EXCEL tabs they should be listed on.

global $numtabs,$patriarch,$exceltab;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	$numtabs= 0;
define ("fullist",0);
	if (fullist == 1)
	{
	fillpatriarch("kaas","Kaas");
	fillpatriarch("kommer","Commerscheit");
	fillpatriarch("kostelijk","Kostelijk");
	fillpatriarch("huibers","Huibers");
	fillpatriarch("strijbis","Strijbes");
	fillpatriarch("bak","Bak");
	fillpatriarch("wagenaar","Wagenaar");
	}
	fillpatriarch("$GEDCOM","");
}
//-- end filltabs

global $ct,$myindilist,$myindialpha;
global $keys,$values;
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;
global $romeins;
$patriarch= array();
$exceltab = array();
global $numtabs,$patriarch,$exceltab;

$personkey1= array();
$fatherkey1= array();
$motherkey1= array();
$famkey1= array();


	$keys = array_keys($myindilist);
	$values = array_values($myindilist);
//--print("basetab2. key,value:".$keys[1].":".$keys[2].":".$values[1].":".$values[2]."<br />");
//--	read the different family's to deal with and assign a tabname(EXCEL) to it. Name overig will always be the last one
	filltabs();
	initbasetab();
//--	Now set the name of the tab to every patriarch. Seting will be as follows
//--	all persons related to a given family will be in that tab
//--	all persons with no parents (remark we only have earthfather/mothers) and with children with a given name
//--	will be in that tab (normally wifes or men with no children)

	$maxsingle= 0;
	$maxmulti= 0;
	$jj= 1;
	while ($jj <= $numtabs)
	{	$kk= 1;
		if ($jj == $numtabs) {$kk= 2;}
		setbasetab($kk,$exceltab[$jj],$patriarch[$jj]);
		$jj++;
	}
//--	print("==============".$ct.":".$maxmulti."============<br />");
	$endmulti= $maxmulti;
//--	$kk= 3; $jj= $numtabs;
//--		setbasetab($kk,$exceltab[$jj],$patriarch[$jj]);
//--	$endmulti= $maxmulti;
//--	print("==============".$ct.":".$maxmulti."============<br />");
//--	================= remove later on. check on case!!!=====

	$oldtab= "";
	$j=1;
	while($j<=$maxmulti)
	{	$i= $tabbladnrreverse[$j];
		$value = $values[$i];
		$key = $keys[$i];
//--if ($key == "I2473") {print("reversetab:".$j.":".$i.":".$key.":".$value.":"."<br />");}

//--/??	print_list_person($key, array($value, $GEDCOM));
		$maxgen= 0;
		$level=1;
		$tabnr= $tabbladnr["$key"];
		$tabblad= $tabbladname["$key"];
		if ($oldtab !== $tabblad)
		{ $begintab["$tabblad"]= $myrecord; $oldtab= $tabblad;
//--print ("====tabbladname en beginwaarde:". $tabblad . " : " . $myrecord . "<br />");
		}
		$nrgenstr= (string) $tabnr . ".";
//-- print("==========next item:".$tabnr.":".$key.":".$nrgenstr.":".$tabblad.":<br />");

		$maxgen= fill_in($tabnr,$key,$value,$level,$nrgenstr,"","",$tabblad);
		$maxperson1=1;
		$personkey1[1]= $key;
		$famkey1[1]="";
		$fatherkey1[1]= "";
		$motherkey1[1]= "";
		$maxgen= fill_in_array($maxperson1,$personkey1,$famkey1,$fatherkey1,$motherkey1,$level);
		$j++;
	}
}

function getmylist()
{
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
//-- print "start getmylist<br />";
		$mytype=	$individual["mytype"];
		$mylevel=	$individual["mylevel"];
		$mygennum=	$individual["mygennum"];
		$mykey=	$individual["mykey"];
		$myfam=	$individual["myfam"];
		$myfather=	$individual["myfather"];
		$mymother=	$individual["mymother"];
		$mytabblad=	$individual["mytabblad"];
}


function putmylist()
{
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
global $refrecord;
global $tabname, $begintab;
//-- print "start putmylist<br />";
		$individual["mytype"]=		$mytype;
		$individual["mylevel"]=		$mylevel;
		$individual["mygennum"]=	$mygennum;
		$individual["mykey"]=		$mykey;
		$individual["myfam"]=		$myfam;
		$individual["myfather"]=	$myfather;
		$individual["mymother"]=	$mymother;
		$individual["mytabblad"]=	$mytabblad;
		$mylist[$myrecord]= $individual;
		$refrecord["$mykey"]= $myrecord;
 		$tabname[$myrecord]= $mytabblad;
//--	print("I2473===".$mykey.":".$myrecord."<br />");
//--		print "<pre>";
//--		printf ("%4s,%10s,%2s,%5s,%30s,%6s,%6s,%6s,%6s",$myrecord,$mytabblad,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother);
//--		print "</pre>";
}

//--	======================= following routines for saving 'roots' ============================
function check_dbkaasnotused() {
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	$indexfile = $INDEX_DIRECTORY.$GEDCOM."_index.php";

	//-- check for index files and update them if necessary
	if (!isset($BUILDING_INDEX)) {
		$updateindex=false;
		if ((file_exists($indexfile))&&(file_exists($GEDCOMS[$GEDCOM]["path"]))) {
			$indextime = filemtime($indexfile);
			$gedtime = filemtime($GEDCOMS[$GEDCOM]["path"]);
			if ($indextime < $gedtime) $updateindex=true;
		}
		else {
			$updateindex=true;
		}

		if (file_exists($indexfile)) {
			//require($indexfile);
			$fp = fopen($indexfile, "r");
		        $fcontents = fread($fp, filesize($indexfile));
		        fclose($fp);
			$lists = unserialize($fcontents);
			unset($fcontents);
			$indilist = $lists["indilist"];
			$famlist = $lists["famlist"];
			$sourcelist = $lists["sourcelist"];
			$otherlist = $lists["otherlist"];
		}
	}
	return true;
}

function get_patriarch_list()
{
//-- save the items in the database
global $ct,$myindilist,$myindialpha;

//-- print "start roots2database<br />";
	global $GEDCOM,$INDEX_DIRECTORY, $FP, $pgv_lang;

	$indexfile = $INDEX_DIRECTORY.$GEDCOM."_patriarch.php";
//--	fclose($FP);
	$FP = fopen($indexfile, "r");
//--	fwrite($FP, "<?php\r\n\$indilist = array();\r\n\$famlist = array();\r\n\$sourcelist = array();\r\n\$otherlist = array();\r\n\r\n");
	if (!$FP) {
		print "<font class=\"error\">".$pgv_lang["unable_to_create_index"]."</font>";
		exit;
	}

	$fcontents = fread($FP, filesize($indexfile));
	fclose($FP);
	$lists = unserialize($fcontents);
	unset($fcontents);
	$myindilist = $lists["patriarchlist"];
}



//--	======================= following routines for creating EXCEL database ============================

//-- function to print a more complete date
function get_number_date($datestr)
{
global $pgv_lang, $DATE_FORMAT, $LANGUAGE, $USE_HEBREW_DATES;
//-- print "start get-number_date<br />";
	$monthtonum["jan"] = 1;
	$monthtonum["feb"] = 2;
	$monthtonum["mar"] = 3;
	$monthtonum["apr"] = 4;
	$monthtonum["may"] = 5;
	$monthtonum["jun"] = 6;
	$monthtonum["jul"] = 7;
	$monthtonum["aug"] = 8;
	$monthtonum["sep"] = 9;
	$monthtonum["oct"] = 10;
	$monthtonum["nov"] = pos_none;
	$monthtonum["dec"] = 12;
	$monthtonum["abt"] = 13;
	$monthtonum["bef"] = 14;
	$monthtonum["aft"] = 15;
	$hstr= "xx";
	$datestrh= $datestr;
if ($datestr !== "")
{
	$ct = preg_match_all("/(\d{1,2})?\s?([a-zA-Z]{3})?\s?(\d{4})/", $datestr, $match, PREG_SET_ORDER);
//--print ("==preg_match :"  . $ct . "::" . $match[0][0] . "::" . $match[0][1] . "::" . $match[0][2] . "::" . $match[0][3] . "<br />");
	for($i=0; $i<$ct; $i++)
	{
		$pos1 = strpos($datestr, $match[$i][0]);
		$pos2 = $pos1 + strlen($match[$i][0]);
		$dstr_beg = trim(strtolower(substr($datestr, 0, $pos1)));
//--print("==preg_match_loop: " . $i . "::" . $pos1 . "::" . $pos2 . "::" . $dstr_beg . "::" . $match[$i][0] . "::" . $match[$i][1] . "<br />");
		if ($dstr_beg == "abt") {$hstr= "ca";};
		if ($dstr_beg == "aft") {$hstr= "gt";};
		if ($dstr_beg == "bef") {$hstr= "lt";};
		$dstr_end = substr($datestr, $pos2);
		$day = trim($match[$i][1]);
		$month = trim(strtolower($match[$i][2]));
		if ($month == "abt") {$hstr= "ca"; $month= ""; $day= "";};
		if ($month == "bef") {$hstr= "lt"; $month= ""; $day= "";};
		if ($month == "aft") {$hstr= "gt"; $month= ""; $day= "";};
		if ($day == "") {$day= $hstr;};
		if ($month == "") {$month= $hstr;} else {$month = $monthtonum[$month];};
		$year = $match[$i][3];
//-- check if day and month are in range (are they given) and check year for a non-digit
		$datestr=sprintf("%02s-%02s-%04s",$day,$month,$year);
//--	print("==result date:". $datestrh . ":" .$day.":".$month.":".$year.":".$datestr."<br />");
	}

//--	$array_short = array;
//--	$array_short=("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec",
//--"abt", "aft", "and", "bef", "bet", "cal", "est", "from", "int", "to");
//--	foreach($array_short as $value) {
//--		$datestr = preg_replace("/$value(\W)/i", $pgv_lang[$value]."\$1", $datestr);
//--	}
}
	return $datestr;
}

function stringinfo($indirec,$lookfor)
//look for a starting string in the gedcom record of a person
//then take the stripped comment
{
//-- print "start stringinfo<br />";
global $match1,$match2,$usedinitials;
	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/".$lookfor." (.*)/", $birthrec, $match1);
			if ($dct < 1)
			{	$match1[1]="";
//-- family treemaker gives a name with no fill and that a continuation as PLAC
				$dct2 = preg_match("/2 PLAC (.*)/", $birthrec, $match1);
				if ($dct2 < 1) {$match1[1]="";}
			}
//-- print("stringinfo:".$dct.":".$lookfor.":".$birthrec.":".$match1[1].":". $match1[2] .":<br />");
			$match1[1]= trim($match1[1]);
			return true;
		}
	else 	{	return false;}
}

function stringevent($indirec,$lookfor)
//look for a starting string in the gedcom record of a person
//then take the stripped comment
{
//-- print "start stringevent<br />";
global $match1,$match2,$usedinitials;
	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
//--first 4.3.3	$dct = preg_match_all("/".$lookfor."(.*)/", $indirec, $match1,PREG_SET_OFFSET,$match2);
			$dct = preg_match_all("/".$lookfor."(.*)/", $indirec, $match1,PREG_SET_OFFSET);
			if ($dct < 1)
			{	$match1[1]="";
//-- family treemaker gives a name with no fill and that a continuation as PLAC
//--				$dct2 = preg_match("/2 PLAC (.*)/", $birthrec, $match1);
//--				if ($dct2 < 1) {$match1[1]="";}
			}
print("stringinfo :".$dct.":".$lookfor.":".$indirec .":<br />");
print("stringinfo1:".$dct.":".$match1[0][0].":". $match2[0] .":<br />");
print("stringinfo2:".$dct.":".$match1[1][0].":". $match2[1] .":<br />");
print("stringinfo3:".$dct.":".$match1[2][0].":". $match2[2] .":<br />");
print("stringinfo4:".$dct.":".$match1[3][0].":". $match2[3] .":<br />");
			$match1[1][0]= trim($match1[1][0]);
			return true;
		}
	else 	{	return false;}
}


function formfile($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person
//--then find the DATE and PLACE variables
{
//-- print "start dateplace<br />";
global $match1,$match2,$usedinitials;

	$objerec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($objerec!==false)
		{
			$dct = preg_match("/2 FORM (.*)/", $objerec, $match1);
//-- if ($dct > 0) {print("birthrec + date" . $objerec . ":::" . $match1[1] . "<br />");};
			if ($dct>0) $match1[1]= get_number_date($match1[1]);
			$pct = preg_match("/2 FILE (.*)/", $objerec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br />";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}

function dateplace($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person
//--then find the DATE and PLACE variables
{
//-- print "start dateplace<br />";
global $match1,$match2,$usedinitials;

	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/2 DATE (.*)/", $birthrec, $match1);
//-- if ($dct > 0) {print("birthrec + date" . $birthrec . ":::" . $match1[1] . "<br />");};
			if ($dct>0) $match1[1]= get_number_date($match1[1]);
			$pct = preg_match("/2 PLAC (.*)/", $birthrec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br />";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}

function datenote($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person
//--then find the DATE and NOTE variables
{
//--print "start datenote<br />";
global $match1,$match2,$usedinitials;

	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/2 DATE (.*)/", $birthrec, $match1);
			if ($dct>0) $match1[1]= get_number_date($match1[1]);
			$pct = preg_match("/2 NOTE (.*)/", $birthrec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br />";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}
// end datenote


function getnameitem($namen)
//-- get the different positions of the name part
//-- take care: Mary/Anna Groot/ is non conformant and should be Mary Anna/Groot/
{
//-- print "start getnameitem<br />";
global $match1,$match2,$used ;

	$initialt= "";
	$strpos1 = strpos($namen, ",");
	if ($strpos1 !== false)
	{
		$strpos2 = strpos($namen,",",$strpos1+1);
		if ($strpos2 > 0)
		{
			$tussen= trim(substr($namen,$strpos1+1,$strpos2-$strpos1-1));
			$strpos1= $strpos2;
			$initialt= substr($tussen,0,1);
		}
		if ($strpos1==0)
		{	$surname="";}
		else
		{	$surname= substr($namen,0,$strpos1);}
	}
	else {return false;};

//--print ("naamontleding: " . $namen .":" . 	$surname .":" . $birthname .":" . $tussen .":" . "<br />");
	$initials="";
	$rest= trim(substr($namen,$strpos1+1));
	$birthname= $rest;
	while (strlen($rest) > 0)
	{	$rest01= substr($rest,0,1);
		$initials= $initials . $rest01;
		$strpos2= strpos($rest," ");
		if ($strpos2 > 0) {$rest= trim(substr($rest,$strpos2+1));} else {$rest= "";}
	}
	$match1[1]= trim($surname);
	$match1[2]= trim($birthname);
	$rest01= substr($surname,0,1);
	if ($rest01 == "(") {$rest01= "U";};
//--	prevent ( from (Unknown) to be in the initials.
	$match1[5]= trim($initials . $initialt . $rest01);
	return true;
}

function addinitials($str1,$str2)
{
global $match1,$match2,$usedinitials;

	$strnew= $str1.$str2;
	$strnew1= $strnew;
	$i= 0;
	$stradd= "";
//--$xltype= gettype($usedinitials[$strnew]);
//--print("addinitials:".$xltype."<br />");
//--if ($str1 == "MB")
//--{print ("addinit: " .$xltype.":" . $strnew . "<br />");};
	while (gettype($usedinitials["$strnew"]) !== "NULL")
	{	$stradd= substr("abcdefghijklmnopqrstuvwxyz",$i,1);
		$strnew= $str1 . $stradd . $str2;
		$i++;
	}
	$usedinitials["$strnew"]= 2;
//--if ($str1 == "MB")
//--{print ("addinit: " . $strnew . " + " . $i . "+" . $stradd . "<br />");};
	return $stradd;
}


function slkvalue_newrow($nr,$myval)
{
global $posnr,$ALLslk;
	fwrite($ALLslk,"C;Y".$nr.";X1;K".$myval."\n");
	$posnr=1;
}

function slkvalue($myval)
{
global $posnr,$ALLslk;
	$posnr++;
	if ($myval != "") {fwrite($ALLslk,"C;X".$posnr.";K".$myval."\n");};
}

function slkformula($mystr)
{
global $posnr,$ALLslk;
	$posnr++;
//--	print("slkformula:".$mystr."<br />");
	fwrite($ALLslk,"C;X".$posnr.";K12345;".$mystr."\n");
}

function slkref($verta,$vertb,$hora,$horb)
{
global $tabname, $begintab;
//--	current position is (verta,hora) related position is (vertb,horb)
//--	make a reference to a different tab
//--	the expression is E<reference>
//--	reference= <tab><Rowref><Columnref>
//--	tab= <tabname>!
//--	Rowref= R[reldif on row] (if reldif=0 then just R
//--	Columnref= C[reldif on Column] (if reldif =0 then just C
//--	C[i]R[j] means relative CiRj absolute adresses
//--	so mytab!R[-2]C[+2] on pos 4,2 means take value of pos 2,4 of tab "mytab"

	$strtab="";
	$strhor= "C";
	$strvert= "R";
	$taba= $tabname[$verta]; $tabb= $tabname[$vertb];
//--	printf ("slkref,%5s,%5s,%5s,%5s,%10s,%10s,%10s,%10s,%10s,%10s<br />",$verta,$vertb,$hora,$horb,$taba,$tabb,$tabname[$verta],$tabname[$vertb],$begintab[$taba],$begintab[$tabb]);

//--	There is obviously a difference in assigning values to a different tab in the same file and a relation
//--	to a different file. in the latter case an absolute (instead of relative) address should be used.
//--	Although it looks like positive values can be used as relative addresses.
	if ($taba !== $tabb)
	{
//--	printf ("slkref,%5s,%5s,%5s,%5s,%10s,%10s,%10s,%10s,%10s,%10s<br />",$verta,$vertb,$hora,$horb,$taba,$tabb,$tabname[$verta],$tabname[$vertb],$begintab[$taba],$begintab[$tabb]);
		$strtab= "[phpgedview.xls]" . $tabb . "!";
		$strhor= "C" . $horb;
		$strvert= "R" . ($vertb- $begintab[$tabb] + 1);
//--	2 is offset. 1 for commentline and 1 because excel strats with 1 instead of 0
	} else
	{
		$hor= $horb - $hora;
		$vert= $vertb - $verta;
		if ($hor !== 0) {$strhor= "C[" . $hor . "]";}
		if ($vert !== 0) {$strvert= "R[" . $vert . "]";}
	}
//--	print("slkref:".$vert.":".$hor.":".$strtab.$strvert.$strhor."<br />");
	slkformula("E" . $strtab . $strvert . $strhor);
}

function slkstr_newrow($nr,$mystr)
{
global $posnr,$ALLslk;
	fwrite($ALLslk,"C;Y".$nr.";X1;K\"".$mystr."\"\n");
	$posnr=1;
}

function slkstr($mystr)
{
global $posnr,$ALLslk;
	$posnr++;
	if ($mystr != "")
		{fwrite($ALLslk,"C;X".$posnr.";K\"".$mystr."\"\n");}
}

function open_slk($file)
{
global $posnr,$ALLslk;
//-- print "start open_slk<br />";
//--	open CSV file to put the data in
	($ALLslk = fopen($file,"w")) or die ("error on opening $file");

	fwrite($ALLslk, "ID;PWXL;N;E"."\n");
	fwrite($ALLslk, "P;PGeneral"."\n");
	fwrite($ALLslk, "F;P0;DG0G8;M255"."\n");
	fwrite($ALLslk, "O;L;D;V0;K47;G100 0.01"."\n");
//--	prefix text
//--	and now the header


	slkstr_newrow(1,"type(1=person,2=fam)");
	slkstr("RESN");
	slkstr("GEN-level");
	slkstr("GEN-generation");
	slkstr("GEN-reference");
	slkstr("NAME-SURN");
	slkstr("GEN-Initials");
	slkstr("NAME-BIRTH");
	slkstr("NAME-NICK");
	slkstr("SEX");
	slkstr("CHR-DATE");
	slkstr("BIRT/MARR-DATE");
	slkstr("DEAT-DATE");
	slkstr("GEN-FATHERreference");
	slkstr("GEN-MOTHERreference");
	slkstr("BIRT/MARR-PLACE");
	slkstr("DEAT/MARR-PLACE");
	slkstr("SOUR");
	slkstr("PICT");
	slkstr("OCCU");
	slkstr("REFN");
	slkstr("NOTE");
	slkstr("CHAN-DATE");
	slkstr("CHAN-SOUR");
	return $ALLslk;
}

function close_slk($ALLslk)
{
//-- print "start close_slk<br />";
	fwrite($ALLslk, "E"."\n");
	fclose($ALLslk);
}

function splitindilines($lct,$indilines)
{
//--	Split the individual lines of a Individual or family record and take all unique combinations together
//--	This way you gather combinations line DEAT/DATE, DEAT/PLACE, OOCU, NOTE and other lines in one array element
//--	I makes it easier to file the defined slk columns with all (i.e. more than one occupation)
	$antlijnen=0;
#	my $line;
#	my @elementen;
#	my @strings;
#	my @totstrings;
#	my $totstr;
#	my $mystr;
#	my $nrm1;
#	my $lastcontent;
#	my $content;
#	my $i;
#	my $j;
$arcodes= array();
$arcontent= array();
$nr = 0;
$naam = "";
settype($nr,'integer');
settype($naam, 'string');

	$lastnr= -1;
	$totstr= "";
	$nrbew= 0;
	$lastcontent= "";
	$antlijnen= 0;
#regel 1003
#print ("start ontleden:".$lct."<br />");
$i1=0;
while ($i1 <= $lct)
{
	$line = "   ";
	if ($i1 < $lct) {$line= $indilines[$i1];}
	$i1= $i1+1;
	$antlijnen=$antlijnen+1;
#	print ("de GEDCOM regel=".$line."<br />");
#	@elementen= map{split separator,$_} $line;
	$elementen= explode(" ",$line);
//--	print ( " 0:".$elementen[0]." 1:".$elementen[1]." 2:".$elementen[2]. "<br />");

	$nr= $elementen[inp_nr];

//--	if (($nr > $lastnr) and ($lastnr > 0))
//--	{
//--		if ($lastcontent != "") {print ("line:".$antlijnen.":extra info:".$lastcontent."<br />");}
//--	};
	if ($nr <= $lastnr)
	{
#regel 1025
//--		print ("nr en lastnr".$nr.":".$lastnr.":line:".$antlijnen." naam:".$totstr." content:". $lastcontent."<br />");
		$arcodes{$totstr}= $totstr;
		if ($arcontent{$totstr} != '')
		{	$arcontent{$totstr}= $arcontent{$totstr} . "," . trim($lastcontent);
#			print ("toegevoegde waarde");
		}
		else
		{	$arcontent{$totstr}= $lastcontent;};
		$lastcontent= "";
	};
#regel 1051

	$naam= trim($elementen[inp_naam]);
#	if ($naam == 'CONC') {$naam= 'CONT';}
#regel 1060
	$i= strpos($line, " ",1);
	$j= strpos($line, " ",$i+1);
	$content= trim(substr($line,$j));
	if ($j < 1) {$content= "";};

#print ("eerste letter:" . substr($naam,0,1) . "<br />");
	if (substr($naam,0,1) == '@')
	{
		$naam= substr($naam,0,2) . "x@";
//--		print (substr($naam,0,1).".....:".$naam."<br />");
	}

#	$mystr= $nr . " " . $naam;
	$mystr= $naam;
	$strings[$nr]= $mystr;
	$nrm1= $nr-1;
	$totstr= $mystr;
	if ($nr > 0)
		{	$totstr= $totstrings[$nrm1]. ":" . $mystr;
//--			print ("nr>0:<br />");
		};

	$totstrings[$nr]= $totstr;
#	print ("nr,totstr, totstrings:".$nr.":".$nrm1.":0:".$strings[0].":1:".$strings[1].":2:".$strings[2].":3:".$strings[3].":tot:".$totstrings[$nr].":".$totstr."<br />");

	$lastnr= $nr;
	if ($content != "")
		{	$lastcontent= $lastcontent . " " . $content;}
}
// end of main loop
global $nrcode, $arcode, $ar1content;
//--	if ($nr == 0)
//--	{
//--		print ("---------------------------------------------------<br />");
		$ant_namen= 0; $nrcode=0;
		foreach ($arcodes as $indexval => $ai)
		{
			$ant_namen= $ant_namen+1; $str= $ai;
//--			print ("nr:".$ant_namen." code=". $str. " content=".$arcontent{$str}."<br />");
			$nrcode= $nrcode + 1;
			$arcode[$nrcode]= $str;
			$ar1content{$str}= trim($arcontent{$str});
		}
//--		print ("---------------------------------------------------<br />");
//--	}

}

function roots2excel()
{
//-- print "start roots2excel<br />";
global $mylist,$myrecord,$individual,$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad;
//--	$myrecord= number of lines to be created in the excel database
//--	$mylist= array of records ($individual) belonging to individual lines
//--	for every line it contains values for:mytype,mylevel,mygennum,mykey,myfather,mymother,myfam,mytabblad

global $refrecord;
global $match1,$match2,$usedinitials;
global $pgv_lang;
global $posnr,$ALLslk;
global $tabname, $begintab;
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY, $BUILDING_INDEX, $indilist, $famlist, $sourcelist, $otherlist;

	$first= 1;
	$oldtabblad= "1";
	$file= $INDEX_DIRECTORY.$GEDCOM. ".slk";
//--	CSV file to put the data in

//--	go in the loop
	$yvalue=0;
	$i=0;
	while($i<$myrecord)
	{	$i++;
		$individual= $mylist["$i"];
		getmylist();
		if ($oldtabblad !== $mytabblad)
		{
			if ($first == 0)
			{
				close_slk($ALLslk); $first= 1;
			}
			$file=  $INDEX_DIRECTORY.$mytabblad . ".slk";
			if ($first == 1)
			{
				$ALLslk= open_slk($file);
				$yvalue=1;
			}
			$first= 0;
 			print ($pgv_lang["excel_tab"] . $mytabblad . $pgv_lang["excel_create"] . $file . "<br />");
			$oldtabblad= $mytabblad;
		}
		$absfa= 0; $absmo= 0;
		if ($myfather !== "") {$absfa= $refrecord["$myfather"];}
		if ($mymother !== "") {$absmo= $refrecord["$mymother"];}
if ($absmo == "")
{
//--	print ("refrecord:".$i.":".$mykey.":".$refrecord["$mykey"].":".$myfather.":".$absfa.":".$mymother.":".$absmo."<br />");
}
		$myparent= $myfather; if ($myparent == ""){$myparent= $mymother;}
		if ($mytype == 1) {$namen= get_sortable_name($mykey);} else {$namen= get_sortable_name($myparent);}
		$person= find_person_record($mykey);
//--	printf ("%2s,%5s,%30s,%6s,%6s,%6s,%6s,%20s<br />",$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$namen);


//-- find all the fact information
	if ($mytype == 1)
	{	$indirec = find_person_record($mykey);}
	else
	{	$indirec= find_family_record($myfam);}

//-- in case you want prints for debugging
	$indilines = split("\n", $indirec);
//--  find the number of lines in the individuals record
	$lct = count($indilines);
#	print ($indirec."<br />");
#	print ("mytype,mykey,myfam,lct:".$mytype.":".$mykey.":".$myfam.":".$lct.":".$indirec[0]."<br />");
#	$i1=0;
#	while($i1<$lct-1)
#	{
#		print ($indilines[$i1]."<br />");
#		$i1++;
#	}
#regel 1187

$arelement= array();
$arelement= (array) "";
global $nrcode, $arcode, $ar1content;
global $defs;
	$nrcode=0;
	$arcode= (array) "";
	$ar1content= (array) "";
	splitindilines($lct,$indilines);
	$i1=0;
	$notes= "";
	while($i1<$nrcode)
	{
		$i1++;
		$str= $arcode[$i1];
		$check= $defs{$str};
		if ($check == '')
			{print ("nieuwe code:".$str."<br />"); $check= pos_none; $defs{$str}= pos_none;}
		if ($check == pos_none)
		{	if ($notes == "") {$notes= $str . ":". $ar1content{$str};}
			else			{$notes= $notes . "," . $str . ":". $ar1content{$str};}
		}
		if ($check > 0) {$arelement[$check] = trim($ar1content{$str});}
	}
	$arelement[pos_NOTE]= $arelement[pos_NOTE] . $notes;
	$arelement[pos_GENlevel]= $mylevel;
	$arelement[pos_GENgen]= $mygennum;

//--	set the privacy element
	$arelement[pos_type]= $mytype;
	if ($arelement[pos_RESN] == "privacy")
		{$arelement[pos_RESN]= 0;}
	else
		{$arelement[pos_RESN]= 1;}

	$arelement[pos_NAME_SURN]= "None";
	if ($mytype == 1)
	{
		$arelement[pos_NAME_BIRT]= "None";
		$arelement[pos_GENinitials]= "NN";
	}

	if (getnameitem($namen)!==false)
	{	$surname= $match1[1];
		$birthname= $match1[2];
		$initials= $match1[5];
		$arelement[pos_NAME_SURN]= $match1[1];
		if ($mytype == 1)
		{	$arelement[pos_NAME_BIRT]= $match1[2];
			$arelement[pos_GENinitials]= $match1[5];
		}
	}

if ($mytype == 1)
{
	$arelement[pos_BIRT_DATE]= get_number_date($arelement[pos_BIRT_DATE]);
	$arelement[pos_CHR_DATE]= get_number_date($arelement[pos_CHR_DATE]);
	$arelement[pos_DEAT_DATE]= get_number_date($arelement[pos_DEAT_DATE]);
//--	translate sex (M,F, <blank>) to 1,2 and 3
	if ($arelement[pos_SEX] == "M") {$arelement[pos_SEX]= 1;}
		else
	if ($arelement[pos_SEX] == "F") {$arelement[pos_SEX]= 2;}
		else
		 {$arelement[pos_SEX]= 3;}
//-- print ("sexe=".$match1[1].":".$sex."<br />");


//--	check if the combination of initials and birthdate is unique. if not add a character to initials
	$initials= $arelement[pos_GENinitials];
	$birthdate= $arelement[pos_BIRT_DATE];
	$addchar= addinitials($initials,$birthdate);
	$initials= $initials . $addchar;
	$arelement[pos_GENinitials]= $initials;

}

//--	not quit sure if I need those items for the SLK file
//--	if (formfile($indirec,"1 OBJE")!==false)
//--		{$form1= $match1[1]; $picture=$match2[1];}
	$arelement[pos_CHAN_DATE]= get_number_date($arelement[pos_CHAN_DATE]);

if ($mytype == 2)
{
//--	print ("fam record:" . $indirec."<br />");
	$arelement[pos_MARR_DATE]= get_number_date($arelement[pos_MARR_DATE]);
	$arelement[pos_DIV_DATE]= get_number_date($arelement[pos_DIV_DATE]);
}


//--	print "<pre>";
//--	printf ("%2s,%5s,%30s,%6s,%6s,%6s,%6s,%10s,%40s",$mytype,$mylevel,$mygennum,$mykey,$myfam,$myfather,$mymother,$mytabblad,$namen);
//--	print "</pre>";
//--	print "<pre>";
//--	$i1=0;
//--	while($i1<pos_max)
//--	{
//--		$i1++;
//--		$str= $arelement[$i1];
//--		if ($str != "") {print ($i1 . ":" . $str . ":<br />");}
//--	}
//--	print "</pre>";

//--	write the line in SYLK format
//--	notation R[vertikaal]C[horizontal]


	$yvalue++;
	slkvalue_newrow($yvalue,$arelement[pos_type]);
	slkvalue($arelement[pos_RESN]);
if ($mytype == 1)
{	slkstr  ($arelement[pos_GENlevel]);
	slkstr  ($arelement[pos_GENgen]);
	slkformula("ECONCATENATE(RC[+2],TEXT(RC[+7],\"dd-mm-jjjj\"))");
} else
{	slkstr  ("");
	slkstr  ("");
	slkstr  ("");
}
	slkstr  ($arelement[pos_NAME_SURN]);
	slkstr  ($arelement[pos_GENinitials]);
	slkstr  ($arelement[pos_NAME_BIRT]);
	slkstr  ("");
	slkvalue($arelement[pos_SEX]);

if ($mytype == 1)
{
	slkstr  ($arelement[pos_CHR]);
	slkstr  ($arelement[pos_BIRT_DATE]);
	slkstr  ($arelement[pos_DEAT_DATE]);
//--	next two items are the references to the father and mother records
	if ($myfather != "") {slkref($i,$absfa,pos_FATHERref,pos_GENref);} else {	slkstr  ("");}
	if ($mymother != "") {slkref($i,$absmo,pos_MOTHERref,pos_GENref);} else {	slkstr  ("");}
	slkstr  ($arelement[pos_BIRT_PLAC]);
	slkstr  ($arelement[pos_DEAT_PLAC]);
} else
{
//--	You even can do without difference in $mytype because the references (i.e. pos_MARR_DATE == pos_BIRT_DATE)are the same
	slkstr  ("");
	slkstr  ($arelement[pos_MARR_DATE]);
	slkstr  ($arelement[pos_DIV_DATE]);
	if ($myfather != "") {slkref($i,$absfa,pos_FATHERref,pos_GENref);} else {	slkstr  ("");}
	if ($mymother != "") {slkref($i,$absmo,pos_MOTHERref,pos_GENref);} else {	slkstr  ("");}
	slkstr  ($arelement[pos_MARR_PLAC]);
	slkstr  ($arelement[pos_DIV_PLAC]);
}
	slkstr($arelement[pos_SOUR]);
	slkstr($arelement[pos_PICT]);
	slkstr($arelement[pos_OCCU]);
	slkstr($arelement[pos_REFN]);
	slkstr($arelement[pos_NOTE]);
	slkstr($arelement[pos_CHAN_DATE]);
	slkstr($arelement[pos_CHAN_NOTE]);
//--	end of loop
	}
	close_slk($ALLslk);
}

function source2excel()
{
//-- there can also be source records in the gedcom file. Normally they look like:
//--	0 SOUR @S<integer>@
//--	1 TITLE <title>
//--	1 REPO
//--	2 CALN
//--	3 MEDI BOOK
//--	So far not implemented
}

function maakromein($nr,$str)
{
//--	print "start maakromein<br />";
global $romeins;
		$romeins[$nr]= $str;
//--	print ("romeins:".$nr.":".$romeins[$i]."<br />");
}

function maakdefs($str,$ok)
{
//-- function to fill default gedcom combinations
global $defs;
	$defs{$str}= $ok;
//-- print ("defs:".$ok.":".$str."<br />");
}

//--	========= start of main program =========

global $ct,$myindilist,$myindialpha;

$tabbladnr= array();
$tabbladname= array();
$tabbladnrreverse= array();
$tabname= array();
$begintab= array();
global $maxmulti, $tabbladname, $tabbladnr, $tabbladnrreverse;
global $tabname, $begintab;

$usedinitials= array();
global $match1,$match2,$usedinitials;

$romeins= array();
$pidused= array();
$levelgen= array();
$nrgen=array();
$mylist= array();
//--$myrecord= integer;
//--$mytype= integer;
//--$mylevel= integer;
//--$mygennum= integer;
//--$mykey= integer;
//--$myfather= integer;
//--$mymother= integer;
//--$mytabblad= string;

$individual= array();
$refrecord= array();


//-- ========================================================================================
//--global $romeins;
	maakromein(1,"I"); maakromein(2,"II"); maakromein(3,"III"); maakromein(4,"IV"); maakromein(5,"V");
	maakromein(6,"VI"); maakromein(7,"VII"); maakromein(8,"VIII"); maakromein(9,"IX"); maakromein(10,"X");
	maakromein(11,"XI"); maakromein(12,"XII"); maakromein(13,"XIII"); maakromein(14,"XIV"); maakromein(15,"XV");
	maakromein(16,"XVI"); maakromein(17,"XVII"); maakromein(18,"XVIII"); maakromein(19,"XIX"); maakromein(20,"XX");
#regel 1511
$defs= array();
global $defs;
//--	Put all known GEDCOM combinations in an array
//--	first name combination. second parameter is nr to store in (now 1 = ok and pos_none= last). negative is forget.
	maakdefs("@Ix@:RESN",pos_RESN);
	maakdefs("@Ix@:NAME",-1); maakdefs("@Ix@:NAME:NOTE",pos_none); maakdefs("@Ix@:NAME:NOTE:CONC",pos_none);maakdefs("@Ix@:NAME:NOTE:CONT",pos_none);
	maakdefs("@Ix@:NAME:SOUR",pos_none);
	maakdefs("@Ix@:SEX",pos_SEX);
	maakdefs("@Ix@:RELI:PLAC",pos_none);
	maakdefs("@Ix@:CHR:DATE",pos_CHR_DATE); maakdefs("@Ix@:CHR:PLAC",pos_none);
	maakdefs("@Ix@:CHR:RELI",pos_none); maakdefs("@Ix@:CHR:WITN",pos_none);
	maakdefs("@Ix@:CHR:NOTE",pos_none); maakdefs("@Ix@:CHR:NOTE:CONC",pos_none);
	maakdefs("@Ix@:BIRT:DATE",pos_BIRT_DATE); maakdefs("@Ix@:BIRT:PLAC",pos_BIRT_PLAC);
	maakdefs("@Ix@:BIRT:NOTE",pos_none); maakdefs("@Ix@:BIRT:NOTE:CONT",pos_none);
	maakdefs("@Ix@:BIRT:TYPE",pos_none);
	maakdefs("@Ix@:DEAT:DATE",pos_DEAT_DATE); maakdefs("@Ix@:DEAT:PLAC",pos_DEAT_PLAC);
	maakdefs("@Ix@:DEAT:NOTE",pos_none); maakdefs("@Ix@:DEAT:NOTE:CONT",pos_none); maakdefs("@Ix@:DEAT:NOTE:CONC",pos_none);
	maakdefs("@Ix@:DEAT:TYPE",pos_none);
	maakdefs("@Ix@:DEAT:CAUS",pos_none);
	maakdefs("@Ix@:BURI:DATE",pos_none); maakdefs("@Ix@:BURI:PLAC",pos_none);
	maakdefs("@Ix@:BURI:NOTE",pos_none); maakdefs("@Ix@:BURI:NOTE:CONT",pos_none);
	maakdefs("@Ix@:BURI:TYPE",pos_none);
	maakdefs("@Ix@:CHAN:DATE",pos_CHAN_DATE); maakdefs("@Ix@:CHAN:NOTE",pos_CHAN_NOTE);
	maakdefs("@Ix@:RESI:DATE",pos_none); maakdefs("@Ix@:RESI:ROLE",pos_none);
	maakdefs("@Ix@:_ORIG:DATE",pos_none); maakdefs("@Ix@:_ORIG:PLAC",pos_none);
	maakdefs("@Ix@:REFN",pos_REFN);
	maakdefs("@Ix@:ADDR",pos_none);
	maakdefs("@Ix@:SOUR",pos_SOUR);
	maakdefs("@Ix@:OCCU",pos_OCCU); maakdefs("@Ix@:OCCU:PLAC",pos_none);
	maakdefs("@Ix@:EVEN",pos_none); maakdefs("@Ix@:EVEN:TYPE",pos_none); maakdefs("@Ix@:EVEN:PLAC",pos_none);
	maakdefs("@Ix@:PICT",pos_PICT);
	maakdefs("@Ix@:OBJE",-1); maakdefs("@Ix@:OBJE:FORM",-1); maakdefs("@Ix@:OBJE:FILE",-1);
	maakdefs("@Ix@:FAMC",-1);
	maakdefs("@Ix@:FAMS",-1);
	maakdefs("@Ix@:NOTE",pos_NOTE); maakdefs("@Ix@:NOTE:CONT",pos_NOTE); maakdefs("@Ix@:NOTE:CONC",pos_NOTE);
	maakdefs("@Fx@:MARR:DATE",pos_MARR_DATE); maakdefs("@Fx@:MARR:PLAC",pos_MARR_PLAC);
	maakdefs("@Fx@:MARB:DATE",pos_none); maakdefs("@Fx@:MARB:PLAC",pos_none);
	maakdefs("@Fx@:MARS:DATE",pos_none);
	maakdefs("@Fx@:MARR:TYPE",pos_none);
	maakdefs("@Fx@:MARR:RELI",pos_none);
	maakdefs("@Fx@:MARB:TYPE",pos_none);
	maakdefs("@Fx@:MARR:NOTE",pos_none);maakdefs("@Fx@:MARR:NOTE:CONT",pos_none);  maakdefs("@Fx@:MARR:NOTE:CONC",pos_none);
	maakdefs("@Fx@:WITN",pos_none);
	maakdefs("@Fx@:DIV",pos_none); maakdefs("@Fx@:DIV:DATE",pos_none); maakdefs("@Fx@:DIV:PLAC",pos_none);
	maakdefs("@Fx@:CHAN:DATE",pos_CHAN_DATE); maakdefs("@Fx@:CHAN:NOTE",pos_CHAN_NOTE);
	maakdefs("@Fx@:TYPE",pos_none);
	maakdefs("@Fx@:_STRT:DATE",pos_none);
	maakdefs("@Fx@:HUSB",-1); maakdefs("@Fx@:WIFE",-1);
	maakdefs("@Fx@:CHIL",-1); maakdefs("@Fx@:CHIL:ADOP",pos_none);
	maakdefs("@Fx@:OBJE",-1); maakdefs("@Fx@:OBJE:FORM",-1); maakdefs("@Fx@:OBJE:FILE",-1);
	maakdefs("@Fx@:REFN",pos_REFN);
	maakdefs("@Fx@:SOUR",pos_SOUR);
	maakdefs("@Fx@:EVEN",pos_none); maakdefs("@Fx@:EVEN:TYPE",pos_none); maakdefs("@Fx@:EVEN:PLAC",pos_none);
	maakdefs("@Fx@:NOTE",pos_NOTE); maakdefs("@Fx@:NOTE:CONT",pos_NOTE); maakdefs("@Fx@:NOTE:CONC",pos_NOTE);

global $nrcode, $arcode, $ar1content;
$arcode = array();
$arcontent= array();

	get_patriarch_list();
	$myrecord= 0;
	$ct= count($myindilist);
	sort_patriarch_list();

print_header($pgv_lang["excel_list"]);
print "\n\t<center><h2>".$pgv_lang["excel_list"]."</h2>\n\t";
print "</center>";

//--print ("aantal namen=".$ct."<br />");
	roots2number();
	error_reporting(E_ALL ^E_NOTICE);
	roots2excel();
	source2excel();

print "\n\t\t</td>\n\t\t</tr>\n\t</table></center>";
print "<br />";
print_footer();

?>
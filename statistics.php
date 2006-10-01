<?php
/**
 * Creates some statistics out of the GEDCOM information.
 * We will start with the following possibilities
 * number of persons -> periodes of 10 years from 1700-2010
 * age -> periodes of 10 years (different for 0-1,1-5,5-10,10-20 etc)
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
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
 * @version $Id: statistics.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
 */
require("config.php");

function get_person()
{
$myindilist= array();
$keys= array();
$values= array();
$dates= array();
$families= array();
global $nrfam, $famgeg, $famgeg1, $nrpers, $persgeg, $persgeg1,$key2ind,$nrman,$nrvrouw;
global $match1,$match2;

	$myindilist= get_indi_list();
	$keys = array_keys($myindilist);
	$values = array_values($myindilist);

	$nrpers= count($myindilist);
	$nrman=0; $nrvrouw=0;
	for($i=0; $i<$nrpers; $i++)
	{
		$value = $values[$i];
		$key = $keys[$i];
		$deathdate="";
		$birthdate="";
		$sex= "";
		$indirec= find_person_record($key);
		if (dateplace($indirec,"1 BIRT")!==false)
			{$birthdate= $match1[1]; $birthplace=$match2[1];}
//--	print ("geboorte:".$birthdate."--".$birthplace."<br>");
		if (dateplace($indirec,"1 DEAT")!==false)
			{$deathdate= $match1[1]; $deathplace=$match2[1];}
//-- print ("overleden:".$deathdate."--".$deathplace."<br>");
		if (stringinfo($indirec,"1 SEX") !==false)
			{	$sex= 0;
				if ($match1[1] == "M") {$sex= 1; $nrman++;}
				if ($match1[1] == "F") {$sex= 2; $nrvrouw++;}
			}
 //--print ("sexe=".$match1[1].":".$sex."<br>");

//-- get the marriage date of (the first) marriage.

		$ybirth= -1; $mbirth= -1;
		$ydeath= -1; $mdeath= -1;
		if ($birthdate !== "")
		{
			$dates= parse_date($birthdate);
// the parse_date function is in function.php
$ik=0; $mrk= "  :  ";
//-- print "gegevens b/m=" . $key . $mrk . $birthdate . $mrk . $dates[$ik]["day"] . $mrk . $dates[$ik]["mon"] . $mrk . $dates[$ik]["year"] . $mrk . $dates[$ik]["ext"] ;
			if ($dates[0]["ext"] == "")
			{	$ybirth= $dates[0]["year"];
				$mbirth= $dates[0]["mon"];
//--print "gevonden jaar en maand" . $birthdate . ":" .$ybirth . ":" . $mbirth . ":<br>";
			}
		}

		if ($deathdate !== "")
		{
			$dates= parse_date($deathdate);
// the parse_date function is in function.php
$ik=0; $mrk= "  :  ";
//-- print "====" . $mrk . $deathdate . $mrk . $dates[$ik]["day"] . $mrk . $dates[$ik]["mon"] . $mrk . $dates[$ik]["year"] . $mrk . $dates[$ik]["ext"] . "<br>" ;
			if ($dates[0]["ext"] == "")
			{	$ydeath= $dates[0]["year"];
				$mdeath= $dates[0]["mon"];
			}
		}
//-- else {print "==== no deathdate<br>";}

		$families= find_sfamily_ids($key); //-- get the number of marriages of this person.
//--print "families:";
//--if (isset($families)) { print_r($families);}
//--print ":einde<br>";
		$persgeg[$i]["key"]= $key;
		$key2ind[$key]= $i;
		$persgeg[$i]["ybirth"]= $ybirth;
		$persgeg[$i]["mbirth"]= $mbirth;
		$persgeg[$i]["ydeath"]= $ydeath;
		$persgeg[$i]["mdeath"]= $mdeath;
		$persgeg1[$i]["arfams"]= $families;
		$persgeg[$i]["sex"]= $sex;
	}
}

function complete_data()
{
// fill in the first marriages instead of the keys.
global $nrfam, $famgeg, $famgeg1, $nrpers, $persgeg, $persgeg1,$key2ind,$nrman,$nrvrouw;
$childs= array();
$families= array();

//look in the persgeg array for marriages that occurred
	for($i=0; $i<$nrpers; $i++)
	{
		$families= $persgeg1[$i]["arfams"];
		$ctc= count($families);
		$marrmonth= -1; $marryear= -1;
		$first= true;
//-- if ($ctc > 0)
//-- {print " eerste huwelijk. nr, aantal, key's:" . $i . " : " . $ctc . " : " ;}
		for($j=0; $j<$ctc; $j++)
		{
			$keyf= $families[$j]; $k= $key2ind[$keyf]; //get the family array and month/date of marriage
//-- print $keyf . " : ";
			$mm= $famgeg[$k]["mmarr"];
			$my= $famgeg[$k]["ymarr"];
			if ($first)
			{	$marryear= $my; $marrmonth= $mm; $marrkey= $keyf; $kb= $k; $first= false;}
			if (($marryear < 0) or (($my < $marryear) and ($my > 0)))
			{	$marryear= $my; $marrmonth= $mm; $marrkey= $keyf; $kb= $k; $first= false;}
		}
		$persgeg[$i]["ymarr1"]= $marryear;
		$persgeg[$i]["mmarr1"]= $marrmonth;
		$famgeg[$kb]["ymarr1"]= $marryear;
		$famgeg[$kb]["mmarr1"]= $marrmonth;
//-- if ($ctc > 0)
//-- {print " keuze=:" . $kb . ":". $marrkey . " : " . $marryear . " : " . $marrmonth . "<br>";}
	}
	for($i=0; $i<$nrfam; $i++)
	{
		$childs= $famgeg1[$i]["arfamc"];
		$ctc= count($childs);
		$birthmonth= -1; $birthyear= -1; $sex=3;
		$first= true;
//-- if ($ctc > 0)
//-- {print " eerste kind. nr, aantal, key's:" . $i . " : " . $ctc . " : " ;}
		for($j=0; $j<$ctc; $j++)
		{
			$key= $childs[$j]; $k= $key2ind[$key];
//-- print $key . ":";
			$bm= $persgeg[$k]["mbirth"];
			$by= $persgeg[$k]["ybirth"];
			$sex= $persgeg[$k]["sex"];
			if ($first)
			{	$birthyear= $by; $birthmonth= $bm; $childkey= $key; $sex1= $sex; $first= false;}
			if (($birthyear < 0) or (($by < $birthyear) and ($by > 0)))
			{	$birthyear= $by; $birthmonth= $bm; $childkey= $key; $sex1= $sex; $first= false;}
//--{print " loop gevonden:" . $key . " : " . $sex . " : " . $by . " : " . $bm . "<br>";}
		}
		$famgeg[$i]["sex1"]= $sex1;
		$famgeg[$i]["ybirth1"]= $birthyear;
		$famgeg[$i]["mbirth1"]= $birthmonth;
		$persgeg[$k]["ybirth1"]= $birthyear;
		$persgeg[$k]["mbirth1"]= $birthmonth;
//--if ($ctc > 0)
//--{print " gevonden:" . $childkey . " : " . $sex1 . " : " . $birthyear . " : " . $birthmonth . "<br>";}
	}
}


function get_family()
{
$myfamlist= array();
$keys= array();
$values= array();
$parents=array();
global $nrfam, $famgeg, $famgeg1, $nrpers, $persgeg, $persgeg1,$key2ind,$nrman,$nrvrouw;
global $match1,$match2;

$dates= array();
	$myfamlist= get_fam_list();
	$nrfam= count($myfamlist);
	$keys = array_keys($myfamlist);
	$values = array_values($myfamlist);

	for($i=0; $i<$nrfam; $i++)
	{
		$value = $values[$i];
		$key = $keys[$i];
		$marriagedate=""; $ymarr= -1; $mmarr= -1;
		$divorcedate= ""; $ydiv= -1; $mdiv= -1;
		$indirec= find_family_record($key);
//--	print("famrec:" . $key . ":" . $indirec . "<BR>");
		if (dateplace($indirec,"1 MARR")!==false)
			{$marriagedate= $match1[1]; $marriageplace=$match2[1]; $sex=1;}
		else
		if (dateplace($indirec,"1 MARS")!==false)
			{$marriagedate= $match1[1]; $marriageplace=$match2[1]; $sex=0;}
//--	 print ("gehuwd:".$marriagedate."--".$marriageplace."<br>");
		if (dateplace($indirec,"1 DIV")!==false)
			{$divorcedate= $match1[1]; $divorceplace=$match2[1];}
		if ($marriagedate !== "")
		{
			$dates= parse_date($marriagedate);
// the parse_date function is in function.php
$ik=0; $mrk= "  :  ";
//-- print "marriage, nr, key=" .$i . $mrk . $key . $mrk . $marriagedate . $mrk . $dates[$ik]["day"] . $mrk . $dates[$ik]["mon"] . $mrk . $dates[$ik]["year"] . $mrk . $dates[$ik]["ext"] ;
//--	==== beware that every about 1850 means that the value will be set to unidentified == -1 ======
			if ($dates[0]["ext"] == "")
			{	$ymarr= $dates[0]["year"];
				$mmarr= $dates[0]["mon"];
			}
		}
		if ($divorcedate !== "")
		{
			$dates= parse_date($divorcedate);
// the parse_date function is in function.php
$ik=0; $mrk= "  :  ";
//-- print "===divorce=" . $mrk . $divorcedate . $mrk . $dates[$ik]["day"] . $mrk . $dates[$ik]["mon"] . $mrk . $dates[$ik]["year"] . $mrk . $dates[$ik]["ext"] ;
//		$ydiv= substr($divorcedate,6,4);
//		$mdiv= substr($divorcedate,3,2);
			if ($dates[0]["ext"] == "")
			{	$ydiv= $dates[0]["year"];
				$mdiv= $dates[0]["mon"];
			}

		}
		$parents= find_parents($key);
//--print ("parents zijn:".$parents["HUSB"].":".$parents["WIFE"]."<BR>");
		$xfather= $parents["HUSB"]; $xmother= $parents["WIFE"];

//--	check if divorcedate exists otherwise get deadthdate from husband or wife
		if ($ydiv !== "")
		{
			$ydeathf= ""; $ydeathm= "";
			if ($xfather !== "") {$indf= $key2ind[$xfather]; $ydeathf= $persgeg[$indf]["ydeath"];}
			if ($xmother !== "") {$indm= $key2ind[$xmother]; $ydeathm= $persgeg[$indm]["ydeath"];}
//--print(" keys en index father mother=" . $indf . ":" . $xfather . ":" . $indm . ":" . $xmother . "<BR>");
			if (($ydeathf !== "") and ($ydeathm !== ""))
			{	if ($ydeathf > $ydeathm)
				{	$ydiv= $ydeathf; $mdiv= $persgeg[$indf]["mdeath"];}
				else
				{	$ydiv= $ydeathm; $mdiv= $persgeg[$indm]["mdeath"];};
			}
		};
		$childs= preg_match_all("/1\s*CHIL\s*@(.*)@/",$indirec,$match1,PREG_SET_ORDER);
//-- print "===kinderen:" . "Aantal=" . $childs . "=nrs=";
//--	for($k=0; $k<$childs; $k++) {print $match1[$k][0] . " : ";} print "<BR>";
$children= array();
		for($k=0; $k<$childs; $k++)
		{	$children[$k]= $match1[$k][1]; //--
 $children[$k] . " : ";
		}


		$famgeg[$i]["key"]= $key;
		$key2ind[$key]= $i;
		$famgeg[$i]["ymarr"]= $ymarr;
		$famgeg[$i]["mmarr"]= $mmarr;
		$famgeg[$i]["ydiv"]= $ydiv;
		$famgeg[$i]["mdiv"]= $mdiv;
		$famgeg[$i]["childs"]= $childs;
		$famgeg1[$i]["arfamc"]= $children;
		$famgeg[$i]["male"]= $xfather;
		$famgeg[$i]["female"]= $xmother;
	//-- print "==ouders==:" . $xfather . ":" . $xmother . "==gehuwd==" . $ymarr . "-" . $mmarr . "<BR>";
	}
}

function stringinfo($indirec,$lookfor)
//look for a starting string in the gedcom record of a person
//then take the stripped comment
{
//-- print "start stringinfo<br>";
global $match1,$match2;
	$birthrec = get_sub_record(1, $lookfor, $indirec);
	$match1[1]="";
	$match2[1]="";
	if ($birthrec!==false)
		{
			$dct = preg_match("/".$lookfor." (.*)/", $birthrec, $match1);
			if ($dct < 1) {$match1[1]="";}
//--print("stringinfo:".$dct.":".$lookfor.":".$birthrec.":".$match1[1].":<BR>");
			$match1[1]= trim($match1[1]);
			return true;
		}
	else 	{	return false;}
}


function dateplace($indirec,$lookfor)
//--look for a starting string in the gedcom record of a person or family
//--then find the DATE and PLACE variables
{
//-- print "start dateplace<br>";
global $match1,$match2;

	$birthrec = get_sub_record(1, $lookfor, $indirec);
//--	$birthrec= $indirec;
//-- You need to get the subrecord in order not to mistaken by another key with same subkeys.
	$match1[1]="";
	$match2[1]="";
//-- print "dataplace:" . $lookfor . "<BR>" . $birthrec . "<br>" . $indirec . "<br>";
	if ($birthrec!== "")
		{
			$dct = preg_match("/2 DATE (.*)/", $birthrec, $match1);
//-- if ($dct > 0) {print("birthrec + date" . $birthrec . ":::" . $match1[1] . "<BR>");};
//--			if ($dct>0) $match1[1]= get_number_date($match1[1]);
//--			$pct = preg_match("/2 PLAC (.*)/", $birthrec, $match2);
//--			if ($pct>0) print " -- ".$match2[1]."<br>";
			if ($dct > 0) {$match1[1]= trim($match1[1]);} else {$match1[1]="";}
//--			if ($pct > 0) {$match2[1]= trim($match2[1]);} else {$match2[1]="";}
			return true;
		}
	else 	{	return false;}
}

function put_plot_data()
{
	global $GEDCOM, $GEDCOMS, $INDEX_DIRECTORY;
global $nrfam, $famgeg, $nrpers, $persgeg,$key2ind,$nrman,$nrvrouw;
global $pgv_lang;

	$indexfile = $INDEX_DIRECTORY.$GEDCOM."_statistiek.php";
	$FP = fopen($indexfile, "wb");
	if (!$FP)
	{
		print "<font class=\"error\">" . $pgv_lang["statutci"] . "</font>";
		exit;
	}

	/*$lists = array("famgeg"=>$famgeg, "persgeg"=>$persgeg, "key2ind"=>$key2ind);
	fwrite($FP, serialize($lists));
	*/
	fwrite($FP, 'a:3:{s:6:"famgeg";');
	fwrite($FP, serialize($famgeg));
	fwrite($FP, 's:7:"persgeg";');
	fwrite($FP, serialize($persgeg));
	fwrite($FP, 's:7:"key2ind";');
	fwrite($FP, serialize($key2ind));
	fwrite($FP, '}');
	fclose($FP);
	$logline = AddToLog($GEDCOM."_statistiek.php updated by >".getUserName()."<");
 	if (!empty($COMMIT_COMMAND)) check_in($logline, $GEDCOM."_statistiek.php", $INDEX_DIRECTORY);	
}

//--	========= start of main program =========

$famgeg = array();
$persgeg= array();
$famgeg1 = array();
$persgeg1= array();
$key2ind= array();
$match1= array();
$match2= array();
global $nrfam, $famgeg, $nrpers, $persgeg,$key2ind,$nrman,$nrvrouw;
global $match1,$match2;

	print_header($pgv_lang["statistics"]);
//-- You should install jpgraph routines on your computer. I implemented them in gaphic/src
//-- Please check this with any availability test

//-- The info below comes from www.php.net when looking at functions

	$mypath= ini_get("include_path");
//--	On some servers the include path does not support the (current) directory from the calling routine
//--	ini_set("include_path", $mypath);

	$GDcheck= 1; $JPcheck= 1;
//-- Check if GD library is loaded
	if (!extension_loaded('gd')) {$GDcheck= 0;}
//-- Check if JPgraph modules are available
	if ((!file_exists( "jpgraph/jpgraph.php")) or
	    (!file_exists( "jpgraph/jpgraph_line.php")) or
	    (!file_exists( "jpgraph/jpgraph_bar.php")))
		{$JPcheck= 0;}

	if (($GDcheck == 0) or ($JPcheck == 0))
	{
		if ($GDcheck == 0)
			{print $pgv_lang["stplGDno"] . "<BR>";}
		if ($JPcheck == 0)
			{print $pgv_lang["stpljpgraphno"] . "<BR>";}
		exit;
	}

	if (!isset($_SESSION[$GEDCOM."nrpers"]))
		{	$nrpers=0;}
		else
		{	$nrpers=$_SESSION[$GEDCOM . "nrpers"];
			$nrfam=$_SESSION[$GEDCOM . "nrfam"];
			$nrman=$_SESSION[$GEDCOM . "nrman"];
			$nrvrouw=$_SESSION[$GEDCOM . "nrvrouw"];
		}
//-- if nrpers<1 means there is no intermediate file yet set in this session
	if ($nrpers < 1)
	{
		get_person();
		get_family();
		complete_data();
		put_plot_data();
	}
	print "\t<left><h3>".$pgv_lang["statistics"]."</h3>\t";
	print ($pgv_lang["statnnames"].$nrpers."<BR>");
	print ($pgv_lang["statnfam"].$nrfam."<BR>");
	print ($pgv_lang["statnmale"].$nrman."<BR>");
	print ($pgv_lang["statnfemale"].$nrvrouw."<BR>");
	$_SESSION[$GEDCOM . "nrpers"]= $nrpers;
	$_SESSION[$GEDCOM . "nrfam"]= $nrfam;
	$_SESSION[$GEDCOM . "nrman"]= $nrman;
	$_SESSION[$GEDCOM . "nrvrouw"]= $nrvrouw;

{
	?>
	<script type="text/javascript">
	<!--
	var pasteto;
	function open_find(textbox)
	{
		pasteto = textbox;
//--<?php print "textbox".textbox."<br>"; ?>
		findwin = window.open('statistiekplot.php', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	function paste_id(value)
	{
		pasteto.value=value;
	}
	//-->
	</script>

<?php
if (!isset($plottype)) $plottype=0;

?>
	<h3><?php print $pgv_lang["statvars"]; ?> <?php print_help_link("stat_help","qm"); ?> </h3>
	<form method="post" name="form" action="statisticsplot.php?action=newform">
	<input type="hidden" name="action" value="update">

	<table class="facts_table">
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statlxa"]; ?> </td>
	<td class="optionbox"> <select name="x-as">
		<option value= "11" selected="selected"><?php print $pgv_lang["stat_11_mb"]; ?>
		<option value= "12"> <?php print $pgv_lang["stat_12_md"]; ?>
		<option value= "13"> <?php print $pgv_lang["stat_13_mm"]; ?>
		<option value= "14"> <?php print $pgv_lang["stat_14_mb1"]; ?>
		<option value= "15"> <?php print $pgv_lang["stat_15_mm1"]; ?>
		<option value= "16"> <?php print $pgv_lang["stat_16_mmb"] . "<i>" . $pgv_lang["stat_gmx"] ."</i>"; ?>
		<option value= "17"> <?php print $pgv_lang["stat_17_arb"] . "<i>" . $pgv_lang["stat_gax"] ."</i>"; ?>
		<option value= "18"> <?php print $pgv_lang["stat_18_ard"] . "<i>" . $pgv_lang["stat_gax"] ."</i>";  ?>
		<option value= "19"> <?php print $pgv_lang["stat_19_arm"] . "<i>" . $pgv_lang["stat_gax"] ."</i>";  ?>
		<option value= "20"> <?php print $pgv_lang["stat_20_arm1"] . "<i>" . $pgv_lang["stat_gax"] ."</i>";  ?>
		<option value= "21"> <?php print $pgv_lang["stat_21_nok"] . "<i>" . $pgv_lang["stat_gnx"] ."</i>";  ?>
	</select>
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statlya"]; ?>  </td>
	<td class="optionbox"> <select name="y-as">
		<option value= "201" selected="selected"> <?php print $pgv_lang["stat_201_num"]; ?>
		<option value= "202"> <?php print $pgv_lang["stat_202_perc"]; ?>
	</select>
	</td>
	</tr>
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statlza"]; ?>  </td>
	<td class="optionbox"> <select name="z-as">
		<option value= "300"> <?php print $pgv_lang["stat_300_none"]; ?>
		<option value= "301"> <?php print $pgv_lang["stat_301_mf"]; ?>
		<option value= "302" selected="selected"> <?php print $pgv_lang["stat_302_cgp"]; ?>
	</select>
	</td>
	</tr>
	</table>
<br>

<?php
print "<h3>" . $pgv_lang["statmess1"] . "</h3>";
/*
#	<tr>
#	<td> <?php print $pgv_lang["statrfpx"]; ?> </td>
#	<td> <input type="text" name="grenzen-jaren" value="1700,1800,1850,1900,1950,1980,2000"
#			size="60" onfocus="getHelp('periode_help');">
#	</td>
#	</tr>
*/
?>

	<table class="facts_table">
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statar_xgl"]; ?> </td>
	<td class="optionbox"> <input type="text" name="xas-grenzen-leeftijden" value="1,5,10,20,30,40,50,60,70,80,90"
			size="60" onfocus="getHelp('periode_help');">
	</td>
	</tr>
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statar_xgm"]; ?> </td>
	<td class="optionbox"> <input type="text" name="xas-grenzen-maanden" value="-24,-12,0,8,12,18,24,48"
			size="60" onfocus="getHelp('periode_help');">
	</td>
	</tr>
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statar_xga"]; ?> </td>
	<td class="optionbox"> <input type="text" name="xas-grenzen-aantallen" value="1,2,3,4,5,6,7,8,9,10"
			size="60" onfocus="getHelp('periode_help');">
	</td>
	</tr>
	<tr>
	<td class="descriptionbox width20 wrap"> <?php print $pgv_lang["statar_zgp"]; ?> </td>
	<td class="optionbox"> <input type="text" name="zas-grenzen-periode" value="1800,1850,1900,1950,1980"
			size="60" onfocus="getHelp('periode_help');">
	</td>
	</tr>
	</table>

</td></tr></table></center>
<br>
<input type="submit" value="<?php print $pgv_lang["statsubmit"]; ?> " onclick="closeHelp();">
<input type="reset"  value=" <?php print $pgv_lang["statreset"]; ?> "><br>
</form>
<?php
}

//--print "plottype=".$plottype."<br>";
$_SESSION["plottype"]=$plottype;


print "<br>";
print_footer();

?>
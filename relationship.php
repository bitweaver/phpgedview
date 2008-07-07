<?php
/**
 * Calculates the relationship between two individuals in the gedcom
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * This Page Is Valid XHTML 1.0 Transitional! > 20 August 2005
 *
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id: relationship.php,v 1.4 2008/07/07 18:01:12 lsces Exp $
 */

/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );
// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );

include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );

$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/functions_charts.php");
require_once("includes/person_class.php");

function getRelationshipSentence($node, $pid1, $pid2)
{
    global $pgv_lang, $lang_short_cut, $LANGUAGE;
    $relationshipDescription = false;
    $sentence = false;
    $started = false;
    $finished = false;
    $numberOfSiblings = 0;
    $generationsOlder = 0;
    $generationsYounger = 0;
    $sosa = 1;
	$bosa = 1;
    $numberOfSpouses = 0;
    $lastRelationshipIsSpouse = false;
    $lastRelationshipIsSibling = false;
    $lastRelationshipButOneIsSibling = false;
    $checkFirstRelationship = false;
    $firstRelationshipIsSpouse = false;
    $siblingIsSister = false;


    // sanity check - helps to prevent the possibility of recursing too deeply
    if($pid1 == $pid2)
        return false;

    foreach($node["path"] as $index=>$pid)
    {
        // only start looking for relationships from the first pid passed in
        if($pid == $pid1)
        {
            $started = true;
		    $checkFirstRelationship = true;
            continue;
        }

        if($started)
        {
            $lastRelationshipIsSpouse = false;
		    $lastRelationshipButOneIsSibling = $lastRelationshipIsSibling;
		    $lastRelationshipIsSibling = false;
            // look to see if we can find a relationship
            switch( $node["relations"][$index])
            {
                case "self":
                    break;

                case "sister":
				    $siblingIsSister = true;
                case "brother":
                    $numberOfSiblings++;
				    $lastRelationshipIsSibling = true;
                    break;

                case "mother":
                    $generationsOlder++;
                    $sosa = $sosa * 2 + 1;
                    break;

                case "father":
                    $generationsOlder++;
                    $sosa = $sosa * 2;
                    break;

                case "son":
                    $generationsYounger++;
					$bosa = $bosa * 2;
                    break;

                case "daughter":
                    $generationsYounger++;
					$bosa = $bosa * 2 + 1;
                    break;

                case "husband":
                case "wife":
                    $numberOfSpouses++;
                    $lastRelationshipIsSpouse = true;
				    if($checkFirstRelationship)
					{
					    $firstRelationshipIsSpouse = true;
					}
                    break;
            }
		    $checkFirstRelationship = false;
        }

        if($pid == $pid2)
        {
            // we have found the second individual - look no further
            $finished = true;
            break;
        }

    }
    // sanity check
    if(!$started || !$finished)
    {
        // passed in pid's are not found in the array!!!
        return false;
    }

    $person1 = find_person_record($pid1);
    $person2 = find_person_record($pid2);
    $mf = "NN";
    if (preg_match("/1 SEX F/", $person2, $smatch)>0) $mf="F";
    if (preg_match("/1 SEX M/", $person2, $smatch)>0) $mf="M";

    // now look to see if we can find some text to describe the relationship
    //check if relationship is parent
    if ($numberOfSpouses == 1 && $numberOfSiblings == 0 && $generationsOlder == 0 && $generationsYounger == 0)
    {
        // check for spouse
        if ($mf=="F")
        {
            if (isset($pgv_lang["wife"]))
                $relationshipDescription = $pgv_lang["wife"];
        }
        else
        {
            if (isset($pgv_lang["husband"]))
                $relationshipDescription = $pgv_lang["husband"];
        }
    }
    //check if relationship is parent in law or step parent
    else if ($numberOfSpouses == 1 && $numberOfSiblings == 0 && $generationsOlder == 1 && $generationsYounger == 0)
    {
		// is this an in-law relationship?
		if(!$lastRelationshipIsSpouse)
		{
	        if (isset($pgv_lang["mother_in_law"]) &&$mf=="F")
	        {
	            $relationshipDescription = $pgv_lang["mother_in_law"];
	        }
	        else if (isset($pgv_lang["father_in_law"]))
	        {
	            $relationshipDescription = $pgv_lang["father_in_law"];
	        }
	    }
		else // step relationship
		{
	        if (isset($pgv_lang["stepmom"]) &&$mf=="F")
	        {
	            $relationshipDescription = $pgv_lang["stepmom"];
	        }
	        else if (isset($pgv_lang["stepdad"]))
	        {
	            $relationshipDescription = $pgv_lang["stepdad"];
	        }
		}
    }
    //checks for brother in law, sister in law realtionships
    else if ($numberOfSpouses == 1 && $numberOfSiblings == 1 && $generationsYounger == 0 && $generationsOlder == 0)
    {
        if (isset($pgv_lang["sister_in_law"]) &&$mf=="F")
        {
            $relationshipDescription = $pgv_lang["sister_in_law"];
        }
        else if (isset($pgv_lang["brother_in_law"]))
        {
            $relationshipDescription = $pgv_lang["brother_in_law"];
        }
    }
    //check if relationship is child in law
    else if ($numberOfSpouses == 1 && $numberOfSiblings == 0 && $generationsOlder == 0 && $generationsYounger == 1)
    {
		// is this an in-law relationship?
		if($lastRelationshipIsSpouse)
		{
	        if (isset($pgv_lang["daughter_in_law"]) &&$mf=="F")
	        {
	            $relationshipDescription = $pgv_lang["daughter_in_law"];
	        }
	        else if (isset($pgv_lang["son_in_law"]))
	        {
	            $relationshipDescription = $pgv_lang["son_in_law"];
	        }
		}
		else // step relationship
		{
	        if (isset($pgv_lang["step_daughter"]) &&$mf=="F")
	        {
	            $relationshipDescription = $pgv_lang["step_daughter"];
	        }
	        else if (isset($pgv_lang["step_son"]))
	        {
	            $relationshipDescription = $pgv_lang["step_son"];
	        }
		}
    }
    //checks for niece/nephew relationship
    else if (($firstRelationshipIsSpouse || $numberOfSpouses == 0) && $numberOfSiblings == 1 && $generationsYounger >= 1 && $generationsOlder == 0)
    {
		if($siblingIsSister)
		{
			if (isset($pgv_lang["bosa_sisters_offspring_$bosa"]))
			{
				$relationshipDescription = $pgv_lang["bosa_sisters_offspring_$bosa"];
			}
			else
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_sisters_daughter"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_sisters_daughter"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
			    }
				else if(isset($pgv_lang["n_x_sisters_son"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_sisters_son"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
				}
			}
	    }
		else
		{
			if (isset($pgv_lang["bosa_brothers_offspring_$bosa"]))
			{
				$relationshipDescription = $pgv_lang["bosa_brothers_offspring_$bosa"];
			}
			else
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_brothers_daughter"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_brothers_daughter"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
			    }
				else if(isset($pgv_lang["n_x_brothers_son"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_brothers_son"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
				}
			}
		}
    }
	// check for step siblings
	else if($numberOfSpouses == 1 && $generationsYounger == 1 && $generationsOlder == 1 && !$firstRelationshipIsSpouse && !$lastRelationshipIsSpouse && $numberOfSiblings == 0)
	{
	        if (isset($pgv_lang["stepsister"]) &&$mf=="F")
	        {
	            $relationshipDescription = $pgv_lang["stepsister"];
	        }
	        else if (isset($pgv_lang["stepbrother"]))
	        {
	            $relationshipDescription = $pgv_lang["stepbrother"];
	        }
	}
    //checks for aunt/uncle relationship by marriage
    else if (($numberOfSiblings == 1) && ($generationsYounger == 0) && ($generationsOlder >= 1) && ($numberOfSpouses == 1) && $lastRelationshipIsSpouse && $lastRelationshipButOneIsSibling )
    {
		if ($mf=="F" && isset($pgv_lang["sosa_aunt_bm_$sosa"]))
		{
			$relationshipDescription = $pgv_lang["sosa_aunt_bm_$sosa"];
		}
		else if (isset($pgv_lang["sosa_uncle_bm_$sosa"]))
		{
			$relationshipDescription = $pgv_lang["sosa_uncle_bm_$sosa"];
		}
		else
		{
			// if line is through father
            if(floor($sosa/pow(2,$generationsOlder-1)) == 2)
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_paternal_aunt_bm"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_paternal_aunt_bm"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
			    }
				else if(isset($pgv_lang["n_x_paternal_uncle_bm"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_paternal_uncle_bm"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
				}
			}
			else
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_maternal_aunt_bm"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_maternal_aunt_bm"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
			    }
				else if(isset($pgv_lang["n_x_maternal_uncle_bm"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_maternal_uncle_bm"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
				}
			}
		}
    }
	// from here on only check for blood relatives
    else if($numberOfSpouses > 0)
    {
    }
    //check if relationship is parent or grandparent
    else if ($numberOfSiblings == 0 && $generationsOlder > 0 && $generationsYounger == 0)
    {
		// the get_sosa_name is probably the best way of getting this name
		$relationshipDescription = get_sosa_name($sosa);
	    if(($relationshipDescription == (($sosa%2) ? $pgv_lang["mother"] : $pgv_lang["father"]) . " " . floor($sosa/2))
			&& ($generationsOlder > 3))
		{
			// the sosa route didn't find a name - lets see if we can find a great grandparent this way
            if (isset($pgv_lang["n_x_great_grandmother"]) && ($mf=="F"))
			{
	            $relationshipDescription = sprintf( $pgv_lang["n_x_great_grandmother"], $generationsOlder-2);
		    }
			else if (isset($pgv_lang["n_x_great_grandfather"]))
			{
	            $relationshipDescription = sprintf( $pgv_lang["n_x_great_grandfather"], $generationsOlder-2);
			}
	    }
    }
    //checks for son/daughter and grandson/granddaughter
    else if ($numberOfSiblings == 0 && $generationsYounger > 0 && $generationsOlder == 0)
    {
		if (isset($pgv_lang["bosa_$bosa"]))
		{
			$relationshipDescription = $pgv_lang["bosa_$bosa"];
		}
		else
		{
			// if line is through son
            if(floor($bosa/pow(2,$generationsYounger-1)) == 2)
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_granddaughter_from_son"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_granddaughter_from_son"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
			    }
				else if(isset($pgv_lang["n_x_grandson_from_son"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_grandson_from_son"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
				}
			}
			else
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_granddaughter_from_daughter"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_granddaughter_from_daughter"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
			    }
				else if(isset($pgv_lang["n_x_grandson_from_daughter"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_grandson_from_daughter"], $generationsYounger, $generationsYounger-1, $generationsYounger-2);
				}
			}
		}
    }
    //checks for sibling realtionships
    else if ($numberOfSiblings == 1 && $generationsYounger == 0 && $generationsOlder == 0)
    {
        if ($mf=="F")
        {
            if (isset($pgv_lang["sister"]))
            {
                $relationshipDescription = $pgv_lang["sister"];
            }
        }
        else
        {
            if (isset($pgv_lang["brother"]))
            {
                $relationshipDescription = $pgv_lang["brother"];
            }
        }
    }
    //checks for blood aunt/uncle relationship
    else if (($numberOfSiblings == 1 && $generationsYounger == 0 && $generationsOlder >= 1) && $lastRelationshipIsSibling )
    {
		if ($mf=="F" && isset($pgv_lang["sosa_aunt_$sosa"]))
		{
			$relationshipDescription = $pgv_lang["sosa_aunt_$sosa"];
		}
		else if (isset($pgv_lang["sosa_uncle_$sosa"]))
		{
			$relationshipDescription = $pgv_lang["sosa_uncle_$sosa"];
		}
		else
		{
			// if line is through father
            if(floor($sosa/pow(2,$generationsOlder-1)) == 2)
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_paternal_aunt"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_paternal_aunt"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
			    }
				else if(isset($pgv_lang["n_x_paternal_uncle"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_paternal_uncle"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
				}
			}
			else
			{
	            if ($mf=="F" && isset($pgv_lang["n_x_maternal_aunt"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_maternal_aunt"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
			    }
				else if(isset($pgv_lang["n_x_maternal_uncle"]))
				{
				    $relationshipDescription = sprintf($pgv_lang["n_x_maternal_uncle"], $generationsOlder, $generationsOlder-1, $generationsOlder-2);
				}
			}
		}
    }
    //checks for nth cousin
    else if ($numberOfSiblings == 1 && $generationsYounger > 0 && $generationsOlder > 0 && ($generationsYounger == $generationsOlder))
    {
        $degree = $generationsYounger;
        if ($mf=="F")
        {
            if(isset($pgv_lang["female_cousin_" . $degree]) && ($pgv_lang["female_cousin_" . $degree] != ""))
            {
                $relationshipDescription = $pgv_lang["female_cousin_" . $degree];
            }
			else if(isset($pgv_lang["female_cousin_n"]) && ($pgv_lang["female_cousin_n"] != ""))
			{
                $relationshipDescription = sprintf($pgv_lang["female_cousin_n"], $degree);
			}

        }
        else
        {
            // treat unknown gender as male
            if(isset($pgv_lang["male_cousin_" . $degree]) && ($pgv_lang["male_cousin_" . $degree] != ""))
            {
                $relationshipDescription = $pgv_lang["male_cousin_" . $degree];
            }
			else if(isset($pgv_lang["male_cousin_n"]) && ($pgv_lang["male_cousin_n"] != ""))
			{
                $relationshipDescription = sprintf($pgv_lang["male_cousin_n"], $degree);
			}
        }
    }
	// Check for half sibling relationships
	else if($numberOfSpouses == 0 && $generationsYounger == 1 && $generationsOlder == 1 && $numberOfSiblings == 0 )
	{
	        if (isset($pgv_lang["halfsister"]) &&$mf=="F")
	        {
	            $relationshipDescription = $pgv_lang["halfsister"];
	        }
	        else if (isset($pgv_lang["halfbrother"]))
	        {
	            $relationshipDescription = $pgv_lang["halfbrother"];
	        }
	}

	$langStr = str_replace("-", "_", $lang_short_cut[$LANGUAGE]);
    // check for language specific override of relationship
    $getLanguageSpecificRelationship = "getRelationshipText_" . $langStr;
    if(function_exists($getLanguageSpecificRelationship ))
    {
        $relationshipDescription = $getLanguageSpecificRelationship($relationshipDescription, $node, $pid1, $pid2);
    }

    // check for language specific override of relationship
    // functions to get locale specific names (eg Finnish genitive)
    $getLanguageSpecificName = "getFirstRelationsName_" . $langStr;
    if(function_exists($getLanguageSpecificName ))
    {
        $pid1Name = PrintReady($getLanguageSpecificName($pid1));
    }
    else
    {
        $pid1Name = PrintReady(get_person_name($pid1));         
    }

    $getLanguageSpecificName = "getSecondRelationsName_" . $langStr;
    if(function_exists($getLanguageSpecificName ))
    {
        $pid2Name = PrintReady($getLanguageSpecificName($pid2));        
    }
    else
    {
        $pid2Name = PrintReady(get_person_name($pid2));          
    }

    if($relationshipDescription != false)
    {
        if(($mf=="F") && isset($pgv_lang["relationship_female_1_is_the_2_of_3"]))
        {
            $sentence = sprintf($pgv_lang["relationship_female_1_is_the_2_of_3"], $pid2Name, $relationshipDescription, $pid1Name);
        }
        else
        if(isset($pgv_lang["relationship_male_1_is_the_2_of_3"]))
        {
            $sentence = sprintf($pgv_lang["relationship_male_1_is_the_2_of_3"], $pid2Name, $relationshipDescription, $pid1Name);
        }
    }
    else if(($pid1 == $node["path"][0]) && ($pid2 == $node["path"][count($node["path"])-1]) && $firstRelationshipIsSpouse )
    {
        // if no relationship was found it may be nice to check if this is a spouses relation
        $firstRelationPid = $node["path"][1];
        //get their relationship
        $sentence1 = getRelationshipSentence($node, $pid1, $firstRelationPid);
        //    get the relationship beteeen them and %pid2
        $sentence2 = getRelationshipSentence($node, $firstRelationPid, $pid2);
        //    if both the above gave strings then concatenate
        if(($sentence1 != false)&&($sentence2 != false))
        {
            $sentence = $sentence1 . "<br/>" . $sentence2;
        }
    }
    return $sentence;
}

$show_full=$PEDIGREE_FULL_DETAILS;
if (isset($_REQUEST['show_full'])) $show_full = $_REQUEST['show_full']; 
if (!isset($_REQUEST['path_to_find'])){
	$path_to_find = 0;
	$pretty = 1;
	unset($_SESSION["relationships"]);
}
else $path_to_find = $_REQUEST['path_to_find'];
if ($path_to_find == -1){
	$path_to_find = 0;
	unset($_SESSION["relationships"]);
}

//-- previously these variables were set in theme.php, now they are no longer required to be set there
$Dbasexoffset = 0;
$Dbaseyoffset = 0;

if ($show_full==false) {
	$Dbheight=25;
	$Dbwidth-=40;
}

$bwidth = $Dbwidth;
$bheight = $Dbheight;

$title_string = "";
if (!isset($_REQUEST['pid1'])) $pid1 = "";
else $pid1 = $_REQUEST['pid1'];
if (!isset($_REQUEST['pid2'])) $pid2 = "";
else $pid2 = $_REQUEST['pid2'];
if (!isset($_REQUEST['followspouse'])) $followspouse = 0;
else $followspouse = $_REQUEST['followspouse'];
if (!isset($_REQUEST['pretty'])) $pretty = 0;
else $pretty = $_REQUEST['pretty'];
if (!isset($_REQUEST['asc'])) $asc=1;
else $asc = $_REQUEST['asc'];
if ($asc=="") $asc=1;
if (empty($pid1)) {
	$followspouse = 1;
	$pretty = 1;
}
$check_node = true;
$disp = true;

//-- cleanup user input
$pid1 = clean_input($pid1);
$pid2 = clean_input($pid2);

$title_string .= $pgv_lang["relationship_chart"];
// -- print html header information
print_header($title_string);
if (!empty($pid1)) {
	//-- check if the id is valid
	$indirec = Person::getInstance($pid1);
	if (!$indirec && $GEDCOM_ID_PREFIX) {
		// Allow user to specify person without the prefix
		$pid1=$GEDCOM_ID_PREFIX.$pid1;
		$indirec=Person::getInstance($pid1);
	}
	if (!$indirec) $pid1 = "";
	if ((!displayDetailsByID($pid1))&&(!showLivingNameByID($pid1))) $title_string .= ": ".$pgv_lang["private"];
	else $title_string .= ":<br />".get_person_name($pid1);
	if (!empty($_SESSION["pid1"]) && ($_SESSION["pid1"]!=$pid1)) {
		unset($_SESSION["relationships"]);
		$path_to_find=0;
	}
}
if (!empty($pid2)) {
	//-- check if the id is valid
	$indirec = find_person_record($pid2);
	if (!$indirec && $GEDCOM_ID_PREFIX) {
		// Allow user to specify person without the prefix
		$pid2=$GEDCOM_ID_PREFIX.$pid2;
		$indirec = find_person_record($pid2);
	}
	if (!$indirec) $pid2 = "";
	if ((!displayDetailsByID($pid2))&&(!showLivingNameByID($pid2))) $title_string .= " - " . $pgv_lang["private"]." ";
	else $title_string .= " ".$pgv_lang["and"]." ".get_person_name($pid2)." ";
	if (!empty($_SESSION["pid2"]) && ($_SESSION["pid2"]!=$pid2)) {
		unset($_SESSION["relationships"]);
		$path_to_find=0;
	}
}
//	print_help_link("relationship_help", "page_help");
?>
<script language="JavaScript" type="text/javascript">
var pastefield;
function paste_id(value) {
	pastefield.value=value;
}
</script>
<div id="relationship_chart_options<?php print ($TEXT_DIRECTION=="ltr")?"":"_rtl";?>" style="position: relative; z-index:90; width:98%;">
<h2><?php print PrintReady($title_string);?></h2><br />
<!-- // Print the form to change the number of displayed generations -->
<?php
if ($view!="preview") {
	$Dbaseyoffset += 110; ?>
	<form name="people" method="get" action="relationship.php">
	<input type="hidden" name="path_to_find" value="<?php print $path_to_find ?>" />

	<table class="list_table <?php print $TEXT_DIRECTION ?>" style="align:<?php print ($TEXT_DIRECTION=="ltr"?"left":"right");?>; margin:0;">

	<!-- // Relationship header -->
	<tr><td colspan="2" class="topbottombar center">
	<?php print $pgv_lang["relationship_chart"]?>
	</td>

	<!-- // Empty space -->
	<td>&nbsp;</td>

	<!-- // Options header -->
	<td colspan="2" class="topbottombar center">
	<?php print $pgv_lang["options"]?>
	</td></tr>

	<!-- // Person 1 -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("relationship_id_help", "qm");
	print $pgv_lang["person1"]?>
	</td>
	<td class="optionbox vmiddle">
	<input tabindex="1" class="pedigree_form" type="text" name="pid1" id="pid1" size="3" value="<?php print $pid1 ?>" />
	<?php
	print_findindi_link("pid1","");?>
        </td>

		<!-- // Empty space -->
	<td></td>

	<!-- // Show details -->
	<td class="descriptionbox">
	<?php
	print_help_link("show_full_help", "qm");
	print $pgv_lang["show_details"];?>
	</td>
	<td class="optionbox vmiddle">
	<input type="hidden" name="show_full" value="<?php print $show_full ?>" />
		<?php
	if (!$pretty && $asc==-1) print "<input type=\"hidden\" name=\"asc\" value=\"$asc\" />";
	print "<input tabindex=\"3\" type=\"checkbox\" name=\"showfull\" value=\"0\"";
	if ($show_full) print " checked=\"checked\"";
	print " onclick=\"document.people.show_full.value='".(!$show_full)."';\" />";?>
	</td></tr>

	<!-- // Person 2 -->
	<tr><td class="descriptionbox">
	<?php
	print_help_link("relationship_id_help", "qm");
	print $pgv_lang["person2"]?>
	</td>
	<td class="optionbox vmiddle">
	<input tabindex="2" class="pedigree_form" type="text" name="pid2" id="pid2" size="3" value="<?php print $pid2 ?>" />
		<?php
		print_findindi_link("pid2","");?>
        </td>

		<!-- // Empty space -->
	<td>&nbsp;</td>

	<!-- // Line up generations -->
	<td class="descriptionbox">
	<?php
	print_help_link("line_up_generations_help", "qm");
	print $pgv_lang["line_up_generations"]?>
	</td>
	<td class="optionbox">
	<input tabindex="5" type="checkbox" name="pretty" value="2"
	<?php
	if ($pretty) print " checked=\"checked\"";
	print " onclick=\"expand_layer('oldtop1'); expand_layer('oldtop2');\"" ?> />
	</td></tr>

	<!-- // Empty line -->
	<tr><td class="descriptionbox">&nbsp;</td>
	<td class="optionbox">&nbsp;</td>

	<!-- // Empty space -->
	<td>&nbsp;</td>

	<!-- // Show oldest top -->
	<td class="descriptionbox">
	<div id="oldtop1" style="display:
	<?php
	if ($pretty) print "block";
	else print "none";
	?>">
	<?php
	print_help_link("oldest_top_help", "qm");
	print $pgv_lang["oldest_top"];
	?>
	</div>
	</td><td class="optionbox">
	<div id="oldtop2" style="display:
	<?php
	if ($pretty) print "block";
	else print "none";?>">
	<input tabindex="4" type="checkbox" name="asc" value="-1"
	<?php
	if ($asc==-1) print " checked=\"checked\"";?>
	/>
	</div></td></tr>

	<!-- // Show path -->
	<tr><td class="descriptionbox">
	<?php $pass = false;
	if ((isset($_SESSION["relationships"]))&&((!empty($pid1))&&(!empty($pid2)))) {
		$pass = true;
		$i=0;
		$new_path=true;
		if (isset($_SESSION["relationships"][$path_to_find])) $node = $_SESSION["relationships"][$path_to_find];
		else $node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
		if (!$node){
			$path_to_find--;
			$check_node=$node;
		}
		foreach($_SESSION["relationships"] as $indexval => $node) {
			if ($i==0) print $pgv_lang["show_path"].": </td><td class=\"list_value\" style=\"padding: 3px;\">";
			if ($i>0) print " | ";
			if ($i==$path_to_find){
				print "<span class=\"error\" style=\"valign: middle\">".($i+1)."</span>";
				$new_path=false;
			}
			else {
				print "<a href=\"relationship.php?pid1=$pid1&amp;pid2=$pid2&amp;path_to_find=$i&amp;followspouse=$followspouse&amp;pretty=$pretty&amp;show_full=$show_full&amp;asc=$asc\">".($i+1)."</a>\n";
			}
			$i++;
		}
		if (($new_path)&&($path_to_find<$i+1)&&($check_node)) print " | <span class=\"error\">".($i+1)."</span>";
		print "</td>";
	}
	else {
		if ((!empty($pid1))&&(!empty($pid2))) {
			if ((!displayDetailsByID($pid1))&&(!showLivingNameByID($pid1))) $disp = false;
			else if ((!displayDetailsByID($pid2))&&(!showLivingNameByID($pid2))) $disp = false;
			if ($disp) {
				print $pgv_lang["show_path"].": </td>";
				print "\n\t\t<td class=\"optionbox\">";
				print " <span class=\"error vmmiddle\">";
				$check_node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
				print ($check_node?"1":"&nbsp;".$pgv_lang["no_results"])."</span></td>";
				$prt = true;
			}
		}
		if (!isset($prt)) print "&nbsp;</td><td class=\"optionbox\">&nbsp;</td>";
	}
?>
	<!-- // Empty space -->
	<td></td>

	<!-- // Check relationships by marriage -->
	<td class="descriptionbox">
	<?php
	print_help_link("follow_spouse_help", "qm");
	print $pgv_lang["follow_spouse"];?>
	</td>
	<td class="optionbox" id="followspousebox">
	<input tabindex="6" type="checkbox" name="followspouse" value="1"
	<?php
	if ($followspouse) print " checked=\"checked\"";
	print " onclick=\"document.people.path_to_find.value='-1';\""?> />
	</td>
	<?php
	if ((!empty($pid1))&&(!empty($pid2))&&($disp)){
		print "</tr><tr>";
		if (($disp)&&(!$check_node)) {
			print "<td class=\"topbottombar wrap vmiddle center\" colspan=\"2\">";
			if (isset($_SESSION["relationships"])) print "<span class=\"error\">".$pgv_lang["no_link_found"]."</span><br />";
			if (!$followspouse) {
				?>
				<script language="JavaScript" type="text/javascript">
				document.getElementById("followspousebox").className='facts_valuered';
				</script>
				<?php
				print "<input class=\"error\" type=\"submit\" value=\"".$pgv_lang["follow_spouse"]."\" onclick=\"people.followspouse.checked='checked';\"/>";
			}
			print "</td>";
		}
		else print "<td class=\"topbottombar vmiddle center\" colspan=\"2\"><input type=\"submit\" value=\"".$pgv_lang["next_path"]."\" onclick=\"document.people.path_to_find.value='".($path_to_find+1)."';\" /></td>\n";
		$pass = true;
	}

	if ($pass == false) print "</tr><tr><td colspan=\"2\" class=\"topbottombar wrap\">&nbsp;</td>";?>

	<!-- // Empty space -->
	<td></td>

	<!-- // View button -->
	<td class="topbottombar vmiddle center" colspan="2">
	<input tabindex="7" type="submit" value="<?php print $pgv_lang["view"]?>" />
	</td></tr>


	</table></form>
	<?php
}
else {
	$Dbaseyoffset=55;
	$Dbasexoffset=10;
}
?>
</div>
<div id="relationship_chart<?php print ($TEXT_DIRECTION=="ltr")?"":"_rtl";?>" style="position:relative; z-index:1; width:98%;">
<?php
$maxyoffset = $Dbaseyoffset;
if ((!empty($pid1))&&(!empty($pid2))) {
	if (!$disp) {
		print "<br /><br />";
		print_privacy_error($CONTACT_EMAIL);
	}
	else {
		if (isset($_SESSION["relationships"][$path_to_find])) $node = $_SESSION["relationships"][$path_to_find];
		else $node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
		if ($node!==false) {
			$_SESSION["pid1"] = $pid1;
			$_SESSION["pid2"] = $pid2;
			if (!isset($_SESSION["relationships"])) $_SESSION["relationships"] = array();
			$_SESSION["relationships"][$path_to_find] = $node;
			$yoffset = $Dbaseyoffset + 20;
			$xoffset = $Dbasexoffset;
			$colNum = 0;
			$rowNum = 0;
			$boxNum = 0;
			$previous="";
			$previous2="";
            $xs = $Dbxspacing+70;
            $ys = $Dbyspacing+50;
			// step1 = tree depth calculation
			if ($pretty) {
               $dmin=0;
               $dmax=0;
               $depth=0;
               foreach($node["path"] as $index=>$pid) {
                  if (($node["relations"][$index]=="father")||($node["relations"][$index]=="mother")) {

                     $depth++;
                     if ($depth>$dmax) $dmax=$depth;
                     if ($asc==0) $asc=1; // the first link is a parent link
                  }
                  if ($node["relations"][$index]=="child") {
                     $depth--;
                     if ($depth<$dmin) $dmin=$depth;
                     if ($asc==0) $asc=-1; // the first link is a child link
                  }
               }
               $depth=$dmax+$dmin;
			   // need more yoffset before the first box ?
               if ($asc==1) $yoffset -= $dmin*($Dbheight+$ys);
               if ($asc==-1) $yoffset += $dmax*($Dbheight+$ys);
				$rowNum = ($asc==-1) ? $depth : 0;
			}
			$maxxoffset = -1*$Dbwidth-20;
			$maxyoffset = $yoffset;
			if ($TEXT_DIRECTION=="ltr") {
				$rArrow = $PGV_IMAGES["rarrow"]["other"];
				$lArrow = $PGV_IMAGES["larrow"]["other"];
			} else {
				$rArrow = $PGV_IMAGES["larrow"]["other"];
				$lArrow = $PGV_IMAGES["rarrow"]["other"];
			}
			foreach($node["path"] as $index=>$pid) {
			    print "\r\n\r\n<!-- Node:{$index} -->\r\n";
				$linex = $xoffset;
				$liney = $yoffset;
				$mfstyle = "NN";
				$indirec = find_person_record($pid);
				if (preg_match("/1 SEX F/", $indirec, $smatch)>0) $mfstyle="F";
				if (preg_match("/1 SEX M/", $indirec, $smatch)>0) $mfstyle="";
				$arrow_img = $PGV_IMAGE_DIR."/".$PGV_IMAGES["darrow"]["other"];
				if (($node["relations"][$index]=="father")||($node["relations"][$index]=="mother")) {
					$line = $PGV_IMAGES["vline"]["other"];
					$liney += $Dbheight;
					$linex += $Dbwidth/2;
					$lh = 54;
					$lw = 3;
					//check for paternal grandparent relationship
					if ($pretty) {
                       if ($asc==0) $asc=1;
                       if ($asc==-1) $arrow_img = $PGV_IMAGE_DIR."/".$PGV_IMAGES["uarrow"]["other"];
					   $lh=$ys;
                       $linex=$xoffset+$Dbwidth/2;
                       // put the box up or down ?
                       $yoffset += $asc*($Dbheight+$lh);
						$rowNum += $asc;
                       if ($asc==1) $liney = $yoffset-$lh; else $liney = $yoffset+$Dbheight;
                       // need to draw a joining line ?
                       if ($previous=="child" and $previous2!="parent") {
                          $joinh = 3;
                          $joinw = $xs/2+2;
                          $xoffset += $Dbwidth+$xs;
							$colNum ++;
							//$rowNum is inherited from the box immediately to the left
                          $linex = $xoffset-$xs/2;
                          if ($asc==-1) $liney=$yoffset+$Dbheight; else $liney=$yoffset-$lh;
                          $joinx = $xoffset-$xs;
                          $joiny = $liney-2-($asc-1)/2*$lh;
                          print "<div id=\"joina$index\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($joinx+$Dbxspacing)."px; top:".($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\" /></div>\n";
                          $joinw = $xs/2+2;
                          $joinx = $joinx+$xs/2;
                          $joiny = $joiny+$asc*$lh;
                          print "<div id=\"joinb$index\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($joinx+$Dbxspacing)."px; top:".($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\" /></div>\n";
                       }
                       $previous2=$previous;;
                       $previous="parent";
                    }
					else $yoffset += $Dbheight+$Dbyspacing+50;
			    }
				if ($node["relations"][$index]=="sibling") {
					$arrow_img = $PGV_IMAGE_DIR."/".$rArrow;
					if ($mfstyle=="F") $node["relations"][$index]="sister";
					if ($mfstyle=="") $node["relations"][$index]="brother";
					$xoffset += $Dbwidth+$Dbxspacing+70;
					$colNum ++;
					//$rowNum is inherited from the box immediately to the left
					$line = $PGV_IMAGES["hline"]["other"];
					$linex += $Dbwidth;
					$liney += $Dbheight/2;
					$lh = 3;
					$lw = 70;
					if ($pretty) {
					   $lw = $xs;
                       $linex = $xoffset-$lw;
                       $liney = $yoffset+$Dbheight/4;
                       $previous2=$previous;;
					   $previous="";
					}
				}
				if ($node["relations"][$index]=="spouse") {
					$arrow_img = $PGV_IMAGE_DIR."/".$rArrow;
					if ($mfstyle=="F") $node["relations"][$index]="wife";
					if ($mfstyle=="") $node["relations"][$index]="husband";
					$xoffset += $Dbwidth+$Dbxspacing+70;
					$colNum ++;
					//$rowNum is inherited from the box immediately to the left
					$line = $PGV_IMAGES["hline"]["other"];
					$linex += $Dbwidth;
					$liney += $Dbheight/2;
					$lh = 3;
					$lw = 70;
					if ($pretty) {
					   $lw = $xs;
                       $linex = $xoffset-$lw;
                       $liney = $yoffset+$Dbheight/4;
                       $previous2=$previous;;
					   $previous="";
					}
				}
				if ($node["relations"][$index]=="child") {
					if ($mfstyle=="F") $node["relations"][$index]="daughter";
					if ($mfstyle=="") $node["relations"][$index]="son";
					$line = $PGV_IMAGES["vline"]["other"];
					$liney += $Dbheight;
					$linex += $Dbwidth/2;
					$lh = 54;
					$lw = 3;
					if ($pretty) {
				       if ($asc==0) $asc=-1;
                       if ($asc==1) $arrow_img = $PGV_IMAGE_DIR."/".$PGV_IMAGES["uarrow"]["other"];
					   $lh=$ys;
                       $linex = $xoffset+$Dbwidth/2;
                       // put the box up or down ?
                       $yoffset -= $asc*($Dbheight+$lh);
                       $rowNum -= $asc;
                       if ($asc==-1) $liney = $yoffset-$lh; else $liney = $yoffset+$Dbheight;
                       // need to draw a joining line ?
                       if ($previous=="parent" and $previous2!="child") {
                          $joinh = 3;
                          $joinw = $xs/2+2;
                          $xoffset += $Dbwidth+$xs;
							$colNum ++;
							//$rowNum is inherited from the box immediately to the left
                          $linex = $xoffset-$xs/2;
                          if ($asc==1) $liney=$yoffset+$Dbheight; else $liney=$yoffset-($lh+$Dbyspacing);
                          $joinx = $xoffset-$xs;
                          $joiny = $liney-2+($asc+1)/2*$lh;
                          print "<div id=\"joina$index\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($joinx+$Dbxspacing)."px; top:".($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\" /></div>\n";
                          $joinw = $xs/2+2;
                          $joinx = $joinx+$xs/2;
                          $joiny = $joiny-$asc*$lh;
                          print "<div id=\"joinb$index\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($joinx+$Dbxspacing)."px; top:".($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\" /></div>\n";
                       }
                       $previous2=$previous;;
                       $previous="child";
                    }
					else $yoffset += $Dbheight+$Dbyspacing+50;
				}
				if ($yoffset > $maxyoffset) $maxyoffset = $yoffset;
				$plinex = $linex;
				$pxoffset = $xoffset;

				// Adjust all box positions for proper placement with respect to other page elements
				if ($BROWSERTYPE=="mozilla" && $TEXT_DIRECTION=="rtl") $pxoffset += 10;
				else $pxoffset -= 3;
				$pyoffset = $yoffset - 2;

				if ($index>0) {
					if ($TEXT_DIRECTION=="rtl" && $line!=$PGV_IMAGES["hline"]["other"]) {
						print "<div id=\"line$index\" dir=\"ltr\" style=\"background:none; position:absolute; right:".($plinex+$Dbxspacing)."px; top:".($liney+$Dbyspacing)."px; width:".($lw+$lh*2)."px; z-index:-100; \" align=\"right\">";
						print "<img src=\"$PGV_IMAGE_DIR/$line\" align=\"right\" width=\"$lw\" height=\"$lh\" alt=\"\" />\n";
						print "<br />";
						print $pgv_lang[$node["relations"][$index]]."\n";
						print "<img src=\"$arrow_img\" border=\"0\" align=\"middle\" alt=\"\" />\n";
					}
					else {
						print "<div id=\"line$index\" style=\"background:none;  position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($plinex+$Dbxspacing)."px; top:".($liney+$Dbyspacing)."px; width:".($lw+$lh*2)."px; z-index:-100; \" align=\"".($lh==3?"center":"left")."\"><img src=\"$PGV_IMAGE_DIR/$line\" align=\"left\" width=\"$lw\" height=\"$lh\" alt=\"\" />\n";
						print "<br />";
						print "<img src=\"$arrow_img\" border=\"0\" align=\"middle\" alt=\"\" />\n";
						if ($lh == 3) print "<br />"; // note: $lh==3 means horiz arrow
						print $pgv_lang[$node["relations"][$index]]."\n";
					}
					print "</div>\n";
				}
				// Determine the z-index for this box
			    $boxNum ++;
				if ($TEXT_DIRECTION=="rtl" && $BROWSERTYPE=="mozilla") {
			    	if ($pretty) $zIndex = ($colNum * $depth - $rowNum + $depth);
			    	else $zIndex = $boxNum;
		    	} else {
			    	if ($pretty) $zIndex = 200 - ($colNum * $depth + $rowNum);
			    	else $zIndex = 200 - $boxNum;
		    	}
		    	
				print "<div id=\"box$pid.0\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".$pxoffset."px; top:".$pyoffset."px; width:".$Dbwidth."px; height:".$Dbheight."px; z-index:".$zIndex."; \"><table><tr><td colspan=\"2\" width=\"$Dbwidth\" height=\"$Dbheight\">";
				print_pedigree_person($pid, 1, ($view!="preview"));
				print "</td></tr></table></div>\n";
			}





			$sentence = getRelationshipSentence($node, $pid1, $pid2);
			if($sentence != false)
			{
				print "<div style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":1px; top:".($Dbaseyoffset-70)."px; z-index:1;\">";
				print "<h4>";
			    print $sentence;
				print "</h4></div>\n";
			}
		}
	}
}

$maxyoffset += 100;
?>
</div>
<script language="JavaScript" type="text/javascript">
	relationship_chart_div = document.getElementById("relationship_chart");
	if (!relationship_chart_div) relationship_chart_div = document.getElementById("relationship_chart_rtl");
	if (relationship_chart_div) {
		relationship_chart_div.style.height = <?php print ($maxyoffset-50); ?> + "px";
		relationship_chart_div.style.width = "100%";
	}
</script>
<?php
print_footer();

?>

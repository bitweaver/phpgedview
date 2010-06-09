<?php
/**
* List branches by surname
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
* @subpackage Lists
* @version $Id$
*/

require './config.php';

//-- const
define('PGV_ICON_RINGS', "<img src=\"images/small/rings.gif\" alt=\"{$factarray["MARR"]}\" title=\"{$factarray["MARR"]}\" />");
define('PGV_ICON_BRANCHES', "<img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["patriarch"]["small"]."\" alt=\"\" align=\"middle\" />");

//-- args
$surn = safe_GET('surn', '[^<>&%{};]*');
$surn = UTF8_strtoupper($surn);
$soundex_std = safe_GET_bool('soundex_std');
$soundex_dm = safe_GET_bool('soundex_dm');
$ged = safe_GET('ged');
if (empty($ged)) {
	$ged = $GEDCOM;
}

//-- rootid
$rootid = "";
if (PGV_USER_ID) {
	$rootid = PGV_USER_ROOT_ID;
	if (empty($_SESSION['user_ancestors'])	|| $_SESSION['user_ancestors'][1]!==$rootid) {
		unset($_SESSION['user_ancestors']);
		load_ancestors_array($rootid);
	}
}

//-- random surname
if ($surn=='*') {
	$surn = array_rand(get_indilist_surns("", "", false, true, PGV_GED_ID));
}

//-- form
print_header($pgv_lang["branch_list"]." - ".$surn);
if ($ENABLE_AUTOCOMPLETE) {
	require './js/autocomplete.js.htm';
}
?>
<form name="surnlist" id="surnlist" action="?">
	<table class="center facts_table width50">
		<tr>
			<td class="descriptionbox <?php echo $TEXT_DIRECTION; ?>">
				<?php print_help_link("surname_help", "qm", "surname"); echo $factarray["SURN"]; ?></td>
			<td class="optionbox <?php echo $TEXT_DIRECTION; ?>">
				<input type="text" name="surn" id="SURN" value="<?php echo $surn?>" />
				<input type="hidden" name="ged" id="ged" value="<?php echo $ged?>" />
				<input type="submit" value="<?php echo $pgv_lang['view']; ?>" />
				<input type="submit" value="<?php echo $pgv_lang['random_surn']; ?>" onclick="document.surnlist.surn.value='*';" />
				<p class="details1">
					<?php print_help_link("soundex_search_help", "qm", "soundex_search"); echo $pgv_lang["soundex_search"]?><br />
					<input type="checkbox" name="soundex_std" id="soundex_std" value="1" <?php if ($soundex_std) echo " checked=\"checked\"" ?> />
					<label for="soundex_std"><?php echo $pgv_lang["search_russell"]?></label>
					<input type="checkbox" name="soundex_dm" id="soundex_dm" value="1" <?php if ($soundex_dm) echo " checked=\"checked\"" ?> />
					<label for="soundex_dm"><?php echo $pgv_lang["search_DM"]?></label>
				</p>
			</td>
		</tr>
	</table>
</form>
<?php
//-- results
if ($surn) {
	$surn_lang = whatLanguage($surn);
	echo "<fieldset><legend>".PGV_ICON_BRANCHES." ".PrintReady($surn)."</legend>";
	$indis = indis_array($surn, $soundex_std, $soundex_dm);
	echo "<ol>";
	foreach ($indis as $k=>$person) {
		$famc = $person->getPrimaryChildFamily();
		if (!$famc || (!array_key_exists($famc->getHusbId(), $indis)) && !array_key_exists($famc->getWifeId(), $indis)) {
			print_fams($person);
		}
	}
	echo "</ol>";
	echo "</fieldset>";
	if ($rootid) {
		$person = Person::getInstance($rootid);
		echo "<p class=\"center\">{$pgv_lang['rootid']} : <a title=\"{$person->xref}\" href=\"{$person->getLinkUrl()}\">{$person->getFullName()}</a>";
		echo "<br />{$pgv_lang["direct-ancestors"]} : ".count($_SESSION['user_ancestors'])."</p>";
	}
}
print_footer();

function print_fams($person, $famid=null) {
	global $pgv_lang, $surn, $surn_lang, $TEXT_DIRECTION;
	// select person name according to searched surname
	$person_name = "";
	foreach ($person->getAllNames() as $n=>$name) {
		list($surn1) = explode(", ", $name['list']);
		if (stripos($surn1, $surn)===false
			&& stripos($surn, $surn1)===false
			&& soundex_std($surn1)!==soundex_std($surn)
			&& soundex_dm($surn1)!==soundex_dm($surn)
			) {
			continue;
		}
		if (whatLanguage($surn1)!==$surn_lang) {
			continue;
		}
		$person_name = $name['full'];
		break;
	}
	if (empty($person_name)) {
		echo "<span title=\"".PrintReady(strip_tags($person->getFullName()))."\">".$person->getSexImage()."...</span>";
		return;
	}
	$person_lang = whatLanguage($person_name);
	// current indi
	echo "<li>";
	$class = "";
	$sosa = @array_search($person->xref, $_SESSION['user_ancestors']);
	if ($sosa) {
		$class = "search_hit";
		$sosa = "<a dir=$TEXT_DIRECTION target=\"_blank\" class=\"details1 {$person->getBoxStyle()}\" title=\"Sosa\" href=\"relationship.php?pid2=".PGV_USER_ROOT_ID."&pid1={$person->xref}\">&nbsp;{$sosa}&nbsp;</a>".sosa_gen($sosa);
	}
	$current = $person->getSexImage().
		"<a target=\"_blank\" class=\"{$class}\" title=\"{$person->xref}\" href=\"{$person->getLinkUrl()}\">".PrintReady($person_name)."</a> ".
		$person->getBirthDeathYears()." {$sosa}"; 
	if ($famid && $person->getChildFamilyPedigree($famid)) {
		$current = "<span class='red'>".$pgv_lang[$person->getChildFamilyPedigree($famid)]."</span> ".$current;
	}
	// spouses and children
	if (count($person->getSpouseFamilies())<1) {
		echo $current;
	}
	foreach ($person->getSpouseFamilies() as $f=>$family) {
		$txt = $current;
		$spouse = $family->getSpouse($person);
		if ($spouse) {
			$class = "";
			$sosa2 = @array_search($spouse->xref, $_SESSION['user_ancestors']);
			if ($sosa2) {
				$class = "search_hit";
				$sosa2 = "<a dir=$TEXT_DIRECTION target=\"_blank\" class=\"details1 {$spouse->getBoxStyle()}\" title=\"Sosa\" href=\"relationship.php?pid2=".PGV_USER_ROOT_ID."&pid1={$spouse->xref}\">&nbsp;{$sosa2}&nbsp;</a>".sosa_gen($sosa2);
			}
			if ($family->getMarriageYear()) {
				$txt .= "&nbsp;<span dir=$TEXT_DIRECTION class='details1' title=\"".strip_tags($family->getMarriageDate()->Display())."\">".PGV_ICON_RINGS.$family->getMarriageYear()."</span>&nbsp;";
			}
			else if ($family->getMarriage()) {
				$txt .= "&nbsp;<span dir=$TEXT_DIRECTION class='details1' title=\"".$pgv_lang["yes"]."\">".PGV_ICON_RINGS."</span>&nbsp;";
			}
			$spouse_name = $spouse->getListName();
			foreach ($spouse->getAllNames() as $n=>$name) {
				if (whatLanguage($name['list']) == $person_lang) {
					$spouse_name = $name['list'];
					break;
				}
				//How can we use check_NN($names) or something else to replace the unknown unknown name from the page language to the language of the spouse's name?
				else if ($name['fullNN']=="@P.N. @N.N.") {
					$spouse_name = $pgv_lang["NN".$person_lang].", ".$pgv_lang["NN".$person_lang];
					break;
				}
			}
			list($surn2, $givn2) = explode(", ", $spouse_name.", x");
			$txt .= $spouse->getSexImage().
				"<a target=\"_blank\" class=\"{$class}\" title=\"{$family->xref}\" href=\"{$family->getLinkUrl()}\">".PrintReady($givn2)."</a> ".
				"<a class=\"{$class}\" title=\"{$surn2}\" href=\"javascript:document.surnlist.surn.value='{$surn2}';document.surnlist.submit();\">".PrintReady($surn2)."</a> ".
				$spouse->getBirthDeathYears()." {$sosa2}";
		}
		echo $txt;
		echo "<ol>";
		foreach ($family->getChildren() as $c=>$child) {
			print_fams($child, $family->xref);
		}
		echo "</ol>";
	}
	echo "</li>";
}

function load_ancestors_array($xref, $sosa=1) {
	if ($xref) {
		$_SESSION['user_ancestors'][$sosa] = $xref;
		$person = Person::getInstance($xref);
		$famc = $person->getPrimaryChildFamily();
		if ($famc) {
			load_ancestors_array($famc->getHusbId(), $sosa*2);
			load_ancestors_array($famc->getWifeId(), $sosa*2+1);
		}
	}
}

function indis_array($surn, $soundex_std, $soundex_dm) {
	global $TBLPREFIX, $gBitDb;
	$sql=
		"SELECT DISTINCT n_id".
		" FROM {$TBLPREFIX}name".
		" WHERE n_file=?".
		" AND n_type!=?".
		" AND (n_surn=? OR n_surname=?";
	$args=array(PGV_GED_ID, '_MARNM', $surn, $surn);
	if ($soundex_std) {
		$sql .= " OR n_soundex_surn_std=?";
		$args[]=soundex_std($surn);
	}
	if ($soundex_dm) {
		$sql .= " OR n_soundex_surn_dm=?";
		$args[]=soundex_dm($surn);
	}
	$sql .= ") ORDER BY n_sort";
	$rows = $gBitDb->query( $sql, $args );
//	var_dump($sql); var_dump($rows);
	$data=array();
	while ( $row = $roes->fetchRow() ) {
		$data[$row[n_id]]=Person::getInstance($row[n_id]);
	}
	return $data;
}

function sosa_gen($sosa) {
	global $pgv_lang;
	$gen = (int)log($sosa, 2)+1;
	return "<sup title=\"".$pgv_lang["generation_number"]."\">{$gen}</sup>";
}
?>

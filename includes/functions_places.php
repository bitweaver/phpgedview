<?php
/**
 * Functions for places selection (clickable maps, autocompletion...)
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
 * @subpackage Edit
 * @version $Id: functions_places.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 */
if (strstr($_SERVER["SCRIPT_NAME"],"functions")) {
	print "Now, why would you want to do that.  You're not hacking are you?";
	exit;
}
?>
<link rel="stylesheet" type="text/css" href="places/dropdown.css" />
<div id='mapdata' name='mapdata'></div>
<script type="text/javascript" src="places/getobject.js"></script>
<script type="text/javascript" src="places/modomt.js"></script>
<script type="text/javascript" src="places/xmlextras.js"></script>
<script type="text/javascript" src="places/acdropdown.js"></script>
<?php

/**
 * creates PLAC input subfields (Country, District ...) according to Gedcom HEAD>PLACE>FORM
 *
 * data split/copy is done locally by javascript functions
 *
 * @param string $element_id	id of PLAC input element in the form
 */
function print_place_subfields($element_id) {
	global $pgv_lang, $PGV_IMAGE_DIR, $PGV_IMAGES, $lang_short_cut, $LANGUAGE;
	global $countries, $factarray;

	if ($element_id=="DEAT_PLAC") return; // known bug - waiting for a patch

	$HEAD = find_gedcom_record("HEAD");
	$HEAD_PLAC = get_sub_record(1, "1 PLAC", $HEAD);
	$HEAD_PLAC_FORM = get_sub_record(1, "2 FORM", $HEAD_PLAC);
	$HEAD_PLAC_FORM = substr($HEAD_PLAC_FORM, 6);
	if (empty($HEAD_PLAC_FORM)) $HEAD_PLAC_FORM = $pgv_lang["default_form"];
	$plac_label = preg_split ("/,/", $HEAD_PLAC_FORM);
	$plac_label = array_reverse($plac_label);
	if ($HEAD_PLAC_FORM == $pgv_lang["default_form"]) $plac_label[0] = $factarray["CTRY"];
	?>
	<script type="text/javascript" src="./js/strings.js"></script>
	<script type="text/javascript">
	<!--
	function http_loadmap(ctry) {
		// already loaded ?
		if (document.getElementsByName(ctry)[0]) return;
		// load data into HTML tag <div id="mapdata"> ... </div>
		document.getElementById("mapdata").innerHTML = "";
		// get mapfile from server
		http_request = XmlHttp.create();
		// 1. user map
		mapfile = 'places/'+ctry+'/'+ctry+'.extra.htm';
		http_request.open('GET', mapfile, false); http_request.send(null);
		if (http_request.status == 200) document.getElementById("mapdata").innerHTML += http_request.responseText;
		// 2. localized map
		mapfile = 'places/'+ctry+'/'+ctry+'.<?php print $lang_short_cut[$LANGUAGE]?>.htm';
		http_request.open('GET', mapfile, false); http_request.send(null);
		if (http_request.status == 200) document.getElementById("mapdata").innerHTML += http_request.responseText;
		// 3. default map
		mapfile = 'places/'+ctry+'/'+ctry+'.htm';
		http_request.open('GET', mapfile, false); http_request.send(null);
		// load data into HTML tag <div id="mapdata"> ... </div>
		if (http_request.status == 200) document.getElementById("mapdata").innerHTML += http_request.responseText;
	}
	// called to refresh field PLAC after any subfield change
	function updatewholeplace(place_tag) {
		place_value="";
		for (p=0; p<<?php print count($plac_label);?>; p++) {
			place_subtag=place_tag+'_'+p;
			if (document.getElementById(place_subtag)) {
				// clear data after opening bracket : Wales (WLS) ==> Wales
				subtagval = document.getElementById(place_subtag).value;
				cut = subtagval.indexOf(' (');
				if (cut>1) subtagval = subtagval.substring(0,cut);
				if (p>0) place_value = subtagval+", "+place_value;
				else place_value = subtagval;
			}
		}
		document.getElementById(place_tag).value = place_value;
	}
	// called to refresh subfields after any field PLAC change
	function splitplace(place_tag) {
		place_value = document.getElementById(place_tag).value;
		var place_array=place_value.split(",");
		var len=place_array.length;
		for (p=0; p<len; p++) {
			q=len-p-1;
			place_subtag=place_tag+'_'+p;
			if (document.getElementById(place_subtag)) {
				//alert(place_subtag+':'+place_array[q]);
				document.getElementById(place_subtag).value=trim(place_array[q]);
			}
		}
		//document.getElementById(place_tag+'_0').focus();
		if (document.getElementsByName('PLAC_CTRY')) {
			elt=document.getElementsByName('PLAC_CTRY')[0];
			ctry=elt.value.toUpperCase();
			//alert(elt.value.charCodeAt(0)+'\n'+elt.value.charCodeAt(1));
			if (elt.value=='\u05d9\u05e9\u05e8\u05d0\u05dc') ctry='ISR'; // Israel hebrew name
			else if (ctry.length==3) elt.value=ctry;
			ctry=ctry.substr(0,3);
			pdir='places/'+ctry+'/';
			// select current country in the list
			sel=document.getElementsByName('PLAC_CTRY_select')[0];
			for(i=0;i<sel.length;++i) if (sel.options[i].value==ctry) sel.options[i].selected=true;
			// refresh country flag
			img=document.getElementsByName('PLAC_CTRY_flag')[0];
			img.src='places/flags/'+ctry+'.gif';
			img.alt=ctry;
			img.title=ctry;
			// load html map file from server
			http_loadmap(ctry);
			// refresh country image
			img=document.getElementsByName('PLAC_CTRY_img')[0];
			if (document.getElementsByName(ctry)[0]) {
				img.src=pdir+ctry+'.gif';
				img.alt=ctry;
				img.title=ctry;
				img.useMap='#'+ctry;
			}
			else {
				img.src='image/pix1.gif'; // show image only if mapname exists
				document.getElementsByName('PLAC_CTRY_div')[0].style.height='auto';
			}
			// refresh state image
			/**img=document.getElementsByName('PLAC_STAE_auto')[0];
			img.alt=ctry;
			img.title=ctry;**/
			stae=document.getElementsByName('PLAC_STAE')[0].value;
			stae=strclean(stae);
			stae=ctry+'_'+stae;
			img=document.getElementsByName('PLAC_STAE_img')[0];
			if (document.getElementsByName(stae)[0]) {
				img.src=pdir+stae+'.gif';
				img.alt=stae;
				img.title=stae;
				img.useMap='#'+stae;
			}
			else {
				img.src='image/pix1.gif'; // show image only if mapname exists
				document.getElementsByName('PLAC_STAE_div')[0].style.height='auto';
			}
			// refresh county image
			/**img=document.getElementsByName('PLAC_CNTY_auto')[0];
			img.alt=stae;
			img.title=stae;**/
			cnty=document.getElementsByName('PLAC_CNTY')[0].value;
			cnty=strclean(cnty);
			cnty=stae+'_'+cnty;
			img=document.getElementsByName('PLAC_CNTY_img')[0];
			if (document.getElementsByName(cnty)[0]) {
				img.src=pdir+cnty+'.gif';
				img.alt=cnty;
				img.title=cnty;
				img.useMap='#'+cnty;
			}
			else {
				img.src='image/pix1.gif'; // show image only if mapname exists
				document.getElementsByName('PLAC_CNTY_div')[0].style.height='auto';
			}
			// refresh city image
			/**img=document.getElementsByName('PLAC_CITY_auto')[0];
			img.alt=cnty;
			img.title=cnty;**/
		}
	}
	// called when clicking on +/- PLAC button
	function toggleplace(place_tag) {
		var ronly=document.getElementById(place_tag).readOnly;
		document.getElementById(place_tag).readOnly=1-ronly;
		if (ronly) {
			document.getElementById(place_tag+'_pop').style.display="inline";
			updatewholeplace(place_tag);
		}
		else {
			document.getElementById(place_tag+'_pop').style.display="none";
			splitplace(place_tag);
		}
	}
	// called when selecting a new country in country list
	function setPlaceCountry(txt) {
		document.getElementsByName('PLAC_CTRY_div')[0].style.height='32px';
		document.getElementsByName('PLAC_STAE_div')[0].style.height='32px';
		document.getElementsByName('PLAC_CNTY_div')[0].style.height='32px';
		document.getElementsByName('PLAC_CTRY')[0].value=txt;
		updatewholeplace('<?php print $element_id?>');
		splitplace('<?php print $element_id?>');
	}
	// called when clicking on a new state/region on country map
	function setPlaceState(txt) {
		document.getElementsByName('PLAC_STAE_div')[0].style.height='32px';
		document.getElementsByName('PLAC_CNTY_div')[0].style.height='32px';
		div=document.getElementsByName('PLAC_CTRY_div')[0];
		if (div.style.height!='auto') { div.style.height='auto'; return; } else div.style.height='32px';
		document.getElementsByName('PLAC_STAE_div')[0].style.height='auto';
		p=txt.indexOf(' ('); if (1<p) txt=txt.substring(0,p); // remove code (XX)
		if (txt.length) document.getElementsByName('PLAC_STAE')[0].value=txt;
		updatewholeplace('<?php print $element_id?>');
		splitplace('<?php print $element_id?>');
	}
	// called when clicking on a new county on state map
	function setPlaceCounty(txt) {
		document.getElementsByName('PLAC_CNTY_div')[0].style.height='32px';
		div=document.getElementsByName('PLAC_STAE_div')[0];
		if (div.style.height!='auto') { div.style.height='auto'; return; } else div.style.height='32px';
		document.getElementsByName('PLAC_CNTY_div')[0].style.height='auto';
		p=txt.indexOf(' ('); if (1<p) txt=txt.substring(0,p); // remove code (XX)
		if (txt.length) document.getElementsByName('PLAC_CNTY')[0].value=txt;
		updatewholeplace('<?php print $element_id?>');
		splitplace('<?php print $element_id?>');
	}
	// called when clicking on a new city on county map
	function setPlaceCity(txt) {
		div=document.getElementsByName('PLAC_CNTY_div')[0];
		if (div.style.height!='auto') { div.style.height='auto'; return; } else div.style.height='32px';
		if (txt.length) document.getElementsByName('PLAC_CITY')[0].value=txt;
		updatewholeplace('<?php print $element_id?>');
		splitplace('<?php print $element_id?>');
	}
	//-->
	</script>
	<?php
	$cols=40;
	print "&nbsp;<a href=\"javascript:;\" onclick=\"expand_layer('".$element_id."_div'); toggleplace('".$element_id."'); return false;\"><img id=\"".$element_id."_div_img\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"\" title=\"\" />&nbsp;</a>";
	print "<br /><div id=\"".$element_id."_div\" style=\"display: none; border-width:thin; border-style:none; padding:0px\">\n";
	// subtags creation : _0 _1 _2 etc...
	$icountry=-1;
	$istate=-1;
	$icounty=-1;
	$icity=-1;
	for ($i=0; $i<count($plac_label); $i++) {
		$subtagid=$element_id."_".$i;
		$subtagname=$element_id."_".$i;
		$plac_label[$i]=trim($plac_label[$i]);
		if (in_array($plac_label[$i], array("Country", "Pays", "Land", "Zeme", "�lke", "Pa�s", "Orsz�g", "Nazione", "Kraj", "Maa", $factarray["CTRY"]))) {
			$cols="8";
			$subtagname="PLAC_CTRY";
			$icountry=$i;
			$istate=$i+1;
			$icounty=$i+2;
			$icity=$i+3;
		} else $cols=40;
		if ($i==$istate) $subtagname="PLAC_STAE";
		if ($i==$icounty) $subtagname="PLAC_CNTY";
		if ($i==$icity) $subtagname="PLAC_CITY";
		$key=strtolower($plac_label[$i]);
		print "<small>";
		if (isset($pgv_lang[$key])) print $pgv_lang[$key];
		else print $plac_label[$i];
		print "</small><br />";
		if ($GLOBALS["DEBUG"]) print $subtagname."<br />\n";
		print "<input type=\"text\" id=\"".$subtagid."\" name=\"".$subtagname."\" value=\"\" size=\"".$cols."\"";
		print " tabindex=\"".($i+1)."\" ";
		print " onblur=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
		print " onchange=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
		print " onmouseout=\"updatewholeplace('".$element_id."'); splitplace('".$element_id."');\" ";
		if ($icountry<$i and $i<=$icity) print " acdropdown=\"true\" autocomplete_list=\"url:places/getdata.php?localized=".$lang_short_cut[$LANGUAGE]."&amp;field=".$subtagname."&amp;s=\" autocomplete=\"off\" autocomplete_matchbegin=\"false\"";
		print " />\n";
		// country selector
		if ($i==$icountry) {
			print " <img id=\"PLAC_CTRY_flag\" name=\"PLAC_CTRY_flag\" /> ";
			print "<select id=\"".$subtagid."_select\" name=\"".$subtagname."_select\" class=\"submenuitem\"";
			print " onchange=\"setPlaceCountry(this.value);\"";
//			print " acdropdown=\"true\" autocomplete_complete=\"true\"";
			print " >\n";
			print "<option value=\"\">?</option>\n";
			foreach ($countries as $alpha3=>$country) {
				$txt=$alpha3." : ".$country;
				if (strlen($txt)>32) $txt=substr($txt,0,32)."...";
				print "<option value=\"".$alpha3."\">".$txt."</option>\n";
			}
			print "</select>\n";
		}
		else {
			/**if ($icountry<$i and $i<=$icity) {
				$text = $pgv_lang["autocomplete"];
				if (isset($PGV_IMAGES["autocomplete"]["button"])) $Link = "<img id=\"".$subtagid."_auto\" name=\"".$subtagname."_auto\" src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["autocomplete"]["button"]."\" alt=\"".$text."\" title=\"".$text."\" />";
				else $Link = $text;
				print "&nbsp;".$Link."&nbsp;";
			}**/
			print_specialchar_link($subtagid, false);
		}
		// clickable map
		if ($i<$icountry or $i>$icounty) print "<br />\n";
		else print "<div id='".$subtagname."_div' name='".$subtagname."_div' style='overflow:hidden; height:32px; width:auto; border-width:thin; border-style:none;'><img name='".$subtagname."_img' src='image/spacer.gif' usemap='usemap' border='0' alt='' title='' style='height:inherit; width:inherit;' /></div>";
	}
	print "</div>";
}
?>

<?php
/**
 * Reorder media Items using drag and drop
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
 * @subpackage Module
 * @version $Id: functions_media_reorder.php,v 1.1 2009/04/30 17:51:51 lsces Exp $
 * @author Brian Holland
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}


/**
 * print a media row in a table
 * @param string $rtype whether this is a 'new', 'old', or 'normal' media row... this is used to determine if the rows should be printed with an outline color
 * @param array $rowm        An array with the details about this media item
 * @param string $pid        The record id this media item was attached to
 */
function media_reorder_row($rtype, $rowm, $pid) {

    global $PGV_IMAGE_DIR, $PGV_IMAGES, $view, $MEDIA_DIRECTORY, $TEXT_DIRECTION;
    global $SHOW_ID_NUMBERS, $GEDCOM, $factarray, $pgv_lang, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
    global $SEARCH_SPIDER;
    global $t, $n, $item, $items, $p, $edit, $SERVER_URL, $reorder, $LB_AL_THUMB_LINKS, $note, $rowm;
	global $LB_URL_WIDTH, $LB_URL_HEIGHT, $order1, $mediaType;

	if (!isset($rowm)) {
		$rowm=$row;
	}
	print "<li class=\"facts_value\" style=\"list-style:none;cursor:move;margin-bottom:2px;\" id=\"li_" . $rowm['m_media'] . "\" >";

    //print $rtype." ".$rowm["m_media"]." ".$pid;
    if (!displayDetailsById($rowm['m_media'], 'OBJE') || FactViewRestricted($rowm['m_media'], $rowm['m_gedrec'])) {
        //print $rowm['m_media']." no privacy ";
        return false;
    }

    $styleadd="";
    if ($rtype=='new') $styleadd = "change_new";
    if ($rtype=='old') $styleadd = "change_old";

    // NOTE Start printing the media details

    $thumbnail = thumbnail_file($rowm["m_file"], true, false, $pid);
    // $isExternal = stristr($thumbnail,"://");
	$isExternal = isFileExternal($thumbnail);

    $linenum = 0;



    // NOTE Get the title of the media
    if (showFactDetails("OBJE", $pid)) {
        $mediaTitle = $rowm["m_titl"];
        $subtitle = get_gedcom_value("TITL", 2, $rowm["mm_gedrec"]);

        if (!empty($subtitle)) $mediaTitle = $subtitle;
		$mainMedia = check_media_depth($rowm["m_file"], "NOTRUNC");
        if ($mediaTitle=="") $mediaTitle = basename($rowm["m_file"]);

		print "\n" . "<table class=\"pic\"><tr>" . "\n";
		print "<td width=\"80\" valign=\"top\" align=\"center\" >". "\n";

		// Get info on how to handle this media file
		$mediaInfo = mediaFileInfo($mainMedia, $thumbnail, $rowm["m_media"], $mediaTitle, '');

		//-- Thumbnail field
		print "<img src=\"".$mediaInfo['thumb']."\" height=\"38\" border=\"0\" " ;

		if ( eregi("1 SOUR",$rowm['m_gedrec'])) {
			print " alt=\"" . PrintReady($mediaTitle) . "\" title=\"" . PrintReady($mediaTitle) . "\nSource info available\" />";
		}else{
			print " alt=\"" . PrintReady($mediaTitle) . "\" title=\"" . PrintReady($mediaTitle) . "\" />";
		}

		//print media info
		$ttype2 = preg_match("/\d TYPE (.*)/", $rowm["m_gedrec"], $match);
		if ($ttype2>0) {
			$mediaType = trim($match[1]);
			$varName = "TYPE__".strtolower($mediaType);
			if (isset($pgv_lang[$varName])) $mediaType = $pgv_lang[$varName];
//		print "\n\t\t\t<br /><span class=\"label\">".$pgv_lang["type"].": </span> <span class=\"field\">$mediaType</span>";
		}

		print "\n" . "</td><td>&nbsp;</td>" . "\n";
		print "<td valign=\"top\" align=\"left\">";
		//print "<font color=\"blue\">";
		print $rowm['m_media'];
		//print "</font>";

		print "<b>";
		print "&nbsp;&nbsp;" . $mediaType;
		print "</b>";

 		print "<br>" . "\n";
		print $mediaTitle . "\n";

		print "</td>" . "\n";
		print "</tr>";
		print "</table>" . "\n";

    }
	if (!isset($j)) {
		$j=0;
	}else{
		$j=$j;
	}
	$media_data = $rowm['m_media'];
	print "<input type=\"hidden\" name=\"order1[$media_data]\" value=\"$j\" />";

    print "</li>";
    print "\n\n";;
    return true;

}
?>

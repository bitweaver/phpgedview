<?php
/**
 * Popup window for viewing images
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
 * @version $Id: imageview.php,v 1.3 2006/10/29 11:02:13 lsces Exp $
 * @package PhpGedView
 * @subpackage Media
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

if (!isset($filename)) $filename = "";
$filename = stripslashes($filename);

print_simple_header($pgv_lang["imageview"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
	var zoom = 100;
	function zoomin() {
		i = document.getElementById('theimage');
		zoom=zoom+10;
		i.style.width=Math.round((zoom/100)*imgwidth)+"px";
		i.style.height=null;
		document.getElementById('zoomval').value=Math.round(zoom);
	}
	function zoomout() {
		i = document.getElementById('theimage');
		zoom=zoom-10;
		if (zoom<10) zoom=10;
		i.style.width=Math.round((zoom/100)*imgwidth)+"px";
		i.style.height=null;
		document.getElementById('zoomval').value=Math.round(zoom);
	}
	function setzoom(perc) {
		i = document.getElementById('theimage');
		zoom=parseInt(perc);
		if (zoom<10) zoom=10;
		i.style.width=Math.round((zoom/100)*imgwidth)+"px";
		i.style.height=null;
	}
	function resetimage() {
		setzoom('100');
		document.getElementById('zoomval').value=zoom;
		i = document.getElementById('theimage');
		i.style.left='0px';
		i.style.top='0px';
	}
	var oldMx = 0;
	var oldMy = 0;
	var movei = "";
	function panimage() {
		if (movei=="") {
			oldMx = msX;
			oldMy = msY;
		}
		i = document.getElementById('theimage');
		//alert(i.style.top);
		movei = i;
		return false;
	}
	function releaseimage() {
		movei = "";
		return true;
	}
	// Main function to retrieve mouse x-y pos.s
	function getMouseXY(e) {
	  if (IE) { // grab the x-y pos.s if browser is IE
	    msX = event.clientX + document.documentElement.scrollLeft;
	    msY = event.clientY + document.documentElement.scrollTop;
	  } else {  // grab the x-y pos.s if browser is NS
	    msX = e.pageX;
	    msY = e.pageY;
	  }
	  // catch possible negative values in NS4
	  if (msX < 0){msX = 0;}
	  if (msY < 0){msY = 0;}
	  if (movei!="") {
		ileft = parseInt(movei.style.left);
		itop = parseInt(movei.style.top);
		ileft = ileft - (oldMx-msX);
		itop = itop - (oldMy-msY);
		movei.style.left = ileft+"px";
		movei.style.top = itop+"px";
		oldMx = msX;
		oldMy = msY;
		return false;
	  }
	}

	 function resizeWindow() {
		if (document.images) {
			if (document.images.length == 3) {
				height=document.images[0].height+80;
				width=document.images[0].width+20;
				if(width > screen.width-100) width = screen.width-100;
				if(height > screen.height-110) height = screen.height-110;
				if (document.layers) window.resizeTo(width+20,height+20)
				else if (document.all) window.resizeTo(width+30,height+50)
				else if (document.getElementById) window.resizeTo(width+40,height+20)
			}
			else setTimeout('resizeWindow()',1000);
		}
		resizeViewport();
		resetimage();
	}

	function resizeViewport() {
		if (IE) {
			pagewidth = document.documentElement.offsetWidth;
			pageheight = document.documentElement.offsetHeight;
		}
		else {
			pagewidth = window.outerWidth-25;
			pageheight = window.outerHeight-25;
		}
		viewport = document.getElementById("imagecropper");
		viewport.style.width=(pagewidth-35)+"px";
		viewport.style.height=(pageheight-60)+"px";
		i = document.getElementById('theimage');
		i.style.left="0px";
		i.style.top="0px";
		if ((pagewidth-40)-imgwidth < ((pageheight-65)-imgheight)) {
			i.style.width=(pagewidth-40)+"px";
			i.style.height=null;
			zoom = ((pagewidth-40) / imgwidth)*100;
		}
		else {
			i.style.height=(pageheight-65)+"px";
			i.style.width=null;
			zoom = ((pageheight-65) / imgheight)*100;
		}
		document.getElementById('zoomval').value=Math.round(zoom);
	}

	var IE = document.all?true:false;
	if (!IE) document.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP)
	document.onmousemove = getMouseXY;
	document.onmouseup = releaseimage;

	window.onresize = resizeViewport;
	//window.onload = resizeWindow;
-->
</script>
<?php
print "<form name=\"zoomform\" onsubmit=\"setzoom(document.getElementById('zoomval').value); return false;\" action=\"imageview.php\">";
$isExternal = strstr($filename, "://");
if (!$isExternal && (empty($filename) || (!@fclose(@fopen(filename_decode($filename),"r"))))) {
	print "<span class=\"error\">".$pgv_lang["file_not_found"]."&nbsp;".$filename."</span>";
	print "<br /><br /><div class=\"center\"><a href=\"javascript:;\" onclick=\"self.close();\">".$pgv_lang["close_window"]."</a></div>\n";
} else {
	print "<center><font size=\"6\"><a href=\"javascript:;\" onclick=\"zoomin(); return false;\">+</a> <a href=\"javascript:;\" onclick=\"zoomout();\">&ndash;</a> </font>";
	print "<input type=\"text\" size=\"2\" name=\"zoomval\" id=\"zoomval\" value=\"100\" />%\n";
	print "<input type=\"button\" value=\"".$pgv_lang["reset"]."\" onclick=\"resetimage(); return false;\" /></center>\n";
	$imgsize = findImageSize($filename);
	$imgwidth = $imgsize[0]+2;
	$imgheight = $imgsize[1]+2;
	print "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	print "var imgwidth = $imgwidth-5;\n var imgheight = $imgheight-5;\n";
	print "var landscape = false;\n";
	print "if (imgwidth > imgheight) landscape = true;\n";
	print "</script>\n";
	print '<br /><center><div id="imagecropper" style="position: relative; border: outset white 3px; background-color: black; overflow: auto; vertical-align: middle; text-align: center; width: '.$imgwidth.'px; height: '.$imgheight.'px; ">';
	print "\n<img id=\"theimage\" src=\"$filename\" style=\"position: absolute; left: 1px; top: 1px; cursor: move;\" onmousedown=\"panimage(); return false;\" alt=\"\" />\n";
	print '</div></center>';
}
print "</form>\n";
print "<div style=\"position: relative; \">\n";
print_simple_footer();
print "</div>\n";
?>

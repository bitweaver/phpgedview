<?php
/**
 * Display a timeline chart for a group of individuals
 *
 * Use the $pids array to set which individuals to show on the chart
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
 * This Page Is Valid XHTML 1.0 Transitional! > 08 August 2005
 *
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id: timeline.php,v 1.2 2006/10/01 22:44:02 lsces Exp $
 */

require_once("includes/controllers/timeline_ctrl.php");

print_header($pgv_lang["timeline_title"]);
?>
<script language="JavaScript" type="text/javascript">
<!--
function showhide(divbox, checkbox) {
	if (checkbox.checked) {
		MM_showHideLayers(divbox, ' ', 'show', ' ');
	}
	else {
		MM_showHideLayers(divbox, ' ', 'hide', ' ');
	}
}

var pastefield = null;
function paste_id(value) {
	pastefield.value=value;
}

var N = (document.all) ? 0 : 1;
var ob=null;
var Y=0;
var X=0;
var oldx=0;
var personnum=0;
var type=0;
var state=0;
var oldstate=0;
var boxmean = 0;

function ageMD(divbox, num) {
	ob=divbox;
	personnum=num;
	type=0;
	if (N) {
		X=ob.offsetLeft;
		Y=ob.offsetTop;
	}
	else {
		X=ob.offsetLeft;
		Y=ob.offsetTop;
		oldx = event.clientX + document.documentElement.scrollLeft;
	}
}

function factMD(divbox, num, mean) {
	if (ob!=null) return;
	ob=divbox;
	personnum=num;
	boxmean = mean;
	type=1;
	if (N) {
		oldx=ob.offsetLeft;
		oldlinew=0;
	}
	else {
		oldx = ob.offsetLeft;
		oldlinew = event.clientX + document.documentElement.scrollLeft;
	}
}

function MM(e) {
	if (ob) {
		tldiv = document.getElementById("timeline_chart");
		if (!tldiv) tldiv = document.getElementById("timeline_chart_rtl");
		if (type==0) {
			// age boxes
			newy = 0;
			newx = 0;
			if (N) {
				newy = e.pageY - tldiv.offsetTop;
				newx = e.pageX - tldiv.offsetLeft;
				if (oldx==0) oldx=newx;
			}
			else {
				newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
				newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
			}
			if ((newy >= topy-bheight/2)&&(newy<=bottomy)) newy = newy;
			else if (newy < topy-bheight/2) newy = topy-bheight/2;
			else newy = (bottomy-1);
			ob.style.top = newy+"px";
			tyear = ((newy+bheight-4 - topy) + scale)/scale + baseyear
			year = Math.floor(tyear);
			month = Math.floor((tyear*12)-(year*12));
			day = Math.floor((tyear*365)-(year*365 + month*30));
			mstamp = (year*365)+(month*30)+day;
			bdstamp = (birthyears[personnum]*365)+(birthmonths[personnum]*30)+birthdays[personnum];
			daydiff = mstamp - bdstamp;
			ba = 1;
			if (daydiff < 0 ) {
				ba = -1;
				daydiff = (bdstamp - mstamp);
			}
			yage = Math.floor(daydiff / 365);
			mage = Math.floor((daydiff-(yage*365))/30);
			dage = Math.floor(daydiff-(yage*365)-(mage*30));
			if (dage<0) mage = mage -1;
			if (dage<-30) {
				dage = 30+dage;
			}
			if (mage<0) yage = yage-1;
			if (mage<-11) {
				mage = 12+mage;
			}
			yearform = document.getElementById('yearform'+personnum);
			ageform = document.getElementById('ageform'+personnum);
			yearform.innerHTML = year+"      "+month+" <?php print get_first_letter($pgv_lang["month"]);?>   "+day+" <?php print get_first_letter($pgv_lang["day"]);?>";
			ageform.innerHTML = (ba*yage)+" <?php print get_first_letter($pgv_lang["year"]);?>   "+(ba*mage)+" <?php print get_first_letter($pgv_lang["month"]);?>   "+(ba*dage)+" <?php print get_first_letter($pgv_lang["day"]);?>";
			var line = document.getElementById('ageline'+personnum);
			temp = newx-oldx;
			if (textDirection=='rtl') temp = temp * -1;
			line.style.width=(line.width+temp)+"px";
			oldx=newx;
			return false;
		}
		else {
			newy = 0;
			newx = 0;
			if (N) {
				newy = e.pageY - tldiv.offsetTop;
				newx = e.pageX - tldiv.offsetLeft;
				if (oldx==0) oldx=newx;
				linewidth = e.pageX;
			}
			else {
				newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
				newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
				linewidth = event.clientX + document.documentElement.scrollLeft;
			}
			// get diagnal line box
			dbox = document.getElementById('dbox'+personnum);
			// set up limits
			if (boxmean-175 < topy) etopy = topy;
			else etopy = boxmean-175;
			if (boxmean+175 > bottomy) ebottomy = bottomy;
			else ebottomy = boxmean+175;
			// check if in the bounds of the limits
			if ((newy >= etopy)&&(newy<=ebottomy)) newy = newy;
			else if (newy < etopy) newy = etopy;
			else if (newy >ebottomy) newy = ebottomy;
			// calculate the change in Y position
			dy = newy-ob.offsetTop;
			// check if we are above the starting point and switch the background image
			if (newy < boxmean) {
				if (textDirection=='ltr') {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 100%";
				}
				else {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline2"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 0%";
				}
				dy = (-1)*dy;
				state=1;
				dbox.style.top = (newy+bheight/3)+"px";
			}
			else {
				if (textDirection=='ltr') {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline2"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 0%";
				}
				else {
					dbox.style.backgroundImage = "url('<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["dline"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 100%";
				}

				dbox.style.top = (boxmean+(bheight/3))+"px";
				state=0;
			}
			// the new X posistion moves the same as the y position
			if (textDirection=='ltr') newx = dbox.offsetLeft+Math.abs(newy-boxmean);
			else newx = dbox.offsetRight+Math.abs(newy-boxmean);
			// set the X position of the box
			if (textDirection=='ltr') ob.style.left=newx+"px";
			else ob.style.right=newx+"px";
			// set new top positions
			ob.style.top = newy+"px";
			// get the width for the diagnal box
			newwidth = (ob.offsetLeft-dbox.offsetLeft);
			// set the width
			dbox.style.width=newwidth+"px";
			if (textDirection=='rtl') dbox.style.right = (dbox.offsetRight - newwidth) + 'px';
			dbox.style.height=newwidth+"px";
			// change the line width to the change in the mouse X position
			line = document.getElementById('boxline'+personnum);
			if (oldlinew!=0) line.width=line.width+(linewidth-oldlinew);
			oldlinew = linewidth;
			oldx=newx;
			oldstate=state;
			return false;
		}
	}
}

function MU() {
	ob = null;
	oldx=0;
}

if (N) {
	document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP);
	//document.onmousedown = MD;
}
document.onmousemove = MM;
document.onmouseup = MU;
//-->
</script>
<h2><?php print $pgv_lang["timeline_chart"]; ?></h2>
<?php if (!$controller->isPrintPreview()) { ?><form name="people" action="timeline.php"><?php } ?>
<?php
$controller->checkPrivacy();
?>
<table class="<?php print $TEXT_DIRECTION; ?>">
	<tr>
	<?php
	$i=0;
	$count = count($controller->people);
	$half = $count;
	if ($count>5) $half = ceil($count/2);
	if (!$controller->isPrintPreview()) $half++;
	foreach($controller->people as $p=>$indi) {
		$sex = $indi->getSex();
		$pid = $indi->getXref();
		$col = $p % 6;
		if ($i==$half) print "</tr><tr>";
		$i++;
		?>
		<td class="person<?php print $col; ?>" style="padding: 5px;">
		<?php
		if ((!is_null($indi))&&($indi->canDisplayDetails())) {
			switch($sex) {
			case "M":
				$seximage = $PGV_IMAGE_DIR."/".$PGV_IMAGES["sex"]["small"];
				?>
				<img src="<?php print $seximage; ?>" title="<?php print $pgv_lang["male"]; ?>" alt="<?php print $pgv_lang["male"]; ?>" vspace="0" hspace="0" class="sex_image" border="0" />
				<?php
				break;
			case "F":
				$seximage = $PGV_IMAGE_DIR."/".$PGV_IMAGES["sexf"]["small"];
				?>
				<img src="<?php print $seximage; ?>" title="<?php print $pgv_lang["female"]; ?>" alt="<?php print $pgv_lang["female"]; ?>" vspace="0" hspace="0" class="sex_image" border="0" />
				<?php
				break;
			default:
				$seximage = $PGV_IMAGE_DIR."/".$PGV_IMAGES["sexn"]["small"];
				?>
				<img src="<?php print $seximage; ?>" title="<?php print $pgv_lang["sex"]." ".$pgv_lang["unknown"]; ?>" alt="<?php print $pgv_lang["sex"]." ".$pgv_lang["unknown"]; ?>" vspace="0" hspace="0" class="sex_image" border="0" />
				<?php
				break;
			}
		?>
 			<a href="individual.php?pid=<?php print $pid; ?>">&nbsp;<?php print PrintReady($indi->getName()); ?><br />
 			<?php $addname = $indi->getAddName(); if (strlen($addname) > 0) print PrintReady($addname); ?>
			</a>
			<input type="hidden" name="pids[<?php print $p; ?>]" value="<?php print $pid; ?>" />
			<?php if (!$controller->isPrintPreview()) {
				print "<br />";
				print_help_link("remove_person_help", "qm");
				?>
				<a href="timeline.php?<?php print $controller->pidlinks; ?>&amp;scale=<?php print $controller->scale; ?>&amp;remove=<?php print $pid;?>" >
				<span class="details1"><?php print $pgv_lang["remove_person"]; ?></span></a>
			<?php if (!empty($controller->birthyears[$pid])) { ?>
				<span class="details1"><br />
				<?php print_help_link("show_age_marker_help", "qm"); ?>
				<?php print $pgv_lang["show_age"]; ?>
				<input type="checkbox" name="agebar<?php print $p; ?>" value="ON" onclick="showhide('agebox<?php print $p; ?>', this);" />
				</span>
			<?php }
			} ?>
			<br />
		<?php
		}
		else {
			print_privacy_error($CONTACT_EMAIL);
			?>
			<input type="hidden" name="pids[<?php print $p; ?>]" value="<?php print $pid; ?>" />
			<?php if (!$controller->isPrintPreview()) {
				print "<br />";
				print_help_link("remove_person_help", "qm");
				?>
				<a href="timeline.php?<?php print $controller->pidlinks; ?>&amp;scale=<?php print $controller->scale; ?>&amp;remove=<?php print $pid;?>" >
				<span class="details1"><?php print $pgv_lang["remove_person"]; ?></span></a>
			<?php } ?>
			<br />
		<?php } ?>
		</td>
	<?php }
	if (!$controller->isPrintPreview()) {
		if (!isset($col)) $col = 0;
		?>
		<td class="person<?php print $col; ?>" style="padding: 5px" valign="top">
			<?php print_help_link("add_person_help", "qm"); ?>
			<?php print $pgv_lang["add_another"];?>&nbsp;
			<input class="pedigree_form" type="text" size="5" id="newpid" name="newpid" />&nbsp;
			<?php print_findindi_link("newpid",""); ?>
			<br />
			<br />
			<div style="text-align: center"><input type="submit" value="<?php print $pgv_lang["show"]; ?>" /></div>
		</td>
	<?php }
	if ((count($controller->people)>0)&&(!$controller->isPrintPreview())) {
		?>
		<td class="list_value" style="padding: 5px">
			<a href="<?php print $SCRIPT_NAME."?".$controller->pidlinks."scale=".($controller->scale+2); ?>"><?php print $pgv_lang["zoom_in"]; ?></a><br />
			<a href="<?php print $SCRIPT_NAME."?".$controller->pidlinks."scale=".($controller->scale-2); ?>"><?php print $pgv_lang["zoom_out"]; ?></a>
		</td>
	<?php } ?>
	</tr>
</table>
<?php if (!$controller->isPrintPreview()) { ?></form><?php } ?>
<?php
if (count($controller->people)>0) {
	?>
	<?php if ($controller->isPrintPreview()) print "\n\t".$pgv_lang['timeline_instructions']."<br /><br />"; ?>
<div id="timeline_chart">
	<!-- print the timeline line image -->
	<div id="line" style="position:absolute; <?php print $TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+20):"right: ".($basexoffset+20); ?>px; top: <?php print $baseyoffset; ?>px; ">
		<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["vline"]["other"]; ?>" width="3" height="<?php print ($baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale)); ?>" alt="" />
	</div>
	<!-- print divs for the grid -->
	<div id="scale<?php print $controller->baseyear; ?>" style="font-family: Arial; position:absolute; <?php print ($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset"); ?>px; top: <?php print ($baseyoffset-5); ?>px; font-size: 7pt; text-align: <?php print ($TEXT_DIRECTION =="ltr"?"left":"right"); ?>;">
	<?php print $controller->baseyear."--"; ?>
	</div>
	<?php
	for($i=$controller->baseyear+1; $i<$controller->topyear; $i++) {
		if ($i % (25/$controller->scale)==0)  {
			print "\n\t\t<div id=\"scale$i\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".floor($baseyoffset+(($i-$controller->baseyear)*$controller->scale)-$controller->scale/2)."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
			print $i."--";
			print "</div>";
		}
	}
	print "\n\t\t<div id=\"scale{$controller->topyear}\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".floor($baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale))."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
	print $controller->topyear."--";
	print "</div>";
	usort($controller->indifacts, "compare_facts");
	$factcount=0;
	foreach($controller->indifacts as $indexval => $fact) {
		$controller->print_time_fact($fact);
		$factcount++;
	}

	// print the age boxes
	foreach($controller->people as $p=>$indi) {
		$pid = $indi->getXref();
		$ageyoffset = $baseyoffset + ($controller->bheight*$p);
		$col = $p % 6;
		?>
		<div id="agebox<?php print $p; ?>" style="position:absolute; <?php print ($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+20):"right: ".($basexoffset+20)); ?>px; top:<?php print $ageyoffset; ?>px; height:<?php print $controller->bheight; ?>px; visibility: hidden;" onmousedown="ageMD(this, <?php print $p; ?>);">
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<img src="<?php print $PGV_IMAGE_DIR."/".$PGV_IMAGES["hline"]["other"]; ?>" name="ageline<?php print $p; ?>" id="ageline<?php print $p; ?>" align="left" hspace="0" vspace="0" width="25" height="3" alt="" />
					</td>
					<td valign="top">
						<?php
						$tyear = round(($ageyoffset+($controller->bheight/2))/$controller->scale)+$controller->baseyear;
						if (!empty($controller->birthyears[$pid])) {
						$tage = $tyear-$controller->birthyears[$pid];
						?>
						<table class="person<?php print $col; ?>" style="cursor: hand;">
							<tr>
								<td valign="top" width="120"><?php print $pgv_lang["year"]; ?>
									<span id="yearform<?php print $p; ?>" class="field">
									<?php print $tyear; ?>
									</span>
								</td>
								<td valign="top" width="130">(<?php print $pgv_lang["age"];?>
									<span id="ageform<?php print $p; ?>" class="field"><?php print $tage; ?></span>)
								</td>
							</tr>
						</table>
						<?php } ?>
					</td>
				</tr>
			</table><br /><br /><br />
		</div><br /><br /><br /><br />
	<?php } ?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var bottomy = <?php print ($baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale)); ?>-5;
	var topy = <?php print $baseyoffset;?>;
	var baseyear = <?php print $controller->baseyear-(25/$controller->scale); ?>;
	var birthyears = new Array();
	var birthmonths = new Array();
	var birthdays = new Array();
	<?php
	foreach($controller->people as $c=>$indi) {
		$pid = $indi->getXref();
		if (!empty($controller->birthyears[$pid])) print "\nbirthyears[".$c."]=".$controller->birthyears[$pid].";";
		if (!empty($controller->birthmonths[$pid])) print "\nbirthmonths[".$c."]=".$controller->birthmonths[$pid].";";
		if (!empty($controller->birthdays[$pid])) print "\nbirthdays[".$c."]=".$controller->birthdays[$pid].";";
	}
	?>

	var bheight=<?php print $controller->bheight;?>;
	var scale=<?php print $controller->scale;?>;
	//-->
	</script>
</div>
<?php } ?>
<script language="JavaScript" type="text/javascript">
<!--
	timeline_chart_div = document.getElementById("timeline_chart");
	if (!timeline_chart_div) timeline_chart_div = document.getElementById("timeline_chart_rtl");
	if (timeline_chart_div) timeline_chart_div.style.height = '<?php print $baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale*1.1); ?>px';
//-->
</script>
<?php
print_footer();
?>

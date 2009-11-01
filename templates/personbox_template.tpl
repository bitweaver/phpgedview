<div id="I{$boxID} links"
	style="position: absolute; left: 0px; top: 0px; width: {$lbwidth}px; visibility: hidden; z-index: '100';">
{$personlinks}</div>
<div id="out-{$boxID}" {$outBoxAdd}>
<!--  table helps to maintain spacing -->
<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="top">
		<div id="icons-{$boxID}"
			style="{$iconsStyleAdd} width: 25px; height: 50px;">{$icons}
		</div>
		{$thumbnail}
		<a onclick="event.cancelBubble = true;"
			href="individual.php?pid={$pid} ?>&amp;ged={$GEDCOM}"
			title="{$title}">
		<span id="namedef-{$boxID}" class="name{$style} {$classfacts}">
			{$name.$addname}
		</span>
		<span class="name{$style}"> {$genderImage}</span>
		{$showid} </a>
		<div id="fontdef-{$boxID}" class="details<?php print $style; ?>">
			<div id="inout2-{$boxID}" style="display: block;"><?php print $BirthDeath; ?></div>
		</div>
		<div id="inout-{$boxID}" style="display: none;">
			<div id="LOADING-inout-{$boxID}">Loading</div>
		</div>
</td></tr></table>
</div>

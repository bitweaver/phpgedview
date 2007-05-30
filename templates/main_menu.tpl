{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/main_menu.tpl,v 1.1 2007/05/30 07:24:17 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle} {tr}Upload GEDCOM{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
		<h1>Charts</h1>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}pedigree.php">Pedigree Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}descendancy.php">Descendancy Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}ancestry.php.php">Ancestry Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}compact.php">Compact Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}fanchart.php">Circle Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}hourglass.php">Hourglass Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}familybook.php">Family Book Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}timeline.php">Timeline Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}relationship.php">Relationship Chart</a>
		<br />
		<h1>Lists</h1>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}indlist.php">Individual List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}famlist.php">Family List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}sourcelist.php">Source List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}repolist.php">Repository List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}placelist.php">Place Hierarchy</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}medialist.php">MultiMedia List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}patriarchlist.php">Patriarch List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}aliveinyear.php">Alive in Year</a>
		<br />
		<h1>Calendar</h1>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}calendar.php">View Day</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}calendar.php?action=calendar">View Month</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}calendar.php?action=year">View Year</a>
		<br />
		<h1>Reports</h1>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/addresslabels.xml">Address Labels</a>
		<br />
	{/strip}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

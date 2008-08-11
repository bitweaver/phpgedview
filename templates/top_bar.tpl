{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/top_bar.tpl,v 1.3 2008/08/11 15:21:49 lsces Exp $ *}
	<div class="data">
		<table class="data">
			<caption><h1>{tr}Gedcom documents navigation menu{/tr}</h1></caption>
			<tr>
				<th>{tr}Charts{/tr}</th>
				<th>{tr}Lists{/tr}</th>
				<th>{tr}Anniversary Calendar{/tr}</th>
				<th><a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=choose">{tr}Reports{/tr}</a></th>
			</tr>

		<tr class="{cycle values="even,odd"}">
		<td style="text-align:center;">
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}pedigree.php">Pedigree Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}descendancy.php">Descendancy Chart</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}ancestry.php">Ancestry Chart</a>
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
		</td>

		<td style="text-align:center;">
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}indilist.php">Individual List</a>
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
		</td>

		<td style="text-align:center;">
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}calendar.php">View Day</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}calendar.php?action=calendar">View Month</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}calendar.php?action=year">View Year</a>
		</td>

		<td style="text-align:center;">
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/addresslabels.xml">Address Labels</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/addresslist.xml">Address List</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/ahnentafel.xml">Ahnentafel Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/bdmlist.xml">Births, Deaths, Marriages</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/birthlist.xml">Birth Date and Place Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/deathlist.xml">Death Date and Place Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/descendancy.xml">Descendancy Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/familygroup.xml">Family Group Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/individual.xml">Individual Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/marrlist.xml">Marriage Date and Place Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/pedigree.xml">Pedigree Chart (Portrait)</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/pedigree_l.xml">Pedigree Chart (Landscape)</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/relativelist.xml">Relatives Report</a>
		<br />
		<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}reportengine.php?action=setup&amp;report=reports/relativelist_ext.xml">Expanded Relatives Report</a>
		</td>
		</tr>
		</table>
	</div>
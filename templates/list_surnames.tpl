{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/list_surnames.tpl,v 1.3 2007/06/04 10:03:21 lsces Exp $ *}
	<table id="{$table_id}" class="sortable list_table center">
	<tr>
	<th class="list_label"> </th>
	<th class="list_label">{tr}Surname{/tr}</th>
	<th class="list_label">
	{if isset($family) } {tr}Families{/tr}
	{else} {tr}Individuals{/tr}
	{/if}
	</th>
	</tr>

	{* table body *}
	{foreach from=$surnames key=valId item=value }
		{if isset($value.upper) }
			<tr>
				<td class="list_value_wrap rela list_item">{$valId+1+$listInfo.offset}</td>
				<td class="list_value_wrap" align="left">
					<a href="{$url}{$value.upper}" class="list_item name1">{$value.upper}</a>
				&nbsp;</td>
				<td class="list_value_wrap">
					<a href="{$url}{$value.upper}" class="list_item name2">{$value.count}</a>
				</td>
			</tr>
		{/if}
	{/foreach}

	{* table footer *}
	<tr class="sortbottom">
	<td class="list_item">&nbsp;</td>
	<td class="list_label name1">{$listInfo.total_records}</td>
	<td class="list_label name2">{$listInfo.sub_total}</td>
	</tr>
	</table>

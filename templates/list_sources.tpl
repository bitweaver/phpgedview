{* $Header$ *}
	<table id="{$table_id}" class="sortable list_table center">
	<tr>
	<th class="list_label"></th>
	<th class="list_label">SOUR</th>
	<th class="list_label">Title</th>
	<th class="list_label">Location</th>
	</tr>

	{* table body *}
	{foreach from=$sourcelist key=valId item=value }
		{if isset($value.name) }
			<tr>
				<td class="list_value_wrap" align="left">
					{$value.n}
				&nbsp;</td>
				<td class="list_value_wrap" align="left">
					<a href="{$value.url}" class="list_item name1">{$valId}</a>
				&nbsp;</td>
				<td class="list_value_wrap">
					<a href="{$value.url}" class="list_item name2">{$value.name}</a>
				</td>
				<td class="list_value_wrap">
					{$value.place}
				</td>
			</tr>
		{/if}
	{/foreach}

	{* table footer *}
	<tr class=\"sortbottom\">
	<td class=\"list_item\">Total</td>
	<td class=\"list_label name2\">{$total}</td>
	</tr>
	</table>


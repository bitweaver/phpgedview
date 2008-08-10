{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/list_families.tpl,v 1.1 2008/08/10 11:46:26 lsces Exp $ *}
	<table id="{$table_id}" class="sortable list_table center">
	<tr>
	<th class="list_label">{tr}Surname{/tr}</th>
	<th class="list_label">Marriage</th>
	<th class="list_label">Marriage Place</th>
	<th class="list_label">Number of children</th>
	</tr>

	{* table body *}
	{foreach from=$families key=valId item=value }
		{if isset($value.name) }
			<tr>
				<td class="list_value_wrap" align="left">
					<a href="{$value.url}" class="list_item name1">{$value.name}</a>
				&nbsp;</td>
				<td class="list_value_wrap">
					{$value.marriagedate}
				</td>
				<td class="list_value_wrap">
					<a href="{$value.placeurl}" class="list_item name2">{$value.marriageplace}</a>
				</td>
				<td class="list_value_wrap">
					{$value.f_numchil}
				</td>
			</tr>
		{/if}
	{/foreach}

	{* table footer *}
	<tr class=\"sortbottom\">
	<td class=\"list_item\">&nbsp;</td>
	<td class=\"list_label name2\">{$surname_total}</td>
	</tr>
	</table>


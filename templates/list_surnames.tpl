{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/list_surnames.tpl,v 1.1 2007/06/02 12:32:56 lsces Exp $ *}
	<table id="{$table_id}" class="sortable list_table center">
	<tr>
	<th class="list_label"> </th>
	<th class="list_label">{tr}Surname{/tr}</th>
	<th class="list_label">{tr}Individuals{/tr}
{*	if ($target=="FAM") echo $pgv_lang["families"]; else echo $pgv_lang["individuals"]; *}
{*	if ($target=="FAM") echo $pgv_lang["spouses"]; else echo $pgv_lang["individuals"]; *}
	</th>
	</tr>

	{* table body *}
	{foreach from=$surnames key=valId item=value }
		{if isset($value.name) }
			<tr>
				<td class="list_value_wrap rela list_item">{$value.n}</td>
				<td class="list_value_wrap" align="left">
					<a href="{$value.url}" class="list_item name1">{$value.name}</a>
				&nbsp;</td>
				<td class="list_value_wrap">
					<a href="{$value.url}" class="list_item name2">{$value.match}</a>
				</td>
			</tr>
		{/if}
	{/foreach}

	{* table footer *}
	<tr class=\"sortbottom\">
	<td class=\"list_item\">&nbsp;</td>
	<td class=\"list_item\">&nbsp;</td>
	<td class=\"list_label name2\">{$surname_total}</td>
	</tr>
	</table>


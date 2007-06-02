{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/list_names.tpl,v 1.1 2007/06/02 12:32:56 lsces Exp $ *}
	<table id="{$table_id}" class="sortable list_table center">
	<tr>
	<th class="list_label">{tr}Surname{/tr}</th>
	<th class="list_label">Birth</th>
	<th class="list_label">Birth Place</th>
	<th class="list_label">Death</th>
	<th class="list_label">Number of children</th>
	</tr>

	{* table body *}
	{foreach from=$names key=valId item=value }
		{if isset($value.name) }
			<tr>
				<td class="list_value_wrap" align="left">
					<a href="{$value.url}" class="list_item name1">{$value.name}</a>
				&nbsp;</td>
				<td class="list_value_wrap">
					<a href="{$value.dateurl}" class="list_item name2">{$value.birthdate}</a>
				</td>
				<td class="list_value_wrap">
					<a href="{$value.placeurl}" class="list_item name2">{$value.birthplace}</a>
				</td>
				<td class="list_value_wrap">
					<a href="{$value.deathdate}" class="list_item name2">{$value.deathdate}</a>
				</td>
				<td class="list_value_wrap">
					{$value.noc}
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


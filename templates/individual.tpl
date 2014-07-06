{* $Header$ *}
<div class="floaticon">{bithelp}</div>
{strip}
<div class="gedcom_indi"><!-- start .gedcom_indi -->
	<div class="header">
		<h1>{$pagetitle}</h1>
		{* <img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border="0" title="Individuals" alt="Individuals" />&nbsp;&nbsp; *}
	</div>

	{formfeedback error=$errors}

	<div class="body">
{if !$controller->indi->canDisplayDetails()}
	<h3>No permission - need to add error block</h3>
{else}
	{jstabs}
		{jstab title="Personal Facts and Details"}
			<table class="facts_table">
				<tr id="row_top">
					<td></td>
					<td class="descriptionbox rela">
					</td>
				</tr>
			{foreach from=$indifacts key=key item=value}
				{* print_fact($value[1], $controller->pid, $value[0], $controller->indi->getGedcomRecord()); *}
				{$value[1]}<br />
			{/foreach}
			{if !$controller->isPrintPreview() && $controller->indi->canDisplayDetails()}
				{* print_add_new_fact($pid, $indifacts, "INDI") *}
				<tr>
					<td class="descriptionbox"> {* help_link("add_new_facts_help", "qm") *} Add New Fact</td>
					<td class="optionbox">
						<form method="get" name="newfactform" action="" onsubmit="return false;">
						<select id="newfact" name="newfact">\n";
							{foreach from=$addfacts key=indexval item=fact}
  								<option value="{$fact}">{$factarray[$fact]} [{$fact}]</option>
							{/foreach}
								<option value="EVEN">Event [EVEN]</option>
						</select>
						<input type="button" value="Add" onclick="add_record('$id', 'newfact');" />
						</form>
					</td>
				</tr>
			{/if}
			</table>
		{/jstab}
		{jstab title="Notes"}
			<table class="facts_table">
			</table>
		{/jstab}
		{jstab title="Sources"}
			<table class="facts_table">
			</table>
		{/jstab}
		{jstab title="Media"}
			<table class="facts_table">
			</table>
		{/jstab}

		{jstab title="Close relatives"}
			<span class="subheaders">Relatives</span>
			<table class="facts_table">
				<tr>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewparent('{$controller->pid}', 'HUSB');">{tr}Add Father{/tr}</a></td>
				</tr>
				<tr>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewparent('{$controller->pid}', 'WIFE');">{tr}Add Mother{/tr}</a></td>
				</tr>
			</table>
			{foreach from=$families key=famid item=family}
				<span class="subheaders">Brothers and Sisters</span>
				<table class="facts_table">
					<tr>
						<td><img src="{$family->thumb}" border="0" class="icon" alt="" /></td>
						<td><span class="subheaders">{$controller->indi->getChildFamilyLabel($family)}</span> 
						</td>
					</tr>
				</table>
			{/foreach}
			{foreach from=$stepfams key=famid item=family}
				<span class="subheaders">Step Brothers and Sisters</span>
				<table class="facts_table">
					<tr>
						<td><img src="{$family->thumb}" border="0" class="icon" alt="" /></td>
						<td><span class="subheaders">{$family->labelgetName()}</span> 
						</td>
					</tr>
				</table>
			{/foreach}
		{/jstab}

		{jstab title="Research Assistant"}
		{/jstab}
		{jstab title="Map Links"}
		{/jstab}
	{/jstabs}
{/if}
	</div><!-- end .body -->
{/strip}
</div><!-- end .gedcom_indi -->

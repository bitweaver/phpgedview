{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}Step 4 of 4{/tr}: {tr}Upload GEDCOM{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
	{form legend="GEDCOM Upload" id="GEDCOM Upload"}
		{forminput}
			{* TODO: Layout for Hebrew *}
			<tr><td class="topbottombar $TEXT_DIRECTION\">
			{tr}Import Statistics{/tr}
			</td></tr>
			<tr><td class="optionbox">
			<table cellspacing="20px"><tr><td class="optionbox" style="vertical-align: top;">
			{if isset($skip_table)} <br />...
			{else}
				{$show_table1}
				{if $marr_names eq "yes"} </td><td class=\"optionbox\">{$show_table_marr}{/if}
			{/if}
			</td></tr></table>\n";
			{* NOTE: Finished Links *}
			</td></tr>";
		{/forminput}

		<div class="control-group submit">
			<input type="submit" class="btn" name="continue" value="{tr}Upload GEDCOM{/tr}" />
		</div>
	{/form}
	{/strip}
	</div><!-- end .body -->

</div><!-- end .gedcom -->
		
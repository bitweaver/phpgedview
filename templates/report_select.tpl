{* $Header$ *}
<div class="floaticon">{bithelp}</div>
{strip}
<div class="repoert_gen"><!-- start .report_gen -->
	<div class="header">
		<h1>{$pagetitle}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		<form name="choosereport" method="get" action="reportengine.php">
		<input type="hidden" name="action" value="setup" />
		<input type="hidden" name="output" value="$output" />
			<table class="facts_table width40 center $TEXT_DIRECTION">
				<tr><td class="topbottombar" colspan="2">Choose Report</td></tr>
				<tr><td class="descriptionbox wrap width33 vmiddle">Select report</td>
				<td class="optionbox">
					<select name="report">
					{foreach from=$reports key=file item=report }
						<option value="{$file}">{$report.title}</option>
					{/foreach}
					</select></td></tr>
				<tr><td class="topbottombar" colspan="2"><input type="submit" value="Go" /></td></tr>
			</table>
		</form>
	</div><!-- end .body -->
{/strip}
</div><!-- end .gedcom_indi -->
	
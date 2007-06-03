{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/report_menu.tpl,v 1.3 2007/06/03 20:37:24 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{$pagetitle}</h1>
		{include file="bitpackage:phpgedview/top_bar.tpl"}
	</div>

	{formfeedback error=$errors}

	<div class="body">
		{form legend="Select Report"}
		<input type="hidden" name="action" value="setup" />
		<input type="hidden" name="output" value="$output" />
		<div class="row">
			{formlabel label="Select Report" for="report"}
			{forminput}
				<select name="report">
				{foreach from = $reports key=myId item=report}
					<option value="{$report.file}">{$report.title}</option>
				{/foreach}
				</select>
				{formhelp note="Select report to produce."}
			{/forminput}
		</div>
		
		<div class="row submit">
			<input type="submit" name="report_select" value="{tr}Process report parameters{/tr}" />
		</div>
		{/form}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

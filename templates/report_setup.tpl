{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/report_setup.tpl,v 1.1 2007/05/31 13:56:58 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{$pagetitle}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		<form name="setupreport" method="get" action="reportengine.php">
		<input type="hidden" name="action" value="run" />
		<input type="hidden" name="report" value="$report" />
		<input type="hidden" name="download" value="" />
		<input type="hidden" name="output" value="PDF" />
		<table class="facts_table width50 center $TEXT_DIRECTION">
			<tr>
				<td class="top""><h2>{$report}</h2>
				</td>
				<td class="optionbox">{$report_array.description}</td>
			</tr>
			{foreach from=$report_array.inputs key=inputId item=input}
				<tr>
					<td class="top">
						{$input.name} - {$input.type} - {$input.lookup} - {$input.options}	
					</td>
				</tr>
			{/foreach}
			<tr>
				<td class="top">
					Select output format
					<select name="output">
						<option value="HTML">HTML</option>
						<option value="PDF">PDF</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="2">
					<input type="submit" value="Download Report" onclick="document.setupreport.elements['download'].value='1';"/>
				</td>
			</tr>
		</table>
		</form>
	</div><!-- end .body -->

</div><!-- end .gedcom -->
		
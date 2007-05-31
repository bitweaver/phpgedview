{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/report_setup.tpl,v 1.2 2007/05/31 16:54:38 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{$pagetitle}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		<form name="setupreport" method="get" action="bit_reportengine.php">
		<input type="hidden" name="action" value="run" />
		<input type="hidden" name="report" value="{$report}" />
		<input type="hidden" name="download" value="" />
		<input type="hidden" name="output" value="PDF" />
		<table class="facts_table" width="100%">
			<tr>
				<td class="top""><h2>{$report_array.name}</h2>
				</td>
				<td class="optionbox">{$report_array.description}</td>
			</tr>
			{foreach from=$report_array.inputs key=inputId item=input}
				<tr>
					<td class="top" colspan="1" >
						<input type="hidden" name="varnames[]" value="{$input.name}" />
						{$input.value}
					</td>
					<td class="top" colspan="2" >
						{if $input.type	eq 'text'}
							<input type="text" name="vars[{$input.name}]" id="{$input.name}" value="{$input.default}" style="direction: ltr;" />
							{if $input.lookup eq 'DATE'}
								[Date selection]	
							{elseif $input.lookup eq 'PLAC'}
								[Place lookup]	
							{elseif $input.lookup eq 'INDI'}
								[Individual lookup]	
							{elseif $input.lookup eq 'FAM'}
								[Family lookup]	
							{/if}
						{elseif $input.type	eq 'checkbox'}
							<input type="checkbox" name="vars[{$input.name}]" id="{$input.name}" value="1" {if $input.default eq '1'} checked="checked" {/if} />
						{elseif $input.type	eq 'select'}
							<select name="vars[{$input.name}]" id="{$input.name}_var">;
								{foreach from=$input.select key=selectId item=select}
									<option value="{$select}">{$select}</option>
								{/foreach}
							</select>
						{else}
							{$input.name} - {$input.type} - {$input.lookup} - {$input.options}
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
				<td class="top">
					Select output format
				</td>
				<td class="top">
					<select name="output">
						<option value="PDF">PDF</option>
						<option value="HTML">HTML</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="3" style="text-align:center;">
					<input type="submit" value="Download Report" onclick="document.setupreport.elements['download'].value='1';"/>
				</td>
			</tr>
		</table>
		</form>
	</div><!-- end .body -->

</div><!-- end .gedcom -->
		
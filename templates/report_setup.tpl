{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{$report_array.name}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		<div class="control-group">
			{$report_array.description}
		</div>

		{form legend="Report Filter Settings"}
		<input type="hidden" name="action" value="run" />
		<input type="hidden" name="report" value="{$report}" />
		<input type="hidden" name="download" value="" />
		<input type="hidden" name="output" value="PDF" />

		{foreach from=$report_array.inputs key=inputId item=input}
			<div class="control-group">
				<input type="hidden" name="varnames[]" value="{$input.name}" />
				{formlabel label="`$input.value`" for="`$input.name`"}
				{forminput}
					{if $input.type	eq 'text'}
						{if $input.lookup eq 'DATE'}
							<input type="hidden" id="{$input.name}" name="vars[{$input.name}]" value="{$input.default|cal_date_format:"%B %e, %Y %H:%M %Z"}" />
							<span class="highlight" style="cursor:pointer;" title="{tr}Date Selector{/tr}" id="datrigger_{$input.name}">{$input.default|bit_long_date}</span>
								&nbsp;&nbsp;&nbsp;<small>&laquo;&nbsp;{tr}click to change{/tr}</small>
								<script type="text/javascript">/* <![CDATA[ */
								function gotocal_{$input.name}() {ldelim}
									document.getElementById('f').submit();
								{rdelim}
							/* ]]> */</script>
							{jscalendar inputField=$input.name time=$input.default onUpdate=gotocal_`$input.name` displayArea=datrigger_`$input.name` daFormat=$gBitSystem->getConfig('site_long_date_format')}	
						{elseif $input.lookup eq 'PLAC'}
							<input type="text" name="vars[{$input.name}]" id="{$input.name}" value="{$input.default}" style="direction: ltr;" />
							[Place lookup]	
						{elseif $input.lookup eq 'INDI'}
							<input type="text" name="vars[{$input.name}]" id="{$input.name}" value="{$input.default}" style="direction: ltr;" />
							[Individual lookup]	
						{elseif $input.lookup eq 'FAM'}
							<input type="text" name="vars[{$input.name}]" id="{$input.name}" value="{$input.default}" style="direction: ltr;" />
							[Family lookup]
						{else}
							<input type="text" name="vars[{$input.name}]" id="{$input.name}" value="{$input.default}" style="direction: ltr;" />	
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
				{/forminput}
			</div>
		{/foreach}

		<div class="control-group">
			{formlabel label="Select output format" for="output"}
			{forminput}
				<select name="output">
					<option value="PDF">PDF</option>
					<option value="HTML">HTML</option>
				</select>
				{formhelp note="Select report output format."}
			{/forminput}
		</div>

		<div class="control-group submit">
			<input type="submit" name="report_submit" value="Download Report" onclick="document.setupreport.elements['download'].value='1';" />
		</div>
		{/form}
	</div><!-- end .body -->

</div><!-- end .gedcom -->
		
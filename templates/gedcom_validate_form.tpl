{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}Step 3 of 4:{/tr} {tr}Upload GEDCOM{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
	{form legend="GEDCOM Upload" id="GEDCOM Upload"}
		{forminput}
			<input type = "hidden" name="verify" value="validate_form" />
			<input type = "hidden" name="GEDFILENAME" value="{$GEDFILENAME}" />
			<input type = "hidden" name="bakfile" value="{$bakfile}" />
			<input type = "hidden" name="path" value="{$path}" />
			<input type = "hidden" name="no_upload" value="{$no_upload}" />
			<input type = "hidden" name="override" value="{$override}" />
			<input type = "hidden" name="ok" value="{$ok}" />
			<tr><td class="topbottombar $TEXT_DIRECTION" colspan="2">
				<a href="javascript: {tr}Verify GEDCOM{/tr}" onclick="expand_layer('verify_gedcom'); return false;"> 
					{if $startimport eq "true"} <img id="upload_gedcom_img" src="image/minus.gif" border="0" width="11" height="11" alt="" > 
					{else} <img id="upload_gedcom_img" src="image/plus.gif" border="0" width="11" height="11" alt="" >
					{/if}
				</a>
			{* print_help_link("validate_gedcom_help", "qm","validate_gedcom") *}
			&nbsp;<a href="javascript: {tr}Validate GEDCOM{/tr}" onclick="expand_layer('validate_gedcom');return false">{tr}Validate GEDCOM{/tr}</a>
			</td></tr>
			<tr><td class="optionbox">
			<div id="validate_gedcom" style="display: 
			{if $startimport ne "true"} block 
			{else} none {/if} 
			">
			<table class="facts_table">
			<tr><td class="descriptionbox" colspan="2">Performing GEDCOM validation...<br />
			{if !empty($error)} <span class="error">$error</span> {/if}
		
			{if $cleanup_needed eq 'false'}
				Valid GEDCOM detected. No cleanup required.
				</td></tr>
			{else}
				<input type="hidden" name="cleanup_needed" value="cleanup_needed">
				{if $l_write eq 'false' }
					<span class="error">{tr}The GEDCOM file{/tr}, $GEDCOM, {tr}is not writable. Please check attributes and access rights{/tr}</span>
					</td></tr>
				{/if}
				{* NOTE: Check for head cleanup *}
				{if $l_headcleanup eq 'true' }
					<tr><td class="optionbox wrap" colspan="2">
					{* print_help_link("invalid_header_help", "qm", "invalid_header") *}
					<span class="error">{tr}~INVALID GEDCOM HEADER~{/tr}</span>\n
					</td></tr>
				{/if}
				{*  NOTE: Check for mac file cleanup *}
				{if $l_macfilecleanup eq 'true' }
					<tr><td class="optionbox wrap" colspan="2">
					{*  print_help_link("macfile_detected_help", "qm", "macfile_detected" ) *}
					<span class="error">{tr}Macintosh file detected.  On cleanup your file will be converted to a DOS file.{/tr}</span>\n
					</td></tr>
				{/if}
				{*  NOTE: Check for line endings cleanup *}
				{if $l_lineendingscleanup eq 'true' }
					<tr><td class="optionbox wrap" colspan="2">
					{*  print_help_link("empty_lines_detected_help", "qm", "empty_lines_detected") *}
					<span class="error">{tr}Empty lines were detected in your GEDCOM file.	On cleanup, these empty lines will be removed.{/tr}</span>\n
					</td></tr>
				{/if}
				{*  NOTE: Check for place cleanup *}
				{if $l_placecleanup eq 'true' }
					<tr><td class="optionbox wrap" colspan="2">
					<table class="facts_table">
					<tr><td class="optionbox wrap" colspan="2">
					<span class="error">{tr}Invalid place encodings were detected.  These errors should be fixed.{/tr}</span>\n
					</td></tr>
					<tr><td class="descriptionbox wrap width20">
					{*  print_help_link("cleanup_places_help", "qm", "cleanup_places") *}
					{tr}Cleanup Places{/tr}
					</td><td class="optionbox" colspan="2"><select name="cleanup_places">\n
					<option value="YES" selected="selected">{tr}yes{/tr}</option>
					<option value="NO">{tr}no{/tr}</option>\n</select>
					</td></tr>
					</td></tr><tr><td class="optionbox" colspan="2">{tr}Example of invalid place from your GEDCOM:{/tr}<br />".PrintReady(nl2br($placesample[0]));
					</table>\n
					</td></tr>
				{/if}
				{* NOTE: Check for date cleanup *}
				{if $l_datecleanup eq 'true' }
					<tr><td class="optionbox wrap" colspan="2">
					<span class="error">".$pgv_lang["invalid_dates"]."</span>\n
					<table class="facts_table">
					<tr><td class="descriptionbox width20">
					{*	print_help_link("detected_date_help", "qm") *}
					{tr}Date Format{/tr}:					
					</td><td class="optionbox" colspan="2">
					{if isset($datesample.choose) }
						<select name="datetype">\n
							<option value="1">{tr}Day{/tr} {tr}before{/tr} {tr}Month{/tr} (DD MM YYYY)</option>
							<option value="2">{tr}Month{/tr} {tr}before{/tr} {tr}Day{/tr} (MM DD YYYY)</option>
						</select>
					{else}
					<input type="hidden" name="datetype" value="3" />
					</td></tr><tr><td class="optionbox" colspan="2">{tr}Example of invalid date from your GEDCOM:{/tr}<br />{$datesample[0]}
					</td></tr>
					</table>\n
					</td></tr>
					{/if}
				{/if}
				{* NOTE: Check for ansi encoding *}
				{if $l_isansi eq 'true' }
					<tr><td class="optionbox" colspan="2">
					<span class="error">{tr}ANSI file encoding detected. PhpGedView works best with files encoded in UTF-8.{/tr}</span>\n
					<table class="facts_table">
					<tr><td class="descriptionbox wrap width20">
					{* print_help_link("detected_ansi2utf_help", "qm", "ansi_to_utf8") *}
					{tr}Convert this ANSI encoded GEDCOM to UTF-8?{/tr}
					</td><td class="optionbox">
						<select name="utf8convert">
							<option value="YES" selected="selected">{tr}yes{/tr}</option>
							<option value="NO">{tr}no{/tr}</option>
						</select>
					</td></tr>
					</table>\n
				{/if}
			{/if}
		{/forminput}

		<div class="row submit">
			<input type="submit" name="continue" value="{tr}Upload GEDCOM{/tr}" />
		</div>
	{/form}
	{/strip}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}Step 2 of 4:{/tr} {tr}Upload GEDCOM{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
	{form legend="GEDCOM Upload" id="GEDCOM Upload"}
		{forminput}
			<input type="hidden" name="no_upload" value="" />
			<input type="hidden" name="check" value="" />
			<!--<input type="hidden" name="override" value="<?php if (isset($override)) print $override; ?>" />-->
			<input type="hidden" name="verify" value="validate_form" />
			<input type="hidden" name="GEDFILENAME" value="{$GEDFILENAME}" />
			<input type="hidden" name="bakfile" value="{$bakfile}" />
			<input type="hidden" name="path" value="{$path}" />

			<table class="facts_table center $text_dir">
			<tr><td class="topbottombar $TEXT_DIRECTION" colspan="2">
				<a href="javascript: {tr}Verify GEDCOM{/tr}" onclick="expand_layer('verify_gedcom'); return false;"> 
					{if $startimport eq "true"} <img id="upload_gedcom_img" src="image/minus.gif" border="0" width="11" height="11" alt="" > 
					{else} <img id="upload_gedcom_img" src="image/plus.gif" border="0" width="11" height="11" alt="" >
					{/if}
				</a>
				{formhelp note="{tr}Here you can choose to either continue with the upload and import of this GEDCOM file or to abort the upload and import.{/tr}"}
				&nbsp;<a href="javascript: {tr}Verify GEDCOM{/tr}" onclick="expand_layer('verify_gedcom');return false">{tr}Verify GEDCOM{/tr}</a>
			</td></tr>
			<tr><td class="descriptionbox width20 wrap" colspan="2">GEDCOM File:</td>
				<td class="optionbox">{if isset($file) } {$file} {/if}</td>
				{if $imported eq 'true' } <span class=error>A GEDCOM with this file name has already been imported into the database.</span><br /><br />{/if}
				{if $bakfile ne "" }{tr}A GEDCOM file with the same name has been found. If you choose to continue, the old GEDCOM file will be replaced with the file that you uploaded and the Import process will begin again.  If you choose to cancel, the old GEDCOM will remain unchanged.{/tr}</td></tr>{/if}
				{if $imported eq 'true' or $bakfile ne "" } 
					</td></tr>
					<tr><td class="descriptionbox width=20 wrap">{tr}Do you want to erase the old data and replace it with this new data?{/tr}</td><td class=\"optionbox vmiddle\">
					<select name="override"
						<option value="yes" 
						{if $override == "yes" } selected="selected" {/if}
						>{tr}yes{/tr}</option>
						<option value="no"
						{if $override != "yes" } selected="selected" {/if}
						>{tr}no{/tr}</option>
					</select></td></tr>
					<tr><td class="optionbox wrap" colspan="2">
				{/if}
			</td></tr>
			</table>
		{/forminput}

		<div class="row submit">
			<input type="submit" name="continue" value="{tr}Upload GEDCOM{/tr}" />
		</div>
	{/form}
	{/strip}
	</div><!-- end .body -->

</div><!-- end .gedcom -->
		
{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}Step{/tr} 2 of 4 - {tr}Upload GEDCOM{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
	{form legend="GEDCOM Upload" id="GEDCOM Upload"}
		{forminput}
			<table class="facts_table center $text_dir">
			<tr><td class="topbottombar $TEXT_DIRECTION" colspan="2">
				<a href="javascript: verify_gedcom" onclick="expand_layer('verify_gedcom'); return false;"> 
					{if $startimport eq "true"} <img id="upload_gedcom_img" src="image/minus.gif" border="0" width="11" height="11" alt="" > 
					{else} <img id="upload_gedcom_img" src="image/plus.gif" border="0" width="11" height="11" alt="" >
					{/if}
				</a>
				<!-- print_help_link("verify_gedcom_help", "qm", "verify_gedcom"); -->
				&nbsp;<a href="javascript: verify_gedcom" onclick="expand_layer('verify_gedcom');return false">verify_gedcom</a>
			</td></tr>
			<tr><td class="optionbox" colspan="2">
				<div id="verify_gedcom" style="display: 
				{if $startimport != "true"} block 
				{else } none
				{/if} ">
			</td></tr>
			<tr><td class="descriptionbox width20 wrap" colspan="2"
				<input type="hidden" name="no_upload" value="" />
				<input type="hidden" name="check" value="" />
				<!--<input type="hidden" name="override" value="<?php if (isset($override)) print $override; ?>" />-->
				<input type="hidden" name="verify" value="validate_form" />
				<input type="hidden" name="GEDFILENAME" value="{$GEDFILENAME}" />
				<input type="hidden" name="bakfile" value="{$bakfile}" />
				<input type="hidden" name="path" value="{$path}" />
		
			{if $imported eq 'true' } <span class=error>dataset_exists</span><br /><br />{/if}
			{if $bakfile ne "" } verify_upload_instructions</td></tr>{/if}
			{if $imported eq 'true' or $bakfile ne "" } 
				<tr><td class="descriptionbox width20 wrap">empty_dataset</td><td class=\"optionbox vmiddle\">
				<select name="override"
					<option value="yes" 
					{if $override == "yes" } selected="selected" {/if}
					>{tr}yes{/tr}</option>
					<option value="no"
					{if $override != "yes" } selected="selected" {/if}
					>{tr}no{/tr}</option>
				</select></td></tr><tr><td class="optionbox wrap" colspan="2">
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
		
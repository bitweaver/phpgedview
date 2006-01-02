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
			<input type="hidden" name="startimport" value="true" />
			<input type="hidden" name="ged" value="{$GEDFILENAME}" />
			<input type="hidden" name="GEDFILENAME" value="{$GEDFILENAME}" />
			<input type="hidden" name="exists" value="{$exists} />
			<input type="hidden" name="ok" value="{$ok}" />
			<input type="hidden" name="import" value="{$import}" />
			<input type="hidden" name="l_isansi" value=" {if isset($l_isansi)) $l_isansi" />
			<input type="hidden" name="check" value="" />
			{* NOTE: Additional import options *}
			<tr><td class="topbottombar $TEXT_DIRECTION" colspan="2">
				<a href="javascript: {tr}Verify GEDCOM{/tr}" onclick="expand_layer('verify_gedcom'); return false;"> 
					{if $startimport eq "true"} <img id="upload_gedcom_img" src="image/minus.gif" border="0" width="11" height="11" alt="" > 
					{else} <img id="upload_gedcom_img" src="image/plus.gif" border="0" width="11" height="11" alt="" >
					{/if}
				</a>
				<!-- print_help_link("verify_gedcom_help", "qm", "verify_gedcom"); -->
				&nbsp;<a href="javascript: {tr}Verify GEDCOM{/tr}" onclick="expand_layer('verify_gedcom');return false">{tr}Verify GEDCOM{/tr}</a>
				</td></tr>
				<tr><td class="optionbox" colspan="2">
				<div id="import_options" style="display: 
				{if $startimport eq "true" } block 
				{else} none {/if}">
				<table class="facts_table">
	
				{* NOTE: Time limit for import *}
				{* TODO: Write help text *}
				<tr><td class="descriptionbox width20 wrap">
				{formhelp note="{tr}<br /><br />The maximum time the import is allowed to run for processing the GEDCOM file."{/tr}"}
				{tr}Time limit{/tr}:
				</td><td class="optionbox"><input type="text" name="timelimit" value="".$timelimit."" size="5"
				{if $startimport eq "true"} disabled {/if} >
				</td></tr>
	
				{* NOTE: Import married names *}
				<tr><td class="descriptionbox width20 wrap">
				{formhelp note="{tr}Show married names on Individual list<br /><br />If you choose the option to import married names PhpGedView will look through all of the females in your GEDCOM file and automatically create a married name subrecord for them in their GEDCOM record.  This will allow you to search for these females by their married name or their maiden name.<br /><br />This option will also allow you to show married names in the individual list if you enable the <b>Show married names on Individual list</b> option in the GEDCOM configuration settings.<br /><br />{/tr}"}
				{tr}Import Married Names{/tr}:
				</td><td class="optionbox">
				{if $startimport eq "true" } $pgv_lang[$marr_names];
				{else}
				<select name="marr_names">\n
					<option value="YES" selected="selected">{tr}yes{/tr}</option>
					<option value="NO">{tr}no{/tr}</option>
				</select>
				{/if}
				</td></tr>
	
				{* NOTE: change XREF to RIN, REFN, or Don't change *}
				<tr><td class="descriptionbox wrap">
				{* print_help_link("change_indi2id_help", "qm", "change_id") *}
				{tr}Change Individual ID to:{/tr}
				</td><td class="optionbox">
				{if $startimport eq "true" }
					{if $xreftype == "NA"} {tr}Do not change{/tr}
					{else} {$xreftype}
					{/if}
				{else}
					<select name="xreftype">\n
					<option value="NA">{tr}Do not change{/tr}</option>\n<option value="RIN">RIN</option>\n
					<option value="REFN">REFN</option>\n</select>
				{/if}
				</td></tr>
	
				{* NOTE: option to convert to utf8 *}
				<tr><td class="descriptionbox wrap">
				{* print_help_link("convert_ansi2utf_help", "qm", "ansi_to_utf8") *}
				{tr}Convert this ANSI encoded GEDCOM to UTF-8?{/tr}
				</td><td class="optionbox">
				{if $startimport eq "true" } {tr}{$utf8convert}{/tr}
				{else}
				<select name="utf8convert">\n
					<option value="YES" selected="selected">{tr}yes{/tr}</option>
					<option value="NO">{tr}no{/tr}</option>
				</select>
				{/if}
				</td></tr>

				{* option to start addmedia tool
				/**
	 			 * Removed Addmedia tool link because of the new media centre
				 * Will leave in case we do need it later
					<tr><td class="descriptionbox wrap">
					print_help_link("inject_media_tool_help", "qm", "inject_media_tool");
				print $pgv_lang["inject_media_tool"];
				</td><td class="optionbox"><a href="addmedia.php?ged=$GEDCOM&action=injectmedia" target="media_win">".$pgv_lang["launch_media_tool"]."</a></td></tr>\n
				*}
			</table></div>
			</td></tr>";
		{/forminput}

		<div class="row submit">
			<input type="submit" name="continue" value="{tr}Upload GEDCOM{/tr}" />
		</div>
	{/form}
	{/strip}
	</div><!-- end .body -->

</div><!-- end .gedcom -->
	
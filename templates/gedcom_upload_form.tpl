{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}Step 1 of 4 - Upload GEDCOM{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
	{form legend="GEDCOM Upload" id="GEDCOM Upload"}
		{forminput}
			<input type="hidden" name="action" value="{$action}" />
			<input type="hidden" name="check" value="upload" />
			Select files from your local computer to upload to your server. All files will be uploaded to the storage directory
			<table class="facts_table center $text_dir">
				<tr><td class="topbottombar $text_dir" colspan="2">
				<a href="javascript: upload_gedcom" onclick="expand_layer('upload_gedcom'); return false;"> 
					{if $startimport != "true"} <img id="upload_gedcom_img" src="images/minus.gif" border="0" width="11" height="11" alt="" > 
					{else} <img id="upload_gedcom_img" src="images/plus.gif" border="0" width="11" height="11" alt="" >
					{/if}
				</a>
				<a href="javascript: upload_gedcom" onclick="expand_layer('upload_gedcom');return false">GEDCOM File:</a>"
				</td></tr>
				<tr><td class="optionbox wrap">		  
					{if $startimport != "true"} <div id="upload_gedcom" style="display:block " >
					{else} <div id="upload_gedcom" style="display:none " >
					{/if}
				</td></tr>	
				<tr>
					<td class="descriptionbox">GEDCOM File:</td>
					<td class="optionbox">
						{if isset($file) } {$file}
						{else}
							<input name="UPFILE" type="file" size="50" />
						{/if}
					</td>
				</tr>
				<tr>
					<td colspan="2">( max_upload_size {$filesize} )</td>
				</tr>
			</table>
		{/forminput}

		<div class="control-group submit">
			<input type="submit" name="continue" value="{tr}Upload GEDCOM{/tr}" />
		</div>
	{/form}
	{/strip}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

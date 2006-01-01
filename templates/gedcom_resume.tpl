{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}{$header}{/tr}: {tr}{$GEDCOM_FILE}{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
	{strip}
	{form legend="GEDCOM Upload" id="GEDCOM Upload"}
		{forminput}
			<input type="hidden" name="ged" value="{$ged}" />
			<input type="hidden" name="stage" value="1" />
			<input type="hidden" name="timelimit" value="{$timelimit}" />
			<input type="hidden" name="importtime" value="{$importtime}" />
			<input type="hidden" name="marr_names" value="{$marr_names}" />
			<input type="hidden" name="xreftype" value="{$xreftype}" />
			<input type="hidden" name="utf8convert" value="{$utf8convert}" />
			<input type="hidden" name="verify" value="{$verify}" />
			<input type="hidden" name="startimport" value="{$startimport}" />
			<input type="hidden" name="import" value="{$import}" />
			<input type="hidden" name="FILE" value="{$FILE}" />
			<input type="submit" name="continue" value="del_proceed" />
			<table>
				<tr><td class="descriptionbox">{tr}The execution time limit was reached.  Click the Continue button below to resume importing the GEDCOM file.{/tr}</td></tr>
				<tr><td class="topbottombar">
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

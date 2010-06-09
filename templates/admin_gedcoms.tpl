{* $Header$ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="admin gedcoms">
	<div class="header">
		<h1>{tr}Admin Gedcoms{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$gGedcom->mErrors}

		{form legend="Create a new Gedcom" enctype="multipart/form-data"}
			<input type="hidden" name="MAX_FILE_SIZE" value="16000000" />

			<div class="row">
				{formlabel label="Gedcom Title" for="gedcom_name"}
				{forminput}
					<input type="text" id="gedcom_name" name="gedcom_name" />
					{formhelp note="If a GEDCOM file with the same name already exists in PhpGedView, it will be overwritten. However, all GEDCOM settings made previously will be preserved."}
				{/forminput}
			</div> 

			<div class="row">
				{formlabel label="Upload Gedcom File" for="gedcom_source"}
				{forminput}
					<input type="text" id="gedcom_source" name="source" />
					{formhelp note="If the input GEDCOM file is not yet on your server, you have to get it there first, before you can start with Adding."}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="fSubmitAddGedcom" value="{tr}Add Gedcom{/tr}" />
			</div>
		{/form}

		<table class="data">
			<caption>{tr}List of Gedcoms{/tr}</caption>
			<tr>
				<th>{tr}Active{/tr}</th>
				<th>{tr}Title{/tr} [ {tr}File Name{/tr} ]</th>
				<th>{tr}Files{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>

			{section name=gedcom loop=$listgedcoms}
				<tr class="{cycle values="even,odd"}">
					<td style="text-align:center;">
						{if $listgedcoms[gedcom].enable eq 'n'}
							{smartlink ititle='activate' ibiticon="icons/face-sad" fActivateTopic=1 topic_id=`$topics[user].topic_id`}
						{else}
							{smartlink ititle='deactivate' ibiticon="icons/face-smile" fDeactivateTopic=1 topic_id=`$topics[user].topic_id`}
						{/if}
					</td>

					<td>
						<h2>
							<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}index.php?content_id={$listgedcoms[gedcom].content_id}">{$listgedcoms[gedcom].title}</a>
							&nbsp; <small>[ {$listgedcoms[gedcom].name} ]</small>
						</h2>
						<br />
						Containing - {$listgedcoms[gedcom].individuals} Individuals and {$listgedcoms[gedcom].families} Families
					</td>

					<td>
						<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}index.php?command=gedcom&ged={$listgedcoms[gedcom].name}">{$listgedcoms[gedcom].path}</a>
						<br />
						{$listgedcoms[gedcom].config}
						{smartlink ititle='edit' ibiticon="icons/accessories-text-editor" ifile='editconfig_gedcom.php' ged=`$listgedcoms[gedcom].name`}
						<br />
						{$listgedcoms[gedcom].privacy}
						{smartlink ititle='edit' ibiticon="icons/accessories-text-editor" ifile='edit_privacy.php' ged=`$listgedcoms[gedcom].name`}
					</td>

					<td align="right">
						<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}admin/admin_gedcoms.php?fUpload=1&amp;g_id={$listgedcoms[gedcom].g_id}">{biticon ipackage="icons" iname="network-receive" iforce=icon_text iexplain="Upload Gedcom"}</a>
						<br />
						{smartlink ititle='delete' ibiticon="icons/user-trash" ifile='admin/admin_gedcoms.php' fRemoveGedcom='1' g_id=`$listgedcoms[gedcom].g_id`}
						{smartlink ititle='permissions' ibiticon="icons/emblem-shared" ipackage='liberty' ifile='content_permissions.php' content_id=`$listgedcoms[gedcom].content_id`}
						<br />
						<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}downloadgedcom.php?ged={$listgedcoms[gedcom].name}">{biticon ipackage="icons" iname="network-transmit" iforce=icon_text iexplain="Download Gedcom"}</a>
					</td>
				</tr>
			{sectionelse}
				<tr class="norecords">
					<td colspan="4">{tr}No records found{/tr}</td>
				</tr>
			{/section}
		</table>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}

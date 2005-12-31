{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/list_gedcom.tpl,v 1.3 2005/12/31 17:32:43 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin wiki">
	<div class="header">
		<h1>{if $pagetitle ne ''}{$pagetitle}{else}{tr}GEDCOM Archive{/tr}{/if}</h1>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
		{strip}
			<div class="navbar">
				<ul>
					<li>{biticon ipackage=liberty iname=sort iexplain="sort by"}</li>
					<li>{smartlink ititle="Gedcom Name" isort="title" offset=$offset}</li> 
					<li>{smartlink ititle="Last Modified" iorder="desc" idefault=1 isort="last_modified" offset=$offset}</li> 
					<li>{smartlink ititle="Author" isort="creator_user" offset=$offset}</li> 
					<li>{smartlink ititle="Last Editor" isort="modifier_user" offset=$offset}</li> 
				</ul>
			</div>

			<input type="hidden" name="offset" value="{$offset}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode}" />

			<table class="clear data">
				<caption>{tr}GEDCOM Archive List{/tr} <span class="total">[ {$pagecount} ]</span></caption>
				<tr>
					{*  at the moment, the only working option to use the checkboxes for is deleting pages. so for now the checkboxes are visible iff $bit_p_remove is set. Other applications make sense as well (categorize, convert to pdf, etc). Add necessary corresponding permission here: *}

					{if $gBitUser->hasPermission( 'bit_p_remove' )}              {* ... "or $gBitUser->hasPermission( 'bit_p_other_sufficient_condition_for_checkboxes' )"  *}
					  {assign var='checkboxes_on' value='y'}
					{else}
					  {assign var='checkboxes_on' value='n'}
					{/if}
					{if $gBitSystem->isFeatureActive( 'wiki_list_lastver' )}
						<th>{smartlink ititle="Last Version" isort="version" offset=$offset}</th> 
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitSystem->isFeatureActive( 'wiki_list_versions' )}
						<th>{smartlink ititle="Version" isort="versions" offset=$offset}</th> 
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitSystem->isFeatureActive( 'wiki_list_format_guid' )}
						<th>{smartlink ititle="GUID" isort="format_guid" offset=$offset}</th> 
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitSystem->isFeatureActive( 'wiki_list_size' )}
						<th>{smartlink ititle="Size" isort="size" offset=$offset}</th> 
						{counter name=cols assign=cols print=false}
					{/if}
					{if $gBitUser->hasPermission( 'bit_p_edit' )}
						<th>{tr}Actions{/tr}</th>
						{counter name=cols assign=cols print=false}
					{/if}
				</tr>

				{cycle values="even,odd" print=false}
				{section name=changes loop=$listgedcom}
					<tr class="{cycle advance=false}">
						<td colspan="{$cols}">
							{if $gBitSystem->isFeatureActive( 'wiki_list_name' )}
								<h3><a href="{$listgedcom[changes].display_url}" title="{$listgedcom[changes].description}">{$listgedcom[changes].title}</a></h3>
							{else}
								<a href="{$smarty.const.WIKI_PKG_URL}index.php?page_id={$listgedcom[changes].page_id}" title="{$listgedcom[changes].page_id}">Page #{$listgedcom[changes].page_id}</a>
							{/if}
							{if $gBitSystem->isFeatureActive( 'wiki_list_creator' )}
								{tr}Created by{/tr} {displayname real_name=$listgedcom[changes].creator_real_name user=$listgedcom[changes].creator_user}
							{/if}
							, {$listgedcom[changes].created|bit_short_datetime}
							{if $gBitSystem->isFeatureActive( 'wiki_list_lastmodif' ) && ($listgedcom[changes].version > 1)}
								<br />
								{tr}Last modified{/tr}
								{if $listgedcom[changes].editor != $listgedcom[changes].creator}
									&nbsp;{tr}by{/tr} {displayname real_name=$listgedcom[changes].modifier_real_name user=$listgedcom[changes].modifier_user}
								{/if}
								, {$listgedcom[changes].last_modified|bit_short_datetime}
							{/if}
						</td>
					</tr>
					<tr class="{cycle}">
						{if $gBitSystem->isFeatureActive( 'wiki_list_lastver' )}
							<td style="text-align:center;">{$listgedcom[changes].version}</td>
						{/if}
						{if $gBitSystem->isFeatureActive( 'wiki_list_versions' )}
							{if $gBitSystem->isFeatureActive( 'feature_history' )}
								<td style="text-align:center;">{smartlink ititle=$listgedcom[changes].version ifile='page_history.php' page_id=$listgedcom[changes].page_id}</td>
							{else}
								<td style="text-align:center;">{$listgedcom[changes].version}</td>
							{/if}
						{/if}
						{if $gBitSystem->isFeatureActive( 'wiki_list_format_guid' )}
							<td>{$listgedcom[changes].format_guid}</td>
						{/if}
						{if $gBitSystem->isFeatureActive( 'wiki_list_size' )}
							<td style="text-align:right;">{$listgedcom[changes].len|kbsize}</td>
						{/if}
						{if $gBitUser->hasPermission( 'bit_p_edit' )}
							<td class="actionicon">
								<a href="{$smarty.const.WIKI_PKG_URL}edit.php?page_id={$listgedcom[changes].page_id}">{biticon ipackage="liberty" iname="edit" iexplain="edit"}</a>
								{if $checkboxes_on eq 'y'}
									<input type="checkbox" name="checked[]" value="{$listgedcom[changes].page_id}" />
								{/if}
							</td>
						{/if}
					</tr>
				{sectionelse}
					<tr class="norecords"><td colspan="{$cols}">
						{tr}No records found{/tr}
					</td></tr>
				{/section}
			</table>
		{/strip}

			{if $checkboxes_on eq 'y'}
				<div style="text-align:right;">
					<script type="text/javascript">//<![CDATA[
						// check / uncheck all.
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'checked[]','switcher')\" />");
					//]]></script>

					<br />

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						{if $gBitUser->hasPermission( 'bit_p_remove' )}
							<option value="remove_pages">{tr}remove{/tr}</option>
						{/if}
					</select>

					<script type="text/javascript">//<![CDATA[
					// Fake js to allow the use of the <noscript> tag (so non-js-users kenn still submit)
					//]]></script>

					<noscript>
						<div><input type="submit" value="{tr}Submit{/tr}" /></div>
					</noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .wiki -->

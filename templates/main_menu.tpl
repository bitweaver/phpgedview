{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/main_menu.tpl,v 1.5 2007/06/02 13:24:07 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="admin gedcom">
	<div class="header">
		<h1>{$pagetitle}</h1>
		{include file="bitpackage:phpgedview/top_bar.tpl"}
		<div class="data">
			<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}clippings.php">Family Tree Clippings Cart</a>
		</div>
	</div>

	{formfeedback error=$errors}

	<div class="body">
		<table class="data">
			<tr>
				<th>{tr}Currently available GEDCOM Archives{/tr}</th>
			</tr>
			{section name=gedcom loop=$listgedcoms}
				<tr class="{cycle values="even,odd"}">
					<td style="text-align:center;">
						<h2>
							<a href="{$smarty.const.PHPGEDVIEW_PKG_URL}index.php?pid=I1&content_id={$listgedcoms[gedcom].content_id}#content">{$listgedcoms[gedcom].title}</a>
							&nbsp; <small>[ {$listgedcoms[gedcom].name} ]</small>
						</h2>
						Containing - {$listgedcoms[gedcom].individuals} Individuals and {$listgedcoms[gedcom].families} Families
					</td>
				</tr>	
			{sectionelse}
				<tr class="norecords">
					<td colspan="4">{tr}No records found{/tr}</td>
				</tr>
			{/section}
		</table>

	</div><!-- end .body -->

</div><!-- end .gedcom -->

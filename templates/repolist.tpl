{* $Header$ *}
<div class="floaticon">{bithelp}</div>

<div class="gedcom_list">
	<div class="header">
		<h1>{$pagetitle}</h1>
		{* <img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border="0" title="Individuals" alt="Individuals" />&nbsp;&nbsp; *}
		{include file="bitpackage:phpgedview/top_bar.tpl"}
	</div>

	{formfeedback error=$errors}

	<div class="body">
		{if isset($repolist) }
			{include file="bitpackage:phpgedview/list_surnames.tpl"}
		{if isset($addrepolist) }
			{include file="bitpackage:phpgedview/list_names.tpl"}
		{/if}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

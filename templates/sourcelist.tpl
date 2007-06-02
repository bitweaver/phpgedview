{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/sourcelist.tpl,v 1.1 2007/06/02 14:15:44 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="gedcom_list">
	<div class="header">
		<h1>{$pagetitle}</h1>
		{* <img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border="0" title="Individuals" alt="Individuals" />&nbsp;&nbsp; *}
		{include file="bitpackage:phpgedview/top_bar.tpl"}
	</div>

	{formfeedback error=$errors}

	<div class="body">
		{if isset($sourcelist) }
			{include file="bitpackage:phpgedview/list_sources.tpl"}
		{/if}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

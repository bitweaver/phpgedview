{* $Header: /cvsroot/bitweaver/_bit_phpgedview/templates/indilist.tpl,v 1.1 2007/06/02 12:32:56 lsces Exp $ *}
<div class="floaticon">{bithelp}</div>

<div class="gedcom_list">
	<div class="header">
		<h1>{$pagetitle}</h1>
		{* <img src=\"".$PGV_IMAGE_DIR."/".$PGV_IMAGES["indis"]["small"]."\" border="0" title="Individuals" alt="Individuals" />&nbsp;&nbsp; *}
	</div>

	{formfeedback error=$errors}

	<div class="body">
		{if isset($indialpha) }
			{include file="bitpackage:phpgedview/list_letters.tpl"}
		{/if}
		{if isset($SEARCH_SPIDER) }
			{if $alpha ne '@' }
				{if $surname_sublist eq 'yes' }
					{* print_help_link("skip_sublist_help", "qm", "skip_surnames") *}
					<a href="?alpha={$alpha}&amp;surname_sublist=no&amp;show_all={$show_all}">{tr}Skip Surname lists{/tr}</a>";
				{else}
					{* print_help_link("skip_sublist_help", "qm", "show_surnames") *}
					<a href="?alpha={$alpha}&amp;surname_sublist=yes&amp;show_all={$show_all}">{tr}Show Surname lists{/tr}</a>";
				{/if}
			{/if}
		{/if}
		{if isset($surnames) }
			{include file="bitpackage:phpgedview/list_surnames.tpl"}
		{elseif isset($names) }
			{include file="bitpackage:phpgedview/list_names.tpl"}
		{/if}
	</div><!-- end .body -->

</div><!-- end .gedcom -->

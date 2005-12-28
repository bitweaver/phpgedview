{strip}
<ul>
	<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}index.php">{tr}List Genealogy{/tr}</a></li>
	{if $gBitUser->hasPermission( 'tiki_p_admin_phpgedview' )}
		<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}admin/index.php">{tr}Admin Genealogy{/tr}</a></li>
	{/if}
</ul>
{/strip}

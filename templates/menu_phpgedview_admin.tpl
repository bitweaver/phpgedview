{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=phpgedview">{tr}phpgedview{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}admin.php">{tr}legacy Management{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}editconfig.php">{tr}legacy Configuration{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}editgedcoms.php">{tr}gedcom Management{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}edit_merge.php">{tr}Merge records{/tr}</a></li>
</ul>
{/strip}

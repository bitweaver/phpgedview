{strip}
<li class="dropdown-submenu">
    <a href="#" onclick="return(false);" tabindex="-1" class="sub-menu-root">{tr}{$smarty.const.PHPGEDVIEW_PKG_DIR|capitalize}{/tr}</a>
	<ul class="dropdown-menu sub-menu">
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=phpgedview">{tr}phpgedview Settings{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}admin.php">{tr}legacy Management{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}editconfig.php">{tr}legacy Configuration{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}editgedcoms.php">{tr}gedcom Management{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.PHPGEDVIEW_PKG_URL}edit_merge.php">{tr}Merge records{/tr}</a></li>
	</ul>
</li>
{/strip}

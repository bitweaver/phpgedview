<?php global $SEARCH_SPIDER; ?>
<script type="text/javascript">

function switchMenu(openMe,closeMe) 
    {
	    var openIt = document.getElementById(openMe);
	    var closeIt = document.getElementById(closeMe);
	    closeIt.style.display = 'none';
	    openIt.style.display = '';
		SetCookie("menu",document.getElementById(openMe).id.toString(),7);
		window.location = '<?php print $SCRIPT_NAME."?".$QUERY_STRING;?>';
	}
function SetCookie(cookieName,cookieValue,nDays) 
	{
 var today = new Date();
 var expire = new Date();
 if (nDays==null || nDays==0) nDays=1;
 expire.setTime(today.getTime() + 3600000*24*nDays);
 document.cookie = cookieName+"="+escape(cookieValue)
                 + ";expires="+expire.toGMTString();
	}


</script>
<div id="header" class="<?php print $TEXT_DIRECTION; ?>">
<table width="99%">
	<tr>
		<td><img src="<?php print $THEME_DIR;?>header.jpg" width="281" height="50" alt="" /></td>
		<td>
			<table width="100%">
			<tr>
				<td align="center" valign="top">
					<b>
					<?php print_user_links(); ?>
					<br />
					<a href="<?php print $HOME_SITE_URL; ?>"><?php print $HOME_SITE_TEXT; ?></a>
					</b>
				</td>
				<?php if(empty($SEARCH_SPIDER)) { ?>
				<td align="<?php print $TEXT_DIRECTION=="rtl"?"left":"right" ?>" valign="middle" >
					<?php print_lang_form(); ?>
					<?php print_theme_dropdown(); ?>
				</td>
				<?php } ?>
                    <td align="<?php print $TEXT_DIRECTION=="rtl"?"left":"right" ?>" valign="middle" >
				<?php if(empty($SEARCH_SPIDER)) { ?>
					<form action="search.php" method="get">
						<input type="hidden" name="action" value="general" />
						<input type="hidden" name="topsearch" value="yes" />
						<input type="text" name="query" accesskey="<?php print $pgv_lang["accesskey_search"]?>" size="12" value="<?php print $pgv_lang['search']?>" onfocus="if (this.value == '<?php print $pgv_lang['search']?>') this.value=''; focusHandler();" onblur="if (this.value == '') this.value='<?php print $pgv_lang['search']?>';" />
						<input type="submit" name="search" value="&gt;" />
					</form>
				<?php } ?>
					<?php print_favorite_selector(); ?>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
<table width="99%">
	<tr>
		<td width="75%">
			<div class="title" style="<?php print $TEXT_DIRECTION=="rtl"?"left":"right" ?>">
				<?php print_gedcom_title_link(TRUE); ?>
			</div>
		</td>
	</tr>
</table>

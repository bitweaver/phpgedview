{if $smarty.const.ACTIVE_PACKAGE == 'phpgedview'}
	<link rel="stylesheet" title="{$style}" type="text/css" href="{$smarty.const.PHPGEDVIEW_PKG_URL}styles/phpgedview.css" media="all" />
	<script language="JavaScript" type="text/javascript">
		plusminus = new Array();
		plusminus[0] = new Image();
		plusminus[0].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/plus.gif";
		plusminus[1] = new Image();
		plusminus[1].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/minus.gif";
		zoominout = new Array();
		zoominout[0] = new Image();
		zoominout[0].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/zoomin.gif";
		zoominout[1] = new Image();
		zoominout[1].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/zoomout.gif";
		arrows = new Array();
		arrows[0] = new Image();
		arrows[0].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/larrow2.gif";
		arrows[1] = new Image();
		arrows[1].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/rarrow2.gif";
		arrows[2] = new Image();
		arrows[2].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/uarrow2.gif";
		arrows[3] = new Image();
		arrows[3].src = "{$smarty.const.PHPGEDVIEW_PKG_URL}images/darrow2.gif";
		{literal}
		function delete_record(pid, linenum, mediaid) {
			if (!mediaid) mediaid="";
	 		if (confirm('Are you sure you want to delete this GEDCOM fact?')) {
				window.open('edit_interface.php?action=delete&pid='+pid+'&linenum='+linenum+'&mediaid='+mediaid+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
			}
			return false;
		}
		function deleteperson(pid) {
			if (confirm('Are you sure you want to delete this person from the GEDCOM file?')) {
				window.open('edit_interface.php?action=deleteperson&pid='+pid+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
			}
		return false;
		}
		function deleterepository(pid) {
			if (confirm('Are you sure you want to delete this Repository from the database?')) {
				window.open('edit_interface.php?action=deleterepo&pid='+pid+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
			}
			return false;
		}
		{/literal}
		//-->
	</script>
	<script src="{$smarty.const.PHPGEDVIEW_PKG_URL}/js/phpgedview.js" language="JavaScript" type="text/javascript"></script>
{/if}

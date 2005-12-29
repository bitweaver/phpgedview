<?php
/**
 *  View Connections Page
 *
 *  Allows a user the ability to check who they have linked to another server.
 *  Allows a user the ability to see the local information and the remote information about that linked person.
 *  Allows a user the ability to remove the link.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package PhpGedView
 * @subpackage Charts
 * @version $Id: viewconnections.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */
require('config.php');
require($PGV_BASE_DIRECTORY.$factsfile["english"]);
if (file_exists($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE])) require($PGV_BASE_DIRECTORY.$factsfile[$LANGUAGE]);
require("includes/functions_edit.php");

print_simple_header('View Connections');

//-- only allow gedcom admins here
if (!userGedcomAdmin(getUserName())) {
	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!userGedcomAdmin(getUserName())) print "<br />".$pgv_lang["user_cannot_edit"];
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".$pgv_lang["close_window"]."\" onclick=\"window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}

$server = "";
$Links="";
$famLinks="";
if (!empty($_REQUEST["selectedServer"])){
  $serverID = $_REQUEST["selectedServer"];
  //$server_split = explode(" - ", $server_gedcomid);
  //$server = $server_split[0];
  //$serverID = server_exists($server, $server_split[1]);
  $Links = search_indis("1 RFN ".$serverID.":");
  $famLinks = search_fams("1 RFN ".$serverID.":");
  }
?>
<script language="javascript">
    function deleteLink(){
         var select2 = document.getElementById('select2');
            var deleteIndex = select2.selectedIndex;
        if(deleteIndex>-1){
         select2[deleteIndex] = null;
         }else{alert('<?php print $pgv_lang["error_delete_person"];?>');}
    }

    function viewLocalInformation(){
        var select2=document.getElementById('select2');
        var viewIndex = select2.selectedIndex;
      if(viewIndex>-1){
        alert('Show Person');
      }else{alert('<?php print $pgv_lang["error_view_info"];?>');}
    }

    function viewRemoteInformation(){
          var select2=document.getElementById('select2');
        var viewIndex = select2.selectedIndex;
      if(viewIndex>-1){
        alert('Show Person');
      }else{alert('<?php print $pgv_lang["error_view_info"];?>');}
    }
</script>

<table width="450px">
<tr><td class="title"><?php echo $pgv_lang["title_view_conns"];?></td></tr>
<tr>
<td style="text-align: left;" class="facts_label">
<?php echo $pgv_lang["label_server_info"]."&nbsp;&nbsp;&nbsp;".$server;?>
</td>
</tr>
<tr>
<td class="facts_label"><u><?php  print $pgv_lang["label_individuals"];?></u></td>
</tr>
<tr>
<td class="facts_value">
<ul>
<?php
foreach($Links as $pid=>$indi){
  print_list_person($pid, array($indi["names"][0][0], $GEDCOM));
}
?>
</ul>
</td>
</tr>
<tr>
<td class="facts_label"><u><?php print $pgv_lang["label_families"];?></u></td>
</tr>
<tr>
<td class="facts_value">
<ul>
<?php
foreach($famLinks as $famPid=>$fam){
  $fullname = check_NN($fam["name"]);
  print_list_family($famPid, array($fullname, $GEDCOM));
}
?>
</ul>
</td>
</tr>
<!--
<tr><td height="20">
<label class="link" id="lblLocalView" onmouseover="lblLocalView.style.cursor='hand';" onclick="viewLocalInformation()">
<u><?php echo $pgv_lang["label_view_local"];?></u>
</label>
</td></tr>
<tr><td height="20">
<label class="link" id="lblRemoteView" onmouseover="lblRemoteView.style.cursor='hand';" onclick="viewRemoteInformation()">
<u><?php echo $pgv_lang["label_view_remote"];?></u>
</label>
</td></tr>
<tr><td height="20">
<input type="button" height="20" value="<?php echo $pgv_lang['label_delete'];?>" onclick="deleteLink()"/>
</td></tr>
-->
</table>
<?php
print_simple_footer();
?>

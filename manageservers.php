<?php
/**
 *  Manage Servers Page
 *
 *  Allow a user the ability to manage servers i.e. allowing, banning, deleting
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
 * @subpackage Admin
 * @version $Id: manageservers.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 * @author rbennett
 */

require("config.php");
require_once("includes/functions.php");
require($factsfile["english"]);
if (file_exists($factsfile[$LANGUAGE])) require($factsfile[$LANGUAGE]);
require_once("includes/functions_edit.php");
require_once("includes/functions_import.php");
require_once("includes/serviceclient_class.php");

/**
 * Adds an ip address to the banned.php file
 * @param varchar(30) $ip	The ip to be saved
 */
function add_banned_ip($ip) {
	global $banned, $INDEX_DIRECTORY, $pgv_lang;
	if (file_exists($INDEX_DIRECTORY."banned.php"))
	{
	include_once($INDEX_DIRECTORY."banned.php");
	}
	$bannedtext = "<?php\n//--List of banned IP addresses\n";
	$bannedtext .= "\$banned = array();\n";	
	if(isset($banned)){
	reset($banned);
		foreach ($banned as $value)
		{
			$bannedtext .= "\$banned[] = \"".$value."\";\n";
		}
	}
	$bannedtext .= "\$banned[] = \"".$ip."\";\n";
	$bannedtext .= "\n"."?>";

	$fp = fopen($INDEX_DIRECTORY."banned.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $bannedtext);
		fclose($fp);
		$logline = AddToLog("banned.php updated by >".getUserName()."<");
 		if (!empty($COMMIT_COMMAND)) check_in($logline, "banned.php", $INDEX_DIRECTORY);	
	}
}

/**
 * Adds an ip address to the search_engines.php file
 * @param varchar(30) $ip	The ip to be saved
 */
function add_search_engine_ip($ip) {
	global $search_engines, $INDEX_DIRECTORY, $pgv_lang;
	if (file_exists($INDEX_DIRECTORY."search_engines.php"))
	{
	include_once($INDEX_DIRECTORY."search_engines.php");
	}
	$searchtext = "<?php\n//--List of search engine IP addresses\n";
	$searchtext .= "\$search_engines = array();\n";	
	if(isset($search_engines)){
	reset($search_engines);
		foreach ($search_engines as $value)
		{
			$searchtext .= "\$search_engines[] = \"".$value."\";\n";
		}
	}
	$searchtext .= "\$search_engines[] = \"".$ip."\";\n";
	$searchtext .= "\n"."?>";

	$fp = fopen($INDEX_DIRECTORY."search_engines.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $searchtext);
		fclose($fp);
		$logline = AddToLog("search_engines.php updated by >".getUserName()."<");
 		if (!empty($COMMIT_COMMAND)) check_in($logline, "search_engines.php", $INDEX_DIRECTORY);	
	}
}

/**
 * Removes an IP address from the banned.php list
 * 
 * @param varchar(30) $ip	IP address to remove
 */
function delete_banned_ip($ip) {
	global $banned, $INDEX_DIRECTORY, $pgv_lang;
	if (file_exists($INDEX_DIRECTORY."banned.php"))
	{
	include_once($INDEX_DIRECTORY."banned.php");
	}
	
	$bannedtext = "<?php\n//--List of banned IP addresses\n";
	$bannedtext .= "\$banned = array();\n";	
	foreach ($banned as $value)
	{
		if ($value != $ip)
		{
			$bannedtext .= "\$banned[] = \"".$value."\";\n";
		}
	}
	
	$bannedtext .= "\n"."?>";

	$fp = fopen($INDEX_DIRECTORY."banned.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $bannedtext);
		fclose($fp);
		$logline = AddToLog("banned.php updated by >".getUserName()."<");
 		if (!empty($COMMIT_COMMAND)) check_in($logline, "banned.php", $INDEX_DIRECTORY);	
	}
}

/**
 * Removes an IP address from the search_engines.php list
 * 
 * @param varchar(30) $ip	IP address to remove
 */
function delete_search_engine_ip($ip) {
	global $search_engines, $INDEX_DIRECTORY, $pgv_lang;
	if (file_exists($INDEX_DIRECTORY."search_engines.php"))
	{
	include_once($INDEX_DIRECTORY."search_engines.php");
	}
	
	$searchtext = "<?php\n//--List of search engine IP addresses\n";
	$searchtext .= "\$search_engines = array();\n";	
	foreach ($search_engines as $value)
	{
		if ($value != $ip)
		{
			$searchtext .= "\$search_engines[] = \"".$value."\";\n";
		}
	}
	
	$searchtext .= "\n"."?>";

	$fp = fopen($INDEX_DIRECTORY."search_engines.php", "wb");
	if (!$fp) {
		print "<span class=\"error\">".$pgv_lang["gedcom_config_write_error"]."<br /></span>\n";
	}
	else {
		fwrite($fp, $searchtext);
		fclose($fp);
		$logline = AddToLog("search_engines.php updated by >".getUserName()."<");
 		if (!empty($COMMIT_COMMAND)) check_in($logline, "search_engines.php", $INDEX_DIRECTORY);	
	}
}


 // print_simple_header("Manage Servers");
print_header($pgv_lang["administration"]);
//-- only allow gedcom admins here
if (!userGedcomAdmin(getUserName())) {
	print $pgv_lang["access_denied"];
	//-- display messages as to why the editing access was denied
	if (!userGedcomAdmin(getUserName())) print "<br />".$pgv_lang["user_cannot_edit"];
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".$pgv_lang["close_window"]."\" onclick=\"window.close();\">".$pgv_lang["close_window"]."</a></div>\n";
	print_simple_footer();
	exit;
}
  
if(isset($_REQUEST["action"])){
	if($_REQUEST["action"]==$pgv_lang["label_add_server"]){
		$serverTitle = $_POST["txtTitle"];
		$serverURL = $_POST["txtNewURL"];
		$username = $_POST["txtUsername"];
		$password = $_POST["txtPassword"];
		$gedcom_id = $_POST["txtGID"];

		if (!$serverTitle=="" || !$serverURL=="")	{
		$gedcom_string = "0 @new@ SOUR\r\n";
		$gedcom_string.= "1 URL ".$serverURL."\r\n";
	    $gedcom_string.= "1 TITL ".$serverTitle."\r\n";
		$gedcom_string.= "1 _DBID ".$gedcom_id."\r\n";
		$gedcom_string.= "2 _USER ".$username."\r\n";
		$gedcom_string.= "2 _PASS ".$password."\r\n";
	  
		$service = new ServiceClient($gedcom_string);
		$sid = $service->authenticate();
		if (PEAR::isError($sid) || empty($sid)) {
			print "<span class=\"error\">".$pgv_lang["error_siteauth_failed"]."</span>";
		}
		if (!empty($sid)) {
			$serverID = append_gedrec($gedcom_string);
			accept_changes($serverID."_".$GEDCOM);
		}
		else print "<span class=\"error\">".$pgv_lang["error_siteauth_failed"]."</span>";
		}else{
			print "<span class=\"error\">".$pgv_lang["error_url_blank"]."</span>";
		}
    }else if($_REQUEST["action"]==$pgv_lang["label_ban_server"]){
       $serverID = $_POST["txtAddIp"];
	   //Validates IP address to make sure it is a number or *. 
		if (preg_match('/^\d{0,3}\.(\d{0,3}|\*)\.(\d{0,3}|\*)\.(\d{0,3}|\*)/', $serverID) && !ip_exists($serverID)) {
			add_banned_ip($serverID);
		} 
		else {
			print("<span class=\"error\">".$pgv_lang["error_ban_server"]."</span>");
		}
    }else if($_REQUEST["action"]==$pgv_lang["label_add_search_server"]){
       $serverID = $_POST["searchtxtAddIp"];
	   //Validates IP address to make sure it is a number or *. 
		if (preg_match('/^\d{0,3}\.(\d{0,3}|\*)\.(\d{0,3}|\*)\.(\d{0,3}|\*)/', $serverID) && !ip_exists($serverID)) {
			add_search_engine_ip($serverID);
		} 
		else {
			print("<span class=\"error\">".$pgv_lang["error_ban_server"]."</span>");
		}
    }else if($_REQUEST["action"]==$pgv_lang["remove_ip"]){
		if (isset($_REQUEST["serverID"]))
		{
			$serverID = $_REQUEST["serverID"];
			delete_search_engine_ip($serverID);
		}
    }else if($_REQUEST["action"]==$pgv_lang["remove"]){
		if (isset($_REQUEST["serverID"]))
		{
			$serverID = $_REQUEST["serverID"];
			delete_banned_ip($serverID);
		}
		else if(isset($_REQUEST["dbID"]))
		{
			if(delete_gedrec($_REQUEST["dbID"]))
			{
			accept_changes($_REQUEST["dbID"]."_".$GEDCOM);
			}
		}
	}
}
  //Requires banned.php down here so page refreshes correctly
  $bannedexists = false;
if (file_exists($INDEX_DIRECTORY."banned.php"))	{
	$bannedexists = true;
	require($INDEX_DIRECTORY."banned.php");
}
  //Requires search_engines.php down here so page refreshes correctly
  $searchexists = false;
if (file_exists($INDEX_DIRECTORY."search_engines.php"))	{
	$searchexists = true;
	require($INDEX_DIRECTORY."search_engines.php");
}

function ip_exists($ip)	{
	global $INDEX_DIRECTORY, $banned;
	if (file_exists($INDEX_DIRECTORY."banned.php")){
		include($INDEX_DIRECTORY."banned.php");
		if(isset($banned))
		{
		reset($banned);
		foreach($banned as $value) {
				if ($ip==$value){
					return true;
				}
			}
		}
	}
	return false;
}

function search_ip_exists($ip)	{
	global $INDEX_DIRECTORY, $search_engines;
	if (file_exists($INDEX_DIRECTORY."search_engines.php")){
		include($INDEX_DIRECTORY."search_engines.php");
		if(isset($search_engines))
		{
		reset($search_engines);
		foreach($search_engines as $value) {
				if ($ip==$value){
					return true;
				}
			}
		}
	}
	return false;
}

?>
<script language="javascript" type="text/javascript">
function validateIP(str){
	ipRegEx = /^(\d{1,3})\.(\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)$/;
	if (str.match(ipRegEx)){
		return true;
	}
	else	{
		alert('<?php print $pgv_lang["error_ban_server"];?>');
		return false;
	}
}

function validateDB(){
	if (document.getElementById('txtTitle').value == "" || document.getElementById('txtNewURL').value == ""){
		alert('<?php print $pgv_lang["error_url_blank"];?>');
		return false;
	}
	else	{
		return true;
	}
}
	
</script>
<table align="center">
<tr>
<td height="2" colspan="2" class="title" align="center">
<?php echo $pgv_lang["title_manage_servers"];?>
</td>
</tr>
<tr>
<td>
<form name="searchegineform" action="manageservers.php" method="post" onsubmit="return validateIP(document.getElementById('searchtxtAddIp').value);">
	<!-- Search Engine IP address table --> 
	    <table width="230px" align="center">
	        <tr>
	            <td class="facts_label"><?php print_help_link("help_manual_search_engines", "qm"); ?><u><?php echo $pgv_lang["label_manual_search_engines"];?></u></td>
	        </tr>
			<tr>
        	<td class="facts_value" height="160">
        		<table align="center">
	            <?php
				if($searchexists)
				{
	            foreach($search_engines as $value){ ?>
	              	<tr>
	               		<td><?php echo $value; ?></td>
	               		<td><a href="<?php echo "manageservers.php?action=".$pgv_lang["remove_ip"]."&amp;serverID=".urlencode($value);?>"><?php echo $pgv_lang["remove_ip"];?></a></td>
                	</tr>
	            <?php }	}?>
             	   <tr>
				   <td colspan="2"><br />
			<?php echo $pgv_lang["label_remove_search"];?></td>
					</tr>
			<tr>	
				<td><input type="text" id="searchtxtAddIp" name="searchtxtAddIp" size="14" />
				<input type="hidden" />
				</td>
				<td>
				<input type="submit" value="<?php echo $pgv_lang['label_add_search_server'];?>" id="searchbtnAddIp" />
				<input name="action" type="hidden" value="<?php echo $pgv_lang['label_add_search_server'];?>"/>
	            </td>
	        </tr>
</table>
</td>
</tr>
</table>
</form>

<table align="center">
<tr>
<td height="2" colspan="2" class="title" align="center">
<?php echo $pgv_lang["title_manage_servers"];?>
</td>
</tr>
<tr>
<td>
<form name="banserversform" action="manageservers.php" method="post" onsubmit="return validateIP(document.getElementById('txtAddIp').value);">
	<!-- Banned IP address table --> 
	    <table width="230px" align="center">
	        <tr>
	            <td class="facts_label"><?php print_help_link("help_banning", "qm"); ?><u><?php echo $pgv_lang["label_banned_servers"];?></u></td>
	        </tr>
			<tr>
        	<td class="facts_value" height="160">
        		<table align="center">
	            <?php
				if($bannedexists)
				{
	            foreach($banned as $value){ ?>
	              	<tr>
	               		<td><?php echo $value; ?></td>
	               		<td><a href="<?php echo "manageservers.php?action=".$pgv_lang["remove"]."&amp;serverID=".urlencode($value);?>"><?php echo $pgv_lang["remove"];?></a></td>
                	</tr>
	            <?php }	}?>
             	   <tr>
				   <td colspan="2"><br />
			<?php echo $pgv_lang["label_remove_ip"];?></td>
					</tr>
			<tr>	
				<td><input type="text" id="txtAddIp" name="txtAddIp" size="14" />
				<input type="hidden" />
				</td>
				<td>
				<input type="submit" value="<?php echo $pgv_lang['label_ban_server'];?>" id="btnAddIp" />
				<input name="action" type="hidden" value="<?php echo $pgv_lang['label_ban_server'];?>"/>
	            </td>
	        </tr>
</table>
</td>
</tr>
</table>
</form>


	<!-- Allowed servers table -->
	    <table align="center" width="430px">
	        <tr>
	            <td class="facts_label"><u><?php echo $pgv_lang["label_added_servers"];?></u></td>
	        </tr>
			<?php
			$remoteServer = get_server_list();
			foreach($remoteServer as $sid=>$value){ ?>
	        <tr>
			<td class="facts_value">
				<table align="center" width="100%">
					<tr>
						<td width="80%"><a href=<?php echo "\"viewconnections.php?selectedServer=".$sid."\"";?>><?php echo $value["name"]; ?></a></td>
						<td align="right"><a href="<?php echo "manageservers.php?action=".$pgv_lang["remove"]."&amp;dbID=".$sid;?>"><?php echo $pgv_lang["remove"];?></a></td>
					</tr>
				</table>
	        </td>
	        </tr>
				<?php }?>
	    </table>
<form name="addserversform" action="manageservers.php" method="post" onsubmit="return validateDB();">
<table align="center">
	<tr>
    <td valign="top">
	<!-- Add remote server table -->
    <table width="500px">
        <tr>
            <td class="facts_label" colspan="2"><?php print_help_link("help_remotesites", "qm"); ?><u><?php print $pgv_lang["label_new_server"];?></u></td>
        </tr>
        <tr>
            <td class="facts_label">
            <label><?php print $pgv_lang["title"];?></label>
            </td>
            <td class="facts_value">
            <input type="text" size="50" id="txtTitle" name="txtTitle"/>
            </td>
        </tr>
		 <tr>
            <td class="facts_label"><?php print_help_link('link_remote_site_help', 'qm');?>
            <label><?php print $pgv_lang["label_server_url"];?></label>
            </td>
            <td class="facts_value">
            <input type="text" size="50" id="txtNewURL" name="txtNewURL"/>
            </td>
        </tr>
        <tr>
            <td class="facts_label"><?php echo $pgv_lang["label_gedcom_id2"];?></td>
            <td class="facts_value"><input type="text" id="txtGID" name="txtGID" size="14"/></td>
        </tr>
        <tr>
            <td class="facts_label">
            <label><?php print $pgv_lang["label_username_id"];?></label>
            </td>
            <td class="facts_value">
            <input type="text" id="txtUsername" name="txtUsername"/>
            </td>
        </tr> 
        <tr>
            <td class="facts_label">
            <label><?php print $pgv_lang["label_password_id"];?></label>
            </td>
            <td class="facts_value">
            <input type="password" id="txtPassword" name="txtPassword"/>
            </td>
        </tr>
		<tr>
            <td class="facts_value" align="center" colspan="2">
            <input type="submit" value="<?php echo $pgv_lang['label_add_server'];?>" id="btnAddServer" />
			<input name="action" type="hidden" value="<?php echo $pgv_lang['label_add_server'];?>"/>
            </td>
        </tr>
    </table>
    </td>
</tr>

</table>
</form>
</td>
</tr>
</table>
</div>
<?php
  print_footer();
?>

<?php
/**
 * Send a message to a user in the system
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  John Finlay and Others
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
 * @version $Id: message.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 */
require("config.php");
require $PGV_BASE_DIRECTORY.$confighelpfile["english"];
if (file_exists($PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE])) require $PGV_BASE_DIRECTORY.$confighelpfile[$LANGUAGE];
if (!isset($action)) $action="compose";

print_simple_header($pgv_lang["phpgedview_message"]);

if (!isset($subject)) $subject = "";
if (!isset($url)) $url = "";
if (!isset($method)) $method="messaging2";
if (isset($body)) $body = stripslashes($body);
else $body = "";
if (!isset($from_name)) $from_name="";
if (!isset($from_email)) $from_email="";

if (empty($to)) {
	print "<span class=\"error\">".$pgv_lang["no_to_user"]."</span><br />";
	print_simple_footer();
	exit;
}
if ($to=="all" && !userIsAdmin(getUserName())) {
	print "<span class=\"error\">".$pgv_lang["no_to_user"]."</span><br />";
	print_simple_footer();
	exit;
}

if (($action=="send")&&(isset($_SESSION["good_to_send"]))&&($_SESSION["good_to_send"]===true)) {
	$_SESSION["good_to_send"] = false;
	if (!empty($from_email)) $from = $from_email;
	$tuser = getUser($from);
	if (!$tuser) {
		$mt = preg_match("/(.+)@(.+)/", $from, $match);
	    if ($mt>0) {
		    $host = trim($match[2]);
		    if (function_exists("checkdnsrr")) {
	            $ip = checkdnsrr($host);
	            if ($ip === false) {
		            $host = "www.".$host;
		            $ip = checkdnsrr($host);
		            if ($ip === false) {
			            print "<center><br /><span class=\"error\">".$pgv_lang["invalid_email"]."</span>\n";
			            print "<br /><br /></center>";
			            $action="compose";
						//print_simple_footer();
						//exit;
					}
	            }
            }
	    }
	    else {
		     print "<center><br /><span class=\"error\">".$pgv_lang["invalid_email"]."</span>\n";
            print "<br /><br /></center>";
            $action="compose";
	    }
	}
	//-- check referer for possible spam attack
	if (!isset($_SERVER['HTTP_REFERER']) || stristr($_SERVER['HTTP_REFERER'],"message.php")===false) {
		print "<center><br /><span class=\"error\">Invalid page referer.</span>\n";
        print "<br /><br /></center>";
        AddToLog('Invalid page referer while trying to send a message.  Possible spam attach.');
        $action="compose";
	}
	if ($action!="compose") {
		$toarray = array();
		$toarray[] = $to;
		if ($to == "all") {
			$toarray = array();
			$users = getUsers();
			foreach($users as $indexval => $tuser) $toarray[] = $tuser["username"];
		}
		if ($to == "never_logged") {
			$toarray = array();
			$users = getUsers();
			foreach($users as $indexval => $tuser) {
				if ($tuser["reg_timestamp"] > $tuser["sessiontime"]) {
					$toarray[] = $tuser["username"];
				}
			}
		}
		if ($to == "last_6mo") {
			$toarray = array();
			$users = getUsers();
			$sixmos = 60*60*24*30*6;	//-- timestamp for six months
			foreach($users as $indexval => $tuser) {
				if (time() - $tuser["sessiontime"] > $sixmos) {
					$toarray[] = $tuser["username"];
				}
			}
		}
		$i = 0;
		foreach($toarray as $indexval => $to) {
			$message = array();
			$message["to"]=$to;
			$message["from"]=$from;
			if (!empty($from_name)) {
				$message["from_name"] = $from_name;
				$message["from_email"] = $from_email;
			}
			$message["subject"] = $subject;
			$url = preg_replace("/".session_name()."=.*/", "", $url);
			$message["body"] = $body;
			$message["created"] = $time;
			$message["method"] = $method;
			$message["url"] = $url;
			if ($i>0) $message["no_from"] = true;
			if (addMessage($message)) print $pgv_lang["message_sent"]." - ".$to;
			$i++;
		}
	}
}

if ($action=="compose") {
	print '<span class="subheaders">'.$pgv_lang["message"].'</span>';
	$_SESSION["good_to_send"] = true;
	?>
	<script language="JavaScript" type="text/javascript">
		function validateEmail(email) {
			if (email.value.search("(.*)@(.*)")==-1) {
				alert('<?php print $pgv_lang["invalid_email"]; ?>');
				email.focus();
				return false;
			}
			return checkForm(document.messageform);
		}
		function checkForm(frm) {
			if (frm.subject.value=="") {
				alert('<?php print $pgv_lang["enter_subject"]; ?>');
				document.messageform.subject.focus();
				return false;
			}
			if (frm.body.value=="") {
				alert('<?php print $pgv_lang["enter_body"]; ?>');
				document.messageform.body.focus();
				return false;
			}
			return true;
		}
	</script>
	<?php
	$username = getUserName();
	if (empty($username)) {
		print "<br /><br />".$pgv_lang["message_instructions"];
	}
	print "<br /><form name=\"messageform\" method=\"post\" action=\"message.php\" onsubmit=\"t = new Date(); document.messageform.time.value=t.toUTCString(); ";
	if (empty($username)) print "return validateEmail(document.messageform.from_email);";
	else print "return checkForm(this);";
	print "\">\n";
	print "<table>\n";
	$touser = getUser($to);
	$lang_temp = "lang_name_".$touser["language"];
	if ($touser) {
		print "<tr><td></td><td>".str_replace("#TO_USER#", "<b>".$touser["firstname"]." ".$touser["lastname"]."</b>", $pgv_lang["sending_to"])."<br />";
		print str_replace("#USERLANG#", "<b>".$pgv_lang[$lang_temp]."</b>", $pgv_lang["preferred_lang"])."</td></tr>\n";
	}

	if (empty($username)){
		print "<tr><td valign=\"top\" width=\"15%\" align=\"right\">".$pgv_lang["message_from_name"]."</td>";
		print "<td><input type=\"text\" name=\"from_name\" size=\"40\" value=\"$from_name\" /></td></tr><tr><td valign=\"top\" align=\"right\">".$pgv_lang["message_from"]."</td><td><input type=\"text\" name=\"from_email\" size=\"40\" value=\"$from_email\" /><br />".$pgv_lang["provide_email"]."<br /><br /></td></tr>\n";
	}
	print "<tr><td align=\"right\">".$pgv_lang["message_subject"]."</td>";
	print "<td>";
	if (!empty($username)){
		print "<input type=\"hidden\" name=\"from\" value=\"$username\" />\n";
	}
	print "<input type=\"hidden\" name=\"action\" value=\"send\" />\n";
	print "<input type=\"hidden\" name=\"to\" value=\"$to\" />\n";
	print "<input type=\"hidden\" name=\"time\" value=\"\" />\n";
	print "<input type=\"hidden\" name=\"method\" value=\"$method\" />\n";
	print "<input type=\"hidden\" name=\"url\" value=\"$url\" />\n";
	print "<input type=\"text\" name=\"subject\" size=\"50\" value=\"$subject\" /><br /></td></tr>\n";
	print "<tr><td valign=\"top\" align=\"right\">".$pgv_lang["message_body"]."<br /></td><td><textarea name=\"body\" cols=\"50\" rows=\"7\">$body</textarea><br /></td></tr>\n";
	print "<tr><td></td><td><input type=\"submit\" value=\"".$pgv_lang["send"]."\" /></td></tr>\n";
	print "</table>\n";
	print "</form>\n";
	if ($method=="messaging2") print $pgv_lang["messaging2_help"];
}
else if ($action=="delete") {
	if (deleteMessage($id)) print $pgv_lang["message_deleted"];
}
print "<center><br /><br /><a href=\"javascript:;\" onclick=\"if (window.opener.refreshpage) window.opener.refreshpage(); window.close();\">".$pgv_lang["close_window"]."</a><br /></center>";

print_simple_footer();
?>

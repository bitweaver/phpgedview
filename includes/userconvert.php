<?php
/*
 * Created on 02-Oct-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function fillUser($gBitUser) {
//	global $;

	if (empty($username)) return false;
	if (isset($users[$username])) return $users[$username];
//	$username = db_prep($username);
	$sql = "SELECT * FROM ".PHPGEDVIEW_DB_PREFIX."users WHERE ";
	if (stristr($DBTYPE, "mysql")!==false) $sql .= "BINARY ";
	$sql .= "u_username='".$username."'";
	$res = dbquery($sql, false);

	if ($res===false) return false;
	if ($res->numRows()==0) return false;
	if ($res) {
		while($user_row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
			if ($user_row) {
				$user = array();
				$user["username"]=$user_row["u_username"];
				$user["firstname"]=stripslashes($user_row["u_firstname"]);
				$user["lastname"]=stripslashes($user_row["u_lastname"]);
				$user["gedcomid"]=unserialize($user_row["u_gedcomid"]);
				$user["rootid"]=unserialize($user_row["u_rootid"]);
				$user["password"]=$user_row["u_password"];
				if ($user_row["u_canadmin"]=='Y') $user["canadmin"]=true;
				else $user["canadmin"]=false;
				$user["canedit"]=unserialize($user_row["u_canedit"]);
				//-- convert old <3.1 access levels to the new 3.2 access levels
				foreach($user["canedit"] as $key=>$value) {
					if ($value=="no") $user["canedit"][$key] = "access";
					if ($value=="yes") $user["canedit"][$key] = "edit";
				}
				foreach($GEDCOMS as $ged=>$gedarray) {
					if (!isset($user["canedit"][$ged])) $user["canedit"][$ged] = "access";
				}
				$user["email"] = $user_row["u_email"];
				$user["verified"] = $user_row["u_verified"];
				$user["verified_by_admin"] = $user_row["u_verified_by_admin"];
				$user["language"] = $user_row["u_language"];
				$user["pwrequested"] = $user_row["u_pwrequested"];
				$user["reg_timestamp"] = $user_row["u_reg_timestamp"];
				$user["reg_hashcode"] = $user_row["u_reg_hashcode"];
				$user["theme"] = $user_row["u_theme"];
				$user["loggedin"] = $user_row["u_loggedin"];
				$user["sessiontime"] = $user_row["u_sessiontime"];
				$user["contactmethod"] = $user_row["u_contactmethod"];
				if ($user_row["u_visibleonline"]!='N') $user["visibleonline"]=true;
				else $user["visibleonline"]=false;
				if ($user_row["u_editaccount"]!='N' || $user["canadmin"]) $user["editaccount"]=true;
				else $user["editaccount"]=false;
				$user["default_tab"] = $user_row["u_defaulttab"];
				$user["comment"] = $user_row["u_comment"];
				$user["comment_exp"] = $user_row["u_comment_exp"];
				$user["sync_gedcom"] = $user_row["u_sync_gedcom"];
				$user["relationship_privacy"] = $user_row["u_relationship_privacy"];
				$user["max_relation_path"] = $user_row["u_max_relation_path"];
//				if ($user_row["u_auto_accept"]!='Y') $user["auto_accept"]=false;
//				else $user["auto_accept"]=true;
				if ($user_row["u_auto_accept"]!='N') $user["auto_accept"]=true;
				else $user["auto_accept"]=false;
				$users[$user_row["u_username"]] = $user;
			}
		}
		$res->free();
		if (isset($user)) return $user;
	}
	return false;
}

?>

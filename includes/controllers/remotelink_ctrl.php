<?php
/**
* Add Remote Link Page
*
* Allow a user the ability to add links to people from other servers and other gedcoms.
*
* phpGedView: Genealogy Viewer
* Copyright (C) 2002 to 2009 PGV Development Team. All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* @package PhpGedView
* @subpackage Charts
* @version $Id: remotelink_ctrl.php,v 1.1 2009/04/30 19:09:48 lsces Exp $
*/

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_REMOTELINK_CTRL_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_edit.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/classes/class_serviceclient.php');

class RemoteLinkController extends BaseController {
	var $has_familysearch=null;
	var $pid=null;
	var $person=null;
	var $server_list=null;
	var $gedcom_list=null;

	// Values from the add remote link form.
	public $form_txtPID=null;
	public $form_cbRelationship='current_person';
	public $form_location=null;
	public $form_txtURL=null;
	public $form_txtTitle=null;
	public $form_txtGID=null;
	public $form_txtUsername=null;
	public $form_txtPassword=null;
	public $form_cbExistingServers=null;
	public $form_txtCB_Title=null;
	public $form_txtCB_GID=null;
	public $form_txtFS_URL=null;
	public $form_txtFS_Title=null;
	public $form_txtFS_GID=null;
	public $form_txtFS_Username=null;
	public $form_txtFS_Password=null;

	// Initialize the controller for the add remote link
	function init() {
		// Cannot edit with a "remember me" login.
		if ($_SESSION["cookie_login"]) {
			header('Location: '.encode_url("login.php?type=simple&url=".urlencode("edit_interface.php?".decode_url($QUERY_STRING)), false));
			exit;
		}

		// Coming soon ???
		$this->has_familysearch=file_exists('modules/FamilySearch/familySearchWrapper.php');
		if ($this->has_familysearch) {;
			require_once 'modules/FamilySearch/familySearchWrapper.php';
		}

		// The PID can come from a URL or a form
		$this->pid=safe_REQUEST($_REQUEST, 'pid', PGV_REGEX_XREF);

		$this->person=Person::getInstance($this->pid);
		$this->server_list=get_server_list();
		$this->gedcom_list=get_all_gedcoms();
		unset($this->gedcom_list[PGV_GED_ID]);

		// Other input values come from the form
		$this->form_txtPID           =safe_POST('txtPID', PGV_REGEX_XREF);
		$this->form_cbRelationship   =safe_POST('cbRelationship');
		$this->form_location         =safe_POST('location');
		$this->form_txtURL           =safe_POST('txtURL', PGV_REGEX_URL);
		$this->form_txtTitle         =safe_POST('txtTitle', '[^<>"%{};]+');
		$this->form_txtGID           =safe_POST('txtGID', $this->gedcom_list);
		$this->form_txtUsername      =safe_POST('txtUsername', PGV_REGEX_USERNAME);
		$this->form_txtPassword      =safe_POST('txtPassword', PGV_REGEX_PASSWORD);
		$this->form_cbExistingServers=safe_POST('cbExistingServers', array_keys($this->server_list));
		$this->form_txtCB_Title      =safe_POST('txtCB_Title', '[^<>"%{};]+');
		$this->form_txtCB_GID        =safe_POST('txtCB_GID', $this->gedcom_list);			
		$this->form_txtFS_URL        =safe_POST('txtFS_URL', PGV_REGEX_URL);
		$this->form_txtFS_Title      =safe_POST('txtFS_Title', '[^<>"%{};]+');
		$this->form_txtFS_GID        =safe_POST('txtFS_GID', $this->gedcom_list);
		$this->form_txtFS_Username   =safe_POST('txtFS_Username', PGV_REGEX_USERNAME);
		$this->form_txtFS_Password   =safe_POST('txtFS_Password', PGV_REGEX_PASSWORD);

		if (is_null($this->form_location)) {
			if ($this->server_list) {
				$this->form_location='existing';			
			} else {
				$this->form_location='remote';
			}
		}

	}

	// Perform the desired action and return true/false sucess.
	function runAction($action) {
		switch ($action) {
		case 'addlink':
			return $this->addLink();
		default:
			return false;
		}
	}

	// Add a remote phpGedView server
	//
	// @param string $title
	// @param string $url
	// @param string $gedcom_id
	// @param string $username
	// @param string $password
	// @return mixed the serverID of the server to link to
	function addRemoteServer($title, $url, $gedcom_id, $username, $password) {
		if (!$url || !$gedcom_id || !$username || !$password) {
			return null;
		}
			
		if (preg_match("/\?wsdl$/", $url)==0) {
			$url.="?wsdl";
		}

		$serverID=$this->checkExistingServer($url, $gedcom_id);
		if (!$serverID) {
			$gedcom_string="0 @new@ SOUR\n1 URL {$url}\n1 _DBID {$gedcom_id}\n2 _USER {$username}\n2 _PASS {$password}\n3 RESN confidential";
			$service=new ServiceClient($gedcom_string);
			$sid=$service->authenticate();
			if (PEAR::isError($sid)) {
				$sid='';
			}
			if (!$sid) {
				echo '<span class="error">failed to authenticate to remote site</span>';
				$serverID=null;
			}	else {
				if (!$title) {
					$title=$service->getServiceTitle();
				}
				$gedcom_string.="\n1 TITL {$title}";
				$serverID=append_gedrec($gedcom_string);
			}
		}
		return $serverID;
	}

	// Add a familySearch server
	//
	// @param string $title
	// @param string $url
	// @param string $gedcom_id
	// @param string $username
	// @param string $password
	// @return mixed the serverID of the server to link to
	function addFamilySearchServer($title, $url, $gedcom_id, $username, $password) {
		$serverID = $this->checkExistingServer($url, $gedcom_id);
		if (!$serverID) {
			$gedcom_string = "0 @new@ SOUR\n";
			$gedcom_string.= "1 URL ".$url."\n";
			$gedcom_string.= "2 TYPE FamilySearch\n";
			$gedcom_string.= "1 _DBID ".$gedcom_id."\n";
			$gedcom_string.= "2 _USER ".$username."\n";
			$gedcom_string.= "2 _PASS ".$password."\n";
			//-- only allow admin users to see password
			$gedcom_string.= "3 RESN confidential\n";
			$service = new FamilySearchWrapper($gedcom_string);
			$sid = $service->authenticate();
			if (PEAR::isError($sid)) {
				$sid = '';
			}
			if (empty($sid)) {
				echo "<span class=\"error\">failed to authenticate to remote site</span>";
			} else {
				if (empty($title)) {
					$title = $service->getServiceTitle();
				}
				$title = $service->getServiceTitle();
				$gedcom_string.= "1 TITL ".$title."\n";
				$serverID = append_gedrec($gedcom_string);
			}
		}
		return $serverID;
	}

	// Add a server record for a local remote link
	//
	// @param string $gedcom_id
	// @return mixed the serverID of the server to link to
	function addLocalServer($title, $gedcom_id) {
		global $SERVER_URL, $GEDCOMS;
		$serverID = $this->checkExistingServer($SERVER_URL, $gedcom_id);
		if ($serverID===false) {
			$gedcom_string = "0 @new@ SOUR\n";
			if (empty($title)) {
				$title = $GEDCOMS[$gedcom_id]["title"];
			}
			$gedcom_string.= "1 TITL ".$title."\n";
			$gedcom_string.= "1 URL ".$SERVER_URL."\n";
			$gedcom_string.= "1 _DBID ".$gedcom_id."\n";
			$gedcom_string.= "2 _BLOCK false\n";
			$serverID = append_gedrec($gedcom_string);
		}
		return $serverID;
	}

	// check if the server already exists
	//
	// @param string $url
	// @param string $gedcom_id
	// @return mixed the id of the server to link to or false if it does not exist
	function checkExistingServer($url, $gedcom_id='') {
		global $pgv_changes;
		//-- get rid of the protocol
		$turl = preg_replace("~^\w+://~", "", $url);
		//-- check the existing server list
		foreach ($this->server_list as $id=>$server) {
			if (stristr($server['url'], $turl)) {
				if (empty($gedcom_id) || preg_match("/\n1 _DBID {$gedcom_id}/", $server['gedcom']))
				return $id;
			}
		}

		//-- check for recent additions
		foreach ($pgv_changes as $cid=>$changes) {
			$change = $changes[count($changes) - 1];
			if ($change['type']!='delete') {
				$gid = $change["gid"];
				$indirec = $change["undo"];
				$surl = get_gedcom_value("URL", 1, $indirec);
				if (!empty($surl) && stristr($surl, $turl)) {
					if (preg_match('/^0 @('.PGV_REGEX_XREF.')@ *('.PGV_REGEX_TAG.')/', $indirec, $match)) {
						$id = $match[1];
						$type=$match[2];
						if ($type=="SOUR") {
							if (empty($gedcom_id) || preg_match("/\n1 _DBID {$gedcom_id}/", $indirec))
								return $id;
						}
					}
				}
			}
		}
		return false;
	}

	function addLink() {
		global $pgv_lang, $GEDCOM;

		switch ($this->form_location) {
		case 'remote':
			$serverID=$this->addRemoteServer(
				$this->form_txtTitle,
				$this->form_txtURL,
				$this->form_txtGID,
				$this->form_txtUsername,
				$this->form_txtPassword
			);
			break;
		case 'local':
			$serverID=$this->addLocalServer(
				$this->form_txtCB_Title,
				$this->form_txtCB_GID
			);
			break;
		case 'existing':
			$serverID=$this->form_cbExistingServers;
			break;
		case "FamilySearch":
			//TODO: Make sure that it is merging correctly
			$serverID=$this->addFamilySearchServer(
				$this->form_txtFS_URL,
				$this->form_txtFS_URL,
				$this->form_txtFS_GID,
				$this->form_txtFS_Username,
				$this->form_txtFS_Password
			);
			break;
		}

		$link_pid     =$this->form_txtPID;
		$relation_type=$this->form_cbRelationship;
		if ($serverID && $link_pid) {
			if (isset($pgv_changes[$this->pid."_".$GEDCOM])) {
				$indirec=find_updated_record($this->pid);
			} else {
				$indirec=find_person_record($this->pid);
			}
	
			switch ($relation_type) {
			case "father":
				$indistub="0 @new@ INDI\n1 SOUR @{$serverID}@\n2 PAGE {$link_pid}\n1 RFN {$serverID}:{$link_pid}";
				$stub_id=append_gedrec($indistub, false);
				$indistub=find_updated_record($stub_id);
	
				$gedcom_fam="0 @new@ FAM\n1 HUSB @{$stub_id}@\n1 CHIL @{$this->pid}@";
				$fam_id=append_gedrec($gedcom_fam);
	
				$indirec.= "\n1 FAMC @{$fam_id}@";
				replace_gedrec($this->pid, $indirec);

				$serviceClient=ServiceClient::getInstance($serverID);
				$indistub=$serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
				$indistub.="\n1 FAMS @{$fam_id}@";
				replace_gedrec($stub_id, $indistub, false);
				break;
			case "mother":
				$indistub="0 @new@ INDI\n1 SOUR @{$serverID}@\n2 PAGE {$link_pid}\n1 RFN {$serverID}:{$link_pid}";
				$stub_id=append_gedrec($indistub, false);
				$indistub=find_updated_record($stub_id);
	
				$gedcom_fam="0 @new@ FAM\n1 WIFE @{$stub_id}@\n1 CHIL @{$this->pid}@";
				$fam_id=append_gedrec($gedcom_fam);
	
				$indirec.="\n1 FAMC @{$fam_id}@";
				replace_gedrec($this->pid, $indirec);
	
				$serviceClient=ServiceClient::getInstance($serverID);
				$indistub=$serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
				$indistub.="\n1 FAMS @".$fam_id."@";
				replace_gedrec($stub_id, $indistub, false);
				break;
			case "husband":
				$indistub="0 @new@ INDI\n1 SOUR @{$serverID}@\n2 PAGE {$link_pid}\n1 RFN {$serverID}:{$link_pid}";
				$stub_id=append_gedrec($indistub, false);
				$indistub=find_updated_record($stub_id);
	
				$gedcom_fam="0 @new@ FAM\n1 MARR Y\n1 WIFE @{$this->pid}@\n1 HUSB @{$stub_id}@\n";
				$fam_id=append_gedrec($gedcom_fam);
	
				$indirec.="\n1 FAMS @{$fam_id}@";
				replace_gedrec($this->pid, $indirec);
	
				$serviceClient=ServiceClient::getInstance($serverID);
				$indistub=$serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
			$indistub.="\n1 FAMS @{$fam_id}@";
				replace_gedrec($stub_id, $indistub, false);
				break;
			case "wife":
				$indistub="0 @new@ INDI\n1 SOUR @{$serverID}@\n2 PAGE {$link_pid}\n1 RFN {$serverID}:{$link_pid}";
				$stub_id=append_gedrec($indistub, false);
				$indistub=find_updated_record($stub_id);

				$gedcom_fam="0 @new@ FAM\n1 MARR Y\n1 WIFE @{$stub_id}@\n1 HUSB @{$this->pid}@";
				$fam_id=append_gedrec($gedcom_fam);
	
				$indirec.="\n1 FAMS @{$fam_id}@";
				replace_gedrec($this->pid, $indirec);
	
				$serviceClient=ServiceClient::getInstance($serverID);
				$indistub=$serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
				$indistub.="\n1 FAMS @{$fam_id}@\n";
				replace_gedrec($stub_id, $indistub, false);
				break;
			case "son":
			case "daughter":
				$indistub="0 @new@ INDI\n1 SOUR @{$serverID}@\n2 PAGE {$link_pid}\n1 RFN {$serverID}:{$link_pid}";
				$stub_id=append_gedrec($indistub, false);
				$indistub=find_updated_record($stub_id);
	
				if (get_gedcom_value('SEX', 1, $indirec, '', false)=='F') {
					$gedcom_fam="0 @new@ FAM\n1 WIFE @{$this->pid}@\n1 CHIL @{$stub_id}@";
				} else {
					$gedcom_fam="0 @new@ FAM\n1 HUSB @{$this->pid}@\n1 CHIL @{$stub_id}@";
				}
				$fam_id=append_gedrec($gedcom_fam);
				$indirec.="\n1 FAMS @{$fam_id}@";
				replace_gedrec($this->pid, $indirec);
	
				$serviceClient=ServiceClient::getInstance($serverID);
				$indistub=$serviceClient->mergeGedcomRecord($link_pid, $indistub, true, true);
				$indistub.="\n1 FAMC @".$fam_id."@";
				replace_gedrec($stub_id, $indistub,false);
				break;
			case 'current_person':
				$indirec.="\n1 RFN {$serverID}:{$link_pid}\n1 SOUR @{$serverID}@\n2 PAGE {$link_pid}";
	
				$serviceClient = ServiceClient::getInstance($serverID);
				if (!is_null($serviceClient)) {
					//-- get rid of change date
				$pos1=strpos($indirec, "\n1 CHAN");
					if ($pos1!==false) {
						$pos2=strpos($indirec, "\n1", $pos1+5);
						$indirec=substr($indirec, 0, $pos1).substr($indirec, $pos2);
					}
				$indirec=$serviceClient->mergeGedcomRecord($link_pid, $indirec, true, true);
				} else {
					echo 'Unable to find server';
				}
				break;
			}
			echo '<b>', $pgv_lang['link_success'], '</b>';
			return true;
		}
		return false;
	}

	/**
	* whether or not the user has access to this area
	*
	* @return boolean
	*/
	function canAccess() {
		global $ALLOW_EDIT_GEDCOM;

		return $ALLOW_EDIT_GEDCOM  && PGV_USER_GEDCOM_ADMIN && $this->person->canDisplayDetails();
	}
}

?>

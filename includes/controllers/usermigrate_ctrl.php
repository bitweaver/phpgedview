<?php
/**
 * Controller for backup and export
 * Exports users and their data to either SQL queries (Index mode) or
 * authenticate.php and xxxxxx.dat files (MySQL mode).
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @author Boudewijn Sjouke	sjouke@users.sourceforge.net
 * @package PhpGedView
 * @subpackage Admin
 * @version $Id: usermigrate_ctrl.php,v 1.3 2009/09/15 20:06:00 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_USERMIGRATE_CTRL_PHP', '');

require_once(PHPGEDVIEW_PKG_PATH.'includes/controllers/basecontrol.php');
require_once(PHPGEDVIEW_PKG_PATH.'includes/functions/functions_export.php');

loadLangFile("pgv_confighelp");

//-- make sure that they have admin status before they can use this page
//-- otherwise have them login again
if (strstr($_SERVER['SCRIPT_NAME'], "usermigrate_cli.php")) {
	if (PGV_USER_IS_ADMIN || !isset($argc)) {
		header("Location: usermigrate.php");
		exit;
	}
}
else if (!PGV_USER_IS_ADMIN) {
	header("Location: login.php?url=usermigrate.php");
	exit;
}


class UserMigrateControllerRoot extends BaseController {
	var $proceed;
	var $flist;
	var $v_list;
	var $fname;
	var $buname;
	var $errorMsg;
	var $fileExists = false;
	var $impSuccess = false;
	var $msgSuccess = false;
	var $favSuccess = false;
	var $newsSuccess = false;
	var $blockSuccess = false;

	/**
	 * constructor
	 */
	function UserMigrateControllerRoot() {
		parent::BaseController();
	}

	/**
	 * Initialize the controller and start the logic
	 *
	 */
	function init() {
		global $INDEX_DIRECTORY;
		if (!isset($_REQUEST['proceed'])) $this->proceed = "backup";
		else $this->proceed = $_REQUEST['proceed'];

		if ($this->proceed == "backup") $this->backup();
		else if ($this->proceed == "export") {
			$i = 0;
			if (file_exists($INDEX_DIRECTORY."authenticate.php")) $i = $i + 1;
			if (file_exists($INDEX_DIRECTORY."news.dat")) $i = $i + 1;
			if (file_exists($INDEX_DIRECTORY."messages.dat")) $i = $i + 1;
			if (file_exists($INDEX_DIRECTORY."blocks.dat")) $i = $i + 1;
			if (file_exists($INDEX_DIRECTORY."favorites.dat")) $i = $i + 1;
			if ($i > 0) {
				$this->fileExists = true;
			}
			else $this->proceed = "exportovr";
		}
		if ($this->proceed == "exportovr") {
			if (file_exists($INDEX_DIRECTORY."authenticate.php")) unlink($INDEX_DIRECTORY."authenticate.php");
			if (file_exists($INDEX_DIRECTORY."news.dat")) unlink($INDEX_DIRECTORY."news.dat");
			if (file_exists($INDEX_DIRECTORY."messages.dat")) unlink($INDEX_DIRECTORY."messages.dat");
			if (file_exists($INDEX_DIRECTORY."blocks.dat")) unlink($INDEX_DIRECTORY."blocks.dat");
			if (file_exists($INDEX_DIRECTORY."favorites.dat")) unlink($INDEX_DIRECTORY."favorites.dat");
			um_export($this->proceed);
		}

		if ($this->proceed == "import") {
			$this->import();
		}
	}

	/**
	 * Return the page title
	 *
	 * @return string
	 */
	function getPageTitle() {
		global $pgv_lang;

		if ($this->proceed == "backup") return $pgv_lang["um_backup"];
		else return $pgv_lang["um_header"];
	}

	/**
	 * generate the backup zip file
	 *
	 */
	function backup() {
		global $INDEX_DIRECTORY, $GEDCOMS, $GEDCOM;
		global $MEDIA_DIRECTORY, $USE_MEDIA_FIREWALL, $MEDIA_FIREWALL_ROOTDIR;

		$this->flist = array();

		// Backup user information
		if (isset($_POST["um_usinfo"])) {
			// If in pure DB mode, we must first create new .dat files and authenticate.php
			// First delete the old files
			if (file_exists($INDEX_DIRECTORY."authenticate.php")) unlink($INDEX_DIRECTORY."authenticate.php");
			if (file_exists($INDEX_DIRECTORY."news.dat")) unlink($INDEX_DIRECTORY."news.dat");
			if (file_exists($INDEX_DIRECTORY."messages.dat")) unlink($INDEX_DIRECTORY."messages.dat");
			if (file_exists($INDEX_DIRECTORY."blocks.dat")) unlink($INDEX_DIRECTORY."blocks.dat");
			if (file_exists($INDEX_DIRECTORY."favorites.dat")) unlink($INDEX_DIRECTORY."favorites.dat");

			// Then make the new ones
			um_export($this->proceed);

			// Make filelist for files to ZIP
			if (file_exists($INDEX_DIRECTORY."authenticate.php")) $this->flist[] = $INDEX_DIRECTORY."authenticate.php";
			if (file_exists($INDEX_DIRECTORY."news.dat")) $this->flist[] = $INDEX_DIRECTORY."news.dat";
			if (file_exists($INDEX_DIRECTORY."messages.dat")) $this->flist[] = $INDEX_DIRECTORY."messages.dat";
			if (file_exists($INDEX_DIRECTORY."blocks.dat")) $this->flist[] = $INDEX_DIRECTORY."blocks.dat";
			if (file_exists($INDEX_DIRECTORY."favorites.dat")) $this->flist[] = $INDEX_DIRECTORY."favorites.dat";
		}

		// Backup config.php
		if (isset($_POST["um_config"])) {
			$this->flist[] = "config.php";
		}

		// Backup gedcoms and media
		if (isset($_POST["um_gedcoms"]) || isset($_POST["um_media"])) {

			$exportOptions = array();
			$exportOptions['privatize'] = 'none';
			$exportOptions['toANSI'] = 'no';
			$exportOptions['noCustomTags'] = 'no';
			$exportOptions['slashes'] = 'forward';

			foreach($GEDCOMS as $key=>$gedcom) {
				//-- load the gedcom configuration settings
				require(get_config_file($key));

				if (isset($_POST["um_gedcoms"])) {
					//-- backup the original gedcom file
					if (file_exists($gedcom["path"])) $this->flist[] = $gedcom["path"];

					//-- recreate the GEDCOM file from the DB
					//-- backup the DB in case of GEDCOM corruption
					$gedname = $INDEX_DIRECTORY.$key.".bak";
					$gedout = fopen(filename_decode($gedname), "wb");

					$exportOptions['path'] = $MEDIA_DIRECTORY;

					export_gedcom($key, $gedout, $exportOptions);
					fclose($gedout);
					$this->flist[] = $gedname;
				}

				if (isset($_POST["um_media"])) {
					// backup media files
					$dir = dir($MEDIA_DIRECTORY);
					while(false !== ($entry = $dir->read())) {
						if ($entry{0} != ".") {
							if ($entry != "thumbs") $this->flist[] = $MEDIA_DIRECTORY.$entry;
						}
					}
					if ($USE_MEDIA_FIREWALL) {
						$dir = dir($MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY);
						while(false !== ($entry = $dir->read())) {
							if ($entry{0} != ".") {
								if ($entry != "thumbs" && $entry != "watermark") $this->flist[] = $MEDIA_FIREWALL_ROOTDIR.$MEDIA_DIRECTORY.$entry;
							}
						}
					}
				}
			}

			//-- restore the old configuration file
			require(get_config_file($GEDCOM));
			$this->flist[] = $INDEX_DIRECTORY."pgv_changes.php";
		}

		// Backup gedcom settings
		if (isset($_POST["um_gedsets"])) {

			// Gedcoms file
			if (file_exists($INDEX_DIRECTORY."gedcoms.php")) $this->flist[] = $INDEX_DIRECTORY."gedcoms.php";

			foreach($GEDCOMS as $key => $gedcom) {

				// Config files
				if (file_exists($INDEX_DIRECTORY.$gedcom["gedcom"]."_conf.php")) $this->flist[] = $INDEX_DIRECTORY.$gedcom["gedcom"]."_conf.php";

				// Privacy files
				if (file_exists($INDEX_DIRECTORY.$gedcom["gedcom"]."_priv.php")) $this->flist[] = $INDEX_DIRECTORY.$gedcom["gedcom"]."_priv.php";
			}
		}

		// Backup logfiles and counters
		if (isset($_POST["um_logs"])) {
			foreach($GEDCOMS as $key => $gedcom) {

				// Gedcom counters
				if (file_exists($INDEX_DIRECTORY.$gedcom["gedcom"]."pgv_counters.php")) $this->flist[] = $INDEX_DIRECTORY.$gedcom["gedcom"]."pgv_counters.php";

				// Gedcom searchlogs and changelogs
				$dir_var = opendir ($INDEX_DIRECTORY);
				while ($file = readdir ($dir_var)) {
					if (strpos($file, ".log") > 0 && (strstr($file, "srch-".$gedcom["gedcom"]) !== false || strstr($file, "ged-".$gedcom["gedcom"]) !== false)) $this->flist[] = $INDEX_DIRECTORY.$file;
				}
				closedir($dir_var);
			}

			// PhpGedView logfiles
			$dir_var = opendir ($INDEX_DIRECTORY);
			while ($file = readdir ($dir_var)) {
				if (strpos($file, ".log") > 0 && strstr($file, "pgv-") !== false) $this->flist[] = $INDEX_DIRECTORY.$file;
			}
			closedir($dir_var);
		}

		// Make the zip
		if (count($this->flist) > 0) {
			require_once "includes/pclzip.lib.php";
			$this->buname = date("YmdHis").".zip";
			$this->fname = $INDEX_DIRECTORY.$this->buname;
			$comment = "Created by ".PGV_PHPGEDVIEW." ".PGV_VERSION_TEXT." on ".date("r").".";
			$archive = new PclZip($this->fname);
			//-- remove ../ from file paths when creating zip
			$ct = preg_match("~((\.\./)+)~", $INDEX_DIRECTORY, $match);
			$rmpath = "";
			if ($ct>0) $rmpath = $match[1];
				$this->v_list = $archive->create($this->flist, PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, $rmpath);
			if ($this->v_list==0) $this->errorMsg = "Error : ".$archive->errorInfo(true);
			if (isset($_POST["um_usinfo"])) {
				// Remove temporary files again
				if (file_exists($INDEX_DIRECTORY."authenticate.php")) unlink($INDEX_DIRECTORY."authenticate.php");
				if (file_exists($INDEX_DIRECTORY."news.dat")) unlink($INDEX_DIRECTORY."news.dat");
				if (file_exists($INDEX_DIRECTORY."messages.dat")) unlink($INDEX_DIRECTORY."messages.dat");
				if (file_exists($INDEX_DIRECTORY."blocks.dat")) unlink($INDEX_DIRECTORY."blocks.dat");
				if (file_exists($INDEX_DIRECTORY."favorites.dat")) unlink($INDEX_DIRECTORY."favorites.dat");
			}
		}
	}

	/**
	 * Import users etc. from index files
	 *
	 */
	function import() {
		global $INDEX_DIRECTORY, $TBLPREFIX, $pgv_lang, $GEDCOMS, $GEDCOM, $gBitDb;

		if ((file_exists($INDEX_DIRECTORY."authenticate.php")) == false) {
			$this->impSuccess = false;
			return;
		} else {
			require $INDEX_DIRECTORY."authenticate.php";
			$countold = count($users);
			$gBitDb->query("DELETE FROM {$TBLPREFIX}users");
			foreach($users as $username=>$user) {
				if ($user["editaccount"] == "1") $user["editaccount"] = "Y";
				else $user["editaccount"] = "N";
				//-- make sure fields are set for v4.0 DB
				if (!isset($user["firstname"])) {
					if (isset($user["fullname"])) {
						$parts = explode(' ', trim($user["fullname"]));
						$user["lastname"] = array_pop($parts);
						$user["firstname"] = implode(" ", $parts);
					}
					else {
						$user["firstname"] = '';
						$user["lastname"] = '';
					}
				}
				if (!isset($user["comment"])) $user["comment"] = '';
				if (!isset($user["comment_exp"])) $user["comment_exp"] = '';
				if (!isset($user["sync_gedcom"])) $user["sync_gedcom"] = 'N';
				if (!isset($user["relationship_privacy"])) $user["relationship_privacy"] = 'N';
				if (!isset($user["max_relation_path"])) $user["max_relation_path"] = '2';
				if (!isset($user["auto_accept"])) $user["auto_accept"] = 'N';

				if ($user_id=create_user($user['username'], $user['password'])) {
					set_user_setting($user_id, 'firstname',            $user["firstname"]);
					set_user_setting($user_id, 'lastname',             $user["lastname"]);
					set_user_setting($user_id, 'email',                $user["email"]);
					set_user_setting($user_id, 'theme',                $user["theme"]);
					set_user_setting($user_id, 'language',             $user["language"]);
					set_user_setting($user_id, 'contactmethod',        $user["contactmethod"]);
					set_user_setting($user_id, 'defaulttab',           $user["defaulttab"]);
					set_user_setting($user_id, 'comment',              $user["comment"]);
					set_user_setting($user_id, 'comment_exp',          $user["comment_exp"]);
					set_user_setting($user_id, 'pwrequested',          $user["pwrequested"]);
					set_user_setting($user_id, 'reg_timestamp',        $user["reg_timestamp"]);
					set_user_setting($user_id, 'reg_hashcode',         $user["reg_hashcode"]);
					set_user_setting($user_id, 'loggedin'    ,         $user["loggedin"]);
					set_user_setting($user_id, 'sessiontime'    ,      $user["sessiontime"]);
					set_user_setting($user_id, 'max_relation_path',    $user["max_relation_path"]);
					set_user_setting($user_id, 'sync_gedcom',          $user["sync_gedcom"] ? 'Y' : 'N');
					set_user_setting($user_id, 'relationship_privacy', $user["relationship_privacy"] ? 'Y' : 'N');
					set_user_setting($user_id, 'auto_accept',          $user["auto_accept"] ? 'Y' : 'N');
					set_user_setting($user_id, 'canadmin',             $user["canadmin"] ? 'Y' : 'N');
					set_user_setting($user_id, 'visibleonline',        $user["visibleonline"] ? 'Y' : 'N');
					set_user_setting($user_id, 'editaccount',          $user["editaccount"] ? 'Y' : 'N');
					set_user_setting($user_id, 'verified',             $user["verified"] ? 'yes' : 'no');
					set_user_setting($user_id, 'verified_by_admin',    $user["verified_by_admin"] ? 'yes' : 'no');
					foreach (array('gedcomid', 'rootid', 'canedit') as $var) {
						if ($user[$var]) {
							foreach (unserialize(stripslashes($user[$var])) as $gedcom=>$id) {
								set_user_gedcom_setting($user_id, $gedcom, $var, $id);
							}
						}
					}
					AddToLog("added user -> {$user['username']} <-");
				}
			}
			if ($countold == get_user_count()) {
				$this->impSuccess = true;
			}
			else {
				$this->impSuccess = false;
			}
		}

		if ((file_exists($INDEX_DIRECTORY."messages.dat")) == false) {
			$this->msgSuccess = false;
		}
		else {
			$gBitDb->query("DELETE FROM {$TBLPREFIX}messages");
			$messages = array();
			$fp = fopen($INDEX_DIRECTORY."messages.dat", "rb");
			$mstring = fread($fp, filesize($INDEX_DIRECTORY."messages.dat"));
			fclose($fp);
			$messages = unserialize($mstring);
			foreach($messages as $newid => $message) {
				$gBitDb->query("INSERT INTO {$TBLPREFIX}messages (m_id, m_from, m_to, m_subject, m_body, m_created) VALUES (?, ? ,? ,? ,? ,?)"
					, array($newid, $message["from"], $message["to"], $message["subject"], $message["body"], $message["created"]));
			}
			$this->msgSuccess = true;
		}

		if ((file_exists($INDEX_DIRECTORY."favorites.dat")) == false) {
			$this->favSuccess = false;
			print $pgv_lang["um_nofav"]."<br /><br />";
		}
		else {
			$gBitDb->query("DELETE FROM {$TBLPREFIX}favorites");
			$favorites = array();
			$fp = fopen($INDEX_DIRECTORY."favorites.dat", "rb");
			$mstring = fread($fp, filesize($INDEX_DIRECTORY."favorites.dat"));
			fclose($fp);
			$favorites = unserialize($mstring);

			foreach($favorites as $newid => $favorite) {
				$res = addFavorite($favorite);
				if (!$res || DB::isError($res)) {
					$this->errorMsg = "<span class=\"error\">Unable to update <i>Favorites</i> table.</span><br />\n";
					return;
				}
			}
			$this->favSuccess = true;
		}

		if ((file_exists($INDEX_DIRECTORY."news.dat")) == false) {
			$this->newsSuccess = false;
		}
		else {
			$gBitDb->query("DELETE FROM {$TBLPREFIX}news");
			$allnews = array();
			$fp = fopen($INDEX_DIRECTORY."news.dat", "rb");
			$mstring = fread($fp, filesize($INDEX_DIRECTORY."news.dat"));
			fclose($fp);
			$allnews = unserialize($mstring);
			foreach($allnews as $newid => $news) {
				$res = addNews($news);
				if (!$res) {
					$this->errorMsg = "<span class=\"error\">Unable to update <i>News</i> table.</span><br />\n";
					return;
				}
			}
			$this->newsSuccess = true;
		}

		if ((file_exists($INDEX_DIRECTORY."blocks.dat")) == false) {
			$this->blockSuccess = false;
		}
		else {
			$gBitDb->query("DELETE FROM {$TBLPREFIX}blocks");
			$allblocks = array();
			$fp = fopen($INDEX_DIRECTORY."blocks.dat", "rb");
			$mstring = fread($fp, filesize($INDEX_DIRECTORY."blocks.dat"));
			fclose($fp);
			$allblocks = unserialize($mstring);
			foreach($allblocks as $bid => $blocks) {
				$username = $blocks["username"];
				$gBitDb->query("INSERT INTO {$TBLPREFIX}blocks (b_id, b_username, b_location, b_order, b_name, b_config) VALUES (?, ? ,? , ?, ?, ?)"
					, array($bid, $blocks["username"], $blocks["location"], $blocks["order"], $blocks["name"], serialize($blocks["config"])));
			}
			$this->blockSuccess = true;
		}
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists('includes/controllers/usermigrate_ctrl_user.php'))
{
	include_once 'includes/controllers/usermigrate_ctrl_user.php';
}
else
{
	class UserMigrateController extends UserMigrateControllerRoot
	{
	}
}

?>

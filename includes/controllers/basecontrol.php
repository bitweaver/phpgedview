<?php
/**
 * Base controller for all controller classes
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage Controllers
 * @version $Id$
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('PGV_BASECONTROL_PHP', '');

class BaseController {
	var $view        =null;
	var $action      =null;
	var $show_changes=null;

	/**
	 * constructor for this class
	 */
	function BaseController() {
		$this->view        =safe_GET('view', 'preview');
		$this->action      =safe_GET('action');
		$this->show_changes=safe_GET('show_changes', 'no', 'yes')=='yes'; // if not specified, then default to "yes"
	}

	/**
	 * check if this controller should be in print preview mode
	 */
	function isPrintPreview() {
		return $this->view=='preview';
	}
}
?>

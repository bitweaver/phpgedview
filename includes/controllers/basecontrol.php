<?php
/**
 * Base controller for all controller classes
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005	John Finlay and Others
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
 * Page does not validate see line number 1109 -> 15 August 2005
 *
 * @package PhpGedView
 * @subpackage Controllers
 * @version $Id$
 */
class BaseController {
	var $view = "";
	/**
	 * constructor for this class
	 */
	function BaseController() {
		if (isset($_REQUEST["view"])) $this->view = $_REQUEST["view"];
	}
	/**
	 * check if this controller should be in print preview mode
	 */
	function isPrintPreview() {
		if ($this->view=="preview") return true;
	}
}
?>
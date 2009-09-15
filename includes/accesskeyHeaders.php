<?php
/**
 *  Code for access key codes
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
 * @version $Id: accesskeyHeaders.php,v 1.2 2009/09/15 20:06:00 lsces Exp $
 */

if (!defined('PGV_PHPGEDVIEW')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $SEARCH_SPIDER, $pgv_lang, $SCRIPT_NAME;
if (empty($SEARCH_SPIDER)) {
	?>
	<div class="accesskeys">
	<a class="accesskeys" href="#content" title="<?php print $pgv_lang["accesskey_skip_to_content_desc"]; ?>" tabindex="-1" accesskey="<?php print $pgv_lang["accesskey_skip_to_content"]; ?>"><?php print $pgv_lang["accesskey_skip_to_content_desc"]; ?></a>
	<a class="accesskeys" href="javascript://accesskey_viewing_advice_help" onfocus="this.onclick" onclick="return helpPopup('accesskey_viewing_advice_help');" title="<?php print $pgv_lang["accesskey_viewing_advice_desc"]; ?>" accesskey="<?php print $pgv_lang["accesskey_viewing_advice"]; ?>"><?php print $pgv_lang["accesskey_viewing_advice_desc"]; ?></a>
	<a href="javascript://help_<?php print basename($SCRIPT_NAME); ?>" onclick="return helpPopup('help_<?php print basename($SCRIPT_NAME); ?>&amp;action=<?php print $action;?>');" accesskey="<?php print $pgv_lang["accesskey_help_current_page"]; ?>"> </a>
	<a href="javascript://help_contents_help" onclick="return helpPopup('help_contents_help');" accesskey="<?php print $pgv_lang["accesskey_help_content"]; ?>"> </a>
	</div>
	<?php
}

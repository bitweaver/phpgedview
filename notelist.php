<?php
/**
 * Parses gedcom file and displays a list of the shared notes in the file.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2009  PGV Development Team.  All rights reserved.
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
 * @version $Id: notelist.php,v 1.1 2009/04/30 19:12:13 lsces Exp $
 * @package PhpGedView
 * @subpackage Lists
 */

require './config.php';

require_once 'includes/functions/functions_print_lists.php';

print_header($pgv_lang['shared_note_list']);
echo '<div class="center"><h2>'.$pgv_lang['shared_note_list'].'</h2>';
print_note_table(get_note_list(PGV_GED_ID));
echo '</div>';
print_footer();
?>

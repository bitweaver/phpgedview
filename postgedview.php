<?php

// This file has been superseded.  This is now just a redirect to its
// replacement.  If you've sucessfully upgraded to PhpGedView 4.1.5 or
// higher, then you can safely delete this file.
//
// phpGedView: Genealogy Viewer
// Copyright (C) 2008 PhpGedView Development team, all rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License or,
// at your discretion, any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id: postgedview.php,v 1.6 2008/07/07 18:01:12 lsces Exp $

header(
	"Location: modules/cms_interface/cms_login.php?cms_login=".urlencode($_COOKIE['post_user'])."&amp;cms_password=".urlencode($_COOKIE['def_upass'])
);

?>

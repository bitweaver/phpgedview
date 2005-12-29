<?php
/**
 * Module system for adding features to phpGedView.
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
 * @subpackage Display
 * @version $Id: module.php,v 1.1 2005/12/29 18:25:56 lsces Exp $
 * @author Patrick Kellum
 */

require_once 'config.php';

// Simple mod system, based on the older phpnuke/postnuke
define('PGV_MOD_SIMPLE', 1);
// More advanced OO module system
define('PGV_MOD_OO', 2);

if (!isset ($_REQUEST['mod']))
{
	// PGV_MOD_NUKE
	if (isset ($_REQUEST['name']))
	{
		$_REQUEST['mod'] = $_REQUEST['name'];
	}
}
if (file_exists('modules/'.$_REQUEST['mod'].'.php'))
{
	$modinfo = parse_ini_file('modules/'.$_REQUEST['mod'].'.php', true);
}
else
{
	header('Location: index.php');
	print ' ';
	exit;
}
switch ($modinfo['Module']['type'])
{
	case PGV_MOD_SIMPLE:
	{
		if (!isset ($_REQUEST['pgvaction']))
		{
			$_REQUEST['pgvaction'] = 'index';
		}
		if (!file_exists('modules/'.$_REQUEST['mod'].'/'.$_REQUEST['pgvaction'].'.php'))
		{
			$_REQUEST['pgvaction'] = 'index';
		}
		include_once 'modules/'.$_REQUEST['mod'].'/'.$_REQUEST['pgvaction'].'.php';
		break;
	}
	case PGV_MOD_OO:
	{
		if (!isset ($_REQUEST['method']))
		{
			$_REQUEST['method'] = 'main';
		}
		if (!isset ($_REQUEST['class']))
		{
			$_REQUEST['class'] = $_REQUEST['mod'];
		}
		include_once 'modules/'.$_REQUEST['mod'].'/'.$_REQUEST['class'].'.php';
		$mod = new $_REQUEST['mod']();
		if (!method_exists($mod, $_REQUEST['method']))
		{
			$_REQUEST['method'] = 'main';
		}
		$out = $mod->$_REQUEST['method']();
		if (is_string($out))
		{
			print $out;
		}
		break;
	}
	default:
	{
		print 'Error: Unknown module type.';
		break;
	}
}

function mod_print_header($title, $head='', $use_alternate_styles=true)
{
	ob_start();
	print_header($title, $head, $use_alternate_styles);
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

function mod_print_simple_header($title)
{
	ob_start();
	print_simple_header($title);
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

function mod_print_footer()
{
	ob_start();
	print_footer();
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}

function mod_print_simple_footer()
{
	ob_start();
	print_simple_footer();
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}
?>
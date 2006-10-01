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
 * @version $Id: module.php,v 1.2 2006/10/01 22:44:01 lsces Exp $
 * @author Patrick Kellum
 */

require_once 'config.php';

// Simple mod system, based on the older phpnuke/postnuke
define('PGV_MOD_SIMPLE', 1);
// More advanced OO module system
define('PGV_MOD_OO', 2);
// Module system version 2, enhanced security and better output control
define('PGV_MOD_V2', 3);

if(!isset($_REQUEST['mod']))
{
	// PGV_MOD_NUKE
	if (isset ($_REQUEST['name']))
	{
		$_REQUEST['mod'] = $_REQUEST['name'];
	}
}
if(file_exists('modules/'.$_REQUEST['mod'].'.php'))
{
	$modinfo = parse_ini_file('modules/'.$_REQUEST['mod'].'.php', true);
}
// v2 modules
elseif(file_exists("modules/{$_REQUEST['mod']}/pgv_version.php"))
{
	$modinfo = parse_ini_file("modules/{$_REQUEST['mod']}/pgv_version.php", true);
}
else
{
	header('Location: index.php');
	print ' ';
	exit;
}
switch($modinfo['Module']['type'])
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
		$mod = new $_REQUEST['class']();
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
	case PGV_MOD_V2:
	{
		/*
		 * Module Security
		 *	1. Test if module is active.
		 *	2. Only Admins can view an inactive module.
		 */
		if((!isset($modinfo['Config']['active']) || $modinfo['Config']['active'] === false) && !userIsAdmin(getUserName()))
		{
			header("Location: {$SERVER_URL}index.php");print ' ';exit;
		}
		/*
		 * Class Security
		 * 	1. Remove any directories that might have been passed.
		 *	2. Test if class file actually exists.
		 *	3. Ignore any filename that starts with an underscore.
		 */
		if(isset($_REQUEST['class'])){$_REQUEST['class'] = basename($_REQUEST['class'], '.php');}
		if(
			!isset($_REQUEST['class']) ||
			!file_exists("modules/{$_REQUEST['mod']}/{$_REQUEST['class']}.php") ||
			$_REQUEST['class'][0] == '_'
		){$_REQUEST['class'] = $_REQUEST['mod'];}
		/*
		 * Load Language
		 *	1. Load english language if exists.
		 *	2. Load current language if exists.
		 */
		if(file_exists("modules/{$_REQUEST['mod']}/pgvlang/lang_{$modinfo['Module']['default_language']}.php")){include_once "modules/{$_REQUEST['mod']}/pgvlang/lang_{$modinfo['Module']['default_language']}.php";}
		if($deflang != $modinfo['Module']['default_language'] && file_exists("modules/{$_REQUEST['mod']}/pgvlang/lang_{$deflang}.php")){include_once "modules/{$_REQUEST['mod']}/pgvlang/lang_{$deflang}.php";}

		/*
		 * Load & Initialize
		 * 	1. Load the class file.
		 *	2. Create a module object.
		 *	3. Initialize the module if needed.
		 */
		include_once "modules/{$_REQUEST['mod']}/{$_REQUEST['class']}.php";
		$mod = new $_REQUEST['class']();
		if(method_exists($mod, 'init')){$mod->init();}
		/*
		 * Method Security
		 *	1. Test if method actually exists in this object.
		 *	2. Ignore any method that starts with an underscore.
		 */
		if(
			!isset($_REQUEST['method']) ||
			!method_exists($mod, $_REQUEST['method']) ||
			$_REQUEST['method'][0] == '_'
		){$_REQUEST['method'] = 'main';}
		/*
		 * Execute Method
		 *	1. Execute the requested method.
		 *	2. Act upon the result of the method call.
		 */
		$results = $mod->$_REQUEST['method']();
		switch($results[0])
		{
			/*
			 * Action: Display Raw Output
			 *	'content':	Raw content to display on the page.
			 */
			case 'display':
			{
				print $results['content'];
				break;
			}
			/*
			 * Action: Wrap Output In Header & Footer
			 *	'title'		Title of the page. [optional]
			 *	'head'		Additional header content. [optional]
			 *	'content'	Content to display on the page.
			 */
			case 'wrap':
			{
				if(!isset($results['title']))
				{
					if(isset($modinfo['Config']['title'])){$results['title'] = $modinfo['Config']['title'];}
					else{$results['title'] = $GEDCOMS[$GEDCOM]['title'];}
				}
				if(!isset($results['head'])){$results['head'] = '';}
				print_header($results['title'], $results['head']);
				print $results['content'];
				print_footer();
				break;
			}
			/*
			 * Action: Redirect Browser
			 *	'url'		URL to redirect the browser to.
			 */
			case 'redirect':
			{
				// fully qualified url is recomended.
				if(!stristr($results['url'], '://')){$results['url'] = "{$SERVER_URL}{$results['url']}";}
				header("Location: {$results['url']}");
				print ' '; // for some older browsers.
				exit;
			}
			/*
			 * Action: Exit
			 */
			case 'exit':
			{
				exit;
			}
			/*
			 * Action: Error
			 */
			default:
			{
				print_header($results['title'], $results['head']);
				print str_replace('[action]', $results['action'], $pgv_lang['module_error_unknown_action_v2']);
				print_footer();
				exit;
			}
		}
		break;
	}
	default:
	{
		print_header($GEDCOMS[$GEDCOM]['title']);
		print $pgv_lang['module_error_unknown_type'];
		print_footer();
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
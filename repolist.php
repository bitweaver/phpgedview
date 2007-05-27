<?php
/**
 * Repositories List
 *
 * Parses gedcom file and displays a list of the repositories in the file.
 *
 * The alphabet bar shows all the available letters users can click. The bar is built
 * up from the lastnames first letter. Added to this bar is the symbol @, which is
 * shown as a translated version of the variable <var>pgv_lang["NN"]</var>, and a
 * translated version of the word ALL by means of variable <var>$pgv_lang["all"]</var>.
 *
 * The details can be shown in two ways, with surnames or without surnames. By default
 * the user first sees a list of surnames of the chosen letter and by clicking on a
 * surname a list with names of people with that chosen surname is displayed.
 *
 * Beneath the details list is the option to skip the surname list or show it.
 * Depending on the current status of the list.
 *
 * @package PhpGedView
 * @subpackage Lists
 * @version $Id: repolist.php,v 1.4 2007/05/27 14:45:30 lsces Exp $
 */

/**
 * load the main configuration and context
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'phpgedview' );
include_once( PHPGEDVIEW_PKG_PATH.'BitGEDCOM.php' );
$gGedcom = new BitGEDCOM();

// leave manual config until we can move it to bitweaver table 
require("config.php");
require_once("includes/functions_print_lists.php");
$repolist = get_repo_list();               //-- array of regular repository titles 
$addrepolist = get_repo_add_title_list();  //-- array of additional repository titlesadd

$cr = count($repolist);
$ca = count($addrepolist);
$ctot = $cr + $ca;

print_header($pgv_lang["repo_list"]);
print "<div class=\"center\">";
print "<h2>".$pgv_lang["repo_list"]."</h2>\n\t";

print_repo_table(array_merge($repolist, $addrepolist));

print "</div>";
print "<br /><br />";
load_behaviour();
print_footer();
?>
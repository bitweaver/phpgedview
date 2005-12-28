<?php
global $gBitInstaller;

$gBitInstaller->makePackageHomeable('phpgedview');

$gBitInstaller->registerPackageInfo( PHPGEDVIEW_PKG_NAME, array(
	'description' => "phpgedview genealogy data management interface.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '0.1',
	'state' => 'beta',
	'dependencies' => 'html',
) );


// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( PHPGEDVIEW_PKG_NAME, array(
	array('bit_p_view_phpgedview', 'Can view gedcom data', 'registered', 'phpgedview'),
	array('bit_p_edit_phpgedview', 'Can edit gedcom data', 'registered', 'phpgedview'),
	array('bit_p_admin_phpgedview', 'Can admin phpgedview interface', 'editors', 'phpgedview')
) );




?>

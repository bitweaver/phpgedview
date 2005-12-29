<?php
$tables = array(

'pgv_gedcom' => "
	g_id I4 PRIMARY,
	g_content_id I4,
	g_name C(255),
	g_path C(255),
	g_config C(255),
	g_privacy C(255),
	g_commonsurnames X
",

'pgv_individuals' => "
	i_id C(250) PRIMARY,
	i_file I4,
	i_rin C(30),
	i_name C(255),
	i_isdead I1,
	i_GEDCOM X,
	i_letter C(5),
	i_surname C(100)
",

'pgv_families' => "
	f_id C(250) PRIMARY,
	f_file I4,
	f_husb C(255),
	f_wife C(255),
	f_chil X,
	f_GEDCOM X,
	f_numchil I1
",

'pgv_sources' => "
	s_id C(250) PRIMARY,
	s_file I4,
	s_name C(255),
	s_GEDCOM X
",

'pgv_other' => "
	o_id C(250) PRIMARY,
	o_file I4,
	o_type C(20),
	o_GEDCOM X
",

'pgv_names' => "
	n_gid C(250) PRIMARY,
	n_file I4,
	n_type C(10),
	n_letter C(5),
	n_surname C(100),
	n_type C(10)
",

'pgv_dates' => "
	d_day I,
	d_month C(15),
	d_mon I,
	d_year I,
	d_datestamp D,
	d_fact I,
	d_gid C(255),
	d_file I4,
	d_type C(10)
",

'pgv_blocks' => "
	b_id I4 PRIMARY,
	b_username C(100),
	b_location C(30),
	b_order I,
	b_name C(255),
	b_config X
",

'pgv_favorites' => "
	fv_id I4 PRIMARY,
	fv_username C(30),
	fv_gid C(10),
	fv_type C(10),
	fv_file C(100),
	fv_url C(255),
	fv_title C(255),
	fv_note X
",

'pgv_messages' => "
	m_id I4 PRIMARY,
	m_from C(255),
	m_to C(30),
	m_subject C(255),
	m_note X,
	m_created C(20)
",

'pgv_news' => "
	n_id I4 PRIMARY,
	n_username C(100),
	n_date I4,
	n_title C(255),
	n_note X
",

'pgv_places' => "
	p_id I4 PRIMARY,
	p_place C(150),
	p_level I4,
	p_parent_id I4,
	p_file I4
",

'pgv_placelinks' => "
	pl_p_id I4 PRIMARY,
	pl_gid C(30),
	pl_file I4
",

'pgv_users' => "
	u_id I4 PRIMARY,
	u_username C(30),
	u_GEDCOMid X,
	u_rootid X
"

);

global $gBitInstaller;

$gBitInstaller->makePackageHomeable('phpgedview');

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( PHPGEDVIEW_PKG_NAME, $tableName, $tables[$tableName] );
}

$indices = array (
	'pgv_id_idx' => array( 'table' => 'pgv_gallery', 'cols' => 'gallery_id', 'opts' => NULL ),
	'pgv_gallery_content_idx' => array( 'table' => 'pgv_gallery', 'cols' => 'content_id', 'opts' => array( 'UNIQUE' ) ),
	'pgv_image_id_idx' => array( 'table' => 'pgv_image', 'cols' => 'image_id', 'opts' => NULL ),
	'pgv_image_content_idx' => array( 'table' => 'pgv_image', 'cols' => 'content_id', 'opts' => array( 'UNIQUE' ) ),
);
$gBitInstaller->registerSchemaIndexes( PHPGEDVIEW_PKG_NAME, $indices );

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

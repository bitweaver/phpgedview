<?php
$tables = array(

PHPGEDVIEW_DB_PREFIX.'gedcom' => "
	g_id I4 PRIMARY,
	g_content_id I8,
	g_name C(250),
	g_path C(250),
	g_config C(250),
	g_privacy C(250),
	g_commonsurnames X
",

PHPGEDVIEW_DB_PREFIX.'site_setting' => "
	site_setting_name C(32) PRIMARY,
	site_setting_value C(250) NOT NULL
",

PHPGEDVIEW_DB_PREFIX.'individuals' => "
	i_id C(20) PRIMARY,
	i_file I2 PRIMARY,
	i_rin C(250),
	i_isdead I1 DEFAULT 1,
	i_GEDCOM X,
	i_content_id I8
",

PHPGEDVIEW_DB_PREFIX.'families' => "
	f_id C(20) PRIMARY,
	f_file I2 PRIMARY,
	f_husb C(250),
	f_wife C(250),
	f_chil X,
	f_GEDCOM X,
	f_numchil I1,
	f_content_id I8
",

PHPGEDVIEW_DB_PREFIX.'sources' => "
	s_id C(20) PRIMARY,
	s_file I2 PRIMARY,
	s_name C(250),
	s_dbid V(1),
	s_GEDCOM X
",

PHPGEDVIEW_DB_PREFIX.'other' => "
	o_id C(20) PRIMARY,
	o_file I2 PRIMARY,
	o_type C(32),
	o_GEDCOM X
",

PHPGEDVIEW_DB_PREFIX.'link' => "
	l_file I2	 NOT NULL,
	l_from C(20) NOT NULL,
	l_type C(15) NOT NULL,
	l_to C(20)	 NOT NULL
",

PHPGEDVIEW_DB_PREFIX.'name' => "
	n_file I2		PRIMARY,
	n_id C(20)		PRIMARY,
	n_num I4		NOT NULL,
	n_type C(15)	NOT NULL,
	n_sort C(250)	NOT NULL,
	n_full C(250)	NOT NULL,
	n_list C(250),
	n_surname C(250),
	n_surn C(250),
	n_givn C(250),
	n_soundex_givn_std C(250),
	n_soundex_surn_std C(250),
	n_soundex_givn_dm C(250),
	n_soundex_surn_dm C(250)
",

PHPGEDVIEW_DB_PREFIX.'dates' => "
	d_day I			NOT NULL,
	d_month C(5),
	d_mon I			NOT NULL,
	d_year I		NOT NULL,
	d_julianday1 I	NOT NULL,
	d_julianday2 I	NOT NULL,
	d_fact C(10)	NOT NULL,
	d_gid C(20)		NOT NULL,
	d_file I4		NOT NULL,
	d_type C(13)	NOT NULL
",

PHPGEDVIEW_DB_PREFIX.'favorites' => "
	fv_id I4 PRIMARY,
	fv_username C(30),
	fv_gid C(10),
	fv_type C(10),
	fv_file C(100),
	fv_url C(250),
	fv_title C(250),
	fv_note X
",

PHPGEDVIEW_DB_PREFIX.'places' => "
	p_id I4 PRIMARY,
	p_place C(150),
	p_level I4,
	p_parent_id I4,
	p_file I4,
	p_std_soundex X,
	p_dm_soundex X
",

PHPGEDVIEW_DB_PREFIX.'placelinks' => "
	pl_p_id I4,
	pl_gid C(30),
	pl_file I4
",

PHPGEDVIEW_DB_PREFIX.'media' => "
	m_id I4 PRIMARY,
	m_media C(15),
	m_ext C(6),
	m_titl C(250),
	m_file C(250),
	m_gedfile I8,
	m_gedrec X
",

PHPGEDVIEW_DB_PREFIX.'media_mapping' => "
	mm_id I4 PRIMARY,
	mm_media C(32) NOTNULL DEFAULT '',
	mm_gid C(32) NOTNULL DEFAULT '',
	mm_order I4 NOTNULL DEFAULT '0',
	mm_gedfile I4,
	mm_gedrec X
",

PHPGEDVIEW_DB_PREFIX.'remotelinks' => " 
	r_gid		C(20) NOT NULL,
	r_linkid	C(250),
	r_file		I2 NOT NULL
",

PHPGEDVIEW_DB_PREFIX.'temple_code' => "
	tc_id I4 PRIMARY AUTO,
	tc_name C(5),
	tc_title C(100),
	tc_memo X
",

PHPGEDVIEW_DB_PREFIX.'status_code' => "
	sc_id I4 PRIMARY AUTO,
	sc_name C(20),
	sc_title C(100)
",

PHPGEDVIEW_DB_PREFIX.'nextid' => " 
	ni_ID		I4 NOTNULL,
	ni_type		C(30) PRIMARY,
	ni_gedfile	I4 PRIMARY
",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( PHPGEDVIEW_PKG_NAME, $tableName, $tables[$tableName] );
}

// these sequences are automatically generated, but Firebird and MSSQL prefers they exist
// Starting the numbering off at 5 for types to allow room for the INSERTs later.
$sequences = array (
	'gedview_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( PHPGEDVIEW_PKG_NAME, $sequences );

$indices = array (
	'indi_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'individuals', 'cols' => 'i_file', 'opts' => NULL ),
	'fam_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'families', 'cols' => 'f_file', 'opts' => NULL ),
	'fam_husb_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'families', 'cols' => 'f_husb', 'opts' => NULL ),
	'fam_wife_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'families', 'cols' => 'f_wife', 'opts' => NULL ),
	'sour_name_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'sources', 'cols' => 's_name', 'opts' => NULL ),
	'sour_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'sources', 'cols' => 's_file', 'opts' => NULL ),
	'sour_dbid_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'sources', 'cols' => 's_dbid', 'opts' => NULL ),
	'other_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'other', 'cols' => 'o_file', 'opts' => NULL ),
	'place_place_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_place', 'opts' => NULL ),
	'place_level_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_level', 'opts' => NULL ),
	'place_parent_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_parent_id', 'opts' => NULL ),
	'place_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_file', 'opts' => NULL ),
	'plindex_p_id_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'placelinks', 'cols' => 'pl_p_id', 'opts' => NULL ),
	'plindex_gid_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'placelinks', 'cols' => 'pl_gid', 'opts' => NULL ),
	'plindex_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'placelinks', 'cols' => 'pl_file', 'opts' => NULL ),
	'm_media_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'media', 'cols' => 'm_media', 'opts' => NULL ),
	'm_media_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'media', 'cols' => 'm_media, m_gedfile', 'opts' => NULL ),
	'mm_media_id_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'media_mapping', 'cols' => 'mm_media, mm_gedfile', 'opts' => NULL ),
	'mm_media_gid_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'media_mapping', 'cols' => 'mm_gid, mm_gedfile', 'opts' => NULL ),
	'mm_media_gedfile_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'media_mapping', 'cols' => 'mm_gedfile', 'opts' => NULL ),
	'place_place_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_place', 'opts' => NULL ),
	'place_level_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_level', 'opts' => NULL ),
	'place_parent_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_parent_id', 'opts' => NULL ),
	'place_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'places', 'cols' => 'p_file', 'opts' => NULL ),
	'plindex_p_id_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'placelinks', 'cols' => 'pl_p_id', 'opts' => NULL ),
	'plindex_gid_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'placelinks', 'cols' => 'pl_gid', 'opts' => NULL ),
	'plindex_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'placelinks', 'cols' => 'pl_file', 'opts' => NULL ),
	'name_full_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'name', 'cols' => 'n_full', 'opts' => NULL ),
	'name_type_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'name', 'cols' => 'n_type', 'opts' => NULL ),
	'name_surn_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'name', 'cols' => 'n_surname', 'opts' => NULL ),
	'name_sort_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'name', 'cols' => 'n_sort', 'opts' => NULL ),
	'name_list_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'name', 'cols' => 'n_list', 'opts' => NULL ),
	'date_day_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_day', 'opts' => NULL ),
	'date_month_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_month', 'opts' => NULL ),
	'date_mon_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_mon', 'opts' => NULL ),
	'date_year_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_year', 'opts' => NULL ),
	'date_julianday1_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_julianday1', 'opts' => NULL ),
	'date_julianday2_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_julianday2', 'opts' => NULL ),
	'date_fact_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_fact', 'opts' => NULL ),
	'date_gid_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_gid', 'opts' => NULL ),
	'date_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_file', 'opts' => NULL ),
	'date_type_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'dates', 'cols' => 'd_type', 'opts' => NULL ),
	'r_gid_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'remotelinks', 'cols' => 'r_gid', 'opts' => NULL ),
	'r_link_id_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'remotelinks', 'cols' => 'r_linkid', 'opts' => NULL ),
	'r_file_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'remotelinks', 'cols' => 'r_file', 'opts' => NULL ),
	'tc_name_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'temple_code', 'cols' => 'tc_name', 'opts' => array( 'UNIQUE' ) ),
	'link_uxl_idx' => array( 'table' => PHPGEDVIEW_DB_PREFIX.'link', 'cols' => 'l_to, l_file, l_type, l_from', 'opts' => array( 'UNIQUE' ) ),
);
$gBitInstaller->registerSchemaIndexes( PHPGEDVIEW_PKG_NAME, $indices );

$gBitInstaller->registerPackageInfo( PHPGEDVIEW_PKG_NAME, array(
	'description' => "phpgedview genealogy data management interface.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( PHPGEDVIEW_PKG_NAME, array(
	array(PHPGEDVIEW_PKG_NAME,'pgv_session_time','7200'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_calendar_format','gregorian'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_default_pedigree_generations','4'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_max_pedigree_generations','10'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_max_descendancy_generations','15'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_use_RIN','n'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_pedigree_root_id','I1'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_gedcom_prefix_id','I'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_source_prefix_id','S'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_repo_prefix_id','R'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_fam_prefix_id','F'),
	array(PHPGEDVIEW_PKG_NAME,'pgv_media_prefix_id','M'),
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( PHPGEDVIEW_PKG_NAME, array(
	array('p_phpgedview_view', 'Can view gedcom data', 'registered', 'phpgedview'),
	array('p_phpgedview_edit', 'Can edit gedcom data', 'registered', 'phpgedview'),
	array('p_phpgedview_admin', 'Can admin phpgedview interface', 'editors', 'phpgedview')
) );

$gBitInstaller->registerSchemaDefault( PHPGEDVIEW_PKG_NAME, array(
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ABA', 'Aba, Nigeria')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ACCRA', 'Accra, Ghana')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ADELA', 'Adelaide, Australia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ALBUQ', 'Albuquerque, New Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ANCHO', 'Anchorage, Alaska')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SAMOA', 'Apia, Samoa')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ASUNC', 'Asuncion, Paraguay')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ATLAN', 'Atlanta, Georgia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SWISS', 'Bern, Switzerland')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BOGOT', 'Bogota, Columbia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BILLI', 'Billings, Montana')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BIRMI', 'Birmingham, Alabama')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BISMA', 'Bismarck, North Dakota')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BOISE', 'Boise, Idaho')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BOSTO', 'Boston, Massachusetts')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BOUNT', 'Bountiful, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BRISB', 'Brisbane, Australia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BROUG', 'Baton Rouge, Louisiana')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('BAIRE', 'Buenos Aires, Argentina')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('CAMPI', 'Campinas, Brazil')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('CARAC', 'Caracas, Venezuela')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ALBER', 'Cardston, Alberta, Canada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('CHICA', 'Chicago, Illinois')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('CIUJU', 'Ciudad Juarez, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('COCHA', 'Cochabamba, Bolivia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('COLJU', 'Colonia Juarez, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('COLSC', 'Columbia, South Carolina')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('COLUM', 'Columbus, Ohio')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('COPEN', 'Copenhagen, Denmark')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('CRIVE', 'Columbia River, Washington')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('DALLA', 'Dallas, Texas')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('DENVE', 'Denver, Colorado')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('DETRO', 'Detroit, Michigan')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('EDMON', 'Edmonton, Alberta, Canada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('EHOUS', 'ENDOWMENT HOUSE')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('FRANK', 'Frankfurt am Main, Germany')",  // There's also a Frankfurt an der Oder in Germany
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('FREIB', 'Freiburg, Germany')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('FRESN', 'Fresno, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('FUKUO', 'Fukuoka, Japan')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('GUADA', 'Guadalajara, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('GUATE', 'Guatemala City, Guatemala')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('GUAYA', 'Guayaquil, Ecuador')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HAGUE', 'The Hague, Netherlands')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HALIF', 'Halifax, Nova Scotia, Canada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NZEAL', 'Hamilton, New Zealand')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HARTF', 'Hartford, Connecticut')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HELSI', 'Helsinki, Finland')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HERMO', 'Hermosillo, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HKONG', 'Hong Kong')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HOUST', 'Houston, Texas')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('IFALL', 'Idaho Falls, Idaho')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('JOHAN', 'Johannesburg, South Africa')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('JRIVE', 'Jordan River, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('KIEV', 'Kiev, Ukraine')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('KONA', 'Kona, Hawaii')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('HAWAI', 'Laie, Hawaii')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LVEGA', 'Las Vegas, Nevada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LIMA', 'Lima, Peru')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LOGAN', 'Logan, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LONDO', 'London, England')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LANGE', 'Los Angeles, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LOUIS', 'Louisville, Kentucky')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('LUBBO', 'Lubbock, Texas')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MADRI', 'Madrid, Spain')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MANIL', 'Manila, Philippines')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MANTI', 'Manti, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MEDFO', 'Medford, Oregon')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MELBO', 'Melbourne, Australia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MEMPH', 'Memphis, Tennessee')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MERID', 'Merida, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ARIZO', 'Mesa, Arizona')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MEXIC', 'Mexico City, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MONTE', 'Monterrey, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MNTVD', 'Montevideo, Uruguay')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MONTI', 'Monticello, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MONTR', 'Montreal, Quebec, Canada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('MTIMP', 'Mt. Timpanogos, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NASHV', 'Nashville, Tennessee')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NAUV2', 'Nauvoo, Illinois (new)')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NAUVO', 'Nauvoo, Illinois (original)')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NBEAC', 'Newport Beach, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NYORK', 'New York, New York')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('NUKUA', 'Nuku''Alofa, Tonga')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('OAKLA', 'Oakland, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('OAXAC', 'Oaxaca, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('OGDEN', 'Ogden, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('OKLAH', 'Oklahoma City, Oklahoma')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('ORLAN', 'Orlando, Florida')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PALEG', 'Porto Alegre, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PALMY', 'Palmyra, New York')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PAPEE', 'Papeete, Tahiti')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PERTH', 'Perth, Australia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PORTL', 'Portland, Oregon')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('POFFI', 'PRESIDENT''S OFFICE')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PREST', 'Preston, England')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('PROVO', 'Provo, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('RALEI', 'Raleigh, North Carolina')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('RECIF', 'Recife, Brazil')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('REDLA', 'Redlands, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('REGIN', 'Regina, Saskatchewan, Canada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('RENO', 'Reno, Nevada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SACRA', 'Sacramento, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SLAKE', 'Salt Lake City, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SANTO', 'San Antonio, Texas')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SDIEG', 'San Diego, California')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SJOSE', 'San Jose, Costa Rica')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SANTI', 'Santiago, Chile')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SDOMI', 'Santo Domingo, Dom. Rep.')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SPAUL', 'Sao Paulo, Brazil')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SEATT', 'Seattle, Washington')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SEOUL', 'Seoul, Korea')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SNOWF', 'Snowflake, Arizona')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SPOKA', 'Spokane, Washington')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SGEOR', 'St. George, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SLOUI', 'St. Louis, Missouri')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SPMIN', 'St. Paul, Minnesota')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('STOCK', 'Stockholm, Sweden')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SUVA', 'Suva, Fiji')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('SYDNE', 'Sydney, Australia')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('TAIPE', 'Taipei, Taiwan')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('TAMPI', 'Tampico, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('TOKYO', 'Tokyo, Japan')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('TORNO', 'Toronto, Ontario, Canada')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('TGUTI', 'Tuxtla Gutierrez, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('VERAC', 'Veracruz, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('VERNA', 'Vernal, Utah')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('VILLA', 'Villa Hermosa, Mexico')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('WASHI', 'Washington, DC')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."temple_code`(`tc_name`, `tc_title`) VALUES ('WINTE', 'Winter Quarters, Nebraska')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('CHILD', 'Died as a child: exempt')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('INFANT', 'Died as an infant: exempt')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('STILLBORN', 'Stillborn: exempt')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('BIC', 'Born in the covenant')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('SUBMITTED', 'Submitted but not yet cleared')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('UNCLEARED', 'Uncleared: insufficient data')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('CLEARED', 'Cleared but not yet completed')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('COMPLETED', 'Completed; date unknown')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('PRE-1970', 'Completed before 1970; date not available')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('CANCELLED', 'Sealing cancelled (divorce)')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('DNS', 'Do Not Seal: unauthorized')",
"INSERT INTO `".PHPGEDVIEW_DB_PREFIX."status_code`(`sc_name`, `sc_title`) VALUES ('DNS/CAN', 'Do Not Seal, previous sealing cancelled')",
) );

/* Hold until RSS completed
if( defined( 'RSS_PKG_NAME' )) {
	$gBitInstaller->registerPreferences( RSS_PKG_NAME, array(
		array( RSS_PKG_NAME, PHPGEDVIEW_PKG_NAME.'_rss', 'y'),
	));
}
*/
?>

<?php
/**
 * see http://unstats.un.org/unsd/methods/m49/m49alpha.htm
 * see http://www.foreignword.com/countries/  for a comprehensive list, with translations
 * see http://susning.nu/Landskod  (list #7) for another list, taken from ISO standards
 * see http://helpdesk.rootsweb.com/codes for a comprehensive list of Chapman codes.
 * see http://www.rootsweb.com/~wlsgfhs/ChapmanCodes.htm for another list of Chapman codes
 *
 * The list that follows is the list of Chapman country codes, with additions from the
 * other sources mentioned above.
 *
 * These codes do not appear in the two Chapman lists cited:
 *		ALA		Åland Islands
 *		COD		Congo (Brazzaville)		This country was known as Zaire
 *		NFK		Norfolk Island
 *		PRI		Puerto Rico				Chapman lists this as a state of the USA
 *		SCG		Serbia and Montenegro	Chapman lists these separately
 *		TLS		Timor-Leste
 *		UMI		US Minor Outlying Islands
 *		VIR		US Virgin Islands		Chapman lists this as a state of the USA
 *		
 * These Chapman country codes do not appear in the list following:
 *		UEL		United Empire Loyalist		This is NOT a country or region, it's
 *											a group of people
 *		UK		United Kingdom				This is the only two-letter country code,
 *											and GBR or one of its components should be
 *											used instead.
 *		SLK		Slovakia					This code, listed in the last source cited,
 *											should be SVK
 *		SLO		Slovenia					This code, listed in the last source cited,
 *											should be SVN
 *		SAM		South America				This code, listed in the last source cited,
 *											is not precise enough
 *		TMP		East Timor					Official name is TLS "Timor-Leste"
 *		HOL		Holland						Official name is NLD "Netherlands"
 *		ESM		Western Samoa				Official name is WSM "Samoa"
 *											
 * @package PhpGedView
 * @subpackage Languages
 */
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "A nyelvi fájl közvetlenül nem érhető el.";
	exit;
}
$countries["ABW"]="Aruba";
	$countries["ACA"]="Acadia";
$countries["AFG"]="Afganisztán";
$countries["AGO"]="Angola";
$countries["AIA"]="Anguilla";
	$countries["ALA"]="Åland-szigetek";
$countries["ALB"]="Albánia";
$countries["AND"]="Andorra";
$countries["ANT"]="Holland Antillák";
$countries["ARE"]="Egyesült Arab Emirátusok";
$countries["ARG"]="Argentína";
$countries["ARM"]="Örményország";
$countries["ASM"]="Amerikai Szamoa";
$countries["ATA"]="Antarktisz";
$countries["ATF"]="Francia Déli Területek";
$countries["ATG"]="Antigua és Barbuda";
$countries["AUS"]="Ausztrália";
$countries["AUT"]="Ausztria";
	$countries["AZR"]="Azores";
$countries["AZE"]="Azerbajdzsán";
$countries["BDI"]="Burundi";
$countries["BEL"]="Belgium";
$countries["BEN"]="Benin";
$countries["BFA"]="Burkina Faso";
$countries["BGD"]="Banglades";
$countries["BGR"]="Bulgária";
$countries["BHR"]="Bahrein";
$countries["BHS"]="Bahama-szigetek";
$countries["BIH"]="Bosznia-Hercegovina";
$countries["BLR"]="Belorusszia";
$countries["BLZ"]="Belize";
$countries["BMU"]="Bermuda";
$countries["BOL"]="Bolívia";
$countries["BRA"]="Brazília";
$countries["BRB"]="Barbados";
$countries["BRN"]="Brunei Darussalam";
$countries["BTN"]="Bhután";
$countries["BVT"]="Bouvet-sziget";
$countries["BWA"]="Botswana";
	$countries["BWI"]="British West Indies";
$countries["CAF"]="Közép-Afrikai Köztársaság";
$countries["CAN"]="Kanada";
	$countries["CAP"]="Cape Colony";
$countries["CCK"]="Kókusz-szigetek (Keeling-szigetek)";
$countries["CHE"]="Svájc";
	$countries["CHI"]="Channel Islands";
$countries["CHL"]="Chile";
$countries["CHN"]="Kína";
$countries["CIV"]="Côte d'Ivoire (Elefántcsontpart)";
$countries["CMR"]="Kamerun";
$countries["COD"]="Kongói Demokratikus Köztársaság";
$countries["COG"]="Kongó (Brazzaville)";
$countries["COK"]="Cook-szigetek";
$countries["COL"]="Kolumbia";
$countries["COM"]="Comore-szigetek";
$countries["CPV"]="Zöld-foki-szigetek";
$countries["CRI"]="Costa Rica";
$countries["CSK"]="Csehszlovákia";
$countries["CUB"]="Kuba";
$countries["CXR"]="Karácsony-sziget";
$countries["CYM"]="Kajmán-szigetek";
$countries["CYP"]="Ciprus";
$countries["CZE"]="Cseh Köztársaság";
$countries["DEU"]="Németország";
$countries["DJI"]="Dzsibuti";
$countries["DMA"]="Dominika";
$countries["DNK"]="Dánia";
$countries["DOM"]="Dominikai Köztársaság";
$countries["DZA"]="Algéria";
$countries["ECU"]="Ecuador";
$countries["EGY"]="Egyiptom";
	$countries["EIR"]="Eire";
$countries["ENG"]="Anglia";
$countries["ERI"]="Eritrea";
$countries["ESH"]="Nyugat Szahara";
$countries["ESP"]="Spanyolország";
$countries["EST"]="Észtország";
$countries["ETH"]="Etiópia";
$countries["FIN"]="Finnország";
$countries["FJI"]="Fidzsi";
	$countries["FLD"]="Flanders";
$countries["FLK"]="Falkland-szigetek (Malvin-szigetek)";
$countries["FRA"]="Franciaország";
$countries["FRO"]="Feröer-szigetek";
$countries["FSM"]="Mikronéziai Szövetségi Államok";
$countries["GAB"]="Gabon";
$countries["GBR"]="Egyesült Királyság";
$countries["GEO"]="Georgia";
$countries["GHA"]="Ghána";
$countries["GIB"]="Gibraltár";
$countries["GIN"]="Guinea";
$countries["GLP"]="Guadeloupe";
$countries["GMB"]="Gambia";
$countries["GNB"]="Bissau-Guinea";
$countries["GNQ"]="Egyenlítõi Guinea";
$countries["GRC"]="Görögország";
$countries["GRD"]="Grenada";
$countries["GRL"]="Grönland";
$countries["GTM"]="Guatemala";
$countries["GUF"]="Francia Guyana";
$countries["GUM"]="Guam";
$countries["GUY"]="Guyana";
$countries["HKG"]="Hongkong (k.k.t.)";
$countries["HMD"]="Heard- és McDonald-szigetek";
$countries["HND"]="Honduras";
$countries["HRV"]="Horvátország";
$countries["HTI"]="Haiti";
$countries["HUN"]="Magyarország";
$countries["IDN"]="Indonézia";
$countries["IND"]="India";
$countries["IOT"]="Brit Indiai-óceáni Terület";
$countries["IRL"]="Írország";
$countries["IRN"]="Irán (Iszlám Köztársaság)";
$countries["IRQ"]="Irak";
$countries["ISL"]="Izland";
$countries["ISR"]="Izrael";
$countries["ITA"]="Olaszország";
$countries["JAM"]="Jamaica";
$countries["JOR"]="Jordánia";
$countries["JPN"]="Japán";
$countries["KAZ"]="Kazahsztán";
$countries["KEN"]="Kenya";
$countries["KGZ"]="Kirgizisztán";
$countries["KHM"]="Kambodzsa";
$countries["KIR"]="Kiribati";
$countries["KNA"]="Saint Kitts és Nevis";
$countries["KOR"]="Koreai Köztársaság";
$countries["KWT"]="Kuvait";
$countries["LAO"]="Lao Népi Demokratikus Köztársaság";
$countries["LBN"]="Libanon";
$countries["LBR"]="Libéria";
$countries["LBY"]="Líbia";
$countries["LCA"]="Szent Lucia";
$countries["LIE"]="Liechtenstein";
$countries["LKA"]="Srí Lanka";
$countries["LSO"]="Lesotho";
$countries["LTU"]="Észtország";
$countries["LUX"]="Luxemburg";
$countries["LVA"]="Litvánia";
$countries["MAC"]="Makaó (k.k.t.)";
$countries["MAR"]="Marokkó";
$countries["MCO"]="Monaco";
$countries["MDA"]="Moldva";
$countries["MDG"]="Madagaszkár";
$countries["MDV"]="Moldva";
$countries["MEX"]="Mexikó";
$countries["MHL"]="Marshall-szigetek";
$countries["MKD"]="Macedónia (volt jugoszláv köztársaság)";
$countries["MLI"]="Mali";
$countries["MLT"]="Málta";
$countries["MMR"]="Myanmar";
$countries["MNG"]="Mongólia";
$countries["MNP"]="Észak-Mariana-szigetek";
	$countries["MNT"]="Montenegro";
$countries["MOZ"]="Mozambik";
$countries["MRT"]="Mauritánia";
$countries["MSR"]="Montserrat";
$countries["MTQ"]="Martinique";
$countries["MUS"]="Mauritius";
$countries["MWI"]="Malawi";
$countries["MYS"]="Malajzia";
$countries["MYT"]="Mayotte";
$countries["NAM"]="Namíbia";
$countries["NCL"]="Új-Kaledónia";
$countries["NER"]="Niger";
$countries["NFK"]="Norfolk-sziget";
$countries["NGA"]="Nigéria";
$countries["NIC"]="Nicaragua";
$countries["NIR"]="Észak Írország";
$countries["NIU"]="Niue";
$countries["NLD"]="Hollandia";
$countries["NOR"]="Norvégia";
$countries["NPL"]="Nepál";
$countries["NRU"]="Nauru";
$countries["NTZ"]="Semleges Zona";
$countries["NZL"]="Új-Zéland";
$countries["OMN"]="Omán";
$countries["PAK"]="Pakisztán";
$countries["PAN"]="Panama";
$countries["PCN"]="Pitcairn";
$countries["PER"]="Peru";
$countries["PHL"]="Fülöp-szigetek";
$countries["PLW"]="Palau";
$countries["PNG"]="Pápua Új-Guinea";
$countries["POL"]="Lengyelország";
$countries["PRI"]="Puerto Rico";
$countries["PRK"]="Koreai Népi Demokratikus Köztársaság";
$countries["PRT"]="Portugália";
$countries["PRY"]="Paraguay";
	$countries["PSE"]="Occupied Palestinian Territory";
$countries["PYF"]="Francia Polinézia";
$countries["QAT"]="Katar";
$countries["REU"]="Reunion";
$countries["ROM"]="Románia";
$countries["RUS"]="Orosz Föderáció";
$countries["RWA"]="Ruanda";
$countries["SAU"]="Szaúd-Arábia";
	$countries["SCG"]="Serbia and Montenegro";
$countries["SCT"]="Skócia";
$countries["SDN"]="Szudán";
$countries["SEA"]="Tengeren";
$countries["SEN"]="Szenegál";
$countries["SER"]="Szerbia";
$countries["SGP"]="Szingapúr";
$countries["SGS"]="Dél-Georgia és a Dél-Sandwich-szigetek";
$countries["SHN"]="Szent Ilona";
$countries["SIC"]="Sicilia";
$countries["SJM"]="Svalbard- és Jan Mayen-szigetek";
$countries["SLB"]="Salamon-szigetek";
$countries["SLE"]="Sierra Leone";
$countries["SLV"]="El Salvador";
$countries["SMR"]="San Marino";
$countries["SOM"]="Szomália";
$countries["SPM"]="St. Pierre és Miquelon";
$countries["STP"]="Sao Tome és Principe";
$countries["SUN"]="U.S.S.R. (Soviet Union)";
$countries["SUR"]="Suriname";
$countries["SVK"]="Szlovákia (Szlovák Köztársaság)";
$countries["SVN"]="Szlovénia";
$countries["SWE"]="Svédország";
$countries["SWZ"]="Szváziföld";
$countries["SYC"]="Seychelle-szigetek";
$countries["SYR"]="Szíriai Arab Köztársaság";
$countries["TCA"]="Turks- és Caicos-szigetek";
$countries["TCD"]="Csád";
$countries["TGO"]="Togo";
$countries["THA"]="Thaiföld";
$countries["TJK"]="Tádzsikisztán";
$countries["TKL"]="Tokelau";
$countries["TKM"]="Türkmenisztán";
$countries["TLS"]="Kelet-Timor";
$countries["TON"]="Tonga";
	$countries["TRN"]="Transylvania";
$countries["TTO"]="Trinidad és Tobago";
$countries["TUN"]="Tunézia";
$countries["TUR"]="Törökország";
$countries["TUV"]="Tuvalu";
$countries["TWN"]="Tajvan";
$countries["TZA"]="Tanzániai Egyesült Köztársaság";
$countries["UGA"]="Uganda";
$countries["UKR"]="Ukrajna";
$countries["UMI"]="Egyesült Államok külsõ szigetei";
$countries["URY"]="Uruguay";
$countries["USA"]="Egyesült Államok";
$countries["UZB"]="Üzbegisztán";
$countries["VAT"]="Vatikán";
$countries["VCT"]="Saint Vincent és Grenadines";
$countries["VEN"]="Venezuela";
$countries["VGB"]="Brit Virgin-szigetek";
$countries["VIR"]="Amerikai Virgin-szigetek";
$countries["VNM"]="Vietnám";
$countries["VUT"]="Vanuatu";
$countries["WAF"]="Nyugat Africa";
$countries["WLF"]="Wallis és Futuna";
	$countries["WLS"]="Wales";
$countries["WSM"]="Szamoa";
$countries["YEM"]="Jemen";
$countries["YUG"]="Jugoszlávia";
$countries["ZAF"]="Dél-Afrika";
	$countries["ZAR"]="Zaire";
$countries["ZMB"]="Zambia";
$countries["ZWE"]="Zimbabwe";

?>
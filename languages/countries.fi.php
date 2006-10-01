<?php

/**
 * see http://unstats.un.org/unsd/methods/m49/m49alpha.htm
 * see http://www.foreignword.com/countries/  for a comprehensive list, with translations
 * see http://susning.nu/Landskod  (list #7) for another list, taken from ISO standards
 * see http://helpdesk.rootsweb.com/codes for a comprehensive list of Chapman codes.
 * see http://www.rootsweb.com/~wlsgfhs/ChapmanCodes.htm for another list of Chapman codes
 * see http://www.oph.fi/info/rahoitus/perus02k/forms/maat02.html
 *
 * The list that follows is the list of Chapman country codes, with additions from the
 * other sources mentioned above.
 *
 * These codes do not appear in the two Chapman lists cited:
 *		ALA		Åland Islands
 *		COD		Congo (Brazzaville)		This country was known as Zaire
 *		NFK		Norfolk Island
 *		PRI		Puerto Rico		Chapman lists this as a state of the USA
 *		SCG		Serbia and Montenegro	Chapman lists these separately
 *		TLS		Timor-Leste
 *		UMI		US Minor Outlying Islands
 *		VIR		US Virgin Islands		Chapman lists this as a state of the USA
 *
 * These Chapman country codes do not appear in the list following:
 *		UEL		United Empire Loyalist	This is NOT a country or region, it's
 *							a group of people
 *		UK		United Kingdom		This is the only two-letter country code,
 *							and GBR or one of its components should be
 *							used instead.
 *		SLK		Slovakia			This code, listed in the last source cited,
 *							should be SVK
 *		SLO		Slovenia			This code, listed in the last source cited,
 *							should be SVN
 *		SAM		South America		This code, listed in the last source cited,
 *							is not precise enough
 *
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
*/

if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
    print "You cannot access a language file directly.";
    exit;
}
$countries["ABW"]="Aruba";
$countries["ACA"]="Acadia";
$countries["AFG"]="Afganistan";
$countries["AGO"]="Angola";
$countries["AIA"]="Anguilla";
$countries["ALA"]="Ahvenanmaa";
$countries["ALB"]="Albania";
$countries["AND"]="Andorra";
$countries["ANT"]="Alankomaiden Antillit";
$countries["ARE"]="Arabiemiirikunnat";
$countries["ARG"]="Argentiina";
$countries["ARM"]="Armenia";
$countries["ASM"]="Amerikan Samoa";
$countries["ATA"]="Antarktis";
$countries["ATF"]="Ranskan eteläiset alueet";
$countries["ATG"]="Antigua ja Barbuda";
$countries["AUS"]="Australia";
$countries["AUT"]="Itävalta";
$countries["AZR"]="Azorit";
$countries["AZE"]="Azerbaidzan";
$countries["BDI"]="Burundi";
$countries["BEL"]="Belgia";
$countries["BEN"]="Benin";
$countries["BFA"]="Burkina";
$countries["BGD"]="Bangladesh";
$countries["BGR"]="Bulgaria";
$countries["BHR"]="Bahrain";
$countries["BHS"]="Bahama";
$countries["BIH"]="Bosnia ja Hertsegovina";
$countries["BLR"]="Valko-Venäjä";
$countries["BLZ"]="Belize";
$countries["BMU"]="Bermuda";
$countries["BOL"]="Bolivia";
$countries["BRA"]="Brasilia";
$countries["BRB"]="Barbados";
$countries["BRN"]="Brunei Darussalam";
$countries["BTN"]="Bhutan";
$countries["BVT"]="Bouvet'n saari";
$countries["BWA"]="Botswana";
$countries["BWI"]="Brittiläinen Länsi-Intia";
$countries["CAF"]="Keski-Afrikka";
$countries["CAN"]="Kanada";
$countries["CAP"]="Kapkolonia";
$countries["CCK"]="Kookossaaret";
$countries["CHE"]="Sveitsi";
$countries["CHI"]="Kanaalisaaret";
$countries["CHL"]="Chile";
$countries["CHN"]="Kiina";
$countries["CIV"]="Norsunluurannikko";
$countries["CMR"]="Kamerun";
$countries["COD"]="Kongon demokraattinen tasavalta";
$countries["COG"]="Kongo";
$countries["COK"]="Cookinsaaret";
$countries["COL"]="Kolumbia";
$countries["COM"]="Komorit";
$countries["CPV"]="Kap Verde";
$countries["CRI"]="Costa Rica";
$countries["CSK"]="Tšekkoslovakia";
$countries["CUB"]="Kuuba";
$countries["CXR"]="Joulusaari";
$countries["CYM"]="Caymansaaret";
$countries["CYP"]="Kypros";
$countries["CZE"]="Tshekki";
$countries["DEU"]="Saksa";
$countries["DJI"]="Djibouti";
$countries["DMA"]="Dominica";
$countries["DNK"]="Tanska";
$countries["DOM"]="Dominikaaninen tasavalta";
$countries["DZA"]="Algeria";
$countries["ECU"]="Ecuador";
$countries["EGY"]="Egypti";
$countries["EIR"]="Irlanti (Eire)";
$countries["ENG"]="Englanti";
$countries["ERI"]="Eritrea";
$countries["ESH"]="Länsi-Sahara";
//$countries["ESM"]="Länsi-Samoa";
$countries["ESP"]="Espanja";
$countries["EST"]="Viro";
$countries["ETH"]="Etiopia";
$countries["FIN"]="Suomi";
$countries["FJI"]="Fidzi";
$countries["FLD"]="Flanders";
$countries["FLK"]="Falklandinsaaret";
$countries["FRA"]="Ranska";
$countries["FRO"]="Färsaaret";
$countries["FSM"]="Mikronesian liittovaltio";
$countries["GAB"]="Gabon";
$countries["GBR"]="Yhdistynyt kuningaskunta";
$countries["GEO"]="Georgia";
$countries["GHA"]="Ghana";
$countries["GIB"]="Gibraltar";
$countries["GIN"]="Guinea";
$countries["GLP"]="Guadeloupe";
$countries["GMB"]="Gambia";
$countries["GNB"]="Guinea-Bissau";
$countries["GNQ"]="Päiväntasaajan Guinea";
$countries["GRC"]="Kreikka";
$countries["GRD"]="Grenada";
$countries["GRL"]="Grönlanti";
$countries["GTM"]="Guatemala";
$countries["GUF"]="Ranskan Guyana";
$countries["GUM"]="Guam";
$countries["GUY"]="Guyana";
$countries["HKG"]="Hongkong";
$countries["HMD"]="Heard ja McDonaldsaaret";
$countries["HND"]="Honduras";
//$countries["HOL"]="Hollanti";
$countries["HRV"]="Kroatia";
$countries["HTI"]="Haiti";
$countries["HUN"]="Unkari";
$countries["IDN"]="Indonesia";
$countries["IND"]="Intia";
$countries["IOT"]="Brittiläinen Intian valtameren alue";
$countries["IRL"]="Irlanti";
$countries["IRN"]="Iran";
$countries["IRQ"]="Irak";
$countries["ISL"]="Islanti";
$countries["ISR"]="Israel";
$countries["ITA"]="Italia";
$countries["JAM"]="Jamaika";
$countries["JOR"]="Jordania";
$countries["JPN"]="Japani";
$countries["KAZ"]="Kazakstan";
$countries["KEN"]="Kenia";
$countries["KGZ"]="Kirgisia";
$countries["KHM"]="Kamputsea";
$countries["KIR"]="Kiribati";
$countries["KNA"]="Saint Kitts ja Nevis";
$countries["KOR"]="Korean tasavalta";
$countries["KWT"]="Kuwait";
$countries["LAO"]="Laos";
$countries["LBN"]="Libanon";
$countries["LBR"]="Liberia";
$countries["LBY"]="Libya";
$countries["LCA"]="Saint Lucia";
$countries["LIE"]="Liechtenstein";
$countries["LKA"]="Sri Lanka";
$countries["LSO"]="Lesotho";
$countries["LTU"]="Liettua";
$countries["LUX"]="Luxemburg";
$countries["LVA"]="Latvia";
$countries["MAC"]="Macao";
$countries["MAR"]="Marokko";
$countries["MCO"]="Monaco";
$countries["MDA"]="Moldova";
$countries["MDG"]="Madagaskar";
$countries["MDV"]="Malediivit";
$countries["MEX"]="Meksiko";
$countries["MHL"]="Marshallinsaaret";
$countries["MKD"]="Makedonia";
$countries["MLI"]="Mali";
$countries["MLT"]="Malta";
$countries["MMR"]="Burma";
$countries["MNG"]="Mongolia";
$countries["MNP"]="Pohjois-Mariaanit";
$countries["MNT"]="Montenegro";
$countries["MOZ"]="Mosambik";
$countries["MRT"]="Mauritania";
$countries["MSR"]="Montserrat";
$countries["MTQ"]="Martinique";
$countries["MUS"]="Mauritania";
$countries["MWI"]="Malawi";
$countries["MYS"]="Malesia";
$countries["MYT"]="Mayotte";
$countries["NAM"]="Namibia";
$countries["NCL"]="Uusi-Kaledonia";
$countries["NER"]="Niger";
$countries["NFK"]="Norfolkinsaari";
$countries["NGA"]="Nigeria";
$countries["NIC"]="Nicaragua";
$countries["NIR"]="Pohjois-Irlanti";
$countries["NIU"]="Niue";
$countries["NLD"]="Alankomaat";
$countries["NOR"]="Norja";
$countries["NPL"]="Nepal";
$countries["NRU"]="Nauru";
$countries["NTZ"]="Neutraali vyöhyke";
$countries["NZL"]="Uusi-Seelanti";
$countries["OMN"]="Oman";
$countries["PAK"]="Pakistan";
$countries["PAN"]="Panama";
$countries["PCN"]="Pitcairn";
$countries["PER"]="Peru";
$countries["PHL"]="Filippiinit";
$countries["PLW"]="Palau";
$countries["PNG"]="Papua-Uusi-Guinea";
$countries["POL"]="Puola";
$countries["PRI"]="Puerto Rico";
$countries["PRK"]="Korean kansantasavalta";
$countries["PRT"]="Portugali";
$countries["PRY"]="Paraguay";
$countries["PSE"]="Palestiina";
$countries["PYF"]="Ranskan Polynesia";
$countries["QAT"]="Qatar";
$countries["REU"]="Réunion";
$countries["ROM"]="Romania";
$countries["RUS"]="Venäjä";
$countries["RWA"]="Ruanda";
$countries["SAU"]="Saudi-Arabia";
$countries["SCG"]="Serbia ja Montenegro";
$countries["SCT"]="Skotlanti";
$countries["SDN"]="Sudan";
$countries["SEA"]="Merellä";
$countries["SEN"]="Senegal";
$countries["SER"]="Serbia";
$countries["SGP"]="Singapore";
$countries["SGS"]="Etelä-Georgia ja Eteläiset Sandwich-saaret";
$countries["SHN"]="Saint Helena";
$countries["SIC"]="Sisilia";
$countries["SJM"]="Huippuvuoret ja Jan Mayenin saaret";
$countries["SLB"]="Salomonsaaret";
$countries["SLE"]="Sierra Leone";
$countries["SLV"]="El Salvador";
$countries["SMR"]="San Marino";
$countries["SOM"]="Somalia";
$countries["SPM"]="Saint-Pierre ja Miquelon";
$countries["STP"]="Sao Tome ja Principe";
$countries["SUN"]="Neuvostoliitto";
$countries["SUR"]="Surinam";
$countries["SVK"]="Slovakia";
$countries["SVN"]="Slovenia";
$countries["SWE"]="Ruotsi";
$countries["SWZ"]="Swazimaa";
$countries["SYC"]="Seychellit";
$countries["SYR"]="Syyria";
$countries["TCA"]="Turks ja Caicos-saaret";
$countries["TCD"]="Tsad";
$countries["TGO"]="Togo";
$countries["THA"]="Thaimaa";
$countries["TJK"]="Tadzikistan";
$countries["TKL"]="Tokelau";
$countries["TKM"]="Turkmenistan";
$countries["TLS"]="Itä-Timor";
//$countries["TMP"]="Itä-Timor";
$countries["TON"]="Tonga";
$countries["TRN"]="Transilvania";
$countries["TTO"]="Trinidad ja Tobago";
$countries["TUN"]="Tunisia";
$countries["TUR"]="Turkki";
$countries["TUV"]="Tuvalu";
$countries["TWN"]="Taiwan";
$countries["TZA"]="Tansania";
$countries["UGA"]="Uganda";
$countries["UKR"]="Ukraina";
$countries["UMI"]="USA:n Tyynenmeren er. alue";
$countries["URY"]="Uruguay";
$countries["USA"]="Yhdysvallat";
$countries["UZB"]="Uzbekistan";
$countries["VAT"]="Vatikaani";
$countries["VCT"]="Saint Vincent ja Grenadiinit";
$countries["VEN"]="Venezuela";
$countries["VGB"]="Neitsyssaaret";
$countries["VIR"]="Yhdysvaltain Neitsytsaaret";
$countries["VNM"]="Vietnam";
$countries["VUT"]="Vanuatu";
$countries["WAF"]="Länsi-Afrikka";
$countries["WLF"]="Wallis ja Futuna";
$countries["WLS"]="Wales";
$countries["WSM"]="Samoa";
$countries["YEM"]="Jemen";
$countries["YUG"]="Jugoslavia";
$countries["ZAF"]="Etelä-Afrikka";
$countries["ZAR"]="Zaire";
$countries["ZMB"]="Sambia";
$countries["ZWE"]="Zimbabwe";
?>
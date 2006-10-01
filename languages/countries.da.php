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
 *					a group of people
 *		UK		United Kingdom				This is the only two-letter country code,
 *					and GBR or one of its components should be
 *			   	used instead.
 *		SLK		Slovakia					This code, listed in the last source cited,
 *				  should be SVK
 *		SLO		Slovenia					This code, listed in the last source cited,
 *					should be SVN
 *		SAM		South America				This code, listed in the last source cited,
 *					is not precise enough
 *		TMP		East Timor					Official name is TLS "Timor-Leste"
 *		HOL		Holland						Official name is NLD "Netherlands"
 *		ESM		Western Samoa				Official name is WSM "Samoa"
 *											
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
 */
if (preg_match("/lang\...\.php$/", $_SERVER["PHP_SELF"])>0) {
		print "Du kan ikke få adgang til en sprog fil direkte.";
		exit;
}

$countries["ABW"]="Aruba";
$countries["ACA"]="Acadia";
$countries["AFG"]="Afghanistan";
$countries["AGO"]="Angola";
$countries["AIA"]="Anguilla";
$countries["ALA"]="Ålandsøerne";
$countries["ALB"]="Albanien";
$countries["AND"]="Andorra";
$countries["ANT"]="Hollandske Antiller";
$countries["ARE"]="Forenede Arabiske Emirater";
$countries["ARG"]="Argentina";
$countries["ARM"]="Armenien";
$countries["ASM"]="Amerikansk Samoa";
$countries["ATA"]="Antarktis";
$countries["ATF"]="French Southern Territories";
$countries["ATG"]="Antigua og Barbuda";
$countries["AUS"]="Australien";
$countries["AUT"]="Østrig";
$countries["AZR"]="Azorerne";
$countries["AZE"]="Azerbadjan";
$countries["BDI"]="Burundi";
$countries["BEL"]="Belgien";
$countries["BEN"]="Benin";
$countries["BFA"]="Burkina Faso";
$countries["BGD"]="Bangladesh";
$countries["BGR"]="Bulgarien";
$countries["BHR"]="Bahrain";
$countries["BHS"]="Bahamas";
$countries["BIH"]="Bosnien-Hercegovina";
$countries["BLR"]="Belarus";
$countries["BLZ"]="Belize";
$countries["BMU"]="Bermuda";
$countries["BOL"]="Bolivia";
$countries["BRA"]="Brasilien";
$countries["BRB"]="Barbados";
$countries["BRN"]="Brunei";
$countries["BTN"]="Bhutan";
$countries["BVT"]="Bouvet-øen";
$countries["BWA"]="Botswana";
$countries["BWI"]="British West Indies";
$countries["CAF"]="Central Afrikanske Republik";
$countries["CAN"]="Canada";
$countries["CAP"]="Cape Colony";
$countries["CCK"]="Kokosøerne";
$countries["CHE"]="Schweiz";
$countries["CHI"]="Channel Islands";
$countries["CHL"]="Chile";
$countries["CHN"]="Kina";
$countries["CIV"]="Elfenbenskysten";
$countries["CMR"]="Kameroun";
$countries["COD"]="Congo (Kinshasa)";
$countries["COG"]="Congo (Brazzaville)";
$countries["COK"]="Cook-øerne";
$countries["COL"]="Colombia";
$countries["COM"]="Comorerne";
$countries["CPV"]="Kap Verde";
$countries["CRI"]="Costa Rica";
//-- $countries["CSK"]="Czechoslovakia";
$countries["CUB"]="Cuba";
$countries["CXR"]="Juleøen";
$countries["CYM"]="Cayman Islands";
$countries["CYP"]="Cypern";
$countries["CZE"]="Tjekkiet";
$countries["DEU"]="Tyskland";
$countries["DJI"]="Djibouti";
$countries["DMA"]="Dominica";
$countries["DNK"]="Danmark";
$countries["DOM"]="Dominikanske Republik";
$countries["DZA"]="Algeriet";
$countries["ECU"]="Ecuador";
$countries["EGY"]="Ægypten";
$countries["EIR"]="Irland";
$countries["ENG"]="England";
$countries["ERI"]="Eritrea";
$countries["ESH"]="Vest-Sahara";
$countries["ESP"]="Spanien";
$countries["EST"]="Estland";
$countries["ETH"]="Etiopien";
$countries["FIN"]="Finland";
$countries["FJI"]="Fiji";
$countries["FLD"]="Flandern";
$countries["FLK"]="Falklandsøerne";
$countries["FRA"]="Frankrig";
$countries["FRO"]="Færøerne";
$countries["FSM"]="Mikronesien";
$countries["GAB"]="Gabon";
$countries["GBR"]="Storbritannien";
$countries["GEO"]="Georgien";
$countries["GHA"]="Ghana";
$countries["GIB"]="Gibraltar";
$countries["GIN"]="Guinea";
$countries["GLP"]="Guadeloupe";
$countries["GMB"]="Gambia";
$countries["GNB"]="Guinea Bissau";
$countries["GNQ"]="Ækvatorial Guinea";
$countries["GRC"]="Grækenland";
$countries["GRD"]="Grenada";
$countries["GRL"]="Grønland";
$countries["GTM"]="Guatemala";
$countries["GUF"]="Fransk Guiana";
$countries["GUM"]="Guam";
$countries["GUY"]="Guyana";
$countries["HKG"]="Hong Kong";
$countries["HMD"]="Heard- og McDonald-øerne";
$countries["HND"]="Honduras";
$countries["HRV"]="Kroatien";
$countries["HTI"]="Haiti";
$countries["HUN"]="Ungarn";
$countries["IDN"]="Indonesien";
$countries["IND"]="Indien";
$countries["IOT"]="British Indian Ocean Territory";
$countries["IRN"]="Iran";
$countries["IRQ"]="Irak";
$countries["IRL"]="Irland";
$countries["ISL"]="Island";
$countries["ISR"]="Israel";
$countries["ITA"]="Italien";
$countries["JAM"]="Jamaica";
$countries["JOR"]="Jordan";
$countries["JPN"]="Japan";
$countries["KAZ"]="Kasakhstan";
$countries["KEN"]="Kenya";
$countries["KGZ"]="Kirgisistan";
$countries["KHM"]="Cambodia";
$countries["KIR"]="Kiribati";
$countries["KNA"]="Sankt Kitts og Nevis";
$countries["KOR"]="Sydkorea";
$countries["KWT"]="Kuwait";
$countries["LAO"]="Laos";
$countries["LBN"]="Libanon";
$countries["LBR"]="Liberia";
$countries["LBY"]="Libya";
$countries["LCA"]="Sankt Lucia";
$countries["LIE"]="Liechtenstein";
$countries["LKA"]="Sri Lanka";
$countries["LSO"]="Lesotho";
$countries["LTU"]="Litauen";
$countries["LUX"]="Luxemborg";
$countries["LVA"]="Letland";
$countries["MAC"]="Macau";
$countries["MAR"]="Marokko";
$countries["MCO"]="Monaco";
$countries["MDA"]="Moldavien";
$countries["MDG"]="Madagaskar";
$countries["MDV"]="Maldiverne";
$countries["MEX"]="Mexico";
$countries["MHL"]="Marshall-øerne";
$countries["MKD"]="Makedonien";
$countries["MLI"]="Mali";
$countries["MLT"]="Malta";
$countries["MMR"]="Burma";
$countries["MNG"]="Mongoliet";
$countries["MNP"]="Nordlige Marianere";
$countries["MNT"]="Montenegro";
$countries["MOZ"]="Mozambique";
$countries["MRT"]="Mauretanien";
$countries["MSR"]="Montserrat";
$countries["MTQ"]="Martinique";
$countries["MUS"]="Mauritius";
$countries["MWI"]="Malawi";
$countries["MYS"]="Malaysia";
$countries["MYT"]="Mayotte";
$countries["NAM"]="Namibia";
$countries["NCL"]="New Caledonia";
$countries["NER"]="Niger";
$countries["NFK"]="Norfolk-øen";
$countries["NGA"]="Nigeria";
$countries["NIC"]="Nicaragua";
$countries["NIR"]="Nord-Irland";
$countries["NIU"]="Niue";
$countries["NOR"]="Norge";
$countries["NLD"]="Holland";
$countries["NPL"]="Nepal";
$countries["NRU"]="Nauru";
$countries["NTZ"]="Neutral Zone";
$countries["NZL"]="New Zealand";
$countries["OMN"]="Oman";
$countries["PAK"]="Pakistan";
$countries["PAN"]="Panama";
$countries["PCN"]="Pitcairn";
$countries["PER"]="Peru";
$countries["PHL"]="Filippinerne";
$countries["PLW"]="Palau-øerne";
$countries["PNG"]="Papua New Guinea";
$countries["POL"]="Polen";
$countries["PRI"]="Puerto Rico";
$countries["PRK"]="Nordkorea";
$countries["PRT"]="Portugal";
$countries["PRY"]="Paraguay";
$countries["PSE"]="Palæstina";
$countries["PYF"]="Fransk Polynesien";
$countries["QAT"]="Qatar";
$countries["REU"]="Reunion";
$countries["ROU"]="Rumænien";
$countries["RUS"]="Rusland";
$countries["RWA"]="Rwanda";
$countries["SAU"]="Saudi-Arabien";
$countries["SCG"]="Serbien og Montenegro";
$countries["SCT"]="Skotland";
$countries["SDN"]="Sudan";
$countries["SEA"]="På havet";
$countries["SEN"]="Senegal";
$countries["SER"]="Serbien";
$countries["SGP"]="Singapore";
$countries["SGS"]="South Georgia and the South Sandwich Islands";
$countries["SHN"]="Sankt Helena";
$countries["SIC"]="Sicilien";
$countries["SJM"]="Svalbard og Jan Mayen";
$countries["SLB"]="Salomon-øerne";
$countries["SLE"]="Sierra Leone";
$countries["SLV"]="El Salvador";
$countries["SMR"]="San Marino";
$countries["SOM"]="Somalia";
$countries["SPM"]="Sankt Pierre og Miquelon";
$countries["SUN"]="U.S.S.R. (Sovjetunionen)";
$countries["STP"]="Sao Tome og Principe";
$countries["SUR"]="Surinam";
$countries["SVK"]="Slovakiet";
$countries["SVN"]="Slovenien";
$countries["SWE"]="Sverige";
$countries["SWZ"]="Swaziland";
$countries["SYC"]="Seychellene";
$countries["SYR"]="Syrien";
$countries["TCA"]="Turks- og Caicos-øerne";
$countries["TCD"]="Chad";
$countries["TGO"]="Togo";
$countries["THA"]="Thailand";
$countries["TJK"]="Tadsjikistan";
$countries["TKL"]="Tokelau";
$countries["TKM"]="Turkmenistan";
$countries["TLS"]="Øst-Timor";
$countries["TON"]="Tonga";
$countries["TRN"]="Transylvanien";
$countries["TTO"]="Trinidad og Tobago";
$countries["TUN"]="Tunesien";
$countries["TUR"]="Tyrkiet";
$countries["TUV"]="Tuvalu";
$countries["TWN"]="Taiwan";
$countries["TZA"]="Tanzania";
$countries["UGA"]="Uganda";
$countries["UKR"]="Ukraine";
$countries["UMI"]="Forenede Staters Forskellige Stillehavsøer";
$countries["URY"]="Uruguay";
$countries["USA"]="USA";
$countries["UZB"]="Usbekistan";
$countries["VAT"]="Vatikanstaten";
$countries["VCT"]="Sankt Vincent og Grenadinerne";
$countries["VEN"]="Venezuela";
$countries["VGB"]="Jomfruøerne (Storbritannien)";
$countries["VIR"]="Jomfruøerne (USA)";
$countries["VNM"]="Vietnam";
$countries["VUT"]="Vanuatu";
$countries["WAF"]="Vest Afrika";
$countries["WLF"]="Wallis- og Futuna-øerne";
$countries["WLS"]="Wales";
$countries["WSM"]="Samoa";
$countries["YEM"]="Yemen";
$countries["YUG"]="Jugoslavien";
$countries["ZAF"]="Sydafrikanske Republik";
$countries["ZAR"]="Zaire";
$countries["ZMB"]="Zambia";
$countries["ZWE"]="Zimbabwe";

?>

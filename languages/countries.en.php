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
 * @version $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], basename(__FILE__))!==false) {
	print "You cannot access a language file directly.";
	exit;
}

$countries["ABW"]="Aruba";
$countries["ACA"]="Acadia";
$countries["AFG"]="Afghanistan";
$countries["AGO"]="Angola";
$countries["AIA"]="Anguilla";
$countries["ALA"]="Åland Islands";
$countries["ALB"]="Albania";
$countries["AND"]="Andorra";
$countries["ANT"]="Netherlands Antilles";
$countries["ARE"]="United Arab Emirates";
$countries["ARG"]="Argentina";
$countries["ARM"]="Armenia";
$countries["ASM"]="American Samoa";
$countries["ATA"]="Antarctica";
$countries["ATF"]="French Southern Territories";
$countries["ATG"]="Antigua and Barbuda";
$countries["AUS"]="Australia";
$countries["AUT"]="Austria";
$countries["AZR"]="Azores";
$countries["AZE"]="Azerbaijan";
$countries["BDI"]="Burundi";
$countries["BEL"]="Belgium";
$countries["BEN"]="Benin";
$countries["BFA"]="Burkina Faso";
$countries["BGD"]="Bangladesh";
$countries["BGR"]="Bulgaria";
$countries["BHR"]="Bahrain";
$countries["BHS"]="Bahamas";
$countries["BIH"]="Bosnia and Herzegovina";
$countries["BLR"]="Belarus";
$countries["BLZ"]="Belize";
$countries["BMU"]="Bermuda";
$countries["BOL"]="Bolivia";
$countries["BRA"]="Brazil";
$countries["BRB"]="Barbados";
$countries["BRN"]="Brunei Darussalam";
$countries["BTN"]="Bhutan";
$countries["BVT"]="Bouvet Island";
$countries["BWA"]="Botswana";
$countries["BWI"]="British West Indies";
$countries["CAF"]="Central African Republic";
$countries["CAN"]="Canada";
$countries["CAP"]="Cape Colony";
$countries["CCK"]="Cocos (Keeling) Islands";
$countries["CHE"]="Switzerland";
$countries["CHI"]="Channel Islands";
$countries["CHL"]="Chile";
$countries["CHN"]="China";
$countries["CIV"]="Côte d'Ivoire";
$countries["CMR"]="Cameroon";
$countries["COD"]="Congo (Kinshasa)";
$countries["COG"]="Congo (Brazzaville)";
$countries["COK"]="Cook Islands";
$countries["COL"]="Colombia";
$countries["COM"]="Comoros";
$countries["CPV"]="Cape Verde";
$countries["CRI"]="Costa Rica";
$countries["CSK"]="Czechoslovakia";
$countries["CUB"]="Cuba";
$countries["CXR"]="Christmas Island";
$countries["CYM"]="Cayman Islands";
$countries["CYP"]="Cyprus";
$countries["CZE"]="Czech Republic";
$countries["DEU"]="Germany";
$countries["DJI"]="Djibouti";
$countries["DMA"]="Dominica";
$countries["DNK"]="Denmark";
$countries["DOM"]="Dominican Republic";
$countries["DZA"]="Algeria";
$countries["ECU"]="Ecuador";
$countries["EGY"]="Egypt";
$countries["EIR"]="Eire";
$countries["ENG"]="England";
$countries["ERI"]="Eritrea";
$countries["ESH"]="Western Sahara";
$countries["ESP"]="Spain";
$countries["EST"]="Estonia";
$countries["ETH"]="Ethiopia";
$countries["FIN"]="Finland";
$countries["FJI"]="Fiji";
$countries["FLD"]="Flanders";
$countries["FLK"]="Falkland Islands";
$countries["FRA"]="France";
$countries["FRO"]="Faeroe Islands";
$countries["FSM"]="Micronesia";
$countries["GAB"]="Gabon";
$countries["GBR"]="United Kingdom";
$countries["GEO"]="Georgia";
$countries["GHA"]="Ghana";
$countries["GIB"]="Gibraltar";
$countries["GIN"]="Guinea";
$countries["GLP"]="Guadeloupe";
$countries["GMB"]="Gambia";
$countries["GNB"]="Guinea-Bissau";
$countries["GNQ"]="Equatorial Guinea";
$countries["GRC"]="Greece";
$countries["GRD"]="Grenada";
$countries["GRL"]="Greenland";
$countries["GTM"]="Guatemala";
$countries["GUF"]="French Guiana";
$countries["GUM"]="Guam";
$countries["GUY"]="Guyana";
$countries["HKG"]="Hong Kong";
$countries["HMD"]="Heard Island and McDonald Islands";
$countries["HND"]="Honduras";
$countries["HRV"]="Croatia";
$countries["HTI"]="Haiti";
$countries["HUN"]="Hungary";
$countries["IDN"]="Indonesia";
$countries["IND"]="India";
$countries["IOT"]="British Indian Ocean Territory";
$countries["IRL"]="Ireland";
$countries["IRN"]="Iran";
$countries["IRQ"]="Iraq";
$countries["ISL"]="Iceland";
$countries["ISR"]="Israel";
$countries["ITA"]="Italy";
$countries["JAM"]="Jamaica";
$countries["JOR"]="Jordan";
$countries["JPN"]="Japan";
$countries["KAZ"]="Kazakhstan";
$countries["KEN"]="Kenya";
$countries["KGZ"]="Kyrgyzstan";
$countries["KHM"]="Cambodia";
$countries["KIR"]="Kiribati";
$countries["KNA"]="Saint Kitts and Nevis";
$countries["KOR"]="Korea";
$countries["KWT"]="Kuwait";
$countries["LAO"]="Laos";
$countries["LBN"]="Lebanon";
$countries["LBR"]="Liberia";
$countries["LBY"]="Libya";
$countries["LCA"]="Saint Lucia";
$countries["LIE"]="Liechtenstein";
$countries["LKA"]="Sri Lanka";
$countries["LSO"]="Lesotho";
$countries["LTU"]="Lithuania";
$countries["LUX"]="Luxembourg";
$countries["LVA"]="Latvia";
$countries["MAC"]="Macau";
$countries["MAR"]="Morocco";
$countries["MCO"]="Monaco";
$countries["MDA"]="Moldova";
$countries["MDG"]="Madagascar";
$countries["MDV"]="Maldives";
$countries["MEX"]="Mexico";
$countries["MHL"]="Marshall Islands";
$countries["MKD"]="Macedonia";
$countries["MLI"]="Mali";
$countries["MLT"]="Malta";
$countries["MMR"]="Myanmar";
$countries["MNG"]="Mongolia";
$countries["MNP"]="Northern Mariana Islands";
$countries["MNT"]="Montenegro";
$countries["MOZ"]="Mozambique";
$countries["MRT"]="Mauritania";
$countries["MSR"]="Montserrat";
$countries["MTQ"]="Martinique";
$countries["MUS"]="Mauritius";
$countries["MWI"]="Malawi";
$countries["MYS"]="Malaysia";
$countries["MYT"]="Mayotte";
$countries["NAM"]="Namibia";
$countries["NCL"]="New Caledonia";
$countries["NER"]="Niger";
$countries["NFK"]="Norfolk Island";
$countries["NGA"]="Nigeria";
$countries["NIC"]="Nicaragua";
$countries["NIR"]="Northern Ireland";
$countries["NIU"]="Niue";
$countries["NLD"]="Netherlands";
$countries["NOR"]="Norway";
$countries["NPL"]="Nepal";
$countries["NRU"]="Nauru";
$countries["NTZ"]="Neutral Zone";
$countries["NZL"]="New Zealand";
$countries["OMN"]="Oman";
$countries["PAK"]="Pakistan";
$countries["PAN"]="Panama";
$countries["PCN"]="Pitcairn";
$countries["PER"]="Peru";
$countries["PHL"]="Philippines";
$countries["PLW"]="Palau";
$countries["PNG"]="Papua New Guinea";
$countries["POL"]="Poland";
$countries["PRI"]="Puerto Rico";
$countries["PRK"]="North Korea";
$countries["PRT"]="Portugal";
$countries["PRY"]="Paraguay";
$countries["PSE"]="Occupied Palestinian Territory";
$countries["PYF"]="French Polynesia";
$countries["QAT"]="Qatar";
$countries["REU"]="Réunion";
$countries["ROM"]="Romania";
$countries["RUS"]="Russia";
$countries["RWA"]="Rwanda";
$countries["SAU"]="Saudi Arabia";
$countries["SCG"]="Serbia and Montenegro";
$countries["SCT"]="Scotland";
$countries["SDN"]="Sudan";
$countries["SEA"]="At Sea";
$countries["SEN"]="Senegal";
$countries["SER"]="Serbia";
$countries["SGP"]="Singapore";
$countries["SGS"]="South Georgia and the South Sandwich Islands";
$countries["SHN"]="Saint Helena";
$countries["SIC"]="Sicily";
$countries["SJM"]="Svalbard and Jan Mayen Islands";
$countries["SLB"]="Solomon Islands";
$countries["SLE"]="Sierra Leone";
$countries["SLV"]="El Salvador";
$countries["SMR"]="San Marino";
$countries["SOM"]="Somalia";
$countries["SPM"]="Saint Pierre and Miquelon";
$countries["STP"]="São Tomé and Príncipe";
$countries["SUN"]="USSR";
$countries["SUR"]="Suriname";
$countries["SVK"]="Slovakia";
$countries["SVN"]="Slovenia";
$countries["SWE"]="Sweden";
$countries["SWZ"]="Swaziland";
$countries["SYC"]="Seychelles";
$countries["SYR"]="Syrian Arab Republic";
$countries["TCA"]="Turks and Caicos Islands";
$countries["TCD"]="Chad";
$countries["TGO"]="Togo";
$countries["THA"]="Thailand";
$countries["TJK"]="Tajikistan";
$countries["TKL"]="Tokelau";
$countries["TKM"]="Turkmenistan";
$countries["TLS"]="Timor-Leste";
$countries["TON"]="Tonga";
$countries["TRN"]="Transylvania";
$countries["TTO"]="Trinidad and Tobago";
$countries["TUN"]="Tunisia";
$countries["TUR"]="Turkey";
$countries["TUV"]="Tuvalu";
$countries["TWN"]="Taiwan";
$countries["TZA"]="Tanzania";
$countries["UGA"]="Uganda";
$countries["UKR"]="Ukraine";
$countries["UMI"]="US Minor Outlying Islands";
$countries["URY"]="Uruguay";
$countries["USA"]="USA";
$countries["UZB"]="Uzbekistan";
$countries["VAT"]="Vatican City";
$countries["VCT"]="Saint Vincent and the Grenadines";
$countries["VEN"]="Venezuela";
$countries["VGB"]="British Virgin Islands";
$countries["VIR"]="US Virgin Islands";
$countries["VNM"]="Viet Nam";
$countries["VUT"]="Vanuatu";
$countries["WAF"]="West Africa";
$countries["WLF"]="Wallis and Futuna Islands";
$countries["WLS"]="Wales";
$countries["WSM"]="Samoa";
$countries["YEM"]="Yemen";
$countries["YUG"]="Yugoslavia";
$countries["ZAF"]="South Africa";
$countries["ZAR"]="Zaire";
$countries["ZMB"]="Zambia";
$countries["ZWE"]="Zimbabwe";
$countries["???"]="Unknown";

/*
 * The following table lists alternate names for various Chapman codes.
 * It will be used when country names have to be converted to Chapman codes.
 * You do not have to list all the possibilities in all page languages.  This
 * will be done automatically by the country-to-Chapman conversion routine.
 *
 * Because the list, and its contents, are specific to each language, the 
 * Translator Tool won't let you work on the list directly.  The list will
 * have to be updated and amended manually.
 *
 * Suppose Chapman code "XYZ" represents the same country, and that country 
 * had the names "Name1", "Name2", "Name3" in its history.  It is now known
 * as "Current name".  You can list the various names like this:
 *
 * $countries["XYZ"]="Current name";
 * $altCountryName["XYZ"]="Name1; Name2; Name3";
 *
 * The Chapman-to-country conversion will always use the $countries list of
 * the current page language, no matter what the original country name was.
 * 
 */
$altCountryNames["COD"]="Zaire";
$altCountryNames["DEU"]="East Germany; West Germany; GDR; FRG";
$altCountryNames["FLK"]="Malvinas";
$altCountryNames["GBR"]="Great Britain";
$altCountryNames["LKA"]="Ceylon";
$altCountryNames["MAC"]="Macao";
$altCountryNames["MMR"]="Burma";
$altCountryNames["NLD"]="Holland";
$altCountryNames["PLW"]="Belau";
$altCountryNames["SUN"]="Soviet Union";
$altCountryNames["TLS"]="East Timor";
$altCountryNames["VAT"]="Holy See";
$altCountryNames["WSM"]="Western Samoa";

?>

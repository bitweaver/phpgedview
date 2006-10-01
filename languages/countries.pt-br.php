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
 * @author: Anderson Wilson
 * @version $Id$
 */
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
		print "You cannot access a language file directly.";
		exit;
}
$countries["ABW"]="Aruba";
$countries["ACA"]="Acadia";
$countries["AFG"]="Afeganistão";
$countries["AGO"]="Angola";
$countries["AIA"]="Anguila";
$countries["ALA"]="Ilhas Åland";
$countries["ALB"]="Albânia";
$countries["AND"]="Andorra";
$countries["ANT"]="Antilhas Neerlandesas";
$countries["ARE"]="Emirados Árabes";
$countries["ARG"]="Argentina";
$countries["ARM"]="Armênia";
$countries["ASM"]="Samoa Americana";
$countries["ATA"]="Antártida";
$countries["ATF"]="Territórios Austrais Franceses";
$countries["ATG"]="Antígua e Barbuda";
$countries["AUS"]="Austrália";
$countries["AUT"]="Áustria";
$countries["AZR"]="Açores";
$countries["AZE"]="Azerbaijão";
$countries["BDI"]="Burúndi";
$countries["BEL"]="Bélgica";
$countries["BEN"]="Benin";
$countries["BFA"]="Burquina Faso";
$countries["BGD"]="Bangladeche";
$countries["BGR"]="Bulgária";
$countries["BHR"]="Barém";
$countries["BHS"]="Bahamas";
$countries["BIH"]="Bôsnia e Herzegovina";
$countries["BLR"]="Bielo-Rússia";
$countries["BLZ"]="Belize";
$countries["BMU"]="Bermudas";
$countries["BOL"]="Bolívia";
$countries["BRA"]="Brasil";
$countries["BRB"]="Barbados";
$countries["BRN"]="Brunei Darussalam";
$countries["BTN"]="Butão";
$countries["BVT"]="Ilha Bouvet";
$countries["BWA"]="Botsuana";
$countries["BWI"]="Índias Orientais Britânicas";
$countries["CAF"]="República Centro-Africana";
$countries["CAN"]="Canadá";
$countries["CAP"]="Colônia do Cabo";
$countries["CCK"]="Ilhas dos Cocos";
$countries["CHE"]="Suíça";
$countries["CHI"]="Ilhas do Canal";
$countries["CHL"]="Chile";
$countries["CHN"]="China";
$countries["CIV"]="Costa do Marfim";
$countries["CMR"]="Camarões";
$countries["COD"]="Congo (Kinshasa)";
$countries["COG"]="Congo (Brazzaville)";
$countries["COK"]="Ilhas Cook";
$countries["COL"]="Colômbia";
$countries["COM"]="Comores";
$countries["CPV"]="Cabo Verde";
$countries["CRI"]="Costa Rica";
$countries["CSK"]="Tchecoslováquia";
$countries["CUB"]="Cuba";
$countries["CXR"]="Ilha do Natal";
$countries["CYM"]="Ilhas Caimão";
$countries["CYP"]="Chipre";
$countries["CZE"]="República Checa";
$countries["DEU"]="Alemanha";
$countries["DJI"]="Jibuti";
$countries["DMA"]="Domínica";
$countries["DNK"]="Dinamarca";
$countries["DOM"]="República Dominicana";
$countries["DZA"]="Algéria";
$countries["ECU"]="Equador";
$countries["EGY"]="Egíto";
$countries["EIR"]="Eire";
$countries["ENG"]="Inglaterra";
$countries["ERI"]="Eritreia";
$countries["ESH"]="Sahara Ocidental";
$countries["ESP"]="Espanha";
$countries["EST"]="Estónia";
$countries["ETH"]="Etiópia";
$countries["FIN"]="Finlândia";
$countries["FJI"]="Fiji";
$countries["FLD"]="Flanders";
$countries["FLK"]="Ilhas Falkland (Malvinas)";
$countries["FRA"]="França";
$countries["FRO"]="Ilhas Faroé";
$countries["FSM"]="Micronésia";
$countries["GAB"]="Gabão";
$countries["GBR"]="Reino Unido (da Grã Bretanha e Irlanda do Norte)";
$countries["GEO"]="Geórgia";
$countries["GHA"]="Ghana";
$countries["GIB"]="Gibraltar";
$countries["GIN"]="Guiné";
$countries["GLP"]="Guadalupe";
$countries["GMB"]="Gâmbia";
$countries["GNB"]="Guiné-Bissau";
$countries["GNQ"]="Guiné Equatorial";
$countries["GRC"]="Grécia";
$countries["GRD"]="Grenada";
$countries["GRL"]="Groenlândia";
$countries["GTM"]="Guatemala";
$countries["GUF"]="Guiana Francesa";
$countries["GUM"]="Guam";
$countries["GUY"]="Guiana";
$countries["HKG"]="Hong Kong";
$countries["HMD"]="Ilhas Heard e McDonald";
$countries["HND"]="Honduras";
$countries["HRV"]="Croácia";
$countries["HTI"]="Haiti";
$countries["HUN"]="Hungria";
$countries["IDN"]="Indonésia";
$countries["IND"]="Índia";
$countries["IOT"]="Território Britânico do Oceano Índico";
$countries["IRL"]="Irlanda";
$countries["IRN"]="Irão";
$countries["IRQ"]="Iraque";
$countries["ISL"]="Islândia";
$countries["ISR"]="Israel";
$countries["ITA"]="Itália";
$countries["JAM"]="Jamaica";
$countries["JOR"]="Jordânia";
$countries["JPN"]="Japão";
$countries["KAZ"]="Cazaquistão";
$countries["KEN"]="Quénia";
$countries["KGZ"]="Quirguizistão";
$countries["KHM"]="Camboja";
$countries["KIR"]="Quiribáti";
$countries["KNA"]="São Cristóvão e Neves";
$countries["KOR"]="Coreia do Sul";
$countries["KWT"]="Kuwait";
$countries["LAO"]="Laos";
$countries["LBN"]="Líbano";
$countries["LBR"]="Libéria";
$countries["LBY"]="Líbia";
$countries["LCA"]="Santa Lúcia";
$countries["LIE"]="Listenstaine";
$countries["LKA"]="Sri Lanca (Ceylon)";
$countries["LSO"]="Lesoto";
$countries["LTU"]="Lituânia";
$countries["LUX"]="Luxemburgo";
$countries["LVA"]="Letónia";
$countries["MAC"]="Macau";
$countries["MAR"]="Marrocos";
$countries["MCO"]="Mónaco";
$countries["MDA"]="Moldávia";
$countries["MDG"]="Madagáscar";
$countries["MDV"]="Maldivas";
$countries["MEX"]="México";
$countries["MHL"]="Ilhas Marshall";
$countries["MKD"]="Macedónia";
$countries["MLI"]="Mali";
$countries["MLT"]="Malta";
$countries["MMR"]="Myanmar (Birmânia)";
$countries["MNG"]="Mongólia";
$countries["MNP"]="Ilhas Marianas do Norte";
$countries["MNT"]="Montenegro";
$countries["MOZ"]="Moçambique";
$countries["MRT"]="Mauritânia";
$countries["MSR"]="Montserrat";
$countries["MTQ"]="Martinica";
$countries["MUS"]="Maurícia";
$countries["MWI"]="Malávi";
$countries["MYS"]="Malásia";
$countries["MYT"]="Mayotte";
$countries["NAM"]="Namíbia";
$countries["NCL"]="Nova Caledónia";
$countries["NER"]="Níger";
$countries["NFK"]="Ilha Norfolk";
$countries["NGA"]="Nigéria";
$countries["NIC"]="Nicarágua";
$countries["NIR"]="Irlanda do Norte";
$countries["NIU"]="Niue";
$countries["NLD"]="Países Baixos";
$countries["NOR"]="Noruega";
$countries["NPL"]="Nepal";
$countries["NRU"]="Nauru";
$countries["NTZ"]="Zona Neutra da Coréia";
$countries["NZL"]="Nova Zelândia";
$countries["OMN"]="Omã";
$countries["PAK"]="Paquistão";
$countries["PAN"]="Panamá";
$countries["PCN"]="Pitcairn";
$countries["PER"]="Peru";
$countries["PHL"]="Filipinas";
$countries["PLW"]="Palau (Belau)";
$countries["PNG"]="Papua-Nova Guiné";
$countries["POL"]="Polónia";
$countries["PRI"]="Porto Rico";
$countries["PRK"]="Coreia do Norte";
$countries["PRT"]="Portugal";
$countries["PRY"]="Paraguai";
$countries["PSE"]="Território Ocupado da Palestina";
$countries["PYF"]="Polinésia Francesa";
$countries["QAT"]="Catar";
$countries["REU"]="Reunião";
$countries["ROM"]="Roménia";
$countries["RUS"]="Rússia";
$countries["RWA"]="Ruanda";
$countries["SAU"]="Arábia Saudita (Reino da)";
$countries["SCG"]="Sérvia e Montenegro";
$countries["SCT"]="Escócia";
$countries["SDN"]="Sudão";
$countries["SEA"]="No Mar";
$countries["SEN"]="Senegal";
$countries["SER"]="Serbia";
$countries["SGP"]="Singapura";
$countries["SGS"]="Ilhas Geórgia do Sul e Sandwich do Sul";
$countries["SHN"]="Santa Helena";
$countries["SIC"]="Sicília";
$countries["SJM"]="Ilhas Svalbard e Jan Mayen";
$countries["SLB"]="Ilhas Salomão";
$countries["SLE"]="Sierra Leone";
$countries["SLV"]="El Salvador";
$countries["SMR"]="São Marinho";
$countries["SOM"]="Somália";
$countries["SPM"]="São Pedro e Miquelon";
$countries["STP"]="São Tomé e Príncipe";
$countries["SUN"]="U.S.S.R. (União Soviética)";
$countries["SUR"]="Suriname";
$countries["SVK"]="Eslováquia";
$countries["SVN"]="Eslovénia";
$countries["SWE"]="Suécia";
$countries["SWZ"]="Suazilândia";
$countries["SYC"]="Seicheles";
$countries["SYR"]="Síria";
$countries["TCA"]="Ilhas Turcas e Caicos";
$countries["TCD"]="Chade";
$countries["TGO"]="Togo";
$countries["THA"]="Tailândia";
$countries["TJK"]="Tajiquistão";
$countries["TKL"]="Tokelau";
$countries["TKM"]="Turquemenistão";
$countries["TLS"]="Timor Leste";
$countries["TON"]="Tonga";
$countries["TRN"]="Transilvânia";
$countries["TTO"]="Trindade e Tobago";
$countries["TUN"]="Tunísia";
$countries["TUR"]="Turquia";
$countries["TUV"]="Tuvalu";
$countries["TWN"]="Taiwan";
$countries["TZA"]="Tanzânia";
$countries["UGA"]="Uganda";
$countries["UKR"]="Ucrânia";
$countries["UMI"]="Ilhas Menores Distantes dos Estados Unidos";
$countries["URY"]="Uruguai";
$countries["USA"]="EUA";
$countries["UZB"]="Usbequistão";
$countries["VAT"]="Vaticano";
$countries["VCT"]="São Vicente e Granadinas";
$countries["VEN"]="Venezuela";
$countries["VGB"]="Ilhas Virgens Britânicas";
$countries["VIR"]="Ilhas Virgens Americanas";
$countries["VNM"]="Vietnam";
$countries["VUT"]="Vanuatu";
$countries["WAF"]="África Ocidental";
$countries["WLF"]="Ilhas Wallis e Futuna";
$countries["WLS"]="Wales";
$countries["WSM"]="Samoa";
$countries["YEM"]="Iémen";
$countries["YUG"]="Iugoslávia";
$countries["ZAF"]="África do Sul";
$countries["ZAR"]="Zaire";
$countries["ZMB"]="Zâmbia";
$countries["ZWE"]="Zimbabué";
?>
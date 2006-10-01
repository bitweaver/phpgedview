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
 * @author Meliza
 * @package PhpGedView
 * @subpackage Languages
 * @version $Id$
 */

if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
    print "You cannot access a language file directly.";
    exit;
}

$countries["ABW"]="ארובה";
$countries["ACA"]="אקדיה";
$countries["AFG"]="אפגניסטן";
$countries["AGO"]="אנגולה";
$countries["AIA"]="אנגווילה";
$countries["ALA"]="איי אולנד";
$countries["ALB"]="אלבניה";
$countries["AND"]="אנדורה";
$countries["ANT"]="האנטילים של ארצות השפלה";
$countries["ARE"]="איחוד האמירויות הערביות";
$countries["ARG"]="ארגנטינה";
$countries["ARM"]="ארמניה";
$countries["ASM"]="סמואה האמריקאית";
$countries["ATA"]="אנטארקטיקה";
$countries["ATF"]="הטריטוריות הדרומיות של צרפת";
$countries["ATG"]="אנטיגואה וברבודה";
$countries["AUS"]="אוסטרליה";
$countries["AUT"]="אוסטריה";
$countries["AZR"]="אזוריים";
$countries["AZE"]="אזרביג'אן";
$countries["BDI"]="בורונדי";
$countries["BEL"]="בלגיה";
$countries["BEN"]="בנין";
$countries["BFA"]="בורקינה פסו";
$countries["BGD"]="בנגלדש";
$countries["BGR"]="בולגריה";
$countries["BHR"]="בחריין";
$countries["BHS"]="בהאמה";
$countries["BIH"]="בוסניה הרצגובינה";
$countries["BLR"]="בלארוס";
$countries["BLZ"]="בליז";
$countries["BMU"]="ברמודה";
$countries["BOL"]="בוליביה";
$countries["BRA"]="ברזיל";
$countries["BRB"]="ברבדוס";
$countries["BRN"]="ברוניי";
$countries["BTN"]="בהוטן";
$countries["BVT"]="אי בווה";
$countries["BWA"]="בוטסואנה";
$countries["BWI"]="הודו המערבית הבריטית";
$countries["CAF"]="הרפובליקה המרכז אפריקאית";
$countries["CAN"]="קנדה";
$countries["CAP"]="מושבת הכף";
$countries["CCK"]="איי קוקוס";
$countries["CHE"]="שוויץ";
$countries["CHI"]="איי התעלה";
$countries["CHL"]="צ'ילה";
$countries["CHN"]="סין";
$countries["CIV"]="קוט דיוואר";
$countries["CMR"]="קמרון";
$countries["COD"]="הרפובליקה הדמוקרטית של קונגו";
$countries["COG"]="הרפובליקה של קונגו";
$countries["COK"]="איי קוק";
$countries["COL"]="קולומביה";
$countries["COM"]="קומורו";
$countries["CPV"]="קייפ ורדה";
$countries["CRI"]="קוסטה ריקה";
$countries["CSK"]="צ'כוסלובקיה";
$countries["CUB"]="קובה";
$countries["CXR"]="איי חג המולד";
$countries["CYM"]="איי קיימן";
$countries["CYP"]="קפריסין";
$countries["CZE"]="צ'כיה";
$countries["DEU"]="גרמניה";
$countries["DJI"]="ג'יבוטי";
$countries["DMA"]="דומיניקה";
$countries["DNK"]="דנמרק";
$countries["DOM"]="רפובליקה דומיניקנית";
$countries["DZA"]="אלג'יריה";
$countries["ECU"]="אקוודור";
$countries["EGY"]="מצרים";
$countries["EIR"]="אירלנד (רפובליקה)";
$countries["ENG"]="אנגליה";
$countries["ERI"]="אריתריאה";
$countries["ESH"]="סהרה המערבית";
//$countries["ESM"]="סמוא המערבית";
$countries["ESP"]="ספרד";
$countries["EST"]="אסטוניה";
$countries["ETH"]="אתיופיה";
$countries["FIN"]="פינלנד";
$countries["FJI"]="פיג'י";
$countries["FLD"]="פלנדריה";
$countries["FLK"]="איי פוקלנד";
$countries["FRA"]="צרפת";
$countries["FRO"]="איי פארו";
$countries["FSM"]="מיקרונזיה";
$countries["GAB"]="גבון";
$countries["GBR"]="בריטניה";
$countries["GEO"]="גרוזיה";
$countries["GHA"]="גאנה";
$countries["GIB"]="גיברלטר";
$countries["GIN"]="גינאה";
$countries["GLP"]="גוואדלופ";
$countries["GMB"]="גמביה";
$countries["GNB"]="גינאה ביסאו";
$countries["GNQ"]="גינאה המשוונית";
$countries["GRC"]="יוון";
$countries["GRD"]="גרנדה";
$countries["GRL"]="גרינלנד";
$countries["GTM"]="גוואטמלה";
$countries["GUF"]="גיאנה הצרפתית";
$countries["GUM"]="גואם";
$countries["GUY"]="גיאנה";
$countries["HKG"]="הונג קונג";
$countries["HMD"]="אי הרד ואיי מקדונלד";
$countries["HND"]="הונדורס";
//$countries["HOL"]="הולנד";
$countries["HRV"]="קרואטיה";
$countries["HTI"]="האיטי";
$countries["HUN"]="הונגריה";
$countries["IDN"]="אינדונזיה";
$countries["IND"]="הודו";
$countries["IOT"]="הטריטוריות הבריתיות באוקיאנוס ההודי";
$countries["IRL"]="אירלנד";
$countries["IRN"]="איראן";
$countries["IRQ"]="עיראק";
$countries["ISL"]="איסלנד";
$countries["ISR"]="ישראל";
$countries["ITA"]="איטליה";
$countries["JAM"]="ג'מייקה";
$countries["JOR"]="ירדן";
$countries["JPN"]="יפן";
$countries["KAZ"]="קזחסטן";
$countries["KEN"]="קניה";
$countries["KGZ"]="קירגיזסטן";
$countries["KHM"]="קמבודיה";
$countries["KIR"]="קיריבטי";
$countries["KNA"]="סנט קיטס ונוויס";
$countries["KOR"]="דרום קוריאה";
$countries["KWT"]="כווית";
$countries["LAO"]="לאוס";
$countries["LBN"]="לבנון";
$countries["LBR"]="ליבריה";
$countries["LBY"]="לוב";
$countries["LCA"]="סנט לוסיה";
$countries["LIE"]="ליכטנשטיין";
$countries["LKA"]="סרי לנקה";
$countries["LSO"]="לסוטו";
$countries["LTU"]="ליטא";
$countries["LUX"]="לוקסמבורג";
$countries["LVA"]="לטביה";
$countries["MAC"]="מקאו";
$countries["MAR"]="מרוקו";
$countries["MCO"]="מונאקו";
$countries["MDA"]="מולדובה";
$countries["MDG"]="מדגסקר";
$countries["MDV"]="איים המלדיביים";
$countries["MEX"]="מכסיקו";
$countries["MHL"]="איי מרשל";
$countries["MKD"]="מקדוניה";
$countries["MLI"]="מאלי";
$countries["MLT"]="מלטה";
$countries["MMR"]="מינמר (בורמה)";
$countries["MNG"]="מונגוליה";
$countries["MNP"]="איי מריינה הצפוניים";
$countries["MNT"]="מונטנגרו";
$countries["MOZ"]="מוזמביק";
$countries["MRT"]="מאוריטניה";
$countries["MSR"]="מונטסרט";
$countries["MTQ"]="מרטיניק";
$countries["MUS"]="מאוריציוס";
$countries["MWI"]="מלאווי";
$countries["MYS"]="מלזיה";
$countries["MYT"]="מאיוט";
$countries["NAM"]="נמיביה";
$countries["NCL"]="קלדוניה החדשה";
$countries["NER"]="ניז'ר";
$countries["NFK"]="אי נורפוק";
$countries["NGA"]="ניגריה";
$countries["NIC"]="ניקרגואה";
$countries["NIR"]="צפון אירלנד";
$countries["NIU"]="ניווה";
$countries["NLD"]="ארצות השפלה";
$countries["NOR"]="נורבגיה";
$countries["NPL"]="נפאל";
$countries["NRU"]="נאורו";
$countries["NTZ"]="עזור נאוטרלי";
$countries["NZL"]="ניו זילנד";
$countries["OMN"]="עומן";
$countries["PAK"]="פקיסטן";
$countries["PAN"]="פנמה";
$countries["PCN"]="פיטקרן";
$countries["PER"]="פרו";
$countries["PHL"]="פיליפינים";
$countries["PLW"]="פלאו";
$countries["PNG"]="פפואה גינאה החדשה";
$countries["POL"]="פולין";
$countries["PRI"]="פורטו ריקו";
$countries["PRK"]="צפון קוראה";
$countries["PRT"]="פורטוגל";
$countries["PRY"]="פרגוואי";
$countries["PSE"]="פלסטינה";
$countries["PYF"]="הפולינזים הצרפתיים";
$countries["QAT"]="קטאר";
$countries["REU"]="ראוניון";
$countries["ROM"]="רומניה";
$countries["RUS"]="רוסיה";
$countries["RWA"]="רואנדה";
$countries["SAU"]="ערב הסעודית";
$countries["SCG"]="סרביה ומונטנגרו";
$countries["SCT"]="סקוטלנד";
$countries["SDN"]="סודן";
$countries["SEA"]="בים";
$countries["SEN"]="סנגל";
$countries["SER"]="סרביה";
$countries["SGP"]="סינגפור";
$countries["SGS"]="איי ג'ורג'יה וסנדביץ הדרומיים";
$countries["SHN"]="סנט הלנה";
$countries["SIC"]="סיציליה";
$countries["SJM"]="איי סבאלברג וז'אן מאיאן";
$countries["SLB"]="איי שלמה";
$countries["SLE"]="סיירה לאונה";
$countries["SLV"]="אל סלבדור";
$countries["SMR"]="סאן מרינו";
$countries["SOM"]="סומליה";
$countries["SPM"]="סן פייר ומיקלון";
$countries["STP"]="סאו טומה ופרינסיפה";
$countries["SUN"]="ברית המועצות";
$countries["SUR"]="סורינאם";
$countries["SVK"]="סלובקיה";
$countries["SVN"]="סלובניה";
$countries["SWE"]="שבדיה";
$countries["SWZ"]="סווזילנד";
$countries["SYC"]="סיישל";
$countries["SYR"]="סוריה";
$countries["TCA"]="איי טרקס וקאיקוס";
$countries["TCD"]="צ'אד";
$countries["TGO"]="טוגו";
$countries["THA"]="תאילנד";
$countries["TJK"]="טג'יקיסטן";
$countries["TKL"]="טוקלאו";
$countries["TKM"]="טורקמניסטן";
$countries["TLS"]="מזרח טימור";
//$countries["TMP"]="מזרח טימור";
$countries["TON"]="טונגה";
$countries["TRN"]="טרנסילבניה";
$countries["TTO"]="טרינידד וטובגו";
$countries["TUN"]="תוניס";
$countries["TUR"]="תורקיה";
$countries["TUV"]="טובאלו";
$countries["TWN"]="טיוואן";
$countries["TZA"]="טנזניה";
$countries["UGA"]="אוגנדה";
$countries["UKR"]="אוקראינה";
$countries["UMI"]="איים קטנים מרוחקים של ארה\"ב";
$countries["URY"]="אורוגוואי";
$countries["USA"]="ארצות הברית";
$countries["UZB"]="אוזבקיסטן";
$countries["VAT"]="קריית הוותיקן";
$countries["VCT"]="סנט וינסנט והגרנדינים";
$countries["VEN"]="ונצואלה";
$countries["VGB"]="איי הבתולה הבריטיים";
$countries["VIR"]="איי הבתולה האמריקאיים";
$countries["VNM"]="ויטנאם";
$countries["VUT"]="ונואטו";
$countries["WAF"]="אפריקה המערבית";
$countries["WLF"]="איי וליס ופוטונה";
$countries["WLS"]="ויילס";
$countries["WSM"]="סמואה";
$countries["YEM"]="תימן";
$countries["YUG"]="יוגוסלביה";
$countries["ZAF"]="דרום אפריקה";
$countries["ZAR"]="זאיר";
$countries["ZMB"]="זמביה";
$countries["ZWE"]="זימבבואה";

?>
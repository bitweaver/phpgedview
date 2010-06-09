<?php
/**
 * Default GEDCOM configuration file
 *
 * The variables in this file are the GEDCOM configuration variables, this file defines the default
 * settings. Site administrators may edit these settings online through the editconfig_gedcom.php
 * file.  Once edited, a new file named gedcom.ged_conf.php that is specific to the GEDCOM is stored
 * in the $INDEX_DIRECTORY.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package PhpGedView
 * @subpackage Admin
 * @see editconfig_gedcom.php
 * @version $Id$
 */

if (stristr($_SERVER["SCRIPT_NAME"], "/".basename(__FILE__))!==false) {
	print "Naughty, Naughty, Naughty!";
	exit;
}

global $PRIV_HIDE, $PRIV_PUBLIC, $PRIV_USER, $PRIV_NONE;

//-- assign the default language.  User can override this setting if $ENABLE_MULTI_LANGUAGE = true
$LANGUAGE = "english";

$CALENDAR_FORMAT = "gregorian";			//-- Translate dates to the specified Calendar
										//-- options are gregorian, julian, french, jewish, jewish_and_gregorian,
										//-- hebrew, hebrew_and_gregorian
$DISPLAY_JEWISH_THOUSANDS = false;		//-- show Alafim in Jewish dates Similar to php 5.0 CAL_JEWISH_ADD_ALAFIM
$DISPLAY_JEWISH_GERESHAYIM = true;		//-- show single and double quotes in Hebrew dates. Similar to php 5.0 CAL_JEWISH_ADD_GERESHAYIM
$JEWISH_ASHKENAZ_PRONUNCIATION = true;	//-- Jewish pronunciation option

$CHARACTER_SET = "UTF-8";				//-- This is the character set of your gedcom file.  UTF-8 is the default and should work for
										//-- almost all sites.  If you export your gedcom using ibm windows encoding, then you should put
										//-- WINDOWS here.
										//-- NOTE: PHP does NOT support UNICODE so don't try it :-)

$ENABLE_MULTI_LANGUAGE = true;			//-- set to true to give users the option of selecting a different language from
										//-- a dropdown list in the footer and default to the language they have set in their browser settings
$DEFAULT_PEDIGREE_GENERATIONS = "4";	// -- set the default number of generations to display on the pedigree charts
$MAX_PEDIGREE_GENERATIONS = "10";		// -- set the max number of generations to display on the pedigree charts
$MAX_DESCENDANCY_GENERATIONS = "15";	// -- set the max number of generations to display on the descendancy charts
$USE_RIN = false;						// -- Use the RIN # instead of the regular GEDCOM ID for places where you are asked to enter an ID
$PEDIGREE_ROOT_ID = "";				// -- use this line to change the default person who appears on
										// -- the Pedigree tree
$GEDCOM_ID_PREFIX = "I";					// -- This is the prefix prepend to newly generated individual records
$SOURCE_ID_PREFIX = "S";					// -- This is the prefix prepend to newly generated source records
$REPO_ID_PREFIX = "R";					// -- This is the prefix prepend to newly generated repository records
$FAM_ID_PREFIX = "F";					// -- This is the prefix prepend to newly generated family records
$MEDIA_ID_PREFIX = "M";					// -- This is the prefix prepend to newly generated media records
$PEDIGREE_FULL_DETAILS = true;			// -- Show the birth and death details of an individual on charts
$PEDIGREE_SHOW_GENDER = true;			// -- Show the gender of an individual by means of an icon on charts
$PEDIGREE_LAYOUT = true;					// -- Set to true for Landscape mode, false for portrait mode
$SHOW_EMPTY_BOXES = true;				// -- Show empty boxes on charts if the person is unknown
$ZOOM_BOXES = "click";				// -- When should pedigree boxes zoom.  Values are "disabled", "mouseover", "click"
$LINK_ICONS = "click";				// -- When should pedigree box extra links show up.  Values are "disabled", "mouseover", "click"
$ABBREVIATE_CHART_LABELS = false;		//-- should chart labels like "Birth" be abbreviated as "B"
$SHOW_PARENTS_AGE = true;				// -- show age of parents on charts next to the birth date

$HIDE_LIVE_PEOPLE = true;				// -- a false value will show details of people who are still alive
$CHECK_CHILD_DATES = true;		// -- When checking if a person is alive, check the children's birth dates in addition to the person's
$REQUIRE_AUTHENTICATION = false;		// -- set this to try to force all visitors to login before they can view anything on the site
$PAGE_AFTER_LOGIN = "mygedview";		// -- Which page should be shown after successful Login
$WELCOME_TEXT_AUTH_MODE = "1";			// -- Sets which predefined of custom welcome message will be displayed on the welcome page in authentication mode
$WELCOME_TEXT_AUTH_MODE_4 = "";			// -- Customized welcome text to display on login screen if that option is chosen
$WELCOME_TEXT_CUST_HEAD = false;		// -- Use standard PGV header to display with custom welcome text
$SHOW_REGISTER_CAUTION = true;		// -- Show text about following site rules on Login-Register page
$SHOW_GEDCOM_RECORD = true;				// -- a true value will provide a link on detail pages that will
										// --allow people to view the actual lines from the gedcom file
$ALLOW_EDIT_GEDCOM = true;				//-- allow users with canEdit privileges to edit the gedcom
$SYNC_GEDCOM_FILE = true;				//-- synchronize the gedcom file with online edits.  when set to false no changes will be written to the gedcom file
$ENABLE_AUTOCOMPLETE = true;			//-- Enable Autocomplete for certain input fields
$POSTAL_CODE = true;		//-- allow users to choose where to print the postal code. True is after the city name, false is before the city name
$SUBLIST_TRIGGER_I = "200";				// -- Number of names required before Individual lists start sub-listing by first name
										// -- Set to zero to disable sub-lists
$SUBLIST_TRIGGER_F = "200";				// -- Number of names required before Family lists start sub-listing by first name
										// -- Set to zero to disable sub-lists
$SURNAME_LIST_STYLE = "style2";			// -- Surname list style.  "style2"=sortable table, "style3"=list of names in varying font sizes

$SHOW_MARRIED_NAMES = false;			// -- Option to show the married name for females in the indilist

$SHOW_ID_NUMBERS = true;				// -- Show gedcom id numbers on charts next to  names
$SHOW_LAST_CHANGE = true;				// -- Show gedcom record last change on lists
$SHOW_EST_LIST_DATES = false;		// -- Show estimated birth/death dates on individual lists
$SHOW_PEDIGREE_PLACES = "9";			// -- What level to show the birth and death places next to the birth and death dates on the pedigree and descendency charts.
$SHOW_LIST_PLACES = "1";				// -- What level of detail to display for places in a list

$MULTI_MEDIA = true;		// -- if you have no multi-media files, set this to false
$MEDIA_EXTERNAL = true;		// -- Set whether or not to change links starting with http, ftp etc.
$MEDIA_DIRECTORY = "media/";			// -- Directory where media files are stored
$MEDIA_DIRECTORY_LEVELS = "0";			// -- the number of sub-directories to keep when getting names of media files
$SHOW_HIGHLIGHT_IMAGES = true;			// -- show highlighted photos on pedigree tree and individual pages.
$USE_THUMBS_MAIN = true;				// -- for the main image on the individual page, whether or not to use the full res image or the thumbnail
$THUMBNAIL_WIDTH = "100";				// -- the width to use when automatically generating thumbnails
$AUTO_GENERATE_THUMBS = true;			// -- whether PGV should try to automatically generate thumbnails
$USE_MEDIA_VIEWER = true;				// -- If set to true, when a user clicks on an image they will be taken to the mediaviewer.php page.  If set to false a new window will open at imageview.php
$USE_MEDIA_FIREWALL = false;			// -- If set to true, enables the media firewall to serve images from the protected image directory
$MEDIA_FIREWALL_ROOTDIR = "";			// -- Dir that contains the protected image directory.  If empty, will use index dir
if (!$MEDIA_FIREWALL_ROOTDIR) $MEDIA_FIREWALL_ROOTDIR = $INDEX_DIRECTORY;
$MEDIA_FIREWALL_THUMBS = false;		// -- When an image is in the protected image directory, should the thumbnail be protected as well?
$SHOW_NO_WATERMARK = $PRIV_USER;		// -- access level for viewing non-watermarked images.  PRIV_HIDE, PRIV_PUBLIC, PRIV_USER, PRIV_NONE
$WATERMARK_THUMB = false;				// -- whether to watermark thumbnails too
$SAVE_WATERMARK_THUMB = false;		// -- whether to save copies of watermarked thumbnails
$SAVE_WATERMARK_IMAGE = false;		// -- whether to save copies of watermarked main images
$SHOW_MEDIA_FILENAME = false;			// -- show Media File Name in Media Viewer
$SHOW_MEDIA_DOWNLOAD = false;			// -- show Media Download link in Media Viewer

$HIDE_GEDCOM_ERRORS = false;			//-- a true value will disable error messages for undefined GEDCOM codes.  See the
										//-- non-standard gedcom codes section of the readme file for more information.

$WORD_WRAPPED_NOTES = false;			//-- some programs wrap notes at word boundaries while others wrap notes anywhere
										//-- setting this to true will add a space between words where they are wrapped in the gedcom
$GEDCOM_DEFAULT_TAB = "0";				//-- this setting controls which tab on the individual page should first be displayed to visitors

$SHOW_CONTEXT_HELP = true;				// show ? links on the page for contextual popup help
$PHPGEDVIEW_EMAIL = "";			// -- "From:" address for automatically generated e-mails
$CONTACT_EMAIL = "you@yourdomain.com";		// -- this is who the user should contact for more information
$CONTACT_METHOD = "messaging2";						// -- the method to allow users to contact you. options are: mailto, messaging, messaging2
$WEBMASTER_EMAIL = "webmaster@yourdomain.com";		// -- this is who the user should contact in case of errors
$SUPPORT_METHOD = "messaging2";						// -- the method to allow users to contact you. options are: mailto, messaging, messaging2
$HOME_SITE_URL = "http://www.phpgedview.net";		// -- url for your home page
$HOME_SITE_TEXT = "About PhpGedView";		// -- name of your site
$SHOW_FACT_ICONS = true;					//-- Show Fact icons on Indi page
$FAVICON = "images/favicon.ico";		// -- change to point to your favicon, either relative or absolute
$THEME_DIR = "themes/standard/";					// -- directory where display theme files are kept
if (substr ($THEME_DIR, -1) != "/") $THEME_DIR = $THEME_DIR . "/";
$ALLOW_THEME_DROPDOWN = false;		//-- allows the themes to display theme change dropdown
$SECURITY_CHECK_GEDCOM_DOWNLOADABLE = true;	//-- check for downloadability of GEDCOM

$SHOW_STATS = false;					//-- Show execution stats at the bottom of the page
$SHOW_COUNTER = false;		//-- Show hit counters on portal and individual pages
$SHOW_SPIDER_TAGLINE = false;		//-- On pages generated for search engines, name the engine as the last line
$DAYS_TO_SHOW_LIMIT = "30";			//-- Maximum number of days in Upcoming Events block
$COMMON_NAMES_THRESHOLD	= "40";		//-- The minimum number of times a surname must appear before it is shown on the most common surnames list
$COMMON_NAMES_ADD	= "";			//-- a comma seperated list of surnames the admin can add to the common surnames list
$COMMON_NAMES_REMOVE	= "";		//-- a comma seperated list of surnames to ignore in the common surnames list

$META_AUTHOR		= "";			//-- the author of the webpage leave empty to use gedcom contact user name
$META_PUBLISHER		= "";			//-- the publisher of the web page, leave empty to use gedcom contact
$META_COPYRIGHT		= "";			//-- the copyright statement, leave empty to use gedcom contact
$META_DESCRIPTION	= "";			//-- the page description, leave empty to use the gedcom title
$META_PAGE_TOPIC	= "";			//-- the page topic, leave empty to use the gedcom title
$META_AUDIENCE		= "All";			//-- the intended audience
$META_PAGE_TYPE		= "Private Homepage";	//-- the type of page
$META_ROBOTS		= "index, follow";		//-- instructions for robots
$META_REVISIT		= "10 days";			//-- how often crawlers should reindex the site
$META_KEYWORDS		= "ancestry, genealogy, pedigree tree";		//-- any aditional keywords, the most common surnames list will be appended to anything you put in
$META_TITLE			= "";			//-- optional text that can be added to the html page <title></title> line
$META_SURNAME_KEYWORDS	= true;		//-- option to use the most common surnames in the keywords field

$CHART_BOX_TAGS		= "";		//-- optional comma seperated gedcom tags to show in chart boxes
$USE_QUICK_UPDATE	= "1";		//-- who can use Quick Update: 0: nobody, 1: site admins, 2: GEDCOM admins, 3: editors
$SHOW_QUICK_RESN	= false;	//-- whether or not to show RESN tags on the quick update form
$QUICK_ADD_FACTS	= "BIRT,CHR,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,FCOM,ORDN,NATU,EMIG,IMMI,GRAD,BAPL,CONL,ENDL,SLGC,RELI";		//-- comma delimited list of facts that can be added on the quick update form
$QUICK_REQUIRED_FACTS	= "BIRT,DEAT";	//-- comma delimited list of facts that will be required by default on the quick update
$QUICK_ADD_FAMFACTS		= "MARR,DIV,SLGS";		//-- comma delimited list of facts that can be added on the quick update form for families
$QUICK_REQUIRED_FAMFACTS	= "MARR";	//-- comma delimited list of facts that will be required by on the quick update for families
$SEARCHLOG_CREATE = "none";	//-- save searches executed by users
$CHANGELOG_CREATE = "monthly";	//-- log changes applied by users
$SHOW_LDS_AT_GLANCE	= false;	//-- Show status of LDS ordinances in chart boxes
$UNDERLINE_NAME_QUOTES	= false;	//-- convert double quotes in names to underlines
$SPLIT_PLACES	= true;	//-- split PLAC tag into subtags (town, county, state...) in edit mode
$SHOW_RELATIVES_EVENTS = "_BIRT_CHIL,_BIRT_COUS,_BIRT_FSIB,_BIRT_GCHI,_BIRT_HSIB,_BIRT_MSIB,_BIRT_NEPH,_BIRT_SIBL,_DEAT_CHIL,_DEAT_COUS,_DEAT_FATH,_DEAT_FSIB,_DEAT_GCHI,_DEAT_GPAR,_DEAT_HSIB,_DEAT_MOTH,_DEAT_MSIB,_DEAT_NEPH,_DEAT_SIBL,_DEAT_SPOU,_MARR_CHIL,_MARR_COUS,_MARR_FATH,_MARR_FSIB,_MARR_GCHI,_MARR_HSIB,_MARR_MOTH,_MARR_MSIB,_MARR_NEPH,_MARR_SIBL";		//-- show events of relatives on individual page
$EXPAND_RELATIVES_EVENTS = false;
$EXPAND_SOURCES = false;
$EXPAND_NOTES = false;
$SHOW_LEVEL2_NOTES = true;			// -- Show level 2 Notes & Sources on Notes & Sources tabs
$SHOW_AGE_DIFF = false;				// -- show age diff between spouses and between children on close relatives tab
$EDIT_AUTOCLOSE = false;		//-- autoclose edit window when update successful
$NOTE_FACTS_UNIQUE = "";
$NOTE_FACTS_ADD = "ASSO,SOUR,NOTE,REPO";
$NOTE_FACTS_QUICK = "";
$SOUR_FACTS_UNIQUE = "AUTH,ABBR,TITL,PUBL,TEXT";
$SOUR_FACTS_ADD = "NOTE,OBJE,REPO,SHARED_NOTE";
$SOUR_FACTS_QUICK = "";
$REPO_FACTS_UNIQUE = "NAME,ADDR";
$REPO_FACTS_ADD = "PHON,EMAIL,FAX,WWW,NOTE,SHARED_NOTE";
$REPO_FACTS_QUICK = "";
$INDI_FACTS_UNIQUE = "";
$INDI_FACTS_ADD = "ADDR,AFN,BIRT,CHR,DEAT,BURI,CREM,ADOP,BAPM,BARM,BASM,BLES,CHRA,CONF,EMAIL,FAX,FCOM,ORDN,NATU,EMIG,IMMI,CENS,PROB,WILL,GRAD,RETI,CAST,DSCR,EDUC,IDNO,NATI,NCHI,NMR,OCCU,PROP,RELI,RESI,SSN,TITL,BAPL,CONL,ENDL,SLGC,_MILI,ASSO";
$INDI_FACTS_QUICK = "BIRT,ADDR,RESI,OCCU,DEAT";
$FAM_FACTS_UNIQUE = "NCHI,MARL,DIV,ANUL,DIVF,ENGA,MARB,MARC,MARS";
$FAM_FACTS_ADD = "CENS,MARR,RESI,SLGS,MARR_CIVIL,MARR_RELIGIOUS,MARR_PARTNERS";
$FAM_FACTS_QUICK = "MARR,DIV";
$SEARCH_FACTS_DEFAULT = "NAME:GIVN:SDX,NAME:SURN:SDX,BIRT:DATE,BIRT:PLAC,FAMS:MARR:DATE,FAMS:MARR:PLAC,DEAT:DATE,DEAT:PLAC";  //-- DEFAULT FACTS ON ADVANCED SEARCH
$GENERATE_UIDS = false;		//-- automatically generate _UID fields for records that do not already have them
$ADVANCED_NAME_FACTS = "NICK,_HEB,ROMN";
$ADVANCED_PLAC_FACTS = "";
$USE_GEONAMES = false;	// use geonames.org with autocomplite function
$SURNAME_TRADITION = "paternal";
$FULL_SOURCES=true; // Include the quality-of-data and date-of-entry-in-original-source fields
$PREFER_LEVEL2_SOURCES='1'; 	//-- When adding close relatives, which Source checkboxes are checked by default: 0: none, 1: facts, 2: record

$ENABLE_RSS = true;
$RSS_FORMAT = "ATOM";		//-- default feed format.
// -- amount of time to execute before quitting in seconds
// -- set this to 0 to remove all time limits
$TIME_LIMIT = "60";

@set_time_limit($TIME_LIMIT);

?>

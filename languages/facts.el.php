<?php
/**
 * Greek Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Nicholas G. Antimisiaris
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
 * @author Nicholas G. Antimisiaris
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
   print "You cannot access a language file directly.";
   exit;
}
// -- Define a fact array to map GEDCOM tags with their English values
$factarray["ABBR"]		= "Συντομογραφία";
$factarray["ADDR"]		= "Διεύθυνση";
$factarray["ADR1"]		= "Διεύθυνση 1";
$factarray["ADR2"]		= "Διεύθυνση 2";
$factarray["ADOP"]		= "Υιοθεσία";
$factarray["AFN"]		= "Ancestral File Number (AFN)";
$factarray["AGE"]		= "Ηλικία";
$factarray["AGNC"]		= "Agency";
$factarray["ALIA"]		= "Ψευδόνυμο";
$factarray["ANCE"]		= "Πρόγονοι";
$factarray["ANCI"]		= "Ενδιαφέροντα Προγόνων";
$factarray["ANUL"]		= "Ακύρωση";
$factarray["ASSO"]		= "Συνέταιροι";
$factarray["AUTH"]		= "Συγγραφέας";
$factarray["BAPL"]		= "LDS Βάπτιση";
$factarray["BAPM"]		= "Βάπτιση";
$factarray["BARM"]		= "Bar Mitzvah";
$factarray["BASM"]		= "Bas Mitzvah";
$factarray["BIRT"]		= "Γέννηση";
$factarray["BLES"]		= "Ευλογία";
$factarray["BLOB"]		= "Binary Data Object";
$factarray["BURI"]		= "Ταφή";
$factarray["CALN"]		= "Call Number";
$factarray["CAST"]		= "Κοινωνική τάξη";
$factarray["CAUS"]		= "Αίτια θανάτου";
$factarray["CENS"]		= "Απογραφή";
$factarray["CHAN"]		= "Τελευταία Αλλαγή";
$factarray["CHAR"]		= "Character Set";
$factarray["CHIL"]		= "Παιδί";
$factarray["CHR"]		= "Βάπτιση";
$factarray["CHRA"]		= "Βάπτιση Ενήλικα";
$factarray["CITY"]		= "Πόλη";
$factarray["CONF"]		= "Χρίσμα";
$factarray["CONL"]		= "LDS Χρίσμα";
$factarray["COPR"]		= "Πνευματική Ιδιοκτησία";
$factarray["CORP"]		= "Εταιρία";
$factarray["CREM"]		= "Αποτέφρωση";
$factarray["CTRY"]		= "Χώρα";
$factarray["DATA"]		= "Δεδομένα";
$factarray["DATE"]		= "Ημερομηνία";
$factarray["DEAT"]		= "Θάνατος";
$factarray["DESC"]		= "Απόγονοι";
$factarray["DESI"]		= "Ενδιαφέροντα Απογόνων";
$factarray["DEST"]		= "Προορισμός";
$factarray["DIV"]		= "Διαζύγιο";
$factarray["DIVF"]		= "Αίτηση Διαζυγίου";
$factarray["DSCR"]		= "Περιγραφή";
$factarray["EDUC"]		= "Εκπαίδευση";
$factarray["EMIG"]		= "Μετανάστευση";
$factarray["ENDL"]		= "LDS Προίκα";
$factarray["ENGA"]		= "Αρραβώνας";
$factarray["EVEN"]		= "Γεγονός";
$factarray["FAM"]		= "Οικογένεια";
$factarray["FAMC"]		= "Οικογένεια σαν παιδί";
$factarray["FAMF"]		= "Οικογενειακό Αρχείο";
$factarray["FAMS"]		= "Οικογένεια σαν σύζυγος";
$factarray["FCOM"]		= "Πρώτη Κοινωνία";
$factarray["FILE"]		= "Εξωτερικό Αρχείο";
$factarray["FORM"]		= "Μορφή";
$factarray["GIVN"]		= "Δοσμένα Ονόματα";
$factarray["GRAD"]		= "Αποφοίτηση";
$factarray["IDNO"]		= "Αριθμός Ταυτότητας";
$factarray["IMMI"]		= "Μετανάστευση";
$factarray["LEGA"]		= "Κληρονόμος";
$factarray["MARB"]		= "Γάμος Bann";
$factarray["MARC"]		= "Συμβόλαιο Γάμου";
$factarray["MARL"]		= "Άδεια Γάμου";
$factarray["MARR"]		= "Γάμος";
$factarray["MARS"]		= "Διακανονισμός Γάμου";
$factarray["MEDI"]		= "Τύπος πολυμέσων";
$factarray["NAME"]		= "Όνομα";
$factarray["NATI"]		= "Υπηκοότητα";
$factarray["NATU"]		= "Πολιτογράφιση";
$factarray["NCHI"]		= "Αριθμός παιδιών";
$factarray["NICK"]		= "Παρατσούκλι";
$factarray["NMR"]		= "Αριθμός γάμων";
$factarray["NOTE"]		= "Σημείωση";
$factarray["NPFX"]		= "Πρόθεμα";
$factarray["NSFX"]		= "Επίθεμα";
$factarray["OBJE"]		= "Αντικείμενο Πολυμέσων";
$factarray["OCCU"]		= "Επάγγελμα";
$factarray["ORDI"]		= "Χειροτονία";
$factarray["ORDN"]		= "Χειροτονία";
$factarray["PAGE"]		= "Λεπτομέρειες Παραπομπης";
$factarray["PEDI"]		= "Γενεαλόγιο";
$factarray["PLAC"]		= "Τόπος";
$factarray["PHON"]		= "Τηλέφωνο";
$factarray["POST"]		= "Ταχυδρομικός κώδικας";
$factarray["PROB"]		= "Επικύρωση (διαθήκης)";
$factarray["PROP"]		= "Ιδιοκτησία";
$factarray["PUBL"]		= "Δημοσίευση";
$factarray["QUAY"]		= "Ποιότητα δεδομένων";
$factarray["REPO"]		= "Χώρος εναπόθεσης";
$factarray["REFN"]		= "Αριθμός Παραπομπής";
$factarray["RELA"]		= "Συγγένεια";
$factarray["RELI"]		= "Θρήσκευμα";
$factarray["RESI"]		= "Διαμονή";
$factarray["RESN"]		= "Περιορισμός";
$factarray["RETI"]		= "Συνταξιοδότηση";
$factarray["RFN"]		= "Αριθμός αρχειοθέτησης";
$factarray["RIN"]		= "Αριθμός αρχειοθέτησης";
$factarray["ROLE"]		= "Ρόλος";
$factarray["SEX"]		= "Φύλο/γένος";
$factarray["SLGC"]		= "LDS Child Sealing";
$factarray["SLGS"]		= "LDS Spouse Sealing";
$factarray["SOUR"]		= "Πηγή";
$factarray["SPFX"]		= "Πρόθεμα Επωνύμου";
$factarray["SSN"]		= "Αριθμός Φορολογικού Μητρώου(ΑΦΜ)";
$factarray["STAE"]		= "Πολιτεία";
$factarray["STAT"]		= "Κατάσταση";
$factarray["SUBM"]		= "Submitter";
$factarray["SUBN"]		= "Submission";
$factarray["SURN"]		= "Επώνυμο";
$factarray["TEMP"]		= "Temple";
$factarray["TEXT"]		= "Text";
$factarray["TIME"]		= "Ώρα";
$factarray["TITL"]		= "Τίτλος";
$factarray["TYPE"]		= "Τύπος";
$factarray["WILL"]		= "Διαθήκη";
$factarray["_EMAIL"]	= "Ηλεκτρονικό Ταχυδρομείο";
$factarray["EMAIL"]		= "Ηλεκτρονικό Ταχυδρομείο";
$factarray["_TODO"]		= "Εργασία";
$factarray["_UID"]		= "Καθολικός αριθμός ταυτότητας";
$factarray["_PGVU"]		= "Τελευταία Αλλαγή απο";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]		= "Ιατρικά";
$factarray["_DEG"]		= "Βαθμός/Πτυχίο";
$factarray["_MILT"]		= "Στρατιωτική Υπηρεσία";
$factarray["_SEPR"]		= "Σε Διάσταση";
$factarray["_DETS"]		= "Θάνατος ενός Συζύγου";
$factarray["CITN"]		= "Υπηκοότητα";
$factarray["_FA1"]		= "Περιστατικό 1";
$factarray["_FA2"]		= "Περιστατικό 2";
$factarray["_FA3"]		= "Περιστατικό 3";
$factarray["_FA4"]		= "Περιστατικό 4";
$factarray["_FA5"]		= "Περιστατικό 5";
$factarray["_FA6"]		= "Περιστατικό 6";
$factarray["_FA7"]		= "Περιστατικό 7";
$factarray["_FA8"]		= "Περιστατικό 8";
$factarray["_FA9"]		= "Περιστατικό 9";
$factarray["_FA10"]		= "Περιστατικό 10";
$factarray["_FA11"]		= "Περιστατικό 11";
$factarray["_FA12"]		= "Περιστατικό 12";
$factarray["_FA13"]		= "Περιστατικό 13";
$factarray["_MREL"]		= "Συγγένεια με Μητέρα";
$factarray["_FREL"]		= "Συγγένεια με Πατέρα";
$factarray["_MSTAT"]	= "Αρχική Κατάσταση Γάμου";
$factarray["_MEND"]		= "Τελική Κατάσταση Γάμου";

// GEDCOM 5.5.1 related facts
$factarray["FAX"]		= "FAX";
$factarray["FACT"]		= "Περιστατικό";
$factarray["WWW"]		= "Σελίδα στο Διαδίκτυο";
$factarray["MAP"]		= "Χάρτης";
$factarray["LATI"]		= "Γεωγραφικό Πλάτος";
$factarray["LONG"]		= "Γεωγραφικό Μήκος";
$factarray["FONE"]		= "Φωνητικά";
$factarray["ROMN"]		= "Εκλατινισμένο";

// PAF related facts
$factarray["_NAME"]		= "Ονοματεπώνυμο για ταχυδρομείο";
$factarray["URL"]		= "Διεύθυνση URL";
$factarray["_HEB"]		= "Εβραϊκά";

// Rootsmagic
$factarray["_SUBQ"]		= "Περιληπτική έκδοση";
$factarray["_BIBL"]		= "Βιβλιογραφία";

// Other common customized facts
$factarray["_ADPF"]		= "Υιοθεσία από πατέρα";
$factarray["_ADPM"]		= "Υιοθεσία από μητέρα";
$factarray["_AKAN"]		= "Γνωστός και ως";
$factarray["_AKA"]		= "Γνωστός και ως";
$factarray["_BRTM"]		= "Brit mila";
$factarray["_COML"]		= "Γάμος κοινού δικαίου";
$factarray["_EYEC"]		= "Χρώμα ματιών";
$factarray["_FNRL"]		= "Κηδεία";
$factarray["_HAIR"]		= "Χρώμα μαλλιών";
$factarray["_HEIG"]		= "Ύψος";
$factarray["_INTE"]		= "Interred";
$factarray["_MARI"]		= "Πρόθεση Γάμου";
$factarray["_MBON"]		= "Marriage bond";
$factarray["_MEDC"]		= "Ιατρική κατάσταση";
$factarray["_MILI"]		= "Στρατιωτικό";
$factarray["_NMR"]		= "Δεν είναι παντρεμένος/η";
$factarray["_NLIV"]		= "Δεν ζει";
$factarray["_NMAR"]		= "Δεν παντρεύτηκε ποτέ";
$factarray["_PRMN"]		= "Permanent Number";
$factarray["_WEIG"]		= "Βάρος";
$factarray["_YART"]		= "Yartzeit";
$factarray["_MARNM"]	= "Όνομα γάμου";
$factarray["_STAT"]		= "Κατάσταση γάμου";
$factarray["COMM"]		= "Σχόλιο";

if (file_exists("languages/facts.el.extra.php")) require "languages/facts.el.extra.php";

?>

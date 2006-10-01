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
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
      print "You cannot access a language file directly.";
      exit;
}
//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]									= ";";
$pgv_lang["qm_ah"]								= ";";
$pgv_lang["page_help"]							= "Βοήθεια";
$pgv_lang["help_for_this_page"]					= "Βοήθεια για τη σελίδα αυτή";
$pgv_lang["help_contents"]						= "Περιεχέμενα Βοήθειας";
$pgv_lang["show_context_help"]					= "Απεικόνιση Εξειδικευμένης Βοήθειας";
$pgv_lang["hide_context_help"]					= "Απόκρυψη Εξειδικευμένης Βοήθειας";
$pgv_lang["sorry"]								= "<b>Συγνώμη, δεν έχουμε ολοκληρώσει το κείμενο βοήθειας για την σελίδα αυτή ή για το συγκεκριμένο θέμα</b>";
$pgv_lang["help_not_exist"]						= "<b>Το κείμενο βοήθειας, για την σελίδα αυτή ή για το θέμα αυτό, δεν είναι διαθέσιμο ακόμη</b>";
$pgv_lang["resolution"]							= "Ανάλυση Οθόνης";
$pgv_lang["menu"]								= "Μενού";
$pgv_lang["header"]								= "Επικεφαλίδα";
$pgv_lang["imageview"]							= "Θέαση Φωτογραφιών";

//-- CONFIG FILE MESSAGES
$pgv_lang["login_head"]							= "PhpGedView Σύνδεση Χρήστη";
$pgv_lang["error_title"]						= "ΣΦΑΛΜΑ: Δεν ανοίγει το αρχείο GEDCOM";
$pgv_lang["error_header"]						= "Το αρχείο GEDCOM, [#GEDCOM#], δεν υπάρχει στο συγκεκριμένο χώρο.";
$pgv_lang["error_header_write"]					= "Το αρχείο GEDCOM, [#GEDCOM#], δεν είναι εγγράψιμο. Ελέγξτε τις ιδιότητες και τα δικαιώματα πρόσβασης.";
$pgv_lang["for_support"]						= "Για τεχνική υποστήριξη και πληροφορίες επικοινωνήστε με";
$pgv_lang["for_contact"]						= "Για βοήθεια με γενεαλογικές ερωτήσεις, παρακαλώ επικοινωνήστε με";
$pgv_lang["for_all_contact"]					= "Για τεχνική υποστήριξη ή γενεαλογικές ερωτήσεις, παρακαλώ επικοινωνήστε με";
$pgv_lang["build_title"]						= "Δημιουργία Αρχείων Ευρετηρίου";
$pgv_lang["build_error"]						= "Το αρχείο GEDCOM έχει ενημερωθεί.";
$pgv_lang["please_wait"]						= "Παρακαλώ περιμένετε να ενημερωθούν τα αρχεία ευρετηρίου.";
$pgv_lang["choose_gedcom"]						= "Επιλέξτε ένα αρχεία GEDCOM";
$pgv_lang["username"]							= "Ψευδώνυμο Χρήστη";
$pgv_lang["invalid_username"]					= "Το ψευδώνυμο χρήστη περιέχει απαγορευμένους χαρακτήρες";
$pgv_lang["fullname"]							= "Ονοματεπώνυμο";
$pgv_lang["password"]							= "Μυστικός Κωδικός Πρόσβασης";
$pgv_lang["confirm"]							= "Επιβεβαίωση μυστικού Κωδικού Πρόσβασης";
$pgv_lang["user_contact_method"]				= "Προτιμητέα Μέθοδος Επικοινωνίας";
$pgv_lang["login"]								= "Σύνδεση Χρήστη";
$pgv_lang["login_aut"]							= "Διαμόρφωση Χρήστη";
$pgv_lang["logout"]								= "Αποσύνδεση";
$pgv_lang["admin"]								= "Διαχείριση";
$pgv_lang["logged_in_as"]						= "Συνδεδεμένος ως";
$pgv_lang["my_pedigree"]						= "Το Γενεαλόγιο μου";
$pgv_lang["my_indi"]							= "Η Καρτέλα μου";
$pgv_lang["yes"]								= "Ναι";
$pgv_lang["no"]									= "Όχι";
$pgv_lang["add_gedcom"]							= "Προσθήκη GEDCOM";
$pgv_lang["no_support"]							= "Έχει εντοπιστεί ότι ο browser που χρησιμοποιείτε δεν υποστηρίζει τα πρωτόκολλα που χρησιμοποιούνται από την εφαρμογή PhpGedView. Οι περισσσότεροι browsers υποστηρίζουν αυτά τα πρωτόκολλα σε νεώτερες εκδόσεις τους. Παρακαλώ όπως αναβαθμίστε το browser σας σε νεώτερη έκδοση.";
$pgv_lang["change_theme"]						= "Αλλαγή Θεματικής Απεικόνισης";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]						= "Γενεαλογικό Δέντρο";
$pgv_lang["gen_ped_chart"]						= "Γενεαλογικό Δέντρο #PEDIGREE_GENERATIONS# Γενεών";
$pgv_lang["generations"]						= "Γενεές";
$pgv_lang["view"]								= "Εμφάνιση";
$pgv_lang["fam_spouse"]							= "Οικογένεια με σύζυγο";
$pgv_lang["root_person"]						= "Κωδικός Ατόμου Ρίζας";
$pgv_lang["hide_details"]						= "Επόκρυψη Λεπτομεριών";
$pgv_lang["show_details"]						= "Εμφάνιση Λεπτομεριών";
$pgv_lang["person_links"]						= "Σύνδεση με διαγράμματα, οικογένειες, και στενούς συγγενείς του ατόμου αυτού. Επιλέξτε το εικονίδιο αυτό για να δείτε την αντίστοιχη σελίδα εστιασμένη στο άτομο αυτό.";
$pgv_lang["zoom_box"]							= "Μεγένθυση/Σμίκρυνση στο κουτί αυτό.";
$pgv_lang["portrait"]							= "Portrait";
$pgv_lang["landscape"]							= "Landscape";
$pgv_lang["start_at_parents"]					= "Εκκίνηση από γονείς";
$pgv_lang["charts"]								= "Διαγράμματα";
$pgv_lang["lists"]								= "Κατάλογοι";
$pgv_lang["welcome_page"]						= "Σελίδα Καλωσορίσματος";
$pgv_lang["max_generation"]						= "Ο μέγιστος αριθμός γενεών στο γενεαλόγιο είναι #PEDIGREE_GENERATIONS#.";
$pgv_lang["min_generation"]						= "Ο ελάχιστος αριθμός γενεών στο γενεαλόγιο είναι 3.";
$pgv_lang["box_width"]							= "Μήκος κουτιού";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]				= "Δεν υπάρχει οικογένεια με κωδικό";
$pgv_lang["unable_to_find_indi"]				= "Δεν υπάρχει άτομο με κωδικό";
$pgv_lang["unable_to_find_record"]				= "Δεν υπάρχει εγγραφή με κωδικό";
$pgv_lang["unable_to_find_source"]				= "Δεν υπάρχει πηγή με κωδικό";
$pgv_lang["unable_to_find_repo"]				= "Unable to find Repository with id";
$pgv_lang["repo_name"]							= "Όνομα Αποθηκοφυλακίου:";
$pgv_lang["address"]							= "Διεύθυνση:";
$pgv_lang["phone"]								= "Τηλέφωνο:";
$pgv_lang["source_name"]						= "Όνομα Πηγής:";
$pgv_lang["title"]								= "Τίτλος";
$pgv_lang["author"]								= "Συγγραφέας:";
$pgv_lang["publication"]						= "Δημοσίευση:";
$pgv_lang["call_number"]						= "Call Number:";
$pgv_lang["living"]								= "Εν ζωή";
$pgv_lang["private"]							= "Προσωπικά";
$pgv_lang["birth"]								= "Γέννηση:";
$pgv_lang["death"]								= "Θάνατος:";
$pgv_lang["descend_chart"]						= "Διάγραμμα Απογόνων";
$pgv_lang["individual_list"]					= "Κατάλογος Ατόμων";
$pgv_lang["family_list"]						= "Κατάλογος Οικογενειών";
$pgv_lang["source_list"]						= "Κατάλογος Πληροφοριακών Πηγών";
$pgv_lang["place_list"]							= "Ιεραρχία Τόπων";
$pgv_lang["place_list_aft"]						= "Place Hierarchy after";
$pgv_lang["media_list"]							= "Κατάλογος Πολυμέσων";
$pgv_lang["search"]								= "Αναζήτηση";
$pgv_lang["clippings_cart"]						= "Καλάθι Αποκομμάτων Οικογενειακού Δέντρου";
$pgv_lang["not_an_array"]						= "Δεν είναι Κατάλογος";
$pgv_lang["print_preview"]						= "Απεικόνιση για εκτύπωση";
$pgv_lang["cancel_preview"]						= "Επιστροφή σε κανονική θέαση";
$pgv_lang["change_lang"]						= "Αλλαγή Γλώσσας";
$pgv_lang["print"]								= "Εκτύπωση";
$pgv_lang["total_queries"]						= "Συνολικές Ανακρίσεις Βάσης Δεδομένων: ";
$pgv_lang["total_privacy_checks"]				= "Σύνολο ελέγχων προστασίας προσωπικών δεδομένων: ";
$pgv_lang["back"]								= "Πίσω";
$pgv_lang["privacy_list_indi_error"]			= "Για λόγους προστασίας προσωπικών δεδομένων, ένα ή περισσότερα άτομα έχουν κρυφτεί.";
$pgv_lang["privacy_list_fam_error"]				= "Για λόγους προστασίας προσωπικών δεδομένων, μια ή περισσότερες οικογένειες έχουν κρυφτεί.";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["aka"]								= "Παρατσούκλι(α)";
$pgv_lang["male"]								= "Άρρεν";
$pgv_lang["female"]								= "Θήλυ";
$pgv_lang["temple"]								= "LDS Temple";
$pgv_lang["temple_code"]						= "LDS Temple Code:";
$pgv_lang["status"]								= "Κατάσταση";
$pgv_lang["source"]								= "Πηγή";
$pgv_lang["citation"]							= "Citation:";
$pgv_lang["text"]								= "Source Text:";
$pgv_lang["note"]								= "Σημείωση";
$pgv_lang["NN"]									= "(άγνωστος/η)";
$pgv_lang["PN"]									= "(άγνωστος/η)";
$pgv_lang["unrecognized_code"]					= "Μη αναγνωρίσιμος κωδικός GEDCOM";
$pgv_lang["unrecognized_code_msg"]				= "Αυτό είναι σφάλμα της εφαρμογής, και θέλουμε να το διορθώσουμε. Παρκαλώ όπως το αναφέρετε στον";
$pgv_lang["indi_info"]							= "Πληροφορίες Ατόμου";
$pgv_lang["pedigree_chart"]						= "Γενεαλογικό Διάγραμμα";
$pgv_lang["desc_chart2"]						= "Διάγραμμα Απογόνων";
$pgv_lang["family"]								= "Οικογένεια";
$pgv_lang["family_with"]						= "Οικογένεια με";
$pgv_lang["as_spouse"]							= "Οικογένεια με Σύζυγο";
$pgv_lang["as_child"]							= "Οικογένεια με Γονείς";
$pgv_lang["view_gedcom"]						= "Εμφάνιση πεδίων GEDCOM";
$pgv_lang["add_to_cart"]						= "Προσθήκη στο Καλάθι";
$pgv_lang["still_living_error"]					= "Το άτομο αυτό είτε είναι εν ζωή είτε δεν έχει καταγεγραμμένη ημερομηνία γέννησης ή ημερομηνία θανάτου.   Οι λεπτομέρειες ατόμων εν ζωή έχουν κρυφτεί από δημόσια θέαση.<br />Για περισσότερες πληροφορίες επικοινωνήστε με";
$pgv_lang["privacy_error"]						= "Λεπτομέρειες για το άτομο αυτό είναι προστατευμένες.<br />";
$pgv_lang["more_information"]					= "Για περισσότερες πληροφορίες επικοινωνήστε με";
$pgv_lang["name"]								= "Όνομα";
$pgv_lang["given_name"]							= "Δωσμένο Όνομα:";
$pgv_lang["surname"]							= "Επώνυμο:";
$pgv_lang["suffix"]								= "Επίθεμα:";
$pgv_lang["object_note"]						= "Object Σημειώσεις:";
$pgv_lang["sex"]								= "Φύλο/γένος";
$pgv_lang["personal_facts"]						= "Προσωπικά Στοιχεία και Λεπτομέρειες";
$pgv_lang["type"]								= "Τύπος";
$pgv_lang["date"]								= "Ημερομηνία";
$pgv_lang["place_description"]					= "Τόπος / Περιγραφή";
$pgv_lang["parents"]							= "Γονείς:";
$pgv_lang["siblings"]							= "Αδέλφια";
$pgv_lang["father"]								= "Πατέρας";
$pgv_lang["mother"]								= "Μητέρα";
$pgv_lang["relatives"]							= "Στενοί Συγγενείς";
$pgv_lang["child"]								= "Παιδί";
$pgv_lang["spouse"]								= "Σύζυγος";
$pgv_lang["surnames"]							= "Επώνυμα";
$pgv_lang["adopted"]							= "Υιοθετημένος/η";
$pgv_lang["foster"]								= "Θετός";
$pgv_lang["sealing"]							= "Sealing";
$pgv_lang["link_as"]							= "Σύνδεση του ατόμου αυτού σε υπάρχουσα οικογένεια ως ";
$pgv_lang["no_tab1"]							= "Δεν υπάρχουν στοιχεία για το άτομο αυτό.";
$pgv_lang["no_tab2"]							= "Δεν υπάρχουν σημειώσεις για το άτομο αυτό.";
$pgv_lang["no_tab3"]							= "Δεν έχουν καταχωρηθεί πληροφοριακές πηγές για το άτομο αυτό.";
$pgv_lang["no_tab4"]							= "Δεν υπάρχουν φωτογραφίες για το άτομο αυτό.";
$pgv_lang["no_tab5"]							= "Δεν υπάρχουν στενοί συγγενείς για το άτομο αυτό.";
$pgv_lang["no_tab6"]							= "Δεν υπάρχουν ημερολόγια έρευνας συνδεδεμένα με το άτομο αυτό.";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]						= "Πληροφορίες για την Οικογένεια";
$pgv_lang["family_group_info"]					= "Πληροφορίες για την Οικογενειακή Ομάδα";
$pgv_lang["husband"]							= "Σύζυγος-Άνδρας";
$pgv_lang["wife"]								= "Σύζυγος-Γυναίκα";
$pgv_lang["marriage"]							= "Γάμος:";
$pgv_lang["lds_sealing"]						= "LDS Sealing:";
$pgv_lang["marriage_license"]					= "Άδεια Γάμου:";
$pgv_lang["media_object"]						= "Αντικείμενο Πολυμέσων:";
$pgv_lang["children"]							= "Παιδιά";
$pgv_lang["no_children"]						= "Δεν έχουν καταχωρηθεί παιδιά";
$pgv_lang["parents_timeline"]					= "Εμφάνιση ζευγαριού στο <br />χρονοδιάγραμμα";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]							= "Καλάθι Αποκομμάτων";
$pgv_lang["clip_explaination"]					= "Το Καλάθι Αποκομμάτων Οικογενειακού Δέντρου επιτρέπει την ανάκτηση &quot;αποκομμάτων&quot; από αυτό το οικογενειακό δέντρο και την δημιουργία ενός αρχείου GEDCOM που μπορείτε να κατεβάσετε στον υπολογιστή σας.<br /><br />";
$pgv_lang["item_with_id"]						= "Αντικείμενο με κωδικό id";
$pgv_lang["error_already"]						= "είναι ήδη στο καλάθι αποκομμάτων.";
$pgv_lang["which_links"]						= "Ποιές συνδέσεις από την οικογένεια αυτή θέλετε να προστέσετε;";
$pgv_lang["just_family"]						= "Προσθήκη μόνο αυτού του οικογενειακού στοιχείου.";
$pgv_lang["parents_and_family"]					= "Προσθήκη των γονέων με αυτό το οικογενειακό στοιχείο.";
$pgv_lang["parents_and_child"]					= "Προσθήκη γονέων και στοιχείων παιδιών με αυτό το οικογενειακό στοιχείο.";
$pgv_lang["parents_desc"]						= "Προσθήκη γονέων και στοιχείων απογόνων με αυτό το οικογενειακό στοιχείο.";
$pgv_lang["continue"]							= "Άλλες Προσθήκες";
$pgv_lang["which_p_links"]						= "Ποιές άλλες συνδέσεις από αυτό το άτομο θα θέλατε επίσης να προστέσετε?";
$pgv_lang["just_person"]						= "Προσθήκη μόνο αυτού του ατόμου.";
$pgv_lang["person_parents_sibs"]				= "Προσθήκη του ατόμου αυτού, των γονιών του, και των αδελφών του.";
$pgv_lang["person_ancestors"]					= "Προσθήκη του ατόμου αυτού και των προγόνων του.";
$pgv_lang["person_ancestor_fams"]				= "Προσθήκη του ατόμου αυτού και των προγόνων του, και των οικογενειών τους.";
$pgv_lang["person_spouse"]						= "Προσθήκη του ατόμου αυτού, του/της συζύγου του, και των παιδιών.";
$pgv_lang["person_desc"]						= "Προσθήκη του ατόμου αυτού, της/του συζύγου, και όλων στοιχείων των απογόνων.";
$pgv_lang["unable_to_open"]						= "Αδυναμία εγγραφής στον κατάλογο αποκομμάτων.";
$pgv_lang["person_living"]						= "Το άτομο αυτό είναι εν ζωή. Ατομικές λεπτομέρειες δεν θα συμπεριληφθούν.";
$pgv_lang["person_private"]						= "Λεπτομέρειες για το άτομο αυτό είναι πριβέ. Ατομικές λεπτομέρειες δεν θα συμπεριληφθούν.";
$pgv_lang["family_private"]						= "Λεπτομέρειες για την οικογένεια αυτή είναι πριβέ. Οικογενειακές λεπτομέρειες δεν θα συμπεριληφθούν.";
$pgv_lang["download"]							= "Πατήστε το δεξί πλήκτρο του ποντικιού (control-click για Mac) στις παρακάτω διασυνδέσεις και επιλέξτε &quot;Save target as&quot; για να ληφθούν τα αρχεία.";
$pgv_lang["media_files"]						= "Αρχεία Φωτογραφιών που αναφέρονται σε αυτό το αρχείο GEDCOM";
$pgv_lang["cart_is_empty"]						= "Το καλάθι αποκομμάτων είναι άδειο.";
$pgv_lang["id"]									= "Κωδικός ID";
$pgv_lang["name_description"]					= "Όνομα / Περιγραφή";
$pgv_lang["remove"]								= "Διαγραφή";
$pgv_lang["empty_cart"]							= "Άδειο Καλάθι";
$pgv_lang["download_now"]						= "Λήψη Τώρα";
$pgv_lang["indi_downloaded_from"]				= "Το Άτομο αυτό έχει ληφθεί από:";
$pgv_lang["family_downloaded_from"]				= "Η Οικογένεια αυτή έχει ληφθεί από:";
$pgv_lang["source_downloaded_from"]				= "Η Πηγή αυτή έχει ληφθεί από:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]						= "Place connections found";
$pgv_lang["top_level"]							= "Ανώτατο Επίπεδο";
$pgv_lang["form"]								= "Τοποθεσίες έχουν καταγραφεί στη μορφή: ";
$pgv_lang["default_form"]						= "Πόλη, County, Πολιτεία/Νομός, Χώρα";
$pgv_lang["default_form_info"]					= "(Default)";
$pgv_lang["gedcom_form_info"]					= "(GEDCOM)";
$pgv_lang["unknown"]							= "άγνωστος/η";
$pgv_lang["individuals"]						= "Άτομα";
$pgv_lang["view_records_in_place"]				= "Θέαση όλων των στοιχείων που υπάρχουν στο τόπο αυτό";
$pgv_lang["place_list2"]						= "Κατάλογος Τοποθεσιών";
$pgv_lang["show_place_hierarchy"]				= "Εμφάνιση Τοποθεσιών σε Ιεραρχία";
$pgv_lang["show_place_list"]					= "Απεικόνιση όλων των τόπων σε κατάλογο";
$pgv_lang["total_unic_places"]					= "Σύνολο μονοσήμαντων τόπων";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["multi_title"]						= "Κατάλογος Πολυμέσων";
$pgv_lang["media_found"]						= "Αντικείμενα Πολυμέσων βρέθηκαν";
$pgv_lang["view_person"]						= "Εμφάνιση Ατόμου";
$pgv_lang["view_family"]						= "Εμφάνιση Οικογένειας";
$pgv_lang["view_source"]						= "Εμφάνιση Πηγής";
$pgv_lang["prev"]								= "&lt; Προηγούμενο";
$pgv_lang["next"]								= "Επόμενο &gt;";
$pgv_lang["file_not_found"]						= "Το αρχείο δεν υπάρχει.";
$pgv_lang["medialist_show"]						= "Εμφάνιση";
$pgv_lang["per_page"]							= "αντικείμενα πολυμέσων ανά σελίδα";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]						= "Αναζήτηση στο αρχείο GEDCOM";
$pgv_lang["enter_terms"]						= "Πληκτρολογήστε Παραμέτρους Αναζήτησης";
$pgv_lang["soundex_search"]						= "- Ή κάντε Αναζήτηση με βάση τον τρόπο που νομίζεται ότι γράφετε φωνητικά (Soundex):";
$pgv_lang["sources"]							= "Πηγές";
$pgv_lang["firstname_search"]					= "Όνομα";
$pgv_lang["lastname_search"]					= "Επώνυμο";
$pgv_lang["search_place"]						= "Τόπος";
$pgv_lang["search_year"]						= "Έτος";
$pgv_lang["no_results"]							= "Δεν βρέθηκαν αποτελέσματα.";
$pgv_lang["invalid_search_input"]				= "Παρακαλώ δώστε Όνομα, Επώνυμο ή Τοποθεσία \\n\\t καθώς και Έτος";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]						= "Πηγές βρέθηκαν";
$pgv_lang["titles_found"]						= "Τίτλοι";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]						= "Πληροφορίες Πηγής";
$pgv_lang["other_records"]						= "Στοιχεία που διασυνδέονται στη πηγή αυτή:";
$pgv_lang["people"]								= "Άτομα";
$pgv_lang["families"]							= "Οικογένειες";
$pgv_lang["total_sources"]						= "Συνολικές Πηγές";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]						= "Δημιουργία Ευρετηρίων Ατόμων και Οικογενειών";
$pgv_lang["building_index"]						= "Δημιουργία Καταλόγων Ευρετηρίου";
$pgv_lang["invalid_gedformat"]					= "Invalid GEDCOM 5.5 format";
$pgv_lang["importing_records"]					= "Εισαγωγή Στοιχείων στη Βάση Δεδομένων";
$pgv_lang["detected_change"]					= "Η εφαρμογή PhpGedView έχει ανιχνεύση αλλαγή στο GEDCOM αρχείο #GEDCOM#. Πρέπει να ενημερωθούν τα αρχεία ευρετηρίου πριν επιτραπούν άλλες ενέργειες.";
$pgv_lang["please_be_patient"]					= "ΠΑΡΑΚΑΛΩ ΥΠΟΜΟΝΗ";
$pgv_lang["reading_file"]						= "Επεξεργασία Αρχείου GEDCOM";
$pgv_lang["flushing"]							= "Flushing contents";
$pgv_lang["found_record"]						= "Βρέθηκαν στοιχεία";
$pgv_lang["exec_time"]							= "Συνολικός Χρόνος Εκτέλεσης:";
$pgv_lang["unable_to_create_index"]				= "Unable to create index file.   Make sure write permissions are available to the PhpGedViewDirectory.  Permissions may be restored once index files are written.";
$pgv_lang["indi_complete"]						= "Individual Index file update complete.";
$pgv_lang["family_complete"]					= "Family Index file update complete.";
$pgv_lang["source_complete"]					= "Source Index file update complete.";
$pgv_lang["tables_exist"]						= "PhpGedView Tables already exist in the database";
$pgv_lang["you_may"]							= "Μπορείτε:";
$pgv_lang["drop_tables"]						= "Διαγραφή των υπαρχόντων πινάκων της βάσης δεδομένων";
$pgv_lang["import_multiple"]					= "Import and work with multiple GEDCOMs";
$pgv_lang["explain_options"]					= "If you choose to drop the tables all of the data will be replaced with this GEDCOM.<br />If you choose to import and work with multiple GEDCOMs, PhpGedView will erase any data that was imported using a GEDCOM with the same file name.  This option allows you to store multiple GEDCOM data in the same tables and easily switch between them.";
$pgv_lang["path_to_gedcom"]						= "Enter the path to your GEDCOM file:";
$pgv_lang["gedcom_title"]						= "Enter a title that describes the data in this GEDCOM file";
$pgv_lang["dataset_exists"]						= "A GEDCOM with this filename has already been imported into the database.";
$pgv_lang["empty_dataset"]						= "Do you want to erase the old data and replace it with this new data?";
$pgv_lang["index_complete"]						= "Index Complete.";
$pgv_lang["click_here_to_go_to_pedigree_tree"]	= "Click here to go the the pedigree tree.";
$pgv_lang["updating_is_dead"]					= "Updating is dead status for INDI ";
$pgv_lang["import_complete"]					= "Import Complete";
$pgv_lang["updating_family_names"]				= "Updating family names for FAM ";
$pgv_lang["processed_for"]						= "Επεξεργασία αρχείου για ";
$pgv_lang["run_tools"]							= "Do you want to run one of the following tools on your GEDCOM before it is imported:";
$pgv_lang["addmedia"]							= "Add Media Tool";
$pgv_lang["dateconvert"]						= "Εργαλείο Μετατροπής Ημερομηνίας";
$pgv_lang["xreftorin"]							= "Convert XREF IDs to RIN number";
$pgv_lang["tools_readme"]						= "See the tools secion of the #README.TXT# file for more information.";
$pgv_lang["sec"]								= "δευτερόλεπτα";
$pgv_lang["bytes_read"]							= "Bytes Διαβάστηκαν:";
$pgv_lang["created_indis"]						= "Επιτυχής δημιουργία <i>Individuals</i> πίνακα.";
$pgv_lang["created_indis_fail"]					= "Ανεπιτυχής δημιουργία <i>Individuals</i> πίνακα.";
$pgv_lang["created_fams"]						= "Επιτυχής δημιουργία <i>Families</i> πίνακα.";
$pgv_lang["created_fams_fail"]					= "Ανεπιτυχής δημιουργία <i>Families</i> πίνακα.";
$pgv_lang["created_sources"]					= "Επιτυχής δημιουργία <i>Sources</i> πίνακα.";
$pgv_lang["created_sources_fail"]				= "Ανεπιτυχής δημιουργία <i>Sources</i> πίνακα.";
$pgv_lang["created_other"]						= "Successfully created <i>Other</i> table.";
$pgv_lang["created_other_fail"]					= "Unable to create <i>Other</i> table.";
$pgv_lang["created_places"]						= "Successfully created <i>Places</i> table.";
$pgv_lang["created_places_fail"]				= "Unable to create <i>Places</i> table.";
$pgv_lang["import_progress"]					= "Πρόοδος Διαδικασίας Εισαγωγής...";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"]							= "Σύνολο Οικογενειών";
$pgv_lang["total_indis"]						= "Σύνολο Ατόμων";
$pgv_lang["starts_with"]						= "Starts With:";
$pgv_lang["person_list"]						= "Κατάλογος Ατόμων:";
$pgv_lang["paste_person"]						= "Επικόλληση Ατόμου";
$pgv_lang["notes_sources_media"]				= "Σημειώσεις, Πηγές, και Πολυμέσα";
$pgv_lang["notes"]								= "Σημειώσεις";
$pgv_lang["ssourcess"]							= "Πηγές";
$pgv_lang["media"]								= "Πολυμέσα";
$pgv_lang["name_contains"]						= "Το όνομα περιέχει:";
$pgv_lang["filter"]								= "Φίλτράρισμα";
$pgv_lang["find_individual"]					= "Εύρεση Ατόμου με ID";
$pgv_lang["find_familyid"]						= "Εύρεση Οικογένειας με ID";
$pgv_lang["find_sourceid"]						= "Εύρεση Πηγής με ID";
$pgv_lang["skip_surnames"]						= "Skip Surname Lists";
$pgv_lang["show_surnames"]						= "Εμφάνιση Καταλόγου Επωνύμων";
$pgv_lang["all"]								= "ΌΛΑ";
$pgv_lang["hidden"]								= "Κρυφό";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]								= "Ηλικία";
$pgv_lang["timeline_title"]						= "PhpGedView Χρονοδιάγραμμα";
$pgv_lang["timeline_chart"]						= "Χρονοδιάγραμμα";
$pgv_lang["remove_person"]						= "Διαγραφή Ατόμου";
$pgv_lang["show_age"]							= "Εμφάνιση Σήμανσης Ηλικιών";
$pgv_lang["add_another"]						= "Προσθήκη και άλλου ατόμου στο καλάθι:<br />Κωδικός Ατόμου ID:";
$pgv_lang["find_id"]							= "Εύρεση ID";
$pgv_lang["show"]								= "Εμφάνιση";
$pgv_lang["year"]								= "Έτος:";
$pgv_lang["timeline_instructions"]				= "In the most recent browsers you can click and drag the boxes around on the chart.";
$pgv_lang["zoom_in"]							= "Μεγένθυνση";
$pgv_lang["zoom_out"]							= "Σμίκρυνση";

//-- MONTH NAMES
$pgv_lang["jan"]								= "Ιανουάριος";
$pgv_lang["feb"]								= "Φεβρουάριος";
$pgv_lang["mar"]								= "Μάρτιος";
$pgv_lang["apr"]								= "Απρίλιος";
$pgv_lang["may"]								= "Μάϊος";
$pgv_lang["jun"]								= "Ιούνιος";
$pgv_lang["jul"]								= "Ιούλιος";
$pgv_lang["aug"]								= "Αύγουστος";
$pgv_lang["sep"]								= "Σεπτέμβριος";
$pgv_lang["oct"]								= "Οκτώβριος";
$pgv_lang["nov"]								= "Νοέμβριος";
$pgv_lang["dec"]								= "Δεκέμβριος";
$pgv_lang["abt"]								= "περίπου";
$pgv_lang["aft"]								= "μετά";
$pgv_lang["and"]								= "και";
$pgv_lang["bef"]								= "πριν";
$pgv_lang["bet"]								= "ανάμεσα";
$pgv_lang["cal"]								= "calculated";
$pgv_lang["est"]								= "υπολογίζεται";
$pgv_lang["from"]								= "από";
$pgv_lang["int"]								= "interpreted";
$pgv_lang["to"]									= "έως";
$pgv_lang["cir"]								= "circa";
$pgv_lang["apx"]								= "περιπ.";

//-- Admin File Messages
$pgv_lang["select_an_option"]					= "Επιλέξτε ένα από τα ακόλουθα:";
$pgv_lang["readme_documentation"]				= "README Κείμενο Τεκμηρίωσης";
$pgv_lang["configuration"]						= "Διαμόρφωση";
$pgv_lang["rebuild_indexes"]					= "Rebuild Indexes";
$pgv_lang["user_admin"]							= "Διαχείριση Χρηστών";
$pgv_lang["user_created"]						= "Ο Χρήστης δημιουργήθηκε επιτυχώς.";
$pgv_lang["user_create_error"]					= "Unable to add user.  Please go back and try again.";
$pgv_lang["password_mismatch"]					= "Οι Κωδικοί δεν ταυτίζονται.";
$pgv_lang["enter_username"]						= "Πρέπει να εισάγετε ψευδώνυμο.";
$pgv_lang["enter_fullname"]						= "Πρέπει να εισάγετε πλήρης ονοματεπώνυμο.";
$pgv_lang["enter_password"]						= "Πρέπει να εισάγετε (μυστικό) κωδικό.";
$pgv_lang["confirm_password"]					= "Πρέπει να επιβεβαιώστε τον (μυστικό) κωδικό.";
$pgv_lang["update_user"]						= "Ενημέρωση Λογαριασμού Χρήστη";
$pgv_lang["update_myaccount"]					= "Update MyAccount";
$pgv_lang["save"]								= "Αποθήκευση";
$pgv_lang["delete"]								= "Διαγραφή";
$pgv_lang["edit"]								= "Διαμόρφωση/Αλλαγή";
$pgv_lang["full_name"]							= "Ονοματεπώνυμο";
$pgv_lang["visibleonline"]						= "Visible to other users when online";
$pgv_lang["editaccount"]						= "Allow this user to edit their account information";
$pgv_lang["admin_gedcom"]						= "Διαχείριση GEDCOM";
$pgv_lang["confirm_user_delete"]				= "Σίγουρα θέλετε να διαγράψτε τον χρήστη";
$pgv_lang["create_user"]						= "Δημιουργία Χρήστη";
$pgv_lang["no_login"]							= "Αδυναμία πιστοποίησης χρήστη.";
$pgv_lang["import_gedcom"]						= "Εισαγωγή Αρχείου GEDCOM";
$pgv_lang["duplicate_username"]					= "Υπάρχον Ψεύδώνυμο. Υπάρχει ήδη χρήστης με το ψευδώνυμο αυτό.  Παρακαλώ όπως πάτε πίσω και επιλέξτε άλλο ψευδώνυμο.";
$pgv_lang["gedcomid"]							= "GEDCOM INDI record ID";
$pgv_lang["enter_gedcomid"]						= "You must enter a GEDCOM ID.";
$pgv_lang["user_info"]							= "My User Information";
$pgv_lang["rootid"]								= "Pedigree Chart Root Person";
$pgv_lang["download_gedcom"]					= "Λήψη Αρχείου GEDCOM";
$pgv_lang["upload_gedcom"]						= "Αποστολή Αρχείου GEDCOM";
$pgv_lang["add_new_gedcom"]						= "Δημιουργία Νέου Αρχείου GEDCOM";
$pgv_lang["GEDCOM_file"]						= "Αρχείο GEDCOM:";
$pgv_lang["enter_filename"]						= "Πρέπει να πληκτρολογήστε όνομα αρχείου GEDCOM.";
$pgv_lang["file_not_exists"]					= "Το όνομα αρχείου που πληκτρολογήσατε δεν υπάρχει.";
$pgv_lang["file_exists"]						= "There is already a GEDCOM with that file name. Please choose a different file name or delete the old file.";
$pgv_lang["new_gedcom_title"]					= "Γενεαλογία από [#GEDCOMFILE#]";
$pgv_lang["upload_error"]						= "Υπήρχε σφάλμα κατά την αποστολή του αρχείου.";
$pgv_lang["upload_help"]						= "Select a file from your local computer to upload to your server.  All files will be uploaded to the directory:";
$pgv_lang["add_gedcom_instructions"]			= "Enter a filename for this new GEDCOM.  The new GEDCOM file will be created in the Index directory: ";
$pgv_lang["file_success"]						= "Η Αποστολή Αρχείου ολοκληρώθηκε με επιτυχία";
$pgv_lang["file_too_big"]						= "Uploaded file exceeds the allowed size";
$pgv_lang["file_partial"]						= "File was only partially uploaded, please try again";
$pgv_lang["file_missing"]						= "No file was received. Upload again.";
$pgv_lang["manage_gedcoms"]						= "Διαχείριση GEDCOMs και Διαμόρφωση Προστασίας Προσωπικών Δεδομένων";
$pgv_lang["research_log"]						= "Ημερολόγιο Έρευνας";
$pgv_lang["administration"]						= "Διαχείριση";
$pgv_lang["ansi_to_utf8"]						= "Μετατροπή ANSI encoded GEDCOM σε UTF-8?";
$pgv_lang["utf8_to_ansi"]						= "Do you want to convert this GEDCOM from UTF-8 to ANSI (ISO-8859-1)?";
$pgv_lang["user_manual"]						= "Εγχειρίδιο Χρήσης PhpGedView";
$pgv_lang["upgrade"]							= "Upgrade PhpGedView/ResearchLog";
$pgv_lang["view_logs"]							= "View logfiles";
$pgv_lang["logfile_content"]					= "Content of log-file";
$pgv_lang["step1"]								= "Βήμα 1 από 4:";
$pgv_lang["step2"]								= "Βήμα 2 από 4:";
$pgv_lang["step3"]								= "Βήμα 3 από 4:";
$pgv_lang["step4"]								= "Βήμα 4 από 4:";
$pgv_lang["validate_gedcom"]					= "Validate GEDCOM";
$pgv_lang["img_admin_settings"]					= "Edit Image Manipulation Configuration";
$pgv_lang["download_note"]						= "NOTE: Large GEDCOMs can take a long time to process before downloading.  If PHP times out before the download is complete, then you may not get a complete download.  You can check the downloaded GEDCOM for the 0 TRLR line at the end of the file to make sure it downloaded correctly.  In general it could take as much time to download as it took to import your GEDCOM.";
$pgv_lang["pgv_registry"]						= "Εμφάνιση άλλων σελίδων που χρησιμοποιούν την εφαρμογή PhpGedView";
$pgv_lang["verify_upload_instructions"]			= "If you choose to continue, the old GEDCOM file will be replaced with the file that you uploaded and the import process will begin again.  If you choose to cancel, the old GEDCOM will remain unchanged.";
$pgv_lang["cancel_upload"]						= "Ακύρωση Αποστολής";

//-- Relationship chart messages
$pgv_lang["relationship_chart"]					= "Διάγραμμα Συγγενείας";
$pgv_lang["person1"]							= "Άτομο 1";
$pgv_lang["person2"]							= "Άτομο 2";
$pgv_lang["no_link_found"]						= "Καμμία (άλλη) διασύνδεση ανάμεσα στα δύο άτομα δεν βρέθηκε.";
$pgv_lang["sibling"]							= "Αδέλφια";
$pgv_lang["follow_spouse"]						= "Έλεγχος συγγένειας από γάμο.";
$pgv_lang["timeout_error"]						= "Ο μέγιστος επιτρεπόμενος χρόνος εκτέλεσης της διαδικασίας έχει ξεπεραστεί πριν βρεθεί κάποια συγγένεια.";
$pgv_lang["son"]								= "Υιός";
$pgv_lang["daughter"]							= "Θυγατέρα";
$pgv_lang["brother"]							= "Αδελφός";
$pgv_lang["sister"]								= "Αδελφή";
$pgv_lang["relationship_to_me"]					= "Συγγένεια με εμένα";
$pgv_lang["next_path"]							= "Εύρεση Άλλης Διαδρομής Διασύνδεσης";
$pgv_lang["show_path"]							= "Εμφάνιση Διαδρομής Διασύνδεσης";
$pgv_lang["line_up_generations"]				= "Στοίχιση γενεών";
$pgv_lang["oldest_top"]							= "Εμφάνιση μεγαλύτερου πρώτα";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]						= "Are you sure you want to delete this GEDCOM fact?";
$pgv_lang["access_denied"]						= "<b>Access Denied</b><br />You do not have access to this resource.";
$pgv_lang["gedrec_deleted"]						= "GEDCOM record succesfully deleted.";
$pgv_lang["gedcom_deleted"]						= "GEDCOM [#GED#] succesfully deleted.";
$pgv_lang["changes_exist"]						= "Changes have been made to this GEDCOM.";
$pgv_lang["accept_changes"]						= "Αποδοχή / Απόρριψη Αλλαγών";
$pgv_lang["show_changes"]						= "This record has been updated.  Click here to show changes.";
$pgv_lang["hide_changes"]						= "Επιλέξτε εδώ για απόκρυψη αλλαγών.";
$pgv_lang["review_changes"]						= "Ανασκόπιση Αλλαγών GEDCOM";
$pgv_lang["undo_successful"]					= "Undo Successful";
$pgv_lang["undo"]								= "Undo";
$pgv_lang["view_change_diff"]					= "View Change Diff";
$pgv_lang["changes_occurred"]					= "The following changes occured to this individual:";
$pgv_lang["find_place"]							= "Εύρεση Τόπου";
$pgv_lang["close_window"]						= "Κλείσιμο Παραθύρου";
$pgv_lang["close_window_without_refresh"]		= "Close Window Without Reloading";
$pgv_lang["place_contains"]						= "Place Contains:";
$pgv_lang["accept_gedcom"]						= "Decide for each change to either accept or reject it.<br />To accept all changes at once, click \"Accept all changes\" in the box below.<br />To get more information about a change, <br />click \"View change diff\" to see the differences between old and new situation, <br />or click \"View GEDCOM record\" to see the new situation in GEDCOM format.";
$pgv_lang["ged_import"]							= "Εισαγωγή";
$pgv_lang["now_import"]							= "Now you should import the GEDCOM records into PhpGedView by clicking on the import link below.";
$pgv_lang["add_fact"]							= "Προσθήκη νέου στοιχείου";
$pgv_lang["add"]								= "Προσθήκη";
$pgv_lang["custom_event"]						= "Custom Event";
$pgv_lang["update_successful"]					= "Ενημερώθηκε επιτυχώς";
$pgv_lang["add_child"]							= "Προσθήκη Παιδιού";
$pgv_lang["add_child_to_family"]				= "Προσθήκη παιδιού στην οικογένεια αυτή";
$pgv_lang["add_sibling"]						= "Προσθήκη Αδελφού ή Αδελφής";
$pgv_lang["add_son_daughter"]					= "Προσθήκη Υιού ή Θυγατέρας";
$pgv_lang["must_provide"]						= "You must provide a ";
$pgv_lang["delete_person"]						= "Delete this Individual";
$pgv_lang["confirm_delete_person"]				= "Are you sure you want to delete this person from the GEDCOM file?";
$pgv_lang["find_media"]							= "Find Media";
$pgv_lang["set_link"]							= "Set Link";
$pgv_lang["add_source_lbl"]						= "Add Source Citation";
$pgv_lang["add_source"]							= "Add a new Source Citation";
$pgv_lang["add_note_lbl"]						= "Προσθήκη Σημείωσης/Σχολίων";
$pgv_lang["add_note"]							= "Προσθήκη νέου Σημειώματος/Σχολίου";
$pgv_lang["add_media_lbl"]						= "Προσθήκη Πολυμέσων";
$pgv_lang["add_media"]							= "Προσθήκη νέου τύπου Πολυμέσων";
$pgv_lang["delete_source"]						= "Delete this Source";
$pgv_lang["confirm_delete_source"]				= "Are you sure you want to delete this source from the GEDCOM file?";
$pgv_lang["add_husb"]							= "Προσθήκη Συζύγου-Άνδρα";
$pgv_lang["add_husb_to_family"]					= "Προσθήκη Συζύγου-Άνδρα σε αυτή την οικογένεια";
$pgv_lang["add_wife"]							= "Προσθήκη Συγύζου-Γυναίκας";
$pgv_lang["add_wife_to_family"]					= "Προσθήκη Συζύγου-Γυναίκας σε αυτή την οικογένεια";
$pgv_lang["find_family"]						= "Εύρεση Οικογένειας";
$pgv_lang["find_fam_list"]						= "Εύρεση Καταλόγου Οικογένειας";
$pgv_lang["add_new_wife"]						= "Προσθήκη νέας συζύγου";
$pgv_lang["add_new_husb"]						= "Προσθήκη νέου συζύγου";
$pgv_lang["edit_name"]							= "Αλλαγή Ονόματος";
$pgv_lang["delete_name"]						= "Διαγραφή Ονόματος";
$pgv_lang["no_temple"]							= "No Temple - Living Ordinance";
$pgv_lang["replace"]							= "Αντικατάσταση Στοιχείου";
$pgv_lang["append"]								= "Append Record";
$pgv_lang["add_father"]							= "Προσθήκη νέου πατέρα";
$pgv_lang["add_mother"]							= "Προσθήκη νέας μητέρας";
$pgv_lang["add_obje"]							= "Προσθήκη νέου αντικειμένου πολυμέσων";
$pgv_lang["no_changes"]							= "There are currently no changes that need to be reviewed.";
$pgv_lang["accept"]								= "Αποδοχή";
$pgv_lang["accept_all"]							= "Αποδοχή όλων των Αλλαγών";
$pgv_lang["accept_successful"]					= "Επιτυχής ενημέρωση αλλαγών στη βάση δεδομένων";
$pgv_lang["edit_raw"]							= "Edit raw GEDCOM record";
$pgv_lang["select_date"]						= "Επιλογή ημερομηνίας";
$pgv_lang["create_source"]						= "Δημιουργία νέας πηγής";
$pgv_lang["new_source_created"]					= "New source created successfully.";
$pgv_lang["paste_id_into_field"]				= "Paste the following source ID into your editing fields to reference this source ";
$pgv_lang["add_name"]							= "Προσθήκη Νέου Ονόματος";
$pgv_lang["privacy_not_granted"]				= "You have no access to";
$pgv_lang["user_cannot_edit"]					= "Το ψευδώνυμο αυτό δεν μπορεί να διαμορφώση αυτό το GEDCOM.";
$pgv_lang["gedcom_editing_disabled"]			= "Editing this GEDCOM has been disabled by the system administrator.";
$pgv_lang["privacy_prevented_editing"]			= "The privacy settings prevent you from editing this record.";

//-- calendar.php messages
$pgv_lang["on_this_day"]						= "Σαν σήμερα, στην Ιστορία μας...";
$pgv_lang["in_this_month"]						= "Στο μήνα αυτό, στην Ιστορία μας...";
$pgv_lang["in_this_year"]						= "In This Year, in Your History...";
$pgv_lang["year_anniversary"]					= "#year_var# Επέτειος";
$pgv_lang["today"]								= "Σήμερα";
$pgv_lang["day"]								= "Ημέρα:";
$pgv_lang["month"]								= "Μήνας:";
$pgv_lang["showcal"]							= "Εμφάνιση Γεγονότων της:";
$pgv_lang["anniversary_calendar"]				= "Ημερολόγιο Επετειών";
$pgv_lang["sunday"]								= "Κυριακή";
$pgv_lang["monday"]								= "Δευτέρα";
$pgv_lang["tuesday"]							= "Τριτη";
$pgv_lang["wednesday"]							= "Τετάρτη";
$pgv_lang["thursday"]							= "Πέμπτη";
$pgv_lang["friday"]								= "Παρασκευή";
$pgv_lang["saturday"]							= "Σάββατο";
$pgv_lang["viewday"]							= "Εμφάνισης Ημέρας";
$pgv_lang["viewmonth"]							= "Εμφάνιση Μήνα";
$pgv_lang["viewyear"]							= "Εμφάνιση Έτους";
$pgv_lang["all_people"]							= "All People";
$pgv_lang["living_only"]						= "Living People";
$pgv_lang["recent_events"]						= "Recent Years (&lt; 100 yrs)";
$pgv_lang["day_not_set"]						= "Day not set";
$pgv_lang["year_error"]							= "Sorry, dates before 1970 are not supported.";

//-- upload media messages
$pgv_lang["upload_media"]						= "Αποστολή Αρχείων Πολυμέσων";
$pgv_lang["media_file"]							= "Αρχείο Πολυμέσων";
$pgv_lang["thumbnail"]							= "Thumbnail";
$pgv_lang["upload_successful"]					= "Επιτυχής Αποστολή";

//-- user self registration module
//$pgv_lang["no_pw_or_account"]					= "If you have no account yet, or lost your password, just click the <b>Login</b> button";
$pgv_lang["lost_password"]						= "Χάσατε τον κωδικό σας;";
$pgv_lang["requestpassword"]					= "Αίτηση νέου κωδικού";
$pgv_lang["no_account_yet"]						= "Δεν έχετε ακόμη λογαριασμό;";
$pgv_lang["requestaccount"]						= "Αίτηση νέου λογαριασμού χρήστη";
$pgv_lang["register_info_01"]					= "The amount of data that can be publicly viewed on this website may be limited due to applicable law concerning privacy protection. Most people do not want their personal data publicly available on the Internet. It could be misused for spam or identity theft.<br /><br />To gain access to the private data, you must have an account on this website. To gain an account you may register yourself by providing the requested information. After the administrator has checked your registration and approved it, you will be able to login and view the private data.<br /><br />If the relationship privacy is activated you will only be able to access your own close relative's private information after logging in. The administrator can also provide access to database editing, so you can change or add information.<br /><br />NOTE: You only will receive access to the private data if you can prove that you are a close relative of a person in the database.<br /><br />If you are not a close relative you will probably not be given an account, so you should save yourself the trouble.<br />If you need any further support, please use the link below to contact the webmaster.<br /><br />";
$pgv_lang["register_info_02"]					= "";
$pgv_lang["pls_note01"]							= "Please note: The system is case-sensitive!";
$pgv_lang["min6chars"]							= "Password has to contain at least 6 characters";
$pgv_lang["pls_note02"]							= "Please note: Passwords can contain letters and numbers and other characters.";
$pgv_lang["pls_note03"]							= "This email address will be verified before account activation. It will not be displayed on the site. A message will be sent to this Email address with your registration data";
$pgv_lang["emailadress"]						= "Διεύθυνση Ηλεκτρονικού Ταχυδρομείου";
$pgv_lang["pls_note04"]							= "Fields marked with * are mandatory.";
$pgv_lang["pls_note05"]							= "Pending completion of the form on this page and verification of your answers, you will be sent a confirmation message to the email address you specify on this page. Using the confirmation email, you will activate your account; if you fail to activate your account within seven days, it will be purged (you may attempt to register the account again at that time). To use this site, you will need to know your login name and password. You must specify an existing, valid email address on this page in order to receive the account confirmation email.<br /><br />If you encounter an issue in registering an account on this website, please submit a Support Request to the webmaster.";

$pgv_lang["mail01_line01"]						= "Γειά σου #user_fullname# ...";
$pgv_lang["mail01_line02"]						= "A request was made at ( #SERVER_NAME# ) to login with your Email address ( #user_email# ).";
$pgv_lang["mail01_line03"]						= "The following data was used.";
$pgv_lang["mail01_line04"]						= "Please click on the link below and fill in the requested data to verify your Account and Email address.";
$pgv_lang["mail01_line05"]						= "If you didn't request this data you can just delete this message.";
$pgv_lang["mail01_line06"]						= "You won't get any mail again from this system, because the account will be deleted without verification within seven days.";
$pgv_lang["mail01_subject"]						= "Your registration at #SERVER_NAME#";

$pgv_lang["mail02_line01"]						= "Γειά σου Διαχειριστή ...";
$pgv_lang["mail02_line02"]						= "A new user made a new user-registration at ( #SERVER_NAME# ).";
$pgv_lang["mail02_line03"]						= "The user received an email with the necessary data to verify their account.";
$pgv_lang["mail02_line04"]						= "As soon as the user has done this verification you will be informed by mail to give this user the permission to login to your site.";
$pgv_lang["mail02_subject"]						= "New registration at #SERVER_NAME#";

$pgv_lang["hashcode"]							= "Verfification code:";
$pgv_lang["thankyou"]							= "Γειά σου #user_fullname# ...<br />Ευχαριστώ για την αίτηση εγγραφής";
$pgv_lang["pls_note06"]							= "Now you will receive a confirmation email to the email address ( #user_email# ). Using the confirmation email, you will activate your account; if you fail to activate your account within seven days, it will be purged (you can register the account again at that point). To login to this site, you will need to know your login name and password.";

$pgv_lang["registernew"]						= "Επιβεβαίωση νέου χρήστη";
$pgv_lang["user_verify"]						= "Επιβεβαίωση Χρήστη";
$pgv_lang["send"]								= "Αποστολή μηνύματος";

$pgv_lang["pls_note07"]							= "Please type in your username, your password and the verification code you received by email from this system to verify your account request.";
$pgv_lang["pls_note08"]							= "The data for the user #user_name# was checked.";

$pgv_lang["mail03_line01"]						= "Γειά σου Διαχειριστή ...";
$pgv_lang["mail03_line02"]						= "#newuser[username]# ( #newuser[fullname]# ) has verified the registration data.";
$pgv_lang["mail03_line03"]						= "Please click on the link below to login to your site edit the user and give him the permission to login to your site.";
$pgv_lang["mail03_subject"]						= "New verification at #SERVER_NAME#";

$pgv_lang["pls_note09"]							= "You were identified as a registered user.";
$pgv_lang["pls_note10"]							= "The Administrator has been informed.<br />As soon as he gives you the permission to login you can login with your username and password.";
$pgv_lang["data_incorrect"]						= "Data was not correct!<br />Please try again!";
$pgv_lang["user_not_found"]						= "Could not verify the information you entered.  Please go back and try again.";

$pgv_lang["lost_pw_reset"]						= "Lost password request";

$pgv_lang["pls_note11"]							= "To have your password reset, supply the username and email address for your user account. <br /><br />We will send you a special URL via email, which contains a confirmation hash for your account. By visiting the provided URL, you will be permitted to change your password and login to this site. For reasons of security, you should not provide this confirmation hash to anyone, including the administrators of this site (we won't ask for it).<br /><br />If you require assistance from the administrator of this site, please contact the site administrator.";
$pgv_lang["enter_email"]						= "You must enter an email address.";

$pgv_lang["mail04_line01"]						= "Hello #user_fullname# ...";
$pgv_lang["mail04_line02"]						= "A new password was requested for your username!";
$pgv_lang["mail04_line03"]						= "Recommendation:";
$pgv_lang["mail04_line04"]						= "Now please click on the link below, login with the new Password and change it to keep the integrity of your data secure.";
$pgv_lang["mail04_subject"]						= "Data request at #SERVER_NAME#";

$pgv_lang["pwreqinfo"]							= "Hello...<br /><br />A mail was sent to the email address (#user[email]#) including the new password.<br /><br />Please check your mail account because the mail should be received in the next few minutes.<br /><br />Recommendation:<br /><br />After you have requested the mail you should login to this site with your new password and change it to keep the integrity of your data sequrity.";

$pgv_lang["editowndata"]						= "Ο Λογαριασμός μου";
$pgv_lang["savedata"]							= "Save changed data";
$pgv_lang["datachanged"]						= "User data was changed!";
$pgv_lang["datachanged_name"]					= "You may need to relogin with your new username.";
$pgv_lang["myuserdata"]							= "Ο Λογαριασμός μου";
$pgv_lang["verified"]							= "User verified himself";
$pgv_lang["verified_by_admin"]					= "User Approved by Admin";
$pgv_lang["user_theme"]							= "My Theme";
$pgv_lang["mgv"]								= "MyGedView";
$pgv_lang["mygedview"]							= "MyGedView Portal";
$pgv_lang["passwordlength"]						= "Password must contain at least 6 characters.";
$pgv_lang["admin_approved"]						= "Your account at #SERVER_NAME# has been approved";
$pgv_lang["you_may_login"]						= " by the site administrator.  You may now login to the PhpGedView Site by going to the link below:";
$pgv_lang["welcome_text_auth_mode_1"]			= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access to this site is permitted to every visitor who has a user account on this website.<br />If you already have a user account you can login on this page.<br /><br />If you don't have a user account yet, you can apply for one by clicking on the appropriate link on this page.<br />After verifying your information, the site administrator will activate your account.<br />You will receive an email on activation.";
$pgv_lang["welcome_text_auth_mode_2"]			= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access to this site is permitted to <b>authorized</b> users only.<br />If you already have a user account you can login on this page.<br /><br />If you don't have a user account yet, you can apply for one by clicking on the appropriate link on this page.<br />After verifying your information, the site administrator will either accept or decline your request.<br />You will receive an e-mail message upon acceptance of your request.";
$pgv_lang["welcome_text_auth_mode_3"]			= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access to this site is permitted <b>to familymembers only</b>.<br />If you already have a user account you can login on this page.<br /><br />If you don't have a user account yet, you can apply for one by clicking on the appropriate link on this page.<br />After verifying your information, the site administrator will either accept or decline your request.<br />You will receive an email when your request is accepted.";
$pgv_lang["welcome_text_cust_head"]				= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access is permitted to users who have a useraccount and a password for this website.<br />";


//-- mygedview page
$pgv_lang["welcome"]							= "Καλωσόρισες";
$pgv_lang["upcoming_events"]					= "Upcoming Events";
$pgv_lang["chat"]								= "Chat";
$pgv_lang["users_logged_in"]					= "Users Logged In";
$pgv_lang["message"]							= "Αποστολή Μηνύματος";
$pgv_lang["my_messages"]						= "Τα μηνύματά μου";
$pgv_lang["date_created"]						= "Date Sent:";
$pgv_lang["message_from"]						= "Διεύθυνση Ηλεκτρονικού Ταχυδρομείου:";
$pgv_lang["message_from_name"]					= "Your Name:";
$pgv_lang["message_to"]							= "Message To:";
$pgv_lang["message_subject"]					= "Θέμα:";
$pgv_lang["message_body"]						= "Body:";
$pgv_lang["no_to_user"]							= "No recipient user was provided.  Cannot continue.";
$pgv_lang["provide_email"]						= "Please provide your email address so that we may contact you in response to this message.   If you do not provide your email address we will not be able to respond to your inquiry.  You email address will not be used in any other way besides responding to this inquiry.";
$pgv_lang["reply"]								= "Απάντηση";
$pgv_lang["message_deleted"]					= "Το Μήνυμα Διαγράφτηκε";
$pgv_lang["message_sent"]						= "Το μήνυμα έχει σταλεί";
$pgv_lang["reset"]								= "Reset";
$pgv_lang["site_default"]						= "Site Default";
$pgv_lang["mygedview_desc"]						= "Your MyGedView page allows you to keep bookmarks of your favorite people, track upcoming events, and collaborate with other PhpGedView users.";
$pgv_lang["no_messages"]						= "You have no pending messages.";
$pgv_lang["clicking_ok"]						= "Clicking OK, will open another window where you may contact #user[fullname]#";
$pgv_lang["my_favorites"]						= "My Favorites";
$pgv_lang["no_favorites"]						= "You have not selected any favorites.  To add an Individual to your favorites, find the details of the individual you want to add and then click on the \"Add to My Favorites\" link or use the ID box below to add an Individual by their ID number.";
$pgv_lang["add_to_my_favorites"]				= "Add to My Favorites";
$pgv_lang["gedcom_favorites"]					= "This GEDCOM's Favorites";
$pgv_lang["no_gedcom_favorites"]				= "At this moment there are no selected Favorites.   The admin can add Favorites to display at startup.";
$pgv_lang["confirm_fav_remove"]					= "Are you sure you want to remove this item from your favorites?";
$pgv_lang["invalid_email"]						= "Please enter a valid email address.";
$pgv_lang["enter_subject"]						= "Please enter a message subject.";
$pgv_lang["enter_body"]							= "Please enter some message text before sending.";
$pgv_lang["confirm_message_delete"]				= "Are you sure you want to delete this message?  It cannot be retrieved later.";
$pgv_lang["message_email1"]						= "The following message has been sent to your PhpGedView User account from ";
$pgv_lang["message_email2"]						= "You sent the following message to a PhpGedView User account:";
$pgv_lang["message_email3"]						= "You sent the following message to a PhpGedView administrator:";
$pgv_lang["viewing_url"]						= "This message was sent while viewing the following url: ";
$pgv_lang["messaging2_help"]					= "When you send this message you will receive a copy sent via email to the email address you provided.";
$pgv_lang["random_picture"]						= "Τυχαία Εικόνα";
$pgv_lang["message_instructions"]				= "<b>Please Note:</b> Private information of living individuals will only be given to family relatives and close friends.  You will be asked to verify your relationship before you will receive any private data.  Sometimes information of dead persons may also be private.  If this is the case, it is because there is not enough information known about the person to determine if they are alive or not and we probaby do not have more information on this person.<br /><br />Before asking a question, please verify that you are inquiring about the correct person by checking dates, places, and close relatives.  If you are submitting changes to the genealogical data, please include the sources where you obtained the data.<br /><br />";
$pgv_lang["sending_to"]							= "This message will be sent to #TO_USER#";
$pgv_lang["preferred_lang"]						= "This user prefers to receive messages in #USERLANG#";
$pgv_lang["gedcom_created_using"]				= "This GEDCOM was created using <b>#SOFTWARE# #VERSION#</b>";
$pgv_lang["gedcom_created_on"]					= "This GEDCOM was created on <b>#DATE#</b>";
$pgv_lang["gedcom_created_on2"]					= " on <b>#DATE#</b>";
$pgv_lang["gedcom_stats"]						= "Στατιστικά GEDCOM";
$pgv_lang["stat_individuals"]					= "Άτομα, ";
$pgv_lang["stat_families"]						= "Οικογένειες, ";
$pgv_lang["stat_sources"]						= "Πηγές, ";
$pgv_lang["stat_other"]							= "Other Records";
$pgv_lang["customize_page"]						= "Customize MyGedView Portal";
$pgv_lang["customize_gedcom_page"]				= "Customize this GEDCOM Welcome Page";
$pgv_lang["upcoming_events_block"]				= "Upcoming Events Block";
$pgv_lang["upcoming_events_descr"]				= "The Upcoming Events Block shows a list of the events in the currently active GEDCOM that occured within the next 30 days.   For a user MyGedView page the block will only list living people.  For a GEDCOM Welcome Page it will list all people.";
$pgv_lang["todays_events_block"]				= "On This Day Block";
$pgv_lang["todays_events_descr"]				= "The On This Day, in Your History... Block shows a list of the events in the currently active GEDCOM that occured today.   If no events are found then the block is not shown.  For a user MyGedView page the block will only list living people.   For a GEDCOM Welcome Page it will list all people.";
$pgv_lang["logged_in_users_block"]				= "Πλαίσιο συνδεδεμένων Χρηστών";
$pgv_lang["logged_in_users_descr"]				= "The Logged In Users Block shows a list of the users who are currently logged in.";
$pgv_lang["user_messages_block"]				= "User Messages Block";
$pgv_lang["user_messages_descr"]				= "The User Messages Block shows a list of the messages that have been sent to the active user.";
$pgv_lang["user_favorites_block"]				= "User Favorites Block";
$pgv_lang["user_favorites_descr"]				= "The User Favorites Block shows the user a list of their favorite people in the system so that they can easily link to them.";
$pgv_lang["welcome_block"]						= "User Welcome Block";
$pgv_lang["welcome_descr"]						= "The User Welcome Block shows the user the current date and time, quick links to modify their account or go to their own pedigree chart, and a link to customize their MyGedView page.";
$pgv_lang["random_media_block"]					= "Random Media Block";
$pgv_lang["random_media_descr"]					= "The Random Media Block randomly selects a photo or other media item in the currently active GEDCOM and displays it to the user.";
$pgv_lang["gedcom_block"]						= "GEDCOM Welcome Block";
$pgv_lang["gedcom_descr"]						= "The GEDCOM Welcome Block works the same as the User Welcome Block by welcoming the visitor to the site and displaying the title of the currently active GEDCOM and the current date and time.";
$pgv_lang["gedcom_favorites_block"]				= "GEDCOM Favorites Block";
$pgv_lang["gedcom_favorites_descr"]				= "The GEDCOM Favorites Block allows the site administrator the ability to select their favorite people in the GEDCOM so that their visitors can easily find them.  This is a way to hilight those people who are important in your family history.";
$pgv_lang["gedcom_stats_block"]					= "GEDCOM Statistics Block";
$pgv_lang["gedcom_stats_descr"]					= "The GEDCOM Statistics Block shows the visitor some basic information about the GEDCOM such as when it was created and how many people are in the GEDCOM.";
$pgv_lang["portal_config_intructions"]			= "Here you can customize the page by positioning the blocks on the page the way that you want them.   The page is divided into two sections, the 'Main' section and the 'Right' section.   The 'Main' section blocks appear larger and under the page title.  The 'Right' section starts to the right of the title and goes down the right hand side of the page.   Each section has its own list of blocks that will be printed on the page in the order they are listed.   You can add, remove, and reorder the blocks however you like.";
$pgv_lang["login_block"]						= "Πλαίσιο Διασύνδεσης";
$pgv_lang["login_descr"]						= "The Login Block prints a Username and Password for users to login.";
$pgv_lang["theme_select_block"]					= "Theme select Block";
$pgv_lang["theme_select_descr"]					= "The theme select block displays the theme selector even when the change theme is disabled.";
$pgv_lang["block_top10_title"]					= "Δημοφιλή Επώνυμα";
$pgv_lang["block_top10"]						= "Top 10 Surnames Block";
$pgv_lang["block_top10_descr"]					= "This block show a table of the 10 most used names in the database";

$pgv_lang["gedcom_news_block"]					= "GEDCOM News Block";
$pgv_lang["gedcom_news_descr"]					= "The GEDCOM News Block shows the visitor news releases or articles posted by an admin user.  The News is a good place to announce an updated GEDCOM file or a family reunion.";
$pgv_lang["user_news_block"]					= "User Journal Block";
$pgv_lang["user_news_descr"]					= "The User Journal Block lets the user keep notes or a journal online.";
$pgv_lang["my_journal"]							= "My Journal";
$pgv_lang["no_journal"]							= "You have not created any journal items.";
$pgv_lang["confirm_journal_delete"]				= "Are you sure you want to delete this journal entry?";
$pgv_lang["add_journal"]						= "Add a new journal entry";
$pgv_lang["gedcom_news"]						= "Νέα";
$pgv_lang["confirm_news_delete"]				= "Are you sure you want to delete this news entry?";
$pgv_lang["add_news"]							= "Add a News article";
$pgv_lang["no_news"]							= "No News Articles have been submitted.";
$pgv_lang["edit_news"]							= "Add/Edit Journal/News Entry";
$pgv_lang["enter_title"]						= "Please enter a title.";
$pgv_lang["enter_text"]							= "Please enter some text for this news or journal entry.";
$pgv_lang["news_saved"]							= "News/Journal Entry Successfully Saved.";
$pgv_lang["article_text"]						= "Entry Text:";
$pgv_lang["main_section"]						= "Main Section Blocks";
$pgv_lang["right_section"]						= "Right Section Blocks";
$pgv_lang["move_up"]							= "Move Up";
$pgv_lang["move_down"]							= "Move Down";
$pgv_lang["move_right"]							= "Move Right";
$pgv_lang["move_left"]							= "Move Left";
$pgv_lang["add_main_block"]						= "Add a block to main section...";
$pgv_lang["add_right_block"]					= "Προσθήκη πλαισίου στη δεξιά περιοχή...";
$pgv_lang["broadcast_all"]						= "Broadcast to All Users";
$pgv_lang["hit_count"]							= "Hit Count:";
$pgv_lang["phpgedview_message"]					= "PhpGedView Message";
$pgv_lang["common_surnames"]					= "Most Common Surnames";
$pgv_lang["default_news_title"]					= "Welcome to Your Genealogy";
$pgv_lang["default_news_text"]					= "The genealogy information on this website is powered by <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView #VERSION#</a>.  This page provides an introduction and overview to this genealogy.  To begin working with the data, choose one of the charts from the charts menu, go to the individual list, or search for a name or place.<br /><br />If you have trouble using the site clicking on the help menu will give you information on how to use the page that you are currently viewing.<br /><br />Thank you for visiting this site.";
$pgv_lang["reset_default_blocks"]				= "Reset to Default Blocks";
$pgv_lang["recent_changes"]						= "Πρόσφατες Αλλαγές";
$pgv_lang["recent_changes_block"]				= "Recent Changes Block";
$pgv_lang["recent_changes_descr"]				= "The Recent Change Block will list all of the changes that have been made to the GEDCOM in the last month.  This block can help you stay up to date with the changes that have been made.  The changes are detected based on the CHAN tag.";
$pgv_lang["delete_selected_messages"]			= "Delete Selected Messages";
$pgv_lang["use_blocks_for_default"]				= "Use these blocks as the default block configuration for all users?";


//-- upgrade.php messages
$pgv_lang["upgrade_util"]						= "Upgrade Util";
$pgv_lang["no_upgrade"]							= "There are no files to upgrade.";
$pgv_lang["use_version"]						= "You are using version:";
$pgv_lang["current_version"]					= "Current stable version:";
$pgv_lang["upgrade_download"]					= "Download:";
$pgv_lang["upgrade_tar"]						= "TAR";
$pgv_lang["upgrade_zip"]						= "ZIP";
$pgv_lang["latest"]								= "You are running the latest version of PhpGedView.";
$pgv_lang["location"]							= "Location of upgrade files: ";
$pgv_lang["include"]							= "Include:";
$pgv_lang["options"]							= "Options:";
$pgv_lang["inc_phpgedview"]						= " PhpGedView";
$pgv_lang["inc_languages"]						= " Γλώσσες";
$pgv_lang["inc_config"]							= " Αρχείο Διαμόρφωσης";
$pgv_lang["inc_researchlog"]					= " Researchlog";
$pgv_lang["inc_index"]							= " Index files";
$pgv_lang["inc_themes"]							= " Themes";
$pgv_lang["inc_docs"]							= " Manuals";
$pgv_lang["inc_privacy"]						= " Privacy file(s)";
$pgv_lang["inc_backup"]							= " Create backup";
$pgv_lang["upgrade_help"]						= " Help me";
$pgv_lang["cannot_read"]						= "Cannot read file:";
$pgv_lang["not_configured"]						= "You do not have PhpGedView configured yet.";
$pgv_lang["location_upgrade"]					= "Please fill in the location of your upgrade files.";
$pgv_lang["new_variable"]						= "Found new variable: ";
$pgv_lang["config_open_error"]					= "There has been error opening the config file.";
$pgv_lang["gedcom_config_write_error"]			= "Error!!! Cannot write to the GEDCOM configuration file.";
$pgv_lang["config_update_ok"]					= "Configuration file updated successfully.";
$pgv_lang["config_uptodate"]					= "Your configuration file is up-to-date.";
$pgv_lang["processing"]							= "Σε επεξεργασία...";
$pgv_lang["privacy_open_error"]					= "There has been error opening the file [#PRIVACY_MODULE#].";
$pgv_lang["privacy_write_error"]				= "ERROR!!! Unable to write to the file [#PRIVACY_MODULE#].<br />Make sure write permissions are available to the file.<br />Permissions may be restored once privacy file is written.";
$pgv_lang["privacy_update_ok"]					= "Privacy file: [#PRIVACY_MODULE#] updated successfully.";
$pgv_lang["privacy_uptodate"]					= "Your [#PRIVACY_MODULE#] file is up-to-date.";
$pgv_lang["heading_privacy"]					= "Αρχεία Προστασίας Προσωπικών Δεδομένων:";
$pgv_lang["heading_phpgedview"]					= "Αρχεία Εφαρμογής PhpGedView:";
$pgv_lang["heading_image"]						= "Αρχεία Εικόνων:";
$pgv_lang["heading_index"]						= "Αρχεία ευρετηρίου:";
$pgv_lang["heading_language"]					= "Αρχεία Γλώσσας:";
$pgv_lang["heading_theme"]						= "Theme files:";
$pgv_lang["heading_docs"]						= "Εγχειρίδια:";
$pgv_lang["heading_researchlog"]				= "Research Log files:";
$pgv_lang["heading_researchloglang"]			= "Research Log language files:";
$pgv_lang["copied_success"]						= "copied successfully.";
$pgv_lang["backup_copied_success"]				= "backup file created successfully.";
$pgv_lang["folder_created"]						= "Δημιουργήθηκε Φάκελος";
$pgv_lang["process_error"]						= "There is a problem processing the page. A newer version cannot be determined.";
$pgv_lang["upgrade_completed"]					= "Upgrade Completed Successfully";
$pgv_lang["start_using_upgrad"]					= "Click here to begin using version";

//-- validate GEDCOM
$pgv_lang["performing_validation"]				= "Performing GEDCOM validation, select the necessary options then click 'Cleanup'";
$pgv_lang["changed_mac"]						= "Macintosh line endings detected. Changed lines ending with only return to end with a return and a linefeed.";
$pgv_lang["changed_places"]						= "Invalid Place encodings detected. Cleaned up place records to match proper GEDCOM 5.5 specifications.  An example from your GEDCOM is:";
$pgv_lang["invalid_dates"]						= "Detected invalid date formats, on cleanup these will be changed to format of DD MMM YYYY (ie. 1 JAN 2004).";
$pgv_lang["valid_gedcom"]						= "Valid GEDCOM Detected.   No cleanup required.";
$pgv_lang["optional_tools"]						= "You may also choose to run the following optional tools before importing.";
$pgv_lang["optional"]							= "Optional Tools";
$pgv_lang["date_format"]						= "Μορφή Απεικόνισης Ημερομηνίας:";
$pgv_lang["day_before_month"]					= "Ημέρα πριν από Μήνα (ΗΗ ΜΜ ΕΕΕΕ)";
$pgv_lang["month_before_day"]					= "Μήνας πριν από Ημέρα (ΜΜ ΗΗ ΕΕΕΕ)";
$pgv_lang["do_not_change"]						= "Do not change";
$pgv_lang["change_id"]							= "Change Individual ID to:";
$pgv_lang["example_date"]						= "Example of invalid date from your GEDCOM:";
$pgv_lang["add_media_tool"]						= "Add Media Tool";
$pgv_lang["launch_media_tool"]					= "Click here to launch the Add Media Tool.";
$pgv_lang["add_media_descr"]					= "This tool will add media OBJE tags to the GEDCOM.  Close this window when you are finished adding media.";
$pgv_lang["highlighted"]						= "Highlited Image";
$pgv_lang["extension"]							= "Extension";
$pgv_lang["order"]								= "Order";
$pgv_lang["add_media_button"]					= "Add Media";
$pgv_lang["media_table_created"]				= "Successfully updated <i>media</i> table.";
$pgv_lang["click_to_add_media"]					= "Click here to Add the Media listed above to GEDCOM #GEDCOM#";
$pgv_lang["adds_completed"]						= "Media successfully added to GEDCOM file.";
$pgv_lang["ansi_encoding_detected"]				= "ANSI File Encoding detected.   PhpGedView works best with files encoded in UTF-8.";
$pgv_lang["invalid_header"]						= "Detected lines before the GEDCOM header (0 HEAD).  On cleanup these lines will be removed.";
$pgv_lang["macfile_detected"]					= "Macintosh file detected.  On cleanup your file will be converted to a DOS file.";
$pgv_lang["place_cleanup_detected"]				= "Invalid place encodings were detected.  These errors should be fixed.  The following sample shows the invalid place that was detected: ";
$pgv_lang["cleanup_places"]						= "Cleanup Places";
$pgv_lang["empty_lines_detected"]				= "Empty lines were detected in your GEDCOM file.   On cleanup, these empty lines will be removed.";

//-- hourglass chart
$pgv_lang["hourglass_chart"]					= "Διάγραμμα σε σχήμα Κλεψύδρας";

//-- report engine
$pgv_lang["choose_report"]						= "Επιλογή αναφοράς";
$pgv_lang["enter_report_values"]				= "Enter report values";
$pgv_lang["selected_report"]					= "Επιλεγμένη Αναφορά";
$pgv_lang["run_report"]							= "Εμφάνιση Αναφοράς";
$pgv_lang["select_report"]						= "Select Report";
$pgv_lang["download_report"]					= "Download Report";
$pgv_lang["reports"]							= "Αναφορές";
$pgv_lang["pdf_reports"]						= "PDF Αναφορές";
$pgv_lang["html_reports"]						= "HTML Αναφορές";
$pgv_lang["family_group_report"]				= "Family Group Report";
$pgv_lang["page"]								= "Σελίδα";
$pgv_lang["of"]									= "από";
$pgv_lang["enter_famid"]						= "Enter Family ID";
$pgv_lang["show_sources"]						= "Εμφάνιση πηγών;";
$pgv_lang["show_notes"]							= "Εμφάνιση σημειώσεων;?";
$pgv_lang["show_basic"]							= "Print basic events when blank?";
$pgv_lang["show_photos"]						= "Εμφάνιση Φωτογραφιών;";
$pgv_lang["individual_report"]					= "Αναφορά Ατόμου";
$pgv_lang["enter_pid"]							= "Enter Individual ID";
$pgv_lang["individual_list_report"]				= "Individual List Report";
$pgv_lang["generated_by"]						= "Generated by";
$pgv_lang["list_children"]						= "List each child in order of birth.";
$pgv_lang["birth_report"]						= "Birth Date and Place Report";
$pgv_lang["birthplace"]							= "Birth Place contains";
$pgv_lang["birthdate1"]							= "Birth Date range start";
$pgv_lang["birthdate2"]							= "Birth Date range end";
$pgv_lang["sort_by"]							= "Ταξινόμιση ως προς";

$pgv_lang["cleanup"]							= "Καθαρισμός";
$pgv_lang["skip_cleanup"]						= "Skip Cleanup";

//-- CONFIGURE (extra) messgaes for programs patriarch, slklist and statistics
$pgv_lang["dynasty_list"]						= "Ανασκόπιση Οικογενειών";
$pgv_lang["make_slklist"]						= "Create EXCEL (SLK) list";
$pgv_lang["excel_list"]							= "Output in EXCEL (slk) format on the following files (first use patriarchlist):";
$pgv_lang["excel_tab"]							= "tabblad:";
$pgv_lang["excel_create"]						= " will be created on file:";
$pgv_lang["patriarch_list"]						= "Patriarch list";
$pgv_lang["slk_list"]							= "EXCEL SLK list";
$pgv_lang["statistics"]							= "Στατιστικά";

//-- Merge Records
$pgv_lang["merge_records"]						= "Merge Records";
$pgv_lang["merge_same"]							= "Records are not the same type.  Cannot merge records that are not the same type.";
$pgv_lang["merge_step1"]						= "Merge Step 1 of 3";
$pgv_lang["merge_step2"]						= "Merge Step 2 of 3";
$pgv_lang["merge_step3"]						= "Merge Step 3 of 3";
$pgv_lang["select_gedcom_records"]				= "Select 2 GEDCOM records to merge.   Records must be of the same type.";
$pgv_lang["merge_to"]							= "Merge To ID:";
$pgv_lang["merge_from"]							= "Merge From ID:";
$pgv_lang["merge_facts_same"]					= "The following facts were exactly the same in both records and will automatically be merged";
$pgv_lang["no_matches_found"]					= "No matching facts found";
$pgv_lang["unmatching_facts"]					= "The following facts did not match.   Select the information you would like to keep.";
$pgv_lang["record"]								= "Record";
$pgv_lang["adding"]								= "Adding";
$pgv_lang["updating_linked"]					= "Updating linked record";
$pgv_lang["merge_more"]							= "Merge more records.";
$pgv_lang["same_ids"]							= "You entered the same IDs.  You cannot merge the same records.";

//-- ANCESTRY FILE MESSAGES
$pgv_lang["ancestry_chart"]						= "Διάγραμμα Προγόνων";
$pgv_lang["gen_ancestry_chart"]					= "#PEDIGREE_GENERATIONS# Generation Ancestry Chart";
$pgv_lang["chart_style"]						= "Chart style";
$pgv_lang["ancestry_list"]						= "Κατάλογος Προγόνων";
$pgv_lang["ancestry_booklet"]					= "Βιβλίο Προγόνων";
// 1st generation
$pgv_lang["sosa_2"]								= "Πατέρας";
$pgv_lang["sosa_3"]								= "Μητέρα";
// 2nd generation
$pgv_lang["sosa_4"]								= "Παππούς";
$pgv_lang["sosa_5"]								= "Γιαγιά";
$pgv_lang["sosa_6"]								= "Παππούς";
$pgv_lang["sosa_7"]								= "Γιαγιά";
// 3rd generation
$pgv_lang["sosa_8"]								= "Προ-Πάππους";
$pgv_lang["sosa_9"]								= "Προ-Γιαγιά";
$pgv_lang["sosa_10"]							= "Προ-Πάππους";
$pgv_lang["sosa_11"]							= "Προ-Γιαγιά";
$pgv_lang["sosa_12"]							= "Προ-Πάππους";
$pgv_lang["sosa_13"]							= "Προ-Γιαγιά";
$pgv_lang["sosa_14"]							= "Προ-Πάππους";
$pgv_lang["sosa_15"]							= "Προ-Γιαγιά";
// 4th generation
$pgv_lang["sosa_16"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_17"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_18"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_19"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_20"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_21"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_22"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_23"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_24"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_25"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_26"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_27"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_28"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_29"]							= "Προ-Προ-Γιαγιά";
$pgv_lang["sosa_30"]							= "Προ-Προ-Πάππους";
$pgv_lang["sosa_31"]							= "Προ-Προ-Γιαγιά";
// 5th generation
$pgv_lang["sosa_32"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_33"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_34"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_35"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_36"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_37"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_38"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_39"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_40"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_41"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_42"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_43"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_44"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_45"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_46"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_47"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_48"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_49"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_50"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_51"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_52"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_53"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_54"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_55"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_56"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_57"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_58"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_59"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_60"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_61"]							= "Προ-Προ-Προ-Γιαγιά";
$pgv_lang["sosa_62"]							= "Προ-Προ-Προ-Πάππους";
$pgv_lang["sosa_63"]							= "Προ-Προ-Προ-Γιαγιά";

//-- FAN CHART
$pgv_lang["fan_chart"]							= "Διάγραμμα Βεντάλια";
$pgv_lang["gen_fan_chart"]						= "Διάγραμμα Βεντάλια #PEDIGREE_GENERATIONS# Γενεών";
$pgv_lang["fan_width"]							= "Μήκος Βεντάλιας";
$pgv_lang["gd_library"]							= "PHP server misconfiguration : GD library required to use image functions.";
$pgv_lang["gd_freetype"]						= "PHP server misconfiguration : Freetype library required to use TrueType fonts.";
$pgv_lang["gd_helplink"]						= "http://www.php.net/gd";
$pgv_lang["fontfile_error"]						= "Font file not found on PHP server";

//-- RSS Feed

$pgv_lang["rss_descr"]							= "Νέα και σύνδεσμοι από το #GEDCOM_TITLE# site";
$pgv_lang["rss_logo_descr"]						= "Feed created by PhpGedView";

if (file_exists("languages/lang.en.extra.php")) require "languages/lang.en.extra.php";
?>

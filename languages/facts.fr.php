<?php
/**
 * French Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Christophe Bx, Julien Damon
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
 * @subpackage Languages
 * @author Christophe Bx
 * @author Julien Damon
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their English values
$factarray["ABBR"]                      = "Abréviation";
$factarray["ADDR"]                      = "Adresse";
$factarray["ADR1"]                      = "Adresse 1";
$factarray["ADR2"]                      = "Adresse 2";
$factarray["ADOP"]                      = "Adoption";
$factarray["AFN"]                       = "N° AFN";
$factarray["AGE"]                       = "Age";
$factarray["AGNC"]                      = "Institution";
$factarray["ALIA"]                      = "Alias";
$factarray["ANCE"]                      = "Ancêtres";
$factarray["ANCI"]                      = "Interêt des ancêtres";
$factarray["ANUL"]                      = "Déclaration de nullité du mariage";
$factarray["ASSO"]                      = "Personne associée";
$factarray["AUTH"]                      = "Auteur";
$factarray["BAPL"]                      = "Baptême (LDS)";
$factarray["BAPM"]                      = "Baptême";
$factarray["BARM"]                      = "Bar_mitzvah";
$factarray["BASM"]                      = "Bas_mitzvah";
$factarray["BIRT"]                      = "Naissance";
$factarray["BLES"]                      = "Bénédiction religieuse";
$factarray["BLOB"]                      = "Objet binaire";
$factarray["BURI"]                      = "Sépulture";
$factarray["CALN"]                      = "N° d'appel";
$factarray["CAST"]                      = "Caste";
$factarray["CAUS"]                      = "Cause de la mort";
$factarray["CEME"]                      = "Cimetière";
$factarray["CENS"]                      = "Recensement";
$factarray["CHAN"]                      = "Modification";
$factarray["CHAR"]                      = "Jeu de caractères";
$factarray["CHIL"]                      = "Enfant";
$factarray["CHR"]                       = "Baptême religieux enfant";
$factarray["CHRA"]                      = "Baptême religieux adulte";
$factarray["CITY"]                      = "Localité";
$factarray["CONF"]                      = "Confirmation";
$factarray["CONL"]                      = "Confirmation (LDS)";
$factarray["COPR"]                      = "Copyright";
$factarray["CORP"]                      = "Institution";
$factarray["CREM"]                      = "Incinération";
$factarray["CTRY"]                      = "Pays";
$factarray["DATA"]                      = "Données";
$factarray["DATE"]                      = "Date";
$factarray["DEAT"]                      = "Décès";
$factarray["DESC"]                      = "Descendants";
$factarray["DESI"]                      = "Intérêt des descendants";
$factarray["DEST"]                      = "Destination";
$factarray["DIV"]                       = "Divorce";
$factarray["DIVF"]                      = "Divorce prononcé";
$factarray["DSCR"]                      = "Description";
$factarray["EDUC"]                      = "Etudes";
$factarray["EMIG"]                      = "Emigration";
$factarray["ENDL"]                      = "Dotation (LDS)";
$factarray["ENGA"]                      = "Fiançailles";
$factarray["EVEN"]                      = "Evénement";
$factarray["FAM"]                       = "Famille";
$factarray["FAMC"]                      = "Famille de l'enfant";
$factarray["FAMF"]                      = "Fichier de la famille";
$factarray["FAMS"]                      = "Famille des conjoints";
$factarray["FCOM"]                      = "Première communion";
$factarray["FILE"]                      = "Fichier externe";
$factarray["FORM"]                      = "Format";
$factarray["GIVN"]                      = "Prénom(s)";
$factarray["GRAD"]                      = "Diplôme";
$factarray["HUSB"]                      = "Mari";
$factarray["IDNO"]                      = "N° identification";
$factarray["IMMI"]                      = "Immigration";
$factarray["LEGA"]                      = "Légataire";
$factarray["MARB"]                      = "Bans de mariage";
$factarray["MARC"]                      = "Contrat de mariage";
$factarray["MARL"]                      = "Autorisation légale de mariage";
$factarray["MARR"]                      = "Mariage";
$factarray["MARS"]                      = "Régime matrimonial";
$factarray["MEDI"]                      = "Type de Media";
$factarray["NAME"]                      = "Nom";
$factarray["NATI"]                      = "Nationalité";
$factarray["NATU"]                      = "Naturalisation";
$factarray["NCHI"]                      = "Nombre d'enfants";
$factarray["NICK"]                      = "Surnom";
$factarray["NMR"]                       = "Nombre de mariages";
$factarray["NOTE"]                      = "Note";
$factarray["NPFX"]                      = "Préfixe du nom";
$factarray["NSFX"]                      = "Suffixe du nom";
$factarray["OBJE"]                      = "Objet MultiMedia";
$factarray["OCCU"]                      = "Profession";
$factarray["ORDI"]                      = "Cérémonie (LDS)";
$factarray["ORDN"]                      = "Ordination";
$factarray["PAGE"]                      = "Page";
$factarray["PEDI"]                      = "Ascendance";
$factarray["PLAC"]                      = "Lieu";
$factarray["PHON"]                      = "Téléphone";
$factarray["POST"]                      = "Code Postal";
$factarray["PROB"]                      = "Testament validé";
$factarray["PROP"]                      = "Biens et possessions";
$factarray["PUBL"]                      = "Publication";
$factarray["QUAY"]                      = "Fiabilité des données";
$factarray["REPO"]                      = "Dépositaire";
$factarray["REFN"]                      = "Référence";
$factarray["RELA"]                      = "Relation";
$factarray["RELI"]                      = "Religion";
$factarray["RESI"]                      = "Domicile";
$factarray["RESN"]                      = "Restriction d'accès";
$factarray["RETI"]                      = "Retraite";
$factarray["RFN"]                       = "N° enregistrement fichier";
$factarray["RIN"]                       = "N° enregistrement ID";
$factarray["ROLE"]                      = "Rôle";
$factarray["SEX"]                       = "Sexe";
$factarray["SLGC"]                      = "Scellement enfant (LDS)";
$factarray["SLGS"]                      = "Scellement conjoint (LDS)";
$factarray["SOUR"]                      = "Source";
$factarray["SPFX"]                      = "Préfixe du nom de famille";
$factarray["SSN"]                       = "Numéro de sécurité sociale";
$factarray["STAE"]                      = "Etat ou région ou département";
$factarray["STAT"]                      = "Statut";
$factarray["SUBM"]                      = "Fournisseur";
$factarray["SUBN"]                      = "Données fournies";
$factarray["SURN"]                      = "Nom de famille";
$factarray["TEMP"]                      = "Temple (LDS)";
$factarray["TEXT"]                      = "Texte";
$factarray["TIME"]                      = "Heure";
$factarray["TITL"]                      = "Titre";
$factarray["TYPE"]                      = "Type";
$factarray["WIFE"]                      = "Femme";
$factarray["WILL"]                      = "Testament";
$factarray["_EMAIL"]                    = "Adresse courriel";
$factarray["EMAIL"]                     = "Adresse courriel";
$factarray["_TODO"]                     = "Note";
$factarray["_UID"]                      = "Identificateur universel (UID)";
$factarray["_PRIM"]                     = "Image principale";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]                     = "Médical";
$factarray["_DEG"]                      = "Diplôme";
$factarray["_MILT"]                     = "Service Militaire";
$factarray["_SEPR"]                     = "Séparé";
$factarray["_DETS"]                     = "Décès du conjoint";
$factarray["CITN"]                      = "Citoyenneté";
$factarray["_FA1"]                      = "Evènement 1";
$factarray["_FA2"]                      = "Evènement 2";
$factarray["_FA3"]                      = "Evènement 3";
$factarray["_FA4"]                      = "Evènement 4";
$factarray["_FA5"]                      = "Evènement 5";
$factarray["_FA6"]                      = "Evènement 6";
$factarray["_FA7"]                      = "Evènement 7";
$factarray["_FA8"]                      = "Evènement 8";
$factarray["_FA9"]                      = "Evènement 9";
$factarray["_FA10"]                     = "Evènement 10";
$factarray["_FA11"]                     = "Evènement 11";
$factarray["_FA12"]                     = "Evènement 12";
$factarray["_FA13"]                     = "Evènement 13";
$factarray["_MREL"]                     = "Lien avec la mère";
$factarray["_FREL"]                     = "Lien avec le père";
$factarray["_MSTAT"]                    = "Début Mariage (LDS)";
$factarray["_MEND"]                     = "Fin Mariage (LDS)";

// GEDCOM 5.5.1 related facts
$factarray["FAX"]                       = "Fax";
$factarray["FACT"]                      = "Evènement";
$factarray["WWW"]                       = "Page Web";
$factarray["MAP"]                       = "Carte";
$factarray["LATI"]                      = "Latitude";
$factarray["LONG"]                      = "Longitude";
$factarray["FONE"]                      = "Phonétique";
$factarray["ROMN"]                      = "Alphabet Romain";

// PAF related facts
$factarray["_NAME"]                     = "Adresse Mailing";
$factarray["URL"]                       = "URL";
$factarray["_HEB"]                      = "Hébreu";
$factarray["_SCBK"]                     = "Album";
$factarray["_TYPE"]                     = "Type MultiMedia";
$factarray["_SSHOW"]                    = "Diaporama";

// Rootsmagic
$factarray["_SUBQ"]                     = "Version courte";
$factarray["_BIBL"]                     = "Bibliographie";

// Reunion
$factarray["EMAL"]                      = "Adresse courriel";

// Other common customized facts
$factarray["_ADPF"]                     = "Adoption par le père";
$factarray["_ADPM"]                     = "Adoption par la mère";
$factarray["_AKA"]                      = "Nom d'usage";
$factarray["_AKAN"]                     = "Nom d'usage";
$factarray["_BRTM"]                     = "Brit mila";
$factarray["_COML"]                     = "Mariage légal";
$factarray["_EYEC"]                     = "Couleur des yeux";
$factarray["_FNRL"]                     = "Funérailles";
$factarray["_GODP"]                     = "Parrain/Marraine";
$factarray["_HAIR"]                     = "Couleur des cheveux";
$factarray["_HEIG"]                     = "Taille";
$factarray["_HOL"]                      = "Holocauste";
$factarray["_INTE"]                     = "Inhumation";
$factarray["_MARI"]                     = "Promesse de mariage";
$factarray["_MARNM"]                    = "Nom de mariage";
$factarray["_MBON"]                     = "Lien par mariage";
$factarray["_MEDC"]                     = "Condition médicale";
$factarray["_MILI"]                     = "Militaire";
$factarray["_NLIV"]                     = "Non vivant(e)";
$factarray["_NMAR"]                     = "Jamais marié(e)";
$factarray["_NMR"]                      = "Non marié(e)";
$factarray["_PRMN"]                     = "Numéro permanent";
$factarray["_STAT"]                     = "Statut Mariage";
$factarray["_WEIG"]                     = "Poids";
$factarray["_WITN"]                     = "Témoin";
$factarray["_YART"]                     = "Yartzeit";
$factarray["COMM"]                      = "Commentaire";

// Aldfaer related facts
$factarray["MARR_CIVIL"]                = "Mariage civil";
$factarray["MARR_RELIGIOUS"]            = "Mariage religieux";
$factarray["MARR_PARTNERS"]             = "Partenaires";
$factarray["MARR_UNKNOWN"]              = "";

$factarray["_HNM"]                      = "Nom hébreu";

// Pseudo-facts for relatives
$factarray["_DEAT_SPOU"]                = "Décès du conjoint";

$factarray["_BIRT_CHIL"]                = "Naissance d'un enfant";
$factarray["_MARR_CHIL"]                = "Mariage d'un enfant";
$factarray["_DEAT_CHIL"]                = "Décès d'un enfant";

$factarray["_BIRT_GCHI"]                = "Naissance d'un petit-enfant";
$factarray["_MARR_GCHI"]                = "Mariage d'un petit-enfant";
$factarray["_DEAT_GCHI"]                = "Décès d'un petit-enfant";

$factarray["_MARR_FATH"]                = "Mariage du père";
$factarray["_DEAT_FATH"]                = "Décès du père";

$factarray["_MARR_MOTH"]                = "Mariage de la mère";
$factarray["_DEAT_MOTH"]                = "Décès de la mère";

$factarray["_BIRT_SIBL"]                = "Naissance frère/sœur";
$factarray["_MARR_SIBL"]                = "Mariage frère/sœur";
$factarray["_DEAT_SIBL"]                = "Décès frère/sœur";

$factarray["_BIRT_HSIB"]                = "Naissance demi-frère/sœur";
$factarray["_MARR_HSIB"]                = "Mariage demi-frère/sœur";
$factarray["_DEAT_HSIB"]                = "Décès demi-frère/sœur";

$factarray["_DEAT_GPAR"]                = "Décès d'un grand-parent";

$factarray["_BIRT_FSIB"]                = "Naissance frère/sœur du père";
$factarray["_MARR_FSIB"]                = "Mariage frère/sœur du père";
$factarray["_DEAT_FSIB"]                = "Décès frère/sœur du père";

$factarray["_BIRT_MSIB"]                = "Naissance frère/sœur de la mère";
$factarray["_MARR_MSIB"]                = "Mariage frère/sœur de la mère";
$factarray["_DEAT_MSIB"]                = "Décès frère/sœur de la mère";

$factarray["_BIRT_COUS"]                = "Naissance cousin(e) germain(e)";
$factarray["_MARR_COUS"]                = "Mariage cousin(e) germain(e)";
$factarray["_DEAT_COUS"]                = "Décès cousin(e) germain(e)";

//-- PGV Only facts
$factarray["_THUM"]                     = "Vignette";
$factarray["_PGVU"]                     = "Dernière modification par";
$factarray["SERV"]                      = "Serveur distant";
$factarray["_GEDF"]                     = "Fichier GEDCOM";

if (file_exists( "languages/facts.fr.extra.php")) require  "languages/facts.fr.extra.php";
?>

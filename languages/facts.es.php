<?php
/**
 * Latin American Spanish Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Ricardo Lago
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
 * @author Ricardo Lago
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their argentinian spanish values
$factarray["ABBR"] = "Abreviatura";
$factarray["ADDR"] = "Dirección";
$factarray["ADR1"] = "Dirección 1";
$factarray["ADR2"] = "Dirección 2";
$factarray["ADOP"] = "Adopción";
$factarray["AFN"]  = "(AFN)";
$factarray["AGE"]  = "Edad";
$factarray["AGNC"] = "Agencia";
$factarray["ALIA"] = "Alias";
$factarray["ANCE"] = "Antepasados";
$factarray["ANCI"] = "Antepasados de Interés";
$factarray["ANUL"] = "Anulación";
$factarray["ASSO"] = "Asociados";
$factarray["AUTH"] = "Autor";
$factarray["BAPL"] = "Bautismo SUD";
$factarray["BAPM"] = "Bautismo";
$factarray["BARM"] = "Bar Mitzvah";
$factarray["BASM"] = "Bas Mitzvah";
$factarray["BIRT"] = "Nacimiento";
$factarray["BLES"] = "Bendición";
$factarray["BLOB"] = "Objeto de Datos Binarios";
$factarray["BURI"] = "Entierro";
$factarray["CALN"] = "Referencia";
$factarray["CAST"] = "Estatus Social";
$factarray["CAUS"] = "Causa de la muerte";
$factarray["CENS"] = "Censo";
$factarray["CHAN"] = "último cambio";
$factarray["CHAR"] = "Juego de Caracteres";
$factarray["CHIL"] = "Hijo";
$factarray["CHR"]  = "Bautismo";
$factarray["CHRA"] = "Bautismo en edad adulta";
$factarray["CITY"] = "Ciudad";
$factarray["CONF"] = "Confirmación";
$factarray["CONL"] = "Confirmación SUD";
$factarray["COPR"] = "Copyright";
$factarray["CORP"] = "Corporación / Compañía";
$factarray["CREM"] = "Cremación";
$factarray["CTRY"] = "País";
$factarray["DATA"] = "Datos";
$factarray["DATE"] = "Fecha";
$factarray["DEAT"] = "Defunción";
$factarray["DESC"] = "Descendientes";
$factarray["DESI"] = "Descendientes de Interés";
$factarray["DEST"] = "Destino";
$factarray["DIV"]  = "Divorcio";
$factarray["DIVF"] = "Divorcio Archivado";
$factarray["DSCR"] = "Descripción";
$factarray["EDUC"] = "Educación";
$factarray["EMIG"] = "Emigración";
$factarray["ENDL"] = "Investidura SUD";
$factarray["ENGA"] = "Compromiso matrimonio";
$factarray["EVEN"] = "Evento";
$factarray["FAM"]  = "Familia";
$factarray["FAMC"] = "Familia como hijo";
$factarray["FAMF"] = "Fichero Familia";
$factarray["FAMS"] = "Familia como cónyuge";
$factarray["FCOM"] = "Primera Communión";
$factarray["FILE"] = "Fichero Externo";
$factarray["FORM"] = "Formato:";
$factarray["GIVN"] = "Habitualmente nombrado";
$factarray["GRAD"] = "Graduación";
$factarray["IDNO"] = "Numero de Identificación";
$factarray["IMMI"] = "Immigración";
$factarray["LEGA"] = "Herencia";
$factarray["MARB"] = "Amonestaciones";
$factarray["MARC"] = "Contrato Matrimonial";
$factarray["MARL"] = "Licencia Matrimonial";
$factarray["MARR"] = "Matrimonio";
$factarray["MARS"] = "Dote";
$factarray["NAME"] = "Nombre";
$factarray["NATI"] = "Nacionalidad";
$factarray["NATU"] = "Natural";
$factarray["NCHI"] = "Número de Hijos";
$factarray["NICK"] = "Apodo";
$factarray["NMR"]  = "Número de matrimonios";
$factarray["NOTE"] = "Nota";
$factarray["NPFX"] = "Prefijo";
$factarray["NSFX"] = "Sufijo";
$factarray["OBJE"] = "Objeto Multimedia";
$factarray["OCCU"] = "Ocupación";
$factarray["ORDI"] = "Ordenanza";
$factarray["ORDN"] = "Ordenación";
$factarray["PAGE"] = "Detalles";
$factarray["PEDI"] = "Antepasados";
$factarray["PLAC"] = "Lugar";
$factarray["PHON"] = "Telef.";
$factarray["POST"] = "Código Postal";
$factarray["PROB"] = "Certificado Testamento";
$factarray["PROP"] = "Propiedad";
$factarray["PUBL"] = "Publicación";
$factarray["QUAY"] = "Calidad de los datos";
$factarray["REPO"] = "Archivo";
$factarray["REFN"] = "Número Ref";
$factarray["RELI"] = "Religión";
$factarray["RESI"] = "Residencia";
$factarray["RESN"] = "Restricción";
$factarray["RETI"] = "Jubilación";
$factarray["RFN"]  = "Número de archivo del registro";
$factarray["RIN"]  = "Número ID";
$factarray["ROLE"] = "Rol";
$factarray["SEX"]  = "Sexo";
$factarray["SLGC"] = "Sellam. SUD hijo";
$factarray["SLGS"] = "Sellam. SUD cónyuge";
$factarray["SOUR"] = "Fuente";
$factarray["SPFX"] = "Prefijo del Apellido";
$factarray["SSN"]  = "Número Seguridad Social";
$factarray["STAE"] = "Estado";
$factarray["STAT"] = "Estatus";
$factarray["SUBM"] = "Remitente";
$factarray["SUBN"] = "Envío";
$factarray["SURN"] = "Apellido";
$factarray["TEMP"] = "Templo";
$factarray["TEXT"] = "Texto";
$factarray["TIME"] = "Tiempo";
$factarray["TITL"] = "Título";
$factarray["WILL"] = "Testamento";
$factarray["TYPE"] = "Tipo";
$factarray["_EMAIL"] = "Email";
$factarray["EMAIL"] = "Correo electrónico";
$factarray["_TODO"]  = "Hacer Item";
$factarray["_UID"]   = "Identificador Universal";
// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] = "Médico";
$factarray["_DEG"]  = "Grado";
$factarray["_MILT"] = "Servicio Militar";
$factarray["_SEPR"] = "Separado";
$factarray["_DETS"] = "Fallecimiento de un cónyuge";
$factarray["CITN"]  = "Ciudadanía";
$factarray["_FA1"]  = "Acontecimiento 1";
$factarray["_FA2"]  = "Acontecimiento 2";
$factarray["_FA3"]  = "Acontecimiento 3";
$factarray["_FA4"]  = "Acontecimiento 4";
$factarray["_FA5"]  = "Acontecimiento 5";
$factarray["_FA6"]  = "Acontecimiento 6";
$factarray["_FA7"]  = "Acontecimiento 7";
$factarray["_FA8"]  = "Acontecimiento 8";
$factarray["_FA9"]  = "Acontecimiento 9";
$factarray["_FA10"] = "Acontecimiento 10";
$factarray["_FA11"] = "Acontecimiento 11";
$factarray["_FA12"] = "Acontecimiento 12";
$factarray["_FA13"] = "Acontecimiento 13";
$factarray["_MREL"] = "Relacción con la Madre";
$factarray["_FREL"] = "Relacción con el Padre";
$factarray["_FA1"]  = "Matrimonio";
$factarray["_MSTAT"]= "Comienzo del matrimonio";
$factarray["_MEND"] = "Final del matrimonio";

// Other common customized facts
$factarray["_ADPF"] = "Adoptado por el padre";
$factarray["_ADPM"] = "Adoptado por la madre";
$factarray["_AKAN"] = "También conocido como";
$factarray["_AKA"] 	= "También conocido como";
$factarray["_BRTM"] = "Brit mila";
$factarray["_COML"] = "Derecho matrimonial";
$factarray["_EYEC"] = "Color de ojos";
$factarray["_FNRL"] = "Funeral";
$factarray["_HAIR"] = "Color de pelo";
$factarray["_HEIG"] = "Altura";
$factarray["_INTE"] = "Entierro";
$factarray["_MARI"] = "Proposición de matrimonio";
$factarray["_MBON"] = "Lazo matrimonial";
$factarray["_MEDC"] = "Estado médico";
$factarray["_MILI"] = "Militar";
$factarray["_NMR"]  = "Soltero";
$factarray["_NLIV"] = "Fallecido";
$factarray["_NMAR"] = "Nunca contrajo matrimonio";
$factarray["_PRMN"] = "Número fijo";
$factarray["_WEIG"] = "Peso";
$factarray["_YART"] = "Yartzeit";

if (file_exists( "languages/facts.es.extra.php")) require  "languages/facts.es.extra.php";

?>

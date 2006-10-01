<?php
/**
 * Spanish Language file for PhpGedView.
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
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "Usted no puede acceder a este archivo de idioma directamente.";
	exit;
}

//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]				    = "?";
$pgv_lang["page_help"]			= "Ayuda";

//-- CONFIG FILE MESSAGES
$pgv_lang["error_title"]		= "ERROR: No es posible abrir el archivo GEDCOM";
$pgv_lang["error_header"] 		= "El archivo GEDCOM, [#GEDCOM#], No existe en la localización especificada.";
$pgv_lang["for_support"]		= "Para ayuda o información póngase en contacto con";
$pgv_lang["for_contact"]		= "Para ayuda por cuestiones de genealogía contacte con";
$pgv_lang["for_all_contact"]	= "Para soporte técnico o por cuestiones de genealogía, por favor contacte con";
$pgv_lang["build_title"]		= "Construyendo el índice";
$pgv_lang["build_error"]		= "El arhivo GEDCOM ha sido actualizado.";
$pgv_lang["please_wait"]		= "Por favor espere mientras se reconstruye el índice.";
$pgv_lang["choose_gedcom"]		= "Seleccione un archivo GEDCOM";
$pgv_lang["username"]			= "Usuario:";
$pgv_lang["password"]			= "Contraseña:";
$pgv_lang["login"]				= "Entrar";
$pgv_lang["login_aut"]			= "Editar Usuario";
$pgv_lang["logout"]				= "Salir";
$pgv_lang["admin"]				= "Admin.";
$pgv_lang["logged_in_as"]		= "Registrado ";
$pgv_lang["my_pedigree"]		= "Mi árbol";
$pgv_lang["my_indi"]			= "Mi Ficha";
$pgv_lang["yes"]				= "Sí";
$pgv_lang["no"]					= "No";
$pgv_lang["add_gedcom"]			= "Añadir otro GEDCOM";
$pgv_lang["no_support"]			= "Hemos detectado que su navegador no admite los estándares utilizados por este sitio con PhpGedView.  La mayoría de los navegadores admiten estos estándares en sus nuevas versiones. Por favor actualice su navegador a una versión reciente.";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]		= "Antepasados";
$pgv_lang["gen_ped_chart"]  	= "#PEDIGREE_GENERATIONS# Generaciones de Antepasados";
$pgv_lang["generations"]		= "Generaciones";
$pgv_lang["view"]				= "Ver";
$pgv_lang["fam_spouse"]	    	= "Familia con el cónyuge:";
$pgv_lang["root_person"]    	= "ID persona raíz:";
$pgv_lang["hide_details"]	    = "Ocultar detalles";
$pgv_lang["show_details"]	    = "Ver detalles";
$pgv_lang["person_links"]	    = "Enlaces a informes y familiares cercanos relaccionados con esta persona.";
$pgv_lang["zoom_box"]		    = "Aumentar/Disminuir registro.";
$pgv_lang["portrait"]		    = "Retrato";
$pgv_lang["landscape"]		    = "Apaisado";
$pgv_lang["start_at_parents"]	= "Comenzar en los padres";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]	        = "Imposible encontrar una familia con el ID";
$pgv_lang["unable_to_find_indi"]		    = "Imposible encontrar una persona con el ID";
$pgv_lang["unable_to_find_record"]	        = "Imposible encontrar un registro con el ID";
$pgv_lang["unable_to_find_source"]      	= "Imposible encontrar una fuente con el ID";
$pgv_lang["unable_to_find_repo"]	        = "Imposible encontrar la información con el ID";
$pgv_lang["repo_name"]					    = "Nombre Archivo:";
$pgv_lang["address"]						= "Dirección:";
$pgv_lang["phone"]							= "Teléfono:";
$pgv_lang["source_name"]					= "Fuente:";
$pgv_lang["title"]							= "Título:";
$pgv_lang["author"]							= "Autor:";
$pgv_lang["publication"]					= "Publicación:";
$pgv_lang["call_number"]					= "Número a llamar:";
$pgv_lang["living"]				            = "Viva";
$pgv_lang["private"]						= "Privada";
$pgv_lang["birth"]						    = "Nac:";
$pgv_lang["death"]							= "Def:";
$pgv_lang["descend_chart"]				    = "Descendientes";
$pgv_lang["individual_list"]				= "Personas";
$pgv_lang["family_list"]					= "Familias";
$pgv_lang["source_list"]					= "Fuentes";
$pgv_lang["place_list"]						= "Lugares";
$pgv_lang["media_list"]						= "Multimedia";
$pgv_lang["search"]							= "Buscar";
$pgv_lang["clippings_cart"]				    = "Carrito genealógico";
$pgv_lang["not_an_array"]				    = "No existe en la lista";
$pgv_lang["print_preview"]				    = "Vista Preliminar";
$pgv_lang["change_lang"]					= "Cambie el Idioma";
$pgv_lang["print"]				            = "Imprimir";
$pgv_lang["total_queries"]		            = "Consultas a la Base de Datos: ";
$pgv_lang["back"]				            = "Regresar";

//-- INDIVUDUAL FILE MESSAGES
$pgv_lang["male"]				= "Hombre";
$pgv_lang["female"]				= "Mujer";
$pgv_lang["NN"]					= "Desconocido";
$pgv_lang["PN"]					= "Desconocido";
$pgv_lang["temple"]				= "Templo SUD";
$pgv_lang["temple_code"]		= "Código Templo SUD:";
$pgv_lang["status"]				= "Estatus";
$pgv_lang["source"]				= "Fuente";
$pgv_lang["citation"]			= "Cita";
$pgv_lang["text"]				= "Texto:";
$pgv_lang["note"]				= "Nota";
$pgv_lang["unrecognized_code"]	= "Código GEDCOM desconocido";
$pgv_lang["indi_info"]			= "Ficha Personal";
$pgv_lang["pedigree_chart"] 	= "Ascendientes";
$pgv_lang["desc_chart2"]		= "Descendientes";
$pgv_lang["family"]				= "Familia";
$pgv_lang["as_spouse"]			= "Familia con el Cónyuge";
$pgv_lang["as_child"]			= "Familia con los Padres";
$pgv_lang["view_gedcom"]	    = "Ver GEDCOM";
$pgv_lang["add_to_cart"]		= "Añadir al Carrito";
$pgv_lang["still_living_error"]	= "Esta persona todavía está viva o su defunción no ha sido registrada.  La información de las personas vivas no es pública.<br />Para más información contactar con";
$pgv_lang["privacy_error"]	    = "Los detalles de esta persona son privados.<br />Para más información contactar con";
$pgv_lang["more_information"]	= "Para más información contacte con";
$pgv_lang["name"]				= "Nombre: ";
$pgv_lang["given_name"]		    = "Nombre habitual:";
$pgv_lang["surname"]			= "Apellido:";
$pgv_lang["suffix"]				= "Sufijo:";
$pgv_lang["object_note"]		= "Nota del Objeto:";
$pgv_lang["sex"]				= "Sexo:";
$pgv_lang["personal_facts"]	    = "Detalles Personales";
$pgv_lang["type"]				= "Tipo";
$pgv_lang["date"]				= "Fecha";
$pgv_lang["place_description"]	= "Lugar";
$pgv_lang["parents"] 			= "Padres:";
$pgv_lang["siblings"] 			= "Hermano";
$pgv_lang["father"] 			= "Padre";
$pgv_lang["mother"] 			= "Madre";
$pgv_lang["relatives"]			= "Familiares Cercanos";
$pgv_lang["child"]				= "Hijo";
$pgv_lang["spouse"]				= "Cónyuge";
$pgv_lang["surnames"]			= "Apellidos";
$pgv_lang["adopted"]			= "Adoptado";
$pgv_lang["foster"]				= "Adoptivo";
$pgv_lang["sealing"]			= "Voto";
$pgv_lang["link_as"]			= "Enlazar esta persona a una familia existente como un ";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]				= "Información Familiar";
$pgv_lang["family_group_info"]	        = "Información del Grupo Familiar";
$pgv_lang["husband"]					= "Esposo";
$pgv_lang["wife"]						= "Esposa";
$pgv_lang["marriage"]					= "Matrimonio:";
$pgv_lang["lds_sealing"]				= "Voto SUD:";
$pgv_lang["marriage_license"]		    = "Acta matrimonial:";
$pgv_lang["media_object"]				= "Objeto Multimedia:";
$pgv_lang["children"]					= "Hijos:";
$pgv_lang["no_children"]				= "Sin Hijos";
$pgv_lang["parents_timeline"]			= "Ver a los padres en<br />el gráfico de edades";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]					= "Carrito Genealógico";
$pgv_lang["clip_explaination"]		    = "El Carrito Genealógico le permite tomar \"segmentos\" del árbol familiar y reunirlos en un solo archivo GEDCOM para descargar.<br /><br />";
$pgv_lang["item_with_id"]			    = "Item con ID";
$pgv_lang["error_already"]			    = "ya está en su carrito.";
$pgv_lang["which_links"]				= "¿Qué enlaces de esta familia le gustaría añadir?";
$pgv_lang["just_family"]				= "Añadir este registro familiar.";
$pgv_lang["parents_and_family"]	        = "Añadir los padres con este registro familiar.";
$pgv_lang["parents_and_child"]	        = "Añadir los padres e hijos con este registro familiar.";
$pgv_lang["parents_desc"]				= "Añadir los padres y todos los registros de los descendientes con este registro familiar.";
$pgv_lang["continue"]					= "Continúe Añadiendo";
$pgv_lang["which_p_links"]			    = "¿Qué enlaces de esta persona le gustaría añadir también?";
$pgv_lang["just_person"]				= "Añadir esta persona.";
$pgv_lang["person_parents_sibs"]        = "Añadir esta persona, sus padres y hermanos.";
$pgv_lang["person_ancestors"]		    = "Añadir esta persona y su línea de antepasados directos.";
$pgv_lang["person_ancestor_fams"]       = "Añadir esta persona, su línea de antepasados directos y sus familias.";
$pgv_lang["person_spouse"]			    = "Añadir esta persona, su cónyuge, e hijos.";
$pgv_lang["person_desc"]				= "Añadir esta persona, su cónyuge, y todos los registros de sus descendientes.";
$pgv_lang["unable_to_open"]		        = "No es posible abrir la carpeta del Carrito para escritura";
$pgv_lang["person_living"]			    = "Esta persona está viva. Los detalles personales no se pueden ver.";
$pgv_lang["person_private"]			    = "Los detalles sobre esta persona son privados. Los detalles personales no se pueden ver.";
$pgv_lang["family_private"]		        = "Los detalles sobre esta familia son privados. Los detalles de esta familia no se pueden ver.";
$pgv_lang["download"]					= "Haga clic con el botón derecho (control-click en un Mac) en los enlaces y seleccione \"Salvar como\" para descargar los archivos.";
$pgv_lang["media_files"]				= "Archivos multimedia  referenciados en este GEDCOM";
$pgv_lang["cart_is_empty"]			    = "Tu carrito está vacío.";
$pgv_lang["id"]							= "ID";
$pgv_lang["name_description"]		    = "Nombre / Descripción";
$pgv_lang["remove"]						= "Vaciar";
$pgv_lang["empty_cart"]				    = "Vaciar Carro";
$pgv_lang["download_now"]			    = "Descargar ahora";
$pgv_lang["indi_downloaded_from"]	    = "La información de esta persona se descargó desde:";
$pgv_lang["family_downloaded_from"]	    = "La información de esta familia se descargó desde:";
$pgv_lang["source_downloaded_from"]	    = "La información de esta fuente se descargó desde:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]				= "Lugares encontrados<br />Ver resultados";
$pgv_lang["top_level"]					= "Lugares investigados";
$pgv_lang["form"]						= "Los lugares se codifican en la forma: ";
$pgv_lang["default_form"]			    = "Parroquia, Ayuntamiento, Provincia, Pais/Estado";
$pgv_lang["default_form_info"]			= "(Por defecto)";
$pgv_lang["gedcom_form_info"]			= "(GEDCOM)";
$pgv_lang["unknown"]					= "Desconocido";
$pgv_lang["individuals"]			    = "Personas";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["multi_title"]				= "Lista Multimedia";
$pgv_lang["media_found"]				= "Objetos Multimedia";
$pgv_lang["view_person"]				= "Ver Persona";
$pgv_lang["view_family"]				= "Ver Familia";
$pgv_lang["view_source"]				= "Ver Fuente";
$pgv_lang["prev"]						= "< Anterior";
$pgv_lang["next"]						= "Siguiente >";
$pgv_lang["file_not_found"]				= "No encontrado.";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]			    = "Buscar archivos GEDCOM";
$pgv_lang["enter_terms"]				= "Escriba una palabra";
$pgv_lang["soundex_search"]			    = "Búsqueda por Soundex";
$pgv_lang["search_results"]			    = "Resultados de la búsqueda";
$pgv_lang["sources"]					= "Fuentes";
$pgv_lang["firstname_search"]			= "Nombre: ";
$pgv_lang["lastname_search"]			= "Apellido: ";
$pgv_lang["search_place"]				= "Lugar: ";
$pgv_lang["search_year"]				= "Año: ";
$pgv_lang["lastname_empty"]			    = "Por favor escriba un apellido.";
$pgv_lang["no_results"]				    = "No se encontraron resultados.";
$pgv_lang["soundex_results"]		    = "Ojalá que los siguientes registros le sean de ayuda.";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]			    = "Fuentes encontradas";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]				= "Información de la Fuente";
$pgv_lang["other_records"]			    = "Otros registros que enlazan con esta fuente:";
$pgv_lang["people"]						= "Personas";
$pgv_lang["families"]					= "Familias";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]			= "Construyendo el índice de Personas y Familias";
$pgv_lang["building_index"]			= "Construyendo el índice";
$pgv_lang["importing_records"]		= "Importando Registros a la Base de datos";
$pgv_lang["detected_change"]		= "PhpGedView detectó un cambio en el archivo #GEDCOM#.  El archivo índice debe de ser reconstruído de nuevo.";
$pgv_lang["please_be_patient"]		= "POR FAVOR SEA PACIENTE";
$pgv_lang["reading_file"]			= "Leyendo archivo GEDCOM";
$pgv_lang["flushing"]				= "Depurando Contenidos";
$pgv_lang["found_record"]			= "Registros encontrados";
$pgv_lang["exec_time"]				= "Tiempo de ejecución:";
$pgv_lang["unable_to_create_index"] = "No se pudo crear un archivo índice. Asegúrese de que los permisos de escritura están habilitados en el directorio de PhpGedView.";
$pgv_lang["indi_complete"]			= "Archivo índice de Personas completamente actualizado.";
$pgv_lang["family_complete"]		= "Archivo índice de Familias completamente actualizado.";
$pgv_lang["source_complete"]		= "Archivo índice de Fuentes completamente actualizado.";
$pgv_lang["tables_exist"]			= "Estas tablas PhpGedView ya existen en la base de datos";
$pgv_lang["you_may"]				= "Debe:";
$pgv_lang["drop_tables"]			= "Eliminar las tablas actuales";
$pgv_lang["import_multiple"]		= "Importar y trabajar con múltiples GEDCOMs";
$pgv_lang["explain_options"]		= "Si escoje eliminar las tablas todos los datos de que disponía sern reemplazados con los de este GEDCOM<br />Si escoje importar y trabajar con múltiples GEDCOMs, PhpGedView borrará todos los datos que sean importados utilizando un GEDCOM con el mismo nombre de archivo.  Esta opción le permite guardar múltiples GEDCOM en las mismas tablas y escoger fácilmente entre ellos.";
$pgv_lang["path_to_gedcom"]			= "Escriba la ruta de su archivo GEDCOM:";
$pgv_lang["gedcom_title"]			= "Escriba un título que describa los datos de este archivo:";
$pgv_lang["dataset_exists"]			= "Un archivo GEDCOM con este nombre ya ha sido importado en esta Base de datos.";
$pgv_lang["empty_dataset"]			= "¿Quiere vaciar los datos?";
$pgv_lang["index_complete"]			= "índice completo.";
$pgv_lang["click_here_to_go_to_pedigree_tree"] = "Pulse aquí para ir al árbol de antepasados.";
$pgv_lang["updating_is_dead"]		= "La actualización está detenida para el INDI ";
$pgv_lang["import_complete"]		= "Importación completa";
$pgv_lang["updating_family_names"]	= "Actualizando los nombres de las familias para el FAM ";
$pgv_lang["processed_for"]			= "Archivo procesado para ";
$pgv_lang["run_tools"]				= "Quiere utilizar alguna de estas herramientas con su GEDCOM antes de importarlo:";
$pgv_lang["addmedia"]				= "Añadir Multimedia";
$pgv_lang["dateconvert"]			= "Conversión de Fechas";
$pgv_lang["xreftorin"]				= "Convertir los IDs XREF a número RIN";
$pgv_lang["tools_readme"]			= "Ver la Secció de Herramientas del archivo #README.TXT# para más información.";

//-- INDIVIDUAL AND FAMILY LIST FILE MESSAGES
$pgv_lang["total_fams"]					= "Familias encontradas";
$pgv_lang["total_indis"]				= "Personas encontradas";
$pgv_lang["starts_with"]				= "Letra Primera:";
$pgv_lang["person_list"]				= "Lista de Personas:";
$pgv_lang["paste_person"]			    = "Pegar Persona";
$pgv_lang["notes_sources_media"]	    = "Notas, Fuentes, y Multimedia";
$pgv_lang["name_contains"]			    = "Nombre Contiene:";
$pgv_lang["filter"]					    = "Filtro";
$pgv_lang["find_individual"]		    = "Encontar persona con ID";
$pgv_lang["find_sourceid"]			    = "Encontrar ID de la Fuente";
$pgv_lang["skip_surnames"]			    = "Ocultar listas de apellidos";
$pgv_lang["show_surnames"]			    = "Mostrar listas de apellidos";
$pgv_lang["all"]					    = "Todas";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]					    = "Edad";
$pgv_lang["timeline_title"]				= "Gráfico de edades";
$pgv_lang["timeline_chart"]			    = "Gráfico de edades";
$pgv_lang["remove_person"]			    = "Borrar Persona";
$pgv_lang["show_age"]					= "Ver Edad";
$pgv_lang["add_another"]				= "Agregar otra persona al gráfico:<br />Id Persona:";
$pgv_lang["find_id"]					= "Buscar ID";
$pgv_lang["show"]						= "Ver";
$pgv_lang["year"]						= "Año:";
$pgv_lang["timeline_instructions"]      = "En los navegadores actuales puede hacer clic en las cajas y arrastrarlas a lo largo de la lía.";
$pgv_lang["zoom_in"]				    = "Acercar";
$pgv_lang["zoom_out"]				    = "Alejar";

//-- MONTH NAMES
$pgv_lang["jan"]			= "Enero";
$pgv_lang["feb"]			= "Febrero";
$pgv_lang["mar"]			= "Marzo";
$pgv_lang["apr"]			= "Abril";
$pgv_lang["may"]			= "Mayo";
$pgv_lang["jun"]			= "Junio";
$pgv_lang["jul"]			= "Julio";
$pgv_lang["aug"]			= "Agosto";
$pgv_lang["sep"]			= "Septiembre";
$pgv_lang["oct"]			= "Octubre";
$pgv_lang["nov"]			= "Noviembre";
$pgv_lang["dec"]			= "Diciembre";
$pgv_lang["abt"]			= "hacia";
$pgv_lang["aft"]			= "después de";
$pgv_lang["and"]			= "y";
$pgv_lang["bef"]			= "antes de";
$pgv_lang["bet"]			= "entre";
$pgv_lang["cal"]			= "calculada";
$pgv_lang["est"]			= "estimada";
$pgv_lang["from"]			= "desde";
$pgv_lang["int"]			= "interpretada";
$pgv_lang["to"]				= "a";
$pgv_lang["cir"]			= "cercana";
$pgv_lang["apx"]			= "aprox.";

//-- Admin File Messages
$pgv_lang["select_an_option"]		= "Seleccione una opción:";
$pgv_lang["readme_documentation"]	= "Lea la documentación";
$pgv_lang["configuration"]			= "Configuración";
$pgv_lang["rebuild_indexes"]		= "Reconstruir los índices";
$pgv_lang["user_admin"]				= "Administrador";
$pgv_lang["user_created"]			= "Usuario creado correctamente.";
$pgv_lang["user_create_error"]		= "No es posible agregar el usuario.  Por favor vuelva atrás y comienze de nuevo.";
$pgv_lang["password_mismatch"]		= "Las contraseñas no coinciden.";
$pgv_lang["enter_username"]			= "Escriba un nombre de usuario.";
$pgv_lang["enter_fullname"]			= "Escriba su nombre completo.";
$pgv_lang["enter_password"]			= "Escriba una contraseña.";
$pgv_lang["confirm_password"]		= "Confirme la contraseña.";
$pgv_lang["update_user"]			= "Actualizar usuario";
$pgv_lang["save"]			        = "Guardar";
$pgv_lang["delete"]					= "Borrar";
$pgv_lang["edit"]					= "Editar";
$pgv_lang["full_name"]				= "Nombre Completo";
$pgv_lang["can_admin"]				= "Puede Admin.";
$pgv_lang["can_edit"]				= "Puede Editar";
$pgv_lang["confirm_user_delete"]	= "Seguro que quiere borrar el usuario";
$pgv_lang["create_user"]			= "Crear Usuario";
$pgv_lang["no_login"]				= "No es posible autentificar al usuario.";
$pgv_lang["import_gedcom"]			= "Importar este archivo GEDCOM";
$pgv_lang["duplicate_username"]		= "Nombre de usuario duplicado.  Ya existe un usuario con ese nombre.  Por favor regrese y cree un usuario con otro nombre.";
$pgv_lang["gedcomid"]				= "ID personal";
$pgv_lang["enter_gedcomid"]			= "Debe escribir una ID del GEDCOM.";
$pgv_lang["user_info"]				= "Mi información de Usuario";
$pgv_lang["rootid"]					= "Persona Inicial de Antepasados";
$pgv_lang["download_gedcom"]		= "Descargar GEDCOM";
$pgv_lang["upload_gedcom"]			= "Subir GEDCOM";
$pgv_lang["add_new_gedcom"]		    = "Crear un nuevo GEDCOM";
$pgv_lang["gedcom_file"]			= "Archivo GEDCOM:";
$pgv_lang["enter_filename"]		    = "Debe introducir el nombre del GEDCOM.";
$pgv_lang["file_exists"]		    = "Ya existe un GEDCOM con ese nombre. Por favor escoja un nombre diferente o borre el archivo existente.";
$pgv_lang["new_gedcom_title"]		= "Genealogía desde [#GEDCOMFILE#]";
$pgv_lang["upload_error"]			= "Hubo un error subiendo su archivo GEDCOM.";
$pgv_lang["upload_help"]			= "Seleccione un archivo GEDCOM en su ordenador para subir al servidor.  Este reemplazará su actual archivo GEDCOM y automaticamente le conducirá a reconstruir los índices.";
$pgv_lang["add_gedcom_instructions"]= "Introduzca un nombre para este nuevo GEDCOM. El archivo GEDCOM nuevo será creado en el directorio Index: ";
$pgv_lang["file_success"]			= "Archivo correctamente subido";
$pgv_lang["file_too_big"]			= "El archivo a subir excede el tamaño permitido";
$pgv_lang["file_partial"]			= "Archivo parcialmente subido, por favor inténtelo de nuevo";
$pgv_lang["file_missing"]			= "Archivo no recibido. Envíelo de nuevo.";
$pgv_lang["manage_gedcoms"]			= "Administrar GEDCOMs";
$pgv_lang["research_log"]			= "Panel de Investigación";
$pgv_lang["administration"]			= "Administración";
$pgv_lang["ansi_to_utf8"]	    	= "¿Quiere convertir este GEDCOM desde ANSI (ISO-8859-1) a UTF-8?";
$pgv_lang["utf8_to_ansi"]		    = "¿Quiere convertir este GEDCOM desde UTF-8 a ANSI (ISO-8859-1)?";
$pgv_lang["user_manual"]		    = "Manual de Usuario del PhpGedView";
$pgv_lang["upgrade"]			    = "Actualizar PhpGedView/Panel de Investigación";
$pgv_lang["view_logs"]			    = "Ver archivos log";
$pgv_lang["step1"]			 	    = "Paso 1 de 4:";
$pgv_lang["step2"]				    = "Paso 2 of 4:";
$pgv_lang["step3"]				    = "Paso 3 of 4:";
$pgv_lang["step4"]				    = "Paso 4 of 4:";
$pgv_lang["step5"]				    = "Paso 5 of 5:";


//-- Relationship chart messages
$pgv_lang["relationship_chart"]		= "Parentesco";
$pgv_lang["person1"]				= "Persona 1";
$pgv_lang["person2"]				= "Persona 2";
$pgv_lang["no_link_found"]			= "No se encontró ninguna relacción entre estas dos personas.";
$pgv_lang["sibling"]				= "Hermano";
$pgv_lang["follow_spouse"]			= "Verificar parentesco por matrimonio.";
$pgv_lang["timeout_error"]			= "Fuera de tiempo antes de que se encontrase un parentesco.";
$pgv_lang["son"]					= "Hijo";
$pgv_lang["daughter"]				= "Hija";
$pgv_lang["brother"]				= "Hermano";
$pgv_lang["sister"]					= "Hermana";
$pgv_lang["relationship_to_me"]		= "Parentesco conmigo";
$pgv_lang["next_path"]		    	= "Buscar otro camino";
$pgv_lang["show_path"]			    = "Ver camino";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]		    = "¿Está seguro que desea eliminar este acontecimiento?";
$pgv_lang["access_denied"]		    = "<b>Acceso denegado</b><br />No tiene permiso para acceder a este recurso.";
$pgv_lang["gedrec_deleted"]		    = "Registro GEDCOM correctamente eliminado.";
$pgv_lang["gedcom_deleted"]		    = "GEDCOM [#GED#] eliminado correctamente.";
$pgv_lang["changes_exist"]		    = "Los cambios han sido aplicados a este GEDCOM.";
$pgv_lang["accept_changes"]		    = "Aceptar / Rechazar Cambios";
$pgv_lang["show_changes"]		    = "Este registro ha sido actualizado.  Pulse aquí para ver los cambios.";
$pgv_lang["review_changes"]		    = "Reseña de los cambios en el GEDCOM";
$pgv_lang["undo_successful"]	    = "Se ha deshecho correctamente";
$pgv_lang["undo"]				    = "Deshacer";
$pgv_lang["view_change_diff"]	    = "Ver modificaciones";
$pgv_lang["changes_occurred"]	    = "Esta persona tuvo los cambios siguientes:";
$pgv_lang["find_place"]			    = "Encontrar Lugar";
$pgv_lang["close_window"]		    = "Cerrar Ventana";
$pgv_lang["close_window_without_refresh"] = "Cerrar ventana sin actualizar";
$pgv_lang["place_contains"]		    = "Los Lugares tienen:";
$pgv_lang["accept_gedcom"]		    = "Para rechazar un cambio, seleccione el enlace Deshacer.  Para aceptar todos los cambios de un Geedcom, reimportar el archivo de nuevo.";
$pgv_lang["ged_import"]			    = "Importar GEDCOM";
$pgv_lang["now_import"]			    = "Ahora debería importar los registros GEDCOM al PhpGedView seleccionando en el enlace de importación siguiente.";
$pgv_lang["add_fact"]			    = "Añadir nuevo acontecimiento";
$pgv_lang["add"]				    = "Añadir";
$pgv_lang["custom_event"]		    = "Denominar Evento";
$pgv_lang["update_successful"]	    = "Actualizado correctamente";
$pgv_lang["add_child"]			    = "Agregar hijo";
$pgv_lang["add_child_to_family"]    = "Agregar un hijo a esta familia";
$pgv_lang["must_provide"]		    = "Debe suministrar un ";
$pgv_lang["delete_person"]		    = "Borrar esta persona";
$pgv_lang["confirm_delete_person"]	= "Seguro que quiere borrar esta persona del archivo GEDCOM?";
$pgv_lang["find_media"]			    = "Buscar Multimedia";
$pgv_lang["set_link"]			    = "Crear Enlace";
$pgv_lang["add_source"]			    = "Añadir Fuente al Acontecimiento";
$pgv_lang["add_note"]			    = "Añadir Nota al Acontecimiento";
$pgv_lang["delete_source"]		    = "Borrar esta Fuente";
$pgv_lang["confirm_delete_source"]	= "Seguro que quiere borrar esta Fuente del archivo GEDCOM?";
$pgv_lang["add_husb"]		     	= "Agregar esposo";
$pgv_lang["add_husb_to_family"]	    = "Agregar esposo a esta familia";
$pgv_lang["add_wife"]		    	= "Agregar esposa";
$pgv_lang["add_wife_to_family"] 	= "Agregar esposa a esta familia";
$pgv_lang["find_family"]	    	= "Buscar Familia";
$pgv_lang["add_new_wife"]		    = "Agregar una nueva esposa";
$pgv_lang["add_new_husb"]	    	= "Agregar un nuevo esposo";
$pgv_lang["edit_name"]		    	= "Editar Nombre";
$pgv_lang["delete_name"]	    	= "Borrar Nombre";
$pgv_lang["no_temple"]			    = "No Temple - Living Ordinance";
$pgv_lang["add_unlinked_person"]    = "Agregar una nueva persona sin enlazar";

//-- calendar.php messages
$pgv_lang["on_this_day"]	   	  = "En un día como este...";
$pgv_lang["in_this_month"]		  = "En un mes como este...";
$pgv_lang["year_anniversary"]	  = "año del aniversario";
$pgv_lang["day"]				  = "Día:";
$pgv_lang["month"]			      = "Mes:";
$pgv_lang["anniversary_calendar"] = "Aniversarios";
$pgv_lang["sunday"]		     	  = "Domingo";
$pgv_lang["monday"]			      = "Lunes";
$pgv_lang["tuesday"]			  = "Martes";
$pgv_lang["wednesday"]			  = "Miércoles";
$pgv_lang["thursday"]			  = "Jueves";
$pgv_lang["friday"]			      = "Viernes";
$pgv_lang["saturday"]			  = "Sábado";
$pgv_lang["viewday"]			  = "Ver día";
$pgv_lang["viewmonth"]			  = "Ver mes";
$pgv_lang["all_people"]			  = "Todas las personas";
$pgv_lang["living_only"]		  = "Personas vivas";
$pgv_lang["recent_events"]		  = "Eventos recientes (&lt; 100 años)";

//-- upload media messages
$pgv_lang["upload_media"]		= "Subir archivos multimedia";
$pgv_lang["media_file"]			= "Archivos Multimedia";
$pgv_lang["thumbnail"]			= "Miniaturas";
$pgv_lang["upload_successful"]	= "Proceso completo";

//-- user self registration module
$pgv_lang["requestpassword"]	= "Solicitar una nueva contrasea ";
$pgv_lang["requestaccount"]		= "Solicitar una nueva cuenta de usuario";
$pgv_lang["register_info_01"]	= "La totalidad de la información que está disponible al público en este sitio está sujeta a los términos de la ley aplicable sobre protección de privacidad. La mayoría de las personas no quiere que sus datos personales sean públicos en Internet. Podría ser utilizado para spam o suplantación de identidad.<br /><br />Para poder acceder a los datos privados debe abrir una cuenta en este sitio. Para obtener la cuenta debe registrarse y suministrar la información requerida. Después de que el administrador compruebe su solicitud y la apruebe, podrá entrar como usuario registrado.<br /><br />Si el parentesco privado está activado solamente podrá acceder a la información privada de sus familiares cercanos cuando entre en el sitio como usuario registrado. El administrador puede darle permiso de edición en la base de datos, pudiendo así modificar o añadir información.<br /><br />NOTA: Solamente podrá acceder a información privada si puede demostrar que es un familiar cercano de una persona de la base de datos.<br /><br />Si no es un familiar con toda probabilidad no se le asigne una cuenta, evitándole así el transtorno.<br />Si desea hacer alguna aclaración, utilize el enlace infererior para contactar con el webmaster.<br /><br />";
$pgv_lang["register_info_02"]	= "";
$pgv_lang["pls_note01"]			= "Tenga en cuenta que el sistema distingue mayúsculas de minúsculas.";
$pgv_lang["min6chars"]			= "La contrasea debe tener al menos 6 caracteres";
$pgv_lang["pls_note02"]			= "Nota: Las contraseñas deberín contener solamente letras y números. Incluir otros caracteres en su contraseña puede impedir el acceso desde algunos sistemas.";
$pgv_lang["pls_note03"]			= "Esta cuenta de correo se verificará antes que su cuenta sea aprobada. No se mostrará en el sitio. Le enviaremos un mensaje a esta dirección de correo con sus datos de registro.";
$pgv_lang["emailadress"]		= "Correo Eleectrónico:";
$pgv_lang["pls_note04"]			= "Los campos marcados con * son obligatorios.";
$pgv_lang["pls_note05"]			= "Una vez complete el formulario de esta página y verifiquemos sus respuestas, le enviaremos un mensaje de confirmación a la dirección de correo electrónico que introduzca. Utilizando el correo de confirmación activará su cuenta; si no lo hace antes de siete días, ésta se eliminará (si esto sucede puede iniciar el proceso de registro de nuevo). Para utilizar este sitio necesitará un nombre de usuario y una contraseña, you will need to know your login name and password. You must specify an existing, valid email address on this page in order to receive the account confirmation email.<br /><br />If you encounter an issue in registering an account on this website, please submit a Support Request to the webmaster.";

$pgv_lang["mail01_line01"]		= "Hola #user_fullname# ...";
$pgv_lang["mail01_line02"]		= "Se ha hecho una petición a ( #SERVER_NAME# ) para acceder con su dirección de correo ( #user_email# ).";
$pgv_lang["mail01_line03"]		= "Fueron utilizados los siguientes datos.";
$pgv_lang["mail01_line04"]		= "Selecione el enlace inferior y rellene los datos requeridos para verificar su Cuenta y su Dirección de correo .";
$pgv_lang["mail01_line05"]		= "Si no est conforme con los datos puede eliminar este mensaje.";
$pgv_lang["mail01_line06"]		= "No recibirá más mensajes desde este sistema, y se eliminará la cuenta sino la verifica antes de siete días.";

$pgv_lang["mail01_subject"]		= "Su registro en #SERVER_NAME#";

$pgv_lang["mail02_line01"]		= "Hola Administrador ...";
$pgv_lang["mail02_line02"]		= "Nuevo usuario registrado en ( #SERVER_NAME# ).";
$pgv_lang["mail02_line03"]		= "Se le ha enviado un correo con los datos necesarios para verificar su cuenta.";
$pgv_lang["mail02_line04"]		= "Tan pronto como el usuario haga esta verificación será informado por correo, entonces podrá autorizar a este usuario para entrar en el sitio.";

$pgv_lang["mail02_subject"]		= "Nuevo registro en #SERVER_NAME#";

$pgv_lang["hashcode"]			= "Código de verificación:";
$pgv_lang["thankyou"]			= "Hola #user_fullname# ...<br />Gracias por registrarse";
$pgv_lang["pls_note06"]			= "Ahora recibirá un correo para  confirmar su dirección de correo:( #user_email# ). Utilizando el correo de confirmación, activará su cuenta; sino la activa antes de siete dís, ésta se borrará (en ese momento podrí registrar su cuenta de nuevo). Para acceder a este sitio, necesitará saber su nombre de usuario y su contraseña.";

$pgv_lang["registernew"]		= "Nueva confirmación de Cuenta";
$pgv_lang["user_verify"]		= "Verificación de usuario";
$pgv_lang["send"]			    = "Enviar";

$pgv_lang["pls_note07"]			= "Por favor, escriba su nombre de usuario, su contraseña, y el código de verificación que ha recibido por correo desde este sistema para verificar su petición de una cuenta.";
$pgv_lang["pls_note08"]			= "Se han chequeado los datos del usuario #user_name#.";

$pgv_lang["mail03_line01"]		= "Hola Administrador ...";
$pgv_lang["mail03_line02"]		= "#newuser[username]# ( #newuser[fullname]# ) ha verificado los datos de registro.";
$pgv_lang["mail03_line03"]		= "Por favor, seleccione el enlace siguiente para editar los datos del usuario, y darle el permiso para acceder a su sitio web.";

$pgv_lang["mail03_subject"]		= "Nueva verificación en #SERVER_NAME#";

$pgv_lang["pls_note09"]			= "Ha sido identificado como un usuario registrado.";
$pgv_lang["pls_note10"]			= "El Administrador ha sido informado.<br />Tan pronto como le dé el permiso para acceder, puede entrar con su nombre de usuario y su contraseña.";
$pgv_lang["data_incorrect"]		= "Los datos no son correctos!<br />Por favor, inténtelo de nuevo!";
$pgv_lang["user_not_found"]		= "No se pudo verificar la información que ha enviado.  Por favor, regrese e inténtelo de nuevo.";

$pgv_lang["lost_pw_reset"]		= "Solicitud de nueva contraseña por pérdida";

$pgv_lang["pls_note11"]			= "Para obtener su contraseña, enví el nombre de usuario y la dirección de correo de su cuenta. <br /><br />Le enviaremos una URL especial por email, con una confirmación para su cuenta. Visitando la direcció URL suministrada, podrá cambiar su contraseña y acceder a este sitio. Por razones de seguridad, no suministre esta confirmación a nadie, administradores de este sitio incluidos (nosotros no se lo pedimos).<br /><br />Si necesita asistencia del administrador del sitio, por favor, contacte con él directamente.";
$pgv_lang["enter_email"]		= "Escriba una dirección de correo electrónico.";

$pgv_lang["mail04_line01"]		= "Hola #user_fullname# ...";
$pgv_lang["mail04_line02"]		= "Ha sido solicitada una nueva contraseña para su nombre de usuario!";
$pgv_lang["mail04_line03"]		= "Recomendación:";
$pgv_lang["mail04_line04"]		= "Ahora, por favor, seleccione el siguiente enlace, entre con la nueva contraseña y cámbiela para preservar la seguridad de sus datos.";

$pgv_lang["mail04_subject"]		= "Solicitud de datos en #SERVER_NAME#";

$pgv_lang["pwreqinfo"]			= "Hola...<br /><br />Se ha enviado un correo a la dirección (#user[email]#) incluyendo la nueva contraseña.<br /><br />Por favor, revise su cuenta de correo porque el mensaje debería de llegarle en los próximos minutos.<br /><br />Recommendación:<br /><br />Después de recibir el mensaje, debería acceder a este sitio con su nueva contraseña y cambiarla preservar la seguridad de sus datos.";

$pgv_lang["editowndata"]		= "Mi Cuenta";
$pgv_lang["savedata"]			= "Guardar cambios";
$pgv_lang["datachanged"]		= "Los datos de usuario han sido modificados!";
$pgv_lang["datachanged_name"]	= "Necesita autentificarse con su nuevo nombre de usuario.";
$pgv_lang["myuserdata"]			= "Mi Cuenta";
$pgv_lang["verified"]			= "Usuario autoverificado:";
$pgv_lang["verified_by_admin"]	= "Usuario aprobado Admin.:";
$pgv_lang["user_theme"]			= "Mi Tema";
$pgv_lang["mygedview"]			= "MiGedView";
$pgv_lang["passwordlength"]		= "La contraseña debe tener al menos 6 caracteres .";
$pgv_lang["admin_approved"]		= "Su cuenta en #SERVER_NAME# fue aprobada";
$pgv_lang["you_may_login"]		= " por el administrador del sitio. Ahora puede entrar en el sitio, seleccionando el enlace siguiente:";


//-- mygedview page
$pgv_lang["welcome"]			= "Bienvenido";
$pgv_lang["upcoming_events"]	= "Próximos Acontecimientos";
$pgv_lang["chat"]			    = "Chat";
$pgv_lang["users_logged_in"]	= "Usuarios registrados";
$pgv_lang["message"]			= "Enviar Mensaje";
$pgv_lang["my_messages"]		= "Mensajes";
$pgv_lang["date_created"]		= "Enviado el:";
$pgv_lang["message_from"]		= "Email:";
$pgv_lang["message_from_name"]	= "De:";
$pgv_lang["message_to"]			= "Para:";
$pgv_lang["message_subject"]	= "Asunto:";
$pgv_lang["message_body"]		= "Mensaje:";
$pgv_lang["no_to_user"]			= "No es posible enviar el mensaje.  Debe de especificar el destinatario.";
$pgv_lang["provide_email"]		= "Recuerde especificar su dirección de correo para que nosotros podamos responder a su mensaje.  Su dirección de correo electrónico no será utilizada en ningún caso para nada diferente que no sea responder a su mensaje.";
$pgv_lang["reply"]			    = "Responder";
$pgv_lang["message_deleted"]	= "Mensaje eliminado";
$pgv_lang["message_sent"]		= "Mensaje enviado";
$pgv_lang["reset"]			    = "Restaurar";
$pgv_lang["site_default"]		= "Sitio predeterminado";
$pgv_lang["mygedview_desc"]		= "Su página MiGedView le permite agregar Favoritos de sus personas preferidas, ver los próximos Acontecimientos , y contactar con otros Usuarios del sitio.";
$pgv_lang["no_messages"]		= "No tiene mensajes pendientes.";
$pgv_lang["clicking_ok"]		= "Seleccionando OK, se abrirá otra ventana donde contactar con #user[fullname]#";
$pgv_lang["my_favorites"]		= "Favoritos";
$pgv_lang["no_favorites"]		= "No tiene a nadie seleccionado en Favoritos.  Para agregar una persona a Favoritos, busque en la página de la persona que le gustaría agregar y seleccione \"Agregar a Favoritos\"  o bien utilice la caja del ID inferior para agregar una persona por su número de ID.";
$pgv_lang["add_to_my_favorites"]= "Agregar a Favoritos";
$pgv_lang["confirm_fav_remove"]	= "Seguro que quiere eliminar este enlace de sus favoritos?";
$pgv_lang["portal"]			    = "Portal";
$pgv_lang["invalid_email"]		= "Por favor introduzca una dirección de correo válida.";
$pgv_lang["enter_subject"]		= "Por favor introduzca el Asunto.";
$pgv_lang["enter_body"]			= "Por favor escriba el texto del mensaje antes de enviarlo.";
$pgv_lang["confirm_message_delete"]	= "Realmente desea eliminar definitivamente este mensaje?";
$pgv_lang["message_email1"]		= "Mensaje recibido de ";
$pgv_lang["message_email2"]		= "Mensaje enviado a un usuario:";
$pgv_lang["message_email3"]		= "Mensaje enviado a un administrador:";
$pgv_lang["viewing_url"]		= "Este mensaje fue enviado desde la página url: ";
$pgv_lang["messaging2_help"]	= "Cuando envíe este mensaje recibirá una copia del mismo en la dirección de correo suministrada.";
$pgv_lang["random_picture"]		= "Imagen al azar";
$pgv_lang["message_instructions"]	= "<b>AVISO:</b> La información privada de las personas vivas solamente se facilitará a familiares cercanos y amigos íntimos.  Se le solicitará su relacción de parentesco para poder recibir datos privados.  En ocasiones la información sobre personas ya fallecidas también puede ser privada.  Esto es así cuando no hay información suficiente para determinar con seguridad si estas persanas están vivas o no, por otra parte probablemente no dispongamos de más información sobre ellas.<br /><br />Antes de hacer una solicitud, revise todos los datos de la persona, fechas, lugares y detalles personales para asegurarse de que efectivamente sea la persona de su interés.  Si está enviando modificaciones sobre los datos genealógicos, no se olvide de incluir las fuentes de donde obtuvo la información.<br /><br />";

//-- upgrade.php messages
$pgv_lang["upgrade_util"]		= "Actualización";
$pgv_lang["no_upgrade"]			= "No hay archivos para actualizar.";
$pgv_lang["use_version"]		= "Versión utilizada:";
$pgv_lang["current_version"]	= "Versión actual estable:";
$pgv_lang["upgrade_download"]	= "Descargar:";
$pgv_lang["upgrade_tar"]		= "TAR";
$pgv_lang["upgrade_zip"]		= "ZIP";
$pgv_lang["latest"]				= "Está corriendo la última versión del PhpGedView.";
$pgv_lang["location"]			= "Archivos de actualización: ";
$pgv_lang["include"]			= "Incluir:";
$pgv_lang["options"]			= "Opciones:";
$pgv_lang["inc_phpgedview"]		= " PhpGedView";
$pgv_lang["inc_languages"]		= " Idiomas";
$pgv_lang["inc_config"]			= " Configuración";
$pgv_lang["inc_researchlog"]	= " Panel de Investigación";
$pgv_lang["inc_index"]			= " Index";
$pgv_lang["inc_themes"]			= " Temas";
$pgv_lang["inc_docs"]			= " Manuales";
$pgv_lang["inc_privacy"]		= " Privacidad";
$pgv_lang["inc_backup"]			= " Crear backup";
$pgv_lang["upgrade_help"]		= " Ayuda";
$pgv_lang["cannot_read"]		= "No puede leer el archivo:";
$pgv_lang["not_configured"]		= "No tiene el PhpGedView configurado todavía.";
$pgv_lang["location_upgrade"]	= "Introduzca la localización de los archivos de actualización.";
$pgv_lang["new_variable"]		= "Encontrada una nueva variable: ";
$pgv_lang["config_open_error"] 	= "Ocurrió un error abriendo el archivo configuración.";
$pgv_lang["config_write_error"] = "Error!!! No se puede escribir en el archivo de configuración.";
$pgv_lang["config_update_ok"]	= "Archivo de configuración actualizado correctamente.";
$pgv_lang["config_uptodate"]	= "Su archivo de configuración está up-to-date.";
$pgv_lang["processing"]			= "Procesando...";
$pgv_lang["privacy_open_error"] = "Ocurrió un error abriendo el archivo [#PRIVACY_MODULE#].";
$pgv_lang["privacy_write_error"]= "ERROR!!! No se puede escribir en el archivo [#PRIVACY_MODULE#].<br />Compruebe los permisos de escritura para este archivo.<br />Los permisos podrán ser restituídos una vez se haya sobreescrito el archivo.";
$pgv_lang["privacy_update_ok"]	= "Archivo de Privacidad: [#PRIVACY_MODULE#] actualizado correctamente.";
$pgv_lang["privacy_uptodate"]	= "Su archivo [#PRIVACY_MODULE#] está up-to-date.";
$pgv_lang["heading_privacy"]	= "Privacidad:";
$pgv_lang["heading_phpgedview"]	= "PhpGedView:";
$pgv_lang["heading_image"]		= "Imágenes:";
$pgv_lang["heading_index"] 		= "Index:";
$pgv_lang["heading_language"]	= "Idiomas:";
$pgv_lang["heading_theme"]		= "Temas:";
$pgv_lang["heading_docs"]		= "Manuales:";
$pgv_lang["heading_researchlog"]= "Panel de Investigación:";
$pgv_lang["heading_researchloglang"]	= "Idiomas del Panel:";
$pgv_lang["copied_success"]		= "copiado correctamente.";
$pgv_lang["backup_copied_success"]		= "archivo backup creado correctamente.";
$pgv_lang["folder_created"]		= "Creada carpeta";
$pgv_lang["process_error"]		= "Hay un problema al procesar la página. No se puede determinar la nueva versión.";

if (file_exists( "languages/lang.es.extra.php")) require  "languages/lang.es.extra.php";

?>

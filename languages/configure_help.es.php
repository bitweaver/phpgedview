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

if (preg_match("/configure_help\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "Usted no puede acceder a este archivo de idioma directamente.";
	exit;
}
//-- CONFIGURE FILE MESSAGES
$pgv_lang["configure"]			    = "Configurar PhpGedView";
$pgv_lang["default_user"]		    = "Cree el usuario de administración por defecto.";
$pgv_lang["about_user"]			    = "Primero debe crear el usuario de administración principal.  Este usuario tendrá privilegios para actualizar los archivos de configuración, ver datos privados, y crear otros usuarios.";
$pgv_lang["fullname"]			    = "Nombre completo:";
$pgv_lang["confirm"]			    = "Confirme Contraseña:";
$pgv_lang["can_admin"]			    = "Administrar:";
$pgv_lang["can_edit"]			    = "Editar:";
$pgv_lang["add_user"]			    = "Agregar nuevo Usuario";
$pgv_lang["current_users"]		    = "Lista de Usuarios";
$pgv_lang["leave_blank"]		    = "Deje la contraseña en blanco si quiere conservar la contraseña actual.";
$pgv_lang["other_theme"]		    = "Otro, escriba por favor";
$pgv_lang["performing_update"]	    = "Actualizando.";
$pgv_lang["config_file_read"]	    = "Fichero Config leído.";
$pgv_lang["does_not_exist"]		    = "no existe";
$pgv_lang["config_write_error"]	    = "Error escribiendo el fichero de configuración.  Revise los permisos y vuelva a empezar.";
$pgv_lang["db_setup_bad"]		    = "Su configuración actual de la base de datos está mal.  Por favor revise su conexión a la base de datos y configure de nuevo.";
$pgv_lang["click_here_to_continue"]	= "Pulse Aquí para continuar.";
$pgv_lang["config_help"]		    = "Ayuda de la Configuración";
$pgv_lang["index"]				    = "Archivos Index";
$pgv_lang["mysql"]				    = "MySQL";
$pgv_lang["admin_gedcoms"]		    = "Pulse aquí para administrar los archivos GEDCOM.";
$pgv_lang["current_gedcoms"]	    = "GEDCOMs en uso";
$pgv_lang["ged_download"]		    = "Descargar";
$pgv_lang["ged_gedcom"]			    = "Archivo";
$pgv_lang["ged_title"]			    = "Título";
$pgv_lang["ged_config"]			    = "Configuración";
$pgv_lang["show_phpinfo"]		    = "Ver Información PHP";
$pgv_lang["confirm_gedcom_delete"]	= "Está seguro de querer eliminar este archivo GEDCOM";
$pgv_lang["gregorian"]			    = "Gregoriano";
$pgv_lang["julian"]				    = "Juliano";
$pgv_lang["config_french"]				    = "Francés";
$pgv_lang["jewish"]				    = "Judío";
$pgv_lang["config_hebrew"]				    = "Hebreo";
$pgv_lang["jewish_and_gregorian"]	= "Judío y Gregoriano";
$pgv_lang["hebrew_and_gregorian"]	= "Hebreo y Gregoriano";
$pgv_lang["disabled"]			    = "Desactivado";
$pgv_lang["mouseover"]			    = "Puntero encima";
$pgv_lang["mousedown"]			    = "Puntero pulsado";
$pgv_lang["click"]			        = "Haciendo clic";
$pgv_lang["mailto"]			        = "Correo para";
$pgv_lang["messaging"]			    = "Mensajes privados";
$pgv_lang["messaging2"]			    = "Mensajes privados con Emails";
$pgv_lang["no_messaging"]		    = "Ningún método de contacto";
$pgv_lang["no_logs"]			    = "Registro de usuarios desactivado";
$pgv_lang["daily"]			        = "Diariamente";
$pgv_lang["weekly"]			        = "Semanalmente";
$pgv_lang["monthly"]			    = "Mensualmente";
$pgv_lang["yearly"]			        = "Anual";
$pgv_lang["PGV_DATABASE"]           = "Sistema de Archivo en PhpGedView:";
$pgv_lang["PGV_DATABASE_help"]      = "Esto le dice al PhpGedView que tipo de sistema de almacenamiento de datos quiere utilizar para importar el archivo GEDCOM. Seleccione 'índices' para utilizar archivos índice guardados en el directorio index, o seleccione 'MySQL' para utilizar una base de datos MySQL.  Esto cambia la variable \$PGV_DATABASE en el archivo config.php.";
$pgv_lang["DBHOST"]                 = "Host MySQL:";
$pgv_lang["DBHOST_help"]            = "La DNS o dirección IP del servidor de su base de datos MySQL.  Esto cambia la variable \$DBHOST en el archivo config.php.";
$pgv_lang["DBUSER"]                 = "Nombre de usuario MySQL:";
$pgv_lang["DBUSER_help"]            = "El nombre de usuario de la Base de Datos MySQL se requiere para conectar con su actual base de datos.  Esto cambia la variable \$DBUSER en el archivo config.php.";
$pgv_lang["DBPASS"]                 = "Contraseña MySQL:";
$pgv_lang["DBPASS_help"]            = "La contraseña de la base de datos MySQL para el usuario que ha escrito en el campo de Nombre de Usuario.  Esto cambia la variable \$DBPASS en el archivo config.php.";
$pgv_lang["DBNAME"]                 = "Nombre Base de Datos:";
$pgv_lang["DBNAME_help"]            = "La Base de Datos en el servidor MySQL que quiere que PhpGedView utilice.  El Nombre de Usuario que escribió en el campo de usuario debe tener privilegios para poder crear, insertar, actualizar, borrar, y seleccionar en esta base de datos.  Esto cambia la variable \$DBNAME en el archivo config.php.";
$pgv_lang["TBLPREFIX"]              = "Prefijo de las Tablas:";
$pgv_lang["TBLPREFIX_help"]         = "Un prefijo para añadir a las tablas MySQL creadas por PhpGedView.  Cambiando este valor puede gestionar múltiples sitios PhpGedView, y utilizar la misma base de datos pero con diferentes tablas.  Esto cambia la variable \$TBLPREFIX en el archivo config.php.";
$pgv_lang["DEFAULT_GEDCOM"]         = "Inicio";
$pgv_lang["DEFAULT_GEDCOM_help"]    = "La versión MySQL de PhpGedView le permite trabajar con múltiples bases de datos GEDCOM en un único PhpGedView.  Use esta variable para fijar el archivo GEDCOM por defecto para todos los usuarios, cuando acceden a su web por primera vez.  Un valor en blanco por defecto seleccionará el archivo GEDCOM que haya sido importado primero.  Si permite que los usuarios puedan cambiar el GEDCOM, aparecerá un enlace en cada página que les permitirá elegir el GEDCOM que deseen utilizar.  Esto cambia la variable \$DEFAULT_GEDCOM en el archivo config.php.";
$pgv_lang["ALLOW_CHANGE_GEDCOM"]    = "Permitir a los visitantes elegir GEDCOMs:";
$pgv_lang["ALLOW_CHANGE_GEDCOM_help"] = "Fijando este valor en 'Sí' permite a los visitantes de su sitio la opción de elegir los GEDCOMs si tiene un setup con un entorno de múltiples GEDCOMs.  Esto cambia la variable \$ALLOW_CHANGE_GEDCOM en el archivo config.php.";
$pgv_lang["GEDCOM"]                   = "Ruta del GEDCOM:";
$pgv_lang["gedcom_path_help"]         = "Primero suba su archivo GEDCOM a una loaclización accesible por php en su servidor.  Luego escriba la ruta al archivo aquí.  Esto cambia la variable \$GEDCOM en el archivo config.php.<br /><br />Ver el archivo <a href=\"readme.txt\">Readme.txt</a> para más ayuda.";
$pgv_lang["CHARACTER_SET"]            = "Codificación del juego de caracteres:";
$pgv_lang["CHARACTER_SET_help"]       = "Este es el juego de caracteres de su archivo GEDCOM.  UTF-8 está por defecto y debería trabajar con casi todos los sitios.  Si exporta su GEDCOM usando codificación ibm windows, luego debería poner WINDOWS aquí.<br />Esto cambia la variable \$CHARACTER_SET en el archivo config.php.<br /><br />NOTA: PHP NO soporta UNICODE (UTF-16) :-)";
$pgv_lang["LANGUAGE"]                 = "Idioma:";
$pgv_lang["LANGUAGE_help"]            = "Asigna el idioma por defecto del sitio.  Los usuarios tienen la posibilidad de sobreescribir este ajuste, utilizando las preferencias de su navegador o seleccionandolo al final de la página, si ENABLE_MULTI_LANGUAGE = true.<br />Esto cambia la variable \$LANGUAGE en el archivo config.php.";
$pgv_lang["ENABLE_MULTI_LANGUAGE"]    = "Permitir a los usuarios elegir idioma:";
$pgv_lang["ENABLE_MULTI_LANGUAGE_help"] = "Fijando en 'Sí' le da a los usuarios la opción de seleccionar un idioma diferente al por defecto, desde una lista desplegable en el pie de la página, y diferente tambien al de su navegador.<br />Esto cambia la variable \$ENABLE_MULTI_LANGUAGE en el archivo config.php.";
$pgv_lang["CALENDAR_FORMAT"]            = "Formato del Calendario:";
$pgv_lang["CALENDAR_FORMAT_help"]       = "Le permite especificar que tipo de Calendario quiere utilizar con este archivo GEDCOM. El Hebreo es el mismo que el Calendario Judío utilizando caracteres Hebreos.<br />Esto cambia la variable \$CALENDAR_FORMAT en el archivo config.php.";
$pgv_lang["DISPLAY_JEWISH_THOUSANDS"]   = "Ver Miles Judíos:";
$pgv_lang["DISPLAY_JEWISH_THOUSANDS_help"]  = "Muestra Alafim en el calendario Hebreo o Judí.<br />Esto cambia la variable \$DISPLAY_JEWISH_THOUSANDS en el archivo config.php.";
$pgv_lang["DISPLAY_JEWISH_GERESHAYIM"]		= "Ver Gershayim Hebreo:";
$pgv_lang["DISPLAY_JEWISH_GERESHAYIM_help"]	= "Muestra simple o doble acotación cuando veamos las fechas en Hebreo. Fijando este valor en 'Sí' una fecha de la forma February 8th 1969 se mostrará así  <span lang='he-IL' dir='rtl'>&#1499;'&#160;&#1513;&#1489;&#1496;&#160;&#1514;&#1513;&#1499;\"&#1496;</span>&lrm; en cambio si lo fijamos en 'No', lo veremos como sigue <span lang='he-IL' dir='rtl'>&#1499;&#160;&#1513;&#1489;&#1496;&#160;&#1514;&#1513;&#1499;&#1496;</span>&lrm;. Esto no afecta al ajuste del año jud%iacute;o si no son utilizadas las acotaciones en las fechas judías mostradas con caracteres latinos.<br />Nota: Este ajuste es similar al de las constantes del php 5.0 Calendar CAL_JEWISH_ADD_ALAFIM_GERESH y CAL_JEWISH_ADD_GERESHAYIM. This single setting effects both.<br />Esto cambia la variable  \$DISPLAY_JEWISH_GERESHAYIM en el archivo config.php.";
$pgv_lang["JEWISH_ASHKENAZ_PRONUNCIATION"]  = "Pronunciación Ashkenaz Judía:";
$pgv_lang["JEWISH_ASHKENAZ_PRONUNCIATION_help"] = "Utiliza pronunciaciones Ashkenazi Judías.<br />Si lo fijamos en Sí, los meses de Cheshvan y Teves serán tratados con la pronunciación Ashkenazi. Setting it to no will change the months to Hesvan and Tevet. <br />This only affects the Jewish setting. Using the Hebrew setting will use the Hebrew alphabet.<br />Esto cambia la variable  \$JEWISH_ASHKENAZ_PRONUNCIATION en el archivo config.php.";
$pgv_lang["DEFAULT_PEDIGREE_GENERATIONS"]       = "Número Generaciones de Antepasados:";
$pgv_lang["DEFAULT_PEDIGREE_GENERATIONS_help"]  = "Ajusta el número de generaciones que se mostrarán por defecto en el informe de Antepasados.<br />Esto cambia la variable \$DEFAULT_PEDIGREE_GENERATIONS en el archivo config.php.";
$pgv_lang["MAX_PEDIGREE_GENERATIONS"]           = "Máximo de Generaciones en Antepasados:";
$pgv_lang["MAX_PEDIGREE_GENERATIONS_help"]      = "Fija el número máximo de generaciones que se mostrarán en los informes de Antepasados.<br /><br />Esto cambia la variable \$MAX_PEDIGREE_GENERATIONS en el archivo config.php.";
$pgv_lang["MAX_DESCENDANCY_GENERATIONS"]        = "Máximo de Generaciones en Descendientes:";
$pgv_lang["MAX_DESCENDANCY_GENERATIONS_help"]   = "Ajusta el número de generaciones que se mostrarán por defecto en el informe de Descendientes.<br /><br />Esto cambia la variable \$MAX_DESCENDANCY_GENERATIONS en el archivo config.php.";
$pgv_lang["USE_RIN"]                            = "Utilizar el RIN# del ID del GEDCOM:";
$pgv_lang["USE_RIN_help"]                       = "Si elije Sí utilizará el número RIN del ID del GEDCOM cuando lo pida para las IDs de las Personas en el archivo de configuración, preferencias de usuario, e informes.  Esto es utilizado para programas de genealogía que no exportan GEDCOMs con IDs de personas pero siempre utilizan el mismo RIN.<br />Esto cambia la variable \$USE_RIN en el archivo config.php.";
$pgv_lang["PEDIGREE_ROOT_ID"]                   = "Persona Inicial en Antepasados y Descendientes:";
$pgv_lang["PEDIGREE_ROOT_ID_help"]              = "Fija el ID de la persona que se mostrará por defecto en los informes de Antepasados y Descendientes.<br />Esto cambia la variable \$PEDIGREE_ROOT_ID en el archivo config.php.";
$pgv_lang["GEDCOM_ID_PREFIX"]		            = "Prefijo ID del GEDCOM:";
$pgv_lang["GEDCOM_ID_PREFIX_help"]	            = "En Antepasados, Descendientes, Parentesco, y otros informes cuando a los usuarios se les requiere introducir un ID, si no añaden un prefijo al ID, éste se añade por defecto.<br /><br />Esto cambia la variable  \$GEDCOM_ID_PREFIX en el archivo config.php.";
$pgv_lang["PEDIGREE_FULL_DETAILS"]              = "Mostrar detalles de nacimiento y defunción en los informes de Antepasados y Descendientes:";
$pgv_lang["PEDIGREE_FULL_DETAILS_help"]         = "Le dice si debe mostrar o no los detalles de nacimiento o defunción de una persona por defecto.<br />Esto cambia la variable \$PEDIGREE_FULL_DETAILS en el archivo config.php.";
$pgv_lang["PEDIGREE_LAYOUT"]		            = "Formato de Antepasados:";
$pgv_lang["PEDIGREE_LAYOUT_help"]	            = "Esto le dice al programa en que formato mostrará el informe de Antepasados: Retrato o Apaisado.<br /><br />Esto cambia la variable \$PEDIGREE_LAYOUT en el archivo config.php.";
$pgv_lang["SHOW_EMPTY_BOXES"]                   = "Ver cajas vacías en Antepasados:";
$pgv_lang["SHOW_EMPTY_BOXES_help"]              = "Le dice si mostrar o no las cajas vacís en los informes de antepasados.<br />Esto cambia la variable \$SHOW_EMPTY_BOXES en el archivo config.php.";
$pgv_lang["ZOOM_BOXES"]			                = "Aumentar cajas en informes:";
$pgv_lang["ZOOM_BOXES_help"]		            = "Le permite a los usuarios aumentar el tamaño de las cajas en los informes y poder acceder así a más datos.  Fijar en \"Disabled\" para desactivar esta utilidad.  Fijar en  \"MouseOver\" para aumentar las cajas cuando los usuarios situen el cursor sobre el icono en la caja. Fijar en \"Click\" para aumentar las cajas cuando los usuarios hagan clic en el icono de la caja.<br /><br />Esto cambia la variable  \$ZOOM_BOXES en el archivo config.php.";
$pgv_lang["LINK_ICONS"]			                = "Ventana de enlaces en informes:";
$pgv_lang["LINK_ICONS_help"]		            = "Permite a los usuarios seleccionar enlaces a otros informes y familiares cercanos de la persona.  Fijar en  \"Disabled\" para desactivar esta utilidad.  Fijar en  \"MouseOver\" para mostrar la ventana de enlaces cuando los usuarios situen el cursor sobre el icono en la caja.  Fijar en \"Click\" para mostrar la ventana de enlaces cuando los usuarios hagan clic en el icono de la caja..<br /><br />Esto cambia la variable  \$LINK_ICONS en el archivo config.php.";
$pgv_lang["AUTHENTICATION_MODULE"]              = "Archivo Módulo de Auntentificación:";
$pgv_lang["AUTHENTICATION_MODULE_help"]         = "Archivo desde el cual se cargan las funciones de auntentificación.  Poniendo las funciones en este archivo, los usuarios pueden cambiar PhpGedView para utilizar un método diferente de autentificación y guardar los usuarios en una base de datos diferente.  Ojalá, los usuarios compartan sus módulos de autentificación con otros usuarios de PhpGedView.<br /><br />Esto cambia la variable \$AUTHENTICATION_MODULE en el archivo config.php.";
$pgv_lang["HIDE_LIVE_PEOPLE"]                   = "Ocultar datos de personas vivas:";
$pgv_lang["HIDE_LIVE_PEOPLE_help"]              = "Activando esta opción se le dice al PhpGedView que no muestre los detalles de las personas que están vivas.  Estas personas son definidas por no tener un evento hace más de $MAX_ALIVE_AGE años, y no tienen hijos hace más de $MAX_ALIVE_AGE años.<br />Esto cambia la variable \$HIDE_LIVE_PEOPLE en el archivo config.php.";
$pgv_lang["REQUIRE_AUTHENTICATION"]	            = "Autentificación requerida:";
$pgv_lang["REQUIRE_AUTHENTICATION_help"]	    = "Activando esta opción  obligará a todos los visitantes a entrar como usuarios registrados para poder ver algún dato en el sitio.<br /><br />Esto cambia la variable  \$REQUIRE_AUTHENTICATION en el archivo config.php.";
$pgv_lang["CHECK_CHILD_DATES"]                  = "Comprobar fechas hijos:";
$pgv_lang["CHECK_CHILD_DATES_help"]             = "Comprueba las fechas de los hijos cuando una persona ya ha fallecido.  En sistemas anticuados y grandes GEDCOMs esto puede relantizar la respuesta de su sitio .<br />Esto cambia la variable \$CHECK_CHILD_DATES en el archivo config.php.";
$pgv_lang["MAX_ALIVE_AGE"]                      = "Edad a la cual presumiblemente una persona ha fallecido:";
$pgv_lang["MAX_ALIVE_AGE_help"]                 = "Edad máxima que una persona pueda tener o edad máxima de sus hijos para determinar si han fallecido o no.<br />Esto cambia la variable \$MAX_ALIVE_AGE en el archivo config.php.";
$pgv_lang["SHOW_GEDCOM_RECORD"]                 = "Permitir a los usuarios ver el archivo GEDCOM original:";
$pgv_lang["SHOW_GEDCOM_RECORD_help"]            = "Fijando esto en 'Sí' aparecerá un enlace en personas, fuentes y familias que permitirá a los usuarios abrir otra ventana con los datos GEDCOM de origen.<br />Esto cambia la variable \$SHOW_GEDCOM_RECORD en el archivo config.php.";
$pgv_lang["ALLOW_EDIT_GEDCOM"]                  = "Activar Edición en línea:";
$pgv_lang["ALLOW_EDIT_GEDCOM_help"]             = "Permite las ventajas de la edición en línea en este GEDCOM, de este modo los usuarios con este privilegio pueden actualizar el GEDCOM en línea.<br /><br />Esto cambia la variable \$ALLOW_EDIT_GEDCOM en el archivo config.php.";
$pgv_lang["INDEX_DIRECTORY"]                    = "Directorio de los archivos de índice:";
$pgv_lang["INDEX_DIRECTORY_help"]               = "La ruta a un directorio de lectura y escritura, donde PhpGedView guardará los archivos de índices (incluya el \"/\")<br />Esto cambia la variable \$INDEX_DIRECTORY en el archivo config.php.";
$pgv_lang["ALPHA_INDEX_LISTS"]                  = "Agrupar listas largas por primera letra:";
$pgv_lang["ALPHA_INDEX_LISTS_help"]             = "Para listas muy largas de personas y familias, fije este campo a true para compactar la lista en páginas ordenadas por la primera letra de su apellido. <br />Esto cambia la variable \$ALPHA_INDEX_LISTS en el archivo config.php.";
$pgv_lang["NAME_FROM_GEDCOM"]                   = "Mostrar el nombre desde el GEDCOM:";
$pgv_lang["NAME_FROM_GEDCOM_help"]              = "Por defecto PhpGedView usa el nombre almacenado en los ndices para mostar el nombre de una persona.  Con algunos formatos GEDCOM e idiomas el nombre almacenado en los índices no se muestra correctamente y la mejor forma de corregirlo es mostrarlo directamente desde el GEDCOM. Los nombres con apellidos españoles son un buen ejemplo de esto. Un nombre español está compuesto de dos apellidos: el primero paterno y el segundo materno, de forma general. Utilizando los índices para ordenar y mostrar, resultaría: Nombre Apellido de la Madre Apellido del Padre, lo cual es incorrecto. Tomando el nombre directamente desde el GEDCOM, éste se mostrar correctamente. El tener que localizar el nombre en el GEDCOM har que el programa funcione un poco ms lento.<br />Esto cambia la variable \$NAME_FROM_GEDCOM en el archivo config.php.";
$pgv_lang["SHOW_ID_NUMBERS"]                    = "Mostrar números de ID a continuación de los nombres:";
$pgv_lang["SHOW_ID_NUMBERS_help"]               = "Muestra los números de ID entre paréntesis despues del nombre de la persona en los informes.<br />Esto cambia la variable \$SHOW_ID_NUMBERS en el archivo config.php.";
$pgv_lang["SHOW_PEDIGREE_PLACES"]               = "Mostrar Lugares en las cajas de la persona:";
$pgv_lang["SHOW_PEDIGREE_PLACES_help"]          = "Muestra los lugares a continuación de las fechas de nacimiento y defuncin en los informes de antepasados y descendientes.<br />Esto cambia la variable \$SHOW_PEDIGREE_PLACES en el archivo config.php.";
$pgv_lang["MULTI_MEDIA"]                        = "Permitir características multimedia:";
$pgv_lang["MULTI_MEDIA_help"]                   = "GEDCOM 5.5 le permite enlazar fotos, videos, y otros objetos multimedia en su GEDCOM.  Si no incluye objetos multimedia en su GEDCOM, entonces puede desactivar estas características multimedia cambiando este valor a 'no'. <br />Vea la sección multimedia en el archivo <a href=\"readme.txt\">readme.txt</a> para más información acerca de como incluir media en su sitio.<br />Esto cambia la variable \$MULTI_MEDIA en el archivo config.php.";
$pgv_lang["MEDIA_DIRECTORY"]                    = "Directorio MultiMedia:";
$pgv_lang["MEDIA_DIRECTORY_help"]               = "La ruta a un Directorio de lectura donde PhpGedView buscará los archivos multimedia (incluya \"/\")<br />Esto cambia la variable \$MEDIA_DIRECTORY en el archivo config.php.";
$pgv_lang["MEDIA_DIRECTORY_LEVELS"]             = "Niveles Directorio Multi-Media Directory para Guardar:";
$pgv_lang["MEDIA_DIRECTORY_LEVELS_help"]        = "Un valor de 0 ignorará todos los directorios en la ruta de archivo del objeto de media.  Un valor de 1 utilizará el primer directorio conteniendo la imagen.  Incrementando el valor numérico se incrementa el número de directorios padre incluidos en la ruta.  <br />Por ejemplo: Si enlaza una imagen en su GEDCOM con una ruta como esta C:\\Documents and Settings\\User\\My Documents\\My Pictures\\Genealogy\\Surname Line\\grandpa.jpg luego un valor de 0 transladará esta ruta a ./media/grandpa.jpg.  Un valor de 1 la transladará a ./media/Surname Line/grandpa.jpg, etc.  La mayoría solo necesitará utilizar 0. Pero es posible que algunos objetos de media tendrán el mismo nombre y se sobreescribirían.  Esto le permite organizar mejor sus media y prevenir catástrofes con los nombres.<br />Esto cambia la variable \$MEDIA_DIRECTORY_LEVELS en el archivo config.php.";
$pgv_lang["SHOW_HIGHLIGHT_IMAGES"]              = "Mostrar miniaturas en las cajas de las personas:";
$pgv_lang["SHOW_HIGHLIGHT_IMAGES_help"]         = "Si tiene activada la multimedia en su sitio, entonces podrá hacer que PhpGedView muestre una imagen en miniatura al lado del nombre de la persona en informes y cajas.  Actualmente PhpGedView utiliza el primer objeto multimedia listado en el registro GEDCOM como miniatura.  Para personas con muchas imágenes, debería ordenar los objetos multimedia para que el que usted desee como miniatura aparezca primero.<br />Vea la sección multimedia en el archivo <a href=\"readme.txt\">readme.txt</a> para mas informacion sobre como incluir media en su sitio.<br />Esto cambia la variable \$SHOW_HIGHLIGHT_IMAGES en el archivo config.php.";
$pgv_lang["ENABLE_CLIPPINGS_CART"]              = "Activar Carrito Genealógico:";
$pgv_lang["ENABLE_CLIPPINGS_CART_help"]         = "El carrito permite a los visitantes de su sitio ir añadiendo la información de personas a un archivo GEDCOM que luego pueden descargar e importar a su programa de genealogía..<br />Esto cambia la variable \$ENABLE_CLIPPINGS_CART en el archivo config.php.";
$pgv_lang["HIDE_GEDCOM_ERRORS"]                 = "Ocultar errores GEDCOM:";
$pgv_lang["HIDE_GEDCOM_ERRORS_help"]            = "Fijando esto en 'Sí' no mostrará los mensajes de error producidos por PhpGedView cuando no comprende una etiqueta GEDCOM en su archivo.  PhpGedView hace lo posible por cumplir el estandard GEDCOM 5.5, pero muchos programas de genealogía tienen sus etiquetas propias.  Vea el archivo <a href=\"readme.txt\">readme.txt</a> para más información.<br />Esto cambia la variable \$HIDE_GEDCOM_ERRORS en el archivo config.php.";
$pgv_lang["WORD_WRAPPED_NOTES"]                 = "Añadir espacios donde las notas fueron comprimidas:";
$pgv_lang["WORD_WRAPPED_NOTES_help"]            = "Algunos programas de genealogía compactan las notas en los límites de las palabras y otros en cualquier parte. Esto puede producir que PhpGedView escriba las palabras seguidas.  Fijando esto a 'yes' añadirá un espacio entre palabras donde fueron compactadas en el GEDCOM.<br />Esto cambia la variable \$WORD_WRAPPED_NOTES en el archivo config.php.";
$pgv_lang["SHOW_CONTEXT_HELP"]		            = "Ver ? de ayuda en las páginas:";
$pgv_lang["SHOW_CONTEXT_HELP_help"]	            = "Esta opción activará enlaces de ayuda con signos de interrogación en las páginas, situados cerca de los enlaces de opciones de cada página les permiten a los usuarios obtener ayuda contextual sobre cada tema.<br /><br />Esto cambia la variable  \$SHOW_CONTEXT_HELP en el archivo config.php.";
$pgv_lang["HOME_SITE_URL"]                      = "URL principal de la web:";
$pgv_lang["HOME_SITE_URL_help"]                 = "Una URL incluida en la cabecera del tema genera un enlace a tu página principal.<br />Esto cambia la variable \$HOME_SITE_URL en el archivo config.php.";
$pgv_lang["HOME_SITE_TEXT"]                     = "Texto del enlace a principal:";
$pgv_lang["HOME_SITE_TEXT_help"]                = "El texto usado para generar el enlace a su página principal.<br />Esto cambia la variable \$HOME_SITE_TEXT en el archivo config.php.";
$pgv_lang["CONTACT_EMAIL"]                      = "Email contacto genealogía:";
$pgv_lang["CONTACT_EMAIL_help"]                 = "La dirección de email que los visitantes deberían de utilizar para consultas sobre los datos genealógicos del sitio.<br />Esto cambia la variable \$CONTACT_EMAIL en el archivo config.php.";
$pgv_lang["CONTACT_METHOD"]		                = "Método de Contacto:";
$pgv_lang["CONTACT_METHOD_help"]	            = "El método que los usuarios utilizarán para contactar en \"Contacto Usuarios\" sobre cuestiones genealógicas con usted. La opción 'Correo Para' aadirá un enlace con la dirección de correo sobre el que el visitante podrá hacer clic para enviarle un mensaje utilizando su cliente de correo.  La opción 'Mensajes Privados' utilizará el sistema de mensajes privados del PhpGedView y no se enviacute;an mensajes de correo.  La opción 'Mensajes privados con correo' es la utilizada por defecto en el PhpGedView, y aparte del mensaje privado enví una copia del mismo por correo electrónico. Escogiendo la opción 'Ningún método de contacto' los visitantes no encontrarán ninguna opción para contactar con usted.<br /><br />Esto cambia la variable  \$CONTACT_METHOD en el archivo config.php.";
$pgv_lang["WEBMASTER_EMAIL"]                    = "Email del Webmaster:";
$pgv_lang["WEBMASTER_EMAIL_help"]               = "La dirección de email que los visitantes deberían de utilizar para consultas técnicas o errores en su sitio.<br />Esto cambia la variable \$WEBMASTER_EMAIL en el archivo config.php.";
$pgv_lang["SUPPORT_METHOD"]		                = "Método de Soporte:";
$pgv_lang["SUPPORT_METHOD_help"]	            = "El método que los usuarios utilizarán para contactar en \"Soporte Usuarios\" sobre cuestiones genealógicas con usted. La opción 'Correo Para' aadirá un enlace con la dirección de correo sobre el que el visitante podrá hacer clic para enviarle un mensaje utilizando su cliente de correo.  La opción 'Mensajes Privados' utilizará el sistema de mensajes privados del PhpGedView y no se enviacute;an mensajes de correo.  La opción 'Mensajes privados con correo' es la utilizada por defecto en el PhpGedView, y aparte del mensaje privado enví una copia del mismo por correo electrónico. Escogiendo la opción 'Ningún método de contacto' los visitantes no encontrarán ninguna opción para contactar con usted.<br /><br />Esto cambia la variable  \$SUPPORT_METHOD en el archivo config.php.";
$pgv_lang["FAVICON"]                            = "Icono para Favoritos:";
$pgv_lang["FAVICON_help"]                       = "Cambie esto para poner el icono que quiere que aparezca en Favoritos, cuando un visitante agrega el enlace de su sitio.<br />Esto cambia la variable \$FAVICON en el archivo config.php.";
$pgv_lang["THEME_DIR"]                          = "Directorio del Tema:";
$pgv_lang["THEME_DIR_help"]                     = "El directorio donde los archivos de su tema del PhpGedView son guardados.  Debe adaptar cualquiera de los temas estandard que vienen con PhpGedView para dar a su sitio un estilo único y diferente.  Vea la sección del archivo <a href=\"readme.txt\">readme.txt</a> para más información.<br />Esto cambia la variable \$THEME_DIR en el archivo config.php.";
$pgv_lang["TIME_LIMIT"]                         = "Tiempo Límite PHP:";
$pgv_lang["TIME_LIMIT_help"]                    = "El máximo de tiempo en segundos que debería estar permitido ejecutar el PhpGedView.  Por defecto es 1 minuto.  Dependiendo del tamaño de su archivo, necesitaría incrementar este tiempo cuando necesite construir los índices.  Fije este valor a 0, para permitir a PHP correr siempre.<br />PRECAUCIóN: Al poner a 0, o demasiado alto podra paralizar algunos sistemas operativos hasta que finalice el script.  Fijándolo en 0 puede ocurrir que nunca finalice, hasta que un administrador del servidor corte el proceso o restaure el servidor. Un informe grande de antepasados puede llevar mucho tiempo para elaborar, dejando este valor bajo nos aseguramos que alguien pueda hechar abajo nuestro servidor web por solicitar un informe de antepasados de 1000 generaciones.";
$pgv_lang["PGV_SESSION_SAVE_PATH"]              = "Ruta para Guardar Sesión:";
$pgv_lang["PGV_SESSION_SAVE_PATH_help"]         = "La ruta para guardar el archivo de sesión del PhpGedView. Algunos servidores no tienen PHP configurado correctamente y las sesiones no se mantienen entre cada solicitud de página.  Esto permite a los administradores del sitio evitarlo salvando el archivo en uno de sus directorios locales.  El directorio ./index/ es una buena opción si necesita cambiar esto.  Si lo deja vacío, por defecto utilizará la ruta configurada en el archivo php.ini.<br />Esto cambia la variable \$PGV_SESSION_SAVE_PATH en el archivo config.php.";
$pgv_lang["SERVER_URL"]			                = "URL del Servidor:";
$pgv_lang["SERVER_URL_help"]		            = "Si utiliza https o un puerto diferente al por defecto, necesitará escribir la URL para acceder a su servidor aquí.<br /><br />Esto cambia la variable  \$SERVER_URL en el archivo config.php.";
$pgv_lang["PGV_SESSION_TIME"]                   = "Duración de la Sesión";
$pgv_lang["PGV_SESSION_TIME_help"]              = "El tiempo en segundos que una sesión en PhpGedView permanece activa antes de pedir de nuevo la contrasea.  Por defecto es de 2 horas.<br />Esto cambia la variables \$PGV_SESSION_TIME en el archivo config.php.";
$pgv_lang["SHOW_STATS"]                         = "Mostrar Estadísticas de Ejecución:";
$pgv_lang["SHOW_STATS_help"]                    = "Muestra el tiempo que ha tardado en ejecutarse la página y las consultas a la base de datos en el pie de cada página.<br />Esto cambia la variable \$SHOW_STATS en el archivo config.php.";
$pgv_lang["USE_REGISTRATION_MODULE"]	        = "Permitir registrarse a los usuarios:";
$pgv_lang["USE_REGISTRATION_MODULE_help"]	    = "Les da la opción a los usuarios de registrarse automaticamente en el sitio.  El Administrador tendrá que aprobar el registro para que la cuenta se active.<br /><br />Esto cambia la variable  \$USE_REGISTRATION_MODULE en el archivo config.php.";
$pgv_lang["ALLOW_USER_THEMES"]		            = "Seleccionar tema por usuarios:";
$pgv_lang["ALLOW_USER_THEMES_help"]	            = "Les da a los usuarios la opción de seleccionar su propio Tema.<br /><br />Esto cambia la variable  \$ALLOW_USER_THEMES en el archivo config.php.";
$pgv_lang["CREATE_GENDEX"]		                = "Crear archivos Gendex:";
$pgv_lang["CREATE_GENDEX_help"]		            = "Permite al PhpGedView generar archivos Gendex cuando se importa un GEDCOM.  Los archivos Gendex son almacenados en el directorio index.<br /><br />Esto cambia la variable  \$CREATE_GENDEX en el archivo config.php.";
$pgv_lang["gedcom_title_help"]		            = "Título para el contenido de este GEDCOM.";
$pgv_lang["LOGFILE_CREATE"]		                = "Directorio para archivos Log:";
$pgv_lang["LOGFILE_CREATE_help"]	            = "Carpeta donde el programa debería guardar los archivos log.<br /><br />Esto cambia la variable  \$LOGFILE_CREATE en el archivo config.php.";

$pgv_lang["welcome"]            = "Bienvenido a su nueva web de PhpGedView. Desde el momento que está viendo esta página ha instalado satisfactoriamente PhpGedView en su servidor y está listo para configurarlo en la forma que desee.<br />";
$pgv_lang["welcome_help"]	    = "Esta ventana de ayuda le guiará a través de proceso de configuración.  Esta ventana muestra información de ayuda cuyo texto contiene los trminos que ha escrito. Puede cerrar esta ventana y abrirla de nuevo al hacer clic en el signo \"?\" .";
$pgv_lang["review_readme"]      = "Debería repasar primero el archivo <a href=\"readme.txt\" target=\"_blank\">readme.txt</a> antes de continuar configurando PhpGedView.<br /><br />Puede volver a esta configuración en cualquier momento señalando en su navegador al archivo configure.php.<br /><br />Puede conseguir ayuda para cada item seleccionando el símbolo \"?\" que aparece en cada celda.";
$pgv_lang["save_config"]        = "Salvar Configuración";
$pgv_lang["reset"]              = "Restaurar";
$pgv_lang["download_here"]	    = "Pulse aquí para descargar el archivo.";
$pgv_lang["download_gedconf"]	= "Descargar configuración del GEDCOM.";
$pgv_lang["not_writable"]	    = "Se ha detectado que su archivo de configuración no se puede reescribir por PHP.  Puede utilizar el botón de descarga para guardar el archivo en su ordenador, editarlo con su configuración y subirlo de nuevo.";
$pgv_lang["download_file"]	    = "Descargar archivo";
$pgv_lang["upload_to_index"]	= "Subir archivo al directorio index: ";

//-- edit privacy messages
$pgv_lang["edit_privacy"]			            = "Editar privacidad";
$pgv_lang["edit_privacy_title"]			        = "Editar Ajustes de Privacidad del GEDCOM";
$pgv_lang["PRIV_PUBLIC"]			            = "Todo el mundo";
$pgv_lang["PRIV_USER"]				            = "Sólo usuarios registrados";
$pgv_lang["PRIV_NONE"]				            = "Sólo administradores";
$pgv_lang["PRIV_HIDE"]				            = "Suprimir acontecimiento";
$pgv_lang["save_changed_settings"]		        = "Salvar cambios";
$pgv_lang["add_new_pp_setting"]			        = "Añadir privacidad a un ID";
$pgv_lang["add_new_up_setting"]			        = "Añadir privacidad a un Usuario";
$pgv_lang["add_new_gf_setting"]			        = "Añadir privacidad global a un Acontecimiento";
$pgv_lang["add_new_pf_setting"]			        = "Añadir nueva privacidad a un Acontecimiento";
$pgv_lang["add_new_pf_setting_indi"]		    = "Añadir privacidad a un Acontecimiento de una Persona ";
$pgv_lang["add_new_pf_setting_source"]		    = "Añadir privacidad a un Acontecimiento de una Fuente ";
$pgv_lang["privacy_indi_id"]			        = "Persona - ID";
$pgv_lang["privacy_source_id"]			        = "Fuente - ID";
$pgv_lang["privacy_indi_source"]		        = "Persona / Fuente";
$pgv_lang["privacy_indi"]			            = "Persona";
$pgv_lang["privacy_source"]			            = "Fuente";
$pgv_lang["file_read_error"]			        = "E R R O R !!! No se pudo leer el archivo de privacidad!";
$pgv_lang["general_settings"]			        = "Ajustes Generales de Privacidad";
$pgv_lang["person_privacy_settings"]		    = "Privacidad del ID";
$pgv_lang["edit_exist_person_privacy_settings"]	= "Editar privacidad de un ID";
$pgv_lang["user_privacy_settings"]		        = "Privacidad de usuario";
$pgv_lang["edit_exist_user_privacy_settings"]	= "Editar privacidad de un Usuario";
$pgv_lang["global_facts_settings"]		        = "Privacidad global de Acontecimientos";
$pgv_lang["edit_exist_global_facts_settings"]	= "Editar privacidad global de un Acontecimiento";
$pgv_lang["person_facts_settings"]		        = "Privacidad de un Acontecimiento para un ID";
$pgv_lang["edit_exist_person_facts_settings"]	= "Editar privacidad de un Acontecimiento";
$pgv_lang["accessible_by"]			            = "Visible por";
$pgv_lang["hide"]				                = "No";
$pgv_lang["show_question"]			            = "Mostrar";
$pgv_lang["user_name"]				            = "Nombre usuario";
$pgv_lang["name_of_fact"]			            = "Nombre acontecimiento";
$pgv_lang["choice"]				                = "Elegir";
$pgv_lang["fact_show"]				            = "Ver acontecimiento";
$pgv_lang["fact_details"]			            = "Ver detalles del Acont.";
$pgv_lang["privacy_header"]			            = "Editar privacidad";
$pgv_lang["unable_to_find_privacy_indi"]	    = "No fue posible encontrar una persona con el ID";
$pgv_lang["save_and_import"]			        = "Después de guardar la configuración para este GEDCOM, necesitará importar el archivo haciendo clic en el botón <b>Importar GEDCOM</b> o yendo a <b>Admin.->Administrar GEDCOMs->Importar</b>";
$pgv_lang["SHOW_LIVING_NAMES"]			        = "Ver nombres de vivos";
$pgv_lang["SHOW_RESEARCH_LOG"]			        = "Ver panel investigación";
$pgv_lang["USE_RELATIONSHIP_PRIVACY"]		    = "Utilizar privacidad en parentesco";
$pgv_lang["MAX_RELATION_PATH_LENGTH"]		    = "Max. largo del camino";
$pgv_lang["CHECK_MARRIAGE_RELATIONS"]		    = "Verificar parentesco por matrimonio";
$pgv_lang["SHOW_DEAD_PEOPLE"]			        = "Ver fallecidos";
$pgv_lang["help_info"]				            = "Puede obtener ayuda para cada opción haciendo clic en el signo de interrogación rojo \"?\" a la derecha de cada enunciado.";
$pgv_lang["SHOW_LIVING_NAMES_help"]		        = "Ver nombres de vivos<br /><br />Los nombres de las personas vivas serín vistos por todo el mundo por defecto, pero podemos cambiar esto aquí.<br /><br />Esto cambia la variable  \$SHOW_LIVING_NAMES en el archivo de privacidad  escogido.";
$pgv_lang["SHOW_RESEARCH_LOG_help"]		        = "Ver panel de investigación<br /><br />Quien puede ver el Panel de Investigación cuando éste está instalado.<br /><br />Esto cambia la variable  \$SHOW_RESEARCH_LOG en el archivo de privacidad  escogido.";
$pgv_lang["USE_RELATIONSHIP_PRIVACY_help"]	    = "Utilizar privacidad en Parentesco<br /><br />Un valor 'No' permite a los usuarios registrados ver los detalles de todas las personas vivas.<br />Un valor 'Sí' sólo permite ver la información privada de las personas vivas con las que los usarios registrados están relaccionados.<br /><br />Esto cambia la variable  \$USE_RELATIONSHIP_PRIVACY en el archivo de privacidad  escogido.";
$pgv_lang["MAX_RELATION_PATH_LENGTH_help"]	    = "Max. largo del camino de parentesco<br /><br />Segundos primos.<br /><br />Esto cambia la variable  \$MAX_RELATION_PATH_LENGTH en el archivo de privacidad  escogido.";
$pgv_lang["CHECK_MARRIAGE_RELATIONS_help"]	    = "Verificar parentesco por matrimonio<br /><br />Chequea el parentesco de las personas también a través de las relacciones por matrimonio.<br /><br />Esto cambia la variable  \$CHECK_MARRIAGE_RELATIONS en el archivo de privacidad  escogido.";
$pgv_lang["SHOW_DEAD_PEOPLE_help"]		        = "Ver Fallecidos<br /><br />Ajusta el nivel de acceso para toda las personas ya fallecidas.";
$pgv_lang["person_privacy_help"]		        = "El \"person_privacy\" permite a los administradores definir un ajuste de privacidad específico para una persona en particular del GEDCOM. Suponga, por ejemplo, que tuvo un hijo que falleció en la infancia. Por defecto como figura como fallecido sus detalles personales serían mostrados a todos los visitantes. Pero sin embargo para usted y su familia esta información sigue perteneciendo al ámbito privado. A usted no le interesa borrar la fecha de la defunción de su hijo pero quiere ocultar su información y hacerla privada. Si su hijo tuviese un ID de I100 debería hacer los ajustes de privacidad como sigue: <br /><br />ID: I100<br />Visible para: \"Sólo para usuarios registrados\"<br /><br />También nos sirve para proceder a la inversa. Si quiere hacer públicos los detalles de alguien (ID 101) que sabe que ha fallecido pero desconoce la fecha, podría ajustar su privacidad de la siguiente forma:<br /><br />ID: I101<br />Visible para: \"Todo el mundo\"<br /><br />Esto cambia la variable  \$person_privacy en el archivo de privacidad escogido.";
$pgv_lang["user_privacy_help"]			        = "El \"user_privacy\" permite a los administradores definir un ajuste de privacidad específico para una persona en particular del GEDCOM y un nombre de usuario del sitio.<br /><br />Así, si no quiero que el usuario registrado \"Juan\" pueda ver mi información personal, y tengo el ID 100 en el GEDCOM, podría configurar la privacidad así:<br /><br />Nombre usuario: Juan<br />ID: I100<br />Mostrar: \"No\"<br /><br /> y mis detalles personales permanecerín ocultos para el usuario registrado \"Juan\" solamente.<br /><br />Para mostrar los detalles de I101 (que normalmente estarían ocultos porque I101 todavía vive) de nuevo a  \"Juan\" ponga:<br /><br />Nombre usuario: Juan<br />ID: I101<br />Mostrar: \"Ver\"<br /><br />Esto cambia la variable  \$user_privacy en el archivo de privacidad escogido.";
$pgv_lang["global_facts_help"]			        = "El \"global_facts\" permite definir que acontecimientos ocultar a nivel global para todas las personas del GEDCOM.<br /><br />El campo [\"Nombre Acontecimiento\"] determina que acontecimientos debería permanecer ocultos. El campo [\"Elejir\"] permite escojer entre el acontecimiento en sí o los detalles relaccionados con él. El campo [\"Mostrar\"] determina a qué nivel de acceso será visible.<br /><br />El \$global_facts permite ocultar determinados acontecimientos para todas las personas vivas o fallecidas por su etiqueta GEDCOM. Por defecto, la etiqueta del NSS (Número Seguridad Social) permanece oculto para los visitantes. Esto es así para prevenir la sustracción de los números de las Seguridad Social y suplantación de identidad de sus familiares fallecidos, lo que es habitual en USA. <br />Si quisiese ocultar todos los matrimonios para todos los visitantes, pondría:<br /><br />Nombre acontecimiento: (MARR) - Matrimonio<br />Elegir: \"Ver acontecimiento\"<br />Visible para: \"Sólo usuarios registrados\"<br /><br />A diferencia de los demás ajustes, en \"global_facts\" puede suprimirr acontecimientos. Esto elimina definitivamente un acontecimiento que no nos interese.<br /><br />Esto cambia la variable  \$global_facts en el archivo de privacidad escogido.";
$pgv_lang["person_facts_help"]			        = "El \"person_facts\" define que acontecimientos permanecen ocultos para una persona en particular del GEDCOM y para que nivel de acceso.<br /><br />El primer campo es el ID de la persona, el segundo campo es el acontecimiento.<br />El campo [\"Elejir\"] permite escojer entre el acontecimiento en si, y los detalles del acontecimiento. El campo [\"Visible para\"] determina a qué nivel de acceso se mostrará el acontecimiento.<br /><br />El \$person_facts funciona de la misma manera que el \$global_facts exceptuando que en este caso puede especificar el ID de la persona de la que quiere ocultar el acontecimiento. De esta forma se podría ocultar el registro de matrimonio para una persona determinada.<br /><br />Esto cambia la variable  \$person_facts en el archivo de privacidad escogido.";
$pgv_lang["find_sourceid"]			            = "Buscar ID de la Fuente";

//-- language edit utility
$pgv_lang["edit_langdiff"]		    = "Editar y Configurar archivos de Idiomas";
$pgv_lang["edit_lang_utility"]		= "Herramienta para Edición de Idiomas";
$pgv_lang["edit_lang_utility_help"]	= "Puede utilizar esta herramienta para editar el contenido de un archivo de idioma usando el archivo del idioma inglés como patrón.<br />Listará el contenido del archivo original en inglés en una fila y el contenido del idioma que escoja en la otra.<br />Haciendo clic en el mensaje a editar, se abrirá una nueva ventana donde podrá hacer los cambios del idioma y guardarlos en el archivo de su idioma.";
$pgv_lang["language_to_edit"]		= "Idioma a editar";
$pgv_lang["file_to_edit"]		    = "Tipo de archivo a editar";
$pgv_lang["lang_save"]			    = "Guardar";
$pgv_lang["contents"]			    = "Contenidos";
$pgv_lang["listing"]			    = "Lista";
$pgv_lang["no_content"]			    = "No hay contenido";
$pgv_lang["editlang_help"]		    = "Editar mensaje desde archivo de idioma";
$pgv_lang["cancel"]			        = "Cancelar";
$pgv_lang["savelang_help"]		    = "Guardar mensaje editado";
$pgv_lang["original_message"]		= "Mensaje original";
$pgv_lang["message_to_edit"]		= "Mensaje a editar";
$pgv_lang["changed_message"]		= "Cambio realizado";
$pgv_lang["message_empty_warning"]	= "-> Atención!!! Este mensaje está vacío en [#LANGUAGE_FILE#] <-";
$pgv_lang["language_to_export"]		= "Idioma a exportar";
$pgv_lang["export_lang_utility"]	= "Exportar un archivo de idioma";
$pgv_lang["export"]			        = "Exportar";
$pgv_lang["export_lang_utility_help"]	= "En este módulo puede escoger un idioma y haciendo clic en el botón de exportar se exportarán los menajes de ayuda desde el archivo configuration_help del idioma elegido en formato html con el fin de crear una documentación.";
$pgv_lang["export_ok"]			    = "Mensajes de ayuda exportados";
$pgv_lang["compare_lang_utility"]	= "Comparación de archivos de idioma";
$pgv_lang["compare_lang_utility_help"]	= "Este módulo compara dos archivos de idiomas y nos suministra una lista con los añadidos y las supresiones habidas entre ellos.<br /><br />Ver también el archivo [<a href=\"languages\/LANG_CHANGELOG.txt\" target=\"_blank\">LANG_CHANGELOG.txt</a>] para todos los cambios.";
$pgv_lang["new_language"]		    = "Nuevo idioma";
$pgv_lang["old_language"]		    = "Idioma anterior";
$pgv_lang["compare"]			    = "Compare";
$pgv_lang["comparing"]			    = "Archivos de idiomas se comparan";
$pgv_lang["additions"]			    = "Añadidos";
$pgv_lang["no_additions"]		    = "No hubo añadidos";
$pgv_lang["subtractions"]		    = "Supresiones";
$pgv_lang["no_subtractions"]		= "No hubo supresiones";
$pgv_lang["config_lang_utility"]	= "Configuración de idiomas";
$pgv_lang["config_lang_utility_help"]	= "Este módulo le ayudará a seleccionar que idiomas en PhpGedView están activados o desactivados.<br /><br />";
$pgv_lang["active"]			        = "Activar";
$pgv_lang["active_help"]		    = "Permite a los usuarios seleccionar este idioma si Escoger Idioma está permitido.";
$pgv_lang["edit_settings"]		    = "Editar preferencias";
$pgv_lang["lang_edit"]			    = "Editar";
$pgv_lang["lang_language"]		    = "Idioma";
$pgv_lang["export_filename"]		= "Nombre del archivo con los datos de exportación:";
$pgv_lang["lang_back"]			    = "Regresar al menú principal para editar y configurar los archivos de idiomas";
$pgv_lang["lang_back_admin"]		= "Regresar al menú de Administración.";
$pgv_lang["lang_name_chinese"]		= "Chino";
$pgv_lang["lang_name_danish"]		= "Danés";
$pgv_lang["lang_name_dutch"]		= "Alemán";
$pgv_lang["lang_name_english"]		= "Inglés";
$pgv_lang["lang_name_french"]		= "Francés";
$pgv_lang["lang_name_german"]		= "Germano";
$pgv_lang["lang_name_hebrew"]		= "Hebreo";
$pgv_lang["lang_name_italian"]		= "Italiano";
$pgv_lang["lang_name_norwegian"]	= "Noruego";
$pgv_lang["lang_name_polish"]		= "Polaco";
$pgv_lang["lang_name_portuguese"]	= "Portugués";
$pgv_lang["lang_name_portuguese-br"]= "Portugués Brasileño";
$pgv_lang["lang_name_russian"]		= "Ruso";
$pgv_lang["lang_name_spanish"]		= "Español";
$pgv_lang["lang_name_spanish-ar"]	= "Español Latino Americano";
$pgv_lang["lang_name_swedish"]		= "Sueco";
$pgv_lang["lang_name_turkish"]		= "Turco";
$pgv_lang["original_lang_name"]		= "Nombre original del idioma en #D_LANGNAME#";
$pgv_lang["original_lang_name_help"]= "No hay ayuda todavía :-(";
$pgv_lang["lang_shortcut"]		    = "Atajo para archivos de idiomas";
$pgv_lang["lang_shortcut_help"]		= "No hay ayuda todavía :-(";
$pgv_lang["lang_filename"]		    = "Archivo de Idioma";
$pgv_lang["lang_filename_help"]		= "Nombre y ruta del archivo de idioma estandard para traducciones.";
$pgv_lang["config_filename"]		= "Nombre del Configure-Help";
$pgv_lang["config_filename_help"]	= "Nombre y ruta del archivo de configuración del idioma para traducciónes.";
$pgv_lang["facts_filename"]		    = "Archivo Acontecimientos ";
$pgv_lang["facts_filename_help"]	= "Nombre y ruta del archivo con las traducciones de los Acontecimientos.";
$pgv_lang["help_filename"]		    = "Archivo de Ayuda";
$pgv_lang["help_filename_help"]		= "Nombre y ruta del archivo con las traducciones del texto de Ayuda.";
$pgv_lang["flagsfile"]			    = "Nombre archivo banderas";
$pgv_lang["flagsfile_help"]		    = "Nombre y ruta del archivo de imágenes con las banderas para el idioma seleccionado.";
$pgv_lang["text_direction"]		    = "Dirección texto";
$pgv_lang["text_direction_help"]	= "No hay ayuda todavía :-(";
$pgv_lang["date_format"]		    = "Formato fecha";
$pgv_lang["date_format_help"]		= "No hay ayuda todavía :-(";
$pgv_lang["week_start"]			    = "Comienzo semana";
$pgv_lang["week_start_help"]		= "El día de la semana en que comienza una nueva en su idioma. La mayoría de los idiomas comienzan la semana el Domingo, pero algunos comienzan el Lunes u otros dís.";
$pgv_lang["name_reverse"]		    = "Primero apellido";
$pgv_lang["name_reverse_help"]		= "En algunos idiomas el apellido debería mostrarse delante del nombre, en lugar del modo por defecto en que se muestra en último lugar. Activando esta opción el apellido se mostrará primero.";
$pgv_lang["ltr"]			        = "Izquierda a derecha";
$pgv_lang["rtl"]			        = "Derecha a izquierda";
$pgv_lang["file_does_not_exist"]	= "ERROR! El archivo no existe...";
$pgv_lang["alphabet_upper"]		    = "Alfabeto Mayúsculas";
$pgv_lang["alphabet_upper_help"]	= "Las letras mayúsculas del alfabeto en este idioma. Este alfabeto es utilizado para ordenar bajo mayúsculas los nombres de las listas de personas del PhpGedView.";
$pgv_lang["alphabet_lower"]		    = "Alfabeto minúsculas";
$pgv_lang["alphabet_lower_help"]	= "Las letras minúsculas del alfabeto en este idioma. Este alfabeto es utilizado para ordenar bajo minúsculas los nombres de las listas de personas del PhpGedView.";
$pgv_lang["lang_config_write_error"]= "Error escribiendo los ajustes del idioma en el archivo [language_settings.php]. Chequee los permisos e inténtelo de nuevo.";
$pgv_lang["lang_save_success"]		= "Cambios del #PGV_LANG# guardados correctamente";
$pgv_lang["translation_forum"]		= "Enlace a foro PhpGedView de traducciones en SourceForge";
$pgv_lang["translation_forum_help"]	= "Este enlace abre una nueva ventana donde será redireccionado al foro de traducciones del PhpGedView en(http://sourceforge.net/forum/forum.php?forum_id=294245) donde puede intercambiar opiniones sobre problemas específicos de la traducción :-)";
$pgv_lang["system_time"]		    = "Tiempo del sistema:";
$pgv_lang["gedcom_not_imported"]	= "Este GEDCOM no ha sido importado todavía.";
$pgv_lang["lang_set_file_read_error"]	= "E R R O R !!! No se pudo leer el [language_settings.php]!";

?>
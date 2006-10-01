<?php
/**
 * Portugese Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Maurício Menegazzo Rosa
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
 * @author Maurício Menegazzo Rosa
 * @author Anderson Wilson
 * @version $Id$
 */
if (preg_match("/configure_help\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
//-- CONFIGURE FILE MESSAGES
$pgv_lang["COMMIT_COMMAND_help"]="~#pgv_lang[COMMIT_COMMAND]#~<br /><br />Deixe em branco caso não queira utilizar um sistema de controle de versão tal como o CVS para salvar as alterações e configurações do seu GEDCOM, caso contrário as opções são <b>cvs</b> e <b>svn</b>.<br />";
$pgv_lang["SHOW_MULTISITE_SEARCH_help"]="~#pgv_lang[SHOW_MULTISITE_SEARCH]#~<br /><br />Esta opção restringe ou não o uso da pesquisa Multi-Site aos usuários identificados. A pesquisa Multi-Site procura a informação nos sites cadastrados em \"Gerenciar Sites\" e nos sites remotos.<br />";
$pgv_lang["SHOW_MULTISITE_SEARCH"]="Exibir Pesquisa Multi-Site";
$pgv_lang["DBPERSIST"]="Usar conexão permanente com o Banco de Dados";
$pgv_lang["DBPERSIST_help"]="Define se a conexão do PhpGedView com o Banco de Dados é perene.<br /><br />Em caso afirmativo, o PhpGedView reutiliza as conexões com o banco de dados agilizando a montagem das páginas. Entretanto isso pode ocasionar êrros se o limite máximo de seu servidor for baixo. Na dúvida responda <b>Não</b>.<br />";
$pgv_lang["INDI_FACTS_ADD"]="Fatos/Eventos da Pessoa";
$pgv_lang["INDI_FACTS_ADD_help"]="~#pgv_lang[INDI_FACTS_ADD]#~<br /><br />Esta é a lista de fatos que os usuários podem adicionar às pessoas da árvore. Você pode criar novos tipos ou remover alguns deles conforme sua necessidade. Tipos de fatos que aperecem nesta lista não devem aparecer na lista <i>#pgv_lang[INDI_FACTS_UNIQUE]#</i>.<br />";
$pgv_lang["INDI_FACTS_UNIQUE"]="Fatos Singulares da Pessoa";
$pgv_lang["INDI_FACTS_UNIQUE_help"]="~#pgv_lang[INDI_FACTS_UNIQUE]#~<br /><br />Esta é a lista de fatos que podem ser adicionados <u>uma única vez</u> a uma pessoa. Fatos que estejam nesta lista não podem estar na lista <i>#pgv_lang[INDI_FACTS_ADD]#</i>.<br />";
$pgv_lang["FAM_FACTS_ADD"]="Fatos/Eventos da Família";
$pgv_lang["FAM_FACTS_ADD_help"]="~#pgv_lang[FAM_FACTS_ADD]#~<br /><br />Estes são os fatos e eventos que podem ser adicionados às famílias. É permitido adicionar ou remover fatos a lista, mesmo os customizados. Entretanto fatos que estejam nesta lista, não podem aparecer na lista <i>#pgv_lang[FAM_FACTS_UNIQUE]#</i>.<br />";
$pgv_lang["FAM_FACTS_UNIQUE"]="Fatos Singulares da Família";
$pgv_lang["FAM_FACTS_UNIQUE_help"]="~#pgv_lang[FAM_FACTS_UNIQUE]#~<br /><br />Esta é a lista de fatos/eventos da família, que os usuários podem adicionar uma <u>única</u> vez. Por exemplo, se MARR esta na lista, usuários não poderão adicionar mais de um MARR à família. Fatos e eventos que estejam nesta lista não podem estar na lista <i>#pgv_lang[FAM_FACTS_ADD]#</i>.<br />";
$pgv_lang["SOUR_FACTS_ADD"]="Fatos/Eventos de Fonte";
$pgv_lang["SOUR_FACTS_ADD_help"]="~#pgv_lang[SOUR_FACTS_ADD]#~<br /><br />Esta é a lista de fatos e eventos que os usuários podem adicionar a uma fonte. A lista pode ser alterada, inclusive com a adição de eventos e fatos customisados, porém fatos e eventos desta lista não podem estar na lista <i>#pgv_lang[SOUR_FACTS_UNIQUE]#</i>.<br />";
$pgv_lang["SOUR_FACTS_UNIQUE"]="Fatos/Eventos Singulares de Fonte";
$pgv_lang["SOUR_FACTS_UNIQUE_help"]="~#pgv_lang[SOUR_FACTS_UNIQUE]#~<br /><br />Esta é a lista de fatos e eventos de fontes, que os usuário podem adicionar uma <u>única</u> vez. Por exemplo, se TITL esta na lista, usuários poderão adicionar apenas uma vez TITL a uma fonte. Fatos e eventos desta lista não podem estar na lista <i>#pgv_lang[SOUR_FACTS_ADD]#</i>.<br />";
$pgv_lang["LANG_SELECTION"]="Idiomas Disponíveis";
$pgv_lang["LANG_SELECTION_help"]="~#pgv_lang[LANG_SELECTION]#~<br /><br />Selecione na lista, quais idiomas seu site PhpGedView oferecerá aos usuários.<br />O mesmo pode ser feito através do link <b>#pgv_lang[enable_disable_lang]#</b> no menu #pgv_lang[admin]#, onde também é possível alterar a bandeira do idioma, o formato de datas, ou se o sobrenome deve aparecer antes do nome.<br />";
$pgv_lang["MEDIA_ID_PREFIX"]="Prefixo da ID da Mídia";
$pgv_lang["MEDIA_ID_PREFIX_help"]="~#pgv_lang[MEDIA_ID_PREFIX]#~<br /><br />Quando uma mídia é adicionada ao GEDCOM, uma nova ID é criada automaticamente para a mídia.<br />";
$pgv_lang["FAM_ID_PREFIX"]="Prefixo da ID da Família";
$pgv_lang["FAM_ID_PREFIX_help"]="~#pgv_lang[FAM_ID_PREFIX]#~<br /><br />Quando um novo registro é adicionado à família, uma nova ID é atribuida a este registro com este prefixo.<br />";
$pgv_lang["QUICK_REQUIRED_FAMFACTS"]="Fatos da Família sempre exibidios na Edição Simplificada";
$pgv_lang["QUICK_REQUIRED_FAMFACTS_help"]="~#pgv_lang[QUICK_REQUIRED_FAMFACTS]#~<br /><br />Esta é uma lista de fatos e eventos separados por virgula, que sempre serão exibidos no formulário de edição simplificada, mesmo que estes fatos ainda não tenho sido informados.<br />";
$pgv_lang["QUICK_ADD_FAMFACTS"]="Fatos da Família sempre exibidios na Edição Simplificada";
$pgv_lang["QUICK_REQUIRED_FACTS"]="Fatos exibidos na Edição Simplificada";
$pgv_lang["QUICK_ADD_FACTS"]="Fatos exibidos na Edição Simplificada";
$pgv_lang["AUTO_GENERATE_THUMBS"]="Miniaturas geradas automaticamente";
$pgv_lang["more_config_hjaelp"]="<br /><b>Mais Ajuda</b><br />Mais ajuda pode ser obtida clicando em <b>?</b> ao lado dos itens da página.<br />";
$pgv_lang["THUMBNAIL_WIDTH"]="Largura das Miniaturas geradas";
$pgv_lang["SHOW_SOURCES"]="Exibir Fontes";
$pgv_lang["REQUIRE_ADMIN_AUTH_REGISTRATION"]="Registro dos novos usuários deverão ser aprovados pelo administrador";
$pgv_lang["SPLIT_PLACES"]="Na edição, \"quebre\" os Locais";
$pgv_lang["ALLOW_REMEMBER_ME"]="Exibir a opção <b>Lembre-se de Mim</b> na página de identificação";
$pgv_lang["UNDERLINE_NAME_QUOTES"]="Sublinhe nomes entre aspas";
$pgv_lang["PRIVACY_BY_RESN"]="Respeitar e Usar a restrição a priovacidade do GEDCOM (RESN)";
$pgv_lang["GEDCOM_DEFAULT_TAB"]="A ficha que será exibida primeiro na página da pessoa";
$pgv_lang["SHOW_MARRIED_NAMES"]="Exibir nome de casada na lista de Pessoas";
$pgv_lang["SHOW_QUICK_RESN"]="Exibir campos no formulário #pgv_lang[quick_update_title]#";
$pgv_lang["USE_QUICK_UPDATE"]="Usar formulário #pgv_lang[quick_update_title]#";
$pgv_lang["CHART_BOX_TAGS"]="Outros fatos a exibir nos gráficos";
$pgv_lang["configure"]="Configurar PhpGedView";
$pgv_lang["standard_conf"]="Opções das Configurações Padrão";
$pgv_lang["advanced_conf"]="Opções das Configurações Avançadas";
$pgv_lang["media_conf"]="Mídias";
$pgv_lang["accpriv_conf"]="Acesso e Privacidade";
$pgv_lang["displ_conf"]="Exibição e Formatação";
$pgv_lang["displ_names_conf"]="Nomes";
$pgv_lang["displ_comsurn_conf"]="Sobrenomes Comuns";
$pgv_lang["displ_layout_conf"]="Formatação";
$pgv_lang["displ_hide_conf"]="Exibir ou Esconder";
$pgv_lang["editopt_conf"]="Opções de Edição";
$pgv_lang["useropt_conf"]="Opções do Usuário";
$pgv_lang["contact_conf"]="Informação para Contato";
$pgv_lang["configure_head"]="Configuração do PhpGedView";
$pgv_lang["gedconf_head"]="Configuração GEDCOM";
$pgv_lang["default_user"]="Criar a conta de Administrador";
$pgv_lang["about_user"]="Primeiro crie a conta do Administrador, pois é ele que tem privilégios para alterar os arquivos de configuração, ver dados privados e criar outros usuários.";
$pgv_lang["can_admin"]="Pode Administrar";
$pgv_lang["can_edit"]="Nível de Acesso";
$pgv_lang["access"]="Acessar";
$pgv_lang["sync_gedcom"]="Sincronizar os dados do Usuário com os dados do Banco de Dados (GEDCOM)";
$pgv_lang["user_relationship_priv"]="Restringir o acesso a pessoas relacionadas ao usuário";
$pgv_lang["add_user"]="Adicionar um novo Usuário";
$pgv_lang["current_users"]="Usuários Cadastrados";
$pgv_lang["leave_blank"]="Deixe a senha em branco para manter a senha inalterada.";
$pgv_lang["other_theme"]="Outro, informe";
$pgv_lang["performing_update"]="Executando atualização.";
$pgv_lang["does_not_exist"]="não existe";
$pgv_lang["media_drive_letter"]="A localização da Mídia não deve conter a letra do drive; a mídia pode não ser exibida.";
$pgv_lang["click_here_to_continue"]="Clique aqui para continuar.";
$pgv_lang["config_help"]="Configuration Help";
$pgv_lang["mysql"]="MySQL";
$pgv_lang["db"]="Banco de Dados";
$pgv_lang["dbase"]="dBase";
$pgv_lang["fbsql"]="FrontBase";
$pgv_lang["ibase"]="InterBase";
$pgv_lang["ifx"]="Informix";
$pgv_lang["msql"]="Mini SQL";
$pgv_lang["mssql"]="Microsoft SQL server";
$pgv_lang["mysqli"]="MySQL 4.1+ and PHP 5";
$pgv_lang["oci8"]="Oracle 7+";
$pgv_lang["pgsql"]="PostgreSQL";
$pgv_lang["sqlite"]="SQLite";
$pgv_lang["sybase"]="Sybase";
$pgv_lang["admin_gedcoms"]="Clique aqui para Administrar os GEDCOMs.";
$pgv_lang["current_gedcoms"]="GEDCOMs Cadastrados";
$pgv_lang["gedcom_adm_head"]="Gerenciar GEDCOM";
$pgv_lang["ged_download"]="Descarregar";
$pgv_lang["ged_gedcom"]="Arquivo GEDCOM";
$pgv_lang["ged_title"]="Título GEDCOM";
$pgv_lang["ged_config"]="Arquivo de Configuração";
$pgv_lang["ged_privacy"]="Arquivo de Privacidade";
$pgv_lang["show_phpinfo"]="Mostrar a página PHPInfo";
$pgv_lang["confirm_gedcom_delete"]="Você tem certeza que deseja excluir este GEDCOM";
$pgv_lang["gregorian"]="Gregoriano";
$pgv_lang["julian"]="Juliano";
$pgv_lang["config_french"]="Francês";
$pgv_lang["jewish"]="Judeu";
$pgv_lang["config_hebrew"]="Hebreu";
$pgv_lang["jewish_and_gregorian"]="Judeu e Gregoriano";
$pgv_lang["hebrew_and_gregorian"]="Hebreu e Gregoriano";
$pgv_lang["hijri"]="Hijri";
$pgv_lang["disabled"]="Desativado";
$pgv_lang["mousedown"]			= "Se Mouse Pressionado";
$pgv_lang["mouseover"]			= "Se Mouse Em Cima";
$pgv_lang["click"]="Ao Clicar";
$pgv_lang["mailto"]="Somente E-Mail Externo";
$pgv_lang["messaging3"]			= "PhpGedView envia emails sem armazena-los";
$pgv_lang["no_logs"]			= "Desabilitar Diário (Log)";
$pgv_lang["messaging"]="Somente E-Mail Interno";
$pgv_lang["messaging2"]="E-Mail Interno e Externo";
$pgv_lang["no_messaging"]="Nenhum método de contato";
$pgv_lang["daily"]="Diariamente";
$pgv_lang["config_still_writable"]	= "O arquivo <i>config.php</i> permanece com permissão de escrita. Por segurança, configure a permissão deste arquivo apenas para leitura após terminar de alterar a configuração de seu site.";
$pgv_lang["admin_user_warnings"]		= "Uma ou mais contas de usuários possuem avisos";
$pgv_lang["weekly"]="Semanalmente";
$pgv_lang["monthly"]="Mensalmente";
$pgv_lang["yearly"]="Anualmente";
$pgv_lang["enter_db_pass"]="Por segurança, informe #pgv_lang[DBUSER]# e #pgv_lang[DBPASS]# antes de alterar a configuração.";
$pgv_lang["admin_verification_waiting"]="Existem solicitações de contas pendentes de autorização";
$pgv_lang["DBPASS"]			= "Database Password";
$pgv_lang["DBUSER"]			= "Database Username";
$pgv_lang["DEFAULT_GEDCOM"]="Default GEDCOM";
$pgv_lang["upload_path"]			= "Pasta de destino";
$pgv_lang["CHARACTER_SET"]		= "Codificação do Conjunto de Caracteres";
$pgv_lang["privileges"]="Privilégios";
$pgv_lang["LANGUAGE_DEFAULT"]		= "Não houve configuração de idiomas para este site.<br />PhpGedView usará o padrão.";
$pgv_lang["MAX_DESCENDANCY_GENERATIONS"]	= "Máximo de gerações de Descendentes";
$pgv_lang["DISPLAY_JEWISH_THOUSANDS"]	= "Exibir Milhares em Hebráico";
$pgv_lang["date_registered"]="Data de Registro";
$pgv_lang["last_login"]="Último acesso";
$pgv_lang["DBTYPE"]="Tipo de Banco de Dados";
$pgv_lang["DBHOST"]="Servidor de Banco de Dados";
$pgv_lang["DBNAME"]="Nome do Banco de Dados";
$pgv_lang["TBLPREFIX"]="Prefixo das Tabelas do Banco de Dados";
$pgv_lang["ALLOW_CHANGE_GEDCOM"]="Permitir trocar de GEDCOM";
$pgv_lang["gedcom_path"]="Localização e nome do GEDCOM no servidor";
$pgv_lang["LANGUAGE"]="Idioma";
$pgv_lang["ENABLE_MULTI_LANGUAGE"]="Permitir a troca de idioma";
$pgv_lang["CALENDAR_FORMAT"]="Formato do Calendário";
$pgv_lang["SHOW_EMPTY_BOXES"]="Exibir caixas vazias no gráfico";
$pgv_lang["ABBREVIATE_CHART_LABELS"]="Abreviar títulos do gráfico";
$pgv_lang["SHOW_PARENTS_AGE"]="Exibir idade dos pais ao lado da data de nascimento";
$pgv_lang["SHOW_RELATIVES_EVENTS"]="Na página da pessoa, exibir eventos dos parentes próximos";
$pgv_lang["EXPAND_RELATIVES_EVENTS"]="Expandir automaticamente lista de eventos";
$pgv_lang["EXPAND_SOURCES"]="Expandir automaticamente Fontes";
$pgv_lang["ALPHA_INDEX_LISTS"]		= "Quebrar longas listas pela primeira letra";
$pgv_lang["POSTAL_CODE"]  = "Posição do Código Postal";
$pgv_lang["MULTI_MEDIA"]		= "Habilitar recursos para Mídias";
$pgv_lang["HIDE_LIVE_PEOPLE"]="Habilitar privacidade";
$pgv_lang["REQUIRE_AUTHENTICATION"]="Visitantes devem identificar-se";
$pgv_lang["WELCOME_TEXT_AUTH_MODE"]="Texto de boas-vindas da página de identificação do modo Autenticador";
$pgv_lang["USE_THUMBS_MAIN"]	= "Usar miniatura da imagem principal na página de indivíduos";
$pgv_lang["WORD_WRAPPED_NOTES"]		= "Adicionar espaços para notas excluídas ";
$pgv_lang["CONTACT_EMAIL"]		= "Contato Genealógico";
$pgv_lang["COMMON_NAMES_REMOVE"]	= "Nomes a serem excluídos da lista de Nomes Comuns (separados por vírgula)";
$pgv_lang["COMMON_NAMES_THRESHOLD"]	= "Mínimo de ocorrências para ser um \"Nome Comum\"";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT1"]="texto informando que qualquer um pode solicitar uma conta";
$pgv_lang["PGV_MEMORY_LIMIT"]		= "Limite de Memória Utilizada";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT2"]="texto informando que o admin. aceitará ou não a solictação de conta";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT3"]="texto informando que somente famíliares serão aceitos";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT4"]="Exibir o texto abaixo como sendo o texto de boas-vindas";
$pgv_lang["CHECK_CHILD_DATES"]="Verificar data dos filhos";
$pgv_lang["MAX_ALIVE_AGE"]="Pessoas serão consideradas falecidas a partir desta idade<br />";
$pgv_lang["SHOW_GEDCOM_RECORD"]="Exibir os registros do GEDCOM para os usuários";
$pgv_lang["ALLOW_EDIT_GEDCOM"]="Permitir edição online";
$pgv_lang["EDIT_AUTOCLOSE"]="Fechamento automático da janela de edição.";
$pgv_lang["INDEX_DIRECTORY"]="Pasta do arquivo de Índice";
$pgv_lang["SHOW_ID_NUMBERS"]="Exibir números de ID ao lado dos nomes";
$pgv_lang["SHOW_FAM_ID_NUMBERS"]="Exibir os IDs ao lado das famílias";
$pgv_lang["MEDIA_EXTERNAL"]="Guardar links";
$pgv_lang["MEDIA_DIRECTORY"]="Pasta de Mídias";
$pgv_lang["MEDIA_DIRECTORY_LEVELS"]="Qtde de sub-níveis da pasta de Mídias";
$pgv_lang["SHOW_HIGHLIGHT_IMAGES"]="Exibir imagens de destaque na página de pessoas";
$pgv_lang["HIDE_GEDCOM_ERRORS"]="Não exibir erros do GEDCOM";
$pgv_lang["DAYS_TO_SHOW_LIMIT"]="Limite de dias do bloco Próximos Eventos";
$pgv_lang["PGV_SESSION_TIME"]="Tempo máximo de uma sessão";
$pgv_lang["USE_REGISTRATION_MODULE"]="Sub-níveis da pasta de Mídias";
$pgv_lang["ALLOW_USER_THEMES"]="Usuários podem selecionar o tema de sua preferência";
$pgv_lang["LOGFILE_CREATE"]="Frequência de arquivamento dos Logs";
$pgv_lang["PGV_STORE_MESSAGES"]="Permitir armazenamento de mensagens no servidor";
$pgv_lang["save_config"]="Salvar Configuração";
$pgv_lang["lang_name_czech"]="Tcheco";
$pgv_lang["lang_name_chinese"]="Chinês";
$pgv_lang["lang_name_danish"]="Dinamarquês";
$pgv_lang["lang_name_dutch"]="Holandês";
$pgv_lang["lang_name_english"]="Inglês";
$pgv_lang["lang_name_finnish"]="Finlandês";
$pgv_lang["lang_name_french"]="Francês";
$pgv_lang["lang_name_german"]="Alemão";
$pgv_lang["lang_name_hebrew"]="Hebraico";
$pgv_lang["lang_name_hungarian"]="Húngaro";
$pgv_lang["lang_name_italian"]="Italiano";
$pgv_lang["lang_name_norwegian"]="Norueguês";
$pgv_lang["lang_name_polish"]="Polonês";
$pgv_lang["lang_name_portuguese"]="Português";
$pgv_lang["lang_name_portuguese-br"]="Português (Brasil)";
$pgv_lang["lang_name_russian"]="Russo";
$pgv_lang["lang_name_spanish"]="Espanhol";
$pgv_lang["lang_name_spanish-ar"]="Espanhol Latino Americano";
$pgv_lang["lang_name_swedish"]="Sueco";
$pgv_lang["lang_name_turkish"]="Turco";
$pgv_lang["lang_new_language"]="Novo Idioma";
$pgv_lang["never"]="Nunca";
?>

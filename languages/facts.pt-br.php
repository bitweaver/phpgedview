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
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their brasilian portugese values
$factarray["ABBR"]="Abreviação";
$factarray["ADDR"]="Endereço";
$factarray["ADR1"]="Endereço 1";
$factarray["ADR2"]="Endereço 2";
$factarray["ADOP"]="Adoção";
$factarray["AGE"]="Idade";
$factarray["AGNC"]="Agência";
$factarray["ALIA"]="Apelido";
$factarray["ANCE"]="Ancestrais";
$factarray["ANUL"]="Anulação";
$factarray["ASSO"]="Associado";
$factarray["AUTH"]="Autor";
$factarray["BAPL"]="Batismo LDS";
$factarray["BAPM"]="Batismo";
$factarray["BARM"]="Bar Mitzvah";
$factarray["BASM"]="Bas Mitzvah";
$factarray["BIRT"]="Nascimento";
$factarray["BLES"]="Bênção";
$factarray["BLOB"]="Objeto Binário";
$factarray["BURI"]="Sepultamento";
$factarray["CALN"]="Número de Chamada";
$factarray["CAST"]="Casta/Status Social";
$factarray["CAUS"]="Causa do Falecimento";
$factarray["CEME"]="Cemitério";
$factarray["CENS"]="Censo";
$factarray["CHAN"]="Atualizado em";
$factarray["CHAR"]="Conjunto de caracteres";
$factarray["CHIL"]="Filho";
$factarray["CHR"]="Batizado";
$factarray["CITY"]="Cidade";
$factarray["CONF"]="Confirmação";
$factarray["CONL"]="Confirmação LDS";
$factarray["COPR"]="Direitos Autorais";
$factarray["CORP"]="Instituição/Empresa";
$factarray["CREM"]="Cremação";
$factarray["CTRY"]="País";
$factarray["DATA"]="Dados";
$factarray["DATE"]="Data";
$factarray["DEAT"]="Falecimento";
$factarray["DESC"]="Descendentes";
$factarray["DEST"]="Destino";
$factarray["DIV"]="Divórcio";
$factarray["DIVF"]="Pedido de Divórcio";
$factarray["DSCR"]="Descrição";
$factarray["EDUC"]="Educação";
$factarray["EMIG"]="Emigração";
$factarray["ENGA"]="Noivado";
$factarray["EVEN"]="Evento";
$factarray["FAM"]="Família";
$factarray["FAMC"]="Família como Filho";
$factarray["FAMF"]="Arquivo de Família";
$factarray["FAMS"]="Familia como Cônjuge";
$factarray["FCOM"]="Primeira Comunhão";
$factarray["FILE"]="Arquivo Externo";
$factarray["FORM"]="Formato";
$factarray["GIVN"]="Nomes";
$factarray["GRAD"]="Graduação";
$factarray["HUSB"]="Marido";
$factarray["IDNO"]="Número de Identificação";
$factarray["IMMI"]="Imigração";
$factarray["LEGA"]="Herdeiro";
$factarray["MARC"]="Contrato Matrimonial";
$factarray["MARL"]="Licença de Casamento";
$factarray["MARR"]="Casamento";
$factarray["MARS"]="Acôrdo Matrimonial";
$factarray["MEDI"]="Tipo de Mídia";
$factarray["NAME"]="Nome";
$factarray["NATI"]="Nacionalidade";
$factarray["NATU"]="Naturalização";
$factarray["NCHI"]="Número de Filhos";
$factarray["NICK"]="Apelido";
$factarray["NMR"]="Número de Casamentos";
$factarray["NOTE"]="Nota";
$factarray["NPFX"]="Prefixo";
$factarray["NSFX"]="Sufixo";
$factarray["OBJE"]="Mídia";
$factarray["OCCU"]="Ocupação";
$factarray["ORDI"]="Mandato";
$factarray["ORDN"]="Ordenação";
$factarray["PAGE"]="Detalhes da Citação";
$factarray["PEDI"]="Árvore Genealógica";
$factarray["PLAC"]="Local";
$factarray["PHON"]="Telefone";
$factarray["POST"]="CEP";
$factarray["PROB"]="Comprovação de Legitimidade";
$factarray["PROP"]="Propriedade";
$factarray["PUBL"]="Publicação";
$factarray["QUAY"]="Credibilidade dos Dados";
$factarray["REPO"]="Repositório ";
$factarray["REFN"]="Número de Referência";
$factarray["RELA"]="Relacionamento";
$factarray["RELI"]="Religião";
$factarray["RESI"]="Residência";
$factarray["RESN"]="Restrição";
$factarray["RETI"]="Aposentadoria";
$factarray["RFN"]="Número do Registro";
$factarray["RIN"]="Número de Identificação do Registro";
$factarray["ROLE"]="Cargo";
$factarray["SEX"]="Sexo";
$factarray["SOUR"]="Fonte";
$factarray["SPFX"]="Prefixo do Sobrenome";
$factarray["SSN"]="Número do Seguro Social";
$factarray["STAE"]="Estado";
$factarray["STAT"]="Situação";
$factarray["SURN"]="Sobrenome";
$factarray["TEMP"]="Templo";
$factarray["TEXT"]="Texto";
$factarray["TIME"]="HH:MM:SS";
$factarray["TITL"]="Título";
$factarray["TYPE"]="Tipo";
$factarray["WIFE"]="Esposa";
$factarray["WILL"]="Testamento";
$factarray["_EMAIL"]="Endereço de Email";
$factarray["EMAIL"]="Endereço de Email";
$factarray["_TODO"]="A Fazer";
$factarray["_UID"]="Identificador Universal";
$factarray["_PRIM"]="Imagem de destaque";
$factarray["_MDCL"]	= "Prontuário";
$factarray["_DEG"]="Graduação";
$factarray["_MILT"]="Serviço Militar";
$factarray["_SEPR"]="Separado";
$factarray["_DETS"]="Falecimento de Cônjuge";
$factarray["CITN"]="Cidadania";
$factarray["_FA1"]="Fato 1";
$factarray["_FA2"]="Fato 2";
$factarray["_FA3"]="Fato 3";
$factarray["_FA4"]="Fato 4";
$factarray["_FA5"]="Fato 5";
$factarray["_FA6"]="Fato 6";
$factarray["_FA7"]="Fato 7";
$factarray["_FA8"]="Fato 8";
$factarray["_FA9"]="Fato 9";
$factarray["_FA10"]="Fato 10";
$factarray["_FA11"]="Fato 11";
$factarray["_FA12"]="Fato 12";
$factarray["_FA13"]="Fato 13";
$factarray["_MEND"]	= "Situação Final Do Casamento";
$factarray["_MSTAT"]	= "Situação Inicial Do Casamento";
$factarray["_FREL"]	= "Relação Paterna";
$factarray["_MREL"]	= "Relação Materna";
$factarray["FAX"]="FAX";
$factarray["FACT"]="Fato";
$factarray["WWW"]="Página Pessoal na Internet";
$factarray["MAP"]="Mapa";
$factarray["LATI"]="Latitude";
$factarray["LONG"]="Longitude";
$factarray["_HEB"] = "Hebreu";
$factarray["FONE"]="Fonético";
$factarray["ROMN"]="Romano";
$factarray["_NAME"]="Nome para Correspondência";
$factarray["URL"]="URL na Internet";
$factarray["_SCBK"]="Livro de Fotos";
$factarray["_TYPE"]="Tipo de Mídia";
$factarray["_SSHOW"]="Show de Slide";
$factarray["_SUBQ"]="Versão Reduzida";
$factarray["_BIBL"]="Bibliografia";
$factarray["EMAL"]="Endereço de Email";
$factarray["_ADPF"]="Adoção Paterna";
$factarray["_ADPM"]="Adoção Materna";
$factarray["_AKAN"]="Também conhecido por";
$factarray["_AKA"]="Também conhecido por";
$factarray["_EYEC"]="Cor dos olhos";
$factarray["_MARI"]	= "Intenção Matrimonial";
$factarray["_FNRL"]="Funeral";
$factarray["_HAIR"]="Cor do cabelo";
$factarray["_HEIG"]="Altura";
$factarray["_HOL"]="Holocausto";
$factarray["_INTE"]="Sepultado";
$factarray["_MBON"]="Ligação Familiar";
$factarray["_MEDC"]="Quadro Clínico";
$factarray["_MILI"]="Militar";
$factarray["_NMR"]="Solteiro";
$factarray["_NLIV"]="Não Vivos";
$factarray["_NMAR"]="Nunca foi casado";
$factarray["_PRMN"]="Número permanente";
$factarray["_WEIG"]="Peso";
$factarray["_YART"]="Yartzeit";
$factarray["_MARNM"]="Nome de casada";
$factarray["_STAT"]="Estado Civil";
$factarray["COMM"]="Comentário";
$factarray["MARR_CIVIL"]="Casamento Civil";
$factarray["MARR_RELIGIOUS"]="Casamento Religioso";
$factarray["MARR_PARTNERS"]="Regime de Bens";
$factarray["MARR_UNKNOWN"]="Casamento Desconhecido";
$factarray["_HNM"]="Nome Hebráico";
$factarray["_DEAT_SPOU"]="Falecimento do Cônjuge";
$factarray["_BIRT_CHIL"]="Nascimento de Filho";
$factarray["_MARR_CHIL"]="Casamento de Filho";
$factarray["_DEAT_CHIL"]="Falecimento de Filho";
$factarray["_BIRT_GCHI"]="Nascimento de Neto";
$factarray["_MARR_GCHI"]="Casamento de Neto";
$factarray["_DEAT_GCHI"]="Falecimento de Neto";
$factarray["_MARR_FATH"]="Casamento do Pai";
$factarray["_DEAT_FATH"]="Falecimento do Pai";
$factarray["_MARR_MOTH"]="Casamento da Mãe";
$factarray["_DEAT_MOTH"]="Falecimento da Mãe";
$factarray["_BIRT_SIBL"]="Nascimento de Parente";
$factarray["_MARR_SIBL"]="Casamento de Parente";
$factarray["_DEAT_SIBL"]="Falecimento de Parente";
$factarray["_BIRT_HSIB"]="Nascimento de Aparentado";
$factarray["_MARR_HSIB"]="Casamento de Aparentado";
$factarray["_DEAT_HSIB"]="Falecimento de Aparentado";
$factarray["_DEAT_GPAR"]="Falecimento de Avô";
$factarray["_BIRT_FSIB"]="Nascimento de Parente Paterno";
$factarray["_MARR_FSIB"]="Casamento de Parente Paterno";
$factarray["_DEAT_FSIB"]="Falecimento de Parente Paterno";
$factarray["_BIRT_MSIB"]="Nascimento de Parente Materno";
$factarray["_MARR_MSIB"]="Casamento de Parente Materno";
$factarray["_DEAT_MSIB"]="Falecimento de Parente Materno";
$factarray["_BIRT_COUS"]="Nascimento de Primo";
$factarray["_MARR_COUS"]="Casamento de Primo";
$factarray["_DEAT_COUS"]="Falecimento de Primo";
$factarray["_THUM"]="Usar esta imagem como Miniatura?";
$factarray["_PGVU"]="Última Alteração por";
$factarray["SERV"]="Servidor Remoto";
$factarray["_GEDF"]="Arquivo GEDCOM";
if (file_exists("languages/facts.pt-br.extra.php")) require "languages/facts.pt-br.extra.php";
?>

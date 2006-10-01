<?php
/**
 * Russian Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Eugene Fedorov, Natalia Anikeeva
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
 * @author Eugene Fedorov
 * @author Natalia Anikeeva
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their Russian values
$factarray["ABBR"] = "Сокращение";
$factarray["ADDR"] = "Адрес";
$factarray["ADR1"] = "Адрес 1";
$factarray["ADR2"] = "Адрес 2";
$factarray["ADOP"] = "Усыновление";
$factarray["AFN"] 	= "Номер прародителя (положение в генеологическом древе)";
//$factarray["AFN";
$factarray["AGE"] = "Возраст";
$factarray["AGNC"] = "Учреждение";
$factarray["ALIA"] = "Иначе";
$factarray["ANCE"] = "Предки";
$factarray["ANCI"] = "Предки представляют интерес";
$factarray["ANUL"] = "Признание брака недействительным";
$factarray["ASSO"] = "Знакомые";
$factarray["AUTH"] = "Автор";
$factarray["BAPL"] = "Крещение у мормонов";
$factarray["BAPM"] = "Крешение";
$factarray["BARM"] = "Бар-мицва";
$factarray["BASM"] = "Бас-мицва";
$factarray["BIRT"] = "Рождение";
$factarray["BLES"] = "Благословение";
$factarray["BLOB"] = "Бинарный объект данных";
$factarray["BURI"] = "Захоронение";
$factarray["CALN"] = "Шифр";
$factarray["CAST"] = "Каста или сословие";
$factarray["CAUS"] = "Причина смерти";
$factarray["CENS"] = "Перепись";
$factarray["CHAN"] = "Последнее изменение";
$factarray["CHAR"] = "Кодировка";
$factarray["CHIL"] = "Ребенок";
$factarray["CHR"] = "Крещение";
$factarray["CHRA"] = "Крещение взрослого";
$factarray["CITY"] = "Населенный пункт";
$factarray["CONC"] = "Конкатенация";
$factarray["CONF"] = "Конфирмация";
$factarray["CONL"] = "Конфирмация у мормонов";
$factarray["CONT"] = "Продолжение";
$factarray["COPR"] = "Авторские права";
$factarray["CORP"] = "Фирма";
$factarray["CREM"] = "Кремация";
$factarray["CTRY"] = "Страна";
$factarray["DATA"] = "Данные";
$factarray["DATE"] = "Дата";
$factarray["DEAT"] = "Кончина";
$factarray["DESC"] = "Потомки";
$factarray["DESI"] = "Потомки представляют интерес";
$factarray["DEST"] = "Назначение";
$factarray["DIV"] = "Развод";
$factarray["DIVF"] = "Дело о разводе";
$factarray["DSCR"] = "Приметы";
$factarray["EDUC"] = "Образование";
$factarray["EMIG"] = "Эмиграция";
$factarray["ENDL"] = "LDS Endowment";
$factarray["ENGA"] = "Помолвка";
$factarray["EVEN"] = "Событие";
$factarray["FAM"] = "Семья";
$factarray["FAMC"] = "Семья в детстве";
$factarray["FAMF"] = "Семейный файл у мормонов";
$factarray["FAMS"] = "Семья в браке";
$factarray["FCOM"] = "Первое причастие";
$factarray["FILE"] = "Внешний файл";
$factarray["FORM"] = "Формат";
$factarray["GIVN"] = "Имя, отчество (имена)";
$factarray["GRAD"] = "Образование (диплом)";
$factarray["HEAD"] = "Заголовок";
$factarray["HUSB"] = "Супруг";
$factarray["IDNO"] = "Идентификационный номер";
$factarray["IMMI"] = "Иммиграция";
$factarray["LANG"] = "Язык";
$factarray["LEGA"] = "Наследник";
$factarray["MARB"] = "Объявление о браке";
$factarray["MARC"] = "Брачный контракт";
$factarray["MARL"] = "Разрешение на брак";
$factarray["MARR"] = "Брак";
$factarray["MARS"] = "Соглашение о браке";
$factarray["NAME"] = "Фамилия, имя, отчество (имена)";
$factarray["NATI"] = "Национальность";
$factarray["NATU"] = "Натурализация";
$factarray["NCHI"] = "Число детей";
$factarray["NICK"] = "Прозвище";
$factarray["NMR"] = "Число браков";
$factarray["NOTE"] = "Примечание";
$factarray["NPFX"] = "Титул (приставка)";
$factarray["NSFX"] = "Суффикс";
$factarray["OBJE"] = "Объект";
$factarray["OCCU"] = "Род занятий";
$factarray["ORDI"] = "Посвящение у мормонов";
$factarray["ORDN"] = "Посвящение в сан";
$factarray["PAGE"] = "Местонахождение цитаты";
$factarray["PEDI"] = "Восходящее древо";
$factarray["PLAC"] = "Место";
$factarray["PHON"] = "Телефон";
$factarray["POST"] = "Почтовый код";
$factarray["PROB"] = "Утверждение завещания";
$factarray["PROP"] = "Собственность";
$factarray["PUBL"] = "Публикация";
$factarray["QUAY"] = "Качество данных";
$factarray["REFN"] = "Ссылка";
$factarray["RELI"] = "Вероисповедание";
$factarray["REPO"] = "Архив";
$factarray["RESI"] = "Место проживания";
$factarray["RESN"] = "Ограниченный доступ";
$factarray["RETI"] = "Отставка";
$factarray["RFN"] = "Номер записи в файле";
$factarray["RIN"] = "Индентификационный номер";
$factarray["ROLE"] = "Роль";
$factarray["SEX"] = "Пол";
$factarray["SLGC"] = "LDS Child Sealing";
$factarray["SLGS"] = "LDS Spouse Sealing";
$factarray["SOUR"] = "Источник";
$factarray["SPFX"] = "Префикс к фамилии";
$factarray["SSN"] = "Номер социального страхования";
$factarray["STAE"] = "Штат";
$factarray["STAT"] = "Статус";
$factarray["SUBM"] = "Податель";
$factarray["SUBN"] = "Подача";
$factarray["SURN"] = "Фамилия";
$factarray["TEMP"] = "Храм мормонов";
$factarray["TEXT"] = "Текст";
$factarray["TIME"] = "Время";
$factarray["TITL"] = "Заглавие";
$factarray["TRLR"] = "Конец файла";
$factarray["TYPE"] = "Вид";
$factarray["VERS"] = "Версия";
$factarray["WIFE"] = "Супруга";
$factarray["WILL"] = "Завещание";
$factarray["_AKA"] = "Иначе";
$factarray["_BRTM"] 	= "Обрезание";
$factarray["_COML"] 	= "Законный брак";
$factarray["_EYEC"] 	= "Цвет глаз";
$factarray["_FNRL"] 	= "Захоронение";
$factarray["_HAIR"] 	= "Цвет волос";
$factarray["_HEIG"] 	= "Рост";
$factarray["_INTE"] 	= "Погребение в фамильном склепе";
$factarray["_MARI"] 	= "Помолвка";
$factarray["_MBON"] 	= "Оглашение вступающих в брак";
$factarray["_MEDC"] 	= "Медицинское состояние";
$factarray["_MILI"] 	= "Военная служба";
$factarray["_NMR"] 	= "Не женат/не замужем";
$factarray["_NLIV"] 	= "Кончина";
$factarray["_NMAR"] 	= "Никогда не состоял(а) в браке";
$factarray["_PRMN"] 	= "Постоянны";
$factarray["_SEPR"]	= "В разводе";
$factarray["_DETS"] 	= "Смерть супруга";
$factarray["CITN"] 	= "Гражданство";
$factarray["_FREL"] = "Родство по отцовской линии";
$factarray["_MREL"] = "Родство по материнской линии";
$factarray["_FA13"] = "Факт 13";
$factarray["_FA12"] = "Факт 12";
$factarray["_FA11"] = "Факт 11";
$factarray["_FA10"] = "Факт 10";
$factarray["_FA9"] = "Факт 9";
$factarray["_FA8"] = "Факт 8";
$factarray["_FA7"] = "Факт 7";
$factarray["_FA6"] = "Факт 6";
$factarray["_FA5"] = "Факт 5";
$factarray["_FA4"] = "Факт 4";
$factarray["_FA3"] = "Факт 3";
$factarray["_FA2"] = "Факт 2";
$factarray["_FA1"] = "Факт 1";
$factarray["_WEIG"] 	= "Вес";
$factarray["_YART"] 	= "Поминание умерших (в еврейской религии)";
$factarray["_EMAIL"] = "Адрес электронной почты (e-mail)";
$factarray["EMAIL"] 	= "Адрес электронной почты (e-mail)";
$factarray["_MARNM"] = "Фамилия в браке:";
$factarray["_TODO"] = "К исполнению";
$factarray["_UID"] = "Универсальный идентификатор";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]	= "Medical";
$factarray["_DEG"] 	= "Звание, чин";
$factarray["_MILT"] 	= "Военная служба";

// Other common customized facts
$factarray["_ADPF"]	= "Усыновлен/удочерена отцом";
$factarray["_ADPM"] 	= "Усыновлен/удочерена матерью";
$factarray["_AKAN"] 	= "Также известен как";

if (file_exists("languages/facts.ru.extra.php")) require "languages/facts.ru.extra.php";

?>

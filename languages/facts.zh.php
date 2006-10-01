<?php
/**
 * Chinese Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  PGV Development
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
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their chinese values
$factarray["ABBR"] = "簡稱";
$factarray["ADDR"] = "地址";
$factarray["ADR1"] = "地址一";
$factarray["ADR2"] = "地址二";
$factarray["ADOP"] = "收養";
$factarray["AFN"] = "祖先文件編號 (AFN)";
$factarray["AGE"] = "年齡";
$factarray["AGNC"] = "代辦處";
$factarray["ALIA"] = "別名";
$factarray["ANCE"] = "祖先";
$factarray["ANCI"] = "祖先興趣";
$factarray["ANUL"] = "取消";
$factarray["ASSO"] = "同事";
$factarray["AUTH"] = "作者";
$factarray["BAPL"] = "LDS 洗禮";
$factarray["BAPM"] = "洗禮";
$factarray["BARM"] = "Bar Mitzvah";
$factarray["BASM"] = "Bas Mitzvah";
$factarray["BIRT"] = "誕生";
$factarray["BLES"] = "祝福";
$factarray["BLOB"] = "二進制資料對象";
$factarray["BURI"] = "埋葬";
$factarray["CALN"] = "索書號";
$factarray["CAST"] = "世襲的社會等級/社會狀態";
$factarray["CAUS"] = "死因";
$factarray["CENS"] = "人口調查";
$factarray["CHAN"] = "前更改";
$factarray["CHAR"] = "字符集";
$factarray["CHIL"] = "子項";
$factarray["CHR"] = "洗禮";
$factarray["CHRA"] = "成人洗禮";
$factarray["CITY"] = "城市";
$factarray["CONF"] = "確認書";
$factarray["CONL"] = "LDS 確認書";
$factarray["COPR"] = "版權";
$factarray["CORP"] = "公司";
$factarray["CREM"] = "火葬";
$factarray["CTRY"] = "國家（地區）";
$factarray["DATA"] = "資料";
$factarray["DATE"] = "日期";
$factarray["DEAT"] = "死亡";
$factarray["DESC"] = "後裔";
$factarray["DESI"] = "後裔利息";
$factarray["DEST"] = "目的地";
$factarray["DIV"] = "離婚";
$factarray["DIVF"] = "離婚被歸檔";
$factarray["DSCR"] = "說明";
$factarray["EDUC"] = "教育";
$factarray["EMIG"] = "移出";
$factarray["ENDL"] = "LDS 捐贈";
$factarray["ENGA"] = "訂婚";
$factarray["EVEN"] = "活動";
$factarray["FAM"] = "系列";
$factarray["FAMC"] = "系列作為子項";
$factarray["FAMF"] = "系列文件";
$factarray["FAMS"] = "系列作為配偶";
$factarray["FCOM"] = "第一個聖餐";
$factarray["FILE"] = "外部文件:";
$factarray["FORM"] = "格式化:";
$factarray["GIVN"] = "指定的名字";
$factarray["GRAD"] = "畢業";
$factarray["IDNO"] = "標識號";
$factarray["IMMI"] = "移民";
$factarray["LEGA"] = "Legatee";
$factarray["MARB"] = "婚姻 Bann";
$factarray["MARC"] = "婚姻合同";
$factarray["MARL"] = "結婚證書";
$factarray["MARR"] = "婚姻";
$factarray["MARS"] = "婚姻結算";
$factarray["NAME"] = "名字";
$factarray["NATI"] = "國籍";
$factarray["NATU"] = "歸化";
$factarray["NCHI"] = "子項的編號";
$factarray["NICK"] = "昵稱";
$factarray["NMR"] = "婚姻的編號";
$factarray["NOTE"] = "附註";
$factarray["NPFX"] = "稱謂";
$factarray["NSFX"] = "後綴";
$factarray["OBJE"] = "多媒體對象";
$factarray["OCCU"] = "職業";
$factarray["ORDI"] = "法令";
$factarray["ORDN"] = "整理";
$factarray["PAGE"] = "引證詳細資料";
$factarray["PEDI"] = "家譜";
$factarray["PLAC"] = "安排";
$factarray["PHON"] = "電話";
$factarray["POST"] = "郵政編碼";
$factarray["PROB"] = "遺囑的認證";
$factarray["PROP"] = "屬性";
$factarray["PUBL"] = "發行";
$factarray["QUAY"] = "資料的質量";
$factarray["REPO"] = "程式庫";
$factarray["REFN"] = "參考編號";
$factarray["RELI"] = "宗教信仰";
$factarray["RESI"] = "住宅";
$factarray["RESN"] = "限制";
$factarray["RETI"] = "報廢";
$factarray["RFN"] = "記錄文件編號";
$factarray["RIN"] = "記錄身份證編號";
$factarray["ROLE"] = "角色";
$factarray["SEX"] = "性別";
$factarray["SLGC"] = "LDS 兒童海豹捕獵";
$factarray["SLGS"] = "LDS 配偶海豹捕獵";
$factarray["SOUR"] = "來源";
$factarray["SPFX"] = "姓氏稱謂";
$factarray["SSN"] = "社會安全號";
$factarray["STAE"] = "State";
$factarray["STAT"] = "狀態";
$factarray["SUBM"] = "提交者";
$factarray["SUBN"] = "提交";
$factarray["SURN"] = "姓氏";
$factarray["TEMP"] = "寺廟";
$factarray["TEXT"] = "文本";
$factarray["TIME"] = "時間";
$factarray["TITL"] = "稱謂";
$factarray["TYPE"] = "型";
$factarray["WILL"] = "意志";
$factarray["_EMAIL"] = "電子郵件";
$factarray["EMAIL"] = "電子郵件";
$factarray["_TODO"] = "做項目";
$factarray["_UID"] = "普遍標識";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"] = "醫療";
$factarray["_DEG"] = "程度";
$factarray["_MILT"] = "兵役";
$factarray["_SEPR"] = "分離";
$factarray["_DETS"] = "一個配偶死亡";
$factarray["CITN"] = "公民身份";

// Other common customized facts
$factarray["_ADPF"] = "由父親採取";
$factarray["_ADPM"] = "由母親採取";
$factarray["_AKAN"] = "亦稱";
$factarray["_BRTM"] = "Brit mila";
$factarray["_COML"] = "普通法婚姻";
$factarray["_EYEC"] = "眼睛顏色";
$factarray["_FNRL"] = "葬禮";
$factarray["_HAIR"] = "頭髮顏色";
$factarray["_HEIG"] = "高度";
$factarray["_INTE"] = "Interred";
$factarray["_MARI"] = "Marriage intention";
$factarray["_MBON"] = "Marriage bond";
$factarray["_MEDC"] = "Medical condition";
$factarray["_MILI"] = "軍事";
$factarray["_NMR"] = "Not married";
$factarray["_NLIV"] = "Not living";
$factarray["_NMAR"] = "Never married";
$factarray["_PRMN"] = "Permanent Number";
$factarray["_WEIG"] = "Weight";
$factarray["_YART"] = "Yartzeit";

if (file_exists("languages/facts.zh.extra.php")) require "languages/facts.zh.extra.php";

?>
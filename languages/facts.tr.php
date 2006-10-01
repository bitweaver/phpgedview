<?php
/**
 * Turkish Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Kurt Norgaz
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
 * @author Kurt Norgaz
 * @version $Id$
 */
if (preg_match("/facts\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}
// -- Define a fact array to map Gedcom tags with their Turkish values
$factarray["ABBR"]	= "Kısaltma";
$factarray["ADDR"]	= "Adres";
$factarray["ADR1"]	= "1. adres";
$factarray["ADR2"]	= "2. adres";
$factarray["ADOP"]	= "Evlât edinme";
$factarray["AGE"]	= "Yaşı";
$factarray["ALIA"]	= "Takma adı";
$factarray["ANCE"]	= "Geçmişler / Atalar";
$factarray["AUTH"]	= "Yazar";
$factarray["BAPL"]	= "LDS vaftizi";
$factarray["BAPM"]	= "Vaftiz";
$factarray["BIRT"]	= "Doğum";
$factarray["BLES"]	= "Kutsama";
$factarray["BLOB"]	= "'Binary' veri nesnesi";
$factarray["BURI"]	= "Defin";
$factarray["CALN"]	= "Telefon numarası";
$factarray["CAUS"]	= "Vefat nedeni";
$factarray["CENS"]	= "Nüfus sayımı";
$factarray["CHAN"]	= "Son değişiklik";
$factarray["CHAR"]	= "Karakter kümesi";
$factarray["CHIL"]	= "Çocuk";
$factarray["CHR"]	= "Vaftiz";
$factarray["CHRA"]	= "Erişkin vaftizi";
$factarray["CITY"]	= "Şehir";
$factarray["CONF"]	= "Protestanlikta<br />Kiliseye kabul edinme ayini";
$factarray["CONL"]	= "LDS kilisesine kabul edinme ayini";
$factarray["COPR"]	= "Telif hakkı";
$factarray["CORP"]	= "Şirket / Kuruluş";
$factarray["CREM"]	= "Ölüyü yakma";
$factarray["CTRY"]	= "Ülke";
$factarray["DATA"]	= "Veri";
$factarray["DATE"]	= "Tarih";
$factarray["DEAT"]	= "Vefat";
$factarray["DESC"]	= "Soyundan gelen şahıslar";
$factarray["DIV"]	= "Boşanma";
$factarray["DIVF"]	= "Boşanma dosyası";
$factarray["DSCR"]	= "Tarif";
$factarray["EDUC"]	= "Eğitim";
$factarray["EMIG"]	= "İçinden göç ettiği ülke";
$factarray["ENGA"]	= "Nişan";
$factarray["EVEN"]	= "Hadise";
$factarray["FAM"]	= "Aile";
$factarray["FAMC"]	= "Çocuk olarak aile";
$factarray["FAMF"]	= "Aile dosyası";
$factarray["FAMS"]	= "Eş olarak aile";
$factarray["FCOM"]	= "Hrist. İlk komünyon";
$factarray["FILE"]	= "Dış dosya";
$factarray["FORM"]	= "Biçim";
$factarray["GIVN"]	= "Verilen isim(ler)";
$factarray["GRAD"]	= "Mezuniyet";
$factarray["IDNO"]	= "GEDCOM varitabanı içindeki tanıtım numarası";
$factarray["IMMI"]	= "İçine göç ettiği ülke";
$factarray["LEGA"]	= "Vasiyette kendisine mal bırakılan kişi";
$factarray["MARC"]	= "Evlilik kontratı";
$factarray["MARR"]	= "Nikâh";
$factarray["MEDI"]	= "Multimedya türü";
$factarray["NAME"]	= "Soy isim";
$factarray["NATI"]	= "Vatandaşlık";
$factarray["NATU"]	= "Yeni yurttaşlık";
$factarray["NICK"]	= "Lakabı";
$factarray["NCHI"]	= "Çocuk sayısı";
$factarray["NMR"]	= "Evlilik sayısı";
$factarray["NOTE"]	= "Not";
$factarray["NPFX"]	= "İsmin önüne konulan unvan";
$factarray["NSFX"]	= "İsmin sonuna konan ek";
$factarray["OBJE"]	= "Multimedya nesnesi";
$factarray["OCCU"]	= "Meslek";
$factarray["PEDI"]	= "Soyağacı / Seçere";
$factarray["PLAC"]	= "Yer";
$factarray["PHON"]	= "Telefon";
$factarray["POST"]	= "Posta kodu";
$factarray["PROP"]	= "Mülkiyet";
$factarray["PUBL"]	= "Yayın";
$factarray["QUAY"]	= "Verinin kalitesi";
$factarray["REPO"]	= "Depolanan yer";
$factarray["REFN"]	= "Kaynak numarası";
$factarray["RELA"]	= "Akrabalık";
$factarray["RELI"]	= "Din";
$factarray["RESI"]	= "Ev";
$factarray["RESN"]	= "Sınırlama";
$factarray["RETI"]	= "Emeklilik";
$factarray["RFN"]	= "Kayıt dosya numarası";
$factarray["RIN"]	= "Kayıt kişisel numarası";
$factarray["SEX"]	= "Cinsiyet";
$factarray["SOUR"]	= "Kaynak";
$factarray["SPFX"]	= "Soyismin sonuna konan ek";
$factarray["SSN"]	= "Sosyal sigorta numarası";
$factarray["STAE"]	= "Durum";
$factarray["SUBM"]	= "Gönderen";
$factarray["SURN"]	= "Soy isim";
$factarray["TEMP"]	= "Tapınak";
$factarray["TEXT"]	= "Metin";
$factarray["TIME"]	= "Saat";
$factarray["TYPE"]	= "Tür / Tip";
$factarray["WILL"]	= "Vasiyetname";
$factarray["_EMAIL"]	= "E-posta adresi";
$factarray["EMAIL"]	= "E-posta adresi:";
$factarray["_TODO"]	= "Yapılması gereken iş";
$factarray["_UID"]	= "Üniversel tanıtıcı";
$factarray["_PGVU"]	= "Son değişikliği yapan";
$factarray["_PRIM"]	= "Tercih edilen fotoğraf";
$factarray["_THUM"]	= "Bu fotoğrafı tırnak resim olarak kullan?";

// These facts are specific to gedcom exports from Family Tree Maker
$factarray["_MDCL"]	= "Sağlıksal bilgiler";
$factarray["_MILT"]	= "Askerlik görevi";
$factarray["_SEPR"]	= "Ayrılmış";
$factarray["_WEIG"]	= "Ağırlık";
$factarray["_DETS"]	= "Bir eşin vefatı";
$factarray["CITN"]	= "Vatandaşlık";
$factarray["_FA1"]	= "1. Hadise";
$factarray["_FA2"]	= "2. Hadise";
$factarray["_FA3"]	= "3. Hadise";
$factarray["_FA4"]	= "4. Hadise";
$factarray["_FA5"]	= "5. Hadise";
$factarray["_FA6"]	= "6. Hadise";
$factarray["_FA7"]	= "7. Hadise";
$factarray["_FA8"]	= "8. Hadise";
$factarray["_FA9"]	= "9. Hadise";
$factarray["_FA10"]	= "10. Hadise";
$factarray["_FA11"]	= "11. Hadise";
$factarray["_FA12"]	= "12. Hadise";
$factarray["_FA13"]	= "13. Hadise";
$factarray["_MREL"]	= "Anne'ye akrabalık derecesi";
$factarray["_FREL"]	= "Baba'ya akrabalık derecesi";
$factarray["WWW"]	= "İnternet sitesi";
$factarray["FONE"]	= "Fonetik";
$factarray["_HEB"]	= "İbranice";
$factarray["_BIBL"]	= "Kaynakça";

// Other common customized facts
$factarray["_ADPF"]	= "Baba tarafından evlât edindi";
$factarray["_ADPM"]	= "Anne tarafından evlât edindi";
$factarray["_AKAN"]	= "Bu isimle de tanınıyor";
$factarray["_AKA"]	= "Bu isimle de tanınıyor";
$factarray["_EYEC"]	= "Göz rengi";
$factarray["_FNRL"]	= "Cenaze töreni";
$factarray["_HAIR"]	= "Saç rengi";
$factarray["_HEIG"]	= "Boy";
$factarray["_INTE"]	= "Defin etme";
$factarray["_MEDC"]	= "Sağlıksal durumu";
$factarray["_MILI"]	= "Askerlik";
$factarray["_NMR"]	= "Evli değil";
$factarray["_NLIV"]	= "Yaşamıyor";
$factarray["_NMAR"]	= "Hiçbir zaman evli değildi";
$factarray["_PRMN"]	= "Kalıcı numarası";
$factarray["_MARNM"]	= "Evlilik soyismi";
$factarray["_STAT"]	= "Evlilik durumu";
$factarray["COMM"]	= "Açıklama";

if (file_exists( "languages/facts.tr.extra.php")) require  "languages/facts.tr.extra.php";

?>
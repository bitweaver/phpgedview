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
if (preg_match("/lang\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

$pgv_lang["continue_import2"]		= "Tarih verilerini okumaya devam et";
$pgv_lang["importing_dates"]		= "Tarih verileri okunuyor";
$pgv_lang["changelog"]			= "v#VERSION# sürümündeki değişiklikler";
$pgv_lang["none"]					= "Hiç biri";
$pgv_lang["ahnentafel_report"]		= "Soy ağacı tablosu raporu";
$pgv_lang["choose_relatives"]		= "Akrabaları seçin";
$pgv_lang["relatives_report"]		= "Akrabalık raporu";
$pgv_lang["total_living"]			= "Yaşayanların sayısı";
$pgv_lang["total_dead"]				= "Ölülerin sayısı";
$pgv_lang["total_not_born"]			= "Doğmamışların sayısı";
$pgv_lang["download_zipped"]		= "GEDCOM ZİP-dosyası olarak indirilsin mi?";
$pgv_lang["remember_me"]		= "Beni bu bilgisayarda hatırla";
$pgv_lang["add_unlinked_person"]	= "Yeni şahıs ekle";
$pgv_lang["support_contact"]		= "Teknik yardım ilişkisi";
$pgv_lang["other"]			= "Çeşitli";
$pgv_lang["total_names"]			= "Toplam isim sayısı";
$pgv_lang["show_spouses"]		= "Eşleri göster";
$pgv_lang["quick_update_title"]		= "Hızlı güncelleme";
$pgv_lang["update_fact"]		= "Hadiseyi güncelle";
$pgv_lang["update_photo"]		= "Fotoğrafı güncelle";
$pgv_lang["photo_replace"]		= "Bu fotoğrafı eski bir fotoğraf yerine mi koymak istiyorsunuz?";
$pgv_lang["select_fact"]		= "Hadise seç...";
$pgv_lang["update_address"]		= "Adresi güncelle";
$pgv_lang["add_new_chil"]		= "Yeni çocuk ekle";
$pgv_lang["user_default_tab"]		= "Şahısların bilgileri sayfasında gösterilecek ilk sekme";
$pgv_lang["indis_charts"]		= "Şahsi seçenekler";
$pgv_lang["locked"]					= "Değiştirme";
$pgv_lang["privacy"]				= "Mahremiyet";

//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]				= "?";
$pgv_lang["page_help"]			= "Yardım";
$pgv_lang["help_for_this_page"]		= "Bu sayfa üzerine yardım";
$pgv_lang["help_contents"]		= "Yardım içeriği";
$pgv_lang["show_context_help"]		= "Bağlamsal yardımı GÖSTER";
$pgv_lang["hide_context_help"]		= "Bağlamsal yardımı SAKLA";
$pgv_lang["sorry"]			= "<b>Ne yazık ki bu sayfa ya da bölümün yardım mesajı daha tamamlanmamıştır</b>";
$pgv_lang["help_not_exist"]		= "<b>Ne yazık ki bu sayfanın ya da bu bölümün yardım mesajı daha işlenmemiştir</b>";
$pgv_lang["resolution"]			= "Ekran çözünürlüğü";
$pgv_lang["menu"]			= "Seçenek listesi";
$pgv_lang["header"]			= "Başlık";
$pgv_lang["login_head"]			= "PhpGedView üye girişi";

//-- CONFIG FILE MESSAGES
$pgv_lang["error_title"]		= "HATA: GEDCOM dosyası açılamıyor";
$pgv_lang["error_header"]		= "[#GEDCOM#], isimli GEDCOM dosyası, belirlenen yerde bulunamadı.";
$pgv_lang["error_header_write"] 	= "[#GEDCOM#] isimli GEDCOM dosyasına yazma izni yoktur. Check attributes and access rights.";
$pgv_lang["for_support"]		= "Teknik yardım ve bilgi için danışabileceğiniz ilişki adresi:";
$pgv_lang["for_contact"]		= "Seçere hakkında yardım için danışabileceğiniz ilişki adresi:";
$pgv_lang["for_all_contact"]		= "Teknik yardım veya seçere ile ilgili sorular için danışabileceğiniz ilişki adresi:";
$pgv_lang["build_title"]		= "İndeks dosyaları inşa ediliyor";
$pgv_lang["build_error"]		= "GEDCOM dosyası güncelleştirildi.";
$pgv_lang["please_wait"]		= "Lütfen biraz bekleyin. İndeks dosyaları yeniden yaratılmaktadır.";
$pgv_lang["choose_gedcom"]		= "Bir GEDCOM veritabanını seç";
$pgv_lang["username"]			= "Rumuz";
$pgv_lang["invalid_username"]		= "Rumuz içinde geçersiz harfler bulunmaktadır";
$pgv_lang["fullname"]			= "Komple isim";
$pgv_lang["password"]			= "Şifre";
$pgv_lang["confirm"]			= "Şifre tekrarlaması";
$pgv_lang["user_contact_method"]	= "Tercih edilen ilişki yöntemi";
$pgv_lang["login"]			= "Giriş";
$pgv_lang["login_aut"]			= "Üye verilerini işle";
$pgv_lang["logout"]			= "Çıkış";
$pgv_lang["admin"]			= "İdare / Ayarlar";
$pgv_lang["logged_in_as"]		= "Giriş rumuzu";
$pgv_lang["my_pedigree"]		= "Benim soyağacım";
$pgv_lang["my_indi"]			= "Şahsi sayfam";
$pgv_lang["yes"]			= "Evet";
$pgv_lang["no"]				= "Hayır";
$pgv_lang["add_gedcom"]			= "GEDCOM ekle";
$pgv_lang["no_support"]			= "Sizin tarayıcınızın bu PhpGedView sitesi için gerekli olan standartları desteklemediği tespit edildi. Çoğu tarayıcılar en yeni sürümlerinde bu standartları desteklemektedirler. Bunun için tarayıcınızı güncelleştirmenizi önermek isteriz.";
$pgv_lang["change_theme"]		= "Temayı değiştir ";
$pgv_lang["gedcom_downloadable"]	= "Bu GEDECOM dosyası İnternet üzerinden indirilebilinir!<br />Lütfen <a href=\"readme.txt\">readme.txt</a> dosyasının \"SECURITY\" bölümünü okuyup bu sorunu ortadan kaldırın.";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]		= "Soy ağacı - Seçere";
$pgv_lang["gen_ped_chart"]		= "#PEDIGREE_GENERATIONS# nesil - Soy ağacı - Seçere görüntüsü";
$pgv_lang["generations"]		= "Gösterilen nesil sayısı";
$pgv_lang["view"]			= "Göster";
$pgv_lang["fam_spouse"]			= "Eş ve aile";
$pgv_lang["root_person"]		= "Kök şahıs numarası";
$pgv_lang["hide_details"]		= "Detayları sakla";
$pgv_lang["show_details"]		= "Detayları göster";
$pgv_lang["person_links"]		= "Bu şahısın çizgelerine, ailesine ve yakın akrabalarına ulaşan bağlantılar. Bu simgeye tıklayıp şahıs ile ilgili sayfaları izleyin.";
$pgv_lang["zoom_box"]			= "Bu kutuyu büyüt / küçült";
$pgv_lang["portrait"]			= "Düşey";
$pgv_lang["landscape"]			= "Yatay";
$pgv_lang["start_at_parents"]		= "Anne ve baba ile başla";
$pgv_lang["charts"]			= "Çizgeler";
$pgv_lang["lists"]			= "Listeler";
$pgv_lang["welcome_page"]		= "Karşılama sayfası";
$pgv_lang["box_width"]			= "Kutu eni";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]	= "Bu numaraya bağlı aile bulunamadı";
$pgv_lang["unable_to_find_indi"]	= "Bu numaraya bağlı şahıs bulunamadı";
$pgv_lang["unable_to_find_record"]	= "Bu numaraya bağlı kayıt bulunamadı";
$pgv_lang["unable_to_find_source"]	= "Bu numaraya bağlı kaynak bulunamadı";
$pgv_lang["unable_to_find_repo"]	= "Bu numaraya bağlı veri tabanı tablosu bulunamadı";
$pgv_lang["repo_name"]			= "Veri tabanı tablosunun ismi:";
$pgv_lang["address"]			= "Adres:";
$pgv_lang["phone"]			= "Telefon:";
$pgv_lang["source_name"]		= "Kaynağın ismi:";
$pgv_lang["title"]			= "Resmi sıfat";
$pgv_lang["author"]			= "Yazar:";
$pgv_lang["publication"]		= "Yayın:";
$pgv_lang["call_number"]		= "Telefon numarası:";
$pgv_lang["living"]			= "Yaşıyor";
$pgv_lang["private"]			= "Özel";
$pgv_lang["birth"]			= "Doğum:";
$pgv_lang["death"]			= "Vefat:";
$pgv_lang["descend_chart"]		= "Şahsı izleyen nesiller çizgesi";
$pgv_lang["individual_list"]		= "Şahıs listesi";
$pgv_lang["family_list"]		= "Aile listesi";
$pgv_lang["source_list"]		= "Kaynak listesi";
$pgv_lang["place_list"]			= "Yer listesi";
$pgv_lang["media_list"]			= "Mültimedya listesi";
$pgv_lang["search"]			= "Arama";
$pgv_lang["clippings_cart"]		= "Parça toplama mahfazası";
$pgv_lang["not_an_array"]		= "Dizi (array) değildir";
$pgv_lang["print_preview"]		= "Baskı ön izleme";
$pgv_lang["cancel_preview"]		= "Normal görüntüye geri dön";
$pgv_lang["change_lang"]		= "Dil seçimi";
$pgv_lang["print"]			= "Yazdır";
$pgv_lang["total_queries"]		= "Veritabanına sorma sayısı: ";
$pgv_lang["total_privacy_checks"]	= "Toplam mahremiyet denetlemeleri: ";
$pgv_lang["back"]			= "geri dön";
$pgv_lang["privacy_list_indi_error"]	= "Mahremiyet kuralları yüzünden bazı şahısların detayları gizli tutulmaktadır.";
$pgv_lang["privacy_list_fam_error"]	= "Mahremiyet kuralları yüzünden bazı ailelerin detayları gizli tutulmaktadır.";
$pgv_lang["aka"]			= "Tanındığı diğer isimler";

//-- INDIVIDUAL FILE MESSAGES
$pgv_lang["male"]			= "erkek";
$pgv_lang["female"]			= "kadın";
$pgv_lang["temple"]			= "LDS Temple";
$pgv_lang["temple_code"]		= "LDS Temple kodu:";
$pgv_lang["status"]			= "Durumu";
$pgv_lang["source"]			= "Kaynak";
$pgv_lang["citation"]			= "Bölüm:";
$pgv_lang["text"]			= "Kaynak metin:";
$pgv_lang["note"]			= "Not";
$pgv_lang["NN"]				= "Soy isimsiz";
$pgv_lang["PN"]				= "İsimsiz";
$pgv_lang["unrecognized_code"]		= "Tanınmayan GEDCOM kodu";
$pgv_lang["unrecognized_code_msg"]	= "Bu düzeltmek istediğimiz bir hatadır. Bu hatayı lütfen bu adrese bildirin:";
$pgv_lang["indi_info"]			= "Kişisel bilgi";
$pgv_lang["pedigree_chart"]		= "Soy ağacı çizgesi";
$pgv_lang["desc_chart2"]		= "Şahsı izleyen nesillerin görüntüsü";
$pgv_lang["family"]			= "Aile";
$pgv_lang["as_spouse"]			= "Aile tablosu (eş olarak)";
$pgv_lang["as_child"]			= "Aile tablosu (çocuk olarak)";
$pgv_lang["view_gedcom"]		= "GEDCOM kayıtını göster";
$pgv_lang["add_to_cart"]		= "Parça toplama mahfazasına kat";
$pgv_lang["still_living_error"]		= "Bu şahıs daha yaşamaktadır veyahut doğum ya da ölüm tarihleri eksiktir.<br />Burada yaşayan şahıslar hakkında hiçbir ayrıntı gösterilmez.<br />Ayrıntılı bilgi için bu adres ile temasa geçin:";
$pgv_lang["privacy_error"]		= "Bu şahısın detayları şahsidir.<br />Ayrıntılı bilgi için bu adres ile temasa geçin:";
$pgv_lang["more_information"]		= "Ayrıntılı bilgi için danışabileceğiniz ilişki adresi:";
$pgv_lang["name"]			= "İsim";
$pgv_lang["given_name"]			= "İsim:";
$pgv_lang["surname"]			= "Soy isim:";
$pgv_lang["suffix"]			= "Ek isim:";
$pgv_lang["object_note"]		= "Nesne notu:";
$pgv_lang["sex"]			= "Cinsiyet";
$pgv_lang["personal_facts"]		= "Kişisel bilgiler ve detaylar";
$pgv_lang["type"]			= "Tip";
$pgv_lang["date"]			= "Tarih";
$pgv_lang["place_description"]		= "Yer / Tarif";
$pgv_lang["parents"]			= "Anne ve baba:";
$pgv_lang["siblings"] 			= "Kardeşleri";
$pgv_lang["father"]			= "Baba";
$pgv_lang["mother"]			= "Anne";
$pgv_lang["relatives"]			= "Yakın akrabalar";
$pgv_lang["child"]			= "Çocuk";
$pgv_lang["surnames"]			= "Soy isimleri";
$pgv_lang["adopted"]			= "Evlatlık edinme";
$pgv_lang["foster"]			= "Babalık / Analık";
$pgv_lang["no_tab1"]				= "Bu şahıs ile ilgili hiç bir hadise bulunmamaktadır.";
$pgv_lang["no_tab2"]				= "Bu şahıs ile ilgili hiç bir not bulunmamaktadır.";
$pgv_lang["no_tab4"]				= "Bu şahıs ile ilgili hiç bir mültimedya nesnesi bulunmamaktadır.";
$pgv_lang["no_tab5"]				= "Bu şahıs ile ilgili hiç bir yakın akraba bulunmamaktadır.";
$pgv_lang["spouse"]			= "Eşi";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]		= "Aile bilgisi";
$pgv_lang["family_group_info"]		= "Aile gurubu üzerine bilgi";
$pgv_lang["husband"]			= "Koca";
$pgv_lang["wife"]			= "Karı";
$pgv_lang["marriage"]			= "Evlilik:";
$pgv_lang["marriage_license"]		= "Evlilik izini:";
$pgv_lang["media_object"]		= "Mültimedya nesnesi:";
$pgv_lang["children"]			= "Çocuklar";
$pgv_lang["no_children"]		= "Kayıt edilmiş çocuk yok";
$pgv_lang["parents_timeline"]		= "Çifti zaman çizgisinde göster";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]			= "Parça toplama mahfazası";
$pgv_lang["clip_explaination"]		= "Parça toplama mahfazası bu seçereden istediğiniz &quot;parçaları&quot işaretleyip sonradan işaretlediğiniz tüm parçaları özel bir GEDCOM dosyası halinde indirmeniz içindir.<br /><br />";
$pgv_lang["item_with_id"]		= "ID üzerine bilgi";
$pgv_lang["error_already"]		= "Parça toplama mahfazasının içindedir.";
$pgv_lang["which_links"]		= "Bu aile ile ilgili hangi bağlantıyı eklemek istiyorsunuz?";
$pgv_lang["just_family"]		= "Sadece bu aile kayıtını ekle.";
$pgv_lang["parents_and_family"]		= "Anne ve babayı bu aile kayıtı ile ekle.";
$pgv_lang["parents_and_child"]		= "Bu şahsı, annesini, babasını ve çocuklarını ekle.";
$pgv_lang["parents_desc"]		= "Bu şahsı, annesini, babasını ve onu izleyen tüm şahısları ekleyin.";
$pgv_lang["continue"]			= "Eklemeye devam et";
$pgv_lang["which_p_links"]		= "Bu şahısla ilgili hangi bağlantıyı eklemek istiyorsunuz?";
$pgv_lang["just_person"]		= "Bu şahsı ekle.";
$pgv_lang["person_parents_sibs"]	= "Bu şahsı, annesini, babasını ve kardeşlerini ekle.";
$pgv_lang["person_ancestors"]		= "Bu şahsı ve direk öncelerini ekle.";
$pgv_lang["person_ancestor_fams"]	= "Bu şahsı, direk öncelerini ve ailesini ekle.";
$pgv_lang["person_spouse"]		= "Bu şahsı, eşlerini ve çocuklarını ekle.";
$pgv_lang["person_desc"]		= "Bu şahsı, eşlerini ve onu izleyen tüm şahısları ekle.";
$pgv_lang["unable_to_open"]		= "Parça toplama mahfazası yazmak için açılamadı.";
$pgv_lang["person_living"]		= "Bu şahıs daha yaşamaktadır veyahut şahısın doğum ya da ölüm tarihleri eksiktir.<br />Burada yaşayan şahıslar hakkında hiçbir ayrıntı gösterilmez.";
$pgv_lang["person_private"]		= "Bu şahısın detayları şahsidir. Kişisel detaylar eklenmeyecektir.";
$pgv_lang["family_private"]		= "Bu ailenin detayları şahsidir. Kişisel detaylar eklenmeyecektir.";
$pgv_lang["download"]			= "Yeni yaratılmış GEDCOM dosyasını yerel bilgisayarınıza indirebilmek için aşağıdaki bağlantıya farenin sağ tuşu ile basıp (MAC te Control-Click) &quot;Save target as&quot; emrini seçin.";
$pgv_lang["media_files"]		= "Medya dosyaları ile ilişkide bulunan GEDCOM";
$pgv_lang["cart_is_empty"]		= "Parça toplama mahfazasınız boştur.";
$pgv_lang["id"]				= "ID";
$pgv_lang["name_description"]		= "İsim / Tarif";
$pgv_lang["remove"]			= "Sil";
$pgv_lang["empty_cart"]			= "Parça toplama mahfazasının boşalt";
$pgv_lang["download_now"]		= "Şimdi indir";
$pgv_lang["indi_downloaded_from"]	= "Şahısın bilgileri buradan indirildi:";
$pgv_lang["family_downloaded_from"]	= "Ailenin bilgileri buradan indirildi:";
$pgv_lang["source_downloaded_from"]	= "Kaynağın bilgileri buradan indirildi:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]		= "Yer bağlantısı bulundu<br />Şimdi sonuçlara bak";
$pgv_lang["top_level"]			= "geri dön";
$pgv_lang["form"]			= "Yerlerin sıralanma şekli:";
$pgv_lang["default_form"]		= "Kasaba, İlçe, İl, Ülke";
$pgv_lang["default_form_info"]		= "(Varsayılan / Default)";
$pgv_lang["unknown"]			= "Soy isim yok";
$pgv_lang["individuals"]		= "Şahıslar";
$pgv_lang["view_records_in_place"]	= "Bu yer ile ilgili tüm kayıtları göster";
$pgv_lang["place_list2"]		= "Yer listesi";
$pgv_lang["show_place_list"]		= "Tüm yerleri tek liste halinde göster";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["multi_title"]		= "Mültimedya nesne listesi";
$pgv_lang["media_found"]		= "mültimedya nesnesi bulundu";
$pgv_lang["view_person"]		= "Şahsı göster";
$pgv_lang["view_family"]		= "Aileyi göster";
$pgv_lang["view_source"]		= "Kaynağı göster";
$pgv_lang["prev"]			= "&lt; bir sayfa geri dön";
$pgv_lang["next"]			= "bir sayfa ilerle &gt;";
$pgv_lang["file_not_found"]		= "Dosya bulunamadı";
$pgv_lang["medialist_show"] 		= "Göster";
$pgv_lang["per_page"]			= "mültimedya nesnesini bir sayfada";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]		= "GEDCOM dosyası içinde arama";
$pgv_lang["enter_terms"]		= "Aranan terimleri ekle";
$pgv_lang["soundex_search"]		= "- Ya da isimin yazıldığını düşündüğünüz şekilde arayın (Soundex):";
$pgv_lang["sources"]			= "Kaynaklar";
$pgv_lang["firstname_search"]		= "İsim";
$pgv_lang["lastname_search"]		= "Soy isim";
$pgv_lang["search_place"]		= "Yer";
$pgv_lang["search_year"]		= "Sene";
$pgv_lang["no_results"]			= "Hiçbir sonuç bulunmadı.";
$pgv_lang["invalid_search_input"]	= "Lütfen sene ile beraber bir isim, soy isim ya da bir yerin ismini ekleyin";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]		= "Kaynak bulundu";
$pgv_lang["titles_found"]			= "Başlıklar";

//-- REPOLIST FILE MESSAGES
$pgv_lang["repo_list"]			= "Veri havuzu listesi";
$pgv_lang["repos_found"]		= "Veri havuzları bulundu";


//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]		= "Kaynak bilgisi";
$pgv_lang["other_records"]		= "Bu kaynakla ilişkide olan diğer kayıtlar:";
$pgv_lang["people"]			= "Şahıslar";
$pgv_lang["families"]			= "Aileler";
$pgv_lang["total_sources"]		= "Toplam kaynak sayısı";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]		= "Şahıs ve aile indeksini geliştiriyorum";
$pgv_lang["building_index"]		= "İndeks listelerini geliştiriyorum";
$pgv_lang["invalid_gedformat"]		= "Hatalı GEDCOM 5.5 biçimi";
$pgv_lang["importing_records"]		= "Kayıtları veritabanına işliyorum";
$pgv_lang["detected_change"]		= "PhpGedView \$GEDCOM isimli GEDCOM dosyasının değiştiğini fark etti. Devam etmeden önce indeks dosyalarının yenilenmesi lazım.";
$pgv_lang["please_be_patient"]		= "LÜTFEN BİRAZ SABIRLI OLUN";
$pgv_lang["reading_file"]		= "GEDCOM dosyası okunuyor";
$pgv_lang["flushing"]			= "İçerikleri siliyorum";
$pgv_lang["found_record"]		= "Bulunan kayıt";
$pgv_lang["exec_time"]			= "Toplam işletim süresi:";
$pgv_lang["unable_to_create_index"]	= "İndeks dosyasını yenileyemedim. PhpGedView dizinine yazma izninizin olup olmadığını denetleyin. İzinler indeks dosyalarının yaratılmasından sonra eski duruma geri getirilebilinir.";
$pgv_lang["indi_complete"]		= "Şahısların indeks dosyası yenilendi.";
$pgv_lang["family_complete"]		= "Ailelerin indeks dosyası yenilendi.";
$pgv_lang["source_complete"]		= "Kaynakların indeks dosyası yenilendi.";
$pgv_lang["tables_exist"]		= "PhpGedView tabloları veri tabanında vardı";
$pgv_lang["you_may"]			= "Seçenekleriniz:";
$pgv_lang["drop_tables"]		= "Aktüel sql-table ları silin";
$pgv_lang["import_multiple"]		= "Çeşitli GEDCOM-Dosyalarını ithal edip devam edin";
$pgv_lang["path_to_gedcom"]		= "GEDCOM-Dosyanızın yerini belirleyin:";
$pgv_lang["dataset_exists"]		= "Veri tabanına bu isim altında başka bir GEDCOM-Dosyası ithal edilmiştir.";
$pgv_lang["empty_dataset"]		= "Veri kümesini hakikatten silmek istiyor musunuz?";
$pgv_lang["index_complete"]		= "İndeks tamamlandı.";
$pgv_lang["click_here_to_go_to_pedigree_tree"]	= "Soy ağacı - seçere tablosuna ulaşmak için buraya tıklayın";
$pgv_lang["import_complete"]		= "İçerik aktarımı tamamlandı";
$pgv_lang["updating_family_names"]	= "Aile soy isimleri FAM için güncelleştiriliyor ";
$pgv_lang["addmedia"]			= "Medya ekleme aracı";
$pgv_lang["dateconvert"]		= "Tarihleri dönüştürme aracı";
$pgv_lang["xreftorin"]			= "XREF İD'lerini RİN numarasına dönüştür";
$pgv_lang["tools_readme"]		= "Ayrıntılı bilgi için #README.TXT# dosyasının \"tools\" bölümünü okuyun.";
$pgv_lang["sec"]			= "saniye";
$pgv_lang["bytes_read"]			= "Okunan byte miktarı:";
$pgv_lang["import_progress"]		= "İthal gelişimi...";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"]			= "Toplam aile sayısı";
$pgv_lang["total_indis"]		= "Toplam şahıs sayısı";
$pgv_lang["starts_with"]		= "Bununla başla:";
$pgv_lang["person_list"]		= "Şahıs listesi:";
$pgv_lang["paste_person"]		= "Şahsı ekle";
$pgv_lang["notes_sources_media"]	= "Notlar, Kaynaklar, Medya-Dosyaları";
$pgv_lang["notes"]			= "Notlar";
$pgv_lang["ssourcess"]			= "Kaynaklar";
$pgv_lang["media"]			= "Mültimedya";
$pgv_lang["name_contains"]		= "İsim içeriği:";
$pgv_lang["filter"]			= "Filtre";
$pgv_lang["find_sourceid"]		= "Kaynak no'sunu ara";
$pgv_lang["find_individual"]		= "Şahıs arama listesi";
$pgv_lang["find_familyid"]		= "Aile numarasını ara";
$pgv_lang["skip_surnames"]		= "Soy isim listesini sakla";
$pgv_lang["show_surnames"]		= "Soy isim listesini göster";
$pgv_lang["all"]			= "HEPSİ";
$pgv_lang["hidden"]			= "Saklı";
$pgv_lang["confidential"]		= "Özel";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]			= "Yaş";
$pgv_lang["timeline_title"]		= "PhpGedView zaman çizgisi";
$pgv_lang["timeline_chart"]		= "Zaman çizgisi çizimi";
$pgv_lang["remove_person"]		= "Şahsı sil";
$pgv_lang["show_age"]			= "Yaşını göster";
$pgv_lang["add_another"]		= "Yeni bir şahsı çizime ekle:<br />Şahıs numarası:";
$pgv_lang["find_id"]			= "Numarayı ara";
$pgv_lang["show"]			= "Göster";
$pgv_lang["year"]			= "Sene:";
$pgv_lang["timeline_instructions"]	= "Yeni tarayıcıların çoğunda tıklayarak veyahut kutuları çekerek görüntünün bir yerinden diğerine yerleştirebilirsiniz.";
$pgv_lang["zoom_out"]			= "Görüntüyü küçült";
$pgv_lang["zoom_in"]			= "Görüntüyü büyüt";

//-- MONTH NAMES
$pgv_lang["jan"]			= "Ocak";
$pgv_lang["feb"]			= "Şubat";
$pgv_lang["mar"]			= "Mart";
$pgv_lang["apr"]			= "Nisan";
$pgv_lang["may"]			= "Mayıs";
$pgv_lang["jun"]			= "Haziran";
$pgv_lang["jul"]			= "Temmuz";
$pgv_lang["aug"]			= "Ağustos";
$pgv_lang["sep"]			= "Eylül";
$pgv_lang["oct"]			= "Ekim";
$pgv_lang["nov"]			= "Kasım";
$pgv_lang["dec"]			= "Aralık";
$pgv_lang["abt"]			= "civarında";
$pgv_lang["aft"]			= "'#EXT# sonra";
$pgv_lang["and"]			= "ve";
$pgv_lang["bef"]			= "'#EXT# önce";
$pgv_lang["bet"]			= "arasında";
$pgv_lang["cal"]			= "hesaplandı";
$pgv_lang["est"]			= "varsayım";
$pgv_lang["from"]			= "'#EXT#";
$pgv_lang["int"]			= "herhalde";
$pgv_lang["to"]				= "'#EXT# kadar";
$pgv_lang["apx"]			= "takriben";
$pgv_lang["cir"]			= "dolaylarında";

//-- Admin File Messages
$pgv_lang["configuration"]		= "Genel ayarlar";
$pgv_lang["rebuild_indexes"]		= "İndeks dosyalarını yenile";
$pgv_lang["user_admin"]			= "Üyelerin ayarları";
$pgv_lang["user_created"]		= "Üye başarıyla eklendi.";
$pgv_lang["user_create_error"]		= "Üye eklenemedi. Lütfen bir sayfa geri dönüp tekrar deneyin.";
$pgv_lang["select_an_option"]		= "Aşağıdaki seçeneklerden birini seçin:";
$pgv_lang["readme_documentation"]	= "README belgelemesi";
$pgv_lang["password_mismatch"]		= "Şifreler birbirine uymuyor.";
$pgv_lang["enter_username"]		= "Üyenin rumuzunu işlemeniz gerekiyor.";
$pgv_lang["enter_fullname"]		= "Üyenin komple ismini işlemeniz gerekiyor.";
$pgv_lang["enter_password"]		= "Üyenin şifresini işlemeniz gerekiyor.";
$pgv_lang["confirm_password"]		= "Şifreyi tekrarlamanız lazım";
$pgv_lang["update_user"]		= "Üye verilerini güncelleştir";
$pgv_lang["update_myaccount"]		= "Benim üyelik verilerimi güncelleştir";
$pgv_lang["save"]			= "Hafıza et";
$pgv_lang["delete"]			= "Sil";
$pgv_lang["edit"]			= "İşle";
$pgv_lang["full_name"]			= "Komple isim";
$pgv_lang["visibleonline"]		= "Siteye bağlantılı iken diğer üyelere görünür";
$pgv_lang["editaccount"]		= "Bu üyeye kendi üyelik verilerini işleme hakkı ver";
$pgv_lang["admin_gedcom"]		= "GEDCOM ayarlarını düzenle";
$pgv_lang["confirm_user_delete"]	= "Üyeyi hakikatten silmek mi istiyorsunuz";
$pgv_lang["create_user"]		= "Yeni üye yarat";
$pgv_lang["no_login"]			= "İlettiğiniz rumuz ya da şifre tasdik edilemedi :-(";
$pgv_lang["import_gedcom"]		= "Bu GEDCOM dosyasını ithal et";
$pgv_lang["duplicate_username"]		= "Çifte rumuz. Siteye bu rumuz ile başka bir üye kayıtlıdır. Lütfen geri dönüp başka bir rumuz seçiniz.";
$pgv_lang["gedcomid"]			= "Şahsın GEDCOM kayıt numarası";
$pgv_lang["enter_gedcomid"]		= "GEDCOM kayıt numarasını işlemeniz gerekiyor.";
$pgv_lang["upload_gedcom"]		= "GEDCOM dosyasını yolla";
$pgv_lang["add_new_gedcom"]		= "Yeni bir GEDCOM yarat";
$pgv_lang["user_info"]			= "Kişisel sayfam";
$pgv_lang["rootid"]			= "Soyağacının kök şahsı";
$pgv_lang["download_gedcom"]		= "GEDCOM dosyasını indir";
$pgv_lang["manage_gedcoms"]		= "GEDCOM - Veritabanı ayarları";
$pgv_lang["gedcom_file"]		= "GEDCOM dosyası";
$pgv_lang["enter_filename"]		= "GEDCOM dosyasının ismini işlemeniz gerekiyor.";
$pgv_lang["file_not_exists"]		= "İşlediğiniz isimli dosya yoktur.";
$pgv_lang["file_exists"]		= "Bu isim altında başka bir GEDCOM bulunmuştur. Lütfen başka bir isim seçin ya da eski dosyayı önce silin.";
$pgv_lang["new_gedcom_title"]		= "[#GEDCOMFILE#] dosyasından alınan seçere ile ilgili veri";
$pgv_lang["upload_error"]		= "Dosyayı yollarken bir hata oldu.";
$pgv_lang["upload_help"]		= "Yerel bilgisayarınızdan sunucuya yollamak için bir dosya seçin. Tüm dosyalar dizine yollanılacaktır.";
$pgv_lang["add_gedcom_instructions"]	= "Bu yeni GEDCOM için bir dosya ismini işleyin. Yeni GEDCOM dosyası indeks dizininde yaratılacaktır.";
$pgv_lang["file_partial"]		= "Dosya tamamen yollanamadı. Lütfen tekrar deneyin";
$pgv_lang["file_missing"]		= "Dosya buraya ulaşmadı. Tekrar yollayın.";
$pgv_lang["file_success"]		= "Dosya başarı ile yollandı.";
$pgv_lang["file_too_big"]		= "Yollanılan dosya izin verilen büyüklüğü geçiyor.";
$pgv_lang["user_manual"]		= "PhpGedView - Kullanıcı belgelemesi";
$pgv_lang["upgrade"]			= "PhpGedView yazılımını güncelleştir";
$pgv_lang["administration"]		= "İdare / Ayarlar";
$pgv_lang["ansi_to_utf8"]		= "ANSİ ile kodlanmısş bu GEDCOM veritabanı UTF-8'e dönüştürülsün mü?";
$pgv_lang["utf8_to_ansi"]		= "UTF-8 ile kodlanmısş bu GEDCOM veritabanı ANSİ'ye (ISO-8859-1) dönüştürülsün mü?";
$pgv_lang["view_logs"]			= "Sistemin günlük raporuna bak";
$pgv_lang["logfile_content"]		= "Sistemin günlük raporunun içeriği. Günlük dosyasının isimi:";
$pgv_lang["step1"]			= "4 adımdan 1'incisi:";
$pgv_lang["step2"]			= "4 adımdan 2'ncisi:";
$pgv_lang["step3"]			= "4 adımdan 3'üncüsü:";
$pgv_lang["step4"]			= "4 adımdan 4'üncüsü:";
$pgv_lang["validate_gedcom"]		= "GEDCOM veritabanının geçerliğini denetle";
$pgv_lang["pgv_registry"]		= "PhpGedView kullanan diğer sitelerin listesi";
$pgv_lang["cancel_upload"]		= "Yollamayı iptal et";
$pgv_lang["manage_media_files"]		= "Mültimedya dosyalarını yönet";

//-- Relationship chart messages
$pgv_lang["relationship_chart"]		= "Akrabalık çizimi";
$pgv_lang["person1"]			= "1. şahıs";
$pgv_lang["person2"]			= "2. şahıs";
$pgv_lang["no_link_found"]		= "İki şahıs arasında hiç bir bağlantı bulunamadı";
$pgv_lang["sibling"]			= "Akrabalık";
$pgv_lang["follow_spouse"]		= "Evlilik ile gelişen akrabalılığı araştır";
$pgv_lang["timeout_error"]		= "Bu \"script\" hiçbir akrabalık bağlantısı bulamadan sona erdi!!!";
$pgv_lang["son"]			= "Erkek çocuk";
$pgv_lang["daughter"]			= "Kız çocuk";
$pgv_lang["brother"]			= "Erkek kardeş";
$pgv_lang["sister"]			= "Kız kardeş";
$pgv_lang["relationship_to_me"]		= "Benimle olan akrabalığı";
$pgv_lang["line_up_generations"]	= "Ayni kuşaktan olan şahısları bir seviyede göster";
$pgv_lang["oldest_top"]			= "En yaşlıları üstte göster";

//-- gedcom edit utility
$pgv_lang["check_delete"]		= "Bu GEDCOM hadisesini gerçekten silmek istiyor musunuz?";
$pgv_lang["gedrec_deleted"] 		= "GEDCOM kayıtı başarı ile silindi.";
$pgv_lang["gedcom_deleted"] 		= "[#GED#] isimli GEDCOM veritabanı başarı ile silindi.";
$pgv_lang["changes_exist"]		= "Bu GEDCOM dosyasında değişiklikler yapılmıştır";
$pgv_lang["accept_changes"]		= "Veritabanındaki değişiklikleri kabul/ret et";
$pgv_lang["show_changes"]		= "Bu kayıt güncelleştirilmiştir. Buraya tıklayıp değişiklikleri gözden geçirin.";
$pgv_lang["hide_changes"]		= "Buraya tıklayıp değişiklikleri saklayın.";
$pgv_lang["review_changes"]		= "GEDCOM içindeki değişiklikleri göster";
$pgv_lang["undo_successful"]		= "Değişiklikler geri alındı";
$pgv_lang["undo"]			= "Geri al";
$pgv_lang["view_change_diff"]		= "Değişiklikler dosyasına bak";
$pgv_lang["changes_occurred"]		= "Bu şahısın hakkında yapılan değişiklikler";
$pgv_lang["place_contains"]		= "Yerin içeriği:";
$pgv_lang["ged_import"]			= "İçeri aktarımı (import)";
$pgv_lang["now_import"]			= "Şimdi GEDCOM kayıtlarını aşağıdaki \"İçeri aktarımı (import)\" bağlantısına tıklayarak PhpGedView içine ithal etmeniz gerekiyor.";
$pgv_lang["find_place"]			= "Yer ara";
$pgv_lang["close_window"]		= "Pencereyi kapat";
$pgv_lang["close_window_without_refresh"]	= "Pencereyi tazelemeden kapat";
$pgv_lang["add_fact"]			= "Yeni hadise ekle";
$pgv_lang["add"]			= "Ekle";
$pgv_lang["add_new_husb"]		= "Yeni bir erkek eş ekle";
$pgv_lang["edit_name"]			= "İsimi işle";
$pgv_lang["add_wife"]			= "Bayan eş ekle";
$pgv_lang["add_new_wife"]		= "Yeni bir bayan eş ekle";
$pgv_lang["add_wife_to_family"]		= "Bu aileye bayan eş ekle";
$pgv_lang["find_family"]		= "Aile ara";
$pgv_lang["find_fam_list"]		= "Aile arama listesi";
$pgv_lang["custom_event"]		= "Kişisel hadise";
$pgv_lang["update_successful"]		= "Güncelleştirme başarılıydı";
$pgv_lang["add_child"]			= "Çocuk ekle";
$pgv_lang["delete_name"]		= "İsimi sil";
$pgv_lang["replace"]			= "Kayıtı değiştir";
$pgv_lang["append"] 			= "Kayıtı ekle";
$pgv_lang["add_father"]			= "Yeni bir baba ekle";
$pgv_lang["add_mother"]			= "Yeni bir anne ekle";
$pgv_lang["add_obje"]			= "Yeni mültimedya nesnesini ekle";
$pgv_lang["no_changes"]			= "Aktüel olarak yapılmış hiç bir değişiklik yoktur.";
$pgv_lang["accept"]			= "Kabul et";
$pgv_lang["accept_all"] 		= "Tüm değişiklikleri kabul et";
$pgv_lang["accept_successful"]		= "Değişiklikler başarı ile veritabanına işlenmiştir";
$pgv_lang["edit_raw"]			= "Sade GEDCOM kayıtını düzenle";
$pgv_lang["select_date"]		= "Bir tarih seçiniz";
$pgv_lang["create_source"]		= "Yeni kaynak ekle";
$pgv_lang["new_source_created"] 	= "Yeni kaynak başarı ile eklenmiştir.";
$pgv_lang["add_name"]			= "Yeni isim ekle";
$pgv_lang["user_cannot_edit"]		= "Bu rumuzlu üye bu GEDCOM veritabanını düzenleyemez.";
$pgv_lang["gedcom_editing_disabled"]	= "Bu GEDCOM veritabanının düzenlenmesi sistem yöneticisi tarafından engellenmiştir.";
$pgv_lang["privacy_prevented_editing"]	= "Mahremiyet ayarları bu kayıtı düzenlemenizi engellemektedir.";
$pgv_lang["add_asso"]				= "Yeni bir ilişki / eş ekle";
$pgv_lang["delete_source"]		= "Bu kaynağı sil";
$pgv_lang["confirm_delete_source"]	= "Bu kaynağı hakikatten GEDCOM dosyasından silmek istiyor musunuz?";
$pgv_lang["add_husb"]			= "Erkek eş ekle";
$pgv_lang["add_husb_to_family"]		= "Bu aileye erkek eş ekle";
$pgv_lang["add_child_to_family"]	= "Bu aileye bir çocuk ekle";
$pgv_lang["add_sibling"]		= "Erkek ya da kız kardeş ekle";
$pgv_lang["add_son_daughter"]		= "Erkek ya da kız çocuk ekle";
$pgv_lang["delete_person"]		= "Bu şahsı sil";
$pgv_lang["confirm_delete_person"]	= "Bu şahsı hakikatten GEDCOM dosyasından silmek istiyor musunuz?";
$pgv_lang["find_media"]			= "Mültimedya dosyalarını ara";
$pgv_lang["set_link"]			= "Bağlantı ekle";
$pgv_lang["add_source_lbl"]		= "Kaynak alıntısını ekle";
$pgv_lang["add_note"]			= "Hadiseye not ekle";
$pgv_lang["add_media_lbl"]		= "Mültimedya nesnesini ekle";
$pgv_lang["add_media"]			= "Yeni mültimedya nesnesini ekle";
$pgv_lang["add_source"]			= "Hadiseye yeni bir kaynak alıntısını ekle";
$pgv_lang["add_note_lbl"]		= "Not ekle";

$pgv_lang["in_this_month"]		= "Sizin tarihinizde bu ay içinde...";
$pgv_lang["in_this_year"]		= "Bu sene içinde - Sizin tarihinizde...";

//-- calendar.php messages
$pgv_lang["on_this_day"]		= "Sizin tarihinizde bu günde...";
$pgv_lang["year_anniversary"]		= "#year_var#. yıl dönümü";
$pgv_lang["today"]			= "Bugün";
$pgv_lang["day"]			= "Gün:";
$pgv_lang["month"]			= "Ay:";
$pgv_lang["showcal"]			= "Gösterilen:";
$pgv_lang["anniversary_calendar"]	= "Yıldönümü takvimi";
$pgv_lang["sunday"]			= "Pazar";
$pgv_lang["monday"]			= "Pazartesi";
$pgv_lang["tuesday"]			= "Salı";
$pgv_lang["wednesday"]			= "Çarşamba";
$pgv_lang["thursday"]			= "Perşembe";
$pgv_lang["living_only"]		= "Yaşayan şahısları";
$pgv_lang["recent_events"]		= "Güncel hadiseler (< 100 sene)";
$pgv_lang["year_error"]			= "Üzgünüz, ama 1970'ten önce olan bir tarih desteklenmiyor.";
$pgv_lang["all_people"]			= "Tüm şahıslar";
$pgv_lang["friday"]			= "Cuma";
$pgv_lang["saturday"]			= "Cumartesi";
$pgv_lang["lost_password"]		= "Şifrenizi unuttunuz mu?";
$pgv_lang["viewday"]			= "Günü göster";
$pgv_lang["viewmonth"]			= "Ayı göster";
$pgv_lang["viewyear"]			= "Seneyi göster";
$pgv_lang["min6chars"]			= "Şifre en azından 6 harf ya da sayı uzunluğunda olmalıdır";
$pgv_lang["pls_note03"]			= "Bu E-posta adresi üyeliğinizin canlandırılmasından önce tasdik edilecektir. Bu adres yöneticiden başka kimseye gösterilmeyecektir. Bu adres üzerinden size üyelik hesabınızın içeriklerini gösteren bir mektup gönderilecektir. Bu mektup sayesinde üyelik hesabınızı tasdik edebileceksiniz.";
$pgv_lang["pls_note02"]			= "Lütfen dikkat edin: Şifrenizde sırf alfabenin büyük / küçük harflerini ya da sayıları kullanın. Eğer şifrenizin içinde diğer özel harfleri kullanacak olursanız başka bir sistemden giriş yapmak isterken zorluklarla karşılaşabilirsiniz.";

//-- upload media messages
$pgv_lang["media_file"]			= "Medya dosyası";
$pgv_lang["upload_media"]		= "Medya dosyalarını yolla";
$pgv_lang["thumbnail"]			= "Tırnak resim";
$pgv_lang["upload_successful"]		= "Yollama başarılı idi";

//-- user self registration module
$pgv_lang["requestpassword"]		= "Şifremi unuttum. Yeni şifre istiyorum";
$pgv_lang["no_account_yet"]		= "Üye değil misiniz?";
$pgv_lang["requestaccount"]		= "Üye olmak istiyorum";
$pgv_lang["register_info_01"]		= "Bu sitenin sırf ziyaretçisi olarak veritabanındaki tüm kayıtları görebilme olanağınız yoktur.<br /><br />Bu ayarlar siteye eklenmiş yaşayan şahısların kişisel detaylarını korumak için yapılmıştır.<br />Eğer sizin kişisel detaylarınız bu sitenin veritabanına eklenmiş olsa, siz de tanımadığınız kişilerin sizinle ilgili tüm bilgileri görebilme olanağına sahip olmalarını istemezsiniz.<br /><br />Bunun için burada siteye üye olup, verilerinizin sitenin yöneticileri tarafından tasdik edilmesinden sonra yaşayan şahısların siteye eklenen kişisel detaylarını görme olanağına kavuşabilirsiniz. Eğer bu site akrabalık denetimini kullanıyorsa siteye üye olduktan sonra bile ancak yakın akrabalarınızın detaylarını görebilirsiniz.<br />Üye olduktan sonra sitenin yöneticileri size bu detayları görme hakkı dışında bunları işleme ve yeni bilgi ekleme hakkını da verebilirler.<br /><br />Üyelik isteminde bulunmadan önce lütfen bu sitede gördüğünüz şahıslarla akrabalık bağınızın bulunup bulunmadığını araştırın. Bunu sitedeki arama fonksiyonu sayesinde soy isimlerini ya da sizin akrabalarınızın yaşadığı şehirleri arayarak öğrenebilirsiniz.<br /><br />Eğer akrabalık bağınız yok ya da belli değilse detayları görebilme olanağı verilmeyeceği için boşuna üyelik için başvurmayın...<br />Eğer emin değilseniz önce bu sayfanın alt tarafında bulunan bağlantıyı tıklayın ve sitenin yöneticilerine E-posta yazıp bunlara danışın!<br /><br />";
$pgv_lang["pls_note01"]			= "Lütfen dikkat edin: Sistem büyük ve küçük harf arasında ayırt ediyor";
$pgv_lang["pls_note04"]			= "* ile işaretlenmiş alanlara içerik eklenmesi şarttır";
$pgv_lang["pls_note05"]			= "İstenen tüm verilerinizi doldurduktan ve sistemin bunları denetlemesinden sonra, iletmiş olduğunuz E-posta adresine bir tasdik mektubu gönderilecektir. Bu mektubun içindeki bilgilerle üyelik hesabınızı tasdik etmeniz gerekir. Eğer bu tasdik işlemini tamamlamazsanız üyelik hesabınız bu günden bir hafta sonra otomatik olarak silinecektir (bu silinme işleminden sonra tekrar istediğiniz rumuzu (username) kullanarak üyelik için başvurabilirsiniz). Bu siteye girmek için rumuzunuzu ve şifrenizi unutmamanız gerekir. Lütfen geçerli bir E-posta adresini iletmeye dikkat edin. Aksi taktirde tasdik mektubu elinize geçmez.<br /><br />Üyelik işleminde hata veyahut sorunlarla karşılaşırsanız yardım için lütfen bu sitenin webmasterine danışınız.";
$pgv_lang["emailadress"]		= "E-posta adresi";
$pgv_lang["savedata"]			= "Değişiklikleri hafıza et";
$pgv_lang["datachanged"]		= "Üyenin verileri değiştirilmiştir!";
$pgv_lang["datachanged_name"]		= "Yeni rumuzunuz ile tekrar giriş yapmak zorunda kalabilirsiniz.";
$pgv_lang["myuserdata"]			= "Şahsi ayarlar";
$pgv_lang["verified"]			= "Üye kendini tasdik etti";
$pgv_lang["verified_by_admin"]		= "Yönetici üyeyi tasdik etti";
$pgv_lang["user_theme"]			= "Şahsi tema";
$pgv_lang["mgv"]			= "Şahsi GedView";
$pgv_lang["editowndata"]		= "Şahsi ayarlar";
$pgv_lang["mail01_line01"]		= "Merhaba #user_fullname# ...";
$pgv_lang["mail01_line02"]		= "( #SERVER_NAME# ) sitesinde sizin E-posta adresiniz ( #user_email# ) ile üyelik isteminde bulunulmuştur.";
$pgv_lang["mail01_line03"]		= "Aşağıda gösterilen veriler iletilmiştir.";
$pgv_lang["mail01_line04"]		= "Üyelik işlemini tamamlamak için lütfen aşağıdaki bağlantıyı tıklayarak açılacak sayfada rumuzunuzu, şifrenizi ve tasdik kodunuzu işleyiniz.";
$pgv_lang["mail01_line05"]		= "Eğer siz bu üyelik isteminde bulunmadıysanız bu mektubu silip unutabilirsiniz.";
$pgv_lang["mail01_line06"]		= "Üyelik verileri başvurudan bir hafta sonra otomatik olarak silineceği için, size bu siteden bir daha mektup gönderilmeyecektir.";
$pgv_lang["mail01_subject"]		= "#SERVER_NAME# sitesine kayıdınız";
$pgv_lang["hashcode"]			= "Tasdik kodunuz:";
$pgv_lang["thankyou"]			= "Merhaba #user_fullname# ...<br />Üyelik isteminde bulunduğunuz için teşekkür ederiz.";
$pgv_lang["pls_note06"]			= "Şimdi işlediğiniz E-posta adresine (#user_email#) tasdik mektubu gönderilecektir. Bu mektubun içindeki verileri kullanıp önümüzdeki 7 gün içersinde verilerinizi tasdik etmeniz gerekiyor. Aksi taktirde verileriniz otomatik olarak silinecektir (bu silinme işleminden sonra tekrar istediğiniz rumuzu (username) kullanarak üyelik için başvurabilirsiniz). Bu siteye girmek için rumuzunuzu ve şifrenizi unutmamanız gerekir.";
$pgv_lang["registernew"]		= "Yeni üyelik tasdiki";
$pgv_lang["user_verify"]		= "Üyelik tasdiki";
$pgv_lang["mail02_line01"]		= "Merhaba Yönetici ...";
$pgv_lang["mail02_line02"]		= "Yeni bir ziyaretçi ( #SERVER_NAME# ) sitesinde üyelik isteminde bulunmuştur.";
$pgv_lang["mail02_line03"]		= "Bu üyeye üyelik işlemini tamamlaması için gerekli bilgiler gönderilmiştir.";
$pgv_lang["mail02_line04"]		= "Üye kendisini tasdik ettikten sonra onun sitenize giriş yapabilmesi için izin vermeniz gerektiğinden bir mesaj ile bilgilendirileceksiniz.";
$pgv_lang["mail02_subject"]		= "#SERVER_NAME# sitesinde yeni üyelik istemi";
$pgv_lang["date_created"]		= "Gönderiliş tarihi:";
$pgv_lang["message_from"]		= "Mesajı gönderen:";
$pgv_lang["message_from_name"]		= "İsminiz:";
$pgv_lang["message_to"]			= "Mesaj gönderilen rumuz:";
$pgv_lang["message_subject"]		= "Mesaj konusu:";
$pgv_lang["message_body"]		= "Mesaj içeriği:";
$pgv_lang["no_to_user"] 		= "Alıcı üye işlenmemiştir. Devam edemem.";
$pgv_lang["message_sent"]		= "Mesaj gönderildi";
$pgv_lang["reset"]			= "Sıfırla - İlk duruma getir";
$pgv_lang["site_default"]		= "Sitenin standardı";
$pgv_lang["invalid_email"]		= "Lütfen geçerli bir E-posta adresi ekleyiniz";
$pgv_lang["enter_subject"]		= "Lütfen mesajın konusunu ekleyin";
$pgv_lang["enter_body"]			= "Lütfen gönderilmeden mesaja birkaç satır ekleyin ;-)";
$pgv_lang["confirm_message_delete"]	= "Bu mesajı gerçekten silmek mi istiyorsunuz? Bu silme emri ileride geri alınamaz.";
$pgv_lang["message_email1"]		= "Aşağıdaki mesajı sizin PhpGedView üye hesabınıza gönderen: ";
$pgv_lang["message_email2"]		= "Aşağıdaki mesajı bir PhpGedView üye hesabına gönderdiniz:";
$pgv_lang["message_email3"]		= "Aşağıdaki mesajı bir PhpGedView yöneticisine (administrator) gönderdiniz:";
$pgv_lang["viewing_url"]		= "Bu mesaj yandaki URL okunurken yollandı:";
$pgv_lang["messaging2_help"]		= "Bu mesajın bir kopyası belirlediğiniz E-posta adresinize gönderilecektir.";
$pgv_lang["mygedview_desc"]		= "Şahsi GedView sayfası sizi ilgilendiren şahısları sık kullanılanlara eklemeniz, gelecek yıldönümlerini gözünüzden kaçırmamanız ve diğer PhpGedView kullanıcıları ile birlikte çalışabilmeniz için geliştirilmiştir.";
$pgv_lang["no_messages"]		= "Okunmamış mesajınız yoktur.";
$pgv_lang["clicking_ok"]		= "OK kelimesini tıklarsanız açılacak pencerede #user[fullname]# ile ilişkiye geçebilirsiniz.";
$pgv_lang["my_favorites"]		= "Sık kullanılanlar";
$pgv_lang["no_favorites"]		= "Daha hiçbir şahsı \"Sık kullanılanlar\" bölümünüze eklemediniz. \"Sık kullanılanlar\" bölümünüze bir şahsı eklemeniz için ekleyeceğiniz şahısın ayrıntılarını bulup \"Sık kullanılanlarıma ekle\" bağına tıklayın veyahut aşağıdaki kutunun içinde eklemek istediğiniz şahısın \"GEDCOM-Kişisel numarasını\" işleyin.";
$pgv_lang["add_to_my_favorites"]	= "\"Sık kullanılanlarıma\" ekle";
$pgv_lang["gedcom_favorites"]		= "Bu GEDCOM veritabanının \"Sık kullanılanları\"";
$pgv_lang["no_gedcom_favorites"]	= "\"Sık kullanılanlar\" bölümüne daha ekleme yapılmamıştır. Buraya ancak sitenin yöneticisi ekleme yapabilir.";
$pgv_lang["confirm_fav_remove"]		= "Bu şahsı hakikatten \"Sık kullanılanlarınızdan\" silmek istiyor musunuz?";
$pgv_lang["message_deleted"]		= "Mesaj silindi";
$pgv_lang["message"]			= "Mesaj gönder";
$pgv_lang["my_messages"]		= "Kişisel mesajlar";
$pgv_lang["send"]			= "Gönder";
$pgv_lang["pls_note07"]			= "Lütfen üyelik isteminizin tamamlanması ve tasdik edilmesi için, size bu siteden gönderiliş olan mektup ile elinize geçen rumuzunuzu, şifrenizi ve tasdik kodunuzu işleyin.";
$pgv_lang["pls_note08"]			= "#user_name# rumuzlu üyenin verileri gözden geçirildi.";
$pgv_lang["mail03_line01"]		= "Merhaba Yönetici ...";
$pgv_lang["mail03_line02"]		= "#newuser[username]# ( #newuser[fullname]# ) kendisine gönderilen tasdik kodu ile verilerini tasdik etmiştir.";
$pgv_lang["mail03_line03"]		= "Lütfen aşağıdaki bağlantıyı tıklayıp sitenize giriş yapın, üyenin verilerini işleyip tasdik edin ve sitenize girmesine izin verin.";
$pgv_lang["mail03_subject"]		= "#SERVER_NAME# sitesinde yeni tasdik";
$pgv_lang["pls_note09"]			= "Üye olarak tasdik edildiniz.";
$pgv_lang["pls_note10"]			= "Yöneticiye haber verilmiştir.<br />O sizin hesabınızı açıp siteye girmenize izin verdikten sonra rumuzunuz ve şifreniz ile siteye giriş yapabilirsiniz.";
$pgv_lang["data_incorrect"]		= "Verileriniz yanlıştır.<br />Lütfen tekrarlayınız!";
$pgv_lang["user_not_found"]		= "Sistem işlediğiniz verileri tasdik edememiştir. Lütfen bir sayfa geri gidip tekrar deneyiniz.";
$pgv_lang["lost_pw_reset"]		= "Yeni şifre istek sayfası";
$pgv_lang["pls_note11"]			= "Size yeni bir şifre yollanmasını istiyorsaniz, üyelik hesabınızın rumuzunu ve E-posta adresini işleyin.<br /><br />Bundan sonra biz size E-posta yolu ile hesabınızı onaylayıcı bilgi içeren özel bir URL yollayacağız.<br />Yollanan bu URL adresini ziyaret ettiğinizde bu siteye giriş için gerekli olan şifrenizi değiştirmenize izin verilecektir.<br />Güvenlik nedenleri yüzünden bu özel URL adresini (bu sitenin yöneticileri dahil) kimseye göstermemeniz rica olunur.<br />Biz zaten böyle bir istekte bulunmayız...<br /><br />Bu sitenin yöneticisi tarafından yardıma ihtiyacınız varsa lütfen direk ona danışın.";
$pgv_lang["enter_email"]		= "E-posta adresinizi eklemeniz gerekiyor";
$pgv_lang["mail04_line01"]		= "Merhaba #user_fullname# ...";
$pgv_lang["mail04_line02"]		= "Rumuzunuz için yeni bir şifre istenmiştir!";
$pgv_lang["mail04_line03"]		= "Tavsiye:";
$pgv_lang["mail04_line04"]		= "Lütfen şimdi aşağıdaki bağlantıyı tıklayıp yeni şifreniz ile giriş yapın ve sonra şifrenizi güvenlik nedenleri yüzünden tekrar değiştirin.";
$pgv_lang["mygedview"]			= "Şahsi GedView bölümü";
$pgv_lang["passwordlength"]		= "Şifre en azından 6 harf ya da sayı uzunluğunda olmalıdır";
$pgv_lang["admin_approved"]		= "#SERVER_NAME# sitesindeki üyeliğiniz yönetici tarafından tasdik edilmiştir.";
$pgv_lang["you_may_login"]		= "Şimdi isterseniz aşağıdaki bağlantıya giderek PhpGedView sitesine girebilirsiniz...";

//-- mygedview page
$pgv_lang["welcome"]			= "Hoş geldiniz";
$pgv_lang["upcoming_events"]		= "Gelecek yıldönümleri";
$pgv_lang["chat"]			= "Chat";
$pgv_lang["users_logged_in"]		= "Bağlı bulunan diğer ziyaretçiler";
$pgv_lang["reply"]			= "Cevapla";
$pgv_lang["random_picture"]		= "Rasgele fotoğraf";
$pgv_lang["sending_to"]			= "Bu mesaj #TO_USER# rumuzlu üyeye yollanacaktır.";
$pgv_lang["preferred_lang"]		= "Bu üye mesajlarını #USERLANG# dilinde okumayı tercih ediyor.";
$pgv_lang["gedcom_created_using"]	= "Bu GEDCOM veritabanı <b>#SOFTWARE#</b> yazılımının <b>#VERSION#</b> sürümü ile yaratılmıştır. ";
$pgv_lang["gedcom_created_on"]		= "Bu GEDCOM veritabanı <b>#DATE#</b> tarihinde yaratılmıştır.";
$pgv_lang["gedcom_created_on2"]		= "Yaratılış tarihi: <b>#DATE#</b>";
$pgv_lang["gedcom_stats"]		= "GEDCOM veritabanı istatistikleri:";
$pgv_lang["stat_individuals"]		= "Şahıslar, ";
$pgv_lang["stat_families"]		= "Aileler, ";
$pgv_lang["stat_sources"]		= "Kaynaklar, ";
$pgv_lang["stat_other"]			= "Başka kayıt";
$pgv_lang["customize_page"]		= "Şahsi GedView bölümünü özelleştir";
$pgv_lang["customize_gedcom_page"]	= "Bu GEDCOM karşılama sayfasını özelleştirin";
$pgv_lang["upcoming_events_block"]	= "Gelecek olaylar kutusu";
$pgv_lang["todays_events_block"]	= "\"Bu günde\" kutusu";
$pgv_lang["logged_in_users_block"]	= "Bağlı bulunan ziyaretçiler kutusu";
$pgv_lang["user_messages_block"]	= "Üye mesajları kutusu";
$pgv_lang["user_favorites_block"]	= "Üye - \"Sık kullanılanlar\" kutusu";
$pgv_lang["welcome_block"]		= "Üye - \"Hoş geldin\" kutusu";
$pgv_lang["random_media_block"]		= "\"Rasgele medya\" kutusu";
$pgv_lang["gedcom_block"]		= "GEDCOM - \"Hoş geldin\" kutusu";
$pgv_lang["gedcom_favorites_block"]	= "GEDCOM - \"Sık kullanılanlar\" kutusu";
$pgv_lang["gedcom_stats_block"]		= "GEDCOM - \"İstatistikler\" kutusu";
$pgv_lang["login_block"]		= "\"Giriş\" kutusu";
$pgv_lang["theme_select_block"]		= "\"Tema seçme\" kutusu";
$pgv_lang["block_top10_title"]		= "Başlıca işlenmiş soy isimleri";
$pgv_lang["block_top10"]		= "Soy isimleri \"Top 10\" kutusu";
$pgv_lang["gedcom_news_block"]		= "GEDCOM - \"Haberler\" kutusu";
$pgv_lang["user_news_block"]		= "Üye - \"Günlük\" kutusu";
$pgv_lang["my_journal"]			= "Benim günlüğüm";
$pgv_lang["add_journal"]		= "Yeni bir günlük mesajını ekle";
$pgv_lang["gedcom_news"]		= "Haberler";
$pgv_lang["confirm_news_delete"]	= "Bu haberi gerçekten silmek mi istiyorsunuz?";
$pgv_lang["add_news"]			= "Haber mesajı ekle";
$pgv_lang["no_news"]			= "Daha hiçbir haber yayınlanmamıştır.";
$pgv_lang["edit_news"]			= "Günlük ya da haberlere ekleme yap veyahut içerikleri değiştir";
$pgv_lang["enter_title"]		= "Lütfen başlık ekleyin.";
$pgv_lang["news_saved"] 		= "Yaptığınız ekleme başarı ile hafıza edilmiştir.";
$pgv_lang["article_text"]		= "Mesajın metni:";
$pgv_lang["main_section"]		= "Ana bölüm kutuları";
$pgv_lang["right_section"]		= "Sağ bölüm kutuları";
$pgv_lang["move_up"]			= "Bir yukarıya taşı";
$pgv_lang["move_down"]			= "Bir aşağıya taşı";
$pgv_lang["move_right"]			= "Sağa taşı";
$pgv_lang["move_left"]			= "Sola taşı";
$pgv_lang["broadcast_all"]		= "Tüm üyeler için yayınla";
$pgv_lang["hit_count"]			= "Sayaç:";
$pgv_lang["phpgedview_message"]		= "PhpGedView mesajı";
$pgv_lang["common_surnames"]		= "Bu veritabanında sık kullanılan soy isimlerin listesi";
$pgv_lang["default_news_title"]		= "Nesep tetkikine hoş geldiniz";
$pgv_lang["default_news_text"]		= "Bu sitede bulunan seçere bilgileri <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView #VERSION#</a> yazılımı ile gösterilmektedir. Bu sayfada bu soy ağacını tanıtan bilgiler bulunmaktadır. Bu bilgilere ulaşmak için 'Çizgeler' menüsünden bir çizgeyi seçin, ya da 'Listeler' menüsünden şahıs listesine gidin, veyahut sizi ilgilendiren bir soy ismini ya da yeri arayın.<br /><br />Bu siteyi gezerken bir sorunla karşılaşacak olursanız yardım simgesine tıklayıp bulunduğunuz sayfa hakkında gerekli yardımı bulabilirsiniz.<br /><br />Bu siteyi ziyaret ettiğiniz için teşekkür ederiz.";
$pgv_lang["reset_default_blocks"]	= "Kutuları varsayılan duruma sıfırla";
$pgv_lang["recent_changes"]		= "En son değişiklikler";
$pgv_lang["recent_changes_block"]	= "\"En son değişiklikler\" kutusu";
$pgv_lang["delete_selected_messages"]	= "Seçilmiş mesajları sil";
$pgv_lang["use_blocks_for_default"]	= "Bu kutular tüm ziyaretçiler icin varsayılan olarak kullanılsın mı?";

//-- upgrade.php messages
$pgv_lang["upgrade_util"]		= "Güncelleştirme aracı";
$pgv_lang["no_upgrade"]			= "Güncelleştirilecek dosya yoktur.";
$pgv_lang["use_version"]		= "Kullandığınız sürüm numarası:";
$pgv_lang["current_version"]		= "Güncel \"stable\" sürüm numarası:";
$pgv_lang["upgrade_download"]		= "İndir:";
$pgv_lang["latest"]			= "Kullandığınız PhpGedView sürümü günceldir.";
$pgv_lang["location"]			= "Güncelleme dosyalarının bulundugu yer: ";
$pgv_lang["options"]			= "Seçenekler:";
$pgv_lang["inc_languages"]		= "Diller";
$pgv_lang["inc_config"]			= "Yapılandırma dosyası";
$pgv_lang["inc_index"]			= "İndeks dosyaları";
$pgv_lang["inc_themes"]			= " Temalar";
$pgv_lang["inc_docs"]			= "Elkitapları";
$pgv_lang["inc_privacy"]		= "Mahremiyet dosyası/dosyaları";
$pgv_lang["inc_backup"]			= " Yedekleme yap";
$pgv_lang["upgrade_help"]		= "Bana yardım et";
$pgv_lang["cannot_read"]		= "Okuyamadığım dosya:";
$pgv_lang["not_configured"]		= "Daha PhpGedView ayarlarını yapılandırmadınız.";
$pgv_lang["location_upgrade"]		= "Lütfen güncelleme dosyalarınızın bulunduğu yeri işleyin.";
$pgv_lang["new_variable"]		= "Yeni değişken bulundu: ";
$pgv_lang["config_open_error"]		= "Yapilandırma dosyasını açarken bir hata oluştu.";
$pgv_lang["gedcom_config_write_error"]	= "HATA!!! GEDCOM yapılandırma dosyasına yazamıyorum.";
$pgv_lang["config_update_ok"]		= "Yapılandırma dosyanız başarı ile güncelleştirilmiştir.";
$pgv_lang["config_uptodate"]		= "Yapılandırma dosyanız günceldir.";
$pgv_lang["processing"]			= "İşliyorum...";
$pgv_lang["privacy_open_error"]		= "[#PRIVACY_MODULE#] isimli mahremiyet dosyasını açılırken bir HATA ortaya çıktı.";
$pgv_lang["privacy_write_error"]	= "HATA!!! [#PRIVACY_MODULE#] isimli mahremiyet dosyasına yazamadım.<br />Lütfen bu dosyanın yazılma hakkına sahip olmasını sağlayın.<br />Bu yazma hakları değişiklikler eklendikten sonra geriye alınabilir.";
$pgv_lang["privacy_update_ok"]		= "[#PRIVACY_MODULE#] isimli mahremiyet dosyası başarı ile güncelleştirildi.";
$pgv_lang["privacy_uptodate"]		= "[#PRIVACY_MODULE#] isimli mahremiyet dosyası günceldir.";
$pgv_lang["heading_privacy"]		= "Mahremiyet dosyası/dosyaları";
$pgv_lang["heading_phpgedview"]		= "PhpGedView dosyaları:";
$pgv_lang["heading_image"]		= "Resim dosyaları:";
$pgv_lang["heading_index"]		= "İndeks dosyaları:";
$pgv_lang["heading_language"]		= "Dil dosyaları:";
$pgv_lang["heading_theme"]		= "Tema dosyaları:";
$pgv_lang["heading_docs"]		= "Elkitapları:";
$pgv_lang["copied_success"]		= "başarıyla kopyalandı.";
$pgv_lang["backup_copied_success"]	= "yedekleme dosyası başarı ile yaratıldı.";
$pgv_lang["folder_created"]		= "Klasör yaratıldı";
$pgv_lang["upgrade_completed"]		= "Güncelleme başarı ile gerçekleştirilmiştir";

//-- validate gedcom
$pgv_lang["invalid_dates"]		= "Hatalı tarih biçimleri bulunmuştur. Temizlemeyi seçerseniz bunlar GG AAA SSSS (örnek: 1 JAN 2004) biçimine çevirilecektir.";
$pgv_lang["valid_gedcom"]		= "Geçerli GEDCOM bulundu. Temizlemeye gerek yok. ";
$pgv_lang["optional_tools"]		= "İsterseniz ithal etmeden önce asağıdaki seçmeli aracları kullanabilirsiniz.";
$pgv_lang["optional"]			= "Seçmeli araçlar";
$pgv_lang["day_before_month"]		= "Önce gün sonra ay (GG AA SSSS)";
$pgv_lang["month_before_day"]		= "Önce ay sonra gün (AA GG SSSS)";
$pgv_lang["do_not_change"]		= "Değiştirme";
$pgv_lang["example_date"]		= "GEDCOM veritabanınızdan hatalı bir tarih biçiminin örneği:";
$pgv_lang["add_media_tool"]		= "Medya dosyası ekleme aracı";
$pgv_lang["launch_media_tool"]		= "Buraya tıklayarak \"medya ekleme aracını\" kullanabilirsiniz.";
$pgv_lang["highlighted"]		= "Vurgulanan resim";
$pgv_lang["extension"]			= "Uzantı";
$pgv_lang["add_media_button"]		= "Medya dosyası ekle";
$pgv_lang["adds_completed"]		= "Medya dosyası başarı ile GEDCOM veritabanına eklenmiştir.";
$pgv_lang["cleanup_places"]		= "Yerleri temizle";
$pgv_lang["empty_lines_detected"]	= "GEDCOM veritabanınızda boş sıralar bulunmuştur. Temizlemeyi seçerseniz bunlar silinecektir.";

//-- hourglass chart
$pgv_lang["hourglass_chart"]		= "Kum saati çizimi";

//-- report engine
$pgv_lang["choose_report"]		= "Rapor seçimi";
$pgv_lang["enter_report_values"]	= "Rapor içeriklerini işleyin";
$pgv_lang["selected_report"]		= "Seçilen rapor türü";
$pgv_lang["run_report"]			= "Rapora bak";
$pgv_lang["select_report"]		= "Raporu seç";
$pgv_lang["download_report"]		= "Raporu indir";
$pgv_lang["reports"]			= "Raporlar";
$pgv_lang["pdf_reports"]		= "PDF raporları";
$pgv_lang["html_reports"]		= "HTML raporları";
$pgv_lang["family_group_report"]	= "Aile raporu";
$pgv_lang["enter_famid"]		= "Aile numarası";
$pgv_lang["show_sources"]		= "Kaynaklar gösterilsin mi?";
$pgv_lang["show_notes"]			= "Notlar gösterilsin mi?";
$pgv_lang["show_basic"]			= "Temel hadiseler, boş olsa da, eklensin mi?";
$pgv_lang["show_photos"]		= "Fotoğraflar gösterilsin mi?";
$pgv_lang["individual_report"]		= "Şahıs raporu";
$pgv_lang["enter_pid"]			= "Şahıs numarası";
$pgv_lang["individual_list_report"]	= "Şahıs listesi raporu";
$pgv_lang["generated_by"]		= "Üretenin ismi:";
$pgv_lang["list_children"]		= "Her çocuğu doğum tarihine göre sırala.";
$pgv_lang["birth_report"]		= "Doğum tarihi ve yeri raporu";
$pgv_lang["birthplace"]				= "Doğum yeri içeriği";
$pgv_lang["birthdate1"]				= "Başlangıc tarihi";
$pgv_lang["birthdate2"]				= "Bitiş tarihi";
$pgv_lang["sort_by"]			= "Sıralama biçimi:";

$pgv_lang["cleanup"]			= "Temizle";
$pgv_lang["skip_cleanup"]		= "Temizlemeyi atla";

//-- CONFIGURE (extra) messages for programs patriarch, slklist and statistics
$pgv_lang["dynasty_list"]		= "Aile tablosu";
$pgv_lang["make_slklist"]		= "EXCEL (SLK) listesini yarat.";
$pgv_lang["patriarch_list"]		= "Ata listesi";
$pgv_lang["slk_list"]			= "EXCEL (SLK) listesi";
$pgv_lang["statistics"]			= "İstatistikler";

//-- Merge Records
$pgv_lang["merge_records"]		= "Kayıtları birleştir";
$pgv_lang["merge_step1"]		= "Kayıt birleştirme - 3 adımdan 1'incisi";
$pgv_lang["merge_step2"]		= "Kayıt birleştirme - 3 adımdan 2'ncisi";
$pgv_lang["merge_step3"]		= "Kayıt birleştirme - 3 adımdan 3'üncüsü";
$pgv_lang["no_matches_found"]		= "Uyan hiç bir hadise bulunamadı";
$pgv_lang["record"]			= "Kayıt";
$pgv_lang["merge_more"]			= "Kayıt birleştirmeye devam et.";

//-- ANCESTRY FILE MESSAGES
$pgv_lang["ancestry_chart"]		= "Soy ağacı tablosu";
$pgv_lang["chart_style"]		= "Çizim türü";
$pgv_lang["ancestry_list"]		= "Seçere listesi";
$pgv_lang["ancestry_booklet"]		= "Seçere kitapçığı";
// 1st generation
$pgv_lang["sosa_2"]			= "Baba";
$pgv_lang["sosa_3"]			= "Anne";
// 2nd generation
$pgv_lang["sosa_4"]			= "Büyükbaba";
$pgv_lang["sosa_5"]			= "Babaanne";
$pgv_lang["sosa_6"]			= "Dede";
$pgv_lang["sosa_7"]			= "Anneanne";
$pgv_lang["sosa_8"]			= "Büyükbabanın babası";
// 3rd generation
$pgv_lang["sosa_9"]			= "Büyükbabanın annesi";
$pgv_lang["sosa_10"]			= "Babaannenin babası";
$pgv_lang["sosa_11"]			= "Babaannenin annesi";
$pgv_lang["sosa_12"]			= "Dedenin babası";
$pgv_lang["sosa_13"]			= "Dedenin annesi";
$pgv_lang["sosa_14"]			= "Anneannenin babası";
$pgv_lang["sosa_15"]			= "Anneannenin annesi";
// 4th generation

// 5th generation

//-- FAN CHART
$pgv_lang["fan_chart"]			= "Değirmi çizge";
$pgv_lang["fan_width"]			= "Değirmi çizge'nin eni";
$pgv_lang["fontfile_error"]		= "Yazı tipi dosyası PHP sunucusunda bulunamadı";

//-- RSS Feed
$pgv_lang["rss_descr"]			= "Haberler ve bağlantılar #GEDCOM_TITLE# sitesindendir";
$pgv_lang["rss_logo_descr"]		= "Feed PhpGedView tarfından yaratılmıştır";
$pgv_lang["statutci"]			= "İndeks yaratılamadı";

//-- statistics utility
$pgv_lang["statnnames"]			= "Soy isim sayısı =";
$pgv_lang["statnfam"]			= "Aile sayısı =";
$pgv_lang["statnmale"]			= "Erkek sayısı =";
$pgv_lang["statnfemale"]		= "Kadın sayısı =";
$pgv_lang["statvars"]			= "Lütfen çizim için gerekli olan değişkenleri işleyin";
$pgv_lang["statlxa"]			= "X-Ekseni boyunca:";
$pgv_lang["statlya"]			= "Y-Ekseni boyunca";
$pgv_lang["statlza"]			= "Z-Ekseni boyunca";
$pgv_lang["stat_10_none"]		= "Hiç biri";
$pgv_lang["stat_11_mb"]			= "Doğum günlerinin ayı";
$pgv_lang["stat_12_md"]			= "Vefat günlerinin ayı";
$pgv_lang["stat_13_mm"]			= "Evlenme günlerinin ayı";
$pgv_lang["stat_14_mb1"]		= "Bir ilişkide doğan 1. çocuğun doğum ayı";
$pgv_lang["stat_15_mm1"]		= "İlk evlenme günlerinin ayı";
$pgv_lang["stat_16_mmb"]		= "Evlilik ile 1. doğum arasındaki ay sayısı.";
$pgv_lang["stat_17_arb"]		= "Doğum senesi bazında yaş.";
$pgv_lang["stat_18_ard"]		= "Vefat senesi bazında yaş.";
$pgv_lang["stat_19_arm"]		= "Evlilik senesi bazında yaş.";
$pgv_lang["stat_20_arm1"]		= "1. Evlilik senesi bazında yaş.";
$pgv_lang["stat_21_nok"]		= "Çocuk sayısı.";
$pgv_lang["stat_gmx"]			= " Aylar için limit değerleri denetleyin";
$pgv_lang["stat_gax"]			= " Yaşlar için limit değerleri denetleyin";
$pgv_lang["stat_gnx"]			= " Miktarlar için limit değerleri denetleyin";
$pgv_lang["stat_200_none"]		= "Hepsi (ya da boş)";
$pgv_lang["stat_201_num"]		= "Miktar";
$pgv_lang["stat_202_perc"]		= "Yüzdelik";
$pgv_lang["stat_300_none"]		= "Hiç biri";
$pgv_lang["stat_301_mf"]		= "Erkek / Kadın";
$pgv_lang["statmess1"]			= "Aşağıda yalnız x-ekseni ya da z-ekseni ile ilgili limit değerleri işleyin";
$pgv_lang["statar_xgp"]			= "Periyotlar için gerekli olan limit değerler (x-ekseni):";
$pgv_lang["statar_xgl"]			= "Yaşlar için gerekli olan limit değerler (x-ekseni):";
$pgv_lang["statar_xgm"]			= "Aylar için gerekli olan limit değerler (x-ekseni):";
$pgv_lang["statar_xga"]			= "Miktarlar için gerekli olan limit değerler (x-ekseni):";
$pgv_lang["statar_zgp"]			= "Periyotlar için gerekli olan limit değerler (z-ekseni):";
$pgv_lang["statreset"]			= "Sıfırla - İlk duruma getir";
$pgv_lang["statsubmit"]			= "Çizimi göster";

//-- statisticsplot utility
$pgv_lang["stpl"]			= "...";
$pgv_lang["alive_in_year"]			= "Yılda yaşayan";
$pgv_lang["is_alive_in"]			= "#YEAR# senesinde hayatta olanlar";

//-- alive in year

//-- find media
$pgv_lang["add_directory"]		= "Dizin ekle";
$pgv_lang["show_thumbnail"]		= "Tırnak resimleri göster";

//-- link media
$pgv_lang["link_media"]			= "Mültimedya bağla";

//-- Help system
$pgv_lang["definitions"]		= "Tanımlar";

//-- Index_edit
$pgv_lang["description"]		= "Betimleme";

//$pgv_lang["add_main_block"]		= "Ana bölüme bir kutu ekle...";
//$pgv_lang["add_right_block"]		= "Sağ bölüme bir kutu ekle...";
//$pgv_lang["gedcom_title"]		= "Bu GEDCOM-Dosyasının içeriğini belirleyen bir başlık belirleyin:";
//$pgv_lang["date_format"]		= "Tarih biçimi";

if (file_exists( "languages/lang.tr.extra.php")) require  "languages/lang.tr.extra.php";

?>
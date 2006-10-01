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
if (preg_match("/configure_help\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
	print "You cannot access a language file directly.";
	exit;
}

$pgv_lang["SHOW_SOURCES"]		= "Kaynakları göster";

//-- CONFIGURE FILE MESSAGES
$pgv_lang["configure"]			= "PhpGedView ayarlarını işle";
$pgv_lang["standard_conf"]		= "Genel ayarlama seçenekleri";
$pgv_lang["advanced_conf"]		= "Gelişmiş ayarlama seçenekleri";
$pgv_lang["gedcom_conf"]		= "Genel GEDCOM ayarları";
$pgv_lang["media_conf"]			= "Mültimedya";
$pgv_lang["accpriv_conf"]		= "Erişim ve mahremiyet";
$pgv_lang["displ_conf"]			= "Görüntü ve düzenler";
$pgv_lang["displ_names_conf"]		= "İsimler";
$pgv_lang["displ_comsurn_conf"]		= "Yaygın soy isimleri";
$pgv_lang["displ_layout_conf"]		= "Görüntü düzeni";
$pgv_lang["displ_hide_conf"]		= "Sakla & Göster";
$pgv_lang["useropt_conf"]		= "Üye seçenekleri";
$pgv_lang["contact_conf"]		= "İlişki ayarları";
$pgv_lang["meta_conf"]			= "META TAG ayarlama seçenekleri";
$pgv_lang["configure_head"]		= "PhpGedView ayarları";
$pgv_lang["gedconf_head"]		= "GEDCOM ayarları";
$pgv_lang["default_user"]		= "Genel yönetici üyeyi yarat.";
$pgv_lang["about_user"]			= "İlk önce genel yönetici üyeyi yaratmanız gerekiyor. Bu üye yaplım dosyalarını güncelleştirme, özel verileri izleme ve diğer üyeleri yaratma ve işleme haklarına sahip olacaktır.";
$pgv_lang["can_admin"]			= "Üye yönetmecilik yapabilir";
$pgv_lang["can_edit"]			= "Verilecek haklar";
$pgv_lang["access"]				= "Erişim";
$pgv_lang["accept"]				= "Onaylama";
$pgv_lang["none"]			= "Hiç biri";
$pgv_lang["add_user"]			= "Yeni üye ekle";
$pgv_lang["current_users"]		= "Aktüel üye listesi";
$pgv_lang["leave_blank"]		= "Kullandığınız şifreyi değiştirmek istemiyorsanız bu alanı boş bırakın.";
$pgv_lang["other_theme"]		= "Başka - Lütfen işleyin";
$pgv_lang["performing_update"]		= "Güncelleme yapılmaktadır.";
$pgv_lang["config_file_read"]		= "Yapılandırma dosyası okundu.";
$pgv_lang["media_drive_letter"]		= "Medya yolunun içinde sürücü harfi bulunmamalıdır. Aksi halde medya belki gösterilemez.";
$pgv_lang["pgv_config_write_error"]	= "Hata!!! PhpGedView yapılandırma dosyasına yazamadım. Lütfen dosya ve dizin izinlerini denetleyin ve ondan sonra bu adımı tekrar deneyin.";
$pgv_lang["click_here_to_continue"]	= "Devam etmek için buraya tıklayın";
$pgv_lang["config_help"]		= "Yapılandırma yardımı";
$pgv_lang["index"]			= "İndex dosyaları";
$pgv_lang["mysql"]			= "MySQL";
$pgv_lang["db"]				= "Veritabanı";
$pgv_lang["admin_gedcoms"]		= "GEDCOM veritabanı ayarlarını değiştirmek için buraya tıklayın.";
$pgv_lang["current_gedcoms"]		= "Aktüel GEDCOM veritabanları";
$pgv_lang["gedcom_adm_head"]		= "GEDCOM ayarlandırması";
$pgv_lang["ged_download"]		= "İndir";
$pgv_lang["ged_gedcom"]			= "GEDCOM dosyası";
$pgv_lang["ged_title"]			= "GEDCOM veritabanın başlığı";
$pgv_lang["ged_config"]			= "Yapılandırma dosyası";
$pgv_lang["ged_search"]			= "Arama-Günlük dosyaları";
$pgv_lang["ged_privacy"]		= "Mahremiyet dosyası";
$pgv_lang["show_phpinfo"]		= "PHPInfo sayfasını göster";
$pgv_lang["confirm_gedcom_delete"]	= "Bu GEDCOM veritabanını hakikatten SİLMEK mi istiyorsunuz";
$pgv_lang["gregorian"]			= "Milâdi";
$pgv_lang["julian"]			= "Jüliyen";
$pgv_lang["config_french"]		= "Fransızca";
$pgv_lang["jewish"]			= "Jahudice";
$pgv_lang["config_hebrew"]		= "İbranice";
$pgv_lang["jewish_and_gregorian"]	= "Jahudice ve milâdi";
$pgv_lang["hebrew_and_gregorian"]	= "İbranice ve milâdi";
$pgv_lang["hijri"]			= "Hicri";
$pgv_lang["arabic_cal"]			= "Arapça";
$pgv_lang["disabled"]			= "Kullanım dışı";
$pgv_lang["mouseover"]			= "Fare üzerindeyken (mouse over)";
$pgv_lang["mousedown"]			= "Fare tıklandıktan sonra (mouse down)";
$pgv_lang["click"]			= "Fare tıklamasında (mouse click)";
$pgv_lang["mailto"]			= "Mailto bağlantısı sistemi";
$pgv_lang["messaging"]			= "PhpGedView dahili mesaj sistemi";
$pgv_lang["messaging2"]			= "PhpGedView dahili mesaj sistemi ve E-posta";
$pgv_lang["messaging3"]			= "PhpGedView sunucuda hafıza etmeden E-postaları yolluyor";
$pgv_lang["no_messaging"]		= "Hiç bir mesaj sistemi";
$pgv_lang["no_logs"]			= "Hiç bir günlük tutma (Disable logging)";
$pgv_lang["daily"]			= "Her gün";
$pgv_lang["weekly"]			= "Haftada bir";
$pgv_lang["monthly"]			= "Ayda bir";
$pgv_lang["yearly"]			= "Senede bir";
$pgv_lang["config_still_writable"]	= "Yapılandırma dosyanıza hala yazma izini vardır.<br />Yapılandırmayı tamamladıysanız güvenlik nedenleri yüzünden bu dosyanın izinlerini yalnız-okuma (read-only) türüne geri çevirin.";
$pgv_lang["admin_verification_waiting"] = "Tasdiklenmeyi bekleyen üyelik istemleri mevcuttur";
$pgv_lang["DEFAULT_GEDCOM"]		= "Genel GEDCOM";
$pgv_lang["privileges"]			= "Verilen haklar";
$pgv_lang["date_registered"]		= "Üye olunan tarih";
$pgv_lang["last_login"]			= "Son giriş tarihi";
$pgv_lang["server_url_note"]		= "Buraya PhpGedView yazılımının yerleştiridiği dizinin URL adresi yazılmalıdır. Bu veriyi ancak ne yaptığınızı gerçekten biliyorsanız değiştirin.<br />PhpGedView sunucunun URL adresini yandaki şekilde belirlemiştir: #GUESS_URL#";
$pgv_lang["PGV_DATABASE"]		= "PhpGedView veritabanı türü";
$pgv_lang["DBTYPE"]			= "Varitabanı türü";

$pgv_lang["DBPASS"]			= "MySQL Veritabanı şifresi";
$pgv_lang["DBNAME"]			= "Veritabanın ismi";
$pgv_lang["ALLOW_CHANGE_GEDCOM"]	= "GEDCOM seçme iznini ver";
$pgv_lang["gedcom_path"]			= "GEDCOM yolu ve isimi";
$pgv_lang["GEDCOM"]			= "GEDCOM yol ve ismi";
$pgv_lang["CHARACTER_SET"]		= "Harflerin kodlanma sistemi";
$pgv_lang["LANGUAGE"]			= "Dil";
$pgv_lang["ENABLE_MULTI_LANGUAGE"]	= "Üyenin gösterilen dili değiştirmesine izin ver";
$pgv_lang["CALENDAR_FORMAT"]		= "Takvim türü";
$pgv_lang["DISPLAY_JEWISH_THOUSANDS"]	= "İbranice binliklerini göster";
$pgv_lang["DISPLAY_JEWISH_GERESHAYIM"]		= "İbranicre \"Gershayim\" göster";
$pgv_lang["USE_RTL_FUNCTIONS"]			= "Sağdan sola (RTL) işlemini kullan";
$pgv_lang["DEFAULT_PEDIGREE_GENERATIONS"]	= "Soy ağacı çizgesinde nesil sayısı";
$pgv_lang["MAX_PEDIGREE_GENERATIONS"]		= "Soy ağacı çizgesinde maksimum nesil sayısı";
$pgv_lang["MAX_DESCENDANCY_GENERATIONS"]	= "Şahsı izleyen nesiller çizgesinde maksimum nesil sayısı";
$pgv_lang["USE_RIN"]			= "GEDCOM - Kişisel numaralarının yerine RIN# kullan";
$pgv_lang["PEDIGREE_ROOT_ID"]		= "Soy ağacı ya da şahsı izleyen nesiller çizgesinde kullanılacak ilk şahıs";
$pgv_lang["GEDCOM_ID_PREFIX"]		= "GEDCOM - Kişisel numaralarının ön eki";
$pgv_lang["SOURCE_ID_PREFIX"]		= "Kaynak numaralarının ön eki";
$pgv_lang["REPO_ID_PREFIX"]		= "Havuz numaralarının ön eki";
$pgv_lang["PEDIGREE_FULL_DETAILS"]	= "Soy ağacı ya da şahsı izleyen nesiller çizgesinde doğum ve ölüm detaylarını göster";
$pgv_lang["PEDIGREE_LAYOUT"]		= "Genel soy ağacı çizgesinin düzeni";
$pgv_lang["SHOW_EMPTY_BOXES"]		= "Soy ağacı çizgelerinde boş kutuları göster";
$pgv_lang["ZOOM_BOXES"]			= "Çizgelerdeki kutuların büyültüp küçültmesine izin ver";
$pgv_lang["LINK_ICONS"]			= "Çizgelerdeki bağlantı kutularının otomatik açılmasına izin ver";
$pgv_lang["ABBREVIATE_CHART_LABELS"]			= "Çizgelerdeki hadise başlıklarını kısalt";
$pgv_lang["SHOW_PARENTS_AGE"]			= "Ebeveyn yaşlarını çocukların doğum tarihinin yanında göster";
$pgv_lang["AUTHENTICATION_MODULE"]	= "Dogrulama modülü dosyası";
$pgv_lang["HIDE_LIVE_PEOPLE"]		= "Mahremiyet kurallarını kullan";
$pgv_lang["REQUIRE_AUTHENTICATION"]	= "Ziyaretçi denetlemesini kullan";
$pgv_lang["WELCOME_TEXT_AUTH_MODE"]	= "Ziyaretçi denetlemesi yapılacaksa giriş sayfasında kullanılacak karşılama mesajı";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT1"]	= "Her ziyaretçinin üye isteminde bulunabileceğini söyleyen önceden tanımlanmış mesaj";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT2"]	= "Her üyelik istemine yöneticinin karar vereceğini söyleyen önceden tanımlanmış mesaj";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT3"]	= "Ancak akrabaların üye olabileceğini söyleyen önceden tanımlanmış mesaj";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_OPT4"]	= "Aşağıdaki ziyaretçi karşılama mesajını seç";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_CUST"]	= "Ziyaretçi denetlemesi yapılacaksa kullanılacak özel karşılama mesajı";
$pgv_lang["WELCOME_TEXT_AUTH_MODE_CUST_HEAD"]	= "Özel karşılama mesajı için standart başlık";
$pgv_lang["CHECK_CHILD_DATES"]		= "Çocuklarin yaşlarını denetle";
$pgv_lang["MAX_ALIVE_AGE"]		= "Bir şahsın öldüğünü farzetmek için yaş sınırı";
$pgv_lang["SHOW_GEDCOM_RECORD"]		= "Ziyaretçiler sade GEDCOM kayıtlarını görebilir";
$pgv_lang["ALLOW_EDIT_GEDCOM"]		= "Çevrimiçi düzenlemeye izin ver";
$pgv_lang["INDEX_DIRECTORY"]		= "İndeks dosyalarının dizini";
$pgv_lang["ALPHA_INDEX_LISTS"]		= "Uzun listeleri ilk harften kes";
$pgv_lang["NAME_FROM_GEDCOM"]		= "Gösterilen isimi GEDCOM içinden kullan";
$pgv_lang["SHOW_ID_NUMBERS"]		= "Şahsi numaraları isimlerle göster";
$pgv_lang["SHOW_FAM_ID_NUMBERS"]	= "Aile numaraları ailerlerle göster";
$pgv_lang["SHOW_PEDIGREE_PLACES"]	= "Şahsi kutularda gösterilecek yer isimlerinin derinliği";
$pgv_lang["MULTI_MEDIA"]		= "Mültimedya özelliklerine izin ver";
$pgv_lang["MULTI_MEDIA_DB"]		= "Mültimedya kontrollü veritabanı kullan";
$pgv_lang["MEDIA_EXTERNAL"]		= "Bağlantıları muhafaza et";
$pgv_lang["MEDIA_DIRECTORY"]		= "Mültimedya dizini";
$pgv_lang["MEDIA_DIRECTORY_LEVELS"]	= "Mültimedya dizin derinliği";
$pgv_lang["SHOW_HIGHLIGHT_IMAGES"]	= "Vurgu resimini şahısların kutularında göster";
$pgv_lang["USE_THUMBS_MAIN"]		= "Şahsi sayfada tırnak resimleri ana resim yerine kullan";
$pgv_lang["ENABLE_CLIPPINGS_CART"]	= "Parça toplama mahfazasını kullan";
$pgv_lang["HIDE_GEDCOM_ERRORS"]		= "GEDCOM hatalarını sakla";
$pgv_lang["WORD_WRAPPED_NOTES"]		= "Notların kesildiği yerde boşluk ekle";
$pgv_lang["SHOW_CONTEXT_HELP"]		= "? yardım bağlantılarını varsayım olarak göster";
$pgv_lang["COMMON_NAMES_THRESHOLD"]	= "\"Yaygın soy isim\" için minimum miktar";
$pgv_lang["COMMON_NAMES_ADD"]		= "\"Yaygın soy isim\" listesine katılacak soy isimler (virgül ile ayrılacak)";
$pgv_lang["COMMON_NAMES_REMOVE"]	= "\"Yaygın soy isim\" listesinden silinecek soy isimleri (virgül ile ayrılacak)";
$pgv_lang["HOME_SITE_URL"]		= "Ana sitenin URL adresi";
$pgv_lang["HOME_SITE_TEXT"]		= "Ana sitenin genel mesajı";
$pgv_lang["CONTACT_EMAIL"]		= "Nesep tetkiki için temas edilecek üye";
$pgv_lang["CONTACT_METHOD"]		= "İlişki türü";
$pgv_lang["WEBMASTER_EMAIL"]		= "Teknik yardım için temas edilecek üye";
$pgv_lang["SUPPORT_METHOD"]		= "Destek türü";
$pgv_lang["FAVICON"]			= "\"Sık kullanılanlar\" (favorites) simgesi";
$pgv_lang["THEME_DIR"]			= "Tema dizini";
$pgv_lang["TIME_LIMIT"]			= "PHP zaman sınırlaması";
$pgv_lang["PGV_SESSION_SAVE_PATH"]	= "Oturum (session) hafızalama yolu";
$pgv_lang["SERVER_URL"]			= "PhpGedView URL adresi";
$pgv_lang["LOGIN_URL"]			= "Giriş URL adresi";
$pgv_lang["PGV_SESSION_TIME"]		= "Oturum (session) zaman aşımı";
$pgv_lang["SHOW_STATS"]			= "İşletim istatistiklerini göster";
$pgv_lang["SHOW_COUNTER"]		= "Sayaçları göster";
$pgv_lang["USE_REGISTRATION_MODULE"]	= "Ziyaretçilerin üyelik istemine izin ver";
$pgv_lang["ALLOW_USER_THEMES"]		= "Üyelere kendi temalarını seçme iznini ver";
$pgv_lang["CREATE_GENDEX"]		= "GENDEX dosyalarını yarat";
$pgv_lang["LOGFILE_CREATE"]		= "Sistemin günlüklerini arşivle";
$pgv_lang["PGV_MEMORY_LIMIT"]		= "Maksimum hafıza sınırı";
$pgv_lang["PGV_STORE_MESSAGES"]		= "Mesajların online hafıza edilmesine izin ver";
$pgv_lang["ALLOW_THEME_DROPDOWN"]	= "Tema değiştirmek için açılırliste göster";
$pgv_lang["META_SURNAME_KEYWORDS"]		= "\"Yaygın soy isimleri\" Keywords META alanına ekle";
$pgv_lang["review_readme"]		= "Bu PhpGedView yazılımını yapılandırmaya devam etmeden önce <a href=\"readme.txt\" target=\"_blank\">readme.txt</a> dosyasını okumanızı tavsiye ederiz.<br /><br />";
$pgv_lang["return_editconfig"]		= "Bu yapılandırma sayfasına her zaman tarayıcınızda <b>editconfig.php</b> dosyasını açarak ya da <b>İdare / Ayarlar</b> sayfasındaki <b>Genel ayarlar</b> bağlantısını tıklayarak geri dönebilirsiniz.<br />";
$pgv_lang["return_editconfig_gedcom"]	= "Bu yapılandırma sayfasına her zaman tarayıcınızda <b>editconfig_gedcom.php</b> dosyasını açarak ya da <b>İdare / Ayarlar</b> sayfasındaki <b>GEDCOM - Veritabanı ayarları</b> bağlantısını tıklayarak açılan <b>Aktüel GEDCOM veritabanları</b> sayfasında ayarlamak istediğiniz GEDCOM Veritabanı yanındaki <b>İşle</b> bağlantısına tıklayarak geri dönebilirsiniz.<br />";
$pgv_lang["save_config"]		= "Ayarları hafıza et";
$pgv_lang["download_here"]		= "Dosyayı indirmek için buraya tıkla.";
$pgv_lang["download_gedconf"]		= "GEDCOM ayarları dosyasını indir.";

$pgv_lang["download_file"]		= "Dosyayı indir";
$pgv_lang["upload_to_index"]		= "Dosyayı indeks dizinine yolla:";

//-- edit privacy messages
$pgv_lang["edit_privacy"]		= "Mahremiyet ayarlarını işle";
$pgv_lang["edit_privacy_title"]		= "GEDCOM mahremiyet ayarlarını işle";
$pgv_lang["PRIV_PUBLIC"]		= "Herkeze göster";
$pgv_lang["PRIV_USER"]			= "Sırf tasdik edilmiş ziyaretçiye göster";
$pgv_lang["PRIV_NONE"]			= "Sırf yöneticilere göster";
$pgv_lang["PRIV_HIDE"]			= "Yöneticilerden bile sakla";
$pgv_lang["save_changed_settings"]	= "Değişiklikleri hafıza et";
$pgv_lang["add_new_pp_setting"]		= "Şahıs mahremiyetine yeni ekleme yap";
$pgv_lang["add_new_up_setting"]		= "Üye mahremiyetine yeni ekleme yap";
$pgv_lang["add_new_gf_setting"]		= "Genel hadise mahremiyetine yeni ekleme yap";
$pgv_lang["add_new_pf_setting"]		= "Şahsi hadise mahremiyetine yeni ekleme yap";
$pgv_lang["add_new_pf_setting_indi"]	= "Kişisel şahsi hadise mahremiyetine yeni ekleme yap";
$pgv_lang["add_new_pf_setting_source"]	= "Şahsi kaynak mahremiyetine yeni ekleme yap";
$pgv_lang["privacy_indi_id"]		= "Kişisel İD numarası";
$pgv_lang["privacy_source_id"]		= "Kaynak İD numarası";
$pgv_lang["privacy_source"]		= "Kaynak";
$pgv_lang["file_read_error"]		= "H A T A !!! Mahremiyet dosyasını okuyamadım!";
$pgv_lang["edit_exist_person_privacy_settings"]	= "Varolan şahıs mahremiyeti ayarını işle";
$pgv_lang["edit_exist_user_privacy_settings"]	= "Varolan üye mahremiyeti ayarını işle";
$pgv_lang["edit_exist_global_facts_settings"]	= "Varolan genel hadise mahremiyeti ayarını işle";
$pgv_lang["edit_exist_person_facts_settings"]	= "Varolan şahsi hadise mahremiyeti ayarını işle";
$pgv_lang["general_privacy"]			= "Genel mahremiyet ayarları";
$pgv_lang["person_privacy"]				= "Şahıs mahremiyeti ayarları";
$pgv_lang["user_privacy"]				= "Üye mahremiyeti ayarları";
$pgv_lang["global_facts"]				= "Genel hadise mahremiyeti ayarları";
$pgv_lang["person_facts"]				= "Şahsi hadise mahremiyeti ayarları";
$pgv_lang["general_settings"]		= "Genel mahremiyet ayarları";
$pgv_lang["person_privacy_settings"]	= "Şahsi mahremiyet ayarları";
$pgv_lang["user_privacy_settings"]	= "Üye mahremiyet ayarları";
$pgv_lang["accessible_by"]		= "Kime gösterilsin?";
$pgv_lang["hide"]			= "Sakla";
$pgv_lang["show_question"]		= "Göster?";
$pgv_lang["user_name"]			= "Üye rumuzu";
$pgv_lang["name_of_fact"]		= "Hadisenin ismi";
$pgv_lang["choice"]			= "Seçenek";
$pgv_lang["fact_show"]			= "Hadiseyi göster";
$pgv_lang["fact_details"]		= "Hadiselerin ayrıntılarını göster";
$pgv_lang["privacy_header"]		= "Mahremiyet ayarlarını düzenleme aracı";
$pgv_lang["unable_to_find_privacy_indi"]	= "Seçilen numaraya bağlı hiç bir şahıs bulunamadı";
$pgv_lang["SHOW_LIVING_NAMES"]		= "Yaşayan şahısların ismini göster";
$pgv_lang["SHOW_RESEARCH_LOG"]		= "\"ResearchLog\" modülünü göster";
$pgv_lang["USE_RELATIONSHIP_PRIVACY"]	= "Akrabalık mahremiyetini kullan";
$pgv_lang["MAX_RELATION_PATH_LENGTH"]	= "Maksimum akrabalık derecesi";
$pgv_lang["CHECK_MARRIAGE_RELATIONS"]	= "Akrabalık derecesini denetle";
$pgv_lang["SHOW_DEAD_PEOPLE"]		= "Hayatta olmayan şahısları göster";
$pgv_lang["help_info"]			= "Her nesne üzerine kırmızı &quot;?&quot; (soru işaretlerine) tıklayıp yardım elde edebilirsiniz.<br />";
$pgv_lang["select_privacyfile_button"]	= "Mahremiyet dosyasını seç";
$pgv_lang["PRIVACY_BY_YEAR"]		= "Mahremiyeti hadisenin yalşı ile sınırla";

//-- language edit utility
$pgv_lang["edit_langdiff"]		= "Dil dosyalarının içeriğini ve dil ayarlarını işle";
$pgv_lang["bom_check"]			= "Dil dosyası içinde BOM denetimi";
$pgv_lang["edit_lang_utility"]		= "Dil dosyalarının içeriğini değiştirme aracı";
$pgv_lang["language_to_edit"]		= "İçeriği değiştirilecek dil";
$pgv_lang["file_to_edit"]		= "İçeriği değiştirilecek dosya tipi";
$pgv_lang["check"]			= "Denetle";
$pgv_lang["lang_save"]			= "Hafıza et";
$pgv_lang["contents"]			= "İçerik";
$pgv_lang["listing"]			= "Gösterilen dosya";
$pgv_lang["no_content"]			= "İçerik yok";
$pgv_lang["editlang_help"]		= "Dil dosyasındaki mesajın içeriğinini değiştir";
$pgv_lang["cancel"]			= "Vazgeç";
$pgv_lang["savelang_help"]		= "İçeriği değiştirilen mesajı hafıza et";
$pgv_lang["original_message"]		= "Orjinal mesaj";
$pgv_lang["message_to_edit"]		= "Değiştirilecek mesaj içeriği";
$pgv_lang["changed_message"]		= "Değiştirmiş içerik";
$pgv_lang["message_empty_warning"]	= "-&lt; Dikkat!!! Bu mesajın içeriği [#LANGUAGE_FILE#] dosyasında boştur &gt;-";
$pgv_lang["language_to_export"]		= "İhraç edilecek dil";
$pgv_lang["export_lang_utility"]	= "Dil dosyasını ihraç etme aracı";
$pgv_lang["export"]			= "İhraç et";
$pgv_lang["export_ok"]			= "Yardım mesajları ihraç edilmiştir";
$pgv_lang["compare_lang_utility"]	= "Dil dosyalarını karşılaştırma aracı";
$pgv_lang["new_language"]		= "Kaynak dil";
$pgv_lang["old_language"]		= "İkinci dil";
$pgv_lang["compare"]			= "Karşılaştır";
$pgv_lang["comparing"]			= "Karşılaştırılan diller";
$pgv_lang["additions"]			= "Eklemeler";
$pgv_lang["no_additions"]		= "Ekleme yapılmamış";
$pgv_lang["subtractions"]		= "Silinenler";
$pgv_lang["no_subtractions"]		= "Hiç bir mesaj silinmemiştir";
$pgv_lang["config_lang_utility"]	= "Desteklenen dillerin ayarı";
$pgv_lang["active"]			= "Kullanımda";
$pgv_lang["edit_settings"]		= "Ayarları değiştir";
$pgv_lang["lang_edit"]			= "İşle";
$pgv_lang["lang_language"]		= "Dil";
$pgv_lang["lang_back"]			= "Dil dosyalarının içeriğini ve dil ayarlarını işlemek için ana sayfaya geri dön";
$pgv_lang["lang_back_admin"]		= "İdare / Ayarlar sayfasına geri dön";
$pgv_lang["lang_back_manage_gedcoms"]	= "GEDCOM ayarlandırma sayfasına geri dön";
$pgv_lang["lang_name_czech"]		= "Çekçe";
$pgv_lang["lang_name_chinese"]		= "Çince";
$pgv_lang["lang_name_danish"]		= "Danca";
$pgv_lang["lang_name_dutch"]		= "Hollandaca";
$pgv_lang["lang_name_english"]		= "İngilizce";
$pgv_lang["lang_name_finnish"]		= "Fince";
$pgv_lang["lang_name_french"]		= "Fransızca";
$pgv_lang["lang_name_german"]		= "Almanca";
$pgv_lang["lang_name_hebrew"]		= "İbranice";
$pgv_lang["lang_name_hungarian"]	= "Macarca";
$pgv_lang["lang_name_italian"]		= "İtalyanca";
$pgv_lang["lang_name_norwegian"]	= "Norveççe";
$pgv_lang["lang_name_polish"]		= "Lehçe";
$pgv_lang["lang_name_portuguese"]	= "Portekizce";
$pgv_lang["lang_name_portuguese-br"]	= "Brezilya Portekizcesi";
$pgv_lang["lang_name_russian"]		= "Rusça";
$pgv_lang["lang_name_spanish"]		= "İspanyolca";
$pgv_lang["lang_name_spanish-ar"]	= "Latin Amerika İspanyolcası";
$pgv_lang["add_new_lang_button"]	= "Yeni dil ekle";
$pgv_lang["hide_translated"]		= "Tercümesi tamamlanmış olanları sakla";
$pgv_lang["lang_file_write_error"]	= "Hata!!!<br /><br />Secilen dil dosyasina degisiklikleri yazamadım!<br />Lütfen [#lang_filename#] adlı dosyaya yazma izinini denetleyin ve ondan sonra bu adımı tekrar deneyin.";
$pgv_lang["no_open"]			= "H A T A !!!<br /><br />#lang_filename# isimli dosyayı okuyamadım";
$pgv_lang["lang_name_swedish"]		= "İsveççe";
$pgv_lang["lang_name_turkish"]		= "Türkçe";
$pgv_lang["lang_name_greek"]		= "Yunanca";
$pgv_lang["lang_name_arabic"]		= "Arapça";
$pgv_lang["lang_new_language"]		= "Yeni Dil";
$pgv_lang["original_lang_name"]		= "Bu dilin #D_LANGNAME# dilindeki gerçek ismi";
$pgv_lang["lang_shortcut"]		= "Dil dosyaları için kısaltma";
$pgv_lang["lang_langcode"]		= "Dil belirleme kodları";
$pgv_lang["lang_filenames"]		= "Dil dosyaları";
$pgv_lang["lang_filename"]		= "Dil dosyası";
$pgv_lang["lang_filename_help"]		= "Standart dil tercüme dosyasının isimi ve yolu.";
$pgv_lang["config_filename"]		= "Ayarlara ait yardım dosyası";
$pgv_lang["config_filename_help"]	= "Yapılandırma için gerekli dil tercüme dosyasının isimi ve yolu.";
$pgv_lang["facts_filename"]		= "Hadise dosyası";
$pgv_lang["facts_filename_help"]	= "GEDCOM hadiseleri için gerekli dil tercüme dosyasının isimi ve yolu.";
$pgv_lang["help_filename"]		= "Yardım dosyası";
$pgv_lang["help_filename_help"]		= "Yardim için gerekli dil tercüme dosyasının isimi ve yolu.";
$pgv_lang["flagsfile"]			= "Bayrak dosyasının ismi";
$pgv_lang["flagsfile_help"]		= "Seçilen dilin ulusal bayrağını içeren resim dosyasının isimi ve yolu.";
$pgv_lang["text_direction"]		= "Yazım yönü";
$pgv_lang["date_format"]		= "Tarih biçimi";
$pgv_lang["time_format"]		= "Saat biçimi";
$pgv_lang["week_start"]			= "Haftanın ilk günü";
$pgv_lang["name_reverse"]		= "Önce soy isim";
$pgv_lang["ltr"]			= "Soldan sağa";
$pgv_lang["rtl"]			= "Sağdan sola";
$pgv_lang["file_does_not_exist"]	= "HATA! Bu dosya yoktur...";
$pgv_lang["alphabet_upper"]		= "Alfabe büyük harf";
$pgv_lang["alphabet_lower"]		= "Alfabe küçük harf";
$pgv_lang["lang_config_write_error"]	= "Hata!!! [language_settings.php] adlı dosyaya yazamadım. Lütfen dosya ve dizin izinlerini denetleyin ve ondan sonra bu adımı tekrar deneyin.";
$pgv_lang["lang_save_success"]		= "#PGV_LANG# dilinin değişikliklerini başarı ile hafıza ettim.";
$pgv_lang["translation_forum"]		= "SourceForge sitesindeki PhpGedView tercüme forumuna bağlantı";
$pgv_lang["system_time"]		= "Sistemin aktüel saati:";
$pgv_lang["gedcom_not_imported"]	= "Bu GEDCOM veritabanı daha ithal edilmemiştir.";
$pgv_lang["add_new_language"]		= "Yeni bir dil için gerekli olan dosya ve ayarları ekle";

//-- User Migration Tool messages
$pgv_lang["um_header"]			= "Üye verilerini kaydırma aracı";
$pgv_lang["um_file_not_created"]	= "Dosya yaratılmadı.";
$pgv_lang["um_backup"]			= "Yedekleme";
$pgv_lang["um_bu_config"]		= "PhpGedView yapılandırma dosyası";
$pgv_lang["um_bu_gedcoms"]		= "GEDCOM dosyaları";
$pgv_lang["um_bu_gedsets"]		= "GEDCOM ayarları, yapılandırma ve mahremiyet dosyaları";
$pgv_lang["um_bu_logs"]			= "GEDCOM sayacları, Arama- ve PhpGedView-Günlük dosyaları";
$pgv_lang["um_mk_bu"]			= "Yedeklemeyi yap";
$pgv_lang["um_nofiles"]			= "Yedekleme için hiç bir dosya bulunamadı.";
$pgv_lang["um_files_exist"]		= "Bir ya da birkaç dosya mevcuttur. Onların üstüne yazmak istiyormusunuz?";
$pgv_lang["SHOW_SOURCES"]		= "Kaynakları göster";
$pgv_lang["SPLIT_PLACES"]		= "Düzenleme kipinde yerleri böl";
$pgv_lang["UNDERLINE_NAME_QUOTES"]	= "Tırnak işaretleri arasındaki isimlerin altını çiz";
$pgv_lang["SHOW_LDS_AT_GLANCE"]		= "Çizge kutularında LDS kurallarının kodlarını göster";
$pgv_lang["GEDCOM_DEFAULT_TAB"]		= "Şahısların bilgileri sayfasında gösterilecek ilk sekme";
$pgv_lang["SHOW_MARRIED_NAMES"]		= "Şahıs listesinde evlilik isimlerini göster";
$pgv_lang["SEARCHLOG_CREATE"]		= "Arama-Günlük dosyalarını arşivle";
$pgv_lang["CHART_BOX_TAGS"]		= "Çizgelerde gösterilecek diğer hadiseler";

?>
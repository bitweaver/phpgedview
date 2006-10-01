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
if (preg_match("/help_text\...\.php$/", $_SERVER["SCRIPT_NAME"])>0) {
print "You cannot access a language file directly.";
exit;
}

//-- GENERAL
$pgv_lang["help_header"]			= "Aşağıdaki konu üzerine ek bilgi:";
$pgv_lang["more_config_help"]			= "<br /><b>Ayrıntılı yardım</b><br />Ayrıntılı yardım form içinde mevcuttur. Bu yardıma ulaşmak için lütfen gerektiğinde <b>?</b> (soru işaretlerine) tıklayın.<br />";

//-- Pages Help-messages
//-- Index-page
$pgv_lang["index_help"]				= "~Tercümanlara~<br />Bu yardım mesajı daha tamamlanmamıştır. Mesajı tercüme edebilirsiniz ama daha bir kaç değişiklik yapılacaktır.<br />Bu size bir yardım sayfasının nasıl görünmesinin gerektiğini göstermek içindir.</b><br /><br /><br /><b>İNDEKS SAYFASI.</b><br /><br /><b>*)</b> ile işaretlenmiş kelimeler bu yardım mesajının sonunda açıklanmıştır.<br /><br />Bu giriş sayfası*) aktüel olarak kullanınlan GEDCOM dosyasının*) ilk sayfasıdır.<br /><br />v3.0 versiyonunun hayata geçirilmesinden beri PhpGedView*) yazılımının yüzü değişmiştir.<br />Artık daha iyi bir menu sistemi vardır, giriş sayfası değişmiştir ve bir çok yeni öğe eklenmiş yada geliştirilmiştir.<br /><br />#pgv_lang[menu_help]#<br />#pgv_lang[menuu_help]#<br /><br />#pgv_lang[index_portal_blocks_help]#<br /><br /><br /><br />At the right of some links, drop-down boxes or buttons you may find a Question Mark. Clicking this ? will pop-up this little help screen with some more information<br /><br />* LOGIN<br />Although you can login on almost every page of this program, mostly you want to do so at the first page, since you can only access the permitted information when you are logged in.<br />You login by clicking the link \"Login\" at the top of the page.<br />Visitors who like to apply for a username and password, also click the \"Login\" link.<br /><br /><br /><br />Some explanations of words, used in this help text:<br /><b>Portal</b><br />Also on this web site a Portal is like a lobby of a restaurant. It is the place where you enter, but you can also find important information like explainations, menus etc..<br /><b>Gedcom file</b><br />The Gedcom file is the file that contains all the information about the family.<br />All facts, dates, events, etc. are stored in the Gedcom file.<br />A Gedcom file has to follow strict rules, because it is exchangeable between many programs, independent of platforms and/or systems.<br /><b>phpGedView</b><br />phpGedView (or PGV) is a program, designed by John Finlay, to view the Gedcom files on line.<br />As you see, this program is not just putting static pages on the web, but is dynamic and can be customized in many ways.<br />John started developing the program on his own, but in the meanwhile a whole, international, team of developers and translators is working together to improve the program.<br />Apart from just viewing the Gedom files, the user can add and edit events on line now.<br /><br /><br />BU YARDIM MESAJI DAHA GELİŞTİRİLMEKTEDİR.";

//-- Pedigree-page
$pgv_lang["help_pedigree.php"]			= "~SOY AĞACI / SEÇERE SAYFASI~<br /><br />Çoğu kullanıcı yada ziyaretçi bir soy ağacı yada seçerenin ne olduğunu herhalde biliyordur. Ama yinede bizim kullandığımğz soy ağacı üzerine bır kaç bilgi aktaralım.<br /><br />The Pedigree is, in fact, an enumeration of all ancestors of the starting person.<br />When you entered this site, the starting person is the person in the box at the left (when you log in, you can choose your own starting person).<br /><br />So, \"All the ancestors\" means father and mother, and their parents and so on.<br />On this site you see the Pedigree in the graphical way.<br />You don't have to struggle through pages of text, to find out where your ancestors are.<br />All individuals are displayed in the name boxes on the screen.<br /><br />* THE NAME BOXES ON THE PEDIGREE<br />If the pedigree page is set to show details, you will see the persons name, and the dates of birth and death.<br />You can click on a persons name, and that will bring you directly to the Indivudual Details page.<br />When \"Show details\" is on there are two icons inside the name box.<br /><br />* PEDIGREE ICON INSIDE THE NAME BOX<br />When the option \"Show Details\" is on, you see a Pedigree Icon at the inside of the Name Box. Depending on the site-settings you have to hoover over the Icon, or to click on it.<br />So, when you click on, or hoover over, this icon, a small sub menu appears.<br />The item \"Pedigree Tree\" and \"Descendancy Chart\" are similar to those items in the main menu, but the difference is that the starting person is now the individual of the name box.<br />Further you see the \"Family with spouse\". You see the name of the spouse, followed by the name(s) of the children.<br />All names are clickable.<br /><br />* MAGNIFYING GLASS INSIDE THE NAME BOX<br />Also depending of the site-settings, you have to hoover over it, or to click on it.<br />This magnifies the name box, so that some more details will be displayed.<br />You see more dates and events. Names are clickable.<br /><br />* OKLAR<br />On the left or right of the utmost left or utmost right nameboxe(s) you may see Arrows.<br />Clicking on these arrow will \"shift\" the screen in the direction of the arrow.<br /><br />More help is available by clicking the <b>?</b> behind the boxes or links, on the page.";
$pgv_lang["show_full_help"]			= "~DETAYLARI GÖSTER YADA SAKLA~<br /><br />Bu seçenek ile şahıs kutularında gösterilen tüm detayları ya gösterebilir yada saklayabirsiniz.<br />Detaylar saklandığında ekranınızda ayni anda normal ayara oranla bir kaç kutu daha görebilirsiniz.<br /><br />Eğer detayları saklamayı tercih ediyorsanız but detayları farenizi büyüteçin (eğer bu sitenin yöneticisi ayarlar bölümünde bu seçeneğe izin vermişse) üzerine getirerek görebilirsiniz.<br /><br />If you normally have to click on the magnifying glass to zoom in, then you can reveal details as well by clicking on the name box.<br />If you normally zoom in, just by hoovering over the magnifying glass, then it will work the same way when you hoover over the \"simple\"box.";
$pgv_lang["talloffset_help"]			= "~EKRANIN BİÇİM AYARI~<br /><br />Bu seçenek ile ekranın biçimini ayarlayabilirsiniz.<br />Bu ayarı niye değiştirmeniz gerekebilir?<br />Eğer ekranda gördüğünüzü printerinizde yazdırmak istiyorsanız yada ekranınızın ölçümü normal bir ekranınkine benzemiyorsa, bu seçenek size yardımcı olabilir";
$pgv_lang["rootid_help"]			= "~YENİ KÖK ŞAHIS~<br /><br />Diğer bir şahısın soy ağacını görmek istiyorsanız yandaki kutya bu şahısın İD (tanımlayıcı) numarasını yazabilirsiniz. <br />Görmek istediğiniz şahısın İD numarasını bilmiyorsanız <b>>Şahıs seç<</b> bağlantısını kullanıp şahısı dırek olarak bir listeden seçebilirsiniz.<br /><br /><b>İD NUMARASI</b><br />PhpGedView yazılımında kullanılan İD numarası şahıslarının kimlik numarası değildir!<br />Burada kullanılan İD numarası sırf bu GEDCOM veritabanında bulunan şahıslara otomatik olarak verilmiş bir tanımlayıcı numarasıdır!";
$pgv_lang["PEDIGREE_GENERATIONS_help"]		= "~GÖSTERİLEN NESİL SAYISI~<br /><br />Bu sayı sayfada görünen soy ağacında ayni anda gösterilen nesillerin sayısıdır.<br />Siyin için uygun olan sayı ekranınızın ölçüsüne ve detayları saklayıp saklamadığınıza bağlıdır.";

//-- LOGIN-page
$pgv_lang["login_page_help"]			= "~GİRİŞ (LOGİN) SAYFASI~<br /><br />Bu sayfadan bu siteye giriş yapabilirsiniz.<br />Eğer sitenin üyesiyseniz unuttuğunuz bir şifrenin yerine yeni bir şifre, yada siteye yeni üyelik isteminde bulunabilirsiniz.<br />Daha ayrıntılı yardımı kutularının yada bağlantıların yanındaki <b>?</b> işaretine tıklayarak bulabilirsiniz.";
$pgv_lang["username_help"]			= "~RUMUZ (USER NAME)~<br /><br />Bu kutuya rumuzunuzu işleyebilirsiniz.<br />Lütfen dikkat edin: Sistem rumuzunuzda büyük ve küçük harf arasında ayırd ediyor.";
$pgv_lang["password_help"]			= "~ŞİFRE~<br /><br />Bu kutuya rumuzunuza bağlı olan şifrenizi işleyebilirsiniz.<br />Lütfen dikkat edin: Sistem şifrenizde büyük ve küçük harf arasında ayırd ediyor.";
$pgv_lang["new_password_help"]			= "~YENİ ŞİFRE İSTEMİ~<br /><br />Eğer sitenin üyesiyseniz bu bağlantıya tıklayıp unuttuğunuz şifrenin yerine yeni bir şifre isteminde bulunabilirsiniz.";
$pgv_lang["new_user_help"]			= "~YENİ ÜYELİK BAŞVURUSU~<br /><br />Eğer bu sitenin ziyaretçisiyseniz ve siteye üye olmak istiyorsanız bu bağlantıya tıklayabilirsiniz.";
$pgv_lang["mygedview_login_help"]		= "Şahsi GedView bölümüne ulaşmak için bu sitenin üyesi olmanız gerekir.<br /><br />Şahsi GedView bölümünde sizin için önemli olan şahısları sık kullananlarınıza ekleyebilir, şahsi günlük tutabilir, siteye ait mesajlarınızı düzenleyebilir, siteze bağlanmış diğer üyeleri görebilir ve bu phpGedView yazılımını kendi isteklerinize göre özelleştirebilirsiniz.<br /><br />Şahsi GedView bölümüne ulaşmak için aşağıdaki alanlara üyelik rumuzunuzu, şifrenizi ve gerekli diğer verileri işleyin.";

?>
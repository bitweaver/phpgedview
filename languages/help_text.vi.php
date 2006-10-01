<?php
/**
 * Vietnamese Language file for PhpGedView.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2002 to 2005  Anton Luu and Lan Nguyen
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
 * @author Anton Luu
 * @author Lan Nguyen
 * @version $Id$
 */
if (preg_match("/help_text\...\.php$/", $_SERVER["PHP_SELF"])>0) {
print "Bạn không thể vào thẳng nhu liệu ngôn ngữ được.";
exit;
}


//-- GENERAL
$pgv_lang["tag_start"]				= "<font class=\"helpstart\">";
$pgv_lang["tag_end"]				= "</font>";
$pgv_lang["help_header"]			= "Chi tiết về:";
$pgv_lang["privacy_error_help"]			= "<b>PRIVATE DETAILS</b><br /><br />Có nhiều lý do bạn thấy cái bức điện này:<br /><br /><b>1. Bạn chưa có đăng ký</b><br />Dữ-kiện của các người còn sống vẫn còn bị đặt dưới dạng \"Riêng Tư\" cho GEDCOM này.  Những quí vị chưa có đăng ký chỉ có thể thấy lý lịch của những người quá cố. Xin dăng ký bằng cách bấm nút Login, rồi theo #pgv_lang[requestpassword]# link.<br /><br /><b>2. Bạn có tên và khẩu lệnh ...</b><br />Nhưng bạn chưa ký vào hay đã bất động quá giờ<br /><br /><b>3. Dữ-kiện hiện còn dưới dạng \"Riêng tư\"</b><br />Người mà bạn chọn đã yêu cầu người admin đặt họ dưới dạng \"Riêng tư\"<br />Một khi mà dữ-kiện đã được đặc trong dạng Riêng tư thì chỉ có administrators mới có thể xem được.<br /><br /><b>4. Ngòai \"Quan hệ\"</b><br />Dù cho bạn có là người năng dùng <b>và</b> logged in, bạn vẫn có thể gặp phải bức điện này nếu mà quan hệ họ hàng giửa bạn và người mà bạn kiếm đã vượt quá giới hạng đặt ra bởi người administrator của GEDCOM này.<br />Ví dụ:<br />Khi số đời (quan-hệ họ hàng) là <b>1</b>, bạn chỉ có thể thấy lý lịch của gia-dình bạn như cha, mẹ, anh chị em ruột (nhưng sẽ không thấy lý lịch của người phối ngẫu kể cả con cái của các anh chị em ruột)<br /><br />Khi số đời (quan-hệ họ hàng) là <b>2</b>, bạn sẽ thấy được lý lịch của những anh em rễ hay chị em dâu (nhưng sẽ không thấy vợ chồng của các cháu).<br />Số đO=`i càng nhiều, thì bạn càng thấy nhiều nghành xa hơn.<br /><br />Nêu bạn nghỉ là bạn hội đủ điều kiện để thấy những chi tiết cần thiết, xin liên lạc với administrator yết trên link của các trang.";
$pgv_lang["more_help"]				= "<br />Nếu cần trang giúp đở theo phạm vi, bật <b>#pgv_lang[cho xem Giúp đở theo phạm vi]#</b> (trên Help Menu) rồi bấm vào <b>?</b> sau chủ đề.<br />";
$pgv_lang["more_config_help"]			= "<br /><b>Giúp nửa</b><br />Có nhiều cách Giúp khác ngay ở trên trang. Bấm vào<b>?</b> sau các nhãn hiệu.<br />";
$pgv_lang["start_admin_help"]			= "+++ Bắt đầu phần chi-tiết về việc quản trị +++";
$pgv_lang["end_admin_help"]			= "--- Hết phần chi-tiết về quản trị ---";
$pgv_lang["multiple_help"]			= "<center>--- Phần Giúp đở tổng quát cho nhiều trang ---</center>";
$pgv_lang["header_general_help"]		= "<div class=\"name_head\"><center><b>TÀi LIỆU TỔNG QUÁT</b></center></div><br />";
$pgv_lang["best_display_help"]			= "~Màn ảnh~<br />PhpGedView được thiết lập cho các màn ảnh 1024x768 pixels.<br />Đó là tiêu chuẩn tối thiểu để cho tài liệu được trình bày gọn gàng đầy đủ.<br />Với màn ảnh ở độ thấp (ví dụ như là 800x600 pix.), bạn phải chịu khó kéo ngang để thấy phần còn lại của trang.<br />";
$pgv_lang["preview_help"]			= "~In~<br />Vì lý do thẩm mỹ, bấm Printer-friendly Version link sẽ xóa bớt đi những phần mà làm xấu bài như  (menus, \"input boxes\", extra \"links\", cái dấu hỏi cho phần Giúp đở theo phạm vi, etc.).<br /><br />Bạn sẽ thấy một cái Print link ở cuối trang Printer-friendly. Bấm vào cái link đó thì bản printer đối-thọai sẽ hiện lên. Khi in xong, bấm Back link thì màn ảnh sẻ trở lại trạng thái bình thường.<br /><br />Ghi chú: Mặc dù có một số các links trên \"Printer-friendly version\" đã bị dấu đi, số còn lại vẫn họat động như thường lệ.<br />";
$pgv_lang["readme_help"]			= "<center>Xem <a href=\"readme.txt\" target=\"_blank\"><b>Readme.txt</b></a> để biết thêm chi-tiết.</center>";
$pgv_lang["is_user_help"]			= "--- Không có chi thay đổi trong phần giúp đở dành cho người xử dụng. --- <br />--- Để tiết-kiệm chổ, chúng tôi không có viết gì thêm về vấn-đề quản-trị. ---";

?>

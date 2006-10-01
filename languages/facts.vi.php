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
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "Bạn không thể vào thẳng nhu-liệu ngôn ngữ được.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their English values
$factarray["ABBR"]	= "Viết Tắt";
$factarray["ADDR"]	= "Địa Chỉ";
$factarray["ADR1"]	= "Địa Chỉ 1";
$factarray["ADR2"]	= "Địa Chỉ 2";
$factarray["ADOP"]	= "Nhận Làm Con Nuôi";
$factarray["AFN"]	= "Hệ Số Gia Phả(AFN)";
$factarray["AGE"]	= "Tuổi";
$factarray["AGNC"]	= "Cơ Quan";
$factarray["ALIA"]	= "Tên Gọi Là";
$factarray["ANCE"]	= "Tổ Tiên";
$factarray["ANCI"]	= "Tâm Ý Của Tổ Tiên";
$factarray["ANUL"]	= "Hủy Bỏ";
$factarray["ASSO"]	= "Bạn Hữu, Cộng tác viên";
$factarray["AUTH"]	= "Tác Gỉa";
$factarray["BAPL"]	= "Bí Tích Rửa Tội Của LDS";
$factarray["BAPM"]	= "Bí Tích Rửa Tội";
$factarray["BARM"]	= "Bar Mitzvah";
$factarray["BASM"]	= "Bas Mitzvah";
$factarray["BIRT"]	= "Sinh";
$factarray["BLES"]	= "Được Ban Phép Lành";
$factarray["BLOB"]	= "Binary Data Object";
$factarray["BURI"]	= "Lể An Táng";
$factarray["CALN"]	= "Call Number";
$factarray["CAST"]	= "Giai Cấp trong Xả Hội";
$factarray["CAUS"]	= "Nguyên Do";
$factarray["CEME"]  = "Nghĩa Trang";
$factarray["CENS"]	= "Kiểm Tra";
$factarray["CHAN"]	= "Cập Nhật Hóa";
$factarray["CHAR"]	= "Bộ Chử";
$factarray["CHIL"]	= "Con";
$factarray["CHR"]	= "Lể Rửa Tội";
$factarray["CHRA"]	= "Lể Rửa Tội Cho Người Lớn";
$factarray["CITY"]	= "Thành-Phố";
$factarray["CONF"]	= "Bí-tích Thêm Sức";
$factarray["CONL"]	= "Bí-tích Thêm Sức của LDS";
$factarray["COPR"]	= "Bản Quyền";
$factarray["CORP"]	= "Công Ty";
$factarray["CREM"]	= "Hỏa Thiêu";
$factarray["CTRY"]	= "Quốc Gia";
$factarray["DATA"]	= "Tài Liệu";
$factarray["DATE"]	= "Ngày";
$factarray["DEAT"]	= "Tử";
$factarray["DESC"]	= "Những Người Nối Dõi";
$factarray["DESI"]	= "Tâm Ý Của Các Con Cháu";
$factarray["DEST"]	= "Mục Tiêu";
$factarray["DIV"]	= "Ly Dị";
$factarray["DIVF"]	= "Ly Thân";
$factarray["DSCR"]	= "Mô Tả";
$factarray["EDUC"]	= "Học Vấn";
$factarray["EMIG"]	= "Di Cư";
$factarray["ENDL"]	= "Vốn Cúng cho LDS";
$factarray["ENGA"]	= "Lể Đính-Hôn";
$factarray["EVEN"]	= "Sự Kiện";
$factarray["FAM"]	= "Gia Đình";
$factarray["FAMC"]	= "Gia Đình của Con";
$factarray["FAMF"]	= "Sổ Gia Đình";
$factarray["FAMS"]	= "Gia Đình của Chồng/Vợ";
$factarray["FCOM"]	= "Rước Lể Lần Đầu";
$factarray["FILE"]	= "Hồ Sơ";
$factarray["FORM"]	= "Khổ";
$factarray["GIVN"]	= "Tên Gọi";
$factarray["GRAD"]	= "Tốt Nghiệp";
$factarray["HUSB"]  = "Chồng";
$factarray["IDNO"]	= "Số Căn Cước";
$factarray["IMMI"]	= "Nhập Cảnh";
$factarray["LEGA"]	= "Người Thừa Kế";
$factarray["MARB"]	= "Rao Hôn-Phối";
$factarray["MARC"]	= "Giấy Giá Thú";
$factarray["MARL"]	= "Giấy Phép Hôn-Nhân";
$factarray["MARR"]	= "Hôn Lể";
$factarray["MARS"]	= "Thoả Thuận sau Hôn-Phối";
$factarray["MEDI"]	= "Lọai Môi Thể";
$factarray["NAME"]	= "Tên";
$factarray["NATI"]	= "Quốc Tịch";
$factarray["NATU"]	= "Nhập Tịch";
$factarray["NCHI"]	= "Các Con";
$factarray["NICK"]	= "Biệt Danh";
$factarray["NMR"]	= "Lập Gia Đình Bao Nhiêu Lần";
$factarray["NOTE"]	= "Ghi Chú";
$factarray["NPFX"]	= "Tước Hiệu";
$factarray["NSFX"]	= "Hậu Tố";
$factarray["OBJE"]	= "Tài Liệu Hữu Hình";
$factarray["OCCU"]	= "Nghề Nghiệp";
$factarray["ORDI"]	= "Sắc-lệnh";
$factarray["ORDN"]	= "Lể Tấn Phong";
$factarray["PAGE"]	= "Sự Biểu Dương Chi Tiết";
$factarray["PEDI"]	= "Dòng Dõi";
$factarray["PLAC"]	= "Địa Điểm";
$factarray["PHON"]	= "Điện Thọai";
$factarray["POST"]	= "Bưu Cục";
$factarray["PROB"]	= "Thủ Tục Chứng Thực Di Chúc";
$factarray["PROP"]	= "Bất Động Sản";
$factarray["PUBL"]	= "Xuất Bản";
$factarray["QUAY"]	= "Phẩm Chất của Tài Liệu";
$factarray["REPO"]	= "Nơi Tàng Trữ";
$factarray["REFN"]	= "Số Tham Khảo";
$factarray["RELA"]	= "Tình Họ Hàng";
$factarray["RELI"]	= "Tôn Giáo";
$factarray["RESI"]	= "Sống ở";
$factarray["RESN"]	= "Hạn Chế";
$factarray["RETI"]	= "Hưu Trí";
$factarray["RFN"]	= "Hồ-sơ số";
$factarray["RIN"]	= "Số lý-lịch";
$factarray["ROLE"]	= "Vai Trò";
$factarray["SEX"]	= "Phái";
$factarray["SLGC"]	= "Niêm phong LDS Con";
$factarray["SLGS"]	= "Niêm Phong LDS Chồng/Vợ";
$factarray["SOUR"]	= "Căn Nguyên";
$factarray["SPFX"]	= "Tước Hiệu của Tên Họ";
$factarray["SSN"]	= "Số An-Sinh Xả-Hội";
$factarray["STAE"]	= "Tiểu Bang";
$factarray["STAT"]	= "Địa Vị";
$factarray["SUBM"]	= "Người Đệ Trình";
$factarray["SUBN"]	= "Đệ Trình";
$factarray["SURN"]	= "Tên Họ";
$factarray["TEMP"]	= "Chùa hay Đền";
$factarray["TEXT"]	= "Nguyên Văn";
$factarray["TIME"]	= "Giờ";
$factarray["TITL"]	= "Tên";
$factarray["TYPE"]	= "Phân Lọai";
$factarray["WIFE"]  = "Vợ";
$factarray["WILL"]	= "Chúc Thư";
$factarray["_EMAIL"]	= "Địa chỉ thư điện tử";
$factarray["EMAIL"]	= "Địa chỉ thư điện tử";
$factarray["_TODO"]	= "Việc Phải Làm";
$factarray["_UID"]	= "Universal Identifier";
$factarray["_PGVU"]	= "Lần Đổi Cuối Bởi";
$factarray["SERV"] = "Server ở xa";
$factarray["_GEDF"] = "Tập Tin GEDCOM";
$factarray["_PRIM"]	= "Tận dụng ảnh này";
$factarray["_THUM"]	= "Dùng biểu tượng của ảnh này?";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]	= "Sức Khoẻ";
$factarray["_DEG"]	= "Bằng Cấp";
$factarray["_MILT"]	= "Nghỉa Vụ Quân-Đội";
$factarray["_SEPR"]	= "Ly Thân";
$factarray["_DETS"]	= "Tữ của Một Người Chồng/Vợ";
$factarray["CITN"]	= "Quốc Tịch";
$factarray["_FA1"]	= "Sự kiện 1";
$factarray["_FA2"]	= "Sự kiện 2";
$factarray["_FA3"]	= "Sự kiện 3";
$factarray["_FA4"]	= "Sự kiện 4";
$factarray["_FA5"]	= "Sự kiện 5";
$factarray["_FA6"]	= "Sự kiện 6";
$factarray["_FA7"]	= "Sự kiện 7";
$factarray["_FA8"]	= "Sự kiện 8";
$factarray["_FA9"]	= "Sự kiện 9";
$factarray["_FA10"]	= "Sự kiện 10";
$factarray["_FA11"]	= "Sự kiện 11";
$factarray["_FA12"]	= "Sự kiện 12";
$factarray["_FA13"]	= "Sự kiện 13";
$factarray["_MREL"]	= "Mối quan-hệ đối với Mẹ";
$factarray["_FREL"]	= "Mối quan-hệ đối với Cha";
$factarray["_MSTAT"]	= "Tình Cảnh Hôn-Phối Lúc Ban Đầu";
$factarray["_MEND"]	= "Tình Cảnh Hôn-Phối Lúc Chót";
$factarray["FACT"] = "Sự Kiện ";
$factarray["WWW"] = "Trang chủ";
$factarray["MAP"] = "Bản Đồ";
$factarray["LATI"] = "Vĩ độ";
$factarray["LONG"] = "Kinh độ";
$factarray["FONE"] = "Ngữ âm";
$factarray["ROMN"] = "Tên Ngoại Ngữ";
$factarray["_NAME"] = "Tên Bưu Cục";
$factarray["_HEB"] = "Hê-brơ";
$factarray["_SCBK"] = "Sổ Ghi";
$factarray["_TYPE"] = "Lọai Tài Liệu";
$factarray["_SSHOW"] = "Phim Dương";
$factarray["_SUBQ"]= "Tóm Tắt";
$factarray["_BIBL"] = "Thư Mục";
$factarray["EMAL"]	= "Địa chỉ thư điện tử";

// Other common customized facts
$factarray["_ADPF"]	= "Được Cha nhận Làm Con Nuôi";
$factarray["_ADPM"]	= "Được Mẹ Nhận Làm Con Nuôi";
$factarray["_AKAN"]	= "Tên Tự";
$factarray["_AKA"] 	= "Tên Tự";
$factarray["_BRTM"]	= "Brit mila";
$factarray["_COML"]	= "Common Law marriage";
$factarray["_EYEC"]	= "Màu Mắt";
$factarray["_FNRL"]	= "Tang Lể";
$factarray["_HAIR"]	= "Tóc Màu";
$factarray["_HEIG"]	= "Chiều Cao";
$factarray["_INTE"]	= "An Táng";
$factarray["_MARI"]	= "ý Định Hôn-Phối";
$factarray["_MBON"]	= "Marriage bond";
$factarray["_MEDC"]	= "Tình Trạng Sức Khỏe";
$factarray["_MILI"]	= "Quân Dịch";
$factarray["_NMR"]	= "Độc Thân";
$factarray["_NLIV"]	= "Thất Lộc";
$factarray["_NMAR"]	= "Chưa Hề Lập Gia-Dình";
$factarray["_PRMN"]	= "Permanent Number";
$factarray["_WEIG"]	= "Cân Nặng";
$factarray["_YART"]	= "Yartzeit";
$factarray["_MARNM"]	= "Tên sau khi lập gia-đình";
$factarray["_STAT"]	= "Gia Cảnh";
$factarray["COMM"]	= "Phê Bình";
$factarray["MARR_CIVIL"] = "Kết Hôn Thường Trực";
$factarray["MARR_RELIGIOUS"] = "Kết Hôn theo lễ nghi Tôn Giáo";
$factarray["MARR_PARTNERS"] = "Chung Thân Đăng Ký";
$factarray["MARR_UNKNOWN"] = "Không biết Lọai Kết Hôn";
$factarray["_DEAT_SPOU"] = "Tữ của người chồng/vợ";
$factarray["_BIRT_CHIL"] = "Sinh của con";
$factarray["_MARR_CHIL"] = "Kết Hôn của con";
$factarray["_DEAT_CHIL"] = "Tữ của người con";
$factarray["_MARR_GCHI"] = "Kết Hôn của cháu";
$factarray["_BIRT_GCHI"] = "Sinh của cháu";
$factarray["_DEAT_GCHI"] = "Tữ của cháu";
$factarray["_MARR_FATH"] = "Kết Hôn của bố";
$factarray["_DEAT_FATH"] = "Tữ của bố";
$factarray["_MARR_MOTH"] = "Kết Hôn của mẹ";
$factarray["_DEAT_MOTH"] = "Tữ của mẹ";
$factarray["_BIRT_SIBL"] = "Sinh của anh (chị, em)";
$factarray["_MARR_SIBL"] = "Kết Hôn của anh (chị, em)";
$factarray["_DEAT_SIBL"] = "Tữ của anh (chị, em)";
$factarray["_BIRT_HSIB"] = "Sinh của anh (chị, em) kế";
$factarray["_MARR_HSIB"] = "Kết Hôn của anh (chị, em) kế";
$factarray["_DEAT_HSIB"] = "Tữ của anh (chị, em) kế";
$factarray["_DEAT_GPAR"] = "Tữ của ông bà";
$factarray["_BIRT_FSIB"] = "Sinh của anh (chị, em) của bố";
$factarray["_MARR_FSIB"] = "Kết Hôn của anh (chị, em) của bố";
$factarray["_DEAT_FSIB"] = "Tữ của anh (chị, em) của bố";
$factarray["_BIRT_MSIB"] = "Sinh của anh (chị, em) của mẹ";
$factarray["_BIRT_COUS"] = "Sinh của anh (chị) con bác, em con chú";
$factarray["_MARR_COUS"] = "Kết Hôn của anh (chị) con bác, em con chú";
$factarray["_DEAT_COUS"] = "Tữ của anh (chị) con bác, em con chú";
$factarray["_DEAT_MSIB"] = "Tữ của anh (chị, em) của mẹ";
$factarray["_MARR_MSIB"] = "Kết Hôn của anh (chị, em) của mẹ";
$factarray["_HNM"] = "Tên Hê-brơ";

if (file_exists("languages/facts.vi.extra.php")) require "languages/facts.vi.extra.php";

?>

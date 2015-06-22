-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2014 at 11:18 AM
-- Server version: 5.5.32
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `phongdodanong`
--
CREATE DATABASE IF NOT EXISTS `phongdodanong` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phongdodanong`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` smallint(5) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL,
  `created_date` int(10) NOT NULL,
  `updated_date` int(10) unsigned NOT NULL,
  `last_login_date` int(10) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`user_id`, `fullname`, `role_id`, `is_locked`, `created_date`, `updated_date`, `last_login_date`) VALUES
(1, 'Tô Nguyễn Hiếu Trung', 1, 0, 1411275833, 1411275833, 1411275833);

-- --------------------------------------------------------

--
-- Table structure for table `attribute`
--

CREATE TABLE IF NOT EXISTS `attribute` (
  `attribute_id` int(10) NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(150) DEFAULT NULL,
  `input_type` varchar(20) DEFAULT NULL,
  `category_id` varchar(30) DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT NULL,
  `is_require` tinyint(1) DEFAULT NULL,
  `updated_date` int(10) DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `category` (`category_id`),
  KEY `visible` (`is_visible`),
  KEY `input type` (`input_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `attribute`
--

INSERT INTO `attribute` (`attribute_id`, `attribute_name`, `input_type`, `category_id`, `is_visible`, `is_require`, `updated_date`) VALUES
(2, 'Dung tích', 'text', '4', 1, 1, 1411850467),
(3, 'Xuất xứ', 'dropdown', '4', 1, 1, 1411850320),
(4, 'Nhà sản xuất', 'dropdown', '4', 1, 1, 1411850426);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_category`
--

CREATE TABLE IF NOT EXISTS `attribute_category` (
  `category_id` int(10) NOT NULL DEFAULT '0',
  `attribute_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`,`attribute_id`),
  KEY `category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attribute_category`
--

INSERT INTO `attribute_category` (`category_id`, `attribute_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(3, 3),
(4, 2),
(4, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_option`
--

CREATE TABLE IF NOT EXISTS `attribute_option` (
  `option_id` int(10) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) DEFAULT NULL,
  `value` varchar(200) DEFAULT NULL,
  `sort_order` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`option_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `attribute_option`
--

INSERT INTO `attribute_option` (`option_id`, `attribute_id`, `value`, `sort_order`) VALUES
(1, 2, 'Thun', 1),
(2, 2, 'Cotton', 2),
(4, 2, 'Lụa tơ tằm', 4),
(5, 2, 'Niken', 5),
(6, 2, 'Phi bóng', 6),
(7, 3, 'Mỹ', 1),
(8, 3, 'Nhật', 2),
(9, 3, 'Thụy Sỹ', 3),
(10, 3, 'New Zealand', 4),
(11, 4, 'Rausch', 1),
(12, 4, 'Herbal', 2),
(13, 4, 'Iconio', 3),
(14, 4, 'Herballife', 0);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE IF NOT EXISTS `banner` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `banner_url` varchar(600) DEFAULT NULL,
  `position_id` int(10) DEFAULT NULL,
  `ordering` tinyint(2) DEFAULT NULL,
  `created_date` int(10) DEFAULT NULL,
  `updated_date` int(10) DEFAULT NULL,
  `banner_name` varchar(300) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `banner_url`, `position_id`, `ordering`, `created_date`, `updated_date`, `banner_name`, `link`) VALUES
(2, 'http://local-web.com.vn/uploads/photo/2014/09/ddac1e0810e7f5813da298062278215a.jpg', 1, 2, 1411893360, 1411893360, 'Banner số 2', 'http://news.zing.vn/'),
(3, 'http://local-web.com.vn/uploads/photo/2014/09/53e143e2fb21886afb93070eab91acd2.jpg', 1, 1, 1411893339, 1411893339, 'Banner số 1', 'http://news.zing.vn/'),
(4, 'http://local-web.com.vn/uploads/photo/2014/09/b415e9ffba2d71cd5d09d2a145f3ff52.jpg', 2, 1, 1411893861, 1411893861, 'Banner bottom 1', 'http://news.zing.vn/'),
(5, 'http://local-web.com.vn/uploads/photo/2014/09/b476f069e6836382cdf8db1c64407650.jpg', 2, 2, 1411893882, 1411893882, 'Banner bottom 2', 'http://news.zing.vn/'),
(6, 'http://local-web.com.vn/uploads/photo/2014/09/8f17c8ce94f3d87568e4bef76c1a4826.jpg', 2, 3, 1411893901, 1411893901, 'Banner bottom 3', 'http://news.zing.vn/'),
(7, 'http://local-web.com.vn/uploads/photo/2014/09/c32731ec843c8f421da1c40f96655626.jpg', 2, 4, 1411893922, 1411893922, 'Banner bottom 4', 'http://news.zing.vn/');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(1800) DEFAULT NULL,
  `category_alias` varchar(1800) DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `page_title` varchar(1800) DEFAULT NULL,
  `meta_keyword` varchar(1800) DEFAULT NULL,
  `meta_description` varchar(2295) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `ordering` smallint(5) DEFAULT NULL,
  `show_menu` tinyint(1) DEFAULT NULL,
  `created_date` int(10) DEFAULT NULL,
  `updated_date` int(10) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_alias`, `parent_id`, `page_title`, `meta_keyword`, `meta_description`, `status`, `ordering`, `show_menu`, `created_date`, `updated_date`) VALUES
(1, 'Sinh lý nam', 'sinh-ly-nam', 0, 'Thuốc sinh lý nam | Kéo dài thời gian quan hệ | Chỗng xuất tinh sớm', 'sinh lý nam,sinh ly nam,thuoc sinh ly nam,thuốc sinh lý nam,thuốc điều trị xuất tinh sớm,chống xuất tinh sớm,chong xuat tinh som', 'Cung cấp Thuốc sinh lý nam USA, Thuốc kéo dài thời gian quan hệ, Thuốc chỗng xuất tinh sớm, Mua Thuoc sinh ly nam liên hệ tại HCM 0947333300', 1, 1, 1, 1411299132, 1411798959),
(3, 'Thuốc mọc tóc - râu', 'thuoc-moc-toc-rau', 0, 'Thuốc mọc râu, tóc | Sản phẩm chăm sóc tóc', 'Thuốc,mọc,râu,tóc,Sản phẩm chăm sóc tóc', 'Giá Sản phẩm chăm sóc tóc Thuốc mọc râu, tóc Nioxin Intensive Therapy Follicle Booster Hãng sản xuất: Nioxin / Xuất xứ: Mỹ', 1, 3, 1, 1230803606, 1411799806),
(4, 'Tăng cường sinh lý Vixmen', 'tang-cuong-sinh-ly-vixmen', 1, 'Thuốc tăng cường sinh lý Vixmen', 'tang cuong sinh ly, thuoc tang cuong sinh ly, vixmen, thuoc vixmen', 'Thuốc tăng cường sinh lý Vixmen được sản xuất độc quyền bởi Vitacare USA LLC giúp tăng cường sinh ý mạnh mẽ cho nam giới, duy trì thời gian quan hệ, hỗ trợ điều trị xuất tinh sớm mang đến hiệu quả bất ngờ.', 1, 1, 1, 0, 1411799716),
(5, 'Vitamin tổng hợp', 'vitamin-tong-hop', 0, 'Vitamin Tự Nhiên, Vitamin tổng hợp', 'vitamin tu nhien, vitamin tong hop', 'Vitamin Tự Nhiên, vitamin tổng hợp là nguồn cung cấp bổ sung cần thiết nếu bạn không có điều kiện sử dụng các thực phẩm tươi sống', 1, 2, 1, 1411799156, 1411799156),
(6, 'Tăng cường sinh lý Vigrx Plus', 'tang-cuong-sinh-ly-vigrx-plus', 1, 'Thảo dược VigRX plus - thuốc tăng cường sinh lý cho nam giới', 'VigRX Plus chinh hang, vigrx plus mua o dau, thuốc vigrx plus', 'Các chàng trai muốn tăng cường sinh lực thì hãy dùng thuốc VigRX plus. Nó là một loại thảo dược quý hiếm có tác dụng triệt để trong vấn đề yếu sinh lý của nam giới.', 1, 2, 1, 1411799948, 1411799948),
(7, 'Vitamin Vitality', 'vitamin-vitality', 5, 'Vitamin Vitality - Thuốc chống lão hóa, tăng cường sinh lực, giảm stress | Mua Thuốc Tốt của Mỹ', 'Viên Uống Vitamin, Vitamin Vitality, thuốc Vitamin', 'Vitamin Vitality được bào chế nhằm phục hồi những hao tổn về sinh lực vì vấn đề tuổi tác, và tác động từ gốc rễ.', 1, 1, 1, 1411800195, 1411800195),
(8, 'Vitamin Lifepak Nano', 'vitamin-lifepak-nano', 5, 'Vitamin Lifepak nano, nuskin lifepak nano, lifepak nano chống lão hóa da', 'lifepak nano, nuskin lifepak nano, lifepak nano chống lão hóa da', 'Lifepak Nano chống lão hóa từ bên trong DNA. Giúp chống lão hóa hiệu quả kéo dài tuổi thanh xuân', 1, 2, 1, 1411800297, 1411800297),
(9, 'Thuốc mọc tóc Rausch', 'thuoc-moc-toc-rausch', 3, 'Thuốc mọc tóc tốt nhất - Rausch Thụy Sĩ - trị rụng tóc, trị hói đầu!', 'thuoc moc toc Rausch,thuốc mọc tóc Rausch', 'Rausch Thụy Sĩ được đánh giá là một trong các thương hiệu chăm sóc tóc, trị hói, trị rụng tóc lâu đời và nổi tiếng hàng đầu thế giới. Hiệu quả - An toàn!', 1, 1, 1, 1411800478, 1411800478),
(10, 'Thuốc mọc râu Nioxin', 'thuoc-moc-rau-nioxin', 3, 'Nioxin - Thuốc mọc râu hiệu quả, mang lại bản lĩnh cho đàn ông', 'thuoc moc rau nioxin, thuốc mọc râu nioxin', 'Nioxin | Thuốc mọc râu | Thuốc Nioxin | Thuốc mọc tóc | Thuốc chữa rụng tóc', 1, 2, 1, 1411800613, 1411800613);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `product_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT '0',
  `product_alias` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `product_summary` mediumtext COLLATE utf8_unicode_ci,
  `product_description` text COLLATE utf8_unicode_ci NOT NULL,
  `product_image` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `product_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `product_price` double(13,2) unsigned NOT NULL DEFAULT '0.00',
  `special_price` double(13,2) unsigned DEFAULT '0.00',
  `product_multi_image` text COLLATE utf8_unicode_ci NOT NULL,
  `meta_keywords` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` int(10) unsigned DEFAULT '0',
  `updated_date` int(10) unsigned DEFAULT '0',
  `attributes` text COLLATE utf8_unicode_ci,
  `is_deleted` tinyint(1) unsigned DEFAULT NULL,
  `product_effect` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `alias` (`product_alias`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `product_code`, `product_alias`, `product_summary`, `product_description`, `product_image`, `product_status`, `product_price`, `special_price`, `product_multi_image`, `meta_keywords`, `meta_description`, `created_date`, `updated_date`, `attributes`, `is_deleted`, `product_effect`) VALUES
(6, 4, 'Thuốc mọc râu Herbal Men', 'TEST', 'thuoc-moc-rau-herbal-men', 'mô tả ngắn nè', '<p>m&ocirc; tả chi tiết n&egrave;</p>', 'http://local-web.com.vn/uploads/photo/2014/09/6e5749c2c364d1adbed2b56f23949823.jpg', 1, 200000.00, 100000.00, '["http:\\/\\/local-web.com.vn\\/uploads\\/photo\\/2014\\/09\\/6e5749c2c364d1adbed2b56f23949823.jpg"]', 'meta keyword nèd  fdgfd', 'meta description nè gddfgfdg', 1411630522, 1411850500, '{"2":{"2":{"id":2,"value":"100ml"}},"3":{"3":{"id":3,"value":10}},"4":{"4":{"id":4,"value":12}}}', 0, '<h2>C&ocirc;ng dụng của HERBAL MEN</h2>\r\n<p><i>..</i>Hỗ trợ điều trị h&oacute;i đầu</p>\r\n<p><i>..</i>K&iacute;ch th&iacute;ch t&oacute;c mọc d&agrave;y hơn, nhanh hơn</p>\r\n<p><i>..</i>Bổ sung vitamin cho ch&acirc;n t&oacute;c</p>\r\n<p><i>..</i>Sử dụng được cho t&oacute;c v&agrave; r&acirc;u</p>'),
(7, 4, 'Thuốc mọc tócHerbal Men', 'TEST', 'thuoc-moc-tocherbal-men', 'mô tả ngắn nè', '<p>m&ocirc; tả chi tiết n&egrave;</p>', 'http://local-web.com.vn/uploads/photo/2014/09/00607cd5d9381c8497efc5c58d8096ca.jpg', 1, 400000.00, 200000.00, '["http:\\/\\/local-web.com.vn\\/uploads\\/photo\\/2014\\/09\\/00607cd5d9381c8497efc5c58d8096ca.jpg"]', 'meta keyword nè', 'meta description nè', 1411630522, 1411849176, '{"3":{"3":{"id":3,"value":["8"]}}}', 0, '<p>c&ocirc;ng dụng n&egrave;&nbsp;</p>');

-- --------------------------------------------------------

--
-- Table structure for table `products_views`
--

CREATE TABLE IF NOT EXISTS `products_views` (
  `product_id` int(10) DEFAULT NULL,
  `views` int(10) DEFAULT NULL,
  `last_view` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_views`
--

INSERT INTO `products_views` (`product_id`, `views`, `last_view`) VALUES
(6, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` tinyint(1) unsigned DEFAULT '0',
  `birthday` int(10) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_verified_email` tinyint(1) unsigned DEFAULT '0',
  `is_ban` tinyint(1) unsigned DEFAULT '0',
  `registered_date` int(10) unsigned DEFAULT '0',
  `last_update` int(10) unsigned DEFAULT '0',
  `banned_date` int(10) unsigned DEFAULT '0',
  `social_id` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `social_type` char(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_email` tinyint(1) unsigned DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `social_id` (`social_id`,`social_type`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `display_name`, `avatar`, `email`, `password`, `salt`, `gender`, `birthday`, `address`, `is_verified_email`, `is_ban`, `registered_date`, `last_update`, `banned_date`, `social_id`, `social_type`, `receive_email`, `mobile`) VALUES
(1, 'Tô Nguyễn Hiếu Trung', NULL, 'nguyentrunk@gmail.com', '1625706dae08ec5f0584d85919989de70d3e1ce4', 'J8Y>?nik7K', 1, NULL, 'Gò Vấp', 1, 0, 0, 1411275912, 0, '0', '0', 0, '0947333300');

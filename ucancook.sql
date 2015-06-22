/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50525
Source Host           : localhost:3306
Source Database       : ucancook

Target Server Type    : MYSQL
Target Server Version : 50525
File Encoding         : 65001

Date: 2014-11-23 01:02:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` smallint(5) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL,
  `created_date` int(10) NOT NULL,
  `updated_date` int(10) unsigned NOT NULL,
  `last_login_date` int(10) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'Tô Nguyễn Hiếu Trung', '1', '0', '1411275833', '1411275833', '1411275833');

-- ----------------------------
-- Table structure for `articles`
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `picture` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` int(10) unsigned NOT NULL DEFAULT '0',
  `is_video` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `meta_keywords` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `meta_desciption` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of articles
-- ----------------------------
INSERT INTO `articles` VALUES ('1', 'Công làm - thủ phá, ĐTVN bị Indonesia cầm hòa 2-2', 'cong-lam-thu-pha-dtvn-bi-indonesia-cam-hoa-2-2', 'Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).', '<p>Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).Hai lần vượt lên dẫn trước từ những pha lập công đẳng cấp của Ngọc Hải, Công Vinh, nhưng ĐTVN vẫn để mất điểm vì sai lầm sơ đẳng ở hàng thủ trong trận gặp Indonesia (19h 22/11).</p>', '2', 'http://local-ucancook.vn/uploads/photo/2014/11/cb4cb225d93c230e9b277fa1e7b4eda6.jpg', '1', '0', '0', '0', '0', '', '', '1416673692', '1416673692');

-- ----------------------------
-- Table structure for `articles_tags`
-- ----------------------------
DROP TABLE IF EXISTS `articles_tags`;
CREATE TABLE `articles_tags` (
  `article_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`article_id`,`tag_id`),
  KEY `article_id` (`article_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of articles_tags
-- ----------------------------
INSERT INTO `articles_tags` VALUES ('1', '3');
INSERT INTO `articles_tags` VALUES ('1', '4');

-- ----------------------------
-- Table structure for `articles_views`
-- ----------------------------
DROP TABLE IF EXISTS `articles_views`;
CREATE TABLE `articles_views` (
  `article_id` int(10) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `last_view` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of articles_views
-- ----------------------------
INSERT INTO `articles_views` VALUES ('1', '0', '0');

-- ----------------------------
-- Table structure for `attribute`
-- ----------------------------
DROP TABLE IF EXISTS `attribute`;
CREATE TABLE `attribute` (
  `attribute_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(150) DEFAULT NULL,
  `input_type` varchar(20) DEFAULT NULL,
  `category_id` varchar(30) DEFAULT NULL,
  `is_visible` tinyint(1) unsigned DEFAULT NULL,
  `is_require` tinyint(1) unsigned DEFAULT NULL,
  `updated_date` int(10) DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `input type` (`input_type`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of attribute
-- ----------------------------
INSERT INTO `attribute` VALUES ('0000000002', 'Độ khó', 'dropdown', '1,2,3', '1', '1', '1416640944');
INSERT INTO `attribute` VALUES ('0000000003', 'Số calo', 'text', '1,2,3', '1', '0', '1416640928');
INSERT INTO `attribute` VALUES ('0000000004', 'Thời gian chuẩn bị', 'text', '1,2,3', '1', '0', '1416640936');
INSERT INTO `attribute` VALUES ('0000000005', 'Gia vị', 'multiple', '1,2,3', '1', '1', '1416640906');
INSERT INTO `attribute` VALUES ('0000000006', 'Đặc tính món ăn', 'multiple', '1,2,3', '1', '0', '1416641850');
INSERT INTO `attribute` VALUES ('0000000007', 'Dụng cụ', 'multiple', '1,2,3', '1', '1', '1416642553');

-- ----------------------------
-- Table structure for `attribute_category`
-- ----------------------------
DROP TABLE IF EXISTS `attribute_category`;
CREATE TABLE `attribute_category` (
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attribute_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attribute_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of attribute_category
-- ----------------------------
INSERT INTO `attribute_category` VALUES ('1', '2');
INSERT INTO `attribute_category` VALUES ('1', '3');
INSERT INTO `attribute_category` VALUES ('1', '4');
INSERT INTO `attribute_category` VALUES ('1', '5');
INSERT INTO `attribute_category` VALUES ('1', '6');
INSERT INTO `attribute_category` VALUES ('1', '7');
INSERT INTO `attribute_category` VALUES ('2', '2');
INSERT INTO `attribute_category` VALUES ('2', '3');
INSERT INTO `attribute_category` VALUES ('2', '4');
INSERT INTO `attribute_category` VALUES ('2', '5');
INSERT INTO `attribute_category` VALUES ('2', '6');
INSERT INTO `attribute_category` VALUES ('2', '7');
INSERT INTO `attribute_category` VALUES ('3', '2');
INSERT INTO `attribute_category` VALUES ('3', '3');
INSERT INTO `attribute_category` VALUES ('3', '4');
INSERT INTO `attribute_category` VALUES ('3', '5');
INSERT INTO `attribute_category` VALUES ('3', '6');
INSERT INTO `attribute_category` VALUES ('3', '7');

-- ----------------------------
-- Table structure for `attribute_option`
-- ----------------------------
DROP TABLE IF EXISTS `attribute_option`;
CREATE TABLE `attribute_option` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned DEFAULT NULL,
  `value` varchar(200) DEFAULT NULL,
  `sort_order` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`option_id`),
  KEY `category_id` (`attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of attribute_option
-- ----------------------------
INSERT INTO `attribute_option` VALUES ('1', '2', 'Khó', '1');
INSERT INTO `attribute_option` VALUES ('2', '2', 'Trung bình', '2');
INSERT INTO `attribute_option` VALUES ('3', '2', 'Dễ', '3');
INSERT INTO `attribute_option` VALUES ('4', '5', 'Bột ngọt', '1');
INSERT INTO `attribute_option` VALUES ('5', '5', 'Đường', '2');
INSERT INTO `attribute_option` VALUES ('6', '5', 'Muối', '3');
INSERT INTO `attribute_option` VALUES ('7', '5', 'Tiêu', '4');
INSERT INTO `attribute_option` VALUES ('8', '5', 'Dầu hào', '5');
INSERT INTO `attribute_option` VALUES ('9', '5', 'Dầu mè', '6');
INSERT INTO `attribute_option` VALUES ('10', '5', 'Nước tương', '7');
INSERT INTO `attribute_option` VALUES ('11', '6', 'Chua', '1');
INSERT INTO `attribute_option` VALUES ('12', '6', 'Cay', '2');
INSERT INTO `attribute_option` VALUES ('13', '6', 'Mặn', '3');
INSERT INTO `attribute_option` VALUES ('14', '6', 'Ngọt', '4');
INSERT INTO `attribute_option` VALUES ('15', '6', 'Không đường', '5');
INSERT INTO `attribute_option` VALUES ('16', '6', 'Ít đường', '6');
INSERT INTO `attribute_option` VALUES ('17', '7', 'Chảo', '1');
INSERT INTO `attribute_option` VALUES ('18', '7', 'Nồi', '2');
INSERT INTO `attribute_option` VALUES ('19', '7', 'Thớt', '3');
INSERT INTO `attribute_option` VALUES ('20', '7', 'Muỗn canh nhỏ', '4');
INSERT INTO `attribute_option` VALUES ('21', '7', 'Muỗng canh lớn', '5');
INSERT INTO `attribute_option` VALUES ('22', '7', 'Tô nhỏ', '6');
INSERT INTO `attribute_option` VALUES ('23', '7', 'Đĩa nhỏ', '7');

-- ----------------------------
-- Table structure for `category`
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(1800) DEFAULT NULL,
  `category_alias` varchar(1800) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `page_title` varchar(1800) DEFAULT NULL,
  `meta_keyword` varchar(1800) DEFAULT NULL,
  `meta_description` varchar(2295) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `ordering` smallint(5) unsigned DEFAULT NULL,
  `show_menu` tinyint(1) unsigned DEFAULT NULL,
  `created_date` int(10) unsigned DEFAULT NULL,
  `updated_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('1', 'Món ngon mỗi ngày', 'mon-ngon-moi-ngay', '0', '', '', '', '1', '1', '1', '1416671801', '1416671801');
INSERT INTO `category` VALUES ('2', 'Món Huế', 'mon-hue', '1', '', '', '', '1', '1', '1', '1416673619', '1416673619');

-- ----------------------------
-- Table structure for `chef`
-- ----------------------------
DROP TABLE IF EXISTS `chef`;
CREATE TABLE `chef` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chef_name` varchar(100) DEFAULT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  `gender` tinyint(1) unsigned DEFAULT NULL,
  `chef_description` text,
  `created_date` int(10) unsigned DEFAULT NULL,
  `updated_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of chef
-- ----------------------------
INSERT INTO `chef` VALUES ('1', 'Elana Karp', 'http://local-ucancook.vn/uploads/photo/2014/11/f2abc3a1ece748ccfe25ce043125709f.jpg', '0', '<p><span style=\"color: #474747; font-family: \'Open Sans\', sans-serif; font-size: 16px; line-height: 24px;\">Elana, as VP of Culinary at Plated, brings her classical training and culinary skills to our table. After completing Teach for America in New York City and earning her Masters degree in Childhood Education, she headed over to Paris to explore France&rsquo;s deep culinary history and tradition. On the way she earned a chef&rsquo;s degree from Le Cordon Bleu, Paris and developed her philosophy that cooking and eating are the best ways to bring people together.&nbsp;</span><br /><br /><br /><span style=\"color: #474747; font-family: \'Open Sans\', sans-serif; font-size: 16px; line-height: 24px;\">While studying in Paris, Elana explored various food establishments, hosted pop up dinners with classmates, and cooked for the Australian Ambassador to France at a major wine and food event. She returned home ready to share her experience and knowledge. Back home in New York, Elana developed a school-based food education program and culinary camp for children. She also oversaw operations for a specialty food service offering children healthy and delicious school lunches. In her spare time she catered, offered private chef services to select clientele, and blogged.&nbsp;</span><br /><br /><br /><span style=\"color: #474747; font-family: \'Open Sans\', sans-serif; font-size: 16px; line-height: 24px;\">Elana believes food should be easy to make, delicious to eat and is best enjoyed when shared. It leads to good conversation, exposes diners to new cultures, and makes great memories. She is thrilled to be part of the Plated culinary team and looks forward to sharing her meals with you!</span></p>', '1416240652', '1416240652');

-- ----------------------------
-- Table structure for `contact`
-- ----------------------------
DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `address` varchar(300) NOT NULL,
  `created_date` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contact
-- ----------------------------

-- ----------------------------
-- Table structure for `coupon`
-- ----------------------------
DROP TABLE IF EXISTS `coupon`;
CREATE TABLE `coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_name` varchar(100) DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL,
  `value` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `created_date` int(10) unsigned DEFAULT NULL,
  `updated_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of coupon
-- ----------------------------
INSERT INTO `coupon` VALUES ('1', 'Coupon 20k -edit', '20000', '20000', '1', '1416237295', '1416238018');

-- ----------------------------
-- Table structure for `dishes`
-- ----------------------------
DROP TABLE IF EXISTS `dishes`;
CREATE TABLE `dishes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `type` tinyint(4) unsigned DEFAULT NULL COMMENT 'loại của món ăn (chay, hải sản, thịt)',
  `chef_id` int(11) unsigned DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL COMMENT 'giá của món ăn',
  `special_price` int(10) unsigned DEFAULT NULL,
  `ingredient` varchar(200) DEFAULT NULL,
  `attributes` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT 'số calorie',
  `image` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT 'hình ảnh đại diện',
  `multi_image` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8 COMMENT 'mô tả cho món ăn',
  `created_date` int(10) unsigned DEFAULT NULL,
  `updated_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chef` (`chef_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dishes
-- ----------------------------
INSERT INTO `dishes` VALUES ('1', 'Sườn sào chua ngọt', '1', '1', '250000', '220000', '3,2', '{\"2\":{\"2\":{\"id\":2,\"value\":2}},\"3\":{\"3\":{\"id\":3,\"value\":\"130 calorie\"}},\"4\":{\"4\":{\"id\":4,\"value\":\"45 ph\\u00fat\"}},\"5\":{\"5\":{\"id\":5,\"value\":[\"4\"]}},\"6\":{\"6\":{\"id\":6,\"value\":[\"4\"]}},\"7\":{\"7\":{\"id\":7,\"value\":[\"4\"]}}}', 'http://local-ucancook.vn/uploads/photo/2014/11/1c1dd305dffce6f00f9e80cec882e38d.jpg', '[\"http:\\/\\/local-ucancook.vn\\/uploads\\/photo\\/2014\\/11\\/1c1dd305dffce6f00f9e80cec882e38d.jpg\",\"http:\\/\\/local-ucancook.vn\\/uploads\\/photo\\/2014\\/11\\/2ee6ee9cb082fa459000d6ace51797e6.jpg\"]', '1', '<p>M&Oacute;n nay n&agrave;y rất ngon n&egrave;<img src=\"http://local-ucancook.vn/uploads/photo/2014/11/3ad91fd1825fc3ef1c6f7f0f81d85208.jpg\" /></p>', '1416644259', '1416649157');
INSERT INTO `dishes` VALUES ('2', 'Gà hầm ngũ quả', '1', '1', '300000', '0', ',', '{\"2\":{\"2\":{\"id\":2,\"value\":1}},\"3\":{\"3\":{\"id\":3,\"value\":\"100\"}},\"4\":{\"4\":{\"id\":4,\"value\":\"140 ph\\u00fat\"}},\"5\":{\"5\":{\"id\":5,\"value\":[\"4\",\"7\",\"10\"]}},\"6\":{\"6\":{\"id\":6,\"value\":[\"4\",\"7\",\"10\",\"12\",\"15\"]}},\"7\":{\"7\":{\"id\":7,\"value\":[\"4\",\"7\",\"10\",\"12\",\"15\",\"18\",\"19\",\"21\",\"22\"]}}}', 'http://local-ucancook.vn/uploads/photo/2014/11/6008b3882ecbc4c49fd4e7b577f6c4b4.jpg', '[\"http:\\/\\/local-ucancook.vn\\/uploads\\/photo\\/2014\\/11\\/6008b3882ecbc4c49fd4e7b577f6c4b4.jpg\",\"http:\\/\\/local-ucancook.vn\\/uploads\\/photo\\/2014\\/11\\/4aded7c55bd8559c44cc100d3cc868b1.jpg\"]', '1', '<p>M&oacute;n G&agrave; hầm ngũ quả n&agrave;y ăn rất ngon, nhiều dinh dưỡng, bổ thận</p>', '1416647194', '1416647194');

-- ----------------------------
-- Table structure for `dish_ingredient`
-- ----------------------------
DROP TABLE IF EXISTS `dish_ingredient`;
CREATE TABLE `dish_ingredient` (
  `dish_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ingredient_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`dish_id`,`ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dish_ingredient
-- ----------------------------
INSERT INTO `dish_ingredient` VALUES ('1', '2');
INSERT INTO `dish_ingredient` VALUES ('1', '3');
INSERT INTO `dish_ingredient` VALUES ('2', '2');
INSERT INTO `dish_ingredient` VALUES ('2', '3');

-- ----------------------------
-- Table structure for `ingredient`
-- ----------------------------
DROP TABLE IF EXISTS `ingredient`;
CREATE TABLE `ingredient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `image` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT 'hình ảnh đại diện',
  `price` int(10) unsigned DEFAULT NULL,
  `special_price` int(10) unsigned DEFAULT NULL,
  `unit_price` tinyint(1) DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL,
  `description` text CHARACTER SET utf8 COMMENT 'mô tả cho món ăn',
  `created_date` int(10) unsigned DEFAULT NULL,
  `updated_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ingredient
-- ----------------------------
INSERT INTO `ingredient` VALUES ('2', 'Khoai tây Đà Lạt', 'http://local-ucancook.vn/uploads/photo/2014/11/5a9b6a01b890d7f5474e33f0f3fc26cb.jpg', '12000', '0', '2', '25', '<p>Khoai T&acirc;y đ&agrave; Lạt ch&iacute;nh hiệu hai con cừu non</p>', '1416595824', '1416642912');
INSERT INTO `ingredient` VALUES ('3', 'Chanh Tây', 'http://local-ucancook.vn/uploads/photo/2014/11/0bc08be86fcd6822d63df76d807447b3.jpg', '170000', '0', '2', '18', '<p>Chanh T&acirc;y ch&iacute;nh hiệu Đ&agrave; Lạt</p>', '1416643042', '1416643042');

-- ----------------------------
-- Table structure for `menu`
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dish_id` int(10) unsigned DEFAULT NULL COMMENT 'mã món ăn',
  `quantity` int(5) unsigned DEFAULT NULL COMMENT 'số lượng bán của món ăn ngày hôm đó',
  `sale_date` int(10) unsigned DEFAULT NULL COMMENT 'ngày bán',
  `created_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dishes_id` (`dish_id`),
  KEY `sale_date` (`sale_date`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('2', '1', '120', '1416697200', '1416652263');
INSERT INTO `menu` VALUES ('3', '2', '15', '1416783600', '1416652278');
INSERT INTO `menu` VALUES ('4', '1', '56', '1416783600', '1416652289');
INSERT INTO `menu` VALUES ('5', '2', '23', '1416697200', '1416652300');

-- ----------------------------
-- Table structure for `newsletter`
-- ----------------------------
DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE `newsletter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of newsletter
-- ----------------------------

-- ----------------------------
-- Table structure for `product_orders`
-- ----------------------------
DROP TABLE IF EXISTS `product_orders`;
CREATE TABLE `product_orders` (
  `order_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_code` char(30) COLLATE utf8_unicode_ci NOT NULL,
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `order_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_city` tinyint(2) DEFAULT NULL,
  `order_district` tinyint(2) DEFAULT NULL,
  `order_phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `order_note` text COLLATE utf8_unicode_ci NOT NULL,
  `payment_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `amount_total` decimal(12,0) unsigned NOT NULL DEFAULT '0',
  `created_date` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_date` int(10) unsigned NOT NULL,
  `shipping_cost` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of product_orders
-- ----------------------------

-- ----------------------------
-- Table structure for `product_order_detail`
-- ----------------------------
DROP TABLE IF EXISTS `product_order_detail`;
CREATE TABLE `product_order_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` smallint(6) unsigned NOT NULL DEFAULT '0',
  `price` decimal(12,0) NOT NULL,
  `created_date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of product_order_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `tags`
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(100) DEFAULT NULL,
  `tag_name_ascii` varchar(100) DEFAULT NULL,
  `tag_alias` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tags
-- ----------------------------
INSERT INTO `tags` VALUES ('1', 'ao khoac', 'ao khoac', 'ao-khoac');
INSERT INTO `tags` VALUES ('2', 'ao so mi', 'ao so mi', 'ao-so-mi');
INSERT INTO `tags` VALUES ('3', 'u23 viet nam', 'u23 viet nam', 'u23-viet-nam');
INSERT INTO `tags` VALUES ('4', 'aff cup 2014', 'aff cup 2014', 'aff-cup-2014');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Nguyễn Thế Đông', null, 'dongnguyenthe123@gmail.com', '1625706dae08ec5f0584d85919989de70d3e1ce4', 'J8Y>?nik7K', '1', null, 'Gò Vấp', '1', '0', '0', '1411275912', '0', '0', '0', '0', '0947333300');

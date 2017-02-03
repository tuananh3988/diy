/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : diy

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-02-03 16:36:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mtb_install
-- ----------------------------
DROP TABLE IF EXISTS `mtb_install`;
CREATE TABLE `mtb_install` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_type` tinyint(4) DEFAULT '1',
  `uuid` char(36) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

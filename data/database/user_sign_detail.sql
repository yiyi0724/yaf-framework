/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:55:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_sign_detail
-- ----------------------------
DROP TABLE IF EXISTS `user_sign_detail`;
CREATE TABLE `user_sign_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `year` int(10) unsigned NOT NULL COMMENT '年',
  `mouth` int(10) unsigned NOT NULL COMMENT '月',
  `day` int(10) unsigned NOT NULL COMMENT '日',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到详情表';

-- ----------------------------
-- Records of user_sign_detail
-- ----------------------------

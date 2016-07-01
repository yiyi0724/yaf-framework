/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:54:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_lauth
-- ----------------------------
DROP TABLE IF EXISTS `user_lauth`;
CREATE TABLE `user_lauth` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户id',
  `username` varchar(128) NOT NULL COMMENT '帐号',
  `password` char(64) NOT NULL COMMENT '密码',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `unsername` (`username`) USING BTREE COMMENT '用户名唯一索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户网站登录表';

-- ----------------------------
-- Records of user_lauth
-- ----------------------------

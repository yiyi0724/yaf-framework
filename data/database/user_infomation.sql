/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-05-17 09:44:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_infomation
-- ----------------------------
DROP TABLE IF EXISTS `user_infomation`;
CREATE TABLE `user_infomation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `nickname` char(16) NOT NULL COMMENT '昵称',
  `avatar` char(255) NOT NULL COMMENT '头像',
  `gender` enum('未设置','男','女') NOT NULL DEFAULT '未设置' COMMENT '性别',
  `mobile` char(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` char(128) NOT NULL DEFAULT '' COMMENT '邮箱',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户的状态, -1|禁用，0|正常',
  `regtime` bigint(20) unsigned NOT NULL COMMENT '注册时间',
  `regip` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册ip',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65535 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of user_infomation
-- ----------------------------

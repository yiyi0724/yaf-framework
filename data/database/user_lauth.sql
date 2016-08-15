/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50624
Source Host           : 192.168.66.149:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-08-08 17:53:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_lauth
-- ----------------------------
DROP TABLE IF EXISTS `user_lauth`;
CREATE TABLE `user_lauth` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户id',
  `username` varchar(128) NOT NULL COMMENT '帐号',
  `mobile` char(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `password` char(64) NOT NULL COMMENT '密码',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `account_username` (`username`) USING BTREE COMMENT '用户名唯一索引',
  UNIQUE KEY `account_mobile` (`mobile`),
  UNIQUE KEY `account_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户网站登录表';

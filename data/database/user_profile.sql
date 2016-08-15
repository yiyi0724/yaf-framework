/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50624
Source Host           : 192.168.66.149:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-08-08 17:53:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_profile
-- ----------------------------
DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `nickname` char(16) CHARACTER SET utf8mb4 NOT NULL COMMENT '昵称',
  `avatar` char(255) NOT NULL COMMENT '头像',
  `gender` enum('未设置','男','女') NOT NULL DEFAULT '未设置' COMMENT '性别',
  `mobile` char(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` char(128) NOT NULL DEFAULT '' COMMENT '邮箱',
  `status` enum('deleted','enable','disable') NOT NULL DEFAULT 'enable' COMMENT '用户状态',
  `regtime` bigint(20) unsigned NOT NULL COMMENT '注册时间',
  `regip` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册ip',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mobile` (`mobile`) COMMENT '电话号码唯一'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

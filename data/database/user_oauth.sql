/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50624
Source Host           : 192.168.66.149:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-08-08 17:53:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_oauth
-- ----------------------------
DROP TABLE IF EXISTS `user_oauth`;
CREATE TABLE `user_oauth` (
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `from` enum('qq','weixin','weibo','taobao') NOT NULL COMMENT '第三方名称',
  `oauth_id` char(128) NOT NULL COMMENT '用户登录token，唯一确定用户标识的信息',
  `union_id` char(128) NOT NULL DEFAULT '' COMMENT ' 其它备注，如微信用户的unionid',
  PRIMARY KEY (`oauth_id`,`from`),
  UNIQUE KEY `login` (`oauth_id`,`from`) USING BTREE COMMENT '第三方登录信息',
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户第三方登录表';

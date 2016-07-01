/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:54:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_oauth
-- ----------------------------
DROP TABLE IF EXISTS `user_oauth`;
CREATE TABLE `user_oauth` (
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `from` enum('qq','微信','微博','淘宝','百度') NOT NULL COMMENT '第三方名称',
  `oauth_id` char(128) NOT NULL COMMENT '用户登录token，唯一确定用户标识的信息',
  `union_id` char(128) NOT NULL DEFAULT '' COMMENT ' 其它备注，如微信用户的unionid',
  PRIMARY KEY (`oauth_id`,`from`),
  UNIQUE KEY `login` (`oauth_id`,`from`) USING BTREE COMMENT '第三方登录信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户第三方登录表';

-- ----------------------------
-- Records of user_oauth
-- ----------------------------

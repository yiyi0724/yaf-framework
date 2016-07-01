/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:54:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_sign
-- ----------------------------
DROP TABLE IF EXISTS `user_sign`;
CREATE TABLE `user_sign` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `count` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '用户连续签到次数',
  `lasttime` char(14) NOT NULL COMMENT '用户最新签到时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户签到表';

-- ----------------------------
-- Records of user_sign
-- ----------------------------

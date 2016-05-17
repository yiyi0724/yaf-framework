/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-05-17 09:35:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_login_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_login_log`;
CREATE TABLE `admin_login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志主键id',
  `uid` int(10) unsigned NOT NULL COMMENT '管理员id',
  `login_time` char(8) NOT NULL COMMENT '登录时间',
  `login_ip` int(10) unsigned NOT NULL COMMENT '登录ip地址',
  `login_count` int(10) unsigned NOT NULL COMMENT '今日登录次数',
  PRIMARY KEY (`id`),
  KEY `user` (`uid`,`login_time`,`login_ip`,`login_count`) USING BTREE COMMENT '查找某一个用户的信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_login_log
-- ----------------------------

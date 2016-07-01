/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:53:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_finance
-- ----------------------------
DROP TABLE IF EXISTS `user_finance`;
CREATE TABLE `user_finance` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户id',
  `income` decimal(10,3) unsigned NOT NULL COMMENT '总金额',
  `normal` decimal(10,3) unsigned NOT NULL COMMENT '可用金额',
  `payout` decimal(10,3) unsigned NOT NULL COMMENT '支出金额',
  `freeze` decimal(10,3) unsigned NOT NULL COMMENT '冻结金额',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户财务表';

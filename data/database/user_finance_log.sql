/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-05-17 09:52:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user_finance_log
-- ----------------------------
DROP TABLE IF EXISTS `user_finance_log`;
CREATE TABLE `user_finance_log` (
  `oid` int(10) unsigned NOT NULL COMMENT '订单id,也就是订单号',
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户id',
  `flow` enum('收入','支出','冻结') NOT NULL COMMENT '交易类型',
  `money` decimal(10,3) NOT NULL COMMENT '金额',
  `remark` char(255) NOT NULL COMMENT '日志备注',
  `when` char(14) NOT NULL COMMENT '发生时间',
  PRIMARY KEY (`oid`,`uid`,`when`),
  KEY `user_log` (`uid`,`flow`,`when`) USING BTREE COMMENT '查找某个用户某个类型某个时间内的日志'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='财务变更日志表';

-- ----------------------------
-- Records of user_finance_log
-- ----------------------------

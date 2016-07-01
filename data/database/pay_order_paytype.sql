/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:55:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pay_order_paytype
-- ----------------------------
DROP TABLE IF EXISTS `pay_order_paytype`;
CREATE TABLE `pay_order_paytype` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trade_no` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `out_trade_no` varchar(255) NOT NULL COMMENT '交易方id，第三方交易的订单号，或者我司红包id，或者0表示金币支付',
  `type` tinyint(3) unsigned NOT NULL COMMENT '支付类型，1-微信，2-支付宝，3-红包，4-金币',
  `total_fee` decimal(10,3) NOT NULL,
  `paytime` char(14) NOT NULL DEFAULT '' COMMENT '交易时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pay_order_paytype
-- ----------------------------

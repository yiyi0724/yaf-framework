/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:55:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pay_order
-- ----------------------------
DROP TABLE IF EXISTS `pay_order`;
CREATE TABLE `pay_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id,也就是订单号',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `pid` int(10) unsigned NOT NULL COMMENT '商品id',
  `type` tinyint(3) unsigned NOT NULL COMMENT '商品种类',
  `unit` decimal(10,3) NOT NULL COMMENT '单价',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,3) NOT NULL COMMENT '总价',
  `status` enum('未支付','已支付','已删除') DEFAULT NULL COMMENT '订单状态',
  `addtime` int(11) NOT NULL COMMENT '订单生成时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000000 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of pay_order
-- ----------------------------

/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:55:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员编号',
  `username` varchar(32) NOT NULL COMMENT '管理员登录邮箱',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `nickname` char(16) NOT NULL COMMENT '昵称',
  `avatar` char(255) NOT NULL DEFAULT '' COMMENT '头像',
  `mobile` char(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '管理员状态：-2|删除，-1|禁用，0|正常',
  `addtime` bigint(20) unsigned NOT NULL COMMENT '账号创建时间',
  `group_id` int(11) unsigned NOT NULL COMMENT '用户组',
  `attach_rules` text NOT NULL COMMENT '附加权限',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`) USING HASH COMMENT '用户名唯一id'
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('10000', '346745114@qq.com', '05b38f336af2473f4fd2716e1cb14ee99c79fedb', '超级管理员', 'portrait/10000.png', '15959375069', '0', '20160424115000', '1', '');

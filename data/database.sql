/*
Navicat MySQL Data Transfer

Source Server         : 192.168.66.149
Source Server Version : 50624
Source Host           : 192.168.66.149:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-03-28 18:00:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for eny_admin
-- ----------------------------
DROP TABLE IF EXISTS `eny_admin`;
CREATE TABLE `eny_admin` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员编号',
  `username` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT '管理员姓名, 仅用于显示, 不用于登录',
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `nickname` char(16) COLLATE utf8_unicode_ci NOT NULL COMMENT '昵称',
  `email` char(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` char(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号码',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `create_time` char(24) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '账号创建时间',
  `group_id` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username_UNIQUE` (`username`) USING BTREE COMMENT '用户名唯一id'
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='管理员';

-- ----------------------------
-- Records of eny_admin
-- ----------------------------
INSERT INTO `eny_admin` VALUES ('10000', 'admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'admin', '346745114@qq.com', '15959375069', '0', '20160301135311', '1');
INSERT INTO `eny_admin` VALUES ('10001', 'cxb901021', 'ade6e2f9daf1dd97974ae5402857c8e1a481509c', '陈晓波', 'chenxiaobo_901021@yeah.net', '15750843982', '0', '20160328172430', '2');

-- ----------------------------
-- Table structure for eny_finance
-- ----------------------------
DROP TABLE IF EXISTS `eny_finance`;
CREATE TABLE `eny_finance` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户id',
  `amount` decimal(10,3) NOT NULL COMMENT '总金额',
  `normal` decimal(10,3) NOT NULL COMMENT '可用金额',
  `payout` decimal(10,3) NOT NULL COMMENT '支出金额',
  `freeze` decimal(10,3) NOT NULL COMMENT '冻结金额',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户财务表';

-- ----------------------------
-- Records of eny_finance
-- ----------------------------

-- ----------------------------
-- Table structure for eny_finance_log
-- ----------------------------
DROP TABLE IF EXISTS `eny_finance_log`;
CREATE TABLE `eny_finance_log` (
  `oid` int(10) unsigned NOT NULL COMMENT '订单号',
  `uid` bigint(20) NOT NULL COMMENT '用户id',
  `flow` enum('充值','支出','提现','退款','冻结') DEFAULT NULL COMMENT '交易类型',
  `paykind` enum('支付宝','微信','银联','余额','退款','多渠道') DEFAULT NULL COMMENT '支付渠道',
  `remark` int(10) unsigned NOT NULL COMMENT '处理结果信息提示',
  `info` char(255) NOT NULL COMMENT ' 支付的具体信息，包含第三方返回的信息，用json封装',
  `paytime` int(11) NOT NULL DEFAULT '0' COMMENT '订单支付时间',
  PRIMARY KEY (`oid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='财务变更日志表';

-- ----------------------------
-- Records of eny_finance_log
-- ----------------------------

-- ----------------------------
-- Table structure for eny_group
-- ----------------------------
DROP TABLE IF EXISTS `eny_group`;
CREATE TABLE `eny_group` (
  `id` int(11) NOT NULL,
  `name` char(8) NOT NULL COMMENT '角色名称',
  `rules` varchar(500) NOT NULL COMMENT '用户权限',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '-1_删除，0_正常，1-禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of eny_group
-- ----------------------------
INSERT INTO `eny_group` VALUES ('1', '管理员', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21', '0');
INSERT INTO `eny_group` VALUES ('2', '测试人员', '1,2,3,4,5', '0');

-- ----------------------------
-- Table structure for eny_member
-- ----------------------------
DROP TABLE IF EXISTS `eny_member`;
CREATE TABLE `eny_member` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `username` char(16) NOT NULL COMMENT '登录名',
  `password` char(64) NOT NULL COMMENT '密码',
  `nickname` char(16) NOT NULL COMMENT '昵称',
  `avatar` char(255) NOT NULL COMMENT '头像',
  `gender` enum('未知','男','女') NOT NULL COMMENT '性别',
  `mobile` char(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` char(128) NOT NULL DEFAULT '' COMMENT '邮箱',
  `status` enum('正常','禁用') NOT NULL COMMENT '用户的状态',
  `regtime` char(24) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username_UNIQUE` (`username`) USING HASH COMMENT '用户唯一索引'
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of eny_member
-- ----------------------------
INSERT INTO `eny_member` VALUES ('10000', 'admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'admin', '1', '男', '15959375069', '346745114@qq.com', '正常', '20160301135311');

-- ----------------------------
-- Table structure for eny_menu
-- ----------------------------
DROP TABLE IF EXISTS `eny_menu`;
CREATE TABLE `eny_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目主键id',
  `name` char(8) NOT NULL COMMENT '栏目名称',
  `icon` varchar(24) NOT NULL DEFAULT '' COMMENT '栏目图标',
  `parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `url` varchar(32) NOT NULL DEFAULT 'javascript:;' COMMENT 'url地址',
  `controller` char(16) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `action` char(16) NOT NULL DEFAULT '' COMMENT '执行方法',
  `is_column` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否是侧边栏目',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '默认排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of eny_menu
-- ----------------------------
INSERT INTO `eny_menu` VALUES ('1', '用户管理', 'entypo-users', '0', 'javascript:;', '', '', '1', '1');
INSERT INTO `eny_menu` VALUES ('2', '用户管理', 'entypo-users', '1', 'javascript:;', '', '', '1', '0');
INSERT INTO `eny_menu` VALUES ('3', '列表', '', '2', '/admin/member', 'Member', 'index', '1', '0');
INSERT INTO `eny_menu` VALUES ('4', '统计', '', '2', '/admin/member/count', 'Member', 'count', '1', '0');
INSERT INTO `eny_menu` VALUES ('5', '登录管理', 'entypo-login', '1', 'javascript:;', '', '', '1', '0');
INSERT INTO `eny_menu` VALUES ('6', '7日登录列表数据', '', '5', '/admin/member/seven', 'Member', 'seven', '1', '0');
INSERT INTO `eny_menu` VALUES ('7', '系统设置', 'entypo-cog', '0', 'javascript:;', '', '', '1', '100');
INSERT INTO `eny_menu` VALUES ('8', '管理员', 'entypo-user', '7', 'javascript:;', '', '', '1', '0');
INSERT INTO `eny_menu` VALUES ('9', '列表', '', '8', '/admin/system/admin', 'System', 'admin', '1', '0');
INSERT INTO `eny_menu` VALUES ('10', '管理员组', '', '8', '/admin/system/group', 'System', 'group', '1', '0');
INSERT INTO `eny_menu` VALUES ('11', '权限管理', 'entypo-key', '7', 'javascript:;', '', '', '1', '0');
INSERT INTO `eny_menu` VALUES ('12', '菜单列表', '', '11', '/admin/system/purview', 'System', 'purview', '1', '0');
INSERT INTO `eny_menu` VALUES ('13', '列表添加', '', '11', '/admin/system/purviewadd', 'System', 'purviewadd', '1', '0');
INSERT INTO `eny_menu` VALUES ('14', '系统设置', 'entypo-cog', '7', 'javascript:;', '', '', '1', '0');
INSERT INTO `eny_menu` VALUES ('15', '日志管理', 'entypo-docs', '0', 'javascript:;', '', '', '1', '99');
INSERT INTO `eny_menu` VALUES ('16', '用户日志', 'entypo-dropbox', '15', 'javascript:;', '', '', '1', '0');
INSERT INTO `eny_menu` VALUES ('17', '查看用户日志', '', '16', '/admin/log/member', 'Log', 'member', '1', '0');
INSERT INTO `eny_menu` VALUES ('18', '查看财务日志', '', '16', '/admin/log/finance', 'Log', 'finance', '1', '0');
INSERT INTO `eny_menu` VALUES ('19', '查看管理员日志', '', '16', '/admin/log/admin', 'Log', 'admin', '1', '0');
INSERT INTO `eny_menu` VALUES ('20', '图片管理', 'entypo-picture', '0', 'javascript:;', '', '', '1', '2');

-- ----------------------------
-- Table structure for eny_oauth
-- ----------------------------
DROP TABLE IF EXISTS `eny_oauth`;
CREATE TABLE `eny_oauth` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '来源id，主键用',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `from` enum('QQ','微信','微博','淘宝','百度') DEFAULT NULL COMMENT '第三方名称',
  `open_id` char(128) NOT NULL COMMENT '用户登录token，唯一确定用户标识的信息',
  `access_token` varchar(255) NOT NULL COMMENT '用户登录token，每次会变，获取',
  PRIMARY KEY (`id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户第三方登录表';

-- ----------------------------
-- Records of eny_oauth
-- ----------------------------

-- ----------------------------
-- Table structure for eny_order
-- ----------------------------
DROP TABLE IF EXISTS `eny_order`;
CREATE TABLE `eny_order` (
  `oid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id,也就是订单号',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `pid` int(10) unsigned NOT NULL COMMENT '商品id',
  `product` tinyint(3) unsigned NOT NULL COMMENT '商品种类',
  `unit` decimal(10,3) NOT NULL COMMENT '单价',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,3) NOT NULL COMMENT '总价',
  `status` enum('未支付','已支付') DEFAULT NULL COMMENT '订单状态',
  `detail` text NOT NULL COMMENT '支出详细信息',
  `addtime` int(11) NOT NULL COMMENT '订单生成时间',
  `del` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '订单是否已经删除，1-未删除，2-已删除',
  PRIMARY KEY (`oid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of eny_order
-- ----------------------------

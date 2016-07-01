/*
Navicat MySQL Data Transfer

Source Server         : 搬瓦工
Source Server Version : 50711
Source Host           : 23.83.239.34:3306
Source Database       : enychen

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2016-07-01 17:55:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目主键id',
  `name` char(8) NOT NULL COMMENT '栏目名称',
  `icon` varchar(24) NOT NULL DEFAULT '' COMMENT '栏目图标',
  `parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `url` varchar(32) NOT NULL DEFAULT 'javascript:;' COMMENT 'url地址',
  `controller` char(16) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `action` char(16) NOT NULL DEFAULT '' COMMENT '执行方法',
  `is_column` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否是侧边栏目',
  `sort` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '默认排序',
  PRIMARY KEY (`id`),
  KEY `permission` (`id`,`is_column`) USING BTREE COMMENT '获取用户的权限',
  KEY `url` (`controller`,`action`) USING BTREE COMMENT '用户检查是否有访问权限'
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES ('1', '用户管理', 'entypo-users', '0', 'javascript:;', '', '', '1', '1');
INSERT INTO `admin_menu` VALUES ('2', '用户列表', 'entypo-user', '1', '/admin/user/list/', 'user', 'list', '1', '1');
INSERT INTO `admin_menu` VALUES ('3', '日志管理', 'entypo-doc-text-inv', '0', 'javascript:;', '', '', '1', '3');
INSERT INTO `admin_menu` VALUES ('4', '系统设置', 'entypo-cog', '0', 'javascript:;', '', '', '1', '100');
INSERT INTO `admin_menu` VALUES ('5', '财务管理', 'entypo-paypal', '1', '/admin/user/finance/', 'user', 'finance', '1', '2');
INSERT INTO `admin_menu` VALUES ('6', '权限管理', 'entypo-key', '4', '/admin/setting/rules/', 'setting', 'rules', '1', '2');
INSERT INTO `admin_menu` VALUES ('7', '管理员', 'entypo-user', '4', 'javascript:;', '', '', '1', '1');
INSERT INTO `admin_menu` VALUES ('8', '用户日志', 'entypo-list', '3', 'javascript:;', '', '', '1', '2');
INSERT INTO `admin_menu` VALUES ('9', '登录日志', '', '8', '/admin/log/login/', 'log', 'login', '1', '1');
INSERT INTO `admin_menu` VALUES ('10', '管理员日志', 'entypo-database', '3', 'javascript:;', '', '', '1', '3');
INSERT INTO `admin_menu` VALUES ('11', '登录日志', '', '10', '/admin/admin/login/', 'admin', 'login', '1', '0');
INSERT INTO `admin_menu` VALUES ('12', '操作日志', '', '10', '/admin/admin/opera/', 'admin', 'opera', '1', '0');
INSERT INTO `admin_menu` VALUES ('13', '财务日志', '', '8', '/admin/log/finance/', 'log', 'finance', '1', '0');
INSERT INTO `admin_menu` VALUES ('14', '列表', '', '7', '/admin/admin/index/', 'admin', 'index', '1', '0');
INSERT INTO `admin_menu` VALUES ('15', '管理组', '', '7', '/admin/admin/group/', 'admin', 'group', '1', '0');

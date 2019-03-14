/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50723
Source Host           : localhost:3306
Source Database       : base

Target Server Type    : MYSQL
Target Server Version : 50723
File Encoding         : 65001

Date: 2019-03-14 16:32:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for be_admin
-- ----------------------------
DROP TABLE IF EXISTS `be_admin`;
CREATE TABLE `be_admin` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `name` varchar(30) NOT NULL COMMENT '管理员名称',
  `password` char(32) NOT NULL COMMENT '管理员密码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of be_admin
-- ----------------------------
INSERT INTO `be_admin` VALUES ('1', 'admin', 'e10adc3949ba59abbe56e057f20f883e');
INSERT INTO `be_admin` VALUES ('28', 'admin000', 'e10adc3949ba59abbe56e057f20f883e');
INSERT INTO `be_admin` VALUES ('29', 'admin123', 'e10adc3949ba59abbe56e057f20f883e');

-- ----------------------------
-- Table structure for be_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `be_auth_group`;
CREATE TABLE `be_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of be_auth_group
-- ----------------------------
INSERT INTO `be_auth_group` VALUES ('1', '超级管理员', '1', '15,16,17,18,19,1,11,12,13,14,9,2,3,10,20,4');
INSERT INTO `be_auth_group` VALUES ('3', '链接专员', '1', '2,3,20,10,4');
INSERT INTO `be_auth_group` VALUES ('4', '配置管理员', '1', '1,9,11,14,13,12');

-- ----------------------------
-- Table structure for be_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `be_auth_group_access`;
CREATE TABLE `be_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of be_auth_group_access
-- ----------------------------
INSERT INTO `be_auth_group_access` VALUES ('1', '1');
INSERT INTO `be_auth_group_access` VALUES ('28', '1');
INSERT INTO `be_auth_group_access` VALUES ('29', '4');

-- ----------------------------
-- Table structure for be_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `be_auth_rule`;
CREATE TABLE `be_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `pid` mediumint(9) NOT NULL DEFAULT '0',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(5) NOT NULL DEFAULT '50',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of be_auth_rule
-- ----------------------------
INSERT INTO `be_auth_rule` VALUES ('1', 'sys', '系统设置', '1', '1', '', '0', '0', '7');
INSERT INTO `be_auth_rule` VALUES ('2', 'link', '友情链接', '1', '1', '', '0', '0', '4');
INSERT INTO `be_auth_rule` VALUES ('3', 'link/lst', '链接列表', '1', '1', '', '2', '1', '5');
INSERT INTO `be_auth_rule` VALUES ('4', 'link/del', '删除链接', '1', '1', '', '3', '2', '6');
INSERT INTO `be_auth_rule` VALUES ('11', 'conf/lst', '配置列表', '1', '1', '', '1', '1', '50');
INSERT INTO `be_auth_rule` VALUES ('10', 'link/add', '添加链接', '1', '1', '', '3', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('9', 'conf/conf', '配置项', '1', '1', '', '1', '1', '50');
INSERT INTO `be_auth_rule` VALUES ('12', 'conf/add', '添加配置', '1', '1', '', '11', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('13', 'conf/del', '配置删除', '1', '1', '', '11', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('14', 'conf/edit', '配置编辑', '1', '1', '', '11', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('15', 'admin', '管理员', '1', '1', '', '0', '0', '50');
INSERT INTO `be_auth_rule` VALUES ('16', 'admin/lst', '管理员列表', '1', '1', '', '15', '1', '50');
INSERT INTO `be_auth_rule` VALUES ('17', 'admin/add', '管理员添加', '1', '1', '', '16', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('18', 'admin/del', '管理员删除', '1', '1', '', '16', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('19', 'admin/edit', '管理员修改', '1', '1', '', '16', '2', '50');
INSERT INTO `be_auth_rule` VALUES ('20', 'index/test', '修改链接', '1', '1', '', '3', '2', '50');

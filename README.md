# layuiAdmin

> 数据库表（基本）



/*
 Navicat MySQL Data Transfer

 Source Server         : A-本地数据库
 Source Server Type    : MySQL
 Source Server Version : 50635
 Source Host           : 127.0.0.1:3306
 Source Schema         : layuiAdmin

 Target Server Type    : MySQL
 Target Server Version : 50635
 File Encoding         : 65001

 Date: 04/05/2020 17:39:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for queue_list
-- ----------------------------
DROP TABLE IF EXISTS `queue_list`;
CREATE TABLE `queue_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `msg` text COMMENT '错误信息',
  `file_name` varchar(100) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件下载地址',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1-待处理 2-处理中 3-已完成',
  `result` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '处理结果 1-全部成功 2-全部失败 3-解析失败 4-部分失败',
  `module` int(11) NOT NULL DEFAULT '0' COMMENT '模块',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型 1-导入 2-导出',
  `operator` int(11) NOT NULL COMMENT '操作人id',
  `operate_time` datetime DEFAULT NULL COMMENT '操作时间',
  `complete_time` datetime DEFAULT NULL COMMENT '完成时间',
  `source_file_name` varchar(100) DEFAULT NULL COMMENT '上传文件名称',
  `source_file_url` varchar(255) DEFAULT NULL COMMENT '上传文件地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='队列';

-- ----------------------------
-- Table structure for skus
-- ----------------------------
DROP TABLE IF EXISTS `skus`;
CREATE TABLE `skus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(25) DEFAULT NULL,
  `spu` varchar(25) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL COMMENT '分类id',
  `serviceline` int(11) DEFAULT NULL COMMENT '业务线id',
  `name` varchar(100) DEFAULT NULL COMMENT '采购名称',
  `reference_price` decimal(15,4) DEFAULT NULL COMMENT '参考价格（RMB）',
  `status` tinyint(1) DEFAULT '1' COMMENT 'SKU状态 0禁用 1启用',
  `sales_status` tinyint(1) DEFAULT NULL COMMENT '销售状态 ①正常在售 ②清库存 ③下架  ④清库存（侵权/违禁）⑤包材 ⑥断货 ⑦菜鸟模型 ⑧海外仓  ⑨亚马逊 ⑩下架（侵权/违禁）⑪ IT冻结',
  `sales_status_old` tinyint(4) DEFAULT NULL COMMENT '旧销售状态',
  `operator` varchar(20) DEFAULT NULL COMMENT '创建人',
  `creation_time` datetime DEFAULT NULL COMMENT '创建时间',
  `modification_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `modification_operator` varchar(20) DEFAULT NULL COMMENT '修改操作人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `spu_id` (`spu`),
  KEY `modification_time` (`modification_time`)
) ENGINE=InnoDB AUTO_INCREMENT=3869522 DEFAULT CHARSET=utf8 COMMENT='sku主表';

-- ----------------------------
-- Records of skus
-- ----------------------------
BEGIN;
INSERT INTO `skus` VALUES (1, 'BR508', '5303', 31510, 0, '19V 3.42A适配器', 15.0000, 1, 1, NULL, NULL, '2016-11-03 00:00:00', '2019-10-30 17:43:10', '连雄山');
INSERT INTO `skus` VALUES (2, 'AA301', '160', 175725, 0, '圆形仿真摄像头', 3.5000, 1, 4, NULL, NULL, '2016-11-03 00:00:00', '2019-11-08 10:01:47', '谢俊宇');
INSERT INTO `skus` VALUES (3, 'AD308', '3346', 61312, 0, 'Wii方向控制器', 32.0000, 1, 8, NULL, NULL, '2016-11-03 00:00:00', '2019-10-11 09:52:31', '谢俊宇');
INSERT INTO `skus` VALUES (4, 'AC309', '1354', 48709, 1, '汽车高音喇叭（连接线颜色随机）', 2.5000, 1, 7, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (15, 'R403', '', 67741, 0, '乐迪6通升级版接收机', 55.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-11-29 14:07:52', NULL);
INSERT INTO `skus` VALUES (17, 'R405', '192318', 182213, 0, '450碳纤空机', 226.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (19, 'R407', '5030', 123847, 0, '450 pro 空机', 240.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (21, 'R409', '389304', 177805, 0, '1：10大脚车（铝合金底+塑胶支架）', 310.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (23, 'R411', '389305', 182213, 0, '450V2尾翼', 1.2500, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-11-08 11:25:12', NULL);
INSERT INTO `skus` VALUES (26, 'R414', '389306', 182213, 0, '1:10大脚车（碳纤底+全升级件）', 760.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (30, 'R418', '389307', 63713, 0, '1:8 SAISU 山地油动', 1350.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (31, 'R419', '389308', 182183, 0, '1:10 拉力车油动1980（不带遥控）', 710.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (32, 'R420', '389309', 53908, 0, '1:10 SAISU 山地油动1986（不带遥控）', 695.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (33, 'R501', '5188', 31510, 0, '250直升机/250碳纤空机/亚拓250空机/(含玻纤头罩+桨）', 220.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-11-08 11:58:17', NULL);
INSERT INTO `skus` VALUES (34, 'R502', '4011', 0, 0, 'KDS900陀螺仪套装', 580.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-10 10:09:25', NULL);
INSERT INTO `skus` VALUES (36, 'R504', '389310', 40703, 0, 'CR4D接收机', 70.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (37, 'R505', '4660', 146247, 0, 'CR6D接收机', 84.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (43, 'R511', '4010', 20655, 0, 'KDS平衡仪', 400.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (54, 'R602', '389311', 182213, 0, '主旋翼握集', 33.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (55, 'R603', '389312', 3220, 0, '平衡翼跷跷板枢纽', 15.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (58, 'R606', '389313', 36802, 0, '向位器', 8.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (59, 'R607', '389314', 174089, 0, '向位器拉臂组件', 22.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (60, 'R608', '389315', 172511, 0, '稳定杆', 1.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (61, 'R609', '389316', 46304, 0, '稳定刀片', 1.2500, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (62, 'R610', '389317', 0, 0, '主轴承固定座', 6.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (63, 'R611', '389318', 182213, 0, '尾传动齿轮组', 12.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (64, 'R612', '389319', 0, 0, '碳纤维电池托盘', 14.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (65, 'R613', '13594', 0, 0, 'TITAN机头罩火焰红', 14.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (66, 'R614', '389320', 20705, 0, '富斯3通发射机', 201.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (70, 'R701', '389321', 182213, 0, '电机安装部件', 8.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (71, 'R702', '389322', 0, 0, '反旋转支架', 8.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (72, 'R703', '389323', 0, 0, '尾梁', 22.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (73, 'R704', '389324', 168061, 0, '底板', 2.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (74, 'R705', '389325', 182213, 0, '尾旋翼控制组', 14.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (75, 'R706', '389326', 182213, 0, '尾桨握', 20.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (76, 'R707', '389327', 0, 0, '盘尾', 15.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (77, 'R708', '389328', 0, 0, '平行翼固定块', 7.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (78, 'R709', '389329', 0, 0, '450V2通用脚垫', 1.5000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (79, 'R710', '389330', 9887, 0, '螺丝垫片', 18.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (81, 'R712', '389331', 123847, 0, 'KDS800陀螺仪', 95.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (83, 'R714', '5326', 182213, 0, '16G胶舵机 test', 16.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 14:58:00', NULL);
INSERT INTO `skus` VALUES (84, 'R801', '389332', 97126, 0, '机壳柱', 1.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (85, 'R802', '389333', 182213, 0, '450桨托', 1.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (86, 'R803', '389334', 179753, 0, '齿轮空心轴', 3.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (87, 'R804', '389335', 0, 0, '水平翼/垂直翼', 17.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (88, 'R805', '389336', 182213, 0, '尾齿轮轴', 4.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (89, 'R806', '389337', 0, 0, '横轴组件（4支+2个螺丝带垫片）', 3.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (90, 'R807', '389338', 0, 0, '主轴（3支装）', 5.5000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (91, 'R808', '389339', 42905, 0, '整机螺丝', 8.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (92, 'R809', '389340', 43504, 0, '球头包装 10个装', 5.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-07-04 15:39:40', NULL);
INSERT INTO `skus` VALUES (93, 'R810', '389341', 258620, 0, '450三桨头', 125.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (94, 'R811', '389342', 35562, 0, '450四桨头', 130.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (100, 'S305', '389343', 2211, 0, 'HAWK SKY 72M泡沫飞机', 480.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (102, 'S307', '389344', 1345, 0, '日本村田陀螺仪', 0.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (103, 'S308', '13596', 40703, 1, '富斯FS9通2.4G接收机9通接收机', 55.0000, 1, 3, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (104, 'S309', '389345', 176985, 0, '富斯九通高频头', 70.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (105, 'S310', '389346', 61312, 0, '2.4G天地飞8通遥控器', 1030.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (108, 'S313', '389347', 182213, 0, '田宫416WE 皮带1:10半金属', 486.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (110, 'S315', '389348', 182213, 0, 'XRAY M18皮带1:18', 310.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (116, 'S402', '389349', 43117, 0, '500碳纤空机', 420.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (117, 'S403', '389350', 40703, 0, '富斯6通发射机35M', 116.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2015-10-23 15:44:48', NULL);
INSERT INTO `skus` VALUES (118, 'S404', '2507', 40703, 0, '富斯6通发射机72M', 116.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (119, 'S405', '12638', 182213, 0, '富斯6通发射机2.4G', 120.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (120, 'S406', '2506', 40703, 0, '富斯9通发射机2.4G', 330.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (121, 'S407', '13597', 9161, 0, '乐迪发射机2.4G6通左手油门', 190.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (122, 'S408', '2826', 182213, 0, '迪乐美模拟器', 62.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (124, 'S410', '389351', 2983, 0, '新3.5通黄色带陀螺仪飞机', 75.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (126, 'S412', '389352', 20441, 0, '450V3碳纤空机-金阳光', 235.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (130, 'S501', '389353', 58167, 0, '三通大阿帕奇20605绿色-带陀螺仪', 108.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (134, 'S505', '1230', 123847, 0, '三通合金飛機20601-B帶陀螺儀黑色', 64.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (135, 'S506', '389354', 155101, 0, '210A绿色', 220.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (138, 'S509', '389355', 41243, 0, '450V3塑料空机新款', 145.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (141, 'S512', '3385', 182183, 0, 'VH-32 1:10 整车', 380.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (149, 'S601', '13598', 40703, 0, '富斯FS-GT2发射机(黑色)', 79.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (150, 'S602', '389356', 36628, 0, '450铝箱银色', 85.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (151, 'S603', '389356', 25645, 0, '450铝箱黑色', 89.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (152, 'S604', '4968', 182213, 0, '500铝箱黑色', 126.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (163, 'S615', '13599', 139973, 0, 'TITAN 450V2 RTF金属版', 710.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (164, 'S616', '3573', 774, 0, 'TITAN 450PRO RTF碳纤版', 755.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2018-10-09 09:32:29', NULL);
INSERT INTO `skus` VALUES (170, 'S622', '389357', 14899, 0, '司马 S032', 128.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (171, 'S623', '389358', 123847, 0, '双马 9053', 185.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (172, 'S624', '389359', 171228, 0, '司马108G', 85.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (174, 'S702', '389360', 80015, 0, 'FMT-XTR模拟器', 22.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (175, 'S703', '13600', 182213, 0, 'B6充电2S 3S转接板', 3.5000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (176, 'S704', '389361', 129138, 0, '4.0 热缩管', 0.2500, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (177, 'S705', '389362', 161586, 0, '5ML螺丝胶', 15.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (178, 'S706', '4474', 182213, 0, 'GY401陀螺仪', 54.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (180, 'S708', '13601', 182213, 0, '遥控器铝箱', 40.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (181, 'S709', '389363', 182189, 1, 'GXR-15发动机', 3.5000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (182, 'S710', '192321', 63696, 0, 'GXR-18发动机', 301.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-11-08 12:02:40', NULL);
INSERT INTO `skus` VALUES (183, 'S711', '389364', 0, 0, 'GXR-28发动机', 399.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-07-04 08:39:12', NULL);
INSERT INTO `skus` VALUES (184, 'S712', '389365', 182213, 0, '好盈60A电调', 143.8000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (185, 'S713', '3293', 182213, 0, 'XT60黄色母头', 0.9000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (186, 'S714', '3293', 182213, 0, 'XT60黄色公头', 0.8000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (189, 'S801', '389366', 0, 0, '604泡沫飞机', 35.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (190, 'S802', '389367', 162517, 0, '九鹰260A红色飞机', 240.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (191, 'S803', '13602', 182213, 0, 'JTL0904A 6通模拟器（左手）', 75.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (192, 'S804', '389368', 99697, 0, '450V3整机', 766.8000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (193, 'S805', '389369', 158925, 0, '3D练习架', 80.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (195, 'S807', '389370', 182182, 0, 'T505黄色', 65.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (196, 'S808', '13603', 47349, 0, '遥控器支架蓝色', 12.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (198, 'S810', '2414', 182213, 0, '黑白桨托', 0.6000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (200, 'S812', '2187', 56615, 0, '简易舵机测试仪', 4.4000, 1, 3, NULL, NULL, '0000-00-00 00:00:00', '2018-12-13 14:55:57', NULL);
INSERT INTO `skus` VALUES (201, 'S813', '13604', 182213, 0, '富斯6通2.4G新款接收机', 38.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (202, 'S814', '4561', 182178, 0, 'EC5插头1套（2个蓝色外壳+2对香蕉公母头）', 2.6000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (209, 'S821', '389371', 170098, 0, '好盈车用设定卡', 32.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (210, 'S822', '5241', 50637, 0, '206仿碳桨', 6.2000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (211, 'S823', '13605', 182213, 0, 'EC3插头1套（2个蓝色外壳+2对香蕉公母头）', 1.8000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (222, 'S908', '13606', 182213, 0, '40A无刷电调', 22.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (223, 'S909', '389372', 182213, 0, 'TITAN 450RTF塑料板黑色', 468.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (228, 'BR301', '13607', 182213, 0, 'SM-S4303R机器人舵机', 26.7800, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (229, 'BR302', '3621', 182213, 0, 'SM-S4306R机器人舵机', 32.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (230, 'BR303', '13608', 182213, 0, 'SM-S4309R机器人舵机', 50.2500, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (231, 'BR304', '13609', 182213, 0, 'SM-S4315R机器人舵机', 51.3400, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (232, 'BR305', '389373', 34061, 0, 'SR401P机器人舵机', 36.7600, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (233, 'BR306', '13610', 182213, 0, 'SR402P机器人舵机', 55.7100, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (234, 'BR307', '389374', 48718, 0, '好盈35A', 170.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (235, 'BR308', '389375', 40703, 0, 'DX6I摇控器(左手）', 390.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (236, 'BR309', '13611', 182213, 0, 'SR403P机器人舵机', 65.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (237, 'BR310', '389376', 185223, 0, '显微镜MG10081-8', 19.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (238, 'BR311', '389377', 123474, 0, '单车车铃指南针红色', 4.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (239, 'BR312', '389377', 123474, 0, '单车车铃指南针黑色', 4.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (240, 'BR313', '389379', 64345, 0, '永诺红外线遥控器ML-L3', 8.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2014-09-20 19:30:03', NULL);
INSERT INTO `skus` VALUES (241, 'BR314', '13612', 182183, 0, '富斯GR3E三通道接收机', 24.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (243, 'BR316', '2550', 185223, 0, '放大镜MG10081-4', 6.1000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (244, 'BR317', '192247', 183695, 0, '显微镜9886', 38.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (245, 'BR318', '389380', 494, 0, '显微镜9882', 14.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2016-12-10 19:14:34', NULL);
INSERT INTO `skus` VALUES (247, 'BR402', '389381', 0, 0, '2G舵机', 20.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (248, 'BR403', '5290', 182213, 0, '2*1.5G舵机', 39.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (249, 'BR404', '13613', 182178, 0, '6.5香蕉头公', 0.6000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (250, 'BR405', '13614', 182178, 0, '6.5香蕉头母', 0.6000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (252, 'BR407', '2235', 182213, 0, '加速黑色9G舵机', 9.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (254, 'BR409', '2980', 10554, 0, 'TITANB3', 15.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2018-09-18 17:20:03', NULL);
INSERT INTO `skus` VALUES (255, 'BR410', '1747', 123417, 0, '美规电源线', 1.1000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (256, 'BR411', '1538', 182097, 0, '欧规电源线', 1.1000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (257, 'BR412', '3248', 182097, 0, '澳规电源线', 2.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (264, 'BR505', '389382', 182213, 0, '16G金属舵机', 23.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (265, 'BR506', '3568', 182213, 0, 'TITAN黑色9G舵机', 9.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (266, 'BR507', '5319', 31510, 0, '18.5V 3.5A 适配器', 15.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (269, 'R615', '389383', 0, 0, 'SG90舵机齿轮', 1.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (282, 'F304', '265', 14964, 0, '音频1分2莲花头', 0.6500, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (283, 'F305', '4751', 50602, 0, 'AG13电池（10粒）', 1.0000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (284, 'F306', '389384', 3668, 0, 'LGH-IDE-K转接卡', 2.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (285, 'F307', '389385', 74941, 0, 'SATA  红色硬盘线', 1.8000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2014-09-20 19:30:03', NULL);
INSERT INTO `skus` VALUES (286, 'F308', '3933', 20695, 0, 'LG手机数据线', 2.6000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (289, 'F311', '389386', 0, 0, '热熔胶条', 0.2000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (291, 'F313', '389387', 11900, 0, '护膝', 6.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (292, 'F314', '389388', 44932, 0, 'USB2.0打印线', 1.6000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (293, 'F401', '1055', 43506, 0, '收纳鞋盒', 4.5000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (294, 'F402', '2894', 171243, 0, '磁性袖套', 12.8000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2018-01-29 22:29:06', NULL);
INSERT INTO `skus` VALUES (298, 'F406', '1097', 4616, 0, '摄像头', 11.5000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (299, 'F407', '875', 35190, 0, '手机座绿色', 18.9000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (301, 'F409', '389389', 182213, 0, '乐迪6通接收机', 45.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2016-04-09 09:26:18', NULL);
INSERT INTO `skus` VALUES (302, 'F501', '389390', 3201, 0, '拉线灯', 5.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (303, 'F502', '389391', 171814, 0, 'XBO*360适配器+光盘', 16.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2014-09-20 19:30:03', NULL);
INSERT INTO `skus` VALUES (304, 'F503', '389392', 36449, 0, '下巴按摩器', 8.0000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (305, 'F504', '2748', 179966, 0, '钓鱼线', 4.8000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (310, 'L301', '389393', 62132, 0, '自行车水壶架(带螺丝)', 2.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (311, 'L302', '5229', 16037, 0, '210-1W 手电', 15.7000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (312, 'L303', '1614', 139971, 0, '蘑菇投影仪橙蓝色', 20.4000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2018-01-29 21:41:53', NULL);
INSERT INTO `skus` VALUES (314, 'L305', '389394', 106984, 0, '8301-9LED 头灯', 7.6000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (317, 'L308', '4963', 774, 0, '500碳纤尾撑', 17.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2018-10-09 18:27:09', NULL);
INSERT INTO `skus` VALUES (318, 'L309', '151', 75041, 1, '钥匙扣药筒', 1.2000, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (319, 'L310', '389395', 179972, 0, '钓鱼线500M 50BL 0.7MM', 8.1000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (320, 'L311', '389396', 20589, 0, '电子定时器', 6.2000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (321, 'L312', '2096', 9972, 0, '卡片计算器', 2.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (322, 'L401', '192297', 16037, 0, '5101-5LED手电', 4.6000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (323, 'L402', '389397', 106984, 0, '8303-19LED头灯', 8.0000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2015-09-21 14:08:09', NULL);
INSERT INTO `skus` VALUES (324, 'L403', '4787', 168867, 0, '9620D-9C+3LED挂灯', 19.8000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (325, 'L404', '5010', 182213, 0, '450十字盘', 25.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2017-12-13 15:01:05', NULL);
INSERT INTO `skus` VALUES (326, 'L405', '389398', 182213, 0, '450平衡翼', 2.5000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2018-09-09 10:04:39', NULL);
INSERT INTO `skus` VALUES (327, 'L406', '4962', 182213, 0, '500主副齿（1对）', 5.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (328, 'L407', '389399', 30093, 0, '450脚架', 2.3000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (329, 'L408', '389400', 0, 0, '450尾管支撑架', 4.0000, 1, 8, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (330, 'L409', '4071', 123417, 0, 'iphone火箭头车充', 3.2000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (331, 'L410', '3318', 117042, 1, 'XBO*360手柄转接线', 2.5500, 1, 1, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (333, 'L412', '192298', 82599, 0, '睫毛刷', 15.5000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
INSERT INTO `skus` VALUES (334, 'L413', '5179', 168867, 0, '267D-5C +1挂灯', 12.6000, 1, 4, NULL, NULL, '0000-00-00 00:00:00', '2016-07-07 10:26:46', NULL);
INSERT INTO `skus` VALUES (337, 'L503', '5031', 123847, 0, '450P-2 PRO机头罩', 14.5000, 1, 12, NULL, NULL, '0000-00-00 00:00:00', '2019-10-11 09:52:31', NULL);
COMMIT;

-- ----------------------------
-- Table structure for sys_log
-- ----------------------------
DROP TABLE IF EXISTS `sys_log`;
CREATE TABLE `sys_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(20) DEFAULT '',
  `time` decimal(8,2) DEFAULT '0.00' COMMENT '耗时',
  `type` int(11) DEFAULT '1' COMMENT '500错误',
  `module` varchar(15) DEFAULT NULL,
  `controller` varchar(25) DEFAULT NULL,
  `action` varchar(25) DEFAULT NULL,
  `data` text,
  `ip` varchar(15) DEFAULT '',
  `ctime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `action` (`action`),
  FULLTEXT KEY `data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='日志表';

-- ----------------------------
-- Table structure for sys_log_config
-- ----------------------------
DROP TABLE IF EXISTS `sys_log_config`;
CREATE TABLE `sys_log_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text COMMENT '监听的用户',
  `all` tinyint(4) DEFAULT '0' COMMENT '1为监听所有',
  `action` text COMMENT '监听的操作方法逗号隔开',
  `notaction` text COMMENT '不监听的操作方法',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='日志配置表';

-- ----------------------------
-- Records of sys_log_config
-- ----------------------------
BEGIN;
INSERT INTO `sys_log_config` VALUES (1, '', 1, '', 'Logindex');
COMMIT;

-- ----------------------------
-- Table structure for sys_menu
-- ----------------------------
DROP TABLE IF EXISTS `sys_menu`;
CREATE TABLE `sys_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `title` varchar(20) NOT NULL COMMENT '菜单标题',
  `icon` varchar(80) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(200) NOT NULL COMMENT '链接地址(模块/控制器/方法)',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `nav` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否为菜单显示，1显示0不显示',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态1启用，0禁用',
  `module` varchar(20) NOT NULL DEFAULT 'index' COMMENT '模块名',
  `ctime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='[系统] 管理菜单';

-- ----------------------------
-- Records of sys_menu
-- ----------------------------
BEGIN;
INSERT INTO `sys_menu` VALUES (4, 0, '系统设置', 'fa fa-cog', '1', 5, 1, 1, 'index', '0000-00-00 00:00:00');
INSERT INTO `sys_menu` VALUES (5, 4, '系统基础', 'fa fa-cog', '1', 0, 1, 1, 'index', '2019-08-23 15:57:03');
INSERT INTO `sys_menu` VALUES (6, 5, '用户列表', '', 'User/getList', 0, 1, 1, 'index', '2019-08-23 16:06:09');
INSERT INTO `sys_menu` VALUES (7, 5, '角色列表', '', 'System/roleList', 0, 1, 1, 'index', '2019-08-23 16:07:06');
INSERT INTO `sys_menu` VALUES (8, 5, '菜单管理', '', 'System/menu', 0, 1, 1, 'index', '2019-08-23 16:30:28');
INSERT INTO `sys_menu` VALUES (11, 6, '用户编辑', '', 'User/edit', 0, 0, 1, 'index', '2019-08-26 14:15:51');
INSERT INTO `sys_menu` VALUES (22, 0, '队列管理', '', '1', 3, 1, 1, 'index', '2019-10-28 11:46:23');
INSERT INTO `sys_menu` VALUES (23, 22, '队列列表', 'fa fa-list-ul', '1', 0, 1, 1, 'index', '2019-10-28 11:47:11');
INSERT INTO `sys_menu` VALUES (24, 23, '队列信息', '', 'Queue/index', 0, 1, 1, 'index', '2019-10-28 11:47:59');
INSERT INTO `sys_menu` VALUES (36, 7, '角色编辑', '', 'System/roleForm', 0, 0, 1, 'index', '2019-11-21 18:46:49');
INSERT INTO `sys_menu` VALUES (37, 8, '菜单编辑', '', 'System/menuEdit', 0, 0, 1, 'index', '2019-11-21 18:49:39');
INSERT INTO `sys_menu` VALUES (38, 8, '菜单新增', '', 'System/menuAdd', 0, 0, 1, 'index', '2019-11-21 18:50:06');
INSERT INTO `sys_menu` VALUES (40, 7, '角色新增', '', 'System/roleFormAdd', 0, 0, 1, 'index', '2019-11-28 15:12:11');
INSERT INTO `sys_menu` VALUES (41, 0, 'Layui模块', '', '1', 0, 1, 1, 'index', '0000-00-00 00:00:00');
INSERT INTO `sys_menu` VALUES (42, 41, '表单', 'fa fa-folder-open', '1', 0, 1, 1, 'index', '0000-00-00 00:00:00');
INSERT INTO `sys_menu` VALUES (43, 42, '表格|TABLE', '', 'Module/table', 1, 1, 1, 'index', '0000-00-00 00:00:00');
INSERT INTO `sys_menu` VALUES (44, 42, '进度条|TAB', '', 'Module/index', 0, 1, 1, 'index', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for sys_role
-- ----------------------------
DROP TABLE IF EXISTS `sys_role`;
CREATE TABLE `sys_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态1启用0禁用',
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `user` varchar(20) DEFAULT NULL COMMENT '最后操作人',
  `auth` text NOT NULL COMMENT '角色权限',
  `ctime` datetime NOT NULL COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理角色';

-- ----------------------------
-- Records of sys_role
-- ----------------------------
BEGIN;
INSERT INTO `sys_role` VALUES (1, 1, 'admin', 'admin', '', '0000-00-00 00:00:00', '2020-05-04 10:46:21');
COMMIT;

-- ----------------------------
-- Table structure for sys_sync
-- ----------------------------
DROP TABLE IF EXISTS `sys_sync`;
CREATE TABLE `sys_sync` (
  `type` int(11) NOT NULL COMMENT '同步的类型 1为同步sku数据到老系统',
  `runtime` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '最后执行时间',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sys_user
-- ----------------------------
DROP TABLE IF EXISTS `sys_user`;
CREATE TABLE `sys_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cn_name` varchar(30) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `role_id` varchar(255) NOT NULL DEFAULT '' COMMENT '角色ID',
  `auth` text COMMENT '独立权限',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1启动2禁用',
  `is_change` tinyint(4) DEFAULT '0' COMMENT '1权限有变',
  `operat_user` varchar(30) DEFAULT '' COMMENT '操作人',
  `operat_time` datetime DEFAULT NULL COMMENT '操作时间',
  `bg` varchar(20) DEFAULT '1' COMMENT '主题颜色',
  `ctime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `utime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户';

-- ----------------------------
-- Records of sys_user
-- ----------------------------
BEGIN;
INSERT INTO `sys_user` VALUES (1, '超管', 'admin', '1', NULL, 1, 0, 'admin', NULL, '1', '0000-00-00 00:00:00', '2020-05-04 10:45:55');
COMMIT;

-- ----------------------------
-- Triggers structure for table skus
-- ----------------------------
DROP TRIGGER IF EXISTS `skus_add_before`;
delimiter ;;
CREATE TRIGGER `skus_add_before` BEFORE INSERT ON `skus` FOR EACH ROW BEGIN

INSERT INTO sys_sync_sku (sku, `time`) VALUES (new.sku,NOW()) ON DUPLICATE KEY UPDATE `time`=VALUES(`time`);


END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table skus
-- ----------------------------
DROP TRIGGER IF EXISTS `skus_update_before`;
delimiter ;;
CREATE TRIGGER `skus_update_before` BEFORE UPDATE ON `skus` FOR EACH ROW BEGIN

INSERT INTO sys_sync_sku (sku, `time`) VALUES (new.sku,NOW()) ON DUPLICATE KEY UPDATE `time`=VALUES(`time`);

	SET  @gather= '{';
	SET  @gatherold= '{';

SET @f1=IF(CHAR_LENGTH(old.spu)>0,old.spu,'');

SET @f2= IF(CHAR_LENGTH(new.spu)>0,new.spu,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"spu":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"spu":"',@f1,'",');
	
	END IF;

SET @f1=IF(CHAR_LENGTH(old.category_id)>0,old.category_id,'');

SET @f2= IF(CHAR_LENGTH(new.category_id)>0,new.category_id,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"category_id":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"category_id":"',@f1,'",');
	
	END IF;

SET @f1=IF(CHAR_LENGTH(old.serviceline)>0,old.serviceline,'');

SET @f2= IF(CHAR_LENGTH(new.serviceline)>0,new.serviceline,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"serviceline":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"serviceline":"',@f1,'",');
	
	END IF;

SET @f1=IF(CHAR_LENGTH(old.name)>0,old.name,'');

SET @f2= IF(CHAR_LENGTH(new.name)>0,new.name,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"name":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"name":"',@f1,'",');
	
	END IF;

SET @f1=IF(CHAR_LENGTH(old.reference_price)>0,old.reference_price,'');

SET @f2= IF(CHAR_LENGTH(new.reference_price)>0,new.reference_price,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"reference_price":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"reference_price":"',@f1,'",');
	
	END IF;

SET @f1=IF(CHAR_LENGTH(old.status)>0,old.status,'');

SET @f2= IF(CHAR_LENGTH(new.status)>0,new.status,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"status":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"status":"',@f1,'",');
	
	END IF;

SET @f1=IF(CHAR_LENGTH(old.sales_status)>0,old.sales_status,'');

SET @f2= IF(CHAR_LENGTH(new.sales_status)>0,new.sales_status,'');

IF  @f1 != @f2  THEN

	SET @gather=CONCAT(@gather,'"sales_status":"',@f2,'",');
	
	SET @gatherold=CONCAT(@gatherold,'"sales_status":"',@f1,'",');
	
	END IF;


    SET  @gather=CONCAT(SUBSTRING(@gather,1,CHAR_LENGTH(@gather)-1),'}');
    
    SET  @gatherold=CONCAT(SUBSTRING(@gatherold,1,CHAR_LENGTH(@gatherold)-1),'}');


    IF @gather!='}' THEN
    
    INSERT INTO log_record (sku,new_val,old_val,operator)VALUES (new.sku,@gather,@gatherold,new.modification_operator);
    
    END IF;


​    
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
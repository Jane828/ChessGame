/*
 Navicat Premium Data Transfer

 Source Server         : www.wx.tt-cool.com
 Source Server Type    : MySQL
 Source Server Version : 100036
 Source Host           : localhost:3306
 Source Schema         : poker

 Target Server Type    : MySQL
 Target Server Version : 100036
 File Encoding         : 65001

 Date: 18/10/2018 15:52:46
*/
CREATE DATABASE IF NOT EXISTS `poker` default character set utf8mb4 collate utf8mb4_unicode_ci;
USE poker;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_detail
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_detail`  (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL,
  `start_timestamp` int(11) NOT NULL,
  `end_timestamp` int(11) NOT NULL,
  `refresh_timestamp` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL COMMENT '0永久，1一天，2七天，3三十天',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '活动内容',
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`activity_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '活动详情' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_fortunewheel
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_fortunewheel`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL,
  `account_id` int(11) NOT NULL,
  `option_1` tinyint(4) NOT NULL,
  `option_2` tinyint(4) NOT NULL,
  `bet` int(11) NOT NULL,
  `result_1` tinyint(4) NOT NULL,
  `result_2` tinyint(4) NOT NULL,
  `reward` int(11) NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_partake
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_partake`  (
  `partake_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  `partake_timestamp` int(11) NOT NULL,
  PRIMARY KEY (`partake_id`) USING BTREE,
  INDEX `account_id`(`account_id`, `activity_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '活动参与表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_redenvelop
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_redenvelop`  (
  `redenvelop_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `type` tinyint(2) NOT NULL DEFAULT 1,
  `account_id` int(11) NOT NULL COMMENT '赠送用户ID',
  `code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '红包码',
  `ticket_count` int(11) NOT NULL DEFAULT 0 COMMENT '房卡数量',
  `redenvelop_count` int(11) NOT NULL DEFAULT 1 COMMENT '红包数量',
  `content` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `is_receive` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否已被领取',
  `is_return` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否退回，0否1是',
  `journal_type` tinyint(2) NOT NULL DEFAULT 2,
  PRIMARY KEY (`redenvelop_id`) USING BTREE,
  INDEX `account_id`(`account_id`) USING BTREE,
  INDEX `code`(`code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6324 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '红包记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_redenvelop_receive
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_redenvelop_receive`  (
  `receive_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL,
  `redenvelop_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  `redenvelop_count` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`receive_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6204 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '红包收取记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_sign
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_sign`  (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL,
  `start_timestamp` int(11) NOT NULL,
  `end_timestamp` int(11) NOT NULL,
  `refresh_timestamp` int(11) NOT NULL,
  `day` tinyint(2) NOT NULL COMMENT '连续签到天数',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '活动内容',
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`activity_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '活动详情' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_sign_partake
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_sign_partake`  (
  `partake_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  `partake_timestamp` int(11) NOT NULL,
  PRIMARY KEY (`partake_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '活动参与表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_slotmachine
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_slotmachine`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL,
  `account_id` int(11) NOT NULL,
  `option_0` tinyint(4) NOT NULL DEFAULT 0,
  `option_1` tinyint(4) NOT NULL DEFAULT 0,
  `option_2` tinyint(4) NOT NULL DEFAULT 0,
  `option_3` tinyint(4) NOT NULL DEFAULT 0,
  `option_4` tinyint(4) NOT NULL DEFAULT 0,
  `option_5` tinyint(4) NOT NULL DEFAULT 0,
  `option_6` tinyint(4) NOT NULL DEFAULT 0,
  `option_7` tinyint(4) NOT NULL DEFAULT 0,
  `bet_count` int(11) NOT NULL,
  `result` tinyint(4) NOT NULL COMMENT '开奖结果 0-23',
  `reward` int(11) NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for activity_slotmachine_mode
-- ----------------------------
CREATE TABLE IF NOT EXISTS `activity_slotmachine_mode`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deposit` int(11) NOT NULL COMMENT '收入',
  `reward` int(11) NOT NULL COMMENT '回报',
  `remain` int(11) NOT NULL DEFAULT 0 COMMENT '系统剩余',
  `last_remain` int(11) NOT NULL DEFAULT 0 COMMENT '上次剩余',
  `mode` tinyint(4) NOT NULL DEFAULT 3 COMMENT '模式 1贪婪吸分 2蓄分  3吐分 4慷慨大送分',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for agent_bind
-- ----------------------------
CREATE TABLE IF NOT EXISTS `agent_bind`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '直营代理绑定列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bull_room
-- ----------------------------
CREATE TABLE IF NOT EXISTS `bull_room`  (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `data_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `room_number` int(11) NOT NULL DEFAULT -1,
  `account_id` int(11) NOT NULL,
  `is_close` tinyint(2) NOT NULL DEFAULT 0,
  `room_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '房间状态：1等待中 2游戏中 (暂未使用)',
  `game_type` tinyint(4) NOT NULL DEFAULT 5,
  `times_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '倍数规则：1，2，3',
  `club_id` int(11) NOT NULL DEFAULT 1 COMMENT '公会ID',
  `bean_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '准入规则 1:≥300豆 2:≥1000豆 3:2000豆 4:5000豆',
  PRIMARY KEY (`room_id`) USING BTREE,
  INDEX `data_key`(`data_key`) USING BTREE,
  INDEX `room_number`(`room_number`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 92289 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_applies
-- ----------------------------
CREATE TABLE IF NOT EXISTS `club_applies`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `player_id` int(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `player_code` int(11) NOT NULL DEFAULT 0 COMMENT '玩家编号',
  `player_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家昵称',
  `player_head` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家头像',
  `apply_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请状态 0待处理 1已同意 2已拒绝',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_id`) USING BTREE,
  INDEX `idx_club_player`(`club_id`, `player_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 443 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '申请记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_beans
-- ----------------------------
CREATE TABLE IF NOT EXISTS `club_beans`  (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '明细类型：1:购买(会长赠豆) 2:提现兑换 3:游戏赢 4:游戏输 5:游戏提成',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `player_id` int(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `player_code` int(11) NOT NULL DEFAULT 0 COMMENT '玩家编号',
  `player_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家昵称',
  `player_head` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家头像',
  `game_type` int(11) NOT NULL DEFAULT 0 COMMENT '游戏代号',
  `room_id` int(11) NOT NULL DEFAULT 0 COMMENT '房间号',
  `before_bean` int(11) NOT NULL DEFAULT 0 COMMENT '变动前豆数',
  `change_bean` int(11) NOT NULL DEFAULT 0 COMMENT '变动的豆数',
  `after_bean` int(11) NOT NULL DEFAULT 0 COMMENT '变动后豆数',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`) USING BTREE,
  INDEX `idx_club_player`(`club_id`, `player_id`) USING BTREE,
  INDEX `idex_game_room_player`(`game_type`, `room_id`, `player_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7028 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '欢乐豆明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_consume_sets
-- ----------------------------
CREATE TABLE IF NOT EXISTS `club_consume_sets`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL DEFAULT 0,
  `club_no` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `winner1` tinyint(4) NOT NULL DEFAULT 0 COMMENT '大赢家消耗百分比',
  `winner2` tinyint(4) NOT NULL DEFAULT 0 COMMENT '二赢家消耗百分比',
  `winner3` tinyint(4) NOT NULL DEFAULT 0 COMMENT '三赢家消耗百分比',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_id`) USING BTREE,
  INDEX `club_no`(`club_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '消耗设置' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_consumes
-- ----------------------------
CREATE TABLE IF NOT EXISTS `club_consumes`  (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `player_id` int(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `game_type` int(11) NOT NULL DEFAULT 0 COMMENT '游戏代号',
  `room_id` int(11) NOT NULL DEFAULT 0 COMMENT '房间号',
  `score` int(11) NOT NULL DEFAULT 0 COMMENT '赢豆数',
  `rate` tinyint(4) NOT NULL DEFAULT 0 COMMENT '消耗比例',
  `bean` int(11) NOT NULL DEFAULT 0 COMMENT '消耗豆数',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`) USING BTREE,
  INDEX `idx_game_room_player`(`game_type`, `room_id`, `player_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1529 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '消耗记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_game_beans
-- ----------------------------
CREATE TABLE IF NOT EXISTS `club_game_beans`  (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `game_type` int(11) NOT NULL DEFAULT 0 COMMENT '游戏代号',
  `room_id` int(11) NOT NULL DEFAULT 0 COMMENT '房间号',
  `game_num` tinyint(4) NOT NULL DEFAULT 0 COMMENT '当前局数',
  `player_id` int(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `bean` int(11) NOT NULL DEFAULT 0 COMMENT '输赢豆数',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`) USING BTREE,
  INDEX `idx_game_room_player`(`game_type`, `room_id`, `player_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '游戏记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_logs
-- ----------------------------
CREATE TABLE IF NOT EXISTS`club_logs`  (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '日志类型 1:创建公会 2:公会改名 3:加入公会 4:玩家退出 5:会长踢出',
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `player_id` int(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `player_code` int(11) NOT NULL DEFAULT 0 COMMENT '玩家编号',
  `player_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家昵称',
  `player_head` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家头像',
  `player_bean` int(11) NOT NULL DEFAULT 0 COMMENT '玩家余豆',
  `club_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '公会名称',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 554 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公会动态表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for club_players
-- ----------------------------
CREATE TABLE IF NOT EXISTS `club_players`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `player_id` int(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `player_code` int(11) NOT NULL DEFAULT 0 COMMENT '玩家编号',
  `player_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '玩家昵称',
  `player_head` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '玩家头像',
  `player_bean` int(11) NOT NULL DEFAULT 0 COMMENT '玩家余豆',
  `player_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '玩家状态 1:加入 2:主动退出 3:会长踢出',
  `is_last` tinyint(1) NOT NULL DEFAULT 0,
  `is_gaming` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_id`) USING BTREE,
  INDEX `idx_club_player`(`club_id`, `player_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 477 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公会成员表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for clubs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `clubs`  (
  `club_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_no` int(11) NOT NULL DEFAULT 0 COMMENT '公会编号',
  `club_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '公会名称',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '会长ID',
  `admin_code` int(11) NOT NULL DEFAULT 0 COMMENT '会长编号',
  `admin_nick` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '会长昵称',
  `admin_head` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '会长头像',
  `club_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '公会状态 1正常 0禁用',
  `create_card` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '创建公会消耗房卡',
  `max_player` smallint(5) UNSIGNED NOT NULL DEFAULT 200 COMMENT '公会人数上限',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`club_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公会表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dealer_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dealer_account`  (
  `dealer_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) DEFAULT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否删除，0否1是',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '游戏商名称',
  `account` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '游戏商cms登陆账号',
  `passwd` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'cms登陆密码',
  `clear_pwd` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'cms密码明文',
  `dealer_num` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '游戏商账号',
  `inventory_count` int(11) NOT NULL DEFAULT 0 COMMENT '库存房卡',
  PRIMARY KEY (`dealer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '经销商ID' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dealer_balance
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dealer_balance`  (
  `balance_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL COMMENT '是否删除，0否1是',
  `dealer_id` int(11) NOT NULL COMMENT '经销商ID',
  `total_fee` float(13, 2) DEFAULT 0.00,
  `trade_type` tinyint(2) NOT NULL COMMENT '支付方式，1银行转账，2微信，3支付宝，4现金',
  `trade_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易流水号',
  `payee` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收款人名称',
  `payee_account` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收款人账号',
  `paying_bank` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '收款人银行',
  PRIMARY KEY (`balance_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '经销商提成金额' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dealer_bind
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dealer_bind`  (
  `bind_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `dealer_screct` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`bind_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '代理商绑定' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dealer_commission
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dealer_commission`  (
  `commission_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL COMMENT '是否删除，0否1是',
  `dealer_id` int(11) NOT NULL COMMENT '经销商ID',
  `account_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `order_price` float(13, 2) NOT NULL DEFAULT 0.00 COMMENT '订单金额',
  `commission_price` float(13, 2) NOT NULL COMMENT '提成金额',
  `is_pay` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否支付，0否1是',
  PRIMARY KEY (`commission_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '经销商提成金额' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dealer_journal
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dealer_journal`  (
  `journal_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `dealer_id` int(11) DEFAULT NULL COMMENT '经销商ID',
  `object_id` int(11) NOT NULL COMMENT '产生对象ID',
  `object_type` tinyint(2) NOT NULL COMMENT '产生对象类型，1提成，2结算',
  `income` int(11) DEFAULT NULL COMMENT '入账',
  `disburse` int(11) DEFAULT NULL COMMENT '支出',
  `balance` int(11) DEFAULT 0 COMMENT '余额',
  `extra` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `abstract` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '流水摘要',
  PRIMARY KEY (`journal_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '经销商流水账' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dealer_recharge
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dealer_recharge`  (
  `recharge_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `journal_type` tinyint(2) NOT NULL DEFAULT 3,
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`recharge_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dist_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dist_account`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL,
  `account_id` int(11) NOT NULL,
  `dist_code` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `intro_aid_1` int(11) NOT NULL DEFAULT -1,
  `intro_aid_2` int(11) NOT NULL DEFAULT -1,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分销系统用户管理' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dist_commission
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dist_commission`  (
  `commission_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `type` tinyint(2) NOT NULL DEFAULT 1,
  `commission_1` int(11) NOT NULL DEFAULT 0,
  `commission_2` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`commission_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分销提成比例' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dist_commission_record
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dist_commission_record`  (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `object_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '对象类型，1购买房卡',
  `object_id` int(11) NOT NULL,
  `object_aid` int(11) NOT NULL,
  `ticket_count` int(11) NOT NULL DEFAULT 0 COMMENT '房卡数量',
  `commission_count` int(11) NOT NULL DEFAULT 0 COMMENT '提成数量',
  PRIMARY KEY (`record_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '提成记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for flower_room
-- ----------------------------
CREATE TABLE IF NOT EXISTS `flower_room`  (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `data_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `room_number` int(11) NOT NULL DEFAULT -1,
  `account_id` int(11) NOT NULL,
  `is_close` tinyint(2) NOT NULL DEFAULT 0,
  `room_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '房间状态：1等待中 2游戏中 (暂未使用)',
  `game_type` tinyint(4) DEFAULT 0 COMMENT '游戏类型',
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '公会ID',
  `bean_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '准入规则 1:≥300豆 2:≥1000豆 3:2000豆 4:5000豆',
  PRIMARY KEY (`room_id`) USING BTREE,
  INDEX `room_number`(`room_number`) USING BTREE,
  INDEX `account_id`(`account_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33545 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for game_announcement
-- ----------------------------
CREATE TABLE IF NOT EXISTS `game_announcement`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `game_type` int(11) NOT NULL,
  `announce_time` int(11) NOT NULL,
  `service_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `announce_text` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `service_text` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '游戏维护公告' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for game_broadcast
-- ----------------------------
CREATE TABLE IF NOT EXISTS `game_broadcast`  (
  `broadcast_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '类型',
  `state` tinyint(1) DEFAULT 1 COMMENT '状态（1、启动，2、关闭）',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '广播内容',
  `introl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '介绍',
  PRIMARY KEY (`broadcast_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for game_list
-- ----------------------------
CREATE TABLE IF NOT EXISTS `game_list`  (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `game_type` int(11) NOT NULL,
  `game_title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `domain_host` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `domain_port` int(11) NOT NULL DEFAULT -1,
  `ip_host` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `ip_port` int(11) NOT NULL DEFAULT -1,
  PRIMARY KEY (`game_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '游戏清单' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for game_whilelist
-- ----------------------------
CREATE TABLE IF NOT EXISTS `game_whilelist`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '白名单' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for gd_mahjong_room
-- ----------------------------
CREATE TABLE IF NOT EXISTS `gd_mahjong_room`  (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `data_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `room_number` int(11) NOT NULL DEFAULT -1,
  `account_id` int(11) NOT NULL,
  `is_close` tinyint(2) NOT NULL DEFAULT 0,
  `room_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '房间状态：0未开始 1游戏进行中',
  PRIMARY KEY (`room_id`) USING BTREE,
  INDEX `room_number`(`room_number`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for landlord_room
-- ----------------------------
CREATE TABLE IF NOT EXISTS `landlord_room`  (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `data_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `room_number` int(11) NOT NULL DEFAULT -1,
  `account_id` int(11) NOT NULL,
  `is_close` tinyint(2) NOT NULL DEFAULT 0,
  `room_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '房间状态：1等待中 2游戏中 (暂未使用)',
  PRIMARY KEY (`room_id`) USING BTREE,
  INDEX `room_number`(`room_number`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for manage_member
-- ----------------------------
CREATE TABLE IF NOT EXISTS `manage_member`  (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL COMMENT '管理员id',
  `user_code` int(11) DEFAULT -1 COMMENT '用户号',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0未处理  1已同意  2已拒绝 3已踢出',
  `is_delete` tinyint(2) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`member_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 62 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理成员' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for payment_detail_wxpay
-- ----------------------------
CREATE TABLE IF NOT EXISTS `payment_detail_wxpay`  (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(10) NOT NULL,
  `time_end` int(10) NOT NULL COMMENT '支付完成时间',
  `transaction_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信支付订单号',
  `out_trade_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商户订单号',
  `result_code` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '业务结果',
  `err_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '错误代码',
  `err_code_des` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '错误代码描述',
  `openid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户标识',
  `trade_type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易类型 JSAPI、NATIVE、APP',
  `bank_type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '付款银行',
  `total_fee` int(11) NOT NULL COMMENT '订单总金额，单位为分',
  `cash_fee` int(11) NOT NULL COMMENT '现金支付金额订单现金支付金额',
  `attach` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '商家数据包，原样返回',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '整个数据包',
  PRIMARY KEY (`detail_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for payment_goods
-- ----------------------------
CREATE TABLE IF NOT EXISTS `payment_goods`  (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `price` float(13, 2) NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `ticket_count` int(11) NOT NULL COMMENT '房票数量',
  PRIMARY KEY (`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品品种' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for payment_order
-- ----------------------------
CREATE TABLE IF NOT EXISTS `payment_order`  (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(10) NOT NULL,
  `update_appid` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `is_delete` int(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `order_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `original_price` float(13, 2) NOT NULL DEFAULT 0.00 COMMENT '原价',
  `total_price` float(11, 2) NOT NULL DEFAULT 0.00 COMMENT '订单总价',
  `status` int(2) NOT NULL COMMENT '订单状态，-2已取消，-1申请取消，1待支付，2已支付',
  `is_pay` int(2) NOT NULL DEFAULT 0 COMMENT '是否已支付',
  `timeout` int(10) NOT NULL,
  `is_audit` int(2) NOT NULL DEFAULT 0,
  `total_discount` float(13, 2) NOT NULL DEFAULT 0.00,
  `remark` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '备注',
  `pay_time` int(10) NOT NULL DEFAULT -1 COMMENT '支付时间',
  `payment_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '支付类型，1支付宝，2微信',
  `discount` float(13, 2) NOT NULL DEFAULT 0.00 COMMENT '折扣价',
  `journal_type` tinyint(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单详细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for payment_order_goods
-- ----------------------------
CREATE TABLE IF NOT EXISTS `payment_order_goods`  (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `price` float(13, 2) NOT NULL,
  `ticket_count` int(11) NOT NULL COMMENT '房票数量',
  `count` int(11) NOT NULL,
  PRIMARY KEY (`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品品种' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for phinxlog
-- ----------------------------
CREATE TABLE IF NOT EXISTS `phinxlog`  (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `start_time` timestamp(0),
  `end_time` timestamp(0),
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `room_account`  (
  `data_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_stat` tinyint(1) NOT NULL DEFAULT 0,
  `room_id` int(11) DEFAULT NULL COMMENT '房间id',
  `game_type` int(11) DEFAULT NULL COMMENT '游戏id',
  `account_id` int(11) DEFAULT NULL COMMENT '用户id',
  `score` int(11) DEFAULT 0,
  `over_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`data_id`) USING BTREE,
  INDEX `aid_gtype`(`account_id`, `game_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 225518 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_create_info
-- ----------------------------
DROP TABLE IF EXISTS `room_create_info`;
CREATE TABLE IF NOT EXISTS `room_create_info`  (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `create_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`info_id`) USING BTREE,
  INDEX `account_id`(`account_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 197 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户开房设置表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_game_result
-- ----------------------------
CREATE TABLE IF NOT EXISTS `room_game_result`  (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `game_type` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `game_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`result_id`) USING BTREE,
  INDEX `game_type`(`game_type`, `room_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `room_id`(`room_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1727442 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间游戏结果' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_game_score
-- ----------------------------
CREATE TABLE IF NOT EXISTS `room_game_score`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `game_time` int(11) NOT NULL,
  `game_type` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_score` int(11) NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE,
  INDEX `game_type`(`game_type`, `account_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户游戏积分表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_scoreboard
-- ----------------------------
CREATE TABLE IF NOT EXISTS `room_scoreboard`  (
  `board_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_stat` tinyint(1) DEFAULT 0 COMMENT '是否统计',
  `game_type` tinyint(4) NOT NULL COMMENT '游戏类型 ：1炸金花  2斗地主  3梭哈  4德州  5斗牛 6广东麻将  ',
  `room_id` int(11) NOT NULL,
  `round` int(11) NOT NULL DEFAULT -1 COMMENT '该房间的第几轮',
  `game_num` int(11) NOT NULL DEFAULT -1 COMMENT '补充：玩了多少局',
  `board` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '积分榜json字符串',
  `create_time` int(10) NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `start_time` int(11) NOT NULL DEFAULT -1,
  `balance_board` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rule_text` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`board_id`) USING BTREE,
  INDEX `game_type`(`game_type`) USING BTREE,
  INDEX `room_id`(`room_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 111274 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_ticket
-- ----------------------------
DROP TABLE IF EXISTS `room_ticket`;
CREATE TABLE IF NOT EXISTS `room_ticket`  (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `ticket_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ticket_id`) USING BTREE,
  INDEX `account_id`(`account_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26025 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房卡表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_ticket_adjustment
-- ----------------------------
CREATE TABLE IF NOT EXISTS `room_ticket_adjustment`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `origin_count` int(11) NOT NULL,
  `adjust_count` int(11) NOT NULL,
  `balance_count` int(11) NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房卡调整记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_ticket_journal
-- ----------------------------
CREATE TABLE IF NOT EXISTS `room_ticket_journal`  (
  `journal_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL COMMENT '用户ID',
  `object_id` int(11) NOT NULL COMMENT '产生对象ID',
  `object_type` tinyint(2) NOT NULL COMMENT '产生对象类型，1，新用户，2充值，3游戏，，4代理商码兑换，5签到，6红包，7转盘，8老虎机，',
  `income` int(11) DEFAULT NULL COMMENT '入账',
  `disburse` int(11) DEFAULT NULL COMMENT '支出',
  `balance` int(11) DEFAULT 0 COMMENT '余额',
  `extra` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `abstract` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '流水摘要',
  `journal_type` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`journal_id`) USING BTREE,
  INDEX `account_id`(`account_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 151614 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '经销商流水账' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sangong_room
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sangong_room`  (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `data_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `room_number` int(11) NOT NULL DEFAULT -1,
  `account_id` int(11) NOT NULL,
  `is_close` tinyint(2) NOT NULL DEFAULT 0,
  `room_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '房间状态：1等待中 2游戏中 (暂未使用)',
  `game_type` tinyint(4) NOT NULL DEFAULT 5,
  PRIMARY KEY (`room_id`) USING BTREE,
  INDEX `data_key`(`data_key`) USING BTREE,
  INDEX `room_number`(`room_number`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 89392 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '三公房间表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for score_logs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `score_logs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '群主ID',
  `player_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '成员ID',
  `log_type` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分变化类型：1-群主增|减 2-开房扣|退 3-本人转移',
  `game_type` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏类型',
  `room_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '房间号',
  `number` int(11) NOT NULL DEFAULT 0 COMMENT '增减积分',
  `score` int(11) NOT NULL DEFAULT 0 COMMENT '变化后积分',
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `account_id`(`account_id`, `player_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for server_parameter
-- ----------------------------
CREATE TABLE IF NOT EXISTS `server_parameter`  (
  `sp_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表ID',
  `key` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'key值',
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'value值',
  `is_delete` int(4) DEFAULT 0 COMMENT '是否生效',
  `explain` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '参数说明',
  PRIMARY KEY (`sp_id`) USING BTREE,
  INDEX `key`(`key`(255)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '服务器参数表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sms_detail
-- ----------------------------
DROP TABLE IF EXISTS `sms_detail`;
CREATE TABLE IF NOT EXISTS `sms_detail`  (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` char(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `identifying_code` int(6) NOT NULL COMMENT '验证码',
  `type` int(2) NOT NULL COMMENT '类型，0注册，1找回密码，2绑定',
  `create_time` int(10) NOT NULL,
  `invaild_time` int(10) NOT NULL,
  `is_delete` int(2) NOT NULL DEFAULT 0,
  `session` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '操作session',
  `extra` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '额外信息',
  PRIMARY KEY (`sms_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 445 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '短信表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for summary_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `summary_account`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `game_type` int(11) NOT NULL,
  `total_score` int(11) NOT NULL DEFAULT 0 COMMENT '游戏总积分',
  `total_count` int(11) NOT NULL DEFAULT 0 COMMENT '总游戏局数',
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 446 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '概况，用户数据' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for summary_account_daily
-- ----------------------------
CREATE TABLE IF NOT EXISTS `summary_account_daily`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `day_timestamp` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `game_type` int(11) NOT NULL,
  `total_score` int(11) NOT NULL DEFAULT 0 COMMENT '游戏总积分',
  `total_count` int(11) NOT NULL DEFAULT 0 COMMENT '总游戏局数',
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '概况，用户数据' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for summary_dealer
-- ----------------------------
DROP TABLE IF EXISTS `summary_dealer`;
CREATE TABLE IF NOT EXISTS `summary_dealer`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `day_timestamp` int(11) NOT NULL,
  `game_type` int(11) NOT NULL,
  `total_count` int(11) NOT NULL DEFAULT 0 COMMENT '游戏局数',
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 417 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '概况，用户数据' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wechat_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `wechat_account`  (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `union_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `open_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'openID',
  `nickname` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '昵称',
  `user_code` int(11) NOT NULL DEFAULT -1 COMMENT '用户编号',
  `recommend_code` int(11) NOT NULL DEFAULT -1 COMMENT '推荐人用户编号',
  `headimgurl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '头像URL',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `secret` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `create_time` int(10) NOT NULL,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) NOT NULL,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `is_refresh` tinyint(2) NOT NULL DEFAULT 0,
  `is_manage_on` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否开启群主功能（0，不开启、1，开启）',
  PRIMARY KEY (`account_id`) USING BTREE,
  INDEX `open_id`(`open_id`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26026 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信账号' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wx_account
-- ----------------------------
CREATE TABLE IF NOT EXISTS `wx_account`  (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'openID',
  `union_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'unionid',
  `nickname` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `user_code` int(11) NOT NULL DEFAULT -1 COMMENT '用户编号',
  `recommend_code` int(11) NOT NULL DEFAULT -1 COMMENT '推荐人用户编号',
  `headimgurl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像URL',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `secret` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密钥',
  `create_time` int(10) NOT NULL DEFAULT 0,
  `create_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `update_time` int(10) NOT NULL DEFAULT 0,
  `update_appid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `is_refresh` tinyint(2) NOT NULL DEFAULT 0,
  `is_manage_on` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启群主功能（0，不开启、1，开启）',
  PRIMARY KEY (`account_id`) USING BTREE,
  INDEX `open_id`(`open_id`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `user_code`(`user_code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信账号' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wxauth_parameter
-- ----------------------------
CREATE TABLE IF NOT EXISTS `wxauth_parameter`  (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_delete` tinyint(2) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `wx_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `wx_value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`data_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信设置参数管理表' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
insert ignore into dealer_account(account, passwd, dealer_num) values('admin_test', 'admin@poker@2018', 11);
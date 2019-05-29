ALTER TABLE `wechat_account`
ADD `union_id` varchar(64) COLLATE 'utf8_general_ci' NULL COMMENT 'unionid' AFTER `open_id`;

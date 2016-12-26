ALTER TABLE `mtb_token`
ADD COLUMN `type`  tinyint(4) NULL DEFAULT 0 COMMENT '1:ios. 2: android' AFTER `uuid`;
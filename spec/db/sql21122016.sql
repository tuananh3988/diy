ALTER TABLE `mtb_user`
ADD COLUMN `type`  tinyint(4) NULL DEFAULT 1 COMMENT '1: ios. 2: aos' AFTER `id`;
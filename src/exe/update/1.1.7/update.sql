
ALTER TABLE `aiwriter_material`
ADD `remark_1` VARCHAR(900) NOT NULL DEFAULT '' COMMENT '备注1' AFTER `description`,
ADD `remark_2` VARCHAR(900) NOT NULL DEFAULT '' COMMENT '备注2' AFTER `remark_1`,
ADD `remark_3` VARCHAR(900) NOT NULL DEFAULT '' COMMENT '备注3' AFTER `remark_2`,
ADD `remark_4` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注1' AFTER `remark_3`,
ADD `remark_5` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注2' AFTER `remark_4`,
ADD `remark_6` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注3' AFTER `remark_5`;

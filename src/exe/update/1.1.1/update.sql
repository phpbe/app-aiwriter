
UPDATE `aiwriter_process_template` SET `role`='user';

ALTER TABLE `aiwriter_process_template` ADD `role` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '角色' AFTER `type`;

ALTER TABLE `be_app_aiwriter`.`aiwriter_process_template` DROP INDEX `type`, ADD INDEX `type_role` (`type`, `role`) USING BTREE;

INSERT INTO `aiwriter_process_template` (`id`, `type`, `role`, `content`, `ordering`, `create_time`, `update_time`) VALUES
((SELECT UUID()), 'title', 'system', '假定你在一家Web软件公司工作，公司主要业务为：网站设计、开发、建设，现在需要生成一些内帮助宣传。处理结果要直接展示给用户，要让它看起来是人工写的。', 100,  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
((SELECT UUID()), 'summary', 'system', '假定你在一家Web软件公司工作，公司主要业务为：网站设计、开发、建设，现在需要生成一些内帮助宣传。处理结果要直接展示给用户，要让它看起来是人工写的。', 100, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
((SELECT UUID()), 'description', 'system', '假定你在一家Web软件公司工作，公司主要业务为：网站设计、开发、建设，现在需要生成一些内帮助宣传。处理结果要直接展示给用户，要让它看起来是人工写的。', 100, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),

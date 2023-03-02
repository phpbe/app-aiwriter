
CREATE TABLE `aiwriter_material` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`category_id` varchar(36) NOT NULL DEFAULT '' COMMENT '分类ID',
`unique_key` varchar(200) NOT NULL COMMENT '唯一键',
`title` varchar(120) NOT NULL DEFAULT '' COMMENT '标题',
`summary` varchar(500) NOT NULL DEFAULT '' COMMENT '摘要',
`description` mediumtext NOT NULL COMMENT '描述',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='素材';

ALTER TABLE `aiwriter_material`
ADD PRIMARY KEY (`id`),
ADD KEY `category_id` (`category_id`, `unique_key`);


CREATE TABLE `aiwriter_material_category` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(120) NOT NULL DEFAULT '' COMMENT '名称',
`ordering` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='分类';

ALTER TABLE `aiwriter_material_category`
ADD PRIMARY KEY (`id`);


CREATE TABLE `aiwriter_process` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(120) NOT NULL DEFAULT '' COMMENT '名称',
`material_category_id` varchar(36) NOT NULL DEFAULT '' COMMENT '素材分类ID',
`details` mediumtext NOT NULL COMMENT  '处理逻辑细节',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='加工任务';

ALTER TABLE `aiwriter_process`
ADD PRIMARY KEY (`id`);


CREATE TABLE `aiwriter_process_content` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`process_id` varchar(36) NOT NULL DEFAULT '' COMMENT '加工任务ID',
`material_id` varchar(36) NOT NULL DEFAULT '' COMMENT '素材ID',
`title` varchar(120) NOT NULL DEFAULT '' COMMENT '标题',
`summary` varchar(500) NOT NULL DEFAULT '' COMMENT '摘要',
`description` mediumtext NOT NULL COMMENT '描述',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='加工结果';

ALTER TABLE `aiwriter_process_content`
ADD PRIMARY KEY (`id`),
ADD KEY `process_id` (`process_id`, `material_id`),
ADD KEY `material_id` (`material_id`);



CREATE TABLE `aiwriter_process_template` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型',
`content` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
`ordering` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='加工';

ALTER TABLE `aiwriter_process_template`
ADD PRIMARY KEY (`id`),
ADD KEY `type` (`type`);

INSERT INTO `aiwriter_process_template` (`id`, `type`, `content`, `ordering`, `create_time`, `update_time`) VALUES
((SELECT UUID()), 'title', '改写以下内容，让它更符合SEO，更吸引用户，长度不超过30个字：\n{素材标题}', 100,  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
((SELECT UUID()), 'summary', '改写以下内容，让它更符合SEO，更吸引用户：\n{素材标题}', 100, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
((SELECT UUID()), 'description', '根据以下内容，生成一篇文章，内容尽可能多：\n{素材标题}', 100, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);




CREATE TABLE `aiwriter_publish` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`name` varchar(120) NOT NULL DEFAULT '' COMMENT '名称',
`process_id` varchar(36) NOT NULL DEFAULT '' COMMENT '加工任务ID',
`post_url` varchar(300) NOT NULL DEFAULT '' COMMENT  '发布网址',
`post_headers` text NOT NULL COMMENT  '请求头',
`post_format` varchar(30) NOT NULL DEFAULT 'form' COMMENT  '请求格式（form/json）',
`post_data_type` varchar(30) NOT NULL DEFAULT 'mapping' COMMENT  '数据处理方法（mapping/code）',
`post_data_mapping` text NOT NULL COMMENT  '数据处理-映射',
`post_data_code` text NOT NULL COMMENT  '数据处理-代码',
`interval` int(11) NOT NULL DEFAULT '1000' COMMENT '间隔时间（毫秒）',
`is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='发布';

ALTER TABLE `aiwriter_publish`
ADD PRIMARY KEY (`id`);



CREATE TABLE `aiwriter_publish_content` (
`id` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`publish_id` varchar(36) NOT NULL DEFAULT '' COMMENT '发布任务ID',
`process_content_id` varchar(36) NOT NULL DEFAULT '' COMMENT '加工结果ID',
`publish_url` varchar(300) NOT NULL DEFAULT '' COMMENT '发布到的网址',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='加工';

ALTER TABLE `aiwriter_publish_content`
ADD PRIMARY KEY (`id`),
ADD KEY `publish_id` (`publish_id`, `process_content_id`),
ADD KEY `process_content_id` (`process_content_id`);



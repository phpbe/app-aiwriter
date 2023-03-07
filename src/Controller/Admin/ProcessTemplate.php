<?php

namespace Be\App\AiWriter\Controller\Admin;


use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 存储管理器
 *
 * @BeMenuGroup("加工")
 * @BePermissionGroup("加工")
 */
class ProcessTemplate extends Auth
{

    /**
     * @BeMenu("AI处理模板", icon = "bi-card-text", ordering="2.30")
     * @BePermission("AI处理模板", ordering="2.30")
     */
    public function index()
    {
        $typeKeyValues = [
            'title' => '标题',
            'summary' => '摘要',
            'description' => '描述',
        ];

        $roleKeyValues = [
            'system' => '系统',
            'user' => '用户',
        ];

        Be::getAdminPlugin('Curd')->setting([
            'label' => 'AI处理模板',
            'table' => 'aiwriter_process_template',
            'grid' => [
                'title' => 'AI处理模板',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'content',
                            'label' => '内容',
                        ],
                        [
                            'name' => 'type',
                            'label' => '类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], $typeKeyValues),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                        ],
                        [
                            'name' => 'role',
                            'label' => '角色',
                            'driver' => FormItemSelect::class,
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], $roleKeyValues),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                        ],
                    ],
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ],
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量删除',
                            'task' => 'delete',
                            'target' => 'ajax',
                            'confirm' => '此操作将从数据库彻底删除，确认要执行么？',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ],
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'type',
                            'label' => '类型',
                            'keyValues' => $typeKeyValues,
                            'width' => '80',
                        ],
                        [
                            'name' => 'role',
                            'label' => '角色',
                            'keyValues' => $roleKeyValues,
                            'width' => '80',
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                            'width' => '120',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '编辑',
                                'ui' => [
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-edit',
                                'task' => 'edit',
                                'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                                'drawer' => [
                                    'width' => '80%'
                                ],
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-delete',
                                'confirm' => '确认要删除么？',
                                'task' => 'delete',
                                'target' => 'ajax',
                            ],
                        ]
                    ],
                ],
            ],

            'create' => [
                'title' => '新建素材',
                'form' => [
                    'items' => [
                        [
                            'name' => 'type',
                            'label' => '类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $typeKeyValues,
                            'defaultValue' => 'title',
                            'required' => true,
                        ],
                        [
                            'name' => 'role',
                            'label' => '角色',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $roleKeyValues,
                            'defaultValue' => 'system',
                            'required' => true,
                        ],
                        [
                            'name' => 'content',
                            'driver' => FormItemInputTextArea::class,
                            'label' => '内容',
                            'required' => true,
                            'ui' => [
                                'maxlength' => 500,
                                'show-word-limit' => true,
                            ],
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                            'driver' => FormItemInputNumberInt::class,
                            'defaultValue' => 100,
                        ],
                    ]
                ],
            ],

            'edit' => [
                'title' => '编辑管理员',
                'form' => [
                    'items' => [
                        [
                            'name' => 'type',
                            'label' => '类型',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $typeKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'role',
                            'label' => '角色',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $roleKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'content',
                            'driver' => FormItemInputTextArea::class,
                            'label' => '内容',
                            'required' => true,
                            'ui' => [
                                'maxlength' => 500,
                                'show-word-limit' => true,
                            ],
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                            'driver' => FormItemInputNumberInt::class,
                        ],
                    ]
                ],
            ],

            'detail' => [
                'title' => '文章详情',
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'type',
                            'label' => '类型',
                            'keyValues' => $typeKeyValues,
                        ],
                        [
                            'name' => 'role',
                            'label' => '色色',
                            'keyValues' => $roleKeyValues,
                        ],
                        [
                            'name' => 'content',
                            'label' => '类型',
                            'driver' => DetailItemHtml::class,
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                        ],
                    ]
                ],
            ],

        ])->execute();
    }


}

<?php

namespace Be\App\AiWriter\Controller\Admin;


use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemTinymce;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemLink;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 加工结果
 *
 * @BeMenuGroup("加工")
 * @BePermissionGroup("加工")
 */
class ProcessContent extends Auth
{

    /**
     * @BeMenu("加工结果", icon = "bi-journals", ordering="2.20")
     * @BePermission("加工结果", ordering="2.20")
     */
    public function index()
    {
        $processKeyValues = Be::getService('App.AiWriter.Admin.Process')->getProcessKeyValues();
        $processId = Be::getRequest()->get('process_id', 'all');
        Be::getAdminPlugin('Curd')->setting([
            'label' => '加工结果',
            'table' => 'aiwriter_process_content',
            'grid' => [
                'title' => '加工结果',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'title',
                            'label' => '标题',
                        ],
                        [
                            'name' => 'process_id',
                            'label' => '加工任务',
                            'driver' => FormItemSelect::class,
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], $processKeyValues),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                            'value' => $processId,
                        ],
                        [
                            'name' => 'is_published',
                            'label' => '是否发布',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                'all' => '全部',
                                'n' => '未发布',
                                'y' => '已发布',
                            ],
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                            'buildSql' => function ($dbName, $formData) {
                                if (isset($formData['is_published'])) {
                                    if ($formData['is_published'] === 'n') {
                                        return 'id NOT IN (SELECT process_content_id FROM aiwriter_publish_content)';
                                    } elseif ($formData['is_published'] === 'y') {
                                        return 'id IN (SELECT process_content_id FROM aiwriter_publish_content)';
                                    }
                                }
                                return '';
                            },
                        ],
                    ],
                ],

                'titleToolbar' => [
                    'items' => [
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemDropDown::class,
                            'ui' => [
                                'icon' => 'bi-download',
                            ],
                            'menus' => [
                                [
                                    'label' => 'CSV',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'excel',
                                    ],
                                    'target' => 'blank',
                                ],
                            ]
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
                            'name' => 'title',
                            'label' => '标题',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                        [
                            'name' => 'process_id',
                            'label' => '加工任务',
                            'keyValues' => $processKeyValues,
                            'width' => '240',
                        ],
                        [
                            'name' => 'publish_count',
                            'label' => '发布',
                            'value' => function($row) {
                                $sql = 'SELECT COUNT(*) FROM aiwriter_publish_content WHERE process_content_id=?';
                                return Be::getDb()->getValue($sql, [$row['id']]);
                            },
                            'width' => '120',
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

            'edit' => [
                'title' => '编辑管理员',
                'form' => [
                    'items' => [
                        [
                            'name' => 'title',
                            'label' => '标题',
                            'ui' => [
                                'maxlength' => 120,
                                'show-word-limit' => true,
                            ],
                            'required' => true,
                        ],
                        [
                            'name' => 'summary',
                            'driver' => FormItemInputTextArea::class,
                            'label' => '摘要',
                            'ui' => [
                                'maxlength' => 500,
                                'show-word-limit' => true,
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => '描述',
                            'driver' => FormItemTinymce::class,
                            'option' => [
                                'toolbar_sticky_offset' => 0
                            ],
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
                            'name' => 'process_id',
                            'label' => '加工任务',
                            'keyValues' => $processKeyValues,
                        ],
                        [
                            'name' => 'title',
                            'label' => '标题',
                        ],
                        [
                            'name' => 'summary',
                            'label' => '摘要',
                        ],
                        [
                            'name' => 'description',
                            'label' => '描述',
                            'driver' => DetailItemHtml::class,
                        ],
                        [
                            'name' => 'publish_count',
                            'label' => '发布',
                            'value' => function($row) {
                                $sql = 'SELECT COUNT(*) FROM aiwriter_publish_content WHERE process_content_id=?';
                                return Be::getDb()->getValue($sql, [$row['id']]);
                            },
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

            'export' => [
                'items' => [
                    [
                        'name' => 'id',
                        'label' => 'ID',
                    ],
                    [
                        'name' => 'process_id',
                        'label' => '加工任务',
                        'keyValues' => $processKeyValues,
                    ],
                    [
                        'name' => 'title',
                        'label' => '标题',
                    ],
                    [
                        'name' => 'summary',
                        'label' => '摘要',
                    ],
                    [
                        'name' => 'description',
                        'label' => '描述',
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

        ])->execute();
    }


}

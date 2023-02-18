<?php

namespace Be\App\AiWriter\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemTinymce;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemLink;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 存储管理器
 *
 * @BeMenuGroup("素材", icon = "bi-journal-text", ordering="1")
 * @BePermissionGroup("素材'")
 */
class Material extends Auth
{

    /**
     * @BeMenu("素材管理", icon = "bi-journals", ordering="1.1")
     * @BePermission("素材管理", ordering="1.1")
     */
    public function index()
    {

        $categoryKeyValues = Be::getService('App.AiWriter.Admin.MaterialCategory')->getCategoryKeyValues();

        Be::getAdminPlugin('Curd')->setting([
            'label' => '素材',
            'table' => 'aiwriter_material',
            'grid' => [
                'title' => '素材管理',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',
                'form' => [
                    'items' => [
                        [
                            'name' => 'title',
                            'label' => '标题',
                        ],
                        [
                            'name' => 'category_id',
                            'label' => '分类',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $categoryKeyValues,
                        ],
                    ],
                ],

                'titleToolbar' => [
                    'items' => [
                        [
                            'label' => '导入',
                            'driver' => ToolbarItemLink::class,
                            'ui' => [
                                'icon' => 'bi-upload',
                            ],
                            'task' => 'import',
                        ],
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

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建素材',
                             'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ],
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                    ],
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量删除',
                            'action' => 'delete',
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
                                'task' => 'fieldEdit',
                                'target' => 'ajax',
                                'postData' => [
                                    'field' => 'is_delete',
                                    'value' => 1,
                                ],
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
                            'name' => 'category_id',
                            'label' => '分类',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $categoryKeyValues,
                        ],
                        [
                            'name' => 'title',
                            'label' => '标题',
                            'driver' => FormItemInput::class,
                            'ui' => [
                                'maxlength' => 120,
                                'show-word-limit' => true,
                            ],
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

            'edit' => [
                'title' => '编辑管理员',
                'form' => [
                    'items' => [
                        [
                            'name' => 'category_id',
                            'label' => '分类',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $categoryKeyValues,
                        ],
                        [
                            'name' => 'title',
                            'label' => '标题',
                            'ui' => [
                                'maxlength' => 120,
                                'show-word-limit' => true,
                            ],
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
                            'name' => 'category',
                            'label' => '分类',
                            'value' => function ($row) use($categoryKeyValues) {
                                if ($row['category_id'] === '') {
                                    return '未分类';
                                }

                                return $categoryKeyValues[$row['category_id']] ?? '';
                            }
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

            'import' => [
                'mapping' => [
                    'items' => [
                        [
                            'name' => 'category_id',
                            'label' => '分类',
                            'value' => function($row) use($categoryKeyValues) {
                                return $categoryKeyValues[$row['category_id']] ?? '';
                            }
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
                    ],
                ]
            ],

            'export' => [
                'items' => [
                    [
                        'name' => 'id',
                        'label' => 'ID',
                    ],
                    [
                        'name' => 'category',
                        'label' => '分类',
                        'value' => function ($row) use($categoryKeyValues) {
                            if ($row['category_id'] === '') {
                                return '未分类';
                            }

                            return $categoryKeyValues[$row['category_id']] ?? '';
                        }
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

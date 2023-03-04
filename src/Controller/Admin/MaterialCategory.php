<?php

namespace Be\App\AiWriter\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemInputNumber;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("素材")
 * @BePermissionGroup("素材")
 */
class MaterialCategory extends Auth
{

    /**
     * 分类
     *
     * @BeMenu("分类", icon="bi-journal-bookmark", ordering="1.2")
     * @BePermission("分类", ordering="1.2")
     */
    public function categories()
    {
        Be::getAdminPlugin('Curd')->setting([

            'label' => '分类',
            'table' => 'aiwriter_material_category',

            'grid' => [
                'title' => '分类',
                'orderBy' => 'ordering',
                'orderByDir' => 'ASC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                    ],
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建分类',
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ],
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量删除',
                            'task' => 'delete',
                            'target' => 'ajax',
                            'confirm' => '确认要删除吗？',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ]
                        ],
                    ]
                ],


                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                        ],
                        [
                            'name' => 'material_count',
                            'label' => '素材数量',
                            'align' => 'center',
                            'width' => '120',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $sql = 'SELECT COUNT(*) FROM aiwriter_material WHERE category_id = ?';
                                $count = Be::getDb()->getValue($sql, [$row['id']]);
                                return $count;
                            },
                            'action' => 'goMaterials',
                            'target' => 'self',
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
                        'width' => '180',
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
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'task' => 'delete',
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-delete',
                            ],
                        ]
                    ],
                ],
            ],

            'create' => [
                'title' => '新建分类',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'ui' => [
                                'maxlength' => 120,
                                'show-word-limit' => true,
                            ],
                            'required' => true,
                            'unique' => true,
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                            'driver' => FormItemInputNumber::class,
                        ],
                    ]
                ],
            ],

            'edit' => [
                'title' => '编辑分类',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'ui' => [
                                'maxlength' => 120,
                                'show-word-limit' => true,
                            ],
                            'required' => true,
                            'unique' => true,
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                            'driver' => FormItemInputNumber::class,
                        ],
                    ]
                ],
            ],

            'detail' => [
                'title' => '分类详情',
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
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

            'delete' => [
                'events' => [
                    'before' => function ($tuple) {
                        Be::getTable('aiwriter_material')
                            ->where('category_id', '=', $tuple->id)
                            ->update([
                                'category_id' => '',
                                'update_time' => date('Y-m-d H:i:s')
                            ]);
                    },
                ],
            ]
        ])->execute();
    }

    /**
     * 指定分类下的分类素材管理
     *
     * @BePermission("*")
     */
    public function goMaterials()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('AiWriter.Material.index', ['category_id' => $postData['row']['id']]));
            }
        }
    }

}

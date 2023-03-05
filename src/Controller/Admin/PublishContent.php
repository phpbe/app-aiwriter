<?php

namespace Be\App\AiWriter\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemCode;
use Be\AdminPlugin\Detail\Item\DetailItemToggleIcon;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemToggleIcon;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 发布记录
 *
 * @BeMenuGroup("发布")
 * @BePermissionGroup("发布")
 */
class PublishContent extends Auth
{

    /**
     * @BeMenu("发布记录", icon = "bi-journals", ordering="3.20")
     * @BePermission("发布记录", ordering="3.20")
     */
    public function index()
    {
        $publishKeyValues = Be::getService('App.AiWriter.Admin.Publish')->getPublishKeyValues();
        $publishId = Be::getRequest()->get('publish_id', 'all');
        Be::getAdminPlugin('Curd')->setting([
            'label' => '发布记录',
            'table' => 'aiwriter_publish_content',
            'grid' => [
                'title' => '发布记录',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'title',
                            'label' => '标题',
                        ],
                        [
                            'name' => 'publish_id',
                            'label' => '发布任务',
                            'driver' => FormItemSelect::class,
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], $publishKeyValues),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                            'value' => $publishId,
                        ],
                        [
                            'name' => 'is_success',
                            'label' => '是否成功',
                            'driver' => FormItemSelect::class,
                            'keyValues' => \Be\Util\Arr::merge([
                                'all' => '全部',
                            ], [
                                '0' => '夫败',
                                '1' => '成功',
                            ]),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                            'value' => $publishId,
                        ],
                    ],
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
                            'value' => function($row) {
                                $sql = 'SELECT title FROM aiwriter_process_content WHERE id=?';
                                $title = Be::getDb()->getValue($sql, [$row['process_content_id']]);
                                if ($title) {
                                    return $title;
                                }
                                return '';
                            },
                            'align' => 'left',
                            'driver' => TableItemLink::class,
                            'task' => 'detail',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                        [
                            'name' => 'publish_id',
                            'label' => '发布任务',
                            'keyValues' => $publishKeyValues,
                            'width' => '240',
                        ],
                        [
                            'name' => 'is_success',
                            'label' => '是否成功',
                            'driver' => TableItemToggleIcon::class,
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


            'detail' => [
                'title' => '发布记录详情',
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'publish_id',
                            'label' => '发布任务',
                            'keyValues' => $publishKeyValues,
                        ],
                        [
                            'name' => 'title',
                            'label' => '标题',
                            'value' => function($row) {
                                $sql = 'SELECT title FROM aiwriter_process_content WHERE id=?';
                                $title = Be::getDb()->getValue($sql, [$row['process_content_id']]);
                                if ($title) {
                                    return $title;
                                }
                                return '';
                            },
                        ],
                        [
                            'name' => 'is_success',
                            'label' => '是否成功',
                            'driver' => DetailItemToggleIcon::class,
                        ],
                        [
                            'name' => 'response',
                            'label' => '响应',
                            'driver' => DetailItemCode::class,
                            'language' => 'auto',
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

<?php

namespace Be\App\AiWriter\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemProgress;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 发布
 *
 * @BeMenuGroup("发布", icon = "bi-cloud-arrow-up", ordering="3")
 * @BePermissionGroup("发布")
 */
class Publish extends Auth
{

    /**
     * @BeMenu("发布任务", icon = "bi-list-check", ordering="3.10")
     * @BePermission("发布任务", ordering="3.10")
     */
    public function index()
    {
        Be::getAdminPlugin('Curd')->setting([
            'label' => '发布任务',
            'table' => 'aiwriter_publish',
            'grid' => [
                'title' => '发布任务',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '是否启用',
                            'driver' => FormItemSelect::class,
                            'value' => Be::getRequest()->request('is_enable', 'all'),
                            'nullValue' => 'all',
                            'defaultValue' => 'all',
                            'counter' => true,
                            'keyValues' => [
                                'all' => '全部',
                                '1' => '启用',
                                '0' => '停用',
                            ],
                        ]
                    ],
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建发布任务',
                             'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ],
                            'action' => 'create',
                            'target' => 'self', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
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
                            'name' => 'name',
                            'label' => '名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                        [
                            'name' => 'process_content_count',
                            'label' => '文章数',
                            'align' => 'center',
                            'width' => '90',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $db = Be::getDb();
                                $sql = 'SELECT COUNT(*) FROM aiwriter_process_content WHERE process_id = ?';
                                $count = $db->getValue($sql, [$row['process_id']]);
                                return $count;
                            },
                            'action' => 'goProcessContents',
                            'target' => 'self',
                        ],
                        [
                            'name' => 'publish_count',
                            'label' => '已发布',
                            'align' => 'center',
                            'width' => '90',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $sql = 'SELECT COUNT(*) FROM aiwriter_publish_content WHERE publish_id = ?';
                                $count = Be::getDb()->getValue($sql, [$row['id']]);
                                return $count;
                            },
                            'action' => 'goPublishContents',
                            'target' => 'self',
                        ],
                        [
                            'name' => 'publish_percent',
                            'label' => '进度',
                            'align' => 'center',
                            'width' => '120',
                            'driver' => TableItemProgress::class,
                            'value' => function ($row) {
                                if ($row['process_content_count'] > 0) {
                                    return round($row['publish_count'] * 100 / $row['process_content_count'], 1);
                                } else {
                                    return 100;
                                }
                            },
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '是否启用',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'fieldEdit',
                            'width' => '80',
                            'exportValue' => function ($row) {
                                return $row['is_enable'] ? '是' : '否';
                            },
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
                                'action' => 'edit',
                                'target' => 'self', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
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
                                'action' => 'delete',
                                'target' => 'ajax',
                            ],
                        ]
                    ],
                ],
            ],

        ])->execute();
    }
    
    /**
     * 新建发布任务
     *
     * @BePermission("新建", ordering="3.11")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                $publish = Be::getService('App.AiWriter.Admin.Publish')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建发布任务成功！');
                $response->set('publish', $publish);
                $response->set('redirectUrl', beAdminUrl('AiWriter.Publish.index'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('publish', false);

            $processKeyValues = Be::getService('App.AiWriter.Admin.Process')->getProcessKeyValues();
            $response->set('processKeyValues', $processKeyValues);

            $response->set('backUrl', beAdminUrl('AiWriter.Publish.index'));
            $response->set('formActionUrl', beAdminUrl('AiWriter.Publish.create'));

            $response->set('title', '新建发布任务');

            $response->display('App.AiWriter.Admin.Publish.edit');
        }
    }

    /**
     * 编辑
     *
     * @BePermission("编辑", ordering="3.12")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            try {
                $publish = Be::getService('App.AiWriter.Admin.Publish')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑发布任务成功！');
                $response->set('publish', $publish);
                $response->set('redirectUrl', beAdminUrl('AiWriter.Publish.index'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } elseif ($request->isPost()) {
            $postData = $request->post('data', '', '');
            if ($postData) {
                $postData = json_decode($postData, true);
                if (isset($postData['row']['id']) && $postData['row']['id']) {
                    $response->redirect(beAdminUrl('AiWriter.Publish.edit', ['id' => $postData['row']['id']]));
                }
            }
        } else {
            $publishId = $request->get('id', '');
            $publish = Be::getService('App.AiWriter.Admin.Publish')->getPublish($publishId);
            $response->set('publish', $publish);

            $processKeyValues = Be::getService('App.AiWriter.Admin.Process')->getProcessKeyValues();
            $response->set('processKeyValues', $processKeyValues);

            $response->set('backUrl', beAdminUrl('AiWriter.Publish.index'));
            $response->set('formActionUrl', beAdminUrl('AiWriter.Publish.edit'));

            $response->set('title', '编辑发布任务');

            $response->display();
        }
    }

    /**
     * 删除
     *
     * @BePermission("删除", ordering="3.13")
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();

            $publishIds = [];
            if (isset($postData['selectedRows'])) {
                foreach ($postData['selectedRows'] as $row) {
                    $publishIds[] = $row['id'];
                }
            } elseif (isset($postData['row'])) {
                $publishIds[] = $postData['row']['id'];
            }

            if (count($publishIds) > 0) {
                Be::getService('App.AiWriter.Admin.Publish')->delete($publishIds);
            }

            $response->set('success', true);
            $response->set('message', '删除成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }



}

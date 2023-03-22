<?php

namespace Be\App\AiWriter\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Detail\Item\DetailItemProgress;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
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
        $processKeyValues = Be::getService('App.AiWriter.Admin.Process')->getProcessKeyValues();
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
                                    $percent = round($row['publish_count'] * 100 / $row['process_content_count'], 1);
                                    if ($percent > 100) {
                                        return 100;
                                    }

                                    return $percent;
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
                                'tooltip' => '复制',
                                'ui' => [
                                    'type' => 'success',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-copy-document',
                                'task' => 'copy',
                                'target' => 'ajax',
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

            'copy' => [
                'events' => [
                    'before' => function ($tuple) {
                        $i = 2;
                        do {
                            $name = $tuple->name . '-' . $i;
                            $count =  Be::getTable('aiwriter_publish')->where('name', $name)->count();
                        } while($count > 0);

                        $tuple->name = $name;
                    },
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
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'process_id',
                            'label' => '加工任务',
                            'keyValues' => $processKeyValues,
                        ],
                        [
                            'name' => 'process_content_count',
                            'label' => '文章数',
                            'value' => function ($row) {
                                $db = Be::getDb();
                                $sql = 'SELECT COUNT(*) FROM aiwriter_process_content WHERE process_id = ?';
                                $count = $db->getValue($sql, [$row['process_id']]);
                                return $count;
                            },
                        ],
                        [
                            'name' => 'publish_count',
                            'label' => '已发布',
                            'value' => function ($row) {
                                $sql = 'SELECT COUNT(*) FROM aiwriter_publish_content WHERE publish_id = ?';
                                $count = Be::getDb()->getValue($sql, [$row['id']]);
                                return $count;
                            },
                        ],
                        [
                            'name' => 'post_url',
                            'label' => '发布网址',
                        ],
                        [
                            'name' => 'post_headers',
                            'label' => '请求头',
                            'driver' => DetailItemHtml::class,
                            'value' => function ($row) {
                                $post_headers = unserialize($row['post_headers']);

                                $html = '';
                                $i = 0;
                                foreach ($post_headers as $post_header) {
                                    if ($i === 0) {
                                        $html .= '<div class="be-row">';
                                    } else {
                                        $html .= '<div class="be-row be-mt-100 be-bt-ccc">';
                                    }

                                    $html .= '<div class="be-col">' . $post_header['name'] . '</div>';
                                    $html .= '<div class="be-col">' . $post_header['value'] . '</div>';
                                    $i++;
                                }

                                return $html;
                            },
                        ],
                        [
                            'name' => 'post_format',
                            'label' => '请求格式',
                            'keyValues' => [
                                'form' => 'FORM 表单',
                                'json' => 'JSON 数据',
                            ],
                        ],
                        [
                            'name' => 'post_data_type',
                            'label' => '数据处理方法',
                            'keyValues' => [
                                'mapping' => '映射',
                                'code' => '代码处理',
                            ],
                        ],
                        [
                            'name' => 'post_data',
                            'label' => '请求数据',
                            'driver' => DetailItemHtml::class,
                            'value' => function ($row) {
                                $html = '';
                                if ($row['post_data_type'] === 'mapping') {
                                    $post_data_mapping = unserialize($row['post_data_mapping']);
                                    $i = 0;
                                    foreach ($post_data_mapping as $mapping) {
                                        if ($i === 0) {
                                            $html .= '<div class="be-row">';
                                        } else {
                                            $html .= '<div class="be-row be-bt-ccc">';
                                        }

                                        $html .= '<div class="be-col">' . $mapping['name'] . '</div>';
                                        switch ($mapping['value_type']) {
                                            case 'field':
                                                $html .= '<div class="be-col">取用：</div>';
                                                $html .= '<div class="be-col">' . $mapping['field'] . '</div>';
                                                break;
                                            case 'custom':
                                                $html .= '<div class="be-col">自定义：</div>';
                                                $html .= '<div class="be-col">' . $mapping['custom'] . '</div>';
                                                break;
                                        }
                                        $html .= '</div>';
                                        $i++;
                                    }
                                } elseif ($row['post_data_type'] === 'code') {
                                    $html .= '<pre>';
                                    $html .= $row['post_data_code'];
                                    $html .= '</pre>';
                                }

                                return $html;
                            },
                        ],
                        [
                            'name' => 'success_mark',
                            'label' => '成功标识',
                        ],
                        [
                            'name' => 'interval',
                            'label' => '间隔时间（毫秒）',
                        ],
                        [
                            'name' => 'is_enable',
                            'driver' => DetailItemSwitch::class,
                            'label' => '是否启用',
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



    /**
     * 指定加工任务下的加工结果
     *
     * @BePermission("*")
     */
    public function goProcessContents()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('AiWriter.ProcessContent.index', ['process_id' => $postData['row']['process_id']]));
            }
        }
    }

    /**
     * 指定发布任务下的发布结果
     *
     * @BePermission("*")
     */
    public function goPublishContents()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('AiWriter.PublishContent.index', ['publish_id' => $postData['row']['id']]));
            }
        }
    }


}

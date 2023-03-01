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
 * 存储管理器
 *
 * @BeMenuGroup("加工", icon = "bi-pencil-square", ordering="2")
 * @BePermissionGroup("加工")
 */
class Process extends Auth
{

    /**
     * @BeMenu("任务管理", icon = "bi-list-check", ordering="2.10")
     * @BePermission("任务管理", ordering="2.10")
     */
    public function index()
    {


        Be::getAdminPlugin('Curd')->setting([
            'label' => '加工任务',
            'table' => 'aiwriter_process',
            'grid' => [
                'title' => '任务管理',
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
                            'label' => '新建加工任务',
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
                            'name' => 'material_count',
                            'label' => '素材数',
                            'align' => 'center',
                            'width' => '90',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $db = Be::getDb();
                                if ($row['material_category_id'] === 'all') {
                                    $sql = 'SELECT COUNT(*) FROM aiwriter_material';
                                    $count = $db->getValue($sql);
                                } else {
                                    $sql = 'SELECT COUNT(*) FROM aiwriter_material WHERE category_id = ?';
                                    $count = $db->getValue($sql, [$row['material_category_id']]);
                                }

                                return $count;
                            },
                            'action' => 'goMaterials',
                            'target' => 'self',
                        ],
                        [
                            'name' => 'process_count',
                            'label' => '已加工',
                            'align' => 'center',
                            'width' => '90',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $sql = 'SELECT COUNT(*) FROM aiwriter_process_content WHERE process_id = ?';
                                $count = Be::getDb()->getValue($sql, [$row['id']]);
                                return $count;
                            },
                            'action' => 'goProcessContents',
                            'target' => 'self',
                        ],
                        [
                            'name' => 'process_percent',
                            'label' => '进度',
                            'align' => 'center',
                            'width' => '120',
                            'driver' => TableItemProgress::class,
                            'value' => function ($row) {
                                if ($row['material_count'] > 0) {
                                    return round($row['process_count'] * 100 / $row['material_count'], 1);
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
     * 新建加工任务
     *
     * @BePermission("新建", ordering="2.11")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                $process = Be::getService('App.AiWriter.Admin.Process')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建加工任务成功！');
                $response->set('process', $process);
                $response->set('redirectUrl', beAdminUrl('AiWriter.Process.index'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('process', false);

            $materialCategoryKeyValues = Be::getService('App.AiWriter.Admin.MaterialCategory')->getCategoryKeyValues();
            $materialCategoryKeyValues = \Be\Util\Arr::merge([
                'all' => '全部',
                '' => '未分类',
            ], $materialCategoryKeyValues);
            $response->set('materialCategoryKeyValues', $materialCategoryKeyValues);

            $serviceProcessTemplate = Be::getService('App.AiWriter.Admin.ProcessTemplate');
            $titleTemplates = $serviceProcessTemplate->getTemplates('title');
            $summaryTemplates = $serviceProcessTemplate->getTemplates('summary');
            $descriptionTemplates = $serviceProcessTemplate->getTemplates('description');
            $response->set('titleTemplates', $titleTemplates);
            $response->set('summaryTemplates', $summaryTemplates);
            $response->set('descriptionTemplates', $descriptionTemplates);

            $response->set('backUrl', beAdminUrl('AiWriter.Process.index'));
            $response->set('formActionUrl', beAdminUrl('AiWriter.Process.create'));

            $response->set('title', '新建加工任务');

            $response->display('App.AiWriter.Admin.Process.edit');
        }
    }

    /**
     * 编辑
     *
     * @BePermission("编辑", ordering="2.11")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            try {
                $process = Be::getService('App.AiWriter.Admin.Process')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑加工任务成功！');
                $response->set('process', $process);
                $response->set('redirectUrl', beAdminUrl('AiWriter.Process.index'));
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
                    $response->redirect(beAdminUrl('AiWriter.Process.edit', ['id' => $postData['row']['id']]));
                }
            }
        } else {
            $processId = $request->get('id', '');
            $process = Be::getService('App.AiWriter.Admin.Process')->getProcess($processId);
            $response->set('process', $process);

            $materialCategoryKeyValues = Be::getService('App.AiWriter.Admin.MaterialCategory')->getCategoryKeyValues();
            $materialCategoryKeyValues = \Be\Util\Arr::merge([
                'all' => '全部',
                '' => '未分类',
            ], $materialCategoryKeyValues);
            $response->set('materialCategoryKeyValues', $materialCategoryKeyValues);

            $serviceProcessTemplate = Be::getService('App.AiWriter.Admin.ProcessTemplate');
            $titleTemplates = $serviceProcessTemplate->getTemplates('title');
            $summaryTemplates = $serviceProcessTemplate->getTemplates('summary');
            $descriptionTemplates = $serviceProcessTemplate->getTemplates('description');
            $response->set('titleTemplates', $titleTemplates);
            $response->set('summaryTemplates', $summaryTemplates);
            $response->set('descriptionTemplates', $descriptionTemplates);

            $response->set('backUrl', beAdminUrl('AiWriter.Process.index'));
            $response->set('formActionUrl', beAdminUrl('AiWriter.Process.edit'));

            $response->set('title', '编辑加工任务');

            $response->display();
        }
    }

    /**
     * 删除
     *
     * @BePermission("删除", ordering="2.13")
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();

            $processIds = [];
            if (isset($postData['selectedRows'])) {
                foreach ($postData['selectedRows'] as $row) {
                    $processIds[] = $row['id'];
                }
            } elseif (isset($postData['row'])) {
                $processIds[] = $postData['row']['id'];
            }

            if (count($processIds) > 0) {
                Be::getService('App.AiWriter.Admin.Process')->delete($processIds);
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

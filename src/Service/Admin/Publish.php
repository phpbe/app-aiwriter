<?php

namespace Be\App\AiWriter\Service\Admin;

use Be\App\ServiceException;
use Be\Be;

class Publish
{

    /**
     * 编辑发布任务
     *
     * @param array $data 发布任务数据
     * @return object
     * @throws \Throwable
     */
    public function edit(array $data): object
    {
        $db = Be::getDb();

        $isNew = true;
        $publishId = null;
        if (isset($data['id']) && $data['id'] !== '') {
            $isNew = false;
            $publishId = $data['id'];
        }

        $tuplePublish = Be::getTuple('aiwriter_publish');
        if (!$isNew) {
            try {
                $tuplePublish->load($publishId);
            } catch (\Throwable $t) {
                throw new ServiceException('发布任务（# ' . $publishId . '）不存在！');
            }
        }

        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new ServiceException('发布任务名称未填写！');
        }

        if (!isset($data['material_category_id']) || !is_string($data['material_category_id'])) {
            throw new ServiceException('素材分类未填写！');
        }

        if (!isset($data['details']) || !is_array($data['details'])) {
            throw new ServiceException('素材发布参数缺失！');
        }


        // ------------------------------------------------------------------------------------------------------------- 标题检测
        if (!isset($data['details']['title']) || !is_array($data['details']['title'])) {
            throw new ServiceException('素材发布 - 标题参数缺失！');
        }

        if (!isset($data['details']['title']['type']) || !is_string($data['details']['title']['type'])) {
            throw new ServiceException('素材发布 - 标题.类型参数缺失！');
        }

        if (!in_array($data['details']['title']['type'], ['material', 'ai'])) {
            throw new ServiceException('素材发布 - 标题.类型参数无效！');
        }

        if ($data['details']['title']['type'] === 'ai') {
            if (!isset($data['details']['title']['ai']) || !is_string($data['details']['title']['ai'])) {
                throw new ServiceException('素材发布 - 标题.AI处理参数缺失！');
            }
        } else {
            $data['details']['title']['ai'] = '';
        }

        $details = [];
        $details['title'] = [
            'type' => $data['details']['title']['type'],
            'ai' => $data['details']['title']['ai'],
        ];
        // ============================================================================================================= 标题检测


        // ------------------------------------------------------------------------------------------------------------- 摘要检测
        if (!isset($data['details']['summary']) || !is_array($data['details']['summary'])) {
            throw new ServiceException('素材发布 - 摘要参数缺失！');
        }

        if (!isset($data['details']['summary']['type']) || !is_string($data['details']['summary']['type'])) {
            throw new ServiceException('素材发布 - 摘要.类型参数缺失！');
        }

        if (!in_array($data['details']['summary']['type'], ['material', 'extract', 'ai'])) {
            throw new ServiceException('素材发布 - 摘要.类型参数无效！');
        }

        if ($data['details']['summary']['type'] === 'extract') {
            if (!isset($data['details']['summary']['extract']) || !is_numeric($data['details']['summary']['extract'])) {
                throw new ServiceException('素材发布 - 摘要.提取长度参数缺失！');
            }
        } else {
            $data['details']['summary']['extract'] = '';
        }

        if ($data['details']['summary']['type'] === 'ai') {
            if (!isset($data['details']['summary']['ai']) || !is_string($data['details']['summary']['ai'])) {
                throw new ServiceException('素材发布 - 摘要.AI处理参数缺失！');
            }
        } else {
            $data['details']['summary']['ai'] = '';
        }

        $details['summary'] = [
            'type' => $data['details']['summary']['type'],
            'extract' => $data['details']['summary']['extract'],
            'ai' => $data['details']['summary']['ai'],
        ];
        // ============================================================================================================= 摘要检测


        // ------------------------------------------------------------------------------------------------------------- 描述检测
        if (!isset($data['details']['description']) || !is_array($data['details']['description'])) {
            throw new ServiceException('素材发布 - 描述参数缺失！');
        }

        if (!isset($data['details']['description']['type']) || !is_string($data['details']['description']['type'])) {
            throw new ServiceException('素材发布 - 描述.类型参数缺失！');
        }

        if (!in_array($data['details']['description']['type'], ['material', 'ai'])) {
            throw new ServiceException('素材发布 - 描述.类型参数无效！');
        }

        if ($data['details']['description']['type'] === 'ai') {
            if (!isset($data['details']['description']['ai']) || !is_string($data['details']['description']['ai'])) {
                throw new ServiceException('素材发布 - 摘要.AI处理参数缺失！');
            }
        } else {
            $data['details']['description']['ai'] = '';
        }

        $details['description'] = [
            'type' => $data['details']['description']['type'],
            'ai' => $data['details']['description']['ai'],
        ];
        // ============================================================================================================= 描述检测


        if (!isset($data['is_enable']) || !is_numeric($data['is_enable'])) {
            $data['is_enable'] = 0;
        } else {
            $data['is_enable'] = (int)$data['is_enable'];
        }
        if (!in_array($data['is_enable'], [-1, 0, 1])) {
            $data['is_enable'] = 0;
        }

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tuplePublish->name = $data['name'];
            $tuplePublish->material_category_id = $data['material_category_id'];
            $tuplePublish->details = serialize($details);
            $tuplePublish->is_enable = $data['is_enable'];
            $tuplePublish->update_time = $now;
            if ($isNew) {
                $tuplePublish->create_time = $now;
                $tuplePublish->insert();
            } else {
                $tuplePublish->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);
            throw new ServiceException(($isNew ? '新建' : '编辑') . '发布任务发生异常！');
        }

        return $tuplePublish->toObject();
    }

    /**
     * 删除发布任务
     *
     * @param array $publishIds
     * @return void
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function delete(array $publishIds)
    {
        if (count($publishIds) === 0) return;

        $db = Be::getDb();

        foreach ($publishIds as $publishId) {
            $tuplePublish = Be::getTuple('aiwriter_publish');
            try {
                $tuplePublish->load($publishId);
            } catch (\Throwable $t) {
                throw new ServiceException('发布任务（# ' . $publishId . '）不存在！');
            }

            $db->startTransaction();
            try {

                // 删除发布任务生成的内容
                Be::getTable('aiwriter_publish_content')
                    ->where('publish_id', '=', $publishId)
                    ->delete();

                $tuplePublish->delete();

                $db->commit();

            } catch (\Throwable $t) {
                $db->rollback();
                Be::getLog()->error($t);

                throw new ServiceException('删除发布任务发生异常！');
            }
        }
    }

    /**
     * 获取发布任务
     *
     * @param $publishId
     * @return object
     */
    public function getPublish($publishId): object
    {
        $tuplePublish = Be::getTuple('aiwriter_publish');
        try {
            $tuplePublish->load($publishId);
        } catch (\Throwable $t) {
            throw new ServiceException('发布任务（# ' . $publishId . '）不存在！');
        }

        $details = unserialize($tuplePublish->details);
        if (!isset($details['title'])) {
            $details['title'] = [
                'type' => 'ai',
                'ai' => "",
            ];
        }
        if (!isset($details['title']['type'])) {
            $details['title']['type'] = 'ai';
        }
        if (!isset($details['title']['ai'])) {
            $details['title']['ai'] = '';
        }

        if (!isset($details['summary'])) {
            $details['summary'] = [
                'type' => 'extract',
                'extract' => 120,
                'ai' => "",
            ];
        }
        if (!isset($details['summary']['type'])) {
            $details['summary']['type'] = 'ai';
        }
        if (!isset($details['summary']['extract'])) {
            $details['summary']['extract'] = 120;
        }
        if (!isset($details['summary']['ai'])) {
            $details['summary']['ai'] = '';
        }

        if (!isset($details['description'])) {
            $details['description'] = [
                'type' => 'ai',
                'ai' => "",
            ];
        }
        if (!isset($details['description']['type'])) {
            $details['description']['type'] = 'ai';
        }
        if (!isset($details['description']['ai'])) {
            $details['summary']['ai'] = '';
        }

        $tuplePublish->details = $details;

        return $tuplePublish->toObject();
    }

    /**
     * 获取发布任务键值对
     *
     * @return array
     */
    public function getPublishKeyValues(): array
    {
        $sql = 'SELECT id, `name` FROM aiwriter_publish ORDER BY create_time DESC';
        return Be::getDb()->getKeyValues($sql);
    }

}

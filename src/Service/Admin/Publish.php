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
            throw new ServiceException('发布任务名称参数无效！');
        }
        $data['name'] = trim($data['name']);
        if ($data['name'] === '') {
            throw new ServiceException('发布任务名称未填写！');
        }

        if (!isset($data['process_id']) || !is_string($data['process_id'])) {
            throw new ServiceException('加工任务参数无效！');
        }
        $data['process_id'] = trim($data['process_id']);
        if ($data['process_id'] === '') {
            throw new ServiceException('加工任务未填写！');
        }

        if (!isset($data['post_url']) || !is_string($data['post_url'])) {
            throw new ServiceException('发布网址参数无效！');
        }
        $data['post_url'] = trim($data['post_url']);
        if ($data['post_url'] === '') {
            throw new ServiceException('发布网址未填写！');
        }

        // ------------------------------------------------------------------------------------------------------------- 请求头
        if (!isset($data['post_headers']) || !is_array($data['post_headers'])) {
            throw new ServiceException('请求头参数无效！');
        }

        $i = 1;
        foreach ($data['post_headers'] as &$header) {

            if (!isset($header['name']) || !is_string($header['name'])) {
                throw new ServiceException('第' . $i . '项请求头的名称参数无效！');
            }

            $header['name'] = trim($header['name']);
            if ($header['name'] === '') {
                throw new ServiceException('第' . $i . '项请求头的名称不能为空！');
            }

            if (!isset($header['value']) || !is_string($header['value'])) {
                throw new ServiceException('第' . $i . '项请求头的值参数无效！');
            }

            $header['value'] = trim($header['value']);
            if ($header['value'] === '') {
                throw new ServiceException('第' . $i . '项请求头的值不能为空！');
            }

            $i++;
        }
        unset($header);
        // ============================================================================================================= 请求头


        if (!isset($data['post_format']) || !is_string($data['post_format'])) {
            throw new ServiceException('请求格式参数无效！');
        }
        $data['post_format'] = trim($data['post_format']);
        if (!in_array($data['post_format'], ['form', 'json'])) {
            throw new ServiceException('请求格式参数无效！');
        }


        if (!isset($data['post_data_type']) || !is_string($data['post_data_type'])) {
            throw new ServiceException('数据处理方法参数无效！');
        }
        $data['post_data_type'] = trim($data['post_data_type']);
        if (!in_array($data['post_data_type'], ['mapping', 'code'])) {
            throw new ServiceException('数据处理方法参数无效！');
        }

        if ($data['post_data_type'] === 'mapping') {
            //$data['post_data_code'] = '';

            if (!isset($data['post_data_mapping']) || !is_array($data['post_data_mapping'])) {
                throw new ServiceException('映射参数无效！');
            }

            $i = 1;
            foreach ($data['post_data_mapping'] as &$mapping) {

                if (!isset($mapping['name']) || !is_string($mapping['name'])) {
                    throw new ServiceException('第' . $i . '项映射的名称参数无效！');
                }
                $mapping['name'] = trim($mapping['name']);
                if ($mapping['name'] === '') {
                    throw new ServiceException('第' . $i . '项映射的名称不能为空！');
                }


                if (!isset($mapping['value_type']) || !is_string($mapping['value_type'])) {
                    throw new ServiceException('第' . $i . '项映射的值类型参数无效！');
                }
                $mapping['value_type'] = trim($mapping['value_type']);
                if (!in_array($mapping['value_type'], ['field', 'custom'])) {
                    throw new ServiceException('第' . $i . '项映射的值类型参数无效！');
                }

                if ($mapping['value_type'] === 'field') {
                    //$mapping['custom'] = '';

                    if (!isset($mapping['field']) || !is_string($mapping['field'])) {
                        throw new ServiceException('第' . $i . '项映射的取用参数无效！');
                    }
                    $mapping['field'] = trim($mapping['field']);
                    if ($mapping['field'] === '') {
                        throw new ServiceException('第' . $i . '项映射的取用不能为空！');
                    }

                } else {
                    //$mapping['field'] = '';

                    if (!isset($mapping['custom']) || !is_string($mapping['custom'])) {
                        throw new ServiceException('第' . $i . '项映射的自定义值参数无效！');
                    }
                    $mapping['custom'] = trim($mapping['custom']);
                    if ($mapping['custom'] === '') {
                        throw new ServiceException('第' . $i . '项映射的自定义值不能为空！');
                    }
                }

                $i++;
            }
            unset($mapping);

        } else {
            //$data['post_data_mapping'] = [];

            if (!isset($data['post_data_code']) || !is_string($data['post_data_code'])) {
                throw new ServiceException('代码处理参数无效！');
            }
            $data['post_data_code'] = trim($data['post_data_code']);
            if ($data['post_data_code'] === '') {
                throw new ServiceException('代码处理未填写！');
            }
        }

        if (!isset($data['success_mark']) || !is_string($data['success_mark'])) {
            throw new ServiceException('成功标识参数无效！');
        }
        $data['success_mark'] = trim($data['success_mark']);
        if ($data['success_mark'] === '') {
            throw new ServiceException('成功标识未填写！');
        }

        if (!isset($data['interval']) || !is_numeric($data['interval'])) {
            $data['interval'] = 1000;
        } else {
            $data['interval'] = (int)$data['interval'];
        }

        if ($data['interval'] < 0) {
            $data['interval'] = 1000;
        }

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
            $tuplePublish->process_id = $data['process_id'];
            $tuplePublish->post_url = $data['post_url'];
            $tuplePublish->post_headers = serialize($data['post_headers']);
            $tuplePublish->post_format = $data['post_format'];
            $tuplePublish->post_data_type = $data['post_data_type'];
            $tuplePublish->post_data_mapping = serialize($data['post_data_mapping']);
            $tuplePublish->post_data_code = $data['post_data_code'];
            $tuplePublish->success_mark = $data['success_mark'];
            $tuplePublish->interval = $data['interval'];
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

        $tuplePublish->post_headers = unserialize($tuplePublish->post_headers);
        $tuplePublish->post_data_mapping = unserialize($tuplePublish->post_data_mapping);

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

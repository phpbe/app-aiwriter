<?php

namespace Be\App\AiWriter\Task;

use Be\Be;
use Be\Task\Task;
use Be\Task\TaskException;
use Be\Util\Net\Curl;

/**
 * 发布
 *
 * @BeTask("发布")
 */
class Publish extends Task
{
    /**
     * 执行超时时间
     *
     * @var null|int
     */
    protected $timeout = 300;


    public function execute()
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM aiwriter_publish WHERE is_enable = 1';
        $publishes = $db->getObjects($sql);
        foreach ($publishes as $publish) {

            $sql = 'SELECT COUNT(*) FROM aiwriter_process_content WHERE process_id = ?';
            $processContentCount = $db->getValue($sql, [$publish->process_id]);
            if ($processContentCount === 0) {
                continue;
            }

            $sql = 'SELECT COUNT(*) FROM aiwriter_publish_content WHERE publish_id = ?';
            $publishContentCount = Be::getDb()->getValue($sql, [$publish->id]);

            if ($publishContentCount >= $processContentCount) {
                continue;
            }

            $sql = 'SELECT t1.* FROM aiwriter_process_content t1 LEFT JOIN aiwriter_publish_content t2 ON t1.id=t2.process_content_id AND t1.process_id=? AND t2.publish_id=? WHERE t2.id is NULL';
            $processContents = $db->getObjects($sql, [$publish->process_id, $publish->id]);

            if (count($processContents) === 0) {
                continue;
            }

            $postHeaders = unserialize($publish->post_headers);

            if ($publish->post_data_type === 'mapping') {
                $postDataMapping = unserialize($publish->post_data_mapping);
            }

            foreach ($processContents as $processContent) {

                try {
                    $sql = 'SELECT * FROM aiwriter_material WHERE id = ?';
                    $material = $db->getObject($sql, [$processContent->material_id]);

                    /*
                    if (!$material) {
                        throw new TaskException('原始素材（' . $processContent->material_id . '）不存在！');
                    }
                    */

                    if ($publish->post_data_type === 'mapping') {
                        $postData = [];
                        foreach ($postDataMapping as $mapping) {
                            switch ($mapping['value_type']) {
                                case 'field':
                                    $field = $mapping['field'];
                                    if (substr($field, 0, 9) === 'material.') {

                                        if (!$material) {
                                            throw new TaskException('原始素材（' . $processContent->material_id . '）不存在！');
                                        }

                                        $postData[$mapping['name']] = $material->$field;
                                    } else {
                                        $postData[$mapping['name']] = $processContent->$field;
                                    }
                                    break;
                                case 'custom':
                                    $postData[$mapping['name']] = $mapping['custom'];
                                    break;
                            }
                        }
                    } else {
                        $postDataCodeFn = eval('return function($row, $material){' . $publish->post_data_code . '};');
                        $postData = $postDataCodeFn($processContent, $material);
                    }

                    if ($publish->post_format === 'form') {
                        $response = Curl::post($publish->post_url, $postData, $postHeaders);
                    } else {
                        $response = Curl::postJson($publish->post_url, $postData, $postHeaders);
                    }

                    $isSuccess = 0;
                    if (strpos($response, $publish->success_mark) !== false) {
                        $isSuccess = 1;
                    }

                    $obj = new \stdClass();
                    $obj->id = $db->uuid();
                    $obj->publish_id = $publish->id;
                    $obj->process_content_id = $processContent->id;
                    $obj->is_success = $isSuccess;
                    $obj->response = $response;
                    $obj->create_time = date('Y-m-d H:i:s');
                    $obj->update_time = date('Y-m-d H:i:s');
                    $db->insert('aiwriter_publish_content', $obj);

                } catch (\Throwable $t) {
                    Be::getLog()->warning($t);
                }

                if (Be::getRuntime()->isSwooleMode()) {
                    \Swoole\Coroutine::sleep($processContent->interval);
                } else {
                    sleep($processContent->interval);
                }

            }
        }
    }


}

<?php

namespace Be\App\AiWriter\Task;

use Be\Be;
use Be\Task\Task;
use Be\Task\TaskException;

/**
 * 加工
 *
 * @BeTask("加工")
 */
class Process extends Task
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
        $sql = 'SELECT * FROM aiwriter_process WHERE is_enable = 1';
        $processes = $db->getObjects($sql);
        foreach ($processes as $process) {
            if ($process->material_category_id === 'all') {
                $sql = 'SELECT COUNT(*) FROM aiwriter_material';
                $materialCount = $db->getValue($sql);
            } else {
                $sql = 'SELECT COUNT(*) FROM aiwriter_material WHERE category_id = ?';
                $materialCount = $db->getValue($sql, [$process->material_category_id]);
            }

            if ($materialCount === 0) {
                continue;
            }

            $sql = 'SELECT COUNT(*) FROM aiwriter_process_content WHERE process_id = ?';
            $processContentCount = Be::getDb()->getValue($sql, [$process->id]);

            if ($processContentCount >= $materialCount) {
                continue;
            }

            if ($process->material_category_id === 'all') {
                $sql = 'SELECT m.* FROM aiwriter_material m LEFT JOIN aiwriter_process_content pc ON m.id=pc.material_id AND pc.process_id=? WHERE pc.id is NULL';
                $materials = $db->getObjects($sql, [$process->id]);
            } else {
                $sql = 'SELECT m.* FROM aiwriter_material m LEFT JOIN aiwriter_process_content pc ON m.id=pc.material_id AND pc.process_id=? WHERE m.category_id = ? AND pc.id is NULL';
                $materials = $db->getObjects($sql, [$process->id, $process->material_category_id]);
            }

            if (count($materials) === 0) {
                continue;
            }

            $processDetails = unserialize($process->details);
            foreach ($materials as $material) {

                $content = new \stdClass();

                try {
                    $title = '';
                    switch ($processDetails['title']['type']) {
                        case 'material':
                            $title = $material->title;
                            break;
                        case 'ai':
                            $messages = $this->formatAiMessages($processDetails['title'], $material, $content);
                            $title = $this->chatCompletion($messages);
                            break;
                    }
                    $title = str_replace("\n", '', $title);
                    $title = strip_tags($title);
                    $title = ltrim($title, ",.;?，、。；？ \t\n\r\0\x0B");
                    $title = trim($title);

                    $content->title = $title;

                    $description = '';
                    switch ($processDetails['description']['type']) {
                        case 'material':
                            $description = $material->description;
                            break;
                        case 'ai':
                            $messages = $this->formatAiMessages($processDetails['description'], $material, $content);
                            $description = $this->chatCompletion($messages);
                            break;
                    }
                    $description = ltrim($description, ",.;?，、。；？ \t\n\r\0\x0B");
                    $description = trim($description);
                    $content->description = $description;
                    $description = nl2br($description);

                    $summary = '';
                    switch ($processDetails['summary']['type']) {
                        case 'material':
                            $summary = $material->summary;
                            break;
                        case 'extract':
                            if (mb_strlen($description) <= $processDetails['summary']['extract']) {
                                $summary = $description;
                            } else {
                                $summary = mb_substr($description, 0, $processDetails['summary']['extract']);
                                $pos = strrpos($summary, '<br />');
                                if ($pos !== false) {
                                    $summary2 = substr($summary, 0, $pos);
                                    if (mb_strlen($summary2) >= $processDetails['summary']['extract'] / 2) {
                                        $summary = $summary2;
                                    }
                                }
                            }
                            break;
                        case 'ai':
                            $messages = $this->formatAiMessages($processDetails['summary'], $material, $content);
                            $summary = $this->chatCompletion($messages);
                            break;
                    }
                    $summary = str_replace("\n", '', $summary);
                    $summary = strip_tags($summary);
                    $summary = ltrim($summary, ",.;?，、。；？ \t\n\r\0\x0B");
                    $summary = trim($summary);

                    $obj = new \stdClass();
                    $obj->id = $db->uuid();
                    $obj->process_id = $process->id;
                    $obj->material_id = $material->id;
                    $obj->title = $title;
                    $obj->summary = $summary;
                    $obj->description = $description;
                    $obj->create_time = date('Y-m-d H:i:s');
                    $obj->update_time = date('Y-m-d H:i:s');
                    $db->insert('aiwriter_process_content', $obj);

                } catch (\Throwable $t) {
                    Be::getLog()->warning($t);
                }
            }
        }
    }

    /**
     * 格式化提问
     *
     * @param array $ai
     * @param object $material
     * @param object $content
     * @return array
     */
    private function formatAiMessages(array $ai, object $material, object $content): array
    {
        $messages = [];

        if (isset($ai['ai_system_prompt']) && is_string($ai['ai_system_prompt']) && $ai['ai_system_prompt'] !== '') {
            $messages[] = [
                'role' => 'system',
                'content' => $ai['ai_system_prompt'],
            ];
        }

        if (!isset($ai['ai_user_prompt']) || !is_string($ai['ai_user_prompt']) || $ai['ai_user_prompt'] === '') {
            throw new TaskException('加工任务未配置AI处理的用户提示语！');
        }

        $userPrompt = $ai['ai_user_prompt'];
        $userPrompt = str_replace('{素材标题}', $material->title, $userPrompt);
        $userPrompt = str_replace('{素材摘要}', $material->summary, $userPrompt);
        $userPrompt = str_replace('{素材描述}', $material->description, $userPrompt);

        if (isset($content->title)) {
            $userPrompt = str_replace('{标题}', $content->title, $userPrompt);
        }

        if (isset($content->description)) {
            $userPrompt = str_replace('{描述}', $content->description, $userPrompt);
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt,
        ];

        return $messages;
    }

    /**
     * 文本应签
     *
     * @param string $prompt
     * @return string
     * @throws TaskException
     */
    private function chatCompletion(array $messages): string
    {
        $serviceApi = Be::getService('App.Openai.Api');

        $err = null;

        $times = 1;
        do {

            $hasError = false;
            try {
                $answer = $serviceApi->chatCompletion($messages);
            } catch (\Throwable $t) {
                $hasError = true;

                $err = $t;
            }

            if (!$hasError) {
                break;
            }

            if (Be::getRuntime()->isSwooleMode()) {
                \Swoole\Coroutine::sleep(3);
            } else {
                sleep(3);
            }

            $times++;

        } while ($times < 5);

        if ($hasError) {
            throw new TaskException('调用OpenAi接口重试出错超过5次：' . $err->getMessage());
        }

        return $answer;
    }

}

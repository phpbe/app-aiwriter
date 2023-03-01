<?php

namespace Be\App\AiWriter\Service\Admin;

use Be\Be;

class ProcessTemplate
{
    /**
     * 获取模板
     *
     * @return array
     */
    public function getTemplates($type): array
    {
        $sql = 'SELECT `content` FROM aiwriter_process_template WHERE type=? ORDER BY ordering ASC';
        return Be::getDb()->getValues($sql, [$type]);
    }

}

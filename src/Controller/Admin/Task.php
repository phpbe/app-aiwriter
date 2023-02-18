<?php

namespace Be\App\AiWriter\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Task extends Auth
{
    /**
     * @BeMenu("计划任务", icon="el-icon-timer", ordering="7.5")
     * @BePermission("计划任务", ordering="7.5")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Task')->setting(['appName' => 'AiWriter'])->execute();
    }

}

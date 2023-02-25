<?php
namespace Be\App\AiWriter\Task;

use Be\Be;
use Be\Task\Task;

/**
 * 加工
 *
 * @BeTask("加工")
 */
class Process extends Task
{

    public function execute()
    {
        return;
        $configSystemEs = Be::getConfig('App.System.Es');
        $service = Be::getService('App.Shop.Admin.TaskProduct');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product WHERE is_enable != -1';
        $products = $db->getYieldObjects($sql);

        $batch = [];
        $i = 0;
        foreach ($products as $product) {
            $batch[] = $product;

            $i++;
            if ($i >= 100) {
                if ($configSystemEs->enable === 1) {
                    $service->syncEs($batch);
                }
                $service->syncCache($batch);

                $batch = [];
                $i = 0;
            }
        }

        if ($i > 0) {
            if ($configSystemEs->enable === 1) {
                $service->syncEs($batch);
            }
            $service->syncCache($batch);
        }
    }

}

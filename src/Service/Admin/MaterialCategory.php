<?php

namespace Be\App\AiWriter\Service\Admin;

use Be\App\ServiceException;
use Be\Be;
use Be\Util\Str\Pinyin;

class MaterialCategory
{

    /**
     * 获取分类列表
     *
     * @return array
     */
    public function getCategories(): array
    {
        $sql = 'SELECT * FROM aiwriter_material_category WHERE is_delete = 0 ORDER BY ordering ASC';
        $categories = Be::getDb()->getObjects($sql);
        return $categories;
    }

    /**
     * 获取分类
     *
     * @param string $categoryId
     * @return object
     */
    public function getCategory(string $categoryId): object
    {
        $sql = 'SELECT * FROM aiwriter_material_category WHERE id=? AND is_delete = 0';
        $category = Be::getDb()->getObject($sql, [$categoryId]);
        if (!$category) {
            throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
        }

        $category->ordering = (int)$category->ordering;

        return $category;
    }

    /**
     * 获取分类键值对
     *
     * @return array
     */
    public function getCategoryKeyValues(): array
    {
        $sql = 'SELECT id, `name` FROM aiwriter_material_category WHERE is_delete = 0 ORDER BY ordering ASC';
        return Be::getDb()->getKeyValues($sql);
    }

    /**
     * 编辑分类
     *
     * @param array $data 分类数据
     * @return object
     */
    public function edit(array $data): object
    {
        $db = Be::getDb();

        $isNew = true;
        $categoryId = null;
        if (isset($data['id']) && is_string($data['id']) && $data['id'] !== '') {
            $isNew = false;
            $categoryId = $data['id'];
        }

        $tupleCategory = Be::getTuple('aiwriter_material_category');
        if (!$isNew) {
            try {
                $tupleCategory->load($categoryId);
            } catch (\Throwable $t) {
                throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
            }

            if ($tupleCategory->is_delete === 1) {
                throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
            }
        }

        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new ServiceException('分类名称未填写！');
        }
        $name = $data['name'];

        if (!isset($data['ordering']) || !is_numeric($data['ordering'])) {
            $data['ordering'] = 0;
        }

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tupleCategory->name = $name;
            $tupleCategory->ordering = $data['ordering'];
            $tupleCategory->update_time = $now;
            if ($isNew) {
                $tupleCategory->is_delete = 0;
                $tupleCategory->create_time = $now;
                $tupleCategory->insert();
            } else {
                $tupleCategory->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException(($isNew ? '新建' : '编辑') . '分类发生异常！');
        }

        return $tupleCategory->toObject();
    }

    /**
     * 删除分类
     *
     * @param array $categoryIds
     * @return void
     */
    public function delete(array $categoryIds)
    {
        if (count($categoryIds) === 0) return;

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            foreach ($categoryIds as $categoryId) {
                $tupleCategory = Be::getTuple('aiwriter_material_category');
                try {
                    $tupleCategory->loadBy([
                        'id' => $categoryId,
                        'is_delete' => 0
                    ]);
                } catch (\Throwable $t) {
                    throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
                }

                if (Be::getTable('aiwriter_material')
                        ->where('category_id', '=', $categoryId)
                        ->count() > 0) {
                    Be::getTable('aiwriter_material')
                        ->where('category_id', '=', $categoryId)
                        ->update([
                            'category_id' => '',
                            'update_time' => $now
                        ]);
                }

                $tupleCategory->is_delete = 1;
                $tupleCategory->update_time = $now;
                $tupleCategory->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('删除分类发生异常！');
        }
    }


}

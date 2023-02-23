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



}

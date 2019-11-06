<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use function is_array;

/**
 * Валидатор массива ID.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class IdsValidator extends AbstractValidator
{
    /**
     * Парсит массив id
     *
     * @param null|int[]|string[] $ids
     * @param array $config
     * @return int[]
     * @throws \yii\base\Exception
     */
    public static function parse($ids, array $config = [])
    {
        if ($ids === null || $ids === '' || $ids === []) {
            return [];
        }

        if (! is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $i => &$id) {
            $id = IdValidator::parse($id);
            if (empty($id)) {
                unset($ids[$i]);
            }
        }

        return $ids;
    }
}

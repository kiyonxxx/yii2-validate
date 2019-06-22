<?php
namespace dicr\validate;

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
     * @throws \Exception
     * @return int[]
     */
    public static function parse($ids)
    {
        if (is_null($ids) || $ids === '' || $ids === []) {
            return [];
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $i => $id) {
            $id = IdValidator::parse($id);
            if (empty($id)) {
                unset($ids[$i]);
            } else {
                $ids[$i] = $id;
            }
        }

        return $ids;
    }
}
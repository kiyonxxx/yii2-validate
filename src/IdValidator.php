<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;
use function is_int;
use function is_string;

/**
 * Валидация ID
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class IdValidator extends AbstractValidator
{
    /**
     * Парсит ID.
     *
     * @param int|string|null $id
     * @param array $config
     * @return int|null
     * @throws \yii\base\Exception
     */
    public static function parse($id, array $config = [])
    {
        // если пустое значение
        if (empty($id)) {
            return null;
        }

        // конверируем в int
        if (is_string($id)) {
            $id = trim($id);
            if ($id === '') {
                return null;
            }

            if (! ctype_digit($id)) {
                throw new Exception('Недопустимые символы');
            }

            $id = (int)$id;
        } elseif (! is_int($id)) {
            throw new Exception('Неизвестный тип значения' . $id);
        }

        // проверяем значение
        if (empty($id)) {
            return null;
        }

        if ($id < 1) {
            throw new Exception('Значение не может быть отрицательным');
        }

        return $id;
    }

    /** @noinspection ClassMethodNameMatchesFieldNameInspection */

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::isEmpty()
     */
    public function isEmpty($value)
    {
        // пропускаем обработку если только явное null
        return $value === null;
    }
}

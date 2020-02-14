<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.02.20 08:17:39
 */

declare(strict_types = 1);
namespace dicr\validate;

/**
 * Валидация ID
 * Значение - целое число > 0
 */
class IdValidator extends AbstractValidator
{
    /**
     * Фильтрует ID
     *
     * @param mixed $value
     * @param array $config
     * @return int|null
     * @throws \dicr\validate\ValidateException
     */
    public static function parse($value, array $config = null)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_scalar($value) && preg_match('~^\d+$~', (string)$value)) {
            $id = (int)$value;
            if ($id > 0) {
                return $id;
            }
        }

        throw new ValidateException('Некорректный id: ' . $value);
    }
}

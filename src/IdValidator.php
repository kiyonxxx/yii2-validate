<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:15:37
 */

declare(strict_types = 1);
namespace dicr\validate;

use function ctype_digit;
use function is_scalar;

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
     * @throws ValidateException
     */
    public static function parse($value, array $config = [])
    {
        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип значения id:');
        }

        if (! ctype_digit($value)) {
            throw new ValidateException('Некорректный формат id: ' . $value);
        }

        $value = (int)$value;
        return empty($value) ? null : $value;
    }

    /**
     * Конвертирует в строку.
     *
     * @param int|string|null $value
     * @param array $config
     * @return string
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        $value = self::parse($value);
        return empty($value) ? '' : (string)$value;
    }
}

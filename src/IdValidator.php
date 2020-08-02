<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 02.08.20 21:52:43
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
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
    public static function parse($value, array $config = []) : ?int
    {
        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип значения id:');
        }

        if (! ctype_digit((string)$value)) {
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
     */
    public static function format($value, array $config = []) : string
    {
        try {
            $value = self::parse($value);
            return empty($value) ? '' : (string)$value;
        } catch (Throwable $ex) {
            return (string)$value;
        }
    }
}

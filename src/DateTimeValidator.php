<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 20.07.20 03:49:46
 */

declare(strict_types = 1);
namespace dicr\validate;

use function date;
use function gettype;
use function is_numeric;
use function is_string;
use function strtotime;

/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формате Y-m-d\TH:i:s
 */
class DateTimeValidator extends AbstractValidator
{
    /** @var string default date time format */
    public const FORMAT_DEFAULT = 'Y-m-d H:i:s';

    /**
     * Конвертирует дату/время в число.
     *
     * @param string|int|null $value
     * @return int
     * @throws ValidateException
     */
    private static function timestamp($value)
    {
        // пустые значения
        if (empty($value)) {
            return 0;
        }

        // числовое значение
        if (is_numeric($value)) {
            $time = (int)$value;

            if ($time < 0) {
                throw new ValidateException('Отрицательное значение времени даты');
            }

            return $time;
        }

        // строковое значение
        if (is_string($value)) {
            $time = strtotime($value);

            if ($time === false) {
                throw new ValidateException('Некорректный формат даты/времени: ' . $value);
            }

            return $time;
        }

        throw new ValidateException('Некорректный тип значения даты: ' . gettype($value));
    }

    /**
     * Парсит значение даты/времени из строки.
     *
     * @param string $value
     * @param array $config
     * @return string|null
     * @throws ValidateException
     */
    public static function parse($value, array $config = [])
    {
        // парсим в число
        $time = self::timestamp($value);

        // форматируем в datetime
        return empty($time) ? null : date('Y-m-d H:i:s', $time);
    }

    /**
     * Форматирует значение в строку.
     *
     * @param string|int $value
     * @param array $config
     * - string $format php-формат date
     * @return string
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        $format = $config['format'] ?? self::FORMAT_DEFAULT;

        // парсим значение в datetime
        $time = self::timestamp($value);

        return empty($time) ? '' : date($format, $value);
    }
}

<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 02.08.20 20:46:09
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
use function gettype;
use function in_array;
use function is_bool;
use function is_scalar;
use function strtotime;

/**
 * Валидатор данных типа флаг со значениями null/datetime, который конвертирует значения типа true в текущее
 * дату/время с заданным форматом.
 *
 * Принимаемые значение:
 * false (null, 0, false, "", "false", "", "no", "off") => null,
 * true (1, true, "true", "yes", "on") => date(формат, time())
 * "Y-m-d H:i:s", int => date(формат, значение)
 *
 * Используется с полями типа DATETIME/null, например в поле типа disabled, published, ....
 */
class TimeFlagValidator extends AbstractValidator
{
    /** @var string формат даты */
    public $format = 'Y-m-d H:i:s';

    /**
     * Парсит значение флага даты
     *
     * @param mixed $value
     * - false, 0, 'false', 'no', 'off' - null
     * - true, 1, 'true', 'yes', 'on' - current date
     * - int - unix timestamp
     * - string - date string
     *
     * @param array $config
     * - format - формат даты и времени (по-умолчанию Y-m-d H:i:s)
     * @return string|null значение в виде даты
     * @throws ValidateException
     */
    public static function parse($value, array $config = []) : ?string
    {
        $format = $config['format'] ?? 'Y-m-d H:i:s';

        // empty
        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип значения даты/времени: ' . gettype($value));
        }

        // boolean
        if (is_bool($value)) {
            return $value ? date($format) : null;
        }

        // конвертируем в строку
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // предопределенные значения
        if (in_array($value, ['0', 'no', 'false', 'off'], true)) {
            return null;
        }

        if (in_array($value, ['1', 'yes', 'true', 'on'], true)) {
            return date($format);
        }

        // numeric
        if (is_numeric($value)) {
            $value = (int)$value;

            if ($value < 0) {
                throw new ValidateException('Некорректное значение флага/даты');
            }

            if (empty($value)) {
                return null;
            }

            if ($value === 1) {
                return date($format);
            }

            // как timestamp
            return date($format, $value);
        }

        // строковая дата
        $value = strtotime($value);
        if ($value === false || $value <= 0) {
            throw new ValidateException('Некорректный форматы флага/даты');
        }

        return date($format, $value);
    }

    /**
     * Форматирует в строку.
     *
     * @param int|string|null $value
     * @param array $config
     * @return string
     */
    public static function format($value, array $config = []) : string
    {
        $format = $config['format'] ?? 'Y-m-d H:i:s';

        try {
            $value = self::parse($value);
            return empty($value) ? '' : date($format, strtotime($value));
        } catch (Throwable $ex) {
            return '';
        }
    }
}

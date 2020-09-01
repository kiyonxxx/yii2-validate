<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.09.20 22:41:27
 */

declare(strict_types = 1);
namespace dicr\validate;

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
     * {@inheritDoc}
     *
     * @param bool|int|string|null $value
     * - false, 0, 'false', 'no', 'off' - null
     * - true, 1, 'true', 'yes', 'on' - current date
     * - int - unix timestamp
     * - string - date string
     *
     * @return ?string значение в виде даты
     */
    public function parseValue($value) : ?string
    {
        // empty
        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип значения даты/времени: ' . gettype($value));
        }

        // boolean
        if (is_bool($value)) {
            return $value ? date($this->format) : null;
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
            return date($this->format);
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
                return date($this->format);
            }

            // как timestamp
            return date($this->format, $value);
        }

        // строковая дата
        $value = strtotime($value);
        if ($value === false || $value <= 0) {
            throw new ValidateException('Некорректный форматы флага/даты');
        }

        return date($this->format, $value);
    }

    /**
     * @inheritDoc
     *
     * @param int|string|null $value
     */
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);

        return empty($value) ? '' : date($this->format, strtotime($value));
    }
}

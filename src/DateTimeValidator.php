<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 08.10.20 06:35:20
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

    /** @var string формат даты/времени */
    public $format = self::FORMAT_DEFAULT;

    /**
     * Конвертирует дату/время в число.
     *
     * @param string|int|null $value
     * @return int
     * @throws ValidateException
     */
    public static function timestamp($value) : int
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
     * @inheritDoc
     *
     * @param string|int|null
     * @return ?string
     */
    public function parseValue($value) : ?string
    {
        // парсим в число
        $time = self::timestamp($value);

        // форматируем в datetime
        return empty($time) ? null : date($this->format, $time);
    }

    /**
     * @inheritDoc
     * @param string|int|null $value
     */
    public function formatValue($value) : string
    {
        // парсим значение в datetime
        $time = self::timestamp($value);

        return empty($time) ? '' : date($this->format, $time);
    }
}

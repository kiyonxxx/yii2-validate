<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.09.20 22:31:14
 */

declare(strict_types = 1);
namespace dicr\validate;

use function count;
use function implode;
use function is_array;
use function is_numeric;
use function is_scalar;
use function preg_split;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор гео-координат.
 */
class GeoValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     *
     * @param string|float[]|null $val
     * @return float[]|null координаты
     */
    public function parseValue($val) : ?array
    {
        // значение строкой
        if (is_scalar($val)) {
            $val = (array)preg_split('~[\s\,]+~u', (string)$val, -1, PREG_SPLIT_NO_EMPTY);
        }

        // пустые значения
        if (empty($val)) {
            return null;
        }

        // проверяем тип и размер массива
        if (! is_array($val) || count($val) !== 2) {
            throw new ValidateException('Некорректные тип значения координат');
        }

        // проверяем каждое значение
        foreach ([0, 1] as $i) {
            if (! isset($val[$i]) || ! is_numeric($val[$i])) {
                throw new ValidateException('Некорректный тип значения координаты');
            }

            $val[$i] = (float)$val[$i];
            if ($val[$i] <= 0) {
                throw new ValidateException('Некорректное значение координаты: ' . $val[$i]);
            }
        }

        return $val;
    }

    /**
     * @inheritDoc
     * @param string|float[]|null $value
     */
    public function formatValue($value) : string
    {
        // парсим в массив
        $value = $this->parseValue($value);

        return empty($value) ? '' : implode(', ', $value);
    }
}

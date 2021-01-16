<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 16.01.21 06:33:14
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
     * @param string|float[]|null $value
     * @return float[]|null координаты
     */
    public function parseValue($value): ?array
    {
        // значение строкой
        if (is_scalar($value)) {
            $value = (array)preg_split('~[\s\,]+~u', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
        }

        // пустые значения
        if (empty($value)) {
            return null;
        }

        // проверяем тип и размер массива
        if (! is_array($value) || count($value) !== 2) {
            throw new ValidateException('Некорректные тип значения координат');
        }

        // проверяем каждое значение
        foreach ([0, 1] as $i) {
            if (! isset($value[$i]) || ! is_numeric($value[$i])) {
                throw new ValidateException('Некорректный тип значения координаты');
            }

            $value[$i] = (float)$value[$i];
            if ($value[$i] <= 0) {
                throw new ValidateException('Некорректное значение координаты: ' . $value[$i]);
            }
        }

        return $value;
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

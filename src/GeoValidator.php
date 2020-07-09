<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:20:06
 */

declare(strict_types = 1);
namespace dicr\validate;

use function count;
use function is_array;
use function is_numeric;
use function is_string;
use function preg_split;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор гео-координат.
 */
class GeoValidator extends AbstractValidator
{
    /**
     * Парсит координаты в формате через запятую
     *
     * @param string|float[]|null $val
     * @param array $config
     * @return float[]|null список email
     * @throws ValidateException
     */
    public static function parse($val, array $config = [])
    {
        // пустые значения
        if (empty($val)) {
            return null;
        }

        // значение строкой
        if (is_string($val)) {
            $val = trim($val);
            if ($val === '') {
                return null;
            }

            $val = (array)preg_split('~[\s\,]+~u', $val, - 1, PREG_SPLIT_NO_EMPTY);
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
     * Форматирует значение гео-координат в строку через запятую.
     *
     * @param string|float[]|null $value
     * @param array $config
     * @return string
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        // парсим в массив
        $value = self::parse($value);

        return empty($value) ? '' : implode(', ', $value);
    }
}

<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 08.03.20 06:33:27
 */

declare(strict_types = 1);
namespace dicr\validate;

use InvalidArgumentException;
use yii\base\Exception;
use function array_values;
use function count;
use function is_array;

/**
 * Валидатор гео-координат.
 *
 * @noinspection PhpUnused
 */
class GeoValidator extends AbstractValidator
{
    /**
     * Парсит координаты в формате через запятую
     *
     * @param string|float[] $value
     * @param array|null $config
     * @return float[]|null список email
     * @throws \yii\base\Exception
     */
    public static function parse($value, array $config = null)
    {
        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        if (! is_array($value)) {
            $matches = null;
            if (! preg_match('~^(\d+\.\d+)[\,\s]+(\d+\.\d+)$~', (string)$value, $matches)) {
                throw new Exception('Некорректноый формат гео-координат: ' . $value);
            }

            $value = [(float)$matches[1], (float)$matches[2]];
        }

        if (count($value) !== 2) {
            throw new Exception('Некорректное значение гео-координат');
        }

        $value = array_values($value);
        return [
            (float)$value[0],
            (float)$value[1]
        ];
    }

    /**
     * Форматирует значение гео-координа в строку через запятую.
     *
     * @param string|float[2] $value
     * @param array|null $config
     * @return string
     */
    public static function format($value, array $config = null)
    {
        if (! is_array($value) || count($value) !== 2) {
            throw new InvalidArgumentException('value');
        }

        return implode(', ', $value);
    }
}

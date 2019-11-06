<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;
use yii\helpers\ArrayHelper;
use function in_array;
use function is_bool;

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
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180623
 */
class TimeFlagValidator extends AbstractValidator
{
    /** @var string формат даты */
    public $format = 'Y-m-d H:i:s';

    /** @var string сообщение об ошибке */
    public $message = 'Некорретное значение флага/даты';

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
     * @return null|string значение в виде даты
     * @throws Exception
     */
    public static function parse($value, array $config = [])
    {
        $format = ArrayHelper::getValue($config, 'format', 'Y-m-d H:i:s');

        // empty
        if (empty($value)) {
            return null;
        }

        // boolean
        if (is_bool($value)) {
            return $value ? date($format) : null;
        }

        // spaces
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
                throw new Exception('Некорректное значение флага/даты');
            }

            if (empty($value)) {
                return null;
            }

            if ($value === 1) {
                return date($format);
            }

            return date($format, $value);
        }

        // сроковая дата
        $value = strtotime($value);
        if ($value <= 0) {
            throw new Exception('Некоректный форматы флага/даты');
        }

        return date($format, $value);
    }
}

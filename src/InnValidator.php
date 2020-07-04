<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 09:27:14
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;

/**
 * Валидатор ИНН
 *
 * @link https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5_%D1%87%D0%B8%D1%81%D0%BB%D0%BE#%D0%9D%D0%BE%D0%BC%D0%B5%D1%80%D0%B0_%D0%98%D0%9D%D0%9D
 * @noinspection PhpUnused
 */
class InnValidator extends AbstractValidator
{
    /**
     * Парсит значение ИНН
     *
     * @param string|int $value
     * @param array|null $config
     * @return int|null
     * @throws Exception некорректное значение
     */
    public static function parse($value, array $config = null)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $n = (string)$value;

        // 10-значный вариант для физических лиц
        if (preg_match('~^\d{10}$~um', $n)) {
            $n9 = ((2 * $n[0] + 4 * $n[1] + 10 * $n[2] + 3 * $n[3] + 5 * $n[4] + 9 * $n[5] + 4 * $n[6] + 6 * $n[7] +
                        8 * $n[8]) % 11) % 10;

            if ((int)$n[9] !== $n9) {
                throw new Exception(sprintf('Некорректное значение ИНН [%d]', $n9));
            }
        } elseif (preg_match('~^\d{12}$~um', $n)) {
            // 12-значный вариант для юридических лиц
            $n10 = ((7 * $n[0] + 2 * $n[1] + 4 * $n[2] + 10 * $n[3] + 3 * $n[4] + 5 * $n[5] + 9 * $n[6] + 4 * $n[7] +
                        6 * $n[8] + 8 * $n[9]) % 11) % 10;
            $n11 = ((3 * $n[0] + 7 * $n[1] + 2 * $n[2] + 4 * $n[3] + 10 * $n[4] + 3 * $n[5] + 5 * $n[6] + 9 * $n[7] +
                        4 * $n[8] + 6 * $n[9] + 8 * $n[10]) % 11) % 10;

            if ((int)$n[10] !== $n10 || (int)$n[11] !== $n11) {
                throw new Exception(sprintf('Некорректное значение ИНН [%d.%d]', $n10, $n11));
            }
        } else {
            throw new Exception('ИНН должен содержать 10 или 12 цифр');
        }

        return (int)$value;
    }

    /**
     * Форматирование значения в строку.
     *
     * @param int $value
     * @param array|null $config
     * @return string|void
     */
    public static function format($value, array $config = null)
    {
        return (string)$value;
    }

}

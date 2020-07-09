<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:16:24
 */

declare(strict_types = 1);
namespace dicr\validate;

use function gettype;
use function is_scalar;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_pad;
use function strlen;
use const STR_PAD_LEFT;

/**
 * Валидатор телефона.
 */
class PhoneValidator extends AbstractValidator
{
    /** @var int код страны для добавления при форматировании */
    public $country;

    /** @var int код региона для добавления при форматировании */
    public $region;

    /**
     * Парсит номер телефона
     *
     * @param $value
     * @param array $config
     * @return int|null цифры номера телефона
     * @throws ValidateException
     */
    public static function parse($value, array $config = [])
    {
        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип значения: ' . gettype($value));
        }

        $value = (string)$value;

        // ищем недопустимый символ
        $matches = null;
        if (preg_match('~([^+\d\s+\-()])~um', $value, $matches)) {
            throw new ValidateException(sprintf('Недопустимый символ "%s" в номере телефона', $matches[1]));
        }

        // очищаем лишние символы (нельзя в int, чтобы не потерять начальные нули)
        $phone = preg_replace('~[\D]+~um', '', $value);

        // пустой телефон
        if ($phone === '' || $phone === '0') {
            return null;
        }

        // проверяем длину
        $length = strlen($phone);
        if ($length < 7) {
            throw new ValidateException('Недостаточно цифр в номере телефона');
        }

        if ($length > 12) {
            throw new ValidateException('Слишком много цифр в номере телефона');
        }

        return (int)$phone;
    }

    /**
     * Форматирование телефона в строку.
     *
     * @param string|int|null $value
     * @param array $config
     * - int $country код страны
     * - int $region код региона по-умолчанию
     * @return string|void
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        $country = (int)($config['country'] ?? 0);
        $region = (int)($config['region'] ?? 0);

        $value = self::parse($value);
        if (empty($value)) {
            return '';
        }

        // дополняем строку до полного размера
        $value = str_pad((string)$value, 12, '0', STR_PAD_LEFT);

        // разбираем сроку на компоненты
        $matches = null;
        if (! preg_match('~^(\d{2})(\d{3})(\d{3})(\d{2})(\d{2})$~um', $value, $matches)) {
            throw new ValidateException('внутренняя ошибка: ' . $value);
        }

        $components = [
            'c' => (int)$matches[1], // код страны
            'r' => (int)$matches[2], // код региона
            'p' => sprintf('%s-%s-%s', $matches[3], $matches[4], $matches[5])
        ];

        // добавляем регион по-умолчанию
        if (empty($components['c'])) {
            $components['c'] = $country;
        }

        // добавляем страну по-умолчанию
        if (empty($components['r'])) {
            $components['r'] = $region;
        }

        // строим строку
        $s = $components['p'];

        // добавляем регион
        if (! empty($components['r'])) {
            $s = '(' . sprintf('%03d', $components['r']) . ') ' . $s;

            // добавляем страну
            if (! empty($components['c'])) {
                $s = '+' . $components['c'] . ' ' . $s;
            }
        }

        return $s;
    }
}

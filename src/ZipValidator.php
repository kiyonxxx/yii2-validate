<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:18:35
 */

declare(strict_types = 1);
namespace dicr\validate;

use function gettype;
use function is_scalar;

/**
 * Валидатор почтовых индексов.
 */
class ZipValidator extends AbstractValidator
{
    /** @var int кол-во цифр в индексе (Украина - 5, Россия - 6) */
    public $digits = 6;

    /**
     * Парсит значение индекса.
     *
     * @param int|string|null $value
     * @param array $config
     * - digits - кол-во цифр
     * @return int|null
     * @throws ValidateException
     */
    public static function parse($value, array $config = [])
    {
        $digits = (int)($config['digits'] ?? 6);

        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип индекса: ' . gettype($value));
        }

        if (! preg_match('~^\d{1,' . $digits . '}$~u', (string)$value)) {
            throw new ValidateException('Некорректное значение индекса: ' . $value);
        }

        $value = (int)$value;
        return $value ?: null;
    }

    /**
     * Форматирует значение индекса.
     *
     * @param int|string|null $value индекс
     * @param array $config
     * - digits - кол-во цифр
     * @return string
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        $digits = (int)($config['digits'] ?? 6);

        $value = self::parse($value, [
            'digits' => $digits
        ]);

        return empty($value) ? '' : sprintf('%0' . $digits . 'd', $value);
    }
}

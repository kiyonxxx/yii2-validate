<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 02.08.20 21:51:28
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
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
    public static function parse($value, array $config = []) : ?int
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
     */
    public static function format($value, array $config = []) : string
    {
        $digits = (int)($config['digits'] ?? 6);

        try {
            $value = self::parse($value, [
                'digits' => $digits
            ]);

            return empty($value) ? '' : sprintf('%0' . $digits . 'd', $value);
        } catch (Throwable $ex) {
            return (string)$value;
        }
    }
}

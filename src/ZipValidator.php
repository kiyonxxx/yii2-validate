<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 13:01:20
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Валидатор почтовых индексов
 *
 * @noinspection PhpUnused
 */
class ZipValidator extends AbstractValidator
{
    /** @var int кол-во цифр в индексе (Украина - 5, Россия - 6) */
    public $digits = 6;

    /**
     * Парсит значение индекса.
     *
     * @param mixed $value
     * @param array $config
     * - digits - кол-во цифр
     * @return int|null
     * @throws Exception
     */
    public static function parse($value, array $config = null)
    {
        $digits = ArrayHelper::getValue($config ?? [], 'digits', 6);

        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (! preg_match('~^\d{1,' . $digits . '}$~', $value)) {
            throw new Exception('Некорректное значение индекса');
        }

        $value = (int)$value;

        return $value ?: null;
    }

    /**
     * Форматирует значение индекса.
     *
     * @param int $value индекс
     * @param array $config
     * - digits - кол-во цифр
     * @return string
     */
    public static function format($value, array $config = null)
    {
        if (empty($value)) {
            return '';
        }

        $digits = ArrayHelper::getValue($config ?: [], 'digits', 6);

        return sprintf('%0' . $digits . 'd', $value);
    }
}

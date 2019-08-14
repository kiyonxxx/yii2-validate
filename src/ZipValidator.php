<?php
namespace dicr\validate;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Валидатор почтовых индексов
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 *
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
     * @throws Exception
     * @return int|null
     */
    public static function parse($value, array $config = [])
    {
        $digits = ArrayHelper::getValue($config, 'digits', 6);

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('~^\d{1,' . $digits . '}$~', $value)) {
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
    public static function format(int $value, array $config = [])
    {
        if (empty($value)) {
            return '';
        }

        $digits = ArrayHelper::getValue($config, 'digits', 6);

        return sprintf('%0' . $digits . 'd', $value);
    }
}

<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.04.20 14:46:53
 */

declare(strict_types = 1);
namespace dicr\validate;

use InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формате Y-m-d\TH:i:s
 *
 * @noinspection PhpUnused
 */
class DateTimeValidator extends AbstractValidator
{
    /** @var string default date time format */
    public const FORMAT_DEFAULT = 'Y-m-d H:i:s';

    /** @var string */
    public $format = self::FORMAT_DEFAULT;

    /**
     * {@inheritDoc}
     * @throws \yii\base\InvalidConfigException
     * @see \yii\validators\Validator::init()
     */
    public function init()
    {
        if (strncmp($this->format, 'php:', 4) === 0) {
            $this->format = substr($this->format, 4);
        }

        if ($this->format === '') {
            throw new InvalidConfigException('format');
        }
    }

    /**
     * Парсит значение даты/времени из строки.
     *
     * @param mixed $value
     * @param array $config
     * @return int|null
     * @throws \InvalidArgumentException
     */
    public static function parse($value, array $config = null)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $time = strtotime($value);
        if ($time <= 0) {
            throw new InvalidArgumentException('Некорректное значение даты/времени: ' . $value);
        }

        return $time;
    }

    /**
     * Форматирует значение в строку.
     *
     * @param int $value
     * @param array|null $config
     * - string $format php-формат date
     * @return string|void
     */
    public static function format($value, array $config = null)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('value: ' . $value);
        }

        $format = ArrayHelper::getValue($config ?: [], 'format', self::FORMAT_DEFAULT);
        return date($format, $value);
    }
}

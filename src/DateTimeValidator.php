<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use Exception;
use InvalidArgumentException;
use yii\base\InvalidConfigException;

/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формате Y-m-d\TH:i:s
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class DateTimeValidator extends AbstractValidator
{
    /** @var string default date time format */
    const FORMAT_DEFAULT = 'Y-m-d H:i:s';

    /** @var string */
    public $format = self::FORMAT_DEFAULT;

    /**
     * {@inheritDoc}
     * @throws \yii\base\InvalidConfigException
     * @see \yii\validators\Validator::init()
     */
    public function init()
    {
        $this->format = trim($this->format);

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
    public static function parse($value, array $config = [])
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
     * {@inheritDoc}
     * @see \yii\validators\DateValidator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        try {
            $value = self::parse($value);

            if (empty($value) && ! $this->skipOnEmpty) {
                $this->addError($model, $attribute, 'Требуется значение {attribute}');
            }

            $model->{$attribute} = date($this->format, $value);
        } catch (Exception $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}

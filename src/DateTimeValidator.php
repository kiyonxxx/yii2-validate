<?php
namespace dicr\validate;

use yii\validators\Validator;

/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формате Y-m-d\TH:i:s
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class DateTimeValidator extends Validator
{
    /**
     * Парсит значение даты/времени из строки.
     *
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return int|null
     */
    public static function parse($value)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $time = strtotime($value);
        if ($time <= 0) {
            throw new \InvalidArgumentException('Некорректное значение даты/времени: ' . $value);
        }

        return $time;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\DateValidator::validateValue()
     */
    protected function validateValue($value)
    {
        try {
            $value = static::parse($value);
        } catch (\Throwable $ex) {
            return [$ex->getMessage()];
        }

        if (empty($value) && !$this->skipOnEmpty) {
            return ['Требуется заполнить значение'];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\DateValidator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        try {
            $value = self::parse($value);

            if (empty($value) && !$this->skipOnEmpty) {
                $this->addError($model, $attribute, 'Требуется значение');
            }

            $model->$attribute = $value;
        } catch (\Exception $ex) {
            $this->addError($model, $attribute, $ex->getMessage());
        }
    }
}
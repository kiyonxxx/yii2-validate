<?php
namespace dicr\validate;

use yii\validators\DateValidator;

/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формате Y-m-d\TH:i:s
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class DateTimeValidator extends DateValidator
{
    /**
     * {@inheritDoc}
     * @see \yii\validators\DateValidator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        // конвертируем значение из "Y-m-d\TH:i:s" в "Y-m-d H:i:s"
        $matches = null;
        if (is_string($model->{$attribute}) && preg_match('~^(.+?)T(.+?)$~uism', $model->{$attribute}, $matches)) {
            $model->{$attribute} = $matches[1] . ' ' . $matches[2];
        }

        return parent::validateAttribute($model, $attribute);
    }
}
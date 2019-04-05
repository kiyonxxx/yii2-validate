<?php
namespace dicr\validate;

use yii\validators\DateValidator;

/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формат Y-m-d\TH:i:s
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
        // конвертируем значение с T в стандартное
        if (is_string($model->{$attribute})) {
            $model->{$attribute} = preg_replace('~^(.+?)T(.+?)$~uism', '${1} ${2}', $model->{$attribute});
        }

        return parent::validateAttribute($model, $attribute);
    }
}
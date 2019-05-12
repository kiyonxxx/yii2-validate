<?php
namespace dicr\validate;

use yii\validators\Validator;

/**
 * Абстрактный валидатор.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 *
 */
abstract class AbstractValidator extends Validator
{
    /**
     * Парсит значение
     *
     * @param mixed $value
     * @throws \Exception
     * @return mixed|null
     */
    public abstract function parse($value);

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        try {
            self::parse($value);
        } catch (\Throwable $ex) {
            return [ $ex->getMessage(), ['value' => $value] ];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        try {
            $model->{$attribute} = self::parse($value);
        } catch (\Throwable $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }

}
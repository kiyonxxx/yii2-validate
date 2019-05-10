<?php
namespace dicr\validate;

use yii\validators\Validator;

/**
 * Валидация ссылок на ID
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class IdValidator extends Validator
{
    /** @var string */
    public $message = 'Некорретное значение id';

    /** @var bool */
    public $skipOnEmpty = true;

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        if ($this->isEmpty($value) || empty($value)) {
            $model->{$attribute} = null;
            return;
        }

        if (!preg_match('~^\d+$~uism', ''.$value)) {
            return $this->addError($model, $attribute, $this->message);
        }

        $value = (int)$value;
        if (empty($value)) {
            $model->{$attribute} = null;
            return;
        }

        if ($value < 0) {
            return $this->addError($model, $attribute, $this->message);
        }

        $model->{$attribute} = $value;
    }
}

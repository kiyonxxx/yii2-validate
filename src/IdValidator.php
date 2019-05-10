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
            $this->addError($model, $attribute, $this->message);
        } elseif (is_numeric($value)) {
            if (!preg_match('~^\d+$~uism', ''.$value)) {
                $this->addError($model, $attribute, $this->message);
            } else {
                $value = (int)$value;
                if ($value < 1) {
                    $this->addError($model, $attribute, $this->message);
                } else {
                    $model->{$attribute} = $value;
                }
            }
        } else {
            $this->addError($model, $attribute, $this->message);
        }
    }
}

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

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};
        if ($this->isEmpty($value) || empty($value)) {
            $model->{$attribute} = null;
        } elseif (is_numeric($value)) {
            if (!preg_match('~^\d+~$', ''.$value)) {
                $this->addError($model, $attribute, $this->message);
            } else {
                $value = (int)$value;
                if ($value < 0) {
                    $this->addError($model, $attribute, $this->message);
                } elseif ($value === 0) {
                    $model->{$attribute} = null;
                } else {
                    $model->{$attribute} = $value;
                }
            }
        } else {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
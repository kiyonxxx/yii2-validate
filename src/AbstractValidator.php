<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
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
     * Парсит значение.
     *
     * @param mixed $value
     * @param array $config
     * @return mixed|null
     * @throws \Exception
     */
    abstract public static function parse($value, array $config = []);

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        // парсим значение
        try {
            $val = static::parse($value, $this->attributes);
            if ($val === null && ! $this->skipOnEmpty) {
                return ['Требуется значение значение'];
            }
        } catch (Throwable $ex) {
            return [$ex->getMessage(), ['value' => $value]];
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
            $val = static::parse($value, $this->attributes);
            if ($val === null && ! $this->skipOnEmpty) {
                $this->addError($model, $attribute, 'Требуется указаь значение');
            } else {
                $model->{$attribute} = $val;
            }
        } catch (Throwable $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}

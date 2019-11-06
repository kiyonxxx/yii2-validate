<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;
use yii\validators\Validator;
use function count;
use function is_array;

/**
 * Валидатор гео-координат.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class GeoValidator extends Validator
{
    /**
     * Парсит координаты
     *
     * @param string|null $value
     * @return float[]|null список email
     * @throws \yii\base\Exception
     */
    public static function parse($value)
    {
        $data = preg_split('~[\s,]+~um', trim($value), - 1, PREG_SPLIT_NO_EMPTY);
        if (empty($data)) {
            return null;
        }

        if (count($data) !== 2 || ! is_numeric($data[0]) || ! is_numeric($data[1])) {
            throw new Exception('некорректный формат гео-позиции');
        }

        return [(float)$data[0], (float)$data[1]];
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        try {
            static::parse(trim($value));
        } catch (Exception $ex) {
            return [$ex->getMessage()];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $coords = $model->{$attribute};

        try {
            if (! is_array($coords)) {
                $coords = static::parse($coords);
            }

            if (empty($coords)) {
                throw new Exception('пустое значение координат');
            }

            $model->{$attribute} = implode(',', $coords);
        } catch (Exception $ex) {
            $this->addError($model, $attribute, $ex->getMessage());
        }
    }
}

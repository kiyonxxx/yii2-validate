<?php
namespace dicr\validate;

use function dicr\validate\GeoValidator\parse as trim;
use yii\base\Exception;
use yii\validators\Validator;

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
     */
    public static function parse($value)
    {
        $data = preg_split('~[\s\,]+~uism', trim($value), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($data)) {
            return null;
        }

        if (count($data) != 2 || !is_numeric($data[0]) || !is_numeric($data[1])) {
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
            self::parse(trim($value));
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
        try {
            $coords = $model->{$attribute};
            if (!is_array($coords)) {
                $coords = self::parse($coords);
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
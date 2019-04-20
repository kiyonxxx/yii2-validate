<?php
namespace dicr\validate;

use yii\base\Exception;
use yii\validators\Validator;

/**
 * Валидатор телефона
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class PhoneValidator extends Validator
{
    /**
     * Парсит номер телефона
     *
     * @param string $phone номер телефона в свободном формате
     * @throws Exception
     * @return int цифры номера телефона
     */
    public static function parse(string $phone)
    {
        $phone = trim($phone);
        if ($phone === '') {
            return null;
        }

        // ищем недопустимый символ
        $matches = null;
        if (preg_match('~([^\d\s\+\-\(\)])~uism', $phone, $matches)) {
            throw new Exception(sprintf('Недопусимый символ "%s" в номере телефона', $matches[1]));
        }

        // очищаем линие символы (нельзя в int, чтобы не потерять начальные нули)
        $phone = preg_replace('~[^\d]+~uism', '', $phone);

        // проверяем длину
        $length = strlen($phone);
        if ($length < 7) {
            throw new Exception('Недостаточно цифр в номере телефона');
        } elseif ($length > 12) {
            throw new Exception('Слишком много цифр в номере телефона');
        }

        return (int)$phone;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        try {
            $value = self::parse((string)$value);
            if (empty($value)) {
                throw new Exception('Пустой номер телефона');
            }
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
            $value = self::parse((string)$model->{$attribute});
            if (empty($value)) {
                throw new Exception('Пустой номер телефона');
            }

            $model->{$attribute} = $value;
        } catch (Exception $ex) {
            $this->addError($model, $attribute, $ex->getMessage());
        }
    }
}
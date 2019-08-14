<?php
namespace dicr\validate;

use yii\base\Exception;
use yii\validators\EmailValidator;

/**
 * Валидатор E-Mail адресов в формате сроки через запятую
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class EmailsValidator extends AbstractValidator
{
    /**
     * Парсит список Email из сроки
     *
     * @param string|null $value
     * @param array $config
     * @return string[]|null список email
     */
    public static function parse($value, array $config = [])
    {
        if (!isset($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }

            $value = preg_split('~[\,\s]+~uism', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($value)) {
            throw new Exception('Некорректный тип значения');
        }

        $emailValidator = new EmailValidator([
            'checkDNS' => true,
            'enableIDN' => true
        ]);

        foreach ($value as $i => $email) {
            $email = trim($email);
            if ($email === '') {
                unset($value[$i]);
            } else {
                $error = null;
                if (!$emailValidator->validate($email, $error)) {
                    throw new Exception($error);
                }

                $value[$i] = $email;
            }
        }

        return $value ?: null;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        try {
            $value = self::parse($value);
            if ($value === null && !$this->skipOnEmpty) {
                $this->addError('Необходимо заполнить {attribute}');
            }

            $model->{$attribute} = implode(', ', $value);
        } catch (\Throwable $ex) {
            $this->addError($attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}
<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
use yii\base\Exception;
use yii\validators\EmailValidator;
use function is_array;
use function is_string;

/**
 * Валидатор E-Mail адресов в формате сроки через запятую.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class EmailsValidator extends AbstractValidator
{
    /**
     * Парсит список Email из сроки
     *
     * @param mixed $value
     * @param array $config
     * @return string[]|null список email
     * @throws \yii\base\Exception
     * @throws \yii\base\Exception
     * @throws \yii\base\Exception
     */
    public static function parse($value, array $config = [])
    {
        if (! isset($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }

            $value = preg_split('~[,\s]+~um', $value, - 1, PREG_SPLIT_NO_EMPTY);
        }

        if (! is_array($value)) {
            throw new Exception('Некорректный тип значения');
        }

        if (empty($value)) {
            return null;
        }

        $emailValidator = new EmailValidator([
            'checkDNS' => true,
            'enableIDN' => true
        ]);

        foreach ($value as $i => &$email) {
            $email = trim($email);
            if ($email === '') {
                unset($value[$i]);
            } else {
                $error = null;
                if (! $emailValidator->validate($email, $error)) {
                    throw new Exception($error);
                }
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
        try {
            $value = self::parse($model->{$attribute});
            if ($value === null && ! $this->skipOnEmpty) {
                throw new Exception('Необходимо заполнить значение');
            }

            $model->{$attribute} = implode(', ', $value);
        } catch (Throwable $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $model->{$attribute}]);
        }
    }
}

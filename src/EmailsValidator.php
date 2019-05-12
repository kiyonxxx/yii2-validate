<?php
namespace dicr\validate;

use dicr\asset\AbstractValidator;
use yii\validators\EmailValidator;

/**
 * Валидатор E-Mail адресов через запятую
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
     * @return string[] список email
     */
    public static function parse($value)
    {
        return preg_split('~[\,\s]+~uism', trim($value), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $emails = $model->{$attribute};

        if (!is_array($emails)) {
            $emails = self::parse($emails);
        }

        if (empty($emails)) {
            $this->addError($model, $attribute, 'Пустой список email-адресов');
            return false;
        }

        $emailValidator = new EmailValidator([
            'checkDNS' => true,
            'enableIDN' => true
        ]);

        foreach ($emails as $i => $email) {
            $email = trim($email);

            if (!$emailValidator->validate($email)) {
                $this->addError($model, $attribute, 'Некорректный EMail: ' . $email);
                break;
            }

            $emails[$i] = $email;
        }

        $model->{$attribute} = implode(', ', $emails);
    }
}
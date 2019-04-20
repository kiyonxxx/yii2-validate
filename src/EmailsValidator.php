<?php
namespace dicr\validate;

use yii\validators\EmailValidator;
use yii\validators\Validator;

/**
 * Валидатор E-Mail адресов через запятую
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class EmailsValidator extends Validator
{
    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $emails = $model->{$attribute};

        if (!is_array($emails)) {
            $emails = preg_split('~[\,\s]+~uism', trim($emails), -1, PREG_SPLIT_NO_EMPTY);
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
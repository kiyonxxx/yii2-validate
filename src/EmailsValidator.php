<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:14:27
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\validators\EmailValidator;
use function is_array;
use function is_scalar;

/**
 * Валидатор E-Mail адресов в формате сроки через запятую.
 */
class EmailsValidator extends AbstractValidator
{
    /**
     * Парсит список Email из сроки
     *
     * @param string|string[]|null $value
     * @param array $config
     * @return string[]|null список email
     * @throws ValidateException
     */
    public static function parse($value, array $config = [])
    {
        if (empty($value)) {
            return null;
        }

        if (is_scalar($value)) {
            $value = trim((string)$value);
            if ($value === '') {
                return null;
            }

            $value = (array)preg_split('~[,\s]+~u', $value, - 1, PREG_SPLIT_NO_EMPTY);
        }

        if (! is_array($value)) {
            throw new ValidateException('Некорректный тип значения');
        }

        if (empty($value)) {
            return null;
        }

        $emailValidator = new EmailValidator([
            'checkDNS' => true,
            'enableIDN' => true
        ]);

        foreach ($value as $i => $email) {
            $error = null;
            if (! $emailValidator->validate($email, $error)) {
                throw new ValidateException($error);
            }
        }

        return $value;
    }

    /**
     * Форматирует список email в строку.
     *
     * @param mixed $value
     * @param array $config
     * @return string|void
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        $value = self::parse($value);
        return empty($value) ? '' : implode(', ', $value);
    }
}

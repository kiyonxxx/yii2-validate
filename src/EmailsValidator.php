<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.04.20 14:49:10
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;
use yii\validators\EmailValidator;
use function is_array;
use function is_string;

/**
 * Валидатор E-Mail адресов в формате сроки через запятую.
 *
 * @noinspection PhpUnused
 */
class EmailsValidator extends AbstractValidator
{
    /**
     * Парсит список Email из сроки
     *
     * @param string|string[] $value
     * @param array $config
     * @return string[]|null список email
     * @throws \yii\base\Exception
     * @throws \yii\base\Exception
     * @throws \yii\base\Exception
     */
    public static function parse($value, array $config = null)
    {
        if ($value === null || $value === '' || $value === []) {
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
     * Форматирует список email в строку.
     *
     * @param mixed $value
     * @param array|null $config
     * @return string|void
     */
    public static function format($value, array $config = null)
    {
        return is_array($value) ? implode(', ', $value) : (string)$value;
    }
}

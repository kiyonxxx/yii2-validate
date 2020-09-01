<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.09.20 22:30:00
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\validators\EmailValidator;

use function array_filter;
use function implode;
use function is_array;
use function is_scalar;
use function preg_split;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор списка E-Mail адресов в формате сроки через пробел или запятую.
 */
class EmailsValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     *
     * @param string|string[]|null $value
     * @return string[]|null список email
     */
    public function parseValue($value) : ?array
    {
        if (is_scalar($value)) {
            $value = (array)preg_split('~[,;\s]+~u', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (empty($value)) {
            return null;
        }

        if (! is_array($value)) {
            throw new ValidateException('Некорректный тип значения');
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
     * @inheritDoc
     *
     * @param string|array|null $value
     * @return array
     */
    public function filterValue($value) : array
    {
        if (is_scalar($value)) {
            $value = preg_split('~[,;\s]+~u', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (empty($value) || ! is_array($value)) {
            return [];
        }

        return array_filter($value, static function ($val) {
            $emailValidator = new EmailValidator([
                'checkDNS' => true,
                'enableIDN' => true
            ]);

            return $emailValidator->validate($val);
        });
    }

    /**
     * @inheritDoc
     *
     * @param array|string|null $value
     */
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);

        return empty($value) ? '' : implode(', ', $value);
    }
}

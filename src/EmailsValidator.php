<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.09.20 23:45:22
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\validators\EmailValidator;

use function implode;
use function is_string;
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
        if ($value === null || $value === '' || $value === 0 || $value === '0' || $value === []) {
            return null;
        }

        if (is_string($value)) {
            $value = (array)preg_split('~[,;\s]+~u', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $ret = [];
        $emailValidator = $this->emailValidator();

        foreach ((array)$value as $val) {
            $error = null;
            if ($emailValidator->validate($val, $error)) {
                $ret[] = $val;
            } else {
                throw new ValidateException($error);
            }
        }

        return $ret ?: null;
    }

    /**
     * @inheritDoc
     *
     * @param string|array|null $value
     * @return array
     */
    public function filterValue($value) : array
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0' || $value === []) {
            return [];
        }

        if (is_string($value)) {
            $value = (array)preg_split('~[,;\s]+~u', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $ret = [];
        $emailValidator = $this->emailValidator();

        foreach ((array)$value as $val) {
            if ($emailValidator->validate($val, $error)) {
                $ret[] = $val;
            }
        }

        return $ret;
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

    /** @var EmailValidator */
    private $_emailValidator;

    /**
     * Валидатор E-mail
     */
    private function emailValidator() : EmailValidator
    {
        if (! isset($this->_emailValidator)) {
            $this->_emailValidator = new EmailValidator([
                'skipOnEmpty' => true,
                'checkDNS' => true,
                'enableIDN' => true
            ]);
        }

        return $this->_emailValidator;
    }
}

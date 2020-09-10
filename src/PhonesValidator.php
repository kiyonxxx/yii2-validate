<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 10.09.20 23:40:00
 */

declare(strict_types = 1);
namespace dicr\validate;

use function implode;
use function is_string;
use function preg_split;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор массива телефонов.
 */
class PhonesValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     * @param string|array|null $value
     * @return int[]|string[]|null
     */
    public function parseValue($value) : ?array
    {
        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        if (is_string($value)) {
            $value = (array)preg_split('~[\s,;]+~u', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $ret = [];
        $phoneValidator = $this->phoneValidator();

        foreach ((array)$value as $val) {
            $phone = $phoneValidator->parseValue($val);
            if ($phone !== null) {
                $ret[] = $phone;
            }
        }

        return $ret ?: null;
    }

    /**
     * @inheritDoc
     * @param string|int[]|string[]|null $value
     * @return int[]|string[]
     */
    public function filterValue($value) : array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if (is_string($value)) {
            $value = (array)preg_split('~[\s,;]+~u', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        $ret = [];
        $phoneValidator = $this->phoneValidator();

        foreach ((array)$value as $val) {
            try {
                $phone = $phoneValidator->parseValue($val);
                if ($phone !== null) {
                    $ret[] = $phone;
                }
            } catch (ValidateException $ex) {
                // skip it's ok
            }
        }

        return $ret;
    }

    /**
     * @inheritDoc
     * @param string|int[]|string[]|null $value
     * @return string
     */
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);

        return $value === null ? '' : implode(', ', $value);
    }

    /**
     * @inheritDoc
     * @param string|int[]|string[]|null $value
     * @return string
     */
    public function formatValueSilent($value, string $error = '') : string
    {
        $value = $this->filterValue($value);

        return $value === null ? '' : implode(', ', $value);
    }

    /** @var PhoneValidator */
    private $_phoneValidator;

    /**
     * Валидатор телефона.
     *
     * @return PhoneValidator
     */
    private function phoneValidator() : PhoneValidator
    {
        if (! isset($this->_phoneValidator)) {
            $this->_phoneValidator = new PhoneValidator([
                'formatOnValidate' => $this->formatOnValidate,
                'skipOnEmpty' => true
            ]);
        }

        return $this->_phoneValidator;
    }
}

<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.09.20 22:32:13
 */

declare(strict_types = 1);
namespace dicr\validate;

use function array_unique;
use function gettype;
use function implode;
use function is_array;
use function is_scalar;
use function preg_split;
use function sort;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор массива ID.
 */
class IdsValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     *
     * @param int[]|string|null $value
     * @return int[]|null если пустой то null
     */
    public function parseValue($value) : ?array
    {
        // строка через пробелы или запятые
        if (is_scalar($value)) {
            $value = (array)preg_split('~[\s,;]+~u', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (empty($value)) {
            return null;
        }

        // проверяем массив
        if (! is_array($value)) {
            throw new ValidateException('Некорректный тип значения: ' . gettype($value));
        }

        $idValidator = new IdValidator();

        foreach ($value as $i => &$val) {
            $val = $idValidator->parseValue($val);

            if (empty($val)) {
                throw new ValidateException('Пустое значение id');
            }
        }

        unset($val);

        sort($value);

        return empty($value) ? null : array_unique($value);
    }

    /**
     * @inheritDoc
     *
     * @param int[]|string|null $value
     * @return int[] массив id
     */
    public function filterValue($value) : array
    {
        // строка через пробелы или запятые
        if (is_scalar($value)) {
            $value = (array)preg_split('~[\s,;]+~u', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
        }

        // проверяем массив
        if (empty($value) || ! is_array($value)) {
            return [];
        }

        $idValidator = new IdValidator();

        foreach ($value as $i => &$val) {
            try {
                $val = $idValidator->parseValue($val);
            } catch (ValidateException $ex) {
                $val = null;
            }

            if (empty($val) || $val < 0) {
                unset($value[$i]);
            }
        }

        unset($val);

        sort($value);

        return array_unique($value);
    }

    /**
     * @inheritDoc
     *
     * @param int[]|string|null $value
     */
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);

        return empty($value) ? '' : implode(',', $value);
    }
}

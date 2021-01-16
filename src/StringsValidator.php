<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 16.01.21 06:31:44
 */

declare(strict_types = 1);
namespace dicr\validate;

use function array_filter;
use function array_map;
use function array_unique;
use function is_array;
use function is_scalar;
use function preg_split;

use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор массива строк string[] с форматированием в "string,string,string"
 */
class StringsValidator extends AbstractValidator
{
    /** @var string разделитель для парсинга строки */
    public $separator = '~[\s\,]+~u';

    /** @var string разделитель для форматирования в строку */
    public $glue = ', ';

    /**
     * @inheritDoc
     * @param string[]|string|null $value
     * @return string[]|null
     * @throws ValidateException
     */
    public function parseValue($value): ?array
    {
        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        if (is_scalar($value)) {
            $value = preg_split($this->separator, $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (! is_array($value)) {
            throw new ValidateException('значение должно быть массивом');
        }

        $value = array_filter(
            array_map(static fn($val): string => (string)$val, $value),
            static fn(string $val): bool => $val !== ''
        );

        if (empty($value)) {
            return null;
        }

        sort($value);

        return array_unique($value);
    }

    /**
     * @inheritDoc
     * @param string[]|string|null $value
     * @return string
     * @throws ValidateException
     */
    public function formatValue($value): string
    {
        $value = $this->parseValue($value);

        return $value === null ? '' : implode($this->separator, $value);
    }
}

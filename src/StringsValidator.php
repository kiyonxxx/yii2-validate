<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 03.12.20 20:17:52
 */

declare(strict_types = 1);
namespace dicr\validate;

use function array_filter;
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
    public function parseValue($value) : ?array
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

        $value = array_map(static function ($val) : string {
            return (string)$val;
        }, $value);

        $value = array_filter($value, static function (string $val) : bool {
            return $val !== '';
        });

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
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);

        return $value === null ? '' : implode($this->separator, $value);
    }
}

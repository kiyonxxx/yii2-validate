<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 03.12.20 20:07:50
 */

declare(strict_types = 1);
namespace dicr\validate;

use function array_filter;
use function array_unique;
use function is_array;
use function is_scalar;

/**
 * Валидатор массива строк string[] с форматированием в "string,string,string"
 */
class StringsValidator extends AbstractValidator
{
    /** @var string разделитель строк при форматировании и парсинге */
    public $separator = ', ';

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
            $value = (array)explode($this->separator, $value);
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

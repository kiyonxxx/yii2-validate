<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:20:06
 */

declare(strict_types = 1);
namespace dicr\validate;

use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function gettype;
use function implode;
use function is_array;
use function is_scalar;
use function preg_split;
use function sort;
use function trim;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Валидатор массива ID.
 */
class IdsValidator extends AbstractValidator
{
    /**
     * Парсит массив id
     *
     * @param int[]|string|null $value
     * @param array $config
     * @return int[]|null если пустой то null
     * @throws ValidateException
     */
    public static function parse($value, array $config = [])
    {
        if (empty($value)) {
            return null;
        }

        // строка через пробелы или запятые
        if (is_scalar($value)) {
            $value = (array)preg_split('~[\s\,]+~u', trim((string)$value));
        }

        // проверяем массив
        if (! is_array($value)) {
            throw new ValidateException('Некорректный тип значения: ' . gettype($value));
        }

        // обходим значения
        $value = array_map(static function($id) {
            $id = IdValidator::parse($id);

            if (empty($id)) {
                throw new ValidateException('Пустое значение id');
            }

            return $id;
        }, array_values($value));

        sort($value);
        return $value ? array_unique($value) : null;
    }

    /**
     * Фильтрует массив id, удаляя некорректные значения.
     *
     * @param int[]|string|null $value
     * @param array $config
     * @return int[] массив id или null
     */
    public static function filter($value, array $config = [])
    {
        if (empty($value)) {
            return [];
        }

        // строка через пробелы или запятые
        if (is_scalar($value)) {
            $value = (array)preg_split('~[\s\,]+~u', trim((string)$value), - 1, PREG_SPLIT_NO_EMPTY);
        }

        // проверяем массив
        if (! is_array($value)) {
            return [];
        }

        // обходим значения
        $value = array_map(static function($id) {
            try {
                $id = IdValidator::parse($id);
            } /** @noinspection BadExceptionsProcessingInspection */
            catch (ValidateException $ex) {
                $id = 0;
            }

            return $id;
        }, array_values($value));

        // фильтруем пустые значения
        $value = array_filter($value, static function($id) {
            return $id > 0;
        });

        sort($value);
        return array_unique($value);
    }

    /**
     * Конвертирует в строку.
     *
     * @param int[]|string $value
     * @param array $config
     * @return string
     * @throws ValidateException
     */
    public static function format($value, array $config = [])
    {
        return implode(', ', self::parse($value));
    }
}

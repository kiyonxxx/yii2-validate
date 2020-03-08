<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 08.03.20 06:22:47
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
use yii\base\Exception;
use function gettype;
use function is_array;

/**
 * Валидатор массива ID.
 *
 * @noinspection PhpUnused
 */
class IdsValidator extends AbstractValidator
{
    /**
     * Парсит массив id
     *
     * @param mixed $value
     * @param array $config
     * @return int[]|null если пустой то null
     * @throws \yii\base\Exception
     */
    public static function parse($value, array $config = null)
    {
        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        if (! is_array($value)) {
            throw new Exception('Некорректный тип значения: ' . gettype($value));
        }

        foreach ($value as &$id) {
            $id = IdValidator::parse($id);
            if ($id === null) {
                throw new ValidateException('Некорректное значение id');
            }
        }

        return $value ?: null;
    }

    /**
     * Фильтрует массив id, удаляя некорретные значения.
     *
     * @param mixed $value
     * @param array $config
     * @return int[]|null массив id или null
     */
    public static function filter($value, array $config = null)
    {
        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        if (! is_array($value)) {
            $value = [$value];
        }

        $ids = [];
        foreach ($value as $id) {
            try {
                $id = IdValidator::parse($id);
            } /** @noinspection BadExceptionsProcessingInspection */
            catch (Throwable $ex) {
                $id = null;
            }

            if ($id !== null) {
                $ids[] = $id;
            }
        }

        return $ids ?: null;
    }

    /**
     * Конвертирует в строку.
     *
     * @param int[]|string $value
     * @param array|null $config
     * @return string
     */
    public static function format($value, array $config = null)
    {
        return is_array($value) ? implode(',', $value) : (string)$value;
    }
}

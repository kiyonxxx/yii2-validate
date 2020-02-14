<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.02.20 08:30:23
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
use yii\base\Exception;
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
            throw new Exception('некорректный тип');
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

        foreach ($value as $i => &$id) {
            try {
                $id = IdValidator::parse($id);
            } /** @noinspection BadExceptionsProcessingInspection */
            catch (Throwable $ex) {
                $id = null;
            }

            if ($id === null) {
                unset($value[$i]);
            }
        }

        return $value ?: null;
    }
}

<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 09.07.20 14:30:11
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\validators\Validator;

/**
 * Абстрактный валидатор.
 */
abstract class AbstractValidator extends Validator
{
    /**
     * Парсит значение, приводя к типу.
     *
     * @param mixed $value значение
     * @param array $config
     * @return mixed|null приведенное к типу значение или null если пустое
     * @throws ValidateException значение некорректное
     */
    abstract public static function parse($value, array $config = []);

    /**
     * Фильтрует значение, приводя к типу (отбрасывая некорректные)
     *
     * @param mixed $value
     * @param array $config
     * @return mixed приведенное к типу значение
     */
    public static function filter($value, array $config = [])
    {
        try {
            return static::parse($value, $config);
        } /** @noinspection BadExceptionsProcessingInspection */
        catch (ValidateException $ex) {
            return null;
        }
    }

    /**
     * Форматирует значение.
     *
     * @param mixed $value тип значения зависит от валидатора и должен быть указан в реализуемом классе
     * @param array $config параметры форматирования
     * @return string
     * @throws ValidateException значение некорректное
     */
    abstract public static function format($value, array $config = []);

    /**
     * @inheritDoc
     */
    protected function validateValue($value)
    {
        // парсим значение
        try {
            $val = static::parse($value);
            if ($val === null && ! $this->skipOnEmpty) {
                return ['Требуется значение'];
            }
        } catch (ValidateException $ex) {
            return [$ex->getMessage()];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        try {
            $val = static::parse($value, $this->attributes);
            if ($val === null && ! $this->skipOnEmpty) {
                throw new ValidateException('Требуется значение');
            }

            $model->{$attribute} = $val;
        } catch (ValidateException $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}

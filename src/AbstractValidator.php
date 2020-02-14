<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.02.20 08:18:23
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
use yii\validators\Validator;

/**
 * Абстрактный валидатор.
 */
abstract class AbstractValidator extends Validator
{
    /** @var bool допускать пустые значения */
    public $skipOnEmpty = false;

    /**
     * Парсит значение, приводя к типу
     *
     * @param mixed $value значение
     * @param array $config
     * @return mixed|null приведенное к типу значение или null если пустое
     * @throws \dicr\validate\ValidateException значение некорректное
     */
    abstract public static function parse($value, array $config = null);

    /**
     * Фильтрует значение, приводя к типу.
     *
     * @param mixed $value
     * @param array $config
     * @return mixed|null приведенное к типу значение или null, если не корректное
     */
    public static function filter($value, array $config = null)
    {
        try {
            return static::parse($value, $config);
        } /** @noinspection BadExceptionsProcessingInspection */
        catch (Throwable $ex) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        // парсим значение
        try {
            $val = static::parse($value, $this->attributes);
            if ($val === null && ! $this->skipOnEmpty) {
                return ['Требуется значение значение'];
            }
        } catch (Throwable $ex) {
            return [$ex->getMessage()];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        try {
            $val = static::parse($value, $this->attributes);
            if ($val === null && ! $this->skipOnEmpty) {
                throw new ValidateException('Требуется указать значение');
            }

            $model->{$attribute} = $val;
        } catch (Throwable $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}

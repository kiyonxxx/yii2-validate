<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 23.01.21 03:20:37
 */

declare(strict_types = 1);
namespace dicr\validate;

use Throwable;
use yii\validators\Validator;

/**
 * Абстрактный валидатор. Расширенная версия валидатора Yii,
 * добавляющая методы `parseValue`, `filterValue`, `formatValue` и статические методы.
 */
abstract class AbstractValidator extends Validator
{
    /** @var bool форматирует при валидации */
    public $formatOnValidate = false;

    /**
     * @inheritDoc
     */
    public function init() : void
    {
        parent::init();

        $this->formatOnValidate = (bool)$this->formatOnValidate;
    }

    /**
     * Парсит значение, приводя к типу.
     * Если значение пустое, необходимо вернуть null, если некорректное - исключение.
     *
     * @param mixed $value значение
     * @return mixed|null приведенное к типу значение или null если пустое
     * @throws ValidateException значение некорректное
     */
    abstract public function parseValue($value);

    /**
     * Парсит значение, приводя к типу (статический метод).
     *
     * @param mixed $value значение
     * @param array $config конфиг валидатора
     * @return mixed|null приведенное к типу значение или null если пустое
     * @throws ValidateException значение некорректное
     */
    public static function parse($value, array $config = [])
    {
        return (new static($config))->parseValue($value);
    }

    /**
     * Фильтрует значение, возвращая приведенный тип или null если некорректное (без выброса исключения).
     *
     * @param mixed $value значение
     * @return mixed|null приведенное к типу значение или null если пустое или некорректное
     */
    public function filterValue($value)
    {
        try {
            return $this->parseValue($value);
        } /** @noinspection BadExceptionsProcessingInspection */
        catch (ValidateException $ex) {
            // it's Ok
            return null;
        }
    }

    /**
     * Фильтрация значения (статический метод).
     * В отличие от `parse` в случае некорректного значения возвращает null.
     *
     * @param mixed $value значение
     * @param array $config конфиг валидатора
     * @return mixed|null приведенное к типу значение или null, если значение пустое или некорректное
     */
    public static function filter($value, array $config = [])
    {
        return (new static($config))->filterValue($value);
    }

    /**
     * Форматирует значение.
     *
     * @param mixed $value тип значения зависит от валидатора и должен быть указан в реализуемом классе
     * @return string строковое представление
     * @throws ValidateException значение некорректное
     */
    public function formatValue($value) : string
    {
        return (string)$this->parseValue($value);
    }

    /**
     * Форматирует значение (статический метод).
     *
     * @param $value
     * @param array $config конфиг валидатора
     * @return string строковое представление значения
     * @throws ValidateException значение некорректное
     */
    public static function format($value, array $config = []) : string
    {
        return (new static($config))->formatValue($value);
    }

    /**
     * "Тихое форматирование (без исключения).
     *
     * @param mixed $value значение
     * @param string $error значение при ошибке
     * @return string строковое представление
     */
    public function formatValueSilent($value, string $error = '') : string
    {
        try {
            return $this->formatValue($value);
        } catch (Throwable $ex) {
            return $error;
        }
    }

    /**
     * Форматирует значение без исключения об ошибке.
     *
     * @param mixed $value значение
     * @param string $error строковое представление
     * @param array $config конфиг валидатора
     * @return string строковое представление
     */
    public static function formatSilent($value, string $error = '', array $config = []) : string
    {
        return (new static($config))->formatValueSilent($value, $error);
    }

    /**
     * @inheritDoc
     */
    protected function validateValue($value) : ?array
    {
        try {
            // парсим значение
            $val = $this->parseValue($value);

            // проверяем пустое
            if ($val === null && ! $this->skipOnEmpty) {
                return ['Требуется значение', []];
            }
        } catch (ValidateException $ex) {
            return [$ex->getMessage(), []];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute) : void
    {
        // получаем значение
        $value = $model->{$attribute};

        try {
            // парсим значение
            $val = $this->parseValue($value);

            // проверяем на пустое
            if ($val === null && ! $this->skipOnEmpty) {
                throw new ValidateException('Требуется значение');
            }

            // форматирование при валидации
            // форматируем при валидации
            if ($this->formatOnValidate) {
                $val = $this->formatValue($val);
            }

            // сохраняем значение
            $model->{$attribute} = $val;
        } catch (ValidateException $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}

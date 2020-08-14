<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.08.20 10:47:04
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;

use function get_class;
use function gettype;
use function implode;
use function is_array;
use function is_scalar;
use function is_string;
use function reset;

/**
 * Ошибка валидации модели.
 */
class ValidateException extends Exception
{
    /** @var Model|null */
    protected $model;

    /**
     * Конструктор.
     *
     * @param Model|array|string $value модель с ошибками, список ошибок или строка ошибки
     */
    public function __construct($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('value');
        }

        if (is_string($value)) {
            $message = $value;
        } elseif (is_array($value)) {
            $message = self::messageFromArray($value);
        } elseif ($value instanceof Model) {
            /** @var Model $value */
            $this->model = $value;
            $message = self::messageFromModel($value);
        } else {
            throw new InvalidArgumentException('value');
        }

        parent::__construct($message);
    }

    /**
     * Формирует сообщение об ошибке из ошибок модели.
     *
     * @param Model $model
     * @return string
     */
    public static function messageFromModel(Model $model) : string
    {
        $message = get_class($model);

        if ($model->hasErrors()) {
            $errors = [];
            foreach ($model->firstErrors as $attribute => $error) {
                $value = $model->{$attribute};
                $errors[] = $attribute . ': ' . $error . ': ' .
                    (is_scalar($value) ? $value : gettype($value));
            }

            $message .= ': ' . implode('; ', $errors);
        }

        return $message;
    }

    /**
     * Формирует сообщение об ошибке из списка ошибок.
     *
     * @param array $errors массив ошибок string[], либо attribute => string|string[]
     * @return string
     */
    public static function messageFromArray(array $errors) : string
    {
        $parts = [];

        if (isset($errors[0])) {
            // список ошибок без аттрибутов
            $parts = $errors;
        } else {
            // список ошибок в виде attribute => string|string[]
            foreach ($errors as $attribute => $error) {
                if (! empty($error)) {
                    $error = (array)$error;
                    $parts[] = $attribute . ': ' . reset($error);
                }
            }
        }

        return implode('; ', $parts);
    }

    /**
     * Возвращает модель.
     *
     * @return Model|null
     */
    public function getModel() : ?Model
    {
        return $this->model;
    }
}

<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use InvalidArgumentException;
use yii\base\Exception;
use yii\base\Model;
use function get_class;

/**
 * Ошибка валидации модели.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180504
 */
class ValidateException extends Exception
{
    /** @var \yii\base\Model */
    protected $model;

    /**
     * Конструктор.
     *
     * @param Model $model
     * @param string|null $message
     */
    public function __construct(Model $model, string $message = null)
    {
        if (empty($model)) {
            throw new InvalidArgumentException('empty model');
        }
        $this->model = $model;

        $msg = ($message ?: get_class($model)) . ': ' . implode('; ', $model->getErrorSummary(false));

        parent::__construct($msg);
    }

    /**
     * Возвращает модель.
     *
     * @return \yii\base\Model
     */
    public function getModel()
    {
        return $this->model;
    }
}

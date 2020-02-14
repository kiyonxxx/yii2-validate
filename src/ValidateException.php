<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.02.20 07:56:38
 */

declare(strict_types = 1);

namespace dicr\validate;

use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Ошибка валидации модели.
 *
 * @noinspection PhpUnused
 */
class ValidateException extends Exception
{
    /** @var \yii\base\Model */
    protected $model;

    /**
     * Конструктор.
     *
     * @param Model|string $value
     */
    public function __construct($value)
    {
        if ($value instanceof Model) {
            $this->model = $value;
            $msg = implode('; ', $value->getErrorSummary(false));
        } elseif (is_scalar($value)) {
            $msg = (string)$value;
        } else {
            throw new InvalidArgumentException('value');
        }

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

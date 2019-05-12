<?php
namespace dicr\validate;

use yii\base\Exception;

/**
 * Валидация ID
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class IdValidator extends AbstractValidator
{
    /** @var bool */
    public $skipOnEmpty = true;

    /**
     * Парсит ID.
     *
     * @param int|string|null $id
     * @throws Exception
     * @return int|null
     */
    public static function parse($id)
    {
        if (is_null($id)) {
            return null;
        }

        if (is_string($id)) {
            $id = trim($id);
            if ($id === '') {
                return null;
            }

            if (!ctype_digit($id)) {
                throw new Exception('Недопустимые символы');
            }

            $id = (int)$id;
        }

        if (!is_int($id)) {
            throw new Exception('Неизвестный тип значения' . $id);
        }

        if ($id < 0) {
            throw new Exception('Значение не может быть отрицательным');
        }

        if (empty($id)) {
            return null;
        }

        return $id;
    }
}

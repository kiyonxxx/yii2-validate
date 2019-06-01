<?php
namespace dicr\validate;

use yii\base\Exception;

/**
 * Валидация ID
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class IdValidator extends \dicr\validate\AbstractValidator
{
    /** @var bool */
    public $skipOnEmpty = false;

    /**
     * Парсит ID.
     *
     * @param int|string|null $id
     * @throws Exception
     * @return int|null
     */
    public static function parse($id)
    {
        // если пустое значение
        if (empty($id)) {
            return null;
        }

        // конверируем в int
        if (is_string($id)) {
            $id = trim($id);
            if ($id === '') {
                return null;
            }

            if (!ctype_digit($id)) {
                throw new Exception('Недопустимые символы');
            }

            $id = (int)$id;
        } elseif (!is_int($id)) {
            throw new Exception('Неизвестный тип значения' . $id);
        }

        // проверяем значение
        if (empty($id)) {
            return null;
        } elseif ($id < 1) {
            throw new Exception('Значение не может быть отрицательным');
        }

        return $id;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::isEmpty()
     */
    public function isEmpty($value)
    {
        // пропускаем обработку если только явное null
        return $value === null;
    }
}

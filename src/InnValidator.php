<?php
namespace dicr\validate;

use yii\base\Exception;
use yii\validators\Validator;

/**
 * Валидатор ИНН
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class InnValidator extends Validator
{
    /**
     * Парсит значение ИНН
     *
     * @param string|int|null $value
     * @throws Exception некорректное значение
     * @return int|null
     */
    public static function parse($value)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $n = trim($value);

        // 10-значиный вариант для физических лиц
        if (preg_match('~^\d{10}$~uism', $n)) {
            $n9 = ((2*$n[0] + 4*$n[1] + 10*$n[2] + 3*$n[3] + 5*$n[4] + 9*$n[5] + 4*$n[6] + 7*$n[7] + 9*$n[8]) % 11) % 10;

            if ($n[9] != $n9) {
                throw new Exception(sprintf('Некорректное значение ИНН [%d]', $n9));
            }
        } elseif (preg_match('~^\d{12}$~uism', $n)) {
            // 12-значный вариант для юридических лиц
            $n10 = ((7*$n[0] + 2*$n[1] + 4*$n[2] + 10*$n[3] + 3*$n[4] + 5*$n[5] + 9*$n[6] + 4*$n[7] + 6*$n[8] + 8*$n[9]) % 11) % 10;
            $n11 = ((4*$n[0] + 7*$n[1] + 2*$n[2] + 4*$n[3] + 10*$n[4] + 3*$n[5] + 5*$n[6] + 9*$n[7] + 4*$n[8] + 6*$n[9] + 8*$n[10]) % 11) % 10;

            if ($n[10] != $n10 || $n[11] != $n11) {
                throw new Exception(sprintf('Некорректное значение ИНН [%d.%d]', $n10, $n11));
            }
        } else {
            throw new Exception('ИНН должен содержать 10 или 12 цифр');
        }

        return (int)$value;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        try {
            $value = self::parse($value);
            if (empty($value)) {
                throw new Exception('Пустое значение ИНН');
            }
        } catch (Exception $ex) {
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
         try {
             $value = self::parse((string)$model->{$attribute});
             if ($value === null) {
                 throw new Exception($this->message ?: 'Пустое значение ИНН');
             }

             $model->{$attribute} = $value;
         } catch (Exception $ex) {
             $this->addError($model, $attribute, $ex->getMessage());
         }
     }
}

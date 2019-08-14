<?php
namespace dicr\validate;


/**
 * Валидатор даты/времени, с поддержкой значения элемента
 * datetime-local в формате Y-m-d\TH:i:s
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class DateTimeValidator extends AbstractValidator
{
    /** @var string */
    public $format = 'Y-m-d H:i:s';

    /**
     * Парсит значение даты/времени из строки.
     *
     * @param mixed $value
     * @param array $config
     * @throws \InvalidArgumentException
     * @return int|null
     */
    public static function parse($value, array $config = [])
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $time = strtotime($value);
        if ($time <= 0) {
            throw new \InvalidArgumentException('Некорректное значение даты/времени: ' . $value);
        }

        return $time;
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\DateValidator::validateAttribute()
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        try {
            $value = self::parse($value);

            if (empty($value) && !$this->skipOnEmpty) {
                $this->addError($model, $attribute, 'Требуется значение {attribute}');
            }

            $model->{$attribute} = date($this->format, $value);
        } catch (\Exception $ex) {
            $this->addError($model, $attribute, $ex->getMessage(), ['value' => $value]);
        }
    }
}
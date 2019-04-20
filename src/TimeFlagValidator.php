<?php
namespace dicr\validate;

use yii\validators\Validator;
use yii\base\Exception;

/**
 * Валидатор данных типа флаг со значениями null/datetime, который конвертирует значения типа true в текущее
 * дату/время с заданным форматом.
 *
 * Принимаемые значение:
 * false (null, 0, false, "", "false", "", "no", "off") => null,
 * true (1, true, "true", "yes", "on") => date(формат, time())
 * "Y-m-d H:i:s", int => date(формат, значение)
 *
 * Используется с полями типа DATETIME/null, например в поле типа disabled, published, ....
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180623
 */
class TimeFlagValidator extends Validator
{
	/** @var string формат даты */
	public $format = 'Y-m-d H:i:s';

	/** @var bool */
	public $skipOnEmpty = false;

	/** @var string сообщение об ошибке */
	public $message = 'Некорретное значение флага/даты';

	/**
	 * Парсит значение флага даты
	 *
	 * @param string $value
	 * - false, 0, 'false', 'no', 'off' - null
	 * - true, 1, 'true', 'yes', 'on' - current date
	 * - int - unix timestamp
	 * - string - date string
	 *
	 * @param string $format формат значения (даты/времени)
	 * @throws Exception
	 * @return null|string значение в виде даты
	 */
    public static function parse(string $value, string $format = 'Y-m-d H:i:s')
    {
        // empty
        if (empty($value)) {
            return null;
        }

        // boolean
        if (is_bool($value)) {
            return $value ? date($format) : null;
        }

        // spaces
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // numeric
        if (is_numeric($value)) {
			$value = (int)$value;

			if ($value < 0) {
			    throw new Exception('Некорректное значение флага/даты');
			}

			if (empty($value)) {
			    return null;
			}

			if ($value == 1) {
			    return date($format);
			}

			return date($format, $value);
        }

        // string
    	if (in_array($value, ['0', 'no', 'false', 'off'])) {
    	    return null;
    	}

    	if (in_array($value, ['1', 'yes', 'true', 'on'])) {
    	    return date($format);
    	}

		$value = strtotime($value);
		if ($value <= 0) {
		    throw new Exception('Некоректный форматы флага/даты');
		}

		return date($format, $value);
    }

    /**
     * {@inheritDoc}
     * @see \yii\validators\Validator::validateValue()
     */
    protected function validateValue($value)
    {
        try {
            self::parse($value);
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
	        $model->{$attribute} = self::parse($model->{$attribute});
	    } catch (Exception $ex) {
	        $this->addError($model, $attribute, $this->message ?: $ex->getMessage());
	    }
	}
}

<?php 
namespace dicr\validate;

use yii\validators\Validator;

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
class TimeFlagValidator extends Validator {
	
	/** @var string формат даты */
	public $format = 'Y-m-d H:i:s';
	
	/** @var bool */
	public $skipOnEmpty = false;

	/**
	 * {@inheritDoc}
	 * @see \yii\validators\Validator::validateAttribute()
	 */	
	public function validateAttribute($model, $attribute) {
		$val = $model->{$attribute} ?? null;
		
		// null or not exists
		if (!isset($val)) return;

		if (empty($val)) {
			$model->{$attribute} = null;
		} else if (is_bool($val)) {
			$model->{$attribute} = $val ? date($this->format) : null;
		} else if (is_numeric("$val")) {
			$val = (int)$val;
			if ($val == 0) $model->{$attribute} = null;
			else if ($val == 1) $model->$attribute = date($this->format);
			else $model->{$attribute} = date($this->format, $val);
		} else if (is_string($val)) {
			$val = trim($val);
			if (in_array($val, ['', 'no', 'false', 'off'])) $model->{$attribute} = null;
			else if (in_array($val, ['yes', 'true', 'on'])) $model->{$attribute} = date($this->format);
			else {
				$tstamp = strtotime($val);
				if (empty($tstamp)) $this->addError($model, $attribute, 'Некорректное значение флага/даты: '.$val);
				else $model->{$attribute} = date($this->format, $tstamp);
			}
		} else {
			$this->addError($model, $attribute, 'Неизвестный тип значения');
		}
	}
}
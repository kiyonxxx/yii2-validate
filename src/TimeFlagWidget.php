<?php
namespace dicr\validate;

use yii\widgets\InputWidget;
use yii\helpers\Html;

/**
 * Ввиджет поля ввода datetime с типом checkbox.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180702
 */
class TimeFlagWidget extends InputWidget {

	/** @var string datetime формат */
	public $format = null;

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Widget::run()
	 */
	public function run() {
		$this->options['labelOptions'] = [
			'class' => 'control-label',
			'style' => 'font-weight: normal'
		];

		if ($this->hasModel()) {
			$val = Html::getAttributeValue($this->model, $this->attribute);
			$this->options['value'] = $val ?: date('Y-m-d H:i:s');
			$this->options['label'] = $val ? \Yii::$app->formatter->asDatetime($val, $this->format) : '';
			return Html::activeCheckbox($this->model, $this->attribute, $this->options);
		}

		$this->options['value'] = $this->value ?: date('Y-m-d H:i:s');
		$this->options['label'] = $this->value ? \Yii::$app->formatter->asDatetime($this->value, $this->format) : '';
		return Html::checkbox($this->name, $this->value, $this->options);
	}
}

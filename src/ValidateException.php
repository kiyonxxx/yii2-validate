<?php
namespace dicr\validate;

use yii\base\Exception;
use yii\base\Model;

/**
 * Ошибка валидации модели.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180504
 */
class ValidateError extends Exception {
	
	/** @var \yii\base\Model */
	protected $model;
	
	/**
	 * Конструктор.
	 *
	 * @param Model $model
	 * @throws \InvalidArgumentException
	 */
	public function __construct(Model $model, string $message=null) {
		if (empty($model)) throw new \InvalidArgumentException('empty model');
		$this->model = $model;
		
		$msg = ($message ?: get_class($model)).': '.implode('; ', $model->getErrorSummary(false));
		
		parent::__construct($msg);
	}
	
	/**
	 * Возвращает модель.
	 *
	 * @return \yii\base\Model
	 */
	public function getModel() {
		return $this->model;
	}
}
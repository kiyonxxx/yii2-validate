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
class ValidateException extends Exception {
	
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
		
		$msg = $model->getErrorSummary(false);
		if (!empty($message)) unshift($msg, $message);
		
		parent::__construct(implode(",\n", $msg));
		$this->model = $model;
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
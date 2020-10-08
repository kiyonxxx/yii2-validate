<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 08.10.20 06:38:41
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\InvalidConfigException;

use function gettype;
use function is_numeric;
use function is_scalar;

/**
 * Валидатор почтовых индексов.
 */
class ZipValidator extends AbstractValidator
{
    /** @var int кол-во цифр в индексе (Украина - 5, Россия - 6) */
    public $digits = 6;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init() : void
    {
        parent::init();

        if (! is_numeric($this->digits) || $this->digits < 1) {
            throw new InvalidConfigException($this->digits);
        }

        $this->digits = (int)$this->digits;
    }

    /**
     * @inheritDoc
     *
     * @param int|string|null $value
     * @return int|null
     */
    public function parseValue($value) : ?int
    {
        if (empty($value)) {
            return null;
        }

        if (! is_scalar($value)) {
            throw new ValidateException('Некорректный тип индекса: ' . gettype($value));
        }

        if (! preg_match('~^\d{1,' . $this->digits . '}$~u', (string)$value)) {
            throw new ValidateException('Некорректное значение индекса: ' . $value);
        }

        $value = (int)$value;

        return $value ?: null;
    }

    /**
     * @inheritDoc
     *
     * @param int|string|null $value индекс
     */
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);

        return $value === null ? '' : sprintf('%0' . $this->digits . 'd', $value);
    }
}

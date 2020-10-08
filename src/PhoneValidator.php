<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 08.10.20 06:37:22
 */

declare(strict_types = 1);
namespace dicr\validate;

use RuntimeException;
use yii\base\InvalidConfigException;

use function preg_match;
use function preg_replace;
use function sprintf;
use function str_pad;
use function strlen;

use const STR_PAD_LEFT;

/**
 * Валидатор телефона.
 */
class PhoneValidator extends AbstractValidator
{
    /** @var int код страны для добавления при форматировании */
    public $country;

    /** @var int код региона для добавления при форматировании */
    public $region;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init() : void
    {
        parent::init();

        if (empty($this->country)) {
            $this->country = null;
        } else {
            if (! preg_match('~^\d{1,2}$~', (string)$this->country)) {
                throw new InvalidConfigException('country: ' . $this->country);
            }

            $this->country = (int)$this->country;
        }

        if (empty($this->region)) {
            $this->region = null;
        } else {
            if (! preg_match('~^\d{1,3}$~', (string)$this->region)) {
                throw new InvalidConfigException('region: ' . $this->region);
            }

            $this->region = (int)$this->region;
        }
    }

    /**
     * @inheritDoc
     *
     * @param string|int|null $value
     * @return ?int цифры номера телефона или null если пустое
     */
    public function parseValue($value) : ?int
    {
        $value = (string)$value;
        if (empty($value)) {
            return null;
        }

        // ищем недопустимый символ
        $matches = null;
        if (preg_match('~([^+\d\s+\-()])~um', $value, $matches)) {
            throw new ValidateException(sprintf('Недопустимый символ "%s" в номере телефона', $matches[1]));
        }

        // очищаем лишние символы (нельзя в int, чтобы не потерять начальные нули)
        $phone = preg_replace('~\D+~um', '', $value);

        // пустой телефон
        if ($phone === '' || $phone === '0') {
            return null;
        }

        // проверяем длину
        $length = strlen($phone);
        if ($length < 7) {
            throw new ValidateException('Недостаточно цифр в номере телефона');
        }

        if ($length > 12) {
            throw new ValidateException('Слишком много цифр в номере телефона');
        }

        return (int)$phone;
    }

    /**
     * @inheritDoc
     * @return ?int цифры телефона или null если пустой
     */
    public static function parse($value, array $config = []) : ?int
    {
        return parent::parse($value, $config);
    }

    /**
     * @inheritDoc
     * @param string|int|null $value
     */
    public function formatValue($value) : string
    {
        $value = $this->parseValue($value);
        if ($value === null) {
            return '';
        }

        // дополняем строку до полного размера
        $value = str_pad((string)$value, 12, '0', STR_PAD_LEFT);

        // разбираем сроку на компоненты
        $matches = null;
        if (! preg_match('~^(\d{2})(\d{3})(\d{3})(\d{2})(\d{2})$~um', $value, $matches)) {
            throw new RuntimeException('внутренняя ошибка: ' . $value);
        }

        $components = [
            'c' => (int)$matches[1], // код страны
            'r' => (int)$matches[2], // код региона
            'p' => sprintf('%s-%s-%s', $matches[3], $matches[4], $matches[5])
        ];

        // добавляем страну по-умолчанию
        if (empty($components['c'])) {
            $components['c'] = $this->country;
        }

        // добавляем регион по-умолчанию
        if (empty($components['r'])) {
            $components['r'] = $this->region;
        }

        // строим строку
        $str = $components['p'];

        // добавляем регион
        if (! empty($components['r'])) {
            $str = '(' . sprintf('%03d', $components['r']) . ') ' . $str;

            // добавляем страну
            if (! empty($components['c'])) {
                $str = '+' . $components['c'] . ' ' . $str;
            }
        }

        return $str;
    }
}

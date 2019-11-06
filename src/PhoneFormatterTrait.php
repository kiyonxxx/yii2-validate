<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\validate;

use yii\base\ErrorException;

/**
 * Форматер телефона
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
trait PhoneFormatterTrait
{
    /** @var string телефонный код страны по-умолчанию */
    public $phoneCountryCode = 7;

    /** @var string телефонный код региона по-умолчанию */
    public $phoneRegionCode = 342;

    /** @var string формат телефона */
    public $phoneFormat = '+c (r) p';

    /**
     * Формаирует телефон
     *
     * @param string|null $value
     * @param string $format
     * @return string форматиррованный телефон
     * @throws \yii\base\ErrorException
     * @throws \yii\base\ErrorException
     */
    public function asPhone(string $value = null, string $format = '')
    {
        // удаляем из значения все кроме цифр
        $value = preg_replace('~[\D]~um', '', trim($value));
        if ($value === '') {
            return '';
        }

        // дополняем строку до полного размера
        $value = str_pad($value, 12, '0', STR_PAD_LEFT);

        // разбираем сроку на компоненты
        $matches = null;
        if (! preg_match('~^(\d{2})(\d{3})(\d{3})(\d{2})(\d{2})$~um', $value, $matches)) {
            throw new ErrorException('внутренняя ошибка: ' . $value);
        }

        $components = [
            'c' => (int)$matches[1],
            'r' => (int)$matches[2],
            'p' => sprintf('%s-%s-%s', $matches[3], $matches[4], $matches[5])
        ];

        if (empty($components['c'])) {
            $components['c'] = $this->phoneCountryCode;
        }

        if (empty($components['r'])) {
            $components['r'] = $this->phoneRegionCode;
        } else {
            $components['r'] = sprintf('%03d', $components['r']);
        }

        // формаируем
        return str_replace(array_keys($components), array_values($components), $format ?: $this->phoneFormat);
    }
}

<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.09.20 22:35:23
 */

declare(strict_types = 1);
namespace dicr\validate;

/**
 * Валидация ID
 * Значение - целое число > 0
 */
class IdValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     *
     * @param string|int|null $value
     * @return ?int
     */
    public function parseValue($value) : ?int
    {
        $value = (string)$value;
        if (empty($value)) {
            return null;
        }

        if (! preg_match('~^\d+$~', $value)) {
            throw new ValidateException('Некорректный формат id: ' . $value);
        }

        $value = (int)$value;

        return empty($value) ? null : $value;
    }
}

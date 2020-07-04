<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 12:59:57
 */

/** @noinspection PhpMethodMayBeStaticInspection */
declare(strict_types = 1);
namespace dicr\tests;

use dicr\validate\IdValidator;
use dicr\validate\ValidateException;
use PHPUnit\Framework\TestCase;

/**
 * IdValidator Test.
 */
class IdValidatorTest extends TestCase
{
    /**
     * @throws ValidateException
     */
    public function testEmpty()
    {
        self::assertNull(IdValidator::parse(null));
        self::assertSame('', IdValidator::format(null));

        self::assertNull(IdValidator::parse(''));
        self::assertSame('', IdValidator::format(''));

        self::assertNull(IdValidator::parse(0));
        self::assertSame('', IdValidator::format(0));
    }

    /**
     * @throws ValidateException
     */
    public function testId()
    {
        self::assertSame(1234, IdValidator::parse(1234));
        self::assertSame('1234', IdValidator::format(1234));

        self::assertSame(1234, IdValidator::parse('1234'));
        self::assertSame('1234', IdValidator::format('1234'));
    }
}

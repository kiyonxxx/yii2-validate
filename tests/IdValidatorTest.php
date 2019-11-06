<?php
/**
 * Copyright (c) 2019.
 *
 * @author Igor (Dicr) Tarasov, develop@dicr.org
 */

declare(strict_types = 1);
namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use dicr\validate\IdValidator;

/**
 * IdValidator Test.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class IdValidatorTest extends TestCase
{
    /**
     * Тесты.
     */
    public function test()
    {
        $model = new TestModel();
        $validator = new IdValidator(['skipOnEmpty' => true]);

        $model->id = null;
        $validator->validateAttribute($model, 'id');
        self::assertNull($model->id);

        $model->id = 0;
        $validator->validateAttribute($model, 'id');
        self::assertNull($model->id);

        $model->id = false;
        $validator->validateAttribute($model, 'id');
        self::assertNull($model->id);
    }

}

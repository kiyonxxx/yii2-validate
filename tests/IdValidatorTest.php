<?php
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
        self::assertSame(null, $model->id);

        $model->id = 0;
        $validator->validateAttribute($model, 'id');
        self::assertSame(null, $model->id);

        $model->id = false;
        $validator->validateAttribute($model, 'id');
        self::assertSame(null, $model->id);
    }

}

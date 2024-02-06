<?php

declare(strict_types=1);

namespace Tests\Stub;

class FatalErrorTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): void
    {
        $foo = new class () implements \JsonSerializable {};

        $message = 'This assertion should not happen: ' . json_encode($foo, JSON_THROW_ON_ERROR);

        self::assertTrue(true, $message);
    }
}

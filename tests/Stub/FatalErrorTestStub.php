<?php

declare(strict_types=1);

namespace Tests\Stub;

class FatalErrorTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): void
    {
        $foo = new class() implements \Serializable {
        };

        $message = 'This assertion should not happen: ' . json_encode($foo);

        self::assertTrue(true, $message);
    }
}

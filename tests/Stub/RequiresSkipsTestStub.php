<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\Attributes\RequiresPhp;

class RequiresSkipsTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testFoo(): void
    {
        // to avoid "no tests executed"
        $this->assertTrue(true);
    }

    #[RequiresPhp('<8.0.0')]
    public function testBrokenTest(): void
    {
        $this->fail('This test should never be executed, it requires an old PHP version');
    }
}

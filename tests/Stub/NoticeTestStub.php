<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Event\Facade;
use PHPUnit\Framework\TestCase;
use Tests\PHPUnitPolyfillTrait;

class NoticeTestStub extends TestCase
{
    use PHPUnitPolyfillTrait;

    public function testToAddDeprecation(): void
    {
        $this->assertTrue(true); // to avoid warnings

        Facade::emitter()->testTriggeredNotice(
            $this->createPHPUnitTestMethod(),
            'Test notice',
            __FILE__,
            __LINE__,
            false,
            false,
        );
    }
}

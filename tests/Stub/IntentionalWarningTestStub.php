<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Event\Facade;
use PHPUnit\Framework\TestCase;
use Tests\PHPUnitPolyfillTrait;

class IntentionalWarningTestStub extends TestCase
{
    use PHPUnitPolyfillTrait;

    public function testWithIntentionalWarning(): void
    {
        $this->assertTrue(true);

        Facade::emitter()->testTriggeredWarning($this->createPHPUnitTestMethod(), 'This is an intentional warning', __FILE__, __LINE__, false);
    }
}

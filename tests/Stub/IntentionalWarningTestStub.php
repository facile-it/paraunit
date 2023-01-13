<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Facade;
use PHPUnit\Framework\TestCase;

class IntentionalWarningTestStub extends TestCase
{
    public function testWithIntentionalWarning(): void
    {
        $this->assertTrue(true);

        Facade::emitter()->testTriggeredWarning(TestMethod::fromTestCase($this), 'This is an intentional warning', __FILE__, __LINE__);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\TestCase;

class PHPDeprecationTestStub extends TestCase
{
    public function testToAddDeprecation(): void
    {
        $this->assertTrue(true); // to avoid warnings

        EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation('test deprecation');
    }
}

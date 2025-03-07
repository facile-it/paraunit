<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Event\Emitter;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\TestCase;
use Tests\PHPUnitPolyfillTrait;

class PHPUnitDeprecationTestStub extends TestCase
{
    use PHPUnitPolyfillTrait;

    public function testToAddDeprecation(): void
    {
        $this->assertTrue(true); // to avoid warnings

        if (method_exists(Emitter::class, 'testRunnerTriggeredPhpunitDeprecation')) {
            EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation('test deprecation');
        } elseif (method_exists(Emitter::class, 'testTriggeredPhpunitDeprecation')) {
            EventFacade::emitter()->testTriggeredPhpunitDeprecation($this->createPHPUnitTestMethod(), 'test deprecation');
        }
    }
}

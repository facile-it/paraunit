<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class IntentionalRiskyTestStub extends TestCase
{
    public function testWithIntentionalRisky(): void
    {
        // no op - risky due to no assertions
    }
}

<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class IntentionalWarningTestStub extends TestCase
{
    public function testWithIntentionalWarning(): void
    {
//        $this->expectOutputRegex('/intentional warning/');
        $this->assertTrue(true);

        @trigger_error('This is an intentional warning', E_USER_WARNING);
    }
}

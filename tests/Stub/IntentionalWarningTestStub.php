<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class IntentionalWarningTestStub extends TestCase
{
    public function testWithIntentionalWarning(): void
    {
        $this->addWarning('This is an intentional warning');
    }
}

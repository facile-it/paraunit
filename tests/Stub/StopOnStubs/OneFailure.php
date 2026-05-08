<?php

declare(strict_types=1);

namespace Tests\Stub\StopOnStubs;

use PHPUnit\Framework\TestCase;

class OneFailure extends TestCase
{
    public function testFailsImmediately(): void
    {
        $this->fail('intentional failure for stop-on tests');
    }
}

<?php

declare(strict_types=1);

namespace Paraunit\Proxy\Coverage;

use PHPUnit\Runner\Version;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class FakeDriver implements Driver
{
    public function start(bool $determineUnusedAndDead = true): void
    {
        throw new \RuntimeException('This is a fake implementation, it shouldn\'t be used!');
    }

    public function stop(): array
    {
        throw new \RuntimeException('This is a fake implementation, it shouldn\'t be used!');
    }
}

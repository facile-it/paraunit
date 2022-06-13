<?php

declare(strict_types=1);

namespace Paraunit\Proxy\Coverage;

use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;

/**
 * @psalm-suppress InternalClass
 */
class FakeDriver extends Driver
{
    public function nameAndVersion(): string
    {
        return 'FakeDriver v.0.0';
    }

    public function start(bool $determineUnusedAndDead = true): void
    {
        throw new \RuntimeException('This is a fake implementation, it shouldn\'t be used!');
    }

    public function stop(): RawCodeCoverageData
    {
        throw new \RuntimeException('This is a fake implementation, it shouldn\'t be used!');
    }
}

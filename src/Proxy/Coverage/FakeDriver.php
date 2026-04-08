<?php

declare(strict_types=1);

namespace Paraunit\Proxy\Coverage;

use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\Driver\Driver;

/**
 * @psalm-suppress InternalClass
 */
class FakeDriver extends Driver
{
    public function name(): string
    {
        return 'FakeDriver';
    }

    public function version(): string
    {
        return 'v.0.0';
    }

    public function nameAndVersion(): string
    {
        return $this->name() . ' ' . $this->version();
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

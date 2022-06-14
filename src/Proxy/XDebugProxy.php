<?php

declare(strict_types=1);

namespace Paraunit\Proxy;

class XDebugProxy
{
    public function isLoaded(): bool
    {
        return extension_loaded('xdebug');
    }

    /**
     * @psalm-return 2|3
     */
    public function getMajorVersion(): int
    {
        if (! $this->isLoaded()) {
            throw new \RuntimeException('Xdebug is not loaded');
        }

        /** @var string $xdebugVersion */
        $xdebugVersion = phpversion('xdebug');

        if (version_compare('3.0', $xdebugVersion, '<=')) {
            return 3;
        }

        return 2;
    }
}

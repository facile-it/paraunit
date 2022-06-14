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
        if (version_compare('3.0', phpversion('xdebug'), '>=')) {
            return 3;
        }

        return 2;
    }
}

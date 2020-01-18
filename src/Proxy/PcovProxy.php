<?php

declare(strict_types=1);

namespace Paraunit\Proxy;

class PcovProxy
{
    public function isLoaded(): bool
    {
        return extension_loaded('pcov') && ini_get('pcov.enabled');
    }
}

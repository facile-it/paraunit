<?php

declare(strict_types=1);

namespace Paraunit\Proxy;

class PcovProxy
{
    public function isLoaded(): bool
    {
        return $this->isInstalled() && $this->isEnabled();
    }

    public function isInstalled(): bool
    {
        return extension_loaded('pcov');
    }

    public function isEnabled(): bool
    {
        return (bool)ini_get('pcov.enabled');
    }
}

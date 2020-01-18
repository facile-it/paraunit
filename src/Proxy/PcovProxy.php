<?php

namespace Paraunit\Proxy;

class PcovProxy
{
    public function isReady(): bool 
    {
        return $this->isLoaded() && $this->isEnabled();
    }

    public function isLoaded(): bool
    {
        return extension_loaded('pcov');
    }

    public function isEnabled(): bool
    {
        return ini_get('pcov.enabled');
    }
}

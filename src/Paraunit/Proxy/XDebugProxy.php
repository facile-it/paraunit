<?php

namespace Paraunit\Proxy;

/**
 * Class XDebugProxy
 * @package Paraunit\Proxy
 */
class XDebugProxy
{
    public function isLoaded()
    {
        return extension_loaded('xdebug');
    }
}

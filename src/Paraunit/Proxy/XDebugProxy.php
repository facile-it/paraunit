<?php
declare(strict_types=1);

namespace Paraunit\Proxy;

/**
 * Class XDebugProxy
 * @package Paraunit\Proxy
 */
class XDebugProxy
{
    public function isLoaded(): bool
    {
        return extension_loaded('xdebug');
    }
}

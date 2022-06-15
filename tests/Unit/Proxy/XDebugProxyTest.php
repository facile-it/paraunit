<?php

declare(strict_types=1);

namespace Tests\Unit\Proxy;

use Paraunit\Proxy\XDebugProxy;
use Tests\BaseUnitTestCase;

class XDebugProxyTest extends BaseUnitTestCase
{
    public function testGetMajorVersionRequiresXdebugLoaded(): void
    {
        if (extension_loaded('xdebug')) {
            $this->markTestSkipped('This test requires Xdebug disabled');
        }

        $proxy = new XDebugProxy();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Xdebug is not loaded');

        $proxy->getMajorVersion();
    }
}

<?php

declare(strict_types=1);

namespace Paraunit\Proxy;

use PHPUnit\Util\Xml;

/**
 * Class PHPUnitUtilXMLProxy.
 *
 * This class exists just as a proxy, 'cause you can't mock a static method with Prophecy
 */
class PHPUnitUtilXMLProxy
{
    public function loadFile(string $filename): \DOMDocument
    {
        return Xml::loadFile($filename, false, true, true);
    }
}

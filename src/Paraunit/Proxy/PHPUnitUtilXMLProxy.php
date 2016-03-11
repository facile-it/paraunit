<?php

namespace Paraunit\Proxy;

/**
 * Class PHPUnitUtilXMLProxy.
 *
 * This class exists just as a proxy, 'cause you can't mock a static method with Prophecy
 */
class PHPUnitUtilXMLProxy
{
    /**
     * @param $filename
     * @param bool|false $isHtml
     * @param bool|false $xinclude
     * @param bool|false $strict
     *
     * @return \DOMDocument
     */
    public function loadFile($filename, $isHtml = false, $xinclude = false, $strict = false)
    {
        return \PHPUnit_Util_XML::loadFile($filename, $isHtml, $xinclude, $strict);
    }
}

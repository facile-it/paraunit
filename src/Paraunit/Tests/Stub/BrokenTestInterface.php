<?php

namespace Paraunit\Tests\Stub;


interface BrokenTestInterface
{

    /**
     * @throws \Exception
     */
    function testBrokenTest();

}

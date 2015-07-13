<?php

namespace Paraunit\Tests\Stub;

class EntityManagerClosedTestStub extends BrokenTestBase implements BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    function testBrokenTest()
    {
        throw new \Exception("Blah Blah The EntityManager is closed Blah Blah");
    }

}

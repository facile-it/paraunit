<?php

namespace Paraunit\Tests\Stub;

use Doctrine\ORM\ORMException;

class EntityManagerClosedTestStub extends BrokenTestBase implements BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    function testBrokenTest()
    {
        throw ORMException::entityManagerClosed();
    }

}

<?php

namespace Paraunit\Tests\Stub;

/**
 * Class EntityManagerClosedTestStub
 * @package Paraunit\Tests\Stub
 */
class EntityManagerClosedTestStub extends BrokenTestBase implements BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception('Blah Blah The EntityManager is closed Blah Blah');
    }
}

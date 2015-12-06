<?php

namespace Paraunit\Tests\Stub;

/**
 * Class EntityManagerClosedTestStub
 * @package Paraunit\Tests\Stub
 */
class EntityManagerClosedTestStub extends BrokenTestBase implements BrokenTestInterface
{
    const OUTPUT = 'Blah Blah The EntityManager is closed Blah Blah';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}

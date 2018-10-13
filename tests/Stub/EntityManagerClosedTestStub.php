<?php

declare(strict_types=1);

namespace Tests\Stub;

/**
 * Class EntityManagerClosedTestStub
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

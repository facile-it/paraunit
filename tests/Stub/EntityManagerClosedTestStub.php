<?php

declare(strict_types=1);

namespace Tests\Stub;

class EntityManagerClosedTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public const OUTPUT = 'Blah Blah The EntityManager is closed Blah Blah';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}

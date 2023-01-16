<?php

declare(strict_types=1);

namespace Tests\Stub;

class EntityManagerClosedTestStub extends BrokenTestBase implements BrokenTestInterface
{
    final public const OUTPUT = 'Blah Blah The EntityManager is closed Blah Blah';

    /**
     * @throws \Exception
     */
    public function testBrokenTest(): never
    {
        throw new \Exception(self::OUTPUT);
    }
}

<?php

namespace Paraunit\Tests\Stub;

class FatalErrorTestStub extends BrokenTestBase implements BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        $var = null;

        $var->nonExistendMethod();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Stub;

/**
 * Class FatalErrorTestStub
 * @package Tests\Stub
 */
class FatalErrorTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest()
    {
        ini_set('memory_limit', '2M');

        $arr = [];

        while (true) {
            $arr[] = 'Allocated memory... allocated memory everywhere!';
        }
    }
}

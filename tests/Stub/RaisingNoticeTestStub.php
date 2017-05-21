<?php

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

/**
 * Class RaisingNoticeTestStub
 * @package Tests\Stub
 */
class RaisingNoticeTestStub extends TestCase
{
    /**
     * @dataProvider errorProvider
     */
    public function testRaise($errorMessage, $errorLevel)
    {
        trigger_error($errorMessage, $errorLevel);
        $this->fail();
    }

    public function errorProvider()
    {
        return array(
            array('YOU SHOULD NOT SEE THIS -- E_USER_NOTICE', E_USER_NOTICE),
            array('YOU SHOULD NOT SEE THIS -- E_USER_WARNING', E_USER_WARNING),
            array('YOU SHOULD NOT SEE THIS -- E_USER_ERROR', E_USER_ERROR),
        );
    }

    public function testVarDump()
    {
        var_dump('YOU SHOULD NOT SEE THIS -- var_dump');
        $this->fail();
    }
}

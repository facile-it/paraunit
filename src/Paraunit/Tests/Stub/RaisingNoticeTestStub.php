<?php

namespace Paraunit\Tests\Stub;

/**
 * Class RaisingNoticeTestStub
 * @package Paraunit\Tests\Stub
 */
class RaisingNoticeTestStub
{
    /**
     * @dataProvider errorProvider
     */
    public function testRaise($errorMessage, $errorLevel)
    {
        trigger_error($errorMessage, $errorLevel);
    }

    public function errorProvider()
    {
        return array(
            array('YOU SHOULD NOT SEE THIS -- E_USER_NOTICE', E_USER_NOTICE),
            array('YOU SHOULD NOT SEE THIS -- E_USER_WARNING', E_USER_WARNING),
            array('YOU SHOULD NOT SEE THIS -- E_USER_ERROR', E_USER_ERROR),
        );
    }
}

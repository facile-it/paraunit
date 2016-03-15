<?php

namespace Unit\Process;

use Paraunit\Process\SymfonyProcessWrapper;
use Tests\BaseUnitTestCase;

/**
 * Class SymfonyProcessWrapperTest
 * @package Unit\Process
 */
class SymfonyProcessWrapperTest extends BaseUnitTestCase
{
    public function testAddTestResultShouldResetExpectingFlag()
    {
        $process = new SymfonyProcessWrapper('', 'uuid');
        $process->setWaitingForTestResult(true);
        $this->assertTrue($process->isWaitingForTestResult());

        $process->addTestResult($this->mockTestResult());

        $this->assertFalse($process->isWaitingForTestResult());
    }
}

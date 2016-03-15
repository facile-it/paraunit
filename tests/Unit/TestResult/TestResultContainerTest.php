<?php

namespace Tests\Unit\TestResult;

use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithFullTestOutput;
use Tests\BaseUnitTestCase;

/**
 * Class TestResultContainerTest
 * @package Tests\Unit\TestResult
 */
class TestResultContainerTest extends BaseUnitTestCase
{
    public function testHandleLogItemAddsProcessOutputWhenNeeded()
    {
        $this->markTestIncomplete();
        $testResult = new TestResultWithFullTestOutput($this->mockTestFormat(), '', '');
        $parser = $this->prophesize();

        $testResultContainer = new TestResultContainer($parser->reveal(), $this->mockTestFormat());
    }
}

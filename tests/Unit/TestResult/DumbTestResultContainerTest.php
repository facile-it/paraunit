<?php

namespace Tests\Unit\TestResult;

use Paraunit\TestResult\DumbTestResultContainer;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class DumbTestResultContainerTest
 * @package Tests\Unit\TestResult
 */
class DumbTestResultContainerTest extends BaseUnitTestCase
{
    public function testAddProcessToFilenames()
    {
        $testResultFormat = $this->prophesize('Paraunit\TestResult\TestResultFormat');
        $dumbContainer = new DumbTestResultContainer($testResultFormat->reveal());
        $unitTestProcess = new StubbedParaunitProcess('phpunit Unit/ClassTest.php');
        $unitTestProcess->setFilename('ClassTest.php');
        $functionalTestProcess = new StubbedParaunitProcess('phpunit Functional/ClassTest.php');
        $functionalTestProcess->setFilename('ClassTest.php');
        
        $dumbContainer->addProcessToFilenames($unitTestProcess);
        $dumbContainer->addProcessToFilenames($functionalTestProcess);
        
        $this->assertCount(2, $dumbContainer->getFileNames());
    }
}

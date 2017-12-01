<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Process\SymfonyProcessWrapper;
use Prophecy\Argument;
use Symfony\Component\Process\Process;
use Tests\BaseUnitTestCase;

/**
 * Class SymfonyProcessWrapperTest
 * @package Unit\Process
 */
class SymfonyProcessWrapperTest extends BaseUnitTestCase
{
    public function testGetUniqueId()
    {
        $process = new SymfonyProcessWrapper($this->mockProcess(), 'Test.php');

        $this->assertEquals('Test.php', $process->getFilename());
        $this->assertEquals(md5('Test.php'), $process->getUniqueId());
    }

    public function testStart()
    {
        $process = $this->prophesize(Process::class);
        $process->start()
            ->shouldBeCalledTimes(1);
        $process->getEnv()
            ->willReturn([]);
        $process->setEnv(Argument::that(function ($envVariables) {
            $this->assertArrayHasKey(EnvVariables::PROCESS_UNIQUE_ID, $envVariables);
            $this->assertArrayHasKey(EnvVariables::PIPELINE_NUMBER, $envVariables);
            $this->assertEquals(4, $envVariables[EnvVariables::PIPELINE_NUMBER]);

            return true;
        }))
            ->shouldBeCalled();

        $processWrapper = new SymfonyProcessWrapper($process->reveal(), 'Test.php');

        $processWrapper->start(4);
    }

    public function testAddTestResultShouldResetExpectingFlag()
    {
        $process = new SymfonyProcessWrapper($this->mockProcess(), 'Test.php');
        $process->setWaitingForTestResult(true);
        $this->assertTrue($process->isWaitingForTestResult());

        $process->addTestResult($this->mockPrintableTestResult());

        $this->assertFalse($process->isWaitingForTestResult());
    }

    private function mockProcess(): Process
    {
        return $this->prophesize(Process::class)->reveal();
    }
}

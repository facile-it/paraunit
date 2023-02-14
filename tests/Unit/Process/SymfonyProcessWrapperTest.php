<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Process\SymfonyProcessWrapper;
use Prophecy\Argument;
use Symfony\Component\Process\Process as SymfonyProcess;
use Tests\BaseUnitTestCase;

class SymfonyProcessWrapperTest extends BaseUnitTestCase
{
    public function testGetUniqueId(): void
    {
        $process = new SymfonyProcessWrapper($this->mockProcess(), 'Test.php');

        $this->assertEquals('Test.php', $process->getFilename());
        $this->assertEquals(md5('Test.php'), $process->getUniqueId());
    }

    public function testStart(): void
    {
        $process = $this->prophesize(SymfonyProcess::class);
        $process->start()
            ->shouldBeCalledTimes(1);
        $process->getEnv()
            ->willReturn([]);
        $process->setEnv(Argument::that(function ($envVariables): bool {
            $this->assertArrayHasKey(EnvVariables::PROCESS_UNIQUE_ID, $envVariables);
            $this->assertArrayHasKey(EnvVariables::PIPELINE_NUMBER, $envVariables);
            $this->assertEquals(4, $envVariables[EnvVariables::PIPELINE_NUMBER]);

            return true;
        }))
            ->willReturn($process->reveal())
            ->shouldBeCalled();

        $processWrapper = new SymfonyProcessWrapper($process->reveal(), 'Test.php');

        $processWrapper->start(4);
    }

    private function mockProcess(): SymfonyProcess
    {
        return $this->prophesize(SymfonyProcess::class)->reveal();
    }
}

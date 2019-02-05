<?php

declare(strict_types=1);

namespace Tests\Unit\Command;

use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\BaseUnitTestCase;

class ParallelCommandTest extends BaseUnitTestCase
{
    public function testExecute()
    {
        $phpunitConfig = $this->prophesize(PHPUnitConfig::class);

        $runner = $this->prophesize(Runner::class);
        $runner->run(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn(0);

        $container = $this->prophesize(ContainerBuilder::class);
        $container->get(PHPUnitConfig::class)
            ->willReturn($phpunitConfig->reveal());
        $container->get(Runner::class)
            ->willReturn($runner->reveal());

        $configuration = $this->prophesize(ParallelConfiguration::class);
        $configuration->buildContainer(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($container->reveal());

        $command = new ParallelCommand($configuration->reveal());
        $application = new Application();
        $application->add($command);
        $command = $application->find('run');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            'stringFilter' => 'someFilter',
            '--testsuite' => 'testSuiteName',
        ]);

        $this->assertEquals(0, $exitCode);
    }
}

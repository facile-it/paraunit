<?php
declare(strict_types=1);

namespace Tests\Unit\Command;

use Paraunit\Command\CoverageCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\BaseUnitTestCase;

/**
 * Class CoverageCommandTest
 * @package Tests\Unit\Command
 */
class CoverageCommandTest extends BaseUnitTestCase
{
    /**
     * @dataProvider validCoverageOptionsProvider
     */
    public function testExecute(string $coverageOptionName)
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

        $configuration = $this->prophesize(CoverageConfiguration::class);
        $configuration->buildContainer(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($container->reveal());

        $command = new CoverageCommand($configuration->reveal());
        $application = new Application();
        $application->add($command);
        $command = $application->find('coverage');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            $coverageOptionName => '.',
        ]);

        $this->assertEquals(0, $exitCode);
    }

    public function validCoverageOptionsProvider(): array
    {
        return [
            ['--clover'],
            ['--xml'],
            ['--html'],
            ['--text'],
            ['--text-to-console'],
            ['--crap4j'],
            ['--php'],
        ];
    }

    public function testExecuteExpectsAtLeastOneCoverageFormat()
    {
        $configuration = $this->prophesize(CoverageConfiguration::class);
        $configuration->buildContainer()
            ->shouldNotBeCalled();

        $command = new CoverageCommand($configuration->reveal());
        $application = new Application();
        $application->add($command);
        $command = $application->find('coverage');
        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);

        $commandTester->execute(['command' => $command->getName()]);
    }
}

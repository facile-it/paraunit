<?php

declare(strict_types=1);

namespace Tests\Unit\Command;

use Paraunit\Command\CoverageCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\Crap4j;
use Paraunit\Coverage\Processor\Html;
use Paraunit\Coverage\Processor\Php;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextSummary;
use Paraunit\Coverage\Processor\Xml;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\BaseUnitTestCase;

class CoverageCommandTest extends BaseUnitTestCase
{
    /**
     * @dataProvider validCoverageOptionsProvider
     *
     * @param string $coverageOptionName
     * @param bool $hasOptionalValue
     */
    public function testExecute(string $coverageOptionName, bool $hasOptionalValue = false): void
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
            '--' . $coverageOptionName => $hasOptionalValue ? null : 'some/path',
        ]);

        $this->assertEquals(0, $exitCode);
    }

    public function validCoverageOptionsProvider(): array
    {
        return [
            [Clover::getConsoleOptionName()],
            [Xml::getConsoleOptionName()],
            [Html::getConsoleOptionName()],
            [Text::getConsoleOptionName(), true],
            [Text::getConsoleOptionName()],
            [TextSummary::getConsoleOptionName(), true],
            [TextSummary::getConsoleOptionName()],
            [Crap4j::getConsoleOptionName()],
            [Php::getConsoleOptionName()],
        ];
    }

    public function testExecuteExpectsAtLeastOneCoverageFormat(): void
    {
        $configuration = $this->prophesize(CoverageConfiguration::class);
        $configuration->buildContainer(Argument::cetera())
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

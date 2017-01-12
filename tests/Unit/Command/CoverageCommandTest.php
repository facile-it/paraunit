<?php

namespace Tests\Unit\Command;

use Paraunit\Command\CoverageCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CoverageCommandTest
 * @package Tests\Unit\Command
 */
class CoverageCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validCoverageOptionsProvider
     */
    public function testExecute($coverageOptionName)
    {
        $filteredFiles = array('Test.php');
        $filter = $this->prophesize('Paraunit\Filter\Filter');
        $filter->filterTestFiles(Argument::cetera())->shouldBeCalled()->willReturn($filteredFiles);

        $runner = $this->prophesize('Paraunit\Runner\Runner');
        $runner->run(Argument::cetera())->shouldBeCalled()->willReturn(0);

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->get('paraunit.filter.filter')->willReturn($filter->reveal());
        $container->get('paraunit.runner.runner')->willReturn($runner->reveal());

        $configuration = $this->prophesize('Paraunit\Configuration\CoverageConfiguration');
        $configuration->buildContainer(Argument::cetera())->shouldBeCalled()->willReturn($container);

        $command = new CoverageCommand($configuration->reveal());
        $application = new Application();
        $application->add($command);
        $command = $application->find('coverage');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            $coverageOptionName => '.',
        ));

        $this->assertEquals(0, $exitCode);
    }

    public function validCoverageOptionsProvider()
    {
        return array(
            array('--clover'),
            array('--xml'),
            array('--html'),
            array('--text'),
            array('--text-to-console'),
        );
    }

    public function testExecuteExpectsAtLeastOneCoverageFormat()
    {
        $configuration = $this->prophesize('Paraunit\Configuration\CoverageConfiguration');
        $configuration->buildContainer()->shouldNotBeCalled();

        $command = new CoverageCommand($configuration->reveal());
        $application = new Application();
        $application->add($command);
        $command = $application->find('coverage');
        $commandTester = new CommandTester($command);

        $this->setExpectedException('\InvalidArgumentException');

        $commandTester->execute(array('command' => $command->getName(),));
    }
}

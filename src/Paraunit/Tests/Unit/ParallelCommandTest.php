<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Command\ParallelCommand;
use Paraunit\Filter\Filter;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;


class ParallelCommandTest extends \PHPUnit_Framework_TestCase
{

    public function test_paralle_command_exit_code_ok()
    {

        $filter = $this->getFilterMock();
        $runner = $this->getRunnerMock();

        $application = new Application();
        $commandInstance = new ParallelCommand($filter->reveal(), $runner->reveal(), $this->getFakeConfig());
        $application->add($commandInstance);

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals(0, $commandTester->getStatusCode());

    }

    public function test_paralle_command_exit_code_fail()
    {

        $filter = $this->getFilterMock();
        $runner = $this->getRunnerMock(25);

        $application = new Application();
        $commandInstance = new ParallelCommand($filter->reveal(), $runner->reveal(), $this->getFakeConfig());
        $application->add($commandInstance);

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals(25, $commandTester->getStatusCode());

    }

    /**
     * @return Runner
     */
    private function getRunnerMock($exitCode = 0)
    {
        $runner = $this->prophesize('\Paraunit\Runner\Runner');
        $runner
            ->setMaxProcessNumber(Argument::type('integer'))
            ->shouldBeCalledTimes(1);
        $runner
            ->setPhpunitConfigFile(Argument::type('string'))
            ->shouldBeCalledTimes(1);
        $runner->run(Argument::type('array'), Argument::any())
            ->shouldBeCalledTimes(1)
            ->willReturn($exitCode);

        return $runner;
    }

    /**
     * @return Filter
     */
    private function getFilterMock()
    {
        $filter = $this->prophesize('Paraunit\Filter\Filter');
        $filter
            ->filterTestFiles(Argument::any(), Argument::any())
            ->shouldBeCalledtimes(1)
            ->willReturn(array('fake_testfile.php'));

        return $filter;
    }

    private function getFakeConfig()
    {

        return array(

            /**
             * PhpUnit xml configuration file path
             */
            'PARAUNIT_PHPUNIT_XML_PATH' => 'phpunit.xml',

            /**
             * Max number of processes that paraunit will swarm
             */
            'PARAUNIT_MAX_PROCESS_NUMBER' => 10,

        );

    }

}

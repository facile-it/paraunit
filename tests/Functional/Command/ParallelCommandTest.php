<?php

namespace Tests\Functional\Command;

use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;

/**
 * Class ParallelCommandTest
 * @package Tests\Functional\Command
 */
class ParallelCommandTest extends BaseTestCase
{
    /**
     * @dataProvider configurationPathProvider
     */
    public function testExecutionAllGreen($configurationPath)
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command'  => $command->getName(),
            '--configuration' => $configurationPath,
            'stringFilter' => 'green',
        ));

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Executed: 0 test classes', $output);
        $this->assertNotContains('ABNORMAL TERMINATIONS', $output);
        $this->assertEquals(0, $exitCode);
    }

    /**
     * @dataProvider configurationPathProvider
     */
    public function testExecution($configurationPath)
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command'  => $command->getName(),
            '--configuration' => $configurationPath,
        ));

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Executed: 0 test classes', $output);
        $this->assertContains('ABNORMAL TERMINATIONS', $output);
        $this->assertNotEquals(0, $exitCode);
    }

    public function configurationPathProvider()
    {
        return array(
            array($this->getConfigForStubs()),
            array(implode(DIRECTORY_SEPARATOR, array('.', 'tests', 'Stub', 'phpunit_for_stubs.xml'))),
        );
    }
}

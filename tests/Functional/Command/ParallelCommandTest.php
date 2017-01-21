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
    public function testExecution()
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command'  => $command->getName(),
            // pass arguments to the helper
            '-c' => $this->getConfigForStubs(),
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Executed: 0 test classes', $output);
        $this->assertContains('ABNORMAL TERMINATIONS', $output);
        $this->assertNotEquals(0, $exitCode);
    }
}

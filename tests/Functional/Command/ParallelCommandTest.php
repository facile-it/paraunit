<?php
declare(strict_types=1);

namespace Tests\Functional\Command;

use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;
use Tests\Stub\MissingProviderTestStub;
use Tests\Stub\MySQLDeadLockTestStub;
use Tests\Stub\RaisingNoticeTestStub;

/**
 * Class ParallelCommandTest
 * @package Tests\Functional\Command
 */
class ParallelCommandTest extends BaseTestCase
{
    public function testExecutionAllGreen()
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
            'stringFilter' => 'green',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Executed: 0 test classes', $output);
        $this->assertNotContains('ABNORMAL TERMINATIONS', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testExecutionAllGreenWithRepeatOption()
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
            '--repeat' => '1',
            'stringFilter' => 'green',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Executed: 0 test classes', $output);
        $this->assertNotContains('ABNORMAL TERMINATIONS', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testExecution()
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
        ));

        $output = $commandTester->getDisplay();
        $this->assertNotContains('BBBBbBBBBBBB', $output, 'Shark logo shown but not required');
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Executed: 0 test classes', $output);
        $this->assertContains('ABNORMAL TERMINATIONS', $output);
        $this->assertContains('ParseErrorTestStub.php', $output);
        $this->assertContains(RaisingNoticeTestStub::class, $output);
        $this->assertContains(MissingProviderTestStub::class, $output);
        $this->assertContains(MySQLDeadLockTestStub::class, $output);
        $this->assertNotEquals(0, $exitCode);

        $this->assertContains('Executed: 11 test classes, 28 tests (12 retried)', $output);
    }

    public function testExecutionWithLogo()
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--logo' => $configurationPath,
            '--filter' => 'doNotExecuteAnyTestSoItsFaster',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('BBBBbBBBBBBB', $output, 'Shark logo missing');
    }

    public function testExecutionWithDebugEnabled()
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
            '--debug' => true,
        ));

        $output = $commandTester->getDisplay();
        $this->assertNotEquals(0, $exitCode);

        $this->assertContains('Executed: 11 test classes, 28 tests (12 retried)', $output, 'Precondition failed');
        $processesCount = 11 + 12;
        $this->assertSame($processesCount, substr_count($output, 'PROCESS STARTED'));
        $this->assertSame($processesCount, substr_count($output, 'PROCESS TERMINATED'));
        $this->assertSame($processesCount, substr_count($output, 'PROCESS PARSING COMPLETED'));
        $this->assertSame(12, substr_count($output, 'PROCESS TO BE RETRIED'));
    }

    public function testExecutionWithoutConfiguration()
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--filter' => 'do_not_execute_anything',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('NO TESTS EXECUTED', $output);
        $this->assertContains('0 tests', $output);
        $this->assertSame(0, $exitCode);
    }

    public function testExecutionWithDeprecationListener()
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--configuration' => $this->getConfigForDeprecationListener(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertNotEquals(0, $exitCode);
        $this->assertContains('Executed: 1 test classes, 1 tests (0 retried)', $output, 'Precondition failed');
        $this->assertContains('1 files with DEPRECATION WARNINGS:', $output);
        $this->assertContains('deprecation triggered by RaisingDeprecationTestStub::testDeprecation', $output);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Functional\Command;

use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;
use Tests\Stub\IntentionalWarningTestStub;
use Tests\Stub\MySQLDeadLockTestStub;
use Tests\Stub\PostgreSQLDeadLockTestStub;
use Tests\Stub\RaisingDeprecationTestStub;
use Tests\Stub\RaisingNoticeTestStub;

class ParallelCommandTest extends BaseTestCase
{
    public function testExecutionAllGreen(): void
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
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringNotContainsString('Executed: 0 test classes', $output);
        $this->assertStringNotContainsString('ABNORMAL TERMINATIONS', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testExecutionAllGreenWithRepeatOption(): void
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
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringNotContainsString('Executed: 0 test classes', $output);
        $this->assertStringNotContainsString('ABNORMAL TERMINATIONS', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testExecution(): void
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringNotContainsString('BBBBbBBBBBBB', $output, 'Shark logo shown but not required');
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringNotContainsString('Executed: 0 test classes', $output);
        $this->assertStringContainsString('ABNORMAL TERMINATIONS', $output);
        $this->assertStringContainsString('ParseErrorTestStub.php', $output);
        $this->assertStringContainsString(RaisingNoticeTestStub::class, $output);
        $this->assertStringContainsString(IntentionalWarningTestStub::class, $output);
        $this->assertStringContainsString(MySQLDeadLockTestStub::class, $output);
        $this->assertStringContainsString(PostgreSQLDeadLockTestStub::class, $output);
        $this->assertNotEquals(0, $exitCode);
        $this->assertStringContainsString('Executed: 15 test classes (21 retried), 29 tests', $output);
    }

    public function testExecutionWithLogo(): void
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--logo' => $configurationPath,
            '--filter' => 'doNotExecuteAnyTestSoItsFaster',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('BBBBbBBBBBBB', $output, 'Shark logo missing');
    }

    public function testExecutionWithDebugEnabled(): void
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
            '--debug' => true,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertNotEquals(0, $exitCode);

        $classExecuted = 15;
        $processRetried = 21;
        $processesCount = $classExecuted + $processRetried;
        $this->assertStringContainsString(
            sprintf('Executed: %d test classes (%d retried), 29 tests', $classExecuted, $processRetried),
            $output,
            'Precondition failed'
        );
        $this->assertSame($processesCount, substr_count($output, 'PROCESS STARTED'));
        $this->assertSame($processesCount, substr_count($output, 'PROCESS TERMINATED'));
        $this->assertSame($classExecuted, substr_count($output, 'PROCESS PARSING COMPLETED'));
        $this->assertSame($processRetried, substr_count($output, 'PROCESS TO BE RETRIED'));
    }

    public function testExecutionWithParametersWithoutValue(): void
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
            '--dont-report-useless-tests' => true,
        ]);

        $this->assertSame(0, $exitCode);
    }

    public function testExecutionWithoutConfiguration(): void
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--filter' => 'do_not_execute_anything',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringContainsString('0 tests', $output);
        $this->assertSame(0, $exitCode);
    }

    public function testExecutionWithDeprecationListener(): void
    {
        if ('disabled' === getenv('SYMFONY_DEPRECATIONS_HELPER')) {
            $this->markTestSkipped('Deprecation handler is disabled');
        }

        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $this->getConfigForDeprecationListener(),
        ]);

        $output = $commandTester->getDisplay();
        $this->assertNotEquals(0, $exitCode);
        $this->assertStringContainsString('Executed: 1 test classes, 3 tests', $output, 'Precondition failed');
        $this->assertStringContainsString('1 files with DEPRECATION WARNINGS:', $output);
        $this->assertStringContainsString(RaisingDeprecationTestStub::DEPRECATION_MESSAGE, $output);
        $this->assertStringContainsString('RaisingDeprecationTestStub::testDeprecation', $output);
        $this->assertStringNotContainsString('2)', $output, 'Deprecations are shown more than once per test file');
    }
}

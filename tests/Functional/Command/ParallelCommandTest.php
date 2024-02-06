<?php

declare(strict_types=1);

namespace Tests\Functional\Command;

use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Logs\ValueObject\Test;
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
    public function testExecutionFailsWithoutExtension(): void
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Paraunit extension is not registered');

        $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $this->createConfigWithoutExtension(),
            'stringFilter' => 'nothing',
        ]);
    }

    public function testExecutionUpgradesTheConfig(): void
    {
        $configWithoutExtension = $this->createConfigWithoutExtension();
        $configChecker = new PHPUnitConfig($configWithoutExtension);
        $this->assertFalse($configChecker->isParaunitExtensionRegistered());

        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['Y']);

        $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configWithoutExtension,
            'stringFilter' => 'nothing',
        ], ['interactive' => true]);

        $this->assertTrue($configChecker->isParaunitExtensionRegistered(), 'Configuration not updated correctly');
        $this->assertSame(0, $commandTester->getStatusCode(), 'Command failed with non-zero exit code');
        $this->assertStringContainsString('Configuration updated successfully', $commandTester->getDisplay());
    }

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
        $this->assertStringContainsString('Executed: 17 test classes (21 retried), 26 tests', $output);
    }

    public function testExecutionWithWarning(): void
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            'stringFilter' => 'warning',
            '--configuration' => $this->getConfigForStubs(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringNotContainsString('BBBBbBBBBBBB', $output, 'Shark logo shown but not required');
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringNotContainsString('UNKNOWN RESULTS', $output);
        $this->assertStringNotContainsString('Executed: 0 test classes', $output);
        $this->assertStringContainsString('WARNINGS', $output);
        $this->assertStringContainsString('This is an intentional warning', $output);
        $this->assertStringContainsString(IntentionalWarningTestStub::class, $output);
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Executed: 1 test classes, 1 tests', $output);
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
            '--configuration' => $configurationPath,
            '--logo' => true,
            'stringFilter' => 'doNotExecuteAnyTestSoItsFaster',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('BBBBbBBBBBBB', $output, 'Shark logo missing');
    }

    public function testRegressionExecutionWithStringFilter(): void
    {
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
            'stringFilter' => 'doNotExecuteAnyTestSoItsFaster',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(' 0 tests', $output, 'Filter is not working');
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

        $classExecuted = 17;
        $processRetried = 21;
        $processesCount = $classExecuted + $processRetried;
        $this->assertStringContainsString(
            sprintf('Executed: %d test classes (%d retried), 26 tests', $classExecuted, $processRetried),
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
            '--pass-through' => ['--dont-report-useless-tests'],
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
            '--pass-through' => ['--filter=do_not_execute_anything'],
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringContainsString('0 tests', $output);
        $this->assertSame(0, $exitCode);
        $this->assertStringNotContainsString(Test::unknown()->name, $output);
    }

    public function testExecutionWithDeprecationListener(): void
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $this->getConfigForDeprecationListener(),
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Executed: 1 test classes, 3 tests', $output, 'Precondition failed');
        $this->assertStringContainsString('1 files with DEPRECATIONS:', $output);
        $this->assertStringContainsString(RaisingDeprecationTestStub::DEPRECATION_MESSAGE, $output);
        $this->assertStringContainsString('3x Tests\Stub\RaisingDeprecationTestStub::testDeprecation', $output);
    }

    public function testExecutionWithRandomOrder(): void
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $this->getConfigForStubs(),
            '--sort' => 'random',
            'stringFilter' => 'Green',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Executed: 1 test classes, 3 tests', $output);
    }

    public function testRegressionWithPHPUnitError(): void
    {
        $application = new Application();
        $application->add(new ParallelCommand(new ParallelConfiguration()));

        $command = $application->find('run');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $this->getConfigForStubs(),
            'stringFilter' => 'PHPUnitError',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertNotEquals(0, $exitCode, 'Expecting test failure, got exit code 0');
        $this->assertStringContainsString('Executed: 1 test classes, 1 tests', $output);
        $this->assertStringContainsStringIgnoringCase('1 files with ERRORS', $output);
    }
}

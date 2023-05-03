<?php

declare(strict_types=1);

namespace Tests\Functional\Command;

use Paraunit\Command\CoverageCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Proxy\XDebugProxy;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;

class CoverageCommandTest extends BaseTestCase
{
    private const COMMAND_NAME = 'coverage';

    public function testExecutionFailsWithoutExtension(): void
    {
        $commandTester = $this->createCommandTester();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Paraunit extension is not registered');

        $commandTester->execute([
            '--configuration' => $this->createConfigWithoutExtension(),
            '--text' => null,
            'stringFilter' => 'nothing',
        ]);
    }

    public function testExecutionWithTextToFile(): void
    {
        if ($this->isXdebugCoverageDisabled()) {
            $this->markTestSkipped('Test does not work without Xdebug');
        }

        $coverageFileName = $this->getTempCoverageFilename();
        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text' => $coverageFileName,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringNotContainsString('Coverage Report', $output);
        $this->assertStringNotContainsString('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode);
        $this->assertFileExists($coverageFileName);
        $fileContent = file_get_contents($coverageFileName);
        unlink($coverageFileName);
        $this->assertNotFalse($fileContent);
        $this->assertStringContainsString('Coverage Report', $fileContent);
        $this->assertStringContainsString('StubbedParaunitProcess', $fileContent);
    }

    public function testExecutionWithTextToConsole(): void
    {
        if ($this->isXdebugCoverageDisabled()) {
            $this->markTestSkipped('Test does not work without Xdebug');
        }

        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text' => null,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringContainsString('Coverage Report', $output);
        $this->assertStringContainsString('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode);
    }

    public function testExecutionWithNoCoverageFetched(): void
    {
        if ($this->isXdebugCoverageDisabled()) {
            $this->markTestSkipped('Test does not work without Xdebug');
        }

        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text' => null,
            'stringFilter' => 'ParseError',
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringContainsString('Coverage Report', $output);
        $this->assertStringContainsString('1 files with COVERAGE NOT FETCHED', $output);
        $this->assertStringContainsString('ParseErrorTestStub.php', $output);
        $this->assertNotEquals(0, $exitCode);
    }

    public function testExecutionWithTextSummaryToFile(): void
    {
        $coverageFileName = $this->getTempCoverageFilename();
        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text-summary' => $coverageFileName,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertStringNotContainsStringIgnoringCase('NO TESTS EXECUTED', $output);
        $this->assertStringNotContainsStringIgnoringCase('Coverage Report', $output);
        $this->assertStringNotContainsStringIgnoringCase('COVERAGE NOT FETCHED', $output);
        $this->assertStringNotContainsStringIgnoringCase('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode, $output);
        $this->assertFileExists($coverageFileName);
        $fileContent = file_get_contents($coverageFileName);
        unlink($coverageFileName);
        $this->assertNotFalse($fileContent);
        $this->assertStringContainsString('Coverage Report', $fileContent);
        $this->assertStringNotContainsStringIgnoringCase('StubbedParaunitProcess', $fileContent);
    }

    public function testExecutionWithTextSummaryToConsole(): void
    {
        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text-summary' => null,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertStringNotContainsString('NO TESTS EXECUTED', $output);
        $this->assertStringContainsString('Coverage Report', $output);
        $this->assertStringNotContainsString('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode);
    }

    private function getTempCoverageFilename(): string
    {
        /** @var string $filename */
        $filename = tempnam(sys_get_temp_dir(), 'coverage.txt');
        $this->assertNotFalse($filename);

        return $filename;
    }

    private function createCommandTester(): CommandTester
    {
        $application = new Application();
        $application->add(new CoverageCommand(new CoverageConfiguration()));

        $command = $application->find(self::COMMAND_NAME);

        return new CommandTester($command);
    }

    /**
     * @param array<string, string|null> $additionalArguments
     *
     * @return array<string, string|null>
     */
    private function prepareArguments(array $additionalArguments = []): array
    {
        return [
            'command' => self::COMMAND_NAME,
            '--configuration' => $this->getConfigForStubs(),
            'stringFilter' => 'green',
            ...$additionalArguments,
        ];
    }

    private function isXdebugCoverageDisabled(): bool
    {
        $xdebug = new XDebugProxy();

        if (! $xdebug->isLoaded()) {
            return true;
        }

        $majorVersion = $xdebug->getMajorVersion();

        if (2 === $majorVersion) {
            return false;
        }

        $xdebugMode = getenv('XDEBUG_MODE');
        if (! is_string($xdebugMode) || '' === $xdebugMode) {
            $xdebugMode = ini_get('xdebug.mode');
        }

        $this->assertIsString($xdebugMode, 'Unable to retrieve Xdebug mode');

        return ! str_contains($xdebugMode, 'coverage')
            && ! str_contains($xdebugMode, 'debug');
    }
}

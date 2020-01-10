<?php

declare(strict_types=1);

namespace Tests\Functional\Command;

use Paraunit\Command\CoverageCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;

class CoverageCommandTest extends BaseTestCase
{
    private const COMMAND_NAME = 'coverage';

    public function testExecutionWithTextToFile(): void
    {
        $coverageFileName = $this->getTempCoverageFilename();
        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text' => $coverageFileName,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Coverage Report', $output);
        $this->assertNotContains('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode);
        $this->assertFileExists($coverageFileName);
        $fileContent = file_get_contents($coverageFileName);
        unlink($coverageFileName);
        $this->assertNotFalse($fileContent);
        $this->assertContains('Coverage Report', $fileContent);
        $this->assertContains('StubbedParaunitProcess', $fileContent);
    }

    public function testExecutionWithTextToConsole(): void
    {
        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text' => null,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertContains('Coverage Report', $output);
        $this->assertContains('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode);
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
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertNotContains('Coverage Report', $output);
        $this->assertNotContains('StubbedParaunitProcess', $output);
        $this->assertEquals(0, $exitCode);
        $this->assertFileExists($coverageFileName);
        $fileContent = file_get_contents($coverageFileName);
        unlink($coverageFileName);
        $this->assertNotFalse($fileContent);
        $this->assertContains('Coverage Report', $fileContent);
        $this->assertNotContains('StubbedParaunitProcess', $fileContent);
    }

    public function testExecutionWithTextSummaryToConsole(): void
    {
        $commandTester = $this->createCommandTester();

        $arguments = $this->prepareArguments([
            '--text-summary' => null,
        ]);
        $exitCode = $commandTester->execute($arguments);

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertContains('Coverage Report', $output);
        $this->assertNotContains('StubbedParaunitProcess', $output);
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
     * @return string[]
     */
    private function prepareArguments(array $additionalArguments = []): array
    {
        return array_merge([
            'command' => self::COMMAND_NAME,
            '--configuration' => $this->getConfigForStubs(),
            'stringFilter' => 'green',
        ], $additionalArguments);
    }
}

<?php

namespace Tests\Functional\Command;

use Paraunit\Command\CoverageCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;

class CoverageCommandTest extends BaseTestCase
{
    public function testExecutionWithTextToFile()
    {
        $coverageFileName = tempnam(sys_get_temp_dir(), 'coverage.txt');
        $configurationPath = $this->getConfigForStubs();
        $application = new Application();
        $application->add(new CoverageCommand(new CoverageConfiguration()));

        $command = $application->find('coverage');
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => $command->getName(),
            '--configuration' => $configurationPath,
            '--text' => $coverageFileName,
            'stringFilter' => 'green',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertNotContains('NO TESTS EXECUTED', $output);
        $this->assertEquals(0, $exitCode);
        $this->assertFileExists($coverageFileName);
        $fileContent = file_get_contents($coverageFileName);
        unlink($coverageFileName);
        $this->assertContains('Coverage', $fileContent);
    }
}
